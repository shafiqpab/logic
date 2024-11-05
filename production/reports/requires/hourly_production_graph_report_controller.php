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
	order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/hourly_production_graph_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/hourly_production_graph_report_controller' );",0 ); 
	exit();    	 
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_id", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=5 
	and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );     	 	
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

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//--------------------------------------------------


	$comp_arr=return_library_array("select id,company_name from lib_company", "id","company_name");
	$loc_name_arr=return_library_array("select id,location_name from lib_location", "id","location_name");
	$floor_name_arr=return_library_array("select id,floor_name from lib_prod_floor", "id","floor_name");
	
	
	
	
	$txt_date=str_replace("'","",$txt_date);
	//$company = str_replace("'","",$cbo_company_id);
	$pro_company = str_replace("'","",$cbo_company_id);
	
	if( $company!=0 )
	{
		$str_comp=" and comp.id=$company";
		$comp_name="Company : ".$comp_arr[$company].";";
	}
	else if( $pro_company!=0 )
	{
		$str_comp=" and comp.id=$pro_company";
		$comp_name="Company : ".$comp_arr[$pro_company].";";
	}
	else
	{
		$str_comp="";
		$comp_name="";
	}
		
	$_SESSION['logic_erp']["data"]='';
	
	
	?>	
		<script src="../../Chart.js-master/Chart.js"></script>
	 
		<div class="wrap" style="margin:0 auto;overflow:hidden; padding:10px;">
			<div style="width:950px; height:300px; position:relative;margin-top:5px;margin-left:10px; border:solid 1px">
				<table style="margin-left:60px; font-size:12px">
					<tr>
						<td colspan="8" id="x">Today Hourly Production</td>
					</tr>
					<tr>
						<td bgcolor="#FF3300" width="15"></td>
						<td>Target</td>
						<td bgcolor="#0066FF" width="15"></td>
						<td>Sewing</td>
						<td bgcolor="#884800" width="15"></td>
						<td>Sewing Rejection</td>
						<td bgcolor="#C846C9" width="15"></td>
						<td>Poly</td>
					</tr>
				</table>
				<canvas id="canvas" height="240" width="900"></canvas>
			</div>
			<?
			if($db_type==0)
			{
				$today=date('Y-m-d',strtotime($txt_date));
				$manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $str_comp $company_cond","company_id");
			}
			else
			{
				$today=date('d-M-Y',strtotime($txt_date));
				$manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $str_comp $company_cond","company_id");
			}
			 
			
			$start_time_arr=array();
			if($db_type==0)
			{
				$start_time_data_arr=sql_select("select company_name, shift_id, TIME_FORMAT( prod_start_time, '%H:%i' ) as prod_start_time, TIME_FORMAT( lunch_start_time, '%H:%i' ) as lunch_start_time from variable_settings_production where company_name in($manufacturing_company) and variable_list=26 and status_active=1 and is_deleted=0");
			}
			else
			{
				$start_time_data_arr=sql_select("select company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where company_name in($manufacturing_company) and variable_list=26 and status_active=1 and is_deleted=0");	
			}
			
			foreach($start_time_data_arr as $row)
			{
				$start_time_arr[$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
				$start_time_arr[$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
			}
			
			$prod_reso_allocation=return_field_value("auto_update","variable_settings_production","company_name in($manufacturing_company) and variable_list=23 and is_deleted=0 and status_active=1");
			
			$tph=0; $lineWiseTph=array(); $lineArr=array(); $loc_arr=array(); $company_id_arr=array();
			$lineDataArr=sql_select( "select id, line_name, company_name, location_name,floor_name from lib_sewing_line where company_name in ($manufacturing_company) and status_active=1 and is_deleted=0");
			foreach($lineDataArr as $row)
			{
				$lineArr[$row[csf('id')]]=$row[csf('line_name')];
				$loc_arr[$row[csf('id')]]=$row[csf('location_name')];
				$company_id_arr[$row[csf('id')]]=$row[csf('company_name')];
				$floor_id_arr[$row[csf('id')]]=$row[csf('floor_name')];
			}
			
			if($prod_reso_allocation==1)
			{
				if($db_type==0)
				{
					$line_sql="select b.id, b.line_number, c.sewing_line_serial, c.location_name, sum(a.target_per_hour) tph from prod_resource_dtls a, prod_resource_mst b, lib_sewing_line c where b.id=a.mst_id and b.line_number=c.id and b.company_id in($manufacturing_company) and a.pr_date='$today' and a.is_deleted=0 and b.is_deleted=0 group by b.id, b.line_number, a.pr_date, c.sewing_line_serial order by c.sewing_line_serial"; // $str_location
				}
				else
				{
					 $line_sql="select b.id, b.line_number, c.sewing_line_serial, c.location_name, sum(a.target_per_hour) tph from prod_resource_dtls a, prod_resource_mst b, lib_sewing_line c where b.id=a.mst_id and b.line_number=to_char(c.id) and b.company_id in($manufacturing_company) and a.pr_date='$today' and a.is_deleted=0 and b.is_deleted=0 group by b.id, b.line_number, a.pr_date, c.sewing_line_serial, c.location_name order by c.sewing_line_serial";// $str_location
				}
				
				$lineData=sql_select($line_sql);
				foreach($lineData as $row)
				{
					$tph+=$row[csf('tph')];
					$lineWiseTph[$row[csf('id')]]=$row[csf('tph')];
					$lineResArr[$row[csf('id')]]=$row[csf('line_number')];
				}
				
				
				$tph_sql=sql_select("select b.id, b.line_number, sum(a.target_per_hour) tph from prod_resource_dtls a, prod_resource_mst b where b.id=a.mst_id and b.company_id in($manufacturing_company) and a.pr_date='$today' and a.is_deleted=0 and b.is_deleted=0 group by b.id, b.line_number, a.pr_date");
				foreach($tph_sql as $row)
				{
					if(!array_key_exists($row[csf('id')],$lineResArr))
					{ 
						$tph+=$row[csf('tph')];
						$lineWiseTph[$row[csf('id')]]=$row[csf('tph')];
						$lineResArr[$row[csf('id')]]=$row[csf('line_number')];
					}
				}
				$linedataArr=$lineResArr;
			}
			else
			{
				$linedataArr=$lineArr;
			}
	
			$prod_start_hour=$start_time_arr[1]['pst'];
			if($prod_start_hour=="") $prod_start_hour="08:00";
			$start_time=explode(":",$prod_start_hour);
			$hour=$start_time[0]; $minutes=$start_time[1]; $last_hour=23;
			$lineWiseProd_arr=array(); $prod_arr=array(); $start_hour_arr=array();
			$lineWiseProd_rej_arr=array();
			
			$start_hour=$prod_start_hour;
			for($j=$hour;$j<$last_hour;$j++)
			{
				$start_hour=add_time($start_hour,60);
				$start_hour_arr[$j+1]=$start_hour;
			}
			$start_hour_arr[$j+1]='23:59:59';
	
			$z=(int)$hour; $s=1;
			$sql="SELECT sewing_line,"; $sql_subconProd="select line_id, ";
			if($db_type==2)
			{
				foreach($start_hour_arr as $val)
				{
					$z++;
					if($s==1)
					{
						$sql.=" sum(case when TO_CHAR(production_hour,'HH24:MI:SS')<='$val' and production_type=5 then production_quantity else 0 end) AS am$z, 
								sum(case when TO_CHAR(production_hour,'HH24:MI:SS')<='$val' and production_type=5 then reject_qnty else 0 end) AS sr$z , 
								sum(case when TO_CHAR(production_hour,'HH24:MI:SS')<='$val' and production_type=11 then production_quantity else 0 end) AS pq$z ";
						$sql_subconProd.=" sum(case when TO_CHAR(hour,'HH24:MI:SS')<='$val' then production_qnty else 0 end) AS am$z,  
										   sum(case when TO_CHAR(hour,'HH24:MI:SS')<='$val' then reject_qnty else 0 end) AS sr$z ";
					}
					else
					{
						$sql.=", sum(case when TO_CHAR(production_hour,'HH24:MI:SS')>'$prev_hour' and TO_CHAR(production_hour,'HH24:MI:SS')<='$val' and production_type=5 then production_quantity else 0 end) AS am$z , 
								sum(case when TO_CHAR(production_hour,'HH24:MI:SS')>'$prev_hour' and TO_CHAR(production_hour,'HH24:MI:SS')<='$val' and production_type=5 then reject_qnty else 0 end) AS sr$z  , 
								sum(case when TO_CHAR(production_hour,'HH24:MI:SS')>'$prev_hour' and TO_CHAR(production_hour,'HH24:MI:SS')<='$val' and production_type=11 then production_quantity else 0 end) AS pq$z ";
						
						$sql_subconProd.=", sum(case when TO_CHAR(hour,'HH24:MI:SS')>'$prev_hour' and TO_CHAR(hour,'HH24:MI:SS')<='$val' then production_qnty else 0 end) AS am$z , 
											sum(case when TO_CHAR(hour,'HH24:MI:SS')>'$prev_hour' and TO_CHAR(hour,'HH24:MI:SS')<='$val' then reject_qnty else 0 end) AS sr$z ";
					}
					
					$prev_hour=$val;
					$s++;
				}
			}
			else
			{
				foreach($start_hour_arr as $val)
				{
					$z++;
					if($s==1)
					{
						$sql.=" sum(case when production_hour<='$val' and production_type=5 then production_quantity else 0 end) AS am$z ,
								sum(case when production_hour<='$val' and production_type=5 then reject_qnty else 0 end) AS sr$z , 
								sum(case when production_hour<='$val' and production_type=11 then production_quantity else 0 end) AS pq$z
								";
						$sql_subconProd.=" sum(case when hour<='$val' then production_qnty else 0 end) AS am$z , 
										   sum(case when hour<='$val' then reject_qnty else 0 end) AS sr$z ";
					}
					else
					{
						$sql.=", sum(case when production_hour>'$prev_hour' and production_hour<='$val' and production_type=5 then production_quantity else 0 end) AS am$z , 
								sum(case when production_hour>'$prev_hour' and production_hour<='$val' and production_type=5 then reject_qnty else 0 end) AS sr$z , 
								sum(case when production_hour>'$prev_hour' and production_hour<='$val' and production_type=11 then production_quantity else 0 end) AS pq$z ";
						$sql_subconProd.=", sum(case when hour>'$prev_hour' and hour<='$val' then production_qnty else 0 end) AS am$z , 
											sum(case when hour>'$prev_hour' and hour<='$val' then reject_qnty else 0 end) AS sr$z ";
					}
					
					$prev_hour=$val;
					$s++;
				}
				
			}
			//swithc company here.................................start;
				if($pro_company!=0){$companyCap ="serving_company";}
				else if($company!=0){$companyCap ="company_id";}
			//swithc company here.................................end;
			
			$sql.=" from pro_garments_production_mst where production_type in(5,11) and company_id in($manufacturing_company) and production_date='$today' and is_deleted=0 and status_active=1 group by sewing_line";
			
			$sql_subconProd.=" from subcon_gmts_prod_dtls WHERE company_id in($manufacturing_company) and production_date='$today' and production_type=2 and status_active=1 and is_deleted=0 group by line_id";
			
			//echo $sql_subconProd;
			
			$sew_data_arr=sql_select($sql);
			foreach($sew_data_arr as $row)
			{
				for($j=$hour+1;$j<=$last_hour;$j++)
				{
					$lineWiseProd_arr[$row[csf('sewing_line')]]['am'.$j]=$row[csf('am'.$j)];
					$prod_arr['am'.$j]+=$row[csf('am'.$j)];
					
					$lineWiseProd_rej_arr[$row[csf('sewing_line')]]['sr'.$j]=$row[csf('sr'.$j)];
					$prod_rej_arr['sr'.$j]+=$row[csf('sr'.$j)];
					
					$lineWiseProd_pq_arr[$row[csf('sewing_line')]]['pq'.$j]=$row[csf('pq'.$j)];
					$prod_pq_arr['pq'.$j]+=$row[csf('pq'.$j)];
				}
				
				$prod_arr['am24']+=$row[csf('am24')];
				$lineWiseProd_arr[$row[csf('sewing_line')]]['am24']=$row[csf('am24')];
				
				$prod_rej_arr['sr24']+=$row[csf('sr24')];
				$lineWiseProd_rej_arr[$row[csf('sewing_line')]]['sr24']=$row[csf('sr24')];
				
				$prod_pq_arr['pq24']+=$row[csf('pq24')];
				$lineWiseProd_pq_arr[$row[csf('sewing_line')]]['pq24']=$row[csf('pq24')];
			}
			
			$subconProdData=sql_select($sql_subconProd);
			foreach($subconProdData as $subRow)
			{
				for($j=$hour+1;$j<=$last_hour;$j++)
				{
					$lineWiseProd_arr[$subRow[csf('line_id')]]['am'.$j]+=$subRow[csf('am'.$j)];
					$prod_arr['am'.$j]+=$subRow[csf('am'.$j)];
					
					$lineWiseProd_rej_arr[$subRow[csf('line_id')]]['sr'.$j]+=$subRow[csf('sr'.$j)];
					$prod_rej_arr['sr'.$j]+=$subRow[csf('sr'.$j)];
				}
				
				$lineWiseProd_arr[$subRow[csf('line_id')]]['am24']+=$subRow[csf('am24')];
				$prod_arr['am24']+=$subRow[csf('am24')];
				
				$lineWiseProd_rej_arr[$subRow[csf('line_id')]]['sr24']+=$subRow[csf('sr24')];
				$prod_rej_arr['sr24']+=$subRow[csf('sr24')];
			}
			
			
			
			
			$hour_array=array(); $sewTphArr=array(); $lineWiseSewTphArr=array(); $sewProdArr=array(); 
			$sewProdRejArr=array(); $sewProdPqArr=array();
			for($j=$hour+1;$j<=$last_hour;$j++)
			{
				$hour_array[]=substr($start_hour_arr[$j],0,5);
				$sewTphArr[]=number_format($tph,0,'.','');
				$production_quantity=$prod_arr['am'.$j];
				$sewProdArr[]=number_format($production_quantity,0,'.','');
				
				$production_rej_quantity=$prod_rej_arr['sr'.$j];
				$sewProdRejArr[]=number_format($production_rej_quantity,0,'.','');
				
				$production_pq_quantity=$prod_pq_arr['pq'.$j];
				$sewProdPqArr[]=number_format($production_pq_quantity,0,'.','');
			}
			
			$sewTphArr[]=number_format($tph,0,'.','');
			$hour_array[]=substr($start_hour_arr[24],0,5);
			$production_quantity=$prod_arr['am24'];
			$sewProdArr[]=number_format($production_quantity,0,'.','');
			
			$production_rej_quantity=$prod_rej_arr['sr24'];
			$sewProdRejArr[]=number_format($production_rej_quantity,0,'.','');
			
			$production_pq_quantity=$prod_pq_arr['pq24'];
			$sewProdPqArr[]=number_format($production_pq_quantity,0,'.','');
			
			$hour_array= json_encode($hour_array);
			$sewTphArr=json_encode($sewTphArr);
			$sewProdArr= json_encode($sewProdArr);
			$sewProdRejArr= json_encode($sewProdRejArr);
			$sewProdPqArr= json_encode($sewProdPqArr);
			
		?>
		<script>
			function show_details(line_id,line_name)
			{
				page_link='../../today_hourly_prod_popup.php?line_id='+line_id+'&line_name='+line_name+'&action=today_hourly_prod_popup';
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Detail View', 'width=900px, height=400px, center=1, resize=0, scrolling=0','');
			}
			
			var lineChartData = {
				labels : <? echo $hour_array; ?>,
				datasets : [
					{
						//label: "My First dataset",
						fillColor : "rgba(220,220,220,0.2)",
						strokeColor : "#FF3300",
						pointColor : "#FF3300",
						pointStrokeColor : "#fff",
						pointHighlightFill : "#fff",
						pointHighlightStroke : "#FF3300",
						data : <? echo $sewTphArr; ?>
					}
					,
					{
						//label: "My Second dataset",
						fillColor : "rgba(151,187,205,0.2)",
						strokeColor : "#0066FF",
						pointColor : "#0066FF",
						pointStrokeColor : "#fff",
						pointHighlightFill : "#fff",
						pointHighlightStroke : "#0066FF",
						data : <? echo $sewProdArr; ?>
					}
					,
					{
						//label: "My Second dataset",
						fillColor : "rgba(155,185,205,0.2)",
						strokeColor : "#884800",
						pointColor : "#884800",
						pointStrokeColor : "#fff",
						pointHighlightFill : "#fff",
						pointHighlightStroke : "#0066FF",
						data : <? echo $sewProdRejArr; ?>
					}
					,
					{
						//label: "My Second dataset",
						fillColor : "rgba(150,180,200,0.2)",
						strokeColor : "#C846C9",
						pointColor : "#C846C9",
						pointStrokeColor : "#fff",
						pointHighlightFill : "#fff",
						pointHighlightStroke : "#0066FF",
						data : <? echo $sewProdPqArr; ?>
					}
					
					
					
				]
	
			}
			
		   // window.onload = function(){
				var ctx = document.getElementById("canvas").getContext("2d");
				window.myLine = new Chart(ctx).Line(lineChartData, {
					responsive: true
				});
		   // }
		  
		</script>
		<?  //var_dump($linedataArr);
			$i=0;
			foreach($linedataArr as $line_id=>$lineName)
			{
				$i++;
				$sewing_line='';
				if($prod_reso_allocation==1)
				{
					$line_number=explode(",",$lineName);
					foreach($line_number as $val)
					{
						if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
						$line_id_for_loc=$val;
					}
				}
				else
				{
					$sewing_line=$lineName;
					$line_id_for_loc=$lineName;
				}
				
				$lineWiseSewTphArr=array(); $lineWiseSewProdArr=array();  
				$lineWiseSewProdRejArr=array(); $lineWiseSewProdPqArr=array();
				$ltph=$lineWiseTph[$line_id];
				for($j=$hour+1;$j<=$last_hour;$j++)
				{
					$lineWiseSewTphArr[]=number_format($ltph,0,'.','');
					$lineProdQty=$lineWiseProd_arr[$line_id]['am'.$j];
					$lineWiseSewProdArr[]=number_format($lineProdQty,0,'.','');
					
					$lineProdRejQty=$lineWiseProd_rej_arr[$line_id]['sr'.$j];
					$lineWiseSewProdRejArr[]=number_format($lineProdRejQty,0,'.','');
					
					$lineProdPqQty=$lineWiseProd_pq_arr[$line_id]['pq'.$j];
					$lineWiseSewProdPqArr[]=number_format($lineProdPqQty,0,'.','');
				}
				
				$location_name="Location : ".$loc_name_arr[$loc_arr[$line_id_for_loc]];
				$floor_name="Floor : ".$floor_name_arr[$floor_id_arr[$line_id_for_loc]];
				
				$lineWiseSewTphArr[]=number_format($ltph,0,'.','');
				$lineProdQty=$lineWiseProd_arr[$line_id]['am24'];
				$lineWiseSewProdArr[]=number_format($lineProdQty,0,'.','');
				
				$lineProdRejQty=$lineWiseProd_rej_arr[$line_id]['sr24'];
				$lineWiseSewProdRejArr[]=number_format($lineProdRejQty,0,'.','');
				
				$lineProdPqQty=$lineWiseProd_pq_arr[$line_id]['pq24'];
				$lineWiseSewProdPqArr[]=number_format($lineProdPqQty,0,'.','');
				 
				
				
				$lineWiseSewTphArr=json_encode($lineWiseSewTphArr);
				$lineWiseSewProdArr= json_encode($lineWiseSewProdArr);
				$lineWiseSewProdRejArr= json_encode($lineWiseSewProdRejArr);
				$lineWiseSewProdPqArr = json_encode($lineWiseSewProdPqArr);
				
				
				
				if($location==0 || $location==$loc_arr[$line_id_for_loc])	
				{
				?>
					<div style="width:469px; height:240px; float:left; position:relative; margin-left:10px; margin-top:5px; border:solid 1px">
						<table style="margin-left:20px; font-size:12px;">
							<tr>
								<td colspan="8" align="char">
								<b><? echo "Company : ".$comp_arr[$company_id_arr[$line_id_for_loc]]; ?>, <? echo $location_name; ?>, <? echo $floor_name; ?>, Line No:<? echo $sewing_line; ?></b>
								<br />Today Hourly Production
								<a href="##" style="text-decoration:none"><input type="button" value="Details" name="a" id="a" class="formbutton" style="width:60px" onclick="show_details(<? echo $line_id; ?>,'<? echo $sewing_line; ?>');"/></a>
								</td>
							</tr>
							<tr>
								<td bgcolor="#FF3300" width="15"></td>
								<td width="80">Target</td>
								<td bgcolor="#0066FF" width="15"></td>
								<td width="80">Sewing</td>
								<td bgcolor="#884800" width="15"></td>
								<td width="80">Sewing Reject</td>
								<td bgcolor="#C846C9" width="15"></td>
								<td>Poly</td>
							</tr>
						</table>
						<canvas id="canvas<? echo $i; ?>" height="165" width="450"></canvas>
					</div>
					<script>
						var lineChartData2 = {
							labels : <? echo $hour_array; ?>,
							datasets : [
								{
									//label: "My First dataset",
									fillColor : "rgba(220,220,220,0.2)",
									strokeColor : "#FF3300",
									pointColor : "#FF3300",
									pointStrokeColor : "#fff",
									pointHighlightFill : "#fff",
									pointHighlightStroke : "#FF3300",
									data : <? echo $lineWiseSewTphArr; ?>
								}
								,
								{
									//label: "My Second dataset",
									fillColor : "rgba(151,187,205,0.2)",
									strokeColor : "#0066FF",
									pointColor : "#0066FF",
									pointStrokeColor : "#fff",
									pointHighlightFill : "#fff",
									pointHighlightStroke : "#0066FF",
									data : <? echo $lineWiseSewProdArr; ?>
								}
								,
								{
									//label: "My Second dataset",
									fillColor : "rgba(151,187,205,0.2)",
									strokeColor : "#884800",
									pointColor : "#884800",
									pointStrokeColor : "#fff",
									pointHighlightFill : "#fff",
									pointHighlightStroke : "#0066FF",
									data : <? echo $lineWiseSewProdRejArr; ?>
								}
								,
								{
									//label: "My Second dataset",
									fillColor : "rgba(151,187,205,0.2)",
									strokeColor : "#C846C9",
									pointColor : "#C846C9",
									pointStrokeColor : "#fff",
									pointHighlightFill : "#fff",
									pointHighlightStroke : "#0066FF",
									data : <? echo $lineWiseSewProdPqArr; ?>
								}
							]
				
						}
						
						//window.onload = function(){
							var ctx = document.getElementById("canvas<? echo $i; ?>").getContext("2d");
							window.myLine = new Chart(ctx).Line(lineChartData2, {
								responsive: true
							});
						//}
					</script>
				<?
				}
			}

	?>
	</div>
    
    
<? 
 	
	//............................................

	exit();      
}
//First Button end

?>