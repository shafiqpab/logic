<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//--------------------------------------------------------------------------------------------------------------------
$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$user_name_library=return_library_array( "select id, user_name from  user_passwd", "id", "user_name"  );
$team_name_library=return_library_array( "select id,team_name from lib_marketing_team", "id", "team_name"  );
$team_member_name_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );   	 
	exit();
}
if ($action=="load_drop_down_team_member")
{
if($data!=0)
	{
        echo create_drop_down( "cbo_team_member", 150, "select id,team_member_name 	 from lib_mkt_team_member_info  where team_id='$data' and status_active=1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Select Team Member-", $selected, "" ); 
	}
 else
   {
		 echo create_drop_down( "cbo_team_member", 150, $blank_array,"", 1, "-Select Team Member- ", $selected, "" );
   }
}
$tmplte=explode("**",$data);
if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;


if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name=str_replace("'","",$cbo_company_name);
	//$serch_by=str_replace("'","",$cbo_search_by);
	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	
	
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_no=trim($txt_job_no);
	if($txt_job_no !="" || $txt_job_no !=0)
	{
		$year = substr(str_replace("'","",$cbo_year_selection), -2); 
		$job_no=$company_library[$company_name]."-".$year."-".str_pad($txt_job_no, 5, 0, STR_PAD_LEFT);
		$jobcond="and a.job_no='".$job_no."'";
	}
	else
	{
		$jobcond="";	
	}
	
	
	$date_cond='';
	if(str_replace("'","",$cbo_category_by)==1)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			$date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
	}
	if(str_replace("'","",$cbo_category_by)==2)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			$date_cond="and b.po_received_date between '$start_date' and '$end_date'";
		}
	}
	
	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond="";
	if(str_replace("'","",$txt_order_no)!="")
	{
		$ordercond=" and b.po_number like '%".str_replace("'","",$txt_order_no)."%'"; 
	}
	else
	{
		$ordercond="";
	}
	
	$team_cond="";
	if(str_replace("'","",$cbo_team_name)!=0) $team_cond=" and a.team_leader=".str_replace("'","",$cbo_team_name)."  ";
	if(str_replace("'","",$cbo_team_member)!=0) $team_cond.=" and a.dealing_marchant=".str_replace("'","",$cbo_team_member)."  ";
	if($template==1)
	{
		//$job_array=array();
	$po_wise_buyer=array();
	$buyer_wise_data=array();
	$po_id_arr=array();
	$report_data_arr=array();
	$sql_data=sql_select("select a.job_no,a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no,a.team_leader,a.dealing_marchant, a.gmts_item_id,a.job_quantity, b.id,b.is_confirmed,b.po_number,b.po_received_date,b.pub_shipment_date,b.shipment_date,b.factory_received_date,b.po_quantity,b.unit_price,b.excess_cut,b.plan_cut,b.status_active,b.projected_po_id,b.packing,b.details_remarks,b.file_no,b.updated_by,b.update_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond");
	foreach( $sql_data as $row_data)
	{
	$po_id_arr[$row_data[csf('id')]]=$row_data[csf('id')];
	$po_wise_buyer[$row_data[csf('id')]]=$row_data[csf('buyer_name')];
	$buyer_wise_data[$row_data[csf('buyer_name')]]['po_quantity']+=$row_data[csf('po_quantity')];
	$buyer_wise_data[$row_data[csf('buyer_name')]]['po_price']+=$row_data[csf('po_quantity')]*$row_data[csf('unit_price')];
	}
	
	if(count($po_id_arr)>0)
	{
	   $po_id=array_chunk($po_id_arr,999, true);
	   $po_cond_in="";
	   $ji=0;
	   foreach($po_id as $key=> $value)
	   {
		   if($ji==0)
		   {
				$po_cond_in=" po_id in(".implode(",",$value).")"; 
				
		   }
		   else
		   {
				$po_cond_in.=" or po_id in(".implode(",",$value).")";
		   }
		   $ji++;
	   }
	}// end if(count($po_id_arr)>0)
	
$array_for_compare=array();	
$log_array=array();
$original_array=array();
$sql_log=sql_select( "select id,po_id,order_status,po_no,po_received_date,shipment_date,org_ship_date,fac_receive_date,previous_po_qty,avg_price,excess_cut_parcent,plan_cut,status,projected_po,packing,remarks,file_no,update_date,update_by from wo_po_update_log where $po_cond_in order by id DESC");
foreach($sql_log as $row_log)
{
	$log_array[$row_log[csf('po_id')]][$row_log[csf('id')]]['order_status']=$row_log[csf('order_status')];
	$log_array[$row_log[csf('po_id')]][$row_log[csf('id')]]['po_no']=$row_log[csf('po_no')];
	$log_array[$row_log[csf('po_id')]][$row_log[csf('id')]]['po_received_date']=$row_log[csf('po_received_date')];
	$log_array[$row_log[csf('po_id')]][$row_log[csf('id')]]['shipment_date']=$row_log[csf('shipment_date')];
	
	$log_array[$row_log[csf('po_id')]][$row_log[csf('id')]]['org_ship_date']=$row_log[csf('org_ship_date')];
	$log_array[$row_log[csf('po_id')]][$row_log[csf('id')]]['fac_receive_date']=$row_log[csf('fac_receive_date')];
	$log_array[$row_log[csf('po_id')]][$row_log[csf('id')]]['previous_po_qty']=$row_log[csf('previous_po_qty')];
	$log_array[$row_log[csf('po_id')]][$row_log[csf('id')]]['avg_price']=$row_log[csf('avg_price')];
	$log_array[$row_log[csf('po_id')]][$row_log[csf('id')]]['excess_cut_parcent']=$row_log[csf('excess_cut_parcent')];
	
	$log_array[$row_log[csf('po_id')]][$row_log[csf('id')]]['plan_cut']=$row_log[csf('plan_cut')];
	$log_array[$row_log[csf('po_id')]][$row_log[csf('id')]]['status']=$row_log[csf('status')];
	$log_array[$row_log[csf('po_id')]][$row_log[csf('id')]]['projected_po']=$row_log[csf('projected_po')];
	$log_array[$row_log[csf('po_id')]][$row_log[csf('id')]]['packing']=$row_log[csf('packing')];
	$log_array[$row_log[csf('po_id')]][$row_log[csf('id')]]['remarks']=$row_log[csf('remarks')];
	
	$log_array[$row_log[csf('po_id')]][$row_log[csf('id')]]['file_no']=$row_log[csf('file_no')];
	$log_array[$row_log[csf('po_id')]][$row_log[csf('id')]]['update_date']=$row_log[csf('update_date')];
	$log_array[$row_log[csf('po_id')]][$row_log[csf('id')]]['update_by']=$row_log[csf('update_by')];
	
	//original=======================
	$original_array[$row_log[csf('po_id')]]['previous_po_qty']=$row_log[csf('previous_po_qty')];
	$original_array[$row_log[csf('po_id')]]['avg_price']=$row_log[csf('avg_price')];
	$original_array[$row_log[csf('po_id')]]['previous_po_amount']=$row_log[csf('previous_po_qty')]*$row_log[csf('avg_price')];
	//===================================
	$array_for_compare[$row_log[csf('po_id')]]['previous_po_qty'][]=$row_log[csf('previous_po_qty')];
	$array_for_compare[$row_log[csf('po_id')]]['avg_price'][]=$row_log[csf('avg_price')];
	
}
$original_array_buyer_wise=array();
foreach($original_array as $key=>$value){
	$original_array_buyer_wise[$po_wise_buyer[$key]]['previous_po_qty']+=$value['previous_po_qty'];
	$original_array_buyer_wise[$po_wise_buyer[$key]]['previous_po_amount']+=$value['previous_po_amount'];
}
//print_r($dddd);
		ob_start();
	?>
		<div style="width:2305px">
		<fieldset style="width:100%;">	
        <table class="rpt_table" width="840" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
                <tr>
					<th width="30" rowspan="2">SL</th>
                    <th width="55" rowspan="2">Buyer</th>
					<th colspan="3">Sum of Original</th>
                    
                    <th colspan="3">Sum of Current</th>
                    
                    <th colspan="3">Net Changed By</th>
                    
                 </tr>
                 <tr>
					
					<th width="80">Qty</th>
                    <th width="50">Rate</th>
					<th width="100">Amount</th>
                    <th width="80">Qty</th>
                    <th width="50">Rate</th>
					<th width="100">Amount</th>
                    <th width="80">Qty</th>
                    <th width="50">Rate</th>
					<th width="100">Amount</th>
                 </tr>
				</thead>
			</table>
            <table class="rpt_table" width="840" cellpadding="0" cellspacing="0" border="1" rules="all">
            <?
			$bi=1;
			$original_po_qty_tot=0;
			$original_po_amount_tot=0;
			$current_po_qty_tot=0;
			$current_po_amount_tot=0;
			//$change_po_qty_tot=0;
			//$change_po_amount_tot=0;
			foreach($buyer_wise_data as $key=>$value){
				if($bi%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
             <tr bgcolor="<? echo $bgcolor;?>">
					<td width="30"><? echo $bi; ?></td>
                    <td width="55"><? echo $buyer_short_name_library[$key]; ?></td>
					<td width="80" align="right"><? echo $original_array_buyer_wise[$key]['previous_po_qty']; $original_po_qty_tot+=$original_array_buyer_wise[$key]['previous_po_qty'];?></td>
                    <td width="50" align="right"><? echo number_format($original_array_buyer_wise[$key]['previous_po_amount']/$original_array_buyer_wise[$key]['previous_po_qty'],2); ?></td>
					<td width="100" align="right"><? echo $original_array_buyer_wise[$key]['previous_po_amount']; $original_po_amount_tot+=$original_array_buyer_wise[$key]['previous_po_amount']?></td>
                    <td width="80" align="right"><? echo $buyer_wise_data[$key]['po_quantity']; $current_po_qty_tot+=$buyer_wise_data[$key]['po_quantity'];?></td>
                    <td width="50" align="right"><? echo number_format($buyer_wise_data[$key]['po_price']/$buyer_wise_data[$key]['po_quantity'],2); ?></td>
					<td width="100" align="right"><? echo $buyer_wise_data[$key]['po_price']; $current_po_amount_tot+=$buyer_wise_data[$key]['po_price'];?></td>
                    <td width="80" align="right"><? echo $buyer_wise_data[$key]['po_quantity']-$original_array_buyer_wise[$key]['previous_po_qty']; ?></td>
                    <td width="50" align="right"> <? echo number_format(($buyer_wise_data[$key]['po_price']/$buyer_wise_data[$key]['po_quantity'])-($original_array_buyer_wise[$key]['previous_po_amount']/$original_array_buyer_wise[$key]['previous_po_qty']),2);?></td>
					<td width="100" align="right"><? echo $buyer_wise_data[$key]['po_price']-$original_array_buyer_wise[$key]['previous_po_amount']; ?></td>
                 </tr>
            <?
			$bi++;
			}
			?>
            </table>
              <table class="rpt_table" width="840" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tfoot>
                 <tr>
					<th width="30" ></th>
                    <th width="55" ></th>
					<th width="80"><? echo $original_po_qty_tot;?></th>
                    <th width="50"></th>
					<th width="100"><? echo number_format($original_po_amount_tot,2);?></th>
                    <th width="80"><? echo $current_po_qty_tot;?></th>
                    <th width="50"></th>
					<th width="100"><? echo number_format($current_po_amount_tot,2);?></th>
                    <th width="80"><? echo $current_po_qty_tot-$original_po_qty_tot;?></th>
                    <th width="50"></th>
					<th width="100"><? echo number_format($current_po_amount_tot-$original_po_amount_tot,2);?></th>
                 </tr>
				</tfoot>
			</table>
        
			<table width="2305">
				<tr class="form_caption">
					<td colspan="24" align="center">Order Edit History</td>
				</tr>
				<tr class="form_caption">
					<td colspan="24" align="center"><? echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
			<table class="rpt_table" width="2283" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="30">SL</th>
                    <th width="55">Company</th>
					<th width="45">Job No</th>
                    <th width="50">Buyer</th>
					<th width="100">Style Ref</th>
                   
                    <th width="100">Team</th>
                    <th width="100">Team Member</th>
                    <th width="60">Edit No</th>
                    <th width="60">Order Status</th>
					<th width="90">Order No</th>
                    <th width="60">PO Received Date</th>
					<th width="60">Pub. Shipment Date</th>
					<th width="60">Org. Shipment Date</th>
					<th width="60">Fac. Receive Date</th>
					<th width="60">PO Quantity</th>
                    <th width="60">Change Po  Qty</th>
					<th width="40">Avg. Price</th>
                    <th width="40">Change Avg. Price</th>
					<th width="40">Excess Cut %</th>
					<th width="60">Plan Cut</th>
                    
                    
					<th width="60">Projected Po</th>
					<th width="40">Packing</th>
					
                    <th width="50">File No</th>
                    <th width="50">Status</th>
                    <th width="60">Update By</th>
                    <th width="90">Update Date</th>
                    <th width="">Remarks </th>
                    
				</thead>
			</table>
			<div style="width:2305px; max-height:400px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="2283" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
	<?
	
                $previous_po_qty=0;
				$avg_price=0;
				$previous_po_qty_net=0;
				$avg_price_net=0;
				$i=1;
				foreach($sql_data as $row_data)
				{
				   
				$rowSpan=count($log_array[$row_data[csf('id')]]);
				$previous_po_qty=$array_for_compare[$row_data[csf('id')]]['previous_po_qty'][0];
				$avg_price=$array_for_compare[$row_data[csf('id')]]['avg_price'][0];
				$previous_po_qty_net=$array_for_compare[$row_data[csf('id')]]['previous_po_qty'][$rowSpan-1];
				$avg_price_net=$array_for_compare[$row_data[csf('id')]]['avg_price'][$rowSpan-1];
				?>
                <tr align="center" bgcolor="#FFCCCC" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
					<td width="30"><? echo $i; ?></td>
                    <td width="55" style="word-wrap:break-word; word-break: break-all;"><? echo $company_library[$row_data[csf('company_name')]];?></td>
					<td width="45" style="word-wrap:break-word; word-break: break-all;" title="<? echo $row_data[csf('job_no')]; ?>"><? echo $row_data[csf('job_no_prefix_num')]; ?></td> 
                    <td width="50" style="word-wrap:break-word; word-break: break-all;"><? echo $buyer_short_name_library[$row_data[csf('buyer_name')]]; ?></td>
					<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $row_data[csf('style_ref_no')]; ?></td>
                   
                    
                    <td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $team_name_library[$row_data[csf('team_leader')]]; ?></td>
                    <td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $team_member_name_library[$row_data[csf('dealing_marchant')]]; ?></td>
                    
                    
                    <td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo "Current Data" ?></td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $order_status[$row_data[csf('is_confirmed')]]; ?></td>
					<td width="90" style="word-wrap:break-word; word-break: break-all;"><? echo $row_data[csf('po_number')]; ?></td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all;">
					<?
					$po_received_date= $row_data[csf('po_received_date')];
					if($po_received_date !="" && $po_received_date !="0000-00-00" && $po_received_date !="0")
					{
					echo change_date_format($po_received_date,'dd-mm-yyyy','-'); 
					}
					?>
                    </td>
					<td width="60" style="word-wrap:break-word; word-break: break-all;">
					<?
					$pub_shipment_date= $row_data[csf('pub_shipment_date')];
					if($pub_shipment_date !="" && $pub_shipment_date !="0000-00-00" && $pub_shipment_date !="0")
					{
					echo change_date_format($pub_shipment_date,'dd-mm-yyyy','-'); 
					}
					?>
                    </td>
					<td width="60" style="word-wrap:break-word; word-break: break-all;">
					<?
					$shipment_date= $row_data[csf('shipment_date')];
					if($shipment_date !="" && $shipment_date !="0000-00-00" && $shipment_date !="0")
					{
					echo change_date_format($shipment_date,'dd-mm-yyyy','-'); 
					}
					?>
                    </td>
					<td width="60" style="word-wrap:break-word; word-break: break-all;">
					<?
					$factory_received_date= $row_data[csf('factory_received_date')];
					if($factory_received_date !="" && $factory_received_date !="0000-00-00" && $factory_received_date !="0")
					{
					echo change_date_format($factory_received_date,'dd-mm-yyyy','-'); 
					}
					?>
                    </td>
					<td width="60" style="word-wrap:break-word; word-break: break-all; text-align:right" title="<?  if($rowSpan>0){echo $row_data[csf('po_quantity')]-$previous_po_qty_net;}  ?>">
					<? echo $row_data[csf('po_quantity')]; ?>
                    </td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? if($rowSpan>0){echo $row_data[csf('po_quantity')]-$previous_po_qty;}?>
                    </td>
					<td width="40" style="word-wrap:break-word; word-break: break-all; text-align:right" title="<?  if($rowSpan>0){echo $row_data[csf('unit_price')]-$avg_price_net;}  ?>">
					<? echo number_format($row_data[csf('unit_price')],2); ?>
                    </td>
                    <td width="40" style="word-wrap:break-word; word-break: break-all; text-align:right" >
					<? if($rowSpan>0){echo number_format($row_data[csf('unit_price')]-$avg_price,2);}?>
                    </td>
					<td width="40" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($row_data[csf('excess_cut')],2); ?>
                    </td>
					<td width="60" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo $row_data[csf('plan_cut')]; ?>
                    </td>
                   
					<td width="60" style="word-wrap:break-word; word-break: break-all;">
                   <? echo $row_data[csf('projected_po_id')]; ?>
                    </td>
					<td width="40" style="word-wrap:break-word; word-break: break-all;">
					<? echo $packing[$row_data[csf('packing')]]; ?>
                    </td>
					
                    <td width="50" style="word-wrap:break-word; word-break: break-all;">
					<? echo $row_data[csf('file_no')]; ?>
                    </td>
                     <td width="50" style="word-wrap:break-word; word-break: break-all;">
					<? echo $row_status[$row_data[csf('status_active')]]; ?>
                    </td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all;">
                    <? echo $user_name_library[$row_data[csf('updated_by')]]; ?>
                    </td>
                    <td width="90" style="word-wrap:break-word; word-break: break-all;">
                   
                     <? echo $row_data[csf('update_date')]; ?>
                    </td>
                    <td width="" style="word-wrap:break-word; word-break: break-all;">
					 <? echo $row_data[csf('details_remarks')]; ?>
                    </td>
                    </tr>
                    
                    
                    <?
					//b.is_confirmed,b.po_number,b.po_received_date,b.pub_shipment_date,b.shipment_date,b.factory_received_date,b.po_quantity,b.unit_price,b.excess_cut,b.plan_cut,b.status_active,b.projected_po_id,b.packing,b.details_remarks,b.file_no,b.updated_by,b.update_date
					$prive_data_array=array();
					$prive_data_array[$row_data[csf('id')]]['is_confirmed']=$row_data[csf('is_confirmed')];
					$prive_data_array[$row_data[csf('id')]]['po_number']=$row_data[csf('po_number')];
					$prive_data_array[$row_data[csf('id')]]['po_received_date']=$row_data[csf('po_received_date')];
					$prive_data_array[$row_data[csf('id')]]['pub_shipment_date']=$row_data[csf('pub_shipment_date')];
					$prive_data_array[$row_data[csf('id')]]['shipment_date']=$row_data[csf('shipment_date')];
					$prive_data_array[$row_data[csf('id')]]['factory_received_date']=$row_data[csf('factory_received_date')];
					$prive_data_array[$row_data[csf('id')]]['po_quantity']=$row_data[csf('po_quantity')];
					$prive_data_array[$row_data[csf('id')]]['unit_price']=$row_data[csf('unit_price')];
					$prive_data_array[$row_data[csf('id')]]['excess_cut']=$row_data[csf('excess_cut')];
					$prive_data_array[$row_data[csf('id')]]['plan_cut']=$row_data[csf('plan_cut')];
					$prive_data_array[$row_data[csf('id')]]['status_active']=$row_data[csf('status_active')];
					$prive_data_array[$row_data[csf('id')]]['projected_po_id']=$row_data[csf('projected_po_id')];
					$prive_data_array[$row_data[csf('id')]]['packing']=$row_data[csf('packing')];
					$prive_data_array[$row_data[csf('id')]]['details_remarks']=$row_data[csf('details_remarks')];
					$prive_data_array[$row_data[csf('id')]]['file_no']=$row_data[csf('file_no')];
					$prive_data_array[$row_data[csf('id')]]['updated_by']=$row_data[csf('updated_by')];
					$prive_data_array[$row_data[csf('id')]]['update_date']=$row_data[csf('update_date')];
					//print_r($prive_data_array);
					//=====================================================================================================================
					$ii=1;
					
					foreach($log_array[$row_data[csf('id')]] as $key=>$value)
				    {
						if($ii%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$order_status_color= $bgcolor;
						if($value['order_status']!=$prive_data_array[$row_data[csf('id')]]['is_confirmed']){
							$order_status_color="#FF0000";
						}
						$po_number_color= $bgcolor;
						if($value['po_no']!=$prive_data_array[$row_data[csf('id')]]['po_number']){
							$po_number_color="#FF0000";
						}
						$po_received_date_color= $bgcolor;
						if(change_date_format($value['po_received_date'],'dd-mm-yyyy','-')!=change_date_format($prive_data_array[$row_data[csf('id')]]['po_received_date'],'dd-mm-yyyy','-')){
							$po_received_date_color="#FF0000";
						}
						$pub_shipment_date_color= $bgcolor;
						if(change_date_format($value['shipment_date'],'dd-mm-yyyy','-')!=change_date_format($prive_data_array[$row_data[csf('id')]]['pub_shipment_date'],'dd-mm-yyyy','-')){
							$pub_shipment_date_color="#FF0000";
						}
						$shipment_date_color= $bgcolor;
						if(change_date_format($value['org_ship_date'],'dd-mm-yyyy','-')!=change_date_format($prive_data_array[$row_data[csf('id')]]['shipment_date'],'dd-mm-yyyy','-')){
							$shipment_date_color="#FF0000";
						}
						
						$factory_received_date_color= $bgcolor;
						if(change_date_format($value['fac_receive_date'],'dd-mm-yyyy','-')!=change_date_format($prive_data_array[$row_data[csf('id')]]['factory_received_date'],'dd-mm-yyyy','-')){
							$factory_received_date_color="#FF0000";
						}
						
						$previous_po_qty_color= $bgcolor;
						if($value['previous_po_qty']!=$prive_data_array[$row_data[csf('id')]]['po_quantity']){
							$previous_po_qty_color="#FF0000";
						}
						
						$avg_price_color= $bgcolor;
						if($value['avg_price']!=$prive_data_array[$row_data[csf('id')]]['unit_price']){
							$avg_price_color="#FF0000";
						}
						
						$excess_cut_parcent_color= $bgcolor;
						if($value['excess_cut_parcent']!=$prive_data_array[$row_data[csf('id')]]['excess_cut']){
							$excess_cut_parcent_color="#FF0000";
						}
						$plan_cut_color= $bgcolor;
						if($value['plan_cut']!=$prive_data_array[$row_data[csf('id')]]['plan_cut']){
							$plan_cut_color="#FF0000";
						}
						$status_color= $bgcolor;
						if($value['status']!=$prive_data_array[$row_data[csf('id')]]['status_active']){
							$status_color="#FF0000";
						}
						
						$projected_po_color= $bgcolor;
						if($value['projected_po']!=$prive_data_array[$row_data[csf('id')]]['projected_po_id']){
							$projected_po_color="#FF0000";
						}
						
						$packing_color= $bgcolor;
						if($value['packing']!=$prive_data_array[$row_data[csf('id')]]['packing']){
							$packing_color="#FF0000";
						}
						
						$remarks_color= $bgcolor;
						if($value['remarks']!=$prive_data_array[$row_data[csf('id')]]['details_remarks']){
							$remarks_color="#FF0000";
						}
						$file_no_color= $bgcolor;
						if($value['file_no']!=$prive_data_array[$row_data[csf('id')]]['file_no']){
							$file_no_color="#FF0000";
						}
						$updated_by_color= $bgcolor;
						if($value['update_by']!=$prive_data_array[$row_data[csf('id')]]['updated_by']){
							$updated_by_color="#FF0000";
						}
						$update_date_color= $bgcolor;
						if(change_date_format($value['update_date'],'dd-mm-yyyy','-')!=change_date_format($prive_data_array[$row_data[csf('id')]]['update_date'],'dd-mm-yyyy','-')){
							$update_date_color="#FF0000";
						}
						
						$previous_po_qt_h=$array_for_compare[$row_data[csf('id')]]['previous_po_qty'][$ii];
				        $avg_price_h=$array_for_compare[$row_data[csf('id')]]['avg_price'][$ii];
					
					?>
                    <tr align="center" bgcolor="<? echo $bgcolor;?>">
					<td width="30"><? echo $i.".".$ii; ?></td>
					
                     <td width="55" style="word-wrap:break-word; word-break: break-all; color:<? echo $bgcolor;?>"><? echo $company_library[$row_data[csf('company_name')]];?></td>
					<td width="45" style="word-wrap:break-word; word-break: break-all; color:<? echo $bgcolor;?>" title="<? echo $row_data[csf('job_no')];  ?>"><? echo $row_data[csf('job_no_prefix_num')]; ?></td>
                    <td width="50" style="word-wrap:break-word; word-break: break-all; color:<? echo $bgcolor;?>"><? echo $buyer_short_name_library[$row_data[csf('buyer_name')]]; ?></td>
					<td width="100" style="word-wrap:break-word; word-break: break-all; color:<? echo $bgcolor;?>"><? echo $row_data[csf('style_ref_no')]; ?></td>
                   
                    
                    <td width="100" style="word-wrap:break-word; word-break: break-all; color:<? echo $bgcolor;?>"><? echo $team_name_library[$row_data[csf('team_leader')]]; ?></td>
                    <td width="100" style="word-wrap:break-word; word-break: break-all; color:<? echo $bgcolor;?>"><? echo $team_member_name_library[$row_data[csf('dealing_marchant')]]; ?></td>
                    
                    
                    <td width="60" style="word-wrap:break-word; word-break: break-all;">
					<?
					$update_no=$rowSpan-$ii;
					if($update_no==0){
					echo "Original Data"; 
					}
					else{
						echo "Edit No: $update_no";
					}
					?>
                    </td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all;" bgcolor="<? echo $order_status_color; ?>"><? echo $order_status[$value['order_status']]; ?></td>
					<td width="90" style="word-wrap:break-word; word-break: break-all;" bgcolor="<? echo $po_number_color; ?>"><? echo $value['po_no']; ?></td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all;"  bgcolor="<? echo $po_received_date_color; ?>">
					<?
					$po_received_date= $value['po_received_date'];
					if($po_received_date !="" && $po_received_date !="0000-00-00" && $po_received_date !="0")
					{
					echo change_date_format($po_received_date,'dd-mm-yyyy','-'); 
					}
					?>
                    </td>
					<td width="60" style="word-wrap:break-word; word-break: break-all;" bgcolor="<? echo $pub_shipment_date_color; ?>">
					<?
					$pub_shipment_date= $value['shipment_date'];
					if($pub_shipment_date !="" && $pub_shipment_date !="0000-00-00" && $pub_shipment_date !="0")
					{
					echo change_date_format($pub_shipment_date,'dd-mm-yyyy','-'); 
					}
					?>
                    </td>
					<td width="60" style="word-wrap:break-word; word-break: break-all;" bgcolor="<? echo $shipment_date_color; ?>">
					<?
					$shipment_date= $value['org_ship_date'];
					if($shipment_date !="" && $shipment_date !="0000-00-00" && $shipment_date !="0")
					{
					echo change_date_format($shipment_date,'dd-mm-yyyy','-'); 
					}
					?>
                    </td>
					<td width="60" style="word-wrap:break-word; word-break: break-all;" bgcolor="<? echo $factory_received_date_color; ?>">
					<?
					$factory_received_date= $value['fac_receive_date'];
					if($factory_received_date !="" && $factory_received_date !="0000-00-00" && $factory_received_date !="0")
					{
					echo change_date_format($factory_received_date,'dd-mm-yyyy','-'); 
					}
					?>
                    </td>
					<td width="60" style="word-wrap:break-word; word-break: break-all; text-align:right" bgcolor="<? echo $previous_po_qty_color; ?>">
					<? echo $value['previous_po_qty']; ?>
                    </td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all; text-align:right" bgcolor="<? echo $previous_po_qty_color; ?>">
					<? if($rowSpan>1 && $ii<count($array_for_compare[$row_data[csf('id')]]['previous_po_qty'])){echo $value['previous_po_qty']-$previous_po_qt_h;} ?>
                    </td>
					<td width="40" style="word-wrap:break-word; word-break: break-all; text-align:right" bgcolor="<? echo $avg_price_color; ?>">
					<? echo number_format($value['avg_price'],2); ?>
                    </td>
                    <td width="40" style="word-wrap:break-word; word-break: break-all; text-align:right" bgcolor="<? echo $avg_price_color; ?>">
					<? if($rowSpan>1 && $ii<count($array_for_compare[$row_data[csf('id')]]['previous_po_qty'])){echo number_format($value['avg_price']-$avg_price_h,2);} ?>
                    </td>
					<td width="40" style="word-wrap:break-word; word-break: break-all; text-align:right" bgcolor="<? echo $excess_cut_parcent_color; ?>">
					<? echo number_format($value['excess_cut_parcent'],2); ?>
                    </td>
					<td width="60" style="word-wrap:break-word; word-break: break-all; text-align:right" bgcolor="<? echo $plan_cut_color; ?>">
					<? echo $value['plan_cut']; ?>
                    </td>
                    
					<td width="60" style="word-wrap:break-word; word-break: break-all;" bgcolor="<? echo $projected_po_color; ?>">
                   <? echo $value['projected_po']; ?>
                    </td>
					<td width="40" style="word-wrap:break-word; word-break: break-all;" bgcolor="<? echo $packing_color; ?>">
					<? echo $packing[$value['packing']]; ?>
                    </td>
					
                    <td width="50" style="word-wrap:break-word; word-break: break-all;" bgcolor="<? echo $file_no_color; ?>">
					<? echo $value['file_no']; ?>
                    </td>
                    <td width="50" style="word-wrap:break-word; word-break: break-all;" bgcolor="<? echo $status_color; ?>">
					<? echo $row_status[$value['status']]; ?>
                    </td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all;" bgcolor="<? echo $updated_by_color; ?>">
                    <? echo $user_name_library[$value['update_by']]; ?>
                    </td>
                    <td width="90" style="word-wrap:break-word; word-break: break-all;" bgcolor="<? echo $update_date_color; ?>">
                     <? echo $value['update_date']; ?>
                    </td>
                    <td width="" style="word-wrap:break-word; word-break: break-all;" bgcolor="<? echo $remarks_color; ?>">
                      <? echo $value['remarks']; ?>
                    </td>
                    </tr>
                    <?
					$prive_data_array[$row_data[csf('id')]]['is_confirmed']=$value['order_status'];
					$prive_data_array[$row_data[csf('id')]]['po_number']=$value['po_no'];
					$prive_data_array[$row_data[csf('id')]]['po_received_date']=$value['po_received_date'];
					$prive_data_array[$row_data[csf('id')]]['pub_shipment_date']=$value['shipment_date'];
					$prive_data_array[$row_data[csf('id')]]['shipment_date']=$value['org_ship_date'];
					$prive_data_array[$row_data[csf('id')]]['factory_received_date']=$value['fac_receive_date'];
					$prive_data_array[$row_data[csf('id')]]['po_quantity']=$value['previous_po_qty'];
					$prive_data_array[$row_data[csf('id')]]['unit_price']=$value['avg_price'];
					$prive_data_array[$row_data[csf('id')]]['excess_cut']=$value['excess_cut_parcent'];
					$prive_data_array[$row_data[csf('id')]]['plan_cut']=$value['plan_cut'];
					$prive_data_array[$row_data[csf('id')]]['status_active']=$value['status'];
					$prive_data_array[$row_data[csf('id')]]['projected_po_id']=$value['projected_po'];
					$prive_data_array[$row_data[csf('id')]]['packing']=$value['packing'];
					$prive_data_array[$row_data[csf('id')]]['details_remarks']=$value['remarks'];
					$prive_data_array[$row_data[csf('id')]]['file_no']=$value['file_no'];
					$prive_data_array[$row_data[csf('id')]]['updated_by']=$value['update_by'];
					$prive_data_array[$row_data[csf('id')]]['update_date']=$value['update_date'];
					$ii++;
					}
					//=========================================================
                    ?>
                <?
				$i++;
				}
				?>
                 
				</table>
				<table class="rpt_table" width="2283" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tfoot>
					<th width="30"><!--SL--></th>
					<th width="50"><!--Buyer--></th>
					<th width="100"><!--Job No--></th>
					<th width="100"><!--Style Ref--></th>
                    <th width="100"><!--Company--></th>
                    <th width="100"><!--Team--></th>
                    <th width="100"><!--Team Member--></th>
                    <th width="80"><!--Item--></th>
                    <th width="90"><!--Order Status--></th>
					<th width="90"><!--Order No--></th>
                    <th width="80"><!--PO Received Date--></th>
					<th width="80"><!--Pub. Shipment Date--></th>
					<th width="80"><!--Org. Shipment Date--></th>
					<th width="80"><!--Fac. Receive Date--></th>
					<th width="80"><!--PO Quantity--></th>
					<th width="60"><!--Avg. Price--></th>
					<th width="60"><!--Excess Cut %--></th>
					<th width="80"><!--Plan Cut--></th>
                    <th width="60"><!--Status--></th>
					<th width="100"><!--Projected Po--></th>
					<th width="60"><!--Packing--></th>
					<th width="90"><!--Remarks--> </th>
                    <th width="60"><!--File No--></th>
                    <th width="80"><!--Update By--></th>
                    <th width=""><!--Update Date--></th>
					</tfoot>
				</table>
				</div>
			</fieldset>
		</div>
	<?
	}
//===========================================================================================================================================================
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows";
	exit();	
}

if($action=="labdip_veiw")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo "select color_name_id, submitted_to_buyer,case when approval_status= 3  then approval_status_date  end  as app_date, case when approval_status= 5  then approval_status_date  end  as resubmit_date from wo_po_lapdip_approval_info where po_break_down_id in($po_id) and color_name_id=$color_id and approval_status in(3,5) and is_deleted=0 and status_active=1 order by id";
	
	$sql_lapdib_comments=sql_select( "select color_name_id,submitted_to_buyer, approval_status, approval_status_date from wo_po_lapdip_approval_info where po_break_down_id in($po_id) and color_name_id=$color_id  and is_deleted=0 and status_active=1 order by id");
	
	
	
	//$sql_lapdib_comments=sql_select("select color_name_id, submitted_to_buyer,case when approval_status= 3  then approval_status_date  end  as app_date, case when approval_status= 5  then approval_status_date  end  as resubmit_date from wo_po_lapdip_approval_info where po_break_down_id in($po_id) and color_name_id=$color_id and approval_status in(3,5) and is_deleted=0 and status_active=1 order by id");
	?>
    
    
    <table class="rpt_table" width="300" cellpadding="0" cellspacing="0" border="1" rules="all">
	<thead>
    <th width="30">SL</th> 
    <th>Color</th>
    <th>Submit Date</th>
    <th>Status</th>
    <th>Status Date</th>
    </thead>
    <tbody>
    <?
	$i=1;
	foreach($sql_lapdib_comments as $row_lapdib_comments)
	{
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";   
	?>
    <tr bgcolor="<? echo $bgcolor; ?>">
    <td><? echo $i;?></td>
    <td><? echo  $color_name_library[$row_lapdib_comments[csf('color_name_id')]];?></td>
    <td>
	<? 
	if($row_lapdib_comments[csf('submitted_to_buyer')] !="" && $row_lapdib_comments[csf('submitted_to_buyer')] !="0000-00-00" && $row_lapdib_comments[csf('submitted_to_buyer')] !="0")
	{
	echo  change_date_format($row_lapdib_comments[csf('submitted_to_buyer')],'dd-mm-yyyy','-');
	}
	?>
    </td>
    <td>
	<? 
	
	echo  $approval_status[$row_lapdib_comments[csf('approval_status')]];
	
	?>
    </td>
    <td>
	<? 
	if($row_lapdib_comments[csf('approval_status_date')] !="" && $row_lapdib_comments[csf('approval_status_date')] !="0000-00-00" && $row_lapdib_comments[csf('approval_status_date')] !="0")
	{
	echo  change_date_format($row_lapdib_comments[csf('approval_status_date')],'dd-mm-yyyy','-');
	}
	?>
    </td>
    </tr>
    <?
	$i++;
	}
	?>
    <tr>
    </tbody>
    </table>
    
    
    
    
    <?
}

