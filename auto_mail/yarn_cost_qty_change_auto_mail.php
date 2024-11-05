<?php

	date_default_timezone_set("Asia/Dhaka");
	
	// require_once('../mailer/class.phpmailer.php');
	require_once('../includes/common.php');
	require_once('setting/mail_setting.php');
	
	
	$company_library=return_library_array( "select id, company_short_name from lib_company where  status_active=1 and is_deleted=0", "id", "company_short_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	
	$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();

	$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d", $strtotime),0))),'','',1);
	$previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-2 day', strtotime($current_date))),'','',1); 
	
	$date_cond	=" and b.UPDATE_DATE between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
	
	
	
	$sql = "select b.JOB_NO,min(b.id) as id, b.count_id, b.copm_one_id, b.percent_one, b.copm_two_id, b.percent_two, b.color,b.type_id, min(b.cons_ratio) as cons_ratio, sum(b.cons_qnty) as cons_qnty, b.rate, sum(b.amount) as amount from wo_pre_cost_mst a,wo_pre_cost_fab_yarn_cost_dtls b where a.job_no=b.job_no and b.status_active=1 and b.is_deleted=0  and a.APPROVED=2 $date_cond  group by b.job_no,b.count_id, b.copm_one_id, b.percent_one, b.copm_two_id, b.percent_two, b.color,b.type_id, b.rate";//   and b.job_no='RpC-22-00159'
	//echo $sql;die;
	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
		$all_job_arr[$row[JOB_NO]]=$row[JOB_NO];
	}
	
	
	$sqlHis = "select b.JOB_NO,min(b.id) as id, b.count_id, b.copm_one_id, b.percent_one, b.copm_two_id, b.percent_two, b.color,b.type_id, min(b.cons_ratio) as cons_ratio, sum(b.cons_qnty) as cons_qnty, b.rate, sum(b.amount) as amount from WO_PRE_COST_FAB_YARN_CST_DTL_H b where b.status_active=1 and b.is_deleted=0 ".where_con_using_array($all_job_arr,1,'b.job_no')." group by b.job_no,b.count_id, b.copm_one_id, b.percent_one, b.copm_two_id, b.percent_two, b.color,b.type_id, b.rate  order by min(b.id)";
	
	//echo $sqlHis;
	
	$data_his_array=sql_select($sqlHis);
	$his_data_array=array();
	$all_job_arr=array();
	foreach($data_his_array as $row)
	{
		
		$all_job_arr[$row[JOB_NO]]=$row[JOB_NO];
		
		
		if($row[csf("percent_one")]==100)
			$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$color_library[$row[csf("color")]]." ".$yarn_type[$row[csf("type_id")]];
		else
			$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$composition[$row[csf("copm_two_id")]]." ".$row[csf("percent_two")]."% ".$color_library[$row[csf("color")]]." ".$yarn_type[$row[csf("type_id")]];
		
		$his_data_array[$row[JOB_NO]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]]['rate']=$row[csf("rate")];
		
		$his_data_array[$row[JOB_NO]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]]['amount']=$row[csf("amount")];
		
		$his_data_array[$row[JOB_NO]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]]['cons_qnty']=$row[csf("cons_qnty")];
		$his_data_array[$row[JOB_NO]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]]['item_descrition']=$item_descrition;

	}
	unset($data_his_array);
	asort($all_job_arr);
	
 	
	
	$gmtsitem_ratio_array=array();
	$gmtsitem_ratio_sql=sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where 1=1 ".where_con_using_array($all_job_arr,1,'job_no').""); 
	foreach($gmtsitem_ratio_sql as $gmtsitem_ratio_sql_row)
	{
		$gmtsitem_ratio_array[$gmtsitem_ratio_sql_row[csf('job_no')]][$gmtsitem_ratio_sql_row[csf('gmts_item_id')]]=$gmtsitem_ratio_sql_row[csf('set_item_ratio')];
	}
	
		
	
	$yarn_data_array=array();
	$sql_y=sql_select("select a.STYLE_REF_NO,a.buyer_name,c.item_number_id,c.order_quantity ,c.plan_cut_qnty,f.JOB_NO,f.color,f.count_id,f.copm_one_id,f.percent_one,f.copm_two_id,f.percent_two,f.type_id,f.cons_ratio,f.cons_qnty,f.avg_cons_qnty,f.rate,f.amount,e.requirment,g.costing_per,h.price_dzn   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_yarn_cost_dtls f,wo_pre_cost_mst g,wo_pre_cost_dtls h where g.job_no=b.job_no_mst and g.job_no=h.job_no and a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and a.job_no=f.job_no and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_cost_dtls_id ".where_con_using_array($all_job_arr,1,'a.job_no')." and e.cons !=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.status_active=1 and d.is_deleted=0");
	foreach($sql_y as $sql_y_r)
	{

		if($sql_y_r[csf("costing_per")]==1){$order_price_per_dzn=12; $costing_for=" DZN";}
		else if($sql_y_r[csf("costing_per")]==2){$order_price_per_dzn=1; $costing_for=" PCS";}
		else if($sql_y_r[csf("costing_per")]==3){$order_price_per_dzn=24; $costing_for=" 2 DZN";}
		else if($sql_y_r[csf("costing_per")]==4){$order_price_per_dzn=36; $costing_for=" 3 DZN";}
		else if($sql_y_r[csf("costing_per")]==5){$order_price_per_dzn=48; $costing_for=" 4 DZN";}
		$order_job_qnty=$sql_y_r[csf("job_quantity")];
		$avg_unit_price=$sql_y_r[csf("avg_unit_price")];
		
		$set_item_ratio=$gmtsitem_ratio_array[$sql_y_r[csf('job_no')]][$sql_y_r[csf('item_number_id')]];
		$cons_qnty = def_number_format((($sql_y_r[csf("requirment")]*$sql_y_r[csf("cons_ratio")]/100)*($sql_y_r[csf("plan_cut_qnty")]/($order_price_per_dzn*$set_item_ratio))),5,"");
		$avg_cons_qnty = def_number_format(($sql_y_r[csf("avg_cons_qnty")]*($sql_y_r[csf("plan_cut_qnty")]/($order_price_per_dzn*$set_item_ratio))),5,"");
		 $amount = def_number_format(($cons_qnty*$sql_y_r[csf("rate")]),5,"");
		 
		
		
		$yarn_data_array[$row[JOB_NO]][$sql_y_r[csf("count_id")]][$sql_y_r[csf("copm_one_id")]][$sql_y_r[csf("percent_one")]][$sql_y_r[csf("type_id")]][$sql_y_r[csf("color")]][$sql_y_r[csf("rate")]][qty]+=$cons_qnty;
		$yarn_data_array[$row[JOB_NO]][$sql_y_r[csf("count_id")]][$sql_y_r[csf("copm_one_id")]][$sql_y_r[csf("percent_one")]][$sql_y_r[csf("type_id")]][$sql_y_r[csf("color")]][$sql_y_r[csf("rate")]][avg_qnty]+=$avg_cons_qnty;
		$yarn_data_array[$row[JOB_NO]][$sql_y_r[csf("count_id")]][$sql_y_r[csf("copm_one_id")]][$sql_y_r[csf("percent_one")]][$sql_y_r[csf("type_id")]][$sql_y_r[csf("color")]][$sql_y_r[csf("rate")]][amount]+=$amount;
	
		$yarn_data_array[$row[JOB_NO]][$sql_y_r[csf("count_id")]][$sql_y_r[csf("copm_one_id")]][$sql_y_r[csf("percent_one")]][$sql_y_r[csf("type_id")]][$sql_y_r[csf("color")]][$sql_y_r[csf("rate")]][price_dzn]=$sql_y_r[csf("rate")];
		$buyer_name_arr[$row[JOB_NO]]=$sql_y_r[csf("buyer_name")];
		$style_ref_arr[$row[JOB_NO]]=$sql_y_r[csf("STYLE_REF_NO")];
	}
	unset($sql_y);

	
	
	

	$yarn_his_data_array=array();
	$yarn_his_data_arr=sql_select("select a.STYLE_REF_NO,a.buyer_name,c.item_number_id,c.order_quantity ,c.plan_cut_qnty,f.JOB_NO,f.color,f.count_id,f.copm_one_id,f.percent_one,f.copm_two_id,f.percent_two,f.type_id,f.cons_ratio,f.cons_qnty,f.avg_cons_qnty,f.rate,f.amount,e.requirment,g.costing_per,h.price_dzn   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,WO_PRE_COST_FAB_YARN_CST_DTL_H f,wo_pre_cost_mst g,wo_pre_cost_dtls h where g.job_no=b.job_no_mst and g.job_no=h.job_no and a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and a.job_no=f.job_no and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_cost_dtls_id ".where_con_using_array($all_job_arr,1,'a.job_no')." and e.cons !=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.status_active=1 and d.is_deleted=0");
	foreach($yarn_his_data_arr as $rows)
	{

		if($rows[csf("costing_per")]==1){$order_price_per_dzn=12; $costing_for=" DZN";}
		else if($rows[csf("costing_per")]==2){$order_price_per_dzn=1; $costing_for=" PCS";}
		else if($rows[csf("costing_per")]==3){$order_price_per_dzn=24; $costing_for=" 2 DZN";}
		else if($rows[csf("costing_per")]==4){$order_price_per_dzn=36; $costing_for=" 3 DZN";}
		else if($rows[csf("costing_per")]==5){$order_price_per_dzn=48; $costing_for=" 4 DZN";}
		$order_job_qnty=$rows[csf("job_quantity")];
		$avg_unit_price=$rows[csf("avg_unit_price")];
		
		$set_item_ratio=$gmtsitem_ratio_array[$rows[csf('job_no')]][$rows[csf('item_number_id')]];
		$cons_qnty = def_number_format((($rows[csf("requirment")]*$rows[csf("cons_ratio")]/100)*($rows[csf("plan_cut_qnty")]/($order_price_per_dzn*$set_item_ratio))),5,"");
		$avg_cons_qnty = def_number_format(($rows[csf("avg_cons_qnty")]*($rows[csf("plan_cut_qnty")]/($order_price_per_dzn*$set_item_ratio))),5,"");
		 $amount = def_number_format(($cons_qnty*$rows[csf("rate")]),5,"");
		 
		
		$yarn_his_data_array[$row[JOB_NO]][$rows[csf("count_id")]][$rows[csf("copm_one_id")]][$rows[csf("percent_one")]][$rows[csf("type_id")]][$rows[csf("color")]][$rows[csf("rate")]][qty]+=$cons_qnty;
		$yarn_his_data_array[$row[JOB_NO]][$rows[csf("count_id")]][$rows[csf("copm_one_id")]][$rows[csf("percent_one")]][$rows[csf("type_id")]][$rows[csf("color")]][$rows[csf("rate")]][avg_qnty]+=$avg_cons_qnty;
		$yarn_his_data_array[$row[JOB_NO]][$rows[csf("count_id")]][$rows[csf("copm_one_id")]][$rows[csf("percent_one")]][$rows[csf("type_id")]][$rows[csf("color")]][$rows[csf("rate")]][amount]+=$amount;
		$yarn_his_data_array[$row[JOB_NO]][$rows[csf("count_id")]][$rows[csf("copm_one_id")]][$rows[csf("percent_one")]][$rows[csf("type_id")]][$rows[csf("color")]][$rows[csf("rate")]][price_dzn]=$rows[csf("rate")];
	}
	unset($yarn_his_data_arr);


		
	  	
		
		$YarnAllocationQtyArr=return_library_array( "select JOB_NO,QNTY from INV_MATERIAL_ALLOCATION_MST where STATUS_ACTIVE=1 and IS_DELETED=0 ".where_con_using_array($all_job_arr,1,'JOB_NO')."", "JOB_NO", "QNTY"  );
		
 	
