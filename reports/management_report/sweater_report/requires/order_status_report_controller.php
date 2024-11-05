<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
//  print_r($action);exit;
//--------------------------------------------------------------------------------------------

$userbrand_idCond = ""; $filterBrandId = "";
if ($userbrand_id !='' && $single_user_id==1) {
    $userbrand_idCond = "and id in ($userbrand_id)";
	$filterBrandId=$userbrand_id;
}
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,20,21,22,23,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/order_status_report_controller', this.value, 'load_drop_down_brand', 'brand_td');");
	exit();
}

if ($action=="load_drop_down_brand")
{
	echo create_drop_down( "cbo_brand_name", 70, "select id, brand_name from lib_buyer_brand where buyer_id in($data) and status_active =1 and is_deleted=0 $userbrand_idCond order by brand_name ASC","id,brand_name", 1, "--Select--", "", "" );
	exit();
}
 

if($action=="report_generate")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_working_company_name=str_replace("'","",$cbo_working_company_name);
	$txt_job_id=str_replace("'","",$txt_job_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer);
	$cbo_brand_name=str_replace("'","",$cbo_brand_name);
	$txt_job=str_replace("'","",$txt_job);
	$txt_job_id=str_replace("'","",$txt_job_id);
	$txt_style=str_replace("'","",$txt_style);
	$cbo_gauge=str_replace("'","",$cbo_gauge);
	$cbo_date_type=str_replace("'","",$cbo_date_type);
	
	

	$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_name_arr=return_library_array( "select id,company_name from lib_company",'id','company_name');
	//$image_arr_arr=return_library_array( "select MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='knit_order_entry' and FILE_TYPE=1",'MASTER_TBLE_ID','IMAGE_LOCATION');

	
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			
		}
		else if($db_type==2)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			
		}
		if($cbo_date_type==1){
			$date_cond=" and b.SHIPMENT_DATE between '$start_date' and '$end_date'";
		}
		else if($cbo_date_type==2){
			$date_cond=" and b.PO_RECEIVED_DATE between '$start_date' and '$end_date'";
		}
	}

	if($txt_job_id!=''){
		$whereCon.="and a.id in($txt_job_id)";
	}
	if($txt_job!=''){
		$whereCon.="and a.JOB_NO_PREFIX_NUM in($txt_job)";
	}
	if($txt_style!=''){
		$whereCon.="and a.STYLE_REF_NO in('".str_replace(",","','",$txt_style)."')";
	}
	
	if($cbo_buyer!=0){
		$whereCon.="and a.BUYER_NAME in($cbo_buyer)";
	}
	if($cbo_gauge!=''){
		$whereCon.="and a.GAUGE in($cbo_gauge)";
	}
	
	if($cbo_working_company_name!=''){
		$workingCompanyCon="and d.DELIVERY_COMPANY_ID in($cbo_working_company_name)";
	}
	$brand_cond="";
	if($cbo_brand_name==0)
	{
		if($filterBrandId!="") $brand_cond="and a.brand_id in ($filterBrandId)"; else $brand_cond="";
	}
	else $brand_cond="and a.brand_id in($cbo_brand_name) ";

	// echo $whereCon;
	
	$width=240;
	
		
		if($reportType==4)
		{
			//$shipCon=" and b.SHIPING_STATUS=3";
		}
		else if($reportType==5)
		{
			$shipCon=" and b.SHIPING_STATUS != 3";
		}
		
		if($reportType==3)
		{
			$table=",pro_garments_production_mst c";
			$tableCon=" and c.po_break_down_id=b.id and c.garments_nature=100 and c.production_type=52  and c.status_active=1";
		}
		
		
	
		if($reportType==1){$confirmCon =" and b.IS_CONFIRMED=1";}
		else if($reportType==2){$confirmCon =" and b.IS_CONFIRMED=2";}
		else if($reportType==5){$confirmCon =" and b.IS_CONFIRMED=1";}
		else{$confirmCon ="";}
		
		$con = connect();
		execute_query("delete from tmp_poid where userid=".$user_id."");
		
		$sql= "SELECT count(b.id) as TOTAL_PO, a.FABRIC_COMPOSITION,a.COMPANY_NAME,a.JOB_NO,a.ID as JOB_ID,a.STYLE_REF_NO,a.BUYER_NAME,a.GAUGE,sum((b.PO_QUANTITY*a.TOTAL_SET_QNTY)) as PO_QUANTITY_PCS,MIN(b.SHIPMENT_DATE) as SHIPMENT_DATE,min(b.IS_CONFIRMED) as IS_CONFIRMED,a.AVG_UNIT_PRICE 
		from wo_po_details_master a,wo_po_break_down b $table 
		where a.job_no=b.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1
		$date_cond $whereCon $shipCon $tableCon $brand_cond and a.COMPANY_NAME in($cbo_company_name) $confirmCon
		
		group by a.FABRIC_COMPOSITION,a.COMPANY_NAME,a.BUYER_NAME,a.JOB_NO,a.ID,a.AVG_UNIT_PRICE,a.STYLE_REF_NO,a.GAUGE
		order by a.FABRIC_COMPOSITION,a.COMPANY_NAME,a.BUYER_NAME,MIN(b.SHIPMENT_DATE)";//c.job_no
		
		// echo $sql;
		$sql_result=sql_select($sql);
		$orderDataArr=array();$buyerWiseJobQty=array();$jobWisebuyerArr=array();$jobPoArr=array();
		foreach($sql_result as $row)
		{
			$orderDataArr[$row[BUYER_NAME]][$row[JOB_NO]]=$row;
			$buyerWiseJobQty[$row[BUYER_NAME]]+=$row[PO_QUANTITY_PCS];
			$jobWisebuyerArr[$row[JOB_NO]]+=$row[BUYER_NAME];
			$jobPoArr[$row[JOB_NO]]=$row[TOTAL_PO];
			$jobIdArr[$row[JOB_ID]]=$row[JOB_ID];

			$r_id2=execute_query("insert into tmp_poid (userid, pono,type) values (".$user_id.",'".$row[JOB_NO]."',1".")");
		
		}
		
		
		
		//TNA----------------------
		$tnsSql="select a.JOB_NO,MIN(a.TASK_START_DATE) as TASK_START_DATE from TNA_PROCESS_MST a,tmp_poid b where a.TASK_NUMBER=47 and a.JOB_NO=b.pono and b.TYPE=1 AND b.userid=$user_id group by a.JOB_NO";	
		$tna_data_arr=return_library_array($tnsSql,'JOB_NO','TASK_START_DATE');
		
		
		//Exfactory--------------------------
		$exfactorySql="select a.JOB_NO_MST,d.BUYER_ID,a.SHIPING_STATUS,b.id as PO_ID, sum((CASE WHEN b.entry_form !=85 THEN b.ex_factory_qnty ELSE 0 END)-(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END)) as exfactory_qnty from wo_po_break_down a,pro_ex_factory_mst b,tmp_poid c,pro_ex_factory_delivery_mst d where d.id=b.DELIVERY_MST_ID $workingCompanyCon and a.id=b.po_break_down_id and a.job_no_mst=c.pono and c.type=1 AND c.userid=$user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.SHIPING_STATUS,d.BUYER_ID,a.job_no_mst,b.id";

		
		$exfactorySqlRes=sql_select($exfactorySql);
		$ex_factory_qty_arr=array();
		$fullExfactoryJobPoArr=array();
		//$jobPoArr=array();
		foreach($exfactorySqlRes as $row)
		{
			$ex_factory_qty_arr[$row[csf('job_no_mst')]]+=$row[csf('exfactory_qnty')];
			//$jobPoArr[$row[JOB_NO_MST]][$row[PO_ID]]=1;
			
			if($row[SHIPING_STATUS]==3){
				$fullExBuyerJob[$row[BUYER_ID]][$row[JOB_NO_MST]]=$row[JOB_NO_MST];
				$fullExfactoryJobPoArr[$row[JOB_NO_MST]][$row[PO_ID]]=1;
			}
		}
		
		  //print_r($orderDataArr);
		 //print_r($fullExfactoryJobPoArr);
		  
		  
		
		if($reportType==4){
			$newDataArr=array();
			$newBuyerWiseJobQty=array();
			foreach($fullExBuyerJob as $buyer =>$jobArr){
				foreach($jobArr as $JOB =>$VAL){
					if(($jobPoArr[$JOB] == count($fullExfactoryJobPoArr[$JOB])) && count($fullExfactoryJobPoArr[$JOB])>0 ){
						$newDataArr[$buyer][$JOB]=$orderDataArr[$buyer][$JOB];
						$newBuyerWiseJobQty[$buyer]=$buyerWiseJobQty[$buyer];
					}
				}
			}
			$orderDataArr=array();
			$buyerWiseJobQty=array();
			$orderDataArr=$newDataArr;
			$buyerWiseJobQty=$newBuyerWiseJobQty;
		}
		
		//print_r($newBuyerWiseJobQty);
		//wo_pre_cost_dtls
		 $job_id_cond_for_in=where_con_using_array($jobIdArr,0,"a.job_id"); 
		
			 $pre_Sql="select a.JOB_NO,a.MARGIN_DZN   from wo_pre_cost_dtls a where   a.status_active=1 $job_id_cond_for_in ";
			$pre_Result=sql_select($pre_Sql);
			foreach($pre_Result as $row)
			{
				$cm_marginArr[$row[JOB_NO]]=$row[MARGIN_DZN];
			}
			
		
		//kniting close--------------------------
		$closingSql="select a.REF_CLOSING_STATUS,B.JOB_NO_MST,A.PRODUCTION_QUANTITY,A.PO_BREAK_DOWN_ID from pro_garments_production_mst a,wo_po_break_down b,tmp_poid tmp where a.po_break_down_id=b.id and a.garments_nature=100  and a.status_active=1  AND tmp.userid=$user_id and b.job_no_mst=tmp.pono and tmp.type=1  and a.production_type=52";	 // and   a.production_type=52	
		$closingSqlRes=sql_select($closingSql);
		$closingDQtyArr=array();$closingDQtyArr=array();$knitingQtyArr=array();
		foreach($closingSqlRes as $row)
		{
			$knitingQtyArr[$row[JOB_NO_MST]]+=$row[PRODUCTION_QUANTITY];
			if($row[REF_CLOSING_STATUS]==1){
				$closingDQtyArr[$row[JOB_NO_MST]]+=$row[PRODUCTION_QUANTITY];
			}
		}
		
		if($reportType==3){
			$newDataArr=array();
			$newBuyerWiseJobQty=array();
			foreach($closingDQtyArr as $JOB=>$qty){
				$buyer=$jobWisebuyerArr[$JOB];
				$newDataArr[$buyer][$JOB]=$orderDataArr[$buyer][$JOB];
				$newBuyerWiseJobQty[$buyer]=$buyerWiseJobQty[$buyer];
			}
			$orderDataArr=array();
			$buyerWiseJobQty=array();
			$orderDataArr=$newDataArr;
			$buyerWiseJobQty=$newBuyerWiseJobQty;
		}
		
		
		
		$image_arr=return_library_array( "select a.MASTER_TBLE_ID,a.IMAGE_LOCATION from COMMON_PHOTO_LIBRARY a,tmp_poid tmp where a.FORM_NAME='knit_order_entry' and a.MASTER_TBLE_ID=tmp.pono and  tmp.userid=".$user_id." and a.FILE_TYPE=1",'MASTER_TBLE_ID','IMAGE_LOCATION');

		$composition_arr=return_library_array( "select a.job_no,a.fabric_description from wo_pre_cost_fabric_cost_dtls a,tmp_poid tmp where a.job_no=tmp.pono and  tmp.userid=".$user_id." and a.fabric_source in(1,2) and a.status_active=1 and a.is_deleted=0",'job_no','fabric_description');
		
		
		//print_r($image_arr);
		
		ob_start();
		?>
        <div align="center">
        <fieldset>
      
              
		<table align="left">
			<tr>
				<td><div style="float:left;"><span style="background:#FFF; padding:0 8px; width:1px; "></span> Confirm&nbsp;</div></td>
				<td><div style="float:left; "><span style="background:#B7DEE8; padding:0 8px; width:8px; "></span>  Projected&nbsp;</div></td>
				<td><div style="float:left; "><span style="background:#FCD5B4; padding:0 8px; width:8px; "></span>  Knit Comp&nbsp;</div></td>
				<td><div style="float:left; "><span style="background:#00FF00; padding:0 8px; width:8px; "></span>  Full Shipped&nbsp;</div></td>
				<td><div style="float:left; "><span style="background:#FBFFCA; padding:0 8px; width:8px; "></span>  Ship Pending&nbsp;</div></td>
			</tr>
			<tr>
				<td colspan="5"><b style="float:left;">All Buyer Total: <?= array_sum($buyerWiseJobQty);?></b></td>
			</tr>
		</table>		  
			  
			  
			  <?php 
			  foreach($orderDataArr as $buyer_id=>$buyerDataArr){ ?>
                <table width="100%" class="rpt_table" border="1" rules="all">
                    <thead>
                        <th colspan="10"> 
                            <b>Buyer Name:</b><?= $buyer_name_arr[$buyer_id];?>,
                            <b>Buyer Total:</b><?= $buyerWiseJobQty[$buyer_id] ;?>
                        </th>
                    <thead>
                </table>
                <table align="left"><tr>              
			  <?
			  $flag=0;
			  foreach($buyerDataArr as $JOB_NO=>$rows){
				 $bgcolor="#B1E6FC";
				 if($closingDQtyArr[$JOB_NO]){$bgcolor="#FCD5B4";}
				 else if($rows[IS_CONFIRMED]==2){$bgcolor="#B7DEE8";}
				 else if($rows[IS_CONFIRMED]==1){$bgcolor="#FFF";}
				 
				 if($jobPoArr[$JOB_NO] == count($fullExfactoryJobPoArr[$JOB_NO]) && count($fullExfactoryJobPoArr[$JOB_NO])>0 ){$bgcolor="#00FF00";}
				 
				 if($reportType==5){$bgcolor="#FBFFCA";}
				 else if($reportType==4){$bgcolor="#00FF00";}
				 else if($reportType==3){$bgcolor="#FCD5B4";}
				 else if($reportType==2){$bgcolor="#B7DEE8";}
				 else if($reportType==1){$bgcolor="#FFF";}
				
			  
			  
			  if($flag==5){echo "</tr><tr><td>";$flag=0;}
			  else{echo "<td>";}
			  ?> 
                <div style="float:left; width:<?= $width+4;?>px; margin:5px;;">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width;?>" class="rpt_table" bgcolor="<?= $bgcolor;?>">
                    <tr>
                        <td colspan="2" align="center">
                        <img src="../../../<?= $image_arr[$rows[JOB_NO]]; ?>" height="200" align="11">
                        </td>
                    </tr>

                    <tr bgcolor="#FFF">
                        <td width="85"><strong>Buyer</strong></td><td><?= $buyer_name_arr[$rows[BUYER_NAME]]; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Style</strong></td><td><?= $rows[STYLE_REF_NO]; ?></td>
                    </tr>
                    <tr bgcolor="#FFF">
                        <td><strong>Job No</strong></td><td><?= $rows[JOB_NO]; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Fab. Composition</strong></td><td><?= $composition_arr[$rows[JOB_NO]]; ?></td>
                    </tr>
                    <tr bgcolor="#FFF">
                        <td><strong>Gauge</strong></td><td><?= $gauge_arr[$rows[GAUGE]]; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Job Quantity</strong></td><td><?= $rows[PO_QUANTITY_PCS]; ?></td>
                    </tr>
                     <tr>
                        <td><strong>FOB/PCS</strong></td><td><?= number_format($rows[AVG_UNIT_PRICE],2); ?></td>
                    </tr>
                     <tr>
                        <td><strong>CM/Dzn</strong></td><td><?= number_format($cm_marginArr[$rows[JOB_NO]],2); ?></td>
                    </tr>
                    
                    <tr bgcolor="#FFF">
                        <td><strong>Yarn in House</strong></td><td><?= change_date_format($tna_data_arr[$rows[JOB_NO]]); ?></td>
                    </tr>
                    
                    <tr>
                        <td><strong>Knitted Qty.</strong></td><td><?= $knitingQtyArr[$rows[JOB_NO]]; ?></td>
                    </tr>
                    
                    <tr bgcolor="#FFF">
                        <td><strong>Shipment :</strong></td><td><?= change_date_format($rows[SHIPMENT_DATE]); ?></td>
                    </tr>
                    
                    <tr>
                        <td><strong>Shipped Qty.</strong></td><td><?= $ex_factory_qty_arr[$rows[JOB_NO]]; ?></td>
                    </tr>
                    
                    <tr bgcolor="#FFF">
                        <td><strong>Shipped %</strong></td><td><?= number_format(($ex_factory_qty_arr[$rows[JOB_NO]]/$rows[PO_QUANTITY_PCS])*100,2); ?></td>
                    </tr>
                </table>
                </div>
                </td>
			<?php
            	$flag++;
			  }
			  	
				echo "</tr></table>";
			  }
			  ?>
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
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "**$filename**$rpt_type";
	disconnect($con);
	exit();
}

