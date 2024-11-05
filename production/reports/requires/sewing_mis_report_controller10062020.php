<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 140, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data' 
	order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/sewing_mis_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/sewing_mis_report_controller' );",0 ); 
	exit();    	 
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_id", 160, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=5 
	and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );     	 	
	exit();    	 
}
if ($action == "eval_multi_select") {
    echo "set_multiselect('cbo_floor_id','0','0','','0');\n";
    exit();
}

if($action=="open_selection_popup")
{	
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
	<table cellspacing="0" width="200"  border="1" rules="all" class="rpt_table" align="center" >
		<thead>
			<th width="200">Report Copies</th>
		</thead>
		<tbody>
			<tr>
				<td width="200"><? echo create_drop_down( "cbo_copy_type", 200, array(1=>"One Copy",2=>"Four Copies"),"", 0, "-- Select Floor --", "", "" ); ?></td>
            </tr>
            <tr>
                <td width="200" align="center"><input type="button" name="search1" id="search1" value="Close" onClick="parent.emailwindow.hide()" style="width:60px" class="formbutton" /></td>
             </tr>
		</tbody>
	</table>
	<?php
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
		if( $floor_id!=0 ) $cond.= " and a.floor_id in($floor_id)";
		
		$line_sql="select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id $data_format and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number";
		//echo $line_sql;
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

if($action=="report_generate_month") 
{
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
	
	$cbo_no_prod_type=str_replace("'","",$cbo_no_prod_type);
	$txt_item_catgory=str_replace("'","",$txt_item_catgory);
	
	$production_date_min = strtotime($txt_producting_day);
	
	$date = new DateTime($txt_producting_day);
	$date->modify('FIRST DAY OF -1 MONTH');
	$last_month_first_date=$date->format('Y-m-d');
	$last_month_last_date=$date->format('Y-m-t');
	
	$current_month_first_date=date("Y-m-01",$production_date_min);
	
	if($db_type==0)
	{
		$last_month_start_date=change_date_format($last_month_first_date,"yyyy-mm-dd");
		$current_month_start_date=change_date_format($current_month_first_date,"yyyy-mm-dd");
		$last_month_end_date=change_date_format($last_month_last_date,"yyyy-mm-dd");
	}
	else
	{
		$last_month_start_date=change_date_format($last_month_first_date,"yyyy-mm-dd","-",1);
		$current_month_start_date=change_date_format($current_month_first_date,"yyyy-mm-dd","-",1);
		$last_month_end_date=change_date_format($last_month_last_date,"yyyy-mm-dd","-",1);
	}
	
	$lineDataArr = sql_select("select id, line_name, sewing_group, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1 order by sewing_line_serial"); 
	$poly_line_arr=array();
	foreach($lineDataArr as $lRow)
	{
		$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
		$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
		$lastSlNo=$lRow[csf('sewing_line_serial')];
		if($lRow[csf('sewing_group')]=="Poly")
		{
			$poly_line_arr[$lRow[csf('id')]]=$lRow[csf('id')];
		}
	}
	//print_r($poly_line_arr);die;
	
	if($db_type==0)
	{
		$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$comapny_id and shift_id=1 and pr_date between '".$last_month_start_date."' and $txt_date and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");	
	}
	else
	{
		$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$comapny_id and shift_id=1 and pr_date between '".$last_month_start_date."' and $txt_date and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
	}
	
	if($min_shif_start=="")
	{
		echo "<p style='font-size:20px', align='center'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<p/>";die;
	}
	
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
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_id and variable_list=23 and is_deleted=0 and status_active=1");
	
	if(str_replace("'","",$cbo_company_id)==0) $company_name=""; else $company_name="and a.serving_company=".str_replace("'","",$cbo_company_id)."";
	
	if(str_replace("'","",$cbo_location_id)==0) 
	{
		$subcon_location=""; $location=""; $location_cond='';
	}
	else 
	{
		$location_cond=" and a.location_id=".str_replace("'","",$cbo_location_id)."";
		$location=" and a.location=".str_replace("'","",$cbo_location_id)."";
		$subcon_location=" and a.location_id=".str_replace("'","",$cbo_location_id)."";
	}
	$cbo_floor_id=str_replace("'","",$cbo_floor_id);
	if($cbo_floor_id=="") $floor=""; else $floor="and a.floor_id in(".$cbo_floor_id.")";
    if(str_replace("'","",$hidden_line_id)==0)
	{ 
		$line=""; $subcon_line=""; $line_cond='';
	}
	else 
	{
		$subcon_line="and a.line_id in(".str_replace("'","",$hidden_line_id).")";
		$line="and a.sewing_line in(".str_replace("'","",$hidden_line_id).")";
		$line_cond="and a.line_number in(".str_replace("'","",$hidden_line_id).")";
	}

	if($db_type==0)
	{
		$manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
	}
	else
	{
		$manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
	}
	 $sql_item="select a.buyer_name, a.style_ref_no, b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost,c.smv_set from wo_po_details_master a,wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.company_name in($manufacturing_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
	// echo $sql_item;die;
	$resultItem=sql_select($sql_item);
	
	foreach($resultItem as $itemData)
	{
		$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
		$po_buyer_arr[$itemData[csf('id')]]=$itemData[csf('buyer_name')];
		$po_style_arr[$itemData[csf('id')]]=$itemData[csf('style_ref_no')];
	}
	
	if($prod_reso_allo[0]==1)
	{
		$prod_resource_array=array();
		
		$dataArray_sql=sql_select("select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, c.target_efficiency, b.helper, b.smv_adjust, b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour, c.from_date, c.to_date, c.capacity, sum(d.target_per_line) as target_per_line, sum(d.operator) as active_operator, sum(d.helper) as active_helper, d.po_id, d.gmts_item_id  from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c left join prod_resource_color_size d on (d.dtls_id=c.id and d.mst_id=c.mst_id) where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id=$comapny_id and b.pr_date between '".$last_month_start_date."' and $txt_date and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $location_cond $floor group by a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, c.target_efficiency, b.helper, b.smv_adjust, b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour, c.from_date, c.to_date, c.capacity, d.po_id, d.gmts_item_id ");// between '".$last_month_start_date."' and  
		foreach($dataArray_sql as $val)
		{
			$poly_line=0;
			$line_ids_arr=explode(",",$val[csf('line_number')]);
			foreach($line_ids_arr as $line_id_single)
			{
				if($poly_line==0)
				{
					if(in_array($line_id_single,$poly_line_arr)) { $poly_line=1;}
				}
			}
			if($poly_line==0)
			{
				$val[csf('pr_date')]=date("d-M-Y",strtotime($val[csf('pr_date')]));
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['man_power']=$val[csf('man_power')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['operator']=$val[csf('active_operator')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['helper']=$val[csf('active_helper')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['terget_hour']=$val[csf('target_per_hour')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['working_hour']=$val[csf('working_hour')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['tpd']=$val[csf('target_per_hour')]*$val[csf('working_hour')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_start']=$val[csf('from_date')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_end']=$val[csf('to_date')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['capacity']=$val[csf('capacity')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['active_machine']=$val[csf('active_operator')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['line_chief']=$val[csf('line_chief')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust']=$val[csf('smv_adjust')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['target_efficiency']=$val[csf('target_efficiency')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['line_chief']=$val[csf('line_chief')];
				
				$sewing_line_id=$val[csf('line_number')];
			
				if($lineSerialArr[$sewing_line_id]=="")
				{
					$lastSlNo++;
					$slNo=$lastSlNo;
					$lineSerialArr[$sewing_line_id]=$slNo;
				}
				else $slNo=$lineSerialArr[$sewing_line_id];

				$production_data_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]]['serial']=$slNo;
				$production_serial_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('line_chief')]][$slNo][$val[csf('id')]]=$val[csf('id')];
				
				if($production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('id')]]['po_id']!="")
				{
					$production_data_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]][$val[csf('po_id')]]['po_id'].=",".$val[csf('po_id')];
					$production_data_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]]['style_ref'].=",".$po_style_arr[$val[csf('po_id')]]; 
				}
				else
				{
					$production_data_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]]['po_id']=$val[csf('po_id')];	
					$production_data_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]]['style_ref']=$po_style_arr[$val[csf('po_id')]];
				}
				
				if($production_data_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]]['item_number_id']!="")
				{
					$production_data_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]]['item_number_id'].="#".$val[csf('po_id')]."**".$val[csf('gmts_item_id')]."**".$val[csf('target_per_line')]; 
				}
				else
				{
					 $production_data_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]]['item_number_id']=$val[csf('po_id')]."**".$val[csf('gmts_item_id')]."**".$val[csf('target_per_line')]; 
				}
				//$po_wise_target_per_line[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]][$val[csf('gmts_item_id')]][$val[csf('po_id')]]+=$val[csf('target_per_line')]; 
				
				if( $production_data_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]]['buyer_name']!="")
				{
					 $production_data_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]]['buyer_name'].=",".$po_buyer_arr[$val[csf('po_id')]]; 
				}
				else
				{
					 $production_data_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]]['buyer_name']=$po_buyer_arr[$val[csf('po_id')]]; 
				}
				
				$production_data_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]]['target_per_line']+=$val[csf('target_per_line')];
				$production_data_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]]['prod_reso_allo']=1;
				$production_data_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]]['type_line']=2;
			} 
		}

		$sql_query=sql_select("select b.mst_id, b.pr_date, b.number_of_emp, b.adjust_hour from prod_resource_mst a, prod_resource_smv_adj b where a.id=b.mst_id  and a.company_id=$comapny_id and b.pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and b.adjustment_source=1  $location_cond  $floor ");
		foreach($sql_query as $val)
		{
			$val[csf('pr_date')]=date("d-M-Y",strtotime($val[csf('pr_date')]));
			$prod_resource_array[$val[csf('mst_id')]][$val[csf('pr_date')]]['number_of_emp']+=$val[csf('number_of_emp')];
			$prod_resource_array[$val[csf('mst_id')]][$val[csf('pr_date')]]['adjust_hour']+=$val[csf('adjust_hour')];
		}
		
		//echo "<pre>";
