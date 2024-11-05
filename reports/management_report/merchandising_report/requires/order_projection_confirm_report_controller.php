<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 140, "select a.id, a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by a.id, a.buyer_name  order by a.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "load_drop_down( 'requires/order_projection_confirm_report_controller', this.value, 'load_drop_down_brand', 'brand_td');" );   
	exit();	 
} 

if ($action=="load_drop_down_brand")
{
	echo create_drop_down( "cbo_brand_id", 100, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	if(str_replace("'","",$cbo_buyer_id)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and c.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and c.buyer_name=$cbo_buyer_id";
	
	$company_id=str_replace("'","",$cbo_company_id);
	$from_month=str_replace("'","",$cbo_from_month);
	$to_month=str_replace("'","",$cbo_to_month);
	$cbo_year=str_replace("'","",$cbo_year_id);
    $cbo_end_year_name=str_replace("'","",$cbo_end_year_name);
	
	$start_date=$cbo_year."-".$from_month;
	$end_date=$cbo_year."-".$to_month;
	$total_months=datediff("m",$start_date,$end_date);
	//strlen($from_month);
	$num_days1 = cal_days_in_month(CAL_GREGORIAN, $from_month, $cbo_year);
	
	$start_date_ship=$cbo_year."-".$from_month."-01";
	//echo $start_date_ship;
	if($total_months>0)
	{
		for ($j=0;$j<=$total_months;$j++)
		{
			$tmp=date("Y-m",strtotime("+$j months", strtotime($start_date_ship)));
			//echo $tmp.'<br>';
			$all_month[$j]=$tmp;
		}
	}
	else
	{
		$tmp=date("Y-m",strtotime("+0 months", strtotime($start_date_ship)));
			//echo $tmp.'<br>';
		$all_month[0]=$tmp;
	}
	// print_r($all_month); die;
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
		}
		else
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";	
		}
	}
	$buyer_id_arr=array();
	foreach($all_month as $key=>$val)
	{
		
		if($db_type==0) 
		{
			$sql="select c.company_name, c.buyer_name, b.id as po_id, c.total_set_qnty as ratio, b.unit_price,
			sum(b.po_quantity) AS projpoqty, 
			sum(CASE WHEN b.is_confirmed=2 THEN b.po_quantity ELSE 0 END) AS projpoqty1,
			sum(CASE WHEN b.is_confirmed=1 THEN b.po_quantity ELSE 0 END) AS confpoqty 
			from wo_po_break_down as b, wo_po_details_master as c where b.job_no_mst=c.job_no and c.company_name=$company_id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.pub_shipment_date like '".$val."-%"."' $buyer_id_cond group by b.id, c.buyer_name";
		}
		else
		{
			$sql="select c.company_name, c.buyer_name, b.id as po_id, c.total_set_qnty as ratio, b.unit_price,
			sum(b.po_quantity) AS projpoqty,
			sum(CASE WHEN b.is_confirmed=2 THEN b.po_quantity ELSE 0 END) AS projpoqty1,
			sum(CASE WHEN b.is_confirmed=1 THEN b.po_quantity ELSE 0 END) AS confpoqty
			from wo_po_break_down b, wo_po_details_master c where b.job_no_mst=c.job_no and c.company_name=$company_id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and to_char(b.pub_shipment_date,'YYYY-MM-DD') like '".$val."-%"."' $buyer_id_cond group by c.company_name, c.buyer_name, b.id, c.total_set_qnty, b.unit_price";	
		}
		//echo $sql.'<br>';
		$result=sql_select($sql);
		
		$confPoQty=0; $projPoQty=0; 
		foreach($result as $row)
		{ 
			$buyer_id_arr[$row[csf('company_name')]][$row[csf('buyer_name')]]=$row[csf('unit_price')];
			$confPoQty+=$row[csf('confpoqty')]; 
			$projPoQty+=$row[csf('projpoqty1')];
			$conf_tot_for_graph_stack[$row[csf('company_name')]][$row[csf('buyer_name')]][$val]['conf']+=$row[csf('confpoqty')]; 
			$proj_tot_for_graph_stack[$row[csf('company_name')]][$row[csf('buyer_name')]][$val]['proj']+=$row[csf('projpoqty1')]; 
		}
	}
	//print_r($proj_tot_for_graph_stack);
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$buyerArr = return_library_array("select id,short_name from lib_buyer where status_active=1 and is_deleted=0","id","short_name");
	
	$tbl_width=160+(($total_months+1)*240);
	$colspan=($total_months+1)*2;
	//echo $tbl_width.'='.$colspan;
	ob_start();
	?>
	<div>
        <table width="<? echo $tbl_width; ?>" cellspacing="0">
            <tr class="form_caption" style="border:none;">
                <td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
            </tr>
            <tr style="border:none;">
                <td colspan="<? echo $colspan; ?>" align="center" style="border:none; font-size:16px; font-weight:bold">
                Company Name:<? echo $company_library[$company_id]; ?>                                
                </td>
            </tr>
            <tr style="border:none;">
                <td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:12px; font-weight:bold">
                <? echo "From : ".$months[$from_month]." To ".$months[$to_month].', '.$cbo_year; ?>
                </td>
            </tr>
        </table>
        <br /> 
        <table width="<? echo $tbl_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
            <thead>
                <tr>
                    <th width="30" rowspan="2">SL.</th>    
                    <th width="120" rowspan="2">Buyer Name</th>
                    <? 
                    foreach($all_month as $key=>$val)
                    {
                        ?>
                        <th colspan="2"><? echo date('M,Y',strtotime($val)); ?></th>
                        <?
                    }
                    ?>
                </tr>
                <tr>
                    <? 
                    foreach($all_month as $key=>$val)
                    {
                        ?>
                        <th width="120">Projected Qty.</th>
                        <th width="120">Confirm Qty.</th>
                        <?
                    }
                    ?>
                </tr>
            </thead>
        </table>
        <div style="width:<? echo $tbl_width+20; ?>px; max-height:350px; overflow-y:scroll" id="scroll_body" > 
            <table width="<? echo $tbl_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="">
            <?
            $i=1; $tot_qty_arr=array();
            foreach($buyer_id_arr as $company=>$val)
            {
                foreach($val as $buyer=>$key)
                {
                    if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                        <td width="30"><? echo $i; ?></td>
                        <td width="120"><? echo $buyerArr[$buyer]; ?></td>
                        <? 
                        foreach($all_month as $inc=>$month_val)
                        {
                            ?>
                            <td width="120" align="right"><? echo number_format($proj_tot_for_graph_stack[$company][$buyer][$month_val]['proj']); ?></td>
                            <td width="120" align="right"><? echo number_format($conf_tot_for_graph_stack[$company][$buyer][$month_val]['conf']); ?></td>
                            <?
                            $tot_qty_arr[$month_val]['proj']+=$proj_tot_for_graph_stack[$company][$buyer][$month_val]['proj'];
                            $tot_qty_arr[$month_val]['conf']+=$conf_tot_for_graph_stack[$company][$buyer][$month_val]['conf'];
                        }
                        ?>
                    </tr>
                    <?
                    $i++;
                }	
            }
            ?>
            </table>
            <table width="<? echo $tbl_width; ?>" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all">
                <tr>
                    <td width="30">&nbsp;</td>
                    <td width="120">Total</td>
                    <? 
                    foreach($all_month as $inc=>$month_val)
                    {
                        ?>
                        <td width="120" align="right"><? echo number_format($tot_qty_arr[$month_val]['proj']); ?></td>
                        <td width="120" align="right"><? echo number_format($tot_qty_arr[$month_val]['conf']); ?></td>
                        <?
                    }
                    ?>
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
	
	if(str_replace("'","",$cbo_buyer_id)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and c.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and c.buyer_name=$cbo_buyer_id";


	
	$company_id=str_replace("'","",$cbo_company_id);
	$from_month=str_replace("'","",$cbo_from_month);
	$to_month=str_replace("'","",$cbo_to_month);
	$cbo_year=str_replace("'","",$cbo_year_id);
    $cbo_end_year_name=str_replace("'","",$cbo_end_year_name);
	$cbo_brand_id=str_replace("'","",$cbo_brand_id);
	$start_date=$cbo_year."-".$from_month;
	$end_date=$cbo_end_year_name."-".$to_month;
	$total_months=datediff("m",$start_date,$end_date);
	//strlen($from_month);
	$num_days1 = cal_days_in_month(CAL_GREGORIAN, $from_month, $cbo_year);
	
	$start_date_ship=$cbo_year."-".$from_month."-01";
    $daysinmonth=cal_days_in_month(CAL_GREGORIAN, $to_month, $cbo_end_year_name);
    $e_date=$cbo_end_year_name."-".$to_month."-".$daysinmonth;
	//echo $total_months;
	if($total_months>0)
	{
		for ($j=0;$j<=$total_months;$j++)
		{
			$tmp=date("Y-m",strtotime("+$j months", strtotime($start_date_ship)));
			$all_month[$j]=$tmp;
		}
	}
	else
	{
		$tmp=date("Y-m",strtotime("+0 months", strtotime($start_date_ship)));
			//echo $tmp.'<br>';
		$all_month[0]=$tmp;
	}
	// print_r($all_month); die;
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
		}
		else
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";	
		}
	}

    if($cbo_brand_id !=0) $brand_cond="and c.brand_id='$cbo_brand_id'"; 
	$buyer_id_arr=array();
	foreach($all_month as $key=>$val)
	{
		
		$sql="select c.brand_id,c.company_name, c.buyer_name, b.id as po_id, c.total_set_qnty as ratio, b.unit_price,
			sum(b.po_quantity) AS projpoqty,
			sum(CASE WHEN b.is_confirmed=2 THEN b.po_quantity*c.total_set_qnty ELSE 0 END) AS projpoqty1,
			sum(CASE WHEN b.is_confirmed=1 THEN b.po_quantity*c.total_set_qnty ELSE 0 END) AS confpoqty,
            sum(CASE WHEN b.is_confirmed=2 THEN b.po_quantity*b.unit_price ELSE 0 END) AS projpoqty_price,
			sum(CASE WHEN b.is_confirmed=1 THEN b.po_quantity*b.unit_price ELSE 0 END) AS confpoqty_price,
            sum(CASE WHEN b.is_confirmed=2 THEN b.po_quantity*c.set_smv ELSE 0 END) AS projpoqty_smv,
			sum(CASE WHEN b.is_confirmed=1 THEN b.po_quantity*c.set_smv ELSE 0 END) AS confpoqty_smv
			from wo_po_break_down b, wo_po_details_master c where b.job_id=c.id and c.company_name=$company_id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and to_char(b.pub_shipment_date,'YYYY-MM-DD') like '".$val."-%"."' $buyer_id_cond $brand_cond group by c.company_name, c.buyer_name, b.id, c.total_set_qnty, b.unit_price,c.brand_id order by c.buyer_name";	
	
		//echo $sql;die;
		$result=sql_select($sql);
		
		$confPoQty=$projPoQty=$projpoqty_price=$confpoqty_price=$projpoqty_smv=$confpoqty_smv=0; 
		foreach($result as $row)
		{ 
			$buyer_id_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('brand_id')]]=$row[csf('unit_price')];
			$confPoQty+=$row[csf('confpoqty')]; 
			$projPoQty+=$row[csf('projpoqty1')];
            $projpoqty_price+=$row[csf('projpoqty_price')]; 
			$confpoqty_price+=$row[csf('confpoqty_price')];
            $projpoqty_smv+=$row[csf('projpoqty_smv')]; 
			$confpoqty_smv+=$row[csf('confpoqty_smv')];
			$conf_tot_for_graph_stack[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('brand_id')]][$val]['conf']+=$row[csf('confpoqty')]; 
			$proj_tot_for_graph_stack[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('brand_id')]][$val]['proj']+=$row[csf('projpoqty1')]; 
            $price_proj_tot_for_graph_stack[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('brand_id')]][$val]['proj_price']+=$row[csf('projpoqty_price')]; 
			$price_conf_tot_for_graph_stack[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('brand_id')]][$val]['conf_price']+=$row[csf('confpoqty_price')];
            $smv_proj_tot_for_graph_stack[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('brand_id')]][$val]['proj_smv']+=$row[csf('projpoqty_smv')]; 
			$smv_conf_tot_for_graph_stack[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('brand_id')]][$val]['conf_smv']+=$row[csf('confpoqty_smv')];

            $conf_tot_for_graph_stack[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('brand_id')]]['conf']+=$row[csf('confpoqty')]; 
			$proj_tot_for_graph_stack[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('brand_id')]]['proj']+=$row[csf('projpoqty1')]; 
            $price_proj_tot_for_graph_stack[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('brand_id')]]['proj_price']+=$row[csf('projpoqty_price')]; 
			$price_conf_tot_for_graph_stack[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('brand_id')]]['conf_price']+=$row[csf('confpoqty_price')];
            $smv_proj_tot_for_graph_stack[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('brand_id')]]['proj_smv']+=$row[csf('projpoqty_smv')]; 
			$smv_conf_tot_for_graph_stack[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('brand_id')]]['conf_smv']+=$row[csf('confpoqty_smv')];

            $sub_conf_tot_for_graph_stack[$row[csf('company_name')]][$row[csf('buyer_name')]][$val]['conf']+=$row[csf('confpoqty')]; 
			$sub_proj_tot_for_graph_stack[$row[csf('company_name')]][$row[csf('buyer_name')]][$val]['proj']+=$row[csf('projpoqty1')]; 
            $sub_price_proj_tot_for_graph_stack[$row[csf('company_name')]][$row[csf('buyer_name')]][$val]['proj_price']+=$row[csf('projpoqty_price')]; 
			$sub_price_conf_tot_for_graph_stack[$row[csf('company_name')]][$row[csf('buyer_name')]][$val]['conf_price']+=$row[csf('confpoqty_price')];
            $sub_smv_proj_tot_for_graph_stack[$row[csf('company_name')]][$row[csf('buyer_name')]][$val]['proj_smv']+=$row[csf('projpoqty_smv')]; 
			$sub_smv_conf_tot_for_graph_stack[$row[csf('company_name')]][$row[csf('buyer_name')]][$val]['conf_smv']+=$row[csf('confpoqty_smv')];

            $sub_conf_tot_for_graph_stack[$row[csf('company_name')]][$row[csf('buyer_name')]]['conf']+=$row[csf('confpoqty')]; 
			$sub_proj_tot_for_graph_stack[$row[csf('company_name')]][$row[csf('buyer_name')]]['proj']+=$row[csf('projpoqty1')]; 
            $sub_price_proj_tot_for_graph_stack[$row[csf('company_name')]][$row[csf('buyer_name')]]['proj_price']+=$row[csf('projpoqty_price')]; 
			$sub_price_conf_tot_for_graph_stack[$row[csf('company_name')]][$row[csf('buyer_name')]]['conf_price']+=$row[csf('confpoqty_price')];
            $sub_smv_proj_tot_for_graph_stack[$row[csf('company_name')]][$row[csf('buyer_name')]]['proj_smv']+=$row[csf('projpoqty_smv')]; 
			$sub_smv_conf_tot_for_graph_stack[$row[csf('company_name')]][$row[csf('buyer_name')]]['conf_smv']+=$row[csf('confpoqty_smv')];

            $total_conf_tot_for_graph_stack[$row[csf('company_name')]][$val]['conf']+=$row[csf('confpoqty')]; 
			$total_proj_tot_for_graph_stack[$row[csf('company_name')]][$val]['proj']+=$row[csf('projpoqty1')]; 
            $total_price_proj_tot_for_graph_stack[$row[csf('company_name')]][$val]['proj_price']+=$row[csf('projpoqty_price')]; 
			$total_price_conf_tot_for_graph_stack[$row[csf('company_name')]][$val]['conf_price']+=$row[csf('confpoqty_price')];
            $total_smv_proj_tot_for_graph_stack[$row[csf('company_name')]][$val]['proj_smv']+=$row[csf('projpoqty_smv')]; 
			$total_smv_conf_tot_for_graph_stack[$row[csf('company_name')]][$val]['conf_smv']+=$row[csf('confpoqty_smv')];

            $total_conf_tot_for_graph_stack[$row[csf('company_name')]]['conf']+=$row[csf('confpoqty')]; 
			$total_proj_tot_for_graph_stack[$row[csf('company_name')]]['proj']+=$row[csf('projpoqty1')]; 
            $total_price_proj_tot_for_graph_stack[$row[csf('company_name')]]['proj_price']+=$row[csf('projpoqty_price')]; 
			$total_price_conf_tot_for_graph_stack[$row[csf('company_name')]]['conf_price']+=$row[csf('confpoqty_price')];
            $total_smv_proj_tot_for_graph_stack[$row[csf('company_name')]]['proj_smv']+=$row[csf('projpoqty_smv')]; 
			$total_smv_conf_tot_for_graph_stack[$row[csf('company_name')]]['conf_smv']+=$row[csf('confpoqty_smv')];
		}
	}
   /*  echo "<pre>";
	print_r($smv_conf_tot_for_graph_stack);die; */
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$buyerArr = return_library_array("select id,short_name from lib_buyer where status_active=1 and is_deleted=0","id","short_name");
    $brandArr = return_library_array("select id,brand_name from lib_buyer_brand where status_active=1 and is_deleted=0","id","brand_name");
	
	$tbl_width=720+240+(($total_months+1)*720);
	$colspan=($total_months+1)*2;
	//echo $tbl_width.'='.$colspan;
	ob_start();
	?>
	<div>
        <table width="<? echo $tbl_width; ?>" cellspacing="0">
            <tr class="form_caption" style="border:none;">
                <td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
            </tr>
            <tr style="border:none;">
                <td colspan="<? echo $colspan; ?>" align="center" style="border:none; font-size:16px; font-weight:bold">
                Company Name:<? echo $company_library[$company_id]; ?>                                
                </td>
            </tr>
            <tr style="border:none;">
                <td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:12px; font-weight:bold">
                <? echo "From : ".$months[$from_month]." To ".$months[$to_month].', '.$cbo_year; ?>
                </td>
            </tr>
        </table>
        <br /> 
        <table width="<? echo $tbl_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
            <thead>
                <tr>
                    <th width="30" rowspan="4">SL.</th>    
                    <th width="100" rowspan="4">Buyer Name</th>
                    <th width="100" rowspan="4">Divisions/ Brand</th>
                </tr>
                    <tr>
                    <? 
                    foreach($all_month as $key=>$val)
                    {
                        ?>
                        <th colspan="3">Confirm Order</th>
                        <th colspan="3">Provisional Order</th>
                        <th colspan="3">Total Month</th>
                        <?
                    }
                    ?>
                    <th colspan="3" rowspan="2">Grand Total Confirm Order</th>
                    <th colspan="3" rowspan="2">Grand Total Provisional Order</th>
                    <th colspan="3" rowspan="2">Grand Total</th>
                </tr>
                <tr>
                    <? 
                    foreach($all_month as $key=>$val)
                    {
                        ?>
                        <th colspan="6"><? echo date('M,Y',strtotime($val)); ?></th>
                        <th colspan="3"><? //echo date('M,Y',strtotime($val)); ?></th>
                        <?
                    }
                    ?>
                     <th colspan="9"><? //echo date('M,Y',strtotime($val)); ?></th>
                </tr>
                <tr>
                    <? 
                    foreach($all_month as $key=>$val)
                    {
                        ?>
                        <th width="80">Volume (Pcs)</th>
                        <th width="80">SMV</th>
                        <th width="80">Revenue ($)</th>
                        <th width="80">Volume (Pcs)</th>
                        <th width="80">SMV</th>
                        <th width="80">Revenue ($)</th>
                        <th width="80">Volume (Pcs)</th>
                        <th width="80">SMV</th>
                        <th width="80">Revenue ($)</th>
                        <?
                    }
                    ?>
                        <th width="80">Total Volume (Pcs)</th>
                        <th width="80">Total SMV</th>
                        <th width="80">Total Revenue ($)</th>
                        <th width="80">Total Volume (Pcs)</th>
                        <th width="80">Total SMV</th>
                        <th width="80">Total Revenue ($)</th>
                        <th width="80">Total Volume (Pcs)</th>
                        <th width="80">Total SMV</th>
                        <th width="80">Total Revenue ($)</th>
                </tr>
            </thead>
        </table>
        <div style="width:<? echo $tbl_width+20; ?>px; max-height:350px; overflow-y:scroll" id="scroll_body" > 
            <table width="<? echo $tbl_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="">
            <?
            $i=1; $tot_qty_arr=array();
            foreach($buyer_id_arr as $company=>$val)
            {
                foreach($val as $buyer=>$val2)
                {
                    foreach($val2 as $brand=>$key)
                {
                    if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                        <td width="30"><? echo $i; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $buyerArr[$buyer]; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $brandArr[$brand]; ?></td>
                        <? 
                        foreach($all_month as $inc=>$month_val)
                        {
                            ?>
                            <td width="80" align="right"><? echo number_format($conf_tot_for_graph_stack[$company][$buyer][$brand][$month_val]['conf']); ?></td>
                            <td width="80" align="right"><? echo number_format($smv_conf_tot_for_graph_stack[$company][$buyer][$brand][$month_val]['conf_smv']); ?></td>
                            <td width="80" align="right"><? echo number_format($price_conf_tot_for_graph_stack[$company][$buyer][$brand][$month_val]['conf_price']); ?></td>
                            <td width="80" align="right"><? echo number_format($proj_tot_for_graph_stack[$company][$buyer][$brand][$month_val]['proj']); ?></td>
                            <td width="80" align="right"><? echo number_format($smv_proj_tot_for_graph_stack[$company][$buyer][$brand][$month_val]['proj_smv']); ?></td>  
                            <td width="80" align="right"><? echo number_format($price_proj_tot_for_graph_stack[$company][$buyer][$brand][$month_val]['proj_price']); ?></td>
                            <td width="80" align="right"><? echo number_format($conf_tot_for_graph_stack[$company][$buyer][$brand][$month_val]['conf']+$proj_tot_for_graph_stack[$company][$buyer][$brand][$month_val]['proj']); ?></td>
                            <td width="80" align="right"><? echo number_format($smv_conf_tot_for_graph_stack[$company][$buyer][$brand][$month_val]['conf_smv']+$smv_proj_tot_for_graph_stack[$company][$buyer][$brand][$month_val]['proj_smv']); ?></td>
                            <td width="80" align="right"><? echo number_format($price_conf_tot_for_graph_stack[$company][$buyer][$brand][$month_val]['conf_price']+$price_proj_tot_for_graph_stack[$company][$buyer][$brand][$month_val]['proj_price']); ?></td>
                            <?
                        }
                        ?>
                            <td width="80" align="right"><? echo number_format($conf_tot_for_graph_stack[$company][$buyer][$brand]['conf']); ?></td>
                            <td width="80" align="right"><? echo number_format($smv_conf_tot_for_graph_stack[$company][$buyer][$brand]['conf_smv']); ?></td>
                            <td width="80" align="right"><? echo number_format($price_conf_tot_for_graph_stack[$company][$buyer][$brand]['conf_price']); ?></td>
                            <td width="80" align="right"><? echo number_format($proj_tot_for_graph_stack[$company][$buyer][$brand]['proj']); ?></td>
                            <td width="80" align="right"><? echo number_format($smv_proj_tot_for_graph_stack[$company][$buyer][$brand]['proj_smv']); ?></td>  
                            <td width="80" align="right"><? echo number_format($price_proj_tot_for_graph_stack[$company][$buyer][$brand]['proj_price']); ?></td>
                            <td width="80" align="right"><? echo number_format($conf_tot_for_graph_stack[$company][$buyer][$brand]['conf']+$proj_tot_for_graph_stack[$company][$buyer][$brand]['proj']); ?></td>
                            <td width="80" align="right"><? echo number_format($smv_conf_tot_for_graph_stack[$company][$buyer][$brand]['conf_smv']+$smv_proj_tot_for_graph_stack[$company][$buyer][$brand]['proj_smv']); ?></td>
                            <td width="80" align="right"><? echo number_format($price_conf_tot_for_graph_stack[$company][$buyer][$brand]['conf_price']+$price_proj_tot_for_graph_stack[$company][$buyer][$brand]['proj_price']); ?></td>
                    </tr>
                    <?
                    $i++;
                }
                $subbgcolor="#4d94ff";
                ?>
                <tr bgcolor="<? echo $subbgcolor; ?>">
                <td width="30">&nbsp;</td>
                <td width="100"></td>
                <td width="100" align="right">Sub Total</td>
                <? 
                foreach($all_month as $inc=>$month_val)
                {
                    ?>
                    <td width="80" align="right"><? echo number_format($sub_conf_tot_for_graph_stack[$company][$buyer][$month_val]['conf']); ?></td>
                    <td width="80" align="right"><? echo number_format($sub_smv_conf_tot_for_graph_stack[$company][$buyer][$month_val]['conf_smv']); ?></td>
                    <td width="80" align="right"><? echo number_format($sub_price_conf_tot_for_graph_stack[$company][$buyer][$month_val]['conf_price']); ?></td>
                    <td width="80" align="right"><? echo number_format($sub_proj_tot_for_graph_stack[$company][$buyer][$month_val]['proj']); ?></td>
                    <td width="80" align="right"><? echo number_format($sub_smv_proj_tot_for_graph_stack[$company][$buyer][$month_val]['proj_smv']); ?></td>                
                    <td width="80" align="right"><? echo number_format($sub_smv_proj_tot_for_graph_stack[$company][$buyer][$month_val]['proj_smv']); ?></td>
                    <td width="80" align="right"><? echo number_format($sub_conf_tot_for_graph_stack[$company][$buyer][$month_val]['conf']+$sub_proj_tot_for_graph_stack[$company][$buyer][$month_val]['proj']); ?></td>
                    <td width="80" align="right"><? echo number_format($sub_smv_conf_tot_for_graph_stack[$company][$buyer][$month_val]['conf_smv']+$sub_smv_proj_tot_for_graph_stack[$company][$buyer][$month_val]['proj_smv']); ?></td>
                    <td width="80" align="right"><? echo number_format($sub_price_conf_tot_for_graph_stack[$company][$buyer][$month_val]['conf_price']+$sub_price_proj_tot_for_graph_stack[$company][$buyer][$month_val]['proj_price']); ?></td>
                    <?
                }
                ?>
                    <td width="80" align="right"><? echo number_format($sub_conf_tot_for_graph_stack[$company][$buyer]['conf']); ?></td>
                    <td width="80" align="right"><? echo number_format($sub_smv_conf_tot_for_graph_stack[$company][$buyer]['conf_smv']); ?></td>
                    <td width="80" align="right"><? echo number_format($sub_price_conf_tot_for_graph_stack[$company][$buyer]['conf_price']); ?></td>
                    <td width="80" align="right"><? echo number_format($sub_proj_tot_for_graph_stack[$company][$buyer]['proj']); ?></td>
                    <td width="80" align="right"><? echo number_format($sub_smv_proj_tot_for_graph_stack[$company][$buyer]['proj_smv']); ?></td>                
                    <td width="80" align="right"><? echo number_format($sub_smv_proj_tot_for_graph_stack[$company][$buyer]['proj_smv']); ?></td>
                    <td width="80" align="right"><? echo number_format($sub_conf_tot_for_graph_stack[$company][$buyer]['conf']+$sub_proj_tot_for_graph_stack[$company][$buyer]['proj']); ?></td>
                    <td width="80" align="right"><? echo number_format($sub_smv_conf_tot_for_graph_stack[$company][$buyer]['conf_smv']+$sub_smv_proj_tot_for_graph_stack[$company][$buyer]['proj_smv']); ?></td>
                    <td width="80" align="right"><? echo number_format($sub_price_conf_tot_for_graph_stack[$company][$buyer]['conf_price']+$sub_price_proj_tot_for_graph_stack[$company][$buyer]['proj_price']); ?></td>
             </tr><?
                }	
            }
           
            ?>
            </table>
            <table width="<? echo $tbl_width; ?>" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all">
            <?  $totalbgcolor="#ff3300"; ?>
            <tr bgcolor="<? echo $totalbgcolor; ?>">
                    <td width="30">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="100" align="right">Grand Total</td>
                    <? 
                    foreach($all_month as $inc=>$month_val)
                    {
                        ?>
                        <td width="80" align="right"><? echo number_format($total_conf_tot_for_graph_stack[$company][$month_val]['conf']); ?></td>
                        <td width="80" align="right"><? echo number_format($total_smv_conf_tot_for_graph_stack[$company][$month_val]['conf_smv']); ?></td>
                        <td width="80" align="right"><? echo number_format($total_price_conf_tot_for_graph_stack[$company][$month_val]['conf_price']); ?></td>
                        <td width="80" align="right"><? echo number_format($total_proj_tot_for_graph_stack[$company][$month_val]['proj']); ?></td>
                        <td width="80" align="right"><? echo number_format($total_smv_proj_tot_for_graph_stack[$company][$month_val]['proj_smv']); ?></td>                
                        <td width="80" align="right"><? echo number_format($total_smv_proj_tot_for_graph_stack[$company][$month_val]['proj_smv']); ?></td>
                        <td width="80" align="right"><? echo number_format($total_conf_tot_for_graph_stack[$company][$month_val]['conf']+$total_proj_tot_for_graph_stack[$company][$month_val]['proj']); ?></td>
                        <td width="80" align="right"><? echo number_format($total_smv_conf_tot_for_graph_stack[$company][$month_val]['conf_smv']+$total_smv_proj_tot_for_graph_stack[$company][$month_val]['proj_smv']); ?></td>
                        <td width="80" align="right"><? echo number_format($total_price_conf_tot_for_graph_stack[$company][$month_val]['conf_price']+$total_price_proj_tot_for_graph_stack[$company][$month_val]['proj_price']); ?></td>
                        <?
                    }
                    ?>
                        <td width="80" align="right"><? echo number_format($total_conf_tot_for_graph_stack[$company]['conf']); ?></td>
                        <td width="80" align="right"><? echo number_format($total_smv_conf_tot_for_graph_stack[$company]['conf_smv']); ?></td>
                        <td width="80" align="right"><? echo number_format($total_price_conf_tot_for_graph_stack[$company]['conf_price']); ?></td>
                        <td width="80" align="right"><? echo number_format($total_proj_tot_for_graph_stack[$company]['proj']); ?></td>
                        <td width="80" align="right"><? echo number_format($total_smv_proj_tot_for_graph_stack[$company]['proj_smv']); ?></td>                
                        <td width="80" align="right"><? echo number_format($total_smv_proj_tot_for_graph_stack[$company]['proj_smv']); ?></td>
                        <td width="80" align="right"><? echo number_format($total_conf_tot_for_graph_stack[$company]['conf']+$total_proj_tot_for_graph_stack[$company]['proj']); ?></td>
                        <td width="80" align="right"><? echo number_format($total_smv_conf_tot_for_graph_stack[$company]['conf_smv']+$total_smv_proj_tot_for_graph_stack[$company]['proj_smv']); ?></td>
                        <td width="80" align="right"><? echo number_format($total_price_conf_tot_for_graph_stack[$company]['conf_price']+$total_price_proj_tot_for_graph_stack[$company]['proj_price']); ?></td>
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