if($action=="report_generate_2") //md mamun -crm-3375-14-02-2023
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_working_company_name=str_replace("'","",$cbo_working_company_name);
	$txt_job_id=str_replace("'","",$txt_job_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer);
	$txt_job=str_replace("'","",$txt_job);
	$txt_job_id=str_replace("'","",$txt_job_id);
	$txt_style=str_replace("'","",$txt_style);
	$cbo_gauge=str_replace("'","",$cbo_gauge);
	$cbo_date_type=str_replace("'","",$cbo_date_type);
	
	

	$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_name_arr=return_library_array( "select id,company_name from lib_company",'id','company_name');
	$sample_library=return_library_array( "select id,sample_name from lib_sample", "id", "sample_name"  );
	$lib_size=return_library_array( "select size_name,id from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");	 
	$season_name_arr=return_library_array( "select id,season_name from   lib_buyer_season  where status_active=1 and is_deleted=0",'id','season_name');
	$brand_name_arr=return_library_array( "select id,brand_name from   lib_buyer_brand  where status_active=1 and is_deleted=0",'id','brand_name');
	$lib_item_group_arr=return_library_array( "select item_name,id from lib_item_group where item_category=4 order by item_name", "id", "item_name");

	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			
		}
		else if($db_type==2)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			
		}
		if($cbo_date_type==1){
			$date_cond=" and b.SHIPMENT_DATE between '$start_date' and '$end_date'";
		}
		else if($cbo_date_type==2){
			$date_cond=" and b.PO_RECEIVED_DATE between '$start_date' and '$end_date'";
		}
	}

	if($txt_job_id!=''){
		$whereCon.="and a.id in($txt_job_id)";
	}
	if($txt_job!=''){
		$whereCon.="and a.JOB_NO_PREFIX_NUM in($txt_job)";
	}
	if($txt_style!=''){
		$whereCon.="and a.STYLE_REF_NO in('".str_replace(",","','",$txt_style)."')";
	}
	
	if($cbo_buyer!=0){
		$whereCon.="and a.BUYER_NAME in($cbo_buyer)";
	}
	if($cbo_gauge!=''){
		$whereCon.="and a.GAUGE in($cbo_gauge)";
	}
	
	if($cbo_working_company_name!=''){
		$workingCompanyCon="and d.DELIVERY_COMPANY_ID in($cbo_working_company_name)";
	}

	// echo $whereCon;
	
	$width=240;
	
		
		if($reportType==4)
		{
			//$shipCon=" and b.SHIPING_STATUS=3";
		}
		else if($reportType==5)
		{
			$shipCon=" and b.SHIPING_STATUS != 3";
		}
		
		if($reportType==3)
		{
			$table=",pro_garments_production_mst c";
			$tableCon=" and c.po_break_down_id=b.id and c.garments_nature=100 and c.production_type=52  and c.status_active=1";
		}
		
		
	
		if($reportType==1){$confirmCon =" and b.IS_CONFIRMED=1";}
		else if($reportType==2){$confirmCon =" and b.IS_CONFIRMED=2";}
		else if($reportType==5){$confirmCon =" and b.IS_CONFIRMED=1";}
		else{$confirmCon ="";}
		
		$con = connect();
		execute_query("delete from tmp_poid where userid=".$user_id."");
		
		$sql= "SELECT b.id as PO_ID,b.PO_NUMBER, a.FABRIC_COMPOSITION,a.COMPANY_NAME,a.JOB_NO,a.ID as JOB_ID,a.STYLE_REF_NO,a.BUYER_NAME,a.GAUGE,sum((b.PO_QUANTITY*a.TOTAL_SET_QNTY)) as PO_QUANTITY_PCS,MIN(b.SHIPMENT_DATE) as SHIPMENT_DATE,min(b.IS_CONFIRMED) as IS_CONFIRMED,a.AVG_UNIT_PRICE ,a.STYLE_DESCRIPTION,a.BRAND_ID,a.YARN_QUALITY,b.UNIT_PRICE,a.SHIP_MODE,a.STYLE_OWNER,a.SEASON_BUYER_WISE
		from wo_po_details_master a,wo_po_break_down b $table 
		where a.job_no=b.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1
		$date_cond $whereCon $shipCon $tableCon and a.COMPANY_NAME in($cbo_company_name) $confirmCon
		
		group by a.FABRIC_COMPOSITION,a.COMPANY_NAME,a.BUYER_NAME,a.JOB_NO,a.ID,a.AVG_UNIT_PRICE,a.STYLE_REF_NO,a.GAUGE,b.id,b.PO_NUMBER,a.STYLE_DESCRIPTION,a.BRAND_ID,a.YARN_QUALITY,b.UNIT_PRICE,a.SHIP_MODE,a.STYLE_OWNER,a.SEASON_BUYER_WISE
		order by a.FABRIC_COMPOSITION,a.COMPANY_NAME,a.BUYER_NAME,MIN(b.SHIPMENT_DATE)";//c.job_no
		
		// echo $sql;
		$sql_result=sql_select($sql);
		$orderDataArr=array();$buyerWiseJobQty=array();$jobWisebuyerArr=array();$jobPoArr=array();
		foreach($sql_result as $row)
		{
			// $orderDataArr[$row[STYLE_REF_NO]][$row[JOB_NO]]=$row;
			$buyerWiseJobQty[$row[STYLE_REF_NO]]+=$row[PO_QUANTITY_PCS];
			$jobWisebuyerArr[$row[JOB_NO]]+=$row[BUYER_NAME];
			$jobWiseStyleArr[$row[JOB_NO]]=$row[STYLE_REF_NO];
			$jobPoArr[$row[STYLE_REF_NO]]=$row[TOTAL_PO];
			$jobIdArr[$row[JOB_ID]]=$row[JOB_ID];
			$poIdArr[$row[PO_ID]]=$row[PO_ID];
			$orderDataArr[$row[STYLE_REF_NO]][$row[PO_ID]]=$row;

			$r_id2=execute_query("insert into tmp_poid (userid, pono,type) values (".$user_id.",'".$row[JOB_NO]."',1".")");
		
		}
		$jobIds=implode(",",$jobIdArr);
		$poIds=implode(",",$poIdArr);
		//-----------------------------------------------------------------------------------
		$pre_cost_array=sql_select("select id, remark, job_no, trim_group, description, brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, cons_breack_down, country, calculatorstring, seq, status_active from wo_pre_cost_trim_cost_dtls where job_id in ($jobIds) order by seq");

			foreach($pre_cost_array as $row){

				$job_wise_data[$row[csf('job_no')]]['accessories'] .=$lib_item_group_arr[$row[csf('trim_group')]].",";
			}

		//--------------------------------sample po wise-------------------------------------
		$sample_data_array=sql_select("Select id,submitted_to_buyer,approval_status,approval_status_date,job_no_mst,po_break_down_id,sample_type_id
		from wo_po_sample_approval_info where     po_break_down_id in ($poIds) and status_active=1");

			foreach($sample_data_array as $row){

					$job_po_wise_data[$row[csf('job_no_mst')]][$row[csf('po_break_down_id')]][$row[csf('sample_type_id')]]['submitted_date']=$row[csf('submitted_to_buyer')];
				if($row[csf('approval_status')]==3){
					$job_po_wise_data[$row[csf('job_no_mst')]][$row[csf('po_break_down_id')]][$row[csf('sample_type_id')]]['approval_date']=$row[csf('approval_status_date')];
				}
			}
		
		
		
		$image_arr=return_library_array( "select a.MASTER_TBLE_ID,a.IMAGE_LOCATION from COMMON_PHOTO_LIBRARY a,tmp_poid tmp where a.FORM_NAME='knit_order_entry' and a.MASTER_TBLE_ID=tmp.pono and  tmp.userid=".$user_id." and a.FILE_TYPE=1",'MASTER_TBLE_ID','IMAGE_LOCATION');

		
		
		
		//print_r($image_arr);
		
		ob_start();
		?>
        <div align="center">
        <fieldset>
      
              
		<table align="left">
			<tr>
				<td><div style="float:left;"><span style="background:#FFF; padding:0 8px; width:1px; "></span> Confirm&nbsp;</div></td>
				<td><div style="float:left; "><span style="background:#B7DEE8; padding:0 8px; width:8px; "></span>  Projected&nbsp;</div></td>
				<td><div style="float:left; "><span style="background:#FCD5B4; padding:0 8px; width:8px; "></span>  Knit Comp&nbsp;</div></td>
				<td><div style="float:left; "><span style="background:#00FF00; padding:0 8px; width:8px; "></span>  Full Shipped&nbsp;</div></td>
				<td><div style="float:left; "><span style="background:#FBFFCA; padding:0 8px; width:8px; "></span>  Ship Pending&nbsp;</div></td>
			</tr>
			<tr>
				<td colspan="5"><b style="float:left;">All Style Total: <?= array_sum($buyerWiseJobQty);?></b></td>
			</tr>
		</table>		  
			  
			  
			  <?php 
				//-----------------------------------------------------------------------
				$short_quatation_data=sql_select("select  b.mst_id, b.tot_cons, b.rate,a.style_ref ,a.company_id	from qc_mst a ,qc_cons_rate_dtls b where a.qc_no=b.mst_id   and a.status_active=1 and b.is_deleted=0 and b.type!=4 and a.company_id in($cbo_company_name)	group by b.mst_id, b.tot_cons, b.rate,a.style_ref ,a.company_id ");
				foreach($short_quatation_data as $row)
				{
					$short_quat_arr[$row[csf("style_ref")]]['yarn_avg_rate']=$row[csf("rate")];
					$short_quat_arr[$row[csf("style_ref")]]['gmts_weight']=$row[csf("tot_cons")];
				}



				$color_size_sql= "select a.po_number,a.id as po_id,b.color_number_id,b.size_number_id,sum(b.order_quantity) as order_quantity from wo_po_break_down a,wo_po_color_size_breakdown b where  b.po_break_down_id=a.id  and a.id in ($poIds)   and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1group by a.po_number,a.id,b.size_number_id,b.color_number_id";

				$result_color=sql_select($color_size_sql);
				$color_size_array=array();
				foreach($result_color as $row)
				{
				$color_size_array[$row[csf("po_id")]][$row[csf("color_number_id")]]=$row[csf("order_quantity")];
				$po_size_qty_array[$row[csf("po_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['po_qty']=$row[csf("order_quantity")];
				$po_array[$row[csf("po_id")]]['po_no']=$row[csf("po_number")];
				$po_size[$row[csf("po_id")]][$row[csf("size_number_id")]]=[$row[csf("size_number_id")]];
				}
				$size_sql= "select b.size_number_id,sum(b.order_quantity) as order_quantity,a.id as po_id from wo_po_break_down a,wo_po_color_size_breakdown b where  b.po_break_down_id=a.id  and a.id in ($poIds)  and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1 group by b.size_number_id,a.id ";

				$result_size=sql_select($size_sql);
				$posize_array=array();
				foreach($result_size as $row)
				{
				$posize_array[$row[csf("po_id")]][$row[csf("size_number_id")]]=$row[csf("size_number_id")];
				}

				$po_rowspan_arr=array();
				foreach($color_size_array as $pokey=>$po_data)
				{
				$po_row_span=0;
				foreach($po_data as $colorkey=>$val)
				{
					$po_row_span++;
				}
				$po_rowspan_arr[$pokey]=$po_row_span;
				}




			  foreach($orderDataArr as $style=>$buyerDataArr){ ?>
                <table width="100%" class="rpt_table" border="1" rules="all">
                    <thead>
                        <th colspan="10"> 
                            <b>Style Name:</b><?= $style;?>,
                            <b>Style Total:</b><?= $buyerWiseJobQty[$style] ;?>
                        </th>
                    <thead>
                </table>
                <table align="left"><tr>              
			  <?
	       
			  $flag=0;
			  foreach($buyerDataArr as $PO_NO=>$rows){
				 $bgcolor="#B1E6FC";
				
				  
			  
			  if($flag==5){echo "</tr><tr><td>";$flag=0;}
			  else{echo "<td>";}


			
			  ?> 
                <div style="float:left; width:<?=255+count($po_size[$PO_NO])*40;?>px; margin:5px;;">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?=255+count($po_size[$PO_NO])*40;?>px" class="rpt_table" bgcolor="<?= $bgcolor;?>">
                    <tr>
                        <td colspan="2" align="center">
                        <img src="../../../<?= $image_arr[$rows[JOB_NO]]; ?>" height="200" align="11">
                        </td>
                    </tr>
					<tr>
                        <td width="130"><strong>Style</strong></td><td><?= $rows[STYLE_REF_NO]; ?></td>
                    </tr>


					<tr bgcolor="#FFF">
                        <td ><strong>Order No</strong></td><td><?= $rows[PO_NUMBER]; ?></td>
                    </tr>
					<tr bgcolor="#FFF">
                        <td ><strong>Session</strong></td><td title="<?=$rows[SEASON_BUYER_WISE];?>"><?=$season_name_arr[$rows[SEASON_BUYER_WISE]]; ?></td>
                    </tr>
                    <tr bgcolor="#FFF">
                        <td ><strong>Buyer</strong></td><td><?= $buyer_name_arr[$rows[BUYER_NAME]]; ?></td>
                    </tr>
					<tr bgcolor="#FFF">
                        <td ><strong>Brand</strong></td><td><?= $brand_name_arr[$rows[BRAND_ID]]; ?></td>
                    </tr>
					<tr bgcolor="#FFF">
                        <td ><strong>Style Description</strong></td><td><?=$rows[STYLE_DESCRIPTION]; ?></td>
                    </tr>
					<tr>
                        <td height="50px;"><strong>Fabrication</strong></td><td><?=$rows[YARN_QUALITY]; ?></td>
                    </tr>
					<tr bgcolor="#FFF">
                        <td><strong>Gauge</strong></td><td><?= $gauge_arr[$rows[GAUGE]]; ?></td>
                    </tr>
					<tr bgcolor="#FFF">
                        <td><strong>Local Yarn Supplier</strong></td><td></td>
                    </tr>
					<tr bgcolor="#FFF">
                        <td><strong>Import Yarn Supplier</strong></td><td></td>
                    </tr>
					<tr bgcolor="#FFF">
                        <td><strong>Gmts Weight</strong></td><td><?= $short_quat_arr[$style]['gmts_weight']; ?> Lbs</td>
                    </tr>

					<tr bgcolor="#FFF">
                        <td><strong>Price/Unit</strong></td><td><?= number_format($rows[UNIT_PRICE],2); ?></td>
                    </tr>
					<tr>
                        <td><strong> Quantity</strong></td><td><?= $rows[PO_QUANTITY_PCS]; ?></td>
                    </tr>
                    <tr bgcolor="#FFF">
                        <td><strong>F. Shipment Date</strong></td><td><?= change_date_format($rows[SHIPMENT_DATE]); ?></td>
                    </tr>
                   
                   
                 
                     <tr>
                        <td><strong>Shipment Mode</strong></td><td><?= $shipment_mode[$rows[SHIP_MODE]]; ?></td>
                    </tr>
                    <tr>
                        <td height="50px;"><strong>Accessories</strong></td><td><?=rtrim($job_wise_data[$rows[JOB_NO]]['accessories'],","); ?></td>
                    </tr>
                    
					<?
					foreach($job_po_wise_data[$rows[JOB_NO]][$PO_NO] as $sample_type_id => $val ){?>
                    <tr bgcolor="#FFF">
                        <td><strong><?=$sample_library[$sample_type_id];?></strong></td><td><?="Sent On-".$val['submitted_date']."; Approved On-".$val['approval_date']; ?></td>
                    </tr>
					<?}?>
                    
                   
                    <tr bgcolor="#FFF">
                        <td><strong>Production Factory</strong></td><td><?=$company_name_arr[$rows[STYLE_OWNER]]; ?></td>
                    </tr>

					<tr bgcolor="#FFF">
                        <td><strong>Color Size Ratio</strong></td>
						<td>
						  <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:<?=20+count($po_size[$PO_NO])*40;?>pxpx;text-align:center; font-family:Arial Narrow" rules="all">
							<label><b>SIZE</b></label>
								<tr style="font-weight:bold">
									
									<td width="60">Color</td>
									<?
									foreach ($posize_array[$PO_NO] as $sizid)
									{
										?>
											<th width="60"><strong><? echo  $lib_size[$sizid];  ?></strong></th>
										<?
									}
									?>
									<td > Color Total</td>
									</tr>
									<?
									$k=1; $total_qnty=0;
									foreach ($color_size_array[$PO_NO] as $colorkey=>$color_data)
									{
										$m=1;
									 
											$po_rowspan=$po_rowspan_arr[$PO_NO];
											?>
											<tr>
												
												<td align="left"><? echo $color_library[$colorkey]; ?></td>
												<?
												 $tot_qnty=array();
												foreach ($posize_array[$PO_NO] as $sizval)
												{
													$size_count=count($sizval);
													$po_size_qty=$po_size_qty_array[$PO_NO][$colorkey][$sizval]['po_qty']
													?>
													<td align="right"><? echo fn_number_format($po_size_qty,0); ?></td>
													<?
													$tot_qnty[$PO_NO][$cid]+=$po_size_qty;
													$tot_qnty_size[$sizval]+=$po_size_qty;
												}
												?>
												<td align="right"><? echo fn_number_format($tot_qnty[$PO_NO][$cid],0); ?></td>
											</tr>
											<?
											$total_qnty+=$tot_qnty[$PO_NO][$cid];
											$m++;
										
										$k++;
									}
									?>
									<tr>
									<td align="right"><strong>Grand Total :</strong></td>
									<?
									foreach ($posize_array[$PO_NO] as $sizval)
									{
										?>
										<td align="right"><?php echo fn_number_format($tot_qnty_size[$sizval],0); ?></td>
										<?
									}
									?>
									<td align="right"><?php echo fn_number_format($total_qnty,0); ?></td>
								</tr>
							</table>
					
					   </td>
                    </tr>
					<tr bgcolor="#FFF">
                        <td><strong>Avg. Yarn Price</strong></td><td><?= number_format($short_quat_arr[$style]['yarn_avg_rate'],2); ?></td>
                    </tr>
					<tr bgcolor="#FFF">
                        <td><strong>Remark</strong></td><td></td>
                    </tr>
                </table>
                </div>
                </td>
			<?php
            	$flag++;
			  }
			  	
				echo "</tr></table>";
			  }
			  ?>
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
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "**$filename**$rpt_type";
	disconnect($con);
	exit();
}

if($action=="report_generate_3") //md mamun -crm-5491-05-03-2023
{

	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_working_company_name=str_replace("'","",$cbo_working_company_name);
	$txt_job_id=str_replace("'","",$txt_job_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer);
	$cbo_brand_name=str_replace("'","",$cbo_brand_name);
	$txt_job=str_replace("'","",$txt_job);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$txt_style=str_replace("'","",$txt_style);
	$cbo_gauge=str_replace("'","",$cbo_gauge);
	$cbo_date_type=str_replace("'","",$cbo_date_type);
	
	

	$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_name_arr=return_library_array( "select id,company_name from lib_company",'id','company_name');
	$sample_library=return_library_array( "select id,sample_name from lib_sample", "id", "sample_name"  );
	$lib_size=return_library_array( "select size_name,id from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");	 
	$season_name_arr=return_library_array( "select id,season_name from   lib_buyer_season  where status_active=1 and is_deleted=0",'id','season_name');
	$brand_name_arr=return_library_array( "select id,brand_name from   lib_buyer_brand  where status_active=1 and is_deleted=0",'id','brand_name');
	$lib_item_group_arr=return_library_array( "select item_name,id from lib_item_group where item_category=4 order by item_name", "id", "item_name");

	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			
		}
		else if($db_type==2)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			
		}
		if($cbo_date_type==1){
			$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
		else if($cbo_date_type==2){
			$date_cond=" and b.PO_RECEIVED_DATE between '$start_date' and '$end_date'";
		}
	}

	if ($cbo_buyer == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "") {
				$whereCon .= " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
				
			} else {
				$whereCon .= "";
				
			}
		} else {
			$whereCon .= "";
			
		}
	} else {
		$whereCon .= " and a.buyer_name in ($cbo_buyer) "; //.str_replace("'","",$cbo_buyer_name)
	
	}


	if($txt_job_id!=''){
		$whereCon.="and a.id in($txt_job_id)";
	}
	if($txt_order_id!=''){
		$whereCon.="and b.id in($txt_order_id)";
	}
	if($txt_job!=''){
		$whereCon.="and a.JOB_NO_PREFIX_NUM in($txt_job)";
	}
	if($txt_style!=''){
		$whereCon.="and a.STYLE_REF_NO in('".str_replace(",","','",$txt_style)."')";
	}
	
	if($cbo_buyer!=0){
		$whereCon.="and a.BUYER_NAME in($cbo_buyer)";
	}
	if($cbo_gauge!=''){
		$whereCon.="and a.GAUGE in($cbo_gauge)";
	}
	if($cbo_working_company_name!=''){
		$whereCon.="and a.style_owner in($cbo_working_company_name)";
	}
	
	if($cbo_working_company_name!=''){
		$workingCompanyCon="and d.DELIVERY_COMPANY_ID in($cbo_working_company_name)";
	}

	$brand_cond="";
	if($cbo_brand_name==0)
	{
		if($filterBrandId!="") $brand_cond="and a.brand_id in ($filterBrandId)"; else $brand_cond="";
	}
	else $brand_cond="and a.brand_id in($cbo_brand_name) ";
			
	// echo $whereCon;
	
	$width=240;


	if($reportType==4)
	{
		//$shipCon=" and b.SHIPING_STATUS=3";
	}
	else if($reportType==5)
	{
		$shipCon=" and b.SHIPING_STATUS != 3";
	}
	
	if($reportType==3)
	{
		$table=",pro_garments_production_mst c";
		$tableCon=" and c.po_break_down_id=b.id and c.garments_nature=100 and c.production_type=52  and c.status_active=1";
	}
		
		
	
		if($reportType==1){$confirmCon =" and b.IS_CONFIRMED=1";}
		else if($reportType==2){$confirmCon =" and b.IS_CONFIRMED=2";}
		else if($reportType==5){$confirmCon =" and b.IS_CONFIRMED=1";}
		else{$confirmCon ="";}
		
		$con = connect();
		execute_query("delete from tmp_poid where userid=".$user_id."");
		
		
	  	$sql= "SELECT b.id as PO_ID,b.PO_NUMBER, a.FABRIC_COMPOSITION,a.COMPANY_NAME,a.JOB_NO,a.ID as JOB_ID,a.STYLE_REF_NO,a.BUYER_NAME,a.GAUGE,sum((b.PO_QUANTITY*a.TOTAL_SET_QNTY)) as PO_QUANTITY_PCS,MIN(b.pub_shipment_date) as SHIPMENT_DATE,min(b.IS_CONFIRMED) as IS_CONFIRMED,a.AVG_UNIT_PRICE ,a.STYLE_DESCRIPTION,a.BRAND_ID,a.YARN_QUALITY,b.UNIT_PRICE,a.SHIP_MODE,a.STYLE_OWNER,a.SEASON_BUYER_WISE,b.details_remarks,a.yarn_avg_rate,a.knitting_pattern
		from wo_po_details_master a,wo_po_break_down b $table 
		where a.job_no=b.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1
		$date_cond $whereCon $shipCon $tableCon $brand_cond and a.COMPANY_NAME in($cbo_company_name) $confirmCon
		
		group by a.FABRIC_COMPOSITION,a.COMPANY_NAME,a.BUYER_NAME,a.JOB_NO,a.ID,a.AVG_UNIT_PRICE,a.STYLE_REF_NO,a.GAUGE,b.id,b.PO_NUMBER,a.STYLE_DESCRIPTION,a.BRAND_ID,a.YARN_QUALITY,b.UNIT_PRICE,a.SHIP_MODE,a.STYLE_OWNER,a.SEASON_BUYER_WISE,b.details_remarks,a.yarn_avg_rate,a.knitting_pattern
		order by  MIN(b.pub_shipment_date),a.JOB_NO";//c.job_no
		
		// echo $sql;
		$sql_result=sql_select($sql);
		$orderDataArr=array();$buyerWiseJobQty=array();$jobWisebuyerArr=array();$jobPoArr=array();
		// print_r($orderDataArr);exit;
		foreach($sql_result as $row)
		{
			// $orderDataArr[$row[STYLE_REF_NO]][$row[JOB_NO]]=$row;
			$buyerWiseJobQty[$row['SHIPMENT_DATE']]+=$row['PO_QUANTITY_PCS'];
			$jobWisebuyerArr[$row['JOB_NO']]+=$row['BUYER_NAME'];
			$jobWiseStyleArr[$row['JOB_NO']]=$row['STYLE_REF_NO'];
			$jobPoArr[$row['STYLE_REF_NO']]=$row['TOTAL_PO'];
			$jobIdArr[$row['JOB_ID']]=$row['JOB_ID'];
			$poIdArr[$row['PO_ID']]=$row['PO_ID'];
			$orderDataArr[$row['SHIPMENT_DATE']][$row['PO_ID']]=$row;

			$r_id2=execute_query("insert into tmp_poid (userid, pono,type) values (".$user_id.",'".$row['JOB_NO']."',1".")");
		
		}
		$jobIds=implode(",",$jobIdArr);
		$poIds=implode(",",$poIdArr);
		//-----------------------------------------------------------------------------------------------------------------
		$pre_cost_array=sql_select("select id, remark, job_no, trim_group, description, brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, cons_breack_down, country, calculatorstring, seq, status_active from wo_pre_cost_trim_cost_dtls where job_id in ($jobIds) order by seq");

			foreach($pre_cost_array as $row){

				$job_wise_data[$row[csf('job_no')]]['accessories'] .=$lib_item_group_arr[$row[csf('trim_group')]].",";
			}

		//--------------------------------sample po wise-------------------------------------
		$sample_data_array=sql_select("Select id,submitted_to_buyer,approval_status,approval_status_date,job_no_mst,po_break_down_id,sample_type_id
		from wo_po_sample_approval_info where     po_break_down_id in ($poIds) and sample_type_id in (35,36,37,38,6,4,79,104,143,56,149,150)  and status_active=1");
		
			foreach($sample_data_array as $row){

					$job_po_wise_data[$row[csf('job_no_mst')]][$row[csf('po_break_down_id')]][$row[csf('sample_type_id')]]['submitted_date']=$row[csf('submitted_to_buyer')];
				if($row[csf('approval_status')]==3){
					$job_po_wise_data[$row[csf('job_no_mst')]][$row[csf('po_break_down_id')]][$row[csf('sample_type_id')]]['approval_date']=$row[csf('approval_status_date')];
				}
			}
		
		
		
		$image_arr=return_library_array( "select a.MASTER_TBLE_ID,a.IMAGE_LOCATION from COMMON_PHOTO_LIBRARY a,tmp_poid tmp where a.FORM_NAME='knit_order_entry' and a.MASTER_TBLE_ID=tmp.pono and  tmp.userid=".$user_id." and a.FILE_TYPE=1",'MASTER_TBLE_ID','IMAGE_LOCATION');

		
		
		
		//print_r($image_arr);
		
		ob_start();
		?>
	
        <div>
		<fieldset>
			<table align="left">
				<tr>
					<td><div style="float:left;"><span style="background:#FFF; padding:0 8px; width:1px; "></span> Confirm&nbsp;</div></td>
					<td><div style="float:left; "><span style="background:#B7DEE8; padding:0 8px; width:8px; "></span>  Projected&nbsp;</div></td>
					<td><div style="float:left; "><span style="background:#FCD5B4; padding:0 8px; width:8px; "></span>  Knit Comp&nbsp;</div></td>
					<td><div style="float:left; "><span style="background:#00FF00; padding:0 8px; width:8px; "></span>  Full Shipped&nbsp;</div></td>
					<td><div style="float:left; "><span style="background:#FBFFCA; padding:0 8px; width:8px; "></span>  Ship Pending&nbsp;</div></td>
				</tr>
				<tr>
					<td colspan="5"><b style="float:left;">All Shipment Date Total: <?= array_sum($buyerWiseJobQty);?></b></td>
				</tr>
			</table>
		</fieldset>		  
        <fieldset>
      
              
		
		<style>
				@media print {
					.pageBreak {
						page-break-after: always;
					}
				}
   		 </style>
			  
			  <?php 
				//-----------------------------------------------------------------------
				$short_quatation_data=sql_select("select  b.mst_id, b.tot_cons, b.rate,a.style_ref ,a.company_id,c.knitting_time,c.makeup_time	from qc_mst a ,qc_cons_rate_dtls b,qc_tot_cost_summary c where a.qc_no=b.mst_id and a.qc_no=c.mst_id   and a.status_active=1 and b.is_deleted=0 and b.type!=4 and a.company_id in($cbo_company_name) and b.tot_cons >0	group by b.mst_id, b.tot_cons, b.rate,a.style_ref ,a.company_id,c.knitting_time,c.makeup_time ");
				 
				foreach($short_quatation_data as $row)
				{
					$short_quat_arr[$row[csf("style_ref")]]['yarn_avg_rate']=$row[csf("rate")];
					$short_quat_arr[$row[csf("style_ref")]]['gmts_weight']=$row[csf("tot_cons")];
					$short_quat_arr[$row[csf("style_ref")]]['knitting_time']=$row[csf("knitting_time")];
					$short_quat_arr[$row[csf("style_ref")]]['makeup_time']=$row[csf("makeup_time")];
				}



				$color_size_sql= "select a.po_number,a.id as po_id,b.color_number_id,b.size_number_id,sum(b.order_quantity) as order_quantity from wo_po_break_down a,wo_po_color_size_breakdown b where  b.po_break_down_id=a.id  and a.id in ($poIds)   and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1group by a.po_number,a.id,b.size_number_id,b.color_number_id";

				$result_color=sql_select($color_size_sql);
				$color_size_array=array();
				foreach($result_color as $row)
				{
				$color_size_array[$row[csf("po_id")]][$row[csf("color_number_id")]]=$row[csf("order_quantity")];
				$po_size_qty_array[$row[csf("po_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['po_qty']=$row[csf("order_quantity")];
				$po_array[$row[csf("po_id")]]['po_no']=$row[csf("po_number")];
				$po_size[$row[csf("po_id")]][$row[csf("size_number_id")]]=$row[csf("size_number_id")];
				}
				$size_sql= "select b.size_number_id,sum(b.order_quantity) as order_quantity,a.id as po_id from wo_po_break_down a,wo_po_color_size_breakdown b where  b.po_break_down_id=a.id  and a.id in ($poIds)  and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1 group by b.size_number_id,a.id order by b.size_number_id";

				$result_size=sql_select($size_sql);
				$posize_array=array();
				foreach($result_size as $row)
				{
				$posize_array[$row[csf("po_id")]][$row[csf("size_number_id")]]=$row[csf("size_number_id")];
				}

				$po_rowspan_arr=array();
				foreach($color_size_array as $pokey=>$po_data)
				{
				$po_row_span=0;
				foreach($po_data as $colorkey=>$val)
				{
					$po_row_span++;
				}
				$po_rowspan_arr[$pokey]=$po_row_span;
				}



			$kk=1;
			  foreach($orderDataArr as $ship_date=>$buyerDataArr){
				$bgcolor="#B1E6FC";
							
				?>

					<table  class="pageBreak" >	
					<br>
						 <div >

							<h2 align="left" bgcolor="<?= $bgcolor;?>"> 
								<b>SHIPMENT DATE :</b><?= $ship_date;?>,
								<b>SHIPMENT DATE Total:</b><?=$buyerWiseJobQty[$ship_date] ;?>
							</h2>

						</div>
						<br>
						<div>
							<tr style="vertical-align:top;">
						<?
	       
						$flag=1;$cmd=1;
						foreach($buyerDataArr as $PO_NO=>$rows){
							$bgcolor="#B1E6FC";
							
							$style= $rows['STYLE_REF_NO'];
							if($flag==4){
								echo "</tr><tr><td>";$flag=1;}
								else{echo "<td>";}
							?>
						 
								
							   <div style="float:left; width:<?=255+count($po_size[$PO_NO])*40;?>px; margin:0px;vertical-align:top;" align="left">
								<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?=255+count($po_size[$PO_NO])*40;?>px"  style="vertical-align:top;" class="rpt_table" bgcolor="<?= $bgcolor;?>" >
										 
									<tr >
											<td colspan="2" rowspan="3" align="center" style="height: 110px;width:110px" >								
												<img  src="<? echo base_url( $image_arr[$rows['JOB_NO']]); ?>" height="100"  width="100"  alt="No Image" />
											</td>
									</tr>
									<tr >	</tr>
									<tr >	</tr>
										 
									<tr bgcolor="#FFF">
						
                     				   <td width="130"><strong><? if($flag==1) echo 'Style';else echo "";?></strong></td>						
										<td width="120"><?= $rows['STYLE_REF_NO']; ?></td>
                   					</tr>


									<tr bgcolor="#FFF">
										<td ><strong><? if($flag==1) echo 'Order No';else echo "";?></strong></td><td><?= $rows['PO_NUMBER']; ?></td>
									</tr>
									
									<tr bgcolor="#FFF">
										<td ><strong><? if($flag==1) echo 'Job No';else echo "";?></strong></td><td><?= $rows['JOB_NO']; ?></td>
									</tr>
									<tr bgcolor="#FFF">
										<td ><strong><? if($flag==1) echo 'Session';else echo "";?></strong></td><td title="<?=$rows['SEASON_BUYER_WISE'];?>"><?=$season_name_arr[$rows['SEASON_BUYER_WISE']]; ?></td>
									</tr>
									<tr bgcolor="#FFF">
										<td ><strong><? if($flag==1) echo 'Buyer';else echo "";?></strong></td><td><?= $buyer_name_arr[$rows['BUYER_NAME']]; ?></td>
									</tr>
									<tr bgcolor="#FFF">
										<td ><strong><? if($flag==1) echo 'Brand';else echo "";?></strong></td><td><?= $brand_name_arr[$rows['BRAND_ID']]; ?></td>
									</tr>
									<tr bgcolor="#FFF">
										<td ><strong><? if($flag==1) echo 'Style Description';else echo "";?></strong></td><td><?=$rows['STYLE_DESCRIPTION']; ?></td>
									</tr>
									<tr bgcolor="#FFF">
										<td ><strong><? if($flag==1) echo 'Knitting Pattern';else echo "";?></strong></td><td><?= $knitting_pattern_arr[$rows[csf('knitting_pattern')]]; ?></td>
									</tr>
									<tr bgcolor="#FFF">
										<td height="50px;"><strong><? if($flag==1) echo 'Fabrication';else echo "";?></strong></td><td><?=$rows['YARN_QUALITY']; ?></td>
									</tr>
									<tr bgcolor="#FFF">
										<td><strong><? if($flag==1) echo 'Gauge';else echo "";?></strong></td><td><?= $gauge_arr[$rows['GAUGE']]; ?></td>
									</tr>
									<tr bgcolor="#FFF">
										<td><strong><? if($flag==1) echo 'Local Yarn Supplier';else echo "";?></strong></td><td></td>
									</tr>
									<tr bgcolor="#FFF">
										<td><strong><? if($flag==1) echo 'Import Yarn Supplier';else echo "";?></strong></td><td></td>
									</tr>
									<tr bgcolor="#FFF">
										<td><strong><? if($flag==1) echo 'Gmts Weight';else echo "";?></strong></td><td><?= $short_quat_arr[$style]['gmts_weight']; ?> Lbs</td>
									</tr>

									<tr bgcolor="#FFF">
										<td><strong><? if($flag==1) echo 'Price/Unit';else echo "";?></strong></td><td><?= number_format($rows['UNIT_PRICE'],2); ?></td>
									</tr>
									<tr bgcolor="#FFF">
										<td><strong> <? if($flag==1) echo 'Quantity';else echo "";?></strong></td><td><?=$rows['PO_QUANTITY_PCS']; ?></td>
									</tr>
									<tr bgcolor="#FFF">
										<td><strong><? if($flag==1) echo 'F. Shipment Date';else echo "";?></strong></td><td><?= change_date_format($rows['SHIPMENT_DATE']); ?></td>
									</tr>
									<tr bgcolor="#FFF">
										<td><strong><? if($flag==1) echo 'Shipment Mode';else echo "";?></strong></td><td><?= $shipment_mode[$rows['SHIP_MODE']]; ?></td>
									</tr>
									<tr bgcolor="#FFF">
										<td height="50px;"><strong><? if($flag==1) echo 'Accessories';else echo "";?></strong></td><td><?=rtrim($job_wise_data[$rows['JOB_NO']]['accessories'],","); ?></td>
									</tr>
									<tr bgcolor="#FFF">
										<td><strong><? if($flag==1) echo 'Knitting Time';else echo "";?></strong></td><td><?=$short_quat_arr[$style]['knitting_time'];?> </td>
									</tr>
									<tr bgcolor="#FFF">
										<td><strong><? if($flag==1) echo 'P2P Time';else echo "";?></strong></td><td> <?=$short_quat_arr[$style]['makeup_time'];?></td>
									</tr>
									<tr bgcolor="#FFF">
										<td title="79"><strong><? if($flag==1) echo 'Development / Proto Sample';else echo "";?></strong></td>
										<td><?="Sent On-".$job_po_wise_data[$rows['JOB_NO']][$PO_NO][35]['submitted_date']."; Approved On-".$job_po_wise_data[$rows['JOB_NO']][$PO_NO][35]['approval_date']; ?></td>
									</tr>
									<tr bgcolor="#FFF">
										<td title="104"><strong><? if($flag==1) echo 'First sample /Ref Tag';else echo "";?></strong></td>
										<td><?="Sent On-".$job_po_wise_data[$rows['JOB_NO']][$PO_NO][36]['submitted_date']."; Approved On-".$job_po_wise_data[$rows['JOB_NO']][$PO_NO][36]['approval_date']; ?></td>
									</tr>
									<tr bgcolor="#FFF">
										<td title="143"><strong><? if($flag==1) echo 'PP/Sealer/Gold Seal Sample';else echo "";?></strong></td>
										<td><?="Sent On-".$job_po_wise_data[$rows['JOB_NO']][$PO_NO][37]['submitted_date']."; Approved On-".$job_po_wise_data[$rows['JOB_NO']][$PO_NO][37]['approval_date']; ?></td>
									</tr>
									<tr bgcolor="#FFF">
										<td title="56"><strong><? if($flag==1) echo 'Photo Sample';else echo "";?></strong></td>
										<td><?="Sent On-".$job_po_wise_data[$rows['JOB_NO']][$PO_NO][4]['submitted_date']."; Approved On-".$job_po_wise_data[$rows['JOB_NO']][$PO_NO][4]['approval_date']; ?></td>
									</tr>
									<tr bgcolor="#FFF">
										<td title="149"><strong><? if($flag==1) echo 'Lab Test/Labo Sample';else echo "";?></strong></td>
										<td><?="Sent On-".$job_po_wise_data[$rows['JOB_NO']][$PO_NO][38]['submitted_date']."; Approved On-".$job_po_wise_data[$rows['JOB_NO']][$PO_NO][38]['approval_date']; ?></td>
									</tr>
									<tr bgcolor="#FFF">
										<td title="150"><strong><? if($flag==1) echo 'Shipment sample';else echo "";?></strong></td>
										<td><?="Sent On-".$job_po_wise_data[$rows['JOB_NO']][$PO_NO][6]['submitted_date']."; Approved On-".$job_po_wise_data[$rows['JOB_NO']][$PO_NO][6]['approval_date']; ?></td>
									</tr>
									
								
									<tr bgcolor="#FFF">
										<td><strong><? if($flag==1) echo 'Production Factory';else echo "";?></strong></td><td><?=$company_name_arr[$rows['STYLE_OWNER']]; ?></td>
									</tr>
                                    <?
                                    $kk++;
									?>
									<tr bgcolor="#FFF">
                     				   <td title="<?=count($po_size[$PO_NO])*40;?>"><strong><? if($flag==1) echo 'Color Size Ratio';else echo "";?></strong></td>
										<td style="width:<?=count($po_size[$PO_NO])*40;?>px;text-align:center; font-family:Arial Narrow">
						 				 <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:<?=count($po_size[$PO_NO])*40;?>px;text-align:center; font-family:Arial Narrow" rules="all">
											<label><b>SIZE</b></label>
									<tr style="font-weight:bold">
									
									<td width="60">Color</td>
									<?
									foreach ($posize_array[$PO_NO] as $sizid)
									{
										?>
											<td width="60"> <?=$lib_size[$sizid];  ?> </td>
										<?
									}
									?>
									<td > Color Total</td>
									</tr>
									<?
									$k=1; $total_qnty=0;$tot_qnty_size=array();
									foreach ($color_size_array[$PO_NO] as $colorkey=>$color_data)
									{
										$m=1;
									 
											$po_rowspan=$po_rowspan_arr[$PO_NO];
											?>
											<tr>
												
												<td align="left" ><? echo $color_library[$colorkey]; ?></td>
												<?
												 $tot_qnty=array();
												foreach ($posize_array[$PO_NO] as $sizval)
												{
													$size_count=count($sizval);
													$po_size_qty=$po_size_qty_array[$PO_NO][$colorkey][$sizval]['po_qty']
													?>
													<td align="right"  width="60"><? echo fn_number_format($po_size_qty,0); ?></td>
													<?
													$tot_qnty[$PO_NO][$cid]+=$po_size_qty;
													$tot_qnty_size[$sizval]+=$po_size_qty;
												}
												?>
												<td align="right"  width="60"><? echo fn_number_format($tot_qnty[$PO_NO][$cid],0); ?></td>
											</tr>
											<?
											$total_qnty+=$tot_qnty[$PO_NO][$cid];
											$m++;
										
										$k++;
									}
									?>
									<tr>
									<td align="right"  width="60"><strong>Grand Total :</strong></td>
									<?

									foreach ($posize_array[$PO_NO] as $sizval)
									{
										?>
										<td align="right"  width="60"><?php echo fn_number_format($tot_qnty_size[$sizval],0); ?></td>
										<?
									}
									?>
									<td align="right"  width="60"><?php echo fn_number_format($total_qnty,0); ?></td>
								</tr>
							</table>
					
					   </td>
                    </tr>
					<tr bgcolor="#FFF">
                        <td><strong><? if($flag==1) echo 'Avg. Yarn Price';else echo "";?></strong></td><td><?= number_format($rows[csf('yarn_avg_rate')],2); ?></td>
                    </tr>
					<tr bgcolor="#FFF">
                        <td><strong><? if($flag==1) echo 'Remark';else echo "";?></strong></td><td><?=$rows[csf('details_remarks')];?></td>
                    </tr>
								</table> 
								</div>
				           </td> 

						<? 	$flag++;}?>
						 </tr>
						 </div>
					</table>
			 <?}?>
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
	$create_new_doc = fopen($filename, 'w');
	
	$ob_con = explode("<!-- image_remove_from_excel -->",ob_get_contents());
	$ob_con_arr = array();
	for($sl = 0 ; $sl < count($ob_con);$sl++)
	{
		if($sl % 2 == 0 )
		{
			$ob_con_arr[$ob_con[$sl]] = $ob_con[$sl];
		}
	}
	$ob_implode = implode("",$ob_con_arr);
	$is_created = fwrite($create_new_doc,$ob_implode);
	$filename=$user_id."_".$name.".xls";
	echo 'text';exit;
	
	echo "**$filename**$rpt_type";
	disconnect($con);
	exit();
} 

/* if($action=="report_generate_8")
{ 

	extract($_REQUEST);
	$cbo_company_name=str_replace("'", "", $cbo_company_name);
	$cbo_working_company_name=str_replace("'", "", $cbo_working_company_name);
	$txt_job_id=str_replace("'", "", $txt_job_id);
	$cbo_buyer=str_replace("'", "", $cbo_buyer);
	$cbo_brand_name=str_replace("'", "", $cbo_brand_name);
	$txt_job=str_replace("'", "", $txt_job);
	$txt_order_id=str_replace("'", "", $txt_order_id);
	$txt_style=str_replace("'", "", $txt_style);
	$cbo_gauge=str_replace("'", "", $cbo_gauge);
	$cbo_date_type=str_replace("'", "", $cbo_date_type);

	$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_name_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$sample_library=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name"  );
	$lib_size=return_library_array( "select size_name, id from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$lib_color=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");	 
	$season_name_arr=return_library_array( "select id, eason_name from lib_buyer_season  where status_active=1 and is_deleted=0",'id','season_name');
	$brand_name_arr=return_library_array( "select id, brand_name from lib_buyer_brand  where status_active=1 and is_deleted=0",'id','brand_name');
	$lib_item_group_arr=return_library_array( "select item_name,id from lib_item_group where item_category=4 order by item_name", "id", "item_name");
	 

	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
		$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
 
		if($cbo_date_type==1){
			$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
			$field ="b.PUB_SHIPMENT_DATE";
		}
		else if($cbo_date_type==2){
			$date_cond=" and b.PO_RECEIVED_DATE between '$start_date' and '$end_date'";
			$field ="b.PO_RECEIVED_DATE";
		}
	}

	if ($cbo_buyer == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "") {
				$whereCon .= " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			} else {
				$whereCon .= "";
			}
		} else {
			$whereCon .= "";
		}
	} else {
		$whereCon .= " and a.buyer_name in ($cbo_buyer) ";
	}

	if($txt_job_id!=''){
		$whereCon.="and a.id in($txt_job_id)";
	}
	if($txt_order_id!=''){
		$whereCon.="and b.id in($txt_order_id)";
	}
	if($txt_job!=''){
		$whereCon.="and a.JOB_NO_PREFIX_NUM in($txt_job)";
	}
	if($txt_style!=''){
		$whereCon.="and a.STYLE_REF_NO in('".str_replace(",","','",$txt_style)."')";
	}
	
	if($cbo_buyer!=0){
		$whereCon.="and a.BUYER_NAME in($cbo_buyer)";
	}
	if($cbo_gauge!=''){
		$whereCon.="and a.GAUGE in($cbo_gauge)";
	}
	if($cbo_working_company_name!=''){
		$whereCon.="and a.style_owner in($cbo_working_company_name)";
	}

	$brand_cond="";
	if($cbo_brand_name !=''){
		$brand_cond = "and a.brand_id in ($cbo_brand_name)";
	}
  

	if($reportType==1){$confirmCon =" and b.IS_CONFIRMED=1";}
	else if($reportType==2){$confirmCon =" and b.IS_CONFIRMED=2";}
	else if($reportType==5){$confirmCon =" and b.IS_CONFIRMED=1";}
	else{$confirmCon ="";}

	$con = connect();
	execute_query("delete from tmp_poid where userid=".$user_id."");


	$reportSql = "SELECT b.id as PO_ID, $field as SHIPMENT_DATE, b.PO_NUMBER, b.UNIT_PRICE, b.details_remarks, b.PO_QUANTITY, b.pub_shipment_date, b.PO_RECEIVED_DATE,
	b.IS_CONFIRMED, b.SHIPING_STATUS,
	a.FABRIC_COMPOSITION, a.COMPANY_NAME, a.JOB_NO, a.GARMENTS_NATURE, a.STYLE_REF_NO, a.BUYER_NAME, a.GAUGE,
	a.AVG_UNIT_PRICE ,a.STYLE_DESCRIPTION, a.brand_id, a.YARN_QUALITY, a.SHIP_MODE, a.STYLE_OWNER, a.TOTAL_SET_QNTY,
	a.SEASON_BUYER_WISE, a.yarn_avg_rate, a.knitting_pattern, a.JOB_NO_PREFIX_NUM
	from WO_PO_DETAILS_MASTER a, WO_PO_BREAK_DOWN b where a.id=b.job_id and a.is_deleted=0 and b.is_deleted=0 $date_cond $whereCon $brand_cond and a.COMPANY_NAME in ($cbo_company_name) order By $field";

 
    $reportSqlRes=sql_select($reportSql);

	$tmp_po_arr = array();
	$dataArr = array();
	$ship_date_wise_po_arr = array();
	$targetBr = 5;
	foreach($reportSqlRes as $rows){
		// if($dataArr[$rows['SHIPMENT_DATE']] ==''){$key=0;$flag = 0;}
		
		$dataArr[$rows['SHIPMENT_DATE']][$key][$rows['PO_ID']] = $rows;
		$ship_date_wise_po_arr[$rows['SHIPMENT_DATE']][$rows['PO_ID']]= $rows['PO_ID'];
		$tmp_po_arr[$rows['PO_ID']] = $rows['PO_ID'];
		 
		$flag++;
		if($flag == $targetBr){$key++;$flag = 0;}
	} 
	unset($reportSqlRes);

	$size_sql= "SELECT a.PO_BREAK_DOWN_ID,a.SIZE_NUMBER_ID, a.COLOR_NUMBER_ID, sum(a.order_quantity) as ORDER_QUANTITY
	            from wo_po_color_size_breakdown a 
				where a.is_deleted=0 and a.status_active=1 ".where_con_using_array($tmp_po_arr,0,'a.PO_BREAK_DOWN_ID')."  
				group by a.PO_BREAK_DOWN_ID,a.SIZE_NUMBER_ID, a.COLOR_NUMBER_ID order by SIZE_NUMBER_ID asc";
	$size_sql_res=sql_select($size_sql);
	unset($tmp_po_arr);


	// $po_color_size_arr[po_id][color_id][size_id]= val
	$po_color_size_raiot_arr = array();
	foreach($size_sql_res as $rows){
		$po_color_size_raiot_arr[$rows['PO_BREAK_DOWN_ID']][$rows['COLOR_NUMBER_ID']][$rows['SIZE_NUMBER_ID']] = $rows['ORDER_QUANTITY'];
		$po_size_arr[$rows['PO_BREAK_DOWN_ID']][$rows['SIZE_NUMBER_ID']] += $rows['ORDER_QUANTITY'];
	}
	unset($size_sql_res);

	$ship_date_wise_qty_arr=array();
	foreach($ship_date_wise_po_arr as $ship_date => $poRow){
		foreach($poRow as $po_id){
			$ship_date_wise_qty_arr[$ship_date] += array_sum($po_size_arr[$po_id]);
		}

	}
	
	ob_start();

	$image_arr = return_library_array( "select a.MASTER_TBLE_ID,a.IMAGE_LOCATION from COMMON_PHOTO_LIBRARY a where a.FORM_NAME='knit_order_entry' and a.FILE_TYPE=1",'MASTER_TBLE_ID','IMAGE_LOCATION');

	foreach($dataArr as $ship_date => $customRow){
		$bgcolor="#B1E6FC";
		?>
		<div>
			<h2 align="left" style="background-color: <?= $bgcolor;?>"> 
				<b><?= str_replace(['b.','_'],' ',$field);?> DATE :</b><? echo $ship_date; ?>,
				<b><?= str_replace(['b.','_'],' ',$field);?> DATE Total:</b><?= $ship_date_wise_qty_arr[$ship_date];?>
			</h2>
		</div>

		<?
		foreach($customRow as $data_row){
			?>
			    <div style="float:left; margin:0px;vertical-align:top;" align="left">
				<table cellspacing="0" cellpadding="0" border="1" rules="all"  style="vertical-align:top;" class="rpt_table">	 
				<tr style="height: 120px;"> 
					<td align="center"><strong>Particulars</strong></td>
					<?
					foreach($data_row as $row){
						?>
						<td align="center"><img  src="<?= base_url( $image_arr[$row['JOB_NO']]); ?>" height="100"  width="100"  alt="No Image" /></td>
						<?	
					}
					?> 
				</tr>
				<tr> 
					<td><strong>Style</strong></td>
					<?
					foreach($data_row as $row){
						?>
						<td><?= $row['STYLE_REF_NO'];?></td>
						<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Order No</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= $row['PO_NUMBER'];?></td>
					<?	
					}
				     ?>
				</tr>

				<tr>
					<td><strong>Job No</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= $row['JOB_NO'];?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Session</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= $row['SEASON_BUYER_WISE'];?></td>
					<?	
					}
					?>
				</tr>

				<tr>
			    <td><strong>Buyer</strong></td>
				    <?
					foreach($data_row as $row){
					?>
						<td><?= $buyer_name_arr[$row['BUYER_NAME']];?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Brand</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= $brand_name_arr[$row['BRAND_ID']];?></td>
					<?	
					}
					?>
				</tr>

				<tr>
			        <td><strong>Style Description</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= $row['STYLE_DESCRIPTION'];?></td>
					<?	
					}
					?>
				</tr>
				<tr>    
					<td><strong>Knitting Pattern</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= $knitting_pattern_arr[$row[csf('knitting_pattern')]];?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Fabrication</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= $row['YARN_QUALITY'];?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Gauge</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= $gauge_arr[$row['GAUGE']];?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Local Yarn Supplier</strong></td>
					<?
					foreach($data_row as $row){
					?>
					    <td></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Gmts Weight</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= $short_quat_arr[$style]['gmts_weight'];?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Price/Unit</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= $row['UNIT_PRICE'];?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Quantity</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= $row['PO_QUANTITY'];?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>F. Shipment Date</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= $row['SHIPMENT_DATE'];?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Shipment Mode</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= $shipment_mode[$row['SHIP_MODE']];?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Accessories</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= rtrim($job_wise_data[$row['JOB_NO']]['accessories'],",");?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Knitting Time</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= $short_quat_arr[$style]['knitting_time'];?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>P2P Time</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= $short_quat_arr[$style]['makeup_time'];?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Development / Proto Sample</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= "Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][35]['submitted_date']."; Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][35]['approval_date']; ?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>First sample /Ref Tag</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= "Sent On-".$job_po_wise_data[$row['JOB_ID']][$row['PO_ID']][36]['submitted_date']."; Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][36]['approval_date'];?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>PP/Sealer/Gold Seal Sample</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= "Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][37]['submitted_date']."; Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][37]['approval_date']; ?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Photo Sample</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= "Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][4]['submitted_date']."; Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][4]['approval_date']; ?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Lab Test/Labo Sample</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= "Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][38]['submitted_date']."; Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][38]['approval_date']; ?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Shipment Sample</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= "Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][6]['submitted_date']."; Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][6]['approval_date']; ?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Production Factory</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= $company_name_arr[$rows['STYLE_OWNER']]; ?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td>Color Size Ratio</td>
					<?
					foreach($data_row as $row){
					?>
					    <div style="margin:0px;vertical-align:top;">
						    <td> 
								<table border="1" cellspacing="0" cellpadding="3" width="100%">
									<thead>
										<tr>
											<th><strong>Color</strong></th>
											<?php
											foreach($po_size_arr[$row['PO_ID']] as $size_id => $sizeQty){
												echo "<th align='center'>{$lib_size[$size_id]}</th>";
											}
											?>
											<th>Color Total</th>
										</tr>
									</thead>
									<?
									foreach($po_color_size_raiot_arr[$row['PO_ID']] as $color_id => $sizeRow){
									?>
									<tbody> 
										<tr>
											<td><?= $lib_color[$color_id];?></td>
											<? 
											foreach($sizeRow as $size_id => $ratioQty){
												echo "<td align='center'>{$ratioQty}</td>";
											}
											?>
											<td align="center"><?= array_sum($sizeRow);?></td>
										</tr>
									</tbody> 
									<?php
									}
									?>
									<tfoot>
										<tr>
											<td><strong>G. Total</strong></td>
											<?php
											foreach($po_size_arr[$row['PO_ID']] as $size_id => $sizeQty){
												echo "<td align='center'>$sizeQty</td>";
											}
											?>
											<td align="center"><strong><?= array_sum($po_size_arr[$row['PO_ID']]);?></strong></td>
										</tr>
									</tfoot>
								</table>
						    </td>
						</div>
					<?
					}
					?>
				</tr>
				<tr>
					<td><strong>Avg. Yarn Price</strong></td>
					<?
					foreach($data_row as $row){
						?>
						<td><?= number_format($short_quat_arr[$style]['yarn_avg_rate'], 2); ?></td>
						<?	
					}
					?>
				</tr>

				<tr>
					<td><strong>Remark</strong></td>
					<?
					foreach($data_row as $row){
						?>
						<td><?= $row[csf('details_remarks')]; ?></td>
						<?	
					}
					?>
				</tr>
			</table>
			<br>
		   <?
		} 
		?>
		<p style="page-break-after:always;"></p>
		<? 
	} 
	?>
 
<? 

foreach (glob("$user_id*.xls") as $filename)
{
	if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
}
//---------end------------//

$name=time();
$filename =$user_id."_".$name.".xls";
$create_new_doc = fopen($filename, 'w');

$is_created = fwrite($create_new_doc,ob_get_contents());
 

	echo "**$filename**$rpt_type";
	disconnect($con);


	unset($dataArr);
	unset($po_size_arr);
	exit();
 
} */
if($action=="report_generate_8")
{ 

	extract($_REQUEST);
	$cbo_company_name=str_replace("'", "", $cbo_company_name);
	$cbo_working_company_name=str_replace("'", "", $cbo_working_company_name);
	$txt_job_id=str_replace("'", "", $txt_job_id);
	$cbo_buyer=str_replace("'", "", $cbo_buyer);
	$cbo_brand_name=str_replace("'", "", $cbo_brand_name);
	$txt_job=str_replace("'", "", $txt_job);
	$txt_order_id=str_replace("'", "", $txt_order_id);
	$txt_style=str_replace("'", "", $txt_style);
	$cbo_gauge=str_replace("'", "", $cbo_gauge);
	$cbo_date_type=str_replace("'", "", $cbo_date_type);

	$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_name_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$lib_size=return_library_array( "select size_name, id from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$lib_color=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$brand_name_arr=return_library_array( "select id, brand_name from lib_buyer_brand  where status_active=1 and is_deleted=0",'id','brand_name');
	$season_name_arr=return_library_array( "select id,season_name from   lib_buyer_season  where status_active=1 and is_deleted=0",'id','season_name');
	$supplier_arr=return_library_array( "select id,supplier_name from   lib_supplier  where status_active=1 and is_deleted=0",'id','supplier_name');
	//lib_supplier

	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
		$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
 
		if($cbo_date_type==1){
			$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
			$field ="b.PUB_SHIPMENT_DATE";
		}
		else if($cbo_date_type==2){
			$date_cond=" and b.PO_RECEIVED_DATE between '$start_date' and '$end_date'";
			$field ="b.PO_RECEIVED_DATE";
		}
	}
	else $field ="b.PUB_SHIPMENT_DATE";

	if ($cbo_buyer == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "") {
				 $whereCon .= " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			} else {
				$whereCon .= "";
			}
		} else {
			$whereCon .= "";
		}
	} else {
		 $whereCon .= " and a.buyer_name in ($cbo_buyer) ";
	}

	if($txt_job_id!=''){
		$whereCon.="and a.id in($txt_job_id)";
	}
	if($txt_order_id!=''){
		$whereCon.="and b.id in($txt_order_id)";
	}
	if($txt_job!=''){
		$whereCon.="and a.JOB_NO_PREFIX_NUM in($txt_job)";
	}
	if($txt_style!=''){
		$whereCon.="and a.STYLE_REF_NO in('".str_replace(",","','",$txt_style)."')";
	}
	
	if($cbo_buyer!=0){
		$whereCon.="and a.BUYER_NAME in($cbo_buyer)";
	}
	if($cbo_gauge!=''){
		$whereCon.="and a.GAUGE in($cbo_gauge)";
	}
	if($cbo_working_company_name!=''){
		$whereCon.="and a.style_owner in($cbo_working_company_name)";
	}

	$brand_cond="";
	if($cbo_brand_name !=''){
		$brand_cond = "and a.brand_id in ($cbo_brand_name)";
	}
  

	if($reportType==1){$confirmCon =" and b.IS_CONFIRMED=1";}
	else if($reportType==2){$confirmCon =" and b.IS_CONFIRMED=2";}
	else if($reportType==5){$confirmCon =" and b.IS_CONFIRMED=1";}
	else{$confirmCon ="";}

	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."  and ENTRY_FORM=148");

	$reportSql = "SELECT a.id as JOBID,b.id as PO_ID, $field as SHIPMENT_DATE, b.PO_NUMBER, b.UNIT_PRICE, b.details_remarks, b.PO_QUANTITY, b.pub_shipment_date, b.PO_RECEIVED_DATE,b.IS_CONFIRMED, b.DETAILS_REMARKS,b.SHIPING_STATUS,a.FABRIC_COMPOSITION, a.COMPANY_NAME, a.JOB_NO, a.GARMENTS_NATURE, a.STYLE_REF_NO, a.BUYER_NAME, a.GAUGE,
	a.AVG_UNIT_PRICE ,a.STYLE_DESCRIPTION, a.brand_id, a.SET_SMV,a.SET_BREAK_DOWN,a.YARN_QUALITY, a.SHIP_MODE, a.STYLE_OWNER, a.TOTAL_SET_QNTY,a.SEASON_BUYER_WISE, a.YARN_AVG_RATE, a.KNITTING_PATTERN, a.JOB_NO_PREFIX_NUM from WO_PO_DETAILS_MASTER a, WO_PO_BREAK_DOWN b where a.id=b.job_id and a.is_deleted=0 and b.is_deleted=0 $date_cond $whereCon and a.COMPANY_NAME in ($cbo_company_name) order By $field"; 
	 
    $reportSqlRes=sql_select($reportSql);

	$tmp_po_arr = array();
	$dataArr = array();
	$ship_date_wise_po_arr = array();
	$targetBr =2;
	$flag=0;
	foreach($reportSqlRes as $rows){
		if(!isset($dataArr[$rows['SHIPMENT_DATE']]))
		{
			$key=0;
			$flag = 0;
		}
		//echo $rows['SHIPMENT_DATE']."=".$flag."=".$targetBr."=".$key."<br />";
		$dataArr[$rows['SHIPMENT_DATE']][$key][$rows['PO_ID']] 			= $rows;
		$ship_date_wise_po_arr[$rows['SHIPMENT_DATE']][$rows['PO_ID']]	= $rows['PO_ID'];
		$tmp_po_arr[$rows['PO_ID']] 									= $rows['PO_ID'];
		$tmp_jobid_arr[$rows['JOBID']] 									= $rows['JOBID'];
		
		if($flag == $targetBr){$key++;$flag = 0;}else{$flag++;}
	} 
	// echo "<pre>";
	// print_r($dataArr);
	// echo "</pre>";
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 148, 1, $tmp_po_arr, $empty_arr);//PO ID Ref from=1
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 148, 2, $tmp_jobid_arr, $empty_arr);//JOb ID Ref from=2

	$size_sql= "SELECT a.PO_BREAK_DOWN_ID,a.SIZE_NUMBER_ID, a.COLOR_NUMBER_ID, (a.order_quantity) as ORDER_QUANTITY
	            from wo_po_color_size_breakdown a ,gbl_temp_engine g
				where  a.po_break_down_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1   and g.entry_form=148 and a.is_deleted=0 and a.status_active=1  order by SIZE_NUMBER_ID asc";
	$size_sql_res=sql_select($size_sql);
	unset($tmp_po_arr);

	$po_color_size_raiot_arr = array();
	foreach($size_sql_res as $rows){
		$po_color_size_raiot_arr[$rows['PO_BREAK_DOWN_ID']][$rows['COLOR_NUMBER_ID']][$rows['SIZE_NUMBER_ID']]+= $rows['ORDER_QUANTITY'];
		$po_size_arr[$rows['PO_BREAK_DOWN_ID']][$rows['SIZE_NUMBER_ID']] += $rows['ORDER_QUANTITY'];
	}
	unset($size_sql_res);

	$ship_date_wise_qty_arr=array();
	foreach($ship_date_wise_po_arr as $ship_date => $poRow){
		foreach($poRow as $po_id){
			$ship_date_wise_qty_arr[$ship_date] += array_sum($po_size_arr[$po_id]);
		}
	}
	//sample_development_fabric_acc
	$sql_yarn_wo=sql_select("select a.SUPPLIER_ID ,b.JOB_NO from wo_non_order_info_dtls b,wo_non_order_info_mst a,gbl_temp_engine g where a.id=b.mst_id and  b.job_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=2   and g.entry_form=148 and a.status_active=1 and b.status_active=1");
	foreach($sql_yarn_wo as $rows){
		$yarn_supplier_arr[$rows['JOB_NO']].= $supplier_arr[$rows['SUPPLIER_ID']].',';
	}
	unset($sql_yarn_wo);
	$sql_time_wgt_wo=sql_select("select b.id as DTL_ID,b.KNITINGGM ,a.STYLE_REF_NO from sample_development_fabric_acc b,
	sample_development_mst a,wo_po_details_master c,gbl_temp_engine g where a.id=b.sample_mst_id and a.ENTRY_FORM_ID = 245
	 and a.style_ref_no=c.style_ref_no and  c.id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=2 and b.KNITINGGM>0  
	  and g.entry_form=148 and a.status_active=1 and b.status_active=1");
	   
	foreach($sql_time_wgt_wo as $rows){

		if($jobTimeWgtArr[$rows['DTL_ID']]=='')
		{
			$gmts_weight_arr[$rows['STYLE_REF_NO']]['gmts_weight']+= $rows['KNITINGGM'];
			$jobTimeWgtArr[$rows['DTL_ID']]=$rows['DTL_ID'];
		}
		
	}
	unset($sql_time_wgt_wo);
	
$image_arr = return_library_array( "select a.MASTER_TBLE_ID,a.IMAGE_LOCATION from COMMON_PHOTO_LIBRARY a,WO_PO_DETAILS_MASTER b,gbl_temp_engine g where a.MASTER_TBLE_ID=b.job_no and  b.id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=2   and g.entry_form=148 and a.FORM_NAME='knit_order_entry' and a.FILE_TYPE=1",'MASTER_TBLE_ID','IMAGE_LOCATION');

$con = connect();
execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and   ENTRY_FORM=148");
oci_commit($con);
disconnect($con); 

$count_date_row= count($dataArr);
$kk=1;

?>
<style>
	
	.pagebreak {
		clear: both;
		page-break-after: always;
	}
	
</style>
<?
$html="";
	foreach($dataArr as $ship_date => $customRow){
		$bgcolor="#B1E6FC";
		$html.="<div><h3 align='left' style='background-color:$bgcolor;font-family:Arial Narrow;margin-bottom:0;font-size:11px;border:1px solid #000000; border-bottom:0;'> <b>".str_replace(['b.','_'],' ',$field)."; DATE :</b>".$ship_date."<b>".str_replace(['b.','_'],' ',$field)."; DATE Total:</b>".$ship_date_wise_qty_arr[$ship_date]."</h3></div>";
		$flag_td=1;
		if(!empty($customRow))
		{
			foreach($customRow as $data_row){
				if(count($data_row)==3)
				{
					$tdwidth="30%";
				}
				if(count($data_row)==2)
				{
					$tdwidth="45%";
				}
				if(count($data_row)==1)
				{
					$tdwidth="90%";
				}

				$html.="<table cellspacing='0' width='100%' cellpadding='1' border='1' rules='all'  style='vertical-align:top;border-collapse:collapse;font-family:Arial Narrow;font-size:10px;' class='rpt_table'>	
				<tr style='height: 120px;'> 
				<td align='center'><strong>Particulars</strong></td>";
				foreach($data_row as $row){
					$job_no=$row['JOB_NO'];
					$img_pic=base_url($image_arr[$job_no]);
					$html.="<td align='center'><img  src='".$img_pic."' height='100'  width='100'  alt='No Image' /></td>";
				}
				$html.="</tr><tr><td width='10%'><strong>Style</strong></td>";

				foreach($data_row as $row){
					$html.="<td width='".$tdwidth."'>".$row['STYLE_REF_NO']."</td>";
				}
				$html.="</tr><tr><td><strong>Order No</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$row['PO_NUMBER']."</td>";
				}
				$html.="</tr><tr><td><strong>Job No</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$row['JOB_NO']."</td>";
				}
				$html.="</tr><tr><td><strong>Session</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$season_name_arr[$row['SEASON_BUYER_WISE']]."</td>";
				}
				$html.="</tr><tr><td><strong>Buyer</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$buyer_name_arr[$row['BUYER_NAME']]."</td>";
				}
				$html.="</tr><tr><td><strong>Brand</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$brand_name_arr[$row['BRAND_ID']]."</td>";
				}
				$html.="</tr><tr><td><strong>Style Des.</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$row['STYLE_DESCRIPTION']."</td>";
				}
				$html.="</tr><tr><td><strong>Knitting Pattern</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$knitting_pattern_arr[$row[('KNITTING_PATTERN')]]."</td>";
				}
				$html.="</tr><tr><td><strong>Fabrication</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$row['YARN_QUALITY']."</td>";
				}
				$html.="</tr><tr><td><strong>Gauge</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$gauge_arr[$row['GAUGE']]."</td>";
				}
				$html.="</tr><tr><td><strong>L.Yarn Supplier</strong></td>";
				foreach($data_row as $row){
					$yarn_suppliers=rtrim($yarn_supplier_arr[$row['JOB_NO']],',');
					$yarn_supplierArr=implode(",",array_unique(explode(",",$yarn_suppliers)));
					$html.="<td>".$yarn_supplierArr."</td>";
				}
				$html.="</tr><tr><td><strong>Gmts Weight</strong></td>";
				foreach($data_row as $row){ 
					$html.="<td>".number_format($gmts_weight_arr[$row['STYLE_REF_NO']]['gmts_weight'],2)."</td>";
				}
				$html.="</tr><tr><td><strong>Price/Unit</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$row['UNIT_PRICE']."</td>";	
				}
				$html.="</tr><tr><td><strong>Quantity</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$row['PO_QUANTITY']."</td>";	
				}
				$html.="</tr><tr><td><strong>F. Shipment Date</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$row['SHIPMENT_DATE']."</td>";						 
				}
				$html.="</tr><tr><td><strong>Shipment Mode</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$shipment_mode[$row['SHIP_MODE']]."</td>";		
				}
				$html.="</tr><tr><td><strong>Accessories</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".rtrim($job_wise_data[$row['JOB_NO']]['accessories'],",")."</td>";		
				}
				$html.="</tr><tr><td><strong>Knitting Time</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$row['SET_SMV']."</td>";	
				}
				$html.="</tr><tr><td><strong>P2P Time</strong></td>";
				foreach($data_row as $row){
					$setBrkDwn=$row['SET_BREAK_DOWN'];
					$set_breck_down=explode('__',$setBrkDwn);
					$p2p_time=0;
					foreach($set_breck_down as $set_data)
					{
						$ex_set_data=explode('_',$set_data);
						$ex_item_id=$ex_set_data[0];
						$ex_item_ratio=$ex_set_data[1];
						$p2p_time=$ex_set_data[6]+$ex_set_data[8]+$ex_set_data[20];
					}
					$html.="<td>".$p2p_time."</td>";	
				}
				$html.="</tr><tr><td><strong>Dev./Proto Sample</strong></td>";
				foreach($data_row as $row){
					$html.="<td>Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][35]['submitted_date'].";Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][35]['approval_date']."</td>";	
				}
				$html.="</tr><tr><td><strong>F.sample/Ref Tag</strong></td>";
				foreach($data_row as $row){
					$html.="<td>Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][36]['submitted_date'].";Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][36]['approval_date']."</td>";	
				}
				$html.="</tr><tr><td><strong>PP/Sealer/G.Sample</strong></td>";
				foreach($data_row as $row){
					$html.="<td>Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][37]['submitted_date'].";Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][37]['approval_date']."</td>";	
				}
				$html.="</tr><tr><td><strong>Photo Sample</strong></td>";
				foreach($data_row as $row){
					$html.="<td>Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][4]['submitted_date'].";Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][4]['approval_date']."</td>";	
				}
				$html.="</tr><tr><td><strong>Lab Test/L.Sample</strong></td>";
				foreach($data_row as $row){
					$html.="<td>Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][38]['submitted_date'].";Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][38]['approval_date']."</td>";	
				}
				$html.="</tr><tr><td><strong>Shipment Sample</strong></td>";
				foreach($data_row as $row){
					$html.="<td>Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][6]['submitted_date'].";Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][6]['approval_date']."</td>";	
				}
				$html.="</tr><tr><td><strong>Production Factory</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$company_name_arr[$row['STYLE_OWNER']]."</td>";	
				}
				$html.="</tr><tr><td><strong>Color Size Ratio</strong></td>";
				foreach($data_row as $row){
					$html.="<td>";
					$html.="<table border='1' cellspacing='0' cellpadding='2' width='100%' style='border-collapse:collapse;'><thead><tr><th><strong>Color</strong></th>";
					foreach($po_size_arr[$row['PO_ID']] as $size_id => $sizeQty){
						$html.="<th align='center'>".$lib_size[$size_id]."</th>";
					}
					$html.="<th width='12%'>Color Total</th></tr></thead>";
					foreach($po_color_size_raiot_arr[$row['PO_ID']] as $color_id => $sizeRow){
						//$sizeCount = count($po_size_arr[$row['PO_ID']]);
						//$size_td_width = (100/$sizeCount);
						$html.="<tbody><tr><td>".$lib_color[$color_id]."</td>";
						foreach($po_size_arr[$row['PO_ID']] as $size_id => $sizeQty){
							$html.="<td align='center'>".$sizeRow[$size_id]."</td>";
						}
						$html.="<td align='center'>".array_sum($sizeRow)."</td></tr></tbody>";
					}
					$html.="<tfoot><tr><td><strong>G. Total</strong></td>";
					foreach($po_size_arr[$row['PO_ID']] as $size_id => $sizeQty){
						$html.="<td align='center'>".$sizeQty."</td>";
					}
					$html.="<td align='center'><strong>".array_sum($po_size_arr[$row['PO_ID']])."</strong></td></tr></tfoot></table></td>";
				}
				$html.="</tr><tr><td><strong>Avg.Yarn Price</strong></td>";
				foreach($data_row as $row){ 
					$html.="<td align='center'>".number_format($row['YARN_AVG_RATE'], 2)."</td>";
				}
				$html.="</tr><tr><td><strong>Remark</strong></td>";
				foreach($data_row as $row){
					$html.="<td align='center'>".$row['DETAILS_REMARKS']."</td>";
				}
				$html.="</tr></table>";
				
				if($kk>0)
				{
					if(!empty($data_row))
					{
						$html.="<div style='page-break-after:always'></div>";
					}
				}
				$kk++;	
			}
			
		}
		//echo $kk."==";
	} 
	//date end
 