//print_r($production_data_arr);die;

		
		if($db_type==0)
		{
			$dataArray=sql_select("select a.id, b.pr_date, d.shift_id, TIME_FORMAT( d.prod_start_time, '%H:%i' ) as prod_start_time, TIME_FORMAT( d.lunch_start_time, '%H:%i' ) as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 and b.pr_date between '".$last_month_start_date."' and  $txt_date"); 
		}
		else
		{
			$dataArray=sql_select("select a.id, b.pr_date, d.shift_id, TO_CHAR(d.prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR( d.lunch_start_time,'HH24:MI') as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 and b.pr_date between '".$last_month_start_date."' and  $txt_date");
		}
		
		$line_number_arr=array();
		foreach($dataArray as $val)
		{
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['shift_id']=$val[csf('shift_id')];
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['prod_start_time']=$val[csf('prod_start_time')];
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['lunch_start_time']=$val[csf('lunch_start_time')];
		}
	}
	
	if($db_type==0) $prod_start_cond="prod_start_time";
	else if($db_type==2) $prod_start_cond="TO_CHAR(prod_start_time,'DD-MON-YYYY HH24:MI')";
	
	$variable_start_time_arr='';
	$prod_start_time=sql_select("select $prod_start_cond as prod_start_time from variable_settings_production where company_name=$cbo_company_id and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");

	foreach($prod_start_time as $row)
	{
		$ex_time=explode(" ",$row[csf('prod_start_time')]);
		$variable_start_time_arr=$ex_time[1];
	}

	unset($prod_start_time);
	$current_date_time=date('d-m-Y H:i');
	$variable_date=change_date_format(str_replace("'","",$txt_date)).' '.$variable_start_time_arr;
	$datediff=datediff("n",$variable_date,$current_date_time);
	
	$ex_date_time=explode(" ",$current_date_time);
	$current_date=$ex_date_time[0];
	$current_time=$ex_date_time[1];
	$ex_time=explode(":",$current_time);
	
	$search_prod_date=change_date_format(str_replace("'","",$txt_date));
	$current_eff_min=($ex_time[0]*60)+$ex_time[1];
	$variable_time= explode(":",$variable_start_time_arr);
	$vari_min=($variable_time[0]*60)+$variable_time[1];
	$difa_time=explode(".",number_format(($current_eff_min-$vari_min)/60,2));//datediff("",$ctime,$variable_start_time_arr);
	$dif_time=number_format($datediff/60,2);
	$dif_hour_min=date("H:i", strtotime($dif_time));
 
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
	
	$product_category_cond='';
	if($txt_item_catgory!=0) $product_category_cond=" and b.product_category=".$txt_item_catgory."";
	
	$i=1; $grand_total_good=0; $grand_alter_good=0; $grand_total_reject=0;
	$html="";
	$floor_html="";
    $check_arr=array();
	
	if($db_type==0)
	{
		$sql="select  a.company_id, a.location, a.floor_id,d.floor_serial_no,e.sewing_line_serial, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name  as buyer_name, b.style_ref_no, a.po_break_down_id, a.item_number_id, c.po_number as po_number, c.unit_price, c.file_no, c.grouping as ref, sum(a.production_quantity) as good_qnty, sum(IFNULL(a.alter_qnty,0)) as alter_qty, sum(IFNULL(a.reject_qnty,0)) as reject_qty, sum(IFNULL(a.spot_qnty,0)) as spot_qty,"; 
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
		$sql.="sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23 from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a left join lib_prod_floor d on a.floor_id=d.id and d.is_deleted=0 left join lib_sewing_line e on a.sewing_line=e.id and e.is_deleted=0  where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $company_name $location $floor $line  and   a.production_date between '".$last_month_start_date."'  and  $txt_date $product_category_cond group by a.company_id, a.location, a.floor_id, a.po_break_down_id, a.prod_reso_allo, a.production_date, a.sewing_line, e.sewing_line_serial, b.buyer_name, a.item_number_id, c.po_number, c.file_no, c.unit_price, c.grouping, b.style_ref_no order by a.location, a.floor_id, e.sewing_line_serial, a.prod_reso_allo";
	}
	else if($db_type==2)
	{
		$sql="select a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name as buyer_name, b.style_ref_no, a.po_break_down_id, a.item_number_id, c.po_number as po_number, c.file_no, c.unit_price, c.grouping as ref, sum(a.production_quantity) as good_qnty, sum(nvl(a.alter_qnty,0)) as alter_qty, sum(nvl(a.reject_qnty,0)) as reject_qty, sum(nvl(a.spot_qnty,0)) as spot_qty,"; 
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
		
		$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23 from wo_po_details_master b, wo_po_break_down c, pro_garments_production_mst a where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $company_name $location $floor $line and a.production_date between '".$last_month_start_date."' and $txt_date $product_category_cond group by a.company_id, a.location, a.floor_id, a.po_break_down_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name, b.style_ref_no, a.item_number_id, c.po_number, c.unit_price, c.file_no, c.grouping order by a.location, a.floor_id, a.sewing_line";
	}
	//echo $sql;die;
	$sql_resqlt=sql_select($sql);
	$production_po_data_arr=array();
	$reso_line_ids=''; $all_po_id="";
	foreach($sql_resqlt as $val)
	{
		$poly_line=0;
		$val[csf('production_date')]=date("d-M-Y",strtotime($val[csf('production_date')]));
		$prodDate=$val[csf('production_date')];
		$poId=$val[csf('po_break_down_id')];
		if($val[csf('prod_reso_allo')]==1)
		{
			$sewing_line_id=$prod_reso_arr[$val[csf('sewing_line')]];
			$reso_line_ids.=$val[csf('sewing_line')].',';
			$line_ids_arr=explode(",",$prod_reso_arr[$val[csf('sewing_line')]]);
			foreach($line_ids_arr as $line_id_single)
			{
				if($poly_line==0)
				{
					if(in_array($line_id_single,$poly_line_arr)) { $poly_line=1;}
				}
			}
		}
		else
		{
			$sewing_line_id=$val[csf('sewing_line')];
			if(in_array($val[csf('sewing_line')],$poly_line_arr)) { $poly_line=1;}
		}
		
		if($poly_line==0)
		{
			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id]=$slNo;
			}
			else $slNo=$lineSerialArr[$sewing_line_id];
			//echo $prod_resource_array[$val[csf('sewing_line')]][$prodDate]['line_chief']."**".$val[csf('pr_date')]."**".$val[csf('sewing_line')];die;
			$production_serial_arr[$prodDate][$val[csf('floor_id')]][$prod_resource_array[$val[csf('sewing_line')]][$prodDate]['line_chief']][$slNo][$val[csf('sewing_line')]]=$val[csf('sewing_line')];
			
			$line_start=$line_number_arr[$val[csf('sewing_line')]][$prodDate]['prod_start_time'];
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
				if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
				{
					if($h>=$line_start_hour && $h<=$actual_time)
					{
						$production_po_data_arr[$prodDate][$val[csf('floor_id')]][$val[csf('sewing_line')]][$poId]+=$val[csf($prod_hour)];
						$production_data_arr[$prodDate][$val[csf('floor_id')]][$val[csf('sewing_line')]][$prod_hour]+=$val[csf($prod_hour)];  
					} 	
				}
				
				if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date)) 
				{	
					$production_po_data_arr[$prodDate][$val[csf('floor_id')]][$val[csf('sewing_line')]][$poId]+=$val[csf($prod_hour)];
					$production_data_arr[$prodDate][$val[csf('floor_id')]][$val[csf('sewing_line')]][$prod_hour]+=$val[csf($prod_hour)]; 
				}
			}
			
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
			{	
				if( $h>=$line_start_hour && $h<=$actual_time)
				{
					//echo $h."#".$actual_time."**";die;
					$production_po_data_arr[$prodDate][$val[csf('floor_id')]][$val[csf('sewing_line')]][$poId]+=$val[csf('prod_hour23')]; 
					$production_data_arr[$prodDate][$val[csf('floor_id')]][$val[csf('sewing_line')]][$prod_hour]+=$val[csf('prod_hour23')];     
				} 	
			}
			
			$production_data_arr[$prodDate][$val[csf('floor_id')]][$val[csf('sewing_line')]]['prod_reso_allo']=$val[csf('prod_reso_allo')];
			$production_data_arr[$prodDate][$val[csf('floor_id')]][$val[csf('sewing_line')]]['serial']=$slNo;  
			
			if($production_data_arr[$prodDate][$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']!="")
			{
				$production_data_arr[$prodDate][$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name'].=",".$val[csf('buyer_name')]; 
			}
			else
			{
				$production_data_arr[$prodDate][$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']=$val[csf('buyer_name')]; 
			}
		
			if($production_data_arr[$prodDate][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_id']!="")
			{
				$production_data_arr[$prodDate][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number'].=",".$val[csf('po_number')];
				$production_data_arr[$prodDate][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_id'].=",".$poId;
				$production_data_arr[$prodDate][$val[csf('floor_id')]][$val[csf('sewing_line')]]['style_ref'].=",".$val[csf('style_ref_no')];
				$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['file'].=",".$val[csf('file_no')];
				$production_data_arr[$prodDate][$val[csf('floor_id')]][$val[csf('sewing_line')]]['ref'].=",".$val[csf('ref')]; 
			}
			else
			{
				$production_data_arr[$prodDate][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']=$val[csf('po_number')];
				$production_data_arr[$prodDate][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_id']=$poId; 
				$production_data_arr[$prodDate][$val[csf('floor_id')]][$val[csf('sewing_line')]]['style_ref']=$val[csf('style_ref_no')]; 
				$production_data_arr[$prodDate][$val[csf('floor_id')]][$val[csf('sewing_line')]]['file']=$val[csf('file_no')]; 
				$production_data_arr[$prodDate][$val[csf('floor_id')]][$val[csf('sewing_line')]]['ref']=$val[csf('ref')]; 
			}
			$production_data_arr[$prodDate][$val[csf('floor_id')]][$val[csf('sewing_line')]]['type_line']=1; 
			$fob_rate_data_arr[$prodDate][$val[csf('floor_id')]][$val[csf('sewing_line')]][$poId]['rate']=$val[csf('unit_price')]; 
			
			if($production_data_arr[$prodDate][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']!="")
			{
				$production_data_arr[$prodDate][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id'].="#".$poId."**".$val[csf('item_number_id')]; 
			}
			else
			{
				 $production_data_arr[$prodDate][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']=$poId."**".$val[csf('item_number_id')]; 
			}
			$production_data_arr[$prodDate][$val[csf('floor_id')]][$val[csf('sewing_line')]]['quantity']+=$val[csf('good_qnty')];
			$production_data_arr[$prodDate][$val[csf('floor_id')]][$val[csf('sewing_line')]]['reject_qty']+=$val[csf('reject_qty')]+$val[csf('alter_qty')]+$val[csf('spot_qty')];
			$production_data_arr_qty[$prodDate][$val[csf('floor_id')]][$val[csf('sewing_line')]][$poId][$val[csf('item_number_id')]]['quantity']+=$val[csf('good_qnty')];
			
			if($all_po_id=="") $all_po_id=$poId; else $all_po_id.=",".$poId;
		}
	}

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
				$poIds_cond.=" b.id  in($ids) or ";
			}
			$poIds_cond=chop($poIds_cond,'or ');
			$poIds_cond.=")";
		}
		else
		{
			$poIds_cond=" and  b.id  in($all_po_id)";
		}
	}
		
	$sql_item_rate="select b.id, c.item_number_id, c.order_quantity, c.order_total from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where b.job_no_mst=a.job_no and b.id=c.po_break_down_id and b.job_no_mst=c.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1";
	$resultRate=sql_select($sql_item_rate);
	$item_po_array=array();
	foreach($resultRate as $row)
	{
		$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['qty']+=$row[csf('order_quantity')];
		$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['amt']+=$row[csf('order_total')];
	}
	
	//Subcoutact Data ************************************************************************************************
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
   		$sql_sub_contuct.="sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.id and d.is_deleted=0 left join lib_sewing_line e on a.line_id=e.id and e.is_deleted=0 where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id=$comapny_id and   a.production_date between '".$last_month_start_date."' and  $txt_date  $subcon_location $floor $subcon_line  group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date,a.prod_reso_allo,a.line_id,b.party_id,c.order_no,c.cust_style_ref,e.sewing_line_serial order by a.location_id,d.floor_serial_no,e.sewing_line_serial,a.prod_reso_allo";
	}
	else
	{
		$sql_sub_contuct= "select  a.company_id, a.location_id, a.floor_id, a.production_date, a.line_id,b.party_id  as buyer_name,a.prod_reso_allo,a.order_id,c.order_no as po_number,c.cust_style_ref,max(c.smv) as smv,a.prod_reso_allo,sum(a.production_qnty) as good_qnty,"; 
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
		
	   	$sql_sub_contuct.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]'	and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a  where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id=$comapny_id  and a.production_date between '".$last_month_start_date."' and  $txt_date $subcon_location $floor $subcon_line  group by a.company_id, a.location_id, a.floor_id,a.order_id, a.production_date, a.line_id,b.party_id,c.order_no,c.cust_style_ref,a.prod_reso_allo order by a.location_id, a.floor_id,a.prod_reso_allo";
		
	}
	$subcon_order_smv=array();		
	foreach($sub_result as $subcon_val)
	{
		$subcon_val[csf('production_date')]=date("d-M-Y",strtotime($subcon_val[csf('production_date')]));
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
		$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['serial']=$slNo;
		$production_serial_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$prod_resource_array[$subcon_val[csf('sewing_line')]][$subcon_val[csf('production_date')]]['line_chief']][$slNo][$subcon_val[csf('sewing_line')]]=$subcon_val[csf('sewing_line')];
		
		$production_po_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$subcon_val[csf('good_qnty')];
		if($production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['buyer_name']!="")
		{
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['buyer_name'].=",".$subcon_val[csf('buyer_name')]; 
		}
		else
		{
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['buyer_name']=$subcon_val[csf('buyer_name')]; 
		}
	
		if($production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number']!="")
		{
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number'].=",".$subcon_val[csf('po_number')];
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['style_ref'].=",".$subcon_val[csf('cust_style_ref')];  
		}
		else
		{
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number']=$subcon_val[csf('po_number')]; 
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['style_ref']=$subcon_val[csf('cust_style_ref')]; 
		}
	
		if($production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id']!="")
		{
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id'].=",".$subcon_val[csf('order_id')]; 
		}
		else
		{
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id'].=$subcon_val[csf('order_id')]; 
		}
		$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['type_line']=1; 
		$subcon_order_smv[$subcon_val[csf('order_id')]]=$subcon_val[csf('smv')];
		$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['quantity']+=$subcon_val[csf('good_qnty')];
		
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
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$prod_hour]+=$subcon_val[csf($prod_hour)]; 
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
		$production_data_arr[$subcon_val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('line_id')]]['prod_hour23']+=$val[csf('prod_hour23')];
	}
	
	//Subcon End-----------------------------------
	
	$total_sewing_input=return_field_value("sum(a.production_quantity) as good_qnty","wo_po_details_master b, wo_po_break_down c, pro_garments_production_mst a"," a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.production_type=4 and a.status_active=1 and a.is_deleted=0  and a.company_id=$comapny_id  and   a.production_date between '".$current_month_start_date."' and $txt_date $location $floor $line $buyer_id_cond $file_cond $ref_cond $product_category_cond and  a.status_active=1 and a.is_deleted=0 ","good_qnty");
	
	$exfactory_qty=return_field_value("sum(a.ex_factory_qnty) as ex_factory_qnty","pro_ex_factory_mst a,pro_ex_factory_delivery_mst b"," b.id=a.delivery_mst_id and b.company_id =$comapny_id and a.ex_factory_date between '".$last_month_start_date."' and  '".$last_month_end_date."'  and a.status_active=1 and a.is_deleted=0","ex_factory_qnty");
	
	$txt_date=str_replace("'","",$txt_date);
	
	$avable_min=0; $today_product=0; $floor_name=""; $floor_man_power=0; $floor_operator=$floor_produc_min=0;
	$floor_smv=$floor_row=$floor_helper=$floor_tgt_h=$floor_days_run=$floor_working_hour=$line_floor_production=$floor_today_product=$floor_avale_minute=0;
	$total_operator=$total_helper=$gnd_hit_rate=0; $total_smv=$total_terget=$grand_total_product=$gnd_line_effi=0; $total_man_power=$gnd_avable_min=$gnd_product_min=0;
	$item_smv=$item_smv_total=$line_efficiency=$days_run=$total_working_hour=$gnd_total_tgt_h=$total_capacity=0; $smv_for_item=""; $line_floor_production=0;  $line_total_production=0; $gnd_total_fob_val=0; $floor_serial_no=0; $line_cheaf_serial=0;
	$j=0;
	
	$line_number_check_arr=array(); $total_production=array(); $floor_production=array(); $graph_line_arr=array(); $graph_line_reject_arr=array(); $floor_serial_data_arr=array(); $line_cheaf_serial_data_arr=array();
	//echo "<pre>";
	//print_r($production_serial_arr);die;
	foreach($production_serial_arr[$txt_date] as $f_id=>$fname)
	{
		ksort($fname); 
		$floor_line_num=0;
		$floot_line_total=0;
		
		foreach($fname as $temp_data)
		{
			$floot_line_total+=count($temp_data);
		}
		//echo $floot_line_total;
		foreach($fname as $line_cheaf=>$line_cheaf_data)
		{
		//echo $line_cheaf; 
		//print_r($line_cheaf_data);die;
			ksort($line_cheaf_data); 
			$line_cheaf_total_row=0;
			$line_cheaf_total_row=count($line_cheaf_data);
			//echo "pp".$line_cheaf_total_row."pp";
			$line_cheaf_row_count=0;
			foreach($line_cheaf_data as $sl=>$s_data)
			{
				$line_cheaf_row_count++;
				
				foreach($s_data as $l_id=>$ldata)
				{
					if($i!=1)
					{
						if(!in_array($f_id, $check_arr))
						{
							// firest content============================================================================
							 $html.='<tr  class="tbl_bottom">
									<td width="40">&nbsp;</td>
									<td width="100">Floor Total</td>
									<td width="80">&nbsp;</td>
									<td width="60" align="center">'.number_format($floor_produc_min/$line_floor_production,2).'</td>
									<td width="60" align="center">'.$floor_action_machine.'</td>
									<td width="60" align="center">'.$floor_operator.'</td>
									<td width="60" align="center">'.$floor_helper.'</td>
									<td width="60" align="center">'.$floor_man_power.'</td>
									<td width="60" align="center">'.$floor_working_hour.'</td>
									<td align="center" width="60">'.$floor_extra_worker.'</td>
									<td align="center" width="60">'.$floor_extra_hour.'</td>
									
									<td align="center" width="100">'.$floor_avale_minute.'</td>
									<td width="80"></td>
									<td width="100"></td>
									<td width="100"></td>
									<td width="70"></td>
									<td width="70">'.number_format(($floor_target_efficiency*100)/$floor_avale_minute,2).'%</td>
									<td align="center" width="70">'.$floor_tgt_h.'</td>
									<td align="center" width="80">'.$eff_target_floor.'</td>';
								
							$gnd_total_fob_val=0;
							for($k=$hour; $k<=$last_hour; $k++)
							{
								$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
								if($start_hour_arr[$k]==$global_start_lanch) $bg_color='background:yellow';
								
								if($floor_tgt_h>$floor_production[$prod_hour])
								{
									$bg_color='background:red';
									if($floor_production[$prod_hour]==0) $bg_color='';
								}
								else $bg_color='';
								$html.='<td align="center" width="50" style='.$bg_color.' >'. $floor_production[$prod_hour].'</td>';
							}
								
							$avarage_production_per_floor=$line_floor_production/$floor_working_hour;
							$html.='<td align="center" width="80">'. $line_floor_production.'</td>
									<td align="center" width="80">'. $floor_sewing_output.'</td>
									<td align="center" width="80">'.number_format($floor_efficency,2).' %</td>
									<td align="center" width="60">'.number_format($floor_target_efficiency,0).'</td>
									<td align="center" width="100">'. number_format($floor_produc_min,0).'</td>
									<td align="center" width="70"></td>
									<td align="center" width="70">'. number_format(($line_floor_production/$eff_target_floor)*100,2).'%</td>
									<td align="center" width="70"></td>
									<td align="center" width="80"></td>
									<td align="center" width=""></td>';
							$html.='</tr>';
								
							$floor_serial_data_arr[$floor_serial_no]=number_format($floor_efficency,2);
							
							$floor_line_num=0; $floor_name=""; $floor_smv=0; $floor_row=0; $floor_operator=0; $floor_helper=0; $floor_tgt_h=0; $floor_man_power=0; $floor_days_run=0; $eff_target_floor=0;
							unset($floor_production);
							$floor_working_hour=0; $line_floor_production=0; $floor_today_product=0; $floor_avale_minute=0; $floor_produc_min=0; $floor_efficency=0; $floor_man_power=0; $floor_capacity=0; $floor_sewing_input=0; $floor_action_machine=0; $floor_target_efficiency=0; $floor_extra_hour=0; $floor_extra_worker=0; $floor_target_efficiency_manual=0;
							$j=0;
						}
					}
				
					$j++;
					$floor_line_num++;
					$germents_item_row=array();
					//echo $production_data_arr[$txt_date][$f_id][$ldata]['item_number_id'];die;
					$item_pos=explode(",",$production_data_arr[$txt_date][$f_id][$ldata]['po_id']);
					$germents_item_row=array_unique(explode('#',$production_data_arr[$txt_date][$f_id][$ldata]['item_number_id']));
					$germents_item=array();
					foreach($germents_item_row as $gps)
					{
						$po_garment_item=explode('**',$gps);
						$germents_item[$po_garment_item[1]][$po_garment_item[0]]+=$po_garment_item[2];
					}
								
					$buyer_neme_all=array_unique(explode(',',$production_data_arr[$txt_date][$f_id][$ldata]['buyer_name']));
					$buyer_name="";
					foreach($buyer_neme_all as $buy)
					{
						if($buyer_name!='') $buyer_name.=',';
						$buyer_name.=$buyerArr[$buy];
					}
					$garment_itemname=''; $item_smv=""; $smv_for_item=""; $produce_minit=""; $order_no_total=""; $efficiency_min=0; $efficiency_min_summary=0;$po_wise_target_min=0;
					
					$index='';
					//if($i==14) {print_r($germents_item);die;}
					//echo "<pre>";
					//print_r($germents_item);die;
					$item_id_arr=array(); $garment_item_arr=array();
					foreach($germents_item as $gmt_itme_id=>$g_val)
					{
						foreach($g_val as $gmt_po_id=>$po_val)
						{
							$item_id_arr[$gmt_itme_id]=$gmt_itme_id;
							$garment_item_arr[$gmt_itme_id]=$garments_item[$gmt_itme_id];
							
							$index=$gmt_itme_id."_".$gmt_po_id;
							if($item_smv_array[$gmt_po_id][$gmt_itme_id]!="")
							{
								if($item_smv!='') $item_smv.='/';
								$item_smv.=$item_smv_array[$gmt_po_id][$gmt_itme_id];
								$po_wise_target_min+=($item_smv_array[$gmt_po_id][$gmt_itme_id]*1)*$po_val;
							}
							if($gmt_po_id!="")
							{
								if($order_no_total!="") $order_no_total.=",";
								$order_no_total.=$gmt_po_id;
							}
							if($smv_for_item!="") $smv_for_item.=",".$gmt_po_id."**".$item_smv_array[$gmt_po_id][$gmt_itme_id];
							else
							$smv_for_item=$gmt_po_id."**".$item_smv_array[$gmt_po_id][$gmt_itme_id];
							if(!in_array($index,$check_index_duplicate))
							{	
								$produce_minit+=$production_po_data_arr[$txt_date][$f_id][$l_id][$gmt_po_id]*$item_smv_array[$gmt_po_id][$gmt_itme_id];
							}
							$check_index_duplicate[]=$index;
						}
					}
					$item_id=implode(",",$item_id_arr);
					unset($check_index_duplicate);
					//die;
					$subcon_po_id=array_unique(explode(',',$production_data_arr[$txt_date][$f_id][$ldata]['order_id']));
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
						$produce_minit+=$production_po_data_arr[$txt_date][$f_id][$l_id][$sub_val]*$subcon_order_smv[$sub_val];
						if($subcon_order_id!="") $subcon_order_id.=",";
						$subcon_order_id.=$sub_val;
					}
		
					$days_run=0;$line_production_hour=0;$sewing_output=0;
					if($order_no_total!="")
					{
						$sewing_output=return_field_value("sum(a.production_quantity) as good_qnty ","pro_garments_production_mst a ","a.production_type=5 and a.sewing_line=$ldata and a.status_active=1 and a.is_deleted=0  and  a.po_break_down_id in(".$order_no_total.")","good_qnty");
	
						$day_run_sql=sql_select("select min(production_date) as min_date,sum(production_quantity) as sewing_input from pro_garments_production_mst
						where po_break_down_id in(".$order_no_total.")  and production_type=5 and sewing_line=$ldata and status_active=1 and is_deleted=0");
						foreach($day_run_sql as $row_run)
						{
							$sewing_day=$row_run[csf('min_date')];
							$sewing_input=$row_run[csf('sewing_input')];
						}
						if($sewing_day!="")
						{
							$days_run=datediff("d",$sewing_day,$txt_date);
						}
						else  $days_run=0;
					}
					$type_line=$production_data_arr[$txt_date][$f_id][$ldata]['type_line'];
					//$prod_reso_allo=$line_data['prod_reso_allo'];
					//echo $production_data_arr[$txt_date][$f_id][$ldata]['prod_reso_allo'];die;
					$sewing_line='';
					$poly_line=0;
					if($production_data_arr[$txt_date][$f_id][$ldata]['prod_reso_allo']==1)
					{
						
						$line_number=explode(",",$prod_reso_arr[$ldata]);
						foreach($line_number as $val)
						{
							if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
							if($poly_line==0)
							{
								if(in_array($val,$poly_line_arr)) { $poly_line=1;}
								
							}
						}
					}
					else 
					{
						$sewing_line=$lineArr[$ldata];
						if(in_array($ldata,$poly_line_arr)) { $poly_line=1;}
					}
							
					//if($poly_line==1){ echo $sewing_line."**";die;}
					//************************************************************************************************************
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
						 //***********************************************************************************			  
					$production_hour=array();
					for($h=$hour;$h<=$last_hour;$h++)
					{
						 $prod_hour="prod_hour".substr($line_start_hour_arr[$h],0,2).""; 
						 $production_hour[$prod_hour]=$production_data_arr[$txt_date][$f_id][$ldata][$prod_hour];
						 $floor_production[$prod_hour]+=$production_data_arr[$txt_date][$f_id][$ldata][$prod_hour];
						 $total_production[$prod_hour]+=$production_data_arr[$txt_date][$f_id][$ldata][$prod_hour];
					}
							
							
					$floor_production['prod_hour24']+=$production_data_arr[$txt_date][$f_id][$ldata]['prod_hour23'];
					$total_production['prod_hour24']+=$production_data_arr[$txt_date][$f_id][$ldata]['prod_hour23'];
					$production_hour['prod_hour24']=$production_data_arr[$txt_date][$f_id][$ldata]['prod_hour23']; 
					
					if(str_replace("'","",$actual_production_date)==str_replace("'","",$actual_date)) 
					{
						if($type_line==2) //No Profuction Line
						{
							$line_start=$production_data_arr[$txt_date][$f_id][$ldata]['prod_start_time'];
						}
						else
						{
							$line_start=$line_number_arr[$ldata][$txt_date]['prod_start_time'];
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
								$line_production_hour+=$production_data_arr[$txt_date][$f_id][$ldata][$line_hour];
								$line_floor_production+=$production_data_arr[$txt_date][$f_id][$ldata][$line_hour];
								$line_total_production+=$production_data_arr[$txt_date][$f_id][$ldata][$line_hour];
								$actual_time_hour=$start_hour_arr[$lh+1];
							}
						}
						
						if($start_hour_arr[$actual_time]>$lunch_start_hour) $total_eff_hour=$total_eff_hour-1;
						
						if($type_line==2)
						{
							if($total_eff_hour>$line_data['working_hour'])
							{
								 $total_eff_hour=$line_data['working_hour'];
							}
						}
						else
						{
							if($total_eff_hour>$prod_resource_array[$ldata][$txt_date]['working_hour'])
							{
								$total_eff_hour=$prod_resource_array[$ldata][$txt_date]['working_hour'];
							}
						}
					}
					if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date)) 
					{
						for($ah=$hour;$ah<=$last_hour;$ah++)
						{
							$prod_hour="prod_hour".substr($start_hour_arr[$ah],0,2).""; 
							$line_production_hour+=$production_data_arr[$txt_date][$f_id][$ldata][$prod_hour];
							$line_floor_production+=$production_data_arr[$txt_date][$f_id][$ldata][$prod_hour];
							$line_total_production+=$production_data_arr[$txt_date][$f_id][$ldata][$prod_hour];
						}
						
						$total_eff_hour=$prod_resource_array[$ldata][$txt_date]['working_hour'];	
					}
					
					if($sewing_day!="")
					{
						$days_run= $diff=datediff("d",$sewing_day,$txt_date);
					}
					else  $days_run=0;
					//******************************* line effiecency***********************************************['']
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
					
					
					$smv_adjustmet_type=$prod_resource_array[$ldata][$txt_date]['smv_adjust_type'];
					
					$target_efficiency=0;
					if($po_wise_target_min>0)
					{
						//echo $po_wise_target_min;
						//echo "**".$total_eff_hour;
						$target_efficiency=($po_wise_target_min*$total_eff_hour);
					}
					//die;
					
					$target_efficiency_manual=$prod_resource_array[$ldata][$txt_date]['target_efficiency'];
					
					$eff_target=($prod_resource_array[$ldata][$txt_date]['terget_hour']*$total_eff_hour);
					
					if($total_eff_hour>=$prod_resource_array[$ldata][$txt_date]['working_hour'])
					{
						if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array[$ldata][$txt_date]['smv_adjust'];
						if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array[$ldata][$txt_date]['smv_adjust'])*(-1);
					}
					
					$efficiency_min+=$total_adjustment+($prod_resource_array[$ldata][$txt_date]['man_power'])*$cla_cur_time*60;
					$line_efficiency=(($produce_minit)*100)/$efficiency_min;
					
						//****************************************************************************************************************
			//if($poly_line==0)
			//{		
							
					$extra_hour=$prod_resource_array[$ldata][$txt_date]['adjust_hour'];	
					$extra_worker=$prod_resource_array[$ldata][$txt_date]['number_of_emp'];	
					$action_machine=$prod_resource_array[$ldata][$txt_date]['active_machine'];	
					//$man_power=$prod_resource_array[$ldata][$txt_date]['man_power'];	
					
					$operator=$prod_resource_array[$ldata][$txt_date]['operator'];
					$helper=$prod_resource_array[$ldata][$txt_date]['helper'];
					$man_power=$operator+$helper;
					$terget_hour=$prod_resource_array[$ldata][$txt_date]['terget_hour'];	
					$capacity=$prod_resource_array[$ldata][$txt_date]['capacity'];
					
					$working_hour=$prod_resource_array[$ldata][$txt_date]['working_hour'];
					
					$floor_capacity+=$prod_resource_array[$ldata][$txt_date]['capacity'];
					//$floor_man_power+=$prod_resource_array[$ldata][$txt_date]['man_power'];
					$floor_man_power+=$man_power;
					$floor_operator+=$prod_resource_array[$ldata][$txt_date]['operator'];
					$floor_helper+=$prod_resource_array[$ldata][$txt_date]['helper'];
					$floor_tgt_h+=$prod_resource_array[$ldata][$txt_date]['terget_hour'];	
					$floor_working_hour+=$prod_resource_array[$ldata][$txt_date]['working_hour']; 
					$floor_action_machine+=$action_machine;
					$eff_target_floor+=$eff_target;
					$floor_today_product+=$today_product;
					$floor_avale_minute+=$efficiency_min;
					$floor_produc_min+=$produce_minit; 
					$floor_extra_hour+=$extra_hour;
					$floor_extra_worker+=$extra_worker;
					$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
					
					$total_operator+=$prod_resource_array[$ldata][$txt_date]['operator'];
					//$total_man_power+=$prod_resource_array[$ldata][$txt_date]['man_power'];
					$total_man_power+=$man_power;
					$total_helper+=$prod_resource_array[$ldata][$txt_date]['helper'];
					$total_capacity+=$prod_resource_array[$ldata][$txt_date]['capacity'];
					$total_working_hour+=$prod_resource_array[$ldata][$txt_date]['working_hour']; 
					$gnd_total_tgt_h+=$prod_resource_array[$ldata][$txt_date]['terget_hour'];
					$grand_extra_hour+=$extra_hour;
					$grand_extra_worker+=$extra_worker;
					$total_terget+=$eff_target;
					$grand_total_product+=$today_product;
					$gnd_avable_min+=$efficiency_min;
					$gnd_product_min+=$produce_minit;
					$gnd_total_fob_val+=$fob_val;
					$gnd_active_machine+=$action_machine; 
				
					$po_id=rtrim($production_data_arr[$txt_date][$f_id][$ldata]['po_id'],',');
					$po_id=array_unique(explode(",",$po_id));
					$style=rtrim($production_data_arr[$txt_date][$f_id][$ldata]['style_ref'],',');
				
					$style=implode(",",array_unique(explode(",",$style)));
					$po_id=$production_data_arr[$txt_date][$f_id][$ldata]['po_id'];
				
				
					$floor_target_efficiency_manual+=$target_efficiency_manual;
					$grand_target_efficiency_manual+=$target_efficiency_manual;
					$floor_target_efficiency+=$target_efficiency;
					$grand_target_efficiency+=$target_efficiency;
					$cbo_get_upto=str_replace("'","",$cbo_get_upto);
					$txt_parcentage=str_replace("'","",$txt_parcentage);
				   //********************************* calclution floor total    *******************$txt_date],$sewing_day
					$floor_name=$floorArr[$f_id];	
					$floor_smv+=$item_smv;
	
					$floor_days_run+=$days_run;
					$floor_sewing_input+=$sewing_input;
					$grand_sewing_input+=$sewing_input;
					$grand_sewing_output+=$sewing_output;
					$floor_sewing_output+=$sewing_output;
					
					$styles=explode(",",$style);
					$style_button='';//
					foreach($styles as $sid)
					{
						$style_button.="<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$ldata."','".$f_id."','".$item_id."','".$prod_reso_allo."','".$txt_date."','show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
					
					}
					
							
					if($line_efficiency<=$txt_parcentage) $efficiency_color="#FF0000"; else $efficiency_color="#FFFFFF";
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
					$html.='<td width="40" align="center">'.$i.'&nbsp;</td>
							<td width="100" align="center">'.$floor_name.'&nbsp; </td>
							<td align="center" width="80" >'. $sewing_line.'&nbsp; </td>
							<td align="center" width="60"><p>'.$item_smv.'</p></td>
							<td align="center" width="60">'.$action_machine.'</td>
							<td align="center" width="60">'.$operator.'</td>
							<td align="center" width="60">'.$helper.'</td>
							<td align="center" width="60">'.$man_power.'</td>
							<td width="60" align="center">'.$working_hour.'</td>
							<td width="60" align="center">'.$extra_worker.'</td>
							<td width="60" align="center">'.$extra_hour.'</td>
							<td align="center" width="100">'.$efficiency_min.'</td>
							<td width="80" align="center"><p>'.$buyer_name.'&nbsp;</p></td>
							
							<td width="100" align="center"><p>'.$style_button.'&nbsp;</p></td>
							<td width="100" align="center"><p>'.implode(",",$garment_item_arr).'&nbsp;</p></td>
							<td align="center" width="70">'.change_date_format($sewing_day).'</td>
							<td align="center" width="70">'.number_format(($target_efficiency_manual),2).'%</td>
							<td align="center" width="70">'. $terget_hour.'</td>
							<td align="center" width="80">'. $eff_target.'</td>';
							$tday_line_qc_pass=0;
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
								$html.='<td align="center" width="50"  style='.$bg_color.'>'.$production_hour[$prod_hour].'</td>';
							}
									
							$avarage_production_per_line=$line_production_hour/$working_hour;
							$html.='<td align="center" width="80"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$ldata.",'tot_prod','".$smv_for_item."','".$actual_time_hour."','".$line_start."','".$txt_date."'".')">'.$line_production_hour.'</a></td>
								<td align="center" width="80">'.$sewing_output.'</td>';
								if($line_efficiency<=$txt_parcentage)
								{
									$html.='<td align="center" width="80" bgcolor="red">'.number_format($line_efficiency,2).'%</td>';
								}
								else
								{
									$html.='<td align="center" width="80">'.number_format($line_efficiency,2).'%</td>'; 
								}
								$html.='<td align="center" width="60" >'.number_format($target_efficiency,0).'</td>
										<td width="100" align="center"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$ldata.",'tot_prod','".$smv_for_item."','".$actual_time_hour."','".$line_start."','".$txt_date."'".')">'.number_format($produce_minit,0).'</a></td>
										<td align="center" width="70" >'. $days_run.'</td>
										<td align="center" width="70" >'. number_format(($line_production_hour/$eff_target)*100,2).'%</td>';
								if($line_cheaf_row_count==1)
								{
									$line_cheaf_serial++;
									$html.='<td align="center" width="70" rowspan="'.$line_cheaf_total_row.'" id="line_cheaf_sl'.$line_cheaf_serial.'" valign="middle" ></td>';
								}
								
								if($floor_line_num==1)
								{
								//echo $floot_line_total;die;
									$floor_serial_no++;
									$html.='<td align="center" width="80" rowspan="'.$floot_line_total.'"   id="line_floor_sl'.$floor_serial_no.'"  valign="middle" ></td>';
								}
								$html.='<td width="" title="'.$fob_rate.'" align="left">'.$production_data_arr[$f_id][$l_id]['remarks'].'</td>';
									 
						$html.='</tr>';
						//echo $line_cheaf_row_count."**".$line_cheaf_serial."##";
						$line_cheaf_serial_data_arr[$line_cheaf_serial]['produced_min']+=$produce_minit;
						$line_cheaf_serial_data_arr[$line_cheaf_serial]['spend_min']+=$efficiency_min;
						$i++;
						$check_arr[]=$f_id;
					//}
				}
			}
		}
	}
	//die;
	//echo "<pre>";
	//print_r($line_cheaf_serial_data_arr);die;
			 $html.='<tr  class="tbl_bottom">
					<td width="40">&nbsp;</td>
					<td width="100">Floor Total</td>
					<td width="80">&nbsp;</td>
					<td width="60" align="center">'.number_format($floor_produc_min/$line_floor_production,2).'</td>
					<td width="60" align="center">'.$floor_action_machine.'</td>
					<td width="60" align="center">'.$floor_operator.'</td>
					<td width="60" align="center">'.$floor_helper.'</td>
					<td width="60" align="center">'.$floor_man_power.'</td>
					<td width="60" align="center">'.$floor_working_hour.'</td>
					<td align="center" width="60">'.$floor_extra_worker.'</td>
					<td align="center" width="60">'.$floor_extra_hour.'</td>
					<td align="center" width="100">'.$floor_avale_minute.'</td>
					<td width="80"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="70"></td>
					<td width="70">'.number_format(($floor_target_efficiency*100)/$floor_avale_minute,2).'%</td>
					<td align="center" width="70">'.$floor_tgt_h.'</td>
					<td align="center" width="80">'.$eff_target_floor.'</td>';
				
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
						$html.='<td align="center" width="50" style='.$bg_color.' >'. $floor_production[$prod_hour].'</td>';
					}
                            
				$avarage_production_per_floor=$line_floor_production/$floor_working_hour;
				$html.='<td align="center" width="80">'. $line_floor_production.'</td>
						<td align="center" width="80">'. $floor_sewing_output.'</td>
						<td align="center" width="80">'.number_format($floor_efficency,2).' %</td>
						<td align="center" width="60">'.number_format($floor_target_efficiency,0).'</td>
						<td align="center" width="100">'.number_format($floor_produc_min,0).'</td>
						<td align="center" width="70"></td>
						<td align="center" width="70">'. number_format(($line_floor_production/$eff_target_floor)*100,2).'%</td>
						<td align="center" width="70"></td>
						<td align="center" width="80"></td>
						<td align="center" width=""></td>';
				$html.='</tr>';
				
				$floor_serial_data_arr[$floor_serial_no]=number_format($floor_efficency,2);
				ob_start();	
				//print_r($floor_serial_data_arr);die;
	?>
    <br/>
    <div>
    	<fieldset>
        <label><strong>Daily Summary:-</strong></label>
    	<?php $table_width=2560+($last_hour-$hour)*50; ?>
        <table id="table_header_1" class="rpt_table" width="<?php echo $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                 <tr height="50">
                    <th width="40">SL</th>
                    <th width="100">Floor Name</th>
                    <th width="80">Line No</th>
                    <th width="60">SMV</th>
                    <th width="60">Actual RM</th>
                    <th width="60">MO</th>
                    <th width="60">HLP</th>
                    <th width="60">Worker</th>
                    <th width="60">Line Hrs</th>
                    <th width="60">Extra Worker</th>
                    <th width="60">Extra Hrs</th>
                    <th width="100">Spent Minutes</th>
                    <th width="80">Buyer</th>
                    <th width="100">Style</th>
                    <th width="100">Garments Item</th>
                    <th width="70">Start Date</th>
					<th width="70">TGT %</th>
                    <th width="70">TGP / Hour</th>
                    <th width="80" >Daily Target</th>

                    <?
                	for($k=$hour+1; $k<=$last_hour+1; $k++)
					{
						?>
                      	<th width="50" style="vertical-align:middle"><div class="block_div"><?  echo substr($start_hour_arr[$k],0,5);   ?></div></th>
						<?	
					}
                	?>
                   	<th width="80">Total Prd.</th>
                    <th width="80">Cumulative Prd.</th>
                    <th width="80">Efficiency. %</th>
                    <th width="60">Target p/m</th>
                	<th width="100">Produced Minutes</th>
                    <th width="70">Run Day</th>
                    <th width="70">Performance</th>
                    <th  width="70">Sr.Sup.</th>
                    <th width="80">Floor Av: Effi</th>
                    <th width="">Remarks</th>
                </tr>
            </thead>
        </table>
        <div style="width:<?php echo $table_width; ?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
            	<tbody>
					<?  echo $html;?> 
                </tbody>
                <tfoot >
                	 <tr>
                        <th width="40">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="60">Total</th>
                        <th width="60"><? echo number_format(($gnd_product_min/$line_total_production),2); ?></th>
                        <th width="60"><? echo $gnd_active_machine;//$total_operator; ?></th>
                        <th width="60"><? echo $total_operator; ?></th>
                        
                        <th width="60"><? echo $total_helper; ?></th>
                        <th align="center" width="60"><? echo $total_man_power; ?></th>
                        
                        <th width="60"><? echo $total_working_hour; ?></th>
                         <th align="center" width="60"><?php  echo $grand_extra_worker; ?></th>
                        <th align="center" width="60"><?php  echo $grand_extra_hour; ?></th>
                        <th align="center" width="100"><? echo $gnd_avable_min; ?></th>
                        <th width="80">&nbsp;</th>
                        <th width="100"></th>
                        <th width="70"><?  //echo number_format($total_terget/$total_working_hour,2); ?></th>
                        <th width="70"><?  //echo $total_terget; ?></th>
                        <th width="70"><?  echo number_format($grand_target_efficiency_manual/($i-1),2)."%"; ?></th>
                        <th width="70"><?  echo $gnd_total_tgt_h; ?></th>
                        <th width="70"><?  echo $total_terget; ?></th>
                        <?
						for($k=$hour; $k<=$last_hour; $k++)
						{
							$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
							?>
							<th align="center" width="50"><? echo $total_production[$prod_hour]; ?></th>
							<?	
						}
                        ?>
                        
                        <th align="center" width="80"><?  echo $line_total_production; ?></th>
                        <th align="center" width="80"><?  echo $grand_sewing_output; ?></th>
                      
                        <th align="center" width="80"><? echo number_format(($gnd_product_min*100)/$gnd_avable_min,2)."%"; ?></th>
                        <th align="center" width="60"><? echo $grand_target_efficiency; ?></th>
                        <th align="center" width="100"><?  echo number_format($gnd_product_min,0); ?></th>
                        
                        <th align="center" width="70"><? //echo number_format($grand_target_efficiency,2).""; ?></th>
                        <th align="center" width="70"><? echo number_format(($line_total_production/$total_terget)*100,2)."%"; ?></th>
                        <th align="center" width="70"><? //echo number_format(($line_total_production/$total_terget)*100,2)."%"; ?></th>
                        <th align="center" width="70"><? //echo number_format(($line_total_production/$total_terget)*100,2)."%"; ?></th>
                        <th align="center" width=""><? //echo $gnd_avable_min; ?></th>
                   </tr>
                </tfoot>
            </table>
		</div>
        </fieldset>
    </div>
    <br/>
    <script>
	
	<?php foreach($floor_serial_data_arr as $floor_serial=>$floor_serial_data)
	{
	?>
		$("#line_floor_sl<?php echo $floor_serial; ?>").text("<?php echo $floor_serial_data; ?>%");
	<?php
	}
	?>
	
	<?php foreach($line_cheaf_serial_data_arr as $line_cheaf_serial=>$line_cheaf_data)
	{
		$line_cheaf_efficiency=number_format(($line_cheaf_data['produced_min']/$line_cheaf_data['spend_min'])*100,2);
		if(is_nan($line_cheaf_efficiency)) $line_cheaf_efficiency=0;
	?>
		$("#line_cheaf_sl<?php echo $line_cheaf_serial; ?>").text("<?php echo $line_cheaf_efficiency; ?>%");
	<?php
	}
	?>
	
	</script>
    <?php
	//echo "<pre>";
	//print_r($production_data_arr);die;
	foreach($production_data_arr as $pr_date=>$pr_date_data)
	{
		foreach($pr_date_data as $f_id=>$fname)
		{
			ksort($fname);
			$floor_line_num=0;
			$produce_minit=0;
			$efficiency_min=0;
			$eff_target=0;
			
			foreach($fname as $l_id=>$ldata)
			{
				$actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$pr_date)));
				$germents_item_row=array();
				$germents_item_row=array_unique(explode('#',$production_data_arr[$pr_date][$f_id][$l_id]['item_number_id']));
				
				$germents_item=array();
				foreach($germents_item_row as $gps)
				{
					$po_garment_item=explode('**',$gps);
					$germents_item[$po_garment_item[1]][$po_garment_item[0]]+=$po_garment_item[2];
				}
				$garment_itemname='';
				$item_smv="";$item_ids='';
				$smv_for_item="";
				$produce_minit=0;
				$order_no_total="";
				$efficiency_min=0;
				$tot_po_qty=0;$fob_val=0;
				$tot_po_amt=0;
				$index='';
				
				$item_id_arr=array();
				foreach($germents_item as $gmt_itme_id=>$g_val)
				{
					foreach($g_val as $gmt_po_id=>$po_val)
					{
						$item_id_arr[$gmt_itme_id]=$gmt_itme_id;
						$garment_item_arr[$gmt_itme_id]=$garments_item[$gmt_itme_id];
						
						$index=$gmt_itme_id."_".$gmt_po_id;
						if($item_smv_array[$gmt_po_id][$gmt_itme_id]!="")
						{
							if($item_smv!='') $item_smv.='/';
							$item_smv.=$item_smv_array[$gmt_po_id][$gmt_itme_id];
							$po_wise_target_min+=($item_smv_array[$gmt_po_id][$gmt_itme_id]*1)*$po_val;
						}
						if($gmt_po_id!="")
						{
							if($order_no_total!="") $order_no_total.=",";
							$order_no_total.=$gmt_po_id;
						}
						if($smv_for_item!="") $smv_for_item.=",".$gmt_po_id."**".$item_smv_array[$gmt_po_id][$gmt_itme_id];
						else
						$smv_for_item=$gmt_po_id."**".$item_smv_array[$gmt_po_id][$gmt_itme_id];
						
						if(!in_array($index,$check_index_duplicate))
						{	
							$produce_minit+=$production_po_data_arr[$pr_date][$f_id][$l_id][$gmt_po_id]*$item_smv_array[$gmt_po_id][$gmt_itme_id];
						}
						$check_index_duplicate[]=$index;
					}
				}
				unset($check_index_duplicate);
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
					$produce_minit+=$production_po_data_arr[$pr_date][$f_id][$l_id][$sub_val]*$subcon_order_smv[$sub_val];
					if($subcon_order_id!="") $subcon_order_id.=",";
					$subcon_order_id.=$sub_val;
				}
			
				$type_line=$ldata['type_line'];
				$prod_reso_allo=$ldata['prod_reso_allo'];
		
				$sewing_line='';$poly_line=0;
				if($ldata['prod_reso_allo']==1)
				{
					$line_number=explode(",",$prod_reso_arr[$l_id]);
					foreach($line_number as $val)
					{
						if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
						if($poly_line==0)
						{
							if(in_array($val,$poly_line_arr)) $poly_line=1;
						}
					}
				}
				else
				{ 
					$sewing_line=$lineArr[$l_id];
					if(in_array($val,$poly_line_arr)) $poly_line=1;
				}
				
				
		//**********************************************************************************************************
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
			 //***************************************************************************************************			  
				$production_hour=array();
				for($h=$hour;$h<=$last_hour;$h++)
				{
					 $prod_hour="prod_hour".substr($line_start_hour_arr[$h],0,2).""; 
					 $production_hour[$prod_hour]=$ldata[$prod_hour];
				}
				
				$line_production_hour=0;
				if(str_replace("'","",$actual_production_date)>str_replace("'","",$actual_date)) 
				{
					if($type_line==2) //No Profuction Line
					{
						$line_start=$ldata['prod_start_time'];
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
						$line_production_hour+=$ldata[$line_hour];
						
						$actual_time_hour=$start_hour_arr[$lh+1];
						}
					}
					
					if($start_hour_arr[$actual_time]>$lunch_start_hour) $total_eff_hour=$total_eff_hour-1;
					
					if($type_line==2)
					{
						if($total_eff_hour>$ldata['working_hour'])
						{
							 $total_eff_hour=$ldata['working_hour'];
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
				if(str_replace("'","",$actual_production_date)<=str_replace("'","",$actual_date)) 
				{
					for($ah=$hour;$ah<=$last_hour;$ah++)
					{
						$prod_hour="prod_hour".substr($start_hour_arr[$ah],0,2).""; 
						$line_production_hour+=$ldata[$prod_hour];
					}
					if($type_line==2)
					{
						$total_eff_hour=$ldata['working_hour'];
					}
					else
					{
						$total_eff_hour=$prod_resource_array[$l_id][$pr_date]['working_hour'];	
					}
				}
				//******************************* line effiecency****************************************************['']
				$current_wo_time=0; 
				if($current_date==str_replace("'","",$actual_production_date))
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
				
				
			
				$smv_adjustmet_type=$prod_resource_array[$l_id][$pr_date]['smv_adjust_type'];
				$eff_target=($prod_resource_array[$l_id][$pr_date]['terget_hour']*$total_eff_hour);
				
				if($total_eff_hour>=$prod_resource_array[$l_id][$pr_date]['working_hour'])
				{
					if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array[$l_id][$pr_date]['smv_adjust'];
					if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array[$l_id][$pr_date]['smv_adjust'])*(-1);
				}
				
				if($poly_line==0)
				{
					$efficiency_min+=$total_adjustment+($prod_resource_array[$l_id][$pr_date]['man_power'])*$cla_cur_time*60;
					$man_power=$prod_resource_array[$l_id][$pr_date]['man_power'];	
					$operator=$prod_resource_array[$l_id][$pr_date]['operator'];
					$helper=$prod_resource_array[$l_id][$pr_date]['helper'];
					$terget_hour=$prod_resource_array[$l_id][$pr_date]['terget_hour'];	
					$capacity=$prod_resource_array[$l_id][$pr_date]['capacity'];
					$working_hour=$prod_resource_array[$l_id][$pr_date]['working_hour'];
						
					$po_id=rtrim($ldata['po_id'],',');
					$po_id=array_unique(explode(",",$po_id));
					$style=rtrim($ldata['style']);
					$style=implode(",",array_unique(explode(",",$style)));
				
					$cbo_get_upto=str_replace("'","",$cbo_get_upto);
				
					$po_id=$ldata['po_id'];
					$line_reject_qty=$ldata['reject_qty'];
				
					$current_pr_date=date("Y-m-d",strtotime($pr_date));
					$current_production_date=date("Y-m-d",$production_date_min);
					//  last month data==========================================
					
					if(($current_pr_date>=$last_month_first_date) && ($current_pr_date<=$last_month_last_date))
					{ 
						$last_month_data['total_target']+=$eff_target;
						$last_month_data['total_production']+=$line_production_hour;
						$last_month_data['total_product_min']+=$produce_minit;
						$last_month_data['total_target_min']+=$efficiency_min;
					}
					//  current month data==========================================
					if(($current_pr_date>=$current_month_first_date) && ($current_pr_date<=$current_production_date))
					{
				
						$current_month_data['total_target']+=$eff_target;
						$current_month_data['total_production']+=$line_production_hour;
						$current_month_data['total_product_min']+=$produce_minit;
						$current_month_data['total_target_min']+=$efficiency_min;
						$current_month_data['total_product_hour']+=$produce_minit/60;
						$current_month_data['total_target_hour']+=$efficiency_min/60;
						$graph_line_data[$ldata['serial']]['produce_minit']+=$produce_minit;
						$graph_line_data[$ldata['serial']]['sewing_line']=$sewing_line;
						$graph_line_data[$ldata['serial']]['efficiency_min']+=$efficiency_min;
						
						$graph_line_data[$ldata['serial']]['total_production']+=$line_production_hour;
						$graph_line_data[$ldata['serial']]['total_reject']+=$line_reject_qty;	
						$total_current_month_day+=1;
					}
					$i++;
				}
			}
		}
	}
	
	$min_width=100;
	$width=0;
	$chart_line_arr=array();
	$chart_line_data_arr=array();
	ksort($graph_line_data);
	//echo "<pre>";
