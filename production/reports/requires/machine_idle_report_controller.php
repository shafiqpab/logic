<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

function min_to_hr($min)
{ 
	$hours =  floor($min/60); 
	$mins =   $min % 60; 
	return $hours.'.'.$mins;
}

//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 120, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "--Select Location--", $selected, "load_drop_down( 'requires/machine_idle_report_controller', document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();     	 
}

if ($action=="load_drop_down_floor")
{
	$ex_data=explode('_',$data);
	if($ex_data[1]!=0) $location_cond=" and b.location_id='$ex_data[1]'"; else $location_cond="";
	
	echo create_drop_down( "cbo_floor_id", 120, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id in(1,2,4) and b.company_id='$ex_data[0]' and b.status_active in(1,2) and b.is_deleted=0  $location_cond group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "-- Select Floor --", 0, "","" );
	exit();	 
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	// txt_date_from*txt_date_to
	$cbo_company=str_replace("'","",$cbo_company_id);
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
		
	$table_width=800; 
	
	ob_start();	
	?>
	<div>
        
        <?
		$machine_name=str_replace("'","",$txt_machine_id);
		$floor_name=str_replace("'","",$cbo_floor_id);
		$machine_type=str_replace("'","",$cbo_machine_type);
		$date_from=str_replace("'","",$txt_date_from);
		$date_to=str_replace("'","",$txt_date_to);
		
		if (str_replace("'","",$cbo_location_id)==0) $location_id=""; else $location_id=" and a.location_id=$cbo_location_id";
		if (str_replace("'","",$txt_machine_id)==0 || str_replace("'","",$txt_machine_id)=='') $machine_id=""; else $machine_id=" and a.id in ( $machine_name )";
		if ($floor_name==0) $floor_id=""; else $floor_id=" and a.floor_id in ( $floor_name )";
		if ($machine_type==0) $machine_type_cond=""; else $machine_type_cond=" and a.category_id in ( $machine_type )";
		if($db_type==0)
		{
			if( $date_from!="" && $date_to!="" )
			{
				$sql_cond .= " and b.reporting_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
			}
		}
		else if($db_type==2)
		{
			if($date_from!="" && $date_to!="") 
			{
				$sql_cond .= " and b.reporting_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
			}
		}
		
		$floor_library=return_library_array( "select id,floor_name from  lib_prod_floor", "id", "floor_name");
		$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name");

		$cause_sql2="SELECT a.company_id, a.prod_capacity, a.floor_id, a.machine_group, b.machine_entry_tbl_id, b.machine_no, b.from_date, b.from_hour, b.from_minute, b.to_date, b.to_hour, b.to_minute,b.reporting_date, b.machine_idle_cause, b.remarks from lib_machine_name a, pro_cause_of_machine_idle b
		where a.company_id=$cbo_company_id $sql_cond $location_id $floor_id $machine_type_cond $machine_id and a.id=b.machine_entry_tbl_id and a.status_active in(1,2) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id";
		// echo $cause_sql2;
		$cause_sql="SELECT a.id as machine_entry_tbl_id, a.company_id, a.prod_capacity, a.floor_id, a.machine_group, a.machine_no
		FROM lib_machine_name a WHERE a.company_id=$cbo_company_id $location_id $floor_id $machine_type_cond $machine_id and a.status_active in(1,2) AND a.is_deleted=0
		ORDER BY a.seq_no";

		$cause_sql_result2=sql_select($cause_sql2);
		$cause_sql_result=sql_select($cause_sql);
		$machine_data_array = array();
		$rowspan_arr=array();
		$machine_count_arr=array();
		foreach ($cause_sql_result as $key => $value) 
		{
			$machine_count_arr[]=$value[csf('machine_no')];
		 	$rowspan_arr[$value[csf('company_id')]]++;
		} 
		$total_machine=count($machine_count_arr);
		/*echo "<pre>";
		print_r($machine_data_array); die;*/
		$cause_arr=array(); $reason_arr=array();$all_data_arr=array();
		foreach ($cause_sql_result2 as $row)
		{
			$from_hour 	 = ($row[csf('from_hour')]=="")?0:$row[csf('from_hour')];
			$to_hour 	 = ($row[csf('to_hour')]=="")?0:$row[csf('to_hour')];
			$from_minute = ($row[csf('from_minute')]=="")?0:$row[csf('from_minute')];
			$to_minute 	 = ($row[csf('to_minute')]=="")?0:$row[csf('to_minute')];

			$start_date=$row[csf("from_date")]." ".$from_hour.":".$from_minute.":00";
			$end_date=$row[csf("to_date")]." ".$to_hour.":".$to_minute.":00";
			$from_time = strtotime($start_date);
			$to_time = strtotime($end_date);
			$minutes= round(abs($from_time - $to_time) / 60,2);

			$all_data_arr[$row[csf('company_id')]][$row[csf('floor_id')]][$row[csf('machine_no')]]=$row[csf('machine_idle_cause')];

			$cause_arr[$row[csf('company_id')]][$row[csf('floor_id')]][$row[csf('machine_no')]][$row[csf('machine_idle_cause')]]["idle_time"]+=$minutes;

			if( $cause_arr[$row[csf('company_id')]][$row[csf('floor_id')]][$row[csf('machine_no')]]["remarks"]=="" )
			$cause_arr[$row[csf('company_id')]][$row[csf('floor_id')]][$row[csf('machine_no')]]["remarks"] = $row[csf('remarks')].',';
			else 
				$cause_arr[$row[csf('company_id')]][$row[csf('floor_id')]][$row[csf('machine_no')]]["remarks"] .=$row[csf('remarks')];

			$reason_arr[$row[csf('machine_idle_cause')]]=$row[csf('machine_idle_cause')];
			$reason_arr_date[$row[csf('machine_idle_cause')]]=$row[csf('reporting_date')];
		}
		$cont_couse=count($reason_arr);

		?>
		<table width="<? echo $table_width+($cont_couse*100); ?>" cellpadding="0" cellspacing="0" id="caption" align="center">
            <tr>
               <td align="center" colspan="<? echo $col_span; ?>" class="form_caption" ><strong style="font-size:16px"><? echo $company_library[$cbo_company]; ?></strong></td>
            </tr>
            <tr>
               <td align="center" colspan="<? echo $col_span; ?>" class="form_caption" ><strong style="font-size:16px"><? echo $report_title; ?></strong></td>
            </tr>
            <tr>  
               <td align="center"colspan="<? echo $col_span; ?>" class="form_caption" ><strong style="font-size:13px"><? echo "Date From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
            </tr>
        </table>
        <table width="<? echo $table_width+($cont_couse*100); ?> " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
            <thead>
                <tr>
                    <th width="160" style="word-wrap:break-word; word-break: break-all;">Company Name</th>
                    <th width="100" style="word-wrap:break-word; word-break: break-all;">Floor</th>
                    <th width="100" style="word-wrap:break-word; word-break: break-all;">Machine No</th>
                    <th width="70" style="word-wrap:break-word; word-break: break-all;">Capacity</th>
                    <th width="80" style="word-wrap:break-word; word-break: break-all;">Group</th>
                    <?
                    foreach($reason_arr as $mc_cause_id)
                    {
                    	?>
                    	<th width="80" style="word-wrap:break-word; word-break: break-all;"><? echo $cause_type[$mc_cause_id].'<br>(M)';?></th>
                    	<?
                    }  
                    ?> 
                    <th width="80" style="word-wrap:break-word; word-break: break-all;">T. time (H)</th>
                    <th width="" style="word-wrap:break-word; word-break: break-all;">Remarks</th>
               </tr>
            </thead>
        </table>
        <div style="width:<? echo $table_width+($cont_couse*105); ?>px; overflow-y:scroll; max-height:300px;" id="scroll_body">
        <table cellpadding="0" cellspacing="0" width="<? echo $table_width+($cont_couse*100); ?> "  border="1" rules="all" class="rpt_table" >
			<? 
            $i=1;
            $j=0;
            $machine_count=0;
            foreach ($cause_sql_result as $row)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
			    ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="height:35px;" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                	<? if ($j==0) { ?>  
                    <td width="160" rowspan="<? echo $rowspan_arr[$cbo_company]; ?>" style="word-wrap:break-word; word-break: break-all;"><? echo $company_library[$cbo_company]; ?></td> 
                    <?  } $j++; ?>                   
                    <td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $floor_library[$row[csf("floor_id")]]; ?></td>
                    <td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $row[csf("machine_no")];?></td>
                    <td width="70" style="word-wrap:break-word; word-break: break-all;" align="right"><? echo $row[csf("prod_capacity")]; ?>&nbsp;</td>
                    <td width="80" style="word-wrap:break-word; word-break: break-all;"><? echo $row[csf("machine_group")]; ?></td>
                    <? 
                    $remarks=chop(ltrim($cause_arr[$row[csf('company_id')]][$row[csf('floor_id')]][$row[csf('machine_no')]]["remarks"],","),",");
                    $total_minutes=0; 
                     $machine_count+=1;
                    //$total_hour_minit_couse=array();
                    foreach ($reason_arr as $reason_id )
                    {
                       $minutes=$cause_arr[$row[csf('company_id')]][$row[csf('floor_id')]][$row[csf('machine_no')]][$reason_id]["idle_time"];  
                       $total_minutes+=$cause_arr[$row[csf('company_id')]][$row[csf('floor_id')]][$row[csf('machine_no')]][$reason_id]["idle_time"];
    		           $hmvalue="";
						if($minutes)
						{
							$hmvalue=$minutes;
						}
						$total_hour_minit_couse[$reason_id]+=$cause_arr[$row[csf('company_id')]][$row[csf('floor_id')]][$row[csf('machine_no')]][$reason_id]["idle_time"];
                        ?>
                        <td title="<? echo $reason_id; ?>" align="right" width="80" style="word-wrap:break-word; word-break: break-all;"><a href="##" onclick="openmypage_idle('<? echo $row[csf("machine_entry_tbl_id")]; ?>','<? echo $date_from;?>','<? echo $date_to;?>','<? echo $reason_id;?>','idle_for');" ><? echo $hmvalue;?></a></td>
                    	<?
                    }                    

                    ?>
                    <td width="80" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo $total_time=number_format($total_minutes/60,2); $grand_total_time+=$total_time; ?></td>
                    <td width="" style="word-wrap:break-word; word-break: break-all;"><? echo $remarks; ?></td>
                </tr>
                <?
                $i++;
            }
            /*echo "<pre>";
            print_r($total_hour_minit_couse);*/
            
            ?>
        </table>
        </div> <!-- Body end -->

        <!-- tfoot start -->
        <table cellspacing="0" cellpadding="0" width="<? echo $table_width+($cont_couse*100); ?>" border="1" rules="all" class="tbl_bottom" >
            <tr>
            	<td width="160">&nbsp;</td>
                <td width="100">&nbsp;</p></td>
                <td width="100">&nbsp;</p></td>
                <td width="70">&nbsp;</p></td>
                <td width="80" style="word-wrap:break-word; word-break: break-all;"><strong>Total(Hrs):</strong></td>
                <?
                $i=1;
                foreach ($reason_arr as $mc_cause_id )
                {	
                	//$hours_mins = floor($total_hour_minit_couse[$mc_cause_id] / 60).'.'.str_pad(($total_hour_minit_couse[$mc_cause_id] % 60), 2, "0", STR_PAD_LEFT);
                	$hours_mins = $total_hour_minit_couse[$mc_cause_id];
                	$hours = floor($hours_mins / 60);
    				$minutes = ($hours_mins % 60);
                	?>
                    <td width="80"><p><? echo number_format($hours.'.'.$minutes,2); ?></p></td>                     
                	<?
                }

                $i++;
                ?>
                <td width="80"><? echo number_format($grand_total_time,2); ?></td>
                <td width="" align="right"></strong>&nbsp;</td>
            </tr>
        </table>
        <!-- tfoot end -->
        
        <!-- Percentage tfoot start -->
        <table cellspacing="0" cellpadding="0" width="<? echo $table_width+($cont_couse*100); ?>" border="1" rules="all" class="tbl_bottom" >
            <tr>
            	<td width="160">&nbsp;</td>
                <td width="100">&nbsp;</p></td>
                <td width="100">&nbsp;</p></td>
                <td width="70">&nbsp;</p></td>
                <td width="80" style="word-wrap:break-word; word-break: break-all;"><strong>Percentage:</strong></td>
                <?
                $i=1;
                $per=($machine_count*24);
                foreach ($reason_arr as $mc_cause_id )
                {	
                	//$hours_mins = floor($total_hour_minit_couse[$mc_cause_id] / 60).'.'.str_pad(($total_hour_minit_couse[$mc_cause_id] % 60), 2, "0", STR_PAD_LEFT);
                	$hours_mins = $total_hour_minit_couse[$mc_cause_id];
                	$hours = floor($hours_mins / 60);
    				$minutes = ($hours_mins % 60);
    				$hour_min=$hours.'.'.$minutes; 
                	?>
                    <td width="80" title="<? echo 'Percentage:('.$hour_min.'/'.$per.')*100'; ?>"><p><?
                    if($per>0){
                    	 echo number_format(($hour_min/$per)*100,2);
                    }
                    else{
                    	 echo number_format(0,2);
                    }
                    ?></p></td>                     
                	<?
                }

                $i++;
                ?>
                <td width="80" title="<? echo 'Percentage:('.$grand_total_time.'/'.$per.')*100'; ?>"><? if($per>0){
                	echo number_format(($grand_total_time/$per)*100,2);
                } else{
                    	 echo number_format(0,2);
                    } ?></td>
                <td width="" align="right"></strong>&nbsp;</td>
            </tr>
        </table>
        <!-- Percentage tfoot end -->
    </div>
     <?
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	//echo "$total_data####$filename";
	exit();
}

