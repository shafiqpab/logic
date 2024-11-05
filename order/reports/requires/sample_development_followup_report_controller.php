<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];



if($db_type==0)
{
	$group_concat="group_concat";
	$select_year="year";
	$year_con="";
	$defalt_date_format="0000-00-00";
}
else
{
	$group_concat="wm_concat";
	$select_year="to_char";
	$year_con=",'YYYY'";
	$defalt_date_format="";
}
//--------------------------------------------------------------------------------------------------------------------
$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$company_library_short=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );

$team_library=return_library_array( "select id, team_name from lib_marketing_team", "id", "team_name"  );
$team_member_library=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info", "id", "team_member_name"  );

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 145, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );   	 
	exit();
}

if($action=="image_view_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample Development Info","../../../", 1, 1, $unicode);
	$imge_data=sql_select("select master_tble_id,image_location from   common_photo_library where form_name='sample_booking_non' and file_type=1 and master_tble_id='$id'");
	?>
	<table>
	<tr>
	<?
	foreach($imge_data as $row)
	{
	?>
	<td><img   src='../../../<? echo $row[csf('image_location')]; ?>' height='100%' width='100%' /></td>
	<?
	}
	?>

	</tr>

	</table>

	<?

}

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;


if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_name 	= str_replace("'","",$cbo_company_name);
	$buyer_name 	= str_replace("'","",$cbo_buyer_name);
	$wo_no 			= str_replace("'","",$txt_wo_no);
	$search_by 		= str_replace("'","",$cbo_search_by);
	$date_from 		= str_replace("'","",$txt_date_from);
	$date_to 		= str_replace("'","",$txt_date_to);
	$sql_cond = "";
	
	if($date_from !="" && $date_to !="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format($date_from,"yyyy-mm-dd","");
			$end_date=change_date_format($date_to,"yyyy-mm-dd","");
		}
		else
		{
			$start_date=date("j-M-Y",strtotime($date_from));
			$end_date=date("j-M-Y",strtotime($date_to));
		}
		
		$sql_cond.= ($search_by==1) ? " AND D.BOOKING_DATE BETWEEN '$start_date' and '$end_date'" : " AND C.DELIVERY_DATE BETWEEN '$start_date' and '$end_date'";
	}
	$sql_cond.= ($company_name !=0) ? " AND A.COMPANY_ID=$company_name" : "";
	$sql_cond.= ($buyer_name !=0) ? " AND A.BUYER_NAME=$buyer_name" : "";
	$sql_cond.= ($wo_no !="") ? " AND D.BOOKING_NO LIKE '%$wo_no'" : "";

	// ================================ CREATING CONSTRUCTION - COMPOSITION ARRAY ===============================
	$composition_arr=array();
	$construction_arr=array();
	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$sql_deter_res=sql_select($sql_deter);
	if(count($sql_deter_res)>0)
	{
		foreach( $sql_deter_res as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				// $composition_arr[$row[csf('id')]]=$row[csf('construction')]."*".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				$construction_arr[$row[csf('id')]]=$row[csf('construction')];
			}
		}
	}
	unset($sql_deter_res);
	// echo "<pre>";
	// print_r($composition_arr);
	// echo $sql_cond;die;

	$sql = "SELECT a.id as STYLE_ID, A.REQUISITION_NUMBER,A.STYLE_REF_NO,D.BOOKING_NO,d.id as BID,C.DELIVERY_DATE,C.GSM,F.COLOR_ID,D.ATTENTION,C.UOM_ID,C.DETERMINATION_ID,C.REMARKS_RA,F.GREY_FAB_QNTY from sample_development_mst a,sample_development_dtls b,sample_development_fabric_acc c, wo_non_ord_samp_booking_mst d,wo_non_ord_samp_booking_dtls e,sample_development_rf_color f where a.id=b.sample_mst_id and a.id=c.sample_mst_id and a.id=e.style_id and d.booking_no=e.booking_no and d.booking_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.entry_form_id=203 and a.id=f.mst_id and f.status_active=1 and f.is_deleted=0 and d.entry_form_id=140 $sql_cond";
	// echo $sql;die();
	$sqlRes = sql_select($sql);
	$dataArray = array();
	$booking_array = array();
	$booking_id_array = array();
	foreach ($sqlRes as $val) 
	{
		$dataArray[$val['STYLE_ID']][$val['BID']][$val['DETERMINATION_ID']][$val['GSM']][$val['COLOR_ID']]['STYLE'] 	= $val['STYLE_REF_NO'];
		$dataArray[$val['STYLE_ID']][$val['BID']][$val['DETERMINATION_ID']][$val['GSM']][$val['COLOR_ID']]['REQNO'] 	= $val['BOOKING_NO'];
		$dataArray[$val['STYLE_ID']][$val['BID']][$val['DETERMINATION_ID']][$val['GSM']][$val['COLOR_ID']]['DATE'] 		= $val['DELIVERY_DATE'];
		$dataArray[$val['STYLE_ID']][$val['BID']][$val['DETERMINATION_ID']][$val['GSM']][$val['COLOR_ID']]['ATTENTION'] = $val['ATTENTION'];
		$dataArray[$val['STYLE_ID']][$val['BID']][$val['DETERMINATION_ID']][$val['GSM']][$val['COLOR_ID']]['UOM'] 		= $val['UOM_ID'];
		$dataArray[$val['STYLE_ID']][$val['BID']][$val['DETERMINATION_ID']][$val['GSM']][$val['COLOR_ID']]['REMARKS'] 	= $val['REMARKS_RA'];
		$dataArray[$val['STYLE_ID']][$val['BID']][$val['DETERMINATION_ID']][$val['GSM']][$val['COLOR_ID']]['QNTY'] 		+= $val['GREY_FAB_QNTY'];
		$booking_array[$val['BOOKING_NO']] = $val['BOOKING_NO'];
		$booking_id_array[$val['BID']] = $val['BID'];
	}
	unset($sqlRes);
	$bookingNo = "'".implode("','", $booking_array)."'";
	$bookingID = implode(",", $booking_id_array);
	// echo "<pre>";print_r($dataArray);die();
	//====================================== getting image ==================================
	$image_library=return_library_array( "SELECT MASTER_TBLE_ID, IMAGE_LOCATION FROM COMMON_PHOTO_LIBRARY WHERE FORM_NAME='sample_booking_non' AND MASTER_TBLE_ID IN($bookingNo)", "MASTER_TBLE_ID", "IMAGE_LOCATION"  );
	// print_r($image_library);		
	
	// =================================== getting BOOKING QNTY ====================================
	$sqlbooking = "SELECT A.ID,B.GSM_WEIGHT AS GSM,B.GMTS_COLOR, B.GREY_FABRIC as QTY FROM WO_NON_ORD_SAMP_BOOKING_MST A,WO_NON_ORD_SAMP_BOOKING_DTLS B WHERE A.BOOKING_NO=B.BOOKING_NO AND A.STATUS_ACTIVE=1 AND B.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.IS_DELETED=0 AND A.BOOKING_NO IN($bookingNo)";
	// echo $sqlbooking;die();
	$sqlBookRes = sql_select($sqlbooking);
	$book_qty_array = array();
	foreach ($sqlBookRes as $val) 
	{
		$book_qty_array[$val['ID']][$val['GSM']][$val['GMTS_COLOR']] += $val['QTY'];
	}
	// print_r($book_qty_array);	

	// =================================== getting kniting status ====================================
	$sqlKnit = "SELECT a.ENTRY_FORM, C.PO_BREAKDOWN_ID, C.QNTY, B.FEBRIC_DESCRIPTION_ID, B.GSM, B.COLOR_ID FROM INV_RECEIVE_MASTER A, PRO_GREY_PROD_ENTRY_DTLS B, PRO_ROLL_DETAILS C WHERE     A.ID = B.MST_ID AND A.STATUS_ACTIVE = 1 AND B.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.IS_DELETED = 0 AND B.ID=C.DTLS_ID AND C.ENTRY_FORM IN(2,22,58) and c.booking_without_order=1 and C.PO_BREAKDOWN_ID in($bookingID)";
	//echo $sqlKnit;
	$sqlKnitRes = sql_select($sqlKnit);
	$knit_status_array = array();
	foreach ($sqlKnitRes as $val) 
	{
		$knit_status_array[$val['ENTRY_FORM']][$val['PO_BREAKDOWN_ID']][$val['FEBRIC_DESCRIPTION_ID']][$val['GSM']][$val['COLOR_ID']] += $val['QNTY'];
		// $knit_status_array[$val['BOOKING_ID']][$val['FEBRIC_DESCRIPTION_ID']][$val['GSM']] += $val['QTY'];
	}
	// print_r($knit_status_array);
	// =================================== GETTING ISSUE QTY ====================================
	$sqlissue = "SELECT d.BOOKING_NO,c.DETARMINATION_ID,c.GSM,b.COLOR_ID, D.PO_BREAKDOWN_ID, d.QNTY as QTY FROM inv_issue_master a,inv_grey_fabric_issue_dtls b,product_details_master c,pro_roll_details d,inv_transaction e WHERE a.id=b.mst_id and e.id=b.trans_id and a.id=e.mst_id and b.prod_id=c.id and b.prod_id=e.prod_id and a.id=d.mst_id and b.id=d.dtls_id and d.entry_form=61 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 AND d.PO_BREAKDOWN_ID IN($bookingID)";
	// echo $sqlissue;
	$issueRes = sql_select($sqlissue);
	$issue_qty_array = array();
	foreach ($issueRes as $val) 
	{
		$issue_qty_array[$val['PO_BREAKDOWN_ID']][$val['DETARMINATION_ID']][$val['GSM']][$val['COLOR_ID']] += $val['QTY'];
	}
	// print_r($issue_qty_array);
	// =================================== GETTING BATCH QTY ====================================
	$sqlBatch = "SELECT C.BOOKING_NO_ID,D.DETARMINATION_ID,D.GSM,C.COLOR_ID, B.BATCH_QNTY as QTY FROM PRO_BATCH_CREATE_DTLS B,PRO_BATCH_CREATE_MST C,PRODUCT_DETAILS_MASTER D WHERE C.ID=B.MST_ID AND B.PROD_ID=D.ID AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 AND C.BOOKING_NO IN($bookingNo)";
	// echo $sqlBatch;
	$sqlBatchRes = sql_select($sqlBatch);
	$batch_qty_array = array();
	foreach ($sqlBatchRes as $val) 
	{
		$batch_qty_array[$val['BOOKING_NO_ID']][$val['DETARMINATION_ID']][$val['GSM']][$val['COLOR_ID']] += $val['QTY'];
	}
	// print_r($batch_qty_array);	
	// =================================== GETTING DYEING STATUS ====================================
	$sqlDyeing = "SELECT C.BOOKING_NO_ID,D.DETARMINATION_ID,B.GSM,C.COLOR_ID, B.PRODUCTION_QTY as QTY FROM PRO_FAB_SUBPROCESS A,PRO_FAB_SUBPROCESS_DTLS B,PRO_BATCH_CREATE_MST C,PRODUCT_DETAILS_MASTER D WHERE A.ID=B.MST_ID AND B.PROD_ID=D.ID AND A.BATCH_ID=C.ID AND A.STATUS_ACTIVE=1 AND B.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 AND C.BOOKING_NO IN($bookingNo) AND A.ENTRY_FORM=35 AND A.RESULT=1";
	// echo $sqlDyeing;
	$sqlDyeingRes = sql_select($sqlDyeing);
	$dyeing_status_array = array();
	foreach ($sqlDyeingRes as $val) 
	{
		$dyeing_status_array[$val['BOOKING_NO_ID']][$val['DETARMINATION_ID']][$val['GSM']][$val['COLOR_ID']] += $val['QTY'];
	}
	// print_r($dyeing_status_array);
	// =================================== GETTING RECV FIN. FAB. DATE ====================================
	$sqlRcvDate = "SELECT C.BOOKING_NO_ID,B.FABRIC_DESCRIPTION_ID,B.GSM,B.COLOR_ID, MAX(A.RECEIVE_DATE) AS RCV_DATE from inv_receive_master a,pro_finish_fabric_rcv_dtls b,pro_batch_create_mst c where a.id=b.mst_id and c.id=b.batch_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.booking_no in($bookingNo) and a.entry_form=225 and a.item_category=2 group by c.booking_no_id,b.fabric_description_id,b.gsm,b.color_id";
	// echo $sqlRcvDate;
	$sqlRcvDateRes = sql_select($sqlRcvDate);
	$rcvdate_status_array = array();
	foreach ($sqlRcvDateRes as $val) 
	{
		$rcvdate_status_array[$val['BOOKING_NO_ID']][$val['FABRIC_DESCRIPTION_ID']][$val['GSM']][$val['COLOR_ID']] = $val['RCV_DATE'];
	}
	// print_r($dyeing_status_array);
	// =================================== calculate rowspan =========================================
	$rowspan_arr = array();
	foreach ($dataArray as $styleNo => $styleData) 
	{
		foreach ($styleData as $bookNo => $bookData) 
		{
			foreach ($bookData as $deterId => $deterData) 
			{
				foreach ($deterData as $gsm => $gsmData) 
				{
					foreach ($gsmData as $colorId => $colorData) 
					{
						$rowspan_arr[$styleNo][$bookNo]++;
					}
				}
			}
		}
	}
	ob_start();
	?>
	<div style="width:1620px">
	<style type="text/css">
		table tr td{ vertical-align: middle; }
	</style>
	<fieldset style="width:100%;">	
		<table width="1600">
			<tr class="form_caption">
				<td colspan="19" align="center"><h2>Sample Development Followup Report</h2></td>
			</tr>
			<tr class="form_caption">
				<td colspan="19" align="center"><? echo $company_library[$company_name]; ?></td>
			</tr>
		</table>
        
        <br />
        <table class="rpt_table" width="1600" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
				<th width="40">Sl</th>
                <th width="100">Booking No.</th>
                <th width="110">Style</th>
                <th width="80">Delivery Date</th>
                <th width="40">Image</th>
                <th width="80">Attention</th>
                <th width="80">Construction</th>
                <th width="170">Composition</th>
                <th width="60">GSM</th>
                <th width="100">Colour</th>
                <th width="80">Fab. Qnty</th>
                <th width="80">Fab. UOM</th>
                <th width="80">Knit Status</th>
                <th width="80">Grey Receive</th>
                <th width="80">Grey Issue</th>
                <th width="80">Batch Qty</th>
                <th width="80">Dyeing Status</th>
                <th width="80">Fin. Fab. Rcv. Date</th>
                <th width="100">Remarks</th>
			</thead>
		</table>
		<div style="width:1620px; max-height:400px; overflow-y:auto;" id="scroll_body">
            <table class="rpt_table" width="1600" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<tbody>
				<?
				$sl=1;
				$i=1;
				$tot_fab_qty 		= 0;
				$tot_knit_qty 		= 0;
				$tot_grey_rcv_qty 	= 0;
				$tot_grey_iss_qty 	= 0;
				$tot_batch_qty 		= 0;
				$tot_dyeing_qty 	= 0;
				foreach ($dataArray as $styleNo => $styleData) 
				{
					foreach ($styleData as $booking_id => $booking_data) 
					{
						$r=0;
						foreach ($booking_data as $deter_id => $deter_data) 
						{
							foreach ($deter_data as $gsm => $gsm_data) 
							{
								foreach ($gsm_data as $color_id => $row) 
								{
									$knit_status= $knit_status_array[2][$booking_id][$deter_id][$gsm][$color_id];
									$grey_status= $knit_status_array[58][$booking_id][$deter_id][$gsm][$color_id];
									$dye_status = $dyeing_status_array[$booking_id][$deter_id][$gsm][$color_id];
									$rcv_date 	= $rcvdate_status_array[$booking_id][$deter_id][$gsm][$color_id];
									$booking_qty= $book_qty_array[$booking_id][$gsm][$color_id];
									$batch_qty= $batch_qty_array[$booking_id][$deter_id][$gsm][$color_id];
									//$issue_qty= $issue_qty_array[$row['REQNO']][$deter_id][$gsm][$color_id];
									$issue_qty= $issue_qty_array[$booking_id][$deter_id][$gsm][$color_id];
									$bookingNO 	= $row['REQNO'];

									$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ; 
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">
										<? if($r==0){?>
										<td rowspan="<? echo $rowspan_arr[$styleNo][$booking_id];?>" width="40"><? echo $sl; ?></td>
					                    <td rowspan="<? echo $rowspan_arr[$styleNo][$booking_id];?>" width="100"><? echo $row['REQNO']; ?></td>
					                    <td rowspan="<? echo $rowspan_arr[$styleNo][$booking_id];?>" width="110"><? echo $row['STYLE']; ?></td>
					                    <td rowspan="<? echo $rowspan_arr[$styleNo][$booking_id];?>" width="80" align="center"><? echo change_date_format($row['DATE']); ?></td>
					                    <td rowspan="<? echo $rowspan_arr[$styleNo][$booking_id];?>" width="40">
						                    <a href="javascript:void(0)" onclick="openImageWindow('<? echo $row[REQNO];?>')">
						                    	<img src="../../<? echo $image_library[$row['REQNO']]; ?>" width="40" height="25">
					                    	</a>
					                    </td>
					                    <td rowspan="<? echo $rowspan_arr[$styleNo][$booking_id];?>" width="80"><? echo $row['ATTENTION']; ?></td>
					                    <?$sl++;}?>
					                    <td width="80"><? echo $construction_arr[$deter_id]; ?></td>
					                    <td width="170"><? echo $composition_arr[$deter_id]; ?></td>
					                    <td align="center" width="60"><? echo $gsm; ?></td>
					                    <td width="100" title="<? echo $color_id;?>"><? echo $color_library[$color_id]; ?></td>
					                    <td align="right" width="80"><? echo number_format($booking_qty,2); ?></td>
					                    <td align="center" width="80"><? echo $unit_of_measurement[$row['UOM']]; ?></td>
					                    <td align="right" width="80">
					                    	<a href="javascript:void(0)" onclick="openpopup('<? echo $styleNo."__".$booking_id."__".$bookingNO."__".$deter_id."__".$gsm."__".$color_id; ?>','kniting_satus_popup');">
					                    		<? echo number_format($knit_status,2); ?>
					                    	</a>
					                    </td>
					                    <td align="right" width="80">
					                    		<? echo number_format($grey_status,2); ?>
					                    </td>
					                    <td align="right" width="80">
					                    		<? echo number_format($issue_qty,2); ?>
					                    </td>
					                    <td align="right" width="80">
					                    		<? echo number_format($batch_qty,2); ?>
					                    </td>
					                    <td align="right" width="80">
					                    	<a href="javascript:void(0)" onclick="openpopup('<? echo $styleNo."__".$booking_id."__".$bookingNO."__".$deter_id."__".$gsm."__".$color_id; ?>','dyeing_satus_popup');">
					                   			<? echo number_format($dye_status,2); ?>
					                   		</a>					                    	
					                    </td>
					                    <td align="center" width="80"><? echo change_date_format($rcv_date);; ?></td>
					                    <td width="100"><? echo $row['REMARKS']; ?></td>
					                </tr>
									<?
									$r++;
									$i++;
									$tot_fab_qty 		+= $booking_qty;
									$tot_knit_qty 		+= $knit_status;
									$tot_grey_rcv_qty 	+= $grey_status;
									$tot_grey_iss_qty 	+= $issue_qty;
									$tot_batch_qty 		+= $batch_qty;
									$tot_dyeing_qty 	+= $dye_status;
								}
							}
						}
						
					}
				}
				?>
					
				</tbody>
			</table>
		</div>
		<div class="tbl-bottom">
			<table class="rpt_table" width="1600" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tfoot>
					<th width="40"></th>
                    <th width="100"></th>
                    <th width="110"></th>
                    <th width="80"></th>
                    <th width="40"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="170"></th>
                    <th width="60"></th>
                    <th width="100">Total</th>
                    <th width="80"><? echo number_format($tot_fab_qty,2); ?></th>
                    <th width="80"></th>
                    <th width="80"><? echo number_format($tot_knit_qty,2); ?></th>
                    <th width="80"><? echo number_format($tot_grey_rcv_qty,2); ?></th>
                    <th width="80"><? echo number_format($tot_grey_iss_qty,2); ?></th>
                    <th width="80"><? echo number_format($tot_batch_qty,2); ?></th>
                    <th width="80"><? echo number_format($tot_dyeing_qty,2); ?></th>
                    <th width="80"></th>
                    <th width="100"></th>
				</tfoot>
			</table>
		</div>
		</fieldset>
	</div>
	<?
	
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows";
	exit();	
}

