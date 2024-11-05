<?php
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
//--------------------------------------------------------------------------------------------

//for load_drop_down_issue_purpose
if ($action == "load_drop_down_issue_purpose")
{
	$sql = "SELECT issue_purpose AS ISSUE_PURPOSE FROM inv_issue_master WHERE entry_form = 3 AND item_category = 1 AND status_active = 1 AND is_deleted = 0 and company_id in(".$data.")";
	$sqlRslt = sql_select($sql);
	$issuePurposeArr = array();
	foreach($sqlRslt as $row)
	{
		$issuePurposeArr[$row['ISSUE_PURPOSE']] = $row['ISSUE_PURPOSE'];
	}
	
	echo create_drop_down("cbo_issue_purpose", 150, $yarn_issue_purpose, "", "0", "", $selected, "", "", implode(',',$issuePurposeArr),"","","");
	exit();
}

//for load_drop_down_store
if ($action == "load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 150, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.status_active=1 and a.is_deleted=0 and company_id in(".$data.") and b.category_type in(1) group by a.id, a.store_name order by a.store_name","id,store_name", 0, "", 0, "" );
}

//for generate_report
if ($action == "generate_report")
{
	//echo "su..re"; die;
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$cbo_company_name = str_replace("'","",$cbo_company_name);
	$cbo_receive_basis = str_replace("'","",$cbo_receive_basis);
	$cbo_receive_purpose = str_replace("'","",$cbo_receive_purpose);
	$cbo_issue_purpose = str_replace("'","",$cbo_issue_purpose);
	$cbo_store_name = str_replace("'","",$cbo_store_name);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	
	if($db_type==0)
	{
		$from_date = change_date_format($txt_date_from,'yyyy-mm-dd');
		$to_date = change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$from_date = change_date_format($txt_date_from,'','',1);
		$to_date = change_date_format($txt_date_to,'','',1);
	}

	//for receive
	$sqlReceive = "
		SELECT
			a.id AS ID, a.receive_purpose AS RECEIVE_PURPOSE, a.entry_form AS ENTRY_FORM
		FROM
			inv_receive_master a
		WHERE
			a.status_active=1 AND 
			a.is_deleted=0 AND
			a.company_id IN(".$cbo_company_name.") AND
			a.receive_basis IN(".$cbo_receive_basis.") AND 
			a.receive_purpose IN(".$cbo_receive_purpose.") AND
			a.store_id IN(".$cbo_store_name.") AND
			a.receive_date BETWEEN '".$from_date."' AND '".$to_date."' AND
			a.item_category = 1 AND
			a.entry_form IN(1,9)";
	//echo $sqlReceive; die;
	$rsltReceive = sql_select($sqlReceive);
	$receiveData = array();
	foreach($rsltReceive as $row)
	{
		if($row['RECEIVE_PURPOSE'] == 5)
		{
			$rcv = $row['ID']."_1_loan";
			$receiveData[$rcv] = $row['ID'];
		}
		else
		{
			if($row['ENTRY_FORM'] == 1)
			{
				$rcv = $row['ID']."_1";
				$receiveData[$rcv] = $row['ID'];
			}
			elseif($row['ENTRY_FORM'] == 9)
			{
				$rcv = $row['ID']."_1_issuereturn";
				$receiveData[$rcv] = $row['ID'];
			}
		}
	}
	//echo "<pre>";
	//print_r($receiveData); die;
	
	//for issue
	$sqlIssue = "
		SELECT
			a.id AS ID, a.issue_purpose AS ISSUE_PURPOSE, a.entry_form AS ENTRY_FORM
		FROM
			inv_issue_master a
		WHERE
			a.status_active=1 AND 
			a.is_deleted=0 AND
			a.company_id IN(".$cbo_company_name.") AND
			a.issue_purpose IN(".$cbo_issue_purpose.") AND
			a.store_id IN(".$cbo_store_name.") AND
			a.issue_date BETWEEN '".$from_date."' AND '".$to_date."' AND
			a.item_category = 1 AND
			a.entry_form IN(3,8)";
	//echo $sqlIssue; die;
	$rsltIssue = sql_select($sqlIssue);
	$issueData = array();
	foreach($issueData as $row)
	{
		if($row['ISSUE_PURPOSE'] == 5)
		{
			$issue = $row['ID']."_2_loan";
			$issueData[$issue] = $row['ID'];
		}
		else
		{
			if($row['ENTRY_FORM'] == 3)
			{
				$issue = $row['ID']."_2";
				$issueData[$issue] = $row['ID'];
			}
			elseif($row['ENTRY_FORM'] == 8)
			{
				$issue = $row['ID']."_2_receivereturn";
				$issueData[$issue] = $row['ID'];
			}
		}
	}
	//echo "<pre>";
	//print_r($issueData); die;

	//for transaction
	$sqlTransaction="
		SELECT
			a.mst_id AS MST_ID, a.transaction_date AS TRANSACTION_DATE, a.transaction_type AS TRANSACTION_TYPE, a.cons_quantity AS CONS_QUANTITY, a.cons_amount AS CONS_AMOUNT
		FROM
			inv_transaction a
		WHERE
			a.status_active=1 AND 
			a.is_deleted=0 AND 
			a.company_id IN(".$cbo_company_name.") AND
			a.store_id IN(".$cbo_store_name.") AND
			a.item_category = 1 AND
			a.transaction_type IN(1,2,3,4,5,6)
	";
	//echo $sqlTransaction; die;
	$rsltTransaction = sql_select($sqlTransaction);
	$data_array = array();
	$issz = 0;
	foreach($rsltTransaction as $row)
	{
		$row["TRANSACTION_DATE"] = date('d-M-Y',strtotime($row["TRANSACTION_DATE"]));
		if($row["TRANSACTION_TYPE"] == 1)
		{
			if(strtotime($row["TRANSACTION_DATE"]) < strtotime($from_date))
			{
				$data_array['rcv_total_opening']+=$row["CONS_QUANTITY"];
				$data_array['rcv_total_opening_amt']+=$row["CONS_AMOUNT"];
			}
			else if( (strtotime($row["TRANSACTION_DATE"]) >= strtotime($from_date)) && (strtotime($row["TRANSACTION_DATE"]) <= strtotime($to_date)))
			{
				//for loan
				if($row["MST_ID"]==$receiveData[$row["MST_ID"]."_1_loan"])
				{
					$data_array['receive_loan']+=$row["CONS_QUANTITY"];
					$data_array['receive_loan_amt']+=$row["CONS_AMOUNT"];
				}
				elseif($row["MST_ID"]==$receiveData[$row["MST_ID"]."_1"])
				{
					$data_array['purchase']+=$row["CONS_QUANTITY"];
					$data_array['purchase_amt']+=$row["CONS_AMOUNT"];
				}
			}
		}
		else if($row["TRANSACTION_TYPE"] == 2)
		{
			if(strtotime($row["TRANSACTION_DATE"]) < strtotime($from_date))
			{
				$data_array['iss_total_opening']+=$row["CONS_QUANTITY"];
				$data_array['iss_total_opening_amt']+=$row["CONS_AMOUNT"];
			}
			else if( (strtotime($row["TRANSACTION_DATE"]) >= strtotime($from_date)) && (strtotime($row["TRANSACTION_DATE"]) <= strtotime($to_date)))
			{
				//for loan
				if($row["MST_ID"]==$issueData[$row["MST_ID"]."_2_loan"])
				{
					$data_array['issue_loan']+=$row["CONS_QUANTITY"];
					$data_array['issue_loan_amt']+=$row["CONS_AMOUNT"];
				}
				else
				{
					$data_array['issue']+=$row["CONS_QUANTITY"];
					$data_array['issue_amt']+=$row["CONS_AMOUNT"];
				}
			}
		}
		else if($row["TRANSACTION_TYPE"]==3)
		{
			if(strtotime($row["TRANSACTION_DATE"])<strtotime($from_date))
			{
				$data_array['iss_total_opening']+=$row["CONS_QUANTITY"];
				$data_array['iss_total_opening_amt']+=$row["CONS_AMOUNT"];
			}
			else if( (strtotime($row["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row["TRANSACTION_DATE"])<=strtotime($to_date)))
			{
				//for loan
				if($row["MST_ID"]==$issueData[$row["MST_ID"]."_2_loan"])
				{
					$data_array['issue_loan']+=$row["CONS_QUANTITY"];
					$data_array['issue_loan_amt']+=$row["CONS_AMOUNT"];
				}
				elseif($row["MST_ID"]==$issueData[$row["MST_ID"]."_2_receivereturn"])
				{
					$data_array['receive_return']+=$row["CONS_QUANTITY"];
					$data_array['receive_return_amt']+=$row["CONS_AMOUNT"];
				}
			}
		}
		else if($row["TRANSACTION_TYPE"]==4)
		{
			if(strtotime($row["TRANSACTION_DATE"])<strtotime($from_date))
			{
				$data_array['rcv_total_opening']+=$row["CONS_QUANTITY"];
				$data_array['rcv_total_opening_amt']+=$row["CONS_AMOUNT"];
			}
			else if( (strtotime($row["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row["TRANSACTION_DATE"])<=strtotime($to_date)))
			{
				//for loan
				if($row["MST_ID"]==$receiveData[$row["MST_ID"]."_1_loan"])
				{
					$data_array['receive_loan']+=$row["CONS_QUANTITY"];
					$data_array['receive_loan_amt']+=$row["CONS_AMOUNT"];
				}
				elseif($row["MST_ID"]==$receiveData[$row["MST_ID"]."_1_issuereturn"])
				{
					$data_array['issue_return']+=$row["CONS_QUANTITY"];
					$data_array['issue_return_amt']+=$row["CONS_AMOUNT"];
				}
			}
		}
		else if($row["TRANSACTION_TYPE"]==5)
		{
			if(strtotime($row["TRANSACTION_DATE"]) < strtotime($from_date))
			{
				$data_array['rcv_total_opening']+=$row["CONS_QUANTITY"];
				$data_array['rcv_total_opening_amt']+=$row["CONS_AMOUNT"];
			}
		}
		else if($row["TRANSACTION_TYPE"]==6)
		{
			if(strtotime($row["TRANSACTION_DATE"]) < strtotime($from_date))
			{
				$data_array['iss_total_opening']+=$row["CONS_QUANTITY"];
				$data_array['iss_total_opening_amt']+=$row["CONS_AMOUNT"];
			}
		}
	}

	//for conversion rate
	$sqlConversion = "SELECT conversion_rate AS CONVERSION_RATE FROM currency_conversion_rate WHERE status_active = 1 AND is_deleted = 0 AND currency = 2 AND company_id IN(".$cbo_company_name.") ORDER BY id ASC";
	$rsltConversion = sql_select($sqlConversion);
	$conversion_rate = 0;
	foreach($rsltConversion as $row)
	{
		$conversion_rate = $row["CONVERSION_RATE"];
	}

	//for opening
	$openingQty = $data_array['rcv_total_opening'] - $data_array['iss_total_opening'];
	$openingValueTK = $data_array['rcv_total_opening_amt'] - $data_array['iss_total_opening_amt'];
	$openingValueUSD = get_tkToUsd($arr=array('rate'=>$conversion_rate, 'tk'=>$openingValueTK));
	
	//for purchase
	$purchaseQty = $data_array['purchase'] - $data_array['receive_return'];
	$purchaseValueTK = $data_array['purchase_amt'] - $data_array['receive_return_amt'];
	$purchaseValueUSD = get_tkToUsd($arr=array('rate'=>$conversion_rate, 'tk'=>$purchaseValueTK));
	
	//for loan
	$loanQty = $data_array['receive_loan'] - $data_array['issue_loan'];
	$loanValueTK = $data_array['receive_loan_amt'] - $data_array['issue_loan_amt'];
	$loanValueUSD = get_tkToUsd($arr=array('rate'=>$conversion_rate, 'tk'=>$loanValueTK));
	
	//for consumption
	$consumptionQty = $data_array['issue'] - $data_array['issue_return'];
	$consumptionValueTK = $data_array['issue_amt'] - $data_array['issue_return_amt'];
	$consumptionValueUSD = get_tkToUsd($arr=array('rate'=>$conversion_rate, 'tk'=>$consumptionValueTK));
	
	//for closing
	/*
	$runTimeQty = ($data_array['purchase'] + $data_array['issue_return']) - ($data_array['issue'] + $data_array['receive_return']);
	$clossingQty = $openingQty+$runTimeQty;
	$runTimeValueTK = ($data_array['purchase_amt'] + $data_array['issue_return_amt']) - ($data_array['issue_amt'] + $data_array['receive_return_amt']);
	$clossingValueTK=$openingValueTK+$runTimeValueTK;
	*/
	//(Opening + Purchase + Loan)-Consumption
	$clossingQty = $openingQty+$purchaseQty+$loanQty-$consumptionQty;
	$clossingValueTK = $openingValueTK+$purchaseValueTK+$loanValueTK-$consumptionValueTK;
	$clossingValueUSD = get_tkToUsd($arr=array('rate'=>$conversion_rate, 'tk'=>$clossingValueTK));

	$width = 600;
	ob_start();
	?>
	<fieldset style="width:<? echo $width; ?>px;margin:5px auto;">
        <table width="<? echo $width; ?>" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
			<thead>
				<tr>
					<th width="100">Particulars</th>
					<th width="100">Quantity</th>
					<th width="100">Value (Tk)</th>
					<th width="100">Value (USD)</th>
					<th>Remarks</th>
				</tr>
			</thead>
            <tbody>
            	<tr>
                	<td>Opening Balance</td>
                    <td align="right"><?php echo number_format($openingQty, 2); ?></td>
                    <td align="right"><?php echo number_format($openingValueTK, 2); ?></td>
                    <td align="right"><?php echo number_format($openingValueUSD, 2); ?></td>
                    <td><?php ?></td>
                </tr>
            	<tr>
                	<td>Purchase</td>
                    <td align="right"><?php echo number_format($purchaseQty, 2); ?></td>
                    <td align="right"><?php echo number_format($purchaseValueTK, 2); ?></td>
                    <td align="right"><?php echo number_format($purchaseValueUSD, 2); ?></td>
                    <td><?php ?></td>
                </tr>
            	<tr>
                	<td>Loan</td>
                    <td align="right"><?php echo number_format($loanQty, 2); ?></td>
                    <td align="right"><?php echo number_format($loanValueTK, 2); ?></td>
                    <td align="right"><?php echo number_format($loanValueUSD, 2); ?></td>
                    <td><?php ?></td>
                </tr>
            	<tr>
                	<td>Consumption</td>
                    <td align="right"><?php echo number_format($consumptionQty, 2); ?></td>
                    <td align="right"><?php echo number_format($consumptionValueTK, 2); ?></td>
                    <td align="right"><?php echo number_format($consumptionValueUSD, 2); ?></td>
                    <td><?php ?></td>
                </tr>
            	<tr>
                	<td>Closing Stock</td>
                    <td align="right"><?php echo number_format($clossingQty, 2)?></td>
                    <td align="right"><?php echo number_format($clossingValueTK, 2)?></td>
                    <td align="right"><?php echo number_format($clossingValueUSD, 2); ?></td>
                    <td><?php ?></td>
                </tr>
            </tbody>
		</table>
	</fieldset>
	<?php
	exit();
}

//get_usdAmount
function get_tkToUsd($arr)
{
	$usd = 0;
	if(!empty($arr))
	{
		$usd = ($arr['tk']/$arr['rate']);
	}
	return $usd;	
}
?>