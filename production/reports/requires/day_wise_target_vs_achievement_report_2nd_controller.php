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
	echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/day_wise_target_vs_achievement_report_2nd_controller', this.value, 'load_drop_down_floor', 'floor_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/day_wise_target_vs_achievement_report_2nd_controller' );",0 ); 
	exit();    	 
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_id", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process=5 order by floor_name","id,floor_name", 0, "-- Select Floor --", $selected, "",0 );     	 	
	exit();    	 
}

if ($action == "eval_multi_select") {
    echo "set_multiselect('cbo_floor_id','0','0','','0');\n";
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
		if($date_from=="" && $date_to =="")
		{
			$data_format="";
		}
		else
		{
			if($db_type==0)	{$data_format="and b.pr_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";}
			if($db_type==2)	{$data_format="and b.pr_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";}
		}
		if( $location!=0 ) $cond .= " and a.location_id= $location";
		if( $floor_id!="" ) $cond.= " and a.floor_id in($floor_id)";
		
		$line_sql="select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id $data_format and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number";
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
		if( $floor_id!="" ) $cond.= " and floor_name in($floor_id)";
		$line_data="select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name";
		echo create_list_view("list_view", "Line No","250","300","310",0, $line_data , "js_set_value", "id,line_name", "", 1, "0", $arr, "line_name", 
		"","setFilterGrid('list_view',-1)","0","",1) ;	
		echo "<input type='hidden' id='txt_selected_id' />";
		echo "<input type='hidden' id='txt_selected' />";
	}
	exit();
}

