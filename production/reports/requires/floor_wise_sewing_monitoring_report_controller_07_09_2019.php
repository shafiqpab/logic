<? 
header('Content-type:text/html; charset=utf-8');
session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_id = $_SESSION['logic_erp']['user_id'];

require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.commisions.php');
require_once('../../../includes/class4/class.trims.php');
require_once('../../../includes/class4/class.fabrics.php');
require_once('../../../includes/class4/class.yarns.php');
require_once('../../../includes/class4/class.conversions.php');
require_once('../../../includes/class4/class.others.php');
require_once('../../../includes/class4/class.emblishments.php');
require_once('../../../includes/class4/class.commercials.php');
require_once('../../../includes/class4/class.washes.php');
require_once('../../../includes/class4/cm_gmt_class.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//-------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 130, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in($data) order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/floor_wise_sewing_monitoring_report_controller', this.value+'_'+$data, 'load_drop_down_floor', 'floor_td' );",0 );  
	// load_floor($data);   	 
}


if ($action=="load_drop_down_floors") 
{ 
	$data_ex = explode("_", $data);
	$location_id = $data_ex[0];
	$company_id = $data_ex[1];
	echo create_drop_down( "cbo_floor", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id='$company_id' and location_id=$location_id and production_process=5 order by floor_name","id,floor_name", 1, "-- Select --", $selected, "",0 ); 
	exit();    	 
}

if ($action=="load_drop_down_floor") 
{ 
	$data_ex = explode("_", $data);
	$location_id = $data_ex[0];
	$company_id = $data_ex[1];
	echo create_drop_down( "cbo_floor", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id='$company_id' and location_id=$location_id and production_process=5 order by floor_name","id,floor_name", 1, "-- Select --", $selected, "",0 ); 
	exit();     	 
}

if ($action == "eval_multi_select") {
    echo "set_multiselect('cbo_line','0','0','','0');\n";
    // echo "setTimeout[($('#floor_td a').attr('onclick','disappear_list(cbo_floor,0);getCompanyId();') ,3000)];\n";
    exit();
}

if ($action=="load_drop_down_line")
{
	$explode_data = explode("_",$data);
	// print_r($explode_data);
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
			if( $explode_data[0]!=0 ) $cond = " and floor_id in($explode_data[0])";
			$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 $cond");
		}
		else
		{
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and a.location_id= $explode_data[1]";
			if( $explode_data[0]!=0 ) $cond = " and a.floor_id in($explode_data[0])";
		 if($db_type==0)	$data_format="and b.pr_date='".change_date_format($txt_date,'yyyy-mm-dd')."'";
		 if($db_type==2)	$data_format="and b.pr_date='".change_date_format($txt_date,'','',1)."'";
	
			$line_data=sql_select( "select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id $data_format and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number order by a.line_number");
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

		echo create_drop_down( "cbo_line", 130,$line_array,"", 0, "-- Select --", $selected, "",0,0 );
	}
	else
	{
		if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name= $explode_data[1]";
		if( $explode_data[0]!=0 ) $cond = " and floor_name= $explode_data[0]";

		echo create_drop_down( "cbo_line", 130, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 0, "-- Select --", $selected, "",0,0 );
	}
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
	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name");
	if($company==0) $company_name=""; else $company_name=" and b.company_name in($company)";//job_no

		$line_array=array();
		if($date_from=="" && $date_to=="")
		{
			$data_format="";
		}
		else
		{
			if($db_type==0)	$data_format=" and b.pr_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
			if($db_type==2)	$data_format=" and b.pr_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
		}
		if( $location!=0 ) $cond .= " and a.location_id in($location)";
		if( $floor_id!=0 ) $cond.= " and a.floor_id in($floor_id)";
		
		$line_sql="select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id $data_format and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number";
		// echo $line_sql;
		$line_sql_result=sql_select($line_sql);
		
		?>
            <input type='hidden' id='txt_selected_id' />
            <input type='hidden' id='txt_selected' />
            <table cellspacing="0" width="300"  border="1" rules="all" class="rpt_table" >
            	<thead>
                	<th width="30"></th>
                    <th width="270">Line Name</th>
                </thead>
            </table>
            <div style="width:300px; max-height:300px; overflow-y:scroll" id="scroll_body" >          
        		<table cellspacing="0" width="280"  border="1" rules="all" class="rpt_table" id="list_view" >
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
                        <td width="270">
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
        <table width="250">
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
 
	?>
		<style type="text/css">
            .block_div 
            { width:auto; height:auto; text-wrap:normal; vertical-align:bottom; display: block; position: !important; -webkit-transform: rotate(-90deg); -moz-transform: rotate(-90deg); }
	          
	    </style> 
	<?
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$companyArr 	= return_library_array("select id,company_name from lib_company","id","company_name"); 
	$buyerArr 		= return_library_array("select id,short_name from lib_buyer","id","short_name"); 
	$locationArr 	= return_library_array("select id,location_name from lib_location","id","location_name"); 
	$floorArr 		= return_library_array("select id,floor_name from lib_prod_floor","id","floor_name"); 
	$lineArr 		= return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 
	$prod_reso_arr 	= return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');	
	$costing_per_arr= return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no","costing_per"); 
	$tot_cost_arr 	= return_library_array("select job_no, cm_for_sipment_sche from wo_pre_cost_dtls","job_no","cm_for_sipment_sche");
	
	$comapny_id 		= str_replace("'","",$cbo_company_name);
    $today_date 		= date("Y-m-d");
	$txt_producting_day ="".str_replace("'","",$txt_date_from)."";
	//***************************************************************************************************************************
	$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
	order by sewing_line_serial"); 
	foreach($lineDataArr as $lRow)
	{
		$lineArr[$lRow[csf('id')]]		= $lRow[csf('line_name')];
		$lineSerialArr[$lRow[csf('id')]]= $lRow[csf('sewing_line_serial')];
		$lastSlNo						= $lRow[csf('sewing_line_serial')];
	}
	
	
	if($db_type==0)
	{
		$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and  a.company_id in($comapny_id) and shift_id=1 and pr_date between $txt_date_from and $txt_date_to and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");	
	}
	else
	{
		$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and   a.company_id in($comapny_id) and shift_id=1 and pr_date between $txt_date_from and $txt_date_to and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
	}
	
	if($min_shif_start=="")
	{
		echo "<p style='font-size:20px', align='center'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<p/>";die;
		
	}
		
	//==============================shift time==============================
	$start_time_arr=array();
	if($db_type==0)
	{
		$start_time_data_arr=sql_select("select company_name, shift_id, TIME_FORMAT( prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( lunch_start_time, '%H:%i' ) as lunch_start_time from variable_settings_production where company_name in($comapny_id) and shift_id=1  and variable_list=26 and status_active=1 and is_deleted=0");
		
		$group_prod_start_time=sql_select("select min(TIME_FORMAT( prod_start_time, '%H:%i' )) from variable_settings_production where company_name in($comapny_id) and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");		
	}
	else
	{
		$start_time_data_arr=sql_select("select company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time,TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($comapny_id) and  shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");	
	
		$group_prod_start_time=sql_select("select min(TO_CHAR(prod_start_time,'HH24:MI')) as prod_start_time  from variable_settings_production where company_name in($comapny_id) and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");		
	}	
	
	foreach($start_time_data_arr as $row)
	{
		$start_time_arr[$row[csf('company_name')]][$row[csf('shift_id')]]['pst'] = $row[csf('prod_start_time')];
		$start_time_arr[$row[csf('company_name')]][$row[csf('shift_id')]]['lst'] = $row[csf('lunch_start_time')];
	}

	$prod_start_hour=$group_prod_start_time[0][csf('prod_start_time')];
	if($prod_start_hour=="") $prod_start_hour="08:00";
	$start_time=explode(":",$prod_start_hour);
	$hour 		= substr($start_time[0],1,1); 
	$minutes 	= $start_time[1]; 
	$last_hour 	= 23;

	$lineWiseProd_arr 	= array(); 
	$prod_arr 			= array(); 
	$start_hour_arr 	= array();
	$start_hour 		= $prod_start_hour;
	$start_hour_arr[$hour]=$start_hour;
	for($j=$hour;$j<$last_hour;$j++)
	{
		$start_hour 			= add_time($start_hour,60);
		$start_hour_arr[$j+1] 	= substr($start_hour,0,5);
	}
	
	$start_hour_arr[$j+1]='23:59';
	// print_r($start_hour_arr);die;

	if($prod_start_hour>$min_shif_start)  $prod_start_hour=$min_shif_start;
	$actual_date=date("Y-m-d");
	$actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$txt_date_from)));
	$actual_time=substr(date("Y-m-d H:i:s",strtotime($pc_date_time)),11,2);	
	$generated_hourarr=array();
	$first_hour_time=explode(":",$min_shif_start);
	$hour_line=substr($first_hour_time[0],1,1); 
	$minutes_one=$start_time[1];
	$line_start_hour_arr[$hour_line]=$min_shif_start;
	
	for($l=$hour_line;$l<$last_hour;$l++)
	{
		$min_shif_start=add_time($min_shif_start,60);
		$line_start_hour_arr[$l+1]=substr($min_shif_start,0,5);
	}
	
	$line_start_hour_arr[$j+1]='23:59';
	// print_r($line_start_hour_arr);die();
		
	if(str_replace("'","",$cbo_company_name)=="" || str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name="and a.serving_company in(".str_replace("'","",$cbo_company_name).")";
	
	if(str_replace("'","",$cbo_location)==0) 
	{
		$subcon_location="";
		$location="";
	}
	else 
	{
		$location=" and a.location in (".str_replace("'","",$cbo_location).")";
		$subcon_location=" and a.location_id in(".str_replace("'","",$cbo_location).") ";
	}
	$cbo_floor_id=str_replace("'","",$cbo_floor);
	if($cbo_floor_id==0) $floor=""; else $floor="and a.floor_id in(".$cbo_floor_id.")";
    if(str_replace("'","",$hidden_line_id)=="")
	{ 
		$line=""; 
		$subcon_line="";
		$resource_line="";
	}
	else 
	{
		$subcon_line="and a.line_id in(".str_replace("'","",$hidden_line_id).")";
		$line="and a.sewing_line in(".str_replace("'","",$hidden_line_id).")";
		$resource_line="and a.id in(".str_replace("'","",$hidden_line_id).")";
	}
	$cbo_no_prod_type=1;
	
	if(str_replace("'","",trim($txt_date_from))=="") $prod_date=""; else $prod_date=" and a.production_date between $txt_date_from and $txt_date_to";
	
	$dataArray_sql=sql_select(" SELECT a.id,a.company_id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity, l.line_name, l.sewing_line_serial, b.line_chief, b.active_machine  from prod_resource_mst a left join lib_sewing_line l on a.line_number=cast(l.id as varchar2(100)), prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id in (".$comapny_id.") and b.pr_date between $txt_date_from and $txt_date_to and b.is_deleted=0 and c.is_deleted=0 $subcon_location $floor $resource_line order by a.company_id,a.line_marge desc, a.location_id,a.floor_id,l.sewing_line_serial");

	if(count($dataArray_sql)==0)
	{
		echo "<div style='font-size:20px;color:red;text-align:center'>Data not found!<div/>";
		die;
		
	}

	$prod_resource_array=array();
	foreach($dataArray_sql as $val)
	{
		$prod_resource_array[$val[csf('id')]]['man_power'] 		= $val[csf('man_power')];
		$prod_resource_array[$val[csf('id')]]['operator'] 		= $val[csf('operator')];
		$prod_resource_array[$val[csf('id')]]['helper'] 		= $val[csf('helper')];
		$prod_resource_array[$val[csf('id')]]['terget_hour'] 	= $val[csf('target_per_hour')];
		$prod_resource_array[$val[csf('id')]]['working_hour'] 	= $val[csf('working_hour')];
		$prod_resource_array[$val[csf('id')]]['tpd'] 			+= $val[csf('target_per_hour')]*$val[csf('working_hour')];
		$prod_resource_array[$val[csf('id')]]['day_start'] 		= $val[csf('from_date')];
		$prod_resource_array[$val[csf('id')]]['day_end'] 		= $val[csf('to_date')];
		$prod_resource_array[$val[csf('id')]]['capacity'] 		= $val[csf('capacity')];
		$prod_resource_array[$val[csf('id')]]['smv_adjust'] 	= $val[csf('smv_adjust')];
		$prod_resource_array[$val[csf('id')]]['smv_adjust_type']= $val[csf('smv_adjust_type')];
		$prod_resource_array[$val[csf('id')]]['line_number'] 	= $val[csf('line_number')];
		$prod_resource_array[$val[csf('id')]]['pr_date'] 		= $val[csf('pr_date')];
		$prod_resource_array[$val[csf('id')]]['machine'] 		= $val[csf('active_machine')];
		$prod_resource_array[$val[csf('id')]]['line_chief'] 	= $val[csf('line_chief')];
		$prod_resource_array[$val[csf('id')]]['company_id'] 	= $val[csf('company_id')];

	}
	// echo "<pre>";
	// print_r($prod_resource_array);die;
	// $company_id_arr = array();
	// foreach ($prod_resource_array as $val) 
	// {
	// 	$company_id_arr[$val['company_id']] = $val['company_id'];
	// }

	if(str_replace("'","",trim($txt_date_from))==""){$pr_date_con="";}else{$pr_date_con=" and b.pr_date between $txt_date_from and $txt_date_to";}

	if($db_type==0)
	{
		$dataArray=sql_select("SELECT a.id,b.pr_date,d.shift_id,TIME_FORMAT( d.prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( d.lunch_start_time, '%H:%i' ) as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id in (".$comapny_id.") and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 $pr_date_con"); 
	}
	else
	{		
		$dataArray=sql_select("SELECT a.id,b.pr_date,d.shift_id,TO_CHAR(d.prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR( d.lunch_start_time,'HH24:MI') as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id in (".$comapny_id.") and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 $pr_date_con");
	}
	
	$line_number_arr=array();
	foreach($dataArray as $val)
	{
		$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['shift_id'] 		= $val[csf('shift_id')];
		$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['prod_start_time'] 	= $val[csf('prod_start_time')];
		$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['lunch_start_time'] = $val[csf('lunch_start_time')];
	}
	//*************************************************************************************************************
  	if($db_type==0)
	{
		$manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
	}
	else
	{
		$manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
	}	
	
	if($db_type==0) $prod_start_cond=" min(prod_start_time) as prod_start_time";
	else if($db_type==2) $prod_start_cond="min(TO_CHAR(prod_start_time,'DD-MON-YYYY HH24:MI')) as prod_start_time";
	
	$variable_start_time_arr='';

	$prod_start_time=sql_select("select $prod_start_cond  from variable_settings_production where company_name in($comapny_id) and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");
	foreach($prod_start_time as $row)
	{
		$ex_time=explode(" ",$row[csf('prod_start_time')]);
		if($db_type==0) $variable_start_time_arr=$row[csf('prod_start_time')];
		else if($db_type==2) $variable_start_time_arr=$ex_time[1];
	}
	//echo $variable_start_time_arr;
	unset($prod_start_time);
	$current_date_time=date('d-m-Y H:i');
	$variable_date=change_date_format(str_replace("'","",$txt_date_from)).' '.$variable_start_time_arr;
	//echo $variable_date.'='.$current_date_time;
	$datediff=datediff("n",$variable_date,$current_date_time);
	
	$ex_date_time 		= explode(" ",$current_date_time);
	$current_date 		= $ex_date_time[0];
	$current_time 		= $ex_date_time[1];
	$ex_time 			= explode(":",$current_time);	
	$search_prod_date 	= change_date_format(str_replace("'","",$txt_date_from));	
	$current_eff_min 	= ($ex_time[0]*60)+$ex_time[1];
	$variable_time 		=  explode(":",$variable_start_time_arr);
	$vari_min 			= ($variable_time[0]*60)+$variable_time[1];
	$difa_time 			= explode(".",number_format(($current_eff_min-$vari_min)/60,2));//datediff("",$ctime,$variable_start_time_arr);
	$dif_time 			= number_format($datediff/60,2);
	$dif_hour_min 		= date("H", strtotime($dif_time));
	
   	$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($manufacturing_company) and variable_list=25 and   status_active=1 and is_deleted=0");
	// echo $smv_source;die();
    if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
	
    if($smv_source==3)
	{
		$sql_item="SELECT b.id, a.sam_style, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1"; $resultItem=sql_select($sql_item);
	
		foreach($resultItem as $itemData)
		{
			$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
		}
	}
	else
	{
		$sql_item="select b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost,c.smv_set from wo_po_details_master a,wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.company_name in($manufacturing_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
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
		$pr_date 		= str_replace("'","",$txt_date_from);
		$pr_date_old 	= explode("-",str_replace("'","",$txt_date_from));
		$month 			= strtoupper($pr_date_old[1]);
		$year 			= substr($pr_date_old[2],2);
		$pr_date 		= $pr_date_old[0]."-".$month."-".$year;
	}
	if($db_type==0)
	{
		$pr_date=str_replace("'","",$txt_date_from);
	}
	// echo $pr_date;die();
	$i=1; $grand_total_good=0; $grand_alter_good=0; $grand_total_reject=0;
	$html="";
	$floor_html="";
    $check_arr=array();
	// ******************************************************************************************
	if($db_type==0)
	{
		$sql="select  a.company_id, a.serving_company, a.location, a.floor_id, a.production_date, a.sewing_line,b.job_no, b.buyer_name  as buyer_name,b.style_ref_no,b.total_set_qnty as ratio, a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.unit_price,
		sum(CASE WHEN a.production_type=5 THEN production_quantity else 0 END) as good_qnty,
		sum(CASE WHEN a.production_type=4 THEN production_quantity else 0 END) as input_qnty,"; 
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
		$sql.="sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23 from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a where a.production_type in (4,5) and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.prod_reso_allo=1 $company_name $location $floor $line  $prod_date group by a.company_id,a.serving_company, a.location, a.floor_id,a.po_break_down_id, a.production_date,b.total_set_qnty, a.prod_reso_allo, a.sewing_line,b.job_no, b.buyer_name, b.style_ref_no, a.item_number_id, c.po_number, c.unit_price";
	}
	else if($db_type==2)
	{
		$sql="select a.company_id, a.serving_company, a.location, a.floor_id, a.production_date, a.sewing_line,b.job_no,b.buyer_name  as buyer_name,b.style_ref_no, b.total_set_qnty as ratio, a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.unit_price,
		sum(CASE WHEN a.production_type=5 THEN production_quantity else 0 END) as good_qnty,
		sum(CASE WHEN a.production_type=4 THEN production_quantity else 0 END) as input_qnty,"; 
		
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
				$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,";
			}
			$first++;
		}
		$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23 from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a where a.production_type in(4,5) and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.prod_reso_allo=1 $company_name $location $floor $line  $prod_date group by a.company_id,a.serving_company, a.location, a.floor_id,a.po_break_down_id, a.production_date, a.prod_reso_allo, a.sewing_line, b.job_no,b.total_set_qnty, b.buyer_name, b.style_ref_no, a.item_number_id, c.po_number, c.unit_price";
		
	}
	//echo $sql;die;
	$sql_resqlt=sql_select($sql);
	$production_data_arr=array();
	$production_po_data_arr=array();
	$production_serial_arr=array(); 
	$line_wise_prod_date_count_arr=array(); 
	$op_arr=array(); 
	$company_id_arr=array(); 
	$reso_line_ids=''; 
	$all_po_id="";
	foreach($sql_resqlt as $val)
	{
		$company_id_arr[$val[csf('company_id')]] = $val[csf('company_id')];
		$line_wise_prod_date_count_arr[$val[csf('sewing_line')]][$val[csf('production_date')]] = $val[csf('production_date')];
		$sewing_line_id=$prod_reso_arr[$val[csf('sewing_line')]];
		$reso_line_ids.=$val[csf('sewing_line')].',';
		
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
			//if(
			$prod_hour="prod_hour".substr($start_hour_arr[$h],0,2)."";
			$production_data_arr[$val[csf('sewing_line')]][$prod_hour]+=$val[csf($prod_hour)]; 
			
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
			{
				if( $h>=$line_start_hour && $h<=$actual_time)
				{
					$production_po_data_arr[$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf($prod_hour)]; 
				} 	
			}
			
			if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date)) 
			{	
				$production_po_data_arr[$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf($prod_hour)];
			}
		}
		
		if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
		{	
			if( $h>=$line_start_hour && $h<=$actual_time)
			{
				$production_po_data_arr[$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf('prod_hour23')];     
			} 	
		}
		else
		{
			$production_po_data_arr[$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf('prod_hour23')];     
		}
		
	 	$production_data_arr[$val[csf('sewing_line')]]['prod_hour23']+=$val[csf('prod_hour23')];  
		$production_data_arr[$val[csf('sewing_line')]]['quantity']+=$val[csf('good_qnty')];
		$production_data_arr_qty[$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]['quantity']+=$val[csf('good_qnty')];
		$production_data_arr[$val[csf('sewing_line')]]['input_qnty']+=$val[csf('input_qnty')];	
		
	 	if($production_data_arr[$val[csf('sewing_line')]]['buyer_name']!="")
		{
			$production_data_arr[$val[csf('sewing_line')]]['buyer_name'].=",".$val[csf('buyer_name')]; 
		}
	 	else
		{
			$production_data_arr[$val[csf('sewing_line')]]['buyer_name']=$val[csf('buyer_name')]; 
		}
	
	 	if($production_data_arr[$val[csf('sewing_line')]]['po_number']!="")
		{
			$production_data_arr[$val[csf('sewing_line')]]['po_number'].=",".$val[csf('po_number')];
			$production_data_arr[$val[csf('sewing_line')]][$val[csf('item_number_id')]]['po_id'].=",".$val[csf('po_break_down_id')];
			$production_data_arr[$val[csf('sewing_line')]]['style'].=",".$val[csf('style_ref_no')];
		}
	 	else
		{
			$production_data_arr[$val[csf('sewing_line')]]['po_number']=$val[csf('po_number')];
			$production_data_arr[$val[csf('sewing_line')]][$val[csf('item_number_id')]]['po_id']=$val[csf('po_break_down_id')]; 
			$production_data_arr[$val[csf('sewing_line')]]['style']=$val[csf('style_ref_no')]; 
		}
		$fob_rate_data_arr[$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['rate']=$val[csf('unit_price')]; 
		
		if($production_data_arr[$val[csf('sewing_line')]]['item_number_id']!="")
		{
			$production_data_arr[$val[csf('sewing_line')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('job_no')]."**".$val[csf('ratio')]; 
		}
		else
		{
			 $production_data_arr[$val[csf('sewing_line')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('job_no')]."**".$val[csf('ratio')]; 
		}
		
		$op_arr[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
		
		if($all_po_id=="") $all_po_id=$val[csf('po_break_down_id')]; else $all_po_id.=",".$val[csf('po_break_down_id')];
		// if($all_po_id=="") $all_po_id=$val[csf('po_break_down_id')]; else $all_po_id.=",".$val[csf('po_break_down_id')];
	}
	// echo "<pre>";
	// print_r($po_ids);
	// echo "</pre>";
			
	$count_po_ids=count($op_arr);
	$po_numIds=implode(",", $op_arr);
	// $po_numIds = implode(",", array_unique(explode(",",$po_numIds_chop)));
	$poIds_cond="";
	if($po_numIds!='' || $po_numIds!=0)
	{
		if($db_type==2 && $count_po_ids>1000)
		{
			$poIds_cond=" and (";
			$poIdsArr=array_chunk(explode(",",$po_numIds),990);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$poIds_cond.=" b.id  in ($ids) or ";
			}
			$poIds_cond=chop($poIds_cond,'or ');
			$poIds_cond.=")";
		}
		else
		{
			$poIds_cond=" and  b.id  in ($po_numIds)";
		}
	}
	
		
	$sql_item_rate="select b.id, c.item_number_id, c.order_quantity, c.order_total from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c where b.job_no_mst=a.job_no and b.id=c.po_break_down_id and b.job_no_mst=c.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1  $poIds_cond";
	$resultRate=sql_select($sql_item_rate);
	$item_po_array=array();
	foreach($resultRate as $row)
	{
		$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['qty']+=$row[csf('order_quantity')];
		$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['amt']+=$row[csf('order_total')];
	}	
	
	$resout_input_output=sql_select("select a.serving_company, a.location, a.floor_id, a.sewing_line, a.po_break_down_id, a.production_type, a.production_date, a.production_quantity from pro_garments_production_mst a where a.production_type in (5) and po_break_down_id in($all_po_id)  and  a.status_active=1 and a.is_deleted=0 $company_name");
	foreach($resout_input_output as $i_val)
	{		
		$input_output_po_arr[$i_val[csf('sewing_line')]][$i_val[csf('po_break_down_id')]]['output']+=$i_val[csf('production_quantity')];
	}
			
	// subcoutact data ****************************************
	
    if($db_type==0)
    {
		$sql_sub_contuct= "select  a.company_id, a.location_id, a.floor_id, a.production_date, a.line_id,b.party_id  as buyer_name,a.order_id,c.order_no as po_number,c.cust_style_ref,max(c.smv) as smv,
		sum(CASE WHEN  a.production_type=2 THEN a.production_qnty else 0 END) as good_qnty,
		sum(CASE WHEN  a.production_type=7 THEN a.production_qnty else 0 END) as input_qnty,";  
		
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
   		$sql_sub_contuct.="sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a  where a.production_type in(2,7) and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.prod_reso_allo=1 and a.company_id in (".$comapny_id.") $subcon_location $floor $subcon_line   $prod_date group by a.company_id, a.location_id, a.floor_id,a.order_id, a.production_date,a.prod_reso_allo,a.line_id,b.party_id,c.order_no,c.cust_style_ref order by a.location_id";
	}
	else
	{
		$sql_sub_contuct= "select  a.company_id, a.location_id, a.floor_id, a.production_date, a.line_id,b.party_id  as buyer_name,a.order_id,c.order_no as po_number,c.cust_style_ref,max(c.smv) as smv,
		sum(CASE WHEN  a.production_type=2 THEN a.production_qnty else 0 END) as good_qnty,
		sum(CASE WHEN a.production_type=7 THEN a.production_qnty else 0 END) as input_qnty,"; 
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
		
	   	$sql_sub_contuct.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]'	and a.production_type=5 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a  where a.production_type in (2,7) and a.prod_reso_allo=1 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id in(".$comapny_id.") $subcon_location $floor $subcon_line   $prod_date group by a.company_id, a.location_id, a.floor_id,a.order_id, a.production_date, a.line_id,b.party_id,c.order_no,c.cust_style_ref ";		
	}
	
	//echo $sql_sub_contuct;die;
	$sub_result=sql_select($sql_sub_contuct);
	$subcon_order_smv=array();		
	foreach($sub_result as $subcon_val)
	{	
		$line_wise_prod_date_count_arr[$val[csf('line_id')]][$val[csf('production_date')]] = $val[csf('production_date')];
		$sewing_line_id=$prod_reso_arr[$subcon_val[csf('sewing_line')]];
		
		$production_po_data_arr[$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$subcon_val[csf('good_qnty')];
		$production_data_arr[$subcon_val[csf('line_id')]]['input_qnty']+=$subcon_val[csf('input_qnty')];
		
		if($production_data_arr[$subcon_val[csf('line_id')]]['buyer_name']!="")
		{
			$production_data_arr[$subcon_val[csf('line_id')]]['buyer_name'].=",".$subcon_val[csf('buyer_name')]; 
		}
		else
		{
			$production_data_arr[$subcon_val[csf('line_id')]]['buyer_name']=$subcon_val[csf('buyer_name')]; 
		}
	
		if($production_data_arr[$subcon_val[csf('line_id')]]['po_number']!="")
		{
			$production_data_arr[$subcon_val[csf('line_id')]]['po_number'].=",".$subcon_val[csf('po_number')];
			$production_data_arr[$subcon_val[csf('line_id')]]['style'].=",".$subcon_val[csf('cust_style_ref')];  
		}
		else
		{
			$production_data_arr[$subcon_val[csf('line_id')]]['po_number']=$subcon_val[csf('po_number')]; 
			$production_data_arr[$subcon_val[csf('line_id')]]['style']=$subcon_val[csf('cust_style_ref')]; 
		}
	
		if($production_data_arr[$subcon_val[csf('line_id')]]['order_id']!="")
		{
			$production_data_arr[$subcon_val[csf('line_id')]]['order_id'].=",".$subcon_val[csf('order_id')]; 
		}
		else
		{
			$production_data_arr[$subcon_val[csf('line_id')]]['order_id'].=$subcon_val[csf('order_id')]; 
		}
		$subcon_order_smv[$subcon_val[csf('order_id')]]=$subcon_val[csf('smv')];
		$production_data_arr[$subcon_val[csf('line_id')]]['quantity']+=$subcon_val[csf('good_qnty')];
		
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
			$production_data_arr[$subcon_val[csf('line_id')]][$prod_hour]+=$subcon_val[csf($prod_hour)]; 
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
			{
				if( $h>=$line_start_hour && $h<=$actual_time)
				{
				 	$production_po_data_arr[$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf($prod_hour)];	                 
				} 
			}
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
			{
				$production_po_data_arr[$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf($prod_hour)];	            
			}
		 }
		if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
		{	
			if( $h>=$line_start_hour && $h<=$actual_time)
			{
				$production_po_data_arr[$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf('prod_hour23')];
			} 	
		}
		else
		{
			$production_po_data_arr[$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf('prod_hour23')];
		}
		$production_data_arr[$val[csf('line_id')]]['prod_hour23']+=$val[csf('prod_hour23')];
	}
	
	// ======================= getting production date wise line ===========================
	$line_count_arr = array();
	foreach ($line_wise_prod_date_count_arr as $line_key => $date_data) 
	{
		foreach ($date_data as $date_key => $val) 
		{
			$line_count_arr[$line_key]++;
		}		
	}
	// echo "<pre>";
	// print_r($company_id_arr);
	// echo "</pre>";
	// echo $po_numIds;
	$cm_gmt_cost_dzn_arr 	= array();
	$cm_gmt_cost_dzn_arr_new= array();		
	$comapny_ids  			=  implode(",", $company_id_arr);
	$new_arr 				= array_unique(explode(",", $po_numIds));
	$chnk_arr 				= array_chunk($new_arr,50);
	// print_r($new_arr);
	foreach($chnk_arr as $vals )
	{
		$p_ids=implode(",", $vals);
		// $cm_gmt_cost_dzn_arr=fnc_po_wise_cm_gmt_class($comapny_id,$po_numIds); 
		$cm_gmt_cost_dzn_arr=fnc_po_wise_cm_gmt_class($comapny_ids,$po_numIds); 
		foreach($cm_gmt_cost_dzn_arr as $po_id=>$vv)
		{
		 	$cm_gmt_cost_dzn_arr_new[$po_id]["dzn"]=$vv["dzn"] ;
		 	$cm_gmt_cost_dzn_arr_new[$po_id]["pcs"]=$vv["pcs"] ;
		}
	}
	// echo "<pre>";
	// print_r($cm_gmt_cost_dzn_arr_new);
	// echo "</pre>";
	// die();
	
    $avable_min=0;
	$today_product=0;
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
    $gnd_total_fob_val=0; 
    $gnd_final_total_fob_val=0;
	
	// echo "<pre>";
	// print_r($prod_resource_array);die;
	if ($type == 0) 
	{
		$floor_html.='<tbody>';
		foreach($prod_resource_array as $resource_id=>$resource_data)
		{
			$global_start_lanch=$start_time_arr[$resource_data['company_id']][1]['lst'];
			
			$germents_item=array_unique(explode('****',$production_data_arr[$resource_id]['item_number_id']));
					
			$garment_itemname 	= '';
			$item_smv 			= "";
			$item_ids 			= '';
			$smv_for_item 		= "";
			$produce_minit 		= "";
			$order_no_total 	= "";
			$efficiency_min 	= 0;
			$tot_po_qty 		= 0;
			$fob_val 			= 0;
			$days_run 			= 0;
			$total_input 		= 0; 
			$total_output 		= 0; 
			$min_input_date 	= ''; 
			$total_wip 			= 0; 
			$line_cm_value 		= 0;
			$today_input 		= 0; 
			$total_smv_achive 	= 0;
			$line_cm_in_pcs		= 0;
			$line_cm_in_dzn		= 0;
			$prod_qty  			= 0;
			foreach($germents_item as $g_val)
			{				
				$po_garment_item=explode('**',$g_val);
				if($garment_itemname!='') $garment_itemname.=',';
				$garment_itemname.=$garments_item[$po_garment_item[1]];
				// echo "<br>item==".$po_garment_item[1].", po==".$po_garment_item[0];
				if($item_ids=='') $item_ids=$po_garment_item[1];else $item_ids.=",".$po_garment_item[1];
				
				$total_input+=$input_output_po_arr[$resource_id][$po_garment_item[0]]['input'];
				$total_output+=$input_output_po_arr[$resource_id][$po_garment_item[0]]['output'];
				if($input_output_po_arr[$resource_id][$po_garment_item[0]]['input_date']!='')
				{
					if($min_input_date!='')
					{
						if(strtotime($input_output_po_arr[$resource_id][$po_garment_item[0]]['input_date'])<strtotime($min_input_date))
						{
							$min_input_date=$input_output_po_arr[$resource_id][$po_garment_item[0]]['input_date'];
						}
					}
					else
					{
						$min_input_date=$input_output_po_arr[$resource_id][$po_garment_item[0]]['input_date'];
					}
				}			

				$tot_po_qty+=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];
				$tot_po_amt+=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt'];
				if($item_smv!='') $item_smv.='/';
				//echo $po_garment_item[0].'='.$po_garment_item[1];
				$item_smv.=$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];			
				
				$total_smv_achive+=$input_output_po_arr[$po_garment_item[0]]['output']*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
				
				if($order_no_total!="") $order_no_total.=",";
				$order_no_total.=$po_garment_item[0];
				if($smv_for_item!="") $smv_for_item.="****".$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
				else
				$smv_for_item=$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];	
				
				$produce_minit+=$production_po_data_arr[$resource_id][$po_garment_item[0]]*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
				
				$fob_rate=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt']/$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];
				
				$prod_qty=$production_data_arr_qty[$resource_id][$po_garment_item[0]][$po_garment_item[1]]['quantity'];
				// echo $po_garment_item[0]."==".$prod_qty."<br>";
				$dzn_qnty=0; 
				$cm_value=0;
				if($costing_per_arr[$po_garment_item[2]]==1) $dzn_qnty=12;
				else if($costing_per_arr[$po_garment_item[2]]==3) $dzn_qnty=12*2;
				else if($costing_per_arr[$po_garment_item[2]]==4) $dzn_qnty=12*3;
				else if($costing_per_arr[$po_garment_item[2]]==5) $dzn_qnty=12*4;
				else $dzn_qnty=1;
				
				$dzn_qnty=$dzn_qnty*$po_garment_item[3];
				// echo $po_garment_item[3]."<br>";
				// $cm_value=($tot_cost_arr[$po_garment_item[2]]/$dzn_qnty)*$prod_qty;
				// if(is_nan($cm_value)){ $cm_value=0; }
				
				// $line_cm_value+=$cm_value;
				if(is_nan($fob_rate)){ $fob_rate=0; }
				$fob_val+=$prod_qty*$fob_rate;
				// echo $po_garment_item[0]."=".$cm_gmt_cost_dzn_arr_new[$po_garment_item[0]]['dzn']."<br>";
				$cm_gmt_cost_dzn=$cm_gmt_cost_dzn_arr_new[$po_garment_item[0]]['dzn'];
				// $cm_per_pcs=$cm_gmt_cost_dzn/$dzn_qnty;
				$cm_per_pcs=$cm_gmt_cost_dzn/12;
				$line_cm_in_pcs += ($prod_qty * $cm_per_pcs);
			}		
		
			$today_input+=$production_data_arr[$resource_id]['input_qnty'];
			//echo $today_input;die;
			//$fob_rate=$tot_po_amt/$tot_po_qty;
			$subcon_po_id=array_unique(explode(',',$production_data_arr[$resource_id]['order_id']));
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
				$produce_minit+=$production_po_data_arr[$resource_id][$sub_val]*$subcon_order_smv[$sub_val];
				if($subcon_order_id!="") $subcon_order_id.=",";
				$subcon_order_id.=$sub_val;
			}			
			
			if($min_input_date!="")
			{
				$days_run=datediff("d",$min_input_date,$pr_date);
			}
			else  $days_run=0;
			
			$type_line=$production_data_arr[$resource_id]['type_line'];
			$prod_reso_allo=$production_data_arr[$resource_id]['prod_reso_allo'];
		
			$sewing_line='';
			$line_number=explode(",",$resource_data['line_number']);
			foreach($line_number as $val)
			{
				if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
			}

			//*************************************************************************************************************
			$lunch_start="";
			$lunch_start=$line_number_arr[$resource_id][$pr_date]['lunch_start_time']; 
			$lunch_hour=$start_time_arr[$company_id][1]['lst']; 
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
				 $production_hour[$prod_hour]=$production_data_arr[$resource_id][$prod_hour];
				 $company_production[$prod_hour]+=$production_data_arr[$resource_id][$prod_hour];
				 $floor_production[$prod_hour]+=$production_data_arr[$resource_id][$prod_hour];
				 $total_production[$prod_hour]+=$production_data_arr[$resource_id][$prod_hour];
			}			
			
			// print_r($production_hour);
			$floor_production['prod_hour24']+=$production_data_arr[$resource_id]['prod_hour23'];
			$total_production['prod_hour24']+=$production_data_arr[$resource_id]['prod_hour23'];
			$production_hour['prod_hour24']=$production_data_arr[$resource_id]['prod_hour23'];
			$company_production['prod_hour24']=$production_data_arr[$resource_id]['prod_hour23'];  
			$line_production_hour=0;
			if(str_replace("'","",$actual_production_date)>str_replace("'","",$actual_date)) 
			{				
				$line_start=$line_number_arr[$resource_id][$pr_date]['prod_start_time'];
				
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
						$line_production_hour+=$production_data_arr[$resource_id][$line_hour];
						$line_floor_production+=$production_data_arr[$resource_id][$line_hour];
						$line_total_production+=$production_data_arr[$resource_id][$line_hour];
						$actual_time_hour=$start_hour_arr[$lh+1];
					}
				}
				//echo $total_eff_hour.'aaaa';
				if($start_hour_arr[$actual_time]>$lunch_start_hour) $total_eff_hour=$total_eff_hour-1;
				
				if($total_eff_hour>$production_data_arr[$resource_id]['working_hour'])
				{
					 $total_eff_hour=$production_data_arr[$resource_id]['working_hour'];
				}
			}
			
			if(str_replace("'","",$actual_production_date)<=str_replace("'","",$actual_date)) 
			{
				for($ah=$hour;$ah<=$last_hour;$ah++)
				{
					$prod_hour="prod_hour".substr($start_hour_arr[$ah],0,2).""; 
					$line_production_hour+=$production_data_arr[$resource_id][$prod_hour];
					//echo $production_data_arr[$ldata][$prod_hour];
					$line_floor_production+=$production_data_arr[$resource_id][$prod_hour];
					$line_total_production+=$production_data_arr[$resource_id][$prod_hour];
				}
				
				$total_eff_hour=$resource_data['working_hour'];	
			}

			if($cbo_no_prod_type==1 && $line_production_hour>0)
			{
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
				
				$smv_adjustmet_type=$resource_data['smv_adjust_type'];
				$eff_target=($resource_data['terget_hour']*$total_eff_hour);				
			
				if($total_eff_hour>=$resource_data['working_hour'])
				{
					if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$resource_data['smv_adjust'];
					if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($resource_data['smv_adjust'])*(-1);
				}
				
				$efficiency_min+=$total_adjustment+($resource_data['man_power'])*$cla_cur_time*60;
				$line_efficiency=($produce_minit*100)/$efficiency_min;

				// echo $produce_minit;die();
				// echo $efficiency_min;die();
				//****************************************************************************************************************
												
				$terget_hour=$resource_data['terget_hour'];	
				$capacity=$resource_data['capacity'];
				$working_hour=$resource_data['working_hour'];
				
				$floor_capacity+=$resource_data['capacity'];
				$floor_man_power+=$resource_data['man_power'];
				$floor_operator+=$resource_data['operator'];
				$floor_helper+=$resource_data['helper'];
				$floor_tgt_h+=$resource_data['terget_hour'];	
				$floor_working_hour+=$resource_data['working_hour']; 
				$eff_target_floor+=$eff_target;
				$floor_today_product+=$line_production_hour;
				$floor_avale_minute+=$efficiency_min;
				$floor_produc_min+=$produce_minit; 
				$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
				
				$floor_cm_value+=$line_cm_in_pcs;
				// $floor_cm_value+=$line_cm_in_pcs;
				$floor_fob_value+=$fob_val;
				$floor_total_input+=$total_input;
				$floor_total_output+=$total_output;
				$floor_today_input+=$today_input;
				$floor_total_wip+=($total_input-$total_output);
				
				$total_operator+=$resource_data['operator'];
				$total_man_power+=$resource_data['man_power'];
				$total_helper+=$resource_data['helper'];
				$total_capacity+=$resource_data['capacity'];
				$total_working_hour+=$resource_data['working_hour']; 
				$gnd_total_tgt_h+=$resource_data['terget_hour'];
				$grand_total_terget+=$resource_data['tpd'];// $target_hour
				$grand_total_product+=$line_production_hour;
				$gnd_avable_min+=$efficiency_min;
				$gnd_product_min+=$produce_minit;
				$gnd_total_fob_val+=$fob_val;
				$gnd_final_total_fob_val+=$fob_val; 
				
				$grand_today_input+=$today_input;
				$grand_total_input+=$total_input;
				$grand_total_output+=$total_output;
				$grand_total_wip+=($total_input-$total_output);
				$grand_cm_value+=$line_cm_value;
				
				
				
				$floor_total_smv_achive+=$total_smv_achive;
				$company_total_smv_achive+=$total_smv_achive;
				$grand_total_smv_achive+=$total_smv_achive;	
				
				$floor_total_machine+=$resource_data['machine'];
				$company_total_machine+=$resource_data['machine'];
				$grand_total_machine+=$resource_data['machine'];
					
				
				$po_id=rtrim($production_data_arr[$resource_id]['po_id'],',');
				$po_id=array_unique(explode(",",$po_id));
				$style=rtrim($production_data_arr[$resource_id]['style']);
				$style=implode(",",array_unique(explode(",",$style)));

				
				$floor_smv+=$item_smv;

				$floor_days_run+=$days_run;

				$po_id=$production_data_arr[$resource_id]['po_id'];//$item_ids//$subcon_order_id
				$styles=explode(",",$style);
				
				$as_on_current_hour_target=0; $as_on_current_hour_variance=0;
				$as_on_current_hour_target=$terget_hour*$cla_cur_time;
				$as_on_current_hour_variance=$line_production_hour-$as_on_current_hour_target;			 
				
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";		
				
				$i++;
			}	
			
			if($cbo_no_prod_type==1 && $line_floor_production>0)
			{
				$achivement = ($line_floor_production/$resource_data['tpd'])*100;
				if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$floor_html.="<tr bgcolor='$bgcolor' onclick=change_color('floor_$j','$bgcolor') id=floor_$j>";
				$floor_html.='<td style="word-wrap:break-word; word-break: break-all;" width="90">'.$sewing_line.'&nbsp;</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="100">'. $resource_data['tpd'].'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="100">'.$line_floor_production.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="100">'. ($resource_data['tpd'] - $line_floor_production).'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="90">'. number_format($achivement,2).'%</td>								
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="100" title="prod min='.$produce_minit.',available min='.$efficiency_min.',prod day='.$line_count_arr[$resource_id].'" >'.number_format($line_efficiency/$line_count_arr[$resource_id],2).' %</td>						
							
							<td width="100" align="right">'. number_format($line_cm_in_pcs,2).'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="100">'. number_format($fob_val,2).'</td>';								
					
				  $floor_html.='</tr>';			  
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
				  $floor_total_machine=0;
				  $floor_today_input=0;
				  $floor_total_input=0;
				  $floor_total_output=0;
				 $floor_total_wip=0;
				 // $floor_cm_value=0;
				$j++;	
			}		
		}							
		
		$floor_html.='</tbody>';
		
		?>
	               
		<fieldset style="width:810px;margin: 0 auto">
	       <table width="790" cellspacing="0" style="margin-bottom: 10px;"> 
	            <tr style="border:none;">               
	                <td colspan="8" align="center" style="border:none; font-size:24px;font-weight: bold;" width="100%">                                	
	                    <? 
	                    //echo $companyArr[str_replace("'","",$cbo_company_name)]; 
	                    $company_name_arr = explode(",", $comapny_id);
	                    $com_name = "";
	                    foreach ($company_name_arr as $val) 
	                    {
	                    	$com_name .= ($com_name == "") ?  $companyArr[$val] : ", ".$companyArr[$val] ;
	                    }
	                    echo $com_name;
	                    ?>                 
	                </td>
	            </tr>
	            <tr style="border:none;">
	                <td colspan="8" align="center" style="border:none; font-size:16px;font-weight: bold;" width="100%"> 
	                   Floor Wise Sewing Monitoring Report                   
	                </td>
	            </tr> 
	            <tr style="border:none;">
	                <td colspan="8" align="center" style="border:none; font-size:16px;font-weight: bold;" width="100%"> Date :                       
	                    <? echo change_date_format(str_replace("'", "", $txt_date_from)); ?> To
	                    <? echo change_date_format(str_replace("'", "", $txt_date_to)); ?>                        
	                </td>
	            </tr>  
	        </table>
	        <br />
	        
	      	<table width="790" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
	            <thead> 	 	 	 	 	 	
	                <tr>  
	                    <th width="90">Line No</th>  
	                    <th width="100">Target</th>
	                    <th width="100">Production</th>
	                    <th width="100">Variation</th>
	                    <th width="100">Ach.%</th>
	                    <th width="100">Avg Effi.%</th>
	                    <th width="100">Sewing CM Value </th>
	                    <th width="100">Sewing FOB Value </th>
	                </tr>
	            </thead>
	        </table>
	        <div style="width:810px; max-height:400px; overflow-y:scroll" id="scroll_body">
	           <table class="rpt_table" width="790" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
	           <?  echo $floor_html; ?> 
	            <tfoot>
	                   <tr>
	                        <th width="90">Total </th>
	                        <th align="right" width="100"><? echo $grand_total_terget; ?></th>
	                        <th align="right" width="100"><? echo $line_total_production; ?></th>
	                        <th align="right" width="100"><? echo $grand_total_terget - $line_total_production; ?></th>
	                        <th align="right" width="100"></th>
	                        <th align="right" width="100"></th>
	                        <th align="right" width="100"><? echo number_format($floor_cm_value,2); ?></th>
	                        <th align="right" width="100"><? echo number_format($floor_fob_value,2); ?></th>      
	                   </tr>
	               </tfoot>

	          </table>
	        
	        </div>
		</fieldset>  
	   
		<?    
	} 

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