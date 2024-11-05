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
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","");  
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 120, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/hourly_production_monitoring_report_gross_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 ); 
	exit();    	 
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_id", 120, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );     	 	
	exit();    	 
}

if($action=="line_search")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
	<script>
			
			var selected_id = new Array;
			var selected_name = new Array;
			
			function check_all_data() {
				var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
				tbl_row_count = tbl_row_count - 0;
				for( var i = 1; i <= tbl_row_count; i++ ) {
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
		</script>
	<?
	extract($_REQUEST);
	//echo $company;die;
	if($company==0) $company_name=""; else $company_name=" and b.company_name=$company";//job_no
	
	if($buyer==0) $buyer_name=""; else $buyer_name="and b.buyer_name=$buyer";
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company and variable_list=23 and is_deleted=0 and status_active=1");
   if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		
		if($txt_date=="")
		{
			if($location!=0 ) $cond = " and a.location_id= $location";
			$line_data="select a.id, b.line_name from prod_resource_mst a,lib_sewing_line b where a.is_deleted=0 and REGEXP_SUBSTR( a.line_number, '[^,]+', 1)=b.id $cond";
		}
		else
		{
			if(  $location!=0 ) $cond = " and a.location_id= $location";
			$txt_date = date('d-M-Y',strtotime($txt_date));
			
			$line_data="select a.id, c.line_name from prod_resource_mst a, prod_resource_dtls b,lib_sewing_line c where a.id=b.mst_id and REGEXP_SUBSTR( a.line_number, '[^,]+', 1)=c.id and b.pr_date='$txt_date' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, c.line_name";
		}
		// echo $line_data;


      echo create_list_view("list_view", "Line ","250","300","310",0, $line_data , "js_set_value", "id,line_name", "", 1, "0", $arr, "line_name", "","setFilterGrid('list_view',-1)","0","",1);	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
		
	}
	else
	{
		if( $location!=0  ) $cond = " and location_name= $location";
		if( $floor_id!=0 ) $cond.= " and floor_name= $floor_id";
		$line_data="select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name";
			
	echo create_list_view("list_view", "Line No","250","300","310",0, $line_data , "js_set_value", "id,line_name", "", 1, "0", $arr, "line_name", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	}
	exit();
}

if($action=="report_generate") // show3 button
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
	$companyArr = return_library_array("select id,company_name from lib_company","id","company_name"); 
	$buyerArr = return_library_array("select id,short_name from lib_buyer","id","short_name"); 
	$locationArr = return_library_array("select id,location_name from lib_location","id","location_name"); 
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name"); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");  
	$prod_reso_arr=return_library_array( "SELECT id, line_number from prod_resource_mst",'id','line_number');
	$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
	order by sewing_line_serial"); 
	foreach($lineDataArr as $lRow)
	{
		$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
		$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
		$lastSlNo=$lRow[csf('sewing_line_serial')];
	}

	$comapny_id=str_replace("'","",$cbo_company_id);
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_id and variable_list=23 and is_deleted=0 and status_active=1");
	//echo $prod_reso_allo."eee";die;
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and b.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
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

	if(str_replace("'","",$cbo_company_id)==0) $company_name=""; else $company_name="and a.company_id=".str_replace("'","",$cbo_company_id)."";
	if(str_replace("'","",$cbo_location_id)==0) $location=""; else $location="and a.location=".str_replace("'","",$cbo_location_id)."";
	if(str_replace("'","",$cbo_location_id)==0) $location2=""; else $location2="and a.location_id=".str_replace("'","",$cbo_location_id)."";
    if(str_replace("'","",$hidden_line_id)=="") $line=""; else $line="and a.sewing_line in(".str_replace("'","",$hidden_line_id).")";
    if(str_replace("'","",$hidden_line_id)=="") $line2=""; else $line2="and a.id in(".str_replace("'","",$hidden_line_id).")";
    if(str_replace("'","",$cbo_buyer_name)==0) $buyer_cond=""; else $buyer_cond="and b.buyer_name=$cbo_buyer_name";
    if(str_replace("'","",$cbo_job_year)==0) $job_year_cond=""; else $job_year_cond="and to_char(b.insert_date,'YYYY')=$cbo_job_year";
    if(str_replace("'","",$style)=="") $style_cond=""; else $style_cond="and b.style_ref_no=$style";
    if(str_replace("'","",$job)=="") $job_cond=""; else $job_cond="and b.job_no_prefix_num=$job";
    if(str_replace("'","",$cbo_gmts_item)==0) $item_cond=""; else $item_cond="and a.item_number_id=$cbo_gmts_item";

	$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and   a.company_id in($comapny_id) and shift_id=1 and pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
	if($min_shif_start=="")
	{
		echo "<div style='width:80%;margin:5px auto;' class='alert alert-danger'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<div/>";die;
		
	}

	// ======================== shift wise line =========================
	
	$sql = "SELECT a.id,min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time,min(TO_CHAR(d.lunch_start_time,'HH24:MI')) as LUNCH_START_TIME from prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d where a.id=d.mst_id and a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and   a.company_id in($comapny_id) and shift_id=1 and pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 group by a.id";
	// echo $sql;
	$res = sql_select($sql);
	$line_wise_shift_arr = array();
	$line_wise_shift_lunch_arr = array();

	foreach ($res as $val)  
	{
		$line_wise_shift_arr[$val['ID']] = $val['LINE_START_TIME'];
		$line_wise_shift_lunch_arr[$val['ID']] = $val['LUNCH_START_TIME'];
	}
	unset($res);
	//==============================shift time===============================
	$start_time_data_arr=sql_select("SELECT company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time,TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($comapny_id) and  shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");
		
		
	$group_prod_start_time=sql_select("SELECT min(TO_CHAR(prod_start_time,'HH24:MI')) as prod_start_time  from variable_settings_production where company_name in($comapny_id) and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");
		
	$lunch_start_time = "";	
	foreach($start_time_data_arr as $row)
	{
		$start_time_arr[$row[csf('company_name')]][$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
		$start_time_arr[$row[csf('company_name')]][$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
		$exp = explode(":",$row[csf('lunch_start_time')]);
		$lunch_start_time = $exp[0]*1;
	}
	// echo $lunch_start_time."ddddddd";
	unset($start_time_data_arr);

	$prod_start_hour=$group_prod_start_time[0][csf('prod_start_time')];
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
		// echo $j."<br>";
	}
	//echo $pc_date_time;die;
	$start_hour_arr[$j+1]='23:59';

	if($prod_start_hour>$min_shif_start)  $prod_start_hour=$min_shif_start;
	$actual_date=date("Y-m-d");
	$actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$txt_date)));
	$actual_time=substr(date("Y-m-d H:i:s",strtotime($pc_date_time)),11,2);	
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

	/* =====================================================================================================/
	/												Prod Resource data										/
	/===================================================================================================== */
 	if($prod_reso_allo==1)
 	{	
		$prod_resource_array=array();
		$dataArray=sql_select("SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity,b.qi,b.iron_man from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and a.id=b.mst_id and c.id=b.mast_dtl_id and a.company_id=$comapny_id $location2 $line2  and b.pr_date=$txt_date");
		foreach($dataArray as $val)
		{
			$prod_resource_array[$val[csf('id')]]['man_power']=$val[csf('man_power')];
			$prod_resource_array[$val[csf('id')]]['operator']=$val[csf('operator')];
			$prod_resource_array[$val[csf('id')]]['helper']=$val[csf('helper')];
			$prod_resource_array[$val[csf('id')]]['qi']=$val[csf('qi')];
			$prod_resource_array[$val[csf('id')]]['iron_man']=$val[csf('iron_man')];
			$prod_resource_array[$val[csf('id')]]['terget_hour']=$val[csf('target_per_hour')];
			$prod_resource_array[$val[csf('id')]]['working_hour']=$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]]['tpd']=$val[csf('target_per_hour')]*$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]]['day_start']=$val[csf('from_date')];
			$prod_resource_array[$val[csf('id')]]['day_end']=$val[csf('to_date')];
			$prod_resource_array[$val[csf('id')]]['capacity']=$val[csf('capacity')];
		}

		

		/*===============================================================================/
		/							Actual resource SMV data							 /
		/============================================================================== */
		$prod_resource_smv_adj_array = array();
		$sql_query="SELECT b.mst_id, b.pr_date,b.number_of_emp ,b.adjust_hour,b.total_smv,b.adjustment_source from prod_resource_mst a,prod_resource_smv_adj b  where a.id=b.mst_id  and a.company_id=$comapny_id and b.pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and b.adjustment_source in(3,4,6) $location2 $line2";
		// echo $sql_query;
		$sql_query_res=sql_select($sql_query);
		foreach($sql_query_res as $val)
		{			
			$prod_resource_smv_adj_array[$val[csf('mst_id')]][$val[csf('adjustment_source')]]['number_of_emp']+=$val[csf('number_of_emp')];
			$prod_resource_smv_adj_array[$val[csf('mst_id')]][$val[csf('adjustment_source')]]['adjust_hour']+=$val[csf('adjust_hour')];
			$prod_resource_smv_adj_array[$val[csf('mst_id')]][$val[csf('adjustment_source')]]['total_smv']+=$val[csf('total_smv')];
			
		}
 	}
	// echo "<pre>";print_r($prod_resource_smv_adj_array);die;
	/* =====================================================================================================/
	/												Gmts Prod data											/
	/===================================================================================================== */
	
	$sql="SELECT  a.company_id, a.location, a.floor_id,a.shift_name, a.production_date, a.sewing_line,b.id as job_id,b.job_no,b.buyer_name  as buyer_name,b.style_ref_no, b.total_set_qnty as ratio, a.po_break_down_id, a.item_number_id,c.job_no_mst as job_no , c.po_number as po_number,c.unit_price,c.grouping,a.prod_reso_allo,c.id as po_id,d.bndl_hold_reason,
	sum(CASE WHEN a.production_type=5 THEN d.production_qnty else 0 END) as good_qnty,
	sum(CASE WHEN a.production_type=5 THEN d.reject_qty else 0 END) as reject_qty,
	sum(CASE WHEN a.production_type=5 THEN d.replace_qty else 0 END) as replace_qty,
	sum(CASE WHEN a.production_type=4 THEN d.production_qnty else 0 END) as input_qnty,"; 
		 
	$first=1;
	for($h=$hour;$h<$last_hour;$h++)
	{
			$bg=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="prod_hour".substr($bg,0,2);
			if($first==1)
			{
				$sql.="sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.production_qnty else 0 END) AS $prod_hour,";
			}
			else
			{
				$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN d.production_qnty else 0 END) AS $prod_hour,";
			}
		$first++;
	}
	$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN d.production_qnty else 0 END) AS prod_hour23 
		from wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a, pro_garments_production_dtls d
		where  c.job_id=b.id and  a.po_break_down_id=c.id and a.id=d.mst_id and a.production_type in(4,5) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.prod_reso_allo=1 and a.production_date=$txt_date $company_name $location $buyer_id_cond $job_cond $style_cond $line $job_year_cond $item_cond
		group by a.company_id, a.location, a.floor_id,a.shift_name,a.po_break_down_id, a.production_date, a.prod_reso_allo,c.id, a.sewing_line,b.id, b.job_no,b.total_set_qnty, b.buyer_name, b.style_ref_no, a.item_number_id, c.po_number,c.grouping,c.job_no_mst,  c.unit_price,d.bndl_hold_reason order by a.floor_id";
	// echo $sql;
	
	$res = sql_select($sql);
	$data_array = array();
	$lc_com_array = array();
	$poIdArr=array();
	$jobIdArr=array();
	$all_style_arr=array();
	$po_unit_price_array = array();
	$line_wise_po_item_array = array();
	foreach ($res as $v)
	{
		$lc_com_array[$v[csf('company_id')]] = $v[csf('company_id')];
		$poIdArr[$v[csf('po_break_down_id')]] = $v[csf('po_break_down_id')];	
		$jobIdArr[$v[csf('job_id')]] = $v[csf('job_id')];	
		$all_style_arr[$v[csf('style_ref_no')]] = $v[csf('style_ref_no')];
		$style_wise_po_arr[$v[csf('style_ref_no')]][$v[csf('po_break_down_id')]] = $v[csf('po_break_down_id')];
		$line_wise_po_item_array[$v['SEWING_LINE']] .= $v[csf('po_break_down_id')]."**".$v[csf('item_number_id')]."**".$v[csf('style_ref_no')]."__";

		$sewing_line='';
		if($v['PROD_RESO_ALLO']==1)
		{
			$sewing_line_ids=$prod_reso_arr[$v['SEWING_LINE']];
			$sl_ids_arr = explode(",", $sewing_line_ids);
			$sewing_line_id = $sl_ids_arr[0]; // always 1st line id will take
			foreach($sl_ids_arr as $val)
			{
				if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
			}
		}
		else
		{
			$sewing_line_id=$v['SEWING_LINE'];
			$sewing_line=$lineArr[$v['SEWING_LINE']];
		}
		
		if($lineSerialArr[$sewing_line_id]=="")
		{
			$lastSlNo++;
			$slNo=$lastSlNo;
			$lineSerialArr[$sewing_line_id]=$slNo;
		}
		else $slNo=$lineSerialArr[$sewing_line_id];

		$data_array[$v['FLOOR_ID']][$slNo][$sewing_line]['job_no'] .= $buyerArr[$v['JOB_NO']]."**";
		$data_array[$v['FLOOR_ID']][$slNo][$sewing_line]['buyer_name'] .= $buyerArr[$v['BUYER_NAME']]."**";
		$data_array[$v['FLOOR_ID']][$slNo][$sewing_line]['style'] .= $v['STYLE_REF_NO']."**";
		$data_array[$v['FLOOR_ID']][$slNo][$sewing_line]['po_number'] .= $v['PO_NUMBER']."**";
		$data_array[$v['FLOOR_ID']][$slNo][$sewing_line]['item_name'] .= $garments_item[$v['ITEM_NUMBER_ID']]."**";
		$data_array[$v['FLOOR_ID']][$slNo][$sewing_line]['line_id'] = $v['SEWING_LINE'];
		$data_array[$v['FLOOR_ID']][$slNo][$sewing_line]['po_id'] .= $v['PO_ID'];
		$data_array[$v['FLOOR_ID']][$slNo][$sewing_line]['reason'] .= $bundle_hold_reason_array[$v['BNDL_HOLD_REASON']]."**";
		if($v['BNDL_HOLD_REASON']!=0)
		{
			$data_array[$v['FLOOR_ID']][$slNo][$sewing_line]['hold_qnty'] += $v['GOOD_QNTY'];
		}
		$data_array[$v['FLOOR_ID']][$slNo][$sewing_line]['all_qnty'] += $v['GOOD_QNTY'];
		$data_array[$v['FLOOR_ID']][$slNo][$sewing_line]['reject_qty'] += $v['REJECT_QTY'];
		$data_array[$v['FLOOR_ID']][$slNo][$sewing_line]['replace_qty'] += $v['REPLACE_QTY'];
		
		
		$data_array[$v['FLOOR_ID']][$slNo][$sewing_line]['totay_input_qnty'] += $v['INPUT_QNTY'];
		
	 	for($h=$hour;$h<$last_hour;$h++)
		{
			$prod_hour="prod_hour".substr($start_hour_arr[$h],0,2)."";
			$data_array[$v['FLOOR_ID']][$slNo][$sewing_line][$prod_hour]+=$v[csf($prod_hour)];
			
		}
		$po_unit_price_array[$v[csf('po_break_down_id')]]=$v[csf('UNIT_PRICE')];
	}
	// echo "<pre>";print_r($data_array);die;
	/*===================================================================================== /
	/										Total Data 										/
	/===================================================================================== */
	$jobIds_cond = where_con_using_array($jobIdArr,0,"c.job_id");
	$sql = "SELECT c.id,a.floor_id,a.sewing_line, c.job_no_mst, c.item_number_id,c.order_rate,c.po_break_down_id as po_id,(CASE
	WHEN a.production_type = 4 THEN  a.production_date END) AS first_input_date, (case when b.production_type=4 then b.production_qnty else 0 end) as input_qty, (case when b.production_type=5 then b.production_qnty else 0 end) as output_qty,c.order_quantity FROM wo_po_color_size_breakdown  c LEFT JOIN pro_garments_production_dtls b ON c.id = b.color_size_break_down_id AND b.status_active = 1 AND b.is_deleted = 0 LEFT JOIN pro_garments_production_mst a ON a.id = b.mst_id and a.status_active = 1 AND a.is_deleted = 0 
	WHERE c.status_active = 1 AND c.is_deleted = 0 $jobIds_cond order by a.production_date desc";
	// echo $sql;
	$res = sql_select($sql);
	$tot_data_array = array();
	$order_qty_array = array();
	$order_rate_array = array();
	$check_array = array();
	foreach ($res as $val) 
	{
		if($check_array[$val['ID']]=="")
		{
			// echo $val['ID']."==".$val['ORDER_QUANTITY']."<br>";
			$order_qty_array[$val['PO_ID']][$val['ITEM_NUMBER_ID']]+=$val['ORDER_QUANTITY'];
			// $tot_data_array[$val['FLOOR_ID']][$val['SEWING_LINE']][$val['JOB_NO_MST']][$val['PO_ID']][$val['ITEM_NUMBER_ID']]['order_quantity']+=$val['ORDER_QUANTITY'];
			$check_array[$val['ID']] = $val['ID'];
			$order_rate_array[$val['PO_ID']][$val['ITEM_NUMBER_ID']]['order_rate'] += $val['ORDER_RATE'];
			$order_rate_array[$val['PO_ID']][$val['ITEM_NUMBER_ID']]['tot_row']++;
		}
		$tot_data_array[$val['FLOOR_ID']][$val['SEWING_LINE']][$val['PO_ID']][$val['ITEM_NUMBER_ID']]['input_qty']+=$val['INPUT_QTY'];
		$tot_data_array[$val['FLOOR_ID']][$val['SEWING_LINE']][$val['PO_ID']][$val['ITEM_NUMBER_ID']]['output_qty']+=$val['OUTPUT_QTY'];
		if($val['FIRST_INPUT_DATE']!="")
		{
			$tot_data_array[$val['FLOOR_ID']][$val['SEWING_LINE']][$val['PO_ID']][$val['ITEM_NUMBER_ID']]['first_input_date']=$val['FIRST_INPUT_DATE'];
		}
	}
		 
	// echo "<pre>";print_r($order_qty_array);die;
	/*===================================================================================== /
	/										po active days									/
	/===================================================================================== */
    $po_active_sql="SELECT a.sewing_line,a.production_date,c.id as po_id,a.item_number_id from  wo_po_break_down c,pro_garments_production_mst a where  a.po_break_down_id=c.id and a.production_type=5 and  a.status_active=1 and a.is_deleted=0 and c.is_deleted=0  $jobIds_cond group by  a.sewing_line,a.production_date,c.id,a.item_number_id";
    // echo $po_active_sql;die;
	foreach(sql_select($po_active_sql) as $vals)
	{
		$prod_dates=$vals[csf('production_date')];
		if($duplicate_date_arr[$vals[csf('sewing_line')]][$vals[csf('po_id')]][$vals[csf('item_number_id')]][$prod_dates]=="")
		{
			$active_days_arr[$vals[csf('sewing_line')]][$vals[csf('po_id')]][$vals[csf('item_number_id')]]++;
			$duplicate_date_arr[$vals[csf('sewing_line')]][$vals[csf('po_id')]][$vals[csf('item_number_id')]][$prod_dates]=$prod_dates;
		}
	}
	// echo "<pre>"; print_r($active_days_arr);

	/*===================================================================================== /
	/									gmts defect data  									/
	/===================================================================================== */
	$po_id_cond = where_con_using_array($poIdArr,0,"a.po_break_down_id");
	$sql = "SELECT a.sewing_line,a.po_break_down_id,a.item_number_id,b.DEFECT_TYPE_ID,b.DEFECT_POINT_ID,b.defect_qty  from pro_garments_production_mst a, PRO_GMTS_PROD_DFT b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and b.production_type=5 and b.DEFECT_TYPE_ID in(2,3,4) and a.production_date=$txt_date $po_id_cond";
	// echo $sql;die;
	$res = sql_select($sql);
	$defect_data_array = array();
	foreach ($res as $v) 
	{
		$defect_data_array[$v['SEWING_LINE']][$v['PO_BREAK_DOWN_ID']][$v['ITEM_NUMBER_ID']][$v['DEFECT_TYPE_ID']] += $v['DEFECT_QTY'];
	}
	// echo "<pre>"; print_r($defect_data_array);
	/*===================================================================================== /
	/									Operation Bulletin 									/
	/===================================================================================== */
	$style_cond = where_con_using_array($all_style_arr,1,"a.style_ref");
	$sqlgsd="SELECT a.PROCESS_ID,a.style_ref,a.gmts_item_id,b.id, b.mst_id, b.row_sequence_no, b.body_part_id, b.lib_sewing_id, b.resource_gsd, b.attachment_id, b.efficiency, b.total_smv, b.target_on_full_perc from PPL_GSD_ENTRY_MST a,ppl_gsd_entry_dtls b where a.id=b.mst_id $style_cond and a.bulletin_type=4 and b.is_deleted=0 order by b.row_sequence_no asc";
	// echo $sqlgsd;die;
	$gsd_res=sql_select($sqlgsd);
	$mst_id_arr = array();
	foreach($gsd_res as $row)
	{
		$mst_id_arr[$row['MST_ID']] = $row['MST_ID'];
	}
	$mst_id_cond = where_con_using_array($mst_id_arr,0,"a.gsd_mst_id");
	// ======================================================================
	$balanceDataArray=array();
	$blData=sql_select("SELECT a.id, gsd_dtls_id, smv, layout_mp from ppl_balancing_mst_entry a, ppl_balancing_dtls_entry b where a.id=b.mst_id and a.balancing_page=1 $mst_id_cond and a.is_deleted=0 and b.is_deleted=0");
	foreach($blData as $row)
	{
		$balanceDataArray[$row[csf('gsd_dtls_id')]]['smv']=$row[csf('smv')];
		$balanceDataArray[$row[csf('gsd_dtls_id')]]['layout_mp']=$row[csf('layout_mp')];
	}

	$gsd_data_array = array();

	foreach($gsd_res as $slectResult)
	{
		if($balanceDataArray[$slectResult[csf('id')]]['smv']>0)	
		{
			$smv=$balanceDataArray[$slectResult[csf('id')]]['smv'];
		}
		else
		{
			$smv=$slectResult[csf('total_smv')];
		}
		
		$rescId=$slectResult[csf('resource_gsd')];
		$layOut=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
		 
		if($rescId==40 || $rescId==41 || $rescId==43 || $rescId==44 || $rescId==48 || $rescId==68 || $rescId==69 || $rescId==70 || $rescId==147)
		{
			$helperSmv=$helperSmv+$smv;
			$helperMp=$helperMp+$layOut;
		}
		else if($rescId==53)
		{
			$fIMSmv=$fIMSmv+$smv;
			$fImMp=$fImMp+$layOut;
		}
		else if($rescId==54)
		{
			$fQISmv=$fQISmv+$smv;
			$fQiMp=$fQiMp+$layOut;
		}
		else if($rescId==55)
		{
			$polyHelperSmv=$polyHelperSmv+$smv;
			$polyHelperMp=$polyHelperMp+$layOut;
		}
		else if($rescId==56)
		{
			$pkSmv=$pkSmv+$smv;
			$pkMp=$pkMp+$layOut;
		}
		else if($rescId==90)
		{
			$htSmv=$htSmv+$smv;
			$htMp=$htMp+$layOut;
		}
		else if($rescId==176)
		{
			$imSmv=$imSmv+$smv;
			$imMp=$imMp+$layOut;
		}
		else
		{
			$machineSmv=$machineSmv+$smv;
			$machineMp=$machineMp+$layOut;
			
			$mpSumm[$rescId]+= $layOut;
		}
		$i++;
		$totMpSumm = $helperMp + $machineMp + $sQiMp + $fImMp + $fQiMp + $polyHelperMp + $pkMp + $htMp + $imMp;
		// echo $helperMp."<br>";
		
		$gsd_data_array[$slectResult['STYLE_REF']][$slectResult['GMTS_ITEM_ID']]['operator'] = $machineMp;
		$gsd_data_array[$slectResult['STYLE_REF']][$slectResult['GMTS_ITEM_ID']]['sew_helper'] = $helperMp;
		$gsd_data_array[$slectResult['STYLE_REF']][$slectResult['GMTS_ITEM_ID']]['plan_man'] = $totMpSumm;
	}

	
	// echo "<pre>";print_r($gsd_data_array);echo "</pre>";
	/*===================================================================================== /
	/										smv sorce 										/
	/===================================================================================== */
	$lc_com_ids = implode(",",$lc_com_array);
	$poIds_cond = where_con_using_array($poIdArr,0,"b.id");
   	$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($lc_com_ids) and variable_list=25 and   status_active=1 and is_deleted=0");
	// echo $smv_source;
	
	if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
    if($smv_source==3) // from gsd enrty
	{
		$style_cond = where_con_using_array($all_style_arr,1,"a.style_ref");
		$sql_item="SELECT a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID  from PPL_GSD_ENTRY_MST a where a.APPLICABLE_PERIOD <= $txt_date and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 $style_cond  group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID ORDER BY a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC";//and a.APPROVED=1
		$gsdSqlResult=sql_select($sql_item);
		// echo $sql_item;die;
		
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
		$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost,c.smv_set from wo_po_details_master a,wo_po_break_down b, wo_po_details_mas_set_details c where b.job_id=a.id and b.job_id=c.job_id and a.company_name in($lc_com_ids) $poIds_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		// echo $sql_item;
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
	// echo "<pre>";print_r($item_smv_array);echo "</pre>";
	$tbl_width = 2660;

	$rowspan_arr = array();
	$rowspan_gt = 0;
	$gt_day_target = 0;
	$gt_acv_qty = 0;
	$floor_total_array = array();
	$floor_defect_count_arr = array();
	foreach ($data_array as $flr_id => $flr_data) 
	{
		foreach ($flr_data as $sl => $sl_data) 
		{
			foreach ($sl_data as $l_name => $l_data) 
			{				
				$rowspan_arr[$flr_id]++;
				$rowspan_gt++;
				$floor_total_array[$flr_id]['all_qnty'] += $l_data['all_qnty'];			
				$floor_total_array[$flr_id]['reject_qty'] += $l_data['reject_qty'];			
				$floor_total_array[$flr_id]['replace_qty'] += $l_data['replace_qty'];			
				$floor_total_array[$flr_id]['day_target'] += $prod_resource_array[$l_data['line_id']]['tpd'];
				$gt_day_target += $prod_resource_array[$l_data['line_id']]['tpd'];
				$gt_acv_qty += $l_data['all_qnty'];	

				// ===========================================
				$po_item_arr = array_unique(array_filter(explode("__",$line_wise_po_item_array[$l_data['line_id']])));
				$produce_min = 0;
				$available_min = 0;
				foreach ($po_item_arr as $key => $v) 
				{
					$po_itm_ex = explode("**",$v);

					$floor_defect_count_arr[$flr_id] += $defect_data_array[$l_data['line_id']][$po_itm_ex[0]][$po_itm_ex[1]][4] + $defect_data_array[$l_data['line_id']][$po_itm_ex[0]][$po_itm_ex[1]][2] + $defect_data_array[$l_data['line_id']][$po_itm_ex[0]][$po_itm_ex[1]][3];

					$produce_min += $item_smv_array[$po_itm_ex[0]][$po_itm_ex[1]]*$l_data['all_qnty'];
					$available_min +=  $prod_resource_array[$l_data['line_id']]['man_power']*($prod_resource_array[$l_data['line_id']]['working_hour']*60);									
								
					$floor_total_array[$flr_id]['produce_min'] += $produce_min;
					$floor_total_array[$flr_id]['available_min'] += $available_min;
					
				}
			}
		}
		$rowspan_gt++;
	}
	$gt_day_perf = ($gt_acv_qty/$gt_day_target)*100;
	// echo $rowspan_gt;die;
	// echo "<pre>";print_r($floor_total_array);echo "</pre>";
	ob_start();
	?>               
	<fieldset style="width:<?=$tbl_width+20;?>px">
		<style>
			th a,td a {color: red !important;}
		</style>
       <table width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0"> 
            <tr class="form_caption">
                <td colspan="25" align="center"><strong><? echo $report_title; ?></strong></td> 
            </tr>
            <tr class="form_caption">
                <td colspan="25" align="center"><strong><? echo $companyArr[$comapny_id]; ?></strong></td> 
            </tr>
            <tr class="form_caption">
                <td colspan="25" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date)) ); ?></strong></td> 
            </tr>
        </table>
         
		<!-- =================================== header start ===================================== -->
        <table id="table_header_1" class="rpt_table" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
				<tr>
                    <th rowspan="2" width="80"><p>Floor Name</p></th>
                    <th rowspan="2" width="80"><p>Line No</p></th>
                    <th rowspan="2" width="80"><p>Buyer</p></th>
                    <th rowspan="2" width="140"><p>Style</p></th>
                    <th rowspan="2" width="60"><p>Order Qty</p></th>
                    <th rowspan="2" width="120"><p>Item</p></th>

					<th colspan="4">Day WIP</th>
					
                    <th rowspan="2" width="60"><p>Day TGT</p></th>
                    <th rowspan="2" width="60"><p>Floor TGT</p></th>
                    <th rowspan="2" width="60"><p>G. TTL</p></th>
                    <th rowspan="2" width="60"><p>SMV</p></th>
                    <th rowspan="2" width="60"><p>ACV Qty</p></th>
                    <th rowspan="2" width="60">QC Pass Qty</th>
                    <th rowspan="2" width="60"><p>Decision Pending Qnty</p></th>
                    <th rowspan="2" width="60"><p>DHU</p></th>
                    <th rowspan="2" width="60"><p>Floor D.H.U</p></th>
                    <th rowspan="2" width="80"><p>Productivity/per man/ per hour</p></th>
                    <th rowspan="2" width="60"><p>W. hour</p></th>
                    <th rowspan="2" width="60"><p>TTL Acv</p></th>
                    <th rowspan="2" width="60"><p>G.TTL</p></th>
                    <th rowspan="2" width="60" title="(ACV Qty / Day TGT)"><p>Line wise <br>perf%</p></th>
                    <th rowspan="2" width="60" title=" (TTL Acv / Floor TGT)"><p>TTL perf%</p></th>
                    <th rowspan="2" width="60" title="{(Day TGT  SMV) / (Plan Man  W. hour * 60)}"><p>PLAN. <br>EFF%</p></th>
                    <th rowspan="2" width="60" title=" (G. Total ACV Qty / G. Total Day TGT)"><p>F. AVG. <br>perf%</p></th>
                    <th rowspan="2" width="60" title="(Line Produce Min / Available Min) * 100"><p>DAY. Eff.%</p></th>
                    <th rowspan="2" width="60" title="(Floor Produce Min / Available Min) * 100"><p>AVG. Eff.%</p></th>

					<th colspan="3">Manpower</th>
					
                    <th rowspan="2" width="60"><p> Helper <br> Used %</p></th>
                    <th rowspan="2" width="60"><p>Op. Work <br>as Helper</p></th>
                    <th rowspan="2" width="60"><p>Total</p></th>
                    <th rowspan="2" width="60"><p>Plan Man</p></th>
                    <th rowspan="2" width="60"><p>MP <br>Variation</p></th>
                    <th rowspan="2" width="60"><p>R. Days</p></th>
				</tr>
                <tr>
                    <th width="60"><p>Day Input Qty</p></th>
                    <th width="60"><p>Total Input Qty</p></th>
                    <th width="60"><p>Total <br>Output Qty</p></th>
                    <th width="60"><p>Line WIP</p></th>

                    <th width="60"><p>Used <br>Machine</p></th>
                    <th width="60"><p>Used <br>Helper</p></th>
                    <th width="60"><p>Q.I</p></th>
                </tr>
            </thead>
        </table>
		
		<!-- ====================================== body part ================================== -->
        <div style="width:<?=$tbl_width+20;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<tbody>
					<?
					$gr_search_string = str_replace("'","",$cbo_company_id)."__".str_replace("'","",$cbo_location_id)."__".str_replace("'","",$cbo_buyer_name)."__".str_replace("'","",$style)."__".str_replace("'","",$job)."__".str_replace("'","",$cbo_gmts_item)."______".str_replace("'","",$txt_date);
					$i = 1;
					$gt = 0;
					$gr_order_qty = 0;
					$gr_day_input = 0;
					$gr_tot_input = 0;
					$gr_tot_output = 0;
					$gr_line_wip = 0;
					$gr_day_tgt = 0;
					$gr_floor_tgt = 0;
					$gr_acv_qty = 0;
					$gr_qc_pass_qty = 0;
					$gr_pending_qty = 0;
					$gr_prod_man = 0;
					$gr_working_hour = 0;
					$gr_ttl_acv = 0;
					$gr_user_machine = 0;
					$gr_used_helper = 0;
					$gr_used_qi = 0;
					$gr_iron_man = 0;
					$gr_manpower = 0;
					$gr_plan_manpower = 0;
					$gr_mp_variation = 0;
					$gr_tot_array = array();
					$hourly_cm_array = array();
					foreach ($data_array as $flr_id => $flr_data) 
					{
						$l = 0;
						$f=0;
						$flr_order_qty = 0;
						$flr_day_input = 0;
						$flr_tot_input = 0;
						$flr_tot_output = 0;
						$flr_line_wip = 0;
						$flr_day_tgt = 0;
						$flr_floor_tgt = 0;
						$flr_acv_qty = 0;
						$flr_qc_pass_qty = 0;
						$flr_pending_qty = 0;
						$flr_prod_man = 0;
						$flr_working_hour = 0;
						$flr_ttl_acv = 0;
						$flr_user_machine = 0;
						$flr_used_helper = 0;
						$flr_used_qi = 0;
						$flr_iron_man = 0;
						$flr_manpower = 0;
						$flr_plan_manpower = 0;
						$flr_mp_variation = 0;
						ksort($flr_data);
						$floor_tot_array = array();
						$search_string_floor = str_replace("'","",$cbo_company_id)."__".str_replace("'","",$cbo_location_id)."__".str_replace("'","",$cbo_buyer_name)."__".str_replace("'","",$style)."__".str_replace("'","",$job)."__".str_replace("'","",$cbo_gmts_item)."__".$flr_id."____".str_replace("'","",$txt_date);
						foreach ($flr_data as $sl => $sl_data) 
						{
							foreach ($sl_data as $l_name => $r) 
							{
								$buyer_name = implode(",",array_unique(array_filter(explode("**",$r['buyer_name']))));
								// $job_no_arr = array_unique(array_filter(explode("**",$r['job_no'])));
								$style_no = implode(",",array_unique(array_filter(explode("**",$r['style']))));
								$item_name = implode(",",array_unique(array_filter(explode("**",$r['item_name']))));
								$reason = implode(",",array_unique(array_filter(explode("**",$r['reason']))));
								$po_item_arr = array_unique(array_filter(explode("__",$line_wise_po_item_array[$r['line_id']])));
								$order_quantity = 0;
								$input_qty = 0;
								$output_qty = 0;
								$item_smv = "";
								$active_days = "";
								$defect_count = 0;
								$plan_effi = 0;
								$plan_man = 0;
								$produce_min = 0;
								$available_min = 0;
								foreach ($po_item_arr as $key => $v) 
								{
									$po_itm_ex = explode("**",$v);
									$order_quantity += $order_qty_array[$po_itm_ex[0]][$po_itm_ex[1]];
									$input_qty += $tot_data_array[$flr_id][$r['line_id']][$po_itm_ex[0]][$po_itm_ex[1]]['input_qty'];
									$output_qty += $tot_data_array[$flr_id][$r['line_id']][$po_itm_ex[0]][$po_itm_ex[1]]['output_qty'];
									$item_smv .= ($item_smv=="") ? $item_smv_array[$po_itm_ex[0]][$po_itm_ex[1]] : "/".$item_smv_array[$po_itm_ex[0]][$po_itm_ex[1]];

									$active_days .= ($active_days=="") ? $active_days_arr[$r['line_id']][$po_itm_ex[0]][$po_itm_ex[1]] : "/".$active_days_arr[$r['line_id']][$po_itm_ex[0]][$po_itm_ex[1]];

									$defect_count += $defect_data_array[$r['line_id']][$po_itm_ex[0]][$po_itm_ex[1]][4] + $defect_data_array[$r['line_id']][$po_itm_ex[0]][$po_itm_ex[1]][2] + $defect_data_array[$r['line_id']][$po_itm_ex[0]][$po_itm_ex[1]][3];

									$plan_effi = ($prod_resource_array[$r['line_id']]['tpd']*$item_smv_array[$po_itm_ex[0]][$po_itm_ex[1]])/($prod_resource_array[$r['line_id']]['working_hour']*60*$gsd_data_array[$po_itm_ex[2]][$po_itm_ex[1]]['plan_man']);

									// echo "(".$prod_resource_array[$r['line_id']]['tpd']."*".$item_smv_array[$po_itm_ex[0]][$po_itm_ex[1]].")/(".$prod_resource_array[$r['line_id']]['working_hour']."*60*".$gsd_data_array[$po_itm_ex[2]][$po_itm_ex[1]]['plan_man'].")<br>";

									$plan_man += $gsd_data_array[$po_itm_ex[2]][$po_itm_ex[1]]['plan_man'];

									$produce_min += $item_smv_array[$po_itm_ex[0]][$po_itm_ex[1]]*$r['all_qnty'];
									$available_min +=  $prod_resource_array[$r['line_id']]['man_power']*($prod_resource_array[$r['line_id']]['working_hour']*60);

									
								}
								$efficiency_min = ($produce_min/$available_min)*100;
								$wip = $input_qty - $output_qty;
								$dhu = ($defect_count) ? ($defect_count/($r['all_qnty']+$r['reject_qty']+$r['replace_qty']))*100 : 0;
								$dhu_title = "defect=".$defect_count."/(tot QC=".$r['all_qnty']."+tot_rej=".$r['reject_qty']."+replace=".$r['replace_qty'].")*100";
								// echo "(".$defect_count."/".$r['all_qnty'].")*100<br>";

								$man_power = $prod_resource_array[$r['line_id']]['man_power'];
								$act_operator = $prod_resource_array[$r['line_id']]['operator'];
								$helper = $prod_resource_array[$r['line_id']]['helper'];
								$qi = $prod_resource_array[$r['line_id']]['qi'];
								$iron_man = $prod_resource_array[$r['line_id']]['iron_man'];
								$terget_hour = $prod_resource_array[$r['line_id']]['terget_hour'];
								$working_hour = $prod_resource_array[$r['line_id']]['working_hour'];
								$day_target = $prod_resource_array[$r['line_id']]['tpd'];

								// $gsd_operator = $gsd_data_array[$r['style']][$itm_id]['operator'];
								// $sew_helper = $gsd_data_array[$r['style']][$itm_id]['sew_helper'];
								$line_wise_cost = $act_operator*32;
								$shortage_gain = $line_wise_cost - $today_tot_cm;

								$helper_used = ($helper/$act_operator)*100;
								// echo "(".$helper."/".$act_operator.")*100<br>";
								
								$achive = ($r['good_qnty']/$tpd)*100;

								$after_5pm_emp = 0;
								$after_5pm_wo_hour = 0;
								if(strtotime(date('d-M-Y')) == strtotime(str_replace("'","",$txt_date)))
								{
									if(date('H')>18)
									{
										$after_5pm_emp = $prod_resource_smv_adj_array[$r['line_id']]['number_of_emp'];
										$after_5pm_wo_hour = $prod_resource_smv_adj_array[$r['line_id']]['adjust_hour'];
									}
								}
								else
								{
									$ot_hour = $working_hour - $prod_resource_smv_adj_array[$r['line_id']]['adjust_hour'];
									// echo $working_hour ."-". $prod_resource_smv_adj_array[$r['line_id']]['adjust_hour']."<br>";
									if($ot_hour>10) // base time 8am to 6pm
									{
										$after_5pm_emp = $prod_resource_smv_adj_array[$r['line_id']]['number_of_emp'];
										$after_5pm_wo_hour = $prod_resource_smv_adj_array[$r['line_id']]['adjust_hour'];
									}												
								}

								$after_5pm_wo_min = $after_5pm_emp*$after_5pm_wo_hour*60;
								$input_min = ($man_power*60*$working_hour) - $after_5pm_wo_min;
								// $output_min = $r['good_qnty']*$item_smv;
								$line_effi = ($output_min*$input_min)/100;

								$pending_qty = $r['all_qnty'] - $r['hold_qnty'];

								$productivity = $r['all_qnty']/$man_power/$working_hour;
								$line_wise_perf = ($r['all_qnty']/$day_target)*100;
								$mp_varience = $man_power - $plan_man;
								
								$search_string = str_replace("'","",$cbo_company_id)."__".str_replace("'","",$cbo_location_id)."__".str_replace("'","",$cbo_buyer_name)."__".str_replace("'","",$style)."__".str_replace("'","",$job)."__".str_replace("'","",$cbo_gmts_item)."__".$flr_id."__".$r['line_id']."__".str_replace("'","",$txt_date);
								

								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
									<? if($f==0) {?>
									<td valign="middle" rowspan="<?=$rowspan_arr[$flr_id];?>" width="80"><p><?=$floorArr[$flr_id];?></p></td>
									<?}?>
									<td width="80" title="<?=$r['line_id'];?>"><p><?=$l_name;?></p></td>
									<td width="80"><p><?=$buyer_name;?></p></td>
									<td width="140"><p><?=$style_no;?></p></td>
									<td width="60" align="right"><p><?=number_format($order_quantity,0);?></p></td>
									<td width="120"><p><?=$item_name;?></p></td>

									<td  align="right" width="60"><p><?=number_format($r['totay_input_qnty'],0);?></p></td>
									<td  align="right" width="60"><p><?=$input_qty;?></p></td>
									<td  align="right" width="60"><p><?=$output_qty;?></p></td>
									<td  align="right" width="60"><p><?=$wip;?></p></td>

									<td  align="right" width="60"><p><?=$day_target;?></p></td>
									
									<? if($f==0) {?>
										<td valign="middle" rowspan="<?=$rowspan_arr[$flr_id];?>" align="right" width="60"><p><?=$floor_total_array[$flr_id]['day_target'];?></p></td>
									<?} if($gt==0){?>
									
									<td valign="middle" rowspan="<?=$rowspan_gt;?>" align="right" width="60"><p><?=number_format($gt_day_target,0);?></p></td>
									<? }?>
									<td  align="right" width="60"><p><?=$item_smv;?></p></td>
									<td  align="right" width="60"><p><?=$r['all_qnty'];?></p></td>
									<td  align="right" width="60"><p><?=$pending_qty;?></p></td>
									<td  align="right" width="60">
										<a href="javascript:void(0)" onclick="show_hold_reason_popup('<?=$search_string;?>');">
											<?=number_format($r['hold_qnty'],0);?>
										</a>
									</td>
									<td title="<?=$dhu_title;?>" align="right" width="60"><p><?=number_format($dhu,2);?></p></td>
									<? if($f==0) 
									{	
										
										// echo $floor_defect_count_arr[$flr_id]."/(".$floor_total_array[$flr_id]['all_qnty']."+".$floor_total_array[$flr_id]['reject_qty']."+".$floor_total_array[$flr_id]['replace_qty'].")*100<br>";
										// echo "<br>";
										$flr_dhu = 0;									
										$flr_dhu = ($floor_defect_count_arr[$flr_id]) ? ($floor_defect_count_arr[$flr_id]/($floor_total_array[$flr_id]['all_qnty']+$floor_total_array[$flr_id]['reject_qty']+$floor_total_array[$flr_id]['replace_qty']))*100 : 0;
										// echo "<br>ss";

										$dhu_floor_title = "defect=".$floor_defect_count_arr[$flr_id]."/(tot QC=".$floor_total_array[$flr_id]['all_qnty']."+tot_rej=".$floor_total_array[$flr_id]['reject_qty']."+replace=".$floor_total_array[$flr_id]['replace_qty'].")*100";
										?>
										<td title="<?=$dhu_floor_title;?>" valign="middle" rowspan="<?=$rowspan_arr[$flr_id];?>" align="right" width="60"><p><?=number_format($flr_dhu,2);?></p></td>
										<?
									}
									?>
									<td  align="right" width="80"><p><?=number_format($productivity,2);?></p></td>
									<td  align="right" width="60"><p><?=$working_hour;?></p></td>
									<? if($f==0) {?>
										<td valign="middle" rowspan="<?=$rowspan_arr[$flr_id];?>" align="right" width="60"><p><?=$floor_total_array[$flr_id]['all_qnty'];?></p></td>
									<?}if($gt==0){?>
									<td valign="middle" rowspan="<?=$rowspan_gt;?>" align="right" width="60"><p><?=number_format($gt_acv_qty,0);?></p></td>
									<?}?>
									<td  align="right" width="60"><p><?=number_format($line_wise_perf,2);?>%</p></td>
									<? if($f==0) 
									{
										$flr_wise_perf = ($floor_total_array[$flr_id]['all_qnty']/$floor_total_array[$flr_id]['day_target'])*100;
										?>
										<td valign="middle" rowspan="<?=$rowspan_arr[$flr_id];?>" align="right" width="60"><p><?=number_format($flr_wise_perf,2);?>%</p></td>
										<?
									}
									?>
									<td  align="right" width="60"><p><?=number_format($plan_effi,2);?>%</p></td>
									<?if($gt==0){?>
									<td  valign="middle" rowspan="<?=$rowspan_gt;?>"  align="right" width="60"><p><?=number_format($gt_day_perf,2);?>%</p></td>
									<?}?>
									<td  align="right" width="60"><p><?=number_format($efficiency_min,2);?>%</p></td>
									<? if($f==0) {
										$flr_effi = ($floor_total_array[$flr_id]['produce_min']/$floor_total_array[$flr_id]['available_min'])*100;
										?>
										<td valign="middle" rowspan="<?=$rowspan_arr[$flr_id];?>" align="right" width="60"><p>
											<?=number_format($flr_effi,2);?>%
										</p></td>
									<?}?>

									<td  align="right" width="60"><p><?=number_format($act_operator,0);?></p></td>
									<td  align="right" width="60"><p><?=number_format($helper,0);?></p></td>
									<td  align="right" width="60"><p><?=number_format($qi,0);?></p></td>

									<td  align="right" width="60"><p><?=number_format($helper_used,2);?>%</p></td>
									
									<td valign="middle" align="right" width="60"><p><?=number_format($iron_man,0);?></p></td>
									
									<td  align="right" width="60"><p><?=number_format($man_power,0);?></p></td>
									<td  align="right" width="60"><p><?=number_format($plan_man,0);?></p></td>
									<td  align="right" width="60"><p><?=number_format($mp_varience,0);?></p></td>
									<td  align="center" width="60"><p>&nbsp;<?=$active_days;?></p></td>
								</tr>
								<?
								$i++;
								$f++;
								$gt++;
																
								$flr_order_qty += $order_quantity;
								$flr_day_input += $r['totay_input_qnty'];
								$flr_tot_input += $input_qty;
								$flr_tot_output += $output_qty;
								$flr_line_wip += $wip;
								$flr_day_tgt += $day_target;
								// $flr_floor_tgt += $order_quantity;
								$flr_acv_qty += $r['all_qnty'];
								$flr_qc_pass_qty += $pending_qty;
								$flr_pending_qty += $r['hold_qnty'];

								$flr_prod_man += $order_quantity;
								$flr_working_hour += $order_quantity;
								$flr_ttl_acv += $order_quantity;

								$flr_user_machine += $act_operator;
								$flr_used_helper += $helper;
								$flr_used_qi += $qi;
								$flr_iron_man += $iron_man;
								$flr_manpower += $man_power;
								$flr_plan_manpower += $plan_man;
								$flr_mp_variation += $man_power - $plan_man;
								//=============================================== 
								$gr_order_qty += $order_quantity;
								$gr_day_input += $r['totay_input_qnty'];
								$gr_tot_input += $input_qty;
								$gr_tot_output += $output_qty;
								$gr_line_wip += $wip;
								$gr_day_tgt += $day_target;
								// $gr_floor_tgt += $order_quantity;
								$gr_acv_qty += $r['all_qnty'];
								$gr_qc_pass_qty += $pending_qty;
								$gr_pending_qty += $r['hold_qnty'];

								$gr_prod_man += $order_quantity;
								$gr_working_hour += $order_quantity;
								$gr_ttl_acv += $order_quantity;

								$gr_user_machine += $act_operator;
								$gr_used_helper += $helper;
								$gr_used_qi += $qi;
								$gr_iron_man += $iron_man;
								$gr_manpower += $man_power;
								$gr_plan_manpower += $plan_man;
								$gr_mp_variation += $man_power - $plan_man;						
									
							}
						}
						?>
						<tr style="text-align: right;font-weight:bold;background:#cddcdc">
							
							<td width="80"><p></p></td>
							<td width="80"><p></p></td>
							<td width="80"><p></p></td>
							<td width="140"><p></p></td>
							<td width="60"><p><?//=$flr_order_qty;?></p></td>
							<td width="120"><p>Floor Total</p></td>

							<td width="60"><p><?=$flr_day_input;?></p></td>
							<td width="60"><p><?=$flr_tot_input;?></p></td>
							<td width="60"><p><?=$flr_tot_output;?></p></td>
							<td width="60"><p><?=$flr_line_wip;?></p></td>

							<td width="60"><p><?=number_format($flr_day_tgt,0);?></p></td>
							<!-- <td width="60"><p></p></td> -->
							<td width="60"><p></p></td>
							<td width="60"><p></p></td>
							<td width="60"><p><?=number_format($flr_acv_qty,0);?></p></td>
							<td width="60"><p><?=number_format($flr_qc_pass_qty,0);?></p></td>
							<td width="60">
								<a href="javascript:void(0)" onclick="show_hold_reason_popup('<?=$search_string_floor;?>');">
									<?=number_format($flr_pending_qty,0);?>
								</a>
							</td>
							<td width="60"><p></p></td>
							<td width="60"><p></p></td>
							<td width="80"><p></p></td>
							<td width="60"><p></p></td>
							<!-- <td width="60"><p></p></td> -->
							<td width="60"><p></p></td>
							<td width="60"><p></p></td>
							<td width="60"><p></p></td>
							<!-- <td width="60"><p></p></td> -->
							<td width="60"><p></p></td>
							<td width="60"><p></p></td>
							<td width="60"><p></p></td>

							<td width="60"><p><?=number_format($flr_user_machine,0);?></p></td>
							<td width="60"><p><?=number_format($flr_used_helper,0);?></p></td>
							<td width="60"><p><?=number_format($flr_used_qi,0);?></p></td>

							<td width="60"><p></p></td>
							<td width="60"><p><?=number_format($flr_iron_man,0);?></p></td>
							<td width="60"><p><?=number_format($flr_manpower,0);?></p></td>
							<td width="60"><p><?=number_format($flr_plan_manpower,0);?></p></td>
							<td width="60"><p><?=number_format($flr_mp_variation,0);?></p></td>
							<td width="60"><p></p></td>
						</tr>
						<?
					}
					?>
				</tbody>
            </table>
		</div>
		<!-- ==================================== footer part ================================ -->
		<table id="table_footer_1" class="rpt_table" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
            <tfoot>
                <tr>					
					<th width="80"><p></p></th>
					<th width="80"><p></p></th>
					<th width="80"><p></p></th>
					<th width="140"><p></p></th>
					<th width="60"><p><?//=$gr_order_qty;?></p></th>
					<th width="120"><p>Grand Total</p></th>

					<th width="60"><p><?=$gr_day_input;?></p></th>
					<th width="60"><p><?=$gr_tot_input;?></p></th>
					<th width="60"><p><?=$gr_tot_output;?></p></th>
					<th width="60"><p><?=$gr_line_wip;?></p></th>

					<th width="60"><p><?=number_format($gr_day_tgt,0);?></p></th>
					<th width="60"><p></p></th>
					<th width="60"><p></p></th>
					<th width="60"><p></p></th>
					<th width="60"><p><?=number_format($gr_acv_qty,0);?></p></th>
					<th width="60"><p><?=number_format($gr_qc_pass_qty,0);?></p></th>
					<th width="60">
						<a href="javascript:void(0)" onclick="show_hold_reason_popup('<?=$gr_search_string;?>');">
							<?=number_format($gr_pending_qty,0);?>
						</a>
					</th>
					<th width="60"><p></p></th>
					<th width="60"><p></p></th>
					<th width="80"><p></p></th>
					<th width="60"><p></p></th>
					<th width="60"><p></p></th>
					<th width="60"><p></p></th>
					<th width="60"><p></p></th>
					<th width="60"><p></p></th>
					<th width="60"><p></p></th>
					<th width="60"><p></p></th>
					<th width="60"><p></p></th>
					<th width="60"><p></p></th>

					<th width="60"><p><?=number_format($gr_user_machine,0);?></p></th>
					<th width="60"><p><?=number_format($gr_used_helper,0);?></p></th>
					<th width="60"><p><?=number_format($gr_used_qi,0);?></p></th>

					<th width="60"><p></p></th>
					<th width="60"><p><?=number_format($gr_iron_man,0);?></p></th>
					<th width="60"><p><?=number_format($gr_manpower,0);?></p></th>
					<th width="60"><p><?=number_format($gr_plan_manpower,0);?></p></th>
					<th width="60"><p><?=number_format($gr_mp_variation,0);?></p></th>
					<th width="60"><p></p></th>
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

