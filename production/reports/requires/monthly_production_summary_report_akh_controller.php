<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_floor")
{
	$data=str_replace("'","", $data);
	echo create_drop_down( "cbo_floor_id", 150, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id in($data) and production_process=5 order by floor_serial_no","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/monthly_production_summary_report_akh_controller', this.value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'load_drop_down_line', 'line_td' );refresh_line();",0 );     	 	
	exit();    	 
}


if ($action=="load_drop_down_line")
{
	$explode_data = explode("_",$data);
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$explode_data[1] and variable_list=23 and is_deleted=0 and status_active=1");
	$txt_date_from = $explode_data[2];
	$txt_date_to = $explode_data[3];
	
	$cond="";
	if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		
		if($txt_date_from=="")
		{
			if( $explode_data[0]!=0 ) $cond = " and floor_id= $explode_data[0]";
			$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 $cond");
		}
		else
		{
			if( $explode_data[0]!=0 ) $cond = " and a.floor_id= $explode_data[0]";
			
			$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date between '".date('d-M-Y',strtotime($txt_date_from))."' and '".date('d-M-Y',strtotime($txt_date_to))."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
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

		echo create_drop_down( "cbo_line", 130,$line_array,"", 1, "-- Select Line --", $selected, "",0,0 );
	}
	else
	{
		if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name= $explode_data[1]";
		if( $explode_data[0]!=0 ) $cond = " and floor_name= $explode_data[0]";

		echo create_drop_down( "cbo_line", 130, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 1, "-- Select Line --", $selected, "",0,0 );
	}
	exit();
}



if($action=="line_popup")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;
		
		function check_all_data() 
		{
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
			else 
			{
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
			id= id.substr( 0, id.length - 1 );
			name= name.substr( 0, name.length - 1 ); 
			
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name ); 
		}
	</script>
	<?
	//echo $company;die;
	if($company==0) $company_name=""; else $company_name=" and a.company_id in($company)";//job_no
	
	if($buyer==0) $buyer_name=""; else $buyer_name="and b.buyer_name=$buyer";
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name in($company) and variable_list=23 and is_deleted=0 and status_active=1");
	if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		
		if($txt_date=="")
		{
			// if(  $location!=0 ) $cond = " and a.location_id= $location";
			if( $floor_id!=0 ) $cond.= " and a.floor_id= $floor_id";
			$line_data="select a.id,a.line_number from prod_resource_mst a where a.is_deleted=0 $company_name $cond";
		}
		else
		{
			// if(  $location!=0 ) $cond = " and a.location_id= $location";
			if( $floor_id!=0 ) $cond.= " and a.floor_id= $floor_id";
			
			$line_data="select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id  and b.pr_date between '".change_date_format($txt_date_from, "", "",1)."' and '".change_date_format($txt_date_to, "", "",1)."' and a.is_deleted=0 and b.is_deleted=0 and a.company_id in($company) $cond";
		}
		$lineDataArr = return_library_array($line_data,"id","line_number"); 
		$html='	
		<table class="rpt_table" rules="all" width="310" cellspacing="0" cellpadding="0" border="0">
		    <thead>
		        <tr>
		            <th width="50">SL No</th>
		            <th>Line </th>
		        </tr>
		    </thead>
		</table>
		<div id="" style="max-height:310px; width:308px; overflow-y:scroll">
		<table id="list_view" class="rpt_table" rules="all" width="288" height="" cellspacing="0" cellpadding="0" border="0">
		    <tbody>';
		        $sl=1;
				foreach($lineDataArr as $id=>$line){
					$lineDR=array();
					foreach(explode(',',$line) as $dr){
						$lineDR[$dr]=$line_library[$dr];	
					}
					
				$bgcolor=($sl%2==0)?'#FFFFFF':'#E9F3FF';
				$jsfunction="js_set_value('".$sl.'_'.$id.'_'.implode(',',$lineDR)."')";
				$html.='
				<tr id="tr_'.$sl.'" onclick="'.$jsfunction.'" style="cursor:pointer" height="20" bgcolor="'.$bgcolor.'">
		            <td width="50">'.$sl.'</td>
		            <td>'.implode(',',$lineDR).'</td>
		        </tr>';
				$sl++;
				}
		    
			$html.='</tbody>
		</table>

		<div class="check_all_container">
			<div style="width:100%">
				<div style="width:50%; float:left" align="left">
					<input id="check_all" name="check_all" onclick="check_all_data()" type="checkbox">
					Check / Uncheck All
				</div>
				<div style="width:50%; float:left" align="left">
					<input id="close" class="formbutton" name="close" onclick="parent.emailwindow.hide();" value="Close" style="width:100px" type="button">
				</div>
				</div>
			</div>
		</div>
		';
		echo $html;		
		
		//echo create_list_view("list_view", "Line ","250","300","310",0, $line_data , "js_set_value", "id,line_number", "", 1, "0", $arr, "line_number", "","setFilterGrid('list_view',-1)","0","",1);	
	}
	else
	{
		// if( $location!=0  ) $cond = " and location_name in($location)";
		if( $floor_id!=0 ) $cond.= " and floor_name in($floor_id)";
		$line_data="select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name";
		
		echo create_list_view("list_view", "Line No","250","300","310",0, $line_data , "js_set_value", "id,line_name", "", 1, "0", $arr, "line_name", "","setFilterGrid('list_view',-1)","0","",1) ;	
	}
	
		echo "<input type='hidden' id='txt_selected_id' />";
		echo "<input type='hidden' id='txt_selected' />";
	
	
	exit();
}

