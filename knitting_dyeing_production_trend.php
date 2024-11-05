<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Graph Will Create Last 30 days Knit Prod Trend and Last 30 days Dyeing Prod Trend
Functionality	:	
JS Functions	:
Created by		:	Sohel 
Creation date 	: 	01.10.2015
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
require_once('includes/common.php');

echo load_html_head_contents("30 days Knit Dyeing Production trend", "", "", $popup, $unicode, $multi_select, $amchart);//this $amchart is not wark in this page
 
extract( $_REQUEST );
$m= base64_decode($m);
$cps=explode("__",$cp);

$company=$cps[0];
$location=$cps[1];
$floor=$cps[2];
$pro_company=$cps[3];
//echo $location;

if($m=="30_days_knit_eff_trend")
{
	$caption="Knitting Production Trend";	
}
else
{
	$caption="Dyeing Production Trend";	
}

if($company!=0)
{
	$company=$company;
}
else
{
	$company="";
}

if($location!="" and $location!=0) $location_cond= "and location_id=$location "; else $location_cond="";

$comp_arr=return_library_array("select id,company_name from lib_company", "id","company_name");
?>
	<script>
    	var lnk='<? echo $m; ?>';
    </script>
	<script src="Chart.js-master/Chart.js"></script>
    <div align="center" style="width:100%;">
        <div style="margin-left:30px; margin-top:10px">
        <!--<a href="index.php">Main Page</a>&nbsp;&nbsp;<a href="index.php?g=1">Dash Board</a>&nbsp;&nbsp;<br />&nbsp;&nbsp;-->
            <div style="width:900px; height:500px;  position:relative; margin-left:10px; margin-top:5px; border:solid 1px">
                <div align="center" style="width:100%; font-size:16px;"><? echo $caption; ?></div>
                <div align="center" style="width:100%; font-size:14px;"><? echo " Company : ". $comp_arr[$company?$company:$pro_company]; ?></div>
                <table style="margin-left:60px; font-size:12px" align="left">
                    <tr>
                        <td bgcolor="#FF3300" width="10"></td>
                        <td>Capacity</td>
                        <td bgcolor="#0066FF" width="10"></td>
                        <td>Production</td>
                    </tr>
                </table>
                <canvas id="canvas8" height="400" width="850"></canvas>
            </div>
        </div>
    </div>
    <br />
