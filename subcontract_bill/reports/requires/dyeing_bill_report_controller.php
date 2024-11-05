<? 
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']['user_id'];
if ($_SESSION['logic_erp']['user_id'] == "")
{
    header("location:login.php");
    die;
}
$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
//extract($_REQUEST);
//$date=date('Y-m-d');

$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
$imge_arr=return_library_array( "select id,master_tble_id,image_location from common_photo_library",'id','image_location');
$party_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');

if ($action=="load_drop_down_buyer")
{ 
	echo create_drop_down( "cbo_buyer_id", 145, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();   	 
} 
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "","","","","","",3 );
	exit();	 
}
if($action=="report_generate_batch_wise")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$txt_batch_no=str_replace("'","",$txt_batch_no);
	$txt_bill_no=str_replace("'","",$txt_bill_no);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$cbo_location_id=str_replace("'","",$cbo_location_id);
	$cbo_process_id=str_replace("'","",$cbo_process_id);
	$cbo_body_part_search=str_replace("'","",$cbo_body_part_search);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	//$txt_bill_no."<BR><BR>";
	
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$body_part_library=return_library_array( "select id,BODY_PART_FULL_NAME from LIB_BODY_PART", "id", "BODY_PART_FULL_NAME"  );
	$fab_des_library=return_library_array( "select id,CONSTRUCTION from LIB_YARN_COUNT_DETERMINA_MST", "id", "CONSTRUCTION"  );
	
	if ($cbo_process_id==0 || $cbo_process_id=='') $cbo_process_cond =""; else $cbo_process_cond =" and A.PROCESS_ID=$cbo_process_id ";
	//------------------------------
	if ($cbo_body_part_search==1) $cbo_body_part = "not in (2,3)";
	if ($cbo_body_part_search==2) $cbo_body_part = "in (2,3)";
	if ($cbo_body_part_search==3) $cbo_body_part = "in (46)";
	if ($cbo_body_part_search==0 || $cbo_body_part_search=='') $cbo_body_part_search_cond =""; else $cbo_body_part_search_cond =" and B.BODY_PART_ID $cbo_body_part ";
	//--------------------------------
	$bill = $txt_bill_no;
	$batch_no = $txt_batch_no;
	if ($bill=="") $txt_bill_cond=""; else $txt_bill_cond =" and A.BILL_NO like '%$bill%'";
	if ($batch_no=="") $txt_batch_no_cond=""; else $txt_batch_no_cond =" and T.BATCH_NO like '%$batch_no%'";
	
	if ($cbo_buyer_id==0  || $cbo_buyer_id=='') $buyer_name_cond =""; else $buyer_name_cond =" and p.buyer_id=$cbo_buyer_id ";
	if ($cbo_location_id==0 || $cbo_location_id=='') $location_id =""; else $location_id =" and A.LOCATION_ID =$cbo_location_id ";
	if ($cbo_company_id==0 || $cbo_company_id=='') $cbo_company_cond =""; else $cbo_company_cond =" and A.COMPANY_ID=$cbo_company_id ";
	if ($cbo_search_by==0 || $cbo_search_by=='') $cbo_search_by_cond =""; else $cbo_search_by_cond =" and A.BILL_FOR=$cbo_search_by ";
	
	//echo $txt_bill_cond."<BR><BR>";//die;
	if($db_type==0)
	{
		if( $date_from!="" && $date_to!="" )
		{
			$prod_date_cond .= " and A.BILL_DATE between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
		}
	}
	else if($db_type==2)
	{
		if($date_from!="" && $date_to!="") 
		{
			$prod_date_cond .= " and A.BILL_DATE between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
		}
	}
	ob_start();	
	$Self_sqls_1="SELECT A.ID,  A.BILL_NO,  A.COMPANY_ID,  A.LOCATION_ID,  A.BILL_DATE,  A.PARTY_ID,  A.BILL_FOR,  A.PROCESS_ID,  B.DELIVERY_QTY,  B.RATE,  B.AMOUNT,  B.REMARKS,  B.CURRENCY_ID,  B.DELIVERY_QTYPCS, 
