<?
include('../includes/common.php');
$con = connect();


$sql_dyes_trans="select b.PROD_ID, b.ID as TRANS_ID, b.CONS_QUANTITY, b.CONS_AMOUNT, b.TRANSACTION_TYPE 
from inv_transaction b where b.prod_id in(180269,180289,180299,181477,181961,181971,181981,183393,182759,188270,187384,187394,187414,187424,187364,187374,187404,189782,189792,189802,189771,190225,190245,194820,195594,198470,194945,196117,198482,194810,201966,201104,199089,201956,203539,203549,203559,203528,203694,29071,28738,32919,30919,31000,32901,32729,32810,32874,32892,31718,32639,32792,32801,32693,31637,41993,42002,51948,60864,69816,69825,74699,73397,79614,81609,83592,80704,84104,80713,85638,91696,100826,100817,99265,100707,107741,107732,107812,107830,108971,112902,143237,143693,143700,143545,143386,143389,143986,143990,144001,144011,161649,172120,174592,172896,180664,192473,195971,50063,52451,53081,54421,54128,60710,61080,61089,62630,61053,61062,62621,63935,68944,73217,73235,70103,77691,77700,75721,76258,80499,80443,85843,87941,85656,93636,93519,93564,93573,91319,93555,92413,97885,98460,100763,100754,100790,101169,100835,99552,99570,100745,100808,105321,105186,105195,105204,110374,112386,155540,155608,155619,162923,172140,183317,207476,26451,26460,26487,26496,26514,26532,26173,26595,26631,26281,29702,32062,32386,32395,32341,32107,32458,29657,32350,55931,57969,74672,92920,100206,100215,100653,100662,161508,161498,159907,168949,168959,172077,172406,195480,195490,56521,56524,56563,56713,56717,56750,62947,73818,80069,169678,195737,195715,195732,25510,25546,25582,25402,25429,25141,25150,25159,25186,25213,31790,31835,53162,67570,67579,78255,75982,85995,95461,97246,102398,102553,106007,110767,113193,113526,192032,192119,192022,198099,42609,42618,42627,42636,42645,42654,42843,42708,42726,42744,42419,42527,42455,42753,42538,42546,42555,42564,42573,42582,42591,42500,42509,42518,49765,49801,71671,71680,102252,108256,108274,108328,108373,108382,108283,108319,157830,157850,157860,157870,157880,181951,185949,204983,204932,204942,204952,204973,204962,204921,103878,69209,56031,56487,56771,56788,56799,56971,56989,80087,103889,167659,183878,183885,204658,115782,27090,27374,34713,34731,34758,29747,30447,34569,34587,34596,34632,34686,32206,34614,29738,31054,32946,30157,34974,34983,34848,34875,34902,34920,34947,35046,35145,35192,35201,35210,35219,35237,35293,40659,53271,53316,53406,53424,53280,53289,53298,57372,57381,57390,61677,62666,62675,61735,65583,67706,69308,68361,70927,76593,83662,80821,80830,82119,82128,85016,90510,91454,100399,101262,102289,100435,101291,98934,99014,103920,103732,105828,112093,110401,114788,114797,115604,115613,115622,115631,115640,114258,115649,152545,152557,152573,152123,152171,152723,152276,152830,152287,152373,152407,152941,152964,162253,162265,160861,163015,167841,169658,169277,167602,167154,176299,176783,178259,183484,183464,183474,188291,189902,186551,194349,193143,192012,191919,195460,195470,194971,194981,194961,195618,198666,204618,206172,206971,206961,206986) and b.status_active=1 and b.is_deleted=0 
order by b.PROD_ID, b.ID";