//	print_r($graph_line_data);die;
	foreach($graph_line_data as $line=>$value)
	{
		$efficiency=($value['produce_minit']*100)/$value['efficiency_min'];
		$reject_percentage=($value['total_reject']*100)/$value['total_production'];
		//$efficiency_old+=100;
		if(is_nan($efficiency)) $efficiency=0;
		$efficiency=number_format($efficiency,2);
		if(is_nan($reject_percentage)) $reject_percentage=0;
		$reject_percentage=number_format($reject_percentage,2);
		$chart_line_arr[]=$value['sewing_line'];
		$chart_line_data_arr[]=$efficiency;
		$chart_line_reject_data_arr[]=$reject_percentage;
		//$chart_data.="{Line: '".$line."',Percentage: $efficiency},";
		$width=$width+60;
	}

	if($width<$min_width) $width=$min_width;
	$last_month_efficiency=$last_month_data['total_product_min']/$last_month_data['total_target_min'];
	$last_month_performance=$last_month_data['total_production']/$last_month_data['total_target'];
	
	$current_month_efficiency=$current_month_data['total_product_min']/$current_month_data['total_target_min'];
	$current_month_performance=$current_month_data['total_production']/$current_month_data['total_target'];
	$chart_line_arr= json_encode($chart_line_arr);
	$chart_line_reject_data_arr= json_encode($chart_line_reject_data_arr);
	$chart_line_data_arr= json_encode($chart_line_data_arr);
	
	
	?>
    <div align="left"> 
    	 <style>
            .tdstyle
            {
                background-image: rgb(156, 170, 234) 96%);
                border: 1px solid #8dafda;
                color: #444;
                font-size: 13px;
                font-weight: bold;
                height: 25px;
                line-height: 12px;
                text-align: center;
            }
            
            .tdstyleeven
            {
                background-image: linear-gradient(rgb(100, 200, 255) 10%, rgb(100, 180, 234) 96%);
                border: 1px solid #8dafda;
                color: #444;
                font-size: 13px;
                font-weight: bold;
                height: 25px;
                line-height: 12px;
                text-align: center;
            }
          
          </style> 
        <div style="float:left;">        
            <fieldset style="width:350px;">
               <label> <strong>Efficiency Sumarry:-</strong></label> 
               <table id="table_header_3" class="rpt_table" width="350" cellpadding="0" cellspacing="0" border="1" rules="all" style="background-color:#E3F2F1">
                    <tr  bgcolor="#E9F3FF">
                        <td width="200" >Last month factory efficiency</td>
                        <td width="100"><?php echo number_format($last_month_efficiency*100,2)."%"; ?></td>
                    </tr>
                    <tr bgcolor="#FFFFFF">
                        <td width="200">Last month production</td>
                        <td width="100"><?php echo $last_month_data['total_production']; ?></td>
                    </tr>
                    <tr bgcolor="#E9F3FF">
                        <td width="200" >Last month produce minutes</td>
                        <td width="100"><?php echo $last_month_data['total_product_min']; ?></td>
                    </tr>
                    <tr bgcolor="#FFFFFF">    
                        <td width="200">Last month cost per minutes</td>
                    </tr>
                    <tr bgcolor="#E9F3FF">
                        <td width="200">Up to date factory efficiency </td>
                        <td width="100"><?php echo number_format($current_month_efficiency*100,2)."%"; ?></td>
                    </tr>
                    <tr bgcolor="#FFFFFF">
                        <td width="200">Up to date production target</td>
                        <td width="100"><?php echo $current_month_data['total_target']; ?></td>
                    </tr>
                    <tr bgcolor="#FFFFFF">
                        <td width="200">Up to date production acheive</td>
                        <td width="100"><?php echo $current_month_data['total_production']; ?></td>
                    </tr>
                    <tr bgcolor="#E9F3FF">
                        <td width="200">Up to date target Produce Min.</td>
                        <td width="100"><?php echo $current_month_data['total_target_min']; ?></td>
                   </tr>
                   <tr bgcolor="#FFFFFF">
                        <td width="200">Up to date Acheive Produce Min</td>
                        <td width="100"><?php echo $current_month_data['total_product_min']; ?></td>
                    </tr>
                    <tr bgcolor="#E9F3FF">
                        <td width="200">Up to date spent minutes</td>
                        <td width="100"><?php echo $current_month_data['total_target_min']; ?></td>
                    </tr>
                    <tr bgcolor="#FFFFFF">
                        <td width="200">Up to date OT hours</td>
                        <td width="100"><?php  ?></td>
                    </tr>
                    <tr bgcolor="#E9F3FF">
                        <td width="200">To day MMR</td>
                        <td width="100"><?php echo number_format(($total_man_power/$gnd_active_machine),2); ?></td>
                    </tr>
                    <tr bgcolor="#E9F3FF">
                        <td width="200">Garments produced per machine</td>
                        <td width="100"><?php echo number_format(($line_total_production/$gnd_active_machine),2);  ?></td>
                    </tr>
                    <tr bgcolor="#FFFFFF">
                        <td width="200">Avg. machine cost perday</td>
                        <td width="100"><?php echo number_format($total_man_power/$total_operator,2); ?></td>
                    </tr>
                    <tr bgcolor="#E9F3FF">
                        <td width="200">Avg. Monthly over head cost</td>
                        
                    </tr>
                    <tr bgcolor="#FFFFFF">
                        <td width="200">Plan efficiency target</td>
                        <td width="100"></td>
                    </tr>
                    <tr bgcolor="#E9F3FF">
                        <td width="200">Plan production target</td>
                        <td width="100"><?php ?></td>
                    </tr>
                    <tr bgcolor="#FFFFFF">
                        <td width="200">Plan produce minutes target</td>
                        <td width="100"></td>
                    </tr>
                    <tr bgcolor="#E9F3FF">
                        <td width="200">Plan cost per minutes target</td>
                        <td width="100"><?php  ?></td>
                    </tr>
                </table>
            </fieldset>
        </div>
       <?php if($width<400) $width=400; ?>
        <div style="width:<?php  echo $width; ?>px; height:580px;  margin-left:10px; border:solid 1px;  float:left">
        	<table style="min-width:400px; font-size:12px" align="center">
					<tr>
						<td colspan="14"><b>Current Month Line Wise Efficiency Graph</b></td>
                     </tr>
                     <tr>
                     	<td bgcolor="#FF6A6A" width="16"></td>
                        <td colspan="2" ><b>Line Efficiency</b></td>
                        <td bgcolor="#00D2D2" width="16"></td>
                        <td colspan="3" ><b>Alter Percentage</b></td>
					</tr>
					
				</table>
        		<canvas id="canvas1" height="380" width="<?php  echo $width; ?>" ></canvas>
        </div>
        
    </div>
    
    <style>
			#canvas1 {
				font-size	: 11px;
			}					
		</style>
    	<script src="Chart.js-master/Chart.js"></script>
        <script >
		
		 	var barChartData2 = {
			labels : <?php echo $chart_line_arr; ?>,
			barPercentage: 0.5,
			datasets : [
					{
						fillColor : "#FF6A6A",
						//strokeColor : "#40FF9F",
						//highlightFill: "#996666",
						//highlightStroke: "#35BDFF",
						data : <?php echo $chart_line_data_arr; ?>
					},
					{
						fillColor : "#00D2D2",
						//strokeColor : "#FFFF00",
						//highlightFill: "#996666",
						//highlightStroke: "#35BDFF",
						data : <?php echo $chart_line_reject_data_arr; ?>
					}
				]
			}
			
			var ctx2 = document.getElementById("canvas1").getContext("2d");
			window.myBar = new Chart(ctx2).Bar(barChartData2, {
				responsive : true
			});	
		
	</script>
	<br/>
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

