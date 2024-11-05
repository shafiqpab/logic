<?php
header('Content-type:text/html; charset=utf-8');
session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_id = $_SESSION['logic_erp']['user_id'];

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 100, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/line_wise_non_prod_hour_controller', this.value, 'load_drop_down_floor', 'floor_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/line_wise_non_prod_hour_controller' );",0 );     	 
}
if ($action=="load_drop_down_floor")  //document.getElementById('cbo_floor').value
{ 
	echo create_drop_down( "cbo_floor", 100, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process=5 order by floor_name","id,floor_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/line_wise_non_prod_hour_controller', this.value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'load_drop_down_line', 'line_td' );",0 ); 


	exit();    	 
}
if ($action=="load_drop_down_line")
{
	$explode_data = explode("_",$data);
	// print_r($explode_data);
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$explode_data[2] and variable_list=23 and is_deleted=0 and status_active=1");
	$txt_date_from = $explode_data[3];
	$txt_date_to = $explode_data[3];
	
	$cond="";
	if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		
		if($txt_date_from=="")
		{
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_id= $explode_data[1]";
			if( $explode_data[0]!=0 ) $cond = " and floor_id in($explode_data[0])";
			$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 $cond");
		}
		else
		{
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and a.location_id= $explode_data[1]";
			if( $explode_data[0]!=0 ) $cond = " and a.floor_id in($explode_data[0])";
		 if($db_type==0)	$data_format="and b.pr_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		 if($db_type==2)	$data_format="and b.pr_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
	
			$line_data=sql_select( "select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id $data_format and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number order by a.line_number");
		}
		
		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_array[$row[csf('id')]]=$line;
		}

		echo create_drop_down( "cbo_line", 100,$line_array,"", 1, "-- Select --", $selected, "",0,0 );
	}
	else
	{
		if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name= $explode_data[1]";
		if( $explode_data[0]!=0 ) $cond = " and floor_name= $explode_data[0]";

		echo create_drop_down( "cbo_line", 100, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 1, "-- Select --", $selected, "",0,0 );
	}
	exit();
}


