<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];
$user_id = $_SESSION['logic_erp']["user_id"];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//cbo_company_id*txt_date_from*txt_date_to
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	//echo $cbo_company_id."*".$txt_date_from."*".$txt_date_to;die;

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");

	//$sql = "select COMPANY_ID, ITEM_CATEGORY, CONS_UOM,
//	sum((case when transaction_type in(1,4,5) and TRANSACTION_DATE < '$txt_date_from' then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) and TRANSACTION_DATE < '$txt_date_from' then cons_quantity else 0 end)) as OPENING_QNTY,
//	sum((case when transaction_type in(1,4,5) and TRANSACTION_DATE < '$txt_date_from' then cons_amount else 0 end)-(case when transaction_type in(2,3,6) and TRANSACTION_DATE < '$txt_date_from' then cons_amount else 0 end)) as OPENING_AMT, 
//	sum((case when transaction_type in(1,4,5) and TRANSACTION_DATE between '$txt_date_from' and '$txt_date_to' then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) and TRANSACTION_DATE between '$txt_date_from' and '$txt_date_to' then cons_quantity else 0 end)) as BAL_QNTY, 
//	sum((case when transaction_type in(1,4,5) and TRANSACTION_DATE between '$txt_date_from' and '$txt_date_to' then cons_amount else 0 end)-(case when transaction_type in(2,3,6) and TRANSACTION_DATE between '$txt_date_from' and '$txt_date_to' then cons_amount else 0 end)) as BAL_AMT 
//	from inv_transaction  
//	where status_active=1 and item_category>0 and CONS_UOM>0 and COMPANY_ID in($cbo_company_id) 
//	group by company_id, item_category, CONS_UOM
//	order by item_category, CONS_UOM";
	
	$sql = "select COMPANY_ID, ITEM_CATEGORY, CONS_UOM, STORE_ID, TRANSACTION_TYPE, TRANSACTION_DATE, CONS_QUANTITY, CONS_AMOUNT
	from inv_transaction  
	where status_active=1 and item_category>0 and CONS_UOM>0 and COMPANY_ID in($cbo_company_id) and TRANSACTION_DATE <= '$txt_date_to'
	order by item_category, CONS_UOM";

	//echo $sql; die;
	$data_result=sql_select($sql);
	

	$dtls_data_arr = array();$com_id_arr = array();
	foreach ($data_result as $val)
	{
		if($val["ITEM_CATEGORY"]==2 || $val["ITEM_CATEGORY"]==3 || $val["ITEM_CATEGORY"]==13 || $val["ITEM_CATEGORY"]==14 || $val["ITEM_CATEGORY"]==5 || $val["ITEM_CATEGORY"]==6 || $val["ITEM_CATEGORY"]==7 || $val["ITEM_CATEGORY"]==23)
		{
			if($val["ITEM_CATEGORY"]==3)
			{
				if($val["STORE_ID"]==60 || $val["STORE_ID"]==61 || $val["STORE_ID"]==58 || $val["STORE_ID"]==66 || $val["STORE_ID"]==62 || $val["STORE_ID"]==42 || $val["STORE_ID"]==63)
				{
					$report_key=$val["ITEM_CATEGORY"]."*".$val["CONS_UOM"]."* Left-Over";
				}
				else
				{
					$report_key=$val["ITEM_CATEGORY"]."*".$val["CONS_UOM"]."*  Running";
				}
			}
			else
			{
				$report_key=$val["ITEM_CATEGORY"]."*".$val["CONS_UOM"];
			}
			
		}
		else
		{
			$report_key=$val["ITEM_CATEGORY"];
		}
				
		if($val["TRANSACTION_TYPE"]==1 || $val["TRANSACTION_TYPE"]==4 || $val["TRANSACTION_TYPE"]==5)
		{
			$dtls_data_arr[$report_key][$val["COMPANY_ID"]]["BAL_QNTY"]+=$val["CONS_QUANTITY"];
			$dtls_data_arr[$report_key][$val["COMPANY_ID"]]["BAL_AMT"]+=$val["CONS_AMOUNT"];
		}
		else
		{
			$dtls_data_arr[$report_key][$val["COMPANY_ID"]]["BAL_QNTY"]-=$val["CONS_QUANTITY"];
			$dtls_data_arr[$report_key][$val["COMPANY_ID"]]["BAL_AMT"]-=$val["CONS_AMOUNT"];
		}
		
		$com_id_arr[$val["COMPANY_ID"]]=$val["COMPANY_ID"];
	}
	//print_r($com_id_arr);
	//echo "<pre>";print_r($dtls_data_arr);die;
	$table_width=450+(count($com_id_arr)*200);
	$tot_row=5+(count($com_id_arr)*2);
	ob_start();
	?>
	<div align="center">
	    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $table_width;?>" class="rpt_table" align="left" id="table_header">
	        <thead>

	        	<tr class="form_caption" style="border:none;">
	        	    <td align="center" style="border:none; font-size:18px;" colspan="<? echo $tot_row; ?>">
	        	        <? echo $report_title; ?>
	        	    </td>
	        	</tr>
	        	<tr>
		            <th width="30" rowspan="2">SL</th>
		            <th width="120" rowspan="2">Head Name</th>
					<th width="100" rowspan="2">Category</th>
		            <th width="60" rowspan="2">UOM</th>
                    <?
					foreach($com_id_arr as $com_id)
					{
						?>
                        <th width="200" colspan="2"><?= $company_library[$com_id];?></th>
                        <?
					}
					?>
                    <th rowspan="2">Total Value(Taka)</th>
	        	</tr>
                <tr>
                	<?
					foreach($com_id_arr as $com_id)
					{
						?>
                        <th width="100">Quantity</th>
                        <th width="100">Value(Taka)</th>
                        <?
					}
					?>
                </tr>
	        </thead>
				<?
				//$item_category_type_arr=array(1 => "Yarn", 2 => "Finish Fabric", 3 => "Woven Finish Fabric", 4 => "Accessories", 5 => "Dyes Chemical and Auxilary Chemical", 8 => "General item", 12 => "Services - Fabric", 13 => "Grey Fabric", 14 => "Woven Grey Fabric", 15 => "Embel Cost", 16 => "Gmt's Wash", 101 => "Raw Material");
		        $i=1;
				$total_allocation_qty = 0;$company_wise_total=array();
		        foreach($dtls_data_arr as $cat_ref=>$cat_val)
		        {
					$cat_ref_val=explode("*",$cat_ref);
					$cat_id=$cat_ref_val[0];
					$uom_id=$cat_ref_val[1];
					if($i%2==0) $bgcolor="#E9F3FF";
					else $bgcolor="#FFFFFF";
					if($cat_id==1 || $cat_id==2 || $cat_id==3 || $cat_id==4 || $cat_id==12 || $cat_id==13 || $cat_id==14 || $cat_id==101)
					{
						$cat_type=$item_category_type_arr[$cat_id];
					}
					else if($cat_id==5 || $cat_id==6 || $cat_id==7 || $cat_id==23)
					{
						$cat_type="Dyes & Chemical";
					}
					else
					{
						$cat_type="General";
					}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
						<td style="word-wrap: break-word;word-break: break-all;" align="center"><? echo $i; ?></td>
						<td style="word-wrap: break-word;word-break: break-all;"><? echo $item_category[$cat_id]." ".$cat_ref_val[2]; ?>&nbsp;</td>
						<td style="word-wrap: break-word;word-break: break-all;"><? echo $cat_type; ?></td>
						<td style="word-wrap: break-word;word-break: break-all;" align="center"><? echo $unit_of_measurement[$uom_id]; ?></td>
						<?
						$row_total_amt=0;
						foreach($com_id_arr as $com_id)
						{
							$opening_qnty=$opening_amt=$date_range_qnty=$date_range_amt=$closing_qnty=$closing_amt=0;
							$opening_qnty=$cat_val[$com_id]["OPENING_QNTY"];
							$opening_amt=$cat_val[$com_id]["OPENING_AMT"];
							$date_range_qnty=$cat_val[$com_id]["BAL_QNTY"];
							$date_range_amt=$cat_val[$com_id]["BAL_AMT"];
							$closing_qnty=$opening_qnty+$date_range_qnty;
							$closing_amt=$opening_amt+$date_range_amt;
							?>
							<td style="word-wrap: break-word;word-break: break-all;" align="right"><? if($uom_id>0) echo number_format($closing_qnty,2); ?></td>
							<td style="word-wrap: break-word;word-break: break-all;" align="right"><? echo number_format($closing_amt,2); ?></td>
							<?
							$row_total_amt+=$closing_amt;
							$company_wise_total[$com_id]+=$closing_amt;
							
						}
						?>
						<td style="word-wrap: break-word;word-break: break-all;" align="right"><? echo number_format($row_total_amt,2); ?></td>
					</tr>
					<?
					$i++;
		        }
		        ?>
            <tfoot>
                <tr>
					<th>&nbsp;</th>
		            <th>&nbsp;</th>
					<th>&nbsp;</th>
		            <th>Total:</th>
                    <?
					$gt_total_value=0;
					foreach($com_id_arr as $com_id)
					{
						?>
                        <th>&nbsp;</th>
                        <th style="word-wrap: break-word;word-break: break-all;"><? echo number_format($company_wise_total[$com_id],2); ?></th>
                        <?
						$gt_total_value+=$company_wise_total[$com_id];
					}
					?>
		            <th style="word-wrap: break-word;word-break: break-all;"><? echo number_format($gt_total_value,2); ?></th>
                </tr>
            </tfoot>
        </table>
	</div>
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
	$filename="".$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}

?>