if($action=="generate_report_graph") 
{
 	extract($_REQUEST);
	$companyArr = return_library_array("select id,company_name from lib_company","id","company_name"); 
	$buyerArr = return_library_array("select id,short_name from lib_buyer","id","short_name"); 
	$locationArr = return_library_array("select id,location_name from lib_location","id","location_name"); 
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name"); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$comapny_id=$cbo_company_id;
    $today_date=date("Y-m-d");
	$txt_producting_day=$txt_date;
	$production_date_min = strtotime($txt_producting_day);
	
	$date = new DateTime($txt_producting_day);
	$date->modify('FIRST DAY OF -1 MONTH');
	$last_month_first_date=$date->format('Y-m-d');
	$last_month_last_date=$date->format('Y-m-t');
	
	$current_month_first_date=date("Y-m-01",$production_date_min);
	
	if($db_type==0)
	{
		$last_month_start_date=change_date_format($last_month_first_date,"yyyy-mm-dd");
		$current_month_start_date=change_date_format($current_month_first_date,"yyyy-mm-dd");
		$last_month_end_date=change_date_format($last_month_last_date,"yyyy-mm-dd");
		$txt_date=change_date_format($txt_date,"yyyy-mm-dd");
	}
	else
	{
		$last_month_start_date=change_date_format($last_month_first_date,"yyyy-mm-dd","-",1);
		$current_month_start_date=change_date_format($current_month_first_date,"yyyy-mm-dd","-",1);
		$last_month_end_date=change_date_format($last_month_last_date,"yyyy-mm-dd","-",1);
		$txt_date=change_date_format($txt_date,"yyyy-mm-dd","-",1);
	}
	
	$lineDataArr = sql_select("select id, line_name,sewing_group, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
	order by sewing_line_serial"); 
	foreach($lineDataArr as $lRow)
	{
		$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
		$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
		$lastSlNo=$lRow[csf('sewing_line_serial')];
		if($lRow[csf('sewing_group')]=="Poly")
		{
			$poly_line_arr[$lRow[csf('id')]]=$lRow[csf('id')];
		}
	}
	
	
	if($db_type==0)
	{
		$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$comapny_id and shift_id=1 and pr_date between '".$last_month_start_date."' and '".$txt_date."' and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");	
	}
	else
	{
	
		$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$comapny_id and shift_id=1 and pr_date between '".$last_month_start_date."' and '".$txt_date."' and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
	}
	
	if($min_shif_start=="")
	{
		echo "<p style='font-size:20px', align='center'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<p/>";die;
	}
	
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
	
	if(str_replace("'","",$cbo_company_id)==0) $company_name=""; else $company_name="and a.serving_company=".str_replace("'","",$cbo_company_id)."";
	
	if(str_replace("'","",$cbo_location_id)==0) 
	{
		$subcon_location="";
		$location="";
		$location_cond='';
	}
	else 
	{
		$location_cond=" and a.location_id=".str_replace("'","",$cbo_location_id)."";
		$location=" and a.location=".str_replace("'","",$cbo_location_id)."";
		$subcon_location=" and a.location_id=".str_replace("'","",$cbo_location_id)."";
	}
	$cbo_floor_id=str_replace("'","",$cbo_floor_id);
	if($cbo_floor_id=="") $floor=""; else $floor="and a.floor_id in(".$cbo_floor_id.")";
    if(str_replace("'","",$hidden_line_id)==0)
	{ 
		$line=""; 
		$subcon_line="";
		$line_cond='';
	}
	else 
	{
		$subcon_line="and a.line_id in(".str_replace("'","",$hidden_line_id).")";
		$line="and a.sewing_line in(".str_replace("'","",$hidden_line_id).")";
		$line_cond="and a.line_number in(".str_replace("'","",$hidden_line_id).")";
	}

	if($db_type==0)
	{
		$manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
	}
	else
	{
		$manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
	}
	 $sql_item="select a.buyer_name,a.style_ref_no,b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost,c.smv_set from wo_po_details_master a,wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.company_name in($manufacturing_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
	 //echo $sql_item;die;
	$resultItem=sql_select($sql_item);
	
	foreach($resultItem as $itemData)
	{
		$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
		$po_buyer_arr[$itemData[csf('id')]]=$itemData[csf('buyer_name')];
		$po_style_arr[$itemData[csf('id')]]=$itemData[csf('style_ref_no')];
	}

	
	if($prod_reso_allo[0]==1)
	{
		$prod_resource_array=array();
		
		$dataArray_sql=sql_select("select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator,c.target_efficiency,b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity,sum(d.target_per_line) as target_per_line,d.po_id,d.gmts_item_id  from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c left join prod_resource_color_size d on (d.dtls_id=c.id and d.mst_id=c.mst_id) where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id=$comapny_id and b.pr_date between '".$last_month_start_date."' and '".$txt_date."' and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  $location_cond  $floor group by a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator,c.target_efficiency,b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity,d.po_id,d.gmts_item_id ");// between '".$last_month_start_date."' and  
		foreach($dataArray_sql as $val)
		{
			$val[csf('pr_date')]=date("d-M-Y",strtotime($val[csf('pr_date')]));
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['man_power']=$val[csf('man_power')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['operator']=$val[csf('operator')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['helper']=$val[csf('helper')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['terget_hour']=$val[csf('target_per_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['working_hour']=$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['tpd']=$val[csf('target_per_hour')]*$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_start']=$val[csf('from_date')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_end']=$val[csf('to_date')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['capacity']=$val[csf('capacity')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['active_machine']=$val[csf('active_machine')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['line_chief']=$val[csf('line_chief')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust']=$val[csf('smv_adjust')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['target_efficiency']=$val[csf('target_efficiency')];
			
			 $sewing_line_id=$val[csf('line_number')];
		
			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id]=$slNo;
			}
			else $slNo=$lineSerialArr[$sewing_line_id];
			
			
			$production_data_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]]['serial']=$slNo;
			$production_serial_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$slNo][$val[csf('id')]]=$val[csf('id')];
			
			if($production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('id')]]['po_id']!="")
			{
				$production_data_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]][$val[csf('po_id')]]['po_id'].=",".$val[csf('po_id')];
				$production_data_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]]['style_ref'].=",".$po_style_arr[$val[csf('po_id')]]; 
			}
			else
			{
				$production_data_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]]['po_id']=$val[csf('po_id')];	
				 $production_data_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]]['style_ref']=$po_style_arr[$val[csf('po_id')]];
			}
			
			if($production_data_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]]['item_number_id']!="")
			{
				$production_data_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]]['item_number_id'].="#".$val[csf('po_id')]."**".$val[csf('gmts_item_id')]."**".$val[csf('target_per_line')]; 
			}
			else
			{
				 $production_data_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]]['item_number_id']=$val[csf('po_id')]."**".$val[csf('gmts_item_id')]."**".$val[csf('target_per_line')]; 
			}
			
			//$po_wise_target_per_line[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]][$val[csf('gmts_item_id')]][$val[csf('po_id')]]+=$val[csf('target_per_line')]; 
			
			if( $production_data_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]]['buyer_name']!="")
			{
				 $production_data_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]]['buyer_name'].=",".$po_buyer_arr[$val[csf('po_id')]]; 
			}
			else
			{
				 $production_data_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]]['buyer_name']=$po_buyer_arr[$val[csf('po_id')]]; 
			}
			
			$production_data_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]]['target_per_line']+=$val[csf('target_per_line')];
			$production_data_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]]['prod_reso_allo']=1;
			$production_data_arr[$val[csf('pr_date')]][$val[csf('floor_id')]][$val[csf('id')]]['type_line']=2; 
		}

		$sql_query=sql_select("select b.mst_id, b.pr_date,b.number_of_emp ,b.adjust_hour  from prod_resource_mst a,prod_resource_smv_adj b  where a.id=b.mst_id  and a.company_id=$comapny_id and b.pr_date='".$txt_date."' and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and b.adjustment_source=1  $location_cond  $floor ");
		foreach($sql_query as $val)
		{
			$val[csf('pr_date')]=date("d-M-Y",strtotime($val[csf('pr_date')]));
			$prod_resource_array[$val[csf('mst_id')]][$val[csf('pr_date')]]['number_of_emp']+=$val[csf('number_of_emp')];
			$prod_resource_array[$val[csf('mst_id')]][$val[csf('pr_date')]]['adjust_hour']+=$val[csf('adjust_hour')];
		}
		
		//echo "<pre>";
