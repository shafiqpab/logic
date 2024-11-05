<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../../includes/common.php');
require_once('../../../../includes/class.reports.php');
require_once('../../../../includes/class.yarns.php');

$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond  group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

if ($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_agent_name", 80, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21))  group by a.id,a.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" );  
	exit(); 	 
} 

if ($action=="load_drop_down_team_member")
{
	echo create_drop_down( "cbo_team_member", 65, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	exit();
}
if ($action=="factory_merchant_dropdown")
{
	echo create_drop_down( "cbo_factory_marchant", 80, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name 	=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name 	=str_replace("'","",$cbo_buyer_name);
	$cbo_agent_name 	=str_replace("'","",$cbo_agent_name);
	$cbo_year 	  		=str_replace("'","",$cbo_year);
	$txt_job_no 		=str_replace("'","",$txt_job_no);
	$txt_style_ref 		=str_replace("'","",$txt_style_ref);
	$txt_ord_no 		=str_replace("'","",$txt_ord_no);
	$cbo_team_name 		=str_replace("'","",$cbo_team_name);
	$cbo_team_member 	=str_replace("'","",$cbo_team_member);
	$cbo_factory_marchant =str_replace("'","",$cbo_factory_marchant);
	$cbo_date_category 	=str_replace("'","",$cbo_date_category);
	$txt_date_from 		=str_replace("'","",$txt_date_from);
	$txt_date_to 		=str_replace("'","",$txt_date_to);
		
	if($cbo_company_name==0) $comp_sql_cond=""; else $comp_sql_cond=" and a.company_name='$cbo_company_name'";
	if($cbo_buyer_name==0) $buyer_sql_cond=""; else $buyer_sql_cond="  and a.buyer_name='$cbo_buyer_name'";
	if($cbo_agent_name==0) $agent_sql_cond=""; else $agent_sql_cond="  and a.agent_name='$cbo_agent_name'";
	if(trim($txt_job_no)!="") $job_sql_cond=" and a.job_no like '%$txt_job_no%'";
	if(trim($txt_style_ref)!="") $style_sql_cond=" and a.style_ref_no like '%$txt_style_ref%'";
	if(trim($txt_ord_no)!="") $po_sql_con=" and c.po_number like '%$txt_ord_no%'";
	
	if($cbo_team_name==0) $team_sql_cond=""; else $team_sql_cond="  and a.team_leader='$cbo_team_name'";
	if($cbo_team_member==0) $t_member_sql_cond=""; else $buyer_sql_cond="  and a.dealing_marchant='$cbo_team_member'";
	if($cbo_factory_marchant==0) $fcty_mrcnt_sql_cond=""; else $fcty_mrcnt_sql_cond="  and a.factory_marchant='$cbo_factory_marchant'";

	$cbo_year=str_replace("'","",$cbo_year);
	if($db_type==0)
	{
		if($cbo_year!=0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if($cbo_year!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";
	}
	
	if($db_type==0)
	{
		$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
		$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	else if($db_type==2) 
	{
		$date_from=change_date_format($txt_date_from,'','',1);
		$date_to=change_date_format($txt_date_to,'','',1);
	}
	else 
	{
		$date_from="";
		$date_to="";
	}
	//$date_cond_ship=""; 
	$date_cond_cut_off="";	
	if($cbo_date_category==1)
	{
		if($date_from!="" && $date_to!="") $date_cond_cut_off=" and d.cutup_date between '".$date_from."' and '".$date_to."'"; else $date_cond_cut_off="";
	}
	/*else if($cbo_date_category==2)
	{
		if($date_from!="" && $date_to!="") $date_cond_ship=" and c.shipment_date between '".$date_from."' and '".$date_to."'"; else $date_cond_ship="";
	}*/

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer ",'id','buyer_name');

	if($db_type==0) $year_field="YEAR(a.insert_date) as job_year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as job_year";
	else $year_field="";//defined Later
	
// po_break_down_id check	
/* echo $sql_querry="select a.company_name,a.buyer_name,a.agent_name,a.job_no,$year_field,a.style_ref_no,a.order_uom,a.set_smv,a.total_set_qnty,c.po_number,sum(c.po_quantity) as po_quantity,c.po_received_date,c.unit_price,c.is_confirmed,c.details_remarks,d.cutup_date as ac_ship_date 
from wo_po_details_master a,wo_po_details_mas_set_details b,wo_po_break_down c,wo_po_color_size_breakdown d,wo_po_sample_approval_info e 
where a.requ_no='D n C-RQSN-17-00021' and a.job_no=b.job_no and b.job_no=c.job_no_mst and c.job_no_mst=d.job_no_mst and c.id=d.po_break_down_id and c.id=e.po_break_down_id and d.job_no_mst=e.job_no_mst $comp_sql_cond $buyer_sql_cond $agent_sql_cond $job_sql_cond $style_sql_cond $po_sql_con $team_sql_cond $t_member_sql_cond $fcty_mrcnt_sql_cond  $year_cond $date_cond_cut_off 
group by  a.company_name,a.buyer_name,a.agent_name,a.job_no,a.insert_date,a.style_ref_no,a.order_uom,a.set_smv,a.total_set_qnty,c.po_number,c.po_received_date,c.unit_price,c.is_confirmed,c.details_remarks,d.cutup_date "; */
// withoud po_break_down_id check
 $sql_querry="select distinct a.company_name,a.buyer_name,a.agent_name,a.job_no,$year_field,a.style_ref_no,a.order_uom,a.set_smv,a.total_set_qnty,c.po_number,c.po_quantity as po_quantity,c.po_received_date,c.unit_price,c.is_confirmed,c.details_remarks,d.cutup_date as ac_ship_date 
from wo_po_details_master a,wo_po_details_mas_set_details b,wo_po_break_down c,wo_po_color_size_breakdown d,wo_po_sample_approval_info e 
where a.job_no=b.job_no and b.job_no=c.job_no_mst and c.job_no_mst=d.job_no_mst and d.job_no_mst=e.job_no_mst 
and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 
$comp_sql_cond $buyer_sql_cond $agent_sql_cond $job_sql_cond $style_sql_cond $po_sql_con $team_sql_cond $t_member_sql_cond $fcty_mrcnt_sql_cond  $year_cond $date_cond_cut_off 
"; 
//group by  a.company_name,a.buyer_name,a.agent_name,a.job_no,a.insert_date,a.style_ref_no,a.order_uom,a.set_smv,a.total_set_qnty,c.po_number,c.po_received_date,c.unit_price,c.is_confirmed,c.details_remarks,d.cutup_date 
//die;


  $sql="SELECT distinct a.id as job_id, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity , a.order_uom, a.total_set_qnty,d.item_number_id as item_number_id, c.id as po_id,c.file_no,c.grouping, c.po_number, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price,d.id, d.cutup, d.country_id,d.country_ship_date,d.cutup_date, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty
	FROM wo_po_details_master a

	LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
	AND c.is_deleted =0
	AND c.status_active =1
	LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
	AND c.id = d.po_break_down_id
	AND d.is_deleted =0
	AND d.status_active =1
	WHERE 
	d.id!=0 and
	a.is_deleted =0
	AND a.status_active =1
	$comp_sql_cond $buyer_sql_cond $agent_sql_cond $job_sql_cond $style_sql_cond $po_sql_con $team_sql_cond $t_member_sql_cond $fcty_mrcnt_sql_cond  $year_cond $date_cond_cut_off
	"; 
	//$company_id $buyer_id $job_id $po_id $pub_shipment_date $job_num_mst $year_cond $file_cond  $ref_cond order by a.job_no,c.id,d.cutup_date,d.cutup,d.id

$job_qnty_color_size_table_array_2=array();
 $sql=sql_select( $sql);
foreach( $sql as $row)
{
	$job_qnty_color_size_table_array_2[$row[csf('job_no')]][$row[csf('cutup_date')]]['order_qty']+=$row[csf('order_quantity')];
}
//print_r( $job_qnty_color_size_table_array_2);
	$result=sql_select($sql_querry);
	ob_start();
?>
<script>
var bottom_total_po_qty_set_to_pcs=document.getElementById('bottom_total_po_qty_set_to_pcs').innerHTML;
var bottom_total_po_qty_set_to_pcs=number_format(bottom_total_po_qty_set_to_pcs,0);

var bottom_total_amount_usd=document.getElementById('bottom_total_amount_usd').innerHTML;
var bottom_total_amount_usd=number_format(bottom_total_amount_usd,0);

document.getElementById('up_total_po_qty_set_to_pcs').innerHTML=bottom_total_po_qty_set_to_pcs + ' Pcs';
document.getElementById('up_total_amount_usd').innerHTML=' USD  ' + bottom_total_amount_usd ;
</script>
	<fieldset>
		<table width="2100">
			<tr class="form_caption">
				<td colspan="7" align="center">
                <? echo $report_title ?> 
	                <br/>
					<? if(($txt_date_from && $txt_date_to)!=""){ echo $txt_date_from . ' To ' . $txt_date_to; } ?>
                </td>
			</tr>
		</table >
       <table style="margin-top:10px" id="table_header_1" class="rpt_table" width="2100" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
				<tr>
                    <th width="100" style="text-align:right;" colspan="5" id="up_total_po_qty_set_to_pcs"></th>	
                    <th width="100"></th>
					<th width="730" style="text-align:left;" id="up_total_amount_usd"> </th> 
                </tr>
        	</thead>
        </table>
		<table style="margin-top:10px" id="table_header_1" class="rpt_table" width="2100" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
            	
				<tr>
					<th width="35">SL</th>
					<th width="140">Company</th>
					<th width="130">Buyer</th>
					<th width="100">Order No</th>
					<th width="100">Job No</th>
                    <th width="100">Job Year</th>
					<th width="150">Style Ref</th>
					<th width="100">PO Rcvd Date</th>
                    <th width="100">Actual Shipment Date</th>
					<th width="100">Order Qty</th>	
					<th width="100">UOM</th>
                    <th width="100">Order Qty (Pcs)</th>	
					<th width="100">Rate</th>
					<th width="100">Amount (USD)</th>  
					<th width="100">Order Status</th> 
					<th width="100">SMV</th> 
					<th width="100">Total SMV</th> 
					<th>Remarks</th> 
				</tr>
			</thead>
		</table>
		<div style="width:2110px; max-height:400px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="2100" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
	            <?
					$total_po_qty_pcs_original="";
					$total_po_qty_set_to_pcs="";
					$total_amount_usd="";
					$grand_total_smv="";
					$i=1; //$b=0; $al=0;	$check_order=array();$check_allocate_qty=array();
				   foreach($result as $row)
					{
						if ($i%2==0)  
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";	
						
				?>
                	  	<tr bgcolor="<? echo  $bgcolor; ?>">
                        	<td width="35"><? echo $i; ?></td>
	                    	<td width="140"><? echo $company_arr[$row[csf('company_name')]]; ?></td>
	                        <td width="130"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                            <td width="100"><? echo $row[csf('po_number')]; ?></td>
	                        <td width="100"><? echo $row[csf('job_no')]; ?></td>
                            <td width="100" align="center"><? echo $row[csf('job_year')]; ?></td>
                            <td width="150"><? echo $row[csf('style_ref_no')]; ?></td>
	                        <td width="100" align="center"><? echo  change_date_format($row[csf('po_received_date')]); ?></td>
                            <td width="100" align="center"><? echo  change_date_format($row[csf('ac_ship_date')]); ?></td>
                            <td width="100" align="right">
							<? 
								if($row[csf('order_uom')]==58)  
								{
									 echo $job_qty= $job_qnty_color_size_table_array_2[$row[csf('job_no')]][$row[csf('ac_ship_date')]]['order_qty']/$row[csf('total_set_qnty')];
									 $total_po_qty_set_to_pcs+=$job_qty;
								}
								else
								{
									 echo $job_qty= $job_qnty_color_size_table_array_2[$row[csf('job_no')]][$row[csf('ac_ship_date')]]['order_qty'];
									 $total_po_qty_pcs_original+=$job_qty;
								}

							?>
                            </td>
	                        <td width="100"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                            <td width="100" align="right">
							<? 
								if($row[csf('order_uom')]==1)  
								{
									 echo $job_qty_pcs= $job_qnty_color_size_table_array_2[$row[csf('job_no')]][$row[csf('ac_ship_date')]]['order_qty']*$row[csf('total_set_qnty')];
									 $total_po_qty_set_to_pcs+=$job_qty_pcs;
								}
								else
								{
								 	 echo $job_qty_pcs= $job_qnty_color_size_table_array_2[$row[csf('job_no')]][$row[csf('ac_ship_date')]]['order_qty']/$row[csf('total_set_qnty')];
									 $total_po_qty_set_to_pcs+=$job_qty_pcs;
								}
								
								

							?>
                            </td>
	                        <td width="100" align="right"><? echo $row[csf('unit_price')]; ?></td>
                            <td width="100" align="right"><? echo  $job_qty* $row[csf('unit_price')]; ?></td>
	                        <td width="100" align="center"><? echo $order_status[$row[csf('is_confirmed')]]; ?></td>
                            <td width="100" align="center"><? echo $row[csf('set_smv')]; ?></td>
	                        <td width="100" align="right"><? echo $job_qty*$row[csf('set_smv')]; ?></td>
	                        <td ><? echo $row[csf('details_remarks')]; ?></td>
                        </tr>
                <?
						$total_amount_usd+=$job_qty* $row[csf('unit_price')];
						$grand_total_smv+=$job_qty*$row[csf('set_smv')];
						$i++;  
	                }
				?>
                  		<tr>
							<td colspan="11" align="right"><b> Grand Total=</b></td>
                            <td align="right" id="bottom_total_po_qty_set_to_pcs"><b><? echo number_format($total_po_qty_set_to_pcs,2); ?></b></td>
                            <td align="right"><b> </b></td>
                            <td align="right" id="bottom_total_amount_usd"><b> <? echo number_format($total_amount_usd,2); ?></b></td>
                            <td align="right"><b> </b></td>
                            <td align="right"><b> </b></td>
                            <td align="right"><b> <? echo number_format($grand_total_smv,2); ?></b></td>
                            <td align="right"><b> </b></td>
                            
						</tr>
			</table>
		</div>
			
	</fieldset> 
<?
	//unset($sql_querry) ;
	
	foreach (glob(".../../../../ext_resource/tmp_report/$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$html=ob_get_contents();
	$name=time();
	$filename="../../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	$filename="../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	echo "$total_data****$filename";
	exit();
}


?>