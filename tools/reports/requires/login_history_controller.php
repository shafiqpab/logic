<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------
$user_name_library=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );


if ($action=="report_generate_login_history")  // Item Description wise Search
{
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$user_name=str_replace("'","",$cbo_user_name);
	$cbo_search=str_replace("'","",$cbo_search);
	$search_value=str_replace("'","",$search_value);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);

	if($user_name==0)
		$user_cond="";
	else
		$user_cond=" and user_id=$user_name";
		if($db_type==2)
		{
			if( $date_from==0 && $date_to==0 ) $log_date=""; else $log_date= "  login_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
		}
		else
		{
			if( $date_from==0 && $date_to==0 ) $log_date=""; else $log_date= "  login_date between '".$date_from."' and '".$date_to."'";
	
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
      <div style="width:880px;">
    
   	  <table width="880" cellspacing="0" cellpadding="0" border="0" rules="all" >
            <tr class="form_caption">
                <td colspan="8" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
            </tr>
           
     </table>
    <?php if($type==0)
    {
    	?>
	     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table" >
		        <thead>
		            <th width="40">SL</th>
		            <th width="80">Session</th>
		            <th width="80">IP</th>
		            <th width="80">User Name</th>
		            <th width="80">Log Date</th>
		            <th width="100">Login Time</th>
		            <th width="80">Logout Date</th>
		            <th width="150">Logout Time</th>
		            <th width="60">Duration</th>
		            <th>Login Status</th>
		        </thead>
	     </table>
	     <div style="width:880px; overflow-y:scroll; max-height:350px; font-size:12px; overflow-x:hidden;" id="scroll_body">
		     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table"  id="table_body" >
					<? 
					function date_deffer($start,$end)
					{
						$date_a = new DateTime($start);
						$date_b = new DateTime($end);
						$interval = date_diff($date_a,$date_b);
						if($start && $end)return $interval->format('%h:%i:%s');
					}
					$act_sql="select id,session_id,user_id from activities_history where user_id='".$row[csf('user_id')]."'";
					//echo $act_sql;die;
					//$act_sql="select id,session_id,user_id from activities_history ";
					$actArray=sql_select( $act_sql );
					$activity_arr=array();
					foreach($actArray as $row_data)
					{
						$activity_arr[$row_data[csf('user_id')]]['user']=$row_data[csf('session_id')];	
					}


					$user_data="select user_id,lan_ip,lan_mac,wan_ip,login_time,login_date,logout_time,logout_date,login_status from login_history where $log_date $user_cond $search_cond order by user_id,login_time DESC";
					$nameArray=sql_select( $user_data );
					$i=1; $log_status_arr=array( 0=>"success", 1=>"pc ip fail", 2=>"password" , 3=>"user", 4=>"proxy");
					foreach($nameArray as $row)
					{
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						//echo substr($row[csf('login_time')],9)
						
						 $login_time = date("h:i:s A",strtotime($row[csf('login_time')]));
						 $logout_time = date("h:i:s A",strtotime($row[csf('logout_time')]));
					
			    		 ?>
				        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
				           <td width="40" align="center"><? echo $i;?></td>
				           <td width="80" align="center"><p><? echo $activity_arr[$row[csf('user_id')]]['user']; ?></p></td>
				           
                           <td width="80"><p><? echo  $row[csf('lan_ip')];?></p></td>
                           
                           <td width="80"><p><? echo  $user_name_library[$row[csf('user_id')]];?></p></td>
				           <td align="center" width="80"><p><? echo ($row[csf('login_date')] == '0000-00-00' || $row[csf('login_date')] == '' ? '' : change_date_format($row[csf('login_date')]));?></p></td>
				           <td align="center" width="100"><p><? echo $login_time;?></p></td>
				           
				           <td width="80" align="center"><p><? echo ($row[csf('logout_date')] == '0000-00-00' || $row[csf('logout_date')] == '' || $row[csf('logout_date')] == '30-11--0001' ? 'Auto Time Out' : change_date_format($row[csf('logout_date')]));?></p></td>
				           <td width="150" align="center"><p><? echo $row[csf('logout_date')] == '0000-00-00' || $row[csf('logout_date')] == '' || $row[csf('logout_date')] == '30-11--0001' ? '' : $logout_time;?></p></td>
				           <td width="60" align="center">
					           	<p>
								   <? 
								   $duration =  date_deffer($row[csf('login_time')],$row[csf('logout_time')]);
						 			echo $row[csf('logout_date')] == '0000-00-00' || $row[csf('logout_date')] == '' || $row[csf('logout_date')] == '30-11--0001' ? '' : $duration;
								   ?>
					           </p>
				       		</td>
				           <td align="center"><p><? echo $log_status_arr[$row[csf('login_status')]]; ?></p></td>
				          
				        </tr>
				        <?
						$i++;
					}
					?>
			        
		        </table>
	    </div>
    
		<?
	}
	else
	{ 
		?>

		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table" >
	     	<thead>
	            <th width="40">SL</th>
	            <th width="100">User Name</th>
	            <th width="100">Log Date</th>
	            <th width="180">First Login Time</th>
	            <th width="180">Last Logout Time</th>
	            <th width="140">Duration</th>
	            <th>Login Status</th>
	        </thead>
	    </table>
	    <div style="width:880px; overflow-y:scroll; max-height:350px; font-size:12px; overflow-x:hidden;" id="scroll_body">
	     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table"  id="table_body" >

	     		<? 
			
				function date_deffer($start,$end)
				{
					$date_a = new DateTime($start);
					$date_b = new DateTime($end);
					$interval = date_diff($date_a,$date_b);
					if($start && $end) return $interval->format('%h:%i:%s');
					else return "0:0:0";
				}
				function get_hour_min_sec($sec){
					$sec=intval($sec);
					$hour=$sec/3600;
					$hour=intval($hour);
					$sec=$sec-$hour*3600;
					$min=$sec/60;
					$min=intval($min);
					$sec=$sec-$min*60;
					return $hour.':'.$min.":".$sec;
				}
				$act_sql="select id,session_id,user_id from activities_history where user_id='".$row[csf('user_id')]."'";
				//$act_sql="select id,session_id,user_id from activities_history ";
				$actArray=sql_select( $act_sql );
				$activity_arr=array();
				foreach($actArray as $row_data)
				{
					$activity_arr[$row_data[csf('user_id')]]['user']=$row_data[csf('session_id')];	
				}
				$user_data="select id,user_id,lan_ip,lan_mac,wan_ip,login_time,login_date,logout_time,logout_date,login_status from login_history where $log_date $user_cond $search_cond order by id";
				$nameArray=sql_select( $user_data );
				$i=1; $log_status_arr=array( 0=>"Success", 1=>"Pc ip fail", 2=>"Password" , 3=>"User", 4=>"Proxy");
				$data_array=array();
				foreach($nameArray as $row)
				{
					$data_array[$row[csf('login_date')]][$row[csf('id')]]['user_id']=$row[csf('user_id')];
					$data_array[$row[csf('login_date')]][$row[csf('id')]]['lan_ip']=$row[csf('lan_ip')];
					$data_array[$row[csf('login_date')]][$row[csf('id')]]['lan_mac']=$row[csf('lan_mac')];
					$data_array[$row[csf('login_date')]][$row[csf('id')]]['wan_ip']=$row[csf('wan_ip')];
					$data_array[$row[csf('login_date')]][$row[csf('id')]]['login_time']=$row[csf('login_time')];
					$data_array[$row[csf('login_date')]][$row[csf('id')]]['login_date']=$row[csf('login_date')];
					$data_array[$row[csf('login_date')]][$row[csf('id')]]['logout_time']=$row[csf('logout_time')];
					$data_array[$row[csf('login_date')]][$row[csf('id')]]['logout_date']=$row[csf('logout_date')];
					$data_array[$row[csf('login_date')]][$row[csf('id')]]['login_status']=$row[csf('login_status')];
				}

				$res_arr=array();
				foreach ($data_array as $login_date => $date_wise) {
					$first_login_time="";

					foreach ($date_wise as $id => $id_wise) {
						if(empty($first_login_time)){
							$first_login_time=$id_wise['login_time'];
						}
						
						$duration =date_deffer($id_wise['login_time'],$id_wise['logout_time']);	
			 			$time=explode(":",$duration);
				 		$res_arr[$login_date]['duration']+=$time[0]*3600+$time[1]*60+$time[2];
						$res_arr[$login_date]['user_id']=$id_wise['user_id'];
						$res_arr[$login_date]['login_time']=$first_login_time;
						if($id_wise['login_time']!=""){
							$res_arr[$login_date]['login']+=1;
						}
						if($id_wise['logout_time']!=""){
							$res_arr[$login_date]['logout_time']=$id_wise['logout_time'];
						}
						
						if($id_wise['logout_date'] != ""){
							$res_arr[$login_date]['logout_date']=$id_wise['logout_date'];
							$res_arr[$login_date]['logout']+=1;
						}
						
						$res_arr[$login_date]['login_status']=$id_wise['login_status'];

					}
				}
				
				
				foreach($res_arr as $login_date=>$data)
				{
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					$login_time = date("h:i:s A",strtotime($data['login_time']));
					$logout_time = date("h:i:s A",strtotime($data['logout_time']));
		    		 ?>
		     		<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
		     			<td width="40" align="center"><? echo $i;?></td>
		     			<td width="100" align="center">
		     				<p><? echo $user_name_library[$data['user_id']]; ?></p>
		     			</td>
		     			<td width="100" align="center"><p><? echo change_date_format($login_date);?></p></td>
		     			<td width="180" align="center"><p><?php echo $login_time; ?></p></td>
		     			<td width="180" align="center"><p><? echo $logout_time;?></p></td>
		     			<td width="140" align="center">
		     				<p style="font-size: 14px;">
		     					<?php 
		     						$duration=get_hour_min_sec($data['duration']);
		     						echo $duration;

		     					?>
		     				
		     				</p>
		     			</td>
		     			<td align="center"><p><?php echo $log_status_arr[$data['login_status']]; ?></p></td>

		     		</tr>
		     	 <?
				  $i++;
				}
				?>

	     	</table>
     	</div>

		<?	
	}

	echo "$total_data**$filename";

	exit();
}

?>