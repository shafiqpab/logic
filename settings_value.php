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

 
echo " <settings><data_type>csv</data_type><plot_area><margins><left>150</left><right>40</right><top>50</top><bottom>50</bottom></margins></plot_area><grid><category><dashed>1</dashed><dash_length>4</dash_length></category><value><dashed>1</dashed><dash_length>4</dash_length></value></grid><axes><category><width>1</width><color>E7E7E7</color></category><value><width>1</width><color>E7E7E7</color></value></axes><values><value><min>0</min></value></values><legend><enabled>0</enabled></legend><angle>0</angle><column><width>85</width><balloon_text>{title}: {value} USD</balloon_text><grow_time>3</grow_time><sequenced_grow>1</sequenced_grow></column><graphs>"; 
$i=0;
$capacity="";
$rs=sql_select("select comp.id as id, comp.company_name,company_short_name from lib_company comp where comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.id asc");
foreach($rs as $row_comp)
{
//	$i++;
	$val=return_field_value("capacity_in_value", "variable_settings_commercial", "company_name like '".$row_comp[csf('id')]."' and variable_list=5");
	if ($capacity!="")$capacity=$capacity.", ".$row_comp[csf('company_short_name')].": $ ".number_format($val,2,'.',',');
	else $capacity="Capacity: ".$row_comp[csf('company_short_name')].": $ ".number_format($val,2,'.',',');
	for($i=0;$i<=11;$i++)
	{
		echo "<graph gid='$i'><title>".$row_comp[csf('company_name')]." </title><color>".$row_comp[csf('graph_color')]."</color> </graph>";
	}
}
	echo "</graphs><labels><label><text>".$capacity."</text><y>12</y><text_color>4D4D4D</text_color><text_size>12</text_size><align>center</align></label></labels></settings>";
 

?>

 