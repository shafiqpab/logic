<?php
ini_set('memory_limit','8024M');

date_default_timezone_set("Asia/Dhaka");

require_once('../includes/common.php');
require_once('../mailer/class.phpmailer.php');

echo load_html_head_contents("Mail Recipient_group","../", 1, 1, $unicode,1,1); 
?>

</head>

<body >
<div >
<? 

$company_library = return_library_array( "select id, company_short_name from lib_company where status_active=1 and is_deleted=0", "id", "company_short_name",$con);

$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");

 $conversion_rate=return_field_value("conversion_rate","currency_conversion_rate","is_deleted=0 and status_active=1 and id=(select max(id) from currency_conversion_rate where currency=2 and is_deleted=0 and status_active=1)","",$con);

 $strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
 $current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s", $strtotime),0))),'','',1);
 $previous_date= change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
 

	$start_date=str_replace("'","",trim($current_date));
	$end_date=str_replace("'","",trim($current_date));
	
	foreach($company_library as  $company_id=>$company_name)
	{		
	



		$cbo_company_id= $company_id;
		
		

		if($cbo_company_id==0)
			$cbo_company_cond="";
		else
			$cbo_company_cond=" and a.company_id=$cbo_company_id";
		
		$from_date=$txt_date_from;
		if(str_replace("'","",$txt_date_to)=="") $to_date=$from_date; else $to_date=$txt_date_to;
		$date_con="";
		if($from_date!="" && $to_date!="") $date_con=" and a.receive_date between '$from_date' and '$to_date'";
		

		if ($db_type == 0) {
			$date_cond = "and a.receive_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and a.receive_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}

		$sql="SELECT sum( b.grey_receive_qnty ) as  grey_receive_qnty, b.machine_no_id,b.shift_name
		from inv_receive_master a,pro_grey_prod_entry_dtls b
		where a.id=b.mst_id and a.entry_form=2 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $cbo_company_cond $date_cond
		group by b.machine_no_id,b.shift_name ";
		// echo "<pre>";
		// echo $sql;
		// echo "</pre>";
		$result=sql_select($sql);

		$machine_data=sql_select("select id, machine_no, prod_capacity, machine_group from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 $lib_mc_cond");
		$mcpacity_arr=return_library_array( "select machine_no, prod_capacity from lib_machine_name where category_id=1", "machine_no", "prod_capacity"  );
		$machine_details=array();
		foreach($machine_data as $row)
		{
			$machine_details[$row[csf('id')]]['no']=$row[csf('machine_no')];
			$machine_details[$row[csf('id')]]['prod_capacity']=$row[csf('prod_capacity')];
			$machine_details[$row[csf('id')]]['machine_group']=$row[csf('machine_group')];
			if($row[csf('machine_no')]!="") $total_machine[$row[csf('id')]]=$row[csf('id')];
		}
		unset($machine_data);

		$machine_group_wise=array();

		foreach ($result as $row) {
			$machine_group=$machine_details[$row[csf('machine_no_id')]]['machine_group'];
			$machine_group_wise[$machine_group][$row[csf('shift_name')]]['grey_receive_qnty']+=$row[csf('grey_receive_qnty')];
			
		}

		// echo "<pre>";
		// print_r($machine_group_wise);
		// echo "</pre>";

		
		$mcpacity_arr=return_library_array( "select machine_no, prod_capacity from lib_machine_name where category_id=1", "machine_no", "prod_capacity"  );

		$machineTo_date=$to_date;
		$shift_details=array();
		
		

		$mc_no_arr=return_library_array( "select id,machine_no from lib_machine_name", "id", "machine_no"  );
		$mc_group_arr=return_library_array( "select id,machine_group from lib_machine_name", "id", "machine_group"  );
		

		
		$group_capacity_arr=return_library_array( "select machine_group, sum(prod_capacity) as capacity from lib_machine_name where category_id=1 and status_active=1 group by machine_group", "machine_group", "capacity"  );


		$mrr_rate_sql = sql_select("select sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction
	      	where status_active=1 and is_deleted=0 and item_category=1 and transaction_type in(1,4,5) ");
		$mrr_rate_arr = array();
		$totalIssue =0;
		$avg_rate=0;
		foreach ($mrr_rate_sql as $row) 
		{
		   $avg_rate   = $row[csf("cons_amount")] / $row[csf("cons_quantiy")];
		   break;
		}
		unset($mrr_rate_sql);

		if ($db_type == 0)
		{
			$exchange_rate = return_field_value("conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1 and is_deleted=0 order by id DESC limit 0,1");
			$to_date = change_date_format($end_date, 'yyyy-mm-dd');
		}
		else if ($db_type == 2)
		{
			$exchange_rate = return_field_value("conversion_rate", "(SELECT conversion_rate FROM currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 ORDER BY id DESC)", "ROWNUM = 1");
			$to_date = change_date_format($end_date, '', '', 1);
		}

		if ($to_date != "")
			$mrr_date_cond = " and a.transaction_date<='$to_date'";
		if ($to_date != "")
			$rcv_date_cond = " and b.transaction_date<='$to_date'";

		if ($cbo_company_name == 0) {
			$company_cond_mrr = "";
		} else {
			$company_cond_mrr = " and a.company_id=$cbo_company_name";
		}

		$issue_qnty_arr = sql_select("select sum( b.issue_qnty) as issue_qnty from  inv_transaction a,  inv_mrr_wise_issue_details b where a.id=b.issue_trans_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type in(2,3,6) and a.item_category=1 $company_cond_mrr $mrr_date_cond");
		$mrr_issue_qnty_arr = array();
		foreach ($issue_qnty_arr as $row) {
			$totalIssue= $row[csf("issue_qnty")];
			break;
		}
		unset($issue_qnty_arr);

		if ($cbo_company_id == 0) {
				$company_cond = "";
		} else {
			$company_cond = " and b.company_id=$cbo_company_id";
		}

		
		
		if ($end_date != "")
			$rcv_date_cond = " and b.transaction_date<='$to_date'";

		if ($db_type == 0)
	        {
		      	$sql_stock = "select sum(b.cons_quantity) as cons_quantity
		      	from product_details_master a, inv_transaction b
		      	where a.id=b.prod_id and a.item_category_id=1 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.transaction_type in(1,4) $company_cond   $rcv_date_cond
		      	union all
		      	select sum(b.cons_quantity) as cons_quantity
		      	from product_details_master a, inv_transaction b, inv_item_transfer_mst c
		      	where a.id=b.prod_id and b.mst_id=c.id and a.item_category_id=1 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.transaction_type in(5) and c.transfer_criteria=1 $company_cond   $rcv_date_cond
		      	";
	        }
	        else
	        {
		      $sql_stock = "select  sum(b.cons_quantity) as cons_quantity
		      	from product_details_master a, inv_transaction b left join wo_yarn_dyeing_mst d on b.pi_wo_batch_no=d.id
		      	where a.id=b.prod_id and a.item_category_id=1 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.transaction_type in(1,4) $company_cond $rcv_date_cond
		      	union all
		      	select  sum(b.cons_quantity) as cons_quantity
		      	from product_details_master a, inv_transaction b left join wo_yarn_dyeing_mst d on b.pi_wo_batch_no=d.id, inv_item_transfer_mst c
		      	where a.id=b.prod_id and b.mst_id=c.id and a.item_category_id=1 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.transaction_type in(5) and c.transfer_criteria=1 $company_cond  $rcv_date_cond
		      	";
	        }
	        //echo $sql_stock;
	        $result_stock = sql_select($sql_stock);
	        $totalRcv=0;
	        foreach ($result_stock as $row) {
	        	$totalRcv+=$row[csf('cons_quantity')];
	        }

	    //echo "<pre>".$avg_rate."</pre>";die;
		$stockInHand = $totalRcv - $totalIssue;
		$stock_value = $stockInHand * $avg_rate;
		$stock_value_usd = $stock_value / $exchange_rate;



		if ($cbo_company_id==0) $company_id =""; else $company_id =" and a.company_id='$cbo_company_id'";

		  $sql_qury="SELECT 
	        sum(case when a.transaction_type=1 and a.transaction_date<'".$to_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
	        sum(case when a.transaction_type=2 and a.transaction_date<'".$to_date."' then a.cons_quantity else 0 end) as iss_total_opening,
	        sum(case when a.transaction_type=3 and a.transaction_date<'".$to_date."' then a.cons_quantity else 0 end) as rcv_return_opening,
	        sum(case when a.transaction_type=4 and a.transaction_date<'".$to_date."' then a.cons_quantity else 0 end) as iss_return_opening,
	        sum(case when a.transaction_type=5 and a.transaction_date<'".$to_date."' then a.cons_quantity else 0 end) as transfer_in_opening,
	        sum(case when a.transaction_type=6 and a.transaction_date<'".$to_date."' then a.cons_quantity else 0 end) as transfer_out_opening,
	        sum(case when a.transaction_type in (1,4,5) and a.transaction_date<'".$to_date."' then a.cons_amount else 0 end) as total_rcv_value_opening,
	        sum(case when a.transaction_type in (2,3,6) and a.transaction_date<'".$to_date."' then a.cons_amount else 0 end) as total_issue_value_opening,
	        sum(case when a.transaction_type=1 and a.transaction_date  <'".$to_date."' then a.cons_quantity else 0 end) as receive,
	        sum(case when a.transaction_type=2 and a.transaction_date  <'".$to_date."' then a.cons_quantity else 0 end) as issue,
	        sum(case when a.transaction_type=3 and a.transaction_date  <'".$to_date."' then a.cons_quantity else 0 end) as rec_return,
	        sum(case when a.transaction_type=4 and a.transaction_date  <'".$to_date."' then a.cons_quantity else 0 end) as issue_return,
	        sum(case when a.transaction_type=5 and a.transaction_date  <'".$to_date."' then a.cons_quantity else 0 end) as transfer_in,
	        sum(case when a.transaction_type=6 and a.transaction_date  <'".$to_date."' then a.cons_quantity else 0 end) as transfer_out,
	        sum(case when a.transaction_type in (1,4,5) and a.transaction_date  <'".$to_date."' then a.cons_amount else 0 end) as total_rcv_value,
	        sum(case when a.transaction_type in (2,3,6) and a.transaction_date < '".$to_date."' then a.cons_amount else 0 end) as total_issue_value
	        from inv_transaction a, product_details_master b
	        where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $company_id 
	     

	        union all

	        SELECT   
	        sum(case when a.transfer_criteria=2 and a.transfer_date<'".$to_date."' then b.transfer_qnty else 0 end) as rcv_total_opening,
	        sum(case when a.transfer_criteria=2 and a.transfer_date<'".$to_date."' then b.transfer_qnty else 0 end) as iss_total_opening,
	        sum(case when a.transfer_criteria=2 and a.transfer_date<'".$to_date."' then b.transfer_qnty else 0 end) as rcv_return_opening,
	        sum(case when a.transfer_criteria=2 and a.transfer_date<'".$to_date."' then b.transfer_qnty else 0 end) as iss_return_opening,
	        sum(case when a.transfer_criteria=2 and a.transfer_date<'".$to_date."' then b.transfer_qnty else 0 end) as transfer_in_opening,
	        sum(case when a.transfer_criteria=2 and a.transfer_date<'".$to_date."' then b.transfer_qnty else 0 end) as transfer_out_opening,
	        sum(case when a.transfer_criteria in (2) and a.transfer_date<'".$to_date."' then b.transfer_qnty else 0 end) as total_rcv_value_opening,
	        sum(case when a.transfer_criteria in (2) and a.transfer_date<'".$to_date."' then b.transfer_qnty else 0 end) as total_issue_value_opening,
	        sum(case when a.transfer_criteria=2 and a.transfer_date  <'".$to_date."' then b.transfer_qnty else 0 end) as receive,
	        sum(case when a.transfer_criteria=2 and a.transfer_date  <'".$to_date."' then b.transfer_qnty else 0 end) as issue,
	        sum(case when a.transfer_criteria=2 and a.transfer_date  <'".$to_date."' then b.transfer_qnty else 0 end) as rec_return,
	        sum(case when a.transfer_criteria=2 and a.transfer_date  <'".$to_date."' then b.transfer_qnty else 0 end) as issue_return,
	        sum(case when a.transfer_criteria=2 and a.transfer_date  <'".$to_date."' then b.transfer_qnty else 0 end) as transfer_in,
	        sum(case when a.transfer_criteria=2 and a.transfer_date  <'".$to_date."' then b.transfer_qnty else 0 end) as transfer_out,
	        sum(case when a.transfer_criteria in (2) and a.transfer_date  <'".$to_date."' then b.transfer_qnty else 0 end) as total_rcv_value,
	        sum(case when a.transfer_criteria in (2) and a.transfer_date  <'".$to_date."' then b.transfer_qnty else 0 end) as total_issue_value
	        from inv_item_transfer_mst a,inv_item_transfer_dtls b, product_details_master c
	        where a.id=b.mst_id and b.to_prod_id=c.id and a.transfer_criteria=2 and a.transfer_date   <'".$to_date."'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id
	          ";
	   // echo $sql_qury;
	    $result_clossing=sql_select($sql_qury);
	    $closingStock=0;
	    foreach ($result_clossing as $row) {
	    	$opening_rcv= ($row[csf('rcv_total_opening')]+$row[csf('iss_return_opening')]+$row[csf('transfer_in_opening')]);
			$opening_issue = $row[csf('iss_total_opening')]+$row[csf('rcv_return_opening')]+$row[csf('transfer_out_opening')];
			$opening= $opening_rcv - $opening_issue;

			 $receive = $row[csf('receive')];
			$issue_return = $row[csf('issue_return')];
			 $transfer_in = $row[csf('transfer_in')];
			$totalReceive=$receive+$issue_return+$transfer_in;

			$total_rcv_value = $row[csf('total_rcv_value')];

			$issue = $row[csf('issue')];
			$rec_return = $row[csf('rec_return')];
			$transfer_out = $row[csf('transfer_out')];
			$totalIssue=$issue+$rec_return+$transfer_out;
			$total_issue_value=$row[csf('total_issue_value')];
			$closingStock=$opening+$totalReceive-$totalIssue;
			break;
	    }


		$cnt=count($shift_name);
		$table_width=1200+$cnt*100; 
		$total=0;
	    $balance=0;
		ob_start();
		?>
	    <fieldset style="<?=$table_width+20; ?>">	
	        <table cellpadding="0" cellspacing="0" width="<?=$table_width; ?>" border="0" rules="all">
	            <tr class="form_caption">
	               <td align="center" width="100%"  style="font-size:16px"><strong><?=$company_arr[$cbo_company_id]; ?></strong></td>
	            </tr>
	            <tr class="form_caption">
	               <td align="center" width="100%"  style="font-size:16px"><strong><?=$report_title; ?></strong></td>
	            </tr>
	        </table>
	        <table class="rpt_table" border="1" rules="all"  cellpadding="0" cellspacing="0" width="<?=$table_width; ?>">
	            <thead >
	                <tr>
	                	<th width="35" rowspan="2">SL</th>
	                    <th width="110" rowspan="2">Machine Group</th>
	                    <th width="150" rowspan="2">Capacity </th>
	                    <th width="<?echo $cnt*100;?>" colspan="<? echo $cnt+1;?>">Knitting Production</th>
	                    <th width="150" rowspan="2">Balance</th>
	                    <th width="150" rowspan="2">Today Yarn Stock</th>
	                    <th width="150" rowspan="2">Value (USD)</th>
	                    <th width="150" rowspan="2">Today Grey Fabric Stock</th>
	                    <th width="150" rowspan="2">Value</th>
	                    <th  rowspan="2">Remarks</th>
	                   
	                </tr>
	                <tr>
	                	
						<?
						foreach($shift_name as $key=>$val)
						{
							
							?>
							<th width="90"><? echo $val;?></th>
							

							<?
							
						}
						?>
						
	                	<th width="100">Total</th>
	                </tr>

	            </thead>
	       
	        
	           <tbody>
	            <?php 

	            	$i=0;
	            	$total_capacity=0;
	            	
	            	$shift_total_arr=array();
	            	$rowspan=count($machine_group_wise);
	            	foreach ($machine_group_wise as $machine_group => $group_data) 
	            	{
	            		$i++;
	            		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	            			?>

	            			<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
	            				<td><?php echo $i; ?></td>
	            				<td><?php echo $machine_group; ?></td>
	            				<td align="right"><?php echo number_format($group_capacity_arr[$machine_group],2); ?></td>
	            				<?php 
	            					$c_total=0;
	            					$total_capacity+=$group_capacity_arr[$machine_group];
	            					foreach($shift_name as $key=>$val)
									{
										$shift_total_arr[$key]+=$group_data[$key]['grey_receive_qnty'];
										?>

										<td align="right"><?php echo number_format($group_data[$key]['grey_receive_qnty'],2); $c_total+=$group_data[$key]['grey_receive_qnty']; ?></td>
										<?
									}
									$total+=$c_total;
	            				 ?>
	            				 <td align="right"><?php echo number_format($c_total,2); ?></td>
	            				 <td align="right"><?php echo number_format($group_capacity_arr[$machine_group]-$c_total,2); 
	            				 	$balance+=$group_capacity_arr[$machine_group]-$c_total;
	            				 ?></td>


	            				 <?php if($i==1){ ?>
	            					 <td align="right" rowspan="<?php echo $rowspan; ?>"><?php echo number_format($stockInHand,2); ?></td>
	            					 <td align="right" rowspan="<?php echo $rowspan; ?>"><?php echo number_format($stock_value_usd,2); ?></td>
	            					 
	            					 <td align="right" rowspan="<?php echo $rowspan; ?>"><?php echo number_format($closingStock,2); ?></td>
	            					 <td align="right" rowspan="<?php echo $rowspan; ?>"></td>
	            					 <td align="right" rowspan="<?php echo $rowspan; ?>"></td>

	            				<? } ?>
	            			</tr>

	            			<?
	            		
	            	}
	             ?>
				
				</tbody>
				 <tfoot >
				 <tr>
				 	<td align="right" colspan="2">Total</td>
				 	<td align="right"><?php echo number_format($total_capacity,2); ?></td>
				 	<?php 
				 		foreach($shift_name as $key=>$val)
						{
							echo "<td align='right'>".number_format($shift_total_arr[$key],2)."</td>"; 

						}
					?>
					<td align="right"><?php echo number_format($total,2); ?></td>
					<td align="right"><?php echo number_format($balance,2); ?></td>
					<td colspan="5"></td>
				 </tr>
				 	
				 	
				 </tfoot>
	           </table>
	        
	        
	    </fieldset>
		


		<?
		}


	$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=18 and b.mail_user_setup_id=c.id and a.company_id=$compid AND a.MAIL_TYPE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	$mail_sql2=sql_select($sql2, '', '', '', $con);
	foreach($mail_sql2 as $row)
	{
		if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}


	$subject="Daily Report of ( Date :".date("d-m-Y", strtotime($previous_date)).")";
	$message="";
	$message_3=ob_get_contents();
	ob_clean();
	$header=mail_header();
	
	$message=$message_1_1.'<br>'.$message_3.'<br>'.$message_1_2.'<br>'.$message_1_3.'<br>'.$message_2;

	//if($to!=""){echo send_mail_mailer( $to, $subject, $message, $from_mail );}
	if($_REQUEST['isview']==1){
		echo $message;
	}
	else{
		if($to!=""){echo send_mail_mailer( $to, $subject, $message, $from_mail );}
	}
		
?>
</div>

</body> 

<script src="../includes/functions_bottom.js" type="text/javascript"></script>
 
</html>










