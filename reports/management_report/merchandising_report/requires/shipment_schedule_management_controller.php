<?
/*-------------------------------------------- Comments
Version                  :  V2
Purpose			         : 	This form will create  Shipment Schedule for Management Report
Functionality	         :	
JS Functions	         :
Created by		         :	Monzu 
Creation date 	         : 
Requirment Client        : 
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :                   
DB Script                : 
Updated by 		         : File wise button created by REZA 		
Update date		         : 15/04/2019	   
QC Performed BY	         :		
QC Date			         :	
Comments		         : From this version oracle conversion is start
*/
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.commisions.php');
require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.fabrics.php');
require_once('../../../../includes/class4/class.yarns.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.others.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.commercials.php');
require_once('../../../../includes/class4/class.washes.php');
require_once('../../../../includes/class4/cm_gmt_class.php');

session_start();
extract($_REQUEST); 
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');


$user_id=$_SESSION['logic_erp']['user_id'];

$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
$company_team_name_arr=return_library_array( "select id,team_name from lib_marketing_team",'id','team_name');
$company_team_member_name_arr=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info",'id','team_member_name');
$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='knit_order_entry'",'master_tble_id','image_location');

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   	 
} 

if ($action=="load_drop_down_team_member")
{
	echo create_drop_down( "cbo_team_member", 130, "select id,team_member_name 	 from lib_mkt_team_member_info  where team_id='$data' and status_active=1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Select Team Member-", $selected, "" );   	 
}