if($action=="machine_no_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$im_data=explode('_',$data);
	//print_r ($im_data);
	?>
	<script>
		var selected_id = new Array; var selected_name = new Array;
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			
			if (str!="") str=str.split("_");
			 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hid_machine_id').val( id );
			$('#hid_machine_name').val( name );
		}
		
		function hidden_field_reset()
		{
			$('#hid_machine_id').val('');
			$('#hid_machine_name').val( '' );
			selected_id = new Array();
			selected_name = new Array();
		}
    </script>
    </head>
    <input type="hidden" name="hid_machine_id" id="hid_machine_id" />
    <input type="hidden" name="hid_machine_name" id="hid_machine_name" />
    <?	
	$sql = "select a.id, a.machine_no, a.brand, a.origin, a.machine_group, b.floor_name  from lib_machine_name a, lib_prod_floor b where a.floor_id=b.id and a.category_id='$im_data[2]' and a.company_id=$im_data[0] and a.status_active in(1,2) and a.is_deleted=0 and a.is_locked=0 order by a.machine_no, b.floor_name ";
	//echo  $sql;
	
	echo create_list_view("tbl_list_search", "Machine Name,Machine Group,Floor Name", "180,110,110","450","350",0, $sql , "js_set_value", "id,machine_no", "", 1, "0,0,0", $arr , "machine_no,machine_group,floor_name", "",'setFilterGrid(\'tbl_list_search\',-1);','0,0,0','',1) ;
	
   exit(); 
}