//$reportBody=ob_get_contents();
//ob_end_clean();
$reportBody=trim($html);
$user_id=$_SESSION['logic_erp']['user_id'];
$report_cat=100;
foreach (glob("order*.xls") as $filename1)
{
	if( @filemtime($filename1) < (time()-$seconds_old) )
	@unlink($filename1);
}
 //echo $reportBody;
$name=time();
$filename1="order".$user_id."_".$name.".xls";
$create_new_doc = fopen($filename1, 'w');	
$is_created = fwrite($create_new_doc, $reportBody);

foreach (glob("../../../../auto_mail/tmp/order_recap_".$user_id.".pdf") as $filename1) {			
	@unlink($filename1);
}

if(!empty($reportBody))
{
	$att_file_arr=array();
	require('../../../../ext_resource/mpdf60/mpdf.php');
	$mpdf = new mPDF('', 'A4-L', '', '', 5, 5, 5, 5, 3, 3);	
	//A4-L
	$mpdf->SetHTMLFooter($report_signature);
	//$mpdf->autoPageBreak = true;
	//$mpdf->shrink_tables_to_fit = 1;
	$mpdf->WriteHTML($reportBody,2);
	//$mpdf->use_kwt = true;//
$user_id=$_SESSION['logic_erp']['user_id'];
$REAL_FILE_NAME = 'order_recap_'.$user_id.'.pdf';
$file_path='../../../../auto_mail/tmp/'.$REAL_FILE_NAME;
$mpdf->Output($file_path, 'F');
$att_file_arr[]='../../../../auto_mail/tmp/'.$REAL_FILE_NAME.'**'.$REAL_FILE_NAME;
}

