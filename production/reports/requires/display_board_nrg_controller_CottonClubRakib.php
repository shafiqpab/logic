<?php
header('Content-type:text/html; charset=utf-8');
session_start();

// if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
// $user_id = $_SESSION['logic_erp']['user_id'];

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action']; 

if ($action=="load_drop_down_floor")
{ 
	echo create_drop_down( "cbo_floor_id", "", "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id='$data' and production_process=5 order by floor_name","id,floor_name", 1, "-- Select --", $selected, "",0 ); 
	
	
	exit();    	 
}

if ($action=="show_floor_listview")
{
	extract($_REQUEST);
	$process = array( &$_POST );
	$sql = "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id=$cbo_company_name and production_process=5 order by floor_name";
	$res = sql_select($sql);
	$i=1;
	foreach ($res as $val) 
	{
		?>
		<div class="inputGroup">
		    <input id="option_<?=$i;?>" name="floor_name[]" value="<?=$val['ID'];?>"  type="checkbox"/>
		    <label for="option_<?=$i;?>"><?=$val['FLOOR_NAME'];?></label>
		 </div>
		 <!-- onclick="get_sewing_line()" -->
		<?
		$i++;
	}
}


if($action=="load_drop_down_sewing_line_floor")
{
	$explode_data = explode("_",$data);
	$txt_sewing_date = $explode_data[2];
	$wo_company_id = $explode_data[1];
	$cond="";
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name =$wo_company_id and variable_list=23 and is_deleted=0 and status_active=1");
	if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		if($txt_sewing_date=="")
		{
			if( $explode_data[0] ) $cond.= " and floor_id in($explode_data[0])";

			$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 $cond");
		}
		else
		{
			if( $explode_data[0]) $cond.= " and a.floor_id in($explode_data[0])";

			if($db_type==0)
			{
				$line_data=sql_select("SELECT a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".change_date_format($txt_sewing_date,'yyyy-mm-dd')."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id  order by a.id, a.prod_resource_num");
			}
			else if($db_type==2 || $db_type==1)
			{
				$line_data=sql_select("SELECT a.id, a.line_number,a.prod_resource_num from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".date("j-M-Y",strtotime($txt_sewing_date))."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id,a.prod_resource_num, a.line_number  order by a.id, a.prod_resource_num");
			}
		}
		$line_merge=9999; 
		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if(count($line_number)>1)
				{
					$line_merge++;
					$new_arr[$line_merge]=$row[csf('id')];
				}
				else
					$new_arr[$val]=$row[csf('id')];
				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_array[$row[csf('id')]]=$line;
		}

		ksort($new_arr);
		foreach($new_arr as $key=>$v)
		{
			$line_array_new[$v]=$line_array[$v];
		}
		//echo create_drop_down( "cbo_sewing_line", 110,$line_array_new,"", 1, "--- Select ---", $selected, "",0,0 );
		echo create_drop_down( "cbo_line_id", "",$line_array_new,"", 1, "--- Select Line ---", $selected, "",0,0 );
	}
	else
	{
		if( $explode_data[0]!=0 ) $cond = " and floor_name in($explode_data[0])";

		echo create_drop_down( "cbo_line_id", "", "select id,line_name from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 $cond order by id, line_name","id,line_name", 1, "--- Select Line---", $selected, "",0,0 );
	}
	exit();
}

if ($action == "eval_multi_select") {
    echo "set_multiselect('cbo_line_id','0','0','','0');\n";
    exit();
}



if($action=="line_search_popup")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1,$unicode,1);
	?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;
		
    	function check_all_data() {

			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ )
			 {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
			 }
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
			//alert(strCon)
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
			
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );					
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 ); 
			}
			var id = ''; var name = ''; var job = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ','; 
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 ); 
			
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name ); 
		}
		
		function set_all_data() {

			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ )
			 {
				 
				if(($('#hidden_old_id' + i).val()*1)==1)
				{ 
					var onclickString = $('#tr_' + i).attr('onclick');
					var paramArr = onclickString.split("'");
					var functionParam = paramArr[1];
					js_set_value( functionParam );
				}
			 }
		}
		
		
		function fn_onClosed()
		{
			parent.emailwindow.hide();
		}
    </script>
	<?
	extract($_REQUEST);
	//echo $company;die;
	$line_library=return_library_array( "SELECT id,line_name from lib_sewing_line", "id", "line_name");
	if($wo_company_name==0) $company_name=""; else $company_name=" and b.company_name in($wo_company_name)";//job_no

		$line_array=array();
		if($date_from=="")
		{
			$data_format="";
		}
		else
		{
			if($db_type==0)	$data_format=" and b.pr_date='".change_date_format($date_from,'yyyy-mm-dd')."'";
			if($db_type==2)	$data_format=" and b.pr_date='".change_date_format($date_from,'','',1)."'";
		}
		// if( $location_name!=0 ) $cond .= " and a.location_id in($location_name)";
		if( $floor_name!="" ) $cond.= " and a.floor_id in($floor_name)";
		
		$line_sql="SELECT a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id $data_format and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number order by a.id";
		// echo $line_sql;
		$line_sql_result=sql_select($line_sql);
		
		?>
            <input type='hidden' id='txt_selected_id' />
            <input type='hidden' id='txt_selected' />
            <table cellspacing="0" width="230"  border="1" rules="all" class="rpt_table" >
            	<thead>
                	<th width="30"></th>
                    <th width="200">Line Name</th>
                </thead>
            </table>
            <div style="width:250px; max-height:300px; overflow-y:auto;" id="scroll_body" >          
        		<table cellspacing="0" width="230"  border="1" rules="all" class="rpt_table" id="list_view" >
                <? 
				
				$i=1;
				$previous_line_arr=explode(",",$line_id);
				 foreach($line_sql_result as $row)
				 {
        			 $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					 $flag=0;
					 if(in_array($row[csf('id')],$previous_line_arr))
					 {
						 $flag=1;
					 }
        
					$line_val='';
					$line_id=explode(",",$row[csf('line_number')]);
					foreach($line_id as $line_id)
					{
						if($line_val=="") $line_val=$line_library[$line_id]; else $line_val.=','.$line_library[$line_id];
					}
					?>
                	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" onClick="js_set_value('<? echo $i.'_'.$row[csf('id')].'_'.$line_val; ?>')" style="cursor:pointer;">
                    	<td width="30"><? echo $i;?></td>
                        <td width="200">
						<? echo $line_val;?>
                        <input type="hidden" id="hidden_old_id<? echo $i; ?>" name="hidden_old_id<? echo $i; ?>" value="<?php echo $flag; ?>" />
                        </td>
                    </tr>
                 <?
				 $i++;
				 }
				 ?>
              </table>
           </div>
        <table width="230">
            <tr align="center">
                <td>
            		<div align="left" style="width:50%; float:left">
                        <input id="check_all" type="checkbox" onclick="check_all_data()" name="check_all">
                            Check / Uncheck All
                    </div>
                    <div align="left" style="width:50%; float:left">
                        <input type="button" name="btn_close" class="formbutton" style="width:100px" value="Close" onClick="fn_onClosed()" />
                    </div>
               </td>
            </tr>
        </table>
         <script>
			set_all_data();
			setFilterGrid("list_view",-1);
		</script>
        <?

	exit();
}

