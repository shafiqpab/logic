<?php
date_default_timezone_set("Asia/Dhaka");
// require_once('../mailer/class.phpmailer.php');
require_once('../includes/common.php');
require_once('setting/mail_setting.php');
extract($_REQUEST);
// var returnValue=return_global_ajax_value(reponse[2], 'sweater_sample_acknowledgement_mail_notification', '', '../../../auto_mail/sweater_sample_acknowledgement_mail_notification');
//echo load_html_head_contents("Mail Notification", "../", 1, 1,'','','');

//$action='sweater_sample_acknowledgement_mail_notification';	
	//$data=2120;
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$season_arr=return_library_array("select id,season_name from  lib_buyer_season","id","season_name");
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	$team_name_arr=return_library_array("select id,team_name from  lib_sample_production_team where  is_deleted=0","id","team_name");
	$team_email_arr=return_library_array("select id,email from  lib_sample_production_team where  is_deleted=0","id","email");
	$team_leader_arr=return_library_array("select id,team_leader_name from lib_marketing_team where project_type=6 and status_active =1 and is_deleted=0 order by team_leader_name","id","team_leader_name");

	$sample_name_arr=return_library_array( "select id,sample_name from  lib_sample where  status_active=1 and is_deleted=0",'id','sample_name');
    $dealing_merchant_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info", 'id', 'team_member_name');
    $dealing_merchant_mail_arr = return_library_array("select id, TEAM_MEMBER_EMAIL from lib_mkt_team_member_info", 'id', 'TEAM_MEMBER_EMAIL');

	$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
	$current_date = change_date_format(date("Y-m-d", $strtotime),'','',1);
	$previous_date = change_date_format(date('Y-m-d', strtotime('-1 day', strtotime($current_date))),'','',1);

	  
	if($db_type==0){
		$date_cond	=" and b.shipment_date between '".$previous_date."' and '".$current_date."'";
	}
	else
	{
		$date_cond	=" and b.shipment_date between '".$previous_date."' and '".$current_date."'";
	}

	
	$main_data=sql_select("select b.id as po_id,a.job_no,a.buyer_name,a.dealing_marchant,a.style_ref_no,b.po_number,b.pub_shipment_date,b.doc_sheet_qty,a.team_leader,b.po_quantity,b.shiping_status,b.shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and to_char(b.pub_shipment_date,'YYYY') in (2021,2022)");



	 foreach($main_data as $val){
 
				 $marchant_wise_data_arr[$val[csf('dealing_marchant')]][$val[csf('job_no')]][$val[csf('style_ref_no')]][$val[csf('po_id')]]['ship_date']=$val[csf('pub_shipment_date')];

				 $marchant_wise_data_arr[$val[csf('dealing_marchant')]][$val[csf('job_no')]][$val[csf('style_ref_no')]][$val[csf('po_id')]]['buyer_name']=$val[csf('buyer_name')];

				 $marchant_wise_data_arr[$val[csf('dealing_marchant')]][$val[csf('job_no')]][$val[csf('style_ref_no')]][$val[csf('po_id')]]['team_leader']=$val[csf('team_leader')];
				 $marchant_wise_data_arr[$val[csf('dealing_marchant')]][$val[csf('job_no')]][$val[csf('style_ref_no')]][$val[csf('po_id')]]['po_number']=$val[csf('po_number')];

			
 
				 $marchant_wise_data_arr[$val[csf('dealing_marchant')]][$val[csf('job_no')]][$val[csf('style_ref_no')]][$val[csf('po_id')]]['in_hand_qnty']+=$val[csf('doc_sheet_qty')];
 
				 $marchant_wise_data_arr[$val[csf('dealing_marchant')]][$val[csf('job_no')]][$val[csf('style_ref_no')]][$val[csf('po_id')]]['po_quantity']+=$val[csf('po_quantity')];

				 $marchant_wise_data_arr[$val[csf('dealing_marchant')]][$val[csf('job_no')]][$val[csf('style_ref_no')]][$val[csf('po_id')]]['shiping_status']+=$val[csf('shiping_status')];
				 
				 $po_arr[$val[csf('id')]]=$val[csf('id')];
				 $dealing_merchantArr[$val[csf('dealing_marchant')]]=$val[csf('dealing_marchant')];
 
	 }
	 
