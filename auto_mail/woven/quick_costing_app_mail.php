<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../../includes/common.php');
require_once('../../mailer/class.phpmailer.php');
require_once('../setting/mail_setting.php');
extract($_REQUEST);

list($wo_id,$app_type,$app_cause,$approval_id)=explode('_',$data);



	
	if ($cbo_year=="" || $cbo_year==0) $year_cond="";
	else
	{
		if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')='".trim($cbo_year)."'";
		else $year_cond=" and YEAR(a.insert_date)='".trim($cbo_year)."'";
	}
	
	if($db_type==2) $year="TO_CHAR(a.insert_date,'YYYY')"; else $year="YEAR(a.insert_date)";

	 
	//$user_id=133;
 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name");


	if($app_type==1){
		$sql="select a.id, a.qc_no, a.inquery_id, b.tot_fob_cost, a.brand_id, a.season_id, a.season_year, a.cost_sheet_id, a.cost_sheet_no, $year as year, a.style_ref, a.buyer_id, a.delivery_date, a.exchange_rate, a.offer_qty, a.costing_date, a.revise_no, a.option_id, c.id as approval_id, c.sequence_no, c.approved_by, c.approved_date, a.approved, a.inserted_by, a.revise_no, a.option_id, d.job_id, d.id as confirm_id from qc_mst a, qc_tot_cost_summary b, approval_history c, qc_confirm_mst d where c.mst_id=a.id and c.entry_form=45 and a.qc_no=b.mst_id and b.mst_id=d.cost_sheet_id  and a.status_active=1 and a.is_deleted=0   and d.ready_to_approve=1 and  b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.current_approval_status=1 and a.qc_no=$wo_id";
		//echo $sql;
	}
	else{
		$sql="select a.id, a.qc_no, a.inquery_id, b.tot_fob_cost, a.brand_id, a.season_id, a.season_year, a.cost_sheet_id, a.cost_sheet_no, TO_CHAR(a.insert_date,'YYYY') as year, a.style_ref, a.buyer_id, a.delivery_date, a.exchange_rate, a.offer_qty, a.costing_date, a.revise_no, a.option_id, 0 as approval_id, a.approved, a.inserted_by, a.revise_no, a.option_id, c.job_id, c.id as confirm_id from qc_mst a, qc_tot_cost_summary b, qc_confirm_mst c where a.qc_no=b.mst_id and b.mst_id=c.cost_sheet_id and a.approved in (0,2) and c.ready_to_approve=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.qc_no='$wo_id'";
	}
	$nameArray=sql_select( $sql );
	
	$brand_id=$nameArray[0][csf(brand_id)];
	$inserted_by=$nameArray[0][csf(inserted_by)];
	
	
  
	$report_action="quick_costing_print";
	
	$sql_request="select booking_id, NOT_APPROVAL_CAUSE,approval_cause,APPROVAL_TYPE from fabric_booking_approval_cause where entry_form=28 and status_active=1 and is_deleted=0 and booking_id=".$nameArray[0][csf(qc_no)]." ";
	//echo $sql_request;
	$nameArray_request=sql_select($sql_request);
	foreach($nameArray_request as $approw)
	{
		$unappRequest_arr[$approw[csf("booking_id")]][1]=$approw[csf("NOT_APPROVAL_CAUSE")];
		$unappRequest_arr[$approw[csf("booking_id")]][2]=$approw[csf("approval_cause")];
	}
	
	$seasonArr = return_library_array("select id,season_name from lib_buyer_season ","id","season_name");
	$brandArr = return_library_array("select id,brand_name from lib_buyer_brand ","id","brand_name");
	$concernMarchantArr=return_library_array( "select id, concern_marchant from wo_quotation_inquery where entry_form=434", "id", "concern_marchant");
	$teamMemberinfoArr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
	ob_start();
	?>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1450" class="rpt_table" >
                <thead style="background-color:#CCCCCC">
                    <th width="120">Buyer</th>
                    <th width="120">Master Style</th>
                    <th width="80">Brand</th>
                    <th width="80">Season</th>
                    <th width="50">Season Year</th>
                    <th width="100">Cost Sheet No</th>
                    <th width="50">Year</th>
                   	<th width="70">Revise No</th>
                   	<th width="70">Option No</th>
                    <th width="65">Costing Date</th>
                    <th width="100">Insert By</th>
                    <th width="70">Offer Qty.</th>
                    <th width="70">FOB Cost</th>
                   	<th width="70">Concern Merchant</th>
                    <th width="70">Approved Date</th>
                    <th width="70">Un-Appv Request</th>
                    <th>Refusing Cause</th>
                </thead>
                    <tbody>
                        <?
                        $i=1;
						$ref_no=""; $file_numbers="";
						foreach ($nameArray as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if($approval_type==2) $value=$row[csf('id')]; else $value=$row[csf('id')]."**".$row[csf('approval_id')]."**".$row[csf('confirm_id')];;
							
							$fob_cost=$row[csf('tot_fob_cost')];
							if($fob_cost=='' || $fob_cost==0) $fob_cost=0; else $fob_cost=$fob_cost;
							if($fob_cost<0 || $fob_cost==0) $td_color="#F00"; else $td_color="";
							
							
							?>
							<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>" align="center">
                                <td style="word-break:break-all"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</td>
                                <td align="center" style="word-break:break-all"><?=$row[csf('style_ref')]; ?></td>
                                
                                <td style="word-break:break-all"><?=$brandArr[$row[csf('brand_id')]]; ?></td>
                                <td style="word-break:break-all"><?=$seasonArr[$row[csf('season_id')]]; ?></td>
                                <td style="word-break:break-all"><?=$row[csf('season_year')]; ?></td>
                                
                                <td style="word-break:break-all;"><?=$row[csf('cost_sheet_no')]; ?></td>
                                <td style="word-break:break-all" align="center"><?=$row[csf('year')]; ?>&nbsp;</td>
                                <td style="word-break:break-all"><?=$row[csf('revise_no')]; ?></td>
								<td style="word-break:break-all"><?=$row[csf('option_id')]; ?></td>
                                <td align="center"><? if($row[csf('costing_date')]!="0000-00-00") echo change_date_format($row[csf('costing_date')]); ?>&nbsp;</td>
                                <td style="word-break:break-all"><?=ucfirst($user_arr[$row[csf('inserted_by')]]); ?>&nbsp;</td>
								<td style="word-break:break-all" align="right"><?=$row[csf('offer_qty')]; ?></td>
								<td  align="right"><p style="color:<?=$td_color; ?>"><?=number_format($fob_cost,2); ?>&nbsp;</p></td>
								<td  style="word-break:break-all"><?=$teamMemberinfoArr[$concernMarchantArr[$row[csf('inquery_id')]]]; ?></td>
								<td align="center"><? if($row[csf('approved_date')]!="0000-00-00") echo $row[csf('approved_date')]; ?>&nbsp;</td>
								<td align="center"><?=$unappRequest_arr[$row[csf("qc_no")]][2];?></td>
								<td align="center"><?=$unappRequest_arr[$row[csf("qc_no")]][1];?></td>
							</tr>
							<?
							$i++;
						}
                        ?>
                    </tbody>
                </table>

            
	<?
	$messageBody=ob_get_contents( );
	ob_clean();
 

 	$buyerData = sql_select("select b.USER_EMAIL,a.BRAND_ID from electronic_approval_setup a,USER_PASSWD b where b.id=a.user_id and a.page_id=1997 and a.is_deleted=0 ");
		foreach($buyerData as $row){
			foreach(explode(',',$row[BRAND_ID]) as $brand){
				if($brand==$brand_id){$mailAddress[$row[USER_EMAIL]]=$row[USER_EMAIL];}
			}
		}
		
		$sql = "select ID,USER_EMAIL,BRAND_ID from USER_PASSWD where STATUS_ACTIVE=1 and id=$inserted_by";
		$sqlResult = sql_select($sql);
		foreach($sqlResult as $row){
			$mailAddress[$row[USER_EMAIL]]=$row[USER_EMAIL];
		}

		$teamSql="select id,TEAM_LEADER_EMAIL  from lib_marketing_team where project_type=2 and team_type in (0,1) and status_active =1 and is_deleted=0 and id in(select  team_id from lib_mkt_team_member_info where USER_TAG_ID =$inserted_by  and status_active =1 and is_deleted=0) and TEAM_LEADER_EMAIL is not null";
		$teamSqlResult = sql_select($teamSql);
		foreach($teamSqlResult as $row){
			$mailAddress[$row[TEAM_LEADER_EMAIL]]=$row[TEAM_LEADER_EMAIL];
		}

		
		$to=implode(',',$mailAddress);
		$subject="Quick Costing";
		$header=mailHeader();
		//echo $messageBody;die;
		echo sendMailMailer( $to, $subject, $messageBody,'','' );
		echo " Mail Address: ".$to;
		

	
		
?>