if($action=="report_generate")
{	
	extract($_REQUEST);
	$process = array( &$_POST );
	
	//change_date_format($txt_date);die;
	
	if($db_type==0)	$txt_date=change_date_format($txt_date,'yyyy-mm-dd');
	if($db_type==2)	$txt_date=change_date_format($txt_date,'M-d-Y','',1);
	$txt_date="'".$txt_date."'";
	//echo 'zbdcdbz hjdhgsgdv ghzvgvsgvf dvcgsdvgfcs yugdyufgsyufgy gdfyusgdyufgysgdfgfysdgfhvhhhhhhhhhdsssssssssssssssssssssssssssssssss hsfdsggggggggggggggggggggggggggggggggggggggg hddddddddddddddddddddddddd hddddddddddddddddddddddddddddddd hdddddddddddddddddddddddddddddddddddddddddd';die;
	// extract(check_magic_quote_gpc( $process ));
	$companyArr = return_library_array("select id,company_name from lib_company","id","company_name"); 
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer","id","buyer_name"); 
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name"); 
	$countryArr = return_library_array("select id,country_name from lib_country","id","country_name"); 
	$locationArr = return_library_array("select id,location_name from lib_location","id","location_name"); 
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name"); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line where company_name=$company_id","id","line_name"); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	$company_id=str_replace("'","",$cbo_company_name);
	//**********************************************************************************************
	$lineDataArr = sql_select("SELECT id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1 and company_name=$company_id order by sewing_line_serial"); 
	foreach($lineDataArr as $lRow)
	{
		$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
		$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
		$lastSlNo=$lRow[csf('sewing_line_serial')];
	}
	
	
	if($db_type==0)
	{
		$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$company_id and shift_id=1 and pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");	
	}
	else
	{
		$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$company_id and shift_id=1 and pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
	}
	if($min_shif_start=="")
	{
		echo "<p style='font-size:20px', align='center'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<p/>";
		disconnect($con);
		die;		
	}
	
	
	/*===================================================================================== /
	/										shift time 										/
	/===================================================================================== */

	$start_time_arr=array();
	if($db_type==0)
	{
		$start_time_data_arr=sql_select("select company_name, shift_id, TIME_FORMAT( prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( lunch_start_time, '%H:%i' ) as lunch_start_time from variable_settings_production where company_name in($company_id) and shift_id=1  and variable_list=26 and status_active=1 and is_deleted=0");
	}
	else
	{
		$start_time_data_arr=sql_select("select company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time,TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($company_id) and  shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");	
	}
	
	foreach($start_time_data_arr as $row)
	{
		$start_time_arr[$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
		$start_time_arr[$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
	}
	
	$prod_start_hour=$start_time_arr[1]['pst'];
	$global_start_lanch=$start_time_arr[1]['lst'];
	if($prod_start_hour=="") $prod_start_hour="08:00";
	$start_time=explode(":",$prod_start_hour);
	$hour=substr($start_time[0],1,1); 
	$minutes=$start_time[1]; 
	$last_hour=23;
	$lineWiseProd_arr=array(); $prod_arr=array(); $start_hour_arr=array();
	$start_hour=$prod_start_hour;
	$start_hour_arr[$hour]=$start_hour;
	for($j=$hour;$j<$last_hour;$j++)
	{
		$start_hour=add_time($start_hour,60);
		$start_hour_arr[$j+1]=substr($start_hour,0,5);
	}
	//echo $pc_date_time;die;
	$start_hour_arr[$j+1]='23:59';
	if($prod_start_hour>$min_shif_start)  $prod_start_hour=$min_shif_start;
	$actual_date=date("Y-m-d");
	$actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$txt_date)));
	$actual_time=substr(date("Y-m-d H:i:s",strtotime($pc_date_time)),11,2);	
	$acturl_hour_minute=date("H:i",strtotime($pc_date_time));	
	$generated_hourarr=array();
	$first_hour_time=explode(":",$min_shif_start);
	$hour_line=substr($first_hour_time[0],1,1); $minutes_one=$start_time[1];
	$line_start_hour_arr[$hour_line]=$min_shif_start;
	
	for($l=$hour_line;$l<$last_hour;$l++)
	{
		$min_shif_start=add_time($min_shif_start,60);
		$line_start_hour_arr[$l+1]=substr($min_shif_start,0,5);
	}
	
	$line_start_hour_arr[$j+1]='23:59';

	// echo "<pre>";print_r($start_hour_arr);echo "</pre>";

	/*===================================================================================== /
	/								get actual resource variable							/
	/===================================================================================== */
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and is_deleted=0 
	and status_active=1");

	/*===================================================================================== /
	/										query condition									/
	/===================================================================================== */
		
	//if(str_replace("'","",$company_id)==0) $company_name=""; else $company_name="and a.company_id=".str_replace("'","",$company_id)."";
	if(str_replace("'","",$company_id)==0) $company_name=""; else $company_name="and a.serving_company=".str_replace("'","",$company_id)."";
	
	if(str_replace("'","",$cbo_location_id)==0) 
	{
		$subcon_location="";
		$location="";
	}
	else 
	{
		$location=" and a.location=".str_replace("'","",$cbo_location_id)."";
		$subcon_location=" and a.location_id=".str_replace("'","",$cbo_location_id)."";
	}
	$cbo_floor_id=str_replace("'","",$cbo_floor_id);
	if($cbo_floor_id==0) $floor=""; else $floor="and a.floor_id in(".$cbo_floor_id.")";
    if(str_replace("'","",$hidden_line_id)==0)
	{ 
		$line=""; 
		$subcon_line="";
	}
	else 
	{
		$subcon_line="and a.line_id in(".str_replace("'","",$hidden_line_id).")";
		$line="and a.sewing_line in(".str_replace("'","",$hidden_line_id).")";
	}
	
	if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date=$txt_date";
	/*===================================================================================== /
	/										prod resource data								/location_id
	/===================================================================================== */	
	// echo $prod_reso_allo[0]; die;
	$prod_res_cond = "";
	$prod_res_cond .= (str_replace("'", "", $cbo_location_id)==0) ? "" : " and a.=$cbo_location_id";
	$prod_res_cond .= (str_replace("'", "", $cbo_floor_id)==0) ? "" : " and a.floor_id in($cbo_floor_id)";
	$prod_res_cond .= (str_replace("'", "", $hidden_line_id)==0) ? "" : " and a.id in(".str_replace("'", "", $hidden_line_id).")";
	

	// $production_serial_arr=array();
	if($prod_reso_allo[0]==1)
	{
		$prod_resource_array=array();
		$prod_resource_array2=array();

		$dataArray_sql=sql_select("SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id=$company_id and b.pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $prod_res_cond");
		
		foreach($dataArray_sql as $val)
		{
			$prod_resource_array[$val[csf('id')]]['man_power']=$val[csf('man_power')];
			$prod_resource_array[$val[csf('id')]]['operator']=$val[csf('operator')];
			$prod_resource_array[$val[csf('id')]]['helper']=$val[csf('helper')];
			$prod_resource_array[$val[csf('id')]]['terget_hour']=$val[csf('target_per_hour')];
			$prod_resource_array[$val[csf('id')]]['working_hour']=$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]]['tpd']=$val[csf('target_per_hour')]*$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]]['day_start']=$val[csf('from_date')];
			$prod_resource_array[$val[csf('id')]]['day_end']=$val[csf('to_date')];
			$prod_resource_array[$val[csf('id')]]['capacity']=$val[csf('capacity')];
			$prod_resource_array[$val[csf('id')]]['smv_adjust']=$val[csf('smv_adjust')];
			$prod_resource_array[$val[csf('id')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];

			/*$sewing_line_id=$prod_reso_arr[$val[csf('id')]];			
			
			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id]=$slNo;
			}
			else $slNo=$lineSerialArr[$sewing_line_id];
			$production_serial_arr[$val[csf('floor_id')]][$slNo][$val[csf('id')]]=$val[csf('id')];	*/
		}
		// echo "<pre>";print_r($prod_resource_array);die();

	}
	
	// print_r($extra_minute_arr);die;
	
 	//*********************************************************************
  	if($db_type==0)
	{
		$manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 and id=$company_id","company_id");
	}
	else
	{
		$manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 and id=$company_id","company_id");
	}
	// echo $manufacturing_company;die();
	
	if($db_type==0) $prod_start_cond="prod_start_time";
	else if($db_type==2) $prod_start_cond="TO_CHAR(prod_start_time,'DD-MON-YYYY HH24:MI')";
	
	$variable_start_time_arr='';
	$prod_start_time=sql_select("select $prod_start_cond as prod_start_time from variable_settings_production where company_name=$company_id and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");
	//echo "select company_name, prod_start_time from variable_settings_production where company_name=$company_id and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1";
	foreach($prod_start_time as $row)
	{
		$ex_time=explode(" ",$row[csf('prod_start_time')]);
		if($db_type==0) $variable_start_time_arr=$row[csf('prod_start_time')];
		else if($db_type==2) $variable_start_time_arr=$ex_time[1];
	}//die;
	//echo $variable_start_time_arr;
	unset($prod_start_time);
	$current_date_time=date('d-m-Y H:i');
	$variable_date=change_date_format(str_replace("'","",$txt_date)).' '.$variable_start_time_arr;
	//echo $variable_date.'='.$current_date_time;
	$datediff=datediff("n",$variable_date,$current_date_time);
	
	$ex_date_time=explode(" ",$current_date_time);
	$current_date=$ex_date_time[0];
	$current_time=$ex_date_time[1];
	$ex_time=explode(":",$current_time);
	
	$search_prod_date=change_date_format(str_replace("'","",$txt_date));
	$current_eff_min=($ex_time[0]*60)+$ex_time[1];
	//echo $current_date.'='.$search_prod_date;
	$variable_time= explode(":",$variable_start_time_arr);
	$vari_min=($variable_time[0]*60)+$variable_time[1];
	$difa_time=explode(".",number_format(($current_eff_min-$vari_min)/60,2));//datediff("",$ctime,$variable_start_time_arr);
	$dif_time=number_format($datediff/60,2);
	$dif_hour_min=date("H", strtotime($dif_time));

	
   
	if($db_type==2)
	{
		$pr_date=str_replace("'","",$txt_date);
		$pr_date_old=explode("-",str_replace("'","",$txt_date));
		$month=strtoupper($pr_date_old[1]);
		$year=substr($pr_date_old[2],2);
		$pr_date=$pr_date_old[0]."-".$month."-".$year;
	}
	if($db_type==0)
	{
		$pr_date=str_replace("'","",$txt_date);
	}
	
	$i=1; $grand_total_good=0; $grand_alter_good=0; $grand_total_reject=0;
	$html="";
	$floor_html="";
    $check_arr=array();
	/*===================================================================================== /
	/								get inhouse production data								/
	/===================================================================================== */
	if($db_type==0)
	{
		$sql="SELECT  a.company_id, a.location, a.floor_id,d.floor_serial_no,e.sewing_line_serial, a.prod_reso_allo, a.production_date, a.sewing_line,
		b.buyer_name  as buyer_name,b.style_ref_no,b.job_no,a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.unit_price,c.file_no,c.grouping as ref,d.color_type_id,a.remarks,sum(a.production_quantity) as good_qnty,"; 
		$first=1;
		for($h=$hour;$h<$last_hour;$h++)
		{
			$bg=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="prod_hour".substr($bg,0,2);
			if($first==1)
			{
				$sql.="sum(CASE WHEN   a.production_hour<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,";
			}
			else
			{
				$sql.="sum(CASE WHEN a.production_hour>'$bg' and  a.production_hour<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,";
			}
			$first=$first+1;
		}
		$sql.="sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23 from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a left join lib_prod_floor d on a.floor_id=d.id and d.is_deleted=0 left join lib_sewing_line e on a.sewing_line=e.id and e.is_deleted=0  where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $company_name $location $floor $line $buyer_id_cond  $txt_date_from group by b.job_no,a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,e.sewing_line_serial,b.buyer_name,a.item_number_id,c.po_number,c.file_no,c.unit_price,c.grouping,b.style_ref_no,d.color_type_id,a.remarks order by a.location, a.floor_id,e.sewing_line_serial,a.prod_reso_allo";
	}
	else if($db_type==2)
	{
		$sql="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name  as buyer_name,b.style_ref_no,b.job_no_prefix_num,b.job_no, a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.file_no,c.unit_price,c.grouping as ref,c.shipment_date,e.color_number_id,e.country_id, d.color_type_id,a.remarks,sum(d.production_qnty) as good_qnty,TO_CHAR(a.production_hour,'HH24:MI') as prod_hour,"; 
		$first=1;
		for($h=$hour;$h<$last_hour;$h++)
		{
			$bg=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="prod_hour".substr($bg,0,2);
			if($first==1)
			{
				$sql.="sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN production_qnty else 0 END) AS $prod_hour,";
			}
			else
			{
				$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 
				THEN production_qnty else 0 END) AS $prod_hour,";
			}
			$first++;
		}
		$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN production_qnty else 0 END) AS prod_hour23 
		FROM  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a ,pro_garments_production_dtls d,wo_po_color_size_breakdown e
		WHERE a.production_type=5 and a.po_break_down_id=c.id and c.job_id=b.id and b.id=e.job_id and c.id=e.po_break_down_id and e.id=d.color_size_break_down_id and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $company_name $location $floor $line $buyer_id_cond  $txt_date_from 
		GROUP BY b.job_no,b.job_no_prefix_num, a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name,b.style_ref_no,a.item_number_id,c.po_number,c.unit_price,c.file_no,c.grouping ,d.color_type_id,a.remarks,a.production_hour,c.shipment_date,e.color_number_id,e.country_id
		ORDER BY a.location,a.floor_id,a.sewing_line";
	}
	 //echo $sql;die;
	$sql_resqlt=sql_select($sql);
	$production_data_arr=array();
	$production_po_data_arr=array();
	$production_serial_arr=array();
	$reso_line_ids=''; 
	$all_po_id_arr=array();
	$all_style_arr=array();
	$active_days_arr=array();
	$duplicate_date_arr=array();
	$poIdArr=array();
	$prod_line_array = array();
	$line_style_chk_array = array();
	$line_hour_wise_job_array = array();
	$line_wise_job_info_array = array();
	foreach($sql_resqlt as $val)
	{	
		$prod_line_array[$val[csf('sewing_line')]] = $val[csf('sewing_line')];
		$poIdArr[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];	 
		if($val[csf('prod_reso_allo')]==1)
		{
			// $sewing_line_id=$prod_reso_arr[$val[csf('sewing_line')]];
			$reso_line_ids.=$val[csf('sewing_line')].',';	
			$sewing_line_ids=$prod_reso_arr[$val[csf('sewing_line')]];
			$sl_ids_arr = explode(",", $sewing_line_ids);
			$sewing_line_id = $sl_ids_arr[0]; // always 1st line id will take
		}
		else
		{
			$sewing_line_id=$val[csf('sewing_line')];
		}
		
		if($lineSerialArr[$sewing_line_id]=="")
		{
			$lastSlNo++;
			$slNo=$lastSlNo;
			$lineSerialArr[$sewing_line_id]=$slNo;
		}
		else $slNo=$lineSerialArr[$sewing_line_id];
		$production_serial_arr[$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]]=$val[csf('sewing_line')];
		
		$line_start=$line_number_arr[$val[csf('sewing_line')]][$val[csf('production_date')]]['prod_start_time'];
		if($line_start!="") 
		{ 
			$line_start_hour=substr($line_start,0,2); 
			if(substr($line_start_hour,0,1)==0)  $line_start_hour=substr($line_start_hour,1,1);	
		}
		else
		{
			$line_start_hour=$hour; 
		}
		
	 	for($h=$hour;$h<$last_hour;$h++)
		{
			$prod_hour="prod_hour".substr($start_hour_arr[$h],0,2)."";
			$production_data_arr[$val[csf('sewing_line')]][$prod_hour]+=$val[csf($prod_hour)]; 
			if($line_hour_wise_job_array[$val[csf('sewing_line')]][substr($val[csf('prod_hour')],0,2)-0]=="")
			{
				$line_hour_wise_job_array[$val[csf('sewing_line')]][substr($val[csf('prod_hour')],0,2)-0] = $val[csf('job_no_prefix_num')];
			}
			else
			{
				$line_hour_wise_job_array[$val[csf('sewing_line')]][substr($val[csf('prod_hour')],0,2)-0] .= ",".$val[csf('job_no_prefix_num')];
			}
			
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
			{
				if( $h>=$line_start_hour && $h<=$actual_time)
				{
					$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf($prod_hour)]; 
				} 	
			}
			
			if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date)) 
			{	
				$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf($prod_hour)];
			}
		}
		
		if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
		{	
			if( $h>=$line_start_hour && $h<=$actual_time)
			{
				$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf('prod_hour23')];     
			} 	
		}
		else
		{
			$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf('prod_hour23')];     
		}
		
	 	$production_data_arr[$val[csf('sewing_line')]]['prod_hour23']+=$val[csf('prod_hour23')];  
		$production_data_arr[$val[csf('sewing_line')]]['prod_reso_allo']=$val[csf('prod_reso_allo')]; 
				
		if($line_wise_job_info_array[$val[csf('sewing_line')]]['job_no']!="")
		{
			$line_wise_job_info_array[$val[csf('sewing_line')]]['job_no'].="**".$val[csf('job_no')];
			$line_wise_job_info_array[$val[csf('sewing_line')]]['buyer_name'].="**".$buyerArr[$val[csf('buyer_name')]];
			$line_wise_job_info_array[$val[csf('sewing_line')]]['style'].="**".$val[csf('style_ref_no')];
			$line_wise_job_info_array[$val[csf('sewing_line')]]['po_number'].="**".$val[csf('po_number')];
			$line_wise_job_info_array[$val[csf('sewing_line')]]['country_id'].="**".$countryArr[$val[csf('country_id')]];
			$line_wise_job_info_array[$val[csf('sewing_line')]]['color_number_id'].="**".$colorArr[$val[csf('color_number_id')]];
			$line_wise_job_info_array[$val[csf('sewing_line')]]['shipment_date'].="**".$val[csf('shipment_date')];
		}
	 	else
		{
			$line_wise_job_info_array[$val[csf('sewing_line')]]['job_no']=$val[csf('job_no')]; 
			$line_wise_job_info_array[$val[csf('sewing_line')]]['buyer_name']=$buyerArr[$val[csf('buyer_name')]]; 
			$line_wise_job_info_array[$val[csf('sewing_line')]]['style']=$val[csf('style_ref_no')]; 
			$line_wise_job_info_array[$val[csf('sewing_line')]]['po_number']=$val[csf('po_number')]; 
			$line_wise_job_info_array[$val[csf('sewing_line')]]['country_id']=$countryArr[$val[csf('country_id')]]; 
			$line_wise_job_info_array[$val[csf('sewing_line')]]['color_number_id']=$colorArr[$val[csf('color_number_id')]]; 
			$line_wise_job_info_array[$val[csf('sewing_line')]]['shipment_date']=$val[csf('shipment_date')]; 
		}
	
	 	if($production_data_arr[$val[csf('sewing_line')]]['job_no']!="")
		{
			$production_data_arr[$val[csf('sewing_line')]]['job_no'].=",".$val[csf('job_no_prefix_num')];
			$production_data_arr[$val[csf('sewing_line')]]['po_id'].=",".$val[csf('po_break_down_id')]; 
		}
	 	else
		{
			$production_data_arr[$val[csf('sewing_line')]]['job_no']=$val[csf('job_no_prefix_num')];
			$production_data_arr[$val[csf('sewing_line')]]['po_id']=$val[csf('po_break_down_id')]; 
		}
		$production_data_arr[$val[csf('sewing_line')]]['quantity']+=$val[csf('good_qnty')];
		$production_data_arr_qty[$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]['quantity']+=$val[csf('good_qnty')];

		if($production_data_arr[$val[csf('sewing_line')]]['item_number_id']!="")
		{
			$production_data_arr[$val[csf('sewing_line')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]; 
		}
		else
		{
			 $production_data_arr[$val[csf('sewing_line')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]; 
		}

		$style_wise_po_arr[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
		
		$all_po_id_arr[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
		$all_style_arr[$val[csf('style_ref_no')]] = $val[csf('style_ref_no')];
	}


	//print_r($style_wise_po_arr);die;

	
	$poIds_cond = where_con_using_array($all_po_id_arr,0,"b.id");
	$poIds_cond2 = where_con_using_array($all_po_id_arr,0,"c.id");
	$poIds_cond3 = where_con_using_array($all_po_id_arr,0,"po_break_down_id");
	$style_cond = where_con_using_array($all_style_arr,1,"a.style_ref");

	
	
	/*===================================================================================== /
	/										smv sorce 										/
	/===================================================================================== */
   	$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($manufacturing_company) and variable_list=25 and   status_active=1 and is_deleted=0");
	 //echo $manufacturing_company;
	
	if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
    if($smv_source==3) // from gsd enrty
	{
		$style_nos="'".implode("','",$all_style_arr)."'";
		$sql_item="SELECT a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID  from PPL_GSD_ENTRY_MST a where a.APPLICABLE_PERIOD <= $txt_date and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 $style_cond and a.APPROVED=1 group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID ORDER BY a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC";
		$gsdSqlResult=sql_select($sql_item);
		   //echo $sql_item;die;
		
		foreach($gsdSqlResult as $rows)
		{
			foreach($style_wise_po_arr[$rows['STYLE_REF']] as $po_id)
			{
				if($item_smv_array[$po_id][$rows['GMTS_ITEM_ID']]=='')
				{
					$item_smv_array[$po_id][$rows['GMTS_ITEM_ID']]=$rows['TOTAL_SMV'];
				}
			}
		}
	}
	else
	{
		$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost,c.smv_set from wo_po_details_master a,wo_po_break_down b, wo_po_details_mas_set_details c where b.job_id=a.id and b.job_id=c.job_id and a.company_name in($manufacturing_company) $poIds_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$resultItem=sql_select($sql_item);
		
		foreach($resultItem as $itemData)
		{
			if($smv_source==1)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
			}
			if($smv_source==2)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs_precost')];
			}
		}
	}
	
	
	
	
	
	
	// echo $sql_item;
	// echo "<pre>";print_r($item_smv_array);echo "</pre>";
	/*===================================================================================== /
	/										po active days									/
	/===================================================================================== */
    $po_active_sql="SELECT a.sewing_line,a.production_date,b.job_no_prefix_num as job_no from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $company_name $location $floor $line $buyer_id_cond  $poIds_cond2 group by  a.sewing_line,a.production_date,b.job_no_prefix_num";
    //echo $po_active_sql;die;
	foreach(sql_select($po_active_sql) as $vals)
	{
		$prod_dates=$vals[csf('production_date')];
		if($duplicate_date_arr[$vals[csf('sewing_line')]][$vals[csf('job_no')]][$prod_dates]=="")
		{
			$active_days_arr[$vals[csf('sewing_line')]]++;
			$active_days_arr_powise[$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]]+=1;
			$duplicate_date_arr[$vals[csf('sewing_line')]][$vals[csf('job_no')]][$prod_dates]=$prod_dates;
		}
	}
	// echo "<pre>"; print_r($active_days_arr);
	

	/*===================================================================================== /
	/							prod resource data no prod line								/
	/===================================================================================== */	
	// echo $prod_reso_allo[0]; die;

	if(count($prod_line_array)>0)
	{
		$prod_line_id_cond = where_con_using_array($prod_line_array,0," a.id not ");
	}
	
	if($prod_reso_allo[0]==1)
	{
		$dataArray_sql=sql_select("SELECT a.id, a.floor_id from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id=$company_id and b.pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $prod_res_cond $prod_line_id_cond  ");//and a.id not in($prod_line_ids)
		
		foreach($dataArray_sql as $val)
		{
			// $sewing_line_id=$prod_reso_arr[$val[csf('id')]];		
			$sewing_line_ids=$prod_reso_arr[$val[csf('id')]];
			$sl_ids_arr = explode(",", $sewing_line_ids);
			$sewing_line_id = $sl_ids_arr[0]; // always 1st line id will take	
			
			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id]=$slNo;
			}
			else $slNo=$lineSerialArr[$sewing_line_id];
			$production_serial_arr[$val[csf('floor_id')]][$slNo][$val[csf('id')]]=$val[csf('id')];		
		}		
	}
		
	// echo "<pre>"; print_r($production_serial_arr);die;

	
	// $tbl_width = 715+($last_hour - ($hour+1))*40;
	$p = 1;
	$tot_td = 0;
	for($k=$hour; $k<=$last_hour; $k++)
	{
		$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
										
		if($p <= 11)
		{	
			$tot_td++;
		}
		$p++;
	}
	$tbl_width = $page_width-20;
	$td_width = round(($tbl_width-765)/$tot_td);
	$page_height = $page_height - 60;

	$floor_wise_tot_line = array();
	$gr_tot_line = 0;
	$gr_tot_floor = 0;
	$line_wise_prod_arr = array();
	foreach ($production_serial_arr as $fkey => $fvalue) 
	{
		foreach ($fvalue as $slkey => $slvalue) 
		{
			foreach ($slvalue as $lkey => $val) 
			{
				$floor_wise_tot_line[$fkey]++;
				$gr_tot_line++;

				// ================ check line wise production ==============
				for($k=$hour; $k<=$last_hour; $k++)
				{
					$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
					$line_wise_prod_arr[$fkey][$slkey][$lkey] += $production_data_arr[$lkey][$prod_hour];
					
				}
			}
		}
		$gr_tot_floor++;
	}

	$tot_tr = $gr_tot_floor+$gr_tot_line+2;
	$tr_height = ceil(($page_height-16)/$tot_tr);
	$font_size = ceil(($tr_height/5))."px";

	// echo "<pre>"; print_r($line_wise_prod_arr);die;
	$floor_name = "";
	$fid_arr = array_filter(explode(",", $cbo_floor_id));
	if(count($fid_arr)>0)
	{
		foreach ($fid_arr as $val) 
		{
			$floor_name.= ($floor_name=="") ? $floorArr[$val] : ",".$floorArr[$val];
		}
	}
	else
	{
		$floor_name = "All Unit";
	}

	$line_name = "";
	$line_id_arr = array_filter(explode(",", str_replace("'","",$hidden_line_id)));
	if(count($line_id_arr)>0)
	{
		foreach ($line_id_arr as $v) 
		{
			$line_number=explode(",",$prod_reso_arr[$v]);
			foreach($line_number as $val)
			{
				if($line_name=='') $line_name=$lineArr[$val]; else $line_name.=",".$lineArr[$val];
			}
		}
	}
	else
	{
		$line_name = "All Line";
	}
	ob_start();
    ?>          
	<div style="width:<? echo $tbl_width;?>px;background:#000000;">
		<style type="text/css">
			td div{font-weight: bold;font-size: 16px;vertical-align: middle;}
			#new_style div { position: relative; }
			td#new_style div::before { position: absolute; left: 0; top: 0; width: 100%; height: 50%; background: yellow;z-index: 99999; }
			#new_style div { box-shadow: inset 0px 7px 0px yellow; }
			.rpt_table tfoot th, td,td p{font-weight: bold;font-size: 16px;vertical-align: middle;color: #FFFFFF;}
			.rpt_table thead th,.rpt_table tfoot th{background: #191A19;}
			.rpt_table thead th{color: #FFFFFF;font-weight: bold;font-size: 18px;}
			.rpt_table tfoot th{font-weight: bold;font-size: 18px;}
			.rpt_info tr td{color: #000000;font-weight: bold;font-size: 20px;}

		</style>
       
            <table class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
				<thead>
					<tr height="<?=$tr_height;?>" style="font-size: <?=$font_size;?>">
						<th width="60"><p>Line No</p></th>
						<th width="75"><p>Job</p></th>
						<th width="150"><p>Gmts Item</p></th>
						<th width="45"><p>OP</p></th>
						<th width="45"><p>HP</p></th>
						<th width="45"><p>MP</p></th>
						<th width="45"><p>SMV</p></th>
						<th width="60"><p>Day Target</p></th>
						<th width="60"><p>Target/Hr</p></th>
					<?
					// print_r($production_hour);
						$p = 1;
						for($k=$hour; $k<=$last_hour; $k++)
						{
							$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
															
							if($p <= 11)
							{
								?>
								<th title="<?=substr($start_hour_arr[$k],0,5)."-".substr($start_hour_arr[$k+1],0,5);?>" width="<?=$td_width;?>" style="vertical-align:middle"><div class="block_div"><?  echo "H".$p; ?></div></th>
								<?	
							}
							/*elseif ($production_hour[$prod_hour]>0 && $p>11) 
							{
								?>
								<th width="40" style="vertical-align:middle"><div class="block_div"><?  echo "H".$p; ?></div></th>
								<?
							}*/
							$p++;
						}
						?> 
						<th width="60"><p>TTL Prod.</p></th>                   
						<th width="60"><p>Achv. %</p></th>                   
						<th width="60"><p>Days Run</p></th> 
					</tr>
				</thead>              
                <tbody>
                	<?
                	$i=1;
                	$gr_op  = 0;
                	$gr_hp  = 0;
                	$gr_mp  = 0;
                	$gr_day_trgt  = 0;
                	$gr_trgt_hr  = 0;
                	$gr_ttl_prd  = 0;
                	$gr_tot_smv  = 0;
                	$gr_tot_achv  = 0;
                	$total_arr = array();
                	// ksort($production_serial_arr);
                	foreach($production_serial_arr as $floor_id=>$flr_data) 
                	{
                		$f_total_arr = array();
	                	$flr_op  = 0;
	                	$flr_hp  = 0;
	                	$flr_mp  = 0;
	                	$flr_day_trgt  = 0;
	                	$flr_trgt_hr  = 0;
	                	$flr_ttl_prd  = 0;
	                	$flr_tot_achv  = 0;
	                	$flr_tot_smv = 0;
                		ksort($flr_data);
	                	foreach($flr_data as $sl=>$sl_data) 
	                	{
	                		foreach($sl_data as $l_id=>$ldata)
							{   
								$bgcolor=($i%2==0)?"#000000":"#000000";
								$sewing_line='';
								if($production_data_arr[$l_id]['prod_reso_allo']!="")
								{
									if($production_data_arr[$l_id]['prod_reso_allo']==1)
									{
										$line_number=explode(",",$prod_reso_arr[$l_id]);
										foreach($line_number as $val)
										{
											if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
										}
									}
									else $sewing_line=$lineArr[$l_id];
								}
								else
								{
									$line_number=explode(",",$prod_reso_arr[$l_id]);
									foreach($line_number as $val)
									{
										if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
									}
									
								}         
								$terget_hour = $prod_resource_array[$l_id]['terget_hour'];    

								$germents_item=array_unique(explode('****',$production_data_arr[$l_id]['item_number_id']));

								$item_smv="";
								$item_name="";
								$chk_smv_arr = array();
								foreach($germents_item as $g_val)
								{
									
									$po_garment_item=explode('**',$g_val);
									// if($item_smv!='') $item_smv.='/';
									//echo $po_garment_item[0].'='.$po_garment_item[1];	
									if(!in_array($item_smv_array[$po_garment_item[0]][$po_garment_item[1]], $chk_smv_arr))	
									{				
										if($item_smv!='') $item_smv.='/';		
										$item_smv.=$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
										$chk_smv_arr[$item_smv_array[$po_garment_item[0]][$po_garment_item[1]]] = $item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
										$flr_tot_smv += $item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
										$gr_tot_smv += $item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
									}
									$item_name .= ($item_name=="") ? $garments_item[$po_garment_item[1]] : ",".$garments_item[$po_garment_item[1]];
								}

								$job_no_arr = array_unique(explode(",", $production_data_arr[$l_id]['job_no']));
								$job_no = implode(",", $job_no_arr);
								$style = "";
								if($line_wise_prod_arr[$floor_id][$sl][$l_id]<1)
								{
									$style = "color: #949494;";
								}

								// ============== for title ===============
								$title = "";
								$buyer = implode(",", array_unique(array_filter(explode("**", $line_wise_job_info_array[$l_id]['buyer_name']))));
								$jobNo = implode(",", array_unique(array_filter(explode("**", $line_wise_job_info_array[$l_id]['job_no']))));
								$styleName = implode(",", array_unique(array_filter(explode("**", $line_wise_job_info_array[$l_id]['style']))));
								$colorName = implode(",", array_unique(array_filter(explode("**", $line_wise_job_info_array[$l_id]['color_number_id']))));
								$POName = implode(",", array_unique(array_filter(explode("**", $line_wise_job_info_array[$l_id]['po_number']))));
								$countryName = implode(",", array_unique(array_filter(explode("**", $line_wise_job_info_array[$l_id]['country_id']))));
								$shipment_date = implode(",", array_unique(array_filter(explode("**", $line_wise_job_info_array[$l_id]['shipment_date']))));
								$capacity = $prod_resource_array[$l_id]['capacity'];

								$title .= "Buyer : ".$buyer."&#013;";
								$title .= "Job No : ".$jobNo."&#013;";
								$title .= "Style : ".$styleName."&#013;";
								$title .= "Color : ".$colorName."&#013;";
								$title .= "PO : ".$POName."&#013;";
								$title .= "Country : ".$countryName."&#013;";
								$title .= "Shipment Date : ".$shipment_date."&#013;";
								$title .= "MC Capacity : ".$capacity."&#013;";
			                	?>
			                   <tr height="<?=$tr_height;?>" bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" title="<?=$title;?>">
			                        <td width="60" title="<?=$floor_id.'='.$sl;?>"><p style="<?=$style;?>"><?=$sewing_line;?></p></td>
				                    <td width="75"><p style="<?=$style;?>"><?=$job_no;?></p></td>
				                    <td width="150"><p style="<?=$style;?>"><?=implode(", ", array_unique(explode(",", $item_name)));?></p></td>
				                    <td align="right" width="45"><p style="<?=$style;?>"><?=$prod_resource_array[$l_id]['operator'];?></p></td>
				                    <td align="right" width="45"><p style="<?=$style;?>"><?=$prod_resource_array[$l_id]['helper'];?></p></td>
				                    <td align="right" width="45"><p style="<?=$style;?>"><?=$prod_resource_array[$l_id]['man_power'];?></p></td>
				                    <td width="45" align="center"><p style="<?=$style;?>"><?=$item_smv;?></p></td>
				                    <td align="right" width="60"><p style="<?=$style;?>"><?=number_format($prod_resource_array[$l_id]['tpd'],0);?></p></td>
				                    <td align="right" width="60"><p style="<?=$style;?>"><?=number_format($prod_resource_array[$l_id]['terget_hour'],0);?></p></td>
			                        <?
			                        $p = 1;
			                        $line_tot = 0;
			                        $new_color = "";
			                        $new_style = "";
			        				$job_chk_arr = array();
									for($k=$hour; $k<=$last_hour; $k++)
									{
										$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
										$color="";
										if($production_data_arr[$l_id][$prod_hour]==$terget_hour)
										{
											$color="green";
										}
										elseif ($production_data_arr[$l_id][$prod_hour]>$terget_hour) 
										{
											$color="blue";
										}
										elseif ($production_data_arr[$l_id][$prod_hour]<$terget_hour && $production_data_arr[$l_id][$prod_hour]>0) 
										{
											$color="red";
										}


										if(count($job_no_arr)>1)//when multiple job in a single line
										{
											$job_nos = $line_hour_wise_job_array[$l_id][substr($start_hour_arr[$k],0,2)+1];
											$hour_wise_job_arr = array_unique(explode(",", $job_nos));
											// echo $job_nos."=".$l_id."=".(substr($start_hour_arr[$k],0,2))."<br>";
											if($job_nos!="")
											{											
												if($p >1) // check start after 1st hour
												{
													if(!in_array($job_nos, $job_chk_arr))
													{
														$new_style = 'new_style';
														$title = 'title="'.implode(",", $hour_wise_job_arr).'"';
													}
												}
												$job_chk_arr[$job_nos]=$job_nos;
												// print_r($job_chk_arr);

												
												// echo "<pre>"; print_r($hour_wise_job_arr);	echo "</pre>";
												// echo count($hour_wise_job_arr)."<br>";
												if(count($hour_wise_job_arr)>1) // when multi job run in same line and same hour
												{
													$new_style = 'new_style';
													$title = 'title="'.implode(",", $hour_wise_job_arr).'"';
												}	
											}
											
										}										

										if($p <= 11)
										{
											?>
						                      <td bgcolor="<?=$color;?>" align="right" width="<?=$td_width;?>" id="<?=$new_style;?>" <?//=$title;?> >
						                      	<?//=$new_color;?>
						                      	<div style="<?=$style;?>">
						                      		<?=number_format($production_data_arr[$l_id][$prod_hour],0); ?>
						                      	</div>
						                      		
						                      	</td>
											<?	
										}
										/*elseif ($total_production[$prod_hour]>0 && $p>11) 
										{
											?>
						                      <td align="right" width="70"><?=number_format($production_data_arr[$l_id][$prod_hour],0);?></td>
											<?
										}*/
										$p++;
										$line_tot += $production_data_arr[$l_id][$prod_hour];
										$total_arr[$prod_hour] += $production_data_arr[$l_id][$prod_hour];
										$f_total_arr[$prod_hour] += $production_data_arr[$l_id][$prod_hour];
									}

									$day_target = $prod_resource_array[$l_id]['tpd'];
									$color = "";
									if($line_tot==$day_target)
									{
										$color="green";
									}
									elseif ($line_tot>$day_target) 
									{
										$color="blue";
									}
									elseif ($line_tot<$day_target && $line_tot>0) 
									{
										$color="red";
									}
			                        ?>
								    <td bgcolor="<?=$color;?>" align="right" width="60" style="<?=$style;?>"><?=number_format($line_tot,0);?></td>                   
				                    <td bgcolor="<?=$color;?>" style="<?=$style;?>" align="right" width="60"><?=($line_tot>0) ? number_format(($line_tot/$prod_resource_array[$l_id]['tpd'])*100,0) : 0;?>%</td>                   
				                    <td style="<?=$style;?>" align="right" width="60"><?=number_format($active_days_arr[$l_id],0);?></td> 
			                    </tr>
			                    <?
			                    $i++;

			                	$flr_op  += $prod_resource_array[$l_id]['operator'];
			                	$flr_hp  += $prod_resource_array[$l_id]['helper'];
			                	$flr_mp  += $prod_resource_array[$l_id]['man_power'];
			                	$flr_day_trgt  += $prod_resource_array[$l_id]['tpd'];
			                	$flr_trgt_hr  += $prod_resource_array[$l_id]['terget_hour'];
			                	$flr_ttl_prd  += $line_tot;
			                	$flr_tot_achv  += ($line_tot/$prod_resource_array[$l_id]['tpd'])*100;

			                	$gr_op  += $prod_resource_array[$l_id]['operator'];
			                	$gr_hp  += $prod_resource_array[$l_id]['helper'];
			                	$gr_mp  += $prod_resource_array[$l_id]['man_power'];
			                	$gr_day_trgt  += $prod_resource_array[$l_id]['tpd'];
			                	$gr_trgt_hr  += $prod_resource_array[$l_id]['terget_hour'];
			                	$gr_ttl_prd  += $line_tot;
			                	$gr_tot_achv  += ($line_tot/$prod_resource_array[$l_id]['tpd'])*100;
			                }
			            }
			            ?>
			            <tr height="<?=$tr_height;?>" style="background: #1F1D36;text-align: right;">
	                        <td colspan="3" ><?=$floorArr[$floor_id];?> : </td>
		                    <td width="45"><?=number_format($flr_op,0);?></td>
		                    <td width="45"><?=number_format($flr_hp,0);?></td>
		                    <td width="45"><?=number_format($flr_mp,0);?></td>
		                    <td width="45"><?=number_format(($flr_tot_smv/$floor_wise_tot_line[$floor_id]),2);?></td>
		                    <td width="60"><?=number_format($flr_day_trgt,0);?></td>
		                    <td width="60"><?=number_format($flr_trgt_hr,0);?></td>
	                        <?
	                        $p = 1;
	                        $total = 0;
							for($k=$hour; $k<=$last_hour; $k++)
							{
								$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
															
								if($p <= 11)
								{
									?>
				                      <td align="right" width="<?=$td_width;?>"><? echo number_format($f_total_arr[$prod_hour],0); ?></td>
									<?	
								}
								/*elseif ($total_production[$prod_hour]>0 && $p>11) 
								{
									?>
				                      <td align="right" width="70"><? echo $total_production[$prod_hour]; ?></td>
									<?
								}*/
								$p++;
								$total += $f_total_arr[$prod_hour];
							}
	                        ?>
						    <td width="60"><?=number_format($total,0);?></td>                   
		                    <td width="60"><?=number_format(($flr_tot_achv/$floor_wise_tot_line[$floor_id]),0);?>%</td>                   
		                    <td width="60"></td> 
	                    </tr>
			            <?
			        }
		            ?>
					
					<tr height="<?=$tr_height;?>" style="background: #1F1D36;text-align:right;">
                        <!-- <th width="60"></th>
	                    <th width="75"></th> -->
	                    <td colspan="3" >Grand Total : </td>
	                    <td width="45"><?=number_format($gr_op,0);?></td>
	                    <td width="45"><?=number_format($gr_hp,0);?></td>
	                    <td width="45"><?=number_format($gr_mp,0);?></td>
	                    <td width="45"><?=number_format(($gr_tot_smv/$gr_tot_line),2);?></td>
	                    <td width="60"><?=number_format($gr_day_trgt,0);?></td>
	                    <td width="60"><?=number_format($gr_trgt_hr,0);?></td>
                        <?
                        $p = 1;
                        $total = 0;
						for($k=$hour; $k<=$last_hour; $k++)
						{
							$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
														
							if($p <= 11)
							{
								?>
			                      <td align="right" width="<?=$td_width;?>"><? echo number_format($total_arr[$prod_hour],0); ?></td>
								<?	
							}
							/*elseif ($total_production[$prod_hour]>0 && $p>11) 
							{
								?>
			                      <td align="right" width="70"><? echo $total_production[$prod_hour]; ?></td>
								<?
							}*/
							$p++;
							$total += $total_arr[$prod_hour];
						}
                        ?>
					    <td width="60"><?=number_format($total,0);?></td>                   
					    <td width="60"><?=number_format(($gr_tot_achv/$gr_tot_line),0);?>%</td> 
	                    <td width="60"></td> 
                    </tr>
                </tfoot>
            </table>
		<!-- </div> -->
		 <!-- <table class="rpt_table" width="<?= $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
              
                <tfoot>
                </tfoot>
            </table> -->
	</div>

	<script type="text/javascript">
		$(document).ready(function () 
		{
	        /*var div_height = screen.height - 220;
	        var div_width = screen.width - 50;
	        document.getElementById("scroll_body").style.maxHeight  = div_height+'px'; 
	        document.getElementById("scroll_body").style.width  = div_width+'px'; 
	        document.getElementById("scroll_body").style.overflowY = "scroll"; */
	    });
	</script>
	<?    
	$user_id=($user_id=='')?1000000000000:$user_id;

	foreach (glob("$user_id*.xls") as $filename) 
	{
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename,'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename####".date('d-m-Y');
	disconnect($con);
	exit();      

}
?>