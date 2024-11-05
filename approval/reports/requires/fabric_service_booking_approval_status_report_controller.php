<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$menu_id=$_SESSION['menu_id'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}



if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name = str_replace("'","",$cbo_company_name);
	$txt_job_no = str_replace("'","",$txt_job_no);
	$txt_fabric_service_booking_no = str_replace("'","",$txt_fabric_service_booking_no);
	$cbo_booking_type = str_replace("'","",$cbo_booking_type);
	$txt_booking_date_from = str_replace("'","",$txt_booking_date_from);
	$txt_booking_date_to = str_replace("'","",$txt_booking_date_to);
	$txt_approval_date = str_replace("'","",$txt_approval_date);
	$cbo_year = str_replace("'","",$cbo_year_selection);
	$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
	$cbo_approve_type = str_replace("'","",$cbo_approve_type);


	$booking_date_cond="";
	if ($txt_booking_date_from != "" && $txt_booking_date_to != "")
	{
		$txt_booking_date_from = date("d-M-Y", strtotime(str_replace("'", "",  $txt_booking_date_from)));
		$txt_booking_date_to = date("d-M-Y", strtotime(str_replace("'", "",  $txt_booking_date_to)));
		$booking_date_cond=" and a.booking_date between '$txt_booking_date_from' and '$txt_booking_date_to'";
	}

	$company_cond=$buyer_cond=$job_cond=$booking_cond="";
	if ($cbo_company_name > 0) $company_cond=" and a.company_id=$cbo_company_name";
	if ($cbo_buyer_name > 0) $buyer_cond=" and a.buyer_id=$cbo_buyer_name";
	if ($txt_job_no != "") $job_cond=" and b.job_no like '%$txt_job_no'";
	if ($txt_fabric_service_booking_no != "") $booking_cond=" and b.job_no like '%$txt_fabric_service_booking_no'";

	$approved_date_cond="";
	if ($txt_approval_date != "" )
	{
		$txt_approval_date = date("d-M-Y", strtotime(str_replace("'", "",  $txt_approval_date)));		
		$approved_date_cond=" and c.approved_date like '$txt_booking_date_from%'";
	}
	$booking_year_cond="";
	if ($cbo_year > 0) $booking_year_cond=" and a.booking_year=$cbo_year";	
	
	$dealing_merchant_array = return_library_array("SELECT id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$designation_array = return_library_array( "SELECT id, custom_designation from lib_designation", "id", "custom_designation" );
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

	$service_booking_print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$cbo_company_name."' and module_id=2 and report_id=11 and is_deleted=0 and status_active=1");	
    $item_format_ids=explode(",",$service_booking_print_report_format);
    $print_btn=$item_format_ids[0];
	
	$user_name_array = array();
	$userData = sql_select( "SELECT id, user_name, user_full_name, designation from user_passwd");
	foreach($userData as $user_row)
	{
		$user_name_array[$user_row[csf('id')]]['name']=$user_row[csf('user_name')];
		$user_name_array[$user_row[csf('id')]]['full_name']=$user_row[csf('user_full_name')];
		$user_name_array[$user_row[csf('id')]]['designation']=$designation_array[$user_row[csf('designation')]];	
	}
		
	$approved_no_array = array();
	$queryApp = "SELECT mst_id, approved_by, approved_no from approval_history where entry_form=65 and un_approved_by=0";
	$resultApp = sql_select($queryApp);
	foreach ($resultApp as $row)
	{
		$approved_no_array[$row[csf('mst_id')]][$row[csf('approved_by')]]=$row[csf('approved_no')];
	}

	$signatory_data_arr = sql_select("SELECT user_id, sequence_no, bypass from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 and page_id=2658 order by sequence_no");
	

	foreach($signatory_data_arr as $sval)
	{
		$signatory_main[$sval[csf('user_id')]]=$sval[csf('bypass')];
		$userArr[$sval[csf('user_id')]]=$sval[csf('user_id')];
	}

	$user_approval_array=array(); $user_ip_array=array(); $max_approval_date_array=array();
	$query = "SELECT mst_id, approved_no, approved_by, approved_date, user_ip, entry_form from approval_history where entry_form=65 ".where_con_using_array($userArr,1,'approved_by')." and un_approved_by=0";
	$result = sql_select($query);
	foreach($result as $row)
	{
		$user_approval_array[$row[csf('mst_id')]][$row[csf('approved_by')]] = $row[csf('approved_date')];
		$approved_date = date("Y-m-d",strtotime($row[csf('approved_date')]));
		$user_approval_mst_count[$row[csf('mst_id')]][$row[csf('approved_by')]]=$row[csf('approved_by')];
		
		if($max_approval_date_array[$row[csf('mst_id')]]=="")
		{
			$max_approval_date_array[$row[csf('mst_id')]]=$approved_date;
		}
		else
		{
			if($approved_date>$max_approval_date_array[$row[csf('mst_id')]])
			{
				$max_approval_date_array[$row[csf('mst_id')]]=$approved_date;
			}
		}
	}

	//echo '<pre>';print_r($user_approval_array);

	if ($cbo_approve_type == 1)
	{
		$sql="SELECT a.id, a.company_id, a.booking_no, a.buyer_id, a.booking_no_prefix_num, a.booking_type, a.is_approved, a.insert_date, a.booking_date, a.inserted_by, a.supplier_id
		from wo_booking_mst a, wo_booking_dtls b
		where a.booking_no=b.booking_no and a.booking_type=3 and a.item_category=12 and a.entry_form=176 and a.is_approved in(0,2) and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 $booking_date_cond $company_cond $buyer_cond $job_cond $booking_cond $booking_year_cond $approved_date_cond
		group by a.id, a.company_id, a.booking_no, a.buyer_id, a.booking_no_prefix_num, a.booking_type, a.is_approved, a.insert_date, a.booking_date, a.inserted_by, a.supplier_id
		order by a.booking_date desc";
	}
	else if ($cbo_approve_type==3)
	{
		$sql="SELECT a.id, a.company_id, a.booking_no, a.buyer_id, a.booking_no_prefix_num, a.booking_type, a.is_approved, a.insert_date, a.booking_date, a.inserted_by, a.supplier_id
		from wo_booking_mst a, wo_booking_dtls b, approval_history c
		where a.booking_no=b.booking_no and a.id=c.mst_id and a.booking_type=3 and a.item_category=12 and a.entry_form=176 and a.is_approved in(1,3) and a.ready_to_approved=1 and c.entry_form=65 and c.current_approval_status=1 and a.status_active=1 and a.is_deleted=0 $booking_date_cond $company_cond $buyer_cond $job_cond $booking_cond $booking_year_cond $approved_date_cond
		group by a.id, a.company_id, a.booking_no, a.buyer_id, a.booking_no_prefix_num, a.booking_type, a.is_approved, a.insert_date, a.booking_date, a.inserted_by, a.supplier_id
		order by a.booking_date desc";
	}
	//echo $sql;die;
	$sql_res=sql_select($sql);
	$fabric_service_booking_arr=array();
	foreach($sql_res as $row)
	{
		$fabric_service_booking_arr[$row[csf('booking_no')]]['booking_no']=$row[csf('booking_no')];
		$fabric_service_booking_arr[$row[csf('booking_no')]]['booking_id']=$row[csf('id')];
		$fabric_service_booking_arr[$row[csf('booking_no')]]['company_id']=$row[csf('company_id')];
		$fabric_service_booking_arr[$row[csf('booking_no')]]['buyer_id']=$row[csf('buyer_id')];
		$fabric_service_booking_arr[$row[csf('booking_no')]]['supplier_id']=$row[csf('supplier_id')];
		$fabric_service_booking_arr[$row[csf('booking_no')]]['id']=$row[csf('id')];
		$fabric_service_booking_arr[$row[csf('booking_no')]]['is_approved']=$row[csf('is_approved')];
		$booking_no.="'".$row[csf('booking_no')]."',";
	}
	$booking_nos=rtrim($booking_no,',');

	$sql_job="select a.booking_no as BOOKING_NO, a.job_no as JOB_NO, c.dealing_marchant as DEALING_MARCHANT, b.po_number as PO_NUMBER, b.pub_shipment_date as PUB_SHIPMENT_DATE from wo_booking_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.booking_no in($booking_nos)";
	$sql_job_res=sql_select($sql_job);
	$min_pub_shipment_date_arr=array();
	foreach($sql_job_res as $row)
	{
		$booking_array[$row['BOOKING_NO']]['job_no'].=$row['JOB_NO'].', ';
		$booking_array[$row['BOOKING_NO']]['dealing_marchant'].=$dealing_merchant_array[$row['DEALING_MARCHANT']].', ';
		$booking_array[$row['BOOKING_NO']]['po_number'].=$row['PO_NUMBER'].', ';

		$pub_shipment_date = date("Y-m-d",strtotime($row['PUB_SHIPMENT_DATE']));
		if($min_pub_shipment_date_arr[$row['BOOKING_NO']]=="")
		{
			$min_pub_shipment_date_arr[$row['BOOKING_NO']]=$pub_shipment_date;
		}
		else
		{
			if($pub_shipment_date<$min_pub_shipment_date_arr[$row['BOOKING_NO']])
			{
				$min_pub_shipment_date_arr[$row['BOOKING_NO']]=$pub_shipment_date;
			}
		}

		
	}

	//echo '<pre>';print_r($user_approval_array);
	$width=1300;
	ob_start();
	?>
	<fieldset style="width:<? echo $width; ?>px;">
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td align="center" width="100%" colspan="10" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="10" style="font-size:16px"><strong><? echo $company_library[$cbo_company_name]; ?></strong></td>
			</tr>
		</table>	
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width; ?>" class="rpt_table" align="left">
			<thead>
				<th width="40">SL</th>
				<th width="120">Booking No</th>               
				<th width="120">Buyer Name</th>
				<th width="100">Dealing Merchant</th>
				<th width="120">Order No</th>
				<th width="100">Shipment Date (Min)</th>
				<th width="120">Job No</th>
				<th width="140">Signatory</th>
				<th width="120">Designation</th>
				<th width="100">Approval Date</th>
				<th width="100">Approval Time</th>
				<th>Approve No</th>
			</thead>
		</table>
		<div style="width:<? echo $width+20; ?>px; overflow-y:scroll; max-height:310px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width; ?>" class="rpt_table" id="tbl_list_search">
				<tbody>
					<?
					$i=1; //$signatory=explode(",",$signatory); $rowspan=count($signatory);
					$rowspanMain=count($signatory_main);						
					foreach ($fabric_service_booking_arr as $booking_no=>$row)
					{
						$full_approval='';
						$rowspan=$rowspanMain;						
						$full_approval=true; $approvedStatus="";
							
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";										
						$z=0; 
						foreach($signatory_main as $userid=>$val)
						{
							
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<?
							if($z==0)
							{
								$dealing_marchant = implode(',', array_unique(explode(',', rtrim($booking_array[$booking_no]['dealing_marchant'],', '))));
								$job_no = implode(',', array_unique(explode(',', rtrim($booking_array[$booking_no]['job_no'],', '))));
								$po_number = implode(',', array_unique(explode(',', rtrim($booking_array[$booking_no]['po_number'],', '))));
								$pub_shipment_date =change_date_format($min_pub_shipment_date_arr[$booking_no]);
								?>
								<td width="40" rowspan="<? echo $rowspan; ?>"><? echo $i; ?></td>
								<td width="120" rowspan="<? echo $rowspan; ?>" align="center"> <a href="##" onClick="fabric_booking_req_report('<?echo $print_btn; ?>','<? echo $booking_no;?>','<? echo $row['company_id'];?>','<? echo $row['supplier_id'];?>','<? echo $row['id'];?>')"> <? echo $booking_no; ?></a></td>
								<td width="120" rowspan="<? echo $rowspan; ?>"><p><? echo $buyer_arr[$row['buyer_id']]; ?>&nbsp;</p></td>
								<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $dealing_marchant; ?>&nbsp;</p></td>
								<td width="120" rowspan="<? echo $rowspan; ?>"><p><? echo $po_number; ?>&nbsp;</p></td>
								<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $pub_shipment_date; ?>&nbsp;</p></td>
								<td width="120" rowspan="<? echo $rowspan; ?>"><p><? echo $job_no; ?>&nbsp;</p></td>
								<?
							}
							
							$approved_no='';							
							$approval_date=$user_approval_array[$row['booking_id']][$userid];
							if($approval_date!="") $approved_no=$approved_no_array[$row['booking_id']][$userid];					
							//echo$approval_date.'system';
							$date=''; $time=''; 
							if($approval_date!="") 
							{
								$date=date("d-M-Y",strtotime($approval_date)); 
								$time=date("h:i:s A",strtotime($approval_date)); 
							}
							//echo $date;
							?>
								<td width="140"><p><? echo $user_name_array[$userid]['full_name']." (".$user_name_array[$userid]['name'].")"; ?>&nbsp;</p></td>
								<td width="120"><p><? echo $user_name_array[$userid]['designation']; ?>&nbsp;</p></td>			
								<td width="100" align="center"><p><? if($row['is_approved']!=0) echo change_date_format($date); ?>&nbsp;</p></td>
								<td width="100" align="center"><p><? if($row['is_approved']!=0) echo $time; ?>&nbsp;</p></td>
								<td><p>&nbsp;<? if($row['is_approved']!=0) echo $approved_no; ?>&nbsp;</p></td>
							</tr>
							<?
							$z++;
						}
						$i++;						
					}
					?>
				</tbody>
			</table>
		</div>
	</fieldset>
	<?	
	foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}

	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename="requires/".$user_name."_".$name.".xls";
	echo "$total_data####$filename";
	exit(); 	
}


?>