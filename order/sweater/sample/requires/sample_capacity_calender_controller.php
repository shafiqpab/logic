<?
header('Content-type:text/html; charset=utf-8');
session_start();
if($_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
include('../../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];


if ($action=="load_drop_down_location")
{
	$sql="select location_name,id from lib_location where company_id='$data' and is_deleted=0  and status_active=1 order by location_name";
	if(count(sql_select($sql))==1)
	{
		echo create_drop_down( "cbo_location_name", 150, $sql,'id,location_name', 0, '--- Select Location ---', 0, ""  );
	}
	else
	{
		echo create_drop_down( "cbo_location_name", 150, $sql,'id,location_name', 1, '--- Select Location ---', 0, ""  );
	}
	exit();
}

if ($action=="report_generate")
{
	$process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    
    $company_arr  = return_library_array("select id, company_name from lib_company", "id", "company_name");
    $location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");
    $sam_team_arr = return_library_array("select id, team_name from lib_sample_production_team", "id", "team_name");

    $cbo_company_id    = str_replace("'","",$cbo_company_name);
    $cbo_location_id   = str_replace("'","",$cbo_location_name);
    $cbo_sample_team_id= str_replace("'","",$cbo_sample_team);
    $date_from         = str_replace("'","",$txt_date_from);
    $date_to           = str_replace("'","",$txt_date_to);

    if ($cbo_company_id == 0) $company_cond=""; else $company_cond=" and company_id=$cbo_company_id";
    if ($cbo_location_id == 0) $location_cond=""; else $location_cond=" and location_id=$cbo_location_id";
    if ($cbo_location_id == 0) $lib_location_cond=""; else $lib_location_cond=" and location_name=$cbo_location_id";
    if ($cbo_sample_team_id == 0) $sample_team_cond=""; else $sample_team_cond=" and team_leader=$cbo_sample_team_id";

    $lib_team_name='';
    if ($cbo_sample_team_id != 0)
    {
        $sql_team="select id, team_name from lib_sample_production_team where product_category=6 and id=$cbo_sample_team_id and is_deleted=0";
	    $sql_result = sql_select($sql_team);
	    foreach ($sql_result as $value)
	    {
	    	$lib_team_name = $value[csf('team_name')];
	    }
	}

    if ($lib_team_name == '') $lib_sample_team_cond=""; else $lib_sample_team_cond=" and team_name='".$lib_team_name."'";

    if($date_from && $date_to)
    {
    	if($db_type==0)
        {
            $date_cond = " and confirm_del_end_date between '".date("Y-m-d", strtotime(str_replace("'", "",  $date_from)))."' and '".date("Y-m-d", strtotime(str_replace("'", "",  $date_to)))."'";
        }
        else
        {
        	$date_cond = " and confirm_del_end_date between '".date("d-M-Y", strtotime(str_replace("'", "",  $date_from)))."' and '".date("d-M-Y", strtotime(str_replace("'", "",  $date_to)))."'";
        }
    }

    $day_diff = datediff( 'd', $date_from, $date_to);

   	//for($i=1; $i<=$day_diff; $i++)
   	for($i=0; $i<$day_diff; $i++)
	{
/*		if($i==1)
			$new_date=date('Y-m-d',strtotime($date_from));
		else
			$new_date=add_date($new_date,1);
*/		
		$new_date=add_date($date_from,$i);
		
		$month_arr[date("Y-m",strtotime($new_date))] = date("Y-m",strtotime($new_date));
		$day_month_count_arr[date("Y-m",strtotime($new_date))]['count_day'] = date("d",strtotime($new_date));
		$month_year_arr[date("Y-m",strtotime($new_date))]['month'] = date("F, Y",strtotime($new_date));
		$day_arr[date("m-d",strtotime($new_date))] = date("m-d",strtotime($new_date));
		$day_month_arr[date("m-d",strtotime($new_date))]['day'] = date("d",strtotime($new_date));
		$day_month_arr[date("m-d",strtotime($new_date))]['full_day'] = date("l",strtotime($new_date));
	}
    //echo '<pre>';print_r($day_month_arr);
	$sql = "select id, team_name, style_capacity from lib_sample_production_team where product_category=6 and status_active=1 and is_deleted=0 $lib_location_cond $lib_sample_team_cond";
	$sql_res = sql_select($sql);
	foreach ($sql_res as $rows)
	{
		$req_capacity_arr[$rows[csf("id")]]=$rows[csf("style_capacity")];
		$team_name_arr[$rows[csf("id")]]=$rows[csf("team_name")];
	}


	$sql_dtls = "select sample_mst_id, team_leader, confirm_del_end_date from sample_requisition_acknowledge where entry_form=345 and team_leader>0 and status_active=1 and is_deleted=0 $company_cond $sample_team_cond $date_cond";
	$sql_rslt = sql_select($sql_dtls);
	foreach ($sql_rslt as $rows)
	{
		$key= date("m-d",strtotime($rows[csf("confirm_del_end_date")]));
		$dataArr[$rows[csf("team_leader")]][$key][$rows[csf("sample_mst_id")]]=$rows[csf("sample_mst_id")];
		$teamarr[$rows[csf("team_leader")]]=$rows[csf("team_leader")];
	}

	$table_width=200+(count($day_arr)*51);
	ob_start();
    ?>
	<div style="width:<? echo $table_width+20;?>px;">
	    <div style="width:<? echo $table_width; ?>px; background-color: #98BDEA;">
	    	<span style="background:#00B0FF; padding:0 6px; border-radius:9px; cursor:pointer;" title="Light Blue">&nbsp;</span>&nbsp; Style Capacity &nbsp;&nbsp;
	        <span style="background:#5ED05A; padding:0 6px; border-radius:9px; cursor:pointer;" title="Light Green">&nbsp;</span>&nbsp; Available &nbsp;&nbsp;        
	        <span style="background:#FF0000; padding:0 6px; border-radius:9px; cursor:pointer;" title="Red">&nbsp;</span>&nbsp; Over &nbsp;&nbsp;
	        <span style="background:#FFFF00; padding:0 6px; border-radius:9px; cursor:pointer;" title="Yellow">&nbsp;</span>&nbsp; Full &nbsp;&nbsp;
	    </div>
    <div style="width:<? echo $table_width+20;?>px;" id="scroll_body_1">
        <table width="<? echo $table_width;?>" border="1" cellspacing="0" cellpadding="0" rules="all" id="table_body" class="rpt_table">
        	<thead>
        		<tr>
        			<th colspan="2" width="200">Month</th>
        			<?
                    	foreach($month_arr as $val)
                    	{
                        	?>
                       	   <th colspan="<? echo $day_month_count_arr[$val]['count_day']; ?>"><? echo $month_year_arr[$val]['month']; ?></th>
                        	<?
                    	}
                    ?>
        		</tr>
        		<tr>
        			<th width="100">Team</th>
        			<th>Particulars/Days</th>
        			<?
                    	foreach($day_arr as $val)
                    	{
                        	?>
                       	   <th style="<? if ($day_month_arr[$val]['full_day'] == 'Friday') { ?> background-image: none; background-color: #f00;<? } ?> " title="<? echo $day_month_arr[$val]['full_day']; ?>"><? echo $day_month_arr[$val]['day']; ?></th>
                        	<?
                    	}
                    ?>
        		</tr>
	        </thead>

            <tbody>
            	<?
            	$i=1;
            	foreach($team_name_arr as $team_id=>$rows)
            	{ 
            		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#D1D1D1";
            		?>
	            	 <tr bgcolor="<? echo $bgcolor; ?>" >
	            		<td rowspan="4" width="100" style="vertical-align: middle;"><? echo $team_name_arr[$team_id];?></td>
	            		<td><b>Style Capacity</b></td>	            		
			             <?
	                    	foreach($day_arr as $val)
	                    	{
	                        	?>
	                       	    <td width="50" align="center"><p style='background-color: #00B0FF'><? echo $req_capacity_arr[$team_id]; ?></p></td>
	                        	<?
	                    	}
	                    ?>		                        	
	            	</tr>
	            	 <tr bgcolor="<? echo $bgcolor; ?>">
	            		<td><b>Booking Done</b></td>
	            		<?
	                    	foreach($day_arr as $val)
	                    	{
	                    		$boking_count = count($dataArr[$team_id][$val]);
	                        	?>
	                       	    <td width="50" align="center"><? if ($boking_count > 0) echo "<p style='background-color: #FFA500'>$boking_count<p/>"; else echo $boking_count; ?></td>
	                        	<?
	                    	}
	                    ?>
	            	</tr>
	            	 <tr bgcolor="<? echo $bgcolor; ?>">
	            		<td><b>Balance</b></td>
	            		<?
	                    	foreach($day_arr as $val)
	                    	{
	                    		$balance = $req_capacity_arr[$team_id]-count($dataArr[$team_id][$val]);
	                        	?>
	                       	   <td width="50" align="center"><? if ($balance < 0) echo "<p style='background-color: #FF0000'>$balance<p/>"; else echo $balance; ?></td>
	                        	<?
	                    	}
	                    ?>
	            	</tr>	            	
	            	 <tr bgcolor="<? echo $bgcolor; ?>">	            		
	            		<td><b>Status</b></td>		
	            		<?
	                    	foreach($day_arr as $val)
	                    	{
	                    		$balance = $req_capacity_arr[$team_id]-count($dataArr[$team_id][$val]);
	                        	?>
	                       	   <td width="50" align="center">
	                       	   		<? 
	                       	   			if ($balance > 0)
	                       	   			{
	                       	   				echo "<p style='background-color: #5ED05A'>Available</p>";
	                       	   			}
	                       	   			else if($balance < 0)
	                       	   			{
	                       	   				echo "<p style='background-color: #FF0000'>Over</p>";
	                       	   			}
	                       	   			else
	                       	   			{
	                       	   				echo "<p style='background-color: #CDFF00'>Full</p>";
	                       	   			}	
	                       	   		?>	                       	   			
	                       	   	</td>
	                        	<?
	                    	}
	                    ?>   		
	            	</tr>
				    <?
				    $i++;
			    } 
			    ?>
            </tbody>
        </table>
    </div>
    </div>
	<?
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');    
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename"; 
    exit();
}

?>