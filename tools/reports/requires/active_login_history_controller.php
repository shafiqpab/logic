<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------
$user_dep_library=return_library_array( "select id, department_name from lib_department", "id", "department_name"  );
$user_deg_library=return_library_array( "select id, custom_designation from  lib_designation", "id", "custom_designation"  );

		$user_sql="select id, user_name,department_id,designation from user_passwd";
		$actArray=sql_select( $user_sql );
		foreach($actArray as $row)
		{
			$user_name_arr[$row[csf('id')]]=$row[csf('user_name')];
			$user_deg_arr[$row[csf('id')]]=$user_deg_library[$row[csf('designation')]];
			$user_dep_arr[$row[csf('id')]]=$user_dep_library[$row[csf('department_id')]];
		}

if ($action=="report_generate_login_history")  // Item Description wise Search
{
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$user_name=str_replace("'","",$cbo_user_name);
	$cbo_search=str_replace("'","",$cbo_search);
	$search_value=str_replace("'","",$search_value);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$time_from=str_replace("'","",$txt_time_from);
	$time_to=str_replace("'","",$txt_time_to);
	if($user_name==0)
		$user_cond="";
	else
		$user_cond=" and user_id=$user_name";
		if($db_type==2)
		{
			if( $date_from==0 && $date_to==0 ) $log_date=""; else $log_date= "  login_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
		
			if($operation==1){$search_con=" and logout_date IS NULL";}//else{$search_con=" and logout_date IS NOT NULL";}
			if($time_from && $date_to){$time_con=" and login_time between '$date_to $time_from' and '$date_to $time_to'";}
		
		}
		else
		{
			if( $date_from==0 && $date_to==0 ) $log_date=""; else $log_date= "  login_date between '".$date_from."' and '".$date_to."'";
		if($operation==1){$search_con=" and logout_date='0000-00-00'";}//else{$search_con=" and logout_date !='0000-00-00'";}
		if($time_from && $date_to){$time_con=" and login_time between '".date("H:i:s A",strtotime($time_from))."' and '".date("H:i:s A",strtotime($time_to))."'";}
		}

	if($cbo_search==0)
		$search_cond="";
	else if($cbo_search==1)
		$search_cond=" and lan_ip like '%$search_value%'";
	else if($cbo_search==2)
		$search_cond=" and lan_mac like '%$search_value%'";
	else if($cbo_search==3)
		$search_cond=" and wan_ip like '%$search_value%'";
	 
	
	//echo $user_name;die;
	ob_start();	
	
	?>
      <div style="width:1040px;">
    
   	  <table width="1040" cellspacing="0" cellpadding="0" border="0" rules="all" >
            <tr class="form_caption">
                <td colspan="8" align="center" style="border:none;font-size:16px; font-weight:bold"> <? 
					if($operation==1){echo $report_title;}else{echo "All Login History";}
				 ?></td>
            </tr>
           
     </table>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1040" class="rpt_table" >
        <thead>
            <th width="40">SL</th>
            <th width="80">Lan IP</th>
            <th width="80">User Name</th>
            <th width="80">Designation</th>
            <th width="80">Department</th>
            <th width="80">Log Date</th>
            <th width="140">Login Time</th>
            <th width="80">Log Out Date</th>
            <th width="140">Login Out Time</th>
            <th width="140">Duration</th>
            <th>Login Status</th>
           
           
         
        </thead>
     </table>
     <div style="width:1040px; overflow-y:scroll; max-height:350px; font-size:12px; overflow-x:hidden;" id="scroll_body">
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1040" class="rpt_table"  id="table_body" >
	<? 
		
function time_elapsed($secs){
    $bit = array(
        ' year'	=> $secs / 31556926 % 12,
        ' week'	=> $secs / 604800 % 52,
        ' day'	=> $secs / 86400 % 7,
        ' hour'	=> $secs / 3600 % 24,
        'm'    => $secs / 60 % 60,
        's'    => $secs % 60
        );
       
    foreach($bit as $k => $v){
        if($v > 1)$ret[] = $v . $k . '';
        if($v == 1)$ret[] = $v . $k;
        }
    array_splice($ret, count($ret)-1, 0, ' ');
    $ret[] = '';
   
    return join(' ', $ret);
    }					
		
		$act_sql="select user_id,lan_ip,lan_mac,wan_ip,login_time,login_date,logout_time,logout_date,login_status from login_history where $log_date $user_cond $search_cond $search_con $time_con order by id,login_time ASC";
		//$act_sql="select id,session_id,user_id from activities_history ";
		$actArray=sql_select( $act_sql );
		$activity_arr=array();
		foreach($actArray as $row_data)
		{
		$activity_arr[$row_data[csf('user_id')]]=array(
				'user_id'=>$row_data[csf('user_id')],
				'lan_ip'=>$row_data[csf('lan_ip')],
				'lan_mac'=>$row_data[csf('lan_mac')],
				'wan_ip'=>$row_data[csf('wan_ip')],
				'login_time'=>$row_data[csf('login_time')],
				'login_date'=>$row_data[csf('login_date')],
				'logout_time'=>$row_data[csf('logout_time')],
				'logout_date'=>$row_data[csf('logout_date')],
				'login_status'=>$row_data[csf('login_status')]
			);	
		}
		
		
		//$user_data="select user_id,lan_ip,lan_mac,wan_ip,login_time,login_date,logout_time,logout_date,login_status from login_history where $log_date $user_cond $search_cond and logout_date IS NULL";
		
		
		//$nameArray=sql_select( $user_data );
		$i=1; $log_status_arr=array( 0=>"success", 1=>"pc ip fail", 2=>"password" , 3=>"user", 4=>"proxy");
		foreach($activity_arr as $row)
		{
		$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
		//echo substr($row[csf('login_time')],9)
		
		 $login_time = date("H:i:s A",strtotime($row['login_time']));
		 $logout_time = date("H:i:s A",strtotime($row['logout_time']));
		
     ?>
        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
           <td width="40" align="center"><? echo $i;?></td>
           <td width="80" align="center"><p><? echo $row['lan_ip']; ?></p></td>
           <td width="80"><p><? echo  $user_name_arr[$row['user_id']];?></p></td>
           <td width="80"><p><? echo  $user_deg_arr[$row['user_id']];?></p></td>
           <td width="80"><p><? echo  $user_dep_arr[$row['user_id']];?></p></td>
          
           <td align="center" width="80"><p><? echo ($row['login_date'] == '0000-00-00' || $row['login_date'] == '' ? '' : change_date_format($row['login_date']));?></p></td>
           <td align="center" width="140"><p><? echo date('h:i:s a',strtotime( $row['login_time'])); ?></p></td>
           
           <td align="center" width="80"><p><? echo ($row['logout_date'] == '0000-00-00' || $row['logout_date'] == '' ? '' : change_date_format($row['logout_date']));?></p></td>
           <td align="center" width="140"><p><?
		    echo ($row['logout_time'] == '0000-00-00' || $row['logout_time'] == '')? '' : date('h:i:s a',strtotime( $row['logout_time']));
			    ?></p></td>
           
           <td width="140" align="center"><p>
		   <?
				if($operation==1){$nowtime = time();}else{$nowtime = strtotime( $row['logout_date'].' '.$row['logout_time']);}
				$oldtime = strtotime( $row['login_time']);
				echo time_elapsed($nowtime-$oldtime);		   
		   ?>
           </p>
           </td>
           <td align="center"><p><? echo $log_status_arr[$row['login_status']]; ?></p></td>
          
        </tr>
        <? 
		$i++;
		}
		?>
        
        </table>
    </div>
    
<?

	echo "$total_data**$filename";

	exit();
}

?>