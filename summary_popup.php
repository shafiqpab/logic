<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="summary_popup")
{
	echo load_html_head_contents("Popup Info","", 1, 1, $unicode,1,1);
	extract($_REQUEST); 
	?>
    <div style="width:770px;" align="center"><input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px" /></div>
	<div style="width:770px;margin-left:5px; margin-top:5px;">
        <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="770">
            <thead>
                <th width="80">Month</th>
                <th width="130">Company</th>
                <th width="115">Proj. Qty (Pcs)</th>
                <th width="115">Conf. Qty (Pcs)</th>
                <th width="115">Total Qty (Pcs)</th>
                <th width="115">Ship Out Qty (Pcs)</th>
                <th>%</th>
            </thead>
		</table>
        <div style="width:770px; overflow-y:scroll; max-height:310px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" id="tbl_list_search">
            <?
                $month_prev=add_month(date("Y-m-d",time()),-3);
                $month_next=add_month(date("Y-m-d",time()),8);
                
                $start_yr=date("Y",strtotime($month_prev));
                $end_yr=date("Y",strtotime($month_next));
                
				$manufacturing_company=''; $company_arr=array(); $no_of_company=1;
				$comData=sql_select("select comp.id as id, comp.company_name as company_name from lib_company comp where comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.id");
				foreach($comData as $row)
                {
					$no_of_company++;
                    $company_arr[$row[csf('id')]]=$row[csf('company_name')];
					$manufacturing_company.=$row[csf('id')].",";
                }
                $manufacturing_company=chop($manufacturing_company,',');
				
                $exFactory_arr=array();
                $data_arr=sql_select( "select po_break_down_id, country_id, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id, country_id");
                foreach($data_arr as $row)
                {
                    $exFactory_arr[$row[csf('po_break_down_id')]][$row[csf('country_id')]]=$row[csf('ex_factory_qnty')];
                }
                
                for($e=0;$e<=11;$e++)
                {
                    $tmp=add_month(date("Y-m-d",strtotime($month_prev)),$e);
                    $yr_mon_part[$e]=date("Y-m",strtotime($tmp));
                }
                $i=1; $totConfQty=0; $totProjQty=0; $totExFactoryQty=0; $grandTotQty=0;
                foreach($yr_mon_part as $key=>$val)
                {
					$monConfQty=0; $monProjQty=0; $monExFactoryQty=0; $monTotQty=0;
                    if($db_type==0) 
                    {
                        $sql="select c.company_name, b.id as po_id, a.country_id, sum(CASE WHEN b.is_confirmed=1 THEN a.order_quantity ELSE 0 END) AS 'confpoqty', sum(CASE WHEN b.is_confirmed=2 THEN a.order_quantity ELSE 0 END) AS 'projpoqty' from wo_po_color_size_breakdown as a, wo_po_break_down as b, wo_po_details_master as c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name in($manufacturing_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.country_ship_date like '".$val."-%"."' group by b.id, c.company_name, a.country_id";
                    }
                    else
                    {
                          $sql="select c.company_name, b.id as po_id, a.country_id, sum(CASE WHEN b.is_confirmed=1 THEN a.order_quantity ELSE 0 END) AS confpoqty, sum(CASE WHEN b.is_confirmed=2 THEN a.order_quantity ELSE 0 END) AS projpoqty from wo_po_color_size_breakdown a, wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name in($manufacturing_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and to_char(a.country_ship_date,'YYYY-MM-DD') like '".$val."-%"."' group by b.id, a.country_id, c.company_name";	 
                    }
                    //echo $sql;die;
                    $result=sql_select($sql);
                    $confPoQty_arr=array(); $projPoQty_arr=array(); $exFactoryQty_arr=array();
                    foreach($result as $row)
                    { 
                        $confPoQty_arr[$row[csf('company_name')]]+=$row[csf('confpoqty')]; 
                        $projPoQty_arr[$row[csf('company_name')]]+=$row[csf('projpoqty')];
                        $exFactoryQty_arr[$row[csf('company_name')]]+=$exFactory_arr[$row[csf('po_id')]][$row[csf('country_id')]];
                    }
					$x=0;
					foreach($company_arr as $company_id=>$comapny_name)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$totCompanyQty=$projPoQty_arr[$company_id]+$confPoQty_arr[$company_id];
						$comExFactoryQty=$exFactoryQty_arr[$company_id];
                    	$perc=($comExFactoryQty/$totCompanyQty)*100;
						
						if($projPoQty_arr[$company_id]>0) $projectQty=number_format($projPoQty_arr[$company_id],0); else $projectQty='&nbsp;';
						if($confPoQty_arr[$company_id]>0) $confirmQty=number_format($confPoQty_arr[$company_id],0); else $confirmQty='&nbsp;';
						if($totCompanyQty>0) $comQty=number_format($totCompanyQty,0); else $comQty='&nbsp;';
						if($comExFactoryQty>0) $exfactoryQty=number_format($comExFactoryQty,0); else $exfactoryQty='&nbsp;';
						
						if($x==0)
						{
							echo '<tr bgcolor="'.$bgcolor.'" onClick="change_color('."'tr_".$i."','".$bgcolor."'".')" id="tr_'.$i.'">';
							echo '<td width="80" align="center" valign="middle" rowspan="'.$no_of_company.'">'.date("M",strtotime($val))." '".date("y",strtotime($val)).'</td>
									<td width="130" style="word-break:break-all;">'.$comapny_name.'</td>
									<td width="115" align="right">'.$projectQty.'</td>
									<td width="115" align="right">'.$confirmQty.'</td>
									<td width="115" align="right">'.$comQty.'</td>
									<td width="115" align="right">'.$exfactoryQty.'</td>
									<td align="right">'.number_format($perc,2).'</td>';
							echo '</tr>';
						}
						else
						{
							echo '<tr bgcolor="'.$bgcolor.'" onClick="change_color('."'tr_".$i."','".$bgcolor."'".')" id="tr_'.$i.'">';
							echo '<td width="130" style="word-break:break-all;">'.$comapny_name.'</td>
									<td width="115" align="right">'.$projectQty.'</td>
									<td width="115" align="right">'.$confirmQty.'</td>
									<td width="115" align="right">'.$comQty.'</td>
									<td width="115" align="right">'.$exfactoryQty.'</td>
									<td align="right">'.number_format($perc,2).'</td>';
							echo '</tr>';
						}
						$i++;
						$x++;
						$monProjQty+=$projPoQty_arr[$company_id];
						$monConfQty+=$confPoQty_arr[$company_id];  
						$monExFactoryQty+=$comExFactoryQty; 
						$monTotQty+=$totCompanyQty;
						
						$totProjQty+=$projPoQty_arr[$company_id];
						$totConfQty+=$confPoQty_arr[$company_id];  
						$totExFactoryQty+=$comExFactoryQty; 
						$grandTotQty+=$totCompanyQty;
					}
					$month_perc=($monExFactoryQty/$monTotQty)*100;
					echo '<tr bgcolor="#FFEEFF">';
					echo '<td width="130" align="right"><b>Month Total&nbsp;&nbsp;</b></td>
							<td width="115" align="right"><b>'.number_format($monProjQty,0).'</b></td>
							<td width="115" align="right"><b>'.number_format($monConfQty,0).'</b></td>
							<td width="115" align="right"><b>'.number_format($monTotQty,0).'</b></td>
							<td width="115" align="right"><b>'.number_format($monExFactoryQty,0).'</b></td>
							<td align="right"><b>'.number_format($month_perc,2).'</b></td>';
					echo '</tr>';
                }
            ?>
		</table>
    </div>
    <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="770">
        <tfoot>
            <th width="80"></th>
            <th width="130">Total&nbsp;</th>
            <th width="115"><? echo number_format($totProjQty,0); ?></th>
            <th width="115"><? echo number_format($totConfQty,0); ?></th>
            <th width="115"><? echo number_format($grandTotQty,0); ?></th>
            <th width="115"><? echo number_format($totExFactoryQty,0); ?></th>
            <th><? $totPerc=($totExFactoryQty/$grandTotQty)*100; echo number_format($totPerc,2); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
        </tfoot>
    </table>  
