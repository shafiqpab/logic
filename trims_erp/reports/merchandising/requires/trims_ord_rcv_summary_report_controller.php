<?

header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------


if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);

	if($data[1]==1)
	{
		echo create_drop_down( "cbo_customer_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- select Company --",'', "");
	}
	else
	{
		echo create_drop_down( "cbo_customer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- select Party --", '', "" );
	}	
	exit();	 
} 





if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//cbo_company_id*cbo_supplier_id*txt_wo_no*cbo_section*txt_date_from*txt_date_to
	$company_name=str_replace("'","",$cbo_company_id);
	$within_group=str_replace("'","",$cbo_customer_source);
	$customer_name=str_replace("'","",$cbo_customer_name);
	$section_id=str_replace("'","",$cbo_section);
	$cbo_source_id=str_replace("'","",$cbo_source_id);
	$txt_wo_rcv_no=str_replace("'","",$txt_wo_rcv_no);
	$txt_wo_no=str_replace("'","",$txt_wo_no);
	$txt_cust_style=str_replace("'","",$txt_cust_style);
	$txt_buyer=str_replace("'","",$txt_buyer);
	$cbo_Status_type=str_replace("'","",$cbo_Status_type);

	$date_category=str_replace("'","",$cbo_date_category);
	$from_date=str_replace("'","",$txt_date_from);
	$to_date=str_replace("'","",$txt_date_to);
	
	if($section_id==0 || $section_id=='') $sql_cond.=""; else $sql_cond.=" and b.section=$section_id";
	if($company_name==0 || $company_name=='') $sql_cond.=""; else $sql_cond.=" and a.company_id=$company_name";
	if($customer_name==0 || $customer_name=='') $sql_cond.=""; else $sql_cond.=" and a.party_id=$customer_name";
	if($within_group==0 || $within_group=='') $sql_cond.=""; else $sql_cond.=" and a.within_group=$within_group";
	if($cbo_source_id==0 || $cbo_source_id=='') $sql_cond.=""; else $sql_cond.=" and b.source_for_order=$cbo_source_id";
	//if($cbo_supplier_id==0 || $cbo_supplier_id=='') $sql_cond.=""; else $sql_cond.=" and d.supplier_id=$cbo_supplier_id";
	if($txt_wo_rcv_no=='') $sql_cond.=""; else $sql_cond.=" and a.subcon_job like '%$txt_wo_rcv_no%'";
	if($txt_wo_no=='') $sql_cond.=""; else $sql_cond.=" and a.order_no like '%$txt_wo_no%'";
	if($txt_cust_style=='') $sql_cond.=""; else $sql_cond.=" and b.buyer_style_ref like '%$txt_cust_style%'";
	if($txt_buyer=='') $sql_cond.=""; else $sql_cond.=" and b.buyer_buyer like '%$txt_buyer%'";
	if($cbo_Status_type=='') $sql_cond.=""; else $sql_cond.=" and a.status like '%$cbo_Status_type%'";
	if($date_category==1) $date_category_cond=" a.receive_date "; else $date_category_cond=" a.delivery_date "; 
	//--------------------------------------------------start	
	
	
	//-------------------------------end
	if($from_date!='' && $to_date!='')
	{
		if($db_type==0) 
		{
			$start_date=change_date_format($from_date,'yyyy-mm-dd');
			$end_date=change_date_format($to_date,'yyyy-mm-dd');
			//( year_id=2019 and month_id>=1)  or  ( year_id=2020 and month_id<=1) or  ( year_id=2021 and month_id<=1) 
			$sql_cond.=" and $date_category_cond between '$start_date' and '$end_date'";
		}
		if($db_type==2) 
		{
			$start_date=change_date_format($from_date,'','',1);
			$end_date=change_date_format($to_date,'','',1);
			$sql_cond.=" and $date_category_cond between '".date("j-M-Y",strtotime($start_date))."' and '".date("j-M-Y",strtotime($end_date))."'";
		}
	}
	
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$source_order = array(1 => 'In-House', 2 => 'Sub-Contract');
	$leader_name_arr=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');
	$member_name_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$trim_group_arr=return_library_array( "select id, item_name from lib_item_group where item_category=4",'id','item_name');
	$user_arr=return_library_array( "select id, user_name from user_passwd ",'id','user_name');
    $subcon_sql ="SELECT a.id,a.within_group, a.subcon_job, a.company_id, a.location_id, a.party_id, a.receive_date,a.delivery_date,a.team_leader,a.team_member,a.currency_id,a.remarks, b.order_no,  b.id as subDtlsID,b.booking_dtls_id, b.order_id, b.booked_qty, b.booked_conv_fac, b.order_quantity , b.rate , b.amount , b.rate_domestic, b.amount_domestic,b.buyer_po_id,b.order_uom,b.buyer_po_no,b.buyer_style_ref,b.buyer_buyer,b.section,b.sub_section,b.item_group, b.booked_uom,b.source_for_order, a.exchange_rate, a.inserted_by ,a.status from subcon_ord_mst a, subcon_ord_dtls b where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 $sql_cond  order by a.id ASC";

	// echo $subcon_sql;
	$qry_result=sql_select($subcon_sql);

	foreach ($qry_result as  $row)
	{
		//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
		$wo_arr[$row[csf("id")]][$row[csf("subDtlsID")]]["subcon_job"] 		=$row[csf("subcon_job")];
		$wo_arr[$row[csf("id")]][$row[csf("subDtlsID")]]["within_group"] 	=$row[csf("within_group")];
		$wo_arr[$row[csf("id")]][$row[csf("subDtlsID")]]["receive_date"] 	=$row[csf("receive_date")];
		$wo_arr[$row[csf("id")]][$row[csf("subDtlsID")]]["order_no"] 		=$row[csf("order_no")];
		$wo_arr[$row[csf("id")]][$row[csf("subDtlsID")]]["party_id"] 		=$row[csf("party_id")];
		$wo_arr[$row[csf("id")]][$row[csf("subDtlsID")]]["currency_id"] 	=$row[csf("currency_id")];
		$wo_arr[$row[csf("id")]][$row[csf("subDtlsID")]]["subDtlsID"] 		=$row[csf("subDtlsID")];
		$wo_arr[$row[csf("id")]][$row[csf("subDtlsID")]]["section"] 		=$row[csf("section")];
		$wo_arr[$row[csf("id")]][$row[csf("subDtlsID")]]["sub_section"] 	=$row[csf("sub_section")];
		$wo_arr[$row[csf("id")]][$row[csf("subDtlsID")]]["item_group"] 		=$row[csf("item_group")];
		$wo_arr[$row[csf("id")]][$row[csf("subDtlsID")]]["booked_uom"] 		=$row[csf("booked_uom")];
		$wo_arr[$row[csf("id")]][$row[csf("subDtlsID")]]["order_quantity"] 	=$row[csf("order_quantity")];
		$wo_arr[$row[csf("id")]][$row[csf("subDtlsID")]]["rate"] 			=$row[csf("rate")];
		$wo_arr[$row[csf("id")]][$row[csf("subDtlsID")]]["amount"] 			=$row[csf("amount")];
		$wo_arr[$row[csf("id")]][$row[csf("subDtlsID")]]["source_for_order"] =$row[csf("source_for_order")];
		$wo_arr[$row[csf("id")]][$row[csf("subDtlsID")]]["delivery_date"] 	=$row[csf("delivery_date")];
		$wo_arr[$row[csf("id")]][$row[csf("subDtlsID")]]["team_leader"] 	=$row[csf("team_leader")];
		$wo_arr[$row[csf("id")]][$row[csf("subDtlsID")]]["team_member"] 	=$row[csf("team_member")];
		$wo_arr[$row[csf("id")]][$row[csf("subDtlsID")]]["inserted_by"] 	=$row[csf("inserted_by")];
		$wo_arr[$row[csf("id")]][$row[csf("subDtlsID")]]["remarks"] 		=$row[csf("remarks")];
		$wo_style_arr[$row[csf("id")]]["buyer_style_ref"] 					.=$row[csf("buyer_style_ref")].' , ';
		$wo_style_arr[$row[csf("id")]]["buyer_buyer"] 						.=$row[csf("buyer_buyer")].' , ';
		
		//$wo_arr[$row[csf("wo_id")]][$row[csf("wo_date")]][$row[csf("supplier_id")]][$row[csf("section")]][$row[csf("sub_section")]][$row[csf("trim_group")]][$row[csf("order_uom")]]["wo_brk_id"] .=$row[csf("id")].',';
	}

	//echo "<pre>";
	//print_r($wo_arr); die;
	//die;

	$section_rowspan_arr=array(); $sub_section_rowspan_arr=array(); $trim_group_rowspan_arr=array(); $wo_id_rowspan_arr=array(); $within_group_rowspan_arr=array(); $supplier_id_rowspan_arr=array();
    foreach($wo_arr as $wo_id=> $wo_id_data)
	{
		$wo_id_rowspan=0;
		foreach($wo_id_data as $subDtlsID=> $row)
		{
			$wo_id_rowspan++;
		}
		$wo_id_rowspan_arr[$wo_id]=$wo_id_rowspan;
	}

	if($com_id) $com_cond=" and company_id=$com_id";
	$lib_conversion_rate=sql_select("select conversion_rate from currency_conversion_rate where status_active=1 $com_cond and currency=$currency_ids and CON_DATE=(select max(CON_DATE) from currency_conversion_rate where status_active=1 and currency=$currency_ids $com_cond)");
	$cu_conversion_rate=$lib_conversion_rate[0][csf("conversion_rate")];

	if($db_type==0){
		$lib_currency_data=sql_select("select conversion_rate from currency_conversion_rate where  company_id=$company_name and currency=2 order by id desc limit 1");
	}else{
		$lib_currency_data=sql_select("select conversion_rate from currency_conversion_rate where  company_id=$company_name and currency=2 and rownum<2 order by id desc");
	}
	$currency_conversion_rate=$lib_currency_data[0][csf("conversion_rate")];