$result=sql_select($sql_dyes_trans);
//echo count($result);die;
$i=1;$k=1;
//$update_field="cons_rate*cons_amount*updated_by*update_date";
$upTransID=true;
foreach($result as $row)
{
	if($prod_check[$row["PROD_ID"]]=="")
	{
		$prod_check[$row["PROD_ID"]]=$row["PROD_ID"];
		$rcv_data[$row["PROD_ID"]]["qnty"]=0;
		$rcv_data[$row["PROD_ID"]]["amt"]=0;
		$runtime_rate=0;
	}
	
	if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
	{
		if($row["TRANSACTION_TYPE"]==4)
		{
			if(number_format($rcv_data[$row["PROD_ID"]]["qnty"],12,'.','') > 0 && number_format($rcv_data[$row["PROD_ID"]]["amt"],12,'.','') > 0)
			{
				$runtime_rate=number_format(($rcv_data[$row["PROD_ID"]]["amt"]/$rcv_data[$row["PROD_ID"]]["qnty"]),12,'.','');
			}
			$issue_amount=number_format(($row["CONS_QUANTITY"]*$runtime_rate),12,'.','');
			$upTransID=execute_query("update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]." ");
			if($upTransID){ $upTransID=1; } else {echo"update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]."";oci_rollback($con);die;}
			$rcv_data[$row["PROD_ID"]]["qnty"]+=$row["CONS_QUANTITY"];
			$rcv_data[$row["PROD_ID"]]["amt"]+=$issue_amount;
		}
		else if($row["TRANSACTION_TYPE"]==5 && $row["CONS_AMOUNT"]==0)
		{
			if(number_format($rcv_data[$row["PROD_ID"]]["qnty"],12,'.','') > 0 && number_format($rcv_data[$row["PROD_ID"]]["amt"],12,'.','') > 0)
			{
				$runtime_rate=number_format(($rcv_data[$row["PROD_ID"]]["amt"]/$rcv_data[$row["PROD_ID"]]["qnty"]),12,'.','');
			}
			$issue_amount=number_format(($row["CONS_QUANTITY"]*$runtime_rate),12,'.','');
			$upTransID=execute_query("update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]." ");
			if($upTransID){ $upTransID=1; } else {echo"update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]."";oci_rollback($con);die;}
			
			$upIssID=execute_query("update INV_ITEM_TRANSFER_DTLS set RATE='".$runtime_rate."', TRANSFER_VALUE='".$issue_amount."' where TO_TRANS_ID=".$row["TRANS_ID"]." ");
				if($upIssID){ $upIssID=1; } else {echo"update INV_ITEM_TRANSFER_DTLS set RATE='".$runtime_rate."', TRANSFER_VALUE='".$issue_amount."' where TO_TRANS_ID=".$row["TRANS_ID"]."";oci_rollback($con);die;}
			
			$rcv_data[$row["PROD_ID"]]["qnty"]+=$row["CONS_QUANTITY"];
			$rcv_data[$row["PROD_ID"]]["amt"]+=$issue_amount;
		}
		else
		{
			$rcv_data[$row["PROD_ID"]]["qnty"]+=$row["CONS_QUANTITY"];
			$rcv_data[$row["PROD_ID"]]["amt"]+=$row["CONS_AMOUNT"];
		}
		
		$k=0;
	}
	else
	{
		if($k==0)
		{
			if(number_format($rcv_data[$row["PROD_ID"]]["qnty"],12,'.','') > 0 && number_format($rcv_data[$row["PROD_ID"]]["amt"],12,'.','') > 0)
			{
				$runtime_rate=number_format(($rcv_data[$row["PROD_ID"]]["amt"]/$rcv_data[$row["PROD_ID"]]["qnty"]),12,'.','');
			}
		}
		$issue_amount=number_format(($row["CONS_QUANTITY"]*$runtime_rate),12,'.','');
		
		$upTransID=execute_query("update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]." ");
		if($upTransID){ $upTransID=1; } else {echo"update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]."";oci_rollback($con);die;}
		$rcv_data[$row["PROD_ID"]]["qnty"] -= $row["CONS_QUANTITY"];
		$rcv_data[$row["PROD_ID"]]["amt"] -= $issue_amount;
		$k++;
	}
}

/* ##### difine Porduct ID Product Part update  */
$upProdID=true;
foreach($rcv_data as $prod_id=>$prod_val)
{
	$prod_agv_rate=0;
	if(number_format($prod_val["qnty"],12,'.','') > 0 && number_format($prod_val["amt"],12,'.','') > 0) 
	{
		$prod_agv_rate=number_format($prod_val["amt"],12,'.','')/number_format($prod_val["qnty"],12,'.','');
	}
	$upProdID=execute_query("update product_details_master set current_stock='".number_format($prod_val["qnty"],12,'.','')."', stock_value='".number_format($prod_val["amt"],12,'.','')."', avg_rate_per_unit='".number_format($prod_agv_rate,12,'.','')."' where id=$prod_id");
	if(!$upProdID) { echo "update product_details_master set current_stock='".number_format($prod_val["qnty"],12,'.','')."', stock_value='".number_format($prod_val["amt"],12,'.','')."', avg_rate_per_unit='".number_format($prod_agv_rate,12,'.','')."' where id=$prod_id";oci_rollback($con); die;}
}


if($db_type==2)
{
	//$upTransID=execute_query(bulk_update_sql_statement2("inv_transaction","id",$update_field,$update_data,$updateID_array));
	if($upTransID && $upProdID)
	{
		oci_commit($con); 
		echo "Transaction Data Update Successfully. <br>";die;
	}
	else
	{
		oci_rollback($con);
		echo "Transaction Data Update Failed";
		die;
	}
}
?>