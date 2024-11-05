<?php
date_default_timezone_set("Asia/Dhaka");

    require_once('../includes/common.php');
   // require_once('../mailer/class.phpmailer.php');
    require_once('setting/mail_setting.php');

    $company_library    = return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
    $country_arr    = return_library_array( "select id, country_name from lib_country", "id", "country_name");



    $previous_date= date('d-M-Y', strtotime("-1 day"));
    $current_date = date('d-M-Y', strtotime("-1 day"));
    $previous_3month_date = change_date_format(date('d-M-Y H:i:s', strtotime('-180 day', strtotime($current_date))),'','',1); 

    $a=mktime(0, 0, 0, date("m"), date("d")-1, date("Y"));


	$str_cond	=" and insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
	$str_cond_a	=" and a.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
     




foreach($company_library as $compid=>$compname) 
{

		$party_lib_array = return_library_array("select a.id,a.buyer_name from lib_buyer a where a.is_deleted=0 and a.status_active=1","id","buyer_name");

		ob_start();

		?>

		<table width="1050"  cellspacing="0" border="0">
			<tr>
				<td colspan="" align="center">
					<strong>Yesterday Total Activities For Subcontract (Dyeing)</strong>
				</td>
			</tr>
			<tr>
				<td colspan="" align="center">
					<strong><? echo $prevDay; ?></strong>
				</td>
			</tr>

			<tr>
				<td colspan="" align="center">
					<strong>Company Name: <?php  echo $company_library[$compid]; ?></strong>
				</td>
			</tr>

			<tr>
				<td colspan="" align="center">
					<? 
					$sql_address=sql_select("select id,plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where status_active=1 and is_deleted=0 and id=$compid"); 
					foreach($sql_address as $result)
					{
						echo $result[csf('plot_no')];  
						echo $result[csf('level_no')];
						echo $result[csf('road_no')]; 
						echo $result[csf('block_no')]; 
						echo $result[csf('city')];
						echo $result[csf('zip_code')];  
						echo $result[csf('province')];
						echo $country_arr[$result[csf('country_id')]];
						echo $result[csf('email')];
						echo $result[csf('website')];
					}
					?>              
				</td>
			</tr>
			<tr>
				<td colspan="" align="center">
					<strong>Date: <?php $prv_date=explode(" ",$previous_date); echo date("d-m-Y", strtotime($prv_date[0])); ?></strong>
				</td>
			</tr>
		</table>
		<!-- Gray Receive Status Creation Status -->
		<table width="600"  cellspacing="0" border="1" rules="all" class="rpt_table">
			<caption><strong>Grey Receive Status</strong></caption>
			<thead style="background-color:#CCC">
				<tr align="center">
					<th width="35">Sl</th>
					<th width="150">Party</th>
					<th width="110">Receive Qty</th>
			<!-- <th width="110">Avg Rate</th>
				<th width="110">Amount</th>-->
				<th>Remarks</th>
			</tr>
		</thead>
		<tbody>

			<?
			//and a.subcon_date='$pre_orcl_format_date'
			//and a.subcon_date='$pre_orcl_format_date' condition last date
			//$sql_material_receive=sql_select("select a.party_id,sum(b.quantity) as quantity,b.rate from sub_material_mst a,sub_material_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$compid  and a.id=b.mst_id and a.subcon_date='$previous_date' group by a.party_id,b.rate");
			$sql_material_receive=sql_select("select a.party_id,sum(b.quantity) as quantity,b.rate from sub_material_mst a,sub_material_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2) and b.is_deleted=0 and a.company_id=$compid  and a.id=b.mst_id  AND a.insert_date BETWEEN DATE_ADD( CURDATE( ) , INTERVAL -1 DAY )  AND CURDATE() group by a.party_id,b.rate");
			$i=1;
			$total_rec_qty='';
			$total_amount='';
			foreach($sql_material_receive as $result)
			{
				//$amount 		=$result[csf('quantity')]*$result[csf('rate')];
				//$avg_rate 		=($amount/$result[csf('quantity')]);
				$total_rec_qty 	+=$result[csf('quantity')];
				$total_amount 	+=$amount;
				?>
				<tr>
					<td> <? echo $i++; ?></td>
					<td> <? echo $party_lib_array[$result[csf('party_id')]]; ?></td>
					<td align="right"> <? echo number_format($result[csf('quantity')],2); ?></td>
				<!-- <td align="right"> <? //echo  number_format($avg_rate,1); ?></td>
					<td align="right"> <? //echo number_format($amount,2); ?></td>-->
					<td> <?  ?></td>
				</tr>
				<?
			}
			?>
		</tbody>
		<tfoot style="background-color:#CCC">
			<th align="right" colspan="2"><b>Total :</b></th>
			<th align="right"><?  echo number_format($total_rec_qty,2); ?></th>
			<th  colspan="2">&nbsp; </th>
	<!-- <th align="right"><? //echo number_format($total_amount,2); ?></th>
		<th>&nbsp; </th>-->

	</tfoot>
	</table>
	<!-- Batch Creation Status -->
	<table width="600"  cellspacing="0" border="1" rules="all" class="rpt_table">
	<caption><strong>Batch Creation Status</strong></caption>
	<thead style="background-color:#CCC">
		<tr align="center">
			<th width="35">Sl</th>
			<th width="150">Party</th>
			<th width="110">Batch Qty</th>
			<th>Remarks</th>
		</tr>
	</thead>
	<tbody>

		<?
			//and a.batch_date='$previous_date'
			//print_r($get_party_by_po_arr);
			//and a.batch_date='$pre_orcl_format_date' condition last date
		$sql_batch_creation=sql_select("SELECT sum( b.batch_qnty ) AS batch_qnty, c.party_id FROM pro_batch_create_mst a, pro_batch_create_dtls b, subcon_ord_mst c,subcon_ord_dtls d WHERE a.status_active =1 AND a.is_deleted =0 AND b.status_active =1 AND b.is_deleted =0 AND a.company_id =$compid AND a.entry_form =36 AND a.id = b.mst_id  AND b.po_id = d.id and c.subcon_job=d.job_no_mst  AND a.insert_date BETWEEN DATE_ADD( CURDATE( ) , INTERVAL -1 DAY )  AND CURDATE()  GROUP BY c.party_id");
			//$sql_batch_creation=sql_select("select sum(b.batch_qnty)as batch_qnty from pro_batch_create_mst a,pro_batch_create_dtls b,subcon_ord_mst c where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$compid and a.entry_form=36 and a.id=b.mst_id  $str_cond_a");
			//$sql_batch_creation=sql_select("select sum(b.batch_qnty)as batch_qnty,c.party_id from pro_batch_create_mst a,pro_batch_create_dtls b,subcon_ord_mst c where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$compid and a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id  $str_cond_a group by c.party_id");
		//echo "SELECT sum( b.batch_qnty ) AS batch_qnty, c.party_id FROM pro_batch_create_mst a, pro_batch_create_dtls b, subcon_ord_mst c,subcon_ord_dtls d WHERE a.status_active =1 AND a.is_deleted =0 AND b.status_active =1 AND b.is_deleted =0 AND a.company_id =2 AND a.entry_form =36 AND a.id = b.mst_id  AND b.po_id = d.id and c.subcon_job=d.job_no_mst  $str_cond_a   GROUP BY c.party_id";
		$i=1;
		$total_batch_qty='';
		foreach($sql_batch_creation as $result)
		{
			$total_batch_qty +=$result[csf('batch_qnty')];;
			?>
			<tr>
				<td> <? echo $i++; ?></td>
				<td> <? echo $party_lib_array[$result[csf('party_id')]]; ?></td>
				<td align="right"> <? echo number_format($result[csf('batch_qnty')],2); ?></td>
				<td> <?  ?></td>
			</tr>
			<?
		}
		?>

	</tbody>
	<tfoot style="background-color:#CCC">
		<th align="right" colspan="2"><b>Total :</b></th>
		<th align="right"><?  echo  number_format($total_batch_qty,2); ?></th>
		<th  colspan="2">&nbsp; </th>
	</tfoot>
	</table>
	<!-- Dyeing Production status -->
	<table width="600"  cellspacing="0" border="1" rules="all" class="rpt_table">
	<caption><strong>Dyeing Production status</strong></caption>
	<thead style="background-color:#CCC">
		<tr align="center">
			<th width="35">Sl</th>
			<th width="150">Party</th>
			<th width="110">Prod. Qty</th>
			<th>Remarks</th>
		</tr>
	</thead>
	<tbody>

		<?

		//and a.process_end_date='$previous_date'
		//echo "select a.load_unload_id,sum(b.batch_weight) as batch_weight from pro_fab_subprocess a, pro_batch_create_mst b where b.id=a.batch_id and a.company_id like '$compid' and a.load_unload_id=2 and a.entry_form=38 and a.result=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.entry_form=38  group by a.load_unload_id";	

			//$sql_dyeing_prod=sql_select("select sum(b.batch_weight) as batch_weight,d.party_id from pro_fab_subprocess a, pro_batch_create_mst b,pro_batch_create_dtls c,subcon_ord_mst d where b.id=a.batch_id and a.company_id = '$compid' and a.load_unload_id=2 and a.entry_form=38 and b.entry_form=36 and a.result=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.id=c.mst_id and c.po_id=d.id  AND a.insert_date BETWEEN DATE_ADD( CURDATE( ) , INTERVAL -1 DAY )  AND CURDATE() group by d.party_id");   

		$sql_dyeing_prod=sql_select("select d.party_id, SUM(b.batch_qnty) AS sub_batch_qnty from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a, pro_fab_subprocess f, lib_color g where f.company_id='$compid' and f.batch_id=a.id and a.entry_form=36 and g.id=a.color_id and a.id=b.mst_id and f.entry_form=38 and f.load_unload_id=2 and f.result=1 and a.batch_against in(1,2) and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 AND f.insert_date BETWEEN DATE_ADD( CURDATE( ) , INTERVAL -1 DAY )  AND CURDATE() and f.result=1 GROUP BY d.party_id");

		$i=1;
		$total_dyeing_batch_qty='';
		foreach($sql_dyeing_prod as $result)
		{
			$total_dyeing_batch_qty += $result[csf('sub_batch_qnty')];
			?>
			<tr>
				<td> <? echo $i++; ?></td>
				<td> <? echo $party_lib_array[$result[csf('party_id')]]; ?></td>
				<td align="right"> <? echo number_format($result[csf('sub_batch_qnty')],2); ?></td>
				<td> <?  ?></td>
			</tr>
			<?
		}
		?>

	</tbody>
	<tfoot style="background-color:#CCC">
		<th align="right" colspan="2"><b>Total :</b></th>
		<th align="right"><?  echo  number_format($total_dyeing_batch_qty,2); ?></th>
		<th  colspan="2">&nbsp; </th>
	</tfoot>
	</table>
	<!-- Finishing Production status -->
	<table width="600"  cellspacing="0" border="1" rules="all" class="rpt_table">
	<caption><strong>Finishing Production status</strong></caption>
	<thead style="background-color:#CCC">
		<tr align="center">
			<th width="35">Sl</th>
			<th width="150">Party</th>
			<th width="110">Prod. Qty</th>
			<th>Remarks</th>
		</tr>
	</thead>
	<tbody>

		<?

			//and a.product_date='$pre_orcl_format_date' condition last date
		$sql_fnsh_prod=sql_select("select a.party_id,sum(b.product_qnty) as product_qnty from subcon_production_mst a,subcon_production_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$compid and a.id=b.mst_id AND a.insert_date BETWEEN DATE_ADD( CURDATE( ) , INTERVAL -1 DAY )  AND CURDATE() group by a.party_id");

		$i=1;
		$total_fnsh_prod_qty='';
		foreach($sql_fnsh_prod as $result)
		{
			$total_fnsh_prod_qty+= $result[csf('product_qnty')];
			?>
			<tr>
				<td> <? echo $i++; ?></td>
				<td> <? echo $party_lib_array[$result[csf('party_id')]]; ?></td>
				<td align="right"> <? echo number_format($result[csf('product_qnty')],2); ?></td>
				<td> <?  ?></td>
			</tr>
			<?
		}
		?>

	</tbody>
	<tfoot style="background-color:#CCC">
		<th align="right" colspan="2"><b>Total :</b></th>
		<th align="right"><?  echo  number_format($total_fnsh_prod_qty,2); ?></th>
		<th  colspan="2">&nbsp; </th>
	</tfoot>
	</table>
	<!-- Finish Fabric Delevery status -->
	<table width="600"  cellspacing="0" border="1" rules="all" class="rpt_table">
	<caption><strong>Finish Fabric Delivery status</strong></caption>
	<thead style="background-color:#CCC">
		<tr align="center">
			<th width="35">Sl</th>
			<th width="150">Party</th>
			<th width="110">Del. Qty</th>
		<!-- <th width="110">Avg Rate</th>
			<th width="110">Amount</th>-->
			<th>Remarks</th>
		</tr>
	</thead>
	<tbody>

		<?
		//and a.delivery_date='$previous_date'
		//and a.delivery_date='$pre_orcl_format_date' condition last date
		$sql_fnsh_feb_del=sql_select("select a.party_id,sum(b.delivery_qty) as delivery_qty from subcon_delivery_mst a,subcon_delivery_dtls b,subcon_ord_dtls c, subcon_ord_mst d where a.status_active=1 and a.is_deleted=0 and a.company_id=$compid and a.id=b.mst_id and b.order_id=d.id and d.subcon_job=c.job_no_mst AND a.insert_date BETWEEN DATE_ADD( CURDATE( ) , INTERVAL -1 DAY )  AND CURDATE() group by a.party_id");
		//select a.party_id,b.remarks,sum(b.delivery_qty)as qty from subcon_delivery_msta,subcon_delivery_dtls b where a.status_active=1 and a.is_deleted=0 and a.company_id=3 and a.id=b.mst_id group by a.party_id,b.remarks; 

		$i=1;
		$total_fnsh_feb_del_qty='';
		//$total_fnsh_feb_del_amount='';
		foreach($sql_fnsh_feb_del as $result)
		{
			//$amount=$result[csf('delivery_qty')]*$result[csf('rate')];
			//$avg_rate=($result[csf('delivery_qty')]/$amount);
			$total_fnsh_feb_del_qty +=$result[csf('delivery_qty')];
			//$total_fnsh_feb_del_amount 	+=$amount;
			?>
			<tr>
				<td> <? echo $i++; ?></td>
				<td> <? echo $party_lib_array[$result[csf('party_id')]]; ?></td>
				<td align="right"> <? echo number_format($result[csf('delivery_qty')],2); ?></td>
				<!--<td align="right"> <? //echo number_format($avg_rate,1); ?></td>
					<td align="right"> <? //echo number_format($amount,2); ?></td>-->
					<td> <?  ?></td>
				</tr>
				<?
			}
			?>
		</tbody>
		<tfoot style="background-color:#CCC">
			<th align="right" colspan="2"><b>Total :</b></th>
			<th align="right"><?  echo number_format($total_fnsh_feb_del_qty,2); ?></th>
	<!--  <th>&nbsp; </th>
		<th align="right"><? //echo number_format($total_fnsh_feb_del_amount,2); ?></th> -->
		<th  colspan="2">&nbsp; </th>

	</tfoot>
	</table>
	<!-- Dyeing And Finishing Bill Issue -->
	<table width="800"  cellspacing="0" border="1" rules="all" class="rpt_table">
	<caption><strong>Dyeing And Finishing Bill Issue</strong></caption>
	<thead style="background-color:#CCC">
		<tr align="center">
			<th width="35">Sl</th>
			<th width="150">Party</th>
			<th width="110">Del. Qty</th>
			<th width="110">Avg Rate</th>
			<th width="110">Amount</th>
			<th>Remarks</th>
		</tr>
	</thead>
	<tbody>

		<?
		//and a.bill_date='$previous_date'
		//and a.bill_date='$pre_orcl_format_date' condition last date
		$sql_dyeing_bill_issue=sql_select("select a.party_id,sum(b.delivery_qty) as delivery_qty,sum(b.amount) as amount,b.rate from subcon_inbound_bill_mst a,subcon_inbound_bill_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$compid and a.id=b.mst_id AND a.insert_date BETWEEN DATE_ADD( CURDATE( ) , INTERVAL -1 DAY )  AND CURDATE() group by a.party_id");
		//echo "select a.party_id,sum(b.delivery_qty) as delivery_qty,sum(b.amount) as amount,b.rate from subcon_inbound_bill_mst a,subcon_inbound_bill_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$compid and a.id=b.mst_id AND a.insert_date BETWEEN DATE_ADD( CURDATE( ) , INTERVAL -1 DAY )  AND CURDATE() group by a.party_id";

		$i=1;
		$total_bill_issue_qty='';
		$total_bill_issue_amount='';
		foreach($sql_dyeing_bill_issue as $result)
		{
			//$amount=$result[csf('delivery_qty')]*$result[csf('rate')];
			$amount=$result[csf('delivery_qty')]*$result[csf('rate')];
			$avg_rate=$amount/($result[csf('delivery_qty')]);
			//$avg_rate=($result[csf('delivery_qty')]/$result[csf('amount')]);
			$total_bill_issue_qty +=$result[csf('delivery_qty')];
			$total_bill_issue_amount +=$result[csf('amount')];
			?>
			<tr>
				<td> <? echo $i++; ?></td>
				<td> <? echo $party_lib_array[$result[csf('party_id')]]; ?></td>
				<td align="right"> <? echo number_format($result[csf('delivery_qty')],2); ?></td>
				<td align="right"> <? echo number_format($avg_rate,2); ?></td>
				<td align="right"> <? echo number_format($result[csf('amount')],2) ?></td>
				<td> <?  ?></td>
			</tr>
			<?
		}
		?>
	</tbody>
	<tfoot style="background-color:#CCC">
		<th align="right" colspan="2"><b>Total :</b></th>
		<th align="right"><?  echo number_format($total_bill_issue_qty,2); ?></th>
		<th>&nbsp; </th>
		<th align="right"><? echo number_format($total_bill_issue_amount,2); ?></th>
		<th>&nbsp; </th>

	</tfoot>
	</table>
	<?
	$to="";	
	$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=9 AND a.MAIL_TYPE=1 and b.mail_user_setup_id=c.id and a.company_id=$compid and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";

	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{

	if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}

	$subject="Subcontract Dyeing";
	$message="";
	$message=ob_get_contents();
	ob_clean();
	$header=mailHeader();

	if($_REQUEST['isview']==1){
		echo $message;
	}
	else{
		if($to!="") echo sendMailMailer( $to, $subject, $message, $from_mail, $host, $user_name, $password, $smtp_port );
	}

 	
}




?> 