if($action=="ppsample_veiw")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo "select a.color_number_id,b.approval_status, approval_status_date from  wo_po_color_size_breakdown a, wo_po_sample_approval_info b , lib_sample c where a.po_break_down_id=b.po_break_down_id and a.id=b.color_number_id and  b.sample_type_id=c.id	and b.po_break_down_id in($po_id)  and c.sample_type=2 and a.color_number_id=$color_id     and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1";
		$sql_sample_comments=sql_select("select a.color_number_id,b.submitted_to_buyer,b.approval_status, approval_status_date from  wo_po_color_size_breakdown a, wo_po_sample_approval_info b , lib_sample c where a.po_break_down_id=b.po_break_down_id and a.id=b.color_number_id and  b.sample_type_id=c.id	and b.po_break_down_id in($po_id)  and c.sample_type=2 and a.color_number_id=$color_id     and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1");

	
	//$sql_sample_comments=sql_select("select a.color_number_id,b.submitted_to_buyer, case when b.approval_status=3 then 	b.approval_status_date end as app_date,case when b.approval_status=5 then 	b.approval_status_date end as resubmit_date from  wo_po_color_size_breakdown a, wo_po_sample_approval_info b , lib_sample c where a.po_break_down_id=b.po_break_down_id and a.id=b.color_number_id and and b.sample_type_id=c.id	b.po_break_down_id in($po_id) and approval_status in(3,5) and c.sample_type=2 and a.color_number_id=$color_id     and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1");
	?>
   <table class="rpt_table" width="300" cellpadding="0" cellspacing="0" border="1" rules="all">
	<thead>
    <th width="30">SL</th> 
    <th>Color</th>
    <th>Submit Date</th>
    <th>Status</th>
    <th>Status Date</th>
    </thead>
    <tbody>
    <?
	$i=1;
	foreach($sql_sample_comments as $sql_sample_comments)
	{
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";   
	?>
    <tr bgcolor="<? echo $bgcolor; ?>">
    <td><? echo $i;?></td>
    <td><? echo  $color_name_library[$sql_sample_comments[csf('color_number_id')]];?></td>
    <td>
	<? 
	if($sql_sample_comments[csf('submitted_to_buyer')] !="" && $sql_sample_comments[csf('submitted_to_buyer')] !="0000-00-00" && $sql_sample_comments[csf('submitted_to_buyer')] !="0")
	{
	echo  change_date_format($sql_sample_comments[csf('submitted_to_buyer')],'dd-mm-yyyy','-');
	}
	?>
    </td>
    <td>
	<? 
	
	echo  $approval_status[$sql_sample_comments[csf('approval_status')]];
	
	?>
    </td>
    <td>
	<? 
	if($sql_sample_comments[csf('approval_status_date')] !="" && $sql_sample_comments[csf('approval_status_date')] !="0000-00-00" && $sql_sample_comments[csf('approval_status_date')] !="0")
	{
	echo  change_date_format($sql_sample_comments[csf('approval_status_date')],'dd-mm-yyyy','-');
	}
	?>
    </td>
    </tr>
    <?
	$i++;
	}
	?>
    <tr>
    </tbody>
    </table>
    
    <?
}

	

