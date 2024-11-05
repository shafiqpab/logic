<?php
date_default_timezone_set("Asia/Dhaka");
require_once('includes/common.php');
require_once('mailer/class.phpmailer.php');

$company_library=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name"  );
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name"  );
$supplier_library = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
$user_arr = return_library_array("select id,user_name from user_passwd where valid=1","id","user_name");

$next_date=add_date(date("Y-m-d"),1);
$prev_date=add_date(date("Y-m-d"),-1);

	if($db_type==0)
	{
		$current_date = date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0)));
		$previous_date = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))); 
	}
	else
	{
		$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
		$previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1); 
	}
	$a=mktime(0, 0, 0, date("m"), date("d")-1, date("Y"));

 //echo $previous_date="28-Nov-15";
ob_start();
foreach($company_library as $compid=>$compname) /// Daily Order Entry
{
	$flag=0;	
	//$b=$a+86399;
	
		$str_cond=" and insert_date between '".$previous_date."' and '".$current_date."'";
		$str_cond_a=" and a.insert_date between '".$previous_date."' and '".$current_date."'";
		$str_cond_b=" and b.insert_date between '".$previous_date."' and '".$current_date."'";
		$str_cond_c=" and c.insert_date between '".$previous_date."' and '".$current_date."'";
	
	//echo $str_cond_a;die;
	ob_start();
	?>
    
    <table width="1160"  cellspacing="0" border="0">
        <tr>
            <td colspan="12" align="center">
                <strong>Company Name:<?php  echo $company_library[$compid]; ?></strong>
            </td>
        </tr>
        
        <tr>
            <td colspan="14" align="center">
                <b style="font-size:14px;">Daily Order Entry ( Date :<?  echo date("d-m-Y", $a);  ?>)</b>
            </td>
        </tr>
        
        
   </table>
    <table width="1160" border="1" rules="all" class="rpt_table" id="table_body3">
        <thead>
            <tr align="center">
                <th width="35">Sl</th>
                <th width="80">Job No</th>
                <th width="100">Order No</th>
                <th width="100">Buyer</th>
                <th width="100">Style</th>
                <th width="100">Item</th>
                <th width="30">SMV</th>
                <th width="100">Order Qty.</th>
                <th width="100">Total SMV</th>
                <th width="50">Unit Price</th>
                <th width="100">Value</th>
                <th width="80">Ship Date</th>
                <th width="50">Lead Time</th>
                <th>Insert By</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $i=0;
        $total_po_qty=0;
        $total_value=0;
        
		if($db_type==0){$date_diff="DATEDIFF(b.pub_shipment_date,b.po_received_date) as  date_diff,";}
		else{$date_diff="(b.pub_shipment_date - b.po_received_date) as  date_diff,";}
		
		
		$sql_mst="select $date_diff a.job_no,a.set_smv,b.po_number,a.buyer_name,a.style_ref_no,a.gmts_item_id,b.po_quantity,b.unit_price,b.shipment_date,b.po_received_date,b.inserted_by from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name like '$compid' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_b";				
		$nameArray_mst=sql_select($sql_mst);
		$tot_rows=count($nameArray_mst);
		foreach($nameArray_mst as $row)
		{
            $i++;
            if ($i%2==0)  
                $bgcolor="#E9F3FF";
            else
                $bgcolor="#FFFFFF";
                
        ?>	
        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                <td align="center"><? echo $i;?></td>
                <td><? echo $row[csf('job_no')]; ?></td>
                <td><? echo $row[csf('po_number')]; ?></td>
                <td><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
                <td><? echo $row[csf('style_ref_no')]; ?></td>
                <td><? echo $garments_item[$row[csf('gmts_item_id')]]; ?></td>
                <td align="right"><? echo $row[csf('set_smv')]; ?></td>
                <td align="right">
					<? 
						echo number_format($row[csf('po_quantity')],2); 
						$total_po_qty+= $row[csf('po_quantity')]; 
                    ?>
                </td>
                <td align="right">
					<? 
						$tot_smv=($row[csf('set_smv')]*$row[csf('po_quantity')]); 
						echo number_format($tot_smv,2); 
						$grund_tot_smv+=$tot_smv; 
                    ?>
                </td>
                <td align="right"><?php echo number_format($row[csf('unit_price')],2); ?></td>
                <td align="right">
                    <?php 
						$value=$row[csf('po_quantity')]*$row[csf('unit_price')]; 
						echo number_format($value,2);
						$total_value+= $value;
                    ?>
                </td>
                <td align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                <td align="center"><? echo $row[csf('date_diff')]; ?></td>
                <td><? echo $user_arr[$row[csf('inserted_by')]]; ?></td>
            </tr>
        <?
        $flag=1;
		}
		if($tot_rows==0)
		{
		?>
        	<tr><td colspan="13" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>
        
		<?	
		}
        ?> 
     </tbody>         
       <tfoot>
            <th align="right" colspan="7"><b>Total :</b></th>
            <th align="right"><?  echo number_format($total_po_qty,2); ?></th>
            <th align="right"><? echo number_format($grund_tot_smv,2); ?></th>
            <th>&nbsp;</th>
            <th align="right"><?  echo number_format($total_value,2); ?></th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
      </tfoot>
  </table>
<?

		$to="";
		
		$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=1 and b.mail_user_setup_id=c.id and a.company_id=$compid";
		
		$mail_sql=sql_select($sql);
		foreach($mail_sql as $row)
		{
			if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
		}
		
 		$subject="Daily Order Entry";
    	$message="";
    	$message=ob_get_contents();
    	ob_clean();
		$header=mail_header();
		if($to!="" && $flag==1)echo send_mail_mailer( $to, $subject, $message, $from_mail );
		
		
		//echo $message;if($flag==1) echo $to;
		
		
	 
}

?> 