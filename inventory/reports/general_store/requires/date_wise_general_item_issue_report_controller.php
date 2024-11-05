<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------

if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_item_cat=str_replace("'","",$cbo_item_cat);
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_challan_no=str_replace("'","",$txt_challan_no);
	
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	
	$search_cond="";
	if($cbo_item_cat>0) $search_cond .= " and a.item_category in($cbo_item_cat)"; else $search_cond .= " and a.item_category in(".implode(",",array_flip($general_item_category)).")";
	$issue_mst_id_arr=array();$current_prod_id_arr=array();$current_req_id_arr=array();
	if($txt_challan_no!="") 
	{
		$sql_issue_mrr = "select a.ID AS TRID, a.TRANSACTION_TYPE, a.PROD_ID, b.ID, b.ISSUE_NUMBER, b.REQ_ID, b.REQ_NO from inv_transaction a, inv_issue_master b
		where a.mst_id=b.id and a.company_id=$cbo_company_name and a.transaction_type in(2) and b.entry_form=21 and b.REQ_NO='$txt_challan_no' and a.TRANSACTION_DATE between '$txt_date_from' and '$txt_date_to' $search_cond ";		 
		$result_iss = sql_select($sql_issue_mrr);
		$issueMRR=array();
		foreach($result_iss as $row)
		{
			$issue_mst_id_arr[$row["ID"]]=$row["ID"];
			$issueMRR[$row["TRID"]."##".$row["TRANSACTION_TYPE"]]["ISSUE_NUMBER"] = $row["ISSUE_NUMBER"];
			$issueMRR[$row["TRID"]."##".$row["TRANSACTION_TYPE"]]["REQ_ID"] = $row["REQ_ID"];
			$issueMRR[$row["TRID"]."##".$row["TRANSACTION_TYPE"]]["REQ_NO"] = $row["REQ_NO"];
			$current_prod_id_arr[$row["PROD_ID"]]=$row["PROD_ID"];
			if($row["REQ_ID"]) $current_req_id_arr[$row["REQ_ID"]]=$row["REQ_ID"];
		}
	}
	else
	{
		$sql_issue_mrr = "select a.ID AS TRID, a.TRANSACTION_TYPE, a.PROD_ID, b.ID, b.ISSUE_NUMBER, b.REQ_ID, b.REQ_NO from inv_transaction a, inv_issue_master b
		where a.mst_id=b.id and a.company_id=$cbo_company_name and a.transaction_type in(2) and b.entry_form=21 and a.TRANSACTION_DATE between '$txt_date_from' and '$txt_date_to' $search_cond ";
		//echo $sql_issue_mrr;		 
		$result_iss = sql_select($sql_issue_mrr);
		$issueMRR=array();
		foreach($result_iss as $row)
		{
			$issueMRR[$row["TRID"]."##".$row["TRANSACTION_TYPE"]]["ISSUE_NUMBER"] = $row["ISSUE_NUMBER"];
			$issueMRR[$row["TRID"]."##".$row["TRANSACTION_TYPE"]]["REQ_ID"] = $row["REQ_ID"];
			$issueMRR[$row["TRID"]."##".$row["TRANSACTION_TYPE"]]["REQ_NO"] = $row["REQ_NO"];
			$current_prod_id_arr[$row["PROD_ID"]]=$row["PROD_ID"];
			if($row["REQ_ID"]) $current_req_id_arr[$row["REQ_ID"]]=$row["REQ_ID"];
		}
	}
	
	
	
	$con = connect();
	$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (21)");
    oci_commit($con);
	if(count($current_prod_id_arr)>0) fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 21, 1, $current_prod_id_arr, $empty_arr);
	if(count($current_req_id_arr)>0) fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 21, 2, $current_req_id_arr, $empty_arr);
	
	$req_qnty_sql="select b.MST_ID, b.PRODUCT_ID, b.REQ_QTY from INV_ITEMISSUE_REQUISITION_DTLS b, GBL_TEMP_ENGINE c where b.mst_id=c.REF_VAL and c.ENTRY_FORM=21 and c.REF_FROM=2 and b.status_active=1";
	//echo $req_qnty_sql;die;
	$req_qnty_sql_result = sql_select($req_qnty_sql);
	$req_data=array();
	foreach($req_qnty_sql_result as $val)
	{
		$req_data[$val["MST_ID"]][$val["PRODUCT_ID"]]+=$val["REQ_QTY"];
	}
	unset($req_qnty_sql_result);
	
	$sql = "select a.ID, a.MST_ID, a.INSERT_DATE, a.PROD_ID, a.TRANSACTION_DATE, a.TRANSACTION_TYPE, a.CONS_QUANTITY, a.CONS_RATE, a.CONS_AMOUNT, b.ITEM_GROUP_ID, b.ITEM_CODE, b.ITEM_NUMBER, b.SUB_GROUP_NAME, b.ITEM_DESCRIPTION, b.UNIT_OF_MEASURE, b.ITEM_CATEGORY_ID
	from inv_transaction a, product_details_master b, GBL_TEMP_ENGINE c 
	where a.prod_id=b.id and b.id=c.REF_VAL and c.ENTRY_FORM=21 and c.REF_FROM=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name $search_cond 
	order by a.prod_id, a.insert_date, a.id ASC";
	//echo $sql;die;
	$result = sql_select($sql);	
	$width="1300";
	$table_width="1280";
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (21)");
	oci_commit($con);
	disconnect($con);
	ob_start();	
	?>
	<fieldset>
		<div>
			<table width="<?= $width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
				<thead>
					<tr class="form_caption" style="border:none;">
						<td colspan="15" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?> </td>
					</tr>
					<tr style="border:none;">
						<td colspan="15" align="center" style="border:none; font-size:14px;">
							Company Name : <? echo $companyArr[str_replace("'","",$cbo_company_name)]; ?>
						</td>
					</tr>
					<tr>
						<th width="30">SL</th>
						<th width="50">Product ID</th>
						<th width="70">Trans Date</th>
						<th width="110">Issue Number</th>
						<th width="110">SR Number</th>
						<th width="120">Item Category</th>
						<th width="70">Item Code</th>
						<th width="80">Item Number</th>
						<th width="100">Sub Group</th>
						<th width="150">Item Description</th>
						<th width="50">UOM</th>
						<th width="80">Reqd.Qty</th>
						<th width="80">Issue Qty</th>
                        <th width="90">Balance After Issue</th>
                        <th>Remarks</th>
					</tr>
				</thead>
			</table>
			<div style="width:<?= $width;?>px; overflow-y:scroll; max-height:250px; overflow-x: hidden;" id="scroll_body" >
			<table width="<?=$width?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
				<tbody>
				<?
				$i=1;$m=1;$product_id_arr=array();$k=1;
				foreach($result as $row)
				{
					if ($i%2==0)$bgcolor="#E9F3FF";	else $bgcolor="#FFFFFF";
					if($product_id_arr[$row["PROD_ID"]]=="")
					{
						$product_id_arr[$row["PROD_ID"]]=$row["PROD_ID"];
						$balance_qnty=0;
					}
					
					if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5) $balance_qnty += $row["CONS_QUANTITY"];
					if($row["TRANSACTION_TYPE"]==2 || $row["TRANSACTION_TYPE"]==3 || $row["TRANSACTION_TYPE"]==6) $balance_qnty -= $row["CONS_QUANTITY"];
					//'$txt_date_from' and '$txt_date_to'
					if($row["TRANSACTION_TYPE"]==2 && strtotime($row["TRANSACTION_DATE"])>=strtotime($txt_date_from) && strtotime($row["TRANSACTION_DATE"])<=strtotime($txt_date_to))
					{
						//echo $issueMRR[$row["ID"]."##".$row["TRANSACTION_TYPE"]]["REQ_ID"]."=";
						$req_qnty=$req_data[$issueMRR[$row["ID"]."##".$row["TRANSACTION_TYPE"]]["REQ_ID"]][$row["PROD_ID"]];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30" align="center" title="<?= "trans id ".$row["ID"]?>"><? echo $k; ?></td>
							<td width="50" align="center" ><p><? echo $row["PROD_ID"]; ?></p></td>
							<td width="70" align="center"><p><? if($row["TRANSACTION_DATE"] !="" && $row["TRANSACTION_DATE"] !="0000-00-00")  echo change_date_format($row["TRANSACTION_DATE"]); ?>&nbsp;</p></td>
							<td width="110"><p><? echo $issueMRR[$row["ID"]."##".$row["TRANSACTION_TYPE"]]["ISSUE_NUMBER"]; ?></p></td>
							<td width="110"><p><? echo $issueMRR[$row["ID"]."##".$row["TRANSACTION_TYPE"]]["REQ_NO"]; ?></p></td>
							<td width="120"><p><? echo $item_category[$row["ITEM_CATEGORY_ID"]]; ?></p></td>
							<td width="70"><p><? echo $row["ITEM_CODE"]; ?></p></td>
                            <td width="80"><p><? echo $row["ITEM_NUMBER"]; ?></p></td>
                            <td width="100"><p><? echo $row["SUB_GROUP_NAME"]; ?></p></td>
                            <td width="150"><p><? echo $row["ITEM_DESCRIPTION"]; ?></p></td>
                            <td width="50" align="center" ><p><? echo $unit_of_measurement[$row["UNIT_OF_MEASURE"]]; ?></p></td>
							<td width="80" align="right"><? echo number_format($req_qnty,2); ?></td>
                            <td width="80" align="right"><? echo number_format($row["CONS_QUANTITY"],2); ?></td>
                            <td width="90" align="right"><? echo number_format($balance_qnty,2); ?></td>
                            <td>&nbsp;</td>
						</tr>
						<?
						$k++;
					}
					$i++;
				}
				?>
				</tbody>
			</table>
			</div>
		</div>
    </fieldset>
    <?
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("$user_id*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename"; 
	exit();
	 
}

?>