if($action=="idle_for")
{
	echo load_html_head_contents("Cause of Machine Idle Pop Up", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);

	if($db_type==0)
	{
		if( $date_from!="" && $date_to!="" )
		{
			$sql_cond .= " and reporting_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
		}
	}
	else if($db_type==2)
	{
		if($date_from!="" && $date_to!="") 
		{
			$sql_cond .= " and reporting_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
		}
	}

	$sql= "SELECT id, machine_entry_tbl_id, machine_no, from_date, from_hour, from_minute, to_date, to_hour, to_minute, machine_idle_cause, remarks from  pro_cause_of_machine_idle where machine_entry_tbl_id='$machine_id' and machine_idle_cause=$reason_id $sql_cond and is_deleted=0 and status_active=1";
	
	?>
	<fieldset style="width:550px; margin-left:5px">
        <table border="1" class="rpt_table" rules="all" width="530" cellpadding="0" cellspacing="0">
            <thead>
                <th width="30">SL</th>
                <th width="120">From Date and Time</th>
                <th width="120">To Date and Time</th>
                <th width="100">Cause of Machine Idle</th>
                <th width="140">Remarks</th>
            </thead>
            <tbody>
			<?
				$i=1; $total_qnty=0;
				$result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					$from_date=date("Y-m-d",strtotime($row[csf('from_date')]));	$from_hour=$row[csf('from_hour')]; $from_minute=$row[csf('from_minute')];
					$to_date=date("Y-m-d",strtotime($row[csf('to_date')]));	$to_hour=$row[csf('to_hour')]; $to_minute=$row[csf('to_minute')];
					
					$start_time='';
					$start_time=$from_hour.':'.$from_minute;
					
					$end_time='';
					$end_time=$to_hour.':'.$to_minute;
					
					$start_date=change_date_format($from_date)." - ".$start_time;
					$end_date=change_date_format($to_date)." - ".$end_time;
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30"><? echo $i; ?></td>
						<td width="120"><p><? echo $start_date; ?>&nbsp;</p></td>
						<td width="120"><p><? echo $end_date; ?>&nbsp;</p></td>
						<td width="100"><p><? echo $cause_type[$row[csf('machine_idle_cause')]]; ?>&nbsp;</p></td>
						<td width="140"><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
					</tr>
					<?
                    $i++;
				}
            ?>
            </tbody>
        </table>
	</fieldset>   
	<?	
	exit();
}
?>