//ob_clean();
if($is_mail_send==1){	

$sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=135 and  b.mail_user_setup_id=c.id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
$mail_sql_res=sql_select($sql);

$mailArr=array();
foreach($mail_sql_res as $row)
{
$mailArr[$row[EMAIL]]=$row[EMAIL]; 
}

$supplier_id=$nameArray[0][csf('supplier_id')];
$supplier_mail=return_field_value("email", "lib_supplier", "status_active=1 and is_deleted=0 and id=$supplier_id ");



if($mail_id!=''){$mailArr[]=$mail_id;}
if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail;}

$to=implode(',',$mailArr);
$subject="Fabric Booking Auto Mail";

if($to!=""){
require '../../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
require_once('../../../../auto_mail/setting/mail_setting.php');
$header=mailHeader();
echo sendMailMailer( $to, $subject, $emailBody,$from_mail,'' );
}
}
	unset($dataArr);
	unset($po_size_arr);
	exit();
 
}

if($action=="report_generate_8_____")
{ 

	extract($_REQUEST);
	$cbo_company_name=str_replace("'", "", $cbo_company_name);
	$cbo_working_company_name=str_replace("'", "", $cbo_working_company_name);
	$txt_job_id=str_replace("'", "", $txt_job_id);
	$cbo_buyer=str_replace("'", "", $cbo_buyer);
	$cbo_brand_name=str_replace("'", "", $cbo_brand_name);
	$txt_job=str_replace("'", "", $txt_job);
	$txt_order_id=str_replace("'", "", $txt_order_id);
	$txt_style=str_replace("'", "", $txt_style);
	$cbo_gauge=str_replace("'", "", $cbo_gauge);
	$cbo_date_type=str_replace("'", "", $cbo_date_type);

	$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_name_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$lib_size=return_library_array( "select size_name, id from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$lib_color=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");	 
	$brand_name_arr=return_library_array( "select id, brand_name from lib_buyer_brand  where status_active=1 and is_deleted=0",'id','brand_name');	 

	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
		$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
 
		if($cbo_date_type==1){
			$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
			$field ="b.PUB_SHIPMENT_DATE";
		}
		else if($cbo_date_type==2){
			$date_cond=" and b.PO_RECEIVED_DATE between '$start_date' and '$end_date'";
			$field ="b.PO_RECEIVED_DATE";
		}
	}
	else $field ="b.PUB_SHIPMENT_DATE";

	if ($cbo_buyer == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "") {
				 $whereCon .= " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			} else {
				$whereCon .= "";
			}
		} else {
			$whereCon .= "";
		}
	} else {
		 $whereCon .= " and a.buyer_name in ($cbo_buyer) ";
	}

	if($txt_job_id!=''){
		$whereCon.="and a.id in($txt_job_id)";
	}
	if($txt_order_id!=''){
		$whereCon.="and b.id in($txt_order_id)";
	}
	if($txt_job!=''){
		$whereCon.="and a.JOB_NO_PREFIX_NUM in($txt_job)";
	}
	if($txt_style!=''){
		$whereCon.="and a.STYLE_REF_NO in('".str_replace(",","','",$txt_style)."')";
	}
	
	if($cbo_buyer!=0){
		$whereCon.="and a.BUYER_NAME in($cbo_buyer)";
	}
	if($cbo_gauge!=''){
		$whereCon.="and a.GAUGE in($cbo_gauge)";
	}
	if($cbo_working_company_name!=''){
		$whereCon.="and a.style_owner in($cbo_working_company_name)";
	}

	$brand_cond="";
	if($cbo_brand_name !=''){
		$brand_cond = "and a.brand_id in ($cbo_brand_name)";
	}
  

	if($reportType==1){$confirmCon =" and b.IS_CONFIRMED=1";}
	else if($reportType==2){$confirmCon =" and b.IS_CONFIRMED=2";}
	else if($reportType==5){$confirmCon =" and b.IS_CONFIRMED=1";}
	else{$confirmCon ="";}

	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."  and ENTRY_FORM=148");


	 $reportSql = "SELECT a.id as JOBID,b.id as PO_ID, $field as SHIPMENT_DATE, b.PO_NUMBER, b.UNIT_PRICE, b.details_remarks, b.PO_QUANTITY, b.pub_shipment_date, b.PO_RECEIVED_DATE,b.IS_CONFIRMED, b.SHIPING_STATUS,a.FABRIC_COMPOSITION, a.COMPANY_NAME, a.JOB_NO, a.GARMENTS_NATURE, a.STYLE_REF_NO, a.BUYER_NAME, a.GAUGE,
	a.AVG_UNIT_PRICE ,a.STYLE_DESCRIPTION, a.brand_id, a.YARN_QUALITY, a.SHIP_MODE, a.STYLE_OWNER, a.TOTAL_SET_QNTY,a.SEASON_BUYER_WISE, a.yarn_avg_rate, a.knitting_pattern, a.JOB_NO_PREFIX_NUM from WO_PO_DETAILS_MASTER a, WO_PO_BREAK_DOWN b where a.id=b.job_id and a.is_deleted=0 and b.is_deleted=0 $date_cond $whereCon and a.COMPANY_NAME in ($cbo_company_name) order By $field"; 
	 
    $reportSqlRes=sql_select($reportSql);

	$tmp_po_arr = array();
	$dataArr = array();
	$ship_date_wise_po_arr = array();
	$targetBr =3;
	foreach($reportSqlRes as $rows){
		$dataArr[$rows['SHIPMENT_DATE']][$key][$rows['PO_ID']] = $rows;
		$ship_date_wise_po_arr[$rows['SHIPMENT_DATE']][$rows['PO_ID']]= $rows['PO_ID'];
		$tmp_po_arr[$rows['PO_ID']] = $rows['PO_ID'];
		$tmp_jobid_arr[$rows['JOBID']] = $rows['JOBID'];
		 
		$flag++;
		if($flag == $targetBr){$key++;$flag = 0;}
	} 
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 148, 1, $tmp_po_arr, $empty_arr);//PO ID Ref from=1
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 148, 2, $tmp_jobid_arr, $empty_arr);//JOb ID Ref from=2

	$size_sql= "SELECT a.PO_BREAK_DOWN_ID,a.SIZE_NUMBER_ID, a.COLOR_NUMBER_ID, (a.order_quantity) as ORDER_QUANTITY
	            from wo_po_color_size_breakdown a ,gbl_temp_engine g
				where  a.po_break_down_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1   and g.entry_form=148 and a.is_deleted=0 and a.status_active=1  order by SIZE_NUMBER_ID asc";
	$size_sql_res=sql_select($size_sql);
	unset($tmp_po_arr);

	$po_color_size_raiot_arr = array();
	foreach($size_sql_res as $rows){
		$po_color_size_raiot_arr[$rows['PO_BREAK_DOWN_ID']][$rows['COLOR_NUMBER_ID']][$rows['SIZE_NUMBER_ID']]+= $rows['ORDER_QUANTITY'];
		$po_size_arr[$rows['PO_BREAK_DOWN_ID']][$rows['SIZE_NUMBER_ID']] += $rows['ORDER_QUANTITY'];
	}
	unset($size_sql_res);

	$ship_date_wise_qty_arr=array();
	foreach($ship_date_wise_po_arr as $ship_date => $poRow){
		foreach($poRow as $po_id){
			$ship_date_wise_qty_arr[$ship_date] += array_sum($po_size_arr[$po_id]);
		}
	}
	
	$image_arr = return_library_array( "select a.MASTER_TBLE_ID,a.IMAGE_LOCATION from COMMON_PHOTO_LIBRARY a,WO_PO_DETAILS_MASTER b,gbl_temp_engine g where a.MASTER_TBLE_ID=b.job_no and  b.id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=2   and g.entry_form=148 and a.FORM_NAME='knit_order_entry' and a.FILE_TYPE=1",'MASTER_TBLE_ID','IMAGE_LOCATION');