if($action=="kniting_satus_popup")
{
	echo load_html_head_contents("Material Description Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('__',$data);
	$styleNo 	= $expData[0];
	$bookingid 	= $expData[1];
	$bookingno 	= $expData[2];
	$deter_id 	= $expData[3];
	$gsm 		= $expData[4];
	$color 		= $expData[5];
	?>
    <fieldset style="width:460px">
    	<script>
		function new_window()
		{
			// $(".flt").css("display","none");
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write(document.getElementById('rptContainer').innerHTML);
			d.close();
			// $(".flt").css("display","block");
		}
		</script>
		<div style="text-align: center;">
    		<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window()" />
    	</div>
    	<div  id="rptContainer">
	        <div style="width:460px;" align="center">
	            <table cellpadding="0" width="440" class="rpt_table" rules="all" border="1" align="left">
	                <thead>
	                	<tr>
	                    	<th colspan="5">Kniting Status Details</th>
	                    </tr>
	                    <tr>
	                        <th width="30">SL</th>
	                        <th width="130">Prod. No</th>
	                        <th width="70">Prod. Date</th>
	                        <th width="130">Booking No</th>
	                        <th width="80">Prod. Qty</th>
	                    </tr>
	                </thead>
	            </table>       
		        <div style="width:460px; max-height:230px; overflow-y:auto;" align="left">
		            <table cellpadding="0" width="440" class="rpt_table" rules="all" border="1" align="left">
		                <?
		                $i=0;
		                $sql= "SELECT A.RECV_NUMBER,A.RECEIVE_DATE,A.BOOKING_NO, SUM(B.GREY_RECEIVE_QNTY) as QTY FROM INV_RECEIVE_MASTER A,PRO_GREY_PROD_ENTRY_DTLS B WHERE A.ID=B.MST_ID AND A.STATUS_ACTIVE=1 AND B.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.IS_DELETED=0 AND A.BOOKING_NO IN('$bookingno') AND B.GSM=$gsm AND B.FEBRIC_DESCRIPTION_ID=$deter_id and B.COLOR_ID='$color' GROUP BY A.RECV_NUMBER,A.RECEIVE_DATE,A.BOOKING_NO";
		                // echo $sql;
		                $sqlRes= sql_select($sql);
		                foreach( $sqlRes as $row )
		                {
		                    $i++;
		                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";	                    
		                    ?>
		                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
		                        <td width="30"><? echo $i; ?></td>
		                        <td width="130"><? echo $row['RECV_NUMBER'];?> </td>
		                        <td align="center" width="70"><? echo change_date_format($row['RECEIVE_DATE']);?> </td> 
		                        <td align="left" width="130"><? echo $row['BOOKING_NO']; ?></td>
		                        <td width="80" align="right"><? echo number_format($row["QTY"],2); ?></td>
		                    </tr>
		                    <? 
		                    $tot_qty+=$row["QTY"];
		                } ?>
		                <tr class="tbl_bottom">
		                    <td colspan="4" align="right">Total: </td>
		                    <td width="80" align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
		                </tr>
		            </table>
		        </div> 
	        </div>
	    </div>
	</fieldset>
   
 
	<?
	exit();
}

if($action=="dyeing_satus_popup")
{
	echo load_html_head_contents("Material Description Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('__',$data);
	$styleNo 	= $expData[0];
	$bookingid 	= $expData[1];
	$bookingno 	= $expData[2];
	$deter_id 	= $expData[3];
	$gsm 		= $expData[4];
	$color 		= $expData[5];
	$machine_arr = return_library_array("select id, machine_no as machine_name from lib_machine_name","id","machine_name");
	$floor_arr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name"); 
	?>
    <fieldset style="width:730px">
    	<script>
		function new_window()
		{
			// $(".flt").css("display","none");
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write(document.getElementById('rptContainer').innerHTML);
			d.close();
			// $(".flt").css("display","block");
		}
		</script>
		<div style="text-align: center;">
    		<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window()" />
    	</div>
    	<div  id="rptContainer">
	        <div style="width:730px;" align="center">
	            <table cellpadding="0" width="710" class="rpt_table" rules="all" border="1" align="left">
	                <thead>
	                	<tr>
	                    	<th colspan="8">Dyeing Status Details</th>
	                    </tr>
	                    <tr>
	                        <th width="30">SL</th>
	                        <th width="130">Batch. No</th>
	                        <th width="70">Batch. Date</th>
	                        <th width="70">Prod. Date</th>
	                        <th width="100">Prod. Floor</th>
	                        <th width="100">M/C No</th>
	                        <th width="130">Booking No</th>
	                        <th width="80">Prod. Qty</th>
	                    </tr>
	                </thead>
	            </table>       
		        <div style="width:730px; max-height:230px; overflow-y:auto;" align="left">
		            <table cellpadding="0" width="710" class="rpt_table" rules="all" border="1" align="left">
		                <?
		                $i=0;
		                $sql= "SELECT A.MACHINE_ID,A.FLOOR_ID,A.PRODUCTION_DATE,C.BATCH_NO,C.BATCH_DATE,C.BOOKING_NO, B.PRODUCTION_QTY as QTY FROM PRO_FAB_SUBPROCESS A,PRO_FAB_SUBPROCESS_DTLS B,PRO_BATCH_CREATE_MST C,PRODUCT_DETAILS_MASTER D WHERE A.ID=B.MST_ID AND B.PROD_ID=D.ID AND A.BATCH_ID=C.ID AND A.STATUS_ACTIVE=1 AND B.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 AND C.BOOKING_NO IN('$bookingno') AND D.DETARMINATION_ID=$deter_id and D.GSM=$gsm AND A.ENTRY_FORM=35 AND A.RESULT=1";
		                // echo $sql;
		                $sqlRes= sql_select($sql);
		                foreach( $sqlRes as $row )
		                {
		                    $i++;
		                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";	                    
		                    ?>
		                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
		                        <td width="30"><? echo $i; ?></td>
		                        <td width="130"><? echo $row['BATCH_NO'];?> </td>
		                        <td align="center" width="70"><? echo change_date_format($row['BATCH_DATE']);?> </td> 
		                        <td align="center" width="70"><? echo change_date_format($row['PRODUCTION_DATE']);?> </td> 
		                        <td align="center" width="100"><? echo $floor_arr[$row['FLOOR_ID']];?> </td> 
		                        <td align="center" width="100"><? echo $machine_arr[$row['MACHINE_ID']];?> </td> 
		                        <td align="left" width="130"><? echo $row['BOOKING_NO']; ?></td>
		                        <td width="80" align="right"><? echo number_format($row["QTY"],2); ?></td>
		                    </tr>
		                    <? 
		                    $tot_qty+=$row["QTY"];
		                } ?>
		                <tr class="tbl_bottom">
		                    <td colspan="7" align="right">Total: </td>
		                    <td width="80" align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
		                </tr>
		            </table>
		        </div> 
	        </div>
	    </div>
	</fieldset>
   
 
	<?
	exit();
}
disconnect($con);
?>