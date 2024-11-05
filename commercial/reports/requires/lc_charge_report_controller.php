<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == '' ) header('location:login.php');
require_once('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];


if($action=="report_generate")
{

	$process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    $cbo_company_id     = str_replace("'","",$cbo_company_name);
    $cbo_bank_id      	= str_replace("'","",$cbo_bank_name);        
    $txt_lc_num         = trim(str_replace("'","",$txt_lc_no));      
    $txt_start_date     = str_replace("'","",$txt_date_from);    
    $txt_end_date       = str_replace("'","",$txt_date_to);      
    
	if ($cbo_company_id!=0) 
	{
		$company=" and a.importer_id =$cbo_company_id ";
		$lib_company="and a.company_id=$cbo_company_id";
	} else { echo "Please Select Company First."; die;}

	if($txt_lc_num!='') {$lc_no="and a.lc_number ='$txt_lc_num'";}

	$bank_id="";$date_cond ="";
	if ($cbo_bank_id != 0)
	{
        $bank_id="and a.issuing_bank_id =$cbo_bank_id ";
        $lib_bank_id="and a.bank_id =$cbo_bank_id";
	}
	else
	{
		$bank_sql= sql_select("SELECT a.issuing_bank_id as BANK_ID from com_btb_lc_master_details a where a.status_active=1 and a.is_deleted=0 $company $lc_no ");
        $lib_bank_id="and a.bank_id =".$bank_sql[0]['BANK_ID'];
	}

	if ($txt_start_date != '' && $txt_end_date != '') 
	{
		if ($db_type == 0) {
            $date_cond = "and b.pay_date between '" . change_date_format($txt_start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_end_date, 'yyyy-mm-dd') . "'";
        } else if ($db_type == 2) {
            $date_cond = "and b.pay_date between '" . change_date_format($txt_start_date, '', '', 1) . "' and '" . change_date_format($txt_end_date, '', '', 1) . "'";
		}
    } 
    else 
    { 
		$date_cond = '';
	}

	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');

	$lib_sql= sql_select("select a.company_id,a.bank_id,a.charge_for_id, b.pay_head_id,b.amount from LIB_LC_CHARGE_MST a, lib_lc_charge_dtls b where a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $lib_company $lib_bank_id");

	$info_charge_lib=array();
	foreach($lib_sql as $row){
		// $info_charge_lib[$row['COMPANY_ID']][$row['BANK_ID']][$row['CHARGE_FOR_ID']][$row['PAY_HEAD_ID']]['actual_charge']=$row['AMOUNT'];
		$info_charge_lib[$row['CHARGE_FOR_ID']][$row['PAY_HEAD_ID']]['sanction_charge']=$row['AMOUNT'];
	}
	$data_sql= "SELECT a.id, a.importer_id,a.supplier_id,a.item_category_id,a.lc_type_id, a.lc_number, a.lc_value, a.issuing_bank_id from com_btb_lc_master_details a ,com_lc_charge b where  a.id=b.btb_lc_id $company $bank_id $date_cond $lc_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.importer_id,a.supplier_id,a.item_category_id,a.lc_type_id, a.lc_number, a.lc_value, a.issuing_bank_id ";
	// echo $data_sql;die;
	$data_result= sql_select($data_sql);
	$btb_mst_id='';
	foreach($data_result as $row){
		if($btb_mst_id!=''){$btb_mst_id.= ",".$row['ID'];}else{$btb_mst_id = $row['ID'];}
	}
	$data_sql_dtls= sql_select("select btb_lc_id,change_for, pay_head_id, amount from com_lc_charge where btb_lc_id in ($btb_mst_id) and status_active=1 and is_deleted=0");
	$info_charge=array();$charge_for_type=array();
	foreach($data_sql_dtls as $row){
		$info_charge[$row['BTB_LC_ID']][$row['CHANGE_FOR']][$row['PAY_HEAD_ID']]['actual_charge']=$row['AMOUNT'];
		$charge_for_type[$row['CHANGE_FOR']] .=$row['PAY_HEAD_ID'].",";
	}

	$lc_charge_for_opening=array_filter(array_unique(explode(',',$charge_for_type[1])));
	$lc_charge_for_amandmend=array_filter(array_unique(explode(',',$charge_for_type[2])));
	$lc_charge_for_acceptance=array_filter(array_unique(explode(',',$charge_for_type[3])));
	// $charge_for_opening = array();$charge_for_amandmend = array();$charge_for_acceptance = array();
	foreach($lc_charge_for_opening as $rows){
	$charge_for_opening[] .= $rows;
	}
	foreach($lc_charge_for_amandmend as $rows){
	$charge_for_amandmend[] .= $rows;
	}
	foreach($lc_charge_for_acceptance as $rows){
	$charge_for_acceptance[] .= $rows;
	}

	$count_opening=count($charge_for_opening);
	$count_amandmend=count($charge_for_amandmend);
	$count_acceptance=count($charge_for_acceptance);
	$width_table= 700+(($count_opening+$count_amandmend+$count_acceptanc)*70);
	$div_span= 9+($count_opening+$count_amandmend+$count_acceptanc);
	if($div_span<11){$div_span=11;}
	if($width_table<910){$width_table=910;}
	ob_start();
    ?>
        <table class="rpt_table" border="1" rules="all" width="<?=$width_table;?>" cellpadding="0" cellspacing="0" id="table_body">
        <thead>
			<tr>
				<th colspan="<?= $div_span;?>" width=""><p style="font-weight:bold; font-size:20px">Report Details</p></th>
			</tr>				
				<tr>
					<th width="30" rowspan="2">SL</th>
					<th width="70" rowspan="2">LC No</th>
					<th width="70" rowspan="2">Supplier</th>
					<th width="70" rowspan="2">Bank</th>
                    <th width="70" rowspan="2">Item Catg.</th>
                    <th width="70" rowspan="2">LC Type</th>
                    <th width="70" rowspan="2">LC Value</th>
                    <th width="50"  >Particular</th>
                    <th colspan="<? echo $count_opening; ?>">LC Opening</th>
                    <th colspan="<? echo $count_amandmend; ?>">LC Amandmend</th>
                    <th colspan="<? echo $count_acceptance; ?>">LC Acceptance</th>
				</tr>
				<tr>
					<th width="70">Pay Head</th>
					<?
						if($count_opening>0)
						{	
							for($m=0; $m<$count_opening; $m++)
							{
								?>
									<th align="center" width="70"><p><? echo $commercial_head[$charge_for_opening[$m]]; ?></p></th>
								<?
							}
						}
						else
						{
							?>
								<th width="70"><p></p></th>
							<?
						}
						if($count_amandmend>0)
						{
							for($m=0; $m<$count_amandmend; $m++)
							{
								?>
								<th align="center" width="70"><p><? echo $commercial_head[$charge_for_amandmend[$m]]; ?></p></th>
								<?
							}
						}
						else
						{
							?>
								<th width="70"><p></p></th>
							<?
						}
						if($count_acceptance>0)
						{
							for($m=0; $m<$count_acceptance; $m++)
							{
								?>
								<th align="center" width="70"><p><? echo $commercial_head[$charge_for_acceptance[$m]]; ?></p></th>
								<?
							}
						}
						else
						{
							?>
								<th width="70"><p></p></th>
							<?
						}
						?>
				</tr>
			</thead>
	
		        <tbody rules="all">
                <?
                    $i=1;
                    $tot_invoice_value=0;
                    $tot_invoice_value_bdt=0;
                    $tot_amount=0;
		        	foreach ($data_result as $row)
		        	{
		        		if ($i%2==0) $bgcolor="#E9F3FF";
						else $bgcolor="#FFFFFF";
		        		?>
                        <tr bgcolor="<?= $bgcolor; ?>">
			        		<td align="center" rowspan="3"><p><?= $i; ?></p></td>
			        		<td align="center" rowspan="3"><p><?= $row[csf('lc_number')]; ?></p></td>
                            <td align="center" rowspan="3"><p><?= $supplier_arr[$row[csf('supplier_id')]];?></p></td>
			        		<td align="center" rowspan="3"><p><?= $bank_arr[$row[csf('issuing_bank_id')]]; ?></p></td>
			        		<td align="center" rowspan="3"><p><?= $item_category[$row[csf('item_category_id')]]; ?></p></td>
			        		<td align="center" rowspan="3"><p><?= $lc_type[$row[csf('lc_type_id')]]; ?></p></td>
			        		<td align="center" rowspan="3"><p><?= number_format($row[csf('lc_value')],2,'.',''); ?></p></td>
			        		<td align="center"><p>Sanction</p></td>
							<?
								if($count_opening>0)
								{
									for($m=0; $m<$count_opening; $m++)
									{
										?>
										<td align="right"><p><? echo number_format($info_charge_lib[1][$charge_for_opening[$m]]['sanction_charge'],0,'.',''); ?></p></td>
										<?
									}
								}
								else
								{
									?>
										<td align="right"><p></p></td>
									<?
								}
								if($count_amandmend>0)
								{
									for($m=0; $m<$count_amandmend; $m++)
									{
										?>
										<td align="right"><p><? echo number_format($info_charge_lib[2][$charge_for_amandmend[$m]]['sanction_charge'],0,'.',''); ?></p></td>
										<?
									}
								}
								else
								{
									?>
										<td align="right"><p></p></td>
									<?
								}
								if($count_acceptance>0)
								{
									for($m=0; $m<$count_acceptance; $m++)
									{
										?>
										<td align="right"><p><? echo number_format($info_charge_lib[3][$charge_for_acceptance[$m]]['sanction_charge'],0,'.',''); ?></p></td>
										<?
									}
								}
								else
								{
									?>
										<td align="right"><p></p></td>
									<?
								}
							?>
			        	</tr>
						<tr>
							<td align="right"><p>Actual Charge</p></td>
							<?
								if($count_opening>0)
								{
									for($m=0; $m<$count_opening; $m++)
									{
										?>
										<td align="right"><p><? echo number_format($info_charge[$row['ID']][1][$charge_for_opening[$m]]['actual_charge'],0,'.',''); ?></p></td>
										<?
									}
								}
								else
								{
									?>
										<td align="right"><p></p></td>
									<?
								}
								if($count_amandmend>0)
								{
									for($m=0; $m<$count_amandmend; $m++)
									{
										?>
										<td align="right"><p><? echo number_format($info_charge[$row['ID']][2][$charge_for_amandmend[$m]]['actual_charge'],0,'.',''); ?></p></td>
										<?
									}
								}
								else
								{
									?>
										<td align="right"><p></p></td>
									<?
								}
								if($count_acceptance>0)
								{
									for($m=0; $m<$count_acceptance; $m++)
									{
										?>
										<td align="right"><p><? echo number_format($info_charge[$row['ID']][3][$charge_for_acceptance[$m]]['actual_charge'],0,'.',''); ?></p></td>
										<?
									}
								}
								else
								{
									?>
										<td align="right"><p></p></td>
									<?
								}
							?>
						</tr>
						<tr>
							<td align="center"><p>Deff</p></td>
							<?
								if($count_opening>0)
								{
									for($m=0; $m<$count_opening; $m++)
									{
										$opening_lib=$info_charge_lib[1][$charge_for_opening[$m]]['sanction_charge'];
										$opening_actual=$info_charge[$row['ID']][1][$charge_for_opening[$m]]['actual_charge'];
										$opening_balance= $opening_lib-$opening_actual;
									?>
									<td align="right"><p><? echo $opening_balance; ?></p></td>
									<?
									}
								}
								else
								{
									?>
										<td align="right"><p></p></td>
									<?
								}
								if($count_amandmend>0)
								{
									for($m=0; $m<$count_amandmend; $m++)
									{
										?>
										<td align="right"><p><? echo $info_charge_lib[2][$charge_for_amandmend[$m]]['sanction_charge']-$info_charge[$row['ID']][2][$charge_for_amandmend[$m]]['actual_charge']; ?></p></td>
										<?
									}
								}
								else
								{
									?>
										<td align="right"><p></p></td>
									<?
								}
								if($count_acceptance>0)
								{
									for($m=0; $m<$count_acceptance; $m++)
									{
										?>
										<td align="right"><p><? echo number_format(($info_charge_lib[3][$charge_for_acceptance[$m]]['sanction_charge']-$info_charge[$row['ID']][3][$charge_for_acceptance[$m]]['actual_charge']),0,'.',''); ?></p></td>
										<?
									}
								}
								else
								{
									?>
										<td align="right"><p></p></td>
									<?
								}
							?>
						</tr>
                            <?
                            $i++;

                    }
                            ?>
                </tbody>
                </table>
    <?
	foreach (glob("$user_id*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename";
	exit();
} 