if($action=="remarks_veiw")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$sql_lapdib_comments=sql_select("select color_name_id,lapdip_comments from wo_po_lapdip_approval_info where po_break_down_id in($po_id) and color_name_id=$color_id  and is_deleted=0 and status_active=1");//and  (send_to_factory_date='$send_to_factory_date' or recv_from_factory_date='$recv_from_factory_date' or submitted_to_buyer='$submitted_to_buyer' or approval_status_date='$approval_status_date') 
	
	$sql_sample_comments=sql_select("select a.color_number_id,b.sample_comments from  wo_po_color_size_breakdown a, wo_po_sample_approval_info b , lib_sample c where a.po_break_down_id=b.po_break_down_id and a.id=b.color_number_id  and b.sample_type_id=c.id	and b.po_break_down_id in($po_id) and c.sample_type=2 and a.color_number_id=$color_id    and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1");//and  (b.submitted_to_buyer='$pp_submitted_to_buyer' or b.approval_status_date='$pp_approval_status_date') 
	?>
    <table class="rpt_table" width="530" cellpadding="0" cellspacing="0" border="1" rules="all">
	<thead>
    <th width="30">SL</th> 
    <th>Comments</th>
    </thead>
    <tbody>
    <tr>
    <td colspan="3"><strong>Lapdib Comments</strong></td>
    </tr>
    
    <?
	$i=1;
	foreach($sql_lapdib_comments as $row_lapdib_comments)
	{
	?>
    <tr>
    <td><? echo $i;?></td>
    <td><? echo  $row_lapdib_comments[csf('lapdip_comments')];?></td>
    </tr>
    <?
	$i++;
	}
	?>
    <tr>
    <td colspan="3"><strong>Sample Comments</strong></td>
    </tr>
    
    <?
	$i=1;
	foreach($sql_sample_comments as $row_sample_comments)
	{
	?>
    <tr>
    <td><? echo $i;?></td>
    <td><? echo  $row_sample_comments[csf('sample_comments')];?></td>
    </tr>
    <?
	$i++;
	}
	?>
    </tbody>
    </table>
    <?
}

