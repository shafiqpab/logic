<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.others.php');

if (!function_exists('pre')) 
{
	function pre($array){
		echo "<pre>";
		print_r($array);
		echo "</pre>";
	}
}


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
	echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/hourly_production_monitoring_report_tg_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_floor', 'floor_td' );",0 );
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
	?>
	<style type="text/css">
		/*.block_div {
				width:auto;
				height:auto;
				text-wrap:normal;
				vertical-align:bottom;
				display: block;
				position: !important;
				-webkit-transform: rotate(-90deg);
				-moz-transform: rotate(-90deg);
		}*/
		table tr th,table tr td
		{
			word-wrap: break-word;
			word-break: break-all;
		}

   </style>
	<?
	extract($_REQUEST);
	$process = array( &$_POST );

	//change_date_format($txt_date);die;

	if($db_type==0)	$txt_date=change_date_format($txt_date,'yyyy-mm-dd');
	if($db_type==2)	$txt_date=change_date_format($txt_date,'','',1);
	$txt_date="'".$txt_date."'";

	extract(check_magic_quote_gpc( $process ));
	$companyArr = return_library_array("select id,company_name from lib_company","id","company_name");
	$buyerArr = return_library_array("select id,short_name from lib_buyer","id","short_name");
	$locationArr = return_library_array("select id,location_name from lib_location","id","location_name");
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
	$lineArr = return_library_array("select id,line_name from lib_sewing_line where company_name=$cbo_company_id","id","line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	$company_id=str_replace("'","",$cbo_company_id);
    $today_date=date("Y-m-d");
	$txt_producting_day="".str_replace("'","",$txt_date)."";

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
	// $hour=substr($start_time[0],1,1);
	$hour=$start_time[0]*1;
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
	$hour_line=$first_hour_time[0]*1; $minutes_one=$start_time[1];
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
	/										prod resource data								/
	/===================================================================================== */
	// echo $prod_reso_allo[0]; die;
	$prod_res_cond = "";
	$prod_res_cond .= (str_replace("'", "", $cbo_location_id)==0) ? "" : " and a.location_id=$cbo_location_id";
	$prod_res_cond .= (str_replace("'", "", $cbo_floor_id)==0) ? "" : " and a.floor_id=$cbo_floor_id";
	$prod_res_cond .= (str_replace("'", "", $hidden_line_id)=="") ? "" : " and a.id in(".str_replace("'", "", $hidden_line_id).")";

	if($prod_reso_allo[0]==1)
	{
		$prod_resource_array=array();
		$prod_resource_array2=array();
		$prod_resource_smv_array = array();

		$dataArray_sql=sql_select("SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id=$company_id and b.pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $prod_res_cond");

		foreach($dataArray_sql as $val)
		{
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['man_power']=$val[csf('man_power')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['operator']=$val[csf('operator')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['helper']=$val[csf('helper')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['terget_hour']=$val[csf('target_per_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['working_hour']=$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['tpd']=$val[csf('target_per_hour')]*$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_start']=$val[csf('from_date')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_end']=$val[csf('to_date')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['capacity']=$val[csf('capacity')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust']=$val[csf('smv_adjust')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];
		}
		// echo "<pre>";print_r($production_serial_arr);die();

		// =======================================================
		$sql="SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, c.from_date,c.to_date,c.capacity,d.po_id,d.target_per_line,d.operator, d.helper,d.working_hour,d.actual_smv,d.gmts_item_id from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c,prod_resource_color_size d where a.id=c.mst_id and c.id=b.mast_dtl_id and d.mst_id=a.id and d.dtls_id=c.id and a.company_id=$company_id and b.pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $prod_res_cond";
		// echo $sql;die();
		$sql_res=sql_select($sql);
		$poIds_arr = array();
		foreach($sql_res as $vals)
		{
			$poIds_arr[$vals[csf('po_id')]] = $vals[csf('po_id')];
		}
		$poIds = implode(",", $poIds_arr);
		$job_no_arr = return_library_array("SELECT a.job_no,b.id from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and a.status_active=1 and b.status_active=1 and b.id in($poIds)","id","job_no");
		foreach($sql_res as $val)
		{
			$prod_resource_array2[$val[csf('id')]][$job_no_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['man_power']+=$val[csf('operator')]+$val[csf('helper')];
			$prod_resource_array2[$val[csf('id')]][$job_no_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['operator']+=$val[csf('operator')];
			$prod_resource_array2[$val[csf('id')]][$job_no_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['helper']+=$val[csf('helper')];
			$prod_resource_array2[$val[csf('id')]][$job_no_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['terget_hour']+=$val[csf('target_per_line')];
			$prod_resource_array2[$val[csf('id')]][$job_no_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['working_hour']+=$val[csf('working_hour')];
			$prod_resource_array2[$val[csf('id')]][$job_no_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['tpd']+=$val[csf('target_per_line')]*$val[csf('working_hour')];
			$prod_resource_array2[$val[csf('id')]][$job_no_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['day_start']=$val[csf('from_date')];
			$prod_resource_array2[$val[csf('id')]][$job_no_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['day_end']=$val[csf('to_date')];
			$prod_resource_array2[$val[csf('id')]][$job_no_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['capacity']+=$val[csf('capacity')];
			$prod_resource_array2[$val[csf('id')]][$job_no_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['smv_adjust']+=$val[csf('smv_adjust')];
			$prod_resource_array2[$val[csf('id')]][$job_no_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];
			$prod_resource_smv_array[$val[csf('id')]][$job_no_arr[$val[csf('po_id')]]][$val[csf('gmts_item_id')]][$val[csf('pr_date')]]['actual_smv']=$val[csf('actual_smv')];


		}

		// echo "<pre>"; print_r($prod_resource_array2);die();

		if(str_replace("'","",trim($txt_date))==""){$pr_date_con="";}else{$pr_date_con=" and b.pr_date=$txt_date";}

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
		$sql_query="SELECT b.mst_id, b.pr_date,b.number_of_emp ,b.adjust_hour,b.total_smv,b.adjustment_source,b.style_ref_no as style from prod_resource_mst a,prod_resource_smv_adj b  where a.id=b.mst_id  and a.company_id=$company_id and b.pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 and b.status_active=1  $prod_res_cond";
		// echo $sql_query;
		$sql_query_res=sql_select($sql_query);
		foreach($sql_query_res as $val)
		{
			$val[csf('pr_date')]=date("d-M-Y",strtotime($val[csf('pr_date')]));

			$prod_resource_smv_adj_array[$val[csf('style')]][$val[csf('mst_id')]][$val[csf('pr_date')]][$val[csf('adjustment_source')]]['number_of_emp']+=$val[csf('number_of_emp')];
			$prod_resource_smv_adj_array[$val[csf('style')]][$val[csf('mst_id')]][$val[csf('pr_date')]][$val[csf('adjustment_source')]]['adjust_hour']+=$val[csf('adjust_hour')];
			$prod_resource_smv_adj_array[$val[csf('style')]][$val[csf('mst_id')]][$val[csf('pr_date')]][$val[csf('adjustment_source')]]['total_smv']+=$val[csf('total_smv')];

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

	$search_prod_date=change_date_format(str_replace("'","",$txt_date));
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
		$sql="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name  as buyer_name,b.style_ref_no,b.job_no, a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.file_no,c.unit_price,c.grouping as ref,d.color_type_id,a.remarks,e.floor_serial_no,sum(d.production_qnty) as good_qnty,";
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
		FROM  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a ,pro_garments_production_dtls d,lib_prod_floor e
		WHERE a.production_type=5 and a.po_break_down_id=c.id and c.job_id=b.id and a.id=d.mst_id and e.id=a.floor_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_name $location $floor $line $buyer_id_cond  $txt_date_from
		GROUP BY b.job_no, a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name,b.style_ref_no,a.item_number_id,c.po_number,c.unit_price,c.file_no,c.grouping ,d.color_type_id,a.remarks,e.floor_serial_no
		ORDER BY a.location,e.floor_serial_no";
	}
	// echo $sql;die;
	$sql_resqlt=sql_select($sql);
	$production_data_arr=array();
	$production_po_data_arr=array();
	$production_serial_arr=array();
	$reso_line_ids='';
	$all_po_id="";
	$active_days_arr=array();
	$duplicate_date_arr=array();
	$poIdArr=array();
	$prod_line_array = array();
	$line_style_chk_array = array();
	$line_wise_style_array = array();
	foreach($sql_resqlt as $val)
	{
		$prod_line_array[$val[csf('sewing_line')]] = $val[csf('sewing_line')];
		$poIdArr[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
		if($val[csf('prod_reso_allo')]==1)
		{
			$sewing_line_ids=$prod_reso_arr[$val[csf('sewing_line')]];
			$sl_ids_arr = explode(",", $sewing_line_ids);
			$sewing_line_id = $sl_ids_arr[0]; // always 1st line id will take

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
		$production_serial_arr[$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]][$val[csf('job_no')]]=$val[csf('style_ref_no')];

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
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]][$prod_hour]+=$val[csf($prod_hour)];

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

	 	$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['prod_hour23']+=$val[csf('prod_hour23')];
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['prod_reso_allo']=$val[csf('prod_reso_allo')];

	 	if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['buyer_name']!="")
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['buyer_name'].=",".$val[csf('buyer_name')];
		}
	 	else
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['buyer_name']=$val[csf('buyer_name')];
		}

		if($line_style_chk_array[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]] =="")
		{
			$line_wise_style_count_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]++;
			$line_wise_style_count_arr2[$val[csf('floor_id')]][$val[csf('sewing_line')]]++;
			$line_style_chk_array[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]] = $val[csf('job_no')];
		}

		/*if($line_wise_style_count_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]!="")
		{
			$line_wise_style_count_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]].=",".$val[csf('buyer_name')];
		}
	 	else
		{
			$line_wise_style_count_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]=$val[csf('buyer_name')];
		}*/

		if($line_wise_style_array[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style']!="")
		{
			$line_wise_style_array[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style'].="##".$val[csf('job_no')];
		}
	 	else
		{
			$line_wise_style_array[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style']=$val[csf('job_no')];
		}


	 	if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['po_number']!="")
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['po_number'].=",".$val[csf('po_number')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['job_no'].=",".$val[csf('job_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['po_id'].=",".$val[csf('po_break_down_id')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['style'].="##".$val[csf('style_ref_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['file'].=",".$val[csf('file_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['ref'].=",".$val[csf('ref')];
		}
	 	else
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['po_number']=$val[csf('po_number')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['job_no']=$val[csf('job_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['po_id']=$val[csf('po_break_down_id')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['style']=$val[csf('style_ref_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['file']=$val[csf('file_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['ref']=$val[csf('ref')];
		}
		if ($val[csf('remarks')] !="")
		{
		 	if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['remarks']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['remarks'].=",".$val[csf('remarks')];
			}
		 	else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['remarks']=$val[csf('remarks')];
			}
		}

		if($po_rate_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['rate']!="")
		{
			$po_rate_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['rate'].=",".$val[csf('unit_price')];
		}
		else
		{
			$po_rate_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['rate'] = $val[csf('unit_price')];
		}

		if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['item_number_id']!="")
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('unit_price')];
		}
		else
		{
			 $production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('unit_price')];
		}
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['quantity']+=$val[csf('good_qnty')];
		$production_data_arr_qty[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]['quantity']+=$val[csf('good_qnty')];

		if($all_po_id=="") $all_po_id=$val[csf('po_break_down_id')]; else $all_po_id.=",".$val[csf('po_break_down_id')];
	}



	// $production_serial_arr[$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]=$val[csf('sewing_line')];
	// $production_serial_arr[5000][1][8000][0]=8000;




	// echo "<pre>"; print_r($production_data_arr);die();
	/*===================================================================================== /
	/										po item wise smv 								/
	/===================================================================================== */
	$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no  and b.id in($all_po_id) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3)";
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
    $po_active_sql="SELECT a.floor_id,a.sewing_line,a.production_date,a.po_break_down_id,a.item_number_id from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $company_name $location $floor $line $buyer_id_cond  $poIds_cond2 group by  a.floor_id,a.sewing_line,a.production_date ,a.po_break_down_id,a.item_number_id";
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
	$sql_item_rate="SELECT b.id, c.item_number_id, c.order_quantity, c.order_total from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c where b.job_no_mst=a.job_no and b.id=c.po_break_down_id and b.job_no_mst=c.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1  $file_cond $ref_cond  $poIds_cond";
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
   		$sql_sub_contuct.="sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.id and d.is_deleted=0 left join lib_sewing_line e on a.line_id=e.id and e.is_deleted=0 where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id=$company_id $subcon_location $floor $subcon_line   $txt_date_from group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date,a.prod_reso_allo,a.line_id,b.party_id,c.order_no,c.cust_style_ref,b.subcon_job ,e.sewing_line_serial order by a.location_id,d.floor_serial_no,e.sewing_line_serial,a.prod_reso_allo";
	}
	else
	{
		$sql_sub_contuct= "SELECT a.company_id, a.location_id, a.floor_id,d.floor_serial_no,e.sewing_line_serial, a.production_date, a.line_id,b.party_id  as buyer_name,a.prod_reso_allo,a.order_id,c.order_no as po_number,c.cust_style_ref, b.subcon_job as job_no,  max(c.smv) as smv,a.prod_reso_allo,sum(a.production_qnty) as good_qnty,c.gmts_item_id as item,";
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

		$sql_sub_contuct.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]'	and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.id  and d.is_deleted=0 left join lib_sewing_line e on a.line_id=e.id and e.is_deleted=0 where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id=$company_id $subcon_location $floor $subcon_line   $txt_date_from group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date,  b.subcon_job,a.line_id,b.party_id,c.order_no,c.cust_style_ref,e.sewing_line_serial,a.prod_reso_allo,c.gmts_item_id order by a.location_id, a.floor_id,e.sewing_line_serial,a.prod_reso_allo";

	}
	//echo $sql_sub_contuct;die;
	$sub_result=sql_select($sql_sub_contuct);
	$subcon_order_smv=array();
	foreach($sub_result as $subcon_val)
	{
		$subcon_job_no 	= $subcon_val['JOB_NO'];
		$subcon_floor 	= $subcon_val['FLOOR_ID'];
		$subcon_line_id = $subcon_val['LINE_ID'];
		$subcon_style 	= $subcon_val['CUST_STYLE_REF'];
		$subcon_po_id 	= $subcon_val['ORDER_ID'];
		$subcon_item 	= $subcon_val['ITEM'];

		$prod_line_array[$subcon_line_id] = $subcon_line_id;
		if($subcon_val[csf('prod_reso_allo')]==1)
		{
			$sewing_line_id=$prod_reso_arr[$subcon_line_id];
		}
		else
		{
			$sewing_line_id=$subcon_line_id;
		}

		if($lineSerialArr[$sewing_line_id]=="")
		{
			$lastSlNo++;
			$slNo=$lastSlNo;
			$lineSerialArr[$sewing_line_id]=$slNo;
		}
		else $slNo=$lineSerialArr[$sewing_line_id];
		
		$production_serial_arr[$subcon_floor][$slNo][$subcon_line_id][$subcon_job_no]= $subcon_style; 

	 	$line_start=$line_number_arr[$subcon_line_id][$subcon_val[csf('production_date')]]['prod_start_time']	;
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
			$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_style][$prod_hour]+=$subcon_val[csf($prod_hour)];
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
			{
				 if( $h>=$line_start_hour && $h<=$actual_time)
				 {
				 $production_po_data_arr[$subcon_floor][$subcon_line_id][$subcon_po_id]+=$val[csf($prod_hour)];	                 }
			}
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
			{
				$production_po_data_arr[$subcon_floor][$subcon_line_id][$subcon_po_id]+=$val[csf($prod_hour)];	            }
		 }
		if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
		{
			if( $h>=$line_start_hour && $h<=$actual_time)
			{
				$production_po_data_arr[$subcon_floor][$subcon_line_id][$subcon_po_id]+=$val[csf('prod_hour23')];
			}
		}
		else
		{
			$production_po_data_arr[$subcon_floor][$subcon_line_id][$subcon_po_id]+=$val[csf('prod_hour23')];
		}
		$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_style]['prod_hour23']+=$val[csf('prod_hour23')];


		// ============================================================================================
												// SUBCON ORDER INFORMATION
		// ============================================================================================
		$production_po_data_arr[$subcon_floor][$subcon_line_id][$subcon_po_id]+=$subcon_val[csf('good_qnty')];
		if($production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['buyer_name']!="")
		{
			$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['buyer_name'].=",".$subcon_val[csf('buyer_name')];
		}
		else
		{
			$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['buyer_name']=$subcon_val[csf('buyer_name')];
		}
		if($line_style_chk_array[$subcon_floor][$subcon_line_id][$subcon_style] =="")
		{
			$line_wise_style_count_arr[$subcon_floor][$subcon_line_id]++;
			$line_wise_style_count_arr2[$subcon_floor][$subcon_line_id]++;
			$line_style_chk_array[$subcon_floor][$subcon_line_id][$subcon_style] = $subcon_style;
		}

		if($production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['po_number']!="")
		{
			$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['po_number'].=",".$subcon_val[csf('po_number')];
			$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['job_no'].=",".$subcon_job_no;
			$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['style'].="##".$subcon_style; 
		}
		else
		{
			$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['po_number']=$subcon_val[csf('po_number')];
			$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['job_no']=$subcon_job_no;
			$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['style']=$subcon_style;
		} 
		if($production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['item_number_id']!="")
		{
			$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['item_number_id'].="****".$subcon_po_id."**".$subcon_item; //."**".$v['UNIT_PRICE']
		}
		else
		{
			$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['item_number_id']=$subcon_po_id."**".$subcon_item; //."**".$v['UNIT_PRICE']
		} 
		if($production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['order_id']!="")
		{
			$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['order_id'].=",".$subcon_po_id;
		}
		else
		{
			$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['order_id'].=$subcon_po_id;
		}
		$subcon_order_smv[$subcon_po_id]=$subcon_val[csf('smv')];
		$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_style]['quantity']+=$subcon_val[csf('good_qnty')];											
	}

	/*===================================================================================== /
	/							prod resource data no prod line								/
	/===================================================================================== */
	// echo $prod_reso_allo[0]; die;

	$prod_line_ids = implode(",", $prod_line_array);

	if($prod_reso_allo[0]==1)
	{
		$dataArray_sql=sql_select("SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity,d.floor_serial_no from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c,lib_prod_floor d where a.id=c.mst_id and c.id=b.mast_dtl_id and d.id=a.floor_id and a.company_id=$company_id and b.pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $prod_res_cond and a.id not in($prod_line_ids) order by d.floor_serial_no ");

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
			$production_serial_arr[$val[csf('floor_id')]][$slNo][$val[csf('id')]][]='';//$val[csf('id')];
			$production_serial_arr2[$val[csf('floor_id')]][$slNo][$val[csf('id')]]='';//$val[csf('id')];
		}
	}

	/*===================================================================================== /
	/							For Summary Report New Add No Prodcut						/
	/===================================================================================== */
	if($cbo_no_prod_type==1)
	{
		//No Production line Start ....
		$prod_reso_arr=return_library_array( "SELECT id, line_number from prod_resource_mst",'id','line_number');
		$sql_active_line=sql_select("SELECT sewing_line,sum(production_quantity) as total_production from pro_garments_production_mst  where production_date=".$txt_date." and production_type=5 and status_active=1 and is_deleted=0 and prod_reso_allo=1 group by  sewing_line");
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
		if(str_replace("'","",$cbo_location_id)==0)
		{
		$location_cond="";
		}
		else
		{
		$location=" and a.location_id=".str_replace("'","",$cbo_location_id)."";
		}

		if(str_replace("'","",$cbo_floor_id)==0) $floor=""; else $floor="and a.floor_id=".str_replace("'","",$cbo_floor_id)."";
		$lin_ids=str_replace("'","",$hidden_line_id);
		$res_line_cond=rtrim($reso_line_ids,",");

		 $dataArray_sum=sql_select("SELECT a.id, a.floor_id,1 as prod_reso_allo,2 as type_line, a.line_number as line_no, b.man_power, b.operator, b.helper, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type,d.prod_start_time from  prod_resource_dtls b,prod_resource_dtls_time d,prod_resource_mst a  where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$company_id and b.pr_date=".$txt_date." and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 and a.id not in($res_line_cond) and a.line_number like '%$lin_ids%'  $location  $floor  group by a.id, a.floor_id, a.line_number, d.prod_start_time,b.man_power, b.operator, b.helper, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type order by a.floor_id");
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
		 $dataArray_sql_cap=sql_select("select  a.floor_id, a.line_number as line_no,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id  and a.company_id=$company_id and b.pr_date=".$txt_date."  and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  group by a.id,a.line_number, a.floor_id, a.line_number, b.man_power, b.operator,c.capacity, b.helper, b.working_hour,b.target_per_hour order by a.floor_id");

		 //$prod_resource_array_summary=array();
		 foreach( $dataArray_sql_cap as $row)
		 {
			 $production_data_arr[$row[csf('floor_id')]][$row[csf('line_no')]]['capacity']=$row[csf('capacity')];
		 }

	} //End

	//echo "<pre>";
	// echo "<pre>"; print_r($production_serial_arr);die;

	$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per");
	$condition= new condition();
	if($cbo_company_name>0){
		$condition->company_name("=$company_id");
	}
	if(count($poIdArr)>0)
	{
		$condition->po_id_in(implode(',',$poIdArr));
	}
	$condition->init();
	$other= new other($condition);
	$other_cost = $other->getAmountArray_by_job();
	// $other_cost[$jobNumber]['cm_cost'];
	// echo "<pre>"; print_r($production_serial_arr);die();
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
	// echo "<pre>"; print_r($rowspan);die();
	// ======================================
    $avable_min=0;
	$today_product=0;
    $floor_name="";
    $floor_man_power=0;
	$floor_operator=$floor_produc_min=0;
	$floor_smv=$floor_row=$floor_helper=$floor_tgt_h=$floor_days_run=$floor_working_hour=$line_floor_production=$floor_today_product=$floor_avale_minute=$floor_line_days_run=0;
	$floor_prod_hour = 0;
	$floor_tot_prod = 0;
	$floor_line_acv = 0;
	$floor_target_gap = 0;
	$floor_target_min = 0;
	$floor_target_effi = 0;
	$floor_achive_effi = 0;
	$floor_efficiency_gap = 0;
	$floor_style_change = 0;
	$floor_avg_cm = 0;
	$floor_ttl_cm = 0;
	$floor_target_cm = 0;
	$floor_avg_rate = 0;
	$floor_ttl_fob_val = 0;
	$floor_target_value_fob = 0;

	$total_operator=$total_helper=$gnd_hit_rate=0;
    $total_smv=$total_terget=$grand_total_product=$gnd_line_effi=0;
    $total_man_power=$gnd_avable_min=$gnd_product_min=0;
	$item_smv=$item_smv_total=$line_efficiency=$days_run=$days_active=$total_working_hour=$gnd_total_tgt_h=$total_capacity=0;

	$total_prod_hour = 0;
	$total_tot_prod = 0;
	$total_line_acv = 0;
	$total_target_gap = 0;
	$total_target_min = 0;
	$total_target_effi = 0;
	$total_achive_effi = 0;
	$total_efficiency_gap = 0;
	$total_style_change = 0;
	$total_avg_cm = 0;
	$total_ttl_cm = 0;
	$total_target_cm = 0;
	$total_avg_rate = 0;
	$total_ttl_fob_val = 0;
	$total_target_value_fob = 0;

	$j=1;
	$i=1;
	ob_start();
	$line_number_check_arr=array();
	$smv_for_item="";
	$total_production=array();
	$floor_production=array();
    $line_floor_production=0;
    $line_total_production=0; $gnd_total_fob_val=0; $gnd_final_total_fob_val=0;
    $f_chk_arr = array();
    $line_chk_arr = array();
    $line_chk_arr2 = array();
    $html ='<tbody>';
	foreach($production_serial_arr as $f_id=>$fname)
	{
		ksort($fname);
		foreach($fname as $sl=>$s_data)
		{
			foreach($s_data as $l_id=>$ldata)
			{
				$l=0;
				$pp = 0;
				$lc = 0;
				foreach ($ldata as $job_key => $style_data)
				{
				  	$po_value=$production_data_arr[$f_id][$l_id][$job_key]['po_number'];
				 	//  if($po_value)
					// {
						if($i!=1)
						{
							if(!in_array($f_id, $check_arr))
							{
								if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								 $html.='<tr  bgcolor="#B6B6B6">
										<td class="break_all" width="20">&nbsp;</td>
										<td class="break_all" width="50">&nbsp;</td>
										<td class="break_all" width="100">&nbsp;</td>
										<td class="break_all" width="100">&nbsp;</td>
										<td class="break_all" width="75">&nbsp;</td>
										<td class="break_all" width="80" title="'.$f_id.'"><p><b>Floor Total('.$floorArr[current($f_chk_arr)].')</b><p></td>

										<td class="break_all" align="right" width="30">&nbsp;</td>
										<td class="break_all" align="right" width="50">'.$floor_tgt_h.'</td>
										<td class="break_all" align="right" width="50">'. $floor_working_hour.'</td>
										<td class="break_all" align="right" width="50">'. $floor_prod_hour.'</td>
										<td class="break_all" align="right" width="50">'. $floor_operator.'</td>
										<td class="break_all" align="right" width="50">'. $floor_helper.'</td>
										<td class="break_all" align="right" width="50">'. $floor_man_power.'</td>
										<td class="break_all" align="right" width="50">'. $floor_days_run.'</td>
										<td class="break_all" align="right" width="50">'. number_format($eff_target_floor,0).'</td>';
										$p=1;
										for($k=$hour; $k<=$last_hour; $k++)
										{
											$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
											if($p <= 11)
											{
												$html.='<td class="break_all" align="right" width="36" style='.$bg_color_.' >'. $floor_production[$prod_hour].'</td>';
											}
											/*elseif ($floor_production[$prod_hour]>0 && $p>11)
											{
												$html.='<td class="break_all" align="right" width="70" style='.$bg_color_.' >'. $floor_production[$prod_hour].'</td>';
											}*/
											$p++;
										}
										$html.='<td class="break_all" align="right" width="50">'.number_format($floor_tot_prod,0).'</td>
										<td class="break_all" align="right" width="50">'.number_format((($floor_tot_prod/$eff_target_floor)*100),0).'%</td>
										<td class="break_all" align="right" width="50">'.number_format($floor_target_gap,0).'</td>
										<td class="break_all" align="right" width="50">'.number_format($floor_target_min,0).'</td>
										<td class="break_all" align="right" width="80">'.number_format($floor_avale_minute,2).'</td>
										<td class="break_all" align="right" width="50">'.number_format($floor_produc_min,0).'</td>
										<td class="break_all" align="right" width="50">'.number_format((($floor_target_min/$floor_avale_minute)*100),0).'%</td>
										<td class="break_all" align="right" width="50">'. number_format((($floor_produc_min/$floor_avale_minute)*100),0).'%</td>
										<td class="break_all" align="right" width="50">'. number_format((($floor_target_min/$floor_avale_minute)*100) - (($floor_produc_min/$floor_avale_minute)*100),0).'%</td>
										<td class="break_all" align="right" width="50">'. number_format($floor_style_change,0).'</td>
										<td class="break_all" align="right" width="180"></td>

										<td class="break_all" width="50" align="right">'.number_format($floor_avg_cm,2).'</td>
										<td class="break_all" width="50" align="right">'.number_format($floor_ttl_cm,2).'</td>
										<td class="break_all" width="50" align="right">'.number_format($floor_target_cm,2).'</td>
										<td class="break_all" width="50" align="right">'.number_format($floor_avg_rate,2).'</td>
										<td class="break_all" width="80" align="right">'.number_format($floor_ttl_fob_val,2).'</td>
										<td class="break_all" width="80" align="right">'.number_format($floor_target_value_fob,2).'</td>';

									$gnd_total_fob_val=0;

									$html.='</tr>';
								  	$floor_name="";
								  	$floor_smv=0;
								  	$floor_row=0;
								  	$floor_operator=0;
								  	$floor_helper=0;
								  	$floor_tgt_h=0;
								  	$floor_man_power=0;
								  	$floor_days_run=0;
								  	$floor_line_days_run=0;
								  	$eff_target_floor=0;
								  	unset($floor_production);
								  	$floor_working_hour=0;
								  	$line_floor_production=0;
								  	$floor_today_product=0;
								  	$floor_avale_minute=0;
								  	$floor_produc_min=0;
								  	$floor_efficency=0;
								  	$floor_man_power=0;
								  	$floor_capacity=0;
								  	$floor_prod_hour = 0;
									$floor_tot_prod = 0;
									$floor_line_acv = 0;
									$floor_target_gap = 0;
									$floor_target_min = 0;
									$floor_target_effi = 0;
									$floor_achive_effi = 0;
									$floor_efficiency_gap = 0;
									$floor_style_change = 0;
									$floor_avg_cm = 0;
									$floor_ttl_cm = 0;
									$floor_target_cm = 0;
									$floor_avg_rate = 0;
									$floor_ttl_fob_val = 0;
									$floor_target_value_fob = 0;
								  	$j++;
								  	unset($f_chk_arr);
							}
						}
						$floor_row++;
						//$item_ids=$production_data_arr[$f_id][$l_id]['item_number_id'];
						$germents_item=array_unique(explode('****',$production_data_arr[$f_id][$l_id][$job_key]['item_number_id']));
						// print_r($germents_item);die();

						$buyer_neme_all=array_unique(explode(',',$production_data_arr[$f_id][$l_id][$job_key]['buyer_name']));
						$buyer_name="";
						foreach($buyer_neme_all as $buy)
						{
							if($buyer_name!='') $buyer_name.=',';
							$buyer_name.=$buyerArr[$buy];
						}
						$garment_itemname='';
						$active_days='';
						$item_smv="";$item_ids='';
						$smv_for_item="";
						$produce_minit="";
						$order_no_total="";
						$efficiency_min=0;
						$tot_po_qty=0;
						$fob_val=0;

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
							$item_smv.=$prod_resource_smv_array[$l_id][$job_key][$po_garment_item[1]][$pr_date]['actual_smv'];
							if($order_no_total!="") $order_no_total.=",";
							$order_no_total.=$po_garment_item[0];
							if($smv_for_item!="") $smv_for_item.="****".$po_garment_item[0]."**".$item_smv;
							else
							$smv_for_item=$po_garment_item[0]."**".$item_smv;
							$produce_minit+=$production_po_data_arr[$f_id][$l_id][$po_garment_item[0]]*$prod_resource_smv_array[$l_id][$job_key][$po_garment_item[1]][$pr_date]['actual_smv'];
							// echo $production_po_data_arr[$f_id][$l_id][$po_garment_item[0]]."*".$prod_resource_smv_array[$l_id][$job_key][$po_garment_item[1]][$pr_date]['actual_smv']."<br>";
							$fob_rate=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt']/$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];
							$prod_qty=$production_data_arr_qty[$f_id][$l_id][$po_garment_item[0]][$po_garment_item[1]]['quantity'];
							//echo $prod_qty.'<br>';
							if(is_nan($fob_rate)){ $fob_rate=0; }
							$fob_val+=$prod_qty*$fob_rate;

						}

						$po_id_arr = array_unique(explode(",", $production_data_arr[$f_id][$l_id][$job_key]['po_id']));
						$po_rate ="";
						foreach ($po_id_arr as $po_val)
						{
							if($po_rate!="") $po_rate.=",";
							$po_rate.=$po_rate_data_arr[$f_id][$l_id][$po_val]['rate'];
						}
						// echo $po_rate."<br>";


						$subcon_po_id=array_unique(explode(',',$production_data_arr[$f_id][$l_id][$job_key]['order_id']));
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
							$produce_minit+=$production_po_data_arr[$f_id][$l_id][$sub_val]*$subcon_order_smv[$sub_val];
							if($subcon_order_id!="") $subcon_order_id.=",";
							$subcon_order_id.=$sub_val;
						}

						if($order_no_total!="")
						{
							$day_run_sql=sql_select("select min(production_date) as min_date from pro_garments_production_mst
							where po_break_down_id in(".$order_no_total.")  and production_type=4");
							foreach($day_run_sql as $row_run)
							{
								$sewing_day=$row_run[csf('min_date')];
							}
							/*if($sewing_day!="")
							{
								// $days_run=datediff("d",$sewing_day,$pr_date);
								$date1=date_create($sewing_day);
								$date2=date_create($pr_date);
								$diff=date_diff($date1,$date2);
								$days_run = $diff->format("%R%a days");
							}
							else  $days_run=0;*/


							$lineWiseProMinDateSql="SELECT min(production_date) as MIN_DATE,FLOOR_ID,SEWING_LINE from pro_garments_production_mst where production_type=4 and po_break_down_id in(".$order_no_total.") group by FLOOR_ID,SEWING_LINE";
							$lineWiseProMinDateSqlResult=sql_select($lineWiseProMinDateSql);
							$line_wise_days_run=array();
							foreach($lineWiseProMinDateSqlResult as $row)
							{
								$line_wise_days_run[$row[FLOOR_ID]][$row[SEWING_LINE]]=datediff("d",$row[MIN_DATE],$pr_date);
							}


						}
						//echo $pr_date;die;
						$type_line=$production_data_arr[$f_id][$l_id][$job_key]['type_line'];
						$prod_reso_allo=$production_data_arr[$f_id][$l_id][$job_key]['prod_reso_allo'];
						$sewing_line='';
						if($production_data_arr[$f_id][$l_id][$job_key]['prod_reso_allo']!="")
						{
							if($production_data_arr[$f_id][$l_id][$job_key]['prod_reso_allo']==1)
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
						// echo $sewing_line."==".$production_data_arr[$f_id][$l_id][$job_key]['prod_reso_allo']."=kakku<br>";
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
							 $production_hour[$prod_hour]=$production_data_arr[$f_id][$l_id][$job_key][$prod_hour];
							 $floor_production[$prod_hour]+=$production_data_arr[$f_id][$l_id][$job_key][$prod_hour];
							 $total_production[$prod_hour]+=$production_data_arr[$f_id][$l_id][$job_key][$prod_hour];
							 if($production_data_arr[$f_id][$l_id][$job_key][$prod_hour]>0)
							 {
							 	$prod_hours++;
							 }
						}

		 				$floor_production['prod_hour24']+=$production_data_arr[$f_id][$l_id][$job_key]['prod_hour23'];
						$total_production['prod_hour24']+=$production_data_arr[$f_id][$l_id][$job_key]['prod_hour23'];
						$production_hour['prod_hour24']=$production_data_arr[$f_id][$l_id][$job_key]['prod_hour23'];
						$line_production_hour=0;
						if(str_replace("'","",$actual_production_date)>str_replace("'","",$actual_date))
						{
							if($type_line==2) //No Profuction Line
							{
								$line_start=$production_data_arr[$f_id][$l_id][$job_key]['prod_start_time'];
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
									$total_eff_hour=$total_eff_hour+1;
									$line_hour="prod_hour".substr($bg,0,2)."";
									$line_production_hour+=$production_data_arr[$f_id][$l_id][$job_key][$line_hour];
									$line_floor_production+=$production_data_arr[$f_id][$l_id][$job_key][$line_hour];
									$line_total_production+=$production_data_arr[$f_id][$l_id][$job_key][$line_hour];
									$actual_time_hour=$start_hour_arr[$lh+1];
								}
							}

		 					if($start_hour_arr[$actual_time]>$lunch_start_hour) $total_eff_hour=$total_eff_hour-1;

							if($type_line==2)
							{
								if($total_eff_hour>$production_data_arr[$f_id][$l_id][$job_key]['working_hour'])
								{
									 $total_eff_hour=$production_data_arr[$f_id][$l_id][$job_key]['working_hour'];
								}
							}
							else
							{
								if($line_wise_style_count_arr[$f_id][$l_id]>1)
								{
									if($total_eff_hour>$prod_resource_array2[$l_id][$job_key][$pr_date]['working_hour'])
									{
										$total_eff_hour=$prod_resource_array2[$l_id][$job_key][$pr_date]['working_hour'];
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
								$line_production_hour+=$production_data_arr[$f_id][$l_id][$job_key][$prod_hour];
								$line_floor_production+=$production_data_arr[$f_id][$l_id][$job_key][$prod_hour];
								$line_total_production+=$production_data_arr[$f_id][$l_id][$job_key][$prod_hour];
							}
							if($type_line==2)
							{
								$total_eff_hour=$production_data_arr[$f_id][$l_id][$job_key]['working_hour'];
							}
							else
							{
								if($line_wise_style_count_arr[$f_id][$l_id]>1)
								{
									$total_eff_hour=$prod_resource_array2[$l_id][$job_key][$pr_date]['working_hour'];
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
							$smv_adjustmet_type=$production_data_arr[$f_id][$l_id][$job_key]['smv_adjust_type'];
							$eff_target=($production_data_arr[$f_id][$l_id][$job_key]['terget_hour']*$total_eff_hour);

							if($total_eff_hour>=$production_data_arr[$f_id][$l_id][$job_key]['working_hour'])
							{
								if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$production_data_arr[$f_id][$l_id][$job_key]['smv_adjust'];
								if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($production_data_arr[$f_id][$l_id][$job_key]['smv_adjust'])*(-1);
							}
							$efficiency_min+=$total_adjustment+($production_data_arr[$f_id][$l_id][$job_key]['man_power'])*$cla_cur_time*60;
							$extra_minute_production_arr=$efficiency_min+$extra_minute_arr[$f_id][$l_id];

							$line_efficiency=(($produce_minit)*100)/$efficiency_min;


						}
						else
						{
							if($line_wise_style_count_arr[$f_id][$l_id]>1)
							{
								$smv_adjustmet_type=$prod_resource_array2[$l_id][$job_key][$pr_date]['smv_adjust_type'];
								$eff_target=($prod_resource_array2[$l_id][$job_key][$pr_date]['terget_hour']*$total_eff_hour);

								if($total_eff_hour>=$prod_resource_array2[$l_id][$job_key][$pr_date]['working_hour'])
								{
									if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array2[$l_id][$job_key][$pr_date]['smv_adjust'];
									if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array2[$l_id][$job_key][$pr_date]['smv_adjust'])*(-1);
								}

								// $efficiency_min+=$total_adjustment+($prod_resource_array2[$l_id][$job_key][$pr_date]['man_power'])*$cla_cur_time*60;
								$efficiency_min+=($prod_resource_array2[$l_id][$job_key][$pr_date]['man_power'])*$cla_cur_time*60;
								// echo "$efficiency_min=$l_id=$job_key=$pr_date=".$prod_resource_array2[$l_id][$job_key][$pr_date]['man_power']."*".$cla_cur_time."*60<br>";
								$extra_minute_resource_arr=$efficiency_min+$extra_minute_arr[$l_id][$pr_date];

								$line_efficiency=(($produce_minit)*100)/$efficiency_min;
							}
							else
							{
								$smv_adjustmet_type=$prod_resource_array[$l_id][$pr_date]['smv_adjust_type'];
								$eff_target=($prod_resource_array[$l_id][$pr_date]['terget_hour']*$total_eff_hour);

								if($total_eff_hour>=$prod_resource_array[$l_id][$pr_date]['working_hour'])
								{
									if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array[$l_id][$pr_date]['smv_adjust'];
									if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array[$l_id][$pr_date]['smv_adjust'])*(-1);
								}

								// $efficiency_min+=$total_adjustment+($prod_resource_array[$l_id][$pr_date]['man_power'])*$cla_cur_time*60;
								$efficiency_min+=($prod_resource_array[$l_id][$pr_date]['man_power'])*$cla_cur_time*60;
								// echo "$efficiency_min=$l_id=$job_key=$pr_date=".$prod_resource_array[$l_id][$pr_date]['man_power']."*".$cla_cur_time."*60<br>";
								$extra_minute_resource_arr=$efficiency_min+$extra_minute_arr[$l_id][$pr_date];

								$line_efficiency=(($produce_minit)*100)/$efficiency_min;
							}


						}

						// adjustment extra hour when multiple style running in a single line =========================
						$txtDate = str_replace("'", "", $txt_date);
						// echo "string==$l_id".$txtDate."<br>";
						// echo $extra_hr = $prod_resource_smv_adj_array[47]['02-Mar-2021'][1]['total_smv']."<br>";
						// echo $line_wise_style_count_arr2[$f_id][$l_id]."<br>";


						if($line_wise_style_count_arr[$f_id][$l_id]>1)
						{
							$mn_power = $prod_resource_smv_adj_array[$style_data][$l_id][$txtDate][1]['number_of_emp'];
							if($line_wise_style_count_arr2[$f_id][$l_id]>1)
							{
								// $late_hr = $prod_resource_smv_adj_array[$style_data][$l_id][$txtDate][5]['total_smv'];
								$extra_hr = $prod_resource_smv_adj_array[$style_data][$l_id][$txtDate][1]['total_smv'];
								$lunch_hr = $prod_resource_smv_adj_array[$style_data][$l_id][$txtDate][2]['total_smv'];
								$sick_hr = $prod_resource_smv_adj_array[$style_data][$l_id][$txtDate][3]['total_smv'];
								$leave_hr = $prod_resource_smv_adj_array[$style_data][$l_id][$txtDate][4]['total_smv'];
								$adjust_hr = ($lunch_hr+$sick_hr+$leave_hr) -  $extra_hr;
							
								if($pp==0)
								{
									$efficiency_min -= $adjust_hr;
									$pp++;
								}
								$line_wise_style_count_arr2[$f_id][$l_id]--;
							}
							else
							{

								$extra_hr = $prod_resource_smv_adj_array[$style_data][$l_id][$txtDate][1]['total_smv'];
								$lunch_hr = $prod_resource_smv_adj_array[$style_data][$l_id][$txtDate][2]['total_smv'];
								$sick_hr = $prod_resource_smv_adj_array[$style_data][$l_id][$txtDate][3]['total_smv'];
								$leave_hr = $prod_resource_smv_adj_array[$style_data][$l_id][$txtDate][4]['total_smv'];
								$adjust_hr = ($lunch_hr+$sick_hr+$leave_hr) -  $extra_hr;

								$efficiency_min -= $adjust_hr;
								// echo $adjust_hr."kakku <br>";

							}

						}
						else // for single line
						{
							$extra_hr = $prod_resource_smv_adj_array[$style_data][$l_id][$txtDate][1]['total_smv'];
							$lunch_hr = $prod_resource_smv_adj_array[$style_data][$l_id][$txtDate][2]['total_smv'];
							$sick_hr = $prod_resource_smv_adj_array[$style_data][$l_id][$txtDate][3]['total_smv'];
							$leave_hr = $prod_resource_smv_adj_array[$style_data][$l_id][$txtDate][4]['total_smv'];
							$adjust_hr = ($lunch_hr+$sick_hr+$leave_hr) - $extra_hr;

							$efficiency_min -= $adjust_hr;

							// echo $efficiency_min."=".$l_id."=".$job_key."=".$pr_date."=".$extra_hr."- (".$lunch_hr."+".$sick_hr."+".$leave_hr.")<br>";

						}


						$po_id=rtrim($production_data_arr[$f_id][$l_id][$job_key]['po_id'],',');
						$po_id=array_unique(explode(",",$po_id));

						$style=rtrim($line_wise_style_array[$f_id][$l_id]['style']);
						$style=implode("##",array_unique(explode("##",$style)));

						$job_arr = array_unique(explode(",", rtrim($production_data_arr[$f_id][$l_id][$job_key]['job_no'])));
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

						$po_id=$production_data_arr[$f_id][$l_id][$job_key]['po_id'];//$item_ids//$subcon_order_id
						$styles=explode("##",$style);
						$style_button='';//
						$style_name ='';
						$style_change = 0;
						foreach($styles as $sid)
						{
							if( $style_button=='')
							{
								$style_button="<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$l_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
								$style_name = $job_key;
							}
							else
							{
								$style_button.=", "."<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$l_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
								// $style_name .= ",".$sid;
								$style_change++; // first style will 0, 2nd style will 1
							}
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
						$target_value_fob = $eff_target*$avg_rate;

						$joNos_arr = array_unique(explode(",", $production_data_arr[$f_id][$l_id][$job_key]['job_no']));
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

							$tot_cm=return_field_value("CM_COST","wo_pre_cost_dtls","job_no='$jobNo' and is_deleted=0 and status_active=1");
							$cm_counter++;
						}
						$avg_cm = ($tot_cm/$dzn_qnty)/$cm_counter;
						$ttl_cm = $line_production_hour*$avg_cm;
						$target_cm = $eff_target*$avg_cm;

						// echo $ttl_cm."=".$l_id."=".$job_key."=".$txtDate."=".$tot_cm."*".$dzn_qnty."=".$cm_counter.")<br>";

						if($type_line==2) //No Production Line
						{
							$man_power=$production_data_arr[$f_id][$l_id][$job_key]['man_power'];
							$operator=$production_data_arr[$f_id][$l_id][$job_key]['operator'];
							$helper=$production_data_arr[$f_id][$l_id][$job_key]['helper'];
							$terget_hour=$production_data_arr[$f_id][$l_id][$job_key]['target_hour'];
							$capacity=$production_data_arr[$f_id][$l_id][$job_key]['capacity'];
							$working_hour=$production_data_arr[$f_id][$l_id][$job_key]['working_hour'];

							$floor_working_hour+=$production_data_arr[$f_id][$l_id][$job_key]['working_hour'];
							$eff_target_floor+=$eff_target;
							$floor_today_product+=$today_product;
							$floor_avale_minute+=$efficiency_min;
							$floor_produc_min+=$produce_minit;
							$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
							$floor_capacity+=$production_data_arr[$f_id][$l_id][$job_key]['capacity'];
							$floor_helper+=$production_data_arr[$l_id][$pr_da[$style]]['helper'];
							$floor_man_power+=$production_data_arr[$f_id][$l_id][$job_key]['man_power'];
							$floor_operator+=$production_data_arr[$f_id][$l_id][$job_key]['operator'];
							$total_operator+=$production_data_arr[$f_id][$l_id][$job_key]['operator'];
							$total_man_power+=$production_data_arr[$f_id][$l_id][$job_key]['man_power'];
							$total_helper+=$production_data_arr[$f_id][$l_id][$job_key]['helper'];
							$total_capacity+=$production_data_arr[$f_id][$l_id][$job_key]['capacity'];
							$floor_tgt_h+=$production_data_arr[$f_id][$l_id][$job_key]['target_hour'];
							$total_working_hour+=$production_data_arr[$f_id][$l_id][$job_key]['working_hour'];
							$gnd_total_tgt_h+=$production_data_arr[$f_id][$l_id][$job_key]['target_hour'];
							$total_terget+=$eff_target;
							$grand_total_product+=$today_product;
							$gnd_avable_min+=$efficiency_min;
							$gnd_product_min+=$produce_minit;

							$gnd_total_fob_val+=$fob_val;
							$gnd_final_total_fob_val+=$fob_val;
						}
						else
						{
							if($line_wise_style_count_arr[$f_id][$l_id]>1) // when multiple style run in single line
							{
								$operator=$prod_resource_array2[$l_id][$job_key][$pr_date]['operator'];
								$helper=$prod_resource_array2[$l_id][$job_key][$pr_date]['helper'];
								$terget_hour=$prod_resource_array2[$l_id][$job_key][$pr_date]['terget_hour'];
								$working_hour=$prod_resource_array2[$l_id][$job_key][$pr_date]['working_hour'];
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
							// echo $l_id."=".$job_key."=".$pr_date."=".$operator ."+". $helper."<br>";

							// $man_power=$prod_resource_array[$l_id][$pr_date]['man_power'];
							$capacity=$prod_resource_array[$l_id][$pr_date]['capacity'];
							// ======================================================
							$floor_capacity+=$prod_resource_array[$l_id][$pr_date]['capacity'];


							/*if($line_wise_style_count_arr[$f_id][$l_id]>1)
							{
								$floor_operator+=$prod_resource_array2[$l_id][$job_key][$pr_date]['operator'];
								$floor_helper+=$prod_resource_array2[$l_id][$job_key][$pr_date]['helper'];
								$floor_tgt_h+=$prod_resource_array2[$l_id][$job_key][$pr_date]['terget_hour'];
								$floor_working_hour+=$prod_resource_array2[$l_id][$job_key][$pr_date]['working_hour'];
								$floor_man_power+=$prod_resource_array2[$l_id][$job_key][$pr_date]['operator']+$prod_resource_array2[$l_id][$job_key][$pr_date]['helper'];
							}
							else
							{
								$floor_operator+=$prod_resource_array[$l_id][$pr_date]['operator'];
								$floor_helper+=$prod_resource_array[$l_id][$pr_date]['helper'];
								$floor_tgt_h+=$prod_resource_array[$l_id][$pr_date]['terget_hour'];
								$floor_working_hour+=$prod_resource_array[$l_id][$pr_date]['working_hour'];
								$floor_man_power+=$prod_resource_array[$l_id][$pr_date]['man_power'];
							}*/

							// if(!in_array($l_id, $line_chk_arr))
							// {
								if($line_wise_style_count_arr[$f_id][$l_id]>1)
								{
									$floor_operator+=$prod_resource_array2[$l_id][$job_key][$pr_date]['operator'];
									$floor_helper+=$prod_resource_array2[$l_id][$job_key][$pr_date]['helper'];
									$floor_tgt_h+=$prod_resource_array2[$l_id][$job_key][$pr_date]['terget_hour'];
									$floor_working_hour+=$prod_resource_array2[$l_id][$job_key][$pr_date]['working_hour'];
									$floor_man_power+=$prod_resource_array2[$l_id][$job_key][$pr_date]['operator']+$prod_resource_array2[$l_id][$job_key][$pr_date]['helper'];
								}
								else
								{
									$floor_operator+=$prod_resource_array[$l_id][$pr_date]['operator'];
									$floor_helper+=$prod_resource_array[$l_id][$pr_date]['helper'];
									$floor_tgt_h+=$prod_resource_array[$l_id][$pr_date]['terget_hour'];
									$floor_working_hour+=$prod_resource_array[$l_id][$pr_date]['working_hour'];
									$floor_man_power+=$prod_resource_array[$l_id][$pr_date]['man_power'];
								}
							// 	$line_chk_arr[$l_id] = $l_id;
							// }


							$floor_prod_hour += $prod_hours;
							$floor_tot_prod += $line_production_hour;
							$floor_line_acv += $line_acv;
							$floor_target_gap += $target_gap;
							$floor_target_min += $target_min;
							$floor_target_effi += $target_effi;
							$floor_achive_effi += $achive_effi;
							$floor_efficiency_gap += $efficiency_gap;
							// $floor_style_change += $style_change;
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
							// $total_style_change += $style_change;
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
							// if(!in_array($l_id, $line_chk_arr2))
							// {
								if($line_wise_style_count_arr[$f_id][$l_id]>1)
								{
									$total_operator+=$prod_resource_array2[$l_id][$job_key][$pr_date]['operator'];
									$total_working_hour+=$prod_resource_array2[$l_id][$job_key][$pr_date]['working_hour'];
									$gnd_total_tgt_h+=$prod_resource_array2[$l_id][$job_key][$pr_date]['terget_hour'];
									$total_helper+=$prod_resource_array2[$l_id][$job_key][$pr_date]['helper'];
									$total_man_power += $prod_resource_array2[$l_id][$job_key][$pr_date]['operator'] + $prod_resource_array2[$l_id][$job_key][$pr_date]['helper'];
								}
								else
								{
									$total_operator+=$prod_resource_array[$l_id][$pr_date]['operator'];
									$total_working_hour+=$prod_resource_array[$l_id][$pr_date]['working_hour'];
									$gnd_total_tgt_h+=$prod_resource_array[$l_id][$pr_date]['terget_hour'];
									$total_helper+=$prod_resource_array[$l_id][$pr_date]['helper'];
									$total_man_power+=$prod_resource_array[$l_id][$pr_date]['man_power'];
								}
							// 	$line_chk_arr2[$l_id] = $l_id;
							// }

							// $total_man_power+=$prod_resource_array[$l_id][$pr_date]['man_power'];
							$total_capacity+=$prod_resource_array[$l_id][$pr_date]['capacity'];
							$total_terget+=$eff_target;
							$grand_total_product+=$today_product;
							$gnd_avable_min+=$efficiency_min;
							$gnd_product_min+=$produce_minit;
							$gnd_total_fob_val+=$fob_val;
							$gnd_final_total_fob_val+=$fob_val;

						}

						if($line_efficiency<=$txt_parcentage) $efficiency_color="#FF0000"; else $efficiency_color="#FFFFFF";
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
						$html.='<td class="break_all" width="20">'.$i.'&nbsp;</td>
								<td class="break_all" align="center" width="50" ><p>'. $sewing_line.'&nbsp; </p></td>
								<td class="break_all" width="100"><p>'.$buyer_name.'&nbsp;</p></td>
								<td class="break_all" width="100"><p>'.$style_data.'&nbsp;</p></td>
								<td class="break_all" width="75"><p>'.$job__no.'&nbsp;</p></td>
								<td class="break_all" width="80" style="word-wrap:break-word; word-break: break-all;"><p>'.implode(",",array_unique(explode(",",$garment_itemname))).'</p></td>

								<td class="break_all" align="center" width="30"><p>'.implode("/",array_unique(explode("/",$item_smv))).'</p></td>
								<td class="break_all" align="right" width="50"><p>'.$terget_hour.'</p></td>
								<td class="break_all" align="right" width="50"><p>'.$working_hour.'</p></td>
								<td class="break_all" align="right" width="50"><p>'.$prod_hours.'</p></td>
								<td class="break_all" align="right" width="50"><p>'.$operator.'</p></td>
								<td class="break_all" align="right" width="50"><p>'.$helper.'</p></td>
								<td class="break_all" align="right" width="50"><p>'.$man_power.'</p></td>
								<td class="break_all" align="right" width="50"><p>'.$days_run.'</p></td>
								<td class="break_all" align="right" width="50"><p>'.$eff_target.'</p></td>';
								$p=1;
								for($k=$hour; $k<=$last_hour; $k++)
								{
									$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
									if($p <= 11)
									{
										$html.='<td class="break_all" align="right" width="36">'.$production_hour[$prod_hour].'</td>';
									}
									/*elseif ($production_hour[$prod_hour]>0 && $p>11)
									{
										$html.='<td class="break_all" align="right" width="70">'.$production_hour[$prod_hour].'</td>';
									}*/
									$p++;

								}

								$html.='<td class="break_all" align="right" width="50">'.$line_production_hour.'</td>
								<td class="break_all" align="right" width="50">'.number_format($line_acv,0).'%</td>
								<td class="break_all" align="right" width="50">'.number_format($target_gap,0).'</td>
								<td class="break_all" align="right" width="50">'.number_format($target_min,0).'</td>

								<td class="break_all" align="right" width="80">'.number_format($efficiency_min,0).'</td>
								<td class="break_all" width="50" align="right">
									<a href="##" onclick="openmypage('.$company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$l_id.",'tot_prod','".$smv_for_item."','".$acturl_hour_minute."','".$line_start."',".$txt_date.')">'.$produce_minit.'</a>
									</td>
								<td class="break_all" align="right" width="50">'.number_format($target_effi,0).'%</td>
								<td class="break_all" align="right" width="50">'.number_format($achive_effi,0).'%</td>
								<td class="break_all" align="right" width="50">'.number_format($efficiency_gap,0).'%</td>';
								if($lc==0)
								{
									$html.='<td rowspan="'.$rowspan[$f_id][$sl][$l_id].'" width="50" align="right">'.number_format($style_change,0).'</td>';
									$lc++;
									$total_style_change += $style_change;
									$floor_style_change += $style_change;
								}

								$html.='<td class="break_all" align="left" width="180" ><p>'.$production_data_arr[$f_id][$l_id][$job_key]['remarks'].'</p></td>
								<td class="break_all" width="50" title="Cm Pre='.($tot_cm.'/'.$dzn_qnty).'/'.$cm_counter.';" align="right">'.number_format($avg_cm,6).'</td>
								<td class="break_all" align="right" width="50">'.number_format($ttl_cm,2).'</td>
								<td class="break_all" align="right" width="50">'.number_format($target_cm,2).'</td>
								<td class="break_all" align="right" width="50">'.number_format($avg_rate,2).'</td>
								<td class="break_all" align="right" width="80">'.number_format($ttl_fob_val,2).'</td>
								<td class="break_all" align="right" width="80">'.number_format($target_value_fob,2).'</td>';

							$html.='</tr>';
							$i++;
							$check_arr[$f_id]=$f_id;
							$f_chk_arr[$f_id]=$f_id;
					//}
				}
			}

		}
	}
			$html.='<tr  bgcolor="#B6B6B6">
					<td class="break_all" width="20">&nbsp;</td>
					<td class="break_all" width="50">&nbsp;</td>
					<td class="break_all" width="100">&nbsp;</td>
					<td class="break_all" width="100">&nbsp;</td>
					<td class="break_all" width="75">&nbsp;</td>
					<td class="break_all" width="80" title="'.$f_id.'"><p><b>Floor Total('.$floorArr[$f_id].')</b><p></td>

					<td class="break_all" align="right" width="30">&nbsp;</td>
					<td class="break_all" align="right" width="50">'.$floor_tgt_h.'</td>
					<td class="break_all" align="right" width="50">'. $floor_working_hour.'</td>
					<td class="break_all" align="right" width="50">'. $floor_prod_hour.'</td>
					<td class="break_all" align="right" width="50">'. $floor_operator.'</td>
					<td class="break_all" align="right" width="50">'. $floor_helper.'</td>
					<td class="break_all" align="right" width="50">'. $floor_man_power.'</td>
					<td class="break_all" align="right" width="50">'. $floor_days_run.'</td>
					<td class="break_all" align="right" width="50">'. number_format($eff_target_floor,0).'</td>';
					$p=1;
					for($k=$hour; $k<=$last_hour; $k++)
					{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
						if($p <= 11)
						{
							$html.='<td class="break_all" align="right" width="36" style='.$bg_color_.' >'. $floor_production[$prod_hour].'</td>';
						}
						/*elseif ($floor_production[$prod_hour]>0 && $p>11)
						{
							$html.='<td class="break_all" align="right" width="70" style='.$bg_color_.' >'. $floor_production[$prod_hour].'</td>';
						}*/
						$p++;
					}
					$html.='<td class="break_all" align="right" width="50">'.number_format($floor_tot_prod,0).'</td>
					<td class="break_all" align="right" width="50">'.number_format((($floor_tot_prod/$eff_target_floor)*100),0).'%</td>
					<td class="break_all" align="right" width="50">'.number_format($floor_target_gap,0).'</td>
					<td class="break_all" align="right" width="50">'.number_format($floor_target_min,0).'</td>
					<td class="break_all" align="right" width="80">'.number_format($floor_avale_minute,2).'</td>
					<td class="break_all" align="right" width="50">'.number_format($floor_produc_min,0).'</td>
					<td class="break_all" align="right" width="50">'.number_format((($floor_target_min/$floor_avale_minute)*100),0).'%</td>
					<td class="break_all" align="right" width="50">'. number_format((($floor_produc_min/$floor_avale_minute)*100),0).'%</td>
					<td class="break_all" align="right" width="50">'. number_format((($floor_target_min/$floor_avale_minute)*100) - (($floor_produc_min/$floor_avale_minute)*100),0).'%</td>
					<td class="break_all" align="right" width="50">'. number_format($floor_style_change,0).'</td>
					<td class="break_all" align="right" width="180"></td>

					<td class="break_all" width="50" align="right">'.number_format($floor_avg_cm,2).'</td>
					<td class="break_all" width="50" align="right">'.number_format($floor_ttl_cm,2).'</td>
					<td class="break_all" width="50" align="right">'.number_format($floor_target_cm,2).'</td>
					<td class="break_all" width="50" align="right">'.number_format($floor_avg_rate,2).'</td>
					<td class="break_all" width="80" align="right">'.number_format($floor_ttl_fob_val,2).'</td>
					<td class="break_all" width="80" align="right">'.number_format($floor_target_value_fob,2).'</td>
					</tr>
					</tbody>';

					$smv_for_item="";
					$tbl_width = 1795+($last_hour - ($hour+1))*40;
					// echo $tbl_width;die();
					$colspan = 32+($last_hour - ($hour+1));
				?>

	<fieldset style="width:<? echo $tbl_width+20;?>px">
       <table width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
            <tr class="form_caption">
                <td colspan="<? echo $colspan;?>" align="center"><strong><? echo $report_title; ?> &nbsp;</strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="<? echo $colspan;?>" align="center"><strong><? echo $companyArr[$company_id]; ?></strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="<? echo $colspan;?>" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date)) ); ?></strong></td>
            </tr>
        </table>
        <br />
        <!-- <table  width="600" cellpadding="0"  cellspacing="0" align="center" style="padding-left:200px">
            <tr>
                <td bgcolor="#FFFF66" height="18" width="30" ></td>
                <td> &nbsp;Lunch Hour</td>
                <td bgcolor="red" height="18" width="30"></td>
                <td> &nbsp;Efficiency % less than Standard And Production less than Target</td>
            </tr>
        </table> -->

        <table id="table_header_1" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th class="break_all" width="20">SL</th>
                    <th class="break_all" width="50">Line No</th>
                    <th class="break_all" width="100">Buyer</th>
                    <th class="break_all" width="100">Style Ref.</th>
                    <th class="break_all" width="75">Job</th>
                    <th class="break_all" width="80">Garments Item</th>
                    <th class="break_all" width="30">SMV</th>
                    <th class="break_all" width="50">Hourly <br>Target <br>(Pcs)<br><br></th>
                    <th class="break_all" width="50">Plan<br>/W.Hour<br></th>
                    <th class="break_all" width="50">Prod.<br> Hour<br></th>
                    <th class="break_all" width="50">Operator</th>
                    <th class="break_all" width="50">Helper</th>
                    <th class="break_all" width="50"> Man <br>Power<br></th>
                    <th class="break_all" width="50">Days <br>Run<br></th>
                    <th class="break_all" width="50">Total <br>Target<br></th>
                   <?
                   // print_r($production_hour);
                   	$p = 1;
                	for($k=$hour; $k<=$last_hour; $k++)
					{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";

						if($p <= 11)
						{
							?>
		                      <th class="break_all" width="36" style="vertical-align:middle"><div class="block_div"><?  echo substr($start_hour_arr[$k],0,5)."<br>-<br>".substr($start_hour_arr[$k+1],0,5); ?></div></th>
							<?
						}
						/*elseif ($production_hour[$prod_hour]>0 && $p>11)
						{
							?>
		                      <th class="break_all" width="70" style="vertical-align:middle"><div class="block_div"><?  echo substr($start_hour_arr[$k],0,5)."-".substr($start_hour_arr[$k+1],0,5); ?></div></th>
							<?
						}*/
						$p++;
					}
                	?>
                    <th class="break_all" width="50">Total <br> Prod.<br></th>
                    <th class="break_all" width="50">Line Acv.</th>
                    <th class="break_all" width="50">Target <br>Gap<br></th>
                    <th class="break_all" width="50">Target <br>Min<br></th>
                    <th class="break_all" width="80">Available Min.</th>
                    <th class="break_all" width="50">Produce<br>/Earn Min<br></th>
                    <th class="break_all" width="50">Target <br>Eff.%<br></th>
                    <th class="break_all" width="50">Acv.<br> Eff.%<br></th>
                    <th class="break_all" width="50">Eff. Gap</th>
                    <th class="break_all" width="50">Style <br>Change<br></th>
                    <th class="break_all" width="180">Remarks</th>
                    <th class="break_all" width="50">CM/PC</th>
                    <th class="break_all" width="50">TTL CM</th>
                    <th class="break_all" width="50">Target <br>CM<br></th>
                    <th class="break_all" width="50">Unit <br>Price<br></th>
                    <th class="break_all" width="80">Ttl. Value(FOB)</th>
                    <th class="break_all" width="80">Target <br>Value(FOB)<br></th>
                </tr>
            </thead>
        </table>
        <div style="width:<?= $tbl_width+20;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
              <? echo $html;  ?>
                <tfoot>
                   <tr>
                        <th class="break_all" width="20">&nbsp;</th>
                        <th class="break_all" width="50">&nbsp;</th>
                        <th class="break_all" width="100">&nbsp;</th>
                        <th class="break_all" width="100">&nbsp;</th>
                        <th class="break_all" width="75">&nbsp;</th>
                        <th class="break_all" width="80">Grand Total</th>

                        <th class="break_all" align="right" width="30">&nbsp;</th>
                        <th class="break_all" align="right" width="50"><? echo number_format($gnd_total_tgt_h,0); ?>&nbsp;</th>
                        <th class="break_all" align="right" width="50"><? echo $total_working_hour; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="50"><? echo $total_prod_hour; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="50"><?  echo $total_operator; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="50"><?  echo $total_helper; ?></th>
                        <th class="break_all" align="right" width="50"><?  echo $total_man_power; ?></th>
                        <th class="break_all" align="right" width="50"><?  echo $total_days_run; ?></th>
                        <th class="break_all" align="right" width="50"><? echo number_format($total_terget,0); ?></th>
                        <?
                        $p = 1;
						for($k=$hour; $k<=$last_hour; $k++)
						{
							$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";

							if($p <= 11)
							{
								?>
			                      <th class="break_all" align="right" width="36"><? echo $total_production[$prod_hour]; ?></th>
								<?
							}
							/*elseif ($total_production[$prod_hour]>0 && $p>11)
							{
								?>
			                      <th class="break_all" align="right" width="70"><? echo $total_production[$prod_hour]; ?></th>
								<?
							}*/
							$p++;
						}
                        ?>
                        <th class="break_all" align="right" width="50"><? echo number_format($total_tot_prod,0); ?></th>
                        <th class="break_all" align="right" width="50"><? echo number_format((($total_tot_prod/$total_terget)*100),0); ?>%</th>
                        <th class="break_all" align="right" width="50"><? echo number_format($total_target_gap,0); ?></th>
                        <th class="break_all" align="right" width="50"><? echo number_format($total_target_min,0); ?>&nbsp;</th>
                        <th class="break_all" align="right" width="80" ><? echo number_format($gnd_avable_min,2);?>&nbsp;</th>
                        <th class="break_all" align="right" width="50"><? echo number_format($gnd_product_min,0); ?>&nbsp;</th>
                        <th class="break_all" align="right" width="50"><? echo number_format((($total_target_min/$gnd_avable_min)*100),0); ?>%</th>
                        <th class="break_all" align="right" width="50"><? echo number_format((($gnd_product_min/$gnd_avable_min)*100),0); ?>%&nbsp;</th>
                        <th class="break_all" align="right" width="50"><? echo number_format((($total_target_min/$gnd_avable_min)*100)-(($gnd_product_min/$gnd_avable_min)*100) ,0); ?>%&nbsp;</th>
                        <th class="break_all" align="right" width="50"><? echo number_format($total_style_change,0); ?>&nbsp;</th>
                        <th class="break_all" align="right" width="180">&nbsp;</th>

                        <th class="break_all" align="right" width="50"><? echo number_format($total_avg_cm,2);?>&nbsp;</th>
                        <th class="break_all" align="right" width="50"><? echo number_format($total_ttl_cm,2);?>&nbsp;</th>
                        <th class="break_all" align="right" width="50"><? echo number_format($total_target_cm,2);?>&nbsp;</th>

                        <th class="break_all" align="right" width="50"><? echo number_format($total_avg_rate,2);?>&nbsp;</th>
                        <th class="break_all" align="right" width="80"><? echo number_format($total_ttl_fob_val,2);?>&nbsp;</th>
                        <th class="break_all" align="right" width="80"><? echo number_format($total_target_value_fob,2);?>&nbsp;</th>

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
	?>
	<style type="text/css">
		/*.block_div {
				width:auto;
				height:auto;
				text-wrap:normal;
				vertical-align:bottom;
				display: block;
				position: !important;
				-webkit-transform: rotate(-90deg);
				-moz-transform: rotate(-90deg);
		}*/
		table tr th,table tr td
		{
			word-wrap: break-word;
			word-break: break-all;
		}

   </style>
	<?
	extract($_REQUEST);
	$process = array( &$_POST );

	//change_date_format($txt_date);die;

	if($db_type==0)	$txt_date=change_date_format($txt_date,'yyyy-mm-dd');
	if($db_type==2)	$txt_date=change_date_format($txt_date,'','',1);
	$txt_date="'".$txt_date."'";

	extract(check_magic_quote_gpc( $process ));
	$companyArr = return_library_array("select id,company_name from lib_company","id","company_name");
	$buyerArr = return_library_array("select id,short_name from lib_buyer","id","short_name");
	$locationArr = return_library_array("select id,location_name from lib_location","id","location_name");
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
	$lineArr = return_library_array("select id,line_name from lib_sewing_line where company_name=$cbo_company_id","id","line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	$company_id=str_replace("'","",$cbo_company_id);
    $today_date=date("Y-m-d");
	$txt_producting_day="".str_replace("'","",$txt_date)."";

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
	$hour=$start_time[0]*1;
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
	$hour_line=$first_hour_time[0]*1; $minutes_one=$start_time[1];
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
	/										prod resource data								/
	/===================================================================================== */
	// echo $prod_reso_allo[0]; die;
	$prod_res_cond = "";
	$prod_res_cond .= (str_replace("'", "", $cbo_location_id)==0) ? "" : " and a.location_id=$cbo_location_id";
	$prod_res_cond .= (str_replace("'", "", $cbo_floor_id)==0) ? "" : " and a.floor_id=$cbo_floor_id";
	$prod_res_cond .= (str_replace("'", "", $hidden_line_id)=="") ? "" : " and a.id in(".str_replace("'", "", $hidden_line_id).")";

	if($prod_reso_allo[0]==1)
	{
		$prod_resource_array=array();
		$prod_resource_array2=array();
		$prod_resource_smv_array = array();

		$dataArray_sql=sql_select("SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id=$company_id and b.pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $prod_res_cond");

		foreach($dataArray_sql as $val)
		{
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['man_power']=$val[csf('man_power')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['operator']=$val[csf('operator')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['helper']=$val[csf('helper')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['terget_hour']=$val[csf('target_per_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['working_hour']=$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['tpd']=$val[csf('target_per_hour')]*$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_start']=$val[csf('from_date')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_end']=$val[csf('to_date')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['capacity']=$val[csf('capacity')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust']=$val[csf('smv_adjust')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];
		}
		// echo "<pre>";print_r($prod_resource_array);die();

		// =======================================================
		$sql="SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, c.from_date,c.to_date,c.capacity,d.po_id,d.target_per_line,d.operator, d.helper,d.working_hour,d.actual_smv,d.gmts_item_id from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c,prod_resource_color_size d where a.id=c.mst_id and c.id=b.mast_dtl_id and d.mst_id=a.id and d.dtls_id=c.id and a.company_id=$company_id and b.pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $prod_res_cond";
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
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['man_power']=$val[csf('operator')]+$val[csf('helper')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['operator']=$val[csf('operator')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['helper']=$val[csf('helper')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['terget_hour']=$val[csf('target_per_line')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['working_hour']=$val[csf('working_hour')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['tpd']=$val[csf('target_per_line')]*$val[csf('working_hour')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['day_start']=$val[csf('from_date')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['day_end']=$val[csf('to_date')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['capacity']=$val[csf('capacity')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['smv_adjust']=$val[csf('smv_adjust')];
			$prod_resource_array2[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];
			$prod_resource_smv_array[$val[csf('id')]][$style_arr[$val[csf('po_id')]]][$val[csf('gmts_item_id')]][$val[csf('pr_date')]]['actual_smv']=$val[csf('actual_smv')];


		}

		// echo "<pre>"; print_r($prod_resource_smv_array);die();

		if(str_replace("'","",trim($txt_date))==""){$pr_date_con="";}else{$pr_date_con=" and b.pr_date=$txt_date";}

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
		// echo $sqlExtraHour;
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
		$sql_query="SELECT b.mst_id, b.pr_date,b.number_of_emp ,b.adjust_hour,b.total_smv,b.adjustment_source from prod_resource_mst a,prod_resource_smv_adj b  where a.id=b.mst_id  and a.company_id=$company_id and b.pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 and b.status_active=1  $prod_res_cond";
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

	$search_prod_date=change_date_format(str_replace("'","",$txt_date));
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
		$sql="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name  as buyer_name,b.style_ref_no,b.job_no, a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.file_no,c.unit_price,c.grouping as ref,d.color_type_id,a.remarks,e.floor_serial_no,sum(d.production_qnty) as good_qnty,";
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
		FROM  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a ,pro_garments_production_dtls d,lib_prod_floor e
		WHERE a.production_type=5 and a.po_break_down_id=c.id and c.job_id=b.id and a.id=d.mst_id and e.id=a.floor_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_name $location $floor $line $buyer_id_cond  $txt_date_from
		GROUP BY b.job_no, a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name,b.style_ref_no,a.item_number_id,c.po_number,c.unit_price,c.file_no,c.grouping ,d.color_type_id,a.remarks,e.floor_serial_no
		ORDER BY a.location,e.floor_serial_no";
	}
	// echo $sql;die;
	$sql_resqlt=sql_select($sql);
	$production_data_arr=array();
	$production_po_data_arr=array();
	$production_serial_arr=array();
	$reso_line_ids='';
	$all_po_id="";
	$active_days_arr=array();
	$duplicate_date_arr=array();
	$poIdArr=array();
	$prod_line_array = array();
	$line_style_chk_array = array();
	$line_wise_style_array = array();
	foreach($sql_resqlt as $val)
	{
		$prod_line_array[$val[csf('sewing_line')]] = $val[csf('sewing_line')];
		$poIdArr[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
		if($val[csf('prod_reso_allo')]==1)
		{
			$sewing_line_ids=$prod_reso_arr[$val[csf('sewing_line')]];
			$sl_ids_arr = explode(",", $sewing_line_ids);
			$sewing_line_id = $sl_ids_arr[0]; // always 1st line id will take

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
		else
		{
			$slNo=$lineSerialArr[$sewing_line_id];
		}
		$production_serial_arr[$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]=$val[csf('sewing_line')];

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
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]][$prod_hour]+=$val[csf($prod_hour)];

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

	 	$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['prod_hour23']+=$val[csf('prod_hour23')];
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['prod_reso_allo']=$val[csf('prod_reso_allo')];

	 	if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['buyer_name']!="")
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['buyer_name'].=",".$val[csf('buyer_name')];
		}
	 	else
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['buyer_name']=$val[csf('buyer_name')];
		}

		if($line_style_chk_array[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]] =="")
		{
			$line_wise_style_count_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]++;
			$line_wise_style_count_arr2[$val[csf('floor_id')]][$val[csf('sewing_line')]]++;
			$line_style_chk_array[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]] = $val[csf('style_ref_no')];
		}

		/*if($line_wise_style_count_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]!="")
		{
			$line_wise_style_count_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]].=",".$val[csf('buyer_name')];
		}
	 	else
		{
			$line_wise_style_count_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]=$val[csf('buyer_name')];
		}*/


		if($line_wise_style_array[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style']!="")
		{
			$line_wise_style_array[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style'].="##".$val[csf('style_ref_no')];
		}
	 	else
		{
			$line_wise_style_array[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style']=$val[csf('style_ref_no')];
		}


	 	if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['po_number']!="")
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['po_number'].=",".$val[csf('po_number')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['job_no'].=",".$val[csf('job_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['po_id'].=",".$val[csf('po_break_down_id')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['style'].="##".$val[csf('style_ref_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['file'].=",".$val[csf('file_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['ref'].=",".$val[csf('ref')];
		}
	 	else
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['po_number']=$val[csf('po_number')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['job_no']=$val[csf('job_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['po_id']=$val[csf('po_break_down_id')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['style']=$val[csf('style_ref_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['file']=$val[csf('file_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['ref']=$val[csf('ref')];
		}
		if ($val[csf('remarks')] !="")
		{
		 	if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['remarks']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['remarks'].=",".$val[csf('remarks')];
			}
		 	else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['remarks']=$val[csf('remarks')];
			}
		}

		if($po_rate_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['rate']!="")
		{
			$po_rate_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['rate'].=",".$val[csf('unit_price')];
		}
		else
		{
			$po_rate_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['rate'] = $val[csf('unit_price')];
		}

		if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['item_number_id']!="")
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('unit_price')];
		}
		else
		{
			 $production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('unit_price')];
		}
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['quantity']+=$val[csf('good_qnty')];
		$production_data_arr_qty[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]['quantity']+=$val[csf('good_qnty')];

		if($all_po_id=="") $all_po_id=$val[csf('po_break_down_id')]; else $all_po_id.=",".$val[csf('po_break_down_id')];
	}



	// $production_serial_arr[$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]=$val[csf('sewing_line')];
	// $production_serial_arr[5000][1][8000][0]=8000;




	// echo "<pre>"; print_r($production_data_arr);die();
	/*===================================================================================== /
	/										po item wise smv 								/
	/===================================================================================== */
	$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no  and b.id in($all_po_id) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3)";
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
    $po_active_sql="SELECT a.floor_id,a.sewing_line,a.production_date,a.po_break_down_id,a.item_number_id from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $company_name $location $floor $line $buyer_id_cond  $poIds_cond2 group by  a.floor_id,a.sewing_line,a.production_date ,a.po_break_down_id,a.item_number_id";
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
	$sql_item_rate="SELECT b.id, c.item_number_id, c.order_quantity, c.order_total from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c where b.job_no_mst=a.job_no and b.id=c.po_break_down_id and b.job_no_mst=c.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1  $file_cond $ref_cond  $poIds_cond";
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
   		$sql_sub_contuct.="sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.id and d.is_deleted=0 left join lib_sewing_line e on a.line_id=e.id and e.is_deleted=0 where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id=$company_id $subcon_location $floor $subcon_line   $txt_date_from group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date,a.prod_reso_allo,a.line_id,b.party_id,c.order_no,c.cust_style_ref,b.subcon_job ,e.sewing_line_serial order by a.location_id,d.floor_serial_no,e.sewing_line_serial,a.prod_reso_allo";
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

	   	$sql_sub_contuct.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]'	and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.id  and d.is_deleted=0 left join lib_sewing_line e on a.line_id=e.id and e.is_deleted=0 where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id=$company_id $subcon_location $floor $subcon_line   $txt_date_from group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date,  b.subcon_job,a.line_id,b.party_id,c.order_no,c.cust_style_ref,e.sewing_line_serial,a.prod_reso_allo order by a.location_id, a.floor_id,e.sewing_line_serial,a.prod_reso_allo";

	}
	//echo $sql_sub_contuct;die;
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

		$production_po_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$subcon_val[csf('good_qnty')];
		if($production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['buyer_name']!="")
		{
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['buyer_name'].=",".$subcon_val[csf('buyer_name')];
		}
		else
		{
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['buyer_name']=$subcon_val[csf('buyer_name')];
		}
		if($line_style_chk_array[$subcon_val[csf('floor_id')]][$subcon_val[csf('sewing_line')]][$subcon_val[csf('style_ref_no')]] =="")
		{
			$line_wise_style_count_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('sewing_line')]]++;
			$line_wise_style_count_arr2[$subcon_val[csf('floor_id')]][$subcon_val[csf('sewing_line')]]++;
			$line_style_chk_array[$subcon_val[csf('floor_id')]][$subcon_val[csf('sewing_line')]][$subcon_val[csf('style_ref_no')]] = $subcon_val[csf('style_ref_no')];
		}

		if($production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['po_number']!="")
		{
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['po_number'].=",".$subcon_val[csf('po_number')];
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['job_no'].=",".$subcon_val[csf('job_no')];
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['style'].="##".$subcon_val[csf('cust_style_ref')];
		}
		else
		{
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['po_number']=$subcon_val[csf('po_number')];
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['job_no']=$subcon_val[csf('job_no')];
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['style']=$subcon_val[csf('cust_style_ref')];
		}

		if($production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['order_id']!="")
		{
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['order_id'].=",".$subcon_val[csf('order_id')];
		}
		else
		{
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['order_id'].=$subcon_val[csf('order_id')];
		}
		$subcon_order_smv[$subcon_val[csf('order_id')]]=$subcon_val[csf('smv')];
		$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['quantity']+=$subcon_val[csf('good_qnty')];

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
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]][$prod_hour]+=$subcon_val[csf($prod_hour)];
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
			{
				 if( $h>=$line_start_hour && $h<=$actual_time)
				 {
				 $production_po_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf($prod_hour)];	                 }
			}
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
			{
				$production_po_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf($prod_hour)];	            }
		 }
		if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
		{
			if( $h>=$line_start_hour && $h<=$actual_time)
			{
				$production_po_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf('prod_hour23')];
			}
		}
		else
		{
			$production_po_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf('prod_hour23')];
		}
		$production_data_arr[$val[csf('floor_id')]][$val[csf('line_id')]][$subcon_val[csf('cust_style_ref')]]['prod_hour23']+=$val[csf('prod_hour23')];
	}

	/*===================================================================================== /
	/							prod resource data no prod line								/
	/===================================================================================== */
	// echo $prod_reso_allo[0]; die;

	$prod_line_ids = implode(",", $prod_line_array);

	if($prod_reso_allo[0]==1)
	{
		$dataArray_sql=sql_select("SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity,d.floor_serial_no from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c,lib_prod_floor d where a.id=c.mst_id and c.id=b.mast_dtl_id and d.id=a.floor_id and a.company_id=$company_id and b.pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $prod_res_cond and a.id not in($prod_line_ids) order by d.floor_serial_no ");

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
			$production_serial_arr[$val[csf('floor_id')]][$slNo][$val[csf('id')]][]=$val[csf('id')];
			$production_serial_arr2[$val[csf('floor_id')]][$slNo][$val[csf('id')]]=$val[csf('id')];
		}
	}

	/*===================================================================================== /
	/							For Summary Report New Add No Prodcut						/
	/===================================================================================== */
	if($cbo_no_prod_type==1)
	{
		//No Production line Start ....
		$prod_reso_arr=return_library_array( "SELECT id, line_number from prod_resource_mst",'id','line_number');
		$sql_active_line=sql_select("SELECT sewing_line,sum(production_quantity) as total_production from pro_garments_production_mst  where production_date=".$txt_date." and production_type=5 and status_active=1 and is_deleted=0 and prod_reso_allo=1 group by  sewing_line");
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
		if(str_replace("'","",$cbo_location_id)==0)
		{
		$location_cond="";
		}
		else
		{
		$location=" and a.location_id=".str_replace("'","",$cbo_location_id)."";
		}

		if(str_replace("'","",$cbo_floor_id)==0) $floor=""; else $floor="and a.floor_id=".str_replace("'","",$cbo_floor_id)."";
		$lin_ids=str_replace("'","",$hidden_line_id);
		$res_line_cond=rtrim($reso_line_ids,",");

		 $dataArray_sum=sql_select("SELECT a.id, a.floor_id,1 as prod_reso_allo,2 as type_line, a.line_number as line_no, b.man_power, b.operator, b.helper, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type,d.prod_start_time from  prod_resource_dtls b,prod_resource_dtls_time d,prod_resource_mst a  where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$company_id and b.pr_date=".$txt_date." and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 and a.id not in($res_line_cond) and a.line_number like '%$lin_ids%'  $location  $floor  group by a.id, a.floor_id, a.line_number, d.prod_start_time,b.man_power, b.operator, b.helper, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type order by a.floor_id");
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
		 $dataArray_sql_cap=sql_select("select  a.floor_id, a.line_number as line_no,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id  and a.company_id=$company_id and b.pr_date=".$txt_date."  and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  group by a.id,a.line_number, a.floor_id, a.line_number, b.man_power, b.operator,c.capacity, b.helper, b.working_hour,b.target_per_hour order by a.floor_id");

		 //$prod_resource_array_summary=array();
		 foreach( $dataArray_sql_cap as $row)
		 {
			 $production_data_arr[$row[csf('floor_id')]][$row[csf('line_no')]]['capacity']=$row[csf('capacity')];
		 }

	} //End

	//echo "<pre>";
	// echo "<pre>"; print_r($production_serial_arr);die;

	$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per");
	$condition= new condition();
	if($cbo_company_name>0){
		$condition->company_name("=$company_id");
	}
	if(count($poIdArr)>0)
	{
		$condition->po_id_in(implode(',',$poIdArr));
	}
	$condition->init();
	$other= new other($condition);
	$other_cost = $other->getAmountArray_by_job();
	// $other_cost[$jobNumber]['cm_cost'];
	// echo "<pre>"; print_r($production_serial_arr);die();
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
    $floor_man_power=0;
	$floor_operator=$floor_produc_min=0;
	$floor_smv=$floor_row=$floor_helper=$floor_tgt_h=$floor_days_run=$floor_working_hour=$line_floor_production=$floor_today_product=$floor_avale_minute=$floor_line_days_run=0;
	$floor_prod_hour = 0;
	$floor_tot_prod = 0;
	$floor_line_acv = 0;
	$floor_target_gap = 0;
	$floor_target_min = 0;
	$floor_target_effi = 0;
	$floor_achive_effi = 0;
	$floor_efficiency_gap = 0;
	$floor_style_change = 0;
	$floor_avg_cm = 0;
	$floor_ttl_cm = 0;
	$floor_target_cm = 0;
	$floor_avg_rate = 0;
	$floor_ttl_fob_val = 0;
	$floor_target_value_fob = 0;

	$total_operator=$total_helper=$gnd_hit_rate=0;
    $total_smv=$total_terget=$grand_total_product=$gnd_line_effi=0;
    $total_man_power=$gnd_avable_min=$gnd_product_min=0;
	$item_smv=$item_smv_total=$line_efficiency=$days_run=$days_active=$total_working_hour=$gnd_total_tgt_h=$total_capacity=0;

	$total_prod_hour = 0;
	$total_tot_prod = 0;
	$total_line_acv = 0;
	$total_target_gap = 0;
	$total_target_min = 0;
	$total_target_effi = 0;
	$total_achive_effi = 0;
	$total_efficiency_gap = 0;
	$total_style_change = 0;
	$total_avg_cm = 0;
	$total_ttl_cm = 0;
	$total_target_cm = 0;
	$total_avg_rate = 0;
	$total_ttl_fob_val = 0;
	$total_target_value_fob = 0;

	$j=1;
	$i=1;
	ob_start();
	$line_number_check_arr=array();
	$smv_for_item="";
	$total_production=array();
	$floor_production=array();
    $line_floor_production=0;
    $line_total_production=0; $gnd_total_fob_val=0; $gnd_final_total_fob_val=0;
    $f_chk_arr = array();
    $line_chk_arr = array();
    $line_chk_arr2 = array();
    $html ='<tbody>';
	foreach($production_serial_arr as $f_id=>$fname)
	{
		ksort($fname);
		foreach($fname as $sl=>$s_data)
		{
			foreach($s_data as $l_id=>$ldata)
			{
				$l=0;
				$pp = 0;
				$lc=0;
				foreach ($ldata as $style_key => $style_data)
				{
				  	$po_value=$production_data_arr[$f_id][$l_id][$style_key]['po_number'];
				 	//  if($po_value)
					// {
						if($i!=1)
						{
							if(!in_array($f_id, $check_arr))
							{
								if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								 $html.='<tr  bgcolor="#B6B6B6">
										<td class="break_all" width="20">&nbsp;</td>
										<td class="break_all" width="50">&nbsp;</td>
										<td class="break_all" width="100">&nbsp;</td>
										<td class="break_all" width="100">&nbsp;</td>
										<td class="break_all" width="75">&nbsp;</td>
										<td class="break_all" width="80" title="'.$f_id.'"><p><b>Floor Total('.$floorArr[current($f_chk_arr)].')</b><p></td>

										<td class="break_all" align="right" width="30">&nbsp;</td>
										<td class="break_all" align="right" width="50">'.$floor_tgt_h.'</td>
										<td class="break_all" align="right" width="50">'. $floor_working_hour.'</td>
										<td class="break_all" align="right" width="50">'. $floor_prod_hour.'</td>
										<td class="break_all" align="right" width="50">'. $floor_operator.'</td>
										<td class="break_all" align="right" width="50">'. $floor_helper.'</td>
										<td class="break_all" align="right" width="50">'. $floor_man_power.'</td>
										<td class="break_all" align="right" width="50">'. $floor_days_run.'</td>
										<td class="break_all" align="right" width="50">'. number_format($eff_target_floor,0).'</td>';
										$p=1;
										for($k=$hour; $k<=$last_hour; $k++)
										{
											$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
											// if($p <= 11)
											// {
												$html.='<td class="break_all" align="right" width="36" style='.$bg_color_.' >'. $floor_production[$prod_hour].'</td>';
											// }
											/*elseif ($floor_production[$prod_hour]>0 && $p>11)
											{
												$html.='<td class="break_all" align="right" width="70" style='.$bg_color_.' >'. $floor_production[$prod_hour].'</td>';
											}*/
											$p++;
										}
										$html.='<td class="break_all" align="right" width="50">'.number_format($floor_tot_prod,0).'</td>
										<td class="break_all" align="right" width="50">'.number_format((($floor_tot_prod/$eff_target_floor)*100),0).'%</td>
										<td class="break_all" align="right" width="50">'.number_format($floor_target_gap,0).'</td>
										<td class="break_all" align="right" width="50">'.number_format($floor_target_min,0).'</td>
										<td class="break_all" align="right" width="80">'.number_format($floor_avale_minute,2).'</td>
										<td class="break_all" align="right" width="50">'.number_format($floor_produc_min,0).'</td>
										<td class="break_all" align="right" width="50">'.number_format((($floor_target_min/$floor_avale_minute)*100),0).'%</td>
										<td class="break_all" align="right" width="50">'. number_format((($floor_produc_min/$floor_avale_minute)*100),0).'%</td>
										<td class="break_all" align="right" width="50">'. number_format((($floor_target_min/$floor_avale_minute)*100) - (($floor_produc_min/$floor_avale_minute)*100),0).'%</td>
										<td class="break_all" align="right" width="50">'. number_format($floor_style_change,0).'</td>
										<td class="break_all" align="right" width="180"></td>

										<td class="break_all" width="50" align="right">'.number_format($floor_avg_cm,2).'</td>
										<td class="break_all" width="50" align="right">'.number_format($floor_ttl_cm,2).'</td>
										<td class="break_all" width="50" align="right">'.number_format($floor_target_cm,2).'</td>
										<td class="break_all" width="50" align="right">'.number_format($floor_avg_rate,2).'</td>
										<td class="break_all" width="80" align="right">'.number_format($floor_ttl_fob_val,2).'</td>
										<td class="break_all" width="80" align="right">'.number_format($floor_target_value_fob,2).'</td>';

									$gnd_total_fob_val=0;

									$html.='</tr>';
								  	$floor_name="";
								  	$floor_smv=0;
								  	$floor_row=0;
								  	$floor_operator=0;
								  	$floor_helper=0;
								  	$floor_tgt_h=0;
								  	$floor_man_power=0;
								  	$floor_days_run=0;
								  	$floor_line_days_run=0;
								  	$eff_target_floor=0;
								  	unset($floor_production);
								  	$floor_working_hour=0;
								  	$line_floor_production=0;
								  	$floor_today_product=0;
								  	$floor_avale_minute=0;
								  	$floor_produc_min=0;
								  	$floor_efficency=0;
								  	$floor_man_power=0;
								  	$floor_capacity=0;
								  	$floor_prod_hour = 0;
									$floor_tot_prod = 0;
									$floor_line_acv = 0;
									$floor_target_gap = 0;
									$floor_target_min = 0;
									$floor_target_effi = 0;
									$floor_achive_effi = 0;
									$floor_efficiency_gap = 0;
									$floor_style_change = 0;
									$floor_avg_cm = 0;
									$floor_ttl_cm = 0;
									$floor_target_cm = 0;
									$floor_avg_rate = 0;
									$floor_ttl_fob_val = 0;
									$floor_target_value_fob = 0;
								  	$j++;
								  	unset($f_chk_arr);
							}
						}
						$floor_row++;
						//$item_ids=$production_data_arr[$f_id][$l_id]['item_number_id'];
						$germents_item=array_unique(explode('****',$production_data_arr[$f_id][$l_id][$style_key]['item_number_id']));
						// print_r($germents_item);die();

						$buyer_neme_all=array_unique(explode(',',$production_data_arr[$f_id][$l_id][$style_key]['buyer_name']));
						$buyer_name="";
						foreach($buyer_neme_all as $buy)
						{
							if($buyer_name!='') $buyer_name.=',';
							$buyer_name.=$buyerArr[$buy];
						}
						$garment_itemname='';
						$active_days='';
						$item_smv="";$item_ids='';
						$smv_for_item="";
						$produce_minit="";
						$order_no_total="";
						$efficiency_min=0;
						$tot_po_qty=0;
						$fob_val=0;

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
							// $item_smv.=$prod_resource_smv_array[$l_id][$style_key][$po_garment_item[1]][$pr_date]['actual_smv'];
							if($order_no_total!="") $order_no_total.=",";
							$order_no_total.=$po_garment_item[0];
							if($smv_for_item!="") $smv_for_item.="****".$po_garment_item[0]."**".$item_smv;
							else
							$smv_for_item=$po_garment_item[0]."**".$item_smv;
							$produce_minit+=$production_po_data_arr[$f_id][$l_id][$po_garment_item[0]]*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
							// echo $production_po_data_arr[$f_id][$l_id][$po_garment_item[0]]."*".$prod_resource_smv_array[$l_id][$style_key][$po_garment_item[1]][$pr_date]['actual_smv']."<br>";
							$fob_rate=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt']/$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];
							$prod_qty=$production_data_arr_qty[$f_id][$l_id][$po_garment_item[0]][$po_garment_item[1]]['quantity'];
							//echo $prod_qty.'<br>';
							if(is_nan($fob_rate)){ $fob_rate=0; }
							$fob_val+=$prod_qty*$fob_rate;

						}

						$po_id_arr = array_unique(explode(",", $production_data_arr[$f_id][$l_id][$style_key]['po_id']));
						$po_rate ="";
						foreach ($po_id_arr as $po_val)
						{
							if($po_rate!="") $po_rate.=",";
							$po_rate.=$po_rate_data_arr[$f_id][$l_id][$po_val]['rate'];
						}
						// echo $po_rate."<br>";


						$subcon_po_id=array_unique(explode(',',$production_data_arr[$f_id][$l_id][$style_key]['order_id']));
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
							$produce_minit+=$production_po_data_arr[$f_id][$l_id][$sub_val]*$subcon_order_smv[$sub_val];
							if($subcon_order_id!="") $subcon_order_id.=",";
							$subcon_order_id.=$sub_val;
						}

						if($order_no_total!="")
						{
							$day_run_sql=sql_select("select min(production_date) as min_date from pro_garments_production_mst
							where po_break_down_id in(".$order_no_total.")  and production_type=4");
							foreach($day_run_sql as $row_run)
							{
								$sewing_day=$row_run[csf('min_date')];
							}
							/*if($sewing_day!="")
							{
								// $days_run=datediff("d",$sewing_day,$pr_date);
								$date1=date_create($sewing_day);
								$date2=date_create($pr_date);
								$diff=date_diff($date1,$date2);
								$days_run = $diff->format("%R%a days");
							}
							else  $days_run=0;*/


							$lineWiseProMinDateSql="SELECT min(production_date) as MIN_DATE,FLOOR_ID,SEWING_LINE from pro_garments_production_mst where production_type=4 and po_break_down_id in(".$order_no_total.") group by FLOOR_ID,SEWING_LINE";
							$lineWiseProMinDateSqlResult=sql_select($lineWiseProMinDateSql);
							$line_wise_days_run=array();
							foreach($lineWiseProMinDateSqlResult as $row)
							{
								$line_wise_days_run[$row[FLOOR_ID]][$row[SEWING_LINE]]=datediff("d",$row[MIN_DATE],$pr_date);
							}


						}
						//echo $pr_date;die;
						$type_line=$production_data_arr[$f_id][$l_id][$style_key]['type_line'];
						$prod_reso_allo=$production_data_arr[$f_id][$l_id][$style_key]['prod_reso_allo'];
						$sewing_line='';
						if($production_data_arr[$f_id][$l_id][$style_key]['prod_reso_allo']!="")
						{
							if($production_data_arr[$f_id][$l_id][$style_key]['prod_reso_allo']==1)
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
							 $production_hour[$prod_hour]=$production_data_arr[$f_id][$l_id][$style_key][$prod_hour];
							 $floor_production[$prod_hour]+=$production_data_arr[$f_id][$l_id][$style_key][$prod_hour];
							 $total_production[$prod_hour]+=$production_data_arr[$f_id][$l_id][$style_key][$prod_hour];
							 if($production_data_arr[$f_id][$l_id][$style_key][$prod_hour]>0)
							 {
							 	$prod_hours++;
							 }
						}

		 				$floor_production['prod_hour24']+=$production_data_arr[$f_id][$l_id][$style_key]['prod_hour23'];
						$total_production['prod_hour24']+=$production_data_arr[$f_id][$l_id][$style_key]['prod_hour23'];
						$production_hour['prod_hour24']=$production_data_arr[$f_id][$l_id][$style_key]['prod_hour23'];
						$line_production_hour=0;
						if(str_replace("'","",$actual_production_date)>str_replace("'","",$actual_date))
						{
							if($type_line==2) //No Profuction Line
							{
								$line_start=$production_data_arr[$f_id][$l_id][$style_key]['prod_start_time'];
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
								$line_production_hour+=$production_data_arr[$f_id][$l_id][$style_key][$line_hour];
								$line_floor_production+=$production_data_arr[$f_id][$l_id][$style_key][$line_hour];
								$line_total_production+=$production_data_arr[$f_id][$l_id][$style_key][$line_hour];
								$actual_time_hour=$start_hour_arr[$lh+1];
								}
							}
		 					if($start_hour_arr[$actual_time]>$lunch_start_hour) $total_eff_hour=$total_eff_hour-1;

							if($type_line==2)
							{
								if($total_eff_hour>$production_data_arr[$f_id][$l_id][$style_key]['working_hour'])
								{
									 $total_eff_hour=$production_data_arr[$f_id][$l_id][$style_key]['working_hour'];
								}
							}
							else
							{
								if($line_wise_style_count_arr[$f_id][$l_id]>1)
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
								$line_production_hour+=$production_data_arr[$f_id][$l_id][$style_key][$prod_hour];
								$line_floor_production+=$production_data_arr[$f_id][$l_id][$style_key][$prod_hour];
								$line_total_production+=$production_data_arr[$f_id][$l_id][$style_key][$prod_hour];
							}
							if($type_line==2)
							{
								$total_eff_hour=$production_data_arr[$f_id][$l_id][$style_key]['working_hour'];
							}
							else
							{
								if($line_wise_style_count_arr[$f_id][$l_id]>1)
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
							$smv_adjustmet_type=$production_data_arr[$f_id][$l_id][$style_key]['smv_adjust_type'];
							$eff_target=($production_data_arr[$f_id][$l_id][$style_key]['terget_hour']*$total_eff_hour);

							if($total_eff_hour>=$production_data_arr[$f_id][$l_id][$style_key]['working_hour'])
							{
								if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$production_data_arr[$f_id][$l_id][$style_key]['smv_adjust'];
								if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($production_data_arr[$f_id][$l_id][$style_key]['smv_adjust'])*(-1);
							}
							$efficiency_min+=$total_adjustment+($production_data_arr[$f_id][$l_id][$style_key]['man_power'])*$cla_cur_time*60;
							$extra_minute_production_arr=$efficiency_min+$extra_minute_arr[$f_id][$l_id];

							$line_efficiency=(($produce_minit)*100)/$efficiency_min;


						}
						else
						{
							if($line_wise_style_count_arr[$f_id][$l_id]>1)
							{
								$smv_adjustmet_type=$prod_resource_array2[$l_id][$style_key][$pr_date]['smv_adjust_type'];
								$eff_target=($prod_resource_array2[$l_id][$style_key][$pr_date]['terget_hour']*$total_eff_hour);

								if($total_eff_hour>=$prod_resource_array2[$l_id][$style_key][$pr_date]['working_hour'])
								{
									if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array2[$l_id][$style_key][$pr_date]['smv_adjust'];
									if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array2[$l_id][$style_key][$pr_date]['smv_adjust'])*(-1);
								}

								// $efficiency_min+=$total_adjustment+($prod_resource_array2[$l_id][$style_key][$pr_date]['man_power'])*$cla_cur_time*60;
								$efficiency_min+=($prod_resource_array2[$l_id][$style_key][$pr_date]['man_power'])*$cla_cur_time*60;
								// echo "string".$total_adjustment."+(".$prod_resource_array[$l_id][$pr_date]['man_power'].")*".$cla_cur_time."*60<br>";
								$extra_minute_resource_arr=$efficiency_min+$extra_minute_arr[$l_id][$pr_date];

								$line_efficiency=(($produce_minit)*100)/$efficiency_min;
							}
							else
							{
								$smv_adjustmet_type=$prod_resource_array[$l_id][$pr_date]['smv_adjust_type'];
								$eff_target=($prod_resource_array[$l_id][$pr_date]['terget_hour']*$total_eff_hour);

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
						$txtDate = str_replace("'", "", $txt_date);
						// echo "string==$l_id".$txtDate."<br>";
						// echo $extra_hr = $prod_resource_smv_adj_array[47]['02-Mar-2021'][1]['total_smv']."<br>";
						// echo $line_wise_style_count_arr2[$f_id][$l_id]."<br>";
						if($line_wise_style_count_arr[$f_id][$l_id]>1)
						{
							$mn_power = $prod_resource_smv_adj_array[$l_id][$txtDate][1]['number_of_emp'];
							if($line_wise_style_count_arr2[$f_id][$l_id]>1)
							{
								$late_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][5]['total_smv'];


								if($pp==0)
								{
									$efficiency_min -= $late_hr;
									$pp++;
								}
								$line_wise_style_count_arr2[$f_id][$l_id]--;
							}
							else
							{

								$extra_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][1]['total_smv'];
								$lunch_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][2]['total_smv'];
								$sick_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][3]['total_smv'];
								$leave_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][4]['total_smv'];
								$adjust_hr = $extra_hr - ($lunch_hr+$sick_hr+$leave_hr);

								$efficiency_min += $adjust_hr;
								// echo $adjust_hr."kakku <br>";

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
						}

						$po_id=rtrim($production_data_arr[$f_id][$l_id][$style_key]['po_id'],',');
						$po_id=array_unique(explode(",",$po_id));

						$style=rtrim($line_wise_style_array[$f_id][$l_id]['style']);
						$style=implode("##",array_unique(explode("##",$style)));

						$job_arr = array_unique(explode(",", rtrim($production_data_arr[$f_id][$l_id][$style_key]['job_no'])));
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

						$po_id=$production_data_arr[$f_id][$l_id][$style_key]['po_id'];//$item_ids//$subcon_order_id
						$styles=explode("##",$style);
						$style_button='';//
						$style_name ='';
						$style_change = 0;
						foreach($styles as $sid)
						{
							if( $style_button=='')
							{
								$style_button="<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$l_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
								$style_name = $style_key;
							}
							else
							{
								$style_button.=", "."<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$l_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
								// $style_name .= ",".$style_key;
								$style_change++; // first style will 0, 2nd style will 1
							}
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
						$target_value_fob = $eff_target*$avg_rate;

						$joNos_arr = array_unique(explode(",", $production_data_arr[$f_id][$l_id][$style_key]['job_no']));
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

							$tot_cm=return_field_value("CM_COST","wo_pre_cost_dtls","job_no='$jobNo' and is_deleted=0 and status_active=1");
							$cm_counter++;
						}
						$avg_cm = ($tot_cm/$dzn_qnty)/$cm_counter;
						$ttl_cm = $line_production_hour*$avg_cm;
						$target_cm = $eff_target*$avg_cm;



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
							if($line_wise_style_count_arr[$f_id][$l_id]>1) // when multiple style run in single line
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

							if(!in_array($l_id, $line_chk_arr))
							{
								if($line_wise_style_count_arr[$f_id][$l_id]>1)
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
								$line_chk_arr[$l_id] = $l_id;
							}


							$floor_prod_hour += $prod_hours;
							$floor_tot_prod += $line_production_hour;
							$floor_line_acv += $line_acv;
							$floor_target_gap += $target_gap;
							$floor_target_min += $target_min;
							$floor_target_effi += $target_effi;
							$floor_achive_effi += $achive_effi;
							$floor_efficiency_gap += $efficiency_gap;
							// $floor_style_change += $style_change;
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
							// $total_style_change += $style_change;
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
							if(!in_array($l_id, $line_chk_arr2))
							{
								if($line_wise_style_count_arr[$f_id][$l_id]>1)
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
								$line_chk_arr2[$l_id] = $l_id;
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

						if($line_efficiency<=$txt_parcentage) $efficiency_color="#FF0000"; else $efficiency_color="#FFFFFF";
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
						$html.='<td class="break_all" width="20">'.$i.'&nbsp;</td>
								<td class="break_all" align="center" width="50" title="'.$sl.'"><p>'. $sewing_line.'&nbsp; </p></td>
								<td class="break_all" width="100"><p>'.$buyer_name.'&nbsp;</p></td>
								<td class="break_all" width="100"><p>'.$style_name.'&nbsp;</p></td>
								<td class="break_all" width="75"><p>'.$job__no.'&nbsp;</p></td>
								<td class="break_all" width="80" style="word-wrap:break-word; word-break: break-all;"><p>'.implode(",",array_unique(explode(",",$garment_itemname))).'</p></td>

								<td class="break_all" align="center" width="30"><p>'.implode("/",array_unique(explode("/",$item_smv))).'</p></td>
								<td class="break_all" align="right" width="50"><p>'.$terget_hour.'</p></td>
								<td class="break_all" align="right" width="50"><p>'.$working_hour.'</p></td>
								<td class="break_all" align="right" width="50"><p>'.$prod_hours.'</p></td>
								<td class="break_all" align="right" width="50"><p>'.$operator.'</p></td>
								<td class="break_all" align="right" width="50"><p>'.$helper.'</p></td>
								<td class="break_all" align="right" width="50"><p>'.$man_power.'</p></td>
								<td class="break_all" align="right" width="50"><p>'.$days_run.'</p></td>
								<td class="break_all" align="right" width="50"><p>'.$eff_target.'</p></td>';
								$p=1;
								for($k=$hour; $k<=$last_hour; $k++)
								{
									$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
									// if($p <= 11)
									// {
										$html.='<td class="break_all" align="right" width="36">'.$production_hour[$prod_hour].'</td>';
									// }
									/*elseif ($production_hour[$prod_hour]>0 && $p>11)
									{
										$html.='<td class="break_all" align="right" width="70">'.$production_hour[$prod_hour].'</td>';
									}*/
									$p++;

								}

								$html.='<td class="break_all" align="right" width="50">'.$line_production_hour.'</td>
								<td class="break_all" align="right" width="50">'.number_format($line_acv,0).'%</td>
								<td class="break_all" align="right" width="50">'.number_format($target_gap,0).'</td>
								<td class="break_all" align="right" width="50">'.number_format($target_min,0).'</td>

								<td class="break_all" align="right" width="80">'.number_format($efficiency_min,0).'</td>
								<td class="break_all" width="50" align="right">
									<a href="##" onclick="openmypage('.$company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$l_id.",'tot_prod','".$smv_for_item."','".$acturl_hour_minute."','".$line_start."',".$txt_date.')">'.$produce_minit.'</a>
									</td>
								<td class="break_all" align="right" width="50">'.number_format($target_effi,0).'%</td>
								<td class="break_all" align="right" width="50">'.number_format($achive_effi,0).'%</td>
								<td class="break_all" align="right" width="50">'.number_format($efficiency_gap,0).'%</td>';
								if($lc==0)
								{
									$html.='<td rowspan="'.$rowspan[$f_id][$sl][$l_id].'" width="50" align="right">'.number_format($style_change,0).'</td>';
									$lc++;
									$total_style_change += $style_change;
									$floor_style_change += $style_change;
								}

								$html.='<td class="break_all" align="left" width="180" ><p>'.$production_data_arr[$f_id][$l_id][$style_key]['remarks'].'</p></td>
								<td class="break_all" width="50" title="Cm Pre='.($tot_cm.'/'.$dzn_qnty).'/'.$cm_counter.';" align="right">'.number_format($avg_cm,2).'</td>
								<td class="break_all" align="right" width="50">'.number_format($ttl_cm,2).'</td>
								<td class="break_all" align="right" width="50">'.number_format($target_cm,2).'</td>
								<td class="break_all" align="right" width="50">'.number_format($avg_rate,2).'</td>
								<td class="break_all" align="right" width="80">'.number_format($ttl_fob_val,2).'</td>
								<td class="break_all" align="right" width="80">'.number_format($target_value_fob,2).'</td>';

							$html.='</tr>';
							$i++;
							$check_arr[$f_id]=$f_id;
							$f_chk_arr[$f_id]=$f_id;
					//}
				}
			}

		}
	}
			$html.='<tr  bgcolor="#B6B6B6">
					<td class="break_all" width="20">&nbsp;</td>
					<td class="break_all" width="50">&nbsp;</td>
					<td class="break_all" width="100">&nbsp;</td>
					<td class="break_all" width="100">&nbsp;</td>
					<td class="break_all" width="75">&nbsp;</td>
					<td class="break_all" width="80" title="'.$f_id.'"><p><b>Floor Total('.$floorArr[$f_id].')</b><p></td>

					<td class="break_all" align="right" width="30">&nbsp;</td>
					<td class="break_all" align="right" width="50">'.$floor_tgt_h.'</td>
					<td class="break_all" align="right" width="50">'. $floor_working_hour.'</td>
					<td class="break_all" align="right" width="50">'. $floor_prod_hour.'</td>
					<td class="break_all" align="right" width="50">'. $floor_operator.'</td>
					<td class="break_all" align="right" width="50">'. $floor_helper.'</td>
					<td class="break_all" align="right" width="50">'. $floor_man_power.'</td>
					<td class="break_all" align="right" width="50">'. $floor_days_run.'</td>
					<td class="break_all" align="right" width="50">'. number_format($eff_target_floor,0).'</td>';
					$p=1;
					for($k=$hour; $k<=$last_hour; $k++)
					{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
						// if($p <= 11)
						// {
							$html.='<td class="break_all" align="right" width="36" style='.$bg_color_.' >'. $floor_production[$prod_hour].'</td>';
						// }
						/*elseif ($floor_production[$prod_hour]>0 && $p>11)
						{
							$html.='<td class="break_all" align="right" width="70" style='.$bg_color_.' >'. $floor_production[$prod_hour].'</td>';
						}*/
						$p++;
					}
					$html.='<td class="break_all" align="right" width="50">'.number_format($floor_tot_prod,0).'</td>
					<td class="break_all" align="right" width="50">'.number_format((($floor_tot_prod/$eff_target_floor)*100),0).'%</td>
					<td class="break_all" align="right" width="50">'.number_format($floor_target_gap,0).'</td>
					<td class="break_all" align="right" width="50">'.number_format($floor_target_min,0).'</td>
					<td class="break_all" align="right" width="80">'.number_format($floor_avale_minute,2).'</td>
					<td class="break_all" align="right" width="50">'.number_format($floor_produc_min,0).'</td>
					<td class="break_all" align="right" width="50">'.number_format((($floor_target_min/$floor_avale_minute)*100),0).'%</td>
					<td class="break_all" align="right" width="50">'. number_format((($floor_produc_min/$floor_avale_minute)*100),0).'%</td>
					<td class="break_all" align="right" width="50">'. number_format((($floor_target_min/$floor_avale_minute)*100) - (($floor_produc_min/$floor_avale_minute)*100),0).'%</td>
					<td class="break_all" align="right" width="50">'. number_format($floor_style_change,0).'</td>
					<td class="break_all" align="right" width="180"></td>

					<td class="break_all" width="50" align="right">'.number_format($floor_avg_cm,2).'</td>
					<td class="break_all" width="50" align="right">'.number_format($floor_ttl_cm,2).'</td>
					<td class="break_all" width="50" align="right">'.number_format($floor_target_cm,2).'</td>
					<td class="break_all" width="50" align="right">'.number_format($floor_avg_rate,2).'</td>
					<td class="break_all" width="80" align="right">'.number_format($floor_ttl_fob_val,2).'</td>
					<td class="break_all" width="80" align="right">'.number_format($floor_target_value_fob,2).'</td>
					</tr>
					</tbody>';

					$smv_for_item="";
					$tbl_width = 1795+($last_hour - ($hour+1))*40;
					// echo $tbl_width;die();
					$colspan = 32+($last_hour - ($hour+1));
				?>

	<fieldset style="width:<? echo $tbl_width+20;?>px">
       <table width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
            <tr class="form_caption">
                <td colspan="<? echo $colspan;?>" align="center"><strong><? echo $report_title; ?> &nbsp;</strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="<? echo $colspan;?>" align="center"><strong><? echo $companyArr[$company_id]; ?></strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="<? echo $colspan;?>" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date)) ); ?></strong></td>
            </tr>
        </table>
        <br />
        <!-- <table  width="600" cellpadding="0"  cellspacing="0" align="center" style="padding-left:200px">
            <tr>
                <td bgcolor="#FFFF66" height="18" width="30" ></td>
                <td> &nbsp;Lunch Hour</td>
                <td bgcolor="red" height="18" width="30"></td>
                <td> &nbsp;Efficiency % less than Standard And Production less than Target</td>
            </tr>
        </table> -->

        <table id="table_header_1" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th class="break_all" width="20">SL</th>
                    <th class="break_all" width="50">Line No</th>
                    <th class="break_all" width="100">Buyer</th>
                    <th class="break_all" width="100">Style Ref.</th>
                    <th class="break_all" width="75">Job</th>
                    <th class="break_all" width="80">Garments Item</th>
                    <th class="break_all" width="30">SMV</th>
                    <th class="break_all" width="50">Hourly Target (Pcs)</th>
                    <th class="break_all" width="50">Plan/W.Hour</th>
                    <th class="break_all" width="50">Prod. Hour</th>
                    <th class="break_all" width="50">Operator</th>
                    <th class="break_all" width="50">Helper</th>
                    <th class="break_all" width="50"> Man Power</th>
                    <th class="break_all" width="50">Days Run</th>
                    <th class="break_all" width="50">Total Target</th>
                   <?
                   // print_r($production_hour);
                   	$p = 1;
                	for($k=$hour; $k<=$last_hour; $k++)
					{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";

						// if($p <= 11)
						// {
							?>
		                      <th class="break_all" width="36" style="vertical-align:middle"><div class="block_div"><?  echo substr($start_hour_arr[$k],0,5)."-".substr($start_hour_arr[$k+1],0,5); ?></div></th>
							<?
						// }
						/*elseif ($production_hour[$prod_hour]>0 && $p>11)
						{
							?>
		                      <th class="break_all" width="70" style="vertical-align:middle"><div class="block_div"><?  echo substr($start_hour_arr[$k],0,5)."-".substr($start_hour_arr[$k+1],0,5); ?></div></th>
							<?
						}*/
						$p++;
					}
                	?>
                    <th class="break_all" width="50">Total Prod.</th>
                    <th class="break_all" width="50">Line Acv.</th>
                    <th class="break_all" width="50">Target Gap</th>
                    <th class="break_all" width="50">Target Min</th>
                    <th class="break_all" width="80">Available Min.</th>
                    <th class="break_all" width="50">Produce/Earn Min</th>
                    <th class="break_all" width="50">Target Eff.%</th>
                    <th class="break_all" width="50">Acv. Eff.%</th>
                    <th class="break_all" width="50">Eff. Gap</th>
                    <th class="break_all" width="50">Style Change</th>
                    <th class="break_all" width="180">Remarks</th>
                    <th class="break_all" width="50">CM/PC</th>
                    <th class="break_all" width="50">TTL CM</th>
                    <th class="break_all" width="50">Target CM</th>
                    <th class="break_all" width="50">Unit Price</th>
                    <th class="break_all" width="80">Ttl. Value(FOB)</th>
                    <th class="break_all" width="80">Target Value(FOB)</th>
                </tr>
            </thead>
        </table>
        <div style="width:<?= $tbl_width+20;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
              <? echo $html;  ?>
                <tfoot>
                   <tr>
                        <th class="break_all" width="20">&nbsp;</th>
                        <th class="break_all" width="50">&nbsp;</th>
                        <th class="break_all" width="100">&nbsp;</th>
                        <th class="break_all" width="100">&nbsp;</th>
                        <th class="break_all" width="75">&nbsp;</th>
                        <th class="break_all" width="80">Grand Total</th>

                        <th class="break_all" align="right" width="30">&nbsp;</th>
                        <th class="break_all" align="right" width="50"><? echo number_format($gnd_total_tgt_h,0); ?>&nbsp;</th>
                        <th class="break_all" align="right" width="50"><? echo $total_working_hour; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="50"><? echo $total_prod_hour; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="50"><?  echo $total_operator; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="50"><?  echo $total_helper; ?></th>
                        <th class="break_all" align="right" width="50"><?  echo $total_man_power; ?></th>
                        <th class="break_all" align="right" width="50"><?  echo $total_days_run; ?></th>
                        <th class="break_all" align="right" width="50"><? echo number_format($total_terget,0); ?></th>
                        <?
                        $p = 1;
						for($k=$hour; $k<=$last_hour; $k++)
						{
							$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";

							// if($p <= 11)
							// {
								?>
			                      <th class="break_all" align="right" width="36"><? echo $total_production[$prod_hour]; ?></th>
								<?
							// }
							/*elseif ($total_production[$prod_hour]>0 && $p>11)
							{
								?>
			                      <th class="break_all" align="right" width="70"><? echo $total_production[$prod_hour]; ?></th>
								<?
							}*/
							$p++;
						}
                        ?>
                        <th class="break_all" align="right" width="50"><? echo number_format($total_tot_prod,0); ?></th>
                        <th class="break_all" align="right" width="50"><? echo number_format((($total_tot_prod/$total_terget)*100),0); ?>%</th>
                        <th class="break_all" align="right" width="50"><? echo number_format($total_target_gap,0); ?></th>
                        <th class="break_all" align="right" width="50"><? echo number_format($total_target_min,0); ?>&nbsp;</th>
                        <th class="break_all" align="right" width="80" ><? echo number_format($gnd_avable_min,2);?>&nbsp;</th>
                        <th class="break_all" align="right" width="50"><? echo number_format($gnd_product_min,0); ?>&nbsp;</th>
                        <th class="break_all" align="right" width="50"><? echo number_format((($total_target_min/$gnd_avable_min)*100),0); ?>%</th>
                        <th class="break_all" align="right" width="50"><? echo number_format((($gnd_product_min/$gnd_avable_min)*100),0); ?>%&nbsp;</th>
                        <th class="break_all" align="right" width="50"><? echo number_format((($total_target_min/$gnd_avable_min)*100)-(($gnd_product_min/$gnd_avable_min)*100) ,0); ?>%&nbsp;</th>
                        <th class="break_all" align="right" width="50"><? echo number_format($total_style_change,0); ?>&nbsp;</th>
                        <th class="break_all" align="right" width="180">&nbsp;</th>

                        <th class="break_all" align="right" width="50"><? echo number_format($total_avg_cm,2);?>&nbsp;</th>
                        <th class="break_all" align="right" width="50"><? echo number_format($total_ttl_cm,2);?>&nbsp;</th>
                        <th class="break_all" align="right" width="50"><? echo number_format($total_target_cm,2);?>&nbsp;</th>

                        <th class="break_all" align="right" width="50"><? echo number_format($total_avg_rate,2);?>&nbsp;</th>
                        <th class="break_all" align="right" width="80"><? echo number_format($total_ttl_fob_val,2);?>&nbsp;</th>
                        <th class="break_all" align="right" width="80"><? echo number_format($total_target_value_fob,2);?>&nbsp;</th>

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
if($action=="report_generate3") //Show 2
{
	?>
	<style type="text/css">
		/*.block_div {
				width:auto;
				height:auto;
				text-wrap:normal;
				vertical-align:bottom;
				display: block;
				position: !important;
				-webkit-transform: rotate(-90deg);
				-moz-transform: rotate(-90deg);
		}*/
		table tr th,table tr td
		{
			word-wrap: break-word;
			word-break: break-all;
		}

   </style>
	<?
	extract($_REQUEST);
	$process = array( &$_POST );

	//change_date_format($txt_date);die;

	$txt_date=change_date_format($txt_date,'','',1);
	$txt_date="'".$txt_date."'";

	extract(check_magic_quote_gpc( $process ));
	$companyArr = return_library_array("select id,company_name from lib_company","id","company_name");
	$buyerArr = return_library_array("select id,short_name from lib_buyer","id","short_name");
	$locationArr = return_library_array("select id,location_name from lib_location","id","location_name");
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
	$lineArr = return_library_array("select id,line_name from lib_sewing_line where company_name=$cbo_company_id","id","line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	$company_id=str_replace("'","",$cbo_company_id);
    $today_date=date("Y-m-d");
	$txt_producting_day="".str_replace("'","",$txt_date)."";

	//**********************************************************************************************
	$lineDataArr = sql_select("SELECT id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1 and company_name=$cbo_company_id order by sewing_line_serial");
	foreach($lineDataArr as $lRow)
	{
		$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
		$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
		$lastSlNo=$lRow[csf('sewing_line_serial')];
	}


	
	$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$company_id and shift_id=1 and pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
	

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
	
	$start_time_data_arr=sql_select("select company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time,TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($company_id) and  shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");
	

	foreach($start_time_data_arr as $row)
	{
		$start_time_arr[$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
		$start_time_arr[$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
	}

	$prod_start_hour=$start_time_arr[1]['pst'];
	$global_start_lanch=$start_time_arr[1]['lst'];
	if($prod_start_hour=="") $prod_start_hour="08:00";
	$start_time=explode(":",$prod_start_hour);
	// $hour=substr($start_time[0],1,1);
	$hour=$start_time[0]*1;
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
	$hour_line=$first_hour_time[0]*1; $minutes_one=$start_time[1];
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
	/										prod resource data								/
	/===================================================================================== */
	// echo $prod_reso_allo[0]; die;
	$prod_res_cond = "";
	$prod_res_cond .= (str_replace("'", "", $cbo_location_id)==0) ? "" : " and a.location_id=$cbo_location_id";
	$prod_res_cond .= (str_replace("'", "", $cbo_floor_id)==0) ? "" : " and a.floor_id=$cbo_floor_id";
	$prod_res_cond .= (str_replace("'", "", $hidden_line_id)=="") ? "" : " and a.id in(".str_replace("'", "", $hidden_line_id).")";

	if($prod_reso_allo[0]==1)
	{
		$prod_resource_array=array();
		$prod_resource_array2=array();
		$prod_resource_smv_array = array();

		$dataArray_sql=sql_select("SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id=$company_id and b.pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $prod_res_cond");

		foreach($dataArray_sql as $val)
		{
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['man_power']=$val[csf('man_power')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['operator']=$val[csf('operator')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['helper']=$val[csf('helper')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['terget_hour']=$val[csf('target_per_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['working_hour']=$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['tpd']=$val[csf('target_per_hour')]*$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_start']=$val[csf('from_date')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_end']=$val[csf('to_date')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['capacity']=$val[csf('capacity')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust']=$val[csf('smv_adjust')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];
		}
		// echo "<pre>";print_r($production_serial_arr);die();

		// =======================================================
		$sql="SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, c.from_date,c.to_date,c.capacity,d.po_id,d.target_per_line,d.operator, d.helper,d.working_hour,d.actual_smv,d.gmts_item_id from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c,prod_resource_color_size d where a.id=c.mst_id and c.id=b.mast_dtl_id and d.mst_id=a.id and d.dtls_id=c.id and a.company_id=$company_id and b.pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $prod_res_cond";
		// echo $sql;die();
		$sql_res=sql_select($sql);
		$poIds_arr = array();
		foreach($sql_res as $vals)
		{
			$poIds_arr[$vals[csf('po_id')]] = $vals[csf('po_id')];
		}
		$poIds = implode(",", $poIds_arr);
		$job_no_arr = return_library_array("SELECT a.job_no,b.id from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and a.status_active=1 and b.status_active=1 and b.id in($poIds)","id","job_no");
		foreach($sql_res as $val)
		{
			$prod_resource_array2[$val[csf('id')]][$job_no_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['man_power']+=$val[csf('operator')]+$val[csf('helper')];
			$prod_resource_array2[$val[csf('id')]][$job_no_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['operator']+=$val[csf('operator')];
			$prod_resource_array2[$val[csf('id')]][$job_no_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['helper']+=$val[csf('helper')];
			$prod_resource_array2[$val[csf('id')]][$job_no_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['terget_hour']+=$val[csf('target_per_line')];
			$prod_resource_array2[$val[csf('id')]][$job_no_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['working_hour']+=$val[csf('working_hour')];
			$prod_resource_array2[$val[csf('id')]][$job_no_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['tpd']+=$val[csf('target_per_line')]*$val[csf('working_hour')];
			$prod_resource_array2[$val[csf('id')]][$job_no_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['day_start']=$val[csf('from_date')];
			$prod_resource_array2[$val[csf('id')]][$job_no_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['day_end']=$val[csf('to_date')];
			$prod_resource_array2[$val[csf('id')]][$job_no_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['capacity']+=$val[csf('capacity')];
			$prod_resource_array2[$val[csf('id')]][$job_no_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['smv_adjust']+=$val[csf('smv_adjust')];
			$prod_resource_array2[$val[csf('id')]][$job_no_arr[$val[csf('po_id')]]][$val[csf('pr_date')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];
			$prod_resource_smv_array[$val[csf('id')]][$job_no_arr[$val[csf('po_id')]]][$val[csf('gmts_item_id')]][$val[csf('pr_date')]]['actual_smv']=$val[csf('actual_smv')];


		}
		
		// echo "<pre>"; print_r($prod_resource_array2);die();

		if(str_replace("'","",trim($txt_date))==""){$pr_date_con="";}else{$pr_date_con=" and b.pr_date=$txt_date";}

		$dataArray=sql_select("SELECT a.id,b.pr_date,d.shift_id,TO_CHAR(d.prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR( d.lunch_start_time,'HH24:MI') as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$company_id and shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 $pr_date_con");
		
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
		$sql_query="SELECT b.mst_id, b.pr_date,b.number_of_emp ,b.adjust_hour,b.total_smv,b.adjustment_source,b.style_ref_no as style from prod_resource_mst a,prod_resource_smv_adj b  where a.id=b.mst_id  and a.company_id=$company_id and b.pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 and b.status_active=1  $prod_res_cond";
		// echo $sql_query;
		$sql_query_res=sql_select($sql_query);
		foreach($sql_query_res as $val)
		{
			$val[csf('pr_date')]=date("d-M-Y",strtotime($val[csf('pr_date')]));

			$prod_resource_smv_adj_array[$val[csf('style')]][$val[csf('mst_id')]][$val[csf('pr_date')]][$val[csf('adjustment_source')]]['number_of_emp']+=$val[csf('number_of_emp')];
			$prod_resource_smv_adj_array[$val[csf('style')]][$val[csf('mst_id')]][$val[csf('pr_date')]][$val[csf('adjustment_source')]]['adjust_hour']+=$val[csf('adjust_hour')];
			$prod_resource_smv_adj_array[$val[csf('style')]][$val[csf('mst_id')]][$val[csf('pr_date')]][$val[csf('adjustment_source')]]['total_smv']+=$val[csf('total_smv')];

		}

		// echo "<pre>";print_r($prod_resource_smv_adj_array);die();


	}

	// print_r($extra_minute_arr);die;

 	//*********************************************************************
  
	$manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 and id=$company_id","company_id");
	// echo $manufacturing_company;die();
	$prod_start_cond="TO_CHAR(prod_start_time,'DD-MON-YYYY HH24:MI')";

	$variable_start_time_arr='';
	$prod_start_time=sql_select("select $prod_start_cond as prod_start_time from variable_settings_production where company_name=$cbo_company_id and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");
	//echo "select company_name, prod_start_time from variable_settings_production where company_name=$cbo_company_id and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1";
	foreach($prod_start_time as $row)
	{
		$ex_time=explode(" ",$row[csf('prod_start_time')]);
		$variable_start_time_arr=$ex_time[1];
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
	/*===================================================================================== /
	/										smv sorce 										/
	/===================================================================================== */
   	$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($manufacturing_company) and variable_list=25 and   status_active=1 and is_deleted=0");
	// echo $smv_source;
    if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;


	
	$pr_date=str_replace("'","",$txt_date);
	$pr_date_old=explode("-",str_replace("'","",$txt_date));
	$month=strtoupper($pr_date_old[1]);
	$year=substr($pr_date_old[2],2);
	$pr_date=$pr_date_old[0]."-".$month."-".$year;
	
	$i=1; $grand_total_good=0; $grand_alter_good=0; $grand_total_reject=0;
	$html="";
	$floor_html="";
    $check_arr=array();


	/*===================================================================================== /
	/								get inhouse production data								/
	/===================================================================================== */
	
	$sql="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name  as buyer_name,b.style_ref_no,b.job_no,b.id as job_id, a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.file_no,c.unit_price,c.grouping as ref,d.color_type_id,a.remarks,e.floor_serial_no,sum(d.production_qnty) as good_qnty,";
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
	$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN production_qnty else 0 END) AS prod_hour23 FROM  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a ,pro_garments_production_dtls d,lib_prod_floor e WHERE a.production_type=5 and a.po_break_down_id=c.id and c.job_id=b.id and a.id=d.mst_id and e.id=a.floor_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_name $location $floor $line $buyer_id_cond  $txt_date_from GROUP BY b.job_no, a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name,b.style_ref_no,a.item_number_id,c.po_number,c.unit_price,c.file_no,c.grouping ,d.color_type_id,a.remarks,e.floor_serial_no ORDER BY a.location,e.floor_serial_no";
	
	// echo $sql;die;
	$sql_resqlt=sql_select($sql);
	$production_data_arr=array();
	$production_po_data_arr=array();
	$production_serial_arr=array();
	$reso_line_ids='';
	$all_po_id="";
	$all_job_id="";
	$active_days_arr=array();
	$duplicate_date_arr=array();
	$poIdArr=array();
	$prod_line_array = array();
	$line_style_chk_array = array();
	$line_wise_style_array = array();
	foreach($sql_resqlt as $val)
	{
		$prod_line_array[$val[csf('sewing_line')]] = $val[csf('sewing_line')];
		$poIdArr[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
		if($val[csf('prod_reso_allo')]==1)
		{
			$sewing_line_ids=$prod_reso_arr[$val[csf('sewing_line')]];
			$sl_ids_arr = explode(",", $sewing_line_ids);
			$sewing_line_id = $sl_ids_arr[0]; // always 1st line id will take

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
		$production_serial_arr[$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]][$val[csf('job_no')]]=$val[csf('style_ref_no')];

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
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]][$prod_hour]+=$val[csf($prod_hour)];

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

	 	$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]['prod_hour23']+=$val[csf('prod_hour23')];
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['prod_reso_allo']=$val[csf('prod_reso_allo')];

	 	if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['buyer_name']!="")
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['buyer_name'].=",".$val[csf('buyer_name')];
		}
	 	else
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['buyer_name']=$val[csf('buyer_name')];
		}

		if($line_style_chk_array[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]] =="")
		{
			$line_wise_style_count_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]++;
			$line_wise_style_count_arr2[$val[csf('floor_id')]][$val[csf('sewing_line')]]++;
			$line_style_chk_array[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]] = $val[csf('job_no')];
		}

		/*if($line_wise_style_count_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]!="")
		{
			$line_wise_style_count_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]].=",".$val[csf('buyer_name')];
		}
	 	else
		{
			$line_wise_style_count_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]=$val[csf('buyer_name')];
		}*/

		if($line_wise_style_array[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style']!="")
		{
			$line_wise_style_array[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style'].="##".$val[csf('job_no')];
		}
	 	else
		{
			$line_wise_style_array[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style']=$val[csf('job_no')];
		}


	 	if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['po_number']!="")
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['po_number'].=",".$val[csf('po_number')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['job_no'].=",".$val[csf('job_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['po_id'].=",".$val[csf('po_break_down_id')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['style'].="##".$val[csf('style_ref_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['file'].=",".$val[csf('file_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['ref'].=",".$val[csf('ref')];
		}
	 	else
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['po_number']=$val[csf('po_number')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['job_no']=$val[csf('job_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['po_id']=$val[csf('po_break_down_id')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['style']=$val[csf('style_ref_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['file']=$val[csf('file_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['ref']=$val[csf('ref')];
		}
		if ($val[csf('remarks')] !="")
		{
		 	if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['remarks']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['remarks'].=",".$val[csf('remarks')];
			}
		 	else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['remarks']=$val[csf('remarks')];
			}
		}

		if($po_rate_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['rate']!="")
		{
			$po_rate_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['rate'].=",".$val[csf('unit_price')];
		}
		else
		{
			$po_rate_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['rate'] = $val[csf('unit_price')];
		}

		if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['item_number_id']!="")
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('unit_price')];
		}
		else
		{
			 $production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('unit_price')];
		}
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('job_no')]]['quantity']+=$val[csf('good_qnty')];
		$production_data_arr_qty[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]['quantity']+=$val[csf('good_qnty')];

		if($all_po_id=="") $all_po_id=$val[csf('po_break_down_id')]; else $all_po_id.=",".$val[csf('po_break_down_id')];
		if($all_job_id=="") $all_job_id=$val['JOB_ID']; else $all_job_id.=",".$val['JOB_ID'];
	}

	

	// $production_serial_arr[$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]][$val[csf('style_ref_no')]]=$val[csf('sewing_line')];
	// $production_serial_arr[5000][1][8000][0]=8000;



	// echo $all_po_id;
	// echo "<pre>"; print_r($production_data_arr);die();
	/*===================================================================================== /
	/										po item wise smv 								/
	/===================================================================================== */
	$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no  and b.id in($all_po_id) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3)";
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
		if($po_ids>1000)
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
    $po_active_sql="SELECT a.floor_id,a.sewing_line,a.production_date,a.po_break_down_id,a.item_number_id from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $company_name $location $floor $line $buyer_id_cond  $poIds_cond2 group by  a.floor_id,a.sewing_line,a.production_date ,a.po_break_down_id,a.item_number_id";
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
	if ($all_po_id !='') 
	{
	 
	 
		$sql_item_rate="SELECT b.id, c.item_number_id, c.order_quantity, c.order_total from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c where b.job_no_mst=a.job_no and b.id=c.po_break_down_id and b.job_no_mst=c.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1  $file_cond $ref_cond  $poIds_cond";
		// echo $sql_item_rate; die;
		$resultRate=sql_select($sql_item_rate);
		
		$item_po_array=array();
		foreach($resultRate as $row)
		{
			$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['qty']+=$row[csf('order_quantity')];
			$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['amt']+=$row[csf('order_total')];
		}
	}
	
	/*===================================================================================== /
	/										subcoutact data									/
	/===================================================================================== */
    
	$sql_sub_contuct= "SELECT a.company_id, a.location_id, a.floor_id,d.floor_serial_no,e.sewing_line_serial, a.production_date, a.line_id,b.party_id  as buyer_name,a.prod_reso_allo,a.order_id,c.order_no as po_number,c.cust_style_ref, b.subcon_job as job_no,  max(c.smv) as smv,a.prod_reso_allo,sum(a.production_qnty) as good_qnty,c.gmts_item_id as item,";
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

	$sql_sub_contuct.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]'	and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.id  and d.is_deleted=0 left join lib_sewing_line e on a.line_id=e.id and e.is_deleted=0 where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id=$company_id $subcon_location $floor $subcon_line   $txt_date_from group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date,  b.subcon_job,a.line_id,b.party_id,c.order_no,c.cust_style_ref,e.sewing_line_serial,a.prod_reso_allo,c.gmts_item_id order by a.location_id, a.floor_id,e.sewing_line_serial,a.prod_reso_allo";

	// echo $sql_sub_contuct;die;
	$sub_result=sql_select($sql_sub_contuct);
	$subcon_order_smv=array();
	foreach($sub_result as $subcon_val)
	{
		$subcon_job_no 	= $subcon_val['JOB_NO'];
		$subcon_floor 	= $subcon_val['FLOOR_ID'];
		$subcon_line_id = $subcon_val['LINE_ID'];
		$subcon_style 	= $subcon_val['CUST_STYLE_REF'];
		$subcon_po_id 	= $subcon_val['ORDER_ID'];
		$subcon_item 	= $subcon_val['ITEM'];

		$prod_line_array[$subcon_line_id] = $subcon_line_id;
		if($subcon_val[csf('prod_reso_allo')]==1)
		{
			$sewing_line_id=$prod_reso_arr[$subcon_line_id];
		}
		else
		{
			$sewing_line_id=$subcon_line_id;
		}

		if($lineSerialArr[$sewing_line_id]=="")
		{
			$lastSlNo++;
			$slNo=$lastSlNo;
			$lineSerialArr[$sewing_line_id]=$slNo;
		}
		else $slNo=$lineSerialArr[$sewing_line_id];
		
		$production_serial_arr[$subcon_floor][$slNo][$subcon_line_id][$subcon_job_no]= $subcon_style; 

	 	$line_start=$line_number_arr[$subcon_line_id][$subcon_val[csf('production_date')]]['prod_start_time']	;
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
			$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_style][$prod_hour]+=$subcon_val[csf($prod_hour)];
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
			{
				 if( $h>=$line_start_hour && $h<=$actual_time)
				 {
				 $production_po_data_arr[$subcon_floor][$subcon_line_id][$subcon_po_id]+=$val[csf($prod_hour)];	                 }
			}
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
			{
				$production_po_data_arr[$subcon_floor][$subcon_line_id][$subcon_po_id]+=$val[csf($prod_hour)];	            }
		 }
		if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
		{
			if( $h>=$line_start_hour && $h<=$actual_time)
			{
				$production_po_data_arr[$subcon_floor][$subcon_line_id][$subcon_po_id]+=$val[csf('prod_hour23')];
			}
		}
		else
		{
			$production_po_data_arr[$subcon_floor][$subcon_line_id][$subcon_po_id]+=$val[csf('prod_hour23')];
		}
		$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_style]['prod_hour23']+=$val[csf('prod_hour23')];


		// ============================================================================================
												// SUBCON ORDER INFORMATION
		// ============================================================================================
		$production_po_data_arr[$subcon_floor][$subcon_line_id][$subcon_po_id]+=$subcon_val[csf('good_qnty')];
		if($production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['buyer_name']!="")
		{
			$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['buyer_name'].=",".$subcon_val[csf('buyer_name')];
		}
		else
		{
			$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['buyer_name']=$subcon_val[csf('buyer_name')];
		}
		if($line_style_chk_array[$subcon_floor][$subcon_line_id][$subcon_style] =="")
		{
			$line_wise_style_count_arr[$subcon_floor][$subcon_line_id]++;
			$line_wise_style_count_arr2[$subcon_floor][$subcon_line_id]++;
			$line_style_chk_array[$subcon_floor][$subcon_line_id][$subcon_style] = $subcon_style;
		}

		if($production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['po_number']!="")
		{
			$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['po_number'].=",".$subcon_val[csf('po_number')];
			$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['job_no'].=",".$subcon_job_no;
			$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['style'].="##".$subcon_style; 
		}
		else
		{
			$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['po_number']=$subcon_val[csf('po_number')];
			$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['job_no']=$subcon_job_no;
			$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['style']=$subcon_style;
		} 
		if($production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['item_number_id']!="")
		{
			$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['item_number_id'].="****".$subcon_po_id."**".$subcon_item; //."**".$v['UNIT_PRICE']
		}
		else
		{
			$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['item_number_id']=$subcon_po_id."**".$subcon_item; //."**".$v['UNIT_PRICE']
		} 
		if($production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['order_id']!="")
		{
			$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['order_id'].=",".$subcon_po_id;
		}
		else
		{
			$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_job_no]['order_id'].=$subcon_po_id;
		}
		$subcon_order_smv[$subcon_po_id]=$subcon_val[csf('smv')];
		$production_data_arr[$subcon_floor][$subcon_line_id][$subcon_style]['quantity']+=$subcon_val[csf('good_qnty')];											
	}
	// pre($production_data_arr); die;
	/*===================================================================================== /
	/							prod resource data no prod line								/
	/===================================================================================== */
	// echo $prod_reso_allo[0]; die;

	$prod_line_ids = implode(",", $prod_line_array);
	// echo $prod_line_ids; die; 
	if($prod_reso_allo[0]==1)
	{
		$prod_line_cond = ($prod_line_ids !='')? " and a.id not in($prod_line_ids)" : ""; 
		$dataArray_sql=sql_select("SELECT a.id, a.location_id, a.floor_id, a.line_number, e.po_id from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c,lib_prod_floor d,prod_resource_color_size e where a.id=c.mst_id and c.id=b.mast_dtl_id and c.id=e.dtls_id and d.id=a.floor_id and a.id=e.mst_id and a.company_id=$company_id $prod_line_cond and b.pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $prod_res_cond order by d.floor_serial_no ");
		$po_wise_data_array = array();
		$no_line_po_array = array();
		foreach($dataArray_sql as $v)
		{
            $po_wise_data_array[$v['PO_ID']]['floor'] = $v['FLOOR_ID'];
			$po_wise_data_array[$v['PO_ID']]['line'] = $v['ID'];

			$no_line_po_array[$v['PO_ID']] = $v['PO_ID']; 
		}
	}
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1) and ENTRY_FORM=101");
	oci_commit($con); 
	
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 101, 1, $no_line_po_array, $empty_arr);//PO ID

	/*===================================================================================== /
	/							Get Order Data 
	/===================================================================================== */
	$order_sql = "select a.job_no,a.id as job_id,a.style_ref_no as style,b.po_number,c.item_number_id,a.buyer_name,b.unit_price,b.id as po_id from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,gbl_temp_engine tmp where a.id=b.job_id and b.id=c.po_break_down_id and b.id=tmp.ref_val and tmp.entry_form=101 and tmp.user_id=$user_id and tmp.ref_from=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	// echo $order_sql; die;
	$order_sql_res=sql_select($order_sql);
	foreach ($order_sql_res as  $v) 
	{
		$line_id   = $po_wise_data_array[$v['PO_ID']]['line'];
		$floor_id  = $po_wise_data_array[$v['PO_ID']]['floor'];

		$sewing_line_ids=$prod_reso_arr[$line_id];
		$sl_ids_arr = explode(",", $sewing_line_ids);
		$sewing_line_id = $sl_ids_arr[0]; // always 1st line id will take

		if($lineSerialArr[$sewing_line_id]=="")
		{
			$lastSlNo++;
			$slNo=$lastSlNo;
			$lineSerialArr[$sewing_line_id]=$slNo;
		}
		else $slNo=$lineSerialArr[$sewing_line_id];


		$production_serial_arr[$floor_id][$slNo][$line_id][$v['JOB_NO']]= $v['STYLE'];
		if($all_job_id=="") $all_job_id=$v['JOB_ID']; else $all_job_id.=",".$v['JOB_ID'];
			
			

		if($production_data_arr[$floor_id][$line_id][$v['JOB_NO']]['po_number']!="")
		{
			$production_data_arr[$floor_id][$line_id][$v['JOB_NO']]['po_number'].=",".$v['PO_NUMBER'];
			$production_data_arr[$floor_id][$line_id][$v['JOB_NO']]['job_no'].=",".$v['JOB_NO'];
			$production_data_arr[$floor_id][$line_id][$v['JOB_NO']]['buyer_name'].=",".$v['JOB_NO'];
		}
		else
		{
			$production_data_arr[$floor_id][$line_id][$v['JOB_NO']]['po_number']=$v['PO_NUMBER'];
			$production_data_arr[$floor_id][$line_id][$v['JOB_NO']]['job_no']=$v['JOB_NO'];
			$production_data_arr[$floor_id][$line_id][$v['JOB_NO']]['buyer_name']=$v['BUYER_NAME'];
		}

		if($line_wise_style_array[$floor_id][$line_id]['style']!="")
		{
			$line_wise_style_array[$floor_id][$line_id]['style'].="##".$v['STYLE'];
		}
		else
		{
			$line_wise_style_array[$floor_id][$line_id]['style']=$v['STYLE'];
		}
		
		if($production_data_arr[$floor_id][$line_id][$v['JOB_NO']]['item_number_id']!="")
		{
			$production_data_arr[$floor_id][$line_id][$v['JOB_NO']]['item_number_id'].="****".$v['PO_ID']."**".$v['ITEM_NUMBER_ID']."**".$v['UNIT_PRICE'];
		}
		else
		{
			$production_data_arr[$floor_id][$line_id][$v['JOB_NO']]['item_number_id']=$v['PO_ID']."**".$v['ITEM_NUMBER_ID']."**".$v['UNIT_PRICE'];
		} 
	}
	// echo "<pre>"; print_r($production_data_arr); die;
	/*===================================================================================== /
	/							For Summary Report New Add No Prodcut						/
	/===================================================================================== */
	if($cbo_no_prod_type==1)
	{
		//No Production line Start ....
		$prod_reso_arr=return_library_array( "SELECT id, line_number from prod_resource_mst",'id','line_number');
		$sql_active_line=sql_select("SELECT sewing_line,sum(production_quantity) as total_production from pro_garments_production_mst  where production_date=".$txt_date." and production_type=5 and status_active=1 and is_deleted=0 and prod_reso_allo=1 group by  sewing_line");
		//$actual_line_arr=array();
		foreach($sql_active_line as $inf)
		{
		   if(str_replace("","",$inf[csf('sewing_line')])!="")
		   {
				//if(str_replace("","",$actual_line_arr)!="") $actual_line_arr.=",";
			    //$actual_line_arr.="'".$inf[csf('sewing_line')]."'";
		   }
		}
		// echo "hello";die;
		$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and is_deleted=0 and status_active=1");
		$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
		if(str_replace("'","",$cbo_location_id)==0)
		{
		$location_cond="";
		}
		else
		{
		$location=" and a.location_id=".str_replace("'","",$cbo_location_id)."";
		}

		if(str_replace("'","",$cbo_floor_id)==0) $floor=""; else $floor="and a.floor_id=".str_replace("'","",$cbo_floor_id)."";
		$lin_ids=str_replace("'","",$hidden_line_id);
		$res_line_cond=rtrim($reso_line_ids,",");

		 $dataArray_sum=sql_select("SELECT a.id, a.floor_id,1 as prod_reso_allo,2 as type_line, a.line_number as line_no, b.man_power, b.operator, b.helper, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type,d.prod_start_time from  prod_resource_dtls b,prod_resource_dtls_time d,prod_resource_mst a  where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$company_id and b.pr_date=".$txt_date." and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 and a.id not in($res_line_cond) and a.line_number like '%$lin_ids%'  $location  $floor  group by a.id, a.floor_id, a.line_number, d.prod_start_time,b.man_power, b.operator, b.helper, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type order by a.floor_id");
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
		 $dataArray_sql_cap=sql_select("select  a.floor_id, a.line_number as line_no,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id  and a.company_id=$company_id and b.pr_date=".$txt_date."  and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  group by a.id,a.line_number, a.floor_id, a.line_number, b.man_power, b.operator,c.capacity, b.helper, b.working_hour,b.target_per_hour order by a.floor_id");

		 //$prod_resource_array_summary=array();
		 foreach( $dataArray_sql_cap as $row)
		 {
			 $production_data_arr[$row[csf('floor_id')]][$row[csf('line_no')]]['capacity']=$row[csf('capacity')];
		 }

	} //End

	//echo "<pre>";
	// echo "<pre>"; print_r($production_serial_arr);die;
	$total_job_id=count(array_unique(explode(",",$all_job_id)));
	$jobIds=chop($all_job_id,',');
	$jobIds_cond=""; 
	if($all_job_id!='' || $all_job_id!=0)
	{
		if($total_job_id>1000)
		{
			$jobIds_cond=" ("; 
			$poIdsArr=array_chunk(explode(",",$jobIds),990);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$jobIds_cond.=" job_id  in($ids) or "; 
			}
			$jobIds_cond=chop($jobIds_cond,'or '); 
			$jobIds_cond.=")"; 
		}
		else
		{
			$jobIds_cond=" job_id in($all_job_id)"; 
		}
	} 
	// echo "hello"; die;
	if ($total_job_id >0) {
		$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per",$jobIds_cond); 
	}
	if(count($poIdArr)>0)
	{
		$condition= new condition();
		if($cbo_company_name>0){
			$condition->company_name("=$company_id");
		} 
			$condition->po_id_in(implode(',',$poIdArr));
		
		$condition->init();
		$other= new other($condition);
		$other_cost = $other->getAmountArray_by_job();
	}
	// echo "<pre>"; print_r($production_serial_arr);die;
	// $other_cost[$jobNumber]['cm_cost'];
	// echo "<pre>"; print_r($production_serial_arr);die();
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

    //==============================================================================================================
    //                                                CLEAR GBL DATA
	//==============================================================================================================
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1) and ENTRY_FORM=101");
	oci_commit($con); 
	disconnect($con);
	// echo "<pre>"; print_r($rowspan);die();
	// ======================================
    $avable_min=0;
	$today_product=0;
    $floor_name="";
    $floor_man_power=0;
	$floor_operator=$floor_produc_min=0;
	$floor_smv=$floor_row=$floor_helper=$floor_tgt_h=$floor_days_run=$floor_working_hour=$line_floor_production=$floor_today_product=$floor_avale_minute=$floor_line_days_run=0;
	$floor_prod_hour = 0;
	$floor_tot_prod = 0;
	$floor_line_acv = 0;
	$floor_target_gap = 0;
	$floor_target_min = 0;
	$floor_target_effi = 0;
	$floor_achive_effi = 0;
	$floor_efficiency_gap = 0;
	$floor_style_change = 0;
	$floor_avg_cm = 0;
	$floor_ttl_cm = 0;
	$floor_target_cm = 0;
	$floor_avg_rate = 0;
	$floor_ttl_fob_val = 0;
	$floor_target_value_fob = 0;

	$total_operator=$total_helper=$gnd_hit_rate=0;
    $total_smv=$total_terget=$grand_total_product=$gnd_line_effi=0;
    $total_man_power=$gnd_avable_min=$gnd_product_min=0;
	$item_smv=$item_smv_total=$line_efficiency=$days_run=$days_active=$total_working_hour=$gnd_total_tgt_h=$total_capacity=0;

	$total_prod_hour = 0;
	$total_tot_prod = 0;
	$total_line_acv = 0;
	$total_target_gap = 0;
	$total_target_min = 0;
	$total_target_effi = 0;
	$total_achive_effi = 0;
	$total_efficiency_gap = 0;
	$total_style_change = 0;
	$total_avg_cm = 0;
	$total_ttl_cm = 0;
	$total_target_cm = 0;
	$total_avg_rate = 0;
	$total_ttl_fob_val = 0;
	$total_target_value_fob = 0;

	$j=1;
	$i=1;
	
	ob_start();
	$line_number_check_arr=array();
	$smv_for_item="";
	$total_production=array();
	$floor_production=array();
    $line_floor_production=0;
    $line_total_production=0; $gnd_total_fob_val=0; $gnd_final_total_fob_val=0;
    $f_chk_arr = array();
    $line_chk_arr = array();
    $line_chk_arr2 = array();
    $html ='<tbody>';
	// echo "<pre>"; print_r($production_serial_arr); echo "**";die;
	foreach($production_serial_arr as $f_id=>$fname)
	{
		ksort($fname);
		foreach($fname as $sl=>$s_data)
		{
			foreach($s_data as $l_id=>$ldata)
			{
				$l=0;
				$pp = 0;
				$lc = 0;
				foreach ($ldata as $job_key => $style_data1)
				{
				  	$po_value=$production_data_arr[$f_id][$l_id][$job_key]['po_number'];
					// echo 	"[$f_id][$l_id][$job_key]"; die;
				 	//  if($po_value)
					// {
						if($i!=1)
						{
							if(!in_array($f_id, $check_arr))
							{
								if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								 $html.='<tr  bgcolor="#B6B6B6">
										<td class="break_all" width="20">&nbsp;</td>
										<td class="break_all" width="50">&nbsp;</td>
										<td class="break_all" width="100">&nbsp;</td>
										<td class="break_all" width="100">&nbsp;</td>
										<td class="break_all" width="75">&nbsp;</td>
										<td class="break_all" width="80" title="'.$f_id.'"><p><b>Floor Total('.$floorArr[current($f_chk_arr)].')</b><p></td>

										<td class="break_all" align="right" width="30">&nbsp;</td>
										<td class="break_all" align="right" width="50">'.$floor_tgt_h.'</td>
										<td class="break_all" align="right" width="50">'. $floor_working_hour.'</td>
										<td class="break_all" align="right" width="50">'. $floor_prod_hour.'</td>
										<td class="break_all" align="right" width="50">'. $floor_operator.'</td>
										<td class="break_all" align="right" width="50">'. $floor_helper.'</td>
										<td class="break_all" align="right" width="50">'. $floor_man_power.'</td>
										<td class="break_all" align="right" width="50">'. $floor_days_run.'</td>
										<td class="break_all" align="right" width="50">'. number_format($eff_target_floor,0).'</td>';
										$p=1;
										for($k=$hour; $k<=$last_hour; $k++)
										{
											$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
											if($p <= 11)
											{
												$html.='<td class="break_all" align="right" width="36" style='.$bg_color_.' >'. $floor_production[$prod_hour].'</td>';
											}
											/*elseif ($floor_production[$prod_hour]>0 && $p>11)
											{
												$html.='<td class="break_all" align="right" width="70" style='.$bg_color_.' >'. $floor_production[$prod_hour].'</td>';
											}*/
											$p++;
										}
										$html.='<td class="break_all" align="right" width="50">'.number_format($floor_tot_prod,0).'</td>
										<td class="break_all" align="right" width="50">'.number_format((($floor_tot_prod/$eff_target_floor)*100),0).'%</td>
										<td class="break_all" align="right" width="50">'.number_format($floor_target_gap,0).'</td>
										<td class="break_all" align="right" width="50">'.number_format($floor_target_min,0).'</td>
										<td class="break_all" align="right" width="80">'.number_format($floor_avale_minute,2).'</td>
										<td class="break_all" align="right" width="50">'.number_format($floor_produc_min,0).'</td>
										<td class="break_all" align="right" width="50">'.number_format((($floor_target_min/$floor_avale_minute)*100),0).'%</td>
										<td class="break_all" align="right" width="50">'. number_format((($floor_produc_min/$floor_avale_minute)*100),0).'%</td>
										<td class="break_all" align="right" width="50">'. number_format((($floor_target_min/$floor_avale_minute)*100) - (($floor_produc_min/$floor_avale_minute)*100),0).'%</td>
										<td class="break_all" align="right" width="50">'. number_format($floor_style_change,0).'</td>
										<td class="break_all" align="right" width="180"></td>

										<td class="break_all" width="50" align="right">'.number_format($floor_avg_cm,2).'</td>
										<td class="break_all" width="50" align="right">'.number_format($floor_ttl_cm,2).'</td>
										<td class="break_all" width="50" align="right">'.number_format($floor_target_cm,2).'</td>
										<td class="break_all" width="50" align="right">'.number_format($floor_avg_rate,2).'</td>
										<td class="break_all" width="80" align="right">'.number_format($floor_ttl_fob_val,2).'</td>
										<td class="break_all" width="80" align="right">'.number_format($floor_target_value_fob,2).'</td>';

									$gnd_total_fob_val=0;

									$html.='</tr>';
								  	$floor_name="";
								  	$floor_smv=0;
								  	$floor_row=0;
								  	$floor_operator=0;
								  	$floor_helper=0;
								  	$floor_tgt_h=0;
								  	$floor_man_power=0;
								  	$floor_days_run=0;
								  	$floor_line_days_run=0;
								  	$eff_target_floor=0;
								  	unset($floor_production);
								  	$floor_working_hour=0;
								  	$line_floor_production=0;
								  	$floor_today_product=0;
								  	$floor_avale_minute=0;
								  	$floor_produc_min=0;
								  	$floor_efficency=0;
								  	$floor_man_power=0;
								  	$floor_capacity=0;
								  	$floor_prod_hour = 0;
									$floor_tot_prod = 0;
									$floor_line_acv = 0;
									$floor_target_gap = 0;
									$floor_target_min = 0;
									$floor_target_effi = 0;
									$floor_achive_effi = 0;
									$floor_efficiency_gap = 0;
									$floor_style_change = 0;
									$floor_avg_cm = 0;
									$floor_ttl_cm = 0;
									$floor_target_cm = 0;
									$floor_avg_rate = 0;
									$floor_ttl_fob_val = 0;
									$floor_target_value_fob = 0;
								  	$j++;
								  	unset($f_chk_arr);
							}
						}
						$floor_row++;
						//$item_ids=$production_data_arr[$f_id][$l_id]['item_number_id'];
						$germents_item=array_unique(explode('****',$production_data_arr[$f_id][$l_id][$job_key]['item_number_id']));
						// print_r($germents_item);die();

						$buyer_neme_all=array_unique(explode(',',$production_data_arr[$f_id][$l_id][$job_key]['buyer_name']));
						$buyer_name="";
						foreach($buyer_neme_all as $buy)
						{
							if($buyer_name!='') $buyer_name.=',';
							$buyer_name.=$buyerArr[$buy];
						}
						$buyer_name = trim($buyer_name,',');
						$garment_itemname='';
						$active_days='';
						$item_smv="";$item_ids='';
						$smv_for_item="";
						$produce_minit="";
						$order_no_total="";
						$efficiency_min=0;
						$tot_po_qty=0;
						$fob_val=0;

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
							$item_smv.=$prod_resource_smv_array[$l_id][$job_key][$po_garment_item[1]][$pr_date]['actual_smv'];
							if($order_no_total!="") $order_no_total.=",";
							$order_no_total.=$po_garment_item[0];
							if($smv_for_item!="") $smv_for_item.="****".$po_garment_item[0]."**".$item_smv;
							else
							$smv_for_item=$po_garment_item[0]."**".$item_smv;
							$produce_minit+=$production_po_data_arr[$f_id][$l_id][$po_garment_item[0]]*$prod_resource_smv_array[$l_id][$job_key][$po_garment_item[1]][$pr_date]['actual_smv'];
							// echo $production_po_data_arr[$f_id][$l_id][$po_garment_item[0]]."*".$prod_resource_smv_array[$l_id][$job_key][$po_garment_item[1]][$pr_date]['actual_smv']."<br>";
							$fob_rate=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt']/$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];
							$prod_qty=$production_data_arr_qty[$f_id][$l_id][$po_garment_item[0]][$po_garment_item[1]]['quantity'];
							//echo $prod_qty.'<br>';
							if(is_nan($fob_rate)){ $fob_rate=0; }
							$fob_val+=$prod_qty*$fob_rate;

						}

						$po_id_arr = array_unique(explode(",", $production_data_arr[$f_id][$l_id][$job_key]['po_id']));
						$po_rate ="";
						foreach ($po_id_arr as $po_val)
						{
							if($po_rate!="") $po_rate.=",";
							$po_rate.=$po_rate_data_arr[$f_id][$l_id][$po_val]['rate'];
						}
						// echo $po_rate."<br>";


						$subcon_po_id=array_unique(explode(',',$production_data_arr[$f_id][$l_id][$job_key]['order_id']));
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
							$produce_minit+=$production_po_data_arr[$f_id][$l_id][$sub_val]*$subcon_order_smv[$sub_val];
							if($subcon_order_id!="") $subcon_order_id.=",";
							$subcon_order_id.=$sub_val;
						}

						if($order_no_total!="")
						{
							$day_run_sql=sql_select("select min(production_date) as min_date from pro_garments_production_mst
							where po_break_down_id in(".$order_no_total.")  and production_type=4");
							foreach($day_run_sql as $row_run)
							{
								$sewing_day=$row_run[csf('min_date')];
							}
							/*if($sewing_day!="")
							{
								// $days_run=datediff("d",$sewing_day,$pr_date);
								$date1=date_create($sewing_day);
								$date2=date_create($pr_date);
								$diff=date_diff($date1,$date2);
								$days_run = $diff->format("%R%a days");
							}
							else  $days_run=0;*/


							$lineWiseProMinDateSql="SELECT min(production_date) as MIN_DATE,FLOOR_ID,SEWING_LINE from pro_garments_production_mst where production_type=4 and po_break_down_id in(".$order_no_total.") group by FLOOR_ID,SEWING_LINE";
							$lineWiseProMinDateSqlResult=sql_select($lineWiseProMinDateSql);
							$line_wise_days_run=array();
							foreach($lineWiseProMinDateSqlResult as $row)
							{
								$line_wise_days_run[$row[FLOOR_ID]][$row[SEWING_LINE]]=datediff("d",$row[MIN_DATE],$pr_date);
							}


						}
						//echo $pr_date;die;
						$type_line=$production_data_arr[$f_id][$l_id][$job_key]['type_line'];
						$prod_reso_allo=$production_data_arr[$f_id][$l_id][$job_key]['prod_reso_allo'];
						$sewing_line='';
						if($production_data_arr[$f_id][$l_id][$job_key]['prod_reso_allo']!="")
						{
							if($production_data_arr[$f_id][$l_id][$job_key]['prod_reso_allo']==1)
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
						// echo $sewing_line."==".$production_data_arr[$f_id][$l_id][$job_key]['prod_reso_allo']."=kakku<br>";
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
							 $production_hour[$prod_hour]=$production_data_arr[$f_id][$l_id][$job_key][$prod_hour];
							 $floor_production[$prod_hour]+=$production_data_arr[$f_id][$l_id][$job_key][$prod_hour];
							 $total_production[$prod_hour]+=$production_data_arr[$f_id][$l_id][$job_key][$prod_hour];
							 if($production_data_arr[$f_id][$l_id][$job_key][$prod_hour]>0)
							 {
							 	$prod_hours++;
							 }
						}

		 				$floor_production['prod_hour24']+=$production_data_arr[$f_id][$l_id][$job_key]['prod_hour23'];
						$total_production['prod_hour24']+=$production_data_arr[$f_id][$l_id][$job_key]['prod_hour23'];
						$production_hour['prod_hour24']=$production_data_arr[$f_id][$l_id][$job_key]['prod_hour23'];
						$line_production_hour=0;
						if(str_replace("'","",$actual_production_date)>str_replace("'","",$actual_date))
						{
							if($type_line==2) //No Profuction Line
							{
								$line_start=$production_data_arr[$f_id][$l_id][$job_key]['prod_start_time'];
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
									$total_eff_hour=$total_eff_hour+1;
									$line_hour="prod_hour".substr($bg,0,2)."";
									$line_production_hour+=$production_data_arr[$f_id][$l_id][$job_key][$line_hour];
									$line_floor_production+=$production_data_arr[$f_id][$l_id][$job_key][$line_hour];
									$line_total_production+=$production_data_arr[$f_id][$l_id][$job_key][$line_hour];
									$actual_time_hour=$start_hour_arr[$lh+1];
								}
							}

		 					if($start_hour_arr[$actual_time]>$lunch_start_hour) $total_eff_hour=$total_eff_hour-1;

							if($type_line==2)
							{
								if($total_eff_hour>$production_data_arr[$f_id][$l_id][$job_key]['working_hour'])
								{
									 $total_eff_hour=$production_data_arr[$f_id][$l_id][$job_key]['working_hour'];
								}
							}
							else
							{
								if($line_wise_style_count_arr[$f_id][$l_id]>1)
								{
									if($total_eff_hour>$prod_resource_array2[$l_id][$job_key][$pr_date]['working_hour'])
									{
										$total_eff_hour=$prod_resource_array2[$l_id][$job_key][$pr_date]['working_hour'];
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
								$line_production_hour+=$production_data_arr[$f_id][$l_id][$job_key][$prod_hour];
								$line_floor_production+=$production_data_arr[$f_id][$l_id][$job_key][$prod_hour];
								$line_total_production+=$production_data_arr[$f_id][$l_id][$job_key][$prod_hour];
							}
							if($type_line==2)
							{
								$total_eff_hour=$production_data_arr[$f_id][$l_id][$job_key]['working_hour'];
							}
							else
							{
								if($line_wise_style_count_arr[$f_id][$l_id]>1)
								{
									$total_eff_hour=$prod_resource_array2[$l_id][$job_key][$pr_date]['working_hour'];
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
							$smv_adjustmet_type=$production_data_arr[$f_id][$l_id][$job_key]['smv_adjust_type'];
							$eff_target=($production_data_arr[$f_id][$l_id][$job_key]['terget_hour']*$total_eff_hour);

							if($total_eff_hour>=$production_data_arr[$f_id][$l_id][$job_key]['working_hour'])
							{
								if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$production_data_arr[$f_id][$l_id][$job_key]['smv_adjust'];
								if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($production_data_arr[$f_id][$l_id][$job_key]['smv_adjust'])*(-1);
							}
							$efficiency_min+=$total_adjustment+($production_data_arr[$f_id][$l_id][$job_key]['man_power'])*$cla_cur_time*60;
							$extra_minute_production_arr=$efficiency_min+$extra_minute_arr[$f_id][$l_id];

							$line_efficiency=(($produce_minit)*100)/$efficiency_min;


						}
						else
						{
							if($line_wise_style_count_arr[$f_id][$l_id]>1)
							{
								$smv_adjustmet_type=$prod_resource_array2[$l_id][$job_key][$pr_date]['smv_adjust_type'];
								$eff_target=($prod_resource_array2[$l_id][$job_key][$pr_date]['terget_hour']*$total_eff_hour);

								if($total_eff_hour>=$prod_resource_array2[$l_id][$job_key][$pr_date]['working_hour'])
								{
									if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array2[$l_id][$job_key][$pr_date]['smv_adjust'];
									if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array2[$l_id][$job_key][$pr_date]['smv_adjust'])*(-1);
								}

								// $efficiency_min+=$total_adjustment+($prod_resource_array2[$l_id][$job_key][$pr_date]['man_power'])*$cla_cur_time*60;
								$efficiency_min+=($prod_resource_array2[$l_id][$job_key][$pr_date]['man_power'])*$cla_cur_time*60;
								// echo "$efficiency_min=$l_id=$job_key=$pr_date=".$prod_resource_array2[$l_id][$job_key][$pr_date]['man_power']."*".$cla_cur_time."*60<br>";
								$extra_minute_resource_arr=$efficiency_min+$extra_minute_arr[$l_id][$pr_date];

								$line_efficiency=(($produce_minit)*100)/$efficiency_min;
							}
							else
							{
								$smv_adjustmet_type=$prod_resource_array[$l_id][$pr_date]['smv_adjust_type'];
								$eff_target=($prod_resource_array[$l_id][$pr_date]['terget_hour']*$total_eff_hour);

								if($total_eff_hour>=$prod_resource_array[$l_id][$pr_date]['working_hour'])
								{
									if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array[$l_id][$pr_date]['smv_adjust'];
									if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array[$l_id][$pr_date]['smv_adjust'])*(-1);
								}

								// $efficiency_min+=$total_adjustment+($prod_resource_array[$l_id][$pr_date]['man_power'])*$cla_cur_time*60;
								$efficiency_min+=($prod_resource_array[$l_id][$pr_date]['man_power'])*$cla_cur_time*60;
								// echo "$efficiency_min=$l_id=$job_key=$pr_date=".$prod_resource_array[$l_id][$pr_date]['man_power']."*".$cla_cur_time."*60<br>";
								$extra_minute_resource_arr=$efficiency_min+$extra_minute_arr[$l_id][$pr_date];

								$line_efficiency=(($produce_minit)*100)/$efficiency_min;
							}


						}

						// adjustment extra hour when multiple style running in a single line =========================
						$txtDate = str_replace("'", "", $txt_date);
						// echo "string==$l_id".$txtDate."<br>";
						// echo $extra_hr = $prod_resource_smv_adj_array[47]['02-Mar-2021'][1]['total_smv']."<br>";
						// echo $line_wise_style_count_arr2[$f_id][$l_id]."<br>";


						if($line_wise_style_count_arr[$f_id][$l_id]>1)
						{
							$mn_power = $prod_resource_smv_adj_array[$style_data][$l_id][$txtDate][1]['number_of_emp'];
							if($line_wise_style_count_arr2[$f_id][$l_id]>1)
							{
								// $late_hr = $prod_resource_smv_adj_array[$style_data][$l_id][$txtDate][5]['total_smv'];
								$extra_hr = $prod_resource_smv_adj_array[$style_data][$l_id][$txtDate][1]['total_smv'];
								$lunch_hr = $prod_resource_smv_adj_array[$style_data][$l_id][$txtDate][2]['total_smv'];
								$sick_hr = $prod_resource_smv_adj_array[$style_data][$l_id][$txtDate][3]['total_smv'];
								$leave_hr = $prod_resource_smv_adj_array[$style_data][$l_id][$txtDate][4]['total_smv'];
								$adjust_hr = ($lunch_hr+$sick_hr+$leave_hr) -  $extra_hr;
							
								if($pp==0)
								{
									$efficiency_min -= $adjust_hr;
									$pp++;
								}
								$line_wise_style_count_arr2[$f_id][$l_id]--;
							}
							else
							{

								$extra_hr = $prod_resource_smv_adj_array[$style_data][$l_id][$txtDate][1]['total_smv'];
								$lunch_hr = $prod_resource_smv_adj_array[$style_data][$l_id][$txtDate][2]['total_smv'];
								$sick_hr = $prod_resource_smv_adj_array[$style_data][$l_id][$txtDate][3]['total_smv'];
								$leave_hr = $prod_resource_smv_adj_array[$style_data][$l_id][$txtDate][4]['total_smv'];
								$adjust_hr = ($lunch_hr+$sick_hr+$leave_hr) -  $extra_hr;

								$efficiency_min -= $adjust_hr;
								// echo $adjust_hr."kakku <br>";

							}

						}
						else // for single line
						{
							$extra_hr = $prod_resource_smv_adj_array[$style_data][$l_id][$txtDate][1]['total_smv'];
							$lunch_hr = $prod_resource_smv_adj_array[$style_data][$l_id][$txtDate][2]['total_smv'];
							$sick_hr = $prod_resource_smv_adj_array[$style_data][$l_id][$txtDate][3]['total_smv'];
							$leave_hr = $prod_resource_smv_adj_array[$style_data][$l_id][$txtDate][4]['total_smv'];
							$adjust_hr = ($lunch_hr+$sick_hr+$leave_hr) - $extra_hr;

							$efficiency_min -= $adjust_hr;

							// echo $efficiency_min."=".$l_id."=".$job_key."=".$pr_date."=".$extra_hr."- (".$lunch_hr."+".$sick_hr."+".$leave_hr.")<br>";

						}


						$po_id=rtrim($production_data_arr[$f_id][$l_id][$job_key]['po_id'],',');
						$po_id=array_unique(explode(",",$po_id));

						$style=rtrim($line_wise_style_array[$f_id][$l_id]['style']);
						$style=implode("##",array_unique(explode("##",$style)));

						$job_arr = array_unique(explode(",", rtrim($production_data_arr[$f_id][$l_id][$job_key]['job_no'])));
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

						$po_id=$production_data_arr[$f_id][$l_id][$job_key]['po_id'];//$item_ids//$subcon_order_id
						$styles=explode("##",$style);
						$style_button='';//
						$style_name ='';
						$style_change = 0;
						foreach($styles as $sid)
						{
							if( $style_button=='')
							{
								$style_button="<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$l_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
								$style_name = $job_key;
							}
							else
							{
								$style_button.=", "."<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$l_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
								// $style_name .= ",".$sid;
								$style_change++; // first style will 0, 2nd style will 1
							}
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
						$target_value_fob = $eff_target*$avg_rate;

						$joNos_arr = array_unique(explode(",", $production_data_arr[$f_id][$l_id][$job_key]['job_no']));
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

							$tot_cm=return_field_value("CM_COST","wo_pre_cost_dtls","job_no='$jobNo' and is_deleted=0 and status_active=1");
							$cm_counter++;
						}
						$avg_cm = ($tot_cm/$dzn_qnty)/$cm_counter;
						$ttl_cm = $line_production_hour*$avg_cm;
						$target_cm = $eff_target*$avg_cm;

						// echo $ttl_cm."=".$l_id."=".$job_key."=".$txtDate."=".$tot_cm."*".$dzn_qnty."=".$cm_counter.")<br>";

						if($type_line==2) //No Production Line
						{
							$man_power=$production_data_arr[$f_id][$l_id][$job_key]['man_power'];
							$operator=$production_data_arr[$f_id][$l_id][$job_key]['operator'];
							$helper=$production_data_arr[$f_id][$l_id][$job_key]['helper'];
							$terget_hour=$production_data_arr[$f_id][$l_id][$job_key]['target_hour'];
							$capacity=$production_data_arr[$f_id][$l_id][$job_key]['capacity'];
							$working_hour=$production_data_arr[$f_id][$l_id][$job_key]['working_hour'];

							$floor_working_hour+=$production_data_arr[$f_id][$l_id][$job_key]['working_hour'];
							$eff_target_floor+=$eff_target;
							$floor_today_product+=$today_product;
							$floor_avale_minute+=$efficiency_min;
							$floor_produc_min+=$produce_minit;
							$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
							$floor_capacity+=$production_data_arr[$f_id][$l_id][$job_key]['capacity'];
							$floor_helper+=$production_data_arr[$l_id][$pr_da[$style]]['helper'];
							$floor_man_power+=$production_data_arr[$f_id][$l_id][$job_key]['man_power'];
							$floor_operator+=$production_data_arr[$f_id][$l_id][$job_key]['operator'];
							$total_operator+=$production_data_arr[$f_id][$l_id][$job_key]['operator'];
							$total_man_power+=$production_data_arr[$f_id][$l_id][$job_key]['man_power'];
							$total_helper+=$production_data_arr[$f_id][$l_id][$job_key]['helper'];
							$total_capacity+=$production_data_arr[$f_id][$l_id][$job_key]['capacity'];
							$floor_tgt_h+=$production_data_arr[$f_id][$l_id][$job_key]['target_hour'];
							$total_working_hour+=$production_data_arr[$f_id][$l_id][$job_key]['working_hour'];
							$gnd_total_tgt_h+=$production_data_arr[$f_id][$l_id][$job_key]['target_hour'];
							$total_terget+=$eff_target;
							$grand_total_product+=$today_product;
							$gnd_avable_min+=$efficiency_min;
							$gnd_product_min+=$produce_minit;

							$gnd_total_fob_val+=$fob_val;
							$gnd_final_total_fob_val+=$fob_val;
						}
						else
						{
							if($line_wise_style_count_arr[$f_id][$l_id]>1) // when multiple style run in single line
							{
								$operator=$prod_resource_array2[$l_id][$job_key][$pr_date]['operator'];
								$helper=$prod_resource_array2[$l_id][$job_key][$pr_date]['helper'];
								$terget_hour=$prod_resource_array2[$l_id][$job_key][$pr_date]['terget_hour'];
								$working_hour=$prod_resource_array2[$l_id][$job_key][$pr_date]['working_hour'];
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
							// echo $l_id."=".$job_key."=".$pr_date."=".$operator ."+". $helper."<br>";

							// $man_power=$prod_resource_array[$l_id][$pr_date]['man_power'];
							$capacity=$prod_resource_array[$l_id][$pr_date]['capacity'];
							// ======================================================
							$floor_capacity+=$prod_resource_array[$l_id][$pr_date]['capacity'];


							/*if($line_wise_style_count_arr[$f_id][$l_id]>1)
							{
								$floor_operator+=$prod_resource_array2[$l_id][$job_key][$pr_date]['operator'];
								$floor_helper+=$prod_resource_array2[$l_id][$job_key][$pr_date]['helper'];
								$floor_tgt_h+=$prod_resource_array2[$l_id][$job_key][$pr_date]['terget_hour'];
								$floor_working_hour+=$prod_resource_array2[$l_id][$job_key][$pr_date]['working_hour'];
								$floor_man_power+=$prod_resource_array2[$l_id][$job_key][$pr_date]['operator']+$prod_resource_array2[$l_id][$job_key][$pr_date]['helper'];
							}
							else
							{
								$floor_operator+=$prod_resource_array[$l_id][$pr_date]['operator'];
								$floor_helper+=$prod_resource_array[$l_id][$pr_date]['helper'];
								$floor_tgt_h+=$prod_resource_array[$l_id][$pr_date]['terget_hour'];
								$floor_working_hour+=$prod_resource_array[$l_id][$pr_date]['working_hour'];
								$floor_man_power+=$prod_resource_array[$l_id][$pr_date]['man_power'];
							}*/

							// if(!in_array($l_id, $line_chk_arr))
							// {
								if($line_wise_style_count_arr[$f_id][$l_id]>1)
								{
									$floor_operator+=$prod_resource_array2[$l_id][$job_key][$pr_date]['operator'];
									$floor_helper+=$prod_resource_array2[$l_id][$job_key][$pr_date]['helper'];
									$floor_tgt_h+=$prod_resource_array2[$l_id][$job_key][$pr_date]['terget_hour'];
									$floor_working_hour+=$prod_resource_array2[$l_id][$job_key][$pr_date]['working_hour'];
									$floor_man_power+=$prod_resource_array2[$l_id][$job_key][$pr_date]['operator']+$prod_resource_array2[$l_id][$job_key][$pr_date]['helper'];
								}
								else
								{
									$floor_operator+=$prod_resource_array[$l_id][$pr_date]['operator'];
									$floor_helper+=$prod_resource_array[$l_id][$pr_date]['helper'];
									$floor_tgt_h+=$prod_resource_array[$l_id][$pr_date]['terget_hour'];
									$floor_working_hour+=$prod_resource_array[$l_id][$pr_date]['working_hour'];
									$floor_man_power+=$prod_resource_array[$l_id][$pr_date]['man_power'];
								}
							// 	$line_chk_arr[$l_id] = $l_id;
							// }


							$floor_prod_hour += $prod_hours;
							$floor_tot_prod += $line_production_hour;
							$floor_line_acv += $line_acv;
							$floor_target_gap += $target_gap;
							$floor_target_min += $target_min;
							$floor_target_effi += $target_effi;
							$floor_achive_effi += $achive_effi;
							$floor_efficiency_gap += $efficiency_gap;
							// $floor_style_change += $style_change;
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
							// $total_style_change += $style_change;
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
							// if(!in_array($l_id, $line_chk_arr2))
							// {
								if($line_wise_style_count_arr[$f_id][$l_id]>1)
								{
									$total_operator+=$prod_resource_array2[$l_id][$job_key][$pr_date]['operator'];
									$total_working_hour+=$prod_resource_array2[$l_id][$job_key][$pr_date]['working_hour'];
									$gnd_total_tgt_h+=$prod_resource_array2[$l_id][$job_key][$pr_date]['terget_hour'];
									$total_helper+=$prod_resource_array2[$l_id][$job_key][$pr_date]['helper'];
									$total_man_power += $prod_resource_array2[$l_id][$job_key][$pr_date]['operator'] + $prod_resource_array2[$l_id][$job_key][$pr_date]['helper'];
								}
								else
								{
									$total_operator+=$prod_resource_array[$l_id][$pr_date]['operator'];
									$total_working_hour+=$prod_resource_array[$l_id][$pr_date]['working_hour'];
									$gnd_total_tgt_h+=$prod_resource_array[$l_id][$pr_date]['terget_hour'];
									$total_helper+=$prod_resource_array[$l_id][$pr_date]['helper'];
									$total_man_power+=$prod_resource_array[$l_id][$pr_date]['man_power'];
								}
							// 	$line_chk_arr2[$l_id] = $l_id;
							// }

							// $total_man_power+=$prod_resource_array[$l_id][$pr_date]['man_power'];
							$total_capacity+=$prod_resource_array[$l_id][$pr_date]['capacity'];
							$total_terget+=$eff_target;
							$grand_total_product+=$today_product;
							$gnd_avable_min+=$efficiency_min;
							$gnd_product_min+=$produce_minit;
							$gnd_total_fob_val+=$fob_val;
							$gnd_final_total_fob_val+=$fob_val;

						}

						if($line_efficiency<=$txt_parcentage) $efficiency_color="#FF0000"; else $efficiency_color="#FFFFFF";
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
						$html.='<td class="break_all" width="20">'.$i.'&nbsp;</td>
								<td class="break_all" align="center" width="50" ><p>'. $sewing_line.'&nbsp; </p></td>
								<td class="break_all" width="100"><p>'.$buyer_name.'&nbsp;</p></td>
								<td class="break_all" width="100"><p>'.$style_data1.'&nbsp;</p></td>
								<td class="break_all" width="75"><p>'.$job__no.'&nbsp;</p></td>
								<td class="break_all" width="80" style="word-wrap:break-word; word-break: break-all;"><p>'.implode(",",array_unique(explode(",",$garment_itemname))).'</p></td>

								<td class="break_all" align="center" width="30"><p>'.implode("/",array_unique(explode("/",$item_smv))).'</p></td>
								<td class="break_all" align="right" width="50"><p>'.$terget_hour.'</p></td>
								<td class="break_all" align="right" width="50"><p>'.$working_hour.'</p></td>
								<td class="break_all" align="right" width="50"><p>'.$prod_hours.'</p></td>
								<td class="break_all" align="right" width="50"><p>'.$operator.'</p></td>
								<td class="break_all" align="right" width="50"><p>'.$helper.'</p></td>
								<td class="break_all" align="right" width="50"><p>'.$man_power.'</p></td>
								<td class="break_all" align="right" width="50"><p>'.$days_run.'</p></td>
								<td class="break_all" align="right" width="50"><p>'.$eff_target.'</p></td>';
								$p=1;
								for($k=$hour; $k<=$last_hour; $k++)
								{
									$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
									if($p <= 11)
									{
										$html.='<td class="break_all" align="right" width="36">'.$production_hour[$prod_hour].'</td>';
									}
									/*elseif ($production_hour[$prod_hour]>0 && $p>11)
									{
										$html.='<td class="break_all" align="right" width="70">'.$production_hour[$prod_hour].'</td>';
									}*/
									$p++;

								}

								$html.='<td class="break_all" align="right" width="50">'.$line_production_hour.'</td>
								<td class="break_all" align="right" width="50">'.number_format($line_acv,0).'%</td>
								<td class="break_all" align="right" width="50">'.number_format($target_gap,0).'</td>
								<td class="break_all" align="right" width="50">'.number_format($target_min,0).'</td>

								<td class="break_all" align="right" width="80">'.number_format($efficiency_min,0).'</td>
								<td class="break_all" width="50" align="right">
									<a href="##" onclick="openmypage('.$company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$l_id.",'tot_prod','".$smv_for_item."','".$acturl_hour_minute."','".$line_start."',".$txt_date.')">'.$produce_minit.'</a>
									</td>
								<td class="break_all" align="right" width="50">'.number_format($target_effi,0).'%</td>
								<td class="break_all" align="right" width="50">'.number_format($achive_effi,0).'%</td>
								<td class="break_all" align="right" width="50">'.number_format($efficiency_gap,0).'%</td>';
								if($lc==0)
								{
									$html.='<td rowspan="'.$rowspan[$f_id][$sl][$l_id].'" width="50" align="right">'.number_format($style_change,0).'</td>';
									$lc++;
									$total_style_change += $style_change;
									$floor_style_change += $style_change;
								}

								$html.='<td class="break_all" align="left" width="180" ><p>'.$production_data_arr[$f_id][$l_id][$job_key]['remarks'].'</p></td>
								<td class="break_all" width="50" title="Cm Pre='.($tot_cm.'/'.$dzn_qnty).'/'.$cm_counter.';" align="right">'.number_format($avg_cm,6).'</td>
								<td class="break_all" align="right" width="50">'.number_format($ttl_cm,2).'</td>
								<td class="break_all" align="right" width="50">'.number_format($target_cm,2).'</td>
								<td class="break_all" align="right" width="50">'.number_format($avg_rate,2).'</td>
								<td class="break_all" align="right" width="80">'.number_format($ttl_fob_val,2).'</td>
								<td class="break_all" align="right" width="80">'.number_format($target_value_fob,2).'</td>';

							$html.='</tr>';
							$i++;
							$check_arr[$f_id]=$f_id;
							$f_chk_arr[$f_id]=$f_id;
					//}
				}
			}

		}
	}
			$html.='<tr  bgcolor="#B6B6B6">
					<td class="break_all" width="20">&nbsp;</td>
					<td class="break_all" width="50">&nbsp;</td>
					<td class="break_all" width="100">&nbsp;</td>
					<td class="break_all" width="100">&nbsp;</td>
					<td class="break_all" width="75">&nbsp;</td>
					<td class="break_all" width="80" title="'.$f_id.'"><p><b>Floor Total('.$floorArr[$f_id].')</b><p></td>

					<td class="break_all" align="right" width="30">&nbsp;</td>
					<td class="break_all" align="right" width="50">'.$floor_tgt_h.'</td>
					<td class="break_all" align="right" width="50">'. $floor_working_hour.'</td>
					<td class="break_all" align="right" width="50">'. $floor_prod_hour.'</td>
					<td class="break_all" align="right" width="50">'. $floor_operator.'</td>
					<td class="break_all" align="right" width="50">'. $floor_helper.'</td>
					<td class="break_all" align="right" width="50">'. $floor_man_power.'</td>
					<td class="break_all" align="right" width="50">'. $floor_days_run.'</td>
					<td class="break_all" align="right" width="50">'. number_format($eff_target_floor,0).'</td>';
					$p=1;
					for($k=$hour; $k<=$last_hour; $k++)
					{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
						if($p <= 11)
						{
							$html.='<td class="break_all" align="right" width="36" style='.$bg_color_.' >'. $floor_production[$prod_hour].'</td>';
						}
						/*elseif ($floor_production[$prod_hour]>0 && $p>11)
						{
							$html.='<td class="break_all" align="right" width="70" style='.$bg_color_.' >'. $floor_production[$prod_hour].'</td>';
						}*/
						$p++;
					}
					$html.='<td class="break_all" align="right" width="50">'.number_format($floor_tot_prod,0).'</td>
					<td class="break_all" align="right" width="50">'.number_format((($floor_tot_prod/$eff_target_floor)*100),0).'%</td>
					<td class="break_all" align="right" width="50">'.number_format($floor_target_gap,0).'</td>
					<td class="break_all" align="right" width="50">'.number_format($floor_target_min,0).'</td>
					<td class="break_all" align="right" width="80">'.number_format($floor_avale_minute,2).'</td>
					<td class="break_all" align="right" width="50">'.number_format($floor_produc_min,0).'</td>
					<td class="break_all" align="right" width="50">'.number_format((($floor_target_min/$floor_avale_minute)*100),0).'%</td>
					<td class="break_all" align="right" width="50">'. number_format((($floor_produc_min/$floor_avale_minute)*100),0).'%</td>
					<td class="break_all" align="right" width="50">'. number_format((($floor_target_min/$floor_avale_minute)*100) - (($floor_produc_min/$floor_avale_minute)*100),0).'%</td>
					<td class="break_all" align="right" width="50">'. number_format($floor_style_change,0).'</td>
					<td class="break_all" align="right" width="180"></td>

					<td class="break_all" width="50" align="right">'.number_format($floor_avg_cm,2).'</td>
					<td class="break_all" width="50" align="right">'.number_format($floor_ttl_cm,2).'</td>
					<td class="break_all" width="50" align="right">'.number_format($floor_target_cm,2).'</td>
					<td class="break_all" width="50" align="right">'.number_format($floor_avg_rate,2).'</td>
					<td class="break_all" width="80" align="right">'.number_format($floor_ttl_fob_val,2).'</td>
					<td class="break_all" width="80" align="right">'.number_format($floor_target_value_fob,2).'</td>
					</tr>
					</tbody>';

					$smv_for_item="";
					$tbl_width = 1795+($last_hour - ($hour+1))*40;
					// echo $tbl_width;die();
					$colspan = 32+($last_hour - ($hour+1));
				?>

	<fieldset style="width:<? echo $tbl_width+20;?>px">
       <table width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
            <tr class="form_caption">
                <td colspan="<? echo $colspan;?>" align="center"><strong><? echo $report_title; ?> &nbsp;</strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="<? echo $colspan;?>" align="center"><strong><? echo $companyArr[$company_id]; ?></strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="<? echo $colspan;?>" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date)) ); ?></strong></td>
            </tr>
        </table>
        <br />
        <!-- <table  width="600" cellpadding="0"  cellspacing="0" align="center" style="padding-left:200px">
            <tr>
                <td bgcolor="#FFFF66" height="18" width="30" ></td>
                <td> &nbsp;Lunch Hour</td>
                <td bgcolor="red" height="18" width="30"></td>
                <td> &nbsp;Efficiency % less than Standard And Production less than Target</td>
            </tr>
        </table> -->

        <table id="table_header_1" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th class="break_all" width="20">SL</th>
                    <th class="break_all" width="50">Line No</th>
                    <th class="break_all" width="100">Buyer</th>
                    <th class="break_all" width="100">Style Ref.</th>
                    <th class="break_all" width="75">Job</th>
                    <th class="break_all" width="80">Garments Item</th>
                    <th class="break_all" width="30">SMV</th>
                    <th class="break_all" width="50">Hourly <br>Target <br>(Pcs)<br><br></th>
                    <th class="break_all" width="50">Plan<br>/W.Hour<br></th>
                    <th class="break_all" width="50">Prod.<br> Hour<br></th>
                    <th class="break_all" width="50">Operator</th>
                    <th class="break_all" width="50">Helper</th>
                    <th class="break_all" width="50"> Man <br>Power<br></th>
                    <th class="break_all" width="50">Days <br>Run<br></th>
                    <th class="break_all" width="50">Total <br>Target<br></th>
                   <?
                   // print_r($production_hour);
                   	$p = 1;
                	for($k=$hour; $k<=$last_hour; $k++)
					{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";

						if($p <= 11)
						{
							?>
		                      <th class="break_all" width="36" style="vertical-align:middle"><div class="block_div"><?  echo substr($start_hour_arr[$k],0,5)."<br>-<br>".substr($start_hour_arr[$k+1],0,5); ?></div></th>
							<?
						}
						/*elseif ($production_hour[$prod_hour]>0 && $p>11)
						{
							?>
		                      <th class="break_all" width="70" style="vertical-align:middle"><div class="block_div"><?  echo substr($start_hour_arr[$k],0,5)."-".substr($start_hour_arr[$k+1],0,5); ?></div></th>
							<?
						}*/
						$p++;
					}
                	?>
                    <th class="break_all" width="50">Total <br> Prod.<br></th>
                    <th class="break_all" width="50">Line Acv.</th>
                    <th class="break_all" width="50">Target <br>Gap<br></th>
                    <th class="break_all" width="50">Target <br>Min<br></th>
                    <th class="break_all" width="80">Available Min.</th>
                    <th class="break_all" width="50">Produce<br>/Earn Min<br></th>
                    <th class="break_all" width="50">Target <br>Eff.%<br></th>
                    <th class="break_all" width="50">Acv.<br> Eff.%<br></th>
                    <th class="break_all" width="50">Eff. Gap</th>
                    <th class="break_all" width="50">Style <br>Change<br></th>
                    <th class="break_all" width="180">Remarks</th>
                    <th class="break_all" width="50">CM/PC</th>
                    <th class="break_all" width="50">TTL CM</th>
                    <th class="break_all" width="50">Target <br>CM<br></th>
                    <th class="break_all" width="50">Unit <br>Price<br></th>
                    <th class="break_all" width="80">Ttl. Value(FOB)</th>
                    <th class="break_all" width="80">Target <br>Value(FOB)<br></th>
                </tr>
            </thead>
        </table>
        <div style="width:<?= $tbl_width+20;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
              <? echo $html;  ?>
                <tfoot>
                   <tr>
                        <th class="break_all" width="20">&nbsp;</th>
                        <th class="break_all" width="50">&nbsp;</th>
                        <th class="break_all" width="100">&nbsp;</th>
                        <th class="break_all" width="100">&nbsp;</th>
                        <th class="break_all" width="75">&nbsp;</th>
                        <th class="break_all" width="80">Grand Total</th>

                        <th class="break_all" align="right" width="30">&nbsp;</th>
                        <th class="break_all" align="right" width="50"><? echo number_format($gnd_total_tgt_h,0); ?>&nbsp;</th>
                        <th class="break_all" align="right" width="50"><? echo $total_working_hour; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="50"><? echo $total_prod_hour; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="50"><?  echo $total_operator; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="50"><?  echo $total_helper; ?></th>
                        <th class="break_all" align="right" width="50"><?  echo $total_man_power; ?></th>
                        <th class="break_all" align="right" width="50"><?  echo $total_days_run; ?></th>
                        <th class="break_all" align="right" width="50"><? echo number_format($total_terget,0); ?></th>
                        <?
                        $p = 1;
						for($k=$hour; $k<=$last_hour; $k++)
						{
							$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";

							if($p <= 11)
							{
								?>
			                      <th class="break_all" align="right" width="36"><? echo $total_production[$prod_hour]; ?></th>
								<?
							}
							/*elseif ($total_production[$prod_hour]>0 && $p>11)
							{
								?>
			                      <th class="break_all" align="right" width="70"><? echo $total_production[$prod_hour]; ?></th>
								<?
							}*/
							$p++;
						}
                        ?>
                        <th class="break_all" align="right" width="50"><? echo number_format($total_tot_prod,0); ?></th>
                        <th class="break_all" align="right" width="50"><? echo number_format((($total_tot_prod/$total_terget)*100),0); ?>%</th>
                        <th class="break_all" align="right" width="50"><? echo number_format($total_target_gap,0); ?></th>
                        <th class="break_all" align="right" width="50"><? echo number_format($total_target_min,0); ?>&nbsp;</th>
                        <th class="break_all" align="right" width="80" ><? echo number_format($gnd_avable_min,2);?>&nbsp;</th>
                        <th class="break_all" align="right" width="50"><? echo number_format($gnd_product_min,0); ?>&nbsp;</th>
                        <th class="break_all" align="right" width="50"><? echo number_format((($total_target_min/$gnd_avable_min)*100),0); ?>%</th>
                        <th class="break_all" align="right" width="50"><? echo number_format((($gnd_product_min/$gnd_avable_min)*100),0); ?>%&nbsp;</th>
                        <th class="break_all" align="right" width="50"><? echo number_format((($total_target_min/$gnd_avable_min)*100)-(($gnd_product_min/$gnd_avable_min)*100) ,0); ?>%&nbsp;</th>
                        <th class="break_all" align="right" width="50"><? echo number_format($total_style_change,0); ?>&nbsp;</th>
                        <th class="break_all" align="right" width="180">&nbsp;</th>

                        <th class="break_all" align="right" width="50"><? echo number_format($total_avg_cm,2);?>&nbsp;</th>
                        <th class="break_all" align="right" width="50"><? echo number_format($total_ttl_cm,2);?>&nbsp;</th>
                        <th class="break_all" align="right" width="50"><? echo number_format($total_target_cm,2);?>&nbsp;</th>

                        <th class="break_all" align="right" width="50"><? echo number_format($total_avg_rate,2);?>&nbsp;</th>
                        <th class="break_all" align="right" width="80"><? echo number_format($total_ttl_fob_val,2);?>&nbsp;</th>
                        <th class="break_all" align="right" width="80"><? echo number_format($total_target_value_fob,2);?>&nbsp;</th>

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
			   $sql_pop=sql_select("SELECT  c.po_number,a.po_break_down_id,
			                sum(CASE WHEN a.production_hour>'$line_date'  and a.production_hour<='$actual_time'  and a.production_type=5 THEN a.production_quantity else 0 END)  as good_qnty

							from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
							where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and a.company_id=".$company_id."  and a.floor_id=".$floor_id." and a.sewing_line=".$sewing_line."  and a.po_break_down_id in(".$po_id.") and a.production_date='".$prod_date."'  group by c.po_number,a.po_break_down_id order by  c.po_number ");
			}
			if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date))
			{
			   	$sql_pop=sql_select("SELECT  c.po_number,a.po_break_down_id,
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
                        <td align="right"><? echo implode(',',array_unique(explode("/", $pop_val['item_smv']))); ?>&nbsp;</td>
                        <td align="right"><? $total_po_qty+=$pop_val['po_qty']; echo $pop_val['po_qty']; ?>&nbsp;</td>
                        <td align="right">
						<?
						//$smv_arr = explode("/", $pop_val['item_smv']);
						$smv_arr = array_unique(explode("/", $pop_val['item_smv']));
						$producd_min = 0;
						foreach ($smv_arr as $value)
						{
							$producd_min+=$pop_val['po_qty']*$value;
						}
						$total_producd_min+=$producd_min;
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