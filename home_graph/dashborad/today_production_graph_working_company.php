<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Today Production Graph.
Functionality	:	
JS Functions	:
Created by		:	Reza 
Creation date 	: 	
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
require_once('../../includes/common.php');
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("GRAPH PAGE","../../", 1, 1, $unicode,'','');
extract( $_REQUEST );
$m= base64_decode($m);
list($company,$pro_company,$location,$floor)=explode("__",$cp);


$comp_arr=return_library_array("select id,company_name from lib_company", "id","company_name");
$loc_name_arr=return_library_array("select id,location_name from lib_location", "id","location_name");
$floor_name_arr=return_library_array("select id,floor_name from lib_prod_floor", "id","floor_name");
	
	
	
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

	if( $location!=0 )
	{
		$location_con=" and c.location_name=$location";
		$location_con_2=" and location=$location";
		$location_con_3=" and location_id=$location";
		$location_con_4=" and location_name=$location";
	}
	else
	{
		$location_con="";
	}

	if( $floor!=0 )
	{
		$floor_con=" and c.floor_name=$floor";
		$floor_con_2=" and floor_id=$floor";
		$floor_con_3=" and floor_name=$floor";
		$floor_con_4=" and floor_id=$floor";
	}
	else
	{
		$floor_con="";
	}

	
	
$_SESSION['logic_erp']["data"]='';

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_home", 160, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- All --", '', "load_drop_down( 'home_graph/dashborad/today_production_graph_working_company',$('#cbo_company_home').val()+'_'+$('#cbo_prod_company_home').val()+'_'+this.value, 'load_drop_down_floor', 'sp_floor');" );	
	exit();	 
} 

