<?
	include('includes/common.php');
	$con = connect();
	
	$sql_booking_order=sql_select("select a.id as book_id, sum(c.amount) as book_amt 
	from wo_booking_mst a, wo_booking_dtls b, wo_trim_book_con_dtls c 
	where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id and a.item_category=4 and a.booking_type=2 and b.booking_type=2 and c.amount>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id");
	
	$booking_order_data=array();
	foreach($sql_booking_order as $row)
	{
		$booking_order_data[$row[csf("book_id")]] +=$row[csf("book_amt")];
	}
	
	$sql_booking_nonorder=sql_select("select a.id as book_id, sum(b.amount) as book_amt from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.item_category=4 and b.amount>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id");
	
	$booking_nonorder_data=array();
	foreach($sql_booking_nonorder as $row)
	{
		$booking_nonorder_data[$row[csf("book_id")]]=$row[csf("book_amt")];
	}
	
	
	$pi_sql=sql_select("select a.id as pi_id, a.pi_number, b.work_order_id, b.work_order_no, b.booking_without_order, b.amount as pi_amount, a.goods_rcv_status
	from com_pi_master_details a, com_pi_item_details b 
	where a.id=b.pi_id and a.entry_form=1 and a.item_category_id=4 and b.work_order_id>0 and a.pi_number is not null and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by  b.work_order_id");
	$pi_data=$pi_check=$good_rcv_check=$double_wo_id=array();
	foreach($pi_sql as $row)
	{
		$good_rcv_check[$row[csf("work_order_id")]][$row[csf("booking_without_order")]][$row[csf("goods_rcv_status")]]=$row[csf("goods_rcv_status")];
		if(count($good_rcv_check[$row[csf("work_order_id")]][$row[csf("booking_without_order")]])>1)
		{
			$double_wo_id[$row[csf("work_order_id")]]=$row[csf("work_order_id")];
		}
		
	}
	
	
	foreach($pi_sql as $row)
	{
		if($pi_check[$row[csf("work_order_id")]][$row[csf("booking_without_order")]][$row[csf("pi_id")]]=="")
		{
			$pi_check[$row[csf("work_order_id")]][$row[csf("booking_without_order")]][$row[csf("pi_id")]]=$row[csf("pi_id")];
			$pi_data[$row[csf("work_order_id")]][$row[csf("booking_without_order")]]["pi_id"].=$row[csf("pi_id")].",";
			$pi_data[$row[csf("work_order_id")]][$row[csf("booking_without_order")]]["pi_number"].=$row[csf("pi_number")].",";
		}
		$pi_data[$row[csf("work_order_id")]][$row[csf("booking_without_order")]]["work_order_no"]=$row[csf("work_order_no")];
		$pi_data[$row[csf("work_order_id")]][$row[csf("booking_without_order")]]["booking_without_order"]=$row[csf("booking_without_order")];
		$pi_data[$row[csf("work_order_id")]][$row[csf("booking_without_order")]]["amount"]+=$row[csf("pi_amount")];
	}
	//echo "<pre>";
	//print_r($pi_data[117]);die;
	//echo count($booking_order_data);
	//echo $booking_order_data[1004]."==".$booking_order_data[3545].jahid;die;
	?>
	
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table">
        <thead>
        	<tr>
                <th width="50">SL</th>
                <th width="150">PI Sys Id</th>
                <th width="250">PI Number</th>
                <th width="150">WO Number</th>
                <th width="150">PI Amount</th>
                <th width="150">WO Amount</th>
            </tr>
        </thead>
        <tbody>
        <? 
        $i=1;
        foreach ($pi_data as $wo_id=>$book_val)
        {
			foreach($book_val as $booking_without_order=>$val)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$wo_amt=$diff=0;
				if($val["booking_without_order"]==1)
				{
					if($val["amount"]>$booking_nonorder_data[$wo_id])
					{
						$wo_amt=$booking_nonorder_data[$wo_id];
					}
				}
				else
				{
					if($val["amount"]>$booking_order_data[$wo_id])
					{
						$wo_amt=$booking_order_data[$wo_id];
					}
				}
				$diff=$val["amount"]-$wo_amt;
				

				if($double_wo_id[$wo_id]!="" && $wo_amt>0)
				{
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>"> 				
						<td align="center"><? echo $i; ?></td>	
						<td style="word-break:break-all"><? echo chop($val["pi_id"],",");?></td>
						<td style="word-break:break-all"><? echo chop($val["pi_number"],",");?></td>
						<td><? echo $val["work_order_no"]; ?></td>
						<td align="right"><? echo number_format($val["amount"],2,".",""); ?></td> 
						<td align="right" title="<? echo $wo_id."==".$val["booking_without_order"]; ?>"><? echo number_format($wo_amt,2,".",""); ?></td>
					</tr>
					<?
					$i++;
				}
			}
			
        }
        ?>
        </tbody>
    </table>
	