$con = connect();
execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and   ENTRY_FORM=148");
oci_commit($con);
disconnect($con); 

$count_date_row= count($dataArr);
$kk=1;

?>
<style>
	
	.pagebreak {
		clear: both;
		page-break-after: always;
	}
	
</style>
<?
$html="";
	foreach($dataArr as $ship_date => $customRow){
		$bgcolor="#B1E6FC";
		$html.="<div><h3 align='left' style='background-color:$bgcolor;font-family:Arial Narrow'> <b>".str_replace(['b.','_'],' ',$field)."; DATE :</b>".$ship_date."<b>".str_replace(['b.','_'],' ',$field)."; DATE Total:</b>".$ship_date_wise_qty_arr[$ship_date]."</h3></div>";
		$flag_td=1;
		if(!empty($customRow))
		{
			foreach($customRow as $data_row){
				$html.="<table cellspacing='0' cellpadding='1' border='1' rules='all'  style='vertical-align:top;border-collapse:collapse;font-family:Arial Narrow;' class='rpt_table'>	
				<tr style='height: 120px;'> 
				<td align='center'><strong>Particulars</strong></td>";
				foreach($data_row as $row){
					$html.="<td align='center'><img  src='base_url(".$image_arr[$row['JOB_NO']].");' height='100'  width='100'  alt='No Image' /></td>";
				}
				$html.="</tr><tr><td ><strong>Style</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$row['STYLE_REF_NO']."</td>";
				}
				$html.="</tr><tr><td><strong>Order No</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$row['PO_NUMBER']."</td>";
				}
				$html.="</tr><tr><td><strong>Job No</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$row['JOB_NO']."</td>";
				}
				$html.="</tr><tr><td><strong>Session</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$row['SEASON_BUYER_WISE']."</td>";
				}
				$html.="</tr><tr><td><strong>Buyer</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$buyer_name_arr[$row['BUYER_NAME']]."</td>";
				}
				$html.="</tr><tr><td><strong>Brand</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$brand_name_arr[$row['BRAND_ID']]."</td>";
				}
				$html.="</tr><tr><td><strong>Style Des.</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$row['STYLE_DESCRIPTION']."</td>";
				}
				$html.="</tr><tr><td><strong>Knitting Pattern</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$knitting_pattern_arr[$row[csf('knitting_pattern')]]."</td>";
				}
				$html.="</tr><tr><td><strong>Fabrication</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$row['YARN_QUALITY']."</td>";
				}
				$html.="</tr><tr><td><strong>Gauge</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$gauge_arr[$row['GAUGE']]."</td>";
				}
				$html.="</tr><tr><td><strong>L.Yarn Supplier</strong></td>";
				foreach($data_row as $row){
					$html.="<td>&nbsp;</td>";
				}
				$html.="</tr><tr><td><strong>Gmts Weight</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$short_quat_arr[$style]['gmts_weight']."</td>";
				}
				$html.="</tr><tr><td><strong>Price/Unit</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$row['UNIT_PRICE']."</td>";	
				}
				$html.="</tr><tr><td><strong>Quantity</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$row['PO_QUANTITY']."</td>";	
				}
				$html.="</tr><tr><td><strong>F. Shipment Date</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$row['SHIPMENT_DATE']."</td>";						 
				}
				$html.="</tr><tr><td><strong>Shipment Mode</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$shipment_mode[$row['SHIP_MODE']]."</td>";		
				}
				$html.="</tr><tr><td><strong>Accessories</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".rtrim($job_wise_data[$row['JOB_NO']]['accessories'],",")."</td>";		
				}
				$html.="</tr><tr><td><strong>Knitting Time</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$short_quat_arr[$style]['knitting_time']."</td>";	
				}
				$html.="</tr><tr><td><strong>P2P Time</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$short_quat_arr[$style]['makeup_time']."</td>";	
				}
				$html.="</tr><tr><td><strong>Dev./Proto Sample</strong></td>";
				foreach($data_row as $row){
					$html.="<td>Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][35]['submitted_date'].";Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][35]['approval_date']."</td>";	
				}
				$html.="</tr><tr><td><strong>F.sample/Ref Tag</strong></td>";
				foreach($data_row as $row){
					$html.="<td>Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][36]['submitted_date'].";Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][36]['approval_date']."</td>";	
				}
				$html.="</tr><tr><td><strong>PP/Sealer/G.Sample</strong></td>";
				foreach($data_row as $row){
					$html.="<td>Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][37]['submitted_date'].";Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][37]['approval_date']."</td>";	
				}
				$html.="</tr><tr><td><strong>Photo Sample</strong></td>";
				foreach($data_row as $row){
					$html.="<td>Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][4]['submitted_date'].";Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][4]['approval_date']."</td>";	
				}
				$html.="</tr><tr><td><strong>Lab Test/L.Sample</strong></td>";
				foreach($data_row as $row){
					$html.="<td>Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][38]['submitted_date'].";Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][38]['approval_date']."</td>";	
				}
				$html.="</tr><tr><td><strong>Shipment Sample</strong></td>";
				foreach($data_row as $row){
					$html.="<td>Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][6]['submitted_date'].";Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][6]['approval_date']."</td>";	
				}
				$html.="</tr><tr><td><strong>Production Factory</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$company_name_arr[$rows['STYLE_OWNER']]."</td>";	
				}
				$html.="</tr><tr><td><strong>Color Size Ratio</strong></td>";
				foreach($data_row as $row){
					$html.="<td>";
					$html.="<table border='1' cellspacing='0' cellpadding='2' width='100%' style='border-collapse:collapse;'><thead><tr><th><strong>Color</strong></th>";
					foreach($po_size_arr[$row['PO_ID']] as $size_id => $sizeQty){
						$html.="<th align='center'>{".$lib_size[$size_id]."}</th>";
					}
					$html.="<th>Color Total</th></tr></thead>";
					foreach($po_color_size_raiot_arr[$row['PO_ID']] as $color_id => $sizeRow){
						$html.="<tbody><tr><td>$lib_color[$color_id]</td>";
						foreach($sizeRow as $size_id => $ratioQty){
							$html.="<td align='center'>{".$ratioQty."}</td>";
						}
						$html.="<td align='center'>".array_sum($sizeRow)."</td></tr></tbody>";
					}
					$html.="<tfoot><tr><td><strong>G. Total</strong></td>";
					foreach($po_size_arr[$row['PO_ID']] as $size_id => $sizeQty){
						$html.="<td align='center'>".$sizeQty."</td>";
					}
					$html.="<td align='center'><strong>".array_sum($po_size_arr[$row['PO_ID']])."</strong></td></tr></tfoot></table></td>";
				}
				$html.="</tr><tr><td><strong>Avg.Yarn Price</strong></td>";
				foreach($data_row as $row){
					$html.="<td align='center'>".number_format($short_quat_arr[$style]['yarn_avg_rate'], 2)."</td>";
				}
				$html.="</tr><tr><td><strong>Remark</strong></td>";
				foreach($data_row as $row){
					$html.="<td align='center'>".$row[('details_remarks')]."</td>";
				}
				$html.="</tr></table>";
				
				if($kk>0)
				{
					if(!empty($data_row))
					{
						$html.="<div style='page-break-after:always'></div>";
					}
				}
				$kk++;	
			}
			
		}
		//echo $kk."==";
	} 
	//date end
 
