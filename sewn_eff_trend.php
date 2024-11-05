<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Graph Will Create 30 days sewn eff trend
Functionality	:	
JS Functions	:
Created by		:	Sohel 
Creation date 	: 	22.09.2015
Updated by 		: 	Md. Saidul Islam Reza		
Update date		: 	01-04-2018 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
require_once('includes/common.php');

echo load_html_head_contents("30 days sewn eff trend", "", "", $popup, $unicode, $multi_select, $amchart);//this $amchart is not wark in this page
 
extract( $_REQUEST );
$m= base64_decode($m);
$cps=explode("__",$cp);

$company=$cps[0];
$location=$cps[1];
$floor=$cps[2];
$pro_company=$cps[3];

if($company!=0){$company=$company;}else{$company="";}
$comp_arr=return_library_array("select id,company_name from lib_company", "id","company_name");
$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
?>
	<script>
    	var lnk='<? echo $m; ?>';
    </script>
	<script src="Chart.js-master/Chart.js"></script>
    <div align="center" style="width:100%;">
        <div style="margin-left:30px; margin-top:10px">
        <!--<a href="index.php">Main Page</a>&nbsp;&nbsp;<a href="index.php?g=1">Dash Board</a>&nbsp;&nbsp;<br />&nbsp;&nbsp;-->
            <div align="center" style="width:900px; height:500px;  margin-left:20px; border:solid 1px">
                <div align="center" style="width:100%; font-size:16px;"><? echo $caption; ?></div>
                <div align="center" style="width:100%; font-size:14px;"><? echo " Company : ". $comp_arr[$company?$company:$pro_company]; ?></div>
                <div align="center" style="width:100%; font-size:12px;">30 Days Sewing Efficiency Trend In %</div>
                <canvas id="canvas6" height="400" width="850"></canvas>
            </div>
        </div>
    </div>
    <br />
