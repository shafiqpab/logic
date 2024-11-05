<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];


if ($action=="load_buyer_dropdown")
{
	echo create_drop_down( "cbo_buyer_id",150,"select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.TAG_COMPANY=$data order by buy.buyer_name","id,buyer_name",1,'-Select',0,"",0);  
 	exit();
}

if ($action=="load_dealing_merchant_dropdown")
{
	echo create_drop_down( "cbo_dealing_merchant", 100, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	exit();	
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_name=str_replace("'","",$cbo_company_id);
	$buyer_id=str_replace("'","",$cbo_buyer_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$reportType=str_replace("'","",$reportType);
	
	$cbo_currier_name=str_replace("'","",$cbo_currier_name);
	$cbo_team_leader=str_replace("'","",$cbo_team_leader);
	$cbo_dealing_merchant=str_replace("'","",$cbo_dealing_merchant);
	$cbo_country_id=str_replace("'","",$cbo_country_id);
	$cbo_style_status=str_replace("'","",$cbo_style_status);
	$txt_air_way_bill=str_replace("'","",$txt_air_way_bill);
	
	$sqlCond = "";
	$sqlCond .= ($company_name!=0) ? " and company_id=$company_name" : "";
	$sqlCond .= ($buyer_id!=0) ? " and buyer_id=$buyer_id" : "";
	$sqlCond .= ($cbo_style_status!=0) ? " and  style_status=$cbo_style_status" : "";
	$sqlCond .= ($cbo_country_id!=0) ? " and  country_id=$cbo_country_id" : "";
	$sqlCond .= ($cbo_currier_name!=0) ? " and  CURRIER_NAME=$cbo_currier_name" : "";
	$sqlCond .= ($cbo_team_leader!=0) ? " and  TEAM_LEADER=$cbo_team_leader" : "";
	$sqlCond .= ($cbo_dealing_merchant!=0) ? " and  DEALING_MERCHANT=$cbo_dealing_merchant" : "";
	$sqlCond .= ($txt_air_way_bill!='') ? " and  AIR_WAY_BILL='$txt_air_way_bill'" : "";

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
        
        $sqlCond .= " and bill_date between '$start_date' and '$end_date'";       
    }

	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$currier_arr = array(1=>"DHL",2=>"TNT",3=>"FedEx Express");
	$user_library = return_library_array( "select id,user_name from user_passwd where status_active =1 and is_deleted=0", "id", "user_name"  );
	
	$dealing_merchant_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info where    status_active =1 and is_deleted=0", "id", "team_member_name"  );
	
	$team_leader_library=return_library_array( "select id,team_leader_name from lib_marketing_team where project_type=6 and status_active =1 and is_deleted=0 order by team_leader_name", "id", "team_leader_name"  );
	$country_library=return_library_array( "select ID,COUNTRY_NAME FROM LIB_COUNTRY WHERE STATUS_ACTIVE=1 AND IS_DELETED=0", "id", "COUNTRY_NAME"  );
	
	$sql= "SELECT BILL_PREFIX, BILL_PREFIX_NUMBER, BILL_SYSTEM_ID, COMPANY_ID, BUYER_ID, CURRIER_NAME, AIR_WAY_BILL, COUNTRY_ID, TEAM_LEADER, DEALING_MERCHANT, STYLE_STATUS, STYLE_NAME, STYLE_QTY, BILL_DATE, WEIGHT, CHARGE_USD, DFS_CHARGE_USD, TOTAL_CHARGE_USD, CHARGE_BDT, RATE, INSERTED_BY, INSERT_DATE from air_way_bill_entry_mst where status_active = 1 AND is_deleted=0 $sqlCond";
	//echo $sql;
	$dataArray=sql_select( $sql );
	// echo "<pre>"; print_r($dataArray);
	
	if($reportType==1)
	{
 		$width=1340;
		ob_start();
		?>
     	<div class="main_container" style="width:<?=$width+20; ?>px;">
     			<table width="<?=$width;?>" cellspacing="0" align="center">
                    <tr>
                        <td align="center" colspan="17" class="form_caption">
                            <strong style="font-size:16px;">Company Name: <? echo $company_library[$company_name]; ?></strong>
                        </td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="17" align="center" class="form_caption"> <strong style="font-size:15px;">Air Way Bill Report</strong></td>
                    </tr>
                </table>
     			<table class="rpt_table" rules="all" width="<?=$width;?>" align="left" border="1">
     				<thead>
     					<tr>
     						<th width="30">Sl</th>
                            <th width="100">Company</th>	
                            <th width="60">Date</th>	
                            <th width="60">Courier Company</th>	
                            <th width="80">Team leader</th>	
                            <th width="80">Deling Merchant</th>	
                            <th width="80">Style Status</th>	
                            <th width="80">Buyer</th>	
                            <th width="80">Style</th>	
                            <th width="60">Style Qty. [PCS]</th>	
                            <th width="80">Bill No</th>	
                            <th width="80">Destination</th>	
                            <th width="60">Weight [Kg]</th>	
                            <th width="50">Charge [$]</th>
                            <th width="60">DFS Charge [$]</th>	
                            <th width="60">Total Charge [$]</th>	
                            <th width="40">Ex. Rate</th>
                            <th width="50">Amount BDT</th>
                            <th width="50">Insert By</th>
                            <th>Insert Date</th>
     					</tr>
     				</thead>
     			</table>
     		<div class="body_part" style="width:<?=$width+20; ?>px;max-height:300px;overflow:auto" align="left" id="scroll_body">
     			<table class="rpt_table" rules="all" width="<?=$width; ?>" id="table_body" border="1">
     				<tbody>
     					<?
     					$i=1;
						$stausArr=array(1=>"Before Order",2=>"After Order");
     					foreach ($dataArray as $rows) 
     					{
							$bgcolor=($i%2==0)? "#E9F3FF":"#FFFFFF";
							?>
							<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>">
								<td width="30" align="center"><?=$i; ?></td>
								<td width="100" style="word-break:break-all"><?=$company_library[$rows[COMPANY_ID]]; ?></td>
								<td width="60" ><?=change_date_format($rows[BILL_DATE]); ?></td>
								<td width="60" ><?=$currier_arr[$rows[CURRIER_NAME]]; ?></td>
								<td width="80" style="word-break:break-all"><?=$team_leader_library[$rows[TEAM_LEADER]]; ?></td>
								<td width="80" style="word-break:break-all"><?=$dealing_merchant_library[$rows[DEALING_MERCHANT]]; ?></td>
								<td width="80" style="word-break:break-all"><?=$stausArr[$rows[STYLE_STATUS]]; ?></td>
								<td width="80" style="word-break:break-all"><?=$buyer_arr[$rows[BUYER_ID]]; ?></td>
								<td width="80" style="word-break:break-all"><?=$rows[STYLE_NAME]; ?></td>
								<td width="60" style="word-break:break-all" align="right"><?=$rows[STYLE_QTY]; ?></td>
								<td width="80" style="word-break:break-all"><?=$rows[AIR_WAY_BILL]; ?></td>
								<td width="80" style="word-break:break-all"><?=$country_library[$rows[COUNTRY_ID]]; ?></td>
								<td width="60" align="right"><?=$rows[WEIGHT]; ?></td>
								<td width="50" align="right"><?=$rows[CHARGE_USD]; ?></td>
								<td width="60" align="right"><?=$rows[DFS_CHARGE_USD]; ?></td>
								<td width="60" align="right"><?=$rows[TOTAL_CHARGE_USD]; ?></td>
								<td width="40" align="right"><?=$rows[RATE]; ?></td>
								<td width="50" align="right"><?=number_format($rows[TOTAL_CHARGE_USD]*$rows[RATE],2); ?></td>
								<td width="50" ><?=$user_library[$rows[INSERTED_BY]]; ?></td>
								<td ><?=change_date_format($rows[INSERT_DATE]); ?></td>
							</tr>
							<?
							$totalWeight+=$rows[WEIGHT];
							$totalChargeUsd+=$rows[CHARGE_USD];
							$totalDFSChargeUsd+=$rows[DFS_CHARGE_USD];
							$totalTotalChargeUsd+=$rows[TOTAL_CHARGE_USD];
							$totalAmount+=($rows[RATE]*$rows[TOTAL_CHARGE_USD]);
							$totalExRate += $rows[RATE];
							$totalAmountBdt += ($rows[RATE]*$rows[TOTAL_CHARGE_USD]);
							$i++;
		     			}
     					?>
     				</tbody>
     			</table>
     		</div>
            <table class="rpt_table" rules="all" width="<?=$width;?>" align="left" border="1">
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="60" align="right" id="totalChargeUsd"><?=$totalChargeUsd;?></th>
                    <th width="50" align="right" id="totalDFSChargeUsd"><?=$totalDFSChargeUsd;?></th>
                    <th width="60" align="right" id="totalTotalChargeUsd"><? $totalTotalChargeUsd;?></th>
					<th width="60" align="right" id="totalTotalChargeUsd"><?=$totalTotalChargeUsd;?></th>
					<th width="40" align="right" id="totalExRate"><?=$totalExRate;?></th>
                    <th width="50" align="right" id="totalAmountBdt"><?=$totalAmountBdt;?></th>
                    <th width="50">&nbsp;</th>
                    <th >&nbsp;</th>
                </tfoot>
            </table>
     	</div>
		<?
	}
	$html=ob_get_contents();
	ob_clean();
	foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	echo $html.'####'.$filename;
	exit();
}
disconnect($con);
?>
