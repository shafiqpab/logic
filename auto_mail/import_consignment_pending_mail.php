<?php

	date_default_timezone_set("Asia/Dhaka");
	
	// require_once('../mailer/class.phpmailer.php');
	require_once('../includes/common.php');
	require_once('setting/mail_setting.php');
	
	$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();

	$file = 'mail_log.txt';
	$current = file_get_contents($file);
	$current .= "Bill of Entry Overdue-Mail :: Date & Time: ".date("d-m-Y H:i:s", $strtotime)."\n";
	file_put_contents($file, $current);
 
	$company_library=return_library_array( "select id, company_name from lib_company where  status_active=1 and is_deleted=0", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$supplier_library = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	
	$mkt_team_lib = return_library_array("select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id","team_leader_name");	
	
	 
	$tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d", $strtotime),1))),'','',1);
	$day_after_tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d", $strtotime),2))),'','',1);
	$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d", $strtotime),0))),'','',1);
	$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-25 day', strtotime($current_date))),'','',1); 
	$actual_date="is null";
	
	
	
//$company_library=array(3=>$company_library[3]);

foreach($company_library as $compid=>$company_name)
{
	$comp_buyer=array();$lcArr=array();$mst_id_arr=array();	$pi_id_arr=array();	
	

 $sql="select a.lc_number,a.lc_value lc_value ,b.bill_of_entry_no, sum(b.document_value) document_value from com_btb_lc_master_details a, com_import_invoice_mst b ,com_import_invoice_dtls c
 where b.id=c.import_invoice_id and  a.id=c.btb_lc_id and (  b.document_value is null  or b.bill_of_entry_no is null )  group by a.lc_number,lc_value,b.bill_of_entry_no"; //and lc_number='194715050079'
 
	$sqlArrayResult=sql_select($sql);
	foreach($sqlArrayResult as $rows){
		if($rows[csf('lc_value')] > $rows[csf('document_value')] or $rows[csf('bill_of_entry_no')]==""){
		$lcArr[$rows[csf('lc_number')]]=$rows[csf('lc_number')];
		}
	}
	
	
	
	
	
	if($db_type==0)
	{	
		$date_diff="(DATEDIFF('".date('Y-m-d',time())."', lc_date))";
	}
	else
	{
		$date_diff="(to_date('".date('d-M-y',time())."', 'dd-MM-yy')- to_date(lc_date, 'dd-MM-yy'))";
	}
	
	
	
		if($db_type==2 && count($lcArr)>990)
		{
			$where_cond=" and (";
			$IdsArr=array_chunk($lcArr,990);
			foreach($IdsArr as $ids)
			{
				$where_cond.=" lc_number  in('".implode("','",$ids)."') or ";
			}
			$where_cond=chop($where_cond,'or ');
			$where_cond.=")";
		}
		else
		{
			$lcString = implode("','",$lcArr);
			$where_cond=" and  lc_number  in('$lcString')";
		}	
	
	$con = connect();
	execute_query("delete from tmp_poid where userid=99999");
	$data_array="select 	id,item_category_id,importer_id,supplier_id,pi_id,last_shipment_date,lc_expiry_date,lc_serial,lc_date,lc_value,lc_number,lc_category, $date_diff as datedef
	from com_btb_lc_master_details
	where importer_id=$compid $where_cond and lc_category in('01','02','05','06','11','12','15') and $date_diff > 60"; 
	$dataArrayResult=sql_select($data_array); //lc_date between '$prev_date' and '$current_date' and 
	foreach($dataArrayResult as $rows){
		$mst_id_arr[$rows[csf('id')]]=$rows[csf('id')];	
		$pi_id_arr[$rows[csf('pi_id')]]=$rows[csf('pi_id')];	
		foreach(explode(',',$rows[csf('pi_id')]) as $pi_id){
			$r_id2=execute_query("insert into tmp_poid (userid, poid,type) values (99999,'".$pi_id."',1".")");
		}
	}
	oci_commit($con);	
	
//$pi_number_arr=return_library_array( "select id, pi_number from com_pi_master_details  where id in(".implode(',',$pi_id_arr).")", "id", "pi_number"  );

//$item_cat_id_arr=return_library_array( "select id, item_category_id from com_pi_master_details  where id in(".implode(',',$pi_id_arr).")", "id", "item_category_id"  );
	
	$pi_item_sql="select a.ID, a.PI_NUMBER, a.item_category_id from com_pi_master_details a,tmp_poid tmp  where a.id=tmp.poid AND tmp.userid=99999";
	$piItemSqlResult=sql_select($pi_item_sql);
	foreach($piItemSqlResult as $pi_item_rows){
		$pi_number_arr[$pi_item_rows[ID]]=$pi_item_rows[PI_NUMBER];
		$item_cat_id_arr[$pi_item_rows[ID]]=$pi_item_rows[ITEM_CATEGORY_ID];
	}


	
//--------------------------
		
	
		if($db_type==2 && count($mst_id_arr)>990)
		{
			$where_cond=" and (";
			$IdsArr=array_chunk($mst_id_arr,990);
			foreach($IdsArr as $ids)
			{
				$where_cond.=" import_mst_id  in(".implode(',',$ids).") or ";
			}
			$where_cond=chop($where_cond,'or ');
			$where_cond.=")";
		}
		else
		{
			$mstIdString = implode(",",$mst_id_arr);
			$where_cond=" and  import_mst_id  in($mstIdString)";
		}	
	
	
	$sql="select import_mst_id,lc_sc_id, is_lc_sc, current_distribution, status_active from com_btb_export_lc_attachment where is_deleted=0 and status_active=1 $where_cond ";
	$lc_sc_sql=sql_select($sql);
		
		foreach($lc_sc_sql as $row_lc)
		{ 	
			if($row_lc[csf("is_lc_sc")]==0) 
			{	
				$sql_sc_lc="select c.buyer_name,c.team_leader,a.import_btb  from com_export_lc_order_info a, wo_po_break_down b,wo_po_details_master c where b.id=a.wo_po_break_down_id and b.job_no_mst=c.job_no and a.com_export_lc_id =".$row_lc[csf("lc_sc_id")]."";
			}
			else
			{
				$sql_sc_lc="select c.buyer_name,c.team_leader,0 as import_btb  from com_sales_contract_order_info a, wo_po_break_down b,wo_po_details_master c where b.id=a.wo_po_break_down_id and b.job_no_mst=c.job_no and a.com_sales_contract_id ='".$row_lc[csf("lc_sc_id")]."'";				
			}
			
			$query_res=sql_select($sql_sc_lc);

			if($query_res[0][csf("import_btb")] == 1)
			{
				$comp_buyer[$row_lc[csf("import_mst_id")]][$query_res[0][csf("buyer_name")]] =  $company_library[$query_res[0][csf("buyer_name")]];
				
			}
			else
			{
				$comp_buyer[$row_lc[csf("import_mst_id")]][$query_res[0][csf("buyer_name")]] = $buyer_library[$query_res[0][csf("buyer_name")]];
				
				$team_leader_arr[$row_lc[csf("import_mst_id")]][$query_res[0][csf("team_leader")]] = $mkt_team_lib[$query_res[0][csf("team_leader")]];
				
				
			}
		}
	
	
	ob_start();
	$flag=0; 
	

?>
<table width="1500">
    <tr>
    	<td valign="top" align="left">
    		<table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tr>
                    <td colspan="13"><span><? echo $company_name; ?></span><br /><strong>Import Consignment Pending List (Bill of Entry Overdue List)</strong></td>
                </tr>
				
                <tr bgcolor="#DDDDDD">
                    <td width="30">SL</td>
                    <td width="120">Item Category</td>
                    <td width="150">Importer</td>
                    <td width="150">Supplier Name</td>
                    <td width="200">PI Numner</td>
                    <td width="130">BTB LC No</td>
                    <td width="100">BTB LC value</td>
                    <td width="100">BTB LC Date</td>
                    <td width="100">Last Ship Date</td>
                    <td width="100">LC Expiry Date</td>
                    <td width="100">Buyer Name</td>
                    <td width="150">Mkt. Team Leader</td>
                    <td >Days Left</td>
                </tr>
				<?
                	$i=0;
					
					foreach($dataArrayResult as $rows)
					{	
						$piNumberArr=array();$itemCatArr=array();
						foreach(explode(',',$rows[csf('pi_id')]) as $pi_id){
							$piNumberArr[$pi_id]=$pi_number_arr[$pi_id];
							$itemCatArr[$item_cat_id_arr[$pi_id]]=$item_category[$item_cat_id_arr[$pi_id]];
						}
						
					$i++;
                ?>
                <tr>
                    <td><? echo $i; ?></td>
                    <td><p style="width:120; word-wrap:break-word;"><? 
					echo implode(',',$itemCatArr);
					// echo$item_category[$rows[csf('item_category_id')]]; ?></p></td>
                    <td><p style="width:150; word-wrap:break-word;"><? echo wordwrap($company_library[$rows[csf('importer_id')]],15,"<br>\n"); ?></p></td>
                    <td><p style="width:150; word-wrap:break-word;"><? echo wordwrap($supplier_library[$rows[csf('supplier_id')]],15,"<br>\n"); ?></p></td>
                    <td><p style="width:200; word-wrap:break-word;"><? //echo $rows[csf('pi_id')];
					
					
					echo wordwrap(implode(', ',$piNumberArr),2,"<br>\n"); ?></p></td>
                    <td align="center"><p style="width:130; word-wrap:break-word;"><? echo $rows[csf('lc_number')]; ?></p></td> 
                    <td align="right"><p><? echo wordwrap(number_format($rows[csf('lc_value')],2),15,"<br>\n");  ?></p></td> 
                    <td align="center"><p style="width:100; word-wrap:break-word;"><? echo change_date_format($rows[csf('lc_date')]); ?></p></td> 
                    <td align="center"><p style="width:100; word-wrap:break-word;"><? echo change_date_format($rows[csf('last_shipment_date')]); ?></p></td> 
                    <td align="center"><p style="width:100; word-wrap:break-word;"><? echo change_date_format($rows[csf('lc_expiry_date')]); ?></p></td> 
                    <td><p style="width:100; word-wrap:break-word;"><? echo wordwrap(implode(', ',$comp_buyer[$rows[csf('id')]]),3,"<br>\n"); ?></p></td> 
                    <td><p style="width:150; word-wrap:break-word;"><? echo wordwrap( implode(', ',$team_leader_arr[$rows[csf('id')]]),3,"<br>\n"); ?></p></td> 
                    <td><p><? echo $rows[csf('datedef')]; ?></p></td>
                </tr>
				<? 
					}
                ?>
			</table>
		</td>
	</tr>
</table>
<?
	
	$to="";
	$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=19 and b.mail_user_setup_id=c.id and a.company_id=$compid and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	$mail_sql2=sql_select($sql2);
	foreach($mail_sql2 as $row)
	{
		if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}

	
	$message="";
	$subject="Bill of Entry Overdue List";
	$message=ob_get_contents();
	ob_clean();
	$header=mailHeader();
	//if($to!="" ){echo send_mail_mailer( $to, $subject, $message, $from_mail );}
	//echo $message;
	if($_REQUEST['isview']==1){
		$mail_item=19;
		if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
		echo $message;
	}
	else{
		if($to!="" ){echo sendMailMailer( $to, $subject, $message, $from_mail );}
	}
	
		
} // End Company
	
execute_query("delete from tmp_poid where userid=99999");


?> 