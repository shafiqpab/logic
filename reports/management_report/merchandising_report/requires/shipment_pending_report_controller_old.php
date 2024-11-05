<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Knit Garments Order Entry

Functionality	:	
JS Functions	:
Created by		:	Monzu 
Creation date 	: 	13-10-2012
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

include('../../../../includes/common.php');

session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

$buyer_short_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
$company_team_name_arr=return_library_array( "select id,team_name from lib_marketing_team",'id','team_name');
$company_team_member_name_arr=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info",'id','team_member_name');
$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library",'master_tble_id','image_location');
$cm_for_shipment_schedule_arr=return_library_array( "select job_no,cm_for_sipment_sche from  wo_pre_cost_dtls",'job_no','cm_for_sipment_sche');

if($action=="shipment_pending_report")
{
	
	$data=explode("_",$data);
	if($data[0]==0) $company_name="%%"; else $company_name=$data[0];
	if(trim($data[1])!="") $job_number="%".$data[1]."%"; else  $job_number="%%";
	if(trim($data[2])!="") $txt_style_number="%".$data[2]."%"; else $txt_style_number="%%";
	if(trim($data[3])!="") $txt_po_number="%".trim($data[3])."%"; else $txt_po_number="%%";
	if(trim($data[4])!="") $txt_order_qnty="%".$data[4]."%"; else $txt_order_qnty="%%";
	if(trim($data[5])!="") $buyer_name="%".$data[5]."%"; else $buyer_name="%%";
	
	if(trim($data[5])!="") 
	{
		$buyer_cond="and b.buyer_name in(select id from lib_buyer where buyer_name like '$buyer_name' and status_active=1 and is_deleted=0)";	
	}
	else
	{
		$buyer_cond="";
	}
	
	
	
	$start_date = return_field_value("min(a.pub_shipment_date)" ,"wo_po_break_down a, wo_po_details_master b"," b.job_no=a.job_no_mst and b.company_name like '$company_name' and b.job_no like '$job_number' and b.style_ref_no like '$txt_style_number' and a.po_number like '$txt_po_number' and a.po_quantity like '$txt_order_qnty' and a.shiping_status!=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_cond $item_cond");

	$end_date=date("Y-m-01"); 

	$start_month=date("Y-m",strtotime($start_date));
	//$end_month=date("Y-m");
	$end_month=date("Y-m",strtotime("-1 days"));
	$end_date2=date("Y-m-d",strtotime("-1 days"));
	
	if($db_type==2)
	{
		$start_date=change_date_format($start_date,'yyyy-mm-dd','-',1);
		$end_date2=change_date_format($end_date2,'yyyy-mm-dd','-',1);
	}
	
	//$diff = abs(strtotime($start_month) - strtotime($end_month));
	//$total_months = floor($diff / (30*60*60*24));
	$total_months=datediff("m",$start_month,$end_month);
	
	$last_month=date("Y-m", strtotime("+1 Months", strtotime($end_month)));
	
	$previous_month_year=date("Y-m",strtotime("-1 Months", strtotime($end_month)));
	$array_previous_month_year=explode("-",$previous_month_year);
	$number_of_dayes_prev_moth=cal_days_in_month(CAL_GREGORIAN, $array_previous_month_year[1], $array_previous_month_year[0]);
	$previous_month_end_date=$previous_month_year."-".$number_of_dayes_prev_moth;
	
	if($db_type==2)
	{
		$previous_month_end_date=change_date_format($previous_month_end_date,'yyyy-mm-dd','-',1);
	}
	
	$month_identify=explode("-",$end_date2);
	$month=$month_identify[1];
	$year=$month_identify[0];
	$num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
	//$current_month_end_date=$year."-".$month."-".$num_days;
	//$current_month_end_date=$year."-".$month;
	$current_month_end_date=date("Y-m-d",strtotime("-1 days"));
	if($db_type==2)
	{
		$current_month_end_date=change_date_format($current_month_end_date,'yyyy-mm-dd','-',1);
	}
	
	if($end_date!="")
	{
		$str_cond="and a.pub_shipment_date between '$start_date' and '$previous_month_end_date'";
		
		$end_date3=date("Y-m-01",strtotime($end_date2));
		if($db_type==2)
		{
			$end_date3=change_date_format($end_date3,'yyyy-mm-dd','-',1);
		}
		$str_cond_curr="and a.pub_shipment_date between '$end_date3' and '$current_month_end_date'";
	}
	else
	{
		$str_cond="";
		$str_cond_curr="";
	}
	
?>
<!--=============================================================Total Summary Start=============================================================================================-->
<div style="width:790px">
    <table width="100%"  cellspacing="0">
        <tr>
            <td colspan="7" align="center" ><font size="3"><strong><?php echo $company_details[$company_name];  ?> </strong> </font></td>
        </tr>
        <tr class="form_caption">
            <td colspan="7" align="center"><font size="3"><strong>Total Pending Order Summary </strong></font></td>
        </tr>
    </table>
	<table border="1" rules="all" class="rpt_table" width="1000">
        <thead>
            <th width="30">SL</th>
            <th width="220"> Month </th>
            <th width="100">Pending PO Qnty. </th>
            <th width="100">Pending PO Value</th>
            <th width="120">Cutting Pending </th>
            <th width="120">Sewing Pending</th>
            <th width="125">Finishing Pending </th>
        </thead>

<?
				$sql_summary_ex_factory=return_library_array("SELECT po_break_down_id,sum(ex_factory_qnty) AS ex_factory_qnty from pro_ex_factory_mst  where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','ex_factory_qnty');
				
				$cutting_qnty=return_library_array("SELECT po_break_down_id,sum(production_quantity) AS production_quantity  from pro_garments_production_mst where production_type ='1' and is_deleted=0 and status_active=1 group by po_break_down_id",'po_break_down_id','production_quantity');
				
				$sewingin_qnty=return_library_array("SELECT po_break_down_id,sum(production_quantity) AS production_quantity  from pro_garments_production_mst where production_type ='5' and is_deleted=0 and status_active=1 group by po_break_down_id",'po_break_down_id','production_quantity');
				
				$finish_qnty=return_library_array("SELECT po_break_down_id,sum(production_quantity) AS production_quantity  from pro_garments_production_mst where production_type ='6' and is_deleted=0 and status_active=1 group by po_break_down_id",'po_break_down_id','production_quantity');
				


		$prev_po_qnty=0; $prev_po_val=0; $prev_sew_qnty=0; $prev_cut_qnty=0; $prev_finish_qnty=0;
		$sql_summary=sql_select( "SELECT a.id, b.order_uom, a.shiping_status, a.job_no_mst, (a.po_quantity*b.total_set_qnty) as po_quantity , a.unit_price from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name like '$company_name'  and b.job_no like '$job_number' and  b.style_ref_no like '$txt_style_number' and a.shiping_status!=3 and a.po_number like '$txt_po_number' and a.po_quantity like '$txt_order_qnty' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_cond $item_cond $str_cond ");
		foreach( $sql_summary as $row_summary)
		{
			if($row_summary[csf('shiping_status')]==2)
			{
				$order_ex_quantity=0;
				/*$sql_summary_ex_factory="SELECT sum(ex_factory_qnty) AS 'ex_factory_qnty' from pro_ex_factory_mst where po_break_down_id='".$row_summary[csf('id')]."' and status_active=1 and is_deleted=0";
				$res_summary_ex_factory=sql_select($sql_summary_ex_factory);
				list($row_summary_ex_factory)=$res_summary_ex_factory;
				$order_ex_quantity=$row_summary_ex_factory[csf('ex_factory_qnty')];*/
				$order_ex_quantity=$sql_summary_ex_factory[$row_summary[csf('id')]];
			}
			else
			{
				$order_ex_quantity=0;
			}
			
			/*$sql_summary_prodution="SELECT  sum(CASE WHEN production_type ='1' THEN production_quantity END) AS 'cutting_qnty', sum(CASE WHEN production_type ='5' THEN production_quantity END) AS 'sewingin_qnty',sum(CASE WHEN production_type ='6' THEN production_quantity END) AS 'finish_qnty' from pro_garments_production_mst where po_break_down_id='".$row_summary[csf('id')]."' and is_deleted=0 and status_active=1";
			$res_summary_prodution=sql_select($sql_summary_prodution);
			list($row_production)=$res_summary_prodution;  
			$order_quantity=$row_summary[csf('po_quantity')]-$order_ex_quantity;
			$prev_po_qnty+=$order_quantity;
			$prev_po_val+=$order_quantity*$row_summary[csf('unit_price')];
			
			$prev_cut_qnty+=$row_summary[csf('po_quantity')]-$row_production[csf('cutting_qnty')];
			$prev_sew_qnty+=$row_summary[csf('po_quantity')]-$row_production[csf('sewingin_qnty')];
			$prev_finish_qnty+=$row_summary[csf('po_quantity')]-$row_production[csf('finish_qnty')];*/
			$order_quantity=$row_summary[csf('po_quantity')]-$order_ex_quantity;
			$prev_po_qnty+=$order_quantity;
			$prev_po_val+=$order_quantity*$row_summary[csf('unit_price')];
			
			$prev_cut_qnty+=$row_summary[csf('po_quantity')]-$cutting_qnty[$row_summary[csf('id')]];
			$prev_sew_qnty+=$row_summary[csf('po_quantity')]-$sewingin_qnty[$row_summary[csf('id')]];
			$prev_finish_qnty+=$row_summary[csf('po_quantity')]-$finish_qnty[$row_summary[csf('id')]];
		}
		
		$curr_po_qnty=0; $curr_po_val=0; $curr_cut_qnty=0; $curr_sew_qnty=0; $curr_finish_qnty=0;
		$sql_summary2=sql_select("SELECT a.id, b.order_uom,  a.shiping_status, a.job_no_mst, (a.po_quantity*b.total_set_qnty) as po_quantity, a.unit_price from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name like '$company_name'  and b.job_no like '$job_number' and  b.style_ref_no like '$txt_style_number' and a.shiping_status!=3 and a.po_number like '$txt_po_number' and a.po_quantity like '$txt_order_qnty' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_cond $item_cond $str_cond_curr");
		
		foreach($sql_summary2 as $row_summary2)
		{
			if($row_summary2[csf('shiping_status')]==2)
			{
				$order_ex_quantity2=0;
				/*$sql_summary_ex_factory2="SELECT sum(ex_factory_qnty) AS 'ex_factory_qnty' from pro_ex_factory_mst where po_break_down_id='".$row_summary2[csf('id')]."' and status_active=1 and is_deleted=0 group by po_break_down_id";
				$res_summary_ex_factory2=sql_select($sql_summary_ex_factory2);
				list($row_summary_ex_factory2)=$res_summary_ex_factory2;
				$order_ex_quantity2=$row_summary_ex_factory2[csf('ex_factory_qnty')];*/
			    $order_ex_quantity2=$sql_summary_ex_factory[$row_summary2[csf('id')]];
			}
			else
			{
				$order_ex_quantity2=0;
			}
			/*$sql_summary_prodution2="SELECT  sum(CASE WHEN production_type ='1' THEN production_quantity END) AS 'cutting_qnty', sum(CASE WHEN production_type ='5' THEN production_quantity END) AS 'sewingin_qnty',sum(CASE WHEN production_type ='6' THEN production_quantity END) AS 'finish_qnty' from pro_garments_production_mst where po_break_down_id='".$row_summary2[csf('id')]."' and is_deleted=0 and status_active=1 group by po_break_down_id";
			$res_summary_prodution2=sql_select($sql_summary_prodution2);
			list($row_production2)=$res_summary_prodution2;  
			$order_quantity2=$row_summary2[csf('po_quantity')]-$order_ex_quantity2;
			$curr_po_qnty+=$order_quantity2;
			$curr_po_val+=$order_quantity2*$row_summary2[csf('unit_price')];
			
			$curr_cut_qnty+=$row_summary2[csf('po_quantity')]-$row_production2[csf('cutting_qnty')];
			$curr_sew_qnty+=$row_summary2[csf('po_quantity')]-$row_production2[csf('sewingin_qnty')];
			$curr_finish_qnty+=$row_summary2[csf('po_quantity')]-$row_production2[csf('finish_qnty')];*/
			$order_quantity2=$row_summary2[csf('po_quantity')]-$order_ex_quantity2;
			$curr_po_qnty+=$order_quantity2;
			$curr_po_val+=$order_quantity2*$row_summary2[csf('unit_price')];
			
			$curr_cut_qnty+=$row_summary2[csf('po_quantity')]-$cutting_qnty[$row_summary2[csf('id')]];
			$curr_sew_qnty+=$row_summary2[csf('po_quantity')]-$sewingin_qnty[$row_summary2[csf('id')]];
			$curr_finish_qnty+=$row_summary2[csf('po_quantity')]-$finish_qnty[$row_summary2[csf('id')]];
		}
		
		$curr_month=date("F",strtotime($end_month)).", ".date("Y",strtotime($end_month));
		
		$summary_grand_total_po_qny=0;
		$summary_grand_total_lc_value=0;
		$summary_grand_total_cut_qny=0;
		$summary_grand_total_sewing_qny=0;
		$summary_grand_total_finish_qny=0;

?>
        <tr bgcolor="<? echo "#E9F3FF"; ?>">
            <td width="30">1</td>
            <td width="220" >Previous To Current Month</td>
            <td width="100" align="right"><? echo $prev_po_qnty; $summary_grand_total_po_qny+=$prev_po_qnty; ?></td>
            <td width="100" align="right"><? echo number_format($prev_po_val,2); $summary_grand_total_lc_value+=$prev_po_val; ?></td>
            <td width="120" align="right"><? echo $prev_cut_qnty; $summary_grand_total_cut_qny+=$prev_cut_qnty; ?></td>
            <td width="120" align="right"><? echo $prev_sew_qnty; $summary_grand_total_sewing_qny+=$prev_sew_qnty; ?></td>
            <td width="125" align="right"><? echo $prev_finish_qnty; $summary_grand_total_finish_qny+=$prev_finish_qnty; ?></td>
        </tr>
        <tr bgcolor="<? echo "#FFFFFF"; ?>">
            <td width="30">2</td>
            <td width="220" > <? echo $curr_month; ?> </td>
           <td width="100" align="right"><? echo $curr_po_qnty; $summary_grand_total_po_qny+=$curr_po_qnty; ?></td>
            <td width="100" align="right"><? echo number_format($curr_po_val,2); $summary_grand_total_lc_value+=$curr_po_val; ?></td>
            <td width="120" align="right"><? echo $curr_cut_qnty; $summary_grand_total_cut_qny+=$curr_cut_qnty; ?></td>
            <td width="120" align="right"><? echo $curr_sew_qnty; $summary_grand_total_sewing_qny+=$curr_sew_qnty; ?></td>
            <td width="125" align="right"><? echo $curr_finish_qnty; $summary_grand_total_finish_qny+=$curr_finish_qnty; ?></td>
        </tr>
        <tfoot>
            <th colspan="2" align="right">Total</th>
            <th width="100px" align="right"><? echo $summary_grand_total_po_qny; ?></th>
            <th width="100px" align="right"><? echo number_format($summary_grand_total_lc_value,2); ?></th>
            <th width="120px" align="right"><? echo $summary_grand_total_cut_qny; ?></th>
            <th width="120px" align="right"><? echo $summary_grand_total_sewing_qny; ?></th>
            <th width="125px" align="right"><? echo $summary_grand_total_finish_qny; ?> </th>
        </tfoot>
    </table> 
</div>

<br/>      
<!--=============================================================Total Summary End=============================================================================================-->


<!--=============================================================Total Current Month Strat=======================================================================================-->
<fieldset style="width:1250px">
<legend>Month Wise Total Summary</legend>
    <table width="1200">
        <tr>
        <?	$s=0;
			for($i=0;$i<=$total_months;$i++)
			{
				$last_month=date("Y-m", strtotime("-1 Months", strtotime($last_month)));
				$month_query=$last_month."-"."%%"; 
				if($i==0)
				{
					$month_query_start_date=$last_month."-01"; 
					$month_query_end_date=date("d",strtotime("-1 days"));
					$month_query_end_date=$last_month."-".$month_query_end_date; 
					if($db_type==2)
					{
						$month_query_start_date=change_date_format($month_query_start_date,'yyyy-mm-dd','-',1);
						$month_query_end_date=change_date_format($month_query_end_date,'yyyy-mm-dd','-',1);
					}
					$month_query_cond="and a.pub_shipment_date between '$month_query_start_date' and '$month_query_end_date'";
				}
				else
				{
					$month_query_cond="and a.pub_shipment_date like '$month_query'";
					if($db_type==2)
					{
						$month_query_cond="and to_char(a.pub_shipment_date,'YYYY-MM-DD') like '$month_query'";
					}
				}
				  $sql_month="SELECT distinct b.buyer_name from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name like '$company_name'  and b.job_no like '$job_number' and  b.style_ref_no like '$txt_style_number' and a.shiping_status!=3 and a.po_number like '$txt_po_number' and a.po_quantity like '$txt_order_qnty' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $month_query_cond $buyer_cond $item_cond";
				 
				$res_month=sql_select($sql_month);
				$tot_rows=count($res_month);
				if($tot_rows>0) 
				{
					
					if($s%3==0) $tr="</tr><tr>"; else $tr="";
					echo $tr;
				?>
					<td valign="top">
						<div style="width:400px">
						<table width="100%"  cellspacing="0"  class="display">
							<tr>
								<td colspan="4" align="center">
								<font size="3"><strong>Total Summary 
								<? $month_name=date("F",strtotime($last_month)).", ".date("Y",strtotime($last_month));
									echo $month_name;
								?>
								</strong></font>
								</td>
							</tr>
						</table>
						<table width="100%" class="rpt_table" border="1" rules="all">
							<thead>
								<th width="30">SL</th>
								<th width="150">Buyer Name</th>
								<th width="100">Po Qnty</th>
								<th width="100">PO Value</th>
							</thead>
                            <?
							$d=1; $tot_po_qnty=0; $tot_po_val=0; 
							foreach( $res_month as $row_month)
							{
								if ($d%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
									
								$sql="SELECT a.id, b.order_uom, a.shiping_status, a.job_no_mst,(a.po_quantity*b.total_set_qnty) as po_quantity, a.unit_price from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name like '$company_name'  and b.job_no like '$job_number' and  b.style_ref_no like '$txt_style_number' and a.shiping_status!=3 and a.po_number like '$txt_po_number' and a.po_quantity like '$txt_order_qnty' $month_query_cond and b.buyer_name='".$row_month[csf('buyer_name')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $item_cond";
								$result=sql_select($sql); $buyer_order_quantity=0; $buyer_order_val=0;$tot_buyer_order_quantity=0;
								foreach( $result as $row)
								{
									if($row[csf('shiping_status')]==2)
									{
										/*$buyer_ex_quantity=0;
										$sql_buyer_ex_factory="SELECT sum(ex_factory_qnty) AS 'ex_factory_qnty' from pro_ex_factory_mst where po_break_down_id='".$row[csf('id')]."' and status_active=1 and is_deleted=0 group by po_break_down_id";
										$res_buyer_ex_factory=sql_select($sql_buyer_ex_factory);
										list($row_buyer_ex_factory)=$res_buyer_ex_factory;
										$buyer_ex_quantity=$row_buyer_ex_factory[csf('ex_factory_qnty')];*/
										$buyer_ex_quantity=0;
										$buyer_ex_quantity=$sql_summary_ex_factory[$row[csf('id')]];
									}
									else
									{
										$buyer_ex_quantity=0;
									}
									$buyer_order_quantity=$row[csf('po_quantity')]-$buyer_ex_quantity;
									$tot_buyer_order_quantity+=$buyer_order_quantity;
									$buyer_order_val+=$buyer_order_quantity*$row[csf('unit_price')];
								}
							?>
								<tr bgcolor="<? echo $bgcolor; ?>">
                                	<td><? echo $d; ?></td>
                                    <td><div style="word-wrap:break-word; width:150px"><? echo $buyer_short_name_arr[$row_month[csf('buyer_name')]]; ?></div></td>
                                    <td align="right"><? echo $tot_buyer_order_quantity; $tot_po_qnty+=$tot_buyer_order_quantity; ?></td>
                                    <td align="right">eee<? echo number_format($buyer_order_val,2); $tot_po_val+=$buyer_order_val; ?></td>
                                </tr>
							<?
                        $d++;}
							?>
                            <tfoot>
                            	<th colspan="2" align="right">Total</th>
                                <th align="right"><? echo $tot_po_qnty; ?></th>
                                <th align="right"><? echo number_format($tot_po_val,2); ?></th>
                            </tfoot>
						</table>
						</div>  
					</td> 
				 <?
				$s++;}
			}		 
		?>
    	</tr>
    </table>
    </fieldset>
    <br/>
    <?
    ob_start();	
    ?>
    <div id="scroll_body" align="center" style="height:auto; width:auto; margin:0 auto; padding:0;">
    <fieldset>
    <legend> Details Report</legend>
    <!--<div style="width:1670px;">-->
        <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="100%">
        	<thead>
            	<tr>
                    <th width="40">SL</th>
                    <th width="70" >Job No</th>
                    <th width="80" >Buyer Name</th>
                    <th width="110" >Po Number</th>
                    <th width="120" >Style Name</th>
                    <th width="140" >Item Name</th>
                    <th width="100" > Po Qnty.</th>
                    <th width="90">Ship Date</th>
                    <th width="60">Delay</th>
                    <th width="90">Cut Qnty</th>
                    <th width="80">Cut Wastage</th>
                    <th width="90">Sewing Qnty </th>
                    <th width="90">Finish Qnty</th>
                    <th width="90">Finish Pending</th>
                    <th width="90">Ship Qnty</th>
                    <th width="100">Pending PO Qnty.</th>
                    <th>Remarks</th>
                </tr>
               <tr>
                	<th width="40">&nbsp;</th>
                	<th width="70"><input type="text" value="<? echo str_replace("%","",$job_number); ?>" onKeyUp="show_inner_filter(event);" name="txt_job_number" id="txt_job_number" class="text_boxes" style="width:50px" /></th>
                    <th width="80" ><input type="text" name="buyer_name"  onkeyup="show_inner_filter(event);" value="<? echo str_replace("%","",$buyer_name); ?>" id="buyer_name" class="text_boxes" style="width:60px" /></th>
                    <th width="110" ><input type="text" name="txt_po_number"  onkeyup="show_inner_filter(event);" value="<? echo str_replace("%","",$txt_po_number); ?>" id="txt_po_number" class="text_boxes" style="width:80px" /></th>
                    <th width="120" ><input type="text" name="txt_style_number"  onkeyup="show_inner_filter(event);" value="<? echo str_replace("%","",$txt_style_number); ?>" id="txt_style_number" class="text_boxes" style="width:80px" /></th>
                    <th width="100" ><input type="text" name="txt_order_qnty"  onkeyup="show_inner_filter(event);" value="<? echo str_replace("%","",$txt_order_qnty); ?>" id="txt_order_qnty" class="text_boxes" style="width:80px" /></th>
                    <th width="100" ><input type="text" name="txt_order_qnty"  onkeyup="show_inner_filter(event);" value="<? echo str_replace("%","",$txt_order_qnty); ?>" id="txt_order_qnty" class="text_boxes" style="width:80px" /></th>
                    <th colspan="10">&nbsp;</th>
                </tr>
        	</thead>
		</table>
<!--        </div>
-->     <div style="width:1670px; max-height:410px; overflow-y: scroll;" id="scroll_body2">
        <table align="center" cellspacing="0" cellpadding="0"  width="1670px"  border="1" rules="all" class="rpt_table" id="tbl_ship_pending" >
			<?
            if ($start_date!="")
            {
                $str_cond3="and a.pub_shipment_date between '$start_date' and '$current_month_end_date' ";
            }
            else
            {
                $str_cond3="";
            }
            
            $ii=1; $k=1; $total_po_qnty=0; $total_cut_qnty=0; $total_sew_qnty=0; $total_finish_qnty=0; $total_ship_qnty=0; $total_balance_qnty=0;
            
            $month_array=array();
            $sql_order_level=sql_select( "SELECT a.id, a.po_number, a.pub_shipment_date, b.order_uom, a.details_remarks, b.buyer_name, b.company_name, b.style_ref_no, b.gmts_item_id, b.job_no_prefix_num, a.shiping_status, a.job_no_mst, (a.po_quantity*b.total_set_qnty) as po_quantity, a.unit_price from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name like '$company_name' and b.job_no like '$job_number' and  b.style_ref_no like '$txt_style_number' and a.shiping_status!=3 and a.po_number like '$txt_po_number' and a.po_quantity like '$txt_order_qnty' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_cond $item_cond $str_cond3 order by a.pub_shipment_date DESC");
            $row_tot=count($sql_order_level);
            foreach( $sql_order_level as $row_order_level)
            {
                if ($ii%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";
                
                if($row_order_level[csf('shiping_status')]==2)
                {
                    /*$company_sql222= sql_select("SELECT  sum(ex_factory_qnty) AS 'ex_factory_qnty' from pro_ex_factory_mst where  po_break_down_id='".$row_order_level[csf('id')]."' and status_active=1 and is_deleted=0");
                    list($row_company_sql22)= $company_sql222;
                    $ex_factory_qnty=$row_company_sql22[csf('ex_factory_qnty')];*/
					$ex_factory_qnty=$sql_summary_ex_factory[$row_order_level[csf('id')]];
                }
                else
                {
                    $ex_factory_qnty=0;
                } 
                    
                $po_quantity=$row_order_level[csf('po_quantity')];
                $month=date("Y-m",strtotime($row_order_level[csf('pub_shipment_date')]));
                if(!in_array($month, $month_array))
                {
                    if ($k!=1)
                    {
                    ?>
                        <tr bgcolor="#CCCCCC">
                            <td colspan="6" align="right"><b>Monthly Total</b></td>
                            <td align="right"><? echo  number_format($monthly_total_po_qnty,0);?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td align="right"><? echo number_format($monthly_total_cut_qnty,0); ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><? echo number_format($monthly_total_sew_qnty,0); ?></td>
                            <td align="right"><? echo number_format($monthly_total_finish_qnty,0); ?></td>
                            <td align="right"><? echo number_format($monthly_total_po_qnty-$monthly_total_finish_qnty,0); ?></td>
                            <td align="right"><? echo number_format($monthly_total_ship_qnty,0); ?></td>
                            <td align="right"><? echo number_format($monthly_total_po_qnty - $monthly_total_ship_qnty,0); ?></td>
                            <td>&nbsp;</td>
                        </tr>
                    <?
                        $monthly_total_po_qnty = 0;
                        $monthly_total_cut_qnty = 0;
                        $monthly_total_sew_qnty = 0;
                        $monthly_total_finish_qnty = 0;
                        $monthly_total_ship_qnty = 0;
                    }
                    $k++;
                    ?>
                    <tr bgcolor="#EFEFEF">
                        <td colspan="17"><b><?php echo date("F",strtotime($row_order_level[csf('pub_shipment_date')])).", ".date("Y",strtotime($row_order_level[csf('pub_shipment_date')]));?></b></td>
                    </tr>
                <?
                    $month_array[]=$month;
                }
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $ii;?>','<? echo $bgcolor;?>')" id="tr_<? echo $ii;?>">
                    <td width="40"><? echo $ii; ?></td>
                    <td width="70" align="center"><? echo $row_order_level[csf('job_no_prefix_num')]; ?></td>
                    <td width="80"><div style="word-wrap:break-word; width:80px"><? echo $buyer_short_name_arr[$row_order_level[csf('buyer_name')]]; ?></div></td>
                    <td width="110"><div style="word-wrap:break-word; width:110px"><a href="##" onClick="show_cost_sheet_popup('<? echo $row_order_level[csf('job_no_mst')];?>', '<? echo $row_order_level[csf('id')]; ?>')"><? echo $row_order_level[csf('po_number')]; ?></a></div></td>
                    <td width="120"><div style="word-wrap:break-word; width:120px"><? echo $row_order_level[csf('style_ref_no')]; ?></div></td>
                    <td width="140">
                        <div style="word-wrap:break-word; width:140px">
                            <?
                                /*if($row_order_level[item_name]!=0)
                                {
                                    echo $garments_name_details[$row_order_level[item_name]];
                                }
                                else
                                {
                                    echo $garments_name_details[$row_order_level[wo_po_item_id]];
                                }*/
                            ?>
                        </div>
                    </td>
                    <td align="right" width="100"><? echo number_format($row_order_level[csf('po_quantity')],0); ?></td>
                    <td width="90" align="center"><? echo change_date_format($row_order_level[csf('pub_shipment_date')],'dd-mm-yyyy','-'); ?></td>
                    <?
                        /*$date=date("Y-m-d",time());
                        $days_remian=daysdiff($date,$row_order_level[pub_shipment_date]);
                        if($days_remian<0) $color="#FF0000"; else $color=""; */
                    ?>
                    <td width="60" bgcolor="<? //echo $color; ?>" ><? //echo $days_remian; ?> </td>
                    <?
                    /*$company_sql= sql_select("SELECT sum(CASE WHEN production_type ='1' THEN production_quantity END) AS 'cutting_qnty',sum(CASE WHEN production_type ='5' THEN production_quantity END) AS'sewingin_qnty',sum(CASE WHEN production_type ='6' THEN production_quantity END) AS 'finish_qnty' from pro_garments_production_mst where  po_break_down_id='".$row_order_level[csf('id')]."'");
                    list($row_company_sql)=$company_sql;*/
                    ?>
                    <td align="right" width="90"><? echo number_format($cutting_qnty[$row_order_level[csf('id')]],0);  ?></td>
                    <?
					
                    $standard_excess_cut=sql_select("select excess_percent from variable_prod_excess_slab where $po_quantity between slab_rang_start and slab_rang_end and company_name='".$row_order_level[csf('company_name')]."' ");
                    list($row11)=$standard_excess_cut;
                    $excess_percent= $row11[('excess_percent')];
                    
                    $actual_excess_cut=(($row_company_sql[csf('cutting_qnty')]-$po_quantity)/$po_quantity)*100;
                    $exceed_cut=$actual_excess_cut - $excess_percent;
					
                    if($actual_excess_cut > $excess_percent && $excess_percent!='' )
                    {
                        $bg_color="red";
                    }
                    else
                    {
                        $bg_color="green";	
                    }

                    $actual_excess_cut=round($actual_excess_cut,2);
                    $actual_excess_cut2=$row_company_sql[csf('cutting_qnty')]-$po_quantity;
                    if($actual_excess_cut2==0 || $actual_excess_cut2<0)
                    {
                    ?>
                        <td align="left" width="80" title="Cutting Qnty Not Exceed Order Qnty" bgcolor="<? echo $bg_color ?>">
                    <?	
                        echo "N/A";	
                    ?>
                        </td>
                    <?
                    }
                    else
                    {
                    ?>
                        <td align="right" width="80" bgcolor="<? echo $bg_color ?>">
                    <?
                        echo "$actual_excess_cut %";
                    ?>
                        </td>
                    <?
                    }
                    ?>
                    <td align="right" width="90"><? echo number_format($sewingin_qnty[$row_order_level[csf('id')]],0); ?></td>
                    <td align="right" width="90"><? echo number_format($finish_qnty[$row_order_level[csf('id')]],0); ?></td>
                    <td align="right" width="90"><? echo number_format($po_quantity-$finish_qnty[$row_order_level[csf('id')]],0); ?></td>
                    <td align="right" width="90"><? echo number_format($ex_factory_qnty,0); ?></td>
                    <td align="right" width="100"><? echo number_format(($po_quantity - $ex_factory_qnty),0);?> </td>
                    <td><div style="word-wrap:break-word; width:120px"><? echo $row_order_level[csf('remarks')]; ?></div></td>
               </tr>
        <?
            $monthly_total_po_qnty+=$po_quantity;
            $monthly_total_cut_qnty+=$cutting_qnty[$row_order_level[csf('id')]];
            $monthly_total_sew_qnty+=$sewingin_qnty[$row_order_level[csf('id')]];
            $monthly_total_finish_qnty+=$finish_qnty[$row_order_level[csf('id')]];
            $monthly_total_ship_qnty+=$ex_factory_qnty;
            
            $total_po_qnty+=$po_quantity;
            $total_cut_qnty+=$cutting_qnty[$row_order_level[csf('id')]];
            $total_sew_qnty+= $cutting_qnty[$row_order_level[csf('if')]];	
            $total_finish_qnty+=$finish_qnty[$row_order_level[csf('id')]];
            $total_ship_qnty+=$ex_factory_qnty;
            
            $ii++;
        }
        if($row_tot>0)
        {
        ?>
            <tr bgcolor="#CCCCCC">
                <td colspan="6" align="right"><b>Monthly Total</b></td>
                <td align="right"><? echo  number_format($monthly_total_po_qnty,0);?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><? echo number_format($monthly_total_cut_qnty,0); ?></td>
                <td>&nbsp;</td>
                <td align="right"><? echo number_format($monthly_total_sew_qnty,0); ?></td>
                <td align="right"><? echo number_format($monthly_total_finish_qnty,0); ?></td>
                <td align="right"><? echo number_format($monthly_total_po_qnty-$monthly_total_finish_qnty,0); ?></td>
                <td align="right"><? echo number_format($monthly_total_ship_qnty,0); ?></td>
                <td align="right"><? echo number_format($monthly_total_po_qnty - $monthly_total_ship_qnty,0); ?></td>
                <td>&nbsp;</td>
            </tr>
        <?	
        }
        ?>
             <tr>
                <th colspan="6" align="right">Grand Total</th>
                <th align="right"><? echo  number_format($total_po_qnty,0);?></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th align="right"><? echo number_format($total_cut_qnty,0); ?></th>
                <th>&nbsp;</th>
                <th align="right"><? echo number_format($total_sew_qnty,0); ?></th>
                <th align="right"><? echo number_format($total_finish_qnty,0); ?></th>
                <th align="right"><? echo number_format($total_po_qnty-$total_finish_qnty,0); ?></th>
                <th align="right"><? echo number_format($total_ship_qnty,0); ?></th>
                <th align="right"><? echo number_format($total_po_qnty - $total_ship_qnty,0); ?></th>
                <th>&nbsp;</th>
            </tr>
        </table> 
    </div>
    </fieldset> 
</div>   
<?
	$html = ob_get_contents();
	ob_clean();
	 
	foreach (glob($_SESSION['logic_erp']['user_id']."*") as $filename) {
	@unlink($filename);
	}
	$name=time();
	$filename=$_SESSION['logic_erp']['user_id']."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,$html);
	echo "$html****$filename";
		
}  // end if($type=="sewing_production_summary")
exit();	
?>