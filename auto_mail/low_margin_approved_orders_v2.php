<?php
date_default_timezone_set("Asia/Dhaka");

// require_once('../includes/common.php');
// require_once('../mailer/class.phpmailer.php');
include('../includes/common.php');
include('setting/mail_setting.php');




$sql="select id,team_member_name,member_contact_no from lib_mkt_team_member_info where  status_active =1 and is_deleted=0";
$data_array=sql_select($sql);
foreach( $data_array as $row )
{ 
	$dealing_merchant_arr[$row[csf("id")]]=$row[csf("team_member_name")].'<br>'.$row[csf("member_contact_no")];
}

 
	$team_leader_name_arr = return_library_array( "select id,team_leader_name from lib_marketing_team where  status_active =1 and is_deleted=0", "id", "team_leader_name");
	$company_library 	=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
	$buyer_library 		=return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name");
	$party_library 		=return_library_array( "select id, other_party_name from lib_other_party where  status_active=1 and is_deleted=0", "id", "other_party_name");
	$supplier_library 	=return_library_array( "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$user_arr 			=return_library_array( "select id,user_full_name from user_passwd where valid=1","id","user_full_name");
	$country_arr 		=return_library_array( "select id, country_name from lib_country", "id", "country_name");


 


	$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
	if($_REQUEST['view_date']){
		$current_date = change_date_format(date("Y-m-d H:i:s",strtotime($_REQUEST['view_date'])),'','',1);
	}
	$previous_date= change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
   // echo $previous_date;die;
	
    //$previous_date='5-Sep-2023';

	$str_cond	=" and a.approved_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
	$str_cond_b	=" and b.approved_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
 


    $sql = "select a.job_no,a.job_id,a.costing_date,a.exchange_rate,a.sew_effi_percent,a.costing_per,b.total_cost,b.cm_cost,b.commission from wo_pre_cost_mst a,wo_pre_cost_dtls b
	where a.job_no=b.job_no $str_cond and a.approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
  //echo $sql;die;
    $app_job_arr = array();
	$data_array=sql_select($sql);
	foreach( $data_array as $row )
	{ 
		//$marginDataArr[$row[csf("job_no")]]['total_material_service_cost']=$row[csf("total_cost")]-($row[csf("commission")]+$row[csf("cm_cost")]);
		$marginDataArr[$row[csf("job_no")]]['total_material_service_cost']=$row[csf("total_cost")]-($row[csf("cm_cost")]);
		$marginDataArr[$row[csf("job_no")]]['cm_cost']=$row[csf("cm_cost")];
		$marginDataArr[$row[csf("job_no")]]['costing_date']=$row[csf("costing_date")];
		$marginDataArr[$row[csf("job_no")]]['exchange_rate']=$row[csf("exchange_rate")];
		$marginDataArr[$row[csf("job_no")]]['sew_effi_percent']=$row[csf("sew_effi_percent")];
		$marginDataArr[$row[csf("job_no")]]['costing_per']=$row[csf("costing_per")];
		$app_job_arr[$row[csf("job_no")]]=$row[csf("job_id")];
	}

    $app_job_str = implode(',',$app_job_arr);
    //echo $app_job_str;die;

 
foreach($company_library as $compid=>$compname) /// Bellow 5% Profitability Orders
{
  
	
	$flag=0;	
	ob_start();
	
   // print_r($app_job_str);die;
	
	
	$cm_cost_method_based_on = return_field_value( "cm_cost_method_based_on", "variable_order_tracking", "company_name='$compid' and variable_list=22" );

	
//var_dump($marginDataArr['D n C-17-01639']);
	
	?>

	<table width="1200"  cellspacing="0" border="0">
		<tr>
			<td colspan="24">
				<strong><?php  echo $company_library[$compid]; ?></strong>
			</td>
		</tr>
		<tr>
			<td colspan="24">
				<b style="font-size:14px;">Details of orders approved with low Margin <br />Approved Date : <? echo date("d-m-Y", strtotime($previous_date));  ?></b>
			</td>
		</tr>
	</table>

	<table width="1350" border="1" rules="all" class="rpt_table" id="table_body3" style="font-size:13px;">
		<thead style="background-color:#CCC">
			<tr align="center">
				<th width="35">Sl</th>
                <th width="100">Team Leader</th>
				<th width="100">Job No</th>
				<th width="100">Buyer</th>
				<th width="100">Style</th>
				<th width="30" hidden >SMV</th>
                <th width="30" hidden >Effi%</th>
				<th width="100">Job Qty (Pcs)</th>
				<th width="100" hidden >Total SMV</th>
				<th width="70" hidden >Avg Unit Price</th>
                
				<td width="80">Total Order Value</td>
				<td width="80">Total Mat & Serv Cost</td>
				<td width="80">CM Value</td>
				<td width="80">CM Cost</td>
				<td width="80">Margin</td>
				<td width="80" bgcolor="#FFFF99">Margin %</td>
				<td width="80">CPM</td>
				<td width="80">EPM</td>
				<th width="80" hidden >Costing Date</th>
				<th width="80" hidden >Dealing Merchant</th>
				<th width="80">Min Ship Date</th>
				<th width="80">Approved By</th>
				<th>Unapproved/Approved</th>
			</tr>
			<tr>
			</tr>
		</thead>
		<tbody>
			<?php
			$i=0;
			$total_po_qty=0;
			$total_value=0;
			if($db_type==0){$date_diff="DATEDIFF(b.shipment_date,b.po_received_date) as  date_diff,";}
			else{$date_diff="(b.shipment_date - b.po_received_date) as  date_diff,";}
				
			$sql_mst="select a.job_no,a.set_smv,a.set_break_down,a.order_uom,a.dealing_marchant,a.buyer_name,a.style_ref_no,a.job_quantity,a.total_set_qnty,a.avg_unit_price,a.team_leader,
			min(b.pub_shipment_date) as shipment_date,
			min(b.pub_shipment_date) as min_pub_shipment_date,
			max(b.pub_shipment_date) as max_pub_shipment_date,
			min(b.shipment_date) as min_shipment_date,
			max(b.shipment_date) as max_shipment_date,
			
			a.inserted_by from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name = $compid and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.id in($app_job_str) group by a.job_no,a.set_smv,a.set_break_down,a.order_uom,a.team_leader,a.dealing_marchant,a.buyer_name,a.style_ref_no,a.avg_unit_price,a.job_quantity,a.total_set_qnty,a.inserted_by";
		 // echo $sql_mst;die;
			
						
			$nameArray_mst=sql_select($sql_mst);
			foreach($nameArray_mst as $row)
			{
				 

				$set_arr=explode('__',$row[csf('set_break_down')]);
				$item_sting='';
				$smv_sting='';
				$set_sting='';
				$smv_sum=0;
				$set_sum=0;
			
				if($cm_cost_method_based_on==2) $con_date=$row[csf('min_shipment_date')];
				else if($cm_cost_method_based_on==3) $con_date=$row[csf('max_shipment_date')];
				else if($cm_cost_method_based_on==4) $con_date=$row[csf('min_pub_shipment_date')];
				else if($cm_cost_method_based_on==5) $con_date=$row[csf('max_pub_shipment_date')];
				else if($cm_cost_method_based_on==1) $con_date=$marginDataArr[$row[csf('job_no')]]['costing_date'];
				
				$dzn=0;
				if($marginDataArr[$row[csf('job_no')]]['costing_per']==1){$dzn=12;}
				else if($marginDataArr[$row[csf('job_no')]]['costing_per']==2){$dzn=1;}
				else if($marginDataArr[$row[csf('job_no')]]['costing_per']==3){$dzn=24;}
				else if($marginDataArr[$row[csf('job_no')]]['costing_per']==4){$dzn=36;}
				else if($marginDataArr[$row[csf('job_no')]]['costing_per']==5){$dzn=48;}
				
				
				
				
				$cost_per_minute = return_field_value( "cost_per_minute", "lib_standard_cm_entry", "company_id='$compid' and (applying_period_date <= '$con_date' and applying_period_to_date >= '$con_date')" );
			
				$cpm =(($cost_per_minute/$marginDataArr[$row[csf('job_no')]]['exchange_rate'])/$marginDataArr[$row[csf('job_no')]]['sew_effi_percent'])*100;
				
			$htmCpm = '(('.$cost_per_minute.'/'.$marginDataArr[$row[csf('job_no')]]['exchange_rate'].')/'.$marginDataArr[$row[csf('job_no')]]['sew_effi_percent'].')*'.(100);
			
			//............................................
					$tot_pic_qty=$row[csf('total_set_qnty')]*$row[csf('job_quantity')];
					$avg_unit_price=$row[csf('avg_unit_price')]/$row[csf('total_set_qnty')];
					$value=$tot_pic_qty*$avg_unit_price; 
					$tmsc=($marginDataArr[$row[csf('job_no')]]['total_material_service_cost']/($dzn*$row[csf('total_set_qnty')]))*$tot_pic_qty;

					$cmValue=($value-$tmsc);
					$cm_cost=($marginDataArr[$row[csf('job_no')]]['cm_cost']/($dzn*$row[csf('total_set_qnty')]))*$tot_pic_qty;
					$margin=$cmValue-$cm_cost;
					
					$cmValueHtml=$value.'-('.$marginDataArr[$row[csf('job_no')]]['total_material_service_cost'].'/'.($dzn*$row[csf('total_set_qnty')]).')*'.$tot_pic_qty;
					
					
					
					$margin_parcent=($margin/$value)*100;
					$EPM=number_format(($cmValue/$tot_pic_qty)/($row[csf('set_smv')]/$row[csf('total_set_qnty')]),4);
					if($margin_parcent<5){
						$total_tmsc+=$tmsc;
						$total_cmValue+=$cmValue;
						$total_cm_cost+=$cm_cost;
						$total_margin+=$margin;
						
						
						$i++;
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF"; 
						?>	
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td align="center"><? echo $i;?></td>
                            <td><? echo $team_leader_name_arr[$row[csf('team_leader')]]; ?></td>
							<td><? echo $row[csf('job_no')]; ?></td>
							<td><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
							<td><? echo $row[csf('style_ref_no')]; ?></td>
							<td align="right" hidden ><? echo $row[csf('set_smv')];?></td>
                            <td align="right" hidden ><? echo $marginDataArr[$row[csf('job_no')]]['sew_effi_percent'];?></td>
                            <td align="right"><? echo number_format($tot_pic_qty,0); $total_po_qty+=$tot_pic_qty ; ?></td>
							<td align="right" hidden >
								<? 
								$tot_smv=($row[csf('set_smv')]*$row[csf('job_quantity')]); 
								echo number_format($tot_smv,0); 
								$grund_tot_smv+=$tot_smv; 
								?>
							</td>
							<td align="right" hidden ><?php echo number_format($avg_unit_price,2); ?></td>
							<td align="right">
								<?php 
								echo number_format($value,0);
								$total_value+= $value;
								?>
							</td>
							<td align="right"><? echo number_format($tmsc,0);?></td>
							<td align="right" title="<? echo $cmValueHtml;?>"><? $cmValue=($value-$tmsc);echo number_format($cmValue,0);?></td>
							<td align="right"><? echo number_format($cm_cost,0);?></td>
							<td align="right"><? echo number_format($margin,0);?></td>
							<td align="right" bgcolor="#FFFF99" style="color:#F00"><? echo number_format(($margin/$value)*100,2);?> %</td>
							<td align="right" title="<? echo $htmCpm;?>"><? echo number_format($cpm,3);?></td>
                            <td align="right"><? echo number_format($EPM,3); ?></td>
                            <td align="center" hidden ><? echo change_date_format($marginDataArr[$row[csf('job_no')]]['costing_date']);?></td>
							<td hidden ><? echo $dealing_merchant_arr[$row[csf('dealing_marchant')]]; ?></td>
							<td align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                            <td><? echo $user_arr[$last_app_data[$row[csf('job_no')]]]; ?></td>
							<td><? echo $user_arr[$higher_auth_id_arr[$row[csf('job_no')]]]; ?></td>
						</tr>
						<?
						$flag=1;
					 }
				}
				?> 
			</tbody>         
			<tfoot style="background-color:#CCC">
				<th align="right" colspan="5"><b>Total :</b></th>
				<th hidden >&nbsp;</th>
				<th hidden >&nbsp;</th>
				<th align="right"><? echo number_format($total_po_qty,0); $total_po_qty=0;?></th>
				<th align="right" hidden ><? echo number_format($grund_tot_smv,0); $grund_tot_smv=0;?></th>
				<th hidden >&nbsp;</th>
				<th align="right"><?  echo number_format($total_value,0); $total_value=0;?></th>
				<th><? echo number_format($total_tmsc,0); $total_tmsc=0;?></th>
				<th><? echo number_format($total_cmValue,0); $total_cmValue=0;?></th>
				<th><? echo number_format($total_cm_cost,0); $total_cm_cost=0;?></th>
				<th><? echo number_format($total_margin,0); $total_margin=0;?></th>
				<th bgcolor="#FFFF99">&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th hidden >&nbsp;</th>
				<th hidden >&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tfoot>
		</table>
		<?

		$mail_item=12;
		$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=$mail_item and b.mail_user_setup_id=c.id and a.company_id=$compid AND a.MAIL_TYPE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";

	
		
		$mail_sql=sql_select($sql);
		foreach($mail_sql as $row)
		{
			if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
		}
		
		$subject="Bellow 5% Profitability Order List";
		$message="";
		$message=ob_get_contents();
		ob_clean();
		$header=mailHeader();




	if($_REQUEST['isview']==1){
		if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
		echo $message;
	}
	else{
		echo  sendMailMailer( $to, $subject, $message, '','' );
	}
	
//exit();

}

?> 

<!-- if,to,exit comment -->