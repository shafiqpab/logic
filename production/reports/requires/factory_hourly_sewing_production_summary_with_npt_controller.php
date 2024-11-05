<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.others.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","");  
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/factory_hourly_sewing_production_summary_with_npt_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_floor', 'floor_td' );",0 ); 
	exit();    	 
}

if ($action=="load_drop_down_floor")
{
	$dataEx = explode("_", $data);
	echo create_drop_down( "cbo_floor_id", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$dataEx[0]' and company_id=$dataEx[1] and production_process=5 order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );     	 	
	exit();    	 
}

if($action=="line_search_popup")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
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
		
		function fn_onClosed()
		{
			parent.emailwindow.hide();
		}
    </script>
	<?
	extract($_REQUEST);
	//echo $company;die;
	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name");
	if($company==0) $company_name=""; else $company_name=" and b.company_name=$company";//job_no
	
	if($buyer==0) $buyer_name=""; else $buyer_name="and b.buyer_name=$buyer";
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company and variable_list=23 and is_deleted=0 and status_active=1");
	$cond ="";
    if($prod_reso_allo==1)
	{
		$line_array=array();
		if($txt_date=="")
		{
			$data_format="";
		}
		else
		{
			if($db_type==0)	$data_format="and b.pr_date='".change_date_format($txt_date,'yyyy-mm-dd')."'";
			if($db_type==2)	$data_format="and b.pr_date='".change_date_format($txt_date,'','',1)."'";
		}
		if( $location!=0 ) $cond .= " and a.location_id= $location";
		if( $floor_id!=0 ) $cond.= " and a.floor_id= $floor_id";
		
		$line_sql="SELECT a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id $data_format and a.is_deleted=0 and b.is_deleted=0 and a.company_id=$company $cond group by a.id, a.line_number";
		// echo $line_sql;
		$line_sql_result=sql_select($line_sql);
		
		?>
            <input type='hidden' id='txt_selected_id' />
            <input type='hidden' id='txt_selected' />
            <table cellspacing="0" width="250"  border="1" rules="all" class="rpt_table" >
            	<thead>
                	<th width="30"></th>
                    <th width="200">Line Name</th>
                </thead>
            </table>
            <div style="width:250px; max-height:350px; overflow-y:scroll" id="scroll_body" >          
        		<table cellspacing="0" width="230"  border="1" rules="all" class="rpt_table" id="list_view" >
                <? $i=1;
				 foreach($line_sql_result as $row)
				 {
        			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
        
					$line_val='';
					$line_id=explode(",",$row[csf('line_number')]);
					foreach($line_id as $line_id)
					{
						if($line_val=="") $line_val=$line_library[$line_id]; else $line_val.=','.$line_library[$line_id];
					}
					?>
                	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" onClick="js_set_value('<? echo $i.'_'.$row[csf('id')].'_'.$line_val; ?>')" style="cursor:pointer;">
                    	<td><? echo $i;?></td>
                        <td><? echo $line_val;?></td>
                    </tr>
                 <?
				 $i++;
				 }
				 ?>
              </table>
           </div>
        <table width="250">
            <tr align="center">
                <td><input type="button" name="btn_close" class="formbutton" style="width:100px" value="Close" onClick="fn_onClosed()" /></td>
            </tr>
        </table>
        <?
	}
	else
	{
		if( $location!=0  ) $cond = " and location_name= $location";
		if( $floor_id!=0 ) $cond.= " and floor_name= $floor_id";
		$line_data="select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name";
		echo create_list_view("list_view", "Line No","250","300","310",0, $line_data , "js_set_value", "id,line_name", "", 1, "0", $arr, "line_name", 
		"","setFilterGrid('list_view',-1)","0","",1) ;	
		echo "<input type='hidden' id='txt_selected_id' />";
		echo "<input type='hidden' id='txt_selected' />";
	}
	exit();
}

