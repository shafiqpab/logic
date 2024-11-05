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
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1
	and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type 
	where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","");  
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data' 
	order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/hourly_production_monitoring_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 ); 
	exit();    	 
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_id", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=5 
	and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );     	 	
	exit();    	 
}
/*
if ($action=="load_drop_down_line")
{
	$explode_data = explode("_",$data);
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$explode_data[2] and variable_list=23 and is_deleted=0 and status_active=1");
	$txt_date = $explode_data[3];
	
	$cond="";
	if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		
		if($txt_date=="")
		{
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_id= $explode_data[1]";
			if( $explode_data[0]!=0 ) $cond = " and floor_id= $explode_data[0]";
			$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 $cond");
		}
		else
		{
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and a.location_id= $explode_data[1]";
			if( $explode_data[0]!=0 ) $cond = " and a.floor_id= $explode_data[0]";
			
			$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".change_date_format($txt_date,'yyyy-mm-dd')."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id");
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

*/

if($action=="job_wise_search")
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
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company and variable_list=23 and is_deleted=0 
	and status_active=1");
    if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		
		if($txt_date=="")
		{
			if(  $location!=0 ) $cond = " and a.location_id= $location";
			if( $floor_id!=0 ) $cond.= " and a.floor_id= $floor_id";
			$line_data="select a.id, b.line_name from prod_resource_mst a,lib_sewing_line b where a.is_deleted=0 and a.line_number=b.id $cond";
		}
		else
		{
			if(  $location!=0 ) $cond = " and a.location_id= $location";
			if( $floor_id!=0 ) $cond.= " and a.floor_id= $floor_id";
			
			$line_data="select a.id, c.line_name from prod_resource_mst a, prod_resource_dtls b,lib_sewing_line c where a.id=b.mst_id and 
			a.line_number=c.id and b.pr_date='".change_date_format($txt_date,'yyyy-mm-dd')."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id";
	     }
		 
      	echo create_list_view("list_view", "Line ","250","300","310",0, $line_data , "js_set_value", "id,line_name", "", 1, "0", $arr, "line_name", 
	  	"","setFilterGrid('list_view',-1)","0","",1);	
	  	echo "<input type='hidden' id='txt_selected_id' />";
	  	echo "<input type='hidden' id='txt_selected' />";
		
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
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$comapny_id=str_replace("'","",$cbo_company_id);
    $today_date=date("Y-m-d");
	$txt_producting_day="".str_replace("'","",$txt_date)."";
	//***************************************************************************************************************************
	if($db_type==0)
	{
	$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,
	prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$comapny_id and shift_id=1
	and pr_date=$txt_date","line_start_time");	
	}
	else
	{
	$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,
	prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$comapny_id and shift_id=1 
	and pr_date=$txt_date","line_start_time");
	}
	//==============================shift time===================================================================================================
	$start_time_arr=array();
	if($db_type==0)
	{
	$start_time_data_arr=sql_select("select company_name, shift_id, TIME_FORMAT( prod_start_time, '%H:%i' ) as prod_start_time,
	TIME_FORMAT( lunch_start_time, '%H:%i' ) as lunch_start_time from variable_settings_production where company_name in($comapny_id) and  
	shift_id=1  and variable_list=26 and status_active=1 and is_deleted=0");
	}
	else
	{
	$start_time_data_arr=sql_select("select company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time,
	TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($comapny_id) and  shift_id=1 
	and variable_list=26 and status_active=1 and is_deleted=0");	
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
	
	$start_hour_arr[$j+1]='23:59';
	if($prod_start_hour>$min_shif_start)  $prod_start_hour=$min_shif_start;
	$actual_date=date("Y-m-d");
	$actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$txt_date)));
	$actual_time=substr(date("Y-m-d H:i:s",strtotime($pc_date_time)),11,2);	
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
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_id and variable_list=23 and is_deleted=0 
	and status_active=1");
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if($_SESSION['logic_erp']["data_level_secured"]==1)
		{
		if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
		else                                       $buyer_id_cond="";
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
	
	if(str_replace("'","",$cbo_company_id)==0) $company_name=""; else $company_name="and a.company_id=".str_replace("'","",$cbo_company_id)."";
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
	
	if(str_replace("'","",$cbo_floor_id)==0) $floor=""; else $floor="and a.floor_id=".str_replace("'","",$cbo_floor_id)."";
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
	
	if($prod_reso_allo[0]==1)
	{
		$prod_resource_array=array();
		$dataArray=sql_select("select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator,
		b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity
		from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id=$comapny_id
		and b.pr_date=$txt_date and b.is_deleted=0 and c.is_deleted=0");
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
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['capacity']=$val[csf('capacity')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust']=$val[csf('smv_adjust')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];
		}
		if($db_type==0)
		{
		$dataArray=sql_select("select a.id,b.pr_date,d.shift_id,TIME_FORMAT( d.prod_start_time, '%H:%i' ) as prod_start_time,
		TIME_FORMAT( d.lunch_start_time, '%H:%i' ) as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d
		where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and shift_id=1 and b.is_deleted=0 and d.is_deleted=0"); 
		}
		else
		{
		$dataArray=sql_select("select a.id,b.pr_date,d.shift_id,TO_CHAR(d.prod_start_time,'HH24:MI') as prod_start_time,
		TO_CHAR(d.lunch_start_time,'HH24:MI') as lunch_start_time 
		from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and
		a.company_id=$comapny_id and shift_id=1 and b.is_deleted=0 and d.is_deleted=0");
		}
		$line_number_arr=array();
		foreach($dataArray as $val)
		{
		$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['shift_id']=$val[csf('shift_id')];
		$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['prod_start_time']=$val[csf('prod_start_time')];
		$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['lunch_start_time']=$val[csf('lunch_start_time')];
		}
	}
 //********************************************************************************************************************************************************
  	if($db_type==0)
	{
	$manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1
	and comp.is_deleted=0 $company_cond","company_id");
	}
	else
	{
	$manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1
	and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
	}
	
   	$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($manufacturing_company) and variable_list=25 and 
	status_active=1 and is_deleted=0");
    if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
	
    if($smv_source==3)
	{
		$sql_item="select b.id, a.sam_style, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0
		and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$resultItem=sql_select($sql_item);
		foreach($resultItem as $itemData)
		{
		$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
		}
	}
	else
	{
		$sql_item="select b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a,
		wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.company_name 
		in($manufacturing_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
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
	
	if($db_type==0)
	{
		$sql="select  a.company_id, a.location, a.floor_id,d.floor_serial_no,e.sewing_line_serial, a.prod_reso_allo, a.production_date, a.sewing_line,
		b.buyer_name  as buyer_name,
		a.po_break_down_id, a.item_number_id,
		c.po_number as po_number,
		sum(a.production_quantity) as good_qnty,"; 
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
			$sql.="sum(CASE WHEN a.production_hour>'$bg' and  a.production_hour<='$end' and a.production_type=5 THEN production_quantity else 0 END)
			AS $prod_hour,";
			}
			$first=$first+1;
		}
		$sql.="sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 
		THEN production_quantity else 0 END) AS prod_hour23
		from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a
		left join lib_prod_floor d on a.floor_id=d.id
		left join lib_sewing_line e on a.sewing_line=e.id 
		where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $company_name $location
		$floor $line   $txt_date_from 
		group by a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date,
		a.sewing_line,e.sewing_line_serial,b.buyer_name,a.item_number_id,c.po_number order by a.location, a.floor_id,e.sewing_line_serial";
	}
	else if($db_type==2)
	{
		$sql="select  a.company_id, a.location, a.floor_id,d.floor_serial_no,e.sewing_line_serial, a.prod_reso_allo, a.production_date, a.sewing_line,
		b.buyer_name  as buyer_name,
		a.po_break_down_id, a.item_number_id,
		c.po_number as po_number,
		sum(a.production_quantity) as good_qnty,"; 
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
		$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and 
		TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23
		from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a
		left join lib_prod_floor d on a.floor_id=d.id
	    left join lib_sewing_line e on a.sewing_line=e.id
	    where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and  a.status_active=1 and a.is_deleted=0 $company_name
		$location $floor $line   $txt_date_from 
		group by a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, 
		a.sewing_line,b.buyer_name,a.item_number_id,c.po_number,d.floor_serial_no,e.sewing_line_serial 
		order by a.location,d.floor_serial_no,e.sewing_line_serial";
	}
	$sql_resqlt=sql_select($sql);
	$production_data_arr=array();
	$production_po_data_arr=array();
	foreach($sql_resqlt as $val)
	{
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
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$prod_hour]+=$val[csf($prod_hour)]; 
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
	 	$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['prod_hour23']+=$val[csf('prod_hour23')];  
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['prod_reso_allo']=$val[csf('prod_reso_allo')]; 
		
	 	if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']!="")
		{
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name'].=",".$val[csf('buyer_name')]; 
		}
	 	else
		{
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']=$val[csf('buyer_name')]; 
		}
	
	 	if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']!="")
		{
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number'].=",".$val[csf('po_number')]; 
		}
	 	else
		{
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']=$val[csf('po_number')]; 
		}
		if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']!="")
		{
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]; 
		}
		else
		{
		 $production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]; 
		}
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['quantity']+=$val[csf('good_qnty')];
	}
	
	// subcoutact data **********************************************************************************************************************
	
    if($db_type==0)
    {
		$sql_sub_contuct= "select  a.company_id, a.location_id,d.floor_serial_no,e.sewing_line_serial, a.floor_id, a.production_date, a.line_id,
		b.party_id  as buyer_name,
		a.order_id,
		c.order_no as po_number,max(c.smv) as smv,
		sum(a.production_qnty) as good_qnty,"; 
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
			$sql_sub_contuct.="sum(CASE WHEN a.hour>'$bg' and a.hour<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) 
			AS $prod_hour,";	
			}
			$first=$first+1;
   		}
   		$sql_sub_contuct.="sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and a.production_type=2 
		THEN a.production_qnty else 0 END) AS prod_hour23
		from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a 
		left join lib_prod_floor d on a.floor_id=d.id
		left join lib_sewing_line e on a.line_id=e.id
		where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 $company_name
		$subcon_location $floor $subcon_line   $txt_date_from 
		group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date,
		a.line_id,b.party_id,c.order_no,e.sewing_line_serial order by a.location_id,d.floor_serial_no,e.sewing_line_serial";
	}
	else
	{
		$sql_sub_contuct= "select  a.company_id, a.location_id, a.floor_id,d.floor_serial_no,e.sewing_line_serial, a.production_date, a.line_id,
		b.party_id  as buyer_name,
		a.order_id,
		c.order_no as po_number,max(c.smv) as smv,
		sum(a.production_qnty) as good_qnty,"; 
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
			$sql_sub_contuct.="sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')>'$bg' and TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN
			a.production_qnty else 0 END) AS $prod_hour,";	
			}
			$first++;
		}
	   	$sql_sub_contuct.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]' 
	   	and a.production_type=5 THEN a.production_qnty else 0 END) AS prod_hour23
		from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a 
		left join lib_prod_floor d on a.floor_id=d.id
		left join lib_sewing_line e on a.line_id=e.id
		where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0
		$company_name $subcon_location $floor $subcon_line   $txt_date_from 
		group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date, a.line_id,b.party_id,
		c.order_no,e.sewing_line_serial                 
		order by a.location_id, a.floor_id,e.sewing_line_serial";
		
	}
	$sub_result=sql_select($sql_sub_contuct);
	$subcon_order_smv=array();		
	foreach($sub_result as $subcon_val)
	{
		$production_po_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$subcon_val[csf('good_qnty')];
		if($production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['buyer_name']!="")
		{
		$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['buyer_name'].=",".$subcon_val[csf('buyer_name')]; 
		}
		else
		{
		$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['buyer_name']=$subcon_val[csf('buyer_name')]; 
		}
	
		if($production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number']!="")
		{
		$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number'].=",".$subcon_val[csf('po_number')]; 
		}
		else
		{
		$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number']=$subcon_val[csf('po_number')]; 
		}
	
		if($production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id']!="")
		{
		$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id'].=",".$subcon_val[csf('order_id')]; 
		}
		else
		{
		$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id'].=$subcon_val[csf('order_id')]; 
		}
		$subcon_order_smv[$subcon_val[csf('order_id')]]=$subcon_val[csf('smv')];
		$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['quantity']+=$subcon_val[csf('good_qnty')];
		
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
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$prod_hour]+=$subcon_val[csf($prod_hour)]; 
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
		$production_data_arr[$val[csf('floor_id')]][$val[csf('line_id')]]['prod_hour23']+=$val[csf('prod_hour23')];
	}
	
	
    $avable_min=0;
	$today_product=0;
    $floor_name="";   
    $floor_man_power=0;
	$floor_operator=$floor_produc_min=0;
	$floor_smv=$floor_row=$floor_helper=$floor_tgt_h=$floor_days_run=$floor_working_hour=$line_floor_production=$floor_today_product=$floor_avale_minute=0;
	$total_operator=$total_helper=$gnd_hit_rate=0;   
    $total_smv=$total_terget=$grand_total_product=$gnd_line_effi=0;
    $total_man_power=$gnd_avable_min=$gnd_product_min=0;
	$item_smv=$item_smv_total=$line_efficiency=$days_run=$total_working_hour=$gnd_total_tgt_h=$total_capacity=0;
	$j=1;
	ob_start();
	$line_number_check_arr=array();
	$smv_for_item="";
	$total_production=array();
	$floor_production=array();
    $line_floor_production=0;
    $line_total_production=0; 
	foreach($production_data_arr as $f_id=>$fname)
	{
		foreach($fname as $l_id=>$ldata)
		{
			if($i!=1)
			{
				if(!in_array($f_id, $check_arr))
				{
					if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					 $html.='<tr  bgcolor="#B6B6B6">
						<td width="40"></td>
						<td width="80"> </td>
						<td width="80"> </td>
						<td width="80"></td>
						<td width="140"></td>
						<td width="120"></td>
						<td align="right" width="60">&nbsp;</td>
						<td align="right" width="70">'.$floor_operator.'&nbsp;</td>
						<td align="right" width="50">'. $floor_helper.'&nbsp;</td>
						<td align="right" width="60">'. $floor_man_power.'&nbsp;</td>
						<td align="right" width="70">'. $floor_tgt_h.'&nbsp;</td>
						<td align="right" width="60">'. $floor_days_run.'&nbsp;</td>
						<td align="right" width="70">'.$floor_capacity.'&nbsp;</td>
						<td align="right" width="60">'. $floor_working_hour.'&nbsp;</td>
						<td align="right" width="80">'.$eff_target_floor.'&nbsp;</td>
						<td align="right" width="80">'.$line_floor_production.'&nbsp;</td>
						<td align="right" width="80">'. ($line_floor_production-$eff_target_floor).'&nbsp;</td>
						<td align="right" width="100">'. $floor_avale_minute.'&nbsp;</td>
						<td align="right" width="100">'. $floor_produc_min.'&nbsp;</td>
						<td align="right" width="60">'. number_format(($line_floor_production/$eff_target_floor)*100,2).'%&nbsp;</td>
						<td align="right" width="90">'.number_format($floor_efficency,2).' %&nbsp;</td>';
						
						for($k=$hour; $k<=$last_hour; $k++)
						{
					    $prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
					    if($start_hour_arr[$k]==$global_start_lanch)
					    {
					       $html.='<td align="right" width="50" style=" background-color:#FFFF66" >'. $floor_production[$prod_hour].'&nbsp;</td>';
						}
						else
						{
						$html.='<td align="right" width="50">'. $floor_production[$prod_hour].'&nbsp;</td>';	
						}
						}
						
			$html.='</tr>';
			$floor_html.='<tbody>';
			$floor_html.="<tr bgcolor='$bgcolor' onclick=change_color('floor_$j','$bgcolor') id=floor_$j>";
			$floor_html.='<td width="40">'.$j.'&nbsp;</td>
						<td width="80" align="center">'.$floor_name.'&nbsp; </td>
						<td width="70" align="right">'. $floor_tgt_h.'&nbsp;</td>
						<td width="70" align="right">'.$floor_capacity.'&nbsp;</td>
						<td align="right" width="60">'. $floor_man_power.'&nbsp;</td>
						<td width="70" align="right">'.$floor_operator.'&nbsp;</td>
						<td width="50" align="right">'. $floor_helper.'&nbsp;</td>
						<td align="right" width="60">'. $floor_working_hour.'&nbsp;</td>
						<td align="right" width="80">'. $eff_target_floor.'&nbsp;</td>
						<td align="right" width="80">'.$line_floor_production.'&nbsp;</td>
						<td align="right" width="80">'. ($line_floor_production-$eff_target_floor).'&nbsp;</td>
						<td align="right" width="100">'. $floor_avale_minute.'&nbsp;</td>
						<td align="right" width="100">'. $floor_produc_min.'&nbsp;</td>
						<td align="right" width="90">'. number_format(($line_floor_production/$eff_target_floor)*100,2).'%&nbsp;</td>';
						if($floor_efficency<=$txt_parcentage)
						{
						$floor_html.='<td align="right" width="90" bgcolor="red">'.number_format($floor_efficency,2).' %&nbsp;</td>';
						}
						else
						{
						$floor_html.='<td align="right" width="90" >'.number_format($floor_efficency,2).' %&nbsp;</td>';
						}
						for($k=$hour; $k<=$last_hour; $k++)
						{
							$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
							if($start_hour_arr[$k]==$global_start_lanch)
							{
							$floor_html.='<td align="right" width="50" style=" background-color:#FFFF66" >'. $floor_production[$prod_hour].'&nbsp;</td>';
							}
							else
							{
							$floor_html.='<td align="right" width="50" >'. $floor_production[$prod_hour].'&nbsp;</td>';
							}
						}	
						
					  $floor_html.='</tr>';
					  $floor_name="";
					  $floor_smv=0;
					  $floor_row=0;
					  $floor_operator=0;
					  $floor_helper=0;
					  $floor_tgt_h=0;
					  $floor_man_power=0;
					  $floor_days_run=0;
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
					  $j++;
				}
			}
			$floor_row++;
			//echo $ldata['item_number_id'];die;	
			$germents_item=array_unique(explode('****',$ldata['item_number_id']));
			$buyer_neme_all=array_unique(explode(',',$ldata['buyer_name']));
			$buyer_name="";
			foreach($buyer_neme_all as $buy)
			{
			if($buyer_name!='') $buyer_name.=',';
			$buyer_name.=$buyerArr[$buy];
			}
			$garment_itemname='';
			$item_smv="";
			$smv_for_item="";
			$produce_minit="";
			$order_no_total="";
			$efficiency_min=0;
			foreach($germents_item as $g_val)
			{
				
				$po_garment_item=explode('**',$g_val);
				if($garment_itemname!='') $garment_itemname.=',';
				$garment_itemname.=$garments_item[$po_garment_item[1]];
				if($item_smv!='') $item_smv.='/';
				$item_smv.=$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
				if($order_no_total!="") $order_no_total.=",";
				$order_no_total.=$po_garment_item[0];
				if($smv_for_item!="") $smv_for_item.="****".$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
				else
				$smv_for_item=$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];	
				$produce_minit+=$production_po_data_arr[$f_id][$l_id][$po_garment_item[0]]*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
			}
		 	$subcon_po_id=array_unique(explode(',',$ldata['order_id']));
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
				if($sewing_day!="")
				{
				$days_run= $diff=datediff("d",$sewing_day,$pr_date);
				}
				else  $days_run=0;
			}
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
				  
	//*************************************************************************************************************************************************
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
		 //***************************************************************************************************************************			  
			$production_hour=array();
			for($h=$hour;$h<=$last_hour;$h++)
			{
				 $prod_hour="prod_hour".substr($line_start_hour_arr[$h],0,2).""; 
				 $production_hour[$prod_hour]=$ldata[$prod_hour];
				 $floor_production[$prod_hour]+=$ldata[$prod_hour];
				 $total_production[$prod_hour]+=$ldata[$prod_hour];
			}
			// print_r($production_hour);
			$floor_production['prod_hour24']+=$ldata['prod_hour23'];
			$total_production['prod_hour24']+=$ldata['prod_hour23'];
			$production_hour['prod_hour24']=$ldata['prod_hour23']; 
			$line_production_hour=0;
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
			{
				$line_start=$line_number_arr[$l_id][$pr_date]['prod_start_time'];
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
					$line_production_hour+=$ldata[$line_hour];
					$line_floor_production+=$ldata[$line_hour];
					$line_total_production+=$ldata[$line_hour];
					$actual_time_hour=$start_hour_arr[$lh+1];
					}
				}
				if($start_hour_arr[$actual_time]>$lunch_start_hour) $total_eff_hour=$total_eff_hour-1;
				if($total_eff_hour>$prod_resource_array[$l_id][$pr_date]['working_hour'])
				{
				$total_eff_hour=$prod_resource_array[$l_id][$pr_date]['working_hour'];
				}
			}
			if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date)) 
			{
				for($ah=$hour;$ah<=$last_hour;$ah++)
				{
				$prod_hour="prod_hour".substr($start_hour_arr[$ah],0,2).""; 
				$line_production_hour+=$ldata[$prod_hour];
				$line_floor_production+=$ldata[$prod_hour];
				$line_total_production+=$ldata[$prod_hour];
				}
				$total_eff_hour=$prod_resource_array[$l_id][$pr_date]['working_hour'];	
			}
			if($sewing_day!="")
			{
				$days_run= $diff=datediff("d",$sewing_day,$pr_date);
			}
			else  $days_run=0;
			//******************************* line effiecency****************************************************************************['']
			$total_adjustment=0;
			$smv_adjustmet_type=$prod_resource_array[$l_id][$pr_date]['smv_adjust_type'];
			$eff_target=($prod_resource_array[$l_id][$pr_date]['terget_hour']*$total_eff_hour);
			
			if($total_eff_hour>=$prod_resource_array[$l_id][$pr_date]['working_hour'])
			{
			if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array[$l_id][$pr_date]['smv_adjust'];
			if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array[$l_id][$pr_date]['smv_adjust'])*(-1);
			}
			$efficiency_min+=$total_adjustment+($prod_resource_array[$l_id][$pr_date]['man_power'])*$total_eff_hour*60;
			$line_efficiency=(($produce_minit)*100)/$efficiency_min;
			//****************************************************************************************************************
		   	$cbo_get_upto=str_replace("'","",$cbo_get_upto);
		   	$txt_parcentage=str_replace("'","",$txt_parcentage);
		   //********************************* calclution floor total    ****************************************************$pr_date],$sewing_day
			$floor_name=$floorArr[$f_id];	
			$floor_smv+=$item_smv;
			$floor_capacity+=$prod_resource_array[$l_id][$pr_date]['capacity'];
			$floor_operator+=$prod_resource_array[$l_id][$pr_date]['operator'];
			$floor_helper+=$prod_resource_array[$l_id][$pr_date]['helper'];
			$floor_tgt_h+=$prod_resource_array[$l_id][$pr_date]['terget_hour'];	
			$floor_days_run+=$days_run;
			$floor_working_hour+=$prod_resource_array[$l_id][$pr_date]['working_hour']; 
			$eff_target_floor+=$eff_target;
			$floor_today_product+=$today_product;
			$floor_avale_minute+=$efficiency_min;
			$floor_produc_min+=$produce_minit; 
			$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
			$floor_man_power+=$prod_resource_array[$l_id][$pr_date]['man_power'];