<?
exit();
}

if($action=="summary_popup_value")
{
	echo load_html_head_contents("Popup Info","", 1, 1, $unicode,1,1);
	extract($_REQUEST); 
	?>
    <div style="width:770px;" align="center"><input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px" /></div>
	<div style="width:770px;margin-left:5px; margin-top:5px;">
        <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="770">
            <thead>
                <th width="80">Month</th>
                <th width="130">Company</th>
                <th width="115">Proj. Value</th>
                <th width="115">Conf. Value</th>
                <th width="115">Total Value</th>
                <th width="115">Ship Out Value</th>
                <th>%</th>
            </thead>
		</table>
        <div style="width:770px; overflow-y:scroll; max-height:310px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" id="tbl_list_search">
            <?
                $month_prev=add_month(date("Y-m-d",time()),-3);
                $month_next=add_month(date("Y-m-d",time()),8);
                
                $start_yr=date("Y",strtotime($month_prev));
                $end_yr=date("Y",strtotime($month_next));
                
				$manufacturing_company=''; $company_arr=array(); $no_of_company=1;
				$comData=sql_select("select comp.id as id, comp.company_name as company_name from lib_company comp where comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.id");
				foreach($comData as $row)
                {
					$no_of_company++;
                    $company_arr[$row[csf('id')]]=$row[csf('company_name')];
					$manufacturing_company.=$row[csf('id')].",";
                }
                $manufacturing_company=chop($manufacturing_company,',');
				
                $exFactory_arr=array();
                $data_arr=sql_select( "select po_break_down_id, country_id, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id, country_id");
                foreach($data_arr as $row)
                {
                    $exFactory_arr[$row[csf('po_break_down_id')]][$row[csf('country_id')]]=$row[csf('ex_factory_qnty')];
                }
                
                for($e=0;$e<=11;$e++)
                {
                    $tmp=add_month(date("Y-m-d",strtotime($month_prev)),$e);
                    $yr_mon_part[$e]=date("Y-m",strtotime($tmp));
                }
                $i=1; $totConfVal=0; $totProjVal=0; $totExFactoryVal=0; $grandTotVal=0;
                foreach($yr_mon_part as $key=>$val)
                {
					$monConfVal=0; $monProjVal=0; $monExFactoryVal=0; $monTotVal=0;
                    if($db_type==0) 
                    {
                        $sql="select c.company_name, c.total_set_qnty as ratio, b.id as po_id, b.unit_price, a.country_id, sum(CASE WHEN b.is_confirmed=1 THEN a.order_total ELSE 0 END) AS 'confpoval', sum(CASE WHEN b.is_confirmed=2 THEN a.order_total ELSE 0 END) AS 'projpoval' from wo_po_color_size_breakdown as a, wo_po_break_down as b, wo_po_details_master as c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name in($manufacturing_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.country_ship_date like '".$val."-%"."' group by b.id, c.company_name, a.country_id";
                    }
                    else
                    {
                          $sql="select c.company_name, c.total_set_qnty as ratio, b.id as po_id, b.unit_price, a.country_id, sum(CASE WHEN b.is_confirmed=1 THEN a.order_total ELSE 0 END) AS confpoval, sum(CASE WHEN b.is_confirmed=2 THEN a.order_total ELSE 0 END) AS projpoval from wo_po_color_size_breakdown a, wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name in($manufacturing_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and to_char(a.country_ship_date,'YYYY-MM-DD') like '".$val."-%"."' group by b.id, a.country_id, c.company_name,c.total_set_qnty,b.unit_price";	 
                    }
                    //if($key==2) echo $sql;//die;
                    $result=sql_select($sql);
                    $confPoVal_arr=array(); $projPoVal_arr=array(); $exFactoryVal_arr=array();
                    foreach($result as $row)
                    { 
                        $confPoVal_arr[$row[csf('company_name')]]+=$row[csf('confpoval')]; 
                        $projPoVal_arr[$row[csf('company_name')]]+=$row[csf('projpoval')];
						$unit_price=$row[csf('unit_price')]/$row[csf('ratio')];
                        $exFactoryVal_arr[$row[csf('company_name')]]+=$exFactory_arr[$row[csf('po_id')]][$row[csf('country_id')]]*$unit_price;
                    }
					$x=0;
					foreach($company_arr as $company_id=>$comapny_name)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$totCompanyVal=$projPoVal_arr[$company_id]+$confPoVal_arr[$company_id];
						$comExFactoryVal=$exFactoryVal_arr[$company_id];
                    	$perc=($comExFactoryVal/$totCompanyVal)*100;
						
						if($projPoVal_arr[$company_id]>0) $projectVal=number_format($projPoVal_arr[$company_id],2); else $projectVal='&nbsp;';
						if($confPoVal_arr[$company_id]>0) $confirmVal=number_format($confPoVal_arr[$company_id],2); else $confirmVal='&nbsp;';
						if($totCompanyVal>0) $comVal=number_format($totCompanyVal,2); else $comVal='&nbsp;';
						if($comExFactoryVal>0) $exfactoryVal=number_format($comExFactoryVal,2); else $exfactoryVal='&nbsp;';
						
						if($x==0)
						{
							echo '<tr bgcolor="'.$bgcolor.'" onClick="change_color('."'tr_".$i."','".$bgcolor."'".')" id="tr_'.$i.'">';
							echo '<td width="80" align="center" valign="middle" rowspan="'.$no_of_company.'">'.date("M",strtotime($val))." '".date("y",strtotime($val)).'</td>
									<td width="130" style="word-break:break-all;">'.$comapny_name.'</td>
									<td width="115" align="right">'.$projectVal.'</td>
									<td width="115" align="right">'.$confirmVal.'</td>
									<td width="115" align="right">'.$comVal.'</td>
									<td width="115" align="right">'.$exfactoryVal.'</td>
									<td align="right">'.number_format($perc,2).'</td>';
							echo '</tr>';
						}
						else
						{
							echo '<tr bgcolor="'.$bgcolor.'" onClick="change_color('."'tr_".$i."','".$bgcolor."'".')" id="tr_'.$i.'">';
							echo '<td width="130" style="word-break:break-all;">'.$comapny_name.'</td>
									<td width="115" align="right">'.$projectVal.'</td>
									<td width="115" align="right">'.$confirmVal.'</td>
									<td width="115" align="right">'.$comVal.'</td>
									<td width="115" align="right">'.$exfactoryVal.'</td>
									<td align="right">'.number_format($perc,2).'</td>';
							echo '</tr>';
						}
						$i++;
						$x++;
						$monProjVal+=$projPoVal_arr[$company_id];
						$monConfVal+=$confPoVal_arr[$company_id];  
						$monExFactoryVal+=$comExFactoryVal; 
						$monTotVal+=$totCompanyVal;
						
						$totProjVal+=$projPoVal_arr[$company_id];
						$totConfVal+=$confPoVal_arr[$company_id];  
						$totExFactoryVal+=$comExFactoryVal; 
						$grandTotVal+=$totCompanyVal;
					}
					$month_perc=($monExFactoryVal/$monTotVal)*100;
					echo '<tr bgcolor="#FFEEFF">';
					echo '<td width="130" align="right"><b>Month Total&nbsp;&nbsp;</b></td>
							<td width="115" align="right"><b>'.number_format($monProjVal,2).'</b></td>
							<td width="115" align="right"><b>'.number_format($monConfVal,2).'</b></td>
							<td width="115" align="right"><b>'.number_format($monTotVal,2).'</b></td>
							<td width="115" align="right"><b>'.number_format($monExFactoryVal,2).'</b></td>
							<td align="right"><b>'.number_format($month_perc,2).'</b></td>';
					echo '</tr>';
                }
            ?>
		</table>
    </div>
    <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="770">
        <tfoot>
            <th width="80"></th>
            <th width="130">Total&nbsp;</th>
            <th width="115"><? echo number_format($totProjVal,0); ?></th>
            <th width="115"><? echo number_format($totConfVal,0); ?></th>
            <th width="115"><? echo number_format($grandTotVal,0); ?></th>
            <th width="115"><? echo number_format($totExFactoryVal,0); ?></th>
            <th><? $totPerc=($totExFactoryVal/$grandTotVal)*100; echo number_format($totPerc,2); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
        </tfoot>
    </table>  
<?
exit();
}

function add_month($orgDate,$mon){
  $cd = strtotime($orgDate);
  $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,1,date('Y',$cd)));
  return $retDAY;
}
?>