//print_r($production_data_arr);die;

		
		if($db_type==0)
		{
			$dataArray=sql_select("select a.id,b.pr_date,d.shift_id,TIME_FORMAT( d.prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( d.lunch_start_time, '%H:%i' ) as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 and b.pr_date between '".$last_month_start_date."' and  '".$txt_date."'"); 
		}
		else
		{
			$dataArray=sql_select("select a.id,b.pr_date,d.shift_id,TO_CHAR(d.prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR( d.lunch_start_time,'HH24:MI') as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 and b.pr_date between '".$last_month_start_date."' and  '".$txt_date."' ");
		}
		
		$line_number_arr=array();
		foreach($dataArray as $val)
		{
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['shift_id']=$val[csf('shift_id')];
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['prod_start_time']=$val[csf('prod_start_time')];
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['lunch_start_time']=$val[csf('lunch_start_time')];
		}
	}
	
	if($db_type==0) $prod_start_cond="prod_start_time";
	else if($db_type==2) $prod_start_cond="TO_CHAR(prod_start_time,'DD-MON-YYYY HH24:MI')";
	
	$variable_start_time_arr='';
	$prod_start_time=sql_select("select $prod_start_cond as prod_start_time from variable_settings_production where company_name=$cbo_company_id and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");

	foreach($prod_start_time as $row)
	{
		$ex_time=explode(" ",$row[csf('prod_start_time')]);
		$variable_start_time_arr=$ex_time[1];
	}

	unset($prod_start_time);
	$current_date_time=date('d-m-Y H:i');
	$variable_date=change_date_format(str_replace("'","",$txt_date)).' '.$variable_start_time_arr;
	$datediff=datediff("n",$variable_date,$current_date_time);
	
	$ex_date_time=explode(" ",$current_date_time);
	$current_date=$ex_date_time[0];
	$current_time=$ex_date_time[1];
	$ex_time=explode(":",$current_time);
	
	$search_prod_date=change_date_format(str_replace("'","",$txt_date));
	$current_eff_min=($ex_time[0]*60)+$ex_time[1];
	$variable_time= explode(":",$variable_start_time_arr);
	$vari_min=($variable_time[0]*60)+$variable_time[1];
	$difa_time=explode(".",number_format(($current_eff_min-$vari_min)/60,2));//datediff("",$ctime,$variable_start_time_arr);
	$dif_time=number_format($datediff/60,2);
	$dif_hour_min=date("H:i", strtotime($dif_time));
	
 
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
	
	$product_category_cond='';
	if($txt_item_catgory!=0) $product_category_cond=" and b.product_category=".$txt_item_catgory."";
	
	$i=1; $grand_total_good=0; $grand_alter_good=0; $grand_total_reject=0;
	$html="";
	$floor_html="";
    $check_arr=array();
	
	if($db_type==0)
	{
		$sql="select  a.company_id, a.location, a.floor_id,d.floor_serial_no,e.sewing_line_serial, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name  as buyer_name, b.style_ref_no, a.po_break_down_id, a.item_number_id, c.po_number as po_number,c.unit_price,c.file_no,c.grouping as ref,sum(a.production_quantity) as good_qnty,
 sum(IFNULL(a.alter_qnty,0)) as alter_qty,sum(IFNULL(a.reject_qnty,0)) as reject_qty,sum(IFNULL(a.spot_qnty,0)) as spot_qty,"; 
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
		$sql.="sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23 from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a left join lib_prod_floor d on a.floor_id=d.id and d.is_deleted=0 left join lib_sewing_line e on a.sewing_line=e.id and e.is_deleted=0  where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $company_name $location $floor   $file_cond  and   a.production_date between '".$last_month_start_date."'  and  '".$txt_date."' $product_category_cond group by a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,e.sewing_line_serial,b.buyer_name,a.item_number_id,c.po_number,c.file_no,c.unit_price,c.grouping,b.style_ref_no order by a.location, a.floor_id,e.sewing_line_serial,a.prod_reso_allo";
	}
	else if($db_type==2)
	{
		$sql="select  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name  as buyer_name,b.style_ref_no, a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.file_no,c.unit_price,c.grouping as ref,sum(a.production_quantity) as good_qnty,
 sum(nvl(a.alter_qnty,0)) as alter_qty,sum(nvl(a.reject_qnty,0)) as reject_qty,sum(nvl(a.spot_qnty,0)) as spot_qty,"; 
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
		
		$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23 from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $company_name $location $floor   and   a.production_date between '".$last_month_start_date."' and  '".$txt_date."' $product_category_cond group by a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name,b.style_ref_no,a.item_number_id,c.po_number,c.unit_price,c.file_no,c.grouping order by a.location,a.floor_id,a.sewing_line";
		
	}
	//echo $sql;die;
	$sql_resqlt=sql_select($sql);
	$production_po_data_arr=array();
	$reso_line_ids=''; $all_po_id="";
	foreach($sql_resqlt as $val)
	{
		$val[csf('production_date')]=date("d-M-Y",strtotime($val[csf('production_date')]));
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
		$production_serial_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]]=$val[csf('sewing_line')];
		
		
		$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['serial']=$slNo;
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
			
			
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
			{
				if( $h>=$line_start_hour && $h<=$actual_time)
				{
					$production_po_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf($prod_hour)];
					$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$prod_hour]+=$val[csf($prod_hour)];  
				} 	
			}
			
			if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date)) 
			{	
				$production_po_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf($prod_hour)];
				$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$prod_hour]+=$val[csf($prod_hour)]; 
			}
		}
		
		if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
		{	
			if( $h>=$line_start_hour && $h<=$actual_time)
			{
				//echo $h."#".$actual_time."**";die;
				$production_po_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf('prod_hour23')]; 
				$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$prod_hour]+=$val[csf('prod_hour23')];     
			} 	
		}
		
		$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['prod_reso_allo']=$val[csf('prod_reso_allo')]; 
		
	 	if($production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']!="")
		{
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name'].=",".$val[csf('buyer_name')]; 
		}
	 	else
		{
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']=$val[csf('buyer_name')]; 
		}
	
	
		if($production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']!="")
		{
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name'].=",".$val[csf('buyer_name')]; 
		}
	 	else
		{
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']=$val[csf('buyer_name')]; 
		}
	
	
	 	if($production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_id']!="")
		{
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number'].=",".$val[csf('po_number')];
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_id'].=",".$val[csf('po_break_down_id')];
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['style_ref'].=",".$val[csf('style_ref_no')];
			$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['file'].=",".$val[csf('file_no')];
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['ref'].=",".$val[csf('ref')]; 
		}
	 	else
		{
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']=$val[csf('po_number')];
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_id']=$val[csf('po_break_down_id')]; 
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['style_ref']=$val[csf('style_ref_no')]; 
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['file']=$val[csf('file_no')]; 
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['ref']=$val[csf('ref')]; 
		}
		$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['type_line']=1; 
		$fob_rate_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['rate']=$val[csf('unit_price')]; 
		
		if($production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']!="")
		{
			$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id'].="#".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]; 
		}
		else
		{
			 $production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]; 
		}
		$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['quantity']+=$val[csf('good_qnty')];
		$production_data_arr[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['reject_qty']+=$val[csf('reject_qty')]+$val[csf('alter_qty')]+$val[csf('spot_qty')];
		$production_data_arr_qty[$val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]['quantity']+=$val[csf('good_qnty')];
		

		
		if($all_po_id=="") $all_po_id=$val[csf('po_break_down_id')]; else $all_po_id.=",".$val[csf('po_break_down_id')];
	}


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
				$poIds_cond.=" b.id  in($ids) or ";
			}
			$poIds_cond=chop($poIds_cond,'or ');
			$poIds_cond.=")";
		}
		else
		{
			$poIds_cond=" and  b.id  in($all_po_id)";
		}
	}
		
	$sql_item_rate="select b.id, c.item_number_id, c.order_quantity, c.order_total from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c where b.job_no_mst=a.job_no and b.id=c.po_break_down_id and b.job_no_mst=c.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1";
	$resultRate=sql_select($sql_item_rate);
	$item_po_array=array();
	foreach($resultRate as $row)
	{
		$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['qty']+=$row[csf('order_quantity')];
		$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['amt']+=$row[csf('order_total')];
	}
	

	
	// subcoutact data ************************************************************************************************
	
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
   		$sql_sub_contuct.="sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.id and d.is_deleted=0 left join lib_sewing_line e on a.line_id=e.id and e.is_deleted=0 where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id=$comapny_id and   a.production_date between '".$last_month_start_date."' and  '".$txt_date."'  $subcon_location $floor   group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date,a.prod_reso_allo,a.line_id,b.party_id,c.order_no,c.cust_style_ref,e.sewing_line_serial order by a.location_id,d.floor_serial_no,e.sewing_line_serial,a.prod_reso_allo";
	}
	else
	{
		$sql_sub_contuct= "select  a.company_id, a.location_id, a.floor_id, a.production_date, a.line_id,b.party_id  as buyer_name,a.prod_reso_allo,a.order_id,c.order_no as po_number,c.cust_style_ref,max(c.smv) as smv,a.prod_reso_allo,sum(a.production_qnty) as good_qnty,"; 
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
		
	   	$sql_sub_contuct.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]'	and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a  where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id=$comapny_id  and a.production_date between '".$last_month_start_date."' and  '".$txt_date."' $subcon_location $floor $subcon_line  group by a.company_id, a.location_id, a.floor_id,a.order_id, a.production_date, a.line_id,b.party_id,c.order_no,c.cust_style_ref,a.prod_reso_allo order by a.location_id, a.floor_id,a.prod_reso_allo";
		
	}
	$subcon_order_smv=array();		
	foreach($sub_result as $subcon_val)
	{
		$subcon_val[csf('production_date')]=date("d-M-Y",strtotime($subcon_val[csf('production_date')]));
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
		$production_serial_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$slNo][$subcon_val[csf('sewing_line')]]=$subcon_val[csf('sewing_line')];
		
		
		$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['serial']=$slNo;
		$production_po_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$subcon_val[csf('good_qnty')];
		if($production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['buyer_name']!="")
		{
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['buyer_name'].=",".$subcon_val[csf('buyer_name')]; 
		}
		else
		{
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['buyer_name']=$subcon_val[csf('buyer_name')]; 
		}
	
		if($production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number']!="")
		{
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number'].=",".$subcon_val[csf('po_number')];
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['style_ref'].=",".$subcon_val[csf('cust_style_ref')];  
		}
		else
		{
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number']=$subcon_val[csf('po_number')]; 
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['style_ref']=$subcon_val[csf('cust_style_ref')]; 
		}
	
		if($production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id']!="")
		{
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id'].=",".$subcon_val[csf('order_id')]; 
		}
		else
		{
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id'].=$subcon_val[csf('order_id')]; 
		}
		$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['type_line']=1; 
		$subcon_order_smv[$subcon_val[csf('order_id')]]=$subcon_val[csf('smv')];
		$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['quantity']+=$subcon_val[csf('good_qnty')];
		
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
			$production_data_arr[$subcon_val[csf('production_date')]][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$prod_hour]+=$subcon_val[csf($prod_hour)]; 
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
		$production_data_arr[$subcon_val[csf('production_date')]][$val[csf('floor_id')]][$val[csf('line_id')]]['prod_hour23']+=$val[csf('prod_hour23')];
	}
	
	$total_sewing_input=return_field_value("sum(a.production_quantity) as good_qnty","wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a "," a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.production_type=4 and a.status_active=1 and a.is_deleted=0  and a.company_id=$comapny_id  and   a.production_date between '".$current_month_start_date."' and  '".$txt_date."' $location $floor   and  a.status_active=1 and a.is_deleted=0 ","good_qnty");
	
	
	
	
	$exfactory_qty=return_field_value("sum(a.ex_factory_qnty) as ex_factory_qnty","pro_ex_factory_mst a,pro_ex_factory_delivery_mst b"," b.id=a.delivery_mst_id and b.company_id =$comapny_id and a.ex_factory_date between '".$last_month_start_date."' and  '".$last_month_end_date."'  and a.status_active=1 and a.is_deleted=0","ex_factory_qnty");
	
	$txt_date=str_replace("'","",$txt_date);
	
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
	$j=0;
	
	$line_number_check_arr=array();
	$smv_for_item="";
	$total_production=array();
	$floor_production=array();
    $line_floor_production=0;
    $line_total_production=0; $gnd_total_fob_val=0;
	$graph_line_arr=array();
	$graph_line_reject_arr=array();

	
	foreach($production_data_arr as $pr_date=>$pr_date_data)
	{
	
		foreach($pr_date_data as $f_id=>$fname)
		{
			ksort($fname);
			$floor_line_num=0;
			$produce_minit=0;
			$efficiency_min=0;
			$eff_target=0;
			foreach($fname as $l_id=>$ldata)
			{
				
				$actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$pr_date)));
				$germents_item_row=array();
				//if($l_id==4 && $pr_date=='01-Sep-2018')
				//{
				
				$germents_item_row=array_unique(explode('#',$ldata['item_number_id']));
				//print_r($germents_item_row);
				
				$germents_item=array();
				foreach($germents_item_row as $gps)
				{
					$po_garment_item=explode('**',$gps);
					$germents_item[$po_garment_item[1]][$po_garment_item[0]]+=$po_garment_item[2];
				}
				$garment_itemname='';
				$item_smv="";$item_ids='';
				$smv_for_item="";
				$produce_minit=0;
				$order_no_total="";
				$efficiency_min=0;
				$tot_po_qty=0;$fob_val=0;
				$tot_po_amt=0;
				$index='';
				
				$item_id_arr=array();
				foreach($germents_item as $gmt_itme_id=>$g_val)
				{
					foreach($g_val as $gmt_po_id=>$po_val)
					{
						$item_id_arr[$gmt_itme_id]=$gmt_itme_id;
						$garment_item_arr[$gmt_itme_id]=$garments_item[$gmt_itme_id];
						
						$index=$gmt_itme_id."_".$gmt_po_id;
						if($item_smv_array[$gmt_po_id][$gmt_itme_id]!="")
						{
							if($item_smv!='') $item_smv.='/';
							$item_smv.=$item_smv_array[$gmt_po_id][$gmt_itme_id];
							$po_wise_target_min+=($item_smv_array[$gmt_po_id][$gmt_itme_id]*1)*$po_val;
						}
						if($gmt_po_id!="")
						{
							if($order_no_total!="") $order_no_total.=",";
							$order_no_total.=$gmt_po_id;
						}
						if($smv_for_item!="") $smv_for_item.=",".$gmt_po_id."**".$item_smv_array[$gmt_po_id][$gmt_itme_id];
						else
						$smv_for_item=$gmt_po_id."**".$item_smv_array[$gmt_po_id][$gmt_itme_id];
						
						
						if(!in_array($index,$check_index_duplicate))
						{
								
							$produce_minit+=$production_po_data_arr[$pr_date][$f_id][$l_id][$gmt_po_id]*$item_smv_array[$gmt_po_id][$gmt_itme_id];
						}
						$check_index_duplicate[]=$index;
					}
				}
				unset($check_index_duplicate);
			
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
					$produce_minit+=$production_po_data_arr[$pr_date][$f_id][$l_id][$sub_val]*$subcon_order_smv[$sub_val];
					if($subcon_order_id!="") $subcon_order_id.=",";
					$subcon_order_id.=$sub_val;
				}
			
				$type_line=$ldata['type_line'];
				$prod_reso_allo=$ldata['prod_reso_allo'];
		
				$sewing_line='';$poly_line=0;
				if($ldata['prod_reso_allo']==1)
				{
					$line_number=explode(",",$prod_reso_arr[$l_id]);
					foreach($line_number as $val)
					{
						if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
						if($poly_line==0)
						{
							if(in_array($val,$poly_line_arr)) $poly_line=1;
						}
					}
				}
				else
				{ 
					$sewing_line=$lineArr[$l_id];
					if(in_array($val,$poly_line_arr)) $poly_line=1;
				}
				
				
		//**********************************************************************************************************
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
			 //***************************************************************************************************			  
				$production_hour=array();
				for($h=$hour;$h<=$last_hour;$h++)
				{
					 $prod_hour="prod_hour".substr($line_start_hour_arr[$h],0,2).""; 
					 $production_hour[$prod_hour]=$ldata[$prod_hour];
				}
				
				$line_production_hour=0;
				if(str_replace("'","",$actual_production_date)>str_replace("'","",$actual_date)) 
				{
					if($type_line==2) //No Profuction Line
					{
						$line_start=$ldata['prod_start_time'];
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
						$line_production_hour+=$ldata[$line_hour];
						
						$actual_time_hour=$start_hour_arr[$lh+1];
						}
					}
					
					if($start_hour_arr[$actual_time]>$lunch_start_hour) $total_eff_hour=$total_eff_hour-1;
					
					if($type_line==2)
					{
						if($total_eff_hour>$ldata['working_hour'])
						{
							 $total_eff_hour=$ldata['working_hour'];
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
				if(str_replace("'","",$actual_production_date)<=str_replace("'","",$actual_date)) 
				{
					for($ah=$hour;$ah<=$last_hour;$ah++)
					{
						$prod_hour="prod_hour".substr($start_hour_arr[$ah],0,2).""; 
						$line_production_hour+=$ldata[$prod_hour];
					}
					if($type_line==2)
					{
						$total_eff_hour=$ldata['working_hour'];
					}
					else
					{
						$total_eff_hour=$prod_resource_array[$l_id][$pr_date]['working_hour'];	
					}
				}
				//******************************* line effiecency****************************************************['']
				$current_wo_time=0; 
				if($current_date==str_replace("'","",$actual_production_date))
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
				
				
			
				$smv_adjustmet_type=$prod_resource_array[$l_id][$pr_date]['smv_adjust_type'];
				$eff_target=($prod_resource_array[$l_id][$pr_date]['terget_hour']*$total_eff_hour);
				
				if($total_eff_hour>=$prod_resource_array[$l_id][$pr_date]['working_hour'])
				{
					if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array[$l_id][$pr_date]['smv_adjust'];
					if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array[$l_id][$pr_date]['smv_adjust'])*(-1);
				}
				
				if($poly_line==0)
				{
					$efficiency_min+=$total_adjustment+($prod_resource_array[$l_id][$pr_date]['man_power'])*$cla_cur_time*60;
			
				
					$man_power=$prod_resource_array[$l_id][$pr_date]['man_power'];	
					$operator=$prod_resource_array[$l_id][$pr_date]['operator'];
					$helper=$prod_resource_array[$l_id][$pr_date]['helper'];
					$terget_hour=$prod_resource_array[$l_id][$pr_date]['terget_hour'];	
					$capacity=$prod_resource_array[$l_id][$pr_date]['capacity'];
					$working_hour=$prod_resource_array[$l_id][$pr_date]['working_hour'];
						
					$po_id=rtrim($ldata['po_id'],',');
					$po_id=array_unique(explode(",",$po_id));
					$style=rtrim($ldata['style']);
					$style=implode(",",array_unique(explode(",",$style)));
				
					$cbo_get_upto=str_replace("'","",$cbo_get_upto);
				
					$po_id=$ldata['po_id'];
					$line_reject_qty=$ldata['reject_qty'];
				
					$current_pr_date=date("Y-m-d",strtotime($pr_date));
					$current_production_date=date("Y-m-d",$production_date_min);
					//  last month data==========================================
					
					if(($current_pr_date>=$last_month_first_date) && ($current_pr_date<=$last_month_last_date))
					{ 
						$last_month_data['total_target']+=$eff_target;
						$last_month_data['total_production']+=$line_production_hour;
						$last_month_data['total_product_min']+=$produce_minit;
						$last_month_data['total_target_min']+=$efficiency_min;
					}
					
					//  current month data==========================================
					if(($current_pr_date>=$current_month_first_date) && ($current_pr_date<=$current_production_date))
					{
					//echo $produce_minit."**".$sewing_line."<br/>";
						$current_month_data['total_target']+=$eff_target;
						$current_month_data['total_production']+=$line_production_hour;
						$current_month_data['total_product_min']+=$produce_minit;
						$current_month_data['total_target_min']+=$efficiency_min;
						$current_month_data['total_product_hour']+=$produce_minit/60;
						$current_month_data['total_target_hour']+=$efficiency_min/60;
						$graph_line_data[$ldata['serial']]['produce_minit']+=$produce_minit;
						$graph_line_data[$ldata['serial']]['sewing_line']=$sewing_line;
						$graph_line_data[$ldata['serial']]['efficiency_min']+=$efficiency_min;
						
						$graph_line_data[$ldata['serial']]['total_production']+=$line_production_hour;
						$graph_line_data[$ldata['serial']]['total_reject']+=$line_reject_qty;	
						$total_current_month_day+=1;
					}
					$i++;
				}
			}
		}
	}
	
	$min_width=100;
	$width=0;
	$chart_line_arr=array();
	$chart_line_data_arr=array();
	ksort($graph_line_data);
	//echo "<pre>";
	//print_r($graph_line_data);die;
	foreach($graph_line_data as $line=>$value)
	{
		$efficiency=($value['produce_minit']*100)/$value['efficiency_min'];
		$reject_percentage=($value['total_reject']*100)/$value['total_production'];
		//$efficiency_old+=100;
		if(is_nan($efficiency)) $efficiency=0;
		$efficiency=number_format($efficiency,2);
		if(is_nan($reject_percentage)) $reject_percentage=0;
		$reject_percentage=number_format($reject_percentage,2);
		$chart_line_arr[]=$value['sewing_line'];
		$chart_line_data_arr[]=$efficiency;
		$chart_line_reject_data_arr[]=$reject_percentage;
		//$chart_data.="{Line: '".$line."',Percentage: $efficiency},";
		$width=$width+60;
	}
	//$chart_data=rtrim($chart_data,',');