if($type=="report_generate")
{
	$data=explode("_",$data);
	
	$txt_job_no=str_replace("'","",$data[15]);
	$txt_job_id=str_replace("'","",$data[16]);
	$txt_order_no=str_replace("'","",$data[17]);
	$txt_order_id=$data[18];
	//echo $txt_job_no.'='.$txt_job_id;
	
	if($txt_job_id!=''){$job_con=" and a.id =$txt_job_id";	}
	else if($txt_job_no!=''){$job_con=" and a.job_no like ('%$txt_job_no%')";}
	else{$job_con="";}
	//echo $job_con;die;
	if($txt_order_id!=''){$po_con=" and b.id=$txt_order_id";	}
	else if($txt_order_no!=''){$po_con=" and b.po_number like ('%$txt_order_no')";}
	else{$po_con="";}

	$user_name_arr=return_library_array( "select id,user_name from  user_passwd",'id','user_name');
	/* $cm_for_shipment_schedule_arr=return_library_array( "select job_no,cm_for_sipment_sche from  wo_pre_cost_dtls",'job_no','cm_for_sipment_sche'); */
	$ex_factory_qty_arr=return_library_array( "select po_break_down_id,sum(ex_factory_qnty) as ex_factory_qnty from  pro_ex_factory_mst where  status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','ex_factory_qnty');
	$template_id_arr=return_library_array("select po_number_id, template_id from tna_process_mst group by po_number_id, template_id","po_number_id","template_id");

	if($data[0]==0) $company_name="%%"; else $company_name=$data[0];
	//if($data[1]==0) $buyer_name="%%"; else $buyer_name=$data[1];
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$data[1]";//.str_replace("'","",$cbo_buyer_name)
	}
	if(trim($data[2])!="") $start_date=$data[2];
	if(trim($data[3])!="") $end_date=$data[3];
	$cbo_order_status2=$data[4];
	if($data[4]==2) $cbo_order_status="%%"; else $cbo_order_status= "$data[4]";
	if(trim($data[5])=="0") $team_leader="%%"; else $team_leader="$data[5]";
	if(trim($data[6])=="0") $dealing_marchant="%%"; else $dealing_marchant="$data[6]";
	if(trim($data[8])!="") $pocond="and b.id in(".str_replace("'",'',$data[8]).")"; else  $pocond="";
	if($db_type==0)
	{
	$start_date=change_date_format($start_date,'yyyy-mm-dd','-');
	$end_date=change_date_format($end_date,'yyyy-mm-dd','-');
	}
	if($db_type==2)
	{
	$start_date=change_date_format($start_date,'yyyy-mm-dd','-',1);
	$end_date=change_date_format($end_date,'yyyy-mm-dd','-',1);
	}
	$cm_for_shipment_schedule_arr=return_library_array( "select c.job_no,c.cm_cost as cm_for_sipment_sche from  wo_pre_cost_dtls c,wo_po_break_down b where c.job_no=b.job_no_mst $pocond ",'job_no','cm_for_sipment_sche');
	$cbo_category_by=$data[7]; $caption_date='';
	
	if($cbo_category_by==1)
	{
		if ($start_date!="" && $end_date!="")
		{
			$date_cond="and b.pub_shipment_date between '$start_date' and  '$end_date'";
		}
		else	
		{
			$date_cond="";
		}
	}
	else if($cbo_category_by==2)
	{
		if ($start_date!="" && $end_date!="")
		{
			$date_cond=" and b.po_received_date between '$start_date' and  '$end_date'";
		}
		else	
		{
			$date_cond="";
		}
	}
	else 
	{
		if ($start_date!="" && $end_date!="")
		{
			$date_cond=" and b.shipment_date between '$start_date' and  '$end_date'";
		}
		else	
		{
			$date_cond="";
		}
	}
	
	if($data[14]>0){$order_status_con=" and b.is_confirmed=".$data[14];}else{$order_status_con="";}
	ob_start();
	?>
	<div align="center">
        <div align="center">
            <table>
                <tr valign="top">
                    <td valign="top">
                    <h3 align="left" id="accordion_h2" class="accordion_h" onClick="accordion_menu( this.id,'content_summary1_panel', '')"> -Summary Panel</h3>
                        <div id="content_summary1_panel"> 
                            <fieldset>
                                <table width="750" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">
                                    <thead>
                                    <th width="50">SL</th>
                                    <th width="130">Company Name</th><th width="200">Buyer Name</th>
                                    <th width="130">Quantity</th><th width="100">Value</th><th width="50">Value %</th>
                                    <th width="130"><strong>Full Shipped</strong></th><th width="130"><strong>Partial Shipped</strong></th> 
                                    <th width="130"><strong>Running</strong></th><th><strong>Ex-factory Percentage</strong></th>  
                                    </thead>
                                    <tbody>
                                    <?
                                    $i=1;
                                    $total_po=0;
                                    $total_price=0;
                                    
                                    $po_qnty_array= array();
                                    $po_value_array= array();
                                    $po_full_shiped_array= array();
                                    $po_full_shiped_value_array= array();
                                    $po_partial_shiped_array= array();
                                    $po_partial_shiped_value_array= array();
                                    $po_running_array= array();
                                    $po_running_value_array= array();
									
                                    $data_array=sql_select("select a.company_name,a.buyer_name,sum(b.po_quantity*a.total_set_qnty) as po_quantity, sum(b.po_total_price) as po_total_price   from wo_po_details_master a, wo_po_break_down b   where a.job_no=b.job_no_mst   and a.company_name like '$company_name' $buyer_id_cond and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' $order_status_con $date_cond $pocond $job_con $po_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.company_name,a.buyer_name");//b.unit_price
								 
                                    foreach ($data_array as $row)
                                    { 
									if ($i%2==0)  
									$bgcolor="#E9F3FF";
									else
									$bgcolor="#FFFFFF";	
                                    //$data_array_po=sql_select("select a.company_name, b.id, sum(b.po_quantity*a.total_set_qnty) as po_quantity , b.shiping_status  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.company_name =$row[company_name] and a.buyer_name =$row[buyer_name] and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond and a.status_active=1 and b.status_active=1 ");
									$data_array_po=sql_select("select a.company_name, b.id, (b.po_quantity*a.total_set_qnty) as po_quantity , b.shiping_status  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.company_name =".$row[csf('company_name')]." and a.buyer_name =".$row[csf('buyer_name')]." and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' $date_cond $order_status_con $pocond  $job_con $po_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
                                    $full_shiped=0;$partial_shiped=0;
                                    foreach ($data_array_po as $row_po)
                                    {
                                   // $ex_factory_qnty=return_field_value( 'sum(ex_factory_qnty)','pro_ex_factory_mst', 'po_break_down_id="'.$row_po[id].'" and status_active=1 and is_deleted=0' );
								  $ex_factory_qnty=$ex_factory_qty_arr[$row_po[csf("id")]];
                                    if($row_po[csf('shiping_status')]==3)
                                    {
                                    $full_shiped+=$ex_factory_qnty;
                                    }
                                    if($row_po[csf('shiping_status')]==2)
                                    {
                                    $partial_shiped+=$ex_factory_qnty;
                                    }
                                    }
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor;?>">
                                    <td width="50"><? echo $i;?></td>
                                    <td width="130"><? echo $company_short_name_arr[$row[csf('company_name')]]; ?></td>
                                    <td width="200"><? echo $buyer_short_name_arr[$row[csf('buyer_name')]];?></td>
                                    <td width="130" align="right">
                                    <? 
                                    echo fn_number_format($row[csf('po_quantity')],0); $total_po +=$row[csf('po_quantity')]; 
                                    if (array_key_exists($row[csf('company_name')], $po_qnty_array)) 
                                    {
                                    $po_qnty_array[$row[csf('company_name')]]+=$row[csf('po_quantity')];
                                    }
                                    else
                                    {
                                    $po_qnty_array[$row[csf('company_name')]]=$row[csf('po_quantity')];	
                                    }
                                    ?>
                                    </td>
                                    <td width="100" align="right">
                                    <? 
                                    echo fn_number_format($row[csf('po_total_price')],2); $total_price+= $row[csf('po_total_price')];
                                    if (array_key_exists($row[csf('company_name')], $po_value_array)) 
                                    {
                                    $po_value_array[$row[csf('company_name')]]+=$row[csf('po_total_price')];
                                    }
                                    else
                                    {
                                    $po_value_array[$row[csf('company_name')]]=$row[csf('po_total_price')];	
                                    }
                                    ?>
                                    <input type="hidden" id="value_<? echo $i; ?>" value="<? echo $row[csf('po_total_price')];?>"/>
                                    </td>
                                    <td width="50" id="value_percent_<? echo $i; ?>" align="right"></td>
                                    <td width="130" align="right">
                                    <? 
                                    echo fn_number_format($full_shiped,0); $full_shipped_total+=$full_shiped;
                                    if (array_key_exists($row[csf('company_name')], $po_full_shiped_array)) 
                                    {
                                    $po_full_shiped_array[$row[csf('company_name')]]+=$full_shiped;
                                    }
                                    else
                                    {
                                    $po_full_shiped_array[$row[csf('company_name')]]=$full_shiped;	
                                    }
                                    /*if (array_key_exists($row[company_name], $po_full_shiped_value_array)) 
                                    {
                                    $po_full_shiped_value_array[$row[company_name]]+=$full_shiped_value;
                                    }
                                    else
                                    {
                                    $po_full_shiped_value_array[$row[company_name]]=$full_shiped_value;	
                                    }*/
                                    
                                    ?>
                                    </td>
                                    <td width="130" align="right">
                                    <? 
                                    echo fn_number_format($partial_shiped,0); $partial_shipped_total+=$partial_shiped;
                                    if (array_key_exists($row[csf('company_name')], $po_partial_shiped_array)) 
                                    {
                                    $po_partial_shiped_array[$row[csf('company_name')]]+=$partial_shiped;
                                    }
                                    else
                                    {
                                    $po_partial_shiped_array[$row[csf('company_name')]]=$partial_shiped;	
                                    }
                                    /*if (array_key_exists($row[company_name], $po_partial_shiped_value_array)) 
                                    {
                                    $po_partial_shiped_value_array[$row[company_name]]+=$partial_shipd_value;
                                    }
                                    else
                                    {
                                    $po_partial_shiped_value_array[$row[company_name]]=$partial_shipd_value;	
                                    }*/
                                    ?>
                                    </td> 
                                    <td width="130" align="right">
                                    <? 
                                    $runing=$row[csf('po_quantity')]-($full_shiped+$partial_shiped); echo fn_number_format($runing,0);$running_shipped_total+=$runing;
                                    if (array_key_exists($row[csf('company_name')], $po_running_array)) 
                                    {
                                    $po_running_array[$row[csf('company_name')]]+=$runing;
                                    }
                                    else
                                    {
                                    $po_running_array[$row[csf('company_name')]]=$runing;	
                                    }
                                    ?>
                                    </td>
                                    <td align="right"><? $status=(($full_shiped+$partial_shiped)/$row[csf('po_quantity')])*100; $full_shipped_total_percent+=$status;  echo fn_number_format($status,2); ?></td>  
                                    </tr>
                                    <?
                                    $i++;
                                    }
                                    ?>
                                    </tbody>
                                    <tfoot>
                                    <th width="50"></th>
                                    <th width="130"></th><th width="200"></th>
                                    <th width="130"><? echo fn_number_format($total_po,0); ?></th><th width="100"><?  echo fn_number_format($total_price,2); ?> <input type="hidden" id="total_value" value="<? echo $total_price;?>"/></th><th width="50"></th>
                                    <th width="130"><? echo fn_number_format($full_shipped_total,0); ?></th><th width="130"><? echo fn_number_format($partial_shipped_total,0); ?></th> 
                                    <th width="130"><? echo fn_number_format($running_shipped_total,0); ?></th><th><input type="hidden" id="tot_row" value="<? echo $i;?>"/></th>  
                                    </tfoot>
                                </table>
                            </fieldset>
                        </div>
                    </td>
                    <td valign="top">
                    <h3 align="left" id="accordion_h3" class="accordion_h" onClick="accordion_menu( this.id,'content_summary2_panel', '')"> -Summary Panel</h3>
                        <div id="content_summary2_panel"> 
                            <fieldset>
                                <table width="800" border="1" class="rpt_table" rules="all">
                                    <thead>
                                    <th>Company Name</th>
                                    <th>Particular Name</th>
                                    <th>Total Amount</th>
                                    <th>Full Shipped </th>
                                    <th>Partial Shipped </th>
                                    <th>Running </th>
                                    <th>Ex-factory Percentage</th>
                                    </thead>
									<?
                                    $comp_po_total=0;
                                    $comp_po_total_value=0;
                                    $total_full_shiped_qnty=0;
                                    $total_par_qnty=0;
                                    $total_run_qnty=0;
                                    $total_full_shiped_val=0;
                                    $total_par_val=0;
                                    $total_run_val=0;
                                    foreach($po_qnty_array as $key=> $value)
                                    {
                                    ?>
                                    <tr>
                                    <td rowspan="2" align="center"><? echo $company_short_name_arr[$key];//echo $company_name; ?></td>
                                    <td align="center">PO Quantity</td>
                                    <td align="right"><? echo fn_number_format($value+$po_qnty_array_projec[$key],0);$comp_po_total=$comp_po_total+$value+$po_qnty_array_projec[$key]; ?></td>
                                    <td align="right"><? echo fn_number_format($po_full_shiped_array[$key],0); $total_full_shiped_qnty+=$po_full_shiped_array[$key];?></td>
                                    <td align="right"><? echo fn_number_format($po_partial_shiped_array[$key],0); $total_par_qnty+=$po_partial_shiped_array[$key];?></td>
                                    <td align="right">
                                    <?
                                    echo fn_number_format($po_running_array[$key],0);
                                    $total_run_qnty+=$po_running_array[$key];
                                    ?>
                                    </td>
                                    <td align="right">
                                    <?
                                    $ex_factory_per=(($po_full_shiped_array[$key]+$po_partial_shiped_array[$key])/($value))*100;
                                    echo fn_number_format($ex_factory_per,2).' %';
                                    ?>
                                    </td>
                                    </tr>
                                    <tr bgcolor="white">
                                    <td align="center">LC Value</td>
                                    <td align="right"><? echo fn_number_format($po_value_array[$key],2);  $comp_po_total_value=$comp_po_total_value+$po_value_array[$key];?></td>
                                    <td align="right">
                                    <?
                                    $full_shiped_value=($po_value_array[$key]/$value)*$po_full_shiped_array[$key];
                                    echo fn_number_format($full_shiped_value,2);
                                    $total_full_shiped_val+=$full_shiped_value;
                                    
                                    //echo fn_number_format($po_full_shiped_value_array[$key],2);
                                    //$total_full_shiped_val+=$po_full_shiped_value_array[$key];
                                    ?>
                                    </td>
                                    <td align="right">
                                    <?
                                    $full_partial_shipeddd_value=($po_value_array[$key]/$value)*$po_partial_shiped_array[$key];
                                    echo fn_number_format($full_partial_shipeddd_value,2);
                                    $total_par_val+=$full_partial_shipeddd_value;
                                    //echo fn_number_format($po_partial_shiped_value_array[$key],2);
                                    // $total_par_val+=$po_partial_shiped_value_array[$key];
                                    ?>
                                    </td>
                                    
                                    <td align="right">
                                    <?
                                    $full_running_value=($po_value_array[$key]/$value)*$po_running_array[$key];
                                    echo fn_number_format($full_running_value,2);
                                    $total_run_val+=$full_running_value;
                                    //echo fn_number_format($po_running_value_array[$key],2);
                                    //$total_run_val+=$po_running_value_array[$key];
                                    ?>
                                    </td>
                                    <td align="right">
                                    <?
                                    $ex_factory_per_value=(($full_shiped_value+$full_partial_shipeddd_value)/($po_value_array[$key]))*100;
                                    echo fn_number_format($ex_factory_per_value,2).' %';
                                    //$ex_factory_per_value=(($po_full_shiped_value_array[$key]+$po_partial_shiped_value_array[$key])/($po_value_array[$key]))*100;
                                    //echo fn_number_format($ex_factory_per_value,2).' %';
                                    ?>
                                    </td>
                                    </tr>
									<?
                                    }
                                    ?>
                                    <tfoot>
                                    <tr>
                                    <th align="center" rowspan="2"> Total:</th>
                                    <th align="center">Qnty Total:</th>
                                    <th align="right"><? echo fn_number_format($comp_po_total,0); ?></th>
                                    
                                    <th align="right">
                                    <?
                                    
                                    echo fn_number_format($total_full_shiped_qnty,2);
                                    ?>
                                    </th>
                                    <th align="right">
                                    <?
                                    
                                    echo fn_number_format($total_par_qnty,2);
                                    ?>
                                    </th>
                                    <th align="right">
                                    <?
                                    
                                    echo fn_number_format($total_run_qnty,2);
                                    ?>
                                    </th>
                                    <th align="right">
                                    <?
                                    //echo fn_number_format($ex_factory_per_value,2).' %';
                                    ?>
                                    </th>
                                    </tr>
                                    <tr bgcolor="#999999">
                                    <th align="center">Value Total:</th>
                                    <th align="right"><? echo fn_number_format($comp_po_total_value,2); ?></th>
                                    <th align="right">
                                    <?
                                    echo fn_number_format($total_full_shiped_val,2);
                                    ?>
                                    </th>
                                    <th align="right">
                                    <?
                                    echo fn_number_format($total_par_val,2);
                                    ?>
                                    </th>
                                    <th align="right">
                                    <?
                                    echo fn_number_format($total_run_val,2);
                                    ?>
                                    </th>
                                    <th align="right">
                                    <?
                                    //echo fn_number_format($ex_factory_per_value,2).' %';
                                    ?>
                                    </th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </fieldset>
                        </div>
                    </td>
                    <td valign="top">
                    <h3 align="left" id="accordion_h4" class="accordion_h" onClick="accordion_menu( this.id,'content_summary3_panel', '')"> -Shipment Performance Summary</h3>
                    <div id="content_summary3_panel"> 
                    </div>
                    </td>
                </tr>
            </table>
            <h3 align="left" width="3150" id="accordion_h4" class="accordion_h" onClick="accordion_menu( this.id,'content_report_panel', '')"> -Report Panel</h3>
            <div id="content_report_panel"> 
                <table width="3150" id="table_header_1" border="1" class="rpt_table" rules="all">
                    <thead>
                        <tr>
                            <th width="50">SL</th>
                            <th width="100" >Company</th>
                            <th width="100">Job No</th>
                            <th  width="100">Buyer</th>
                            <th  width="100">PO No</th>
                            <th  width="100">Agent</th>
                            <th width="100">Order Status</th>
                            <th width="100">Prod. Catg</th>
                            <th width="100">Img</th>
                            <th width="100">Style Ref</th>
                            <th width="100">Item</th>
							<th width="100">SMV</th>
                            <th width="100">Org.Ship Date</th>
                            <th width="100">Pub.Ship Date</th>
                            <th width="100">PO Rec. Date</th>
                            <th  width="100">Days in Hand</th>
                            <th width="100">Order Qnty(Pcs)</th>
                            <th width="100">Order Qnty</th>
                            <th width="100">Uom</th>
                            <th  width="100">Per Unit Price</th>
                            <th width="100">Order Value</th>
                            <th width="100">LC/SC No</th>
                            <th width="100">Ex-Fac Qnty </th>
                            <th  width="100">Short/Access Qnty</th>
                            <th width="100">Short/Access Value</th>
                            <th width="100">Yarn Req</th>
                            <th width="100">CM </th>
                            <th width="100" >Shipping Status</th>
                            <th width="100"> Team Member</th>
                            <th width="100">Team Name</th>
                            <th width="100">User Id</th>
                            <th width="100">Remarks</th>
                        </tr>
                    </thead>
                </table>
                <div style=" max-height:400px; overflow-y:scroll; width:3180px"  align="left" id="scroll_body">
                    <table width="3150" border="1" class="rpt_table" rules="all" id="table_body">
                    <?
                    $i=1;
                    $order_qnty_pcs_tot=0;
                    $order_qntytot=0;
                    $oreder_value_tot=0;
                    $total_ex_factory_qnty=0;
                    $total_short_access_qnty=0;
                    $total_short_access_value=0;
                    $yarn_req_for_po_total=0;
                    //$data_array=sql_select("select a.job_no,a.company_name,a.buyer_name,a.agent_name,a.style_ref_no,a.job_quantity,a.product_category,a.job_no,a.gmts_item_id,a.total_set_qnty,a.order_uom,a.team_leader,a.dealing_marchant,b.id,b.is_confirmed ,b.po_number,b.po_quantity,b.pub_shipment_date,b.po_received_date,DATEDIFF(b.pub_shipment_date,'$date') date_diff,b.unit_price,b.po_total_price,b.details_remarks,b.shiping_status,sum(c.ex_factory_qnty) as ex_factory_qnty,MAX(ex_factory_date) as ex_factory_date  from wo_po_details_master a, wo_po_break_down b,pro_ex_factory_mst c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id  and a.company_name like '$company_name' and a.buyer_name like '$buyer_name' and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond and a.status_active=1 and b.status_active=1 group by b.id order by b.pub_shipment_date,a.job_no_prefix_num,b.id");
					//$lc_number_arr=return_library_array( "select a.wo_po_break_down_id, b.export_lc_no from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id",'wo_po_break_down_id','export_lc_no');

					 $data_array_group=sql_select("select b.grouping  as grouping   from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id where  a.job_no=b.job_no_mst   and a.company_name like '$company_name' $buyer_id_cond $order_status_con and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond  $job_con $po_con and a.status_active=1 and b.status_active=1 group by b.grouping");
					 
					 foreach ($data_array_group as $row_group)
                    { 
					$gorder_qnty_pcs_tot=0;
                    $gorder_qntytot=0;
					$goreder_value_tot=0;
					$gtotal_ex_factory_qnty=0;
					$gtotal_short_access_qnty=0;
					$gtotal_short_access_value=0;
                    $gyarn_req_for_po_total=0;
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" >
                            <th width="50" align="center" > <p><? echo $row_group[csf('grouping')]; ?></p> </th>
							<th width="100"></th>
							<th width="100"></th>
                            <th width="100"></th>
							<th width="100" ></th>
							<th width="100"></th>
							<th width="100"></th>
                            <th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100" ></th>
                            <th width="100" ></th>
							<th width="100" ></th>
							<th width="100" ></th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="100" ></th>
							<th width="100" ></th>
                           <th width="100" ></th>
                            <th width="100"></th>
                            <th width="100"> </th>
                            <th width="100" ></th>
							<th width="100" ></th>
							<th width="100" ></th>
							<th width="100" ></th>
							<th width="100" ></th>
                        
                            </tr>
					<? 
					
					if($db_type==0)
					{
                    $data_array=sql_select("select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, b.inserted_by,b.id, b.is_confirmed, b.po_number, b.po_quantity, b.pub_shipment_date, b.shipment_date, b.po_received_date, DATEDIFF(b.pub_shipment_date, '$date') date_diff_1, DATEDIFF(b.shipment_date,'$date') date_diff_2, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, sum(c.ex_factory_qnty) as ex_factory_qnty, MAX(c.ex_factory_date) as ex_factory_date, DATEDIFF(b.pub_shipment_date,MAX(c.ex_factory_date)) date_diff_3, DATEDIFF(b.shipment_date, MAX(c.ex_factory_date)) date_diff_4 from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 where  a.job_no=b.job_no_mst and a.company_name like '$company_name'  $buyer_id_cond $order_status_con and a.team_leader like '$team_leader' and b.grouping='".$row_group[csf('grouping')]."' and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $job_con $po_con and a.status_active=1 and b.status_active=1  group by b.id order by b.pub_shipment_date,a.job_no_prefix_num,b.id");
					}
					
					if($db_type==2)
					{
						$date=date('d-m-Y');
						if($row_group[csf('grouping')]!="")
						{
						$grouping="and b.grouping='".$row_group[csf('grouping')]."'";
						}
						if($row_group[csf('grouping')]=="")
						{
						 $grouping	= "and b.grouping IS NULL";
						}
                    $data_array=sql_select("select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,b.inserted_by, b.id, b.is_confirmed, b.po_number, b.po_quantity, b.pub_shipment_date, b.shipment_date, b.po_received_date, (b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_1, (b.shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_2, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, sum(c.ex_factory_qnty) as ex_factory_qnty, MAX(c.ex_factory_date) as ex_factory_date, (b.pub_shipment_date - MAX(c.ex_factory_date)) date_diff_3, (b.shipment_date - MAX(c.ex_factory_date)) date_diff_4,a.set_smv from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 where  a.job_no=b.job_no_mst and a.company_name like '$company_name'  $buyer_id_cond $order_status_con and a.team_leader like '$team_leader' $grouping and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $job_con $po_con and a.status_active=1 and b.status_active=1 group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,b.inserted_by, b.id, b.is_confirmed, b.po_number, b.po_quantity, b.pub_shipment_date, b.shipment_date, b.po_received_date,b.unit_price,b.po_total_price, b.details_remarks, b.shiping_status,a.set_smv order by b.pub_shipment_date,a.job_no_prefix_num,b.id");
					}
					foreach ($data_array as $row)
                    {
				
						$poId_arr[$row[csf('id')]]=$row[csf('id')];	
					}
					$poId_cond=where_con_using_array($poId_arr,0,'a.wo_po_break_down_id');
					
					// echo "<pre>";
					// print_r($data_array);
					 $sc_lc_sql="select a.wo_po_break_down_id as po_id, b.export_lc_no as sc_lc from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $poId_cond
								union  all
								 select a.wo_po_break_down_id as  po_id,b.contract_no as sc_lc from com_sales_contract_order_info a , com_sales_contract b where  a.com_sales_contract_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $poId_cond";
								
					$sc_lc_sql_res= sql_select($sc_lc_sql);
					foreach ($sc_lc_sql_res as $row)
                    {
						$lc_number_arr[$row[csf('po_id')]]=$row[csf('sc_lc')];	
					} 
					
					
					// echo "<pre>";
					// print_r($data_array);
                    foreach ($data_array as $row)
                    { 
						if ($i%2==0)  
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";	
						//--Calculation Yarn Required-------
						/*$data_array_yarn_cons=sql_select("select cons from  wo_pre_cos_fab_co_avg_con_dtls where  po_break_down_id='$row[id]'");
						$total_cons=0;
						$total_row=0;
						foreach($data_array_yarn_cons as $row_yarn_cons)
						{
						$total_cons=$total_cons+$row_yarn_cons['cons'];
						if($row_yarn_cons['cons'] !=0 || $row_yarn_cons['cons']!='' )
						{
						$total_row++;
						}
						}
						$yarn_req_for_po=($total_cons/$total_row)*$row['po_quantity'];*/
						$cons=0;
						$costing_per_pcs=0;
						$data_array_yarn_cons=sql_select("select yarn_cons_qnty from  wo_pre_cost_sum_dtls where  job_no='".$row[csf('job_no')]."'");
						$data_array_costing_per=sql_select("select costing_per from  wo_pre_cost_mst where  job_no='".$row[csf('job_no')]."'");
						list($costing_per)=$data_array_costing_per;
						if($costing_per[csf('costing_per')]==1)
						{
						  $costing_per_pcs=1*12;	
						}
						else if($costing_per[csf('costing_per')]==2)
						{
						 $costing_per_pcs=1*1;	
						}
						else if($costing_per[csf('costing_per')]==3)
						{
						 $costing_per_pcs=2*12;	
						}
						else if($costing_per[csf('costing_per')]==4)
						{
						 $costing_per_pcs=3*12;	
						}
						else if($costing_per[csf('costing_per')]==5)
						{
						 $costing_per_pcs=4*12;	
						}
						
						$yarn_req_for_po=0;
						foreach($data_array_yarn_cons as $row_yarn_cons)
						{
							$cons=$row_yarn_cons[csf('yarn_cons_qnty')];
							$yarn_req_for_po=($row_yarn_cons[csf('yarn_cons_qnty')]/ $costing_per_pcs)*$row[csf('po_quantity')];
						}
						
						//--Calculation Yarn Required-------
						//--Color Determination-------------
						//==================================
						$shipment_performance=0;
						if($row[csf('shiping_status')]==1 && $row[csf('date_diff_1')]>10 )
						{
						$color="";	
						$number_of_order['yet']+=1;
						$shipment_performance=0;
						}
						if($row[csf('shiping_status')]==1 && ($row[csf('date_diff_1')]<=10 && $row[csf('date_diff_1')]>=0))
						{
						$color="orange";
						$number_of_order['yet']+=1;
						$shipment_performance=0;
						}
						if($row[csf('shiping_status')]==1 &&  $row[csf('date_diff_1')]<0)
						{
						$color="red";	
						$number_of_order['yet']+=1;
						$shipment_performance=0;
						}
						//=====================================
						if($row[csf('shiping_status')]==2 && $row[csf('date_diff_1')]>10 )
						{
						$color="";	
						}
						if($row[csf('shiping_status')]==2 && ($row[csf('date_diff_1')]<=10 && $row[csf('date_diff_1')]>=0))
						{
						$color="orange";	
						}
						if($row[csf('shiping_status')]==2 &&  $row[csf('date_diff_1')]<0)
						{
						$color="red";	
						}
						if($row[csf('shiping_status')]==2 &&  $row[csf('date_diff_2')]>=0)
						{
						$number_of_order['ontime']+=1;
						$shipment_performance=1;	
						}
						if($row[csf('shiping_status')]==2 &&  $row[csf('date_diff_2')]<0)
						{
						$number_of_order['after']+=1;
						$shipment_performance=2;	
						}
						//========================================
						if($row[csf('shiping_status')]==3 && $row[csf('date_diff_3')]>=0 )
						{
						$color="green";	
						}
						if($row[csf('shiping_status')]==3 &&  $row[csf('date_diff_3')]<0)
						{
						$color="#2A9FFF";	
						}
						if($row[csf('shiping_status')]==3 && $row[csf('date_diff_4')]>=0 )
						{
						$number_of_order['ontime']+=1;
						$shipment_performance=1;
						}
						if($row[csf('shiping_status')]==3 &&  $row[csf('date_diff_4')]<0)
						{
						$number_of_order['after']+=1;
						$shipment_performance=2;	
						}
						
						$template_id=$template_id_arr[$row[csf('id')]];
						
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
								<td width="50" align="center" bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
								<td width="105" align="center" ><? echo $company_short_name_arr[$row[csf('company_name')]];?></td>
								<td width="100" align="center"><? echo $row[csf('job_no_prefix_num')];?></td>
								<td  width="100" align="center"><p><? echo $buyer_short_name_arr[$row[csf('buyer_name')]];?></p></td>
								<td  width="100" align="center"><p><a href='#report_details' onClick="order_dtls_popup('<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('id')]; ?>','<? echo $template_id; ?>','<? echo $tna_process_type; ?>');"><? echo $row[csf('po_number')];?></a></p></td>
								<td  width="100" align="center"><? echo $buyer_short_name_arr[$row[csf('agent_name')]];?></td>
								<td width="100" align="center"><? echo $order_status[$row[csf('is_confirmed')]];?></td>
								<td width="100" align="center"><? echo $product_category[$row[csf('product_category')]];?></td>
								<td width="100"><img  src='../../../<? echo $imge_arr[$row[csf('job_no')]]; ?>' height='25' width='30' /></td>
								<td width="100" align="center"><p><? echo $row[csf('style_ref_no')];?></p></td>
								<td width="100" align="center">
								<p>
								<?
									$gmts_item_id=explode(',',$row[csf('gmts_item_id')]);
									for($j=0; $j<=count($gmts_item_id); $j++)
									{
									echo $garments_item[$gmts_item_id[$j]];
									}
								?>
								</p>
								</td>
								<td width="100" align="center"><?=$row[csf('set_smv')];?> </td>
                                <td width="100" align="center"><? echo change_date_format($row[csf('shipment_date')],'dd-mm-yyyy','-');?></td>
								<td width="100" align="center"><? echo change_date_format($row[csf('pub_shipment_date')],'dd-mm-yyyy','-');?></td>
								<td width="100" align="center"><? echo change_date_format($row[csf('po_received_date')],'dd-mm-yyyy','-');?></td>
								<td  width="100" align="center" bgcolor="<? echo $color; ?>"> 
								<?
								if($row[csf('shiping_status')]==1 || $row[csf('shiping_status')]==2)
								{
								echo $row[csf('date_diff_1')];
								}
								if($row[csf('shiping_status')]==3)
								{
								echo $row[csf('date_diff_3')];
								}
								?>
								</td>
								<td width="100" align="right">
								<? 
								echo fn_number_format(($row[csf('po_quantity')]*$row[csf('total_set_qnty')]),0);  
								$order_qnty_pcs_tot=$order_qnty_pcs_tot+($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
								$gorder_qnty_pcs_tot=$gorder_qnty_pcs_tot+($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
								?>
                                </td>
								<td width="100" align="right">
								<? 
								echo fn_number_format( $row[csf('po_quantity')],0);
								$order_qntytot=$order_qntytot+$row[csf('po_quantity')];
								$gorder_qntytot=$gorder_qntytot+$row[csf('po_quantity')];
								?>
								</td>
								<td width="100" align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]];?></td>
								<td  width="100" align="right"><? echo fn_number_format($row[csf('unit_price')],2);?></td>
								<td width="100" align="right">
								<? 
								echo fn_number_format($row[csf('po_total_price')],2);
								$oreder_value_tot=$oreder_value_tot+$row[csf('po_total_price')];
								$goreder_value_tot=$goreder_value_tot+$row[csf('po_total_price')];
								?>
                                </td>
								<td width="100" align="center"><? echo $lc_number_arr[$row[csf('id')]]; ?></td>
								<td width="100" align="right">
								<? 
								$ex_factory_qnty=$row[csf('ex_factory_qnty')]; 
								echo  fn_number_format( $ex_factory_qnty,0); 
								$total_ex_factory_qnty=$total_ex_factory_qnty+$ex_factory_qnty ;
								$gtotal_ex_factory_qnty=$gtotal_ex_factory_qnty+$ex_factory_qnty ;;
								if ($shipment_performance==0)
								{
								$po_qnty['yet']+=($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
								$po_value['yet']+=100;
								}
								else if ($shipment_performance==1)
								{
								$po_qnty['ontime']+=$ex_factory_qnty;
								$po_value['ontime']+=((100*$ex_factory_qnty)/($row[csf('po_quantity')]*$row[csf('total_set_qnty')]));
								$po_qnty['yet']+=(($row[csf('po_quantity')]*$row[csf('total_set_qnty')])-$ex_factory_qnty);
								}
								else if ($shipment_performance==2)
								{
								$po_qnty['after']+=$ex_factory_qnty;
								$po_value['after']+=((100*$ex_factory_qnty)/($row[csf('po_quantity')]*$row[csf('total_set_qnty')]));
								$po_qnty['yet']+=(($row[csf('po_quantity')]*$row[csf('total_set_qnty')])-$ex_factory_qnty);
								}
								?> 
								</td>
								<td  width="100" align="right">
								<? 
								$short_access_qnty=($row[csf('po_quantity')]*$row[csf('total_set_qnty')]-$ex_factory_qnty); 
								echo fn_number_format($short_access_qnty,0);
								$total_short_access_qnty=$total_short_access_qnty+$short_access_qnty;
								$gtotal_short_access_qnty=$gtotal_short_access_qnty+$short_access_qnty;;
								?>
                                </td>
								<td width="100" align="right">
								<? 
								$short_access_value=$short_access_qnty*$row[csf('unit_price')];
								echo  fn_number_format($short_access_value,2);
								$total_short_access_value=$total_short_access_value+$short_access_value;
								$gtotal_short_access_value=$gtotal_short_access_value+$short_access_value;
								?>
                                </td>
								<td width="100" align="right" title="<? echo "Cons:".$cons."Costing per:".$costing_per[csf('costing_per')];?>">
								<? 
								echo fn_number_format($yarn_req_for_po,2);
								$yarn_req_for_po_total=$yarn_req_for_po_total+$yarn_req_for_po;
								$gyarn_req_for_po_total=$gyarn_req_for_po_total+$yarn_req_for_po;
								?>
                                </td>
                                
								<td width="100" align="right"><? echo fn_number_format(($cm_for_shipment_schedule_arr[$row[csf('job_no')]]/$costing_per_pcs)*$row[csf('po_quantity')],2); ?></td>
								<td width="100" align="center"><? echo $shipment_status[$row[csf('shiping_status')]]; ?></td>
								<td width="100" align="center"><? echo $company_team_member_name_arr[$row[csf('dealing_marchant')]];?></td>
								<td width="100" align="center"><? echo $company_team_name_arr[$row[csf('team_leader')]];?></td>
								<td width="100" title="Id=<? echo $row[csf('inserted_by')];?>"><? echo $user_name_arr[$row[csf('inserted_by')]]; ?></td>
								<td width="100"><p><? echo $row[csf('details_remarks')]; ?></p></td>
							</tr>
                    <?
                    $i++;
                    }
					?>
                     <tr bgcolor="#CCCCCC" style="vertical-align:middle" height="25">
                            <td width="50" align="center" >  Total: </td>
                            <td width="100" ></td>
                            <td  width="100"></td>
                            <td  width="100"></td>
                            <td width="100"></td>
                            <td  width="100"></td>
                            <td width="100"></td>
                            <td width="100"></td>
                            <td width="100"></td>
                            <td width="100"></td>
                            <td width="100"></td>
							<td width="100"></td>
                            <td width="100"></td>
                            <td width="100"></td>
                            <td width="100"></td>
                            <td  width="100"></td>
                            <td width="100" align="right"><? echo fn_number_format($gorder_qnty_pcs_tot,0); ?></td>
                            <td width="100" align="right"><? echo fn_number_format($gorder_qntytot,0); ?></td>
                            <td width="100"></td>
                            <td  width="100"></td>
                            <td width="100" align="right"><? echo fn_number_format($goreder_value_tot,2); ?></td>
                            <td width="100"></td>
                            <td width="100" align="right"><? echo fn_number_format($gtotal_ex_factory_qnty,0); ?></td>
                            <td  width="100" align="right"> <? echo fn_number_format($gtotal_short_access_qnty,0); ?></td>
                            <td width="100" align="right"> <? echo fn_number_format($gtotal_short_access_value,0); ?></td>
                            <td width="100" align="right"><? echo fn_number_format($gyarn_req_for_po_total,2); ?></td>
                            <td width="100"></td>
                            <td width="100" ></td>
                            <td width="100"></td>
                            <td width="100"></td>
                            <td width="100"></td>
                            <td width="100"></td>
                            </tr>
                    <?
					}
                    ?>
                    </table>
                </div>
                <table width="3150" id="report_table_footer" border="1" class="rpt_table" rules="all">
                    <tfoot>
                        <tr>
                            <th width="50"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
                            <th width="100" id="total_order_qnty_pcs"><? echo fn_number_format($order_qnty_pcs_tot,0); ?></th>
                            <th width="100" id="total_order_qnty"><? echo fn_number_format($order_qntytot,0); ?></th>
                            <th width="100"></th>
                            <th  width="100"></th>
                            <th width="100" id="value_total_order_value"><? echo fn_number_format($oreder_value_tot,2); ?></th>
                            <th width="100"></th>
                            <th width="100" id="total_ex_factory_qnty"> <? echo fn_number_format($total_ex_factory_qnty,0); ?></th>
                            <th  width="100" id="total_short_access_qnty"><? echo fn_number_format($total_short_access_qnty,0); ?></th>
                            <th width="100" id="value_total_short_access_value"><? echo fn_number_format($total_short_access_value,0); ?></th>
                            <th width="100" id="value_yarn_req_tot"><? echo fn_number_format($yarn_req_for_po_total,2); ?></th>
                            <th width="100"></th>
                            <th width="100" ></th>
                            <th width="100"> </th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="100"></th>
                        </tr>
                    </tfoot>
                </table>
                <div id="shipment_performance" style="visibility:hidden">
                    <fieldset>
                        <table width="600" border="1" cellpadding="0" cellspacing="1" class="rpt_table" rules="all" >
                        <thead>
                        <tr>
                        <th colspan="4"> <font size="4">Shipment Performance</font></th>
                        </tr>
                        <tr>
                        <th>Particulars</th><th>No of PO</th><th>PO Qnty</th><th> %</th>
                        </tr>
                        </thead>
                        <tr bgcolor="#E9F3FF">
                        <td>On Time Shipment</td><td><? echo $number_of_order['ontime']; ?></td><td align="right"><? echo fn_number_format($po_qnty['ontime'],0); ?></td><td align="right"><? echo fn_number_format(((100*$po_qnty['ontime'])/$order_qnty_pcs_tot),2); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        <td> Delivery After Shipment Date</td><td><? echo $number_of_order['after']; ?></td><td align="right"><? echo fn_number_format($po_qnty['after'],0); ?></td><td align="right"><? echo fn_number_format(((100*$po_qnty['after'])/$order_qnty_pcs_tot),2); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        <td>Yet To Shipment </td><td><? echo $number_of_order['yet']; ?></td><td align="right"><? echo fn_number_format($po_qnty['yet'],0); ?></td><td align="right"><? echo fn_number_format(((100*$po_qnty['yet'])/$order_qnty_pcs_tot),2); ?></td>
                        </tr>
                        
                        <tr bgcolor="#E9F3FF">
                        <td> </td><td></td><td align="right"><? echo fn_number_format($po_qnty['yet']+$po_qnty['ontime']+$po_qnty['after'],0); ?></td><td align="right"><? echo fn_number_format(((100*$po_qnty['yet'])/$order_qnty_pcs_tot)+((100*$po_qnty['after'])/$order_qnty_pcs_tot)+((100*$po_qnty['ontime'])/$order_qnty_pcs_tot),2); ?></td>
                        </tr>
                        </table>
                    </fieldset>
                </div>
            </div>
        </div>
	</div>
      <?
      $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename)
      {
      @unlink($filename);
    }
    //---------end------------//
    
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');    
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename"; 
    exit();
}

if($action=="report_generate_3") // show 2 button
{
      //echo "helal****j";die;
       $process = array( &$_POST );

      //print_r($process);die;
      extract(check_magic_quote_gpc( $process ));
      $cbo_company_name=str_replace("'","",$cbo_company_name);
      $cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
      $cbo_team_name=str_replace("'","",$cbo_team_name);
      $cbo_team_member=str_replace("'","",$cbo_team_member);
      $cbo_category_by=str_replace("'","",$cbo_category_by);
      $txt_date_from=str_replace("'","",$txt_date_from);
      $txt_date_to=str_replace("'","",$txt_date_to);
      
      $txt_job_no=str_replace("'","",$txt_job_no);
      $txt_job_id=str_replace("'","",$txt_job_id);
      $txt_order_no=str_replace("'","",$txt_order_no);
      $txt_order_id=str_replace("'","",$txt_order_id);
      $cbo_order_status=str_replace("'","",$cbo_order_status);
            
      
      if($cbo_order_status>0){$order_status_con=" and b.is_confirmed=".$cbo_order_status;}else{$order_status_con="";}
      
      if($txt_job_id!=''){$job_con=" and a.id=$txt_job_id";}
      else if($txt_job_no!=''){$job_con=" and a.job_no like('%$txt_job_no')";}
      
      if($txt_order_id!=''){$po_con=" and b.id=$txt_order_id";}
      else if($txt_order_no!=''){$po_con=" and b.po_number like('%$txt_order_no%')";}
      
      //echo $job_con;die;
      
      if($cbo_company_name==0){$company_cond="";}else{$company_cond= " and a.company_name=$cbo_company_name";}
      if($cbo_buyer_name==0)
      {
            if ($_SESSION['logic_erp']["data_level_secured"]==1)
            {
                  if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
            }
            else
            {
                  $buyer_cond="";
            }
      }
      else
      {
            $buyer_cond=" and a.buyer_name=$cbo_buyer_name";
      }
      
      if($txt_date_from!="" && $txt_date_to!="")
      {
            if($db_type==0)
            {
                  $start_date=change_date_format($txt_date_from,"yyyy-mm-dd","");
                  $end_date=change_date_format($txt_date_to,"yyyy-mm-dd","");
            }
            else if($db_type==2)
            {
                  $start_date=change_date_format($txt_date_from,"","",1);
                  $end_date=change_date_format($txt_date_to,"","",1);
            }
      }


      if($cbo_category_by==1)
      {
            if ($start_date!="" && $end_date!="")
            {
                  $date_cond="and b.pub_shipment_date between '$start_date' and  '$end_date'";
            }
            else  
            {
                  $date_cond="";
            }
      }
      else if($cbo_category_by==2)
      {
            if ($start_date!="" && $end_date!="")
            {
                  $date_cond=" and b.po_received_date between '$start_date' and  '$end_date'";
            }
            else  
            {
                  $date_cond="";
            }
      }
      else 
      {
            if ($start_date!="" && $end_date!="")
            {
                  $date_cond=" and b.shipment_date between '$start_date' and  '$end_date'";
            }
            else  
            {
                  $date_cond="";
            }
      }


      if($cbo_team_name==0){$team_cond="";}else{$team_cond= " a.team_leader = $cbo_team_name";}
      if($cbo_team_member==0){$dealing_marchant_cond="";}else{$dealing_marchant_cond=" and a.dealing_marchant = $cbo_team_member";}
      
      
      ob_start();
      ?>
      
           
            <div width="2475"  align="left" >
                <table width="2472" id="table_header_1" border="1" class="rpt_table" rules="all">
                    <thead>
                        <tr>
                            <th style="word-break: break-all;" width="50" ><p>SL</p></th>
                            <th style="word-break: break-all;" width="50" ><p>File No</p></th>
                            <th style="word-break: break-all;" width="65"><p>Job No</p></th>
                            <th style="word-break: break-all;" width="55"><p>Year</p></th>
                            <th style="word-break: break-all;"  width="75"><p>Buyer</p></th>
                            <th style="word-break: break-all;"  width="75"><p>Ref No</p></th>
                            <th style="word-break: break-all;" width="162"><p>Img</p></th>
                            <th style="word-break: break-all;" width="100"><p>Style Ref</p></th>
                            <th style="word-break: break-all;" width="170"><p>Item</p></th>
                            <th style="word-break: break-all;" width="190"><p>Fab. Description</p></th>
                            <th style="word-break: break-all;"  width="100"><p>PO No</p></th>
                            <th style="word-break: break-all;" width="65"><p>Original<br>Ship Date</p></th>
                            <th style="word-break: break-all;" width="75"><p>Order Qnty</p></th>
                            <th style="word-break: break-all;" width="65"><p>Uom</p></th>
                            <th style="word-break: break-all;" width="75"><p>Order Qnty(Pcs)</p></th>
                            <th style="word-break: break-all;"  width="70"><p>Unit Price</p></th>
                            <th style="word-break: break-all;" width="85"><p>Order Value</p></th>
                            <th style="word-break: break-all;" width="75"><p>Ex-Fac Qnty</p> </th>
                            <th style="word-break: break-all;"  width="75"><p>Short/Access Qnty</p></th>
                            <th style="word-break: break-all;" width="75"><p>Yarn Req</p></th>

                            <!-- New six Colmun add==================================== -->
                            <th style="word-break: break-all;" width="75"><p>Knit Qty</p></th>
                            <th style="word-break: break-all;" width="75"><p>Dyeing Qty</p></th>
                            <th style="word-break: break-all;" width="75"><p>Fin Fab Prod</p></th>
                            <th style="word-break: break-all;" width="75"><p>Fin Fab Issue</p></th>
                            <th style="word-break: break-all;" width="75"><p>Cut Qty</p></th>
                            <th style="word-break: break-all;" width="75"><p>Sewing Qty</p></th>


                            <th style="word-break: break-all;" width="100" ><p>Shipping Status</p></th>
                            <th style="word-break: break-all;" width="70"><p>Po Id</p></th>
                            <th style="word-break: break-all;" ><p>Remarks</p></th>
                        </tr>
                    </thead>
                </table>
            </div> 
                <div style=" max-height:400px; overflow-y:scroll; width: 2475px;"  align="left" id="scroll_body">
                    <table width="2472" border="1" class="rpt_table" rules="all" id="table_body">
                    
                        <tbody>
                                <?
                                //ob_start();
                                $i=1;
                                $order_qnty_pcs_tot=0;
                                $order_qntytot=0;
                                $oreder_value_tot=0;
                                $total_ex_factory_qnty=0;
                                $total_short_access_qnty=0;
                                $total_short_access_value=0;
                                $yarn_req_for_po_total=0;
                              
                              $lc_number_arr=return_library_array( "select a.wo_po_break_down_id, b.export_lc_no from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id",'wo_po_break_down_id','export_lc_no');

                               $data_array_group=sql_select("select b.grouping  as grouping   from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id where  a.job_no=b.job_no_mst   and a.company_name like '$company_name' $buyer_id_cond $order_status_con and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond  $job_con $po_con and a.status_active=1 and b.status_active=1 group by b.grouping");
                                
                              $gorder_qnty_pcs_tot=0;
                              $gorder_qntytot=0;
                              $goreder_value_tot=0;
                              $gtotal_ex_factory_qnty=0;
                              $gtotal_short_access_qnty=0;
                              $gtotal_short_access_value=0;
                              $gyarn_req_for_po_total=0;
                              ?>
                   
                              <? 
                              
                              if($db_type==0)
                              {
                                    
                                    $sql="SELECT a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category,  a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, b.id, b.is_confirmed, b.po_number, b.po_quantity, b.pub_shipment_date,b.shipment_date, b.po_received_date,b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, sum(c.ex_factory_qnty) as ex_factory_qnty,b.file_no,YEAR(a.insert_date)  as year,b.grouping,d.po_breakdown_id, d.qnty
                                    from wo_po_details_master a, 
                                    pro_roll_details d,
                                    wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 
                                   
                                    where  a.job_no=b.job_no_mst
                                    and d.po_breakdown_id=b.id 
                                    and a.company_name =$cbo_company_name
                                    $buyer_id_cond $order_status_con 
                                    $team_cond  
                                    $dealing_marchant_cond  
                                    $date_cond $pocond $job_con $po_con $buyer_cond
                                    and a.status_active=1 and b.status_active=1  
                                    group by  a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category,  a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, b.id, b.is_confirmed, b.po_number, b.po_quantity, b.pub_shipment_date, b.shipment_date, b.po_received_date, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status,b.file_no,a.insert_date,b.grouping,d.po_breakdown_id, d.qnty
                                    order by b.pub_shipment_date,a.job_no_prefix_num,b.id";
                              }
                              else if($db_type==2)
                              {
                                    $date=date('d-m-Y');
                                   
                                    $sql="SELECT a.id as job_id,a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category,  a.gmts_item_id,a.order_uom, a.team_leader, a.dealing_marchant, b.id, b.is_confirmed, b.po_number,a.total_set_qnty,b.po_quantity, b.pub_shipment_date, b.shipment_date, b.po_received_date, (b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_1, (b.shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_2, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, sum(c.ex_factory_qnty) as ex_factory_qnty,a.set_smv,b.file_no ,to_char(a.insert_date,'YYYY') as year,b.grouping 
                                    from wo_po_details_master a,
                                    wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 
                                    where  a.job_no=b.job_no_mst
                                    and a.company_name =$cbo_company_name   
                                        $buyer_id_cond $order_status_con 
                                        $team_cond  
                                        $dealing_marchant_cond  
                                        $date_cond $pocond $job_con $po_con $buyer_cond
                                        and a.status_active=1 
                                        and b.status_active=1 
                                    group by a.id,a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category,  a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, b.id, b.is_confirmed, b.po_number, b.po_quantity, b.pub_shipment_date, b.shipment_date, b.po_received_date,b.unit_price,b.po_total_price, b.details_remarks, b.shiping_status,a.set_smv,b.file_no,a.insert_date,b.grouping
                                    order by a.id,b.pub_shipment_date,a.job_no_prefix_num,b.id";
                                   
                                    
                              }
                              //echo $sql;//die;//pro_qc_result_mst pro_grey_prod_entry_dtls
                              $data_array=sql_select($sql);
                              $job_span=array();
                              $job_arr=array();
                              foreach ($data_array as $row) {
                                 $job_span[$row[csf('job_no')]]++;
                                 array_push($job_arr, $row[csf('job_no')]);
                              }
                              $job_cond=where_con_using_array(array_unique(array_filter($job_arr)),1,"job_no");
                              $job_cond2=where_con_using_array(array_unique(array_filter($job_arr)),1,"b.job_no_mst");
                              $fabric_sql=sql_select("SELECT fabric_description,job_no FROM wo_pre_cost_fabric_cost_dtls WHERE status_active=1 and body_part_type in (1,20) $job_cond");

                             // echo $job_cond."test";

                              $fabric_data=array();

                              foreach ($fabric_sql as $row) 
                              {
                                   $fabric_data[$row[csf('job_no')]].=$row[csf('fabric_description')]."***";
                              }

                              
                              $data_array_costing_per=return_library_array( "select job_no,costing_per from wo_pre_cost_mst where status_active=1 and is_deleted=0 $job_cond", "job_no", "costing_per"  );

                              $data_array_knit_qnty=return_library_array( "SELECT a.po_breakdown_id, sum(a.qnty) as qnty from pro_roll_details a,wo_po_break_down b where a.po_breakdown_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $job_cond2 group by a.po_breakdown_id", "po_breakdown_id", "qnty" );

                              $data_array_dying_qnty=return_library_array( "SELECT a.po_breakdown_id, sum(c.production_qty) as dyeing_qnty from pro_roll_details a,wo_po_break_down b,pro_fab_subprocess_dtls c,pro_fab_subprocess d where a.po_breakdown_id=b.id and a.barcode_no=c.barcode_no and c.mst_id=d.id and c.load_unload_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $job_cond2 group by a.po_breakdown_id", "po_breakdown_id", "dyeing_qnty" );

                              $data_array_finish_feb_qnty=return_library_array( "SELECT a.po_breakdown_id,sum(a.quantity)  as finish_quantity
                              from order_wise_pro_details a,
                              wo_po_break_down b,
                              inv_receive_master c,
                              pro_finish_fabric_rcv_dtls d
                               where a.po_breakdown_id=b.id 
                               and a.dtls_id=d.id and c.id=d.mst_id
                               and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
                                $job_cond2 
                                group by a.po_breakdown_id", "po_breakdown_id", "finish_quantity" );

                                $data_array_finish_feb_issue=return_library_array( "SELECT a.po_breakdown_id,sum(a.quantity)  as production_quantity
                              from order_wise_pro_details a,
                              wo_po_break_down b,
                              inv_receive_master c,
                              pro_finish_fabric_rcv_dtls d
                               where a.po_breakdown_id=b.id 
                               and a.dtls_id=d.id and c.id=d.mst_id
                               and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
                                $job_cond2 
                                group by a.po_breakdown_id", "po_breakdown_id", "production_quantity");
                                $data_array_cut_qnty=return_library_array("SELECT a.id,SUM(c.marker_qty) as cut_qty,a.order_id 
                                from ppl_cut_lay_bundle  a,wo_po_break_down b,ppl_cut_lay_size_dtls c
                                where a.mst_id=c.mst_id and a.order_id=b.id and a.dtls_id=c.dtls_id and a.size_id=c.size_id 
                                and c.status_active=1 
                                and c.is_deleted=0 
                                and a.status_active=1 
                                and a.is_deleted=0
                                and b.status_active=1 
                                and b.is_deleted=0  
                                $job_cond2 GROUP by a.id,a.order_id","order_id","cut_qty");

                                $data_array_swing_qnty=return_library_array("SELECT SUM(a.production_qnty) as swing_qty,b.po_break_down_id from  pro_garments_production_dtls a, wo_po_color_size_breakdown b 
                                where a.production_type=4 and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
                                $job_cond2 GROUP by b.po_break_down_id","po_break_down_id","swing_qty");

                                $data_array_yarn_cons=return_library_array( "select job_no,yarn_cons_qnty from wo_pre_cost_sum_dtls where status_active=1 and is_deleted=0 $job_cond", "job_no", "yarn_cons_qnty"  );

                              $master_tble_id_cond=where_con_using_array(array_unique(array_filter($job_arr)),1,"master_tble_id");
                              $sql_img= "select master_tble_id,image_location from common_photo_library where form_name='knit_order_entry' and is_deleted=0 $master_tble_id_cond";
                              $res_img=sql_select($sql_img);
                              $job_wise_image=array();
                              foreach ($res_img as $row) {
                                    $job_wise_image[$row[csf('master_tble_id')]].=$row[csf('image_location')]."***";
                              }
                              
                              // echo "<pre>";
                              // print_r($data_array); pro_garments_production_dtls
                              $pre_job='';
                              $sl=1;
                              $job_wise_cnt=array();
                              $job_sum=array();
                              $tot_sum=array();
                              foreach ($data_array as $row)
                              { 
                                    if ($i%2==0)  
                                    $bgcolor="#E9F3FF";
                                    else
                                    $bgcolor="#FFFFFF";    
                                    $cons=0;
                                    $costing_per_pcs=0;
                                    $yarn_cons_qnty=$data_array_yarn_cons[$row[csf('job_no')]];
                                    $costing_per=$data_array_costing_per[$row[csf('job_no')]];
                                    if($costing_per==1)
                                    {
                                      $costing_per_pcs=1*12;      
                                    }
                                    else if($costing_per==2)
                                    {
                                      $costing_per_pcs=1*1;  
                                    }
                                    else if($costing_per==3)
                                    {
                                      $costing_per_pcs=2*12; 
                                    }
                                    else if($costing_per==4)
                                    {
                                      $costing_per_pcs=3*12; 
                                    }
                                    else if($costing_per==5)
                                    {
                                      $costing_per_pcs=4*12; 
                                    }
                                    $yarn_req_for_po=0;
                                    $cons=$yarn_cons_qnty;
                                    $yarn_req_for_po=($yarn_cons_qnty/ $costing_per_pcs)*$row[csf('po_quantity')];
                                    $template_id=$template_id_arr[$row[csf('id')]];
                                    $job_wise_cnt[$row[csf('job_no')]]++;
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                                          <?php if ( $pre_job!=$row[csf('job_no')]): ?>
                                                
                                                <td rowspan="<?=$job_span[$row[csf('job_no')]]?>" style="word-break: break-all;" width="50"  align="center" bgcolor="<? echo $color; ?>"> <p><? echo $sl++; ?></p> </td>
                                                <td rowspan="<?=$job_span[$row[csf('job_no')]]?>" style="word-break: break-all;" width="50" align="center"><p><? echo $row[csf('file_no')];?></p></td>
                                                <td rowspan="<?=$job_span[$row[csf('job_no')]]?>" style="word-break: break-all;"  width="65" align="center"><p><? echo $row[csf('job_no')];?></p></td>
                                                <td rowspan="<?=$job_span[$row[csf('job_no')]]?>" style="word-break: break-all;" width="55"  align="center"><p><? echo $row[csf('year')];?></p></td>
                                                <td rowspan="<?=$job_span[$row[csf('job_no')]]?>" style="word-break: break-all;"  width="75"   align="center"><p><? echo $buyer_short_name_arr[$row[csf('buyer_name')]];?></p></td>
                                                <td rowspan="<?=$job_span[$row[csf('job_no')]]?>" style="word-break: break-all;" width="75" align="center"><p><? echo $row[csf('grouping')];?></p></td>
                                                <td rowspan="<?=$job_span[$row[csf('job_no')]]?>" style="word-break: break-all;" width="162">
                                                   <p>
                                                      <?php
                                                            $images=array_unique(array_filter(explode("***", $job_wise_image[$row[csf('job_no')]])));
                                                            foreach ($images as $key => $image) 
                                                            {
                                                                  ?>
                                                                  <img  src="../../../<? echo $image; ?>" height='120' width='100%' />
                                                                  <br>
                                                                  <?
                                                            }
                                                      ?>
                                                      </p>
                                                </td>
                                                <td rowspan="<?=$job_span[$row[csf('job_no')]]?>"  style="word-break: break-all;" width="100" align="center"><p><? echo $row[csf('style_ref_no')];?></p></td>
                                                <td rowspan="<?=$job_span[$row[csf('job_no')]]?>" style="word-break: break-all;" width="170" align="center">
                                                      <p>
                                                      <?
                                                            $gmts_item_id=explode(',',$row[csf('gmts_item_id')]);
                                                            for($j=0; $j<=count($gmts_item_id); $j++)
                                                            {
                                                            echo $garments_item[$gmts_item_id[$j]];
                                                            }
                                                      ?>
                                                     
                                                      </p>
                                                </td>
                                                <td rowspan="<?=$job_span[$row[csf('job_no')]]?>" style="word-break: break-all;" width='190'><p><?php echo implode(",", array_unique(array_filter(explode("***", $fabric_data[$row[csf('job_no')]])))) ?></p></td>
                                          <?php endif ?>
                                          <td style="word-break: break-all;" width="100"  align="center"><p><? echo $row[csf('po_number')];?></p></td>

                                          <td style="word-break: break-all;" width="65"  align="center"><p><? echo change_date_format($row[csf('shipment_date')],'dd-mm-yyyy','-');?></p></td>
                  
                                          <td style="word-break: break-all;" width="75" align="right">
                                               <p> <? 
                                                      echo fn_number_format( $row[csf('po_quantity')],0);
                                                     
                                                      $job_sum[$row[csf('job_no')]]['po_quantity']+=$row[csf('po_quantity')];
                                                      $tot_sum['po_quantity']+=$row[csf('po_quantity')];
                                                ?></p>
                                          </td>
                                          <td style="word-break: break-all;" width="65"  align="center"><p><? echo $unit_of_measurement[$row[csf('order_uom')]];?></p></td>
                                          <td style="word-break: break-all;" width="75"  align="right">
                                               <p> <? 
                                                      echo fn_number_format(($row[csf('po_quantity')]*$row[csf('total_set_qnty')]),0);  
                                                      

                                                      $job_sum[$row[csf('job_no')]]['po_quantity_pc']+=($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
                                                      $tot_sum['po_quantity_pc']+=($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
                                                ?></p>
                                          </td>
                                          <td style="word-break: break-all;" width="70"   align="right"><? echo fn_number_format($row[csf('unit_price')],2);?></td>
                                          <td style="word-break: break-all;" width="85"  align="right">
                                               <p> <? 
                                                      echo fn_number_format($row[csf('po_total_price')],2);
                                                     

                                                       $job_sum[$row[csf('job_no')]]['po_total_price']+=$row[csf('po_total_price')];
                                                      $tot_sum['po_total_price']+=$row[csf('po_total_price')];
                                                ?></p>
                                                      
                                          </td>

                                          
                                          <td style="word-break: break-all;" width="75"  align="right">
                                               <p> <? 
                                                $ex_factory_qnty=$row[csf('ex_factory_qnty')]; 
                                                echo  fn_number_format( $ex_factory_qnty,0); 

                                                 $job_sum[$row[csf('job_no')]]['ex_factory_qnty']+=$row[csf('ex_factory_qnty')];
                                                $tot_sum['ex_factory_qnty']+=$row[csf('ex_factory_qnty')];
                                               
                                                ?> </p>
                                          </td>
                                          <td style="word-break: break-all;" width="75"   align="right">
                                               <p> <? 
                                                $short_access_qnty=($row[csf('po_quantity')]*$row[csf('total_set_qnty')]-$ex_factory_qnty); 
                                                echo fn_number_format($short_access_qnty,0);

                                                 $job_sum[$row[csf('job_no')]]['short_access_qnty']+=$short_access_qnty;
                                                $tot_sum['short_access_qnty']+=$short_access_qnty;
                                               
                                                ?></p>
                                          </td>
                                          <td style="word-break: break-all;" width="75"  align="right" title="<? echo "Cons:".$cons."Costing per:".$costing_per[csf('costing_per')];?>">
                                               <p> <? 
                                                echo fn_number_format($yarn_req_for_po,2);
                                                $job_sum[$row[csf('job_no')]]['yarn_req_for_po']+=$yarn_req_for_po;
                                                $tot_sum['yarn_req_for_po']+=$yarn_req_for_po;
                                                ?></p>
                                          </td>
                                          <!-- New six Colmun add================================= -->
                                          <td style="word-break: break-all;" width="75"  align="right" title=""><p><? echo fn_number_format($data_array_knit_qnty[$row[csf('id')]],2);
                                          $job_sum[$row[csf('job_no')]]['qnty']+=$data_array_knit_qnty[$row[csf('id')]];
                                                      $tot_sum['qnty']+=$data_array_knit_qnty[$row[csf('id')]];
                                          ?></p></td>
                                          <td style="word-break: break-all;" width="75"  align="right" title=""><p><? echo fn_number_format($data_array_dying_qnty[$row[csf('id')]],2);
                                          $job_sum[$row[csf('job_no')]]['dyeing_qnty']+=$data_array_dying_qnty[$row[csf('id')]];
                                                      $tot_sum['dyeing_qnty']+=$data_array_dying_qnty[$row[csf('id')]];
                                          ?></p></td>
                                          <td style="word-break: break-all;" width="75"  align="right" title=""><p><? 
                                          echo fn_number_format($data_array_finish_feb_qnty[$row[csf('id')]],2);
                                          $job_sum[$row[csf('job_no')]]['finish_quantity']+=$data_array_finish_feb_qnty[$row[csf('id')]];
                                          $tot_sum['finish_quantity']+=$data_array_finish_feb_qnty[$row[csf('id')]];
                                          ?></p></td>
                                          <td style="word-break: break-all;" width="75"  align="right" title=""><p><? 
                                                      echo fn_number_format($data_array_finish_feb_issue[$row[csf('id')]],2);

                                                       $job_sum[$row[csf('job_no')]]['production_quantity']+=$data_array_finish_feb_issue[$row[csf('id')]];
                                                      $tot_sum['production_quantity']+=$data_array_finish_feb_issue[$row[csf('id')]];
                                                ?></p></td>
                                          <td style="word-break: break-all;" width="75"  align="right" title=""><p><?
                                          echo fn_number_format($data_array_cut_qnty[$row[csf('id')]],2);
                                           $job_sum[$row[csf('job_no')]]['cut_qnty']+=$data_array_cut_qnty[$row[csf('id')]];
                                           $tot_sum['cut_qnty']+=$data_array_cut_qnty[$row[csf('id')]];
                                          ?></p></td>
                                          <td style="word-break: break-all;" width="75"  align="right" title=""><p><?
                                          
                                          echo fn_number_format($data_array_swing_qnty[$row[csf('id')]],2);
                                           $job_sum[$row[csf('job_no')]]['swing_qty']+=$data_array_swing_qnty[$row[csf('id')]];
                                           $tot_sum['swing_qty']+=$data_array_swing_qnty[$row[csf('id')]];
                                           ?>
                                          </p></td>
                                          <td style="word-break: break-all;"  width="100" align="center"><p><? echo $shipment_status[$row[csf('shiping_status')]]; ?></p></td>

                                          <td style="word-break: break-all;" width="70" ><p><? echo $row[csf('id')]; ?></p></td>
                                          <td style="word-break: break-all;" ><p><? echo $row[csf('details_remarks')]; ?></p></td>
                                    </tr>
                                    <?
                                    $i++;

                                     $pre_job=$row[csf('job_no')];

                                     if($job_wise_cnt[$row[csf('job_no')]]==$job_span[$row[csf('job_no')]])
                                     {
                                          ?>
                                          <tr bgcolor="#CCCCCC">
                                                <td colspan="14" width="1297" align="right">Style Total:</td>
                                                <td style="word-break: break-all;" width="75"  align="right">
                                                     <p> <? 
                                                            echo fn_number_format( $job_sum[$row[csf('job_no')]]['po_quantity_pc'],0);  
                                                           
                                                      ?></p>
                                                </td>
                                                <td style="word-break: break-all;" width="70"   align="right"></td>
                                                <td style="word-break: break-all;" width="85"  align="right">
                                                     <p> <? 
                                                            echo fn_number_format($job_sum[$row[csf('job_no')]]['po_total_price'],2);
                                                      ?></p> 
                                                </td>
                                                <td style="word-break: break-all;" width="75"  align="right">
                                                     <p> <? 
                                                      echo  fn_number_format( $job_sum[$row[csf('job_no')]]['ex_factory_qnty'],0); 
                                                      ?> </p>
                                                </td>
                                                <td style="word-break: break-all;" width="75"   align="right">
                                                     <p> <? 
                                                      echo fn_number_format($job_sum[$row[csf('job_no')]]['short_access_qnty'],0);
                                                      ?></p>
                                                </td>
                                                <td style="word-break: break-all;" width="75"  align="right" >
                                                     <p> <? 
                                                      echo fn_number_format($job_sum[$row[csf('job_no')]]['yarn_req_for_po'] ,2);
                                                      
                                                      ?></p>
                                                      
                                                </td>
                                                
                                          <!-- New six Colmun add==================================== -->
                                          <td style="word-break: break-all;" width="75"  align="right" title=""><p>
                                              <? 
                                                      echo fn_number_format($job_sum[$row[csf('job_no')]]['qnty'] ,2);
                                                      
                                                      ?>
                                              
                                          </p></td>
                                          <td style="word-break: break-all;" width="75"  align="right" title=""><p> <? 
                                                      echo fn_number_format($job_sum[$row[csf('job_no')]]['dyeing_qnty'] ,2);
                                                      ?></p></td>
                                          <td style="word-break: break-all;" width="75"  align="right" title=""><p><? 
                                                      echo fn_number_format($job_sum[$row[csf('job_no')]]['finish_quantity'],2);
                                                      ?></p></td>
                                          <td style="word-break: break-all;" width="75"  align="right" title=""><p><? 
                                                      echo fn_number_format($job_sum[$row[csf('job_no')]]['po_qnty_in_pcs'] ,2);
                                                      ?></p></td>
                                          <td style="word-break: break-all;" width="75"  align="right" title=""><p><?
                                          echo fn_number_format( $job_sum[$row[csf('job_no')]]['cut_qnty'] ,2);
                                          ?>
                                          </p></td>
                                          <td style="word-break: break-all;" width="75"  align="right" title=""><p><? 
                                          echo fn_number_format($job_sum[$row[csf('job_no')]]['swing_qty'],2);
                                          ?></p></td>
                                                
                                                <td style="word-break: break-all;"  width="100" align="center"></td>

                                                <td style="word-break: break-all;" width="70" ></td>
                                                <td style="word-break: break-all;" ></td>
                                          </tr>
                                          <?
                                     }
                              }
                              ?>
                              <tr bgcolor="#FFF">
                                    <td colspan="14" width="1297" align="right">Grand Total :</td>
                                    <td style="word-break: break-all;" width="75"  align="right">
                                         <p> <? echo fn_number_format( $tot_sum['po_quantity_pc'],0);
                                          ?></p>
                                    </td>
                                    <td style="word-break: break-all;" width="70"   align="right"></td>
                                    <td style="word-break: break-all;" width="85"  align="right">
                                         <p> <? echo fn_number_format($tot_sum['po_total_price'],2);
                                          ?></p>    
                                    </td>
                                    <td style="word-break: break-all;" width="75"  align="right">
                                         <p> <? echo  fn_number_format( $tot_sum['ex_factory_qnty'],0);?></p>
                                    </td>
                                    <td style="word-break: break-all;" width="75"   align="right">
                                         <p> <? echo fn_number_format($tot_sum['short_access_qnty'],0);?></p>
                                    </td>
                                    <td style="word-break: break-all;" width="75"  align="right" >
                                         <p> <? echo fn_number_format($tot_sum['yarn_req_for_po'] ,2);
                                          ?></p>
                                    </td>
                                    <!-- New six Colmun add==================================== -->
                                          <td style="word-break: break-all;" width="75"  align="right" title=""><p>
                                          <? echo fn_number_format($tot_sum['qnty'] ,2);?>
                                          </p></td>
                                          <td style="word-break: break-all;" width="75"  align="right" title=""><p><? echo fn_number_format($tot_sum['dyeing_qnty'],2);
                                          ?></p></td>
                                          <td style="word-break: break-all;" width="75"  align="right" title=""><p><? echo fn_number_format($tot_sum['finish_quantity'],2);?></p></td>
                                          <td style="word-break: break-all;" width="75"  align="right" title=""><p><? 
                                          echo fn_number_format($tot_sum['production_quantity'],2);
                                          ?></p></td>
                                          <td style="word-break: break-all;" width="75"  align="right" title=""><p><? echo fn_number_format($tot_sum['cut_qnty'],2);?></p></td>
                                          <td style="word-break: break-all;" width="75"  align="right" title=""><p><? 
                                          echo fn_number_format($tot_sum['swing_qty'],2);
                                          ?></p></td>
                                    
                                    <td style="word-break: break-all;"  width="100" align="center"></td>
                                    <td style="word-break: break-all;" width="70" ></td>
                                    <td style="word-break: break-all;" ></td>
                              </tr>
                         </tbody>
                  </table>
            </div> 
      <?
      //$html = ob_get_contents();
      $html=ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename)
      {
      @unlink($filename);
    }
    //---------end------------//
    
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');    
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename"; 
    exit();
}

if($action=="report_generate_2")
{
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_team_name=str_replace("'","",$cbo_team_name);
	$cbo_team_member=str_replace("'","",$cbo_team_member);
	$cbo_category_by=str_replace("'","",$cbo_category_by);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_id=str_replace("'","",$txt_job_id);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$cbo_order_status=str_replace("'","",$cbo_order_status);
		
	
	if($cbo_order_status>0){$order_status_con=" and b.is_confirmed=".$cbo_order_status;}else{$order_status_con="";}
	
	if($txt_job_id!=''){$job_con=" and a.id=$txt_job_id";}
	else if($txt_job_no!=''){$job_con=" and a.job_no like('%$txt_job_no')";}
	
	if($txt_order_id!=''){$po_con=" and b.id=$txt_order_id";}
	else if($txt_order_no!=''){$po_con=" and b.po_number like('%$txt_order_no%')";}
	
	//echo $job_con;die;
	
	if($cbo_company_name==0){$company_cond="";}else{$company_cond= " and a.company_name=$cbo_company_name";}
	if($cbo_buyer_name==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_cond="";
		}
	}
	else
	{
		$buyer_cond=" and a.buyer_name=$cbo_buyer_name";
	}
	
	if($txt_date_from!="" && $txt_date_to!="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format($txt_date_from,"yyyy-mm-dd","");
			$end_date=change_date_format($txt_date_to,"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$start_date=change_date_format($txt_date_from,"","",1);
			$end_date=change_date_format($txt_date_to,"","",1);
		}
	}


	if($cbo_category_by==1)
	{
		if ($start_date!="" && $end_date!="")
		{
			$date_cond="and b.pub_shipment_date between '$start_date' and  '$end_date'";
		}
		else	
		{
			$date_cond="";
		}
	}
	else if($cbo_category_by==2)
	{
		if ($start_date!="" && $end_date!="")
		{
			$date_cond=" and b.po_received_date between '$start_date' and  '$end_date'";
		}
		else	
		{
			$date_cond="";
		}
	}
	else 
	{
		if ($start_date!="" && $end_date!="")
		{
			$date_cond=" and b.shipment_date between '$start_date' and  '$end_date'";
		}
		else	
		{
			$date_cond="";
		}
	}


	if($cbo_team_name==0){$team_cond="";}else{$team_cond= " a.team_leader = $cbo_team_name";}
	if($cbo_team_member==0){$dealing_marchant_cond="";}else{$dealing_marchant_cond=" and a.dealing_marchant = $cbo_team_member";}
	
	
	
      //Order.........................................	
      $sql="SELECT a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.order_uom,b.file_no,b.po_number,b.shiping_status,b.id,b.po_quantity,a.total_set_qnty,b.is_confirmed,b.po_total_price,b.shipment_date,b.pub_shipment_date,b.po_received_date,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $company_cond $buyer_cond $team_cond $dealing_marchant_cond $date_cond $job_con $po_con $order_status_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.buyer_name,a.job_no,b.po_number";   
      // echo $sql; die;
      $data_array=sql_select($sql);
      foreach ($data_array as $row)
      { 
		$row[csf('file_no')]=($row[csf('file_no')]=='')?'No File':$row[csf('file_no')];
		$data_arr[$row[csf('file_no')]][]=array(
			job_no=>$row[csf('job_no')],
			po_id=>$row[csf('id')],
			po_number=>$row[csf('po_number')],
			company_name=>$row[csf('company_name')],
			buyer_name=>$row[csf('buyer_name')],
			style_ref_no=>$row[csf('style_ref_no')],
			shipment_date=>$row[csf('shipment_date')],
			pub_shipment_date=>$row[csf('pub_shipment_date')],
			po_received_date=>$row[csf('po_received_date')],
			po_quantity=>$row[csf('po_quantity')],
			order_uom=>$row[csf('order_uom')],
			shiping_status=>$row[csf('shiping_status')],
			is_confirmed=>$row[csf('is_confirmed')]
		);
		
		$key=$row[csf('company_name')].'**'.$row[csf('buyer_name')];
		$company_buyer_data_arr[$key]=array(
			job_no=>$row[csf('job_no')],
			company_name=>$row[csf('company_name')],
			buyer_name=>$row[csf('buyer_name')]
		);
		
		$po_unite_price_arr[$row[csf('id')]]=($row[csf('po_total_price')]/$row[csf('po_quantity')])/$row[csf('total_set_qnty')];
		$po_id_arr[$row[csf('id')]]=$row[csf('id')];
		$job_no_arr[$row[csf('job_no')]]=$row[csf('job_no')];
		$company_buyer_po_wise_job_arr[$key][$row[csf('id')]]=$row[csf('job_no')];
		$company_buyer_po_qty_data_arr[$key]+=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
		$company_buyer_po_val_data_arr[$key]+=$row[csf('po_total_price')];
		$po_wise_company_buyer_arr[$row[csf('id')]]=$key;
		$po_wise_ship_date_arr[$row[csf('id')]]=$row[csf('shipment_date')];
		$po_wise_pub_ship_date_arr[$row[csf('id')]]=$row[csf('pub_shipment_date')];
		$po_wise_po_rec_date_arr[$row[csf('id')]]=$row[csf('po_received_date')];
		$po_wise_ship_status_arr[$row[csf('id')]]=$row[csf('shiping_status')];
		$po_wise_po_pcs_qty_data_arr[$row[csf('id')]]+=($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
		$po_wise_po_val_data_arr[$row[csf('id')]]+=$row[csf('po_total_price')];
		$po_buyer_data_arr[$row[csf('id')]]=$key;
		$job_buyer_data_arr[$row[csf('id')]]=$row[csf('job_no')];
      }

      //Sewing.............................
      if($db_type==2 && count($po_id_arr)>1000)
      {
      	$sql_con=" and (";
      	$chunk_arr=array_chunk($po_id_arr,999);
      	foreach($chunk_arr as $ids)
      	{
      		$sql_con.=" a.po_break_down_id in(".implode(",",$ids).") or"; 
      	}
      	$sql_con=chop($sql_con,'or');
      	$sql_con.=")";
      }
      else
      {
      	$sql_con=" and a.po_break_down_id in(".implode(",",$po_id_arr).")";
      }

      $sewing_sql="select a.po_break_down_id, sum(b.production_qnty) as production_qnty  from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_con group by a.po_break_down_id";
      $sewing_sql_data_array=sql_select($sewing_sql);
      $po_wise_sewing_qty_arr=array();
      foreach($sewing_sql_data_array as $row)
      {
      	$po_wise_sewing_qty_arr[$row[csf('po_break_down_id')]]=$row[csf('production_qnty')];
      	$key=$po_wise_company_buyer_arr[$row[csf('po_break_down_id')]];
      	$company_buyer_sewing_output_data_arr[$key]+=$row[csf("production_qnty")];
      	$company_buyer_sewing_val_data_arr[$key]+=$row[csf("production_qnty")]*$po_unite_price_arr[$row[csf('po_break_down_id')]];
      }
      unset($sewing_sql_data_array);

      //Exfactory.............................
      $poIDs=implode(",",$po_id_arr);
      if($db_type==2 && count($po_id_arr)>1000)
      {
      	$sql_con=" and (";
      	$chunk_arr=array_chunk($po_id_arr,999);
      	foreach($chunk_arr as $ids)
      	{
      		$sql_con.=" po_break_down_id in(".implode(",",$ids).") or"; 
      	}
      	$sql_con=chop($sql_con,'or');
      	$sql_con.=")";
      }
      else
      {
      	$sql_con=" and po_break_down_id in(".implode(",",$po_id_arr).")";
      }


      $ex_sql="select shiping_status, po_break_down_id,  sum(ex_factory_qnty) as ex_factory_qnty, MAX(ex_factory_date) as ex_factory_date from pro_ex_factory_mst where  status_active=1 and is_deleted=0 $sql_con GROUP BY shiping_status, po_break_down_id order by po_break_down_id,ex_factory_date"; //echo $ex_sql;die;

      //echo $ex_sql;die;

      $ex_sql_data_array=sql_select($ex_sql);
      foreach($ex_sql_data_array as $row)
      {
      	
      	$key=$po_wise_company_buyer_arr[$row[csf('po_break_down_id')]];
      	if($row[csf('shiping_status')]==3)
      	{
      		$company_buyer_full_shiped_data_arr[$key]+=$row[csf("ex_factory_qnty")];
      	}
      	if($row[csf('shiping_status')]==2)
      	{
      		$company_buyer_partial_shiped_data_arr[$key]+=$row[csf("ex_factory_qnty")];
      	}
      	
      	$po_wise_export_date_arr[$row[csf('po_break_down_id')]]=$row[csf('ex_factory_date')];
      	$po_wise_export_qty_arr[$row[csf('po_break_down_id')]]+=$row[csf('ex_factory_qnty')];
      	
      }

	//Pre cost....................................
	if($db_type==2 && count($job_no_arr)>1000)
	{
		$sql_con=" and (";
		$chunk_arr=array_chunk($job_no_arr,999);
		foreach($chunk_arr as $ids)
		{
			$sql_con.=" b.job_no in('".implode("','",$ids)."') or"; 
		}
		$sql_con=chop($sql_con,'or');
		$sql_con.=")";
	}
	else
	{
		$sql_con=" and b.job_no in('".implode("','",$job_no_arr)."')";
	}
	
	$sql="select a.costing_per,b.id,b.job_no,b.total_cost,b.cm_cost from wo_pre_cost_mst a,wo_pre_cost_dtls b where a.status_active=1 and a.is_deleted=0 $sql_con";
	foreach(sql_select($sql) as $rows)
	{
		$cm_cost_arr[$rows[csf("job_no")]]=$rows[csf("cm_cost")];
		$total_cost_arr[$rows[csf("job_no")]]=$rows[csf("total_cost")];
		$costing_per_arr[$rows[csf("job_no")]]=$rows[csf("costing_per")];
	}

    //----Shipment Performance Summary----------
	if($poIDs!="")
	{
		$cm_gmt_cost_dzn_arr=array();
		$buyer_cm_gmt_dzn_arr=array();
		$cm_gmt_cost_dzn_arr=fnc_po_wise_cm_gmt_class($cbo_company_name,$poIDs); 
	}

	foreach($po_wise_ship_date_arr as $po_id=>$po_date)
	{
		$job=$job_buyer_data_arr[$po_id];
		if($costing_per_arr[$job]==1) $dzn_qnty=12;
		else if($costing_per_arr[$job]==3) $dzn_qnty=12*2;
		else if($costing_per_arr[$job]==4) $dzn_qnty=12*3;
		else if($costing_per_arr[$job]==5) $dzn_qnty=12*4;
		else $dzn_qnty=1;
	
		$buyer_cm_gmt_cost_pcs=$cm_gmt_cost_dzn_arr[$po_id]['dzn']/$dzn_qnty;						
		$po_key=$po_buyer_data_arr[$po_id];
		//echo $cm_gmt_cost_dzn_arr[$po_id].'='.$po_key.',';
		$buyer_cm_gmt_dzn_arr[$po_key]+=$buyer_cm_gmt_cost_pcs*$po_wise_po_pcs_qty_data_arr[$po_id];
			
	     /*if($po_wise_ship_date_arr[$po_id]==1){
			$date_def=datediff('d',$po_wise_export_date_arr[$po_id],$po_date);
		}
		else if($po_wise_ship_date_arr[$po_id]==2){
			$date_def=datediff('d',$po_wise_export_date_arr[$po_id],$po_date);
		}
		else if($po_wise_ship_date_arr[$po_id]==3){
			$date_def=datediff('d',$po_wise_export_date_arr[$po_id],$po_date);
		}
	     */		
		if($cbo_category_by==1 || $cbo_category_by==2){$calculativeDate=$po_wise_pub_ship_date_arr[$po_id];}
		else{$calculativeDate=$po_wise_ship_date_arr[$po_id];}
		
		
		if($po_wise_ship_status_arr[$po_id]==1){
			$date_def=datediff('d',date('y-m-d',time()),$calculativeDate);	
		}
		else if($po_wise_ship_status_arr[$po_id]==2){
			$date_def=datediff('d',date('y-m-d',time()),$calculativeDate);	
		}
		else if($po_wise_ship_status_arr[$po_id]==3){
			$date_def=datediff('d',$po_wise_export_date_arr[$po_id],$calculativeDate);	
		}

		if($po_wise_ship_status_arr[$po_id]==3 && $date_def>=0)
		{
			$number_of_order['ontime'][$po_id]=1;
			$po_qty_arr['ontime'][$po_id]=$po_wise_po_pcs_qty_data_arr[$po_id];
		} 
		else if($po_wise_ship_status_arr[$po_id]==3 && $date_def<0)
		{
			$number_of_order['delay'][$po_id]=1;
			$po_qty_arr['delay'][$po_id]=$po_wise_po_pcs_qty_data_arr[$po_id];
		} 
		else if($po_wise_ship_status_arr[$po_id]!=3)
		{
			$number_of_order['yet_to'][$po_id]=1;
			$po_qty_arr['yet_to'][$po_id]=$po_wise_po_pcs_qty_data_arr[$po_id];
		} 
	}

     //print_r($number_of_order['ontime']);
     $company_arr=return_library_array( "select id,company_name from lib_company where id=$cbo_company_name",'id','company_name');
     // echo "<pre>" ;print_r($data_arr);die;
	 
	/*
	|--------------------------------------------------------------------------
	| getting price quotation wise cm valu
	| start
	|--------------------------------------------------------------------------
	*/
	$jobNoCondition = '';
	$noOfjobNo = count($job_no_arr);
	if($db_type == 2 && $noOfjobNo > 1000)
	{
		$jobNoCondition = " and (";
		$jobNoArr = array_chunk($job_no_arr,999);
		foreach($jobNoArr as $job)
		{
			$jobNoCondition.=" c.job_no in('".implode("','",$job)."') or";
		}
		$jobNoCondition = chop($jobNoCondition,'or');
		$jobNoCondition .= ")";
	}
	else
	{
		$jobNoCondition=" and c.job_no in('".implode("','",$job_no_arr)."')";
	}
	
	//echo $jobNoCondition; die;
	//$all_job = "'".implode("','", $jobArr)."'";
	$all_job = $all_jobs;
	$quotation_qty_sql="
		SELECT
			a.id as quotation_id, a.mkt_no, a.sew_smv, a.sew_effi_percent, a.gmts_item_id, a.company_id, a.buyer_id, a.costing_per, a.style_desc as style_desc, a.style_ref, a.order_uom,a.offer_qnty, a.total_set_qnty as ratio, a.quot_date, a.est_ship_date, b.costing_per_id, b.price_with_commn_pcs, b.total_cost, b.costing_per_id, c.job_no
		FROM
			wo_price_quotation a,
			wo_price_quotation_costing_mst b,
			wo_po_details_master c
		WHERE
			a.id=b.quotation_id and a.id=c.quotation_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.offer_qnty>0 ".$jobNoCondition."
		ORDER BY
			a.id
	";
	//and c.job_no in(".$all_job."
	//echo $quotation_qty_sql; die();
	$quotation_qty_sql_res = sql_select($quotation_qty_sql);
	$quotation_qty_array = array();
	$quotation_id_array = array();
	
	//don't use
	$all_jobs_array = array();
	//don't use
	$jobs_wise_quot_array = array();
	$quot_wise_arr = array();
	foreach ($quotation_qty_sql_res as $val)
	{
		$quotation_qty_array[$val['JOB_NO']]['QTY_PCS'] += $val['OFFER_QNTY']*$val['RATIO'];
		$quotation_qty_array[$val['JOB_NO']]['COSTING_PER_ID'] += $val['COSTING_PER_ID'];
		$quotation_id_array[$val['QUOTATION_ID']] = $val['QUOTATION_ID'];
		//$all_jobs_array[$val['JOB_NO']] = $val['JOB_NO'];
		//$jobs_wise_quot_array[$val['JOB_NO']] = $val['QUOTATION_ID'];
	
		$quot_wise_arr[$val[csf("quotation_id")]]['offer_qnty']=$val[csf("offer_qnty")];
		$quot_wise_arr[$val[csf("quotation_id")]]['costing_per_id']=$val[csf("costing_per_id")];
	
		//$style_wise_arr[$val[csf("job_no")]]['costing_per']=$val[csf("costing_per")];
		//$style_wise_arr[$val[csf("job_no")]]['gmts_item_id']=$val[csf("gmts_item_id")];
		//$style_wise_arr[$val[csf("job_no")]]['sew_effi_percent']=$val[csf("sew_effi_percent")];
		//$style_wise_arr[$val[csf("job_no")]]['shipment_date'].=$val[csf('est_ship_date')].',';
		//$style_wise_arr[$val[csf("job_no")]]['buyer_name']=$val[csf("buyer_id")];
		//$offer_qnty_pcs=$val[csf('offer_qnty')]*$val[csf('ratio')];
		
		$style_wise_arr[$val[csf("job_no")]]['quotation_id']=$val[csf("quotation_id")];
		$style_wise_arr[$val[csf("job_no")]]['sew_smv']=$val[csf("sew_smv")];
		$style_wise_arr[$val[csf("job_no")]]['qty_pcs']+=$val[csf('offer_qnty')]*$val[csf('ratio')];
		$style_wise_arr[$val[csf("job_no")]]['qty']+=$val[csf('offer_qnty')];
		$style_wise_arr[$val[csf("job_no")]]['final_cost_pcs']+=$val[csf('price_with_commn_pcs')];
		//$style_wise_arr[$val[csf("job_no")]]['total_cost']+=$offer_qnty_pcs*$val[csf('price_with_commn_pcs')];
	}
	$all_quot_id = implode(",", $quotation_id_array);
	//echo "<pre>";
	//print_r($quot_wise_arr); die;
	
	// print_r($style_wise_arr);die();
	// ===============================================================================
	$sql_fab = "
		SELECT
			a.quotation_id, sum(a.avg_cons) as cons_qnty, sum(a.amount) as amount, b.job_no
		from
			wo_pri_quo_fabric_cost_dtls a,
			wo_po_details_master b
		where
			a.quotation_id=b.quotation_id and a.quotation_id in(".$all_quot_id.") and a.fabric_source=2 and a.status_active=1 and b.status_active=1
		group by
			a.quotation_id, b.job_no
	";
	//echo $sql_fab; die();
	$data_array_fab=sql_select($sql_fab);
	$fab_summary_data = array();
	$fab_order_price_per_dzn = 1;
	foreach($data_array_fab as $row)
	{
		$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
		if($costing_per_id==1)
		{
			$fab_order_price_per_dzn=12;
		}
		else if($costing_per_id==2)
		{
			$fab_order_price_per_dzn=1;
		}
		else if($costing_per_id==3)
		{
			$fab_order_price_per_dzn=24;
		}
		else if($costing_per_id==4)
		{
			$fab_order_price_per_dzn=36;
		}
		else if($costing_per_id==5)
		{
			$fab_order_price_per_dzn=48;
		}
	
		$fab_summary_data[$row[csf("job_no")]]['fab_amount_dzn']+=$row[csf("amount")];
		$fab_summary_data[$row[csf("job_no")]]['fab_amount_total_value']+=($row[csf("amount")]/$fab_order_price_per_dzn)*$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
	}
	//echo "<pre>";
	//print_r($fab_summary_data); die;
	
	// ==================================================================================
	$sql_yarn = "
		SELECT
			a.quotation_id, sum(a.cons_qnty) as cons_qnty, sum(a.amount) as amount, b.job_no 
		from
			wo_pri_quo_fab_yarn_cost_dtls a, wo_po_details_master b 
		where
			a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 
		group by
			a.quotation_id,b.job_no
	";
	//echo $sql_yarn; die();
	$data_array_yarn=sql_select($sql_yarn);
	$yarn_summary_data = array();
	foreach($data_array_yarn as $row)
	{
		$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
		if($costing_per_id==1){$yarn_order_price_per_dzn=12;}
		else if($costing_per_id==2){$yarn_order_price_per_dzn=1;}
		else if($costing_per_id==3){$yarn_order_price_per_dzn=24;}
		else if($costing_per_id==4){$yarn_order_price_per_dzn=36;}
		else if($costing_per_id==5){$yarn_order_price_per_dzn=48;}
		//$yarn_order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
		//$yarn_summary_dzn=$yarn_summary_data[$row[csf("quotation_id")]]['yarn_amount_dzn'];
		$yarn_summary_data[$row[csf("job_no")]]['yarn_amount_dzn']+=$row[csf("amount")];
		// $summary_data['yarn_amount_dzn']+=$yarn_summary_dzn;
		//$yarn_summary_data['yarn_amount_total_value']+=($row[csf("amount")]/$yarn_order_price_per_dzn)*$yarn_order_job_qnty;
	}
	
	// ===================================================================================
	$sql_conversion = "
		SELECT
			a.id, a.quotation_id, a.cons_type, a.req_qnty, a.charge_unit, a.amount, a.status_active, b.body_part_id, b.fab_nature_id, b.color_type_id, b.construction, b.composition, c.job_no
		from
			wo_po_details_master c,
			wo_pri_quo_fab_conv_cost_dtls a left join wo_pri_quo_fabric_cost_dtls b on a.quotation_id=b.quotation_id and a.cost_head=b.id
		where
			a.quotation_id in(".$all_quot_id.") and a.quotation_id=c.quotation_id and a.status_active=1
	";
	//echo $sql_conversion; die();
	$data_array_conversion=sql_select($sql_conversion);
	$conv_order_price_per_dzn = 1;
	$conv_summary_data = array();
	$conversion_cost_arr = array();
	foreach($data_array_conversion as $row)
	{
		$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
		if($costing_per_id==1){$conv_order_price_per_dzn=12;}
		else if($costing_per_id==2){$conv_order_price_per_dzn=1;}
		else if($costing_per_id==3){$conv_order_price_per_dzn=24;}
		else if($costing_per_id==4){$conv_order_price_per_dzn=36;}
		else if($costing_per_id==5){$conv_order_price_per_dzn=48;}
		$conv_summary_data[$row[csf("job_no")]]['conv_amount_dzn']+=$row[csf("amount")];
		$conversion_cost_arr[$row[csf("job_no")]][$row[csf('cons_type')]]['conv_amount_dzn']+=$row[csf('amount')];
		$conversion_cost_arr[$row[csf("job_no")]][$row[csf('cons_type')]]['conv_amount_total_value']+=($row[csf("amount")]/$conv_order_price_per_dzn)*$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
	}
	//echo "<pre>";
	//print_r($conversion_cost_arr); die();
	
	if($db_type==0)
	{
		$sql = "SELECT MAX(a.id),a.quotation_id,a.fabric_cost,a.fabric_cost_percent,a.trims_cost,a.trims_cost_percent,a.embel_cost,a.embel_cost_percent,a.wash_cost,a.wash_cost_percent,a.comm_cost,a.comm_cost_percent,a.commission,a.commission_percent,a.lab_test,a.lab_test_percent,a.inspection,a.inspection_percent,a.cm_cost,a.cm_cost_percent,a.freight,a.freight_percent,a.currier_pre_cost,a.currier_percent ,a.certificate_pre_cost,a.certificate_percent,a.common_oh,a.common_oh_percent,a.depr_amor_pre_cost,a.depr_amor_po_price,a.interest_pre_cost,a.interest_po_price,a.income_tax_pre_cost,a.income_tax_po_price,a.total_cost ,a.total_cost_percent,a.final_cost_dzn ,a.final_cost_dzn_percent ,a.confirm_price_dzn ,a.confirm_price_dzn_percent,a.final_cost_pcs,a.margin_dzn,a.margin_dzn_percent,a.a1st_quoted_price,a.confirm_price,a.revised_price,a.price_with_commn_dzn,a.costing_per_id,a.design_pre_cost,a.design_percent,a.studio_pre_cost,a.studio_percent,a.offer_qnty,b.job_no
		from wo_price_quotation_costing_mst a,wo_po_details_master b
		where a.quotation_id in($all_quot_id) and a.status_active=1 and a.quotation_id=b.quotation_id and b.status_active=1 ";
	}
	if($db_type==2)
	{
		$sql = "SELECT MAX(a.id),a.fabric_cost,a.quotation_id,a.fabric_cost_percent,a.trims_cost,a.trims_cost_percent,a.embel_cost,a.embel_cost_percent,a.wash_cost,a.wash_cost_percent,a.comm_cost,a.comm_cost_percent,a.commission,a.commission_percent,a.lab_test,a.lab_test_percent,a.inspection,a.inspection_percent,a.cm_cost,a.cm_cost_percent,a.freight,a.freight_percent,a.currier_pre_cost,a.currier_percent ,a.certificate_pre_cost,a.certificate_percent,a.common_oh,a.common_oh_percent,a.depr_amor_pre_cost,a.depr_amor_po_price,a.interest_pre_cost,a.interest_po_price,a.income_tax_pre_cost,a.income_tax_po_price,a.total_cost ,a.total_cost_percent,a.final_cost_dzn ,a.final_cost_dzn_percent ,a.confirm_price_dzn ,a.confirm_price_dzn_percent,a.final_cost_pcs,a.margin_dzn,a.margin_dzn_percent,a.a1st_quoted_price,a.confirm_price,a.revised_price,a.price_with_commn_dzn,a.costing_per_id,a.design_pre_cost,a.design_percent,a.studio_pre_cost,a.studio_percent,b.job_no
		from wo_price_quotation_costing_mst a,wo_po_details_master b
		where a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 and b.status_active=1   group by a.fabric_cost,a.quotation_id,a.fabric_cost_percent,a.trims_cost,a.trims_cost_percent,a.embel_cost,a.embel_cost_percent,a.wash_cost,a.wash_cost_percent,a.comm_cost,a.comm_cost_percent,a.commission,a.commission_percent,a.lab_test,a.lab_test_percent,a.inspection,a.inspection_percent,a.cm_cost,a.cm_cost_percent,a.freight,a.freight_percent,a.currier_pre_cost,a.currier_percent ,a.certificate_pre_cost,a.certificate_percent,a.common_oh,a.common_oh_percent,a.depr_amor_pre_cost,a.depr_amor_po_price,a.interest_pre_cost,a.interest_po_price,a.income_tax_pre_cost,a.income_tax_po_price,a.total_cost ,a.total_cost_percent,a.final_cost_dzn ,a.final_cost_dzn_percent ,a.confirm_price_dzn ,a.confirm_price_dzn_percent,a.final_cost_pcs,a.margin_dzn,a.margin_dzn_percent,a.a1st_quoted_price,a.confirm_price,a.revised_price,a.price_with_commn_dzn,a.costing_per_id,a.design_pre_cost,a.design_percent,a.studio_pre_cost,a.studio_percent,b.job_no";
	}
	//echo $sql; die();
	$data_array=sql_select($sql);
	foreach( $data_array as $row )
	{
		//$sl=$sl+1;
		if($row[csf("costing_per_id")]==1){$order_price_per_dzn=12;$costing_val=" DZN";}
		else if($row[csf("costing_per_id")]==2){$order_price_per_dzn=1;$costing_per=" PCS";}
		else if($row[csf("costing_per_id")]==3){$order_price_per_dzn=24;$costing_val=" 2 DZN";}
		else if($row[csf("costing_per_id")]==4){$order_price_per_dzn=36;$costing_val=" 3 DZN";}
		else if($row[csf("costing_per_id")]==5){$order_price_per_dzn=48;$costing_val=" 4 DZN";}
		$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
		//used
		$summary_data[$row[csf('job_no')]]['trims_cost_total_value']+=($row[csf("trims_cost")]/$order_price_per_dzn)*$order_job_qnty;
		//used
		$summary_data[$row[csf('job_no')]]['embel_cost_total_value']+=($row[csf("embel_cost")]/$order_price_per_dzn)*$order_job_qnty;
		//used
		$summary_data[$row[csf('job_no')]]['lab_test_total_value']+=($row[csf("lab_test")]/$order_price_per_dzn)*$order_job_qnty;
		//used
		$summary_data[$row[csf('job_no')]]['inspection_total_value']+=($row[csf("inspection")]/$order_price_per_dzn)*$order_job_qnty;
		//used
		$summary_data[$row[csf('job_no')]]['freight_total_value']+=($row[csf("freight")]/$order_price_per_dzn)*$order_job_qnty;
		//used
		$summary_data[$row[csf('job_no')]]['currier_pre_cost_total_value']+=($row[csf("currier_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
		//used
		$summary_data[$row[csf('job_no')]]['certificate_pre_cost_total_value']+=($row[csf("certificate_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
		//used
		$summary_data[$row[csf('job_no')]]['design_pre_cost_total_value']+=($row[csf("design_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
		//used
		$summary_data[$row[csf('job_no')]]['studio_pre_cost_total_value']+=($row[csf("studio_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
		//used
		$summary_data[$row[csf('job_no')]]['yarn_amount_total_value']+=($yarn_amount_dzn/$order_price_per_dzn)*$order_job_qnty;
		//used
		$summary_data[$row[csf('job_no')]]['conversion_cost_total_value']+=($conv_amount_dzn/$order_price_per_dzn)*$order_job_qnty;
		//used
		$summary_data[$row[csf('job_no')]]['comm_cost_total_value']+=($row[csf("comm_cost")]/$order_price_per_dzn)*$order_job_qnty;
		//used
		$summary_data[$row[csf('job_no')]]['common_oh_total_value']+=($row[csf("common_oh")]/$order_price_per_dzn)*$order_job_qnty;
		
		$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn']=$order_price_per_dzn;
		$freight_cost_data[$row[csf("job_no")]]['freight_total_value']=($row[csf("freight")]/$order_price_per_dzn)*$order_job_qnty;
		
		/*
		//$price_dzn=$row[csf("confirm_price_dzn")];
		//$others_cost_value=$row[csf("total_cost")]-$row[csf("cm_cost")]-$row[csf("freight")]-$row[csf("comm_cost")]-$row[csf("commission")];
		$summary_data[$row[csf('job_no')]]['price_with_commn_dzn']+=$row[csf("price_with_commn_dzn")];
		$summary_data[$row[csf('job_no')]]['price_with_total_value']+=($row[csf("price_with_commn_dzn")]/$order_price_per_dzn)*$order_job_qnty;
		$summary_data[$row[csf('job_no')]]['price_dzn']+=$row[csf("confirm_price_dzn")];
		$summary_data[$row[csf('job_no')]]['commission_dzn']+=$row[csf("commission")];
		$summary_data[$row[csf('job_no')]]['commission_total_value']+=($row[csf("commission")]/$order_price_per_dzn)*$order_job_qnty;
		$summary_data[$row[csf('job_no')]]['trims_cost_dzn']+=$row[csf("trims_cost")];
		$summary_data[$row[csf('job_no')]]['embel_cost_dzn']+=$row[csf("embel_cost")];
		//$row[csf("commission")]
		$other_direct_expenses=$row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("currier_pre_cost")]+$row[csf("certificate_pre_cost")]+$row[csf("design_pre_cost")]+$row[csf("studio_pre_cost")];
		$summary_data[$row[csf('job_no')]]['other_direct_dzn']+=$other_direct_expenses;
		$summary_data[$row[csf('job_no')]]['other_direct_total_value']+=($other_direct_expenses/$order_price_per_dzn)*$order_job_qnty;
		$summary_data[$row[csf('job_no')]]['wash_cost_dzn']+=$row[csf("wash_cost")];
		$summary_data[$row[csf('job_no')]]['wash_cost_total_value']+=($row[csf("wash_cost")]/$order_price_per_dzn)*$order_job_qnty;
		$summary_data[$row[csf('job_no')]]['lab_test_dzn']+=$row[csf("lab_test")];
		$summary_data[$row[csf('job_no')]]['inspection_dzn']+=$row[csf("inspection")];
		$summary_data[$row[csf('job_no')]]['freight_dzn']+=$row[csf("freight")];
		$summary_data[$row[csf('job_no')]]['currier_pre_cost_dzn']+=$row[csf("currier_pre_cost")];
		$summary_data[$row[csf('job_no')]]['certificate_pre_cost_dzn']+=$row[csf("certificate_pre_cost")];
		$summary_data[$row[csf('job_no')]]['design_pre_cost_dzn']+=$row[csf("design_pre_cost")];
		$summary_data[$row[csf('job_no')]]['studio_pre_cost_dzn']+=$row[csf("studio_pre_cost")];
		$quot_studio_cost_dzn_arr[$row[csf("job_no")]]['studio_dzn_cost']=$row[csf("studio_percent")];
		$quot_studio_cost_dzn_arr[$row[csf("job_no")]]['common_oh']=$row[csf("common_oh")];
		$fab_amount_dzn=$fab_summary_data[$row[csf("job_no")]]['fab_amount_dzn'];
		$summary_data[$row[csf('job_no')]]['fab_amount_dzn']+=$fab_amount_dzn;
		$summary_data[$row[csf('job_no')]]['fab_amount_total_value']+=($fab_amount_dzn/$order_price_per_dzn)*$order_job_qnty;
		$yarn_amount_dzn=$yarn_summary_data[$row[csf("job_no")]]['yarn_amount_dzn'];
		//echo ($yarn_amount_dzn/$order_price_per_dzn)*$order_job_qnty.'d';
		$summary_data[$row[csf('job_no')]]['yarn_amount_dzn']+=$yarn_amount_dzn;
		$conv_amount_dzn=$conv_summary_data[$row[csf("job_no")]]['conv_amount_dzn'];
		$summary_data[$row[csf('job_no')]]['conversion_cost_dzn']+=$conv_amount_dzn;
		//$NetFOBValue=($row[csf("price_with_commn_dzn")]-$row[csf("commission")]);
		$net_value_dzn=$row[csf("price_with_commn_dzn")];
		$summary_data[$row[csf('job_no')]]['netfobvalue_dzn']+=($row[csf("price_with_commn_dzn")]);
		$summary_data[$row[csf('job_no')]]['netfobvalue']+=(($row[csf("price_with_commn_dzn")])/$order_price_per_dzn)*$order_job_qnty;
		//yarn_amount_total_value
		$all_cost_dzn=$yarn_amount_dzn+$fab_amount_dzn+$conv_amount_dzn+$row[csf("trims_cost")]+$row[csf("embel_cost")]+$other_direct_expenses;
		//echo $yarn_amount_dzn.'Y='.$fab_amount_dzn.'F='.$conv_amount_dzn.'Cnv='.$row[csf("trims_cost")].'Tr='.$row[csf("embel_cost")].'Em='.$other_direct_expenses;
		$summary_data[$row[csf('job_no')]]['cost_of_material_service']+=$all_cost_dzn;
		$summary_data[$row[csf('job_no')]]['cost_of_material_service_total_value']+=($all_cost_dzn/$order_price_per_dzn)*$order_job_qnty;
		$contribute_netfob_value_dzn=$net_value_dzn-($fab_amount_dzn+$yarn_amount_dzn+$conv_amount_dzn+$row[csf("trims_cost")]+$row[csf("embel_cost")]+$other_direct_expenses);
		$summary_data[$row[csf('job_no')]]['contribution_margin_dzn']+=$contribute_netfob_value_dzn;
		$summary_data[$row[csf('job_no')]]['contribution_margin_total_value']+=(($contribute_netfob_value_dzn)/$order_price_per_dzn)*$order_job_qnty;
		$summary_data[$row[csf('job_no')]]['cm_cost_dzn']+=$row[csf("cm_cost")];
		$summary_data[$row[csf('job_no')]]['cm_cost_total_value']+=($row[csf("cm_cost")]/$order_price_per_dzn)*$order_job_qnty;
		$summary_data[$row[csf('job_no')]]['comm_cost_dzn']+=$row[csf("comm_cost")];
		$summary_data[$row[csf('job_no')]]['common_oh_dzn']+=$row[csf("common_oh")];
		//echo $netfob_value_dzn.'='.$row[csf("cm_cost")];
		$Contribution_Margin=$netfob_value_dzn-$LessCostOfMaterialServices;
		$tot_gross_profit_dzn=$contribute_netfob_value_dzn-$row[csf("cm_cost")];
		$summary_data[$row[csf('job_no')]]['gross_profit_dzn']+=$tot_gross_profit_dzn;
		$summary_data[$row[csf('job_no')]]['gross_profit_total_value']+=(($tot_gross_profit_dzn)/$order_price_per_dzn)*$order_job_qnty;
	
		//$Gross_Profit= $Contribution_Margin-$row[csf("cm_cost")];
		$operate_profit_loss_dzn=$tot_gross_profit_dzn;//-($row[csf("comm_cost")]+$row[csf("common_oh")]);
		$summary_data[$row[csf('job_no')]]['operating_profit_loss_dzn']+=$operate_profit_loss_dzn;
		$summary_data[$row[csf('job_no')]]['operating_profit_loss_total_value']+=($operate_profit_loss_dzn/$order_price_per_dzn)*$order_job_qnty;
		$summary_data[$row[csf('job_no')]]['depr_amor_pre_cost_dzn']+=$row[csf("depr_amor_pre_cost")];
		$summary_data[$row[csf('job_no')]]['depr_amor_pre_cost_total_value']+=($row[csf("depr_amor_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
		$summary_data[$row[csf('job_no')]]['interest_pre_cost_dzn']+=$row[csf("interest_pre_cost")];
		$summary_data[$row[csf('job_no')]]['interest_pre_cost_total_value']+=($row[csf("interest_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
		$summary_data[$row[csf('job_no')]]['income_tax_pre_cost_dzn']+=$row[csf("income_tax_pre_cost")];
		$summary_data[$row[csf('job_no')]]['income_tax_pre_cost_total_value']+=($row[csf("income_tax_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
		$net_profit_dzn=$operate_profit_loss_dzn-($row[csf("depr_amor_pre_cost")]+$row[csf("interest_pre_cost")]+$row[csf("income_tax_pre_cost")]);
		$summary_data[$row[csf('job_no')]]['net_profit_dzn']+=$net_profit_dzn;
		$summary_data[$row[csf('job_no')]]['net_profit_dzn_total_value']+=($net_profit_dzn/$order_price_per_dzn)*$order_job_qnty;
		*/
	}
	//echo "<pre>";
	//print_r($summary_data);
	//die();
	
	//======================================================================
	$sql_commi = "
		SELECT
			a.id,a.quotation_id, a.particulars_id, a.commission_base_id, a.commision_rate, a.commission_amount, a.status_active, b.job_no
		from
			wo_pri_quo_commiss_cost_dtls a, wo_po_details_master b
		where
			a.quotation_id=b.quotation_id and a.quotation_id in(".$all_quot_id.") and a.status_active=1 and a.commission_amount>0 and b.status_active=1
	";
	//echo $sql_commi; die();
	$result_commi=sql_select($sql_commi);
	$CommiData_foreign_cost=0;
	//$CommiData_lc_cost=0;
	//$foreign_dzn_commission_amount=0;
	//$local_dzn_commission_amount=0;
	$CommiData_foreign_quot_cost_arr = array();
	$commision_local_quot_cost_arr = array();
	foreach($result_commi as $row)
	{
		$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
		$order_price_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];
	
		if($row[csf("particulars_id")]==1) //Foreign
		{
			$CommiData_foreign_cost+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
			//$foreign_dzn_commission_amount+=$row[csf("commission_amount")];
			$CommiData_foreign_quot_cost_arr[$row[csf("job_no")]]+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
		}
		else
		{
			//$CommiData_lc_cost+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
			//$local_dzn_commission_amount+=$row[csf("commission_amount")];
			$commision_local_quot_cost_arr[$row[csf("job_no")]]=$row[csf("commision_rate")];
		}
	}
	//echo "<pre>su..re";
	//print_r($CommiData_foreign_quot_cost_arr); die();
	
	//=====================================================================================
	$sql_comm="SELECT a.item_id,a.quotation_id,sum(a.amount) as amount,b.job_no from wo_pri_quo_comarcial_cost_dtls a,wo_po_details_master b where a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 group by a.quotation_id,a.item_id,b.job_no";
	//echo $sql_comm; die();
	$tot_lc_dzn_Commer=$tot_without_lc_dzn_Commer=0;
	// $summary_data['comm_cost_dzn']=0;
	// $summary_data['comm_cost_total_value']=0;
	$result_comm=sql_select($sql_comm);
	$commer_lc_cost = array();
	$commer_lc_cost_quot_arr = array();
	//$commer_without_lc_cost = array();
	foreach($result_comm as $row)
	{
		$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
		$order_price_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];
		//$summary_data['comm_cost_dzn']=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
		//$comm_amtPri=$row[csf('amount')];
		//$item_id=$row[csf('item_id')];
		if($row[csf('item_id')] == 1)//LC
		{
			$commer_lc_cost[$row[csf('job_no')]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
			$commer_lc_cost_quot_arr[$row[csf("job_no")]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
		}
		/*
		else
		{
			$commer_without_lc_cost[$row[csf('job_no')]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
		}
		*/
	}
	//echo "<pre>";
	//print_r($commer_lc_cost_quot_arr); die();
	
	/*
	|--------------------------------------------------------------------------
	| getting price quotation wise cm valu
	| end
	|--------------------------------------------------------------------------
	*/ 
	ob_start();
	?>
	<style>
		table{font-size:12px!important;}
		td,th{padding:0;}
    </style>
    <div style="width:2110px;">
        <table align="left">
            <tr><td colspan="7" align="center"><h2><? echo $company_arr[$cbo_company_name];?></h2></td></tr>
            <tr><td colspan="7" align="center"><h3>Shipment Schedule for Management</h3></td></tr>
            <tr valign="top">
            <td valign="top" id="summary_td">
                <h3 align="left" id="accordion_h2" class="accordion_h" onClick="accordion_menu( this.id,'content_summary1_panel', '')"> -Summary Panel</h3>
                <fieldset id="content_summary1_panel">
                    <table cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">
                        <thead>
                        <th width="35">SL</th>
                        <th width="80">Buyer Name</th>
                        <th width="80">Quantity</th>
                        <th width="80">CM Value BOM</th>
                        <th width="80">CM Value LC</th>
                        <th width="100">Value</th>
                        <th width="60">Value <br> %</th>
                        <th width="80"><strong>Full Shipped</strong></th>
                        <th width="80"><strong>Partial Shipped</strong></th>
                        <th width="80"><strong>Total Shipped</strong></th>  
                        <th width="80"><strong>Running</strong></th>
                        <th width="80">Sew Out</th>
                        <th width="80">Sew-Export</th>
                        <th><strong>Export<br>%</strong></th>  
                        </thead>
                        <tbody>
                        <?
                        $cm_lc_arr = array();
                        foreach ($data_arr as $grouping=>$grouping_data_arr)
                        { 
                            foreach ($grouping_data_arr as $row)
                            { 
                                /*
                                |--------------------------------------------------------------------------
                                | for price quotation wise cm value LC
                                | calculate cm value
                                | start
                                |--------------------------------------------------------------------------
                                */
                                $tot_dye_chemi_process_amount 	= $conversion_cost_arr[$row['job_no']][101]['conv_amount_total_value']*1;
                                $tot_yarn_dye_process_amount 	= $conversion_cost_arr[$row['job_no']][30]['conv_amount_total_value']*1;
                                $tot_aop_process_amount 		= $conversion_cost_arr[$row['job_no']][35]['conv_amount_total_value']*1;
                                
                                foreach($style_wise_arr as $style_key=>$val)
                                {
                                    $total_cost = $val[('qty')]*$val[('final_cost_pcs')];
                                    $total_quot_qty += $val[('qty')];
                                    $total_quot_pcs_qty += $val[('qty_pcs')];
                                    $total_sew_smv += $val[('sew_smv')];
                                    $total_quot_amount += $total_cost;
                                    $total_quot_amount_arr[$val[('quotation_id')]] += $total_cost;
                                }
                                $total_quot_amount_cal = $style_wise_arr[$row['job_no']]['qty']*$style_wise_arr[$row['job_no']]['final_cost_pcs'];
                                $tot_cm_for_fab_cost = $summary_data[$row['job_no']]['conversion_cost_total_value']-($tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$tot_aop_process_amount);
                                $commision_quot_local = $commision_local_quot_cost_arr[$row['job_no']];
                                $tot_sum_amount_quot_calc = $total_quot_amount_cal-($CommiData_foreign_quot_cost_arr[$row['job_no']]+$commer_lc_cost_quot_arr[$row['job_no']]+$freight_cost_data[$row['job_no']]['freight_total_value']);
                                $tot_sum_amount_quot_calccc = ($tot_sum_amount_quot_calc*$commision_quot_local)/100;
                                $tot_inspect_cour_certi_cost = $summary_data[$row['job_no']]['inspection_total_value']+$summary_data[$row['job_no']]['currier_pre_cost_total_value']+$summary_data[$row['job_no']]['certificate_pre_cost_total_value']+$tot_sum_amount_quot_calccc+$summary_data[$row['job_no']]['design_pre_cost_total_value'];
                                
                                $tot_emblish_cost = $summary_data[$row['job_no']]['embel_cost_total_value'];
                                $pri_freight_cost_per = $summary_data[$row['job_no']]['freight_total_value'];
                                $pri_commercial_per = $commer_lc_cost[$row['job_no']];
                                $CommiData_foreign_cost = $CommiData_foreign_quot_cost_arr[$row['job_no']];
                                
                                $total_btb = $summary_data[$row['job_no']]['lab_test_total_value']+$tot_emblish_cost+$summary_data[$row['job_no']]['comm_cost_total_value']+$summary_data[$row['job_no']]['trims_cost_total_value']+$tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$summary_data[$row['job_no']]['yarn_amount_total_value']+$tot_aop_process_amount+$summary_data[$row['job_no']]['common_oh_total_value']+$summary_data[$row['job_no']]['studio_pre_cost_total_value']+$tot_inspect_cour_certi_cost;
                                $tot_quot_sum_amount=$total_quot_amount_cal-($CommiData_foreign_cost+$pri_freight_cost_per+$pri_commercial_per);
                                $total_cm_for_gmt = ($tot_quot_sum_amount-$tot_cm_for_fab_cost-$total_btb);
                                $total_quot_pcs_qty = $quotation_qty_array[$row['job_no']]['QTY_PCS'];
                                $cm_lc_pcs = ($total_cm_for_gmt/$total_quot_pcs_qty);
                                //$cm_lc_dzn = ($total_cm_for_gmt/$total_quot_pcs_qty)*12;
                                //$cm_lc_value = ($po_wise_po_pcs_qty_data_arr[$row['po_id']])*($cm_lc_pcs);
                                //$cm_lc_value_export = ($po_wise_export_qty_arr[$row['po_id']])*($cm_lc_pcs);
                                
                                $zs = $row['company_name'].'**'.$row['buyer_name'];
                                $cm_lc_arr[$zs] += $po_wise_po_pcs_qty_data_arr[$row['po_id']]*($total_cm_for_gmt/$total_quot_pcs_qty);
                                /*
                                |--------------------------------------------------------------------------
                                | for price quotation wise cm value LC
                                | calculate cm value
                                | end
                                |--------------------------------------------------------------------------
                                */
                            }
                        }
                        
                        $i=1;
                        $total_cm_value_dzn=0;
                        $cm_value_dzn=0;
                        $cm_lc_value_summary = 0;
                        $total_cm_lc_value_summary = 0;
                        
                        foreach ($company_buyer_data_arr as $key=>$row)
                        {
                            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
                            foreach($company_buyer_po_wise_job_arr[$key] as $po=>$job)
                            {	
                                //$cm_gmt_cost_dzn=$cm_gmt_cost_dzn_arr[$po];
                                if($costing_per_arr[$job]==1) $dzn_qnty=12;
                                else if($costing_per_arr[$job]==3) $dzn_qnty=12*2;
                                else if($costing_per_arr[$job]==4) $dzn_qnty=12*3;
                                else if($costing_per_arr[$job]==5) $dzn_qnty=12*4;
                                else $dzn_qnty=1;
                                //$cm_gmt_cost_per_pcs=$cm_gmt_cost_dzn/$dzn_qnty;
                                $export_qty=$po_wise_export_qty_arr[$po];
                                $buyer_cm_gmt=$buyer_cm_gmt_cost_dzn_arr[$po]['dzn'];
                                //$cm_per_pcs=(($po_unite_price_arr[$po]*$dzn_qnty)-$total_cost_arr[$job])+$cm_cost_arr[$job];
                                //$cm_per_pcs=$buyer_cm_gmt/$dzn_qnty;
                                //$cm_value_dzn+=$cm_per_pcs*$export_qty; $po_wise_po_pcs_qty_data_arr[$row['po_id']]
                                //$cm_value_dzn+=$cm_gmt_cost_per_pcs*$company_buyer_po_qty_data_arr[$key];
                            } 
                            $cm_gmt_cost_dzn=$buyer_cm_gmt_dzn_arr[$key];
                            $total_cm_value_dzn+=$buyer_cm_gmt_dzn_arr[$key];
                            
                            //$cm_lc_arr[$zs] += $po_wise_po_pcs_qty_data_arr[$row['po_id']]*($total_cm_for_gmt/$total_quot_pcs_qty);
                            $cm_lc_value_summary = $cm_lc_arr[$key];
                            $total_cm_lc_value_summary += $cm_lc_arr[$key];
                            ?>
                            <tr bgcolor="<? echo $bgcolor;?>">
                                <td><? echo $i;?></td>
                                <td><? echo $buyer_short_name_arr[$row['buyer_name']];?></td>
                                <td align="right"><? echo round($company_buyer_po_qty_data_arr[$key]); ?></td>
                                <td align="right"><? echo fn_number_format($cm_gmt_cost_dzn,2); ?></td>
                                <td align="right"><? echo fn_number_format($cm_lc_value_summary,2); ?></td>
                                <td align="right"><? echo fn_number_format($company_buyer_po_val_data_arr[$key],2); ?></td>
                                <td align="right"><? echo fn_number_format(($company_buyer_po_val_data_arr[$key]/array_sum($company_buyer_po_val_data_arr))*100,2);?></td>
                                <td align="right"><? echo round($company_buyer_full_shiped_data_arr[$key]);?></td>
                                <td align="right"><? echo round($company_buyer_partial_shiped_data_arr[$key]); ?></td> 
                                <td align="right"><? echo round($company_buyer_full_shiped_data_arr[$key]+$company_buyer_partial_shiped_data_arr[$key]); ?></td> 
                                <td align="right"><? echo $runing=round($company_buyer_po_qty_data_arr[$key]-($company_buyer_full_shiped_data_arr[$key]+$company_buyer_partial_shiped_data_arr[$key])); ?></td>
                                
                                <td align="right"><? echo fn_number_format($company_buyer_sewing_output_data_arr[$key],0);?></td>
                                <td align="right"><? echo fn_number_format($company_buyer_sewing_output_data_arr[$key]-($company_buyer_full_shiped_data_arr[$key]+$company_buyer_partial_shiped_data_arr[$key]),0);?></td>
                                
                                <td align="right">
                                <? $ex_factory_per=(($company_buyer_full_shiped_data_arr[$key]+$company_buyer_partial_shiped_data_arr[$key])/$company_buyer_po_qty_data_arr[$key])*100;
                                echo fn_number_format($ex_factory_per,2); ?>
                                </td>  
                            </tr>
                            <?
                            $i++;
                        }
                        ?>
                        </tbody>
                        <tfoot>
                            <th></th>
                            <th></th>
                            <th><? echo fn_number_format(array_sum($company_buyer_po_qty_data_arr),0); ?></th>
                            <th><? echo fn_number_format($total_cm_value_dzn,0); ?></th>
                            <th><? echo fn_number_format($total_cm_lc_value_summary,0); ?></th>
                            <th><? echo fn_number_format(array_sum($company_buyer_po_val_data_arr),2);?></th>
                            <th></th>
                            <th><? echo fn_number_format(array_sum($company_buyer_full_shiped_data_arr));?></th>
                            <th><? echo fn_number_format(array_sum($company_buyer_partial_shiped_data_arr));?></th> 
                            <th><? echo fn_number_format(array_sum($company_buyer_full_shiped_data_arr)+array_sum($company_buyer_partial_shiped_data_arr));?></th> 
                            <th><? echo round(array_sum($company_buyer_po_qty_data_arr)-(array_sum($company_buyer_full_shiped_data_arr)+array_sum($company_buyer_partial_shiped_data_arr))); ?></th>
                            <th><? echo round(array_sum($company_buyer_sewing_output_data_arr));?></th>
                            <th><? echo round(array_sum($company_buyer_sewing_output_data_arr)-(array_sum($company_buyer_full_shiped_data_arr)+array_sum($company_buyer_partial_shiped_data_arr)));?></th>
                            <th>
                            <? 
                            $ex_factory_per=((array_sum($company_buyer_full_shiped_data_arr)+array_sum($company_buyer_partial_shiped_data_arr))/array_sum($company_buyer_po_qty_data_arr))*100;
                            //echo fn_number_format($ex_factory_per,2);
                            ?>
                            </th>  
                        </tfoot>
                    </table>
                </fieldset>
            </td>
            <td></td>
            <td valign="top">
                <h3 align="left" id="accordion_h3" class="accordion_h" onClick="accordion_menu( this.id,'content_summary2_panel', '')"> -Summary Panel</h3>
                <div id="content_summary2_panel"> 
                    <fieldset>
                        <table border="1" class="rpt_table" rules="all">
                            <thead>
                                <th>Particular</th>
                                <th>Total</th>
                                <th width="80">Full<br> Shipped </th>
                                <th width="80">Partial<br> Shipped </th>
                                <th width="80">Total<br>Shipped</th>
                                <th width="80">Running </th>
                                <th width="80">Sew Out</th>
                                <th width="80">Sew-Export <br> Qty Balance</th>
                                <th>Export %</th>
                            </thead>
                            <tr>
                                <td align="center">PO Quantity</td>
                                <td align="right"><? echo fn_number_format(array_sum($company_buyer_po_qty_data_arr),0); ?></td>
                                <td align="right"><? echo fn_number_format(array_sum($company_buyer_full_shiped_data_arr));?></td>
                                <td align="right"><? echo fn_number_format(array_sum($company_buyer_partial_shiped_data_arr),0);?></td>
                                <td align="right"><? echo fn_number_format(array_sum($company_buyer_full_shiped_data_arr)+array_sum($company_buyer_partial_shiped_data_arr),0);?></td>
                                <td align="right"><? echo round(array_sum($company_buyer_po_qty_data_arr)-(array_sum($company_buyer_full_shiped_data_arr)+array_sum($company_buyer_partial_shiped_data_arr))); ?></td>
                                
                                <td align="right"><? echo round(array_sum($company_buyer_sewing_output_data_arr));?></td>
                                <td align="right"><? echo round(array_sum($company_buyer_sewing_output_data_arr)-(array_sum($company_buyer_full_shiped_data_arr)+array_sum($company_buyer_partial_shiped_data_arr)));?></td>
                                
                                <td align="right">
                                <? 
                                $ex_factory_per=((array_sum($company_buyer_full_shiped_data_arr)+array_sum($company_buyer_partial_shiped_data_arr))/array_sum($company_buyer_po_qty_data_arr))*100;
                                echo fn_number_format($ex_factory_per,0);
                                ?>                                   
                                </td>
                                </tr>
                                <tr bgcolor="white">
                                <td align="center">Value</td>
                                <td align="right"><? echo fn_number_format(array_sum($company_buyer_po_val_data_arr),2);?></td>
                                <td align="right">
                                <?
                                $av_rage=array_sum($company_buyer_po_val_data_arr)/array_sum($company_buyer_po_qty_data_arr);
                                echo fn_number_format((array_sum($company_buyer_full_shiped_data_arr)*$av_rage),2);
                                ?>
                                </td>
                                <td align="right">
                                <?
                                echo fn_number_format((array_sum($company_buyer_partial_shiped_data_arr)*$av_rage),2);
                                ?>
                                </td>
                                <td align="right">
                                <?
                                echo fn_number_format((array_sum($company_buyer_full_shiped_data_arr)*$av_rage)+(array_sum($company_buyer_partial_shiped_data_arr)*$av_rage),2);
                                ?>
                                </td>
                                <td align="right">
                                <?
                                $running=(array_sum($company_buyer_po_qty_data_arr)*$av_rage)-((array_sum($company_buyer_full_shiped_data_arr)*$av_rage)+(array_sum($company_buyer_partial_shiped_data_arr)*$av_rage))	;
                                echo fn_number_format(($running),2);
                                ?>
                                </td>
                                <td align="right"><? echo fn_number_format(array_sum($company_buyer_sewing_val_data_arr),2);?></td>
                                <td align="right"><? echo fn_number_format(array_sum($company_buyer_sewing_val_data_arr)-((array_sum($company_buyer_full_shiped_data_arr)*$av_rage)+(array_sum($company_buyer_partial_shiped_data_arr)*$av_rage)),2);?></td>
                                <td align="right">
                                <?
                                $ex_factory_val_per=((array_sum($company_buyer_full_shiped_data_arr)*$av_rage)+(array_sum($company_buyer_partial_shiped_data_arr)*$av_rage))/array_sum($company_buyer_po_val_data_arr)*100;
                                echo fn_number_format($ex_factory_val_per,2);
                                ?>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </div>
            </td>
            <td></td>
            <td>
                <h3 align="left" id="accordion_h4" class="accordion_h" onClick="accordion_menu( this.id,'content_summary3_panel', '')"> -Performance Summary</h3>
                <div id="content_summary3_panel"> 
                    <fieldset>
                        <table border="1" class="rpt_table" rules="all">
                            <thead>
                                <th width="120">Particular</th>
                                <th width="60">No of PO</th>
                                <th width="80">PO Qnty</th>
                                <th width="35">%</th>
                            </thead>
                            <tr>
                                <td align="center">On Time Shipment</td>
                                <td align="right"><? echo array_sum($number_of_order['ontime']);?></td>
                                <td align="right"><? echo array_sum($po_qty_arr['ontime']);?></td>
                                <td align="right"><? echo fn_number_format((array_sum($po_qty_arr['ontime'])/array_sum($company_buyer_po_qty_data_arr))*100,2); ?></td>
                            </tr>
                            <tr bgcolor="white">
                                <td align="center">Delay Shipment</td>
                                <td align="right"><? echo array_sum($number_of_order['delay']);?></td>
                                <td align="right"><? echo array_sum($po_qty_arr['delay']);?></td>
                                <td align="right"><? echo fn_number_format((array_sum($po_qty_arr['delay'])/array_sum($company_buyer_po_qty_data_arr))*100,2); ?></td>
                            </tr>
                            <tr bgcolor="white">
                                <td align="center">Yet To Shipment</td>
                                <td align="right"><? echo array_sum($number_of_order['yet_to']);?></td>
                                <td align="right"><? echo array_sum($po_qty_arr['yet_to']);?></td>
                                <td align="right"><? echo fn_number_format((array_sum($po_qty_arr['yet_to'])/array_sum($company_buyer_po_qty_data_arr))*100,2); ?></td>
                            </tr>
                        </table>
                    </fieldset>
                </div>
            </td>
            </tr>
            <tr><td colspan="5"></td></tr>
        </table>
        <div id="content_report_panel">
            <table>
                <tr>
                    <td bgcolor="#FF9900" title="Orange" width="20">&nbsp;</td><td colspan="2">Maximum 10 Days Remainding</td>
                    <td bgcolor="#009900" title="Green" width="20">&nbsp;</td><td>On Time Shipment</td>
                    <td bgcolor="#3399FF" title="Blue" width="20">&nbsp;</td><td>Delay Shipment</td>
                    <td bgcolor="#FF0000" title="Red" width="20">&nbsp;</td><td colspan="2">Shipment Date Over & Pending</td>
                </tr>
            </table> 
            <table width="2420" id="table_header_1" border="1" class="rpt_table" rules="all">
                <thead>
                    <tr>
                        <th width="50">SL</th>
                        <th  width="80">Buyer</th>
                        <th width="100">Job No</th>
                        <th  width="110">PO No</th>
                        <th width="90">Style Ref</th>
                        <th width="35">Img</th>
                        <th width="80">PO Rec. Date</th>
                        <th width="80">Org.Ship Date</th>
                        <th width="80">Pub.Ship Date</th>
                        <th  width="50" title="Switch on Date Category">Days in Hand</th>
                        <th width="50">UOM</th>
                        <th width="90">Order Qty(Pcs)</th>
                        <th  width="50">Per Unit Price</th>
                        <th width="60">CM Dzn BOM</th>
                        <th width="60">CM Dzn LC</th>
                        <th width="100">CM Value(Pcs) BOM</th>
                        <th width="100">CM Value(Pcs) LC</th>
                        <th width="100">PO FOB</th>
                        <th width="100">Sew Output</th>
                        <th width="100">Sew FOB</th>
                        <th width="80">Export Date</th>
                        <th width="80">Export Qty</th>
                        <th width="80">Export CM Value BOM</th>
                        <th width="80">Export CM Value LC</th>
                        <th width="80">Export FOB Value</th>
                        <th width="80">Sew-Export Qty Balance</th>
                        <th  width="80">Short/Access Qty</th>
                        <th width="80">Short/Access Value</th>
                        <th width="100">Shipping Status</th>
                        <th>Order Status</th>
                    </tr>
                </thead>
            </table>
            <div style="max-height:400px; overflow-y:scroll; width:2440px;"  align="left" id="scroll_body">
                <table width="2420" border="1" class="rpt_table" rules="all" id="table_body">
                    <tbody>
                    <?			
                    $i=1;
                    $total_short_access_qty=$total_short_access_val=$total_export=$total_fob=$grand_total_cm_value=$grand_total_cm_value_export=$grand_total_fob_value_export=$grand_total_sew_export_qty_balance=$grand_total_sew_output=$grand_total_sew_fob=0;
                    $grand_total_cm_lc_dzn = 0;
                    $grand_total_cm_lc_value = 0;
                    $grand_total_cm_lc_value_export = 0;
            
                    foreach ($data_arr as $grouping=>$grouping_data_arr)
                    { 
                        ?>
                        <tr bgcolor="#999999">
                        <th colspan="30" align="left"><? echo $grouping; ?> </th>
                        </tr>
                        <? 
                        $group_total_order_qty_pcs=$group_total_short_access_qty=$group_total_short_access_val=$group_total_export=$group_total_fob=$group_total_cm_dzn_value=$tot_cm_value=$group_total_cm_value_export=$group_total_fob_value_export=$group_total_sew_export_qty_balance=$group_total_sew_output=$group_total_sew_fob=0;
                        $group_total_cm_lc_dzn = 0;
                        $group_total_cm_lc_value = 0;
                        $group_total_cm_lc_value_export = 0;
            
						foreach ($grouping_data_arr as $row)
						{ 
							$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
							$cm_value_dzn=0;
							$cm_per_pcs=0;
							$cm_value_dzn=$cm_gmt_cost_dzn_arr[$row['po_id']]['dzn'];
							
							if($costing_per_arr[$row['job_no']]==1) $dzn_qnty=12;
							else if($costing_per_arr[$row['job_no']]==3) $dzn_qnty=12*2;
							else if($costing_per_arr[$row['job_no']]==4) $dzn_qnty=12*3;
							else if($costing_per_arr[$row['job_no']]==5) $dzn_qnty=12*4;
							else $dzn_qnty=1;
							//echo $cm_value_dzn.'<br>'.$dzn_qnty;
							$cm_per_pcs=$cm_value_dzn/$dzn_qnty;
							
							//$cm_value_dzn=$cm_per_pcs*$po_wise_po_pcs_qty_data_arr[$row['po_id']];
							$cm_value_export=$cm_per_pcs*$po_wise_export_qty_arr[$row['po_id']];
							$group_total_cm_value_export+=$cm_value_export;
							$grand_total_cm_value_export+=$cm_value_export;
							$group_total_fob_value_export+=$po_unite_price_arr[$row['po_id']]*$po_wise_export_qty_arr[$row['po_id']];
							$grand_total_fob_value_export+=$po_unite_price_arr[$row['po_id']]*$po_wise_export_qty_arr[$row['po_id']];
							$group_total_cm_dzn_value+=$cm_value_dzn;
							$total_cm_dzn_value+=$cm_value_dzn;
							$group_total_fob+=$po_unite_price_arr[$row['po_id']]*$po_wise_po_pcs_qty_data_arr[$row['po_id']];
							$total_fob+=$po_unite_price_arr[$row['po_id']]*$po_wise_po_pcs_qty_data_arr[$row['po_id']];
							$group_total_export+=$po_wise_export_qty_arr[$row['po_id']];
							$total_export+=$po_wise_export_qty_arr[$row['po_id']];
							$group_total_short_access_qty+=$po_wise_po_pcs_qty_data_arr[$row['po_id']]-$po_wise_export_qty_arr[$row['po_id']];
							$total_short_access_qty+=$po_wise_po_pcs_qty_data_arr[$row['po_id']]-$po_wise_export_qty_arr[$row['po_id']];
							$group_total_short_access_val+=($po_wise_po_pcs_qty_data_arr[$row['po_id']]-$po_wise_export_qty_arr[$row['po_id']])*$po_unite_price_arr[$row['po_id']];
							$total_short_access_val+=($po_wise_po_pcs_qty_data_arr[$row['po_id']]-$po_wise_export_qty_arr[$row['po_id']])*$po_unite_price_arr[$row['po_id']];
							$group_total_order_qty_pcs+=$po_wise_po_pcs_qty_data_arr[$row['po_id']];
							$group_total_sew_export_qty_balance+=($po_wise_sewing_qty_arr[$row['po_id']]-$po_wise_export_qty_arr[$row['po_id']]);
							$grand_total_sew_export_qty_balance+=($po_wise_sewing_qty_arr[$row['po_id']]-$po_wise_export_qty_arr[$row['po_id']]);
							$group_total_sew_output+=$po_wise_sewing_qty_arr[$row['po_id']];
							$grand_total_sew_output+=$po_wise_sewing_qty_arr[$row['po_id']];
							$group_total_sew_fob+=$po_wise_sewing_qty_arr[$row['po_id']]*$po_unite_price_arr[$row['po_id']];
							$grand_total_sew_fob+=$po_wise_sewing_qty_arr[$row['po_id']]*$po_unite_price_arr[$row['po_id']];
							
							//days cal----------------------------------------------------	
							
							if($cbo_category_by==1 || $cbo_category_by==2){$calculativeDate=$row['pub_shipment_date'];}
							else{$calculativeDate=$row['shipment_date'];}
							
							
							if($row['shiping_status']==1){
								$days_in_hand=datediff('d',date('y-m-d',time()),$calculativeDate);	
							}
							else if($row['shiping_status']==2){
								$days_in_hand=datediff('d',date('y-m-d',time()),$calculativeDate);	
							}
							else if($row['shiping_status']==3){
								$days_in_hand=datediff('d',$po_wise_export_date_arr[$row['po_id']],$calculativeDate);	
							}
							
							//colour----------------------------------------------------
							$color="";	
							if($row['shiping_status']==1 && $days_in_hand>10 )
							{
								$color="";	
							}
							else if($row['shiping_status']==2 && $days_in_hand>10 )
							{
								$color="";	
							}
							
							else if($row['shiping_status']==1 && ($days_in_hand<=10 && $days_in_hand>=0))
							{
								$color="orange";
							}
							else if($row['shiping_status']==2 && ($days_in_hand<=10 && $days_in_hand>=0))
							{
								$color="orange";	
							}
							
							else if($row['shiping_status']==1 &&  $days_in_hand<0)
							{
								$color="red";	
							}
							else if($row['shiping_status']==2 && $days_in_hand<0)
							{
								$color="red";	
							}
							else if($row['shiping_status']==3 && $days_in_hand>=0 )
							{
								$color="green";	
							}
							else if($row['shiping_status']==3 && $days_in_hand<0)
							{
								$color="#3399FF";	
							}
							
							/*
							|--------------------------------------------------------------------------
							| for price quotation wise cm value LC
							| calculate cm value
							| start
							|--------------------------------------------------------------------------
							*/
							$tot_dye_chemi_process_amount 	= $conversion_cost_arr[$row['job_no']][101]['conv_amount_total_value']*1;
							$tot_yarn_dye_process_amount 	= $conversion_cost_arr[$row['job_no']][30]['conv_amount_total_value']*1;
							$tot_aop_process_amount 		= $conversion_cost_arr[$row['job_no']][35]['conv_amount_total_value']*1;
							
							foreach($style_wise_arr as $style_key=>$val)
							{
								$total_cost=$val[('qty')]*$val[('final_cost_pcs')];
								$total_quot_qty+=$val[('qty')];
								$total_quot_pcs_qty+=$val[('qty_pcs')];
								$total_sew_smv+=$val[('sew_smv')];
								$total_quot_amount+=$total_cost;
								$total_quot_amount_arr[$val[('quotation_id')]]+=$total_cost;
							}
							$total_quot_amount_cal = $style_wise_arr[$row['job_no']]['qty']*$style_wise_arr[$row['job_no']]['final_cost_pcs'];
							$tot_cm_for_fab_cost=$summary_data[$row['job_no']]['conversion_cost_total_value']-($tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$tot_aop_process_amount);
							$commision_quot_local=$commision_local_quot_cost_arr[$row['job_no']];
							$tot_sum_amount_quot_calc=$total_quot_amount_cal-($CommiData_foreign_quot_cost_arr[$row['job_no']]+$commer_lc_cost_quot_arr[$row['job_no']]+$freight_cost_data[$row['job_no']]['freight_total_value']);
							$tot_sum_amount_quot_calccc = ($tot_sum_amount_quot_calc*$commision_quot_local)/100;
							$tot_inspect_cour_certi_cost=$summary_data[$row['job_no']]['inspection_total_value']+$summary_data[$row['job_no']]['currier_pre_cost_total_value']+$summary_data[$row['job_no']]['certificate_pre_cost_total_value']+$tot_sum_amount_quot_calccc+$summary_data[$row['job_no']]['design_pre_cost_total_value'];
							
							$tot_emblish_cost=$summary_data[$row['job_no']]['embel_cost_total_value'];
							$pri_freight_cost_per=$summary_data[$row['job_no']]['freight_total_value'];
							$pri_commercial_per=$commer_lc_cost[$row['job_no']];
							$CommiData_foreign_cost=$CommiData_foreign_quot_cost_arr[$row['job_no']];
							
							$total_btb=$summary_data[$row['job_no']]['lab_test_total_value']+$tot_emblish_cost+$summary_data[$row['job_no']]['comm_cost_total_value']+$summary_data[$row['job_no']]['trims_cost_total_value']+$tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$summary_data[$row['job_no']]['yarn_amount_total_value']+$tot_aop_process_amount+$summary_data[$row['job_no']]['common_oh_total_value']+$summary_data[$row['job_no']]['studio_pre_cost_total_value']+$tot_inspect_cour_certi_cost;
							$tot_quot_sum_amount=$total_quot_amount_cal-($CommiData_foreign_cost+$pri_freight_cost_per+$pri_commercial_per);
							$total_cm_for_gmt=($tot_quot_sum_amount-$tot_cm_for_fab_cost-$total_btb);
							$total_quot_pcs_qty = $quotation_qty_array[$row['job_no']]['QTY_PCS'];
							$cm_lc_pcs = ($total_cm_for_gmt/$total_quot_pcs_qty);
							$cm_lc_dzn = ($total_cm_for_gmt/$total_quot_pcs_qty)*12;
							$cm_lc_value = ($po_wise_po_pcs_qty_data_arr[$row['po_id']])*($cm_lc_pcs);
							$cm_lc_value_export = ($po_wise_export_qty_arr[$row['po_id']])*($cm_lc_pcs);
							
							$group_total_cm_lc_dzn+=$cm_lc_dzn;
							$group_total_cm_lc_value+=$cm_lc_value;
							$group_total_cm_lc_value_export+=$cm_lc_value_export;
							$grand_total_cm_lc_dzn+=$cm_lc_dzn;
							$grand_total_cm_lc_value+=$cm_lc_value;
							$grand_total_cm_lc_value_export+=$cm_lc_value_export;
							
							/*
							|--------------------------------------------------------------------------
							| for price quotation wise cm value LC
							| calculate cm value
							| end
							|--------------------------------------------------------------------------
							*/
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                                <td width="50" align="center" bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
                                <td width="80" align="center"><? echo $buyer_short_name_arr[$row['buyer_name']];?></td>
                                <td width="100" align="center"><? echo $row['job_no'];?></td>
                                <td width="110" align="center"><p><? echo $row['po_number'];?></p></td>
                                <td width="90" align="center"><p><? echo $row['style_ref_no'];?></p></td>
                                <td width="35" align="center"><a href="javascript:openmypage_img('<? echo $imge_arr[$row['job_no']]; ?>')"><img  src='../../../<? echo $imge_arr[$row['job_no']]; ?>' height='25' width='25' /></a></td>
                                <td width="80" align="center"><? echo change_date_format($row['po_received_date'],'dd-mm-yyyy','-');?></td>
                                <td width="80" align="center"><? echo change_date_format($row['shipment_date'],'dd-mm-yyyy','-');?></td>
                                <td width="80" align="center"><? echo change_date_format($row['pub_shipment_date'],'dd-mm-yyyy','-');?></td>
                                <td width="50" align="center" bgcolor="<? echo $color;?>"><? echo $days_in_hand; ?></td>
                                <td width="50" align="center"><a href="javascript:fn_get_order_dtls(<? echo $row['po_id'];?>);"><? echo $unit_of_measurement[$row['order_uom']];?></a></td>
                                <td width="90" align="right"><? echo $po_wise_po_pcs_qty_data_arr[$row['po_id']];?></td>
                                <td width="50" align="right"><? echo fn_number_format($po_unite_price_arr[$row['po_id']],2);?></td>
                                <td width="60" align="right"><? echo fn_number_format($cm_value_dzn,2); ?></td>
                                <td width="60" align="right"><? echo fn_number_format($cm_lc_dzn,2); ?></td>
                                <td width="100" align="right" title="CM Per Pcs=<? echo fn_number_format($cm_per_pcs,4);?>">
                                <?
                                $tot_cm_value+=$po_wise_po_pcs_qty_data_arr[$row['po_id']]*$cm_per_pcs;
                                $grand_total_cm_value+=$po_wise_po_pcs_qty_data_arr[$row['po_id']]*$cm_per_pcs;
                                echo fn_number_format($po_wise_po_pcs_qty_data_arr[$row['po_id']]*$cm_per_pcs,2);
                                ?>
                                </td>
                                <td width="100" align="right" title="CM Per Pcs=<? echo fn_number_format($cm_lc_pcs,4);?>"><?php echo fn_number_format($cm_lc_value,2); ?></td>
                                <td width="100" align="right"><? echo fn_number_format($po_unite_price_arr[$row['po_id']]*$po_wise_po_pcs_qty_data_arr[$row['po_id']],2); ?></td>
                                <td width="100" align="right"><? echo fn_number_format($po_wise_sewing_qty_arr[$row['po_id']]); ?></td>
                                <td width="100" align="right"><? echo fn_number_format($po_wise_sewing_qty_arr[$row['po_id']]*$po_unite_price_arr[$row['po_id']],2)?></td>
                                <td width="80" align="center"><? echo change_date_format($po_wise_export_date_arr[$row['po_id']]); ?></td>
                                <td width="80" align="right">
                                <a href="javascript:void()" onClick="open_exfact_popup(<? echo $row['po_id'];?>);">
                                <? echo fn_number_format($po_wise_export_qty_arr[$row['po_id']],0); ?>
                                </a>                                          
                                </td>
                                <td width="80" align="right" title="CM Dzn=<? echo fn_number_format($cm_value_dzn,4).',Per Pcs='.fn_number_format($cm_per_pcs,4);?>"><? echo fn_number_format($cm_value_export,2);?></td>
                                <td width="80" align="right" title="CM Dzn=<? echo fn_number_format($cm_lc_dzn,4).',Per Pcs='.fn_number_format($cm_lc_pcs,4);?>"><? echo fn_number_format($cm_lc_value_export,2);?></td>
                                <td width="80" align="right"><? echo fn_number_format($po_unite_price_arr[$row['po_id']]*$po_wise_export_qty_arr[$row['po_id']],2); ?></td>
                                <td width="80" align="right"><? echo fn_number_format($po_wise_sewing_qty_arr[$row['po_id']]-$po_wise_export_qty_arr[$row['po_id']]); ?></td>
                                
                                <td width="80" align="right"><? echo fn_number_format($po_wise_po_pcs_qty_data_arr[$row['po_id']]-$po_wise_export_qty_arr[$row['po_id']]);?></td>
                                <td width="80" align="right"><? echo fn_number_format(($po_wise_po_pcs_qty_data_arr[$row['po_id']]-$po_wise_export_qty_arr[$row['po_id']])*$po_unite_price_arr[$row['po_id']],2);?></td>
                                <td align="center" width="100"><? echo $shipment_status[$row['shiping_status']]; ?></td>
                                <td align="center"><? echo $order_status[$row['is_confirmed']]; ?></td>
							</tr>
							<?
							$i++;
						}
						?>
						<tr bgcolor="#CCCCCC" style="vertical-align:middle" height="26">
                            <td colspan="11" align="right">Total: </td>
                            <td align="right"><? echo fn_number_format($group_total_order_qty_pcs,0); ?></td>
                            <td></td>
                            <td align="right"><? echo fn_number_format($group_total_cm_dzn_value,2); ?></td>
                            <td align="right"><? echo fn_number_format($group_total_cm_lc_dzn,2); ?></td>
                            <td align="right"><? echo fn_number_format($tot_cm_value,2); ?></td>
                            <td align="right"><? echo fn_number_format($group_total_cm_lc_value,2); ?></td>
                            <td align="right"><? echo fn_number_format($group_total_fob,2); ?></td>
                            <td align="right"><? echo fn_number_format($group_total_sew_output,0);?></td>
                            <td align="right"><? echo fn_number_format($group_total_sew_fob,0);?></td>
                            <td></td>
                            <td align="right"><? echo fn_number_format($group_total_export,0); ?></td>
                            <td align="right"><? echo fn_number_format($group_total_cm_value_export,2); ?></td>
                            <td align="right"><? echo fn_number_format($group_total_cm_lc_value_export,2); ?></td>
                            <td align="right"><? echo fn_number_format($group_total_fob_value_export,2); ?></td>
                            <td align="right"><? echo fn_number_format($group_total_sew_export_qty_balance,2); ?></td>
                            <td align="right"><? echo fn_number_format($group_total_short_access_qty,0); ?></td>
                            <td align="right"><? echo fn_number_format($group_total_short_access_val,0); ?></td>
                            <td></td>
                            <td></td>
						</tr>
						<?
						}
						?>
                    </tbody>
                </table>
            </div>
            <table width="2420" id="report_table_footer" border="1" class="rpt_table" rules="all">
                <tfoot>
                    <tr>
                        <th width="50"></th>
                        <th width="80"></th>
                        <th width="100"></th>
                        <th width="110"></th>
                        <th width="90"></th>
                        <th width="35"></th>
                        <th width="80"></th>
                        <th width="80"></th>
                        <th width="80">Grand Total</th>
                        <th width="50"></th>
                        <th width="50"></th>
                        <th width="90" align="right"><? echo fn_number_format(array_sum($po_wise_po_pcs_qty_data_arr),0); ?></th>
                        <th width="50"></th>
                        <th width="60" align="right"><? echo fn_number_format($total_cm_dzn_value,2); ?></th>
                        <th width="60" align="right"><? echo fn_number_format($grand_total_cm_lc_dzn,2); ?></th>
                        <th width="100" align="right"><? echo fn_number_format($grand_total_cm_value,2); ?></th>
                        <th width="100" align="right"><? echo fn_number_format($grand_total_cm_lc_value,2); ?></th>
                        <th width="100" align="right"><? echo fn_number_format($total_fob,2); ?></th>
                        <th width="100" align="right"><? echo fn_number_format($grand_total_sew_output,0);?></th>
                        <th width="100" align="right"><? echo fn_number_format($grand_total_sew_fob,0);?></th>
                        <th width="80"></th>
                        <th width="80" align="right"><? echo fn_number_format($total_export,0); ?></th>
                        <th width="80" align="right"><? echo fn_number_format($grand_total_cm_value_export,2); ?></th>
                        <th width="80" align="right"><? echo fn_number_format($grand_total_cm_lc_value_export,2); ?></th>
                        <th width="80" align="right"><? echo fn_number_format($grand_total_fob_value_export,2); ?></th>
                        <th width="80" align="right"><? echo fn_number_format($grand_total_sew_export_qty_balance,0);?></th>
                        <th width="80" align="right"><? echo fn_number_format($total_short_access_qty,0); ?></th>
                        <th width="80" align="right"><? echo fn_number_format($total_short_access_val,0); ?></th>
                        <th width="100"></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
	<?
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename)
	{
    	@unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename"; 
    exit();
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]);
			$("#hide_job_no").val(splitData[1]);
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:580px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <?
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>
                        <td align="center">
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'shipment_schedule_management_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	//echo $month_id;
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$data[1]";
	}

	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(insert_date) as year";
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";

	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(insert_date)=$year_id"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";
	}
	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by job_no";

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
	exit();
} // Job Search end

if($action=="order_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	list($company_name,$buyer_name,$job_no)=explode('_',$data);
	?>
	<script>
		function js_set_value(str)
		{
			var splitData = str.split("_");
			$("#order_no_id").val(splitData[0]);
			$("#order_no_val").val(splitData[1]);
			parent.emailwindow.hide();
		}
	</script>
        <input type="hidden" id="order_no_id" />
        <input type="hidden" id="order_no_val" />
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:580px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Order No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <?
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_name $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>
                        <td align="center">
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Order No",3=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "2",$dd,0 );
						?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_name; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'order_no_popup_list', 'search_div', 'shipment_schedule_management_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="order_no_popup_list")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($companyID,$buyer_name,$search_by,$search_common,$year_id,$month_id)=explode('**',$data);
 
 	if ($buyer_name!=0) $where_con.=" and b.buyer_name=$buyer_name";
	
	if($search_by==1 && $search_common!=''){
		$where_con.=" and b.job_no like('%".$search_common."%')";
	}
	else if($search_by==2 && $search_common!='')
	{
		$where_con.=" and a.po_number like('%".$search_common."%')";
	}
	else if($search_by==3 && $search_common!='')
	{
		$where_con.=" and b.style_ref_no like('%".$search_common."%')";
	}
	

	$sql="select a.id, a.po_number, b.job_no_prefix_num, b.job_no, b.buyer_name, b.style_ref_no from wo_po_details_master b, wo_po_break_down a  where b.job_no=a.job_no_mst and b.company_name=$companyID and b.is_deleted=0 $where_con ORDER BY b.job_no";
	  //echo $sql;
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	$arr=array(1=>$buyer);

	echo  create_list_view("list_view", "Job No,Buyer,Style Ref.,Order No", "110,110,150,180","610","350",0, $sql, "js_set_value", "id,po_number", "", 1, "0,buyer_name,0,0,0", $arr , "job_no_prefix_num,buyer_name,style_ref_no,po_number", "budget_breakdown_report_controller",'','0,0,0,0,0','') ;
	disconnect($con);
	exit();
}

if($action=="img_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
    <img src="../../../../<? echo $data;?>" width="280">
    <?
}

if($action=="po_break_down")
{
	echo load_html_head_contents("PO Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$orderSql="select a.job_no,a.product_dept,a.dealing_marchant,a.season_buyer_wise,a.buyer_name,a.order_uom,a.set_break_down,b.id,b.po_quantity,b.unit_price,b.po_total_price,b.po_number,c.item_number_id,SUM(c.order_quantity) AS order_quantity,a.set_smv,SUM(c.order_total) AS order_total  from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and  a.job_no=c.job_no_mst and b.id=c.po_break_down_id and c.po_break_down_id=$data and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.product_dept,a.dealing_marchant,a.season_buyer_wise,a.buyer_name,b.id,a.order_uom,a.set_break_down,b.po_quantity,b.unit_price,b.po_total_price,a.job_no,c.item_number_id,a.set_smv,b.po_number";
	$orderSqlResult=sql_select($orderSql);
	foreach($orderSqlResult as $rows)
	{
		$jobDataArr[$rows[csf("id")]]=array(
			job_no=>$rows[csf("job_no")],
			product_dept=>$rows[csf("product_dept")],
			dealing_marchant=>$rows[csf("dealing_marchant")],
			season=>$rows[csf("season_buyer_wise")],
			order_uom=>$rows[csf("order_uom")],
			item_number_id=>$rows[csf("item_number_id")],
			set_smv=>$rows[csf("set_smv")],
			po_number=>$rows[csf("po_number")],
			po_quantity=>$rows[csf("po_quantity")],
			unit_price=>$rows[csf("unit_price")],
			po_total_price=>$rows[csf("po_total_price")],
		);

		foreach(explode("__",$rows[csf("set_break_down")]) as $data_row){
			list($item,$set_ratio)=explode("_",$data_row);
			$setRatioARr[$item]=$set_ratio;
			
		}
		
		$qtyArr[$rows[csf("item_number_id")]]+=$rows[csf("order_quantity")];
		$valArr[$rows[csf("item_number_id")]]+=$rows[csf("order_total")];
		$smvArr[$rows[csf("item_number_id")]]=$rows[csf("set_smv")];
		
	}
	//print_r($setRatioARr);
	
	
	
	$season_arr=return_library_array( "select id, season_name from lib_buyer_season where buyer_id=".$orderSqlResult[0][csf("buyer_name")]." and status_active =1 and is_deleted=0 order by season_name ASC",'id','season_name');	
	
	
	?>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:580px;">
            <p>PO Summary</p>
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                     <th>Job</th>
                     <th>PO</th>
                     <th>Prod. Dept.</th>
                     <th>Season</th>
                     <th>UOM</th>
                     <th>QTY</th>
                     <th>Rate</th>
                     <th>Amount</th>
                     <th>Dealing Merchant</th>
                </thead>
                <tbody>
                	<?php foreach($jobDataArr as $row){?>
                    <tr>
                        <td align="center"><? echo $row[job_no];?></td>
                        <td><? echo $row[po_number];?></td>
                        <td><? echo $product_dept[$row[product_dept]];?></td>
                        <td align="center"><? echo $season_arr[$row[season]];?></td>
                        <td align="center"><? echo $unit_of_measurement[$row[order_uom]];?></td>
                        <td align="right"><? echo $row[po_quantity];?></td>
                        <td align="center"><? echo number_format($row[unit_price],2);?></td>
                        <td align="right"><? echo $row[po_total_price];?></td>
                        <td align="center"><? echo $company_team_member_name_arr[$row[dealing_marchant]];?></td>
                    </tr>
                    <?php } ?>
            	</tbody>
           	</table>
            
            <br>
            <p>Item Details(In Pcs)</p>
            <table width="80%" cellspacing="5" cellpadding="5" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                     
                    <th>Item Name</th>
                    <th width="50">Set Ratio</th>
                    <th width="50">SMV</th>
                    <th>Qty</th>
                    <th>Rate</th>
                    <th>Amount</th>
                </thead>
                <tbody>
                	<?php foreach($orderSqlResult as $row){?>
                    <tr>
                        <td><? echo $garments_item[$row[csf("item_number_id")]];?></td>
                        <td align="center"><? echo $setRatioARr[$row[csf("item_number_id")]];?></td>
                        <td align="right"><? echo $row[csf("set_smv")];?></td>
                        <td align="right"><? echo $row[csf("order_quantity")];?></td>
                        <td align="center"><? echo number_format($row[csf("order_total")]/$row[csf("order_quantity")],2);?></td>
                        <td align="right"><? echo $row[csf("order_total")];?></td>
                    </tr>
                    <?php } ?>
            	</tbody>
            	<tfoot>
                    <tr>
                        <th colspan="2"><strong>Total</strong></th>
                        <th><? echo array_sum($smvArr);?></th>
                        <th align="right"><? echo array_sum($qtyArr);?></th>
                        <th></th>
                        <th align="right"><? echo array_sum($valArr);?></th>
                    </tr>
            	</tfoot>
           	</table>
            
            
            
		</fieldset>
	</form>
    </div>
    </body>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="exfact_popup")
{
      echo load_html_head_contents("PO Info", "../../../../", 1, 1,'','','');
      extract($_REQUEST);
      $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
      $country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
      $cuttUpArr = array(1=>'1st',2=>'2nd',3=>'3rd');
      $orderInfo=sql_select("SELECT A.JOB_NO,A.STYLE_REF_NO,A.BUYER_NAME,B.SHIPMENT_DATE,sum(b.po_quantity*a.total_set_qnty) as QNTY_PCS,b.PO_NUMBER from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$data and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  group by a.job_no,a.style_ref_no,a.buyer_name,b.shipment_date,b.po_number");
      // print_r($orderInfo);die();
      $sqlCountry = sql_select("SELECT PO_BREAK_DOWN_ID,COUNTRY_ID,CUTUP,COUNTRY_SHIP_DATE,SHIPING_STATUS,SUM(ORDER_QUANTITY) AS QTY FROM WO_PO_COLOR_SIZE_BREAKDOWN WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 AND PO_BREAK_DOWN_ID=$data GROUP BY COUNTRY_ID,CUTUP,COUNTRY_SHIP_DATE,SHIPING_STATUS,PO_BREAK_DOWN_ID order by COUNTRY_SHIP_DATE");
      // echo $sqlCountry;die();
      $exfactSql = sql_select("SELECT A.PO_BREAK_DOWN_ID,A.COUNTRY_ID, sum(B.PRODUCTION_QNTY) as EX_QTY FROM PRO_EX_FACTORY_MST A,PRO_EX_FACTORY_DTLS B,PRO_EX_FACTORY_DELIVERY_MST C WHERE A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND A.ID=B.MST_ID AND C.ID=A.DELIVERY_MST_ID AND A.PO_BREAK_DOWN_ID=$data GROUP BY A.PO_BREAK_DOWN_ID,A.COUNTRY_ID");
      // echo $exfactSql;die();
      $exFactArray = array();
      foreach ($exfactSql as $val) 
      {
            $exFactArray[$val['PO_BREAK_DOWN_ID']][$val['COUNTRY_ID']] = $val['EX_QTY'];
      }

      ?>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
            <fieldset style="width:580px;">
            <p><b>PO Information</b></p>
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                  <thead>
                     <th width="20%"><b>Buyer</b></th>
                     <th width="20%"><b>Style</b></th>
                     <th width="15%"><b>Job</b></th>
                     <th width="20%"><b>Order</b></th>
                     <th width="15%"><b>Order Qty Pcs</b></th>
                     <th width="10%"><b>Orig.Ship Date</b></th>
                </thead>
                <tbody>                 
                    <tr>
                        <td><? echo $buyer_arr[$orderInfo[0][BUYER_NAME]];?></td>
                        <td><? echo $orderInfo[0][STYLE_REF_NO];?></td>
                        <td><? echo $orderInfo[0][JOB_NO];?></td>
                        <td><? echo $orderInfo[0][PO_NUMBER];?></td>
                        <td align="right"><? echo $orderInfo[0][QNTY_PCS];?></td>
                        <td align="center"><? echo change_date_format($orderInfo[0][SHIPMENT_DATE]);?></td>
                    </tr>                   
                  </tbody>
            </table>
            
            <br>
            <p><b>Country Details(In Pcs)</b></p>
            <table width="100%" cellspacing="5" cellpadding="5" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                  <thead>
                     
                    <th width="25%">Country Name</th>
                    <th width="15%">Qty</th>
                    <th width="10%">Cutt-Off</th>
                    <th width="15%">Ship Date</th>
                    <th width="15%">Ex-Factory Qty</th>
                    <th width="20%">Status</th>
                </thead>
                <tbody>
                  <?php
                  $order_total = 0;
                  $exfact_total = 0;
                  foreach($sqlCountry as $row)
                  {
                        ?>
                        <tr>
                              <td><? echo $country_arr[$row['COUNTRY_ID']];?></td>
                              <td align="right"><? echo number_format($row['QTY'],0);?></td>
                              <td align="center"><? echo $cuttUpArr[$row['CUTUP']];?></td>
                              <td align="center"><? echo change_date_format($row['COUNTRY_SHIP_DATE']);?></td>
                              <td align="right"><? echo number_format($exFactArray[$row['PO_BREAK_DOWN_ID']][$row['COUNTRY_ID']],0);?></td>
                              <td align="left"><? echo $shipment_status[$row['SHIPING_STATUS']];?></td>
                        </tr>
                         <?php 
                        $order_total += $row['QTY'];
                        $exfact_total += $exFactArray[$row['PO_BREAK_DOWN_ID']][$row['COUNTRY_ID']]; 
                  } ?>
                  </tbody>
                  <tfoot>
                    <tr>
                        <th><strong>Total</strong></th>
                        <th align="right"><? echo number_format($order_total,0);?></th>
                        <th></th>
                        <th></th>
                        <th align="right"><? echo number_format($exfact_total,0);?></th>
                        <th></th>
                    </tr>
                  </tfoot>
            </table>
            
            
            
            </fieldset>
      </form>
    </div>
    </body>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
      exit();
}
?>