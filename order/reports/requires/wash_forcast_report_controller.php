<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All --", $selected, "load_drop_down( 'requires/wash_forcast_report_controller',this.value, 'load_drop_brand', 'brand_td' );load_drop_down( 'requires/wash_forcast_report_controller',this.value, 'load_drop_season', 'season_td' );" );   	 
	exit();
}

if($action=="load_drop_brand")
{
	echo create_drop_down( "cbo_brand_id", 70, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "-- All --", $selected, "" );   	 
	exit();
}


if($action=="load_drop_season")
{
	echo create_drop_down( "cbo_season_id", 70, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- All --", $selected, "" );   	 
	exit();
}




//--------------------------------------------------------------------------------------------------------------------


if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	$cbo_buyer_id 	= str_replace("'","",$cbo_buyer_id);
	$cbo_brand_id 	= str_replace("'","",$cbo_brand_id);
	$cbo_season_id 	= str_replace("'","",$cbo_season_id);
	$cbo_order_type = str_replace("'","",$cbo_order_type);
	$cbo_season_year = str_replace("'","",$cbo_season_year);
	$cbo_date_type 	= str_replace("'","",$cbo_date_type);
	$txt_date_from 	= str_replace("'","",$txt_date_from);
	$txt_date_to 	= str_replace("'","",$txt_date_to);
	$type 			= str_replace("'","",$type);
	
	
	//$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	//$company_library_short=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$item_arr=return_library_array( "select id, ITEM_NAME from LIB_GARMENT_ITEM", "id", "ITEM_NAME"  );
	$season_arr=return_library_array( "select id, SEASON_NAME from LIB_BUYER_SEASON", "id", "SEASON_NAME"  );
	$brand_arr=return_library_array( "select id, BRAND_NAME from lib_buyer_brand where IS_DELETED=0 and STATUS_ACTIVE=1", "id", "BRAND_NAME"  );
	$pro_dep_arr=return_library_array( "select id, DEPARTMENT_NAME from LIB_DEPARTMENT where IS_DELETED=0 and STATUS_ACTIVE=1", "id", "DEPARTMENT_NAME"  );
	$country_arr=return_library_array( "select id, COUNTRY_NAME from LIB_COUNTRY where IS_DELETED=0 and STATUS_ACTIVE=1", "id", "COUNTRY_NAME"  );
	$code_arr=return_library_array( "select id, ultimate_country_code from lib_country_loc_mapping where IS_DELETED=0 and STATUS_ACTIVE=1", "id", "ultimate_country_code"  );
	$marchentr_arr = return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");
	// $body_color = return_library_array("select style_refernce,color from wo_quotation_inquery","style_refernce","color");

	
	
	if($txt_date_from !="" && $txt_date_to !="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format($txt_date_from,"yyyy-mm-dd","");
			$end_date=change_date_format($txt_date_to,"yyyy-mm-dd","");
		}
		else
		{
			$start_date=date("j-M-Y",strtotime($txt_date_from));
			$end_date=date("j-M-Y",strtotime($txt_date_to));
		}
		
		
		if($cbo_date_type==1){$sql_cond.=" AND c.SHIPMENT_DATE BETWEEN '$start_date' and '$end_date'";}
		else if($cbo_date_type==2){$sql_cond.=" AND c.PUB_SHIPMENT_DATE BETWEEN '$start_date' and '$end_date'";}
		else if($cbo_date_type==3){$sql_cond.=" AND d.COUNTRY_SHIP_DATE BETWEEN '$start_date' and '$end_date'";}
	}
	
	$sql_cond.= ($cbo_company_id !=0) ? " AND A.COMPANY_NAME=$cbo_company_id" : "";
	$sql_cond.= ($cbo_buyer_id !=0) ? " AND A.BUYER_NAME=$cbo_buyer_id" : "";
	$sql_cond.= ($cbo_brand_id !=0) ? " AND A.BRAND_ID=$cbo_brand_id" : "";
	$sql_cond.= ($cbo_season_id !=0) ? " AND A.SEASON_BUYER_WISE=$cbo_season_id" : "";
	$sql_cond.= ($cbo_order_type !=0) ? " AND c.IS_CONFIRMED=$cbo_order_type" : "";
	$sql_cond.= ($cbo_season_year !=0) ? " AND a.season_year=$cbo_season_year" : "";


	
	
	$sql_order="select a.JOB_NO,a.STYLE_REF_NO,A.BUYER_NAME,a.DEALING_MARCHANT,a.SEASON_BUYER_WISE as SEASON,a.PRODUCT_DEPT,a.BRAND_ID,b.GMTS_ITEM_ID,c.id as PO_ID,c.GROUPING as FILE_NO,c.SHIPMENT_DATE,c.PUB_SHIPMENT_DATE,c.PO_NUMBER,d.CODE_ID,d.COUNTRY_ID,d.COLOR_NUMBER_ID,d.ORDER_QUANTITY,a.SEASON_YEAR from WO_PO_DETAILS_MASTER a,WO_PO_DETAILS_MAS_SET_DETAILS b,WO_PO_BREAK_DOWN c,WO_PO_COLOR_SIZE_BREAKDOWN d where a.id=b.job_id and b.job_id=c.job_id and c.job_id=c.job_id and a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and c.id=d.PO_BREAK_DOWN_ID $sql_cond order by a.JOB_NO,c.SHIPMENT_DATE"; //
	 //echo $sql_order;
	 
	$sql_order_result=sql_select($sql_order);
	$dataArr=array();
	foreach( $sql_order_result as $row ){
		$key=$row[JOB_NO].'**'.$row[GMTS_ITEM_ID];
		
		$dataArr[$key][FILE_NO][$row[FILE_NO]]=$row[FILE_NO];
		$dataArr[$key][PO_NUMBER][$row[PO_NUMBER]]=$row[PO_NUMBER];
		$dataArr[$key][PO_ID][$row[PO_ID]]=$row[PO_ID];
		$dataArr[$key][COUNTRY_ID][$row[COUNTRY_ID]]=$country_arr[$row[COUNTRY_ID]];
		$dataArr[$key][CODE_ID][$row[CODE_ID]]=$code_arr[$row[CODE_ID]];
		$dataArr[$key][ORDER_QUANTITY]+=$row[ORDER_QUANTITY];
		$dataArr[$key][SHIPMENT_DATE][]=$row[SHIPMENT_DATE];
		
		$dataArr[$key][JOB_NO]=$row[JOB_NO];
		$dataArr[$key][STYLE_REF_NO]=$row[STYLE_REF_NO];
		$dataArr[$key][BUYER_NAME]=$row[BUYER_NAME];
		$dataArr[$key][GMTS_ITEM_ID]=$row[GMTS_ITEM_ID];
		$dataArr[$key][SEASON]=$row[SEASON];
		$dataArr[$key][SEASON_YEAR]=$row[SEASON_YEAR];
		$dataArr[$key][BRAND_ID]=$row[BRAND_ID];
		$dataArr[$key][PRODUCT_DEPT]=$row[PRODUCT_DEPT];
		$dataArr[$key][MARCHANT]=$row[DEALING_MARCHANT];
		$dataArr[$key][FILENO]=$row[FILE_NO];
		
		$all_po_id_arr[$row[PO_ID]]=$row[PO_ID];
		$dataArr[$key][COLOR_QUANTITY][$row[COLOR_NUMBER_ID]]+=$row[ORDER_QUANTITY];
		$style_ref_arr[$row[FILE_NO]]=$row[FILE_NO];

	}
	unset($sql_order_result);
	
	
	
	
	$pre_cost_sql="select a.EMB_TYPE,b.JOB_NO,b.ITEM_NUMBER_ID,b.COLOR_NUMBER_ID  from WO_PRE_COST_EMBE_COST_DTLS a,WO_PRE_COS_EMB_CO_AVG_CON_DTLS b where a.id=b.PRE_COST_EMB_COST_DTLS_ID and a.JOB_NO=b.JOB_NO and a.EMB_TYPE>0 and b.REQUIRMENT>0 ".where_con_using_array($all_po_id_arr,0,'b.PO_BREAK_DOWN_ID');
	$pre_cost_sql_result=sql_select($pre_cost_sql);
	$preCostDataArr=array();
	$washTypeArr=array();
	$jobItemTypeWiseColor=array();
	foreach( $pre_cost_sql_result as $rows ){

		$washTypeArr[$rows[EMB_TYPE]]=$emblishment_wash_type[$rows[EMB_TYPE]];
		$jobItemTypeWiseColor[$rows[JOB_NO]][$rows[ITEM_NUMBER_ID]][$rows[EMB_TYPE]][$rows[COLOR_NUMBER_ID]]=$rows[COLOR_NUMBER_ID];
	}
	
	$width=(count($washTypeArr)*100)+1680;
	
	//echo count($washTypeArr);die;
	
	$body_wash_data = sql_select("select style_refernce,color from wo_quotation_inquery where status_active=1  ".where_con_using_array($style_ref_arr,1,'style_refernce')."");


	
		foreach($body_wash_data as $row){
			$body_color[$row[csf('style_refernce')]]['color']=$row[csf('color')];
		}

			// echo "<pre>";
			// print_r($body_color);

	// =================================== calculate rowspan =========================================
	ob_start();
	?>
	<div style="width:<?= $width+20;?>px">
	<style type="text/css">
		table tr td{ vertical-align: middle; }
	</style>
	<fieldset style="width:100%;">	
		<table width="<?= $width;?>">
			<tr class="form_caption">
				<td colspan="41" align="center"><h2>Sample Development Followup Report</h2></td>
			</tr>
			<tr class="form_caption">
				<td colspan="41" align="center"><? echo $company_library[$company_name]; ?></td>
			</tr>
		</table>
        
        <br />
        <table class="rpt_table" width="<?= $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
                <th width="35">Sl</th>
                <th width="100">Buyer</th>
				<th width="100">Brand/Dept't</th>           
                <th width="100">Season</th>
				<th width="100">Season Year</th>
				<th width="100">ITEMS</th>
                <th width="100">Dept't</th>
                <th width="100">Master style</th>
				<th width="100">Body /Wash Color</th>
                <th width="100">Merch number</th>
                <th width="60">Order No</th>
                <th width="100">Markets</th>
                <th width="100">CHANNEL</th>
                <th width="100">Order Quantity</th>
                <th width="80">SHIP CXL Date</th>
                <th width="80">Inspection Date</th>
                <th width="100">Merchant</th>
                <? foreach($washTypeArr as $type_id=>$type_value){?>
                   <th width="100"><p><?= $type_value;?></p></th>
                <? } ?>
                <th>Wash Type Total</th>
			</thead>
		</table>
		<div style="width:<?= $width+20;?>px; max-height:400px; overflow-y:auto;" id="scroll_body">
            <table class="rpt_table" width="<?= $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<tbody>
				<?
				$i=1;$item_qty_arr=array();
				foreach($dataArr as $key=>$rows){ 
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				$InspectionDate = date('Y-m-d', strtotime('-7 day', strtotime($rows[SHIPMENT_DATE][0]))); 
				
				
				?>
				<tr bgcolor="<?= $bgcolor;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor;?>')" id="tr_<?= $i; ?>">
					<td width="35"><?= $i; ?></td>
					<td width="100"><p><?= $buyer_arr[$rows[BUYER_NAME]];?></p></td>
					<td width="100"><p><?= $brand_arr[$rows[BRAND_ID]];?></p></td>					
					<td width="100"><p><?= $season_arr[$rows[SEASON]];?></p></td>
					<td width="100"><p><?= $rows[SEASON_YEAR];?></p></td>
					<td width="100"><p><?= $item_arr[$rows[GMTS_ITEM_ID]];?></p></td>
					<td width="100"><p><?= $pro_dep_arr[$rows[PRODUCT_DEPT]];?></p></td>
					<td width="100"><p><?= implode(',',$rows[FILE_NO]);?></p></td>
					<td width="100"><p><?=$body_color[$rows[FILENO]]['color'];?></p></td>
					<td width="100"><p><?= $rows[STYLE_REF_NO];?></p></td>
					<td width="60" align="center" title="<?= $rows[JOB_NO];?>"><a href="javascript:openPopup('po_dtls','<?= implode(',',$rows[PO_ID]).'__'.$rows[GMTS_ITEM_ID];?>')">View</a></td>
					<td width="100"><p><?= implode(',',$rows[COUNTRY_ID]);?></p></td>
					<td width="100"><p><?= implode(',',$rows[CODE_ID]);?></p></td>
					<td width="100" align="right"><?= $rows[ORDER_QUANTITY]?></td>
					<td width="80" align="center"><?= change_date_format($rows[SHIPMENT_DATE][0])?></td>
					<td width="80" align="center"><?= $InspectionDate;?></td>
					<td width="100"><?= $marchentr_arr[$rows[MARCHANT]];?></td>
                    <? 
					$type_qty=0;
					foreach($washTypeArr as $type_id=>$type_value){
						$type_wise_color_qty=0;
						foreach($jobItemTypeWiseColor[$rows[JOB_NO]][$rows[GMTS_ITEM_ID]][$type_id] as $color_id){
							$type_wise_color_qty+=$rows[COLOR_QUANTITY][$color_id];
						}
						$type_qty+=$type_wise_color_qty;
						$item_qty_arr[$type_id]+=$type_wise_color_qty;
						
						?>
                    	<td width="100" align="right"><?= $type_wise_color_qty;?></td>
                    <? } ?>
					<td align="right"><?= $type_qty;?></td>
				</tr>
				<?
				$i++;	
				}
				?>
					
				</tbody>
			</table>
		</div>
		<div class="tbl-bottom">
			<table class="rpt_table" width="<?= $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tfoot>
					<th width="35"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="60"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="100"></th>
                    <? foreach($washTypeArr as $type_id=>$type_value){?>
                    	<th width="100" align="right"><?= $item_qty_arr[$type_id];?></th>
                    <? } ?>
                    <th align="right"><?= array_sum($item_qty_arr);?></th>
				</tfoot>
			</table>
		</div>
		</fieldset>
	</div>
	<?
	
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows";
	exit();	
}