if($action=="booking_date_view")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$sql_booking=sql_select("select b.booking_no, b.booking_date from wo_pre_cost_fabric_cost_dtls a,wo_booking_mst b, wo_booking_dtls c where a.job_no=b.job_no and a.job_no=c.job_no and a.id=c.pre_cost_fabric_cost_dtls_id and b.booking_no=c.booking_no and b.booking_type=4 and b.item_category=2 and b.fabric_source=1  and c.po_break_down_id in($po_id) and c.fabric_color_id=$color_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.booking_no,b.booking_date");//and a.body_part_id in(1,14,15,16,17,20)
	?>
    <table class="rpt_table" width="310" cellpadding="0" cellspacing="0" border="1" rules="all">
	<thead>
    <th width="30">SL</th> 
    <th width="100">Booking No</th>
    <th>Booking Date</th>
    </thead>
    <tbody>
    <?
	$i=1;
	foreach($sql_booking as $row_booking)
	{
	?>
    <tr>
    <td><? echo $i;?></td>
    <td><? echo  $row_booking[csf('booking_no')];?></td>
    <td>
	<? 
	if($row_booking[csf('booking_date')] !="" && $row_booking[csf('booking_date')] !="0000-00-00" && $row_booking[csf('booking_date')] !="0")
	{
	echo  change_date_format($row_booking[csf('booking_date')],'dd-mm-yyyy','-');
	}
	?>
    </td>
    </tr>
    <?
	$i++;
	}
	?>
    </tbody>
    </table>
    <?
}