B.COLLER_CUFF_MEASUREMENT,  B.DELIVERY_ID,  B.FEBRIC_DESCRIPTION_ID,  B.BODY_PART_ID,  B.SHADE_PERCENTAGE,  B.COLOR_RANGE_ID,  B.ADD_PROCESS_NAME,  B.BATCH_ID,  T.BATCH_NO
FROM SUBCON_INBOUND_BILL_MST A INNER JOIN SUBCON_INBOUND_BILL_DTLS B ON A.ID = B.MST_ID INNER JOIN PRO_BATCH_CREATE_MST T ON B.BATCH_ID       = T.ID
WHERE B.STATUS_ACTIVE = 1
AND A.STATUS_ACTIVE = 1 $cbo_company_cond $location_id $buyer_name_cond $cbo_process_cond $prod_date_cond $txt_bill_cond $cbo_search_by_cond $cbo_body_part_search_cond $txt_batch_no_cond";
	
	//echo $Self_sqls_1;die;
		$sql_process_result = sql_select($Self_sqls_1);
		foreach($sql_process_result as $rows)
		{
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("BATCH_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]][$rows[csf("PROCESS_ID")]]["BILL_NO"]=$rows[csf(BILL_NO)];
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("BATCH_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]][$rows[csf("PROCESS_ID")]]["COMPANY_ID"]=$rows[csf(COMPANY_ID)];
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("BATCH_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]][$rows[csf("PROCESS_ID")]]["LOCATION_ID"]=$rows[csf(LOCATION_ID)];
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("BATCH_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]][$rows[csf("PROCESS_ID")]]["BILL_DATE"]=$rows[csf(BILL_DATE)];
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("BATCH_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]][$rows[csf("PROCESS_ID")]]["PARTY_ID"]=$rows[csf(PARTY_ID)];
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("BATCH_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]][$rows[csf("PROCESS_ID")]]["DELIVERY_QTY"]=+$rows[csf(DELIVERY_QTY)];
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("BATCH_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]][$rows[csf("PROCESS_ID")]]["RATE"]=$rows[csf(RATE)];
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("BATCH_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]][$rows[csf("PROCESS_ID")]]["FEBRIC_DESCRIPTION_ID"]=$rows[csf(FEBRIC_DESCRIPTION_ID)];
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("BATCH_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]][$rows[csf("PROCESS_ID")]]["DELIVERY_ID"].=$rows[csf(DELIVERY_ID)].", ";
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("BATCH_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]][$rows[csf("PROCESS_ID")]]["COLLER_CUFF_MEASUREMENT"]=$rows[csf(COLLER_CUFF_MEASUREMENT)];
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("BATCH_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]][$rows[csf("PROCESS_ID")]]["DELIVERY_QTYPCS"]=+$rows[csf(DELIVERY_QTYPCS)];
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("BATCH_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]][$rows[csf("PROCESS_ID")]]["AMOUNT"]+=$rows[csf(AMOUNT)];
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("BATCH_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]][$rows[csf("PROCESS_ID")]]["BODY_PART_ID"].=$body_part_library[$rows[csf(BODY_PART_ID)]].",";
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("BATCH_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]][$rows[csf("PROCESS_ID")]]["SHADE_PERCENTAGE"]=$rows[csf(SHADE_PERCENTAGE)];
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("BATCH_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]][$rows[csf("PROCESS_ID")]]["COLOR_RANGE_ID"]=$rows[csf(COLOR_RANGE_ID)];
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("BATCH_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]][$rows[csf("PROCESS_ID")]]["ADD_PROCESS_NAME"]=$rows[csf(ADD_PROCESS_NAME)];
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("BATCH_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]][$rows[csf("PROCESS_ID")]]["BATCH_NO"]=$rows[csf(BATCH_NO)];
			
		}	

	?>
<div style="width:1410px;" align="center">
		<fieldset style="width:100%;">
			<table align="center" cellpadding="0" width="100%" class="rpt_table" id="table_header_1" rules="all" border="1">
				<tr><td align="center"><strong> Date Wise Details Report </strong></td></tr>
				<tr><td align="center"><strong> Report Date<?  echo "   ".$date_from." To. ". $date_to; ?> <? 
						if ($cbo_body_part_search==1) $cbo_body_part = "Without Collar N Cuff";
						if ($cbo_body_part_search==2) $cbo_body_part = "Collar N Cuff";
						if ($cbo_body_part_search==3) $cbo_body_part = "Drawstring";
						echo " ".$cbo_body_part;
				?> </strong></td></tr>
			</table><br><br>
		
		<div style="width:1410px;" align="center">	
            <table align="center" width="1400px" cellpadding="0" width="100%" class="rpt_table" id="table_header_1" rules="all" border="1">
                <thead>
                	<tr>
                    	<th width="30">SL</th>
						<th width="120">BILL_NO</th>
						<th width="80">BILL_DATE</th>
						<th width="190">COMPANY_ID</th>
						<th width="150">PARTY</th>
						<th width="120">COLOR RANGE</th>
						<th width="100">BATCH NO</th>
						<th width="120">FABRIC</th>
						<th width="70">DEL(KG)</th>
						<th width="70">DEL(PCS)</th>
						<th width="60">RATE</th>
						<th width="80">AMOUNT</th>						
						<th style="word-wrap: break-word;word-break: break-all;">PROCESS</th>
                    </tr>
				</thead>
			</table>
		</div>
		<div style="width:1410px;" align="center" style="max-height:500px; overflow-y:scroll;" id="scroll_body">	
			<table width="1400px" align="center"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<tbody>
				<?  
				$sl=0;
					foreach($data as $bid => $bval)
					{
						foreach($bval as $cid => $cval)
						{
							foreach($cval as $lid => $lval)
							{
								foreach($lval as $pid => $pval)
								{
									foreach($pval as $bt_id => $btval)
									{
										foreach($btval as $bfid => $bfval)
										{
											foreach($bfval as $did => $dval)
											{
												foreach($dval as $fbfid => $fbf_va)
												{
													foreach($fbf_va as $pro_id => $dr)
													{
														if ($sl%2==0)  
																	$bgcolor="#E9F3FF";
																	else
																	$bgcolor="#FFFFFF";
														?>
														<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl;?>">
															<td width="30"><? echo $sl;?>	</td>
															<td width="120"><? echo $dr[csf('BILL_NO')];?>	</td>
															<td width="80"><? echo $dr[csf('BILL_DATE')];?></td>
															<td width="190"><? echo $company_library[$dr[csf('COMPANY_ID')]];?></td>														
															<td width="150"><? echo $company_library[$dr[csf('PARTY_ID')]];?></td>
															<td width="120" style="word-wrap: break-word;word-break: break-all;"><? echo $color_range[$dr[csf('COLOR_RANGE_ID')]];?></td>
															<td width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $dr[csf('BATCH_NO')];?></td>
															<td width="120" style="word-wrap: break-word;word-break: break-all;"><? echo $fab_des_library[$dr[csf('FEBRIC_DESCRIPTION_ID')]];?></td>
															<td width="70" align="center"><? echo $dr[csf('DELIVERY_QTY')];?></td>
															<td width="70" align="center"><? echo $dr[csf('DELIVERY_QTYPCS')];?></td>
															<td width="60" align="center"><? echo $dr[csf('RATE')];?></td>
															<td width="80" align="right"><?  echo $dr[csf('AMOUNT')]; //if($dr[csf('COLLER_CUFF_MEASUREMENT')]== "") echo $dr[csf('DELIVERY_QTY')]*$dr[csf('RATE')];else  echo $dr[csf('DELIVERY_QTYPCS')]*$dr[csf('RATE')];?></td>
															
															<td style="word-wrap: break-word;word-break: break-all;"><? echo $dr[csf('ADD_PROCESS_NAME')];?></td>
														</tr>
														<?
														$sl++;
														$tot_del+= $dr[csf('DELIVERY_QTY')];
														$tot_del_pcs+= $dr[csf('DELIVERY_QTYPCS')];
														/* if($dr[csf('COLLER_CUFF_MEASUREMENT')]== "") 
														   $amount_tot=  $dr[csf('DELIVERY_QTY')]*$dr[csf('RATE')];
														   else $amount_tot=  $dr[csf('DELIVERY_QTYPCS')]*$dr[csf('RATE')];*/
														 $amount_tot+=$dr[csf('AMOUNT')];
													}
												}
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
						<td width="120" colspan="7" style="word-wrap: break-word;word-break: break-all;"></td>
						<td width="120" style="word-wrap: break-word;word-break: break-all;"><strong> Total :</strong></td>
						<td width="70" align="center"><strong><? echo number_format($tot_del,2); ?></strong></td>
						<td width="70" align="center"><strong><? echo number_format($tot_del_pcs,2); ?></strong></td>
						<td width="60" align="center"><strong><? echo number_format($amount_tot/($tot_del+$tot_del_pcs),2); ?></strong></td>
						<td width="80" align="right"><strong><? echo number_format($amount_tot,2); ?></strong></td>
						
						<td style="word-wrap: break-word;word-break: break-all;"></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
</div>			
	<?
	//-----------------------------------------------------------------------------------------------excel export---------------------------
    foreach (glob("$user_id*.xls") as $filename) {
        if (@filemtime($filename) < (time() - $seconds_old))
            @unlink($filename);
    }
    //---------end------------//
    $name = time();
    $filename = $user_id . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, ob_get_contents());
    $filename = $user_id . "_" . $name . ".xls";
    echo "$total_data####$filename";
    exit();
}
if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$txt_bill_no=str_replace("'","",$txt_bill_no);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$cbo_location_id=str_replace("'","",$cbo_location_id);
	$cbo_process_id=str_replace("'","",$cbo_process_id);
	$cbo_body_part_search=str_replace("'","",$cbo_body_part_search);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	//$txt_bill_no."<BR><BR>";
	
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$body_part_library=return_library_array( "select id,BODY_PART_FULL_NAME from LIB_BODY_PART", "id", "BODY_PART_FULL_NAME"  );
	$fab_des_library=return_library_array( "select id,CONSTRUCTION from LIB_YARN_COUNT_DETERMINA_MST", "id", "CONSTRUCTION"  );
	$batch_library=return_library_array( "select id,BATCH_NO from PRO_BATCH_CREATE_MST", "id", "BATCH_NO"  );
	
	if ($cbo_process_id==0 || $cbo_process_id=='') $cbo_process_cond =""; else $cbo_process_cond =" and A.PROCESS_ID=$cbo_process_id ";
	//------------------------------
	if ($cbo_body_part_search==1) $cbo_body_part = "not in (2,3)";
	if ($cbo_body_part_search==2) $cbo_body_part = "in (2,3)";
	if ($cbo_body_part_search==3) $cbo_body_part = "in (46)";
	if ($cbo_body_part_search==0 || $cbo_body_part_search=='') $cbo_body_part_search_cond =""; else $cbo_body_part_search_cond =" and B.BODY_PART_ID $cbo_body_part ";
	//--------------------------------
	$bill = $txt_bill_no;
	if ($bill=="") $txt_bill_cond=""; else $txt_bill_cond =" and A.BILL_NO like '%$bill%'";
	
	if ($cbo_buyer_id==0  || $cbo_buyer_id=='') $buyer_name_cond =""; else $buyer_name_cond =" and p.buyer_id=$cbo_buyer_id ";
	if ($cbo_location_id==0 || $cbo_location_id=='') $location_id =""; else $location_id =" and A.LOCATION_ID =$cbo_location_id ";
	if ($cbo_company_id==0 || $cbo_company_id=='') $cbo_company_cond =""; else $cbo_company_cond =" and A.COMPANY_ID=$cbo_company_id ";
	if ($cbo_search_by==0 || $cbo_search_by=='') $cbo_search_by_cond =""; else $cbo_search_by_cond =" and A.BILL_FOR=$cbo_search_by ";
	
	//echo $txt_bill_cond."<BR><BR>";//die;
	if($db_type==0)
	{
		if( $date_from!="" && $date_to!="" )
		{
			$prod_date_cond .= " and A.BILL_DATE between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
		}
	}
	else if($db_type==2)
	{
		if($date_from!="" && $date_to!="") 
		{
			$prod_date_cond .= " and A.BILL_DATE between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
		}
	}
	ob_start();	
	$Self_sqls_1="SELECT A.ID, A.BILL_NO,  A.COMPANY_ID,  A.LOCATION_ID,  A.BILL_DATE,  A.PARTY_ID,  A.BILL_FOR,  A.PROCESS_ID,  B.DELIVERY_QTY,  B.RATE,  B.AMOUNT,  B.REMARKS,  B.CURRENCY_ID,
	B.DELIVERY_QTYPCS,  B.COLLER_CUFF_MEASUREMENT,  B.DELIVERY_ID,  B.FEBRIC_DESCRIPTION_ID,B.BODY_PART_ID,  B.SHADE_PERCENTAGE,  B.COLOR_RANGE_ID,  B.ADD_PROCESS_NAME, B.BATCH_ID
	FROM SUBCON_INBOUND_BILL_MST A INNER JOIN SUBCON_INBOUND_BILL_DTLS B
	ON A.ID= B.MST_ID WHERE  B.STATUS_ACTIVE = 1 AND A.STATUS_ACTIVE = 1 $cbo_company_cond $location_id $buyer_name_cond $cbo_process_cond $prod_date_cond $txt_bill_cond $cbo_search_by_cond $cbo_body_part_search_cond";
	
	echo $Self_sqls_1;die;
		$sql_process_result = sql_select($Self_sqls_1);
		foreach($sql_process_result as $rows)
		{
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("PROCESS_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]]["BILL_NO"]=$rows[csf(BILL_NO)];
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("PROCESS_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]]["COMPANY_ID"]=$rows[csf(COMPANY_ID)];
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("PROCESS_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]]["LOCATION_ID"]=$rows[csf(LOCATION_ID)];
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("PROCESS_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]]["BILL_DATE"]=$rows[csf(BILL_DATE)];
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("PROCESS_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]]["PARTY_ID"]=$rows[csf(PARTY_ID)];
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("PROCESS_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]]["DELIVERY_QTY"]+=$rows[csf(DELIVERY_QTY)];
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("PROCESS_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]]["RATE"]=$rows[csf(RATE)];
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("PROCESS_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]]["FEBRIC_DESCRIPTION_ID"]=$rows[csf(FEBRIC_DESCRIPTION_ID)];
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("PROCESS_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]]["DELIVERY_ID"].=$rows[csf(DELIVERY_ID)].", ";
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("PROCESS_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]]["COLLER_CUFF_MEASUREMENT"]=$rows[csf(COLLER_CUFF_MEASUREMENT)];
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("PROCESS_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]]["DELIVERY_QTYPCS"]=+$rows[csf(DELIVERY_QTYPCS)];
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("PROCESS_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]]["AMOUNT"]+=$rows[csf(AMOUNT)];
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("PROCESS_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]]["BODY_PART_ID"].=$body_part_library[$rows[csf(BODY_PART_ID)]].",";
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("PROCESS_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]]["SHADE_PERCENTAGE"]=$rows[csf(SHADE_PERCENTAGE)];
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("PROCESS_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]]["COLOR_RANGE_ID"]=$rows[csf(COLOR_RANGE_ID)];
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("PROCESS_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]]["ADD_PROCESS_NAME"]=$rows[csf(ADD_PROCESS_NAME)];
			$data[$rows[csf("ID")]][$rows[csf("COMPANY_ID")]][$rows[csf("LOCATION_ID")]][$rows[csf("PARTY_ID")]][$rows[csf("PROCESS_ID")]][$rows[csf("BILL_FOR")]][$rows[csf("DELIVERY_ID")]][$rows[csf("FEBRIC_DESCRIPTION_ID")]]["BATCH_ID"]=$rows[csf(BATCH_ID)];
		}	

	?>
<div style="width:1410px;" align="center">
		<fieldset style="width:100%;">
			<table align="center" cellpadding="0" width="100%" class="rpt_table" id="table_header_1" rules="all" border="1">
				<tr><td colspan="13" align="center"><strong> Date Wise Details Report </strong></td></tr>
				<tr><td colspan="13" align="center"><strong> Report Date<?  echo "   ".$date_from." To. ". $date_to; ?> <? 
						if ($cbo_body_part_search==1) $cbo_body_part = "Without Collar N Cuff";
						if ($cbo_body_part_search==2) $cbo_body_part = "Collar N Cuff";
						if ($cbo_body_part_search==3) $cbo_body_part = "Drawstring";
						echo " ".$cbo_body_part;
				?> </strong></td></tr>
			</table><br><br>
		
		<div style="width:1410px;" align="center">	
            <table align="center" width="1400px" cellpadding="0"  class="rpt_table" id="table_header_1" rules="all" border="1">
                <thead>
                	<tr>
                    	<th width="30">SL</th>
						<th width="120">BILL_NO</th>
						<th width="80">BILL_DATE</th>
						<th width="190">COMPANY_ID</th>
						<th width="150">PARTY</th>
						<th width="120">COLOR RANGE</th>
						<th width="100">BATCH NO</th>
						<th width="120">FABRIC</th>
						<th width="70">DEL(KG)</th>
						<th width="70">DEL(PCS)</th>
						<th width="60">RATE</th>
						<th width="80">AMOUNT</th>						
						<th style="word-wrap: break-word;word-break: break-all;">PROCESS</th>
                    </tr>
				</thead>
			</table>
		</div>
		<div style="width:1410px;max-height:500px; overflow-y:scroll;" align="center" id="scroll_body">	
			<table width="1400px" align="center"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<tbody>
				<?  
				$sl=0;
					foreach($data as $bid => $bval)
					{
						foreach($bval as $cid => $cval)
						{
							foreach($cval as $lid => $lval)
							{
								foreach($lval as $pid => $pval)
								{
									foreach($pval as $prid => $prval)
									{
										foreach($prval as $bfid => $bfval)
										{
											foreach($bfval as $did => $dval)
											{
												foreach($dval as $fbfid => $dr)
												{
													if ($sl%2==0)  
																$bgcolor="#E9F3FF";
																else
																$bgcolor="#FFFFFF";
													?>
													<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl;?>">
														<td width="30"><? echo $sl;?>	</td>
														<td width="120"><? echo $dr[csf('BILL_NO')];?>	</td>
														<td width="80"><? echo $dr[csf('BILL_DATE')];?></td>
														<td width="190"><? echo $company_library[$dr[csf('COMPANY_ID')]];?></td>														
														<td width="150"><? echo $company_library[$dr[csf('PARTY_ID')]];?></td>
														<td width="120" style="word-wrap: break-word;word-break: break-all;"><? echo $color_range[$dr[csf('COLOR_RANGE_ID')]];?></td>
														<td width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $batch_library[$dr[csf('BATCH_ID')]];?></td>
														<td width="120" style="word-wrap: break-word;word-break: break-all;"><? echo $fab_des_library[$dr[csf('FEBRIC_DESCRIPTION_ID')]];?></td>
														<td width="70" align="center"><? echo $dr[csf('DELIVERY_QTY')];?></td>
														<td width="70" align="center"><? echo $dr[csf('DELIVERY_QTYPCS')];?></td>
														<td width="60" align="center"><? echo $dr[csf('RATE')];?></td>
														<td width="80" align="right"><?  echo $dr[csf('AMOUNT')]; //if($dr[csf('COLLER_CUFF_MEASUREMENT')]== "") echo $dr[csf('DELIVERY_QTY')]*$dr[csf('RATE')];else  echo $dr[csf('DELIVERY_QTYPCS')]*$dr[csf('RATE')];?></td>
														
														<td style="word-wrap: break-word;word-break: break-all;"><? echo $dr[csf('ADD_PROCESS_NAME')];?></td>
													</tr>
													<?
													$sl++;
													$tot_del+= $dr[csf('DELIVERY_QTY')];
													$tot_del_pcs+= $dr[csf('DELIVERY_QTYPCS')];
													/* if($dr[csf('COLLER_CUFF_MEASUREMENT')]== "") 
													   $amount_tot=  $dr[csf('DELIVERY_QTY')]*$dr[csf('RATE')];
													   else $amount_tot=  $dr[csf('DELIVERY_QTYPCS')]*$dr[csf('RATE')];*/
													 $amount_tot+=$dr[csf('AMOUNT')];
												}
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
						<td colspan="7" style="word-wrap: break-word;word-break: break-all;"></td>
						<td width="120" style="word-wrap: break-word;word-break: break-all;"><strong> Total :</strong></td>
						<td width="70" align="center"><strong><? echo number_format($tot_del,2); ?></strong></td>
						<td width="70" align="center"><strong><? echo number_format($tot_del_pcs,2); ?></strong></td>
						<td width="60" align="center"><strong><? echo number_format($amount_tot/($tot_del+$tot_del_pcs),2); ?></strong></td>
						<td width="80" align="right"><strong><? echo number_format($amount_tot,2); ?></strong></td>
						
						<td style="word-wrap: break-word;word-break: break-all;"></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
</div>			
	<?
	//-----------------------------------------------------------------------------------------------excel export---------------------------
    foreach (glob("$user_id*.xls") as $filename) {
        if (@filemtime($filename) < (time() - $seconds_old))
            @unlink($filename);
    }
    //---------end------------//
    $name = time();
    $filename = $user_id . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, ob_get_contents());
    $filename = $user_id . "_" . $name . ".xls";
    echo "$total_data####$filename";
    exit();
}
if($action=="report_generate_summary")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$txt_bill_no=str_replace("'","",$txt_bill_no);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$cbo_location_id=str_replace("'","",$cbo_location_id);
	$cbo_process_id=str_replace("'","",$cbo_process_id);
	$cbo_body_part_search=str_replace("'","",$cbo_body_part_search);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	//echo $txt_bill_no."<BR><BR>";
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$body_part_library=return_library_array( "select id,BODY_PART_FULL_NAME from LIB_BODY_PART", "id", "BODY_PART_FULL_NAME"  );
	
	if ($cbo_process_id==0 || $cbo_process_id=='') $cbo_process_cond =""; else $cbo_process_cond =" and A.PROCESS_ID=$cbo_process_id ";
	
	//-------------------------------
	if ($cbo_body_part_search==1) $cbo_body_part = "not in (2,3)";
	if ($cbo_body_part_search==2) $cbo_body_part = "in (2,3)";
	if ($cbo_body_part_search==3) $cbo_body_part = "in (46)";
	if ($cbo_body_part_search==0 || $cbo_body_part_search=='') $cbo_body_part_search_cond =""; else $cbo_body_part_search_cond =" and B.BODY_PART_ID $cbo_body_part ";
	//--------------------------------
	$bill = $txt_bill_no;
	if ($bill=="") $txt_bill_cond=""; else $txt_bill_cond =" and A.BILL_NO like '%$bill%'";
	
	if ($cbo_buyer_id==0  || $cbo_buyer_id=='') $buyer_name_cond =""; else $buyer_name_cond =" and p.buyer_id=$cbo_buyer_id ";
	if ($cbo_location_id==0 || $cbo_location_id=='') $location_id =""; else $location_id =" and A.LOCATION_ID =$cbo_location_id ";
	if ($cbo_company_id==0 || $cbo_company_id=='') $cbo_company_cond =""; else $cbo_company_cond =" and A.COMPANY_ID=$cbo_company_id ";
	if ($cbo_search_by==0 || $cbo_search_by=='') $cbo_search_by_cond =""; else $cbo_search_by_cond =" and A.BILL_FOR=$cbo_search_by ";
	
	//echo $cbo_search_by_cond."<BR><BR>";die;
	if($db_type==0)
	{
		if( $date_from!="" && $date_to!="" )
		{
			$prod_date_cond .= " and A.BILL_DATE between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
		}
	}
	else if($db_type==2)
	{
		if($date_from!="" && $date_to!="") 
		{
			$prod_date_cond .= " and A.BILL_DATE between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
		}
	}
	ob_start();	
	$Self_sqls_1="SELECT A.ID, A.BILL_NO,  A.COMPANY_ID,  A.LOCATION_ID,  A.BILL_DATE,  A.PARTY_ID,  A.BILL_FOR,  A.PROCESS_ID,  B.DELIVERY_QTY,  B.RATE,  B.AMOUNT,  B.REMARKS,  B.CURRENCY_ID,
	B.DELIVERY_QTYPCS,  B.COLLER_CUFF_MEASUREMENT,  B.DELIVERY_ID,  B.FEBRIC_DESCRIPTION_ID, B.BODY_PART_ID,  B.SHADE_PERCENTAGE,  B.COLOR_RANGE_ID,  B.ADD_PROCESS_NAME FROM SUBCON_INBOUND_BILL_MST A INNER JOIN SUBCON_INBOUND_BILL_DTLS B
	ON A.ID= B.MST_ID WHERE  B.STATUS_ACTIVE = 1 AND A.STATUS_ACTIVE = 1 $cbo_company_cond $location_id $buyer_name_cond $cbo_process_cond $prod_date_cond $txt_bill_cond $cbo_search_by_cond $cbo_body_part_search_cond ";

	//echo $Self_sqls_1;die;
		$sql_process_result = sql_select($Self_sqls_1);
		foreach($sql_process_result as $rows)
		{
			$data[$rows[csf("BILL_DATE")]][$rows[csf("PARTY_ID")]][$rows[csf("BILL_FOR")]]["BILL_FOR"]=$rows[csf(BILL_FOR)];
			$data[$rows[csf("BILL_DATE")]][$rows[csf("PARTY_ID")]][$rows[csf("BILL_FOR")]]["BILL_DATE"]=$rows[csf(BILL_DATE)];
			$data[$rows[csf("BILL_DATE")]][$rows[csf("PARTY_ID")]][$rows[csf("BILL_FOR")]]["PARTY_ID"]=$rows[csf(PARTY_ID)];
			$data[$rows[csf("BILL_DATE")]][$rows[csf("PARTY_ID")]][$rows[csf("BILL_FOR")]]["DELIVERY_QTY"]+=$rows[csf(DELIVERY_QTY)];
			$data[$rows[csf("BILL_DATE")]][$rows[csf("PARTY_ID")]][$rows[csf("BILL_FOR")]]["DELIVERY_QTYPCS"]+=$rows[csf(DELIVERY_QTYPCS)];
			$data[$rows[csf("BILL_DATE")]][$rows[csf("PARTY_ID")]][$rows[csf("BILL_FOR")]]["AMOUNT"]+=$rows[csf(AMOUNT)];
			$data[$rows[csf("BILL_DATE")]][$rows[csf("PARTY_ID")]][$rows[csf("BILL_FOR")]]["BODY_PART_ID"].=$body_part_library[$rows[csf(BODY_PART_ID)]].",";
			$data[$rows[csf("BILL_DATE")]][$rows[csf("PARTY_ID")]][$rows[csf("BILL_FOR")]]["SHADE_PERCENTAGE"]=$rows[csf(SHADE_PERCENTAGE)];
			$data[$rows[csf("BILL_DATE")]][$rows[csf("PARTY_ID")]][$rows[csf("BILL_FOR")]]["ADD_PROCESS_NAME"]=$rows[csf(ADD_PROCESS_NAME)];
			$data[$rows[csf("BILL_DATE")]][$rows[csf("PARTY_ID")]][$rows[csf("BILL_FOR")]]["COLOR_RANGE_ID"]=$rows[csf(COLOR_RANGE_ID)];
			
			$data_date_tot_del[$rows[csf("BILL_DATE")]]+=$rows[csf(DELIVERY_QTY)];
			$data_date_tot_del_pcs[$rows[csf("BILL_DATE")]]+=$rows[csf(DELIVERY_QTYPCS)];
			$data_date_tot_amount[$rows[csf("BILL_DATE")]]+=$rows[csf(AMOUNT)];
		}	
	
	?>
		<div style="width:1210px;" align="center">
		<fieldset style="width:100%;">
			<table align="center" cellpadding="0" width="100%" class="rpt_table" id="table_header_1" rules="all" border="1">
				<tr><td align="center"><strong> Date Wise Summery Report </strong></td></tr>
				<tr><td align="center"><strong> Report Date<?  echo "   ".$date_from." To. ". $date_to; ?> <? 
						if ($cbo_body_part_search==1) $cbo_body_part = "Without Collar N Cuff";
						if ($cbo_body_part_search==2) $cbo_body_part = "Collar N Cuff";
						if ($cbo_body_part_search==3) $cbo_body_part = "Drawstring";
						echo " ".$cbo_body_part;
				?> </strong></td></tr>
			</table><br><br>
		
		<div style="width:1210px;" align="center">	
            <table align="center" width="1200px" cellpadding="0" width="100%" class="rpt_table" id="table_header_1" rules="all" border="1">
                <thead>
                	<tr>
                    	<th width="30">SL</th>
						<th width="100">BILL_DATE</th>
						<th width="200">PARTY</th>
						<th width="100">BILL FOR</th>
						<th width="100">DELIVERY(KG)</th>
						<th width="100">DELIVERY(PCS)</th>
						<th  width="100">AVG RATE</th>						
						<th width="100">AMOUNT</th>
						<th  width="150">COLOR RANGE</th>
						<th >PROCESS</th>
                    </tr>
				</thead>
			</table>
		</div>
		<div style="width:1210px;" align="center" style="max-height:500px; overflow-y:scroll;" id="scroll_body">	
			<table width="1200px" align="center"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<tbody>
					<?  
					$sl=0;
					foreach($data as $did => $dval)
					{
								foreach($dval as $pid => $pval)
								{
									foreach($pval as $bfid => $dr)
									{
													if ($sl%2==0)  
																$bgcolor="#E9F3FF";
																else
																$bgcolor="#FFFFFF";
													?>
													<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl;?>">
														<td width="30"><? echo $sl;?>	</td>
														<td width="100"><? echo $dr[csf('BILL_DATE')];?></td>														
														<td width="200"><? echo $company_library[$dr[csf('PARTY_ID')]];?></td>
														<td width="100"><? echo $bill_for[$dr[csf('BILL_FOR')]];?></td>
														<td width="100" align="center"><? echo $dr[csf('DELIVERY_QTY')];?></td>
														<td width="100" align="center"><? echo $dr[csf('DELIVERY_QTYPCS')];?></td>
														<td width="100" align="center"><? echo $dr[csf('AMOUNT')]/($dr[csf('DELIVERY_QTY')]+$dr[csf('DELIVERY_QTYPCS')]);?></td>
														<td width="100" align="right"><?  echo $dr[csf('AMOUNT')];?></td>
														<td  width="150" align="left"><?  echo $color_range[$dr[csf('COLOR_RANGE_ID')]];?></td>
														<td  align="left"><?  echo $dr[csf('ADD_PROCESS_NAME')];?></td>
														

													</tr>
													<?
													$sl++;
													$tot_del+= $dr[csf('DELIVERY_QTY')];
													$tot_del_pcs+= $dr[csf('DELIVERY_QTYPCS')];
													$amount_tot+=$dr[csf('AMOUNT')];
									}
								}
								?>
								<tr>
									<td align="right" colspan="4"><strong> <? echo $dr[csf('BILL_DATE')]."  ";?> Total :</strong></td>
									<td width="100" align="center"><strong><? echo number_format($data_date_tot_del[$did],2); ?></strong></td>
									<td width="100" align="center"><strong><? echo number_format($data_date_tot_del_pcs[$did],2); ?></strong></td>
									<td width="100" align="center"><strong><? echo number_format($data_date_tot_amount[$did]/($data_date_tot_del[$did]+$data_date_tot_del_pcs[$did]),2); ?></strong></td>
									
									<td  align="right"><strong><? echo number_format($data_date_tot_amount[$did],2); ?></strong></td>

								</tr>
								<?
					}
				?>
				</tbody>
					<tfoot>
					<tr>
						<td align="right" colspan="4"> <strong>Grand Total :</strong></td>
						<td width="100" align="center"><strong><? echo number_format($tot_del,2); ?></strong></td>
						<td width="100" align="center"><strong><? echo number_format($tot_del_pcs,2); ?></strong></td>
						<td width="100" align="center"><strong><? echo number_format($amount_tot/($tot_del+$tot_del_pcs),2); ?></strong></td>
						
						<td   align="right"><strong><? echo number_format($amount_tot,2); ?></strong></td>

					</tr>
					</tfoot>
			</table>
			</div>
		</fieldset>	
	</div>
	<?
	//-----------------------------------------------------------------------------------------------excel export---------------------------
    foreach (glob("$user_id*.xls") as $filename) {
        if (@filemtime($filename) < (time() - $seconds_old))
            @unlink($filename);
    }
    //---------end------------//
    $name = time();
    $filename = $user_id . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, ob_get_contents());
    $filename = $user_id . "_" . $name . ".xls";
    echo "$total_data####$filename";
    exit();
}
if($action=="report_generate_summary_bill")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$txt_bill_no=str_replace("'","",$txt_bill_no);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$cbo_location_id=str_replace("'","",$cbo_location_id);
	$cbo_process_id=str_replace("'","",$cbo_process_id);
	$cbo_body_part_search=str_replace("'","",$cbo_body_part_search);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	//echo $txt_bill_no."<BR><BR>";
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$body_part_library=return_library_array( "select id,BODY_PART_FULL_NAME from LIB_BODY_PART", "id", "BODY_PART_FULL_NAME"  );
	if ($cbo_process_id==0 || $cbo_process_id=='') $cbo_process_cond =""; else $cbo_process_cond =" and A.PROCESS_ID=$cbo_process_id ";
	
	//------------------------------
	if ($cbo_body_part_search==1) $cbo_body_part = "not in (2,3)";
	if ($cbo_body_part_search==2) $cbo_body_part = "in (2,3)";
	if ($cbo_body_part_search==3) $cbo_body_part = "in (46)";
	if ($cbo_body_part_search==0 || $cbo_body_part_search=='') $cbo_body_part_search_cond =""; else $cbo_body_part_search_cond =" and B.BODY_PART_ID $cbo_body_part ";
	//--------------------------------
	$bill = $txt_bill_no;
	if ($bill=="") $txt_bill_cond=""; else $txt_bill_cond =" and A.BILL_NO like '%$bill%'";
	
	if ($cbo_buyer_id==0  || $cbo_buyer_id=='') $buyer_name_cond =""; else $buyer_name_cond =" and p.buyer_id=$cbo_buyer_id ";
	if ($order_type==0) $order_type_cond =""; else $order_type_cond =" and a.order_type=$order_type ";
	if ($cbo_location_id==0 || $cbo_location_id=='') $location_id =""; else $location_id =" and A.LOCATION_ID =$cbo_location_id ";
	if ($cbo_company_id==0 || $cbo_company_id=='') $cbo_company_cond =""; else $cbo_company_cond =" and A.COMPANY_ID=$cbo_company_id ";
	if ($cbo_search_by==0 || $cbo_search_by=='') $cbo_search_by_cond =""; else $cbo_search_by_cond =" and A.BILL_FOR=$cbo_search_by ";
	
	//echo $cbo_search_by_cond."<BR><BR>";die;
	if($db_type==0)
	{
		if( $date_from!="" && $date_to!="" )
		{
			$prod_date_cond .= " and A.BILL_DATE between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
		}
	}
	else if($db_type==2)
	{
		if($date_from!="" && $date_to!="") 
		{
			$prod_date_cond .= " and A.BILL_DATE between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
		}
	}
	
	$Self_sqls_1="SELECT A.ID, A.BILL_NO,  A.COMPANY_ID,  A.LOCATION_ID,  A.BILL_DATE,  A.PARTY_ID,  A.BILL_FOR,  A.PROCESS_ID,  B.DELIVERY_QTY,  B.RATE,  B.AMOUNT,  B.REMARKS,  B.CURRENCY_ID,
	B.DELIVERY_QTYPCS,  B.COLLER_CUFF_MEASUREMENT,  B.DELIVERY_ID,  B.FEBRIC_DESCRIPTION_ID,B.BODY_PART_ID,  B.SHADE_PERCENTAGE,  B.COLOR_RANGE_ID,  B.ADD_PROCESS_NAME
	FROM SUBCON_INBOUND_BILL_MST A INNER JOIN SUBCON_INBOUND_BILL_DTLS B
	ON A.ID= B.MST_ID WHERE  B.STATUS_ACTIVE = 1 AND A.STATUS_ACTIVE = 1 $cbo_company_cond $location_id $buyer_name_cond $cbo_process_cond $prod_date_cond $txt_bill_cond $cbo_search_by_cond $cbo_body_part_search_cond";

	//echo $Self_sqls_1;die;
		$sql_process_result = sql_select($Self_sqls_1);
		foreach($sql_process_result as $rows)
		{
			$data[$rows[csf("BILL_DATE")]][$rows[csf("BILL_NO")]][$rows[csf("PARTY_ID")]][$rows[csf("BILL_FOR")]]["BILL_FOR"]=$rows[csf(BILL_FOR)];
			$data[$rows[csf("BILL_DATE")]][$rows[csf("BILL_NO")]][$rows[csf("PARTY_ID")]][$rows[csf("BILL_FOR")]]["BILL_NO"]=$rows[csf(BILL_NO)];
			$data[$rows[csf("BILL_DATE")]][$rows[csf("BILL_NO")]][$rows[csf("PARTY_ID")]][$rows[csf("BILL_FOR")]]["BILL_DATE"]=$rows[csf(BILL_DATE)];
			$data[$rows[csf("BILL_DATE")]][$rows[csf("BILL_NO")]][$rows[csf("PARTY_ID")]][$rows[csf("BILL_FOR")]]["PARTY_ID"]=$rows[csf(PARTY_ID)];
			$data[$rows[csf("BILL_DATE")]][$rows[csf("BILL_NO")]][$rows[csf("PARTY_ID")]][$rows[csf("BILL_FOR")]]["DELIVERY_QTY"]+=$rows[csf(DELIVERY_QTY)];
			$data[$rows[csf("BILL_DATE")]][$rows[csf("BILL_NO")]][$rows[csf("PARTY_ID")]][$rows[csf("BILL_FOR")]]["DELIVERY_QTYPCS"]+=$rows[csf(DELIVERY_QTYPCS)];
			$data[$rows[csf("BILL_DATE")]][$rows[csf("BILL_NO")]][$rows[csf("PARTY_ID")]][$rows[csf("BILL_FOR")]]["AMOUNT"]+=$rows[csf(AMOUNT)];
			$data[$rows[csf("BILL_DATE")]][$rows[csf("BILL_NO")]][$rows[csf("PARTY_ID")]][$rows[csf("BILL_FOR")]]["BODY_PART_ID"].=$body_part_library[$rows[csf(BODY_PART_ID)]].",";
			$data[$rows[csf("BILL_DATE")]][$rows[csf("BILL_NO")]][$rows[csf("PARTY_ID")]][$rows[csf("BILL_FOR")]]["SHADE_PERCENTAGE"]=$rows[csf(SHADE_PERCENTAGE)];
			$data[$rows[csf("BILL_DATE")]][$rows[csf("BILL_NO")]][$rows[csf("PARTY_ID")]][$rows[csf("BILL_FOR")]]["COLOR_RANGE_ID"]=$rows[csf(COLOR_RANGE_ID)];
			$data[$rows[csf("BILL_DATE")]][$rows[csf("BILL_NO")]][$rows[csf("PARTY_ID")]][$rows[csf("BILL_FOR")]]["ADD_PROCESS_NAME"]=$rows[csf(ADD_PROCESS_NAME)];
			
			$data_date_tot_del[$rows[csf("BILL_DATE")]]+=$rows[csf(DELIVERY_QTY)];
			$data_date_tot_del_pcs[$rows[csf("BILL_DATE")]]+=$rows[csf(DELIVERY_QTYPCS)];
			$data_date_tot_amount[$rows[csf("BILL_DATE")]]+=$rows[csf(AMOUNT)];
		}	
ob_start();		
	?>
<div style="width:1210px;" align="center">
	<fieldset style="width:100%;">
		<table align="center"  width="100%">
				<tr><td align="center"><strong> Date And Bill Number Wise Summery Report </strong></td></tr>
								<tr><td align="center"><strong> Report Date<?  echo "   ".$date_from." To. ". $date_to; ?> <? 
						if ($cbo_body_part_search==1) $cbo_body_part = "Without Collar N Cuff";
						if ($cbo_body_part_search==2) $cbo_body_part = "Collar N Cuff";
						if ($cbo_body_part_search==3) $cbo_body_part = "Drawstring";
						echo " ".$cbo_body_part;
				?> </strong></td></tr>
		</table><br><br>
		
		<div style="width:1210px;" align="center">	
            <table align="center" width="1200px" cellpadding="0" width="100%" class="rpt_table" id="table_header_1" rules="all" border="1">
                <thead>
                	<tr>
                    	<th width="30">SL</th>
						<th width="100">BILL DATE</th>
						<th width="120">BILL NO</th>
						<th width="200">PARTY</th>
						<th width="100">BILL FOR</th>
						<th width="100">DELIVERY(KG)</th>
						<th width="100">DELIVERY(PCS)</th>
						<th width="100">RATE</th>
						<th width="80">AMOUNT</th>
						<th>PROCESS</th>
                    </tr>
				</thead>
			</table>
		</div>
		<div style="width:1210px;" align="center" style="max-height:500px; overflow-y:scroll;" id="scroll_body">	
			<table width="1200px" align="center"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<tbody>
					<?  
					$sl=0;
					foreach($data as $did => $dval)
					{
						foreach($dval as $blid => $blval)
						{
								foreach($blval as $pid => $pval)
								{
									foreach($pval as $bfid => $dr)
									{
													if ($sl%2==0)  
																$bgcolor="#E9F3FF";
																else
																$bgcolor="#FFFFFF";
													?>
													<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl;?>">
														<td width="30"><? echo $sl;?>	</td>
														<td width="100"><? echo $dr[csf('BILL_DATE')];?></td>
														<td width="120"><? echo $dr[csf('BILL_NO')];?></td>															
														<td width="200"><? echo $company_library[$dr[csf('PARTY_ID')]];?></td>
														<td width="100"><? echo $bill_for[$dr[csf('BILL_FOR')]];?></td>
														<td width="100" align="center"><? echo $dr[csf('DELIVERY_QTY')];?></td>
														<td width="100" align="center"><? echo $dr[csf('DELIVERY_QTYPCS')];?></td>
														<td width="100" align="center"><? echo $dr[csf('AMOUNT')]/($dr[csf('DELIVERY_QTY')]+$dr[csf('DELIVERY_QTYPCS')]);?></td>
														<td  width="80" align="right"><?  echo $dr[csf('AMOUNT')];?></td>
														<td align="right"><?  echo $dr[csf('ADD_PROCESS_NAME')];?></td>
													</tr>
													<?
													$sl++;
													$tot_del+= $dr[csf('DELIVERY_QTY')];
													$tot_del_pcs+= $dr[csf('DELIVERY_QTYPCS')];
													$amount_tot+=$dr[csf('AMOUNT')];
									}
								}
						}
						?>
								<tr>
									<td align="right" colspan="5"><strong> Total :</strong></td>
									<td width="100" align="center"><strong><? echo number_format($data_date_tot_del[$did],2); ?></strong></td>
									<td width="100" align="center"><strong><? echo number_format($data_date_tot_del_pcs[$did],2); ?></strong></td>
									<td width="100" align="center"><strong><? echo number_format($data_date_tot_amount[$did]/($data_date_tot_del[$did]+$data_date_tot_del_pcs[$did]),2); ?></strong></td>
									<td  width="80" align="right"><strong><? echo number_format($data_date_tot_amount[$did],2); ?></strong></td>
									<td align="right"></td>
								</tr>
						<?
					}
				?>
				</tbody>

					<tfoot>
						<tr>
							<td align="right" colspan="5"> <strong>Grand Total :</strong></td>
							<td width="100" align="center"><strong><? echo number_format($tot_del,2); ?></strong></td>
							<td width="100" align="center"><strong><? echo number_format($tot_del_pcs,2); ?></strong></td>
							<td width="100" align="center"><strong><? echo number_format($amount_tot/($tot_del+$tot_del_pcs),2); ?></strong></td>
							<td width="80" align="right"><strong><? echo number_format($amount_tot,2); ?></strong></td>
							<td align="right"></td>
						</tr>
					</tfoot>
			</table>
		</div>
	</fieldset>	
</div>
	<?
    foreach (glob("$user_id*.xls") as $filename) {
        if (@filemtime($filename) < (time() - $seconds_old))
            @unlink($filename);
    }
    //---------end------------//
    $name = time();
    $filename = $user_id . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, ob_get_contents());
    $filename = $user_id . "_" . $name . ".xls";
    echo "$total_data####$filename";
    exit();
}

if($action=="material_desc_popup")
{
	echo load_html_head_contents("Material Description Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="11">Receive Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Receive ID</th>
                        <th width="70">Challan No</th>
                        <th width="70">Rec. Date</th>
                        <th width="60">Party</th>
                        <th width="80">Order No</th>
                        <th width="80">Category</th>
                        <th width="150">Description</th>
                        <th width="80">Receive Qty</th>
                        <th width="80">Bag/ Roll</th>
                        <th>Cone</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $party_arr=return_library_array( "select id, short_name from  lib_buyer",'id','short_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
				$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $i=0;
                
                $sql= "select a.sys_no, a.prefix_no_num, a.chalan_no, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia, sum(b.quantity) as quantity, sum(b.subcon_roll) as subcon_roll, sum(b.rec_cone) as rec_cone from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=2 and b.is_deleted=0 and a.trans_type=1 group by a.sys_no, a.prefix_no_num, a.chalan_no, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia order by a.sys_no, a.subcon_date";
               //echo $sql;
                $material_sql= sql_select($sql);
                foreach( $material_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					//if ($row[csf("color_id")]!=0 && $row[csf("gsm")]!=0  && $row[csf("grey_dia")]!=0  && $row[csf("fin_dia")]!=0 )
					//{
						$material_name=$row[csf("material_description")].', '.$color_arr[$row[csf("color_id")]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
					//}
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("prefix_no_num")];?> </td>
                    <td width="70"><? echo $row[csf("chalan_no")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("subcon_date")]);?> </td> 
                    <td width="60"><p><? echo $party_arr[$row[csf("party_id")]]; ?></p></td>
                    <td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                    <td align="center" width="150"><? echo $material_name; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
                    <td align="center" width="80"><? echo $row[csf("subcon_roll")];; ?></td>
                    <td><p><? echo $row[csf("rec_cone")]; ?></p></td>
                </tr>
                <? 
                $tot_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
        </div> 
	</fieldset>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="11">Return Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Return ID</th>
                        <th width="70">Challan No</th>
                        <th width="70">Return Date</th>
                        <th width="60">Party</th>
                        <th width="80">Order No</th>
                        <th width="80">Category</th>
                        <th width="150">Description</th>
                        <th width="80">Return Qty</th>
                        <th width="80">Bag/ Roll</th>
                        <th>Cone</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $party_arr=return_library_array( "select id, short_name from  lib_buyer",'id','short_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
				$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $i=0;
                
                $sql_ret= "select a.sys_no, a.prefix_no_num, a.chalan_no, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia, sum(b.quantity) as quantity, sum(b.subcon_roll) as subcon_roll, sum(b.rec_cone) as rec_cone from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trans_type=3 group by a.sys_no, a.prefix_no_num, a.chalan_no, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia order by a.sys_no, a.subcon_date";
               //echo $sql;
                $material_ret_sql= sql_select($sql_ret);
                foreach( $material_ret_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					//if ($row[csf("color_id")]!=0 && $row[csf("gsm")]!=0  && $row[csf("grey_dia")]!=0  && $row[csf("fin_dia")]!=0 )
					//{
					$material_name=$row[csf("material_description")].', '.$color_arr[$row[csf("color_id")]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
					//}
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("prefix_no_num")];?> </td>
                    <td width="70"><? echo $row[csf("chalan_no")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("subcon_date")]);?> </td> 
                    <td width="60"><p><? echo $party_arr[$row[csf("party_id")]]; ?></p></td>
                    <td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                    <td align="center" width="150"><? echo $material_name; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
                    <td align="center" width="80"><? echo $row[csf("subcon_roll")];; ?></td>
                    <td><p><? echo $row[csf("rec_cone")]; ?></p></td>
                </tr>
                <? 
                $tot_ret_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_ret_qty,2); ?></p></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
        </div> 
	</fieldset>
 </div> 
	<?
	exit();
}

if($action=="material_desc_iss_popup")
{
	echo load_html_head_contents("Material Issue Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="11">Issue Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Issue ID</th>
                        <th width="70">Challan No</th>
                        <th width="70">Issue Date</th>
                        <th width="60">Issue To</th>
                        <th width="80">Order No</th>
                        <th width="80">Category</th>
                        <th width="150">Description</th>
                        <th width="80">Issue Qty</th>
                        <th width="80">Bag/ Roll</th>
                        <th>Cone</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
				$company_array=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
				$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $i=0;
                
                $sql= "select a.sys_no, a.prefix_no_num, a.chalan_no, a.prod_source, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia, sum(b.quantity) as quantity, sum(b.subcon_roll) as subcon_roll, sum(b.rec_cone) as rec_cone from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trans_type=2 group by a.sys_no, a.prefix_no_num, a.chalan_no, a.prod_source, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia order by a.sys_no, a.subcon_date";
               //echo $sql;
                $material_sql= sql_select($sql);
                foreach( $material_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					//if ($row[csf("color_id")]!=0 && $row[csf("gsm")]!=0  && $row[csf("grey_dia")]!=0  && $row[csf("fin_dia")]!=0 )
					//{
						$material_name=$row[csf("material_description")].', '.$color_arr[$row[csf("color_id")]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
					//}
					$issue_to="";
					if($row[csf("prod_source")]==1) $issue_to=$company_array[$row[csf("party_id")]]; else if($row[csf("prod_source")]==3) $issue_to=$supplier_array[$row[csf("party_id")]]; else $issue_to="";
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("prefix_no_num")];?> </td>
                    <td width="70"><? echo $row[csf("chalan_no")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("subcon_date")]);?> </td> 
                    <td width="60"><p><? echo  $issue_to; ?></p></td>
                    <td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                    <td align="center" width="150"><? echo $material_name; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
                    <td align="center" width="80"><? echo $row[csf("subcon_roll")];; ?></td>
                    <td><p><? echo $row[csf("rec_cone")]; ?></p></td>
                </tr>
                <? 
                $tot_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
        </div> 
	</fieldset>
 </div> 
	<?
	exit();
}

if($action=="product_qty_pop_up")
{
	echo load_html_head_contents("Production Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	$order_id=$expData[0];
	$process_id=$expData[1];
	$btn_type=$expData[2];
		?>
        <fieldset style="width:820px">
            <div style="width:100%;" align="center">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                    <thead>
                    	<tr>
                            <th width="30">SL</th>
                            <th width="60"><? if ($process_id==3) echo "Batch NO"; else echo "Sys ID" ?></th>
                            <th width="70">Prod. Date</th>
                            <th width="100">Party</th>
                            <th width="80">Order No</th>
                            <th width="130">Process</th>
                            <th width="150">Description</th>
                            <th width="">Prod. Qty</th>
                    	</tr>
                    </thead>
                </table>
            </div>  
            <div style="width:100%; max-height:330px; overflow-y:scroll" align="left">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                    <?
					$party_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
					$kniting_item_arr=return_library_array( "select id, const_comp from lib_subcon_charge",'id','const_comp');
					$po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
					$po_party_arr=return_library_array( "select b.id, a.party_id from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst",'id','party_id');
					$color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
                    $i=0;
					if ($process_id==1)
					{
						 $sql = "select '' as sys_id, production_date, order_id, gmts_item_id as item_id, '' as process, sum(production_qnty) as production_qnty from subcon_gmts_prod_dtls where order_id='$order_id' and production_type=1 group by production_date, order_id, gmts_item_id";
					}
					else if ($process_id==5)
					{
						$sql = "select '' as sys_id, production_date, order_id, gmts_item_id as item_id, '' as process, sum(production_qnty) as production_qnty from subcon_gmts_prod_dtls where order_id='$order_id' and production_type=2 group by production_date, order_id, gmts_item_id";

					}
					else if ($process_id==11)
					{
						$sql = "select '' as sys_id, production_date, order_id, gmts_item_id as item_id, '' as process, sum(production_qnty) as production_qnty from subcon_gmts_prod_dtls where order_id='$order_id' and production_type=4 group by production_date, order_id, gmts_item_id";

					}
					else if ($process_id==2)
					{
						$sql="select a.prefix_no_num as sys_id, a.product_no, a.party_id, a.product_date as production_date, b.order_id, b.process, b.cons_comp_id as item_id, b.color_id, sum(b.no_of_roll) as roll_qty, sum(b.product_qnty) as production_qnty from subcon_production_mst a, subcon_production_dtls b where b.order_id='$order_id' and b.product_type=2 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.prefix_no_num, a.product_no, a.party_id, a.product_date, b.order_id, b.process, b.cons_comp_id, b.color_id order by b.color_id";
					}
					else if($process_id==3)
					{
						if($db_type==0)
						{
							$sql="select b.batch_no as sys_id, a.process_end_date as production_date, c.po_id as order_id, c.item_description as item_id, a.process_id as process, sum(c.batch_qnty) as production_qnty from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.entry_form=38 and a.result=1 and a.load_unload_id=2 and b.entry_form=36 and c.po_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.batch_no, a.process_end_date, a.process_id, c.po_id, c.item_description ";
						}
						elseif($db_type==2)
						{
							$sql="select b.batch_no as sys_id, a.process_end_date as production_date, c.po_id as order_id, c.item_description as item_id, a.process_id as process, sum(c.batch_qnty) as production_qnty from pro_fab_subprocess a, pro_batch_create_mst b,  pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.entry_form=38 and a.result=1 and a.load_unload_id=2 and b.entry_form=36 and c.po_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.batch_no, a.process_end_date, a.process_id, c.po_id, c.item_description ";
						}
					}
					else if($process_id==4)
					{
						$sql = "select a.prefix_no_num as sys_id, a.product_no, a.product_date as production_date, a.party_id, c.order_id, b.process as process, b.fabric_description as item_id, sum(c.quantity) as production_qnty from subcon_production_mst a, subcon_production_dtls b, subcon_production_qnty c where a.id=b.mst_id and b.id=c.dtls_id and c.order_id in ($order_id) and b.product_type='$process_id' group by a.prefix_no_num, a.product_no, a.party_id, a.product_date, c.order_id, b.process, b.fabric_description";
					}
                   //echo $sql;
					$production_sql= sql_select($sql); $color_array=array(); $k=1;
					foreach( $production_sql as $row )
                    {
                        $i++;
                        if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						
						if ($process_id==1 || $process_id==5 || $process_id==11)
						{
							$item_name=$garments_item[$row[csf('item_id')]];
							$party_name=$party_arr[$po_party_arr[$row[csf("order_id")]]];
						}
						else if ($process_id==2)
						{
							$party_name=$party_arr[$row[csf("party_id")]];
							$process_name=$conversion_cost_head_array[$row[csf("process")]];
							$item_name=$kniting_item_arr[$row[csf('item_id')]];
						}
						else if ($process_id==3)
						{
							$party_name=$party_arr[$po_party_arr[$row[csf("order_id")]]];
							$process_name="";
							$process_id=explode(',',$row[csf('process')]);
							foreach($process_id as $val)
							{
								if($process_name=="") $process_name=$conversion_cost_head_array[$val]; else $process_name.=','.$conversion_cost_head_array[$val];
							}
							$item_name=$row[csf('item_id')];
						}
						else if ($process_id==4)
						{
							$party_name=$party_arr[$row[csf("party_id")]];
							$process_name="";
							$process_id=explode(',',$row[csf('process')]);
							foreach($process_id as $val)
							{
								if($process_name=="") $process_name=$conversion_cost_head_array[$val]; else $process_name.=','.$conversion_cost_head_array[$val];
							}
							$item_name=$row[csf('item_id')];
						}
						else
						{
							$item_name=$row[csf('item_id')];
						}
						if ($process_id==2)
						{
							if (!in_array($row[csf("color_id")],$color_array) )
							{
								if($k!=1)
								{
								?>
									<tr class="tbl_bottom">
										<td colspan="7" align="right"><b>Color Total:</b></td>
										<td align="right"><? echo number_format($color_qty); ?></td>
									</tr>
									<tr bgcolor="#dddddd">
										<td colspan="8" align="left" ><b>Color : <? echo $color_arr[$row[csf("color_id")]]; ?></b></td>
									</tr>
								<?
									unset($color_qty);
								}
								else
								{
									?>
									<tr bgcolor="#dddddd">
										<td colspan="8" align="left" ><b>Color : <? echo $color_arr[$row[csf("color_id")]]; ?></b></td>
									</tr>
									<?
								}					
								$color_array[]=$row[csf('color_id')];            
								$k++;
							}							
						   ?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="60" align="center"><? echo $row[csf("sys_id")];?> </td>
								<td width="70"><? echo change_date_format($row[csf("production_date")]);?> </td> 
								<td width="100"><p><? echo $party_name; ?></p></td>
								<td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
								<td align="center" width="130"><p><? echo $process_name; ?></p></td>
								<td align="center" width="150"><p><? echo $item_name; ?></p></td>
								<td align="right" width=""><? echo number_format($row[csf("production_qnty")],2); ?></td>
							</tr>
							<? 
							$color_qty+=$row[csf("production_qnty")];
							$tot_qty+=$row[csf("production_qnty")];
							
						}
						else
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="60" align="center"><? echo $row[csf("sys_id")];?> </td>
								<td width="70"><? echo change_date_format($row[csf("production_date")]);?> </td> 
								<td width="100"><p><? echo $party_name; ?></p></td>
								<td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
								<td align="center" width="130"><p><? echo $process_name; ?></p></td>
								<td align="center" width="150"><p><? echo $item_name; ?></p></td>
								<td align="right" width=""><? echo number_format($row[csf("production_qnty")],2); ?></td>
							</tr>
							<?
							$tot_qty+=$row[csf("production_qnty")];
						}
					}
					if ($process_id==2)
					{ 
                    ?>
                        <tr class="tbl_bottom">
                            <td colspan="7" align="right"><b>Color Total:</b></td>
                            <td align="right"><? echo number_format($color_qty); ?></td>
                        </tr>
					<? } ?>
                    <tr class="tbl_bottom">
                    	<td colspan="7" align="right">Total: </td>
                        <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    </tr>
                </table>
            </div> 
		</fieldset>	  
		<?
		
	exit();
}

if($action=="bill_qty_pop_up")
{
	echo load_html_head_contents("Delivery Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Bill ID</th>
                        <th width="70">Bill Date</th>
                        <th width="100">Party</th>
                        <th width="80">Order No</th>
                        <th width="80">Category</th>
                        <th width="150">Description</th>
                        <th width="80">Bill Qty</th>
                        <th>Amount</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $party_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
				$kniting_item_arr=return_library_array( "select id, const_comp from lib_subcon_charge",'id','const_comp');
				$dye_fin_item_arr=return_library_array( "select id, item_description from pro_batch_create_dtls",'id','item_description');
                $i=0;
                $sql= "select a.bill_no, a.bill_date, a.party_id, b.order_id, b.process_id, b.item_id, sum(b.delivery_qty) as quantity, sum(b.amount) as amount from  subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and b.process_id='$expData[1]' and a.status_active=1 and a.is_deleted=0  group by a.bill_no, a.bill_date, a.party_id, b.order_id, b.process_id, b.item_id order by a.bill_no, a.bill_date";
                //echo $sql;
                $production_sql= sql_select($sql);
                foreach( $production_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					if ($row[csf("process_id")]==1 || $row[csf("process_id")]==5)
					{
						$item_name=$garments_item[$row[csf('item_id')]];
					}
					else if ($row[csf("process_id")]==2)
					{
						$item_name=$kniting_item_arr[$row[csf('item_id')]];
					}
					else if ($row[csf("process_id")]==3 || $row[csf("process_id")]==4)
					{
						$item_name=$dye_fin_item_arr[$row[csf('item_id')]];
					}
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="100"><? echo $row[csf("bill_no")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("bill_date")]);?> </td> 
                    <td width="100"><p><? echo $party_arr[$row[csf("party_id")]]; ?></p></td>
                    <td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $production_process[$row[csf("process_id")]]; ?></td>
                    <td align="center" width="150"><? echo $item_name; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
                    <td align="right" width=""><? echo number_format($row[csf("amount")],2); ?></td>
                </tr>
                <? 
                $tot_qty+=$row[csf("quantity")];
                $tot_amount+=$row[csf("amount")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="7" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    <td align="right"><p><? echo number_format($tot_amount,2); ?></p></td>
                </tr>
            </table>
        </div> 
	</fieldset>
 </div> 
	<?
	exit();
}

if($action=="image_view_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Work Progress Info","../../../", 1, 1, $unicode);
	//echo "select master_tble_id,image_location from common_photo_library where form_name='sub_contract_order_entry' and file_type=1 and master_tble_id='$id'";

	$imge_data=sql_select("select id,master_tble_id,image_location from common_photo_library where form_name='sub_contract_order_entry' and file_type=1 and master_tble_id='$id'");
	?>
	<table>
        <tr>
			<?
            foreach($imge_data as $row)
            {
				?>
                    <td><img src='../../../<? echo $imge_arr[$row[csf("id")]]; ?>' height='100px' width='100px' /></td>
				<?
            }
            ?>
        </tr>
	</table>
	<?
	exit();
}

if($action=="batch_qty_pop_up")
{	
	echo load_html_head_contents("Batch Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	$order_id=$expData[0];
	$btn_type=$expData[1];
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
	if ($btn_type == "") 
	{
		?>
	    <fieldset style="width:800px">
	        <div style="width:100%;" align="center">
	            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
	                <thead>
	                    <tr>
	                        <th width="30">SL</th>
	                        <th width="80">Batch No</th>
	                        <th width="30">Ext.</th>
	                        <th width="65">Batch Date</th>
	                        <th width="100">Color</th>
	                        <th width="100">Order</th>
	                        <th width="100">Rec. Challan</th>
	                        <th width="180">Description</th>
	                        <th width="">Batch Qty</th>
	                    </tr>
	                </thead>
	            </table>
	        </div>  
	        <div style="width:100%; max-height:330px; overflow-y:scroll" align="left">
	            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >    
				<?
					$sql_batch="Select a.batch_no, a.extention_no, a.batch_date, a.color_id, b.po_id, b.item_description, b.rec_challan, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form=36 and b.po_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.batch_no, a.extention_no, a.batch_date, a.color_id, b.po_id, b.item_description, b.rec_challan";
					$sql_batch_result=sql_select($sql_batch); $i=0;
					foreach ($sql_batch_result as $row)
					{
						$i++;
						if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="80" align="center"><? echo $row[csf("batch_no")];?> </td>
							<td width="30" align="center"><? echo $row[csf("extention_no")];?> </td>
							<td width="65"><? echo change_date_format($row[csf("batch_date")]);?> </td> 
							<td width="100"><p><? echo $color_arr[$row[csf("color_id")]];?></p></td>
							<td width="100"><? echo $po_arr[$row[csf("po_id")]]; ?></td>
							<td width="100"><p><? echo $row[csf("rec_challan")]; ?></p></td>
							<td width="180"><p><? echo $row[csf("item_description")]; ?></p></td>
							<td align="right" width=""><? echo number_format($row[csf("batch_qnty")],2); ?></td>
						</tr>
						<?
						$tot_batch_qnty+=$row[csf("batch_qnty")];
					}
					?>
	                <tr class="tbl_bottom">
	                    <td colspan="8" align="right">Total: </td>
	                    <td align="right"><p><? echo number_format($tot_batch_qnty,2); ?></p></td>
	                </tr>
	            </table>
	        </div> 
		</fieldset>
		<? 
	} 	
	else // summary btn
	{		
		echo load_html_head_contents("Batch Details", "../../../", 1, 1,$unicode,'','');
		//echo $order_id;//die;
		$expData=explode('_',$order_id);
		$order_id=$expData[0];
		//$process_id=$expData[1];
		$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
		$yarn_lot_arr=array();
		if($db_type==0)
		{
			$yarn_lot_data=sql_select("select b.po_breakdown_id, a.prod_id,group_concat(distinct(a.yarn_lot)) as yarn_lot from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yarn_lot!='0' group by a.prod_id, b.po_breakdown_id");
		}
		else if($db_type==2)
		{	
			$yarn_lot_data=sql_select("SELECT  b.order_id, b.yarn_lot 
				from subcon_production_mst a, subcon_production_dtls b 
				where a.id=b.mst_id and b.order_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.yarn_lot!='0' 
				group by b.order_id, b.yarn_lot ");	
		}
		
		foreach($yarn_lot_data as $rows)
		{
			$yarn_lot_arr[$rows[csf('order_id')]] = $rows[csf('yarn_lot')];
		}
		// print_r($yarn_lot_arr);
		?>
	    <fieldset style="width:950px">
	        <div style="width:100%;" align="center">
	            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
	                <thead>
	                    <tr>
	                        <th width="3%">SL</th>
	                        <th width="8%">Batch Date</th>
	                        <th width="10%">Batch No</th>
	                        <th width="10%">Party</th>
	                        <th width="10%">Order No.</th>
	                        <th width="10%">Batch Color</th>
	                        <th width="8%">Construction</th>
	                        <th width="8%">Composition</th>
	                        <th width="6%">Dia</th>
	                        <th width="6%">GSM</th>
	                        <th width="7%">Lot</th>
	                        <th width="7%">Batch Qty</th>
	                        <th width="7%">Batch Weight</th>
	                    </tr>
	                </thead>
	            </table>
	        </div>  
	        <div style="width:100%; max-height:330px; overflow-y:auto;" align="left">
	            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >    
				<?
					$const_comp = array();
					$const_comp_sql = "select comapny_id, buyer_id, const_comp from lib_subcon_charge order by comapny_id";
					$const_comp_result = sql_select($const_comp_sql);
					foreach ($const_comp_result as $key => $value) {
						
					}
					$sql_batch="SELECT a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.booking_no_id,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type, c.job_no_mst,d.job_no_prefix_num,d.party_id as buyer_name, f.gsm,f.grey_dia
					from pro_batch_create_mst a, pro_batch_create_dtls b,subcon_ord_dtls c,subcon_ord_mst d, sub_material_mst e, sub_material_dtls f  
					where a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and c.id=$order_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.entry_form=36 and f.order_id = $order_id and e.id = f.mst_id and f.id = b.prod_id  and f.status_active=1 and f.is_deleted =0 and c.status_active =1 and c.is_deleted =0
					GROUP BY a.batch_no, b.item_description,a.batch_date, a.batch_weight, a.color_id, a.booking_no_id, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.party_id,f.gsm,f.grey_dia order by a.batch_no";
					// echo $sql_batch; //and d.entry_form = 238
					$sql_batch_result=sql_select($sql_batch); $i=0;
					foreach ($sql_batch_result as $row)
					{
						$i++;
						if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						$desc=explode(",",$row[csf('item_description')]);
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="3%"><? echo $i; ?></td>
							<td width="8%"><? echo change_date_format($row[csf("batch_date")]);?> </td> 
							<td width="10%"><? echo $row[csf("batch_no")];?> </td>
							<td width="10%"><? echo $party_arr[$row[csf('buyer_name')]]; ?></td>
							<td width="10%"><? echo $po_arr[$row[csf("po_id")]]; ?></td>
							<td width="10%"><p><? echo $color_arr[$row[csf("color_id")]];?></p></td>
							<td width="8%"><? echo $desc[0];?></td>
							<td width="8%"><p><? echo $desc[1]; ?></p></td>
							<td align="center" width="6%"><p><? echo $row[csf("grey_dia")]; ?></p></td>
							<td align="center" width="6%"><? echo $row[csf("gsm")]; ?></td>
							<td align="right" width="7%"><? echo $yarn_lot_arr[$rows[csf('order_id')]]; ?></td>
							<td align="right" width="7%"><? echo number_format($row[csf("batch_qnty")]); ?></td>
							<td align="right" width="7%"><? echo $row[csf("batch_weight")]; ?></td>
						</tr>
						<?
						$tot_batch_qnty+=$row[csf("batch_qnty")];
						$tot_batch_weight+=$row[csf("batch_weight")];
					}
					?>
	                <tr class="tbl_bottom">
	                    <td colspan="11" align="right">Total: </td>
	                    <td align="right"><p><? echo number_format($tot_batch_qnty); ?></p></td>
	                    <td align="right"><p><? echo $tot_batch_weight; ?></p></td>
	                </tr>
	            </table>
	        </div> 
		</fieldset>	
		<?	
	}		
	exit();
}

if($action=="payment_rec_pop_up")
{
	echo load_html_head_contents("Payment Receive Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	$order_id=$expData[0];
	$order_bill_amount=$expData[1];
	//$process_id=$expData[1];
	$po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Rec. No</th>
                        <th width="120">Party</th>
                        <th width="65">Rec. Date</th>
                        <th width="80">Instrument</th>
                        <th width="60">Currency</th>
                        <th width="120">Bill No</th>
                        <th width="80">Order No</th>
                        <th width="65">Bill Date</th>
                        <th width="">Rec. Amount</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:330px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >    
			<?
			$order_wise_tot_bill="select a.id, d.order_id, b.bill_id, b.bill_amount as bill_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.order_id='$order_id' and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 order by a.id asc";
			$order_wise_tot_bill_result=sql_select($order_wise_tot_bill);
			foreach ($order_wise_tot_bill_result as $row)
			{
				$order_wise_tot_bill_arr2[$row[csf('order_id')]][$row[csf('bill_id')]][$row[csf('id')]]=$row[csf('bill_amount')];
			}

			$sum=0;
			foreach ($order_wise_tot_bill_arr2 as $key=>$value) 
			{
				foreach ($value as $val) 
				{
					foreach ($val as $val2) 
					{
						 $sum+=$val2;
						 break;
					}
				}
				$order_wise_tot_bill_arr[$key]=$sum;
				$sum=0;
			}

				//$payment_sql="select a.prefix_no_num, a.receive_no, a.party_name, a.receipt_date, a.instrument_id, a.currency_id, c.bill_no, c.bill_date, d.order_id, sum(b.total_adjusted) as rec_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and d.order_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by a.prefix_no_num, a.receive_no, a.party_name, a.receipt_date, a.instrument_id, a.currency_id, c.bill_no, c.bill_date, d.order_id";

			$payment_sql="select a.prefix_no_num, a.receive_no, a.party_name, a.receipt_date, a.instrument_id, a.currency_id, c.bill_no, c.bill_date, d.order_id, b.total_adjusted as rec_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and d.order_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by a.prefix_no_num, a.receive_no, a.party_name, a.receipt_date, a.instrument_id, a.currency_id, c.bill_no, c.bill_date, d.order_id,b.total_adjusted";

				$payment_sql_result=sql_select($payment_sql); $i=0;
				foreach ($payment_sql_result as $row)
				{
					$i++;
					if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="30"><? echo $i; ?></td>
						<td width="100" align="center"><? echo $row[csf("receive_no")];?> </td>
						<td width="120" align="center"><? echo $buyer_arr[$row[csf("party_name")]];?> </td>
						<td width="65"><? echo change_date_format($row[csf("receipt_date")]);?> </td> 
						<td width="80"><p><? echo $instrument_payment[$row[csf("instrument_id")]];?></p></td>
						<td width="60"><? echo $currency[$row[csf("currency_id")]]; ?></td>
						<td width="120"><p><? echo $row[csf("bill_no")]; ?></p></td>
                        <td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
						<td width="65"><? echo change_date_format($row[csf("bill_date")]);?> </td> 
						<td align="right" width="">
							<? 
							$received_amount = ($row[csf("rec_amount")]/$order_wise_tot_bill_arr[$order_id])*$order_bill_amount;
							echo number_format($received_amount,2); 
							
							//echo number_format($row[csf("rec_amount")],2); 
							?>
						</td>
					</tr>
					<?
					$tot_rec_amount+=$received_amount;
				}
				?>
                <tr class="tbl_bottom">
                    <td colspan="9" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_rec_amount,2); ?></p></td>
                </tr>
            </table>
        </div> 
	</fieldset>
 </div> 
	<?
	exit();
}


if($action=="order_desc_popup")
{
	echo load_html_head_contents("Order Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="11">Order Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Order No</th>
                        <th width="70">Category</th>
                        <th width="120">Item Description </th>
                        <th width="80">Color</th>
                        <th width="60">Size</th>
                        <th width="80">Receive Date</th>
                        <th width="50">Rate</th>
                        <th width="93">Quantity</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
				//$company_array=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

                $item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');	
				$size_arr=return_library_array( "select id,size_name from  lib_size",'id','size_name');
				$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $i=0;

                $sql="SELECT a.party_id, b.order_no, b.order_rcv_date, b.main_process_id, c.item_id, c.color_id, c.size_id, c.qnty, c.rate, c.gsm, c.grey_dia, c.finish_dia, c.dia_width_type 
                from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c 
                where c.mst_id=a.id and c.order_id=b.id and a.subcon_job=b.job_no_mst and b.id=$expData[0] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
                // echo $sql;
                $order_dtls_sql= sql_select($sql);
                foreach( $order_dtls_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";

                    $process_id=$row[csf('main_process_id')];
					
						//$material_name=$row[csf("material_description")].', '.$color_arr[$row[csf("color_id")]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("order_no")];?> </td>
                    <td align="center" width="70"><? echo $production_process[$row[csf("main_process_id")]];?> </td>
                    <td width="120">
                    	<? 
			                if($process_id==2 || $process_id==3 || $process_id==4 || $process_id==6 || $process_id==7)
							{
								echo $item_arr[$row[csf('item_id')]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("finish_dia")];	
							}
							else
							{
								echo $garments_item[$row[csf('item_id')]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("finish_dia")];
							}
                    	?> 
                    </td> 
                    <td align="center" width="80"><p><? echo  $color_arr[$row[csf("color_id")]]; ?></p></td>
                    <td align="center" width="60"><? echo $size_arr[$row[csf("size_id")]]; ?></td>
                    <td align="center" width="80"><? echo change_date_format($row[csf("order_rcv_date")]); ?></td>
                    
                    <td align="right" width="50"><? echo $row[csf("rate")]; ?> &nbsp; </td>
                    <td align="right" width="80"><? echo number_format($row[csf("qnty")]); ?> &nbsp;</td>
                   
                </tr>
                <? 
                $tot_qty+=$row[csf("qnty")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: &nbsp;</td>
                    <td align="right"><p><? echo number_format($tot_qty); ?> &nbsp; </p></td>
                </tr>
            </table>
        </div> 
	</fieldset>
 </div> 
	<?
	exit();
}

if($action=="delivery_qty_pop_up")
{
	echo load_html_head_contents("Delivery Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	if($expData[2] == "")
	{
		?>
        <fieldset style="width:820px">
            <div style="width:100%;" align="center">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                    <thead>
                    	<tr>
                            <th width="30">SL</th>
                            <th width="60">Delivery ID</th>
                            <th width="70">Delivery Date</th>
                            <th width="80">Batch No</th>
                            <th width="80">Order No</th>
                            <th width="80">Category</th>
                            <th width="150">Description</th>
                            <th width="">Delivery Qty</th>
                    	</tr>
                    </thead>
                </table>
            </div>  
            <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                    <?
					$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
					$po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
					$color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
					$kniting_item_arr=return_library_array( "select id, const_comp from lib_subcon_charge",'id','const_comp');
					$dye_fin_item_arr=return_library_array( "select id, item_description from pro_batch_create_dtls",'id','item_description');
                    $i=0;
                    $sql= "SELECT a.delivery_prefix_num, a.delivery_date, a.party_id, b.order_id, b.process_id, b.item_id, b.color_id, b.batch_id, sum(b.delivery_qty) as quantity from  subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and b.process_id='$expData[1]' and a.status_active=1 and a.is_deleted=0 group by a.delivery_prefix_num, a.delivery_date, a.party_id, b.order_id, b.process_id, b.batch_id, b.item_id, b.color_id order by a.delivery_prefix_num, a.delivery_date";
                    // echo $sql;
					$production_sql= sql_select($sql); $color_array=array(); $k=1; $process_id=0;
					foreach( $production_sql as $row )
                    {
                        $i++;
                        if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						$process_id=$row[csf("process_id")];
						if ($row[csf("process_id")]==1 || $row[csf("process_id")]==5)
						{
							$item_name=$garments_item[$row[csf('item_id')]];
						}
						else if ($row[csf("process_id")]==2)
						{
							$item_name=$kniting_item_arr[$row[csf('item_id')]];
						}
						else if ($row[csf("process_id")]==3 || $row[csf("process_id")]==4)
						{
							$item_name=$dye_fin_item_arr[$row[csf('item_id')]];
						}
						
						if ($row[csf("process_id")]==2)
						{
							if (!in_array($row[csf("color_id")],$color_array) )
							{
								if($k!=1)
								{
								?>
									<tr class="tbl_bottom">
										<td colspan="7" align="right"><b>Color Total:</b></td>
										<td align="right"><? echo number_format($color_qty); ?></td>
									</tr>
									<tr bgcolor="#dddddd">
										<td colspan="8" align="left" ><b>Color : <? echo $color_arr[$row[csf("color_id")]]; ?></b></td>
									</tr>
								<?
									unset($color_qty);
								}
								else
								{
									?>
									<tr bgcolor="#dddddd">
										<td colspan="8" align="left" ><b>Color : <? echo $color_arr[$row[csf("color_id")]]; ?></b></td>
									</tr>
									<?
								}					
								$color_array[]=$row[csf('color_id')];            
								$k++;
							}							
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="60"><? echo $row[csf("delivery_prefix_num")];?> </td>
								<td width="70"><? echo change_date_format($row[csf("delivery_date")]);?> </td> 
								<td width="80"><p><? echo $batch_arr[$row[csf("batch_id")]]; ?></p></td>
								<td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
								<td align="center" width="80"><? echo $production_process[$row[csf("process_id")]]; ?></td>
								<td align="center" width="150"><p><? echo $item_name; ?></p></td>
								<td align="right" width=""><? echo number_format($row[csf("quantity")],2); ?></td>
							</tr>
							<? 
							$color_qty+=$row[csf("quantity")];
							$tot_qty+=$row[csf("quantity")];
						}
						else
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="60"><? echo $row[csf("delivery_prefix_num")];?> </td>
								<td width="70"><? echo change_date_format($row[csf("delivery_date")]);?> </td> 
								<td width="80"><p><? echo $batch_arr[$row[csf("batch_id")]]; ?></p></td>
								<td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
								<td align="center" width="80"><? echo $production_process[$row[csf("process_id")]]; ?></td>
								<td align="center" width="150"><p><? echo $item_name; ?></p></td>
								<td align="right" width=""><? echo number_format($row[csf("quantity")],2); ?></td>
							</tr>
							<? 
							$tot_qty+=$row[csf("quantity")];
						}
					} 
					if($process_id==2)
					{
					?>
                        <tr class="tbl_bottom">
                            <td colspan="7" align="right"><b>Color Total:</b></td>
                            <td align="right"><? echo number_format($color_qty); ?></td>
                        </tr>
                    <?
					}
					?>
                    <tr class="tbl_bottom">
                    	<td colspan="7" align="right">Total: </td>
                        <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    </tr>
                </table>
            </div> 
		</fieldset>
 		</div> 
		<?
	}
	else
	{
		?>
        <fieldset style="width:820px">
            <div style="width:100%;" align="center">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                    <thead>
                    	<tr>
                            <th width="30">SL</th>
                            <th width="60">Delivery ID</th>
                            <th width="70">Delivery Date</th>
                            <th width="80">Batch No</th>
                            <th width="80">Order No</th>
                            <th width="80">Category</th>
                            <th width="150">Description</th>
                            <th width="">Delivery Qty</th>
                    	</tr>
                    </thead>
                </table>
            </div>  
            <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                    <?
					$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
					$po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
					$color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
					$kniting_item_arr=return_library_array( "select id, const_comp from lib_subcon_charge",'id','const_comp');
					$dye_fin_item_arr=return_library_array( "select id, item_description from pro_batch_create_dtls",'id','item_description');

					$knit_production_array=array();
					$knit_production_sql="SELECT b.order_id, sum(b.product_qnty) AS kniting
					from  subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and b.order_id = '$expData[0]' and b.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id";
					// echo $knit_production_sql;
					$knit_production_sql_result=sql_select($knit_production_sql);
					foreach ($knit_production_sql_result as $row)
					{
						$knit_production_array[$row[csf('order_id')]]['kniting']=$row[csf('kniting')];
					}	
					// var_dump ($knit_production_array);

                    $i=0;
                    $sql= "SELECT a.delivery_prefix_num, a.delivery_date, a.party_id, b.order_id, b.process_id, b.item_id, b.color_id, b.batch_id, sum(b.delivery_qty) as quantity from  subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and b.process_id='$expData[1]' and a.delivery_date between '$expData[2]' and '$expData[3]' and a.status_active=1 and a.is_deleted=0 group by a.delivery_prefix_num, a.delivery_date, a.party_id, b.order_id, b.process_id, b.batch_id, b.item_id, b.color_id order by a.delivery_prefix_num, a.delivery_date";
                    /*$sql="SELECT a.prefix_no_num as sys_id, a.product_no, a.party_id, a.product_date as production_date, b.order_id, b.process, b.cons_comp_id as item_id, b.color_id, sum(b.no_of_roll) as roll_qty
					from subcon_production_mst a, subcon_production_dtls b 
					where b.order_id='$expData[1]' and b.product_type=2 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
					group by a.prefix_no_num, a.product_no, a.party_id, a.product_date, b.order_id, b.process, b.cons_comp_id, b.color_id order by b.color_id";*/
                    // echo $sql;
					$production_sql= sql_select($sql); $color_array=array(); $k=1; $process_id=0;
					foreach( $production_sql as $row )
                    {
                        $i++;
                        if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						$process_id=$row[csf("process_id")];
						if ($row[csf("process_id")]==1 || $row[csf("process_id")]==5)
						{
							$item_name=$garments_item[$row[csf('item_id')]];
						}
						else if ($row[csf("process_id")]==2)
						{
							$item_name=$kniting_item_arr[$row[csf('item_id')]];
						}
						else if ($row[csf("process_id")]==3 || $row[csf("process_id")]==4)
						{
							$item_name=$dye_fin_item_arr[$row[csf('item_id')]];
						}						
						
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="60"><? echo $row[csf("delivery_prefix_num")];?> </td>
							<td width="70"><? echo change_date_format($row[csf("delivery_date")]);?> </td> 
							<td width="80"><p><? echo $batch_arr[$row[csf("batch_id")]]; ?></p></td>
							<td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
							<td align="center" width="80"><? echo $production_process[$row[csf("process_id")]]; ?></td>
							<td align="center" width="150"><p><? echo $item_name; ?></p></td>
							<td align="right" width=""><? echo $row[csf("quantity")];//echo number_format($knit_production_array[$row[csf('order_id')]]['kniting']); ?></td>
						</tr>
						<? 
						$tot_qty+=$row[csf("quantity")];
						
					} 
				
					?>
                    <tr class="tbl_bottom">
                    	<td colspan="7" align="right">Total: </td>
                        <td align="right"><p><? echo number_format($tot_qty); ?></p></td>
                    </tr>
                </table>
            </div> 
		</fieldset>
 		</div> 
		<?
	}
	
	
	exit();
}

if($action=="delivery_qty_fin_pop_up")
{
	echo load_html_head_contents("Delivery Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	?>
        <fieldset style="width:820px">
            <div style="width:100%;" align="center">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                    <thead>
                    	<tr>
                            <th width="5%">SL</th>
                            <th width="10%">Delivery ID</th>
                            <th width="10%">Delivery Date</th>
                            <th width="10%">Batch No</th>
                            <th width="15%">Order No</th>
                            <th width="15%">Category</th>
                            <th width="15%">Description</th>
                            <th width="10%">Delivery Qty</th>
                            <th width="10%">Process Loss</th>
                    	</tr>
                    </thead>
                </table>
            </div>  
            <div style="width:100%; max-height:230px; overflow-y:auto;" align="left">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                    <?
					$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
					$po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
					$color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
					$kniting_item_arr=return_library_array( "select id, const_comp from lib_subcon_charge",'id','const_comp');
					$dye_fin_item_arr=return_library_array( "select id, item_description from pro_batch_create_dtls",'id','item_description');
                    $i=0;
                    $sql= "SELECT a.delivery_prefix_num, a.delivery_date, a.party_id, b.order_id, b.process_id, b.item_id, b.color_id, b.batch_id, sum(b.delivery_qty) as quantity 
                    from  subcon_delivery_mst a, subcon_delivery_dtls b 
                    where a.id=b.mst_id and b.order_id in ($expData[0]) and b.process_id='$expData[1]' and a.status_active=1 and a.is_deleted=0 
                    group by a.delivery_prefix_num, a.delivery_date, a.party_id, b.order_id, b.process_id, b.batch_id, b.item_id, b.color_id 
                    order by a.delivery_prefix_num, a.delivery_date";
                    //echo $sql;
					$production_sql= sql_select($sql); $color_array=array(); $k=1; $process_id=0;
					foreach( $production_sql as $row )
                    {
                        $i++;
                        if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						$process_id=$row[csf("process_id")];
						if ($row[csf("process_id")]==1 || $row[csf("process_id")]==5)
						{
							$item_name=$garments_item[$row[csf('item_id')]];
						}
						else if ($row[csf("process_id")]==2)
						{
							$item_name=$kniting_item_arr[$row[csf('item_id')]];
						}
						else if ($row[csf("process_id")]==3 || $row[csf("process_id")]==4)
						{
							$item_name=$dye_fin_item_arr[$row[csf('item_id')]];
						}
						
						if ($row[csf("process_id")]==2)
						{
							if (!in_array($row[csf("color_id")],$color_array) )
							{
								if($k!=1)
								{
								?>
									<tr class="tbl_bottom">
										<td width="80%" colspan="7" align="right"><b>Color Total:</b></td>
										<td width="10%" align="right"><? echo number_format($color_qty); ?></td>
										<td width="10%" align="right"><? echo number_format($color_qty); ?></td>
									</tr>
									<tr bgcolor="#dddddd">
										<td width="100%" colspan="9" align="left" ><b>Color : <? echo $color_arr[$row[csf("color_id")]]; ?></b></td>
									</tr>
								<?
									unset($color_qty);
								}
								else
								{
									?>
									<tr bgcolor="#dddddd">
										<td colspan="9" width="100%" align="left" ><b>Color : <? echo $color_arr[$row[csf("color_id")]]; ?></b></td>
									</tr>
									<?
								}					
								$color_array[]=$row[csf('color_id')];            
								$k++;
							}							
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="5%"><? echo $i; ?></td>
								<td width="10%"><? echo $row[csf("delivery_prefix_num")];?> </td>
								<td width="10%"><? echo change_date_format($row[csf("delivery_date")]);?> </td> 
								<td width="10%"><p><? echo $batch_arr[$row[csf("batch_id")]]; ?></p></td>
								<td width="15%"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
								<td align="center" width="15%"><? echo $production_process[$row[csf("process_id")]]; ?></td>
								<td align="center" width="15%"><p><? echo $item_name; ?></p></td>
								<td align="right" width="10%"><? echo number_format($row[csf("quantity")],2); ?></td>
								<td align="right" width="10%"><? //echo number_format($row[csf("quantity")],2); ?></td>
							</tr>
							<? 
							$color_qty+=$row[csf("quantity")];
							// $tot_qty+=$row[csf("quantity")];
						}
						else
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="5%"><? echo $i; ?></td>
								<td width="10%"><? echo $row[csf("delivery_prefix_num")];?> </td>
								<td width="10%"><? echo change_date_format($row[csf("delivery_date")]);?> </td> 
								<td width="10%"><p><? echo $batch_arr[$row[csf("batch_id")]]; ?></p></td>
								<td width="15%"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
								<td align="center" width="15%"><? echo $production_process[$row[csf("process_id")]]; ?></td>
								<td align="center" width="15%"><p><? echo $item_name; ?></p></td>
								<td align="right" width="10%"><? echo number_format($row[csf("quantity")],2); ?></td>
								<td align="right" width="10%"><? //echo number_format($row[csf("quantity")],2); ?></td>
							</tr>
							<? 
							$tot_qty+=$row[csf("quantity")];
						}
					} 
					if($process_id==2)
					{
					?>
                        <tr class="tbl_bottom">
                            <td width="80%" colspan="7" align="right"><b>Color Total:</b></td>
                            <td width="10%" align="right"><? echo number_format($color_qty); ?></td>
                            <td width="10%" align="right"><? echo number_format($color_qty); ?></td>
                        </tr>
                    <?
					}
					?>
                    <tr class="tbl_bottom">
                    	<td width="80%" colspan="7" align="right">Total: </td>
                        <td width="10%" align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                        <td width="10%" align="right"><p><? //echo number_format($tot_qty,2); ?></p></td>
                    </tr>
                </table>
            </div> 
	</fieldset>
 </div> 
	<?
	exit();
}
?>