?>
        <div style="margin-top:15px">
        	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px;text-align:center;" rules="all">
		
        	<tr>
                <td colspan="12" align="center">
                    <h4>Asrotex Group</4><br />
                    <b><?= $previous_date;?></b><br />
                    <b>Budget Yarn Cost Change List</b>
                </td>
            </tr>
                
            <? foreach($all_job_arr as $job_no){ 
				if($YarnAllocationQtyArr[$row[JOB_NO]]>0){
			?>   
               
               
               <tr>
                    <td colspan="12" align="left">
                        <strong>Job No:</strong> <?=$job_no;?>
                        <strong>Buyer:</strong> <?= $buyer_library[$buyer_name_arr[$job_no]]; ?>
                        <strong>Style Ref. No:</strong> <?= $style_ref_arr[$job_no]; ?>
                    </td>
               </tr> 
                <tr>
                    <th bgcolor="#DDDDDD">Prev. Yarn Desc</th>
                    <th>Yarn Desc</th>
                    <th bgcolor="#DDDDDD">Prev.Yarn Qty</th> 
                    <th>Yarn Qty</th> 
					<th bgcolor="#DDDDDD">Prev.TTL Yarn Qty</th>
					<th>TTL Yarn Qty</th>
                    <th bgcolor="#DDDDDD">Prev. Rate</th>
                    <th>Rate</th>
                    <th bgcolor="#DDDDDD">Prev. Amount</th>
                    <th>Amount</th>
					<th bgcolor="#DDDDDD">Prev. TTL Amount</th>
					<th>TTL Amount</th>
                </tr>
			<?
		
			foreach( $data_array as $row )
            { 
				if($row[csf("percent_one")]==100)
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$color_library[$row[csf("color")]]." ".$yarn_type[$row[csf("type_id")]];
            	else
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$composition[$row[csf("copm_two_id")]]." ".$row[csf("percent_two")]."% ".$color_library[$row[csf("color")]]." ".$yarn_type[$row[csf("type_id")]];
 				
				$ttl_yarn_qty = $yarn_data_array[$row[JOB_NO]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['qty'];
				$rowavgcons_qnty = $yarn_data_array[$row[JOB_NO]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['qty'];
				$rowamount = $yarn_data_array[$row[JOB_NO]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['amount'];
				$price_dzn = $yarn_data_array[$row[JOB_NO]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['price_dzn'];
				
				
				$rate_his=$his_data_array[$row[JOB_NO]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]]['rate'];
				$amount_his=$his_data_array[$row[JOB_NO]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]]['amount'];
				
				
				
				$cons_qnty_his=$his_data_array[$row[JOB_NO]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]]['cons_qnty'];
				$item_descrition_his=$his_data_array[$row[JOB_NO]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]]['item_descrition'];


				$ttl_yarn_his_qty = $yarn_his_data_array[$row[JOB_NO]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$rate_his]['qty'];
				$rowamount_his = $yarn_his_data_array[$row[JOB_NO]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$rate_his]['amount'];

				
				
				
				if(($row[csf("rate")]!=$rate_his || $row[csf("cons_qnty")]!=$cons_qnty_his || $item_descrition_his!=$item_descrition || $ttl_yarn_his_qty!=$ttl_yarn_qty || $rowamount_his!=$rowamount) && $rate_his>0  && $YarnAllocationQtyArr[$row[JOB_NO]]>0){
				
				if(is_infinite($rowamount) || is_nan($rowamount)){$rowamount=0;}
			?>	 
                <tr>
                    <td bgcolor="#DDD"><? echo $item_descrition_his; ?></td>
                    <td><? echo $item_descrition; ?></td>
                    <td bgcolor="#DDDDDD" align="right"><? echo fn_number_format($cons_qnty_his,3); ?></td>
                    <td align="right"><? echo fn_number_format($row[csf("cons_qnty")],3); ?></td>
					<td bgcolor="#DDDDDD" align="right"><?= fn_number_format($ttl_yarn_his_qty,2); ?></td>
					<td align="right"><? echo fn_number_format($ttl_yarn_qty,2); ?></td>
                    <td bgcolor="#DDDDDD" align="right"><? echo fn_number_format($rate_his,3); ?></td>
                    <td align="right"><? echo fn_number_format($row[csf("rate")],3); ?></td>
					<td bgcolor="#DDDDDD" align="right"><? echo fn_number_format($amount_his,4); ?></td>
					<td align="right"><? echo fn_number_format($row[csf("amount")],4); ?></td>
                    <td bgcolor="#DDDDDD" align="right"><? echo fn_number_format($rowamount_his,2); ?></td>
                    <td align="right"><? echo fn_number_format($rowamount,2); ?></td>
                </tr>
            <? 
				}
			}
            }
			}
            ?>
        	</table>
      </div>

	

<?

	$to="";$message="";
	$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=92 AND a.MAIL_TYPE=1 and b.mail_user_setup_id=c.id ";
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}
	$header=mailHeader();
	
	$subject="Budget Yarn Cost Change Auto Mail";
	$message=ob_get_contents();
	ob_clean();
	//if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
	//echo $to.$message;
	if($_REQUEST['isview']==1){
		echo $message;
	}
	else{
		if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
	}

		
	
?>






</body>
</html>