//$reportBody=ob_get_contents();
//ob_end_clean();
$reportBody=trim($html);
$user_id=$_SESSION['logic_erp']['user_id'];
$report_cat=100;
foreach (glob("order*.xls") as $filename1)
{
	if( @filemtime($filename1) < (time()-$seconds_old) )
	@unlink($filename1);
}
 //echo $reportBody;
$name=time();
$filename1="order".$user_id."_".$name.".xls";
$create_new_doc = fopen($filename1, 'w');	
$is_created = fwrite($create_new_doc, $reportBody);

foreach (glob("../../../../auto_mail/tmp/order_recap_".$user_id.".pdf") as $filename1) {			
	@unlink($filename1);
}

if(!empty($reportBody))
{
	$att_file_arr=array();
	require('../../../../ext_resource/mpdf60/mpdf.php');
	$mpdf = new mPDF('', 'A4-L', '', '', 10, 10, 10, 15, 3, 3);	
	//A4-L
	$mpdf->SetHTMLFooter($report_signature);
	//$mpdf->autoPageBreak = true;
	//$mpdf->shrink_tables_to_fit = 1;
	$mpdf->WriteHTML($reportBody,2);
	//$mpdf->use_kwt = true;//
$user_id=$_SESSION['logic_erp']['user_id'];
$REAL_FILE_NAME = 'order_recap_'.$user_id.'.pdf';
$file_path='../../../../auto_mail/tmp/'.$REAL_FILE_NAME;
$mpdf->Output($file_path, 'F');
$att_file_arr[]='../../../../auto_mail/tmp/'.$REAL_FILE_NAME.'**'.$REAL_FILE_NAME;
}

//ob_clean();
if($is_mail_send==1){	

$sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=135 and  b.mail_user_setup_id=c.id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
$mail_sql_res=sql_select($sql);

$mailArr=array();
foreach($mail_sql_res as $row)
{
$mailArr[$row[EMAIL]]=$row[EMAIL]; 
}

$supplier_id=$nameArray[0][csf('supplier_id')];
$supplier_mail=return_field_value("email", "lib_supplier", "status_active=1 and is_deleted=0 and id=$supplier_id ");



if($mail_id!=''){$mailArr[]=$mail_id;}
if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail;}

$to=implode(',',$mailArr);
$subject="Fabric Booking Auto Mail";

if($to!=""){
require '../../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
require_once('../../../../auto_mail/setting/mail_setting.php');
$header=mailHeader();
echo sendMailMailer( $to, $subject, $emailBody,$from_mail,'' );
}
}
	unset($dataArr);
	unset($po_size_arr);
	exit();
 
}

if($action=="report_generate_8--14")
{ 

	extract($_REQUEST);
	$cbo_company_name=str_replace("'", "", $cbo_company_name);
	$cbo_working_company_name=str_replace("'", "", $cbo_working_company_name);
	$txt_job_id=str_replace("'", "", $txt_job_id);
	$cbo_buyer=str_replace("'", "", $cbo_buyer);
	$cbo_brand_name=str_replace("'", "", $cbo_brand_name);
	$txt_job=str_replace("'", "", $txt_job);
	$txt_order_id=str_replace("'", "", $txt_order_id);
	$txt_style=str_replace("'", "", $txt_style);
	$cbo_gauge=str_replace("'", "", $cbo_gauge);
	$cbo_date_type=str_replace("'", "", $cbo_date_type);

	$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_name_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$lib_size=return_library_array( "select size_name, id from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$lib_color=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");	 
	$brand_name_arr=return_library_array( "select id, brand_name from lib_buyer_brand  where status_active=1 and is_deleted=0",'id','brand_name');	 

	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
		$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
 
		if($cbo_date_type==1){
			$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
			$field ="b.PUB_SHIPMENT_DATE";
		}
		else if($cbo_date_type==2){
			$date_cond=" and b.PO_RECEIVED_DATE between '$start_date' and '$end_date'";
			$field ="b.PO_RECEIVED_DATE";
		}
	}
	else $field ="b.PUB_SHIPMENT_DATE";

	if ($cbo_buyer == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "") {
				 $whereCon .= " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			} else {
				$whereCon .= "";
			}
		} else {
			$whereCon .= "";
		}
	} else {
		 $whereCon .= " and a.buyer_name in ($cbo_buyer) ";
	}

	if($txt_job_id!=''){
		$whereCon.="and a.id in($txt_job_id)";
	}
	if($txt_order_id!=''){
		$whereCon.="and b.id in($txt_order_id)";
	}
	if($txt_job!=''){
		$whereCon.="and a.JOB_NO_PREFIX_NUM in($txt_job)";
	}
	if($txt_style!=''){
		$whereCon.="and a.STYLE_REF_NO in('".str_replace(",","','",$txt_style)."')";
	}
	
	if($cbo_buyer!=0){
		$whereCon.="and a.BUYER_NAME in($cbo_buyer)";
	}
	if($cbo_gauge!=''){
		$whereCon.="and a.GAUGE in($cbo_gauge)";
	}
	if($cbo_working_company_name!=''){
		$whereCon.="and a.style_owner in($cbo_working_company_name)";
	}

	$brand_cond="";
	if($cbo_brand_name !=''){
		$brand_cond = "and a.brand_id in ($cbo_brand_name)";
	}
  

	if($reportType==1){$confirmCon =" and b.IS_CONFIRMED=1";}
	else if($reportType==2){$confirmCon =" and b.IS_CONFIRMED=2";}
	else if($reportType==5){$confirmCon =" and b.IS_CONFIRMED=1";}
	else{$confirmCon ="";}

	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."  and ENTRY_FORM=148");


	 $reportSql = "SELECT a.id as JOBID,b.id as PO_ID, $field as SHIPMENT_DATE, b.PO_NUMBER, b.UNIT_PRICE, b.details_remarks, b.PO_QUANTITY, b.pub_shipment_date, b.PO_RECEIVED_DATE,b.IS_CONFIRMED, b.SHIPING_STATUS,a.FABRIC_COMPOSITION, a.COMPANY_NAME, a.JOB_NO, a.GARMENTS_NATURE, a.STYLE_REF_NO, a.BUYER_NAME, a.GAUGE,
	a.AVG_UNIT_PRICE ,a.STYLE_DESCRIPTION, a.brand_id, a.YARN_QUALITY, a.SHIP_MODE, a.STYLE_OWNER, a.TOTAL_SET_QNTY,a.SEASON_BUYER_WISE, a.yarn_avg_rate, a.knitting_pattern, a.JOB_NO_PREFIX_NUM from WO_PO_DETAILS_MASTER a, WO_PO_BREAK_DOWN b where a.id=b.job_id and a.is_deleted=0 and b.is_deleted=0 $date_cond $whereCon and a.COMPANY_NAME in ($cbo_company_name) order By $field";
	 
    $reportSqlRes=sql_select($reportSql);

	$tmp_po_arr = array();
	$dataArr = array();
	$ship_date_wise_po_arr = array();
	$targetBr =3;
	foreach($reportSqlRes as $rows){
		$dataArr[$rows['SHIPMENT_DATE']][$key][$rows['PO_ID']] = $rows;
		$ship_date_wise_po_arr[$rows['SHIPMENT_DATE']][$rows['PO_ID']]= $rows['PO_ID'];
		$tmp_po_arr[$rows['PO_ID']] = $rows['PO_ID'];
		$tmp_jobid_arr[$rows['JOBID']] = $rows['JOBID'];
		 
		$flag++;
		if($flag == $targetBr){$key++;$flag = 0;}
	} 
	//print_r($dataArr);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 148, 1, $tmp_po_arr, $empty_arr);//PO ID Ref from=1
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 148, 2, $tmp_jobid_arr, $empty_arr);//JOb ID Ref from=2

	$size_sql= "SELECT a.PO_BREAK_DOWN_ID,a.SIZE_NUMBER_ID, a.COLOR_NUMBER_ID, (a.order_quantity) as ORDER_QUANTITY
	            from wo_po_color_size_breakdown a ,gbl_temp_engine g
				where  a.po_break_down_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1   and g.entry_form=148 and a.is_deleted=0 and a.status_active=1  order by SIZE_NUMBER_ID asc";
	$size_sql_res=sql_select($size_sql);
	unset($tmp_po_arr);

	$po_color_size_raiot_arr = array();
	foreach($size_sql_res as $rows){
		$po_color_size_raiot_arr[$rows['PO_BREAK_DOWN_ID']][$rows['COLOR_NUMBER_ID']][$rows['SIZE_NUMBER_ID']]+= $rows['ORDER_QUANTITY'];
		$po_size_arr[$rows['PO_BREAK_DOWN_ID']][$rows['SIZE_NUMBER_ID']] += $rows['ORDER_QUANTITY'];
	}
	unset($size_sql_res);

	$ship_date_wise_qty_arr=array();
	foreach($ship_date_wise_po_arr as $ship_date => $poRow){
		foreach($poRow as $po_id){
			$ship_date_wise_qty_arr[$ship_date] += array_sum($po_size_arr[$po_id]);
		}
	}
	
	$image_arr = return_library_array( "select a.MASTER_TBLE_ID,a.IMAGE_LOCATION from COMMON_PHOTO_LIBRARY a,WO_PO_DETAILS_MASTER b,gbl_temp_engine g where a.MASTER_TBLE_ID=b.job_no and  b.id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=2   and g.entry_form=148 and a.FORM_NAME='knit_order_entry' and a.FILE_TYPE=1",'MASTER_TBLE_ID','IMAGE_LOCATION');

$con = connect();
execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and   ENTRY_FORM=148");
oci_commit($con);
disconnect($con); 

$count_date_row= count($dataArr);
//echo $count_date_row.'=A';
$kk=1;

?>
<style>
	
	.pagebreak {
		clear: both;
		page-break-after: always;
	}
	
</style>
<?
$html="";
	foreach($dataArr as $ship_date => $customRow){
		$bgcolor="#B1E6FC";
		$html.="<div><h3 align='left' style='background-color:$bgcolor'> <b>".str_replace(['b.','_'],' ',$field)."; DATE :</b>".$ship_date."<b>".str_replace(['b.','_'],' ',$field)."; DATE Total:</b>".$ship_date_wise_qty_arr[$ship_date]."</h3></div>";
		$flag_td=1;
		foreach($customRow as $data_row){
			$html.="<table cellspacing='0' cellpadding='1' border='1' rules='all'  style='vertical-align:top;border-collapse:collapse;font-size:18px;' class='rpt_table'>	
			<tr style='height: 120px;'> 
			<td align='center'><strong>Particulars</strong></td>";
			foreach($data_row as $row){
				$html.="<td align='center'><img  src='base_url(".$image_arr[$row['JOB_NO']].");' height='100'  width='100'  alt='No Image' /></td>";
			}
			$html.="</tr><tr><td ><strong>Style</strong></td>";
			foreach($data_row as $row){
				$html.="<td>".$row['STYLE_REF_NO']."</td>";
			}
			$html.="</tr><tr><td><strong>Order No</strong></td>";
			foreach($data_row as $row){
				$html.="<td>".$row['PO_NUMBER']."</td>";
			}
			$html.="</tr><tr><td><strong>Job No</strong></td>";
			foreach($data_row as $row){
				$html.="<td>".$row['JOB_NO']."</td>";
			}
			$html.="</tr><tr><td><strong>Session</strong></td>";
			foreach($data_row as $row){
				$html.="<td>".$row['SEASON_BUYER_WISE']."</td>";
			}
			$html.="</tr><tr><td><strong>Buyer</strong></td>";
			foreach($data_row as $row){
				$html.="<td>".$buyer_name_arr[$row['BUYER_NAME']]."</td>";
			}
			$html.="</tr><tr><td><strong>Brand</strong></td>";
			foreach($data_row as $row){
				$html.="<td>".$brand_name_arr[$row['BRAND_ID']]."</td>";
			}
			$html.="</tr><tr><td><strong>Style Des.</strong></td>";
			foreach($data_row as $row){
				$html.="<td>".$row['STYLE_DESCRIPTION']."</td>";
			}
			$html.="</tr><tr><td><strong>Knitting Pattern</strong></td>";
			foreach($data_row as $row){
				$html.="<td>".$knitting_pattern_arr[$row[csf('knitting_pattern')]]."</td>";
			}
			$html.="</tr><tr><td><strong>Fabrication</strong></td>";
			foreach($data_row as $row){
				$html.="<td>".$row['YARN_QUALITY']."</td>";
			}
			$html.="</tr><tr><td><strong>Gauge</strong></td>";
			foreach($data_row as $row){
				$html.="<td>".$gauge_arr[$row['GAUGE']]."</td>";
			}
			$html.="</tr><tr><td><strong>L.Yarn Supplier</strong></td>";
			foreach($data_row as $row){
				$html.="<td> </td>";
			}
			$html.="</tr><tr><td><strong>Gmts Weight</strong></td>";
			foreach($data_row as $row){
				$html.="<td>".$short_quat_arr[$style]['gmts_weight']."</td>";
			}
			$html.="</tr><tr><td><strong>Price/Unit</strong></td>";
			foreach($data_row as $row){
				$html.="<td>".$row['UNIT_PRICE']."</td>";	
			}
			$html.="</tr><tr><td><strong>Quantity</strong></td>";
			foreach($data_row as $row){
				$html.="<td>".$row['PO_QUANTITY']."</td>";	
			}
			$html.="</tr><tr><td><strong>F. Shipment Date</strong></td>";
			foreach($data_row as $row){
				$html.="<td>".$row['SHIPMENT_DATE']."</td>";						 
			}
			$html.="</tr><tr><td><strong>Shipment Mode</strong></td>";
			foreach($data_row as $row){
				$html.="<td>".$shipment_mode[$row['SHIP_MODE']]."</td>";		
			}
			$html.="</tr><tr><td><strong>Accessories</strong></td>";
			foreach($data_row as $row){
				$html.="<td>".rtrim($job_wise_data[$row['JOB_NO']]['accessories'],",")."</td>";		
			}
			$html.="</tr><tr><td><strong>Knitting Time</strong></td>";
			foreach($data_row as $row){
				$html.="<td>".$short_quat_arr[$style]['knitting_time']."</td>";	
			}
			$html.="</tr><tr><td><strong>P2P Time</strong></td>";
			foreach($data_row as $row){
				$html.="<td>".$short_quat_arr[$style]['makeup_time']."</td>";	
			}
			$html.="</tr><tr><td><strong>Dev./Proto Sample</strong></td>";
			foreach($data_row as $row){
				$count_posize=count($po_size_arr[$row['PO_ID']]);
				if($count_posize>=4) $font_size='font-size:18px';else $font_size='';
				$html.="<td style='$font_size' >Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][35]['submitted_date'].";Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][35]['approval_date']."</td>";	
			}
			$html.="</tr><tr><td><strong>F.sample/Ref Tag</strong></td>";
			foreach($data_row as $row){
			//	$count_posize=count($po_size_arr[$row['PO_ID']]);
			//	if($count_posize>=4) $font_size='font-size:18px';else $font_size='';
				$html.="<td style='$font_size'>Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][36]['submitted_date'].";Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][36]['approval_date']."</td>";	
			}
			$html.="</tr><tr><td><strong>PP/Sealer/G.Sample</strong></td>";
			foreach($data_row as $row){
				$html.="<td>Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][37]['submitted_date'].";Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][37]['approval_date']."</td>";	
			}
			$html.="</tr><tr><td><strong>Photo Sample</strong></td>";
			foreach($data_row as $row){
				$html.="<td>Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][4]['submitted_date'].";Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][4]['approval_date']."</td>";	
			}
			$html.="</tr><tr>
				<td><strong>Lab Test/L.Sample</strong></td>";
			foreach($data_row as $row){
				$html.="<td>Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][38]['submitted_date'].";Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][38]['approval_date']."</td>";	
			}
			$html.="</tr><tr>
				<td><strong>Shipment Sample</strong></td>";
			foreach($data_row as $row){
				$html.="<td>Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][6]['submitted_date'].";Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][6]['approval_date']."</td>";	
			}
			$html.="</tr><tr>
				<td><strong>Production Factory</strong></td>";
			foreach($data_row as $row){
				$html.="<td>".$company_name_arr[$rows['STYLE_OWNER']]."</td>";	
			}
			$html.="</tr><tr>
				<td>Color Size Ratio</td>";
			foreach($data_row as $row){
			//	$count_posize=count($po_size_arr[$row['PO_ID']]);
			//	if($count_posize>=4) $font_size='font-size:19px';else $font_size='';
				$html.="<td>";
				$html.="<table border='1' cellspacing='0' cellpadding='3' width='100%' style='border-collapse:collapse;font-size:19px;'><thead><tr><th><strong>Color</strong></th>";
				foreach($po_size_arr[$row['PO_ID']] as $size_id => $sizeQty){
					$html.="<th align='center'>{".$lib_size[$size_id]."}</th>";
				}
				$html.="<th>Color Total</th></th></tr></thead>";
				foreach($po_color_size_raiot_arr[$row['PO_ID']] as $color_id => $sizeRow){
					$html.="<tbody><tr><td>$lib_color[$color_id]</td>";
					foreach($sizeRow as $size_id => $ratioQty){
						$html.="<td align='center'>{".$ratioQty."}</td>";
					}
					$html.="<td align='center'>".array_sum($sizeRow)."</td></tr></tbody>";
				}
				$html.="<tfoot><tr><td><strong>G. Total</strong></td>";
				foreach($po_size_arr[$row['PO_ID']] as $size_id => $sizeQty){
					$html.="<td align='center'>".$sizeQty."</td>";
				}
				$html.="<td align='center'><strong>".array_sum($po_size_arr[$row['PO_ID']])."</strong></td></tr></tfoot></table></td>";
			}
			$html.="</tr><tr><td><strong>Avg.Yarn Price</strong></td>";
			foreach($data_row as $row){
				$html.="<td align='center'>".number_format($short_quat_arr[$style]['yarn_avg_rate'], 2)."</td>";
			}
			$html.="</tr><tr><td><strong>Remark</strong></td>";
			foreach($data_row as $row){
				$html.="<td align='center'>".$row[('details_remarks')]."</td>";
			}
			$html.="</tr></table>";
			 
			if($kk<=$count_date_row && $count_date_row!=1)
			{
				$html.="<pagebreak>";
			}
			 
		}
	//	$html.="<div style='page-break-after:always'>&nbsp;</div>";
		
		$kk++;
	} 
	//date end
 
//$reportBody=ob_get_contents();
//ob_end_clean();
$reportBody=trim($html);
$user_id=$_SESSION['logic_erp']['user_id'];
$report_cat=100;
foreach (glob("order*.xls") as $filename1)
{
	if( @filemtime($filename1) < (time()-$seconds_old) )
	@unlink($filename1);
}
 //echo $reportBody;
$name=time();
$filename1="order".$user_id."_".$name.".xls";
$create_new_doc = fopen($filename1, 'w');	
$is_created = fwrite($create_new_doc, $reportBody);

foreach (glob("../../../../auto_mail/tmp/order_recap_".$user_id.".pdf") as $filename1) {			
	@unlink($filename1);
}

if(!empty($reportBody))
{
	$att_file_arr=array();
	require('../../../../ext_resource/mpdf60/mpdf.php');
	$mpdf = new mPDF('', 'A4-L', '', '', 10, 10, 10, 15, 3, 3);	
	//A4-L
	$mpdf->SetHTMLFooter($report_signature);
	$mpdf->autoPageBreak = true;
	$mpdf->shrink_tables_to_fit = 1;
	$mpdf->SetDisplayMode(90);
	$mpdf->WriteHTML($reportBody,2);
	
	//$mpdf->use_kwt = true;//
$user_id=$_SESSION['logic_erp']['user_id'];
$REAL_FILE_NAME = 'order_recap_'.$user_id.'.pdf';
$file_path='../../../../auto_mail/tmp/'.$REAL_FILE_NAME;
$mpdf->Output($file_path, 'F');
$att_file_arr[]='../../../../auto_mail/tmp/'.$REAL_FILE_NAME.'**'.$REAL_FILE_NAME;
}

