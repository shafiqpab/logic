<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Dash Board.
Functionality	:	
JS Functions	:
Created by		:	Nayem
Creation date 	: 	25-2-2021
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
extract($_REQUEST);
if($print==1){
	$printLink="../../";
	include("../../includes/common.php");
	echo load_html_head_contents("Graph", "../../", "",'', '', '', 1);
	$width="60";
}
else
{
	$width="92";
}

$type=1;// org shipment date
 
//--------------------------------------------------------------------------------------------------------------------

?>	
<script src="<? echo $printLink;?>Chart.js-master/Chart.min.js"></script>

	<?
        list($lcCompany,$location,$floor,$workingCompany)=explode('__',$_REQUEST['cp']);
		if($workingCompany){$company_cond=" and c.company_name=$workingCompany";}
		else if($lcCompany){$company_cond=" and c.company_name=$lcCompany";}
		else{$company_cond="";}
		
		$month_arr=array();	
        $month_prev=add_month(date("Y-m-d",time()),-2);
        $month_next=add_month(date("Y-m-d",time()),9);
        
       //------------------------------------------------------
	   $company_array = return_library_array("select id,company_name from lib_company where is_deleted=0","id","company_name");
	   $company_short_arr = return_library_array("select id,company_short_name from lib_company where is_deleted=0","id","company_short_name");
	   $commission_arr = return_library_array("select job_id,sum(commision_rate) as commision_rate from wo_pre_cost_commiss_cost_dtls where is_deleted=0 group by job_id","job_id","commision_rate");
	   $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	
    
       if($_SESSION['logic_erp']["month_arr"]=="")
	{
		###################### Delivery Status as Running Full Pending and Partial Deliverd START ###############################
			$running_sql="SELECT b.id as PO_ID,sum(b.po_quantity*c.total_set_qnty) AS QNTY,sum(b.po_total_price) AS ORDER_VALUE, b.UNIT_PRICE, b.IS_CONFIRMED, c.TOTAL_SET_QNTY, c.COMPANY_NAME, c.id as JOB_ID from wo_po_break_down b, wo_po_details_master c where b.job_no_mst=c.job_no and b.shiping_status<>3 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $company_cond group by b.id,b.unit_price,b.is_confirmed,c.total_set_qnty,c.company_name,c.id";
			// echo $running_sql;die;
			$running_com_arr=$running_po=$running_factoryValArr=$running_exFactoryValArr=$running_exFactoryQtyArr=$runningOrderQty=		$com_job_chk=$com_job_chk1=$com_job_chk2=$job_count_arr=array();
			$running_result=sql_select($running_sql);
			foreach($running_result as $row)
			{
				$running_com[$row['COMPANY_NAME']]=$row['COMPANY_NAME'];
				$running_po[$row['PO_ID']]=$row['PO_ID'];
				// $runningOrderQty[$row['COMPANY_NAME']]+=$row['QNTY'];
				$running_factoryValArr[$row['PO_ID']]=$row['UNIT_PRICE']/$row['TOTAL_SET_QNTY'];
				$commission_val=0;
				if($commission_arr[$row['JOB_ID']]>0)
				{
					$commission_val=(($row['ORDER_VALUE']*$commission_arr[$row['JOB_ID']])/100);
				}
				$commission_val=(($row['ORDER_VALUE']*$commission_arr[$row['JOB_ID']])/100);
				if($row['IS_CONFIRMED']==1)
				{
					$runningOrderQtyConfirmed[$row['COMPANY_NAME']]+=$row['QNTY'];
					$runningOrderGrossValConfirmed[$row['COMPANY_NAME']]+=$row['ORDER_VALUE'];
					$runningOrderNetValConfirmed[$row['COMPANY_NAME']]+=$row['ORDER_VALUE']-$commission_val;

					if(!in_array($row['JOB_ID'],$com_job_chk1[$row['COMPANY_NAME']]))
					{
						$com_job_chk1[$row['COMPANY_NAME']][]=$row['JOB_ID'];
						$job_count_confirmed_arr[$row['COMPANY_NAME']]++;
					}
				}
				if($row['IS_CONFIRMED']==2)
				{
					$runningOrderQtyProjected[$row['COMPANY_NAME']]+=$row['QNTY'];
					$runningOrderGrossValProjected[$row['COMPANY_NAME']]+=$row['ORDER_VALUE'];
					$runningOrderNetValProjected[$row['COMPANY_NAME']]+=$row['ORDER_VALUE']-$commission_val;

					if(!in_array($row['JOB_ID'],$com_job_chk2[$row['COMPANY_NAME']]))
					{
						$com_job_chk2[$row['COMPANY_NAME']][]=$row['JOB_ID'];
						$job_count_projected_arr[$row['COMPANY_NAME']]++;
					}
				}
			}

			$running_poId_in=where_con_using_array($running_po,0,'po_break_down_id');
			$running_exFactory_sql="SELECT PO_BREAK_DOWN_ID, sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as EX_FACTORY_QNTY,
				sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as EX_FACTORY_RETURN_QNTY from pro_ex_factory_mst where status_active=1 and is_deleted=0 $running_poId_in group by po_break_down_id";
			// echo $running_sql;die;
			$running_exFactory_result=sql_select($running_exFactory_sql);
			foreach($running_exFactory_result as $row)
			{
				$running_exFactoryQtyArr[$row['PO_BREAK_DOWN_ID']]=$row['EX_FACTORY_QNTY']-$row['EX_FACTORY_RETURN_QNTY'];
				$running_exFactoryValArr[$row['PO_BREAK_DOWN_ID']]=($row['EX_FACTORY_QNTY']-$row['EX_FACTORY_RETURN_QNTY'])*$running_factoryValArr[$row['PO_BREAK_DOWN_ID']];
			}
			$running_invoice_id_sql=sql_select("SELECT PO_BREAK_DOWN_ID, INVOICE_NO from pro_ex_factory_mst where status_active=1 and is_deleted=0 $running_poId_in ");
			$running_invoiceIdArr=$running_exFactoryPoIdArr=array();
			foreach($running_invoice_id_sql as $row)
			{
				if($row['INVOICE_NO']){$running_invoiceIdArr[$row['INVOICE_NO']]=$row['INVOICE_NO'];}
				$running_exFactoryPoIdArr[$row['PO_BREAK_DOWN_ID']]=$row['PO_BREAK_DOWN_ID'];
			}
			$running_po_id_in=where_con_using_array($running_exFactoryPoIdArr,0,'b.po_breakdown_id');
			$running_invoice_id_in=where_con_using_array($running_invoiceIdArr,0,'a.id');
			$running_exInvoice_sql="SELECT a.id, a.INVOICE_VALUE, a.NET_INVO_VALUE, b.PO_BREAKDOWN_ID, b.CURRENT_INVOICE_VALUE from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b where a.id=b.mst_id $running_po_id_in $running_invoice_id_in and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
			$running_exInvoice_data=sql_select($running_exInvoice_sql);
			$running_exInvoiceArr=array();
			foreach($running_exInvoice_data as $row)
			{
				if($row['CURRENT_INVOICE_VALUE']){$running_exInvoiceArr[$row['PO_BREAKDOWN_ID']]+=($row['NET_INVO_VALUE']/$row['INVOICE_VALUE'])*$row['CURRENT_INVOICE_VALUE'];}
			}

			foreach($running_result as $row)
			{
				$runningExFactoryQtyCom[$row['COMPANY_NAME']]+=$running_exFactoryQtyArr[$row['PO_ID']];
				$runningExFactoryValCom[$row['COMPANY_NAME']]+=$running_exFactoryValArr[$row['PO_ID']];
				$runningExFactoryNetValCom[$row['COMPANY_NAME']]+=$running_exInvoiceArr[$row['PO_ID']];
				if($running_exFactoryQtyArr[$row['PO_ID']]>0)
				{
					if(!in_array($row['JOB_ID'],$com_job_chk[$row['COMPANY_NAME']])){
						$com_job_chk[$row['COMPANY_NAME']][]=$row['JOB_ID'];
						$job_count_export_arr[$row['COMPANY_NAME']]++;
					}
				}
			}
		###################### Delivery Status as Running Full Pending and Partial Deliverd END ###############################
		
		if($db_type==0)
        {
            $startDate = date("Y-m-d",strtotime($month_prev));
            $endDate = date("Y-m-t",strtotime($month_next));
        }
        else
        {
            $startDate = date("d-M-Y", strtotime($month_prev));
            $endDate = date("t-M-Y", strtotime($month_next));
        }

        // $sql_result="SELECT c.buyer_name,b.id as po_id,b.shipment_date,sum(b.po_quantity*c.total_set_qnty) AS qnty,sum(b.po_total_price) AS order_value, b.unit_price, b.shiping_status, c.total_set_qnty, c.company_name, c.id as JOB_ID from wo_po_break_down b, wo_po_details_master c where b.job_no_mst=c.job_no and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.shipment_date >='$startDate' and b.shipment_date <='$endDate' and b.is_confirmed=1 and b.status_active=1 $company_cond group by c.buyer_name,b.id,b.shipment_date,b.unit_price,b.shiping_status,c.total_set_qnty,c.company_name,c.id";

        $sql_result="SELECT b.id as PO_ID, b.SHIPMENT_DATE, sum(b.po_quantity*c.total_set_qnty) AS QNTY, sum(b.po_total_price) AS ORDER_VALUE, b.UNIT_PRICE, b.SHIPING_STATUS, b.IS_CONFIRMED, c.TOTAL_SET_QNTY, c.COMPANY_NAME, c.id as JOB_ID, c.BUYER_NAME from wo_po_break_down b, wo_po_details_master c where b.job_no_mst=c.job_no and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.shipment_date >='$startDate' and b.shipment_date <='$endDate' and b.status_active=1 $company_cond group by b.id,b.shipment_date,b.unit_price,b.shiping_status,b.is_confirmed,c.total_set_qnty,c.company_name,c.id,c.buyer_name";

		$order_result=sql_select($sql_result);
		// echo $sql_result;die;
		$orderIdArr=$factoryValArr=$company_arr=$shiping_status_arr=array();
		$orderQtyArrConfirmed=$orderValArrConfirmed=$orderNetValArrConfirmed=$orderQtyComConfirmed=$orderValComConfirmed=$orderNetValComConfirmed=array();
		$orderQtyArrProjected=$orderValArrProjected=$orderNetValArrProjected=$orderQtyComProjected=$orderValComProjected=$orderNetValComProjected=array();
		$buyerWisePoQtyArrConfirmed=$buyerWisePoQtyArrProjected=$buyerWisePoArr=$poWisebuyerArr=$buyerIdArr=array();
		foreach($order_result as $row)
		{ 
			$monthKey=date("M y",strtotime($row['SHIPMENT_DATE']));
			$company_arr[$row['COMPANY_NAME']]=$row['COMPANY_NAME'];
			$shiping_status_arr[$row['PO_ID']]=$row['SHIPING_STATUS'];

			$commission_amt=0;
			if($commission_arr[$row['JOB_ID']]>0)
			{
				$commission_amt=(($row['ORDER_VALUE']*$commission_arr[$row['JOB_ID']])/100);
			}
			if($row['IS_CONFIRMED']==1)
			{
				$orderQtyArrConfirmed[$monthKey]+=$row['QNTY'];
				$orderValArrConfirmed[$monthKey]+=$row['ORDER_VALUE'];
				$orderNetValArrConfirmed[$monthKey]+=$row['ORDER_VALUE']-$commission_amt;
				$orderQtyComConfirmed[$row['COMPANY_NAME']][$monthKey]+=$row['QNTY'];
				$orderValComConfirmed[$row['COMPANY_NAME']][$monthKey]+=$row['ORDER_VALUE'];
				$orderNetValComConfirmed[$row['COMPANY_NAME']][$monthKey]+=$row['ORDER_VALUE']-$commission_amt;
			}
			if($row['IS_CONFIRMED']==2)
			{
				$orderQtyArrProjected[$monthKey]+=$row['QNTY'];
				$orderValArrProjected[$monthKey]+=$row['ORDER_VALUE'];
				$orderNetValArrProjected[$monthKey]+=$row['ORDER_VALUE']-$commission_amt;
				$orderQtyComProjected[$row['COMPANY_NAME']][$monthKey]+=$row['QNTY'];
				$orderValComProjected[$row['COMPANY_NAME']][$monthKey]+=$row['ORDER_VALUE'];
				$orderNetValComProjected[$row['COMPANY_NAME']][$monthKey]+=$row['ORDER_VALUE']-$commission_amt;
			}

			$orderIdArr[$row['PO_ID']]=$row['PO_ID'];
			$factoryValArr[$row['PO_ID']]=$row['UNIT_PRICE']/$row['TOTAL_SET_QNTY'];
			if($row['IS_CONFIRMED']==1)
			{
				$monthWisePoArr[$monthKey][$row['PO_ID']]=$row['PO_ID'];
				$monthWisePoComArr[$row['COMPANY_NAME']][$monthKey][$row['PO_ID']]=$row['PO_ID'];
			}
			$monthWisePoArrQty[$row['PO_ID']]=$row['QNTY'];
			$monthWisePoArrVal[$row['PO_ID']]=$row['ORDER_VALUE'];
			$monthWiseJobArr[$row['PO_ID']]=$row['JOB_ID'];

			if($monthKey==date("M y",time())){
				$buyerIdArr[$row['BUYER_NAME']]=$row['BUYER_NAME'];
				$buyerWisePoArr[$row['BUYER_NAME']][$row['PO_ID']]=$row['PO_ID'];
				$poWisebuyerArr[$row['PO_ID']]=$row['BUYER_NAME'];
				if($row['IS_CONFIRMED']==1){ $buyerWisePoQtyArrConfirmed[$row['BUYER_NAME']]+=$row['QNTY']; }
				if($row['IS_CONFIRMED']==2){ $buyerWisePoQtyArrProjected[$row['BUYER_NAME']]+=$row['QNTY']; }
			}
		}
		ksort($company_arr);
		// print_r($buyerIdArr);die;
		//----------------------------------------------------------------------------------------
		$exFactoryQtyArr=array();$exFactoryValArr=array();
		$poId_in=where_con_using_array($orderIdArr,0,'po_break_down_id');

		$exfactory_sql="SELECT PO_BREAK_DOWN_ID, sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as EX_FACTORY_QNTY,
			sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as EX_FACTORY_RETURN_QNTY from pro_ex_factory_mst where status_active=1 and is_deleted=0 $poId_in group by po_break_down_id";

		$exfactory_result=sql_select($exfactory_sql);
		// echo $sql;die;
		foreach($exfactory_result as $row)
		{
			// $exFactoryQtyArr[$row[csf('po_break_down_id')]]=$row[csf('ex_factory_qnty')]-$row[csf('ex_factory_return_qnty')];
			$exFactoryValArr[$row['PO_BREAK_DOWN_ID']]=($row['EX_FACTORY_QNTY']-$row['EX_FACTORY_RETURN_QNTY'])*$factoryValArr[$row['PO_BREAK_DOWN_ID']];
			$cuntryExFactoryQtyArr[$row['PO_BREAK_DOWN_ID']]=$row['EX_FACTORY_QNTY']-$row['EX_FACTORY_RETURN_QNTY'];
		}
		// var_dump($cuntryExFactoryQtyArr);die;
		$invoice_id_sql=sql_select("SELECT PO_BREAK_DOWN_ID, INVOICE_NO from pro_ex_factory_mst where status_active=1 and is_deleted=0 $poId_in ");
		$invoiceIdArr=$exFactoryPoIdArr=array();
		foreach($invoice_id_sql as $row)
		{
			if($row['INVOICE_NO']){$invoiceIdArr[$row['INVOICE_NO']]=$row['INVOICE_NO'];}
			$exFactoryPoIdArr[$row['PO_BREAK_DOWN_ID']]=$row['PO_BREAK_DOWN_ID'];
		}
		$po_id_in=where_con_using_array($exFactoryPoIdArr,0,'b.po_breakdown_id');
		$invoice_id_in=where_con_using_array($invoiceIdArr,0,'a.id');
		$exInvoice_sql="SELECT a.id, a.INVOICE_VALUE, a.NET_INVO_VALUE, b.PO_BREAKDOWN_ID, b.CURRENT_INVOICE_VALUE from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b where a.id=b.mst_id $po_id_in $invoice_id_in and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
		$exInvoice_data=sql_select($exInvoice_sql);
		$exInvoiceArr=array();
		foreach($exInvoice_data as $row)
		{
			if($row['CURRENT_INVOICE_VALUE']){$exInvoiceArr[$row['PO_BREAKDOWN_ID']]+=($row['NET_INVO_VALUE']/$row['INVOICE_VALUE'])*$row['CURRENT_INVOICE_VALUE'];}
		}
		
		//----------------------------------------------------------------------------------------
		// $remain_months=datediff( "m",$month_prev,date("Y-m-d",strtotime($maxDate)));
		$remain_months=datediff( "m",$month_prev,date("Y-m-d",strtotime($month_next)));
	    for($e=0;$e<=$remain_months;$e++)
        {
            $tmp=add_month(date("Y-m-d",strtotime($month_prev)),$e);
            $month_arr[$e]=date("M y",strtotime($tmp));
        }
		   
        $order_qty_arr_confirm=$order_qty_arr_project=array();$exFactory_qty_array=array();$order_net_val_array=array();
		$shortQty=array();$shortQtyArr=array();$shortVal=array();$shortValArr=array();$overQty=array();$overQtyArr=array();$overVal=array();$overValArr=array();
		// print_r($orderQtyArrProjected);die;
		foreach($month_arr as $key=>$monthYear)
        {
            $order_qty_arr_confirm[]=number_format($orderQtyArrConfirmed[$monthYear],0,'.','');
            $order_qty_arr_project[]=number_format($orderQtyArrProjected[$monthYear],0,'.','');
			// $order_net_val_array[]=number_format($orderNetValArr[$monthYear],0,'.','');
           	
			$exFactory_qty=$exFactory_val=$short_Qty_Arr=$short_Val_Arr=$over_Qty_Arr=$over_Val_Arr=$exFactory_net_val=$short_Net_Val_Arr=$over_Net_Val_Arr=0;	
			foreach($monthWisePoArr[$monthYear] as $po)
			{
				$exFactory_qty+=$cuntryExFactoryQtyArr[$po];
				$exFactory_val+=$exFactoryValArr[$po];

				$exFactory_net_val+=$exInvoiceArr[$po];

				if($monthYear==date("M y",time())){
					$buyerWiseExportQtyArr[$poWisebuyerArr[$po]]+=$cuntryExFactoryQtyArr[$po];
				}

				$all_pcs_rate=$monthWisePoArrVal[$po]/$monthWisePoArrQty[$po];
				if($commission_arr[$monthWiseJobArr[$po]]>0)
				{
					$all_pcs_rate_after_commission=$all_pcs_rate-(($all_pcs_rate*$commission_arr[$monthWiseJobArr[$po]])/100);
				}
				else
				{
					$all_pcs_rate_after_commission=$all_pcs_rate;
				}
				$order_qty_fnc=$monthWisePoArrQty[$po];
				$exFactory_qty_fnc=$cuntryExFactoryQtyArr[$po];
				
				if($order_qty_fnc>$exFactory_qty_fnc){
					$short_Qty_Arr+=($order_qty_fnc-$exFactory_qty_fnc);
					$short_Val_Arr+=(($order_qty_fnc-$exFactory_qty_fnc)*$all_pcs_rate);
					$short_Net_Val_Arr+=(($order_qty_fnc-$exFactory_qty_fnc)*$all_pcs_rate_after_commission);
				}
				if($exFactory_qty_fnc>$order_qty_fnc){
					$over_Qty_Arr+=($exFactory_qty_fnc-$order_qty_fnc);
					$over_Val_Arr+=(($exFactory_qty_fnc-$order_qty_fnc)*$all_pcs_rate);
					$over_Net_Val_Arr+=($exInvoiceArr[$po]/$exFactoryValArr[$po])*($exFactory_qty_fnc-$order_qty_fnc)*$all_pcs_rate;
				}	
			}

			// var_dump($exFactoryValArr);die;
			$exFactory_qty_array[]=number_format($exFactory_qty,0,'.','');
			$exFactoryQtyArr[$monthYear]=number_format($exFactory_qty,0,'.','');
			$short_qty_Array[]=number_format($short_Qty_Arr,0,'.','');
			$shortQtyArr[$monthYear]=number_format($short_Qty_Arr,0,'.','');
			$shortValArr[$monthYear]=number_format($short_Val_Arr,0,'.','');
			$shortNetValArr[$monthYear]=number_format($short_Net_Val_Arr,0,'.','');
			$overQtyArr[$monthYear]=number_format($over_Qty_Arr,0,'.','');
			$overValArr[$monthYear]=number_format($over_Val_Arr,0,'.','');
			$overNetValArr[$monthYear]=number_format($over_Net_Val_Arr,0,'.','');
			if($exFactory_val!=''){$exFactoryValArr[$monthYear]=number_format($exFactory_val,2,'.','');}
			if($exFactory_net_val!=''){$exFactoryNetValArr[$monthYear]=number_format($exFactory_net_val,2,'.','');}

        }

		foreach($company_arr as $com=>$com_nam)
		{
			foreach($month_arr as $key=>$monthYear)
			{ 
				$exFactory_qty_com=$exFactory_val_com=$short_Qty_Arr=$short_Val_Arr=$over_Qty_Arr=$exFactory_Net_val_com=$over_Val_Arr=$short_Net_Val_Arr=$over_Net_Val_Arr=0;	
				foreach($monthWisePoComArr[$com_nam][$monthYear] as  $po)
				{
						$exFactory_qty_com+=$cuntryExFactoryQtyArr[$po];
						$exFactory_val_com+=$exFactoryValArr[$po];
						$exFactory_Net_val_com+=$exInvoiceArr[$po];

						if($monthYear==date("M y",time())){
							//$buyerWiseExportQtyArr[$poWisebuyerArr[$po]]+=$cuntryExFactoryQtyArr[$po];
						}
						$all_pcs_rate=$monthWisePoArrVal[$po]/$monthWisePoArrQty[$po];
						if($commission_arr[$monthWiseJobArr[$po]]>0)
						{
							$all_pcs_rate_after_commission=$all_pcs_rate-(($all_pcs_rate*$commission_arr[$monthWiseJobArr[$po]])/100);
						}
						else
						{
							$all_pcs_rate_after_commission=$all_pcs_rate;
						}
						$order_qty_fnc=$monthWisePoArrQty[$po];
						$exFactory_qty_fnc=$cuntryExFactoryQtyArr[$po];
						if($order_qty_fnc>$exFactory_qty_fnc){
							$short_Qty_Arr+=($order_qty_fnc-$exFactory_qty_fnc);
							$short_Val_Arr+=(($order_qty_fnc-$exFactory_qty_fnc)*$all_pcs_rate);
							$short_Net_Val_Arr+=(($order_qty_fnc-$exFactory_qty_fnc)*$all_pcs_rate_after_commission);
						}
						if($exFactory_qty_fnc>$order_qty_fnc){
							$over_Qty_Arr+=($exFactory_qty_fnc-$order_qty_fnc);
							$over_Val_Arr+=(($exFactory_qty_fnc-$order_qty_fnc)*$all_pcs_rate);
							// $over_Net_Val_Arr+=(($exFactory_qty_fnc-$order_qty_fnc)*$all_pcs_rate_after_commission);
							$over_Net_Val_Arr+=($exInvoiceArr[$po]/$exFactoryValArr[$po])*($exFactory_qty_fnc-$order_qty_fnc)*$all_pcs_rate;
						}

					$exFactoryQtyComArr[$com_nam][$monthYear]=number_format($exFactory_qty_com,0,'.','');
					$shortQty[$com_nam][$monthYear]=number_format($short_Qty_Arr,0,'.','');
					$shortVal[$com_nam][$monthYear]=number_format($short_Val_Arr,0,'.','');
					$shortNetVal[$com_nam][$monthYear]=number_format($short_Net_Val_Arr,0,'.','');
					$overQty[$com_nam][$monthYear]=number_format($over_Qty_Arr,0,'.','');
					$overVal[$com_nam][$monthYear]=number_format($over_Val_Arr,0,'.','');
					$overNetVal[$com_nam][$monthYear]=number_format($over_Net_Val_Arr,0,'.','');
					if($exFactory_val_com!=''){$exFactoryValComArr[$com_nam][$monthYear]=number_format($exFactory_val_com,2,'.','');}
					if($exFactory_Net_val_com!=''){$exFactoryNetValComArr[$com_nam][$monthYear]=number_format($exFactory_Net_val_com,2,'.','');}
				}

			}
		}
		// var_dump($monthWisePoArr['Jul 19']);
		// var_dump($cuntryExFactoryQtyArr[16]);

		##################### Delivery Status as Running Full Pending and Partial Deliverd ##################################
			$_SESSION['logic_erp']["job_count_confirmed_arr"]=$job_count_confirmed_arr;	
			$_SESSION['logic_erp']["job_count_projected_arr"]=$job_count_projected_arr;
			$_SESSION['logic_erp']["job_count_export_arr"]=$job_count_export_arr;	
			$_SESSION['logic_erp']["runningOrderQtyConfirmed"]=$runningOrderQtyConfirmed;	
			$_SESSION['logic_erp']["runningOrderQtyProjected"]=$runningOrderQtyProjected;	
			$_SESSION['logic_erp']["runningOrderGrossValConfirmed"]=$runningOrderGrossValConfirmed;
			$_SESSION['logic_erp']["runningOrderGrossValProjected"]=$runningOrderGrossValProjected;
			$_SESSION['logic_erp']["runningOrderNetValConfirmed"]=$runningOrderNetValConfirmed;
			$_SESSION['logic_erp']["runningOrderNetValProjected"]=$runningOrderNetValProjected;
			$_SESSION['logic_erp']["runningExFactoryQtyCom"]=$runningExFactoryQtyCom;
			$_SESSION['logic_erp']["runningExFactoryValCom"]=$runningExFactoryValCom;
			$_SESSION['logic_erp']["runningExFactoryNetValCom"]=$runningExFactoryNetValCom;
		##################### All Company Monthly Projected AND Confirmed ##################################
			$_SESSION['logic_erp']["month_arr"]=$month_arr;	
			$_SESSION['logic_erp']["company_arr"]=$company_arr;	
			$_SESSION['logic_erp']["orderQtyArrConfirmed"]=$orderQtyArrConfirmed;
			$_SESSION['logic_erp']["orderQtyArrProjected"]=$orderQtyArrProjected;
			$_SESSION['logic_erp']["orderValArrConfirmed"]=$orderValArrConfirmed;
			$_SESSION['logic_erp']["orderValArrProjected"]=$orderValArrProjected;
			$_SESSION['logic_erp']["orderNetValArrConfirmed"]=$orderNetValArrConfirmed;
			$_SESSION['logic_erp']["orderNetValArrProjected"]=$orderNetValArrProjected;
			$_SESSION['logic_erp']["exFactoryQtyArr"]=$exFactoryQtyArr;
			$_SESSION['logic_erp']["exFactoryValArr"]=$exFactoryValArr;
			$_SESSION['logic_erp']["exFactoryNetValArr"]=$exFactoryNetValArr;
			$_SESSION['logic_erp']["overQtyArr"]=$overQtyArr;
			$_SESSION['logic_erp']["overValArr"]=$overValArr;
			$_SESSION['logic_erp']["overNetValArr"]=$overNetValArr;
			$_SESSION['logic_erp']["shortQtyArr"]=$shortQtyArr;
			$_SESSION['logic_erp']["shortValArr"]=$shortValArr;
			$_SESSION['logic_erp']["shortNetValArr"]=$shortNetValArr;
		##################### Company Wise Monthly Projected AND Confirmed ##################################
			$_SESSION['logic_erp']["orderQtyComConfirmed"]=$orderQtyComConfirmed;
			$_SESSION['logic_erp']["orderQtyComProjected"]=$orderQtyComProjected;
			$_SESSION['logic_erp']["orderValComConfirmed"]=$orderValComConfirmed;
			$_SESSION['logic_erp']["orderValComProjected"]=$orderValComProjected;
			$_SESSION['logic_erp']["orderNetValComConfirmed"]=$orderNetValComConfirmed;
			$_SESSION['logic_erp']["orderNetValComProjected"]=$orderNetValComProjected;
			$_SESSION['logic_erp']["exFactoryQtyComArr"]=$exFactoryQtyComArr;
			$_SESSION['logic_erp']["exFactoryValComArr"]=$exFactoryValComArr;
			$_SESSION['logic_erp']["exFactoryNetValComArr"]=$exFactoryNetValComArr;
			$_SESSION['logic_erp']["overQty"]=$overQty;
			$_SESSION['logic_erp']["overVal"]=$overVal;
			$_SESSION['logic_erp']["overNetVal"]=$overNetVal;
			$_SESSION['logic_erp']["shortQty"]=$shortQty;
			$_SESSION['logic_erp']["shortVal"]=$shortVal;
			$_SESSION['logic_erp']["shortNetVal"]=$shortNetVal;
		##################### Print for Buyer Wise ##################################
			$_SESSION['logic_erp']["buyerIdArr"]=$buyerIdArr;
			$_SESSION['logic_erp']["buyerWisePoQtyArrConfirmed"]=$buyerWisePoQtyArrConfirmed;
			$_SESSION['logic_erp']["buyerWisePoQtyArrProjected"]=$buyerWisePoQtyArrProjected;
			$_SESSION['logic_erp']["buyerWiseExportQtyArr"]=$buyerWiseExportQtyArr;
		##################### Grape ##################################
			$_SESSION['logic_erp']["order_qty_arr_confirm"]=$order_qty_arr_confirm;	
			$_SESSION['logic_erp']["order_qty_arr_project"]=$order_qty_arr_project;	
			$_SESSION['logic_erp']["exFactory_qty_array"]=$exFactory_qty_array;
			$_SESSION['logic_erp']["short_qty_Array"]=$short_qty_Array;
	}
	else
	{
		##################### Delivery Status as Running Full Pending and Partial Deliverd ##################################
			$job_count_confirmed_arr=$_SESSION['logic_erp']["job_count_confirmed_arr"];
			$job_count_projected_arr=$_SESSION['logic_erp']["job_count_projected_arr"];
			$job_count_export_arr=$_SESSION['logic_erp']["job_count_export_arr"];
			$runningOrderQtyConfirmed=$_SESSION['logic_erp']["runningOrderQtyConfirmed"];
			$runningOrderQtyProjected=$_SESSION['logic_erp']["runningOrderQtyProjected"];
			$runningOrderGrossValConfirmed=$_SESSION['logic_erp']["runningOrderGrossValConfirmed"];
			$runningOrderGrossValProjected=$_SESSION['logic_erp']["runningOrderGrossValProjected"];
			$runningOrderNetValConfirmed=$_SESSION['logic_erp']["runningOrderNetValConfirmed"];
			$runningOrderNetValProjected=$_SESSION['logic_erp']["runningOrderNetValProjected"];
			$runningExFactoryQtyCom=$_SESSION['logic_erp']["runningExFactoryQtyCom"];
			$runningExFactoryValCom=$_SESSION['logic_erp']["runningExFactoryValCom"];
			$runningExFactoryNetValCom=$_SESSION['logic_erp']["runningExFactoryNetValCom"];
		##################### All Company Monthly Projected AND Confirmed ##################################
			$month_arr=$_SESSION['logic_erp']["month_arr"];	
			$company_arr=$_SESSION['logic_erp']["company_arr"];	
			$orderQtyArrConfirmed=$_SESSION['logic_erp']["orderQtyArrConfirmed"];	
			$orderQtyArrProjected=$_SESSION['logic_erp']["orderQtyArrProjected"];	
			$orderValArrConfirmed=$_SESSION['logic_erp']["orderValArrConfirmed"];
			$orderValArrProjected=$_SESSION['logic_erp']["orderValArrProjected"];
			$orderNetValArrConfirmed=$_SESSION['logic_erp']["orderNetValArrConfirmed"];
			$orderNetValArrProjected=$_SESSION['logic_erp']["orderNetValArrProjected"];
			$exFactoryQtyArr=$_SESSION['logic_erp']["exFactoryQtyArr"];
			$exFactoryValArr=$_SESSION['logic_erp']["exFactoryValArr"];
			$exFactoryNetValArr=$_SESSION['logic_erp']["exFactoryNetValArr"];
			$overQtyArr=$_SESSION['logic_erp']["overQtyArr"];
			$overValArr=$_SESSION['logic_erp']["overValArr"];
			$overNetValArr=$_SESSION['logic_erp']["overNetValArr"];
			$shortQtyArr=$_SESSION['logic_erp']["shortQtyArr"];
			$shortValArr=$_SESSION['logic_erp']["shortValArr"];
			$shortNetValArr=$_SESSION['logic_erp']["shortNetValArr"];
		##################### Company Wise Monthly Projected AND Confirmed ##################################
			$orderQtyComConfirmed=$_SESSION['logic_erp']["orderQtyComConfirmed"];
			$orderQtyComProjected=$_SESSION['logic_erp']["orderQtyComProjected"];
			$orderValComConfirmed=$_SESSION['logic_erp']["orderValComConfirmed"];
			$orderValComProjected=$_SESSION['logic_erp']["orderValComProjected"];
			$orderNetValComConfirmed=$_SESSION['logic_erp']["orderNetValComConfirmed"];
			$orderNetValComProjected=$_SESSION['logic_erp']["orderNetValComProjected"];
			$exFactoryQtyComArr=$_SESSION['logic_erp']["exFactoryQtyComArr"];
			$exFactoryValComArr=$_SESSION['logic_erp']["exFactoryValComArr"];
			$exFactoryNetValComArr=$_SESSION['logic_erp']["exFactoryNetValComArr"];
			$overQty=$_SESSION['logic_erp']["overQty"];
			$overVal=$_SESSION['logic_erp']["overVal"];
			$overNetVal=$_SESSION['logic_erp']["overNetVal"];
			$shortQty=$_SESSION['logic_erp']["shortQty"];
			$shortVal=$_SESSION['logic_erp']["shortVal"];
			$shortNetVal=$_SESSION['logic_erp']["shortNetVal"];
		##################### Print for Buyer Wise ##################################
			$buyerIdArr=$_SESSION['logic_erp']["buyerIdArr"];
			$buyerWisePoQtyArrConfirmed=$_SESSION['logic_erp']["buyerWisePoQtyArrConfirmed"];
			$buyerWisePoQtyArrProjected=$_SESSION['logic_erp']["buyerWisePoQtyArrProjected"];
			$buyerWiseExportQtyArr=$_SESSION['logic_erp']["buyerWiseExportQtyArr"];
		##################### Grape ##################################
		$order_qty_arr_confirm=$_SESSION['logic_erp']["order_qty_arr_confirm"];	
		$order_qty_arr_project=$_SESSION['logic_erp']["order_qty_arr_project"];	
		$exFactory_qty_array=$_SESSION['logic_erp']["exFactory_qty_array"];
		$short_qty_Array=$_SESSION['logic_erp']["short_qty_Array"];
	}
	
	$monthArray= json_encode($month_arr); 
	$order_qty_arr_confirm= json_encode($order_qty_arr_confirm); 
	$order_qty_arr_project= json_encode($order_qty_arr_project); 
	$exFactory_qty_array= json_encode($exFactory_qty_array); 
	$short_qty_Array= json_encode($short_qty_Array); 

    ?>
	<div style="margin:10px;width:100%;" >
        <div style="width:<? echo $width;?>%;float:left; overflow:hidden; margin-left:10px; margin-bottom:10px; border:solid 1px;">
			<table class="orderExportData canvas_info" border="1" rules="all">
				<thead>
					<tr>
						<th colspan="13" bgcolor="#D5D9D8">In Hand Summery</th>
					</tr>
					<tr>
						<th rowspan="2" bgcolor="#FFFF00">Order Company</th>
						<th colspan="4" bgcolor="#ffb6c1">Total Projected Order [Pcs]</th>
						<th colspan="4" bgcolor="#8DB4E2">Total Confirm Order [Pcs]</th>
						<th colspan="4" bgcolor="#A6FF79">Total Export Status [Pcs]</th>
					</tr>
					<tr>
						<th bgcolor="#ffb6c1">Order Qty</th>
						<th bgcolor="#ffb6c1">Gross Value $</th>
						<th bgcolor="#ffb6c1">Net Value $</th>
						<th bgcolor="#ffb6c1">Job Count</th>
						<th bgcolor="#8DB4E2">Order Qty</th>
						<th bgcolor="#8DB4E2">Gross Value $</th>
						<th bgcolor="#8DB4E2">Net Value $</th>
						<th bgcolor="#8DB4E2">Job Count</th>
						<th bgcolor="#A6FF79">Export Qty</th>
						<th bgcolor="#A6FF79">Gross Value $</th>
						<th bgcolor="#A6FF79">Net Value $</th>
						<th bgcolor="#A6FF79">Job Count</th>
					</tr>
				</thead>
				<tbody>
				<?
					foreach($company_arr as $k=>$v)
					{
						?>
							<tr>
								<td width="100"><b><? echo $company_short_arr[$v];?></b></td>
								<td width="100" align='right'><? echo number_format($runningOrderQtyProjected[$v]); ?>&nbsp;</td>
								<td width="100" align='right'><? echo number_format($runningOrderGrossValProjected[$v]); ?>&nbsp;</td>
								<td width="100" align='right'><? echo number_format($runningOrderNetValProjected[$v]); ?>&nbsp;</td>
								<td width="60" align='right'><? echo $job_count_projected_arr[$v]; ?>&nbsp;</td>
								<td width="100" align='right'><? echo number_format($runningOrderQtyConfirmed[$v]); ?>&nbsp;</td>
								<td width="100" align='right'><? echo number_format($runningOrderGrossValConfirmed[$v]); ?>&nbsp;</td>
								<td width="100" align='right'><? echo number_format($runningOrderNetValConfirmed[$v]); ?>&nbsp;</td>
								<td width="60" align='right'><? echo $job_count_confirmed_arr[$v]; ?></td>
								<td width="100" align='right'><? echo number_format($runningExFactoryQtyCom[$v]); ?>&nbsp;</td>
								<td width="100" align='right'><? echo number_format($runningExFactoryValCom[$v]); ?>&nbsp;</td>
								<td width="100" align='right'><? echo number_format($runningExFactoryNetValCom[$v],2); ?>&nbsp;</td>
								<td width="80" align='right'><? echo $job_count_export_arr[$v];?></td>
							</tr>
						<?
					}
				?>
				</tbody>
			</table>
			<canvas id="canvas"></canvas>
            <table class="orderExportData canvas_info" border="1" rules="all">
				<tbody>
					<tr bgcolor="#D5D9D8">
						<td width="100"><b>All Company</b></td>
						<? foreach($month_arr as $month){echo "<td width='100' align='center'><b>" . $month . "</b></td>";} ?>
					</tr>
					<tr>
						<td><b>Projected Order Qty(pcs)</b></td>
						<? foreach($month_arr as $month){echo "<td align='right'>" ;if(!empty($orderQtyArrProjected[$month])){echo number_format($orderQtyArrProjected[$month]);}echo "&nbsp;</td>";} ?>
					</tr>
					<tr>
						<td><b>Projected Order Gross Value ($)</b></td>
						<? foreach($month_arr as $month){echo "<td align='right'>" ;if(!empty($orderValArrProjected[$month])){echo number_format($orderValArrProjected[$month],2) ;}echo "&nbsp;</td>";} ?>
					</tr>
					<tr>
						<td style="border-bottom: 1px solid yellow;"><b>Projected Order Net Value ($)</b></td>
						<? foreach($month_arr as $month){echo "<td align='right' style='border-bottom: 1px solid yellow;'>" ;if(!empty($orderNetValArrProjected[$month])){echo number_format($orderNetValArrProjected[$month],2) ;}echo "&nbsp;</td>";} ?>
					</tr>
					<tr>
						<td style="border-top: 1px solid yellow;"><b>Confirmed Order Qty(pcs)</b></td>
						<? foreach($month_arr as $month){echo "<td align='right' style='border-top: 1px solid yellow;'>" ;if(!empty($orderQtyArrConfirmed[$month])){echo number_format($orderQtyArrConfirmed[$month]);}echo "&nbsp;</td>";} ?>
					</tr>
					<tr>
						<td><b>Confirmed Order Gross Value ($)</b></td>
						<? foreach($month_arr as $month){echo "<td align='right'>" ;if(!empty($orderValArrConfirmed[$month])){echo number_format($orderValArrConfirmed[$month],2) ;}echo "&nbsp;</td>";} ?>
					</tr>
					<tr>
						<td><b>Confirmed Order Net Value ($)</b></td>
						<? foreach($month_arr as $month){echo "<td align='right'>" ;if(!empty($orderNetValArrConfirmed[$month])){echo number_format($orderNetValArrConfirmed[$month],2) ;}echo "&nbsp;</td>";} ?>
					</tr>
					<tr>
						<td><b>Export Qty(pcs)</b></td>
						<? foreach($month_arr as $month){echo "<td align='right' style='color:green;'>" ;if(!empty($exFactoryQtyArr[$month])){echo number_format($exFactoryQtyArr[$month]);}echo "&nbsp;</td>";} ?>
					</tr>
					<tr>
						<td><b>Export Gross Value ($)</b></td>
						<? foreach($month_arr as $month){echo "<td align='right' style='color:green;'>" ;if(!empty($exFactoryValArr[$month])){echo number_format($exFactoryValArr[$month],2);}echo "&nbsp;</td>";} ?>
					</tr>
					<tr>
						<td><b>Export Net Value ($)</b></td>
						<? foreach($month_arr as $month){echo "<td align='right' style='color:green;'>" ;if(!empty($exFactoryNetValArr[$month])){echo number_format($exFactoryNetValArr[$month],2);}echo "&nbsp;</td>";} ?>
					</tr>
					<tr>
						<td><b>Excess Export Qty (pcs)</b></td>
						<? foreach($month_arr as $month){echo "<td align='right' style='color:green;'>" ;if(!empty($overQtyArr[$month])){echo number_format($overQtyArr[$month]);}echo "&nbsp;</td>";} ?>
					</tr>
					<tr>
						<td><b>Excess Export Gross Value ($)</b></td>
						<? foreach($month_arr as $month){echo "<td align='right' style='color:green;'>" ;if(!empty($overValArr[$month])){echo number_format($overValArr[$month],2);}echo "&nbsp;</td>";} ?>
					</tr>
					<tr>
						<td><b>Excess Export Net Value ($)</b></td>
						<? foreach($month_arr as $month){echo "<td align='right' style='color:green;'>" ;if(!empty($overNetValArr[$month])){echo number_format($overNetValArr[$month],2);}echo "&nbsp;</td>";} ?>
					</tr>
					<tr>
						<td><b>Short Export Qty (pcs)</b></td>
						<? foreach($month_arr as $month){echo "<td align='right' style='color:red;'>" ;if(!empty($shortQtyArr[$month])){echo number_format($shortQtyArr[$month]);}echo "&nbsp;</td>";} ?>
					</tr>
					<tr>
						<td><b>Short Export Gross Value ($)</b></td>
						<? foreach($month_arr as $month){echo "<td align='right' style='color:red;'>" ;if(!empty($shortValArr[$month])){echo number_format($shortValArr[$month],2);}echo "&nbsp;</td>";} ?>
					</tr>
					<tr>
						<td><b>Short Export Net Value ($)</b></td>
						<? foreach($month_arr as $month){echo "<td align='right' style='color:red;'>" ;if(!empty($shortNetValArr[$month])){echo number_format($shortNetValArr[$month],2);}echo "&nbsp;</td>";} ?>
					</tr>
				</tbody>
            </table>
			<?
			foreach($company_arr as $k=>$v)
			{
				?>
				<table class="orderExportData canvas_info" border="1" rules="all">
					<tbody>
						<tr bgcolor="#D5D9D8">
							<td width="100" align="center"><b><? echo $company_array[$v];?></b></td>
							<? foreach($month_arr as $month){echo "<td width='100' align='center'><b>" . $month . "</b></td>";} ?>
						</tr>
						<tr>
							<td><b>Projected Order Qty(pcs)</b></td>
							<? foreach($month_arr as $month){echo "<td align='right'>" ;if(!empty($orderQtyComProjected[$v][$month])){echo  number_format($orderQtyComProjected[$v][$month]);}echo "&nbsp;</td>";} ?>
						</tr>
						<tr>
							<td><b>Projected Order Gross Value ($)</b></td>
							<? foreach($month_arr as $month){echo "<td align='right'>" ;if(!empty($orderValComProjected[$v][$month])){echo  number_format($orderValComProjected[$v][$month],2);}echo "&nbsp;</td>";} ?>
						</tr>
						<tr>
							<td style="border-bottom: 1px solid yellow;"><b>Projected Order Net Value ($)</b></td>
							<? foreach($month_arr as $month){echo "<td align='right' style='border-bottom: 1px solid yellow;'>" ;if(!empty($orderNetValComProjected[$v][$month])){echo  number_format($orderNetValComProjected[$v][$month],2);}echo "&nbsp;</td>";} ?>
						</tr>
						<tr>
							<td style="border-top: 1px solid yellow;"><b>Confirmed Order Qty(pcs)</b></td>
							<? foreach($month_arr as $month){echo "<td align='right' style='border-top: 1px solid yellow;'>" ;if(!empty($orderQtyComConfirmed[$v][$month])){echo  number_format($orderQtyComConfirmed[$v][$month]);}echo "&nbsp;</td>";} ?>
						</tr>
						<tr>
							<td><b>Confirmed Order Gross Value ($)</b></td>
							<? foreach($month_arr as $month){echo "<td align='right'>" ;if(!empty($orderValComConfirmed[$v][$month])){echo  number_format($orderValComConfirmed[$v][$month],2);}echo "&nbsp;</td>";} ?>
						</tr>
						<tr>
							<td><b>Confirmed Order Net Value ($)</b></td>
							<? foreach($month_arr as $month){echo "<td align='right'>" ;if(!empty($orderNetValComConfirmed[$v][$month])){echo  number_format($orderNetValComConfirmed[$v][$month],2);}echo "&nbsp;</td>";} ?>
						</tr>
						<tr>
							<td><b>Export Qty(pcs)</b></td>
							<? foreach($month_arr as $month){echo "<td align='right' style='color:green;'>";if(!empty($exFactoryQtyComArr[$v][$month])){echo number_format($exFactoryQtyComArr[$v][$month]) ;}echo "&nbsp;</td>";} ?>
						</tr>
						<tr>
							<td><b>Export Gross Value ($)</b></td>
							<? foreach($month_arr as $month){echo "<td align='right' style='color:green;'>" ;if(!empty($exFactoryValComArr[$v][$month])){echo  number_format($exFactoryValComArr[$v][$month],2)  ;}echo "&nbsp;</td>";} ?>
						</tr>
						<tr>
							<td><b>Export Net Value ($)</b></td>
							<? foreach($month_arr as $month){echo "<td align='right' style='color:green;'>" ;if(!empty($exFactoryNetValComArr[$v][$month])){echo  number_format($exFactoryNetValComArr[$v][$month],2)  ;}echo "&nbsp;</td>";} ?>
						</tr>
						<tr>
							<td><b>Excess Export Qty (pcs)</b></td>
							<? foreach($month_arr as $month){echo "<td align='right' style='color:green;'>" ;if(!empty($overQty[$v][$month])){echo number_format($overQty[$v][$month]);}echo "&nbsp;</td>";} ?>
						</tr>
						<tr>
							<td><b>Excess Export Gross Value ($)</b></td>
							<? foreach($month_arr as $month){echo "<td align='right' style='color:green;'>" ;if(!empty($overVal[$v][$month])){echo number_format($overVal[$v][$month],2);}echo "&nbsp;</td>";} ?>
						</tr>
						<tr>
							<td><b>Excess Export Net Value ($)</b></td>
							<? foreach($month_arr as $month){echo "<td align='right' style='color:green;'>" ;if(!empty($overNetVal[$v][$month])){echo number_format($overNetVal[$v][$month],2);}echo "&nbsp;</td>";} ?>
						</tr>
						<tr>
							<td><b>Short Export Qty (pcs)</b></td>
							<? foreach($month_arr as $month){echo "<td align='right' style='color:red;'>" ;if(!empty($shortQty[$v][$month])){echo number_format($shortQty[$v][$month]);}echo "&nbsp;</td>";} ?>
						</tr>
						<tr>
							<td><b>Short Export Gross Value ($)</b></td>
							<? foreach($month_arr as $month){echo "<td align='right' style='color:red;'>" ;if(!empty($shortVal[$v][$month])){echo number_format($shortVal[$v][$month],2);}echo "&nbsp;</td>";} ?>
						</tr>
						<tr>
							<td><b>Short Export Net Value ($)</b></td>
							<? foreach($month_arr as $month){echo "<td align='right' style='color:red;'>" ;if(!empty($shortNetVal[$v][$month])){echo number_format($shortNetVal[$v][$month],2);}echo "&nbsp;</td>";} ?>
						</tr>
					</tbody>
				</table>
				<?
			}
			?>
        </div>
        <a href="home_graph/dashborad/monthly_order_export_with_value.php?print=1" target="_blank"><img src="img/print.jpg" height="20" alt="" /></a>
        <? 
		if($print==1)
		{
			?>
			<div style="width:35%; padding:10px; float:left;">
			
			<table class="orderExportData"  border="1" rules="all" width="80%" align="center">
				<tr>
					<td width="100">Generate Date:</td>
					<td> <? echo date("Y-m-d",time());?></td>
					<td align="right" style="border:none" rowspan="2"></td>
				</tr>
				<tr>
					<td>Generate Time:</td>
					<td> <? echo date("h:i:s a",time());?></td>
				</tr>
			</table>    
	
			<b>&nbsp; &nbsp; TOTAL EXPORT</b>
			<table class="orderExportData"  border="1" rules="all" width="80%" align="center">
				<tr>
					<th width="100">Month</th>
					<th>Total Projected Order</th>
					<th>Total Confirmed Order</th>
					<th>Total Export</th>
				</tr>
				<tr>
					<td align="center"> <? echo date("M-Y",time());?></td>
					<td align="right"><? echo array_sum($buyerWisePoQtyArrProjected);?></td>
					<td align="right"><? echo array_sum($buyerWisePoQtyArrConfirmed);?></td>
					<td align="right"><? echo array_sum($buyerWiseExportQtyArr);?></td>
				</tr>
			</table>    
	
			<b>&nbsp; &nbsp; EXPORT PROGRESS OF ORDERS</b>
			<table id="buyerOrderExportData" class="orderExportData" border="1" rules="all" width="75%" align="center">
				<tr bgcolor="#D5D9D8">
					<th>Buyer</th>
					<th width="80">Projected Order Qty</th>
					<th width="80">Confirmed Order Qty</th>
					<th width="80">Export Qty</th>
					<th width="70">Export Progress (%)</th>
				</tr>
				
				<? foreach($buyerIdArr as $buyer_id)
				{	
					?>
					<tr>
						<td><? echo $buyer_arr[$buyer_id];?></td>
						<td align="right"><? echo $buyerWisePoQtyArrProjected[$buyer_id];?></td>
						<td align="right"><? echo $buyerWisePoQtyArrConfirmed[$buyer_id];?></td>
						<td align="right"><? echo $buyerWiseExportQtyArr[$buyer_id];?></td>
						<td align="right"><? echo number_format(($buyerWiseExportQtyArr[$buyer_id]*100)/$buyerWisePoQtyArrProjected[$buyer_id],2);?></td>
					</tr>
					<? 
				} ?>
				
			</table>        
			</div>
			<?
		} 
		?>
        
	</div>
    
    <script>		
		var line_bar_data= {
			type: 'bar',
			data: {
			  labels:<? echo $monthArray;?>,
			  datasets: [
				{
				  label: "PROJECTED ORDER QTY(PCS)",
				  type: "bar",
				  backgroundColor: "#ffb6c1",
				  data:<? echo $order_qty_arr_project;?>,
				  fill: false
				},
				{
				  label: "CONFIRMED ORDER QTY(PCS)",
				  type: "bar",
				  backgroundColor: "#2E75B6",
				  data:<? echo $order_qty_arr_confirm;?>,
				  fill: false
				},
				{
				  label: "EXPORT QTY(PCS)",
				  type: "bar",
				  backgroundColor: "#9DDE58",
				  data:<? echo $exFactory_qty_array;?>,
				  fill: false
				},
				{
				  label: "SHORT EXPORT QTY(PCS)",
				  type: "bar",
				  backgroundColor: "#ff0000",
				  data:<? echo $short_qty_Array;?>,
				  fill: false
				}
			  ]
			},
			options: {
			  title: {
				display: true,
				text: 'MONTHLY PROJECTED AND CONFIRMED ORDER AND EXPORT WITH VALUE'
			  },
			  legend: { display: true }
			}
		}  
		new Chart(document.getElementById("canvas"),line_bar_data);
    </script>
 
<style>
.orderExportData{margin:5px 0 20px 10px;}
.orderExportData td , .orderExportData th {border:1px solid #000;}
#canvas{width:100%!important;}
.canvas_info{width:95%!important; font-size:10px!important;}


#buyerOrderExportData{width:95%!important; font-size:12px!important;}

td{word-break:break-word;}

</style> 
 
<?
function add_month($orgDate,$mon){
  $cd = strtotime($orgDate);
  $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,1,date('Y',$cd)));
  return $retDAY;
}
?>

