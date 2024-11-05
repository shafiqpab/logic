<?
session_start();
include('../../includes/common.php');
include('../../includes/array_function.php');
include('../../includes/common_functions.php');

extract($_GET);
extract($_POST);

$login_status=array(0=>"Success",1=>"LAN IP Error",4=>"WAN IP Error");
//User 0=success, 1=pc ip fail, 2=password , 3= user, 4=proxy
$query_type=array(1=>"New Insert",2=>"Update/Edit",3=>"Delete");

$sql = "select id,user_name from user_passwd where valid=1  order by user_name";
$result = mysql_query( $sql ) or die( $sql . "<br />" . mysql_error() );

$user_name = array();
while( $row = mysql_fetch_assoc( $result ) ) {
	$user_name[$row['id']] =$row['user_name'];
}

$sql = "select m_menu_id,menu_name from main_menu where status=1  order by menu_name";
$result = mysql_query( $sql ) or die( $sql . "<br />" . mysql_error() );

$main_menu = array();
while( $row = mysql_fetch_assoc( $result ) ) {
	$main_menu[$row['m_menu_id']] =$row['menu_name'];
}


if ($action=="login_history_report")
{
	if($cbo_user_name==0)
		$user_cond="";
	else
		$user_cond=" and user_id=$cbo_user_name";
	
	if($cbo_search_by==0)
		$ext_cond="";
	else if($cbo_search_by==1)
		$ext_cond=" and lan_ip like '%$search_value%'";
	else if($cbo_search_by==2)
		$ext_cond=" and lan_mac like '%$search_value%'";
	else if($cbo_search_by==3)
		$ext_cond=" and wan_ip like '%$search_value%'";
	 
	 
	$txt_date_from=convert_to_mysql_date($txt_date_from);
	$txt_date_to=convert_to_mysql_date($txt_date_to);
						 
	?>
    <div style="width:1090px" >
    		<table width="1060" cellpadding="0" cellspacing="0" >
            <tr>
            	<td colspan="9" align="center">
                    	<font size="3"><strong>Login Report</strong></font>
                 </td>
             </tr>
            </table>
        	<table width="1060" border="1" rules="all"  class="rpt_table">
                <thead> 
                    <th width="60">Session</th>
                	<th width="130">User Name</th>
                    <th width="100">Log Date</th>
                    <th width="100">Login Time</th>
                    <th width="100">Logout Date</th> 
                    <th width="100">Logout Time</th> 
                    <th width="100">Login Status</th>
                    <th width="100">LAN IP</th>
                    <th width="100">WAN IP</th>
                    <th>LAN MAC</th>
                </thead>
            </table>
        	<div style="overflow-y:scroll; max-height:400px; width:1080px" >
            	<table width="1060" border="1"  class="rpt_table" rules="all">	
                	<? 
						$i=0;
				
					$company_sql= mysql_db_query($DB, "select * from login_history where login_date between '$txt_date_from' and '$txt_date_to' $user_cond $ext_cond order by login_time asc");
			
					while ($row=mysql_fetch_array($company_sql))  // Master Job  table queery ends here
					{
						$i++;
						if ($i%2==0)  
								$bgcolor="#D8CEEC";
							else
								$bgcolor="#FFFFFF";	
								?>
                                
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="60"> 
                            <? echo $row['id'];?> 
                           
                            </td>
                            <td width="130" >
                                <? 
                                echo $row['user_name'];
                                ?>
                            </td>
                            <td width="100">
                                <? echo convert_to_mysql_date($row[login_date]); ?>
                            </td>
                            <td width="100">
                                <? echo $row[login_time]; ?>
                            </td>
                            <td width="100">
                                <? echo convert_to_mysql_date($row[logout_date]); ?>
                            </td>
                            <td width="100">
                                <? echo $row[logout_time]; ?>
                            </td> 
                            <td width="100">
                                <? echo $login_status[$row[login_status]]; ?>
                            </td> 
                            <td width="100">
                                <? echo $row[lan_ip]; ?>
                            </td> 
                            <td width="100">
                                <? echo $row[wan_ip]; ?>
                            </td> 
                            <td>
                                <? echo $row[lan_mac]; ?>
                            </td> 
                       </tr>
                        <? } ?>
                </table>
            </div>				 
	
	
<?	
echo "####2";
 
}