if($action=="report_generate_buyer_summary")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	$cbo_buyer_id 	= str_replace("'","",$cbo_buyer_id);
	$cbo_brand_id 	= str_replace("'","",$cbo_brand_id);
	$cbo_season_id 	= str_replace("'","",$cbo_season_id);
	$cbo_order_type = str_replace("'","",$cbo_order_type);
	$cbo_date_type 	= str_replace("'","",$cbo_date_type);
	$txt_date_from 	= str_replace("'","",$txt_date_from);
	$txt_date_to 	= str_replace("'","",$txt_date_to);
	$type 			= str_replace("'","",$type);
	
	
	//$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	//$company_library_short=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$item_arr=return_library_array( "select id, ITEM_NAME from LIB_GARMENT_ITEM", "id", "ITEM_NAME"  );
	$season_arr=return_library_array( "select id, SEASON_NAME from LIB_BUYER_SEASON", "id", "SEASON_NAME"  );
	$brand_arr=return_library_array( "select id, BRAND_NAME from lib_buyer_brand where IS_DELETED=0 and STATUS_ACTIVE=1", "id", "BRAND_NAME"  );
	$pro_dep_arr=return_library_array( "select id, DEPARTMENT_NAME from LIB_DEPARTMENT where IS_DELETED=0 and STATUS_ACTIVE=1", "id", "DEPARTMENT_NAME"  );
	$country_arr=return_library_array( "select id, COUNTRY_NAME from LIB_COUNTRY where IS_DELETED=0 and STATUS_ACTIVE=1", "id", "COUNTRY_NAME"  );
	$code_arr=return_library_array( "select id, ultimate_country_code from lib_country_loc_mapping where IS_DELETED=0 and STATUS_ACTIVE=1", "id", "ultimate_country_code"  );
	$marchentr_arr = return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");


	
	
	if($txt_date_from !="" && $txt_date_to !="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format($txt_date_from,"yyyy-mm-dd","");
			$end_date=change_date_format($txt_date_to,"yyyy-mm-dd","");
		}
		else
		{
			$start_date=date("j-M-Y",strtotime($txt_date_from));
			$end_date=date("j-M-Y",strtotime($txt_date_to));
		}
		
		
		if($cbo_date_type==1){$sql_cond.=" AND c.SHIPMENT_DATE BETWEEN '$start_date' and '$end_date'";}
		else if($cbo_date_type==2){$sql_cond.=" AND c.PUB_SHIPMENT_DATE BETWEEN '$start_date' and '$end_date'";}
		else if($cbo_date_type==3){$sql_cond.=" AND d.COUNTRY_SHIP_DATE BETWEEN '$start_date' and '$end_date'";}
	}
	
	$sql_cond.= ($cbo_company_id !=0) ? " AND A.COMPANY_NAME=$cbo_company_id" : "";
	$sql_cond.= ($cbo_buyer_id !=0) ? " AND A.BUYER_NAME=$cbo_buyer_id" : "";
	$sql_cond.= ($cbo_brand_id !=0) ? " AND A.BRAND_ID=$cbo_brand_id" : "";
	$sql_cond.= ($cbo_season_id !=0) ? " AND A.SEASON_BUYER_WISE=$cbo_season_id" : "";
	$sql_cond.= ($cbo_order_type !=0) ? " AND c.IS_CONFIRMED=$cbo_order_type" : "";


	
	
	$sql_order="select a.JOB_NO,a.STYLE_REF_NO,A.BUYER_NAME,a.DEALING_MARCHANT,a.SEASON_BUYER_WISE as SEASON,a.PRODUCT_DEPT,a.BRAND_ID,b.GMTS_ITEM_ID,c.id as PO_ID,c.GROUPING as FILE_NO,c.SHIPMENT_DATE,c.PUB_SHIPMENT_DATE,c.PO_NUMBER,d.CODE_ID,d.COUNTRY_ID,d.COLOR_NUMBER_ID,d.ORDER_QUANTITY from WO_PO_DETAILS_MASTER a,WO_PO_DETAILS_MAS_SET_DETAILS b,WO_PO_BREAK_DOWN c,WO_PO_COLOR_SIZE_BREAKDOWN d where a.id=b.job_id and b.job_id=c.job_id and c.job_id=c.job_id and a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and c.id=d.PO_BREAK_DOWN_ID $sql_cond order by a.JOB_NO,c.SHIPMENT_DATE"; //
	 //echo $sql_order;
	$sql_order_result=sql_select($sql_order);
	$dataArr=array();
	foreach( $sql_order_result as $row ){
		$key=$row[BUYER_NAME];
		
		$dataArr[$key][FILE_NO][$row[FILE_NO]]=$row[FILE_NO];
		$dataArr[$key][PO_NUMBER][$row[PO_NUMBER]]=$row[PO_NUMBER];
		$dataArr[$key][PO_ID][$row[PO_ID]]=$row[PO_ID];
		$dataArr[$key][COUNTRY_ID][$row[COUNTRY_ID]]=$country_arr[$row[COUNTRY_ID]];
		$dataArr[$key][CODE_ID][$row[CODE_ID]]=$code_arr[$row[CODE_ID]];
		$dataArr[$key][ORDER_QUANTITY]+=$row[ORDER_QUANTITY];
		$dataArr[$key][SHIPMENT_DATE][]=$row[SHIPMENT_DATE];
		
		$dataArr[$key][JOB_NO]=$row[JOB_NO];
		$dataArr[$key][STYLE_REF_NO]=$row[STYLE_REF_NO];
		$dataArr[$key][BUYER_NAME]=$row[BUYER_NAME];
		$dataArr[$key][GMTS_ITEM_ID]=$row[GMTS_ITEM_ID];
		$dataArr[$key][SEASON]=$row[SEASON];
		$dataArr[$key][BRAND_ID]=$row[BRAND_ID];
		$dataArr[$key][PRODUCT_DEPT]=$row[PRODUCT_DEPT];
		$dataArr[$key][MARCHANT]=$row[DEALING_MARCHANT];
		
		$all_po_id_arr[$row[PO_ID]]=$row[PO_ID];
		$dataArr[$key][COLOR_QUANTITY][$row[COLOR_NUMBER_ID]]+=$row[ORDER_QUANTITY];
	
	}
	unset($sql_order_result);
	
	
	
	
	$pre_cost_sql="select a.EMB_TYPE,b.JOB_NO,b.ITEM_NUMBER_ID,b.COLOR_NUMBER_ID  from WO_PRE_COST_EMBE_COST_DTLS a,WO_PRE_COS_EMB_CO_AVG_CON_DTLS b where a.id=b.PRE_COST_EMB_COST_DTLS_ID and a.JOB_NO=b.JOB_NO and a.EMB_TYPE>0 and b.REQUIRMENT>0 ".where_con_using_array($all_po_id_arr,0,'b.PO_BREAK_DOWN_ID');
	//echo $pre_cost_sql;
	$pre_cost_sql_result=sql_select($pre_cost_sql);
	$preCostDataArr=array();
	$washTypeArr=array();
	$jobItemTypeWiseColor=array();
	foreach( $pre_cost_sql_result as $rows ){

		$washTypeArr[$rows[EMB_TYPE]]=$emblishment_wash_type[$rows[EMB_TYPE]];
		$jobItemTypeWiseColor[$rows[JOB_NO]][$rows[ITEM_NUMBER_ID]][$rows[EMB_TYPE]][$rows[COLOR_NUMBER_ID]]=$rows[COLOR_NUMBER_ID];
	}
	
	$width=(count($washTypeArr)*100)+360;
	
	//echo count($washTypeArr);die;
	
	
	// =================================== calculate rowspan =========================================
	ob_start();
	?>
	<div style="width:<?= $width+20;?>px">
	<style type="text/css">
		table tr td{ vertical-align: middle; }
	</style>
	<fieldset style="width:100%;">	
		<table width="<?= $width;?>">
			<tr class="form_caption">
				<td colspan="41" align="center"><h2>Sample Development Followup Report</h2></td>
			</tr>
			<tr class="form_caption">
				<td colspan="41" align="center"><? echo $company_library[$company_name]; ?></td>
			</tr>
		</table>
        
        <br />
        <table class="rpt_table" width="<?= $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
                <th width="35">Sl</th>
                <th width="100">Buyer</th>
                <th width="100">Order Quantity</th>
                <? foreach($washTypeArr as $type_id=>$type_value){?>
                   <th width="100"><p><?= $type_value;?></p></th>
                <? } ?>
                <th>Wash Type Total</th>
			</thead>
		</table>
		<div style="width:<?= $width+20;?>px; max-height:400px; overflow-y:auto;" id="scroll_body">
            <table class="rpt_table" width="<?= $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<tbody>
				<?
				$i=1;$item_qty_arr=array();
				foreach($dataArr as $key=>$rows){ 
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				$InspectionDate = date('Y-m-d', strtotime('-7 day', strtotime($rows[SHIPMENT_DATE][0]))); 
				
				
				?>
				<tr bgcolor="<?= $bgcolor;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor;?>')" id="tr_<?= $i; ?>">
					<td width="35"><?= $i; ?></td>
					<td width="100"><p><?= $buyer_arr[$rows[BUYER_NAME]];?></p></td>

					<td width="100" align="right"><?= $rows[ORDER_QUANTITY]?></td>
                    <? 
					$type_qty=0;
					foreach($washTypeArr as $type_id=>$type_value){
						$type_wise_color_qty=0;
						foreach($jobItemTypeWiseColor[$rows[JOB_NO]][$rows[GMTS_ITEM_ID]][$type_id] as $color_id){
							$type_wise_color_qty+=$rows[COLOR_QUANTITY][$color_id];
						}
						$type_qty+=$type_wise_color_qty;
						$item_qty_arr[$type_id]+=$type_wise_color_qty;
						
						?>
                    	<td width="100" align="right"><?= $type_wise_color_qty;?></td>
                    <? } ?>
					<td align="right"><?= $type_qty;?></td>
				</tr>
				<?
				$i++;	
				}
				?>
					
				</tbody>
			</table>
		</div>
		<div class="tbl-bottom">
			<table class="rpt_table" width="<?= $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tfoot>
					<th width="35"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <? foreach($washTypeArr as $type_id=>$type_value){?>
                    	<th width="100" align="right"><?= $item_qty_arr[$type_id];?></th>
                    <? } ?>
                    <th align="right"><?= array_sum($item_qty_arr);?></th>
				</tfoot>
			</table>
		</div>
		</fieldset>
	</div>
	<?
	
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows";
	exit();	
}