//ob_clean();
if($is_mail_send==1){	

$sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=135 and  b.mail_user_setup_id=c.id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
$mail_sql_res=sql_select($sql);

$mailArr=array();
foreach($mail_sql_res as $row)
{
$mailArr[$row[EMAIL]]=$row[EMAIL]; 
}

$supplier_id=$nameArray[0][csf('supplier_id')];
$supplier_mail=return_field_value("email", "lib_supplier", "status_active=1 and is_deleted=0 and id=$supplier_id ");



if($mail_id!=''){$mailArr[]=$mail_id;}
if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail;}

$to=implode(',',$mailArr);
$subject="Fabric Booking Auto Mail";

if($to!=""){
require '../../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
require_once('../../../../auto_mail/setting/mail_setting.php');
$header=mailHeader();
echo sendMailMailer( $to, $subject, $emailBody,$from_mail,'' );
}
}
	unset($dataArr);
	unset($po_size_arr);
	exit();
 
}

if($action=="report_generate_8_old")
{ 

	extract($_REQUEST);
	$cbo_company_name=str_replace("'", "", $cbo_company_name);
	$cbo_working_company_name=str_replace("'", "", $cbo_working_company_name);
	$txt_job_id=str_replace("'", "", $txt_job_id);
	$cbo_buyer=str_replace("'", "", $cbo_buyer);
	$cbo_brand_name=str_replace("'", "", $cbo_brand_name);
	$txt_job=str_replace("'", "", $txt_job);
	$txt_order_id=str_replace("'", "", $txt_order_id);
	$txt_style=str_replace("'", "", $txt_style);
	$cbo_gauge=str_replace("'", "", $cbo_gauge);
	$cbo_date_type=str_replace("'", "", $cbo_date_type);

	$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_name_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$lib_size=return_library_array( "select size_name, id from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$lib_color=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");	 
	$brand_name_arr=return_library_array( "select id, brand_name from lib_buyer_brand  where status_active=1 and is_deleted=0",'id','brand_name');	 

	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
		$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
 
		if($cbo_date_type==1){
			$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
			$field ="b.PUB_SHIPMENT_DATE";
		}
		else if($cbo_date_type==2){
			$date_cond=" and b.PO_RECEIVED_DATE between '$start_date' and '$end_date'";
			$field ="b.PO_RECEIVED_DATE";
		}
	}
	else $field ="b.PUB_SHIPMENT_DATE";

	if ($cbo_buyer == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "") {
				 $whereCon .= " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			} else {
				$whereCon .= "";
			}
		} else {
			$whereCon .= "";
		}
	} else {
		 $whereCon .= " and a.buyer_name in ($cbo_buyer) ";
	}

	if($txt_job_id!=''){
		$whereCon.="and a.id in($txt_job_id)";
	}
	if($txt_order_id!=''){
		$whereCon.="and b.id in($txt_order_id)";
	}
	if($txt_job!=''){
		$whereCon.="and a.JOB_NO_PREFIX_NUM in($txt_job)";
	}
	if($txt_style!=''){
		$whereCon.="and a.STYLE_REF_NO in('".str_replace(",","','",$txt_style)."')";
	}
	
	if($cbo_buyer!=0){
		$whereCon.="and a.BUYER_NAME in($cbo_buyer)";
	}
	if($cbo_gauge!=''){
		$whereCon.="and a.GAUGE in($cbo_gauge)";
	}
	if($cbo_working_company_name!=''){
		$whereCon.="and a.style_owner in($cbo_working_company_name)";
	}

	$brand_cond="";
	if($cbo_brand_name !=''){
		$brand_cond = "and a.brand_id in ($cbo_brand_name)";
	}
  

	if($reportType==1){$confirmCon =" and b.IS_CONFIRMED=1";}
	else if($reportType==2){$confirmCon =" and b.IS_CONFIRMED=2";}
	else if($reportType==5){$confirmCon =" and b.IS_CONFIRMED=1";}
	else{$confirmCon ="";}

	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."  and ENTRY_FORM=148");


	 $reportSql = "SELECT a.id as JOBID,b.id as PO_ID, $field as SHIPMENT_DATE, b.PO_NUMBER, b.UNIT_PRICE, b.details_remarks, b.PO_QUANTITY, b.pub_shipment_date, b.PO_RECEIVED_DATE,b.IS_CONFIRMED, b.SHIPING_STATUS,a.FABRIC_COMPOSITION, a.COMPANY_NAME, a.JOB_NO, a.GARMENTS_NATURE, a.STYLE_REF_NO, a.BUYER_NAME, a.GAUGE,
	a.AVG_UNIT_PRICE ,a.STYLE_DESCRIPTION, a.brand_id, a.YARN_QUALITY, a.SHIP_MODE, a.STYLE_OWNER, a.TOTAL_SET_QNTY,a.SEASON_BUYER_WISE, a.yarn_avg_rate, a.knitting_pattern, a.JOB_NO_PREFIX_NUM from WO_PO_DETAILS_MASTER a, WO_PO_BREAK_DOWN b where a.id=b.job_id and a.is_deleted=0 and b.is_deleted=0 $date_cond $whereCon and a.COMPANY_NAME in ($cbo_company_name) order By $field"; 
	 
    $reportSqlRes=sql_select($reportSql);

	$tmp_po_arr = array();
	$dataArr = array();
	$ship_date_wise_po_arr = array();
	$targetBr =3;
	foreach($reportSqlRes as $rows){
		$dataArr[$rows['SHIPMENT_DATE']][$key][$rows['PO_ID']] = $rows;
		$ship_date_wise_po_arr[$rows['SHIPMENT_DATE']][$rows['PO_ID']]= $rows['PO_ID'];
		$tmp_po_arr[$rows['PO_ID']] = $rows['PO_ID'];
		$tmp_jobid_arr[$rows['JOBID']] = $rows['JOBID'];
		 
		$flag++;
		if($flag == $targetBr){$key++;$flag = 0;}
	} 
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 148, 1, $tmp_po_arr, $empty_arr);//PO ID Ref from=1
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 148, 2, $tmp_jobid_arr, $empty_arr);//JOb ID Ref from=2

	$size_sql= "SELECT a.PO_BREAK_DOWN_ID,a.SIZE_NUMBER_ID, a.COLOR_NUMBER_ID, (a.order_quantity) as ORDER_QUANTITY
	            from wo_po_color_size_breakdown a ,gbl_temp_engine g
				where  a.po_break_down_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1   and g.entry_form=148 and a.is_deleted=0 and a.status_active=1  order by SIZE_NUMBER_ID asc";
	$size_sql_res=sql_select($size_sql);
	unset($tmp_po_arr);

	$po_color_size_raiot_arr = array();
	foreach($size_sql_res as $rows){
		$po_color_size_raiot_arr[$rows['PO_BREAK_DOWN_ID']][$rows['COLOR_NUMBER_ID']][$rows['SIZE_NUMBER_ID']]+= $rows['ORDER_QUANTITY'];
		$po_size_arr[$rows['PO_BREAK_DOWN_ID']][$rows['SIZE_NUMBER_ID']] += $rows['ORDER_QUANTITY'];
	}
	unset($size_sql_res);

	$ship_date_wise_qty_arr=array();
	foreach($ship_date_wise_po_arr as $ship_date => $poRow){
		foreach($poRow as $po_id){
			$ship_date_wise_qty_arr[$ship_date] += array_sum($po_size_arr[$po_id]);
		}
	}
	
	$image_arr = return_library_array( "select a.MASTER_TBLE_ID,a.IMAGE_LOCATION from COMMON_PHOTO_LIBRARY a,WO_PO_DETAILS_MASTER b,gbl_temp_engine g where a.MASTER_TBLE_ID=b.job_no and  b.id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=2   and g.entry_form=148 and a.FORM_NAME='knit_order_entry' and a.FILE_TYPE=1",'MASTER_TBLE_ID','IMAGE_LOCATION');

$con = connect();
execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and   ENTRY_FORM=148");
oci_commit($con);
disconnect($con); 

$count_date_row= count($dataArr);
$kk=1;

?>
<style>
	
	.pagebreak {
		clear: both;
		page-break-after: always;
	}
	
</style>
<?
$html="";
	foreach($dataArr as $ship_date => $customRow){
		$bgcolor="#B1E6FC";
		$html.="<div><h3 align='left' style='background-color:$bgcolor;font-family:Arial Narrow'> <b>".str_replace(['b.','_'],' ',$field)."; DATE :</b>".$ship_date."<b>".str_replace(['b.','_'],' ',$field)."; DATE Total:</b>".$ship_date_wise_qty_arr[$ship_date]."</h3></div>";
		$flag_td=1;
		if(!empty($customRow))
		{
			foreach($customRow as $data_row){
				$html.="<table cellspacing='0' cellpadding='1' border='1' rules='all'  style='vertical-align:top;border-collapse:collapse;font-family:Arial Narrow;' class='rpt_table'>	
				<tr style='height: 120px;'> 
				<td align='center'><strong>Particulars</strong></td>";
				foreach($data_row as $row){
					$html.="<td align='center'><img  src='base_url(".$image_arr[$row['JOB_NO']].");' height='100'  width='100'  alt='No Image' /></td>";
				}
				$html.="</tr><tr><td ><strong>Style</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$row['STYLE_REF_NO']."</td>";
				}
				$html.="</tr><tr><td><strong>Order No</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$row['PO_NUMBER']."</td>";
				}
				$html.="</tr><tr><td><strong>Job No</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$row['JOB_NO']."</td>";
				}
				$html.="</tr><tr><td><strong>Session</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$row['SEASON_BUYER_WISE']."</td>";
				}
				$html.="</tr><tr><td><strong>Buyer</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$buyer_name_arr[$row['BUYER_NAME']]."</td>";
				}
				$html.="</tr><tr><td><strong>Brand</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$brand_name_arr[$row['BRAND_ID']]."</td>";
				}
				$html.="</tr><tr><td><strong>Style Des.</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$row['STYLE_DESCRIPTION']."</td>";
				}
				$html.="</tr><tr><td><strong>Knitting Pattern</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$knitting_pattern_arr[$row[csf('knitting_pattern')]]."</td>";
				}
				$html.="</tr><tr><td><strong>Fabrication</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$row['YARN_QUALITY']."</td>";
				}
				$html.="</tr><tr><td><strong>Gauge</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$gauge_arr[$row['GAUGE']]."</td>";
				}
				$html.="</tr><tr><td><strong>L.Yarn Supplier</strong></td>";
				foreach($data_row as $row){
					$html.="<td>&nbsp;</td>";
				}
				$html.="</tr><tr><td><strong>Gmts Weight</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$short_quat_arr[$style]['gmts_weight']."</td>";
				}
				$html.="</tr><tr><td><strong>Price/Unit</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$row['UNIT_PRICE']."</td>";	
				}
				$html.="</tr><tr><td><strong>Quantity</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$row['PO_QUANTITY']."</td>";	
				}
				$html.="</tr><tr><td><strong>F. Shipment Date</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$row['SHIPMENT_DATE']."</td>";						 
				}
				$html.="</tr><tr><td><strong>Shipment Mode</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$shipment_mode[$row['SHIP_MODE']]."</td>";		
				}
				$html.="</tr><tr><td><strong>Accessories</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".rtrim($job_wise_data[$row['JOB_NO']]['accessories'],",")."</td>";		
				}
				$html.="</tr><tr><td><strong>Knitting Time</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$short_quat_arr[$style]['knitting_time']."</td>";	
				}
				$html.="</tr><tr><td><strong>P2P Time</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$short_quat_arr[$style]['makeup_time']."</td>";	
				}
				$html.="</tr><tr><td><strong>Dev./Proto Sample</strong></td>";
				foreach($data_row as $row){
					$html.="<td>Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][35]['submitted_date'].";Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][35]['approval_date']."</td>";	
				}
				$html.="</tr><tr><td><strong>F.sample/Ref Tag</strong></td>";
				foreach($data_row as $row){
					$html.="<td>Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][36]['submitted_date'].";Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][36]['approval_date']."</td>";	
				}
				$html.="</tr><tr><td><strong>PP/Sealer/G.Sample</strong></td>";
				foreach($data_row as $row){
					$html.="<td>Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][37]['submitted_date'].";Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][37]['approval_date']."</td>";	
				}
				$html.="</tr><tr><td><strong>Photo Sample</strong></td>";
				foreach($data_row as $row){
					$html.="<td>Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][4]['submitted_date'].";Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][4]['approval_date']."</td>";	
				}
				$html.="</tr><tr><td><strong>Lab Test/L.Sample</strong></td>";
				foreach($data_row as $row){
					$html.="<td>Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][38]['submitted_date'].";Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][38]['approval_date']."</td>";	
				}
				$html.="</tr><tr><td><strong>Shipment Sample</strong></td>";
				foreach($data_row as $row){
					$html.="<td>Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][6]['submitted_date'].";Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][6]['approval_date']."</td>";	
				}
				$html.="</tr><tr><td><strong>Production Factory</strong></td>";
				foreach($data_row as $row){
					$html.="<td>".$company_name_arr[$rows['STYLE_OWNER']]."</td>";	
				}
				$html.="</tr><tr><td><strong>Color Size Ratio</strong></td>";
				foreach($data_row as $row){
					$html.="<td>";
					$html.="<table border='1' cellspacing='0' cellpadding='2' width='100%' style='border-collapse:collapse;'><thead><tr><th><strong>Color</strong></th>";
					foreach($po_size_arr[$row['PO_ID']] as $size_id => $sizeQty){
						$html.="<th align='center'>{".$lib_size[$size_id]."}</th>";
					}
					$html.="<th>Color Total</th></tr></thead>";
					foreach($po_color_size_raiot_arr[$row['PO_ID']] as $color_id => $sizeRow){
						$html.="<tbody><tr><td>$lib_color[$color_id]</td>";
						foreach($sizeRow as $size_id => $ratioQty){
							$html.="<td align='center'>{".$ratioQty."}</td>";
						}
						$html.="<td align='center'>".array_sum($sizeRow)."</td></tr></tbody>";
					}
					$html.="<tfoot><tr><td><strong>G. Total</strong></td>";
					foreach($po_size_arr[$row['PO_ID']] as $size_id => $sizeQty){
						$html.="<td align='center'>".$sizeQty."</td>";
					}
					$html.="<td align='center'><strong>".array_sum($po_size_arr[$row['PO_ID']])."</strong></td></tr></tfoot></table></td>";
				}
				$html.="</tr><tr><td><strong>Avg.Yarn Price</strong></td>";
				foreach($data_row as $row){
					$html.="<td align='center'>".number_format($short_quat_arr[$style]['yarn_avg_rate'], 2)."</td>";
				}
				$html.="</tr><tr><td><strong>Remark</strong></td>";
				foreach($data_row as $row){
					$html.="<td align='center'>".$row[('details_remarks')]."</td>";
				}
				$html.="</tr></table>";
				
				if($kk>0)
				{
					if(!empty($data_row))
					{
						$html.="<div style='page-break-after:always'></div>";
					}
				}
				$kk++;	
			}
			
		}
		//echo $kk."==";
	} 
	//date end
 
//$reportBody=ob_get_contents();
//ob_end_clean();
$reportBody=trim($html);
$user_id=$_SESSION['logic_erp']['user_id'];
$report_cat=100;
foreach (glob("order*.xls") as $filename1)
{
	if( @filemtime($filename1) < (time()-$seconds_old) )
	@unlink($filename1);
}
 //echo $reportBody;
$name=time();
$filename1="order".$user_id."_".$name.".xls";
$create_new_doc = fopen($filename1, 'w');	
$is_created = fwrite($create_new_doc, $reportBody);

foreach (glob("../../../../auto_mail/tmp/order_recap_".$user_id.".pdf") as $filename1) {			
	@unlink($filename1);
}

if(!empty($reportBody))
{
	$att_file_arr=array();
	require('../../../../ext_resource/mpdf60/mpdf.php');
	$mpdf = new mPDF('', 'A4-L', '', '', 10, 10, 10, 15, 3, 3);	
	//A4-L
	$mpdf->SetHTMLFooter($report_signature);
	//$mpdf->autoPageBreak = true;
	//$mpdf->shrink_tables_to_fit = 1;
	$mpdf->WriteHTML($reportBody,2);
	//$mpdf->use_kwt = true;//
$user_id=$_SESSION['logic_erp']['user_id'];
$REAL_FILE_NAME = 'order_recap_'.$user_id.'.pdf';
$file_path='../../../../auto_mail/tmp/'.$REAL_FILE_NAME;
$mpdf->Output($file_path, 'F');
$att_file_arr[]='../../../../auto_mail/tmp/'.$REAL_FILE_NAME.'**'.$REAL_FILE_NAME;
}

//ob_clean();
if($is_mail_send==1){	

$sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=135 and  b.mail_user_setup_id=c.id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
$mail_sql_res=sql_select($sql);

$mailArr=array();
foreach($mail_sql_res as $row)
{
$mailArr[$row[EMAIL]]=$row[EMAIL]; 
}

$supplier_id=$nameArray[0][csf('supplier_id')];
$supplier_mail=return_field_value("email", "lib_supplier", "status_active=1 and is_deleted=0 and id=$supplier_id ");



if($mail_id!=''){$mailArr[]=$mail_id;}
if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail;}

$to=implode(',',$mailArr);
$subject="Fabric Booking Auto Mail";

if($to!=""){
require '../../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
require_once('../../../../auto_mail/setting/mail_setting.php');
$header=mailHeader();
echo sendMailMailer( $to, $subject, $emailBody,$from_mail,'' );
}
}
	unset($dataArr);
	unset($po_size_arr);
	exit();
 
}

if($action=="report_generate_8_old")
{ 

	extract($_REQUEST);
	$cbo_company_name=str_replace("'", "", $cbo_company_name);
	$cbo_working_company_name=str_replace("'", "", $cbo_working_company_name);
	$txt_job_id=str_replace("'", "", $txt_job_id);
	$cbo_buyer=str_replace("'", "", $cbo_buyer);
	$cbo_brand_name=str_replace("'", "", $cbo_brand_name);
	$txt_job=str_replace("'", "", $txt_job);
	$txt_order_id=str_replace("'", "", $txt_order_id);
	$txt_style=str_replace("'", "", $txt_style);
	$cbo_gauge=str_replace("'", "", $cbo_gauge);
	$cbo_date_type=str_replace("'", "", $cbo_date_type);

	$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_name_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	//$sample_library=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name"  );
	$lib_size=return_library_array( "select size_name, id from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$lib_color=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");	 
//	$season_name_arr=return_library_array( "select id, eason_name from lib_buyer_season  where status_active=1 and is_deleted=0",'id','season_name');
	$brand_name_arr=return_library_array( "select id, brand_name from lib_buyer_brand  where status_active=1 and is_deleted=0",'id','brand_name');
	//$lib_item_group_arr=return_library_array( "select item_name,id from lib_item_group where item_category=4 order by item_name", "id", "item_name");
	 

	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
		$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
 
		if($cbo_date_type==1){
			$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
			$field ="b.PUB_SHIPMENT_DATE";
		}
		else if($cbo_date_type==2){
			$date_cond=" and b.PO_RECEIVED_DATE between '$start_date' and '$end_date'";
			$field ="b.PO_RECEIVED_DATE";
		}
	}
	else $field ="b.PUB_SHIPMENT_DATE";

	if ($cbo_buyer == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "") {
				 $whereCon .= " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			} else {
				$whereCon .= "";
			}
		} else {
			$whereCon .= "";
		}
	} else {
		 $whereCon .= " and a.buyer_name in ($cbo_buyer) ";
	}

	if($txt_job_id!=''){
		$whereCon.="and a.id in($txt_job_id)";
	}
	if($txt_order_id!=''){
		$whereCon.="and b.id in($txt_order_id)";
	}
	if($txt_job!=''){
		$whereCon.="and a.JOB_NO_PREFIX_NUM in($txt_job)";
	}
	if($txt_style!=''){
		$whereCon.="and a.STYLE_REF_NO in('".str_replace(",","','",$txt_style)."')";
	}
	
	if($cbo_buyer!=0){
		$whereCon.="and a.BUYER_NAME in($cbo_buyer)";
	}
	if($cbo_gauge!=''){
		$whereCon.="and a.GAUGE in($cbo_gauge)";
	}
	if($cbo_working_company_name!=''){
		$whereCon.="and a.style_owner in($cbo_working_company_name)";
	}

	$brand_cond="";
	if($cbo_brand_name !=''){
		$brand_cond = "and a.brand_id in ($cbo_brand_name)";
	}
  

	if($reportType==1){$confirmCon =" and b.IS_CONFIRMED=1";}
	else if($reportType==2){$confirmCon =" and b.IS_CONFIRMED=2";}
	else if($reportType==5){$confirmCon =" and b.IS_CONFIRMED=1";}
	else{$confirmCon ="";}

	$con = connect();
	//execute_query("delete from tmp_poid where userid=".$user_id."");
 	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."  and ENTRY_FORM=148");


	    $reportSql = "SELECT a.id as JOBID,b.id as PO_ID, $field as SHIPMENT_DATE, b.PO_NUMBER, b.UNIT_PRICE, b.details_remarks, b.PO_QUANTITY, b.pub_shipment_date, b.PO_RECEIVED_DATE,
	b.IS_CONFIRMED, b.SHIPING_STATUS,
	a.FABRIC_COMPOSITION, a.COMPANY_NAME, a.JOB_NO, a.GARMENTS_NATURE, a.STYLE_REF_NO, a.BUYER_NAME, a.GAUGE,
	a.AVG_UNIT_PRICE ,a.STYLE_DESCRIPTION, a.brand_id, a.YARN_QUALITY, a.SHIP_MODE, a.STYLE_OWNER, a.TOTAL_SET_QNTY,
	a.SEASON_BUYER_WISE, a.yarn_avg_rate, a.knitting_pattern, a.JOB_NO_PREFIX_NUM
	from WO_PO_DETAILS_MASTER a, WO_PO_BREAK_DOWN b where a.id=b.job_id and a.is_deleted=0 and b.is_deleted=0 $date_cond $whereCon and a.COMPANY_NAME in ($cbo_company_name) order By $field";
