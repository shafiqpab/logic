 <?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="load_drop_down_buyer")
{
	$data=str_replace("'","", $data);
	echo create_drop_down( "cbo_buyer_name", 130, "SELECT buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) $buyer_cond and buy.id in (SELECT buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","");  
	exit();
}

if ($action=="load_drop_down_location")
{
	$data=str_replace("'","", $data);
	echo create_drop_down( "cbo_location_id", 140, "SELECT id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id in($data) order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/hourly_poly_monitoring_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 ); 
	exit();    	 
}

if ($action=="load_drop_down_floor")
{
	$data=str_replace("'","", $data);
	echo create_drop_down( "cbo_floor_id", 120, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id in($data) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "refresh_line();",0 );     	 	
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
			if(  $location!=0 ) $cond = " and a.location_id= $location";
			if( $floor_id!=0 ) $cond.= " and a.floor_id= $floor_id";
			$line_data="select a.id,a.line_number from prod_resource_mst a where a.is_deleted=0 $company_name $cond";
		}
		else
		{
			if(  $location!=0 ) $cond = " and a.location_id= $location";
			if( $floor_id!=0 ) $cond.= " and a.floor_id= $floor_id";
			
			$line_data="select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id  and b.pr_date='".change_date_format($txt_date, "", "",1)."' and a.is_deleted=0 and b.is_deleted=0 and a.company_id in($company) $cond";
			//echo $line_data;
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
		if( $location!=0  ) $cond = " and location_name in($location)";
		if( $floor_id!=0 ) $cond.= " and floor_name in($floor_id)";
		$line_data="select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name";
		
		echo create_list_view("list_view", "Line No","250","300","310",0, $line_data , "js_set_value", "id,line_name", "", 1, "0", $arr, "line_name", "","setFilterGrid('list_view',-1)","0","",1) ;	
	}
	
		echo "<input type='hidden' id='txt_selected_id' />";
		echo "<input type='hidden' id='txt_selected' />";
	
	
	exit();
}