if($action=="report_generate_monthly_summary")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	$cbo_buyer_id 	= str_replace("'","",$cbo_buyer_id);
	$cbo_brand_id 	= str_replace("'","",$cbo_brand_id);
	$cbo_season_id 	= str_replace("'","",$cbo_season_id);
	$cbo_order_type = str_replace("'","",$cbo_order_type);
	$cbo_date_type 	= str_replace("'","",$cbo_date_type);
	$txt_date_from 	= str_replace("'","",$txt_date_from);
	$txt_date_to 	= str_replace("'","",$txt_date_to);
	$type 			= str_replace("'","",$type);
	
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$item_arr=return_library_array( "select id, ITEM_NAME from LIB_GARMENT_ITEM", "id", "ITEM_NAME"  );
	$season_arr=return_library_array( "select id, SEASON_NAME from LIB_BUYER_SEASON", "id", "SEASON_NAME"  );
	$brand_arr=return_library_array( "select id, BRAND_NAME from lib_buyer_brand where IS_DELETED=0 and STATUS_ACTIVE=1", "id", "BRAND_NAME"  );
	$pro_dep_arr=return_library_array( "select id, DEPARTMENT_NAME from LIB_DEPARTMENT where IS_DELETED=0 and STATUS_ACTIVE=1", "id", "DEPARTMENT_NAME"  );
	$country_arr=return_library_array( "select id, COUNTRY_NAME from LIB_COUNTRY where IS_DELETED=0 and STATUS_ACTIVE=1", "id", "COUNTRY_NAME"  );
	$code_arr=return_library_array( "select id, ultimate_country_code from lib_country_loc_mapping where IS_DELETED=0 and STATUS_ACTIVE=1", "id", "ultimate_country_code"  );
	$marchentr_arr = return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");


	
	
	if($txt_date_from !="" && $txt_date_to !="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format($txt_date_from,"yyyy-mm-dd","");
			$end_date=change_date_format($txt_date_to,"yyyy-mm-dd","");
		}
		else
		{
			$start_date=date("j-M-Y",strtotime($txt_date_from));
			$end_date=date("j-M-Y",strtotime($txt_date_to));
		}
		
		
		if($cbo_date_type==1){$sql_cond.=" AND c.SHIPMENT_DATE BETWEEN '$start_date' and '$end_date'";}
		else if($cbo_date_type==2){$sql_cond.=" AND c.PUB_SHIPMENT_DATE BETWEEN '$start_date' and '$end_date'";}
		else if($cbo_date_type==3){$sql_cond.=" AND d.COUNTRY_SHIP_DATE BETWEEN '$start_date' and '$end_date'";}
	}
	
	$sql_cond.= ($cbo_company_id !=0) ? " AND A.COMPANY_NAME=$cbo_company_id" : "";
	$sql_cond.= ($cbo_buyer_id !=0) ? " AND A.BUYER_NAME=$cbo_buyer_id" : "";
	$sql_cond.= ($cbo_brand_id !=0) ? " AND A.BRAND_ID=$cbo_brand_id" : "";
	$sql_cond.= ($cbo_season_id !=0) ? " AND A.SEASON_BUYER_WISE=$cbo_season_id" : "";
	$sql_cond.= ($cbo_order_type !=0) ? " AND c.IS_CONFIRMED=$cbo_order_type" : "";


	
	
	$sql_order="select a.JOB_NO,a.STYLE_REF_NO,A.BUYER_NAME,a.DEALING_MARCHANT,a.SEASON_BUYER_WISE as SEASON,a.PRODUCT_DEPT,a.BRAND_ID,b.GMTS_ITEM_ID,c.id as PO_ID,c.GROUPING as FILE_NO,c.SHIPMENT_DATE,c.PUB_SHIPMENT_DATE,c.PO_NUMBER,d.CODE_ID,d.COUNTRY_ID,d.COLOR_NUMBER_ID,d.ORDER_QUANTITY from WO_PO_DETAILS_MASTER a,WO_PO_DETAILS_MAS_SET_DETAILS b,WO_PO_BREAK_DOWN c,WO_PO_COLOR_SIZE_BREAKDOWN d where a.id=b.job_id and b.job_id=c.job_id and c.job_id=c.job_id and a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and c.id=d.PO_BREAK_DOWN_ID $sql_cond order by a.JOB_NO,c.SHIPMENT_DATE"; //
	 //echo $sql_order;
	$sql_order_result=sql_select($sql_order);
	$dataArr=array();
	foreach( $sql_order_result as $row ){
		$key=$row[BUYER_NAME];
		$monthYear = date('M-Y', strtotime($row[SHIPMENT_DATE])); 
		
		
		$dataArr[$monthYear][$key][FILE_NO][$row[FILE_NO]]=$row[FILE_NO];
		$dataArr[$monthYear][$key][PO_NUMBER][$row[PO_NUMBER]]=$row[PO_NUMBER];
		$dataArr[$monthYear][$key][PO_ID][$row[PO_ID]]=$row[PO_ID];
		$dataArr[$monthYear][$key][COUNTRY_ID][$row[COUNTRY_ID]]=$country_arr[$row[COUNTRY_ID]];
		$dataArr[$monthYear][$key][CODE_ID][$row[CODE_ID]]=$code_arr[$row[CODE_ID]];
		$dataArr[$monthYear][$key][ORDER_QUANTITY]+=$row[ORDER_QUANTITY];
		$dataArr[$monthYear][$key][SHIPMENT_DATE][]=$row[SHIPMENT_DATE];
		
		$dataArr[$monthYear][$key][JOB_NO]=$row[JOB_NO];
		$dataArr[$monthYear][$key][STYLE_REF_NO]=$row[STYLE_REF_NO];
		$dataArr[$monthYear][$key][BUYER_NAME]=$row[BUYER_NAME];
		$dataArr[$monthYear][$key][GMTS_ITEM_ID]=$row[GMTS_ITEM_ID];
		$dataArr[$monthYear][$key][SEASON]=$row[SEASON];
		$dataArr[$monthYear][$key][BRAND_ID]=$row[BRAND_ID];
		$dataArr[$monthYear][$key][PRODUCT_DEPT]=$row[PRODUCT_DEPT];
		$dataArr[$monthYear][$key][MARCHANT]=$row[DEALING_MARCHANT];
		
		$all_po_id_arr[$row[PO_ID]]=$row[PO_ID];
		$dataArr[$monthYear][$key][COLOR_QUANTITY][$row[COLOR_NUMBER_ID]]+=$row[ORDER_QUANTITY];
	
	}
	unset($sql_order_result);
	
	
	
	
	$pre_cost_sql="select a.EMB_TYPE,b.JOB_NO,b.ITEM_NUMBER_ID,b.COLOR_NUMBER_ID  from WO_PRE_COST_EMBE_COST_DTLS a,WO_PRE_COS_EMB_CO_AVG_CON_DTLS b where a.id=b.PRE_COST_EMB_COST_DTLS_ID and a.JOB_NO=b.JOB_NO and a.EMB_TYPE>0 and b.REQUIRMENT>0 ".where_con_using_array($all_po_id_arr,0,'b.PO_BREAK_DOWN_ID');
	//echo $pre_cost_sql;
	$pre_cost_sql_result=sql_select($pre_cost_sql);
	$preCostDataArr=array();
	$washTypeArr=array();
	$jobItemTypeWiseColor=array();
	foreach( $pre_cost_sql_result as $rows ){

		$washTypeArr[$rows[EMB_TYPE]]=$emblishment_wash_type[$rows[EMB_TYPE]];
		$jobItemTypeWiseColor[$rows[JOB_NO]][$rows[ITEM_NUMBER_ID]][$rows[EMB_TYPE]][$rows[COLOR_NUMBER_ID]]=$rows[COLOR_NUMBER_ID];
	}
	
	$width=(count($washTypeArr)*100)+460;
	
	//echo count($washTypeArr);die;
	
	
	// =================================== calculate rowspan =========================================
	ob_start();
	?>
	<div style="width:<?= $width+20;?>px">
	<style type="text/css">
		table tr td{ vertical-align: middle; }
	</style>
	<fieldset style="width:100%;">	
		<table width="<?= $width;?>">
			<tr class="form_caption">
				<td colspan="41" align="center"><h2>Sample Development Followup Report</h2></td>
			</tr>
			<tr class="form_caption">
				<td colspan="41" align="center"><? echo $company_library[$company_name]; ?></td>
			</tr>
		</table>
        
        <br />
        <table class="rpt_table" width="<?= $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
                <th width="35">Sl</th>
                <th width="100">Month</th>
                <th width="100">Buyer</th>
                <th width="100">Order Quantity</th>
                <? foreach($washTypeArr as $type_id=>$type_value){?>
                   <th width="100"><p><?= $type_value;?></p></th>
                <? } ?>
                <th>Wash Type Total</th>
			</thead>
		</table>
		<div style="width:<?= $width+20;?>px; max-height:400px; overflow-y:auto;" id="scroll_body">
            <table class="rpt_table" width="<?= $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<tbody>
				<?
				$i=1;$s=1;$item_qty_arr=array();
				foreach($dataArr as $monthYear=>$rowArr){ 
				$item_sub_qty_arr=array();$monthly_po_qty=0;
				$flag=0;
				foreach($rowArr as $key=>$rows){ 
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				$InspectionDate = date('Y-m-d', strtotime('-7 day', strtotime($rows[SHIPMENT_DATE][0]))); 
				$monthly_po_qty+=$rows[ORDER_QUANTITY];
					if($flag==0){
				?>
				   
                   <tr>
                    	<td rowspan="<?= count($rowArr);?>" width="35"><?= $s; ?></td>
                        <td rowspan="<?= count($rowArr);?>" width="100"><?= $monthYear;?></td>
                    <?
					}else{
                    ?>

                
                
                <tr bgcolor="<?= $bgcolor;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor;?>')" id="tr_<?= $i; ?>">
                <? } ?>
                
					<td width="100"><p><?= $buyer_arr[$rows[BUYER_NAME]];?></p></td>

					<td width="100" align="right"><?= $rows[ORDER_QUANTITY]?></td>
                    <? 
					$type_qty=0;
					foreach($washTypeArr as $type_id=>$type_value){
						$type_wise_color_qty=0;
						foreach($jobItemTypeWiseColor[$rows[JOB_NO]][$rows[GMTS_ITEM_ID]][$type_id] as $color_id){
							$type_wise_color_qty+=$rows[COLOR_QUANTITY][$color_id];
						}
						$type_qty+=$type_wise_color_qty;
						$item_qty_arr[$type_id]+=$type_wise_color_qty;
						$item_sub_qty_arr[$type_id]+=$type_wise_color_qty;
						
						?>
                    	<td width="100" align="right"><?= $type_wise_color_qty;?></td>
                    <? } ?>
					<td align="right"><?= $type_qty;?></td>
				</tr>
				<?
				$i++;$flag=1;	
				}
				$s++;
				?>
                	<tr bgcolor="#999999">
                    	<td colspan="3" align="right">Total:</td>
                    	<td align="right"><?= $monthly_po_qty;?></td>
						<? foreach($washTypeArr as $type_id=>$type_value){?>
                           <td align="right"><?= $item_sub_qty_arr[$type_id];?></td>
                        <? } ?>
                        <td align="right"><?= array_sum($item_sub_qty_arr);?></td>
                    </tr>
                
                <?
				}
				?>
					
				</tbody>
			</table>
		</div>
		<div class="tbl-bottom">
			<table class="rpt_table" width="<?= $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tfoot>
					<th width="35"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <? foreach($washTypeArr as $type_id=>$type_value){?>
                    	<th width="100" align="right"><?= $item_qty_arr[$type_id];?></th>
                    <? } ?>
                    <th align="right"><?= array_sum($item_qty_arr);?></th>
				</tfoot>
			</table>
		</div>
		</fieldset>
	</div>
	<?
	
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows";
	exit();	
}