if($action=="report_generate")// show 
{
	?>
	<style type="text/css">		
		table tr th,table tr td
		{
			word-wrap: break-word;
			word-break: break-all;
		}
	  
   </style> 
	<?
	extract($_REQUEST);
	$process = array( &$_POST );
	
	// echo change_date_format(str_replace("'", "", $txt_date_from));die;
	
	if($db_type==0)
	{
		$date_from=change_date_format(str_replace("'", "", $txt_date_from),'yyyy-mm-dd');
		$date_to=change_date_format(str_replace("'", "", $txt_date_to),'yyyy-mm-dd');
	}	
	if($db_type==2)	
	{
		$date_from=change_date_format(str_replace("'", "", $txt_date_from),'','',1);
		$date_to=change_date_format(str_replace("'", "", $txt_date_to),'','',1);
	}
	
	
	extract(check_magic_quote_gpc( $process ));
	$companyArr = return_library_array("select id,company_name from lib_company","id","company_name"); 
	$buyerArr = return_library_array("select id,short_name from lib_buyer","id","short_name"); 
	$locationArr = return_library_array("select id,location_name from lib_location","id","location_name"); 
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name"); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	$company_id=str_replace("'","",$cbo_company_id);
	$location_id=str_replace("'","",$cbo_location_id);
	$floor_id=str_replace("'","",$cbo_floor_id);
	$hidden_line_id=str_replace("'","",$hidden_line_id);
	
	//***********************************************************************************************************
	/*$lineDataArr = sql_select("SELECT id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
	order by sewing_line_serial"); 
	foreach($lineDataArr as $lRow)
	{
		$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
		$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
		$lastSlNo=$lRow[csf('sewing_line_serial')];
	}*/
	
	
	if($db_type==0)
	{
		$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$company_id and shift_id=1 and pr_date between '$date_from' and '$date_to' and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");	
	}
	else
	{
		$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$company_id and shift_id=1 and pr_date between '$date_from' and '$date_to' and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
	}
	// echo "min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$company_id and shift_id=1 and pr_date between '$date_from' and '$date_to' and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time";die();
	
	if($min_shif_start=="")
	{
		echo "<p style='font-size:20px;color:red', align='center'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<p/>";
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

	$cbo_floor_id=str_replace("'","",$cbo_floor_id);
	if($cbo_floor_id=="") $floor=""; else $floor="and a.floor_id in(".$cbo_floor_id.")";

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
	
	
	/*===================================================================================== /
	/										prod resource data								/
	/===================================================================================== */	
	// echo $prod_reso_allo[0]; die;
	if($prod_reso_allo[0]==1)
	{
		$prod_resource_array=array();
		$prod_resource_smv_array=array();

		$sql="SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id=$company_id and b.pr_date between '$date_from' and '$date_to' and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0";
		// echo $sql;die();
		$sql_resqlt = sql_select($sql);

		foreach($sql_resqlt as $val)
		{
			$sewing_line = '';
			$line_number=explode(",",$prod_reso_arr[$val[csf('id')]]);
			foreach($line_number as $value)
			{
				if($sewing_line=='') $sewing_line=$lineArr[$value]; else $sewing_line.=",".$lineArr[$value];
			}

			$prod_resource_array[$val[csf('floor_id')]][$sewing_line][$val[csf('pr_date')]]['man_power']+=$val[csf('man_power')];
			$prod_resource_array[$val[csf('floor_id')]][$sewing_line][$val[csf('pr_date')]]['operator']+=$val[csf('operator')];
			$prod_resource_array[$val[csf('floor_id')]][$sewing_line][$val[csf('pr_date')]]['helper']+=$val[csf('helper')];
			$prod_resource_array[$val[csf('floor_id')]][$sewing_line][$val[csf('pr_date')]]['terget_hour']+=$val[csf('target_per_hour')];
			$prod_resource_array[$val[csf('floor_id')]][$sewing_line][$val[csf('pr_date')]]['working_hour']+=$val[csf('working_hour')];
			$prod_resource_array[$val[csf('floor_id')]][$sewing_line][$val[csf('pr_date')]]['tpd']+=$val[csf('target_per_hour')]*$val[csf('working_hour')];
			$prod_resource_array[$val[csf('floor_id')]][$sewing_line][$val[csf('pr_date')]]['day_start']=$val[csf('from_date')];
			$prod_resource_array[$val[csf('floor_id')]][$sewing_line][$val[csf('pr_date')]]['day_end']=$val[csf('to_date')];
			$prod_resource_array[$val[csf('floor_id')]][$sewing_line][$val[csf('pr_date')]]['capacity']+=$val[csf('capacity')];
			$prod_resource_array[$val[csf('floor_id')]][$sewing_line][$val[csf('pr_date')]]['smv_adjust']=$val[csf('smv_adjust')];
			$prod_resource_array[$val[csf('floor_id')]][$sewing_line][$val[csf('pr_date')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];
		}
		// var_dump($prod_resource_array);die();	

		// =======================================================
		$sql="SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, c.from_date,c.to_date,c.capacity,d.po_id,d.target_per_line,d.operator, d.helper,d.working_hour,d.actual_smv,d.gmts_item_id from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c,prod_resource_color_size d where a.id=c.mst_id and c.id=b.mast_dtl_id and d.mst_id=a.id and d.dtls_id=c.id and a.company_id=$company_id and b.pr_date between '$date_from' and '$date_to' and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $prod_res_cond";
		// echo $sql;die();
		$sql_res=sql_select($sql);
		foreach($sql_res as $val)
		{			
			$prod_resource_array2[$val[csf('floor_id')]][$val[csf('id')]][$val[csf('pr_date')]]['man_power']+=$val[csf('operator')]+$val[csf('helper')];
			$prod_resource_array2[$val[csf('floor_id')]][$val[csf('id')]][$val[csf('pr_date')]]['terget_hour']+=$val[csf('target_per_line')];
			$prod_resource_array2[$val[csf('floor_id')]][$val[csf('id')]][$val[csf('pr_date')]]['working_hour']+=$val[csf('working_hour')];
			$prod_resource_array2[$val[csf('floor_id')]][$val[csf('id')]][$val[csf('pr_date')]]['tpd']+=$val[csf('target_per_line')]*$val[csf('working_hour')];


			$prod_resource_array3[$val[csf('id')]][$val[csf('po_id')]][$val[csf('gmts_item_id')]][$val[csf('pr_date')]]['man_power']+=$val[csf('operator')]+$val[csf('helper')];

			$prod_resource_array3[$val[csf('id')]][$val[csf('po_id')]][$val[csf('gmts_item_id')]][$val[csf('pr_date')]]['working_hour']+=$val[csf('working_hour')];

			$prod_resource_smv_array[$val[csf('id')]][$val[csf('po_id')]][$val[csf('gmts_item_id')]][$val[csf('pr_date')]]=$val[csf('actual_smv')];	
		}
		// echo "<pre>";print_r($prod_resource_array3);die();

		if(str_replace("'","",trim($txt_date_from))=="")
		{
			$pr_date_con="";
		}
		else
		{
			$pr_date_con=" and b.pr_date between $txt_date_from and $txt_date_to";
		}

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
			$extra_minute_production_arr[$ex_row['FLOOR_ID']][$ex_row['MST_ID']]+=$ex_row['TOTAL_SMV'];
			$extra_minute_resource_arr[$ex_row['MST_ID']][$ex_row['PR_DATE']]+=$ex_row['TOTAL_SMV'];
		}	
	}
	
	// echo "<pre>";print_r($prod_resource_array);die;

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
   	$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($manufacturing_company) and variable_list=25 and   status_active=1 and is_deleted=0");
	// echo $smv_source;
    if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
		
	/*===================================================================================== /
	/								get inhouse production data								/
	/===================================================================================== */
	if($db_type==0)
	{
		$sql="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name  as buyer_name,b.style_ref_no,b.job_no, a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.file_no,c.unit_price,c.grouping as ref,d.color_type_id,a.remarks,sum(d.production_qnty) as good_qnty,";
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
		$sql.="sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23 FROM  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a ,pro_garments_production_dtls d
		WHERE a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_name $location $floor $line and a.production_date between '$date_from' and '$date_to'
		GROUP BY b.job_no, a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name,b.style_ref_no,a.item_number_id,c.po_number,c.unit_price,c.file_no,c.grouping ,d.color_type_id,a.remarks
		ORDER BY a.floor_id,a.sewing_line";
	}
	else if($db_type==2)
	{
		$sql="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name  as buyer_name,b.style_ref_no,b.job_no, a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.file_no,c.unit_price,c.grouping as ref,d.color_type_id,a.remarks,sum(d.reject_qty) as reject_qty,sum(d.replace_qty) as replace_qty,sum(d.production_qnty) as good_qnty,";
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
		$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN production_qnty else 0 END) AS prod_hour23  FROM  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a ,pro_garments_production_dtls d
		WHERE a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_name $location $floor $line and a.production_date between '$date_from' and '$date_to'
		GROUP BY b.job_no, a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name,b.style_ref_no,a.item_number_id,c.po_number,c.unit_price,c.file_no,c.grouping ,d.color_type_id,a.remarks
		ORDER BY a.floor_id,a.sewing_line";
	}
	// echo $sql;die;
	$sql_resqlt=sql_select($sql);
	$dataArray = array();
	$floorLineArray = array();
	$po_id_array = array();
	$line_style_chk_array = array();
	$line_wise_style_count_arr = array();
	$production_po_data_arr = array();
	$style_count_arr = array();
	foreach($sql_resqlt as $val)
	{	
		$poIdArr[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];	

		$sewing_line='';
		if($val[csf('prod_reso_allo')]==1)
		{
			$line_number=explode(",",$prod_reso_arr[$val[csf('sewing_line')]]);
			foreach($line_number as $value)
			{
				if($sewing_line=='') $sewing_line=$lineArr[$value]; else $sewing_line.=",".$lineArr[$value];
			}
		}
		else
		{ 
			$sewing_line=$lineArr[$val[csf('sewing_line')]];
		}
		$dataArray[$val[csf('floor_id')]][$sewing_line][$val[csf('production_date')]]['qty'] += $val[csf('good_qnty')];
		$dataArray[$val[csf('floor_id')]][$sewing_line][$val[csf('production_date')]]['reject_qty'] += $val[csf('reject_qty')];
		$dataArray[$val[csf('floor_id')]][$sewing_line][$val[csf('production_date')]]['replace_qty'] += $val[csf('replace_qty')];
		$floorLineArray[$val[csf('floor_id')]][$sewing_line] = $val[csf('sewing_line')];

		if($dataArray[$val[csf('floor_id')]][$sewing_line][$val[csf('production_date')]]['item_number_id']!="")
		{
			$dataArray[$val[csf('floor_id')]][$sewing_line][$val[csf('production_date')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('style_ref_no')]; 
		}
		else
		{
			 $dataArray[$val[csf('floor_id')]][$sewing_line][$val[csf('production_date')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('style_ref_no')];; 
		}
		$production_data_arr_qty[$val[csf('floor_id')]][$sewing_line][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('production_date')]]['quantity']+=$val[csf('good_qnty')];
		$production_data_arr_qty2[$val[csf('floor_id')]][$sewing_line][$val[csf('po_break_down_id')]][$val[csf('production_date')]]['quantity']+=$val[csf('good_qnty')];
		
		$po_id_array[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];

		for($h=$hour;$h<$last_hour;$h++)
		{
			$prod_hour="prod_hour".substr($start_hour_arr[$h],0,2)."";
			$production_data_arr[$val[csf('floor_id')]][$sewing_line][$prod_hour]+=$val[csf($prod_hour)]; 
			
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
			{
				if( $h>=$line_start_hour && $h<=$actual_time)
				{
					$production_po_data_arr[$val[csf('floor_id')]][$sewing_line][$val[csf('po_break_down_id')]]+=$val[csf($prod_hour)]; 
				} 	
			}
			
			if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date)) 
			{	
				$production_po_data_arr[$val[csf('floor_id')]][$sewing_line][$val[csf('po_break_down_id')]]+=$val[csf($prod_hour)];
			}
		}
		if($line_style_chk_array[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]] =="")
		{
			$line_wise_style_count_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]++;
			$line_wise_style_count_arr2[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]++;
			$style_count_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]] .= $val[csf('job_no')]."*";
			$line_style_chk_array[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]] = $val[csf('job_no')];
		}
	}
	// echo "<pre>"; print_r($style_count_arr);die();
	
	/*===================================================================================== /
	/										subcoutact data									/
	/===================================================================================== */
    if($db_type==0)
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
				$sql_sub_contuct.="sum(CASE WHEN  a.hour<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,";
			}
			else
			{
				$sql_sub_contuct.="sum(CASE WHEN a.hour>'$bg' and a.hour<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,";	
			}
			$first=$first+1;
   		}
   		$sql_sub_contuct.="sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.id  and d.is_deleted=0 left join lib_sewing_line e on a.line_id=e.id and e.is_deleted=0 where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id=$company_id $subcon_location $floor $subcon_line and a.production_date between '$date_from' and '$date_to' group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date,  b.subcon_job,a.line_id,b.party_id,c.order_no,c.cust_style_ref,e.sewing_line_serial,a.prod_reso_allo order by a.location_id, a.floor_id,e.sewing_line_serial,a.prod_reso_allo";
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
		
	   	$sql_sub_contuct.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]'	and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.id  and d.is_deleted=0 left join lib_sewing_line e on a.line_id=e.id and e.is_deleted=0 where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id=$company_id $subcon_location $floor $subcon_line and a.production_date between '$date_from' and '$date_to' group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date,  b.subcon_job,a.line_id,b.party_id,c.order_no,c.cust_style_ref,e.sewing_line_serial,a.prod_reso_allo order by a.location_id, a.floor_id,e.sewing_line_serial,a.prod_reso_allo";
		
	}
	// echo $sql_sub_contuct;die;
	$sub_result=sql_select($sql_sub_contuct);
		
	foreach($sub_result as $subcon_val)
	{
		$sewing_line='';
		if($subcon_val[csf('prod_reso_allo')]==1)
		{
			$line_number=explode(",",$prod_reso_arr[$subcon_val[csf('line_id')]]);
			foreach($line_number as $value)
			{
				if($sewing_line=='') $sewing_line=$lineArr[$value]; else $sewing_line.=",".$lineArr[$value];
			}
		}
		else
		{ 
			$sewing_line=$lineArr[$subcon_val[csf('line_id')]];
		}
		$dataArray[$subcon_val[csf('floor_id')]][$sewing_line][$subcon_val[csf('production_date')]]['qty'] += $subcon_val[csf('good_qnty')];
		$po_id_array[$val[csf('order_id')]] = $val[csf('order_id')];
	}
	// echo "<pre>"; print_r($dataArray);die();	


	/*===================================================================================== /
	/										po item wise smv 								/
	/===================================================================================== */
	// $all_po_id = implode(",", array_filter($po_id_array));
	$po_id_cond = where_con_using_array($po_id_array,0," b.id");
	$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_id=a.id and b.job_id=c.job_id  $po_id_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3)";
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
	/										po active days									/
	/===================================================================================== */
	$po_id_cond = where_con_using_array($po_id_array,0," c.id");
    $po_active_sql="SELECT a.sewing_line,a.production_date,b.job_no from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a where c.job_id=b.id and  a.po_break_down_id=c.id and a.production_type=5 and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $po_id_cond  group by  a.sewing_line,a.production_date,b.job_no";
    //echo $po_active_sql;die;
	$k=0;
	foreach(sql_select($po_active_sql) as $vals)
	{
		$prod_dates=$vals[csf('production_date')];
		if($duplicate_date_arr[$vals[csf('sewing_line')]][$vals[csf('job_no')]][$prod_dates]=="")
		{
			if($k!=0)
			{
				// echo $vals[csf('job_no')]."<br>";
				$active_days_arr[$vals[csf('sewing_line')]][$vals[csf('job_no')]]++;
				$duplicate_date_arr[$vals[csf('sewing_line')]][$vals[csf('job_no')]][$prod_dates]=$prod_dates;
			}
			$k++;
		}
	}
	// echo "<pre>"; print_r($active_days_arr);
	
	/*===================================================================================== /
	/										get defect qty									/
	/===================================================================================== */
	$po_id_cond = where_con_using_array($po_id_array,0,"a.po_break_down_id");
	$sql="SELECT a.sewing_line,a.production_date,b.defect_type_id,b.defect_qty from pro_garments_production_mst a,pro_gmts_prod_dft b where a.id=b.mst_id and a.production_type=5  and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $po_id_cond  and b.defect_type_id in(2,3,4)";
	// echo $sql;die;
	$res = sql_select($sql);
	$dhu_qty_array = array();
	foreach ($res as $v) 
	{
		$dhu_qty_array[$v['SEWING_LINE']][$v['PRODUCTION_DATE']]+=$v['DEFECT_QTY'];
	}

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
	$date_range = get_date_range($date_from,$date_to);
	// echo "<pre>";print_r($date_range);die();
	$tbl_width = 300 + (count($date_range)*40);
	$colspan = 3 + count($date_range);
	
	ob_start();	
	?>
               
	<fieldset style="width:<? echo $tbl_width+20;?>px">
       <table width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
            <tr class="form_caption">
                <td colspan="<? echo $colspan;?>" align="center"><strong><? echo $companyArr[$company_id]; ?></strong></td> 
            </tr> 
            <tr class="form_caption">
                <td colspan="<? echo $colspan;?>" align="center"><strong>Day Wise Target VS Achivement Report &nbsp;</strong></td> 
            </tr>
            <tr class="form_caption">
                <td colspan="<? echo $colspan;?>" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date_from)) ); ?> Tot <? echo change_date_format( str_replace("'","",trim($txt_date_to)) ); ?></strong></td> 
            </tr>
        </table>
        <!-- ============================== heading part ============================== -->
        <div>
	        <table id="table_header_1" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
	            <thead>
	                <tr height="30">
	                    <th class="break_all" width="100">Floor Name</th>
	                    <th class="break_all" width="80">Line No</th>
	                    <th class="break_all" width="50">Particular</th>
	                   	<?
	                   	foreach ($date_range as $key => $value) 
	                  	{
	                   		?>
	                   		<th width="40"><? echo date('d-M',strtotime($value));?></th>
	                   		<?
	                   	}
	                	?> 
	                    <th class="break_all" width="50">Total</th>
	                </tr>
	            </thead>
	        </table>
	    </div>
        <!-- ============================== body part ================================== -->
        <div style="width:<?= $tbl_width+20;?>px; max-height:350px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
            	<tbody>
            		<?
            		$i = 1;
            		$grand_total_arr = array();
            		$grand_tot_target = 0;
    				$grand_tot_acv = 0;
    				$grand_tot_devi = 0;
    				$grand_tot_effi = 0;
            		foreach ($floorLineArray as $f_key => $f_data) 
            		{
            			$floor_qty_arr = array();
            			$floor_tot_target = 0;
        				$floor_tot_acv = 0;
        				$floor_tot_devi = 0;
        				$floor_tot_effi = 0;
            			foreach ($f_data as $l_key => $line_date) 
            			{
            				// echo $l_key."=string=".$line_date;
            				$line_tot_target = 0;
            				$line_tot_acv = 0;
            				$line_tot_devi = 0;
            				$line_tot_effi = 0;

            				// ======================== for target efficiency =====================


            				/*$item_smv_ex = explode("/", $item_smv);
							$counter = 0;
							$tot_smv = 0;
							foreach ($item_smv_ex as $val) 
							{
								$tot_smv += $val;
								$counter++;
							}
							$avg_smv = $tot_smv/$counter;
							$target_min = $eff_target * $avg_smv; 
							$target_effi = ($target_min / $efficiency_min)*100;*/

							

            				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            				?>
            				<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
            					<td rowspan="8" width="100" valign="middle"><?=$floorArr[$f_key];?></td>
            					<td rowspan="8" width="80" valign="middle"><? echo $l_key;?></td>
            					<td width="50">Target</td>
            					<?
            					$target = 0;
			                   	foreach ($date_range as $key => $value) 
			                  	{
			                  		// echo $f_key."=".$line_date."=".$value."<br>";
			                  		if($line_wise_style_count_arr[$value][$f_key][$line_date]>1)
			                  		{
				                  		$target = $prod_resource_array2[$f_key][$line_date][$value]['tpd'];
				                  		$floor_qty_arr[$f_key][$value]['target'] += $target;
				                  		$grand_total_arr[$value]['target'] += $target;
				                  		$line_tot_target += $target;
				                  	}
				                  	else
				                  	{
				                  		$target = $prod_resource_array[$f_key][$l_key][$value]['tpd'];
				                  		$floor_qty_arr[$f_key][$value]['target'] += $target;
				                  		$grand_total_arr[$value]['target'] += $target;
				                  		$line_tot_target += $target;
				                  	}
			                   		?>
			                   		<td align="right" width="40"><? echo number_format($target,0);?></td>
			                   		<?
			                   	}
			                   	$i++;
			                   	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			                	?>
            					<td align="right" width="50"><? echo number_format($line_tot_target,0); ?></td>
            				</tr>
            				<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
            					<td width="50">Achieve</td>
            					<?
            					$achivement = 0;
			                   	foreach ($date_range as $key => $value) 
			                  	{
			                  		$achivement = $dataArray[$f_key][$l_key][$value]['qty'];
			                  		$floor_qty_arr[$f_key][$value]['achivement'] += $achivement;
			                  		$grand_total_arr[$value]['achivement'] += $achivement;
			                  		$line_tot_acv += $achivement;
			                   		?>
			                   		<td align="right" width="40"><? echo number_format($achivement,0);?></td>
			                   		<?
			                   	}
			                   	$i++;
			                   	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			                	?>
            					<td align="right" width="50"><? echo number_format($line_tot_acv,0); ?></td>
            				</tr>
            				<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
            					<td width="50">Deviation</td>
            					<?
            					$deviation = 0;
            					
			                   	foreach ($date_range as $key => $value) 
			                  	{
			                  		if($line_wise_style_count_arr[$value][$f_key][$line_date]>1)
			                  		{
				                  		$target = $prod_resource_array2[$f_key][$line_date][$value]['tpd'];
				                  		$achivement = $dataArray[$f_key][$l_key][$value]['qty'];
				                  		$deviation = $achivement - $target;			                  		
				                  		$floor_qty_arr[$f_key][$value]['deviation'] += $deviation;
				                  		$grand_total_arr[$value]['deviation'] += $deviation;
				                  		$line_tot_devi += $deviation;
				                  	}
				                  	else
				                  	{
				                  		$target = $prod_resource_array[$f_key][$l_key][$value]['tpd'];
				                  		$achivement = $dataArray[$f_key][$l_key][$value]['qty'];
				                  		$deviation = $achivement - $target;			                  		
				                  		$floor_qty_arr[$f_key][$value]['deviation'] += $deviation;
				                  		$grand_total_arr[$value]['deviation'] += $deviation;
				                  		$line_tot_devi += $deviation;
				                  	}
			                   		?>
			                   		<td align="right" width="40"><? echo number_format($deviation,0);?></td>
			                   		<?
			                   	}
			                   	$i++;
			                   	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			                	?>
            					<td align="right" width="50"><? echo number_format($line_tot_devi,0); ?></td>
            				</tr>
            				<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
            					<td width="50">Achieve. %</td>
            					<?
            					$achive_prsnt = 0;
			                   	foreach ($date_range as $key => $value) 
			                  	{
			                  		if($line_wise_style_count_arr[$value][$f_key][$line_date]>1)
			                  		{
				                  		$target = $prod_resource_array2[$f_key][$line_date][$value]['tpd'];
				                  		$achivement = $dataArray[$f_key][$l_key][$value]['qty'];
	            						$achive_prsnt = ($target>0) ? ($achivement/$target)*100 : 0; 
				                  		$floor_qty_arr[$f_key][$value]['achive_prsnt'] += $achive_prsnt;
				                  		$grand_total_arr[$value]['achive_prsnt'] += $achive_prsnt;
				                  		// $line_tot_achive_prsnt += $achive_prsnt;
				                  	}
				                  	else
				                  	{
				                  		$target = $prod_resource_array[$f_key][$l_key][$value]['tpd'];
				                  		$achivement = $dataArray[$f_key][$l_key][$value]['qty'];
	            						$achive_prsnt = ($target>0) ? ($achivement/$target)*100 : 0; 
				                  		$floor_qty_arr[$f_key][$value]['achive_prsnt'] += $achive_prsnt;
				                  		$grand_total_arr[$value]['achive_prsnt'] += $achive_prsnt;
				                  		// $line_tot_achive_prsnt += $achive_prsnt;
				                  	}
			                   		?>
			                   		<td align="right" width="40"><? echo number_format($achive_prsnt,2);?></td>
			                   		<?
			                   	}
			                   	$i++;
			                   	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			                   	$line_tot_achive_prsnt = ($line_tot_target>0) ? ($line_tot_acv/$line_tot_target)*100 : 0; 
			                	?>
            					<td align="right" width="50"><? echo number_format($line_tot_achive_prsnt,2); ?></td>
            				</tr>
            				<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
            					<td width="50">Eff. %</td>
            					<?
            					$effi = 0;
            					$line_total_produce_min = 0;
            					$line_total_efficiency_min = 0;
			                   	foreach ($date_range as $key => $value) 
			                  	{
			                  		$germents_item=array_unique(explode('****',$dataArray[$f_key][$l_key][$value]['item_number_id']));
			                  		// print_r($germents_item);
			                  		$produce_minit=0;
									$efficiency_min=0;
									$item_smv="";
									$line_production_hour=0;
									foreach ($germents_item as $g_val) 
									{
										$po_garment_item=explode('**',$g_val);
										if($item_smv!='') $item_smv.='/';
										$item_smv.=$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
										$produce_minit+=$production_data_arr_qty2[$f_key][$l_key][$po_garment_item[0]][$value]['quantity']*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];

										if(str_replace("'","",$actual_production_date)>str_replace("'","",$actual_date)) 
										{
											
											$line_start = $line_number_arr[$l_key][$value]['prod_start_time'];
											
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
													$line_production_hour+=$production_data_arr[$f_key][$l_key][$line_hour];
													$line_floor_production+=$production_data_arr[$f_key][$l_key][$line_hour];
													$line_total_production+=$production_data_arr[$f_key][$l_key][$line_hour];
													$actual_time_hour=$start_hour_arr[$lh+1];
												}
											}
						 					if($start_hour_arr[$actual_time]>$lunch_start_hour) $total_eff_hour=$total_eff_hour-1;
											
											if($type_line==2)
											{
												if($total_eff_hour>$production_data_arr[$f_key][$l_key]['working_hour'])
												{
													 $total_eff_hour=$production_data_arr[$f_key][$l_key]['working_hour'];
												}
											}
											else
											{
												if($line_wise_style_count_arr[$f_key][$l_key]>1)
												{
													if($total_eff_hour>$prod_resource_array2[$l_key][$value]['working_hour'])
													{
														$total_eff_hour=$prod_resource_array2[$l_key][$value]['working_hour'];
													}
												}
												else
												{
													if($total_eff_hour>$prod_resource_array[$f_key][$l_key][$value]['working_hour'])
													{
														$total_eff_hour=$prod_resource_array[$f_key][$l_key][$value]['working_hour'];
													}
												}
											}
											
										}
										
										
										if(str_replace("'","",$actual_production_date)<=str_replace("'","",$actual_date)) 
										{
											for($ah=$hour;$ah<=$last_hour;$ah++)
											{
												$prod_hour="prod_hour".substr($start_hour_arr[$ah],0,2).""; 
												$line_production_hour+=$production_data_arr[$f_key][$l_key][$prod_hour];
												$line_floor_production+=$production_data_arr[$f_key][$l_key][$prod_hour];
												$line_total_production+=$production_data_arr[$f_key][$l_key][$prod_hour];

												// echo "$line_production_hour**$f_key**$l_key**$prod_hour<br>";
											}
											if($type_line==2)
											{
												$total_eff_hour=$production_data_arr[$f_key][$l_key]['working_hour'];
											}
											else
											{
												if($line_wise_style_count_arr[$value][$f_key][$line_date]>1)
												{
													$total_eff_hour=$prod_resource_array3[$line_date][$po_garment_item[0]][$po_garment_item[1]][$value]['working_hour'];	
													$man_power = $prod_resource_array3[$line_date][$po_garment_item[0]][$po_garment_item[1]][$value]['man_power'];	
													$efficiency_min += $man_power*$total_eff_hour*60;
													// echo $man_power."*".$total_eff_hour."*60<br>";
												}
												else
												{
													$total_eff_hour = $prod_resource_array[$f_key][$l_key][$value]['working_hour'];
													// echo "$total_eff_hour**$l_key**$value<br>";
												}
												
											}
										}
									}
									// echo $produce_minit."<br>";
									// =============================================================================

									
									


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

									// echo "**".$prod_resource_array[$f_key][$l_key][$value]['man_power'];
									$smv_adjustmet_type=$prod_resource_array[$f_key][$l_key][$value]['smv_adjust_type'];

									if($total_eff_hour>=$prod_resource_array[$f_key][$l_key][$value]['working_hour'])
									{
										if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array[$f_key][$l_key][$value]['smv_adjust'];
										if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array[$f_key][$l_key][$value]['smv_adjust'])*(-1);
									}
									if($line_wise_style_count_arr[$value][$f_key][$line_date]>1)
			                  		{
										// $efficiency_min = $total_adjustment+($prod_resource_array[$f_key][$l_key][$value]['man_power'])*$cla_cur_time*60;
										// $efficiency_min = ($prod_resource_array2[$f_key][$line_date][$value]['man_power'])*$cla_cur_time*60;
										// echo $l_key."=".$prod_resource_array2[$f_key][$line_date][$value]['man_power']."*".$cla_cur_time."*60<br>";
									}
									else
									{
										$efficiency_min = ($prod_resource_array[$f_key][$l_key][$value]['man_power'])*$cla_cur_time*60;
									}
									$achive_effi = ($efficiency_min>0) ? ($produce_minit / $efficiency_min)*100 : 0; 
			                  		
            						// echo $l_key."**".$produce_minit."/".$efficiency_min."*100<br>";
			                  		$floor_qty_arr[$f_key][$value]['produce_minit'] += $produce_minit;
			                  		$floor_qty_arr[$f_key][$value]['efficiency_min'] += $efficiency_min;

			                  		$grand_total_arr[$value]['produce_minit'] += $produce_minit;
			                  		$grand_total_arr[$value]['efficiency_min'] += $efficiency_min;
			                  		// $line_tot_achive_effi += $achive_effi;

            						$line_total_produce_min += $produce_minit;
            						$line_total_efficiency_min += $efficiency_min;
			                   		?>
			                   		<td align="right" width="40"><? echo number_format($achive_effi,2);?></td>
			                   		<?
			                   	}
								$i++;
			                   	$line_tot_achive_effi = ($line_total_efficiency_min>0) ? ($line_total_produce_min / $line_total_efficiency_min)*100 : 0;
			                   	// echo $line_total_produce_min ."/". $line_total_efficiency_min."*10<br>";
			                	?>
            					<td align="right" width="50"><? echo number_format($line_tot_achive_effi,2); ?></td>
            				</tr>

							
            				<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
            					<td width="50">DHU</td>
            					<?
								$line_tot_dhu = 0;
								$tot_defect_count = 0;
								$tot_all_qty = 0;
								$tot_reject_qty = 0;
								$tot_replace_qty = 0;
			                   	foreach ($date_range as $key => $value) 
			                  	{
									$defect_count = $dhu_qty_array[$line_date][$value];
									$all_qty = $dataArray[$f_key][$l_key][$value]['qty'];
									$reject_qty = $dataArray[$f_key][$l_key][$value]['reject_qty'];
									$replace_qty = $dataArray[$f_key][$l_key][$value]['replace_qty'];

									$dhu = ($defect_count) ? ($defect_count/($all_qty+$reject_qty+$replace_qty))*100 : 0;
			                   		?>
			                   		<td align="right" width="40"><? echo number_format($dhu,2);?></td>
			                   		<?
									$floor_qty_arr[$f_key][$value]['dhu']+=$dhu_qty_array[$line_date][$value];
									$grand_total_arr[$value]['dhu']+=$dhu_qty_array[$line_date][$value];
									$line_tot_dhu += $dhu_qty_array[$line_date][$value];

									$tot_defect_count += $dhu_qty_array[$line_date][$value];
									$tot_all_qty += $dataArray[$f_key][$l_key][$value]['qty'];
									$tot_reject_qty += $dataArray[$f_key][$l_key][$value]['reject_qty'];
									$tot_replace_qty += $dataArray[$f_key][$l_key][$value]['replace_qty'];
			                   	}
			                   	$i++;
			                   	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$line_dhu = ($tot_defect_count) ? ($tot_defect_count/($tot_all_qty+$tot_reject_qty+$tot_replace_qty))*100 : 0;
			                	?>
            					<td align="right" width="50"><? echo number_format($line_dhu,2); ?></td>
            				</tr>
							
            				<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
            					<td width="50">Reject Qty</td>
            					<?
								$line_tot_rej= 0;
			                   	foreach ($date_range as $key => $value) 
			                  	{
			                   		?>
			                   		<td align="right" width="40"><? echo number_format($dataArray[$f_key][$l_key][$value]['reject_qty'],0);?></td>
			                   		<?
									$floor_qty_arr[$f_key][$value]['reject']+=$dataArray[$f_key][$l_key][$value]['reject_qty'];
									$grand_total_arr[$value]['reject']+=$dataArray[$f_key][$l_key][$value]['reject_qty'];
									$line_tot_rej += $dataArray[$f_key][$l_key][$value]['reject_qty'];
			                   	}
			                   	$i++;
			                   	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			                	?>
            					<td align="right" width="50"><? echo number_format($line_tot_rej,0); ?></td>
            				</tr>
							
            				<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
            					<td width="50">Style Change</td>
            					<?
								$line_tot_style_cng= 0;
			                   	foreach ($date_range as $key => $value) 
			                  	{
									$style_count = 0;
									$sty_arr = array_filter(explode("*", $style_count_arr[$value][$f_key][$line_date]));
									if(count($sty_arr)>1)
									{
										$style_count = count($sty_arr) - 1;
									}
			                   		?>
			                   		<td align="right" width="40"><? echo number_format($style_count,0);?></td>
			                   		<?
									$floor_qty_arr[$f_key][$value]['style_cng']+=$style_count;
									$grand_total_arr[$value]['style_cng']+=$style_count;
									$line_tot_style_cng+=$style_count;
			                   	}
			                   	$i++;
			                   	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			                	?>
            					<td align="right" width="50"><? echo number_format($line_tot_style_cng,0); ?></td>
            				</tr>
            				<?
            				$i++;
            				
            			}
            			?>
            			<!-- ======================= floor wise summary ======================= -->
            			<tr style="background: #a8d5baff; font-weight: bold;">
	    					<td colspan="2" rowspan="8" width="50" valign="middle"><? echo $floorArr[$f_key];?> Total</td>
	    					<td width="50">Target</td>
	    					<?
	    					$floor_target = 0;
		                   	foreach ($date_range as $key => $value) 
		                  	{
		                  		$floor_target = $floor_qty_arr[$f_key][$value]['target'];
		                  		$floor_tot_target += $floor_target;
		                   		?>
		                   		<td align="right" width="40"><? echo number_format($floor_target,0);?></td>
		                   		<?
		                   	}
		                	?>
	    					<td align="right" width="50"><? echo number_format($floor_tot_target,0); ?></td>
	    				</tr>
	    				<?	
	    				?>
	    				<tr style="background: #d0c9ff;font-weight: bold;">
	    					<td width="50">Achieve</td>
	    					<?
	    					$floor_achivement = 0;
		                   	foreach ($date_range as $key => $value) 
		                  	{
		                  		$floor_achivement = $floor_qty_arr[$f_key][$value]['achivement'];
		                  		$floor_tot_acv += $floor_achivement;
		                   		?>
		                   		<td align="right" width="40"><? echo number_format($floor_achivement,0);?></td>
		                   		<?
		                   	}
		                	?>
	    					<td align="right" width="50"><? echo number_format($floor_tot_acv,0); ?></td>
	    				</tr>
	    				<?
	    				?>
	    				<tr style="background: #c3d5ff;font-weight: bold;">
	    					<td width="50">Deviation</td>
	    					<?
	    					$floor_deviation = 0;
		                   	foreach ($date_range as $key => $value) 
		                  	{
		                  		$floor_target = $floor_qty_arr[$f_key][$value]['target'];
		                  		$floor_achivement = $floor_qty_arr[$f_key][$value]['achivement'];
		                  		$floor_deviation = $floor_achivement - $floor_target;
		                  		$floor_tot_devi += $floor_deviation;
		                   		?>
		                   		<td align="right" width="40"><? echo number_format($floor_deviation,0);?></td>
		                   		<?
		                   	}
		                	?>
	    					<td align="right" width="50"><? echo number_format($floor_tot_devi,0); ?></td>
	    				</tr>
	    				<?
	    				?>
	    				<tr style="background: #a094ff;font-weight: bold;">
	    					<td width="50">Achi. %</td>
	    					<?
	    					$floor_achive_prsnt = 0;
		                   	foreach ($date_range as $key => $value) 
		                  	{
		                  		$floor_target = $floor_qty_arr[$f_key][$value]['target'];
		                  		$floor_achivement = $floor_qty_arr[$f_key][$value]['achivement'];
		                  		$floor_achive_prsnt = ($floor_target>0) ? ($floor_achivement/$floor_target)*100 : 0;
		                  		$floor_tot_achive_prsnt += $floor_achive_prsnt;
		                   		?>
		                   		<td align="right" width="40"><? echo number_format($floor_achive_prsnt,2);?></td>
		                   		<?
		                   	}
		                   	$floor_tot_achive_prsnt = ($floor_tot_target>0) ? ($floor_tot_acv/$floor_tot_target)*100 : 0;
		                	?>
	    					<td align="right" width="50"><? echo number_format($floor_tot_achive_prsnt,2); ?></td>
	    				</tr>
	    				<tr style="background: #c6b039;font-weight: bold;">
	    					<td width="50">Eff. %</td>
	    					<?
	    					$floor_achive_effi = 0;
	    					$floor_tot_produce_min = 0;
	    					$floor_tot_efficiency_min = 0;
		                   	foreach ($date_range as $key => $value) 
		                  	{
		                  		$floor_produce_minit = $floor_qty_arr[$f_key][$value]['produce_minit'];
		                  		$floor_efficiency_min = $floor_qty_arr[$f_key][$value]['efficiency_min'];

		                  		$floor_achive_effi = ($floor_efficiency_min>0) ? ($floor_produce_minit / $floor_efficiency_min)*100 : 0;
		                  		$floor_tot_achive_effi += $floor_achive_effi;
		    					$floor_tot_produce_min += $floor_produce_minit;
		    					$floor_tot_efficiency_min += $floor_efficiency_min;
		                   		?>
		                   		<td align="right" width="40"><? echo number_format($floor_achive_effi,2);?></td>
		                   		<?
		                   	}
		                   	$floor_tot_achive_effi = ($floor_tot_efficiency_min>0) ? ($floor_tot_produce_min / $floor_tot_efficiency_min)*100 : 0;
		                	?>
	    					<td align="right" width="50"><? echo number_format($floor_tot_achive_effi,2); ?></td>
	    				</tr>
						
	    				<tr style="background: #C9F4AA;font-weight: bold;">
	    					<td width="50">DHU</td>
	    					<?
	    					$floor_dhu = 0;
		                   	foreach ($date_range as $key => $value) 
		                  	{
		                  		$floor_dhu += $floor_qty_arr[$f_key][$value]['dhu'];
		                   		?>
		                   		<td align="right" width="40"><? echo number_format($floor_qty_arr[$f_key][$value]['dhu'],0);?></td>
		                   		<?
		                   	}
		                	?>
	    					<td align="right" width="50"><? echo number_format($floor_dhu,0); ?></td>
	    				</tr>
						
	    				<tr style="background: #F6E6C2;font-weight: bold;">
	    					<td width="50">Rejct qty</td>
	    					<?
	    					$floor_reject = 0;
		                   	foreach ($date_range as $key => $value) 
		                  	{
		                  		$floor_reject += $floor_qty_arr[$f_key][$value]['reject'];
		                   		?>
		                   		<td align="right" width="40"><? echo number_format($floor_qty_arr[$f_key][$value]['reject'],0);?></td>
		                   		<?
		                   	}
		                	?>
	    					<td align="right" width="50"><? echo number_format($floor_reject,0); ?></td>
	    				</tr>
						
	    				<tr style="background: #D9ACF5;font-weight: bold;">
	    					<td width="50">Style Changed</td>
	    					<?
	    					$floor_style_cng = 0;
		                   	foreach ($date_range as $key => $value) 
		                  	{
		                  		$floor_style_cng += $floor_qty_arr[$f_key][$value]['style_cng'];
		                   		?>
		                   		<td align="right" width="40"><? echo number_format($floor_qty_arr[$f_key][$value]['style_cng'],0);?></td>
		                   		<?
		                   	}
		                	?>
	    					<td align="right" width="50"><? echo number_format($floor_style_cng,0); ?></td>
	    				</tr>
            			<?
            		}
            		?>
            	</tbody>
            </table>
		</div>
		<!-- ============================== footer part ================================== -->
        <div>
            <table class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
            	<tfoot>            		
    				<tr>
    					<th colspan="2"  rowspan="8" width="80">Total Achive</th>
    					<th width="50">Target</th>
    					<?
    					$grand_target = 0;
	                   	foreach ($date_range as $key => $value) 
	                  	{
	                  		$grand_target = $grand_total_arr[$value]['target'];
	                  		$grand_tot_target += $grand_target;
	                   		?>
	                   		<th width="40"><? echo number_format($grand_target,0);?></th>
	                   		<?
	                   	}
	                	?>
    					<th width="50"><? echo number_format($grand_tot_target,0);?></th>
    				</tr>
    				<?	
    				?>
    				<tr>
    					<th width="50">Achieve</th>
    					<?
    					$grand_acv = 0;
	                   	foreach ($date_range as $key => $value) 
	                  	{
	                  		$grand_acv = $grand_total_arr[$value]['achivement'];
	                  		$grand_tot_acv += $grand_acv;
	                   		?>
	                   		<th width="40"><? echo number_format($grand_acv,0);?></th>
	                   		<?
	                   	}
	                	?>
    					<th width="50"><? echo number_format($grand_tot_acv,0);?></th>
    				</tr>
    				<?
    				?>
    				<tr>
    					<th width="50">Deviation</th>
    					<?
    					$grand_devi = 0;
	                   	foreach ($date_range as $key => $value) 
	                  	{	                  		
	                  		$grand_target = $grand_total_arr[$value]['target'];
	                  		$grand_acv = $grand_total_arr[$value]['achivement'];
	                  		$grand_devi = $grand_acv - $grand_target;
	                  		$grand_tot_devi += $grand_devi;
	                   		?>
	                   		<th width="40"><? echo number_format($grand_devi,0);?></th>
	                   		<?
	                   	}
	                	?>
    					<th width="50"><? echo number_format($grand_tot_devi,0);?></th>
    				</tr>
    				<?
    				?>
    				<tr>
    					<th width="50">Achieve. %</th>
    					<?
    					$grand_achive_prsnt = 0;
	                   	foreach ($date_range as $key => $value) 
	                  	{
	                  		$grand_target = $grand_total_arr[$value]['target'];
	                  		$grand_acv = $grand_total_arr[$value]['achivement'];
	                  		$grand_achive_prsnt = ($grand_target>0) ? ($grand_acv/$grand_target)*100 : 0;
	                  		// $grand_tot_achive_prsnt += $grand_achive_prsnt;
	                   		?>
	                   		<th width="40"><? echo number_format($grand_achive_prsnt,2);?></th>
	                   		<?
	                   	}
	                   	$grand_tot_achive_prsnt = ($grand_tot_target>0) ? ($grand_tot_acv/$grand_tot_target)*100 : 0;
	                	?>
    					<th width="50"><? echo number_format($grand_tot_achive_prsnt,2);?></th>
    				</tr>
    				<tr>
    					<th width="50">Eff. %</th>
    					<?
    					$grand_achive_effi = 0;
    					$grand_tot_prod_min = 0;
    					$grand_tot_effi_min = 0;
	                   	foreach ($date_range as $key => $value) 
	                  	{
	                  		$grand_produce_minit = $grand_total_arr[$value]['produce_minit'];
	                  		$grand_efficiency_min = $grand_total_arr[$value]['efficiency_min'];
	                  		$grand_achive_effi = ($grand_efficiency_min>0) ? ($grand_produce_minit / $grand_efficiency_min)*100 : 0;
	                  		// $grand_tot_achive_effi += $grand_achive_effi;
    						$grand_tot_prod_min += $grand_produce_minit;
    						$grand_tot_effi_min += $grand_efficiency_min;
	                   		?>
	                   		<th width="40"><? echo number_format($grand_achive_effi,2);?></th>
	                   		<?
	                   	}
	                   	$grand_tot_achive_effi = ($grand_tot_effi_min>0) ? ($grand_tot_prod_min / $grand_tot_effi_min)*100 : 0;
	                	?>
    					<th width="50"><? echo number_format($grand_tot_achive_effi,2);?></th>
    				</tr>
					
    				<tr>
    					<th width="50">DHU</th>
    					<?
    					$grand_dhu = 0;
	                   	foreach ($date_range as $key => $value) 
	                  	{	                  		
	                  		$grand_dhu += $grand_total_arr[$value]['dhu'];
	                   		?>
	                   		<th width="40"><? echo number_format($grand_total_arr[$value]['dhu'],0);?></th>
	                   		<?
	                   	}
	                	?>
    					<th width="50"><? echo number_format($grand_dhu,0);?></th>
    				</tr>
					
    				<tr>
    					<th width="50">Rejct qty</th>
    					<?
    					$grand_rej = 0;
	                   	foreach ($date_range as $key => $value) 
	                  	{	                  		
	                  		$grand_rej += $grand_total_arr[$value]['reject'];
	                   		?>
	                   		<th width="40"><? echo number_format($grand_total_arr[$value]['reject'],0);?></th>
	                   		<?
	                   	}
	                	?>
    					<th width="50"><? echo number_format($grand_rej,0);?></th>
    				</tr>
					
    				<tr>
    					<th width="50">Style Changed</th>
    					<?
    					$grand_style_cng = 0;
	                   	foreach ($date_range as $key => $value) 
	                  	{	                  		
	                  		$grand_style_cng += $grand_total_arr[$value]['style_cng'];
	                   		?>
	                   		<th width="40"><? echo number_format($grand_total_arr[$value]['style_cng'],0);?></th>
	                   		<?
	                   	}
	                	?>
    					<th width="50"><? echo number_format($grand_style_cng,0);?></th>
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