//  echo count($dealing_merchantArr);
	 $ex_factory_data=sql_select("SELECT a.po_break_down_id,a.ex_factory_date,a.ex_factory_qnty,a.shiping_status,b.po_number	from pro_ex_factory_mst a,wo_po_break_down b where  b.id=a.po_break_down_id 
		 and a.status_active=1  and a.entry_form<>85 and a.is_deleted=0 ");
 
 
		 foreach($ex_factory_data as $row){
			 $po_wise_ship_qty[$row[csf('po_break_down_id')]]['ship_qty']+=$row[csf('ex_factory_qnty')];
		 }

		
	

	

	foreach($marchant_wise_data_arr as $marchant_id=>$marchant_data){
		ob_start();	
	?>
	<div style="width:100%; margin-bottom:5px;" align="left">
		<fieldset>
				<table width="100%" cellpadding="0" cellspacing="0" id="caption">
					<tr>
					<td align="center" width="100%" colspan="13" class="form_caption" ><strong style="font-size:18px"><? echo $dealing_merchant_arr[$marchant_id]; ?></strong></td>
					</tr> 
					<tr>  
					<td align="center" width="100%" colspan="13">Scheduled Shipment Reminder Report</td>
					</tr>  
				</table>

				<table width="100%" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
					<thead>
						<tr>
							<th width="35">Sl</th>
							<th width="120">Buyer Name</th>
							<th width="150">Team Leader</th>
							<th width="120" >Dealing Merchandiser</th>
							<th width="100">Job No</th>
							<th width="150">Style No</th>
							<th width="100">PO No</th>
							<th width="100">Pub. Ship Date</th>
							<th width="60">Ship Remaning Days</th>
							<th width="100">PO Qty (Pcs)</th>
							<th width="100">Ship Qty</th>
							<th width="100">In Hand Qty</th>
							<th width="100">Ship Status</th>
						</tr>
					</thead>
					<tbody>
	                <?
					$i= 1;
		
				foreach($marchant_data as $job_id=>$job_data){	
					foreach($job_data as $style_id=>$style_data){			
					  foreach($style_data as $po_id=>$row){


						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$currentdate =strtotime( date_format(date_create($current_date),"Y-m-d"))  ;
						$ship_date =strtotime(date_format(date_create($row['ship_date']),"Y-m-d")) ;					
						$sdays    = round($ship_date/60/60/24);
						$cdays    = round($currentdate/60/60/24);
						$days=$sdays-$cdays;
						
						if( $days<4 && $row['shiping_status']<3){

						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                        <td align="center"><? echo $i;?></td>
	                        <td align="center"><? echo $buyer_arr[$row['buyer_name']];?></td>
	                        <td><? echo $team_leader_arr[$row['team_leader']];?></td>
	                        <td title="<?=$marchant_id;?>"><? echo $dealing_merchant_arr[$marchant_id];?></td>
	                        <td><? echo $job_id;?></td>
	                        <td><? echo $style_id;?></td>
	                        <td><? echo $row['po_number']; ?></td>
	                        <td><? echo change_date_format($row['ship_date']);?></td>
	                        <td><? echo $days." Days";	?></td>
	                        <td align="center"><? echo $row['po_quantity'];?></td>
	                        <td align="right"><? echo $po_wise_ship_qty[$po_id]['ship_qty'];?></td>
	                        <td align="right"><? echo $row['po_quantity']-$po_wise_ship_qty[$po_id]['ship_qty'];?></td>
	                        <td align="center" title="<?=$row['shiping_status'];?>"><? echo $shipment_status[$row['shiping_status']];;?></td>
	                    </tr>
						<?
						
	                    $i++;
							
						}
						
	              
		       
	   					      }
           					 }}
	                ?>
	                
	                </tbody>
	            </table>
			
				 
        </fieldset>
    </div>
	
		<?  
		$emailBody=ob_get_contents();
		ob_clean();
		
		if($dealing_merchant_mail_arr[$marchant_id]){
			$to=$dealing_merchant_mail_arr[$marchant_id];
		}
	

			
		// $subject="Scheduled Shipment Reminder Report";
		// if($to!=""){echo sendMailMailer( $to, $subject, $emailBody );}else{ echo 'Sorry. Email not found';}
		if($_REQUEST['isview']==1){
			if($to){
				echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
			}else{
				echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
			}
			echo $emailBody;
		}
		else{
		  if($to!=""){echo sendMailMailer( $to, $subject, $emailBody );}else{ echo 'Sorry. Email not found';}
	}



		}
 	 
  

	
	
 // }
	
exit();	



?>