if($action=="po_dtls"){
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($po_id,$item_id)=explode('__',$data);
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$country_arr=return_library_array( "select id, COUNTRY_NAME from LIB_COUNTRY where IS_DELETED=0 and STATUS_ACTIVE=1", "id", "COUNTRY_NAME"  );
	$color_arr=return_library_array( "select id, COLOR_NAME from LIB_COLOR", "id", "COLOR_NAME"  );
	$size_arr=return_library_array( "select id, SIZE_NAME from LIB_SIZE", "id", "SIZE_NAME"  );
	
	
	
	
	
	$sql_order="select a.JOB_NO, a.STYLE_REF_NO,  A.BUYER_NAME, c.GROUPING as FILE_NO,c.PO_NUMBER, d.COUNTRY_ID,  d.COLOR_NUMBER_ID,  d.SIZE_NUMBER_ID from WO_PO_DETAILS_MASTER a,WO_PO_DETAILS_MAS_SET_DETAILS b,WO_PO_BREAK_DOWN c,WO_PO_COLOR_SIZE_BREAKDOWN d where a.id=b.job_id and b.job_id=c.job_id and c.job_id=c.job_id and a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and c.id=d.PO_BREAK_DOWN_ID and d.PO_BREAK_DOWN_ID in($po_id) and b.GMTS_ITEM_ID=$item_id 
	group by a.JOB_NO, a.STYLE_REF_NO,  A.BUYER_NAME, c.GROUPING,c.PO_NUMBER, d.COUNTRY_ID,  d.COLOR_NUMBER_ID,  d.SIZE_NUMBER_ID
	order by d.COLOR_NUMBER_ID,d.SIZE_NUMBER_ID"; //
	//echo $sql_order;
	$sql_order_result=sql_select($sql_order);
	
	
	
	?>
        <table class="rpt_table" width="" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
                <th width="35">Sl</th>
                <th width="100">Buyer</th>
                <th width="100">Master Style</th>
                <th width="100">Merch Number</th>
                <th width="100">Order No</th>
                <th width="100">Country</th>
                <th width="100">Color</th>
                <th width="100">Size</th>
			</thead>
			<tbody>
                <? 
				$i=1;
				foreach($sql_order_result as $rows){ 
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				?>
                <tr bgcolor="<?= $bgcolor;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor;?>')" id="tr_<?= $i; ?>">
                    <td width="35"><?= $i;?></td>
                    <td width="100"><p><?= $buyer_arr[$rows[BUYER_NAME]];?></p></td>
                    <td width="100"><p><?= $rows[FILE_NO];?></p></td>
                    <td width="100"><p><?= $rows[STYLE_REF_NO];?></p></td>
                    <td width="100"><p><?= $rows[PO_NUMBER];?></p></td>
                    <td width="100"><?= $country_arr[$rows[COUNTRY_ID]];?></td>
                    <td width="100"><?= $color_arr[$rows[COLOR_NUMBER_ID]];?></td>
                    <td width="100"><?= $size_arr[$rows[SIZE_NUMBER_ID]];?></td>
                </tr>
                <? $i++;} ?>
			</tbody>
		</table>


<?	
	
}



//disconnect($con);
?>