if($action=="hold_reason_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  ); 
	$line_library=return_library_array( "select id,line_name from lib_sewing_line ", "id", "line_name"  ); 
	$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	extract($_REQUEST);
	list($company_id,$location_id,$buyer_id,$style,$job,$item,$floor_id,$line_id,$pr_date) = explode("__",$search_string);
	$company_cond = ($company_id!=0) ? " and d.company_id=$company_id" : "";
	$location_cond = ($location_id!=0) ? " and d.location=$location_id" : "";
	$buyer_cond = ($buyer_id!=0) ? " and a.buyer_name=$buyer_id" : "";
	$style_cond = ($style!="") ? " and a.style_ref_no='$style'" : "";
	$job_cond = ($job!="") ? " and a.job_no_prefix_num='$job'" : "";
	$item_cond = ($item!=0) ? " and d.item_number_id=$item" : "";
	$floor_cond = ($floor_id!="") ? " and d.floor_id=$floor_id" : "";
	$line_cond = ($line_id!="") ? " and d.sewing_line=$line_id" : "";
	
	$sql = "SELECT a.style_ref_no,a.buyer_name,d.floor_id,d.sewing_line,d.item_number_id,e.bndl_hold_reason,e.production_qnty from wo_po_details_master a,WO_PO_BREAK_DOWN b,WO_PO_COLOR_SIZE_BREAKDOWN c,PRO_GARMENTS_PRODUCTION_MST d,PRO_GARMENTS_PRODUCTION_DTLS e where a.id=b.job_id and b.id=c.po_break_down_id and b.id=d.po_break_down_id and c.id=e.color_size_break_down_id and d.id=e.mst_id  and d.production_date='$pr_date' and e.bndl_hold_reason !=0 and d.status_Active=1 and d.is_deleted=0 and e.status_Active=1 and e.is_deleted=0 $company_cond $location_cond $buyer_cond $style_cond $job_cond $item_cond $floor_cond $line_cond";
	// echo $sql;
	$res = sql_select($sql);
	$reason_data = array();
	foreach ($res as $v) 
	{
		$reason_data[$v['BUYER_NAME']][$v['STYLE_REF_NO']][$v['ITEM_NUMBER_ID']][$v['FLOOR_ID']][$v['SEWING_LINE']][$v['BNDL_HOLD_REASON']] += $v['PRODUCTION_QNTY'];
	}

	?>
	<fieldset>
		<div id="data_panel" style="width:100%;text-align: center;padding: 5px;">
			<script>
	            function new_window()
	            {
	            	$('.fltrow').hide();
	                var w = window.open("Surprise", "#");
	                var d = w.document.open();
	                d.write(document.getElementById('details_reports').innerHTML);
	                d.close();
	                $('.fltrow').show();
	            }
	        </script>
			
	    	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onClick="new_window()" />
	    	<span id="popup_report_container" align="center" style="width: 120px;"> </span>
		</div>
		<?
	    ob_start();
		?>
		<div id="details_reports" align="center" style="width:100%;" >
			<table width="720" class="rpt_table" rules="all" border="1">
				<thead>
					<tr>
						<th width="100">Floor</th>
						<th width="100">Line</th>
						<th width="100">Buyer</th>
						<th width="100">Style</th>
						<th width="100">Item</th>
						<th width="60">Hold Qty</th>
						<th width="150">Cause</th>
					</tr>
				</thead>
				<tbody>
					<?
					$tot = 0;
					foreach ($reason_data as $buyer => $buyer_data) 
					{
						foreach ($buyer_data as $style => $style_data) 
						{
							foreach ($style_data as $item => $item_data) 
							{
								foreach ($item_data as $floor_id => $floor_data) 
								{
									foreach ($floor_data as $line_id => $line_data) 
									{
										foreach ($line_data as $reason_id => $r) 
										{
											$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$line_id]);
											$line_name="";
											foreach($line_resource_mst_arr as $resource_id)
											{
												$line_name .= ($line_name == "") ? $line_library[$resource_id] : ",".$line_library[$resource_id];
											}

											?>
											<tr>
												<td><?=$floor_library[$floor_id];?></td>
												<td><?=$line_name;?></td>
												<td><?=$buyer_library[$buyer];?></td>
												<td><?=$style;?></td>
												<td><?=$garments_item[$item];?></td>
												<td align="right"><?=$r;?></td>
												<td><?=$bundle_hold_reason_array[$reason_id];?></td>
											</tr>
											<?
											$tot += $r;
										}
									}
								}
							}
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th>Total</th>
						<th><?=$tot;?></th>
						<th></th>
					</tr>
				</tfoot>
			</table>
		</div>
      	<?
		$html=ob_get_contents();
		ob_flush();
		
		foreach (glob(""."*.xls") as $filename) 
		{
		   @unlink($filename);
		}
		
		//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');	
		$is_created = fwrite($create_new_excel,$html);	
		?>
	    <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
	    <script>
			$(document).ready(function(e) 
			{
				document.getElementById('popup_report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Convert to Excel" name="excel" id="excel" style="padding:0 2px;" class="formbutton"/></a>&nbsp;&nbsp;';
			});	
		</script>
	</fieldset>
	<?
}