// $brand_cond
 
    $reportSqlRes=sql_select($reportSql);

	$tmp_po_arr = array();
	$dataArr = array();
	$ship_date_wise_po_arr = array();
	$targetBr =3;
	foreach($reportSqlRes as $rows){
		// if($dataArr[$rows['SHIPMENT_DATE']] ==''){$key=0;$flag = 0;}
		
		$dataArr[$rows['SHIPMENT_DATE']][$key][$rows['PO_ID']] = $rows;
		$ship_date_wise_po_arr[$rows['SHIPMENT_DATE']][$rows['PO_ID']]= $rows['PO_ID'];
		$tmp_po_arr[$rows['PO_ID']] = $rows['PO_ID'];
		$tmp_jobid_arr[$rows['JOBID']] = $rows['JOBID'];
		 
		$flag++;
		if($flag == $targetBr){$key++;$flag = 0;}
	} 
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 148, 1, $tmp_po_arr, $empty_arr);//PO ID Ref from=1
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 148, 2, $tmp_jobid_arr, $empty_arr);//JOb ID Ref from=2
//	unset($reportSqlRes);
	//echo "<pre>";
	//print_r($dataArr);die;

	$size_sql= "SELECT a.PO_BREAK_DOWN_ID,a.SIZE_NUMBER_ID, a.COLOR_NUMBER_ID, (a.order_quantity) as ORDER_QUANTITY
	            from wo_po_color_size_breakdown a ,gbl_temp_engine g
				where  a.po_break_down_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1   and g.entry_form=148 and a.is_deleted=0 and a.status_active=1  order by SIZE_NUMBER_ID asc";
				//".where_con_using_array($tmp_po_arr,0,'a.PO_BREAK_DOWN_ID')."
	$size_sql_res=sql_select($size_sql);
	unset($tmp_po_arr);

	// $po_color_size_arr[po_id][color_id][size_id]= val
	$po_color_size_raiot_arr = array();
	foreach($size_sql_res as $rows){
		$po_color_size_raiot_arr[$rows['PO_BREAK_DOWN_ID']][$rows['COLOR_NUMBER_ID']][$rows['SIZE_NUMBER_ID']]+= $rows['ORDER_QUANTITY'];
		$po_size_arr[$rows['PO_BREAK_DOWN_ID']][$rows['SIZE_NUMBER_ID']] += $rows['ORDER_QUANTITY'];
	}
	unset($size_sql_res);

	$ship_date_wise_qty_arr=array();
	foreach($ship_date_wise_po_arr as $ship_date => $poRow){
		foreach($poRow as $po_id){
			$ship_date_wise_qty_arr[$ship_date] += array_sum($po_size_arr[$po_id]);
		}

	}
	
	ob_start();
 
	$image_arr = return_library_array( "select a.MASTER_TBLE_ID,a.IMAGE_LOCATION from COMMON_PHOTO_LIBRARY a,WO_PO_DETAILS_MASTER b,gbl_temp_engine g where a.MASTER_TBLE_ID=b.job_no and  b.id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=2   and g.entry_form=148 and a.FORM_NAME='knit_order_entry' and a.FILE_TYPE=1",'MASTER_TBLE_ID','IMAGE_LOCATION');
?>
 
<?

$con = connect();
execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and   ENTRY_FORM=148");
oci_commit($con);
disconnect($con); 

$count_date_row= count($dataArr);
$kk=1;
	foreach($dataArr as $ship_date => $customRow){
		$bgcolor="#B1E6FC";
		?>
		<div>
			<h3 align="left" style="background-color: <?= $bgcolor;?>"> 
				<b><?= str_replace(['b.','_'],' ',$field);?> DATE :</b><? echo $ship_date; ?>,
				<b><?= str_replace(['b.','_'],' ',$field);?> DATE Total:</b><?= $ship_date_wise_qty_arr[$ship_date];?>
			</h3>
		</div>

		<?
		// if(!empty($customRow))
		// {
			
		// }
		$flag_td=1;
		foreach($customRow as $data_row){
			?>
			    <div style="float:left; margin:0px;vertical-align:top;" align="left">
				<table cellspacing="0" cellpadding="1" border="1" rules="all"  style="vertical-align:top;border-collapse:collapse;" class="rpt_table">	 
				<tr style="height: 120px;"> 
					<td align="center"  ><strong>Particulars</strong></td>
					<?
					
					foreach($data_row as $row){
						// if($flag_td==4){
						// 	echo "</tr><tr><td>";$flag_td=1;}
						// 	else{echo "<td>";}
						 
						?>
						<td align="center" ><img  src="<?= base_url($image_arr[$row['JOB_NO']]); ?>" height="100"  width="100"  alt="No Image" /></td>
						<?	
						 
					}
					?> 
				</tr>
				<tr> 
					<td ><strong>Style</strong></td>
					<?
					foreach($data_row as $row){
						// if($flag_td==4){
						// 	echo "</tr><tr><td>";$flag_td=1;}
						// 	else{echo "<td>";}
						 
						?>
						<td><?= $row['STYLE_REF_NO'];?></td>
						<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Order No</strong></td>
					<?
					foreach($data_row as $row){
						// if($flag_td==4){
						// 	echo "</tr><tr><td>";$flag_td=1;}
						// 	else{echo "<td>";}
					 
					?>
						<td><?= $row['PO_NUMBER'];?></td>
					<?	
					}
				     ?>
				</tr>

				<tr>
					<td><strong>Job No</strong></td>
					<?
					foreach($data_row as $row){
						// if($flag_td==4){
						// 	echo "</tr><tr><td>";$flag_td=1;}
						// 	else{echo "<td>";}
						 
						 
					?>
						<td><?= $row['JOB_NO'];?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Session</strong></td>
					<?
					foreach($data_row as $row){
						
					?>
						<td><?= $row['SEASON_BUYER_WISE'];?></td>
					<?	
					}
					?>
				</tr>

				<tr>
			    <td><strong>Buyer</strong></td>
				    <?
					foreach($data_row as $row){
					?>
						<td><?= $buyer_name_arr[$row['BUYER_NAME']];?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Brand</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= $brand_name_arr[$row['BRAND_ID']];?></td>
					<?	
					}
					?>
				</tr>

				<tr>
			        <td><strong>Style Des.</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= $row['STYLE_DESCRIPTION'];?></td>
					<?	
					}
					?>
				</tr>
				<tr>    
					<td><strong>Knitting Pattern</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= $knitting_pattern_arr[$row[csf('knitting_pattern')]];?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Fabrication</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= $row['YARN_QUALITY'];?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Gauge</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= $gauge_arr[$row['GAUGE']];?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>L.Yarn Supplier</strong></td>
					<?
					foreach($data_row as $row){
					?>
					    <td></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Gmts Weight</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= $short_quat_arr[$style]['gmts_weight'];?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Price/Unit</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= $row['UNIT_PRICE'];?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Quantity</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= $row['PO_QUANTITY'];?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>F. Shipment Date</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= $row['SHIPMENT_DATE'];?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Shipment Mode</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= $shipment_mode[$row['SHIP_MODE']];?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Accessories</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= rtrim($job_wise_data[$row['JOB_NO']]['accessories'],",");?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Knitting Time</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= $short_quat_arr[$style]['knitting_time'];?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>P2P Time</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= $short_quat_arr[$style]['makeup_time'];?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Dev./Proto Sample</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= "Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][35]['submitted_date']."; Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][35]['approval_date']; ?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>First sample</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= "Sent On-".$job_po_wise_data[$row['JOB_ID']][$row['PO_ID']][36]['submitted_date']."; Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][36]['approval_date'];?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>PP Sample</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= "Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][37]['submitted_date']."; Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][37]['approval_date']; ?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Photo Sample</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= "Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][4]['submitted_date']."; Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][4]['approval_date']; ?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Lab Test/Sample</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= "Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][38]['submitted_date']."; Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][38]['approval_date']; ?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Shipment Sample</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= "Sent On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][6]['submitted_date']."; Approved On-".$job_po_wise_data[$row['JOB_NO']][$row['PO_ID']][6]['approval_date']; ?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td><strong>Production Factory</strong></td>
					<?
					foreach($data_row as $row){
					?>
						<td><?= $company_name_arr[$rows['STYLE_OWNER']]; ?></td>
					<?	
					}
					?>
				</tr>
				<tr>
					<td>Color Size Ratio</td>
					<?
					foreach($data_row as $row){
					?>
					    <div style="margin:0px;vertical-align:top;">
						    <td> 
								<table border="1" cellspacing="0" cellpadding="3" width="100%" style="border-collapse:collapse;">
									<thead>
										<tr>
											<th><strong>Color</strong></th>
											<?php
											foreach($po_size_arr[$row['PO_ID']] as $size_id => $sizeQty){
												echo "<th align='center'>{$lib_size[$size_id]}</th>";
											}
											?>
											<th>Color Total</th>
										</tr>
									</thead>
									<?
									foreach($po_color_size_raiot_arr[$row['PO_ID']] as $color_id => $sizeRow){
									?>
									<tbody> 
										<tr>
											<td><?= $lib_color[$color_id];?></td>
											<? 
											foreach($sizeRow as $size_id => $ratioQty){
												echo "<td align='center'>{$ratioQty}</td>";
											}
											?>
											<td align="center"><?= array_sum($sizeRow);?></td>
										</tr>
									</tbody> 
									<?php
									}
									?>
									<tfoot>
										<tr>
											<td><strong>G. Total</strong></td>
											<?php
											foreach($po_size_arr[$row['PO_ID']] as $size_id => $sizeQty){
												echo "<td align='center'>$sizeQty</td>";
											}
											?>
											<td align="center"><strong><?=array_sum($po_size_arr[$row['PO_ID']]);?></strong></td>
										</tr>
									</tfoot>
								</table>
						    </td>
						</div>
						
					<?
					}
					?>
				</tr>
				<tr>
					<td><strong>Avg.Yarn Price</strong></td>
					<?
					foreach($data_row as $row){
						?>
						<td><?= number_format($short_quat_arr[$style]['yarn_avg_rate'], 2); ?></td>
						<?	
					}
					?>
				</tr>

				<tr>
					<td><strong>Remark</strong></td>
					<?
					foreach($data_row as $row){
						?>
						<td><?= $row[csf('details_remarks')]; ?></td>
						<?	
					}
					?>
				</tr>
			</table>  
			<br clear="all">
		 
			 <?
			 if($flag_td>3)
			 { ?>
			  <p style="page-break-after:always;"></p> 
			<pagebreak>  
			<?
			 $flag_td=1;
			 }
		
		   $flag_td++;
		}
		 //PO end
		if($kk<$count_date_row)
		{

		?>
		<p style="page-break-after:always;"></p> 
		<pagebreak>
		
		<? 
		//$dateChakarr[$ship_date]=$ship_date;
		}
		$kk++;
	} 
	//date end
	?>
<?
 //echo $kk.'=AAAAAAAAAAAAAAA';
$reportBody=ob_get_contents();
ob_end_clean();
$user_id=$_SESSION['logic_erp']['user_id'];
$report_cat=100;
foreach (glob("order*.xls") as $filename1)
{
	if( @filemtime($filename1) < (time()-$seconds_old) )
	@unlink($filename1);
}

$name=time();
$filename1="order".$user_id."_".$name.".xls";
$create_new_doc = fopen($filename1, 'w');	
$is_created = fwrite($create_new_doc, $reportBody);

foreach (glob("../../../../auto_mail/tmp/order_recap_".$user_id.".pdf") as $filename1) {			
	@unlink($filename1);
}

// echo $reportBody;die;
if(!empty($reportBody))
{
	$att_file_arr=array();
	require('../../../../ext_resource/mpdf60/mpdf.php');
	$mpdf = new mPDF('', 'A4-L', '', '', 10, 10, 10, 15, 3, 3);	
	//A4-L
	$mpdf->SetHTMLFooter($report_signature);
	$mpdf->autoPageBreak = true;
	$mpdf->shrink_tables_to_fit = 1;
	$mpdf->WriteHTML($reportBody,2);
	//$mpdf->use_kwt = true;//
$user_id=$_SESSION['logic_erp']['user_id'];
$REAL_FILE_NAME = 'order_recap_'.$user_id.'.pdf';
$file_path='../../../../auto_mail/tmp/'.$REAL_FILE_NAME;
$mpdf->Output($file_path, 'F');
$att_file_arr[]='../../../../auto_mail/tmp/'.$REAL_FILE_NAME.'**'.$REAL_FILE_NAME;
}


//ob_clean();
if($is_mail_send==1){	

$sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=135 and  b.mail_user_setup_id=c.id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
$mail_sql_res=sql_select($sql);

$mailArr=array();
foreach($mail_sql_res as $row)
{
$mailArr[$row[EMAIL]]=$row[EMAIL]; 
}

$supplier_id=$nameArray[0][csf('supplier_id')];
$supplier_mail=return_field_value("email", "lib_supplier", "status_active=1 and is_deleted=0 and id=$supplier_id ");



if($mail_id!=''){$mailArr[]=$mail_id;}
if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail;}

$to=implode(',',$mailArr);
$subject="Fabric Booking Auto Mail";

if($to!=""){
require '../../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
require_once('../../../../auto_mail/setting/mail_setting.php');
$header=mailHeader();
echo sendMailMailer( $to, $subject, $emailBody,$from_mail,'' );
}
}
	unset($dataArr);
	unset($po_size_arr);
	exit();
 
}
    

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
    <script>
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
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
				//selected_no.push( str );				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				//selected_no.splice( i, 1 ); 
			}
			var id = ''; var name = ''; var job = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				//num += selected_no[i] + ','; 
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 ); 
			//num 	= num.substr( 0, num.length - 1 );
			//alert(name);
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name ); 
			//$('#hide_job_no').val( num );
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="100">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:80px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($cbo_company_name) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $cbo_company_name; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $txt_style_ref_no; ?>'+'**'+'<? echo $txt_style_ref_id; ?>', 'create_job_no_search_list_view', 'search_div', 'order_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}
if($action=="create_job_no_search_list_view")
{
	extract($_REQUEST);
	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	list($company,$buyer,$search_type,$search_value,$cbo_year,$txt_style_ref_no,$txt_style_ref_id)=explode('**',$data);

	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
		}
		else
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";	
		}
	}
	if($db_type==0) if($cbo_year!=0) $job_cond=" and year(a.insert_date)='$cbo_year'";
	else if($cbo_year!=0) $job_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
	
	if($search_type==1 && $search_value!=''){
		$search_con=" and a.job_no_prefix_num='$search_value'";	
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no='$search_value'";	
	}

	
	if($db_type==0) $year_field="YEAR(a.insert_date) as job_year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as job_year";
	else $year_field="";
	
	if($buyer!=0) $buyer_cond="and a.buyer_name=$buyer"; else $buyer_cond="";
	$sql = "select a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,$year_field from wo_po_details_master a where a.company_name in($company) $buyer_cond $year_cond $job_cond $search_con and is_deleted=0 order by a.id desc"; 
	echo create_list_view("list_view", "Style Ref No,Job No,Year","160,90,100","400","200",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,job_year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='hide_job_id' />";
	//echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='hide_job_no' />";
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_style_ref_no;?>';
	var style_id='<? echo $txt_style_ref_id;?>';
	//var style_des='<? echo $txt_style_ref;?>';
	//alert(style_id);
	if(style_no!="")
	{
		style_no_arr=style_no.split(",");
		style_id_arr=style_id.split(",");
		style_des_arr=style_des.split(",");
		var str_ref="";
		for(var k=0;k<style_no_arr.length; k++)
		{
			str_ref=style_no_arr[k]+'_'+style_id_arr[k];
			js_set_value(str_ref);
		}
	}
	</script>
    <?
	exit();
}

//Order search------------------------------//
if($action=="order_no_popup")
{
	echo load_html_head_contents("Order Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
    <script>
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
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
				//selected_no.push( str );				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				//selected_no.splice( i, 1 ); 
			}
			var id = ''; var name = ''; var job = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				//num += selected_no[i] + ','; 
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 ); 
			//num 	= num.substr( 0, num.length - 1 );
			//alert(name);
			$('#hide_order_id').val( id );
			$('#hide_order_no').val( name ); 
			//$('#hide_job_no').val( num );
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="100">Please Enter Order No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:80px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					
					<input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
                    <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($cbo_company_name) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Order No",2=>"Job No",3=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $cbo_company_name; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $txt_style_ref_no; ?>'+'**'+'<? echo $txt_style_ref_id; ?>', 'create_order_no_search_list_view', 'search_div', 'order_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}


if($action=="create_order_no_search_list_view")
{
	extract($_REQUEST);
	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	list($company,$buyer,$search_type,$search_value,$cbo_year,$txt_style_ref_no,$txt_style_ref_id)=explode('**',$data);

	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
		}
		else
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";	
		}
	}
	if($db_type==0) if($cbo_year!=0) $job_cond=" and year(a.insert_date)='$cbo_year'";
	else if($cbo_year!=0) $job_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
	
	if($search_type==1 && $search_value!=''){
		$search_con=" and b.po_number LIKE '%$search_value%'";	
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and a.job_no_prefix_num='$search_value'";		
	}else if($search_type==3 && $search_value!=''){
		$search_con=" and a.style_ref_no='$search_value'";	
	}


	

	if($db_type==0) $year_field="YEAR(a.insert_date) as job_year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as job_year";
	else $year_field="";
	
	if($buyer!=0) $buyer_cond="and a.buyer_name=$buyer"; else $buyer_cond="";
	$sql = "select b.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,b.po_number,$year_field from wo_po_details_master a,wo_po_break_down b  where a.job_no=b.job_no_mst and a.company_name in($company) $buyer_cond $year_cond $job_cond $search_con and a.is_deleted=0 order by a.job_no_prefix_num"; 
	echo create_list_view("list_view", "Style Ref No,Job No,Year,Order No","160,90,100,100","500","200",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,job_year,po_number", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='hide_order_id' />";
	//echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='hide_order_no' />";
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_style_ref_no;?>';
	var style_id='<? echo $txt_style_ref_id;?>';
	//var style_des='<? echo $txt_style_ref;?>';
	//alert(style_id);
	if(style_no!="")
	{
		style_no_arr=style_no.split(",");
		style_id_arr=style_id.split(",");
		style_des_arr=style_des.split(",");
		var str_ref="";
		for(var k=0;k<style_no_arr.length; k++)
		{
			str_ref=style_no_arr[k]+'_'+style_id_arr[k];
			js_set_value(str_ref);
		}
	}
	</script>
    <?
	exit();
}

//Style search------------------------------//


if($action=="style_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
    <script>
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
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
				//selected_no.push( str );				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				//selected_no.splice( i, 1 ); 
			}
			var id = ''; var name = ''; var job = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				//num += selected_no[i] + ','; 
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 ); 
			//num 	= num.substr( 0, num.length - 1 );
			//alert(name);
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name ); 
			//$('#hide_job_no').val( num );
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="100">Please Enter Style</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:80px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($cbo_company_name) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(2=>"Style Ref",1=>"Job No");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $cbo_company_name; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $txt_style_ref_no; ?>'+'**'+'<? echo $txt_style_ref_id; ?>', 'create_style_search_list_view', 'search_div', 'order_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

//style search------------------------------//
if($action=="create_style_search_list_view")
{
	extract($_REQUEST);
	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	list($company,$buyer,$search_type,$search_value,$cbo_year,$txt_style_ref_no,$txt_style_ref_id)=explode('**',$data);

	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
		}
		else
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";	
		}
	}
	if($db_type==0) if($cbo_year!=0) $job_cond=" and year(a.insert_date)='$cbo_year'";
	else if($cbo_year!=0) $job_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
	
	if($search_type==1 && $search_value!=''){
		$search_con=" and a.job_no_prefix_num='$search_value'";	
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no='$search_value'";	
	}

	
	if($db_type==0) $year_field="YEAR(a.insert_date) as job_year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as job_year";
	else $year_field="";
	
	if($buyer!=0) $buyer_cond="and a.buyer_name=$buyer"; else $buyer_cond="";
	$sql = "select a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,$year_field from wo_po_details_master a where a.company_name in($company) $buyer_cond $year_cond $job_cond $search_con and is_deleted=0 order by job_no_prefix_num"; 
	//echo $sql;
	echo create_list_view("list_view", "Style Ref No,Job No,Year","160,90,100","400","200",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,job_year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='hide_job_id' />";
	//echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='hide_job_no' />";
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_style_ref_no;?>';
	var style_id='<? echo $txt_style_ref_id;?>';
	//var style_des='<? echo $txt_style_ref;?>';
	//alert(style_id);
	if(style_no!="")
	{
		style_no_arr=style_no.split(",");
		style_id_arr=style_id.split(",");
		style_des_arr=style_des.split(",");
		var str_ref="";
		for(var k=0;k<style_no_arr.length; k++)
		{
			str_ref=style_no_arr[k]+'_'+style_id_arr[k];
			js_set_value(str_ref);
		}
	}
	</script>
    <?
	exit();
}