//echo "<pre>";	
//print_r($wo_id_rowspan_arr);	die;
ob_start();
?>

<div style="width:2200px;">
	<table cellspacing="0" width="2200"   align="center" >
		<tr colspan="20" ><td style="font-size:xx-large; text-align:center;" align="center"><strong ><? echo $company_arr[$company_name]; ?></strong> </td></tr>
		<tr colspan="20" ><td style="font-size:large; text-align:center;" align="center"><strong >
			<?
			if($start_date!='' && $end_date!='') 
			{ 
				echo change_date_format($start_date). " To ". change_date_format($end_date);
			}
			?></strong> </td>
		</tr>
	</table>
	<br>
		<div align="left" style="height:auto; width:500px; margin:0 auto; padding:0;">

		<table width="500" cellpadding="0" cellspacing="0" id="caption" align="center">
				<thead class="form_caption" >
					<tr>
						<td  width="500" class="rpt_table"  style="font-size:24px; font-weight:bold" ><? echo "Summary"; ?></td>
					</tr>
				</thead>
			</table>
			<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="500" rules="all" id="rpt_table_header" align="center">
				<thead>
					<th width="35">SL</th>
					<th width="100">Section</th>
					<th width="100">Sub-Section</th>
				<th>Curr. Rec. Value ($)</th>
			</thead>
		</table>
		<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="500" rules="all" align="center">
			<?
		$sammary_sql="SELECT a.id,a.within_group, a.subcon_job, a.company_id, a.location_id, a.party_id, a.receive_date,a.delivery_date,a.team_leader,a.team_member,a.currency_id,a.remarks, b.order_no,  b.id as subDtlsID,b.booking_dtls_id, b.order_id, b.booked_qty, b.booked_conv_fac, b.order_quantity , b.rate , b.amount , b.rate_domestic, b.amount_domestic,b.buyer_po_id,b.order_uom,b.buyer_po_no,b.buyer_style_ref,b.buyer_buyer,b.section,b.sub_section,b.item_group, b.booked_uom,b.source_for_order, a.exchange_rate, a.inserted_by ,a.status from subcon_ord_mst a, subcon_ord_dtls b where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 $sql_cond  order by a.id ASC";
		// echo $sammary_sql;
		
		$sammary_result = sql_select($sammary_sql);
		$sammary_array=array();
		foreach($sammary_result as $row)
		{
				$sammary_array[$row[csf('section')]][$row[csf('sub_section')]]['section']=$row[csf('section')];
				$sammary_array[$row[csf('section')]][$row[csf('sub_section')]]['sub_section']=$row[csf('sub_section')];
				$sammary_array[$row[csf('section')]][$row[csf('sub_section')]]['currency_id']=$row[csf('currency_id')];

				if($row[csf('currency_id')]==1){		
				$sammary_array[$row[csf('section')]][$row[csf('sub_section')]]['amount']+=$row[csf('amount')]/$currency_conversion_rate;             
				}
				else{
				$sammary_array[$row[csf('section')]][$row[csf('sub_section')]]['amount']+=$row[csf('amount')];
					
				}
		}		  
			$t=1;
			foreach($sammary_array as $section_key_id=>$section_data)
			{
				$section_total=0;
				foreach($section_data as $sub_section_key_id=>$row)
				{
					
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $t; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $t; ?>">
						<td width="35"  align="center"><? echo $t;?></td>
						<td width="100" align="center"><? echo $trims_section[$row["section"]];?></td>
						<td width="100" align="center"><? echo $trims_sub_section[$row["sub_section"]];?></td>
						<td align="right"><? echo number_format($row['amount'],2);$section_total+=$row['amount'];?></td>
						</tr>
					<? 
				$t++;
				}
				?>
				<tr style="background-color:#CCC">
						<td colspan="3" align="right"><b><? echo $trims_section[$row["section"]];?> Total</b></td>
					<td align="right"><b><? echo number_format($section_total,2);?></b></td>
				</tr>
				<?
				$grand_section_total+=$section_total;
			} 
			?>
			<tfoot>
				<tr style="background-color:#CCC">
						<td colspan="3" align="right"><b>Grand Total</b></td>
					<td align="right"><b><? echo number_format($grand_section_total,2);?></b></td>
				</tr>
			</tfoot>
			</table>
	</div>
	<br>
	<table align="left" cellspacing="0" width="2200"  border="1" rules="all" class="rpt_table"  >
		<thead>
			<tr><!-- Order Rcv. No	Within Group	Order Rcv.Date	Cust. WO No	Customer Name	Cust. Style Ref.	Section	Sub-Section	Trims Group	Order UOM	Order Qty	Order Rate ($)	Order Amount ($)	Source	Target Delv. Date	Team Leader	Mkt. By	Remarks -->

				<th width="30">SL</th>
				<th width="110">Order Rcv. No</th>
				<th width="60">Within Group</th>
				<th width="80">Order Rcv.Date</th>
				<th width="100">Attached File</th>
				<th width="110">Cust. WO No</th>
				<th width="140">Customer Name</th>
				<th width="100">Buyer</th>
				<th width="200">Cust. Style Ref.</th>
        		<th width="120">Section</th>
                <th width="120" >Sub Section</th>
                <th width="120">Trims Group</th>
                <th width="60">Order UOM</th>
                <th width="80">Order Qty</th>
                <th width="70">Order Rate ($)</th>
                <th width="80">Order Amount ($)</th>
                <th width="80">Source</th>
                <th width="80">Target Delv. Date</th>
                <th width="120">Team Leader</th>
                <th width="120">Mkt. By</th>
                <th width="120">Insert By</th>
                <th width="60">Remarks</th>
        	</tr>
		</thead>
	</table>
	<div style="width:<? echo 2218;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
		<table align="left" cellspacing="0" width="2200"  border="1" rules="all" class="rpt_table"  >
			<tbody>
				<?
				$tblRow=1; $i=1; $grand_tot_amt=0;
				foreach($wo_arr as $wo_id=> $wo_id_data)
				{
					$wo_id_rowspan=0;
					foreach($wo_id_data as $subDtlsID=> $row)
					{
						if($row['within_group']==1) $party=$company_arr[$row['party_id']] ; else $party=$party_arr[$row['party_id']] ;
						
						$buyer_style_ref=implode(" , ",array_unique(explode(" , ",$wo_style_arr[$wo_id]["buyer_style_ref"])));
						$buyer_buyer=implode(" , ",array_unique(explode(" , ",$wo_style_arr[$wo_id]["buyer_buyer"])));
						$img_val =  return_field_value("master_tble_id","common_photo_library","form_name='trims_order_receive' and master_tble_id='$wo_id'","master_tble_id");
						if($row['currency_id']==1){
							$rate=$row['rate']/$currency_conversion_rate;
							$amount=$row['amount']/$currency_conversion_rate;
							$title=$currency_conversion_rate;
						}
						else{
							$rate=$row['rate'];
							$amount=$row['amount'];
							$title=1;
						}
						$grand_tot_amt +=$amount;
						$bgcolor=($tblRow%2==0)?"#E9F3FF":"#FFFFFF";
						?>
						<tr bgcolor="<?php echo $bgcolor; ?>" onclick="change_color('tr_<?php echo $tblRow; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $tblRow; ?>" align="center">
							<? if($wo_id_rowspan==0){ 
								?> 	
								<td valign="middle" width="30" rowspan="<? echo $wo_id_rowspan_arr[$wo_id]; ?>"><p><?  echo $tblRow ; ?></p></td>
								<td valign="middle" width="110" rowspan="<? echo $wo_id_rowspan_arr[$wo_id]; ?>" align="left"><a href='##' style='color:#000' onClick="print_report('<? echo $company_name.'*'.$wo_id.'*'."Trims Order Receive Entry".'*'.$row['within_group'];?>','trims_order_receive_print', '../../marketing/requires/trims_order_receive_controller')"><font color="blue"><strong><? echo $row['subcon_job']; ?></strong></font></a></td>
								<td valign="middle" width="60" rowspan="<? echo $wo_id_rowspan_arr[$wo_id]; ?>"><p><?  echo $yes_no[$row['within_group']]; ?></p></td>
								<td valign="middle" width="80" rowspan="<? echo $wo_id_rowspan_arr[$wo_id]; ?>"><p><?  echo change_date_format($row['receive_date']); ?></p></td>
								<td valign="middle" width="100" rowspan="<? echo $wo_id_rowspan_arr[$wo_id]; ?>"><a href="javascript:void()" onClick="downloiadFile('<? echo $wo_id; ?>','<? echo $company_name; ?>');">
                                <? if ($img_val != '') echo 'View File'; ?></a></td>
								<td valign="middle" width="110" rowspan="<? echo $wo_id_rowspan_arr[$wo_id]; ?>" align="left" ><p><?  echo $row['order_no']; ?></p></td>
								<td valign="middle" width="140" rowspan="<? echo $wo_id_rowspan_arr[$wo_id]; ?>" align="left"><p><?  echo $party; ?></p></td>
								<td valign="middle" width="100" rowspan="<? echo $wo_id_rowspan_arr[$wo_id]; ?>" align="left"><p><?  echo chop($buyer_buyer,' , '); ?></p></td>
								<td valign="middle" width="200" rowspan="<? echo $wo_id_rowspan_arr[$wo_id]; ?>" align="left"><p><?  echo chop($buyer_style_ref,' , ') ; ?></p></td>
								<? } ?>
								<td valign="middle" width="120" align="left"><p><?  echo $trims_section[$row['section']]; ?></p></td>
								<td valign="middle" width="120" align="left"><p><?  echo $trims_sub_section[$row['sub_section']]; ?></p></td>
								<td valign="middle" width="120" align="left"><p><?  echo $trim_group_arr[$row['item_group']]; ?></p></td>
								<td valign="middle" width="60" ><p><?  echo $unit_of_measurement[$row['booked_uom']]; ?></p></td>
								<td valign="middle" width="80" align="right" ><a href="##" onclick="fnc_qty('<? echo $row['subcon_job'] ;?>','<? echo $subDtlsID ;?>','qty_popup')"><?  echo number_format($row['order_quantity'],4) ; ?></a></td>
								<td valign="middle" width="70" align="right"><p><?  echo number_format($rate,4); ?></p></td>
								<td valign="middle" width="80" align="right" title="<? echo $title; ?>"><p><?  echo number_format($amount,2); ?></p></td>
								<td valign="middle" width="80" ><p><?  echo $source_order[$row['source_for_order']]; ?></p></td>
								<? if($wo_id_rowspan==0){ 
								?> 	<td valign="middle" width="80" rowspan="<? echo $wo_id_rowspan_arr[$wo_id]; ?>"><p><?  echo  change_date_format($row['delivery_date']); ?></p></td>
								 	<td valign="middle" width="120" rowspan="<? echo $wo_id_rowspan_arr[$wo_id]; ?>" align="left" ><p><?  echo  $leader_name_arr[$row['team_leader']]; ?></p></td>
								 	<td valign="middle" width="120" rowspan="<? echo $wo_id_rowspan_arr[$wo_id]; ?>" align="left" ><p><?  echo  $member_name_arr[$row['team_member']]; ?></p></td>
								 	<td valign="middle" width="120" rowspan="<? echo $wo_id_rowspan_arr[$wo_id]; ?>" align="left" ><p><?  echo  $user_arr[$row['inserted_by']]; ?></p></td>
								 	<td valign="middle" width="60" rowspan="<? echo $wo_id_rowspan_arr[$wo_id]; ?>"  align="left" ><a href="##" onclick="fnc_remarks('<? echo $wo_id ;?>','remarks_popup')"><? if($row['remarks']!='') echo 'View';?></a></td>
								<? } ?>
						</tr>
						<?
						$tblRow++; $wo_id_rowspan++;
					}
				}
				?>
			</tbody>
		</table>
	</div>
	<table align="left" cellspacing="0" width="2200"  border="1" rules="all" class="rpt_table" >
		<tfoot>
			<tr>
				<th width="1524" align="right"><strong>G.Total:</strong></th>
				<th width="80" align="right"><p><strong><? echo number_format($grand_tot_amt,2) ; ?></strong></p></th>
				<th >&nbsp;</th>
			</tr>
		</tfoot>
	</table>
