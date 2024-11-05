<?
/*-------------------------------------------- Comments----------------
Version                  :   V2
Purpose			         : 	This form will create  Capacity and Order Booking Status Report V2
Functionality	         :
JS Functions	         :
Created by				 :	Md Mamun Ahmed Sagor 
Creation date 			 : 	15/03/2023 
Requirment Client        :
Requirment By            :
Requirment type          :
Requirment               :
Affected page            :
Affected Code            :
DB Script                :
Updated by 		         :
Update date		         :
QC Performed BY	         :
QC Date			         :
Comments		         :  Oracle Compatible Version
-----------------------------------------------------------------------*/
session_start();
include('../../../includes/common.php');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.fabrics.php');

extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');
$user_id=$_SESSION['logic_erp']['user_id'];
$_SESSION['page_permission']=$permission;



$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id, brand_id, single_user_id FROM user_passwd where id=$user_id");
//echo "SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id, brand_id, single_user_id FROM user_passwd where id=$user_id";

$location_id = $userCredential[0][csf('location_id')];
$userbrand_id = $userCredential[0][csf('brand_id')];
$single_user_id = $userCredential[0][csf('single_user_id')];
$location_credential_cond="";

if ($location_id) {
    $location_credential_cond = " and id in($location_id)";
}

if($action=="get_company_config"){
	$action($data);
}

