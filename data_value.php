
<? 

session_start();
/*if ($_SESSION['logic_erp']["data_level_secured"]==1) 
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
}*/
include('includes/common.php');

//echo '<?xml version="1.0" encoding="UTF-8"';
//echo "<chart>";

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
	//echo "<series>";
		
	//for ($i=0; $i<=11; $i++)
	//{
		//echo "<value xid='$i'>".date("M",strtotime($yr_mon_part[$i]))." '".date("y",strtotime($yr_mon_part[$i]))."</value>";
	//}
	//echo "</series>";	
	//echo "<graphs>";		
			 	
			
		$k=1;
		//$sql_comp=sql_select("select * from lib_company where core_business=1 and status_active=1 and is_deleted=0 $company_name order by id asc");
		
			
			for($i=0;$i<=11;$i++)
			{
				
				$data .=date("M",strtotime($yr_mon_part[$i]))."; ";
				//"21 Jan;91;96;69\n22 Jan;87;112;101\n23 Jan;68;79;66\n24 Jan;30;32;23\n25 Jan;52;57;41"
				
				$sql_comp=sql_select("select comp.id as id, comp.company_name from lib_company comp where comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.id asc");
		 		$k=1;
				foreach($sql_comp as $row_comp)
				{
					$value=0;
					if($db_type==0) $year_field="a.country_ship_date"; else $year_field="to_char(a.country_ship_date,'YYYY-MM-DD')";
					
					$sql="select sum(a.order_total) AS povalue from wo_po_color_size_breakdown a, wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name='".$row_comp[csf('id')]."' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and $year_field like '".$yr_mon_part[$i]."-%"."'";
					$row=sql_select($sql);
					$value=$row[0][csf('povalue')];
					if(count($sql_comp)!=$k) $data .=number_format( $value,0,'.','').";"; else $data .=number_format( $value,0,'.','')."";
					$k++;
				}
				if( $i!=11) $data .="\\n";
				//echo "<value xid='$i'>"; echo number_format( $value,0,'.',''); echo "</value>";
			}
			
			//echo "</graph>";
			
		 
		//echo "</graphs>";
	echo $data;
	 
function add_month($orgDate,$mon){
  $cd = strtotime($orgDate);
  //$retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,date('d',$cd),date('Y',$cd)));
  $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,1,date('Y',$cd)));
  return $retDAY;
}

?>