//**************************** calclution total ***************************************************************************************
			$total_capacity+=$prod_resource_array[$l_id][$pr_date]['capacity'];
			$gnd_total_tgt_h+=$prod_resource_array[$l_id][$pr_date]['terget_hour'];	
			$total_working_hour+=$prod_resource_array[$l_id][$pr_date]['working_hour']; 
			$total_operator+=$prod_resource_array[$l_id][$pr_date]['operator'];
			$total_helper+=$prod_resource_array[$l_id][$pr_date]['helper'];
			$total_man_power+=$prod_resource_array[$l_id][$pr_date]['man_power'];
			$total_terget+=$eff_target;
			$grand_total_product+=$today_product;
			$gnd_avable_min+=$efficiency_min;
			$gnd_product_min+=$produce_minit; 
			if($line_efficiency<=$txt_parcentage) $efficiency_color="#FF0000";
			else $efficiency_color="#FFFFFF";
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$html.='<tbody>';
			$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
			$html.='<td width="40">'.$i.'&nbsp;</td>
					<td width="80">'.$floor_name.'&nbsp; </td>
					<td align="center" width="80" >'. $sewing_line.'&nbsp; </td>
					<td width="80"><p>'.$buyer_name.'&nbsp;</p></td>
					<td width="140"><p>'.$ldata['po_number'].'&nbsp;</p></td>
					<td width="120"><p>'.$garment_itemname.'&nbsp;<p/> </td>
					<td align="right" width="60"><p>'.$item_smv.'&nbsp;</p></td>
					<td align="right" width="70">'.$prod_resource_array[$l_id][$pr_date]['operator'].'&nbsp;</td>
					<td align="right" width="50">'.$prod_resource_array[$l_id][$pr_date]['helper'].'&nbsp;</td>
					<td align="right" width="60">'.$prod_resource_array[$l_id][$pr_date]['man_power'].'&nbsp;</td>
					<td align="right" width="70">'.$prod_resource_array[$l_id][$pr_date]['terget_hour'].'&nbsp;</td>
					<td align="right" width="60">'.$days_run.'&nbsp;</td>
					<td align="right" width="70">'.$prod_resource_array[$l_id][$pr_date]['capacity'].'&nbsp;</td>
					<td align="right" width="60">'.$prod_resource_array[$l_id][$pr_date]['working_hour'].'&nbsp;</td>
					<td align="right" width="80">'. $eff_target.'&nbsp;</td>
					<td width="75" align="right"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$l_id.",'tot_prod','".$smv_for_item."','".$actual_time_hour."','".$line_start."',".$txt_date.')">'.$line_production_hour.'</a>&nbsp;</td>
					<td align="right" width="80">'. ($line_production_hour-$eff_target).'&nbsp;</td>
					<td align="right" width="100">'.$efficiency_min.'&nbsp;</td>
					<td width="100" align="right"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$l_id.",'tot_prod','".$smv_for_item."','".$actual_time_hour."','".$line_start."',".$txt_date.')">'.$produce_minit.'</a>&nbsp;</td>
					<td align="right" width="60" >'. number_format(($line_production_hour/$eff_target)*100,2).' %&nbsp;</td>';
					 
					if($line_efficiency<=$txt_parcentage)
					{
					$html.='<td align="right" width="60" bgcolor="red">'.number_format($line_efficiency,2). '%&nbsp;</td>';
					}
					else
					{
					$html.='<td align="right" width="60" >'.number_format($line_efficiency,2). '%&nbsp;</td>'; 
					}
					for($k=$hour; $k<=$last_hour; $k++)
					{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
						if($start_hour_arr[$k]==$lunch_start_hour)
						{
						$html.='<td align="right" width="50"  style=" background-color:#FFFF66" >'.$production_hour[$prod_hour].'&nbsp;</td>';
						}
						else
						{
						$html.='<td align="right" width="50" >'. $production_hour[$prod_hour].'&nbsp;</td>';
						}
					}
			$html.='</tr>';
			$i++;
			$check_arr[]=$f_id;
		}
	}
			$html.='<tr  bgcolor="#B6B6B6">
					<td width="40"></td>
					<td width="80"> </td>
					<td width="80"> </td>
					<td width="80"></td>
					<td width="140"></td>
					<td width="120"></td>
					<td align="right" width="60">&nbsp;</td>
					<td align="right" width="70">'.$floor_operator.'&nbsp;</td>
					<td align="right" width="50">'. $floor_helper.'&nbsp;</td>
					<td align="right" width="60">'. $floor_man_power.'&nbsp;</td>
					<td align="right" width="70">'. $floor_tgt_h.'&nbsp;</td>
					<td align="right" width="60">'. $floor_days_run.'&nbsp;</td>
					<td align="right" width="70">&nbsp;</td>
					<td align="right" width="60">'. $floor_working_hour.'&nbsp;</td>
					<td align="right" width="80">'. $eff_target_floor.'&nbsp;</td>
					<td align="right" width="80">'.$line_floor_production.'&nbsp;</td>
					<td align="right" width="80">'. ($line_floor_production-$eff_target_floor).'&nbsp;</td>
					<td align="right" width="100">'. $floor_avale_minute.'&nbsp;</td>
					<td align="right" width="100">'. $floor_produc_min.'&nbsp;</td>
					<td align="right" width="60">'. number_format(($line_floor_production/$eff_target_floor)*100,2).'%&nbsp;</td>
					<td align="right" width="90">'.number_format($floor_efficency,2).' %&nbsp;</td>';
					for($k=$hour; $k<=$last_hour; $k++)
					{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
						if($start_hour_arr[$k]==$global_start_lanch)
						{
						$html.='<td align="right" width="50" style=" background-color:#FFFF66" >'. $floor_production[$prod_hour].'&nbsp;</td>';
						}
						else
						{
						$html.='<td align="right" width="50">'. $floor_production[$prod_hour].'&nbsp;</td>';
						}
					}
									
				   $html.='</tr> </tbody>';
				   $floor_html.="<tr bgcolor='$bgcolor' onclick=change_color('floor_$j','$bgcolor') id=floor_$j>";
				   $floor_html.='<td width="40">'.$j.'&nbsp;</td>
								<td width="80" align="center">'.$floor_name.'&nbsp; </td>
								<td width="70" align="right">'. $floor_tgt_h.'&nbsp;</td>
								<td width="70" align="right">'.$floor_capacity.'&nbsp;</td>
								<td align="right" width="60">'. $floor_man_power.'&nbsp;</td>
								<td width="70" align="right">'.$floor_operator.'&nbsp;</td>
								<td width="50" align="right">'. $floor_helper.'&nbsp;</td>
								<td align="right" width="60">'. $floor_working_hour.'&nbsp;</td>
								<td align="right" width="80">'.$eff_target_floor.'&nbsp;</td>
								<td align="right" width="80">'.$line_floor_production.'&nbsp;</td>
								<td align="right" width="80">'. ($line_floor_production-$eff_target_floor).'&nbsp;</td>
								<td align="right" width="100">'. $floor_avale_minute.'&nbsp;</td>
								<td align="right" width="100">'. $floor_produc_min.'&nbsp;</td>
								<td align="right" width="90">'. number_format(($line_floor_production/$eff_target_floor)*100,2).'%&nbsp;</td>';
								if($floor_efficency<=$txt_parcentage)
								{
								$floor_html.='<td align="right" width="90" bgcolor="red">'.number_format($floor_efficency,2).' %&nbsp;</td>';
								}
								else
								{
								$floor_html.='<td align="right" width="90" >'.number_format($floor_efficency,2).' %&nbsp;</td>';
								}
								for($k=$hour; $k<=$last_hour; $k++)
								{
									$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
									if($start_hour_arr[$k]==$global_start_lanch)
									{
									$floor_html.='<td align="right" width="50" style=" background-color:#FFFF66 ">'. $floor_production[$prod_hour].'&nbsp;</td>';
									}
									else
									{
									$floor_html.='<td align="right" width="50" >'. $floor_production[$prod_hour].'&nbsp;</td>';
									}
								}
					$floor_html.='</tr></tbody>';
					$smv_for_item="";
				?>
               
	<fieldset style="width:2450px">
       <table width="2200" cellpadding="0" cellspacing="0"> 
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
        <br />
        <table  width="600" cellpadding="0"  cellspacing="0" align="center" style="padding-left:200px">
            <tr>
                
               
                <td bgcolor="#FFFF66" height="18" width="30" ></td>
                <td> &nbsp;Lunch Hour</td>
                <td bgcolor="red" height="18" width="30"></td>
                <td> &nbsp;Efficiency % less than Standard</td>
                
            
            </tr>
        </table>
        <label> <strong>Report Sumarry:-</strong></label> 
          <table id="table_header_2" class="rpt_table" width="1940" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr height="50">
                    <th width="40">SL</th>
                    <th width="80">Floor Name</th>
                    <th width="70">Hourly Terget</th>
                    <th width="70">Capacity</th>
                    <th width="60">Total Man Power</th>
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
                   
                	<?
                	for($k=$hour+1; $k<=$last_hour+1; $k++)
					{
					?>
                    <th width="50" style="vertical-align:middle"><div class="block_div"><?  echo substr($start_hour_arr[$k],0,5);   ?></div></th>
					<?	
					}
                	?>
                </tr>
            </thead>
        </table>
        <div style="width:1960px; max-height:400px; overflow-y:scroll" id="scroll_body">
           <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
           <?  echo $floor_html; ?> 
            <tfoot>
                   <tr>
                        <th width="40"></th>
                        <th width="80">Total </th>
                        <th width="70"><? echo $gnd_total_tgt_h;   ?> </th>
                        <th width="70" align="right"><? echo $total_capacity; ?> </th>
                        <th width="60"><? echo $total_man_power; ?>&nbsp;</th>
                        <th width="70"><? echo $total_operator; ?></th>
                        <th width="50"><? echo $total_helper; ?></th>
                        <th align="right" width="60"><? echo $total_working_hour; ?>&nbsp;</th>
                        <th align="right" width="80"><? echo $total_terget; ?>&nbsp;</th>
                        <th align="right" width="80"><? echo $line_total_production; ?>&nbsp;</th>
                        <th align="right" width="80"><? echo $line_total_production-$total_terget; ?>&nbsp;</th>
                        <th align="right" width="100"><? echo $gnd_avable_min; ?>&nbsp;</th>
                        <th align="right" width="100"><? echo $gnd_product_min; ?>&nbsp;</th>
                        <th align="right" width="90"><?    echo number_format(($line_total_production/$total_terget)*100,2)."%"; ?>&nbsp;</th>
                        <th align="center" width="90"><?    echo number_format(($gnd_product_min/$gnd_avable_min)*100,2)."%"; ?>&nbsp;</th>
                        <?
						for($k=$hour; $k<=$last_hour; $k++)
						{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
						?>
						  <th width="50" ><?  echo $total_production[$prod_hour];   ?></th>
						<?	
						}
                		?>
                   </tr>
               </tfoot>
            
          </table>
        
        </div>
    </br><br/>
        <table id="table_header_1" class="rpt_table" width="2470" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr height="50">
                    <th width="40">SL</th>
                    <th width="80">Floor Name</th>
                    <th width="80">Line No</th>
                    <th width="80">Buyer</th>
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
                    <th width="80">Total Target</th>
                    <th width="80">Total Prod.</th>
                    <th width="80">Variance pcs </th>
                    <th width="100">Available Minutes</th>
                    <th width="100">Produce Minutes</th>
                    <th width="60">Target Hit rate</th>
                    <th  width="90">Line Effi %</th>
                   <?
				
                	for($k=$hour+1; $k<=$last_hour+1; $k++)
					{
					?>
                      <th width="50" style="vertical-align:middle"><div class="block_div"><?  echo substr($start_hour_arr[$k],0,5);   ?></div></th>
					<?	
					}
                ?>
                </tr>
            </thead>
        </table>
        <div style="width:2490px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
              <? echo $html;  ?>
                <tfoot>
                   <tr>
                        <th width="40"></th>
                        <th width="80"> </th>
                        <th width="80"> </th>
                        <th width="80"></th>
                        <th width="140"></th>
                        <th width="120">Total</th>
                        <th align="right" width="60"><? // echo number_format($total_smv/($i-1),2); ?>&nbsp;</th>
                        <th align="right" width="70"><? echo $total_operator; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_helper; ?>&nbsp;</th>
                        <th align="right" width="60"><? echo $total_man_power; ?>&nbsp;</th>
                        <th align="right" width="70"><?  echo $gnd_total_tgt_h; ?>&nbsp;</th>
                        <th align="right" width="60"><? //echo $prod_resource_array[$row[csf('sewing_line')]][$pr_date]['man_power']; ?>&nbsp;</th>
                        <th align="right" width="70"><? echo $total_capacity; ?></th>
                        <th align="right" width="60"><? echo $total_working_hour; ?></th>
                        <th align="right" width="80"><? echo $total_terget; ?>&nbsp;</th>
                        <th align="right" width="80"><? echo $line_total_production; ?>&nbsp;</th>
                        <th align="right" width="80"><? echo $line_total_production-$total_terget; ?>&nbsp;</th>
                        <th align="right" width="100"><? echo $gnd_avable_min; ?>&nbsp;</th>
                        <th align="right" width="100"><? echo $gnd_product_min; ?>&nbsp;</th>
                        <th align="right" width="60"><?    echo number_format(($line_total_production/$total_terget)*100,2)."%"; ?>&nbsp;</th>
                        <th align="right" width="90" ><?     echo number_format(($gnd_product_min/$gnd_avable_min)*100,2)."%";?>&nbsp;</th>
					    <?
						for($k=$hour; $k<=$last_hour; $k++)
						{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
						?>
						<th align="right" width="50"><? echo $total_production[$prod_hour]; ?>&nbsp;</th>
						<?	
						}
                        ?>
                    </tr>
                </tfoot>
            </table>
		</div>
	</fieldset>
     <br/>
         <fieldset style="width:950px">
			<label><b>No Production Line</b></label>
        	<table id="table_header_1" class="rpt_table" width="930" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="40">SL</th>
					<th width="100">Line No</th>
					<th width="100">Floor</th>
					<th width="75">Man Power</th>
					<th width="75">Operator</th>
					<th width="75">Helper</th>
                    <th width="75">Working Hour</th>
					<th width="380">Remarks</th>
					
				</thead>
			</table>
			<div style="width:950px; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                <? 
				$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
			    $sql_active_line=sql_select("select sewing_line,sum(production_quantity) as total_production from pro_garments_production_mst  where production_date=".$txt_date." and production_type=5 and status_active=1 and is_deleted=0 group by  sewing_line");
				
				
				//$actual_line_arr=array();
				foreach($sql_active_line as $inf)
				{	
				
				   if(str_replace("","",$inf[csf('sewing_line')])!="")
				   {
						if(str_replace("","",$actual_line_arr)!="") $actual_line_arr.=",";
					    $actual_line_arr.="'".$inf[csf('sewing_line')]."'";
				   }
				}
						//echo $actual_line_arr;die;
			$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$comapny_id and variable_list=23 and is_deleted=0 and status_active=1");
			$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
			if($actual_line_arr!="") $line_cond=" and a.id not in ($actual_line_arr)";
			
			 $dataArray=sql_select("select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.line_chief, b.target_per_hour, b.working_hour,d.remarks from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and b.pr_date=".$txt_date." and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 $line_cond ");
					$l=1; $location_array=array(); $floor_array=array();
					foreach( $dataArray as $row )
					{
						if ($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tbltr_<? echo $l; ?>','<? echo $bgcolor; ?>')" id="tbltr_<? echo $l; ?>">
                        	<td width="40"><? echo $l; ?></td>
                            <td width="100"><p><? echo $lineArr[$row[csf('line_number')]]; ?>&nbsp;</p></td>
                            <td width="100" align="right"><p>&nbsp;<? echo $floorArr[$row[csf('floor_id')]]; ?></p></td>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('man_power')]; ?></th>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('operator')]; ?></th>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('helper')]; ?></td>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('working_hour')]; ?></td>
                            <td width="380"><? echo $row[csf('remarks')]; ?>&nbsp;</td>
                        </tr>
                    <?
						$l++;
						
					}
					
			
				?>
					
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
	    $actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$txt_date)));
		if($db_type==0)
		{	
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
	       $sql_pop=sql_select("select  c.po_number,a.po_break_down_id,
		                sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$line_date'  and TO_CHAR(a.production_hour,'HH24:MI')<='$actual_time'  and a.production_type=5 THEN a.production_quantity else 0 END)  as good_qnty 
					 
						from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
						where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and a.company_id=".$company_id."  and a.floor_id=".$floor_id." and a.sewing_line=".$sewing_line."  and a.po_break_down_id in(".$po_id.") and a.production_date='".$prod_date."'  group by c.po_number,a.po_break_down_id order by  c.po_number ");
		 }
		 if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date))
		  {
	
	       $sql_pop=sql_select("select  c.po_number,a.po_break_down_id,
		                sum(CASE WHEN  a.production_type=5 THEN a.production_quantity else 0 END)  as good_qnty 
					 
						from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
						where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and a.company_id=".$company_id."  and a.floor_id=".$floor_id." and a.sewing_line=".$sewing_line."  and a.po_break_down_id in(".$po_id.") and a.production_date='".$prod_date."'  group by c.po_number,a.po_break_down_id order by  c.po_number ");
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
								  echo $producd_min;
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
                        <th align="right"><? echo $total_producd_min; ?>&nbsp;</th>
                    </tfoot>
                </table>
           
        </div>
	</fieldset>   
<?
exit();
}