if($action=="tot_prod")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:520px; ">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<th width="30">SL</th>
                    <th width="120">Order No</th>
                    <th width="70">Item Smv</th>
                    <th width="100">Production Qnty</th>
                    <th width="100">Produced Min.</th>
				</thead>
              <?
			       $new_smv=array();
                   $item_smv_pop=explode("****",$item_smv);
				   $order_id="";
				   foreach($item_smv_pop as $po_id_smv) 
				     {
						   $po_id_smv_pop=explode("**",$po_id_smv);
						   $new_smv[$po_id_smv_pop[0]]=$po_id_smv_pop[1];
					 }
					
		$actual_date=date("Y-m-d");
	    $actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$prod_date)));
		if($db_type==0)
		{	
			$actual_date=date("Y-m-d");
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
			{
			   $sql_pop=sql_select("select  c.po_number,a.po_break_down_id,
			                sum(CASE WHEN a.production_hour>'$line_date'  and a.production_hour<='$actual_time'  and a.production_type=5 THEN a.production_quantity else 0 END)  as good_qnty 
						 
							from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
							where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and a.company_id=".$company_id."  and a.floor_id=".$floor_id." and a.sewing_line=".$sewing_line."  and a.po_break_down_id in(".$po_id.") and a.production_date='".$prod_date."'  group by c.po_number,a.po_break_down_id order by  c.po_number ");
			}
			if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date))
			{
			   	$sql_pop=sql_select("select  c.po_number,a.po_break_down_id,
			                sum(CASE WHEN a.production_type=5 THEN a.production_quantity else 0 END)  as good_qnty 
						 
							from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
							where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and a.company_id=".$company_id."  and a.floor_id=".$floor_id." and a.sewing_line=".$sewing_line."  and a.po_break_down_id in(".$po_id.") and a.production_date='".$prod_date."'  group by c.po_number,a.po_break_down_id order by  c.po_number ");
			}
		}
		else
		{
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
		 	{
	       		$sql_pop=sql_select("SELECT  c.po_number,a.po_break_down_id,
		                sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$line_date'  and TO_CHAR(a.production_hour,'HH24:MI')<='$actual_time'  and a.production_type=5 THEN d.production_qnty else 0 END)  as good_qnty 
					 
						from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c, pro_garments_production_dtls d
						where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.id=d.mst_id and d.is_deleted=0 and d.status_active=1 and a.status_active=1 and a.is_deleted=0 and a.company_id=".$company_id."  and a.floor_id=".$floor_id." and a.sewing_line=".$sewing_line."  and a.po_break_down_id in(".$po_id.") and a.production_date='".$prod_date."'  group by c.po_number,a.po_break_down_id order by  c.po_number ");
						
		 	}
		 	if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date))
		  	{
	
	       		$sql_pop=sql_select("SELECT  c.po_number,a.po_break_down_id,
		                sum(CASE WHEN  a.production_type=5 THEN d.production_qnty else 0 END)  as good_qnty 
					 
						from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c, pro_garments_production_dtls d
						where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.id=d.mst_id and d.is_deleted=0 and d.status_active=1 and a.status_active=1 and a.is_deleted=0 and a.serving_company=".$company_id."  and a.floor_id=".$floor_id." and a.sewing_line=".$sewing_line."  and a.po_break_down_id in(".$po_id.") and a.production_date='".$prod_date."'  group by c.po_number,a.po_break_down_id order by  c.po_number ");
						
					
		  	}
			
		}
		
         $subcon_production_data_arr=array();
		 foreach($sql_pop as $pro_val)
				{
				  $subcon_production_data_arr[$pro_val[csf('po_break_down_id')]][$pro_val[csf('po_number')]]['po_number']=$pro_val[csf('po_number')];	
				  $subcon_production_data_arr[$pro_val[csf('po_break_down_id')]][$pro_val[csf('po_number')]]['po_qty']=$pro_val[csf('good_qnty')];	
                  $subcon_production_data_arr[$pro_val[csf('po_break_down_id')]][$pro_val[csf('po_number')]]['item_smv']=$new_smv[$pro_val[csf('po_break_down_id')]];	
					
				}
				
		if($subcon_order!="")
		{
	         if($db_type==0)
			 {
			 if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
		      {
	             $sql_subcon=sql_select("select  
						       a.order_id,c.smv,
					           c.order_no as po_number,
						 	 sum(CASE WHEN a.hour>'$line_date' and a.hour<='$actual_time' and a.production_type=2  THEN a.production_qnty else 0 END) AS good_qnty
						     from subcon_gmts_prod_dtls a, subcon_ord_dtls c
						       where a.production_type=2 and a.order_id=c.id  and a.status_active=1 and a.is_deleted=0  and a.company_id=".$company_id."  and a.floor_id=".$floor_id." and a.line_id=".$sewing_line."  and a.order_id in(".$subcon_order.") and a.production_date='".$prod_date."'                         	   group by a.order_id, c.order_no,c.smv");
			  }
			  if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date))
		         {
					
	             $sql_subcon=sql_select("select  
						       a.order_id,c.smv,
					           c.order_no as po_number,
						 	   sum(a.production_qnty ) AS good_qnty
						       from subcon_gmts_prod_dtls a, subcon_ord_dtls c
						       where a.production_type=2 and a.order_id=c.id  and a.status_active=1 and a.is_deleted=0  and a.company_id=".$company_id."  and a.floor_id=".$floor_id." and a.line_id=".$sewing_line."  and a.order_id in(".$subcon_order.") and a.production_date='".$prod_date."'                         	   group by a.order_id, c.order_no,c.smv");
			     }
			 }
			 else
			 {
			  if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
		         {
				  $sql_subcon=sql_select("select  
						       a.order_id,c.smv,
					           c.order_no as po_number,
							   sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')>'$line_date' and TO_CHAR(a.hour,'HH24:MI')<='$actual_time' and a.production_type=2  THEN a.production_qnty else 0 END) AS good_qnty
						     from subcon_gmts_prod_dtls a, subcon_ord_dtls c
						       where a.production_type=2 and a.order_id=c.id  and a.status_active=1 and a.is_deleted=0  and a.company_id=".$company_id."  and a.floor_id=".$floor_id." and a.line_id=".$sewing_line."  and a.order_id in(".$subcon_order.") and a.production_date='".$prod_date."'                         	   group by a.order_id, c.order_no,c.smv"); 
				 }
				 
				if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date))
		         {
					
					 
				  $sql_subcon=sql_select("select  
						       a.order_id,c.smv,
					           c.order_no as po_number,
						 	   sum(a.production_qnty) AS good_qnty
						       from subcon_gmts_prod_dtls a, subcon_ord_dtls c
						       where a.production_type=2 and a.order_id=c.id  and a.status_active=1 and a.is_deleted=0  and a.company_id=".$company_id."  and a.floor_id=".$floor_id." and a.line_id=".$sewing_line."  and a.order_id in(".$subcon_order.") and a.production_date='".$prod_date."'                         	   group by a.order_id, c.order_no,c.smv"); 
				 }
			 }
		}
		 foreach($sql_subcon as $sub_val)
				{
				  $subcon_production_data_arr[$sub_val[csf('order_id')]][$sub_val[csf('po_number')]]['po_number']=$sub_val[csf('po_number')];	
				  $subcon_production_data_arr[$sub_val[csf('order_id')]][$sub_val[csf('po_number')]]['po_qty']=$sub_val[csf('good_qnty')];	
                  $subcon_production_data_arr[$sub_val[csf('order_id')]][$sub_val[csf('po_number')]]['item_smv']=$sub_val[csf('smv')];	
					
				}		   
							   
					//print_r($subcon_production_data_arr);
                 
					
			$total_producd_min=0;
			$i=1; $total_qnty=0;
			foreach($subcon_production_data_arr as $sub_id=>$pop_val)
			{
			foreach($pop_val as $po_id=>$pop_val)
                    {
					
                       if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                       else
                            $bgcolor="#FFFFFF";	
                    
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="120" align="center"><? echo $pop_val['po_number']; ?></td>
                            <td align="right"><? echo $pop_val['item_smv']; ?>&nbsp;</td>
                            <td align="right"><? $total_po_qty+=$pop_val['po_qty']; echo $pop_val['po_qty']; ?>&nbsp;</td>
                            <td align="right">
							     <?
								   $producd_min=$pop_val['po_qty']*$pop_val['item_smv'];  $total_producd_min+=$producd_min;
								  echo number_format($producd_min,2);
								  ?>&nbsp;</td>
                        </tr>
                    <?
                    $i++;
                    }
			}
                    ?>
                    <tfoot>
                        <th colspan="3" align="right">Total</th>
                       
                        <th align="right"><? echo $total_po_qty; ?>&nbsp;</th>
                        <th align="right"><? echo number_format($total_producd_min,2); ?>&nbsp;</th>
                    </tfoot>
                </table>
           
        </div>
	</fieldset>   
	<?
	exit();
} 
?>