<?
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$datediff=30; 
	$today=date('Y-m-d'); 
	if($db_type==0)
	{
		$firstDate = date("Y-m-d", strtotime("-29 day", strtotime($today)));
		$lastDate = date("Y-m-d", strtotime($today));
	}
	else
	{
		$firstDate = date("d-M-Y", strtotime("-29 day", strtotime($today)));
		$lastDate = date("d-M-Y", strtotime($today));	
	}
	
	for($j=0;$j<$datediff;$j++)
	{
		$newdate =add_date($firstDate,$j);
		$date_array[$j]=date("d-M", strtotime($newdate));
	} 
	//print_r($date_array);
	if($pro_company){$companyCon="and serving_company=$pro_company";}else{$companyCon="and company_id=$company";}
	if($location!=0 and $location!="") $location_cond_effi= "and location=$location "; else $location_cond_effi="";
	if($floor!=0 and $floor!="") $floor_con= "and floor_id=$floor "; else $floor_con="";

	$effi_data_arr=array(); $po_id_arr=array();
	
	//$proSql="select production_date, sewing_line, prod_reso_allo, po_break_down_id, item_number_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=5 and prod_reso_allo=1 $companyCon and production_date between '$firstDate' and '$lastDate' $location_cond_effi $floor_con group by production_date, sewing_line, prod_reso_allo, po_break_down_id, item_number_id";
	
	 $proSql="select a.production_date, a.sewing_line, a.prod_reso_allo, a.po_break_down_id, a.item_number_id, sum(b.production_qnty) as production_quantity 
  from pro_garments_production_mst a, pro_garments_production_dtls b
  where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type=5 $companyCon $location_cond_effi $floor_con and a.prod_reso_allo=1 and a.production_date between '$firstDate' and '$lastDate' group by a.production_date, a.sewing_line, a.prod_reso_allo, a.po_break_down_id, a.item_number_id";
	
	$sew_data_arr=sql_select( $proSql);//used for daily prduction
	foreach($sew_data_arr as $row)
	{
		$production_date=date("Y-m-d", strtotime($row[csf('production_date')])); 
		$effi_data_arr[$production_date].=$row[csf('sewing_line')]."**".$row[csf('production_quantity')]."**".$row[csf('po_break_down_id')]."**".$row[csf('item_number_id')]."**".$row[csf('prod_reso_allo')].",";
		$po_id_arr[$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
	}
	
	
	if($location!=0 and $location!="") $location_cond_subcon= "and a.location_id=$location "; else $location_cond_subcon="";
	if($pro_company){$companyCon="and a.company_id=$pro_company";}else{$companyCon="and a.company_id=$company";}
	if($floor!=0 and $floor!="") $floor_con= "and a.floor_id=$floor "; else $floor_con="";

	$subConProd_arr=array(); $subAchvSmv_arr=array();
	$sql_subconProd="SELECT a.gmts_item_id as item_number_id,a.line_id as sewing_line,a.order_id, a.production_date, a.production_qnty, b.smv ,a.prod_reso_allo FROM subcon_gmts_prod_dtls a, subcon_ord_dtls b WHERE a.order_id=b.id $companyCon and a.production_date between '$firstDate' and '$lastDate' and a.production_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $location_cond_subcon  $floor_con";
	$subconProdData=sql_select($sql_subconProd);
	foreach($subconProdData as $row)
	{
		 $production_date=date("Y-m-d", strtotime($row[csf('production_date')]));
		 $sewProdArr[$production_date]+=$row[csf('production_qnty')];
		 $subAchvSmv_arr[$production_date]+=$row[csf('production_qnty')]*$row[csf('smv')];
		 $effi_data_arr[$production_date].=$row[csf('sewing_line')]."**".$row[csf('production_qnty')]."**".$row[csf('order_id')]."**".$row[csf('item_number_id')]."**".$row[csf('prod_reso_allo')].",";

	}
	
	
	
	$company_name=$company;
	$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($company) and variable_list=25 and status_active=1 and is_deleted=0");
	if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
	
	if($location!=0 and $location!="") $location_cond_item= "and a.location_name=$location "; else $location_cond_item="";
	
	$item_smv_array=array();
	if($smv_source==3)
	{
		$sql_item="select b.id, a.sam_style, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$resultItem=sql_select($sql_item);
		foreach($resultItem as $itemData)
		{
			$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
		}
	}
	else
	{
		
		if($pro_company){
			$whereCon =" and";
			$po_list_arr=array_chunk($po_id_arr,999);
			$p=1;
			foreach($po_list_arr as $po_process)
			{
				if($p==1){$whereCon .=" ( b.id in(".implode(',',$po_process).")";} 
				else{$whereCon .=" or b.id in(".implode(',',$po_process).")";}
				$p++;
			}
			$whereCon .=")";
		}
		else
		{
			$whereCon="and a.company_name=$company";	
		}
		
		
		
		$sql_item="select b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no $whereCon and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ";
		$resultItem=sql_select($sql_item);
		foreach($resultItem as $itemData)
		{
			
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]['smv_pcs']=$itemData[csf('smv_pcs')];
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]['smv_pcs_precost']=$itemData[csf('smv_pcs_precost')];
		}
	}
	//var_dump($item_smv_array);

	
	if($pro_company){$companyCon="and a.company_id=$pro_company";}else{$companyCon="and a.company_id=$company";}
	if($location!=0 and $location!="") $location_cond= "and a.location_id=$location "; else $location_cond="";
	if($floor!=0 and $floor!="") $floor_con= "and a.floor_id=$floor "; else $floor_con="";

	$tpdArr=array(); $tsmvArr=array();
	$tpd_data_arr=sql_select("select a.id, b.pr_date, b.man_power, b.smv_adjust,b.smv_adjust_type, b.target_per_hour, b.working_hour from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id $companyCon and b.pr_date between '$firstDate' and '$lastDate' and b.is_deleted=0 and c.is_deleted=0 $location_cond $floor_con group by a.id, b.pr_date, b.man_power, b.smv_adjust,b.smv_adjust_type, b.target_per_hour, b.working_hour ");
	foreach($tpd_data_arr as $row)
	{
		$production_date=date("Y-m-d", strtotime($row[csf('pr_date')])); 
		$tsmvArr[$production_date][$row[csf('id')]]+=$row[csf('man_power')]*$row[csf('working_hour')]*60;
		
		if($row[csf('smv_adjust_type')]==1) 
		{
			$tsmvArr[$production_date][$row[csf('id')]]+=$row[csf('smv_adjust')];
		}
		else if($row[csf('smv_adjust_type')]==2) 
		{
			$tsmvArr[$production_date][$row[csf('id')]]-=$row[csf('smv_adjust')];
		}
	}

	$prod_capacity_arr=array(); $dyeing_capacity_arr=array();	
	for($j=0;$j<$datediff;$j++)
	{
		$newdate=add_date($firstDate,$j);
		$prod_date=date("Y-m-d", strtotime($newdate)); 
		
		$achv_smv=0;
		$today_smv=0;
		$effi_data=explode(",",substr($effi_data_arr[$prod_date],0,-1));
		foreach($effi_data as $data)
		{
			$data=explode("**",$data);
			$sewing_line=$data[0];
			$production_quantity=$data[1];
			$prod_reso_allo=$data[4];
			//echo $prod_date."ss".$sewing_line."dd";
			
			/*if( $line_arr_check[$sewing_line][$prod_date]=='')
			{
				$line_arr_check[$sewing_line][$prod_date]=$sewing_line;
				if($prod_reso_allo==1){$today_smv+=$tsmvArr[$prod_date][$sewing_line];}
			}*/
			if($prod_reso_allo==1){$today_smv_arr[$prod_date][$sewing_line]=$tsmvArr[$prod_date][$sewing_line];}
			
			
			$po_break_down_id=$data[2];
			$item_number_id=$data[3];
			
			$item_smv=0;
			if($smv_source==2)
			{
				$item_smv=$item_smv_array[$po_break_down_id][$item_number_id]['smv_pcs_precost'];
			}
			else if($smv_source==3)
			{
				$item_smv=$item_smv_array[$po_break_down_id][$item_number_id];	
			}
			else
			{
				$item_smv=$item_smv_array[$po_break_down_id][$item_number_id]['smv_pcs'];	
			}
			$achv_smv+=$production_quantity*$item_smv;
		}
		$achv_smv+=$subAchvSmv_arr[$prod_date];
		$today_smv=array_sum($today_smv_arr[$prod_date]);
		
		//echo $prod_date."==".$achv_smv."==".$today_smv."ssssssss";
		 //echo $prod_date."**".$achv_smv."**".$today_smv."<br>";
		/*echo '<pre>';
		if($prod_date=='2018-04-11'){var_dump($today_smv_arr[$prod_date]);}
		echo '</pre>';*/
		
		$today_aff_perc=$achv_smv/$today_smv*100;
		if(is_nan($today_aff_perc)==true){$today_aff_perc=0;}
		$effi_perc_arr[]=number_format($today_aff_perc,2,'.','');
		//$effi_perc_arr[]=$achv_smv;
	}
	//print_r($effi_perc_arr);
	//var_dump($date_wise_active_line);
	
	$date_array= json_encode($date_array); 
	$effi_perc_arr= json_encode($effi_perc_arr);
	?>
	<script>
		var lineChartData3 = {
			labels : <? echo $date_array; ?>,
			datasets : [
			{
				//label: "My First dataset",
				fillColor : "rgba(220,220,220,0.2)",
				strokeColor : "#0066FF",
				pointColor : "#0066FF",
				pointStrokeColor : "#fff",
				pointHighlightFill : "#fff",
				pointHighlightStroke : "#0066FF",
				data : <? echo $effi_perc_arr; ?>
			}
			]
		}
		
		var ctx = document.getElementById("canvas6").getContext("2d");
		window.myLine = new Chart(ctx).Line(lineChartData3, {
			responsive: true
		});
		
    </script>
<?

function add_month($orgDate,$mon)
{
	$cd = strtotime($orgDate);
	$retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,1,date('Y',$cd)));
	return $retDAY;
}

?>

     