if ($action=="activities_history_report")
{
	$DB_SERVER_back		= "localhost";		// Database Server ID
	$DB_LOGIN_back		= "root";			// Database UserName
	$DB_PASSWORD_back	= "";				// Database Password
	$DB_back				= "logic_backup";		// Database containing the tables

$link_back = mysql_pconnect( $DB_SERVER_back, $DB_LOGIN_back, $DB_PASSWORD_back );
mysql_select_db( $DB_back );


	if($cbo_user_name==0)
		$user_cond="";
	else
		$user_cond=" and user_id=$cbo_user_name";
	
	if($cbo_search_by==0)
		$ext_cond="";
	else if($cbo_search_by==1)
		$ext_cond=" and query_type like '%$cbo_search_by%'";
	else if($cbo_search_by==2)
		$ext_cond=" and query_type like '%$cbo_search_by%'";
	else if($cbo_search_by==3)
		$ext_cond=" and query_type like '%$cbo_search_by%'";
	 
	if ($cbo_mdule_name==0)
		$module_name="";
	else
		$module_name=" and module_name=$cbo_mdule_name";
	
	if ($cbo_menu_name==0)
		$menu_name="";
	else
		$menu_name=" and form_name=$cbo_menu_name";
		
	$txt_date_from=convert_to_mysql_date($txt_date_from);
	$txt_date_to=convert_to_mysql_date($txt_date_to);

	?>
    <div style="width:1090px" >
    		<table width="1060" cellpadding="0" cellspacing="0" >
            <tr>
            	<td colspan="9" align="center">
                    	<font size="3"><strong>Login Report</strong></font>
                 </td>
             </tr>
            </table>
        	<table width="1060" border="1" rules="all"  class="rpt_table">
                <thead> 
                    <th width="60">SL</th>
                    <th width="80">Session</th>
                	<th width="100">Entry Date</th>
                    <th width="100">Entry  Time	</th>
                    <th width="100">Page Name</th> 
                    <th width="100">Operation</th>
                    <th>Qeury Details</th>
                     
                </thead>
            </table>
        	<div style="overflow-y:scroll; max-height:400px; width:1080px" >
            	<table width="1060" border="1"  class="rpt_table" rules="all">	
                	<? 
						$i=0;
				// echo "select * from activities_history where user_id=$cbo_user_name $module_name $menu_name  $ext_cond";
					$company_sql= mysql_db_query($DB_back, "select * from activities_history where user_id=$cbo_user_name $module_name $menu_name $ext_cond ");
			
					while ($row=mysql_fetch_array($company_sql))  // Master Job  table queery ends here
					{
						$i++;
						if ($i%2==0)  
								$bgcolor="#D8CEEC";
							else
								$bgcolor="#FFFFFF";	
								?>
                                
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="60"> 
                            <? echo $i; ?> 
                           
                            </td>
                            <td width="80"> 
                            <? echo $row['session_id'];?> 
                           
                            </td>
                            <td width="100" >
                                <? 
                                echo convert_to_mysql_date($row[entry_date]);
                                ?>
                            </td>
                            <td width="100">
                                <? echo $row[entry_time]; ?>
                            </td>
                            <td width="100">
                                <? echo $main_menu[$row[form_name]]; ?>
                            </td>
                            <td width="100">
                                <? echo $query_type[$row[query_type]]; ?>
                            </td>
                            <td >
                                <? echo str_replace(",",", ",decrypt($row[query_details], "logic_erp_2011_2012")) ; ?>
                            </td> 
                             
                       </tr>
                        <? } ?>
                </table>
            </div>				 
	
	
<?	
echo "####2";
 
}


?>