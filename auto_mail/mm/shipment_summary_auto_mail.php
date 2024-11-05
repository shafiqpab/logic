<?
		require_once('../../includes/common.php');
		require_once('../../mailer/class.phpmailer.php');
		require_once('../setting/mail_setting.php');
		
		
 	$buyer_lib_arr=return_library_array( "select id, short_name from lib_buyer where status_active=1",'id','short_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	
	$previous_date= date('d-M-Y', strtotime("first day of -0 month"));
	$current_date = date('d-M-Y', strtotime("last day of -0 month"));
	
 	
	//Exfactory ..................................................
	$sqlExf="select a.DELIVERY_COMPANY_ID,a.BUYER_ID,b.ACTUAL_PO,b.EX_FACTORY_QNTY,b.PO_BREAK_DOWN_ID from PRO_EX_FACTORY_DELIVERY_MST a,PRO_EX_FACTORY_MST b where a.id=b.DELIVERY_MST_ID  and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 AND B.EX_FACTORY_DATE between '$previous_date' and '$current_date'";
	   //echo $sqlExf; 
	$sqlExfRes=sql_select($sqlExf);
	$exfactory_data_arr=array();
	foreach($sqlExfRes as $row)
	{
		$exfactory_data_arr[PO_QTY][$row[BUYER_ID]][$row[PO_BREAK_DOWN_ID]]+=$row[EX_FACTORY_QNTY];
		$exfactory_data_arr[PO_ID][$row[PO_BREAK_DOWN_ID]]=$row[PO_BREAK_DOWN_ID];
		
		if($row[ACTUAL_PO]){
			foreach(explode(',',$row[ACTUAL_PO]) as $acc_po_id){
				$exfactory_data_arr[ACTUAL_PO_ID][$row[BUYER_ID]][$row[PO_BREAK_DOWN_ID]][$acc_po_id]=$acc_po_id;
			}
		}
	}

	//var_dump($exfactory_data_arr[PO_ID]);die;
	
	//ORDER ..................................................
	$sqlPo = "select a.CLIENT_ID,a.BUYER_NAME,a.COMPANY_NAME,B.ID as PO_ID,b.PO_NUMBER,(a.TOTAL_SET_QNTY*b.PO_QUANTITY) as PO_QTY_PCS from WO_PO_DETAILS_MASTER a,WO_PO_BREAK_DOWN b where a.id=b.job_id and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 ".where_con_using_array($exfactory_data_arr[PO_ID],0,'b.ID')."";
	  //echo $sqlPo;
	$sqlPoRes=sql_select($sqlPo);
	$po_data_arr=array();
	foreach($sqlPoRes as $row)
	{
		$po_data_arr[PO_QTY][$row[BUYER_NAME]][$row[PO_ID]]=$row[PO_QTY_PCS];
		$po_data_arr[PO_NO][$row[BUYER_NAME]][$row[PO_ID]]=$row[PO_NUMBER];
		$buyer_by_po[$row[PO_ID]]=$row[BUYER_NAME];
		
		if($row[CLIENT_ID]>0){
			$po_data_arr[CLIENT_ID][$row[BUYER_NAME]][$row[CLIENT_ID]]=$buyer_lib_arr[$row[CLIENT_ID]];
		}
		
		
		
		if($row[PO_QTY_PCS]>$exfactory_data_arr[PO_QTY][$row[BUYER_NAME]][$row[PO_ID]]){
			//$po_data_arr[SHORT_SHIP][$row[BUYER_NAME]][$row[PO_ID]]=$row[PO_QTY_PCS];
			$po_data_arr[SHORT_SHIP_QTY][$row[BUYER_NAME]][$row[PO_ID]]=$row[PO_QTY_PCS]-$exfactory_data_arr[PO_QTY][$row[BUYER_NAME]][$row[PO_ID]];
		}
		if($row[PO_QTY_PCS]<$exfactory_data_arr[PO_QTY][$row[BUYER_NAME]][$row[PO_ID]]){
			//$po_data_arr[EXCESS_SHIP][$row[BUYER_NAME]][$row[PO_ID]]=$row[PO_QTY_PCS];
			$po_data_arr[EXCESS_SHIP_QTY][$row[BUYER_NAME]][$row[PO_ID]]=$exfactory_data_arr[PO_QTY][$row[BUYER_NAME]][$row[PO_ID]]-$row[PO_QTY_PCS];
		}
		
		
	}
	
	 //var_dump($po_data_arr[PO_QTY]);
	
	
	
	$ACC_PO_SQL = "SELECT c.PO_BREAK_DOWN_ID,c.id as ACC_PO_ID,c.ACC_PO_NO,c.ACC_PO_QTY FROM wo_po_acc_po_info c WHERE c.STATUS_ACTIVE=1 and c.IS_DELETED=0 ".where_con_using_array($exfactory_data_arr[PO_ID],0,'C.PO_BREAK_DOWN_ID')."";
	//echo $ACC_PO_SQL; 
	$ACC_PO_SQL_RES=sql_select($ACC_PO_SQL);
	$acc_data_arr=array();
	foreach($ACC_PO_SQL_RES as $row)
	{
		$acc_data_arr[$row[PO_BREAK_DOWN_ID]][$row[ACC_PO_ID]]=$row[ACC_PO_QTY];
	}
	
	
	foreach($acc_data_arr as $po_id=>$poValArr){
		$po_data_arr[PO_QTY][$buyer_by_po[$po_id]][$po_id]=0;
		$po_data_arr[SHORT_SHIP_QTY][$buyer_by_po[$po_id]][$po_id]=0;
		$po_data_arr[EXCESS_SHIP_QTY][$buyer_by_po[$po_id]][$po_id]=0;
		foreach($poValArr as $acc_po_id=>$ACC_PO_QTY){
			
			//if($ACC_PO_QTY>0){
				$po_data_arr[PO_QTY][$buyer_by_po[$po_id]][$po_id] += $ACC_PO_QTY;
				
				$ACC_EX_QTY = ($exfactory_data_arr[ACTUAL_PO_ID][$buyer_by_po[$po_id]][$po_id][$acc_po_id]!='')?$ACC_PO_QTY:0;
				
				//if($po_data_arr[SHORT_SHIP_QTY][$buyer_by_po[$po_id]][$po_id]){
					$po_data_arr[SHORT_SHIP_QTY][$buyer_by_po[$po_id]][$po_id] += $ACC_PO_QTY-$ACC_EX_QTY;
					
				//}if($po_data_arr[EXCESS_SHIP_QTY][$buyer_by_po[$po_id]][$po_id]){
					 $po_data_arr[EXCESS_SHIP_QTY][$buyer_by_po[$po_id]][$po_id] += $ACC_EX_QTY - $ACC_PO_QTY;
				//}
			//}
			
			
		}
	}
	
	
	//var_dump($exfactory_data_arr[ACTUAL_PO_ID]); 
	
	
	
	
?>   
    <div class="main_table">
        <table cellpadding="0" cellspacing="0" align="center">
            <tr>
                <td align="center" width="100%" style="font-size:20px"><strong>Shipment Summary</strong></td>
            </tr>
            <tr>
                <td align="center" width="100%" style="font-size:16px"><strong>Month of <?=date('M Y',strtotime($previous_date));?> </strong></td>
            </tr>
        </table>
        <table cellspacing="0" cellpadding="3" border="1" rules="all">
            <thead bgcolor="#CCCCCC">
                <th width="120">Buyer Name</th>
                <th width="80">Client</th>
                <th width="100">Order Qty</th>
                <th width="80">Number of PO</th>
                <th width="100">Shipment Qty</th>
                <th width="100">Excess Ship. Qty</th>
                <th width="120">Number of Excess Ship. PO</th>
                <th width="80">Short Ship. Qty</th>
                <th width="120">Number of Shorts Ship. PO</th>
                <th width="80">Average (+-)</th>
            </thead>
            <tbody>
            	<? $i=0;
				foreach($exfactory_data_arr[PO_QTY] as $buyer_id=>$row){
					$i++;
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
					
					
					
					
					$ShortShipArr[$buyer_id] = array_sum($po_data_arr[SHORT_SHIP_QTY][$buyer_id]);
					$ExcessShipArr[$buyer_id] = array_sum($po_data_arr[EXCESS_SHIP_QTY][$buyer_id]);
					
					//$ExcessShip=$po_wise_exf_qty-$po_wise_po_qty;
					//$ExcessShip=($ExcessShip>0)?$ExcessShip:0;
					
					//$ShortShip=$po_wise_exf_qty-$po_wise_po_qty;
					//$ShortShip=($ShortShip>0)?$ShortShip:0;
					
					$po_qty_arr[$buyer_id] = array_sum($po_data_arr[PO_QTY][$buyer_id]);
					$exf_qty_arr[$buyer_id] = array_sum($exfactory_data_arr[PO_QTY][$buyer_id]);
					$average_arr[$buyer_id]=$exf_qty_arr[$buyer_id]-$po_qty_arr[$buyer_id];
					
					
					
				 
					$tmp_po_arr=array();
					foreach($po_data_arr[PO_QTY][$buyer_id] as $po_id=>$po_val){
						$tmp_po_arr[$po_id]=count($acc_data_arr[$po_id])?count($acc_data_arr[$po_id]):1;
					}
					$number_of_po_arr[$buyer_id]=array_sum($tmp_po_arr);
					//$number_of_po_arr[$buyer_id]=count($po_data_arr[PO_QTY][$buyer_id]);
					
					
					$tmp_po_arr=array();
					foreach($po_data_arr[EXCESS_SHIP_QTY][$buyer_id] as $po_id=>$po_val){
						$tmp_po_arr[$po_id]=count($acc_data_arr[$po_id])?count($acc_data_arr[$po_id]):1;
					}
					$number_of_excess_po_arr[$buyer_id]=array_sum($tmp_po_arr);
					//$number_of_excess_po_arr[$buyer_id]=count($po_data_arr[EXCESS_SHIP_QTY][$buyer_id]);

					$tmp_po_arr=array();
					foreach($po_data_arr[SHORT_SHIP_QTY][$buyer_id] as $po_id=>$po_val){
						$tmp_po_arr[$po_id]=count($acc_data_arr[$po_id])?count($acc_data_arr[$po_id]):1;
					}
					$number_of_short_po_arr[$buyer_id]=array_sum($tmp_po_arr);
					//$number_of_short_po_arr[$buyer_id]=count($po_data_arr[SHORT_SHIP_QTY][$buyer_id]);
					

					//var_dump($acc_data_arr);
					
				?>
                <tr bgcolor="<?=$bgcolor;?>">
                    <td><?=$buyer_lib_arr[$buyer_id];?></td>
                    <td><p><?=implode(', ',$po_data_arr[CLIENT_ID][$buyer_id]);?></p></td>
                    <td align="right"><?=$po_qty_arr[$buyer_id];?></td>
                    <td align="right"><?=$number_of_po_arr[$buyer_id];?></td>
                    <td align="right"><?=$exf_qty_arr[$buyer_id];?></td>
                    <td align="right"><?=$ExcessShipArr[$buyer_id];?></td>
                    <td align="right"><?=$number_of_excess_po_arr[$buyer_id];?></td>
                    <td align="right"><?=$ShortShipArr[$buyer_id];?></td>
                    <td align="right"><?=$number_of_short_po_arr[$buyer_id];?></td>
                    <td align="right"><?=$average_arr[$buyer_id];?></td>
                </tr>
                <?
				}
				?>
            </tbody>
            
            <tfoot bgcolor="#CCCCCC">
                <th colspan="2">Total</th>
                <th align="right"><?=array_sum($po_qty_arr);?></th>
                <th align="right"><?=array_sum($number_of_po_arr); ?></th>
                <th align="right"><?=array_sum($exf_qty_arr);?></th>
                <th align="right"><?=array_sum($ExcessShipArr); ?></th>
                <th align="right"><?=array_sum($number_of_excess_po_arr); ?></th>
                <th align="right"><?=array_sum($ShortShipArr); ?></td>
                <th align="right"><?=array_sum($number_of_short_po_arr); ?></th>
                <th align="right"><?=array_sum($average_arr);?></th>
            </tfoot>
            
            
        </table>
    </div>



    
