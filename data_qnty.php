
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
include('includes/common.php');

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
		//$sql_comp=sql_select("select * from lib_company where core_business=1 and status_active=1 and is_deleted=0 $company_name order by id asc");
		$sql_comp=sql_select("select comp.id as id, comp.company_name from lib_company comp where comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.id asc");
		foreach($sql_comp as $row_comp)
		{
			echo "<graph gid='$k'>";
			for($i=0;$i<=11;$i++)
			{
				$qntty=0;
				//echo "select sum(wp.po_quantity) as pQuantity from wo_po_break_down as wp, wo_po_details_master as wm where wp.job_no_mst=wm.job_no   and wm.company_name like '$row_comp[id]' and wp.pub_shipment_date like '".$yr_mon_part[$i]."%"."'  and wp.is_deleted=0 and wp.status_active=1 $buyer_name group by wm.buyer_name"."<br>";
				/*$company_sql3= sql_select("select sum(wp.po_quantity*wm.total_set_qnty) as pQuantity from wo_po_break_down as wp, wo_po_details_master as wm where wp.job_no_mst=wm.job_no and wm.company_name like '$row_comp[id]' and wp.pub_shipment_date like '".$yr_mon_part[$i]."%"."' and wp.is_deleted=0 and wp.status_active=1 and wm.is_deleted=0 and wm.status_active=1 $buyer_name group by wm.buyer_name");
				foreach ($company_sql3 as $row_po_bd)  // Master Job  table queery ends here
				{
					$qntty= $qntty+$row_po_bd['pQuantity'];
				}*/
				
				if($db_type==0) $year_field="a.country_ship_date"; else $year_field="to_char(a.country_ship_date,'YYYY-MM-DD')";
				
				$sql="select sum(a.order_quantity) AS pquantity from wo_po_color_size_breakdown a, wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name='".$row_comp[csf('id')]."' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and $year_field like '".$yr_mon_part[$i]."-%"."'";
				$row=sql_select($sql);
				$qntty=$row[0][csf('pquantity')];
				
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