function get_company_config($data)
{
	global $buyer_cond;
	$cbo_buyer_name= create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 0, "-- Select Buyer --", $selected, "" );

	echo "document.getElementById('buyer_td').innerHTML = '".$cbo_buyer_name."';\n";
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 0, "-- Select Buyer --", $selected );
}


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and c.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and c.buyer_name=$cbo_buyer_name";


	
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_working_factory=str_replace("'","",$cbo_working_factory);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_job_no=str_replace("'","",$txt_job_no);
    $txt_style_ref=str_replace("'","",$txt_style_ref);
	$cbo_order_status=str_replace("'","",$cbo_order_status);
	$cbo_category_by=str_replace("'","",$cbo_category_by);

	if($cbo_category_by==1)
	{
		if ($txt_date_from!="" && $txt_date_to!="")
		{
			$date_cond=" and b.pub_shipment_date between '$txt_date_from' and  '$txt_date_to'";
		}
		else
		{
			$date_cond="";
		}
	}
	else if($cbo_category_by==2)
	{
		if ($txt_date_from!="" && $txt_date_to!="")
		{
			$date_cond="and b.shipment_date between '$txt_date_from' and  '$txt_date_to'";
		}
		else
		{
			$date_cond="";
		}
	}

	if($txt_style_ref!="") $style_refCond=" and c.style_ref_no='$txt_style_ref'"; else $style_refCond="";
	if($txt_job_no!="") $jobCond=" and c.job_no_prefix_num='$txt_job_no'"; else $jobCond="";

	$exfactory_data=sql_select("select po_break_down_id,sum(ex_factory_qty) as ex_factory_qty from bh_pro_ex_factory_mst  where status_active=1 and is_deleted=0 group by po_break_down_id");
	foreach($exfactory_data as $exfatory_row)
	{
		$exfactory_data_array[$exfatory_row[csf('po_break_down_id')]]['ex_factory_qnty']=$exfatory_row[csf('ex_factory_qty')];
	}
	
	$buyer_id_arr=array();
		$sql="select c.brand_id,c.company_name, c.buyer_name, b.id as po_id, c.total_set_qnty as ratio, b.unit_price, sum(b.po_quantity) AS projpoqty,b.is_confirmed, sum(b.po_quantity*c.total_set_qnty) AS poqty, sum(b.po_quantity*b.unit_price) AS poqty_price, sum(b.factory_price) AS factory_qnty from bh_wo_po_break_down b, bh_wo_po_details_master c where b.job_id=c.id and c.company_name=$cbo_company_name and b.supplier_id=$cbo_working_factory and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $date_cond $buyer_id_cond $style_refCond $jobCond group by c.company_name, c.buyer_name, b.id, c.total_set_qnty, b.unit_price,c.brand_id,b.is_confirmed order by b.is_confirmed,c.buyer_name";	
	
		$result=sql_select($sql);
		
		$confPoQty=$projPoQty=$projpoqty_price=$confpoqty_price=$projpoqty_smv=$confpoqty_smv=0; 
		foreach($result as $row)
		{ 
			$buyer_id_arr[$row[csf('company_name')]][$row[csf('is_confirmed')]][$row[csf('buyer_name')]][$row[csf('brand_id')]]=$row[csf('unit_price')];
			$confarm_po_qnty[$row[csf('company_name')]][$row[csf('is_confirmed')]][$row[csf('buyer_name')]][$row[csf('brand_id')]]['confPo']+=$row[csf('poqty')];
			$confarm_po_value[$row[csf('company_name')]][$row[csf('is_confirmed')]][$row[csf('buyer_name')]][$row[csf('brand_id')]]['confValue']+=$row[csf('poqty_price')];
			$confarm_factory_qnty[$row[csf('company_name')]][$row[csf('is_confirmed')]][$row[csf('buyer_name')]][$row[csf('brand_id')]]['confFactoryqnty']+=$row[csf('factory_qnty')];
			$full_ship_value[$row[csf('company_name')]][$row[csf('is_confirmed')]][$row[csf('buyer_name')]][$row[csf('brand_id')]]['fullshipqnty']+=$exfactory_data_array[$row[csf('po_id')]]['ex_factory_qnty']; 

			$sub_confarm_po_qnty[$row[csf('company_name')]][$row[csf('is_confirmed')]][$row[csf('buyer_name')]]['confPo']+=$row[csf('poqty')];
			$sub_confarm_po_value[$row[csf('company_name')]][$row[csf('is_confirmed')]][$row[csf('buyer_name')]]['confValue']+=$row[csf('poqty_price')];
			$sub_confarm_factory_qnty[$row[csf('company_name')]][$row[csf('is_confirmed')]][$row[csf('buyer_name')]]['confFactoryqnty']+=$row[csf('factory_qnty')];
			$sub_full_ship_value[$row[csf('company_name')]][$row[csf('is_confirmed')]][$row[csf('buyer_name')]]['fullshipqnty']+=$exfactory_data_array[$row[csf('po_id')]]['ex_factory_qnty'];

			$status_confarm_po_qnty[$row[csf('company_name')]][$row[csf('is_confirmed')]]['confPo']+=$row[csf('poqty')];
			$status_confarm_po_value[$row[csf('company_name')]][$row[csf('is_confirmed')]]['confValue']+=$row[csf('poqty_price')];
			$status_confarm_factory_qnty[$row[csf('company_name')]][$row[csf('is_confirmed')]]['confFactoryqnty']+=$row[csf('factory_qnty')];
			$status_full_ship_value[$row[csf('company_name')]][$row[csf('is_confirmed')]]['fullshipqnty']+=$exfactory_data_array[$row[csf('po_id')]]['ex_factory_qnty'];

			$grand_confarm_po_qnty[$row[csf('company_name')]]['confPo']+=$row[csf('poqty')];
			$grand_confarm_po_value[$row[csf('company_name')]]['confValue']+=$row[csf('poqty_price')];
			$grand_confarm_factory_qnty[$row[csf('company_name')]]['confFactoryqnty']+=$row[csf('factory_qnty')];
			$grand_full_ship_value[$row[csf('company_name')]]['fullshipqnty']+=$exfactory_data_array[$row[csf('po_id')]]['ex_factory_qnty'];
		}
		/* echo "<pre>";
	print_r($full_ship_value);die;   */
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$buyerArr = return_library_array("select id,short_name from lib_buyer where status_active=1 and is_deleted=0","id","short_name");
    $brandArr = return_library_array("select id,brand_name from lib_buyer_brand where status_active=1 and is_deleted=0","id","brand_name");
	ob_start();
	?>
	<div>
        <table width="1310" cellspacing="0">
            <tr style="border:none;">
                <td colspan="11" align="center" style="border:none; font-size:16px; font-weight:bold">
                Company Name:<? echo $company_library[$cbo_company_name]; ?>                                
                </td>
            </tr>
        </table>
        <br /> 
        <table width="1310" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
            <thead>
                <tr>
                    <th width="30">SL.</th>    
                    <th width="100">Buyer Name</th>
                    <th width="100">Brand Name</th>
					<th width="120">Quantity (pcs)</th>
					<th width="120">BH Value</th>
					<th width="120">Factory Value</th>
					<th width="120">Full Shipped</th>
					<th width="120">Running</th>
					<th width="120">Running Order Value</th>
					<th width="120">BH Avg FOB</th>
					<th width="120">Factory Avg. Price</th>
					<th width="120">BH Commission</th>
                </tr>
            </thead>
        </table>
        <div style="width:1330px; max-height:350px; overflow-y:scroll" id="scroll_body" > 
            <table width="1310" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="">
            <?
			$buyer_row_span_arr = array();
			foreach($buyer_id_arr as $company=>$order_data)
            {
				foreach($order_data as $order=>$buyer_data)
				{
                	foreach($buyer_data as $buyer=>$brand_data)
                	{
						$buyer_row_span= 0;
                    	foreach($brand_data as $brand=>$key)
                		{
							$buyer_row_span++;
						}
						$buyer_row_span_arr[$company."*".$order."*".$buyer] =$buyer_row_span;
					}
				}
			}
            $i=1; $tot_qty_arr=array();
            foreach($buyer_id_arr as $company=>$order_data)
            {
				foreach($order_data as $order=>$buyer_data)
				{
                	foreach($buyer_data as $buyer=>$brand_data)
                	{
                    	foreach($brand_data as $brand=>$key)
                		{
							if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
							$buyer_td_span = $buyer_row_span_arr[$company."*".$order."*".$buyer];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
								<td rowspan="<? echo $buyer_td_span;?>" width="30"><? echo $i; ?></td>
								<td rowspan="<? echo $buyer_td_span;?>" width="100" style="word-break:break-all"><? echo $buyerArr[$buyer]; ?></td>
								<td width="100" style="word-break:break-all"><? echo $brandArr[$brand]; ?></td>
								<td width="120" align="right"><? echo number_format($confarm_po_qnty[$company][$order][$buyer][$brand]['confPo']); ?></td>
								<td width="120" align="right"><? echo number_format($confarm_po_value[$company][$order][$buyer][$brand]['confValue']); ?></td>
								<td width="120" align="right"><? echo number_format($confarm_factory_qnty[$company][$order][$buyer][$brand]['confFactoryqnty']); ?></td>
								<td width="120" align="right"><? echo number_format($full_ship_value[$company][$order][$buyer][$brand]['fullshipqnty']); ?></td>
								<td width="120" align="right"><? 
								$running_qnty=$confarm_po_qnty[$company][$order][$buyer][$brand]['confPo']-$full_ship_value[$company][$order][$buyer][$brand]['fullshipqnty'];
								echo number_format($running_qnty); ?></td>
								<td width="120" align="right"><? 
								$avg_fob=$confarm_po_value[$company][$order][$buyer][$brand]['confValue']/$confarm_po_qnty[$company][$order][$buyer][$brand]['confPo'];
								$running_order_value=$avg_fob*$running_qnty;
								echo number_format($running_order_value); ?></td>
								<td width="120" align="right"><? echo number_format($avg_fob); ?></td>
								<td width="120" align="right"><? echo number_format($confarm_factory_qnty[$company][$order][$buyer][$brand]['confFactoryqnty']/$confarm_po_qnty[$company][$order][$buyer][$brand]['confPo'],4); ?></td>
								<td width="120" align="right"><? echo number_format($confarm_po_value[$company][$order][$buyer][$brand]['confValue']-$confarm_factory_qnty[$company][$order][$buyer][$brand]['confFactoryqnty']); ?></td>
							</tr>
							<?
							$i++;
                		}
							$subbgcolor="#4d94ff";
							?>
							<tr bgcolor="<? echo $subbgcolor; ?>">
							<td width="30">&nbsp;</td>
							<td width="100"></td>
							<td width="100" align="right">Buyer Total</td>
							<td width="120" align="right"><? echo number_format($sub_confarm_po_qnty[$company][$order][$buyer]['confPo']); ?></td>
							<td width="120" align="right"><? echo number_format($sub_confarm_po_value[$company][$order][$buyer]['confValue']); ?></td>
							<td width="120" align="right"><? echo number_format($sub_confarm_factory_qnty[$company][$order][$buyer]['confFactoryqnty']); ?></td>
							<td width="120" align="right"><? echo number_format($sub_full_ship_value[$company][$order][$buyer]['fullshipqnty']); ?></td>
							<td width="120" align="right"><? 
							$sub_running_qnty=$sub_confarm_po_qnty[$company][$order][$buyer]['confPo']-$sub_full_ship_value[$company][$order][$buyer]['fullshipqnty'];
							echo number_format($sub_running_qnty); ?></td>
							<td width="120" align="right"><? 
							$sub_avg_fob=$sub_confarm_po_value[$company][$order][$buyer]['confValue']/$sub_confarm_po_qnty[$company][$order][$buyer]['confPo'];
							$sub_running_order_value=$sub_avg_fob*$sub_running_qnty;
							echo number_format($sub_running_order_value); ?></td>
							<td width="120" align="right"><? echo number_format($sub_avg_fob); ?></td>
							<td width="120" align="right"><? echo number_format($sub_confarm_factory_qnty[$company][$order][$buyer]['confFactoryqnty']/$sub_confarm_po_qnty[$company][$order][$buyer]['confPo'],4); ?></td>
							<td width="120" align="right"><? echo number_format($sub_confarm_po_value[$company][$order][$buyer]['confValue']-$sub_confarm_factory_qnty[$company][$order][$buyer]['confFactoryqnty']); ?></td>
						</tr><?
                	}	
					$statusbgcolor="#D2691E";
					?>
					<tr bgcolor="<? echo $statusbgcolor; ?>">
					<td width="30">&nbsp;</td>
					<td width="100"></td>
					<td width="100" align="right"><? echo $order_status[$order]?> Total</td>
					<td width="120" align="right"><? echo number_format($status_confarm_po_qnty[$company][$order]['confPo']); ?></td>
					<td width="120" align="right"><? echo number_format($status_confarm_po_value[$company][$order]['confValue']); ?></td>
					<td width="120" align="right"><? echo number_format($status_confarm_factory_qnty[$company][$order]['confFactoryqnty']); ?></td>
					<td width="120" align="right"><? echo number_format($status_full_ship_value[$company][$order]['fullshipqnty']); ?></td>
					<td width="120" align="right"><? 
					$status_running_qnty=$status_confarm_po_qnty[$company][$order]['confPo']-$status_full_ship_value[$company][$order]['fullshipqnty'];
					echo number_format($status_running_qnty); ?></td>
					<td width="120" align="right"><? 
					$status_avg_fob=$status_confarm_po_value[$company][$order]['confValue']/$status_confarm_po_qnty[$company][$order]['confPo'];
					$status_running_order_value=$status_avg_fob*$status_running_qnty;
					echo number_format($status_running_order_value); ?></td>
					<td width="120" align="right"><? echo number_format($status_avg_fob); ?></td>
					<td width="120" align="right"><? echo number_format($status_confarm_factory_qnty[$company][$order]['confFactoryqnty']/$status_confarm_po_qnty[$company][$order]['confPo'],4); ?></td>
					<td width="120" align="right"><? echo number_format($status_confarm_po_value[$company][$order]['confValue']-$status_confarm_factory_qnty[$company][$order]['confFactoryqnty']); ?></td>
				</tr><?
				}
            }
           
            ?>
            </table>
            <table width="1310" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all">
            <?  $totalbgcolor="#ff3300"; ?>
            <tr bgcolor="<? echo $totalbgcolor; ?>">
                    <td width="30">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="100" align="right">Grand Total</td>
                    <td width="120" align="right"><? echo number_format($grand_confarm_po_qnty[$company]['confPo']); ?></td>
					<td width="120" align="right"><? echo number_format($grand_confarm_po_value[$company]['confValue']); ?></td>
					<td width="120" align="right"><? echo number_format($grand_confarm_factory_qnty[$company]['confFactoryqnty']); ?></td>
					<td width="120" align="right"><? echo number_format($grand_full_ship_value[$company]['fullshipqnty']); ?></td>
					<td width="120" align="right"><? 
					$grand_running_qnty=$grand_confarm_po_qnty[$company]['confPo']-$grand_full_ship_value[$company]['fullshipqnty'];
					echo number_format($grand_running_qnty); ?></td>
					<td width="120" align="right"><? 
					$grand_avg_fob=$grand_confarm_po_value[$company]['confValue']/$grand_confarm_po_qnty[$company]['confPo'];
					$grand_running_order_value=$grand_avg_fob*$grand_running_qnty;
					echo number_format($grand_running_order_value); ?></td>
					<td width="120" align="right"><? echo number_format($grand_avg_fob); ?></td>
					<td width="120" align="right"><? echo number_format($grand_confarm_factory_qnty[$company]['confFactoryqnty']/$grand_confarm_po_qnty[$company]['confPo'],4); ?></td>
					<td width="120" align="right"><? echo number_format($grand_confarm_po_value[$company]['confValue']-$grand_confarm_factory_qnty[$company]['confFactoryqnty']); ?></td>
                 </tr>
            </table>
        </div>
    </div>
    <?
	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
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