if ($action=="load_drop_down_floor")
{ 
	list($lcCompany,$workingCompany,$location)=explode('_',$data);
	$company=($workingCompany)?$workingCompany:$lcCompany;
	echo create_drop_down( "cbo_floor", 100, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id=$company and location_id=$location order by floor_name","id,floor_name", 1, "-- All --", '', "" );	
	
	
//this part for only urmi FAKHRUDDIN TEXTILE MILLS LTD..........................................	
/*	$unite_1=array(14,20,24,37);
	$unite_2=array(7,15,19);
	$prod_start_time=sql_select("select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id=$company and location_id=$location order by floor_name");
	foreach($prod_start_time as $row)
	{
		
		if(in_array($row[csf('id')],$unite_1) && $comp_arr[1]=='FAKHRUDDIN TEXTILE MILLS LTD.'){
			$floorArr[implode(',',$unite_1)]='Unit-1';
		}
		else if(in_array($row[csf('id')],$unite_2) && $comp_arr[1]=='FAKHRUDDIN TEXTILE MILLS LTD.'){
			$floorArr[implode(',',$unite_2)]='Unit-2';
		}
		else
		{
			$floorArr[$row[csf('id')]]=$row[csf('floor_name')];
		}
	}
	asort($floorArr);
	echo create_drop_down( "cbo_floor", 100, $floorArr,"", 1, "-- All --", $selected, "",0 );
*/	
	
	
	exit();	 
} 
 

?>	
    <script src="../../Chart.js-master/Chart.js"></script>
    <!--<div style="margin-left:15px; margin-top:10px"><a href="index.php">Main Page</a>&nbsp;&nbsp;<a href="index.php?g=1">Dash Board</a>&nbsp;&nbsp;<a href="index.php?g=3">Trend Monthly</a>&nbsp;&nbsp;<a href="index.php?g=4">Trend Daily</a></div>-->
	
        <?
        if($db_type==0)
        {
			$today=date('Y-m-d');
            $manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $str_comp $company_cond","company_id");
        }
        else
        {
			$today=date('d-M-Y');
			//$today="4-Mar-2017";
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
			/*$strat_time_arr[$row[csf('company_name')]][$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
			$strat_time_arr[$row[csf('company_name')]][$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];*/
		}
		
		
		$prod_reso_allocation=return_field_value("auto_update","variable_settings_production","company_name in($manufacturing_company) and variable_list=23 and is_deleted=0 and status_active=1");
		
        $tph=0; $lineWiseTph=array(); $lineArr=array(); $loc_arr=array(); $company_id_arr=array();
		$lineDataArr=sql_select( "select id, line_name,floor_name, company_name, location_name from lib_sewing_line where company_name in ($manufacturing_company) and status_active=1 and is_deleted=0 $location_con_4 $floor_con_3 ");
		foreach($lineDataArr as $row)
		{
			$lineArr[$row[csf('id')]]=$row[csf('line_name')];
			$loc_arr[$row[csf('id')]]=$row[csf('location_name')];
			$floor_arr[$row[csf('id')]]=$row[csf('floor_name')];
			$company_id_arr[$row[csf('id')]]=$row[csf('company_name')];
		}
		
		
		
		if($prod_reso_allocation==1)
		{
			if($db_type==0)
			{
				$line_sql="select b.id, b.line_number, c.sewing_line_serial, c.location_name, sum(a.target_per_hour) tph from prod_resource_dtls a, prod_resource_mst b, lib_sewing_line c where b.id=a.mst_id and b.line_number=c.id and b.company_id in($manufacturing_company) and a.pr_date='$today' and a.is_deleted=0 and b.is_deleted=0 $location_con $floor_con group by b.id, b.line_number, a.pr_date, c.sewing_line_serial order by c.company_name,c.location_name,c.floor_name,c.sewing_line_serial"; // $str_location
			}
			else
			{
				 $line_sql="select c.company_name,c.location_name,c.floor_name,b.id, b.line_number, c.sewing_line_serial, c.location_name, sum(a.target_per_hour) tph from prod_resource_dtls a, prod_resource_mst b, lib_sewing_line c where b.id=a.mst_id and b.line_number=to_char(c.id) and b.company_id in($manufacturing_company) and a.pr_date='$today' and a.is_deleted=0 and b.is_deleted=0 $location_con $floor_con 
				 group by b.id, b.line_number, a.pr_date, c.company_name,c.location_name,c.floor_name, c.sewing_line_serial, c.location_name 
				 order by c.company_name,c.location_name,c.floor_name,c.sewing_line_serial";// $str_location
			}
			 //echo $line_sql;
			$lineData=sql_select($line_sql);
			foreach($lineData as $row)
			{
				$tph+=$row[csf('tph')];
				$lineWiseTph[$row[csf('id')]]=$row[csf('tph')];
				$lineResArr[$row[csf('id')]]=$row[csf('line_number')];
			}
			
			
			
			$sql="select b.id, b.line_number, sum(a.target_per_hour) tph from prod_resource_dtls a, prod_resource_mst b where b.id=a.mst_id and b.company_id in($manufacturing_company) and a.pr_date='$today' and a.is_deleted=0 and b.is_deleted=0  $location_con_3 $floor_con_4  group by b.id, b.line_number, a.pr_date ";
			$tph_sql=sql_select($sql);
			foreach($tph_sql as $row)
			{
				//if(!array_key_exists($row[csf('id')],$lineResArr))
				if($lineResArr[$row[csf('id')]]=='')
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
		  //print_r($linedataArr);
		//$start_hour=9; $no_of_hour=16; $hourdiff=$start_hour+$no_of_hour; 
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
		
		if($company!=0){$companyField=" company_id ";}
		else if($pro_company!=0){$companyField=" serving_company ";}
		else{$companyField=" serving_company ";}
		
		
		$sql.=" from pro_garments_production_mst where production_type in(5,11) and $companyField in($manufacturing_company) and production_date='$today' and is_deleted=0 and status_active=1 $location_con_2 $floor_con_2 group by sewing_line";
		
		$sql_subconProd.=" from subcon_gmts_prod_dtls WHERE company_id in($manufacturing_company) and production_date='$today' and production_type=2 and status_active=1 and is_deleted=0 $location_con_3 $floor_con_2 group by line_id";
			
		//echo $sql;	
			
					
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
		
		
		
		//print_r($hour_array);
        $hour_array= json_encode($hour_array);
		$sewTphArr=json_encode($sewTphArr);
        $sewProdArr= json_encode($sewProdArr);
        $sewProdRejArr= json_encode($sewProdRejArr);
        $sewProdPqArr= json_encode($sewProdPqArr);
       // list($company,$pro_company,$location,$floor)
    ?>






    <div class="wrap" style="margin-left:15px; margin-top:10px">
        <div style="width:950px; height:300px; float:left; position:relative; margin-left:10px; margin-top:5px; border:solid 1px">
        	<table style="margin-left:60px; font-size:12px">
            	<tr>
                	<td colspan="10" id="x">Today Hourly Production</td>
                </tr>
                <tr>
                    <td bgcolor="#FF3300" width="10"></td>
                    <td>Target</td>
                    <td bgcolor="#0066FF" width="10"></td>
                    <td>Sewing</td>
					<td bgcolor="#884800" width="10"></td>
                    <td>Sewing Rejection</td>
                    <td bgcolor="#C846C9" width="10"></td>
                    <td>Poly</td>
                    <td><input type="button" value="Sewing DHU" name="dhu" id="dhu" class="formbutton" style="width:120px" onclick="show_dhu_details(<? echo $floor; ?>,'<? echo $location; ?>','<? echo $pro_company; ?>','<? echo $company; ?>',5);"/></td>
                	<td>
                		<input type="button" value="Poly DHU" name="dhu" id="dhu" class="formbutton" style="width:120px" onclick="show_dhu_details(<? echo $floor; ?>,'<? echo $location; ?>','<? echo $pro_company; ?>','<? echo $company; ?>',11);"/>
                	</td>
                </tr>

            </table>
            <canvas id="canvas" height="240" width="900"></canvas>
		</div>


    <script>
		function show_dhu_details(floor,location,pro_company,company,production_type)
		{
			page_link='today_hourly_prod_popup.php?floor_id='+floor+'&location_id='+location+'&pro_company_id='+pro_company+'&cbo_production_type='+production_type+'&company_id='+company+'&action=today_dhu_prod_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Detail View', 'width=900px, height=400px, center=1, resize=0, scrolling=0','../');
		}

		function show_dhu_line_details(line_id,floor,location,pro_company,company,production_type)
		{
			page_link='today_hourly_prod_popup.php?floor_id='+floor+'&line_id='+line_id+'&location_id='+location+'&pro_company_id='+pro_company+'&cbo_production_type='+production_type+'&company_id='+company+'&action=today_dhu_prod_per_line_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Detail View', 'width=900px, height=400px, center=1, resize=0, scrolling=0','../');
		}

		function show_details(line_id,line_name,data)
		{
			page_link='today_hourly_prod_popup.php?line_id='+line_id+'&line_name='+line_name+'&cp='+data+'&action=today_hourly_prod_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Detail View', 'width=900px, height=400px, center=1, resize=0, scrolling=0','../');
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
 	<?  
	
		$lDataArr=array();
		foreach($linedataArr as $line_id=>$lineName)
		{
			$key=$company_id_arr[$line_id].'**'.$loc_arr[$line_id].'**'.$floor_arr[$line_id];
			$lDataArr[$key][$line_id]=$lineName;
		}
	 
	 //var_dump($lDataArr);
	 
		$i=0;
		
		
		
		//foreach($linedataArr as $line_id=>$lineName)
		//{
		foreach($lDataArr as $lineRow){
		 foreach($lineRow as $line_id=>$lineName){
			
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
			$floor_name="Floor : ".$floor_name_arr[$floor_arr[$line_id_for_loc]];
			
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
					<table style="margin-left:50px; font-size:12px;">
						<tr>
							<td colspan="9"><b><? echo "Company : ".$comp_arr[$company_id_arr[$line_id_for_loc]].";";// $comp_name; ?> &nbsp; <? echo $location_name; ?>, &nbsp; <? echo $floor_name; ?></b><br />Today Hourly Production Line No- <? echo $sewing_line; ?></td>
							<td><input type="button" value="Sewing DHU" name="dhu" id="dhu" class="formbutton" style="width:80px" onclick="show_dhu_line_details(<? echo $line_id; ?>,<? echo $floor; ?>,'<? echo $location; ?>','<? echo $pro_company; ?>','<? echo $company; ?>',5);"/></td>
						</tr>
						<tr>
							<td bgcolor="#FF3300" width="10"></td>
							<td>Target</td>
							<td bgcolor="#0066FF" width="10"></td>
							<td>Sewing</td>
							<td bgcolor="#884800" width="10"></td>
							<td>Sewing Rejection</td>
							<td bgcolor="#C846C9" width="10"></td>
							<td>Poly</td>
							<td>
							<input type="button" value="Details" name="a" id="a" class="formbutton" style="width:60px" onclick="show_details(<? echo $line_id; ?>,'<? echo $sewing_line; ?>','<? echo $company_id_arr[$line_id_for_loc]."__".$pro_company."__".$loc_arr[$line_id_for_loc]."__".$floor_arr[$line_id_for_loc]; ?>');"/></td>
							
		                	<td>
		                		<input type="button" value="Poly DHU" name="dhu" id="dhu" class="formbutton" style="width:80px" onclick="show_dhu_line_details(<? echo $line_id; ?>,<? echo $floor; ?>,'<? echo $location; ?>','<? echo $pro_company; ?>','<? echo $company; ?>',11);"/>
		                	</td>
						</tr>
					</table>
					<canvas id="canvas<? echo $i; ?>" height="180" width="450"></canvas>
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
		
		
		
		}}
		//}
	?>
	</div>