<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

if($db_type==2) $select_date=" to_char(a.insert_date,'YYYY')";
else if ($db_type==0) $select_date=" year(a.insert_date)";

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond  group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0); 
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));


	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_supplier=str_replace("'","",$cbo_supplier);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_style_no=str_replace("'","",$txt_style_no);
	$txt_wo_no=str_replace("'","",$txt_wo_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);

	$location_nameArray=sql_select( "select id, location_name from lib_location where company_id=$cbo_company_name and status_active=1 order by id desc");
	foreach( $location_nameArray as $row)
	{
	   $location_name=$row[csf('location_name')]; 
	}
	$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$suplierArr = return_library_array("select id,supplier_name from lib_supplier ","id","supplier_name");
	
	$dateCond="";
    $caption="Delivery Date : ";
    if($txt_date_from!="" && $txt_date_to!="") $dateCond=" and a.wo_date between '$txt_date_from' and '$txt_date_to'";

	ob_start();

	if($cbo_company_name!=0) $company_name_cond="and a.company_id in ($cbo_company_name) ";else $company_name_cond="";
	if($cbo_buyer_name!=0) $buyer_name_cond="and d.buyer_name in ($cbo_buyer_name) ";else $buyer_name_cond="";
	if($txt_job_no!='') $job_no_cond="and b.job_no like '%$txt_job_no' ";else $job_no_cond="";
	if($txt_style_no!='') $style_no_cond="and d.style_ref_no like  '%$txt_style_no%' ";else $style_no_cond="";
	if($txt_wo_no!='') $wo_no_cond="and a.labtest_no like  '%$txt_wo_no' ";else $wo_no_cond="";
	if($cbo_supplier!=0) $supplier_cond="and a.supplier_id in ('$cbo_supplier') ";else $supplier_cond="";

	//echo $cbo_company_name."*".$cbo_buyer_name."*".$txt_job_no."*".$txt_style_no."*".$txt_wo_no."*".$cbo_supplier."*".$txt_date_from."*".$txt_date_to;


	$sql= "SELECT b.id as ID,b.po_id ,b.test_for,b.test_item_id, b.remarks,a.labtest_no,a.currency, a.company_id, a.supplier_id, a.wo_date, a.pay_mode,
	b.JOB_NO,b.QTY_BREAKDOWN,c.po_number,d.style_ref_no,d.job_quantity,e.rate
	FROM wo_labtest_mst a,wo_labtest_dtls b ,wo_po_break_down c, wo_po_details_master d,wo_pre_cost_lab_test_dtls e
	where a.id=b.mst_id and c.id=b.po_id and d.job_no = c.job_no_mst and d.id = e.job_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 
	and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0 $company_name_cond $buyer_name_cond $job_no_cond $style_no_cond $wo_no_cond  $supplier_cond $dateCond
	order by b.id";
	//echo $sql;
	$data_array=sql_select($sql);
	$dtls_data_arr=array();
	foreach($data_array as $result){
		$test_id = $result['QTY_BREAKDOWN'].',';
		$test_id_arr=explode(",",$result['QTY_BREAKDOWN']);
		$wo_date = $result['WO_DATE']; 
		$supplier_id.= $result['SUPPLIER_ID'].',';
		foreach($test_id_arr as $test_datas)
		{
			$test_datas_arr=explode("_",$test_datas);
			$dtls_data_arr[$result['ID']][$test_datas_arr[0]]['test_id']=$test_datas_arr[0];
			$dtls_data_arr[$result['ID']][$test_datas_arr[0]]['wo_qnty']=$test_datas_arr[1];
			$dtls_data_arr[$result['ID']][$test_datas_arr[0]]['wo_amt']=$test_datas_arr[2];
		}
	}
	// echo "<pre>";
	// print_r($dtls_data_arr);
	//echo $supplier_id;
	$tes_company=implode(",",array_unique(explode(",",chop($supplier_id,',')))); 
	if($db_type==2) { $txt_workorder_date=change_date_format($wo_date,'yyyy-mm-dd',"-",1);}
	else { $txt_workorder_date=change_date_format($wo_date,'yyyy-mm-dd');}
	$test_sql =" SELECT id,test_category,testing_company,test_for,test_item,rate,upcharge_parcengate,upcharge_amount,net_rate, currency_id,testing_company 
	FROM lib_lab_test_rate_chart WHERE status_active =1 AND is_deleted =0 and testing_company in ($tes_company) ";
	//echo $test_sql;
	$test_data_array=sql_select($test_sql);
	$test_data_arr=array();
	foreach($test_data_array as $row){
		//echo "dwd". $currency;
		$converted_currency=set_conversion_rate($row[csf('currency_id')],$txt_workorder_date);
		$current_currency=set_conversion_rate($row[csf('currency_id')],$txt_workorder_date);
		$actual_currency=$converted_currency/$current_currency;
	 	$actual_net_rate=$actual_currency*$row[csf('net_rate')];
		$test_data_arr[$row['ID']][$row['TEST_FOR']]['TEST_ITEM']=$row['TEST_ITEM'];
		$test_data_arr_usd[$row['ID']][$row['TEST_FOR']]['USD']=$actual_net_rate;
	}
	
	$pi_sql = "SELECT b.pi_id,b.work_order_no ,a.pi_number,d.lc_number FROM COM_PI_ITEM_DETAILS b, COM_PI_MASTER_DETAILS a,COM_BTB_LC_PI c,COM_BTB_LC_MASTER_DETAILS d WHERE b.pi_id = a.id AND a.id =c.pi_id AND d.id = c.COM_BTB_LC_MASTER_DETAILS_ID AND a.status_active =1 AND a.is_deleted =0 and b.status_active =1 AND b.is_deleted =0 AND a.importer_id = '$cbo_company_name' ";
    //echo $pi_sql;
	$pi_data_array=sql_select($pi_sql);
	$pi_data_arr=array();
	foreach($pi_data_array as $row){
		$pi_data_arr[$row['WORK_ORDER_NO']]['PI_NUMBER'] = $row['PI_NUMBER'];
		$pi_data_arr[$row['WORK_ORDER_NO']]['LC_NUMBER'] = $row['LC_NUMBER'];
	}
	unset($pi_sql);
	unset($pi_data_array);

	ob_start();
    ?>
    <div style="width:1590px; margin-top: 10px;" align='center'>
		<table width="1560px" cellspacing="0">
			
			<tr style="border:none;">
				<td colspan="9" align="center" style="border:none; font-size:16px; font-weight:bold"><? echo "Company : ".$companyArr[$cbo_company_name]; ?></td>
			</tr>
			<tr style="border:none;"> 
				<td colspan="9" align="center" style="border:none;font-size:12px; font-weight:bold">
				<? echo "Location : ".$location_name; ?>
				</td>
			</tr>
		</table>
        <table width="1560" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th width="30"><p> SL. <br> NO.</p></th>
                    <th width="80"><p>WO DATE</p></th>
                    <th width="120"><p>WO NUMBER</p></th>
                    <th width="50"><p>WO <br> TYPE</p></th>
                    <th width="100"><p>JOB NUMBER</p></th>
                    <th width="120"><p>STYLE NAME</p></th>
                    <th width="100"><p>PO NO.</p></th>
                    <th width="150"><p>TEST PROCESS</p></th>
                    <th width="100"><p>BUDGET <br> AMOUNT USD</p></th>
                    <th width="50"><p>WO  QTY</p></th>
                    <th width="80"><p>USD/BDT <br> RATE</p></th>
                    <th width="100"><p>USD AMOUNT</p></th>
                    <th width="100"><p>BDT AMOUNT</p></th>
                    <th width="80"><p>SUPPLIER</p></th>
                    <th width="100"><p>PI</p></th>
                    <th width="100"><p>BTB</p></th>
                    <th width="100"><p>REMARKS</p></th>

                </tr>
            </thead>
            </table>
            <div style="width:1560px; overflow-y: scroll; max-height:400px; overflow-x:hidden;" id="scroll_body">
            <table cellspacing="0" width="1560"  border="1" rules="all" class="rpt_table" id="tbl_body">
            <tbody id="table_body" >
                <?
                    $i=1;
                    foreach($data_array as $result)
                    {
						$test_id = $result['QTY_BREAKDOWN'].',';
						$test_id_arr=explode(",",$result['QTY_BREAKDOWN']);
						foreach($test_id_arr as $test_datas)
						{
							$test_datas_arr=explode("_",$test_datas);
							if ($i%2==0) $bgcolor="#E9F3FF";
							else $bgcolor="#FFFFFF";
							?>
								<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
									<td width="30" align="center"><p><?=$i;?></p></td>
									<td width="80" align="center"><p><? echo change_date_format($result['WO_DATE']); ?></p></td>
									<td width="120" align="center"><p><? echo $result['LABTEST_NO']; ?></p></td>
									<td width="50" align="center"><p><? echo  $pay_mode[$result['PAY_MODE']]; ?></p></td>
									<td width="100" align="center"><p><? echo $result['JOB_NO']; ?></p></td>
									<td width="120" align="center"><p><? echo $result['STYLE_REF_NO']; ?></p></td>
									<td width="100" align="center"><p><? echo $result['PO_NUMBER']; ?></p></td>
									<td width="150" align="center"><p><? 
									echo $test_data_arr[$dtls_data_arr[$result['ID']][$test_datas_arr[0]]['test_id']][$result['TEST_FOR']]['TEST_ITEM'];?></p></td>
									<td width="100" align="right"><p> <? echo number_format($result['JOB_QUANTITY']*$result['RATE'],2); ?></p></td>
									<td width="50" align="center"> <p><? echo $wo_qnty =  $dtls_data_arr[$result['ID']][$test_datas_arr[0]]['wo_qnty']; 
									$total_wo_qnty += $wo_qnty;
									?> </p></td>  

									<td width="80" align="center"><p><? echo $usd_bdt_rate =  $test_data_arr_usd[$dtls_data_arr[$result['ID']][$test_datas_arr[0]]['test_id']][$result['TEST_FOR']]['USD'];
									?></p></td>

									<td width="100" align="right"><p><?
									if($result['CURRENCY']==2){echo number_format($usd_bdt_rate*$wo_qnty,2);
									$usd_total += $usd_bdt_rate*$wo_qnty;
									}?></p></td>

									<td width="100" align="right"><p><?
									if($result['CURRENCY']==1){
									echo number_format($usd_bdt_rate*$wo_qnty,2);
									$bdt_total += $usd_bdt_rate*$wo_qnty;}?></p></td>

									<td width="80" align="center"><p><?echo $suplierArr[$result['SUPPLIER_ID']];?></p></td>
									<td width="100" align="center"><p><?
									echo $pi_data_arr[$result['LABTEST_NO']]['PI_NUMBER'];?></p></td>
									<td width="100" align="center"><p><?echo $pi_data_arr[$result['LABTEST_NO']]['LC_NUMBER'];?></p></td>
									<td width="100" align="center"><p><?echo $result['REMARKS'];?></p></td>

								</tr>
							<?
							$i++;
						}
                    }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="9"><strong>Total : </strong></th>
                    <th width="50"><p><?echo $total_wo_qnty;?></p></th>
                    <th width="80"></th>
                    <th width="100"><p><?echo number_format($usd_total,2);?></p></th>
                    <th width="100"><p><?echo number_format($bdt_total,2);?></th></p>
                    <th width="80"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>  
                </tr>
            </tfoot>
        </table>
        </div>
    <?
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    $name=time();
    $filename=$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc,ob_get_contents());
    echo "$html****$filename";
    exit();	
}