if($action=="fin_receive_date_view")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

					
	$sql_fin_fab_receive_qty=sql_select("select  a.receive_date,a.booking_id,a.booking_no
					from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c 
					where a.id=b.mst_id  and a.entry_form=37 and  a.item_category=2  and b.id=c.dtls_id and b.trans_id=c.trans_id and c.trans_type=1 and c.po_breakdown_id in($po_id) and c.color_id=$color_id $booking_id   and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by  a.receive_date,a.booking_id,a.booking_no");
	?>
    <table class="rpt_table" width="310" cellpadding="0" cellspacing="0" border="1" rules="all">
	<thead>
    <th width="30">SL</th> 
    <th width="100">Booking No</th>
    <th>Receive Date</th>
    </thead>
    <tbody>
    <?
	$i=1;
	foreach($sql_fin_fab_receive_qty as $row_fin_fab_receive_qty)
	{
	?>
    <tr>
    <td><? echo $i;?></td>
    <td><? echo  $row_fin_fab_receive_qty[csf('booking_no')];?></td>
    <td>
	<? 
	if($row_fin_fab_receive_qty[csf('receive_date')] !="" && $row_fin_fab_receive_qty[csf('receive_date')] !="0000-00-00" && $row_fin_fab_receive_qty[csf('receive_date')] !="0")
	{
	echo  change_date_format($row_fin_fab_receive_qty[csf('receive_date')],'dd-mm-yyyy','-');
	}
	?>
    </td>
    </tr>
    <?
	$i++;
	}
	?>
    </tbody>
    </table>
    <?
}
?>