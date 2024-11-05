
<? 

session_start();
if ($_SESSION['logic_erp']["data_level_secured"]==1) 
{
	if ($_SESSION['logic_erp']["buyer_id"]!=0) $buyer_name=" and wm.buyer_name=".$_SESSION['logic_erp']["buyer_id"]; else $buyer_name="";
	if ($_SESSION['logic_erp']["buyer_id"]!=0) $buyer_name_bl=" and buyer_name=".$_SESSION['logic_erp']["buyer_id"]; else $buyer_name_bl="";
	if ($_SESSION['logic_erp']["company_id"]!=0) $company_name="and id=".$_SESSION['logic_erp']["company_id"]; else $company_name="";
}
else
{
	$buyer_name="";
	$company_name="";
	$buyer_name_bl="";
}
include('../../includes/common.php');

 echo '<?xml version="1.0" encoding="UTF-8"?>';
echo "<chart>";

$date=date("Y",time());
$month_prev=add_month(date("Y-m-d",time()),-3);
//echo $month_prev;
$month_next=add_month(date("Y-m-d",time()),8);
//echo $month_next;

$start_yr=date("Y",strtotime($month_prev));
$end_yr=date("Y",strtotime($month_next));
for($e=0;$e<=11;$e++)
{
	$tmp=add_month(date("Y-m-d",strtotime($month_prev)),$e);
	$yr_mon_part[$e]=date("Y-m",strtotime($tmp));
	 //echo "<br>$yr_mon_part[$i]";
}
		 
		
		
		
$date_month=date("m",time());
	echo "<series>";
		
	for ($i=0; $i<=11; $i++)
	{
		echo "<value xid='$i'>".date("M",strtotime($yr_mon_part[$i]))." '".date("y",strtotime($yr_mon_part[$i]))."</value>";
	}
	echo "</series>";	
	echo "<graphs>";		
			 	
			
		 $k=1;
		$sql_comp=sql_select("select * from lib_company where core_business=1 and status_active=1 and is_deleted=0 $company_name order by id asc");
		foreach($sql_comp as $row_comp)
		{
			echo "<graph gid='$k'>";
			for($i=0;$i<=11;$i++)
			{
				$qntty=0;
				$company_sql3= sql_select("select sum(wp.po_quantity) as pQuantity from wo_po_break_down as wp, wo_po_details_master as wm where wp.job_no_mst=wm.job_no   and wm.company_name like '$row_comp[id]' and wp.shipment_date like '".$yr_mon_part[$i]."%"."'  and wp.is_deleted=0 and wp.status_active=1 $buyer_name group by wm.buyer_name");
				foreach ($company_sql3 as $row_po_bd)  // Master Job  table queery ends here
				{
					$qntty= $qntty+$row_po_bd['pQuantity'];
				}
				echo "<value xid='$i'>"; echo number_format( $qntty,0,'.',''); echo "</value>";
			}
			
			echo "</graph>";
			$k++;
		}
		echo "</graphs>";
	echo "</chart>";
	 
function add_month($orgDate,$mon){
  $cd = strtotime($orgDate);
  //$retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,date('d',$cd),date('Y',$cd)));
  $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,1,date('Y',$cd)));
  return $retDAY;
}

?>