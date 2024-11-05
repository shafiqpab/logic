<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form for Ship Date Extention Report.
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	21-05-2015
Updated by 		:  		
Update date		: 	   
QC Performed BY	:		
QC Date			:	
Comments		: 
*/

header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
extract($_REQUEST);
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//------------------------------------------------------------------------------------------------------------
if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 180, "select buy.buyer_name, buy.id from lib_buyer buy where status_active =1 and is_deleted=0 order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );   	 
	exit();
}
$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
$dealing_marchant_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$report_title=str_replace("'","",$report_title);
	$buyer_cond="";
	if($cbo_buyer_name!=0) $buyer_cond=" and a.buyer_name=$cbo_buyer_name";
	if($txt_date_from!="" && $txt_date_to!="")
	{
		if($db_type==0)
		{
			$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
		}
		else if($db_type==2)
		{
			$txt_date_from=change_date_format($txt_date_from,'','',-1);
			$txt_date_to=change_date_format($txt_date_to,'','',-1);
		}
	}
	
	$update_log_sql=sql_select("select c.id as log_id, c.job_no, c.po_id, c.order_status, c.org_ship_date, c.po_received_date, c.shipment_date, c.fac_receive_date from wo_po_update_log c where c.org_ship_date between '$txt_date_from' and '$txt_date_to' order by  c.org_ship_date desc");
	
	$update_log_data=array();
	foreach($update_log_sql as $row)
	{
		$update_log_data[$row[csf("po_id")]][$row[csf("org_ship_date")]]['log_id']=$row[csf("log_id")];
		$update_log_data[$row[csf("po_id")]][$row[csf("org_ship_date")]]['job_no']=$row[csf("job_no")];
		$update_log_data[$row[csf("po_id")]][$row[csf("org_ship_date")]]['order_status']=$row[csf("order_status")];
		$update_log_data[$row[csf("po_id")]][$row[csf("org_ship_date")]]['org_ship_date']=$row[csf("org_ship_date")];
		$update_log_data[$row[csf("po_id")]][$row[csf("org_ship_date")]]['po_received_date']=$row[csf("po_received_date")];
		$update_log_data[$row[csf("po_id")]][$row[csf("org_ship_date")]]['shipment_date']=$row[csf("shipment_date")];
		$update_log_data[$row[csf("po_id")]][$row[csf("org_ship_date")]]['fac_receive_date']=$row[csf("fac_receive_date")];
	}
	$po_sql=sql_select("select a.id as job_id, a.dealing_marchant, a.buyer_name, a.job_no, a.style_ref_no, b.id as po_id, b.po_number, b.is_confirmed, b.po_quantity, b.shipment_date, b.po_received_date, b.pub_shipment_date, b.factory_received_date from wo_po_details_master a,  wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$cbo_company_name and b.shipment_date between '$txt_date_from' and '$txt_date_to' $buyer_cond order by  b.id asc");

	?>
	<div style="width:1250px;" align="left">
    <table cellpadding="0" cellspacing="0" width="1150"  align="left">
         <tr>
           <td align="center" width="100%" colspan="14" class="form_caption"><strong><? echo $report_title; ?></strong></td>
        </tr>
    </table>	
    <table cellspacing="2" cellpadding="0" border="1" rules="all" width="1250" class="rpt_table"  align="left">
        <thead>
            <tr>
                <th width="40" >SL</th>
                <th width="120" >Dealing Merchant</th>
                <th width="100" >Buyer Name</th>
                <th width="120">Job No</th>
                <th width="120">Style No</th>
                <th width="120">PO No</th>
                <th width="80">PO Status</th>
                <th width="80">PO Qnty</th>
                <th width="50">Change</th>
                <th width="70">Org. Ship Date</th>
                <th width="70">Extension Days (From Initial)</th>
                <th width="70">PO Rcv. Date</th>
                <th width="70">Pub Shipment Date</th>
                <th >Fac. Po Rcv. Date</th>
            </tr>
        </thead>
    </table>
    </div>
    <br />
    <div style="width:1250px; overflow-y:scroll; max-height:330px;" id="scroll_body"  align="left">
    <table cellspacing="2" cellpadding="0" border="1" rules="all" width="1230" class="rpt_table" id="table_body"  align="left">
	<? 
		$k=1;
		foreach($po_sql as $row)
		{
			if ($k%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			$row_span=count($update_log_data[$row[csf("po_id")]]);
			if($row_span<1) $row_span=1; else $row_span=$row_span+1;
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
            	<td width="40" align="center" rowspan="<? echo $row_span; ?>" valign="top"><p><? echo $k; ?>&nbsp;</p></td>
                <td width="120" rowspan="<? echo $row_span; ?>" valign="top"><p><? echo $dealing_marchant_library[$row[csf("dealing_marchant")]]; ?>&nbsp;</p></td>
                <td width="100" rowspan="<? echo $row_span; ?>" valign="top"><p><? echo $buyer_library[$row[csf("buyer_name")]]; ?>&nbsp;</p></td>
                <td width="120" rowspan="<? echo $row_span; ?>" valign="top"><p><? echo $row[csf("job_no")]; ?>&nbsp;</p></td>
                <td width="120" rowspan="<? echo $row_span; ?>" valign="top"><p><? echo $row[csf("style_ref_no")]; ?>&nbsp;</p></td>
                <td width="120" rowspan="<? echo $row_span; ?>" valign="top"><p><? echo $row[csf("po_number")]; ?>&nbsp;</p></td>
                <td width="80" rowspan="<? echo $row_span; ?>" valign="top"><p><? echo $order_status[$row[csf("is_confirmed")]]; ?>&nbsp;</p></td>
                <td width="80" rowspan="<? echo $row_span; ?>" align="right" valign="top"><? echo number_format($row[csf("po_quantity")],0); ?></td>
                <?
				if($row_span<1)
				{
					?>
					<td width="50" align="center"><p><? echo "Initial"; ?></p></td>
					<td width="70" align="center"><p><? if($row[csf("shipment_date")]!="" && $row[csf("shipment_date")]!="0000-00-00") echo change_date_format($row[csf("shipment_date")]); ?>&nbsp;</p></td>
					<td width="70" align="center"><p><? echo "0"; ?>&nbsp;</p></td>
					<td width="70" align="center"><p><? if($row[csf("po_received_date")]!="" && $row[csf("po_received_date")]!="0000-00-00") echo change_date_format($row[csf("po_received_date")]); ?>&nbsp;</p></td>
					<td width="70" align="center"><p><? if($row[csf("pub_shipment_date")]!="" && $row[csf("pub_shipment_date")]!="0000-00-00") echo change_date_format($row[csf("pub_shipment_date")]); ?>&nbsp;</p></td>
					<td  align="center"><p><? if($row[csf("factory_received_date")]!="" && $row[csf("factory_received_date")]!="0000-00-00") echo change_date_format($row[csf("factory_received_date")]); ?>&nbsp;</p></td>
                    </tr>
					<?
				}
				else
				{
					//$m=2;
					$change=$row_span;
					foreach($update_log_data[$row[csf("po_id")]] as $log_id=>$val)
					{
						$ext_date=datediff( 'd', $row[csf("shipment_date")], $val['org_ship_date']);
						if($ext_date>0) $ext_date=$ext_date-1;
						?>
                        <td width="50" align="center"><? echo $change; ?></td>
                        <td width="70" align="center"><p><? if($val['org_ship_date']!="" && $val['org_ship_date']!="0000-00-00") echo change_date_format($val['org_ship_date']); ?>&nbsp;</p></td>
                        <td width="70" align="center"><p><? /*if($ext_date>0)*/ echo $ext_date." Days"; ?>&nbsp;</p></td>
                        <td width="70" align="center"><p><? if($val['po_received_date']!="" && $val['po_received_date']!="0000-00-00") echo change_date_format($val['po_received_date']); ?>&nbsp;</p></td>
                        <td width="70" align="center"><p><? if($val['shipment_date']!="" && $val['shipment_date']!="0000-00-00") echo change_date_format($val['shipment_date']); ?>&nbsp;</p></td>
                        <td align="center"><p><? if($val['fac_receive_date']!="" && $val['fac_receive_date']!="0000-00-00") echo change_date_format($val['fac_receive_date']); ?>&nbsp;</p></td>
                        </tr>
                        <?
						$change--;
					}
					?>
					<td width="50" align="center"><p><? echo "Initial"; ?></p></td>
					<td width="70" align="center"><p><? if($row[csf("shipment_date")]!="" && $row[csf("shipment_date")]!="0000-00-00") echo change_date_format($row[csf("shipment_date")]); ?>&nbsp;</p></td>
					<td width="70" align="center"><p><? echo "0"; ?>&nbsp;</p></td>
					<td width="70" align="center"><p><? if($row[csf("po_received_date")]!="" && $row[csf("po_received_date")]!="0000-00-00") echo change_date_format($row[csf("po_received_date")]); ?>&nbsp;</p></td>
					<td width="70" align="center"><p><? if($row[csf("pub_shipment_date")]!="" && $row[csf("pub_shipment_date")]!="0000-00-00") echo change_date_format($row[csf("pub_shipment_date")]); ?>&nbsp;</p></td>
					<td  align="center"><p><? if($row[csf("factory_received_date")]!="" && $row[csf("factory_received_date")]!="0000-00-00") echo change_date_format($row[csf("factory_received_date")]); ?>&nbsp;</p></td>
                    </tr>
					<?
				}
			$k++;
		}
		?> 
    </table>
    </div>
	<?
	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();	
}