<?

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_name=$company;
	
	$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($company) and variable_list=25 and status_active=1 and is_deleted=0");
	if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
	
	if($pro_company){$companyCon="and company_id=$pro_company";}else{$companyCon="and company_id=$company";}

	$machine_arr=array(); $machine_id_arr=array(); $machine_dyeing_id_arr=array();
	$machine_sql_arr = sql_select("select id, prod_capacity, category_id from lib_machine_name where category_id in(1,2) $companyCon and is_deleted=0 and status_active=1 $location_cond");
	foreach($machine_sql_arr as $machineRow)
	{
		$machine_arr[$machineRow[csf('id')]]=$machineRow[csf('prod_capacity')];
		
		if($machineRow[csf('category_id')]==1)
		{
			$machine_id_arr[]=$machineRow[csf('id')];
		}
		else
		{
			$machine_dyeing_id_arr[]=$machineRow[csf('id')];
		}
	}
	//print_r($machine_id_arr);
	
	$idle_machine_array=array();
	$sql_machine_idle=sql_select("select machine_entry_tbl_id, from_date, to_date from pro_cause_of_machine_idle where machine_idle_cause in(1,2,3,6,7,8) and is_deleted=0 and status_active=1");
	foreach($sql_machine_idle as $idleRow)
	{
		$from_date=date("Y-m-d", strtotime($idleRow[csf('from_date')]));
		$to_date=date("Y-m-d", strtotime($idleRow[csf('to_date')]));
		$datediff_n = datediff( 'd', $from_date, $to_date);
		for($k=0; $k<$datediff_n; $k++)
		{
			$newdate_n=add_date(str_replace("'","",$from_date),$k);
			$idle_machine_array[$newdate_n].=$idleRow[csf('machine_entry_tbl_id')].",";
		}
	}
	
	$datediff=30; $today=date('Y-m-d'); //$today='2014-06-04';
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
	//echo $firstDate."".$lastDate;
	
	for($j=0;$j<$datediff;$j++)
	{
		$newdate =add_date($firstDate,$j);
		$date_array[$j]=date("d-M", strtotime($newdate));
	} 
	//print_r($date_array);
	
	$yarn_stock_array=array(); $knit_array=array(); $dye_array=array(); 
	$sql_yarn="select "; $sql_knit="select "; $sql_subcon="select "; $sql_dye="select ";
	for($j=0;$j<$datediff;$j++)
	{
		$newdate =add_date($firstDate,$j);
		$date_array[$j]=date("d-M", strtotime($newdate));
		if($db_type==0) $trans_date=date("Y-m-d", strtotime($newdate)); else $trans_date=date("d-M-Y", strtotime($newdate));
		if($j!=0) $add_comma=',';
		$sql_yarn.="$add_comma sum(case when a.transaction_type in (1,4) and a.transaction_date<='".$trans_date."' then a.cons_quantity else 0 end) as recv_$j,
					sum(case when a.transaction_type in (2,3) and a.transaction_date<='".$trans_date."' then a.cons_quantity else 0 end) as issue_$j";
		
		$sql_knit.="$add_comma sum(case when a.receive_date='".$trans_date."' then b.grey_receive_qnty else 0 end) as knit_$j";
		$sql_dye.="$add_comma sum(case when c.process_end_date='".$trans_date."' then b.batch_qnty else 0 end) as dye_$j";
		
		$sql_subcon.="$add_comma sum(case when a.product_type=2 and a.product_date='".$trans_date."' then b.product_qnty else 0 end) as knit_subcon_$j";
	} 
        
	if($pro_company){$companyConYarn="and a.company_id=$pro_company";}else{$companyConYarn="and a.company_id=$company";}
	
	$sql_yarn.=" FROM inv_transaction a, product_details_master b WHERE a.prod_id=b.id $companyConYarn and a.item_category=1 and a.transaction_type in(1,2,3,4) and a.transaction_date<='$lastDate' and a.status_active=1 and a.is_deleted=0 and b.item_category_id=1 and b.status_active=1 and b.is_deleted=0";
	$yarnData=sql_select($sql_yarn);
	
	if($pro_company){$companyConKnit="and a.knitting_company=$pro_company";}else{$companyConKnit="and a.company_id=$company";}
	
	$sql_knit.=" FROM inv_receive_master a, pro_grey_prod_entry_dtls b WHERE a.id=b.mst_id $companyConKnit and a.item_category=13 and a.entry_form=2 and a.knitting_source=1 and a.receive_date<='$lastDate' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$knitData=sql_select($sql_knit);
	
	
	$sql_dye.=" from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and c.load_unload_id=2 and c.entry_form in(35,38) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.process_end_date<='$lastDate' and c.result in(1,5)";
	$dyeData=sql_select($sql_dye);
	
	
	if($pro_company){$companyConSub="and a.knitting_company=$pro_company";}else{$companyConSub="and a.company_id=$company";}
	$sql_subcon.=" FROM subcon_production_mst a, subcon_production_dtls b WHERE a.id=b.mst_id $companyConSub and a.product_date<='$lastDate' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$subconData=sql_select($sql_subcon);
	
	$tpdArr=array(); $tsmvArr=array();
	$tpd_data_arr=sql_select( "select b.id, a.pr_date, sum(a.target_per_hour*a.working_hour) tpd, sum(a.man_power*a.working_hour) tsmv from prod_resource_dtls a, prod_resource_mst b where b.id=a.mst_id and b.company_id in($company) and a.pr_date between '$firstDate' and '$lastDate' and a.is_deleted=0 and b.is_deleted=0 group by b.id, a.pr_date");
	foreach($tpd_data_arr as $row)
	{
		$production_date=date("Y-m-d", strtotime($row[csf('pr_date')])); 
		$tpdArr[$production_date]+=$row[csf('tpd')];
		$tsmvArr[$production_date]+=$row[csf('tsmv')]*60;
	}
	
	
	if($pro_company){$companyCon="and serving_company=$pro_company";}else{$companyCon="and company_id=$company";}
	if($location!="" and $location!=0) $location_cond_garments_production= "and location=$location "; else $location_cond_garments_production="";
	$sewProdArr=array(); $lineArr=array(); $effi_data_arr=array(); $effi_perc_arr=array(); $sew_target_achv_trend_arr=array(); 
	$sew_data_arr=sql_select( "select production_date, sewing_line, prod_reso_allo, po_break_down_id, item_number_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=5 $companyCon and production_date between '$firstDate' and '$lastDate' $location_cond_garments_production group by production_date, sewing_line, prod_reso_allo, po_break_down_id, item_number_id");//used for daily prduction
	foreach($sew_data_arr as $row)
	{
		$production_date=date("Y-m-d", strtotime($row[csf('production_date')])); 
		$sewProdArr[$production_date]+=$row[csf('production_quantity')];
		$effi_data_arr[$production_date].=$row[csf('sewing_line')]."**".$row[csf('production_quantity')]."**".$row[csf('po_break_down_id')]."**".$row[csf('item_number_id')]."**".$row[csf('prod_reso_allo')].",";
		$po_id_arr[$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
	}
	
	if($location!="" and $location!=0) $location_cond_subcon= "and a.location_id=$location "; else $location_cond_subcon="";
	if($pro_company){$companyCon="and a.company_id=$pro_company";}else{$companyCon="and a.company_id=$company";}

	$subConProd_arr=array(); $subAchvSmv_arr=array();
	$sql_subconProd="select a.order_id, a.production_date, a.production_qnty, b.smv FROM subcon_gmts_prod_dtls a, subcon_ord_dtls b WHERE a.order_id=b.id $companyCon and a.production_date between '$firstDate' and '$lastDate' and a.production_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $location_cond_subcon ";
	$subconProdData=sql_select($sql_subconProd);
	foreach($subconProdData as $row)
	{
		 $production_date=date("Y-m-d", strtotime($row[csf('production_date')]));
		 $sewProdArr[$production_date]+=$row[csf('production_qnty')];
		 $subAchvSmv_arr[$production_date]+=$row[csf('production_qnty')]*$row[csf('smv')];
	}
	
	
	if($location!="" and $location!=0) $location_cond_item_smv= "and a.location_name=$location "; else $location_cond_item_smv="";
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
		
		
		
		$sql_item="select b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no $whereCon and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $location_cond_item_smv";
		$resultItem=sql_select($sql_item);
		foreach($resultItem as $itemData)
		{
			$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]['smv_pcs']=$itemData[csf('smv_pcs')];
			$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]['smv_pcs_precost']=$itemData[csf('smv_pcs_precost')];
		}
	}

	
	
	
	$prod_capacity_arr=array(); $dyeing_capacity_arr=array();	
	for($j=0;$j<$datediff;$j++)
	{
		$newdate=add_date($firstDate,$j);
		$prod_date=date("Y-m-d", strtotime($newdate)); 
		
		if($tpdArr[$prod_date]<=0) $tpd=0; else $tpd=$tpdArr[$prod_date];
		$tpd_arr[]=$tpd;
		
		if($sewProdArr[$prod_date]<=0) $production_quantity=0; else $production_quantity=$sewProdArr[$prod_date];
		$production_quantity+=$subconProdData[0][csf('subconProd_'.$j)];
		$sew_prod_arr[]=$production_quantity;
		
		$sew_target_achv_trend=($production_quantity*100)/$tpd;
		if($sew_target_achv_trend=="") $sew_target_achv_trend=0;
		$sew_target_achv_trend_arr[]=number_format($sew_target_achv_trend,2,'.','');
		
		$today_smv=0;
		$today_smv=$tsmvArr[$prod_date];

		$achv_smv=0;
		$effi_data=explode(",",substr($effi_data_arr[$prod_date],0,-1));
		foreach($effi_data as $data)
		{
			$data=explode("**",$data);
			$sewing_line=$data[0];
			$production_quantity=$data[1];
			$po_break_down_id=$data[2];
			$item_number_id=$data[3];
			$prod_reso_allo=$data[4];
		   
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
		//echo $prod_date."==".$achv_smv."**".$today_smv."<br>";
		$today_aff_perc=$achv_smv/$today_smv*100;
		$effi_perc_arr[]=number_format($today_aff_perc,2,'.','');

		$yarn_stcok=$yarnData[0][csf('recv_'.$j)]-$yarnData[0][csf('issue_'.$j)];
		$yarn_stock_array[]=number_format($yarn_stcok,2,'.','');
		
		$knit_array[]=number_format($knitData[0][csf('knit_'.$j)]+$subconData[0][csf('knit_subcon_'.$j)],2,'.','');
		$dye_array[]=number_format($dyeData[0][csf('dye_'.$j)],2,'.','');
		
		$idle_machine=explode(",",substr($idle_machine_array[$prod_date],0,-1));
		$active_machine=array_diff($machine_id_arr,$idle_machine);
		$machine_capacity=0;
		foreach($active_machine as $machine)
		{
			$machine_capacity+=$machine_arr[$machine];
		}
		$prod_capacity_arr[]=$machine_capacity;
		
		$dye_active_machine=array_diff($machine_dyeing_id_arr,$idle_machine);
		$dye_machine_capacity=0;
		foreach($dye_active_machine as $machine_dye)
		{
			$dye_machine_capacity+=$machine_arr[$machine_dye];
		}
		$dyeing_capacity_arr[]=$dye_machine_capacity;
	}
	
	$date_array= json_encode($date_array); 
	$prod_capacity_arr= json_encode($prod_capacity_arr);
	$knit_array= json_encode($knit_array);
	
	$dyeing_capacity_arr= json_encode($dyeing_capacity_arr);
	$dye_array= json_encode($dye_array);
		
	?>
	<script>
	
	if(lnk=='30_days_knit_eff_trend')
	{
		 var lineChartData5 = {
            labels : <? echo $date_array; ?>,
            datasets : [
                {
                    //label: "My First dataset",
                    fillColor : "rgba(220,220,220,0.2)",
                    strokeColor : "#FF3300",
                    pointColor : "#FF3300",
                    pointStrokeColor : "#fff",
                    pointHighlightFill : "#fff",
                    pointHighlightStroke : "#FF3300",
                    data : <? echo $prod_capacity_arr; ?>
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
                    data : <? echo $knit_array; ?>
                }
            ]

        }
		window.onload = function(){
			var ctx = document.getElementById("canvas8").getContext("2d");
			window.myLine = new Chart(ctx).Line(lineChartData5, {
			responsive: true
			});
		}
	}
	else
	{
		 var lineChartData6 = {
            labels : <? echo $date_array; ?>,
            datasets : [
                {
                    //label: "My First dataset",
                    fillColor : "rgba(220,220,220,0.2)",
                    strokeColor : "#FF3300",
                    pointColor : "#FF3300",
                    pointStrokeColor : "#fff",
                    pointHighlightFill : "#fff",
                    pointHighlightStroke : "#FF3300",
                    data : <? echo $dyeing_capacity_arr; ?>
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
                    data : <? echo $dye_array; ?>
                }
            ]

        }
		 window.onload = function(){
		 var ctx = document.getElementById("canvas8").getContext("2d");
            window.myLine = new Chart(ctx).Line(lineChartData6, {
                responsive: true
            });
		 }
	}
	
	</script>
    
<?

function add_month($orgDate,$mon)
{
	$cd = strtotime($orgDate);
	$retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,1,date('Y',$cd)));
	return $retDAY;
}

?>
        
     