if($action=="report_generate")
{
	/*
		smv source : as per variable settings
	*/
	$starttime = microtime(true);
	extract($_REQUEST);
	$process = array( &$_POST );	
	extract(check_magic_quote_gpc( $process ));

	function secondsToTime($s)
	{
	    $h = floor($s / 3600);
	    $s -= $h * 3600;
	    $m = floor($s / 60);
	    $s -= $m * 60;
	    return $h.':'.sprintf('%02d', $m).':'.sprintf('%02d', $s);
	}

	$start_date = $txt_date_from;
	
	$start_date_ex = explode("-", str_replace("'", "", $txt_date_from));
	$txt_date_from = "'01-Jan-".$start_date_ex[2]."'";// $txt_date_to;
	if($db_type==0)
	{
		$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
		$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	/*if($db_type==2)
	{	
		$txt_date_from=change_date_format($txt_date_from,'','',1);
		$txt_date_to=change_date_format($txt_date_to,'','',1);
	}*/
	// echo $txt_date_from;die();
	$company_id=str_replace("'","",$cbo_company_id);
	$companyArr = return_library_array("select id,company_name from lib_company","id","company_name"); 
	$buyerArr = return_library_array("select id,short_name from lib_buyer","id","short_name"); 
	$locationArr = return_library_array("select id,location_name from lib_location","id","location_name"); 
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name"); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line where company_name='$company_id'","id","line_name"); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	
    $today_date=date("Y-m-d");
	//**********************************************************************************************
	$lineDataArr = sql_select("SELECT id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1 and company_name=$cbo_company_id order by sewing_line_serial"); 
	foreach($lineDataArr as $lRow)
	{
		$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
		$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
		$lastSlNo=$lRow[csf('sewing_line_serial')];
	}
	
	
	if($db_type==0)
	{
		$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$company_id and shift_id=1 and pr_date between $txt_date_from and $txt_date_to and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");	
	}
	else
	{
		$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$company_id and shift_id=1 and pr_date between $txt_date_from and $txt_date_to and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
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
	$actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$txt_date_from)));
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

	/*===================================================================================== /
	/								get actual resource variable							/
	/===================================================================================== */
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_id and variable_list=23 and is_deleted=0 
	and status_active=1");

	/*===================================================================================== /
	/										query condition									/
	/===================================================================================== */
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and b.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else  $buyer_id_cond="";
		}
		else
		{
		$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and b.buyer_name=$cbo_buyer_name";
	}
	
	//if(str_replace("'","",$cbo_company_id)==0) $company_name=""; else $company_name="and a.company_id=".str_replace("'","",$cbo_company_id)."";
	if(str_replace("'","",$cbo_company_id)==0) $company_name=""; else $company_name="and a.serving_company=".str_replace("'","",$cbo_company_id)."";
	
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
	$date_cond="";
	if(str_replace("'","",trim($txt_date_from))!="")
	{
		$date_cond =" and a.production_date between $txt_date_from AND $txt_date_to";
	}

	/*===================================================================================== /
	/										prod resource data								/
	/===================================================================================== */	
	// echo $prod_reso_allo[0]; die;
	$prod_res_cond = "";
	$prod_res_cond .= (str_replace("'", "", $cbo_location_id)==0) ? "" : " and a.location_id=$cbo_location_id";
	
	if($prod_reso_allo[0]==1)
	{
		$prod_resource_array=array();
		$prod_resource_array2=array();

		$dataArray_sql=sql_select("SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id=$company_id and b.pr_date between $txt_date_from and $txt_date_to and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $prod_res_cond");
		
		foreach($dataArray_sql as $val)
		{
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['man_power']+=$val[csf('man_power')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['operator']+=$val[csf('operator')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['helper']+=$val[csf('helper')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['terget_hour']+=$val[csf('target_per_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['working_hour']+=$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['tpd']+=$val[csf('target_per_hour')]*$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_start']=$val[csf('from_date')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_end']=$val[csf('to_date')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['capacity']+=$val[csf('capacity')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust']=$val[csf('smv_adjust')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];		
		}
		// echo "<pre>";print_r($prod_resource_array);die();

		// =======================================================
		$sql="SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, c.from_date,c.to_date,c.capacity,d.po_id,d.target_per_line,d.operator, d.helper,d.working_hour,d.actual_smv from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c,prod_resource_color_size d where a.id=c.mst_id and c.id=b.mast_dtl_id and d.mst_id=a.id and d.dtls_id=c.id and a.company_id=$company_id and b.pr_date between $txt_date_from and $txt_date_to and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $prod_res_cond";
		// echo $sql;die();
		$sql_res=sql_select($sql);
		$poIds_arr = array();
		foreach($sql_res as $vals)
		{
			$poIds_arr[$vals[csf('po_id')]] = $vals[csf('po_id')];
		}
		$poIds = implode(",", $poIds_arr);
		$style_arr = return_library_array("SELECT a.style_ref_no,b.id from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and a.status_active=1 and b.status_active=1 and b.id in($poIds)","id","style_ref_no");
		foreach($sql_res as $val)
		{
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['man_power']+=$val[csf('operator')]+$val[csf('helper')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['operator']+=$val[csf('operator')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['helper']+=$val[csf('helper')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['terget_hour']+=$val[csf('target_per_line')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['working_hour']+=$val[csf('working_hour')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['tpd']+=$val[csf('target_per_line')]*$val[csf('working_hour')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['day_start']=$val[csf('from_date')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['day_end']=$val[csf('to_date')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['capacity']=$val[csf('capacity')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['smv_adjust']=$val[csf('smv_adjust')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];
		}

		// echo "<pre>"; print_r($prod_resource_array);die();
		if(str_replace("'","",trim($txt_date_from))!=""){$pr_date_con=" and b.pr_date between $txt_date_from and $txt_date_to";}

		if($db_type==0)
		{
			$dataArray=sql_select("SELECT a.id,b.pr_date,d.shift_id,TIME_FORMAT( d.prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( d.lunch_start_time, '%H:%i' ) as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$company_id and shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 $pr_date_con"); 
		}
		else
		{
			$dataArray=sql_select("SELECT a.id,b.pr_date,d.shift_id,TO_CHAR(d.prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR( d.lunch_start_time,'HH24:MI') as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$company_id and shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 $pr_date_con");
		}
		
		$line_number_arr=array();
		foreach($dataArray as $val)
		{
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['shift_id']=$val[csf('shift_id')];
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['prod_start_time']=$val[csf('prod_start_time')];
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['lunch_start_time']=$val[csf('lunch_start_time')];
		}
		
		$sqlExtraHour="SELECT a.FLOOR_ID, b.MST_ID,b.TOTAL_SMV, b.PR_DATE FROM prod_resource_mst a, prod_resource_smv_adj b WHERE  a.id = b.mst_id AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0 AND b.ADJUSTMENT_SOURCE = 1 $pr_date_con";
		$sqlExtraHourResultArr=sql_select($sqlExtraHour);
		$extra_minute_production_arr=array();
		$extra_minute_resource_arr=array();
		foreach($sqlExtraHourResultArr as $ex_row)
		{
			$extra_minute_production_arr[$ex_row[FLOOR_ID]][$ex_row[MST_ID]]+=$ex_row[TOTAL_SMV];
			$extra_minute_resource_arr[$ex_row[MST_ID]][$ex_row[PR_DATE]]+=$ex_row[TOTAL_SMV];
		}

		/*===============================================================================/
		/							Actual resource SMV data							 /
		/============================================================================== */
		$prod_resource_smv_adj_array = array();
		$sql_query="SELECT b.mst_id, b.pr_date,b.number_of_emp ,b.adjust_hour,b.total_smv,b.adjustment_source from prod_resource_mst a,prod_resource_smv_adj b  where a.id=b.mst_id  and a.company_id=$company_id and b.pr_date between $txt_date_from and $txt_date_to and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 and b.status_active=1  $prod_res_cond";
		// echo $sql_query;
		$sql_query_res=sql_select($sql_query);
		foreach($sql_query_res as $val)
		{
			$val[csf('pr_date')]=date("d-M-Y",strtotime($val[csf('pr_date')]));
			
			$prod_resource_smv_adj_array[$val[csf('mst_id')]][$val[csf('pr_date')]][$val[csf('adjustment_source')]]['number_of_emp']+=$val[csf('number_of_emp')];
			$prod_resource_smv_adj_array[$val[csf('mst_id')]][$val[csf('pr_date')]][$val[csf('adjustment_source')]]['adjust_hour']+=$val[csf('adjust_hour')];
			$prod_resource_smv_adj_array[$val[csf('mst_id')]][$val[csf('pr_date')]][$val[csf('adjustment_source')]]['total_smv']+=$val[csf('total_smv')];
			
		}
		
		// echo "<pre>";print_r($prod_resource_smv_adj_array);die();
	
		
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
	$prod_start_time=sql_select("select $prod_start_cond as prod_start_time from variable_settings_production where company_name=$cbo_company_id and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");
	//echo "select company_name, prod_start_time from variable_settings_production where company_name=$cbo_company_id and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1";
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
	
	$search_prod_date=change_date_format(str_replace("'","",$txt_date_from));
	$current_eff_min=($ex_time[0]*60)+$ex_time[1];
	//echo $current_date.'='.$search_prod_date;
	$variable_time= explode(":",$variable_start_time_arr);
	$vari_min=($variable_time[0]*60)+$variable_time[1];
	$difa_time=explode(".",number_format(($current_eff_min-$vari_min)/60,2));//datediff("",$ctime,$variable_start_time_arr);
	$dif_time=number_format($datediff/60,2);
	$dif_hour_min=date("H", strtotime($dif_time));
	/*===================================================================================== /
	/										smv sorce 										/
	/===================================================================================== */
   	$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($manufacturing_company) and variable_list=25 and   status_active=1 and is_deleted=0");
	// echo $smv_source;
    if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
	
   
	if($db_type==2)
	{
		$pr_date=str_replace("'","",$txt_date_from);
		$pr_date_old=explode("-",str_replace("'","",$txt_date_from));
		$month=strtoupper($pr_date_old[1]);
		$year=substr($pr_date_old[2],2);
		$pr_date=$pr_date_old[0]."-".$month."-".$year;
	}
	if($db_type==0)
	{
		$pr_date=str_replace("'","",$txt_date_from);
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
		$sql.="sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23 from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a left join lib_prod_floor d on a.floor_id=d.id and d.is_deleted=0 left join lib_sewing_line e on a.sewing_line=e.id and e.is_deleted=0  where a.production_type=5 and a.po_break_down_id=c.id and c.job_id=b.id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $company_name $location $floor $line $buyer_id_cond  $date_cond group by b.job_no,a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,e.sewing_line_serial,b.buyer_name,a.item_number_id,c.po_number,c.file_no,c.unit_price,c.grouping,b.style_ref_no,d.color_type_id,a.remarks order by a.production_date";
	}
	else if($db_type==2)
	{
		$sql="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name  as buyer_name, b.style_ref_no, b.job_no, a.po_break_down_id, a.item_number_id, c.po_number as po_number, c.file_no, c.unit_price, c.grouping as ref, d.color_type_id, a.remarks, sum(CASE WHEN a.production_type=5 THEN production_qnty else 0 END) as good_qnty,";
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
		$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN production_qnty else 0 END) AS prod_hour23, sum(CASE WHEN a.production_type=8 THEN production_qnty else 0 END) AS finish_qty
		FROM  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a ,pro_garments_production_dtls d
		WHERE a.production_type in (5,8) and a.po_break_down_id=c.id and c.job_id=b.id and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_name $location $floor $line $buyer_id_cond  $date_cond 
		GROUP BY b.job_no, a.company_id, a.location, a.floor_id, a.po_break_down_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name, b.style_ref_no, a.item_number_id, c.po_number, c.unit_price, c.file_no, c.grouping, d.color_type_id, a.remarks
		ORDER BY a.production_date";
	}
	// echo $sql;die;
	$sql_resqlt=sql_select($sql);
	$production_data_arr=array(); $style_chane_arr=array(); $production_po_data_arr=array(); $production_serial_arr=array(); $reso_line_ids=''; $all_po_id=""; $active_days_arr=array(); $duplicate_date_arr=array(); $poIdArr=array(); $jobArr=array(); $prod_line_array = array(); $line_style_chk_array = array(); $date_wise_line_chk_array = array();
	foreach($sql_resqlt as $val)
	{	
		$prod_line_array[$val[csf('sewing_line')]] = $val[csf('sewing_line')];
		$poIdArr[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
		if($val[csf('sewing_line')]=="") $val[csf('sewing_line')]=0; 
		if($val[csf('prod_reso_allo')]==1)
		{
			$sewing_line_id=$prod_reso_arr[$val[csf('sewing_line')]];
			$reso_line_ids.=$val[csf('sewing_line')].',';
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
		$production_serial_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]=$val[csf('sewing_line')];
		$date_wise_line_chk_array[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]] = $val[csf('sewing_line')];
		
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
		
	 	for($h=$hour; $h<$last_hour; $h++)
		{
			$prod_hour="prod_hour".substr($start_hour_arr[$h],0,2)."";
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]][$prod_hour]+=$val[csf($prod_hour)]; 
			
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
			{
				if( $h>=$line_start_hour && $h<=$actual_time)
				{
					$production_po_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf($prod_hour)]; 
				} 	
			}
			
			if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date)) 
			{	
				$production_po_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf($prod_hour)];
			}
		}
		
		if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
		{	
			if( $h>=$line_start_hour && $h<=$actual_time)
			{
				$production_po_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf('prod_hour23')];     
			} 	
		}
		else
		{
			$production_po_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf('prod_hour23')];     
		}
		
	 	$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['prod_hour23']+=$val[csf('prod_hour23')];  
		$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['prod_reso_allo']=$val[csf('prod_reso_allo')]; 
		
	 	if($production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['buyer_name']!="")
		{
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['buyer_name'].=",".$val[csf('buyer_name')]; 
		}
	 	else
		{
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['buyer_name']=$val[csf('buyer_name')]; 
		}

		if($line_style_chk_array[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]] =="")
		{
			$line_wise_style_count_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]++;
			$line_wise_style_count_arr2[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]++;
			$line_style_chk_array[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]] = $val[csf('style_ref_no')];
		}

		/*if($line_wise_style_count_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]!="")
		{
			$line_wise_style_count_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]].=",".$val[csf('buyer_name')]; 
		}
	 	else
		{
			$line_wise_style_count_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]=$val[csf('buyer_name')]; 
		}*/

	
	 	if($production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['po_number']!="")
		{
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['po_number'].=",".$val[csf('po_number')];
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['job_no'].=",".$val[csf('job_no')];
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['po_id'].=",".$val[csf('po_break_down_id')];
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['style'].="##".$val[csf('style_ref_no')];
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['file'].=",".$val[csf('file_no')];
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['ref'].=",".$val[csf('ref')]; 
		}
	 	else
		{
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['po_number']=$val[csf('po_number')];
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['job_no']=$val[csf('job_no')];
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['po_id']=$val[csf('po_break_down_id')]; 
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['style']=$val[csf('style_ref_no')]; 
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['file']=$val[csf('file_no')]; 
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['ref']=$val[csf('ref')]; 
		}

		if($style_chane_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['style']!="")
		{
			$style_chane_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['style'].="##".$val[csf('style_ref_no')];
		}
	 	else
		{
			$style_chane_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['style']=$val[csf('style_ref_no')];
		}

		
		if ($val[csf('remarks')] !="") 
		{
		 	if($production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['remarks']!="")
			{
				$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['remarks'].=",".$val[csf('remarks')]; 
			}
		 	else
			{
				$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['remarks']=$val[csf('remarks')]; 
			}
		}

		if($po_rate_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['rate']!="")
		{
			$po_rate_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['rate'].=",".$val[csf('unit_price')]; 
		}
		else
		{
			$po_rate_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['rate'] = $val[csf('unit_price')];
		}
		
		if($production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['item_number_id']!="")
		{
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('unit_price')]; 
		}
		else
		{
			 $production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('unit_price')]; 
		}
		$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['quantity']+=$val[csf('good_qnty')];
		$production_data_arr_qty[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]['quantity']+=$val[csf('good_qnty')];
		$production_data_arr_qty[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]['finishqty']+=$val[csf('finish_qty')];
		
		if($all_po_id=="") $all_po_id=$val[csf('po_break_down_id')]; else $all_po_id.=",".$val[csf('po_break_down_id')];
		$jobArr[$val[csf('job_no')]] = $val[csf('job_no')];
	}



	// $production_serial_arr[$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]=$val[csf('sewing_line')];
	// $production_serial_arr[5000][1][8000][0]=8000;




	// echo "<pre>"; print_r($production_po_data_arr);die();
	/*===================================================================================== /
	/										po item wise smv 								/
	/===================================================================================== */
	$po_id_cond = where_con_using_array($poIdArr,0,"b.id");

	$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_id=a.id and b.job_id=c.job_id $po_id_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3)";
	// echo $smv_source."===".$sql_item;die;
	$resultItem=sql_select($sql_item);
	foreach($resultItem as $itemData)
	{
		if($smv_source==1)
		{
			$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
		}
		else if($smv_source==2)
		{
			$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs_precost')];
		}
	}
	// print_r($item_smv_array);die();
	/*===================================================================================== /
	/								color tipe wise smv 									/
	/===================================================================================== */
	/*$sql_item="SELECT max(a.id) as mst_id,a.total_smv,a.style_ref, a.gmts_item_id,c.id,a.color_type from ppl_gsd_entry_mst a,wo_po_details_master b, wo_po_break_down c where a.style_ref=b.style_ref_no and a.bulletin_type=4 and  TRUNC(a.insert_date)<=TO_DATE($txt_date) and a.is_deleted=0 and a.status_active=1 and b.job_no=c.job_no_mst and c.id in($all_po_id) and  b.status_active=1 and b.is_deleted=0 
		group by a.total_smv,a.style_ref, a.gmts_item_id,c.id,a.color_type ";
	//echo $sql_item;die;
	$resultItem=sql_select($sql_item);
	foreach($resultItem as $itemData)
	{
		$item_smv_array_color_type[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]][$itemData[csf('color_type')]]=$itemData[csf('total_smv')];
	}

	foreach($sql_resqlt as $val2)
	{
		//echo $val2[csf('po_break_down_id')]."**".$val2[csf('item_number_id')]."**".$val2[csf('color_type_id')]."<br/>";
		if($item_smv_array_color_type[$val2[csf('po_break_down_id')]][$val2[csf('item_number_id')]][$val2[csf('color_type_id')]]!="")
		$item_smv_array[$val2[csf('po_break_down_id')]][$val2[csf('item_number_id')]]=$item_smv_array_color_type[$val2[csf('po_break_down_id')]][$val2[csf('item_number_id')]][$val2[csf('color_type_id')]];
	}*/

	// print_r($item_smv_array);die();
	$po_ids=count(array_unique(explode(",",$all_po_id)));
	$po_numIds=chop($all_po_id,',');
	$poIds_cond="";
	$poIds_cond2="";
	if($all_po_id!='' || $all_po_id!=0)
	{
		if($db_type==2 && $po_ids>1000)
		{
			$poIds_cond=" and (";
			$poIds_cond2=" and (";
			$poIds_cond3=" and (";
			$poIdsArr=array_chunk(explode(",",$po_numIds),990);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$poIds_cond.=" b.id  in($ids) or ";
				$poIds_cond2.=" c.id  in($ids) or ";
				$poIds_cond3.=" po_break_down_id  in($ids) or ";
			}
			$poIds_cond=chop($poIds_cond,'or ');
			$poIds_cond2=chop($poIds_cond2,'or ');
			$poIds_cond3=chop($poIds_cond3,'or ');
			$poIds_cond.=")";
			$poIds_cond2.=")";
			$poIds_cond3.=")";
		}
		else
		{
			$poIds_cond=" and  b.id  in($all_po_id)";
			$poIds_cond2=" and  c.id  in($all_po_id)";
			$poIds_cond3=" and  po_break_down_id  in($all_po_id)";
		}
	}
	/*===================================================================================== /
	/										po active days									/
	/===================================================================================== */
    $po_active_sql="SELECT a.floor_id,a.sewing_line,a.production_date,a.po_break_down_id,a.item_number_id from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a where a.production_type=5 and a.po_break_down_id=c.id and c.job_id=b.id and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $company_name $location $floor $line $buyer_id_cond  $poIds_cond2 group by  a.floor_id,a.sewing_line,a.production_date ,a.po_break_down_id,a.item_number_id";
    //echo $po_active_sql;die;
	foreach(sql_select($po_active_sql) as $vals)
	{
		$prod_dates=$vals[csf('production_date')];
		if($duplicate_date_arr[$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]][$prod_dates]=="")
		{
			$active_days_arr[$vals[csf('floor_id')]][$vals[csf('sewing_line')]]++;
			$active_days_arr_powise[$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]]+=1;
			$duplicate_date_arr[$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]][$prod_dates]=$prod_dates;
		}
	}
	// echo "<pre>"; print_r($active_days_arr);
	/*===================================================================================== /
	/								item wise order qty and value							/
	/===================================================================================== */
	$sql_item_rate="SELECT b.id, c.item_number_id, c.order_quantity, c.order_total from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c where b.job_id=a.id and b.id=c.po_break_down_id and b.job_id=c.job_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1  $file_cond $ref_cond  $poIds_cond";
	$resultRate=sql_select($sql_item_rate);
	$item_po_array=array();
	foreach($resultRate as $row)
	{
		$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['qty']+=$row[csf('order_quantity')];
		$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['amt']+=$row[csf('order_total')];
	}
	
	/*===================================================================================== /
	/										subcoutact data									/
	/===================================================================================== */
    if($db_type==0)
    {
		$sql_sub_contuct= "SELECT  a.company_id, a.location_id,d.floor_serial_no,e.sewing_line_serial,a.prod_reso_allo, a.floor_id, a.production_date, a.line_id,b.party_id  as buyer_name,a.order_id,c.order_no as po_number,c.cust_style_ref,b.subcon_job as job_no, max(c.smv) as smv,sum(a.production_qnty) as good_qnty,"; 
		
		$first=1;
		for($h=$hour;$h<$last_hour;$h++)
		{
			$bg=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="prod_hour".substr($bg,0,2);
			if($first==1)
			{
				$sql_sub_contuct.="sum(CASE WHEN  a.hour<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,";
			}
			else
			{
				$sql_sub_contuct.="sum(CASE WHEN a.hour>'$bg' and a.hour<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,";	
			}
			$first=$first+1;
   		}
   		$sql_sub_contuct.="sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.id and d.is_deleted=0 left join lib_sewing_line e on a.line_id=e.id and e.is_deleted=0 where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id=$company_id $subcon_location $floor $subcon_line   $date_cond group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date,a.prod_reso_allo,a.line_id,b.party_id,c.order_no,c.cust_style_ref,b.subcon_job ,e.sewing_line_serial order by a.location_id,d.floor_serial_no,e.sewing_line_serial,a.prod_reso_allo";
	}
	else
	{
		$sql_sub_contuct= "SELECT a.company_id, a.location_id, a.floor_id,d.floor_serial_no,e.sewing_line_serial, a.production_date, a.line_id,b.party_id  as buyer_name,a.prod_reso_allo,a.order_id,c.order_no as po_number,c.cust_style_ref, b.subcon_job as job_no,  max(c.smv) as smv,a.prod_reso_allo,sum(a.production_qnty) as good_qnty,"; 
		$first=1;
		for($h=$hour;$h<$last_hour;$h++)
		{
			$bg=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="prod_hour".substr($bg,0,2);
			if($first==1)
			{
				$sql_sub_contuct.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,";
			}
			else
			{
				$sql_sub_contuct.="sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')>'$bg' and TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,";	
			}
			$first++;
		}
		
	   	$sql_sub_contuct.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]'	and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.id  and d.is_deleted=0 left join lib_sewing_line e on a.line_id=e.id and e.is_deleted=0 where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id=$company_id $subcon_location $floor $subcon_line   $date_cond group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date,  b.subcon_job,a.line_id,b.party_id,c.order_no,c.cust_style_ref,e.sewing_line_serial,a.prod_reso_allo order by a.location_id, a.floor_id,e.sewing_line_serial,a.prod_reso_allo";
	}
	// echo $sql_sub_contuct;die;
	$sub_result=sql_select($sql_sub_contuct);
	$subcon_order_smv=array();		
	foreach($sub_result as $subcon_val)
	{
		$prod_line_array[$subcon_val[csf('sewing_line')]] = $subcon_val[csf('sewing_line')];
		if($subcon_val[csf('prod_reso_allo')]==1)
		{
			$sewing_line_id=$prod_reso_arr[$subcon_val[csf('sewing_line')]];
		}
		else
		{
			$sewing_line_id=$subcon_val[csf('sewing_line')];
		}
		
		if($lineSerialArr[$sewing_line_id]=="")
		{
			$lastSlNo++;
			$slNo=$lastSlNo;
			$lineSerialArr[$sewing_line_id]=$slNo;
		}
		else $slNo=$lineSerialArr[$sewing_line_id];
		//$production_serial_arr[$subcon_val[csf('floor_id')]][$slNo][$subcon_val[csf('sewing_line')]]=$subcon_val[csf('sewing_line')];
		
		$production_po_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$subcon_val[csf('good_qnty')];
		if($production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['buyer_name']!="")
		{
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['buyer_name'].=",".$subcon_val[csf('buyer_name')]; 
		}
		else
		{
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['buyer_name']=$subcon_val[csf('buyer_name')]; 
		}
		if($line_style_chk_array[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('sewing_line')]][$subcon_val[csf('style_ref_no')]] =="")
		{
			$line_wise_style_count_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('sewing_line')]]++;
			$line_wise_style_count_arr2[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('sewing_line')]]++;
			$line_style_chk_array[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('sewing_line')]][$subcon_val[csf('style_ref_no')]] = $subcon_val[csf('style_ref_no')];
		}
	
		if($production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['po_number']!="")
		{
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['po_number'].=",".$subcon_val[csf('po_number')];
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['job_no'].=",".$subcon_val[csf('job_no')];
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['style'].="##".$subcon_val[csf('cust_style_ref')];  
		}
		else
		{
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['po_number']=$subcon_val[csf('po_number')]; 
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['job_no']=$subcon_val[csf('job_no')]; 
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['style']=$subcon_val[csf('cust_style_ref')]; 
		}
	
		if($production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['order_id']!="")
		{
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['order_id'].=",".$subcon_val[csf('order_id')]; 
		}
		else
		{
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['order_id'].=$subcon_val[csf('order_id')]; 
		}
		$subcon_order_smv[$subcon_val[csf('order_id')]]=$subcon_val[csf('smv')];
		$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['quantity']+=$subcon_val[csf('good_qnty')];
		
	 	$line_start=$line_number_arr[$val[csf('line_id')]][$val[csf('production_date')]]['prod_start_time']	;
	 	if($line_start!="") 
	 	{ 
			$line_start_hour=substr($line_start,0,2); 
			if(substr($line_start_hour,0,1)==0)  $line_start_hour=substr($line_start_hour,1,1);	
	 	}
		else
	 	{
			$line_start_hour=$hour; 
	 	}
		for($h=$hour;$h<=$last_hour;$h++)
		{
			$prod_hour="prod_hour".substr($start_hour_arr[$h],0,2).""; 
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]][$prod_hour]+=$subcon_val[csf($prod_hour)]; 
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
			{
				 if( $h>=$line_start_hour && $h<=$actual_time)
				 {
				 $production_po_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf($prod_hour)];	                 } 
			}
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
			{
				$production_po_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf($prod_hour)];	            }
		 }
		if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
		{	
			if( $h>=$line_start_hour && $h<=$actual_time)
			{
				$production_po_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf('prod_hour23')];
			} 	
		}
		else
		{
			$production_po_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf('prod_hour23')];
		}
		$production_data_arr[$subcon_val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['prod_hour23']+=$val[csf('prod_hour23')];
	}

	/*===================================================================================== /
	/							prod resource data no prod line								/
	/===================================================================================== */	
	// echo $prod_reso_allo[0]; die;

	$prod_line_ids = implode(",", array_filter($prod_line_array));
	
	if($prod_reso_allo[0]==1)
	{
		$dataArray_sql=sql_select("SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id=$company_id and b.pr_date between $txt_date_from and $txt_date_to and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $prod_res_cond ");//and a.id not in($prod_line_ids) 
		
		foreach($dataArray_sql as $val)
		{
			$sewing_line_id=$prod_reso_arr[$val[csf('id')]];			
			
			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id]=$slNo;
			}
			else $slNo=$lineSerialArr[$sewing_line_id];
			if($date_wise_line_chk_array[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]]=="")
			{
				$production_serial_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$slNo][$val[csf('id')]][]=$val[csf('id')];			
				$production_serial_arr2[$val[csf('pr_date')]][$val[csf('floor_id')]][$slNo][$val[csf('id')]]=$val[csf('id')];
			}
						
		}		
	}
	
	/*===================================================================================== /
	/							For Summary Report New Add No Prodcut						/
	/===================================================================================== */
	if($cbo_no_prod_type==1)
	{
		//No Production line Start ....
		$prod_reso_arr=return_library_array( "SELECT id, line_number from prod_resource_mst",'id','line_number');
		$sql_active_line=sql_select("SELECT sewing_line,sum(production_quantity) as total_production from pro_garments_production_mst  where production_date between $txt_date_from and $txt_date_to and production_type=5 and status_active=1 and is_deleted=0 and prod_reso_allo=1 group by  sewing_line");
		//$actual_line_arr=array();
		foreach($sql_active_line as $inf)
		{	
		   if(str_replace("","",$inf[csf('sewing_line')])!="")
		   {
				//if(str_replace("","",$actual_line_arr)!="") $actual_line_arr.=",";
			    //$actual_line_arr.="'".$inf[csf('sewing_line')]."'";
		   }
		}
		//echo $actual_line_arr;die;
		$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and is_deleted=0 and status_active=1");
		$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
		if(str_replace("'","",$cbo_location_id)!=0) 
		{
			$location=" and a.location_id=".str_replace("'","",$cbo_location_id)."";
		}		
		
		$res_line_cond=rtrim($reso_line_ids,",");
		
		 $dataArray_sum=sql_select("SELECT a.id, a.floor_id,1 as prod_reso_allo,2 as type_line, a.line_number as line_no, b.man_power, b.operator, b.helper, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type,d.prod_start_time from  prod_resource_dtls b,prod_resource_dtls_time d,prod_resource_mst a  where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$company_id and b.pr_date=".$txt_date." and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 and a.id not in($res_line_cond)  $location   group by a.id, a.floor_id, a.line_number, d.prod_start_time,b.man_power, b.operator, b.helper, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type order by a.floor_id");
		 $no_prod_line_arr=array();
		 foreach( $dataArray_sum as $row)
		 { 			 
			$sewing_line_id=$row[csf('line_no')];
		
			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id]=$slNo;
			}
			else $slNo=$lineSerialArr[$sewing_line_id];
			
			// $production_serial_arr[$row[csf('floor_id')]][$slNo][$row[csf('id')]]=$row[csf('id')]; 
			$production_data_arr[$row[csf('floor_id')]][$row[csf('id')]]['type_line']=$row[csf('type_line')];
			$production_data_arr[$row[csf('floor_id')]][$row[csf('id')]]['prod_reso_allo']=$row[csf('prod_reso_allo')]; 
			$production_data_arr[$row[csf('floor_id')]][$row[csf('id')]]['man_power']=$row[csf('man_power')]; 
			$production_data_arr[$row[csf('floor_id')]][$row[csf('id')]]['operator']=$row[csf('operator')]; 
			$production_data_arr[$row[csf('floor_id')]][$row[csf('id')]]['helper']=$row[csf('helper')]; 
			$production_data_arr[$row[csf('floor_id')]][$row[csf('id')]]['working_hour']=$row[csf('working_hour')];						
			$production_data_arr[$row[csf('floor_id')]][$row[csf('id')]]['terget_hour']=$row[csf('target_per_hour')];
			$production_data_arr[$row[csf('floor_id')]][$row[csf('id')]]['total_line_hour']=$row[csf('man_power')]*$row[csf('working_hour')]; 
			$production_data_arr[$row[csf('floor_id')]][$row[csf('id')]]['smv_adjust']=$row[csf('smv_adjust')]; 
			$production_data_arr[$row[csf('floor_id')]][$row[csf('id')]]['smv_adjust_type']=$row[csf('smv_adjust_type')]; 
			$production_data_arr[$row[csf('floor_id')]][$row[csf('id')]]['prod_start_time']=$row[csf('prod_start_time')];
		 }
		 $dataArray_sql_cap=sql_select("SELECT  a.floor_id, a.line_number as line_no,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id  and a.company_id=$company_id and b.pr_date between $txt_date_from and $txt_date_to  and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  group by a.id,a.line_number, a.floor_id, a.line_number, b.man_power, b.operator,c.capacity, b.helper, b.working_hour,b.target_per_hour order by a.floor_id");
		 
		 //$prod_resource_array_summary=array();
		 foreach( $dataArray_sql_cap as $row)
		 {
			 $production_data_arr[$row[csf('floor_id')]][$row[csf('line_no')]]['capacity']=$row[csf('capacity')]; 
		 }
	
	} //End
	
	//echo "<pre>";
	// echo "<pre>"; print_r($production_serial_arr);die;
	
	
	$allJobs = "'".implode("','", $jobArr)."'";
	$costing_per_arr = return_library_array("SELECT job_no, costing_per from wo_pre_cost_mst where status_active=1 and job_no in($allJobs)","job_no","costing_per");
	$tot_cm_arr = return_library_array("SELECT job_no, cm_cost from wo_pre_cost_dtls where status_active=1 and job_no in($allJobs)","job_no","cm_cost");

	// ============================== get npt min ===========================
	$date_cond_npt = str_replace("production_date", IDLE_DATE, $date_cond);
	$sql = "SELECT a.IDLE_DATE,b.MANPOWER,b.DURATION_HOUR,b.REMARKS from SEWING_LINE_IDLE_MST a, SEWING_LINE_IDLE_DTLS b where a.id=b.mst_id and a.company_id=$company_id $subcon_location $date_cond_npt";
	// echo $sql; die();
	$nptRes = sql_select($sql);
	$npt_date_data_array = array();
	$npt_month_data_array = array();
	foreach ($nptRes as $val) 
	{
		$nptmin = $val['MANPOWER'] * $val['DURATION_HOUR'] * 60;
		// $npt_date_data_array[$val['IDLE_DATE']][$val['FLOOR_ID']][$val['PROD_RESOURCE_ID']] += $nptmin;
		$npt_date_data_array[$val['IDLE_DATE']]['idle_min'] += $nptmin;
		$npt_date_data_array[$val['IDLE_DATE']]['remarks'] .= $val['REMARKS']."**";
		$npt_month_data_array[date('M-Y',strtotime($val['IDLE_DATE']))]['idle_min'] += $nptmin;
		$npt_month_data_array[date('M-Y',strtotime($val['IDLE_DATE']))]['remarks'] .= $val['REMARKS']."**";
	}
	// echo "<pre>"; print_r($npt_month_data_array);die;
	
	/*$condition= new condition();
	if($cbo_company_name>0){
		$condition->company_name("=$company_id");
	}
	if(count($poIdArr)>0)
	{
		$condition->po_id_in(implode(',',$poIdArr));
	}
	$condition->init();
	$other= new other($condition);
	$other_cost = $other->getAmountArray_by_job();*/
	// $other_cost[$jobNumber]['cm_cost'];
	// echo "<pre>"; print_r($production_data_arr);die();
	$rowspan = array();
	foreach($production_serial_arr as $f_id=>$fname)
	{
		ksort($fname);
		foreach($fname as $sl=>$s_data)
		{			
			foreach($s_data as $l_id=>$ldata)
			{
				foreach ($ldata as $style_key => $style_data) 
				{
					$rowspan[$f_id][$sl][$l_id]++;
				}
			}
		}
	}
	// ======================================
    $avable_min=0;
	$today_product=0;
    $floor_name=""; 
	$j=1;
	$i=1;
	ob_start();
	$line_number_check_arr=array();
	$smv_for_item="";
	$total_production=array();
	$floor_production=array();
    $line_floor_production=0;
    $line_total_production=0; 
    $gnd_total_fob_val=0; 
    $gnd_final_total_fob_val=0;
    $f_chk_arr = array();
    $line_chk_arr = array();
    $line_chk_arr2 = array();
    $line_chk_arr3 = array();
    $date_data_array = array();
    $month_data_array = array();
    foreach($production_serial_arr as $pr_date=>$date_data)
    {
		foreach($date_data as $f_id=>$fname)
		{
			ksort($fname);
			foreach($fname as $sl=>$s_data)
			{			
				foreach($s_data as $l_id=>$ldata)
				{
					$l=0;
					$pp = 0;
					foreach ($ldata as $style_key => $style_data) 
					{
					  	$po_value=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['po_number'];
					 	//  if($po_value)
						// {
							$floor_row++;
							//$item_ids=$production_data_arr[$f_id][$l_id]['item_number_id'];
							$germents_item=array_unique(explode('****',$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['item_number_id']));
							// print_r($germents_item);die();
						
							$buyer_neme_all=array_unique(explode(',',$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['buyer_name']));
							$buyer_name="";
							foreach($buyer_neme_all as $buy)
							{
								if($buyer_name!='') $buyer_name.=',';
								$buyer_name.=$buyerArr[$buy];
							}
							$garment_itemname=''; $active_days=''; $item_smv=""; $item_ids=''; $smv_for_item=""; $produce_minit=""; $order_no_total=""; $efficiency_min=0; $tot_po_qty=0; $fob_val=0; $finishqty=$finishProduceMin=$finishingCm=$finishingFob=0;
							
							foreach($germents_item as $g_val)
							{
								$po_garment_item=explode('**',$g_val);
								if($garment_itemname!='') $garment_itemname.=',';
								$garment_itemname.=$garments_item[$po_garment_item[1]];
								if($item_ids=='') $item_ids=$po_garment_item[1];else $item_ids.=",".$po_garment_item[1];
								if($active_days=="")$active_days=$active_days_arr_powise[$po_garment_item[0]][$po_garment_item[1]];
								else $active_days.=','.$active_days_arr_powise[$po_garment_item[0]][$po_garment_item[1]];
								
								//echo $item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt'].'<br>';
								$tot_po_qty+=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];
								$tot_po_amt+=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt'];
								if($item_smv!='') $item_smv.='/';
								//echo $po_garment_item[0].'='.$po_garment_item[1];
								$item_smv.=$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
								if($order_no_total!="") $order_no_total.=",";
								$order_no_total.=$po_garment_item[0];
								if($smv_for_item!="") $smv_for_item.="****".$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
								else
								$smv_for_item=$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];	
								$produce_minit+=$production_po_data_arr[$pr_date][$f_id][$l_id][$po_garment_item[0]]*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
								
								$finishqty+=$production_data_arr_qty[$pr_date][$f_id][$l_id][$po_garment_item[0]][$po_garment_item[1]]['finishqty'];
								
								$finishProduceMin+=$production_data_arr_qty[$pr_date][$f_id][$l_id][$po_garment_item[0]][$po_garment_item[1]]['finishqty']*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
								// echo $production_po_data_arr[$pr_date][$f_id][$l_id][$po_garment_item[0]]."*".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]]."<br>";
								$fob_rate=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt']/$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];
								$prod_qty=$production_data_arr_qty[$f_id][$l_id][$po_garment_item[0]][$po_garment_item[1]]['quantity'];
								//echo $prod_qty.'<br>';
								if(is_nan($fob_rate)){ $fob_rate=0; }
								$fob_val+=$prod_qty*$fob_rate;
								
								$finishingFob+=$production_data_arr_qty[$pr_date][$f_id][$l_id][$po_garment_item[0]][$po_garment_item[1]]['finishqty']*$fob_rate;
							}
							
							$po_id_arr = array_unique(explode(",", $production_data_arr[$pr_date][$f_id][$l_id][$style_key]['po_id']));
							$po_rate ="";
							foreach ($po_id_arr as $po_val) 
							{
								if($po_rate!="") $po_rate.=",";
								$po_rate.=$po_rate_data_arr[$pr_date][$f_id][$l_id][$po_val]['rate'];
							}
							// echo $po_rate."<br>";


							$subcon_po_id=array_unique(explode(',',$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['order_id']));
							$subcon_order_id="";
							foreach($subcon_po_id as $sub_val)
							{
								$subcon_po_smv=explode(',',$sub_val); 
								if($sub_val!=0)
								{
								if($item_smv!='') $item_smv.='/';
								if($item_smv!='') $item_smv.='/';
								$item_smv.=$subcon_order_smv[$sub_val];
								}
								$produce_minit+=$production_po_data_arr[$pr_date][$f_id][$l_id][$sub_val]*$subcon_order_smv[$sub_val];
								if($subcon_order_id!="") $subcon_order_id.=",";
								$subcon_order_id.=$sub_val;
							}

							//echo $pr_date;die;
							$type_line=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['type_line'];
							$prod_reso_allo=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['prod_reso_allo'];
							$sewing_line='';
							if($production_data_arr[$pr_date][$f_id][$l_id][$style_key]['prod_reso_allo']!="")
							{
								if($production_data_arr[$pr_date][$f_id][$l_id][$style_key]['prod_reso_allo']==1)
								{
									$line_number=explode(",",$prod_reso_arr[$l_id]);
									foreach($line_number as $val)
									{
										// echo $l_id."<br>";
										if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
									}
								}
								else $sewing_line=$lineArr[$l_id];
							}
							else
							{
								// echo $l_id."kakku<br>";
								$line_number=explode(",",$prod_reso_arr[$l_id]);
								foreach($line_number as $val)
								{
									// echo $val."kakku<br>";
									if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
								}
								
							}
							// echo $sewing_line."==".$production_data_arr[$f_id][$l_id][$style_key]['prod_reso_allo']."=kakku<br>";
					 		// 	die();

							$lunch_start="";
							$lunch_start=$line_number_arr[$l_id][$pr_date]['lunch_start_time'];  
							$lunch_hour=$start_time_arr[$row[1]]['lst']; 
							if($lunch_start!="") 
							{ 
							$lunch_start_hour=$lunch_start; 
							}
							else
							{
							$lunch_start_hour=$lunch_hour; 
							}
						  	  
							$production_hour=array();
							$prod_hours = 0;
							for($h=$hour;$h<=$last_hour;$h++)
							{
								 $prod_hour="prod_hour".substr($line_start_hour_arr[$h],0,2).""; 
								 $production_hour[$prod_hour]=$production_data_arr[$pr_date][$f_id][$l_id][$style_key][$prod_hour];
								 $floor_production[$prod_hour]+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key][$prod_hour];
								 $total_production[$prod_hour]+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key][$prod_hour];
								 if($production_data_arr[$pr_date][$f_id][$l_id][$style_key][$prod_hour]>0)
								 {
								 	$prod_hours++;
								 }
							}				
							
			 				$floor_production['prod_hour24']+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['prod_hour23'];
							$total_production['prod_hour24']+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['prod_hour23'];
							$production_hour['prod_hour24']=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['prod_hour23']; 
							$line_production_hour=0;
							if(str_replace("'","",$actual_production_date)>str_replace("'","",$actual_date)) 
							{
								if($type_line==2) //No Profuction Line
								{
									$line_start=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['prod_start_time'];
								}
								else
								{
									$line_start=$line_number_arr[$l_id][$pr_date]['prod_start_time'];
								}
								if($line_start!="") 
								{ 
									$line_start_hour=substr($line_start,0,2); 
									if(substr($line_start_hour,0,1)==0)  $line_start_hour=substr($line_start_hour,1,1);	
								}
								else
								{
									$line_start_hour=$hour; 
								}
								$actual_time_hour=0;
								$total_eff_hour=0;
								for($lh=$line_start_hour;$lh<=$last_hour;$lh++)
								{
									$bg=$start_hour_arr[$lh];
									if($lh<$actual_time)
									{
									$total_eff_hour=$total_eff_hour+1;;	
									$line_hour="prod_hour".substr($bg,0,2)."";
									$line_production_hour+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key][$line_hour];
									$line_floor_production+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key][$line_hour];
									$line_total_production+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key][$line_hour];
									$actual_time_hour=$start_hour_arr[$lh+1];
									}
								}
			 					if($start_hour_arr[$actual_time]>$lunch_start_hour) $total_eff_hour=$total_eff_hour-1;
								
								if($type_line==2)
								{
									if($total_eff_hour>$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['working_hour'])
									{
										 $total_eff_hour=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['working_hour'];
									}
								}
								else
								{
									if($line_wise_style_count_arr[$pr_date][$f_id][$l_id]>1)
									{
										if($total_eff_hour>$prod_resource_array2[$l_id][$style_key][$pr_date]['working_hour'])
										{
											$total_eff_hour=$prod_resource_array2[$l_id][$style_key][$pr_date]['working_hour'];
										}
									}
									else
									{
										if($total_eff_hour>$prod_resource_array[$l_id][$pr_date]['working_hour'])
										{
											$total_eff_hour=$prod_resource_array[$l_id][$pr_date]['working_hour'];
										}
									}
								}
								
							}
							
							
							if(str_replace("'","",$actual_production_date)<=str_replace("'","",$actual_date)) 
							{
								for($ah=$hour;$ah<=$last_hour;$ah++)
								{
									$prod_hour="prod_hour".substr($start_hour_arr[$ah],0,2).""; 
									$line_production_hour+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key][$prod_hour];
									$line_floor_production+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key][$prod_hour];
									$line_total_production+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key][$prod_hour];
								}
								if($type_line==2)
								{
									$total_eff_hour=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['working_hour'];
								}
								else
								{
									if($line_wise_style_count_arr[$pr_date][$f_id][$l_id]>1)
									{
										$total_eff_hour=$prod_resource_array2[$l_id][$style_key][$pr_date]['working_hour'];	
									}
									else
									{
										$total_eff_hour=$prod_resource_array[$l_id][$pr_date]['working_hour'];
									}
								}
							}
							/*if($sewing_day!="")
							{
								$days_active= $active_days_arr[$f_id][$l_id];
								// $days_run=datediff("d",$sewing_day,$pr_date);
								$date1=date_create($sewing_day);
								$date2=date_create($pr_date);
								$diff=date_diff($date1,$date2);
								$days_run = $diff->format("%d");
							}
							else  
							{
								$days_run=0; 
								$days_active=0;
							}*/
							$days_run=0;
							$days_run= $active_days_arr[$f_id][$l_id];
							/*if($sewing_day!="")
							{
								// $days_run= $diff=datediff("d",$sewing_day,$pr_date);
								$days_active= $active_days_arr[$f_id][$l_id];
							}
							else 
							{
								 // $days_run=0;
								 $days_active=0;
							}*/ 
							 
							$current_wo_time=0;
							if($current_date==$search_prod_date)
							{
								$prod_wo_hour=$total_eff_hour;
								
								if ($dif_time<$prod_wo_hour)//
								{
									$current_wo_time=$dif_hour_min;
									$cla_cur_time=$dif_time;
								}
								else
								{
									$current_wo_time=$prod_wo_hour;
									$cla_cur_time=$prod_wo_hour;
								}
							}
							else
							{
								$current_wo_time=$total_eff_hour;
								$cla_cur_time=$total_eff_hour;
							}
							$total_adjustment=0;
							if($type_line==2) //No Production Line
							{
								$smv_adjustmet_type=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['smv_adjust_type'];
								$eff_target=($production_data_arr[$pr_date][$f_id][$l_id][$style_key]['terget_hour']*$total_eff_hour);

								if($total_eff_hour>=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['working_hour'])
								{
									if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['smv_adjust'];
									if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($production_data_arr[$pr_date][$f_id][$l_id][$style_key]['smv_adjust'])*(-1);
								}
								$efficiency_min+=$total_adjustment+($production_data_arr[$pr_date][$f_id][$l_id][$style_key]['man_power'])*$cla_cur_time*60;
								$extra_minute_production_arr=$efficiency_min+$extra_minute_arr[$f_id][$l_id];
								
								$line_efficiency=(($produce_minit)*100)/$efficiency_min;
								
								
							}
							else
							{
								if($line_wise_style_count_arr[$pr_date][$f_id][$l_id]>1)
								{
									$smv_adjustmet_type=$prod_resource_array2[$l_id][$style_key][$pr_date]['smv_adjust_type'];
									$eff_target=($prod_resource_array2[$l_id][$style_key][$pr_date]['terget_hour']*$total_eff_hour);
									// echo $l_id."=(".$prod_resource_array2[$l_id][$pr_date]['terget_hour']."*".$total_eff_hour.")<br>";
									if($total_eff_hour>=$prod_resource_array2[$l_id][$style_key][$pr_date]['working_hour'])
									{
										if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array2[$l_id][$style_key][$pr_date]['smv_adjust'];
										if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array2[$l_id][$style_key][$pr_date]['smv_adjust'])*(-1);
									}						
									
									// $efficiency_min+=$total_adjustment+($prod_resource_array2[$l_id][$style_key][$pr_date]['man_power'])*$cla_cur_time*60;
									$efficiency_min+=($prod_resource_array2[$l_id][$style_key][$pr_date]['man_power'])*$cla_cur_time*60;
									// echo "$style_key=$pr_date=".$prod_resource_array2[$l_id][$style_key][$pr_date]['man_power']."*".$cla_cur_time."*60<br>";
									$extra_minute_resource_arr=$efficiency_min+$extra_minute_arr[$l_id][$pr_date];
									
									$line_efficiency=(($produce_minit)*100)/$efficiency_min;
								}
								else
								{
									$smv_adjustmet_type=$prod_resource_array[$l_id][$pr_date]['smv_adjust_type'];
									$eff_target=($prod_resource_array[$l_id][$pr_date]['terget_hour']*$total_eff_hour);
									// echo $l_id."=(".$prod_resource_array[$l_id][$pr_date]['terget_hour']."*".$total_eff_hour.")<br>";
									if($total_eff_hour>=$prod_resource_array[$l_id][$pr_date]['working_hour'])
									{
										if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array[$l_id][$pr_date]['smv_adjust'];
										if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array[$l_id][$pr_date]['smv_adjust'])*(-1);
									}						
									
									// $efficiency_min+=$total_adjustment+($prod_resource_array[$l_id][$pr_date]['man_power'])*$cla_cur_time*60;
									$efficiency_min+=($prod_resource_array[$l_id][$pr_date]['man_power'])*$cla_cur_time*60;
									// echo "string".$total_adjustment."+(".$prod_resource_array[$l_id][$pr_date]['man_power'].")*".$cla_cur_time."*60<br>";
									$extra_minute_resource_arr=$efficiency_min+$extra_minute_arr[$l_id][$pr_date];
									
									$line_efficiency=(($produce_minit)*100)/$efficiency_min;
								}
							
								
							}

							// adjustment extra hour when multiple style running in a single line =========================
							$txtDate = date('d-M-Y',strtotime($pr_date));

							// $extra_hr = $prod_resource_smv_adj_array[$l_id][$pr_date][1]['adjust_hour']."<br>";
							// echo $pr_date."==".$line_wise_style_count_arr2[$pr_date][$f_id][$l_id]."<br>";
							if($line_wise_style_count_arr[$pr_date][$f_id][$l_id]>1)
							{
								$mn_power = $prod_resource_smv_adj_array[$l_id][$txtDate][1]['number_of_emp'];
								if($line_wise_style_count_arr2[$pr_date][$f_id][$l_id]>1)
								{
									$late_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][5]['total_smv'];

									
									if($pp==0)
									{
										$efficiency_min -= $late_hr;
										$pp++;
									}
									$line_wise_style_count_arr2[$pr_date][$f_id][$l_id]--;
								}
								else
								{
									
									$extra_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][1]['total_smv'];
									$lunch_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][2]['total_smv'];
									$sick_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][3]['total_smv'];
									$leave_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][4]['total_smv'];
									$adjust_hr = $extra_hr - ($lunch_hr+$sick_hr+$leave_hr);

									$efficiency_min += $adjust_hr;
									
								}

							}
							else // for single line
							{
								$extra_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][1]['total_smv'];
								$lunch_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][2]['total_smv'];
								$sick_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][3]['total_smv'];
								$leave_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][4]['total_smv'];
								$adjust_hr = $extra_hr - ($lunch_hr+$sick_hr+$leave_hr);

								$efficiency_min += $adjust_hr;
								// echo $efficiency_min."=".$l_id."=".$style_key."=".$txtDate."=".$extra_hr."- (".$lunch_hr."+".$sick_hr."+".$leave_hr.")<br>";
							}
							
							$po_id=rtrim($production_data_arr[$pr_date][$f_id][$l_id][$style_key]['po_id'],',');
							$po_id=array_unique(explode(",",$po_id));

							$style=rtrim($style_chane_arr[$pr_date][$f_id][$l_id]['style']);
							$style=implode("##",array_unique(explode("##",$style)));

							$job_arr = array_unique(explode(",", rtrim($production_data_arr[$pr_date][$f_id][$l_id][$style_key]['job_no'])));
							$job__no = '';
							foreach ($job_arr as $jobvalue) 
							{
								$job__no .= ($job__no=="") ? $jobvalue : ",".$jobvalue;
							}
							
							$cbo_get_upto=str_replace("'","",$cbo_get_upto);
							$txt_parcentage=str_replace("'","",$txt_parcentage);
						  
							$floor_name=$floorArr[$f_id];	
							$floor_smv+=$item_smv;
							
							$floor_days_run+=$days_run;
							$total_days_run+=$days_run;
							$floor_line_days_run+=$line_wise_days_run[$f_id][$l_id];
							$floor_days_active+=$days_active;				
							
							$po_id=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['po_id'];//$item_ids//$subcon_order_id
							$styles=explode("##",$style);
							$style_button='';//
							$style_name ='';
							$style_change = 0;
							$kk=0;
							foreach($styles as $sid)
							{
								if($kk!=0)
								{
									if($line_chk_array[$pr_date][$f_id][$l_id]=="")
									{
										$style_change++; // first style will 0, 2nd style will 1
										$line_chk_array[$pr_date][$f_id][$l_id]="kakku"	;							
									}
								}
								$kk++;
							}
							$as_on_current_hour_target=0; $as_on_current_hour_variance=0;
							$as_on_current_hour_target=$terget_hour*$cla_cur_time;
							$as_on_current_hour_variance=$line_production_hour-$as_on_current_hour_target;
							
							$line_acv = ($eff_target>0) ? (($line_production_hour/$eff_target)*100) : 0; 
							$target_gap = $line_production_hour - $eff_target; 
							$item_smv_ex = explode("/", $item_smv);
							$counter = 0;
							$tot_smv = 0;
							foreach ($item_smv_ex as $val) 
							{
								$tot_smv += $val;
								$counter++;
							}
							$avg_smv = $tot_smv/$counter;
							$target_min = $eff_target * $avg_smv; 
							// echo $pr_date."=".$style_key."=".$eff_target ."*". $avg_smv."<br>";
							$target_effi = ($efficiency_min>0) ? ($target_min / $efficiency_min)*100 : 0; 
							$achive_effi = ($efficiency_min>0) ? ($produce_minit / $efficiency_min)*100 : 0; 
							$efficiency_gap = $target_effi - $achive_effi;
							// $ttl_fob_val = $line_production_hour;
							$po_rate_arr = explode(",", $po_rate);
							$tot_po_rate = 0;
							$rate_counter = 0;
							foreach ($po_rate_arr as $value) 
							{
								$tot_po_rate +=$value;
								$rate_counter++;
							}
							$avg_rate = $tot_po_rate/$rate_counter;
							$ttl_fob_val = $line_production_hour*$avg_rate;
							// echo $line_production_hour."*".$avg_rate."<br>";
							$target_value_fob = $eff_target*$avg_rate;

							$joNos_arr = array_unique(explode(",", $production_data_arr[$pr_date][$f_id][$l_id][$style_key]['job_no']));
							//print_r($joNos_arr);die;
							$tot_cm = 0;
							$cm_counter = 0;
							$dzn_qnty = 0;
							foreach ($joNos_arr as $jobNo) 
							{
								$costing_per=$costing_per_arr[$jobNo];
								if($costing_per==1) $dzn_qnty=12;
								else if($costing_per==3) $dzn_qnty=12*2;
								else if($costing_per==4) $dzn_qnty=12*3;
								else if($costing_per==5) $dzn_qnty=12*4;
								else $dzn_qnty=1;

								// $tot_cm += $other_cost[$jobNo]['cm_cost']/$dzn_qnty;

								$tot_cm = $tot_cm_arr[$jobNo];
								$cm_counter++;
							}				
							$avg_cm = ($tot_cm/$dzn_qnty)/$cm_counter;
							$ttl_cm = $line_production_hour*$avg_cm;
							$target_cm = $eff_target*$avg_cm;
							
							$finishingCm=$finishqty*$avg_cm;

							// echo $ttl_cm."=".$l_id."=".$style_key."=".$txtDate."=".$line_production_hour."*".$avg_cm.")<br>";
							
							if($type_line==2) //No Production Line
							{
								$man_power=$production_data_arr[$f_id][$l_id][$style_key]['man_power'];
								$operator=$production_data_arr[$f_id][$l_id][$style_key]['operator'];
								$helper=$production_data_arr[$f_id][$l_id][$style_key]['helper'];
								$terget_hour=$production_data_arr[$f_id][$l_id][$style_key]['target_hour'];	
								$capacity=$production_data_arr[$f_id][$l_id][$style_key]['capacity'];
								$working_hour=$production_data_arr[$f_id][$l_id][$style_key]['working_hour']; 
								
								$floor_working_hour+=$production_data_arr[$f_id][$l_id][$style_key]['working_hour']; 
								$eff_target_floor+=$eff_target;
								$floor_today_product+=$today_product;
								$floor_avale_minute+=$efficiency_min;
								$floor_produc_min+=$produce_minit; 
								$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
								$floor_capacity+=$production_data_arr[$f_id][$l_id][$style_key]['capacity'];
								$floor_helper+=$production_data_arr[$l_id][$pr_da[$style]]['helper'];
								$floor_man_power+=$production_data_arr[$f_id][$l_id][$style_key]['man_power'];
								$floor_operator+=$production_data_arr[$f_id][$l_id][$style_key]['operator'];
								$total_operator+=$production_data_arr[$f_id][$l_id][$style_key]['operator'];
								$total_man_power+=$production_data_arr[$f_id][$l_id][$style_key]['man_power'];	
								$total_helper+=$production_data_arr[$f_id][$l_id][$style_key]['helper'];
								$total_capacity+=$production_data_arr[$f_id][$l_id][$style_key]['capacity'];
								$floor_tgt_h+=$production_data_arr[$f_id][$l_id][$style_key]['target_hour'];
								$total_working_hour+=$production_data_arr[$f_id][$l_id][$style_key]['working_hour']; 
								$gnd_total_tgt_h+=$production_data_arr[$f_id][$l_id][$style_key]['target_hour'];
								$total_terget+=$eff_target;	
								$grand_total_product+=$today_product;
								$gnd_avable_min+=$efficiency_min;
								$gnd_product_min+=$produce_minit;
								
								$gnd_total_fob_val+=$fob_val; 
								$gnd_final_total_fob_val+=$fob_val;
							}
							else
							{
								if($line_wise_style_count_arr[$pr_date][$f_id][$l_id]>1) // when multiple style run in single line
								{									
									$operator=$prod_resource_array2[$l_id][$style_key][$pr_date]['operator'];
									$helper=$prod_resource_array2[$l_id][$style_key][$pr_date]['helper'];
									$terget_hour=$prod_resource_array2[$l_id][$style_key][$pr_date]['terget_hour'];
									$working_hour=$prod_resource_array2[$l_id][$style_key][$pr_date]['working_hour'];
									$man_power=$operator + $helper;	
								}
								else
								{
									$operator=$prod_resource_array[$l_id][$pr_date]['operator'];
									$helper=$prod_resource_array[$l_id][$pr_date]['helper'];
									$terget_hour=$prod_resource_array[$l_id][$pr_date]['terget_hour'];
									$working_hour=$prod_resource_array[$l_id][$pr_date]['working_hour'];
									$man_power=$prod_resource_array[$l_id][$pr_date]['man_power'];	
								}
								

								// $man_power=$prod_resource_array[$l_id][$pr_date]['man_power'];	
								$capacity=$prod_resource_array[$l_id][$pr_date]['capacity'];
								// ======================================================
								$floor_capacity+=$prod_resource_array[$l_id][$pr_date]['capacity'];
								

								/*if($line_wise_style_count_arr[$pr_date][$f_id][$l_id]>1)
								{
									$floor_operator+=$prod_resource_array2[$l_id][$style_key][$pr_date]['operator'];
									$floor_helper+=$prod_resource_array2[$l_id][$style_key][$pr_date]['helper'];
									$floor_tgt_h+=$prod_resource_array2[$l_id][$style_key][$pr_date]['terget_hour'];	
									$floor_working_hour+=$prod_resource_array2[$l_id][$style_key][$pr_date]['working_hour'];
									$floor_man_power+=$prod_resource_array2[$l_id][$style_key][$pr_date]['operator']+$prod_resource_array2[$l_id][$style_key][$pr_date]['helper'];
								}
								else
								{
									$floor_operator+=$prod_resource_array[$l_id][$pr_date]['operator'];
									$floor_helper+=$prod_resource_array[$l_id][$pr_date]['helper'];
									$floor_tgt_h+=$prod_resource_array[$l_id][$pr_date]['terget_hour'];	
									$floor_working_hour+=$prod_resource_array[$l_id][$pr_date]['working_hour'];
									$floor_man_power+=$prod_resource_array[$l_id][$pr_date]['man_power'];
								}*/

								if($line_chk_arr[$l_id][$pr_date]=="")
								{
									if($line_wise_style_count_arr[$pr_date][$f_id][$l_id]>1)
									{
										$floor_operator+=$prod_resource_array2[$l_id][$style_key][$pr_date]['operator'];
										$floor_helper+=$prod_resource_array2[$l_id][$style_key][$pr_date]['helper'];
										$floor_tgt_h+=$prod_resource_array2[$l_id][$style_key][$pr_date]['terget_hour'];	
										$floor_working_hour+=$prod_resource_array2[$l_id][$style_key][$pr_date]['working_hour'];
										$floor_man_power+=$prod_resource_array2[$l_id][$style_key][$pr_date]['operator']+$prod_resource_array2[$l_id][$style_key][$pr_date]['helper'];
									}
									else
									{
										$floor_operator+=$prod_resource_array[$l_id][$pr_date]['operator'];
										$floor_helper+=$prod_resource_array[$l_id][$pr_date]['helper'];
										$floor_tgt_h+=$prod_resource_array[$l_id][$pr_date]['terget_hour'];	
										$floor_working_hour+=$prod_resource_array[$l_id][$pr_date]['working_hour'];
										$floor_man_power+=$prod_resource_array[$l_id][$pr_date]['man_power'];
									}
									$line_chk_arr[$l_id][$pr_date] = "kakku";
								}


								$floor_prod_hour += $prod_hours;
								$floor_tot_prod += $line_production_hour;
								$floor_line_acv += $line_acv;
								$floor_target_gap += $target_gap;
								$floor_target_min += $target_min;
								$floor_target_effi += $target_effi;
								$floor_achive_effi += $achive_effi;
								$floor_efficiency_gap += $efficiency_gap;
								$floor_style_change += $style_change;
								$floor_avg_cm += $avg_cm;
								$floor_ttl_cm += $ttl_cm;
								$floor_target_cm += $target_cm;
								$floor_avg_rate += $avg_rate;
								$floor_ttl_fob_val += $ttl_fob_val;
								$floor_target_value_fob += $target_value_fob;

								$total_prod_hour += $prod_hours;
								$total_tot_prod += $line_production_hour;
								$total_line_acv += $line_acv;
								$total_target_gap += $target_gap;
								$total_target_min += $target_min;
								$total_target_effi += $target_effi;
								$total_achive_effi += $achive_effi;
								$total_efficiency_gap += $efficiency_gap;
								$total_style_change += $style_change;
								$total_avg_cm += $avg_cm;
								$total_ttl_cm += $ttl_cm;
								$total_target_cm += $target_cm;
								$total_avg_rate += $avg_rate;
								$total_ttl_fob_val += $ttl_fob_val;
								$total_target_value_fob += $target_value_fob;


								$eff_target_floor+=$eff_target;
								$floor_today_product+=$today_product;
								$floor_avale_minute+=$efficiency_min; 
								$floor_produc_min+=$produce_minit; 
								$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
								if($line_chk_arr2[$l_id][$pr_date]=="")
								{
									if($line_wise_style_count_arr[$pr_date][$f_id][$l_id]>1)
									{
										$total_operator+=$prod_resource_array2[$l_id][$style_key][$pr_date]['operator'];
										$total_working_hour+=$prod_resource_array2[$l_id][$style_key][$pr_date]['working_hour']; 
										$gnd_total_tgt_h+=$prod_resource_array2[$l_id][$style_key][$pr_date]['terget_hour'];
										$total_helper+=$prod_resource_array2[$l_id][$style_key][$pr_date]['helper'];
										$total_man_power += $prod_resource_array2[$l_id][$style_key][$pr_date]['operator'] + $prod_resource_array2[$l_id][$style_key][$pr_date]['helper'];
									}
									else
									{
										$total_operator+=$prod_resource_array[$l_id][$pr_date]['operator'];
										$total_working_hour+=$prod_resource_array[$l_id][$pr_date]['working_hour']; 
										$gnd_total_tgt_h+=$prod_resource_array[$l_id][$pr_date]['terget_hour'];
										$total_helper+=$prod_resource_array[$l_id][$pr_date]['helper'];
										$total_man_power+=$prod_resource_array[$l_id][$pr_date]['man_power'];
									}
									$line_chk_arr2[$l_id][$pr_date] = "kakku";
								}

								// $total_man_power+=$prod_resource_array[$l_id][$pr_date]['man_power'];
								$total_capacity+=$prod_resource_array[$l_id][$pr_date]['capacity'];
								$total_terget+=$eff_target;
								$grand_total_product+=$today_product;
								$gnd_avable_min+=$efficiency_min;
								$gnd_product_min+=$produce_minit;
								$gnd_total_fob_val+=$fob_val;
								$gnd_final_total_fob_val+=$fob_val; 
								
							} 
							// echo $pr_date."=$l_id=".$eff_target."<br>"	;
							$date_data_array[$pr_date]['target'] += $eff_target;//$terget_hour;
							$date_data_array[$pr_date]['production'] += $line_production_hour;
							$date_data_array[$pr_date]['man_power'] += $man_power;
							$date_data_array[$pr_date]['prod_hours'] += $prod_hours;
							$date_data_array[$pr_date]['available_minit'] += $efficiency_min;
							$date_data_array[$pr_date]['target_min'] += $target_min;
							$date_data_array[$pr_date]['produce_minit'] += $produce_minit;

							// $date_data_array[$pr_date]['nptmin'] += $npt_date_data_array[$pr_date];

							$date_data_array[$pr_date]['style_change'] += $style_change;
							$date_data_array[$pr_date]['target_effi'] += $target_effi;
							$date_data_array[$pr_date]['achive_effi'] += $achive_effi;
							$date_data_array[$pr_date]['cm_earn'] += $ttl_cm;
							$date_data_array[$pr_date]['fob_earn'] += $ttl_fob_val;
							$date_data_array[$pr_date]['finishqty'] += $finishqty;
							$date_data_array[$pr_date]['finishproducemint'] += $finishProduceMin;
							$date_data_array[$pr_date]['finishcm'] += $finishingCm;
							$date_data_array[$pr_date]['finishfob'] += $finishingFob;
							
							$date_data_array[$pr_date]['remarks'] .= $production_data_arr[$f_id][$l_id][$style_key]['remarks'].', ';

							if($line_chk_arr3[$pr_date][$l_id]=="")
							{
								$date_data_array[$pr_date]['tot_line']++;
								$month_data_array[date('M-Y',strtotime($pr_date))]['tot_line']++;
								$line_chk_arr3[$pr_date][$l_id] = $l_id;
							}

							$month_data_array[date('M-Y',strtotime($pr_date))]['target'] += $eff_target;//$eff_target;
							$month_data_array[date('M-Y',strtotime($pr_date))]['production'] += $line_production_hour;
							$month_data_array[date('M-Y',strtotime($pr_date))]['man_power'] += $man_power;
							$month_data_array[date('M-Y',strtotime($pr_date))]['prod_hours'] += $prod_hours;
							$month_data_array[date('M-Y',strtotime($pr_date))]['available_minit'] += $efficiency_min;
							$month_data_array[date('M-Y',strtotime($pr_date))]['target_min'] += $target_min;
							$month_data_array[date('M-Y',strtotime($pr_date))]['produce_minit'] += $produce_minit;

							// $month_data_array[date('M-Y',strtotime($pr_date))]['nptmin'] += $npt_date_data_array[$pr_date];

							$month_data_array[date('M-Y',strtotime($pr_date))]['style_change'] += $style_change;
							$month_data_array[date('M-Y',strtotime($pr_date))]['target_effi'] += $target_effi;
							$month_data_array[date('M-Y',strtotime($pr_date))]['achive_effi'] += $achive_effi;
							$month_data_array[date('M-Y',strtotime($pr_date))]['cm_earn'] += $ttl_cm;
							$month_data_array[date('M-Y',strtotime($pr_date))]['fob_earn'] += $ttl_fob_val;
							
							$month_data_array[date('M-Y',strtotime($pr_date))]['finishqty'] += $finishqty;
							$month_data_array[date('M-Y',strtotime($pr_date))]['finishproducemint'] += $finishProduceMin;
							$month_data_array[date('M-Y',strtotime($pr_date))]['finishcm'] += $finishingCm;
							$month_data_array[date('M-Y',strtotime($pr_date))]['finishfob'] += $finishingFob;
							$month_data_array[date('M-Y',strtotime($pr_date))]['remarks'] .= $production_data_arr[$f_id][$l_id][$style_key]['remarks'].', ';
							
						//}
					}
				}
			
			}
		}
	}
	
	// echo "<pre>";print_r($date_data_array);die();

	$date_from = str_replace("'", "", $start_date);
	$date_to = str_replace("'", "", $txt_date_to);
	function get_date_range($first, $last, $step = '+1 day', $output_format = 'd-M-y' ) 
	{
	    $dates = array();
	    $current = strtotime($first);
	    $last = strtotime($last);

	    while( $current <= $last ) {

	        $dates[] = strtoupper(date($output_format, $current));
	        $current = strtotime($step, $current);
	    }

	    return $dates;
	}
	$date_range_arr = get_date_range($date_from,$date_to); 
	// echo "<pre>";print_r($date_range_arr);die();

	$smv_for_item="";
	$tbl_width = 1290;
	$colspan = 21;
   /* $endtime = microtime(true);
	$timediff = $endtime - $starttime;
	echo "Elapsed time : ".secondsToTime(round($timediff));die();*/
	?>
    
	<fieldset style="width:<? echo $tbl_width+20;?>px; margin: 0 auto;">
		<div class="details-part">
	       <table width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0"> 
	            <tr class="form_caption">
	                <td colspan="<? echo $colspan;?>" align="center"><strong><? echo $report_title; ?> &nbsp;</strong></td> 
	            </tr>
	            <tr class="form_caption">
	                <td colspan="<? echo $colspan;?>" align="center"><strong><? echo $companyArr[$company_id]; ?></strong></td> 
	            </tr>
	            <tr class="form_caption">
	                <td colspan="<? echo $colspan;?>" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date_from)) )." To ".change_date_format( str_replace("'","",trim($txt_date_to)) ); ?></strong></td> 
	            </tr>
	        </table>
	       <!-- ======================== Details part ============================= -->    
	        <table id="table_header_1" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
	        	<caption>Date Wise Summary</caption>
	            <thead>
	                <tr>
	                    <th width="60"><p>Date</p></th>
	                    <th width="80"><p>Target</p></th>
	                    <th width="80"><p>Production</p></th>
	                    <th width="80"><p>Variance(Pcs)</p></th>
	                    <th width="60"><p>Achieve%</p></th>
	                    <th width="60"><p>Manpower</p></th>
	                    <th width="40"><p>WH</p></th>
	                    <th width="80"><p>Available<br>/Input Min.</p></th>
	                    <th width="80"><p>Target in<br> Min.</p></th>
	                    <th width="80"><p>Produce/<br> Output<br> Min.</p></th>
	                    <th width="50"><p>NPT Min.</p></th>
	                    <th width="50"><p>Style Cha<br>nge</p></th>
	                    <th width="50"><p>Target<br>Eff%</p></th>
	                    <th width="50"><p>Achieve<br>Eff%</p></th>
	                    <th width="50"><p>CM Earn</p></th>                    
	                    <th width="50"><p>FOB Earn</p></th> 
                        <th width="50"><p>Finishing<br>Qty.</p></th>
                        <th width="50"><p>Finishing<br>Produce<br> Min. </p></th>
                        <th width="50"><p>Finishing<br>CM</p></th>
                        <th width="50"><p>Finishing<br>FOB</p></th>                  
	                    <th ><p>Remarks</p></th> 
	                </tr>
	            </thead>
	        </table>
	        <div style="width:<?= $tbl_width+20;?>px; max-height:300px; overflow-y:scroll" id="scroll_body">
	            <table class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
	                <tbody>
	                	<?
	                	$i=1;
	                	$total_target 			= 0;
				 		$total_production 		= 0;
				  		$total_varience 		= 0;
				    	$total_achive 			= 0;
				 		$total_man_power 		= 0;
						$total_prod_hours 		= 0;
						$total_available_minit 	= 0;
				 		$total_target_min 		= 0;
						$total_produce_minit 	= 0;
						$total_npt_minit 		= 0;
						$total_style_change 	= 0;
						$total_target_effi 		= 0;
						$total_achive_effi 		= 0;
				   		$total_cm_earn 			= 0;
				  		$total_fob_earn 		= 0;
						$total_finish_qty=$total_finish_prod_min=$total_finish_cm=$total_finish_fob=0;
	                	foreach ($date_range_arr as $date_key => $date_val) 
	                	{
	                		$bgcolor = ($i%2==0) ? "#ffffff" : "#f6faff";
	                		$day = date('l', strtotime($date_val));
	                		if($day=='Friday')
	                		{
	                			$bgcolor = "#ff0000";
	                		}
	                		$target 		= $date_data_array[$date_val]['target'];
	                		$production 	= $date_data_array[$date_val]['production'];
	                		$varience 		= $target - $production;
	                		$achive 		= ($target>0) ? ($production/$target)*100 : 0;
	                		$man_power 		= $date_data_array[$date_val]['man_power'];
	                		$prod_hours 	= $date_data_array[$date_val]['prod_hours'];
	                		$available_minit= $date_data_array[$date_val]['available_minit'];
	                		$target_min 	= $date_data_array[$date_val]['target_min'];
	                		$produce_minit 	= $date_data_array[$date_val]['produce_minit'];
	                		$npt_minit 		= $npt_date_data_array[$date_val]['idle_min'];
	                		$remarks 		= $npt_date_data_array[$date_val]['remarks'];
	                		$style_change 	= $date_data_array[$date_val]['style_change'];
	                		// $target_effi 	= $date_data_array[$date_val]['target_effi'];
	                		// $achive_effi 	= $date_data_array[$date_val]['achive_effi'];
	                		$cm_earn 		= $date_data_array[$date_val]['cm_earn'];
	                		$fob_earn 		= $date_data_array[$date_val]['fob_earn'];
							
							$finish_qty 	= $date_data_array[$date_val]['finishqty'];
							$finish_prod_min= $date_data_array[$date_val]['finishproducemint'];
							$finish_cm 		= $date_data_array[$date_val]['finishcm'];
							$finish_fob		= $date_data_array[$date_val]['finishfob'];
							
	                		$tot_line 		= $date_data_array[$date_val]['tot_line'];

	                		$avg_prod_hours = $prod_hours/$tot_line;
	                		// echo $prod_hours."/".$tot_line."<br>";

	                		$target_effi = ($available_minit>0) ? ($target_min / $available_minit)*100 : 0; 
							$achive_effi = ($available_minit>0) ? ($produce_minit / $available_minit)*100 : 0; 

		                	?>
		                   <tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
			                    <td width="60"><? echo date('d-M',strtotime($date_val));?></td>
			                    <td align="right" width="80"><p><? echo number_format($target,0);?></p></td>
			                    <td align="right" width="80"><p><? echo $production;?></p></td>
			                    <td align="right" width="80"><p><? echo number_format($varience);?></p></td>
			                    <td align="right" width="60"><p><? echo number_format($achive,2);?></p></td>
			                    <td align="right" width="60"><p><? echo number_format($man_power,0);?></p></td>
			                    <td align="right" width="40"><p><? echo number_format($avg_prod_hours,0);?></p></td>
			                    <td align="right" width="80"><p><? echo number_format($available_minit,0);?></p></td>
			                    <td align="right" width="80"><p><? echo number_format($target_min,0);?></p></td>
			                    <td align="right" width="80"><p><? echo number_format($produce_minit,0);?></p></td>
			                    <td align="right" width="50"><p><? echo number_format($npt_minit,0);?></p></td>
			                    <td align="right" width="50"><p><? echo number_format($style_change,0);?></p></td>
			                    <td align="right" width="50"><p><? echo number_format($target_effi,0);?>%</p></td>
			                    <td align="right" width="50"><p><? echo number_format($achive_effi,0);?>%</p></td>
			                    <td align="right" width="50"><p><? echo number_format($cm_earn,0);?></p></td>                    
			                    <td align="right" width="50"><p><? echo number_format($fob_earn,0);?></p></td> 
                                <td width="50" align="right"><p><? echo number_format($finish_qty,0);?></p></td> 
                                <td width="50" align="right"><p><? echo number_format($finish_prod_min,0);?></p></td> 
                                <td width="50" align="right"><p><? echo number_format($finish_cm,0);?></p></td> 
                                <td width="50" align="right"><p><? echo number_format($finish_fob,0);?></p></td>
                                                  
			                    <td ><p><? echo implode(", ",array_unique(array_filter(explode("**", $remarks))));?></p></td> 					    
		                    </tr>
		                    <?
		                    $i++;
		                    $total_target 			+= $target;
					 		$total_production 		+= $production;
					  		$total_varience 		+= $varience;
					    	$total_achive 			+= $achive;
					 		$total_man_power 		+= $man_power;
							$total_prod_hours 	+= $avg_prod_hours;
							$total_available_minit 	+= $available_minit;
					 		$total_target_min 		+= $target_min;
							$total_produce_minit 	+= $produce_minit;
							$total_npt_minit 		+= $npt_date_data_array[$date_val]['idle_min'];
							$total_style_change 	+= $style_change;
							$total_target_effi 		+= $target_effi;
							$total_achive_effi 		+= $achive_effi;
					   		$total_cm_earn 			+= $cm_earn;
					  		$total_fob_earn 		+= $fob_earn;
							
							$total_finish_qty		+=$finish_qty;
							$total_finish_prod_min	+=$finish_prod_min;
							$total_finish_cm		+=$finish_cm;
							$total_finish_fob		+=$finish_fob;
		                }
		                ?>
	                </tbody>
	            </table>
			</div>
			<table class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                <tfoot>
                   <tr>
	                    <th width="60">Total</th>
	                    <th width="80"><p><? echo number_format($total_target,0); ?></p></th>
	                    <th width="80"><p><? echo number_format($total_production,0); ?></p></th>
	                    <th width="80"><p><? echo number_format($total_varience,0); ?></p></th>
	                    <th width="60"><p><? //echo number_format($total_achive,0); ?></p></th>
	                    <th width="60"><p><? echo number_format($total_man_power,0); ?></p></th>
	                    <th width="40"><p><? echo number_format($total_prod_hours,0); ?></p></th>
	                    <th width="80"><p><? echo number_format($total_available_minit,0); ?></p></th>
	                    <th width="80"><p><? echo number_format($total_target_min,0); ?></p></th>
	                    <th width="80"><p><? echo number_format($total_produce_minit,0); ?></p></th>
	                    <th width="50"><p><? echo number_format($total_npt_minit,0); ?></p></th>
	                    <th width="50"><p><? echo number_format($total_style_change,0); ?></p></th>
	                    <th width="50"><p><? //echo number_format($total_target_effi,0); ?></p></th>
	                    <th width="50"><p><? //echo number_format($total_achive_effi,0); ?></p></th>
	                    <th width="50"><p><? echo number_format($total_cm_earn,0); ?></p></th>                    
	                    <th width="50"><p><? echo number_format($total_fob_earn,0); ?></p></th> 
                        <th width="50"><p><? echo number_format($total_finish_qty,0); ?></p></th>
                        <th width="50"><p><? echo number_format($total_finish_prod_min,0); ?></p></th>
                        <th width="50"><p><? echo number_format($total_finish_cm,0); ?></p></th>
                        <th width="50"><p><? echo number_format($total_finish_fob,0); ?></p></th>                  
	                    <th ><p></p></th> 					    
                    </tr>
                </tfoot>
            </table>
		</div>
		<!-- ================================== summary part ======================== -->		
		<div class="summary-part" style="margin-top: 20px;">  
	        <table id="table_header_1" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
	        	<caption>Month Wise Summary</caption>
	            <thead>
	                <tr>
	                    <th width="60"><p>Date</p></th>
	                    <th width="80"><p>Target</p></th>
	                    <th width="80"><p>Production</p></th>
	                    <th width="80"><p>Variance(Pcs)</p></th>
	                    <th width="60"><p>Achieve%</p></th>
	                    <th width="60"><p>Manpower</p></th>
	                    <th width="40"><p>WH</p></th>
	                    <th width="80"><p>Available<br>/Input Min.</p></th>
	                    <th width="80"><p>Target in<br> Min.</p></th>
	                    <th width="80"><p>Produce/<br> Output<br> Min.</p></th>
	                    <th width="50"><p>NPT Min.</p></th>
	                    <th width="50"><p>Style Cha<br>nge</p></th>
	                    <th width="50"><p>Target<br> Eff%</p></th>
	                    <th width="50"><p>Achieve <br>Eff%</p></th>
	                    <th width="50"><p>CM Earn</p></th>                    
	                    <th width="50"><p>FOB Earn</p></th>
                        <th width="50"><p>Finishing<br> Qty.</p></th>
                        <th width="50"><p>Finishing <br>Produce Min. </p></th>
                        <th width="50"><p>Finishing<br> CM</p></th>
                        <th width="50"><p>Finishing<br> FOB</p></th>                   
	                    <th ><p>Remarks</p></th> 
	                </tr>
	            </thead>
	        </table>
	        <div style="width:<?= $tbl_width+20;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
	            <table class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
	                <tbody>
	                	<?
	                	$total_target 			= 0;
				 		$total_production 		= 0;
				  		$total_varience 		= 0;
				    	$total_achive 			= 0;
				 		$total_man_power 		= 0;
						$total_prod_hours 	= 0;
						$total_available_minit 	= 0;
				 		$total_target_min 		= 0;
						$total_produce_minit 	= 0;
						$total_npt_minit 		= 0;
						$total_style_change 	= 0;
						$total_target_effi 		= 0;
						$total_achive_effi 		= 0;
				   		$total_cm_earn 			= 0;
				  		$total_fob_earn 		= 0;
						$total_finish_qty=$total_finish_prod_min=$total_finish_cm=$total_finish_fob=0;
	                	foreach ($month_data_array as $m_key => $row) 
	                	{
	                		$target 		= $row['target'];
	                		$production 	= $row['production'];
	                		$varience 		= $target - $production;
	                		$achive 		= ($target>0) ? ($production/$target)*100 : 0;
	                		$man_power 		= $row['man_power'];
	                		$prod_hours 	= $row['prod_hours'];
	                		$available_minit= $row['available_minit'];
	                		$target_min 	= $row['target_min'];
	                		$produce_minit 	= $row['produce_minit'];
	                		$npt_minit 		= $npt_month_data_array[$m_key]['idle_min'];
	                		$remarks 		= $npt_month_data_array[$m_key]['remarks'];
	                		$style_change 	= $row['style_change'];
	                		// $target_effi 	= $row['target_effi'];
	                		// $achive_effi 	= $row['achive_effi'];
	                		$cm_earn 		= $row['cm_earn'];
	                		$fob_earn 		= $row['fob_earn'];
	                		$tot_line 		= $tot_line;

	                		$avg_prod_hours = $prod_hours/$tot_line;

	                		$target_effi = ($available_minit>0) ? ($target_min / $available_minit)*100 : 0; 
							$achive_effi = ($available_minit>0) ? ($produce_minit / $available_minit)*100 : 0;

	                		$i++;
	                		$bgcolor = ($i%2==0) ? "#ffffff" : "#f6faff";
		                	?>
		                   <tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
			                    <td width="60"><p><? echo date('F',strtotime($m_key));?></p></td>
			                    <td width="80" align="right"><p><? echo number_format($target,0);?></p></td>
			                    <td width="80" align="right"><p><? echo number_format($production,0);?></p></td>
			                    <td width="80" align="right"><p><? echo number_format($varience,0);?></p></td>
			                    <td width="60" align="right"><p><? echo number_format($achive,0);?></p></td>
			                    <td width="60" align="right"><p><? echo number_format($man_power,0);?></p></td>
			                    <td width="40" align="right"><p><? echo number_format($avg_prod_hours,0);?></p></td>
			                    <td width="80" align="right"><p><? echo number_format($available_minit,0);?></p></td>
			                    <td width="80" align="right"><p><? echo number_format($target_min,0);?></p></td>
			                    <td width="80" align="right"><p><? echo number_format($produce_minit,0);?></p></td>
			                    <td width="50" align="right"><p><? echo number_format($npt_minit,0);?></p></td>
			                    <td width="50" align="right"><p><? echo number_format($style_change,0);?></p></td>
			                    <td width="50" align="right"><p><? echo number_format($target_effi,0);?>%</p></td>
			                    <td width="50" align="right"><p><? echo number_format($achive_effi,0);?>%</p></td>
			                    <td width="50" align="right"><p><? echo number_format($cm_earn,0);?></p></td>                    
			                    <td width="50" align="right"><p><? echo number_format($fob_earn,0);?></p></td>  
                                <td width="50" align="right"><p><? echo number_format($row['finishqty'],0);?></p></td> 
                                <td width="50" align="right"><p><? echo number_format($row['finishproducemint'],0);?></p></td> 
                                <td width="50" align="right"><p><? echo number_format($row['finishcm'],0);?></p></td> 
                                <td width="50" align="right"><p><? echo number_format($row['finishfob'],0);?></p></td>                 
			                    <td><p><? echo implode(", ",array_unique(array_filter(explode("**", $remarks))));?></p></td> 					    
		                    </tr>
		                    <?		                    
		                    $total_target 			+= $target;
					 		$total_production 		+= $production;
					  		$total_varience 		+= $varience;
					    	$total_achive 			+= $achive;
					 		$total_man_power 		+= $man_power;
							$total_prod_hours 	+= $avg_prod_hours;
							$total_available_minit 	+= $available_minit;
					 		$total_target_min 		+= $target_min;
							$total_produce_minit 	+= $produce_minit;
							$total_npt_minit 		+= $npt_month_data_array[$m_key]['idle_min'];
							$total_style_change 	+= $style_change;
							$total_target_effi 		+= $target_effi;
							$total_achive_effi 		+= $achive_effi;
					   		$total_cm_earn 			+= $cm_earn;
					  		$total_fob_earn 		+= $fob_earn;
							$total_finish_qty		+=$row['finishqty'];
							$total_finish_prod_min	+=$row['finishproducemint'];
							$total_finish_cm		+=$row['finishcm'];
							$total_finish_fob		+=$row['finishfob'];
					  		// echo $npt_month_data_array[$m_key]['idle_min']."<br>";
		                }
		                unset($npt_date_data_array);
		                unset($npt_month_data_array);
		                ?>
	                </tbody>
	            </table>
			</div>
			<table class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                <tfoot>
                   <tr>
	                    <th width="60"><p>Total</p></th>
	                    <th width="80"><p><? echo number_format($total_target,0); ?></p></th>
	                    <th width="80"><p><? echo number_format($total_production,0); ?></p></th>
	                    <th width="80"><p><? echo number_format($total_varience,0); ?></p></th>
	                    <th width="60"><p><? //echo number_format($total_achive,0); ?></p></th>
	                    <th width="60"><p><? echo number_format($total_man_power,0); ?></p></th>
	                    <th width="40"><p><? echo number_format($total_prod_hours,0); ?></p></th>
	                    <th width="80"><p><? echo number_format($total_available_minit,0); ?></p></th>
	                    <th width="80"><p><? echo number_format($total_target_min,0); ?></p></th>
	                    <th width="80"><p><? echo number_format($total_produce_minit,0); ?></p></th>
	                    <th width="50"><p><? echo number_format($total_npt_minit,0); ?></p></th>
	                    <th width="50"><p><? echo number_format($total_style_change,0); ?></p></th>
	                    <th width="50"><p><? //echo number_format($total_target_effi,0); ?></p></th>
	                    <th width="50"><p><? //echo number_format($total_achive_effi,0); ?></p></th>
	                    <th width="50"><p><? echo number_format($total_cm_earn,0); ?></p></th>                    
	                    <th width="50"><p><? echo number_format($total_fob_earn,0); ?></p></th> 
                        <th width="50"><p><? echo number_format($total_finish_qty,0); ?></p></th>
                        <th width="50"><p><? echo number_format($total_finish_prod_min,0); ?></p></th>
                        <th width="50"><p><? echo number_format($total_finish_cm,0); ?></p></th>
                        <th width="50"><p><? echo number_format($total_finish_fob,0); ?></p></th>                   
	                    <th ><p></p></th> 					    
                    </tr>
                </tfoot>
            </table>
		</div>
	</fieldset>
	<?    
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename,'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename";
	disconnect($con);
	exit();      

}