</div>

<?
	/*foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	
	exit();*/


	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
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

if($action=="get_user_pi_file_without_download")
{
    // var_dump($_REQUEST);
    extract($_REQUEST);
    //echo "SELECT id,image_location,master_tble_id,real_file_name,FILE_TYPE from common_photo_library where form_name='trims_order_receive' and master_tble_id='$id'";
  	//$img_val =  return_field_value("master_tble_id","common_photo_library","form_name='trims_order_receive' and master_tble_id=$id","master_tble_id");
    $img_sql = "SELECT id,image_location,master_tble_id,real_file_name,FILE_TYPE from common_photo_library where form_name='trims_order_receive' and master_tble_id='$id'";
    $img_sql_res = sql_select($img_sql);
    if(count($img_sql_res)==0){ echo "<div style='text-align:center;color:red;font-size:18px;'>Image/File is not available.</div>";die();}
    foreach($img_sql_res as $img)
    {
        //if($img[FILE_TYPE]==1){
			echo '<p style="display:inline-block;word-wrap:break-word;word-break:break-all;width:115px;padding-right:10px;vertical-align:top;margin:0px;"><a href="?action=downloiadFile&file='.urlencode($img[csf("image_location")]).'"><img src="../../../../file_upload/blank_file.png" width="89px" height="97px"></a><br>'.$img[csf("real_file_name")].'</p>'; 
		//}
    }
}

