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
 // echo $month_prev;
 //$month_prev=$date_month-3;
 //$month_next=$date_month+9;
 
	echo "<series>";
		
	for ($i=0; $i<=11; $i++)
	{
		echo "<value xid='$i'>".date("M",strtotime($yr_mon_part[$i]))." '".date("y",strtotime($yr_mon_part[$i]))."</value>";
	}
	echo "</series>";	
	echo "<graphs>";		
			$fdata="sum(wp.po_total_price)";
			$tdata="wo_po_break_down wp, wo_po_details_master wm";
			$fdata_p="sum(value)";
			$tdata_p="wo_projected_order_mst";	
			
		 $k=1;
		$sql_comp="select * from lib_company where core_business=1 and status_active=1 and is_deleted=0 $company_name order by id asc";
		$rs=mysql_query($sql_comp);
		while($row_comp=mysql_fetch_array($rs))
		{
			echo "<graph gid='$k'>";
			for($i=0;$i<=11;$i++)
			{
				$value=0;
				/*$company_sql3= mysql_db_query($DB, "select sum(wp.po_total_price) as tValue ,lib_buyer.buyer_name as buyer_name_display,lib_company.company_name as company_name,wm.company_name as com_id, wm.buyer_name as buyer_id from wo_po_break_down as wp, wo_po_details_master as wm,lib_buyer,lib_company where wp.job_no_mst=wm.job_no  and wm.company_name=lib_company.id and wm.company_name like '$row_comp[id]' and wp.shipment_date like '".$yr_mon_part[$i]."%"."' and wm.buyer_name=lib_buyer.id and wp.is_deleted=0 and wp.status_active=1 and wm.is_deleted=0 and wm.status_active=1 $buyer_name group by wm.buyer_name");
				// $company_sql3= mysql_db_query($DB, "select sum(wp.po_quantity * wp.unit_price) as tValue ,lib_buyer.buyer_name as buyer_name_display,lib_company.company_name as company_name,wm.company_name as com_id, wm.buyer_name as buyer_id from wo_po_break_down as wp, wo_po_details_master as wm,lib_buyer,lib_company where wp.job_no_mst=wm.job_no  and wm.company_name=lib_company.id and wm.company_name like '$row_comp[id]' and wp.shipment_date like '".$yr_mon_part[$i]."%"."' and wm.buyer_name=lib_buyer.id and wp.is_countable=0 and wp.is_deleted=0 and wp.status_active=1 $buyer_name group by wm.buyer_name");
				while ($row_po_bd=mysql_fetch_array($company_sql3))  // Master Job  table queery ends here
				{
				  $value= $value+$row_po_bd['tValue'];
				  //$qntty=$row_po_bd['pQuantity'];
				}*/
				
				
				// $cdata="wp.job_no_mst=wm.job_no and wp.shipment_date like '".$yr_mon_part[$i]."%"."' and wp.working_company like '$row_comp[id]'    and wp.is_deleted=0 and wp.status_active=1";
				  //$cdata_p="shipment_date like '".$yr_mon_part[$i]."%"."' and company_name like '$row_comp[id]' and is_confirmed=0 and is_deleted=0 and status_active=1 $buyer_name_bl";
				 
				 
				/* 
				$cdata_set=mysql_db_query($DB, "select a.*,b.*,c.gmt_item_name from wo_po_break_down a, wo_po_set_details_info b,lib_garments_item c where b.set_item_name=c.id and a.job_no_mst=b.job_no and b.set_item_name=a.wo_po_item_id  and a.is_deleted=0 and a.shipment_date like '".$yr_mon_part[$i]."%"."' and a.working_company like '$row_comp[id]' order by a.id");
				while ($row_po=mysql_fetch_array($cdata_set))  // Master Job  table queery ends here
				{
					 
					//$qntty=$qntty+($row_po['po_quantity']*$row_po['quantity']);
					$value=$value+($row_po[po_quantity]*$row_po[set_pc_rate]*$row_po[quantity]);
					//$tot_price=  echo number_format($tot_price,2,'.','');
				} 
				 
				  $total=  $value +return_field_value($fdata_p,$tdata_p,$cdata_p);*/
				  
				 $sql="select sum(a.order_total) AS 'povalue' from wo_po_color_size_breakdown as a, wo_po_break_down as b, wo_po_details_master as c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name like '$row_comp[id]' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.country_ship_date like '".$yr_mon_part[$i]."%"."'";
				 $result=mysql_query($sql);
				 $row=mysql_fetch_array($result);
				 $value=$row[csf('povalue')];
				
				 $total=$value;
				 
				 echo "<value xid='$i'>"; echo number_format($total,0,'.','');
				 echo "</value>";
			}
			//echo number_format((return_field_value($fdata,$tdata,$cdata)+return_field_value($fdata_p,$tdata_p,$cdata_p)),0,'.','');
			echo "</graph>";
			$k++;
		}
			 
			
		echo "</graphs>";
	echo "</chart>";
	

function return_field_value($fdata,$tdata,$cdata){

$sql_data="select $fdata from  $tdata where $cdata";
$sql_data_exe=mysql_query($sql_data);
$sql_data_rslt=mysql_fetch_array($sql_data_exe);
$m_data  = $sql_data_rslt[0];

return $m_data ;

}

function add_month($orgDate,$mon){
  $cd = strtotime($orgDate);
  //$retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,date('d',$cd),date('Y',$cd)));
  $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,1,date('Y',$cd)));
  return $retDAY;
}

?>