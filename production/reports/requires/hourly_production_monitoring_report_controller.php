<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//-------
if($action=="print_button_variable_setting")
{
    $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name in($data) and module_id=7 and report_id=159 and is_deleted=0 and status_active=1","format_id","format_id");
    echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
    exit();
}

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","");
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/hourly_production_monitoring_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/hourly_production_monitoring_report_controller' );",0 );
	exit();
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_id", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=5 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );
	exit();
}

if ($action == "eval_multi_select") {
    echo "set_multiselect('cbo_floor_id','0','0','','0');\n";
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
	//$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$comapny_id=str_replace("'","",$cbo_company_id);
    $today_date=date("Y-m-d");
	$txt_producting_day="".str_replace("'","",$txt_date)."";
	//***************************************************************************************************************************
	$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
	order by sewing_line_serial");
	foreach($lineDataArr as $lRow)
	{
		$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
		$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
		$lastSlNo=$lRow[csf('sewing_line_serial')];
	}


	if($db_type==0)
	{
		$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$comapny_id and shift_id=1 and pr_date=$txt_date","line_start_time");
	}
	else
	{
		$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$comapny_id and shift_id=1 and pr_date=$txt_date","line_start_time");
	}

	if($min_shif_start=="")
	{
		echo "<p style='font-size:20px', align='center'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<p/>";die;

	}


	//================shift time==================================================================
	$start_time_arr=array();
	if($db_type==0)
	{
		$start_time_data_arr=sql_select("select company_name, shift_id, TIME_FORMAT( prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( lunch_start_time, '%H:%i' ) as lunch_start_time from variable_settings_production where company_name in($comapny_id) and shift_id=1  and variable_list=26 and status_active=1 and is_deleted=0");
	}
	else
	{
		$start_time_data_arr=sql_select("select company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time,TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($comapny_id) and  shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");
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
	$hour=(int)substr($start_time[0],1,1); $minutes=$start_time[1]; $last_hour=23;
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
	$actual_time=(int)substr(date("Y-m-d H:i:s",strtotime($pc_date_time)),11,2);
	$generated_hourarr=array();
	$first_hour_time=explode(":",$min_shif_start);
	$hour_line=(int)substr($first_hour_time[0],1,1); $minutes_one=$start_time[1];
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
	$file_no=str_replace("'","",$txt_file_no);
	$cbo_no_prod_type=str_replace("'","",$cbo_no_prod_type);
	$ref_no=str_replace("'","",$txt_ref_no);
	if($file_no!="") $file_cond="and c.file_no=$file_no";else $file_cond="";
	if($ref_no!="") $ref_cond="and c.grouping='$ref_no'";else $ref_cond="";
	//echo $file_cond;

	if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date=$txt_date";
	// echo $txt_date_from; die;
	if($prod_reso_allo[0]==1)
	{
		$prod_resource_array=array();

		$dataArray_sql=sql_select("select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id=$comapny_id and b.pr_date=$txt_date and b.is_deleted=0 and c.is_deleted=0");
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

			//$prod_resource_array[$val[csf('floor_id')]]['terget_hour']=$val[csf('target_per_hour')];
		}
		//var_dump($prod_resource_array);

		if($db_type==0)
		{
			$dataArray=sql_select("select a.id,b.pr_date,d.shift_id,TIME_FORMAT( d.prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( d.lunch_start_time, '%H:%i' ) as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and shift_id=1 and b.is_deleted=0 and d.is_deleted=0");
		}
		else
		{
			$dataArray=sql_select("select a.id,b.pr_date,d.shift_id,TO_CHAR(d.prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR( d.lunch_start_time,'HH24:MI') as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and shift_id=1 and b.is_deleted=0 and d.is_deleted=0");
		}

		$line_number_arr=array();
		foreach($dataArray as $val)
		{
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['shift_id']=$val[csf('shift_id')];
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['prod_start_time']=$val[csf('prod_start_time')];
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['lunch_start_time']=$val[csf('lunch_start_time')];
		}
	}
 //********************************************************************************************************
  	if($db_type==0)
	{
		$manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
	}
	else
	{
		$manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
	}

   	$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($manufacturing_company) and variable_list=25 and   status_active=1 and is_deleted=0");
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
		$sql_item="select b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a,wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
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
	 //print_r($item_smv_array);


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

	//For Summary Report New Add No Prodcut
	if($cbo_no_prod_type==1)
	{
	//$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
			    $sql_active_line=sql_select("select sewing_line,sum(production_quantity) as total_production from pro_garments_production_mst  where production_date=".$txt_date." and production_type=5 and status_active=1 and is_deleted=0 and prod_reso_allo=1 group by  sewing_line");


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
			//$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$comapny_id and variable_list=23 and is_deleted=0 and status_active=1");
			//$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
			if($actual_line_arr!="") $line_cond=" and a.id not in ($actual_line_arr)";
			if($db_type==0) $remarks_cond="group_concat(d.remarks)";
			else if($db_type==2) $remarks_cond="LISTAGG(cast(d.remarks as varchar2(4000)), ',') WITHIN GROUP (ORDER BY d.remarks)";

			 $dataArray_sum=sql_select("select a.id, a.floor_id, a.line_number, b.man_power, b.operator, b.helper, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type, $remarks_cond as remarks from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and b.pr_date=".$txt_date." and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 $line_cond group by a.id, a.floor_id, a.line_number, b.man_power, b.operator, b.helper, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type order by a.floor_id");
			 $no_prod_line_arr=array();
			 foreach( $dataArray_sum as $row)
			 {
				 $no_prod_line_arr[$row[csf('floor_id')]]['man_power']+=$row[csf('man_power')];
				 $no_prod_line_arr[$row[csf('floor_id')]]['operator']+=$row[csf('operator')];
				 $no_prod_line_arr[$row[csf('floor_id')]]['helper']+=$row[csf('helper')];
				 $no_prod_line_arr[$row[csf('floor_id')]]['working_hour']+=$row[csf('working_hour')];
				 $no_prod_line_arr[$row[csf('floor_id')]]['hour']+=$row[csf('target_per_hour')];
				 $no_prod_line_arr[$row[csf('floor_id')]]['total_line_hour']+=$row[csf('man_power')]*$row[csf('working_hour')];
				 $no_prod_line_arr[$row[csf('floor_id')]]['smv_adjust']+=$row[csf('smv_adjust')];
				 $no_prod_line_arr[$row[csf('floor_id')]]['smv_adjust_type']=$row[csf('smv_adjust_type')];
			 }
			 $dataArray_sql_cap=sql_select("select  a.floor_id, c.capacity, $remarks_cond as remarks from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id  and a.company_id=$comapny_id and b.pr_date=".$txt_date."  and a.is_deleted=0 and b.is_deleted=0 $line_cond group by a.id, a.floor_id, a.line_number, b.man_power, b.operator, b.helper, b.working_hour,b.target_per_hour order by a.floor_id");

			 $prod_resource_array_summary=array();
			 foreach( $dataArray_sql_cap as $row)
			 {

				 $prod_resource_array_summary[$row[csf('floor_id')]]['capacity']+=$row[csf('capacity')];
			 }

	}

			 //End



	$i=1; $grand_total_good=0; $grand_alter_good=0; $grand_total_reject=0;
	$html="";
	$floor_html="";
    $check_arr=array();

	if($db_type==0)
	{
		$sql="select  a.company_id, a.location, a.floor_id,d.floor_serial_no,e.sewing_line_serial, a.prod_reso_allo, a.production_date, a.sewing_line,
	b.buyer_name  as buyer_name,a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.file_no,c.grouping as ref,sum(a.production_quantity) as good_qnty,";
		$first=1;
		for($h=$hour;$h<$last_hour;$h++)
		{
			$bg=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="prod_hour".substr($bg,0,2);
			if($first==1)
			{
				$sql.="sum(CASE WHEN a.production_hour<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,";
			}
			else
			{
				$sql.="sum(CASE WHEN a.production_hour>'$bg' and  a.production_hour<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,";
			}
			$first=$first+1;
		}
		$sql.="sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23 from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a left join lib_prod_floor d on a.floor_id=d.id left join lib_sewing_line e on a.sewing_line=e.id  where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no  and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 $company_name $location $floor $line $buyer_id_cond  $txt_date_from $file_cond $ref_cond group by a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,e.sewing_line_serial,b.buyer_name,a.item_number_id,c.po_number,c.file_no,c.grouping order by a.location, a.floor_id,e.sewing_line_serial,a.prod_reso_allo";
	}
	else if($db_type==2)
	{
		$sql="select  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name  as buyer_name, a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.file_no,c.grouping as ref,sum(a.production_quantity) as good_qnty,";
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
		$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23 from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 $company_name $location $floor $line $buyer_id_cond  $txt_date_from $file_cond $ref_cond group by a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name,a.item_number_id,c.po_number,c.file_no,c.grouping order by a.location,a.floor_id,a.sewing_line";
	}
		 //echo $sql;die;
	$sql_resqlt=sql_select($sql);
	$production_data_arr=array();
	$production_po_data_arr=array();
	$production_serial_arr=array();
	foreach($sql_resqlt as $val)
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
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['file'].=",".$val[csf('file_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['ref'].=",".$val[csf('ref')];
		}
	 	else
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']=$val[csf('po_number')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['file']=$val[csf('file_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['ref']=$val[csf('ref')];
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

	// subcoutact data *************************************************************************************

    if($db_type==0)
    {
		$sql_sub_contuct= "select  a.company_id, a.location_id,d.floor_serial_no,e.sewing_line_serial,a.prod_reso_allo, a.floor_id, a.production_date, a.line_id,b.party_id  as buyer_name,a.order_id,c.order_no as po_number,max(c.smv) as smv,sum(a.production_qnty) as good_qnty,";

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
   		$sql_sub_contuct.="sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.idleft join lib_sewing_line e on a.line_id=e.id where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $company_name $subcon_location $floor $subcon_line   $txt_date_from group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date,a.prod_reso_allo,a.line_id,b.party_id,c.order_no,e.sewing_line_serial order by a.location_id,d.floor_serial_no,e.sewing_line_serial,a.prod_reso_allo";
	}
	else
	{
		$sql_sub_contuct= "select  a.company_id, a.location_id, a.floor_id,d.floor_serial_no,e.sewing_line_serial, a.production_date, a.line_id,b.party_id  as buyer_name,a.prod_reso_allo,a.order_id,c.order_no as po_number,max(c.smv) as smv,a.prod_reso_allo,sum(a.production_qnty) as good_qnty,";
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

	   	$sql_sub_contuct.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]'	and a.production_type=5 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.id left join lib_sewing_line e on a.line_id=e.id where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_name $subcon_location $floor $subcon_line   $txt_date_from group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date, a.line_id,b.party_id,c.order_no,e.sewing_line_serial,a.prod_reso_allo order by a.location_id, a.floor_id,e.sewing_line_serial,a.prod_reso_allo";

	}
	//echo $sql_sub_contuct;die;
	$sub_result=sql_select($sql_sub_contuct);
	$subcon_order_smv=array();
	foreach($sub_result as $subcon_val)
	{

		if($val[csf('prod_reso_allo')]==1)
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
		$production_serial_arr[$val[csf('floor_id')]][$slNo][$subcon_val[csf('sewing_line')]]=$subcon_val[csf('sewing_line')];

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
    $total_man_power=$gnd_avable_min=$gnd_product_min=0;$summary_total_man_power=0;$summary_total_operator=0;$gnd_avable_min_summary=0;$summary_total_helper=0;$summary_total_working_hour=0;$summary_total_day_terget=0;$summary_total_capacity=0;$tot_floor_efficency_summary=0;
	$item_smv=$item_smv_total=$line_efficiency=$days_run=$total_working_hour=$gnd_total_tgt_h=$total_capacity=0;
	$j=1;
	ob_start();
	$line_number_check_arr=array();
	$smv_for_item="";
	$total_production=array();
	$floor_production=array();
    $line_floor_production=0;
    $line_total_production=0;
	foreach($production_serial_arr as $f_id=>$fname)
	{
		ksort($fname);
		foreach($fname as $sl=>$s_data)
		{

			foreach($s_data as $l_id=>$ldata)
			{

				if($i!=1)
				{
					if(!in_array($f_id, $check_arr))
					{
						if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						 $html.='<tr  bgcolor="#B6B6B6">
							<td width="40"></td>
							<td width="80"></td>
							<td width="80"></td>
							<td width="80"></td>
							<td width="140"></td>
							<td width="80"></td>
							<td width="80"></td>
							<td width="120"></td>
							<td align="right" width="60">&nbsp;</td>
							<td align="right" width="70">'.$floor_operator.'&nbsp;</td>
							<td align="right" width="50">'.$floor_helper.'&nbsp;</td>
							<td align="right" width="60">'.$floor_man_power.'&nbsp;</td>
							<td align="right" width="70">'.$floor_tgt_h.'&nbsp;</td>
							<td align="right" width="60">'.$floor_days_run.'&nbsp;</td>
							<td align="right" width="70">'.$floor_capacity.'&nbsp;</td>
							<td align="right" width="60">'.$floor_working_hour.'&nbsp;</td>
							<td align="right" width="80">'.$eff_target_floor.'&nbsp;</td>
							<td align="right" width="80">'.$line_floor_production.'&nbsp;</td>
							<td align="right" width="80">'.($line_floor_production-$eff_target_floor).'&nbsp;</td>
							<td align="right" width="100">'.$floor_avale_minute.'&nbsp;</td>
							<td align="right" width="100">'.$floor_produc_min.'&nbsp;</td>
							<td align="right" width="60">'.number_format(($line_floor_production/$eff_target_floor)*100,2).'%&nbsp;</td>
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
							<td width="70" align="right">'. ($floor_tgt_h+$floor_sum_day_terget).'&nbsp;</td>
							<td width="70" align="right">'.($floor_capacity+$summary_floor_capacity).'&nbsp;</td>
							<td align="right" width="60">'. ($floor_man_power+$floor_sum_man_power).'&nbsp;</td>
							<td width="70" align="right">'.($floor_operator+$floor_sum_operator).'&nbsp;</td>
							<td width="50" align="right">'. ($floor_helper+$floor_sum_helper).'&nbsp;</td>
							<td align="right" width="60">'. ($floor_working_hour+$floor_sum_working_hour).'&nbsp;</td>
							<td align="right" width="80">'. ($eff_target_floor+$floor_sum_day_terget).'&nbsp;</td>
							<td align="right" width="80">'.$line_floor_production.'&nbsp;</td>
							<td align="right" width="80">'. ($line_floor_production-($eff_target_floor+$floor_sum_day_terget)).'&nbsp;</td>
							<td align="right" width="100">'. ($efficiency_min+$floor_avale_minute_summary).'&nbsp;</td>
							<td align="right" width="100">'. $floor_produc_min.'&nbsp;</td>
							<td align="right" width="90">'. number_format(($line_floor_production/($eff_target_floor+$floor_sum_day_terget))*100,2).'%&nbsp;</td>';
							if($floor_efficency<=$txt_parcentage)
							{
							$floor_html.='<td align="right" width="90" bgcolor="red">'.number_format(($floor_produc_min/($efficiency_min+$floor_avale_minute_summary))*100,2).' %&nbsp;</td>';
							}
							else
							{
							$floor_html.='<td align="right" width="90" >'.number_format(($floor_produc_min/($efficiency_min+$floor_avale_minute_summary))*100,2).' %&nbsp;</td>';
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
						  $floor_man_power=0;$floor_sum_man_power=0;$floor_sum_operator=0;$floor_sum_helper=0;$floor_sum_working_hour=0;$floor_sum_day_terget=0;$summary_floor_capacity=0;$floor_avale_minute_summary=0;
						  $floor_capacity=0;$floor_efficency_summary=0;
						  $j++;
					}
				}
				$floor_row++;

				$germents_item=array_unique(explode('****',$production_data_arr[$f_id][$ldata]['item_number_id']));

				$buyer_neme_all=array_unique(explode(',',$production_data_arr[$f_id][$ldata]['buyer_name']));
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
				$efficiency_min=0;$efficiency_min_summary=0;
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
				$subcon_po_id=array_unique(explode(',',$production_data_arr[$f_id][$ldata]['order_id']));
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
					$days_run=datediff("d",$sewing_day,$pr_date);
					}
					else  $days_run=0;
				}
				$sewing_line='';
				if($production_data_arr[$f_id][$ldata]['prod_reso_allo']==1)
				{
					$line_number=explode(",",$prod_reso_arr[$ldata]);
					foreach($line_number as $val)
					{
						if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
					}
				}
				else $sewing_line=$lineArr[$ldata];

		//******************************************************************************************************************
				$lunch_start="";
				$lunch_start=$line_number_arr[$ldata][$pr_date]['lunch_start_time'];
				$lunch_hour=$start_time_arr[$row[1]]['lst'];
				if($lunch_start!="")
				{
				$lunch_start_hour=$lunch_start;
				}
				else
				{
				$lunch_start_hour=$lunch_hour;
				}
			 //***********************************************************************************************
				$production_hour=array();
				for($h=$hour;$h<=$last_hour;$h++)
				{
					 $prod_hour="prod_hour".substr($line_start_hour_arr[$h],0,2)."";
					 $production_hour[$prod_hour]=$production_data_arr[$f_id][$ldata][$prod_hour];
					 $floor_production[$prod_hour]+=$production_data_arr[$f_id][$ldata][$prod_hour];
					 $total_production[$prod_hour]+=$production_data_arr[$f_id][$ldata][$prod_hour];
				}
				// print_r($production_hour);
				$floor_production['prod_hour24']+=$production_data_arr[$f_id][$ldata]['prod_hour23'];
				$total_production['prod_hour24']+=$production_data_arr[$f_id][$ldata]['prod_hour23'];
				$production_hour['prod_hour24']=$production_data_arr[$f_id][$ldata]['prod_hour23'];
				$line_production_hour=0;
				if(str_replace("'","",$actual_production_date)>str_replace("'","",$actual_date))
				{
					$line_start=$line_number_arr[$ldata][$pr_date]['prod_start_time'];
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
					$total_eff_hour=0;$summary_total_eff_hour=0;
					for($lh=$line_start_hour;$lh<=$last_hour;$lh++)
					{
						$bg=$start_hour_arr[$lh];
						if($lh<$actual_time)
						{
						$total_eff_hour=$total_eff_hour+1;;
						$summary_total_eff_hour=$summary_total_eff_hour+1;;
						$line_hour="prod_hour".substr($bg,0,2)."";
						$line_production_hour+=$production_data_arr[$f_id][$ldata][$line_hour];
						$line_floor_production+=$production_data_arr[$f_id][$ldata][$line_hour];
						$line_total_production+=$production_data_arr[$f_id][$ldata][$line_hour];
						$actual_time_hour=$start_hour_arr[$lh+1];
						}
					}
					if($start_hour_arr[$actual_time]>$lunch_start_hour) {$total_eff_hour=$total_eff_hour-1;$summary_total_eff_hour=$summary_total_eff_hour-1;}
					if($total_eff_hour>$prod_resource_array[$ldata][$pr_date]['working_hour'])
					{
					$total_eff_hour=$prod_resource_array[$ldata][$pr_date]['working_hour'];
					}
					if($summary_total_eff_hour>$no_prod_line_arr[$f_id]['working_hour'])
					{
					$summary_total_eff_hour=$no_prod_line_arr[$f_id]['working_hour'];
					}
				}
				if(str_replace("'","",$actual_production_date)<=str_replace("'","",$actual_date))
				{
					for($ah=$hour;$ah<=$last_hour;$ah++)
					{
					$prod_hour="prod_hour".substr($start_hour_arr[$ah],0,2)."";
					$line_production_hour+=$production_data_arr[$f_id][$ldata][$prod_hour];
					$line_floor_production+=$production_data_arr[$f_id][$ldata][$prod_hour];
					$line_total_production+=$production_data_arr[$f_id][$ldata][$prod_hour];
					}
					$total_eff_hour=$prod_resource_array[$ldata][$pr_date]['working_hour'];
					$summary_total_eff_hour=$no_prod_line_arr[$f_id]['working_hour'];
				}
				//echo $summary_total_eff_hour.'dsd';
				if($sewing_day!="")
				{
					$days_run= $diff=datediff("d",$sewing_day,$pr_date);
				}
				else  $days_run=0;
				//******************************* line effiecency****************************************************************************['']
				$total_adjustment=0;$total_adjustment_summary=0;
				$smv_adjustmet_type=$prod_resource_array[$ldata][$pr_date]['smv_adjust_type'];
				$summary_smv_adjustmet_type=$no_prod_line_arr[$f_id]['smv_adjust_type'];
				$eff_target=($prod_resource_array[$ldata][$pr_date]['terget_hour']*$total_eff_hour);

				if($total_eff_hour>=$prod_resource_array[$ldata][$pr_date]['working_hour'])
				{

				if(str_replace("'","",$smv_adjustmet_type)==1)
				{
					$total_adjustment=$prod_resource_array[$ldata][$pr_date]['smv_adjust'];
					//$total_adjustment_summary=$no_prod_line_arr[$f_id]['smv_adjust'];
				}
				if(str_replace("'","",$smv_adjustmet_type)==2)
					{
				 	$total_adjustment=($prod_resource_array[$ldata][$pr_date]['smv_adjust'])*(-1);
					//$total_adjustment_summary=($no_prod_line_arr[$f_id]['smv_adjust'])*(-1);
					}
				}

				if($total_adjustment_summary>=$no_prod_line_arr[$f_id]['working_hour'])
				{

				if(str_replace("'","",$summary_smv_adjustmet_type)==1)
				{
					//$total_adjustment=$prod_resource_array[$ldata][$pr_date]['smv_adjust'];
					$total_adjustment_summary=$no_prod_line_arr[$f_id]['smv_adjust'];
				}
				if(str_replace("'","",$summary_smv_adjustmet_type)==2)
					{
				 	//$total_adjustment=($prod_resource_array[$ldata][$pr_date]['smv_adjust'])*(-1);
					$total_adjustment_summary=($no_prod_line_arr[$f_id]['smv_adjust'])*(-1);
					}
				}
				$floor_sum_operator+=$no_prod_line_arr[$f_id]['operator'];
				//$floor_sum_operator+=$no_prod_line_arr[$f_id]['operator'];
				//echo $floor_sum_operator.'='.$summary_total_eff_hour;
				$efficiency_min+=$total_adjustment+($prod_resource_array[$ldata][$pr_date]['man_power'])*$total_eff_hour*60;
				$efficiency_min_summary+=$total_adjustment_summary+($no_prod_line_arr[$f_id]['total_line_hour'])*60;
				//echo $total_adjustment.'='.$total_adjustment_summary;
				//echo $total_adjustment_summary.'=='.$no_prod_line_arr[$f_id]['operator'].'=='.$prod_resource_array[$ldata][$pr_date]['operator'].'='.($summary_total_eff_hour.'='.$total_eff_hour);
				//echo $efficiency_min_summary.'asas';

				$line_efficiency=(($produce_minit)*100)/$efficiency_min;
				//$summary_line_efficiency=(($produce_minit)*100)/$efficiency_min_summary;
				$avil_min_summ=($floor_working_hour+$floor_sum_working_hour)*$floor_sum_operator*60;
				//****************************************************************************************************************
				$cbo_get_upto=str_replace("'","",$cbo_get_upto);
				$txt_parcentage=str_replace("'","",$txt_parcentage);
			   //********************************* calclution floor total    ****************************************************$pr_date],$sewing_day
				$floor_name=$floorArr[$f_id];
				$floor_smv+=$item_smv;
				$floor_capacity+=$prod_resource_array[$ldata][$pr_date]['capacity'];
				$summary_floor_capacity+=$prod_resource_array_summary[$f_id]['capacity'];
				$floor_operator+=$prod_resource_array[$ldata][$pr_date]['operator'];
				$floor_helper+=$prod_resource_array[$ldata][$pr_date]['helper'];
				$floor_tgt_h+=$prod_resource_array[$ldata][$pr_date]['terget_hour'];
				$floor_days_run+=$days_run;
				$floor_working_hour+=$prod_resource_array[$ldata][$pr_date]['working_hour'];
				$eff_target_floor+=$eff_target;
				$floor_today_product+=$today_product;
				$floor_avale_minute+=$efficiency_min;
				$floor_avale_minute_summary+=$efficiency_min_summary;
				$floor_produc_min+=$produce_minit;
				$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
				$floor_efficency_summary=($floor_produc_min/$efficiency_min_summary)*100;
				$tot_floor_efficency_summary=($floor_produc_min/$efficiency_min_summary)*100;
				//echo $prod_resource_array[$ldata][$pr_date]['working_hour'].'=='.$no_prod_line_arr[$f_id]['working_hour'];
				$floor_man_power+=$prod_resource_array[$ldata][$pr_date]['man_power'];
				$floor_sum_man_power+= $no_prod_line_arr[$f_id]['man_power'];

				$floor_sum_helper+=$no_prod_line_arr[$f_id]['helper'];
				$floor_sum_working_hour+=$no_prod_line_arr[$f_id]['working_hour'];
				$floor_sum_day_terget+=$no_prod_line_arr[$f_id]['hour']*$no_prod_line_arr[$f_id]['working_hour'];
				//echo $floor_avale_minute.'='.$floor_avale_minute_summary;
	//**************************** calclution total ***************************************************************************************
				$total_capacity+=$prod_resource_array[$ldata][$pr_date]['capacity'];
				$gnd_total_tgt_h+=$prod_resource_array[$ldata][$pr_date]['terget_hour'];
				$total_working_hour+=$prod_resource_array[$ldata][$pr_date]['working_hour'];
				$total_operator+=$prod_resource_array[$ldata][$pr_date]['operator'];
				$total_helper+=$prod_resource_array[$ldata][$pr_date]['helper'];
				$total_man_power+=$prod_resource_array[$ldata][$pr_date]['man_power'];
				$summary_total_man_power+=$no_prod_line_arr[$f_id]['man_power'];
				$summary_total_operator+=$no_prod_line_arr[$f_id]['operator'];
				$summary_total_helper+=$no_prod_line_arr[$f_id]['helper'];
				$summary_total_working_hour+=$no_prod_line_arr[$f_id]['working_hour'];
				$summary_total_capacity+=$prod_resource_array_summary[$f_id]['capacity'];
				$summary_total_day_terget+=$no_prod_line_arr[$f_id]['hour']*$no_prod_line_arr[$f_id]['working_hour'];
				$total_terget+=$eff_target;
				$grand_total_product+=$today_product;
				$gnd_avable_min+=$efficiency_min;
				$gnd_avable_min_summary+=$efficiency_min_summary;;
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
						<td width="140"><p>'.$production_data_arr[$f_id][$ldata]['po_number'].'&nbsp;</p></td>
						<td width="80"><p>'.$production_data_arr[$f_id][$ldata]['file'].'&nbsp;</p></td>
						<td width="80"><p>'.$production_data_arr[$f_id][$ldata]['ref'].'&nbsp;</p></td>
						<td width="120"><p>'.$garment_itemname.'&nbsp;<p/> </td>
						<td align="right" width="60"><p>'.$item_smv.'&nbsp;</p></td>
						<td align="right" width="70">'.$prod_resource_array[$ldata][$pr_date]['operator'].'&nbsp;</td>
						<td align="right" width="50">'.$prod_resource_array[$ldata][$pr_date]['helper'].'&nbsp;</td>
						<td align="right" width="60">'.$prod_resource_array[$ldata][$pr_date]['man_power'].'&nbsp;</td>
						<td align="right" width="70">'.$prod_resource_array[$ldata][$pr_date]['terget_hour'].'&nbsp;</td>
						<td align="right" width="60">'.$days_run.'&nbsp;</td>
						<td align="right" width="70">'.$prod_resource_array[$ldata][$pr_date]['capacity'].'&nbsp;</td>
						<td align="right" width="60">'.$prod_resource_array[$ldata][$pr_date]['working_hour'].'&nbsp;</td>
						<td align="right" width="80">'. $eff_target.'&nbsp;</td>
						<td width="75" align="right"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$ldata.",'tot_prod','".$smv_for_item."','".$actual_time_hour."','".$line_start."',".$txt_date.')">'.$line_production_hour.'</a>&nbsp;</td>
						<td align="right" width="80">'. ($line_production_hour-$eff_target).'&nbsp;</td>
						<td align="right" width="100">'.$efficiency_min.'&nbsp;</td>
						<td width="100" align="right"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$ldata.",'tot_prod','".$smv_for_item."','".$actual_time_hour."','".$line_start."',".$txt_date.')">'.$produce_minit.'</a>&nbsp;</td>
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
	}
			$html.='<tr  bgcolor="#B6B6B6">
					<td width="40"></td>
					<td width="80"> </td>
					<td width="80"> </td>
					<td width="80"></td>
					<td width="140"></td>
					<td width="80"></td>
					<td width="80"></td>
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
								<td width="70" align="right">'. ($floor_tgt_h+$floor_sum_day_terget).'&nbsp;</td>
								<td width="70" align="right">'.($floor_capacity+$summary_floor_capacity).'&nbsp;</td>
								<td align="right" width="60">'. ($floor_man_power+$floor_sum_man_power).'&nbsp;</td>
								<td width="70" align="right">'.($floor_operator+$floor_sum_operator).'&nbsp;</td>
								<td width="50" align="right">'. ($floor_helper+$floor_sum_helper).'&nbsp;</td>
								<td align="right" width="60">'. ($floor_working_hour+$floor_sum_working_hour).'&nbsp;</td>
								<td align="right" width="80">'.($eff_target_floor+$floor_sum_day_terget).'&nbsp;</td>
								<td align="right" width="80">'.$line_floor_production.'&nbsp;</td>
								<td align="right" width="80">'. ($line_floor_production-($eff_target_floor+$floor_sum_day_terget)).'&nbsp;</td>
								<td align="right" width="100">'. ($efficiency_min+$floor_avale_minute_summary).'&nbsp;</td>
								<td align="right" width="100">'. $floor_produc_min.'&nbsp;</td>

								<td align="right" width="90">'. number_format(($line_floor_production/($eff_target_floor+$floor_sum_day_terget))*100,2).'%&nbsp;</td>';

								if($floor_efficency<=$txt_parcentage)
								{
								$floor_html.='<td align="right" width="90" bgcolor="red">'. number_format(($floor_produc_min/($efficiency_min+$floor_avale_minute_summary))*100,2).' %&nbsp;</td>';
								}
								else
								{
								$floor_html.='<td align="right" width="90" >'.number_format(($floor_produc_min/($efficiency_min+$floor_avale_minute_summary))*100,2).' %&nbsp;</td>';
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
                        <th width="70"><? echo ($gnd_total_tgt_h+$summary_total_day_terget);   ?> </th>
                        <th width="70" align="right"><? echo ($total_capacity+$summary_total_capacity); ?> </th>
                        <th width="60"><? echo ($total_man_power+$summary_total_man_power); ?>&nbsp;</th>
                        <th width="70"><? echo ($total_operator+$summary_total_operator); ?></th>
                        <th width="50"><? echo ($total_helper+$summary_total_helper); ?></th>
                        <th align="right" width="60"><? echo ($total_working_hour+$summary_total_working_hour); ?>&nbsp;</th>
                        <th align="right" width="80"><? echo ($total_terget+$summary_total_day_terget); ?>&nbsp;</th>
                        <th align="right" width="80"><? echo $line_total_production; ?>&nbsp;</th>
                        <th align="right" width="80"><? echo $line_total_production-($total_terget+$summary_total_day_terget); ?>&nbsp;</th>
                        <th align="right" width="100"><? echo ($gnd_avable_min+$gnd_avable_min_summary); ?>&nbsp;</th>
                        <th align="right" width="100"><? echo $gnd_product_min; ?>&nbsp;</th>
                        <th align="right" width="90"><? echo number_format(($line_total_production/($total_terget+$summary_total_day_terget))*100,2)."%"; ?>&nbsp;</th>
                        <th align="center" width="90"><? echo number_format(($gnd_product_min/($gnd_avable_min+$gnd_avable_min_summary))*100,2)."%"; ?>&nbsp;</th>
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
        <table id="table_header_1" class="rpt_table" width="2630" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr height="50">
                    <th width="40">SL</th>
                    <th width="80">Floor Name</th>
                    <th width="80">Line No</th>
                    <th width="80">Buyer</th>
                    <th width="140">Order No</th>
                    <th width="80">File No</th>
                    <th width="80">Ref. No</th>
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
        <div style="width:2650px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
              <? echo $html;  ?>
                <tfoot>
                   <tr>
                        <th width="40"></th>
                        <th width="80"> </th>
                        <th width="80"> </th>
                        <th width="80"></th>
                        <th width="140"></th>
                        <th width="80"></th>
                        <th width="80"></th>
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
				//$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
			    $sql_active_line=sql_select("select sewing_line,sum(production_quantity) as total_production from pro_garments_production_mst  where production_date=".$txt_date." and production_type=5 and status_active=1 and is_deleted=0 and prod_reso_allo=1 group by  sewing_line");


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
			//$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
			if($actual_line_arr!="") $line_cond=" and a.id not in ($actual_line_arr)";
			if($db_type==0) $remarks_cond="group_concat(d.remarks)";
			else if($db_type==2) $remarks_cond="LISTAGG(cast(d.remarks as varchar2(4000)), ',') WITHIN GROUP (ORDER BY d.remarks)";

			//echo "select a.id, a.floor_id, a.line_number, b.man_power, b.operator, b.helper, b.working_hour, $remarks_cond as remarks from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and b.pr_date=".$txt_date." and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 $line_cond group by a.id, a.floor_id, a.line_number, b.man_power, b.operator, b.helper, b.working_hour";
			 $dataArray=sql_select("select a.id, a.floor_id, a.line_number, b.man_power, b.operator, b.helper, b.working_hour, $remarks_cond as remarks from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and b.pr_date=".$txt_date." and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 $line_cond group by a.id, a.floor_id, a.line_number, b.man_power, b.operator, b.helper, b.working_hour order by a.floor_id");
					$l=1; $location_array=array(); $floor_array=array();
					foreach( $dataArray as $row )
					{
						if ($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$remarks="";
						$remarks=implode(",",array_unique(explode(",",$row[csf('remarks')])));
					?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tbltr_<? echo $l; ?>','<? echo $bgcolor; ?>')" id="tbltr_<? echo $l; ?>">
                        	<td width="40"><? echo $l; ?></td>
                            <td width="100"><p><? echo $lineArr[$row[csf('line_number')]]; ?>&nbsp;</p></td>
                            <td width="100" align="right"><p>&nbsp;<? echo $floorArr[$row[csf('floor_id')]]; ?></p></td>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('man_power')]; ?></th>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('operator')]; ?></th>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('helper')]; ?></td>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('working_hour')]; ?></td>
                            <td width="380"><? echo $remarks; ?>&nbsp;</td>
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
//First Button end

if($action=="report_generate2") //2nd Button Start...
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
            .break_all
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
	$floorSeqArr = return_library_array("select id,floor_serial_no from lib_prod_floor","id","floor_serial_no");
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$comapny_id=str_replace("'","",$cbo_company_id);
    $today_date=date("Y-m-d");
	$txt_producting_day="".str_replace("'","",$txt_date)."";

	/***************************************************************************************************************************/
	$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
	order by sewing_line_serial");
	foreach($lineDataArr as $lRow)
	{
		$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
		$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
		$lastSlNo=$lRow[csf('sewing_line_serial')];
	}


	if($db_type==0)
	{
		$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$comapny_id and shift_id=1 and pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
	}
	else
	{
		$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$comapny_id and shift_id=1 and pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
	}

	if($min_shif_start=="")
	{
		echo "<p style='font-size:20px', align='center'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<p/>";die;

	}


	//==============================shift time===================================================================================================
	$start_time_arr=array();
	if($db_type==0)
	{
		$start_time_data_arr=sql_select("select company_name, shift_id, TIME_FORMAT( prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( lunch_start_time, '%H:%i' ) as lunch_start_time from variable_settings_production where company_name in($comapny_id) and shift_id=1  and variable_list=26 and status_active=1 and is_deleted=0");
	}
	else
	{
		$start_time_data_arr=sql_select("select company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time,TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($comapny_id) and  shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");
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
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_id and variable_list=23 and is_deleted=0
	and status_active=1");

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
	$cbo_no_prod_type=str_replace("'","",$cbo_no_prod_type);
	$file_no=str_replace("'","",$txt_file_no);
	$ref_no=str_replace("'","",$txt_ref_no);
	if($file_no!="") $file_cond="and c.file_no=$file_no";else $file_cond="";
	if($ref_no!="") $ref_cond="and c.grouping='$ref_no'";else $ref_cond="";
	//echo $file_cond;

	if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date=$txt_date";
	// echo $txt_date_from; die;

	if($prod_reso_allo[0]==1)
	{
		$prod_resource_array=array();

		$dataArray_sql=sql_select("select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id=$comapny_id and b.pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0");
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
		//var_dump($prod_resource_array);

		/* $sql = "SELECT MST_ID, NUMBER_OF_EMP ,ADJUST_HOUR,PR_DATE from PROD_RESOURCE_SMV_ADJ where ADJUSTMENT_SOURCE=1 and PR_DATE=$txt_date and IS_DELETED=0 and STATUS_ACTIVE=1";
		$res = sql_select($sql);
		$smv_adjustment_array = array();
		foreach ($res as $v)
		{
			// $smv_adjustment_array[$v['MST_ID']] += $v['ADJUST_HOUR'];
			$smv_adjustment_array[$v[csf('MST_ID')]][$v[csf('PR_DATE')]]['working_hour'] += $v[csf('ADJUST_HOUR')];
			$smv_adjustment_array[$v[csf('MST_ID')]][$v[csf('PR_DATE')]]['number_of_emp'] += $v[csf('NUMBER_OF_EMP')];
		}
		var_dump($smv_adjustment_array);die; */

		if(str_replace("'","",trim($txt_date))==""){$pr_date_con="";}else{$pr_date_con=" and b.pr_date=$txt_date";}

		if($db_type==0)
		{
			$dataArray=sql_select("select a.id,b.pr_date,d.shift_id,TIME_FORMAT( d.prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( d.lunch_start_time, '%H:%i' ) as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 $pr_date_con");
		}
		else
		{
			$dataArray=sql_select("select a.id,b.pr_date,d.shift_id,TO_CHAR(d.prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR( d.lunch_start_time,'HH24:MI') as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 $pr_date_con");
		}

		$line_number_arr=array();
		foreach($dataArray as $val)
		{
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['shift_id']=$val[csf('shift_id')];
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['prod_start_time']=$val[csf('prod_start_time')];
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['lunch_start_time']=$val[csf('lunch_start_time')];
		}
	}
 /************************************************************************************************************************/
  	if($db_type==0)
	{
		$manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
	}
	else
	{
		$manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
	}


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

   	//$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($manufacturing_company) and variable_list=25 and   status_active=1 and is_deleted=0");



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
		b.buyer_name  as buyer_name,b.style_ref_no,b.job_no,a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.unit_price,c.file_no,c.grouping as ref,sum(a.production_quantity) as good_qnty,";
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
		$sql.="sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23 from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a left join lib_prod_floor d on a.floor_id=d.id and d.is_deleted=0 left join lib_sewing_line e on a.sewing_line=e.id and e.is_deleted=0  where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $company_name $location $floor $line $buyer_id_cond  $txt_date_from $file_cond $ref_cond group by b.job_no,a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,e.sewing_line_serial,b.buyer_name,a.item_number_id,c.po_number,c.file_no,c.unit_price,c.grouping,b.style_ref_no order by a.location, a.floor_id,e.sewing_line_serial,a.prod_reso_allo";
	}
	else if($db_type==2)
	{
		$sql="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name  as buyer_name,b.style_ref_no,b.job_no, a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.file_no,c.unit_price,c.grouping as ref,sum(d.production_qnty) as good_qnty,";
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
		FROM  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a ,pro_garments_production_dtls d
		WHERE a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_name $location $floor $line $buyer_id_cond  $txt_date_from $file_cond $ref_cond
		GROUP BY b.job_no, a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name,b.style_ref_no,a.item_number_id,c.po_number,c.unit_price,c.file_no,c.grouping
		ORDER BY a.location,a.floor_id,a.sewing_line";
	}
	//echo $sql;die;
	$sql_resqlt=sql_select($sql);
	$production_data_arr=array();
	$production_po_data_arr=array();
	$production_serial_arr=array(); $reso_line_ids=''; $all_po_id="";
	$active_days_arr=array();
	$duplicate_date_arr=array();
	$style_wise_po_arr=array();
	$all_style_arr=array();
	foreach($sql_resqlt as $val)
	{

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
		$production_serial_arr[$floorSeqArr[$val[csf('floor_id')]]][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]]=$val[csf('sewing_line')];

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
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['job_no'].=",".$val[csf('job_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_id'].=",".$val[csf('po_break_down_id')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style'].=",".$val[csf('style_ref_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['file'].=",".$val[csf('file_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['ref'].=",".$val[csf('ref')];
		}
	 	else
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']=$val[csf('po_number')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['job_no']=$val[csf('job_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_id']=$val[csf('po_break_down_id')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style']=$val[csf('style_ref_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['file']=$val[csf('file_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['ref']=$val[csf('ref')];
		}
		$fob_rate_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['rate']=$val[csf('unit_price')];

		if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']!="")
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')];
		}
		else
		{
			 $production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')];
		}
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['quantity']+=$val[csf('good_qnty')];
		$production_data_arr_qty[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]['quantity']+=$val[csf('good_qnty')];

		if($all_po_id=="") $all_po_id=$val[csf('po_break_down_id')]; else $all_po_id.=",".$val[csf('po_break_down_id')];
		$style_wise_po_arr[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
		$all_style_arr[$val[csf('style_ref_no')]]=$val[csf('style_ref_no')];
	}
	//print_r($production_data_arr_qty);
	$po_ids=count(array_unique(explode(",",$all_po_id)));
	$po_numIds=chop($all_po_id,',');
	$poIds_cond="";
	$poIds_cond2="";
	$poIds_cond3="";
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
				$poIds_cond3.=" c.po_break_down_id  in($ids) or ";
			}
			$poIds_cond=chop($poIds_cond,'or ');
			$poIds_cond2=chop($poIds_cond,'or ');
			$poIds_cond3=chop($poIds_cond,'or ');
			$poIds_cond.=")";
			$poIds_cond2.=")";
			$poIds_cond3.=")";
		}
		else
		{
			$poIds_cond=" and  b.id  in($all_po_id)";
			$poIds_cond2=" and  c.id  in($all_po_id)";
		}
	}


	$smv_source=return_field_value("smv_source","variable_settings_production","company_name =$comapny_id and variable_list=25 and   status_active=1 and is_deleted=0");
	if($smv_source=="" || $smv_source==0) {$smv_source = 1;}

	// echo "SELECT smv_source from variable_settings_production where company_name =$comapny_id and variable_list=25 and   status_active=1 and is_deleted=0";

    if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
	// echo $smv_source;die;
    if($smv_source==3) // from gsd enrty
	{
		$style_nos="'".implode("','",$all_style_arr)."'";
		$sql_item="SELECT a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID  from PPL_GSD_ENTRY_MST a where  A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and A.STYLE_REF in($style_nos)
			group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID
			 ORDER BY a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC";//a.APPLICABLE_PERIOD <= $txt_date and a.APPROVED=1
		$gsdSqlResult=sql_select($sql_item);

		/*foreach($resultItem as $itemData)
		{
			// $item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
			$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('total_smv')];
		}*/

		foreach($gsdSqlResult as $rows)
		{
			foreach($style_wise_po_arr[$rows['STYLE_REF']] as $po_id)
			{
				if($item_smv_array[$po_id][$rows['GMTS_ITEM_ID']]==''){
					$item_smv_array[$po_id][$rows['GMTS_ITEM_ID']]=$rows['TOTAL_SMV'];
				}
			}
		}
	}
	else
	{
		$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost,c.smv_set from wo_po_details_master a,wo_po_break_down b, wo_po_details_mas_set_details c where a.id=b.job_id and b.job_id=c.job_id and a.company_name in($manufacturing_company) $poIds_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
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
	// echo "<pre>";print_r($item_smv_array);


    $po_active_sql="SELECT a.floor_id,a.sewing_line,a.production_date,a.po_break_down_id,a.item_number_id from pro_garments_production_mst a , wo_po_break_down c, wo_po_details_master b where b.id=c.job_id and c.id=a.po_break_down_id and a.production_type=5 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $company_name $location $floor $line $buyer_id_cond   $file_cond $ref_cond $poIds_cond2 group by  a.floor_id,a.sewing_line,a.production_date ,a.po_break_down_id,a.item_number_id";
    //echo $po_active_sql;die;
	foreach(sql_select($po_active_sql) as $vals)
	{
		$prod_dates=$vals[csf('production_date')];
		if($duplicate_date_arr[$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]][$prod_dates]=="")
		{
			$active_days_arr[$vals[csf('floor_id')]][$vals[csf('sewing_line')]]+=1;
			$active_days_arr_powise[$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]]+=1;
			$duplicate_date_arr[$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]][$prod_dates]=$prod_dates;
		}

	}
	//print_r($duplicate_date_arr);

	$sql_item_rate="SELECT c.po_break_down_id, c.item_number_id, c.order_quantity, c.order_total from wo_po_color_size_breakdown c where  c.is_deleted=0 and c.status_active=1 $poIds_cond3";
	$resultRate=sql_select($sql_item_rate);
	$item_po_array=array();
	foreach($resultRate as $row)
	{
		$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['qty']+=$row[csf('order_quantity')];
		$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['amt']+=$row[csf('order_total')];
	}

	// subcoutact data **********************************************************************************************************************

    if($db_type==0)
    {
		$sql_sub_contuct= "select  a.company_id, a.location_id,d.floor_serial_no,e.sewing_line_serial,a.prod_reso_allo, a.floor_id, a.production_date, a.line_id,b.party_id  as buyer_name,a.order_id,c.order_no as po_number,c.cust_style_ref,b.subcon_job as job_no, max(c.smv) as smv,sum(a.production_qnty) as good_qnty,";

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
   		$sql_sub_contuct.="sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.id and d.is_deleted=0 left join lib_sewing_line e on a.line_id=e.id and e.is_deleted=0 where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id=$comapny_id $subcon_location $floor $subcon_line   $txt_date_from group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date,a.prod_reso_allo,a.line_id,b.party_id,c.order_no,c.cust_style_ref,b.subcon_job ,e.sewing_line_serial order by a.location_id,d.floor_serial_no,e.sewing_line_serial,a.prod_reso_allo";
	}
	else
	{
		$sql_sub_contuct= "SELECT  a.company_id, a.location_id, a.floor_id,d.floor_serial_no,e.sewing_line_serial, a.production_date, a.line_id,b.party_id  as buyer_name,a.prod_reso_allo,a.order_id,c.order_no as po_number,c.cust_style_ref, b.subcon_job as job_no,  max(c.smv) as smv,a.prod_reso_allo,sum(a.production_qnty) as good_qnty,";
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

	   	$sql_sub_contuct.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]'	and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.id  and d.is_deleted=0 left join lib_sewing_line e on a.line_id=e.id and e.is_deleted=0 where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id=$comapny_id $subcon_location $floor $subcon_line   $txt_date_from group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date,  b.subcon_job,a.line_id,b.party_id,c.order_no,c.cust_style_ref,e.sewing_line_serial,a.prod_reso_allo order by a.location_id, a.floor_id,e.sewing_line_serial,a.prod_reso_allo";

	}
	//echo $sql_sub_contuct;die;
	$sub_result=sql_select($sql_sub_contuct);
	$subcon_order_smv=array();
	foreach($sub_result as $subcon_val)
	{

		if($val[csf('prod_reso_allo')]==1)
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
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['job_no'].=",".$subcon_val[csf('job_no')];
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['style'].=",".$subcon_val[csf('cust_style_ref')];
		}
		else
		{
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number']=$subcon_val[csf('po_number')];
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['job_no']=$subcon_val[csf('job_no')];
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['style']=$subcon_val[csf('cust_style_ref')];
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

		$style_wise_po_arr[$val[csf('cust_style_ref')]][$val[csf('order_id')]] = $val[csf('order_id')];
	}
	//For Summary Report New Add No Prodcut
	if($cbo_no_prod_type==1)
	{
		//No Production line Start ....
		$prod_reso_arr=return_library_array( "SELECT id, line_number from prod_resource_mst",'id','line_number');
		$sql_active_line=sql_select("select sewing_line,sum(production_quantity) as total_production from pro_garments_production_mst  where production_date=".$txt_date." and production_type=5 and status_active=1 and is_deleted=0 and prod_reso_allo=1 group by  sewing_line");
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
			$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$comapny_id and variable_list=23 and is_deleted=0 and status_active=1");
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

			 $dataArray_sum=sql_select("SELECT a.id, a.floor_id,1 as prod_reso_allo,2 as type_line, a.line_number as line_no, b.man_power, b.operator, b.helper, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type,d.prod_start_time from  prod_resource_dtls b,prod_resource_dtls_time d,prod_resource_mst a  where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and b.pr_date=".$txt_date." and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 and a.id not in($res_line_cond) and a.line_number like '%$lin_ids%'  $location  $floor  group by a.id, a.floor_id, a.line_number, d.prod_start_time,b.man_power, b.operator, b.helper, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type order by a.floor_id");
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

				 $production_serial_arr[$floorSeqArr[$row[csf('floor_id')]]][$row[csf('floor_id')]][$slNo][$row[csf('id')]]=$row[csf('id')];
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
			 $dataArray_sql_cap=sql_select("select  a.floor_id, a.line_number as line_no,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id  and a.company_id=$comapny_id and b.pr_date=".$txt_date."  and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  group by a.id,a.line_number, a.floor_id, a.line_number, b.man_power, b.operator,c.capacity, b.helper, b.working_hour,b.target_per_hour order by a.floor_id");

			 //$prod_resource_array_summary=array();
			 foreach( $dataArray_sql_cap as $row)
			 {
				 $production_data_arr[$row[csf('floor_id')]][$row[csf('line_no')]]['capacity']=$row[csf('capacity')];
			 }

	} //End

	// ================================= smv adjust ===========================
	$res_line_cond=rtrim($reso_line_ids,",");
	$sql2 = "SELECT MST_ID,NUMBER_OF_EMP ,ADJUST_HOUR from PROD_RESOURCE_SMV_ADJ where ADJUSTMENT_SOURCE=1 and PR_DATE=$txt_date and IS_DELETED=0 and STATUS_ACTIVE=1 and MST_ID in($res_line_cond)";
	// echo $sql2;
	$res = sql_select($sql2);
	$extra_hour_data_array = array();
	foreach ($res as $v)
	{
		$extra_hour_data_array[$v["MST_ID"]] += $v['NUMBER_OF_EMP']*$v['ADJUST_HOUR']*60;
	}


	//echo "<pre>";
	//var_dump($extra_hour_data_array);die;
    $avable_min=0;
	$today_product=0;
    $floor_name="";
    $floor_man_power=0;
	$floor_operator=$floor_produc_min=0;
	$floor_smv=$floor_row=$floor_helper=$floor_tgt_h=$floor_days_run=$floor_working_hour=$line_floor_production=$floor_today_product=$floor_avale_minute=0;
	$total_operator=$total_helper=$gnd_hit_rate=0;
    $total_smv=$total_terget=$grand_total_product=$gnd_line_effi=0;
    $total_man_power=$gnd_avable_min=$gnd_product_min=0;
	$item_smv=$item_smv_total=$line_efficiency=$days_run=$days_active=$total_working_hour=$gnd_total_tgt_h=$total_capacity=0;
	$j=1;
	ob_start();
	$line_number_check_arr=array();
	$smv_for_item="";
	$total_production=array();
	$floor_production=array();
    $line_floor_production=0;
    $line_total_production=0; $gnd_total_fob_val=0; $gnd_final_total_fob_val=0;
	$gnd_total_fob_rate_val=0;$gnd_total_fob_final_rate_val=0;
	ksort($production_serial_arr);
	foreach($production_serial_arr as $f_sl=>$fsl_data)
	{
		foreach($fsl_data as $f_id=>$fname)
		{
			ksort($fname);
			foreach($fname as $sl=>$s_data)
			{

				foreach($s_data as $l_id=>$ldata)
				{
				$po_value=$production_data_arr[$f_id][$ldata]['po_number'];
				if($po_value)
				{

					//}

					if($i!=1)
					{
						if(!in_array($f_id, $check_arr))
						{
							if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$html.='<tr  bgcolor="#B6B6B6">
								<td class="break_all" width="40">&nbsp;</td>
								<td class="break_all" width="80">&nbsp;</td>
								<td class="break_all" width="80">&nbsp;</td>
								<td class="break_all" width="80">&nbsp;</td>
								<td class="break_all" width="100">&nbsp;</td>
								<td class="break_all" width="140">&nbsp;</td>
								<td class="break_all" width="100">&nbsp;</td>
								<td class="break_all" width="80">&nbsp;</td>
								<td class="break_all" width="80">&nbsp;</td>
								<td class="break_all" width="80">&nbsp;</td>
								<td class="break_all" align="right" width="60">&nbsp;</td>
								<td class="break_all" align="right" width="70">'.number_format($floor_operator,0).'</td>
								<td class="break_all" align="right" width="50">'. number_format($floor_helper,0).'</td>
								<td class="break_all" align="right" width="60">'. number_format($floor_man_power,0).'</td>
								<td class="break_all" align="right" width="70">'. number_format($floor_tgt_h,0).'</td>
								<td class="break_all" align="right" width="60">'. number_format($floor_days_run,0).'</td>
								<td class="break_all" align="right" width="60">'. number_format($floor_days_active,0).'</td>
								<td class="break_all" align="right" width="70">'.number_format($floor_capacity,0).'</td>
								<td class="break_all" align="right" width="60">'. number_format($floor_working_hour,0).'</td>
								<td class="break_all" align="right" width="60">&nbsp;</td>
								<td class="break_all" align="right" width="60">&nbsp;</td>
								<td class="break_all" align="right" width="80">'.number_format($eff_target_floor,0).'</td>
								<td class="break_all" align="right" width="80">'.number_format($line_floor_production,0).'</td>
								<td class="break_all" align="right" width="80">&nbsp;</td>
								<td class="break_all" align="right" width="80">'. number_format(($line_floor_production-$eff_target_floor),0).'</td>
								<td class="break_all" align="right" width="100">'. number_format($floor_avale_minute,0).'</td>
								<td class="break_all" align="right" width="100">'. number_format($floor_produc_min,0).'</td>
								<td class="break_all" align="right" width="60">'.number_format(($line_floor_production/$eff_target_floor)*100,2).'%</td>
								<td class="break_all" align="right" width="90">'.number_format($floor_efficency,2).' %</td>
								<td class="break_all" align="right" width="70">'.number_format($gnd_total_fob_rate_val,2).' </td>
								<td class="break_all" align="right" width="70">&nbsp;</td>
								<td class="break_all" align="right" width="70">'.number_format($gnd_total_fob_val,2).'</td>';

								// $gnd_total_hourly_val=0;
                                $gnd_total_fob_rate_val=0;
								$gnd_total_fob_val=0;
								for($k=$hour; $k<=$last_hour; $k++)
								{
									$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";


									if($start_hour_arr[$k]==$global_start_lanch)
									{
										$bg_color='background:yellow';
									}
									if($floor_tgt_h>$floor_production[$prod_hour])
									{
										$bg_color='background:red';
										if($floor_production[$prod_hour]==0)
										{
											$bg_color='';
										}
									}
									else
									{
										$bg_color='';
									}


									$html.='<td class="break_all" align="right" width="50" style='.$bg_color.' >'. $floor_production[$prod_hour].'</td>';
								}
								$html.='</tr>';
								$floor_html.='<tbody>';
								$floor_html.="<tr bgcolor='$bgcolor' onclick=change_color('floor_$j','$bgcolor') id=floor_$j>";
								$floor_html.='<td class="break_all" width="40">'.$j.'&nbsp;</td>
								<td class="break_all" width="80" align="center">'.$floor_name.'&nbsp; </td>
								<td class="break_all" width="70" align="right">'. $floor_tgt_h.'</td>
								<td class="break_all" width="70" align="right">'.$floor_capacity.'</td>
								<td class="break_all" align="right" width="60">'. $floor_man_power.'</td>
								<td class="break_all" width="70" align="right">'.$floor_operator.'</td>
								<td class="break_all" width="50" align="right">'. $floor_helper.'</td>
								<td class="break_all" align="right" width="60">'. $floor_working_hour.'</td>
								<td class="break_all" align="right" width="80">'. $eff_target_floor.'</td>
								<td class="break_all" align="right" width="80">'.$line_floor_production.'</td>
								<td class="break_all" align="right" width="80">'. ($line_floor_production-$eff_target_floor).'</td>
								<td class="break_all" align="right" width="100">'. $floor_avale_minute.'</td>
								<td class="break_all" align="right" width="100">'. number_format($floor_produc_min,0).'</td>
								<td class="break_all" align="right" width="90">'. number_format(($line_floor_production/$eff_target_floor)*100,2).'%</td>';
								if($floor_efficency<=$txt_parcentage)
								{
									$floor_html.='<td class="break_all" align="right" width="90" bgcolor="red">'.number_format($floor_efficency,2).' %</td>';
								}
								else
								{
									$floor_html.='<td class="break_all" align="right" width="90" >'.number_format($floor_efficency,2).' %</td>';
								}
								for($k=$hour; $k<=$last_hour; $k++)
								{
									$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
									if($start_hour_arr[$k]==$global_start_lanch)
									{
										$floor_html.='<td class="break_all" align="right" width="50" style=" background-color:#FFFF66" >'. $floor_production[$prod_hour].'</td>';
									}
									else
									{
										$floor_html.='<td class="break_all" align="right" width="50" >'. $floor_production[$prod_hour].'</td>';
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
					//$item_ids=$production_data_arr[$f_id][$ldata]['item_number_id'];
					$germents_item=array_unique(explode('****',$production_data_arr[$f_id][$ldata]['item_number_id']));

					$buyer_neme_all=array_unique(explode(',',$production_data_arr[$f_id][$ldata]['buyer_name']));
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
					$tot_po_qty=0;$fob_val=0;
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
						if($item_smv !='') $item_smv.='/';
						//echo $po_garment_item[0].'='.$po_garment_item[1];

						$item_smv .= $item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
						//echo gettype($item_smv_array[$po_garment_item[0]][$po_garment_item[1]]); exit();
						if($order_no_total!="") $order_no_total.=",";
						$order_no_total.=$po_garment_item[0];
						if($smv_for_item!="") $smv_for_item.="****".$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]]."**".$po_garment_item[1];
						else
						$smv_for_item=$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]]."**".$po_garment_item[1];
						$produce_minit+=$production_po_data_arr[$f_id][$l_id][$po_garment_item[0]]*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
						// echo $production_po_data_arr[$f_id][$l_id][$po_garment_item[0]]."*".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]]."<br>";
						$fob_rate=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt']/$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];
						$prod_qty=$production_data_arr_qty[$f_id][$l_id][$po_garment_item[0]][$po_garment_item[1]]['quantity'];
						//echo $prod_qty.'<br>';
						if(is_nan($fob_rate)){ $fob_rate=0; }
						$fob_val+=$prod_qty*$fob_rate;
					}
					//$fob_rate=$tot_po_amt/$tot_po_qty;

					$subcon_po_id=array_unique(explode(',',$production_data_arr[$f_id][$ldata]['order_id']));
					$subcon_order_id="";
					foreach($subcon_po_id as $sub_val)
					{
						$subcon_po_smv=explode(',',$sub_val);
						if($sub_val!=0)
						{
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
						$days_run=datediff("d",$sewing_day,$pr_date);
						}
						else  $days_run=0;
					}
					$type_line=$production_data_arr[$f_id][$ldata]['type_line'];
					$prod_reso_allo=$production_data_arr[$f_id][$ldata]['prod_reso_allo'];
					/*if($type_line==2)
					{
						$sewing_line='';
						if($production_data_arr[$f_id][$ldata]['prod_reso_allo']==1)
						{
							$line_number='';
							$line_number=explode(",",$ldata);
							foreach($line_data as $lin_id)
							{
								//echo $lin_id.'dd';
								$line_number=explode(",",$prod_reso_arr[$lin_id]);
							}
							foreach($line_number as $val)
							{
								if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
							}
						}
						else $sewing_line=$lineArr[$lin_id];
					}
					else
					{*/
						$sewing_line='';
						if($production_data_arr[$f_id][$ldata]['prod_reso_allo']==1)
						{
							$line_number=explode(",",$prod_reso_arr[$ldata]);
							foreach($line_number as $val)
							{
								if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
							}
						}
						else $sewing_line=$lineArr[$ldata];
					//}

					/*$sewing_line='';
					if($production_data_arr[$f_id][$ldata]['prod_reso_allo']==1)
					{
					$line_number=explode(",",$prod_reso_arr[$ldata]);
					foreach($line_number as $val)
					{
					if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
					}
					}
					else $sewing_line=$lineArr[$ldata];*/


					$lunch_start="";
					$lunch_start=$line_number_arr[$ldata][$pr_date]['lunch_start_time'];
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
					for($h=$hour;$h<=$last_hour;$h++)
					{
						$prod_hour="prod_hour".substr($line_start_hour_arr[$h],0,2)."";
						$production_hour[$prod_hour]=$production_data_arr[$f_id][$ldata][$prod_hour];
						$floor_production[$prod_hour]+=$production_data_arr[$f_id][$ldata][$prod_hour];
						$total_production[$prod_hour]+=$production_data_arr[$f_id][$ldata][$prod_hour];
					}

					$floor_production['prod_hour24']+=$production_data_arr[$f_id][$ldata]['prod_hour23'];
					$total_production['prod_hour24']+=$production_data_arr[$f_id][$ldata]['prod_hour23'];
					$production_hour['prod_hour24']=$production_data_arr[$f_id][$ldata]['prod_hour23'];
					$line_production_hour=0;
					if(str_replace("'","",$actual_production_date)>str_replace("'","",$actual_date))
					{
						if($type_line==2) //No Profuction Line
						{
							$line_start=$production_data_arr[$f_id][$l_id]['prod_start_time'];
						}
						else
						{
							$line_start=$line_number_arr[$ldata][$pr_date]['prod_start_time'];
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
						$total_effi_hour=0;
						for($lh=$line_start_hour;$lh<=$last_hour;$lh++)
						{
							$bg=$start_hour_arr[$lh];
							if($lh<$actual_time)
							{
							$total_eff_hour=$total_eff_hour+1;;
							$line_hour="prod_hour".substr($bg,0,2)."";
							$line_production_hour+=$production_data_arr[$f_id][$ldata][$line_hour];
							$line_floor_production+=$production_data_arr[$f_id][$ldata][$line_hour];
							$line_total_production+=$production_data_arr[$f_id][$ldata][$line_hour];
							$actual_time_hour=$start_hour_arr[$lh+1];
							}
						}
						if($start_hour_arr[$actual_time]>$lunch_start_hour) $total_eff_hour=$total_eff_hour-1;

						if($type_line==2)
						{
							if($total_eff_hour>$production_data_arr[$f_id][$l_id]['working_hour'])
							{
								$total_eff_hour=$production_data_arr[$f_id][$l_id]['working_hour'];
							}
						}
						else
						{
							if($total_eff_hour>$prod_resource_array[$ldata][$pr_date]['working_hour'])
							{
								$total_eff_hour=$prod_resource_array[$ldata][$pr_date]['working_hour'];
							}
						}

					}
					if(str_replace("'","",$actual_production_date)<=str_replace("'","",$actual_date))
					{
						for($ah=$hour;$ah<=$last_hour;$ah++)
						{
							$prod_hour="prod_hour".substr($start_hour_arr[$ah],0,2)."";
							$line_production_hour+=$production_data_arr[$f_id][$ldata][$prod_hour];
							$line_floor_production+=$production_data_arr[$f_id][$ldata][$prod_hour];
							$line_total_production+=$production_data_arr[$f_id][$ldata][$prod_hour];
							if($production_data_arr[$f_id][$ldata][$prod_hour]>0)
							{
								$total_effi_hour++;
							}
						}
						if($type_line==2)
						{
							$total_eff_hour=$production_data_arr[$f_id][$l_id]['working_hour'];
						}
						else
						{
							$total_eff_hour=$prod_resource_array[$ldata][$pr_date]['working_hour'];

						}
					}

					if($sewing_day!="")
					{
						$days_run= $diff=datediff("d",$sewing_day,$pr_date);
						$days_active= $active_days_arr[$f_id][$l_id];
					}
					else
					{
						$days_run=0;
						$days_active=0;
					}

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
						// $cla_cur_time=$total_effi_hour;
					}
					else
					{
						$current_wo_time=$total_eff_hour;
						$cla_cur_time=$total_eff_hour;
						// $cla_cur_time=$total_effi_hour;
					}
					$total_adjustment=0;
					if($type_line==2) //No Production Line
					{
						$smv_adjustmet_type=$production_data_arr[$f_id][$l_id]['smv_adjust_type'];
						$eff_target=($production_data_arr[$f_id][$l_id]['terget_hour']*$total_eff_hour);

						if($total_eff_hour>=$production_data_arr[$f_id][$l_id]['working_hour'])
						{
							if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$production_data_arr[$f_id][$l_id]['smv_adjust'];
							if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($production_data_arr[$f_id][$l_id]['smv_adjust'])*(-1);
						}
						$efficiency_min+=$total_adjustment+($production_data_arr[$f_id][$l_id]['man_power'])*$cla_cur_time*60;
						$line_efficiency=(($produce_minit)*100)/$efficiency_min;
					}
					else
					{
						$smv_adjustmet_type=$prod_resource_array[$ldata][$pr_date]['smv_adjust_type'];
						$eff_target=($prod_resource_array[$ldata][$pr_date]['terget_hour']*$total_eff_hour);

						if($total_eff_hour>=$prod_resource_array[$ldata][$pr_date]['working_hour'])
						{
						if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array[$ldata][$pr_date]['smv_adjust'];
						if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array[$ldata][$pr_date]['smv_adjust'])*(-1);
						}

						/*$actual_hours=date("H",time())-$line_start_hour; for metro
						$cur_time=date("H",time());
						if($cur_time>$line_start_hour){
							$actual_hours=$actual_hours-1;
						}
						else
						{
							$actual_hours=$actual_hours	;
						}
						$total_eff_hour_custom=($actual_hours>$total_eff_hour)?$total_eff_hour:$actual_hours;

						$producting_day=strtotime("Y-m-d",$txt_producting_day);

						if($producting_day>$today_date)
						{

							$efficiency_min+=$total_adjustment+($prod_resource_array[$ldata][$pr_date]['man_power'])*$total_eff_hour*60;
							//echo $total_eff_hour."_";
						}
						else
						{
							$efficiency_min+=$total_adjustment+($prod_resource_array[$ldata][$pr_date]['man_power'])*$total_eff_hour_custom*60;
							//echo $total_eff_hour_custom."_";
						}*/




						$efficiency_min+=$total_adjustment+($prod_resource_array[$ldata][$pr_date]['man_power'])*$cla_cur_time*60;
						$line_efficiency=(($produce_minit)*100)/$efficiency_min;
						// echo "((".$produce_minit.")*100)/".$efficiency_min."==".$cla_cur_time."<br>";
					}




					if($type_line==2) //No Production Line
					{
						$man_power=$production_data_arr[$f_id][$l_id]['man_power'];
						$operator=$production_data_arr[$f_id][$l_id]['operator'];
						$helper=$production_data_arr[$f_id][$l_id]['helper'];
						$terget_hour=$production_data_arr[$f_id][$l_id]['target_hour'];
						$capacity=$production_data_arr[$f_id][$l_id]['capacity'];
						$working_hour=$production_data_arr[$f_id][$l_id]['working_hour'];

						$floor_working_hour+=$production_data_arr[$f_id][$l_id]['working_hour'];
						$eff_target_floor+=$eff_target;
						$floor_today_product+=$today_product;
						$floor_avale_minute+=$efficiency_min;
						$floor_produc_min+=$produce_minit;
						$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
						$floor_capacity+=$production_data_arr[$f_id][$l_id]['capacity'];
						$floor_helper+=$production_data_arr[$ldata][$pr_date]['helper'];
						$floor_man_power+=$production_data_arr[$f_id][$l_id]['man_power'];
						$floor_operator+=$production_data_arr[$f_id][$l_id]['operator'];
						$total_operator+=$production_data_arr[$f_id][$l_id]['operator'];
						$total_man_power+=$production_data_arr[$f_id][$l_id]['man_power'];
						$total_helper+=$production_data_arr[$f_id][$l_id]['helper'];
						$total_capacity+=$production_data_arr[$f_id][$l_id]['capacity'];
						$floor_tgt_h+=$production_data_arr[$f_id][$l_id]['target_hour'];
						$total_working_hour+=$production_data_arr[$f_id][$l_id]['working_hour'];
						$gnd_total_tgt_h+=$production_data_arr[$f_id][$l_id]['target_hour'];
						$total_terget+=$eff_target;
						$grand_total_product+=$today_product;
						$gnd_avable_min+=$efficiency_min;
						$gnd_product_min+=$produce_minit;
						$gnd_total_fob_rate_val+=$fob_rate;
                        $gnd_total_fob_final_rate_val+=$fob_rate;
						$gnd_total_hourly_val+=$hourly_traget;
						$gnd_final_hourly_val+=$hourly_traget;
						$gnd_total_fob_val+=$fob_val;
						$gnd_final_total_fob_val+=$fob_val;
					}
					else
					{
						$man_power=$prod_resource_array[$ldata][$pr_date]['man_power'];
						$operator=$prod_resource_array[$ldata][$pr_date]['operator'];
						$helper=$prod_resource_array[$ldata][$pr_date]['helper'];
						$terget_hour=$prod_resource_array[$ldata][$pr_date]['terget_hour'];
						$capacity=$prod_resource_array[$ldata][$pr_date]['capacity'];
						$working_hour=$prod_resource_array[$ldata][$pr_date]['working_hour'];
						$avl_min = $man_power*$working_hour*60;

						// $adj_working_hour=$smv_adjustment_array[$ldata][$pr_date]['working_hour'];
						// $adj_number_of_emp=$smv_adjustment_array[$ldata][$pr_date]['number_of_emp'];
						// $avl_min += $adj_number_of_emp*$adj_working_hour*60;
						$extra_hour = $extra_hour_data_array[$ldata];
						$working_hour = number_format((($avl_min+$extra_hour)/$man_power/60),2);

						$floor_capacity+=$prod_resource_array[$ldata][$pr_date]['capacity'];
						$floor_man_power+=$prod_resource_array[$ldata][$pr_date]['man_power'];
						$floor_operator+=$prod_resource_array[$ldata][$pr_date]['operator'];
						$floor_helper+=$prod_resource_array[$ldata][$pr_date]['helper'];
						$floor_tgt_h+=$prod_resource_array[$ldata][$pr_date]['terget_hour'];
						$floor_working_hour+=$prod_resource_array[$ldata][$pr_date]['working_hour'];
						$eff_target_floor+=$eff_target;
						$floor_today_product+=$today_product;
						$floor_avale_minute+=$efficiency_min;
						$floor_produc_min+=$produce_minit;
						$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;

						$total_operator+=$prod_resource_array[$ldata][$pr_date]['operator'];
						$total_man_power+=$prod_resource_array[$ldata][$pr_date]['man_power'];
						$total_helper+=$prod_resource_array[$ldata][$pr_date]['helper'];
						$total_capacity+=$prod_resource_array[$ldata][$pr_date]['capacity'];
						$total_working_hour+=$prod_resource_array[$ldata][$pr_date]['working_hour'];
						$gnd_total_tgt_h+=$prod_resource_array[$ldata][$pr_date]['terget_hour'];
						$total_terget+=$eff_target;
						$grand_total_product+=$today_product;
						$gnd_avable_min+=$efficiency_min;
						$gnd_product_min+=$produce_minit;
						$gnd_total_fob_rate_val+=$fob_rate;
                        $gnd_total_fob_final_rate_val+=$fob_rate;
						$gnd_total_hourly_val+=$hourly_traget;
						$gnd_final_hourly_val+=$hourly_traget;
						$gnd_total_fob_val+=$fob_val;
						$gnd_final_total_fob_val+=$fob_val;

					}
					$po_id=rtrim($production_data_arr[$f_id][$ldata]['po_id'],',');
					$po_id=array_unique(explode(",",$po_id));
					$style=rtrim($production_data_arr[$f_id][$ldata]['style']);
					$style=implode(",",array_unique(explode(",",$style)));

					$cbo_get_upto=str_replace("'","",$cbo_get_upto);
					$txt_parcentage=str_replace("'","",$txt_parcentage);

					$floor_name=$floorArr[$f_id];
					$floor_smv+=$item_smv;

					$floor_days_run+=$days_run;
					$floor_days_active+=$days_active;

					$po_id=$production_data_arr[$f_id][$ldata]['po_id'];//$item_ids//$subcon_order_id
					$styles=explode(",",$style);
					$style_button='';//
					foreach($styles as $sid)
					{
						if( $style_button=='')
						{
							$style_button="<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$ldata."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
						}
						else
						{
							$style_button.=", "."<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$ldata."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
						}
					}
					$as_on_current_hour_target=0; $as_on_current_hour_variance=0;
					$as_on_current_hour_target=$terget_hour*$cla_cur_time;
					$hourly_traget=$eff_target*$fob_rate;
					// echo $hourly_traget;
					$as_on_current_hour_variance=$line_production_hour-$as_on_current_hour_target;
					$itmSMV = explode("/", $item_smv);
					$newItemSmvString = "";
					foreach($itmSMV as $iSMV){
						$newItemSmvString .= number_format((float)$iSMV, 2)."/";
					}
					if($line_efficiency<=$txt_parcentage) $efficiency_color="#FF0000"; else $efficiency_color="#FFFFFF";
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$html.='<tbody>';
					$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
					$html.='<td class="break_all" width="40">'.$i.'&nbsp;</td>
							<td class="break_all" width="80">'.$floor_name.'&nbsp; </td>
							<td class="break_all" align="center" width="80" >'. $sewing_line.'&nbsp; </td>
							<td class="break_all" width="80"><p>'.$buyer_name.'&nbsp;</p></td>
							<td class="break_all" width="100"><p>'.$production_data_arr[$f_id][$ldata]['job_no'].'&nbsp;</p></td>
							<td class="break_all" width="140"><p>'.$production_data_arr[$f_id][$ldata]['po_number'].'&nbsp;</p></td>
							<td class="break_all" width="100"><p>'.$style_button.'&nbsp;</p></td>
							<td class="break_all" width="80"><p>'.$production_data_arr[$f_id][$ldata]['file'].'&nbsp;</p></td>
							<td class="break_all" width="80"><p>'.$production_data_arr[$f_id][$ldata]['ref'].'&nbsp;</p></td>
							<td class="break_all" width="120" style="word-wrap:break-word; word-break: break-all;">'.$garment_itemname.'</td>
							<td class="break_all" align="right" width="60"><p>'.$newItemSmvString.'</p></td>
							<td class="break_all" align="right" width="70">'.$operator.'</td>
							<td class="break_all" align="right" width="50">'.$helper.'</td>
							<td class="break_all" align="right" width="60">'.$man_power.'</td>
							<td class="break_all" align="right" width="70">'.$terget_hour.'</td>
							<td class="break_all" align="right" width="60">'.$days_run.'</td>
							<td class="break_all" align="right" width="60">'.$active_days.'</td>
							<td class="break_all" align="right" width="70">'.$capacity.'</td>

							<td class="break_all" align="right" width="60"><a href="##" onclick="open_smv_adjust_popup('.$ldata.",".$txt_date.')">'.$working_hour.'</a></td>

							<td class="break_all" align="right" width="60" title="Production Hour">'.$cla_cur_time.'</td>
							<td class="break_all" align="right" width="60" title="Target Hour*Working Hour">'.number_format($as_on_current_hour_target,0).'</td>
							<td class="break_all" align="right" width="80">'.number_format($eff_target,0).'</td>
							<td class="break_all" width="75" align="right"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$ldata.",'tot_prod','".$smv_for_item."','".$acturl_hour_minute."','".$line_start."',".$txt_date.')">'.$line_production_hour.'</a></td>
							<td class="break_all" align="right" width="80" title="Line Production Hour - (Target Hour*Working Hour ">'.number_format($as_on_current_hour_variance,0).'</td>
							<td class="break_all" align="right" width="80" title="Line Production Hour - Effiecency Target">'.($line_production_hour-$eff_target).'</td>
							<td class="break_all" align="right" width="100" title="SMV Adjustment+(Manpower*Working Hour*60)">'.number_format($efficiency_min,0).'</td>

							<td class="break_all" width="100" align="right"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$ldata.",'tot_prod','".$smv_for_item."','".$acturl_hour_minute."','".$line_start."',".$txt_date.')">'.number_format($produce_minit,0).'</a></td>
							<td class="break_all" align="right" width="60" title="(Line production hour/Effiecency Target) * 100">'. number_format(($line_production_hour/$eff_target)*100,2).'%</td>';

							if($line_efficiency<=$txt_parcentage)
							{
								$html.='<td class="break_all" align="right" width="60" bgcolor="red" title="(Produce min/Effiecency min) * 100">'.number_format($line_efficiency,2).'%</td>';
							}
							else
							{
								$html.='<td class="break_all" align="right" width="60" title="(Produce min/Effiecency min) * 100">'.number_format($line_efficiency,2).'%</td>';
							}
							$html.='<td class="break_all" align="right" width="70">'.number_format($fob_rate,2).'</td>';
							$html.='<td class="break_all" align="right" width="70">'.number_format($hourly_traget,2).'</td>';
							$html.='<td class="break_all" width="70" title='.$fob_rate.' align="right"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$ldata.",'tot_fob_value_popup','".$smv_for_item."','".$actual_time_hour."','".$line_start."',".$txt_date.')">'.number_format($fob_val,2).'</a></td>';
							for($k=$hour; $k<=$last_hour; $k++)
							{
								$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";


								if($start_hour_arr[$k]==$lunch_start_hour)
								{
									$bg_color='background:yellow';
								}
								else if($terget_hour>$production_hour[$prod_hour])
								{
									$bg_color='background:red';
									if($production_hour[$prod_hour]==0)
									{
										$bg_color='';
									}
								}
								else if($terget_hour<$production_hour[$prod_hour])
								{
									$bg_color='background:green';
									if($production_hour[$prod_hour]==0)
									{
										$bg_color='';
									}
								}
								else
								{
									$bg_color="";
								}

								$html.='<td class="break_all" align="right" width="50"  style='.$bg_color.'>'.$production_hour[$prod_hour].'</td>';
								//$html.='<td class="break_all" align="right" width="50"  style=" background-color:#FFFF66" >'.$production_hour[$prod_hour].'&nbsp;kk</td>';
							}
					$html.='</tr>';
					$i++;
					$check_arr[]=$f_id;
				}
				}

			}
		}
	}
			$html.='<tr  bgcolor="#B6B6B6">
					<td class="break_all" width="40">&nbsp;</td>
					<td class="break_all" width="80">&nbsp;</td>
					<td class="break_all" width="80">&nbsp;</td>
					<td class="break_all" width="80">&nbsp;</td>
					<td class="break_all" width="100">&nbsp;</td>
					<td class="break_all" width="140">&nbsp;</td>
					<td class="break_all" width="100">&nbsp;</td>
					<td class="break_all" width="80">&nbsp;</td>
					<td class="break_all" width="80">&nbsp;</td>
					<td class="break_all" width="120">&nbsp;</td>
					<td class="break_all" align="right" width="60">&nbsp;</td>
					<td class="break_all" align="right" width="70">'. number_format($floor_operator,0).'</td>
					<td class="break_all" align="right" width="50">'. number_format($floor_helper,0).'</td>
					<td class="break_all" align="right" width="60">'. number_format($floor_man_power,0).'</td>
					<td class="break_all" align="right" width="70">'. number_format($floor_tgt_h,0).'</td>
					<td class="break_all" align="right" width="60">'. number_format($floor_days_run,0).'</td>
					<td class="break_all" align="right" width="60">'. number_format($floor_days_active,0).'</td>
					<td class="break_all" align="right" width="70">&nbsp;</td>
					<td class="break_all" align="right" width="60">'. number_format($floor_working_hour,0).'</td>
					<td class="break_all" align="right" width="60">&nbsp;</td>
					<td class="break_all" align="right" width="60">&nbsp;</td>
					<td class="break_all" align="right" width="80">'. number_format($eff_target_floor).'</td>
					<td class="break_all" align="right" width="80">'.number_format($line_floor_production,0).'</td>
					<td class="break_all" align="right" width="80">'.number_format($line_floor_production,0).'</td>
					<td class="break_all" align="right" width="80">'. ($line_floor_production-$eff_target_floor).'</td>
					<td class="break_all" align="right" width="100">'. number_format($floor_avale_minute,0).'</td>
					<td class="break_all" align="right" width="100">'. number_format($floor_produc_min,0).'</td>
					<td class="break_all" align="right" width="60">'. number_format(($line_floor_production/$eff_target_floor)*100,2).'%</td>
					<td class="break_all" align="right" width="90">'.number_format($floor_efficency,2).'%</td>
					<td class="break_all" align="right" width="70">'.number_format($gnd_total_fob_rate_val,2).'</td>
					<td class="break_all" align="right" width="70">&nbsp;</td>
					<td class="break_all" width="70" align="right">'.number_format($gnd_total_fob_val,2).'</td>';

					for($k=$hour; $k<=$last_hour; $k++)
					{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";

						if($floor_tgt_h>$floor_production[$prod_hour])
						{
							$bg_color='background:red';
							if($floor_production[$prod_hour]==0)
							{
								$bg_color='';
							}
						}
						else if($floor_tgt_h<$floor_production[$prod_hour])
						{
							$bg_color='background:green';
							if($floor_production[$prod_hour]==0)
							{
								$bg_color='';
							}
						}
						else if($start_hour_arr[$k]==$global_start_lanch)
						{
							 $bg_color='background:yellow';
						//$html.='<td class="break_all" align="right" width="50" style=" background-color:#FFFF66" >'. $floor_production[$prod_hour].'&nbsp;</td>';
						}
						else
						{
							 $bg_color='';
						//$html.='<td class="break_all" align="right" width="50">'. $floor_production[$prod_hour].'&nbsp;</td>';
						}
						$html.='<td class="break_all" align="right" width="50" style='.$bg_color.' >'. $floor_production[$prod_hour].'</td>';
					}

				   $html.='</tr> </tbody>';
				   $floor_html.="<tr bgcolor='$bgcolor' onclick=change_color('floor_$j','$bgcolor') id=floor_$j>";
				   $floor_html.='<td class="break_all" width="40">'.$j.'&nbsp;</td>
								<td class="break_all" width="80" align="center">'.$floor_name.'&nbsp; </td>
								<td class="break_all" width="70" align="right">'. number_format($floor_tgt_h,0).'</td>
								<td class="break_all" width="70" align="right">'.number_format($floor_capacity,0).'</td>
								<td class="break_all" align="right" width="60">'. number_format($floor_man_power,0).'</td>
								<td class="break_all" width="70" align="right">'.number_format($floor_operator,0).'</td>
								<td class="break_all" width="50" align="right">'. number_format($floor_helper,0).'</td>
								<td class="break_all" align="right" width="60">'. number_format($floor_working_hour,0).'</td>
								<td class="break_all" align="right" width="80">'. number_format($eff_target_floor,0).'</td>
								<td class="break_all" align="right" width="80">'.number_format($line_floor_production,0).'</td>
								<td class="break_all" align="right" width="80">'. number_format(($line_floor_production-$eff_target_floor),0).'</td>
								<td class="break_all" align="right" width="100">'. number_format($floor_avale_minute,0).'</td>
								<td class="break_all" align="right" width="100">'. number_format($floor_produc_min,0).'</td>
								<td class="break_all" align="right" width="90">'. number_format(($line_floor_production/$eff_target_floor)*100,2).'%</td>';
								if($floor_efficency<=$txt_parcentage)
								{
									$floor_html.='<td class="break_all" align="right" width="90" bgcolor="red">'.number_format($floor_efficency,2).'%</td>';
								}
								else
								{
									$floor_html.='<td class="break_all" align="right" width="90" >'.number_format($floor_efficency,2).'%</td>';
								}
								for($k=$hour; $k<=$last_hour; $k++)
								{
									$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
									if($start_hour_arr[$k]==$global_start_lanch)
									{
										$floor_html.='<td class="break_all" align="right" width="50" style=" background-color:#FFFF66 ">'. $floor_production[$prod_hour].'</td>';
									}
									else
									{
										$floor_html.='<td class="break_all" align="right" width="50" >'. $floor_production[$prod_hour].'</td>';
									}
								}
					$floor_html.='</tr></tbody>';
					$smv_for_item="";
				?>

	<fieldset style="width:2530px">
       <table width="2200" cellpadding="0" cellspacing="0">
            <tr class="form_caption">
                <td colspan="26" align="center"><strong><? echo $report_title; ?> &nbsp;</strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="26" align="center"><strong><? echo $companyArr[$comapny_id]; ?></strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="26" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date)) ); ?></strong></td>
            </tr>
        </table>
        <br />
        <table  width="600" cellpadding="0"  cellspacing="0" align="center" style="padding-left:200px">
            <tr>


                <td bgcolor="#FFFF66" height="18" width="30" ></td>
                <td> &nbsp;Lunch Hour</td>
                <td bgcolor="red" height="18" width="30"></td>
                <td> &nbsp;Efficiency % less than Standard And Production less than Target</td>


            </tr>
        </table>
        <label> <strong>Report Summary:-</strong></label>
          <table id="table_header_2" class="rpt_table" width="1940" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr height="50">
                    <th class="break_all" width="40">SL</th>
                    <th class="break_all" width="80">Floor Name</th>
                    <th class="break_all" width="70">Hourly Target</th>
                    <th class="break_all" width="70">Capacity</th>
                    <th class="break_all" width="60">Total Man Power</th>
                    <th class="break_all" width="70">Operator</th>
                    <th class="break_all" width="50">Helper</th>
                    <th class="break_all" width="60">Line Hour</th>
                    <th class="break_all" width="80">Day Target</th>
                    <th class="break_all" width="80">Total Prod.</th>
                    <th class="break_all" width="80">Variance </th>
                    <th class="break_all" width="100">SMV Available</th>
                    <th class="break_all" width="100">SMV Achieved</th>
                    <th class="break_all" width="90">Achievement %</th>
                    <th class="break_all" width="90">Floor Eff. %</th>

                	<?
                	for($k=$hour+1; $k<=$last_hour+1; $k++)
					{
					?>
                    <th class="break_all" width="50" style="vertical-align:middle"><div class="block_div"><?  echo substr($start_hour_arr[$k],0,5);   ?></div></th>
					<?
					}
                	?>
                </tr>
            </thead>
        </table>
        <div style="width:1960px; max-height:400px; overflow-y:scroll" id="scroll_body">
           <table class="rpt_table" width="1940" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
           <?  echo $floor_html; ?>
            <tfoot>
                   <tr>
                        <th class="break_all" width="40"></th>
                        <th class="break_all" width="80">Total </th>
                        <th class="break_all" width="70"><? echo number_format($gnd_total_tgt_h,0);   ?> </th>
                        <th class="break_all" width="70" align="right"><? echo number_format($total_capacity,0); ?> </th>
                        <th class="break_all" width="60"><? echo number_format($total_man_power,0); ?></th>
                        <th class="break_all" width="70"><? echo number_format($total_operator,0); ?></th>
                        <th class="break_all" width="50"><? echo number_format($total_helper,0); ?></th>
                        <th class="break_all" align="right" width="60"><? echo number_format($total_working_hour,0); ?></th>
                        <th class="break_all" align="right" width="80"><? echo number_format($total_terget,0); ?></th>
                        <th class="break_all" align="right" width="80"><? echo number_format($line_total_production,0); ?></th>
                        <th class="break_all" align="right" width="80"><? echo number_format($line_total_production-$total_terget,0); ?></th>
                        <th class="break_all" align="right" width="100"><? echo number_format($gnd_avable_min,0); ?></th>
                        <th class="break_all" align="right" width="100"><? echo number_format($gnd_product_min,0); ?></th>
                        <th class="break_all" align="right" width="90"><?  echo number_format(($line_total_production/$total_terget)*100,2)."%"; ?></th>
                        <th class="break_all" align="center" width="90"><? echo number_format(($gnd_product_min/$gnd_avable_min)*100,2)."%"; ?></th>
                        <?
						for($k=$hour; $k<=$last_hour; $k++)
						{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
						?>
						  <th class="break_all" width="50" ><?  echo $total_production[$prod_hour];   ?></th>
						<?
						}
                		?>
                   </tr>
               </tfoot>

          </table>

        </div>
    </br><br/>
    <? $smv_source_arr = array(1=>'Order Entry',2=>'Pre Costing',3=>'GSD Entry');?>
        <table id="table_header_1" class="rpt_table" width="3300" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr height="50">
                    <th class="break_all" width="40">SL</th>
                    <th class="break_all" width="80">Floor Name</th>
                    <th class="break_all" width="80">Line No</th>
                    <th class="break_all" width="80">Buyer</th>
                    <th class="break_all" width="100">Job</th>
                    <th class="break_all" width="140">Order No</th>
                    <th class="break_all" width="100">Style Ref.</th>
                    <th class="break_all" width="80">File No</th>
                    <th class="break_all" width="80">Ref. No</th>
                    <th class="break_all" width="120">Garments Item</th>
                    <th class="break_all" width="60" title="SMV Source From [<?= $smv_source_arr[$smv_source];?>]">SMV</th>
                    <th class="break_all" width="70">Operator</th>
                    <th class="break_all" width="50">Helper</th>
                    <th class="break_all" width="60"> Man Power</th>
                    <th class="break_all" width="70">Hourly Target (Pcs)</th>
                    <th class="break_all" width="60">Days Run</th>
                    <th class="break_all" width="60">Active Prod.Days</th>
                    <th class="break_all" width="70">Capacity</th>
                    <th class="break_all" width="60">Working Hour</th>
                    <th class="break_all" width="60">Current Hour</th>
                    <th class="break_all" width="60">As On Current Hour Target (Pcs)</th>
                    <th class="break_all" width="80">Total Target</th>
                    <th class="break_all" width="80">Total Prod.</th>
                    <th class="break_all" width="80">As On Current Hour Prod.Variance</th>
                    <th class="break_all" width="80">Total Variance (Pcs)</th>
                    <th class="break_all" width="100">Available Minutes</th>
                    <th class="break_all" width="100">Produce Minutes</th>
                    <th class="break_all" width="60">Target Hit rate</th>
                    <th class="break_all" width="90">Line Effi %</th>
                    <th class="break_all" width="70">FOB RATE(Avg)</th>
                    <th class="break_all" width="70">Hourly Target Value</th>
                    <th class="break_all" width="70">FOB Val.</th>
                   <?

                	for($k=$hour+1; $k<=$last_hour+1; $k++)
					{
					?>
                      <th class="break_all" width="50" style="vertical-align:middle"><div class="block_div"><?  echo substr($start_hour_arr[$k],0,5);   ?></div></th>
					<?
					}
                ?>
                </tr>
            </thead>
        </table>
        <div style="width:3300px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="3300" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
              <? echo $html;  ?>
                <tfoot>
                   <tr>
                        <th class="break_all" width="40">&nbsp;</th>
                        <th class="break_all" width="80">&nbsp;</th>
                        <th class="break_all" width="80">&nbsp;</th>
                        <th class="break_all" width="80">&nbsp;</th>
                        <th class="break_all" width="100">&nbsp;</th>
                        <th class="break_all" width="140">&nbsp;</th>
                        <th class="break_all" width="100">&nbsp;</th>
                        <th class="break_all" width="80">&nbsp;</th>
                        <th class="break_all" width="80">&nbsp;</th>
                        <th class="break_all" width="120">Total</th>
                        <th class="break_all" align="right" width="60"><? // echo number_format($total_smv/($i-1),2); ?>&nbsp;</th>
                        <th class="break_all" align="right" width="70"><? echo number_format($total_operator,0); ?></th>
                        <th class="break_all" align="right" width="50"><? echo number_format($total_helper,0); ?></th>
                        <th class="break_all" align="right" width="60"><? echo number_format($total_man_power,0); ?></th>
                        <th class="break_all" align="right" width="70"><?  echo number_format($gnd_total_tgt_h,0); ?></th>
                        <th class="break_all" align="right" width="60"><? //echo $prod_resource_array[$row[csf('sewing_line')]][$pr_date]['man_power']; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="60">&nbsp;</th>
                        <th class="break_all" align="right" width="70"><? echo number_format($total_capacity,0); ?></th>
                        <th class="break_all" align="right" width="60"><? echo number_format($total_working_hour,0); ?></th>
                        <th class="break_all" align="right" width="60">&nbsp;</th>
                        <th class="break_all" align="right" width="60">&nbsp;</th>
                        <th class="break_all" align="right" width="80"><? echo number_format($total_terget,0); ?></th>
                        <th class="break_all" align="right" width="80"><? echo number_format($line_total_production,0); ?></th>
                        <th class="break_all" align="right" width="80">&nbsp;</th>
                        <th class="break_all" align="right" width="80"><? echo number_format($line_total_production-$total_terget,0); ?></th>
                        <th class="break_all" align="right" width="100"><? echo number_format($gnd_avable_min,0); ?></th>
                        <th class="break_all" align="right" width="100"><? echo number_format($gnd_product_min,0); ?></th>
                        <th class="break_all" align="right" width="60"><? echo number_format(($line_total_production/$total_terget)*100,2)."%"; ?></th>
                        <th class="break_all" align="right" width="90" ><? echo number_format(($gnd_product_min/$gnd_avable_min)*100,2)."%";?></th>
						<th class="break_all" align="right" width="70"><? echo number_format($gnd_total_fob_final_rate_val,2);?></th>
						<th class="break_all" align="right" width="70"><?$gnd_final_hourly_val+=$hourly_traget; echo number_format($gnd_final_hourly_val,2);?></th>
                        <th class="break_all" align="right" width="70"><? echo number_format($gnd_final_total_fob_val,2);?></th>
					    <?
						for($k=$hour; $k<=$last_hour; $k++)
						{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
						?>
						<th class="break_all" align="right" width="50"><? echo $total_production[$prod_hour]; ?></th>
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
			    $sql_active_line=sql_select("select sewing_line,sum(production_quantity) as total_production from pro_garments_production_mst  where production_date=".$txt_date." and production_type=5 and status_active=1 and is_deleted=0 and prod_reso_allo=1 group by  sewing_line");

				//$actual_line_arr=array();
				foreach($sql_active_line as $inf)
				{

				   if(str_replace("","",$inf[csf('sewing_line')])!="")
				   {
						if(str_replace("","",$actual_line_arr)!="") $actual_line_arr.=",";
					    $actual_line_arr.="'".$inf[csf('sewing_line')]."'";
				   }
				}

				$sql_active_line=sql_select("SELECT line_id as sewing_line,sum(production_qnty) as total_production from subcon_gmts_prod_dtls  where production_date=".$txt_date." and production_type=2 and status_active=1 and is_deleted=0 and prod_reso_allo=1 group by  line_id");

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
			if($db_type==0) $remarks_cond="group_concat(d.remarks)";
			else if($db_type==2) $remarks_cond="LISTAGG(cast(d.remarks as varchar2(4000)), ',') WITHIN GROUP (ORDER BY d.remarks)";

			//echo "select a.id, a.floor_id, a.line_number, b.man_power, b.operator, b.helper, b.working_hour, $remarks_cond as remarks from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and b.pr_date=".$txt_date." and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 $line_cond group by a.id, a.floor_id, a.line_number, b.man_power, b.operator, b.helper, b.working_hour";
			 $dataArray=sql_select("select a.id, a.floor_id, a.line_number, b.man_power, b.operator, b.helper, b.working_hour, $remarks_cond as remarks from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and b.pr_date=".$txt_date." and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 $line_cond group by a.id, a.floor_id, a.line_number, b.man_power, b.operator, b.helper, b.working_hour order by a.floor_id");
					$l=1; $location_array=array(); $floor_array=array();
					foreach( $dataArray as $row )
					{
						if ($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$remarks="";
						$remarks=implode(",",array_unique(explode(",",$row[csf('remarks')])));
						$sewing_lines='';
						$line_numbers=array_unique(explode(",",$row[csf('line_number')]));
						//print_r($line_number);
						foreach($line_numbers as $val)
						{
							if($sewing_lines=='') $sewing_lines=$lineArr[$val]; else $sewing_lines.=",".$lineArr[$val];
						}
					?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tbltr_<? echo $l; ?>','<? echo $bgcolor; ?>')" id="tbltr_<? echo $l; ?>">
                        	<td width="40"><? echo $l; ?></td>
                            <td width="100"><p><? echo $sewing_lines; ?>&nbsp;</p></td>
                            <td width="100" align="right"><p>&nbsp;<? echo $floorArr[$row[csf('floor_id')]]; ?></p></td>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('man_power')]; ?></th>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('operator')]; ?></th>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('helper')]; ?></td>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('working_hour')]; ?></td>
                            <td width="380"><? echo $remarks; ?>&nbsp;</td>
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

} // 2nd Button End

if($action=="report_generate3") //3rd Button Start...
{

	?>
	<style type="text/css">
        .block_div
        {
            width:auto;
            height:auto;
            text-wrap:normal;
            vertical-align:bottom;
            display: block;
            position: !important;
            -webkit-transform: rotate(-90deg);
            -moz-transform: rotate(-90deg);
        }
       .break_all
            {
            	word-wrap: break-word;
            	word-break: break-all;
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
	$group_library=return_library_array( "select id,sewing_group from lib_sewing_line ", "id", "sewing_group"  );
	$groupArr = return_library_array("select id,sewing_group from lib_sewing_line order by id","id","sewing_group");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$comapny_id=str_replace("'","",$cbo_company_id);
    $today_date=date("Y-m-d");
	$txt_producting_day="".str_replace("'","",$txt_date)."";
	//***************************************************************************************************************************
	$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
	order by sewing_line_serial");
	foreach($lineDataArr as $lRow)
	{
		$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
		$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
		$lastSlNo=$lRow[csf('sewing_line_serial')];
	}

	if($db_type==2)
	{
		// $prod_reso_arr=return_library_array( "select a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  REGEXP_SUBSTR( a.line_number, '[^,]+', 1)=b.id order by b.floor_name, b.sewing_line_serial","id","line_name");
		$prod_reso_group=return_library_array( "select a.line_number, b.sewing_group from prod_resource_mst a, lib_sewing_line b where  REGEXP_SUBSTR( a.line_number, '[^,]+', 1)=b.id order by b.floor_name, b.sewing_line_serial","line_number","sewing_group");
	}
	else if($db_type==0)
	{
		// $prod_reso_arr=return_library_array( "select a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  SUBSTRING_INDEX( a.line_number, ' , ', 1)=b.id order by b.floor_name, b.sewing_line_serial","id","line_name");
		$prod_reso_group=return_library_array( "select a.line_number, b.sewing_group from prod_resource_mst a, lib_sewing_line b where  SUBSTRING_INDEX( a.line_number, ' , ', 1)=b.id order by b.floor_name, b.sewing_line_serial","line_number","sewing_group");
		$prod_reso_group2=return_library_array( "select a.id, b.sewing_group from prod_resource_mst a, lib_sewing_line b where  SUBSTRING_INDEX( a.line_number, ' , ', 1)=b.id order by b.floor_name, b.sewing_line_serial","id","sewing_group");
	}

	$sewing_group = str_replace("'","",$cbo_sewing_group);
	$sewing_group=str_replace(" ","",$sewing_group);
	$group_name = "";
	$line_name = "";
	$group_cond ="";
	if($sewing_group!='0')
	{
		$group_sql = "SELECT id from lib_sewing_line where sewing_group=$cbo_sewing_group and status_active =1 and is_deleted=0";
		$group_sql_res = sql_select($group_sql);
		foreach ($group_sql_res as $val)
		{
			if($line_name == "")
			{
				$line_name = $val[csf('id')];
				$group_name .= "'$line_name'";
			}
			else
			{
				$line_name = $val[csf('id')];
				$group_name .= ","."'$line_name'";
			}
		}
		$group_cond = " and a.sewing_line in($group_name)";
	}
	// echo $group_cond;

	if($db_type==0)
	{
		$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$comapny_id and shift_id=1 and pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
	}
	else
	{
	$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$comapny_id and shift_id=1 and pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
	}

	if($min_shif_start=="")
	{
		echo "<p style='font-size:20px', align='center'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<p/>";die;

	}


	//==============================shift time===================================================================================================
	$start_time_arr=array();
	if($db_type==0)
	{
		$start_time_data_arr=sql_select("select company_name, shift_id, TIME_FORMAT( prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( lunch_start_time, '%H:%i' ) as lunch_start_time from variable_settings_production where company_name in($comapny_id) and shift_id=1  and variable_list=26 and status_active=1 and is_deleted=0");
	}
	else
	{
		$start_time_data_arr=sql_select("select company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time,TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($comapny_id) and  shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");
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
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_id and variable_list=23 and is_deleted=0
	and status_active=1");

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
	$cbo_no_prod_type=str_replace("'","",$cbo_no_prod_type);
	$file_no=str_replace("'","",$txt_file_no);
	$ref_no=str_replace("'","",$txt_ref_no);
	if($file_no!="") $file_cond="and c.file_no=$file_no";else $file_cond="";
	if($ref_no!="") $ref_cond="and c.grouping='$ref_no'";else $ref_cond="";
	//echo $file_cond;

	if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date=$txt_date";
	// echo $txt_date_from; die;

	if($prod_reso_allo[0]==1)
	{
		$prod_resource_array=array();

		$dataArray_sql=sql_select("select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id=$comapny_id and b.pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0");
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
		//var_dump($prod_resource_array);
		if(str_replace("'","",trim($txt_date))==""){$pr_date_con="";}else{$pr_date_con=" and b.pr_date=$txt_date";}

		if($db_type==0)
		{
			$dataArray=sql_select("select a.id,b.pr_date,d.shift_id,TIME_FORMAT( d.prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( d.lunch_start_time, '%H:%i' ) as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 $pr_date_con");
		}
		else
		{
			$dataArray=sql_select("select a.id,b.pr_date,d.shift_id,TO_CHAR(d.prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR( d.lunch_start_time,'HH24:MI') as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 $pr_date_con");
		}

		$line_number_arr=array();
		foreach($dataArray as $val)
		{
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['shift_id']=$val[csf('shift_id')];
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['prod_start_time']=$val[csf('prod_start_time')];
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['lunch_start_time']=$val[csf('lunch_start_time')];
		}
	}
 //*********************************************************************************************************************************
  	if($db_type==0)
	{
		$manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
	}
	else
	{
		$manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
	}


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

   	//$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($manufacturing_company) and variable_list=25 and   status_active=1 and is_deleted=0");

   	// $smv_source=return_field_value("smv_source","variable_settings_production","company_name =$comapny_id and variable_list=25 and   status_active=1 and is_deleted=0");

    $name_iso_Array=sql_select( "select iso_no from lib_iso where company_id=$comapny_id and status_active=1 and module_id=7 and menu_id=420");



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
		b.buyer_name  as buyer_name,b.style_ref_no,a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.unit_price,c.file_no,c.grouping as ref,sum(a.production_quantity) as good_qnty,";
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
		$sql.="sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23 from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a left join lib_prod_floor d on a.floor_id=d.id and d.is_deleted=0 left join lib_sewing_line e on a.sewing_line=e.id and e.is_deleted=0  where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $company_name $location $floor $line $group_cond $buyer_id_cond  $txt_date_from $file_cond $ref_cond group by a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,e.sewing_line_serial,b.buyer_name,a.item_number_id,c.po_number,c.file_no,c.unit_price,c.grouping,b.style_ref_no order by a.location, a.floor_id,e.sewing_line_serial,a.prod_reso_allo";
	}
	else if($db_type==2)
	{
		$sql="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name  as buyer_name,b.style_ref_no, a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.file_no,c.unit_price,c.grouping as ref,sum(d.production_qnty) as good_qnty,";
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
		FROM  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a ,pro_garments_production_dtls d
		WHERE a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_name $location $floor $line $group_cond $buyer_id_cond  $txt_date_from $file_cond $ref_cond
		GROUP BY a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name,b.style_ref_no,a.item_number_id,c.po_number,c.unit_price,c.file_no,c.grouping
		ORDER BY a.location,a.floor_id,a.sewing_line";
	}
	// echo $sql;die;
	$sql_resqlt=sql_select($sql);
	$production_data_arr=array();
	$production_po_data_arr=array();
	$production_serial_arr=array(); $reso_line_ids=''; $all_po_id="";
	$active_days_arr=array();
	$duplicate_date_arr=array();
	foreach($sql_resqlt as $val)
	{

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
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_id'].=",".$val[csf('po_break_down_id')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style'].=",".$val[csf('style_ref_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['file'].=",".$val[csf('file_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['ref'].=",".$val[csf('ref')];
		}
	 	else
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']=$val[csf('po_number')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_id']=$val[csf('po_break_down_id')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style']=$val[csf('style_ref_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['file']=$val[csf('file_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['ref']=$val[csf('ref')];
		}
		$fob_rate_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['rate']=$val[csf('unit_price')];

		if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']!="")
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')];
		}
		else
		{
			 $production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')];
		}
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['quantity']+=$val[csf('good_qnty')];
		$production_data_arr_qty[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]['quantity']+=$val[csf('good_qnty')];

		if($all_po_id=="") $all_po_id=$val[csf('po_break_down_id')]; else $all_po_id.=",".$val[csf('po_break_down_id')];
		$all_style_arr[$val[csf('style_ref_no')]] = $val[csf('style_ref_no')];
		$style_wise_po_arr[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
	}
	//print_r($production_data_arr_qty);
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
				$poIds_cond3.=" c.po_break_down_id  in($ids) or ";
			}
			$poIds_cond=chop($poIds_cond,'or ');
			$poIds_cond2=chop($poIds_cond,'or ');
			$poIds_cond3=chop($poIds_cond,'or ');
			$poIds_cond.=")";
			$poIds_cond2.=")";
			$poIds_cond3.=")";
		}
		else
		{
			$poIds_cond=" and  b.id  in($all_po_id)";
			$poIds_cond2=" and  c.id  in($all_po_id)";
			$poIds_cond3=" and  c.id  in($all_po_id)";
		}
	}



	$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($comapny_id) and variable_list=25 and   status_active=1 and is_deleted=0");
	// echo $smv_source;
    if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;

    if($smv_source==3)
	{
		// $sql_item="SELECT b.id, a.sam_style, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 	and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$style_nos="'".implode("','",$all_style_arr)."'";

		$style_cond = where_con_using_array($all_style_arr,1,"a.style_ref");
		$sql_item="SELECT a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID  from PPL_GSD_ENTRY_MST a where a.APPLICABLE_PERIOD <= $txt_date and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and a.BULLETIN_TYPE=4 $style_cond  group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID ORDER BY a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC";
		// echo $sql_item;die;
		$resultItem=sql_select($sql_item);

		/* foreach($resultItem as $itemData)
		{
			$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
		} */
		foreach($resultItem as $rows)
		{
			foreach($style_wise_po_arr[$rows[STYLE_REF]] as $po_id)
			{
				if($item_smv_array[$po_id][$rows[GMTS_ITEM_ID]]=='')
				{
					$item_smv_array[$po_id][$rows[GMTS_ITEM_ID]]=$rows[TOTAL_SMV];
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


    $po_active_sql="SELECT a.floor_id,a.sewing_line,a.production_date,a.po_break_down_id,a.item_number_id from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $company_name $location $floor $line $group_cond $buyer_id_cond   $file_cond $ref_cond $poIds_cond2 group by  a.floor_id,a.sewing_line,a.production_date ,a.po_break_down_id,a.item_number_id";
    //echo $po_active_sql;die;
	foreach(sql_select($po_active_sql) as $vals)
	{
		$prod_dates=$vals[csf('production_date')];
		if($duplicate_date_arr[$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]][$prod_dates]=="")
		{
			$active_days_arr[$vals[csf('floor_id')]][$vals[csf('sewing_line')]]+=1;
			$active_days_arr_powise[$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]]+=1;
			$duplicate_date_arr[$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]][$prod_dates]=$prod_dates;
		}

	}
	//print_r($duplicate_date_arr);

	$sql_item_rate="SELECT c.po_break_down_id, c.item_number_id, c.order_quantity, c.order_total from wo_po_color_size_breakdown c where  c.is_deleted=0 and c.status_active=1 $poIds_cond3";
	$resultRate=sql_select($sql_item_rate);
	$item_po_array=array();
	foreach($resultRate as $row)
	{
		$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['qty']+=$row[csf('order_quantity')];
		$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['amt']+=$row[csf('order_total')];
	}

	// subcoutact data **********************************************************************************************************************

    if($db_type==0)
    {
		$sql_sub_contuct= "select  a.company_id, a.location_id,d.floor_serial_no,e.sewing_line_serial,a.prod_reso_allo, a.floor_id, a.production_date, a.line_id,b.party_id  as buyer_name,a.order_id,c.order_no as po_number,c.cust_style_ref,max(c.smv) as smv,sum(a.production_qnty) as good_qnty,";

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
   		$sql_sub_contuct.="sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.id and d.is_deleted=0 left join lib_sewing_line e on a.line_id=e.id and e.is_deleted=0 where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id=$comapny_id $subcon_location $floor $subcon_line   $txt_date_from group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date,a.prod_reso_allo,a.line_id,b.party_id,c.order_no,c.cust_style_ref,e.sewing_line_serial order by a.location_id,d.floor_serial_no,e.sewing_line_serial,a.prod_reso_allo";
	}
	else
	{
		$sql_sub_contuct= "select  a.company_id, a.location_id, a.floor_id,d.floor_serial_no,e.sewing_line_serial, a.production_date, a.line_id,b.party_id  as buyer_name,a.prod_reso_allo,a.order_id,c.order_no as po_number,c.cust_style_ref,max(c.smv) as smv,a.prod_reso_allo,sum(a.production_qnty) as good_qnty,";
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

	   	$sql_sub_contuct.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]'	and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.id  and d.is_deleted=0 left join lib_sewing_line e on a.line_id=e.id and e.is_deleted=0 where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id=$comapny_id $subcon_location $floor $subcon_line   $txt_date_from group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date, a.line_id,b.party_id,c.order_no,c.cust_style_ref,e.sewing_line_serial,a.prod_reso_allo order by a.location_id, a.floor_id,e.sewing_line_serial,a.prod_reso_allo";

	}
	//echo $sql_sub_contuct;die;
	$sub_result=sql_select($sql_sub_contuct);
	$subcon_order_smv=array();
	foreach($sub_result as $subcon_val)
	{

		if($val[csf('prod_reso_allo')]==1)
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
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['style'].=",".$subcon_val[csf('cust_style_ref')];
		}
		else
		{
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number']=$subcon_val[csf('po_number')];
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['style']=$subcon_val[csf('cust_style_ref')];
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
	//For Summary Report New Add No Prodcut
	if($cbo_no_prod_type==1)
	{
		//No Production line Start ....
		$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
		$sql_active_line=sql_select("select sewing_line,sum(production_quantity) as total_production from pro_garments_production_mst  where production_date=".$txt_date." and production_type=5 and status_active=1 and is_deleted=0 and prod_reso_allo=1 group by  sewing_line");
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
		$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$comapny_id and variable_list=23 and is_deleted=0 and status_active=1");
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

		 $dataArray_sum=sql_select("SELECT a.id, a.floor_id,1 as prod_reso_allo,2 as type_line, a.line_number as line_no, b.man_power, b.operator, b.helper, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type,d.prod_start_time from  prod_resource_dtls b,prod_resource_dtls_time d,prod_resource_mst a  where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and b.pr_date=".$txt_date." and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 and a.id not in($res_line_cond) and a.line_number like '%$lin_ids%'  $location  $floor  group by a.id, a.floor_id, a.line_number, d.prod_start_time,b.man_power, b.operator, b.helper, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type order by a.floor_id");
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

			 $production_serial_arr[$row[csf('floor_id')]][$slNo][$row[csf('id')]]=$row[csf('id')];
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
			 $production_data_arr[$row[csf('floor_id')]][$row[csf('id')]]['group_name']=$row[csf('line_no')];
		 }
		 $dataArray_sql_cap=sql_select("select  a.floor_id, a.line_number as line_no,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id  and a.company_id=$comapny_id and b.pr_date=".$txt_date."  and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  group by a.id,a.line_number, a.floor_id, a.line_number, b.man_power, b.operator,c.capacity, b.helper, b.working_hour,b.target_per_hour order by a.floor_id");

		 //$prod_resource_array_summary=array();
		 foreach( $dataArray_sql_cap as $row)
		 {
			 $production_data_arr[$row[csf('floor_id')]][$row[csf('line_no')]]['capacity']=$row[csf('capacity')];
		 }

	} //End

	//echo "<pre>";
	//var_dump($production_data_arr);die;
    $avable_min=0;
	$today_product=0;
    $floor_name="";
    $floor_man_power=0;
	$floor_operator=$floor_produc_min=0;
	$floor_smv=$floor_row=$floor_helper=$floor_tgt_h=$floor_days_run=$floor_working_hour=$line_floor_production=$floor_today_product=$floor_avale_minute=0;
	$total_operator=$total_helper=$gnd_hit_rate=0;
    $total_smv=$total_terget=$grand_total_product=$gnd_line_effi=0;
    $total_man_power=$gnd_avable_min=$gnd_product_min=0;
	$item_smv=$item_smv_total=$line_efficiency=$days_run=$days_active=$total_working_hour=$gnd_total_tgt_h=$total_capacity=0;
	$j=1;
	ob_start();
	$line_number_check_arr=array();
	$smv_for_item="";
	$total_production=array();
	$floor_production=array();
    $line_floor_production=0;
    $line_total_production=0; $gnd_total_fob_val=0; $gnd_final_total_fob_val=0;
	foreach($production_serial_arr as $f_id=>$fname)
	{
		ksort($fname);
		foreach($fname as $sl=>$s_data)
		{

			foreach($s_data as $l_id=>$ldata)
			{
			  $po_value=$production_data_arr[$f_id][$ldata]['po_number'];
			  if($po_value)
			  {

				//}

				if($i!=1)
				{
					if(!in_array($f_id, $check_arr))
					{
						if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						 $html.='<tr  bgcolor="#B6B6B6">
							<td class=".break_all" width="40">&nbsp;</td>
							<td class="break_all" width="80">&nbsp;</td>
							<td class="break_all" width="80">&nbsp;</td>
							<td class="break_all" width="80">&nbsp;</td>
							<td class="break_all" width="80">&nbsp;</td>
							<td class="break_all" width="140">&nbsp;</td>
							<td class="break_all" width="100">&nbsp;</td>
							<td class="break_all" width="80">&nbsp;</td>
							<td class="break_all" width="80">&nbsp;</td>
							<td class="break_all" width="80">&nbsp;</td>
							<td class="break_all" align="right" width="60">&nbsp;</td>
							<td class="break_all" align="right" width="70">'.$floor_operator.'</td>
							<td class="break_all" align="right" width="50">'. $floor_helper.'</td>
							<td class="break_all" align="right" width="60">'. $floor_man_power.'</td>
							<td class="break_all" align="right" width="70">'. $floor_tgt_h.'</td>
							<td class="break_all" align="right" width="60">'. $floor_days_run.'</td>
							<td class="break_all" align="right" width="60">'. $floor_days_active.'</td>
							<td class="break_all" align="right" width="70">'.$floor_capacity.'</td>
							<td class="break_all" align="right" width="60">'. $floor_working_hour.'</td>
							<td class="break_all" align="right" width="60">&nbsp;</td>
							<td class="break_all" align="right" width="60">&nbsp;</td>
							<td class="break_all" align="right" width="80">'.$eff_target_floor.'</td>
							<td class="break_all" align="right" width="80">'.$line_floor_production.'</td>
							<td class="break_all" align="right" width="80">&nbsp;</td>
							<td class="break_all" align="right" width="80">'. ($line_floor_production-$eff_target_floor).';</td>
							<td class="break_all" align="right" width="100">'. number_format($floor_avale_minute,0).'</td>
							<td class="break_all" align="right" width="100">'. number_format($floor_produc_min,0).'</td>
							<td class="break_all" align="right" width="60">'.number_format(($line_floor_production/$eff_target_floor)*100,2).'%</td>
							<td class="break_all" align="right" width="90">'.number_format($floor_efficency,2).' %</td>
							<td class="break_all" align="right" width="70">'.number_format($gnd_total_fob_val,2).'</td>';

							$gnd_total_fob_val=0;
							for($k=$hour; $k<=$last_hour; $k++)
							{
								$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";


								if($start_hour_arr[$k]==$global_start_lanch)
								{
									$bg_color='background:yellow';
								}
								if($floor_tgt_h>$floor_production[$prod_hour])
								{
									$bg_color='background:red';
									if($floor_production[$prod_hour]==0)
									{
										$bg_color='';
									}
								}
								else
								{
									$bg_color='';
								}


								$html.='<td class="break_all" align="right" width="50" style='.$bg_color.' >'. $floor_production[$prod_hour].'</td>';
							}
							$html.='</tr>';
							$floor_html.='<tbody>';
							$floor_html.="<tr bgcolor='$bgcolor' onclick=change_color('floor_$j','$bgcolor') id=floor_$j>";
							$floor_html.='<td class="break_all" width="40">'.$j.'&nbsp;</td>
							<td class="break_all" width="80" align="center">'.$floor_name.'&nbsp; </td>
							<td class="break_all" width="70" align="right">'. $floor_tgt_h.'</td>
							<td class="break_all" width="70" align="right">'.$floor_capacity.'</td>
							<td class="break_all" align="right" width="60">'. $floor_man_power.'</td>
							<td class="break_all" width="70" align="right">'.$floor_operator.'</td>
							<td class="break_all" width="50" align="right">'. $floor_helper.'</td>
							<td class="break_all" align="right" width="60">'. $floor_working_hour.'</td>
							<td class="break_all" align="right" width="80">'. $eff_target_floor.'</td>
							<td class="break_all" align="right" width="80">'.$line_floor_production.'</td>
							<td class="break_all" align="right" width="80">'. ($line_floor_production-$eff_target_floor).'</td>
							<td class="break_all" align="right" width="100">'. $floor_avale_minute.'</td>
							<td class="break_all" align="right" width="100">'. number_format($floor_produc_min,0).'</td>
							<td class="break_all" align="right" width="90">'. number_format(($line_floor_production/$eff_target_floor)*100,2).'%</td>';
							if($floor_efficency<=$txt_parcentage)
							{
								$floor_html.='<td class="break_all" align="right" width="90" bgcolor="red">'.number_format($floor_efficency,2).' %</td>';
							}
							else
							{
								$floor_html.='<td class="break_all" align="right" width="90" >'.number_format($floor_efficency,2).' %</td>';
							}
							for($k=$hour; $k<=$last_hour; $k++)
							{
								$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
								if($start_hour_arr[$k]==$global_start_lanch)
								{
									$floor_html.='<td class="break_all" align="right" width="50" style=" background-color:#FFFF66" >'. $floor_production[$prod_hour].'</td>';
								}
								else
								{
									$floor_html.='<td class="break_all" align="right" width="50" >'. $floor_production[$prod_hour].'</td>';
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
				//$item_ids=$production_data_arr[$f_id][$ldata]['item_number_id'];
				$germents_item=array_unique(explode('****',$production_data_arr[$f_id][$ldata]['item_number_id']));

				$buyer_neme_all=array_unique(explode(',',$production_data_arr[$f_id][$ldata]['buyer_name']));
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
				$tot_po_qty=0;$fob_val=0;
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
					$produce_minit+=$production_po_data_arr[$f_id][$l_id][$po_garment_item[0]]*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
					$fob_rate=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt']/$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];
					$prod_qty=$production_data_arr_qty[$f_id][$l_id][$po_garment_item[0]][$po_garment_item[1]]['quantity'];
					//echo $prod_qty.'<br>';
					if(is_nan($fob_rate)){ $fob_rate=0; }
					$fob_val+=$prod_qty*$fob_rate;
				}
				//$fob_rate=$tot_po_amt/$tot_po_qty;

				$subcon_po_id=array_unique(explode(',',$production_data_arr[$f_id][$ldata]['order_id']));
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
					$days_run=datediff("d",$sewing_day,$pr_date);
					}
					else  $days_run=0;
				}
				$type_line=$production_data_arr[$f_id][$ldata]['type_line'];
				$prod_reso_allo=$production_data_arr[$f_id][$ldata]['prod_reso_allo'];

				$sewing_line='';
				$groupName="";

				$groupNameArr=array();
				if($production_data_arr[$f_id][$ldata]['prod_reso_allo']==1)
				{
					$line_number=explode(",",$prod_reso_arr[$ldata]);
					foreach($line_number as $val)
					{
						if($sewing_line=='') {$sewing_line=$lineArr[$val];} else {$sewing_line.=",".$lineArr[$val];}
						if($groupName=='') {$groupName=$groupArr[$val];} else {$groupName.=",".$groupArr[$val];}
					}

				}
				else {$sewing_line=$lineArr[$ldata];$groupName=$groupArr[$ldata];}




				$lunch_start="";
				$lunch_start=$line_number_arr[$ldata][$pr_date]['lunch_start_time'];
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
				for($h=$hour;$h<=$last_hour;$h++)
				{
					 $prod_hour="prod_hour".substr($line_start_hour_arr[$h],0,2)."";
					 $production_hour[$prod_hour]=$production_data_arr[$f_id][$ldata][$prod_hour];
					 $floor_production[$prod_hour]+=$production_data_arr[$f_id][$ldata][$prod_hour];
					 $total_production[$prod_hour]+=$production_data_arr[$f_id][$ldata][$prod_hour];
				}

 				$floor_production['prod_hour24']+=$production_data_arr[$f_id][$ldata]['prod_hour23'];
				$total_production['prod_hour24']+=$production_data_arr[$f_id][$ldata]['prod_hour23'];
				$production_hour['prod_hour24']=$production_data_arr[$f_id][$ldata]['prod_hour23'];
				$line_production_hour=0;
				if(str_replace("'","",$actual_production_date)>str_replace("'","",$actual_date))
				{
					if($type_line==2) //No Profuction Line
					{
						$line_start=$production_data_arr[$f_id][$l_id]['prod_start_time'];
					}
					else
					{
						$line_start=$line_number_arr[$ldata][$pr_date]['prod_start_time'];
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
						$line_production_hour+=$production_data_arr[$f_id][$ldata][$line_hour];
						$line_floor_production+=$production_data_arr[$f_id][$ldata][$line_hour];
						$line_total_production+=$production_data_arr[$f_id][$ldata][$line_hour];
						$actual_time_hour=$start_hour_arr[$lh+1];
						}
					}
 					if($start_hour_arr[$actual_time]>$lunch_start_hour) $total_eff_hour=$total_eff_hour-1;

					if($type_line==2)
					{
						if($total_eff_hour>$production_data_arr[$f_id][$l_id]['working_hour'])
						{
							 $total_eff_hour=$production_data_arr[$f_id][$l_id]['working_hour'];
						}
					}
					else
					{
						if($total_eff_hour>$prod_resource_array[$ldata][$pr_date]['working_hour'])
						{
							$total_eff_hour=$prod_resource_array[$ldata][$pr_date]['working_hour'];
						}
					}

				}
				if(str_replace("'","",$actual_production_date)<=str_replace("'","",$actual_date))
				{
					for($ah=$hour;$ah<=$last_hour;$ah++)
					{
						$prod_hour="prod_hour".substr($start_hour_arr[$ah],0,2)."";
						$line_production_hour+=$production_data_arr[$f_id][$ldata][$prod_hour];
						$line_floor_production+=$production_data_arr[$f_id][$ldata][$prod_hour];
						$line_total_production+=$production_data_arr[$f_id][$ldata][$prod_hour];
					}
					if($type_line==2)
					{
						$total_eff_hour=$production_data_arr[$f_id][$l_id]['working_hour'];
					}
					else
					{
						$total_eff_hour=$prod_resource_array[$ldata][$pr_date]['working_hour'];
					}
				}

				if($sewing_day!="")
				{
					$days_run= $diff=datediff("d",$sewing_day,$pr_date);
					$days_active= $active_days_arr[$f_id][$l_id];
				}
				else
				{
					 $days_run=0;
					 $days_active=0;
				}

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
						$smv_adjustmet_type=$production_data_arr[$f_id][$l_id]['smv_adjust_type'];
						$eff_target=($production_data_arr[$f_id][$l_id]['terget_hour']*$total_eff_hour);

						if($total_eff_hour>=$production_data_arr[$f_id][$l_id]['working_hour'])
						{
							if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$production_data_arr[$f_id][$l_id]['smv_adjust'];
							if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($production_data_arr[$f_id][$l_id]['smv_adjust'])*(-1);
						}
						$efficiency_min+=$total_adjustment+($production_data_arr[$f_id][$l_id]['man_power'])*$cla_cur_time*60;
						$line_efficiency=(($produce_minit)*100)/$efficiency_min;
					}
					else
					{
						$smv_adjustmet_type=$prod_resource_array[$ldata][$pr_date]['smv_adjust_type'];
						$eff_target=($prod_resource_array[$ldata][$pr_date]['terget_hour']*$total_eff_hour);

						if($total_eff_hour>=$prod_resource_array[$ldata][$pr_date]['working_hour'])
						{
						if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array[$ldata][$pr_date]['smv_adjust'];
						if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array[$ldata][$pr_date]['smv_adjust'])*(-1);
						}

						/*$actual_hours=date("H",time())-$line_start_hour; for metro
						$cur_time=date("H",time());
						if($cur_time>$line_start_hour){
							$actual_hours=$actual_hours-1;
						}
						else
						{
							$actual_hours=$actual_hours	;
						}
						$total_eff_hour_custom=($actual_hours>$total_eff_hour)?$total_eff_hour:$actual_hours;

	  					$producting_day=strtotime("Y-m-d",$txt_producting_day);

	  					if($producting_day>$today_date)
						{

							$efficiency_min+=$total_adjustment+($prod_resource_array[$ldata][$pr_date]['man_power'])*$total_eff_hour*60;
							//echo $total_eff_hour."_";
						}
						else
						{
							$efficiency_min+=$total_adjustment+($prod_resource_array[$ldata][$pr_date]['man_power'])*$total_eff_hour_custom*60;
							//echo $total_eff_hour_custom."_";
						}*/




						$efficiency_min+=$total_adjustment+($prod_resource_array[$ldata][$pr_date]['man_power'])*$cla_cur_time*60;
						$line_efficiency=(($produce_minit)*100)/$efficiency_min;
					}




				if($type_line==2) //No Production Line
				{
					$man_power=$production_data_arr[$f_id][$l_id]['man_power'];
					$operator=$production_data_arr[$f_id][$l_id]['operator'];
					$helper=$production_data_arr[$f_id][$l_id]['helper'];
					$terget_hour=$production_data_arr[$f_id][$l_id]['target_hour'];
					$capacity=$production_data_arr[$f_id][$l_id]['capacity'];
					$working_hour=$production_data_arr[$f_id][$l_id]['working_hour'];

					$floor_working_hour+=$production_data_arr[$f_id][$l_id]['working_hour'];
					$eff_target_floor+=$eff_target;
					$floor_today_product+=$today_product;
					$floor_avale_minute+=$efficiency_min;
					$floor_produc_min+=$produce_minit;
					$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
					$floor_capacity+=$production_data_arr[$f_id][$l_id]['capacity'];
					$floor_helper+=$production_data_arr[$ldata][$pr_date]['helper'];
					$floor_man_power+=$production_data_arr[$f_id][$l_id]['man_power'];
					$floor_operator+=$production_data_arr[$f_id][$l_id]['operator'];
					$total_operator+=$production_data_arr[$f_id][$l_id]['operator'];
					$total_man_power+=$production_data_arr[$f_id][$l_id]['man_power'];
					$total_helper+=$production_data_arr[$f_id][$l_id]['helper'];
					$total_capacity+=$production_data_arr[$f_id][$l_id]['capacity'];
					$floor_tgt_h+=$production_data_arr[$f_id][$l_id]['target_hour'];
					$total_working_hour+=$production_data_arr[$f_id][$l_id]['working_hour'];
					$gnd_total_tgt_h+=$production_data_arr[$f_id][$l_id]['target_hour'];
					$total_terget+=$eff_target;
					$grand_total_product+=$today_product;
					$gnd_avable_min+=$efficiency_min;
					$gnd_product_min+=$produce_minit;

					$gnd_total_fob_val+=$fob_val;
					$gnd_final_total_fob_val+=$fob_val;
				}
				else
				{
					$man_power=$prod_resource_array[$ldata][$pr_date]['man_power'];
					$operator=$prod_resource_array[$ldata][$pr_date]['operator'];
					$helper=$prod_resource_array[$ldata][$pr_date]['helper'];
					$terget_hour=$prod_resource_array[$ldata][$pr_date]['terget_hour'];
					$capacity=$prod_resource_array[$ldata][$pr_date]['capacity'];
					$working_hour=$prod_resource_array[$ldata][$pr_date]['working_hour'];

					$floor_capacity+=$prod_resource_array[$ldata][$pr_date]['capacity'];
					$floor_man_power+=$prod_resource_array[$ldata][$pr_date]['man_power'];
					$floor_operator+=$prod_resource_array[$ldata][$pr_date]['operator'];
					$floor_helper+=$prod_resource_array[$ldata][$pr_date]['helper'];
					$floor_tgt_h+=$prod_resource_array[$ldata][$pr_date]['terget_hour'];
					$floor_working_hour+=$prod_resource_array[$ldata][$pr_date]['working_hour'];
					$eff_target_floor+=$eff_target;
					$floor_today_product+=$today_product;
					$floor_avale_minute+=$efficiency_min;
					$floor_produc_min+=$produce_minit;
					$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;

					$total_operator+=$prod_resource_array[$ldata][$pr_date]['operator'];
					$total_man_power+=$prod_resource_array[$ldata][$pr_date]['man_power'];
					$total_helper+=$prod_resource_array[$ldata][$pr_date]['helper'];
					$total_capacity+=$prod_resource_array[$ldata][$pr_date]['capacity'];
					$total_working_hour+=$prod_resource_array[$ldata][$pr_date]['working_hour'];
					$gnd_total_tgt_h+=$prod_resource_array[$ldata][$pr_date]['terget_hour'];
					$total_terget+=$eff_target;
					$grand_total_product+=$today_product;
					$gnd_avable_min+=$efficiency_min;
					$gnd_product_min+=$produce_minit;
					$gnd_total_fob_val+=$fob_val;
					$gnd_final_total_fob_val+=$fob_val;

				}
				$po_id=rtrim($production_data_arr[$f_id][$ldata]['po_id'],',');
				$po_id=array_unique(explode(",",$po_id));
				$style=rtrim($production_data_arr[$f_id][$ldata]['style']);
				$style=implode(",",array_unique(explode(",",$style)));

				$cbo_get_upto=str_replace("'","",$cbo_get_upto);
				$txt_parcentage=str_replace("'","",$txt_parcentage);

				$floor_name=$floorArr[$f_id];
				$floor_smv+=$item_smv;

				$floor_days_run+=$days_run;
				$floor_days_active+=$days_active;

				$po_id=$production_data_arr[$f_id][$ldata]['po_id'];//$item_ids//$subcon_order_id
				$styles=explode(",",$style);
				 $style_button='';//
				foreach($styles as $sid)
				{
					if( $style_button=='')
					{
						$style_button="<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$ldata."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
					}
					else
					{
						$style_button.=", "."<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$ldata."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
					}
				}
				$as_on_current_hour_target=0; $as_on_current_hour_variance=0;
				$as_on_current_hour_target=$terget_hour*$cla_cur_time;
				$as_on_current_hour_variance=$line_production_hour-$as_on_current_hour_target;

				if($line_efficiency<=$txt_parcentage) $efficiency_color="#FF0000"; else $efficiency_color="#FFFFFF";
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$html.='<tbody>';
				$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
				$html.='<td class="break_all" width="40">'.$i.'&nbsp;</td>
						<td class="break_all" width="80">'.$groupName.'&nbsp; </td>
						<td class="break_all" width="80">'.$floor_name.'&nbsp; </td>
						<td class="break_all" align="center" width="80" >'. $sewing_line.'&nbsp; </td>
						<td class="break_all" width="80"><p>'.$buyer_name.'&nbsp;</p></td>
						<td class="break_all" width="140" style="max-width:140px;"><p>'.$production_data_arr[$f_id][$ldata]['po_number'].'&nbsp;</p></td>
						<td class="break_all" width="100"><p>'.$style_button.'&nbsp;</p></td>
						<td class="break_all" width="80"><p>'.$production_data_arr[$f_id][$ldata]['file'].'&nbsp;</p></td>
						<td class="break_all" width="80"><p>'.$production_data_arr[$f_id][$ldata]['ref'].'&nbsp;</p></td>
						<td class="break_all" width="120" style="word-wrap:break-word; word-break: break-all;">'.$garment_itemname.'</td>
						<td class="break_all" align="right" width="60"><p>'.$item_smv.'</p></td>
						<td class="break_all" align="right" width="70">'.$operator.'</td>
						<td class="break_all" align="right" width="50">'.$helper.'</td>
						<td class="break_all" align="right" width="60">'.$man_power.'</td>
						<td class="break_all" align="right" width="70">'.$terget_hour.'</td>
						<td class="break_all" align="right" width="60">'.$days_run.'</td>
						<td class="break_all" align="right" width="60">'.$active_days.'</td>
						<td class="break_all" align="right" width="70">'.$capacity.'</td>
						<td class="break_all" align="right" width="60">'.$working_hour.'</td>
						<td class="break_all" align="right" width="60">'.$cla_cur_time.'</td>
						<td class="break_all" align="right" width="60">'.$as_on_current_hour_target.'</td>
						<td class="break_all" align="right" width="80">'.$eff_target.'</td>
						<td class="break_all" width="75" align="right"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$ldata.",'tot_prod','".$smv_for_item."','".$acturl_hour_minute."','".$line_start."',".$txt_date.')">'.$line_production_hour.'</a></td>
						<td class="break_all" align="right" width="80">'.$as_on_current_hour_variance.'</td>
						<td class="break_all" align="right" width="80">'.($line_production_hour-$eff_target).'</td>
						<td class="break_all" align="right" width="100">'.number_format($efficiency_min,0).'</td>
						<td class="break_all" width="100" align="right"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$ldata.",'tot_prod','".$smv_for_item."','".$acturl_hour_minute."','".$line_start."',".$txt_date.')">'.number_format($produce_minit,0).'</a></td>
						<td class="break_all" align="right" width="60" >'. number_format(($line_production_hour/$eff_target)*100,2).'%</td>';

						if($line_efficiency<=$txt_parcentage)
						{
							$html.='<td class="break_all" align="right" width="60" bgcolor="red">'.number_format($line_efficiency,2).'%</td>';
						}
						else
						{
							$html.='<td class="break_all" align="right" width="60">'.number_format($line_efficiency,2).'%</td>';
						}
						$html.='<td class="break_all" width="70" title='.$fob_rate.' align="right"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$ldata.",'tot_fob_value_popup','".$smv_for_item."','".$acturl_hour_minute."','".$line_start."',".$txt_date.')">'.number_format($fob_val,2).'</a></td>';
						for($k=$hour; $k<=$last_hour; $k++)
						{
							$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";


							if($start_hour_arr[$k]==$lunch_start_hour)
							{
								 $bg_color='background:yellow';
							}
							else if($terget_hour>$production_hour[$prod_hour])
							{
								$bg_color='background:red';
								if($production_hour[$prod_hour]==0)
								{
									$bg_color='';
								}
							}
							else if($terget_hour<$production_hour[$prod_hour])
							{
								$bg_color='background:green';
								if($production_hour[$prod_hour]==0)
								{
									$bg_color='';
								}
							}
							else
							{
								$bg_color="";
							}

							$html.='<td class="break_all" align="right" width="50"  style='.$bg_color.'>'.$production_hour[$prod_hour].'</td>';
							//$html.='<td class="break_all" align="right" width="50"  style=" background-color:#FFFF66" >'.$production_hour[$prod_hour].'&nbsp;kk</td>';
						}
				$html.='</tr>';
				$i++;
				$check_arr[]=$f_id;
			  }
			}

		}
	}
			$html.='<tr  bgcolor="#B6B6B6">
					<td class="break_all" width="40">&nbsp;</td>
					<td class="break_all" width="80">&nbsp;</td>
					<td class="break_all" width="80">&nbsp;</td>
					<td class="break_all" width="80">&nbsp;</td>
					<td class="break_all" width="80">&nbsp;</td>
					<td class="break_all" width="140">&nbsp;</td>
					<td class="break_all" width="100">&nbsp;</td>
					<td class="break_all" width="80">&nbsp;</td>
					<td class="break_all" width="80">&nbsp;</td>
					<td class="break_all" width="120">&nbsp;</td>
					<td class="break_all" align="right" width="60">&nbsp;</td>
					<td class="break_all" align="right" width="70">'.$floor_operator.'</td>
					<td class="break_all" align="right" width="50">'. $floor_helper.'</td>
					<td class="break_all" align="right" width="60">'. $floor_man_power.'</td>
					<td class="break_all" align="right" width="70">'. $floor_tgt_h.'</td>
					<td class="break_all" align="right" width="60">'. $floor_days_run.'</td>
					<td class="break_all" align="right" width="60">'. $floor_days_active.'</td>
					<td class="break_all" align="right" width="70">&nbsp;</td>
					<td class="break_all" align="right" width="60">'. $floor_working_hour.'</td>
					<td class="break_all" align="right" width="60">&nbsp;</td>
					<td class="break_all" align="right" width="60">&nbsp;</td>
					<td class="break_all" align="right" width="80">'. $eff_target_floor.'</td>
					<td class="break_all" align="right" width="80">'.$line_floor_production.'</td>
					<td class="break_all" align="right" width="80">'.$line_floor_production.'</td>
					<td class="break_all" align="right" width="80">'. ($line_floor_production-$eff_target_floor).'</td>
					<td class="break_all" align="right" width="100">'. number_format($floor_avale_minute,0).'</td>
					<td class="break_all" align="right" width="100">'. number_format($floor_produc_min,0).'</td>
					<td class="break_all" align="right" width="60">'. number_format(($line_floor_production/$eff_target_floor)*100,2).'%</td>
					<td class="break_all" align="right" width="90">'.number_format($floor_efficency,2).'%</td>
					<td class="break_all" width="70" align="right">'.number_format($gnd_total_fob_val,2).'</td>';

					for($k=$hour; $k<=$last_hour; $k++)
					{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";

						if($floor_tgt_h>$floor_production[$prod_hour])
						{
							$bg_color='background:red';
							if($floor_production[$prod_hour]==0)
							{
								$bg_color='';
							}
						}
						else if($floor_tgt_h<$floor_production[$prod_hour])
						{
							$bg_color='background:green';
							if($floor_production[$prod_hour]==0)
							{
								$bg_color='';
							}
						}
						else if($start_hour_arr[$k]==$global_start_lanch)
						{
							 $bg_color='background:yellow';
						//$html.='<td class="break_all" align="right" width="50" style=" background-color:#FFFF66" >'. $floor_production[$prod_hour].'&nbsp;</td>';
						}
						else
						{
							 $bg_color='';
						//$html.='<td class="break_all" align="right" width="50">'. $floor_production[$prod_hour].'&nbsp;</td>';
						}
						$html.='<td class="break_all" align="right" width="50" style='.$bg_color.' >'. $floor_production[$prod_hour].'</td>';
					}

				   $html.='</tr> </tbody>';
				   $floor_html.="<tr bgcolor='$bgcolor' onclick=change_color('floor_$j','$bgcolor') id=floor_$j>";
				   $floor_html.='<td class="break_all" width="40">'.$j.'&nbsp;</td>
								<td class="break_all" width="80" align="center">'.$floor_name.'&nbsp; </td>
								<td class="break_all" width="70" align="right">'. $floor_tgt_h.'</td>
								<td class="break_all" width="70" align="right">'.$floor_capacity.'</td>
								<td class="break_all" align="right" width="60">'. $floor_man_power.'</td>
								<td class="break_all" width="70" align="right">'.$floor_operator.'</td>
								<td class="break_all" width="50" align="right">'. $floor_helper.'</td>
								<td class="break_all" align="right" width="60">'. $floor_working_hour.'</td>
								<td class="break_all" align="right" width="80">'.$eff_target_floor.'</td>
								<td class="break_all" align="right" width="80">'.$line_floor_production.'</td>
								<td class="break_all" align="right" width="80">'. ($line_floor_production-$eff_target_floor).'</td>
								<td class="break_all" align="right" width="100">'. $floor_avale_minute.'</td>
								<td class="break_all" align="right" width="100">'. number_format($floor_produc_min,0).'</td>
								<td class="break_all" align="right" width="90">'. number_format(($line_floor_production/$eff_target_floor)*100,2).'%</td>';
								if($floor_efficency<=$txt_parcentage)
								{
									$floor_html.='<td class="break_all" align="right" width="90" bgcolor="red">'.number_format($floor_efficency,2).'%</td>';
								}
								else
								{
									$floor_html.='<td class="break_all" align="right" width="90" >'.number_format($floor_efficency,2).'%</td>';
								}
								for($k=$hour; $k<=$last_hour; $k++)
								{
									$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
									if($start_hour_arr[$k]==$global_start_lanch)
									{
										$floor_html.='<td class="break_all" align="right" width="50" style=" background-color:#FFFF66 ">'. $floor_production[$prod_hour].'</td>';
									}
									else
									{
										$floor_html.='<td class="break_all" align="right" width="50" >'. $floor_production[$prod_hour].'</td>';
									}
								}
					$floor_html.='</tr></tbody>';
					$smv_for_item="";
				?>

	<fieldset style="width:2530px">
       <table width="2200" cellpadding="0" cellspacing="0">
            <tr class="form_caption">
                <td  colspan="26" align="center"><strong><? echo $report_title; ?> &nbsp;V2</strong></td>
            </tr>
            <tr class="form_caption">
                <td  colspan="26" align="center"><strong><? echo $companyArr[$comapny_id]; ?></strong></td>
            </tr>
			<tr class="form_caption">
			    <td colspan="26" align="center"><b><?= "ISO Number :".$name_iso_Array[0]["ISO_NO"];?></b></td>
            </tr>
            <tr class="form_caption">
                <td colspan="26" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date)) ); ?></strong></td>
            </tr>
        </table>
        <br />
        <table  width="600" cellpadding="0"  cellspacing="0" align="center" style="padding-left:200px">
            <tr>


                <td bgcolor="#FFFF66" height="18" width="30" ></td>
                <td> &nbsp;Lunch Hour</td>
                <td bgcolor="red" height="18" width="30"></td>
                <td> &nbsp;Efficiency % less than Standard And Production less than Target</td>


            </tr>
        </table>
        <label> <strong>Report Sumarry:-</strong></label>
          <table id="table_header_2" class="rpt_table" width="1940" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr height="50">
                    <th class="break_all" width="40">SL</th>
                    <th class="break_all" width="80">Floor Name</th>
                    <th class="break_all" width="70">Hourly Target</th>
                    <th class="break_all" width="70">Capacity</th>
                    <th class="break_all" width="60">Total Man Power</th>
                    <th class="break_all" width="70">Operator</th>
                    <th class="break_all" width="50">Helper</th>
                    <th class="break_all" width="60">Line Hour</th>
                    <th class="break_all" width="80">Day Target</th>
                    <th class="break_all" width="80">Total Prod.</th>
                    <th class="break_all" width="80">Variance </th>
                    <th class="break_all" width="100">SMV Available</th>
                    <th class="break_all" width="100">SMV Achieved</th>
                    <th class="break_all" width="90">Achievement %</th>
                    <th class="break_all" width="90">Floor Eff. %</th>

                	<?
                	for($k=$hour; $k<=$last_hour; $k++)
					{
					?>
                    <th class="break_all" width="50" style="vertical-align:middle"><div class="block_div"><?  echo substr($start_hour_arr[$k],0,5);   ?></div></th>
					<?
					}
                	?>
                </tr>
            </thead>
        </table>
        <div style="width:1960px; max-height:400px; overflow-y:scroll" id="scroll_body">
           <table class="rpt_table" width="1940" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
           <?  echo $floor_html; ?>
            <tfoot>
                   <tr>
                        <th class="break_all" width="40"></th>
                        <th class="break_all" width="80">Total </th>
                        <th class="break_all" width="70"><? echo $gnd_total_tgt_h;   ?> </th>
                        <th class="break_all" width="70" align="right"><? echo $total_capacity; ?> </th>
                        <th class="break_all" width="60"><? echo $total_man_power; ?>&nbsp;</th>
                        <th class="break_all" width="70"><? echo $total_operator; ?></th>
                        <th class="break_all" width="50"><? echo $total_helper; ?></th>
                        <th class="break_all" align="right" width="60"><? echo $total_working_hour; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="80"><? echo $total_terget; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="80"><? echo $line_total_production; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="80"><? echo $line_total_production-$total_terget; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="100"><? echo $gnd_avable_min; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="100"><? echo number_format($gnd_product_min,0); ?>&nbsp;</th>
                        <th class="break_all" align="right" width="90"><?  echo number_format(($line_total_production/$total_terget)*100,2)."%"; ?>&nbsp;</th>
                        <th class="break_all" align="center" width="90"><? echo number_format(($gnd_product_min/$gnd_avable_min)*100,2)."%"; ?>&nbsp;</th>
                        <?
						for($k=$hour; $k<=$last_hour; $k++)
						{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
						?>
						  <th class="break_all" width="50" ><?  echo $total_production[$prod_hour];   ?></th>
						<?
						}
                		?>
                   </tr>
               </tfoot>

          </table>

        </div>
    </br><br/>
        <table id="table_header_1" class="rpt_table" width="3060" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr height="50">
                    <th class="break_all" width="40">SL</th>
                    <th class="break_all" width="80">Group</th>
                    <th class="break_all" width="80">Floor Name</th>
                    <th class="break_all" width="80">Line No</th>
                    <th class="break_all" width="80">Buyer</th>
                    <th class="break_all" width="140">Order No</th>
                    <th class="break_all" width="100">Style Ref.</th>
                    <th class="break_all" width="80">File No</th>
                    <th class="break_all" width="80">Ref. No</th>
                    <th class="break_all" width="120">Garments Item</th>
                    <th class="break_all" width="60">SMV</th>
                    <th class="break_all" width="70">Operator</th>
                    <th class="break_all" width="50">Helper</th>
                    <th class="break_all" width="60"> Man Power</th>
                    <th class="break_all" width="70">Hourly Target (Pcs)</th>
                    <th class="break_all" width="60">Days Run</th>
                    <th class="break_all" width="60">Active Prod.Days</th>
                    <th class="break_all" width="70">Capacity</th>
                    <th class="break_all" width="60">Working Hour</th>
                    <th class="break_all" width="60">Current Hour</th>
                    <th class="break_all" width="60">As On Current Hour Target (Pcs)</th>
                    <th class="break_all" width="80">Total Target</th>
                    <th class="break_all" width="80">Total Prod.</th>
                    <th class="break_all" width="80">As On Current Hour Prod.Variance</th>
                    <th class="break_all" width="80">Total Variance (Pcs)</th>
                    <th class="break_all" width="100">Available Minutes</th>
                    <th class="break_all" width="100">Produce Minutes</th>
                    <th class="break_all" width="60">Target Hit rate</th>
                    <th class="break_all" width="90">Line Effi %</th>
                    <th class="break_all" width="70">FOB Val.</th>
                   <?

                	for($k=$hour; $k<=$last_hour; $k++)
					{
					?>
                      <th class="break_all" width="50" style="vertical-align:middle"><div class="block_div"><?  echo substr($start_hour_arr[$k],0,5);   ?></div></th>
					<?
					}
                ?>
                </tr>
            </thead>
        </table>
        <div style="width:3080px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="3060" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
              <? echo $html;  ?>
                <tfoot>
                   <tr>
                        <th class="break_all" width="40">&nbsp;</th>
                        <th class="break_all" width="80">&nbsp;</th>
                        <th class="break_all" width="80">&nbsp;</th>
                        <th class="break_all" width="80">&nbsp;</th>
                        <th class="break_all" width="80">&nbsp;</th>
                        <th class="break_all" width="140">&nbsp;</th>
                        <th class="break_all" width="100">&nbsp;</th>
                        <th class="break_all" width="80">&nbsp;</th>
                        <th class="break_all" width="80">&nbsp;</th>
                        <th class="break_all" width="120">Total</th>
                        <th class="break_all" align="right" width="60"><? // echo number_format($total_smv/($i-1),2); ?>&nbsp;</th>
                        <th class="break_all" align="right" width="70"><? echo $total_operator; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="50"><? echo $total_helper; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="60"><? echo $total_man_power; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="70"><?  echo $gnd_total_tgt_h; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="60"><? //echo $prod_resource_array[$row[csf('sewing_line')]][$pr_date]['man_power']; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="60">&nbsp;</th>
                        <th class="break_all" align="right" width="70"><? echo $total_capacity; ?></th>
                        <th class="break_all" align="right" width="60"><? echo $total_working_hour; ?></th>
                        <th class="break_all" align="right" width="60">&nbsp;</th>
                        <th class="break_all" align="right" width="60">&nbsp;</th>
                        <th class="break_all" align="right" width="80"><? echo $total_terget; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="80"><? echo $line_total_production; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="80">&nbsp;</th>
                        <th class="break_all" align="right" width="80"><? echo $line_total_production-$total_terget; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="100"><? echo number_format($gnd_avable_min,0); ?>&nbsp;</th>
                        <th class="break_all" align="right" width="100"><? echo number_format($gnd_product_min,0); ?>&nbsp;</th>
                        <th class="break_all" align="right" width="60"><? echo number_format(($line_total_production/$total_terget)*100,2)."%"; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="90" ><? echo number_format(($gnd_product_min/$gnd_avable_min)*100,2)."%";?>&nbsp;</th>
                        <th class="break_all" align="right" width="70"><? echo number_format($gnd_final_total_fob_val,2);?>&nbsp;</th>
					    <?
						for($k=$hour; $k<=$last_hour; $k++)
						{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
						?>
						<th class="break_all" align="right" width="50"><? echo $total_production[$prod_hour]; ?></th>
						<?
						}
                        ?>
                    </tr>
                </tfoot>
            </table>
		</div>
	</fieldset>
     <br/>
         <fieldset style="width:1050px">
			<label><b>No Production Line</b></label>
        	<table id="table_header_1" class="rpt_table" width="1030" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="40">SL</th>
					<th width="100">Line No</th>
					<th width="100">Floor</th>
					<th width="100">Group</th>
					<th width="75">Man Power</th>
					<th width="75">Operator</th>
					<th width="75">Helper</th>
                    <th width="75">Working Hour</th>
					<th width="380">Remarks</th>

				</thead>
			</table>
			<div style="width:1050px; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                <?
				$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
			    $sql_active_line=sql_select("select sewing_line,sum(production_quantity) as total_production from pro_garments_production_mst  where production_date=".$txt_date." and production_type=5 and status_active=1 and is_deleted=0 and prod_reso_allo=1 group by  sewing_line");

				//$actual_line_arr=array();
				foreach($sql_active_line as $inf)
				{

				   if(str_replace("","",$inf[csf('sewing_line')])!="")
				   {
						if(str_replace("","",$actual_line_arr)!="") $actual_line_arr.=",";
					    $actual_line_arr.="'".$inf[csf('sewing_line')]."'";
				   }
				}

				$sql_active_line=sql_select("SELECT line_id as sewing_line,sum(production_qnty) as total_production from subcon_gmts_prod_dtls  where production_date=".$txt_date." and production_type=2 and status_active=1 and is_deleted=0 and prod_reso_allo=1 group by  line_id");

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
			if($db_type==0) $remarks_cond="group_concat(d.remarks)";
			else if($db_type==2) $remarks_cond="LISTAGG(cast(d.remarks as varchar2(4000)), ',') WITHIN GROUP (ORDER BY d.remarks)";

			//echo "select a.id, a.floor_id, a.line_number, b.man_power, b.operator, b.helper, b.working_hour, $remarks_cond as remarks from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and b.pr_date=".$txt_date." and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 $line_cond group by a.id, a.floor_id, a.line_number, b.man_power, b.operator, b.helper, b.working_hour";
			$group_cond_sub = str_replace("a.sewing_line", "a.line_number", $group_cond);
			 $dataArray=sql_select("select a.id, a.floor_id, a.line_number, b.man_power, b.operator, b.helper, b.working_hour, $remarks_cond as remarks from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and b.pr_date=".$txt_date." and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 $line_cond $group_cond_sub group by a.id, a.floor_id, a.line_number, b.man_power, b.operator, b.helper, b.working_hour order by a.floor_id");
					$l=1; $location_array=array(); $floor_array=array();
					foreach( $dataArray as $row )
					{
						if ($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$remarks="";
						$remarks=implode(",",array_unique(explode(",",$row[csf('remarks')])));
						$sewing_lines='';
						$line_numbers=array_unique(explode(",",$row[csf('line_number')]));
						//print_r($line_number);
						foreach($line_numbers as $val)
						{
							if($sewing_lines=='') $sewing_lines=$lineArr[$val]; else $sewing_lines.=",".$lineArr[$val];
						}
					?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tbltr_<? echo $l; ?>','<? echo $bgcolor; ?>')" id="tbltr_<? echo $l; ?>">
                        	<td width="40"><? echo $l; ?></td>
                            <td width="100"><p><? echo $sewing_lines; ?>&nbsp;</p></td>
                            <td width="100" align="right"><p>&nbsp;<? echo $floorArr[$row[csf('floor_id')]]; ?></p></td>
                            <td width="100" align="right"><p>&nbsp;<? echo $groupArr[$row[csf('line_number')]]; ?></p></td>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('man_power')]; ?></th>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('operator')]; ?></th>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('helper')]; ?></td>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('working_hour')]; ?></td>
                            <td width="380"><? echo $remarks; ?>&nbsp;</td>
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

} // 3rd Button End

if($action=="report_generate4") //4th Button Start...
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

		// $costing_per_arr = return_library_array("SELECT job_no,costing_per from wo_pre_cost_mst","job_no","costing_per");
		// $tot_cost_arr = return_library_array("SELECT job_no, cm_for_sipment_sche from wo_pre_cost_dtls","job_no","cm_for_sipment_sche");

		// $imge_arr=return_library_array( "SELECT master_tble_id,image_location from common_photo_library where file_type=1 and form_name='knit_order_entry'",'master_tble_id','image_location');

		$comapny_id=str_replace("'","",$cbo_company_id);
	    $today_date=date("Y-m-d");
		$txt_producting_day="".str_replace("'","",$txt_date)."";
		$sewing_group = str_replace("'","",$cbo_sewing_group);
		$group_name = "";
		$line_name = "";
		$group_cond ="";
		if($sewing_group!=0)
		{
			$group_sql = "SELECT id from lib_sewing_line where sewing_group=$cbo_sewing_group and status_active =1 and is_deleted=0";
			$group_sql_res = sql_select($group_sql);
			foreach ($group_sql_res as $val)
			{
				if($line_name == "")
				{
					$line_name = $val[csf('id')];
					$group_name .= "'$line_name'";
				}
				else
				{
					$line_name = $val[csf('id')];
					$group_name .= ","."'$line_name'";
				}
			}
			unset($group_sql_res);
			$group_cond = " and a.sewing_line in($group_name)";
			$group_cond2 = " and l.id in($group_name)";
		}
		//***************************************************************************************************************************
		$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
		order by sewing_line_serial");
		foreach($lineDataArr as $lRow)
		{
			$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
			$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
			$lastSlNo=$lRow[csf('sewing_line_serial')];
		}
		unset($lineDataArr);

		if($db_type==0)
		{
			$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and  a.company_id in($comapny_id) and shift_id=1 and pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
		}
		else
		{
			$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and   a.company_id in($comapny_id) and shift_id=1 and pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
		}
		// echo $min_shif_start;
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
		// echo "<pre>";print_r($line_wise_shift_arr);die();
		//==============================shift time===============================
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
			$start_time_arr[$row[csf('company_name')]][$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
			$start_time_arr[$row[csf('company_name')]][$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
		}
		unset($start_time_data_arr);

		$prod_start_hour=$group_prod_start_time[0][csf('prod_start_time')];
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
			// echo $j."<br>";
		}
		//echo $pc_date_time;die;
		$start_hour_arr[$j+1]='23:59';
		// echo "<pre>";print_r($start_hour_arr);die();

	    //  ======================= geting shift name ============================
	    $sql = "SELECT shift_name,start_time,end_time from shift_duration_entry where status_active=1 and is_deleted=0 and production_type=3 order by shift_name asc";
	    $res = sql_select($sql);
	    $shift_arr = array();
	    foreach ($res as $val)
	    {
	    	$shift_arr[$val['SHIFT_NAME']]['start_time'] = $val['START_TIME'];
	    	$shift_arr[$val['SHIFT_NAME']]['end_time'] = $val['END_TIME'];
	    }
		unset($res);
	    // echo "<pre>";print_r($shift_arr);die();
	    /*$start_hour2=$prod_start_hour;
	    foreach ($shift_arr as $s_key => $s_val)
	    {
	    	if(strlen($s_val['start_time'])==5)
	    	{
	    		$startTime = substr($s_val['start_time'], 0,2);
	    	}
	    	else
	    	{
	    		$startTime = substr($s_val['start_time'], 0,1);
	    	}

	    	if(strlen($s_val['end_time'])==5)
	    	{
	    		$endTime = substr($s_val['end_time'], 0,2);
	    	}
	    	else
	    	{
	    		$endTime = substr($s_val['end_time'], 0,1);
	    	}

	    	echo $endTime;die();
	    	for($s=substr($s_val['start_time'],0,1);$s<substr($s_val['end_time'],0,1);$s++)
			{
				$start_hour2=add_time($start_hour,60);
				$start_hour_arr2[$s+1]=substr($s_val['start_time'],0,5);
				echo "<pre>";print_r($start_hour_arr2);die();
			}
	    }*/
	    // echo "<pre>";print_r($start_hour_arr2);die();

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
		if(str_replace("'","",$cbo_company_id)=="" || str_replace("'","",$cbo_company_id)==0) $company_name=""; else $company_name="and a.serving_company in(".str_replace("'","",$cbo_company_id).")";

		if(str_replace("'","",$cbo_location_id)==0)
		{
			$subcon_location="";
			$location="";
		}
		else
		{
			$location=" and a.location in (".str_replace("'","",$cbo_location_id).")";
			$subcon_location=" and a.location_id in(".str_replace("'","",$cbo_location_id).") ";
		}
		$cbo_floor_id=str_replace("'","",$cbo_floor_id);
		if($cbo_floor_id=="") $floor=""; else $floor="and a.floor_id in(".$cbo_floor_id.")";
	    if(str_replace("'","",$hidden_line_id)==0)
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
		$cbo_no_prod_type=str_replace("'","",$cbo_no_prod_type);
		$file_no=str_replace("'","",$txt_file_no);
		$ref_no=str_replace("'","",$txt_ref_no);
		if($file_no!="") $file_cond="and c.file_no=$file_no";else $file_cond="";
		if($ref_no!="") $ref_cond="and c.grouping='$ref_no'";else $ref_cond="";
		//echo $file_cond;

		if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date=$txt_date";
		// echo $txt_date_from; die;

		$prod_resource_array=array();

		$dataArray_sql=sql_select(" SELECT a.id,a.company_id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity, l.line_name, l.sewing_line_serial, b.line_chief, b.active_machine, c.target_efficiency
		from prod_resource_mst a
		left join lib_sewing_line l on a.line_number=cast(l.id as varchar2(100)),prod_resource_dtls b,prod_resource_dtls_mast c
		where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id in (".$comapny_id.") and b.pr_date=$txt_date and b.is_deleted=0 and c.is_deleted=0 $subcon_location $floor $resource_line $group_cond2 order by a.company_id,a.line_marge desc, a.location_id,a.floor_id,l.sewing_line_serial");

		foreach($dataArray_sql as $val)
		{
			$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['man_power']=$val[csf('man_power')];
			$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['operator']=$val[csf('operator')];
			$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['helper']=$val[csf('helper')];
			$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['terget_hour']=$val[csf('target_per_hour')];
			$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['working_hour']=$val[csf('working_hour')];
			$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['tpd']=$val[csf('target_per_hour')]*$val[csf('working_hour')];
			$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['day_start']=$val[csf('from_date')];
			$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['day_end']=$val[csf('to_date')];
			$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['capacity']=$val[csf('capacity')];
			$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['smv_adjust']=$val[csf('smv_adjust')];
			$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];
			$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['line_number']=$val[csf('line_number')];
			$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['pr_date']=$val[csf('pr_date')];
			$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['machine']=$val[csf('active_machine')];
			$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['line_chief']=$val[csf('line_chief')];
			$prod_resource_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]]['target_efficiency']=$val[csf('target_efficiency')];

		}
		unset($dataArray_sql);
		$smv_adjustment_sql="SELECT a.id,a.company_id, a.location_id, a.floor_id,b.ADJUSTMENT_SOURCE, sum(b.TOTAL_SMV) as TOTAL_SMV
		from prod_resource_mst a, prod_resource_smv_adj b
		where a.id=b.mst_id and a.is_deleted=0 and b.status_active=1 and b.adjustment_source in (9,10) and a.company_id in($comapny_id)   $subcon_location $floor $resource_line $group_cond2 and b.pr_date=$txt_date group by a.id,a.company_id, a.location_id, a.floor_id,b.adjustment_source ";
		// echo $smv_adjustment_sql;
		$smv_adjustment_data=sql_select($smv_adjustment_sql);
		foreach($smv_adjustment_data as $row)
		{
			if($row["ADJUSTMENT_SOURCE"]==9)
			{
				$prod_resource_array[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('id')]]['smv_adjustment_plus']=$row["TOTAL_SMV"];
			}
			if($row["ADJUSTMENT_SOURCE"]==10)
			{
				$prod_resource_array[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('id')]]['smv_adjustment_minus']=$row["TOTAL_SMV"];
			}
		}
		unset($smv_adjustment_data);
		//print_r($prod_resource_array);die;
		if(str_replace("'","",trim($txt_date))==""){$pr_date_con="";}else{$pr_date_con=" and b.pr_date=$txt_date";}

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
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['shift_id']=$val[csf('shift_id')];
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['prod_start_time']=$val[csf('prod_start_time')];
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['lunch_start_time']=$val[csf('lunch_start_time')];
		}
		unset($dataArray);
		// print_r($line_number_arr);die;
		//**********************************************************************************************************************
	  	if($db_type==0)
		{
			$manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 and comp.id in($cbo_company_id)","company_id");
		}
		else
		{
			$manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 and comp.id in($cbo_company_id)","company_id");
		}


		if($db_type==0) $prod_start_cond=" min(prod_start_time) as prod_start_time";
		else if($db_type==2) $prod_start_cond="min(TO_CHAR(prod_start_time,'DD-MON-YYYY HH24:MI')) as prod_start_time";

		$variable_start_time_arr='';

		$prod_start_time=sql_select("SELECT $prod_start_cond  from variable_settings_production where company_name in($cbo_company_id) and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");
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
		//echo $search_prod_date;die;

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

		/*=============================================================================/
		/									Inhouse data 							   /
		/=============================================================================*/
		if($db_type==0)
		{
			$sql="SELECT  c.job_no_mst as job_no , a.serving_company, a.location, a.floor_id, a.production_date, a.sewing_line,b.job_no, b.buyer_name  as buyer_name,b.style_ref_no,b.total_set_qnty as ratio, a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.unit_price,
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
			$sql.="sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23 from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a where a.production_type in (4,5) and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.prod_reso_allo=1 $company_name $location $floor $line $buyer_id_cond  $txt_date_from $file_cond $ref_cond $group_cond group by a.serving_company, a.location, a.floor_id,a.po_break_down_id, a.production_date,b.total_set_qnty, a.prod_reso_allo, a.sewing_line,b.job_no, b.buyer_name, b.style_ref_no, a.item_number_id, c.po_number, c.unit_price ,c.job_no_mst";
		}
		else if($db_type==2)
		{
			$sql="SELECT  a.serving_company, a.location, a.floor_id,a.shift_name, a.production_date, a.sewing_line,b.id as job_id,b.job_no,b.buyer_name  as buyer_name,b.style_ref_no, b.total_set_qnty as ratio, a.po_break_down_id, a.item_number_id,c.job_no_mst as job_no , c.po_number as po_number,c.unit_price,c.grouping,
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
			$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23
			from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a
			where a.production_type in(4,5) and a.po_break_down_id=c.id and c.job_id=b.id and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.prod_reso_allo=1 $company_name $location $floor $line $buyer_id_cond  $txt_date_from $file_cond $ref_cond $group_cond group by a.serving_company, a.location, a.floor_id,a.shift_name,a.po_break_down_id, a.production_date, a.prod_reso_allo, a.sewing_line,b.id, b.job_no,b.total_set_qnty, b.buyer_name, b.style_ref_no, a.item_number_id, c.po_number,c.grouping,c.job_no_mst,  c.unit_price";

		}
		// echo $sql;die;
		// echo "<pre>";print_r($start_hour_arr);die();
		$sql_resqlt=sql_select($sql);
		$production_data_arr=array();
		$production_po_data_arr=array();
		$production_serial_arr=array(); $reso_line_ids=''; $all_po_id="";
		$all_style_arr=array();$style_wise_po_arr=array();
		$job_no_arr = array();
		foreach($sql_resqlt as $val)
		{
			$job_no_arr[$val[csf('job_id')]] = $val[csf('job_id')];
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

				if(substr($line_wise_shift_arr[$val[csf('sewing_line')]],0,2) > substr($start_hour_arr[$h],0,2))
				{
					// echo $line_wise_shift_arr[$val[csf('sewing_line')]]."==".$start_hour_arr[$h]."<br>";
					// sew out qty before shift start
					$prod_hour="prod_hour".substr($start_hour_arr[$h],0,2)."";
					$output_qty += $val[csf($prod_hour)];
				}
				else
				{
					$prod_hour="prod_hour".substr($start_hour_arr[$h],0,2)."";
					$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$prod_hour]+=$val[csf($prod_hour)]+$output_qty;
					$output_qty = 0;

					if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
					{
						if( $h>=$line_start_hour && $h<=$actual_time)
						{
							$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]+=$val[csf($prod_hour)];
						}
					}

					if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date))
					{
						$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]+=$val[csf($prod_hour)];
					}
				}
			}

			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
			{
				if( $h>=$line_start_hour && $h<=$actual_time)
				{
					$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]+=$val[csf('prod_hour23')];
				}
			}
			else
			{
				$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]+=$val[csf('prod_hour23')];
			}

		 	$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['prod_hour23']+=$val[csf('prod_hour23')];

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
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['grouping'].=",".$val[csf('grouping')];
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_id'].=",".$val[csf('po_break_down_id')];
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style'].=",".$val[csf('style_ref_no')];
			}
		 	else
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']=$val[csf('po_number')];
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['grouping']=$val[csf('grouping')];
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_id']=$val[csf('po_break_down_id')];
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style']=$val[csf('style_ref_no')];
			}
			$fob_rate_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['rate']=$val[csf('unit_price')];

			if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']!="")
			{
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('job_no')]."**".$val[csf('ratio')];
			}
			else
			{
				 $production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('job_no')]."**".$val[csf('ratio')];
			}

			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['prod_hour23']+=$val[csf('prod_hour23')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['quantity']+=$val[csf('good_qnty')];
			$production_data_arr_qty[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]['quantity']+=$val[csf('good_qnty')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['input_qnty']+=$val[csf('input_qnty')];

			if($all_po_id=="") $all_po_id=$val[csf('po_break_down_id')]; else $all_po_id.=",".$val[csf('po_break_down_id')];

			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['job_no']=$val[csf('job_no')];
			$all_style_arr[$val[csf('style_ref_no')]]=$val[csf('style_ref_no')];
			$style_wise_po_arr[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];

		}
		unset($sql_resqlt);
		//echo "<pre>";print_r($production_data_arr);die;
		$po_ids=count(array_unique(explode(",",$all_po_id)));
		$po_numIds=chop($all_po_id,','); $poIds_cond="";
		if($all_po_id!='' || $all_po_id!=0)
		{
			if($db_type==2 && $po_ids>1000)
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
				$poIds_cond=" and  b.id  in ($all_po_id)";
			}
		}

		$job_id_cond = where_con_using_array($job_no_arr,0,"job_id");
		$costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst where status_active=1 $job_id_cond","job_no","costing_per");
		$tot_cost_arr = return_library_array("select job_no, cm_for_sipment_sche from wo_pre_cost_dtls where status_active=1 $job_id_cond","job_no","cm_for_sipment_sche");


		$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($manufacturing_company) and variable_list=25 and   status_active=1 and is_deleted=0");
		// echo $smv_source;die();
	    if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;

	    if($smv_source==3)
		{
			$style_nos=implode("','",$all_style_arr);
			$gsdSql="SELECT a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID  from PPL_GSD_ENTRY_MST a where a.APPLICABLE_PERIOD <= $txt_date and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and a.BULLETIN_TYPE in(4) and A.STYLE_REF in('".$style_nos."')
			group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID
			 ORDER BY a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC";// and a.APPROVED=1
			     // echo $gsdSql;
			$gsdSqlResult = sql_select($gsdSql);

			foreach($gsdSqlResult as $rows)
			{
				foreach($style_wise_po_arr[$rows[STYLE_REF]] as $po_id)
				{
					if($item_smv_array[$po_id][$rows[GMTS_ITEM_ID]]==''){
						$item_smv_array[$po_id][$rows[GMTS_ITEM_ID]] = number_format($rows[TOTAL_SMV],2);
					}
				}
			}
			unset($gsdSqlResult);
		}
		else
		{
			$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost,c.smv_set from wo_po_details_master a,wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.company_name in($manufacturing_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
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
			unset($resultItem);
		}
	 	// echo "<pre>";print_r($item_smv_array);die();

		$sql_item_rate="SELECT b.id, c.item_number_id, c.order_quantity, c.order_total from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c where b.job_id=a.id and b.id=c.po_break_down_id and b.job_id=c.job_id and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1  $file_cond $ref_cond  $poIds_cond";
		$resultRate=sql_select($sql_item_rate);
		$item_po_array=array();
		foreach($resultRate as $row)
		{
			$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['qty']+=$row[csf('order_quantity')];
			$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['amt']+=$row[csf('order_total')];
		}
		unset($resultRate);

		$resout_input_output=sql_select("SELECT
				a.serving_company,
				a.location,
				a.floor_id,
				a.sewing_line,
				a.po_break_down_id,
				a.production_type,
				a.production_date,
				a.production_quantity

				from pro_garments_production_mst a
				where a.production_type in (5,4) and po_break_down_id in($all_po_id) $group_cond and  a.status_active=1 and a.is_deleted=0 $company_name");

		foreach($resout_input_output as $i_val)
		{
			if($i_val[csf('production_type')]==4)
			{

				if($input_output_po_arr[$i_val[csf('serving_company')]][$i_val[csf('floor_id')]][$i_val[csf('sewing_line')]][$i_val[csf('po_break_down_id')]]['input_date']!='')
				{
					if(strtotime($input_output_po_arr[$i_val[csf('serving_company')]][$i_val[csf('floor_id')]][$i_val[csf('sewing_line')]][$i_val[csf('po_break_down_id')]]['input_date'])>strtotime($i_val[csf('production_date')]))
					{
						$input_output_po_arr[$i_val[csf('serving_company')]][$i_val[csf('floor_id')]][$i_val[csf('sewing_line')]][$i_val[csf('po_break_down_id')]]['input_date']=$i_val[csf('production_date')];
					}
				}
				else
				{
					$input_output_po_arr[$i_val[csf('serving_company')]][$i_val[csf('floor_id')]][$i_val[csf('sewing_line')]][$i_val[csf('po_break_down_id')]]['input_date']=$i_val[csf('production_date')];
				}

				$input_output_po_arr[$i_val[csf('serving_company')]][$i_val[csf('floor_id')]][$i_val[csf('sewing_line')]][$i_val[csf('po_break_down_id')]]['input']+=$i_val[csf('production_quantity')];

				if(change_date_format($i_val[csf('production_date')])==$search_prod_date)
				{
					$input_po_arr[$i_val[csf('serving_company')]][$i_val[csf('floor_id')]][$i_val[csf('sewing_line')]][$i_val[csf('po_break_down_id')]][change_date_format($i_val[csf('production_date')])]+=$i_val[csf('production_quantity')];
				}
			}
			else
			{
				$input_output_po_arr[$i_val[csf('serving_company')]][$i_val[csf('floor_id')]][$i_val[csf('sewing_line')]][$i_val[csf('po_break_down_id')]]['output']+=$i_val[csf('production_quantity')];
			}
		}
		unset($resout_input_output);
		//print_r($input_output_po_arr);

		/* *********************************************************************************************************
		/ 											subcoutact data													/
		********************************************************************************************************  */

	    if($db_type==0)
	    {
			$sql_sub_contuct= "SELECT  a.company_id, a.location_id, a.floor_id, a.production_date, a.line_id,b.party_id  as buyer_name,a.order_id,c.order_no as po_number,c.cust_style_ref,max(c.smv) as smv,
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
	   		$sql_sub_contuct.="sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a  where a.production_type in (2,7) and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.prod_reso_allo=1 and a.company_id in (".$comapny_id.") $subcon_location $floor $subcon_line   $txt_date_from group by a.company_id, a.location_id, a.floor_id,a.order_id, a.production_date,a.prod_reso_allo,a.line_id,b.party_id,c.order_no,c.cust_style_ref order by a.location_id";
		}
		else
		{
			$sql_sub_contuct= "SELECT  a.company_id, a.location_id, a.floor_id, a.production_date, a.line_id,b.party_id  as buyer_name,a.order_id,c.order_no as po_number,c.cust_style_ref,max(c.smv) as smv,
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

		   	$sql_sub_contuct.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]'	and a.production_type=5 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a  where a.production_type in (2,7) and a.prod_reso_allo=1 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id in(".$comapny_id.") $subcon_location $floor $subcon_line   $txt_date_from group by a.company_id, a.location_id, a.floor_id,a.order_id, a.production_date, a.line_id,b.party_id,c.order_no,c.cust_style_ref ";

		}
		//echo $sql_sub_contuct;die;
		$sub_result=sql_select($sql_sub_contuct);
		$subcon_order_smv=array();
		foreach($sub_result as $subcon_val)
		{


			$sewing_line_id=$prod_reso_arr[$subcon_val[csf('sewing_line')]];

			$production_po_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$subcon_val[csf('good_qnty')];
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['input_qnty']+=$subcon_val[csf('input_qnty')];

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
				$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['style'].=",".$subcon_val[csf('cust_style_ref')];
			}
			else
			{
				$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number']=$subcon_val[csf('po_number')];
				$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['style']=$subcon_val[csf('cust_style_ref')];
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
		unset($sub_result);


		// echo "<pre>";var_dump($production_serial_arr);die;
	    $avable_min=0;
		$today_product=0;
	    $floor_name="";
	    $floor_man_power=0;
		$floor_operator=$floor_produc_min=0;
		$floor_smv=$floor_row=$floor_helper=$floor_tgt_h=$floor_days_run=$floor_working_hour=$line_floor_production=$floor_today_product=$floor_avale_minute=$floor_avale_hour=0;
		$total_operator=$total_helper=$gnd_hit_rate=0;
	    $total_smv=$total_terget=$grand_total_product=$gnd_line_effi=0;
	    $total_man_power=$gnd_avable_min=$gnd_avable_hour=$gnd_product_min=0;
		$item_smv=$item_smv_total=$line_efficiency=$over_all_line_efficiency=$days_run=$total_working_hour=$gnd_total_tgt_h=$total_capacity=0;
		$j=1;
		ob_start();
		$line_number_check_arr=array();
		$smv_for_item="";
		$total_production=array();
		$floor_production=array();
	    $line_floor_production=0;
	    $line_total_production=0; $gnd_total_fob_val=0; $gnd_final_total_fob_val=0;

		// echo "<pre>";print_r($prod_resource_array);die;
	$type=0;
	if ($type == 0)
	{
		$html.='<tbody>';
		$floor_html.='<tbody>';
		foreach($prod_resource_array as $company_id=>$com_name)
		{
			$global_start_lanch=$start_time_arr[$company_id][1]['lst'];

			$html.='<tr  bgcolor="#E8FFFF">
						<td width="" colspan="32"><strong>Company Name:'.$companyArr[$company_id].'</strong></td>';
						for($k=$hour; $k<=$last_hour; $k++)
						{
							$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
							if($k==$last_hour) $hourwidth=''; else $hourwidth='50';
							$html.='<td align="right" width='.$hourwidth.' style='.$bg_color.' ></td>';
						}


			$html.='<td></td></tr>';

			$floor_html.='<tr  bgcolor="#CFCFA0">
						<td width="" colspan="19"><strong>Company Name:'.$companyArr[$company_id].'</strong></td>';

						for($k=$hour; $k<=$last_hour; $k++)
						{
							$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
							if($k==$last_hour) $hourwidth=''; else $hourwidth='50';
							$floor_html.='<td align="right" width='.$hourwidth.' style='.$bg_color.' ></td>';
						}
			$floor_html.='</tr>';

			foreach($com_name as $lo_id=>$lo_name)
			{
				ksort($lo_name);
				foreach($lo_name as $f_id=>$fname)
				{
					foreach($fname as $resource_id=>$resource_data)
					{
						$germents_item=array_unique(explode('****',$production_data_arr[$f_id][$resource_id]['item_number_id']));

						$buyer_neme_all=array_unique(explode(',',$production_data_arr[$f_id][$resource_id]['buyer_name']));
						$buyer_name="";
						foreach($buyer_neme_all as $buy)
						{
							if($buyer_name!='') $buyer_name.=',';
							$buyer_name.=$buyerArr[$buy];
						}
						$garment_itemname='';
						$item_smv="";$item_ids='';
						$smv_for_item="";
						$produce_minit=0;
						$order_no_total="";
						$efficiency_min=0;
						$over_all_effi_min=0;
						$efficiency_hour=0;
						$tot_po_qty=0;$fob_val=0;$days_run=0;
						$total_input=0; $total_output=0; $min_input_date=''; $total_wip=0; $line_cm_value=0;
						$today_input=0; $total_smv_achive=0;
						foreach($germents_item as $g_val)
						{

							$po_garment_item=explode('**',$g_val);
							if($garment_itemname!='') $garment_itemname.=',';
							$garment_itemname.=$garments_item[$po_garment_item[1]];

							if($item_ids=='') $item_ids=$po_garment_item[1];else $item_ids.=",".$po_garment_item[1];

							$total_input+=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input'];
							$total_output+=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['output'];
							if($input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input_date']!='')
							{
								if($min_input_date!='')
								{
									if(strtotime($input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input_date'])<strtotime($min_input_date))
									{
										$min_input_date=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input_date'];
									}
								}
								else
								{
									$min_input_date=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input_date'];
								}
							}
							//echo $company_id."*".$f_id."*".$resource_id."*".$po_garment_item[0]."*".$search_prod_date;
							//print_r($input_po_arr);die;
						    //echo $input_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]][$search_prod_date]."**";
							//$today_input+=$input_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]][$search_prod_date];
							$today_input+=$production_data_arr[$f_id][$resource_id]['input_qnty'];

							$tot_po_qty+=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];
							$tot_po_amt+=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt'];
							if($item_smv!='') $item_smv.='/';
							//echo $po_garment_item[0].'='.$po_garment_item[1];
							$item_smv.=$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];


							$total_smv_achive+=$input_output_po_arr[$po_garment_item[0]]['output']*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];

							if($order_no_total!="") $order_no_total.=",";
							$order_no_total.=$po_garment_item[0];
							if($smv_for_item!="") $smv_for_item.="****".$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]]."**".$po_garment_item[1];
							else
							$smv_for_item=$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]]."**".$po_garment_item[1];

							$produce_minit+=$production_po_data_arr[$f_id][$resource_id][$po_garment_item[0]][$po_garment_item[1]]*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
							// echo $production_po_data_arr[$f_id][$resource_id][$po_garment_item[0]][$po_garment_item[1]]."*".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]]."<br>";

							$fob_rate=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt']/$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];

							$prod_qty=$production_data_arr_qty[$f_id][$resource_id][$po_garment_item[0]][$po_garment_item[1]]['quantity'];

							$dzn_qnty=0; $cm_value=0;
							if($costing_per_arr[$po_garment_item[2]]==1) $dzn_qnty=12;
							else if($costing_per_arr[$po_garment_item[2]]==3) $dzn_qnty=12*2;
							else if($costing_per_arr[$po_garment_item[2]]==4) $dzn_qnty=12*3;
							else if($costing_per_arr[$po_garment_item[2]]==5) $dzn_qnty=12*4;
							else $dzn_qnty=1;

							$dzn_qnty=$dzn_qnty*$po_garment_item[3];
							$cm_value=($tot_cost_arr[$po_garment_item[2]]/$dzn_qnty)*$prod_qty;
							// echo $tot_cost_arr[$po_garment_item[2]]."/".$dzn_qnty."*".$prod_qty."<br>";
							if(is_nan($cm_value)){ $cm_value=0; }

							$line_cm_value+=$cm_value;
							if(is_nan($fob_rate)){ $fob_rate=0; }
							$fob_val+=$prod_qty*$fob_rate;
						}
						//echo $today_input;die;
						//$fob_rate=$tot_po_amt/$tot_po_qty;
						$subcon_po_id=array_unique(explode(',',$production_data_arr[$f_id][$resource_id]['order_id']));
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
							$produce_minit+=$production_po_data_arr[$f_id][$resource_id][$sub_val]*$subcon_order_smv[$sub_val];
							if($subcon_order_id!="") $subcon_order_id.=",";
							$subcon_order_id.=$sub_val;
						}
						$produce_minit+=$resource_data["smv_adjustment_plus"]-$resource_data["smv_adjustment_minus"];


						if($min_input_date!="")
						{
							$days_run=datediff("d",$min_input_date,$pr_date);
						}
						else  $days_run=0;

						$type_line=$production_data_arr[$f_id][$resource_id]['type_line'];
						$prod_reso_allo=$production_data_arr[$f_id][$resource_id]['prod_reso_allo'];

						$sewing_line='';
						$line_number=explode(",",$resource_data['line_number']);
						foreach($line_number as $val)
						{
							if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
						}


				//******************************************************************************************************************
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
					 //******************************************************************************************************
						$production_hour=array();
						for($h=$hour;$h<=$last_hour;$h++)
						{
							 $prod_hour="prod_hour".substr($line_start_hour_arr[$h],0,2)."";
							 $production_hour[$prod_hour]=$production_data_arr[$f_id][$resource_id][$prod_hour];
							 $company_production[$prod_hour]+=$production_data_arr[$f_id][$resource_id][$prod_hour];
							 $floor_production[$prod_hour]+=$production_data_arr[$f_id][$resource_id][$prod_hour];
							 $total_production[$prod_hour]+=$production_data_arr[$f_id][$resource_id][$prod_hour];
						}


						// print_r($production_hour);
						$floor_production['prod_hour24']+=$production_data_arr[$f_id][$resource_id]['prod_hour23'];
						$total_production['prod_hour24']+=$production_data_arr[$f_id][$resource_id]['prod_hour23'];
						$production_hour['prod_hour24']=$production_data_arr[$f_id][$resource_id]['prod_hour23'];
						$company_production['prod_hour24']=$production_data_arr[$f_id][$resource_id]['prod_hour23'];
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
									$line_production_hour+=$production_data_arr[$f_id][$resource_id][$line_hour];
									$line_floor_production+=$production_data_arr[$f_id][$resource_id][$line_hour];
									$line_total_production+=$production_data_arr[$f_id][$resource_id][$line_hour];
									$actual_time_hour=$start_hour_arr[$lh+1];
								}
							}
							//echo $total_eff_hour.'aaaa';
							if($start_hour_arr[$actual_time]>$lunch_start_hour) $total_eff_hour=$total_eff_hour-1;

							if($total_eff_hour>$production_data_arr[$f_id][$resource_id]['working_hour'])
							{
								 $total_eff_hour=$production_data_arr[$f_id][$resource_id]['working_hour'];
							}
						}

						if(str_replace("'","",$actual_production_date)<=str_replace("'","",$actual_date))
						{
							for($ah=$hour;$ah<=$last_hour;$ah++)
							{
								$prod_hour="prod_hour".substr($start_hour_arr[$ah],0,2)."";
								$line_production_hour+=$production_data_arr[$f_id][$resource_id][$prod_hour];
								//echo $production_data_arr[$f_id][$ldata][$prod_hour];
								$line_floor_production+=$production_data_arr[$f_id][$resource_id][$prod_hour];
								$line_total_production+=$production_data_arr[$f_id][$resource_id][$prod_hour];
								$actual_time_hour=$start_hour_arr[$ah+1];
							}

							$total_eff_hour=$resource_data['working_hour'];
						}

						//rtdfgdfgfd 88888888888888888888888888888888888888888888888888888888888888888

						if($cbo_no_prod_type==2)// && $line_production_hour>0
						{
							$current_wo_time=0;
							if($current_date==$search_prod_date)
							{
								$prod_wo_hour=$total_eff_hour;
								// if ($dif_time<$prod_wo_hour)//
								// {
									// $current_wo_time=$dif_hour_min;
									// $cla_cur_time=$dif_time;
									$current_hour_min=date('H:i');
									$line_shift_hour_min=$line_wise_shift_arr[$resource_id];
									$timeDiff=datediff("n",$line_shift_hour_min,$current_hour_min);
									$time_dif=number_format($timeDiff/60,2);
									if(strtotime(date('H:i'))>strtotime($line_wise_shift_lunch_arr[$resource_id]) && $line_wise_shift_lunch_arr[$resource_id]!="")
									{
										/* $cur_h_m =date('H:i:s');
										$line_wise_shift_lunch_h_m = $line_wise_shift_lunch_arr[$resource_id].":00";
										$timeDiff=datediff("n",$line_wise_shift_lunch_h_m,$cur_h_m);
										if($timeDiff>60)
										{
											$cla_cur_time=$dif_time-1;
										}
										else
										{
											$lunchMin=number_format($timeDiff/60,2);
											$cla_cur_time=$dif_time-$lunchMin;
										} */
										$line_wise_shift_lunch_h_m = $line_wise_shift_lunch_arr[$resource_id];
										$lunchTimeDiff=datediff("n",$line_wise_shift_lunch_h_m,$current_hour_min);
										if($lunchTimeDiff>60)
										{
											$cla_cur_time=$time_dif-1;
										}
										else
										{
											$lunchMin=number_format($lunchTimeDiff/60,2);
											$cla_cur_time=$time_dif-$lunchMin;
										}
									}
									else
									{
										$cla_cur_time=$dif_time;
									}
									$current_wo_time=$dif_hour_min;
								// }
								// else
								// {
								// 	$current_wo_time=$prod_wo_hour;
								// 	$cla_cur_time=$prod_wo_hour;
								// }
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
							$over_all_effi_min+=($resource_data['man_power'])*$cla_cur_time*60;
							$efficiency_hour+=($efficiency_min/60)/$resource_data['man_power'];
							$line_efficiency=(($produce_minit)*100)/$efficiency_min;
							$over_all_line_efficiency=(($produce_minit)*100)/$over_all_effi_min;


							//****************************************************************************************************************

						    //echo $today_input;die;

							$man_power=$resource_data['man_power'];
							$operator=$resource_data['operator'];
							$helper=$resource_data['helper'];
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
							$floor_avale_hour+=$efficiency_hour;
							$floor_avale_minute+=$efficiency_min;
							$over_all_floor_avale_minute+=$over_all_effi_min;
							$floor_produc_min+=$produce_minit;
							$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;

							$floor_cm_value+=$line_cm_value;
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
							$grand_total_terget+=$eff_target;
							$grand_total_product+=$line_production_hour;
							$gnd_avable_hour+=$efficiency_hour;
							$gnd_avable_min+=$efficiency_min;
							$over_all_gnd_avable_min+=$over_all_effi_min;
							$gnd_product_min+=$produce_minit;
							$gnd_total_fob_val+=$fob_val;
							$gnd_final_total_fob_val+=$fob_val;

							$grand_today_input+=$today_input;
							$grand_total_input+=$total_input;
							$grand_total_output+=$total_output;
							$grand_total_wip+=($total_input-$total_output);
							$grand_cm_value+=$line_cm_value;


							$company_today_input+=$today_input;
							$company_total_input+=$total_input;
							$company_total_output+=$total_output;
							$company_total_wip+=($total_input-$total_output);
							$company_operator+=$resource_data['operator'];
							$company_man_power+=$resource_data['man_power'];
							$company_helper+=$resource_data['helper'];
							$company_capacity+=$resource_data['capacity'];
							$company_working_hour+=$resource_data['working_hour'];
							$company_total_tgt_h+=$resource_data['terget_hour'];
							$company_total_terget+=$eff_target;
							$company_total_product+=$line_production_hour;
							$company_avable_hour+=$efficiency_hour;
							$company_avable_min+=$efficiency_min;
							$over_all_company_avable_min+=$over_all_effi_min;
							$company_product_min+=$produce_minit;
							$company_total_fob_val+=$fob_val;
							$company_final_total_fob_val+=$fob_val;
							$company_cm_value+=$line_cm_value;

							$floor_total_smv_achive+=$total_smv_achive;
							$company_total_smv_achive+=$total_smv_achive;
							$grand_total_smv_achive+=$total_smv_achive;

							$floor_total_machine+=$resource_data['machine'];
							$company_total_machine+=$resource_data['machine'];
							$grand_total_machine+=$resource_data['machine'];

							$floor_total_target_efficiency+=$resource_data['target_efficiency'];
							$company_total_target_efficiency+=$resource_data['target_efficiency'];
							$grand_total_target_efficiency+=$resource_data['target_efficiency'];
							$floor_target_efficiency_count++;
							$company_target_efficiency_count++;
							$grand_target_efficiency_count++;

							$po_id=rtrim($production_data_arr[$f_id][$resource_id]['po_id'],',');
							$po_id=array_unique(explode(",",$po_id));
							$style=rtrim($production_data_arr[$f_id][$resource_id]['style']);
							$style=implode(",",array_unique(explode(",",$style)));

							$cbo_get_upto=str_replace("'","",$cbo_get_upto);
							$txt_parcentage=str_replace("'","",$txt_parcentage);
						   //********************************* calclution floor total    ****************************************************$pr_date],$sewing_day
							$floor_name=$floorArr[$f_id];
							$floor_smv+=$item_smv;

							$floor_days_run+=$days_run;

							$po_id=$production_data_arr[$f_id][$resource_id]['po_id'];//$item_ids//$subcon_order_id
							$styles=explode(",",$style);
							$style_button='';//
							foreach($styles as $sid)
							{
								if( $style_button=='')
								{
									$style_button="<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$resource_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
								}
								else
								{
									$style_button.=", "."<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$resource_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
								}
							}
							$as_on_current_hour_target=0; $as_on_current_hour_variance=0;
							$as_on_current_hour_target=$terget_hour*$cla_cur_time;
							$as_on_current_hour_variance=$line_production_hour-$as_on_current_hour_target;

							$dateTimeObject1 = date_create($line_wise_shift_arr[$resource_id].":00");
							$dateTimeObject2 = date_create(date('H:i:s'));
							$dateTimeObject3 = date_create($line_wise_shift_lunch_arr[$resource_id].":00");
							// $dateTimeObject1 = date_create('17:13:00');
							// $dateTimeObject2 = date_create('12:13:00');

							// $difference = date_diff($dateTimeObject1, $dateTimeObject2);
							// $curent_hour = $difference->h.":".$difference->i;
							if($current_date==$search_prod_date)
							{
								if(strtotime(date('H:i'))>strtotime($line_wise_shift_lunch_arr[$resource_id]) && $line_wise_shift_lunch_arr[$resource_id]!="")
								{
									$difference_lunch = date_diff($dateTimeObject3, $dateTimeObject2);
									$curent_hour_min_arr = explode(":",date('H:i'));
									if($difference_lunch->h > 1 || $difference_lunch->h == 1)
									{
										$hour_cal=$curent_hour_min_arr[0]-1;
										$dateTimeObject4 = date_create($hour_cal.":".$curent_hour_min_arr[1].":00");
									}
									else
									{
										// $min=$curent_hour_min_arr[1]-$difference_lunch->i;
										// $dateTimeObject4 = date_create($curent_hour_min_arr[0].":".$min.":00");
										if($curent_hour_min_arr[1] > $difference_lunch->i)
										{
											$min=$curent_hour_min_arr[1]-$difference_lunch->i;
											$dateTimeObject4 = date_create($curent_hour_min_arr[0].":".$min.":00");
										}
										else if($difference_lunch->i > $curent_hour_min_arr[1])
										{
											$min_cal=$difference_lunch->i - $curent_hour_min_arr[1];
											$hour_cal=$curent_hour_min_arr[0]-1;
											$min=60-$min_cal;
											$dateTimeObject4 = date_create($hour_cal.":".$min.":00");
										}
										else
										{
											$dateTimeObject4 = date_create($curent_hour_min_arr[0].":00:00");
										}
									}

									$difference = date_diff($dateTimeObject1, $dateTimeObject4);
									$curent_hour = $difference->h.":".$difference->i;
								}
								else
								{
									$difference = date_diff($dateTimeObject1, $dateTimeObject2);
									$curent_hour = $difference->h.":".$difference->i;
								}
							}
							else
							{
								$curent_hour = $total_eff_hour;
							}

							if($line_efficiency<=$txt_parcentage) $efficiency_color="#FF0000"; else $efficiency_color="#FFFFFF";
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$itmSmvArray = explode("/", $item_smv);
							$newStringSMV = "";
							foreach($itmSmvArray as $itmSMV){
								$newStringSMV .= number_format((float)$itmSMV, 2)."/";
							}
							$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
							$html.='<td width="40">'.$i.'&nbsp;</td>
									<td width="80">'.$floor_name.'&nbsp; </td>
									<td align="center" width="80" >'. $sewing_line.'&nbsp; </td>
									<td width="80"><p>'.$buyer_name.'&nbsp;</p></td>
									<td width="140"><p>'.$production_data_arr[$f_id][$resource_id]['grouping'].'&nbsp;</p></td>
									<td width="100"><p>'.$style_button.'&nbsp;</p></td>
									<td width="120" style="word-wrap:break-word; word-break: break-all;">'.$garment_itemname.'</td>
									<td align="right" width="60"><p>'.$newStringSMV.'</p></td>

									<td align="right" width="70">'.$terget_hour.'</td>
									<td align="right" width="60">'.$days_run.'</td>
									<td align="right" width="60">'.$working_hour.'</td>
									<td align="right" width="60">'.$curent_hour.'</td>
									<td align="right" width="80">'.$eff_target.'</td>';
									$string="'";
									$html.='<td width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'4','".$company_id."',".$txt_date.')">'.$today_input.'</a></td>
									<td width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'5','".$company_id."',".$txt_date.')">'.$line_production_hour.'</a></td>

									<td align="right" width="80">'.$as_on_current_hour_variance.'</td>
									<td width="80" title='.$fob_rate.' align="right"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$resource_id.",'tot_fob_value_popup','".$smv_for_item."','".$actual_time_hour."','".$line_start."',".$txt_date.')">'.number_format($fob_val,2).'</a></td>
									<td align="right" width="80">'.number_format($line_cm_value,2).'</td>
									<td align="right" width="80">'.$resource_data['target_efficiency'].'%</td>
									<td width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'1','".$company_id."',".$txt_date.')">'.$total_input.'</a></td>
									<td width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'2','".$company_id."',".$txt_date.')">'.$total_output.'</a></td>
									<td width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'3','".$company_id."',".$txt_date.')">'.($total_wip=$total_input-$total_output).'</a></td>

									<td width="100" align="right">'.number_format($efficiency_hour,2).'</td>
									<td width="100" align="right">'.$efficiency_min.'</td>
									<td width="100" align="right"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$resource_id.",'tot_prod','".$smv_for_item."','".$actual_time_hour."','".$line_start."',".$txt_date.')">'.number_format($produce_minit,2).'</a></td>
									<td align="right" width="60" >'. number_format(($line_production_hour/$eff_target)*100,2).'%</td>';

									if($line_efficiency<=$txt_parcentage)
									{
										$html.='<td align="right" width="90" bgcolor="red">'.number_format($line_efficiency,2).'%</td>';
									}
									else
									{
										$html.='<td align="right" width="90">'.number_format($line_efficiency,2).'%</td>';
									}

									$html.='<td align="right" width="70">'.$operator.'</td>
									<td align="right" width="50">'.$helper.'</td>
									<td align="right" width="60">'.$man_power.'</td>
									<td align="right" width="100">'.$resource_data['line_chief'].'</td>';


									for($k=$hour; $k<=$last_hour; $k++)
									{
										$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
										//if($k==$last_hour) $hourwidth=''; else $hourwidth='50';

										if($start_hour_arr[$k]==$lunch_start_hour)
										{
											 $bg_color='background:yellow';
										}
										else if($terget_hour>$production_hour[$prod_hour])
										{
											$bg_color='background:red';
											if($production_hour[$prod_hour]==0)
											{
												$bg_color='';
											}
										}
										else if($terget_hour<$production_hour[$prod_hour])
										{
											$bg_color='background:green';
											if($production_hour[$prod_hour]==0)
											{
												$bg_color='';
											}
										}
										else
										{
											$bg_color="";
										}

										$html.='<td align="right" width="50"  style='.$bg_color.'>'.$production_hour[$prod_hour].'</td>';
									}
									$html.='<td><input type="button"  value="View" class="formbutton" onclick="show_line_remarks('.$cbo_company_id.",'".$order_no_total."','".$f_id."','".$resource_id."','remarks_popup',".$txt_date.')"/></td>';

							$html.='</tr>';
							$i++;
						}
						if($cbo_no_prod_type==1)
						{
							$current_wo_time=0;
							// echo $line_wise_shift_lunch_arr[$resource_id]."*";die;
							if($current_date==$search_prod_date)
							{
								$prod_wo_hour=$total_eff_hour;
								// if ($dif_time<$prod_wo_hour)//
								// {
									// $current_wo_time=$dif_hour_min;
									// $cla_cur_time=$dif_time;

									$current_hour_min=date('H:i');
									$line_shift_hour_min=$line_wise_shift_arr[$resource_id];
									$timeDiff=datediff("n",$line_shift_hour_min,$current_hour_min);
									$time_dif=number_format($timeDiff/60,2);
									if(strtotime(date('H:i'))>strtotime($line_wise_shift_lunch_arr[$resource_id]) && $line_wise_shift_lunch_arr[$resource_id]!="")
									{
										$line_wise_shift_lunch_h_m = $line_wise_shift_lunch_arr[$resource_id];
										$lunchTimeDiff=datediff("n",$line_wise_shift_lunch_h_m,$current_hour_min);
										if($lunchTimeDiff>60)
										{
											$cla_cur_time=$time_dif-1;
										}
										else
										{
											$lunchMin=number_format($lunchTimeDiff/60,2);
											$cla_cur_time=$time_dif-$lunchMin;
										}
									}
									else
									{
										$cla_cur_time=$time_dif;
									}
									$current_wo_time=$dif_hour_min;
								// }
								// else
								// {
								// 	$current_wo_time=$prod_wo_hour;
								// 	$cla_cur_time=$prod_wo_hour;
								// }
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
							$over_all_effi_min+=($resource_data['man_power'])*$cla_cur_time*60;
							// echo $total_adjustment."+(".$resource_data['man_power'].")*".$cla_cur_time."*60<br>";
							$efficiency_hour+=($efficiency_min/60)/$resource_data['man_power'];
							$line_efficiency=(($produce_minit)*100)/$efficiency_min;
							$over_all_line_efficiency=(($produce_minit)*100)/$over_all_effi_min;


							//****************************************************************************************************************

							$man_power=$resource_data['man_power'];
							$operator=$resource_data['operator'];
							$helper=$resource_data['helper'];
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
							$floor_today_product+=$today_product;
							$floor_avale_hour+=$efficiency_hour;
							$floor_avale_minute+=$efficiency_min;
							$over_all_floor_avale_minute+=$over_all_effi_min;
							$floor_produc_min+=$produce_minit;
							$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
							$over_all_floor_efficency=($floor_produc_min/$over_all_floor_avale_minute)*100;

							$floor_today_input+=$today_input;
							$floor_cm_value+=$line_cm_value;
							$floor_total_input+=$total_input;
							$floor_total_output+=$total_output;
							$floor_total_wip+=($total_input-$total_output);

							$total_operator+=$resource_data['operator'];
							$total_man_power+=$resource_data['man_power'];
							$total_helper+=$resource_data['helper'];
							$total_capacity+=$resource_data['capacity'];
							$total_working_hour+=$resource_data['working_hour'];
							$gnd_total_tgt_h+=$resource_data['terget_hour'];
							$grand_total_terget+=$eff_target;
							$grand_total_product+=$today_product;
							$gnd_avable_hour+=$efficiency_hour;
							$gnd_avable_min+=$efficiency_min;
							$over_all_gnd_avable_min+=$over_all_effi_min;
							$gnd_product_min+=$produce_minit;
							$gnd_total_fob_val+=$fob_val;
							$gnd_final_total_fob_val+=$fob_val;

							$grand_today_input+=$today_input;
							$grand_total_input+=$total_input;
							$grand_total_output+=$total_output;
							$grand_total_wip+=($total_input-$total_output);
							$grand_cm_value+=$line_cm_value;

							$company_today_input+=$today_input;
							$company_total_input+=$total_input;
							$company_total_output+=$total_output;
							$company_total_wip+=($total_input-$total_output);
							$company_operator+=$resource_data['operator'];
							$company_man_power+=$resource_data['man_power'];
							$company_helper+=$resource_data['helper'];
							$company_capacity+=$resource_data['capacity'];
							$company_working_hour+=$resource_data['working_hour'];
							$company_total_tgt_h+=$resource_data['terget_hour'];
							$company_total_terget+=$eff_target;
							$company_total_product+=$line_production_hour;
							$company_avable_hour+=$efficiency_hour;
							$company_avable_min+=$efficiency_min;
							$over_all_company_avable_min+=$over_all_effi_min;
							$company_product_min+=$produce_minit;
							$company_total_fob_val+=$fob_val;
							$company_final_total_fob_val+=$fob_val;
							$company_cm_value+=$line_cm_value;


							$floor_total_smv_achive+=$total_smv_achive;
							$company_total_smv_achive+=$total_smv_achive;
							$grand_total_smv_achive+=$total_smv_achive;


							$floor_total_machine+=$resource_data['machine'];
							$company_total_machine+=$resource_data['machine'];
							$grand_total_machine+=$resource_data['machine'];

							$floor_total_target_efficiency+=$resource_data['target_efficiency'];
							$company_total_target_efficiency+=$resource_data['target_efficiency'];
							$grand_total_target_efficiency+=$resource_data['target_efficiency'];
							$floor_target_efficiency_count++;
							$company_target_efficiency_count++;
							$grand_target_efficiency_count++;

							$po_id=rtrim($production_data_arr[$f_id][$resource_id]['po_id'],',');
							$po_id=array_unique(explode(",",$po_id));
							$style=rtrim($production_data_arr[$f_id][$resource_id]['style']);
							$style=implode(",",array_unique(explode(",",$style)));

							$cbo_get_upto=str_replace("'","",$cbo_get_upto);
							$txt_parcentage=str_replace("'","",$txt_parcentage);
						   //********************************* calclution floor total    ****************************************************$pr_date],$sewing_day
							$floor_name=$floorArr[$f_id];
							$floor_smv+=$item_smv;

							$floor_days_run+=$days_run;

							$po_id=$production_data_arr[$f_id][$resource_id]['po_id'];//$item_ids//$subcon_order_id
							$styles=explode(",",$style);
							$style_button='';//
							foreach($styles as $sid)
							{
								if( $style_button=='')
								{
									$style_button="<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$resource_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
								}
								else
								{
									$style_button.=", "."<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$resource_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
								}
							}
							$as_on_current_hour_target=0; $as_on_current_hour_variance=0;
							$as_on_current_hour_target=$terget_hour*$cla_cur_time;
							$as_on_current_hour_variance=$line_production_hour-$as_on_current_hour_target;

							$dateTimeObject1 = date_create($line_wise_shift_arr[$resource_id].":00");
							$dateTimeObject2 = date_create(date('H:i:s'));
							$dateTimeObject3 = date_create($line_wise_shift_lunch_arr[$resource_id].":00");
							// $dateTimeObject1 = date_create('17:13:00');
							// $dateTimeObject2 = date_create('12:13:00');
							if($current_date==$search_prod_date)
							{
								if(strtotime(date('H:i'))>strtotime($line_wise_shift_lunch_arr[$resource_id]) && $line_wise_shift_lunch_arr[$resource_id]!="")
								{
									$difference_lunch = date_diff($dateTimeObject3, $dateTimeObject2);
									$curent_hour_min_arr = explode(":",date('H:i'));
									if($difference_lunch->h > 1 || $difference_lunch->h == 1)
									{
										$hour_cal=$curent_hour_min_arr[0]-1;
										$dateTimeObject4 = date_create($hour_cal.":".$curent_hour_min_arr[1].":00");
									}
									else
									{
										if($curent_hour_min_arr[1] > $difference_lunch->i)
										{
											$min=$curent_hour_min_arr[1]-$difference_lunch->i;
											$dateTimeObject4 = date_create($curent_hour_min_arr[0].":".$min.":00");
										}
										else if($difference_lunch->i > $curent_hour_min_arr[1])
										{
											$min_cal=$difference_lunch->i - $curent_hour_min_arr[1];
											$hour_cal=$curent_hour_min_arr[0]-1;
											$min=60-$min_cal;
											$dateTimeObject4 = date_create($hour_cal.":".$min.":00");
										}
										else
										{
											$dateTimeObject4 = date_create($curent_hour_min_arr[0].":00:00");
										}
									}

									$difference = date_diff($dateTimeObject1, $dateTimeObject4);
									$curent_hour = $difference->h.":".$difference->i;
								}
								else
								{
									$difference = date_diff($dateTimeObject1, $dateTimeObject2);
									$curent_hour = $difference->h.":".$difference->i;
								}
							}
							else
							{
								$curent_hour = $total_eff_hour;
							}

							if($line_efficiency<=$txt_parcentage) $efficiency_color="#FF0000"; else $efficiency_color="#FFFFFF";
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$itmSmvArray = explode("/", $item_smv);
							$newStringSMV = "";
							foreach($itmSmvArray as $itmSMV){
								$newStringSMV .= number_format((float)$itmSMV, 2)."/";
							}
							$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
							$html.='<td width="40">'.$i.'&nbsp;</td>
									<td width="80">'.$floor_name.'&nbsp; </td>
									<td align="center" width="80" >'. $sewing_line.'&nbsp; </td>
									<td width="80"><p>'.$buyer_name.'&nbsp;</p></td>
									<td width="140"><p>'.$production_data_arr[$f_id][$resource_id]['grouping'].'&nbsp;</p></td>
									<td width="100"><p>'.$style_button.'&nbsp;</p></td>
									<td width="120" style="word-wrap:break-word; word-break: break-all;">'.$garment_itemname.'</td>
									<td align="right" width="60"><p>'.$newStringSMV.'</p></td>

									<td align="right" width="70">'.$terget_hour.'</td>
									<td align="right" width="60">'.$days_run.'</td>
									<td align="right" width="60">'.$working_hour.'</td>
									<td align="right" width="60">'.$curent_hour.'</td>
									<td align="right" width="80">'.$eff_target.'</td>';
									$string="'";
									$html.='<td width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'4','".$company_id."',".$txt_date.')">'.$today_input.'</a></td>
									<td width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'5','".$company_id."',".$txt_date.')">'.$line_production_hour.'</a></td>
									<td align="right" width="80">'.$as_on_current_hour_variance.'</td>
									<td width="80" title='.$fob_rate.' align="right"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$resource_id.",'tot_fob_value_popup','".$smv_for_item."','".$actual_time_hour."','".$line_start."',".$txt_date.')">'.number_format($fob_val,2).'</a></td>

									<td align="right" width="80">'.number_format($line_cm_value,2).'</td>
									<td align="right" width="80">'.$resource_data['target_efficiency'].'%</td>
									<td width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'1','".$company_id."',".$txt_date.')">'.$total_input.'</a></td>
									<td width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'2','".$company_id."',".$txt_date.')">'.$total_output.'</a></td>
									<td width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'3','".$company_id."',".$txt_date.')">'.($total_wip=$total_input-$total_output).'</a></td>


									<td width="100" align="right">'.number_format($efficiency_hour,2).'</td>
									<td width="100" align="right">'.$efficiency_min.'</td>

									<td width="100" align="right"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$resource_id.",'tot_prod','".$smv_for_item."','".$actual_time_hour."','".$line_start."',".$txt_date.')">'.number_format($produce_minit,2).'</a></td>

									<td align="right" width="60" >'. number_format(($line_production_hour/$eff_target)*100,2).'%</td>

									';

									if($line_efficiency<=$txt_parcentage)
									{
										$html.='<td align="right" width="90" bgcolor="red">'.number_format($line_efficiency,2).'%</td>';
									}
									else
									{
										$html.='<td align="right" width="90">'.number_format($line_efficiency,2).'%</td>';
									}

									$html.='<td align="right" width="90">'.number_format($over_all_line_efficiency,2).'%</td>
									<td align="right" width="70">'.$operator.'</td>
									<td align="right" width="50">'.$helper.'</td>
									<td align="right" width="60">'.$man_power.'</td>

									<td align="right" width="100">'.$resource_data['line_chief'].'</td>';

									for($k=$hour; $k<=$last_hour; $k++)
									{
										$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";

										if($start_hour_arr[$k]==$lunch_start_hour)
										{
											 $bg_color='background:yellow';
										}
										else if($terget_hour>$production_hour[$prod_hour])
										{
											$bg_color='background:red';
											if($production_hour[$prod_hour]==0)
											{
												$bg_color='';
											}
										}
										else if($terget_hour<$production_hour[$prod_hour])
										{
											$bg_color='background:green';
											if($production_hour[$prod_hour]==0)
											{
												$bg_color='';
											}
										}
										else
										{
											$bg_color="";
										}

										$html.='<td align="right" width="50"  style='.$bg_color.'>'.$production_hour[$prod_hour].'</td>';
										//$html.='<td align="right" width="50"  style=" background-color:#FFFF66" >'.$production_hour[$prod_hour].'&nbsp;kk</td>';
									}
							$html.='<td><input type="button"  value="View" class="formbutton" onclick="show_line_remarks('.$cbo_company_id.",'".$order_no_total."','".$f_id."','".$resource_id."','remarks_popup',".$txt_date.')"/></td>';

							$html.='</tr>';
							$i++;
						}
						//echo $floor_cm_value."***";die;
					}

					if($cbo_no_prod_type==2 && $line_floor_production>0)
					{
						$html.='<tr  bgcolor="#B6B6B6">
								<td width="40">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="140">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="120">&nbsp;</td>
								<td align="right" width="60">&nbsp;</td>

								<td align="right" width="70">'. $floor_tgt_h.'</td>
								<td align="right" width="60">'. $floor_days_run.'</td>

								<td align="right" width="60">'. $floor_working_hour.'</td>
								<td align="right" width="60">&nbsp;</td>
								<td align="right" width="80">'.$eff_target_floor.'</td>
								<td align="right" width="80">'.$floor_today_input.'</td>
								<td align="right" width="80">'.$line_floor_production.'</td>
								<td align="right" width="80">'. ($line_floor_production-$eff_target_floor).';</td>
								<td align="right" width="80">'.number_format($gnd_total_fob_val,2).'</td>
								<td align="right" width="80">'.number_format($floor_cm_value,2).'</td>
								<td align="right" width="80">'.number_format($floor_total_target_efficiency/$floor_target_efficiency_count,2).'</td>
								<td align="right" width="80">'. $floor_total_input.'</td>
								<td align="right" width="80">'. $floor_total_output.'</td>
								<td align="right" width="80">'. $floor_total_wip.'</td>

								<td align="right" width="100">'. number_format($floor_avale_hour,2).'</td>
								<td align="right" width="100">'. $floor_avale_minute.'</td>
								<td align="right" width="100">'. number_format($floor_produc_min, 2).'</td>
								<td align="right" width="60">'. number_format(($line_floor_production/$eff_target_floor)*100,2).'%</td>
								<td align="right" width="90">'.number_format($floor_efficency,2).' %</td>
								<td align="right" width="90">'.number_format($over_all_floor_efficency,2).' %</td>

								<td align="right" width="70">'.$floor_operator.'</td>
								<td align="right" width="50">'. $floor_helper.'</td>
								<td align="right" width="60">'. $floor_man_power.'</td>
								<td align="right" width="100"></td>
								';

								$gnd_total_fob_val=0;
								for($k=$hour; $k<=$last_hour; $k++)
								{
									$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";

									if($start_hour_arr[$k]==$global_start_lanch)
									{
										 $bg_color='background:yellow';
									}
									if($floor_tgt_h>$floor_production[$prod_hour])
									{
										$bg_color='background:red';
										if($floor_production[$prod_hour]==0)
										{
											$bg_color='';
										}
									}
									else
									{
										 $bg_color='';
									}


									$html.='<td align="right" width="50" style='.$bg_color.' >'. $floor_production[$prod_hour].'</td>';
								}

							$html.='<td align="right" width=""></td></tr>';
							if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$floor_html.="<tr bgcolor='$bgcolor' onclick=change_color('floor_$j','$bgcolor') id=floor_$j>";
							$floor_html.='<td width="40">'.$j.'&nbsp;</td>
										<td width="80" align="center">'.$floor_name.'&nbsp; </td>
										<td width="70" align="right">'. $floor_tgt_h.'</td>

										<td align="right" width="60">'. $floor_working_hour.'</td>
										<td align="right" width="80">'. $eff_target_floor.'</td>
										<td align="right" width="80">'.$floor_today_input.'</td>
										<td align="right" width="80">'.$line_floor_production.'</td>
										<td align="right" width="80">'. ($line_floor_production-$eff_target_floor).'</td>
										<td align="right" width="100">'. $floor_avale_minute.'</td>
										<td align="right" width="100">'. number_format($floor_produc_min, 2).'</td>
										<td align="right" width="90">'. number_format(($line_floor_production/$eff_target_floor)*100,2).'%</td>';
										if($floor_efficency<=$txt_parcentage)
										{
											$floor_html.='<td align="right" width="90" bgcolor="red">'.number_format($floor_efficency,2).' %</td>';
										}
										else
										{
											$floor_html.='<td align="right" width="90" >'.number_format($floor_efficency,2).' %</td>';
										}

							$floor_html.='
							<td align="right" width="90">'.number_format($over_all_floor_efficency,2).' %</td><td align="right" width="80">'. $floor_total_input.'</td>
										<td align="right" width="80">'. $floor_total_output.'</td>
										<td align="right" width="80">'. $floor_total_wip.'</td>

										<td width="70" align="right">'.$floor_operator.'</td>
										<td width="50" align="right">'. $floor_helper.'</td>
										<td align="right" width="60">'. $floor_man_power.'</td>';

										for($k=$hour; $k<=$last_hour; $k++)
										{
											$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
											if($start_hour_arr[$k]==$global_start_lanch)
											{
											$floor_html.='<td align="right" width="50" style=" background-color:#FFFF66" >'. $floor_production[$prod_hour].'</td>';
											}
											else
											{
											$floor_html.='<td align="right" width="50" >'. $floor_production[$prod_hour].'</td>';
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
						  $floor_avale_hour=0;
						  $floor_avale_minute=0;
						  $over_all_floor_avale_minute=0;
						  $floor_produc_min=0;
						  $floor_efficency=0;
						  $over_all_floor_efficency=0;
						  $floor_man_power=0;
						  $floor_capacity=0;
						  $floor_total_target_efficiency=0;
						  $floor_target_efficiency_count=0;
						  $floor_total_machine=0;
						  $floor_today_input=0;
						  $floor_total_input=0;
						  $floor_total_output=0;
						 $floor_total_wip=0;
						 $floor_cm_value=0;
					$j++;
					}
					if($cbo_no_prod_type==1)
					{
						$html.='<tr  bgcolor="#B6B6B6">
								<td width="40">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="140">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="120">&nbsp;</td>
								<td align="right" width="60">&nbsp;</td>

								<td align="right" width="70">'. $floor_tgt_h.'</td>
								<td align="right" width="60">'. $floor_days_run.'</td>

								<td align="right" width="60">'. $floor_working_hour.'</td>
								<td align="right" width="60">&nbsp;</td>
								<td align="right" width="80">'.$eff_target_floor.'</td>
								<td align="right" width="80">'.$floor_today_input.'</td>
								<td align="right" width="80">'.$line_floor_production.'</td>
								<td align="right" width="80">'. ($line_floor_production-$eff_target_floor).';</td>
								<td align="right" width="80">'.number_format($gnd_total_fob_val,2).'</td>
								<td align="right" width="80">'.number_format($floor_cm_value,2).'</td>
								<td align="right" width="80">'.number_format($floor_total_target_efficiency/$floor_target_efficiency_count,2).'%</td>
								<td align="right" width="80">'. $floor_total_input.'</td>
								<td align="right" width="80">'. $floor_total_output.'</td>
								<td align="right" width="80">'. $floor_total_wip.'</td>

								<td align="right" width="100">'. number_format($floor_avale_hour,2).'</td>
								<td align="right" width="100">'. $floor_avale_minute.'</td>
								<td align="right" width="100">'. number_format($floor_produc_min, 2).'</td>
								<td align="right" width="60">'. number_format(($line_floor_production/$eff_target_floor)*100,2).'%</td>
								<td align="right" width="90">'.number_format($floor_efficency,2).' %</td>
								<td align="right" width="90">'.number_format($over_all_floor_efficency,2).' %</td>

								<td align="right" width="70">'.$floor_operator.'</td>
								<td align="right" width="50">'. $floor_helper.'</td>
								<td align="right" width="60">'. $floor_man_power.'</td>
								<td align="right" width="100"></td>
								';

								$gnd_total_fob_val=0;
								for($k=$hour; $k<=$last_hour; $k++)
								{
									$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";

									if($start_hour_arr[$k]==$global_start_lanch)
									{
										 $bg_color='background:yellow';
									}
									if($floor_tgt_h>$floor_production[$prod_hour])
									{
										$bg_color='background:red';
										if($floor_production[$prod_hour]==0)
										{
											$bg_color='';
										}
									}
									else
									{
										 $bg_color='';
									}


									$html.='<td align="right" width="50" style='.$bg_color.' >'. $floor_production[$prod_hour].'</td>';
								}

							$html.='<td align="right" width=""></td></tr>';
							if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$floor_html.="<tr bgcolor='$bgcolor' onclick=change_color('floor_$j','$bgcolor') id=floor_$j>";
							$floor_html.='<td width="40">'.$j.'&nbsp;</td>
										<td width="80" align="center">'.$floor_name.'&nbsp; </td>
										<td width="70" align="right">'. $floor_tgt_h.'</td>

										<td align="right" width="60">'. $floor_working_hour.'</td>
										<td align="right" width="80">'. $eff_target_floor.'</td>
										<td align="right" width="80">'.$floor_today_input.'</td>
										<td align="right" width="80">'.$line_floor_production.'</td>
										<td align="right" width="80">'. ($line_floor_production-$eff_target_floor).'</td>
										<td align="right" width="100">'. $floor_avale_minute.'</td>
										<td align="right" width="100">'. number_format($floor_produc_min, 2).'</td>
										<td align="right" width="90">'. number_format(($line_floor_production/$eff_target_floor)*100,2).'%</td>
										';
										if($floor_efficency<=$txt_parcentage)
										{
											$floor_html.='<td align="right" width="90" bgcolor="red">'.number_format($floor_efficency,2).' %</td>';
										}
										else
										{
											$floor_html.='<td align="right" width="90" >'.number_format($floor_efficency,2).' %</td>';
										}

										$floor_html.='
										<td align="right" width="90">'.number_format($over_all_floor_efficency,2).' %</td>
										<td align="right" width="80">'. $floor_total_input.'</td>
											<td align="right" width="80">'. $floor_total_output.'</td>
											<td align="right" width="80">'. $floor_total_wip.'</td>

											<td width="70" align="right">'.$floor_operator.'</td>
											<td width="50" align="right">'. $floor_helper.'</td>
											<td align="right" width="60">'. $floor_man_power.'</td>';

										for($k=$hour; $k<=$last_hour; $k++)
										{
											$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
											if($start_hour_arr[$k]==$global_start_lanch)
											{
											$floor_html.='<td align="right" width="50" style=" background-color:#FFFF66" >'. $floor_production[$prod_hour].'</td>';
											}
											else
											{
											$floor_html.='<td align="right" width="50" >'. $floor_production[$prod_hour].'</td>';
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
									  $floor_avale_hour=0;
									  $floor_avale_minute=0;
									  $floor_produc_min=0;
									  $floor_efficency=0;
									  $over_all_floor_efficency=0;
									  $floor_man_power=0;
									  $floor_capacity=0;
									  $floor_total_target_efficiency=0;
									  $floor_target_efficiency_count=0;
									  $floor_total_machine=0;
									  $floor_today_input=0;
									  $floor_total_input=0;
									  $floor_total_output=0;
									 $floor_total_wip=0;
									 $floor_cm_value=0;
								$j++;
					}
				}
			}

			// company -total last
			$html.='<tr class="tbl_bottom">
					<td width="700" colspan="8">'.$companyArr[$company_id].' Company Total</td>
					<td align="right" width="70">'. $company_total_tgt_h.'</td>
					<td align="right" width="60"></td>

					<td align="right" width="60">'. $company_working_hour.'</td>
					<td align="right" width="60">&nbsp;</td>
					<td align="right" width="80">'.$company_total_terget.'</td>
					<td align="right" width="80">'.$company_today_input.'</td>

					<td align="right" width="80">'.$company_total_product.'</td>
					<td align="right" width="80">'. ($company_total_product-$company_total_terget).'</td>
					<td align="right" width="80">'.number_format($company_final_total_fob_val,2).'</td>
					<td align="right" width="80">'.number_format($company_cm_value,2).'</td>
					<td align="right" width="80">'.number_format($company_total_target_efficiency/$company_target_efficiency_count,2).'%</td>
					<td align="right" width="80">'. $company_total_input.'</td>
					<td align="right" width="80">'. $company_total_output.'</td>
					<td align="right" width="80">'. $company_total_wip.'</td>

					<td align="right" width="100">'. number_format($company_avable_hour,2).'</td>
					<td align="right" width="100">'. $company_avable_min.'</td>
					<td align="right" width="100">'. number_format($company_product_min, 2).'</td>
					<td align="right" width="60">'. number_format(($company_total_product/$company_total_terget)*100,2).'%</td>
					<td align="right" width="90">'.number_format(($company_product_min*100/$company_avable_min),2).' %</td>
					<td align="right" width="90">'.number_format(($company_product_min*100/$over_all_company_avable_min),2).' %</td>

					<td align="right" width="70">'.$company_operator.'</td>
					<td align="right" width="50">'. $company_helper.'</td>
					<td align="right" width="60">'. $company_man_power.'</td>
					<td align="right" width="70"></td>';


					//$gnd_total_fob_val=0;
					for($k=$hour; $k<=$last_hour; $k++)
					{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
						//if($k==$last_hour) $hourwidth=''; else $hourwidth='50';

						if($start_hour_arr[$k]==$global_start_lanch)
						{
							 $bg_color='background:yellow';
						}
						if($floor_tgt_h>$floor_production[$prod_hour])
						{
							$bg_color='background:red';
							if($floor_production[$prod_hour]==0)
							{
								$bg_color='';
							}
						}
						else
						{
							 $bg_color='';
						}
						$html.='<td align="right" width="50" style='.$bg_color.' >'. $company_production[$prod_hour].'</td>';
					}


			$html.='<td align="right" width=""></td></tr>';

			$floor_html.='<tr class="tbl_bottom">
						<td width="120" colspan="2">Company Total 2</td>
						<td width="70" align="right">'. $company_total_tgt_h.'</td>

						<td align="right" width="60">'. $company_working_hour.'</td>
						<td align="right" width="80">'. $company_total_terget.'</td>
						<td align="right" width="80">'.$company_today_input.'</td>
						<td align="right" width="80">'.$company_total_product.'</td>
						<td align="right" width="80">'. ($company_total_product-$company_total_terget).'</td>
						<td align="right" width="100">'. $company_avable_min.'</td>
						<td align="right" width="100">'. number_format($company_product_min, 2).'</td>
						<td align="right" width="90">'. number_format(($company_total_product/$company_total_terget)*100,2).'%</td>
						<td align="right" width="90" >'.number_format(($company_product_min*100/$company_avable_min),2).' %</td>
						<td align="right" width="90">'.number_format(($company_product_min*100/$over_all_company_avable_min),2).' %</td>';

			$floor_html.='<td align="right" width="80">'. $company_total_input.'</td>
						<td align="right" width="80">'. $company_total_output.'</td>
						<td align="right" width="80">'. $company_total_wip.'</td>
						<td width="70" align="right">'.$company_operator.'</td>
						<td width="50" align="right">'. $company_helper.'</td>
						<td align="right" width="60">'. $company_man_power.'</td>';

						for($k=$hour; $k<=$last_hour; $k++)
						{
							$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
							if($start_hour_arr[$k]==$global_start_lanch)
							{
								$floor_html.='<td align="right" width="50" style=" background-color:#FFFF66" >'. $company_production[$prod_hour].'</td>';
							}
							else
							{
								$floor_html.='<td align="right" width="50" >'. $company_production[$prod_hour].'</td>';
							}
						}
			$floor_html.='</tr>';

			$company_total_tgt_h=0;
			$company_capacity=0;
			$company_working_hour=0;
			$company_final_total_fob_val=0;
			$company_total_terget=0;
			$company_total_product=0;
			$company_avable_hour=0;
			$company_avable_min=0;
			$company_product_min=0;
			$company_man_power=0;
			$company_helper=0;
			$company_operator=0;
			$company_total_input=0;
			$company_total_output=0;
			$company_total_wip=0;
			$company_cm_value=0;
			$company_today_input=0;
			$company_total_target_efficiency=0;
			$company_target_efficiency_count=0;
			$company_total_machine=0;
			$company_total_smv_achive=0;
			unset($company_production);
		}



			$html.='</tbody>';
			$floor_html.='</tbody>';
			$smv_for_item="";
		?>

		<fieldset style="width:2530px">
	       	<table width="2200" cellpadding="0" cellspacing="0">
	            <tr class="form_caption">
	                <td colspan="26" align="center"><strong><? echo $report_title; ?></strong></td>
	            </tr>
	            <tr class="form_caption">
	                <td colspan="26" align="center"><strong><? echo $companyArr[$comapny_id]; ?></strong></td>
	            </tr>
	            <tr class="form_caption">
	                <td colspan="26" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date)) ); ?></strong></td>
	            </tr>
	        </table>
	        <br />
	        <table  width="600" cellpadding="0"  cellspacing="0" align="center" style="padding-left:200px">
	            <tr>


	                <td bgcolor="#FFFF66" height="18" width="30" ></td>
	                <td> &nbsp;Lunch Hour</td>
	                <td bgcolor="red" height="18" width="30"></td>
	                <td> &nbsp;Efficiency % less than Standard And Production less than Target</td>


	            </tr>
	        </table>
	        <label> <strong>Report Sumarry:-</strong></label>
	          <table id="table_header_2" class="rpt_table" width="2340" cellpadding="0" cellspacing="0" border="1" rules="all">
	            <thead>
	                <tr height="50">
	                    <th width="40">SL</th>
	                    <th width="80">Floor Name</th>
	                    <th width="70">Hourly Target</th>
	                    <!-- <th width="70">Capacity</th> -->

	                    <th width="60">Line Hour</th>
	                    <th width="80">Day Target</th>
	                    <th width="80">Today Input</th>
	                    <th width="80">Today Prod.</th>
	                    <th width="80"> Today Variance </th>
	                    <th width="100">Today SMV Avail</th>
	                    <th width="100">Today SMV Achv</th>
	                    <th width="90">Today Achv %</th>
	                    <th width="90">Today Floor Eff. %</th>
	                    <th width="90">Over All Eff. %</th>
	                    <th width="80">Total Input</th>
	                    <th width="80">Total Prod.</th>
	                    <th width="80">WIP</th>
	                    <th width="70">Operator</th>
	                    <th width="50">Helper</th>
	                    <th width="60">Total Man Power</th>

	                	<?
	                	$p=1;
	                	for($k=$hour+1; $k<=$last_hour+1; $k++)
						{
							?>
		                    	<th width="50" style="vertical-align:middle"><div class="block_divs"><?  echo 'H'.$p;   ?></div></th>
							<?
							$p++;
						}
	                	?>
	                </tr>
	            </thead>
	        </table>
	        <div style="width:2360px; max-height:400px; overflow-y:auto" id="scroll_body">
	           <table class="rpt_table" width="2340" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
	           <?  echo $floor_html; ?>
	            <tfoot>
	                   <tr>
	                        <th width="40"></th>
	                        <th width="80">Group Total </th>
	                        <th width="70"><?= $gnd_total_tgt_h;   ?> </th>
	                        <!-- <th width="70" align="right"><?= $total_capacity; ?> </th> -->

	                        <th align="right" width="60"><?= $total_working_hour; ?>&nbsp;</th>
	                        <th align="right" width="80"><?= $grand_total_terget; ?>&nbsp;</th>
	                        <th align="right" width="80"><?= $grand_today_input; ?>&nbsp;</th>
	                        <th align="right" width="80"><?= $line_total_production; ?>&nbsp;</th>
	                        <th align="right" width="80"><?= $line_total_production-$grand_total_terget; ?>&nbsp;</th>
	                        <th align="right" width="100"><?= $gnd_avable_min; ?>&nbsp;</th>
	                        <th align="right" width="100"><?= number_format($gnd_product_min, 2); ?>&nbsp;</th>
	                        <th align="right" width="90"><?= number_format(($line_total_production/$grand_total_terget)*100,2)."%"; ?>&nbsp;</th>
	                        <th align="center" width="90"><?= number_format(($gnd_product_min/$gnd_avable_min)*100,2)."%"; ?>&nbsp;</th>
	                        <th align="center" width="90"><?= number_format(($gnd_product_min/$over_all_gnd_avable_min)*100,2)."%"; ?>&nbsp;</th>
	                        <th align="right" width="80"><?= $grand_total_input; ?>&nbsp;</th>
	                        <th align="right" width="80"><?= $grand_total_output; ?>&nbsp;</th>
	                        <th align="right" width="80"><?= $grand_total_wip; ?>&nbsp;</th>

	                        <th width="70"><?= $total_operator; ?></th>
	                        <th width="50"><?= $total_helper; ?></th>
	                        <th width="60"><?= $total_man_power; ?>&nbsp;</th>
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
	        <table id="table_header_1" class="rpt_table" width="3600" cellpadding="0" cellspacing="0" border="1" rules="all">
	            <thead>
	                <tr height="50">
	                    <th width="40">SL</th>
	                    <th width="80">Floor Name</th>
	                    <th width="80">Line No</th>
	                    <th width="80">Buyer</th>
	                    <th width="140">Int. Ref.</th>
	                    <th width="100">Style Ref.</th>
	                    <th width="120">Garments Item</th>
	                    <th width="60">SMV</th>
	                    <th width="70">Hourly Target (Pcs)</th>
	                    <th width="60">Days Run</th>
	                    <!-- <th width="70">Capacity</th> -->
	                    <th width="60">Working Hour</th>
	                    <th width="60">Current Hour</th>
	                    <th width="80">Today Target</th>
	                    <th width="80">Today Input</th>
	                    <th width="80">Today Prod.</th>
	                    <th width="80">Today Variance (Pcs)</th>
	                    <th width="80">Today Prod. FOB value</th>
	                    <th width="80">Today CM Value</th>
	                    <th width="80">Target Effi</th>
	                    <th width="80">Total Input</th>
	                    <th width="80">Total Prod.</th>
	                    <th width="80">WIP</th>

	                    <th width="100">Today Avail. Hour</th>
	                    <th width="100">Today Avail. Mint</th>
	                    <th width="100">Today Produce Mint</th>
	                    <th width="60">Today Achv %</th>
	                    <th width="90">Today Line Effi %</th>
	                    <th width="90">Ovel All Effi %</th>

	                    <!-- <th width="70">Machine</th> -->
	                    <th width="70">Operator</th>
	                    <th width="50">Helper</th>
	                    <th width="60">TTL Man Power</th>
	                    <th width="100">Line Chief</th>

	                   <?
						$p=1;
	                	for($k=$hour+1; $k<=$last_hour+1; $k++)
						{
							//if($k==$last_hour+1) $hourwidth=''; else $hourwidth='50';
						?>
	                      	<th width="50" style="vertical-align:middle"><div class="block_divs"><?  echo 'H'.$p;   ?></div></th>
						<?
						$p++;
						}
	                ?>
	                	<th width=""> Remarks</th>
	                </tr>
	            </thead>
	        </table>
	        <div style="width:3620px; max-height:400px; overflow-y:scroll" id="scroll_body">
	            <table class="rpt_table" width="3600" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
	             	 <? echo $html;  ?>
	           		 <tfoot>
	                    <tr>
	                        <th width="40">&nbsp;</th>
	                        <th width="80">&nbsp;</th>
	                        <th width="80">&nbsp;</th>
	                        <th width="80">&nbsp;</th>
	                        <th width="140">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="120">Group Total</th>
	                        <th align="right" width="60"><? // echo number_format($total_smv/($i-1),2); ?>&nbsp;</th>

	                        <th align="right" width="70"><?  echo $gnd_total_tgt_h; ?>&nbsp;</th>
	                        <th align="right" width="60"><? //echo $prod_resource_array[$row[csf('sewing_line')]][$pr_date]['man_power']; ?>&nbsp;</th>
	                        <!-- <th align="right" width="70"><? echo $total_capacity; ?></th> -->
	                        <th align="right" width="60"><? echo $total_working_hour; ?></th>
	                        <th align="right" width="60">&nbsp;</th>
	                        <th align="right" width="80"><? echo $grand_total_terget; ?>&nbsp;</th>
	                        <th align="right" width="80"><? echo $grand_today_input; ?>&nbsp;</th>
	                        <th align="right" width="80"><? echo $line_total_production; ?>&nbsp;</th>
	                        <th align="right" width="80"><? echo $line_total_production-$grand_total_terget; ?>&nbsp;</th>
	                        <th align="right" width="80"><? echo number_format($gnd_final_total_fob_val,2);?>&nbsp;</th>
	                        <th align="right" width="80"><? echo number_format($grand_cm_value,2);?>&nbsp;</th>
	                        <th align="right" width="80"><? echo number_format($grand_total_target_efficiency/$grand_target_efficiency_count,2); ?>%&nbsp;</th>
	                        <th align="right" width="80"><? echo $grand_total_input; ?></th>
	                        <th align="right" width="80"><? echo $grand_total_output; ?></th>
	                        <th align="right" width="80"><? echo $grand_total_wip; ?></th>


	                        <th align="right" width="100"><? echo number_format($gnd_avable_hour,2); ?>&nbsp;</th>
	                        <th align="right" width="100"><? echo $gnd_avable_min; ?>&nbsp;</th>
	                        <th align="right" width="100"><? echo number_format($gnd_product_min, 2); ?>&nbsp;</th>
	                        <th align="right" width="60"><? echo number_format(($line_total_production/$grand_total_terget)*100,2)."%"; ?>&nbsp;</th>
	                        <th align="right" width="90" ><? echo number_format(($gnd_product_min/$gnd_avable_min)*100,2)."%";?>&nbsp;</th>
	                        <th align="right" width="90" ><? echo number_format(($gnd_product_min/$over_all_gnd_avable_min)*100,2)."%";?>&nbsp;</th>

	                        <!-- <th align="right" width="70"><? echo $grand_total_machine; ?>&nbsp;</th> -->
	                        <th align="right" width="70"><? echo $total_operator; ?>&nbsp;</th>
	                        <th align="right" width="50"><? echo $total_helper; ?>&nbsp;</th>
	                        <th align="right" width="60"><? echo $total_man_power; ?>&nbsp;</th>
	                        <th align="right" width="100"></th>
	                        <?
	                        for($k=$hour; $k<=$last_hour; $k++)
	                        {
								$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
								if($k==$last_hour) $hourwidth=''; else $hourwidth='50';
								?>
									<th align="right" width="<?php echo $hourwidth;?> "><? echo $total_production[$prod_hour]; ?></th>
								<?
	                        }
	                        ?>
	                        <th width=""></th>
	                    </tr>
	                </tfoot>

	            </table>
			</div>
		</fieldset>

		<?
	}
	elseif ($type == 2)
	{

		?>

		<fieldset style="width:1325px">
	       <table width="1325" cellpadding="0" cellspacing="0">
	            <tr class="form_caption">
	                <td colspan="26" align="center"><strong><? echo $report_title; ?></strong></td>
	            </tr>
	            <tr class="form_caption">
	                <td colspan="26" align="center"><strong><? echo $companyArr[$comapny_id]; ?></strong></td>
	            </tr>
	            <tr class="form_caption">
	                <td colspan="26" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date)) ); ?></strong></td>
	            </tr>
	        </table>
	        <br />
	        <table  width="500" cellpadding="0"  cellspacing="0" align="center" style="padding-left:200px">
	            <tr>

	                <td bgcolor="#FFFF66" height="18" width="30" ></td>
	                <td> &nbsp;Lunch Hour</td>
	                <td bgcolor="red" height="18" width="30"></td>
	                <td>&nbsp;Efficiency % less than Standard And Production less than Target</td>

	            </tr>
	        </table>

	        <!-- Calculation For header sum show  -->

	        <?php
	        foreach($prod_resource_array as $company_id=>$com_name)
			{
				$global_start_lanch=$start_time_arr[$company_id][1]['lst'];
				for($k=$hour; $k<=$last_hour; $k++)
				{
					$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
					if($k==$last_hour) $hourwidth=''; else $hourwidth='50';
				}
				foreach($com_name as $lo_id=>$lo_name)
				{
					ksort($lo_name);
					foreach($lo_name as $f_id=>$fname)
					{
						foreach($fname as $resource_id=>$resource_data)
						{
							$germents_item=array_unique(explode('****',$production_data_arr[$f_id][$resource_id]['item_number_id']));

							$buyer_neme_all=array_unique(explode(',',$production_data_arr[$f_id][$resource_id]['buyer_name']));
							$buyer_name="";
							foreach($buyer_neme_all as $buy)
							{
								if($buyer_name!='') $buyer_name.=',';
								$buyer_name.=$buyerArr[$buy];
							}
							$garment_itemname='';
							$item_smv="";$item_ids='';
							$smv_for_item="";
							$produce_minit="";
							$order_no_total="";
							$efficiency_min=0;
							$tot_po_qty=0;$fob_val=0;$days_run=0;
							$total_input=0; $total_output=0; $min_input_date=''; $total_wip=0; $line_cm_value=0;
							$today_input=0; $total_smv_achive=0;
							foreach($germents_item as $g_val)
							{

								$po_garment_item=explode('**',$g_val);
								if($garment_itemname!='') $garment_itemname.=',';
								$garment_itemname.=$garments_item[$po_garment_item[1]];

								if($item_ids=='') $item_ids=$po_garment_item[1];else $item_ids.=",".$po_garment_item[1];

								$total_input+=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input'];
								$total_output+=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['output'];
								if($input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input_date']!='')
								{
									if($min_input_date!='')
									{
										if(strtotime($input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input_date'])<strtotime($min_input_date))
										{
											$min_input_date=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input_date'];
										}
									}
									else
									{
										$min_input_date=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input_date'];
									}
								}
								//echo $today_input;die;
								//echo $company_id."*".$f_id."*".$resource_id."*".$po_garment_item[0]."*".$search_prod_date;
								//print_r($input_po_arr);die;
							    //echo $input_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]][$search_prod_date]."**";
								//$today_input+=$input_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]][$search_prod_date];
								//echo $today_input."2gsgd";die;

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

								$produce_minit+=$production_po_data_arr[$f_id][$resource_id][$po_garment_item[0]]*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];

								$fob_rate=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt']/$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];

								$prod_qty=$production_data_arr_qty[$f_id][$resource_id][$po_garment_item[0]][$po_garment_item[1]]['quantity'];

								$dzn_qnty=0; $cm_value=0;
								if($costing_per_arr[$po_garment_item[2]]==1) $dzn_qnty=12;
								else if($costing_per_arr[$po_garment_item[2]]==3) $dzn_qnty=12*2;
								else if($costing_per_arr[$po_garment_item[2]]==4) $dzn_qnty=12*3;
								else if($costing_per_arr[$po_garment_item[2]]==5) $dzn_qnty=12*4;
								else $dzn_qnty=1;

								$dzn_qnty=$dzn_qnty*$po_garment_item[3];
								$cm_value=($tot_cost_arr[$po_garment_item[2]]/$dzn_qnty)*$prod_qty;
								if(is_nan($cm_value)){ $cm_value=0; }

								$line_cm_value+=$cm_value;
								if(is_nan($fob_rate)){ $fob_rate=0; }
								$fob_val+=$prod_qty*$fob_rate;
							}

						    $today_input+=$production_data_arr[$f_id][$resource_id]['input_qnty'];
							//echo $today_input."gsdfgsg";die;
							//$fob_rate=$tot_po_amt/$tot_po_qty;
							$subcon_po_id=array_unique(explode(',',$production_data_arr[$f_id][$resource_id]['order_id']));
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
								$produce_minit+=$production_po_data_arr[$f_id][$resource_id][$sub_val]*$subcon_order_smv[$sub_val];
								if($subcon_order_id!="") $subcon_order_id.=",";
								$subcon_order_id.=$sub_val;
							}


							if($min_input_date!="")
							{
								$days_run=datediff("d",$min_input_date,$pr_date);
							}
							else  $days_run=0;

							$type_line=$production_data_arr[$f_id][$resource_id]['type_line'];
							$prod_reso_allo=$production_data_arr[$f_id][$resource_id]['prod_reso_allo'];

							$sewing_line='';
							$line_number=explode(",",$resource_data['line_number']);
							foreach($line_number as $val)
							{
								if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
							}


					        //*************************************************************************************************************************************************
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
								 $production_hour[$prod_hour]=$production_data_arr[$f_id][$resource_id][$prod_hour];
								 $company_production[$prod_hour]+=$production_data_arr[$f_id][$resource_id][$prod_hour];
								 $floor_production[$prod_hour]+=$production_data_arr[$f_id][$resource_id][$prod_hour];
								 $total_production[$prod_hour]+=$production_data_arr[$f_id][$resource_id][$prod_hour];
							}


							// print_r($production_hour);
							$floor_production['prod_hour24']+=$production_data_arr[$f_id][$resource_id]['prod_hour23'];
							$total_production['prod_hour24']+=$production_data_arr[$f_id][$resource_id]['prod_hour23'];
							$production_hour['prod_hour24']=$production_data_arr[$f_id][$resource_id]['prod_hour23'];
							$company_production['prod_hour24']=$production_data_arr[$f_id][$resource_id]['prod_hour23'];
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
										$line_production_hour+=$production_data_arr[$f_id][$resource_id][$line_hour];
										$line_floor_production+=$production_data_arr[$f_id][$resource_id][$line_hour];
										$line_total_production+=$production_data_arr[$f_id][$resource_id][$line_hour];
										$actual_time_hour=$start_hour_arr[$lh+1];
									}
								}
								//echo $total_eff_hour.'aaaa';
								if($start_hour_arr[$actual_time]>$lunch_start_hour) $total_eff_hour=$total_eff_hour-1;

								if($total_eff_hour>$production_data_arr[$f_id][$resource_id]['working_hour'])
								{
									 $total_eff_hour=$production_data_arr[$f_id][$resource_id]['working_hour'];
								}
							}

							if(str_replace("'","",$actual_production_date)<=str_replace("'","",$actual_date))
							{
								for($ah=$hour;$ah<=$last_hour;$ah++)
								{
									$prod_hour="prod_hour".substr($start_hour_arr[$ah],0,2)."";
									$line_production_hour+=$production_data_arr[$f_id][$resource_id][$prod_hour];
									//echo $production_data_arr[$f_id][$ldata][$prod_hour];
									$line_floor_production+=$production_data_arr[$f_id][$resource_id][$prod_hour];
									$line_total_production+=$production_data_arr[$f_id][$resource_id][$prod_hour];
								}

								$total_eff_hour=$resource_data['working_hour'];
							}

							//rtdfgdfgfd 88888888888888888888888888888888888888888888888888888888888888888

							if($cbo_no_prod_type==2 && $line_production_hour>0)
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
								$line_efficiency=(($produce_minit)*100)/$efficiency_min;


								//****************************************************************************************************************

							    //echo $today_input;die;

								$man_power=$resource_data['man_power'];
								$operator=$resource_data['operator'];
								$helper=$resource_data['helper'];
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

								$floor_cm_value+=$line_cm_value;
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
								$grand_total_terget+=$eff_target;
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


								$company_today_input+=$today_input;
								$company_total_input+=$total_input;
								$company_total_output+=$total_output;
								$company_total_wip+=($total_input-$total_output);
								$company_operator+=$resource_data['operator'];
								$company_man_power+=$resource_data['man_power'];
								$company_helper+=$resource_data['helper'];
								$company_capacity+=$resource_data['capacity'];
								$company_working_hour+=$resource_data['working_hour'];
								$company_total_tgt_h+=$resource_data['terget_hour'];
								$company_total_terget+=$eff_target;
								$company_total_product+=$line_production_hour;
								$company_avable_min+=$efficiency_min;
								$company_product_min+=$produce_minit;
								$company_total_fob_val+=$fob_val;
								$company_final_total_fob_val+=$fob_val;
								$company_cm_value+=$line_cm_value;

								$floor_total_smv_achive+=$total_smv_achive;
								$company_total_smv_achive+=$total_smv_achive;
								$grand_total_smv_achive+=$total_smv_achive;

								$floor_total_machine+=$resource_data['machine'];
								$company_total_machine+=$resource_data['machine'];
								$grand_total_machine+=$resource_data['machine'];


								$po_id=rtrim($production_data_arr[$f_id][$resource_id]['po_id'],',');
								$po_id=array_unique(explode(",",$po_id));
								$style=rtrim($production_data_arr[$f_id][$resource_id]['style']);
								$style=implode(",",array_unique(explode(",",$style)));

								$cbo_get_upto=str_replace("'","",$cbo_get_upto);
								$txt_parcentage=str_replace("'","",$txt_parcentage);
							   //********************************* calclution floor total    ****************************************************$pr_date],$sewing_day
								$floor_name=$floorArr[$f_id];
								$floor_smv+=$item_smv;

								$floor_days_run+=$days_run;

								$po_id=$production_data_arr[$f_id][$resource_id]['po_id'];//$item_ids//$subcon_order_id
								$styles=explode(",",$style);
								$style_button='';//
								foreach($styles as $sid)
								{
									if( $style_button=='')
									{
										$style_button="<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$resource_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
									}
									else
									{
										$style_button.=", "."<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$resource_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
									}
								}
								$as_on_current_hour_target=0; $as_on_current_hour_variance=0;
								$as_on_current_hour_target=$terget_hour*$cla_cur_time;
								$as_on_current_hour_variance=$line_production_hour-$as_on_current_hour_target;

								if($line_efficiency<=$txt_parcentage) $efficiency_color="#FF0000"; else $efficiency_color="#FFFFFF";
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$string="'";

								for($k=$hour; $k<=$last_hour; $k++)
								{
									$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
									//if($k==$last_hour) $hourwidth=''; else $hourwidth='50';

									if($start_hour_arr[$k]==$lunch_start_hour)
									{
										 $bg_color='background:yellow';
									}
									else if($terget_hour>$production_hour[$prod_hour])
									{
										$bg_color='background:red';
										if($production_hour[$prod_hour]==0)
										{
											$bg_color='';
										}
									}
									else if($terget_hour<$production_hour[$prod_hour])
									{
										$bg_color='background:green';
										if($production_hour[$prod_hour]==0)
										{
											$bg_color='';
										}
									}
									else
									{
										$bg_color="";
									}
								}
								if($line_efficiency<=$txt_parcentage)
								{}
								else
								{
							    }
						    $i++;
					        }
							if($cbo_no_prod_type==1)
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
								$line_efficiency=(($produce_minit)*100)/$efficiency_min;


								//****************************************************************************************************************

								$man_power=$resource_data['man_power'];
								$operator=$resource_data['operator'];
								$helper=$resource_data['helper'];
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
								$floor_today_product+=$today_product;
								$floor_avale_minute+=$efficiency_min;
								$floor_produc_min+=$produce_minit;
								$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;

								$floor_today_input+=$today_input;
								//echo $today_input;die;
								$floor_cm_value+=$line_cm_value;
								$floor_total_input+=$total_input;
								$floor_total_output+=$total_output;
								$floor_total_wip+=($total_input-$total_output);

								$total_operator+=$resource_data['operator'];
								$total_man_power+=$resource_data['man_power'];
								$total_helper+=$resource_data['helper'];
								$total_capacity+=$resource_data['capacity'];
								$total_working_hour+=$resource_data['working_hour'];
								$gnd_total_tgt_h+=$resource_data['terget_hour'];
								$grand_total_terget+=$eff_target;
								$grand_total_product+=$today_product;
								$gnd_avable_min+=$efficiency_min;
								$gnd_product_min+=$produce_minit;
								$gnd_total_fob_val+=$fob_val;
								$gnd_final_total_fob_val+=$fob_val;

								$grand_today_input+=$today_input;
								$grand_total_input+=$total_input;
								$grand_total_output+=$total_output;
								$grand_total_wip+=($total_input-$total_output);
								$grand_cm_value+=$line_cm_value;

								$company_today_input+=$today_input;
								$company_total_input+=$total_input;
								$company_total_output+=$total_output;
								$company_total_wip+=($total_input-$total_output);
								$company_operator+=$resource_data['operator'];
								$company_man_power+=$resource_data['man_power'];
								$company_helper+=$resource_data['helper'];
								$company_capacity+=$resource_data['capacity'];
								$company_working_hour+=$resource_data['working_hour'];
								$company_total_tgt_h+=$resource_data['terget_hour'];
								$company_total_terget+=$eff_target;
								$company_total_product+=$line_production_hour;
								$company_avable_min+=$efficiency_min;
								$company_product_min+=$produce_minit;
								$company_total_fob_val+=$fob_val;
								$company_final_total_fob_val+=$fob_val;
								$company_cm_value+=$line_cm_value;


								$floor_total_smv_achive+=$total_smv_achive;
								$company_total_smv_achive+=$total_smv_achive;
								$grand_total_smv_achive+=$total_smv_achive;


								$floor_total_machine+=$resource_data['machine'];
								$company_total_machine+=$resource_data['machine'];
								$grand_total_machine+=$resource_data['machine'];

								$po_id=rtrim($production_data_arr[$f_id][$resource_id]['po_id'],',');
								$po_id=array_unique(explode(",",$po_id));
								$style=rtrim($production_data_arr[$f_id][$resource_id]['style']);
								$style=implode(",",array_unique(explode(",",$style)));

								$cbo_get_upto=str_replace("'","",$cbo_get_upto);
								$txt_parcentage=str_replace("'","",$txt_parcentage);
							   //********************************* calclution floor total    ****************************************************$pr_date],$sewing_day
								$floor_name=$floorArr[$f_id];
								$floor_smv+=$item_smv;

								$floor_days_run+=$days_run;

								$po_id=$production_data_arr[$f_id][$resource_id]['po_id'];//$item_ids//$subcon_order_id
								$styles=explode(",",$style);
								$style_button='';//
								foreach($styles as $sid)
								{
									if( $style_button=='')
									{
										$style_button="<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$resource_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
									}
									else
									{
										$style_button.=", "."<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$resource_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
									}
								}
								$as_on_current_hour_target=0; $as_on_current_hour_variance=0;
								$as_on_current_hour_target=$terget_hour*$cla_cur_time;
								$as_on_current_hour_variance=$line_production_hour-$as_on_current_hour_target;

								if($line_efficiency<=$txt_parcentage) $efficiency_color="#FF0000"; else $efficiency_color="#FFFFFF";
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$string="'";

								for($k=$hour; $k<=$last_hour; $k++)
								{
									$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";

									if($start_hour_arr[$k]==$lunch_start_hour)
									{
										 $bg_color='background:yellow';
									}
									else if($terget_hour>$production_hour[$prod_hour])
									{
										$bg_color='background:red';
										if($production_hour[$prod_hour]==0)
										{
											$bg_color='';
										}
									}
									else if($terget_hour<$production_hour[$prod_hour])
									{
										$bg_color='background:green';
										if($production_hour[$prod_hour]==0)
										{
											$bg_color='';
										}
									}
									else
									{
										$bg_color="";
									}
							    }
							    if($line_efficiency<=$txt_parcentage)
								{}
								else
								{
							    }
						    $i++;
					        }
					        //echo $floor_cm_value."***";die;
				        }
						if($cbo_no_prod_type==2 && $line_floor_production>0)
						{
							$gnd_total_fob_val=0;
							for($k=$hour; $k<=$last_hour; $k++)
							{
								$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";

								if($start_hour_arr[$k]==$global_start_lanch)
								{
									 $bg_color='background:yellow';
								}
								if($floor_tgt_h>$floor_production[$prod_hour])
								{
									$bg_color='background:red';
									if($floor_production[$prod_hour]==0)
									{
										$bg_color='';
									}
								}
								else
								{
									 $bg_color='';
								}
							}
							if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

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
						    $floor_total_machine=0;
						    $floor_today_input=0;
						    $floor_total_input=0;
						    $floor_total_output=0;
						    $floor_total_wip=0;
						    $floor_cm_value=0;
					        $j++;
					    }
						if($cbo_no_prod_type==1)
						{
							$gnd_total_fob_val=0;
							for($k=$hour; $k<=$last_hour; $k++)
							{
								$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";

								if($start_hour_arr[$k]==$global_start_lanch)
								{
									 $bg_color='background:yellow';
								}
								if($floor_tgt_h>$floor_production[$prod_hour])
								{
									$bg_color='background:red';
									if($floor_production[$prod_hour]==0)
									{
										$bg_color='';
									}
								}
								else
								{
									 $bg_color='';
								}
							}
						    if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

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
						    $floor_total_machine=0;
						    $floor_today_input=0;
						    $floor_total_input=0;
						    $floor_total_output=0;
						    $floor_total_wip=0;
						    $floor_cm_value=0;
							$j++;
						}
					}
				}

	        }

	        ?>

	        <style type="text/css">
	        	#talbe_1_gt{
	        		background: #bababa;
	        	}
	        	#talbe_1_gt tr th{
	        		border: 1px solid black!important;
	        	}
	        	.wrd_brk{
	        		word-break: break-all;
	        	}
	        </style>

	        <table  id="talbe_1_gt" width="1325" cellpadding="0"  cellspacing="0" rules="all">
	            <tr>
	                <th width="273">Total</th>
	                <th align="center" width="60"><? echo $total_capacity; ?></th>
	                <th align="center" width="70"><?  echo $gnd_total_tgt_h; ?>&nbsp;</th>
	                <th align="center" width="45" ><? echo number_format(($gnd_product_min/$gnd_avable_min)*100,2)."%";?>&nbsp;</th>
	                <?
	                for($k=$hour; $k<=$last_hour; $k++)
	                {
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
						if($k==$last_hour) $hourwidth='40'; else $hourwidth='40';
						?>
							<th align="center" width="<?php echo $hourwidth;?> "><? echo $total_production[$prod_hour]; ?></th>
						<?
	                }
	                ?>

	                <th align="center" width="50"><? echo $grand_total_terget; ?>&nbsp;</th>
	                <th align="center" width="50"><? echo $grand_today_input; ?>&nbsp;</th>
	                <th align="center" width="50"><? echo $line_total_production; ?>&nbsp;</th>
	                <th width="">&nbsp;</th>
	            </tr>
	        </table>


	        <table id="table_header_1" class="rpt_table" width="1325" cellpadding="0" cellspacing="0" border="1" rules="all">
	            <thead>
	                <tr height="50">
	                    <th width="40">Line No</th>
	                    <th width="60">Buyer</th>
	                    <th width="100">Order No</th>
	                    <th width="70">Garments Picture</th>
	                    <th width="60">Line Capacity</th>
	                    <th width="70">Hourly Target (Pcs)</th>
	                    <th width="45">Line Effi %</th>

	                    <?
	                	for($k=$hour+1; $k<=$last_hour+1; $k++)
						{
							//if($k==$last_hour+1) $hourwidth=''; else $hourwidth='50';
						?>
	                      	<th width="40" style="vertical-align:middle"><div class="block_div"><?  echo substr($start_hour_arr[$k],0,5);   ?></div></th>
						<?
						}
	                    ?>

	                    <th width="50">Today Target</th>
	                    <th width="50">Today Input</th>
	                    <th width="50">Today Prod.</th>
	                	<th width=""> Remarks</th>
	                </tr>
	            </thead>
	        </table>
	        <div style="width:1345; max-height:400px; overflow-y:scroll" id="scroll_body">
	            <table class="rpt_table" width="1325" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
	             	 <!-- <? //echo $html;  ?> -->
	             	<tbody>
	             	<?
	         		foreach($prod_resource_array as $company_id=>$com_name)
					{
						$global_start_lanch=$start_time_arr[$company_id][1]['lst'];
						?>
						<tr bgcolor="#E8FFFF">
							<td width="" colspan="7"><strong>Company Name:<? echo $companyArr[$company_id]?></strong></td>
							<?
							for($k=$hour; $k<=$last_hour; $k++)
							{
								$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
								if($k==$last_hour) $hourwidth=''; else $hourwidth='40';?>
								<td align="right" width=<? echo $hourwidth;?> style=<? echo $bg_color; ?>></td>
							<?
							}
							?>
						</tr>

						<?
						foreach($com_name as $lo_id=>$lo_name)
						{
							ksort($lo_name);
							foreach($lo_name as $f_id=>$fname)
							{
								ksort($fname);
								?>
								<tr bgcolor="#E8FFFF">
							        <td width="" colspan="7"><strong>Floor Name:<? echo $floorArr[$f_id];?></strong></td>
								    <?
									for($k=$hour; $k<=$last_hour; $k++)
									{
										$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
										if($k==$last_hour) $hourwidth=''; else $hourwidth='40';?>
										<td align="right" width=<? echo $hourwidth;?> style=<? echo $bg_color; ?>></td>
									<?
									}
									?>
							    </tr>
								<?foreach($fname as $resource_id=>$resource_data)
								{
									$germents_item=array_unique(explode('****',$production_data_arr[$f_id][$resource_id]['item_number_id']));

									$buyer_neme_all=array_unique(explode(',',$production_data_arr[$f_id][$resource_id]['buyer_name']));
									$buyer_name="";
									foreach($buyer_neme_all as $buy)
									{
										if($buyer_name!='') $buyer_name.=',';
										$buyer_name.=$buyerArr[$buy];
									}
									$garment_itemname='';
									$item_smv="";$item_ids='';
									$smv_for_item="";
									$produce_minit="";
									$order_no_total="";
									$efficiency_min=0;
									//echo $today_input;die;
									$tot_po_qty=0;$fob_val=0;$days_run=0;
									$total_input=0; $total_output=0; $min_input_date=''; $total_wip=0; $line_cm_value=0;
									$today_input=0; $total_smv_achive=0;
									foreach($germents_item as $g_val)
									{

										$po_garment_item=explode('**',$g_val);
										if($garment_itemname!='') $garment_itemname.=',';
										$garment_itemname.=$garments_item[$po_garment_item[1]];

										if($item_ids=='') $item_ids=$po_garment_item[1];else $item_ids.=",".$po_garment_item[1];

										$total_input+=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input'];
										$total_output+=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['output'];
										if($input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input_date']!='')
										{
											if($min_input_date!='')
											{
												if(strtotime($input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input_date'])<strtotime($min_input_date))
												{
													$min_input_date=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input_date'];
												}
											}
											else
											{
												$min_input_date=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input_date'];
											}
										}
										//echo $company_id."*".$f_id."*".$resource_id."*".$po_garment_item[0]."*".$search_prod_date;
										//print_r($input_po_arr);die;
									    //echo $input_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]][$search_prod_date]."**";
										$today_input+=$input_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]][$search_prod_date];

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

										$produce_minit+=$production_po_data_arr[$f_id][$resource_id][$po_garment_item[0]]*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];

										$fob_rate=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt']/$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];

										$prod_qty=$production_data_arr_qty[$f_id][$resource_id][$po_garment_item[0]][$po_garment_item[1]]['quantity'];

										$dzn_qnty=0; $cm_value=0;
										if($costing_per_arr[$po_garment_item[2]]==1) $dzn_qnty=12;
										else if($costing_per_arr[$po_garment_item[2]]==3) $dzn_qnty=12*2;
										else if($costing_per_arr[$po_garment_item[2]]==4) $dzn_qnty=12*3;
										else if($costing_per_arr[$po_garment_item[2]]==5) $dzn_qnty=12*4;
										else $dzn_qnty=1;

										$dzn_qnty=$dzn_qnty*$po_garment_item[3];
										$cm_value=($tot_cost_arr[$po_garment_item[2]]/$dzn_qnty)*$prod_qty;
										if(is_nan($cm_value)){ $cm_value=0; }

										$line_cm_value+=$cm_value;
										if(is_nan($fob_rate)){ $fob_rate=0; }
										$fob_val+=$prod_qty*$fob_rate;
									}
									$today_input=0;

								    $today_input+=$production_data_arr[$f_id][$resource_id]['input_qnty'];
									//echo $today_input;die;
									//$fob_rate=$tot_po_amt/$tot_po_qty;
									$subcon_po_id=array_unique(explode(',',$production_data_arr[$f_id][$resource_id]['order_id']));
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
										$produce_minit+=$production_po_data_arr[$f_id][$resource_id][$sub_val]*$subcon_order_smv[$sub_val];
										if($subcon_order_id!="") $subcon_order_id.=",";
										$subcon_order_id.=$sub_val;
									}


									if($min_input_date!="")
									{
										$days_run=datediff("d",$min_input_date,$pr_date);
									}
									else  $days_run=0;

									$type_line=$production_data_arr[$f_id][$resource_id]['type_line'];
									$prod_reso_allo=$production_data_arr[$f_id][$resource_id]['prod_reso_allo'];

									$sewing_line='';
									$line_number=explode(",",$resource_data['line_number']);
									foreach($line_number as $val)
									{
										if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
									}


							        //*************************************************************************************************************************************************
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
										 $production_hour[$prod_hour]=$production_data_arr[$f_id][$resource_id][$prod_hour];
										 $company_production[$prod_hour]+=$production_data_arr[$f_id][$resource_id][$prod_hour];
										 $floor_production[$prod_hour]+=$production_data_arr[$f_id][$resource_id][$prod_hour];
										 $total_production[$prod_hour]+=$production_data_arr[$f_id][$resource_id][$prod_hour];
									}


									// print_r($production_hour);
									$floor_production['prod_hour24']+=$production_data_arr[$f_id][$resource_id]['prod_hour23'];
									$total_production['prod_hour24']+=$production_data_arr[$f_id][$resource_id]['prod_hour23'];
									$production_hour['prod_hour24']=$production_data_arr[$f_id][$resource_id]['prod_hour23'];
									$company_production['prod_hour24']=$production_data_arr[$f_id][$resource_id]['prod_hour23'];
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
												$line_production_hour+=$production_data_arr[$f_id][$resource_id][$line_hour];
												$line_floor_production+=$production_data_arr[$f_id][$resource_id][$line_hour];
												$line_total_production+=$production_data_arr[$f_id][$resource_id][$line_hour];
												$actual_time_hour=$start_hour_arr[$lh+1];
											}
										}
										//echo $total_eff_hour.'aaaa';
										if($start_hour_arr[$actual_time]>$lunch_start_hour) $total_eff_hour=$total_eff_hour-1;

										if($total_eff_hour>$production_data_arr[$f_id][$resource_id]['working_hour'])
										{
											 $total_eff_hour=$production_data_arr[$f_id][$resource_id]['working_hour'];
										}
									}

									if(str_replace("'","",$actual_production_date)<=str_replace("'","",$actual_date))
									{
										for($ah=$hour;$ah<=$last_hour;$ah++)
										{
											$prod_hour="prod_hour".substr($start_hour_arr[$ah],0,2)."";
											$line_production_hour+=$production_data_arr[$f_id][$resource_id][$prod_hour];
											//echo $production_data_arr[$f_id][$ldata][$prod_hour];
											$line_floor_production+=$production_data_arr[$f_id][$resource_id][$prod_hour];
											$line_total_production+=$production_data_arr[$f_id][$resource_id][$prod_hour];
										}

										$total_eff_hour=$resource_data['working_hour'];
									}

									//rtdfgdfgfd 88888888888888888888888888888888888888888888888888888888888888888

									if($cbo_no_prod_type==2 && $line_production_hour>0)
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
										$line_efficiency=(($produce_minit)*100)/$efficiency_min;


										//****************************************************************************************************************

									    //echo $today_input;die;

										$man_power=$resource_data['man_power'];
										$operator=$resource_data['operator'];
										$helper=$resource_data['helper'];
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

										$floor_cm_value+=$line_cm_value;
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
										$grand_total_terget+=$eff_target;
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


										$company_today_input+=$today_input;
										$company_total_input+=$total_input;
										$company_total_output+=$total_output;
										$company_total_wip+=($total_input-$total_output);
										$company_operator+=$resource_data['operator'];
										$company_man_power+=$resource_data['man_power'];
										$company_helper+=$resource_data['helper'];
										$company_capacity+=$resource_data['capacity'];
										$company_working_hour+=$resource_data['working_hour'];
										$company_total_tgt_h+=$resource_data['terget_hour'];
										$company_total_terget+=$eff_target;
										$company_total_product+=$line_production_hour;
										$company_avable_min+=$efficiency_min;
										$company_product_min+=$produce_minit;
										$company_total_fob_val+=$fob_val;
										$company_final_total_fob_val+=$fob_val;
										$company_cm_value+=$line_cm_value;

										$floor_total_smv_achive+=$total_smv_achive;
										$company_total_smv_achive+=$total_smv_achive;
										$grand_total_smv_achive+=$total_smv_achive;

										$floor_total_machine+=$resource_data['machine'];
										$company_total_machine+=$resource_data['machine'];
										$grand_total_machine+=$resource_data['machine'];


										$po_id=rtrim($production_data_arr[$f_id][$resource_id]['po_id'],',');
										$po_id=array_unique(explode(",",$po_id));
										$style=rtrim($production_data_arr[$f_id][$resource_id]['style']);
										$style=implode(",",array_unique(explode(",",$style)));

										$cbo_get_upto=str_replace("'","",$cbo_get_upto);
										$txt_parcentage=str_replace("'","",$txt_parcentage);
									   //********************************* calclution floor total    ****************************************************$pr_date],$sewing_day
										$floor_name=$floorArr[$f_id];
										$floor_smv+=$item_smv;

										$floor_days_run+=$days_run;

										$po_id=$production_data_arr[$f_id][$resource_id]['po_id'];//$item_ids//$subcon_order_id
										$styles=explode(",",$style);
										$style_button='';//
										foreach($styles as $sid)
										{
											if( $style_button=='')
											{
												$style_button="<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$resource_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
											}
											else
											{
												$style_button.=", "."<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$resource_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
											}
										}
										$as_on_current_hour_target=0; $as_on_current_hour_variance=0;
										$as_on_current_hour_target=$terget_hour*$cla_cur_time;
										$as_on_current_hour_variance=$line_production_hour-$as_on_current_hour_target;

										if($line_efficiency<=$txt_parcentage) $efficiency_color="#FF0000"; else $efficiency_color="#FFFFFF";
										if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										?>
										<tr bgcolor=<? echo $bgcolor;?> onclick=change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>') id=tr_<? echo $i;?>>
										<td align="center" width="40" class="wrd_brk"><? echo $sewing_line;?>&nbsp; </td>
										<td width="60" align="center" class="wrd_brk"><p><? echo $buyer_name;?>&nbsp;</p></td>
										<td width="100" align="center" class="wrd_brk"><p><? echo $production_data_arr[$f_id][$resource_id]['po_number'];?>&nbsp;</p></td>
										<td width="70" onclick="openmypage_image('requires/company_wise_hourly_production_monitoring_controller.php?action=show_image&job_no=<? echo $job_no;?>','Image View')">
											<img src="../../<?echo $imge_arr[$production_data_arr[$f_id][$resource_id]['job_no']];?>" height="30" width="40" />
										</td>

								        <td align="center" width="60"><? echo $capacity;?></td>
										<td align="center" width="70"><? echo $terget_hour;?></td>

										<?
										if($line_efficiency<=$txt_parcentage)
										{?>
											<td align="center" width="45" bgcolor="red"><? echo number_format($line_efficiency,2).'%';?></td>
										<?}
										else
										{?>
											<td align="center" width="45"><? echo number_format($line_efficiency,2).'%';?></td>
										<?
										}

									    $string="'";

										for($k=$hour; $k<=$last_hour; $k++)
										{
											$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
											//if($k==$last_hour) $hourwidth=''; else $hourwidth='50';

											if($start_hour_arr[$k]==$lunch_start_hour)
											{
												 $bg_color='background:yellow';
											}
											else if($terget_hour>$production_hour[$prod_hour])
											{
												$bg_color='background:red';
												if($production_hour[$prod_hour]==0)
												{
													$bg_color='';
												}
											}
											else if($terget_hour<$production_hour[$prod_hour])
											{
												$bg_color='background:green';
												if($production_hour[$prod_hour]==0)
												{
													$bg_color='';
												}
											}
											else
											{
												$bg_color="";
											}
											?>

										    <td align="center" width="40"  style=<? echo $bg_color;?>><? echo $production_hour[$prod_hour];?></td>
										<?
										}
								        ?>

									    <td align="center" width="50"><? echo $eff_target;?></td>
									    <td width="50"  align="center"><a href="##" onclick="generate_in_out_popup('<? echo $order_no_total;?>','tot_input_output_popup',<? echo $f_id;?>,<? echo $resource_id;?>,'4','<? echo $company_id;?>',<? echo $txt_date;?>)"><? echo $today_input;?></a></td>
									    <td width="50"  align="center"><a href="##" onclick="generate_in_out_popup('<? echo $order_no_total;?>','tot_input_output_popup',<? echo $f_id;?>,<? echo $resource_id;?>,'5','<? echo $company_id;?>',<? echo $txt_date;?>)"><? echo $line_production_hour;?></a></td>
										<td  class="wrd_brk"><input type="button"  value="View" class="formbutton" onclick="show_line_remarks(<? echo $cbo_company_id;?>,'<? echo $order_no_total;?>','<? echo $f_id;?>','<? echo $resource_id;?>','remarks_popup',<? echo $txt_date;?>)"/></td>

								        </tr>
								    <?
								    $i++;
							        }
									if($cbo_no_prod_type==1)
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
										$line_efficiency=(($produce_minit)*100)/$efficiency_min;


										//****************************************************************************************************************

										$man_power=$resource_data['man_power'];
										$operator=$resource_data['operator'];
										$helper=$resource_data['helper'];
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
										$floor_today_product+=$today_product;
										$floor_avale_minute+=$efficiency_min;
										$floor_produc_min+=$produce_minit;
										$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;

										$floor_today_input+=$today_input;
										$floor_cm_value+=$line_cm_value;
										$floor_total_input+=$total_input;
										$floor_total_output+=$total_output;
										$floor_total_wip+=($total_input-$total_output);

										$total_operator+=$resource_data['operator'];
										$total_man_power+=$resource_data['man_power'];
										$total_helper+=$resource_data['helper'];
										$total_capacity+=$resource_data['capacity'];
										$total_working_hour+=$resource_data['working_hour'];
										$gnd_total_tgt_h+=$resource_data['terget_hour'];
										$grand_total_terget+=$eff_target;
										$grand_total_product+=$today_product;
										$gnd_avable_min+=$efficiency_min;
										$gnd_product_min+=$produce_minit;
										$gnd_total_fob_val+=$fob_val;
										$gnd_final_total_fob_val+=$fob_val;

										$grand_today_input+=$today_input;
										$grand_total_input+=$total_input;
										$grand_total_output+=$total_output;
										$grand_total_wip+=($total_input-$total_output);
										$grand_cm_value+=$line_cm_value;

										$company_today_input+=$today_input;
										$company_total_input+=$total_input;
										$company_total_output+=$total_output;
										$company_total_wip+=($total_input-$total_output);
										$company_operator+=$resource_data['operator'];
										$company_man_power+=$resource_data['man_power'];
										$company_helper+=$resource_data['helper'];
										$company_capacity+=$resource_data['capacity'];
										$company_working_hour+=$resource_data['working_hour'];
										$company_total_tgt_h+=$resource_data['terget_hour'];
										$company_total_terget+=$eff_target;
										$company_total_product+=$line_production_hour;
										$company_avable_min+=$efficiency_min;
										$company_product_min+=$produce_minit;
										$company_total_fob_val+=$fob_val;
										$company_final_total_fob_val+=$fob_val;
										$company_cm_value+=$line_cm_value;


										$floor_total_smv_achive+=$total_smv_achive;
										$company_total_smv_achive+=$total_smv_achive;
										$grand_total_smv_achive+=$total_smv_achive;


										$floor_total_machine+=$resource_data['machine'];
										$company_total_machine+=$resource_data['machine'];
										$grand_total_machine+=$resource_data['machine'];

										$po_id=rtrim($production_data_arr[$f_id][$resource_id]['po_id'],',');
										$po_id=array_unique(explode(",",$po_id));
										$style=rtrim($production_data_arr[$f_id][$resource_id]['style']);
										$style=implode(",",array_unique(explode(",",$style)));

										$cbo_get_upto=str_replace("'","",$cbo_get_upto);
										$txt_parcentage=str_replace("'","",$txt_parcentage);
									   //********************************* calclution floor total    ****************************************************$pr_date],$sewing_day
										$floor_name=$floorArr[$f_id];
										$floor_smv+=$item_smv;

										$floor_days_run+=$days_run;
										$job_no=$production_data_arr[$f_id][$resource_id]['job_no'];

										$po_id=$production_data_arr[$f_id][$resource_id]['po_id'];//$item_ids//$subcon_order_id
										$styles=explode(",",$style);
										$style_button='';//
										foreach($styles as $sid)
										{
											if( $style_button=='')
											{
												$style_button="<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$resource_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
											}
											else
											{
												$style_button.=", "."<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$resource_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
											}
										}
										$as_on_current_hour_target=0; $as_on_current_hour_variance=0;
										$as_on_current_hour_target=$terget_hour*$cla_cur_time;
										$as_on_current_hour_variance=$line_production_hour-$as_on_current_hour_target;


										if($line_efficiency<=$txt_parcentage) $efficiency_color="#FF0000"; else $efficiency_color="#FFFFFF";
										if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										?>

										<tr bgcolor='<? echo $bgcolor;?>' onclick=change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>') id=tr_<? echo $i;?>>
										<td align="center" width="40" class="wrd_brk"><? echo $sewing_line;?>&nbsp;</td>
										<td width="40" align="center" class="wrd_brk"><p><? echo $buyer_name;?>&nbsp;</p></td>
										<td width="100" align="center" class="wrd_brk"><p><? echo $production_data_arr[$f_id][$resource_id]['po_number'];?>&nbsp;</p></td>
										<td width="70" onclick="openmypage_image('requires/company_wise_hourly_production_monitoring_controller.php?action=show_image&job_no=<? echo $job_no;?>','Image View')">
											<img src="../../<? echo $imge_arr[$job_no]; ?>" height="30" width="40" />
										</td>
										<td align="center" width="60"><? echo $capacity;?></td>
										<td align="center" width="70"><? echo $terget_hour;?></td>
										<?
									    if($line_efficiency<=$txt_parcentage)
										{?>
											<td align="center" width="45" bgcolor="red"><? echo number_format($line_efficiency,2).'%';?></td>
										<?}
										else
										{?>
											<td align="center" width="45"><? echo number_format($line_efficiency,2).'%';?></td>
									    <?
									    }

										$string="'";

										for($k=$hour; $k<=$last_hour; $k++)
										{
											$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";

											if($start_hour_arr[$k]==$lunch_start_hour)
											{
												 $bg_color='background:yellow';
											}
											else if($terget_hour>$production_hour[$prod_hour])
											{
												$bg_color='background:red';
												if($production_hour[$prod_hour]==0)
												{
													$bg_color='';
												}
											}
											else if($terget_hour<$production_hour[$prod_hour])
											{
												$bg_color='background:green';
												if($production_hour[$prod_hour]==0)
												{
													$bg_color='';
												}
											}
											else
											{
												$bg_color="";
											}
											?>
											<td align="center" width="40"  style='<? echo $bg_color;?>'><? echo $production_hour[$prod_hour];?></td>
										<?
									    }
									    ?>
									    <td align="center" width="50"><? echo $eff_target;?></td>

	                                    <td width="50"  align="center"><a href="##" onclick="generate_in_out_popup('<? echo $order_no_total;?>','tot_input_output_popup',<? echo $f_id;?>,<? echo $resource_id;?>,'4','<? echo $company_id;?>',<? echo $txt_date;?>)"><? echo $today_input;?></a></td>

										<td width="50"  align="center"><a href="##" onclick="generate_in_out_popup('<? echo $order_no_total;?>','tot_input_output_popup',<? echo $f_id;?>,<? echo $resource_id;?>,'5','<? echo $company_id;?>',<? echo $txt_date;?>)"><? echo $line_production_hour;?></a></td>
		                                <td class="wrd_brk"><input type="button"  value="View" class="formbutton" onclick="show_line_remarks(<? echo $cbo_company_id;?>,'<? echo $order_no_total;?>','<? echo $f_id;?>','<? echo $resource_id;?>','remarks_popup',<? echo $txt_date;?>)"/></td>

								        </tr>
								    <?
								    $i++;
							        }
							        //echo $floor_cm_value."***";die;
						        }
								if($cbo_no_prod_type==2 && $line_floor_production>0)
								{?>
							        <tr  bgcolor="#B6B6B6">
									<td width="40">&nbsp;</td>
									<td width="60">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="70">&nbsp;</td>

									<td align="center" width="60"><? echo $floor_capacity;?></td>
									<td align="center" width="70"><? echo $floor_tgt_h;?></td>
									<td align="center" width="45"><? echo number_format($floor_efficency,2).'%';?></td>

									<?
									$gnd_total_fob_val=0;
									for($k=$hour; $k<=$last_hour; $k++)
									{
										$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";

										if($start_hour_arr[$k]==$global_start_lanch)
										{
											 $bg_color='background:yellow';
										}
										if($floor_tgt_h>$floor_production[$prod_hour])
										{
											$bg_color='background:red';
											if($floor_production[$prod_hour]==0)
											{
												$bg_color='';
											}
										}
										else
										{
											 $bg_color='';
										}
								        ?>

										<td align="center" width="40" style='<? echo $bg_color;?>' ><? echo $floor_production[$prod_hour];?></td>
									<?
									}
									?>

									<td align="center" width="50"><? echo $eff_target_floor;?></td>
									<td align="center" width="50"><? echo $floor_today_input;?></td>
									<td align="center" width="50"><? echo $line_floor_production;?></td>
								    <td align="center" width=""></td>
								    </tr>
									<?
									if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

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
								    $floor_total_machine=0;
								    $floor_today_input=0;
								    $floor_total_input=0;
								    $floor_total_output=0;
								    $floor_total_wip=0;
								    $floor_cm_value=0;
							        $j++;
							    }
								if($cbo_no_prod_type==1)
								{?>
							        <tr  bgcolor="#B6B6B6">
									<td width="40">&nbsp;</td>
									<td width="60">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="70">&nbsp;</td>

									<td align="center" width="60"><? echo $floor_capacity;?></td>
									<td align="center" width="70"><? echo $floor_tgt_h;?></td>
									<td align="center" width="45"><? echo number_format($floor_efficency,2).'%';?></td>

									<?
									$gnd_total_fob_val=0;
									for($k=$hour; $k<=$last_hour; $k++)
									{
										$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";

										if($start_hour_arr[$k]==$global_start_lanch)
										{
											 $bg_color='background:yellow';
										}
										if($floor_tgt_h>$floor_production[$prod_hour])
										{
											$bg_color='background:red';
											if($floor_production[$prod_hour]==0)
											{
												$bg_color='';
											}
										}
										else
										{
											 $bg_color='';
										}
								        ?>

										<td align="center" width="40" style='<? echo $bg_color;?>' ><? echo $floor_production[$prod_hour];?></td>
									<?
									}
									?>

									<td align="center" width="50"><? echo $eff_target_floor;?></td>
									<td align="center" width="50"><? echo $floor_today_input;?></td>
									<td align="center" width="50"><? echo $line_floor_production;?></td>
								    <td align="center" width=""></td>
								    </tr>
								    <?
								    if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

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
								    $floor_total_machine=0;
								    $floor_today_input=0;
								    $floor_total_input=0;
								    $floor_total_output=0;
								    $floor_total_wip=0;
								    $floor_cm_value=0;
									$j++;
								}
							//l++;
							}
						}

			        }
					?>

		            </tbody>

				    <? $smv_for_item="";?>

	            </table>
			</div>
		</fieldset>

		<?
	}
	else  // Line wise Summary start
	{

		$html.='<tbody>';
		foreach($prod_resource_array as $company_id=>$com_name)
		{
			$global_start_lanch=$start_time_arr[$company_id][1]['lst'];

			$html.='<tr  bgcolor="#E8FFFF">
						<td width="" colspan="22"><strong>Company Name:'.$companyArr[$company_id].'</strong></td>';
						/*for($k=$hour; $k<=$last_hour; $k++)
						{
							$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
							if($k==$last_hour) $hourwidth=''; else $hourwidth='50';
							$html.='<td align="right" width='.$hourwidth.' style='.$bg_color.' ></td>';
						}
						*/

			$html.='</tr>';


			foreach($com_name as $lo_id=>$lo_name)
			{
				ksort($lo_name);
				foreach($lo_name as $f_id=>$fname)
				{
					foreach($fname as $resource_id=>$resource_data)
					{
						$germents_item=array_unique(explode('****',$production_data_arr[$f_id][$resource_id]['item_number_id']));

						$buyer_neme_all=array_unique(explode(',',$production_data_arr[$f_id][$resource_id]['buyer_name']));
						$buyer_name="";
						foreach($buyer_neme_all as $buy)
						{
							if($buyer_name!='') $buyer_name.=',';
							$buyer_name.=$buyerArr[$buy];
						}
						$garment_itemname='';
						$item_smv="";$item_ids='';
						$smv_for_item="";
						$produce_minit="";
						$order_no_total="";
						$efficiency_min=0;
						$tot_po_qty=0;$fob_val=0;$days_run=0;
						$total_input=0; $total_output=0; $min_input_date=''; $total_wip=0; $line_cm_value=0;
						$today_input=0; $total_smv_achive=0;
						foreach($germents_item as $g_val)
						{

							$po_garment_item=explode('**',$g_val);
							if($garment_itemname!='') $garment_itemname.=',';
							$garment_itemname.=$garments_item[$po_garment_item[1]];

							if($item_ids=='') $item_ids=$po_garment_item[1];else $item_ids.=",".$po_garment_item[1];

							$total_input+=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input'];
							$total_output+=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['output'];
							if($input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input_date']!='')
							{
								if($min_input_date!='')
								{
									if(strtotime($input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input_date'])<strtotime($min_input_date))
									{
										$min_input_date=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input_date'];
									}
								}
								else
								{
									$min_input_date=$input_output_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]]['input_date'];
								}
							}
							//echo $company_id."*".$f_id."*".$resource_id."*".$po_garment_item[0]."*".$search_prod_date;
							//print_r($input_po_arr);die;
						//echo $input_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]][$search_prod_date]."**";
							$today_input+=$input_po_arr[$company_id][$f_id][$resource_id][$po_garment_item[0]][$search_prod_date];

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

							$produce_minit+=$production_po_data_arr[$f_id][$resource_id][$po_garment_item[0]]*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];

							$fob_rate=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt']/$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];

							$prod_qty=$production_data_arr_qty[$f_id][$resource_id][$po_garment_item[0]][$po_garment_item[1]]['quantity'];

							$dzn_qnty=0; $cm_value=0;
							if($costing_per_arr[$po_garment_item[2]]==1) $dzn_qnty=12;
							else if($costing_per_arr[$po_garment_item[2]]==3) $dzn_qnty=12*2;
							else if($costing_per_arr[$po_garment_item[2]]==4) $dzn_qnty=12*3;
							else if($costing_per_arr[$po_garment_item[2]]==5) $dzn_qnty=12*4;
							else $dzn_qnty=1;

							$dzn_qnty=$dzn_qnty*$po_garment_item[3];
							$cm_value=($tot_cost_arr[$po_garment_item[2]]/$dzn_qnty)*$prod_qty;
							if(is_nan($cm_value)){ $cm_value=0; }

							$line_cm_value+=$cm_value;
							if(is_nan($fob_rate)){ $fob_rate=0; }
							$fob_val+=$prod_qty*$fob_rate;
						}

						//echo $today_input;die;
						//$fob_rate=$tot_po_amt/$tot_po_qty;
						$subcon_po_id=array_unique(explode(',',$production_data_arr[$f_id][$resource_id]['order_id']));
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
							$produce_minit+=$production_po_data_arr[$f_id][$resource_id][$sub_val]*$subcon_order_smv[$sub_val];
							if($subcon_order_id!="") $subcon_order_id.=",";
							$subcon_order_id.=$sub_val;
						}


						if($min_input_date!="")
						{
							$days_run=datediff("d",$min_input_date,$pr_date);
						}
						else  $days_run=0;

						$type_line=$production_data_arr[$f_id][$resource_id]['type_line'];
						$prod_reso_allo=$production_data_arr[$f_id][$resource_id]['prod_reso_allo'];

						$sewing_line='';
						$line_number=explode(",",$resource_data['line_number']);
						foreach($line_number as $val)
						{
							if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
						}


				//*************************************************************************************************************************************************
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
							 $production_hour[$prod_hour]=$production_data_arr[$f_id][$resource_id][$prod_hour];
							 $company_production[$prod_hour]+=$production_data_arr[$f_id][$resource_id][$prod_hour];
							 $floor_production[$prod_hour]+=$production_data_arr[$f_id][$resource_id][$prod_hour];
							 $total_production[$prod_hour]+=$production_data_arr[$f_id][$resource_id][$prod_hour];
						}


						// print_r($production_hour);
						$floor_production['prod_hour24']+=$production_data_arr[$f_id][$resource_id]['prod_hour23'];
						$total_production['prod_hour24']+=$production_data_arr[$f_id][$resource_id]['prod_hour23'];
						$production_hour['prod_hour24']=$production_data_arr[$f_id][$resource_id]['prod_hour23'];
						$company_production['prod_hour24']=$production_data_arr[$f_id][$resource_id]['prod_hour23'];
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
									$line_production_hour+=$production_data_arr[$f_id][$resource_id][$line_hour];
									$line_floor_production+=$production_data_arr[$f_id][$resource_id][$line_hour];
									$line_total_production+=$production_data_arr[$f_id][$resource_id][$line_hour];
									$actual_time_hour=$start_hour_arr[$lh+1];
								}
							}
							//echo $total_eff_hour.'aaaa';
							if($start_hour_arr[$actual_time]>$lunch_start_hour) $total_eff_hour=$total_eff_hour-1;

							if($total_eff_hour>$production_data_arr[$f_id][$resource_id]['working_hour'])
							{
								 $total_eff_hour=$production_data_arr[$f_id][$resource_id]['working_hour'];
							}
						}

						if(str_replace("'","",$actual_production_date)<=str_replace("'","",$actual_date))
						{
							for($ah=$hour;$ah<=$last_hour;$ah++)
							{
								$prod_hour="prod_hour".substr($start_hour_arr[$ah],0,2)."";
								$line_production_hour+=$production_data_arr[$f_id][$resource_id][$prod_hour];
								//echo $production_data_arr[$f_id][$ldata][$prod_hour];
								$line_floor_production+=$production_data_arr[$f_id][$resource_id][$prod_hour];
								$line_total_production+=$production_data_arr[$f_id][$resource_id][$prod_hour];
							}

							$total_eff_hour=$resource_data['working_hour'];
						}

						//rtdfgdfgfd 88888888888888888888888888888888888888888888888888888888888888888

						if($cbo_no_prod_type==2 && $line_production_hour>0)
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
							$line_efficiency=(($produce_minit)*100)/$efficiency_min;


							//****************************************************************************************************************

							//echo $today_input;die;

							$man_power=$resource_data['man_power'];
							$operator=$resource_data['operator'];
							$helper=$resource_data['helper'];
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

							$floor_cm_value+=$line_cm_value;
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
							$grand_total_terget+=$eff_target;
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


							$company_today_input+=$today_input;
							$company_total_input+=$total_input;
							$company_total_output+=$total_output;
							$company_total_wip+=($total_input-$total_output);
							$company_operator+=$resource_data['operator'];
							$company_man_power+=$resource_data['man_power'];
							$company_helper+=$resource_data['helper'];
							$company_capacity+=$resource_data['capacity'];
							$company_working_hour+=$resource_data['working_hour'];
							$company_total_tgt_h+=$resource_data['terget_hour'];
							$company_total_terget+=$eff_target;
							$company_total_product+=$line_production_hour;
							$company_avable_min+=$efficiency_min;
							$company_product_min+=$produce_minit;
							$company_total_fob_val+=$fob_val;
							$company_final_total_fob_val+=$fob_val;
							$company_cm_value+=$line_cm_value;

							$floor_total_smv_achive+=$total_smv_achive;
							$company_total_smv_achive+=$total_smv_achive;
							$grand_total_smv_achive+=$total_smv_achive;

							$floor_total_machine+=$resource_data['machine'];
							$company_total_machine+=$resource_data['machine'];
							$grand_total_machine+=$resource_data['machine'];


							$po_id=rtrim($production_data_arr[$f_id][$resource_id]['po_id'],',');
							$po_id=array_unique(explode(",",$po_id));
							$style=rtrim($production_data_arr[$f_id][$resource_id]['style']);
							$style=implode(",",array_unique(explode(",",$style)));

							$cbo_get_upto=str_replace("'","",$cbo_get_upto);
							$txt_parcentage=str_replace("'","",$txt_parcentage);
						   //********************************* calclution floor total    ****************************************************$pr_date],$sewing_day
							$floor_name=$floorArr[$f_id];
							$floor_smv+=$item_smv;

							$floor_days_run+=$days_run;

							$po_id=$production_data_arr[$f_id][$resource_id]['po_id'];//$item_ids//$subcon_order_id
							$styles=explode(",",$style);
							$style_button='';//
							foreach($styles as $sid)
							{
								if( $style_button=='')
								{
									$style_button="<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$resource_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
								}
								else
								{
									$style_button.=", "."<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$resource_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
								}
							}
							$as_on_current_hour_target=0; $as_on_current_hour_variance=0;
							$as_on_current_hour_target=$terget_hour*$cla_cur_time;
							$as_on_current_hour_variance=$line_production_hour-$as_on_current_hour_target;

							if($line_efficiency<=$txt_parcentage) $efficiency_color="#FF0000"; else $efficiency_color="#FFFFFF";
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$itmSmvArray = explode("/", $item_smv);
							$newStringSMV = "";
							foreach($itmSmvArray as $itmSMV){
								$newStringSMV .= number_format((float)$itmSMV, 2)."/";
							}
							$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
							$html.='<td width="40">'.$i.'&nbsp;</td>
									<td width="80">'.$floor_name.'&nbsp; </td>
									<td align="center" width="80" >'. $sewing_line.'&nbsp; </td>
									<td width="80"><p>'.$buyer_name.'&nbsp;</p></td>
									<td width="140"><p>'.$production_data_arr[$f_id][$resource_id]['po_number'].'&nbsp;</p></td>
									<td width="100"><p>'.$style_button.'&nbsp;</p></td>
									<td width="120" style="word-wrap:break-word; word-break: break-all;">'.$garment_itemname.'</td>
									<td align="right" width="60"><p>'.$newStringSMV.'</p></td>
									<td align="right" width="60">'.$working_hour.'</td>
									<td align="right" width="80">'.$eff_target.'</td>';
									$string="'";
									$html.='<td align="right" width="80">'.change_date_format($min_input_date).'</td>
									<td width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'4','".$company_id."',".$txt_date.')">'.$today_input.'</a></td>
									<td width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'1','".$company_id."',".$txt_date.')">'.$total_input.'</a></td>
									<td width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'5','".$company_id."',".$txt_date.')">'.$line_production_hour.'</a></td>
									<td width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'2','".$company_id."',".$txt_date.')">'.$total_output.'</a></td>
									<td width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'3','".$company_id."',".$txt_date.')">'.($total_wip=$total_input-$total_output).'</a></td>

									<td align="right" width="80">'.$as_on_current_hour_variance.'</td>

									<td align="right" width="60" >'. number_format(($line_production_hour/$eff_target)*100,2).'%</td>

									<td align="right" width="80">'.number_format($line_cm_value,2).'</td>
									<td width="80" title="'.$fob_rate.'" align="right"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$resource_id.",'tot_fob_value_popup','".$smv_for_item."','".$actual_time_hour."','".$line_start."',".$txt_date.')">'.number_format($fob_val,2).'</a></td>';


									$html.='<td align="right" width="100">'.$resource_data['line_chief'].'</td>';


									/*for($k=$hour; $k<=$last_hour; $k++)
									{
										$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
										//if($k==$last_hour) $hourwidth=''; else $hourwidth='50';

										if($start_hour_arr[$k]==$lunch_start_hour)
										{
											 $bg_color='background:yellow';
										}
										else if($terget_hour>$production_hour[$prod_hour])
										{
											$bg_color='background:red';
											if($production_hour[$prod_hour]==0)
											{
												$bg_color='';
											}
										}
										else if($terget_hour<$production_hour[$prod_hour])
										{
											$bg_color='background:green';
											if($production_hour[$prod_hour]==0)
											{
												$bg_color='';
											}
										}
										else
										{
											$bg_color="";
										}

										$html.='<td align="right" width="50"  style='.$bg_color.'>'.$production_hour[$prod_hour].'</td>';
									}*/
									$html.='<td><input type="button"  value="View" class="formbutton" onclick="show_line_remarks('.$cbo_company_id.",'".$order_no_total."','".$f_id."','".$resource_id."','remarks_popup',".$txt_date.')"/></td>';

							$html.='</tr>';
							$i++;
						}
						if($cbo_no_prod_type==1)
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
							$line_efficiency=(($produce_minit)*100)/$efficiency_min;


							//****************************************************************************************************************

							$man_power=$resource_data['man_power'];
							$operator=$resource_data['operator'];
							$helper=$resource_data['helper'];
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
							$floor_today_product+=$today_product;
							$floor_avale_minute+=$efficiency_min;
							$floor_produc_min+=$produce_minit;
							$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;

							$floor_today_input+=$today_input;
							$floor_cm_value+=$line_cm_value;
							$floor_total_input+=$total_input;
							$floor_total_output+=$total_output;
							$floor_total_wip+=($total_input-$total_output);

							$total_operator+=$resource_data['operator'];
							$total_man_power+=$resource_data['man_power'];
							$total_helper+=$resource_data['helper'];
							$total_capacity+=$resource_data['capacity'];
							$total_working_hour+=$resource_data['working_hour'];
							$gnd_total_tgt_h+=$resource_data['terget_hour'];
							$grand_total_terget+=$eff_target;
							$grand_total_product+=$today_product;
							$gnd_avable_min+=$efficiency_min;
							$gnd_product_min+=$produce_minit;
							$gnd_total_fob_val+=$fob_val;
							$gnd_final_total_fob_val+=$fob_val;

							$grand_today_input+=$today_input;
							$grand_total_input+=$total_input;
							$grand_total_output+=$total_output;
							$grand_total_wip+=($total_input-$total_output);
							$grand_cm_value+=$line_cm_value;

							$company_today_input+=$today_input;
							$company_total_input+=$total_input;
							$company_total_output+=$total_output;
							$company_total_wip+=($total_input-$total_output);
							$company_operator+=$resource_data['operator'];
							$company_man_power+=$resource_data['man_power'];
							$company_helper+=$resource_data['helper'];
							$company_capacity+=$resource_data['capacity'];
							$company_working_hour+=$resource_data['working_hour'];
							$company_total_tgt_h+=$resource_data['terget_hour'];
							$company_total_terget+=$eff_target;
							$company_total_product+=$line_production_hour;
							$company_avable_min+=$efficiency_min;
							$company_product_min+=$produce_minit;
							$company_total_fob_val+=$fob_val;
							$company_final_total_fob_val+=$fob_val;
							$company_cm_value+=$line_cm_value;


							$floor_total_smv_achive+=$total_smv_achive;
							$company_total_smv_achive+=$total_smv_achive;
							$grand_total_smv_achive+=$total_smv_achive;


							$floor_total_machine+=$resource_data['machine'];
							$company_total_machine+=$resource_data['machine'];
							$grand_total_machine+=$resource_data['machine'];

							$po_id=rtrim($production_data_arr[$f_id][$resource_id]['po_id'],',');
							$po_id=array_unique(explode(",",$po_id));
							$style=rtrim($production_data_arr[$f_id][$resource_id]['style']);
							$style=implode(",",array_unique(explode(",",$style)));

							$cbo_get_upto=str_replace("'","",$cbo_get_upto);
							$txt_parcentage=str_replace("'","",$txt_parcentage);
						   //********************************* calclution floor total    ****************************************************$pr_date],$sewing_day
							$floor_name=$floorArr[$f_id];
							$floor_smv+=$item_smv;

							$floor_days_run+=$days_run;

							$po_id=$production_data_arr[$f_id][$resource_id]['po_id'];//$item_ids//$subcon_order_id
							$styles=explode(",",$style);
							$style_button='';//
							foreach($styles as $sid)
							{
								if( $style_button=='')
								{
									$style_button="<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$resource_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
								}
								else
								{
									$style_button.=", "."<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$resource_id."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
								}
							}
							$as_on_current_hour_target=0; $as_on_current_hour_variance=0;
							$as_on_current_hour_target=$terget_hour*$cla_cur_time;
							$as_on_current_hour_variance=$line_production_hour-$as_on_current_hour_target;

							if($line_efficiency<=$txt_parcentage) $efficiency_color="#FF0000"; else $efficiency_color="#FFFFFF";
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$itmSmvArray = explode("/", $item_smv);
							$newStringSMV = "";
							foreach($itmSmvArray as $itmSMV){
								$newStringSMV .= number_format((float)$itmSMV, 2)."/";
							}
							$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
							$html.='<td width="40">'.$i.'&nbsp;</td>
									<td width="80">'.$floor_name.'&nbsp; </td>
									<td align="center" width="80" >'. $sewing_line.'&nbsp; </td>
									<td width="80"><p>'.$buyer_name.'&nbsp;</p></td>
									<td width="140"><p>'.$production_data_arr[$f_id][$resource_id]['po_number'].'&nbsp;</p></td>
									<td width="100"><p>'.$style_button.'&nbsp;</p></td>
									<td width="120" style="word-wrap:break-word; word-break: break-all;">'.$garment_itemname.'</td>
									<td align="right" width="60"><p>'.$newStringSMV.'</p></td>


									<td align="right" width="60">'.$working_hour.'</td>

									<td align="right" width="80">'.$eff_target.'</td>';
									$string="'";

									$html.='<td align="right" width="80">'.change_date_format($min_input_date).'</td>
									<td width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'4','".$company_id."',".$txt_date.')">'.$today_input.'</a></td>
									<td width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'1','".$company_id."',".$txt_date.')">'.$total_input.'</a></td>
									<td width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'5','".$company_id."',".$txt_date.')">'.$line_production_hour.'</a></td>
									<td width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'2','".$company_id."',".$txt_date.')">'.$total_output.'</a></td>
									<td width="80"  align="right"><a href="##" onclick="generate_in_out_popup('.$string.$order_no_total."','tot_input_output_popup',".$f_id.",".$resource_id.",'3','".$company_id."',".$txt_date.')">'.($total_wip=$total_input-$total_output).'</a></td>

									<td align="right" width="80">'.$as_on_current_hour_variance.'</td>
									<td align="right" width="60" >'. number_format(($line_production_hour/$eff_target)*100,2).'%</td>
									<td align="right" width="80">'.number_format($line_cm_value,2).'</td>
									<td width="80" title="'.$fob_rate.'" align="right"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$resource_id.",'tot_fob_value_popup','".$smv_for_item."','".$actual_time_hour."','".$line_start."',".$txt_date.')">'.number_format($fob_val,2).'</a></td>';
									$html.='<td align="right" width="100">'.$resource_data['line_chief'].'</td>';

									/*for($k=$hour; $k<=$last_hour; $k++)
									{
										$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";

										if($start_hour_arr[$k]==$lunch_start_hour)
										{
											 $bg_color='background:yellow';
										}
										else if($terget_hour>$production_hour[$prod_hour])
										{
											$bg_color='background:red';
											if($production_hour[$prod_hour]==0)
											{
												$bg_color='';
											}
										}
										else if($terget_hour<$production_hour[$prod_hour])
										{
											$bg_color='background:green';
											if($production_hour[$prod_hour]==0)
											{
												$bg_color='';
											}
										}
										else
										{
											$bg_color="";
										}

										$html.='<td align="right" width="50"  style='.$bg_color.'>'.$production_hour[$prod_hour].'</td>';
										//$html.='<td align="right" width="50"  style=" background-color:#FFFF66" >'.$production_hour[$prod_hour].'&nbsp;kk</td>';
									}*/
							$html.='<td><input type="button"  value="View" class="formbutton" onclick="show_line_remarks('.$cbo_company_id.",'".$order_no_total."','".$f_id."','".$resource_id."','remarks_popup',".$txt_date.')"/></td>';

							$html.='</tr>';
							$i++;
						}
						//echo $floor_cm_value."***";die;
					}

					if($cbo_no_prod_type==2 && $line_floor_production>0)
					{
						$html.='<tr  bgcolor="#B6B6B6">
								<td width="40">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="140">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="120">&nbsp;</td>
								<td align="right" width="60">&nbsp;</td>

								<td align="right" width="60">'. $floor_working_hour.'</td>
								<td align="right" width="80">'.$eff_target_floor.'</td>
								<td align="right" width="80"></td>

								<td align="right" width="80">'.$floor_today_input.'</td>
								<td align="right" width="80">'. $floor_total_input.'</td>
								<td align="right" width="80">'.$line_floor_production.'</td>
								<td align="right" width="80">'. $floor_total_output.'</td>
								<td align="right" width="80">'. $floor_total_wip.'</td>
								<td align="right" width="80">'. ($line_floor_production-$eff_target_floor).';</td>
								<td align="right" width="60">'. number_format(($line_floor_production/$eff_target_floor)*100,2).'%</td>
								<td align="right" width="80">'.number_format($floor_cm_value,2).'</td>
								<td align="right" width="80">'.number_format($gnd_total_fob_val,2).'</td>

								<td align="right" width="100"></td>
								';

								$gnd_total_fob_val=0;
								/*for($k=$hour; $k<=$last_hour; $k++)
								{
									$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";

									if($start_hour_arr[$k]==$global_start_lanch)
									{
										 $bg_color='background:yellow';
									}
									if($floor_tgt_h>$floor_production[$prod_hour])
									{
										$bg_color='background:red';
										if($floor_production[$prod_hour]==0)
										{
											$bg_color='';
										}
									}
									else
									{
										 $bg_color='';
									}


									$html.='<td align="right" width="50" style='.$bg_color.' >'. $floor_production[$prod_hour].'</td>';
								}*/

							$html.='<td align="right" width=""></td></tr>';
							if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

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
						  $floor_total_machine=0;
						  $floor_today_input=0;
						  $floor_total_input=0;
						  $floor_total_output=0;
						 $floor_total_wip=0;
						 $floor_cm_value=0;
					$j++;
					}
					if($cbo_no_prod_type==1)
					{
						$html.='<tr  bgcolor="#B6B6B6">
								<td width="40">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="140">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="120">&nbsp;</td>
								<td align="right" width="60">&nbsp;</td>

								<td align="right" width="60">'. $floor_working_hour.'</td>
								<td align="right" width="80">'.$eff_target_floor.'</td>
								<td align="right" width="80"></td>
								<td align="right" width="80">'.$floor_today_input.'</td>
								<td align="right" width="80">'. $floor_total_input.'</td>
								<td align="right" width="80">'.$line_floor_production.'</td>
								<td align="right" width="80">'. $floor_total_output.'</td>
								<td align="right" width="80">'. $floor_total_wip.'</td>
								<td align="right" width="80">'. ($line_floor_production-$eff_target_floor).';</td>
								<td align="right" width="60">'. number_format(($line_floor_production/$eff_target_floor)*100,2).'%</td>
								<td align="right" width="80">'.number_format($floor_cm_value,2).'</td>
								<td align="right" width="80">'.number_format($gnd_total_fob_val,2).'</td>

								<td align="right" width="100"></td>
								';

								$gnd_total_fob_val=0;
								/*for($k=$hour; $k<=$last_hour; $k++)
								{
									$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";

									if($start_hour_arr[$k]==$global_start_lanch)
									{
										 $bg_color='background:yellow';
									}
									if($floor_tgt_h>$floor_production[$prod_hour])
									{
										$bg_color='background:red';
										if($floor_production[$prod_hour]==0)
										{
											$bg_color='';
										}
									}
									else
									{
										 $bg_color='';
									}


									$html.='<td align="right" width="50" style='.$bg_color.' >'. $floor_production[$prod_hour].'</td>';
								}*/

							$html.='<td align="right" width=""></td></tr>';
							if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

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
									  $floor_total_machine=0;
									  $floor_today_input=0;
									  $floor_total_input=0;
									  $floor_total_output=0;
									 $floor_total_wip=0;
									 $floor_cm_value=0;
								$j++;
					}
				}
			}

			// company -total checked
			$html.='<tr class="tbl_bottom">
					<td width="700" colspan="8">'.$companyArr[$company_id].' Company Total</td>

					<td align="right" width="60">'. $company_working_hour.'</td>

					<td align="right" width="80">'.$company_total_terget.'</td>
					<td align="right" width="80"></td>
					<td align="right" width="80">'.$company_today_input.'</td>
					<td align="right" width="80">'. $company_total_input.'</td>
					<td align="right" width="80">'.$company_total_product.'</td>
					<td align="right" width="80">'. $company_total_output.'</td>
					<td align="right" width="80">'. $company_total_wip.'</td>
					<td align="right" width="80">'. ($company_total_product-$company_total_terget).'</td>

					<td align="right" width="60">'. number_format(($company_total_product/$company_total_terget)*100,2).'%</td>
					<td align="right" width="80">'.number_format($company_cm_value,2).'</td>
					<td align="right" width="80">'.number_format($company_final_total_fob_val,2).'</td>';

					//$gnd_total_fob_val=0;
					/*for($k=$hour; $k<=$last_hour; $k++)
					{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
						//if($k==$last_hour) $hourwidth=''; else $hourwidth='50';

						if($start_hour_arr[$k]==$global_start_lanch)
						{
							 $bg_color='background:yellow';
						}
						if($floor_tgt_h>$floor_production[$prod_hour])
						{
							$bg_color='background:red';
							if($floor_production[$prod_hour]==0)
							{
								$bg_color='';
							}
						}
						else
						{
							 $bg_color='';
						}
						$html.='<td align="right" width="50" style='.$bg_color.' >'. $company_production[$prod_hour].'</td>';
					}*/


			$html.='<td align="right" width=""></td></tr>';


			$company_total_tgt_h=0;
			$company_capacity=0;
			$company_working_hour=0;
			$company_final_total_fob_val=0;
			$company_total_terget=0;
			$company_total_product=0;
			$company_avable_min=0;
			$company_product_min=0;
			$company_man_power=0;
			$company_helper=0;
			$company_operator=0;
			$company_total_input=0;
			$company_total_output=0;
			$company_total_wip=0;
			$company_cm_value=0;
			$company_today_input=0;
			$company_total_machine=0;
			$company_total_smv_achive=0;
			unset($company_production);
		}



			$html.='</tbody>';
			$smv_for_item="";
		?>

		<fieldset style="width:2030px">
	       <table width="1700" cellpadding="0" cellspacing="0">
	            <tr class="form_caption">
	                <td colspan="26" align="center"><strong><? echo $report_title; ?></strong></td>
	            </tr>
	            <tr class="form_caption">
	                <td colspan="26" align="center"><strong><? echo $companyArr[$comapny_id]; ?></strong></td>
	            </tr>
	            <tr class="form_caption">
	                <td colspan="26" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date)) ); ?></strong></td>
	            </tr>
	        </table>
	        <br />
	        <table  width="600" cellpadding="0"  cellspacing="0" align="center" style="padding-left:200px">
	            <tr>

	                <td bgcolor="#FFFF66" height="18" width="30" ></td>
	                <td> &nbsp;Lunch Hour</td>
	                <td bgcolor="red" height="18" width="30"></td>
	                <td> &nbsp;Efficiency % less than Standard And Production less than Target</td>

	            </tr>
	        </table>

	    </br><br/>
	        <table id="table_header_1" class="rpt_table" width="1820" cellpadding="0" cellspacing="0" border="1" rules="all">
	            <thead>
	                <tr height="50">
	                    <th width="40">SL</th>
	                    <th width="80">Floor Name</th>
	                    <th width="80">Line No</th>
	                    <th width="80">Buyer</th>
	                    <th width="140">Order No</th>
	                    <th width="100">Style Ref.</th>
	                    <th width="120">Garments Item</th>
	                    <th width="60">SMV</th>

	                    <th width="60">Working Hour</th>
	                    <th width="80">Today Target</th>
	                    <th width="80">1st Input Date</th>
	                    <th width="80">Today Input</th>
	                    <th width="80">Total Input</th>
	                    <th width="80">Today Prod.</th>
	                    <th width="80">Total Prod.</th>
	                    <th width="80">WIP</th>
	                    <th width="80">Today Variance (Pcs)</th>
	                    <th width="60">Today Achv %</th>
	                    <th width="80">Today CM Value</th>
	                    <th width="80">Today Prod. FOB value</th>
	                    <th width="100">Line Chief</th>
	                   <!-- <?

	                	//for($k=$hour+1; $k<=$last_hour+1; $k++)
						{
							//if($k==$last_hour+1) $hourwidth=''; else $hourwidth='50';
						?>
	                      	<th width="50" style="vertical-align:middle"><div class="block_div"><?  //echo substr($start_hour_arr[$k],0,5);   ?></div></th>
						<?
						}
	                ?> -->
	                	<th width=""> Remarks</th>
	                </tr>
	            </thead>
	        </table>
	        <div style="width:1820; max-height:400px; overflow-y:scroll" id="scroll_body">
	            <table class="rpt_table" width="1800" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
	             	 <? echo $html;  ?>
	           		 <tfoot>
	                    <tr>
	                        <th width="40">&nbsp;</th>
	                        <th width="80">&nbsp;</th>
	                        <th width="80">&nbsp;</th>
	                        <th width="80">&nbsp;</th>
	                        <th width="140">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="120">Group Total</th>
	                        <th align="right" width="60"><? // echo number_format($total_smv/($i-1),2); ?>&nbsp;</th>


	                        <th align="right" width="60"><? echo $total_working_hour; ?></th>

	                        <th align="right" width="80"><? echo $grand_total_terget; ?>&nbsp;</th>
	                        <th align="right" width="80"></th>
	                        <th align="right" width="80"><? echo $grand_today_input; ?>&nbsp;</th>
	                        <th align="right" width="80"><? echo $grand_total_input; ?></th>
	                        <th align="right" width="80"><? echo $line_total_production; ?>&nbsp;</th>
	                        <th align="right" width="80"><? echo $grand_total_output; ?></th>
	                        <th align="right" width="80"><? echo $grand_total_wip; ?></th>
	                        <th align="right" width="80"><? echo $line_total_production-$grand_total_terget; ?>&nbsp;</th>

	                        <th align="right" width="60"><? echo number_format(($line_total_production/$grand_total_terget)*100,2)."%"; ?>&nbsp;</th>

	                        <th align="right" width="80"><? echo number_format($grand_cm_value,2);?>&nbsp;</th>
	                        <th align="right" width="80"><? echo number_format($gnd_final_total_fob_val,2);?>&nbsp;</th>


	                        <th align="right" width="100"></th>
	                        <!-- <?
	                       // for($k=$hour; $k<=$last_hour; $k++)
	                        {
								//$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
								//if($k==$last_hour) $hourwidth=''; else $hourwidth='50';
								?>
									<th align="right" width="<?php //echo $hourwidth;?> "><? //echo $total_production[$prod_hour]; ?></th>
								<?
	                        }
	                        ?> -->
	                        <th width=""></th>
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
	echo "$total_data####$filename####$type";
	exit();

} // 4th Button End

if($action=="report_generate5") //5th Button Start...
{
	extract($_REQUEST);
	$process = array( &$_POST );
	$monthStartDate = "'".date("01-M-Y", strtotime(trim($txt_date, "'")))."'";

	extract(check_magic_quote_gpc( $process ));
	$companyArr = return_library_array("select id,company_name from lib_company","id","company_name");
	$buyerArr = return_library_array("select id,short_name from lib_buyer","id","short_name");
	$locationArr = return_library_array("select id,location_name from lib_location","id","location_name");
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
	$floorSeqArr = return_library_array("select id,floor_serial_no from lib_prod_floor","id","floor_serial_no");
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$comapny_id=str_replace("'","",$cbo_company_id);
    $today_date=date("Y-m-d");
	$txt_producting_day="".str_replace("'","",$txt_date)."";

	/***************************************************************************************************************************/
	$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
	order by sewing_line_serial");
	foreach($lineDataArr as $lRow)
	{
		$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
		$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
		$lastSlNo=$lRow[csf('sewing_line_serial')];
	}

	if($db_type==0)
	{
		$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$comapny_id and shift_id=1 and pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
	}
	else
	{
		$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$comapny_id and shift_id=1 and pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
	}
	//echo "<pre>"; print_r($min_shif_start); die;
	if($min_shif_start=="")
	{
		echo "<p style='font-size:20px', align='center'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<p/>";die;
	}


	//==============================shift time===================================================================================================
	$start_time_arr=array();
	if($db_type==0)
	{
		$start_time_data_arr=sql_select("SELECT company_name, shift_id, TIME_FORMAT( prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( lunch_start_time, '%H:%i' ) as lunch_start_time from variable_settings_production where company_name in($comapny_id) and shift_id=1  and variable_list=26 and status_active=1 and is_deleted=0");
	}
	else
	{
		$start_time_data_arr=sql_select("SELECT company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time,TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($comapny_id) and  shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");
	}

	foreach($start_time_data_arr as $row)
	{
		$start_time_arr[$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
		$start_time_arr[$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
	}
	//print_r($start_time_arr); exit();

	$prod_start_hour=$start_time_arr[1]['pst'];
	$global_start_lanch=$start_time_arr[1]['lst'];
	if($prod_start_hour=="") $prod_start_hour="08:00";
	//$prod_start_hour="09:00";
	$start_time=explode(":",$prod_start_hour);
	$hour=(int)substr($start_time[0],1,1); $minutes=$start_time[1]; $last_hour=23;
	$lineWiseProd_arr=array(); $prod_arr=array(); $start_hour_arr=array();
	$start_hour=$prod_start_hour;
	$start_hour_arr[$hour]=$start_hour;
	for($j=$hour;$j<$last_hour;$j++)
	{
		$start_hour=add_time($start_hour,60);
		$start_hour_arr[$j+1]=substr($start_hour,0,5);
	}

	$start_hour_arr[$j+1]='23:59';
	$prod_start_hour>$min_shif_start ?  $prod_start_hour=$prod_start_hour : $prod_start_hour=$min_shif_start;
	$actual_date=date("Y-m-d");
	$actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$txt_date)));
	$actual_time=substr(date("Y-m-d H:i:s",strtotime($pc_date_time)),11,2);
	$acturl_hour_minute=date("H:i",strtotime($pc_date_time));
	$generated_hourarr=array();
	$first_hour_time=explode(":",$prod_start_hour);

	$hour_line=$first_hour_time[0]; $minutes_one=$start_time[1];
	$line_start_hour_arr[$hour_line]=$prod_start_hour;
	for($l=$hour_line;$l<$last_hour;$l++)
	{
		$prod_start_hour=add_time($prod_start_hour,60);
		$line_start_hour_arr[$l+1]=substr($prod_start_hour,0,5);
	}

	$line_start_hour_arr[$j+1]='23:59'; //$line_start_hour_arr is the line where loops on the table header.
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_id and variable_list=23 and is_deleted=0 and status_active=1");
	//echo "<pre>"; print_r($prod_reso_allo); exit();
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		$buyer_id_cond="";
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
	if($cbo_floor_id=="") $floor=""; else $floor=" and a.floor_id in(".$cbo_floor_id.")";
    if(str_replace("'","",$hidden_line_id)==0)
	{
		$line="";
		$subcon_line="";
		$line_number="";
	}
	else
	{
		$line_number=" and a.id in(".str_replace("'","",$hidden_line_id).")";
		$subcon_line=" and a.line_id in(".str_replace("'","",$hidden_line_id).")";
		$line=" and a.sewing_line in(".str_replace("'","",$hidden_line_id).")";
	}
	$cbo_no_prod_type=str_replace("'","",$cbo_no_prod_type);
	$file_no=str_replace("'","",$txt_file_no);
	$ref_no=str_replace("'","",$txt_ref_no);
	if($file_no!="") $file_cond="and c.file_no=$file_no";else $file_cond="";
	if($ref_no!="") $ref_cond="and c.grouping='$ref_no'";else $ref_cond="";
	//echo $file_cond;

	if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date between $monthStartDate and $txt_date ";
	// echo $txt_date_from; die;

	if($prod_reso_allo[0]==1)
	{
		$prod_resource_array=array();

		$dataArray_sql=sql_select("SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity, c.line_chief,d.SEWING_LINE_SERIAL
		from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c, LIB_SEWING_LINE d where a.id=c.mst_id and c.id=b.mast_dtl_id and REGEXP_SUBSTR( a.line_number, '[^,]+', 1)=d.id  and a.company_id=$comapny_id $subcon_location $line_number $floor and b.pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 order by a.floor_id, d.SEWING_LINE_SERIAL
	");
		// echo $dataArray_sql; die;	b.id,c.id
		$sewing_line='';
		foreach($dataArray_sql as $val)
		{
			$sewing_line_ids=$val['LINE_NUMBER'];

			$sl_ids_arr = explode(",", $sewing_line_ids);
			$sewing_line_id = $sl_ids_arr[0]; // always 1st line id will take
			foreach($sl_ids_arr as $v)
			{
			 $sewing_line=$lineArr[$v];
			}



			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id]=$slNo;
			}
			else $slNo=$lineSerialArr[$sewing_line_id];
			// arsort($slNo);
			// echo $slNo."<br>";


			$prod_resource_array[$val[csf('line_chief')]][$slNo][$sewing_line][$val[csf('id')]][$val[csf('pr_date')]]['man_power']=$val[csf('man_power')];

			$prod_resource_array[$val[csf('line_chief')]][$slNo][$sewing_line][$val[csf('id')]][$val[csf('pr_date')]]['operator']=$val[csf('operator')];

			$prod_resource_array[$val[csf('line_chief')]][$slNo][$sewing_line][$val[csf('id')]][$val[csf('pr_date')]]['helper']=$val[csf('helper')];

			$prod_resource_array[$val[csf('line_chief')]][$slNo][$sewing_line][$val[csf('id')]][$val[csf('pr_date')]]['hourly_target']=$val[csf('target_per_hour')];

			$prod_resource_array[$val[csf('line_chief')]][$slNo][$sewing_line][$val[csf('id')]][$val[csf('pr_date')]]['working_hour']=$val[csf('working_hour')];

			$prod_resource_array[$val[csf('line_chief')]][$slNo][$sewing_line][$val[csf('id')]][$val[csf('pr_date')]]['tpd']=$val[csf('target_per_hour')]*$val[csf('working_hour')];

			$prod_resource_array[$val[csf('line_chief')]][$slNo][$sewing_line][$val[csf('id')]][$val[csf('pr_date')]]['day_start']=$val[csf('from_date')];

			$prod_resource_array[$val[csf('line_chief')]][$slNo][$sewing_line][$val[csf('id')]][$val[csf('pr_date')]]['day_end']=$val[csf('to_date')];

			$prod_resource_array[$val[csf('line_chief')]][$slNo][$sewing_line][$val[csf('id')]][$val[csf('pr_date')]]['capacity']=$val[csf('capacity')];

			$prod_resource_array[$val[csf('line_chief')]][$slNo][$sewing_line][$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust']=$val[csf('smv_adjust')];

			$prod_resource_array[$val[csf('line_chief')]][$slNo][$sewing_line][$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];

			// $prod_resource_array[$val[csf('line_chief')]][$slNo][$sewing_line][$val[csf('id')]][$val[csf('pr_date')]]['line_number']=$val[csf('line_number')];


		}
		// echo "<pre>"; print_r($prod_resource_array); die;
		if(str_replace("'","",trim($txt_date))==""){$pr_date_con="";}else{$pr_date_con=" and b.pr_date=$txt_date";}

		if($db_type==0)
		{
			$dataArray=sql_select("SELECT a.id,b.pr_date,d.shift_id,TIME_FORMAT( d.prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( d.lunch_start_time, '%H:%i' ) as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 $pr_date_con");
		}
		else
		{
			$dataArray=sql_select("SELECT a.id,b.pr_date,d.shift_id,TO_CHAR(d.prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR( d.lunch_start_time,'HH24:MI') as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 $pr_date_con");
		}
		//echo "<pre>"; print_r($dataArray); die;
		$line_number_arr=array();
		foreach($dataArray as $val)
		{
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['shift_id']=$val[csf('shift_id')];
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['prod_start_time']=$val[csf('prod_start_time')];
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['lunch_start_time']=$val[csf('lunch_start_time')];
		}
	}

	//for production data..

	$sql="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name  as buyer_name,b.style_ref_no,b.job_no, a.po_break_down_id, b.avg_unit_price, a.item_number_id,c.po_number as po_number,c.file_no,c.unit_price,c.grouping as ref,sum(case when a.production_type=4 then d.production_qnty else 0 end) as input_qnty,sum(case when a.production_type=5 then d.production_qnty else 0 end) as good_qnty, a.production_type,e.order_rate,e.article_number,";
	$first=1;
	for($h=$hour;$h<$last_hour;$h++)
	{
		$bg=$start_hour_arr[$h];
		$end=substr(add_time($start_hour_arr[$h],60),0,5);
		$prod_hour="prod_hour".substr($bg,0,2);
		//echo $end."##".$prod_hour; exit();
		if($first==1)
		{
			$sql.="sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type IN(5) THEN d.production_qnty else 0 END) AS $prod_hour,";
		}
		else
		{
			$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$bg' and TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type IN(5)
			THEN d.production_qnty else 0 END) AS $prod_hour,";
		}
		$first++;
	}
	/*$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<'$start_hour_arr[24]' and a.production_type IN(5) THEN d.production_qnty else 0 END) AS prod_hour23
	FROM
		wo_po_details_master b,
		wo_po_break_down c,
		wo_po_color_size_breakdown e,
		pro_garments_production_mst a ,
		pro_garments_production_dtls d
	WHERE
	a.po_break_down_id=c.id and c.job_id=b.id and a.id=d.mst_id
	and b.id=e.job_id and c.id=e.po_break_down_id and e.id=d.color_size_break_down_id and
	a.production_type in (4,5) and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_name $location $floor $line $buyer_id_cond  $txt_date_from $file_cond $ref_cond
	GROUP BY b.job_no, a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name,b.style_ref_no,a.item_number_id,c.po_number,c.unit_price,c.file_no,c.grouping, b.avg_unit_price, a.production_type, e.order_rate,e.article_number
	ORDER BY a.location,a.floor_id,a.sewing_line";*/

	$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<'$start_hour_arr[24]' and a.production_type IN(5) THEN d.production_qnty else 0 END) AS prod_hour23
	FROM
		wo_po_details_master b,
		wo_po_break_down c,
		wo_po_color_size_breakdown e,
		pro_garments_production_dtls d,
		pro_garments_production_mst a
	WHERE
	b.id=c.job_id and c.id=e.po_break_down_id and e.id=d.color_size_break_down_id and d.mst_id=a.id and
	a.production_type in (4,5) and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_name $location $floor $line $buyer_id_cond  $txt_date_from $file_cond $ref_cond
	GROUP BY b.job_no, a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name,b.style_ref_no,a.item_number_id,c.po_number,c.unit_price,c.file_no,c.grouping, b.avg_unit_price, a.production_type, e.order_rate,e.article_number";

	//echo $sql;die;
	$sql_resqlt=sql_select($sql);
	$production_data_arr=array();
	$production_po_data_arr=array();
	$production_serial_arr=array(); $reso_line_ids=''; $all_po_id="";
	$active_days_arr=array();
	$duplicate_date_arr=array();
	$style_wise_po_arr=array();
	$job_wise_po_arr=array();
	$all_style_arr=array();
	$all_job_arr=array();
	$summeryArray = array();
	$poJobArray = array();
	$lineStyleJobArray = array();
	$line_wise_article_arr = array();
	foreach($sql_resqlt as $val)
	{


		if($val[csf('production_type')] == 5)
		{
			$dbDate = date("d-M-Y", strtotime($val[csf('production_date')]));
			if("'".$dbDate."'" == $txt_date){
				//echo "'".$dbDate."' == ".$txt_date; exit();
				$line_wise_article_arr[$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val['ARTICLE_NUMBER']]=$val['ARTICLE_NUMBER'];
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
				$production_serial_arr[$floorSeqArr][$slNo][$val[csf('sewing_line')]]=$val[csf('sewing_line')];

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
					//$production_data_arr[$val[csf('sewing_line')]][$prod_hour]+=$val[csf($prod_hour)];
					$production_data_arr [$val[csf('sewing_line')]] [$val[csf('po_break_down_id')]] [$val[csf('item_number_id')]] [$prod_hour] += $val[csf($prod_hour)];
					//a.po_break_down_id, b.avg_unit_price, a.item_number_id
					if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
					{
						if( $h>=$line_start_hour && $h<=$actual_time)
						{
							$production_po_data_arr[$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]+=$val[csf($prod_hour)];
						}
					}

					if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date))
					{
						$production_po_data_arr[$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]+=$val[csf($prod_hour)];
					}
				}

				//echo "<pre>"; print_r($production_data_arr); exit();

				if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
				{
					if( $h>=$line_start_hour && $h<=$actual_time)
					{
						$production_po_data_arr[$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]+=$val[csf('prod_hour23')];
					}
				}
				else
				{
					$production_po_data_arr[$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]+=$val[csf('prod_hour23')];
				}

				$production_data_arr[$val[csf('sewing_line')]]['prod_hour23']+=$val[csf('prod_hour23')];
				$production_data_arr[$val[csf('sewing_line')]]['prod_reso_allo']=$val[csf('prod_reso_allo')];

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
					$production_data_arr[$val[csf('sewing_line')]]['job_no'].=",".$val[csf('job_no')];
					$production_data_arr[$val[csf('sewing_line')]]['po_id'].=",".$val[csf('po_break_down_id')];
					$production_data_arr[$val[csf('sewing_line')]]['style'].=",".$val[csf('style_ref_no')];
					$production_data_arr[$val[csf('sewing_line')]]['file'].=",".$val[csf('file_no')];
					$production_data_arr[$val[csf('sewing_line')]]['ref'].=",".$val[csf('ref')];
				}
				else
				{
					$production_data_arr[$val[csf('sewing_line')]]['po_number']=$val[csf('po_number')];
					$production_data_arr[$val[csf('sewing_line')]]['job_no']=$val[csf('job_no')];
					$production_data_arr[$val[csf('sewing_line')]]['po_id']=$val[csf('po_break_down_id')];
					$production_data_arr[$val[csf('sewing_line')]]['style']=$val[csf('style_ref_no')];
					$production_data_arr[$val[csf('sewing_line')]]['file']=$val[csf('file_no')];
					$production_data_arr[$val[csf('sewing_line')]]['ref']=$val[csf('ref')];
				}
				$fob_rate_data_arr[$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['rate']=$val[csf('unit_price')];

				if($production_data_arr[$val[csf('sewing_line')]]['item_number_id']!="")
				{
					$production_data_arr[$val[csf('sewing_line')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('style_ref_no')]."**".$val[csf('buyer_name')];
				}
				else
				{
					$production_data_arr[$val[csf('sewing_line')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('style_ref_no')]."**".$val[csf('buyer_name')];
				}


				$production_data_arr[$val[csf('sewing_line')]] [$val[csf('po_break_down_id')]] ['fob']=$val[csf('order_rate')];
				//$production_data_arr[$val[csf('sewing_line')]] [$val[csf('style_ref_no')]] ['fob_rate']=$val[csf('order_rate')];
				$production_data_arr[$val[csf('sewing_line')]]['quantity']+=$val[csf('good_qnty')];


				$production_data_arr_qty[$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]['quantity']+=$val[csf('good_qnty')];

				if($all_po_id=="") $all_po_id=$val[csf('po_break_down_id')]; else $all_po_id.=",".$val[csf('po_break_down_id')];
				$style_wise_po_arr[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
				$job_wise_po_arr[$val[csf('job_no')]][$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
				$all_style_arr[$val[csf('style_ref_no')]]=$val[csf('style_ref_no')];
				$all_job_arr[$val[csf('job_no')]]="'".$val[csf('job_no')]."'";
				$po_id_arr[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];

				$lineStyleJobArray[$val[csf('sewing_line')]] [$val[csf('style_ref_no')]]    = $val[csf('job_no')];
			}
			else
			{
				//echo "'".$dbDate."' == ".$txt_date; exit();
				$summeryArray[$val[csf('production_date')]] ['total_production_qty'] += $val[csf('good_qnty')];
				$summeryArray[$val[csf('production_date')]] ['total_production_val'] += $val[csf('good_qnty')] * $val[csf('order_rate')];

			}


		}
		else
		{
			//production_type = 4
			$production_data_arr[$val[csf('sewing_line')]]['input_qnty']+=$val[csf('input_qnty')];
			$production_data_arr_qty[$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]['input_qnty']+=$val[csf('input_qnty')];
		}
		$poJobArray[$val[csf('po_break_down_id')]] = $val[csf('job_no')];
		$po_id_number_array[$val[csf('po_break_down_id')]]=$val[csf('po_number')];





	}


	krsort($summeryArray);
	//echo "<pre>"; print_r($summeryArray); exit();
	//SMV source config...
	if($db_type==0)
	{
		$manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 comp.id=$comapny_id","company_id");
	}
	else
	{
		$manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 comp.id=$comapny_id","company_id");
	}

	$poIds_cond2 = where_con_using_array($po_id_arr,0,"b.id");
	$smv_source=return_field_value("smv_source","variable_settings_production","company_name =$comapny_id and variable_list=25 and   status_active=1 and is_deleted=0"); //echo $smv_source; exit();
	if($smv_source=="" || $smv_source==0) {$smv_source = 1;}

	if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
	// echo $smv_source;die;
	if($smv_source==3) // from gsd enrty
	{
		$style_nos="'".implode("','",$all_style_arr)."'";
		$sql_item="SELECT a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID  from PPL_GSD_ENTRY_MST a where a.APPLICABLE_PERIOD <= $txt_date and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and A.STYLE_REF in($style_nos)
			group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID
			 ORDER BY a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC";
		//echo $sql_item; exit();
		$gsdSqlResult=sql_select($sql_item);


		foreach($gsdSqlResult as $rows)
		{
			foreach($style_wise_po_arr[$rows['STYLE_REF']] as $po_id)
			{
				if($item_smv_array[$po_id][$rows['GMTS_ITEM_ID']]==''){
					$item_smv_array[$po_id][$rows['GMTS_ITEM_ID']]=$rows['TOTAL_SMV'];
				}
			}
		}
	}
	else
	{
		$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost,c.smv_set from wo_po_details_master a,wo_po_break_down b, wo_po_details_mas_set_details c where a.id=b.job_id and b.job_id=c.job_id and a.company_name in($manufacturing_company) $poIds_cond2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
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


	$poIds_cond2 = str_replace("b.id","c.id",$poIds_cond2);
	//Run days
    $po_active_sql="SELECT a.floor_id,a.sewing_line,a.production_date,a.po_break_down_id,a.item_number_id, b.style_ref_no from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a where b.id=c.job_id and c.id=a.po_break_down_id and  a.production_type IN(5) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $company_name $location $floor $line $buyer_id_cond   $file_cond $ref_cond $poIds_cond2 group by  a.floor_id,a.sewing_line,a.production_date ,a.po_break_down_id,a.item_number_id, b.style_ref_no";
    // echo $po_active_sql;die;
	foreach(sql_select($po_active_sql) as $vals)
	{
		$prod_dates=$vals[csf('production_date')];
		if($duplicate_date_arr[$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]][$prod_dates]=="")
		{
			$active_days_arr[$vals[csf('sewing_line')]] [$vals[csf('style_ref_no')]] [$vals[csf('item_number_id')]] +=1;
			$active_days_arr_powise[$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]]+=1;
			$duplicate_date_arr[$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]][$prod_dates]=$prod_dates;
		}

	}

	//WIP Run+Next coloum
    $wip_sql="SELECT a.floor_id,a.sewing_line, a.production_type, b.production_qnty, a.po_break_down_id, a.item_number_id, a.production_date

	FROM  pro_garments_production_mst a ,pro_garments_production_dtls b
	WHERE a.id=b.mst_id and a.po_break_down_id in($all_po_id)
	and a.production_type in (4,5) and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.serving_company = $comapny_id";
	//echo $wip_sql; die;
	$wip_results = sql_select($wip_sql);
	$wipArray = array();
	$wipArrayStyle = array();
	foreach($wip_results as $wip){
		//$wipArray[$wip[csf('sewing_line')]] [$wip[csf('po_break_down_id')]] [$wip[csf('item_number_id')]] [$wip[csf('production_type')]]+=$wip[csf('production_qnty')];

		$wipArrayStyle[$wip[csf('sewing_line')]] [$poJobArray[$wip[csf('po_break_down_id')]]] [$wip[csf('po_break_down_id')]] [$wip[csf('item_number_id')]] [$wip[csf('production_type')]]+=$wip[csf('production_qnty')];

	}
	/* $dbDate = date("d-M-Y", strtotime($val[csf('production_date')]));
			if("'".$dbDate."'" == $txt_date){ */
	//echo "<pre>"; print_r($wipArrayStyle); exit();
	$table_width = 2100 + (count($line_start_hour_arr) * 50);
	ob_start();
	?>
		<div style="margin-top: 20px;">
			<table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="<?=$table_width;?>">
				<caption style="padding: 30px; background-color: #8DAFDA; color: #0172BE;">
					<p><strong>Hourly Production Monitoring Reports</strong></p>
					<p><strong><?=$companyArr[$comapny_id]; ?></strong></p>
					<p><strong><?=trim($txt_date, "'");?></strong></p>
				</caption>
				<thead>
					<tr>
						<th rowspan="2" width="100">Line Number</th>
						<th rowspan="2" width="100">Line cheaf</th>
						<th rowspan="2" width="100">Buyer</th>
						<th rowspan="2" width="100">Style No.</th>
						<th rowspan="2" width="100">Article No.</th>

						<th rowspan="2" width="100">Items</th>
						<th rowspan="2" width="100">Run days</th>
						<th rowspan="2" width="100">SMV</th>

						<th rowspan="2" width="100">OP:</th>
						<th rowspan="2" width="100">HP:</th>
						<th rowspan="2" width="100">FOB($)</th>
						<th rowspan="2" width="100">Hourly Target Value</th>
						<th rowspan="2" width="100">Day Target Value</th>
						<th rowspan="2" width="100">Achivement Value</th>
						<th rowspan="2" width="100">Hourly Target</th>

						<th colspan="2" width="200">WIP</th>

						<?php foreach($line_start_hour_arr as $val) { ?>
							<th rowspan="2" width="50" style="transform: rotate(-90deg);"><?=$val;?></th>
						<?php } ?>

						<th rowspan="2" width="100">Total Production</th>
						<th rowspan="2" width="100">Day Target</th>
						<th rowspan="2" width="100">Total IE Target Difference</th>
						<th rowspan="2" width="100">Remarks</th>
					</tr>

					<tr>
						<th width="100">Run</th>
						<th width="100">Next</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$total_smv = 0; $smv_count = 0;
						$total_op = 0;
						$total_hp = 0;
						$total_fob = 0; $fob_count = 0;
						$total_hourly_target_value = 0;
						$total_day_target_value = 0;
						$total_achievement_value = 0;
						$total_hourly_target = 0;
						$total_run = 0;

						$total_hourly_production_array = array();

						$total_production = 0;
						$total_day_target = 0;
						$total_ie_difference = 0;

						$i=1;
						/* foreach($prod_resource_array as $floor => $lineCheafArray)
						{ */
							// ksort($prod_resource_array);
							foreach($prod_resource_array as $line_cheaf => $sl_id)
							{
								// foreach($sl_v as $line_cheaf => $sl_id)
								// {
									$line_cheap_total_smv = 0; $line_cheap_smv_count = 0;
									$line_cheap_total_op = 0;
									$line_cheap_total_hp = 0;
									$line_cheap_total_fob = 0; $line_cheap_fob_count = 0;
									$line_cheap_total_hourly_target_value = 0;
									$line_cheap_total_day_target_value = 0;
									$line_cheap_total_achievement_value = 0;
									$line_cheap_total_hourly_target = 0;
									$line_cheap_total_run = 0;

									$line_cheap_total_hourly_production_array = array();

									$line_cheap_total_production = 0;
									$line_cheap_total_day_target = 0;
									$line_cheap_total_ie_difference = 0;
									ksort($sl_id);
									foreach($sl_id as $sl_val => $lineNumArr)
									{

										// ksort($lineNumArr);
										foreach($lineNumArr as $line_number => $lineIdsArray)
										{
											foreach($lineIdsArray as $lineID => $prodDateArr)
											{
												// $smv_count++; // line count
												$hours_worked = 0;
												$hours_worked_day_target = 0;
												foreach($prodDateArr as $value)
												{
													if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

													//SMV Calculation
													$poItemArray =  array_unique(explode("****", $production_data_arr[$lineID]['item_number_id']));
													$k=0;
													//echo "<pre>"; print_r($poItemArray); die();

													foreach ($poItemArray as $key => $poItems)
													{
														$poItem = explode("**", $poItems);
														$smv = $item_smv_array[$poItem[0]][$poItem[1]];

														//Quantified
														$total_hourly_prod = 0;
														$hourly_target_value = $value['hourly_target'] * $production_data_arr[$lineID][$poItem[0]]['fob'];
														foreach($line_start_hour_arr as $val){
															$pro_hour = "prod_hour".substr($val, 0, 2);
															$hourly_prod_qty = $production_data_arr[$lineID][$poItem[0]][$poItem[1]][$pro_hour];
															$total_hourly_prod += $hourly_prod_qty;

															if(!empty($hourly_prod_qty)){
																$hours_worked_day_target++;
															}

														}

														$day_target_value = $hours_worked_day_target * $hourly_target_value;
														$achievement_value = $total_hourly_prod * $production_data_arr[$lineID][$poItem[0]]['fob'];
														//$day_target = $value['hourly_target'] * $hours_worked;
														$day_target = $value['hourly_target'] * $value['working_hour'];
														$ie_difference = $total_hourly_prod - $day_target;
														//Quantified
														//$poItem [PO,item,style,buyer ]
														//wipArrayStyle[line, style, po, item,prod_type]
														$run = $wipArrayStyle[$lineID] [$poJobArray[$poItem[0]]] [$poItem[0]] [$poItem[1]] [4] - $wipArrayStyle[$lineID] [$poJobArray[$poItem[0]]] [$poItem[0]] [$poItem[1]] [5];


														//Total value lineStyleJobArray
														$total_smv += $smv;
														$smv_count++;
														if($k==0){
															$total_op += $value['operator'];
															$total_hp += $value['helper'];
															$line_cheap_total_op += $value['operator'];
															$line_cheap_total_hp += $value['helper'];
														}

														$total_fob += $production_data_arr[$lineID][$poItem[0]]['fob'];
														$fob_count++;
														$total_hourly_target_value += $hourly_target_value;
														$total_day_target_value += $day_target_value;
														$total_achievement_value += $achievement_value;

														$total_run += $run;

														$total_production += $total_hourly_prod;


														//line_cheap wise subtotal.
														$line_cheap_total_smv += $smv;
														$line_cheap_smv_count++;

														$line_cheap_total_fob += $production_data_arr[$lineID][$poItem[0]]['fob'];
														$line_cheap_fob_count++;
														$line_cheap_total_hourly_target_value += $hourly_target_value;
														$line_cheap_total_day_target_value += $day_target_value;
														$line_cheap_total_achievement_value += $achievement_value;

														$line_cheap_total_run += $run;

														//$line_cheap_total_hourly_production_array = array();

														$line_cheap_total_production += $total_hourly_prod;
														$article_arr = array();
														$article_arr = $line_wise_article_arr[$lineID][$poItem[0]][$poItem[1]];
														$article 	 = implode(',',$article_arr);

														?>
															<tr bgcolor="<?=$bgcolor;?>" onclick="change_color('tr_<?=$i?>','<?=$bgcolor?>')" id="tr_<?=$i?>">
																<? if($k==0){?>
																	<td align="left" width="100" title="<? echo $sl_val?>" rowspan="<?=count($poItemArray);?>"><?=$line_number;?></td>
																	<td align="left" width="100" rowspan="<?=count($poItemArray);?>"><?=$line_cheaf;?></td>
																<?}?>
																<td align="left" width="100"><?=$buyerArr[$poItem[3]];?></td>
																<td align="left" width="100"><?=$poItem[2];?></td>

																<td align="left" width="100"><?=$article;?></td>
																<td align="left" width="100" title="<?='PO: '.$po_id_number_array[$poItem[0]];?>"><?=$garments_item[$poItem[1]];?></td>
																<td align="right" width="100"><?=$active_days_arr[$lineID][$poItem[2]][$poItem[1]];?></td>

																<td align="right" width="100"><?=number_format($smv,2);?></td>
																<? if($k==0){?>
																	<td align="right" width="100" rowspan="<?=count($poItemArray);?>" ><?=$value['operator'];?></td>
																	<td align="right" width="100" rowspan="<?=count($poItemArray);?>"><?=$value['helper'];?></td>
																<?}?>

																<td align="right" width="100"><?=number_format($production_data_arr[$lineID][$poItem[0]]['fob'],4);?></td>
																<td align="right" width="100"><?=number_format($hourly_target_value,2);?></td>
																<td align="right" width="100"><?=number_format($day_target_value,2);?></td>
																<td align="right" width="100"><?=number_format($achievement_value,2);?></td>
																<? if($k==0)
																{
																	$line_cheap_total_hourly_target += $value['hourly_target'];
																	$total_hourly_target += $value['hourly_target'];
																	?>
																	<td align="right" width="100" rowspan="<?=count($poItemArray);?>"><?=$value['hourly_target'];?></td>
																	<?
																}
																?>

																<td align="right" width="100" title="<?='Input: '.$wipArrayStyle[$lineID] [$poJobArray[$poItem[0]]] [$poItem[0]] [$poItem[1]] [4].' Output: '.$wipArrayStyle[$lineID] [$poJobArray[$poItem[0]]] [$poItem[0]] [$poItem[1]] [5];?>"><?=$run;?></td>
																<td align="right" width="100"></td>

																<?php

																	foreach($line_start_hour_arr as $val)
																	{
																		$column = "prod_hour".substr($val, 0, 2);
																		//$hourly_prod = $production_data_arr[$lineID][$column];
																		$hourly_prod = $production_data_arr[$lineID][$poItem[0]][$poItem[1]][$column];
																		//$poItem[0]][$poItem[1]]
																		$total_hourly_production_array[$column] += $hourly_prod;
																		$line_cheap_total_hourly_production_array[$line_cheaf][$column] += $hourly_prod;
																		if(!empty($hourly_prod)){
																			$hours_worked++;
																		}
																		?>
																		<td align="right" width="50"><?=$hourly_prod;?></td>
																		<?php
																	}
																	//$day_target = $value['hourly_target'] * $hours_worked;

																	$ie_difference = $production_data_arr[$lineID]['quantity'] - $day_target;





																	$hours_worked=0;
																?>

																<td align="right" width="100"><?=$total_hourly_prod;?></td>
																<? if($k==0){
																	$line_cheap_total_ie_difference += $ie_difference;
																	$total_ie_difference += $ie_difference;
																	$total_day_target += $day_target;
																	$line_cheap_total_day_target += $day_target;
																	?>
																	<td align="right" width="100" rowspan="<?=count($poItemArray);?>" ><?=$day_target;?></td>

																	<td align="right" width="100" rowspan="<?=count($poItemArray);?>" ><?=$ie_difference;?></td>
																<?}?>
																<td align="right" width="100"></td>
															</tr>
														<?php
														$i++;
														$k++;
														$total_hourly_prod=0;
														$hours_worked_day_target=0;
													}


												}
											}


										}
									}
									?>
										<tr style="background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #F0F0F0), color-stop(100%, #DBDBDB)); font-weight: bold; text-align: right;">
											<td colspan="7" width="600" align="center">Sub-Total</td>

											<td width="100"><?=round($line_cheap_total_smv/$line_cheap_smv_count, 2);?></td>

											<td width="100"><?=$line_cheap_total_op;?></td>
											<td width="100"><?=$line_cheap_total_hp;?></td>
											<td width="100" title="<?= "Total Line FOB($line_cheap_total_fob) / Num Of Line Row($line_cheap_fob_count)" ?> "><?=number_format($line_cheap_total_fob/$line_cheap_fob_count, 4);?></td>
											<td width="100"><?=number_format($line_cheap_total_hourly_target_value,2);?></td>
											<td width="100"><?=number_format($line_cheap_total_day_target_value,2);?></td>
											<td width="100"><?=number_format($line_cheap_total_achievement_value,2);?></td>
											<td width="100"><?=number_format($line_cheap_total_hourly_target,2);?></td>

											<td width="100"><?=$line_cheap_total_run;?></td>
											<td width="100"></td>

											<?php

												foreach($line_start_hour_arr as $val)
												{
													$column = "prod_hour".substr($val, 0, 2);
													//$hourly_prod = $production_data_arr[$lineID][$column];
													?>
													<td width="50"><?=$line_cheap_total_hourly_production_array[$line_cheaf][$column];?></td>
													<?php
												}
											?>

											<td width="100"><?=$line_cheap_total_production;?></td>
											<td width="100"><?=$line_cheap_total_day_target;?></td>
											<td width="100"><?=$line_cheap_total_ie_difference;?></td>
											<td width="100"></td>
										</tr>
									<?php

							    // }


							}


						//}

					?>
				</tbody>
				<tfoot style="font-weight: bold;">
					<tr style="background: gray;">
						<th colspan="7" width="600">Total</th>

						<th width="100"><?=round($total_smv/$smv_count, 2);?></th>

						<th width="100"><?=$total_op;?></th>
						<th width="100"><?=$total_hp;?></th>
						<th width="100" title="<?= "Total FOB($total_fob) / Num Of Row($fob_count)" ?>" ><?=round($total_fob/$fob_count, 4);?></th>
						<th width="100"><?=number_format($total_hourly_target_value,2);?></th>
						<th width="100"><?=number_format($total_day_target_value,2);?></th>
						<th width="100"><?=number_format($total_achievement_value,2);?></th>
						<th width="100"><?=number_format($total_hourly_target,2);?></th>

						<th width="100"><?=$total_run;?></th>
						<th width="100"></th>

						<?php

							foreach($line_start_hour_arr as $val)
							{
								$column = "prod_hour".substr($val, 0, 2);
								//$hourly_prod = $production_data_arr[$lineID][$column];
								?>
								<th width="50"><?=$total_hourly_production_array[$column];?></th>
								<?php
							}
						?>

						<th width="100"><?=$total_production;?></th>
						<th width="100"><?=$total_day_target;?></th>
						<th width="100"><?=$total_ie_difference;?></th>
						<th width="100"></th>
					</tr>
				</tfoot>
			</table>

			<table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="420">
				<caption style="text-align:center; padding: 20px; background-color: #8DAFDA;"><h3>Previous Days History</h3></caption>
				<thead>
					<tr>
						<th width="100">Date</th>
						<th width="100"></th>
						<th width="100">Total Production</th>
						<th width="100">Production Value</th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach($summeryArray as $date => $summery)
						{
							?>
								<tr>
									<td width="100"><?=$date;?></td>
									<td width="100"></td>
									<td width="100" align="right"><?=$summery['total_production_qty'];?></td>
									<td width="100" align="right"><?=number_format($summery['total_production_val'],2);?></td>
								</tr>
							<?
						}
					?>
				</tbody>
			</table>
		</div>

	<?php
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

if($action=="report_generate6") // Button Start...
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
            .break_all
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
	$floorSeqArr = return_library_array("select id,floor_serial_no from lib_prod_floor","id","floor_serial_no");
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$comapny_id=str_replace("'","",$cbo_company_id);
    $today_date=date("Y-m-d");
	$txt_producting_day="".str_replace("'","",$txt_date)."";

	//***************************************************************************************************************************
	$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
	order by sewing_line_serial");
	foreach($lineDataArr as $lRow)
	{
		$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
		$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
		$lastSlNo=$lRow[csf('sewing_line_serial')];
	}

	if($db_type==0)
	{
		$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$comapny_id and shift_id=1 and pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
	}
	else
	{
		$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$comapny_id and shift_id=1 and pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
	}

	if($min_shif_start=="")
	{
		echo "<p style='font-size:20px', align='center'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<p/>";die;
	}


	//==============================shift time===================================================================================================
	$start_time_arr=array();
	if($db_type==0)
	{
		$start_time_data_arr=sql_select("select company_name, shift_id, TIME_FORMAT( prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( lunch_start_time, '%H:%i' ) as lunch_start_time from variable_settings_production where company_name in($comapny_id) and shift_id=1  and variable_list=26 and status_active=1 and is_deleted=0");
	}
	else
	{
		$start_time_data_arr=sql_select("select company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time,TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($comapny_id) and  shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");
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
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_id and variable_list=23 and is_deleted=0 and status_active=1");

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
	$cbo_no_prod_type=str_replace("'","",$cbo_no_prod_type);
	$file_no=str_replace("'","",$txt_file_no);
	$ref_no=str_replace("'","",$txt_ref_no);
	if($file_no!="") $file_cond="and c.file_no=$file_no";else $file_cond="";
	if($ref_no!="") $ref_cond="and c.grouping='$ref_no'";else $ref_cond="";
	//echo $file_cond;

	if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date=$txt_date";
	// echo $txt_date_from; die;

	if($prod_reso_allo[0]==1)
	{
		$prod_resource_array=array();

		$dataArray_sql=sql_select("select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id=$comapny_id and b.pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0");
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
		//var_dump($prod_resource_array);
		if(str_replace("'","",trim($txt_date))==""){$pr_date_con="";}else{$pr_date_con=" and b.pr_date=$txt_date";}

		if($db_type==0)
		{
			$dataArray=sql_select("select a.id,b.pr_date,d.shift_id,TIME_FORMAT( d.prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( d.lunch_start_time, '%H:%i' ) as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 $pr_date_con");
		}
		else
		{
			$dataArray=sql_select("select a.id,b.pr_date,d.shift_id,TO_CHAR(d.prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR( d.lunch_start_time,'HH24:MI') as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 $pr_date_con");
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
		$manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
	}
	else
	{
		$manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
	}


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

   	//$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($manufacturing_company) and variable_list=25 and   status_active=1 and is_deleted=0");



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
		b.buyer_name  as buyer_name,b.style_ref_no,b.job_no,a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.unit_price,c.file_no,c.grouping as ref,sum(a.production_quantity) as good_qnty,";
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
		$sql.="sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23 from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a left join lib_prod_floor d on a.floor_id=d.id and d.is_deleted=0 left join lib_sewing_line e on a.sewing_line=e.id and e.is_deleted=0  where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $company_name $location $floor $line $buyer_id_cond  $txt_date_from $file_cond $ref_cond group by b.job_no,a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,e.sewing_line_serial,b.buyer_name,a.item_number_id,c.po_number,c.file_no,c.unit_price,c.grouping,b.style_ref_no order by a.location, a.floor_id,e.sewing_line_serial,a.prod_reso_allo";
	}
	else if($db_type==2)
	{
		$sql="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name  as buyer_name,b.style_ref_no,b.job_no, a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.file_no,c.unit_price,c.grouping as ref,TO_CHAR(a.production_hour,'HH24') as total_hour,sum(d.production_qnty) as good_qnty,";
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
		FROM  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a ,pro_garments_production_dtls d
		WHERE a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_name $location $floor $line $buyer_id_cond  $txt_date_from $file_cond $ref_cond
		GROUP BY b.job_no, a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name,b.style_ref_no,a.item_number_id,c.po_number,c.unit_price,c.file_no,c.grouping,a.production_hour
		ORDER BY a.location,a.floor_id,a.sewing_line";
	}
	//  echo $sql;die;
	$sql_resqlt=sql_select($sql);
	$production_data_arr=array();
	$production_po_data_arr=array();
	$production_serial_arr=array(); $reso_line_ids=''; $all_po_id="";
	$active_days_arr=array();
	$duplicate_date_arr=array();
	$style_wise_po_arr=array();
	$job_wise_po_arr=array();
	$production_hour_arr=array();
	$production_floor_arr=array();
	$hour_arr=array();
	$all_style_arr=array();
	$all_job_arr=array();
	foreach($sql_resqlt as $val)
	{
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
		$production_serial_arr[$floorSeqArr[$val[csf('floor_id')]]][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]]=$val[csf('sewing_line')];
	
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
					$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]+=$val[csf($prod_hour)];
				}
			}

			if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date))
			{
				$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]+=$val[csf($prod_hour)];
			}
		}

		if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
		{
			if( $h>=$line_start_hour && $h<=$actual_time)
			{
				$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]+=$val[csf('prod_hour23')];
			}
		}
		else
		{
			$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]+=$val[csf('prod_hour23')];
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
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['job_no'].=",".$val[csf('job_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_id'].=",".$val[csf('po_break_down_id')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style'].=",".$val[csf('style_ref_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['file'].=",".$val[csf('file_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['ref'].=",".$val[csf('ref')];
		}
	 	else
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']=$val[csf('po_number')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['job_no']=$val[csf('job_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_id']=$val[csf('po_break_down_id')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['style']=$val[csf('style_ref_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['file']=$val[csf('file_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['ref']=$val[csf('ref')];
		}
		$fob_rate_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['rate']=$val[csf('unit_price')];

		if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']!="")
		{
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')];
		}
		else
		{
			 $production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')];
		}
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['quantity']+=$val[csf('good_qnty')];
		$production_data_arr_qty[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]['quantity']+=$val[csf('good_qnty')];

		if($all_po_id=="") $all_po_id=$val[csf('po_break_down_id')]; else $all_po_id.=",".$val[csf('po_break_down_id')];
		$style_wise_po_arr[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
		$job_wise_po_arr[$val[csf('job_no')]][$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
		$all_style_arr[$val[csf('style_ref_no')]]=$val[csf('style_ref_no')];
		$production_hour_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('total_hour')]]=$val[csf('total_hour')];
		// $production_floor_arr[$val[csf('floor_id')]][$val[csf('total_hour')]]=$val[csf('total_hour')];
		$hour_arr['total_hour']=$val[csf('total_hour')];
		$all_job_arr[$val[csf('job_no')]]="'".$val[csf('job_no')]."'";
	}
	//  print_r($hour_arr);
	foreach ($production_hour_arr as $fkey => $fdata) 
	{
		foreach($fdata as $sew_line=>$sew_data)
		{
			foreach($sew_data as $tot_hour=>$val)
			{
				$production_floor_arr[$fkey]+=$val[csf('total_hour')];

			}
		}
		
	}
	// print_r($production_floor_arr);
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
			$poIdsArr=array_chunk(explode(",",$po_numIds),990);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$poIds_cond.=" b.id  in($ids) or ";
				$poIds_cond2.=" c.id  in($ids) or ";
			}
			$poIds_cond=chop($poIds_cond,'or ');
			$poIds_cond2=chop($poIds_cond,'or ');
			$poIds_cond.=")";
			$poIds_cond2.=")";
		}
		else
		{
			$poIds_cond=" and  b.id  in($all_po_id)";
			$poIds_cond2=" and  c.id  in($all_po_id)";
		}
	}

	if($smvsourcefor_charka==1)//ISD-23-06056
	{
		$jovCondForsmvWs=where_con_using_array($all_job_arr,0,"a.PO_JOB_NO");

		$sql_item="SELECT a.id, a.PO_JOB_NO, A.TOTAL_SMV, A.STYLE_REF, a.GMTS_ITEM_ID from PPL_GSD_ENTRY_MST a where a.BULLETIN_TYPE=4 and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 $jovCondForsmvWs
				group by a.id, a.PO_JOB_NO, A.TOTAL_SMV, A.STYLE_REF, a.GMTS_ITEM_ID ORDER BY a.GMTS_ITEM_ID, a.id DESC";
		//echo $sql_item;
		$gsdSqlResult=sql_select($sql_item);
		foreach($gsdSqlResult as $rows)
		{
			foreach($job_wise_po_arr[$rows['PO_JOB_NO']] as $po_id)
			{
				if($item_smv_array[$po_id][$rows['GMTS_ITEM_ID']]==''){
					$item_smv_array[$po_id][$rows['GMTS_ITEM_ID']]=$rows[TOTAL_SMV];
				}
			}
		}

		$jovCondForsmvPo=where_con_using_array($all_job_arr,0,"a.job_no");
		$sql_item="SELECT b.id, c.gmts_item_id, c.smv_pcs, c.smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where a.id=b.job_id and a.id=c.job_id and b.job_id=c.job_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $jovCondForsmvPo";
		$resultItem=sql_select($sql_item);

		foreach($resultItem as $itemData)
		{
			if($item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=='')
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
			}
		}
	}
	else
	{
		$smv_source=return_field_value("smv_source","variable_settings_production","company_name =$comapny_id and variable_list=25 and   status_active=1 and is_deleted=0");
		if($smv_source=="" || $smv_source==0) {$smv_source = 1;}

		// echo "SELECT smv_source from variable_settings_production where company_name =$comapny_id and variable_list=25 and   status_active=1 and is_deleted=0";

		if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
		// echo $smv_source;die;
		if($smv_source==3) // from gsd enrty
		{
			$style_nos="'".implode("','",$all_style_arr)."'";
			$sql_item="SELECT a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID  from PPL_GSD_ENTRY_MST a where  A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and A.STYLE_REF in($style_nos)
				group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID
				 ORDER BY a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC";//a.APPLICABLE_PERIOD <= $txt_date and
			$gsdSqlResult=sql_select($sql_item);

			/*foreach($resultItem as $itemData)
			{
				// $item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('total_smv')];
			}*/

			foreach($gsdSqlResult as $rows)
			{
				foreach($style_wise_po_arr[$rows['STYLE_REF']] as $po_id)
				{
					if($item_smv_array[$po_id][$rows['GMTS_ITEM_ID']]==''){
						$item_smv_array[$po_id][$rows['GMTS_ITEM_ID']]=$rows[TOTAL_SMV];
					}
				}
			}
		}
		else
		{
			$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost,c.smv_set from wo_po_details_master a,wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.company_name in($manufacturing_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
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
	}
	// echo $sql_item;
	// echo "<pre>";print_r($item_smv_array);


    $po_active_sql="SELECT a.floor_id,a.sewing_line,a.production_date,a.po_break_down_id,a.item_number_id from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $company_name $location $floor $line $buyer_id_cond   $file_cond $ref_cond $poIds_cond2 group by  a.floor_id,a.sewing_line,a.production_date ,a.po_break_down_id,a.item_number_id";
    //echo $po_active_sql;die;
	foreach(sql_select($po_active_sql) as $vals)
	{
		$prod_dates=$vals[csf('production_date')];
		if($duplicate_date_arr[$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]][$prod_dates]=="")
		{
			$active_days_arr[$vals[csf('floor_id')]][$vals[csf('sewing_line')]]+=1;
			$active_days_arr_powise[$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]]+=1;
			$duplicate_date_arr[$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]][$prod_dates]=$prod_dates;
		}

	}
	//print_r($duplicate_date_arr);

	$sql_item_rate="select b.id, c.item_number_id, c.order_quantity, c.order_total from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c where b.job_no_mst=a.job_no and b.id=c.po_break_down_id and b.job_no_mst=c.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1  $file_cond $ref_cond  $poIds_cond";
	$resultRate=sql_select($sql_item_rate);
	$item_po_array=array();
	foreach($resultRate as $row)
	{
		$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['qty']+=$row[csf('order_quantity')];
		$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['amt']+=$row[csf('order_total')];
	}

	// subcoutact data **********************************************************************************************************************

    if($db_type==0)
    {
		$sql_sub_contuct= "select  a.company_id, a.location_id,d.floor_serial_no,e.sewing_line_serial,a.prod_reso_allo, a.floor_id, a.production_date, a.line_id,b.party_id  as buyer_name,a.order_id,c.order_no as po_number,c.cust_style_ref,b.subcon_job as job_no, max(c.smv) as smv,sum(a.production_qnty) as good_qnty,";

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
   		$sql_sub_contuct.="sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.id and d.is_deleted=0 left join lib_sewing_line e on a.line_id=e.id and e.is_deleted=0 where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id=$comapny_id $subcon_location $floor $subcon_line   $txt_date_from group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date,a.prod_reso_allo,a.line_id,b.party_id,c.order_no,c.cust_style_ref,b.subcon_job ,e.sewing_line_serial order by a.location_id,d.floor_serial_no,e.sewing_line_serial,a.prod_reso_allo";
	}
	else
	{
		$sql_sub_contuct= "select  a.company_id, a.location_id, a.floor_id,d.floor_serial_no,e.sewing_line_serial, a.production_date, a.line_id,b.party_id  as buyer_name,a.prod_reso_allo,a.order_id,a.GMTS_ITEM_ID,c.order_no as po_number,c.cust_style_ref, b.subcon_job as job_no,  max(c.smv) as smv,a.prod_reso_allo,sum(a.production_qnty) as good_qnty,";
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

	   	$sql_sub_contuct.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]'	and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.id  and d.is_deleted=0 left join lib_sewing_line e on a.line_id=e.id and e.is_deleted=0 where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id=$comapny_id $subcon_location $floor $subcon_line   $txt_date_from group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id,a.GMTS_ITEM_ID, a.production_date,  b.subcon_job,a.line_id,b.party_id,c.order_no,c.cust_style_ref,e.sewing_line_serial,a.prod_reso_allo order by a.location_id, a.floor_id,e.sewing_line_serial,a.prod_reso_allo";

	}
	//echo $sql_sub_contuct;die;
	$sub_result=sql_select($sql_sub_contuct);
	$subcon_order_smv=array();
	foreach($sub_result as $subcon_val)
	{

		if($val[csf('prod_reso_allo')]==1)
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

		$production_po_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]][$subcon_val[csf('gmts_item_id')]]+=$subcon_val[csf('good_qnty')];
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
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['job_no'].=",".$subcon_val[csf('job_no')];
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['style'].=",".$subcon_val[csf('cust_style_ref')];
		}
		else
		{
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number']=$subcon_val[csf('po_number')];
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['job_no']=$subcon_val[csf('job_no')];
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['style']=$subcon_val[csf('cust_style_ref')];
		}

		if($production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id']!="")
		{
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id'].=",".$subcon_val[csf('order_id')]."**".$subcon_val[csf('gmts_item_id')];
		}
		else
		{
			$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id'].=$subcon_val[csf('order_id')]."**".$subcon_val[csf('gmts_item_id')];
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
				 	$production_po_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]][$subcon_val[csf('gmts_item_id')]]+=$val[csf($prod_hour)];
				 }
			}
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
			{
				$production_po_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]][$subcon_val[csf('gmts_item_id')]]+=$val[csf($prod_hour)];
			}
		 }
		if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
		{
			if( $h>=$line_start_hour && $h<=$actual_time)
			{
				$production_po_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]][$subcon_val[csf('gmts_item_id')]]+=$val[csf('prod_hour23')];
			}
		}
		else
		{
			$production_po_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]][$subcon_val[csf('gmts_item_id')]]+=$val[csf('prod_hour23')];
		}
		$production_data_arr[$val[csf('floor_id')]][$val[csf('line_id')]]['prod_hour23']+=$val[csf('prod_hour23')];

		$style_wise_po_arr[$val[csf('cust_style_ref')]][$val[csf('order_id')]] = $val[csf('order_id')];
	}
	//For Summary Report New Add No Prodcut
	if($cbo_no_prod_type==1)
	{
		//No Production line Start ....
		$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
		$sql_active_line=sql_select("select sewing_line,sum(production_quantity) as total_production from pro_garments_production_mst  where production_date=".$txt_date." and production_type=5 and status_active=1 and is_deleted=0 and prod_reso_allo=1 group by  sewing_line");
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
		$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$comapny_id and variable_list=23 and is_deleted=0 and status_active=1");
		$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
		if(str_replace("'","",$cbo_location_id)==0) $location_cond=""; else $location=" and a.location_id=".str_replace("'","",$cbo_location_id)."";

		if(str_replace("'","",$cbo_floor_id)==0) $floor=""; else $floor="and a.floor_id=".str_replace("'","",$cbo_floor_id)."";
		$lin_ids=str_replace("'","",$hidden_line_id);
		$res_line_cond=rtrim($reso_line_ids,",");

			 $dataArray_sum=sql_select("select a.id, a.floor_id,1 as prod_reso_allo,2 as type_line, a.line_number as line_no, b.man_power, b.operator, b.helper, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type,d.prod_start_time from  prod_resource_dtls b,prod_resource_dtls_time d,prod_resource_mst a  where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and b.pr_date=".$txt_date." and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 and a.id not in($res_line_cond) and a.line_number like '%$lin_ids%'  $location  $floor  group by a.id, a.floor_id, a.line_number, d.prod_start_time,b.man_power, b.operator, b.helper, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type order by a.floor_id");
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

				 $production_serial_arr[$floorSeqArr[$row[csf('floor_id')]]][$row[csf('floor_id')]][$slNo][$row[csf('id')]]=$row[csf('id')];
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
			 $dataArray_sql_cap=sql_select("select  a.floor_id, a.line_number as line_no,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id  and a.company_id=$comapny_id and b.pr_date=".$txt_date."  and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  group by a.id,a.line_number, a.floor_id, a.line_number, b.man_power, b.operator,c.capacity, b.helper, b.working_hour,b.target_per_hour order by a.floor_id");

			 //$prod_resource_array_summary=array();
			 foreach( $dataArray_sql_cap as $row)
			 {
				 $production_data_arr[$row[csf('floor_id')]][$row[csf('line_no')]]['capacity']=$row[csf('capacity')];
			 }

	} //End

	//echo "<pre>";
	//var_dump($production_serial_arr);die;
    $avable_min=0;
	$today_product=0;
    $floor_name="";
    $floor_man_power=0;
	$floor_operator=$floor_produc_min=0;
	$floor_smv=$floor_row=$floor_helper=$floor_tgt_h=$floor_days_run=$floor_working_hour=$line_floor_production=$floor_today_product=$floor_avale_minute=0;
	$total_operator=$total_helper=$gnd_hit_rate=0;
    $total_smv=$total_terget=$grand_total_product=$gnd_line_effi=0;
	
    $total_man_power=$gnd_avable_min=$gnd_product_min=0;$gnd_achivement_for_details=0;
	$item_smv=$item_smv_total=$line_efficiency=$days_run=$days_active=$total_working_hour=$gnd_total_tgt_h=$total_capacity=0;
	$j=1;
	ob_start();
	$line_number_check_arr=array();
	$smv_for_item="";
	$total_production=array();
	$floor_production=array();
    $line_floor_production=0;
    $line_total_production=0; $gnd_total_fob_val=0; $gnd_final_total_fob_val=0; 
	ksort($production_serial_arr);
	foreach($production_serial_arr as $f_sl=>$fsl_data)
	{
		foreach($fsl_data as $f_id=>$fname)
		{
			ksort($fname);
			foreach($fname as $sl=>$s_data)
			{
              
				foreach($s_data as $l_id=>$ldata)
				{
				$po_value=$production_data_arr[$f_id][$ldata]['po_number'];
				if($po_value)
				{

					//}

					if($i!=1)
					{
						if(!in_array($f_id, $check_arr))
						{
							if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$html.='<tr  bgcolor="#B6B6B6">
								<td class="break_all" width="40">&nbsp;</td>
								<td class="break_all" width="80">&nbsp;</td>
								<td class="break_all" width="80">&nbsp;</td>
								<td class="break_all" width="80">&nbsp;</td>
								<td class="break_all" width="100">&nbsp;</td>
								<td class="break_all" width="140">&nbsp;</td>
								<td class="break_all" width="100">&nbsp;</td>
								<td class="break_all" width="80">&nbsp;</td>
								<td class="break_all" width="80">&nbsp;</td>
								<td class="break_all" width="80">&nbsp;</td>
								<td class="break_all" align="right" width="60">&nbsp;</td>
								<td class="break_all" align="right" width="70">'.number_format($floor_operator,0).'</td>
								<td class="break_all" align="right" width="50">'. number_format($floor_helper,0).'</td>
								<td class="break_all" align="right" width="60">'. number_format($floor_man_power,0).'</td>
								<td class="break_all" align="right" width="70">'. number_format($floor_tgt_h,0).'</td>
								<td class="break_all" align="right" width="60">'. number_format($floor_days_run,0).'</td>
								<td class="break_all" align="right" width="60">'. number_format($floor_days_active,0).'</td>
								<td class="break_all" align="right" width="70">'.number_format($floor_capacity,0).'</td>
								<td class="break_all" align="right" width="60">'. number_format($floor_working_hour,0).'</td>
								<td class="break_all" align="right" width="60">&nbsp;</td>
								<td class="break_all" align="right" width="60">&nbsp;</td>
								<td class="break_all" align="right" width="80">'.number_format($eff_target_floor,0).'</td>
								<td class="break_all" align="right" width="80">'.number_format($line_floor_production,0).'</td>
								<td class="break_all" align="right" width="80">&nbsp;</td>
								<td class="break_all" align="right" width="80">'. number_format(($line_floor_production-$eff_target_floor),0).'</td>
								<td class="break_all" align="right" width="100">'. number_format($floor_avale_minute,0).'</td>
								<td class="break_all" align="right" width="100">'. number_format($floor_produc_min,0).'</td>
								<td class="break_all" align="right" width="60">'.number_format($line_floor_production/($floor_tgt_h*$tot_floor_hour)*100,2).'%</td>
								<td class="break_all" align="right" width="90">'.number_format($floor_efficency,2).' %</td>
								<td class="break_all" width="70" align="right">'.number_format($gnd_total_fob_val / $line_floor_production ,2).'</td>
								<td class="break_all" align="right" width="70">'.number_format($gnd_total_fob_val,2).'</td>';

								$gnd_total_fob_val=0;
								$gnd_total_fob_rate=0;
								for($k=$hour; $k<=$last_hour; $k++)
								{
									$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";


									if($start_hour_arr[$k]==$global_start_lanch)
									{
										$bg_color='background:yellow';
									}
									if($floor_tgt_h>$floor_production[$prod_hour])
									{
										$bg_color='background:red';
										if($floor_production[$prod_hour]==0)
										{
											$bg_color='';
										}
									}
									else
									{
										$bg_color='';
									}


									$html.='<td class="break_all" align="right" width="50" style='.$bg_color.' >'. $floor_production[$prod_hour].'</td>';
								}
								$html.='</tr>';
								$floor_html.='<tbody>';
								$floor_html.="<tr bgcolor='$bgcolor' onclick=change_color('floor_$j','$bgcolor') id=floor_$j>";
								$floor_html.='<td class="break_all" width="40">'.$j.'&nbsp;</td>
								<td class="break_all" width="80" align="center">'.$floor_name.'&nbsp;</td>
								<td class="break_all" width="70" align="right">'. $floor_tgt_h.'</td>
								<td class="break_all" width="70" align="right">'.$floor_capacity.'</td>
								<td class="break_all" align="right" width="60">'. $floor_man_power.'</td>
								<td class="break_all" width="70" align="right">'.$floor_operator.'</td>
								<td class="break_all" width="50" align="right">'. $floor_helper.'</td>
								<td class="break_all" align="right" width="60">'. $floor_working_hour.'</td>
								<td class="break_all" align="right" width="80">'. $eff_target_floor.'</td>
								<td class="break_all" align="right" width="80">'.$line_floor_production.'</td>
								<td class="break_all" align="right" width="80">'. ($line_floor_production-$eff_target_floor).'</td>
								<td class="break_all" align="right" width="100">'. number_format($floor_avale_minute,0).'</td>
								<td class="break_all" align="right" width="100">'. number_format($floor_produc_min,0).'</td>
								<td class="break_all" align="right" width="90">'. number_format(($line_floor_production/($floor_tgt_h*$tot_floor_hour))*100,2).'%</td>';
								if($floor_efficency<=$txt_parcentage)
								{
									$floor_html.='<td class="break_all" align="right" width="90" bgcolor="red">'.number_format($floor_efficency,2).' %</td>';
								}
								else
								{
									$floor_html.='<td class="break_all" align="right" width="90" >'.number_format($floor_efficency,2).' %</td>';
								}
								for($k=$hour; $k<=$last_hour; $k++)
								{
									$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
									if($start_hour_arr[$k]==$global_start_lanch)
									{
										$floor_html.='<td class="break_all" align="right" width="50" style=" background-color:#FFFF66" >'. $floor_production[$prod_hour].'</td>';
									}
									else
									{
										$floor_html.='<td class="break_all" align="right" width="50" >'. $floor_production[$prod_hour].'</td>';
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
					//$item_ids=$production_data_arr[$f_id][$ldata]['item_number_id'];
					$germents_item=array_unique(explode('****',$production_data_arr[$f_id][$ldata]['item_number_id']));

					$buyer_neme_all=array_unique(explode(',',$production_data_arr[$f_id][$ldata]['buyer_name']));
					$buyer_name="";
					foreach($buyer_neme_all as $buy)
					{
						if($buyer_name!='') $buyer_name.=',';
						$buyer_name.=$buyerArr[$buy];
					}
				    $tot_line_hour = count($production_hour_arr[$f_id][$l_id]);

					// $tot_floor_hour=count($production_floor_arr[$f_id]);

					// $tot_hour=count($production_total_hour);
                    $tot_floor_hour=$production_floor_arr[$f_id];

					$floor_tot+=$tot_line_hour;
					

				

					// echo $tot_line_hour;
					//  echo $tot_floor_hour;
					//  echo $summary_tot_floor_hour;


				
					$garment_itemname='';
					$active_days='';
					$item_smv="";$item_ids='';
					$smv_for_item="";
					$produce_minit="";
					$order_no_total="";
					$efficiency_min=0;
				
					
					$tot_po_qty=0;$fob_val=0;
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
						if($smv_for_item!="") $smv_for_item.="****".$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]]."**".$po_garment_item[1];
						else
						$smv_for_item=$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]]."**".$po_garment_item[1];
						$produce_minit+=$production_po_data_arr[$f_id][$l_id][$po_garment_item[0]][$po_garment_item[1]]*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
						// echo $production_po_data_arr[$f_id][$l_id][$po_garment_item[0]]."*".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]]."<br>";
						$fob_rate=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt']/$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];
						$prod_qty=$production_data_arr_qty[$f_id][$l_id][$po_garment_item[0]][$po_garment_item[1]]['quantity'];
						//echo $prod_qty.'<br>';
						if(is_nan($fob_rate)){ $fob_rate=0; }
						$fob_val+=$prod_qty*$fob_rate;


					}
					//$fob_rate=$tot_po_amt/$tot_po_qty;

					$subcon_po_id=array_unique(explode(',',$production_data_arr[$f_id][$ldata]['order_id']));
					$subcon_order_id="";
					foreach($subcon_po_id as $sub_val)
					{
						$subcon_item=explode('**',$sub_val);
						$subcon_po_smv=explode(',',$sub_val);
						if($subcon_item[0]!=0)
						{
						if($item_smv!='') $item_smv.='/';
						if($item_smv!='') $item_smv.='/';
						$item_smv.=$subcon_order_smv[$subcon_item[0]];
						}
						$produce_minit+=$production_po_data_arr[$f_id][$l_id][$subcon_item[0]][$subcon_item[1]]*$subcon_order_smv[$subcon_item[0]];
						if($subcon_order_id!="") $subcon_order_id.=",";
						$subcon_order_id.=$subcon_item[0];
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
						$days_run=datediff("d",$sewing_day,$pr_date);
						}
						else  $days_run=0;
					}
					$type_line=$production_data_arr[$f_id][$ldata]['type_line'];
					$prod_reso_allo=$production_data_arr[$f_id][$ldata]['prod_reso_allo'];
					/*if($type_line==2)
					{
						$sewing_line='';
						if($production_data_arr[$f_id][$ldata]['prod_reso_allo']==1)
						{
							$line_number='';
							$line_number=explode(",",$ldata);
							foreach($line_data as $lin_id)
							{
								//echo $lin_id.'dd';
								$line_number=explode(",",$prod_reso_arr[$lin_id]);
							}
							foreach($line_number as $val)
							{
								if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
							}
						}
						else $sewing_line=$lineArr[$lin_id];
					}
					else
					{*/
						$sewing_line='';
						if($production_data_arr[$f_id][$ldata]['prod_reso_allo']==1)
						{
							$line_number=explode(",",$prod_reso_arr[$ldata]);
							foreach($line_number as $val)
							{
								if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
							}
						}
						else $sewing_line=$lineArr[$ldata];
					//}

					/*$sewing_line='';
					if($production_data_arr[$f_id][$ldata]['prod_reso_allo']==1)
					{
					$line_number=explode(",",$prod_reso_arr[$ldata]);
					foreach($line_number as $val)
					{
					if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
					}
					}
					else $sewing_line=$lineArr[$ldata];*/


					$lunch_start="";
					$lunch_start=$line_number_arr[$ldata][$pr_date]['lunch_start_time'];
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
					for($h=$hour;$h<=$last_hour;$h++)
					{
						$prod_hour="prod_hour".substr($line_start_hour_arr[$h],0,2)."";
						$production_hour[$prod_hour]=$production_data_arr[$f_id][$ldata][$prod_hour];
						$floor_production[$prod_hour]+=$production_data_arr[$f_id][$ldata][$prod_hour];
						$total_production[$prod_hour]+=$production_data_arr[$f_id][$ldata][$prod_hour];
					}

					
					$floor_production['prod_hour24']+=$production_data_arr[$f_id][$ldata]['prod_hour23'];
					$total_production['prod_hour24']+=$production_data_arr[$f_id][$ldata]['prod_hour23'];
					$production_hour['prod_hour24']=$production_data_arr[$f_id][$ldata]['prod_hour23'];
					$line_production_hour=0;
					if(str_replace("'","",$actual_production_date)>str_replace("'","",$actual_date))
					{
						if($type_line==2) //No Profuction Line
						{
							$line_start=$production_data_arr[$f_id][$l_id]['prod_start_time'];
						}
						else
						{
							$line_start=$line_number_arr[$ldata][$pr_date]['prod_start_time'];
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
						$total_effi_hour=0;
						for($lh=$line_start_hour;$lh<=$last_hour;$lh++)
						{
							$bg=$start_hour_arr[$lh];
							if($lh<$actual_time)
							{
							$total_eff_hour=$total_eff_hour+1;;
							$line_hour="prod_hour".substr($bg,0,2)."";
							$line_production_hour+=$production_data_arr[$f_id][$ldata][$line_hour];
							$line_floor_production+=$production_data_arr[$f_id][$ldata][$line_hour];
							$line_total_production+=$production_data_arr[$f_id][$ldata][$line_hour];
							$actual_time_hour=$start_hour_arr[$lh+1];
							}
						}
						if($start_hour_arr[$actual_time]>$lunch_start_hour) $total_eff_hour=$total_eff_hour-1;

						if($type_line==2)
						{
							if($total_eff_hour>$production_data_arr[$f_id][$l_id]['working_hour'])
							{
								$total_eff_hour=$production_data_arr[$f_id][$l_id]['working_hour'];
							}
						}
						else
						{
							if($total_eff_hour>$prod_resource_array[$ldata][$pr_date]['working_hour'])
							{
								$total_eff_hour=$prod_resource_array[$ldata][$pr_date]['working_hour'];
							}
						}

					}
					if(str_replace("'","",$actual_production_date)<=str_replace("'","",$actual_date))
					{
						for($ah=$hour;$ah<=$last_hour;$ah++)
						{
							$prod_hour="prod_hour".substr($start_hour_arr[$ah],0,2)."";
							$line_production_hour+=$production_data_arr[$f_id][$ldata][$prod_hour];
							$line_floor_production+=$production_data_arr[$f_id][$ldata][$prod_hour];
							$line_total_production+=$production_data_arr[$f_id][$ldata][$prod_hour];
							if($production_data_arr[$f_id][$ldata][$prod_hour]>0)
							{
								$total_effi_hour++;
							}
						}
						if($type_line==2)
						{
							$total_eff_hour=$production_data_arr[$f_id][$l_id]['working_hour'];
						}
						else
						{
							$total_eff_hour=$prod_resource_array[$ldata][$pr_date]['working_hour'];

						}
					}

					if($sewing_day!="")
					{
						$days_run= $diff=datediff("d",$sewing_day,$pr_date);
						$days_active= $active_days_arr[$f_id][$l_id];
					}
					else
					{
						$days_run=0;
						$days_active=0;
					}

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
						// $cla_cur_time=$total_effi_hour;
					}
					else
					{
						$current_wo_time=$total_eff_hour;
						$cla_cur_time=$total_eff_hour;
						// $cla_cur_time=$total_effi_hour;
					}
					$total_adjustment=0;
					if($type_line==2) //No Production Line
					{
						$smv_adjustmet_type=$production_data_arr[$f_id][$l_id]['smv_adjust_type'];
						$eff_target=($production_data_arr[$f_id][$l_id]['terget_hour']*$total_eff_hour);

						if($total_eff_hour>=$production_data_arr[$f_id][$l_id]['working_hour'])
						{
							if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$production_data_arr[$f_id][$l_id]['smv_adjust'];
							if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($production_data_arr[$f_id][$l_id]['smv_adjust'])*(-1);
						}
						$efficiency_min+=$total_adjustment+($production_data_arr[$f_id][$l_id]['man_power'])*$cla_cur_time*60;
						$line_efficiency=(($produce_minit)*100)/$efficiency_min;
					}
					else
					{
						$smv_adjustmet_type=$prod_resource_array[$ldata][$pr_date]['smv_adjust_type'];
						$eff_target=($prod_resource_array[$ldata][$pr_date]['terget_hour']*$total_eff_hour);

						if($total_eff_hour>=$prod_resource_array[$ldata][$pr_date]['working_hour'])
						{
						if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array[$ldata][$pr_date]['smv_adjust'];
						if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array[$ldata][$pr_date]['smv_adjust'])*(-1);
						}

						/*$actual_hours=date("H",time())-$line_start_hour; for metro
						$cur_time=date("H",time());
						if($cur_time>$line_start_hour){
							$actual_hours=$actual_hours-1;
						}
						else
						{
							$actual_hours=$actual_hours	;
						}
						$total_eff_hour_custom=($actual_hours>$total_eff_hour)?$total_eff_hour:$actual_hours;

						$producting_day=strtotime("Y-m-d",$txt_producting_day);

						if($producting_day>$today_date)
						{

							$efficiency_min+=$total_adjustment+($prod_resource_array[$ldata][$pr_date]['man_power'])*$total_eff_hour*60;
							//echo $total_eff_hour."_";
						}
						else
						{
							$efficiency_min+=$total_adjustment+($prod_resource_array[$ldata][$pr_date]['man_power'])*$total_eff_hour_custom*60;
							//echo $total_eff_hour_custom."_";
						}*/



						
						$efficiency_min+=$total_adjustment+($prod_resource_array[$ldata][$pr_date]['man_power'])*$cla_cur_time*60;
						$line_efficiency=(($produce_minit)*100)/$efficiency_min;
						// echo "((".$produce_minit.")*100)/".$efficiency_min."==".$cla_cur_time."<br>";
					}
                 



					if($type_line==2) //No Production Line
					{
						$man_power=$production_data_arr[$f_id][$l_id]['man_power'];
						$operator=$production_data_arr[$f_id][$l_id]['operator'];
						$helper=$production_data_arr[$f_id][$l_id]['helper'];
						$terget_hour=$production_data_arr[$f_id][$l_id]['target_hour'];
						$capacity=$production_data_arr[$f_id][$l_id]['capacity'];
						$working_hour=$production_data_arr[$f_id][$l_id]['working_hour'];

						$floor_working_hour+=$production_data_arr[$f_id][$l_id]['working_hour'];
						$eff_target_floor+=$eff_target;
						$floor_today_product+=$today_product;
						$floor_avale_minute+=$efficiency_min;
						$floor_produc_min+=$produce_minit;
						$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
						$floor_capacity+=$production_data_arr[$f_id][$l_id]['capacity'];
						$floor_helper+=$production_data_arr[$ldata][$pr_date]['helper'];
						$floor_man_power+=$production_data_arr[$f_id][$l_id]['man_power'];
						$floor_operator+=$production_data_arr[$f_id][$l_id]['operator'];
						$total_operator+=$production_data_arr[$f_id][$l_id]['operator'];
						$total_man_power+=$production_data_arr[$f_id][$l_id]['man_power'];
						$total_helper+=$production_data_arr[$f_id][$l_id]['helper'];
						$total_capacity+=$production_data_arr[$f_id][$l_id]['capacity'];
						$floor_tgt_h+=$production_data_arr[$f_id][$l_id]['target_hour'];
						$total_working_hour+=$production_data_arr[$f_id][$l_id]['working_hour'];
						$gnd_total_tgt_h+=$production_data_arr[$f_id][$l_id]['target_hour'];
						
						
						
					
						

						
						$total_terget+=$eff_target;
						$grand_total_product+=$today_product;
						$gnd_avable_min+=$efficiency_min;
						$gnd_product_min+=$produce_minit;
						// $gnd_achivement+=$achivement;
						

						$gnd_total_fob_val+=$fob_val;
						$gnd_final_total_fob_val+=$fob_val;
					}
					else
					{
						$man_power=$prod_resource_array[$ldata][$pr_date]['man_power'];
						$operator=$prod_resource_array[$ldata][$pr_date]['operator'];
						$helper=$prod_resource_array[$ldata][$pr_date]['helper'];
						$terget_hour=$prod_resource_array[$ldata][$pr_date]['terget_hour'];
						$capacity=$prod_resource_array[$ldata][$pr_date]['capacity'];
						$working_hour=$prod_resource_array[$ldata][$pr_date]['working_hour'];

						$floor_capacity+=$prod_resource_array[$ldata][$pr_date]['capacity'];
						$floor_man_power+=$prod_resource_array[$ldata][$pr_date]['man_power'];
						$floor_operator+=$prod_resource_array[$ldata][$pr_date]['operator'];
						$floor_helper+=$prod_resource_array[$ldata][$pr_date]['helper'];
						$floor_tgt_h+=$prod_resource_array[$ldata][$pr_date]['terget_hour'];
						$floor_working_hour+=$prod_resource_array[$ldata][$pr_date]['working_hour'];
						$eff_target_floor+=$eff_target;
						$floor_today_product+=$today_product;
						$floor_avale_minute+=$efficiency_min;
						$floor_produc_min+=$produce_minit;
						$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
						
						// echo $summary_tot_floor_hour_two;
						
						// $gnd_achivement+=$achivement;
						

						$achievment_for_details=($line_production_hour/($terget_hour*$tot_line_hour))*100;

						$total_operator+=$prod_resource_array[$ldata][$pr_date]['operator'];
						$total_man_power+=$prod_resource_array[$ldata][$pr_date]['man_power'];
						$total_helper+=$prod_resource_array[$ldata][$pr_date]['helper'];
						$total_capacity+=$prod_resource_array[$ldata][$pr_date]['capacity'];
						$total_working_hour+=$prod_resource_array[$ldata][$pr_date]['working_hour'];
						$gnd_total_tgt_h+=$prod_resource_array[$ldata][$pr_date]['terget_hour'];
						
						
						$tot_achivement_for_details+=$achievment_for_details;
						$total_terget+=$eff_target;
						$gnd_achivement_for_details+=$tot_achivement_for_details;
						$grand_total_product+=$today_product;
						$gnd_avable_min+=$efficiency_min;
						$gnd_product_min+=$produce_minit;
						$gnd_total_fob_val+=$fob_val;
						$gnd_final_total_fob_val+=$fob_val;
						

					}
					$po_id=rtrim($production_data_arr[$f_id][$ldata]['po_id'],',');
					$po_id=array_unique(explode(",",$po_id));
					$style=rtrim($production_data_arr[$f_id][$ldata]['style']);
					$style=implode(",",array_unique(explode(",",$style)));

					$cbo_get_upto=str_replace("'","",$cbo_get_upto);
					$txt_parcentage=str_replace("'","",$txt_parcentage);

					$floor_name=$floorArr[$f_id];
					$floor_smv+=$item_smv;

					$floor_days_run+=$days_run;
					$floor_days_active+=$days_active;

					$po_id=$production_data_arr[$f_id][$ldata]['po_id'];//$item_ids//$subcon_order_id
					$styles=explode(",",$style);
					$style_button='';//
					foreach($styles as $sid)
					{
						if( $style_button=='')
						{
							$style_button="<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$ldata."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
						}
						else
						{
							$style_button.=", "."<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$ldata."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
						}
					}
					$as_on_current_hour_target=0; $as_on_current_hour_variance=0;
					$as_on_current_hour_target=$terget_hour*$cla_cur_time;
					$as_on_current_hour_variance=$line_production_hour-$as_on_current_hour_target;
					$rate_fob=($fob_val / $line_production_hour );
					// $gnd_total_fob_rate +=$rate_fob;

					//echo $line_production_hour."**".$fob_val."<br>";

					if($line_efficiency<=$txt_parcentage) $efficiency_color="#FF0000"; else $efficiency_color="#FFFFFF";
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$html.='<tbody>';
					$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
					$html.='<td class="break_all" width="40">'.$i.'&nbsp;</td>
							<td class="break_all" width="80">'.$floor_name.'&nbsp;</td>
							<td class="break_all" align="center" width="80" >'. $sewing_line.'&nbsp; </td>
							<td class="break_all" width="80"><p>'.$buyer_name.'&nbsp;</p></td>
							<td class="break_all" width="100"><p>'.$production_data_arr[$f_id][$ldata]['job_no'].'&nbsp;</p></td>
							<td class="break_all" width="140"><p>'.$production_data_arr[$f_id][$ldata]['po_number'].'&nbsp;</p></td>
							<td class="break_all" width="100"><p>'.$style_button.'&nbsp;</p></td>
							<td class="break_all" width="80"><p>'.$production_data_arr[$f_id][$ldata]['file'].'&nbsp;</p></td>
							<td class="break_all" width="80"><p>'.$production_data_arr[$f_id][$ldata]['ref'].'&nbsp;</p></td>
							<td class="break_all" width="120" style="word-wrap:break-word; word-break: break-all;">'.$garment_itemname.'</td>
							<td class="break_all" align="right" width="60"><p>'.$item_smv.'</p></td>
							<td class="break_all" align="right" width="70">'.$operator.'</td>
							<td class="break_all" align="right" width="50">'.$helper.'</td>
							<td class="break_all" align="right" width="60">'.$man_power.'</td>
							<td class="break_all" align="right" width="70">'.$terget_hour.'</td>
							<td class="break_all" align="right" width="60">'.$days_run.'</td>
							<td class="break_all" align="right" width="60">'.$active_days.'</td>
							<td class="break_all" align="right" width="70">'.$capacity.'</td>
							<td class="break_all" align="right" width="60">'.$working_hour.'</td>
							<td class="break_all" align="right" width="60" title="Production Hour">'.$cla_cur_time.'</td>
							<td class="break_all" align="right" width="60" title="Target Hour*Working Hour">'.number_format($as_on_current_hour_target,0).'</td>
							<td class="break_all" align="right" width="80">'.number_format($eff_target,0).'</td>
							<td class="break_all" width="75" align="right"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$ldata.",'tot_prod','".$smv_for_item."','".$acturl_hour_minute."','".$line_start."',".$txt_date.')">'.$line_production_hour.'</a></td>
							<td class="break_all" align="right" width="80" title="Line Production Hour - (Target Hour*Working Hour ">'.number_format($as_on_current_hour_variance,0).'</td>
							<td class="break_all" align="right" width="80" title="Line Production Hour - Effiecency Target">'.($line_production_hour-$eff_target).'</td>
							<td class="break_all" align="right" width="100" title="SMV Adjustment+(Manpower*Working Hour*60)">'.number_format($efficiency_min,0).'</td>

							<td class="break_all" width="100" align="right"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$ldata.",'tot_prod','".$smv_for_item."','".$acturl_hour_minute."','".$line_start."',".$txt_date.')">'.number_format($produce_minit,0).'</a></td>
							<td class="break_all" align="right" width="100"?>'. number_format(($line_production_hour/($terget_hour*$tot_line_hour))*100,2).'%</td>';

							if($line_efficiency<=$txt_parcentage)
							{
								$html.='<td class="break_all" align="right" width="60" bgcolor="red" title="(Produce min/Effiecency min) * 100">'.number_format($line_efficiency,2).'%</td>';
							}
							else
							{
								$html.='<td class="break_all" align="right" width="60" title="(Produce min/Effiecency min) * 100">'.number_format($line_efficiency,2).'%</td>';
							}
							$html.='<td class="break_all" width="70"  align="right">'.number_format($rate_fob,2).'</a></td>';
							$html.='<td class="break_all" width="70" title='.$fob_rate.' align="right"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$ldata.",'tot_fob_value_popup','".$smv_for_item."','".$actual_time_hour."','".$line_start."',".$txt_date.')">'.number_format($fob_val,2).'</a></td>';
							for($k=$hour; $k<=$last_hour; $k++)
							{
								$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";


								if($start_hour_arr[$k]==$lunch_start_hour)
								{
									$bg_color='background:yellow';
								}
								else if($terget_hour>$production_hour[$prod_hour])
								{
									$bg_color='background:red';
									if($production_hour[$prod_hour]==0)
									{
										$bg_color='';
									}
								}
								else if($terget_hour<$production_hour[$prod_hour])
								{
									$bg_color='background:green';
									if($production_hour[$prod_hour]==0)
									{
										$bg_color='';
									}
								}
								else
								{
									$bg_color="";
								}

								$html.='<td class="break_all" align="right" width="50"  style='.$bg_color.'>'.$production_hour[$prod_hour].'</td>';
								//$html.='<td class="break_all" align="right" width="50"  style=" background-color:#FFFF66" >'.$production_hour[$prod_hour].'&nbsp;kk</td>';
							}
					$html.='</tr>';
					$i++;
					$check_arr[]=$f_id;
					
				}
				}

			}
		}
	}
			$html.='<tr  bgcolor="#B6B6B6">
					<td class="break_all" width="40">&nbsp;</td>
					<td class="break_all" width="80">&nbsp;</td>
					<td class="break_all" width="80">&nbsp;</td>
					<td class="break_all" width="80">&nbsp;</td>
					<td class="break_all" width="100">&nbsp;</td>
					<td class="break_all" width="140">&nbsp;</td>
					<td class="break_all" width="100">&nbsp;</td>
					<td class="break_all" width="80">&nbsp;</td>
					<td class="break_all" width="80">&nbsp;</td>
					<td class="break_all" width="120">&nbsp;</td>
					<td class="break_all" align="right" width="60">&nbsp;</td>
					<td class="break_all" align="right" width="70">'. number_format($floor_operator,0).'</td>
					<td class="break_all" align="right" width="50">'. number_format($floor_helper,0).'</td>
					<td class="break_all" align="right" width="60">'. number_format($floor_man_power,0).'</td>
					<td class="break_all" align="right" width="70">'. number_format($floor_tgt_h,0).'</td>
					<td class="break_all" align="right" width="60">'. number_format($floor_days_run,0).'</td>
					<td class="break_all" align="right" width="60">'. number_format($floor_days_active,0).'</td>
					<td class="break_all" align="right" width="70">&nbsp;</td>
					<td class="break_all" align="right" width="60">'. number_format($floor_working_hour,0).'</td>
					<td class="break_all" align="right" width="60">&nbsp;</td>
					<td class="break_all" align="right" width="60">&nbsp;</td>
					<td class="break_all" align="right" width="80">'. number_format($eff_target_floor).'</td>
					<td class="break_all" align="right" width="80">'.number_format($line_floor_production,0).'</td>
					<td class="break_all" align="right" width="80">'.number_format($line_floor_production,0).'</td>
					<td class="break_all" align="right" width="80">'. ($line_floor_production-$eff_target_floor).'</td>
					<td class="break_all" align="right" width="100">'. number_format($floor_avale_minute,0).'</td>
					<td class="break_all" align="right" width="100">'. number_format($floor_produc_min,0).'</td>
					<td class="break_all" align="right" width="60">'. number_format($line_floor_production/($floor_tgt_h*$tot_floor_hour)*100,2).'%</td>
					<td class="break_all" align="right" width="90">'.number_format($floor_efficency,2).'%</td>
					<td class="break_all" width="70" align="right">'.number_format($gnd_total_fob_val / $line_floor_production ,2).'</td>
					<td class="break_all" width="70" align="right">'.number_format($gnd_total_fob_val,2).'</td>';

					for($k=$hour; $k<=$last_hour; $k++)
					{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";

						if($floor_tgt_h>$floor_production[$prod_hour])
						{
							$bg_color='background:red';
							if($floor_production[$prod_hour]==0)
							{
								$bg_color='';
							}
						}
						else if($floor_tgt_h<$floor_production[$prod_hour])
						{
							$bg_color='background:green';
							if($floor_production[$prod_hour]==0)
							{
								$bg_color='';
							}
						}
						else if($start_hour_arr[$k]==$global_start_lanch)
						{
							 $bg_color='background:yellow';
						//$html.='<td class="break_all" align="right" width="50" style=" background-color:#FFFF66" >'. $floor_production[$prod_hour].'&nbsp;</td>';
						}
						else
						{
							 $bg_color='';
						//$html.='<td class="break_all" align="right" width="50">'. $floor_production[$prod_hour].'&nbsp;</td>';
						}
						$html.='<td class="break_all" align="right" width="50" style='.$bg_color.' >'. $floor_production[$prod_hour].'</td>';
					}

				   $html.='</tr> </tbody>';
				   $floor_html.="<tr bgcolor='$bgcolor' onclick=change_color('floor_$j','$bgcolor') id=floor_$j>";
				   $floor_html.='<td class="break_all" width="40">'.$j.'&nbsp;</td>
								<td class="break_all" width="80" align="center">'.$floor_name.'&nbsp; </td>
								<td class="break_all" width="70" align="right">'. number_format($floor_tgt_h,0).'</td>
								<td class="break_all" width="70" align="right">'.number_format($floor_capacity,0).'</td>
								<td class="break_all" align="right" width="60">'. number_format($floor_man_power,0).'</td>
								<td class="break_all" width="70" align="right">'.number_format($floor_operator,0).'</td>
								<td class="break_all" width="50" align="right">'. number_format($floor_helper,0).'</td>
								<td class="break_all" align="right" width="60">'. number_format($floor_working_hour,0).'</td>
								<td class="break_all" align="right" width="80">'. number_format($eff_target_floor,0).'</td>
								<td class="break_all" align="right" width="80">'.number_format($line_floor_production,0).'</td>
								<td class="break_all" align="right" width="80">'. number_format(($line_floor_production-$eff_target_floor),0).'</td>
								<td class="break_all" align="right" width="100">'. number_format($floor_avale_minute,0).'</td>
								<td class="break_all" align="right" width="100">'. number_format($floor_produc_min,0).'</td>
								<td class="break_all" align="right" width="90" title="Total Production/(Today Target*Production Hour)">'. number_format(($line_floor_production/($floor_tgt_h*$tot_floor_hour))*100,2).'%</td>';
								if($floor_efficency<=$txt_parcentage)
								{
									$floor_html.='<td class="break_all" align="right" width="90" bgcolor="red">'.number_format($floor_efficency,2).'%</td>';
								}
								else
								{
									$floor_html.='<td class="break_all" align="right" width="90" >'.number_format($floor_efficency,2).'%</td>';
								}
								for($k=$hour; $k<=$last_hour; $k++)
								{
									$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
									if($start_hour_arr[$k]==$global_start_lanch)
									{
										$floor_html.='<td class="break_all" align="right" width="50" style=" background-color:#FFFF66 ">'. $floor_production[$prod_hour].'</td>';
									}
									else
									{
										$floor_html.='<td class="break_all" align="right" width="50" >'. $floor_production[$prod_hour].'</td>';
									}
								}
					$floor_html.='</tr></tbody>';
					$smv_for_item="";
				?>

	<fieldset style="width:2530px">
       <table width="2200" cellpadding="0" cellspacing="0">
            <tr class="form_caption">
                <td colspan="26" align="center"><strong><? echo $report_title; ?> &nbsp;</strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="26" align="center"><strong><? echo $companyArr[$comapny_id]; ?></strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="26" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date)) ); ?></strong></td>
            </tr>
        </table>
        <br />
        <table  width="600" cellpadding="0"  cellspacing="0" align="center" style="padding-left:200px">
            <tr>


                <td bgcolor="#FFFF66" height="18" width="30" ></td>
                <td> &nbsp;Lunch Hour</td>
                <td bgcolor="red" height="18" width="30"></td>
                <td> &nbsp;Efficiency % less than Standard And Production less than Target</td>


            </tr>
        </table>
        <label> <strong>Report Summary:-</strong></label>
          <table id="table_header_2" class="rpt_table" width="1940" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr height="50">
                    <th class="break_all" width="40">SL</th>
                    <th class="break_all" width="80">Floor Name</th>
                    <th class="break_all" width="70">Hourly Target</th>
                    <th class="break_all" width="70">Capacity</th>
                    <th class="break_all" width="60">Total Man Power</th>
                    <th class="break_all" width="70">Operator</th>
                    <th class="break_all" width="50">Helper</th>
                    <th class="break_all" width="60">Line Hour</th>
                    <th class="break_all" width="80">Day Target</th>
                    <th class="break_all" width="80">Total Prod.</th>
                    <th class="break_all" width="80">Variance </th>
                    <th class="break_all" width="100">SMV Available</th>
                    <th class="break_all" width="100">SMV Achieved</th>
                    <th class="break_all" width="90" title="Total Prod/(Hourly Target*Number of production Hour)*100">Achievement %</th>
                    <th class="break_all" width="90">Floor Eff. %</th>

                	<?
                	for($k=$hour+1; $k<=$last_hour+1; $k++)
					{
					?>
                    <th class="break_all" width="50" style="vertical-align:middle"><div class="block_div"><?  echo substr($start_hour_arr[$k],0,5);   ?></div></th>
					<?
					}
                	?>
                </tr>
            </thead>
        </table>
        <div style="width:1960px; max-height:400px; overflow-y:scroll" id="scroll_body">
           <table class="rpt_table" width="1940" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
           <?  echo $floor_html; ?>
            <tfoot>
                   <tr>
                        <th class="break_all" width="40"></th>
                        <th class="break_all" width="80">Total </th>
                        <th class="break_all" width="70"><? echo number_format($gnd_total_tgt_h,0);   ?> </th>
                        <th class="break_all" width="70" align="right"><? echo number_format($total_capacity,0); ?> </th>
                        <th class="break_all" width="60"><? echo number_format($total_man_power,0); ?></th>
                        <th class="break_all" width="70"><? echo number_format($total_operator,0); ?></th>
                        <th class="break_all" width="50"><? echo number_format($total_helper,0); ?></th>
                        <th class="break_all" align="right" width="60"><? echo number_format($total_working_hour,0); ?></th>
                        <th class="break_all" align="right" width="80"><? echo number_format($total_terget,0); ?></th>
                        <th class="break_all" align="right" width="80"><? echo number_format($line_total_production,0); ?></th>
                        <th class="break_all" align="right" width="80"><? echo number_format($line_total_production-$total_terget,0); ?></th>
                        <th class="break_all" align="right" width="100"><? echo number_format($gnd_avable_min,0); ?></th>
                        <th class="break_all" align="right" width="100"><? echo number_format($gnd_product_min,0); ?></th>
                        <th class="break_all" align="right" width="90" title="<? echo "Line Total Production=$line_total_production/(Today Target=$gnd_total_tgt_h*Total Hour=$floor_tot)*100;"?>"><?  echo number_format(($line_total_production/($gnd_total_tgt_h*$floor_tot))*100,2)."%" ; ?></th>
                        <th class="break_all" align="center" width="90"><? echo number_format(($gnd_product_min/$gnd_avable_min)*100,2)."%"; ?></th>
                        <?
						for($k=$hour; $k<=$last_hour; $k++)
						{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
						?>
						  <th class="break_all" width="50" ><?  echo $total_production[$prod_hour];   ?></th>
						<?
						}
                		?>
                   </tr>
               </tfoot>

          </table>

        </div>
    </br><br/>
    <? $smv_source_arr = array(1=>'Order Entry',2=>'Pre Costing',3=>'GSD Entry');?>
        <table id="table_header_1" class="rpt_table" width="3200" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr height="50">
                    <th class="break_all" width="40">SL</th>
                    <th class="break_all" width="80">Floor Name</th>
                    <th class="break_all" width="80">Line No</th>
                    <th class="break_all" width="80">Buyer</th>
                    <th class="break_all" width="100">Job</th>
                    <th class="break_all" width="140">Order No</th>
                    <th class="break_all" width="100">Style Ref.</th>
                    <th class="break_all" width="80">File No</th>
                    <th class="break_all" width="80">Ref. No</th>
                    <th class="break_all" width="120">Garments Item</th>
                    <th class="break_all" width="60" title="SMV Source From [<?= $smv_source_arr[$smv_source];?>]">SMV</th>
                    <th class="break_all" width="70">Operator</th>
                    <th class="break_all" width="50">Helper</th>
                    <th class="break_all" width="60"> Man Power</th>
                    <th class="break_all" width="70">Hourly Target (Pcs)</th>
                    <th class="break_all" width="60">Days Run</th>
                    <th class="break_all" width="60">Active Prod.Days</th>
                    <th class="break_all" width="70">Capacity</th>
                    <th class="break_all" width="60">Working Hour</th>
                    <th class="break_all" width="60">Current Hour</th>
                    <th class="break_all" width="60">As On Current Hour Target (Pcs)</th>
                    <th class="break_all" width="80">Total Target</th>
                    <th class="break_all" width="80">Total Prod.</th>
                    <th class="break_all" width="80">As On Current Hour Prod.Variance</th>
                    <th class="break_all" width="80">Total Variance (Pcs)</th>
                    <th class="break_all" width="100">Available Minutes</th>
                    <th class="break_all" width="100">Produce Minutes</th>
                    <th class="break_all" width="100"><p>Achivement%<p></th>
                    <th class="break_all" width="90">Line Effi %</th>
					<th class="break_all" width="70">FOB Rate(Avg.)</th>
                    <th class="break_all" width="70">FOB Val.</th>
                   <?

                	for($k=$hour+1; $k<=$last_hour+1; $k++)
					{
					?>
                      <th class="break_all" width="50" style="vertical-align:middle"><div class="block_div"><?  echo substr($start_hour_arr[$k],0,5);   ?></div></th>
					<?
					}
                ?>
                </tr>
            </thead>
        </table>
        <div style="width:3200px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="3200" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
              <? echo $html;  ?>
                <tfoot>
                   <tr>
                        <th class="break_all" width="40">&nbsp;</th>
                        <th class="break_all" width="80">&nbsp;</th>
                        <th class="break_all" width="80">&nbsp;</th>
                        <th class="break_all" width="80">&nbsp;</th>
                        <th class="break_all" width="100">&nbsp;</th>
                        <th class="break_all" width="140">&nbsp;</th>
                        <th class="break_all" width="100">&nbsp;</th>
                        <th class="break_all" width="80">&nbsp;</th>
                        <th class="break_all" width="80">&nbsp;</th>
                        <th class="break_all" width="120">Total</th>
                        <th class="break_all" align="right" width="60"><? // echo number_format($total_smv/($i-1),2); ?>&nbsp;</th>
                        <th class="break_all" align="right" width="70"><? echo number_format($total_operator,0); ?></th>
                        <th class="break_all" align="right" width="50"><? echo number_format($total_helper,0); ?></th>
                        <th class="break_all" align="right" width="60"><? echo number_format($total_man_power,0); ?></th>
                        <th class="break_all" align="right" width="70"><?  echo number_format($gnd_total_tgt_h,0); ?></th>
                        <th class="break_all" align="right" width="60"><? //echo $prod_resource_array[$row[csf('sewing_line')]][$pr_date]['man_power']; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="60">&nbsp;</th>
                        <th class="break_all" align="right" width="70"><? echo number_format($total_capacity,0); ?></th>
                        <th class="break_all" align="right" width="60"><? echo number_format($total_working_hour,0); ?></th>
                        <th class="break_all" align="right" width="60">&nbsp;</th>
                        <th class="break_all" align="right" width="60">&nbsp;</th>
                        <th class="break_all" align="right" width="80"><? echo number_format($total_terget,0); ?></th>
                        <th class="break_all" align="right" width="80"><? echo number_format($line_total_production,0); ?></th>
                        <th class="break_all" align="right" width="80">&nbsp;</th>
                        <th class="break_all" align="right" width="80"><? echo number_format($line_total_production-$total_terget,0); ?></th>
                        <th class="break_all" align="right" width="100"><? echo number_format($gnd_avable_min,0); ?></th>
                        <th class="break_all" align="right" width="100"><? echo number_format($gnd_product_min,0); ?></th>
                        <th class="break_all" align="right" width="60"><? echo number_format($line_total_production/($gnd_total_tgt_h*$floor_tot)*100,2)."%"; ?></th>
                        <th class="break_all" align="right" width="90" ><? echo number_format(($gnd_product_min/$gnd_avable_min)*100,2)."%";?></th>
                        <th class="break_all" align="right" width="70"><? echo number_format($gnd_final_total_fob_val /$line_total_production ,2);?></th>

                        <th class="break_all" align="right" width="70"><? echo number_format($gnd_final_total_fob_val,2);?></th>
					    <?
						for($k=$hour; $k<=$last_hour; $k++)
						{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
						?>
						<th class="break_all" align="right" width="50"><? echo $total_production[$prod_hour]; ?></th>
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
			    $sql_active_line=sql_select("select sewing_line,sum(production_quantity) as total_production from pro_garments_production_mst  where production_date=".$txt_date." and production_type=5 and status_active=1 and is_deleted=0 and prod_reso_allo=1 group by  sewing_line");

				//$actual_line_arr=array();
				foreach($sql_active_line as $inf)
				{

				   if(str_replace("","",$inf[csf('sewing_line')])!="")
				   {
						if(str_replace("","",$actual_line_arr)!="") $actual_line_arr.=",";
					    $actual_line_arr.="'".$inf[csf('sewing_line')]."'";
				   }
				}

				$sql_active_line=sql_select("SELECT line_id as sewing_line,sum(production_qnty) as total_production from subcon_gmts_prod_dtls  where production_date=".$txt_date." and production_type=2 and status_active=1 and is_deleted=0 and prod_reso_allo=1 group by  line_id");

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
			if($db_type==0) $remarks_cond="group_concat(d.remarks)";
			else if($db_type==2) $remarks_cond="LISTAGG(cast(d.remarks as varchar2(4000)), ',') WITHIN GROUP (ORDER BY d.remarks)";

			//echo "select a.id, a.floor_id, a.line_number, b.man_power, b.operator, b.helper, b.working_hour, $remarks_cond as remarks from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and b.pr_date=".$txt_date." and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 $line_cond group by a.id, a.floor_id, a.line_number, b.man_power, b.operator, b.helper, b.working_hour";
			 $dataArray=sql_select("select a.id, a.floor_id, a.line_number, b.man_power, b.operator, b.helper, b.working_hour, $remarks_cond as remarks from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and b.pr_date=".$txt_date." and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 $line_cond group by a.id, a.floor_id, a.line_number, b.man_power, b.operator, b.helper, b.working_hour order by a.floor_id");
					$l=1; $location_array=array(); $floor_array=array();
					foreach( $dataArray as $row )
					{
						if ($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$remarks="";
						$remarks=implode(",",array_unique(explode(",",$row[csf('remarks')])));
						$sewing_lines='';
						$line_numbers=array_unique(explode(",",$row[csf('line_number')]));
						//print_r($line_number);
						foreach($line_numbers as $val)
						{
							if($sewing_lines=='') $sewing_lines=$lineArr[$val]; else $sewing_lines.=",".$lineArr[$val];
						}
					?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tbltr_<? echo $l; ?>','<? echo $bgcolor; ?>')" id="tbltr_<? echo $l; ?>">
                        	<td width="40"><? echo $l; ?></td>
                            <td width="100"><p><? echo $sewing_lines; ?>&nbsp;</p></td>
                            <td width="100" align="right"><p>&nbsp;<? echo $floorArr[$row[csf('floor_id')]]; ?></p></td>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('man_power')]; ?></th>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('operator')]; ?></th>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('helper')]; ?></td>
                            <td width="75" align="right">&nbsp;<? echo $row[csf('working_hour')]; ?></td>
                            <td width="380"><? echo $remarks; ?>&nbsp;</td>
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

} // 6th Button End

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
						   $new_smv[$po_id_smv_pop[0]][$po_id_smv_pop[2]]=$po_id_smv_pop[1];
					 }

		$actual_date=date("Y-m-d");
	    $actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$prod_date)));
		if($db_type==0)
		{
			$actual_date=date("Y-m-d");
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
			{
			   $sql_pop="SELECT  c.po_number,a.po_break_down_id,
						sum(CASE WHEN a.production_hour>'$line_date'  and a.production_hour<='$actual_time'  and a.production_type=5 THEN a.production_quantity else 0 END)  as good_qnty
						from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
						where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and a.serving_company=".$company_id."  and a.floor_id=".$floor_id." and a.sewing_line=".$sewing_line."  and a.po_break_down_id in(".$po_id.") and a.production_date='".$prod_date."'  group by c.po_number,a.po_break_down_id order by  c.po_number ";
			}
			if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date))
			{
			   	$sql_pop="SELECT  c.po_number,a.po_break_down_id,
						sum(CASE WHEN a.production_type=5 THEN a.production_quantity else 0 END)  as good_qnty
						from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
						where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and a.serving_company=".$company_id."  and a.floor_id=".$floor_id." and a.sewing_line=".$sewing_line."  and a.po_break_down_id in(".$po_id.") and a.production_date='".$prod_date."'  group by c.po_number,a.po_break_down_id order by  c.po_number ";
			}
		}
		else
		{
			$actual_date=date("Y-m-d");
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
		 	{
	       		$sql_pop="SELECT  c.po_number,a.po_break_down_id,a.item_number_id,
		                sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$line_date'  and TO_CHAR(a.production_hour,'HH24:MI')<='$actual_time'  and a.production_type=5 THEN d.production_qnty else 0 END)  as good_qnty
						from pro_garments_production_mst a,pro_garments_production_dtls d, wo_po_details_master b, wo_po_break_down c
						where a.production_type=5 and d.mst_id=a.id and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id=".$company_id."  and a.floor_id=".$floor_id." and a.sewing_line=".$sewing_line."  and a.po_break_down_id in(".$po_id.") and a.production_date='".$prod_date."'  group by c.po_number,a.po_break_down_id,a.item_number_id order by  c.po_number ";
		 	}
		 	if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date))
		  	{

	       		$sql_pop="SELECT  c.po_number,a.po_break_down_id,a.item_number_id,
		                sum(CASE WHEN  a.production_type=5 THEN d.production_qnty else 0 END)  as good_qnty
						from pro_garments_production_mst a,pro_garments_production_dtls d, wo_po_details_master b, wo_po_break_down c
						where a.production_type=5 and d.mst_id=a.id and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id=".$company_id."  and a.floor_id=".$floor_id." and a.sewing_line=".$sewing_line."  and a.po_break_down_id in(".$po_id.") and a.production_date='".$prod_date."'  group by c.po_number,a.po_break_down_id,a.item_number_id order by  c.po_number ";
		  	}

		}
		// echo $sql_pop;
		$sql_pop_data=sql_select($sql_pop);
        $subcon_production_data_arr=array();
		foreach($sql_pop_data as $pro_val)
		{
			$subcon_production_data_arr[$pro_val[csf('po_break_down_id')]][$pro_val[csf('po_number')]][$pro_val[csf('item_number_id')]]['po_number']=$pro_val[csf('po_number')];
			$subcon_production_data_arr[$pro_val[csf('po_break_down_id')]][$pro_val[csf('po_number')]][$pro_val[csf('item_number_id')]]['po_qty']=$pro_val[csf('good_qnty')];
			$subcon_production_data_arr[$pro_val[csf('po_break_down_id')]][$pro_val[csf('po_number')]][$pro_val[csf('item_number_id')]]['item_smv']=$new_smv[$pro_val[csf('po_break_down_id')]][$pro_val[csf('item_number_id')]];

		}

		if($subcon_order!="")
		{
	         if($db_type==0)
			 {
				if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
				{
					$sql_subcon="SELECT
								a.order_id,c.smv,
								c.order_no as po_number,
								sum(CASE WHEN a.hour>'$line_date' and a.hour<='$actual_time' and a.production_type=2  THEN a.production_qnty else 0 END) AS good_qnty
								from subcon_gmts_prod_dtls a, subcon_ord_dtls c
								where a.production_type=2 and a.order_id=c.id  and a.status_active=1 and a.is_deleted=0  and a.company_id=".$company_id."  and a.floor_id=".$floor_id." and a.line_id=".$sewing_line."  and a.order_id in(".$subcon_order.") and a.production_date='".$prod_date."'                         	   group by a.order_id, c.order_no,c.smv";
				}
				if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date))
				{
	             	$sql_subcon="SELECT
						       a.order_id,c.smv,
					           c.order_no as po_number,
						 	   sum(a.production_qnty ) AS good_qnty
						       from subcon_gmts_prod_dtls a, subcon_ord_dtls c
						       where a.production_type=2 and a.order_id=c.id  and a.status_active=1 and a.is_deleted=0  and a.company_id=".$company_id."  and a.floor_id=".$floor_id." and a.line_id=".$sewing_line."  and a.order_id in(".$subcon_order.") and a.production_date='".$prod_date."'    group by a.order_id, c.order_no,c.smv";
			     }
			 }
			 else
			 {
			  	if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date))
				{
					$sql_subcon="SELECT
							a.order_id,c.smv,
							c.order_no as po_number,
							sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')>'$line_date' and TO_CHAR(a.hour,'HH24:MI')<='$actual_time' and a.production_type=2  THEN a.production_qnty else 0 END) AS good_qnty
							from subcon_gmts_prod_dtls a, subcon_ord_dtls c
							where a.production_type=2 and a.order_id=c.id  and a.status_active=1 and a.is_deleted=0  and a.company_id=".$company_id."  and a.floor_id=".$floor_id." and a.line_id=".$sewing_line."  and a.order_id in(".$subcon_order.") and a.production_date='".$prod_date."'  group by a.order_id, c.order_no,c.smv";
				}

				if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date))
				{
					$sql_subcon="select
							a.order_id,c.smv,
							c.order_no as po_number,
							sum(a.production_qnty) AS good_qnty
							from subcon_gmts_prod_dtls a, subcon_ord_dtls c
							where a.production_type=2 and a.order_id=c.id  and a.status_active=1 and a.is_deleted=0  and a.company_id=".$company_id."  and a.floor_id=".$floor_id." and a.line_id=".$sewing_line."  and a.order_id in(".$subcon_order.") and a.production_date='".$prod_date."'     group by a.order_id, c.order_no,c.smv";
				}
			 }
			//  echo $sql_subcon;
			 $sql_subcon_data=sql_select($sql_subcon);
			 foreach($sql_subcon_data as $sub_val)
			 {
				 $subcon_production_data_arr[$sub_val[csf('order_id')]][$sub_val[csf('po_number')]]['po_number']=$sub_val[csf('po_number')];
				 $subcon_production_data_arr[$sub_val[csf('order_id')]][$sub_val[csf('po_number')]]['po_qty']=$sub_val[csf('good_qnty')];
				 $subcon_production_data_arr[$sub_val[csf('order_id')]][$sub_val[csf('po_number')]]['item_smv']=$sub_val[csf('smv')];

			 }
		}

		$smv_adjustment_sql="SELECT b.ADJUSTMENT_SOURCE, sum(b.NUMBER_OF_EMP) as NUMBER_OF_EMP, sum(b.ADJUST_HOUR) as ADJUST_HOUR, sum(b.TOTAL_SMV) as TOTAL_SMV
		from prod_resource_mst a, prod_resource_smv_adj b
		where a.id=b.mst_id and a.id=$sewing_line and a.is_deleted=0 and b.status_active=1 and b.adjustment_source in (9,10) and a.company_id=$company_id  and a.floor_id=$floor_id and b.pr_date='$prod_date' group by b.adjustment_source";
		// echo $smv_adjustment_sql;
		$smv_adjustment_data=sql_select($smv_adjustment_sql);
		$smv_adjustment_plus=$smv_adjustment_minus=0;
		foreach($smv_adjustment_data as $row)
		{
			if($row["ADJUSTMENT_SOURCE"]==9)
			{
				$smv_adjustment_plus=$row["TOTAL_SMV"];
			}
			if($row["ADJUSTMENT_SOURCE"]==10)
			{
				$smv_adjustment_minus=$row["TOTAL_SMV"];
			}
		}

		//print_r($subcon_production_data_arr);


			$total_producd_min=0;
			$i=1; $total_qnty=0;
			foreach($subcon_production_data_arr as $sub_id=>$pop_val)
			{
				foreach($pop_val as $po_id=>$pop_no_val)
                {
					foreach($pop_no_val as $item_id=>$pop_val)
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
			}
				?>
				<tfoot>
					<tr>
						<th colspan="3" align="right">Total</th>
						<th align="right"><? echo $total_po_qty; ?>&nbsp;</th>
						<th align="right"><? echo number_format($total_producd_min,2); ?>&nbsp;</th>
					</tr>
					<tr>
						<th colspan="4" align="right">Final Total</th>
						<th align="right"><? echo number_format($total_producd_min+$smv_adjustment_plus-$smv_adjustment_minus,2); ?>&nbsp;</th>
					</tr>
				</tfoot>
			</table>
			<br>
			<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th width="200">Particulars</th>
						<th width="80">No Of Employee</th>
						<th width="80">Hour</th>
						<th >Minutes</th>
					</tr>
				</thead>
				<tbody>
					<?
						foreach($smv_adjustment_data as $row)
						{
							?>
								<tr>
									<td ><?=$smv_adjustment_head[$row["ADJUSTMENT_SOURCE"]];?></td>
									<td align="right"><?=$row["NUMBER_OF_EMP"];?></td>
									<td align="right"><?=$row["ADJUST_HOUR"];?></td>
									<td align="right"><?=$row["TOTAL_SMV"];?></td>
								</tr>
							<?
						}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th ></th>
						<th colspan="2">Final Total</th>
						<th ><? echo number_format($smv_adjustment_plus-$smv_adjustment_minus,2); ?></th>
					</tr>
				</tfoot>
			</table>

        </div>
	</fieldset>
	<?
	exit();
}

if($action=="tot_fob_value_popup")
{
	echo load_html_head_contents("FOB Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

     <script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			$("#table_body tr:first").hide();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			$("#table_body tr:first").show();
		}
	</script>
    <fieldset style="width:500px; ">
    <div style="width:500px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:80px"  class="formbutton"/>
        </div>
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center">
				<caption><strong>FOB Value </strong></caption>
                <thead>
                	<th width="30">SL</th>
                    <th width="120">Order No</th>
                    <th width="120">Item</th>
                    <th width="80">Prod. Qnty</th>
                    <th width="60">Unit Price</th>
                    <th width="100">Amount</th>
				</thead>
                </table>
                <table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center" id="table_body">
                <?
						$sql_item_rate="select b.id, c.item_number_id, c.order_quantity, c.order_total from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c where b.job_no_mst=a.job_no and b.id=c.po_break_down_id and b.job_no_mst=c.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1  and c.po_break_down_id in(".$po_id.") ";
						$resultRate=sql_select($sql_item_rate);
						$item_po_array=array();
						foreach($resultRate as $row)
						{
							$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['qty']+=$row[csf('order_quantity')];
							$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['amt']+=$row[csf('order_total')];
						}

						$sql_pop=("select  c.po_number,a.po_break_down_id,a.item_number_id,avg(c.unit_price) as unit_price,
		                sum(CASE WHEN  a.production_type=5 THEN a.production_quantity else 0 END)  as good_qnty
						from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
						where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and a.serving_company=".$company_id."  and a.floor_id=".$floor_id." and a.sewing_line=".$sewing_line."  and a.po_break_down_id in(".$po_id.") and a.production_date='".$prod_date."' group by c.po_number,a.po_break_down_id,a.item_number_id  order by  c.po_number ");
						$sql_result=sql_select($sql_pop);
						$k=1;$total_amount=0;$total_prod_qty=0;
					  foreach($sql_result as $row)
					   {
					   if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$po_amount=$item_po_array[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]]['amt'];
						$po_qty=$item_po_array[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]]['qty'];
						//echo $po_amount.'=='.$po_qty.'<br>';
						$fob_rate=$po_amount/$po_qty;
			   ?>
                  <tr style="font:'Arial Narrow';" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
					<td width="30"><? echo $k; ?></td>
					<td width="120" style="word-wrap:break-word; word-break: break-all; text-align:left" ><? echo $row[csf('po_number')]; ?></td>
                    <td width="120" style="word-wrap:break-word; word-break: break-all; text-align:left" ><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
                    <td width="80" style="word-wrap:break-word; word-break: break-all; text-align:right" ><? echo number_format($row[csf('good_qnty')],0); ?></td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all; text-align:center" ><? echo number_format($fob_rate,6);?></td>
                    <td width="100" style="word-wrap:break-word; word-break: break-all; text-align:right" ><? echo  number_format($row[csf('good_qnty')]*$fob_rate,2); ?></td>
                </tr>
                <?
				$total_amount+=$row[csf('good_qnty')]*$fob_rate;
				$total_prod_qty+=$row[csf('good_qnty')];
				$k++;
                  }
				?>
                <tr class="tbl_bottom" >
                <td colspan="3"> Total </td>
                 <td align="right"> <? echo number_format($total_prod_qty);?> </td>
                 <td> </td>
                 <td align="right"> <? echo number_format($total_amount,2);?></td>
                </tr>
                </table>
         </div>
          <script>
 		setFilterGrid("table_body",-1);
 		</script>
     <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>


	<?
	exit();
}

if($action=="show_style_line_generate_report")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>

		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

			d.close();
		}

	</script>
		<div style="width:1080px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
		<fieldset style="width:1070px; margin-left:5px">
			<div id="report_container" >
	        <table border="1" class="rpt_table" rules="all" width="1070" cellpadding="0" cellspacing="0">

	        <caption> <strong>Style Details</strong></caption>
	        <?
			$buyerArr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	        $sqlPo="select a.job_no,a.buyer_name,a.set_smv,b.po_number,b.id as po_id from wo_po_break_down b,wo_po_details_master a where a.job_no=b.job_no_mst and b.id in(".$po_id.") and a.style_ref_no='$style'";
			$po_no='';$po_ids='';
			$dataPo=sql_select($sqlPo);
			foreach( $dataPo as $row)
			{
				if($po_no!='') $po_no.=",".$row[csf('po_number')];else $po_no=$row[csf('po_number')];
				if($po_ids!='') $po_ids.=",".$row[csf('po_id')];else $po_ids=$row[csf('po_id')];

				//if($po_ids!='') $po_ids.=",".$row[csf('po_id')];else $po_ids=$row[csf('po_id')];

				$set_smv=$row[csf('set_smv')];
				$job_no=$row[csf('job_no')];
				//echo $row[csf('buyer_name')];
				$buyer_name=$buyerArr[$row[csf('buyer_name')]];

			}
			//echo $buyerArr[$buyer_name];
			//$buyerArr
			$germents_id=array_unique(explode(",",$item_id));
			$item_name='';
			foreach($germents_id as $g_val)
			{
				//$item_name=$garments_item[$g_val];
				if($item_name!='') $item_name.=",".$garments_item[$g_val];else $item_name=$garments_item[$g_val];
			}
			//garments_item
			?>
	        <tr>
	             <td width="50"> Buyer</td> <td width="100">  <? echo $buyer_name;?></td>
	             <td width="70"> Order No</td> <td width="100"> <? echo $po_no;?></td>
	             <td width="100"> Style Ref</td> <td width="100"> <? echo $style;?></td>
	             <td width="100"> Garments Item</td> <td> <? echo $item_name;?></td>
	             <td width="50"> SMV</td> <td width="60">  <? echo $set_smv;?></td>
	        </tr>
	        </table>
				<table border="1" class="rpt_table" rules="all" width="1070" cellpadding="0" cellspacing="0">

	                <?

					if($db_type==0)
					{
						$dataArray=sql_select("select TIME_FORMAT( d.prod_start_time, '%H:%i' ) as prod_start_time, TIME_FORMAT( d.prod_start_time, '%H' ) as start_hour, TIME_FORMAT( d.prod_start_time, '%i' ) as start_min from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and d.shift_id=1 and b.pr_date='$prod_date' and a.id='$sewing_line'");
						$prod_start_hour=$dataArray[0][csf('prod_start_time')];
						if($prod_start_hour=="") $prod_start_hour="08:00";
						$start_time=explode(":",$prod_start_hour);
						$hour=$start_time[0]; $minutes=$start_time[1]; $last_hour=23;
						$start_hour_arr=array(); $s=1;

						$start_hour=$prod_start_hour;
						for($j=$hour;$j<$last_hour;$j++)
						{
							$start_hour=add_time($start_hour,60);
							$start_hour_arr[$j+1]=$start_hour;
						}
						$start_hour_arr[$j+1]='23:59:59';

						$sql="SELECT ";
						foreach($start_hour_arr as $val)
						{
							$z++;
							if($s==1)
							{
								$sql.=" sum(case when production_hour<='$val' then production_quantity else 0 end) AS am$z ";
							}
							else
							{
								$sql.=", sum(case when production_hour>'$prev_hour' and production_hour<='$val' then production_quantity else 0 end) AS am$z ";
							}

							$prev_hour=$val;
							$s++;
						}

						$sql.="from pro_garments_production_mst where po_break_down_id in(".$po_ids.") and item_number_id in($item_id) and floor_id='".$floor_id."' and sewing_line='".$sewing_line."' and prod_reso_allo='".$prod_reso_allo."' and production_type=5 and production_date='$prod_date' and is_deleted=0 and status_active=1";
						//echo $sql;die;
						/*$sql="SELECT
							sum(case when production_hour>'00:00' and  production_hour<='01:00' then  production_quantity else 0 end ) AS am1,
							sum(case when production_hour>'01:00' and  production_hour<='02:00' then production_quantity else 0 end ) AS am2,
							sum(case when production_hour>'02:00' and  production_hour<='03:00' then production_quantity else 0 end ) AS am3,
							sum(case when production_hour>'03:00' and  production_hour<='04:00' then production_quantity else 0 end ) AS am4,
							sum(case when production_hour>'04:00' and  production_hour<='05:00' then production_quantity else 0 end ) AS am5,
							sum(case when production_hour>'05:00' and  production_hour<='06:00' then production_quantity else 0 end ) AS am6,
							sum(case when production_hour>'06:00' and  production_hour<='07:00' then production_quantity else 0 end ) AS am7,
							sum(case when production_hour>'07:00' and  production_hour<='08:00' then production_quantity else 0 end ) AS am8,
							sum(case when production_hour>'08:00' and  production_hour<='09:00' then production_quantity else 0 end ) AS am9,
							sum(case when production_hour>'09:00' and  production_hour<='10:00' then production_quantity else 0 end ) AS am10,
							sum(case when production_hour>'10:00' and  production_hour<='11:00' then production_quantity else 0 end ) AS am11,
							sum(case when production_hour>'11:00' and  production_hour<='12:00' then production_quantity else 0 end ) AS pm12,
							sum(case when production_hour>'12:00' and  production_hour<='13:00' then production_quantity else 0 end ) AS pm13,
							sum(case when production_hour>'13:00' and  production_hour<='14:00' then production_quantity else 0 end ) AS pm14,
							sum(case when production_hour>'14:00' and  production_hour<='15:00' then production_quantity else 0 end ) AS pm15,
							sum(case when production_hour>'15:00' and  production_hour<='16:00' then production_quantity else 0 end ) AS pm16,
							sum(case when production_hour>'16:00' and  production_hour<='17:00' then production_quantity else 0 end ) AS pm17,
							sum(case when production_hour>'17:00' and  production_hour<='18:00' then production_quantity else 0 end ) AS pm18,
							sum(case when production_hour>'18:00' and  production_hour<='19:00' then production_quantity else 0 end ) AS pm19,
							sum(case when production_hour>'19:00' and  production_hour<='20:00' then production_quantity else 0 end ) AS pm20,
							sum(case when production_hour>'20:00' and  production_hour<='21:00' then production_quantity else 0 end ) AS pm21,
							sum(case when production_hour>'21:00' and  production_hour<='22:00' then production_quantity else 0 end ) AS pm22,
							sum(case when production_hour>'22:00' and  production_hour<='23:00' then production_quantity else 0 end ) AS pm23,
							sum(case when production_hour>'23:00' and  production_hour<='23:59' then production_quantity else 0 end ) AS pm24

							 from pro_garments_production_mst
							where po_break_down_id in(".$po_id.") and item_number_id='".$item_id."' and location='".$location."' and floor_id='".$floor_id."' and sewing_line='".$sewing_line."' and prod_reso_allo='".$prod_reso_allo."' and production_type='$prod_type' and production_date='$prod_date' and is_deleted=0 and status_active=1";*/
					}
					else
					{

						$dataArray=sql_select("select TO_CHAR(d.prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR(d.prod_start_time,'HH24') as start_hour, TO_CHAR(d.prod_start_time,'MI') as start_min, TO_CHAR(d.lunch_start_time,'HH24:MI') as lunch_start_time from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and d.shift_id=1 and b.pr_date='$prod_date' and a.id='$sewing_line'");
						$prod_start_hour=$dataArray[0][csf('prod_start_time')];
						if($prod_start_hour=="") $prod_start_hour="08:00";
						$start_time=explode(":",$prod_start_hour);
						$hour=$start_time[0]; $minutes=$start_time[1]; $last_hour=23;
						$start_hour_arr=array(); $s=1;

						$start_hour=$prod_start_hour;
						for($j=$hour;$j<$last_hour;$j++)
						{
							$start_hour=add_time($start_hour,60);
							$start_hour_arr[$j+1]=$start_hour;
						}
						$start_hour_arr[$j+1]='23:59:59';

						$sql="SELECT ";
						foreach($start_hour_arr as $val)
						{
							$z++;
							if($s==1)
							{
								$sql.="sum(case when TO_CHAR(production_hour,'HH24:MI:SS')<='$val' then production_quantity else 0 end) AS am$z ";
							}
							else
							{
								$sql.=", sum(case when TO_CHAR(production_hour,'HH24:MI:SS')>'$prev_hour' and TO_CHAR(production_hour,'HH24:MI:SS')<='$val' then production_quantity else 0 end) AS am$z ";
							}

							$prev_hour=$val;
							$s++;
						}

						$sql.="from pro_garments_production_mst where po_break_down_id in(".$po_ids.") and item_number_id in(".$item_id.") and floor_id='".$floor_id."' and sewing_line='".$sewing_line."' and prod_reso_allo='".$prod_reso_allo."' and production_type=5 and production_date='$prod_date' and is_deleted=0 and status_active=1";
						//echo $sql;
					}

					$result=sql_select($sql);
					foreach($result as $row);
					//$total_qnty=$row[csf('am1')]+$row[csf('am2')]+$row[csf('am3')]+$row[csf('am4')]+$row[csf('am5')]+$row[csf('am6')]+$row[csf('am7')]+$row[csf('am8')]+$row[csf('am9')]+$row[csf('am10')]+$row[csf('am11')]+$row[csf('pm12')]+$row[csf('pm13')]+$row[csf('pm14')]+$row[csf('pm15')]+$row[csf('pm16')]+$row[csf('pm17')]+$row[csf('pm18')]+$row[csf('pm19')]+$row[csf('pm20')]+$row[csf('pm21')]+$row[csf('pm22')]+$row[csf('pm23')]+$row[csf('pm24')];
					// bgcolor="#E9F3FF"
					echo '<thead><tr>';
					$x=1;
					foreach($start_hour_arr as $val)
					{
						if($x<13)
						{
							echo '<th width="70">'.$val.'</th>';
							$x++;
						}
					}
					echo '</tr></thead><tr bgcolor="#E9F3FF">';

					$x=1; $total_qnty=0;
					foreach($start_hour_arr as $val)
					{
						if($x<13)
						{
							echo '<td width="70" align="right">'.$row[csf('am'.$x)].'&nbsp;&nbsp;</td>';
							$total_qnty+=$row[csf('am'.$x)];
							$x++;
						}
					}
					echo '</tr>';

					array_splice($start_hour_arr,0, 12);
					$x=13;
					if(count($start_hour_arr)>0)
					{
						echo '<thead><tr>';
						foreach($start_hour_arr as $val)
						{
							echo '<th width="70">'.$val.'</th>';
							$x++;
						}
						$x=13;
						echo '</tr></thead><tr bgcolor="#E9F3FF">';
						foreach($start_hour_arr as $val)
						{
							echo '<td width="70" align="right">'.$row[csf('am'.$x)].'&nbsp;&nbsp;</td>';
							$total_qnty+=$row[csf('am'.$x)];
							$x++;
						}
						echo '</tr>';
					}
					?>
	                <tr><td colspan="12"><strong>Total: &nbsp;&nbsp;<? echo  $total_qnty;?> </strong></td></tr>
				</table>
	            <br>
	            <table border="1" class="rpt_table" rules="all" style="width:auto" cellpadding="0" cellspacing="0">
	            <?
					$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
					$data_file=sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=2");
				?>
	            	<tr>
	                <td width="60">  <b> Image </b></td>
	                 <?
					foreach ($data_array as $row)
					{
					?>
					<td width="150"><a href="<? $row['image_location'] ?>" target="_new"><img src='../../../<? echo $row[csf('image_location')]; ?>' height='120' width='150' align="middle" /></a></td>
					<?
					}
					?>
	                </tr>
	                <tr>
	                <td  width="60"> <b>File </b></td>
	             	  <?
						foreach ($data_file as $row)
						{
						?>
						<td><a href="../../../<? echo $row[csf('image_location')] ?>" target="_new">
							<img src="../../../file_upload/blank_file.png" width="80" height="60"> </a>
						</td>
						<?
						}
						?>
	                </tr>
	            </table>

	        </div>
		</fieldset>
	<?
	exit();
}


if($action=="remarks_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $company_id;
	//$sewing_line=explode("*",$sewing_line);
	//$sewing_line=implode(",",$sewing_line);
	$po_id=explode("*",$po_id);
	$po_id=implode(",",$po_id);
	$sql_line_remark=sql_select("SELECT remarks,production_hour from pro_garments_production_mst where company_id in(".$company_id.") and  floor_id=$floor_id and sewing_line in($sewing_line) and po_break_down_id in($po_id) and production_date='".$prod_date."' and status_active=1 and is_deleted=0 order by production_hour");

	?>
	<fieldset style="width:520px;  ">
		<div id="report_container">

        		<h4>Remarks From Sewing Output</h4>
				<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center">
					<thead>
                    	<tr>
                            <th width="40">SL</th>
                            <th width="460">Remarks</th>
                        </tr>
					</thead>
					<tbody>
					<?
					$i=1;
					foreach($sql_line_remark as $inf)
					{
						 if ($i%2==0)    $bgcolor="#E9F3FF";
						 else            $bgcolor="#FFFFFF";
						 if(trim($inf[csf('remarks')])!="")
						 {
						 ?>
						   <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><? echo $i; ?></td>
							<td align="left"><? echo $inf[csf('remarks')]; ?>&nbsp;</td>
						</tr>
						<?
						$i++;
						 }
					}
					?>
					</tbody>
				</table>
		</div>
	</fieldset>
    <br/>

    <fieldset style="width:520px;  ">
		<div id="report_container">

        		<h4>Remarks From Actual Resource</h4>
				<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center">
					<thead>
                    	<tr>
                            <th width="40">SL</th>
                            <th width="460">Remarks</th>
                        </tr>
					</thead>
					<tbody>
					<?

					$sql_Actual_remarks=sql_select("select d.remarks from prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d where a.id='".$sewing_line."' and a.id=b.mst_id and b.mast_dtl_id =d.mast_dtl_id and b.mst_id=d.mst_id and  a.company_id  in(".$company_id.") and pr_date='".$prod_date."' and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0  ");

					$i=1;
					foreach($sql_Actual_remarks as $inf)
					{
						 if ($i%2==0)    $bgcolor="#E9F3FF";
						 else            $bgcolor="#FFFFFF";
						 if(trim($inf[csf('remarks')])!="")
						 {
							 ?>
							   <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td align="left"><? echo $inf[csf('remarks')]; ?>&nbsp;</td>
							</tr>
							<?
							$i++;
						 }
					}
					?>
					</tbody>
				</table>
		</div>
	</fieldset>

		  <?
}

if($action=="tot_input_output_popup")
{
	echo load_html_head_contents("FOB Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
    $itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$po_number_Arr = return_library_array("select id,po_number from  wo_po_break_down ","id","po_number");

	?>

     <script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			$("#table_body tr:first").hide();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			$("#table_body tr:first").show();
		}
	</script>

    <?php


	if($type==1)
	{
		?>

		<fieldset style="width:1000px; ">
			<div style="width:500px;" align="center">
				<input  type="button" value="Print Preview" onClick="print_window()" style="width:80px"  class="formbutton"/>
			</div>
			<div id="report_container" align="center">
				<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center">
					<caption><strong>Sewing Input Details</strong></caption>
					<thead>
						<th width="30">SL</th>
						<th width="120">Order No</th>

						<th width="100">Input Qty.</th>
					</thead>
					</table>
					<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center" id="table_body">
					<?


					$sql_pop="select  c.po_number,a.po_break_down_id, sum(a.production_quantity)  as good_qnty from pro_garments_production_mst a, wo_po_break_down c where a.production_type=4 and a.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and a.serving_company=".$company_id." and a.po_break_down_id in(".$po_id.")  and a.sewing_line=".$sewing_line." and a.floor_id=".$floor." group by c.po_number,a.po_break_down_id  order by  c.po_number ";

				//and a.production_date='".$production_date."'
				//echo $sql_pop;die;
					$sql_result=sql_select($sql_pop);
					$k=1;$total_amount=0;$total_prod_qty=0;
					foreach($sql_result as $row)
					{
						if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
						<tr style="font:'Arial Narrow';" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
							<td width="30"><? echo $k; ?></td>
							<td width="120" style="word-wrap:break-word; word-break: break-all; text-align:left" ><? echo $row[csf('po_number')]; ?></td>

							<td width="100" style="word-wrap:break-word; word-break: break-all; text-align:right" ><? echo  number_format($row[csf('good_qnty')],2); ?></td>
						</tr>
						<?
						$total_prod_qty+=$row[csf('good_qnty')];
						$k++;
					}
					?>
					<tr class="tbl_bottom" >
					<td colspan="2"> Total </td>

					<td align="right"> <? echo number_format($total_prod_qty,2);?></td>
					</tr>
				</table>

					<?

					$sql_color_size="select  c.color_number_id,c.size_number_id ,a.po_break_down_id, sum(b.production_qnty)  as good_qnty from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.production_type=4 and a.production_type=4 and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and a.serving_company=".$company_id." and a.po_break_down_id in(".$po_id.")  and a.sewing_line=".$sewing_line." and a.floor_id=".$floor."  group by c.color_number_id,c.size_number_id ,a.po_break_down_id  order by  a.po_break_down_id ";

				//echo $sql_color_size;die;
					$size_arr=array();
					$order_color_arr=array();
					$grand_size_arr=array();
					$grand_total=0;
					$color_size_qty_arr=array();
					$sql_color_size_result=sql_select($sql_color_size);
					foreach($sql_color_size_result as $cs_val)
					{
						$size_arr[$cs_val[csf('size_number_id')]]=$cs_val[csf('size_number_id')];
						$color_size_qty_arr[$cs_val[csf('po_break_down_id')]][$cs_val[csf('color_number_id')]][$cs_val[csf('size_number_id')]]+=$cs_val[csf('good_qnty')];

						$order_color_arr[$cs_val[csf('po_break_down_id')]][$cs_val[csf('color_number_id')]]+=$cs_val[csf('good_qnty')];

						$grand_size_arr[$cs_val[csf('size_number_id')]]+=$cs_val[csf('good_qnty')];
						$grand_total+=$cs_val[csf('good_qnty')];
					}

				$input_width=450+count($size_arr)*50;
					?>
					<table border="1" class="rpt_table" rules="all" width="<?php echo $input_width; ?>" cellpadding="0" cellspacing="0" align="center">
					<caption><strong>Sewing Input Color Size Breakdown</strong></caption>
					<thead>
						<th width="30">SL</th>
						<th width="180">Order No</th>
						<th width="150">Color</th>
						<?php
						foreach($size_arr as $sid)
						{
						?>
							<th width="70"><?php echo $itemSizeArr[$sid] ;?></th>
						<?php
						}
						?>
						<th width="100">Color Total</th>
					</thead>
					</table>
					<table border="1" class="rpt_table" rules="all" width="<?php echo $input_width; ?>" cellpadding="0" cellspacing="0" align="center" id="table_body">

					<?php
					$r=1;
					foreach($color_size_qty_arr as $po_id=>$po_value)
					{

						foreach($po_value as $color_id=>$color_value)
						{
							if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
								<tr style="font:'Arial Narrow';" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
									<td width="30" rowspan="<?php // echo $order_color_arr[$po_id]; ?> "><? echo $r; ?></td>
									<td width="180" style="word-wrap:break-word; word-break: break-all; text-align:center" ><? echo $po_number_Arr[$po_id]; ?></td>

									<td width="150" style="word-wrap:break-word; word-break: break-all; text-align:center" ><? echo  $colorArr[$color_id]; ?></td> 			<?php
									foreach($size_arr as $sid)
									{
									?>
										<td width="70" align="right"><?php echo $color_value[$sid] ;?></td>
									<?php
									}
									?>
									<td width="100" align="right"><?php echo $order_color_arr[$po_id][$color_id] ;?></td>
								</tr>
							<?
							$r++;
						}
					}
					?>
					<tr class="tbl_bottom" >
						<td colspan="3"> Total </td>

						<?php
						foreach($size_arr as $sid)
						{
						?>
							<td width="70" align="right"><?php echo $grand_size_arr[$sid] ;?></td>
						<?php
						}
						?>
						<td width="100" align="right"><?php echo $grand_total ;?></td>
					</tr>
				</table>
			</div>
			<script>
			setFilterGrid("table_body",-1);
			</script>
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
		</fieldset>


		<?
	}
	else if($type==2)
	{
		?>

		<fieldset style="width:1000px; ">
			<div style="width:500px;" align="center">
				<input  type="button" value="Print Preview" onClick="print_window()" style="width:80px"  class="formbutton"/>
			</div>
			<div id="report_container" align="center">
				<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center">
					<caption><strong>Sewing Output Details</strong></caption>
					<thead>
						<th width="30">SL</th>
						<th width="120">Order No</th>

						<th width="100">Output Qty.</th>
					</thead>
					</table>
					<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center" id="table_body">
					<?


					$sql_pop="select  c.po_number,a.po_break_down_id, sum(a.production_quantity)  as good_qnty from pro_garments_production_mst a, wo_po_break_down c where a.production_type=5 and a.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and a.serving_company=".$company_id." and a.po_break_down_id in(".$po_id.")  and a.sewing_line=".$sewing_line." and a.floor_id=".$floor." group by c.po_number,a.po_break_down_id  order by  c.po_number ";

					$sql_result=sql_select($sql_pop);
					$k=1;$total_amount=0;$total_prod_qty=0;
					foreach($sql_result as $row)
					{
						if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
						<tr style="font:'Arial Narrow';" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
							<td width="30"><? echo $k; ?></td>
							<td width="120" style="word-wrap:break-word; word-break: break-all; text-align:left" ><? echo $row[csf('po_number')]; ?></td>

							<td width="100" style="word-wrap:break-word; word-break: break-all; text-align:right" ><? echo  number_format($row[csf('good_qnty')],2); ?></td>
						</tr>
						<?
						$total_prod_qty+=$row[csf('good_qnty')];
						$k++;
					}
					?>
					<tr class="tbl_bottom" >
					<td colspan="2"> Total </td>

					<td align="right"> <? echo number_format($total_prod_qty,2);?></td>
					</tr>
				</table>
					<?

					$sql_color_size="select  c.color_number_id,c.size_number_id ,a.po_break_down_id, sum(b.production_qnty)  as good_qnty from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.production_type=5 and a.production_type=5 and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and a.serving_company=".$company_id." and a.po_break_down_id in(".$po_id.")  and a.sewing_line=".$sewing_line." and a.floor_id=".$floor."  group by c.color_number_id,c.size_number_id ,a.po_break_down_id  order by  a.po_break_down_id ";

				//echo $sql_color_size;
					$size_arr=array();
					$order_color_arr=array();
					$grand_size_arr=array();
					$grand_total=0;
					$color_size_qty_arr=array();
					$sql_color_size_result=sql_select($sql_color_size);
					foreach($sql_color_size_result as $cs_val)
					{
						$size_arr[$cs_val[csf('size_number_id')]]=$cs_val[csf('size_number_id')];
						$color_size_qty_arr[$cs_val[csf('po_break_down_id')]][$cs_val[csf('color_number_id')]][$cs_val[csf('size_number_id')]]+=$cs_val[csf('good_qnty')];

						$order_color_arr[$cs_val[csf('po_break_down_id')]][$cs_val[csf('color_number_id')]]+=$cs_val[csf('good_qnty')];

						$grand_size_arr[$cs_val[csf('size_number_id')]]+=$cs_val[csf('good_qnty')];
						$grand_total+=$cs_val[csf('good_qnty')];
					}

				$input_width=450+count($size_arr)*50;
					?>
					<table border="1" class="rpt_table" rules="all" width="<?php echo $input_width; ?>" cellpadding="0" cellspacing="0" align="center">
					<caption><strong>Sewing Output Color Size Breakdown</strong></caption>
					<thead>
						<th width="30">SL</th>
						<th width="180">Order No</th>
						<th width="150">Color</th>
						<?php
						foreach($size_arr as $sid)
						{
						?>
							<th width="70"><?php echo $itemSizeArr[$sid] ;?></th>
						<?php
						}
						?>
						<th width="100">Color Total</th>
					</thead>
					</table>
					<table border="1" class="rpt_table" rules="all" width="<?php echo $input_width; ?>" cellpadding="0" cellspacing="0" align="center" id="table_body">

					<?php
					$r=1;
					foreach($color_size_qty_arr as $po_id=>$po_value)
					{

						foreach($po_value as $color_id=>$color_value)
						{
							if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
								<tr style="font:'Arial Narrow';" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
									<td width="30" rowspan="<?php // echo $order_color_arr[$po_id]; ?> "><? echo $r; ?></td>
									<td width="180" style="word-wrap:break-word; word-break: break-all; text-align:center" ><? echo $po_number_Arr[$po_id]; ?></td>

									<td width="150" style="word-wrap:break-word; word-break: break-all; text-align:center" ><? echo  $colorArr[$color_id]; ?></td> 			<?php
									foreach($size_arr as $sid)
									{
									?>
										<td width="70" align="right"><?php echo $color_value[$sid] ;?></td>
									<?php
									}
									?>
									<td width="100" align="right"><?php echo $order_color_arr[$po_id][$color_id] ;?></td>
								</tr>
							<?
							$r++;
						}
					}
					?>
					<tr class="tbl_bottom" >
						<td colspan="3"> Total </td>

						<?php
						foreach($size_arr as $sid)
						{
						?>
							<td width="70" align="right"><?php echo $grand_size_arr[$sid] ;?></td>
						<?php
						}
						?>
						<td width="100" align="right"><?php echo $grand_total ;?></td>
					</tr>
				</table>
			</div>
			<script>
			setFilterGrid("table_body",-1);
			</script>
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
		</fieldset>


		<?
	}
	else if($type==3)
	{
		?>

		<fieldset style="width:1000px; ">
			<div style="width:500px;" align="center">
				<input  type="button" value="Print Preview" onClick="print_window()" style="width:80px"  class="formbutton"/>
			</div>
			<div id="report_container" align="center">
				<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center">
					<caption><strong>Sewing Input Output Details </strong></caption>
					<thead>
						<th width="30">SL</th>
						<th width="120">Order No</th>
						<th width="100">Input Qty.</th>
						<th width="100">Output Qty.</th>
						<th width="100">Balance Qty.</th>
					</thead>
					</table>
					<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center" id="table_body">
					<?


					$sql_pop="select  c.po_number,a.po_break_down_id, sum( case when a.production_type=4 THEN a.production_quantity ELSE 0 END)  as input_qty ,sum( case when a.production_type=5 THEN a.production_quantity ELSE 0 END)  as output_qty from pro_garments_production_mst a, wo_po_break_down c where a.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and a.serving_company=".$company_id." and a.po_break_down_id in(".$po_id.")  and a.sewing_line=".$sewing_line." and a.floor_id=".$floor."  group by c.po_number,a.po_break_down_id  order by  c.po_number ";
				//echo $sql_pop;die;
					$sql_result=sql_select($sql_pop);
					$k=1;$total_amount=0;$total_prod_qty=0;
					foreach($sql_result as $row)
					{
						if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
						<tr style="font:'Arial Narrow';" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
							<td width="30"><? echo $k; ?></td>
							<td width="120" style="word-wrap:break-word; word-break: break-all; text-align:left" ><? echo $row[csf('po_number')]; ?></td>

							<td width="100" align="right"><? echo  number_format($row[csf('input_qty')],2); ?></td>
							<td width="100" align="right"><? echo  number_format($row[csf('output_qty')],2); ?></td>
							<td width="100" align="right"><? echo  number_format($row[csf('input_qty')]-$row[csf('output_qty')],2); ?></td>
						</tr>
						<?
						$total_input_qty+=$row[csf('input_qty')];
						$total_output_qty+=$row[csf('output_qty')];
						$k++;
					}
					?>
					<tr class="tbl_bottom" >
					<td colspan="2"> Total </td>

					<td align="right"> <? echo number_format($total_input_qty,2);?></td>
					<td align="right"> <? echo number_format($total_output_qty,2);?></td>
					<td align="right"> <? echo number_format($total_input_qty-$total_output_qty,2);?></td>
					</tr>
				</table>

					<?

					$sql_color_size="select  a.production_type,c.color_number_id,c.size_number_id ,a.po_break_down_id, b.production_qnty  as good_qnty from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.production_type in (4,5) and a.production_type in (4,5) and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and a.serving_company=".$company_id." and a.po_break_down_id in(".$po_id.")  and a.sewing_line=".$sewing_line." and a.floor_id=".$floor."   order by  a.po_break_down_id ";

				//echo $sql_color_size;
					$size_arr=array();
					$order_color_arr=array();
					$grand_size_arr=array();
					$grand_total=array();
					$color_size_qty_arr=array();
					$sql_color_size_result=sql_select($sql_color_size);
					foreach($sql_color_size_result as $cs_val)
					{
						$size_arr[$cs_val[csf('size_number_id')]]=$cs_val[csf('size_number_id')];
						$color_size_qty_arr[$cs_val[csf('po_break_down_id')]][$cs_val[csf('color_number_id')]][$cs_val[csf('size_number_id')]][$cs_val[csf('production_type')]]+=$cs_val[csf('good_qnty')];

						$order_color_arr[$cs_val[csf('po_break_down_id')]][$cs_val[csf('color_number_id')]][$cs_val[csf('production_type')]]+=$cs_val[csf('good_qnty')];

						$grand_size_arr[$cs_val[csf('size_number_id')]][$cs_val[csf('production_type')]]+=$cs_val[csf('good_qnty')];
						$grand_total[$cs_val[csf('production_type')]]+=$cs_val[csf('good_qnty')];
					}

				$input_width=460+count($size_arr)*150;
					?>
					<table border="1" class="rpt_table" rules="all" width="<?php echo $input_width; ?>" cellpadding="0" cellspacing="0" align="center">
					<caption><strong>Sewing Input Output Color Size Breakdown</strong></caption>
					<thead>
						<tr>
							<th width="30" rowspan="2">SL</th>
							<th width="150" rowspan="2">Order No</th>
							<th width="120" rowspan="2">Color</th>
							<?php
							foreach($size_arr as $sid)
							{
							?>
								<th width="150" colspan="3"><?php echo $itemSizeArr[$sid] ;?></th>
							<?php
							}
							?>
							<th width="150" colspan="3">Color Total</th>
						</tr>

						<tr>
							<?php
							foreach($size_arr as $sid)
							{
							?>
								<th width="50">Input</th>
								<th width="50">Output</th>
								<th width="50">Balance</th>
							<?php
							}
							?>
							<th width="50">Input</th>
							<th width="50">Output</th>
							<th width="50">Balance</th>
						</tr>
					</thead>
					</table>
					<table border="1" class="rpt_table" rules="all" width="<?php echo $input_width; ?>" cellpadding="0" cellspacing="0" align="center" id="table_body">

					<?php
					$r=1;
					foreach($color_size_qty_arr as $po_id=>$po_value)
					{

						foreach($po_value as $color_id=>$color_value)
						{
							if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
								<tr style="font:'Arial Narrow';" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
									<td width="30" rowspan="<?php // echo $order_color_arr[$po_id]; ?> "><? echo $r; ?></td>
									<td width="150" style="word-wrap:break-word; word-break: break-all; text-align:center" ><? echo $po_number_Arr[$po_id]; ?></td>

									<td width="120" style="word-wrap:break-word; word-break: break-all; text-align:center" ><? echo  $colorArr[$color_id]; ?></td> 			<?php
									foreach($size_arr as $sid)
									{
									?>
										<td width="50" align="right"><?php echo $color_value[$sid][4] ;?></td>
										<td width="50" align="right"><?php echo $color_value[$sid][5] ;?></td>
										<td width="50" align="right"><?php echo $color_value[$sid][4]-$color_value[$sid][5] ;?></td>
									<?php
									}
									?>
									<td width="50" align="right"><?php echo $order_color_arr[$po_id][$color_id][4] ;?></td>
									<td width="50" align="right"><?php echo $order_color_arr[$po_id][$color_id][5] ;?></td>
									<td width="50" align="right"><?php echo $order_color_arr[$po_id][$color_id][4]-$order_color_arr[$po_id][$color_id][5] ;?></td>
								</tr>
							<?
							$r++;
						}
					}
					?>
					<tr class="tbl_bottom" >
						<td colspan="3"> Total </td>

						<?php
						foreach($size_arr as $sid)
						{
						?>
							<td width="50" align="right"><?php echo $grand_size_arr[$sid][4] ;?></td>
							<td width="50" align="right"><?php echo $grand_size_arr[$sid][5] ;?></td>
							<td width="50" align="right"><?php echo $grand_size_arr[$sid][4]-$grand_size_arr[$sid][5];?></td>
						<?php
						}
						?>
						<td width="50" align="right"><?php echo $grand_total[4] ;?></td>
						<td width="50" align="right"><?php echo $grand_total[5] ;?></td>
						<td width="50" align="right"><?php echo $grand_total[4]-$grand_total[5] ;?></td>
					</tr>
				</table>
			</div>
			<script>
			setFilterGrid("table_body",-1);
			</script>
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
		</fieldset>


		<?
	}
	if($type==4)
	{
		?>

		<fieldset style="width:1000px; ">
			<div style="width:500px;" align="center">
				<input  type="button" value="Print Preview" onClick="print_window()" style="width:80px"  class="formbutton"/>
			</div>
			<div id="report_container" align="center">
				<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center">
					<caption><strong>Sewing Input Details</strong></caption>
					<thead>
						<th width="30">SL</th>
						<th width="120">Order No</th>

						<th width="100">Input Qty.</th>
					</thead>
					</table>
					<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center" id="table_body">
					<?


					$sql_pop="select  c.po_number,a.po_break_down_id, sum(a.production_quantity)  as good_qnty from pro_garments_production_mst a, wo_po_break_down c where a.production_type=4 and a.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and a.serving_company=".$company_id." and a.po_break_down_id in(".$po_id.") and a.production_date='".$production_date."' and a.sewing_line=".$sewing_line." and a.floor_id=".$floor." group by c.po_number,a.po_break_down_id  order by  c.po_number ";

				//and a.production_date='".$production_date."'
				//echo $sql_pop;die;
					$sql_result=sql_select($sql_pop);
					$k=1;$total_amount=0;$total_prod_qty=0;
					foreach($sql_result as $row)
					{
						if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
						<tr style="font:'Arial Narrow';" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
							<td width="30"><? echo $k; ?></td>
							<td width="120" style="word-wrap:break-word; word-break: break-all; text-align:left" ><? echo $row[csf('po_number')]; ?></td>

							<td width="100" style="word-wrap:break-word; word-break: break-all; text-align:right" ><? echo  number_format($row[csf('good_qnty')],2); ?></td>
						</tr>
						<?
						$total_prod_qty+=$row[csf('good_qnty')];
						$k++;
					}
					?>
					<tr class="tbl_bottom" >
					<td colspan="2"> Total </td>

					<td align="right"> <? echo number_format($total_prod_qty,2);?></td>
					</tr>
				</table>

					<?


					$sql_color_size="select  c.color_number_id,c.size_number_id ,a.po_break_down_id, sum(b.production_qnty)  as good_qnty from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.production_type=4 and a.production_type=4 and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and a.serving_company=".$company_id." and a.po_break_down_id in(".$po_id.") and a.production_date='".$production_date."'  and a.sewing_line=".$sewing_line." and a.floor_id=".$floor."  group by c.color_number_id,c.size_number_id ,a.po_break_down_id  order by  a.po_break_down_id ";

				//echo $sql_color_size;die;
					$size_arr=array();
					$order_color_arr=array();
					$grand_size_arr=array();
					$grand_total=0;
					$color_size_qty_arr=array();
					$sql_color_size_result=sql_select($sql_color_size);
					foreach($sql_color_size_result as $cs_val)
					{
						$size_arr[$cs_val[csf('size_number_id')]]=$cs_val[csf('size_number_id')];
						$color_size_qty_arr[$cs_val[csf('po_break_down_id')]][$cs_val[csf('color_number_id')]][$cs_val[csf('size_number_id')]]+=$cs_val[csf('good_qnty')];

						$order_color_arr[$cs_val[csf('po_break_down_id')]][$cs_val[csf('color_number_id')]]+=$cs_val[csf('good_qnty')];

						$grand_size_arr[$cs_val[csf('size_number_id')]]+=$cs_val[csf('good_qnty')];
						$grand_total+=$cs_val[csf('good_qnty')];
					}

				$input_width=450+count($size_arr)*50;
					?>
					<table border="1" class="rpt_table" rules="all" width="<?php echo $input_width; ?>" cellpadding="0" cellspacing="0" align="center">
					<caption><strong>Sewing Input Color Size Breakdown</strong></caption>
					<thead>
						<th width="30">SL</th>
						<th width="180">Order No</th>
						<th width="150">Color</th>
						<?php
						foreach($size_arr as $sid)
						{
						?>
							<th width="70"><?php echo $itemSizeArr[$sid] ;?></th>
						<?php
						}
						?>
						<th width="100">Color Total</th>
					</thead>
					</table>
					<table border="1" class="rpt_table" rules="all" width="<?php echo $input_width; ?>" cellpadding="0" cellspacing="0" align="center" id="table_body">

					<?php
					$r=1;
					foreach($color_size_qty_arr as $po_id=>$po_value)
					{

						foreach($po_value as $color_id=>$color_value)
						{
							if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
								<tr style="font:'Arial Narrow';" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
									<td width="30" rowspan="<?php // echo $order_color_arr[$po_id]; ?> "><? echo $r; ?></td>
									<td width="180" style="word-wrap:break-word; word-break: break-all; text-align:center" ><? echo $po_number_Arr[$po_id]; ?></td>

									<td width="150" style="word-wrap:break-word; word-break: break-all; text-align:center" ><? echo  $colorArr[$color_id]; ?></td> 			<?php
									foreach($size_arr as $sid)
									{
									?>
										<td width="70" align="right"><?php echo $color_value[$sid] ;?></td>
									<?php
									}
									?>
									<td width="100" align="right"><?php echo $order_color_arr[$po_id][$color_id] ;?></td>
								</tr>
							<?
							$r++;
						}
					}
					?>
					<tr class="tbl_bottom" >
						<td colspan="3"> Total </td>

						<?php
						foreach($size_arr as $sid)
						{
						?>
							<td width="70" align="right"><?php echo $grand_size_arr[$sid] ;?></td>
						<?php
						}
						?>
						<td width="100" align="right"><?php echo $grand_total ;?></td>
					</tr>
				</table>
			</div>
			<script>
			setFilterGrid("table_body",-1);
			</script>
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
		</fieldset>


		<?
	}
	else if($type==5)
	{
		?>

		<fieldset style="width:1000px; ">
			<div style="width:500px;" align="center">
				<input  type="button" value="Print Preview" onClick="print_window()" style="width:80px"  class="formbutton"/>
			</div>
			<div id="report_container" align="center">
				<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center">
					<caption><strong>Sewing Output Details</strong></caption>
					<thead>
						<th width="30">SL</th>
						<th width="120">Order No</th>

						<th width="100">Output Qty.</th>
					</thead>
					</table>
					<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center" id="table_body">
					<?


					$sql_pop="select  c.po_number,a.po_break_down_id, sum(a.production_quantity)  as good_qnty from pro_garments_production_mst a, wo_po_break_down c where a.production_type=5 and a.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and a.serving_company=".$company_id." and a.po_break_down_id in(".$po_id.")  and a.sewing_line=".$sewing_line." and a.production_date='".$production_date."' and a.floor_id=".$floor." group by c.po_number,a.po_break_down_id  order by  c.po_number ";

					$sql_result=sql_select($sql_pop);
					$k=1;$total_amount=0;$total_prod_qty=0;
					foreach($sql_result as $row)
					{
						if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
						<tr style="font:'Arial Narrow';" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
							<td width="30"><? echo $k; ?></td>
							<td width="120" style="word-wrap:break-word; word-break: break-all; text-align:left" ><? echo $row[csf('po_number')]; ?></td>

							<td width="100" style="word-wrap:break-word; word-break: break-all; text-align:right" ><? echo  number_format($row[csf('good_qnty')],2); ?></td>
						</tr>
						<?
						$total_prod_qty+=$row[csf('good_qnty')];
						$k++;
					}
					?>
					<tr class="tbl_bottom" >
					<td colspan="2"> Total </td>

					<td align="right"> <? echo number_format($total_prod_qty,2);?></td>
					</tr>
				</table>
					<?


					$sql_color_size="select  c.color_number_id,c.size_number_id ,a.po_break_down_id, sum(b.production_qnty)  as good_qnty from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.production_type=5 and a.production_type=5 and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and a.serving_company=".$company_id." and a.po_break_down_id in(".$po_id.") and a.production_date='".$production_date."' and a.sewing_line=".$sewing_line." and a.floor_id=".$floor."  group by c.color_number_id,c.size_number_id ,a.po_break_down_id  order by  a.po_break_down_id ";

				//echo $sql_color_size;
					$size_arr=array();
					$order_color_arr=array();
					$grand_size_arr=array();
					$grand_total=0;
					$color_size_qty_arr=array();
					$sql_color_size_result=sql_select($sql_color_size);
					foreach($sql_color_size_result as $cs_val)
					{
						$size_arr[$cs_val[csf('size_number_id')]]=$cs_val[csf('size_number_id')];
						$color_size_qty_arr[$cs_val[csf('po_break_down_id')]][$cs_val[csf('color_number_id')]][$cs_val[csf('size_number_id')]]+=$cs_val[csf('good_qnty')];

						$order_color_arr[$cs_val[csf('po_break_down_id')]][$cs_val[csf('color_number_id')]]+=$cs_val[csf('good_qnty')];

						$grand_size_arr[$cs_val[csf('size_number_id')]]+=$cs_val[csf('good_qnty')];
						$grand_total+=$cs_val[csf('good_qnty')];
					}

				$input_width=450+count($size_arr)*50;
					?>
					<table border="1" class="rpt_table" rules="all" width="<?php echo $input_width; ?>" cellpadding="0" cellspacing="0" align="center">
					<caption><strong>Sewing Output Color Size Breakdown</strong></caption>
					<thead>
						<th width="30">SL</th>
						<th width="180">Order No</th>
						<th width="150">Color</th>
						<?php
						foreach($size_arr as $sid)
						{
						?>
							<th width="70"><?php echo $itemSizeArr[$sid] ;?></th>
						<?php
						}
						?>
						<th width="100">Color Total</th>
					</thead>
					</table>
					<table border="1" class="rpt_table" rules="all" width="<?php echo $input_width; ?>" cellpadding="0" cellspacing="0" align="center" id="table_body">

					<?php
					$r=1;
					foreach($color_size_qty_arr as $po_id=>$po_value)
					{

						foreach($po_value as $color_id=>$color_value)
						{
							if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
								<tr style="font:'Arial Narrow';" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
									<td width="30" rowspan="<?php // echo $order_color_arr[$po_id]; ?> "><? echo $r; ?></td>
									<td width="180" style="word-wrap:break-word; word-break: break-all; text-align:center" ><? echo $po_number_Arr[$po_id]; ?></td>

									<td width="150" style="word-wrap:break-word; word-break: break-all; text-align:center" ><? echo  $colorArr[$color_id]; ?></td> 			<?php
									foreach($size_arr as $sid)
									{
									?>
										<td width="70" align="right"><?php echo $color_value[$sid] ;?></td>
									<?php
									}
									?>
									<td width="100" align="right"><?php echo $order_color_arr[$po_id][$color_id] ;?></td>
								</tr>
							<?
							$r++;
						}
					}
					?>
					<tr class="tbl_bottom" >
						<td colspan="3"> Total </td>

						<?php
						foreach($size_arr as $sid)
						{
						?>
							<td width="70" align="right"><?php echo $grand_size_arr[$sid] ;?></td>
						<?php
						}
						?>
						<td width="100" align="right"><?php echo $grand_total ;?></td>
					</tr>
				</table>
			</div>
			<script>
			setFilterGrid("table_body",-1);
			</script>
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
		</fieldset>


		<?
	}
	exit();
}


if($action=="smv_adjust_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$sql="SELECT c.MAN_POWER,c.WORKING_HOUR from PROD_RESOURCE_MST a,PROD_RESOURCE_DTLS_MAST b, PROD_RESOURCE_DTLS c where a.id=b.mst_id and b.id=c.MAST_DTL_ID and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and c.pr_date='$prod_date' and a.id=$line_id";
	// echo $sql;
	$res = sql_select($sql);

	$sql2 = "SELECT NUMBER_OF_EMP ,ADJUST_HOUR from PROD_RESOURCE_SMV_ADJ where ADJUSTMENT_SOURCE=1 and PR_DATE='$prod_date' and IS_DELETED=0 and STATUS_ACTIVE=1 and MST_ID=$line_id";
	$res2 = sql_select($sql2);


	?>
	<fieldset style="width:320px;  ">
		<div id="report_container">

        		<h4>SMV Adjustment Details</h4>
				<table border="1" class="rpt_table" rules="all" width="300" cellpadding="0" cellspacing="0" align="center">
					<thead>
                    	<tr>
                            <th width="60"></th>
                            <th width="80">Man Power</th>
                            <th width="80">Working Hour</th>
                            <th width="80">Available Minutes</th>
                        </tr>
					</thead>
					<tbody>
					<?
					$i=1;
					$avl_min = 0;
					$man_power = 0;
					foreach($res as $v)
					{
						 if ($i%2==0)    $bgcolor="#E9F3FF";
						 else            $bgcolor="#FFFFFF";

						 ?>
						   <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="60">Main</td>
							<td align="right"><? echo $v['MAN_POWER']; ?>&nbsp;</td>
							<td align="right"><? echo $v['WORKING_HOUR']; ?>&nbsp;</td>
							<td align="right"><? echo number_format(($v['MAN_POWER']*$v['WORKING_HOUR']*60),2); ?>&nbsp;</td>
						</tr>
						<?
						$i++;
						$avl_min += $v['MAN_POWER']*$v['WORKING_HOUR']*60;
						$man_power += $v['MAN_POWER'];
					}
					foreach($res2 as $v)
					{
						 if ($i%2==0)    $bgcolor="#E9F3FF";
						 else            $bgcolor="#FFFFFF";

						 ?>
						   <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="60">Extra Hours</td>
							<td align="right"><? echo $v['NUMBER_OF_EMP']; ?>&nbsp;</td>
							<td align="right"><? echo $v['ADJUST_HOUR']; ?>&nbsp;</td>
							<td align="right"><? echo number_format(($v['NUMBER_OF_EMP']*$v['ADJUST_HOUR']*60),2); ?>&nbsp;</td>
						</tr>
						<?
						$i++;
						$avl_min += $v['NUMBER_OF_EMP']*$v['ADJUST_HOUR']*60;
					}
					?>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="2">Actual </th>
							<th title="Available Minutes/Main Man Power/60"><?=number_format(($avl_min/$man_power/60),2);?></th>
							<th><?=number_format($avl_min,2);?></th>
						</tr>
					</tfoot>
				</table>
		</div>
	</fieldset>

	<?
}
?>