//	$chart_data.=']';
	if($width<$min_width) $width=$min_width;
	$last_month_efficiency=$last_month_data['total_product_min']/$last_month_data['total_target_min'];
	$last_month_performance=$last_month_data['total_production']/$last_month_data['total_target'];
	
	$current_month_efficiency=$current_month_data['total_product_min']/$current_month_data['total_target_min'];
	$current_month_performance=$current_month_data['total_production']/$current_month_data['total_target'];
	$chart_line_arr= json_encode($chart_line_arr);
	$chart_line_reject_data_arr= json_encode($chart_line_reject_data_arr);
	$chart_line_data_arr= json_encode($chart_line_data_arr);
	
	//print_r($chart_line_arr);die;	
	?>
    <div align="left"> 
    	 <style>
            .tdstyle
            {
                background-image: rgb(156, 170, 234) 96%);
                border: 1px solid #8dafda;
                color: #444;
                font-size: 13px;
                font-weight: bold;
                height: 25px;
                line-height: 12px;
                text-align: center;
            }
            
            .tdstyleeven
            {
                background-image: linear-gradient(rgb(100, 200, 255) 10%, rgb(100, 180, 234) 96%);
                border: 1px solid #8dafda;
                color: #444;
                font-size: 13px;
                font-weight: bold;
                height: 25px;
                line-height: 12px;
                text-align: center;
            }
          
          </style> 
     
       <?php 
	   if($bundle_copyes==1)
	   {
	   
		   $width=700; ?>
			<div style="width:<?php  echo $width; ?>px; height:580px;  margin-left:10px; border:solid 1px;  float:left">
				<table style="min-width:400px; font-size:12px" align="center">
						<tr>
							<td colspan="14"><b>Current Month Line Wise Efficiency Graph</b></td>
						 </tr>
						 <tr>
							<td bgcolor="#FF6A6A" width="16"></td>
							<td colspan="2" ><b>Line Efficiency</b></td>
							<td bgcolor="#00D2D2" width="16"></td>
							<td colspan="3" ><b>Alter  Percentage</b></td>
						</tr>
						
					</table>
					<canvas id="canvas1" height="380" width="<?php  echo $width; ?>" ></canvas>
			</div>
			
		</div>
		
		<style>
				#canvas1 {
					font-size	: 11px;
				}					
			</style>
			<script src="../Chart.js-master/Chart.js"></script>
		
			<script >
				
				var barChartData2 = {
				labels : <?php echo $chart_line_arr; ?>,
				barPercentage: 0.5,
				datasets : [
						{
							fillColor : "#FF6A6A",
							//strokeColor : "#40FF9F",
							//highlightFill: "#996666",
							//highlightStroke: "#35BDFF",
							data : <?php echo $chart_line_data_arr; ?>
						},
						{
							fillColor : "#00D2D2",
							//strokeColor : "#FFFF00",
							//highlightFill: "#996666",
							//highlightStroke: "#35BDFF",
							data : <?php echo $chart_line_reject_data_arr; ?>
						}
					]
				}
				
				var ctx2 = document.getElementById("canvas1").getContext("2d");
				window.myBar = new Chart(ctx2).Bar(barChartData2, {
					responsive : true
				});	
			
			
		</script>

