<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Dash Board.
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam Reza 
Creation date 	: 	25-6-2018
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/


session_start();
require_once('../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
echo load_html_head_contents("Graph", "../../", "", $popup, $unicode, $multi_select, 1);
 
//--------------------------------------------------------------------------------------------------------------------
if($action=="opendate_popup")
{
	?>
	<script>
		function js_set_value()
		{
			if( form_validation('txt_date_from*txt_date_to','Select Start Date*Select End Date')==false)
			{
				return;
			}
			else
			{
				parent.emailwindow.hide();
			}
		}
	</script>
    </head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="keydate_1"  id="keydate_1" autocomplete="off">
                <table width="290" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                         <tr>
                            <th width="50%">Start Date</th>
                            <th>End Date</th>
                         </tr>
                  	</thead>
                    <tr>
                    	<td align="center"><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:130px" placeholder="Start Date"></td>
                    	<td align="center"><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:130px" placeholder="End Date"></td>
                    </tr>
                    <tr>
                    	<td colspan="2" align="center"><input type="button" name="button2" class="formbutton" value="Close" onClick="js_set_value( document.getElementById('txt_date_from').value )" style="width:70px;" /></td>
                    </tr>
                 </table>
             </form>
             </div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
    <?
	exit();
}


if($_REQUEST['m']=='Z2FybWVudHNfZ3JhcGg='){
	
	list($lcCompany,$location,$floor,$workingCompany)=explode('__',$_REQUEST['cp']);
	list($start_date,$end_date)=explode('__',$_REQUEST['date_data']);

?>	
    <script src="../../Chart.js-master/Chart.js"></script>
    <!--<div style="margin-left:10px; margin-top:10px">
    	<a href="index.php">Main Page</a>&nbsp;&nbsp;<a href="index.php?g=2">Today Hourly Prod.</a>
    </div>-->
	<div style="margin-left:10px; margin-top:10px; width:98%">
		<div style="width:32%; height:300px; float:left; position:relative; border:solid 1px">
            <table width="100%">
            	<tr>
                	<td align="center" title="Produce Minute/Available Minute * 100"><b>Efficiency in %</b></td>
                </tr>
            	<tr>
                	<td align="center">
                        <span style="background-color:#C9662B;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Sewing
                        <span style="background-color:#FCB22B;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Poly
                    </td>
                </tr>
            </table>
            <canvas id="canvas" height="300" width="500"></canvas>
		</div>
        
        
        
        <div style="width:32%; height:300px; float:left; position:relative; margin-left:10px; border:solid 1px">
        	<table width="100%">
            	<tr>
                	<td align="center" title="Total Reject Qty/Total Qc qty * 100"><b>Cut Panel Rejection</b></td>
                </tr>
            </table>
            <canvas id="canvas2" height="300" width="500"></canvas>
		</div>
        
        
        <div style="width:32%; height:300px; float:left; position:relative; margin-left:10px; border:solid 1px">
        	<table width="100%">
            	<tr>
                	<td align="center" title="Total Shipment Qty/Total Lay Qty * 100"><b>Cut to Shipment Ratio</b></td>
                </tr>
            </table>
            <canvas id="canvas3" height="300" width="500"></canvas>
		</div>
         
       <div style="width:32%; height:300px;margin:10px 0 0 0; float:left; position:relative; border:solid 1px">
        	<table width="100%">
            	<tr>
                	<td align="center" title="Total Shipment Qty/Total Poly Qty * 100"><b>Poly to Shipment Ratio</b></td>
                </tr>
            </table>
            <canvas id="canvas4" height="300" width="500"></canvas>
		</div>
        
        
      <div style="width:32%; height:300px;margin:10px 0 0 10px; float:left; position:relative; border:solid 1px">
        	<table width="100%">
            	<tr>
                	<td align="center" title="Total Ratio/Total Number Of Days"><b>Man Machine Ratio</b></td>
                </tr>
            </table>
            <canvas id="canvas5" height="300" width="500"></canvas>
		</div> 
        
        
      <div style="width:32%; height:300px;margin:10px 0 0 10px; float:left; position:relative; border:solid 1px">
        	<table width="100%">
            	<tr>
                	<td align="center" title="Total Replace Qty/Total Check Qty * 100"><b>Re-Check</b></td>
                </tr>
            </table>
            <canvas id="canvas6" height="300" width="500"></canvas>
		</div> 
        
        
      <div style="width:32%; height:300px;margin:10px 0 0 0; float:left; position:relative; border:solid 1px">
        	<table width="100%">
            	<tr>
                	<td align="center" title=""><b>Air freight in Qty (lakh/pcs)</b></td>
                </tr>
            </table>
            <canvas id="canvas7" height="300" width="500"></canvas>
		</div> 

      <div style="width:32%; height:300px;margin:10px 0 0 10px; float:left; position:relative; border:solid 1px">
        	<table width="100%">
            	<tr>
                	<td align="center" title=""><b>Average SMV</b></td>
                </tr>
            </table>
            <canvas id="canvas8" height="300" width="500"></canvas>
		</div> 

      <div style="width:32%; height:300px;margin:10px 0 0 10px; float:left; position:relative; border:solid 1px">
        	<table width="100%">
            	<tr>
                	<td align="center" title=""><b>DHU (%)</b></td>
                </tr>
            </table>
            <canvas id="canvas9" height="300" width="500"></canvas>
		</div> 
        
        
        
      <div style="width:32%; height:300px;margin:10px 0 0 0; float:left; position:relative; border:solid 1px">
        	<table width="100%">
            	<tr>
                	<td align="center" title=""><b>Average FOB in $</b></td>
                </tr>
            </table>
            <canvas id="canvas10" height="300" width="500"></canvas>
		</div> 
        
        
        
        
	</div>
	<?
		
		
		
		
		$month_array=array();	
	    $month_prev=date("Y-m-d",strtotime($start_date));
        $month_next=date("Y-m-d",strtotime($end_date));
		$remain_months=datediff( "m",$month_prev,$month_next);
		
		
        $start_yr=date("Y",strtotime($month_prev));
        $end_yr=date("Y",strtotime($month_next));
        for($e=0;$e<=$remain_months;$e++)
        {
            $tmp=add_month(date("Y-m-d",strtotime($month_prev)),$e);
            $yr_mon_part[$e]=date("Y-m",strtotime($tmp));
            //$month_array[$e]=date("M",strtotime($tmp))." '".date("y",strtotime($tmp));
            $month_array[$e]=date("M y",strtotime($tmp));
        }
         $month_array[$e]='Avg';
		
		$month_array= json_encode($month_array); 
	
		if($db_type==0)
		{
			$start_date=change_date_format($start_date,'YYYY-MM-DD');
			$end_date=change_date_format($end_date,'YYYY-MM-DD');
		}
		else if($db_type==2)
		{
			$start_date=change_date_format($start_date,'','',-1);
			$end_date=change_date_format($end_date,'','',-1);
		}
	
	
	
	
	//------------------------------------------------------------------------------------------------------
	
	
	//Efficiency in %----------------------------------------------start;
	
	$cd=date('d-m-Y');
	$sd=date('d-m-Y',strtotime(str_replace("'","",$end_date)));
	if($cd==$sd)
	{
		$end_date_one_day_back = date('d-m-Y', strtotime('-1 day', strtotime($end_date))); 
			if($db_type==0)
			{
				$end_date_one_day_back=change_date_format($end_date_one_day_back,'YYYY-MM-DD');
			}
			else if($db_type==2)
			{
				$end_date_one_day_back=change_date_format($end_date_one_day_back,'','',-1);
			}
	}
	else
	{
		$end_date_one_day_back=$end_date;
	}
	

	
	
	if($db_type==0) $prod_start_cond="prod_start_time";
	else if($db_type==2) $prod_start_cond="TO_CHAR(prod_start_time,'HH24:MI')";
	
	$variable_start_time_arr='';
	if($workingCompany){$company_cond=" and company_name=$workingCompany";}else{$company_cond=" and company_name=$lcCompany";}
	$prod_start_time=sql_select("select $prod_start_cond as prod_start_time from variable_settings_production where variable_list=26 $company_cond and status_active=1 and is_deleted=0 and shift_id=1");
	foreach($prod_start_time as $row)
	{
		$ex_time=explode(" ",$row[csf('prod_start_time')]);
		$variable_start_time_arr=$row[csf('prod_start_time')];
	}
	unset($prod_start_time);
	$current_date_time=date('d-m-Y H:i');
	$ex_date_time=explode(" ",$current_date_time);
	$current_date=$ex_date_time[0];
	$current_time=$ex_date_time[1];
	$ex_time=explode(":",$current_time);
	
	$search_prod_date=change_date_format(str_replace("'","",$end_date_one_day_back),'yyyy-mm-dd');
	$current_eff_min=($ex_time[0]*60)+$ex_time[1];
	
	$variable_time= explode(":",$variable_start_time_arr);
	$vari_min=($variable_time[0]*60)+$variable_time[1];
	$difa_time=explode(".",number_format(($current_eff_min-$vari_min)/60,2));
	$dif_time=$difa_time[0];
	$dif_hour_min=date("H:i", strtotime($dif_time));
	
	
	if($workingCompany){$company_cond=" and company_name=$workingCompany";}else{$company_cond=" and company_name=$lcCompany";}
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","variable_list=23 $company_cond and is_deleted=0 and status_active=1");
	
	 
	
	
	if($workingCompany){$company_cond=" and a.company_id=$workingCompany";}else{$company_cond=" and a.company_id=$lcCompany";}
	if($start_date!="" && $end_date!=""){$sql_cond=" and pr_date between '$start_date' and '$end_date_one_day_back'";}
	 
	if($location){$location_cond=" and a.location_id=$location";}
	if($floor){$floor_cond=" and a.floor_id in($floor)";}

	 
	 if($prod_reso_allo==1)
	 {
		$prod_resource_array=array();
		$sql="select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.smv_adjust, b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour, c.from_date, c.to_date, c.capacity as mc_capacity from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c where a.id=c.mst_id and a.id=b.mst_id and c.id=b.mast_dtl_id $company_cond $location_cond $floor_cond $sql_cond";
		$dataArray=sql_select($sql);// and a.id=1 and c.from_date=$end_date
		
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
			
			$date_line_wise_prod_resource[$val[csf('id')]][$val[csf('pr_date')]]+=$val[csf('man_power')];
		}
	 }
	
	
	
	
	if($workingCompany){$company_cond=" and a.serving_company=$workingCompany";}else{$company_cond=" and a.company_id=$lcCompany";}
	if($start_date!="" && $end_date!=""){$sql_cond=" and a.production_date between '$start_date' and '$end_date_one_day_back'";}
	
	if($location){$location_cond=" and a.location=$location";}
	if($floor){$floor_cond=" and a.floor_id in($floor)";}
		
	$sql="SELECT  a.production_type, a.floor_id, a.production_date, a.sewing_line,  a.po_break_down_id, a.item_number_id,sum(d.production_qnty) as good_qnty 
			from pro_garments_production_mst a,pro_garments_production_dtls d, wo_po_details_master b, wo_po_break_down c,wo_po_color_size_breakdown e
			where  a.production_type in(5,11) and d.production_type in(5,11) and a.id=d.mst_id and   a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.po_break_down_id=e.po_break_down_id and d.color_size_break_down_id=e.id and b.job_no=e.job_no_mst and c.id=e.po_break_down_id and  a.status_active=1 and a.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and c.is_deleted=0 and c.status_active in(1,2,3)  and e.status_active in(1,2,3) and e.is_deleted=0 $sql_cond $company_cond $location_cond $floor_cond
			group by a.production_type,a.floor_id, a.po_break_down_id,  a.production_date, a.sewing_line, a.item_number_id order by a.floor_id, a.po_break_down_id";
	$sql_result=sql_select($sql);	
	$sewing_production_po_data_arr=array();
	$poly_production_po_data_arr=array();
	foreach($sql_result as $val)
	{
	
		//$key=$val[csf('floor_id')].'**'.$val[csf('sewing_line')];
		$key=$val[csf('floor_id')].'**'.$val[csf('sewing_line')].'**'.$val[csf('po_break_down_id')].'**'.$val[csf('item_number_id')].'**'.$val[csf('production_date')];
		//$production_data_arr[$key]=$key;
		
		if($val[csf('production_type')]==5){// sewing
			$sewing_production_po_data_arr[$key]+=$val[csf('good_qnty')];
		}
		else if($val[csf('production_type')]==11){// poly
			$poly_production_po_data_arr[$key]+=$val[csf('good_qnty')];
		}
		
		$all_po_id_arr[$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];
	}
	
	
	
	
	
	if($workingCompany){$company_cond=" and a.company_id=$workingCompany";}else{$company_cond=" and a.company_id=$lcCompany";}
	
	if($location){$location_cond=" and a.location_id=$location";}
	if($floor){$floor_cond=" and a.floor_id in($floor)";}
	
 $sql_subcon="select  a.production_type, a.floor_id, a.production_date, a.line_id as sewing_line, a.order_id as po_break_down_id, a.gmts_item_id as item_number_id,sum(d.prod_qnty) as good_qnty
				 from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d, subcon_ord_mst b, subcon_ord_dtls c,subcon_ord_breakdown e
				where a.production_type in(2,5) and d.production_type in(2,5) and a.id=d.dtls_id and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and e.id=d.ord_color_size_id and e.order_id=c.id and a.order_id=e.order_id $sql_cond $company_cond $location_cond $floor_cond
				group by a.production_type, a.floor_id, a.order_id, a.production_date, a.line_id , a.gmts_item_id order by a.floor_id, a.order_id";	
	$sql_subcon_result=sql_select($sql_subcon);	
	foreach($sql_subcon_result as $val)
	{
	
		//$key=$val[csf('floor_id')].'**'.$val[csf('sewing_line')];
		$key=$val[csf('floor_id')].'**'.$val[csf('sewing_line')].'**'.$val[csf('po_break_down_id')].'**'.$val[csf('item_number_id')].'**'.$val[csf('production_date')];
		//$production_data_arr[$key]=$key;
		
		if($val[csf('production_type')]==2){//sub sewing
			$sewing_production_po_data_arr[$key]+=$val[csf('good_qnty')];
		}
		else if($val[csf('production_type')]==5){//sub poly
			$poly_production_po_data_arr[$key]+=$val[csf('good_qnty')];
		}
		
		
		$all_po_id_arr[$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];
	}
	
	
	
	if($workingCompany){$company_cond=" and company_name=$workingCompany";}else{$company_cond=" and company_name=$lcCompany";}
	$smv_source=return_field_value("smv_source","variable_settings_production","variable_list=25 $company_cond and status_active=1 and is_deleted=0");
	
	
		$where_cond='';$poIds_cond='';
		if($db_type==2 && count($all_po_id_arr)>999)
		{
			$po_id_chunk_arr=array_chunk($all_po_id_arr,999) ;
			foreach($po_id_chunk_arr as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);	
				$poIds_cond.=" b.id in($chunk_arr_value) or ";	
			}
			
			$where_cond.=" and (".chop($poIds_cond,'or ').")";			
		}
		else
		{
			$where_cond=" and b.id in(".implode(',',$all_po_id_arr).")";	 
		}
	
	
	
	if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
	if($smv_source==3)
	{
		$sql_item="select b.id, a.sam_style, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3)  $where_cond";
		$resultItem=sql_select($sql_item);
		foreach($resultItem as $itemData)
		{
			$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
		}
	}
	else
	{
		$sql_item="select b.id,c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3) $where_cond";
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

	
	
	 //echo $sql_item;die;
	//Sewing...............................................
	foreach($sewing_production_po_data_arr as $key=>$good_qnty){
		list($floor_id,$line_id,$po_id,$item_id,$production_date)=explode('**',$key);
		
			$monthKey=date("Y-m",strtotime($production_date));
			$produce_minit_arr[$monthKey]+=$good_qnty*$item_smv_array[$po_id][$item_id];
		
		
		if($tempSewLine[$line_id.$production_date]==''){

			if($current_date==$search_prod_date)
			{
				$prod_wo_hour=$prod_resource_array[$line_id][$production_date]['working_hour'];
				
				if ($dif_time<$prod_wo_hour)//
				{
					$cla_cur_time=$dif_time;
				}
				else
				{
					$cla_cur_time=$prod_wo_hour;
				}
			}
			else
			{
				$cla_cur_time=$prod_resource_array[$line_id][$production_date]['working_hour'];
			}

			$smv_adjustmet_type=$prod_resource_array[$line_id][$production_date]['smv_adjust_type'];
			
			$total_adjustment=0;
			if(str_replace("'","",$smv_adjustmet_type)==1)
			{ 
				$total_adjustment=$prod_resource_array[$line_id][$production_date]['smv_adjust'];
			}
			else if(str_replace("'","",$smv_adjustmet_type)==2)
			{
				$total_adjustment=($prod_resource_array[$line_id][$production_date]['smv_adjust'])*(-1);
			}
			
			$efficiency_min_arr[$monthKey]+=$total_adjustment+$prod_resource_array[$line_id][$production_date]['man_power']*$cla_cur_time*60;
			
			$tempSewLine[$line_id.$production_date]=1;
		}
		
	}//foreach end;



	//Poly...............................................
	foreach($poly_production_po_data_arr as $key=>$good_qnty){
		list($floor_id,$line_id,$po_id,$item_id,$production_date)=explode('**',$key);
		
			$monthKey=date("Y-m",strtotime($production_date));
			$poly_produce_minit_arr[$monthKey]+=$good_qnty*$item_smv_array[$po_id][$item_id];
	//}
		
		//foreach($date_line_wise_prod_resource as $line_id=>$dataDataArr){
		//foreach($dataDataArr as $production_date=>$row){
		$monthKey=date("Y-m",strtotime($production_date));
		if($tempPolyLine[$line_id.$production_date]==''){
			if($current_date==$search_prod_date)
			{
				$prod_wo_hour=$prod_resource_array[$line_id][$production_date]['working_hour'];
				
				if ($dif_time<$prod_wo_hour)//
				{
					$cla_cur_time=$dif_time;
				}
				else
				{
					$cla_cur_time=$prod_wo_hour;
				}
			}
			else
			{
				$cla_cur_time=$prod_resource_array[$line_id][$production_date]['working_hour'];
			}
			
			$smv_adjustmet_type=$prod_resource_array[$line_id][$production_date]['smv_adjust_type'];
			
			$total_adjustment=0;
			if(str_replace("'","",$smv_adjustmet_type)==1)
			{ 
				$total_adjustment=$prod_resource_array[$line_id][$production_date]['smv_adjust'];
			}
			else if(str_replace("'","",$smv_adjustmet_type)==2)
			{
				$total_adjustment=($prod_resource_array[$line_id][$production_date]['smv_adjust'])*(-1);
			}
			
		
			$poly_efficiency_min_arr[$monthKey]+=$total_adjustment+$prod_resource_array[$line_id][$production_date]['man_power']*$cla_cur_time*60;
			$tempPolyLine[$line_id.$production_date]=1;
		}
		
		
		
	//}//foreach end;
	}//foreach end;

	/*echo "<pre>";
	var_dump($produce_minit_arr);
	echo '---';
	var_dump($efficiency_min_arr);
	echo "</pre>";die;*/
	
	foreach($yr_mon_part as $Ym){
		$sewing_efficiency_array[]=number_format(($produce_minit_arr[$Ym]/$efficiency_min_arr[$Ym])*100,2,".","");
		$poly_efficiency_array[]=number_format(($poly_produce_minit_arr[$Ym]/$poly_efficiency_min_arr[$Ym])*100,2,".","");
	}
	
	$sewing_efficiency_array[]=number_format((array_sum($produce_minit_arr)/array_sum($efficiency_min_arr))*100,2,".","");
	$poly_efficiency_array[]=number_format((array_sum($poly_produce_minit_arr)/array_sum($poly_efficiency_min_arr))*100,2,".","");
	

	//Sewing/poly Eff data.....................................end;
			
		
	//Cut Panel Rejection----------------------------------------------start;
		
		if($workingCompany){$company_cond=" and a.serving_company=$workingCompany";}else{$company_cond=" and a.company_id=$lccompany";}
		if($start_date!="" && $end_date!=""){$date_cond=" and a.production_date between '$start_date' and '$end_date'";}
		
		if($location){$location_cond=" and a.location=$location";}
		if($floor){$floor_cond=" and a.floor_id in($floor)";}

		
		
		$production_sql ="select a.po_break_down_id,a.production_date,
		sum(CASE WHEN b.production_type=1 then b.reject_qty ELSE 0 END) as cut_reject_qty,
		sum(CASE WHEN b.production_type=1 then b.replace_qty ELSE 0 END) as cut_replace_qty,
		sum(CASE WHEN b.production_type=1 then b.production_qnty ELSE 0 END) as cut_production_qnty,
		
		sum(case when b.production_type=5 then b.reject_qty else 0 end) as reject_qty,
		sum(case when b.production_type=5 then b.replace_qty else 0 end) as replace_qty,
		sum(case when b.production_type=5 then b.alter_qty else 0 end) as alter_qty,
		sum(case when b.production_type=5 then b.spot_qty else 0 end) as spot_qty,
		sum(case when b.production_type=5 then b.production_qnty else 0 end) as production_qnty
		
		from pro_garments_production_mst a,pro_garments_production_dtls b 
		where a.id=b.mst_id and b.color_size_break_down_id!=0 and a.production_type in(1,5) and b.production_type in(1,5,11) $company_cond $location_cond $floor_cond $date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.po_break_down_id,a.production_date";		
		
		
		$production_sql_result = sql_select($production_sql);
		foreach($production_sql_result as $row){
			$monthkey=date("Y-m",strtotime($row[csf('production_date')]));
			$cut_rej_data_array[$monthkey]+=$row[csf('cut_reject_qty')];
			$cut_production_data_array[$monthkey]+=(($row[csf('cut_reject_qty')]+$row[csf('cut_production_qnty')])-$row[csf('cut_replace_qty')]);
			
			
			
			$sew_rej_data_array[$monthkey]+=$row[csf('reject_qty')];
			$sew_rep_data_array[$monthkey]+=$row[csf('replace_qty')];
			$sew_alt_data_array[$monthkey]+=$row[csf('alter_qty')];
			$sew_spot_data_array[$monthkey]+=$row[csf('spot_qty')];
			$sew_production_data_array[$monthkey]+=$row[csf('production_qnty')];
			$production_po_arr[$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
			$production_po_month_arr[$row[csf('po_break_down_id')]]=$monthkey;
		}

		
		
		foreach($yr_mon_part as $Ym){
			$cut_panel_rejection=($cut_rej_data_array[$Ym]/$cut_production_data_array[$Ym])*100;
			$cut_panel_rejection_array[]=number_format($cut_panel_rejection,2,".","");
		}
	 
		$cut_rej_data_avg=array_sum($cut_panel_rejection_array);
		$cut_panel_rejection_array[]=number_format($cut_rej_data_avg/($remain_months+1),2,".","");
		
		
		
//Cut Panel Rejection----------------------------------------------end;
//Cut to Shipment Ratio----------------------------------------------start;
		
		if($workingCompany){ $company_cond=" and a.delivery_company_id=$workingCompany"; $company_cond_working=" and a.serving_company=$workingCompany"; }else{$company_cond=" and a.company_id=$lccompany"; $company_cond_working=" and a.company_id=$workingCompany";}
		if($start_date!="" && $end_date!=""){$date_cond=" and b.ex_factory_date between '$start_date' and '$end_date'";}
		
		if($location){$location_cond=" and a.delivery_location_id=$location";}
		if($floor){$floor_cond=" and a.delivery_floor_id in($floor)";}
		
		
		
		
		
		
		 $shipment_po_sql="select  b.po_break_down_id,max(b.ex_factory_date) as ex_factory_date from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and b.shiping_status=3 $company_cond $location_cond $floor_cond  $date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id";
		 
		$shipment_po_sql_result = sql_select($shipment_po_sql);
		$shipment_po_array=array();
		$monthly_shipment_po_array=array();
		foreach($shipment_po_sql_result as $row){
			$monthKey=date("Y-m",strtotime($row[csf('ex_factory_date')]));
			$shipment_po_array[$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
			$monthly_shipment_po_array[$monthKey][$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
		}
		

		$where_cond='';$poIds_cond='';
		if($db_type==2 && count($shipment_po_array)>999)
		{
			$po_id_chunk_arr=array_chunk($shipment_po_array,999) ;
			foreach($po_id_chunk_arr as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);	
				$poIds_cond.=" a.po_break_down_id in($chunk_arr_value) or ";	
				$poIds_cond_b.=" b.po_break_down_id in($chunk_arr_value) or ";	
			}
			
			$where_cond.=" and (".chop($poIds_cond,'or ').")";			
			$where_cond_b.=" and (".chop($poIds_cond_b,'or ').")";			
		}
		else
		{
			$where_cond=" and a.po_break_down_id in(".implode(',',$shipment_po_array).")";	
			$where_cond_b=" and b.po_break_down_id in(".implode(',',$shipment_po_array).")";	
			 
		}
		
		
		
		
		 $shipment_qty_sql="select b.ex_factory_qnty,b.po_break_down_id from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and b.shiping_status=3 $company_cond $location_cond $floor_cond $where_cond_b and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		 $shipment_qty_sql_result = sql_select($shipment_qty_sql);
		 $po_shipment_qty_data_array=array();
		foreach($shipment_qty_sql_result as $row){
			 $po_shipment_qty_data_array[$row[csf('po_break_down_id')]]+=$row[csf('ex_factory_qnty')];
		}
		
		 
		
		if($location){$location_cond=" and a.location=$location";}
		if($floor){$floor_cond=" and a.floor_id in($floor)";}

		
		$production_sql ="select a.po_break_down_id,
		sum(CASE WHEN b.production_type=1 then b.reject_qty ELSE 0 END) as cut_reject_qty,
		sum(CASE WHEN b.production_type=1 then b.replace_qty ELSE 0 END) as cut_replace_qty,
		sum(CASE WHEN b.production_type=1 then b.production_qnty ELSE 0 END) as cut_production_qnty,
		sum(CASE WHEN b.production_type=11 then b.production_qnty ELSE 0 END) as poly_qty
		
		from pro_garments_production_mst a,pro_garments_production_dtls b 
		where a.id=b.mst_id and b.color_size_break_down_id!=0 and a.production_type in(1,11) and b.production_type in(1,11) $where_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by a.po_break_down_id";		
		
		$production_sql_result = sql_select($production_sql);
		foreach($production_sql_result as $row){
			$po_poly_qty_data_array[$row[csf('po_break_down_id')]]=$row[csf('poly_qty')];
			$po_cut_rej_data_array[$row[csf('po_break_down_id')]]=$row[csf('cut_reject_qty')];
			$po_cut_production_data_array[$row[csf('po_break_down_id')]]=( $row[csf('cut_production_qnty')]+$row[csf('cut_reject_qty')]);
		}
		
		
		$cut_production_data_arrays=array();
		$shipment_qty_data_array=array();
		foreach($monthly_shipment_po_array as $month=>$poArr){
			foreach($poArr as $po_id){
				if($po_cut_production_data_array[$po_id]>0){
					$cut_production_data_arrays[$month]+=$po_cut_production_data_array[$po_id];
					$shipment_qty_data_array[$month]+=$po_shipment_qty_data_array[$po_id];
					$poly_qty_data_array[$month]+=$po_poly_qty_data_array[$po_id];
				}
			}
		}

		foreach($yr_mon_part as $Ym){
			$cut_to_shipment_ratio=($shipment_qty_data_array[$Ym]/$cut_production_data_arrays[$Ym])*100;
			$cut_to_shipment_ratio_array[]=number_format($cut_to_shipment_ratio,2,".","");
		}
		$cut_to_shipment_ratio_array[]=number_format(array_sum($cut_to_shipment_ratio_array)/($remain_months+1),2,".","");
		

//Cut to Shipment Ratio----------------------------------------------end;
//Poly to Shipment Ratio----------------------------------------------start;
		
		foreach($yr_mon_part as $Ym){
			$poly_to_shipment_ratio=($poly_qty_data_array[$Ym]/$cut_production_data_arrays[$Ym])*100;
			$poly_to_shipment_ratio_array[]=number_format($poly_to_shipment_ratio,2,".","");
		}
		$poly_to_shipment_ratio_array[]=number_format(array_sum($poly_to_shipment_ratio_array)/($remain_months+1),2,".","");
		
//Poly to Shipment Ratio----------------------------------------------end;
//Man Machine Ratio----------------------------------------------start;
		if($workingCompany){$company_cond=" and unit_id=$workingCompany";}else{$company_cond=" and unit_id=$lccompany";}
		if($start_date!="" && $end_date!=""){$date_cond=" and insert_date between '$start_date' and '$end_date'";}
		
		
		
		$machine_sql="select insert_date,sum(mmr_value) as mmr_value  from  mmrdashboard where mmr_value>0 $company_cond $date_cond group by insert_date";
		$machine_sql_result = sql_select($machine_sql);
		foreach($machine_sql_result as $row){
			$monthkey=date("Y-m",strtotime($row[csf('insert_date')]));
			$machine_data_array[$monthkey]+=$row[csf('mmr_value')]*1;
			$day_data_array[$monthkey]+=1;
		}
		
		foreach($yr_mon_part as $Ym){
			$man_machine_ratio=($machine_data_array[$Ym]/$day_data_array[$Ym])*1;
			if($day_data_array[$Ym]){$man_machine_ratio_array[]=number_format($man_machine_ratio,2,".","");}
			else{$man_machine_ratio_array[]=0;}
		}
		$man_machine_ratio_array[]=number_format(array_sum($man_machine_ratio_array)/($remain_months+1),2,".","");
		
		
//Man Machine Ratio----------------------------------------------end;
//Re-Cheque----------------------------------------------start;

		
		foreach($yr_mon_part as $Ym){
			$total_check_qty=$sew_rej_data_array[$Ym]+$sew_alt_data_array[$Ym]+$sew_spot_data_array[$Ym]+$sew_production_data_array[$Ym];
			
			$re_check_qty_percent=($sew_rep_data_array[$Ym]/$total_check_qty)*100;
			$re_check_qty_array[]=number_format($re_check_qty_percent,2,".","");
		}
	 
		$re_check_qty_array[]=number_format(array_sum($re_check_qty_array)/($remain_months+1),2,".","");
		

//Re-Cheque----------------------------------------------end;
		
//Air freight in Qty (lakh/pcs)--------------------------start;

	if($workingCompany){$company_cond=" and a.delivery_company_id=$workingCompany";}else{$company_cond=" and a.company_id=$lccompany";}
	
	if($start_date!="" && $end_date!=""){$date_cond=" and a.delivery_date between '$start_date' and '$end_date'";}
	
		if($location){$location_cond=" and a.delivery_location_id=$location";}
		if($floor){$floor_cond=" and a.delivery_floor_id in($floor)";}
	
	
	$air_exfactory_sql="select b.ex_factory_qnty,a.delivery_date from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b where a.id=b.delivery_mst_id $date_cond $company_cond $location_cond $floor_cond and b.shiping_mode=2";

	$air_exfactory_sql_result = sql_select($air_exfactory_sql);
	foreach($air_exfactory_sql_result as $row){
		$monthkey=date("Y-m",strtotime($row[csf('delivery_date')]));
		$air_exfactory_data_array[$monthkey]+=($row[csf('ex_factory_qnty')]/100000);
	}

	foreach($yr_mon_part as $Ym){
		$air_exfactory_qty_array[]=number_format($air_exfactory_data_array[$Ym],2,".","");
	}
	$air_exfactory_qty_array[]=number_format(array_sum($air_exfactory_data_array)/($remain_months+1),2,".","");
		
//Air freight in Qty (lakh/pcs)--------------------------end;
		
		
//Average SMV-------------------------------------------------start;

	/*if($workingCompany){$company_cond=" and a.style_owner=$workingCompany";}else{$company_cond=" and a.company_name=$lccompany";}
	$date_cond=" and c.country_ship_date between '$start_date' and  '$end_date'";

	$avg_smg_sql="select 
	a.job_no,a.set_smv,
	max(c.country_ship_date) pub_shipment_date, 
	sum(c.order_quantity/a.total_set_qnty) as po_quantity, 
	sum(c.order_quantity) as po_quantity_pcs
	from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
	 where 
	 a.job_no=b.job_no_mst and 
	 a.job_no=c.job_no_mst and 
	 b.id=c.po_break_down_id 
	
	 $company_cond
	 $date_cond and
	 a.status_active=1 and 
	 a.is_deleted=0 and 
	 b.status_active=1 and 
	 b.is_deleted=0 and 
	 c.status_active=1 and 
	 c.is_deleted=0 and b.is_confirmed=1 
	 group by a.job_no,a.set_smv";
	 
	$avg_smg_sql_result=sql_select($avg_smg_sql);
	$quantity_tot=array();
	foreach ($avg_smg_sql_result as $row){
		$monthKey=date("Y-m",strtotime($row[csf('pub_shipment_date')]));
		$quantity_tot[$monthKey]+=$row[csf("po_quantity_pcs")];
		//$company_buyer_array+=$row[csf("set_smv")]*$row[csf('po_quantity')];
		$avg_smv_data_array[$monthKey]+=$row[csf("set_smv")]*$row[csf('po_quantity')];
	}
	

	foreach($yr_mon_part as $Ym){
		$avg_smv_qty_array[]=number_format($avg_smv_data_array[$Ym]/$quantity_tot[$Ym],2,".","");
	}
	//$avg_smv_qty_array[]=number_format((array_sum($avg_smv_data_array)/($remain_months+1))/array_sum($quantity_tot),2,".","");
	$avg_smv_qty_array[]=number_format((array_sum($avg_smv_qty_array)/($remain_months+1)),2,".","");*/



//Average SMV-------------------------------------------------start;

	if($workingCompany){$company_cond=" and a.style_owner=$workingCompany";}else{$company_cond=" and a.company_name=$lccompany";}
	$date_cond=" and c.country_ship_date between '$start_date' and  '$end_date'";
	//if($location){$location_cond=" and a.working_location_id=$location";}
	

$avg_smg_sql="select 
	a.style_owner,a.job_no,a.set_smv,c.country_ship_date,
	sum(c.order_quantity/a.total_set_qnty) as po_quantity, 
	sum(c.order_quantity) as po_quantity_pcs
	
	from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
	 where 
	 a.job_no=b.job_no_mst and 
	 a.job_no=c.job_no_mst and 
	 b.id=c.po_break_down_id 
	
	 $company_cond  $date_cond  and
	 a.status_active=1 and 
	 a.is_deleted=0 and 
	 b.status_active=1 and 
	 b.is_deleted=0 and 
	 c.status_active=1 and 
	 c.is_deleted=0
	 group by a.style_owner,a.job_no,a.set_smv,c.country_ship_date"; // and b.is_confirmed=1 

	$avg_smg_sql_result=sql_select($avg_smg_sql);
	
	foreach ($avg_smg_sql_result as $row){
		$monthKey=date("Y-m",strtotime($row[csf("country_ship_date")]));
		$quantity_tot[$row[csf("style_owner")]][$monthKey]+=$row[csf("po_quantity_pcs")];
		$avg_smv_data_array[$row[csf("style_owner")]][$monthKey]+=$row[csf("set_smv")]*$row[csf('po_quantity')];
	}
	
	foreach($quantity_tot as $company_id=>$dateWiseDataArr){
		foreach($dateWiseDataArr as $ship_date=>$qty){
			$avg_smv_qty_data_array[$ship_date]+=$avg_smv_data_array[$company_id][$ship_date]/$qty;
		}
	}

	
	foreach($yr_mon_part as $Ym){
		$avg_smv_qty_array[]=number_format($avg_smv_qty_data_array[$Ym],2,".","");
	}
	$avg_smv_qty_array[]=number_format((array_sum($avg_smv_qty_data_array)/($remain_months+1)),2,".","");


//Average SMV-------------------------------------------------end;


//Average SMV-------------------------------------------------end;
		
	
	
//DHU-------------------------------------------------start;
	if($workingCompany){$company_cond=" and d.serving_company=$workingCompany";}else{$company_cond=" and a.company_id=$lccompany";}
	$date_cond=" and d.production_date between '$start_date' and  '$end_date'";

	if($location){$location_cond=" and d.location=$location";}
	if($floor){$floor_cond=" and d.floor_id in($floor)";}
	

	$dhu_sql = "SELECT d.production_date,d.serving_company,d.floor_id, sum(a.defect_qty) as defect_qty,sum(d.reject_qnty) as reject_qnty 
		FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst d, pro_gmts_prod_dft a
		WHERE b.job_no=c.job_no_mst and d.po_break_down_id=c.id and d.id=a.mst_id and  a.defect_type_id in (3,4) and a.production_type=5 and a.is_deleted=0 and d.is_deleted=0 and d.status_active=1 and a.status_active=1 and c.status_active in(1,2,3)  $date_cond $company_cond $location_cond $floor_cond
		group by d.serving_company,d.production_date,d.floor_id";

	$dhu_deft_sql_result=sql_select($dhu_sql);
	foreach($dhu_deft_sql_result as $row){
		$monthkey=date("Y-m",strtotime($row[csf('production_date')]));
		$dhu_defet_data_array[$monthkey]+=$row[csf('defect_qty')];
	}

	
	
	$dhu_sql = "SELECT d.production_date,d.serving_company,d.floor_id,sum(f.production_qnty) as qc_pass_qty,sum(f.alter_qty) as alter_qnty, 
        sum(f.reject_qty) as reject_qnty,sum(f.spot_qty) as spot_qnty,sum(f.replace_qty) as replace_qty
FROM pro_garments_production_mst d,pro_garments_production_dtls f
WHERE d.production_type=5 and f.production_type=5 and d.id=f.mst_id  and d.is_deleted=0 and d.status_active=1 and f.is_deleted=0 and f.status_active=1 $company_cond $date_cond  $location_cond $floor_cond  group by d.serving_company,d.production_date,d.floor_id
";


	$dhu_qc_sql_result=sql_select($dhu_sql);
	foreach($dhu_qc_sql_result as $row){
		$monthkey=date("Y-m",strtotime($row[csf('production_date')]));
		$dhu_qc_data_array[$monthkey]+=($row[csf('qc_pass_qty')]+$row[csf('alter_qnty')]+$row[csf('reject_qnty')]+$row[csf('spot_qnty')]+$row[csf('replace_qty')]);
	}
	
	
	
	
	foreach($yr_mon_part as $Ym){
		$dhu_qty_array[]=number_format($dhu_defet_data_array[$Ym]/$dhu_qc_data_array[$Ym]*100,2,".","");
	}
	$dhu_qty_array[]=number_format((array_sum($dhu_qty_array)/($remain_months+1)),2,".","");



//DHU-------------------------------------------------end;
	
	
//Avg FoB in $------------------------------------start;
	
	if($workingCompany){$company_cond=" and a.style_owner=$workingCompany";}else{$company_cond=" and a.company_name=$lccompany";}
	$date_cond=" and c.country_ship_date between '$start_date' and  '$end_date'";
	if($location){$location_cond=" and a.working_location_id=$location";}
	
	$sql="select 
    a.style_owner,c.country_ship_date,
    sum(c.order_total) val , sum(c.order_quantity) as qty
    from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
     where 
     a.job_no=b.job_no_mst and 
     a.job_no=c.job_no_mst and 
     b.id=c.po_break_down_id 
     $company_cond
  	 $date_cond and
     a.status_active=1 and 
     a.is_deleted=0 and 
     b.status_active=1 and 
     b.is_deleted=0 and 
     c.status_active=1 and 
     c.is_deleted=0
     group by a.style_owner,c.country_ship_date";

	$avg_fob_sql_result=sql_select($sql);
	foreach ($avg_fob_sql_result as $row){
		$monthkey=date("Y-m",strtotime($row[csf('country_ship_date')]));
		
		$ex_fac_fob_val_array[$monthkey]+=$row[csf("val")];
		$ex_fac_fob_qty_array[$monthkey]+=$row[csf("qty")];
	}
	
	foreach($yr_mon_part as $Ym){
		$ex_fac_fob_array[]=number_format($ex_fac_fob_val_array[$Ym]/$ex_fac_fob_qty_array[$Ym],2,".","");
	}
	$ex_fac_fob_array[]=number_format((array_sum($ex_fac_fob_array)/($remain_months+1)),2,".","");

//Avg FoB in $------------------------------------end;
	
	
	
		
		
		
		$sewing_efficiency_array= json_encode($sewing_efficiency_array);
		$poly_efficiency_array= json_encode($poly_efficiency_array);
		$cut_panel_rejection_array= json_encode($cut_panel_rejection_array); 
		$cut_to_shipment_ratio_array= json_encode($cut_to_shipment_ratio_array); 
		$poly_to_shipment_ratio_array= json_encode($poly_to_shipment_ratio_array); 
		
		$man_machine_ratio_array = json_encode($man_machine_ratio_array); 
		$re_check_qty_array = json_encode($re_check_qty_array); 
		$air_exfactory_qty_array = json_encode($air_exfactory_qty_array); 
		$avg_smv_qty_array = json_encode($avg_smv_qty_array); 
		$dhu_qty_array = json_encode($dhu_qty_array); 
		
		$ex_fac_fob_array = json_encode($ex_fac_fob_array); 
		
		

	
    ?>
    <script>
        var barChartData = {
            labels : <? echo $month_array; ?>,
            datasets : [
                {
                    fillColor : "#C9662B",
				    //strokeColor : "rgba(151,187,205,0.8)",
                    //highlightFill : "rgba(252,177,40,0.50)",
                    //highlightStroke : "rgba(151,187,205,1)",
                    data : <? echo $sewing_efficiency_array; ?>
                },
                {
                    fillColor : "#FCB22B",
                    //strokeColor : "rgba(151,187,205,0.8)",
                    // highlightFill : "rgba(200,100,40,0.50)",
                    //highlightStroke : "rgba(151,187,205,1)",
                    data : <? echo $poly_efficiency_array; ?>
                }
            ]
        }
       
        var barChartData2 = {
            labels : <? echo $month_array; ?>,
            datasets : [
                {
                    fillColor : "rgba(236,21,40,0.99)",
                    //strokeColor : "rgba(220,220,220,0.8)",
                    highlightFill :  "rgba(236,21,40,0.1)",
                    //highlightStroke: "rgba(220,220,220,1)",
                    data : <? echo $cut_panel_rejection_array; ?>
                }
            ]
        }
        
        var barChartData3 = {
            labels : <? echo $month_array; ?>,
            datasets : [
                {
                    fillColor : "rgba(100,220,220,0.99)",
                    //strokeColor : "rgba(220,220,220,0.8)",
                    highlightFill : "rgba(100,220,220,0.1)",
                    //highlightStroke: "rgba(220,220,220,1)",
                    data : <? echo $cut_to_shipment_ratio_array; ?>
                }
            ]
        }
        
         var barChartData4 = {
            labels : <? echo $month_array; ?>,
            datasets : [
                {
                    fillColor : "rgba(120,210,22,0.99)",
                    highlightFill : "rgba(120,210,22,0.1)",
                    data : <? echo $poly_to_shipment_ratio_array; ?>
                }
            ]

        }
        
       var barChartData5 = {
            labels : <? echo $month_array; ?>,
            datasets : [
                {
                    fillColor : "rgba(100,100,220,0.99)",
                    highlightFill : "rgba(100,100,220,0.1)",
                    data : <? echo $man_machine_ratio_array; ?>
                }
            ]
        }
	
	
       var barChartData6 = {
            labels : <? echo $month_array; ?>,
            datasets : [
                {
                    fillColor : "rgba(233,100,220,0.99)",
                    highlightFill : "rgba(100,100,220,0.1)",
                    data : <? echo $re_check_qty_array; ?>
                }
            ]
        }
	
	
       var barChartData7 = {
            labels : <? echo $month_array; ?>,
            datasets : [
                {
                    fillColor : "rgba(233,100,220,0.99)",
                    highlightFill : "rgba(100,100,220,0.1)",
                    data : <? echo $air_exfactory_qty_array; ?>
                }
            ]
        }
	
	
	
       var barChartData8 = {
            labels : <? echo $month_array; ?>,
            datasets : [
                {
                    fillColor : "rgba(222,180,555,0.99)",
                    highlightFill : "rgba(100,100,220,0.1)",
                    data : <? echo $avg_smv_qty_array; ?>
                }
            ]
        }
	
       var barChartData9 = {
            labels : <? echo $month_array; ?>,
            datasets : [
                {
                    fillColor : "rgba(235,0,0,0.99)",
                    highlightFill : "rgba(100,100,220,0.1)",
                    data : <? echo $dhu_qty_array; ?>
                }
            ]
        }
	
       var barChartData10 = {
            labels : <? echo $month_array; ?>,
            datasets : [
                {
                    fillColor : "rgba(0,164,239,0.99)",
                    highlightFill : "rgba(100,100,220,0.1)",
                    data : <? echo $ex_fac_fob_array; ?>
                }
            ]
        }
	
	
	
    
        window.onload = function(){
            var ctx = document.getElementById("canvas").getContext("2d");
            window.myBar = new Chart(ctx).Bar(barChartData, {
                responsive : true
            });
            
            var ctx2 = document.getElementById("canvas2").getContext("2d");
            window.myBar = new Chart(ctx2).Bar(barChartData2, {
                responsive : true
            });
            
            var ctx3 = document.getElementById("canvas3").getContext("2d");
            window.myBar = new Chart(ctx3).Bar(barChartData3, {
                responsive : true
            });
            
			
            var ctx4 = document.getElementById("canvas4").getContext("2d");
            window.myBar = new Chart(ctx4).Bar(barChartData4, {
                responsive : true
            });
			
          var ctx5 = document.getElementById("canvas5").getContext("2d");
            window.myLine = new Chart(ctx5).Bar(barChartData5, {
                responsive: true
            });
			
          var ctx6 = document.getElementById("canvas6").getContext("2d");
            window.myLine = new Chart(ctx6).Bar(barChartData6, {
                responsive: true
            });
			
          var ctx7 = document.getElementById("canvas7").getContext("2d");
            window.myLine = new Chart(ctx7).Bar(barChartData7, {
                responsive: true
            });
			
          var ctx8 = document.getElementById("canvas8").getContext("2d");
            window.myLine = new Chart(ctx8).Bar(barChartData8, {
                responsive: true
            });
          var ctx9 = document.getElementById("canvas9").getContext("2d");
            window.myLine = new Chart(ctx9).Bar(barChartData9, {
                responsive: true
            });
			
          var ctx10 = document.getElementById("canvas10").getContext("2d");
            window.myLine = new Chart(ctx10).Bar(barChartData10, {
                responsive: true
            });
			
			
			
			
        }
    </script>
 
<?

}

function add_month($orgDate,$mon){
  $cd = strtotime($orgDate);
  $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,1,date('Y',$cd)));
  return $retDAY;
}


?>
