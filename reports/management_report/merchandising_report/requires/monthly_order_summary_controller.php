<?
include('../../../../includes/common.php');

require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.fabrics.php');
//require_once('../../../includes/class4/class.yarns.php');
//require_once('../../../includes/class4/class.washes.php');
//require_once('../../../includes/class4/class.emblishments.php');
//require_once('../../../includes/class4/class.trims.php');



session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "");
	exit();
}

/*
$buyer_short_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
$team_leader_arr=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');
*/

if($action=="report_generate")
{
	extract($_REQUEST);
	//data variable.............................................
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_order_status=str_replace("'","",$cbo_order_status);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_product_category=str_replace("'","",$cbo_product_category);
	$cbo_date_type=str_replace("'","",$cbo_date_type);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$location_arr=return_library_array( "select id, LOCATION_NAME from LIB_LOCATION",'id','LOCATION_NAME');

	if($type==1)
	{
		//where con.............................................
		if($cbo_company_name!=0){$whereCon=" and a.company_name =$cbo_company_name";}
		if($cbo_order_status!=0){$whereCon.=" and b.is_confirmed=$cbo_order_status";}
		if($cbo_buyer_name!=""){$whereCon.=" and a.buyer_name in($cbo_buyer_name)";}
		if($cbo_product_category >0){$whereCon.=" and a.PRODUCT_CATEGORY in($cbo_product_category)";}
	
		if($txt_date_from!="" && $txt_date_to!=""){
			$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd','-',1);
			$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd','-',1);
			if($cbo_date_type==1){$whereConDate=" AND c.country_ship_date between '$txt_date_from' and '$txt_date_to'";}
			if($cbo_date_type==2){$whereConDate=" AND b.shipment_date between '$txt_date_from' and '$txt_date_to'";}
			
		}
	 
	 
		//Query.....................................................
		
		if($cbo_date_type==1) // Country ship date
		{
			 $orderSql="SELECT  a.BUYER_NAME,a.PRODUCT_CATEGORY, c.country_ship_date as SHIPMENT_DATE, c.order_quantity as PO_QUANTITY, c.order_rate as UNIT_PRICE, c.order_total as PO_TOTAL_PRICE,b.id as PO_ID,b.IS_CONFIRMED from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c
			where a.id=b.job_id and a.job_no=c.job_no_mst and b.id=c.PO_BREAK_DOWN_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $whereCon $whereConDate
			order by c.country_ship_date";
		}
		else if($cbo_date_type==2)
		{
		}
		// echo $orderSql; die;
	
		$orderSqlRes=sql_select($orderSql);
		 
		$monthArr=array(); 
		$orderDataArr=array();
		$allPoArr=array();
		foreach( $orderSqlRes as $row)
		{
			$dateKey=date("Y-m",strtotime($row["SHIPMENT_DATE"]));
			$monthArr[$dateKey]=date("M,Y",strtotime($row["SHIPMENT_DATE"]));
			
			$prevMonthKeyArr[$dateKey]=date("Y-m", strtotime("-1 year", strtotime($row["SHIPMENT_DATE"])));		
			// $orderDataArr[$row["PRODUCT_CATEGORY"]][$row["BUYER_NAME"]][$dateKey]+=$row["PO_QUANTITY"];
			if($row["PRODUCT_CATEGORY"]==2)
			{
				$orderDataArr["Lingerie"][$row["BUYER_NAME"]][$dateKey]+=$row["PO_QUANTITY"];
			}
			else
			{
				$orderDataArr["Outwears"][$row["BUYER_NAME"]][$dateKey]+=$row["PO_QUANTITY"];
			}

			if($row["IS_CONFIRMED"]==1){
				// $orderDataArrByCat[$row["PRODUCT_CATEGORY"]][$dateKey]+=$row["PO_QUANTITY"];
				if($row["PRODUCT_CATEGORY"]==2)
				{
					$orderDataArrByCat["Lingerie"][$dateKey]+=$row["PO_QUANTITY"];
				}
				else
				{
					$orderDataArrByCat["Outwears"][$dateKey]+=$row["PO_QUANTITY"];
				}
			}
			$orderDataArrByDate[$dateKey]+=$row["PO_QUANTITY"];
			
			$allPoArr[$row["PO_ID"]]=$row["PO_ID"];
			$productionCatIdBypo[$row["PO_ID"]]=$row["PRODUCT_CATEGORY"];
			$shipDateKeyBypo[$row["PO_ID"]]=$dateKey;
		}
		


 		//Last year data.........................................................................
		$pre_date_from=date("Y-m-d", strtotime("-1 year", strtotime($txt_date_from)));
 		$pre_date_to=date("Y-m-d", strtotime("-1 year", strtotime($txt_date_to)));
		
		if($txt_date_from!="" && $txt_date_to!=""){
			$pre_date_from=change_date_format($pre_date_from,'yyyy-mm-dd','-',1);
			$pre_date_to=change_date_format($pre_date_to,'yyyy-mm-dd','-',1);
			if($cbo_date_type==1){$whereConDate2=" AND c.country_ship_date between '$pre_date_from' and '$pre_date_to'";}
			if($cbo_date_type==2){$whereConDate2=" AND b.shipment_date between '$pre_date_from' and '$pre_date_to'";}
			
		}
		
		if($cbo_date_type==1) // Country ship date
		{
			 $preOrderSql="SELECT  a.BUYER_NAME,a.PRODUCT_CATEGORY, c.country_ship_date as SHIPMENT_DATE, c.order_quantity as PO_QUANTITY, c.order_rate as UNIT_PRICE, c.order_total as PO_TOTAL_PRICE,b.id as PO_ID from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c
			where a.id=b.job_id and a.job_no=c.job_no_mst and b.id=c.PO_BREAK_DOWN_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $whereCon $whereConDate2
			order by c.country_ship_date";
		}
		else if($cbo_date_type==2)
		{
		}
		$preOrderSqlRes=sql_select($preOrderSql);
		$preOrderDataArrByCat=array();
		foreach( $preOrderSqlRes as $row)
		{
			$dateKey=date("Y-m",strtotime($row["SHIPMENT_DATE"]));
			// $preOrderDataArrByCat[$row["PRODUCT_CATEGORY"]][$dateKey]+=$row["PO_QUANTITY"];
			if($row["PRODUCT_CATEGORY"]==2)
			{
				$preOrderDataArrByCat["Lingerie"][$dateKey]+=$row["PO_QUANTITY"];
			}
			else
			{
				$preOrderDataArrByCat["Outwears"][$dateKey]+=$row["PO_QUANTITY"];
			}
		}
		
 		
		//sewing qty---------------------------
		 $proSql="select A.PO_BREAK_DOWN_ID,A.PRODUCTION_QUANTITY,A.PRODUCTION_DATE from PRO_GARMENTS_PRODUCTION_MST a, wo_po_break_down b where A.PO_BREAK_DOWN_ID=b.id and A.PRODUCTION_TYPE=5 and A.STATUS_ACTIVE=1 and A.IS_DELETED=0 and B.IS_DELETED=0 ".where_con_using_array($allPoArr,0,'A.PO_BREAK_DOWN_ID')."";
		$proSqlRes=sql_select($proSql);
		$sewingDataArrByCat=array();
		foreach( $proSqlRes as $row)
		{
			$dateKey=date("Y-m",strtotime($row["PRODUCTION_DATE"]));
			$prod_cat=$productionCatIdBypo[$row["PO_BREAK_DOWN_ID"]];
			//echo $prod_cat.'<br>';
			
			if($prod_cat==2)
			{
				$sewingDataArrByCat["Lingerie"][$dateKey]+=$row["PRODUCTION_QUANTITY"];	
			}
			else 
			{
				$sewingDataArrByCat["Outwears"][$dateKey]+=$row["PRODUCTION_QUANTITY"];
			}
			//$sewingDataArrByCat[$productionCatIdBypo[$row["PO_BREAK_DOWN_ID"]]][$dateKey]+=$row["PRODUCTION_QUANTITY"];
		}
		//print_r($sewingDataArrByCat);
		
		//Production Capacity Min---------------------------
		if($txt_date_from!="" && $txt_date_to!=""){
			$whereConDate3=" AND b.DATE_CALC between '$txt_date_from' and '$txt_date_to'";
		}
		
		 $proCapSql="SELECT (A.LOCATION_ID) as LOCATION_ID,B.CAPACITY_MIN,B.DATE_CALC,B.CAPACITY_PCS,b.DAY_STATUS from LIB_CAPACITY_CALC_MST a,LIB_CAPACITY_CALC_DTLS b where a.id=b.mst_id and A.STATUS_ACTIVE=1 and B.STATUS_ACTIVE=1  and a.COMAPNY_ID=$cbo_company_name  and A.LOCATION_ID in(1,4) $whereConDate3 ";
		// echo $proCapSql;
		$proCapSqlRes=sql_select($proCapSql);
		$proCapDataArrByCat=array();
		foreach( $proCapSqlRes as $row)
		{
			$dateKey=date("Y-m",strtotime($row["DATE_CALC"]));
			$proCapDataArrByLocation[$row["LOCATION_ID"]][$dateKey]+=$row["CAPACITY_MIN"];
			$proCapDataArrByDate[$dateKey]+=$row["CAPACITY_MIN"];
			$proCapQtyDataArrByLocation[$row["LOCATION_ID"]][$dateKey]+=$row["CAPACITY_PCS"];
			$proCapQtyDataArrByDate[$dateKey]+=$row["CAPACITY_PCS"];
			if($row["DAY_STATUS"]==1 && $row["LOCATION_ID"]==1)
			{
				$workingDay[$dateKey]+=1;
			}

		}
		
		
		
		//Gray and finish fab---------------------------
		
		 $finishGraySql="select a.PO_BREAKDOWN_ID,B.TRANSACTION_DATE,		
		sum((case when a.TRANS_TYPE in (1,4,5) and A.ENTRY_FORM in(22,2,58,80,81,82,83,110,180,183,84,51) then a.QUANTITY else 0 end)-
		(case when a.TRANS_TYPE in (2,3,6) and A.ENTRY_FORM in(80,81,82,83,110,180,183,16,61) then a.QUANTITY else 0 end)) GRAY_STOCK_QTY,		
		(sum(case when a.TRANS_TYPE in (1,4,5) and A.ENTRY_FORM in(7,37,68, 14,15,134,219,216,214,52,126) then a.QUANTITY else 0 end)-
		sum(case when a.TRANS_TYPE in (2,3,6) and A.ENTRY_FORM in(46,14,15,134,219,216,214,18,71) then a.QUANTITY else 0 end)) FINISH_STOCK_QTY
		
		from ORDER_WISE_PRO_DETAILS a,INV_TRANSACTION b where A.TRANS_ID=b.id and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and B.IS_DELETED=0 and B.STATUS_ACTIVE=1 ".where_con_using_array($allPoArr,0,'A.PO_BREAKDOWN_ID')."
		group by a.PO_BREAKDOWN_ID,B.TRANSACTION_DATE";
		//echo $finishGraySql;die;
		$finishGraySqlRes=sql_select($finishGraySql);
		$proCapDataArrByCat=array();
		foreach( $finishGraySqlRes as $row)
		{
			$dateKey=date("Y-m",strtotime($row["TRANSACTION_DATE"]));
			$grayDataArrByDate[$dateKey]+=$row["GRAY_STOCK_QTY"];
			$finishDataArrByDate[$dateKey]+=$row["FINISH_STOCK_QTY"];
		}
	//	print_r($grayDataArrByDate);
		  
		$condition= new condition();
		if($cbo_company_name>0){
			$condition->company_name("=$cbo_company_name");
		}
		/*		if($cbo_buyer>0){
					$condition->buyer_name("=$cbo_buyer");
				}
				if($tna_process_start_date !=''){
					$condition->pub_shipment_date(" > '".$tna_process_start_date."'");
				}
				
				if(str_replace("'","",$txt_job_no) !=''){
					$condition->job_no(" = '".str_replace("'","",$txt_job_no)."'");
				}
		*/	
							
		$piid_arr_cond=array_chunk($allPoArr,1000, true);
		$pi_arr="";

		// echo "<pre>";
		// print_r($piid_arr_cond);
		$k=0;
		foreach($piid_arr_cond as $key=>$value)
		{
			if($k==0)
			{
				$pi_arr=" in(".implode(",",$value).")";
				
			}
			else
			{
				$pi_arr.=" or b.id  in(".implode(",",$value).")";
			
				
			}
			$k++;
		}

		$pi_arr.=$whereConDate;



		// if(count($allPoArr)>0){
		// 	$condition->po_id(" in(".implode(',',$allPoArr).") ");
		// }

		if(count($allPoArr)>0){
		$condition->po_id_in(implode(',',$allPoArr));
		}


		$condition->init();
		$fabric= new fabric($condition);
		  // echo $fabric->getQuery();
		$fabricdata=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish_purchase();

		$fabricdata_prod=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();
		
		
		$monthWiseGrayQtyArr=array();
		$monthWiseFinishQtyArr=array();
		foreach($fabricdata['knit']['grey'] as $po_id=>$rows){
			$monthKey=$shipDateKeyBypo[$po_id];	
			$monthWiseGrayQtyArr[$monthKey]+=array_sum($rows);
		}
		
		foreach($fabricdata['knit']['finish'] as $po_id=>$rows){
			$monthKey=$shipDateKeyBypo[$po_id];	
			$monthWiseFinishQtyArr[$monthKey]+=array_sum($rows);
		}

		foreach($fabricdata_prod['knit']['grey'] as $po_id=>$rows){
			$monthKey=$shipDateKeyBypo[$po_id];	
			$monthWiseGrayQtyArr[$monthKey]+=array_sum($rows);
		}
		
		foreach($fabricdata_prod['knit']['finish'] as $po_id=>$rows){
			$monthKey=$shipDateKeyBypo[$po_id];	
			$monthWiseFinishQtyArr[$monthKey]+=array_sum($rows);
		}
					
 		
		
		//Sales Forecast Entry---------------------------
		$whereConDate4=" AND a.COMPANY_ID=$cbo_company_name";
		if($txt_date_from!="" && $txt_date_to!=""){
			$whereConDate4 .=" AND b.SALES_TARGET_DATE between '$txt_date_from' and '$txt_date_to'";
		}
 		$salesForSql="select B.SALES_TARGET_QTY,B.SALES_TARGET_DATE from WO_SALES_TARGET_MST a,WO_SALES_TARGET_DTLS b where a.id=b.SALES_TARGET_MST_ID and A.STATUS_ACTIVE=1 and A.IS_DELETED=0 $whereConDate4";
		//echo $salesForSql;die;
		$salesForSqlRes=sql_select($salesForSql);
		$sales_forecast_qty=array();
		foreach( $salesForSqlRes as $row)
		{
			$dateKey=date("Y-m",strtotime($row["SALES_TARGET_DATE"]));
			$sales_forecast_qty[$dateKey]+=$row["SALES_TARGET_QTY"];
		}
		
	 
		
		
		$width=(count($monthArr)*70)+300;
		ob_start();
		?>
		
        <div style="width:<?=$width+20;?>px; margin:0 0 0 5px;">
				<div>
			        <table width="100%" cellspacing="0"  align="center" >
			            <tr>
			                <td colspan="<? echo $colspan;?>" align="center" class="form_caption">
			                    <strong style="font-size:16px;"><? echo  $company_library[$cbo_company_name] ;?></strong>
			                </td>
			            </tr>
			            <tr class="form_caption">
			                <td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Gorai, Mirzapur, Tangail</strong></td>
			            </tr>
			            <tr align="center">
			                <td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Monthly Delivery Confirm Quantity From : <? echo $txt_date_from." To ".$txt_date_to;?></strong></td>
			            </tr>
			            <tr align="center">
			                <td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Date : <? echo date("j F, Y ");?></strong></td>
			            </tr>
			       	</table>			      
			    </div>
        <table id="table_header_1" class="rpt_table" width="<?=$width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
        	<thead>
            	<th width="30">SL</th>
                <th>Buyer</th>
                <th>Product Category</th>
                <? foreach($monthArr as $monthName){echo "<th width='70'>$monthName</th>";}?>
                <th width="80">Total</th>
             </thead>
             <tbody>
             	
					<? 
					
					foreach($orderDataArr as $product_cat=>$proCatRows){
						$i=0;
						foreach($proCatRows as $buyer_id=>$buyerRows){
							$i++;
							 $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                                <td align="center"><?=$i;?></td>
                                <td><?=$buyer_arr[$buyer_id];?></td>
                                <td><?=$product_cat;?></td>
                                <?
								foreach($monthArr as $monthKey=>$monthName){
									echo "<td width='70' align='right' title='$monthKey'>$buyerRows[$monthKey]</td>";
								}
								?>
								<td align="right"><?=array_sum($buyerRows);?></td>
                            </tr>
                            <?
						}
						?>
                        	<tr bgcolor="#CCCCCC">
                            	<td colspan="3" align="center"><?=$product_cat;?> Total:</td>
                                <?
								foreach($monthArr as $monthKey=>$monthName){
									echo "<td align='right'>".$orderDataArrByCat[$product_cat][$monthKey]."</td>";
								}
								?>
								<td align="right"><?=array_sum($orderDataArrByCat[$product_cat]);?></td>
                            </tr>
                        <?
					}
					?>
                    <tfoot>
                        <th colspan="3" align="center">Grand Total:</th>
                        <?
                        foreach($monthArr as $monthKey=>$monthName){
                            echo "<th align='right'>".$orderDataArrByDate[$monthKey]."</th>";
                        }
                        ?>
                        <th align="right"><?=array_sum($orderDataArrByDate);?></th>
                    </tfoot>
                
             </tbody>
       </table>
       </div>
                
        
        
      <div style="width:<?=$width+20;?>px; margin:5px 0 0 5px;">
        <table id="table_header_1" class="rpt_table" width="<?=$width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
        	<thead>
                <th colspan="3"></th>
                <? foreach($monthArr as $monthName){echo "<th width='70'>$monthName</th>";}?>
                <th width="80">Total</th>
             </thead>
             <tbody>
             	<?
				$i=0;
				foreach($orderDataArrByCat as $product_cat=>$rows){
				$i++;
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trr_<? echo $i; ?>">
					<td colspan="3">Last Year Confirm Qty in Pcs [<?=$product_cat;?>]</td>
					<?
                    foreach($monthArr as $monthKey=>$monthName){
                        echo "<td align='right'>".number_format($preOrderDataArrByCat[$product_cat][$prevMonthKeyArr[$monthKey]])."</td>";
                    }
                    ?>
                    <td align="right"><?=number_format(array_sum($preOrderDataArrByCat[$product_cat]),0);?></td>
                </tr>
                
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trrr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trrr_<? echo $i; ?>">
					<td colspan="3">Current Confirm Qty in Pcs [<?=$product_cat;?>]</td>
					<?
                    foreach($monthArr as $monthKey=>$monthName){
                        echo "<td align='right'>".number_format($rows[$monthKey])."</td>";
                    }
                    ?>
                    <td align="right"><?=number_format(array_sum($rows),0);?></td>
                </tr>
                <?
				}
				?>
			</tbody>

		</table>
	</div>       
    
    <div style="width:<?=$width+20;?>px; margin:5px 0 0 5px;">
        <table id="table_header_1" class="rpt_table" width="<?=$width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
        	<thead>
                <th colspan="3"></th>
                <? foreach($monthArr as $monthName){echo "<th width='70'>$monthName</th>";}?>
                <th width="80">Total</th>
             </thead>
             <tbody>
                <tr>
					<td colspan="2" valign="middle">Order Information [Pcs]</td>
					<td>Order Forecasting Qty</td>
					<?
                    foreach($monthArr as $monthKey=>$monthName){
                        echo "<td align='right'>".$sales_forecast_qty[$monthKey]."</td>";
                    }
                    ?>
                    <td align="right"><?=array_sum($sales_forecast_qty);?></td>
                </tr>
                
                
                <tr bgcolor="#E9F3FF">
					<td rowspan="4" colspan="2" valign="middle">Grey & Finish Fabrics Information  [Kg]</td>
					<td>Total Grey Fabric Required</td>
					<?
                    $grandTotalGrayFabReq=0;
					foreach($monthArr as $monthKey=>$monthName){
                        $grandTotalGrayFabReq+=$monthWiseGrayQtyArr[$monthKey];
						echo "<td align='right'>".number_format($monthWiseGrayQtyArr[$monthKey],2,'.','')."</td>";
                    } 
						
                    ?>
                    <td align="right"><?=number_format($grandTotalGrayFabReq,2,'.','');?></td>
                </tr>
                <tr bgcolor="#E9F3FF">
					<td>Total Grey Fabric Balance</td>
					<?
                    $grandTotalGrayFabBal=0;
					foreach($monthArr as $monthKey=>$monthName){
                        $grandTotalGrayFabBal+=($monthWiseGrayQtyArr[$monthKey]-$grayDataArrByDate[$monthKey]);
						echo "<td align='right'>".number_format(($monthWiseGrayQtyArr[$monthKey]-$grayDataArrByDate[$monthKey]),2,'.','')."</td>";
                    }
                    ?>
                    <td align="right"><?=number_format($grandTotalGrayFabBal,2,'.','');;?></td>
                </tr>
                <tr bgcolor="#E9F3FF">
					<td>Total Finish Fabric Required</td>
					<?
                    $grandTotalFinishFabReq=0;
					foreach($monthArr as $monthKey=>$monthName){
                       $grandTotalFinishFabReq+=$monthWiseFinishQtyArr[$monthKey];
						echo "<td align='right'>".number_format($monthWiseFinishQtyArr[$monthKey],2,'.','')."</td>";
                    }
                    ?>
                    <td align="right"><?= number_format($grandTotalFinishFabReq,2,'.','');;?></td>
                </tr>
                <tr bgcolor="#E9F3FF">
					<td>Total Finish Fabric Balance</td>
					<?
                    $grandTotalFinishFabBal=0;
					foreach($monthArr as $monthKey=>$monthName){
                       $grandTotalFinishFabBal+=($monthWiseFinishQtyArr[$monthKey]-$finishDataArrByDate[$monthKey]);
					    echo "<td align='right'>".number_format(($monthWiseFinishQtyArr[$monthKey]-$finishDataArrByDate[$monthKey]),2,'.','')."</td>";
                    }
                    ?>
                    <td align="right"><?= number_format($grandTotalFinishFabBal,2,'.','');; ?></td>
                </tr>
                
                
                <? 
				$s=0;  
				foreach($sewingDataArrByCat as $por_cat_id=>$rows){?>
                <tr>
					<? if($s==0){?>
                    <td rowspan="<?=count($sewingDataArrByCat)*2;?>" colspan="2" valign="middle">Sewing Information [Pcs]</td>
                    <? } ?>
					<td>Sewing Qty [<?=$por_cat_id;?>]</td>
					<?
                    $catTotal=0;
					foreach($monthArr as $monthKey=>$monthName){
                        $catTotal+=$rows[$monthKey];
						echo "<td align='right'>".$rows[$monthKey]."</td>";
                    }
                    ?>
                    <td align="right"><?=$catTotal;?></td>
                </tr>
                <tr>
					<td>Sewing Balance Qty [<?=$por_cat_id;?>]</td>
					<?
                    $catTotalBal=0;
					foreach($monthArr as $monthKey=>$monthName){
						$cal_qty=$orderDataArrByCat[$por_cat_id][$monthKey];
						$ttt=$rows[$monthKey];
                       // $catTotalBal+=($orderDataArrByCat[$por_cat_id][$monthKey]-$rows[$monthKey]); 
					   $catTotalBal+=($orderDataArrByCat[$por_cat_id][$monthKey]-$rows[$monthKey]);
						echo "<td align='right' title='POQty=$cal_qty=$ttt'>".($orderDataArrByCat[$por_cat_id][$monthKey]-$rows[$monthKey])."</td>";
                    }
                    ?>
                    <td align="right"><?=$catTotalBal;?></td>
                </tr>
                
                <?
				$s=1; 
				} 
				?>
			
            
                <?
				$s=0; 
				foreach($proCapDataArrByLocation as $location_id=>$row){ ?>
                
                <tr bgcolor="#E9F3FF">
					<? if($s==0){?>
                    <td rowspan="<?=count($proCapDataArrByLocation)+1;?>" colspan="2" valign="middle">Production Capacity Information  [Min]</td>
                    <? } ?>
					<td><?=$location_arr[$location_id];?></td>
					<?
                    $locationTotalCapacity=0;
					foreach($monthArr as $monthKey=>$monthName){
                        $locationTotalCapacity+=$row[$monthKey];
						echo "<td align='right'>".$row[$monthKey]."</td>";
                    }
                    ?>
                    <td align="right"><?=$locationTotalCapacity;?></td>
                </tr>
                <? 
				$s=1; 
				} 
				?>
                <tr bgcolor="#E9F3FF">
					<td><b>Total Capacity</b></td>
					<?
                    $totalCapacity=0;
					foreach($monthArr as $monthKey=>$monthName){
                        $totalCapacity+=$proCapDataArrByDate[$monthKey];
						echo "<td align='right'>".$proCapDataArrByDate[$monthKey]."</td>";
                    }
                    ?>
                    <td align="right"><?=$totalCapacity;?></td>
                </tr>

                <?
				$s=0; 
				foreach($proCapQtyDataArrByLocation as $location_id=>$row){ ?>
                
                <tr bgcolor="#E9F3FF">
					<? if($s==0){?>
                    <td rowspan="<?=count($proCapQtyDataArrByLocation)+1;?>" colspan="2" valign="middle">Production Capacity Information [Qty]</td>
                    <? } ?>
					<td><?=$location_arr[$location_id];?></td>
					<?
                    $locationTotalCapacityQty=0;
					foreach($monthArr as $monthKey=>$monthName){
                        $locationTotalCapacityQty+=$row[$monthKey];
						echo "<td align='right'>".$row[$monthKey]."</td>";
                    }
                    ?>
                    <td align="right"><?=$locationTotalCapacityQty;?></td>
                </tr>
                <? 
				$s=1; 
				} 
				?>
                <tr bgcolor="#E9F3FF">
					<td><b>Total Capacity</b></td>
					<?
                    $totalCapacityQty=0;
					foreach($monthArr as $monthKey=>$monthName){
                        $totalCapacityQty+=$proCapQtyDataArrByDate[$monthKey];
						echo "<td align='right'>".$proCapQtyDataArrByDate[$monthKey]."</td>";
                    }
                    ?>
                    <td align="right"><?=$totalCapacityQty;?></td>
                </tr>
            
                <tr>
					<td rowspan="2" colspan="2" valign="middle">Line and Working day Information</td>
					<td>Total Line Number</td>
					<?
					$total_line_number=0;
                    foreach($monthArr as $monthKey=>$monthName){
                        echo "<td align='right'>118</td>";
						$total_line_number+=118;
                    }
                    ?>
                    <td align="right"><?=$total_line_number;?></td>
                </tr>
                <tr>
					<td>Working days</td>
					<?
					$total_working_day=0;
                    foreach($monthArr as $monthKey=>$monthName){
                        echo "<td align='right'>".$workingDay[$monthKey]."</td>";
						$total_working_day+=$workingDay[$monthKey];
                    }
                    ?>
                    <td align="right"><?=$total_working_day;?></td>
                </tr>
            
            
            
            </tbody>

		</table>
	</div>       
 
        <?
		
		
		
		
		
		
	}//type end;


	
	foreach (glob("*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$filename=time().".xls";
	$create_new_doc = fopen($filename, 'w');
	$fdata=ob_get_contents();
	fwrite($create_new_doc,$fdata);
	ob_end_clean();
	echo "$fdata####$filename";
	exit();
}
?>