<?    
   }
   else
   {
   	?>
		<table style="width:700px; font-size:12px" align="center">
			<tr>
				<td>
					<div id="div_canvas1">
						<table style="width:350px; font-size:12px" align="center">
							<tr>
								<td colspan="14"><b>Current Month Line Wise Efficiency Graph</b></td>
							 </tr>
							 <tr>
								<td bgcolor="#FF6A6A" width="16"></td>
								<td colspan="2" ><b>Line Efficiency</b></td>
								<td bgcolor="#00D2D2" width="16"></td>
								<td colspan="3" ><b>Alter Percentage</b></td>
							</tr>
							
						</table>
						<canvas id="canvas1"></canvas>
					</div>
				
				</td>
				<td>
					<div id="div_canvas2">
						<table style="width:350px; font-size:12px" align="center">
							<tr>
								<td colspan="14"><b>Current Month Line Wise Efficiency Graph</b></td>
							 </tr>
							 <tr>
								<td bgcolor="#FF6A6A" width="16"></td>
								<td colspan="2" ><b>Line Efficiency</b></td>
								<td bgcolor="#00D2D2" width="16"></td>
								<td colspan="3" ><b>Reject Percentage</b></td>
							</tr>
							
						</table>
						<canvas id="canvas2" ></canvas>
					</div>
				</td>
			 </tr>
			 <tr>
				<td>
					<div  id="div_canvas3">
						<table style="width:350px; font-size:12px" align="center">
							<tr>
								<td colspan="14"><b>Current Month Line Wise Efficiency Graph</b></td>
							 </tr>
							 <tr>
								<td bgcolor="#FF6A6A" width="16"></td>
								<td colspan="2" ><b>Line Efficiency</b></td>
								<td bgcolor="#00D2D2" width="16"></td>
								<td colspan="3" ><b>Alter Percentage</b></td>
							</tr>
							
						</table>
						<canvas id="canvas3" ></canvas>
					</div>
				
				</td>
				<td>
					<div id="div_canvas4">
						<table style="width:350px; font-size:12px" align="center">
							<tr>
								<td colspan="14"><b>Current Month Line Wise Efficiency Graph</b></td>
							 </tr>
							 <tr>
								<td bgcolor="#FF6A6A" width="16"></td>
								<td colspan="2" ><b>Line Efficiency</b></td>
								<td bgcolor="#00D2D2" width="16"></td>
								<td colspan="3" ><b>Alter Percentage</b></td>
							</tr>
							
						</table>
						<canvas id="canvas4" ></canvas>
					</div>
				</td>
			 </tr>	 
			
			
		</div>
		
		<style>
				#div_canvas1 {

					height: 400px;
   					border: 1px solid black;
					width:350px;
					border:solid 1px; 
					float:left;
				}
				
				#div_canvas2 {
    				width:350px;
					height:400px;
					margin-left:20px;
					border:solid 1px black; 
					float:left
				}
				
				#div_canvas3 {
    				width:350px;
					height:400px;
					margin-top:20px;
					border:solid 1px black; 
					float:left
				}
				
				#div_canvas4 {
    				width:350px;
					height:400px;
					margin-left:20px;
					margin-top:20px;
					border:solid 1px black; 
					float:left
				}
									
			</style>
			<script src="../Chart.js-master/Chart.js"></script>
		
			<script >
				
				var barChartData2 = {
				labels : <?php echo $chart_line_arr; ?>,
				barPercentage: 0.5,
				datasets : [
						{
							fillColor : "#FF6A6A",
							//strokeColor : "#40FF9F",
							//highlightFill: "#996666",
							//highlightStroke: "#35BDFF",
							data : <?php echo $chart_line_data_arr; ?>
						},
						{
							fillColor : "#00D2D2",
							//strokeColor : "#FFFF00",
							//highlightFill: "#996666",
							//highlightStroke: "#35BDFF",
							data : <?php echo $chart_line_reject_data_arr; ?>
						}
					]
				}
				
				var ctx2 = document.getElementById("canvas1").getContext("2d");
				window.myBar = new Chart(ctx2).Bar(barChartData2, {
						responsive : true,
						options: {
						maintainAspectRatio: false,
					}
				});	
			
			/*var barChartData2 = {
				labels : <?php echo $chart_line_arr; ?>,
				barPercentage: 0.5,
				datasets : [
						{
							fillColor : "#FF6A6A",
							//strokeColor : "#40FF9F",
							//highlightFill: "#996666",
							//highlightStroke: "#35BDFF",
							data : <?php echo $chart_line_data_arr; ?>
						},
						{
							fillColor : "#00D2D2",
							//strokeColor : "#FFFF00",
							//highlightFill: "#996666",
							//highlightStroke: "#35BDFF",
							data : <?php echo $chart_line_reject_data_arr; ?>
						}
					]
				}
				*/
				var ctx2 = document.getElementById("canvas2").getContext("2d");
				window.myBar = new Chart(ctx2).Bar(barChartData2, {
					responsive : true,
					options: {
						maintainAspectRatio: false,
					}
				});
				
				/*var barChartData2 = {
				labels : <?php echo $chart_line_arr; ?>,
				barPercentage: 0.5,
				datasets : [
						{
							fillColor : "#FF6A6A",
							//strokeColor : "#40FF9F",
							//highlightFill: "#996666",
							//highlightStroke: "#35BDFF",
							data : <?php echo $chart_line_data_arr; ?>
						},
						{
							fillColor : "#00D2D2",
							//strokeColor : "#FFFF00",
							//highlightFill: "#996666",
							//highlightStroke: "#35BDFF",
							data : <?php echo $chart_line_reject_data_arr; ?>
						}
					]
				}*/
				
				var ctx2 = document.getElementById("canvas3").getContext("2d");
				window.myBar = new Chart(ctx2).Bar(barChartData2, {
					responsive : true,
					options: {
						maintainAspectRatio: false,
					}
				});
				
				/*var barChartData2 = {
				labels : <?php echo $chart_line_arr; ?>,
				barPercentage: 0.5,
				datasets : [
						{
							fillColor : "#FF6A6A",
							//strokeColor : "#40FF9F",
							//highlightFill: "#996666",
							//highlightStroke: "#35BDFF",
							data : <?php echo $chart_line_data_arr; ?>
						},
						{
							fillColor : "#00D2D2",
							//strokeColor : "#FFFF00",
							//highlightFill: "#996666",
							//highlightStroke: "#35BDFF",
							data : <?php echo $chart_line_reject_data_arr; ?>
						}
					]
				}*/
				
				var ctx2 = document.getElementById("canvas4").getContext("2d");
				window.myBar = new Chart(ctx2).Bar(barChartData2, {
					responsive : true,
					options: {
						maintainAspectRatio: false,
					}
				});
			
		</script>

  <?php
   
   
   
   }

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
                   $item_smv_pop=explode(",",$item_smv);
				   $order_id="";
				   foreach($item_smv_pop as $po_id_smv) 
				 {
					   $po_id_smv_pop=explode("**",$po_id_smv);
					   $new_smv[$po_id_smv_pop[0]]=$po_id_smv_pop[1];
				 }
				//print_r($new_smv);	
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
		//echo $sqlPo;die;
		$po_no='';$po_ids='';
		$dataPo=sql_select($sqlPo);
		foreach( $dataPo as $row)
		{
			if($po_no!='') $po_no.=",".$row[csf('po_number')];else $po_no=$row[csf('po_number')];
			if($po_ids!='') $po_ids.=",".$row[csf('po_id')];else $po_ids=$row[csf('po_id')];
			
			
			$set_smv=$row[csf('set_smv')];
			$job_no=$row[csf('job_no')];
			$buyer_name=$buyerArr[$row[csf('buyer_name')]];
			
		}
		
		$germents_id=array_unique(explode(",",$item_id));
		$item_name=''; $item_id_arr=array();
		foreach($germents_id as $g_val)
		{
			$item_id_arr[$g_val]=$g_val;
			if($item_name!='') $item_name.=",".$garments_item[$g_val];else $item_name=$garments_item[$g_val];
			
		}
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
					
					$sql.="from pro_garments_production_mst where po_break_down_id in(".$po_ids.") and item_number_id in (".implode(",",$item_id_arr).") and floor_id='".$floor_id."' and sewing_line='".$sewing_line."' and prod_reso_allo='".$prod_reso_allo."' and production_type=5 and production_date='$prod_date' and is_deleted=0 and status_active=1";
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
					
					$sql.="from pro_garments_production_mst where po_break_down_id in(".$po_ids.") and item_number_id in (".implode(",",$item_id_arr).") and floor_id='".$floor_id."' and sewing_line='".$sewing_line."' and prod_reso_allo='".$prod_reso_allo."' and production_type=5 and production_date='$prod_date' and is_deleted=0 and status_active=1";
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
?>