if($action=="report_generate")
{ 
	?>
	<style type="text/css">
        .block_div { 
            width:auto;
            height:auto;
            text-wrap:normal;
            vertical-align:bottom;
            display: block;
            position: !important; 
            -webkit-transform: rotate(-90deg);
            -moz-transform: rotate(-90deg);
        }
    </style> 
	<?
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_id=str_replace("'", "",$cbo_company_id);
	
	$companyArr = return_library_array("SELECT id,company_name from lib_company","id","company_name"); 
	$buyerArr = return_library_array("SELECT id,short_name from lib_buyer","id","short_name"); 
	$locationArr = return_library_array("SELECT id,location_name from lib_location","id","location_name"); 
	$floorArr = return_library_array("SELECT id,floor_name from lib_prod_floor","id","floor_name"); 
	//$lineArr = return_library_array("select id, line_name from lib_sewing_line","id","line_name"); 
	$prod_reso_arr=return_library_array( "SELECT id, line_number from prod_resource_mst",'id','line_number');
	
	if($type==1){ $report_title .=" (Poly Output)";} elseif ($type==2){ $report_title .=" (Sewing Output)";} else { $report_title .=" (Hangtag Output)";}
	if(str_replace("'","",$cbo_company_id)==0) $company_name_subcon=""; else $company_name_subcon="and a.company_id in(".str_replace("'","",$cbo_company_id).")";
	if(str_replace("'","",$cbo_location_id)==0) $location_subcon=""; else $location_subcon="and a.location_id in(".str_replace("'","",$cbo_location_id).")";
	if(str_replace("'","",$cbo_floor_id)==0) $floor=""; else $floor="and a.floor_id in(".str_replace("'","",$cbo_floor_id).")";
	if(str_replace("'","",$hidden_line_id)==0) $line=""; else $line="and a.lide_id in(".str_replace("'","",$hidden_line_id).")";
	if(str_replace("'","",$cbo_buyer_name)==0) $buyer=""; else $buyer="and a.lide_id in(".str_replace("'","",$cbo_buyer_name).")";
	if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date=$txt_date";
 	
	
	if($type==1) // poly button
	{
		$lineDataArr = sql_select("SELECT id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1 order by sewing_line_serial"); 
		foreach($lineDataArr as $lRow)
		{
			$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
			$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
			$lastSlNo=$lRow[csf('sewing_line_serial')];
		}
		
		$comapny_id=str_replace("'","",$cbo_company_id);
		
		$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name in($comapny_id) and variable_list=23 and is_deleted=0 and status_active=1");
		//echo $prod_reso_allo."eee";die;
		
		if($db_type==0)
		{
			$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id  and pr_date=$txt_date","line_start_time");	
		}//and  a.company_id=$comapny_id and shift_id=1
		else if($db_type==2)
		{
			$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and pr_date=$txt_date","line_start_time");
		}//
		
		if($min_shif_start=="")
		{
			echo "<p style='font-size:20px', align='center'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<p/>";die;
			
		}
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
		}
		
		if(str_replace("'","",$cbo_company_id)==0) $company_name=""; else $company_name="and a.serving_company in(".str_replace("'","",$cbo_company_id).")";
		if(str_replace("'","",$cbo_location_id)==0) $location=""; else $location="and a.location=".str_replace("'","",$cbo_location_id)."";
		if(str_replace("'","",$cbo_floor_id)==0) $floor=""; else $floor="and a.floor_id=".str_replace("'","",$cbo_floor_id)."";
		if(str_replace("'","",$hidden_line_id)==0) $line=""; else $line="and a.sewing_line in(".str_replace("'","",$hidden_line_id).")";
		if(str_replace("'","",$cbo_buyer_name)==0) $buyer=""; else $buyer="and b.buyer_name in(".str_replace("'","",$cbo_buyer_name).")";
		if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date=$txt_date";
		
		if($db_type==0) $prod_start_cond="prod_start_time";
		else if($db_type==2) $prod_start_cond="TO_CHAR(prod_start_time,'HH24:MI')";
		
		$variable_start_time_arr='';
		$prod_start_time=sql_select("select $prod_start_cond as prod_start_time from variable_settings_production where company_name in($cbo_company_id) and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");
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
		
		$search_prod_date=change_date_format(str_replace("'","",$txt_date),'yyyy-mm-dd');
		$current_eff_min=($ex_time[0]*60)+$ex_time[1];
		
		$variable_time= explode(":",$variable_start_time_arr);
		$vari_min=($variable_time[0]*60)+$variable_time[1];
		$difa_time=explode(".",number_format(($current_eff_min-$vari_min)/60,2));//datediff("",$ctime,$variable_start_time_arr);
		$dif_time=$difa_time[0];
		$dif_hour_min=date("H:i", strtotime($dif_time));
		
		if($prod_reso_allo==1)
		{
			$prod_resource_array=array();
			$dataArray=sql_select("select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.smv_adjust, b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour, c.from_date, c.to_date, c.capacity as mc_capacity from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c where a.id=c.mst_id and a.id=b.mst_id and c.id=b.mast_dtl_id and a.company_id in($comapny_id) and pr_date=$txt_date ");// and a.id=1 and c.from_date=$txt_date
			
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
		//print_r($prod_resource_array); die;die;
		//print_r($prod_resource_array);die;
		//*****************************************************************************************************
		if($db_type==0)
		{
			// $country_ship_date_fld="a.country_ship_date";
			$manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 and comp.id in($cbo_company_id)","company_id");
		}
		else
		{
			// $country_ship_date_fld="to_char(a.country_ship_date,'YYYY-MM-DD')";
			$manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 and comp.id in($cbo_company_id)","company_id");
		}
		
		$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($manufacturing_company) and variable_list=25 and status_active=1 and is_deleted=0");
		
		
		
		
		//echo $variable_start_time;
		//print_r($prod_resource_array);
		if($db_type==2)
		{
			$pr_date=str_replace("'","",$txt_date);
			$pr_date_old=explode("-",str_replace("'","",$txt_date));
			$month=strtoupper($pr_date_old[1]);
			$year=substr($pr_date_old[2],2);
			$pr_date=$pr_date_old[0]."-".$month."-".$year;
		}
		else if($db_type==0)
		{
			$pr_date=str_replace("'","",$txt_date);
		}
		//echo $pr_date;die; 
		$prod_start_hour="08:00";
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
		
		
		$first_hour_time=explode(":",$min_shif_start);
		$hour_line=substr($first_hour_time[0],1,1); $minutes_one=$start_time[1];
		$line_start_hour_arr[$hour_line]=$min_shif_start;
		
		for($l=$hour_line;$l<$last_hour;$l++)
		{
			$min_shif_start=add_time($min_shif_start,60);
			$line_start_hour_arr[$l+1]=substr($min_shif_start,0,5);
		}
		
		$line_start_hour_arr[$j+1]='23:59';
		
		
		
		/*$start_hour_arr=array();
		$start_hour='00:00';
		for($j=0;$j<=23;$j++)
		{
			$start_hour=add_time($start_hour,60);
			$start_hour_arr[$j+1]=substr($start_hour,0,5);
		}*/
		//echo $pc_date_time;die;
		//$start_hour_arr[$j+1]='23:59';
		//echo '<pre>';
		//print_r($start_hour_arr); die;
		$i=1; $grand_total_good=0; $grand_alter_good=0; $grand_total_reject=0;
		$html="";
		$floor_html="";
		$check_arr=array();
		if($db_type==0)
		{
			$sql_query="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name as buyer_name, b.style_ref_no, a.po_break_down_id, a.item_number_id, c.po_number as po_number,c.unit_price,
				sum(d.production_qnty) as good_qnty, 
				sum(CASE WHEN production_hour ='1' THEN production_qnty else 0 END) AS good_1am,
				sum(CASE WHEN production_hour ='2' THEN production_qnty else 0 END) AS good_2am,
				sum(CASE WHEN production_hour ='3' THEN production_qnty else 0 END) AS good_3am,
				sum(CASE WHEN production_hour ='4' THEN production_qnty else 0 END) AS good_4am,
				sum(CASE WHEN production_hour ='5' THEN production_qnty else 0 END) AS good_5am,
				sum(CASE WHEN production_hour ='6' THEN production_qnty else 0 END) AS good_6am,
				sum(CASE WHEN production_hour ='7' THEN production_qnty else 0 END) AS good_7am,
				sum(CASE WHEN production_hour ='8' THEN production_qnty else 0 END) AS good_8am,
				sum(CASE WHEN production_hour ='9' THEN production_qnty else 0 END) AS good_9am,
				sum(CASE WHEN production_hour ='10' THEN production_qnty else 0 END) AS good_10am,
				sum(CASE WHEN production_hour ='11' THEN production_qnty else 0 END) AS good_11am,
				sum(CASE WHEN production_hour ='12' THEN production_qnty else 0 END) AS good_12am,
				sum(CASE WHEN production_hour ='13' THEN production_qnty else 0 END) AS good_1pm,
				sum(CASE WHEN production_hour ='14' THEN production_qnty else 0 END) AS good_2pm,
				sum(CASE WHEN production_hour ='15' THEN production_qnty else 0 END) AS good_3pm,
				sum(CASE WHEN production_hour ='16' THEN production_qnty else 0 END) AS good_4pm,
				sum(CASE WHEN production_hour ='17' THEN production_qnty else 0 END) AS good_5pm,
				sum(CASE WHEN production_hour ='18' THEN production_qnty else 0 END) AS good_6pm,
				sum(CASE WHEN production_hour ='19' THEN production_qnty else 0 END) AS good_7pm,
				sum(CASE WHEN production_hour ='20' THEN production_qnty else 0 END) AS good_8pm,
				sum(CASE WHEN production_hour ='21' THEN production_qnty else 0 END) AS good_9pm,
				sum(CASE WHEN production_hour ='22' THEN production_qnty else 0 END) AS good_10pm,
				sum(CASE WHEN production_hour ='23' THEN production_qnty else 0 END) AS good_11pm,
				sum(CASE WHEN production_hour ='24' THEN production_qnty else 0 END) AS good_12pm
				from pro_garments_production_mst a,pro_garments_production_dtls d, wo_po_details_master b, wo_po_break_down c,wo_po_color_size_breakdown e
				where a.production_type=11 and d.production_type=11 and a.po_break_down_id=c.id and a.id=d.mst_id and d.color_size_break_down_id=e.id and e.po_break_down_id=c.id and e.job_no_mst=b.job_no and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and e.status_active in(1,2,3)  and e.is_deleted=0 $company_name $buyer $location $floor $line $txt_date_from 
				group by a.company_id, a.location, a.floor_id,a.po_break_down_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name, b.style_ref_no, a.item_number_id, c.po_number,c.unit_price order by a.location, a.floor_id, a.po_break_down_id";
				$production_hour_subcon=" hour ";
		}
		else if($db_type==2)
		{
			
			$production_hour="TO_CHAR(production_hour,'HH24:MI')";
			//$txt_reporting_hour=str_replace("'","",$txt_poly_date)." ".str_replace("'","",$txt_reporting_hour);
			//$production_hour="to_date('".$production_hour."','DD MONTH YYYY HH24:MI:SS')";
			
			$sql_query="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name as buyer_name, b.style_ref_no, b.set_break_down,b.set_smv, a.po_break_down_id, e.item_number_id, c.po_number as po_number,c.unit_price,
				sum(d.production_qnty) as good_qnty";
				$first=1;
				for($h=$hour;$h<$last_hour;$h++)
				{
					$bg=$start_hour_arr[$h];
					//echo $bg;die;
					$end=substr(add_time($start_hour_arr[$h],60),0,5);
					$prod_hour="good_".substr((add_time($start_hour_arr[$h],60)),0,2);
					if($first==1)
					{
						$sql_query.=", sum(CASE WHEN $production_hour<='$end' and d.production_type=11 THEN d.production_qnty else 0 END) AS $prod_hour";
					}
					else
					{
						$sql_query.=", sum(CASE WHEN $production_hour>'$bg' and $production_hour<='$end' and d.production_type=11 THEN d.production_qnty else 0 END) AS $prod_hour";
					}
					$first=$first+1;
				}
				$sql_query.=", sum(CASE WHEN $production_hour>='$start_hour_arr[$last_hour]' and $production_hour<='$start_hour_arr[24]' and d.production_type=5 THEN d.production_qnty else 0 END) AS good_23 
				
				FROM pro_garments_production_mst a,pro_garments_production_dtls d,wo_po_color_size_breakdown e, wo_po_break_down c, wo_po_details_master b
				where a.production_type=11 and  a.id=d.mst_id and d.COLOR_SIZE_BREAK_DOWN_ID=e.id and e.PO_BREAK_DOWN_ID=c.id and c.job_id=b.id 
				and a.PO_BREAK_DOWN_ID=c.id
				and d.production_type=11 and a.status_active=1 and a.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and c.is_deleted=0 and c.status_active in(1,2,3) and e.status_active in(1,2,3) and e.is_deleted=0 $company_name $buyer $location $floor $line $txt_date_from 
				group by a.company_id, a.location, a.floor_id, a.po_break_down_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name, b.style_ref_no, b.set_break_down,b.set_smv, e.item_number_id, c.po_number,c.unit_price order by a.location, a.floor_id, a.po_break_down_id";
				
				$production_hour_subcon="TO_CHAR(hour,'HH24:MI')";
		}
		//echo $sql_query;

		$sql_query_subcon="SELECT  a.company_id, a.location_id as location, a.floor_id, a.prod_reso_allo, a.production_date, a.line_id as sewing_line, b.party_id as buyer_name, c.cust_style_ref as style_ref_no, c.smv as set_smv, a.order_id as po_break_down_id, a.gmts_item_id as item_number_id , c.order_no as po_number,c.rate,
				sum(d.prod_qnty) as good_qnty";
				$first=1;
				for($h=$hour;$h<$last_hour;$h++)
				{
					$bg=$start_hour_arr[$h];
					$end=substr(add_time($start_hour_arr[$h],60),0,5);
					$prod_hour="good_".substr((add_time($start_hour_arr[$h],60)),0,2);
					if($first==1)
					{
						$sql_query_subcon.=", sum(CASE WHEN $production_hour_subcon<='$end' and d.production_type=5 THEN d.prod_qnty else 0 END) AS $prod_hour";
					}
					else
					{
						$sql_query_subcon.=", sum(CASE WHEN $production_hour_subcon>'$bg' and $production_hour_subcon<='$end' and d.production_type=5 THEN d.prod_qnty else 0 END) AS $prod_hour";
					}
					$first=$first+1;
				}
				$sql_query_subcon.=", sum(CASE WHEN $production_hour_subcon>'$start_hour_arr[$last_hour]' and $production_hour_subcon<='$start_hour_arr[24]' and d.production_type=5 THEN d.prod_qnty else 0 END) AS good_24 from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d, subcon_ord_mst b, subcon_ord_dtls c,subcon_ord_breakdown e
				where a.production_type=5 and d.production_type=5 and a.id=d.dtls_id and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and e.id=d.ord_color_size_id and e.order_id=c.id and a.order_id=e.order_id $company_name_subcon $location_subcon $floor $line_subcon   $txt_date_from 
				group by a.company_id, a.location_id, a.floor_id, a.order_id, a.prod_reso_allo, a.production_date, a.line_id, b.party_id, c.cust_style_ref, c.smv, a.gmts_item_id, c.order_no,c.rate order by a.location_id, a.floor_id, a.order_id";
		//echo $sql_query_subcon; //die;
		$sql=sql_select($sql_query);			 
		$sql_subcon=sql_select($sql_query_subcon);
		$production_data_arr=array();
		$production_po_data_arr=array();$all_style_arr=array();$style_wise_po_arr=array();
		foreach($sql as $val)
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
			else $slNo=$lineSerialArr[$sewing_line_id];
			$production_serial_arr[$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]]=$val[csf('sewing_line')];

			$production_data_arr2[$val[csf('po_break_down_id')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('item_number_id')]]+=$val[csf('good_qnty')];
			$production_data_arr3[$val[csf('po_break_down_id')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]=$val[csf('set_break_down')];

			
			$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['qty']+=$val[csf('good_qnty')];
			$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['unit_price'] =$val[csf('unit_price')];

			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['prod_reso_allo']=$val[csf('prod_reso_allo')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_break_down_id'].=$val[csf('po_break_down_id')].",";
			
			if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name'].=",".$val[csf('buyer_name')]; 
			}
			else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']=$val[csf('buyer_name')]; 
			}
			
			
			if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv'].=",".number_format($val[csf('set_smv')],2); 
			}
			else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv']=number_format($val[csf('set_smv')],2); 
			}
			
			
			if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number'].=", ".$val[csf('po_number')]; 
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style_ref'].=",".$val[csf('style_ref_no')]; 
			}
			else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']=$val[csf('po_number')]; 
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style_ref']=$val[csf('style_ref_no')]; 
			}
			
			if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('style_ref_no')]."**".$val[csf('unit_price')]; 
			}
			else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('style_ref_no')]."**".$val[csf('unit_price')]; 
			}
			
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['quantity']+=$val[csf('good_qnty')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['1am']+=$val[csf('good_01')];  
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['2am']+=$val[csf('good_02')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['3am']+=$val[csf('good_03')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['4am']+=$val[csf('good_04')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['5am']+=$val[csf('good_05')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['6am']+=$val[csf('good_06')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['7am']+=$val[csf('good_07')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['8am']+=$val[csf('good_08')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['9am']+=$val[csf('good_09')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['10am']+=$val[csf('good_10')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['11am']+=$val[csf('good_11')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['12pm']+=$val[csf('good_12')];
			
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['1pm']+=$val[csf('good_13')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['2pm']+=$val[csf('good_14')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['3pm']+=$val[csf('good_15')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['4pm']+=$val[csf('good_16')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['5pm']+=$val[csf('good_17')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['6pm']+=$val[csf('good_18')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['7pm']+=$val[csf('good_19')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['8pm']+=$val[csf('good_20')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['9pm']+=$val[csf('good_21')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['10pm']+=$val[csf('good_22')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['11pm']+=$val[csf('good_23')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['12am']+=$val[csf('good_24')]; 
			$all_po_id_arr[$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];
			$all_style_arr[$val[csf('style_ref_no')]]=$val[csf('style_ref_no')];
			$style_wise_po_arr[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];
			
			for($iii=1; $iii<=24; $iii++)
			{
				if($iii<10){$iii='0'.$iii;}
				if(!empty($val[csf("good_".$iii)]))
				{
					$last_pro_hour[$val[csf('floor_id')].'**'.$val[csf('sewing_line')]][$iii]=$iii;
				}
			}
			
			
		}
		
 		 foreach($last_pro_hour as $fl=>$ph){
			 ksort($ph);
			list($f,$l)=explode('**',$fl);
			for($ni=9; $ni<=end($ph); $ni++)
			{
				if($ni != 13){
					$production_data_arr[$f][$l]['production_hour']+=1;
				}
			}
		 }

		
		
		//print_r($last_pro_hour);
		
		 //print_r($production_data_arr_1);
		// echo "<pre>";
		// print_r($production_data_arr3);
		// echo "<pre>";
		//echo $smv_source;die;
		$all_po_ids=implode(",", array_unique($all_po_id_arr)); 
		if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
		if($smv_source==3)
		{
			/*$sql_item="select b.id, a.sam_style, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3)  and b.id in($all_po_ids)";
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
			}*/
			
			 
			$style_nos=implode("','",$all_style_arr);
			$gsdSql="SELECT a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID  from PPL_GSD_ENTRY_MST a where a.APPLICABLE_PERIOD <= $txt_date and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and a.BULLETIN_TYPE in(4) and A.STYLE_REF in('".$style_nos."') and a.APPROVED=1 
			group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID
			 ORDER BY a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC";
			     //echo $gsdSql; 
			$gsdSqlResult = sql_select($gsdSql); 
			 
			foreach($gsdSqlResult as $rows)
			{
				foreach($style_wise_po_arr[$rows[STYLE_REF]] as $po_id)
				{
					if($item_smv_array[$po_id][$rows[GMTS_ITEM_ID]]==''){
						$item_smv_array[$po_id][$rows[GMTS_ITEM_ID]]=$rows[TOTAL_SMV];
					}
				}
			}
			
			//print_r($item_smv_array);die;
		}
		else
		{
			$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where a.id=b.job_id nad b.job_id=c.job_id and b.id in($all_po_ids) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3)";
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

		//print_r($item_smv_array);die;

		$last_pro_hour=array();
		$subcon_fob_calc=array();
		foreach($sql_subcon as $val)
		{
			//floor and line wise 
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
			else $slNo=$lineSerialArr[$sewing_line_id];
			$production_serial_arr[$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]]=$val[csf('sewing_line')];
			
			$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['qty']+=$val[csf('good_qnty')];
			$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['unit_price'] = $val[csf('rate')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['prod_reso_allo']=$val[csf('prod_reso_allo')]; 
			
			if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name'].=",".$val[csf('buyer_name')]; 
			}
			else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']=$val[csf('buyer_name')]; 
			}
			
			
			if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv'].=",".number_format($val[csf('set_smv')],2); 
			}
			else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv']=number_format($val[csf('set_smv')],2); 
			}
			
			if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv_subcon']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv_subcon'].=",".number_format($val[csf('set_smv')],2); 
			}
			else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv_subcon']=number_format($val[csf('set_smv')],2); 
			}
			
			
			if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number'].=", ".$val[csf('po_number')]; 
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style_ref'].=",".$val[csf('style_ref_no')]; 
			}
			else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']=$val[csf('po_number')]; 
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style_ref']=$val[csf('style_ref_no')]; 
			}
			
			if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')].'**'."subcon**".$val[csf('style_ref_no')]; 
			}
			else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')].'**'."subcon**".$val[csf('style_ref_no')]; 
			}

			$subcon_smv_calc[$val[csf('floor_id')]][$val[csf('sewing_line')]]+=$val[csf('set_smv')]*$val[csf('good_qnty')];
			$subcon_fob_calc[$val[csf('floor_id')]][$val[csf('sewing_line')]]+=$val[csf('rate')]*$val[csf('good_qnty')];

			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['quantity']+=$val[csf('good_qnty')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['1am']+=$val[csf('good_01')];  
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['2am']+=$val[csf('good_02')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['3am']+=$val[csf('good_03')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['4am']+=$val[csf('good_04')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['5am']+=$val[csf('good_05')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['6am']+=$val[csf('good_06')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['7am']+=$val[csf('good_07')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['8am']+=$val[csf('good_08')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['9am']+=$val[csf('good_09')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['10am']+=$val[csf('good_10')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['11am']+=$val[csf('good_11')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['12pm']+=$val[csf('good_12')];
			
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['1pm']+=$val[csf('good_13')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['2pm']+=$val[csf('good_14')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['3pm']+=$val[csf('good_15')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['4pm']+=$val[csf('good_16')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['5pm']+=$val[csf('good_17')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['6pm']+=$val[csf('good_18')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['7pm']+=$val[csf('good_19')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['8pm']+=$val[csf('good_20')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['9pm']+=$val[csf('good_21')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['10pm']+=$val[csf('good_22')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['11pm']+=$val[csf('good_23')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['12am']+=$val[csf('good_24')]; 
			$all_po_id_arr_sub[$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')]; 
			$all_style_arr[$val[csf('style_ref_no')]]=$val[csf('style_ref_no')];
			$style_wise_po_arr[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];
			
			
			
			for($iii=1; $iii<=24; $iii++){
				
				if($iii<10){$iii='0'.$iii;}
				if(!empty($val[csf("good_".$iii)]))
				{
					$last_pro_hour[$val[csf('floor_id')].'**'.$val[csf('sewing_line')]][$iii]=$iii;
				}
			}

		}
		
		
		
		 foreach($last_pro_hour as $fl=>$ph){
			 ksort($ph);
			list($f,$l)=explode('**',$fl);
			for($ni=9; $ni<=end($ph); $ni++)
			{
				if($ni != 13){
					$production_data_arr[$f][$l]['production_hour']+=1;
				}
			}
		 }
		
		
		
	    $all_po_ids_subcon=implode(",", array_unique($all_po_id_arr_sub));
		$subcon_days_run_sql=sql_select("SELECT min(production_date) as min_date,order_id,line_id from subcon_gmts_prod_dtls where order_id in ($all_po_ids_subcon) and production_type=5 group by order_id,line_id");
		foreach ($subcon_days_run_sql as $key => $value) 
		{
			 $subcon_days_run_arr[$value[csf("order_id")]][$value[csf("line_id")]]=$value[csf("min_date")];
		}
		$days_run_sqls=sql_select("SELECT min(production_date) as min_date,po_break_down_id,sewing_line from pro_garments_production_mst where po_break_down_id in($all_po_ids)  and production_type=11 group by po_break_down_id,sewing_line");
		foreach ($days_run_sqls as $key => $value) 
		{
			 $days_run_main_arr[$value[csf("po_break_down_id")]][$value[csf("sewing_line")]]=$value[csf("min_date")];
		}

		//echo "<pre>";
		// print_r($production_data_arr);die;

		 /*echo "<pre>";
		print_r($subcon_smv_calc);
		echo "</pre>";*/
		unset($sql);		
		$before_8_am=$production_hour1=$production_hour2=$production_hour3=$production_hour4=$production_hour5=$production_hour6=$production_hour7=$production_hour8=0;   
		$production_hour9=$production_hour10=$production_hour11=$production_hour12=$production_hour13=$production_hour14=$production_hour15=$production_hour16=$avable_min=0;
		$production_hour17=$production_hour18=$production_hour19=$production_hour20=$production_hour21=$production_hour22=$production_hour23=$production_hour24=$today_product=0;
		$floor_hour1=$floor_hour2=$floor_hour3=$floor_hour4=$floor_hour5=$floor_hour6==$floor_hour7=$floor_hour8=$floor_before_9am=0;  $floor_name="";   
		$floor_hour9=$floor_hour10=$floor_hour11=$floor_hour12=$floor_hour13=$floor_hour14=$floor_hour15=$floor_hour16=$floor_man_power=0;
		$floor_hour17=$floor_hour18=$floor_hour19=$floor_hour20=$floor_hour21=$floor_hour22=$floor_hour23=$floor_hour24=$floor_operator=$floor_produc_min=$floor_fob_val=0;
		$floor_smv=$floor_row=$floor_helper=$floor_tgt_h=$floor_days_run=$floor_before_8_am=$floor_working_hour=$floor_ttl_tgt=$floor_today_product=$floor_avale_minute=0;
		$total_hour1=$total_hour2=$total_hour3=$total_hour4=$total_hour5=$total_hour6==$total_hour7=$total_hour8=$total_before_8am=$total_operator=$total_helper=$gnd_hit_rate=0;   
		$total_hour9=$total_hour10=$total_hour11=$total_hour12=$total_hour13=$total_hour14=$total_hour15=$total_hour16=$total_smv=$total_terget=$grand_total_product=$gnd_line_effi=0;
		$total_hour17=$total_hour18=$total_hour19=$total_hour20=$total_hour21=$total_hour22=$total_hour23=$total_hour24=$total_man_power=$gnd_avable_min=$gnd_product_min=$gnd_fob_val=0;
		$item_smv=$item_smv_total=$line_efficiency=$days_run=$total_working_hour=$gnd_total_tgt_h=$total_capacity=0;
		$j=1;
		ob_start();
		$line_number_check_arr=array();
		$smv_for_item="";
		//echo "<pre>";
		//print_r($production_data_arr);	  
		foreach($production_data_arr as $f_id=>$fname)
		{
			ksort($fname);
			foreach($fname as $l_id=>$ldata)
			{
				if($i!=1)
				{
					if(!in_array($f_id, $check_arr))
					{
						if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$html.='<tr  bgcolor="#B6B6B6">
							<td width="40">&nbsp;</td>
							<td width="80">&nbsp;</td>
							<td width="80">&nbsp;</td>
							<td width="80">&nbsp;</td>
							<td width="140">&nbsp;</td>
							<td width="140">&nbsp;</td>
							<td width="120" align="right">Sub Total:</td>
							<td align="right" width="60">&nbsp;</td>
							<td align="right" width="70">'.$floor_operator.'</td>
							<td align="right" width="50">'.$floor_helper.'</td>
							<td align="right" width="60">'.$floor_man_power.'</td>
							<td align="right" width="70">'.$floor_tgt_h.'</td>
							<td align="right" width="60">'.$floor_days_run.'</td>
							<td align="right" width="70">'.$floor_capacity.'</td>
							<td align="right" width="60">'.number_format($floor_working_hour,2).'</td>
							<td align="right" width="60">'.$floor_working_hours.'</td>
							<td align="right" width="60"></td>
							
							<td align="right" width="80">'.number_format($floor_ttl_tgt,0).'</td>
							<td align="right" width="80">'.$floor_today_product.'</td>
							<td align="right" width="80">'.number_format($floor_fob_val,2).'</td>
							
							<td align="right" width="80">'.number_format(($floor_today_product-$floor_ttl_tgt),0).'</td>
							<td align="right" width="100">'.$floor_avale_minute.'</td>
							<td align="right" width="100">'.number_format($floor_produc_min,2).'</td>
							<td align="right" width="60">'.number_format(($floor_today_product/$floor_ttl_tgt)*100,2).'%</td>
							<td align="right" width="90">'.number_format($floor_efficency,2).'%</td>
							<td align="right" width="50">'.$floor_hour9.'</td>
							<td align="right" width="50">'.$floor_hour10.'</td>
							<td align="right" width="50">'.$floor_hour11.'</td>
							<td align="right" width="50">'.$floor_hour12.'</td>
							<td align="right" width="50">'.$floor_hour13.'</td>
							<td align="right" width="50">'.$floor_hour14.'</td>
							<td align="right" width="50">'.$floor_hour15.'</td>
							<td align="right" width="50">'.$floor_hour16.'</td>
							<td align="right" width="50">'.$floor_hour17.'</td>
							<td align="right" width="50">'.$floor_hour18.'</td>
							<td align="right" width="50">'.$floor_hour19.'</td>
							<td align="right" width="50">'.$floor_hour20.'</td>
							<td align="right" width="50">'.$floor_hour21.'</td>
							<td align="right" width="50">'.$floor_hour22.'</td>
							<td align="right" width="50">'.$floor_hour23.'</td>
							<td align="right">'.$floor_hour24.'</td>
						</tr>';
						
						$floor_html.='<tbody>';
						$floor_html.="<tr bgcolor='$bgcolor' onclick=change_color('floor_$j','$bgcolor') id=floor_$j>";
						$floor_html.='<td width="40">'.$j.'&nbsp;</td>
							<td width="80" align="center">'.$floor_name.' </td>
							<td width="70" align="right">'.$floor_tgt_h.'</td>
							<td width="70" align="right">'.$floor_capacity.'</td>
							<td width="60" align="right">'.$floor_man_power.'</td>
							<td width="70" align="right">'.$floor_operator.'</td>
							<td width="50" align="right">'.$floor_helper.'</td>
							<td align="right" width="60">'.number_format($floor_working_hour,2).'</td>
							<td align="right" width="80">'.number_format($floor_ttl_tgt,0).'</td>
							<td align="right" width="80">'.$floor_today_product.'</td>
							<td align="right" width="80">'.number_format($floor_fob_val,2).'</td>
							<td align="right" width="80">'.number_format(($floor_today_product-$floor_ttl_tgt),0).'</td>
							<td align="right" width="100">'.$floor_avale_minute.'</td>
							<td align="right" width="100">'.number_format($floor_produc_min,2).'</td>
							<td align="right" width="90">'.number_format(($floor_today_product/$floor_ttl_tgt)*100,2).'%</td>
							<td align="right" width="90">'.number_format($floor_efficency,2).'%</td>
							<td align="right" width="50">'.$floor_hour9.'</td>
							<td align="right" width="50">'.$floor_hour10.'</td>
							<td align="right" width="50">'.$floor_hour11.'</td>
							<td align="right" width="50">'.$floor_hour12.'</td>
							<td align="right" width="50">'.$floor_hour13.'</td>
							<td align="right" width="50">'.$floor_hour14.'</td>
							<td align="right" width="50">'.$floor_hour15.'</td>
							<td align="right" width="50">'.$floor_hour16.'</td>
							<td align="right" width="50">'.$floor_hour17.'</td>
							<td align="right" width="50">'.$floor_hour18.'</td>
							<td align="right" width="50">'.$floor_hour19.'</td>
							<td align="right" width="50">'.$floor_hour20.'</td>
							<td align="right" width="50">'.$floor_hour21.'</td>
							<td align="right" width="50">'.$floor_hour22.'</td>
							<td align="right" width="50">'.$floor_hour23.'</td>
							<td align="right">'. $floor_hour24.'</td>
						</tr>';
						$floor_name=""; $floor_smv=0; $floor_row=0; $floor_operator=0; $floor_helper=0; $floor_tgt_h=0; $floor_man_power=0; $floor_days_run=0; $floor_before_9_am=0;
						$floor_hour9=0; $floor_hour10=0; $floor_hour11=0; $floor_hour12=0; $floor_hour13=0; $floor_hour14=0; $floor_hour15=0; $floor_hour16=0; $floor_hour17=0; $floor_hour18=0; $floor_hour19=0; $floor_hour20=0; $floor_hour21=0; $floor_hour22=0; $floor_hour23=0; $floor_hour24=0;
						$floor_working_hour=0; $floor_ttl_tgt=0; $floor_today_product=0; $floor_avale_minute=0; $floor_produc_min=0;$floor_fob_val=0; $floor_efficency=0; $floor_man_power=0; $floor_capacity=0;
						$j++;
					}
				}
				$floor_row++;	
			
				$po_number=array_unique(explode(',',$row[csf('po_number')]));
				$germents_item=array_unique(explode('****',$ldata['item_number_id']));
				$buyer_neme_all=array_unique(explode(',',$ldata['buyer_name']));
				
				// $set_smv_all=implode(',',array_unique(explode(',',$ldata['set_smv'])));
				$style_ref=implode(', ',array_unique(explode(',',$ldata['style_ref'])));
				$po_break_down_id=implode(', ',array_unique(explode(',',$ldata['po_break_down_id'])));
				//print_r($germents_item);
				$set_smv_all="";
				$itm_smv_arr = array();
				$chk_smv_array = array();
				foreach ($germents_item as $value) 
				{
					$po_item = explode("**", $value);
					$po_id = $po_item[0];
					$item_id = $po_item[1];
					$style = $po_item[2];
					if($production_data_arr2[$po_id][$f_id][$l_id][$item_id] !="")
					{
						$break_down_smv = $production_data_arr3[$po_id][$f_id][$l_id];
						$break_down_smv_arr = explode("__", $break_down_smv);
						// print_r($break_down_smv_arr);die();
						foreach ($break_down_smv_arr as $smv) 
						{
							$set_smv = explode("_", $smv);
							if($item_id==$set_smv[0])
							{
								if(!isset($chk_smv_array[$style][$item_id]))
								{
									$set_smv[2]=($item_smv_array[$po_id][$item_id])?$item_smv_array[$po_id][$item_id]:$set_smv[2];
									
									$item_smv_array3[$po_id][$item_id]=$set_smv[2];
									
									
									$itm_smv_arr[$po_id][$f_id][$l_id]+=$set_smv[2];
									$chk_smv_array[$style][$item_id] = $set_smv[2];
								}
							}							
						}
					}	
				}
				//print_r($itm_smv_arr);die;
				$po_break_down_id = explode(",", $po_break_down_id);
				//print_r($po_break_down_id);die;
				foreach (array_filter($po_break_down_id) as $po) 
				{
					$po=$po*1;
					if(!empty($itm_smv_arr[$po][$f_id][$l_id])){
						$set_smv_all .= number_format($itm_smv_arr[$po][$f_id][$l_id],2).",";
					}
				}
				//print_r($po_break_down_id);die;
				
				$subcontact_smvs = explode(",", $ldata['set_smv_subcon']);
				foreach (array_filter($subcontact_smvs) as $set_smv_subcon) 
				{
					$set_smv_all .= $set_smv_subcon.",";
				}
				//echo	$ldata['set_smv_subcon'];die;
				
				// print_r($po_break_down_id);
				// echo $set_smv_all;
				
				$buyer_name="";
				foreach($buyer_neme_all as $buy)
				{
					if($buyer_name!='') $buyer_name.=',';
					$buyer_name.=$buyerArr[$buy];
				}
				$chk_item_array = array();
				$garment_itemname=''; $item_smv=""; $smv_for_item=""; $produce_minit=""; $order_no_total="";
				$prod_fob_val = 0;
				$garment_item_id_arr=array();
				$days_run="";
				foreach($germents_item as $g_val)
				{
					$po_garment_item=explode('**',$g_val);
					if($po_garment_item[2]=="subcon" )
					{  
						//$day_run_sql=sql_select("");
						 
						 $sewing_day=$subcon_days_run_arr[$po_garment_item[0]][$l_id];

					}
					else
					{  
						//$day_run_sql=sql_select("");
						$sewing_day=$days_run_main_arr[$po_garment_item[0]][$l_id];
					}
					
					 
				    //$sewing_day=$day_run_sql[0][csf('min_date')];
					if($sewing_day!="")
					{ 
						if($days_run=="")
						{  
							  $days_run .=datediff("d",$sewing_day,$pr_date);
						}
						else
						{
							  
							 $days_run .=','.datediff("d",$sewing_day,$pr_date);
						}
						
					}
					if(!isset($chk_item_array[$style_ref][$po_garment_item[1]]))
					{	
						if($garment_itemname!='') $garment_itemname.=',';
							$garment_itemname.=$garments_item[$po_garment_item[1]];
							$garment_item_id_arr[$po_garment_item[1]]=$po_garment_item[1];
					}	
					$chk_item_array[$style_ref][$po_garment_item[1]] = $po_garment_item[1];

					/*if($garment_itemname!='') $garment_itemname.=',';
						$garment_itemname.=$garments_item[$po_garment_item[1]];*/
						
					if($item_smv!='') $item_smv.='/';
						$item_smv.=$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
						
					if($order_no_total!="") $order_no_total.=",";
						$order_no_total.=$po_garment_item[0];
					
					if($smv_for_item!="") 
						$smv_for_item.="****".$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
					else
						$smv_for_item=$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];	
					
					//$produce_minit+=($production_po_data_arr[$f_id][$l_id][$po_garment_item[0]]['qty']*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]]);
					
					$produce_minit+=($production_po_data_arr[$f_id][$l_id][$po_garment_item[0]]['qty']*$item_smv_array3[$po_garment_item[0]][$po_garment_item[1]]);
					
					
					
					
					
					//echo $production_po_data_arr[$f_id][$l_id][$po_garment_item[0]]['qty'].'='.$po_garment_item[0].'**'.$po_garment_item[1]."<br>";
					
					
					
					$prod_fob_val += ($production_po_data_arr[$f_id][$l_id][$po_garment_item[0]]['qty']*$production_po_data_arr[$f_id][$l_id][$po_garment_item[0]]['unit_price']);
				}

				$produce_minit+=$subcon_smv_calc[$f_id][$l_id]; 
				$prod_fob_val+=$subcon_fob_calc[$f_id][$l_id]; 
			 
				$sewing_line='';
				if($ldata['prod_reso_allo']==1)
				{
					$line_number=explode(",",$prod_reso_arr[$l_id]);
					foreach($line_number as $val)
					{
						if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=", ".$lineArr[$val];
					}
				}
				else $sewing_line=$lineArr[$l_id];
			
				$total_eff_hour=0;
				$production_hour1=$ldata['1am']; if($production_hour1!=0) $total_eff_hour+=1;
				$production_hour2=$ldata['2am']; if($production_hour2!=0) $total_eff_hour+=1;
				$production_hour3=$ldata['3am']; if($production_hour3!=0) $total_eff_hour+=1;
				$production_hour4=$ldata['4am']; if($production_hour4!=0) $total_eff_hour+=1;
				$production_hour5=$ldata['5am']; if($production_hour5!=0) $total_eff_hour+=1;
				$production_hour6=$ldata['6am']; if($production_hour6!=0) $total_eff_hour+=1;
				$production_hour7=$ldata['7am']; if($production_hour7!=0) $total_eff_hour+=1;
				$production_hour8=$ldata['8am']; if($production_hour8!=0) $total_eff_hour+=1;
				$production_hour9=$ldata['9am']; if($production_hour9!=0) $total_eff_hour+=1;
				$production_hour10=$ldata['10am']; if($production_hour10!=0) $total_eff_hour+=1;
				$production_hour11=$ldata['11am']; if($production_hour11!=0) $total_eff_hour+=1;
				$production_hour12=$ldata['12pm']; if($production_hour12!=0) $total_eff_hour+=1;
				$production_hour13=$ldata['1pm']; if($production_hour13!=0) $total_eff_hour+=1;
				$production_hour14=$ldata['2pm']; if($production_hour14!=0) $total_eff_hour+=1;
				$production_hour15=$ldata['3pm']; if($production_hour15!=0) $total_eff_hour+=1;
				$production_hour16=$ldata['4pm']; if($production_hour16!=0) $total_eff_hour+=1;
				$production_hour17=$ldata['5pm']; if($production_hour17!=0) $total_eff_hour+=1;
				$production_hour18=$ldata['6pm']; if($production_hour18!=0) $total_eff_hour+=1; 
				$production_hour19=$ldata['7pm']; if($production_hour19!=0) $total_eff_hour+=1;
				$production_hour20=$ldata['8pm']; if($production_hour20!=0) $total_eff_hour+=1;
				$production_hour21=$ldata['9pm']; if($production_hour21!=0) $total_eff_hour+=1;
				$production_hour22=$ldata['10pm']; if($production_hour22!=0) $total_eff_hour+=1;
				$production_hour23=$ldata['11pm']; if($production_hour23!=0) $total_eff_hour+=1;
				$production_hour24=$ldata['12am']; if($production_hour24!=0) $total_eff_hour+=1;
			
				if($total_eff_hour>$prod_resource_array[$l_id][$pr_date]['working_hour'])
				{
					$total_eff_hour=$prod_resource_array[$l_id][$pr_date]['working_hour'];
				}
				$before_8_am=$production_hour1+$production_hour2+$production_hour3+$production_hour4+$production_hour5+$production_hour6+$production_hour7+$production_hour8;//$before_8_am+
				$today_product=$production_hour8+$production_hour9+$production_hour10+$production_hour11+$production_hour12+$production_hour13+$production_hour14+$production_hour15+$production_hour16+$production_hour17+$production_hour18+$production_hour19+$production_hour20+$production_hour21+$production_hour22+$production_hour23+$production_hour24;
				
				/*if($sewing_day!="")
				{
					$days_run= $diff=datediff("d",$sewing_day,$pr_date);
				}
				else  $days_run=0;*/
				
				//******************************* line effiecency****************************************************************************['']
				$current_wo_time=0;
				if($current_date==$search_prod_date)
				{
					$prod_wo_hour=$prod_resource_array[$l_id][$pr_date]['working_hour'];
					
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
					$current_wo_time=$prod_resource_array[$l_id][$pr_date]['working_hour'];
					$cla_cur_time=$prod_resource_array[$l_id][$pr_date]['working_hour'];
				}
				//$avable_min=($prod_resource_array[$l_id][$pr_date]['man_power'])*$prod_resource_array[$l_id][$pr_date]['working_hour']*60;
				//******************************* line effiecency****************************************************************************['']
				$smv_adjustmet_type=$prod_resource_array[$l_id][$pr_date]['smv_adjust_type'];
				//$summary_smv_adjustmet_type=$no_prod_line_arr[$f_id]['smv_adjust_type'];
				$eff_target=($prod_resource_array[$l_id][$pr_date]['terget_hour']*$prod_resource_array[$l_id][$pr_date]['working_hour']);
				
				/*if($total_eff_hour>=$prod_resource_array[$l_id][$pr_date]['working_hour'])
				{*/
					$total_adjustment=0;
					if(str_replace("'","",$smv_adjustmet_type)==1)
					{ 
						$total_adjustment=$prod_resource_array[$l_id][$pr_date]['smv_adjust'];
					}
					else if(str_replace("'","",$smv_adjustmet_type)==2)
					{
						$total_adjustment=($prod_resource_array[$l_id][$pr_date]['smv_adjust'])*(-1);
					}
				//}
				
				/*if($total_adjustment_summary>=$no_prod_line_arr[$f_id]['working_hour'])
				{
					if(str_replace("'","",$summary_smv_adjustmet_type)==1)
					{ 
						$total_adjustment_summary=$no_prod_line_arr[$f_id]['smv_adjust'];
					}
					if(str_replace("'","",$summary_smv_adjustmet_type)==2)
					{
						$total_adjustment_summary=($no_prod_line_arr[$f_id]['smv_adjust'])*(-1);
					}
				}*/
				
				//$efficiency_min=($prod_resource_array[$l_id][$pr_date]['man_power'])*$prod_resource_array[$l_id][$pr_date]['working_hour']*60;
				$efficiency_min=$total_adjustment+($prod_resource_array[$l_id][$pr_date]['man_power'])*$cla_cur_time*60;
				//$efficiency_min+=$prod_resource_array[$l_id][$pr_date]['man_power'];
			
				$line_efficiency=(($produce_minit)*100)/$efficiency_min;
				//********************************* calclution floor total ****************************************************$pr_date],$sewing_day
				$floor_name=$floorArr[$f_id];	
				$floor_hour24+=$production_hour24;
				$floor_hour9+=$production_hour9;
				$floor_hour10+=$production_hour10;
				$floor_hour11+=$production_hour11;
				$floor_hour12+=$production_hour12; 
				$floor_hour13+=$production_hour13; 
				$floor_hour14+=$production_hour14;
				$floor_hour15+=$production_hour15;
				$floor_hour16+=$production_hour16;
				$floor_hour17+=$production_hour17;
				$floor_hour18+=$production_hour18;
				$floor_hour19+=$production_hour19; 
				$floor_hour20+=$production_hour20;
				$floor_hour21+=$production_hour21;
				$floor_hour22+=$production_hour22;
				$floor_hour23+=$production_hour23; 
				$floor_before_8_am+=$before_8_am;
				$floor_smv+=$item_smv;
				$floor_capacity+=$prod_resource_array[$l_id][$pr_date]['capacity'];
				$floor_operator+=$prod_resource_array[$l_id][$pr_date]['operator'];
				$floor_helper+=$prod_resource_array[$l_id][$pr_date]['helper'];
				$floor_tgt_h+=$prod_resource_array[$l_id][$pr_date]['terget_hour'];	
				$floor_days_run+=$days_run;
				$floor_working_hour+=$prod_resource_array[$l_id][$pr_date]['working_hour']; 
				$floor_ttl_tgt+=$eff_target;
				$floor_today_product+=$today_product;
				$floor_avale_minute+=$efficiency_min;
				$floor_produc_min+=$produce_minit; 
				$floor_fob_val+=$prod_fob_val; 
				$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
				$floor_man_power+=$prod_resource_array[$l_id][$pr_date]['man_power'];
				//**************************** calclution total ********************************************************************
				$total_hour24+=$production_hour24;
				$total_hour9+=$production_hour9;
				$total_hour10+=$production_hour10;
				$total_hour11+=$production_hour11;
				$total_hour12+=$production_hour12; 
				$total_hour13+=$production_hour13;
				$total_hour14+=$production_hour14;
				$total_hour15+=$production_hour15;
				$total_hour16+=$production_hour16;
				$total_hour17+=$production_hour17;
				$total_hour18+=$production_hour18;
				$total_hour19+=$production_hour19; 
				$total_hour20+=$production_hour20;
				$total_hour21+=$production_hour21;
				$total_hour22+=$production_hour22;
				$total_hour23+=$production_hour923; 
				$total_before_8am+=$before_8_am;
				$total_capacity+=$prod_resource_array[$l_id][$pr_date]['capacity'];
				$gnd_total_tgt_h+=$prod_resource_array[$l_id][$pr_date]['terget_hour'];	
				$total_working_hour+=$prod_resource_array[$l_id][$pr_date]['working_hour']; 
				$total_operator+=$prod_resource_array[$l_id][$pr_date]['operator'];
				$total_helper+=$prod_resource_array[$l_id][$pr_date]['helper'];
				$total_man_power+=$prod_resource_array[$l_id][$pr_date]['man_power'];
				//$total_smv+=$item_smv;
				$total_terget+=$eff_target;
				$grand_total_product+=$today_product;
				$gnd_avable_min+=$efficiency_min;
				$gnd_product_min+=$produce_minit; 
				$gnd_fob_val+=$prod_fob_val; 
				//$gnd_hit_rate=($grand_total_product/$total_terget)*100;
				//$gnd_line_effi=($gnd_product_min/$gnd_avable_min)*100;
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$html.='<tbody>';
				$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
				$html.='<td width="40">'.$i.'&nbsp;</td>
							<td width="80">'.$floor_name.'&nbsp; </td>
							<td align="center" width="80">'.$sewing_line.'&nbsp; </td>
							<td width="80"><p>'.$buyer_name.'&nbsp;</p></td>
							<td width="140"><p>'.$style_ref.'&nbsp;</p></td>
							<td width="140"><p>'.$ldata['po_number'].'&nbsp;</p></td>
							<td width="120"><p>'.$garment_itemname.'&nbsp;<p/> </td>
							
							<td align="center" width="60"><p>'.chop($set_smv_all,',').'</p></td>
							<td align="right" width="70">'.$prod_resource_array[$l_id][$pr_date]['operator'].'</td>
							<td align="right" width="50">'.$prod_resource_array[$l_id][$pr_date]['helper'].'</td>
							<td align="right" width="60">'.$prod_resource_array[$l_id][$pr_date]['man_power'].'</td>
							<td align="right" width="70">'.$prod_resource_array[$l_id][$pr_date]['terget_hour'].'</td>
							<td align="right" width="60"><p>'.$days_run.'</p></td>
							<td align="right" width="70">'.$prod_resource_array[$l_id][$pr_date]['capacity'].'</td>
							<td align="right" width="60">'.$prod_resource_array[$l_id][$pr_date]['working_hour'].'</td>
							<td align="right" width="60">'.$cla_cur_time.'</td>
							<td align="right" width="60">'.$production_data_arr[$f_id][$l_id]['production_hour'].'</td>
							<td align="right" width="80">'.number_format($eff_target,0).'</td>
							
							<td align="right" width="80">
								<a href="##" onclick="openmypage('."'".$cbo_company_id."'".",'".$order_no_total."',".$f_id.",".$l_id.",'tot_prod','".$smv_for_item."',".$txt_date.',11,'."'".implode(',',$garment_item_id_arr)."'".')">'.$today_product.'</a>
							</td>
							<td align="right" width="80">
								<a href="##" onclick="openmypage('."'".$cbo_company_id."'".",'".$order_no_total."',".$f_id.",".$l_id.",'tot_fob_val','".$smv_for_item."',".$txt_date.',11,'."'".implode(',',$garment_item_id_arr)."'".')">'.number_format($prod_fob_val,2).'</a>
							</td>
							
							<td align="right" width="80">'.number_format(($today_product-$eff_target),0).'</td>
							<td align="right" width="100">'.number_format($efficiency_min,2).'</td>
							<td align="right" width="100"><a href="##" onclick="openmypage('."'".$cbo_company_id."'".",'".$order_no_total."',".$f_id.",".$l_id.",'tot_prod','".$smv_for_item."',".$txt_date.',11,'."'".implode(',',$garment_item_id_arr)."'".')">'.number_format($produce_minit,2).'</a></td>
							
							<td align="right" width="60">'.number_format(($today_product/$eff_target)*100,2).' %</td>
							<td align="right" width="90">'.number_format($line_efficiency,2). '%</td>
							<td align="right" width="50">'.$production_hour9.'</td>
							<td align="right" width="50">'.$production_hour10.'</td>
							<td align="right" width="50">'.$production_hour11.'</td>
							<td align="right" width="50">'.$production_hour12.'</td>
							<td align="right" width="50">'.$production_hour13.'</td>
							<td align="right" width="50">'.$production_hour14.'</td>
							<td align="right" width="50">'.$production_hour15.'</td>
							<td align="right" width="50">'.$production_hour16.'</td>
							<td align="right" width="50">'.$production_hour17.'</td>
							<td align="right" width="50">'.$production_hour18.'</td>
							<td align="right" width="50">'.$production_hour19.'</td>
							<td align="right" width="50">'.$production_hour20.'</td>
							<td align="right" width="50">'.$production_hour21.'</td>
							<td align="right" width="50">'.$production_hour22.'</td>
							<td align="right" width="50">'.$production_hour23.'</td>
							<td align="right" >'.$production_hour24.'</td>
						</tr>
					</tbody>';
				$i++;
				$check_arr[]=$f_id;
			}
		}
		$html.='<tr  bgcolor="#B6B6B6">
			<td >&nbsp;</td>
			<td>&nbsp;</td>
			<td >&nbsp;</td>
			<td >&nbsp;</td>
			<td >&nbsp;</td>
			<td >&nbsp;</td>
			<td align="right">Sub Total:</td>
			<td align="right">&nbsp;</td>
			<td align="right" >'.$floor_operator.'</td>
			<td align="right" >'.$floor_helper.'</td>
			<td align="right" >'.$floor_man_power.'</td>
			<td align="right" >'.$floor_tgt_h.'</td>
			<td align="right" >'.$floor_days_run.'</td>
			<td align="right" >&nbsp;</td>
			<td align="right" >'.number_format($floor_working_hour,2).'</td>
			<td align="right">&nbsp;</td>
			<td align="right" >&nbsp;</td>
			<td align="right" >'.number_format($floor_ttl_tgt,0).'</td>
			<td align="right">'.$floor_today_product.'</td>
			<td align="right">'.number_format($floor_fob_val,2).'</td>
			<td align="right" >'.number_format(($floor_today_product-$floor_ttl_tgt),2).'</td>
			<td align="right">'.$floor_avale_minute.'</td>
			<td align="right">'.number_format($floor_produc_min,2).'</td>
			<td align="right" >'.number_format(($floor_today_product/$floor_ttl_tgt)*100,2).'%</td>
			<td align="right" >'.number_format($floor_efficency,2).' %</td>
			<td align="right">'.$floor_hour9.'</td>
			<td align="right">'.$floor_hour10.'</td>
			<td align="right">'.$floor_hour11.'</td>
			<td align="right">'.$floor_hour12.'</td>
			<td align="right">'.$floor_hour13.'</td>
			<td align="right">'.$floor_hour14.'</td>
			<td align="right">'.$floor_hour15.'</td>
			<td align="right">'.$floor_hour16.'</td>
			<td align="right">'.$floor_hour17.'</td>
			<td align="right">'.$floor_hour18.'</td>
			<td align="right">'.$floor_hour19.'</td>
			<td align="right">'.$floor_hour20.'</td>
			<td align="right">'.$floor_hour21.'</td>
			<td align="right">'.$floor_hour22.'</td>
			<td align="right">'.$floor_hour23.'</td>
			<td align="right">'.$floor_hour24.'</td>
		</tr>';
		
		
		
		
		$floor_html.="<tr bgcolor='$bgcolor' onclick=change_color('floor_$j','$bgcolor') id=floor_$j>";
		$floor_html.='<td width="40">'.$j.'&nbsp;</td>
			<td width="80" align="center">'.$floor_name.'&nbsp; </td>
			<td width="70" align="right">'.$floor_tgt_h.'</td>
			<td width="70" align="right">'.$floor_capacity.'</td>
			<td width="60" align="right">'.$floor_man_power.'</td>
			<td width="70" align="right">'.$floor_operator.'</td>
			<td width="50" align="right">'.$floor_helper.'</td>
			<td align="right" width="60">'.number_format($floor_working_hour,2).'</td>
			<td align="right" width="80">'.number_format($floor_ttl_tgt,0).'</td>
			<td align="right" width="80">'.$floor_today_product.'</td>
			<td align="right" width="80">'.number_format($floor_fob_val,2).'</td>
			<td align="right" width="80">'.number_format(($floor_today_product-$floor_ttl_tgt),0).'</td>
			<td align="right" width="100">'.$floor_avale_minute.'</td>
			<td align="right" width="100">'.number_format($floor_produc_min,2).'</td>
			<td align="right" width="90">'.number_format(($floor_today_product/$floor_ttl_tgt)*100,2).'%</td>
			<td align="right" width="90">'.number_format($floor_efficency,2).'%</td>
			
			<td align="right" width="50">'.$floor_hour9.'</td>
			<td align="right" width="50">'.$floor_hour10.'</td>
			<td align="right" width="50">'.$floor_hour11.'</td>
			<td align="right" width="50">'.$floor_hour12.'</td>
			<td align="right" width="50">'.$floor_hour13.'</td>
			<td align="right" width="50">'.$floor_hour14.'</td>
			<td align="right" width="50">'.$floor_hour15.'</td>
			<td align="right" width="50">'.$floor_hour16.'</td>
			<td align="right" width="50">'.$floor_hour17.'</td>
			<td align="right" width="50">'.$floor_hour18.'</td>
			<td align="right" width="50">'.$floor_hour19.'</td>
			<td align="right" width="50">'.$floor_hour20.'</td>
			<td align="right" width="50">'.$floor_hour21.'</td>
			<td align="right" width="50">'.$floor_hour22.'</td>
			<td align="right">'.$floor_hour23.'</td>
			<td align="right" width="50">'.$floor_hour24.'</td>
		</tr></tbody>';
		$smv_for_item="";
		?>
		<fieldset style="width:2760px">
			<table width="2280" cellpadding="0" cellspacing="0"> 
				<tr class="form_caption">
					<td colspan="28" align="center"><strong><? echo $report_title; ?></strong></td> 
				</tr>
				<tr class="form_caption">
					<td colspan="28" align="center"><strong><? echo $companyArr[$comapny_id]; ?></strong></td> 
				</tr>
				<tr class="form_caption">
					<td colspan="28" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date)) ); ?></strong></td> 
				</tr>
			</table>
			<br />
			<label><strong>Report Sumarry:-</strong></label> 
			<table id="table_header_2" class="rpt_table" width="2030" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr height="50">
						<th width="40">SL</th>
						<th width="80">Floor Name</th>
						<th width="70">Hourly Terget</th>
						<th width="70">Capacity</th>
						<th width="60">Total Man  Power</th>
						<th width="70">Operator</th>
						<th width="50">Helper</th>
						<th width="60">Line Hour</th>
						<th width="80">Day Target</th>
						<th width="80">Total Prod.</th>
						<th width="80">Total Production FOB Value</th>
						<th width="80">Variance </th>
						<th width="100">SMV Available</th>
						<th width="100">SMV Achieved</th>
						<th width="90">Achievement %</th>
						<th width="90">Floor Eff. %</th>
						<th width="50" style="vertical-align:middle"><div class="block_div">9 AM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">10 AM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">11 AM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">12 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">1 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">2 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">3 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">4 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">5 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">6 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">7 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">8 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">9 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">10 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">11 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">12 AM</div></th>
					</tr>
				</thead>
			</table>
			<div style="width:2048px; max-height:400px; overflow-y:scroll" id="scroll_body_1">
				<table class="rpt_table" width="2030" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
					<? echo $floor_html; ?> 
					<tfoot>
						<tr>
							<th width="40">&nbsp;</th>
							<th width="80" align="right"> Total: </th>
							<th width="70"><? echo $gnd_total_tgt_h;   ?> </th>
							<th width="70" align="right"><? echo $total_capacity; ?> </th>
							<th width="60"><? echo $total_man_power; ?></th>
							<th width="70"><? echo $total_operator; ?></th>
							<th width="50"><? echo $total_helper; ?></th>
							<th align="right" width="60"><? echo number_format($total_working_hour,2); ?></th>
							<th align="right" width="80"><? echo number_format($total_terget,0); ?></th>
							<th align="right" width="80"><? echo $grand_total_product; ?></th>
							<th align="right" width="80"><? echo number_format($gnd_fob_val,2); ?></th>
							<th align="right" width="80"><? echo number_format(($grand_total_product-$total_terget),0); ?></th>
							<th align="right" width="100"><? echo $gnd_avable_min; ?></th>
							<th align="right" width="100"><? echo number_format($gnd_product_min,2); ?></th>
							<th align="right" width="90"><? echo number_format(($grand_total_product/$total_terget)*100,2)."%"; ?></th>
							<th align="center" width="90"><?  echo number_format(($gnd_product_min/$gnd_avable_min)*100,2)."%"; ?></th>
							<th align="right" width="50"><? echo $total_hour9; ?></th>
							<th align="right" width="50"><? echo $total_hour10; ?></th>
							<th align="right" width="50"><? echo $total_hour11; ?></th>
							<th align="right" width="50"><? echo $total_hour12; ?></th>
							<th align="right" width="50"><? echo $total_hour13; ?></th>
							<th align="right" width="50"><? echo $total_hour14; ?></th>
							<th align="right" width="50"><? echo $total_hour15; ?></th>
							<th align="right" width="50"><? echo $total_hour16; ?></th>
							<th align="right" width="50"><? echo $total_hour17; ?></th>
							<th align="right" width="50"><? echo $total_hour18; ?></th>
							<th align="right" width="50"><? echo $total_hour19; ?></th>
							<th align="right" width="50"><? echo $total_hour20; ?></th>
							<th align="right" width="50"><? echo $total_hour21; ?></th>
							<th align="right" width="50"><? echo $total_hour22; ?></th>
							<th align="right" width="50"><? echo $total_hour23; ?></th>
							<th align="right" ><? echo $total_hour24; ?></th>
						</tr>
					</tfoot>
				</table>
			</div>
			</br><br/>
			<!-- ================================== details part ============================== -->
			<table id="table_header_1" class="rpt_table" width="2840" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr height="50">
						<th width="40">SL</th>
						<th width="80">Floor Name </th>
						<th width="80">Line No</th>
						<th width="80">Buyer</th>
	                    <th width="140">Style Ref.</th>
						<th width="140">Order No</th>
						<th width="120">Garments Item</th>
						<th width="60">SMV</th>
						<th width="70">Operator</th>
						<th width="50">Helper</th>
						<th width="60"> Man Power</th>
						<th width="70">Hourly Terget</th>
						<th width="60">Days Run</th>
						<th width="70">Capacity</th>
						<th width="60">Working Hour</th>
	                    <th width="60">Current Hour</th>
                        <th width="60">Production Hour</th>
						<th width="80">Total Target</th>
						<th width="80">Total Prod.</th>
						<th width="80">Total Production FOB Vale</th>
						<th width="80">Variance pcs </th>
						<th width="100">Available Minutes</th>
						<th width="100">Produce Minutes</th>
						<th width="60">Target Hit rate</th>
						<th width="90">Line Effi %</th>
						<th width="50" style="vertical-align:middle"><div class="block_div">9 AM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">10 AM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">11 AM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">12 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">1 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">2 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">3 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">4 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">5 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">6 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">7 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">8 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">9 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">10 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">11 PM</div></th>
						<th style="vertical-align:middle"><div class="block_div">12 AM</div></th>
					</tr>
				</thead>
			</table>
			<div style="width:2860px; max-height:400px; overflow-y:scroll" id="scroll_body_2">
				<table class="rpt_table" width="2840" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<? echo $html;  ?>
					<tfoot>
						<tr>
							<th width="40">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="140">&nbsp;</th>
	                        <th width="140">&nbsp;</th>
							<th width="120" align="right">Grand Total:</th>
							<th align="right" width="60"><? // echo number_format($total_smv/($i-1),2); ?>&nbsp;</th>
							<th align="right" width="70"><? echo $total_operator; ?></th>
							<th align="right" width="50"><? echo $total_helper; ?></th>
							<th align="right" width="60"><? echo $total_man_power; ?></th>
							<th align="right" width="70"><?  echo $gnd_total_tgt_h; ?></th>
							<th align="right" width="60"><? //echo $prod_resource_array[$row[csf('sewing_line')]][$pr_date]['man_power']; ?>&nbsp;</th>
							<th align="right" width="70"><? echo $total_capacity; ?></th>
							<th align="right" width="60"><? echo number_format($total_working_hour,2); ?></th>
	                        <th align="right" width="60">&nbsp;</th>
	                        <th align="right" width="60">&nbsp;</th>
							<th align="right" width="80"><? echo number_format($total_terget,0); ?></th>
							<th align="right" width="80"><? echo $grand_total_product; ?></th>
							<th align="right" width="80"><? echo number_format($gnd_fob_val,2); ?></th>
							<th align="right" width="80"><? echo number_format(($grand_total_product-$total_terget),0); ?></th>
							<th align="right" width="100"><? echo $gnd_avable_min; ?></th>
							<th align="right" width="100"><? echo number_format($gnd_product_min,2); ?></th>
							<th align="right" width="60"><? echo number_format(($grand_total_product/$total_terget)*100,2)."%"; ?></th>
							<th align="right" width="90" ><? echo number_format(($gnd_product_min/$gnd_avable_min)*100,2)."%";?></th>
                            
							<th align="right" width="50"><? echo $total_hour9; ?></th>
							<th align="right" width="50"><? echo $total_hour10; ?></th>
							<th align="right" width="50"><? echo $total_hour11; ?></th>
							<th align="right" width="50"><? echo $total_hour12; ?></th>
							<th align="right" width="50"><? echo $total_hour13; ?></th>
							<th align="right" width="50"><? echo $total_hour14; ?></th>
							<th align="right" width="50"><? echo $total_hour15; ?></th>
							<th align="right" width="50"><? echo $total_hour16; ?></th>
							<th align="right" width="50"><? echo $total_hour17; ?></th>
							<th align="right" width="50"><? echo $total_hour18; ?></th>
							<th align="right" width="50"><? echo $total_hour19; ?></th>
							<th align="right" width="50"><? echo $total_hour20; ?></th>
							<th align="right" width="50"><? echo $total_hour21; ?></th>
							<th align="right" width="50"><? echo $total_hour22; ?></th>
							<th align="right" width="50"><? echo $total_hour23; ?></th>
							<th><? echo $total_hour24; ?></th>
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
		exit();
	}
	else if($type==2)
	{
		
		$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
		order by sewing_line_serial"); 
		foreach($lineDataArr as $lRow)
		{
			$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
			$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
			$lastSlNo=$lRow[csf('sewing_line_serial')];
		}
		
		$comapny_id=str_replace("'","",$cbo_company_id);
		
		$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name in($comapny_id) and variable_list=23 and is_deleted=0 and status_active=1");
		//echo $prod_reso_allo."eee";die;
		
		if($db_type==0)
		{
			$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id  and pr_date=$txt_date","line_start_time");	
		}//and  a.company_id=$comapny_id and shift_id=1
		else if($db_type==2)
		{
			$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and pr_date=$txt_date","line_start_time");
		}//
		
		if($min_shif_start=="")
		{
			echo "<p style='font-size:20px', align='center'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<p/>";die;
			
		}
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
		}
		
		if(str_replace("'","",$cbo_company_id)==0) $company_name=""; else $company_name="and a.serving_company in(".str_replace("'","",$cbo_company_id).")";
		if(str_replace("'","",$cbo_location_id)==0) $location=""; else $location="and a.location=".str_replace("'","",$cbo_location_id)."";
		if(str_replace("'","",$cbo_floor_id)==0) $floor=""; else $floor="and a.floor_id=".str_replace("'","",$cbo_floor_id)."";
	    if(str_replace("'","",$hidden_line_id)==0) $line=""; else $line="and a.sewing_line in(".str_replace("'","",$hidden_line_id).")";
		if(str_replace("'","",$cbo_buyer_name)==0) $buyer=""; else $buyer="and b.buyer_name in(".str_replace("'","",$cbo_buyer_name).")";
		if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date=$txt_date";
		
		if($db_type==0) $prod_start_cond="prod_start_time";
		else if($db_type==2) $prod_start_cond="TO_CHAR(prod_start_time,'HH24:MI')";
		
		$variable_start_time_arr='';
		$prod_start_time=sql_select("select $prod_start_cond as prod_start_time from variable_settings_production where company_name in($comapny_id) and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");
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
		
		$search_prod_date=change_date_format(str_replace("'","",$txt_date),'yyyy-mm-dd');
		$current_eff_min=($ex_time[0]*60)+$ex_time[1];
		
		$variable_time= explode(":",$variable_start_time_arr);
		$vari_min=($variable_time[0]*60)+$variable_time[1];
		$difa_time=explode(".",number_format(($current_eff_min-$vari_min)/60,2));//datediff("",$ctime,$variable_start_time_arr);
		$dif_time=$difa_time[0];
		$dif_hour_min=date("H:i", strtotime($dif_time));
		
		if($prod_reso_allo==1)
		{
			$prod_resource_array=array();
			$dataArray=sql_select("SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.smv_adjust, b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour, c.from_date, c.to_date, c.capacity as mc_capacity from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c where a.id=c.mst_id and a.id=b.mst_id and c.id=b.mast_dtl_id and a.company_id in($comapny_id) and pr_date=$txt_date");// and a.id=1 and c.from_date=$txt_date
			
			
			
			
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
		// print_r($prod_resource_array);die;
		//***************************************************************************************************************
		if($db_type==0)
		{
			// $country_ship_date_fld="a.country_ship_date";
			$manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
		}
		else
		{
			// $country_ship_date_fld="to_char(a.country_ship_date,'YYYY-MM-DD')";
			$manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
		}
		
		$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($manufacturing_company) and variable_list=25 and status_active=1 and is_deleted=0");
		

		//print_r($prod_resource_array);
		if($db_type==2)
		{
			$pr_date=str_replace("'","",$txt_date);
			$pr_date_old=explode("-",str_replace("'","",$txt_date));
			$month=strtoupper($pr_date_old[1]);
			$year=substr($pr_date_old[2],2);
			$pr_date=$pr_date_old[0]."-".$month."-".$year;
		}
		else if($db_type==0)
		{
			$pr_date=str_replace("'","",$txt_date);
		}
		//echo $pr_date;die; 
		$prod_start_hour="08:00";
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
		
		
		$first_hour_time=explode(":",$min_shif_start);
		$hour_line=substr($first_hour_time[0],1,1); $minutes_one=$start_time[1];
		$line_start_hour_arr[$hour_line]=$min_shif_start;
		
		for($l=$hour_line;$l<$last_hour;$l++)
		{
			$min_shif_start=add_time($min_shif_start,60);
			$line_start_hour_arr[$l+1]=substr($min_shif_start,0,5);
		}
		
		$line_start_hour_arr[$j+1]='23:59';
		
		
		
		
		$i=1; $grand_total_good=0; $grand_alter_good=0; $grand_total_reject=0;
		$html="";
		$floor_html="";
	    $check_arr=array();
		if($db_type==0)
		{
			$production_hour_subcon=" hour ";
			$sql_query="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name as buyer_name, b.style_ref_no, a.po_break_down_id, a.item_number_id, c.po_number as po_number,
				sum(d.production_qnty) as good_qnty, 
				sum(CASE WHEN production_hour ='1' THEN production_qnty else 0 END) AS good_1am,
				sum(CASE WHEN production_hour ='2' THEN production_qnty else 0 END) AS good_2am,
				sum(CASE WHEN production_hour ='3' THEN production_qnty else 0 END) AS good_3am,
				sum(CASE WHEN production_hour ='4' THEN production_qnty else 0 END) AS good_4am,
				sum(CASE WHEN production_hour ='5' THEN production_qnty else 0 END) AS good_5am,
				sum(CASE WHEN production_hour ='6' THEN production_qnty else 0 END) AS good_6am,
				sum(CASE WHEN production_hour ='7' THEN production_qnty else 0 END) AS good_7am,
				sum(CASE WHEN production_hour ='8' THEN production_qnty else 0 END) AS good_8am,
				sum(CASE WHEN production_hour ='9' THEN production_qnty else 0 END) AS good_9am,
				sum(CASE WHEN production_hour ='10' THEN production_qnty else 0 END) AS good_10am,
				sum(CASE WHEN production_hour ='11' THEN production_qnty else 0 END) AS good_11am,
				sum(CASE WHEN production_hour ='12' THEN production_qnty else 0 END) AS good_12am,
				sum(CASE WHEN production_hour ='13' THEN production_qnty else 0 END) AS good_1pm,
				sum(CASE WHEN production_hour ='14' THEN production_qnty else 0 END) AS good_2pm,
				sum(CASE WHEN production_hour ='15' THEN production_qnty else 0 END) AS good_3pm,
				sum(CASE WHEN production_hour ='16' THEN production_qnty else 0 END) AS good_4pm,
				sum(CASE WHEN production_hour ='17' THEN production_qnty else 0 END) AS good_5pm,
				sum(CASE WHEN production_hour ='18' THEN production_qnty else 0 END) AS good_6pm,
				sum(CASE WHEN production_hour ='19' THEN production_qnty else 0 END) AS good_7pm,
				sum(CASE WHEN production_hour ='20' THEN production_qnty else 0 END) AS good_8pm,
				sum(CASE WHEN production_hour ='21' THEN production_qnty else 0 END) AS good_9pm,
				sum(CASE WHEN production_hour ='22' THEN production_qnty else 0 END) AS good_10pm,
				sum(CASE WHEN production_hour ='23' THEN production_qnty else 0 END) AS good_11pm,
				sum(CASE WHEN production_hour ='24' THEN production_qnty else 0 END) AS good_12pm
				from pro_garments_production_mst a,pro_garments_production_dtls d, wo_po_details_master b, wo_po_break_down c,wo_po_color_size_breakdown e
				where  a.production_type=5 and d.production_type=5 and a.id=d.mst_id and   a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.po_break_down_id=e.po_break_down_id and d.color_size_break_down_id=e.id and b.job_no=e.job_no_mst and c.id=e.po_break_down_id and  a.status_active=1 and a.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and c.is_deleted=0 and c.status_active in(1,2,3)  and e.status_active in(1,2,3) and e.is_deleted=0 $company_name $buyer $location $floor $line $txt_date_from 
				group by a.company_id, a.location, a.floor_id, a.po_break_down_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name, b.style_ref_no, a.item_number_id, c.po_number order by a.location, a.floor_id, a.po_break_down_id";
		}
		else if($db_type==2)
		{
			$production_hour_subcon="TO_CHAR(hour,'HH24:MI')";
			$production_hour="TO_CHAR(production_hour,'HH24:MI')";
			//$txt_reporting_hour=str_replace("'","",$txt_poly_date)." ".str_replace("'","",$txt_reporting_hour);
			//$production_hour="to_date('".$production_hour."','DD MONTH YYYY HH24:MI:SS')";
			
			$sql_query="SELECT  a.company_id,a.serving_company, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name as buyer_name, b.style_ref_no,b.set_break_down, b.set_smv, a.po_break_down_id, a.item_number_id, c.po_number as po_number,
				sum(d.production_qnty) as good_qnty,f.floor_serial_no";
				$first=1;
				for($h=$hour;$h<$last_hour;$h++)
				{
					$bg=$start_hour_arr[$h];
					$end=substr(add_time($start_hour_arr[$h],60),0,5);
					$prod_hour="good_".substr($bg,0,2);
					if($first==1)
					{
						$sql_query.=", sum(CASE WHEN $production_hour<'$end' and d.production_type=5 THEN d.production_qnty else 0 END) AS $prod_hour";
					}
					else
					{
						$sql_query.=", sum(CASE WHEN $production_hour>='$bg' and $production_hour<'$end' and d.production_type=5 THEN d.production_qnty else 0 END) AS $prod_hour";
					}
					$first=$first+1;
				}
				$sql_query.=", sum(CASE WHEN $production_hour>='$start_hour_arr[$last_hour]' and $production_hour<='$start_hour_arr[24]' and d.production_type=5 THEN d.production_qnty else 0 END) AS good_23 
				
				FROM pro_garments_production_mst a,pro_garments_production_dtls d,wo_po_color_size_breakdown e, wo_po_break_down c, wo_po_details_master b
				where a.production_type=5 and  a.id=d.mst_id and d.COLOR_SIZE_BREAK_DOWN_ID=e.id and e.PO_BREAK_DOWN_ID=c.id and c.job_id=b.id 
				and a.PO_BREAK_DOWN_ID=c.id
				and d.production_type=5 and a.status_active=1 and a.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and c.is_deleted=0 and c.status_active in(1,2,3) and e.status_active in(1,2,3) and e.is_deleted=0 $company_name $buyer $location $floor $line $txt_date_from 
				group by a.company_id, a.location, a.floor_id, a.po_break_down_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name, b.style_ref_no, b.set_break_down,b.set_smv, a.item_number_id, c.po_number,c.unit_price,d.color_type_id";
				//echo $h;
		}
		//  echo $sql_query;    die;     
				   
				   // subcontract query will generate here
					
					$sql_query_subcon="SELECT  a.company_id, a.location_id as location, a.floor_id, a.prod_reso_allo, a.production_date, a.line_id as sewing_line, b.party_id as buyer_name, c.cust_style_ref as style_ref_no, c.smv as set_smv, a.order_id as po_break_down_id, a.gmts_item_id as item_number_id , c.order_no as po_number,
					sum(d.prod_qnty) as good_qnty,f.floor_serial_no";
					$first=1;
					for($h=$hour;$h<$last_hour;$h++)
					{
						$bg=$start_hour_arr[$h];
						$end=substr(add_time($start_hour_arr[$h],60),0,5);
						$prod_hour="good_".substr($bg,0,2);
						if($first==1)
						{
							$sql_query_subcon.=", sum(CASE WHEN $production_hour_subcon<='$end' and d.production_type=2 THEN d.prod_qnty else 0 END) AS $prod_hour";
						}
						else
						{
							$sql_query_subcon.=", sum(CASE WHEN $production_hour_subcon>='$bg' and $production_hour_subcon<'$end' and d.production_type=2 THEN d.prod_qnty else 0 END) AS $prod_hour";
						}
						$first=$first+1;
					}
					$sql_query_subcon.=", sum(CASE WHEN $production_hour_subcon>='$start_hour_arr[$last_hour]' and $production_hour_subcon<='$start_hour_arr[24]' and d.production_type=2 THEN d.prod_qnty else 0 END) AS good_23 from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d, subcon_ord_mst b, subcon_ord_dtls c,subcon_ord_breakdown e,lib_prod_floor f
					where a.production_type=2 and d.production_type=2 and a.id=d.dtls_id and a.order_id=c.id and c.job_no_mst=b.subcon_job and f.id=a.floor_id  and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and e.id=d.ord_color_size_id and e.order_id=c.id and a.order_id=e.order_id $company_name_subcon $location_subcon $floor $line_subcon   $txt_date_from 
					group by a.company_id, a.location_id, a.floor_id, a.order_id, a.prod_reso_allo, a.production_date, a.line_id, b.party_id, c.cust_style_ref, c.smv, a.gmts_item_id, c.order_no order by a.location_id, a.floor_id, a.order_id,f.floor_serial_no ";

		//echo $sql_query_subcon;die;
 
		$sql=sql_select($sql_query);	
		$sql_subcon=sql_select($sql_query_subcon);	
		$production_data_arr=array();$last_pro_hour=array();
		$production_po_data_arr=array();$all_style_arr=array();$style_wise_po_arr=array();
		foreach($sql as $val)
		{
			
			//$val[csf('set_smv')]=($gsdDataArr[$val[csf('item_number_id')]][$val[csf('style_ref_no')]])?$gsdDataArr[$val[csf('item_number_id')]][$val[csf('style_ref_no')]]:$val[csf('set_smv')];
			
			
			
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
			else $slNo=$lineSerialArr[$sewing_line_id];

			
			$production_data_arr2[$val[csf('po_break_down_id')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('item_number_id')]]+=$val[csf('good_qnty')];
			$production_data_arr3[$val[csf('po_break_down_id')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]=$val[csf('set_break_down')];


			$production_serial_arr[$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]]=$val[csf('sewing_line')];
			
			$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf('good_qnty')];
			$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['prod_reso_allo']=$val[csf('prod_reso_allo')]; 
			$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_break_down_id'].=$val[csf('po_break_down_id')].","; 
			
			
			if($production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_break_down']!="")
			{
				$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_break_down'].=",".$val[csf('set_break_down')]; 
			}
			else
			{
				$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_break_down']=$val[csf('set_break_down')]; 
			}

			if($production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']!="")
			{
				$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name'].=",".$val[csf('buyer_name')]; 
			}
			else
			{
				$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']=$val[csf('buyer_name')]; 
			}
			
			
			if($production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv']!="")
			{
				$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv'].=",".number_format($val[csf('set_smv')],2); 
			}
			else
			{
				$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv']=number_format($val[csf('set_smv')],2); 
			}
			
			if($production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']!="")
			{
				$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number'].=",".$val[csf('po_number')]; 
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style_ref'].=",".$val[csf('style_ref_no')]; 
			}
			else
			{
				$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']=$val[csf('po_number')];
				$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['style_ref']=$val[csf('style_ref_no')]; 
			}
			
			if($production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']!="")
			{
				$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('style_ref_no')]; 
			}
			else
			{
				$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('style_ref_no')]; 
			}
			
			$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['quantity']+=$val[csf('good_qnty')];
			$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['1am']+=$val[csf('good_01')];  
			$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['2am']+=$val[csf('good_02')];
			$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['3am']+=$val[csf('good_03')]; 
			$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['4am']+=$val[csf('good_04')];
			$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['5am']+=$val[csf('good_05')]; 
			$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['6am']+=$val[csf('good_06')];
			$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['7am']+=$val[csf('good_07')]; 
			$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['8am']+=$val[csf('good_08')];
			$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['9am']+=$val[csf('good_09')]; 
			$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['10am']+=$val[csf('good_10')];
			$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['11am']+=$val[csf('good_11')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['12am']+=$val[csf('good_00')];
			
			$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['1pm']+=$val[csf('good_13')]; 
			$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['2pm']+=$val[csf('good_14')]; 
			$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['3pm']+=$val[csf('good_15')];
			$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['4pm']+=$val[csf('good_16')];
			$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['5pm']+=$val[csf('good_17')]; 
			$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['6pm']+=$val[csf('good_18')];
			$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['7pm']+=$val[csf('good_19')]; 
			$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['8pm']+=$val[csf('good_20')]; 
			$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['9pm']+=$val[csf('good_21')]; 
			$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['10pm']+=$val[csf('good_22')]; 
			$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['11pm']+=$val[csf('good_23')]; 
			$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['12pm']+=$val[csf('good_12')]; 
			$all_po_id_arr[$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];
			
			$all_style_arr[$val[csf('style_ref_no')]]=$val[csf('style_ref_no')];
			$style_wise_po_arr[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];
		
		
				for($iii=1; $iii<=24; $iii++){
					if($iii<10){$iii='0'.$iii;}
					if(!empty($val[csf("good_".$iii)]))
					{
						$last_pro_hour[$val[csf('floor_id')].'**'.$val[csf('sewing_line')]][$iii]=$iii;
					}
				}
	
		
		
		}
		
	 // print_r($last_pro_hour);
				
		 foreach($last_pro_hour as $fl=>$ph){
			 ksort($ph);
			list($f,$l)=explode('**',$fl);
			for($ni=9; $ni<=end($ph); $ni++)
			{
				if($ni != 13){
					$production_data_arr[$f][$l]['production_hour']+=1;
				}
			}
		 }

		
		//echo "<pre>";
		//var_dump($production_data_arr2);
		
		// 
		// print_r($production_data_arr3);
		$last_pro_hour=array();
		foreach( $sql_subcon as $val)
		{
			//$val[csf('set_smv')]=($gsdDataArr[$val[csf('item_number_id')]][$val[csf('style_ref_no')]])?$gsdDataArr[$val[csf('item_number_id')]][$val[csf('style_ref_no')]]:$val[csf('set_smv')];
			
			//floor and line wise 
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
			else $slNo=$lineSerialArr[$sewing_line_id];
			$production_serial_arr[$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]]=$val[csf('sewing_line')];
			
			$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf('good_qnty')];
			$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['prod_reso_allo']=$val[csf('prod_reso_allo')]; 
			
			if($production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']!="")
			{
				$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name'].=",".$val[csf('buyer_name')]; 
			}
			else
			{
				$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']=$val[csf('buyer_name')]; 
			}
			
			
			if($production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv']!="")
			{
				$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv'].=",".number_format($val[csf('set_smv')],2); 
			}
			else
			{
				$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv']=number_format($val[csf('set_smv')],2); 
			}
			
			if($production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv_subcon']!="")
			{
				$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv_subcon'].=",".number_format($val[csf('set_smv')],2); 
			}
			else
			{
				$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv_subcon']=number_format($val[csf('set_smv')],2); 
			}
			
			if($production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']!="")
			{
				$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number'].=",".$val[csf('po_number')]; 
				$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['style_ref'].=",".$val[csf('style_ref_no')]; 
			}
			else
			{
				$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']=$val[csf('po_number')]; 
				$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['style_ref']=$val[csf('style_ref_no')]; 
			}
			
			if($production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']!="")
			{
				$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')].'**'."subcon**".$val[csf('style_ref_no')]; 
			}
			else
			{
				$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')].'**'."subcon**".$val[csf('style_ref_no')]; 
			}

			$subcon_smv_calc[$val[csf('floor_id')]][$val[csf('sewing_line')]]+=$val[csf('set_smv')]*$val[csf('good_qnty')];

			$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['quantity']+=$val[csf('good_qnty')];
			$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['1am']+=$val[csf('good_01')];  
			$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['2am']+=$val[csf('good_02')];
			$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['3am']+=$val[csf('good_03')]; 
			$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['4am']+=$val[csf('good_04')];
			$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['5am']+=$val[csf('good_05')]; 
			$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['6am']+=$val[csf('good_06')];
			$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['7am']+=$val[csf('good_07')]; 
			$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['8am']+=$val[csf('good_08')];
			$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['9am']+=$val[csf('good_09')]; 
			$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['10am']+=$val[csf('good_10')];
			$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['11am']+=$val[csf('good_11')]; 
			$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['12am']+=$val[csf('good_00')];
			
			$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['1pm']+=$val[csf('good_13')]; 
			$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['2pm']+=$val[csf('good_14')]; 
			$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['3pm']+=$val[csf('good_15')];
			$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['4pm']+=$val[csf('good_16')];
			$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['5pm']+=$val[csf('good_17')]; 
			$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['6pm']+=$val[csf('good_18')];
			$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['7pm']+=$val[csf('good_19')]; 
			$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['8pm']+=$val[csf('good_20')]; 
			$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['9pm']+=$val[csf('good_21')]; 
			$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['10pm']+=$val[csf('good_22')]; 
			$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['11pm']+=$val[csf('good_23')]; 
			$production_data_arr[$val[csf('company_id')]][$val[csf('floor_serial_no')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['12pm']+=$val[csf('good_12')]; 

			$all_po_id_arr_sub[$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];	
			
			$all_style_arr[$val[csf('style_ref_no')]]=$val[csf('style_ref_no')];
			$style_wise_po_arr[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];

				for($iii=1; $iii<=24; $iii++){
					if($iii<10){$iii='0'.$iii;}
					if(!empty($val[csf("good_".$iii)]))
					{
						$last_pro_hour[$val[csf('floor_id')].'**'.$val[csf('sewing_line')]][$iii]=$iii;
					}
				}
			
					 

		}
		
				
		 foreach($last_pro_hour as $fl=>$ph){
			 ksort($ph);
			list($f,$l)=explode('**',$fl);
			for($ni=9; $ni<=end($ph); $ni++)
			{
				if($ni != 13){
					$production_data_arr[$f][$l]['production_hour']+=1;
				}
			}
		 }

		
		
		
		$all_po_ids=implode(",", array_unique($all_po_id_arr));
 		$all_po_ids_subcon=implode(",", array_unique($all_po_id_arr_sub));
		$subcon_days_run_sql=sql_select("SELECT min(production_date) as min_date,order_id,line_id from subcon_gmts_prod_dtls where order_id in ($all_po_ids_subcon) and production_type=2 group by order_id,line_id");
		foreach ($subcon_days_run_sql as $key => $value) 
		{
			 $subcon_days_run_arr[$value[csf("order_id")]][$value[csf("line_id")]]=$value[csf("min_date")];
		}
		$days_run_sqls=sql_select("SELECT min(production_date) as min_date,po_break_down_id,sewing_line from pro_garments_production_mst where po_break_down_id in($all_po_ids)  and production_type=5 group by po_break_down_id,sewing_line");
 		foreach ($days_run_sqls as $key => $value) 
		{
			 $days_run_main_arr[$value[csf("po_break_down_id")]][$value[csf("sewing_line")]]=$value[csf("min_date")];
		}

		/* echo "<pre>";
		print_r($days_run_main_arr);
		echo "</pre>";die;*/
		
		if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
		if($smv_source==3)
		{
			/*$sql_item="select b.id, a.sam_style, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3)  and b.id in($all_po_ids)";
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
			}*/
			
			$style_nos=implode("','",$all_style_arr);
			$gsdSql="select a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID  from PPL_GSD_ENTRY_MST a where a.APPLICABLE_PERIOD <= $txt_date and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and a.BULLETIN_TYPE in(3,4) and A.STYLE_REF in('".$style_nos."') and a.APPROVED=1 
			group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID
			 ORDER BY  a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC";
			   //echo $gsdSql; 
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
			$sql_item="select b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3) and b.id in($all_po_ids)"; //echo $sql_item;die;
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
	 
		
		// var_dump($production_data_arr[126][199]['8am']);die;
		
		 //print_r($item_smv_array);
		
		unset($sql);		
		$before_8_am=$production_hour1=$production_hour2=$production_hour3=$production_hour4=$production_hour5=$production_hour6=$production_hour7=$production_hour8=0;   
	    $production_hour9=$production_hour10=$production_hour11=$production_hour12=$production_hour13=$production_hour14=$production_hour15=$production_hour16=$avable_min=0;
		$production_hour17=$production_hour18=$production_hour19=$production_hour20=$production_hour21=$production_hour22=$production_hour23=$production_hour24=$today_product=0;
		$floor_hour1=$floor_hour2=$floor_hour3=$floor_hour4=$floor_hour5=$floor_hour6==$floor_hour7=$floor_hour8=$floor_before_9am=0;  $floor_name="";   
	    $floor_hour9=$floor_hour10=$floor_hour11=$floor_hour12=$floor_hour13=$floor_hour14=$floor_hour15=$floor_hour16=$floor_man_power=0;
		$floor_hour17=$floor_hour18=$floor_hour19=$floor_hour20=$floor_hour21=$floor_hour22=$floor_hour23=$floor_hour24=$floor_operator=$floor_produc_min=0;
		$floor_smv=$floor_row=$floor_helper=$floor_tgt_h=$floor_days_run=$floor_before_8_am=$floor_working_hour=$floor_ttl_tgt=$floor_today_product=$floor_avale_minute=0;
		$total_hour1=$total_hour2=$total_hour3=$total_hour4=$total_hour5=$total_hour6==$total_hour7=$total_hour8=$total_before_8am=$total_operator=$total_helper=$gnd_hit_rate=0;   
	    $total_hour9=$total_hour10=$total_hour11=$total_hour12=$total_hour13=$total_hour14=$total_hour15=$total_hour16=$total_smv=$total_terget=$grand_total_product=$gnd_line_effi=0;
		$total_hour17=$total_hour18=$total_hour19=$total_hour20=$total_hour21=$total_hour22=$total_hour23=$total_hour24=$total_man_power=$gnd_avable_min=$gnd_product_min=0;
		$item_smv=$item_smv_total=$line_efficiency=$days_run=$total_working_hour=$gnd_total_tgt_h=$total_capacity=0;
		$j=1;
		ob_start();
		$line_number_check_arr=array();
		$smv_for_item="";
		 foreach ($production_data_arr as $com_key => $com_value) 
		 {
			foreach ($com_value as $fl_key => $fl_value) 
			{	ksort($fl_value);
				foreach($fl_value as $f_id=>$fname)
				{
					ksort($fname);
					foreach($fname as $l_id=>$ldata)
					{
						if($i!=1)
						{
							if(!in_array($f_id, $check_arr))
							{
								if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$html.='<tr  bgcolor="#B6B6B6">
									<td width="40">&nbsp;</td>
									<td width="80">&nbsp;</td>
									<td width="80">&nbsp;</td>
									<td width="80">&nbsp;</td>
									<td width="140">&nbsp;</td>
									<td width="140">&nbsp;</td>
									<td width="120" align="right">Sub Total: </td>
									<td align="right" width="60">&nbsp;</td>
									<td align="right" width="70">'.$floor_operator.'</td>
									<td align="right" width="50">'.$floor_helper.'</td>
									<td align="right" width="60">'.$floor_man_power.'</td>
									<td align="right" width="70">'.$floor_tgt_h.'</td>
									<td align="right" width="60">'.$floor_days_run.'</td>
									<td align="right" width="70">'.$floor_capacity.'</td>
									<td align="right" width="60">'.number_format($floor_working_hour,2).'</td>
									<td align="right" width="60">'.$floor_working_hourss.'</td>
									<td align="right" width="60"></td>
									<td align="right" width="80">'.number_format($floor_ttl_tgt,0).'</td>
									<td align="right" width="80">'.$floor_today_product.'</td>
									<td align="right" width="80">'.number_format(($floor_today_product-$floor_ttl_tgt),0).'</td>
									<td align="right" width="100">'.number_format($floor_avale_minute,2).'</td>
									<td align="right" width="100">'.number_format($floor_produc_min,2).'</td>
									<td align="right" width="60">'.number_format(($floor_today_product/$floor_ttl_tgt)*100,2).'%</td>
									<td align="right" width="90">'.number_format($floor_efficency,2).'%</td>
									<td align="right" width="50">'.$floor_hour8.'</td>
									<td align="right" width="50">'.$floor_hour9.'</td>
									<td align="right" width="50">'.$floor_hour10.'</td>
									<td align="right" width="50">'.$floor_hour11.'</td>
									<td align="right" width="50">'.$floor_hour12.'</td>
									<td align="right" width="50">'.$floor_hour13.'</td>
									<td align="right" width="50">'.$floor_hour14.'</td>
									<td align="right" width="50">'.$floor_hour15.'</td>
									<td align="right" width="50">'.$floor_hour16.'</td>
									<td align="right" width="50">'.$floor_hour17.'</td>
									<td align="right" width="50">'.$floor_hour18.'</td>
									<td align="right" width="50">'.$floor_hour19.'</td>
									<td align="right" width="50">'.$floor_hour20.'</td>
									<td align="right" width="50">'.$floor_hour21.'</td>
									<td align="right" width="50">'.$floor_hour22.'</td>
									<td align="right" width="50">'.$floor_hour23.'</td>
									<td align="right">'.$floor_hour24.'</td>
								</tr>';
								
								$floor_html.='<tbody>';
								$floor_html.="<tr bgcolor='$bgcolor' onclick=change_color('floor_$j','$bgcolor') id=floor_$j>";
								$floor_html.='<td width="40">'.$j.'&nbsp;</td>
									<td width="80" align="center">'.$floor_name.'&nbsp; </td>
									<td width="70" align="right">'.$floor_tgt_h.'</td>
									<td width="70" align="right">'.$floor_capacity.'</td>
									<td width="60" align="right">'.$floor_man_power.'</td>
									<td width="70" align="right">'.$floor_operator.'</td>
									<td width="50" align="right">'.$floor_helper.'</td>
									<td align="right" width="60">'.number_format($floor_working_hour,2).'</td>
									<td align="right" width="80">'.number_format($floor_ttl_tgt,0).'</td>
									<td align="right" width="80">'.number_format($floor_today_product,0).'</td>
									<td align="right" width="80">'.number_format(($floor_today_product-$floor_ttl_tgt),0).'</td>
									<td align="right" width="100">'.$floor_avale_minute.'</td>
									<td align="right" width="100">'.number_format($floor_produc_min,2).'</td>
									<td align="right" width="90">'.number_format(($floor_today_product/$floor_ttl_tgt)*100,2).'%</td>
									<td align="right" width="90">'.number_format($floor_efficency,2).'%</td>
									
									<td align="right" width="50">'.$floor_hour8.'</td>
									<td align="right" width="50">'.$floor_hour9.'</td>
									<td align="right" width="50">'.$floor_hour10.'</td>
									<td align="right" width="50">'.$floor_hour11.'</td>
									<td align="right" width="50">'.$floor_hour12.'</td>
									<td align="right" width="50">'.$floor_hour13.'</td>
									<td align="right" width="50">'.$floor_hour14.'</td>
									<td align="right" width="50">'.$floor_hour15.'</td>
									<td align="right" width="50">'.$floor_hour16.'</td>
									<td align="right" width="50">'.$floor_hour17.'</td>
									<td align="right" width="50">'.$floor_hour18.'</td>
									<td align="right" width="50">'.$floor_hour19.'</td>
									<td align="right" width="50">'.$floor_hour20.'</td>
									<td align="right" width="50">'.$floor_hour21.'</td>
									<td align="right" width="50">'.$floor_hour22.'</td>
									<td align="right" width="50">'.$floor_hour23.'</td>
									<td align="right">'. $floor_hour24.'</td>
								</tr>';
								$floor_name=""; $floor_smv=0; $floor_row=0; $floor_operator=0; $floor_helper=0; $floor_tgt_h=0; $floor_man_power=0; $floor_days_run=0; $floor_before_9_am=0;
								$floor_hour8=0;$floor_hour9=0; $floor_hour10=0; $floor_hour11=0; $floor_hour12=0; $floor_hour13=0; $floor_hour14=0; $floor_hour15=0; $floor_hour16=0; $floor_hour17=0; $floor_hour18=0; $floor_hour19=0; $floor_hour20=0; $floor_hour21=0; $floor_hour22=0; $floor_hour23=0; $floor_hour24=0;
								$floor_working_hour=0; $floor_ttl_tgt=0; $floor_today_product=0; $floor_avale_minute=0; $floor_produc_min=0; $floor_efficency=0; $floor_man_power=0; $floor_capacity=0;
								$j++;
							}
						}
						$floor_row++;	
					
						$po_number=array_unique(explode(',',$row[csf('po_number')]));
						$germents_item=array_unique(explode('****',$ldata['item_number_id']));
						$buyer_neme_all=array_unique(explode(',',$ldata['buyer_name']));
						// $set_smv_all=implode(',',array_unique(explode(',',$ldata['set_smv'])));
						$style_ref=implode(',',array_unique(explode(',',$ldata['style_ref'])));
						$po_break_down_id=implode(',',array_unique(explode(',',$ldata['po_break_down_id'])));
					
						//print_r($germents_item);
						$set_smv_all="";
						$itm_smv_arr = array();
						$chk_smv_array = array();
						foreach ($germents_item as $value) 
						{
							$po_item = explode("**", $value);
							$po_id = $po_item[0];
							$item_id = $po_item[1];
							$style = $po_item[2];
							// echo "<br>";
							//echo $po_id;
							//echo "<br>";
							// echo $item_id;
							// echo "<br>";
							// echo $f_id;
							// echo "<br>";
							// echo $l_id;
							// echo "<br>";
							// echo $production_data_arr2[33866][92][121][139]."<br>";
							// echo $production_data_arr2[$po_id][$val[csf('floor_id')]][$val[csf('sewing_line')]][$item_id]."<br>";
							if($production_data_arr2[$po_id][$f_id][$l_id][$item_id] !="")
							{
								$break_down_smv = $production_data_arr3[$po_id][$f_id][$l_id];
								$break_down_smv_arr = explode("__", $break_down_smv);
								// print_r($break_down_smv_arr);die();
								
								
								foreach ($break_down_smv_arr as $smv) 
								{
									$set_smv = explode("_", $smv);
									//echo $set_smv[2]."<br>";
									if($item_id==$set_smv[0])
									{
										if(!isset($chk_smv_array[$style][$item_id]))
										{
											$set_smv[2]=($item_smv_array[$po_id][$item_id])?$item_smv_array[$po_id][$item_id]:$set_smv[2];
											
											$itm_smv_arr[$po_id][$f_id][$l_id]=$set_smv[2];
										
											$chk_smv_array[$style][$item_id] = $set_smv[2];
										}
										
									}
									
								}

							}	
						}
						$po_break_down_id = explode(",", $po_break_down_id);
						foreach (array_filter($po_break_down_id) as $po) 
						{
							if(!empty($itm_smv_arr[$po][$f_id][$l_id])){
								$set_smv_all .= number_format($itm_smv_arr[$po][$f_id][$l_id],2).",";
							}
						}
						
						$subcontact_smvs = explode(",", $ldata['set_smv_subcon']);
						foreach (array_filter($subcontact_smvs) as $set_smv_subcon) 
						{
							$set_smv_all .= $set_smv_subcon.",";
						}
						// print_r($po_break_down_id);
						//echo $set_smv_all;

						$buyer_name="";
						foreach($buyer_neme_all as $buy)
						{
							if($buyer_name!='') $buyer_name.=',';
							$buyer_name.=$buyerArr[$buy];
						}
						$chk_item_array = array();$garment_item_id_arr = array();
						$garment_itemname=''; $item_smv=""; $smv_for_item=""; $produce_minit=""; $order_no_total="";$days_run="";
						foreach($germents_item as $g_val)
						{
							$po_garment_item=explode('**',$g_val);
							if($po_garment_item[2]=="subcon" )
								{  
									//$day_run_sql=sql_select("");
									
									$sewing_day=$subcon_days_run_arr[$po_garment_item[0]][$l_id];

								}
								else
								{  
									//$day_run_sql=sql_select("");
									$sewing_day=$days_run_main_arr[$po_garment_item[0]][$l_id];
								}
								//$sewing_day="";
								//$sewing_day=$day_run_sql[0][csf('min_date')];
								if($sewing_day!="")
								{ 
									if($days_run=="")
									{  
										$days_run .=datediff("d",$sewing_day,$pr_date);
									}
									else
									{
										
										$days_run .=', '.datediff("d",$sewing_day,$pr_date);
									}
									
								}
							
							
							if(!isset($chk_item_array[$style_ref][$po_garment_item[1]]))
							{	
								if($garment_itemname!=''){$garment_itemname.=',';}
									$garment_itemname.=$garments_item[$po_garment_item[1]];
									$garment_item_id_arr[$po_garment_item[1]]=$po_garment_item[1];
									
							}
							$chk_item_array[$style_ref][$po_garment_item[1]] = $po_garment_item[1];
							
							
							if($item_smv!='') $item_smv.='/';
								$item_smv.=$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
								
							if($order_no_total!="") $order_no_total.=",";
								$order_no_total.=$po_garment_item[0];
							
							if($smv_for_item!="") 
								$smv_for_item.="****".$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
							else
								$smv_for_item=$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];	
							
							
							if($temp_data_arr[$f_id][$l_id][$po_garment_item[0]]==''){
							
								$produce_minit+=$production_po_data_arr[$f_id][$l_id][$po_garment_item[0]]*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
								$temp_data_arr[$f_id][$l_id][$po_garment_item[0]]=1;
							}
							
							//print_r($production_po_data_arr[19][153])."====";
							
							//$produce_minit+=$production_po_data_arr[$f_id][$l_id][$po_garment_item[0]];
						}
					$produce_minit+=$subcon_smv_calc[$f_id][$l_id];
						
					
					
						
						
						
						/*$day_run_sql=sql_select("select min(production_date) as min_date from pro_garments_production_mst where po_break_down_id in(".$order_no_total.")  and production_type=4");
						foreach($day_run_sql as $row_run)
						{
							$sewing_day=$row_run[csf('min_date')];
						}
					
						if($sewing_day!="")
						{
							$days_run= $diff=datediff("d",$sewing_day,$pr_date);
						}
						else $days_run=0;*/
					
						$sewing_line='';
						if($ldata['prod_reso_allo']==1)
						{
							$line_number=explode(",",$prod_reso_arr[$l_id]);
							foreach($line_number as $val)
							{
								if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
							}
						}
						else $sewing_line=$lineArr[$l_id];
					
						$total_eff_hour=0;
						$production_hour1=$ldata['1am']; if($production_hour1!=0) $total_eff_hour+=1;
						$production_hour2=$ldata['2am']; if($production_hour2!=0) $total_eff_hour+=1;
						$production_hour3=$ldata['3am']; if($production_hour3!=0) $total_eff_hour+=1;
						$production_hour4=$ldata['4am']; if($production_hour4!=0) $total_eff_hour+=1;
						$production_hour5=$ldata['5am']; if($production_hour5!=0) $total_eff_hour+=1;
						$production_hour6=$ldata['6am']; if($production_hour6!=0) $total_eff_hour+=1;
						$production_hour7=$ldata['7am']; if($production_hour7!=0) $total_eff_hour+=1;
						$production_hour8=$ldata['8am']; if($production_hour8!=0) $total_eff_hour+=1;
						$production_hour9=$ldata['9am']; if($production_hour9!=0) $total_eff_hour+=1;
						$production_hour10=$ldata['10am']; if($production_hour10!=0) $total_eff_hour+=1;
						$production_hour11=$ldata['11am']; if($production_hour11!=0) $total_eff_hour+=1;
						$production_hour12=$ldata['12pm']; if($production_hour12!=0) $total_eff_hour+=1;
						$production_hour13=$ldata['1pm']; if($production_hour13!=0) $total_eff_hour+=1;
						$production_hour14=$ldata['2pm']; if($production_hour14!=0) $total_eff_hour+=1;
						$production_hour15=$ldata['3pm']; if($production_hour15!=0) $total_eff_hour+=1;
						$production_hour16=$ldata['4pm']; if($production_hour16!=0) $total_eff_hour+=1;
						$production_hour17=$ldata['5pm']; if($production_hour17!=0) $total_eff_hour+=1;
						$production_hour18=$ldata['6pm']; if($production_hour18!=0) $total_eff_hour+=1; 
						$production_hour19=$ldata['7pm']; if($production_hour19!=0) $total_eff_hour+=1;
						$production_hour20=$ldata['8pm']; if($production_hour20!=0) $total_eff_hour+=1;
						$production_hour21=$ldata['9pm']; if($production_hour21!=0) $total_eff_hour+=1;
						$production_hour22=$ldata['10pm']; if($production_hour22!=0) $total_eff_hour+=1;
						$production_hour23=$ldata['11pm']; if($production_hour23!=0) $total_eff_hour+=1;
						$production_hour24=$ldata['12am']; if($production_hour24!=0) $total_eff_hour+=1;
						$production_hour_all=$ldata['quantity']; if($production_hour24!=0) $total_eff_hour+=1;
					
					
						if($total_eff_hour>$prod_resource_array[$l_id][$pr_date]['working_hour'])
						{
							$total_eff_hour=$prod_resource_array[$l_id][$pr_date]['working_hour'];
						}
						$before_8_am=$production_hour1+$production_hour2+$production_hour3+$production_hour4+$production_hour5+$production_hour6+$production_hour7+$production_hour8;//$before_8_am+
						$today_product=$production_hour9+$production_hour10+$production_hour11+$production_hour12+$production_hour13+$production_hour14+$production_hour15+$production_hour16+$production_hour17+$production_hour18+$production_hour19+$production_hour20+$production_hour21+$production_hour22+$production_hour23+$production_hour24;
						$today_product=$production_hour_all;
						
						//$today_product=$today_product+$production_hour8;
						
						/*if($sewing_day!="")
						{
							$days_run= $diff=datediff("d",$sewing_day,$pr_date);
						}
						else  $days_run=0;*/
						
						$current_wo_time=0;
						if($current_date==$search_prod_date)
						{
							$prod_wo_hour=$prod_resource_array[$l_id][$pr_date]['working_hour'];
							
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
							$current_wo_time=$prod_resource_array[$l_id][$pr_date]['working_hour'];
							$cla_cur_time=$prod_resource_array[$l_id][$pr_date]['working_hour'];
						}
						
						
						//$avable_min=($prod_resource_array[$l_id][$pr_date]['man_power'])*$prod_resource_array[$l_id][$pr_date]['working_hour']*60;
						//******************************* line effiecency****************************************************************************['']
						$smv_adjustmet_type=$prod_resource_array[$l_id][$pr_date]['smv_adjust_type'];
						
						$eff_target=($prod_resource_array[$l_id][$pr_date]['terget_hour']*$prod_resource_array[$l_id][$pr_date]['working_hour']);
						
						/*if($total_eff_hour>=$prod_resource_array[$l_id][$pr_date]['working_hour'])
						{*/	$total_adjustment=0;
							if(str_replace("'","",$smv_adjustmet_type)==1)
							{ 
								$total_adjustment=$prod_resource_array[$l_id][$pr_date]['smv_adjust'];
							}
							else if(str_replace("'","",$smv_adjustmet_type)==2)
							{
								$total_adjustment=($prod_resource_array[$l_id][$pr_date]['smv_adjust'])*(-1);
							}
						//}
						
						$efficiency_min=$total_adjustment+($prod_resource_array[$l_id][$pr_date]['man_power'])*$cla_cur_time*60;
					
					
					//echo $total_adjustment.'+('.$prod_resource_array[$l_id][$pr_date]['man_power'].')*'.$cla_cur_time.'*60#';
					
					
						$line_efficiency=(($produce_minit)*100)/$efficiency_min;
						//********************************* calclution floor total ****************************************************$pr_date],$sewing_day
						$floor_name=$floorArr[$f_id];	
						$floor_hour24+=$production_hour24;
						$floor_hour8+=$production_hour8;
						$floor_hour9+=$production_hour9;
						$floor_hour10+=$production_hour10;
						$floor_hour11+=$production_hour11;
						$floor_hour12+=$production_hour12; 
						$floor_hour13+=$production_hour13; 
						$floor_hour14+=$production_hour14;
						$floor_hour15+=$production_hour15;
						$floor_hour16+=$production_hour16;
						$floor_hour17+=$production_hour17;
						$floor_hour18+=$production_hour18;
						$floor_hour19+=$production_hour19; 
						$floor_hour20+=$production_hour20;
						$floor_hour21+=$production_hour21;
						$floor_hour22+=$production_hour22;
						$floor_hour23+=$production_hour23; 
						$floor_before_8_am+=$before_8_am;
						$floor_smv+=$item_smv;
						$floor_capacity+=$prod_resource_array[$l_id][$pr_date]['capacity'];
						$floor_operator+=$prod_resource_array[$l_id][$pr_date]['operator'];
						$floor_helper+=$prod_resource_array[$l_id][$pr_date]['helper'];
						$floor_tgt_h+=$prod_resource_array[$l_id][$pr_date]['terget_hour'];	
						$floor_days_run+=$days_run;
						$floor_working_hour+=$prod_resource_array[$l_id][$pr_date]['working_hour']; 
						$floor_ttl_tgt+=$eff_target;
						$floor_today_product+=$today_product;
						$floor_avale_minute+=$efficiency_min;
						$floor_produc_min+=$produce_minit; 
						$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
						$floor_man_power+=$prod_resource_array[$l_id][$pr_date]['man_power'];
						
						//echo $floor_produc_min .'/'.$floor_avale_minute."";
						
						//**************************** calclution total ********************************************************************
						$total_hour24+=$production_hour24;
						$total_hour8+=$production_hour8;
						$total_hour9+=$production_hour9;
						$total_hour10+=$production_hour10;
						$total_hour11+=$production_hour11;
						$total_hour12+=$production_hour12; 
						$total_hour13+=$production_hour13;
						$total_hour14+=$production_hour14;
						$total_hour15+=$production_hour15;
						$total_hour16+=$production_hour16;
						$total_hour17+=$production_hour17;
						$total_hour18+=$production_hour18;
						$total_hour19+=$production_hour19; 
						$total_hour20+=$production_hour20;
						$total_hour21+=$production_hour21;
						$total_hour22+=$production_hour22;
						$total_hour23+=$production_hour923; 
						$total_before_8am+=$before_8_am;
						$total_capacity+=$prod_resource_array[$l_id][$pr_date]['capacity'];
						$gnd_total_tgt_h+=$prod_resource_array[$l_id][$pr_date]['terget_hour'];	
						$total_working_hour+=$prod_resource_array[$l_id][$pr_date]['working_hour']; 
						$total_operator+=$prod_resource_array[$l_id][$pr_date]['operator'];
						$total_helper+=$prod_resource_array[$l_id][$pr_date]['helper'];
						$total_man_power+=$prod_resource_array[$l_id][$pr_date]['man_power'];
						//$total_smv+=$item_smv;
						$total_terget+=$eff_target;
						$grand_total_product+=$today_product;
						$gnd_avable_min+=$efficiency_min;
						$gnd_product_min+=$produce_minit; 
						//$gnd_hit_rate=($grand_total_product/$total_terget)*100;
						//$gnd_line_effi=($gnd_product_min/$gnd_avable_min)*100;
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$html.='<tbody>';
						$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
						$html.='<td width="40">'.$i.'&nbsp;</td>
									<td width="80">'.$floor_name.'&nbsp; </td>
									<td align="center" width="80">'.$sewing_line.'&nbsp; </td>
									<td width="80"><p>'.$buyer_name.'&nbsp;</p></td>
									<td width="140"><p>'.$style_ref.'&nbsp;</p></td>
									<td width="140"><p>'.$ldata['po_number'].'&nbsp;</p></td>
									<td width="120" title="'.$item_id.'"><p>'.$garment_itemname.'&nbsp;<p/> </td>
									<td align="center" width="60"><p>'.chop($set_smv_all,',').'</p></td>
									<td align="right" width="70">'.$prod_resource_array[$l_id][$pr_date]['operator'].'</td>
									<td align="right" width="50">'.$prod_resource_array[$l_id][$pr_date]['helper'].'</td>
									<td align="right" width="60">'.$prod_resource_array[$l_id][$pr_date]['man_power'].'</td>
									<td align="right" width="70">'.$prod_resource_array[$l_id][$pr_date]['terget_hour'].'</td>
									<td align="right" width="60"><p>'.$days_run.'</p></td>
									<td align="right" width="70">'.$prod_resource_array[$l_id][$pr_date]['capacity'].'</td>
									<td align="right" width="60">'.$prod_resource_array[$l_id][$pr_date]['working_hour'].'</td>
									<td align="right" width="60">'.$cla_cur_time.'</td>
									<td align="right" width="60">'.$production_data_arr[$f_id][$l_id]['production_hour'].'</td>
									<td align="right" width="80">'.number_format($eff_target,0).'</td>
									
									<td align="right" width="80"><a href="##" onclick="openmypage('."'".$cbo_company_id."'".",'".$order_no_total."',".$f_id.",".$l_id.",'tot_prod','".$smv_for_item."',".$txt_date.',5,'."'".implode(',',$garment_item_id_arr)."'".')">'.$today_product.'</a></td>
									
									<td align="right" width="80">'.number_format(($today_product-$eff_target),0).'</td>
									<td align="right" width="100">'.number_format($efficiency_min,2).'</td>
									<td align="right" width="100"><a href="##" onclick="openmypage('."'".$cbo_company_id."'".",'".$order_no_total."',".$f_id.",".$l_id.",'tot_prod','".$smv_for_item."',".$txt_date.',5,'."'".implode(',',$garment_item_id_arr)."'".')">'.number_format($produce_minit,2).'</a></td>
									
									<td align="right" width="60">'.number_format(($today_product/$eff_target)*100,2).'%</td>
									<td align="right" width="90">'.number_format($line_efficiency,2). '%</td>
									
									<td align="right" width="50">'.$production_hour8.'</td>
									<td align="right" width="50">'.$production_hour9.'</td>
									<td align="right" width="50">'.$production_hour10.'</td>
									<td align="right" width="50">'.$production_hour11.'</td>
									<td align="right" width="50">'.$production_hour12.'</td>
									<td align="right" width="50">'.$production_hour13.'</td>
									<td align="right" width="50">'.$production_hour14.'</td>
									<td align="right" width="50">'.$production_hour15.'</td>
									<td align="right" width="50">'.$production_hour16.'</td>
									<td align="right" width="50">'.$production_hour17.'</td>
									<td align="right" width="50">'.$production_hour18.'</td>
									<td align="right" width="50">'.$production_hour19.'</td>
									<td align="right" width="50">'.$production_hour20.'</td>
									<td align="right" width="50">'.$production_hour21.'</td>
									<td align="right" width="50">'.$production_hour22.'</td>
									<td align="right" width="50">'.$production_hour23.'</td>
									<td align="right" >'.$production_hour24.'</td>
								</tr>
							</tbody>';
						$i++;
						$check_arr[]=$f_id;
					}
				}
			}	
		}	
		$html.='<tr  bgcolor="#B6B6B6">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td align="right">Sub Total: </td>
			<td align="right">&nbsp;</td>
			<td align="right">'.$floor_operator.'</td>
			<td align="right">'.$floor_helper.'</td>
			<td align="right">'.$floor_man_power.'</td>
			<td align="right">'.$floor_tgt_h.'</td>
			<td align="right">'.$floor_days_run.'</td>
			<td align="right">&nbsp;</td>
			<td align="right">'.number_format($floor_working_hour,2).'</td>
			<td align="right">&nbsp;</td>
			<td align="right">&nbsp;</td>
			<td align="right" >'.number_format($floor_ttl_tgt,0).'</td>
			<td align="right">'.$floor_today_product.'</td>
			<td align="right">'.number_format(($floor_today_product-$floor_ttl_tgt),0).'</td>
			<td align="right">'.number_format($floor_avale_minute,2).'</td>
			<td align="right">'.number_format($floor_produc_min,2).'</td>
			<td align="right">'.number_format(($floor_today_product/$floor_ttl_tgt)*100,2).'%</td>
			<td align="right">'.number_format($floor_efficency,2).'%</td>
			
			<td align="right">'.$floor_hour8.'</td>
			<td align="right">'.$floor_hour9.'</td>
			<td align="right">'.$floor_hour10.'</td>
			<td align="right">'.$floor_hour11.'</td>
			<td align="right">'.$floor_hour12.'</td>
			<td align="right">'.$floor_hour13.'</td>
			<td align="right">'.$floor_hour14.'</td>
			<td align="right">'.$floor_hour15.'</td>
			<td align="right">'.$floor_hour16.'</td>
			<td align="right">'.$floor_hour17.'</td>
			<td align="right">'.$floor_hour18.'</td>
			<td align="right">'.$floor_hour19.'</td>
			<td align="right">'.$floor_hour20.'</td>
			<td align="right">'.$floor_hour21.'</td>
			<td align="right">'.$floor_hour22.'</td>
			<td align="right">'.$floor_hour23.'</td>
			<td align="right">'.$floor_hour24.'</td>
		</tr>';
		
		$floor_html.="<tr bgcolor='$bgcolor' onclick=change_color('floor_$j','$bgcolor') id=floor_$j>";
		$floor_html.='<td width="40">'.$j.'&nbsp;</td>
			<td width="80" align="center">'.$floor_name.'&nbsp; </td>
			<td width="70" align="right">'.$floor_tgt_h.'</td>
			<td width="70" align="right">'.$floor_capacity.'</td>
			<td width="60" align="right">'.$floor_man_power.'</td>
			<td width="70" align="right">'.$floor_operator.'</td>
			<td width="50" align="right">'.$floor_helper.'</td>
			<td align="right" width="60">'.number_format($floor_working_hour,2).'</td>
			<td align="right" width="80">'.number_format($floor_ttl_tgt,0).'</td>
			<td align="right" width="80">'.number_format($floor_today_product,0).'</td>
			<td align="right" width="80">'.number_format(($floor_today_product-$floor_ttl_tgt),0).'</td>
			<td align="right" width="100">'.$floor_avale_minute.'</td>
			<td align="right" width="100">'.number_format($floor_produc_min,2).'</td>
			<td align="right" width="90">'.number_format(($floor_today_product/$floor_ttl_tgt)*100,2).'%</td>
			<td align="right" width="90">'.number_format($floor_efficency,2).'%</td>
			
			<td align="right" width="50">'.$floor_hour8.'</td>
			<td align="right" width="50">'.$floor_hour9.'</td>
			<td align="right" width="50">'.$floor_hour10.'</td>
			<td align="right" width="50">'.$floor_hour11.'</td>
			<td align="right" width="50">'.$floor_hour12.'</td>
			<td align="right" width="50">'.$floor_hour13.'</td>
			<td align="right" width="50">'.$floor_hour14.'</td>
			<td align="right" width="50">'.$floor_hour15.'</td>
			<td align="right" width="50">'.$floor_hour16.'</td>
			<td align="right" width="50">'.$floor_hour17.'</td>
			<td align="right" width="50">'.$floor_hour18.'</td>
			<td align="right" width="50">'.$floor_hour19.'</td>
			<td align="right" width="50">'.$floor_hour20.'</td>
			<td align="right" width="50">'.$floor_hour21.'</td>
			<td align="right" width="50">'.$floor_hour22.'</td>
			<td align="right">'.$floor_hour23.'</td>
			<td align="right" width="50">'.$floor_hour24.'</td>
		</tr></tbody>';
	    $smv_for_item="";
		?>
	    <fieldset style="width:2730px">
	        <table width="2200" cellpadding="0" cellspacing="0"> 
	            <tr class="form_caption">
	            	<td colspan="28" align="center"><strong><? echo $report_title; ?></strong></td> 
	            </tr>
	            <tr class="form_caption">
	            	<td colspan="28" align="center"><strong><? echo $companyArr[$comapny_id]; ?></strong></td> 
	            </tr>
	            <tr class="form_caption">
	            	<td colspan="28" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date)) ); ?></strong></td> 
	            </tr>
	        </table>
	   		<br />
	    	<label><strong>Report Sumarry:-</strong></label> 
	        <table id="table_header_2" class="rpt_table" width="2000" cellpadding="0" cellspacing="0" border="1" rules="all">
	            <thead>
	                <tr height="50">
	                    <th width="40">SL</th>
	                    <th width="80">Floor Name</th>
	                    <th width="70">Hourly Terget</th>
	                    <th width="70">Capacity</th>
	                    <th width="60">Total Man  Power</th>
	                    <th width="70">Operator</th>
	                    <th width="50">Helper</th>
	                    <th width="60">Line Hour</th>
	                    <th width="80">Day Target</th>
	                    <th width="80">Total Prod.</th>
	                    <th width="80">Variance </th>
	                    <th width="100">SMV Available</th>
	                    <th width="100">SMV Achieved</th>
	                    <th width="90">Achievement %</th>
	                    <th width="90">Floor Eff. %</th>
	                    
	                    <th width="50" style="vertical-align:middle"><div class="block_div">8 AM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">9 AM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">10 AM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">11 AM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">12 PM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">1 PM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">2 PM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">3 PM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">4 PM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">5 PM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">6 PM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">7 PM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">8 PM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">9 PM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">10 PM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">11 PM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">12 AM</div></th>
	                </tr>
	            </thead>
	        </table>
	        <div style="width:2018px; max-height:400px; overflow-y:scroll" id="scroll_body_1">
	            <table class="rpt_table" width="2000" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
					<? echo $floor_html; ?> 
	                <tfoot>
	                    <tr>
	                        <th width="40">&nbsp;</th>
	                        <th width="80">Total</th>
	                        <th width="70"><? echo $gnd_total_tgt_h;   ?> </th>
	                        <th width="70" align="right"><? echo $total_capacity; ?> </th>
	                        <th width="60"><? echo $total_man_power; ?></th>
	                        <th width="70"><? echo $total_operator; ?></th>
	                        <th width="50"><? echo $total_helper; ?></th>
	                        <th align="right" width="60"><? echo number_format($total_working_hour,2); ?></th>
	                        <th align="right" width="80"><? echo number_format($total_terget,0); ?></th>
	                        <th align="right" width="80"><? echo number_format($grand_total_product,0); ?></th>
	                        <th align="right" width="80"><? echo number_format($grand_total_product-$total_terget,0); ?></th>
	                        <th align="right" width="100"><? echo $gnd_avable_min; ?></th>
	                        <th align="right" width="100"><? echo number_format($gnd_product_min,2); ?></th>
	                        <th align="right" width="90"><? echo number_format(($grand_total_product/$total_terget)*100,2)."%"; ?></th>
	                        <th align="center" width="90"><?  echo number_format(($gnd_product_min/$gnd_avable_min)*100,2)."%"; ?></th>
	                        <th align="right" width="50"><? echo $total_hour8; ?></th>
	                        <th align="right" width="50"><? echo $total_hour9; ?></th>
	                        <th align="right" width="50"><? echo $total_hour10; ?></th>
	                        <th align="right" width="50"><? echo $total_hour11; ?></th>
	                        <th align="right" width="50"><? echo $total_hour12; ?></th>
	                        <th align="right" width="50"><? echo $total_hour13; ?></th>
	                        <th align="right" width="50"><? echo $total_hour14; ?></th>
	                        <th align="right" width="50"><? echo $total_hour15; ?></th>
	                        <th align="right" width="50"><? echo $total_hour16; ?></th>
	                        <th align="right" width="50"><? echo $total_hour17; ?></th>
	                        <th align="right" width="50"><? echo $total_hour18; ?></th>
	                        <th align="right" width="50"><? echo $total_hour19; ?></th>
	                        <th align="right" width="50"><? echo $total_hour20; ?></th>
	                        <th align="right" width="50"><? echo $total_hour21; ?></th>
	                        <th align="right" width="50"><? echo $total_hour22; ?></th>
	                        <th align="right" width="50"><? echo $total_hour23; ?></th>
	                        <th align="right" ><? echo $total_hour24; ?></th>
	                    </tr>
	                </tfoot>
	            </table>
	        </div>
	    	</br><br/>
	        <table id="table_header_1" class="rpt_table" width="2780" cellpadding="0" cellspacing="0" border="1" rules="all">
	            <thead>
	                <tr height="50">
	                    <th width="40">SL</th>
	                    <th width="80">Floor Name</th>
	                    <th width="80">Line No</th>
	                    <th width="80">Buyer</th>
	                    <th width="140">Style Ref.</th>
	                    <th width="140">Order No</th>
	                    <th width="120">Garments Item</th>
	                    <th width="60">SMV</th>
	                    <th width="70">Operator</th>
	                    <th width="50">Helper</th>
	                    <th width="60"> Man Power</th>
	                    <th width="70">Hourly Terget</th>
	                    <th width="60">Days Run</th>
	                    <th width="70">Capacity</th>
	                    <th width="60">Working Hour</th>
	                    <th width="60">Current Hour</th>
	                    <th width="60">Production Hour</th>
	                    <th width="80">Total Target</th>
	                    <th width="80">Total Prod.</th>
	                    <th width="80">Variance pcs </th>
	                    <th width="100">Available Minutes</th>
	                    <th width="100">Produce Minutes</th>
	                    <th width="60">Target Hit rate</th>
	                    <th  width="90">Line Effi %</th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">8 AM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">9 AM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">10 AM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">11 AM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">12 PM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">1 PM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">2 PM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">3 PM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">4 PM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">5 PM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">6 PM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">7 PM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">8 PM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">9 PM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">10 PM</div></th>
	                    <th width="50" style="vertical-align:middle"><div class="block_div">11 PM</div></th>
	                    <th style="vertical-align:middle"><div class="block_div">12 AM</div></th>
	                </tr>
	            </thead>
	        </table>
	        <div style="width:2800px; max-height:400px; overflow-y:scroll" id="scroll_body_2">
	            <table class="rpt_table" width="2780" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<? echo $html;  ?>
	                <tfoot>
	                    <tr>
	                        <th width="40">&nbsp;</th>
	                        <th width="80">&nbsp;</th>
	                        <th width="80">&nbsp;</th>
	                        <th width="80">&nbsp;</th>
	                        <th width="140">&nbsp;</th>
	                        <th width="140">&nbsp;</th>
	                        <th width="120" align="right">Grand Total:</th>
	                        <th align="right" width="60"><? // echo number_format($total_smv/($i-1),2); ?>&nbsp;</th>
	                        <th align="right" width="70"><? echo $total_operator; ?></th>
	                        <th align="right" width="50"><? echo $total_helper; ?></th>
	                        <th align="right" width="60"><? echo $total_man_power; ?></th>
	                        <th align="right" width="70"><?  echo $gnd_total_tgt_h; ?></th>
	                        <th align="right" width="60"><? //echo $prod_resource_array[$row[csf('sewing_line')]][$pr_date]['man_power']; ?>&nbsp;</th>
	                        <th align="right" width="70"><? echo $total_capacity; ?></th>
	                        <th align="right" width="60"><? echo number_format($total_working_hour,2); ?></th>
	                        <th align="right" width="60">&nbsp;</th>
	                        <th align="right" width="60">&nbsp;</th>
	                        <th align="right" width="80"><? echo number_format($total_terget,0); ?></th>
	                        <th align="right" width="80"><? echo $grand_total_product; ?></th>
	                        <th align="right" width="80"><? echo number_format(($grand_total_product-$total_terget),0); ?></th>
	                        <th align="right" width="100"><? echo number_format($gnd_avable_min,2); ?></th>
	                        <th align="right" width="100"><? echo number_format($gnd_product_min,2); ?></th>
	                        <th align="right" width="60"><? echo number_format(($grand_total_product/$total_terget)*100,2)."%"; ?></th>
	                        <th align="right" width="90" ><? echo number_format(($gnd_product_min/$gnd_avable_min)*100,2)."%";?></th>
	                        <th align="right" width="50"><? echo $total_hour8; ?></th>
	                        <th align="right" width="50"><? echo $total_hour9; ?></th>
	                        <th align="right" width="50"><? echo $total_hour10; ?></th>
	                        <th align="right" width="50"><? echo $total_hour11; ?></th>
	                        <th align="right" width="50"><? echo $total_hour12; ?></th>
	                        <th align="right" width="50"><? echo $total_hour13; ?></th>
	                        <th align="right" width="50"><? echo $total_hour14; ?></th>
	                        <th align="right" width="50"><? echo $total_hour15; ?></th>
	                        <th align="right" width="50"><? echo $total_hour16; ?></th>
	                        <th align="right" width="50"><? echo $total_hour17; ?></th>
	                        <th align="right" width="50"><? echo $total_hour18; ?></th>
	                        <th align="right" width="50"><? echo $total_hour19; ?></th>
	                        <th align="right" width="50"><? echo $total_hour20; ?></th>
	                        <th align="right" width="50"><? echo $total_hour21; ?></th>
	                        <th align="right" width="50"><? echo $total_hour22; ?></th>
	                        <th align="right" width="50"><? echo $total_hour23; ?></th>
	                        <th align="right" ><? echo $total_hour24; ?></th>
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
		exit();
		
	}
	if($type==3) // Hangtag Output
	{
		$lineDataArr = sql_select("SELECT id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1 order by sewing_line_serial"); 
		foreach($lineDataArr as $lRow)
		{
			$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
			$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
			$lastSlNo=$lRow[csf('sewing_line_serial')];
		}
		
		$comapny_id=str_replace("'","",$cbo_company_id);
		
		$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name in($comapny_id) and variable_list=23 and is_deleted=0 and status_active=1");
		//echo $prod_reso_allo."eee";die;
		
		if($db_type==0)
		{
			$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id  and pr_date=$txt_date","line_start_time");	
		}//and  a.company_id=$comapny_id and shift_id=1
		else if($db_type==2)
		{
			$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and pr_date=$txt_date","line_start_time");
		}//
		
		if($min_shif_start=="")
		{
			echo "<p style='font-size:20px', align='center'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<p/>";die;
			
		}
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
		}
		
		if(str_replace("'","",$cbo_company_id)==0) $company_name=""; else $company_name="and a.serving_company in(".str_replace("'","",$cbo_company_id).")";
		if(str_replace("'","",$cbo_location_id)==0) $location=""; else $location="and a.location=".str_replace("'","",$cbo_location_id)."";
		if(str_replace("'","",$cbo_floor_id)==0) $floor=""; else $floor="and a.floor_id=".str_replace("'","",$cbo_floor_id)."";
		if(str_replace("'","",$hidden_line_id)==0) $line=""; else $line="and a.sewing_line in(".str_replace("'","",$hidden_line_id).")";
		if(str_replace("'","",$cbo_buyer_name)==0) $buyer=""; else $buyer="and b.buyer_name in(".str_replace("'","",$cbo_buyer_name).")";
		if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date=$txt_date";
		
		if($db_type==0) $prod_start_cond="prod_start_time";
		else if($db_type==2) $prod_start_cond="TO_CHAR(prod_start_time,'HH24:MI')";
		
		$variable_start_time_arr='';
		$prod_start_time=sql_select("select $prod_start_cond as prod_start_time from variable_settings_production where company_name in($cbo_company_id) and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");
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
		
		$search_prod_date=change_date_format(str_replace("'","",$txt_date),'yyyy-mm-dd');
		$current_eff_min=($ex_time[0]*60)+$ex_time[1];
		
		$variable_time= explode(":",$variable_start_time_arr);
		$vari_min=($variable_time[0]*60)+$variable_time[1];
		$difa_time=explode(".",number_format(($current_eff_min-$vari_min)/60,2));//datediff("",$ctime,$variable_start_time_arr);
		$dif_time=$difa_time[0];
		$dif_hour_min=date("H:i", strtotime($dif_time));
		
		if($prod_reso_allo==1)
		{
			$prod_resource_array=array();
			$dataArray=sql_select("select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.smv_adjust, b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour, c.from_date, c.to_date, c.capacity as mc_capacity from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c where a.id=c.mst_id and a.id=b.mst_id and c.id=b.mast_dtl_id and a.company_id in($comapny_id) and pr_date=$txt_date ");// and a.id=1 and c.from_date=$txt_date
			
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
		//print_r($prod_resource_array); die;die;
		//print_r($prod_resource_array);die;
		//*****************************************************************************************************
		if($db_type==0)
		{
			// $country_ship_date_fld="a.country_ship_date";
			$manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 and comp.id in($cbo_company_id)","company_id");
		}
		else
		{
			// $country_ship_date_fld="to_char(a.country_ship_date,'YYYY-MM-DD')";
			$manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 and comp.id in($cbo_company_id)","company_id");
		}
		
		$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($manufacturing_company) and variable_list=25 and status_active=1 and is_deleted=0");
		
		
		
		
		//echo $variable_start_time;
		//print_r($prod_resource_array);
		if($db_type==2)
		{
			$pr_date=str_replace("'","",$txt_date);
			$pr_date_old=explode("-",str_replace("'","",$txt_date));
			$month=strtoupper($pr_date_old[1]);
			$year=substr($pr_date_old[2],2);
			$pr_date=$pr_date_old[0]."-".$month."-".$year;
		}
		else if($db_type==0)
		{
			$pr_date=str_replace("'","",$txt_date);
		}
		//echo $pr_date;die; 
		$prod_start_hour="08:00";
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
		
		
		$first_hour_time=explode(":",$min_shif_start);
		$hour_line=substr($first_hour_time[0],1,1); $minutes_one=$start_time[1];
		$line_start_hour_arr[$hour_line]=$min_shif_start;
		
		for($l=$hour_line;$l<$last_hour;$l++)
		{
			$min_shif_start=add_time($min_shif_start,60);
			$line_start_hour_arr[$l+1]=substr($min_shif_start,0,5);
		}
		
		$line_start_hour_arr[$j+1]='23:59';
		
		
		
		/*$start_hour_arr=array();
		$start_hour='00:00';
		for($j=0;$j<=23;$j++)
		{
			$start_hour=add_time($start_hour,60);
			$start_hour_arr[$j+1]=substr($start_hour,0,5);
		}*/
		//echo $pc_date_time;die;
		//$start_hour_arr[$j+1]='23:59';
		//echo '<pre>';
		//print_r($start_hour_arr); die;
		$i=1; $grand_total_good=0; $grand_alter_good=0; $grand_total_reject=0;
		$html="";
		$floor_html="";
		$check_arr=array();
		if($db_type==0)
		{
			$sql_query="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name as buyer_name, b.style_ref_no, a.po_break_down_id, a.item_number_id, c.po_number as po_number,c.unit_price,
				sum(d.production_qnty) as good_qnty, 
				sum(CASE WHEN production_hour ='1' THEN production_qnty else 0 END) AS good_1am,
				sum(CASE WHEN production_hour ='2' THEN production_qnty else 0 END) AS good_2am,
				sum(CASE WHEN production_hour ='3' THEN production_qnty else 0 END) AS good_3am,
				sum(CASE WHEN production_hour ='4' THEN production_qnty else 0 END) AS good_4am,
				sum(CASE WHEN production_hour ='5' THEN production_qnty else 0 END) AS good_5am,
				sum(CASE WHEN production_hour ='6' THEN production_qnty else 0 END) AS good_6am,
				sum(CASE WHEN production_hour ='7' THEN production_qnty else 0 END) AS good_7am,
				sum(CASE WHEN production_hour ='8' THEN production_qnty else 0 END) AS good_8am,
				sum(CASE WHEN production_hour ='9' THEN production_qnty else 0 END) AS good_9am,
				sum(CASE WHEN production_hour ='10' THEN production_qnty else 0 END) AS good_10am,
				sum(CASE WHEN production_hour ='11' THEN production_qnty else 0 END) AS good_11am,
				sum(CASE WHEN production_hour ='12' THEN production_qnty else 0 END) AS good_12am,
				sum(CASE WHEN production_hour ='13' THEN production_qnty else 0 END) AS good_1pm,
				sum(CASE WHEN production_hour ='14' THEN production_qnty else 0 END) AS good_2pm,
				sum(CASE WHEN production_hour ='15' THEN production_qnty else 0 END) AS good_3pm,
				sum(CASE WHEN production_hour ='16' THEN production_qnty else 0 END) AS good_4pm,
				sum(CASE WHEN production_hour ='17' THEN production_qnty else 0 END) AS good_5pm,
				sum(CASE WHEN production_hour ='18' THEN production_qnty else 0 END) AS good_6pm,
				sum(CASE WHEN production_hour ='19' THEN production_qnty else 0 END) AS good_7pm,
				sum(CASE WHEN production_hour ='20' THEN production_qnty else 0 END) AS good_8pm,
				sum(CASE WHEN production_hour ='21' THEN production_qnty else 0 END) AS good_9pm,
				sum(CASE WHEN production_hour ='22' THEN production_qnty else 0 END) AS good_10pm,
				sum(CASE WHEN production_hour ='23' THEN production_qnty else 0 END) AS good_11pm,
				sum(CASE WHEN production_hour ='24' THEN production_qnty else 0 END) AS good_12pm
				from pro_garments_production_mst a,pro_garments_production_dtls d, wo_po_details_master b, wo_po_break_down c,wo_po_color_size_breakdown e
				where a.production_type=15 and d.production_type=15 and a.po_break_down_id=c.id and a.id=d.mst_id and d.color_size_break_down_id=e.id and e.po_break_down_id=c.id and e.job_no_mst=b.job_no and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and e.status_active in(1,2,3)  and e.is_deleted=0 $company_name $buyer $location $floor $line $txt_date_from 
				group by a.company_id, a.location, a.floor_id,a.po_break_down_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name, b.style_ref_no, a.item_number_id, c.po_number,c.unit_price order by a.location, a.floor_id, a.po_break_down_id";
				$production_hour_subcon=" hour ";
		}
		else if($db_type==2)
		{
			
			$production_hour="TO_CHAR(production_hour,'HH24:MI')";
			//$txt_reporting_hour=str_replace("'","",$txt_poly_date)." ".str_replace("'","",$txt_reporting_hour);
			//$production_hour="to_date('".$production_hour."','DD MONTH YYYY HH24:MI:SS')";
			
			$sql_query="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name as buyer_name, b.style_ref_no, b.set_break_down,b.set_smv, a.po_break_down_id, e.item_number_id, c.po_number as po_number,c.unit_price,
				sum(d.production_qnty) as good_qnty";
				$first=1;
				for($h=$hour;$h<$last_hour;$h++)
				{
					$bg=$start_hour_arr[$h];
					//echo $bg;die;
					$end=substr(add_time($start_hour_arr[$h],60),0,5);
					$prod_hour="good_".substr((add_time($start_hour_arr[$h],60)),0,2);
					if($first==1)
					{
						$sql_query.=", sum(CASE WHEN $production_hour<='$end' and d.production_type=15 THEN d.production_qnty else 0 END) AS $prod_hour";
					}
					else
					{
						$sql_query.=", sum(CASE WHEN $production_hour>'$bg' and $production_hour<='$end' and d.production_type=15 THEN d.production_qnty else 0 END) AS $prod_hour";
					}
					$first=$first+1;
				}
				$sql_query.=", sum(CASE WHEN $production_hour>'$start_hour_arr[$last_hour]' and $production_hour<='$start_hour_arr[24]' and d.production_type=15 THEN d.production_qnty else 0 END) AS good_24 from pro_garments_production_mst a,pro_garments_production_dtls d, wo_po_details_master b, wo_po_break_down c,wo_po_color_size_breakdown e
				where a.production_type=15 and d.production_type=15 and a.id=d.mst_id and a.po_break_down_id=c.id and c.job_id=b.id and a.status_active=1 and a.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3)  and c.is_deleted=0  and e.status_active in(1,2,3) and e.is_deleted=0 and e.id=d.color_size_break_down_id and e.po_break_down_id=c.id and a.po_break_down_id=e.po_break_down_id $company_name $buyer $location $floor $line   $txt_date_from 
				group by a.company_id, a.location, a.floor_id, a.po_break_down_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name, b.style_ref_no, b.set_break_down,b.set_smv, e.item_number_id, c.po_number,c.unit_price order by a.location, a.floor_id, a.po_break_down_id";
				
				$production_hour_subcon="TO_CHAR(hour,'HH24:MI')";
		}
		//echo $sql_query; die;

		$sql_query_subcon="SELECT  a.company_id, a.location_id as location, a.floor_id, a.prod_reso_allo, a.production_date, a.line_id as sewing_line, b.party_id as buyer_name, c.cust_style_ref as style_ref_no, c.smv as set_smv, a.order_id as po_break_down_id, a.gmts_item_id as item_number_id , c.order_no as po_number,c.rate,
				sum(d.prod_qnty) as good_qnty";
				$first=1;
				for($h=$hour;$h<$last_hour;$h++)
				{
					$bg=$start_hour_arr[$h];
					$end=substr(add_time($start_hour_arr[$h],60),0,5);
					$prod_hour="good_".substr((add_time($start_hour_arr[$h],60)),0,2);
					if($first==1)
					{
						$sql_query_subcon.=", sum(CASE WHEN $production_hour_subcon<='$end' and d.production_type=5 THEN d.prod_qnty else 0 END) AS $prod_hour";
					}
					else
					{
						$sql_query_subcon.=", sum(CASE WHEN $production_hour_subcon>'$bg' and $production_hour_subcon<='$end' and d.production_type=5 THEN d.prod_qnty else 0 END) AS $prod_hour";
					}
					$first=$first+1;
				}
				$sql_query_subcon.=", sum(CASE WHEN $production_hour_subcon>'$start_hour_arr[$last_hour]' and $production_hour_subcon<='$start_hour_arr[24]' and d.production_type=5 THEN d.prod_qnty else 0 END) AS good_24 from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d, subcon_ord_mst b, subcon_ord_dtls c,subcon_ord_breakdown e
				where a.production_type=5 and d.production_type=5 and a.id=d.dtls_id and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and e.id=d.ord_color_size_id and e.order_id=c.id and a.order_id=e.order_id $company_name_subcon $location_subcon $floor $line_subcon   $txt_date_from 
				group by a.company_id, a.location_id, a.floor_id, a.order_id, a.prod_reso_allo, a.production_date, a.line_id, b.party_id, c.cust_style_ref, c.smv, a.gmts_item_id, c.order_no,c.rate order by a.location_id, a.floor_id, a.order_id";
		//echo $sql_query_subcon; //die;
		$sql=sql_select($sql_query);			 
		$sql_subcon=sql_select($sql_query_subcon);
		$production_data_arr=array();
		$production_po_data_arr=array();$all_style_arr=array();$style_wise_po_arr=array();
		foreach($sql as $val)
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
			else $slNo=$lineSerialArr[$sewing_line_id];
			$production_serial_arr[$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]]=$val[csf('sewing_line')];

			$production_data_arr2[$val[csf('po_break_down_id')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('item_number_id')]]+=$val[csf('good_qnty')];
			$production_data_arr3[$val[csf('po_break_down_id')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]=$val[csf('set_break_down')];

			
			$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['qty']+=$val[csf('good_qnty')];
			$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['unit_price'] =$val[csf('unit_price')];

			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['prod_reso_allo']=$val[csf('prod_reso_allo')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_break_down_id'].=$val[csf('po_break_down_id')].",";
			
			if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name'].=",".$val[csf('buyer_name')]; 
			}
			else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']=$val[csf('buyer_name')]; 
			}
			
			
			if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv'].=",".number_format($val[csf('set_smv')],2); 
			}
			else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv']=number_format($val[csf('set_smv')],2); 
			}
			
			
			if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number'].=", ".$val[csf('po_number')]; 
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style_ref'].=",".$val[csf('style_ref_no')]; 
			}
			else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']=$val[csf('po_number')]; 
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style_ref']=$val[csf('style_ref_no')]; 
			}
			
			if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('style_ref_no')]."**".$val[csf('unit_price')]; 
			}
			else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('style_ref_no')]."**".$val[csf('unit_price')]; 
			}
			
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['quantity']+=$val[csf('good_qnty')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['1am']+=$val[csf('good_01')];  
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['2am']+=$val[csf('good_02')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['3am']+=$val[csf('good_03')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['4am']+=$val[csf('good_04')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['5am']+=$val[csf('good_05')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['6am']+=$val[csf('good_06')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['7am']+=$val[csf('good_07')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['8am']+=$val[csf('good_08')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['9am']+=$val[csf('good_09')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['10am']+=$val[csf('good_10')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['11am']+=$val[csf('good_11')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['12pm']+=$val[csf('good_12')];
			
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['1pm']+=$val[csf('good_13')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['2pm']+=$val[csf('good_14')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['3pm']+=$val[csf('good_15')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['4pm']+=$val[csf('good_16')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['5pm']+=$val[csf('good_17')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['6pm']+=$val[csf('good_18')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['7pm']+=$val[csf('good_19')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['8pm']+=$val[csf('good_20')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['9pm']+=$val[csf('good_21')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['10pm']+=$val[csf('good_22')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['11pm']+=$val[csf('good_23')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['12am']+=$val[csf('good_24')]; 
			$all_po_id_arr[$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];
			$all_style_arr[$val[csf('style_ref_no')]]=$val[csf('style_ref_no')];
			$style_wise_po_arr[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];
			
			for($iii=1; $iii<=24; $iii++)
			{
				if($iii<10){$iii='0'.$iii;}
				if(!empty($val[csf("good_".$iii)]))
				{
					$last_pro_hour[$val[csf('floor_id')].'**'.$val[csf('sewing_line')]][$iii]=$iii;
				}
			}
			
			
		}
		
 		 foreach($last_pro_hour as $fl=>$ph){
			 ksort($ph);
			list($f,$l)=explode('**',$fl);
			for($ni=9; $ni<=end($ph); $ni++)
			{
				if($ni != 13){
					$production_data_arr[$f][$l]['production_hour']+=1;
				}
			}
		 }

		
		
		//print_r($last_pro_hour);
		
		 //print_r($production_data_arr_1);
		// echo "<pre>";
		// print_r($production_data_arr3);
		// echo "<pre>";
		//echo $smv_source;die;
		$all_po_ids=implode(",", array_unique($all_po_id_arr)); 
		if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
		if($smv_source==3)
		{
			/*$sql_item="select b.id, a.sam_style, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3)  and b.id in($all_po_ids)";
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
			}*/
			
			 
			$style_nos=implode("','",$all_style_arr);
			$gsdSql="SELECT a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID  from PPL_GSD_ENTRY_MST a where a.APPLICABLE_PERIOD <= $txt_date and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and a.BULLETIN_TYPE in(4) and A.STYLE_REF in('".$style_nos."') and a.APPROVED=1 
			group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID
			 ORDER BY a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC";
			     //echo $gsdSql; 
			$gsdSqlResult = sql_select($gsdSql); 
			 
			foreach($gsdSqlResult as $rows)
			{
				foreach($style_wise_po_arr[$rows[STYLE_REF]] as $po_id)
				{
					if($item_smv_array[$po_id][$rows[GMTS_ITEM_ID]]==''){
						$item_smv_array[$po_id][$rows[GMTS_ITEM_ID]]=$rows[TOTAL_SMV];
					}
				}
			}
			
			//print_r($item_smv_array);die;
		}
		else
		{
			$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_id=a.job_id and b.job_no_mst=c.job_no  and b.id in($all_po_ids) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3)";
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

		//print_r($item_smv_array);die;

		$last_pro_hour=array();
		$subcon_fob_calc=array();
		foreach($sql_subcon as $val)
		{
			//floor and line wise 
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
			else $slNo=$lineSerialArr[$sewing_line_id];
			$production_serial_arr[$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]]=$val[csf('sewing_line')];
			
			$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['qty']+=$val[csf('good_qnty')];
			$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['unit_price'] = $val[csf('rate')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['prod_reso_allo']=$val[csf('prod_reso_allo')]; 
			
			if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name'].=",".$val[csf('buyer_name')]; 
			}
			else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']=$val[csf('buyer_name')]; 
			}
			
			
			if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv'].=",".number_format($val[csf('set_smv')],2); 
			}
			else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv']=number_format($val[csf('set_smv')],2); 
			}
			
			if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv_subcon']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv_subcon'].=",".number_format($val[csf('set_smv')],2); 
			}
			else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['set_smv_subcon']=number_format($val[csf('set_smv')],2); 
			}
			
			
			if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number'].=", ".$val[csf('po_number')]; 
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style_ref'].=",".$val[csf('style_ref_no')]; 
			}
			else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']=$val[csf('po_number')]; 
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style_ref']=$val[csf('style_ref_no')]; 
			}
			
			if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')].'**'."subcon**".$val[csf('style_ref_no')]; 
			}
			else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')].'**'."subcon**".$val[csf('style_ref_no')]; 
			}

			$subcon_smv_calc[$val[csf('floor_id')]][$val[csf('sewing_line')]]+=$val[csf('set_smv')]*$val[csf('good_qnty')];
			$subcon_fob_calc[$val[csf('floor_id')]][$val[csf('sewing_line')]]+=$val[csf('rate')]*$val[csf('good_qnty')];

			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['quantity']+=$val[csf('good_qnty')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['1am']+=$val[csf('good_01')];  
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['2am']+=$val[csf('good_02')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['3am']+=$val[csf('good_03')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['4am']+=$val[csf('good_04')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['5am']+=$val[csf('good_05')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['6am']+=$val[csf('good_06')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['7am']+=$val[csf('good_07')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['8am']+=$val[csf('good_08')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['9am']+=$val[csf('good_09')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['10am']+=$val[csf('good_10')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['11am']+=$val[csf('good_11')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['12pm']+=$val[csf('good_12')];
			
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['1pm']+=$val[csf('good_13')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['2pm']+=$val[csf('good_14')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['3pm']+=$val[csf('good_15')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['4pm']+=$val[csf('good_16')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['5pm']+=$val[csf('good_17')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['6pm']+=$val[csf('good_18')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['7pm']+=$val[csf('good_19')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['8pm']+=$val[csf('good_20')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['9pm']+=$val[csf('good_21')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['10pm']+=$val[csf('good_22')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['11pm']+=$val[csf('good_23')]; 
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['12am']+=$val[csf('good_24')]; 
			$all_po_id_arr_sub[$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')]; 
			$all_style_arr[$val[csf('style_ref_no')]]=$val[csf('style_ref_no')];
			$style_wise_po_arr[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];
			
			
			
			for($iii=1; $iii<=24; $iii++){
				
				if($iii<10){$iii='0'.$iii;}
				if(!empty($val[csf("good_".$iii)]))
				{
					$last_pro_hour[$val[csf('floor_id')].'**'.$val[csf('sewing_line')]][$iii]=$iii;
				}
			}

		}
		
		
		
		 foreach($last_pro_hour as $fl=>$ph){
			 ksort($ph);
			list($f,$l)=explode('**',$fl);
			for($ni=9; $ni<=end($ph); $ni++)
			{
				if($ni != 13){
					$production_data_arr[$f][$l]['production_hour']+=1;
				}
			}
		 }
		
		
		
	    $all_po_ids_subcon=implode(",", array_unique($all_po_id_arr_sub));
		$subcon_days_run_sql=sql_select("SELECT min(production_date) as min_date,order_id,line_id from subcon_gmts_prod_dtls where order_id in ($all_po_ids_subcon) and production_type=5 group by order_id,line_id");
		foreach ($subcon_days_run_sql as $key => $value) 
		{
			 $subcon_days_run_arr[$value[csf("order_id")]][$value[csf("line_id")]]=$value[csf("min_date")];
		}
		$days_run_sqls=sql_select("SELECT min(production_date) as min_date,po_break_down_id,sewing_line from pro_garments_production_mst where po_break_down_id in($all_po_ids)  and production_type=11 group by po_break_down_id,sewing_line");
		foreach ($days_run_sqls as $key => $value) 
		{
			 $days_run_main_arr[$value[csf("po_break_down_id")]][$value[csf("sewing_line")]]=$value[csf("min_date")];
		}

		//echo "<pre>";
		// print_r($production_data_arr);die;

		 /*echo "<pre>";
		print_r($subcon_smv_calc);
		echo "</pre>";*/
		unset($sql);		
		$before_8_am=$production_hour1=$production_hour2=$production_hour3=$production_hour4=$production_hour5=$production_hour6=$production_hour7=$production_hour8=0;   
		$production_hour9=$production_hour10=$production_hour11=$production_hour12=$production_hour13=$production_hour14=$production_hour15=$production_hour16=$avable_min=0;
		$production_hour17=$production_hour18=$production_hour19=$production_hour20=$production_hour21=$production_hour22=$production_hour23=$production_hour24=$today_product=0;
		$floor_hour1=$floor_hour2=$floor_hour3=$floor_hour4=$floor_hour5=$floor_hour6==$floor_hour7=$floor_hour8=$floor_before_9am=0;  $floor_name="";   
		$floor_hour9=$floor_hour10=$floor_hour11=$floor_hour12=$floor_hour13=$floor_hour14=$floor_hour15=$floor_hour16=$floor_man_power=0;
		$floor_hour17=$floor_hour18=$floor_hour19=$floor_hour20=$floor_hour21=$floor_hour22=$floor_hour23=$floor_hour24=$floor_operator=$floor_produc_min=$floor_fob_val=0;
		$floor_smv=$floor_row=$floor_helper=$floor_tgt_h=$floor_days_run=$floor_before_8_am=$floor_working_hour=$floor_ttl_tgt=$floor_today_product=$floor_avale_minute=0;
		$total_hour1=$total_hour2=$total_hour3=$total_hour4=$total_hour5=$total_hour6==$total_hour7=$total_hour8=$total_before_8am=$total_operator=$total_helper=$gnd_hit_rate=0;   
		$total_hour9=$total_hour10=$total_hour11=$total_hour12=$total_hour13=$total_hour14=$total_hour15=$total_hour16=$total_smv=$total_terget=$grand_total_product=$gnd_line_effi=0;
		$total_hour17=$total_hour18=$total_hour19=$total_hour20=$total_hour21=$total_hour22=$total_hour23=$total_hour24=$total_man_power=$gnd_avable_min=$gnd_product_min=$gnd_fob_val=0;
		$item_smv=$item_smv_total=$line_efficiency=$days_run=$total_working_hour=$gnd_total_tgt_h=$total_capacity=0;
		$j=1;
		ob_start();
		$line_number_check_arr=array();
		$smv_for_item="";
		//echo "<pre>";
		//print_r($production_data_arr);	  
		foreach($production_data_arr as $f_id=>$fname)
		{
			ksort($fname);
			foreach($fname as $l_id=>$ldata)
			{
				if($i!=1)
				{
					if(!in_array($f_id, $check_arr))
					{
						if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$html.='<tr  bgcolor="#B6B6B6">
							<td width="40">&nbsp;</td>
							<td width="80">&nbsp;</td>
							<td width="80">&nbsp;</td>
							<td width="80">&nbsp;</td>
							<td width="140">&nbsp;</td>
							<td width="140">&nbsp;</td>
							<td width="120" align="right">Sub Total:</td>
							<td align="right" width="60">&nbsp;</td>
							<td align="right" width="70">'.$floor_operator.'</td>
							<td align="right" width="50">'.$floor_helper.'</td>
							<td align="right" width="60">'.$floor_man_power.'</td>
							<td align="right" width="70">'.$floor_tgt_h.'</td>
							<td align="right" width="60">'.$floor_days_run.'</td>
							<td align="right" width="70">'.$floor_capacity.'</td>
							<td align="right" width="60">'.number_format($floor_working_hour,2).'</td>
							<td align="right" width="60">'.$floor_working_hours.'</td>
							<td align="right" width="60"></td>
							
							<td align="right" width="80">'.number_format($floor_ttl_tgt,0).'</td>
							<td align="right" width="80">'.$floor_today_product.'</td>
							<td align="right" width="80">'.number_format($floor_fob_val,2).'</td>
							
							<td align="right" width="80">'.number_format(($floor_today_product-$floor_ttl_tgt),0).'</td>
							<td align="right" width="100">'.$floor_avale_minute.'</td>
							<td align="right" width="100">'.number_format($floor_produc_min,2).'</td>
							<td align="right" width="60">'.number_format(($floor_today_product/$floor_ttl_tgt)*100,2).'%</td>
							<td align="right" width="90">'.number_format($floor_efficency,2).'%</td>
							<td align="right" width="50">'.$floor_hour9.'</td>
							<td align="right" width="50">'.$floor_hour10.'</td>
							<td align="right" width="50">'.$floor_hour11.'</td>
							<td align="right" width="50">'.$floor_hour12.'</td>
							<td align="right" width="50">'.$floor_hour13.'</td>
							<td align="right" width="50">'.$floor_hour14.'</td>
							<td align="right" width="50">'.$floor_hour15.'</td>
							<td align="right" width="50">'.$floor_hour16.'</td>
							<td align="right" width="50">'.$floor_hour17.'</td>
							<td align="right" width="50">'.$floor_hour18.'</td>
							<td align="right" width="50">'.$floor_hour19.'</td>
							<td align="right" width="50">'.$floor_hour20.'</td>
							<td align="right" width="50">'.$floor_hour21.'</td>
							<td align="right" width="50">'.$floor_hour22.'</td>
							<td align="right" width="50">'.$floor_hour23.'</td>
							<td align="right">'.$floor_hour24.'</td>
						</tr>';
						
						$floor_html.='<tbody>';
						$floor_html.="<tr bgcolor='$bgcolor' onclick=change_color('floor_$j','$bgcolor') id=floor_$j>";
						$floor_html.='<td width="40">'.$j.'&nbsp;</td>
							<td width="80" align="center">'.$floor_name.' </td>
							<td width="70" align="right">'.$floor_tgt_h.'</td>
							<td width="70" align="right">'.$floor_capacity.'</td>
							<td width="60" align="right">'.$floor_man_power.'</td>
							<td width="70" align="right">'.$floor_operator.'</td>
							<td width="50" align="right">'.$floor_helper.'</td>
							<td align="right" width="60">'.number_format($floor_working_hour,2).'</td>
							<td align="right" width="80">'.number_format($floor_ttl_tgt,0).'</td>
							<td align="right" width="80">'.$floor_today_product.'</td>
							<td align="right" width="80">'.number_format($floor_fob_val,2).'</td>
							<td align="right" width="80">'.number_format(($floor_today_product-$floor_ttl_tgt),0).'</td>
							<td align="right" width="100">'.$floor_avale_minute.'</td>
							<td align="right" width="100">'.number_format($floor_produc_min,2).'</td>
							<td align="right" width="90">'.number_format(($floor_today_product/$floor_ttl_tgt)*100,2).'%</td>
							<td align="right" width="90">'.number_format($floor_efficency,2).'%</td>
							<td align="right" width="50">'.$floor_hour9.'</td>
							<td align="right" width="50">'.$floor_hour10.'</td>
							<td align="right" width="50">'.$floor_hour11.'</td>
							<td align="right" width="50">'.$floor_hour12.'</td>
							<td align="right" width="50">'.$floor_hour13.'</td>
							<td align="right" width="50">'.$floor_hour14.'</td>
							<td align="right" width="50">'.$floor_hour15.'</td>
							<td align="right" width="50">'.$floor_hour16.'</td>
							<td align="right" width="50">'.$floor_hour17.'</td>
							<td align="right" width="50">'.$floor_hour18.'</td>
							<td align="right" width="50">'.$floor_hour19.'</td>
							<td align="right" width="50">'.$floor_hour20.'</td>
							<td align="right" width="50">'.$floor_hour21.'</td>
							<td align="right" width="50">'.$floor_hour22.'</td>
							<td align="right" width="50">'.$floor_hour23.'</td>
							<td align="right">'. $floor_hour24.'</td>
						</tr>';
						$floor_name=""; $floor_smv=0; $floor_row=0; $floor_operator=0; $floor_helper=0; $floor_tgt_h=0; $floor_man_power=0; $floor_days_run=0; $floor_before_9_am=0;
						$floor_hour9=0; $floor_hour10=0; $floor_hour11=0; $floor_hour12=0; $floor_hour13=0; $floor_hour14=0; $floor_hour15=0; $floor_hour16=0; $floor_hour17=0; $floor_hour18=0; $floor_hour19=0; $floor_hour20=0; $floor_hour21=0; $floor_hour22=0; $floor_hour23=0; $floor_hour24=0;
						$floor_working_hour=0; $floor_ttl_tgt=0; $floor_today_product=0; $floor_avale_minute=0; $floor_produc_min=0;$floor_fob_val=0; $floor_efficency=0; $floor_man_power=0; $floor_capacity=0;
						$j++;
					}
				}
				$floor_row++;	
			
				$po_number=array_unique(explode(',',$row[csf('po_number')]));
				$germents_item=array_unique(explode('****',$ldata['item_number_id']));
				$buyer_neme_all=array_unique(explode(',',$ldata['buyer_name']));
				
				// $set_smv_all=implode(',',array_unique(explode(',',$ldata['set_smv'])));
				$style_ref=implode(', ',array_unique(explode(',',$ldata['style_ref'])));
				$po_break_down_id=implode(', ',array_unique(explode(',',$ldata['po_break_down_id'])));
				//print_r($germents_item);
				$set_smv_all="";
				$itm_smv_arr = array();
				$chk_smv_array = array();
				foreach ($germents_item as $value) 
				{
					$po_item = explode("**", $value);
					$po_id = $po_item[0];
					$item_id = $po_item[1];
					$style = $po_item[2];
					if($production_data_arr2[$po_id][$f_id][$l_id][$item_id] !="")
					{
						$break_down_smv = $production_data_arr3[$po_id][$f_id][$l_id];
						$break_down_smv_arr = explode("__", $break_down_smv);
						// print_r($break_down_smv_arr);die();
						foreach ($break_down_smv_arr as $smv) 
						{
							$set_smv = explode("_", $smv);
							if($item_id==$set_smv[0])
							{
								if(!isset($chk_smv_array[$style][$item_id]))
								{
									$set_smv[2]=($item_smv_array[$po_id][$item_id])?$item_smv_array[$po_id][$item_id]:$set_smv[2];
									
									$item_smv_array3[$po_id][$item_id]=$set_smv[2];
									
									
									$itm_smv_arr[$po_id][$f_id][$l_id]+=$set_smv[2];
									$chk_smv_array[$style][$item_id] = $set_smv[2];
								}
							}							
						}
					}	
				}
				//print_r($itm_smv_arr);die;
				$po_break_down_id = explode(",", $po_break_down_id);
				//print_r($po_break_down_id);die;
				foreach (array_filter($po_break_down_id) as $po) 
				{
					$po=$po*1;
					if(!empty($itm_smv_arr[$po][$f_id][$l_id])){
						$set_smv_all .= number_format($itm_smv_arr[$po][$f_id][$l_id],2).",";
					}
				}
				//print_r($po_break_down_id);die;
				
				$subcontact_smvs = explode(",", $ldata['set_smv_subcon']);
				foreach (array_filter($subcontact_smvs) as $set_smv_subcon) 
				{
					$set_smv_all .= $set_smv_subcon.",";
				}
				//echo	$ldata['set_smv_subcon'];die;
				
				// print_r($po_break_down_id);
				// echo $set_smv_all;
				
				$buyer_name="";
				foreach($buyer_neme_all as $buy)
				{
					if($buyer_name!='') $buyer_name.=',';
					$buyer_name.=$buyerArr[$buy];
				}
				$chk_item_array = array();
				$garment_itemname=''; $item_smv=""; $smv_for_item=""; $produce_minit=""; $order_no_total="";
				$prod_fob_val = 0;
				$garment_item_id_arr=array();
				$days_run="";
				foreach($germents_item as $g_val)
				{
					$po_garment_item=explode('**',$g_val);
					if($po_garment_item[2]=="subcon" )
					{  
						//$day_run_sql=sql_select("");
						 
						 $sewing_day=$subcon_days_run_arr[$po_garment_item[0]][$l_id];

					}
					else
					{  
						//$day_run_sql=sql_select("");
						$sewing_day=$days_run_main_arr[$po_garment_item[0]][$l_id];
					}
					
					 
				    //$sewing_day=$day_run_sql[0][csf('min_date')];
					if($sewing_day!="")
					{ 
						if($days_run=="")
						{  
							  $days_run .=datediff("d",$sewing_day,$pr_date);
						}
						else
						{
							  
							 $days_run .=','.datediff("d",$sewing_day,$pr_date);
						}
						
					}
					if(!isset($chk_item_array[$style_ref][$po_garment_item[1]]))
					{	
						if($garment_itemname!='') $garment_itemname.=',';
							$garment_itemname.=$garments_item[$po_garment_item[1]];
							$garment_item_id_arr[$po_garment_item[1]]=$po_garment_item[1];
					}	
					$chk_item_array[$style_ref][$po_garment_item[1]] = $po_garment_item[1];

					/*if($garment_itemname!='') $garment_itemname.=',';
						$garment_itemname.=$garments_item[$po_garment_item[1]];*/
						
					if($item_smv!='') $item_smv.='/';
						$item_smv.=$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
						
					if($order_no_total!="") $order_no_total.=",";
						$order_no_total.=$po_garment_item[0];
					
					if($smv_for_item!="") 
						$smv_for_item.="****".$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
					else
						$smv_for_item=$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];	
					
					//$produce_minit+=($production_po_data_arr[$f_id][$l_id][$po_garment_item[0]]['qty']*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]]);
					
					$produce_minit+=($production_po_data_arr[$f_id][$l_id][$po_garment_item[0]]['qty']*$item_smv_array3[$po_garment_item[0]][$po_garment_item[1]]);
					
					
					
					
					
					//echo $production_po_data_arr[$f_id][$l_id][$po_garment_item[0]]['qty'].'='.$po_garment_item[0].'**'.$po_garment_item[1]."<br>";
					
					
					
					$prod_fob_val += ($production_po_data_arr[$f_id][$l_id][$po_garment_item[0]]['qty']*$production_po_data_arr[$f_id][$l_id][$po_garment_item[0]]['unit_price']);
				}

				$produce_minit+=$subcon_smv_calc[$f_id][$l_id]; 
				$prod_fob_val+=$subcon_fob_calc[$f_id][$l_id]; 
			 
				$sewing_line='';
				if($ldata['prod_reso_allo']==1)
				{
					$line_number=explode(",",$prod_reso_arr[$l_id]);
					foreach($line_number as $val)
					{
						if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=", ".$lineArr[$val];
					}
				}
				else $sewing_line=$lineArr[$l_id];
			
				$total_eff_hour=0;
				$production_hour1=$ldata['1am']; if($production_hour1!=0) $total_eff_hour+=1;
				$production_hour2=$ldata['2am']; if($production_hour2!=0) $total_eff_hour+=1;
				$production_hour3=$ldata['3am']; if($production_hour3!=0) $total_eff_hour+=1;
				$production_hour4=$ldata['4am']; if($production_hour4!=0) $total_eff_hour+=1;
				$production_hour5=$ldata['5am']; if($production_hour5!=0) $total_eff_hour+=1;
				$production_hour6=$ldata['6am']; if($production_hour6!=0) $total_eff_hour+=1;
				$production_hour7=$ldata['7am']; if($production_hour7!=0) $total_eff_hour+=1;
				$production_hour8=$ldata['8am']; if($production_hour8!=0) $total_eff_hour+=1;
				$production_hour9=$ldata['9am']; if($production_hour9!=0) $total_eff_hour+=1;
				$production_hour10=$ldata['10am']; if($production_hour10!=0) $total_eff_hour+=1;
				$production_hour11=$ldata['11am']; if($production_hour11!=0) $total_eff_hour+=1;
				$production_hour12=$ldata['12pm']; if($production_hour12!=0) $total_eff_hour+=1;
				$production_hour13=$ldata['1pm']; if($production_hour13!=0) $total_eff_hour+=1;
				$production_hour14=$ldata['2pm']; if($production_hour14!=0) $total_eff_hour+=1;
				$production_hour15=$ldata['3pm']; if($production_hour15!=0) $total_eff_hour+=1;
				$production_hour16=$ldata['4pm']; if($production_hour16!=0) $total_eff_hour+=1;
				$production_hour17=$ldata['5pm']; if($production_hour17!=0) $total_eff_hour+=1;
				$production_hour18=$ldata['6pm']; if($production_hour18!=0) $total_eff_hour+=1; 
				$production_hour19=$ldata['7pm']; if($production_hour19!=0) $total_eff_hour+=1;
				$production_hour20=$ldata['8pm']; if($production_hour20!=0) $total_eff_hour+=1;
				$production_hour21=$ldata['9pm']; if($production_hour21!=0) $total_eff_hour+=1;
				$production_hour22=$ldata['10pm']; if($production_hour22!=0) $total_eff_hour+=1;
				$production_hour23=$ldata['11pm']; if($production_hour23!=0) $total_eff_hour+=1;
				$production_hour24=$ldata['12am']; if($production_hour24!=0) $total_eff_hour+=1;
			
				if($total_eff_hour>$prod_resource_array[$l_id][$pr_date]['working_hour'])
				{
					$total_eff_hour=$prod_resource_array[$l_id][$pr_date]['working_hour'];
				}
				$before_8_am=$production_hour1+$production_hour2+$production_hour3+$production_hour4+$production_hour5+$production_hour6+$production_hour7+$production_hour8;//$before_8_am+
				$today_product=$production_hour8+$production_hour9+$production_hour10+$production_hour11+$production_hour12+$production_hour13+$production_hour14+$production_hour15+$production_hour16+$production_hour17+$production_hour18+$production_hour19+$production_hour20+$production_hour21+$production_hour22+$production_hour23+$production_hour24;
				
				/*if($sewing_day!="")
				{
					$days_run= $diff=datediff("d",$sewing_day,$pr_date);
				}
				else  $days_run=0;*/
				
				//******************************* line effiecency****************************************************************************['']
				$current_wo_time=0;
				if($current_date==$search_prod_date)
				{
					$prod_wo_hour=$prod_resource_array[$l_id][$pr_date]['working_hour'];
					
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
					$current_wo_time=$prod_resource_array[$l_id][$pr_date]['working_hour'];
					$cla_cur_time=$prod_resource_array[$l_id][$pr_date]['working_hour'];
				}
				//$avable_min=($prod_resource_array[$l_id][$pr_date]['man_power'])*$prod_resource_array[$l_id][$pr_date]['working_hour']*60;
				//******************************* line effiecency****************************************************************************['']
				$smv_adjustmet_type=$prod_resource_array[$l_id][$pr_date]['smv_adjust_type'];
				//$summary_smv_adjustmet_type=$no_prod_line_arr[$f_id]['smv_adjust_type'];
				$eff_target=($prod_resource_array[$l_id][$pr_date]['terget_hour']*$prod_resource_array[$l_id][$pr_date]['working_hour']);
				
				/*if($total_eff_hour>=$prod_resource_array[$l_id][$pr_date]['working_hour'])
				{*/
					$total_adjustment=0;
					if(str_replace("'","",$smv_adjustmet_type)==1)
					{ 
						$total_adjustment=$prod_resource_array[$l_id][$pr_date]['smv_adjust'];
					}
					else if(str_replace("'","",$smv_adjustmet_type)==2)
					{
						$total_adjustment=($prod_resource_array[$l_id][$pr_date]['smv_adjust'])*(-1);
					}
				//}
				
				/*if($total_adjustment_summary>=$no_prod_line_arr[$f_id]['working_hour'])
				{
					if(str_replace("'","",$summary_smv_adjustmet_type)==1)
					{ 
						$total_adjustment_summary=$no_prod_line_arr[$f_id]['smv_adjust'];
					}
					if(str_replace("'","",$summary_smv_adjustmet_type)==2)
					{
						$total_adjustment_summary=($no_prod_line_arr[$f_id]['smv_adjust'])*(-1);
					}
				}*/
				
				//$efficiency_min=($prod_resource_array[$l_id][$pr_date]['man_power'])*$prod_resource_array[$l_id][$pr_date]['working_hour']*60;
				$efficiency_min=$total_adjustment+($prod_resource_array[$l_id][$pr_date]['man_power'])*$cla_cur_time*60;
				//$efficiency_min+=$prod_resource_array[$l_id][$pr_date]['man_power'];
			
				$line_efficiency=(($produce_minit)*100)/$efficiency_min;
				//********************************* calclution floor total ****************************************************$pr_date],$sewing_day
				$floor_name=$floorArr[$f_id];	
				$floor_hour24+=$production_hour24;
				$floor_hour9+=$production_hour9;
				$floor_hour10+=$production_hour10;
				$floor_hour11+=$production_hour11;
				$floor_hour12+=$production_hour12; 
				$floor_hour13+=$production_hour13; 
				$floor_hour14+=$production_hour14;
				$floor_hour15+=$production_hour15;
				$floor_hour16+=$production_hour16;
				$floor_hour17+=$production_hour17;
				$floor_hour18+=$production_hour18;
				$floor_hour19+=$production_hour19; 
				$floor_hour20+=$production_hour20;
				$floor_hour21+=$production_hour21;
				$floor_hour22+=$production_hour22;
				$floor_hour23+=$production_hour23; 
				$floor_before_8_am+=$before_8_am;
				$floor_smv+=$item_smv;
				$floor_capacity+=$prod_resource_array[$l_id][$pr_date]['capacity'];
				$floor_operator+=$prod_resource_array[$l_id][$pr_date]['operator'];
				$floor_helper+=$prod_resource_array[$l_id][$pr_date]['helper'];
				$floor_tgt_h+=$prod_resource_array[$l_id][$pr_date]['terget_hour'];	
				$floor_days_run+=$days_run;
				$floor_working_hour+=$prod_resource_array[$l_id][$pr_date]['working_hour']; 
				$floor_ttl_tgt+=$eff_target;
				$floor_today_product+=$today_product;
				$floor_avale_minute+=$efficiency_min;
				$floor_produc_min+=$produce_minit; 
				$floor_fob_val+=$prod_fob_val; 
				$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
				$floor_man_power+=$prod_resource_array[$l_id][$pr_date]['man_power'];
				//**************************** calclution total ********************************************************************
				$total_hour24+=$production_hour24;
				$total_hour9+=$production_hour9;
				$total_hour10+=$production_hour10;
				$total_hour11+=$production_hour11;
				$total_hour12+=$production_hour12; 
				$total_hour13+=$production_hour13;
				$total_hour14+=$production_hour14;
				$total_hour15+=$production_hour15;
				$total_hour16+=$production_hour16;
				$total_hour17+=$production_hour17;
				$total_hour18+=$production_hour18;
				$total_hour19+=$production_hour19; 
				$total_hour20+=$production_hour20;
				$total_hour21+=$production_hour21;
				$total_hour22+=$production_hour22;
				$total_hour23+=$production_hour923; 
				$total_before_8am+=$before_8_am;
				$total_capacity+=$prod_resource_array[$l_id][$pr_date]['capacity'];
				$gnd_total_tgt_h+=$prod_resource_array[$l_id][$pr_date]['terget_hour'];	
				$total_working_hour+=$prod_resource_array[$l_id][$pr_date]['working_hour']; 
				$total_operator+=$prod_resource_array[$l_id][$pr_date]['operator'];
				$total_helper+=$prod_resource_array[$l_id][$pr_date]['helper'];
				$total_man_power+=$prod_resource_array[$l_id][$pr_date]['man_power'];
				//$total_smv+=$item_smv;
				$total_terget+=$eff_target;
				$grand_total_product+=$today_product;
				$gnd_avable_min+=$efficiency_min;
				$gnd_product_min+=$produce_minit; 
				$gnd_fob_val+=$prod_fob_val; 
				//$gnd_hit_rate=($grand_total_product/$total_terget)*100;
				//$gnd_line_effi=($gnd_product_min/$gnd_avable_min)*100;
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$html.='<tbody>';
				$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
				$html.='<td width="40">'.$i.'&nbsp;</td>
							<td width="80">'.$floor_name.'&nbsp; </td>
							<td align="center" width="80">'.$sewing_line.'&nbsp; </td>
							<td width="80"><p>'.$buyer_name.'&nbsp;</p></td>
							<td width="140"><p>'.$style_ref.'&nbsp;</p></td>
							<td width="140"><p>'.$ldata['po_number'].'&nbsp;</p></td>
							<td width="120"><p>'.$garment_itemname.'&nbsp;<p/> </td>
							
							<td align="center" width="60"><p>'.chop($set_smv_all,',').'</p></td>
							<td align="right" width="70">'.$prod_resource_array[$l_id][$pr_date]['operator'].'</td>
							<td align="right" width="50">'.$prod_resource_array[$l_id][$pr_date]['helper'].'</td>
							<td align="right" width="60">'.$prod_resource_array[$l_id][$pr_date]['man_power'].'</td>
							<td align="right" width="70">'.$prod_resource_array[$l_id][$pr_date]['terget_hour'].'</td>
							<td align="right" width="60"><p>'.$days_run.'</p></td>
							<td align="right" width="70">'.$prod_resource_array[$l_id][$pr_date]['capacity'].'</td>
							<td align="right" width="60">'.$prod_resource_array[$l_id][$pr_date]['working_hour'].'</td>
							<td align="right" width="60">'.$cla_cur_time.'</td>
							<td align="right" width="60">'.$production_data_arr[$f_id][$l_id]['production_hour'].'</td>
							<td align="right" width="80">'.number_format($eff_target,0).'</td>
							
							<td align="right" width="80">
								<a href="##" onclick="openmypage3('."'".$cbo_company_id."'".",'".$order_no_total."','".$f_id."','".$l_id."','tot_prod_hangtag','".$smv_for_item."',".$txt_date.',15,'."'".implode(',',$garment_item_id_arr)."'".')">'.$today_product.'</a>
							</td>
							<td align="right" width="80">
								<a href="##" onclick="openmypage3('."'".$cbo_company_id."'".",'".$order_no_total."','".$f_id."','".$l_id."','tot_fob_val_hangtag','".$smv_for_item."',".$txt_date.',15,'."'".implode(',',$garment_item_id_arr)."'".')">'.number_format($prod_fob_val,2).'</a>
							</td>
							
							<td align="right" width="80">'.number_format(($today_product-$eff_target),0).'</td>
							<td align="right" width="100">'.number_format($efficiency_min,2).'</td>
							<td align="right" width="100"><a href="##" onclick="openmypage3('."'".$cbo_company_id."'".",'".$order_no_total."','".$f_id."','".$l_id."','tot_prod_hangtag','".$smv_for_item."',".$txt_date.',15,'."'".implode(',',$garment_item_id_arr)."'".')">'.number_format($produce_minit,2).'</a></td>
							
							<td align="right" width="60">'.number_format(($today_product/$eff_target)*100,2).' %</td>
							<td align="right" width="90">'.number_format($line_efficiency,2). '%</td>
							<td align="right" width="50">'.$production_hour9.'</td>
							<td align="right" width="50">'.$production_hour10.'</td>
							<td align="right" width="50">'.$production_hour11.'</td>
							<td align="right" width="50">'.$production_hour12.'</td>
							<td align="right" width="50">'.$production_hour13.'</td>
							<td align="right" width="50">'.$production_hour14.'</td>
							<td align="right" width="50">'.$production_hour15.'</td>
							<td align="right" width="50">'.$production_hour16.'</td>
							<td align="right" width="50">'.$production_hour17.'</td>
							<td align="right" width="50">'.$production_hour18.'</td>
							<td align="right" width="50">'.$production_hour19.'</td>
							<td align="right" width="50">'.$production_hour20.'</td>
							<td align="right" width="50">'.$production_hour21.'</td>
							<td align="right" width="50">'.$production_hour22.'</td>
							<td align="right" width="50">'.$production_hour23.'</td>
							<td align="right" >'.$production_hour24.'</td>
						</tr>
					</tbody>';
				$i++;
				$check_arr[]=$f_id;
			}
		}
		$html.='<tr  bgcolor="#B6B6B6">
			<td >&nbsp;</td>
			<td>&nbsp;</td>
			<td >&nbsp;</td>
			<td >&nbsp;</td>
			<td >&nbsp;</td>
			<td >&nbsp;</td>
			<td align="right">Sub Total:</td>
			<td align="right">&nbsp;</td>
			<td align="right" >'.$floor_operator.'</td>
			<td align="right" >'.$floor_helper.'</td>
			<td align="right" >'.$floor_man_power.'</td>
			<td align="right" >'.$floor_tgt_h.'</td>
			<td align="right" >'.$floor_days_run.'</td>
			<td align="right" >&nbsp;</td>
			<td align="right" >'.number_format($floor_working_hour,2).'</td>
			<td align="right">&nbsp;</td>
			<td align="right" >&nbsp;</td>
			<td align="right" >'.number_format($floor_ttl_tgt,0).'</td>
			<td align="right">'.$floor_today_product.'</td>
			<td align="right">'.number_format($floor_fob_val,2).'</td>
			<td align="right" >'.number_format(($floor_today_product-$floor_ttl_tgt),2).'</td>
			<td align="right">'.$floor_avale_minute.'</td>
			<td align="right">'.number_format($floor_produc_min,2).'</td>
			<td align="right" >'.number_format(($floor_today_product/$floor_ttl_tgt)*100,2).'%</td>
			<td align="right" >'.number_format($floor_efficency,2).' %</td>
			<td align="right">'.$floor_hour9.'</td>
			<td align="right">'.$floor_hour10.'</td>
			<td align="right">'.$floor_hour11.'</td>
			<td align="right">'.$floor_hour12.'</td>
			<td align="right">'.$floor_hour13.'</td>
			<td align="right">'.$floor_hour14.'</td>
			<td align="right">'.$floor_hour15.'</td>
			<td align="right">'.$floor_hour16.'</td>
			<td align="right">'.$floor_hour17.'</td>
			<td align="right">'.$floor_hour18.'</td>
			<td align="right">'.$floor_hour19.'</td>
			<td align="right">'.$floor_hour20.'</td>
			<td align="right">'.$floor_hour21.'</td>
			<td align="right">'.$floor_hour22.'</td>
			<td align="right">'.$floor_hour23.'</td>
			<td align="right">'.$floor_hour24.'</td>
		</tr>';
		
		
		
		
		$floor_html.="<tr bgcolor='$bgcolor' onclick=change_color('floor_$j','$bgcolor') id=floor_$j>";
		$floor_html.='<td width="40">'.$j.'&nbsp;</td>
			<td width="80" align="center">'.$floor_name.'&nbsp; </td>
			<td width="70" align="right">'.$floor_tgt_h.'</td>
			<td width="70" align="right">'.$floor_capacity.'</td>
			<td width="60" align="right">'.$floor_man_power.'</td>
			<td width="70" align="right">'.$floor_operator.'</td>
			<td width="50" align="right">'.$floor_helper.'</td>
			<td align="right" width="60">'.number_format($floor_working_hour,2).'</td>
			<td align="right" width="80">'.number_format($floor_ttl_tgt,0).'</td>
			<td align="right" width="80">'.$floor_today_product.'</td>
			<td align="right" width="80">'.number_format($floor_fob_val,2).'</td>
			<td align="right" width="80">'.number_format(($floor_today_product-$floor_ttl_tgt),0).'</td>
			<td align="right" width="100">'.$floor_avale_minute.'</td>
			<td align="right" width="100">'.number_format($floor_produc_min,2).'</td>
			<td align="right" width="90">'.number_format(($floor_today_product/$floor_ttl_tgt)*100,2).'%</td>
			<td align="right" width="90">'.number_format($floor_efficency,2).'%</td>
			
			<td align="right" width="50">'.$floor_hour9.'</td>
			<td align="right" width="50">'.$floor_hour10.'</td>
			<td align="right" width="50">'.$floor_hour11.'</td>
			<td align="right" width="50">'.$floor_hour12.'</td>
			<td align="right" width="50">'.$floor_hour13.'</td>
			<td align="right" width="50">'.$floor_hour14.'</td>
			<td align="right" width="50">'.$floor_hour15.'</td>
			<td align="right" width="50">'.$floor_hour16.'</td>
			<td align="right" width="50">'.$floor_hour17.'</td>
			<td align="right" width="50">'.$floor_hour18.'</td>
			<td align="right" width="50">'.$floor_hour19.'</td>
			<td align="right" width="50">'.$floor_hour20.'</td>
			<td align="right" width="50">'.$floor_hour21.'</td>
			<td align="right" width="50">'.$floor_hour22.'</td>
			<td align="right">'.$floor_hour23.'</td>
			<td align="right" width="50">'.$floor_hour24.'</td>
		</tr></tbody>';
		$smv_for_item="";
		?>
		<fieldset style="width:2760px">
			<table width="2280" cellpadding="0" cellspacing="0"> 
				<tr class="form_caption">
					<td colspan="28" align="center"><strong><? echo $report_title; ?></strong></td> 
				</tr>
				<tr class="form_caption">
					<td colspan="28" align="center"><strong><? echo $companyArr[$comapny_id]; ?></strong></td> 
				</tr>
				<tr class="form_caption">
					<td colspan="28" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date)) ); ?></strong></td> 
				</tr>
			</table>
			<br />
			<label><strong>Report Sumarry:-</strong></label> 
			<table id="table_header_2" class="rpt_table" width="2030" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr height="50">
						<th width="40">SL</th>
						<th width="80">Floor Name</th>
						<th width="70">Hourly Terget</th>
						<th width="70">Capacity</th>
						<th width="60">Total Man  Power</th>
						<th width="70">Operator</th>
						<th width="50">Helper</th>
						<th width="60">Line Hour</th>
						<th width="80">Day Target</th>
						<th width="80">Total Prod.</th>
						<th width="80">Total Production FOB Value</th>
						<th width="80">Variance </th>
						<th width="100">SMV Available</th>
						<th width="100">SMV Achieved</th>
						<th width="90">Achievement %</th>
						<th width="90">Floor Eff. %</th>
						<th width="50" style="vertical-align:middle"><div class="block_div">9 AM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">10 AM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">11 AM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">12 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">1 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">2 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">3 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">4 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">5 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">6 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">7 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">8 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">9 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">10 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">11 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">12 AM</div></th>
					</tr>
				</thead>
			</table>
			<div style="width:2048px; max-height:400px; overflow-y:scroll" id="scroll_body_1">
				<table class="rpt_table" width="2030" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
					<? echo $floor_html; ?> 
					<tfoot>
						<tr>
							<th width="40">&nbsp;</th>
							<th width="80" align="right"> Total: </th>
							<th width="70"><? echo $gnd_total_tgt_h;   ?> </th>
							<th width="70" align="right"><? echo $total_capacity; ?> </th>
							<th width="60"><? echo $total_man_power; ?></th>
							<th width="70"><? echo $total_operator; ?></th>
							<th width="50"><? echo $total_helper; ?></th>
							<th align="right" width="60"><? echo number_format($total_working_hour,2); ?></th>
							<th align="right" width="80"><? echo number_format($total_terget,0); ?></th>
							<th align="right" width="80"><? echo $grand_total_product; ?></th>
							<th align="right" width="80"><? echo number_format($gnd_fob_val,2); ?></th>
							<th align="right" width="80"><? echo number_format(($grand_total_product-$total_terget),0); ?></th>
							<th align="right" width="100"><? echo $gnd_avable_min; ?></th>
							<th align="right" width="100"><? echo number_format($gnd_product_min,2); ?></th>
							<th align="right" width="90"><? echo number_format(($grand_total_product/$total_terget)*100,2)."%"; ?></th>
							<th align="center" width="90"><?  echo number_format(($gnd_product_min/$gnd_avable_min)*100,2)."%"; ?></th>
							<th align="right" width="50"><? echo $total_hour9; ?></th>
							<th align="right" width="50"><? echo $total_hour10; ?></th>
							<th align="right" width="50"><? echo $total_hour11; ?></th>
							<th align="right" width="50"><? echo $total_hour12; ?></th>
							<th align="right" width="50"><? echo $total_hour13; ?></th>
							<th align="right" width="50"><? echo $total_hour14; ?></th>
							<th align="right" width="50"><? echo $total_hour15; ?></th>
							<th align="right" width="50"><? echo $total_hour16; ?></th>
							<th align="right" width="50"><? echo $total_hour17; ?></th>
							<th align="right" width="50"><? echo $total_hour18; ?></th>
							<th align="right" width="50"><? echo $total_hour19; ?></th>
							<th align="right" width="50"><? echo $total_hour20; ?></th>
							<th align="right" width="50"><? echo $total_hour21; ?></th>
							<th align="right" width="50"><? echo $total_hour22; ?></th>
							<th align="right" width="50"><? echo $total_hour23; ?></th>
							<th align="right" ><? echo $total_hour24; ?></th>
						</tr>
					</tfoot>
				</table>
			</div>
			</br><br/>
			<!-- ================================== details part ============================== -->
			<table id="table_header_1" class="rpt_table" width="2840" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr height="50">
						<th width="40">SL</th>
						<th width="80">Floor Name </th>
						<th width="80">Line No</th>
						<th width="80">Buyer</th>
	                    <th width="140">Style Ref.</th>
						<th width="140">Order No</th>
						<th width="120">Garments Item</th>
						<th width="60">SMV</th>
						<th width="70">Operator</th>
						<th width="50">Helper</th>
						<th width="60"> Man Power</th>
						<th width="70">Hourly Terget</th>
						<th width="60">Days Run</th>
						<th width="70">Capacity</th>
						<th width="60">Working Hour</th>
	                    <th width="60">Current Hour</th>
                        <th width="60">Production Hour</th>
						<th width="80">Total Target</th>
						<th width="80">Total Prod.</th>
						<th width="80">Total Production FOB Vale</th>
						<th width="80">Variance pcs </th>
						<th width="100">Available Minutes</th>
						<th width="100">Produce Minutes</th>
						<th width="60">Target Hit rate</th>
						<th width="90">Line Effi %</th>
						<th width="50" style="vertical-align:middle"><div class="block_div">9 AM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">10 AM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">11 AM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">12 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">1 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">2 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">3 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">4 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">5 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">6 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">7 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">8 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">9 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">10 PM</div></th>
						<th width="50" style="vertical-align:middle"><div class="block_div">11 PM</div></th>
						<th style="vertical-align:middle"><div class="block_div">12 AM</div></th>
					</tr>
				</thead>
			</table>
			<div style="width:2860px; max-height:400px; overflow-y:scroll" id="scroll_body_2">
				<table class="rpt_table" width="2840" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<? echo $html;  ?>
					<tfoot>
						<tr>
							<th width="40">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="140">&nbsp;</th>
	                        <th width="140">&nbsp;</th>
							<th width="120" align="right">Grand Total:</th>
							<th align="right" width="60"><? // echo number_format($total_smv/($i-1),2); ?>&nbsp;</th>
							<th align="right" width="70"><? echo $total_operator; ?></th>
							<th align="right" width="50"><? echo $total_helper; ?></th>
							<th align="right" width="60"><? echo $total_man_power; ?></th>
							<th align="right" width="70"><?  echo $gnd_total_tgt_h; ?></th>
							<th align="right" width="60"><? //echo $prod_resource_array[$row[csf('sewing_line')]][$pr_date]['man_power']; ?>&nbsp;</th>
							<th align="right" width="70"><? echo $total_capacity; ?></th>
							<th align="right" width="60"><? echo number_format($total_working_hour,2); ?></th>
	                        <th align="right" width="60">&nbsp;</th>
	                        <th align="right" width="60">&nbsp;</th>
							<th align="right" width="80"><? echo number_format($total_terget,0); ?></th>
							<th align="right" width="80"><? echo $grand_total_product; ?></th>
							<th align="right" width="80"><? echo number_format($gnd_fob_val,2); ?></th>
							<th align="right" width="80"><? echo number_format(($grand_total_product-$total_terget),0); ?></th>
							<th align="right" width="100"><? echo $gnd_avable_min; ?></th>
							<th align="right" width="100"><? echo number_format($gnd_product_min,2); ?></th>
							<th align="right" width="60"><? echo number_format(($grand_total_product/$total_terget)*100,2)."%"; ?></th>
							<th align="right" width="90" ><? echo number_format(($gnd_product_min/$gnd_avable_min)*100,2)."%";?></th>
                            
							<th align="right" width="50"><? echo $total_hour9; ?></th>
							<th align="right" width="50"><? echo $total_hour10; ?></th>
							<th align="right" width="50"><? echo $total_hour11; ?></th>
							<th align="right" width="50"><? echo $total_hour12; ?></th>
							<th align="right" width="50"><? echo $total_hour13; ?></th>
							<th align="right" width="50"><? echo $total_hour14; ?></th>
							<th align="right" width="50"><? echo $total_hour15; ?></th>
							<th align="right" width="50"><? echo $total_hour16; ?></th>
							<th align="right" width="50"><? echo $total_hour17; ?></th>
							<th align="right" width="50"><? echo $total_hour18; ?></th>
							<th align="right" width="50"><? echo $total_hour19; ?></th>
							<th align="right" width="50"><? echo $total_hour20; ?></th>
							<th align="right" width="50"><? echo $total_hour21; ?></th>
							<th align="right" width="50"><? echo $total_hour22; ?></th>
							<th align="right" width="50"><? echo $total_hour23; ?></th>
							<th><? echo $total_hour24; ?></th>
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
		exit();
	}
}


 
if($action=="tot_prod")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$color_lib=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
	// print_r($color_lib);
	if($db_type==0)
	{
		$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id  and pr_date='".$prod_date."'","line_start_time");	
	}//and  a.company_id=$comapny_id and shift_id=1
	else if($db_type==2)
	{
		$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and pr_date='".$prod_date."'","line_start_time");
	}//
	
	if($min_shif_start=="")
	{
		echo "<p style='font-size:20px', align='center'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<p/>";die;
		
	}
	
	if($db_type==2)
	{
		$pr_date=str_replace("'","",$prod_date);
		$pr_date_old=explode("-",str_replace("'","",$prod_date));
		$month=strtoupper($pr_date_old[1]);
		$year=substr($pr_date_old[2],2);
		$pr_date=$pr_date_old[0]."-".$month."-".$year;
	}
	else if($db_type==0)
	{
		$pr_date=str_replace("'","",$prod_date);
	}
	//echo $pr_date;die; 
	$prod_start_hour="08:00";
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
	
	
	$first_hour_time=explode(":",$min_shif_start);
	$hour_line=substr($first_hour_time[0],1,1); $minutes_one=$start_time[1];
	$line_start_hour_arr[$hour_line]=$min_shif_start;
	
	for($l=$hour_line;$l<$last_hour;$l++)
	{
		$min_shif_start=add_time($min_shif_start,60);
		$line_start_hour_arr[$l+1]=substr($min_shif_start,0,5);
	}
	
	$line_start_hour_arr[$j+1]='23:59';

	$production_hour="TO_CHAR(production_hour,'HH24:MI')";
    $sql_pop="select b.style_ref_no, c.po_number, a.po_break_down_id,f.color_number_id as color_id, sum(d.production_qnty) as good_qnty";
	$first=1;
	for($h=$hour;$h<$last_hour;$h++)
	{
		$bg=$start_hour_arr[$h];
		$end=substr(add_time($start_hour_arr[$h],60),0,5);
		$prod_hour="good_".substr($bg,0,2);
		if($first==1)
		{
			$sql_pop.=", sum(CASE WHEN $production_hour<='$end' and a.production_type=$pro_type and d.production_type=$pro_type  THEN d.production_qnty else 0 END) AS $prod_hour";
		}
		else
		{
			$sql_pop.=", sum(CASE WHEN $production_hour>'$bg' and $production_hour<='$end' and a.production_type=$pro_type and d.production_type=$pro_type  THEN d.production_qnty else 0 END) AS $prod_hour";
		}
		$first=$first+1;
	}
	 

		$sql_pop.=", sum(CASE WHEN $production_hour>'$start_hour_arr[$last_hour]' and $production_hour<='$start_hour_arr[24]' and a.production_type=$pro_type and d.production_type=$pro_type  THEN d.production_qnty else 0 END) AS prod_hour23  from pro_garments_production_mst a,pro_garments_production_dtls d, wo_po_details_master b, wo_po_break_down c,wo_po_color_size_breakdown f
		where a.id=d.mst_id and d.production_type=$pro_type and f.id=d.color_size_break_down_id and d.status_active=1 and d.is_deleted=0 and  a.production_type=$pro_type and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and c.id=f.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active in (1,2,3) and c.is_deleted=0 and f.status_active in (1,2,3) and f.is_deleted=0  and c.is_deleted=0  and a.serving_company in($company_id)  and a.floor_id=".$floor_id." and a.sewing_line=".$sewing_line."  and a.po_break_down_id in(".$po_id.") and a.production_date='".$prod_date."'  group by b.style_ref_no, c.po_number,a.po_break_down_id,f.color_number_id order by  c.po_number,f.color_number_id ";
		
		 // echo $sql_pop;
		//================================= subcontact =====================================
		if($db_type==2)
		{
			$production_hour_subcon="TO_CHAR(hour,'HH24:MI')";
		}
		else
		{
			$production_hour_subcon=" hour ";
		}
		$sql_query_subcon="SELECT  c.cust_style_ref as style_ref_no, 00 as po_break_down_id,c.smv as sub_smv, c.order_no as po_number,e.color_id as color_id,
		sum(d.prod_qnty) as good_qnty";
		$first=1;
		$subcon_type=$pro_type;
		$sub_type=($subcon_type==11)?"5" : "2";
		for($h=$hour;$h<$last_hour;$h++)
		{
			$bg=$start_hour_arr[$h];
			$ends=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="good_".substr($bg,0,2);
			if($first==1)
			{
				$sql_query_subcon.=", sum(CASE WHEN $production_hour_subcon<'$ends' and d.production_type=$sub_type THEN d.prod_qnty else 0 END) AS $prod_hour";
			}
			else
			{
				$sql_query_subcon.=", sum(CASE WHEN $production_hour_subcon>='$bg' and $production_hour_subcon<'$ends' and d.production_type=$sub_type THEN d.prod_qnty else 0 END) AS $prod_hour";
			}
			$first=$first+1;
		}
		$sql_query_subcon.=", sum(CASE WHEN $production_hour_subcon>='$start_hour_arr[$last_hour]' and $production_hour_subcon<='$start_hour_arr[24]' and d.production_type=$sub_type THEN d.prod_qnty else 0 END) AS good_23 from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d, subcon_ord_mst b, subcon_ord_dtls c,subcon_ord_breakdown e
		where a.production_type=$sub_type and d.production_type=$sub_type and a.id=d.dtls_id and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and e.id=d.ord_color_size_id and e.order_id=c.id and a.order_id=e.order_id AND a.company_id in(".str_replace("'","",$company_id).")  and a.floor_id in(".$floor_id.") and a.line_id in(".$sewing_line.")   and a.production_date='".$prod_date."'
		group by c.cust_style_ref, c.order_no,c.smv,e.color_id order by c.order_no,e.color_id";

	  // echo $sql_query_subcon;die;
		
	$sql_res=sql_select($sql_pop);
	$sql_subcon=sql_select($sql_query_subcon);
	if( count($sql_res)>0 && count($sql_subcon)>0)
	{
		$sql_res=array_merge($sql_res, $sql_subcon);
	}
	else if(count($sql_res)<=0 && count($sql_subcon)>0)
	{
		$sql_res=$sql_subcon;
	}        	
	
	$new_smv=array();
	$item_smv_pop=explode("****",$item_smv);
	$order_id="";
	foreach($item_smv_pop as $po_id_smv) 
	{
		$po_id_smv_pop=explode("**",$po_id_smv);
		$new_smv[$po_id_smv_pop[0]]=$po_id_smv_pop[1];
		/*  if($order_id!="") $order_id.=",".$po_id_smv_pop[0]; 
		else $order_id=$po_id_smv_pop[0]; */
	}
 //print_r($item_smv_pop);die;


	$order_data_array = array();
	$order_color_data_array = array();
	foreach($sql_res as $row)
	{

		$order_data_array[$row['PO_BREAK_DOWN_ID']]['style_ref_no'] = $row['STYLE_REF_NO'];
		$order_data_array[$row['PO_BREAK_DOWN_ID']]['po_number'] = $row['PO_NUMBER'];
		$order_data_array[$row['PO_BREAK_DOWN_ID']]['good_qnty'] += $row['GOOD_QNTY'];
		$order_data_array[$row['PO_BREAK_DOWN_ID']]['item_smv'] = $new_smv[$row[csf('po_break_down_id')]];
		//$order_data_array[$row['PO_BREAK_DOWN_ID']]['produce_minute'] += $row['GOOD_QNTY']*$item_smv_array[$row[csf('po_break_down_id')]][];
		
		if($row[csf('po_break_down_id')]=="00")
		{
			$order_data_array[$row['PO_BREAK_DOWN_ID']]['item_smv'] = $row[csf('sub_smv')];
		}
		//================================================================
		$order_color_data_array[$row['PO_BREAK_DOWN_ID']][$row['COLOR_ID']]['style_ref_no'] = $row['STYLE_REF_NO'];
		$order_color_data_array[$row['PO_BREAK_DOWN_ID']][$row['COLOR_ID']]['po_number'] = $row['PO_NUMBER'];
		$order_color_data_array[$row['PO_BREAK_DOWN_ID']][$row['COLOR_ID']]['good_qnty'] = $row['GOOD_QNTY'];
		$order_color_data_array[$row['PO_BREAK_DOWN_ID']][$row['COLOR_ID']]['item_smv'] = $new_smv[$row[csf('po_break_down_id')]];
		if($row[csf('po_break_down_id')]=="00")
		{
			$order_color_data_array[$row['PO_BREAK_DOWN_ID']][$row['COLOR_ID']]['item_smv'] = $row[csf('sub_smv')];
		}
		
		
		
		$style_wise_po_arr[$row['STYLE_REF_NO']][$row['PO_BREAK_DOWN_ID']]=$row['PO_BREAK_DOWN_ID'];
		$all_style_arr[$row['STYLE_REF_NO']]=$row['STYLE_REF_NO'];
		$all_po_arr[$row['PO_BREAK_DOWN_ID']]=$row['PO_BREAK_DOWN_ID'];
	}
	//====================================
	$rowspan_arr = array();
	foreach ($order_color_data_array as $po_id => $po_data) 
	{
		foreach ($po_data as $color_id => $row) 
		{
			$rowspan_arr[$po_id]++;
		}		
	}
	
	
	
	$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($company_id) and variable_list=25 and status_active=1 and is_deleted=0");
	//echo $smv_source;
		if($smv_source==3)
		{
			//$style_wise_po_arr[$row['STYLE_REF_NO']][$row['PO_BREAK_DOWN_ID']]=$row['PO_BREAK_DOWN_ID'];
			$gsdSql="select a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID  from PPL_GSD_ENTRY_MST a where a.APPLICABLE_PERIOD <= '$prod_date' and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and a.BULLETIN_TYPE in(3,4) ".where_con_using_array($all_style_arr,1,'A.STYLE_REF')." and a.APPROVED=1  and a.GMTS_ITEM_ID in($item_id)
			group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID
			 ORDER BY a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC";
			   
			    //echo $gsdSql; 
			$gsdSqlResult = sql_select($gsdSql); 
			 
			foreach($gsdSqlResult as $rows)
			{
				foreach($style_wise_po_arr[$rows[STYLE_REF]] as $po_id)
				{
					if($item_smv_array[$po_id][$rows[STYLE_REF]]==''){
						$item_smv_array[$po_id][$rows[STYLE_REF]]=$rows[TOTAL_SMV];
					}
				}
			}
			
		}
		else
		{
			$sql_item="select b.id, a.set_break_down, a.STYLE_REF_NO, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no ".where_con_using_array($all_po_arr,0,'b.id')."  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3)";
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				if($smv_source==1)
				{
					$item_smv_array[$itemData[csf('id')]][$itemData[csf('STYLE_REF_NO')]]=$itemData[csf('smv_pcs')];
				}
				else if($smv_source==2)
				{
					$item_smv_array[$itemData[csf('id')]][$itemData[csf('STYLE_REF_NO')]]=$itemData[csf('smv_pcs_precost')];
				}
			}
		}	
	
	
 	
	
	
	
	 //print_r($item_smv_array);die();
	?>
	<fieldset style="width:620px; ">
		<div style="text-align: center;padding: 5px;">
			<script>
				function new_window()
				{					
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('report_container').innerHTML);
					d.close();
				}
			</script>
			<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
		</div>
        <div id="report_container">
        	<!-- ======================== SUMMARY PART ======================= -->
        	<table border="1" class="rpt_table" rules="all" width="600" cellpadding="0" cellspacing="0" align="center">
	        	<caption><b>Summary</b></caption>
	            <thead>
	                <th width="30">SL</th>
	                <th width="120">Style Ref.</th>
	                <th width="120">Order No</th>
	                <th width="70">Item Smv</th>
	                <th width="80">Production Qty</th>
	                <th>Produced Min.</th>
	            </thead>
				<?

	        
				$total_producd_min=0;
				$i=1; $total_qnty=0;
				foreach($order_data_array as $po_id=>$row)
				{
					$style_ref_no=$row['style_ref_no'];
					$po_number=$row['po_number'];
					//$item_smv=$row['item_smv'];
					$po_qty=$row['good_qnty'];

					$item_smv=$item_smv_array[$po_id][$row['style_ref_no']];
					
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
	                    <td width="30"><? echo $i; ?></td>
	                    <td width="120" align="center"><? echo $style_ref_no; ?></td>
	                    <td width="120" align="center"><? echo $po_number; ?></td>
	                    <td align="right"><? echo number_format($item_smv,2); ?></td>
	                    <td align="right"><? $total_po_qty+=$po_qty; echo $po_qty; ?></td>
	                    <td align="right">
	                    <?
	                    $producd_min=$po_qty*$item_smv;  $total_producd_min+=$producd_min;
	                    echo number_format($po_qty*$item_smv,2);
	                    ?>&nbsp;</td>
	                    </tr>
					<?
					$i++;
				}
				?>
				<tfoot>
	                <th colspan="4" align="right">Total</th>
	                <th align="right"><? echo $total_po_qty; ?>&nbsp;</th>
	                <th align="right"><? echo number_format($total_producd_min,2); ?>&nbsp;</th>
	            </tfoot>
	        </table>
        	<!-- ======================== DETAILS PART ======================= -->
	        <table border="1" class="rpt_table" rules="all" width="600" cellpadding="0" cellspacing="0" align="center">
	        	<caption><b>Order & Color Wise Details</b></caption>
	            <thead>
	                <th width="30">SL</th>
	                <th width="120">Style Ref.</th>
	                <th width="120">Order No</th>
	                <th width="70">Item Smv</th>
	                <th width="80">Color</th>
	                <th>Production Qty</th>
	            </thead>
				<?

	        
				$total_producd_min=0;
				$i=1; $total_po_qty=0;
				foreach($order_color_data_array as $po_id=>$po_data)
				{
					$order_total = 0;
					$r=0;
					foreach($po_data as $color_id=>$row)
					{	
						$style_ref_no=$row['style_ref_no'];
						$po_number=$row['po_number'];
						//$item_smv=$row['item_smv'];
						$po_qty=$row['good_qnty'];
						$item_smv=$item_smv_array[$po_id][$row['style_ref_no']];
						
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<? if($r==0){?>
		                    <td valign="middle" rowspan="<? echo $rowspan_arr[$po_id];?>" width="30"><? echo $i; ?></td>
		                    <td valign="middle" rowspan="<? echo $rowspan_arr[$po_id];?>" width="120" align="center"><? echo $style_ref_no; ?></td>
		                    <td valign="middle" rowspan="<? echo $rowspan_arr[$po_id];?>" width="120" align="center"><? echo $po_number; ?></td>
		                    <td valign="middle" rowspan="<? echo $rowspan_arr[$po_id];?>" align="center"><? echo number_format($item_smv,2); ?>&nbsp;</td>
		                    <?}?>
		                    <td align="left"><? echo $color_lib[$color_id]; ?>&nbsp;</td>
		                    <td align="right"><?  echo $po_qty; ?>&nbsp;</td>
		                </tr>
						<?
						$i++;
						$r++;
						$order_total += $po_qty;
						$total_po_qty+=$po_qty;
					}
					?>
					<tr style="background-color: #dccdcd;">
	                	<th colspan="5" align="right">Order Total</th>
	                	<th align="right"><? echo $order_total; ?>&nbsp;</th>
	            	</tr>
					<?
				}
				?>
				<tfoot>
	                <th colspan="5" align="right">Total</th>
	                <th align="right"><? echo $total_po_qty; ?>&nbsp;</th>
	            </tfoot>
	        </table>
        </div>
	</fieldset>   
	<?
	exit();
}
if($action=="tot_prod_hangtag")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$color_lib=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
	// print_r($color_lib);
	if($db_type==0)
	{
		$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id  and pr_date='".$prod_date."'","line_start_time");	
	}//and  a.company_id=$comapny_id and shift_id=1
	else if($db_type==2)
	{
		$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and pr_date='".$prod_date."'","line_start_time");
	}//
	
	if($min_shif_start=="")
	{
		echo "<p style='font-size:20px', align='center'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<p/>";die;
		
	}
	
	if($db_type==2)
	{
		$pr_date=str_replace("'","",$prod_date);
		$pr_date_old=explode("-",str_replace("'","",$prod_date));
		$month=strtoupper($pr_date_old[1]);
		$year=substr($pr_date_old[2],2);
		$pr_date=$pr_date_old[0]."-".$month."-".$year;
	}
	else if($db_type==0)
	{
		$pr_date=str_replace("'","",$prod_date);
	}
	//echo $pr_date;die; 
	$prod_start_hour="08:00";
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
	
	
	$first_hour_time=explode(":",$min_shif_start);
	$hour_line=substr($first_hour_time[0],1,1); $minutes_one=$start_time[1];
	$line_start_hour_arr[$hour_line]=$min_shif_start;
	
	for($l=$hour_line;$l<$last_hour;$l++)
	{
		$min_shif_start=add_time($min_shif_start,60);
		$line_start_hour_arr[$l+1]=substr($min_shif_start,0,5);
	}
	
	$line_start_hour_arr[$j+1]='23:59';

	$production_hour="TO_CHAR(production_hour,'HH24:MI')";
    $sql_pop="select b.style_ref_no, c.po_number, a.po_break_down_id,f.color_number_id as color_id, sum(d.production_qnty) as good_qnty";
	$first=1;
	for($h=$hour;$h<$last_hour;$h++)
	{
		$bg=$start_hour_arr[$h];
		$end=substr(add_time($start_hour_arr[$h],60),0,5);
		$prod_hour="good_".substr($bg,0,2);
		if($first==1)
		{
			$sql_pop.=", sum(CASE WHEN $production_hour<='$end' and a.production_type=$pro_type and d.production_type=$pro_type  THEN d.production_qnty else 0 END) AS $prod_hour";
		}
		else
		{
			$sql_pop.=", sum(CASE WHEN $production_hour>'$bg' and $production_hour<='$end' and a.production_type=$pro_type and d.production_type=$pro_type  THEN d.production_qnty else 0 END) AS $prod_hour";
		}
		$first=$first+1;
	}
	 

		$sql_pop.=", sum(CASE WHEN $production_hour>'$start_hour_arr[$last_hour]' and $production_hour<='$start_hour_arr[24]' and a.production_type=$pro_type and d.production_type=$pro_type  THEN d.production_qnty else 0 END) AS prod_hour23  from pro_garments_production_mst a,pro_garments_production_dtls d, wo_po_details_master b, wo_po_break_down c,wo_po_color_size_breakdown f
		where a.id=d.mst_id and d.production_type=$pro_type and f.id=d.color_size_break_down_id and d.status_active=1 and d.is_deleted=0 and  a.production_type=$pro_type and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and c.id=f.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active in (1,2,3) and c.is_deleted=0 and f.status_active in (1,2,3) and f.is_deleted=0  and c.is_deleted=0  and a.serving_company in($company_id)  and a.floor_id=".$floor_id."   and a.po_break_down_id in(".$po_id.") and a.production_date='".$prod_date."'  group by b.style_ref_no, c.po_number,a.po_break_down_id,f.color_number_id order by  c.po_number,f.color_number_id ";
		
		  //echo $sql_pop;and a.sewing_line=".$sewing_line."
		//================================= subcontact =====================================
		if($db_type==2)
		{
			$production_hour_subcon="TO_CHAR(hour,'HH24:MI')";
		}
		else
		{
			$production_hour_subcon=" hour ";
		}
		$sql_query_subcon="SELECT  c.cust_style_ref as style_ref_no, 00 as po_break_down_id,c.smv as sub_smv, c.order_no as po_number,e.color_id as color_id,
		sum(d.prod_qnty) as good_qnty";
		$first=1;
		$subcon_type=$pro_type;
		$sub_type=($subcon_type==15)?"5" : "2";
		for($h=$hour;$h<$last_hour;$h++)
		{
			$bg=$start_hour_arr[$h];
			$ends=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="good_".substr($bg,0,2);
			if($first==1)
			{
				$sql_query_subcon.=", sum(CASE WHEN $production_hour_subcon<'$ends' and d.production_type=$sub_type THEN d.prod_qnty else 0 END) AS $prod_hour";
			}
			else
			{
				$sql_query_subcon.=", sum(CASE WHEN $production_hour_subcon>='$bg' and $production_hour_subcon<'$ends' and d.production_type=$sub_type THEN d.prod_qnty else 0 END) AS $prod_hour";
			}
			$first=$first+1;
		}
		$sql_query_subcon.=", sum(CASE WHEN $production_hour_subcon>='$start_hour_arr[$last_hour]' and $production_hour_subcon<='$start_hour_arr[24]' and d.production_type=$sub_type THEN d.prod_qnty else 0 END) AS good_23 from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d, subcon_ord_mst b, subcon_ord_dtls c,subcon_ord_breakdown e
		where a.production_type=$sub_type and d.production_type=$sub_type and a.id=d.dtls_id and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and e.id=d.ord_color_size_id and e.order_id=c.id and a.order_id=e.order_id AND a.company_id in(".str_replace("'","",$company_id).")  and a.floor_id in(".$floor_id.")   and a.production_date='".$prod_date."'
		group by c.cust_style_ref, c.order_no,c.smv,e.color_id order by c.order_no,e.color_id";

	  // echo $sql_query_subcon;die; and a.line_id in(".$sewing_line.")
		
	$sql_res=sql_select($sql_pop);
	$sql_subcon=sql_select($sql_query_subcon);
	if( count($sql_res)>0 && count($sql_subcon)>0)
	{
		$sql_res=array_merge($sql_res, $sql_subcon);
	}
	else if(count($sql_res)<=0 && count($sql_subcon)>0)
	{
		$sql_res=$sql_subcon;
	}        	
	
	$new_smv=array();
	$item_smv_pop=explode("****",$item_smv);
	$order_id="";
	foreach($item_smv_pop as $po_id_smv) 
	{
		$po_id_smv_pop=explode("**",$po_id_smv);
		$new_smv[$po_id_smv_pop[0]]=$po_id_smv_pop[1];
		/*  if($order_id!="") $order_id.=",".$po_id_smv_pop[0]; 
		else $order_id=$po_id_smv_pop[0]; */
	}
 //print_r($item_smv_pop);die;


	$order_data_array = array();
	$order_color_data_array = array();
	foreach($sql_res as $row)
	{

		$order_data_array[$row['PO_BREAK_DOWN_ID']]['style_ref_no'] = $row['STYLE_REF_NO'];
		$order_data_array[$row['PO_BREAK_DOWN_ID']]['po_number'] = $row['PO_NUMBER'];
		$order_data_array[$row['PO_BREAK_DOWN_ID']]['good_qnty'] += $row['GOOD_QNTY'];
		$order_data_array[$row['PO_BREAK_DOWN_ID']]['item_smv'] = $new_smv[$row[csf('po_break_down_id')]];
		//$order_data_array[$row['PO_BREAK_DOWN_ID']]['produce_minute'] += $row['GOOD_QNTY']*$item_smv_array[$row[csf('po_break_down_id')]][];
		
		if($row[csf('po_break_down_id')]=="00")
		{
			$order_data_array[$row['PO_BREAK_DOWN_ID']]['item_smv'] = $row[csf('sub_smv')];
		}
		//================================================================
		$order_color_data_array[$row['PO_BREAK_DOWN_ID']][$row['COLOR_ID']]['style_ref_no'] = $row['STYLE_REF_NO'];
		$order_color_data_array[$row['PO_BREAK_DOWN_ID']][$row['COLOR_ID']]['po_number'] = $row['PO_NUMBER'];
		$order_color_data_array[$row['PO_BREAK_DOWN_ID']][$row['COLOR_ID']]['good_qnty'] = $row['GOOD_QNTY'];
		$order_color_data_array[$row['PO_BREAK_DOWN_ID']][$row['COLOR_ID']]['item_smv'] = $new_smv[$row[csf('po_break_down_id')]];
		if($row[csf('po_break_down_id')]=="00")
		{
			$order_color_data_array[$row['PO_BREAK_DOWN_ID']][$row['COLOR_ID']]['item_smv'] = $row[csf('sub_smv')];
		}
		
		
		
		$style_wise_po_arr[$row['STYLE_REF_NO']][$row['PO_BREAK_DOWN_ID']]=$row['PO_BREAK_DOWN_ID'];
		$all_style_arr[$row['STYLE_REF_NO']]=$row['STYLE_REF_NO'];
		$all_po_arr[$row['PO_BREAK_DOWN_ID']]=$row['PO_BREAK_DOWN_ID'];
	}
	//====================================
	$rowspan_arr = array();
	foreach ($order_color_data_array as $po_id => $po_data) 
	{
		foreach ($po_data as $color_id => $row) 
		{
			$rowspan_arr[$po_id]++;
		}		
	}
	
	
	
	$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($company_id) and variable_list=25 and status_active=1 and is_deleted=0");
	//echo $smv_source;
		if($smv_source==3)
		{
			//$style_wise_po_arr[$row['STYLE_REF_NO']][$row['PO_BREAK_DOWN_ID']]=$row['PO_BREAK_DOWN_ID'];
			$gsdSql="select a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID  from PPL_GSD_ENTRY_MST a where a.APPLICABLE_PERIOD <= '$prod_date' and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and a.BULLETIN_TYPE in(3,4) ".where_con_using_array($all_style_arr,1,'A.STYLE_REF')." and a.APPROVED=1  and a.GMTS_ITEM_ID in($item_id)
			group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID
			 ORDER BY a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC";
			   
			    //echo $gsdSql; 
			$gsdSqlResult = sql_select($gsdSql); 
			 
			foreach($gsdSqlResult as $rows)
			{
				foreach($style_wise_po_arr[$rows[STYLE_REF]] as $po_id)
				{
					if($item_smv_array[$po_id][$rows[STYLE_REF]]==''){
						$item_smv_array[$po_id][$rows[STYLE_REF]]=$rows[TOTAL_SMV];
					}
				}
			}
			
		}
		else
		{
			$sql_item="select b.id, a.set_break_down, a.STYLE_REF_NO, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no ".where_con_using_array($all_po_arr,0,'b.id')."  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3)";
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				if($smv_source==1)
				{
					$item_smv_array[$itemData[csf('id')]][$itemData[csf('STYLE_REF_NO')]]=$itemData[csf('smv_pcs')];
				}
				else if($smv_source==2)
				{
					$item_smv_array[$itemData[csf('id')]][$itemData[csf('STYLE_REF_NO')]]=$itemData[csf('smv_pcs_precost')];
				}
			}
		}	
	
	
 	
	
	
	
	 //print_r($item_smv_array);die();
	?>
	<fieldset style="width:620px; ">
		<div style="text-align: center;padding: 5px;">
			<script>
				function new_window()
				{					
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('report_container').innerHTML);
					d.close();
				}
			</script>
			<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
		</div>
        <div id="report_container">
        	<!-- ======================== SUMMARY PART ======================= -->
        	<table border="1" class="rpt_table" rules="all" width="600" cellpadding="0" cellspacing="0" align="center">
	        	<caption><b>Summary</b></caption>
	            <thead>
	                <th width="30">SL</th>
	                <th width="120">Style Ref.</th>
	                <th width="120">Order No</th>
	                <th width="70">Item Smv</th>
	                <th width="80">Production Qty</th>
	                <th>Produced Min.</th>
	            </thead>
				<?

	        
				$total_producd_min=0;
				$i=1; $total_qnty=0;
				foreach($order_data_array as $po_id=>$row)
				{
					$style_ref_no=$row['style_ref_no'];
					$po_number=$row['po_number'];
					//$item_smv=$row['item_smv'];
					$po_qty=$row['good_qnty'];

					$item_smv=$item_smv_array[$po_id][$row['style_ref_no']];
					
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
	                    <td width="30"><? echo $i; ?></td>
	                    <td width="120" align="center"><? echo $style_ref_no; ?></td>
	                    <td width="120" align="center"><? echo $po_number; ?></td>
	                    <td align="right"><? echo number_format($item_smv,2); ?></td>
	                    <td align="right"><? $total_po_qty+=$po_qty; echo $po_qty; ?></td>
	                    <td align="right">
	                    <?
	                    $producd_min=$po_qty*$item_smv;  $total_producd_min+=$producd_min;
	                    echo number_format($po_qty*$item_smv,2);
	                    ?>&nbsp;</td>
	                    </tr>
					<?
					$i++;
				}
				?>
				<tfoot>
	                <th colspan="4" align="right">Total</th>
	                <th align="right"><? echo $total_po_qty; ?>&nbsp;</th>
	                <th align="right"><? echo number_format($total_producd_min,2); ?>&nbsp;</th>
	            </tfoot>
	        </table>
        	<!-- ======================== DETAILS PART ======================= -->
	        <table border="1" class="rpt_table" rules="all" width="600" cellpadding="0" cellspacing="0" align="center">
	        	<caption><b>Order & Color Wise Details</b></caption>
	            <thead>
	                <th width="30">SL</th>
	                <th width="120">Style Ref.</th>
	                <th width="120">Order No</th>
	                <th width="70">Item Smv</th>
	                <th width="80">Color</th>
	                <th>Production Qty</th>
	            </thead>
				<?

	        
				$total_producd_min=0;
				$i=1; $total_po_qty=0;
				foreach($order_color_data_array as $po_id=>$po_data)
				{
					$order_total = 0;
					$r=0;
					foreach($po_data as $color_id=>$row)
					{	
						$style_ref_no=$row['style_ref_no'];
						$po_number=$row['po_number'];
						//$item_smv=$row['item_smv'];
						$po_qty=$row['good_qnty'];
						$item_smv=$item_smv_array[$po_id][$row['style_ref_no']];
						
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<? if($r==0){?>
		                    <td valign="middle" rowspan="<? echo $rowspan_arr[$po_id];?>" width="30"><? echo $i; ?></td>
		                    <td valign="middle" rowspan="<? echo $rowspan_arr[$po_id];?>" width="120" align="center"><? echo $style_ref_no; ?></td>
		                    <td valign="middle" rowspan="<? echo $rowspan_arr[$po_id];?>" width="120" align="center"><? echo $po_number; ?></td>
		                    <td valign="middle" rowspan="<? echo $rowspan_arr[$po_id];?>" align="center"><? echo number_format($item_smv,2); ?>&nbsp;</td>
		                    <?}?>
		                    <td align="left"><? echo $color_lib[$color_id]; ?>&nbsp;</td>
		                    <td align="right"><?  echo $po_qty; ?>&nbsp;</td>
		                </tr>
						<?
						$i++;
						$r++;
						$order_total += $po_qty;
						$total_po_qty+=$po_qty;
					}
					?>
					<tr style="background-color: #dccdcd;">
	                	<th colspan="5" align="right">Order Total</th>
	                	<th align="right"><? echo $order_total; ?>&nbsp;</th>
	            	</tr>
					<?
				}
				?>
				<tfoot>
	                <th colspan="5" align="right">Total</th>
	                <th align="right"><? echo $total_po_qty; ?>&nbsp;</th>
	            </tfoot>
	        </table>
        </div>
	</fieldset>   
	<?
	exit();
}

if($action=="tot_prod_min")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$color_lib=return_library_array( "SELECT id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
	// print_r($color_lib);
	if($db_type==0)
	{
		$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id  and pr_date='".$prod_date."'","line_start_time");	
	}//and  a.company_id=$comapny_id and shift_id=1
	else if($db_type==2)
	{
		$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and pr_date='".$prod_date."'","line_start_time");
	}//
	
	if($min_shif_start=="")
	{
		echo "<p style='font-size:20px', align='center'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<p/>";die;
		
	}
	
	if($db_type==2)
	{
		$pr_date=str_replace("'","",$prod_date);
		$pr_date_old=explode("-",str_replace("'","",$prod_date));
		$month=strtoupper($pr_date_old[1]);
		$year=substr($pr_date_old[2],2);
		$pr_date=$pr_date_old[0]."-".$month."-".$year;
	}
	else if($db_type==0)
	{
		$pr_date=str_replace("'","",$prod_date);
	}
	//echo $pr_date;die; 
	$prod_start_hour="08:00";
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
	
	
	$first_hour_time=explode(":",$min_shif_start);
	$hour_line=substr($first_hour_time[0],1,1); $minutes_one=$start_time[1];
	$line_start_hour_arr[$hour_line]=$min_shif_start;
	
	for($l=$hour_line;$l<$last_hour;$l++)
	{
		$min_shif_start=add_time($min_shif_start,60);
		$line_start_hour_arr[$l+1]=substr($min_shif_start,0,5);
	}
	
	$line_start_hour_arr[$j+1]='23:59';

	$production_hour="TO_CHAR(production_hour,'HH24:MI')";
    $sql_pop="select b.style_ref_no, c.po_number, a.po_break_down_id,f.color_number_id as color_id, sum(d.production_qnty) as good_qnty,d.color_type_id,a.item_number_id,b.set_break_down";
	$first=1;
	for($h=$hour;$h<$last_hour;$h++)
	{
		$bg=$start_hour_arr[$h];
		$end=substr(add_time($start_hour_arr[$h],60),0,5);
		$prod_hour="good_".substr($bg,0,2);
		if($first==1)
		{
			$sql_pop.=", sum(CASE WHEN $production_hour<='$end' and a.production_type=$pro_type and d.production_type=$pro_type  THEN d.production_qnty else 0 END) AS $prod_hour";
		}
		else
		{
			$sql_pop.=", sum(CASE WHEN $production_hour>'$bg' and $production_hour<='$end' and a.production_type=$pro_type and d.production_type=$pro_type  THEN d.production_qnty else 0 END) AS $prod_hour";
		}
		$first=$first+1;
	}
	 

		$sql_pop.=", sum(CASE WHEN $production_hour>'$start_hour_arr[$last_hour]' and $production_hour<='$start_hour_arr[24]' and a.production_type=$pro_type and d.production_type=$pro_type  THEN d.production_qnty else 0 END) AS prod_hour23  from pro_garments_production_mst a,pro_garments_production_dtls d, wo_po_details_master b, wo_po_break_down c,wo_po_color_size_breakdown f
		where a.id=d.mst_id and d.production_type=$pro_type and f.id=d.color_size_break_down_id and d.status_active=1 and d.is_deleted=0 and  a.production_type=$pro_type and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and c.id=f.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active in (1,2,3) and c.is_deleted=0 and f.status_active in (1,2,3) and f.is_deleted=0  and c.is_deleted=0  and a.serving_company in($company_id)  and a.floor_id=".$floor_id." and a.sewing_line=".$sewing_line."  and a.po_break_down_id in(".$po_id.") and a.production_date='".$prod_date."'  group by b.style_ref_no, c.po_number,a.po_break_down_id,f.color_number_id,d.color_type_id,a.item_number_id,b.set_break_down order by  c.po_number,f.color_number_id ";
		
		 // echo $sql_pop;
		//================================= subcontact =====================================
		if($db_type==2)
		{
			$production_hour_subcon="TO_CHAR(hour,'HH24:MI')";
		}
		else
		{
			$production_hour_subcon=" hour ";
		}
		$sql_query_subcon="SELECT  c.cust_style_ref as style_ref_no, 00 as po_break_down_id,c.smv as sub_smv, c.order_no as po_number,e.color_id as color_id,a.gmts_item_id as item_number_id,
		sum(d.prod_qnty) as good_qnty";
		$first=1;
		$subcon_type=$pro_type;
		$sub_type=($subcon_type==11)?"5" : "2";
		for($h=$hour;$h<$last_hour;$h++)
		{
			$bg=$start_hour_arr[$h];
			$ends=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="good_".substr($bg,0,2);
			if($first==1)
			{
				$sql_query_subcon.=", sum(CASE WHEN $production_hour_subcon<'$ends' and d.production_type=$sub_type THEN d.prod_qnty else 0 END) AS $prod_hour";
			}
			else
			{
				$sql_query_subcon.=", sum(CASE WHEN $production_hour_subcon>='$bg' and $production_hour_subcon<'$ends' and d.production_type=$sub_type THEN d.prod_qnty else 0 END) AS $prod_hour";
			}
			$first=$first+1;
		}
		$sql_query_subcon.=", sum(CASE WHEN $production_hour_subcon>='$start_hour_arr[$last_hour]' and $production_hour_subcon<='$start_hour_arr[24]' and d.production_type=$sub_type THEN d.prod_qnty else 0 END) AS good_23 from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d, subcon_ord_mst b, subcon_ord_dtls c,subcon_ord_breakdown e
		where a.production_type=$sub_type and d.production_type=$sub_type and a.id=d.dtls_id and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and e.id=d.ord_color_size_id and e.order_id=c.id and a.order_id=e.order_id AND a.company_id in(".str_replace("'","",$company_id).")  and a.floor_id in(".$floor_id.") and a.line_id in(".$sewing_line.")   and a.production_date='".$prod_date."'
		group by c.cust_style_ref, c.order_no,c.smv,e.color_id,a.gmts_item_id order by c.order_no,e.color_id";

	  // echo $sql_query_subcon;die;
		
	$sql_res=sql_select($sql_pop);
	$sql_subcon=sql_select($sql_query_subcon);
	if( count($sql_res)>0 && count($sql_subcon)>0)
	{
		$sql_res=array_merge($sql_res, $sql_subcon);
	}
	else if(count($sql_res)<=0 && count($sql_subcon)>0)
	{
		$sql_res=$sql_subcon;
	}        	
	
	$new_smv=array();
	$item_smv_pop=explode("****",$item_smv);
	$order_id="";
	foreach($item_smv_pop as $po_id_smv) 
	{
		$po_id_smv_pop=explode("**",$po_id_smv);
		$new_smv[$po_id_smv_pop[0]]=$po_id_smv_pop[1];
		/*  if($order_id!="") $order_id.=",".$po_id_smv_pop[0]; 
		else $order_id=$po_id_smv_pop[0]; */
	}
 //print_r($item_smv_pop);die;


	$order_data_array = array();
	$order_color_data_array = array();
	foreach($sql_res as $row)
	{

		$order_data_array[$row['PO_BREAK_DOWN_ID']][$row['COLOR_TYPE_ID']][$row['ITEM_NUMBER_ID']]['style_ref_no'] = $row['STYLE_REF_NO'];
		$order_data_array[$row['PO_BREAK_DOWN_ID']][$row['COLOR_TYPE_ID']][$row['ITEM_NUMBER_ID']]['po_number'] = $row['PO_NUMBER'];
		$order_data_array[$row['PO_BREAK_DOWN_ID']][$row['COLOR_TYPE_ID']][$row['ITEM_NUMBER_ID']]['good_qnty'] += $row['GOOD_QNTY'];
		$order_data_array[$row['PO_BREAK_DOWN_ID']][$row['COLOR_TYPE_ID']][$row['ITEM_NUMBER_ID']]['item_smv'] = $new_smv[$row[csf('po_break_down_id')]];
		$order_data_array[$row['PO_BREAK_DOWN_ID']][$row['COLOR_TYPE_ID']][$row['ITEM_NUMBER_ID']]['set_smv'] = $row[csf('set_break_down')];

		//$order_data_array[$row['PO_BREAK_DOWN_ID']][$row['COLOR_TYPE_ID']][$row['ITEM_NUMBER_ID']]['produce_minute'] += $row['GOOD_QNTY']*$item_smv_array[$row[csf('po_break_down_id')]][];
		
		if($row[csf('po_break_down_id')]=="00")
		{
			$order_data_array[$row['PO_BREAK_DOWN_ID']][$row['COLOR_TYPE_ID']][$row['ITEM_NUMBER_ID']]['item_smv'] = $row[csf('sub_smv')];
		}
		//================================================================
		$order_color_data_array[$row['PO_BREAK_DOWN_ID']][$row['COLOR_TYPE_ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_ID']]['style_ref_no'] = $row['STYLE_REF_NO'];
		$order_color_data_array[$row['PO_BREAK_DOWN_ID']][$row['COLOR_TYPE_ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_ID']]['po_number'] = $row['PO_NUMBER'];
		$order_color_data_array[$row['PO_BREAK_DOWN_ID']][$row['COLOR_TYPE_ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_ID']]['good_qnty'] = $row['GOOD_QNTY'];
		$order_color_data_array[$row['PO_BREAK_DOWN_ID']][$row['COLOR_TYPE_ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_ID']]['item_smv'] = $new_smv[$row[csf('po_break_down_id')]];
		$order_color_data_array[$row['PO_BREAK_DOWN_ID']][$row['COLOR_TYPE_ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_ID']]['set_smv'] = $row[csf('set_break_down')];

		if($row[csf('po_break_down_id')]=="00")
		{
			$order_color_data_array[$row['PO_BREAK_DOWN_ID']][$row['COLOR_TYPE_ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_ID']]['item_smv'] = $row[csf('sub_smv')];
		}
		
		
		
		$style_wise_po_arr[$row['STYLE_REF_NO']][$row['PO_BREAK_DOWN_ID']]=$row['PO_BREAK_DOWN_ID'];
		$all_style_arr[$row['STYLE_REF_NO']]=$row['STYLE_REF_NO'];
		$all_po_arr[$row['PO_BREAK_DOWN_ID']]=$row['PO_BREAK_DOWN_ID'];
	}
	//====================================
	$rowspan_arr = array();
	foreach ($order_color_data_array as $po_id => $po_data) 
	{			
		foreach ($col_type_data as $color_id => $row) 
		{
			$rowspan_arr[$po_id]++;
		}		
		
	}
	
	
	
	$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($company_id) and variable_list=25 and status_active=1 and is_deleted=0");
	//echo $smv_source;
	$color_type_ids = implode(",",array_unique(explode(",", $color_type_id)));
	if($color_type_ids!="")
	{
		$color_type_cond = " and a.color_type in($color_type_ids)";
	}

	$item_smv_array2 = array();
	if($smv_source==3)
	{
		//$style_wise_po_arr[$row['STYLE_REF_NO']][$row['PO_BREAK_DOWN_ID']]=$row['PO_BREAK_DOWN_ID'];
		$gsdSql="SELECT a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID,a.COLOR_TYPE  from PPL_GSD_ENTRY_MST a where a.APPLICABLE_PERIOD <= '$prod_date' and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and a.BULLETIN_TYPE in(4) ".where_con_using_array($all_style_arr,1,'A.STYLE_REF')." and a.APPROVED=1  and a.GMTS_ITEM_ID in($item_id) 
		group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID,a.COLOR_TYPE
		 ORDER BY a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC";
		   
		// echo $gsdSql; 
		$gsdSqlResult = sql_select($gsdSql); 
		 
		foreach($gsdSqlResult as $rows)
		{
			foreach($style_wise_po_arr[$rows[STYLE_REF]] as $po_id)
			{
				if($item_smv_array[$po_id][$rows[COLOR_TYPE]][$rows[GMTS_ITEM_ID]]==''){
					$item_smv_array[$po_id][$rows[COLOR_TYPE]][$rows[GMTS_ITEM_ID]]=$rows[TOTAL_SMV];
				}
				if($item_smv_array2[$po_id][$rows[GMTS_ITEM_ID]]==''){
					$item_smv_array2[$po_id][$rows[GMTS_ITEM_ID]]=$rows[TOTAL_SMV];
				}
			}
		}
		
	}
	else
	{
		$sql_item="select b.id, a.set_break_down, a.STYLE_REF_NO, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no ".where_con_using_array($all_po_arr,0,'b.id')."  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3)";
		$resultItem=sql_select($sql_item);
		foreach($resultItem as $itemData)
		{
			if($smv_source==1)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('STYLE_REF_NO')]]=$itemData[csf('smv_pcs')];
			}
			else if($smv_source==2)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('STYLE_REF_NO')]]=$itemData[csf('smv_pcs_precost')];
			}
		}
	}	
	
	
	// print_r($item_smv_array2);die();
	?>
	<fieldset style="width:720px; ">
		<div style="text-align: center;padding: 5px;">
			<script>
				function new_window()
				{					
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('report_container').innerHTML);
					d.close();
				}
			</script>
			<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
		</div>
        <div id="report_container">

        	<!-- ======================== SUMMARY PART ======================= -->

        	<table border="1" class="rpt_table" rules="all" width="700" cellpadding="0" cellspacing="0" align="center">
	        	<caption><b>Summary</b></caption>
	            <thead>
	                <th width="30">SL</th>
	                <th width="120">Style Ref.</th>
	                <th width="120">Order No</th>
	                <th width="100">Color Type</th>
	                <th width="70">Item Smv</th>
	                <th width="80">Production Qty</th>
	                <th>Produced Min.</th>
	            </thead>
				<?

	        
				$total_producd_min=0;
				$i=1; $total_qnty=0;
				foreach($order_data_array as $po_id=>$po_data)
				{
					foreach ($po_data as $color_type_key => $color_type_data) 
					{
						foreach ($color_type_data as $item_id => $row) 
						{
							$style_ref_no=$row['style_ref_no'];
							$po_number=$row['po_number'];
							// $item_smv=$row['item_smv'];
							$po_qty=$row['good_qnty'];
							$set_smv_ex = explode("__", $row['set_smv']);
							// echo $set_smv_ex[2];

							foreach ($set_smv_ex as $key => $smv) 
							{
								$set_smv = explode("_", $smv);
								if($item_id==$set_smv[0])
								{
									$item_smv = 0;
									$color_type_smv=($item_smv_array[$po_id][$color_type_key][$item_id]!="") ? $item_smv_array[$po_id][$color_type_key][$item_id] : $item_smv_array2[$po_id][$item_id];
									$item_smv = ($color_type_smv !="")?$color_type_smv:$set_smv[2];
									
								}

							}
							
							
							
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
			                    <td width="30"><? echo $i; ?></td>
			                    <td width="120" align="center"><? echo $style_ref_no; ?></td>
			                    <td width="120" align="center"><? echo $po_number; ?></td>
			                    <td width="100" align="center"><? echo $color_type[$color_type_key]; ?></td>
			                    <td align="right"><? echo number_format($item_smv,2); ?></td>
			                    <td align="right"><? $total_po_qty+=$po_qty; echo $po_qty; ?></td>
			                    <td align="right">
			                    <?
			                    $producd_min=$po_qty*$item_smv;  $total_producd_min+=$producd_min;
			                    echo number_format($po_qty*$item_smv,2);
			                    ?>&nbsp;</td>
			                    </tr>
							<?
							$i++;
						}
					}
				}
				?>
				<tfoot>
	                <th colspan="5" align="right">Total</th>
	                <th align="right"><? echo $total_po_qty; ?>&nbsp;</th>
	                <th align="right"><? echo number_format($total_producd_min,2); ?>&nbsp;</th>
	            </tfoot>
	        </table>

        	<!-- ======================== DETAILS PART ======================= -->

	        <table border="1" class="rpt_table" rules="all" width="700" cellpadding="0" cellspacing="0" align="center">
	        	<caption><b>Order & Color Wise Details</b></caption>
	            <thead>
	                <th width="30">SL</th>
	                <th width="120">Style Ref.</th>
	                <th width="120">Order No</th>
	                <th width="100">Color Type</th>
	                <th width="70">Item Smv</th>
	                <th width="80">Color</th>
	                <th>Production Qty</th>
	            </thead>
				<?

	        
				$total_producd_min=0;
				$i=1; $total_po_qty=0;
				foreach($order_color_data_array as $po_id=>$po_data)
				{
					$order_total = 0;
					$r=0;
					foreach ($po_data as $color_type_key => $color_type_data) 
					{
						foreach ($color_type_data as $item_key => $item_data) 
						{
						
							foreach($item_data as $color_id=>$row)
							{	
								$style_ref_no=$row['style_ref_no'];
								$po_number=$row['po_number'];
								//$item_smv=$row['item_smv'];
								$po_qty=$row['good_qnty'];
								$set_smv_ex = explode("_", $row['set_smv']);
								$item_smv=($item_smv_array[$po_id][$color_type_key][$item_key]!="") ? $item_smv_array[$po_id][$color_type_key][$item_key] : $item_smv_array2[$po_id][$item_key];
								$set_smv_ex = explode("__", $row['set_smv']);
								// echo $set_smv_ex[2];

								foreach ($set_smv_ex as $key => $smv) 
								{
									$set_smv = explode("_", $smv);
									if($item_id==$set_smv[0])
									{
										$color_type_smv=$item_smv_array[$po_id][$color_type_key][$item_key];
										$item_smv = ($color_type_smv !="")?$color_type_smv:$set_smv[2];
										
									}

								}
								
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
									<? if($r==0){?>
				                    <td valign="middle" rowspan="<? echo $rowspan_arr[$po_id];?>" width="30"><? echo $i; ?></td>
				                    <td valign="middle" rowspan="<? echo $rowspan_arr[$po_id];?>" width="120" align="center"><? echo $style_ref_no; ?></td>
				                    <td valign="middle" rowspan="<? echo $rowspan_arr[$po_id];?>" width="120" align="center"><? echo $po_number; ?></td>
				                    <td valign="middle" rowspan="<? echo $rowspan_arr[$po_id];?>" width="100" align="center"><? echo $color_type[$color_type_key]; ?></td>
				                    <td valign="middle" rowspan="<? echo $rowspan_arr[$po_id];?>" align="center"><? echo number_format($item_smv,2); ?>&nbsp;</td>
				                    <?}?>
				                    <td align="left"><? echo $color_lib[$color_id]; ?>&nbsp;</td>
				                    <td align="right"><?  echo $po_qty; ?>&nbsp;</td>
				                </tr>
								<?
								$i++;
								$r++;
								$order_total += $po_qty;
								$total_po_qty+=$po_qty;
							}
						}
					}
					?>
					<tr style="background-color: #dccdcd;">
	                	<th colspan="6" align="right">Order Total</th>
	                	<th align="right"><? echo $order_total; ?>&nbsp;</th>
	            	</tr>
					<?
				}
				?>
				<tfoot>
	                <th colspan="6" align="right">Total</th>
	                <th align="right"><? echo $total_po_qty; ?>&nbsp;</th>
	            </tfoot>
	        </table>
        </div>
	</fieldset>   
	<?
	exit();
}

if($action=="tot_fob_val")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$color_lib=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
	// print_r($color_lib);
	if($db_type==0)
	{
		$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id  and pr_date='".$prod_date."'","line_start_time");	
	}//and  a.company_id=$comapny_id and shift_id=1
	else if($db_type==2)
	{
		$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and pr_date='".$prod_date."'","line_start_time");
	}//
	
	if($min_shif_start=="")
	{
		echo "<p style='font-size:20px', align='center'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<p/>";die;
		
	}
	
	if($db_type==2)
	{
		$pr_date=str_replace("'","",$prod_date);
		$pr_date_old=explode("-",str_replace("'","",$prod_date));
		$month=strtoupper($pr_date_old[1]);
		$year=substr($pr_date_old[2],2);
		$pr_date=$pr_date_old[0]."-".$month."-".$year;
	}
	else if($db_type==0)
	{
		$pr_date=str_replace("'","",$prod_date);
	}
	//echo $pr_date;die; 
	$prod_start_hour="08:00";
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
	
	
	$first_hour_time=explode(":",$min_shif_start);
	$hour_line=substr($first_hour_time[0],1,1); $minutes_one=$start_time[1];
	$line_start_hour_arr[$hour_line]=$min_shif_start;
	
	for($l=$hour_line;$l<$last_hour;$l++)
	{
		$min_shif_start=add_time($min_shif_start,60);
		$line_start_hour_arr[$l+1]=substr($min_shif_start,0,5);
	}
	
	$line_start_hour_arr[$j+1]='23:59';

	$production_hour="TO_CHAR(production_hour,'HH24:MI')";
    $sql_pop="select b.style_ref_no, c.po_number,c.unit_price, a.po_break_down_id,f.color_number_id as color_id, sum(d.production_qnty) as good_qnty";
	$first=1;
	for($h=$hour;$h<$last_hour;$h++)
	{
		$bg=$start_hour_arr[$h];
		$end=substr(add_time($start_hour_arr[$h],60),0,5);
		$prod_hour="good_".substr($bg,0,2);
		if($first==1)
		{
			$sql_pop.=", sum(CASE WHEN $production_hour<='$end' and a.production_type=$pro_type and d.production_type=$pro_type  THEN d.production_qnty else 0 END) AS $prod_hour";
		}
		else
		{
			$sql_pop.=", sum(CASE WHEN $production_hour>'$bg' and $production_hour<='$end' and a.production_type=$pro_type and d.production_type=$pro_type  THEN d.production_qnty else 0 END) AS $prod_hour";
		}
		$first=$first+1;
	}
	 

		$sql_pop.=", sum(CASE WHEN $production_hour>'$start_hour_arr[$last_hour]' and $production_hour<='$start_hour_arr[24]' and a.production_type=$pro_type and d.production_type=$pro_type  THEN d.production_qnty else 0 END) AS prod_hour23 ,b.set_break_down from pro_garments_production_mst a,pro_garments_production_dtls d, wo_po_details_master b, wo_po_break_down c,wo_po_color_size_breakdown f
		where a.id=d.mst_id and d.production_type=$pro_type and f.id=d.color_size_break_down_id and d.status_active=1 and d.is_deleted=0 and  a.production_type=$pro_type and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and c.id=f.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active in (1,2,3) and c.is_deleted=0 and f.status_active in (1,2,3) and f.is_deleted=0  and c.is_deleted=0  and a.serving_company in($company_id)  and a.floor_id=".$floor_id." and a.sewing_line=".$sewing_line."  and a.po_break_down_id in(".$po_id.") and a.production_date='".$prod_date."'  group by b.style_ref_no, c.po_number,c.unit_price,a.po_break_down_id,f.color_number_id,b.set_break_down order by  c.po_number,f.color_number_id ";
		
		 // echo $sql_pop;
		//================================= subcontact =====================================
		if($db_type==2)
		{
			$production_hour_subcon="TO_CHAR(hour,'HH24:MI')";
		}
		else
		{
			$production_hour_subcon=" hour ";
		}
		$sql_query_subcon="SELECT  c.cust_style_ref as style_ref_no, 00 as po_break_down_id,c.smv as sub_smv, c.order_no as po_number,c.rate as unit_price,e.color_id as color_id,
		sum(d.prod_qnty) as good_qnty";
		$first=1;
		$subcon_type=$pro_type;
		$sub_type=($subcon_type==11)?"5" : "2";
		for($h=$hour;$h<$last_hour;$h++)
		{
			$bg=$start_hour_arr[$h];
			$ends=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="good_".substr($bg,0,2);
			if($first==1)
			{
				$sql_query_subcon.=", sum(CASE WHEN $production_hour_subcon<'$ends' and d.production_type=$sub_type THEN d.prod_qnty else 0 END) AS $prod_hour";
			}
			else
			{
				$sql_query_subcon.=", sum(CASE WHEN $production_hour_subcon>='$bg' and $production_hour_subcon<'$ends' and d.production_type=$sub_type THEN d.prod_qnty else 0 END) AS $prod_hour";
			}
			$first=$first+1;
		}
		$sql_query_subcon.=", sum(CASE WHEN $production_hour_subcon>='$start_hour_arr[$last_hour]' and $production_hour_subcon<='$start_hour_arr[24]' and d.production_type=$sub_type THEN d.prod_qnty else 0 END) AS good_23 from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d, subcon_ord_mst b, subcon_ord_dtls c,subcon_ord_breakdown e
		where a.production_type=$sub_type and d.production_type=$sub_type and a.id=d.dtls_id and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and e.id=d.ord_color_size_id and e.order_id=c.id and a.order_id=e.order_id AND a.company_id in(".str_replace("'","",$company_id).")  and a.floor_id in(".$floor_id.") and a.line_id in(".$sewing_line.")   and a.production_date='".$prod_date."'
		group by c.cust_style_ref, c.order_no,c.rate,c.smv,e.color_id order by c.order_no,e.color_id";

	  //echo $sql_query_subcon;die;
		
	$sql_res=sql_select($sql_pop);
	$sql_subcon=sql_select($sql_query_subcon);
	if( count($sql_res)>0 && count($sql_subcon)>0)
	{
		$sql_res=array_merge($sql_res, $sql_subcon);
	}
	else if(count($sql_res)<=0 && count($sql_subcon)>0)
	{
		$sql_res=$sql_subcon;
	}        	
	
	$new_smv=array();
	$item_smv_pop=explode("****",$item_smv);
	$order_id="";
	foreach($item_smv_pop as $po_id_smv) 
	{
		$po_id_smv_pop=explode("**",$po_id_smv);
		$new_smv[$po_id_smv_pop[0]]=$po_id_smv_pop[1];
		/*  if($order_id!="") $order_id.=",".$po_id_smv_pop[0]; 
		else $order_id=$po_id_smv_pop[0]; */
	}
 //print_r($item_smv_pop);die;


	$order_data_array = array();
	foreach($sql_res as $row)
	{
		$set_ratio=explode("__",$row[csf('set_break_down')]);
		$order_data_array[$row['PO_BREAK_DOWN_ID']]['style_ref_no'] = $row['STYLE_REF_NO'];
		$order_data_array[$row['PO_BREAK_DOWN_ID']]['po_number'] = $row['PO_NUMBER'];
		$order_data_array[$row['PO_BREAK_DOWN_ID']]['good_qnty'] += $row['GOOD_QNTY'];
		$order_data_array[$row['PO_BREAK_DOWN_ID']]['fob_val'] += $row['UNIT_PRICE']*$row['GOOD_QNTY'];
		$order_data_array[$row['PO_BREAK_DOWN_ID']]['unit_price'] = $row['UNIT_PRICE'];
		$order_data_array[$row['PO_BREAK_DOWN_ID']]['set_ratio'] = count($set_ratio);
	}
		
	
	 //print_r($item_smv_array);die();
	?>
	<fieldset style="width:620px; ">
		<div style="text-align: center;padding: 5px;">
			<script>
				function new_window()
				{					
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('report_container').innerHTML);
					d.close();
				}
			</script>
			<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
		</div>
        <div id="report_container">
        	<!-- ======================== SUMMARY PART ======================= -->
        	<table border="1" class="rpt_table" rules="all" width="600" cellpadding="0" cellspacing="0" align="center">
	            <thead>
	                <th width="30">SL</th>
	                <th width="120">Style Ref.</th>
	                <th width="120">Order No</th>
	                <th width="70">Item FOB</th>
	                <th width="80">Production Qty</th>
	                <th>Production FOB Value</th>
	            </thead>
				<?

	        
				$total_producd_min=0;
				$i=1; $total_qnty=0;
				foreach($order_data_array as $po_id=>$row)
				{
					$style_ref_no=$row['style_ref_no'];
					$po_number=$row['po_number'];
					//$item_smv=$row['item_smv'];
					$po_qty=$row['good_qnty'];
					$fob_price=$row['unit_price']/$row['set_ratio'];
					$tot_fob_val+=$fob_price*$po_qty;

					$item_smv=$item_smv_array[$po_id][$row['style_ref_no']];
					
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
	                    <td width="30"><? echo $i; ?></td>
	                    <td width="120" align="center"><? echo $style_ref_no; ?></td>
	                    <td width="120" align="center"><? echo $po_number; ?></td>
						<td align="right"><? echo number_format($fob_price,4); ?></td>
						<td align="right"><? $total_po_qty+=$po_qty; echo $po_qty; ?></td>
	                    <td align="right"><? echo number_format($fob_price*$po_qty,2);?>&nbsp;</td>
	                    </tr>
					<?
					$i++;
				}
				?>
				<tfoot>
	                <th colspan="4" align="right">Total</th>
	                <th align="right"><? echo $total_po_qty; ?>&nbsp;</th>
	                <th align="right"><? echo number_format($tot_fob_val,2); ?>&nbsp;</th>
	            </tfoot>
	        </table>
        </div>
	</fieldset>   
	<?
	exit();
}
if($action=="tot_fob_val_hangtag")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$color_lib=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
	// print_r($color_lib);
	if($db_type==0)
	{
		$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id  and pr_date='".$prod_date."'","line_start_time");	
	}//and  a.company_id=$comapny_id and shift_id=1
	else if($db_type==2)
	{
		$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and pr_date='".$prod_date."'","line_start_time");
	}//
	
	if($min_shif_start=="")
	{
		echo "<p style='font-size:20px', align='center'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<p/>";die;
		
	}
	
	if($db_type==2)
	{
		$pr_date=str_replace("'","",$prod_date);
		$pr_date_old=explode("-",str_replace("'","",$prod_date));
		$month=strtoupper($pr_date_old[1]);
		$year=substr($pr_date_old[2],2);
		$pr_date=$pr_date_old[0]."-".$month."-".$year;
	}
	else if($db_type==0)
	{
		$pr_date=str_replace("'","",$prod_date);
	}
	//echo $pr_date;die; 
	$prod_start_hour="08:00";
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
	
	
	$first_hour_time=explode(":",$min_shif_start);
	$hour_line=substr($first_hour_time[0],1,1); $minutes_one=$start_time[1];
	$line_start_hour_arr[$hour_line]=$min_shif_start;
	
	for($l=$hour_line;$l<$last_hour;$l++)
	{
		$min_shif_start=add_time($min_shif_start,60);
		$line_start_hour_arr[$l+1]=substr($min_shif_start,0,5);
	}
	
	$line_start_hour_arr[$j+1]='23:59';

	$production_hour="TO_CHAR(production_hour,'HH24:MI')";
    $sql_pop="select b.style_ref_no, c.po_number,c.unit_price, a.po_break_down_id,f.color_number_id as color_id, sum(d.production_qnty) as good_qnty";
	
	$first=1;
	for($h=$hour;$h<$last_hour;$h++)
	{
		$bg=$start_hour_arr[$h];
		$end=substr(add_time($start_hour_arr[$h],60),0,5);
		$prod_hour="good_".substr($bg,0,2);
		if($first==1)
		{
			$sql_pop.=", sum(CASE WHEN $production_hour<='$end' and a.production_type=$pro_type and d.production_type=$pro_type  THEN d.production_qnty else 0 END) AS $prod_hour";
		}
		else
		{
			$sql_pop.=", sum(CASE WHEN $production_hour>'$bg' and $production_hour<='$end' and a.production_type=$pro_type and d.production_type=$pro_type  THEN d.production_qnty else 0 END) AS $prod_hour";
		}
		$first=$first+1;
	}
	 

		$sql_pop.=", sum(CASE WHEN $production_hour>'$start_hour_arr[$last_hour]' and $production_hour<='$start_hour_arr[24]' and a.production_type=$pro_type and d.production_type=$pro_type  THEN d.production_qnty else 0 END) AS prod_hour23 ,b.set_break_down from pro_garments_production_mst a,pro_garments_production_dtls d, wo_po_details_master b, wo_po_break_down c,wo_po_color_size_breakdown f
		where a.id=d.mst_id and d.production_type=$pro_type and f.id=d.color_size_break_down_id and d.status_active=1 and d.is_deleted=0 and  a.production_type=$pro_type and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and c.id=f.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active in (1,2,3) and c.is_deleted=0 and f.status_active in (1,2,3) and f.is_deleted=0  and c.is_deleted=0  and a.serving_company in($company_id)  and a.floor_id=".$floor_id."  and a.po_break_down_id in(".$po_id.") and a.production_date='".$prod_date."'  group by b.style_ref_no, c.po_number,c.unit_price,a.po_break_down_id,f.color_number_id,b.set_break_down order by  c.po_number,f.color_number_id ";
		
		 // echo $sql_pop; a.sewing_line=".$sewing_line."
		//================================= subcontact =====================================
		if($db_type==2)
		{
			$production_hour_subcon="TO_CHAR(hour,'HH24:MI')";
		}
		else
		{
			$production_hour_subcon=" hour ";
		}
		$sql_query_subcon="SELECT  c.cust_style_ref as style_ref_no, 00 as po_break_down_id,c.smv as sub_smv, c.order_no as po_number,c.rate as unit_price,e.color_id as color_id,
		sum(d.prod_qnty) as good_qnty";
		$first=1;
		$subcon_type=$pro_type;
		$sub_type=($subcon_type==15)?"5" : "2";
		for($h=$hour;$h<$last_hour;$h++)
		{
			$bg=$start_hour_arr[$h];
			$ends=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="good_".substr($bg,0,2);
			if($first==1)
			{
				$sql_query_subcon.=", sum(CASE WHEN $production_hour_subcon<'$ends' and d.production_type=$sub_type THEN d.prod_qnty else 0 END) AS $prod_hour";
			}
			else
			{
				$sql_query_subcon.=", sum(CASE WHEN $production_hour_subcon>='$bg' and $production_hour_subcon<'$ends' and d.production_type=$sub_type THEN d.prod_qnty else 0 END) AS $prod_hour";
			}
			$first=$first+1;
		}
		$sql_query_subcon.=", sum(CASE WHEN $production_hour_subcon>='$start_hour_arr[$last_hour]' and $production_hour_subcon<='$start_hour_arr[24]' and d.production_type=$sub_type THEN d.prod_qnty else 0 END) AS good_23 from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d, subcon_ord_mst b, subcon_ord_dtls c,subcon_ord_breakdown e
		where a.production_type=$sub_type and d.production_type=$sub_type and a.id=d.dtls_id and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and e.id=d.ord_color_size_id and e.order_id=c.id and a.order_id=e.order_id AND a.company_id in(".str_replace("'","",$company_id).")  and a.floor_id in(".$floor_id.")    and a.production_date='".$prod_date."'
		group by c.cust_style_ref, c.order_no,c.rate,c.smv,e.color_id order by c.order_no,e.color_id";

	 // echo $sql_query_subcon;die;and a.line_id in(".$sewing_line.")
		
	$sql_res=sql_select($sql_pop);
	$sql_subcon=sql_select($sql_query_subcon);
	if( count($sql_res)>0 && count($sql_subcon)>0)
	{
		$sql_res=array_merge($sql_res, $sql_subcon);
	}
	else if(count($sql_res)<=0 && count($sql_subcon)>0)
	{
		$sql_res=$sql_subcon;
	}        	
	
	$new_smv=array();
	$item_smv_pop=explode("****",$item_smv);
	$order_id="";
	foreach($item_smv_pop as $po_id_smv) 
	{
		$po_id_smv_pop=explode("**",$po_id_smv);
		$new_smv[$po_id_smv_pop[0]]=$po_id_smv_pop[1];
		/*  if($order_id!="") $order_id.=",".$po_id_smv_pop[0]; 
		else $order_id=$po_id_smv_pop[0]; */
	}
 //print_r($item_smv_pop);die;


	$order_data_array = array();
	foreach($sql_res as $row)
	{
		$set_ratio=explode("__",$row[csf('set_break_down')]);
		$order_data_array[$row['PO_BREAK_DOWN_ID']]['style_ref_no'] = $row['STYLE_REF_NO'];
		$order_data_array[$row['PO_BREAK_DOWN_ID']]['po_number'] = $row['PO_NUMBER'];
		$order_data_array[$row['PO_BREAK_DOWN_ID']]['good_qnty'] += $row['GOOD_QNTY'];
		$order_data_array[$row['PO_BREAK_DOWN_ID']]['fob_val'] += $row['UNIT_PRICE']*$row['GOOD_QNTY'];
		$order_data_array[$row['PO_BREAK_DOWN_ID']]['unit_price'] = $row['UNIT_PRICE'];
		$order_data_array[$row['PO_BREAK_DOWN_ID']]['set_ratio'] = count($set_ratio);
	}
		
	
	 //print_r($item_smv_array);die();
	?>
	<fieldset style="width:620px; ">
		<div style="text-align: center;padding: 5px;">
			<script>
				function new_window()
				{					
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('report_container').innerHTML);
					d.close();
				}
			</script>
			<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
		</div>
        <div id="report_container">
        	<!-- ======================== SUMMARY PART ======================= -->
        	<table border="1" class="rpt_table" rules="all" width="600" cellpadding="0" cellspacing="0" align="center">
	            <thead>
	                <th width="30">SL</th>
	                <th width="120">Style Ref.</th>
	                <th width="120">Order No</th>
	                <th width="70">Item FOB</th>
	                <th width="80">Production Qty</th>
	                <th>Production FOB Value</th>
	            </thead>
				<?

	        
				$total_producd_min=0;
				$i=1; $total_qnty=0;
				foreach($order_data_array as $po_id=>$row)
				{
					$style_ref_no=$row['style_ref_no'];
					$po_number=$row['po_number'];
					//$item_smv=$row['item_smv'];
					$po_qty=$row['good_qnty'];
					$fob_price=$row['unit_price']/$row['set_ratio'];
					$tot_fob_val+=$fob_price*$po_qty;

					$item_smv=$item_smv_array[$po_id][$row['style_ref_no']];
					
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
	                    <td width="30"><? echo $i; ?></td>
	                    <td width="120" align="center"><? echo $style_ref_no; ?></td>
	                    <td width="120" align="center"><? echo $po_number; ?></td>
						<td align="right"><? echo number_format($fob_price,4); ?></td>
						<td align="right"><? $total_po_qty+=$po_qty; echo $po_qty; ?></td>
	                    <td align="right"><? echo number_format($fob_price*$po_qty,2);?>&nbsp;</td>
	                    </tr>
					<?
					$i++;
				}
				?>
				<tfoot>
	                <th colspan="4" align="right">Total</th>
	                <th align="right"><? echo $total_po_qty; ?>&nbsp;</th>
	                <th align="right"><? echo number_format($tot_fob_val,2); ?>&nbsp;</th>
	            </tfoot>
	        </table>
        </div>
	</fieldset>   
	<?
	exit();
}
 
 
?>