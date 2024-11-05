<?
/*--------------------------------------------Comments----------------
Version (MySql)          :  V1
Version (Oracle)         :  V1
Converted by             :  MONZU
Converted Date           :  
Purpose			         : 	This form will create Bom Process
Functionality	         :	
JS Functions	         :
Created by		         :	Monzu 
Creation date 	         : 	17-11-2014
Requirment Client        : 
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :                   
DB Script                : 
Updated by 		         : 		
Update date		         : 		   
QC Performed BY	         :		
QC Date			         :	
Comments		         :
----------------------------------------------------------------------*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');

extract($_REQUEST);
$_SESSION['page_permission']=$permission;
echo load_html_head_contents("Order Info","../../", 1, 1, $unicode,1,'');

include('../../includes/class3/class.conditions.php');
include('../../includes/class3/class.reports.php');
include('../../includes/class3/class.fabrics.php');

?>	
</head>
<body>
<?
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
$yarn_color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
$job ="'OG-16-00417'";
$job ="'OG-16-00441'";


$color_qty_array=array();
$sql_color_qty='select a.job_no AS "job_no",a.buyer_name AS "buyer_name",a.style_ref_no "style_ref_no",b.id AS "id",b.po_number as "po_number",b.po_received_date AS "po_received_date",c.color_number_id AS "color_number_id", c.plan_cut_qnty AS "plan_cut_qnty" from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c   where 1=1 and a.job_no='.$job.' and a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1';
$data_color_qty=sql_select($sql_color_qty);
foreach($data_color_qty as $row_color_qty){
	$color_qty_array[$row_color_qty['id']][$row_color_qty['color_number_id']]+=$row_color_qty['plan_cut_qnty'];
}
//echo 'select a.job_no AS "job_no",a.buyer_name AS "buyer_name",a.style_ref_no "style_ref_no",b.id AS "id",b.po_number as "po_number",b.po_received_date AS "po_received_date",b.shipment_date AS "shipment_date",c.color_number_id AS "color_number_id",f.gmts_color_id AS "gmts_color_id",f.contrast_color_id AS "contrast_color_id",d.fabric_description AS "fabric_description",min(d.gsm_weight) AS "gsm_weight",d.lib_yarn_count_deter_id AS "lib_yarn_count_deter_id",AVG(d.rate) AS "rate",AVG(e.cons) AS "cons",AVG(e.requirment) AS "requirment" from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e left join wo_pre_cos_fab_co_color_dtls f on e.pre_cost_fabric_cost_dtls_id=f.pre_cost_fabric_cost_dtls_id and e.color_number_id=f.gmts_color_id  where 1=1 and a.job_no='.$job.' and a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id   and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and e.cons !=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by a.job_no, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.po_received_date, b.shipment_date, d.fabric_description,d.lib_yarn_count_deter_id, c.color_number_id,f.gmts_color_id,f.contrast_color_id order by c.color_number_id';
$contrast_color=array();
$rowspan=array();
$sql_contrast=sql_select('select a.job_no AS "job_no",a.buyer_name AS "buyer_name",a.style_ref_no "style_ref_no",b.id AS "id",b.po_number as "po_number",b.po_received_date AS "po_received_date",b.shipment_date AS "shipment_date",c.color_number_id AS "color_number_id",f.gmts_color_id AS "gmts_color_id",f.contrast_color_id AS "contrast_color_id",d.fabric_description AS "fabric_description",min(d.gsm_weight) AS "gsm_weight",d.lib_yarn_count_deter_id AS "lib_yarn_count_deter_id",AVG(d.rate) AS "rate",AVG(e.cons) AS "cons",AVG(e.requirment) AS "requirment" from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e left join wo_pre_cos_fab_co_color_dtls f on e.pre_cost_fabric_cost_dtls_id=f.pre_cost_fabric_cost_dtls_id and e.color_number_id=f.gmts_color_id  where 1=1 and a.job_no='.$job.' and a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id   and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and e.cons !=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by a.job_no, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.po_received_date, b.shipment_date, d.fabric_description,d.lib_yarn_count_deter_id, c.color_number_id,f.gmts_color_id,f.contrast_color_id order by c.color_number_id');
foreach( $sql_contrast as  $row_contrast){
	if($row_contrast['contrast_color_id']>0){
	$contrast_color[$row_contrast['lib_yarn_count_deter_id']][$row_contrast['color_number_id']][$row_contrast['contrast_color_id']]=$row_contrast['contrast_color_id'];
	}
	else{
	 $contrast_color[$row_contrast['lib_yarn_count_deter_id']][$row_contrast['color_number_id']][$row_contrast['color_number_id']]=$row_contrast['color_number_id'];
	}
	$rowspan[$row_contrast['color_number_id']][$row_contrast['contrast_color_id']]=$row_contrast['contrast_color_id'];
}
	
	
$sql='select a.job_no AS "job_no",a.buyer_name AS "buyer_name",a.style_ref_no "style_ref_no",b.id AS "id",b.po_number as "po_number",b.po_received_date AS "po_received_date",b.shipment_date AS "shipment_date",c.color_number_id AS "color_number_id",d.fabric_description AS "fabric_description",min(d.gsm_weight) AS "gsm_weight",d.lib_yarn_count_deter_id AS "lib_yarn_count_deter_id",AVG(d.rate) AS "rate",AVG(e.cons) AS "cons",AVG(e.requirment) AS "requirment" from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e where 1=1 and a.job_no='.$job.' and a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and e.cons !=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by a.job_no, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.po_received_date, b.shipment_date, d.fabric_description,d.lib_yarn_count_deter_id, c.color_number_id order by c.color_number_id';

$data=sql_select($sql);


$condition= new condition();
	if(str_replace("'","",$job) !=''){
		$condition->job_no("=$job");
	}
	$condition->init();
	$fabric= new fabric($condition);
	$fabric_costing_arr=$fabric->getQtyArray_by_LibYarnCountDeterId_knitAndwoven_greyAndfinish();
	ob_start();
?>

<table width="4450" id="table_header_1" border="1" class="rpt_table" rules="all">
    <thead>
        <tr>
            <th width="30">Booking rcvd date</th>
            <th width="50">Booking rcvd date</th>
            <th width="50" >OPD Date</th>
            <th width="50">Buyer</th>
            <th width="50">Order No</th>
            <th width="50">Job No</th>
            <th width="60">style ref</th>                   
            <th width="100">Color Name (Unique to Booking col)</th>
            
            <th width="100">Required Ship Qty (Col wise Pcs)</th>
            <th width="130">Fabrication</th>
            <th width="30">GSM</th>
            <th width="100">Fabric Color</th>
            <th width="80">Shipment date</th>
            <th width="90">Avg Fab Consumption per dozen</th>
            <th width="100">Knit TOD Start</th>
            <th width="100">Knit TOD End</th>
            <th width="100">Fabric Del Start</th> 
            <th width="100">Fabric Del End</th>
            <th width="80">Actual Knit Start date</th>
            <th width="80">Actual Knit End date</th>
            <th width="100">Actual Fabric Del Start Date</th>
            <th width="100">Actual Fabric Del End Date</th>
            <th width="90">Cutting Start Date</th>
            <th width="90">Cutting End Date</th>
            <th width="100">Actual Cutting Start Date</th>
            <th width="80">Actual Cutting End Date</th>                                                         
            <th width="80">PP Date</th>
            <th width="80">PCD Date</th>
            <th width="100">Reqd Grey fab QTY( Booking)</th>
            <th width="100">Req Finish fab QTY (Booking)</th>
            <th width="80">Knitted qty</th>
            <th width="80">Knit Bal</th>
            <th width="80">Batch Qty</th>
            <th width="80">Dyed Qty</th>
            <th width="80">Dyed Bal</th>
            <th width="80">Dyeing-Finshing qty</th>
            <th width="80">Dyeing-Finshing Bal</th>
            <th width="60">Finish Fab Del Qty</th>
            <th width="60">Finish Fab Del Bal</th>
            <th width="60">Ship Month</th>
        </tr>
    </thead>
    
    <tbody> 
    <?
	$tnaarray=array();
	$sql_tna='select a.po_number_id AS "po_number_id",a.task_number AS "task_number",a.task_start_date AS "task_start_date", a.task_finish_date AS "task_finish_date", a.actual_start_date AS "actual_start_date", a.actual_finish_date AS "actual_finish_date"  from tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id and a.task_number in(60,73,84,12) and a.po_number_id=7323  and b.status_active=1 and b.po_quantity>0 and b.is_confirmed=1';
	$data_tna=sql_select($sql_tna);
	foreach($data_tna as $row_tna){
		$tnaarray['task_start_date'][$row_tna['task_number']][$row_tna['po_number_id']]=$row_tna['task_start_date'];
		$tnaarray['task_finish_date'][$row_tna['task_number']][$row_tna['po_number_id']]=$row_tna['task_finish_date'];
		$tnaarray['actual_start_date'][$row_tna['task_number']][$row_tna['po_number_id']]=$row_tna['actual_start_date'];
		$tnaarray['actual_finish_date'][$row_tna['task_number']][$row_tna['po_number_id']]=$row_tna['actual_finish_date'];
	}
	
	 $kint_ac=array();
	 $sql_kint_ac='select a.id AS "po_number_id" ,c.febric_description_id AS "febric_description_id",max(b.receive_date) AS "max_receive_date",min(b.receive_date) AS "min_receive_date", sum(d.quantity) AS "quantity"
from  wo_po_break_down a,inv_receive_master b, pro_grey_prod_entry_dtls c,order_wise_pro_details d where b.id=c.mst_id and c.id=d.dtls_id and d.po_breakdown_id=a.id and d.entry_form=2 and  b.entry_form=2 and  a.id=7323 group by a.id,c.febric_description_id';
	$data_kint_ac=sql_select($sql_kint_ac);
	foreach($data_kint_ac as $row_kint_ac){
		$kint_ac['actual_start_date'][$row_kint_ac['febric_description_id']][$row_kint_ac['po_number_id']]=$row_kint_ac['max_receive_date'];
		$kint_ac['actual_finish_date'][$row_kint_ac['febric_description_id']][$row_kint_ac['po_number_id']]=$row_kint_ac['min_receive_date'];
		$kint_ac['quantity'][$row_kint_ac['febric_description_id']][$row_kint_ac['po_number_id']]=$row_kint_ac['quantity'];
	}
	
	$fin_ac=array();
	  $sql_fin_ac='select a.id AS "po_number_id" ,c.fabric_description_id AS "febric_description_id",max(b.receive_date) AS "max_receive_date",min(b.receive_date) AS "min_receive_date", sum(d.quantity) AS "quantity" from wo_po_break_down a,inv_receive_master b, pro_finish_fabric_rcv_dtls c,order_wise_pro_details d where b.id=c.mst_id and c.id=d.dtls_id and d.po_breakdown_id=a.id and d.entry_form=37 and b.entry_form=37 and a.id=7323 group by a.id,c.fabric_description_id ';
	$data_fin_ac=sql_select($sql_fin_ac);
	foreach($data_fin_ac as $row_fin_ac){
		$fin_ac['actual_start_date'][$row_fin_ac['febric_description_id']][$row_fin_ac['po_number_id']]=$row_fin_ac['max_receive_date'];
		$fin_ac['actual_finish_date'][$row_fin_ac['febric_description_id']][$row_fin_ac['po_number_id']]=$row_fin_ac['min_receive_date'];
		$fin_ac['quantity'][$row_fin_ac['febric_description_id']][$row_fin_ac['po_number_id']]=$row_fin_ac['quantity'];
	}
	
	    $pp=array();
		$sql_pp=sql_select( 'select a.id AS "id", c.color_number_id AS "color_number_id", max(c.approval_status_date) AS "approval_status_date" from wo_po_break_down a , wo_po_color_size_breakdown b, wo_po_sample_approval_info c where a.id=b.po_break_down_id and a.id=c.po_break_down_id and b.color_number_id=c.color_number_id and b.po_break_down_id=8093 and c.sample_type_id=7 and approval_status=3 and  b.status_active=1 and b.is_deleted=0 and (c.entry_form_id is null or c.entry_form_id=0) group by a.id,c.color_number_id');
		foreach($sql_pp as $row_pp){
			$pp[$row_pp['id']][$row_pp['color_number_id']]=$row_pp['max_production_date'];
		}
		
	
		$cutting=array();
		$sql_cutting=sql_select( 'select a.id AS "id", c.color_number_id AS "color_number_id", max(production_date) as "max_production_date",min(production_date) as "min_production_date" from wo_po_break_down a,  pro_garments_production_mst b, wo_po_color_size_breakdown c, pro_garments_production_dtls d where a.id=b.po_break_down_id and a.id=c.po_break_down_id and b.id=d.mst_id and c.id=d.color_size_break_down_id and b.po_break_down_id=7323 and b.production_type=1 and  b.status_active=1 and b.is_deleted=0 group by a.id,c.color_number_id');
		foreach($sql_cutting as $row_cutting){
			$cutting['max_production_date'][$row_cutting['id']][$row_cutting['color_number_id']]=$row_cutting['max_production_date'];
			$cutting['min_production_date'][$row_cutting['id']][$row_cutting['color_number_id']]=$row_cutting['min_production_date'];
		}
		$batcharr=array();
		$sql_batch=sql_select('select a.color_id AS "color_id",b.po_id AS "po_id", c.detarmination_id AS "detarmination_id", sum(b.batch_qnty) AS "batch_qnty" from pro_batch_create_mst a,pro_batch_create_dtls b,product_details_master c, wo_po_break_down e where a.id=b.mst_id and b.prod_id=c.id and b.po_id=e.id group by a.color_id,b.po_id,c.detarmination_id');
		foreach($sql_batch as $row_batch){
			$batcharr[$row_batch['po_id']][$row_batch['detarmination_id']][$row_batch['color_id']]=$row_batch['batch_qnty'];
		}
		$batcharr=array();
		$sql_batch=sql_select('select a.color_id AS "color_id",b.po_id AS "po_id", c.detarmination_id AS "detarmination_id", sum(b.batch_qnty) AS "batch_qnty" from pro_batch_create_mst a,pro_batch_create_dtls b,product_details_master c, wo_po_break_down e where a.id=b.mst_id and b.prod_id=c.id and b.po_id=e.id and a.batch_against<>2 and a.entry_form=0 group by a.color_id,b.po_id,c.detarmination_id');
		foreach($sql_batch as $row_batch){
			$batcharr[$row_batch['po_id']][$row_batch['detarmination_id']][$row_batch['color_id']]=$row_batch['batch_qnty'];
		}
		
		$dye_qnty_arr=array();
		$sql_dye='select b.po_id, a.color_id,e.detarmination_id AS "detarmination_id", sum(b.batch_qnty) as dye_qnty from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c,product_details_master e,wo_po_break_down f where a.id=b.mst_id and a.id=c.batch_id and b.prod_id=e.id and b.po_id=f.id and c.load_unload_id=2 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.po_id, a.color_id,e.detarmination_id';
		$resultDye=sql_select($sql_dye);
		foreach($resultDye as $dyeRow)
		{
			$dye_qnty_arr[$dyeRow[csf('po_id')]][$dyeRow[csf('detarmination_id')]][$dyeRow[csf('color_id')]]=$dyeRow[csf('dye_qnty')];
		}
	$check_color=array();
	$i=1;
	foreach($data as $row){
		$color=1;
		foreach($contrast_color[$row['lib_yarn_count_deter_id']][$row['color_number_id']] as $contrast_color_id){
	?>
        <tr>
            <td width="30"><? echo $i; ?></td>
           	<td width="50"><? echo date("d-m-Y",strtotime($row['po_received_date'])); ?></td>
            <td width="50"></td>
            <td width="50"><? echo $buyer_library[$row['buyer_name']]; ?></td>
            <td width="50"><? echo $row['po_number']; ?></td>
            <td width="50"><? echo $row['job_no']; ?></td>
            <td width="60"><? echo $row['style_ref_no']; ?></td> 
            <?
			//if($color==1){
			?>
            <td width="100"  rowspan="<? //echo count($contrast_color[$row['lib_yarn_count_deter_id']][$row['color_number_id']]); ?>">
			<? 
			echo $yarn_color_library[$row['color_number_id']]; 
			?>
            </td>
            <td width="100" rowspan="<? //echo count($contrast_color[$row['lib_yarn_count_deter_id']][$row['color_number_id']]); ?>">
			<? 
			if($check_color[$row['id']][$row['color_number_id']]==''){
			echo $color_qty_array[$row['id']][$row['color_number_id']];
			$check_color[$row['id']][$row['color_number_id']]=$row['color_number_id'];
			}
			//$check_color[$row['id']][$row['color_number_id']]=$row['color_number_id'];
			?>
            </td>
            <?
			//}
			?>
            
            
            <td width="130" title="<? echo $row['pre_cost_dtls_id']; ?>"><? echo $row['fabric_description']; ?> </td> 
            <td width="30"><? echo $row['gsm_weight']; ?> </td>
            <td width="100"><? echo $yarn_color_library[$contrast_color_id]; ?></td>
            <td width="80"><? echo date("d-m-Y",strtotime($row['shipment_date'])); ?></td>
            <td width="90" align="right">
            <?
			echo $fabric_costing_arr['knit']['finish'][$row['lib_yarn_count_deter_id']][$row['color_number_id']]/$color_qty_array[$row['id']][$row['color_number_id']]*12;
			?>
            </td>
            <td width="100">
			<? 
			$KnitTODStart=date("d-m-Y",strtotime($tnaarray['task_start_date'][60][$row['id']]));
			if($KnitTODStart !='01-01-1970'){
			echo $KnitTODStart;
			}
			?>
            </td>
            <td width="100">
			<? 
			$KnitTODEnd=date("d-m-Y",strtotime($tnaarray['task_finish_date'][60][$row['id']])); 
			if($KnitTODEnd !='01-01-1970'){
			echo $KnitTODEnd; 
			}
			?>
            </td>
            <td width="100">
			<? 
			$FabricDelStart=date("d-m-Y",strtotime($tnaarray['task_start_date'][73][$row['id']])); 
			if($FabricDelStart !='01-01-1970'){
			echo $FabricDelStart; 
			} 
			?>
            </td> 
            <td width="100">
			<? 
			$FabricDelEnd = date("d-m-Y",strtotime($tnaarray['task_finish_date'][73][$row['id']])); 
			if($FabricDelEnd !='01-01-1970'){
			echo $FabricDelEnd; 
			} 
			?>
            </td>
            <td width="80">
			<? 
			$ActualKnitStart=date("d-m-Y",strtotime($kint_ac['actual_start_date'][$row['lib_yarn_count_deter_id']][$row['id']]));
			if($ActualKnitStart !='01-01-1970'){
			echo $ActualKnitStart; 
			} 
			?>
            </td>
            <td width="80">
			<?
			$ActualKnitEnddate = date("d-m-Y",strtotime($kint_ac['actual_finish_date'][$row['lib_yarn_count_deter_id']][$row['id']])); 
			if($ActualKnitEnddate !='01-01-1970')
			{
				echo $ActualKnitEnddate;
			}			
			?>
            </td>
            <td width="100">
			<?
			$ActualFabricDelStartDate = date("d-m-Y",strtotime($fin_ac['actual_start_date'][$row['lib_yarn_count_deter_id']][$row['id']])); 
			if($ActualFabricDelStartDate !='01-01-1970')
			{
				echo $ActualFabricDelStartDate;
			}	 
			?>
            </td>
            <td width="100">
			<? 
			$ActualFabricDelEndDate =  date("d-m-Y",strtotime($fin_ac['actual_finish_date'][$row['lib_yarn_count_deter_id']][$row['id']])); 
			if($ActualFabricDelEndDate !='01-01-1970')
			{
				echo $ActualFabricDelEndDate;
			}	
			?>
            </td>
            <td width="90">
			<? 
			$CuttingStartDate =  date("d-m-Y",strtotime($tnaarray['task_start_date'][84][$row['id']])); 
			if($CuttingStartDate !='01-01-1970')
			{
				echo $CuttingStartDate;
			}	 
			?>
            </td>
            <td width="90">
			<?
			$CuttingEndDate =  date("d-m-Y",strtotime($tnaarray['task_finish_date'][84][$row['id']])); 
			if($CuttingEndDate !='01-01-1970')
			{
				echo $CuttingEndDate;
			}	
			?>
            </td>
            <td width="100">
			<? 
			$ActualCuttingStartDate = date("d-m-Y",strtotime($cutting['max_production_date'][$row['id']][$row['color_number_id']]));
			if($ActualCuttingStartDate !='01-01-1970')
			{
				echo $ActualCuttingStartDate;
			}	
			?>
            </td>
            <td width="80">
			<? 
			$ActualCuttingEndDate = date("d-m-Y",strtotime($cutting['min_production_date'][$row['id']][$row['color_number_id']])) ;
			if($ActualCuttingEndDate !='01-01-1970')
			{
				echo $ActualCuttingEndDate;
			}
			?>
            </td>                                                         
            <td width="80"><? echo $pp[$row['id']][$row['color_number_id']]; ?></td>
            <td width="80">PCD Date</td>
            <td width="100">
			<? 
			echo $fabric_costing_arr['knit']['grey'][$row['lib_yarn_count_deter_id']][$row['color_number_id']]; 
			
			?>
            </td>
            <td width="100">
			<?
			echo $fabric_costing_arr['knit']['finish'][$row['lib_yarn_count_deter_id']][$row['color_number_id']]; 
			?>
            </td>
            <td width="80"><?   echo $kint_ac['quantity'][$row['lib_yarn_count_deter_id']][$row['id']]; ?></td>
            <td width="80">
            <?
			echo $fabric_costing_arr['knit']['grey'][$row['pre_cost_dtls_id']][$row['color_number_id']]-$kint_ac['quantity'][$row['lib_yarn_count_deter_id']][$row['id']];
			?>
            </td>
            <td width="80"><? echo $batcharr[$row['id']][$row['lib_yarn_count_deter_id']][$row[$contrast_color_id]] ?></td>
            <td width="80"><? echo $dye_qnty_arr[$row[csf('id')]][$row[csf('lib_yarn_count_deter_id')]][$row[$contrast_color_id]]; ?></td>
            <td width="80"><? echo $batcharr[$row['id']][$row['lib_yarn_count_deter_id']][$row[$contrast_color_id]]-$dye_qnty_arr[$row[csf('id')]][$row[csf('lib_yarn_count_deter_id')]][$row[$contrast_color_id]]?></td>
            <td width="80"><!--Dyeing-Finshing qty--></td>
            <td width="80"><!--Dyeing-Finshing Bal--></td>
            <td width="60"><? echo  $fin_ac['quantity'][$row['lib_yarn_count_deter_id']][$row['id']]; ?></td>
            <td width="60">
           <? 
		   	echo $fabric_costing_arr['knit']['finish'][$row['pre_cost_dtls_id']][$row['color_number_id']]-$fin_ac['quantity'][$row['lib_yarn_count_deter_id']][$row['id']]; 
			?>
            </td>
            <td width="60"><? echo date("Y",strtotime($row['shipment_date'])); ?></td>
        </tr>
        
        <?
		$color++;
        $i++;
		}
	}
?>
    </tbody>
</table>

</body>
</html>