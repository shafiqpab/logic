<?php
date_default_timezone_set("Asia/Dhaka");
if(date('D')!='Sat'){exit('This mail will be send only Saturday');}


require_once('../includes/common.php');
// require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');

$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s", $strtotime),0))),'','',1);
$previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-7 day', strtotime($current_date))),'','',1);
 
$date_cond	=" login_date between '".$previous_date."' and '".$current_date."'";
	 
ob_start();

$i=1;
?>
  
<table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" >
	<caption><b>Login History Weekly Report</b></caption>
		        <thead>
		            <th width="40">SL</th>
		            <th width="200">User Name</th>
		            <th width="180">Log Date</th>
		            <th width="180">First Login Time</th>
		            <th width="180">Last Logout Time</th>
		            <th width="">Duration</th>
		            
		        </thead>
	     </table>
   
        <div style="width:900px; overflow-y:scroll; max-height:450px; font-size:12px; overflow-x:hidden;" id="scroll_body">
		     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table"  id="table_body" >
					<? 
					function date_deffer($start,$end)
					{
						$date_a = new DateTime($start);
						$date_b = new DateTime($end);
						/*echo $date_b;die;*/
						$interval = date_diff($date_a,$date_b);
						if($start && $end)return $interval->format('%h:%i:%s');
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
					


					/*$user_data="select id,user_id,lan_ip,lan_mac,wan_ip,login_time,login_date,logout_time,logout_date,login_status from login_history where login_date between '02-JAN-2020' and '09-JAN-2020' order by user_id,login_time";*/ //test with this. no data available in august 2020
					$user_data="select id,user_id,lan_ip,lan_mac,wan_ip,login_time,login_date,logout_time,logout_date,login_status from login_history where $date_cond order by user_id,login_time";

					$user_name_library=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );

					/*echo $user_data;die;*/
					$nameArray=sql_select( $user_data );
					$user_wise_data=array();
					$row_span=array();
					
					foreach($nameArray as $row)
				    {
						$data_array[$row[csf('user_id')]][$row[csf('login_date')]][$row[csf('id')]]['user_id']=$row[csf('user_id')];
						$data_array[$row[csf('user_id')]][$row[csf('login_date')]][$row[csf('id')]]['lan_ip']=$row[csf('lan_ip')];
						$data_array[$row[csf('user_id')]][$row[csf('login_date')]][$row[csf('id')]]['lan_mac']=$row[csf('lan_mac')];
						$data_array[$row[csf('user_id')]][$row[csf('login_date')]][$row[csf('id')]]['wan_ip']=$row[csf('wan_ip')];
						$data_array[$row[csf('user_id')]][$row[csf('login_date')]][$row[csf('id')]]['login_time']=$row[csf('login_time')];
						$data_array[$row[csf('user_id')]][$row[csf('login_date')]][$row[csf('id')]]['login_date']=$row[csf('login_date')];
						$data_array[$row[csf('user_id')]][$row[csf('login_date')]][$row[csf('id')]]['logout_time']=$row[csf('logout_time')];
						$data_array[$row[csf('user_id')]][$row[csf('login_date')]][$row[csf('id')]]['logout_date']=$row[csf('logout_date')];
						$data_array[$row[csf('user_id')]][$row[csf('login_date')]][$row[csf('id')]]['login_status']=$row[csf('login_status')];
				   }

				   $res_arr=array();
				   
				foreach ($data_array as $user_id => $user_wise)
				{
					foreach ($user_wise as $login_date => $date_wise)
					{
						
					
						$first_login_time="";
						
						foreach ($date_wise as $id => $id_wise) {
							if(empty($first_login_time)){
								$first_login_time=$id_wise['login_time'];
							}
							
							$duration =date_deffer($id_wise['login_time'],$id_wise['logout_time']);	
				 			$time=explode(":",$duration);
					 		$res_arr[$user_id][$login_date]['duration']+=$time[0]*3600+$time[1]*60+$time[2];
							$res_arr[$user_id][$login_date]['user_id']=$id_wise['user_id'];
							$res_arr[$user_id][$login_date]['login_time']=$first_login_time;
							if($id_wise['login_time']!=""){
								$res_arr[$user_id][$login_date]['login']+=1;
							}
							$login_date1=date("Y-m-d",strtotime($id_wise['login_date']));
							$logout_date1=date("Y-m-d",strtotime($id_wise['logout_date']));
							if($id_wise['logout_time']!="")
							{
								if($login_date1==$logout_date1)
								{
									
									$res_arr[$user_id][$login_date]['logout_time']=$id_wise['logout_time'];

								}
								else{

									$logout_date12=date("d-M-y h:i:s A",strtotime($id_wise['login_date'].' 06.00.00 PM'));
								    $res_arr[$user_id][$login_date]['logout_time']=$logout_date12;
							
								}	

							}
							else
							{
								
								$logout_date12=date("d-M-y h.i.s A",strtotime($id_wise['login_date'].' 06.00.00 PM'));
								$res_arr[$user_id][$login_date]['logout_time']=$logout_date12;

							}

							if($id_wise['logout_date'] != ""){
								$res_arr[$user_id][$login_date]['logout_date']=$id_wise['logout_date'];
								$res_arr[$user_id][$login_date]['logout']+=1;
							}
							
							$res_arr[$user_id][$login_date]['login_status']=$id_wise['login_status'];
							$res_arr[$user_id][$login_date]['login_date']=$id_wise['login_date'];

						}
						$row_span[$user_id]+=1;
					}
				}

					$i=1;$total_duration=0;
					foreach($res_arr as $user_data)
					{
						$rowsp=0;
						foreach ($user_data as $data) 
						{
							$rowsp++;
						
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						//echo substr($row[csf('login_time')],9)
						
						 $login_time = date("h:i:s A",strtotime($data['login_time']));
						 $logout_time = date("h:i:s A",strtotime($data['logout_time']));
						 $login_time_day=date("h:i:s A",strtotime($data['login_time']));
						
					
			    		 ?>
				        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
				           <td width="40" align="center"><? echo $i;?></td>

				           <?php if($rowsp==1){

				           		$span=$row_span[$data['user_id']];

				            ?>

				           <td width="200" rowspan="<?php echo $span+1; ?>"><p><? echo  $user_name_library[$data['user_id']];?></p>
				           		
				           </td>
				      	 <?php } ?>
				           
				           <td align="center" width="180"><p><? echo ($data['login_date'] == '0000-00-00' || $data['login_date'] == '' ? '' : change_date_format($data['login_date']));?></p></td>
				           <td width="180" align="center"><p><? echo $login_time;?></p></td>
				           <td width="180" align="center"><p><? echo $logout_time;?></p></td>
				           <td width="" align="center">
					           	<p>
								   <? 

								   	$duration =  date_deffer($data['login_time'],$data['logout_time']);
						 			echo $duration;

						 			$time=explode(":", $duration);
						 			$total_time=(3600*$time[0])+(60*$time[1])+$time[2];
						 			/*echo $total_time;die;*/
						 			$total_duration=$total_duration+$total_time;
								   ?>
					           </p>
				       		</td>   
				          
				        </tr>

				        <?
						$i++;
					}?>

					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right">Total:</td>
						<td align="center"><?echo get_hour_min_sec($total_duration);?></td>
					</tr>
					<?$total_duration=0; }
					?>
			        
		        </table>
	    </div>



<br />




<?
	/*$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=18 and b.mail_user_setup_id=c.id and a.company_id in($companyStr)";
	$mail_sql2=sql_select($sql2, '', '', '', $con);
	foreach($mail_sql2 as $row)
	{
		if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}*/

 	$to='sohel@logicsoftbd.com,allmerchandiser@soniagroup.com,allcommercial@soniagroup.com,allaccounts@soniagroup.com,anwar.hossain@soniagroup.com,mahabub@soniagroup.com,jewel@soniagroup.com,zia@soniagroup.com,Khales.rahman@soniagroup.com,Amirul.islam@soniagroup.com';
	$subject="Weekly Login Report of ".$previous_date.' to '.$current_date."";
	$message="";
	$message=ob_get_contents();
	ob_clean();
	$header=mailHeader();
	//if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
	if($_REQUEST['isview']==1){
		echo $message;
	}
	else{
		if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
	}
		
?>

