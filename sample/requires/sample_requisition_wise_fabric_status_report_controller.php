<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_name=$_SESSION['logic_erp']['user_id'];
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
$company_arr=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0",'id','company_name');
$dealing_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0",'id','team_member_name');
$sample_name_arr=return_library_array( "select id,sample_name  from lib_sample where is_deleted=0 and status_active=1 order by sample_name", "id", "sample_name"  );
$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
$size_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0","id","size_name");
$season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0","id","season_name");

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$req_no=str_replace("'", "", $txt_req_no);
	$cbo_sample_type=str_replace("'", "", $cbo_sample_type);
	$cbo_company_name=str_replace("'", "", $cbo_company_name);
	$sample_year=str_replace("'", "", $cbo_year);
	$year_cond="";
	if($db_type==2)
	{
		$year_cond=($sample_year)? " and  to_char(a.insert_date,'YYYY')=$sample_year" : " ";
	}
	else
	{
		$year_cond=($sample_year)? " and year(a.insert_date)=$sample_year" : " ";
	}

	if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and a.company_id=$cbo_company_name";
	if(str_replace("'","",$cbo_buyer_name)==0) $buyer_name="";else $buyer_name=" and a.buyer_name=$cbo_buyer_name";
	if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="")
		$txt_date="";
	else
		$txt_date=" and b.delivery_date between $txt_date_from and $txt_date_to";

	if(str_replace("'","",$txt_req_no)=="") $req_no=""; else $req_no=" and a.requisition_number_prefix_num like '%$req_no%' ";
	
	if(str_replace("'","",$cbo_sample_type)==0) $sample_type="";else $sample_type=" and b.sample_name=$cbo_sample_type";

	$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
	$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
	$style=str_replace("'", "", $txt_style_ref);
	if($style=='') $style_ref="";else $style_ref=" and a.style_ref_no like '%$style%'";

	$booking_without_order_sql=sql_select("SELECT b.style_id,a.booking_no
		from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b
		where a.booking_no=b.booking_no and a.status_active=1 and b.status_active=1  group by  b.style_id,a.booking_no");
	foreach($booking_without_order_sql as $vals)
	{
		$booking_without_order_arr[$vals[csf("style_id")]]=$vals[csf("booking_no")];
	}
	ob_start();
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	?>
	<script type="text/javascript">setFilterGrid('table_body',-1);</script>
	<div>
        <table cellpadding="0" cellspacing="0" width="1850">
            <tr  class="form_caption" style="border:none;">
           		 <td align="center" width="100%" colspan="18" style="font-size:20px"><strong><? echo 'Sample Requisition Wise Fabric Status Report '; ?></strong></td>
            </tr>
            <tr  class="form_caption" style="border:none;">
                <td colspan="18" align="center" style="border:none; font-size:14px;">
                <b><? echo $company_library[$cbo_company_name]; ?></b>
                </td>
            </tr>
            <tr  class="form_caption" style="border:none;">
                <td align="center" width="100%" colspan="18" style="font-size:12px">
                <? if(str_replace("'","",$fromDate)!="" && str_replace("'","",$toDate)!="") echo "From ".change_date_format(str_replace("'","",$fromDate),'dd-mm-yyyy')." To ".change_date_format(str_replace("'","",$toDate),'dd-mm-yyyy')."" ;?>
                </td>
            </tr>
        </table>
		<table cellspacing="0" cellpadding="0" border="1" class="rpt_table" width="1850" rules="all" id="table_header" >
			<thead>
				<tr>
					<th width="30">Sl No</th>
					<th width="110">Merchant Name</th>
					<th width="100">Buyer Name</th>
					<th width="90">Style Name</th>
					<th width="95">Requisition No</th>
					<th width="90">Booking No</th>
                    <th width="90">Booking Date</th>
					<th width="110"> Sample Type</th>
					<th width="70">Season</th>
					<th width="105">Fabric Delivery Date(Req)</th>
					<th width="80">GMT. Fabric Color / Code</th>
					<th width="260">Fabric Type</th>
					<th width="80">Fabric Receive date</th>
					<th width="80">Fabric Rev Qty</th>
					<th width="80">Program Lead Time (PLT)</th>
					<th width="80">Fabric Received Early</th>
					<th width="80">Fabric Received Delay</th>
					<th width="80">Total Take time</th>
					<th>Remaks</th>
				</tr>
			</thead>
		</table>
		<div style="max-height:320px; overflow-y:scroll; width:1868px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="1850" rules="all" id="table_body">
				<tbody>
					<?

					if($db_type==0) $yearCond="YEAR(a.insert_date)"; else if($db_type==2) $yearCond="to_char(a.insert_date,'YYYY')";

					$query="select a.id, a.dealing_marchant, a.requisition_number_prefix_num,to_char(a.insert_date,'YYYY') as year,
					a.buyer_name, a.style_ref_no, a.season, b.sample_name, c.color_id as sample_color, d.lib_yarn_count_deter_id,b.delivery_date,(b.delivery_date-e.booking_date) as  lead_date_diff,b.fabric_description,e.booking_date,b.remarks_ra,e.id booking_id,e.booking_no,e.booking_date
					from sample_development_mst a,sample_development_fabric_acc b,sample_development_rf_color c,wo_non_ord_samp_booking_dtls d,wo_non_ord_samp_booking_mst e
					where a.entry_form_id in(203)  and a.id=b.sample_mst_id and b.id=c.dtls_id and b.id=d.dtls_id and a.id=d.style_id and d.booking_no=e.booking_no
					and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 $txt_date $company_name $buyer_name $sample_stages $dealing_merchant $req_no  $style_ref  $year_cond $sample_type group by  a.id, a.dealing_marchant, a.requisition_number_prefix_num,a.insert_date,
					a.buyer_name, a.style_ref_no, a.season, b.sample_name,c.color_id, d.lib_yarn_count_deter_id,b.delivery_date,b.fabric_description,e.booking_date,b.remarks_ra,e.id,e.booking_no
					order by  e.id,b.delivery_date asc, c.color_id,b.fabric_description";

//echo $query;die;
					$sql=sql_select($query);
					

					$book_count_arr=array();
					$batch_id_arr=array();
					foreach($sql as $row)
					{
						$booking_id_arr[$row[csf('booking_id')]]  = $row[csf('booking_id')];
						$book_count_arr[$row[csf('booking_id')]][] = $row[csf('sample_name')];
						$color_date_key=$row[csf('delivery_date')]."_".$row[csf('sample_color')];
						$color_date_count_arr[$row[csf('booking_id')]][$color_date_key][] =$color_date_key;
						
					}

					if(!empty($booking_id_arr)){
						$batch_sql = sql_select("select id,booking_no from pro_batch_create_mst where booking_no_id in(".implode(",", $booking_id_arr).")");
						foreach ($batch_sql as $batch_data) {
							$batch_id_arr[$batch_data[csf("id")]] = $batch_data[csf("id")];
							$batch_booking_arr[$batch_data[csf("id")]] = $batch_data[csf("booking_no")];
						}
					}
					if(!empty($batch_id_arr)){
						$rcv_sql = "SELECT c.id batch_id, a.receive_date, b.fabric_description_id, b.color_id, sum(b.receive_qnty) recv_qnty
						from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c
						where a.id = b.mst_id  and b.batch_id = c.id and b.trans_id >0 and a.entry_form = 37 and a.status_active =1 and b.status_active =1
						and c.status_active =1 and c.id in(".implode(",",$batch_id_arr).")
						group by c.id,c.booking_no_id, a.receive_date, b.fabric_description_id, b.color_id
						union all
						select d.id batch_id,a.transfer_date receive_date, b.feb_description_id,b.color_id,sum(b.transfer_qnty) recv_qnty
						from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c , pro_batch_create_mst d
						where a.id=b.mst_id and b.to_trans_id=c.id and c.pi_wo_batch_no=d.id
						and c.item_category=2 and c.transaction_type=5 and a.status_active=1 and b.status_active=1 and c.status_active=1  and a.entry_form in (14,15,306) and d.id in(".implode(",",$batch_id_arr).")
						group by d.id,a.transfer_date, b.feb_description_id,b.color_id";
						$recv_data = sql_select($rcv_sql);

						$recv_arr = array();
						if(!empty($recv_data))
						{
							foreach ($recv_data as $recv_row) {
								$booking_no = $batch_booking_arr[$recv_row[csf("batch_id")]];
								$recv_arr[$booking_no][$recv_row[csf("fabric_description_id")]][$recv_row[csf("color_id")]]["recv_qnty"] += $recv_row[csf("recv_qnty")];
								$recv_arr[$booking_no][$recv_row[csf("fabric_description_id")]][$recv_row[csf("color_id")]]["receive_date"] = $recv_row[csf("receive_date")];
							}
						}
					}

					$i=0; 
					$books_ar=array();
					$j=1;
					$book_rowspan="";
					$receive_qty=0;
					foreach ($sql as $key => $value)
					{

					   	$book_rowspan = count($book_count_arr[$value[csf('booking_id')]]);
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$booking_no = $value[csf("booking_no")];
						$date_diff_1=$value[csf('lead_date_diff')];//datediff( "d", $value[csf('booking_date')] , $value[csf('delivery_date')])-1;
						$date_diff_2=datediff( "d", $recv_arr[$booking_no][$value[csf("lib_yarn_count_deter_id")]][$value[csf("sample_color")]]["receive_date"] , $value[csf('delivery_date')])-1;
						$date_diff_3=datediff( "d", $value[csf('delivery_date')] , $recv_arr[$booking_no][$value[csf("lib_yarn_count_deter_id")]][$value[csf("sample_color")]]["receive_date"])-1;
						
						
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
							<?  if(!in_array($value[csf('booking_id')], $book_check_arr))
							{
									$i++;
									foreach($book_count_arr[$row[csf('booking_id')]] as $key=>$val)
									{
										$sample_names[$val]=$sample_name_arr[$val];
									}
									?>
									<td width="30"  align="center"  rowspan="<? echo $book_rowspan; ?>"><? echo $i; ?></td>
									<td width="110" style="word-break:break-all"  rowspan="<? echo $book_rowspan; ?>"><? echo $dealing_arr[$value[csf('dealing_marchant')]]; ?></td>
									<td width="100" style="word-break:break-all"  rowspan="<? echo $book_rowspan; ?>"><? echo $buyer_arr[$value[csf('buyer_name')]]; ?></td>
									<td width="90" style="word-break:break-all"  rowspan="<? echo $book_rowspan; ?>"><? echo  $value[csf('style_ref_no')] ; ?></td>
									<td width="95" align="center"  rowspan="<? echo $book_rowspan; ?>"><? echo  $value[csf('requisition_number_prefix_num')]; ?></td>
									<td width="90" style="word-break:break-all" rowspan="<? echo $book_rowspan; ?>">
										<? echo $booking_no; ?>
									</td>
                                    <td width="90" style="word-break:break-all" rowspan="<? echo $book_rowspan; ?>">
										<? echo change_date_format($value[csf('booking_date')]); ?>
									</td>
									<td width="110" style="word-break:break-all"  rowspan="<? echo $book_rowspan; ?>"><? echo implode(",",$sample_names); ?></td>
									<td width="70" style="word-break:break-all"  rowspan="<? echo $book_rowspan; ?>"><? echo $season_arr[$value[csf('season')]] ; ?></td>
									<?
									$book_check_arr[] = $value[csf('booking_id')];
								}
								$color_date_index=$value[csf('delivery_date')]."_".$value[csf('sample_color')];
								 
								if(!in_array($color_date_index ,$color_data_check_arr[$value[csf('booking_id')]]))
								{
								?>
								<td width="105" style="word-break:break-all" rowspan="<?php echo count($color_date_count_arr[$value[csf('booking_id')]][$color_date_index]); ?>"><?  
									echo  change_date_format($value[csf('delivery_date')]) ; ?></td>
								<td width="80" style="word-break:break-all" align="center" rowspan="<?php echo count($color_date_count_arr[$value[csf('booking_id')]][$color_date_index]); ?>" ><? echo $color_arr[$value[csf('sample_color')]]; ?></td>
                                <? 
									$color_data_check_arr[$value[csf('booking_id')]][] = $color_date_index;
								}
								?>
								<td width="260" style="word-break:break-all"><? echo  $value[csf('fabric_description')] ; ?></td>
								<td width="80" style="word-break:break-all"  align="center" title="<? echo $value[csf("batch_id")]."sd".$value[csf("lib_yarn_count_deter_id")]."sd".$value[csf("sample_color")]?>"><? echo change_date_format($recv_arr[$booking_no][$value[csf("lib_yarn_count_deter_id")]][$value[csf("sample_color")]]["receive_date"]) ; ?></td>
								<td width="80" style="word-break:break-all" align="right"><? echo $recv_arr[$booking_no][$value[csf("lib_yarn_count_deter_id")]][$value[csf("sample_color")]]["recv_qnty"] ; $receive_qty+=$recv_arr[$booking_no][$value[csf("lib_yarn_count_deter_id")]][$value[csf("sample_color")]]["recv_qnty"] ; ?></td>
								<td width="80" style="word-break:break-all" align="center"><?
								
										if($date_diff_1>0)
										{
											echo $date_diff_1=$date_diff_1; 
										}
										else
										{ 
											echo $date_diff_1="0";
										}
								// echo  $date_diff_1; ?></td>
								<td width="80" style="word-break:break-all" align="center">
								<? 
										
									if(!empty($recv_arr[$booking_no][$value[csf("lib_yarn_count_deter_id")]][$value[csf("sample_color")]]["receive_date"]))
									{ 
										if($date_diff_2>0)
										{
											echo $date_diff_2=$date_diff_2; 
										}
										else
										{ 
											echo $date_diff_2="0";
										}
									}
								?></td>
								<td width="80" style="word-break:break-all" align="center">
								<? 
									if(!empty($recv_arr[$booking_no][$value[csf("lib_yarn_count_deter_id")]][$value[csf("sample_color")]]["receive_date"]))
									{
										if($date_diff_3>0)
										{
											echo $date_diff_3=$date_diff_3; 
										}
										else
										{ 
											echo $date_diff_3="0";
										}
									} 
								?></td>
								<td width="80" style="word-break:break-all" align="center" title="<? echo $recv_arr[$booking_no][$value[csf("lib_yarn_count_deter_id")]][$value[csf("sample_color")]]["receive_date"]; ?> ">
								<? 
								if(!empty($recv_arr[$booking_no][$value[csf("lib_yarn_count_deter_id")]][$value[csf("sample_color")]]["receive_date"]))
								{
									 echo $date_diff_3+$date_diff_1; 
								}
								else
								{
									echo $date_diff_1;
								}
								?></td>
								<td style="word-break:break-all"><? echo $value[csf('remarks_ra')] ; ?></td>
							</tr>
							<?
								
						}
						?>
					</tbody>
				</table>
                 <table width="1850" border="1" cellpadding="0" cellspacing="0" rules="all"> 
                 <tr class="tbl_bottom">
					<td width="30">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="90">&nbsp;</td>
					<td width="95">&nbsp;</td>
					<td width="90">&nbsp;</td>
                    <td width="90">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="105">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="260">&nbsp;</td>
					<td width="80">Total</td>
					<td width="80" align="right"><? echo $receive_qty ; ?></td>
					<td width="80">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td>&nbsp;</td>
                    </tr>
				</table>
			</div>
		</div>
		<?
	foreach (glob("$user_name*.xls") as $filename) {
	if (@filemtime($filename) < (time() - $seconds_old))
		@unlink($filename);
	}
	//---------end------------//
	$name = time();
	$filename = $user_name . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, ob_get_contents());
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	}

	?>