if($action=="get_user_pi_file")
{
	echo load_html_head_contents("File View", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	?>
	<fieldset style="width:600px; margin-left:5px">
		<div style="width:100%; word-wrap:break-word" id="scroll_body">
             <table border="0" rules="all" width="100%" cellpadding="2" cellspacing="2">
             	<tr>
					<?
					$i=0;
                    $sql="SELECT id,image_location,master_tble_id,real_file_name,FILE_TYPE from common_photo_library where form_name='trims_order_receive' and master_tble_id='$id'";
                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
						$i++;
                    ?>
                    	<td width="100" align="center"><a target="_blank" href="../../../../<? echo $row[csf('image_location')]; ?>"><img width="89" height="97" src="../../../../file_upload/blank_file.png"><br>File-<? echo $i; ?></a></td>
                    <?
						if($i%6==0) echo "</tr><tr>";
                    }
                    ?>
                </tr>
            </table>
        </div>	
	</fieldset>     
	<?
	exit();
}

if($action=="remarks_popup")
{
	echo load_html_head_contents("Remarks Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script type="text/javascript">
	function fnc_close()
	{
		parent.emailwindow.hide();
	}
	</script>

    <table width="480" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
        <tbody>
		<?
		$details_sql="select a.id , a.remarks from subcon_ord_mst a where a.id in(".chop($ids,',').") and a.status_active=1 and a.is_deleted=0 ";
		//echo $details_sql;
		$sql_result=sql_select($details_sql); $t=1;
		foreach($sql_result as $row)
		{
			if ($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
        	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $t; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $t; ?>">
        		<td width="30"><p><? echo $t; ?>&nbsp;</p></td>
                <td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
            </tr>
            <?
			$t++;
		}
		?>
        </tbody>
        <tfoot>
				<tr>
					<td align="center" colspan="2" align="center"><input type="button" name="main_close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" /></td>
				</tr>
			</tfoot>
    </table>
    <?
}

if($action=="qty_popup")
{
	echo load_html_head_contents("Quantity Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script type="text/javascript">
	function fnc_close()
	{
		parent.emailwindow.hide();
	}
	</script>
	<style type="text/css">
		td.make_bold {
	  		font-weight: 900;
		}
	</style>
	<?
	$source_order = array(1 => 'In-House', 2 => 'Sub-Contract');
	$trim_group_arr=return_library_array( "select id, item_name from lib_item_group where item_category=4",'id','item_name');
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');

	//echo  "select id, mst_id, order_id, job_no_mst, book_con_dtls_id, description, color_id, size_id, qnty, rate, amount,is_revised from subcon_ord_breakdown where status_active=1 and is_deleted=0 and job_no_mst='$job' and mst_id='$ids'  order by id";
	$dtls_result=sql_select( "select id, job_no_mst,item_group,source_for_order,order_uom from subcon_ord_dtls where status_active=1 and is_deleted=0 and job_no_mst='$job' and id='$ids'  order by id");	?>

    <div align="center" style="width:100%;" >
    	<table width="600" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
	        <tr>
	            <td width="110" class="make_bold">Order Rcv. No:</td>
	            <td width="160" class="make_bold"><? echo $dtls_result[0][csf('job_no_mst')]; ?></td>
	            <td width="110" class="make_bold">Source</td>
	            <td width="160" class="make_bold"><? echo $source_order[$dtls_result[0][csf('source_for_order')]]; ?></td>
	        </tr>
	        <tr>
	            <td width="110" class="make_bold">Trims Group:</td>
	            <td width="160" class="make_bold"><? echo $trim_group_arr[$dtls_result[0][csf('item_group')]]; ?></td>
	            <td width="110" class="make_bold">UOM</td>
	            <td width="160" class="make_bold"><? echo $unit_of_measurement[$dtls_result[0][csf('order_uom')]]; ?></td>
	        </tr>
	    </table>
		</br>
		<table class="rpt_table" width="630px" cellspacing="0" cellpadding="0" rules="all" id="tbl_share_details_entry" align="left">
			<thead>
				<th width="150">Style</th>
				<th width="150">Description</th>
				<th width="120">Color</th>
				<th width="120">Size</th>
				<th>Order Qty</th>
				
			</thead>
		</table>
		<div style="width:<? echo 648;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
			<table class="rpt_table" width="630px" cellspacing="0" cellpadding="0" rules="all" id="tbl_share_details_entry">
				<tbody>
					<?
					//echo  "select id, mst_id, order_id, job_no_mst, book_con_dtls_id, description, color_id, size_id, qnty, rate, amount,is_revised from subcon_ord_breakdown where status_active=1 and is_deleted=0 and job_no_mst='$job' and mst_id='$ids'  order by id";
					$qry_result=sql_select( "select id, mst_id, order_id, job_no_mst, book_con_dtls_id, description, color_id, size_id, qnty, rate, amount,is_revised,style from subcon_ord_breakdown where status_active=1 and is_deleted=0 and job_no_mst='$job' and mst_id='$ids'  order by id");	
					//echo "<pre>";
					//print_r($qry_result);
					foreach($qry_result as $row)
					{
						if ($k%2==0)  
	            			$bgcolor="#E9F3FF";
	            		else
	            			$bgcolor="#FFFFFF";	
						$k++;
						$total_qty +=$row[csf('qnty')];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="150"><? echo $row[csf('style')]; ?></td>
							<td width="150"><? echo $row[csf('description')]; ?></td>
							<td width="120"><? echo  $color_library[$row[csf('color_id')]]; ?></td>
							<td width="120"><? echo  $size_arr[$row[csf('size_id')]];?></td>
							<td align="right"><? echo number_format($row[csf('qnty')],2);?></td>
						</tr>
						<?
					}
					?> 
				</tbody>
				<tfoot>
					<td colspan="4" align="right"><strong>Total:</strong></td>
					<td align="right"><strong><? echo number_format($total_qty,2);?></strong></td>
				</tfoot>
			</table>
		</div> 
		<table>
			<tr>
				<td align="center"><input type="button" name="main_close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" /></td>
			</tr>
		</table>
	</div>
    <?
}