<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_buyer")
{
	if($data!=0)
	{
		echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0); 
	}
	else
	{
		echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0  $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",0,"" );
	}
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 100, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/ref_and_cut_wise_production_status_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 );     	 
}

if ($action=="load_drop_down_floor")
{ 
	echo create_drop_down( "cbo_floor_name", 100, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process=5 order by floor_name","id,floor_name", 1, "-- Select --", $selected,0 ); 
	exit();    	 
}

if($action == 'report_gen_show_button') //Show button
{

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//print_r($_REQUEST); exit();
	$cbo_company_name		= str_replace("'","",$cbo_company_name);
	$cbo_po_company			= str_replace("'","",$cbo_po_company);
	$cbo_location			= str_replace("'","",$cbo_location);
	$cbo_buyer_name			= str_replace("'","",$cbo_buyer_name);
	$cbo_floor_name			= trim(str_replace("'","",$cbo_floor_name));
	$txt_cuttiong_no		= str_replace("'","",$txt_cuttiong_no);
	$internal_ref			= trim(str_replace("'","",$internal_ref));

	$txt_date_from			= str_replace("'","",$txt_date_from);
	$txt_date_to			= str_replace("'","",$txt_date_to);

	$companyArr 			= return_library_array("select id,company_name from lib_company", "id", "company_name");
    $companyArr[0] 			= "All Company";
    $buyerArr 				= return_library_array("select id, BUYER_NAME from LIB_BUYER", "id", "BUYER_NAME");
    $com_dtls 				= fnc_company_location_address($cbo_company_name, "", 1);

	$filter = '';
	if($cbo_company_name != 0 && $cbo_po_company != 0 && $cbo_location != 0){
		$filter .= " a.company_name='$cbo_company_name' and d.serving_company='$cbo_po_company' and d.location='$cbo_location' ";
		if($txt_date_from != '' && $txt_date_to != ''){
			$filter .= " and d.production_date between '$txt_date_from' and '$txt_date_to' ";
		}else{
			if($cbo_buyer_name==0 && $cbo_floor_name==0 && $txt_cuttiong_no=='' && $internal_ref==''){
				echo "Date from & to is required!"; exit();
			}else{
				//filter ....
				if($cbo_buyer_name 	!= 0) 	$filter .= " and a.buyer_name='$cbo_buyer_name' ";
				if($cbo_floor_name 	!= 0) 	$filter .= " and d.floor_id='$cbo_floor_name' ";
				if($txt_cuttiong_no != '') 	$filter .= " and e.cut_no='$txt_cuttiong_no' ";
				if($internal_ref 	!= '') 	$filter .= " and b.grouping='$internal_ref' ";
			}
		}
	}else{
		echo "Compnay, Working Factory & Location is required!"; exit();
	}

	$production_type="1,2,3,4,5";
	$sql = "SELECT  d.serving_company, a.job_no, b.po_number, a.buyer_name, a.style_ref_no, b.file_no, b.grouping,
					c.item_number_id, e.production_type,d.embel_name, e.production_qnty, d.production_date, d.cut_no,

					CASE WHEN e.production_type =1 THEN e.production_qnty ELSE 0 END AS cutting_qty,
					CASE WHEN e.production_type =2 THEN e.production_qnty ELSE 0 END AS print_issue,
					CASE WHEN e.production_type =3 THEN e.production_qnty ELSE 0 END AS print_recv,
					CASE WHEN e.production_type =4 THEN e.production_qnty ELSE 0 END AS sewing_in,
					CASE WHEN e.production_type =5 THEN e.production_qnty ELSE 0 END AS sewing_out
			
			FROM 
				wo_po_details_master a,
				wo_po_break_down b,
				wo_po_color_size_breakdown c,
				pro_garments_production_mst d,
				pro_garments_production_dtls e
			WHERE 
				a.id = b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and b.id = d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and e.production_type IN($production_type) and $filter
				";
	//echo $sql; exit();
	$results = sql_select($sql);

	$dataArray = array();
	foreach($results as $result){
		$dataArray[$result['SERVING_COMPANY']] [$result['JOB_NO']] [$result['PO_NUMBER']] [$result['STYLE_REF_NO']] [$result['ITEM_NUMBER_ID']] [$result['PRODUCTION_TYPE']] += $result['PRODUCTION_QNTY'];

		$dataArray[$result['SERVING_COMPANY']] [$result['JOB_NO']] [$result['PO_NUMBER']] [$result['STYLE_REF_NO']] [$result['ITEM_NUMBER_ID']] ['PRODUCTION_DATE'] = $result['PRODUCTION_DATE'];

		$dataArray[$result['SERVING_COMPANY']] [$result['JOB_NO']] [$result['PO_NUMBER']] [$result['STYLE_REF_NO']] [$result['ITEM_NUMBER_ID']] ['CUT_NO'] = $result['CUT_NO'];

		$dataArray[$result['SERVING_COMPANY']] [$result['JOB_NO']] [$result['PO_NUMBER']] [$result['STYLE_REF_NO']] [$result['ITEM_NUMBER_ID']] ['GROUPING'] = $result['GROUPING'];

		$dataArray[$result['SERVING_COMPANY']] [$result['JOB_NO']] [$result['PO_NUMBER']] [$result['STYLE_REF_NO']] [$result['ITEM_NUMBER_ID']] ['FILE_NO'] = $result['FILE_NO'];

		$dataArray[$result['SERVING_COMPANY']] [$result['JOB_NO']] [$result['PO_NUMBER']] [$result['STYLE_REF_NO']] [$result['ITEM_NUMBER_ID']] ['BUYER_NAME'] = $result['BUYER_NAME'];
	}
	//echo "<pre>"; print_r($dataArray); exit();

	ob_start();
	?>
	<fieldset style="margin-top: 20px; width: 1950px;">
		<style>
			.breakAll{
				word-break:break-all;
				word-wrap: break-word;
			}
			.inline { 
				display: inline-block; 
			}
		</style>

		<table width="1950" >
			<tr>
				<td align="center" width="100%" colspan="29" class="form_caption"><? echo $companyArr[str_replace("'","",$cbo_company_name)]; ?></td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="29" class="form_caption" style="font-size:12px;"><? echo   $com_dtls[1]; ?></td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="29" class="form_caption" style="font-size:18px;">Ref Wise Cutting Number and Production</td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="29" class="form_caption" style="font-size:14px;"><? echo $date_range; ?></td>
			</tr>
		</table>
		
		<div >
			<table align="left" width="1950" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" style="margin-top: 10px; position: sticky;">
				<thead >
					<tr >
						<th width="30">SL</th>
						<th width="120">Working Factory</th>
						<th width="120">Job No.</th>
						<th width="120">Order Number</th>
						<th width="120">Buyer Name</th>
						<th width="120">Style Name</th>
						<th width="120">File No.</th>
						<th width="120">Internal Ref.</th>
						<th width="120">Item Name</th>

						<th width="120">Cutting Date</th>
						<th width="120">Cutting Number</th>
						<th width="120">Cutting QTY</th>
						<th width="120">QC</th>
						<th width="120">Sent To Print/EMb</th>
						<th width="120">Recv Print/EMb</th>
						<th width="120">Sewing Input</th>
						<th width="120">Sewing Output</th>
					</tr>
				</thead>
			</table>
		</div>
		<div> 
			<table align="left" width="1950" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
				<tbody>
					<?php 
						$total_cutting_qty 		=0;
						$total_cutting_qc 		=0;
						$total_print_issue		=0;
						$total_print_recv		=0;
						$total_sewing_in		=0;
						$total_sewing_out		=0;


						$i = 1;
						foreach($dataArray as $factory => $jobs)
						{
							foreach($jobs as $job => $orders)
							{
								foreach($orders as $po_number => $style_refs)
								{
									foreach($style_refs as $style => $items)
									{
										foreach($items as $item => $data)
										{
											$total_cutting_qty 		+=$data[1];
											$total_cutting_qc 		+=$data[1];
											$total_print_issue		+=$data[2];
											$total_print_recv		+=$data[3];
											$total_sewing_in		+=$data[4];
											$total_sewing_out		+=$data[5];
											if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											?>
												<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_2nd<? echo $i; ?>">
													<td  width="30" class='breakAll'><?=$i;?></td>
													<td  width="120" class='breakAll'><?=$companyArr[$factory];?></td>
													<td  width="120" class='breakAll'><?=$job;?></td>
													<td  width="120" class='breakAll'><?=$po_number;?></td>
													<td  width="120" class='breakAll'><?=$buyerArr[$data['BUYER_NAME']];?></td>
													<td  width="120" class='breakAll'><?=$style;?></td>
													<td  width="120" class='breakAll'><?=$data['FILE_NO'];?></td>
													<td  width="120" class='breakAll'><?=$data['GROUPING'];?></td>
													<td  width="120" class='breakAll'><?=$garments_item[$item];?></td>

													<td  width="120" class='breakAll' align="right"><?=$data['PRODUCTION_DATE'];?></td>
													<td  width="120" class='breakAll' align="right"><?=$data['CUT_NO'];?></td>
													<td  width="120" class='breakAll' align="right"><?=$data[1];?></td>
													<td  width="120" class='breakAll' align="right"><?=$data[1];?></td>
													<td  width="120" class='breakAll' align="right"><?=$data[2];?></td>
													<td  width="120" class='breakAll' align="right"><?=$data[3];?></td>
													<td  width="120" class='breakAll' align="right"><?=$data[4];?></td>
													<td  width="120" class='breakAll' align="right"><?=$data[5];?></td>
												</tr>
											<?php 
										}
										$i++;
									}
								}
							}
							
						}
					?>
				</tbody>
			</table>
		</div>
		
		<div>
			<table align="left" width="1950" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
				<tfoot>
					
					<tr >
						<th width="120" colspan='11'>Total:</th>
						<th width="120"><?=$total_cutting_qty;?></th>
						<th width="120"><?=$total_cutting_qc;?></th>
						<th width="120"><?=$total_print_issue;?></th>
						<th width="120"><?=$total_print_recv;?></th>
						<th width="120"><?=$total_sewing_in;?></th>
						<th width="120"><?=$total_sewing_out;?></th>
					</tr>
				</tfoot>
			</table>
		</div>
	<fieldset>

	<?php 

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

?>