if($action=="report_generate2") 
{
	/*
		smv source : actual production resource entry
	*/
	$starttime = microtime(true);
	extract($_REQUEST);
	$process = array( &$_POST );	
	extract(check_magic_quote_gpc( $process ));

	function secondsToTime($s)
	{
	    $h = floor($s / 3600);
	    $s -= $h * 3600;
	    $m = floor($s / 60);
	    $s -= $m * 60;
	    return $h.':'.sprintf('%02d', $m).':'.sprintf('%02d', $s);
	}

	$start_date = $txt_date_from;
	
	$start_date_ex = explode("-", str_replace("'", "", $txt_date_from));
	// $txt_date_from = "'01-Jan-".$start_date_ex[2]."'";// $txt_date_to;
	if($db_type==0)
	{
		$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
		$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	/*if($db_type==2)
	{	
		$txt_date_from=change_date_format($txt_date_from,'','',1);
		$txt_date_to=change_date_format($txt_date_to,'','',1);
	}*/
	// echo $txt_date_from;die();
	$company_id=str_replace("'","",$cbo_company_id);
	$companyArr = return_library_array("select id,company_name from lib_company","id","company_name"); 
	$buyerArr = return_library_array("select id,short_name from lib_buyer","id","short_name"); 
	$locationArr = return_library_array("select id,location_name from lib_location","id","location_name"); 
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name"); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line where company_name='$company_id'","id","line_name"); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	
    $today_date=date("Y-m-d");
	//**********************************************************************************************
	$lineDataArr = sql_select("SELECT id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1 and company_name=$cbo_company_id order by sewing_line_serial"); 
	foreach($lineDataArr as $lRow)
	{
		$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
		$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
		$lastSlNo=$lRow[csf('sewing_line_serial')];
	}
	
	
	if($db_type==0)
	{
		$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$company_id and shift_id=1 and pr_date between $txt_date_from and $txt_date_to and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");	
	}
	else
	{
		$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$company_id and shift_id=1 and pr_date between $txt_date_from and $txt_date_to and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
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
	$actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$txt_date_from)));
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

	/*===================================================================================== /
	/								get actual resource variable							/
	/===================================================================================== */
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_id and variable_list=23 and is_deleted=0 
	and status_active=1");

	/*===================================================================================== /
	/										query condition									/
	/===================================================================================== */
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and b.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else  $buyer_id_cond="";
		}
		else
		{
		$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and b.buyer_name=$cbo_buyer_name";
	}
	
	//if(str_replace("'","",$cbo_company_id)==0) $company_name=""; else $company_name="and a.company_id=".str_replace("'","",$cbo_company_id)."";
	if(str_replace("'","",$cbo_company_id)==0) $company_name=""; else $company_name="and a.serving_company=".str_replace("'","",$cbo_company_id)."";
	
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
	$date_cond="";
	if(str_replace("'","",trim($txt_date_from))!="")
	{
		$date_cond =" and a.production_date between $txt_date_from AND $txt_date_to";
	}

	/*===================================================================================== /
	/										prod resource data								/
	/===================================================================================== */	
	// echo $prod_reso_allo[0]; die;
	$prod_res_cond = "";
	$prod_res_cond .= (str_replace("'", "", $cbo_location_id)==0) ? "" : " and a.location_id=$cbo_location_id";
	
	if($prod_reso_allo[0]==1)
	{
		$prod_resource_array=array();
		$prod_resource_array2=array();
		$prod_resource_smv_array = array();

		$dataArray_sql=sql_select("SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id=$company_id and b.pr_date between $txt_date_from and $txt_date_to and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $prod_res_cond");
		
		foreach($dataArray_sql as $val)
		{
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['man_power']+=$val[csf('man_power')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['operator']+=$val[csf('operator')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['helper']+=$val[csf('helper')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['terget_hour']+=$val[csf('target_per_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['working_hour']+=$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['tpd']+=$val[csf('target_per_hour')]*$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_start']=$val[csf('from_date')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_end']=$val[csf('to_date')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['capacity']+=$val[csf('capacity')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust']=$val[csf('smv_adjust')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];		
		}
		// echo "<pre>";print_r($prod_resource_array);die();

		// =======================================================
		$sql="SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, c.from_date,c.to_date,c.capacity,d.po_id,d.target_per_line,d.operator, d.helper,d.working_hour,d.actual_smv,d.gmts_item_id from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c,prod_resource_color_size d where a.id=c.mst_id and c.id=b.mast_dtl_id and d.mst_id=a.id and d.dtls_id=c.id and a.company_id=$company_id and b.pr_date between $txt_date_from and $txt_date_to and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $prod_res_cond";
		// echo $sql;die();
		$sql_res=sql_select($sql);
		$poIds_arr = array();
		foreach($sql_res as $vals)
		{
			$poIds_arr[$vals[csf('po_id')]] = $vals[csf('po_id')];
		}
		$poIds = implode(",", $poIds_arr);
		$style_arr = return_library_array("SELECT a.job_no,b.id from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and a.status_active=1 and b.status_active=1 and b.id in($poIds)","id","job_no");
		foreach($sql_res as $val)
		{
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['man_power']+=$val[csf('operator')]+$val[csf('helper')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['operator']+=$val[csf('operator')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['helper']+=$val[csf('helper')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['terget_hour']+=$val[csf('target_per_line')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['working_hour']+=$val[csf('working_hour')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['tpd']+=$val[csf('target_per_line')]*$val[csf('working_hour')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['day_start']=$val[csf('from_date')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['day_end']=$val[csf('to_date')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['capacity']=$val[csf('capacity')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['smv_adjust']=$val[csf('smv_adjust')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];
			$prod_resource_smv_array[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('gmts_item_id')]][$val[csf('pr_date')]]['actual_smv']=$val[csf('actual_smv')];
		}

		// echo "<pre>"; print_r($prod_resource_array2);die();

		if(str_replace("'","",trim($txt_date_from))!=""){$pr_date_con=" and b.pr_date between $txt_date_from and $txt_date_to";}

		if($db_type==0)
		{
			$dataArray=sql_select("SELECT a.id,b.pr_date,d.shift_id,TIME_FORMAT( d.prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( d.lunch_start_time, '%H:%i' ) as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$company_id and shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 $pr_date_con"); 
		}
		else
		{
			$dataArray=sql_select("SELECT a.id,b.pr_date,d.shift_id,TO_CHAR(d.prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR( d.lunch_start_time,'HH24:MI') as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$company_id and shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 $pr_date_con");
		}
		
		$line_number_arr=array();
		foreach($dataArray as $val)
		{
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['shift_id']=$val[csf('shift_id')];
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['prod_start_time']=$val[csf('prod_start_time')];
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['lunch_start_time']=$val[csf('lunch_start_time')];
		}
		
		$sqlExtraHour="SELECT a.FLOOR_ID, b.MST_ID,b.TOTAL_SMV, b.PR_DATE FROM prod_resource_mst a, prod_resource_smv_adj b WHERE  a.id = b.mst_id AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0 AND b.ADJUSTMENT_SOURCE = 1 $pr_date_con";
		$sqlExtraHourResultArr=sql_select($sqlExtraHour);
		$extra_minute_production_arr=array();
		$extra_minute_resource_arr=array();
		foreach($sqlExtraHourResultArr as $ex_row)
		{
			$extra_minute_production_arr[$ex_row[FLOOR_ID]][$ex_row[MST_ID]]+=$ex_row[TOTAL_SMV];
			$extra_minute_resource_arr[$ex_row[MST_ID]][$ex_row[PR_DATE]]+=$ex_row[TOTAL_SMV];
		}

		/*===============================================================================/
		/							Actual resource SMV data							 /
		/============================================================================== */
		$prod_resource_smv_adj_array = array();
		$sql_query="SELECT b.mst_id, b.pr_date,b.number_of_emp ,b.adjust_hour,b.total_smv,b.adjustment_source from prod_resource_mst a,prod_resource_smv_adj b  where a.id=b.mst_id  and a.company_id=$company_id and b.pr_date between $txt_date_from and $txt_date_to and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 and b.status_active=1  $prod_res_cond";
		// echo $sql_query;
		$sql_query_res=sql_select($sql_query);
		foreach($sql_query_res as $val)
		{
			$val[csf('pr_date')]=date("d-M-Y",strtotime($val[csf('pr_date')]));
			
			$prod_resource_smv_adj_array[$val[csf('mst_id')]][$val[csf('pr_date')]][$val[csf('adjustment_source')]]['number_of_emp']+=$val[csf('number_of_emp')];
			$prod_resource_smv_adj_array[$val[csf('mst_id')]][$val[csf('pr_date')]][$val[csf('adjustment_source')]]['adjust_hour']+=$val[csf('adjust_hour')];
			$prod_resource_smv_adj_array[$val[csf('mst_id')]][$val[csf('pr_date')]][$val[csf('adjustment_source')]]['total_smv']+=$val[csf('total_smv')];
		}
		// echo "<pre>";print_r($prod_resource_smv_adj_array);die();
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
	$prod_start_time=sql_select("select $prod_start_cond as prod_start_time from variable_settings_production where company_name=$cbo_company_id and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");
	//echo "select company_name, prod_start_time from variable_settings_production where company_name=$cbo_company_id and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1";
	foreach($prod_start_time as $row)
	{
		$ex_time=explode(" ",$row[csf('prod_start_time')]);
		if($db_type==0) $variable_start_time_arr=$row[csf('prod_start_time')];
		else if($db_type==2) $variable_start_time_arr=$ex_time[1];
	}//die;
	//echo $variable_start_time_arr;
	unset($prod_start_time);
	$current_date_time=date('d-m-Y H:i');
	$variable_date=change_date_format(str_replace("'","",$txt_date_from)).' '.$variable_start_time_arr;
	//echo $variable_date.'='.$current_date_time;
	$datediff=datediff("n",$variable_date,$current_date_time);
	
	$ex_date_time=explode(" ",$current_date_time);
	$current_date=$ex_date_time[0];
	$current_time=$ex_date_time[1];
	$ex_time=explode(":",$current_time);
	
	$search_prod_date=change_date_format(str_replace("'","",$txt_date_from));
	$current_eff_min=($ex_time[0]*60)+$ex_time[1];
	//echo $current_date.'='.$search_prod_date;
	$variable_time= explode(":",$variable_start_time_arr);
	$vari_min=($variable_time[0]*60)+$variable_time[1];
	$difa_time=explode(".",number_format(($current_eff_min-$vari_min)/60,2));//datediff("",$ctime,$variable_start_time_arr);
	$dif_time=number_format($datediff/60,2);
	$dif_hour_min=date("H", strtotime($dif_time));
	/*===================================================================================== /
	/										smv sorce 										/
	/===================================================================================== */
   	$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($manufacturing_company) and variable_list=25 and  status_active=1 and is_deleted=0");
	// echo $smv_source;
    if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
   
	if($db_type==2)
	{
		$pr_date=str_replace("'","",$txt_date_from);
		$pr_date_old=explode("-",str_replace("'","",$txt_date_from));
		$month=strtoupper($pr_date_old[1]);
		$year=substr($pr_date_old[2],2);
		$pr_date=$pr_date_old[0]."-".$month."-".$year;
	}
	if($db_type==0)
	{
		$pr_date=str_replace("'","",$txt_date_from);
	}
	
	$i=1; $grand_total_good=0; $grand_alter_good=0; $grand_total_reject=0;
	$html=""; $floor_html=""; $check_arr=array();
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
		$sql.="sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23 from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a left join lib_prod_floor d on a.floor_id=d.id and d.is_deleted=0 left join lib_sewing_line e on a.sewing_line=e.id and e.is_deleted=0  where a.production_type=5 and a.po_break_down_id=c.id and c.job_id=b.id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $company_name $location $floor $line $buyer_id_cond  $date_cond group by b.job_no,a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,e.sewing_line_serial,b.buyer_name,a.item_number_id,c.po_number,c.file_no,c.unit_price,c.grouping,b.style_ref_no,d.color_type_id,a.remarks order by a.production_date";
	}
	else if($db_type==2)
	{
		$sql="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name  as buyer_name, b.style_ref_no, b.job_no, a.po_break_down_id, a.item_number_id, c.po_number as po_number, c.file_no, c.unit_price, c.grouping as ref, d.color_type_id, a.remarks, sum(CASE WHEN a.production_type=5 THEN production_qnty else 0 END) as good_qnty,"; 
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
		$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN production_qnty else 0 END) AS prod_hour23, sum(CASE WHEN a.production_type=8 THEN production_qnty else 0 END) AS finish_qty
		FROM pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c, pro_garments_production_dtls d
		WHERE a.production_type in (5,8) and a.po_break_down_id=c.id and c.job_id=b.id and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_name $location $floor $line $buyer_id_cond  $date_cond and d.production_qnty>0
		GROUP BY b.job_no, a.company_id, a.location, a.floor_id, a.po_break_down_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name, b.style_ref_no, a.item_number_id, c.po_number, c.unit_price, c.file_no, c.grouping, d.color_type_id, a.remarks
		ORDER BY a.production_date";
	}
	// echo $sql;die;
	$sql_resqlt=sql_select($sql);
	$production_data_arr=array(); $style_chane_arr=array(); $production_po_data_arr=array(); $production_serial_arr=array(); $reso_line_ids='';  $all_po_id=""; $active_days_arr=array(); $duplicate_date_arr=array(); $poIdArr=array(); $jobArr=array(); $prod_line_array = array(); $line_style_chk_array = array(); $date_wise_line_chk_array = array();
	foreach($sql_resqlt as $val)
	{	
		$prod_line_array[$val[csf('sewing_line')]] = $val[csf('sewing_line')];
		$poIdArr[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];	
		if($val[csf('sewing_line')]=="") $val[csf('sewing_line')]=0;
		if($val[csf('prod_reso_allo')]==1)
		{
			$sewing_line_id=$prod_reso_arr[$val[csf('sewing_line')]];
			$reso_line_ids.=$val[csf('sewing_line')].',';
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
		$production_serial_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]][$val[csf('job_no')]]=$val[csf('sewing_line')];
		$date_wise_line_chk_array[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]] = $val[csf('sewing_line')];
		
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
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]][$prod_hour]+=$val[csf($prod_hour)]; 
			
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
			{
				if( $h>=$line_start_hour && $h<=$actual_time)
				{
					$production_po_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf($prod_hour)]; 
				} 	
			}
			
			if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date)) 
			{	
				$production_po_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf($prod_hour)];
			}
		}
		
		if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
		{	
			if( $h>=$line_start_hour && $h<=$actual_time)
			{
				$production_po_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf('prod_hour23')];
			} 	
		}
		else
		{
			$production_po_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf('prod_hour23')];     
		}
		
	 	$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['prod_hour23']+=$val[csf('prod_hour23')];  
		$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['prod_reso_allo']=$val[csf('prod_reso_allo')]; 
		
	 	if($production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['buyer_name']!="")
		{
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['buyer_name'].=",".$val[csf('buyer_name')]; 
		}
	 	else
		{
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['buyer_name']=$val[csf('buyer_name')]; 
		}

		if($line_style_chk_array[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]] =="")
		{
			$line_wise_style_count_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]++;
			$line_wise_style_count_arr2[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]++;
			$line_style_chk_array[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]] = $val[csf('job_no')];
		}

		/*if($line_wise_style_count_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]!="")
		{
			$line_wise_style_count_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]].=",".$val[csf('buyer_name')]; 
		}
	 	else
		{
			$line_wise_style_count_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]=$val[csf('buyer_name')]; 
		}*/

	
	 	if($production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['po_number']!="")
		{
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['po_number'].=",".$val[csf('po_number')];
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['job_no'].=",".$val[csf('job_no')];
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['po_id'].=",".$val[csf('po_break_down_id')];
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['style'].="##".$val[csf('job_no')];
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['file'].=",".$val[csf('file_no')];
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['ref'].=",".$val[csf('ref')]; 
		}
	 	else
		{
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['po_number']=$val[csf('po_number')];
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['job_no']=$val[csf('job_no')];
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['po_id']=$val[csf('po_break_down_id')]; 
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['style']=$val[csf('job_no')]; 
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['file']=$val[csf('file_no')]; 
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['ref']=$val[csf('ref')]; 
		}

		if($style_chane_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['style']!="")
		{
			$style_chane_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['style'].="##".$val[csf('job_no')];
		}
	 	else
		{
			$style_chane_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['style']=$val[csf('job_no')];
		}

		if ($val[csf('remarks')] !="") 
		{
		 	if($production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['remarks']!="")
			{
				$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['remarks'].=",".$val[csf('remarks')]; 
			}
		 	else
			{
				$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['remarks']=$val[csf('remarks')]; 
			}
		}

		if($po_rate_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['rate']!="")
		{
			$po_rate_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['rate'].=",".$val[csf('unit_price')]; 
		}
		else
		{
			$po_rate_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['rate'] = $val[csf('unit_price')];
		}
		
		if($production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['item_number_id']!="")
		{
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('unit_price')]; 
		}
		else
		{
			 $production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('unit_price')]; 
		}
		$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['quantity']+=$val[csf('good_qnty')];
		$production_data_arr_qty[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]['quantity']+=$val[csf('good_qnty')];
		
		$production_data_arr_qty[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]['finishqty']+=$val[csf('finish_qty')];
		
		if($all_po_id=="") $all_po_id=$val[csf('po_break_down_id')]; else $all_po_id.=",".$val[csf('po_break_down_id')];
		$jobArr[$val[csf('job_no')]] = $val[csf('job_no')];
	}
	// $production_serial_arr[$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]][$val[csf('job_no')]]=$val[csf('sewing_line')];
	// $production_serial_arr[5000][1][8000][0]=8000;




	// echo "<pre>"; print_r($style_chane_arr);die();
	/*===================================================================================== /
	/										po item wise smv 								/
	/===================================================================================== */
	$po_id_cond = where_con_using_array($poIdArr,0,"b.id");

	$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_id=a.id and b.job_id=c.job_id $po_id_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3)";
	// echo $smv_source."===".$sql_item;die;
	$resultItem=sql_select($sql_item);
	foreach($resultItem as $itemData)
	{
		if($smv_source==1)
		{
			$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
		}
		else if($smv_source==2)
		{
			$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs_precost')];
		}
	}
	// print_r($item_smv_array);die();
	/*===================================================================================== /
	/								color tipe wise smv 									/
	/===================================================================================== */
	/*$sql_item="SELECT max(a.id) as mst_id,a.total_smv,a.style_ref, a.gmts_item_id,c.id,a.color_type from ppl_gsd_entry_mst a,wo_po_details_master b, wo_po_break_down c where a.style_ref=b.style_ref_no and a.bulletin_type=4 and  TRUNC(a.insert_date)<=TO_DATE($txt_date) and a.is_deleted=0 and a.status_active=1 and b.job_no=c.job_no_mst and c.id in($all_po_id) and  b.status_active=1 and b.is_deleted=0 
		group by a.total_smv,a.style_ref, a.gmts_item_id,c.id,a.color_type ";
	//echo $sql_item;die;
	$resultItem=sql_select($sql_item);
	foreach($resultItem as $itemData)
	{
		$item_smv_array_color_type[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]][$itemData[csf('color_type')]]=$itemData[csf('total_smv')];
	}

	foreach($sql_resqlt as $val2)
	{
		//echo $val2[csf('po_break_down_id')]."**".$val2[csf('item_number_id')]."**".$val2[csf('color_type_id')]."<br/>";
		if($item_smv_array_color_type[$val2[csf('po_break_down_id')]][$val2[csf('item_number_id')]][$val2[csf('color_type_id')]]!="")
		$item_smv_array[$val2[csf('po_break_down_id')]][$val2[csf('item_number_id')]]=$item_smv_array_color_type[$val2[csf('po_break_down_id')]][$val2[csf('item_number_id')]][$val2[csf('color_type_id')]];
	}*/

	// print_r($item_smv_array);die();
	$po_ids=count(array_unique(explode(",",$all_po_id)));
	$po_numIds=chop($all_po_id,',');
	$poIds_cond="";
	$poIds_cond2="";
	if($all_po_id!='' || $all_po_id!=0)
	{
		if($db_type==2 && $po_ids>1000)
		{
			$poIds_cond=" and (";
			$poIds_cond2=" and (";
			$poIds_cond3=" and (";
			$poIdsArr=array_chunk(explode(",",$po_numIds),990);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$poIds_cond.=" b.id  in($ids) or ";
				$poIds_cond2.=" c.id  in($ids) or ";
				$poIds_cond3.=" po_break_down_id  in($ids) or ";
			}
			$poIds_cond=chop($poIds_cond,'or ');
			$poIds_cond2=chop($poIds_cond2,'or ');
			$poIds_cond3=chop($poIds_cond3,'or ');
			$poIds_cond.=")";
			$poIds_cond2.=")";
			$poIds_cond3.=")";
		}
		else
		{
			$poIds_cond=" and  b.id  in($all_po_id)";
			$poIds_cond2=" and  c.id  in($all_po_id)";
			$poIds_cond3=" and  po_break_down_id  in($all_po_id)";
		}
	}
	/*===================================================================================== /
	/										po active days									/
	/===================================================================================== */
    $po_active_sql="SELECT a.floor_id,a.sewing_line,a.production_date,a.po_break_down_id,a.item_number_id from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a where a.production_type=5 and a.po_break_down_id=c.id and c.job_id=b.id and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $company_name $location $floor $line $buyer_id_cond  $poIds_cond2 group by  a.floor_id,a.sewing_line,a.production_date ,a.po_break_down_id,a.item_number_id";
    //echo $po_active_sql;die;
	foreach(sql_select($po_active_sql) as $vals)
	{
		$prod_dates=$vals[csf('production_date')];
		if($duplicate_date_arr[$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]][$prod_dates]=="")
		{
			$active_days_arr[$vals[csf('floor_id')]][$vals[csf('sewing_line')]]++;
			$active_days_arr_powise[$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]]+=1;
			$duplicate_date_arr[$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]][$prod_dates]=$prod_dates;
		}
	}
	// echo "<pre>"; print_r($active_days_arr);
	/*===================================================================================== /
	/								item wise order qty and value							/
	/===================================================================================== */
	$sql_item_rate="SELECT b.id, c.item_number_id, c.order_quantity, c.order_total from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c where b.job_id=a.id and b.id=c.po_break_down_id and b.job_id=c.job_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1  $file_cond $ref_cond  $poIds_cond";
	$resultRate=sql_select($sql_item_rate);
	$item_po_array=array();
	foreach($resultRate as $row)
	{
		$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['qty']+=$row[csf('order_quantity')];
		$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['amt']+=$row[csf('order_total')];
	}
	
	/*===================================================================================== /
	/										subcoutact data									/
	/===================================================================================== */
    if($db_type==0)
    {
		$sql_sub_contuct= "SELECT  a.company_id, a.location_id,d.floor_serial_no,e.sewing_line_serial,a.prod_reso_allo, a.floor_id, a.production_date, a.line_id,b.party_id  as buyer_name,a.order_id,c.order_no as po_number,c.cust_style_ref,b.subcon_job as job_no, max(c.smv) as smv,sum(a.production_qnty) as good_qnty,"; 
		
		$first=1;
		for($h=$hour;$h<$last_hour;$h++)
		{
			$bg=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="prod_hour".substr($bg,0,2);
			if($first==1)
			{
				$sql_sub_contuct.="sum(CASE WHEN  a.hour<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,";
			}
			else
			{
				$sql_sub_contuct.="sum(CASE WHEN a.hour>'$bg' and a.hour<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,";	
			}
			$first=$first+1;
   		}
   		$sql_sub_contuct.="sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.id and d.is_deleted=0 left join lib_sewing_line e on a.line_id=e.id and e.is_deleted=0 where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id=$company_id $subcon_location $floor $subcon_line   $date_cond group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date,a.prod_reso_allo,a.line_id,b.party_id,c.order_no,c.cust_style_ref,b.subcon_job ,e.sewing_line_serial order by a.location_id,d.floor_serial_no,e.sewing_line_serial,a.prod_reso_allo";
	}
	else
	{
		$sql_sub_contuct= "SELECT a.company_id, a.location_id, a.floor_id,d.floor_serial_no,e.sewing_line_serial, a.production_date, a.line_id,b.party_id  as buyer_name,a.prod_reso_allo,a.order_id,c.order_no as po_number,c.cust_style_ref, b.subcon_job as job_no,  max(c.smv) as smv,a.prod_reso_allo,sum(a.production_qnty) as good_qnty,"; 
		$first=1;
		for($h=$hour;$h<$last_hour;$h++)
		{
			$bg=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="prod_hour".substr($bg,0,2);
			if($first==1)
			{
				$sql_sub_contuct.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,";
			}
			else
			{
				$sql_sub_contuct.="sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')>'$bg' and TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,";	
			}
			$first++;
		}
		
	   	$sql_sub_contuct.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]'	and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.id  and d.is_deleted=0 left join lib_sewing_line e on a.line_id=e.id and e.is_deleted=0 where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id=$company_id $subcon_location $floor $subcon_line   $date_cond group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date,  b.subcon_job,a.line_id,b.party_id,c.order_no,c.cust_style_ref,e.sewing_line_serial,a.prod_reso_allo order by a.location_id, a.floor_id,e.sewing_line_serial,a.prod_reso_allo";
		
	}
	// echo $sql_sub_contuct;die;
	$sub_result=sql_select($sql_sub_contuct);
	$subcon_order_smv=array();		
	foreach($sub_result as $subcon_val)
	{
		$prod_line_array[$subcon_val[csf('sewing_line')]] = $subcon_val[csf('sewing_line')];
		if($subcon_val[csf('prod_reso_allo')]==1)
		{
			$sewing_line_id=$prod_reso_arr[$subcon_val[csf('sewing_line')]];
		}
		else
		{
			$sewing_line_id=$subcon_val[csf('sewing_line')];
		}
		
		if($lineSerialArr[$sewing_line_id]=="")
		{
			$lastSlNo++;
			$slNo=$lastSlNo;
			$lineSerialArr[$sewing_line_id]=$slNo;
		}
		else $slNo=$lineSerialArr[$sewing_line_id];
		//$production_serial_arr[$subcon_val[csf('floor_id')]][$slNo][$subcon_val[csf('sewing_line')]]=$subcon_val[csf('sewing_line')];
		
		$production_po_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$subcon_val[csf('good_qnty')];
		if($production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['buyer_name']!="")
		{
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['buyer_name'].=",".$subcon_val[csf('buyer_name')]; 
		}
		else
		{
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['buyer_name']=$subcon_val[csf('buyer_name')]; 
		}
		if($line_style_chk_array[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('sewing_line')]][$subcon_val[csf('style_ref_no')]] =="")
		{
			$line_wise_style_count_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('sewing_line')]]++;
			$line_wise_style_count_arr2[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('sewing_line')]]++;
			$line_style_chk_array[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('sewing_line')]][$subcon_val[csf('style_ref_no')]] = $subcon_val[csf('style_ref_no')];
		}
	
		if($production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['po_number']!="")
		{
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['po_number'].=",".$subcon_val[csf('po_number')];
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['job_no'].=",".$subcon_val[csf('job_no')];
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['style'].="##".$subcon_val[csf('cust_style_ref')];  
		}
		else
		{
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['po_number']=$subcon_val[csf('po_number')]; 
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['job_no']=$subcon_val[csf('job_no')]; 
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['style']=$subcon_val[csf('cust_style_ref')]; 
		}
	
		if($production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['order_id']!="")
		{
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['order_id'].=",".$subcon_val[csf('order_id')]; 
		}
		else
		{
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['order_id'].=$subcon_val[csf('order_id')]; 
		}
		$subcon_order_smv[$subcon_val[csf('order_id')]]=$subcon_val[csf('smv')];
		$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['quantity']+=$subcon_val[csf('good_qnty')];
		
	 	$line_start=$line_number_arr[$val[csf('line_id')]][$val[csf('production_date')]]['prod_start_time']	;
	 	if($line_start!="") 
	 	{ 
			$line_start_hour=substr($line_start,0,2); 
			if(substr($line_start_hour,0,1)==0)  $line_start_hour=substr($line_start_hour,1,1);	
	 	}
		else
	 	{
			$line_start_hour=$hour; 
	 	}
		for($h=$hour;$h<=$last_hour;$h++)
		{
			$prod_hour="prod_hour".substr($start_hour_arr[$h],0,2).""; 
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]][$prod_hour]+=$subcon_val[csf($prod_hour)]; 
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
			{
				 if( $h>=$line_start_hour && $h<=$actual_time)
				 {
				 $production_po_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf($prod_hour)];	                 } 
			}
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
			{
				$production_po_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf($prod_hour)];	            }
		 }
		if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
		{	
			if( $h>=$line_start_hour && $h<=$actual_time)
			{
				$production_po_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf('prod_hour23')];
			} 	
		}
		else
		{
			$production_po_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf('prod_hour23')];
		}
		$production_data_arr[$subcon_val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['prod_hour23']+=$val[csf('prod_hour23')];
	}

	/*===================================================================================== /
	/							prod resource data no prod line								/
	/===================================================================================== */	
	// echo $prod_reso_allo[0]; die;

	$prod_line_ids = implode(",", array_filter($prod_line_array));
	
	if($prod_reso_allo[0]==1)
	{
		$dataArray_sql=sql_select("SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id=$company_id and b.pr_date between $txt_date_from and $txt_date_to and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $prod_res_cond ");//and a.id not in($prod_line_ids) 
		
		foreach($dataArray_sql as $val)
		{
			$sewing_line_id=$prod_reso_arr[$val[csf('id')]];			
			
			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id]=$slNo;
			}
			else $slNo=$lineSerialArr[$sewing_line_id];
			if($date_wise_line_chk_array[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]]=="")
			{
				$production_serial_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$slNo][$val[csf('id')]][]=$val[csf('id')];			
				$production_serial_arr2[$val[csf('pr_date')]][$val[csf('floor_id')]][$slNo][$val[csf('id')]]=$val[csf('id')];
			}
		}		
	}
	
	/*===================================================================================== /
	/							For Summary Report New Add No Prodcut						/
	/===================================================================================== */
	if($cbo_no_prod_type==1)
	{
		//No Production line Start ....
		$prod_reso_arr=return_library_array( "SELECT id, line_number from prod_resource_mst",'id','line_number');
		$sql_active_line=sql_select("SELECT sewing_line,sum(production_quantity) as total_production from pro_garments_production_mst  where production_date between $txt_date_from and $txt_date_to and production_type=5 and status_active=1 and is_deleted=0 and prod_reso_allo=1 group by  sewing_line");
		//$actual_line_arr=array();
		foreach($sql_active_line as $inf)
		{	
		   if(str_replace("","",$inf[csf('sewing_line')])!="")
		   {
				//if(str_replace("","",$actual_line_arr)!="") $actual_line_arr.=",";
			    //$actual_line_arr.="'".$inf[csf('sewing_line')]."'";
		   }
		}
		//echo $actual_line_arr;die;
		$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and is_deleted=0 and status_active=1");
		$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
		if(str_replace("'","",$cbo_location_id)!=0) 
		{
			$location=" and a.location_id=".str_replace("'","",$cbo_location_id)."";
		}		
		
		$res_line_cond=rtrim($reso_line_ids,",");
		
		 $dataArray_sum=sql_select("SELECT a.id, a.floor_id,1 as prod_reso_allo,2 as type_line, a.line_number as line_no, b.man_power, b.operator, b.helper, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type,d.prod_start_time from  prod_resource_dtls b,prod_resource_dtls_time d,prod_resource_mst a  where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$company_id and b.pr_date=".$txt_date." and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 and a.id not in($res_line_cond)  $location   group by a.id, a.floor_id, a.line_number, d.prod_start_time,b.man_power, b.operator, b.helper, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type order by a.floor_id");
		 $no_prod_line_arr=array();
		 foreach( $dataArray_sum as $row)
		 { 			 
			$sewing_line_id=$row[csf('line_no')];
		
			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id]=$slNo;
			}
			else $slNo=$lineSerialArr[$sewing_line_id];
			
			// $production_serial_arr[$row[csf('floor_id')]][$slNo][$row[csf('id')]]=$row[csf('id')]; 
			$production_data_arr[$row[csf('floor_id')]][$row[csf('id')]]['type_line']=$row[csf('type_line')];
			$production_data_arr[$row[csf('floor_id')]][$row[csf('id')]]['prod_reso_allo']=$row[csf('prod_reso_allo')]; 
			$production_data_arr[$row[csf('floor_id')]][$row[csf('id')]]['man_power']=$row[csf('man_power')]; 
			$production_data_arr[$row[csf('floor_id')]][$row[csf('id')]]['operator']=$row[csf('operator')]; 
			$production_data_arr[$row[csf('floor_id')]][$row[csf('id')]]['helper']=$row[csf('helper')]; 
			$production_data_arr[$row[csf('floor_id')]][$row[csf('id')]]['working_hour']=$row[csf('working_hour')];						
			$production_data_arr[$row[csf('floor_id')]][$row[csf('id')]]['terget_hour']=$row[csf('target_per_hour')];
			$production_data_arr[$row[csf('floor_id')]][$row[csf('id')]]['total_line_hour']=$row[csf('man_power')]*$row[csf('working_hour')]; 
			$production_data_arr[$row[csf('floor_id')]][$row[csf('id')]]['smv_adjust']=$row[csf('smv_adjust')]; 
			$production_data_arr[$row[csf('floor_id')]][$row[csf('id')]]['smv_adjust_type']=$row[csf('smv_adjust_type')]; 
			$production_data_arr[$row[csf('floor_id')]][$row[csf('id')]]['prod_start_time']=$row[csf('prod_start_time')];
		 }
		 $dataArray_sql_cap=sql_select("SELECT  a.floor_id, a.line_number as line_no,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id  and a.company_id=$company_id and b.pr_date between $txt_date_from and $txt_date_to  and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  group by a.id,a.line_number, a.floor_id, a.line_number, b.man_power, b.operator,c.capacity, b.helper, b.working_hour,b.target_per_hour order by a.floor_id");
		 
		 //$prod_resource_array_summary=array();
		 foreach( $dataArray_sql_cap as $row)
		 {
			 $production_data_arr[$row[csf('floor_id')]][$row[csf('line_no')]]['capacity']=$row[csf('capacity')]; 
		 }
	
	} //End
	
	//echo "<pre>";
	// echo "<pre>"; print_r($production_serial_arr);die;
	
	
	$allJobs = "'".implode("','", $jobArr)."'";
	$costing_per_arr = return_library_array("SELECT job_no, costing_per from wo_pre_cost_mst where status_active=1 and job_no in($allJobs)","job_no","costing_per");
	$tot_cm_arr = return_library_array("SELECT job_no, cm_cost from wo_pre_cost_dtls where status_active=1 and job_no in($allJobs)","job_no","cm_cost");

	// ============================== get npt min ===========================
	$date_cond_npt = str_replace("production_date", IDLE_DATE, $date_cond);
	$sql = "SELECT a.IDLE_DATE,b.MANPOWER,b.DURATION_HOUR,b.REMARKS from SEWING_LINE_IDLE_MST a, SEWING_LINE_IDLE_DTLS b where a.id=b.mst_id and a.company_id=$company_id $subcon_location $date_cond_npt";
	// echo $sql; die();
	$nptRes = sql_select($sql);
	$npt_date_data_array = array();
	$npt_month_data_array = array();
	foreach ($nptRes as $val) 
	{
		$nptmin = $val['MANPOWER'] * $val['DURATION_HOUR'] * 60;
		// $npt_date_data_array[$val['IDLE_DATE']][$val['FLOOR_ID']][$val['PROD_RESOURCE_ID']] += $nptmin;
		$npt_date_data_array[$val['IDLE_DATE']]['idle_min'] += $nptmin;
		$npt_date_data_array[$val['IDLE_DATE']]['remarks'] .= $val['REMARKS']."**";
		$npt_month_data_array[date('M-Y',strtotime($val['IDLE_DATE']))]['idle_min'] += $nptmin;
		$npt_month_data_array[date('M-Y',strtotime($val['IDLE_DATE']))]['remarks'] .= $val['REMARKS']."**";
	}
	// echo "<pre>"; print_r($production_serial_arr);die;
	
	/*$condition= new condition();
	if($cbo_company_name>0){
		$condition->company_name("=$company_id");
	}
	if(count($poIdArr)>0)
	{
		$condition->po_id_in(implode(',',$poIdArr));
	}
	$condition->init();
	$other= new other($condition);
	$other_cost = $other->getAmountArray_by_job();*/
	// $other_cost[$jobNumber]['cm_cost'];
	// echo "<pre>"; print_r($production_data_arr);die();
	$rowspan = array();
	foreach($production_serial_arr as $f_id=>$fname)
	{
		ksort($fname);
		foreach($fname as $sl=>$s_data)
		{			
			foreach($s_data as $l_id=>$ldata)
			{
				foreach ($ldata as $style_key => $style_data) 
				{
					$rowspan[$f_id][$sl][$l_id]++;
				}
			}
		}
	}
	// ======================================
    $avable_min=0; $today_product=0; $floor_name=""; 
	$j=1; $i=1;
	ob_start();
	$line_number_check_arr=array(); $smv_for_item=""; $total_production=array(); $floor_production=array(); $line_floor_production=0; $line_total_production=0; $gnd_total_fob_val=0;  $gnd_final_total_fob_val=0; $f_chk_arr = array(); $line_chk_arr = array(); $line_chk_arr2 = array(); $line_chk_arr3 = array(); $date_data_array = array(); $month_data_array = array(); $line_style_chk_array = array();
    foreach($production_serial_arr as $pr_date=>$date_data)
    {
		foreach($date_data as $f_id=>$fname)
		{
			ksort($fname);
			foreach($fname as $sl=>$s_data)
			{			
				foreach($s_data as $l_id=>$ldata)
				{
					$l=0;
					$pp = 0;
					foreach ($ldata as $style_key => $style_data) 
					{
						// echo $style_key."<br>";
					  	$po_value=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['po_number'];
					 	
						$floor_row++;
						//$item_ids=$production_data_arr[$f_id][$l_id]['item_number_id'];
						$germents_item=array_unique(explode('****',$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['item_number_id']));
						// print_r($germents_item);die();
					
						$buyer_neme_all=array_unique(explode(',',$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['buyer_name']));
						$buyer_name="";
						foreach($buyer_neme_all as $buy)
						{
							if($buyer_name!='') $buyer_name.=',';
							$buyer_name.=$buyerArr[$buy];
						}
						$garment_itemname=''; $active_days=''; $item_smv=""; $item_ids=''; $smv_for_item=""; $produce_minit=""; $order_no_total=""; $efficiency_min=0; $tot_po_qty=0; $fob_val=0; $finishqty=$finishProduceMin=$finishingCm=$finishingFob=0;
						
						foreach($germents_item as $g_val)
						{
							$po_garment_item=explode('**',$g_val);
							if($garment_itemname!='') $garment_itemname.=',';
							$garment_itemname.=$garments_item[$po_garment_item[1]];
							if($item_ids=='') $item_ids=$po_garment_item[1];else $item_ids.=",".$po_garment_item[1];
							if($active_days=="")$active_days=$active_days_arr_powise[$po_garment_item[0]][$po_garment_item[1]];
							else $active_days.=','.$active_days_arr_powise[$po_garment_item[0]][$po_garment_item[1]];
							
							//echo $item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt'].'<br>';
							$tot_po_qty+=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];
							$tot_po_amt+=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt'];
							if($item_smv!='') $item_smv.='/';
							//echo $po_garment_item[0].'='.$po_garment_item[1];
							// $item_smv.=$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
							$item_smv.=$prod_resource_smv_array[$l_id][$style_key][$po_garment_item[1]][$pr_date]['actual_smv'];
							if($order_no_total!="") $order_no_total.=",";
							$order_no_total.=$po_garment_item[0];
							if($smv_for_item!="") $smv_for_item.="****".$po_garment_item[0]."**".$prod_resource_smv_array[$l_id][$style_key][$po_garment_item[1]][$pr_date]['actual_smv'];
							else
							$smv_for_item=$po_garment_item[0]."**".$prod_resource_smv_array[$l_id][$style_key][$po_garment_item[1]][$pr_date]['actual_smv'];	
							$produce_minit+=$production_po_data_arr[$pr_date][$f_id][$l_id][$po_garment_item[0]]*$prod_resource_smv_array[$l_id][$style_key][$po_garment_item[1]][$pr_date]['actual_smv'];
							
							$finishqty+=$production_data_arr_qty[$pr_date][$f_id][$l_id][$po_garment_item[0]][$po_garment_item[1]]['finishqty'];
							
							$finishProduceMin+=$production_data_arr_qty[$pr_date][$f_id][$l_id][$po_garment_item[0]][$po_garment_item[1]]['finishqty']*$prod_resource_smv_array[$l_id][$style_key][$po_garment_item[1]][$pr_date]['actual_smv'];
							
							// echo $production_po_data_arr[$pr_date][$f_id][$l_id][$po_garment_item[0]]."*".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]]."<br>";
							$fob_rate=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt']/$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];
							$prod_qty=$production_data_arr_qty[$f_id][$l_id][$po_garment_item[0]][$po_garment_item[1]]['quantity'];
							//echo $prod_qty.'<br>';
							if(is_nan($fob_rate)){ $fob_rate=0; }
							$fob_val+=$prod_qty*$fob_rate;
							$finishingFob+=$production_data_arr_qty[$pr_date][$f_id][$l_id][$po_garment_item[0]][$po_garment_item[1]]['finishqty']*$fob_rate;
						}
						
						$po_id_arr = array_unique(explode(",", $production_data_arr[$pr_date][$f_id][$l_id][$style_key]['po_id']));
						$po_rate ="";
						foreach ($po_id_arr as $po_val) 
						{
							if($po_rate!="") $po_rate.=",";
							$po_rate.=$po_rate_data_arr[$pr_date][$f_id][$l_id][$po_val]['rate'];
						}
						// echo $po_rate."<br>";


						$subcon_po_id=array_unique(explode(',',$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['order_id']));
						$subcon_order_id="";
						foreach($subcon_po_id as $sub_val)
						{
							$subcon_po_smv=explode(',',$sub_val); 
							if($sub_val!=0)
							{
							if($item_smv!='') $item_smv.='/';
							if($item_smv!='') $item_smv.='/';
							$item_smv.=$subcon_order_smv[$sub_val];
							}
							$produce_minit+=$production_po_data_arr[$pr_date][$f_id][$l_id][$sub_val]*$subcon_order_smv[$sub_val];
							if($subcon_order_id!="") $subcon_order_id.=",";
							$subcon_order_id.=$sub_val;
						}

						//echo $pr_date;die;
						$type_line=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['type_line'];
						$prod_reso_allo=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['prod_reso_allo'];
						$sewing_line='';
						if($production_data_arr[$pr_date][$f_id][$l_id][$style_key]['prod_reso_allo']!="")
						{
							if($production_data_arr[$pr_date][$f_id][$l_id][$style_key]['prod_reso_allo']==1)
							{
								$line_number=explode(",",$prod_reso_arr[$l_id]);
								foreach($line_number as $val)
								{
									// echo $l_id."<br>";
									if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
								}
							}
							else $sewing_line=$lineArr[$l_id];
						}
						else
						{
							// echo $l_id."kakku<br>";
							$line_number=explode(",",$prod_reso_arr[$l_id]);
							foreach($line_number as $val)
							{
								// echo $val."kakku<br>";
								if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
							}
							
						}
						// echo $sewing_line."==".$production_data_arr[$f_id][$l_id][$style_key]['prod_reso_allo']."=kakku<br>";
						// 	die();

						$lunch_start="";
						$lunch_start=$line_number_arr[$l_id][$pr_date]['lunch_start_time'];  
						$lunch_hour=$start_time_arr[$row[1]]['lst']; 
						if($lunch_start!="") 
						{ 
							$lunch_start_hour=$lunch_start; 
						}
						else
						{
							$lunch_start_hour=$lunch_hour; 
						}
						  
						$production_hour=array();
						$prod_hours = 0;
						for($h=$hour;$h<=$last_hour;$h++)
						{
							 $prod_hour="prod_hour".substr($line_start_hour_arr[$h],0,2).""; 
							 $production_hour[$prod_hour]=$production_data_arr[$pr_date][$f_id][$l_id][$style_key][$prod_hour];
							 $floor_production[$prod_hour]+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key][$prod_hour];
							 $total_production[$prod_hour]+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key][$prod_hour];
							 if($production_data_arr[$pr_date][$f_id][$l_id][$style_key][$prod_hour]>0)
							 {
								$prod_hours++;
							 }
						}	
						
						
						$floor_production['prod_hour24']+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['prod_hour23'];
						$total_production['prod_hour24']+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['prod_hour23'];
						$production_hour['prod_hour24']=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['prod_hour23']; 
						$line_production_hour=0;
						// echo str_replace("'","",$actual_production_date).">".str_replace("'","",$actual_date)."<br>";
						if(str_replace("'","",$actual_production_date)>str_replace("'","",$actual_date)) 
						{
							if($type_line==2) //No Profuction Line
							{
								$line_start=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['prod_start_time'];
							}
							else
							{
								$line_start=$line_number_arr[$l_id][$pr_date]['prod_start_time'];
							}
							if($line_start!="") 
							{ 
								$line_start_hour=substr($line_start,0,2); 
								if(substr($line_start_hour,0,1)==0)  $line_start_hour=substr($line_start_hour,1,1);	
							}
							else
							{
								$line_start_hour=$hour; 
							}
							$actual_time_hour=0;
							$total_eff_hour=0;
							for($lh=$line_start_hour;$lh<=$last_hour;$lh++)
							{
								$bg=$start_hour_arr[$lh];
								if($lh<$actual_time)
								{
									$total_eff_hour=$total_eff_hour+1;;	
									$line_hour="prod_hour".substr($bg,0,2)."";
									$line_production_hour+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key][$line_hour];
									$line_floor_production+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key][$line_hour];
									$line_total_production+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key][$line_hour];
									$actual_time_hour=$start_hour_arr[$lh+1];
								}
							}
							if($start_hour_arr[$actual_time]>$lunch_start_hour) $total_eff_hour=$total_eff_hour-1;
							
							if($type_line==2)
							{
								if($total_eff_hour>$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['working_hour'])
								{
									 $total_eff_hour=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['working_hour'];
								}
							}
							else
							{
								if($line_wise_style_count_arr[$pr_date][$f_id][$l_id]>1)
								{
									if($total_eff_hour>$prod_resource_array2[$l_id][$style_key][$pr_date]['working_hour'])
									{
										$total_eff_hour=$prod_resource_array2[$l_id][$style_key][$pr_date]['working_hour'];
									}
								}
								else
								{
									if($total_eff_hour>$prod_resource_array[$l_id][$pr_date]['working_hour'])
									{
										$total_eff_hour=$prod_resource_array[$l_id][$pr_date]['working_hour'];
									}
								}
							}
						}
						
						if(str_replace("'","",$actual_production_date)<=str_replace("'","",$actual_date)) 
						{
							for($ah=$hour;$ah<=$last_hour;$ah++)
							{
								$prod_hour="prod_hour".substr($start_hour_arr[$ah],0,2).""; 
								$line_production_hour+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key][$prod_hour];
								$line_floor_production+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key][$prod_hour];
								$line_total_production+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key][$prod_hour];
							}
							if($type_line==2)
							{
								$total_eff_hour=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['working_hour'];
							}
							else
							{
								if($line_wise_style_count_arr[$pr_date][$f_id][$l_id]>1)
								{
									$total_eff_hour=$prod_resource_array2[$l_id][$style_key][$pr_date]['working_hour'];
									// echo $total_eff_hour."=<br>";
								}
								else
								{
									$total_eff_hour=$prod_resource_array[$l_id][$pr_date]['working_hour'];
								}
							}
						}
						/*if($sewing_day!="")
						{
							$days_active= $active_days_arr[$f_id][$l_id];
							// $days_run=datediff("d",$sewing_day,$pr_date);
							$date1=date_create($sewing_day);
							$date2=date_create($pr_date);
							$diff=date_diff($date1,$date2);
							$days_run = $diff->format("%d");
						}
						else  
						{
							$days_run=0; 
							$days_active=0;
						}*/
						$days_run=0;
						$days_run= $active_days_arr[$f_id][$l_id];
						/*if($sewing_day!="")
						{
							// $days_run= $diff=datediff("d",$sewing_day,$pr_date);
							$days_active= $active_days_arr[$f_id][$l_id];
						}
						else 
						{
							 // $days_run=0;
							 $days_active=0;
						}*/ 
						 
						$current_wo_time=0;
						if($current_date==$search_prod_date)
						{
							$prod_wo_hour=$total_eff_hour;
							
							if ($dif_time<$prod_wo_hour)//
							{
								$current_wo_time=$dif_hour_min;
								$cla_cur_time=$dif_time;
							}
							else
							{
								$current_wo_time=$prod_wo_hour;
								$cla_cur_time=$prod_wo_hour;
							}
						}
						else
						{
							$current_wo_time=$total_eff_hour;
							$cla_cur_time=$total_eff_hour;
						}
						$total_adjustment=0;
						if($type_line==2) //No Production Line
						{
							$smv_adjustmet_type=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['smv_adjust_type'];
							$eff_target=($production_data_arr[$pr_date][$f_id][$l_id][$style_key]['terget_hour']*$total_eff_hour);

							if($total_eff_hour>=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['working_hour'])
							{
								if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['smv_adjust'];
								if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($production_data_arr[$pr_date][$f_id][$l_id][$style_key]['smv_adjust'])*(-1);
							}
							$efficiency_min+=$total_adjustment+($production_data_arr[$pr_date][$f_id][$l_id][$style_key]['man_power'])*$cla_cur_time*60;
							$extra_minute_production_arr=$efficiency_min+$extra_minute_arr[$f_id][$l_id];
							
							$line_efficiency=(($produce_minit)*100)/$efficiency_min;
						}
						else
						{
							if($line_wise_style_count_arr[$pr_date][$f_id][$l_id]>1)
							{
								$smv_adjustmet_type=$prod_resource_array2[$l_id][$style_key][$pr_date]['smv_adjust_type'];
								$eff_target=($prod_resource_array2[$l_id][$style_key][$pr_date]['terget_hour']*$total_eff_hour);
								// echo $l_id."=(".$prod_resource_array2[$l_id][$style_key][$pr_date]['terget_hour']."*".$total_eff_hour.")<br>";
								if($total_eff_hour>=$prod_resource_array2[$l_id][$style_key][$pr_date]['working_hour'])
								{
									if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array2[$l_id][$style_key][$pr_date]['smv_adjust'];
									if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array2[$l_id][$style_key][$pr_date]['smv_adjust'])*(-1);
								}						
								
								// $efficiency_min+=$total_adjustment+($prod_resource_array2[$l_id][$style_key][$pr_date]['man_power'])*$cla_cur_time*60;
								$efficiency_min+=($prod_resource_array2[$l_id][$style_key][$pr_date]['man_power'])*$cla_cur_time*60;
								// echo "$style_key=$pr_date=".$prod_resource_array2[$l_id][$style_key][$pr_date]['man_power']."*".$cla_cur_time."*60<br>";
								$extra_minute_resource_arr=$efficiency_min+$extra_minute_arr[$l_id][$pr_date];
								
								$line_efficiency=(($produce_minit)*100)/$efficiency_min;
							}
							else
							{
								$smv_adjustmet_type=$prod_resource_array[$l_id][$pr_date]['smv_adjust_type'];
								$eff_target=($prod_resource_array[$l_id][$pr_date]['terget_hour']*$total_eff_hour);
								// echo $l_id."=".$prod_resource_array[$l_id][$pr_date]['terget_hour']."*".$total_eff_hour."<br>";
								if($total_eff_hour>=$prod_resource_array[$l_id][$pr_date]['working_hour'])
								{
									if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array[$l_id][$pr_date]['smv_adjust'];
									if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array[$l_id][$pr_date]['smv_adjust'])*(-1);
								}						
								
								// $efficiency_min+=$total_adjustment+($prod_resource_array[$l_id][$pr_date]['man_power'])*$cla_cur_time*60;
								$efficiency_min+=($prod_resource_array[$l_id][$pr_date]['man_power'])*$cla_cur_time*60;
								// echo "$efficiency_min=$l_id=$style_key=$pr_date=".$prod_resource_array[$l_id][$pr_date]['man_power']."*".$cla_cur_time."*60<br>";
								$extra_minute_resource_arr=$efficiency_min+$extra_minute_arr[$l_id][$pr_date];
								
								$line_efficiency=(($produce_minit)*100)/$efficiency_min;
							}
						
							
						}

						// adjustment extra hour when multiple style running in a single line =========================
						$txtDate = date('d-M-Y',strtotime($pr_date));

						// $extra_hr = $prod_resource_smv_adj_array[$l_id][$pr_date][1]['adjust_hour']."<br>";
						// echo $pr_date."==".$line_wise_style_count_arr2[$pr_date][$f_id][$l_id]."<br>";
						if($line_wise_style_count_arr[$pr_date][$f_id][$l_id]>1)
						{
							$mn_power = $prod_resource_smv_adj_array[$l_id][$txtDate][1]['number_of_emp'];
							if($line_wise_style_count_arr2[$pr_date][$f_id][$l_id]>1)
							{
								$late_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][5]['total_smv'];

								
								if($pp==0)
								{
									$efficiency_min -= $late_hr;
									$pp++;
								}
								$line_wise_style_count_arr2[$pr_date][$f_id][$l_id]--;
							}
							else
							{
								$extra_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][1]['total_smv'];
								$lunch_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][2]['total_smv'];
								$sick_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][3]['total_smv'];
								$leave_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][4]['total_smv'];
								$adjust_hr = $extra_hr - ($lunch_hr+$sick_hr+$leave_hr);

								$efficiency_min += $adjust_hr;
							}
						}
						else // for single line
						{
							$extra_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][1]['total_smv'];
							$lunch_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][2]['total_smv'];
							$sick_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][3]['total_smv'];
							$leave_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][4]['total_smv'];
							$adjust_hr = $extra_hr - ($lunch_hr+$sick_hr+$leave_hr);

							$efficiency_min += $adjust_hr;
							// echo $efficiency_min."=".$l_id."=".$style_key."=".$txtDate."=".$extra_hr."- (".$lunch_hr."+".$sick_hr."+".$leave_hr.")<br>";
						}

						/*if($line_wise_style_count_arr[$pr_date][$f_id][$l_id]>1)
						{
							$efficiency_min+=($prod_resource_array2[$l_id][$style_key][$pr_date]['man_power'])*$cla_cur_time*60;
							echo "$efficiency_min=$l_id=$style_key=$pr_date=".$prod_resource_array2[$l_id][$style_key][$pr_date]['man_power']."*".$cla_cur_time."*60<br>";
						}
						else
						{
							$efficiency_min+=($prod_resource_array[$l_id][$pr_date]['man_power'])*$cla_cur_time*60;
							// echo "string".$total_adjustment."+(".$prod_resource_array[$l_id][$pr_date]['man_power'].")*".$cla_cur_time."*60<br>";
						}*/

						
						$po_id=rtrim($production_data_arr[$pr_date][$f_id][$l_id][$style_key]['po_id'],',');
						$po_id=array_unique(explode(",",$po_id));

						$style=rtrim($style_chane_arr[$pr_date][$f_id][$l_id]['style']);
						$style=implode("##",array_unique(explode("##",$style)));

						$job_arr = array_unique(explode(",", rtrim($production_data_arr[$pr_date][$f_id][$l_id][$style_key]['job_no'])));
						$job__no = '';
						foreach ($job_arr as $jobvalue) 
						{
							$job__no .= ($job__no=="") ? $jobvalue : ",".$jobvalue;
						}
						
						$cbo_get_upto=str_replace("'","",$cbo_get_upto);
						$txt_parcentage=str_replace("'","",$txt_parcentage);
					  
						$floor_name=$floorArr[$f_id];	
						$floor_smv+=$item_smv;
						
						$floor_days_run+=$days_run;
						$total_days_run+=$days_run;
						$floor_line_days_run+=$line_wise_days_run[$f_id][$l_id];
						$floor_days_active+=$days_active;				
						
						$po_id=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['po_id'];//$item_ids//$subcon_order_id
						$styles=explode("##",$style);
						// print_r($styles);die();
						$style_button='';//
						$style_name ='';
						$style_change = 0;
						$kk=0;
						foreach($styles as $sid)
						{
							if($kk!=0)
							{
								if($line_chk_array[$pr_date][$f_id][$l_id]=="")
								{
									$style_change++; // first style will 0, 2nd style will 1
									$line_chk_array[$pr_date][$f_id][$l_id]="kakku"	;							
								}
							}
							$kk++;
						}
						$as_on_current_hour_target=0; $as_on_current_hour_variance=0;
						$as_on_current_hour_target=$terget_hour*$cla_cur_time;
						$as_on_current_hour_variance=$line_production_hour-$as_on_current_hour_target;
						
						$line_acv = ($eff_target>0) ? (($line_production_hour/$eff_target)*100) : 0; 
						$target_gap = $line_production_hour - $eff_target; 
						$item_smv_ex = explode("/", $item_smv);
						$counter = 0;
						$tot_smv = 0;
						foreach ($item_smv_ex as $val) 
						{
							$tot_smv += $val;
							$counter++;
						}
						$avg_smv = $tot_smv/$counter;
						$target_min = $eff_target * $avg_smv; 
						// echo $eff_target ."*". $avg_smv."=kakku<br>";
						$target_effi = ($efficiency_min>0) ? ($target_min / $efficiency_min)*100 : 0; 
						$achive_effi = ($efficiency_min>0) ? ($produce_minit / $efficiency_min)*100 : 0; 
						$efficiency_gap = $target_effi - $achive_effi;
						// $ttl_fob_val = $line_production_hour;
						$po_rate_arr = explode(",", $po_rate);
						$tot_po_rate = 0;
						$rate_counter = 0;
						foreach ($po_rate_arr as $value) 
						{
							$tot_po_rate +=$value;
							$rate_counter++;
						}
						$avg_rate = $tot_po_rate/$rate_counter;
						$ttl_fob_val = $line_production_hour*$avg_rate;
						// echo $line_production_hour."*".$avg_rate."<br>";
						$target_value_fob = $eff_target*$avg_rate;

						$joNos_arr = array_unique(explode(",", $production_data_arr[$pr_date][$f_id][$l_id][$style_key]['job_no']));
						//print_r($joNos_arr);die;
						$tot_cm = 0;
						$cm_counter = 0;
						$dzn_qnty = 0;
						foreach ($joNos_arr as $jobNo) 
						{
							$costing_per=$costing_per_arr[$jobNo];
							if($costing_per==1) $dzn_qnty=12;
							else if($costing_per==3) $dzn_qnty=12*2;
							else if($costing_per==4) $dzn_qnty=12*3;
							else if($costing_per==5) $dzn_qnty=12*4;
							else $dzn_qnty=1;

							// $tot_cm += $other_cost[$jobNo]['cm_cost']/$dzn_qnty;

							$tot_cm = $tot_cm_arr[$jobNo];
							// $tot_cm=return_field_value("CM_COST","wo_pre_cost_dtls","job_no='$jobNo' and is_deleted=0 and status_active=1");
							$cm_counter++;
						}				
						$avg_cm = ($tot_cm/$dzn_qnty)/$cm_counter;
						$ttl_cm = $line_production_hour*$avg_cm;
						$target_cm = $eff_target*$avg_cm;
						
						$finishingCm=$finishqty*$avg_cm;

						// echo $ttl_cm."=".$l_id."=".$style_key."=".$txtDate."=".$tot_cm."*".$dzn_qnty."=".$cm_counter.")<br>";
						if($type_line==2) //No Production Line
						{
							$man_power=$production_data_arr[$f_id][$l_id][$style_key]['man_power'];
							$operator=$production_data_arr[$f_id][$l_id][$style_key]['operator'];
							$helper=$production_data_arr[$f_id][$l_id][$style_key]['helper'];
							$terget_hour=$production_data_arr[$f_id][$l_id][$style_key]['target_hour'];	
							$capacity=$production_data_arr[$f_id][$l_id][$style_key]['capacity'];
							$working_hour=$production_data_arr[$f_id][$l_id][$style_key]['working_hour']; 
							
							$floor_working_hour+=$production_data_arr[$f_id][$l_id][$style_key]['working_hour']; 
							$eff_target_floor+=$eff_target;
							$floor_today_product+=$today_product;
							$floor_avale_minute+=$efficiency_min;
							$floor_produc_min+=$produce_minit; 
							$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
							$floor_capacity+=$production_data_arr[$f_id][$l_id][$style_key]['capacity'];
							$floor_helper+=$production_data_arr[$l_id][$pr_da[$style]]['helper'];
							$floor_man_power+=$production_data_arr[$f_id][$l_id][$style_key]['man_power'];
							$floor_operator+=$production_data_arr[$f_id][$l_id][$style_key]['operator'];
							$total_operator+=$production_data_arr[$f_id][$l_id][$style_key]['operator'];
							$total_man_power+=$production_data_arr[$f_id][$l_id][$style_key]['man_power'];	
							$total_helper+=$production_data_arr[$f_id][$l_id][$style_key]['helper'];
							$total_capacity+=$production_data_arr[$f_id][$l_id][$style_key]['capacity'];
							$floor_tgt_h+=$production_data_arr[$f_id][$l_id][$style_key]['target_hour'];
							$total_working_hour+=$production_data_arr[$f_id][$l_id][$style_key]['working_hour']; 
							$gnd_total_tgt_h+=$production_data_arr[$f_id][$l_id][$style_key]['target_hour'];
							$total_terget+=$eff_target;	
							$grand_total_product+=$today_product;
							$gnd_avable_min+=$efficiency_min;
							$gnd_product_min+=$produce_minit;
							
							$gnd_total_fob_val+=$fob_val; 
							$gnd_final_total_fob_val+=$fob_val;
						}
						else
						{
							if(!in_array($l_id, $line_chk_arr10))
							{
								if($line_wise_style_count_arr[$pr_date][$f_id][$l_id]>1) // when multiple style run in single line
								{									
									$operator=$prod_resource_array2[$l_id][$style_key][$pr_date]['operator'];
									$helper=$prod_resource_array2[$l_id][$style_key][$pr_date]['helper'];
									$terget_hour=$prod_resource_array2[$l_id][$style_key][$pr_date]['terget_hour'];
									$working_hour=$prod_resource_array2[$l_id][$style_key][$pr_date]['working_hour'];
									$man_power=$operator + $helper;											
								}
								else
								{
									$operator=$prod_resource_array[$l_id][$pr_date]['operator'];
									$helper=$prod_resource_array[$l_id][$pr_date]['helper'];
									$terget_hour=$prod_resource_array[$l_id][$pr_date]['terget_hour'];
									$working_hour=$prod_resource_array[$l_id][$pr_date]['working_hour'];
									$man_power=$prod_resource_array[$l_id][$pr_date]['man_power'];	
								}
								$line_chk_arr10[$pr_date][$l_id] = $l_id;
								// echo $l_id."=".$style_key."=".$pr_date."=".$operator ."+". $helper."<br>";
							}
							

							// $man_power=$prod_resource_array[$l_id][$pr_date]['man_power'];	
							$capacity=$prod_resource_array[$l_id][$pr_date]['capacity'];
							// ======================================================
							$floor_capacity+=$prod_resource_array[$l_id][$pr_date]['capacity'];
							

							/*if($line_wise_style_count_arr[$f_id][$l_id]>1)
							{
								$floor_operator+=$prod_resource_array2[$l_id][$style_key][$pr_date]['operator'];
								$floor_helper+=$prod_resource_array2[$l_id][$style_key][$pr_date]['helper'];
								$floor_tgt_h+=$prod_resource_array2[$l_id][$style_key][$pr_date]['terget_hour'];	
								$floor_working_hour+=$prod_resource_array2[$l_id][$style_key][$pr_date]['working_hour'];
								$floor_man_power+=$prod_resource_array2[$l_id][$style_key][$pr_date]['operator']+$prod_resource_array2[$l_id][$style_key][$pr_date]['helper'];
							}
							else
							{
								$floor_operator+=$prod_resource_array[$l_id][$pr_date]['operator'];
								$floor_helper+=$prod_resource_array[$l_id][$pr_date]['helper'];
								$floor_tgt_h+=$prod_resource_array[$l_id][$pr_date]['terget_hour'];	
								$floor_working_hour+=$prod_resource_array[$l_id][$pr_date]['working_hour'];
								$floor_man_power+=$prod_resource_array[$l_id][$pr_date]['man_power'];
							}*/

							if($line_chk_arr[$l_id][$pr_date]=="")
							{
								if($line_wise_style_count_arr[$pr_date][$f_id][$l_id]>1)
								{
									$floor_operator+=$prod_resource_array2[$l_id][$style_key][$pr_date]['operator'];
									$floor_helper+=$prod_resource_array2[$l_id][$style_key][$pr_date]['helper'];
									$floor_tgt_h+=$prod_resource_array2[$l_id][$style_key][$pr_date]['terget_hour'];	
									$floor_working_hour+=$prod_resource_array2[$l_id][$style_key][$pr_date]['working_hour'];
									$floor_man_power+=$prod_resource_array2[$l_id][$style_key][$pr_date]['operator']+$prod_resource_array2[$l_id][$style_key][$pr_date]['helper'];
								}
								else
								{
									$floor_operator+=$prod_resource_array[$l_id][$pr_date]['operator'];
									$floor_helper+=$prod_resource_array[$l_id][$pr_date]['helper'];
									$floor_tgt_h+=$prod_resource_array[$l_id][$pr_date]['terget_hour'];	
									$floor_working_hour+=$prod_resource_array[$l_id][$pr_date]['working_hour'];
									$floor_man_power+=$prod_resource_array[$l_id][$pr_date]['man_power'];
								}
								$line_chk_arr[$l_id][$pr_date] = "kakku";
							}


							$floor_prod_hour += $prod_hours;
							$floor_tot_prod += $line_production_hour;
							$floor_line_acv += $line_acv;
							$floor_target_gap += $target_gap;
							$floor_target_min += $target_min;
							$floor_target_effi += $target_effi;
							$floor_achive_effi += $achive_effi;
							$floor_efficiency_gap += $efficiency_gap;
							$floor_style_change += $style_change;
							$floor_avg_cm += $avg_cm;
							$floor_ttl_cm += $ttl_cm;
							$floor_target_cm += $target_cm;
							$floor_avg_rate += $avg_rate;
							$floor_ttl_fob_val += $ttl_fob_val;
							$floor_target_value_fob += $target_value_fob;

							$total_prod_hour += $prod_hours;
							$total_tot_prod += $line_production_hour;
							$total_line_acv += $line_acv;
							$total_target_gap += $target_gap;
							$total_target_min += $target_min;
							$total_target_effi += $target_effi;
							$total_achive_effi += $achive_effi;
							$total_efficiency_gap += $efficiency_gap;
							$total_style_change += $style_change;
							$total_avg_cm += $avg_cm;
							$total_ttl_cm += $ttl_cm;
							$total_target_cm += $target_cm;
							$total_avg_rate += $avg_rate;
							$total_ttl_fob_val += $ttl_fob_val;
							$total_target_value_fob += $target_value_fob;


							$eff_target_floor+=$eff_target;
							$floor_today_product+=$today_product;
							$floor_avale_minute+=$efficiency_min; 
							$floor_produc_min+=$produce_minit; 
							$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
							if($line_chk_arr2[$l_id][$pr_date]=="")
							{
								if($line_wise_style_count_arr[$pr_date][$f_id][$l_id]>1)
								{
									$total_operator+=$prod_resource_array2[$l_id][$style_key][$pr_date]['operator'];
									$total_working_hour+=$prod_resource_array2[$l_id][$style_key][$pr_date]['working_hour']; 
									$gnd_total_tgt_h+=$prod_resource_array2[$l_id][$style_key][$pr_date]['terget_hour'];
									$total_helper+=$prod_resource_array2[$l_id][$style_key][$pr_date]['helper'];
									$total_man_power += $prod_resource_array2[$l_id][$style_key][$pr_date]['operator'] + $prod_resource_array2[$l_id][$style_key][$pr_date]['helper'];
								}
								else
								{
									$total_operator+=$prod_resource_array[$l_id][$pr_date]['operator'];
									$total_working_hour+=$prod_resource_array[$l_id][$pr_date]['working_hour']; 
									$gnd_total_tgt_h+=$prod_resource_array[$l_id][$pr_date]['terget_hour'];
									$total_helper+=$prod_resource_array[$l_id][$pr_date]['helper'];
									$total_man_power+=$prod_resource_array[$l_id][$pr_date]['man_power'];
								}
								$line_chk_arr2[$l_id][$pr_date] = "kakku";
							}

							// $total_man_power+=$prod_resource_array[$l_id][$pr_date]['man_power'];
							$total_capacity+=$prod_resource_array[$l_id][$pr_date]['capacity'];
							$total_terget+=$eff_target;
							$grand_total_product+=$today_product;
							$gnd_avable_min+=$efficiency_min;
							$gnd_product_min+=$produce_minit;
							$gnd_total_fob_val+=$fob_val;
							$gnd_final_total_fob_val+=$fob_val; 
							
						} 
						// echo $pr_date."=$l_id=".$eff_target."<br>"	;
						$date_data_array[$pr_date]['target'] += $eff_target;//$terget_hour;
						$date_data_array[$pr_date]['production'] += $line_production_hour;
						$date_data_array[$pr_date]['man_power'] += $man_power;
						$date_data_array[$pr_date]['prod_hours'] += $prod_hours;
						$date_data_array[$pr_date]['available_minit'] += $efficiency_min;
						$date_data_array[$pr_date]['target_min'] += $target_min;
						$date_data_array[$pr_date]['produce_minit'] += $produce_minit;

						// $date_data_array[$pr_date]['nptmin'] += $npt_date_data_array[$pr_date];

						$date_data_array[$pr_date]['style_change'] += $style_change;
						$date_data_array[$pr_date]['target_effi'] += $target_effi;
						$date_data_array[$pr_date]['achive_effi'] += $achive_effi;
						$date_data_array[$pr_date]['cm_earn'] += $ttl_cm;
						$date_data_array[$pr_date]['fob_earn'] += $ttl_fob_val;
						
						$date_data_array[$pr_date]['finishqty'] += $finishqty;
						$date_data_array[$pr_date]['finishproducemint'] += $finishProduceMin;
						$date_data_array[$pr_date]['finishcm'] += $finishingCm;
						$date_data_array[$pr_date]['finishfob'] += $finishingFob;
						
						$date_data_array[$pr_date]['remarks'] .= $production_data_arr[$f_id][$l_id][$style_key]['remarks'].', ';

						if($line_chk_arr3[$pr_date][$l_id]=="")
						{
							$date_data_array[$pr_date]['tot_line']++;
							$month_data_array[date('M-Y',strtotime($pr_date))]['tot_line']++;
							$line_chk_arr3[$pr_date][$l_id] = $l_id;
						}

						$month_data_array[date('M-Y',strtotime($pr_date))]['target'] += $eff_target;//$eff_target;
						$month_data_array[date('M-Y',strtotime($pr_date))]['production'] += $line_production_hour;
						$month_data_array[date('M-Y',strtotime($pr_date))]['man_power'] += $man_power;
						$month_data_array[date('M-Y',strtotime($pr_date))]['prod_hours'] += $prod_hours;
						$month_data_array[date('M-Y',strtotime($pr_date))]['available_minit'] += $efficiency_min;
						$month_data_array[date('M-Y',strtotime($pr_date))]['target_min'] += $target_min;
						$month_data_array[date('M-Y',strtotime($pr_date))]['produce_minit'] += $produce_minit;

						// $month_data_array[date('M-Y',strtotime($pr_date))]['nptmin'] += $npt_date_data_array[$pr_date];

						$month_data_array[date('M-Y',strtotime($pr_date))]['style_change'] += $style_change;
						$month_data_array[date('M-Y',strtotime($pr_date))]['target_effi'] += $target_effi;
						$month_data_array[date('M-Y',strtotime($pr_date))]['achive_effi'] += $achive_effi;
						$month_data_array[date('M-Y',strtotime($pr_date))]['cm_earn'] += $ttl_cm;
						$month_data_array[date('M-Y',strtotime($pr_date))]['fob_earn'] += $ttl_fob_val;
						
						$month_data_array[date('M-Y',strtotime($pr_date))]['finishqty'] += $finishqty;
						$month_data_array[date('M-Y',strtotime($pr_date))]['finishproducemint'] += $finishProduceMin;
						$month_data_array[date('M-Y',strtotime($pr_date))]['finishcm'] += $finishingCm;
						$month_data_array[date('M-Y',strtotime($pr_date))]['finishfob'] += $finishingFob;
						
						$month_data_array[date('M-Y',strtotime($pr_date))]['remarks'] .= $production_data_arr[$f_id][$l_id][$style_key]['remarks'].', ';
					}
				}
			}
		}
	}
	
	// echo "<pre>";print_r($date_data_array);die();

	$date_from = str_replace("'", "", $start_date);
	$date_to = str_replace("'", "", $txt_date_to);
	function get_date_range($first, $last, $step = '+1 day', $output_format = 'd-M-y' ) 
	{
	    $dates = array();
	    $current = strtotime($first);
	    $last = strtotime($last);

	    while( $current <= $last ) {
	        $dates[] = strtoupper(date($output_format, $current));
	        $current = strtotime($step, $current);
	    }
	    return $dates;
	}
	$date_range_arr = get_date_range($date_from,$date_to); 
	// echo "<pre>";print_r($date_range_arr);die();

	$smv_for_item=""; $tbl_width = 1290; $colspan = 21;
   /* $endtime = microtime(true);
	$timediff = $endtime - $starttime;
	echo "Elapsed time : ".secondsToTime(round($timediff));die();*/
	?>
    
	<fieldset style="width:<? echo $tbl_width+20;?>px; margin: 0 auto;">
		<div class="details-part">
	       <table width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0"> 
	            <tr class="form_caption">
	                <td colspan="<? echo $colspan;?>" align="center"><strong><? echo $report_title; ?> &nbsp;</strong></td> 
	            </tr>
	            <tr class="form_caption">
	                <td colspan="<? echo $colspan;?>" align="center"><strong><? echo $companyArr[$company_id]; ?></strong></td> 
	            </tr>
	            <tr class="form_caption">
	                <td colspan="<? echo $colspan;?>" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date_from)) )." To ".change_date_format( str_replace("'","",trim($txt_date_to)) ); ?></strong></td> 
	            </tr>
	        </table>
	       <!-- ======================== Details part ============================= -->    
	        <table id="table_header_1" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
	        	<caption>Date Wise Summary</caption>
	            <thead>
	                <tr>
	                    <th width="60"><p>Date</p></th>
	                    <th width="80"><p>Target</p></th>
	                    <th width="80"><p>Production</p></th>
	                    <th width="80"><p>Variance(Pcs)</p></th>
	                    <th width="60"><p>Achieve%</p></th>
	                    <th width="60"><p>Manpower</p></th>
	                    <th width="40"><p>WH</p></th>
	                    <th width="80"><p>Available/<br>Input Min.</p></th>
	                    <th width="80"><p>Target in<br> Min.</p></th>
	                    <th width="80"><p>Produce/<br> Output M<br>in.</p></th>
	                    <th width="50"><p>NPT Min.</p></th>
	                    <th width="50"><p>Style Cha<br>nge</p></th>
	                    <th width="50"><p>Target Ef<br>f%</p></th>
	                    <th width="50"><p>Achieve<br> Eff%</p></th>
	                    <th width="50"><p>CM Earn</p></th>                    
	                    <th width="50"><p>FOB Earn</p></th>
                        <th width="50"><p>Finishing<br> Qty.</p></th>
                        <th width="50"><p>Finishing<br> Produce<br> Min. </p></th>
                        <th width="50"><p>Finishing <br>CM</p></th>
                        <th width="50"><p>Finishing<br> FOB</p></th>                   
	                    <th ><p>Remarks</p></th> 
	                </tr>
	            </thead>
	        </table>
	        <div style="width:<?= $tbl_width+20;?>px; max-height:300px; overflow-y:scroll" id="scroll_body">
	            <table class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
	                <tbody>
	                	<?
	                	$i=1;
	                	$total_target 			= 0;
				 		$total_production 		= 0;
				  		$total_varience 		= 0;
				    	$total_achive 			= 0;
				 		$total_man_power 		= 0;
						$total_prod_hours 		= 0;
						$total_available_minit 	= 0;
				 		$total_target_min 		= 0;
						$total_produce_minit 	= 0;
						$total_npt_minit 		= 0;
						$total_style_change 	= 0;
						$total_target_effi 		= 0;
						$total_achive_effi 		= 0;
				   		$total_cm_earn 			= 0;
				  		$total_fob_earn 		= 0;
						$total_finish_qty=$total_finish_prod_min=$total_finish_cm=$total_finish_fob=0;
	                	foreach ($date_range_arr as $date_key => $date_val) 
	                	{
	                		$bgcolor = ($i%2==0) ? "#ffffff" : "#f6faff";
	                		$day = date('l', strtotime($date_val));
	                		if($day=='Friday')
	                		{
	                			$bgcolor = "#ff0000";
	                		}
	                		$target 		= $date_data_array[$date_val]['target'];
	                		$production 	= $date_data_array[$date_val]['production'];
	                		$varience 		= $target - $production;
	                		$achive 		= ($target>0) ? ($production/$target)*100 : 0;
	                		$man_power 		= $date_data_array[$date_val]['man_power'];
	                		$prod_hours 	= $date_data_array[$date_val]['prod_hours'];
	                		$available_minit= $date_data_array[$date_val]['available_minit'];
	                		$target_min 	= $date_data_array[$date_val]['target_min'];
	                		$produce_minit 	= $date_data_array[$date_val]['produce_minit'];
	                		$npt_minit 		= $npt_date_data_array[$date_val]['idle_min'];
	                		$remarks 		= $npt_date_data_array[$date_val]['remarks'];
	                		$style_change 	= $date_data_array[$date_val]['style_change'];
							
	                		// $target_effi 	= $date_data_array[$date_val]['target_effi'];
	                		// $achive_effi 	= $date_data_array[$date_val]['achive_effi'];
	                		$cm_earn 		= $date_data_array[$date_val]['cm_earn'];
	                		$fob_earn 		= $date_data_array[$date_val]['fob_earn'];
							
							$finish_qty		= $date_data_array[$date_val]['finishqty'];
							$finish_prod_min= $date_data_array[$date_val]['finishproducemint'];
							$finish_cm 		= $date_data_array[$date_val]['finishcm'];
							$finish_fob		= $date_data_array[$date_val]['finishfob'];
							
	                		$tot_line 		= $date_data_array[$date_val]['tot_line'];

	                		$avg_prod_hours = $prod_hours/$tot_line;
	                		// echo $prod_hours."/".$tot_line."<br>";

	                		$target_effi = ($available_minit>0) ? ($target_min / $available_minit)*100 : 0; 
							$achive_effi = ($available_minit>0) ? ($produce_minit / $available_minit)*100 : 0; 

		                	?>
		                   <tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
			                    <td width="60"><? echo date('d-M',strtotime($date_val));?></td>
			                    <td align="right" width="80"><p><? echo number_format($target,0);?></p></td>
			                    <td align="right" width="80"><p><? echo $production;?></p></td>
			                    <td align="right" width="80"><p><? echo number_format($varience);?></p></td>
			                    <td align="right" width="60"><p><? echo number_format($achive,2);?></p></td>
			                    <td align="right" width="60"><p><? echo number_format($man_power,0);?></p></td>
			                    <td align="right" width="40"><p><? echo number_format($avg_prod_hours,0);?></p></td>
			                    <td align="right" width="80"><p><? echo number_format($available_minit,0);?></p></td>
			                    <td align="right" width="80"><p><? echo number_format($target_min,0);?></p></td>
			                    <td align="right" width="80"><p><? echo number_format($produce_minit,0);?></p></td>
			                    <td align="right" width="50"><p><? echo number_format($npt_minit,0);?></p></td>
			                    <td align="right" width="50"><p><? echo number_format($style_change,0);?></p></td>
			                    <td align="right" width="50"><p><? echo number_format($target_effi,0);?>%</p></td>
			                    <td align="right" width="50"><p><? echo number_format($achive_effi,0);?>%</p></td>
			                    <td align="right" width="50"><p><? echo number_format($cm_earn,0);?></p></td>                    
			                    <td align="right" width="50"><p><? echo number_format($fob_earn,0);?></p></td> 
                                  
                                <td align="right" width="50"><p><? echo number_format($finish_qty,0);?></p></td> 
                                <td align="right" width="50"><p><? echo number_format($finish_prod_min,0);?></p></td> 
                                <td align="right" width="50"><p><? echo number_format($finish_cm,0);?></p></td> 
                                <td align="right" width="50"><p><? echo number_format($finish_fob,0);?></p></td>                 
			                    <td ><p><? echo implode(", ",array_unique(array_filter(explode("**", $remarks))));?></p></td> 					    
		                    </tr>
		                    <?
		                    $i++;
		                    $total_target 			+= $target;
					 		$total_production 		+= $production;
					  		$total_varience 		+= $varience;
					    	$total_achive 			+= $achive;
					 		$total_man_power 		+= $man_power;
							$total_prod_hours 		+= $avg_prod_hours;
							$total_available_minit 	+= $available_minit;
					 		$total_target_min 		+= $target_min;
							$total_produce_minit 	+= $produce_minit;
							$total_npt_minit 		+= $npt_date_data_array[$date_val]['idle_min'];
							$total_style_change 	+= $style_change;
							$total_target_effi 		+= $target_effi;
							$total_achive_effi 		+= $achive_effi;
					   		$total_cm_earn 			+= $cm_earn;
					  		$total_fob_earn 		+= $fob_earn;
							$total_finish_qty		+=$finish_qty;
							$total_finish_prod_min	+=$finish_prod_min;
							$total_finish_cm		+=$finish_cm;
							$total_finish_fob		+=$finish_fob;
		                }
		                ?>
	                </tbody>
	            </table>
			</div>
			<table class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                <tfoot>
                   <tr>
	                    <th width="60">Total</th>
	                    <th width="80"><p><? echo number_format($total_target,0); ?></p></th>
	                    <th width="80"><p><? echo number_format($total_production,0); ?></p></th>
	                    <th width="80"><p><? echo number_format($total_varience,0); ?></p></th>
	                    <th width="60"><p><? //echo number_format($total_achive,0); ?></p></th>
	                    <th width="60"><p><? echo number_format($total_man_power,0); ?></p></th>
	                    <th width="40"><p><? echo number_format($total_prod_hours,0); ?></p></th>
	                    <th width="80"><p><? echo number_format($total_available_minit,0); ?></p></th>
	                    <th width="80"><p><? echo number_format($total_target_min,0); ?></p></th>
	                    <th width="80"><p><? echo number_format($total_produce_minit,0); ?></p></th>
	                    <th width="50"><p><? echo number_format($total_npt_minit,0); ?></p></th>
	                    <th width="50"><p><? echo number_format($total_style_change,0); ?></p></th>
	                    <th width="50"><p><? //echo number_format($total_target_effi,0); ?></p></th>
	                    <th width="50"><p><? //echo number_format($total_achive_effi,0); ?></p></th>
	                    <th width="50"><p><? echo number_format($total_cm_earn,0); ?></p></th>                    
	                    <th width="50"><p><? echo number_format($total_fob_earn,0); ?></p></th> 
                          
                        <th width="50"><p><? echo number_format($total_finish_qty,0); ?></p></th>  
                        <th width="50"><p><? echo number_format($total_finish_prod_min,0); ?></p></th>  
                        <th width="50"><p><? echo number_format($total_finish_cm,0); ?></p></th>  
                        <th width="50"><p><? echo number_format($total_finish_fob,0); ?></p></th>                  
	                    <th><p></p></th> 					    
                    </tr>
                </tfoot>
            </table>
		</div>
		<!-- ================================== summary part ======================== -->		
		<div class="summary-part" style="margin-top: 20px;">  
	        <table id="table_header_1" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
	        	<caption>Month Wise Summary</caption>
	            <thead>
	                <tr>
	                    <th width="60"><p>Date</p></th>
	                    <th width="80"><p>Target</p></th>
	                    <th width="80"><p>Production</p></th>
	                    <th width="80"><p>Variance(Pcs)</p></th>
	                    <th width="60"><p>Achieve%</p></th>
	                    <th width="60"><p>Manpower</p></th>
	                    <th width="40"><p>WH</p></th>
	                    <th width="80"><p>Available/<br> Input Min.</p></th>
	                    <th width="80"><p>Target in<br> Min.</p></th>
	                    <th width="80"><p>Produce/<br> Output M<br>in.</p></th>
	                    <th width="50"><p>NPT Min.</p></th>
	                    <th width="50"><p>Style Cha<br>nge</p></th>
	                    <th width="50"><p>Target Ef<br>f%</p></th>
	                    <th width="50"><p>Achieve <br>Eff%</p></th>
	                    <th width="50"><p>CM Earn</p></th>                    
	                    <th width="50"><p>FOB Earn</p></th>  
                        <th width="50"><p>Finishing<br> Qty.</p></th>
                        <th width="50"><p>Finishing <br>Produce<br> Min. </p></th>
                        <th width="50"><p>Finishing<br> CM</p></th>
                        <th width="50"><p>Finishing<br> FOB</p></th>                 
	                    <th ><p>Remarks</p></th> 
	                </tr>
	            </thead>
	        </table>
	        <div style="width:<?= $tbl_width+20;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
	            <table class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
	                <tbody>
	                	<?
	                	$total_target 			= 0;
				 		$total_production 		= 0;
				  		$total_varience 		= 0;
				    	$total_achive 			= 0;
				 		$total_man_power 		= 0;
						$total_prod_hours 	= 0;
						$total_available_minit 	= 0;
				 		$total_target_min 		= 0;
						$total_produce_minit 	= 0;
						$total_npt_minit 		= 0;
						$total_style_change 	= 0;
						$total_target_effi 		= 0;
						$total_achive_effi 		= 0;
				   		$total_cm_earn 			= 0;
				  		$total_fob_earn 		= 0;
						$total_finish_qty=$total_finish_prod_min=$total_finish_cm=$total_finish_fob=0;
	                	foreach ($month_data_array as $m_key => $row) 
	                	{

	                		$target 		= $row['target'];
	                		$production 	= $row['production'];
	                		$varience 		= $target - $production;
	                		$achive 		= ($target>0) ? ($production/$target)*100 : 0;
	                		$man_power 		= $row['man_power'];
	                		$prod_hours 	= $row['prod_hours'];
	                		$available_minit= $row['available_minit'];
	                		$target_min 	= $row['target_min'];
	                		$produce_minit 	= $row['produce_minit'];
	                		$npt_minit 		= $npt_month_data_array[$m_key]['idle_min'];
	                		$remarks 		= $npt_month_data_array[$m_key]['remarks'];
	                		$style_change 	= $row['style_change'];
	                		// $target_effi 	= $row['target_effi'];
	                		// $achive_effi 	= $row['achive_effi'];
	                		$cm_earn 		= $row['cm_earn'];
	                		$fob_earn 		= $row['fob_earn'];
	                		$tot_line 		= $tot_line;

	                		$avg_prod_hours = $prod_hours/$tot_line;

	                		$target_effi = ($available_minit>0) ? ($target_min / $available_minit)*100 : 0; 
							$achive_effi = ($available_minit>0) ? ($produce_minit / $available_minit)*100 : 0;

	                		$i++;
	                		$bgcolor = ($i%2==0) ? "#ffffff" : "#f6faff";
		                	?>
		                   <tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
			                    <td width="60"><p><? echo date('F',strtotime($m_key));?></p></td>
			                    <td width="80" align="right"><p><? echo number_format($target,0);?></p></td>
			                    <td width="80" align="right"><p><? echo number_format($production,0);?></p></td>
			                    <td width="80" align="right"><p><? echo number_format($varience,0);?></p></td>
			                    <td width="60" align="right"><p><? echo number_format($achive,0);?></p></td>
			                    <td width="60" align="right"><p><? echo number_format($man_power,0);?></p></td>
			                    <td width="40" align="right"><p><? echo number_format($avg_prod_hours,0);?></p></td>
			                    <td width="80" align="right"><p><? echo number_format($available_minit,0);?></p></td>
			                    <td width="80" align="right"><p><? echo number_format($target_min,0);?></p></td>
			                    <td width="80" align="right"><p><? echo number_format($produce_minit,0);?></p></td>
			                    <td width="50" align="right"><p><? echo number_format($npt_minit,0);?></p></td>
			                    <td width="50" align="right"><p><? echo number_format($style_change,0);?></p></td>
			                    <td width="50" align="right"><p><? echo number_format($target_effi,0);?>%</p></td>
			                    <td width="50" align="right"><p><? echo number_format($achive_effi,0);?>%</p></td>
			                    <td width="50" align="right"><p><? echo number_format($cm_earn,0);?></p></td>                    
			                    <td width="50" align="right"><p><? echo number_format($fob_earn,0);?></p></td> 
                                
                                <td width="50" align="right"><p><? echo number_format($row['finishqty'],0);?></p></td> 
                                <td width="50" align="right"><p><? echo number_format($row['finishproducemint'],0);?></p></td> 
                                <td width="50" align="right"><p><? echo number_format($row['finishcm'],0);?></p></td> 
                                <td width="50" align="right"><p><? echo number_format($row['finishfob'],0);?></p></td> 
                                                  
			                    <td ><p><? echo implode(", ",array_unique(array_filter(explode("**", $remarks))));?></p></td> 					    
		                    </tr>
		                    <?		                    
		                    $total_target 			+= $target;
					 		$total_production 		+= $production;
					  		$total_varience 		+= $varience;
					    	$total_achive 			+= $achive;
					 		$total_man_power 		+= $man_power;
							$total_prod_hours 		+= $avg_prod_hours;
							$total_available_minit 	+= $available_minit;
					 		$total_target_min 		+= $target_min;
							$total_produce_minit 	+= $produce_minit;
							$total_npt_minit 		+= $npt_month_data_array[$m_key]['idle_min'];
							$total_style_change 	+= $style_change;
							$total_target_effi 		+= $target_effi;
							$total_achive_effi 		+= $achive_effi;
					   		$total_cm_earn 			+= $cm_earn;
					  		$total_fob_earn 		+= $fob_earn;
							$total_finish_qty		+=$row['finishqty'];
							$total_finish_prod_min	+=$row['finishproducemint'];
							$total_finish_cm		+=$row['finishcm'];
							$total_finish_fob		+=$row['finishfob'];
					  		// echo $npt_month_data_array[$m_key]['idle_min']."<br>";
		                }
		                unset($npt_date_data_array);
		                unset($npt_month_data_array);
		                ?>
	                </tbody>
	            </table>
			</div>
			<table class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                <tfoot>
                   <tr>
	                    <th width="60"><p>Total</p></th>
	                    <th width="80"><p><? echo number_format($total_target,0); ?></p></th>
	                    <th width="80"><p><? echo number_format($total_production,0); ?></p></th>
	                    <th width="80"><p><? echo number_format($total_varience,0); ?></p></th>
	                    <th width="60"><p><? //echo number_format($total_achive,0); ?></p></th>
	                    <th width="60"><p><? echo number_format($total_man_power,0); ?></p></th>
	                    <th width="40"><p><? echo number_format($total_prod_hours,0); ?></p></th>
	                    <th width="80"><p><? echo number_format($total_available_minit,0); ?></p></th>
	                    <th width="80"><p><? echo number_format($total_target_min,0); ?></p></th>
	                    <th width="80"><p><? echo number_format($total_produce_minit,0); ?></p></th>
	                    <th width="50"><p><? echo number_format($total_npt_minit,0); ?></p></th>
	                    <th width="50"><p><? echo number_format($total_style_change,0); ?></p></th>
	                    <th width="50"><p><? //echo number_format($total_target_effi,0); ?></p></th>
	                    <th width="50"><p><? //echo number_format($total_achive_effi,0); ?></p></th>
	                    <th width="50"><p><? echo number_format($total_cm_earn,0); ?></p></th>                    
	                    <th width="50"><p><? echo number_format($total_fob_earn,0); ?></p></th>
                        
                        <th width="50"><p><? echo number_format($total_finish_qty,0); ?></p></th>
                        <th width="50"><p><? echo number_format($total_finish_prod_min,0); ?></p></th>
                        <th width="50"><p><? echo number_format($total_finish_cm,0); ?></p></th>
                        <th width="50"><p><? echo number_format($total_finish_fob,0); ?></p></th>                    
	                    <th ><p></p></th> 					    
                    </tr>
                </tfoot>
            </table>
		</div>
	</fieldset>
	<?    
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename,'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename";
	disconnect($con);
	exit();      
}
?>