if($action=="report_generate")
{

		$process = array( &$_POST );
		extract(check_magic_quote_gpc( $process ));
		$company_id = str_replace("'","",$cbo_company_name);
		$cbo_location = str_replace("'","",$cbo_location);
		$cbo_floor = str_replace("'","",$cbo_floor);
		$cbo_line = str_replace("'","",$cbo_line);
		$txt_date = str_replace("'","",$txt_date_from);
		
		$sql_cond = "";
		$sql_cond .= ($company_id !=0) ? " and b.company_id=$company_id" : "";
		$sql_cond .= ($cbo_location !=0) ? " and b.location_id=$cbo_location" : "";	
		$sql_cond .= ($cbo_floor !=0) ? " and b.floor_id=$cbo_floor" : "";	
		$sql_cond .= ($cbo_line !=0) ? " and b.id=$cbo_line" : "";
		$sql_cond .= ($txt_date !="") ? " and a.production_date='$txt_date'" : "";	
		
		$sql_cond2 = "";
		$sql_cond2 .= ($company_id !=0) ? " and b.company_id=$company_id" : "";
		$sql_cond2.= ($cbo_location !=0) ? " and b.location_id=$cbo_location" : "";	
		$sql_cond2 .= ($cbo_floor !=0) ? " and b.floor_id=$cbo_floor" : "";	
		$sql_cond2 .= ($cbo_line !=0) ? " and b.id=$cbo_line" : "";
		$sql_cond2 .= ($txt_date !="") ? " and a.pr_date='$txt_date'" : "";	
        
		$prod_start_hour="08:00";
		$start_time=explode(":",$prod_start_hour);
		
		$hour = $start_time[0]*1;
		$last_hour=23;
		$start_hour=$prod_start_hour;
		$start_hour_arr[$hour]=$start_hour;
		for($j=$hour;$j<$last_hour;$j++)
		{
			$start_hour=add_time($start_hour,60);
			$start_hour_arr[$j+1]=substr($start_hour,0,5);
		}
		$start_hour_arr[$j+1]='23:59';
		$curent_date=date("d-M-Y");
		$curent_hour=date("H");
		// $curent_date="5-Aug-2023";
		
		$sql="SELECT d.floor_name,a.floor_id, a.sewing_line, c.line_name,c.SEWING_LINE_SERIAL,d.FLOOR_SERIAL_NO,"; 
		$first=1;
		for($h=$hour;$h<$last_hour;$h++)
		{
			$bg=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="prod_hour".substr($bg,0,2);
			if($first==1)
			{
				$sql.="sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,";
			}
			else
			{
				$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 
				THEN production_quantity else 0 END) AS $prod_hour,";
			}
			$first++;
		}
		$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour233 
		FROM  pro_garments_production_mst a , PROD_RESOURCE_MST b,LIB_SEWING_LINE c,LIB_PROD_FLOOR d where a.sewing_line=b.id and REGEXP_SUBSTR( b.line_number, '[^,]+', 1)=c.id and a.floor_id=d.id and d.PRODUCTION_PROCESS=5 and a.status_active=1 and a.is_deleted=0   and a.production_type=5   $sql_cond
		group by  d.floor_name,d.FLOOR_SERIAL_NO,a.floor_id,a.sewing_line, c.line_name,c.SEWING_LINE_SERIAL order by d.FLOOR_SERIAL_NO,c.SEWING_LINE_SERIAL";//and a.location=1 
		// echo $sql;
		$res = sql_select($sql);
		$data_array = array();
		$tot_hour_arr = array();
		$line_id_arr = array();
		$formDate = strtotime($txt_date);
		$todayDate = strtotime($curent_date);
		
		foreach ($res as $v) 
		{
			$line_id_arr[$v['SEWING_LINE']] = $v['SEWING_LINE'];
			for($h=$hour;$h<=$last_hour;$h++)
			{
				$prod_hour="PROD_HOUR".substr($start_hour_arr[$h],0,2)."";
				$hr = substr($prod_hour,9,11).":00";
				$hr = date('H',strtotime($hr));
				// echo $hr;
				
				if($formDate==$todayDate)
				{
					if($hr<=$curent_hour)
					{
						if($v[$prod_hour]==0)
						{
							$data_array[$v['FLOOR_NAME']][$v['LINE_NAME']][$prod_hour] = $prod_hour;
							$tot_hour_arr[$prod_hour] = $prod_hour;
						}
					}			
				}
				else
				{
					if($v[$prod_hour]==0)
						{
							$data_array[$v['FLOOR_NAME']][$v['LINE_NAME']][$prod_hour] = $prod_hour;
							$tot_hour_arr[$prod_hour] = $prod_hour;
						}
				}
				
				
			}
		}
		asort($tot_hour_arr);
		// echo "<pre>";print_r($data_array);
		$line_id_cond = where_con_using_array($line_id_arr,0,"b.id not");
		// echo $line_id_cond;die;
		$sql = "SELECT d.floor_name, c.line_name,c.SEWING_LINE_SERIAL,d.FLOOR_SERIAL_NO from PROD_RESOURCE_MST b,PROD_RESOURCE_DTLS a, LIB_SEWING_LINE c,LIB_PROD_FLOOR d where 
		 b.id=a.mst_id and REGEXP_SUBSTR( b.line_number, '[^,]+', 1)=c.id and b.floor_id=d.id and c.FLOOR_NAME=d.id and d.PRODUCTION_PROCESS=5 and b.is_deleted=0 and a.is_deleted=0  $line_id_cond
		 $sql_cond2";
		// echo $sql;die;
		$res = sql_select($sql);
		$non_prod_line_arr = array();
		foreach ($res as $v) 
		{
			$non_prod_line_arr[$v['FLOOR_NAME']][$v['LINE_NAME']] = $v['LINE_NAME'];
		}
		// echo $sql;die;
		// echo "<pre>";print_r($non_prod_line_arr);
		$tbl_width = 300+count($tot_hour_arr)*60;
		echo load_html_head_contents("Line Wise Non Production Hour Report", "", 1, 1,$unicode,1,1);
		ob_start();
		?>
		<fieldset style="margin: 0 auto;width:<? echo $tbl_width+20; ?>px;">
		<center><h2>Line Wise Non Production Hour Report. </h2></center>
		<center><h2>Date : <?=$txt_date;?> </h2></center>
		<center><div id="txt_excl_link"></div></center><br>
			<table width="<? echo $tbl_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="" align="left"  >
				<thead>
					<tr>
						<th width="150">Floor Name</th>
						<th width="150">Sewing Line</th>
						<?
						foreach ($tot_hour_arr as $hr) 
						{ 
							$hr = substr($hr,9,11).":00";
							$hr = date('h:i A',strtotime($hr));
							?>
							<th><?=$hr;?></th>
							<?
						}
						?>
					</tr>
				</thead>
				<tbody>
					<?php
					$i=1;
					foreach ($data_array as $floor_name => $f_data) 
					{
						foreach ($f_data as $line_name => $l_data) 
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<?=$bgcolor;?>" onclick="change_color('tr_<?=$i;?>','<?=$bgcolor;?>')" id="tr_<?=$i?>">
								<td><p><?=$floor_name;?></p></td>
								<td><p>&nbsp;<?=$line_name;?></p></td>
								<?
								foreach ($tot_hour_arr as $hour_key => $hour) 
								{
									$phr = $data_array[$floor_name][$line_name][$hour];
									if($phr!="")
									{
										$phr = substr($phr,9,11).":00";
										$phr = date('h:i A',strtotime($phr));
									}
									// $phr = ($phr!="") ? date('h:i A',strtotime($phr)) : '';
									?>
									<td><?=$phr;?></td>
									<?
								}
								?>
	
							</tr>
							<?php
							$i++;
						}
					}
					foreach ($non_prod_line_arr as $f_name => $f_data) 
					{
						foreach ($f_data as $l_name => $r) 
						{
							$bgcolor = "#dccdcd";
							?>
							<tr title="no production line" bgcolor="<?=$bgcolor;?>" onclick="change_color('tr_<?=$i;?>','<?=$bgcolor;?>')" id="tr_<?=$i?>">
								<td><p><?=$f_name;?></p></td>
								<td><p>&nbsp;<?=$l_name;?></p></td>
								<?
								foreach ($tot_hour_arr as $hour_key => $hour) 
								{
									$phr = substr($hour,9,11).":00";
									$phr = date('h:i A',strtotime($phr));								
									?>
									<td><?=$phr;?></td>
									<?
								}
								?>
	
							</tr>
							<?
							$i++;
						}
					}
					?>
				</tbody>
			</table>
		</fieldset>
		<?
		$html=ob_get_contents();
		ob_flush();
		// $user_id =1000000000;
		foreach (glob("$user_id*.xls") as $filename) 
		{
		   @unlink($filename);
		}
		
		//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w') or die("can not open");	
		$is_created = fwrite($create_new_excel,$html) or die("can not write");	
	
    ?>
    <script>
		$(document).ready(function(e) 
		{
			document.getElementById('txt_excl_link').innerHTML='<a href="requires/<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" style="padding:0 2px;" class="formbutton"/></a>&nbsp;&nbsp;';
		});	
	</script>
	<?php
	}
	?>
    
   
	
	