$companyArr = return_library_array("SELECT id,company_name from lib_company","id","company_name"); 
$buyerArr = return_library_array("SELECT id,short_name from lib_buyer","id","short_name"); 
$locationArr = return_library_array("SELECT id,location_name from lib_location","id","location_name"); 
$floorArr = return_library_array("SELECT id,floor_name from lib_prod_floor","id","floor_name"); 
//$lineArr = return_library_array("select id, line_name from lib_sewing_line","id","line_name"); 
$prod_reso_arr=return_library_array( "SELECT id, line_number from prod_resource_mst",'id','line_number');

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_id = str_replace("'", "",$cbo_company_id);
	$cbo_floor_id 	= str_replace("'", "",$cbo_floor_id);
	$hidden_line_id = str_replace("'", "",$hidden_line_id);
	$cbo_line = str_replace("'", "",$cbo_line);
	//  ================== making query condition =========================
	if(str_replace("'","",$cbo_floor_id)==0) $floor_cond=""; else $floor_cond="and a.floor_id in(".str_replace("'","",$cbo_floor_id).")";
	if(str_replace("'","",$cbo_line)==0) $line_cond=""; else $line_cond="and a.sewing_line in(".str_replace("'","",$cbo_line).")";
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond=""; else $date_cond=" and a.production_date between $txt_date_from and $txt_date_to";

	// echo $txt_date_to;die();
	$date_from = str_replace("'", "", $txt_date_from);
	$date_to = str_replace("'", "", $txt_date_to);
	function get_date_range($first, $last, $step = '+1 day', $output_format = 'd-M-Y' ) 
	{
	    $dates = array();
	    $current = strtotime($first);
	    $last = strtotime($last);

	    while( $current <= $last ) {

	        $dates[] = date($output_format, $current);
	        $current = strtotime($step, $current);
	    }

	    return $dates;
	}
	$date_range_arr = get_date_range($date_from,$date_to); 
	// echo "<pre>";print_r($date_range_arr);die();

			
	$lineDataArr = sql_select("SELECT id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
	order by sewing_line_serial"); 
	foreach($lineDataArr as $lRow)
	{
		$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
		$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
		$lastSlNo=$lRow[csf('sewing_line_serial')];
	}
	
	if($db_type==0)
	{
		$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id  and pr_date between $txt_date_from and $txt_date_to","line_start_time");	
	}
	else if($db_type==2)
	{
		$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and pr_date between $txt_date_from and $txt_date_to","line_start_time");
	}//
	
	if($min_shif_start=="")
	{
		echo "<p style='font-size:20px', align='center'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<p/>";disconnect($con);die;
		
	}

	//==============================shift time======================================
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
	$hour=substr($start_time[0],1,1); $minutes=$start_time[1]; $last_hour=23;
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

	// echo "<pre>";print_r($start_hour_arr);

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
	
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and is_deleted=0 and status_active=1");
	//echo $prod_reso_allo."eee";die;

	if($prod_reso_allo==1)
	{
		$prod_resource_array=array();
		$sql="SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.smv_adjust, b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour, c.from_date, c.to_date, c.capacity as mc_capacity from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c where a.id=c.mst_id and a.id=b.mst_id and c.id=b.mast_dtl_id and a.company_id=$company_id and pr_date between $txt_date_from and $txt_date_to";	
		// echo $sql;die();
		$dataArray = sql_select($sql);		
		
		foreach($dataArray as $val)
		{
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['man_power']=$val[csf('man_power')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['operator']=$val[csf('operator')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['helper']=$val[csf('helper')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['terget_hour']=$val[csf('target_per_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['working_hour']=$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['tpd']=$val[csf('target_per_hour')]*$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_start']=$val[csf('from_date')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_end']=$val[csf('to_date')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['capacity']=$val[csf('mc_capacity')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust']=$val[csf('smv_adjust')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];
		}
	}
	unset($dataArray);
	// print_r($prod_resource_array);die;
	//***************************************************************************************************************
	if($db_type==0)
	{
		// $country_ship_date_fld="a.country_ship_date";
		$manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 and id=$company_id","company_id");
	}
	else
	{
		// $country_ship_date_fld="to_char(a.country_ship_date,'YYYY-MM-DD')";
		$manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 and id=$company_id","company_id");
	}
	
	if($db_type==0) $prod_start_cond="prod_start_time";
	else if($db_type==2) $prod_start_cond="TO_CHAR(prod_start_time,'HH24:MI')";
	
	$variable_start_time_arr='';
	$prod_start_time=sql_select("select $prod_start_cond as prod_start_time from variable_settings_production where company_name in($company_id) and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");
	//echo "select company_name, prod_start_time from variable_settings_production where company_name=$cbo_company_id and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1";
	foreach($prod_start_time as $row)
	{
		$ex_time=explode(" ",$row[csf('prod_start_time')]);
		$variable_start_time_arr=$row[csf('prod_start_time')];
	}//die;
	unset($prod_start_time);
	$current_date_time=date('d-m-Y H:i');
	$ex_date_time=explode(" ",$current_date_time);
	$current_date=$ex_date_time[0];
	$current_time=$ex_date_time[1];
	$ex_time=explode(":",$current_time);
	
	$search_prod_date=change_date_format(str_replace("'","",$txt_date_from),'yyyy-mm-dd');
	$current_eff_min=($ex_time[0]*60)+$ex_time[1];
	
	$variable_time= explode(":",$variable_start_time_arr);
	$vari_min=($variable_time[0]*60)+$variable_time[1];
	$difa_time=explode(".",number_format(($current_eff_min-$vari_min)/60,2));//datediff("",$ctime,$variable_start_time_arr);
	$dif_time=$difa_time[0];
	$dif_hour_min=date("H:i", strtotime($dif_time));
	
	
	
	if($db_type==2)
	{
		$pr_date=str_replace("'","",$txt_date_from);
		$pr_date_old=explode("-",str_replace("'","",$txt_date_from));
		$month=strtoupper($pr_date_old[1]);
		$year=substr($pr_date_old[2],2);
		$pr_date=$pr_date_old[0]."-".$month."-".$year;
	}
	else if($db_type==0)
	{
		$pr_date=str_replace("'","",$txt_date_from);
	}
	//echo $pr_date;die; 
	/*$prod_start_hour="08:00";
	$start_time=explode(":",$prod_start_hour);
	$hour=substr($start_time[0],1,1); $minutes=$start_time[1]; $last_hour=23;
	
	$prod_arr=array(); $start_hour_arr=array();
	$start_hour=$prod_start_hour;
	$start_hour_arr[$hour]=$start_hour;
	for($j=$hour; $j<$last_hour; $j++)
	{
		$start_hour=add_time($start_hour,60);
		$start_hour_arr[$j+1]=substr($start_hour,0,5);
	}
	//echo $pc_date_time;die;
	$start_hour_arr[$j+1]='23:59';
	$actual_date=date("Y-m-d");
	$actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$txt_date_from)));
	
	
	$first_hour_time=explode(":",$min_shif_start);
	$hour_line=substr($first_hour_time[0],1,1); $minutes_one=$start_time[1];
	$line_start_hour_arr[$hour_line]=$min_shif_start;
	
	for($l=$hour_line;$l<$last_hour;$l++)
	{
		$min_shif_start=add_time($min_shif_start,60);
		$line_start_hour_arr[$l+1]=substr($min_shif_start,0,5);
	}
	
	$line_start_hour_arr[$j+1]='23:59';	*/

	// echo "string";
	
	$i=1; $grand_total_good=0; $grand_alter_good=0; $grand_total_reject=0;
	$html="";
	$floor_html="";
    $check_arr=array();
	if($db_type==0)
	{
		$sql_query="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name as buyer_name, b.style_ref_no, a.po_break_down_id, a.item_number_id, c.po_number as po_number,c.grouping,c.file_no,d.color_type_id,
			sum(d.production_qnty) as good_qnty,"; 
		$first=1;
		for($h=$hour;$h<$last_hour;$h++)
		{
			$bg=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="prod_hour".substr($bg,0,2);
			if($first==1)
			{
				$sql_query.="sum(CASE WHEN   a.production_hour<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,";
			}
			else
			{
				$sql_query.="sum(CASE WHEN a.production_hour>'$bg' and  a.production_hour<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,";
			}
			$first=$first+1;
		}
		$sql_query.="sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23 
			from pro_garments_production_mst a,pro_garments_production_dtls d, wo_po_details_master b, wo_po_break_down c
			where  a.production_type=5 and d.production_type=5 and a.id=d.mst_id and   a.po_break_down_id=c.id and c.job_no_mst=b.job_no and  a.status_active=1 and a.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and c.is_deleted=0 and c.status_active in(1,2,3) and a.production_source=1 and b.company_name=$company_id $floor_cond $line_cond $date_cond
			group by a.company_id, a.location,d.color_type_id, a.floor_id, a.po_break_down_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name, b.style_ref_no, a.item_number_id, c.po_number,c.grouping,c.file_no order by a.production_date";
	}
	else if($db_type==2)
	{		
		$sql_query="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name as buyer_name, b.style_ref_no,b.set_break_down, b.set_smv, a.po_break_down_id, a.item_number_id, c.po_number as po_number,c.grouping,c.file_no,d.color_type_id,
			sum(d.production_qnty) as good_qnty,"; 
		$first=1;
		for($h=$hour;$h<$last_hour;$h++)
		{
			$bg=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="prod_hour".substr($bg,0,2);
			if($first==1)
			{
				$sql_query.="sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN production_qnty else 0 END) AS $prod_hour,";
			}
			else
			{
				$sql_query.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 
				THEN production_qnty else 0 END) AS $prod_hour,";
			}
			$first++;
		}
		$sql_query.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN production_qnty else 0 END) AS prod_hour23 			
			FROM pro_garments_production_mst a,pro_garments_production_dtls d, wo_po_details_master b, wo_po_break_down c
			where a.production_type=5 and d.production_type=5 and a.id=d.mst_id and   a.po_break_down_id=c.id and c.job_id=b.id and  a.status_active=1 and a.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and c.is_deleted=0 and c.status_active in(1,2,3) and a.production_source=1 and b.company_name=$company_id $floor_cond $line_cond $date_cond
			group by a.company_id, a.location, a.floor_id, a.po_break_down_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name,d.color_type_id, b.style_ref_no, b.set_break_down,b.set_smv, a.item_number_id, c.po_number,c.grouping,c.file_no order by a.production_date";
	}
	 // echo $sql_query; die();        
		   

	$sql_res=sql_select($sql_query);
	$production_data_arr=array();
	$production_data_arr2=array();
	$all_po_id_arr=array();
	$all_style_arr=array();
	$style_wise_po_arr = array();
	$production_po_data_arr=array();
	foreach($sql_res as $val)
	{		
		if($val[csf('prod_reso_allo')]==1)
		{
			$sewing_line_id=$prod_reso_arr[$val[csf('sewing_line')]];
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
		$style_wise_po_arr[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
		$production_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]]['prod_reso_allo']=$val[csf('prod_reso_allo')]; 
		$production_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]]['po_break_down_id'].=$val[csf('po_break_down_id')].","; 
		
		
		if($production_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]]['set_break_down']!="")
		{
			$production_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]]['set_break_down'].=",".$val[csf('set_break_down')]; 
		}
		else
		{
			$production_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]]['set_break_down']=$val[csf('set_break_down')]; 
		}

		if($production_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]]['buyer_name']!="")
		{
			$production_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]]['buyer_name'].=",".$val[csf('buyer_name')]; 
		}
		else
		{
			$production_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]]['buyer_name']=$val[csf('buyer_name')]; 
		}

		if($production_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]]['floor_id']!="")
		{
			$production_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]]['floor_id'].=",".$val[csf('floor_id')]; 
		}
		else
		{
			$production_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]]['floor_id']=$val[csf('floor_id')]; 
		}
		
		
		if($production_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]]['set_smv']!="")
		{
			$production_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]]['set_smv'].=",".number_format($val[csf('set_smv')],2); 
		}
		else
		{
			$production_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]]['set_smv']=number_format($val[csf('set_smv')],2); 
		}
		
		if($production_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]]['po_number']!="")
		{
			$production_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]]['po_number'].=",".$val[csf('po_number')]; 
			$production_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]]['style_ref'].=",".$val[csf('style_ref_no')]; 
			$production_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]]['grouping'].=",".$val[csf('grouping')]; 
			$production_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]]['file_no'].=",".$val[csf('file_no')]; 
		}
		else
		{
			$production_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]]['po_number']=$val[csf('po_number')];
			$production_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]]['style_ref']=$val[csf('style_ref_no')]; 
			$production_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]]['grouping']=$val[csf('grouping')]; 
			$production_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]]['file_no']=$val[csf('file_no')]; 
		}
		
		if($production_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]]['item_number_id']!="")
		{
			$production_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('style_ref_no')]; 
		}
		else
		{
			$production_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('style_ref_no')]; 
		}
		
		$production_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]]['quantity']+=$val[csf('good_qnty')];
		// $production_po_data_arr[$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf('good_qnty')];

		for($h=$hour;$h<$last_hour;$h++)
		{
			$prod_hour="prod_hour".substr($start_hour_arr[$h],0,2)."";
			// $production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$prod_hour]+=$val[csf($prod_hour)]; 
			
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
			{
				if( $h>=$line_start_hour && $h<=$actual_time)
				{
					$production_po_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf($prod_hour)]; 
				} 	
			}
			
			if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date)) 
			{	
				$production_po_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf($prod_hour)];
			}
		}
		
		if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
		{	
			if( $h>=$line_start_hour && $h<=$actual_time)
			{
				$production_po_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf('prod_hour23')];     
			} 	
		}
		else
		{
			$production_po_data_arr[$val[csf('production_date')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf('prod_hour23')];     
		}


		$production_data_arr2[$val[csf('po_break_down_id')]][$val[csf('sewing_line')]][$val[csf('item_number_id')]]+=$val[csf('good_qnty')];

		$production_data_arr3[$val[csf('po_break_down_id')]][$val[csf('sewing_line')]]=$val[csf('set_break_down')];

		$all_style_arr[$val[csf('style_ref_no')]]=$val[csf('style_ref_no')];
		$all_po_id_arr[$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];
	}
	
	
	
	// echo "<pre>"; print_r($production_data_arr);die();
	$all_po_ids=implode(",", array_unique($all_po_id_arr));

	
	$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($manufacturing_company) and variable_list=25 and status_active=1 and is_deleted=0");
	if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
	// echo $smv_source;

	
	if($smv_source==3) // from gsd enrty
	{		
		$style_cond = where_con_using_array($all_style_arr,1,"a.style_ref");
		$sql_item="SELECT a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID  from 
		PPL_GSD_ENTRY_MST a where a.APPLICABLE_PERIOD <= $txt_date_to and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and 
		a.APPROVED=1 $style_cond group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID ORDER
		 BY a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC";
		$gsdSqlResult=sql_select($sql_item);
		// echo $sql_item;die;
		// $style_wise_unique_po_arr = array_unique(explode(',',$style_wise_po_arr[$rows['STYLE_REF']]));
		// echo "<pre>";print_r($rows['STYLE_REF']); echo "ggggg";die;
		foreach($gsdSqlResult as $rows)
		{
			foreach( $style_wise_po_arr[$rows['STYLE_REF']] as $po_id)
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
		$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no  and b.id in($all_po_ids) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3)";
		// echo $sql_item;die;
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
	}
	// echo "<pre>";print_r($item_smv_array);die;
	$sql_item="SELECT a.id as mst_id,a.total_smv,a.style_ref, a.gmts_item_id,c.id,a.color_type from ppl_gsd_entry_mst a,wo_po_details_master b, wo_po_break_down c where a.style_ref=b.style_ref_no and a.bulletin_type=4 and  TRUNC(a.insert_date)<=TO_DATE($txt_date_from) and a.is_deleted=0 and a.status_active=1 and b.job_no=c.job_no_mst and c.id in($all_po_ids) and  b.status_active=1 and b.is_deleted=0 
		group by a.total_smv,a.style_ref, a.gmts_item_id,c.id,a.color_type,a.id order by a.id ";
	// echo $sql_item;die;
	$resultItem=sql_select($sql_item);
	foreach($resultItem as $itemData)
	{
		$item_smv_array_color_type[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]][$itemData[csf('color_type')]]=$itemData[csf('total_smv')];
	}

	foreach($sql_res as $val2)
	{
		//echo $val2[csf('po_break_down_id')]."**".$val2[csf('item_number_id')]."**".$val2[csf('color_type_id')]."<br/>";
		if($item_smv_array_color_type[$val2[csf('po_break_down_id')]][$val2[csf('item_number_id')]][$val2[csf('color_type_id')]]!="")
		$item_smv_array[$val2[csf('po_break_down_id')]][$val2[csf('item_number_id')]]=$item_smv_array_color_type[$val2[csf('po_break_down_id')]][$val2[csf('item_number_id')]][$val2[csf('color_type_id')]];
	}

	// echo "<pre>$smv_source";print_r($item_smv_array);die;
	unset($sql_res);	
	/*if($smv_source==3)
	{		
		$style_nos=implode("','",$all_style_arr);
		$gsdSql="SELECT a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID  from PPL_GSD_ENTRY_MST a where a.APPLICABLE_PERIOD <= $txt_date_from and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and a.BULLETIN_TYPE in(3,4) and A.STYLE_REF in('".$style_nos."') and a.APPROVED=1 
		group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID
		 ORDER BY  a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC";
		// echo $gsdSql; 
		$gsdSqlResult = sql_select($gsdSql); 
		//$gsdDataArr=array();
		foreach($gsdSqlResult as $rows)
		{
			foreach($style_wise_po_arr[$rows[STYLE_REF]] as $po_id)
			{
				if($item_smv_array[$po_id][$rows[GMTS_ITEM_ID]]==''){
					$item_smv_array[$po_id][$rows[GMTS_ITEM_ID]]=$rows[TOTAL_SMV];
				}
			}
		}
		
	}
	else
	{
		$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3) and b.id in($all_po_ids)"; //echo $sql_item;die;
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
	}*/	
	
	
	ob_start();
	?>
    <fieldset style="width:1900px">
        <table width="1880" cellpadding="0" cellspacing="0"> 
            <tr class="form_caption">
            	<td colspan="21" align="center"><strong>Monthly Production Summary Report</strong></td> 
            </tr>
            <tr class="form_caption">
            	<td colspan="21" align="center"><strong><? echo $companyArr[$company_id]; ?></strong></td> 
            </tr>
            <tr class="form_caption">
            	<td colspan="21" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date_from)) )." To ".change_date_format( str_replace("'","",trim($txt_date_to)) ); ?></strong></td> 
            </tr>
        </table>
        <table id="table_header_1" class="rpt_table" width="1880" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr height="30">
                    <th width="40">SL</th>
                    <th width="80">Production Date </th>
                    <th width="80">Floor Name</th>
                    <th width="80">Line No</th>
                    <th width="80">Buyer</th>
                    <th width="140">Style Ref.</th>
                    <th width="140">Order No</th>
                    <th width="140">File No</th>
                    <th width="140">Ref. No</th>
                    <th width="120">Garments Item</th>
                    <th width="60">SMV</th>
                    <th width="70">Operator</th>
                    <th width="50">Helper</th>
                    <th width="60"> Man Power</th>
                    <th width="70">Hourly Terget(pcs)</th>
                    <th width="80">Total Target</th>
                    <th width="80">Total Prod.</th>
                    <th width="80">Variance pcs </th>
                    <th width="100">Available Minutes</th>
                    <th width="100">Produce Minutes</th>
                    <th  width="90">Line Effi %</th>
                </tr>
            </thead>
        </table>
        <div style="width:1900px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="1880" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<tbody>
					<?
					$i=1;
					$avg_smv = 0;
					$avg_operator = 0;
					$avg_helper = 0;
					$avg_manpower = 0;
					$avg_hr_target = 0;
					$avg_tot_target = 0;
					$avg_tot_prod = 0;
					$avg_varience = 0;
					$avg_avl_min = 0;
					$avg_prod_min = 0;
					$avg_line_effi = 0;
					$tot_produce_min = 0;
					$tot_efficiency_min = 0;
					foreach ($production_data_arr as $date_key => $date_data) 
					{
						foreach ($date_data as $line_id => $val) 
						{
							// echo $val['quantity']."<br>";
							$floor_ids = array_unique(explode(",", $val['floor_id']));
							$floor_name = "";
							foreach ($floor_ids as $row) 
							{
								$floor_name .= ($floor_name=="") ? $floorArr[$row] : ", ".$floorArr[$row];
							}
							// ===========================
							$buyer_ids = array_unique(explode(",", $val['buyer_name']));
							// print_r($buyer_ids);
							$buyer_name = "";
							foreach ($buyer_ids as $row) 
							{
								$buyer_name .= ($buyer_name=="") ? $buyerArr[$row] : ", ".$buyerArr[$row];
							}
							// ===========================
							$style_ref_no = array_unique(explode(",", $val['style_ref']));
							$style_ref = "";
							foreach ($style_ref_no as $row) 
							{
								$style_ref .= ($style_ref=="") ? $row : ", ".$row;
							}
							// ===========================
							$po_numbers = array_unique(explode(",", $val['po_number']));
							$po_number = "";
							foreach ($po_numbers as $row) 
							{
								$po_number .= ($po_number=="") ? $row : ", ".$row;
							}
							// ===========================
							$po_numbers = array_unique(explode(",", $val['po_number']));
							$po_number = "";
							foreach ($po_numbers as $row) 
							{
								$po_number .= ($po_number=="") ? $row : ", ".$row;
							}
							// ===========================
							$groupings = array_unique(explode(",", $val['grouping']));
							$grouping = "";
							foreach ($groupings as $row) 
							{
								$grouping .= ($grouping=="") ? $row : ", ".$row;
							}
							// ===========================
							$file_nos = array_unique(explode(",", $val['file_no']));
							$file_no = "";
							foreach ($file_nos as $row) 
							{
								$file_no .= ($file_no=="") ? $row : ", ".$row;
							}
							// ===========================
							// echo $val['item_number_id']."<br>";
							$item_number_ids = array_unique(explode("****", $val['item_number_id']));
							// echo "<pre>";print_r($item_number_ids);
							$item_name = "";
							$temp_data_arr = array();
							$temp_data_arr2 = array();
							$set_smv_all = "";
							$item_smv="";
							$produce_minit = 0;
							foreach ($item_number_ids as $row) 
							{
								$data_ex = explode("**", $row);	
								if($temp_data_arr[$line_id][$data_ex[2]][$data_ex[1]]=='')
								{
									$item_name .= ($item_name=="") ? $garments_item[$data_ex[1]] : ",".$garments_item[$data_ex[1]];
									$temp_data_arr[$line_id][$data_ex[2]][$data_ex[1]]="Kakku";
								}

								if($temp_data_arr2[$line_id][$data_ex[0]][$data_ex[1]]=='')
								{
									if($set_smv_all!='') $set_smv_all.=',';
									$single_smv = $item_smv_array[$data_ex[0]][$data_ex[1]];
									$set_smv_all .= number_format($single_smv,2) ;

									$temp_data_arr2[$line_id][$data_ex[0]][$data_ex[1]]="Kakku";
								}

								// ===================  get produce min ===============
								// if($temp_data_arr[$line_id][$data_ex[1]]=='')
								// {					
									$produce_minit+=$production_po_data_arr[$date_key][$line_id][$data_ex[0]]*$item_smv_array[$data_ex[0]][$data_ex[1]];
									// echo $production_po_data_arr[$date_key][$line_id][$data_ex[0]]."*".$item_smv_array[$data_ex[0]][$data_ex[1]]."<br>";
									// $temp_data_arr[$line_id][$data_ex[1]]=1;
								// }
								// ================== getting smv ======================
								// if($set_smv_all!='') $set_smv_all.='/';
								// $set_smv_all.=$item_smv_array[$data_ex[0]][$data_ex[1]];

								/*$po_break_down_id=implode(',',array_unique(explode(',',$val['po_break_down_id'])));
				 				if($production_data_arr2[$data_ex[0]][$line_id][$data_ex[1]] !="")
								{
									$break_down_smv = $production_data_arr3[$data_ex[0]][$line_id];
									$break_down_smv_arr = explode("__", $break_down_smv);									
									foreach ($break_down_smv_arr as $smv) 
									{
										$set_smv = explode("_", $smv);
										//echo $set_smv[2]."<br>";
										if($data_ex[1]==$set_smv[0])
										{
											if(!isset($chk_smv_array[$data_ex[2]][$data_ex[1]]))
											{
												$set_smv[2]=($item_smv_array[$data_ex[0]][$data_ex[1]])?$item_smv_array[$data_ex[0]][$data_ex[1]]:$set_smv[2];
												
												$itm_smv_arr[$data_ex[0]][$line_id]=$set_smv[2];
											
												$chk_smv_array[$data_ex[2]][$data_ex[1]] = $set_smv[2];
											}
											
										}
										
									}

								}*/	
							
								$po_break_down_id = explode(",", $po_break_down_id);
								
								foreach (array_filter($po_break_down_id) as $po) 
								{
									if(!empty($itm_smv_arr[$po][$line_id])){
										$set_smv_all .= ($set_smv_all=="") ? $itm_smv_arr[$po][$line_id] : ", ".$itm_smv_arr[$po][$line_id];
									}
								}
							}

							$man_power 	= $prod_resource_array[$line_id][$date_key]['man_power'];
							$operator 	= $prod_resource_array[$line_id][$date_key]['operator'];
							$helper 	= $prod_resource_array[$line_id][$date_key]['helper'];
							$terget_hour= $prod_resource_array[$line_id][$date_key]['terget_hour'];
							$working_hour= $prod_resource_array[$line_id][$date_key]['working_hour'];
							$smv_adjust = $prod_resource_array[$line_id][$date_key]['smv_adjust'];
							$tot_target =  $terget_hour*$working_hour;

							$current_wo_time=0;
							if($current_date==$search_prod_date)
							{
								$prod_wo_hour=$prod_resource_array[$line_id][$date_key]['working_hour'];
								
								if ($dif_time<$prod_wo_hour)
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
								$current_wo_time=$prod_resource_array[$line_id][$date_key]['working_hour'];
								$cla_cur_time=$prod_resource_array[$line_id][$date_key]['working_hour'];
							}

							$smv_adjustmet_type=$prod_resource_array[$line_id][$date_key]['smv_adjust_type'];
							$total_adjustment=0;
							if(str_replace("'","",$smv_adjustmet_type)==1)
							{ 
								$total_adjustment=$prod_resource_array[$line_id][$date_key]['smv_adjust'];
							}
							else if(str_replace("'","",$smv_adjustmet_type)==2)
							{
								$total_adjustment=($prod_resource_array[$line_id][$date_key]['smv_adjust'])*(-1);
							}

							$efficiency_min=$total_adjustment+($prod_resource_array[$line_id][$date_key]['man_power'])*$cla_cur_time*60;

							$line_efficiency=(($produce_minit)*100)/$efficiency_min;

							$tot_produce_min += $produce_minit;
							$tot_efficiency_min += $efficiency_min;

							$sewing_line='';
							if($val['prod_reso_allo']==1)
							{
								$line_number=explode(",",$prod_reso_arr[$line_id]);
								foreach($line_number as $value)
								{
									if($sewing_line=='') $sewing_line=$lineArr[$value]; else $sewing_line.=",".$lineArr[$value];
								}
							}
							else
							{ 
								$sewing_line=$lineArr[$line_id];
							}

							$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
							?>					
			                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
			                    <td width="40"><?=$i;?></td>
			                    <td width="80" align="center"> <? echo change_date_format($date_key);?></td>
			                    <td width="80"><p><? echo chop($floor_name,',');?></p></td>
			                    <td width="80"><p title="<? echo $line_id;?>"><? echo $sewing_line;?></p></td>
			                    <td width="80"><p><? echo $buyer_name;?></p></td>
			                    <td width="140"><p><? echo $style_ref;?></p></td>
			                    <td width="140"><p><? echo $po_number;?></p></td>
			                    <td width="140"><p><? echo $file_no;?></p></td>
			                    <td width="140"><p><? echo $grouping;?></p></td>
			                    <td width="120"><p><? echo $item_name;?></p></td>
			                    <td align="right" width="60"><p><? echo $set_smv_all;?></p></td>
			                    <td align="right" width="70"><? echo number_format($operator,0);?></td>
			                    <td align="right" width="50"><? echo number_format($helper,0);?></td>
			                    <td align="right" width="60"><? echo number_format($man_power,0);?></td>
			                    <td align="right" width="70"><? echo number_format($terget_hour,0);?></td>
			                    <td align="right" width="80"><? echo number_format($tot_target,0);?></td>
			                    <td align="right" width="80"><? echo number_format($val['quantity'],0);?></td>
			                    <td align="right" width="80"><? echo number_format(($val['quantity'] - $tot_target),0);?></td>
			                    <td align="right" width="100"><? echo number_format($efficiency_min,2);?></td>
			                    <td align="right" width="100"><? echo number_format($produce_minit,2);?></td>
			                    <td align="right" width="90"><? echo number_format($line_efficiency,2);?>%</td>
			                </tr>
			                <?
			                $i++;			                
							$avg_smv		+= $set_smv_all;
							$avg_operator 	+= $operator;
							$avg_helper 	+= $helper;
							$avg_manpower 	+= $man_power;
							$avg_hr_target 	+= $terget_hour;
							$avg_tot_target += $tot_target;
							$avg_tot_prod 	+= $val['quantity'];
							$avg_varience 	+= $val['quantity'] - $tot_target;
							$avg_avl_min 	+= $efficiency_min;
							$avg_prod_min 	+= $produce_minit;
							$avg_line_effi 	+= $line_efficiency;
			            }
			        }
			        //$avg_line_effi=(($tot_produce_min)*100)/$tot_efficiency_min;
	                ?>
				</tbody>
            </table>
        </div>
        <table class="rpt_table" width="1880" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<tfoot>					
	                <tr>
	                    <th width="40"></th>
	                    <th width="80"> </th>
	                    <th width="80"></th>
	                    <th width="80"></th>
	                    <th width="80"></th>
	                    <th width="140"></th>
	                    <th width="140"></th>
	                    <th width="140"></th>
	                    <th width="140"></th>
	                    <th width="120">Grand Total</th>
	                    <th width="60"><?php $avg=$i-1; echo number_format($avg_smv/$avg,2);?></th>
	                    <th width="70"><? echo number_format($avg_operator/$avg,0);?></th>
	                    <th width="50"><? echo number_format($avg_helper/$avg,0);?></th>
	                    <th width="60"><? echo number_format($avg_manpower/$avg,0);?></th>
	                    <th width="70"><? echo number_format($avg_hr_target/$avg,0);?></th>
	                    <th width="80"><? echo number_format($avg_tot_target,0);?></th>
	                    <th width="80"><? echo number_format($avg_tot_prod,0);?></th>
	                    <th width="80"><? echo number_format($avg_varience,2);?></th>
	                    <th width="100"><? echo number_format($avg_avl_min,2);?></th>
	                    <th width="100"><? echo number_format($avg_prod_min,2);?></th>
	                    <th  width="90"><? echo number_format($avg_line_effi/$avg,2);?>%</th>
	                </tr>
				</tfoot>
            </table>
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
	exit();
		
}

 
 
?>