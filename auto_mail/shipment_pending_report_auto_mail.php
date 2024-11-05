<?php
/*-------------------------------------------- Comments
Purpose         : Shipment Pending Report Auto Mail
Functionality   :   
JS Functions    :
Created by      :   Al-Hassan
Creation date   :   16-10-2023
Updated by      :       
Update date     :          
QC Performed BY :       
QC Date         :   
Comments        : 
*/

date_default_timezone_set("Asia/Dhaka");
header('Content-type:text/html; charset=utf-8');
include('../includes/common.php');
require_once('../mailer/class.phpmailer.php');
include('setting/mail_setting.php');
session_start();

$company_arr = return_library_array( "select id,company_name from lib_company where STATUS_ACTIVE=1 and IS_DELETED=0",'id','company_name');
$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	
foreach($company_arr as $company_id => $company_name){
	
	$buyer_wise_arr = array();
    $buyer_wise_arr = array();
	
	$cbo_date_category=1;

	$current_month_end_date=date("Y-m-d",strtotime("-1 days"));
	if($db_type==0)
	{
		$date_start= change_date_format($current_month_end_date,"yyyy-mm-dd");
	}
	else
	{
		$date_start= change_date_format($current_month_end_date,"dd-mm-yyyy","-",1);
	}
	
	$str_cond="and a.pub_shipment_date<='$date_start' ";

	if($txt_from_date && $txt_to_date){
		$str_cond="and a.pub_shipment_date between '$txt_from_date' and  '$txt_to_date' ";
	}

	
	$sql_summary_ex_factory=return_library_array("SELECT po_break_down_id,sum(ex_factory_qnty) AS ex_factory_qnty from pro_ex_factory_mst  where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','ex_factory_qnty');
	
	
	$production_sql="select c.po_break_down_id as po_id,
		sum(CASE WHEN c.production_type =1 THEN c.production_quantity ELSE 0 END) AS cutting_qnty,
		sum(CASE WHEN c.production_type =5 THEN c.production_quantity ELSE 0 END) AS sewing_out_qnty,	
		sum(CASE WHEN c.production_type =8 THEN c.production_quantity ELSE 0 END) AS finish_qnty
		from  pro_garments_production_mst c ,wo_po_break_down a,wo_po_details_master b
		where c.po_break_down_id=a.id and a.is_confirmed=1 and a.shiping_status!=3  and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.production_type in(1,5,8) and b.job_no=a.job_no_mst and b.company_name =$company_id group by c.po_break_down_id order by c.po_break_down_id";
		//echo $production_sql;die;
		  
		$production_result=sql_select($production_sql);
		
		$tot_cutting_qnty=$tot_sewing_out_qnty=$tot_finish_qnty=$tot_prev_cut_qnty=$tot_prev_sew_qnty=$tot_prev_finish_qnty=$tot_current_sewing_qty=$tot_current_finish_qty=0;
		$tot_plan_cut_qnty=0;
		foreach($production_result as $row)
		{
			$prod_qty_arr[$row[csf('po_id')]]['cutting_qnty']=$row[csf('cutting_qnty')];
			$prod_qty_arr[$row[csf('po_id')]]['sewing_out_qnty']=$row[csf('sewing_out_qnty')];
			$prod_qty_arr[$row[csf('po_id')]]['finish_qnty']=$row[csf('finish_qnty')];
			
			if($extended_ship_date!='')
			{
				$pub_current_month=date("Y-m", strtotime($extended_ship_date));
			}
			else
			{
				$pub_current_month=date("Y-m", strtotime($pub_ship_date));
			}
		} 
		unset($production_result); 
		 
		$sql_orderlevel="SELECT b.team_leader,b.dealing_marchant,a.id, a.po_number, a.pub_shipment_date,a.extended_ship_date,a.sea_discount,a.air_discount,a.extend_ship_mode,a.po_total_price, b.order_uom, a.details_remarks, b.buyer_name, b.agent_name, b.company_name, b.style_ref_no, b.gmts_item_id, b.job_no_prefix_num, a.shiping_status, a.job_no_mst, a.grouping, a.po_quantity, b.total_set_qnty, a.plan_cut, (a.unit_price/b.total_set_qnty) as unit_price,b.set_smv from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name =$company_id  and a.shiping_status!=3  and a.is_confirmed=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $str_cond order by a.pub_shipment_date DESC";
		 
		$sql_order_level=sql_select($sql_orderlevel);
		$row_tot=count($sql_order_level);
		$tot_pending_po_qty_previ=$tot_prev_po_val=$tot_prev_PendingSAH=$tot_pre_plan_cut=$tot_current_pending_po_quantity=$tot_current_pending_po_val=$tot_current_PendingSAH=$tot_current_plan_cut=0;
		
		$all_po_id="";$curr_po_id="";
		$end_month=date("Y-m",strtotime("-1 days"));
		$current_month=date("Y-m", strtotime($end_month));
 
		
		foreach($sql_order_level as $row)
		{
			if($all_po_id=="") $all_po_id=$row[csf("id")]; else $all_po_id.=",".$row[csf("id")];
			$pub_shipment_date=date("Y-m-d",strtotime($row[csf('pub_shipment_date')]));
			$extended_ship_date=date("Y-m-d",strtotime($row[csf('extended_ship_date')]));
 
			if($cbo_date_category==2) // Exten Ship
			{
				if($extended_ship_date=='')
				{
					$extended_ship_date=$current_month_end_date;
				}
				$date_cond_dynamic = ($extended_ship_date<=$current_month_end_date);
			}
			else
			{
				$extended_ship_date=$pub_shipment_date;
				$date_cond_dynamic= ($pub_shipment_date!='');
			}
			if($date_cond_dynamic) //Check here
			{
				$month_date='';
				if($row[csf('extended_ship_date')]!='')
				{
					if($cbo_date_category==1)
					{
					 $month_date = date("Y-m",strtotime($row[csf('pub_shipment_date')]));
					 $pub_current_month = date("Y-m", strtotime($row[csf('pub_shipment_date')]));
					}
					else
					{
					 $month_date=date("Y-m",strtotime($row[csf('extended_ship_date')]));
					
					 $pub_current_month=date("Y-m", strtotime($row[csf('extended_ship_date')]));
					}
				}
				else
				{
					if($row[csf('extended_ship_date')]!='')
					{
						if($cbo_date_category==1)
						{
							$month_date=date("Y-m",strtotime($row[csf('pub_shipment_date')]));
							$pub_current_month=date("Y-m", strtotime($row[csf('pub_shipment_date')]));
						}
						else
						{
							$month_date=date("Y-m",strtotime($row[csf('extended_ship_date')]));
							$pub_current_month=date("Y-m", strtotime($row[csf('extended_ship_date')]));
						}
					}
					else
					{
						if($cbo_date_category==1)
						{
							$month_date=date("Y-m",strtotime($row[csf('pub_shipment_date')]));
							$pub_current_month=date("Y-m", strtotime($row[csf('pub_shipment_date')]));
						}
						else
						{
							$month_date=date("Y-m",strtotime($row[csf('pub_shipment_date')]));
							$pub_current_month=date("Y-m", strtotime($row[csf('pub_shipment_date')]));
						}
					}
				}
				if($row[csf('shiping_status')]==2)
					{
						$ex_fact_qty=$sql_summary_ex_factory[$row[csf('id')]];
					}
					else
					{
						$ex_fact_qty=0;
					}
					$plan_cut_qty=$row[csf('plan_cut')]*$row[csf('total_set_qnty')];
					if($current_month==$pub_current_month)//Current Month
					{
						$curr_po_quantity=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
						if(($curr_po_quantity-$ex_fact_qty)>0){
							$tot_current_pending_po_quantity+=$curr_po_quantity-$ex_fact_qty;
							$tot_pending_qty=$curr_po_quantity-$ex_fact_qty;
						//	echo $pub_current_month.'='.$current_month.'<br>';
							if($tot_pending_qty>0)
							{
							$tot_current_pending_po_val+=$tot_pending_qty*$row[csf('unit_price')];
							}
							$tot_current_plan_cut+=$row[csf('plan_cut')]*$row[csf('total_set_qnty')];
							$tot_current_PendingSAH+=(($curr_po_quantity-$ex_fact_qty)*$row[csf('set_smv')])/60;
							
							if($plan_cut_qty-$prod_qty_arr[$row[csf('id')]]['cutting_qnty']>0)
							{
								$tot_current_cut_qty+=$plan_cut_qty-$prod_qty_arr[$row[csf('id')]]['cutting_qnty'];
							}
							if($curr_po_quantity-$prod_qty_arr[$row[csf('id')]]['sewing_out_qnty']>0)
							{
								$tot_current_sewing_qty+=$curr_po_quantity-$prod_qty_arr[$row[csf('id')]]['sewing_out_qnty'];
							}
							if($curr_po_quantity-$prod_qty_arr[$row[csf('id')]]['finish_qnty']>0)
							{
								$tot_current_finish_qty+=$curr_po_quantity-$prod_qty_arr[$row[csf('id')]]['finish_qnty'];
							}
							 
							if($curr_po_id=="") $curr_po_id=$row[csf("id")]; else $curr_po_id.=",".$row[csf("id")];
						}
					}
					
					$prev_po_quantity=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
					$prev_plan_cut_qty=$row[csf('plan_cut')]*$row[csf('total_set_qnty')];
					$pending_po_quantity=$prev_po_quantity-$ex_fact_qty;
					if($prev_po_quantity-$prod_qty_arr[$row[csf('id')]]['finish_qnty']>0)
					{
						$tot_prev_finish_qnty+=$prev_po_quantity-$prod_qty_arr[$row[csf('id')]]['finish_qnty'];
					}
					if($prev_po_quantity-$prod_qty_arr[$row[csf('id')]]['sewing_out_qnty']>0)
					{
						$tot_prev_sew_qnty+=$prev_po_quantity-$prod_qty_arr[$row[csf('id')]]['sewing_out_qnty'];
					}
					
					if($prev_plan_cut_qty-$prod_qty_arr[$row[csf('id')]]['cutting_qnty']>0)
					{
						$tot_prev_cut_qnty+=$prev_plan_cut_qty-$prod_qty_arr[$row[csf('id')]]['cutting_qnty'];
					}
					
					if($pending_po_quantity>0)
					{
					$tot_prev_po_val+=$pending_po_quantity*$row[csf('unit_price')];
					}
					if(($prev_po_quantity-$ex_fact_qty)>0){
						$tot_prev_pending_po_qty_previ+=$prev_po_quantity-$ex_fact_qty;
					}
					if($pending_po_quantity>0)
					{
					$tot_prev_PendingSAH+=($pending_po_quantity*$row[csf('set_smv')])/60;
					}
					$pre_plan_cut_qty=$row[csf('plan_cut')]*$row[csf('total_set_qnty')];
					$tot_pre_plan_cut+=$pre_plan_cut_qty;
				if($pending_po_quantity>0)
				{
					// Buyer wise summary
					$buyer_wise_arr[$month_date][$row[csf('buyer_name')]]['pending_poqty']+=$pending_po_quantity;
					$buyer_wise_arr[$month_date][$row[csf('buyer_name')]]['total_set_qnty']=$row[csf('total_set_qnty')];
					$buyer_wise_arr[$month_date][$row[csf('buyer_name')]]['plan_cut']+=$row[csf('plan_cut')];
					$buyer_wise_arr[$month_date][$row[csf('buyer_name')]]['unit_price']=$row[csf('unit_price')];
					$buyer_wise_arr[$month_date][$row[csf('buyer_name')]]['po_value']+=$pending_po_quantity*$row[csf('unit_price')];
					$buyer_wise_arr[$month_date][$row[csf('buyer_name')]]['buyer_sah']+=($pending_po_quantity*$row[csf('set_smv')])/60;
					
				}
			} //Check End
		}
		$po_ids=count(array_unique(explode(",",$all_po_id)));
		$po_numIds=chop($all_po_id,','); $poIds_cond="";
		if($all_po_id!='' || $all_po_id!=0)
		{
			if($db_type==2 && $po_ids>1000)
			{
				$poIds_cond=" and (";
				$poIdsArr=array_chunk(explode(",",$po_numIds),990);
				foreach($poIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$poIds_cond.=" c.po_break_down_id  in($ids) or ";
				}
				$poIds_cond=chop($poIds_cond,'or ');
				$poIds_cond.=")";
			}
			else
			{
				$poIds_cond=" and  c.po_break_down_id  in($all_po_id)";
			}
		}
	ob_start();	
	?>
	<!--=============================================================Total Summary Start=============================================================================================-->
    <div style="width:1300px;">
		<table width="1300px" style=" float:left;"  cellspacing="0">
			<tr>
				<td colspan="7" align="center" ><font size="3"><strong><?php echo $company_arr[$company_id]; ?></strong></font></td>
			</tr>
			<tr class="form_caption">
				<td colspan="7" align="center"><font size="3"><strong>Total Pending Order Summary </strong></font></td>
			</tr>
		</table>
		<table border="1" style=" float:left;" rules="all" class="rpt_table" width="1200">
			<thead>
				<th width="30">SL</th>
				<th width="130">Month</th>
				<th width="130">Pending PO Qty.</th>
				<th width="140">Pending PO Value</th>
				<th width="135">Plan Cut Qty.</th>
				<th width="125">Cutting Pending</th>
				<th width="125">Sewing Pending</th>
				<th>Finishing Pending </th>
			</thead>
			<?
			$prev_PendingSAH=0;$prev_po_qnty=0; $prev_po_val=0; $prev_sew_qnty=0; $prev_cut_qnty=0; $prev_finish_qnty=0;
			$curr_month=date("F",strtotime($current_month_end_date)).", ".date("Y",strtotime($current_month_end_date));
			
			$summary_grand_total_po_qny=0;
			$summary_grand_total_lc_value=0;
			$summary_grand_total_cut_qny=0;
			$summary_grand_total_sewing_qny=0;
			$summary_grand_total_finish_qny=0;
			$bgcolor1='#E9F3FF';
			$bgcolor2='#FFFFFF';
			?>
			<tr bgcolor="<? echo $bgcolor1; ?>" onclick="change_color('tr1st_1','<? echo $bgcolor1; ?>')" id="tr1st_1">
				<td>1</td>
				<td>Previous To January</td>
				<td align="right"  title="Prev PO Qty=<? echo $tot_prev_pending_po_qty_previ;?> -Shipout Qty=<? echo $tot_current_pending_po_quantity;?>"><? 
				$tot_prev_pending_po_qty_previ=$tot_prev_pending_po_qty_previ-$tot_current_pending_po_quantity;
				echo number_format($tot_prev_pending_po_qty_previ,0); $summary_grand_total_po_qny+=$tot_prev_pending_po_qty_previ; ?></td>
				<td align="right" title="Prev PO Value=<? echo $tot_prev_po_val;?>-Shipout Value=<? echo $tot_current_pending_po_val;?>"><? 
				$tot_prev_po_val=$tot_prev_po_val-$tot_current_pending_po_val;$tot_prev_PendingSAH=$tot_prev_PendingSAH-$tot_current_PendingSAH;
				$tot_pre_plan_cut=$tot_pre_plan_cut-$tot_current_plan_cut;$tot_prev_cut_qnty=$tot_prev_cut_qnty-$tot_current_cut_qty;
				$tot_prev_sew_qnty=$tot_prev_sew_qnty-$tot_current_sewing_qty;$tot_prev_finish_qnty=$tot_prev_finish_qnty-$tot_current_finish_qty;
				
				echo number_format($tot_prev_po_val,2); $summary_grand_total_lc_value+=$tot_prev_po_val; ?></td>
				 
				<td align="right"> <? echo number_format($tot_pre_plan_cut,0); $summary_grand_total_plan_cut+=$tot_pre_plan_cut; ?></td>
				<td align="right" title="Prev Plan Cut -Cutting Prod. Qty"><? echo number_format($tot_prev_cut_qnty,0); $summary_grand_total_cut_qny+=$tot_prev_cut_qnty; ?></td>
				<td align="right"  title="Prev PO Qty -Cutting Prod. Qty"><? echo number_format($tot_prev_sew_qnty,0); $summary_grand_total_sewing_qny+=$tot_prev_sew_qnty; ?></td>
				<td align="right"  title="Prev PO Qty -Finish Prod. Qty"><? echo number_format($tot_prev_finish_qnty,0); $summary_grand_total_finish_qny+=$tot_prev_finish_qnty; ?></td>
			</tr>
			<tr bgcolor="<? echo $bgcolor2; ?>" onclick="change_color('tr1st_2','<? echo $bgcolor2; ?>')" id="tr1st_2">
				<td>2</td>
				<td> <? echo $curr_month; ?> </td>
				<td align="right"  title="Current PO Qty -Shipout Qty"><? echo number_format($tot_current_pending_po_quantity,0); $summary_grand_total_po_qny+=$tot_current_pending_po_quantity; ?></td>
				<td align="right" title="Current PO Value -Shipout Value"><? echo number_format($tot_current_pending_po_val,2); $summary_grand_total_lc_value+=$tot_current_pending_po_val; ?></td>
				 
				<td align="right"><? echo number_format($tot_current_plan_cut,0); $summary_grand_total_plan_cut+=$tot_current_plan_cut; ?></td>
				<td align="right" title="Plan Cut -Cutting Prod. Qty"><? echo number_format($tot_current_cut_qty,0); $summary_grand_total_cut_qny+=$tot_current_cut_qty; ?></td>
				<td align="right"  title="PO Qty -Sewing Prod. Qty"><? echo number_format($tot_current_sewing_qty,0); $summary_grand_total_sewing_qny+=$tot_current_sewing_qty; ?></td>
				<td align="right"  title="PO Qty -Finish Prod. Qty"><? echo number_format($tot_current_finish_qty,0); $summary_grand_total_finish_qny+=$tot_current_finish_qty; ?></td>
			</tr>
			<tfoot>
				<th colspan="2" align="right">Total</th>
				<th align="right"><? echo number_format($summary_grand_total_po_qny,0); ?></th>
				<th align="right"><? echo number_format($summary_grand_total_lc_value,2); ?></th>
				<th align="right"><? echo number_format($summary_grand_total_PendingSAH,2); ?></th>
				<th align="right"><? echo number_format($summary_grand_total_plan_cut,0); ?></th>
				 
				<th align="right"><? echo number_format($summary_grand_total_sewing_qny,0); ?></th>
				<th align="right"><? echo number_format($summary_grand_total_finish_qny,0); ?> </th>
			</tfoot>
		</table>
        <br/> 
		<table width="1300">
			<tr>
			<td valign="top">
			    <?
				foreach( $buyer_wise_arr as $month_key=>$row_month)
				{
					?>
					<div style="width:400px; float:left; margin:5px;">
					<table width="400px"  cellspacing="0"  class="display">
						<tr>
							<td colspan="4" align="center"><font size="3"><strong>Total Summary <? $month_name=date("F",strtotime($month_key)).", ".date("Y",strtotime($month_key)); echo $month_name; ?></strong></font></td>
						</tr>
					</table>
					<table width="400px" class="rpt_table" border="1" rules="all">
						<thead>
							<th width="30">SL</th>
							<th width="100">Buyer Name</th>
							<th width="90">Pending Qnty</th>
							<th width="90">Pending Value</th>
						</thead>
					<?
					$tot_buyer_po_qnty=$tot_buyer_po_val=$tot_buyer_SAH=0;
					$m=1;
					foreach( $row_month as $buyer_key=>$row)
					{
					if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$tot_buyer_order_quantity=$row['pending_poqty'];
					$buyer_order_val=$row['po_value'];
					$buyer_SAH=$row['buyer_sah'];
				?>
				
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr1st_<? echo $i; ?><? echo $d; ?>','<? echo $bgcolor; ?>')" id="tr1st_<? echo $i; ?><? echo $d; ?>">
						<td><? echo $m; ?></td>
						<td><p><? echo $buyer_arr[$buyer_key]; ?></p></td>
						<td align="right"><? echo number_format($tot_buyer_order_quantity,0); $tot_buyer_po_qnty+=$tot_buyer_order_quantity; ?></td>
						<td align="right"><? echo number_format($buyer_order_val,2); $tot_buyer_po_val+=$buyer_order_val; ?></td>
					</tr>
				<?	
					$m++;
					}
					?>
					<tfoot>
						<th colspan="2" align="right">Total</th>
						<th align="right"><? echo number_format($tot_buyer_po_qnty,0); ?></th>
						<th align="right"><? echo number_format($tot_buyer_po_val,2); ?></th>
					</tfoot>
				</table>
			</div>	
			<?
			}
		?>
		</td>
    	</tr>
    </table>
	</div>
    <br/>
    <?
    //ob_start();	
    ?>
    <div>
	<?php
    $message = ob_get_contents();
    ob_clean();
    $mail_item = 136;
    $sql = "SELECT a.BRAND_IDS,a.BUYER_IDS,c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id  and a.mail_item=b.MAIL_ITEM_MST and a.mail_item=$mail_item and b.mail_user_setup_id=c.id and a.company_id = $company_id and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
    //echo $sql;die; 
    $mail_sql=sql_select($sql);
    $receverMailArr=array();
    foreach($mail_sql as $row)
    {
        // $mailAdd="alhassan.cse@gmail.com";
        $receverMailArr[$row['EMAIL_ADDRESS']]=$row['EMAIL_ADDRESS'];		
    }

    $to=implode(',',$receverMailArr); 
 
    $subject="Shipment Pending Report";
    $header=mailHeader();
    if($_REQUEST['isview']==1){
        if($to){
            echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
        }else{
            echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
        }
        echo  $message;
    }
    else{
        if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
    }
}
?>