if($action=="report_generate2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and c.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and c.buyer_name=$cbo_buyer_name";


	
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_working_factory=str_replace("'","",$cbo_working_factory);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_job_no=str_replace("'","",$txt_job_no);
    $txt_style_ref=str_replace("'","",$txt_style_ref);
	$cbo_order_status=str_replace("'","",$cbo_order_status);
	$cbo_category_by=str_replace("'","",$cbo_category_by);

	if($cbo_category_by==1)
	{
		if ($txt_date_from!="" && $txt_date_to!="")
		{
			$date_cond=" and b.pub_shipment_date between '$txt_date_from' and  '$txt_date_to'";
		}
		else
		{
			$date_cond="";
		}
	}
	else if($cbo_category_by==2)
	{
		if ($txt_date_from!="" && $txt_date_to!="")
		{
			$date_cond="and b.shipment_date between '$txt_date_from' and  '$txt_date_to'";
		}
		else
		{
			$date_cond="";
		}
	}
	if($txt_style_ref!="") $style_refCond=" and c.style_ref_no='$txt_style_ref'"; else $style_refCond="";
	if($txt_job_no!="") $jobCond=" and c.job_no_prefix_num='$txt_job_no'"; else $jobCond="";

	$exfactory_data=sql_select("select po_break_down_id,sum(ex_factory_qty) as ex_factory_qty from bh_pro_ex_factory_mst  where status_active=1 and is_deleted=0 group by po_break_down_id");
	foreach($exfactory_data as $exfatory_row)
	{
		$exfactory_data_array[$exfatory_row[csf('po_break_down_id')]]['ex_factory_qnty']=$exfatory_row[csf('ex_factory_qty')];
	}
	$buyer_id_arr=array();
	if($cbo_category_by==1)
	{
		$sql="select b.pub_shipment_date as main_date,c.company_name, b.id as po_id, c.total_set_qnty as ratio, b.unit_price, sum(b.po_quantity) AS projpoqty,b.is_confirmed, sum(b.po_quantity*c.total_set_qnty) AS poqty, sum(b.po_quantity*b.unit_price) AS poqty_price, sum(b.factory_price) AS factory_qnty from bh_wo_po_break_down b, bh_wo_po_details_master c where b.job_id=c.id and c.company_name=$cbo_company_name and b.supplier_id=$cbo_working_factory  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $date_cond $buyer_id_cond $style_refCond $jobCond group by c.company_name, b.id, c.total_set_qnty, b.unit_price,c.brand_id,b.is_confirmed,b.pub_shipment_date order by b.is_confirmed,b.pub_shipment_date";	
	}
	else if($cbo_category_by==2)
	{
		$sql="select b.shipment_date as main_date,c.company_name, b.id as po_id, c.total_set_qnty as ratio, b.unit_price, sum(b.po_quantity) AS projpoqty,b.is_confirmed, sum(b.po_quantity*c.total_set_qnty) AS poqty, sum(b.po_quantity*b.unit_price) AS poqty_price, sum(b.factory_price) AS factory_qnty from bh_wo_po_break_down b, bh_wo_po_details_master c where b.job_id=c.id and c.company_name=$cbo_company_name and b.supplier_id=$cbo_working_factory  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $date_cond $buyer_id_cond group by c.company_name, b.id, c.total_set_qnty, b.unit_price,c.brand_id,b.is_confirmed,b.shipment_date order by b.is_confirmed,b.shipment_date";
	}
		$result=sql_select($sql);
		
		$confPoQty=$projPoQty=$projpoqty_price=$confpoqty_price=$projpoqty_smv=$confpoqty_smv=0; 
		foreach($result as $row)
		{ 
			$mainDate=date('M-Y',strtotime($row[csf('main_date')]));
			$buyer_id_arr[$row[csf('company_name')]][$row[csf('is_confirmed')]][$mainDate]=$row[csf('unit_price')];
			$month_po_qnty[$row[csf('company_name')]][$row[csf('is_confirmed')]][$mainDate]['confPo']+=$row[csf('poqty')];
			$month_po_value[$row[csf('company_name')]][$row[csf('is_confirmed')]][$mainDate]['confValue']+=$row[csf('poqty_price')];
			$month_factory_qnty[$row[csf('company_name')]][$row[csf('is_confirmed')]][$mainDate]['confFactoryqnty']+=$row[csf('factory_qnty')];
			$month_full_ship_value[$row[csf('company_name')]][$row[csf('is_confirmed')]][$mainDate]['fullshipqnty']+=$exfactory_data_array[$row[csf('po_id')]]['ex_factory_qnty']; 

			$status_month_po_qnty[$row[csf('company_name')]][$row[csf('is_confirmed')]]['confPo']+=$row[csf('poqty')];
			$status_month_po_value[$row[csf('company_name')]][$row[csf('is_confirmed')]]['confValue']+=$row[csf('poqty_price')];
			$status_month_factory_qnty[$row[csf('company_name')]][$row[csf('is_confirmed')]]['confFactoryqnty']+=$row[csf('factory_qnty')];
			$status_month_full_ship_value[$row[csf('company_name')]][$row[csf('is_confirmed')]]['fullshipqnty']+=$exfactory_data_array[$row[csf('po_id')]]['ex_factory_qnty']; 

			$grand_month_po_qnty[$row[csf('company_name')]]['confPo']+=$row[csf('poqty')];
			$grand_month_po_value[$row[csf('company_name')]]['confValue']+=$row[csf('poqty_price')];
			$grand_month_factory_qnty[$row[csf('company_name')]]['confFactoryqnty']+=$row[csf('factory_qnty')];
			$grand_month_full_ship_value[$row[csf('company_name')]]['fullshipqnty']+=$exfactory_data_array[$row[csf('po_id')]]['ex_factory_qnty']; 
		}
	
     /*  echo "<pre>";
	print_r($month_po_qnty);die; */  
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$buyerArr = return_library_array("select id,short_name from lib_buyer where status_active=1 and is_deleted=0","id","short_name");
    $brandArr = return_library_array("select id,brand_name from lib_buyer_brand where status_active=1 and is_deleted=0","id","brand_name");
	ob_start();
	?>
	<div>
        <table width="1310" cellspacing="0">
            <tr style="border:none;">
                <td colspan="11" align="center" style="border:none; font-size:16px; font-weight:bold">
                Company Name:<? echo $company_library[$cbo_company_name]; ?>                                
                </td>
            </tr>
        </table>
        <br /> 
        <table width="1310" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
            <thead>
                <tr>
					<th width="30">SL</th>
                    <th width="100">Status</th>
                    <th width="100">Name of Month</th>
					<th width="120">Quantity (pcs)</th>
					<th width="120">BH Value</th>
					<th width="120">Factory Value</th>
					<th width="120">Full Shipped</th>
					<th width="120">Running</th>
					<th width="120">Running Order Value</th>
					<th width="120">BH Avg FOB</th>
					<th width="120">Factory Avg. Price</th>
					<th width="120">BH Commission</th>
                </tr>
            </thead>
        </table>
        <div style="width:1330px; max-height:350px; overflow-y:scroll" id="scroll_body" > 
            <table width="1310" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="">
            <?
			$rowspan_arr = array();
			foreach ($buyer_id_arr as $companyId => $order_data) 
			{
				foreach ($order_data as $orderId => $buyer_data) 
				{
					foreach ($buyer_data as $buyerId=>$key) 
					{
						
						$rowspan_arr[$companyId][$orderId]++;
					}
				}
			}
            $i=1; $tot_qty_arr=array();$sl=1;
            foreach($buyer_id_arr as $company=>$order_data)
            {
				foreach($order_data as $order=>$buyer_data)
				{	$r=0;
                	foreach($buyer_data as $buyer=>$key)
                	{
							if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
							$buyer_td_span = $buyer_row_span_array[$company."*".$order];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
							<? if($r==0){?>
								<td rowspan="<? echo $rowspan_arr[$company][$order];?>" width="30" style="word-break:break-all"><? echo $sl; ?></td>
								<td rowspan="<? echo $rowspan_arr[$company][$order];?>" width="100" style="word-break:break-all"><? echo $order_status[$order]?></td>
								<?$sl++;}?>
								<td width="100" style="word-break:break-all"><? echo $buyer; ?></td>
								<td width="120" align="right"><? echo number_format($month_po_qnty[$company][$order][$buyer]['confPo']); ?></td>
								<td width="120" align="right"><? echo number_format($month_po_value[$company][$order][$buyer]['confValue']); ?></td>
								<td width="120" align="right"><? echo number_format($month_factory_qnty[$company][$order][$buyer]['confFactoryqnty']); ?></td>
								<td width="120" align="right"><? echo number_format($month_full_ship_value[$company][$order][$buyer]['fullshipqnty']); ?></td>
								<td width="120" align="right"><? 
								$running_qnty=$month_po_qnty[$company][$order][$buyer]['confPo']-$month_full_ship_value[$company][$order][$buyer]['fullshipqnty'];
								echo number_format($running_qnty); ?></td>
								<td width="120" align="right"><? 
								$avg_fob=$month_po_value[$company][$order][$buyer]['confValue']/$month_po_qnty[$company][$order][$buyer]['confPo'];
								$running_order_value=$avg_fob*$running_qnty;
								echo number_format($running_order_value); ?></td>
								<td width="120" align="right"><? echo number_format($avg_fob); ?></td>
								<td width="120" align="right"><? echo number_format($month_factory_qnty[$company][$order][$buyer]['confFactoryqnty']/$month_po_qnty[$company][$order][$buyer]['confPo'],4); ?></td>
								<td width="120" align="right"><? echo number_format($month_po_value[$company][$order][$buyer]['confValue']-$month_factory_qnty[$company][$order][$buyer]['confFactoryqnty']); ?></td>
							</tr>
							<?
							$i++;$r++;
                		}
					$statusbgcolor="#D2691E";
					?>
					<tr bgcolor="<? echo $statusbgcolor; ?>">
					<td width="30">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right"><? echo $order_status[$order]?> Total</td>
					<td width="120" align="right"><? echo number_format($status_month_po_qnty[$company][$order]['confPo']); ?></td>
					<td width="120" align="right"><? echo number_format($status_month_po_value[$company][$order]['confValue']); ?></td>
					<td width="120" align="right"><? echo number_format($status_month_factory_qnty[$company][$order]['confFactoryqnty']); ?></td>
					<td width="120" align="right"><? echo number_format($status_month_full_ship_value[$company][$order]['fullshipqnty']); ?></td>
					<td width="120" align="right"><? 
					$status_running_qnty=$status_month_po_qnty[$company][$order]['confPo']-$status_month_full_ship_value[$company][$order]['fullshipqnty'];
					echo number_format($status_running_qnty); ?></td>
					<td width="120" align="right"><? 
					$status_avg_fob=$status_month_po_value[$company][$order]['confValue']/$status_month_po_qnty[$company][$order]['confPo'];
					$status_running_order_value=$status_avg_fob*$status_running_qnty;
					echo number_format($status_running_order_value); ?></td>
					<td width="120" align="right"><? echo number_format($status_avg_fob); ?></td>
					<td width="120" align="right"><? echo number_format($status_month_factory_qnty[$company][$order]['confFactoryqnty']/$status_month_po_qnty[$company][$order]['confPo'],4); ?></td>
					<td width="120" align="right"><? echo number_format($status_month_po_value[$company][$order]['confValue']-$status_month_factory_qnty[$company][$order]['confFactoryqnty']); ?></td>
				</tr><?
				}
            }
           
            ?>
            </table>
            <table width="1310" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all">
            <?  $totalbgcolor="#ff3300"; ?>
            <tr bgcolor="<? echo $totalbgcolor; ?>">
					<td width="30">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="100" align="right">Grand Total</td>
                    <td width="120" align="right"><? echo number_format($grand_month_po_qnty[$company]['confPo']); ?></td>
					<td width="120" align="right"><? echo number_format($grand_month_po_value[$company]['confValue']); ?></td>
					<td width="120" align="right"><? echo number_format($grand_month_factory_qnty[$company]['confFactoryqnty']); ?></td>
					<td width="120" align="right"><? echo number_format($grand_month_full_ship_value[$company]['fullshipqnty']); ?></td>
					<td width="120" align="right"><? 
					$grand_running_qnty=$grand_month_po_qnty[$company]['confPo']-$grand_month_full_ship_value[$company]['fullshipqnty'];
					echo number_format($grand_running_qnty); ?></td>
					<td width="120" align="right"><? 
					$grand_avg_fob=$grand_month_po_value[$company]['confValue']/$grand_month_po_qnty[$company]['confPo'];
					$grand_running_order_value=$grand_avg_fob*$grand_running_qnty;
					echo number_format($grand_running_order_value); ?></td>
					<td width="120" align="right"><? echo number_format($grand_avg_fob); ?></td>
					<td width="120" align="right"><? echo number_format($grand_month_factory_qnty[$company]['confFactoryqnty']/$grand_confarm_po_qnty[$company]['confPo'],4); ?></td>
					<td width="120" align="right"><? echo number_format($grand_month_po_value[$company]['confValue']-$grand_month_factory_qnty[$company]['confFactoryqnty']); ?></td>
                 </tr>
            </table>
        </div>
    </div>
    <?
	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
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

?>