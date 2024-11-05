<?
session_start();

if ($_SESSION['logic_erp']["data_level_secured"]==1) 
{
	if ($_SESSION['logic_erp']["buyer_id"]!=0) $buyer_name=" and id=".$_SESSION['logic_erp']["buyer_id"]; else $buyer_name="";
	if ($_SESSION['logic_erp']["company_id"]!=0) $company_name="and id=".$_SESSION['logic_erp']["company_id"]; else $company_name="";
}
else
{
	$buyer_name="";
	$company_name="";
}
include('includes/common.php');
 
echo "<settings><background><alpha>2000</alpha><border_alpha>20</border_alpha><color>FAFAFA</color></background><grid><category><dashed>1</dashed></category><value><dashed>1</dashed></value></grid><axes><category><width>1</width><color>000000</color></category><value><width>1</width><color>000000</color></value></axes><values><value><min>0</min></value></values><depth>15</depth><column><width>85</width><balloon_text>{title}: {value} USD</balloon_text><grow_time>3</grow_time></column><graphs>"; // 80CFE2
$i=0;
 $capacity="";
$rs=mysql_query("select * from lib_company where core_business=1 and status_active=1 and is_deleted=0 $company_name order by id asc");
$num=mysql_num_rows($rs);
while($row_comp=mysql_fetch_array($rs))
{
	$i++;
	$val=return_field_value("capacity_in_value", "variable_settings_commercial", "company_name like '$row_comp[id]' and variable_list=5");
	if ($capacity!="")$capacity=$capacity.", ".$row_comp[company_name].": $ ".number_format($val,2,'.',',');
	else $capacity="Capacity: ".$row_comp[company_name].": $ ".number_format($val,2,'.',',');
	echo "<graph gid='$i'><title>".$row_comp[company_name]." </title><color>".$row_comp[graph_color]."</color> </graph>";
}
	echo "</graphs><labels><label><text>".$capacity."</text><y>12</y><text_color>4D4D4D</text_color><text_size>12</text_size><align>center</align></label></labels></settings>";

function return_field_value($fdata,$tdata,$cdata){

$sql_data="select $fdata from  $tdata where $cdata";
$sql_data_exe=mysql_query($sql_data);
$sql_data_rslt=mysql_fetch_array($sql_data_exe);
$m_data  = $sql_data_rslt[0];

return $m_data ;

}

?>

 