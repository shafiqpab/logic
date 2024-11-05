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

//--------------------------------------------------------------------------------------------


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,20,21,22,23,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
	exit();
}


if($action=="report_generate")
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

	// echo $whereCon;
	
	$width=240;
	if($reportType==1 || $reportType==2)//confirm and projected
	{
		$is_confirm =($reportType==1)?1:2;
		$con = connect();
		execute_query("delete from tmp_poid where userid=".$user_id."");
		
		$sql= "SELECT a.FABRIC_COMPOSITION,a.COMPANY_NAME,a.JOB_NO,a.STYLE_REF_NO,a.BUYER_NAME,a.GAUGE,sum((b.PO_QUANTITY*a.TOTAL_SET_QNTY)) as PO_QUANTITY_PCS,MIN(b.SHIPMENT_DATE) as SHIPMENT_DATE
		from wo_po_details_master a,wo_po_break_down b 
		where a.job_no=b.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1
		$date_cond $whereCon and a.COMPANY_NAME in($cbo_company_name) and b.IS_CONFIRMED=$is_confirm
		
		group by a.FABRIC_COMPOSITION,a.COMPANY_NAME,a.BUYER_NAME,a.JOB_NO,a.STYLE_REF_NO,a.GAUGE
		order by a.FABRIC_COMPOSITION,a.COMPANY_NAME,a.BUYER_NAME,MIN(b.SHIPMENT_DATE)";//c.job_no
		
		 //echo $sql;
		$sql_result=sql_select($sql);
		$orderDataArr=array();$buyerWiseJobQty=array();
		foreach($sql_result as $row)
		{
			$orderDataArr[$row[BUYER_NAME]][$row[JOB_NO]]=$row;
			$buyerWiseJobQty[$row[BUYER_NAME]]+=$row[PO_QUANTITY_PCS];

			$r_id2=execute_query("insert into tmp_poid (userid, pono,type) values (".$user_id.",'".$row[JOB_NO]."',1".")");
		
		}
		
		
		
		//TNA----------------------
		$tnsSql="select a.JOB_NO,MIN(a.TASK_START_DATE) as TASK_START_DATE from TNA_PROCESS_MST a,tmp_poid b where a.TASK_NUMBER=47 and a.JOB_NO=b.pono and b.TYPE=1 AND b.userid=$user_id group by a.JOB_NO";	
		$tna_data_arr=return_library_array($tnsSql,'JOB_NO','TASK_START_DATE');
		//Exfactory--------------------------
		
		
		$exfactorySql="select a.job_no_mst, sum((CASE WHEN b.entry_form !=85 THEN b.ex_factory_qnty ELSE 0 END)-(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END)) as exfactory_qnty from wo_po_break_down a,pro_ex_factory_mst b,tmp_poid c,pro_ex_factory_delivery_mst d where d.id=b.DELIVERY_MST_ID $workingCompanyCon and a.id=b.po_break_down_id and a.job_no_mst=c.pono AND c.userid=$user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no_mst";
		$exfactorySqlRes=sql_select($exfactorySql);
		$ex_factory_qty_arr=array();
		foreach($exfactorySqlRes as $row)
		{
			$ex_factory_qty_arr[$row[csf('job_no_mst')]]=$row[csf('exfactory_qnty')];
		}
	

		$image_arr=return_library_array( "select a.MASTER_TBLE_ID,a.IMAGE_LOCATION from COMMON_PHOTO_LIBRARY a,tmp_poid tmp where a.FORM_NAME='knit_order_entry' and a.MASTER_TBLE_ID=tmp.pono and  tmp.userid=".$user_id." and a.FILE_TYPE=1",'MASTER_TBLE_ID','IMAGE_LOCATION');

		$composition_arr=return_library_array( "select a.job_no,a.fabric_description from wo_pre_cost_fabric_cost_dtls a,tmp_poid tmp where a.job_no=tmp.pono and  tmp.userid=".$user_id." and a.fabric_source in(1,2) and a.status_active=1 and a.is_deleted=0",'job_no','fabric_description');
		ob_start();
		?>
        <div align="center">
        <b style="float:left;">All Buyer Total: <?= array_sum($buyerWiseJobQty);?></b>
              <?php 
			  foreach($orderDataArr as $buyer_id=>$buyerDataArr){ ?>
                <table width="100%">
                    <tr>
                        <td style="border-bottom:1px solid #CCC"> 
                        <b>Buyer Name:</b><?= $buyer_name_arr[$buyer_id];?>,
                        <b>Buyer Total:</b><?= $buyerWiseJobQty[$buyer_id] ;?>
                        
                        </td>
                    </tr>
                </table>
                <table align="left"><tr>              
			  <?
			  $flag=0;
			  foreach($buyerDataArr as $rows){
			  
			  if($flag==5){echo "</tr><tr><td>";$flag=0;}
			  else{echo "<td>";}
			  ?> 
                <div style="float:left; width:<?= $width+4;?>px; margin:5px;;">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width;?>" class="rpt_table" bgcolor="#CCCCCC">
                    <tr>
                        <td colspan="2" align="center">
                        <img src="../../../<?= $image_arr[$rows[JOB_NO]]; ?>" height="200" align="">
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
                    <tr bgcolor="#FFF">
                        <td><strong>Yarn in House</strong></td><td><?= change_date_format($tna_data_arr[$rows[JOB_NO]]); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Shipment :</strong></td><td><?= change_date_format($rows[SHIPMENT_DATE]); ?></td>
                    </tr>
                    <tr bgcolor="#FFF">
                        <td><strong>Shipped Qty.</strong></td><td><?= $ex_factory_qty_arr[$rows[JOB_NO]]; ?></td>
                    </tr>
                    <tr>
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
         
    	<?
	}
	else if($reportType==4)//exfactory
	{
		$con = connect();
		execute_query("delete from tmp_poid where userid=".$user_id."");
		
		$exfactorySql="select b.JOB_NO_MST, sum((CASE WHEN c.entry_form !=85 THEN c.ex_factory_qnty ELSE 0 END)-(CASE WHEN c.entry_form=85 THEN c.ex_factory_qnty ELSE 0 END)) as exfactory_qnty from 
		wo_po_details_master a,
		wo_po_break_down b,pro_ex_factory_mst c,pro_ex_factory_delivery_mst d where a.job_no=b.JOB_NO_MST and d.id=c.DELIVERY_MST_ID $workingCompanyCon $date_cond $whereCon and a.COMPANY_NAME in($cbo_company_name) and b.id=c.po_break_down_id and b.status_active=1 and b.SHIPING_STATUS=3 and b.is_deleted=0 and c.status_active=1 and b.is_deleted=0 group by b.job_no_mst";
		$exfactorySqlRes=sql_select($exfactorySql);
		$ex_factory_qty_arr=array();
		foreach($exfactorySqlRes as $row)
		{
			$ex_factory_qty_arr[$row[JOB_NO_MST]]=$row[csf('exfactory_qnty')];
			$r_id2=execute_query("insert into tmp_poid (userid, pono,type) values (".$user_id.",'".$row[JOB_NO_MST]."',1".")");
		}
		
		
		
		
		
		
		$sql= "SELECT a.FABRIC_COMPOSITION,a.COMPANY_NAME,a.JOB_NO,a.STYLE_REF_NO,a.BUYER_NAME,a.GAUGE,sum((b.PO_QUANTITY*a.TOTAL_SET_QNTY)) as PO_QUANTITY_PCS,MIN(b.SHIPMENT_DATE) as SHIPMENT_DATE
		from wo_po_details_master a,wo_po_break_down b ,tmp_poid tmp
		where a.job_no=b.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1
		$date_cond $whereCon and a.COMPANY_NAME in($cbo_company_name) and b.IS_CONFIRMED in(1,2) and a.JOB_NO=tmp.pono  AND tmp.userid=$user_id
		group by a.FABRIC_COMPOSITION,a.COMPANY_NAME,a.BUYER_NAME,a.JOB_NO,a.STYLE_REF_NO,a.GAUGE
		order by a.FABRIC_COMPOSITION,a.COMPANY_NAME,a.BUYER_NAME,MIN(b.SHIPMENT_DATE)";//c.job_no
		
		   //echo $sql;
		$sql_result=sql_select($sql);
		$orderDataArr=array();$buyerWiseJobQty=array();
		foreach($sql_result as $row)
		{
			$orderDataArr[$row[BUYER_NAME]][$row[JOB_NO]]=$row;
			$buyerWiseJobQty[$row[BUYER_NAME]]+=$row[PO_QUANTITY_PCS];
		}
		
		
		
		//TNA----------------------
		$tnsSql="select a.JOB_NO,MIN(a.TASK_START_DATE) as TASK_START_DATE from TNA_PROCESS_MST a,tmp_poid b where a.TASK_NUMBER=47 and a.JOB_NO=b.pono AND b.userid=$user_id and b.TYPE=1 group by a.JOB_NO";	
		$tna_data_arr=return_library_array($tnsSql,'JOB_NO','TASK_START_DATE');
		//Exfactory--------------------------
		
		$image_arr=return_library_array( "select a.MASTER_TBLE_ID,a.IMAGE_LOCATION from COMMON_PHOTO_LIBRARY a,tmp_poid tmp where a.FORM_NAME='knit_order_entry' and a.MASTER_TBLE_ID=tmp.pono AND tmp.userid=$user_id and a.FILE_TYPE=1",'MASTER_TBLE_ID','IMAGE_LOCATION');
		
		$composition_arr=return_library_array( "select a.job_no,a.fabric_description from wo_pre_cost_fabric_cost_dtls a,tmp_poid tmp where a.job_no=tmp.pono and  tmp.userid=".$user_id." and a.fabric_source in(1,2) and a.status_active=1 and a.is_deleted=0",'job_no','fabric_description');



		
		ob_start();
		?>
		<div align="center">
              <b style="float:left;">All Buyer Total: <?= array_sum($buyerWiseJobQty);?></b>
			  <?php 
			  foreach($orderDataArr as $buyer_id=>$buyerDataArr){ ?>
                <table width="100%">
                    <tr>
                        <td style="border-bottom:1px solid #CCC"> 
                        <b>Buyer Name:</b><?= $buyer_name_arr[$buyer_id];?>,
                        <b>Buyer Total:</b><?= $buyerWiseJobQty[$buyer_id] ;?>
                        </td>
                    </tr>
                </table>
                <table align="left"><tr>              
			  <?
              $flag=0;
              foreach($buyerDataArr as $rows){
			  if($flag==5){echo "</tr><tr><td>";$flag=0;}
			  else{echo "<td>";}
				  ?> 
                <div style="float:left; width:<?= $width+4;?>px; margin:5px;;">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width;?>" class="rpt_table" bgcolor="#CCCCCC">
                    <tr>
                        <td colspan="2" align="center">
                        <img src="../../../<?= $image_arr[$rows[JOB_NO]]; ?>" height="200">
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
                    <tr bgcolor="#FFF">
                        <td><strong>Yarn in House</strong></td><td><?= change_date_format($tna_data_arr[$rows[JOB_NO]]); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Shipment :</strong></td><td><?= change_date_format($rows[SHIPMENT_DATE]); ?></td>
                    </tr>
                    <tr bgcolor="#FFF">
                        <td><strong>Shipped Qty.</strong></td><td><?= $ex_factory_qty_arr[$rows[JOB_NO]]; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Shipped %</strong></td><td><?= number_format(($ex_factory_qty_arr[$rows[JOB_NO]]/$rows[PO_QUANTITY_PCS])*100,2); ?></td>
                    </tr>
                </table>
                </div>

			<?php
            	$flag++;
			  }
			  	
				echo "</tr></table>";
			  }
			  ?>
		</div>
    	<?
	}
	else if($reportType==3)//Knit
	{
		$con = connect();
		execute_query("delete from tmp_poid where userid=".$user_id."");
		
		// 
		
		//  
		
		
		$orderSql="select a.FABRIC_COMPOSITION,a.COMPANY_NAME,a.JOB_NO,a.STYLE_REF_NO,a.BUYER_NAME,a.GAUGE,sum((b.PO_QUANTITY*a.TOTAL_SET_QNTY)) as PO_QUANTITY_PCS,MIN(b.SHIPMENT_DATE) as SHIPMENT_DATE from wo_po_details_master a , wo_po_break_down b where b.job_no_mst=a.job_no and b.is_deleted=0 and b.status_active=1 and a.garments_nature=100 and a.company_name in($cbo_company_name) $date_cond $whereCon and b.shiping_status=3 group by a.FABRIC_COMPOSITION,a.COMPANY_NAME,a.BUYER_NAME,a.JOB_NO,a.STYLE_REF_NO,a.GAUGE
		order by a.FABRIC_COMPOSITION,a.COMPANY_NAME,a.BUYER_NAME,MIN(b.SHIPMENT_DATE)";
		$orderSqlRes=sql_select($orderSql);
		foreach($orderSqlRes as $row)
		{
			$orderDataArr[$row[BUYER_NAME]][$row[JOB_NO]]=$row;
			$r_id2=execute_query("insert into tmp_poid (userid, pono,type) values (".$user_id.",'".$row[JOB_NO]."',1".")");
		}
		
		
		
		$closingSql="select B.JOB_NO_MST,A.PRODUCTION_QUANTITY,A.PO_BREAK_DOWN_ID from pro_garments_production_mst a,wo_po_break_down b,tmp_poid tmp where a.po_break_down_id=b.id and a.garments_nature=100 and a.production_type=52 and a.status_active=1  AND tmp.userid=$user_id and b.job_no_mst=tmp.pono";		
		$closingSqlRes=sql_select($closingSql);
		$closingDQtyArr=array();
		foreach($closingSqlRes as $row)
		{
			$closingDQtyArr[$row[JOB_NO_MST]]+=$row[PRODUCTION_QUANTITY];
		}
		
		
		
		//TNA----------------------
		$tnsSql="select a.JOB_NO,MIN(a.TASK_START_DATE) as TASK_START_DATE from TNA_PROCESS_MST a,tmp_poid b where a.TASK_NUMBER=47 and a.JOB_NO=b.pono AND b.userid=$user_id and b.TYPE=1 group by a.JOB_NO";	
		$tna_data_arr=return_library_array($tnsSql,'JOB_NO','TASK_START_DATE');
		//Exfactory--------------------------
		
		$image_arr=return_library_array( "select a.MASTER_TBLE_ID,a.IMAGE_LOCATION from COMMON_PHOTO_LIBRARY a,tmp_poid tmp where a.FORM_NAME='knit_order_entry' and a.MASTER_TBLE_ID=tmp.pono AND tmp.userid=$user_id and a.FILE_TYPE=1",'MASTER_TBLE_ID','IMAGE_LOCATION');
		
		$composition_arr=return_library_array( "select a.job_no,a.fabric_description from wo_pre_cost_fabric_cost_dtls a,tmp_poid tmp where a.job_no=tmp.pono and  tmp.userid=".$user_id." and a.fabric_source in(1,2) and a.status_active=1 and a.is_deleted=0",'job_no','fabric_description');



		
		ob_start();
		?>
		<div align="center">
              <b style="float:left;">All Buyer Total: <?= array_sum($buyerWiseJobQty);?></b>
			  <?php 
			  foreach($orderDataArr as $buyer_id=>$buyerDataArr){ ?>
                <table width="100%">
                    <tr>
                        <td style="border-bottom:1px solid #CCC"> 
                        <b>Buyer Name:</b><?= $buyer_name_arr[$buyer_id];?>,
                        <b>Buyer Total:</b><?= $buyerWiseJobQty[$buyer_id] ;?>
                        </td>
                    </tr>
                </table>
                <table align="left"><tr>              
			  <?
              $flag=0;
              foreach($buyerDataArr as $rows){
			  if($flag==5){echo "</tr><tr><td>";$flag=0;}
			  else{echo "<td>";}
				  ?> 
                <div style="float:left; width:<?= $width+4;?>px; margin:5px;;">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width;?>" class="rpt_table" bgcolor="#CCCCCC">
                    <tr>
                        <td colspan="2" align="center">
                        <img src="../../../<?= $image_arr[$rows[JOB_NO]]; ?>" height="200">
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
                    <tr bgcolor="#FFF">
                        <td><strong>Yarn in House</strong></td><td><?= change_date_format($tna_data_arr[$rows[JOB_NO]]); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Shipment :</strong></td><td><?= change_date_format($rows[SHIPMENT_DATE]); ?></td>
                    </tr>
                    
                    <tr bgcolor="#FFF">
                        <td><strong>Knitted Qty.</strong></td><td><?= $closingDQtyArr[$rows[JOB_NO]]; ?></td>
                    </tr>
                    <tr bgcolor="#FFF">
                        <td><strong>Shipped Qty.</strong></td><td><?= $ex_factory_qty_arr[$rows[JOB_NO]]; ?></td>
                    </tr>
                    
                    <tr>
                        <td><strong>Shipped %</strong></td><td><?= number_format(($ex_factory_qty_arr[$rows[JOB_NO]]/$rows[PO_QUANTITY_PCS])*100,2); ?></td>
                    </tr>
                </table>
                </div>

			<?php
            	$flag++;
			  }
			  	
				echo "</tr></table>";
			  }
			  ?>
		</div>
        
        
        
        
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
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "**$filename**$rpt_type";
	disconnect($con);
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

//style search------------------------------//
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
	$sql = "select a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,$year_field from wo_po_details_master a where a.company_name in($company) $buyer_cond $year_cond $job_cond $search_con and is_deleted=0 order by job_no_prefix_num"; 
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


