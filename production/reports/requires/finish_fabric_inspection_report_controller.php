<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");


require_once('../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];




if($action=="report_generate")
{ 
	
	$process = array( &$_POST );
    //var_dump($process);die;
	extract(check_magic_quote_gpc( $process )); 
	 
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_batch_no=str_replace("'","",$txt_batch_no);
	
	if($cbo_company_name>0){$company_name_cond="and a.company_id =$cbo_company_name";}else{$company_name_cond="";}


  
	if($txt_batch_no!="")
	{
		$batch_cond="and a.batch_no='$txt_batch_no'";
	}
	
	
	
	$details_report="";
	$master_data=array();
	$current_date=date("Y-m-d");
	$date=date("Y-m-d");$break_id=0;$sc_lc_id=0;
	$sy = date('Y',strtotime($txt_date_from));
	//$basic_smv_arr=return_library_array( "select comapny_id, basic_smv from lib_capacity_calc_mst where year=$sy",'comapny_id','basic_smv');
	
	//$tot_cost_arr = return_library_array("select job_no, cm_for_sipment_sche from wo_pre_cost_dtls","job_no","cm_for_sipment_sche"); 
	//$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per"); 
	
	
	
	
        $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
       // $buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
        $machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");				
        $yarn_count_lib_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
        $operator_lib_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');
        $supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
        $style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
        $buyer_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
        $color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
        $yarncount = return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');
        
        //$non_booking_arr=return_library_array( "select  id,booking_no  from  wo_non_ord_samp_booking_mst ", "id", "booking_no"  );
        
        $knitting_company_library = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");
        
        
        $job_array = array();
        $job_sql = "select distinct(a.buyer_name) as buyer_name,a.style_ref_no, a.job_no_prefix_num, a.job_no, b.pub_shipment_date, b.id, b.po_number, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

        $job_sql_result = sql_select($job_sql);
        foreach ($job_sql_result as $row) {
            $job_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
            $job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
            $job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
            $job_array[$row[csf('id')]]['ship_date'] = $row[csf('pub_shipment_date')];
            $job_array[$row[csf('id')]]['ref'] = $row[csf('grouping')];
            $job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
            $job_array[$row[csf('id')]]['style'] = $row[csf('style_ref_no')];
        }
        
        $sql = "select a.id,a.company_id, a.batch_no,a.batch_date, a.booking_no_id,a.booking_no,a.batch_type_id,a.booking_without_order,a.sales_order_no, a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.dyeing_machine,b.width_dia_type,count(b.width_dia_type) as num_of_rows, a.remarks, a.collar_qty, a.cuff_qty, a.SAVE_STRING, LISTAGG(b.po_id, ',') WITHIN GROUP (ORDER BY b.po_id) AS po_id , LISTAGG(CAST(b.prod_id AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.prod_id) AS prod_id,LISTAGG(CAST(b.barcode_no AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.barcode_no) AS barcode_no,LISTAGG(CAST(d.yarn_lot AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) as yarn_lot, LISTAGG(CAST(d.yarn_count AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_count) as yarn_count,b.item_description from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
		where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id $company_name_cond $batch_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
        group by a.id,a.company_id, a.batch_no,a.batch_date, a.color_id, a.batch_against,a.batch_type_id, a.color_range_id, a.organic, a.extention_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.total_trims_weight, a.process_id, a.batch_for,a.sales_order_no, a.batch_weight, a.dyeing_machine,a.remarks,a.collar_qty,a.cuff_qty, a.SAVE_STRING,b.item_description,b.width_dia_type";

        //echo $sql;die;	

        $dataArray = sql_select($sql);

        $po_number = "";
        $job_number = "";
        $job_style = "";
        $buyer_id = "";
        $ship_date = "";
        $internal_ref = "";
        $file_nos = "";
        $po_id = array_unique(explode(",", $dataArray[0][csf('po_id')]));
        $barcode_no = implode(",",array_unique(explode(",", $dataArray[0][csf('barcode_no')])));
        $sql_barcode_no = array_unique(explode(",", $dataArray[0][csf('barcode_no')]));
        $desc = explode(",", $dataArray[0][csf('item_description')]);
        $booking_no = $dataArray[0][csf('booking_no')];
        $batch_against_id = $dataArray[0][csf('batch_against')];
        $batch_booking_id = $dataArray[0][csf('booking_no_id')];
        $batch_product_id = $dataArray[0][csf('prod_id')];
        $sales_order_no = $dataArray[0][csf('sales_order_no')];
        $batch_booking_without = $dataArray[0][csf('booking_without_order')];
        $y_count =array_unique(explode(",", $dataArray[0][csf('yarn_count')]));
        $yarn_lot = implode(",", array_unique(explode(",", $dataArray[0][csf('yarn_lot')])));
        
        $yarn_count_value = "";
        foreach ($y_count as $val) {
            if ($val > 0) {
                if ($yarn_count_value == '') $yarn_count_value = $yarncount[$val]; else $yarn_count_value .= ", " . $yarncount[$val];
            }
        }

        // var_dump($yarn_count_value);

        foreach ($po_id as $val) {
            if ($po_number == "") $po_number = $job_array[$val]['po']; else $po_number .= ',' . $job_array[$val]['po'];
            if ($job_number == "") $job_number = $job_array[$val]['job']; else $job_number .= ',' . $job_array[$val]['job'];
            if ($job_style == "") $job_style = $job_array[$val]['style']; else $job_style .= ',' . $job_array[$val]['style'];
            if ($buyer_id == "") $buyer_id = $job_array[$val]['buyer']; else $buyer_id .= ',' . $job_array[$val]['buyer'];
            if ($ship_date == "") $ship_date = change_date_format($job_array[$val]['ship_date']); else $ship_date .= ',' . change_date_format($job_array[$val]['ship_date']);

            if ($internal_ref == "") $internal_ref = $job_array[$val]['ref']; else $internal_ref .= ',' . $job_array[$val]['ref'];
            if ($job_array[$val]['file_no'] > 0) {
                if ($file_nos == "") $file_nos = $job_array[$val]['file_no']; else $file_nos .= ',' . $job_array[$val]['file_no'];
            }
        }
       
        $job_no = implode(",", array_unique(explode(",", $job_number)));
        $jobstyle = implode(",", array_unique(explode(",", $job_style)));
        $buyer = implode(",", array_unique(explode(",", $buyer_id)));
        $internal_ref = implode(",", array_unique(array_filter(explode(",", $internal_ref))));
        $file_nos = implode(",", array_unique(explode(",", $file_nos)));
	//$booking_without_order=return_field_value( "booking_no as booking_no", "wo_non_ord_samp_booking_mst","booking_no=$booking_no","booking_no");

	if ($dataArray[0][csf('booking_without_order')] == 1) {
		$booking_without_order = sql_select("select a.booking_no_prefix_num, a.buyer_id,b.body_part,b.booking_no,b.color_type_id,b.fabric_color from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where  a.booking_no=b.booking_no and a.company_id=$company and a.booking_no='$booking_no' and a.booking_type=4");
		foreach ($booking_without_order as $row) {
			$color_type_array2[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('fabric_color')]]['color_type_id'] = $row[csf('color_type_id')];
			$booking_id = $row[csf('booking_no_prefix_num')];
			$buyer_id_booking = $row[csf('buyer_id')];
		}


	} else {
		$booking_with_order = sql_select("select booking_no_prefix_num, buyer_id from wo_booking_mst where company_id=$company and booking_no='$booking_no' and booking_type=4");
		$booking_id = $booking_with_order[0][csf('booking_no_prefix_num')];
		$buyer_id_booking = $booking_with_order[0][csf('buyer_id')];

	}

	ob_start();

		?>

		<style type="text/css">
				.block_div { 
					width:auto;
					height:auto;
					text-wrap:normal;
					vertical-align:bottom;
					display: block;
					position: !important; 
					-webkit-transform: rotate(-90deg);
					-moz-transform: rotate(-90deg);					
				}
		</style> 
		<? 	
	if($report_format==1) // Show
	{
		?>
		<fieldset style="width:1580px;">
			<table width="1500">
				<tr>
					<td align="center" width="100%" colspan="12" class="form_caption" style="font-size:18px;">Finish Fabric Inspection Report</td>
				</tr>
				<tr>
					<td align="center" width="100%" colspan="16" class="form_caption"><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></td>
				</tr>
				<tr>
				   <td align="center" width="100%" colspan="12" class="form_caption" style="font-size:14px;"><p>92,Raj- Phulbaria, Tetuljhora, Savar, 1347, info@akhfashions.com, www.akhfashions.com</p></td>
				</tr>
                <tr>
				   <td></td>
				</tr>
                <tr >
                    <td style="font-size:15px"><strong>Buyer</strong></td>
                    <td style="font-size:15px">
                        :&nbsp;<? if ($dataArray[0][csf('batch_against')] == 3) echo $buyer_arr[$buyer_id_booking]; else echo $buyer_arr[$buyer]; ?>
                    </td>
                    <td style="font-size:15px"><strong>Order No</strong></td>
					<td style="font-size:15px">:&nbsp;<? echo $po_number; ?></td>
                    <td style="font-size:15px"><strong>Batch No</strong></td>
					<td style="font-size:15px">:&nbsp;<? echo $dataArray[0][csf('batch_no')]; ?></td>
                    <td style="font-size:15px"><strong>Batch Against</strong></td>
					<td style="font-size:15px">:&nbsp;<? echo $batch_against[$dataArray[0][csf('batch_against')]]; ?></td>
                    <td style="font-size:15px"><strong>Fabrication  </strong></td>
					<td style="font-size:15px">:&nbsp;<? echo $desc[0] . "," . $desc[1] . "," . $desc[2]; ?></td>
                </tr>
                <tr>
                    <td style="font-size:15px"><strong>File No</strong></td>
					<td style="font-size:15px">:&nbsp;<? echo $file_nos; ?></td>
                    <td style="font-size:15px"><strong>Style no.</strong></td>
					<td style="font-size:15px">:&nbsp;<? echo $jobstyle; ?></td>
                    <td style="font-size:15px"><strong>B. Color</strong></td>
					<td style="font-size:15px">:&nbsp;<? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
                    <td style="font-size:15px"><strong>Dia Type</strong></td>
					<td style="font-size:15px">:&nbsp;<? echo $fabric_typee[$dataArray[0][csf('width_dia_type')]]; ?></td>
                    <td style="font-size:15px"><strong>Yarn Count & Lot</strong></td>
					<td style="font-size:15px">:&nbsp;<? echo $yarn_count_value.' & '. implode(",", array_unique(explode(",", $dataArray[0][csf('yarn_lot')]))) ?></td>
                </tr>
                <tr>
                    <td style="font-size:15px"><strong>Int. Ref.</strong></td>
					<td style="font-size:15px">:&nbsp;<? echo $internal_ref; ?></td>
                    <td style="font-size:15px"><strong>Booking No.</strong></td>
					<td style="font-size:15px">:&nbsp;<? echo $dataArray[0][csf('booking_no')]; ?></td>
                    <td style="font-size:15px"><strong>B. Weight</strong></td>
					<td style="font-size:15px">:&nbsp;<? echo $dataArray[0][csf('batch_weight')]; ?></td>
                    <td style="font-size:15px"><strong>Total No of B.Roll</strong></td>
					<td style="font-size:15px">:&nbsp;<? echo $dataArray[0][csf('num_of_rows')]; ?></td>
                </tr>
			</table>
			  <div style="width:2070px; float:left; ">
				<table width="2050" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left;">
					<thead>
						<tr height="100">
							<th width="40">SL</th>
							<th width="100">Barcode No</th>
							<th width="40">Req. <br>Width</th>
							<th width="40">Ac.<br> width</th>
							<th width="40">Req.<br> GSM</th>
							<th width="50">Ac. <br>GSM</th>
							<th width="40">Weight<br>(Kg)</th>
							<th width="40">Length<br>(Yds)</th>
							<th width="40" style="vertical-align:middle"><div class="rotate_90_deg">Hole</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">Color/Dye Spot</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">Poly Conta</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">Slub</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">Patta/Barrie Mark</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">Cut/Joint</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">Print Mis</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">Yarn Conta</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">NEPS</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">Needle Drop</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">Dead Cotton</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">Thick & Thin</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">Needle Broken Mark</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">Side To Center Shade</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">Bowing</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">Uneven </div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">Dia Mark</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">Dust</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">Hairy</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">G.S.M Hole</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">Running Shade</div></th>
                            <th width="50" style="vertical-align:middle"><div class="rotate_90_deg">Crease mark</div></th>
                            <th width="50" style="vertical-align:middle"><div class="rotate_90_deg">Loop Out</div></th>
							<th width="50" style="vertical-align:middle"><div class="rotate_90_deg">Cut Hole</div></th>
							<th width="60"> Total Point</th>
							<th width="60"> Fabric Grade</th>
							<th width="60" >Fabric Shade</th>
                            <th width="60"> Def. %</th>
							<th width="60">Reject Qty </th>
							<th width="100">Comment</th>
						</tr>
					</thead>
				</table>
				  	<style>
						.breakAll{
							word-break:break-all;
							word-wrap: break-word;
						}
					</style>
				  <div style="width:2070px; float:left; max-height:400px; overflow-y:scroll;" id="scroll_body">
				  <table width="2050" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body" style="float:left;">
					<tbody>
					<?


					$qcbyroll_sql = "select a.gsm, a.width,a.receive_qnty, b.barcode_no from pro_finish_fabric_rcv_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=66 and b.status_active=1 and b.is_deleted=0 and b.barcode_no in($barcode_no)";
					//echo $qcbyroll_sql;die;
					$qcbyroll_data=sql_select($qcbyroll_sql);

					$qcbyroll_arr = array();
                    foreach($qcbyroll_data as $dataRow)
					{
						$qcbyroll_arr[$dataRow[csf("barcode_no")]]["gsm"]=$dataRow[csf("gsm")];
						$qcbyroll_arr[$dataRow[csf("barcode_no")]]["width"]=$dataRow[csf("width")];
						$qcbyroll_arr[$dataRow[csf("barcode_no")]]["receive_qnty"]=$dataRow[csf("receive_qnty")];
                    }

					
					$data_array = "select  a.barcode_no,gsm, width from pro_roll_details a, pro_grey_prod_entry_dtls b where a.dtls_id=b.id and a.barcode_no in($barcode_no) and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

					//echo $data_array;die;

					$data_array_sql=sql_select($data_array);

					$data_array_sql_arr = array();
                    foreach($data_array_sql as $dataRows)
					{
						$data_array_sql_arr[$dataRows[csf("barcode_no")]]["gsm"]=$dataRows[csf("gsm")];
						$data_array_sql_arr[$dataRows[csf("barcode_no")]]["width"]=$dataRows[csf("width")];
                    }

					//var_dump($data_array_sql_arr);die;



                    $sql_def_dtls="SELECT b.barcode_no, c.width
                    from pro_batch_create_mst a, pro_batch_create_dtls b,pro_finish_fabric_rcv_dtls c where a.id=b.mst_id  
					and  c.batch_id=a.id and b.barcode_no in($barcode_no) and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0  order by b.barcode_no";

                    //echo $sql_def_dtls;die;

                    $sql_def_dtls_data=sql_select($sql_def_dtls);

                    $sql_def_dtls_arr = array();
                    foreach($sql_def_dtls_data as $dataRow)
					{
						$sql_def_dtls_arr[$dataRow[csf("barcode_no")]]["width"]=$dataRow[csf("width")];
                    }
                    //var_dump($sql_def_dtls_arr);die;

					
					$sql_qc_dtls="SELECT d.id, d.pro_dtls_id, d.roll_maintain, d.barcode_no, d.roll_id, d.roll_no, d.qc_name, d.roll_status, d.roll_width, d.roll_weight, d.roll_length, d.reject_qnty, d.qc_date, d.total_penalty_point, d.total_point,d.fabric_grade, d.comments,e.defect_name,e.defect_count,e.found_in_inch,e.penalty_point,d.insert_date,d.fabric_shade,

					case when e.defect_name ='1'  then e.defect_count else 0 end as hole_defect_count,
					case when e.defect_name ='5'  then e.defect_count else 0 end as dye_defect_count,
					case when e.defect_name ='10'  then e.defect_count else 0 end as insect_defect_count,
					case when e.defect_name ='15'  then e.defect_count else 0 end as yellowSpot_defect_count,
					case when e.defect_name ='20'  then e.defect_count else 0 end as poly_defect_count,
					case when e.defect_name ='25'  then e.defect_count else 0 end as dust_defect_count,
					case when e.defect_name ='30'  then e.defect_count else 0 end as oilspot_defect_count,
					case when e.defect_name ='35'  then e.defect_count else 0 end as flyconta_defect_count,
					case when e.defect_name ='40'  then e.defect_count else 0 end as slub_defect_count,
					case when e.defect_name ='45'  then e.defect_count else 0 end as patta_defect_count,
					case when e.defect_name ='50'  then e.defect_count else 0 end as cut_defect_count,
					case when e.defect_name ='55'  then e.defect_count else 0 end as sinker_defect_count,
					case when e.defect_name ='60'  then e.defect_count else 0 end as print_mis_defect_count,
					case when e.defect_name ='65'  then e.defect_count else 0 end as yarn_conta_defect_count,
					case when e.defect_name ='70'  then e.defect_count else 0 end as slub_hole_defect_count,
					case when e.defect_name ='75'  then e.defect_count else 0 end as softener_Spot_defect_count,
					case when e.defect_name ='95'  then e.defect_count else 0 end as dirty_stain_defect_count,
					case when e.defect_name ='100' then e.defect_count else 0 end as neps_defect_count,
					case when e.defect_name ='105' then e.defect_count else 0 end as needle_drop_defect_count,
					case when e.defect_name ='110' then e.defect_count else 0 end as chem_defect_count,
					case when e.defect_name ='115' then e.defect_count else 0 end as cotton_seeds_defect_count,
					case when e.defect_name ='120' then e.defect_count else 0 end as Loop_hole_defect_count,
					case when e.defect_name ='125' then e.defect_count else 0 end as dead_cotton_defect_count,
					case when e.defect_name ='130' then e.defect_count else 0 end as thick_thin_defect_count,
					case when e.defect_name ='135' then e.defect_count else 0 end as rust_spot_defect_count,
					case when e.defect_name ='140' then e.defect_count else 0 end as needle_broken_mark_defect_count,
					case when e.defect_name ='145' then e.defect_count else 0 end as dirty_spot_defect_count,
					case when e.defect_name ='150' then e.defect_count else 0 end as side_center_shade_defect_count,
					case when e.defect_name ='155' then e.defect_count else 0 end as bowing_defect_count,
					case when e.defect_name ='160' then e.defect_count else 0 end as uneven_defect_count,
					case when e.defect_name ='165' then e.defect_count else 0 end as yellow_writing_defect_count,
					case when e.defect_name ='170' then e.defect_count else 0 end as fabric_missing_defect_count,
					case when e.defect_name ='175' then e.defect_count else 0 end as dia_mark_defect_count,
					case when e.defect_name ='180' then e.defect_count else 0 end as miss_print_defect_count,
					case when e.defect_name ='185' then e.defect_count else 0 end as hairy_defect_count,
					case when e.defect_name ='190' then e.defect_count else 0 end as gsm_hole_defect_count,
					case when e.defect_name ='195' then e.defect_count else 0 end as compacting_mark_defect_count,
					case when e.defect_name ='200' then e.defect_count else 0 end as rib_body_shade_defect_count,
					case when e.defect_name ='205' then e.defect_count else 0 end as running_shade_defect_count,
					case when e.defect_name ='210' then e.defect_count else 0 end as plastic_conta_defect_count,
					case when e.defect_name ='215' then e.defect_count else 0 end as crease_mark_defect_count,
					case when e.defect_name ='220' then e.defect_count else 0 end as patches_defect_count,
					case when e.defect_name ='225' then e.defect_count else 0 end as mc_toppage_defect_count,
					case when e.defect_name ='230' then e.defect_count else 0 end as needle_line_defect_count,
					case when e.defect_name ='235' then e.defect_count else 0 end as crample_mark_defect_count,
					case when e.defect_name ='240' then e.defect_count else 0 end as shite_specks_defect_count,
					case when e.defect_name ='245' then e.defect_count else 0 end as mellange_effect_defect_count,
					case when e.defect_name ='250' then e.defect_count else 0 end as line_mark_defect_count,
					case when e.defect_name ='255' then e.defect_count else 0 end as loop_out_defect_count,
					case when e.defect_name ='260' then e.defect_count else 0 end as needle_broken_defect_count,
					case when e.defect_name ='261' then e.defect_count else 0 end as loop_defect_count,
					case when e.defect_name ='262' then e.defect_count else 0 end as oil_spot_line_defect_count,
					case when e.defect_name ='263' then e.defect_count else 0 end as lycra_out_drop_defect_count,
					case when e.defect_name ='264' then e.defect_count else 0 end as miss_yarn_defect_count,
					case when e.defect_name ='265' then e.defect_count else 0 end as color_contra_defect_count,
					case when e.defect_name ='266' then e.defect_count else 0 end as friction_mark_defect_count,
					case when e.defect_name ='267' then e.defect_count else 0 end as pin_out_defect_count,
					case when e.defect_name ='268' then e.defect_count else 0 end as rust_stain_defect_count,
					case when e.defect_name ='269' then e.defect_count else 0 end as stop_mark_defect_count,
					case when e.defect_name ='270' then e.defect_count else 0 end as compacting_broken_defect_count,
					case when e.defect_name ='271' then e.defect_count else 0 end as grease_spot_defect_count,
					case when e.defect_name ='272' then e.defect_count else 0 end as cut_hole_defect_count,
					case when e.defect_name ='273' then e.defect_count else 0 end as snagging_pull_out_defect_count,
					case when e.defect_name ='274' then e.defect_count else 0 end as press_off_defect_count,
					case when e.defect_name ='275' then e.defect_count else 0 end as wheel_free_defect_count,
					case when e.defect_name ='276' then e.defect_count else 0 end as count_mix_defect_count,
					case when e.defect_name ='277' then e.defect_count else 0 end as black_spot_defect_count,
					case when e.defect_name ='278' then e.defect_count else 0 end as set_up_defect_count,
					case when e.defect_name ='279' then e.defect_count else 0 end as pin_ole_defect_count

					

					from pro_qc_result_mst d,pro_qc_result_dtls e  
					where  d.id=e.mst_id and d.barcode_no in($barcode_no) and d.entry_form=267 and d.status_active=1 and d.is_deleted=0 and  e.status_active=1 and e.is_deleted=0  order by d.barcode_no";
					//d.pro_dtls_id=24893 and 
					//echo $sql_qc_dtls;die; 
                   

					$sql_qc_dtls_data=sql_select($sql_qc_dtls); 
					//print_r($sql_qc_dtls_data);die("with jj");
					$sql_qc_data_arr=array(); 
					$sql_barcode_qc_data_arr=array(); 
					foreach($sql_qc_dtls_data as $dataRow)
					{
						$sql_barcode_qc_data_arr[$dataRow[csf("barcode_no")]]=$dataRow[csf("barcode_no")];

						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["hole_defect_count"]+=$dataRow[csf("hole_defect_count")];
						//print_r($sql_qc_data_arr);die("with sumon");
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["dye_defect_count"]+=$dataRow[csf("dye_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["insect_defect_count"]+=$dataRow[csf("insect_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["yellowSpot_defect_count"]+=$dataRow[csf("yellowSpot_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["poly_defect_count"]+=$dataRow[csf("poly_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["dust_defect_count"]+=$dataRow[csf("dust_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["oilspot_defect_count"]+=$dataRow[csf("oilspot_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["flyconta_defect_count"]+=$dataRow[csf("flyconta_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["slub_defect_count"]+=$dataRow[csf("slub_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["patta_defect_count"]+=$dataRow[csf("patta_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["cut_defect_count"]+=$dataRow[csf("cut_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["sinker_defect_count"]+=$dataRow[csf("sinker_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["print_mis_defect_count"]+=$dataRow[csf("print_mis_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["yarn_conta_defect_count"]+=$dataRow[csf("yarn_conta_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["slub_hole_defect_count"]+=$dataRow[csf("slub_hole_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["softener_Spot_defect_count"]+=$dataRow[csf("softener_Spot_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["dirty_stain_defect_count"]+=$dataRow[csf("dirty_stain_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["neps_defect_count"]+=$dataRow[csf("neps_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["needle_drop_defect_count"]+=$dataRow[csf("needle_drop_defect_count")];
						
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["chem_defect_count"]+=$dataRow[csf("chem_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["cotton_seeds_defect_count"]+=$dataRow[csf("cotton_seeds_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["Loop_hole_defect_count"]+=$dataRow[csf("Loop_hole_defect_count")];
						
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["dead_cotton_defect_count"]+=$dataRow[csf("dead_cotton_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["thick_thin_defect_count"]+=$dataRow[csf("thick_thin_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["rust_spot_defect_count"]+=$dataRow[csf("rust_spot_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["needle_broken_mark_defect_count"]+=$dataRow[csf("needle_broken_mark_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["dirty_spot_defect_count"]+=$dataRow[csf("dirty_spot_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["side_center_shade_defect_count"]+=$dataRow[csf("side_center_shade_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["bowing_defect_count"]+=$dataRow[csf("bowing_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["uneven_defect_count"]+=$dataRow[csf("uneven_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["yellow_writing_defect_count"]+=$dataRow[csf("yellow_writing_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["fabric_missing_defect_count"]+=$dataRow[csf("fabric_missing_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["dia_mark_defect_count"]+=$dataRow[csf("dia_mark_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["miss_print_defect_count"]+=$dataRow[csf("miss_print_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["hairy_defect_count"]+=$dataRow[csf("hairy_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["gsm_hole_defect_count"]+=$dataRow[csf("gsm_hole_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["compacting_mark_defect_count"]+=$dataRow[csf("compacting_mark_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["rib_body_shade_defect_count"]+=$dataRow[csf("rib_body_shade_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["running_shade_defect_count"]+=$dataRow[csf("running_shade_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["plastic_conta_defect_count"]+=$dataRow[csf("plastic_conta_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["crease_mark_defect_count"]+=$dataRow[csf("crease_mark_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["patches_defect_count"]+=$dataRow[csf("patches_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["mc_toppage_defect_count"]+=$dataRow[csf("mc_toppage_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["needle_line_defect_count"]+=$dataRow[csf("needle_line_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["crample_mark_defect_count"]+=$dataRow[csf("crample_mark_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["shite_specks_defect_count"]+=$dataRow[csf("shite_specks_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["mellange_effect_defect_count"]+=$dataRow[csf("mellange_effect_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["line_mark_defect_count"]+=$dataRow[csf("line_mark_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["loop_out_defect_count"]+=$dataRow[csf("loop_out_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["needle_broken_defect_count"]+=$dataRow[csf("needle_broken_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["loop_defect_count"]+=$dataRow[csf("loop_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["oil_spot_line_defect_count"]+=$dataRow[csf("oil_spot_line_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["lycra_out_drop_defect_count"]+=$dataRow[csf("lycra_out_drop_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["miss_yarn_defect_count"]+=$dataRow[csf("miss_yarn_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["color_contra_defect_count"]+=$dataRow[csf("color_contra_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["friction_mark_defect_count"]+=$dataRow[csf("friction_mark_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["pin_out_defect_count"]+=$dataRow[csf("pin_out_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["rust_stain_defect_count"]+=$dataRow[csf("rust_stain_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["stop_mark_defect_count"]+=$dataRow[csf("stop_mark_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["compacting_broken_defect_count"]+=$dataRow[csf("compacting_broken_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["grease_spot_defect_count"]+=$dataRow[csf("grease_spot_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["cut_hole_defect_count"]+=$dataRow[csf("cut_hole_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("pro_dtls_id")]][$dataRow[csf("barcode_no")]]["snagging_pull_out_defect_count"]+=$dataRow[csf("snagging_pull_out_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["press_off_defect_count"]+=$dataRow[csf("press_off_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["wheel_free_defect_count"]+=$dataRow[csf("wheel_free_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["count_mix_defect_count"]+=$dataRow[csf("count_mix_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["black_spot_defect_count"]+=$dataRow[csf("black_spot_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["set_up_defect_count"]+=$dataRow[csf("set_up_defect_count")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["pin_ole_defect_count"]+=$dataRow[csf("pin_ole_defect_count")];


						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["comments"]=$dataRow[csf("comments")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["reject_qnty"]=$dataRow[csf("reject_qnty")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["roll_weight"]=$dataRow[csf("roll_weight")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["roll_status"]=$dataRow[csf("roll_status")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["insert_date"]=$dataRow[csf("insert_date")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["qc_name"]=$dataRow[csf("qc_name")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["fabric_shade"]=$dataRow[csf("fabric_shade")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["roll_width"]=$dataRow[csf("roll_width")];


						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["fabric_grade"]=$dataRow[csf("fabric_grade")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["roll_length"]=$dataRow[csf("roll_length")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["total_penalty_point"]=$dataRow[csf("total_penalty_point")];
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["total_point"]=$dataRow[csf("total_point")];
						
						$sql_qc_data_arr[$dataRow[csf("barcode_no")]]["tr_color"]=$dataRow[csf("barcode_no")];
					}

					//var_dump($sql_barcode_qc_data_arr);die;

					
					
                    

					$i=1;
					foreach($sql_barcode_qc_data_arr as $key=>$barcodedata)
					{
						 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
						?>

						
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_2nd<? echo $i; ?>" >
							<td width="40" style="font-size: 16px;"><? echo $i; ?></td>
							<td width="100" align="center" style="font-size: 16px;"><? echo $barcodedata; ?></td> 
							<td width="40" align="right" style="font-size: 16px;"><p><? echo $data_array_sql_arr[$barcodedata]["width"]; ?></p></td>
							<td width="40" align="right" style="font-size: 16px;"><p><? echo $sql_qc_data_arr[$barcodedata]["roll_width"]; ?></p></td>
							<td width="40" align="right" style="font-size: 16px;"><p><? echo $data_array_sql_arr[$barcodedata]["gsm"]; ?></p></td>
							<td width="50" align="right" style="font-size: 16px;"><? echo $qcbyroll_arr[$barcodedata]["gsm"]; ?></td>
							<td width="40" align="right" style="font-size: 16px;"><? echo $qcbyroll_arr[$barcodedata]["receive_qnty"]; ?></td>
							<td width="40" align="right" style="font-size: 16px;"><? echo number_format($sql_qc_data_arr[$barcodedata]["roll_length"],2); ?></td>
							
							<td width="40" align="right" style="font-size: 16px;" ><? echo $sql_qc_data_arr[$barcodedata]["hole_defect_count"]; ?></td>
							<td width="50" align="right" style="font-size: 16px;" ><? echo $sql_qc_data_arr[$barcodedata]["dye_defect_count"]; ?></td>
							<td width="50" align="right" style="font-size: 16px;"  title=""><? echo $sql_qc_data_arr[$barcodedata]["poly_defect_count"]; ?></td>
							<td width="50" align="right" style="font-size: 16px;" ><? echo $sql_qc_data_arr[$barcodedata]["slub_defect_count"]; ?></td>
							<td width="50" align="right" style="font-size: 16px;" ><? echo $sql_qc_data_arr[$barcodedata]["patta_defect_count"]; ?></td>
							<td width="50" align="right" style="font-size: 16px;" ><? echo $sql_qc_data_arr[$barcodedata]["cut_defect_count"]; ?></td>
							<td width="50" align="right" style="font-size: 16px;" ><? echo $sql_qc_data_arr[$barcodedata]["print_mis_defect_count"]; ?></td>
							<td width="50" align="right" style="font-size: 16px;" ><? echo $sql_qc_data_arr[$barcodedata]["yarn_conta_defect_count"]; ?></td>
							<td width="50" align="right" style="font-size: 16px;" ><? echo $sql_qc_data_arr[$barcodedata]["neps_defect_count"]; ?></td>
							<td width="50" align="right" style="font-size: 16px;" ><? echo $sql_qc_data_arr[$barcodedata]["needle_drop_defect_count"]; ?></td>
							<td width="50" align="right" style="font-size: 16px;" ><? echo $sql_qc_data_arr[$barcodedata]["dead_cotton_defect_count"]; ?></td>
							<td width="50" align="right" style="font-size: 16px;" ><? echo $sql_qc_data_arr[$barcodedata]["thick_thin_defect_count"]; ?></td>
							<td width="50" align="right" style="font-size: 16px;" ><? echo $sql_qc_data_arr[$barcodedata]["needle_broken_mark_defect_count"]; ?></td>
							<td width="50" align="right" style="font-size: 16px;" ><? echo $sql_qc_data_arr[$barcodedata]["side_center_shade_defect_count"]; ?></td>
							<td width="50" align="right" style="font-size: 16px;" ><? echo $sql_qc_data_arr[$barcodedata]["bowing_defect_count"]; ?></td>
							<td width="50" align="right" style="font-size: 16px;" ><? echo $sql_qc_data_arr[$barcodedata]["uneven_defect_count"]; ?></td>
							<td width="50" align="right" style="font-size: 16px;" ><? echo $sql_qc_data_arr[$barcodedata]["dia_mark_defect_count"]; ?></td>
							<td width="50" align="right" style="font-size: 16px;" ><? echo $sql_qc_data_arr[$barcodedata]["dust_defect_count"]; ?></td>
							<td width="50" align="right" style="font-size: 16px;" ><? echo $sql_qc_data_arr[$barcodedata]["hairy_defect_count"]; ?></td>
							
							
							<td width="50" align="right" style="font-size: 16px;" ><? echo $sql_qc_data_arr[$barcodedata]["gsm_hole_defect_count"]; ?></td>
							<td width="50" align="right" style="font-size: 16px;" ><? echo $sql_qc_data_arr[$barcodedata]["running_shade_defect_count"]; ?></td>
							<td width="50" align="right" style="font-size: 16px;" ><? echo $sql_qc_data_arr[$barcodedata]["crease_mark_defect_count"]; ?></td>
                            
                            <td width="50" align="right" style="font-size: 16px;" ><? echo $sql_qc_data_arr[$barcodedata]["loop_out_defect_count"]; ?></td>
                            <td width="50" align="right" style="font-size: 16px;" ><? echo $sql_qc_data_arr[$barcodedata]["cut_hole_defect_count"]; ?></td>

                           
							<td width="60" align="right" style="font-size: 16px;" ><? echo number_format($sql_qc_data_arr[$barcodedata]["total_point"],2); ?></td>
							<td width="60" align="right" style="font-size: 16px;" ><? echo $sql_qc_data_arr[$barcodedata]["fabric_grade"]; ?></td>
							<td width="60" align="center" style="font-size: 16px;" ><? echo $fabric_shade[$sql_qc_data_arr[$barcodedata]["fabric_shade"]]; ?></td>
							<td width="60" align="center" style="font-size: 16px;" ><? $defect_percent=($sql_qc_data_arr[$barcodedata]["total_point"]*36*100)/($sql_def_dtls_arr[$barcodedata]["width"]*$sql_qc_data_arr[$barcodedata]["roll_length"]); echo number_format($defect_percent,2).'%'; ?></td>
							<td width="60" align="center" style="font-size: 16px;" ><? echo $sql_qc_data_arr[$barcodedata]["reject_qnty"]; ?></td>
							<td width="100" align="center" style="font-size: 16px;" ><p><? echo substr($sql_qc_data_arr[$barcodedata]["comments"],0,40); ?></p></td>
						</tr>
						<?
						
						$total_penalty_point+=$sql_qc_data_arr[$barcodedata]["total_penalty_point"];
						$total_point+=$sql_qc_data_arr[$barcodedata]["total_point"];
						$total_reject_qty+=	$sql_qc_data_arr[$barcodedata]["reject_qnty"];
						$total_defect_percent+= $defect_percent;
						$total_Inspected_Qty +=$qcbyroll_arr[$barcodedata]["receive_qnty"];
						$total_length +=$sql_qc_data_arr[$barcodedata]["roll_length"];
						$total_hole_defect +=$sql_qc_data_arr[$barcodedata]["hole_defect_count"];
						$total_dye_defect +=$sql_qc_data_arr[$barcodedata]["dye_defect_count"];
						$total_poly_defect +=$sql_qc_data_arr[$barcodedata]["poly_defect_count"];
						$total_slub_defect_count +=$sql_qc_data_arr[$barcodedata]["slub_defect_count"];
						$total_patta_defect_count +=$sql_qc_data_arr[$barcodedata]["patta_defect_count"];
						$total_cut_defect_count +=$sql_qc_data_arr[$barcodedata]["cut_defect_count"];
						$total_print_mis_defect_count +=$sql_qc_data_arr[$barcodedata]["print_mis_defect_count"];
						$total_yarn_conta_defect_count +=$sql_qc_data_arr[$barcodedata]["yarn_conta_defect_count"];
						$total_neps_defect_count +=$sql_qc_data_arr[$barcodedata]["neps_defect_count"];
						$total_needle_drop_defect_count +=$sql_qc_data_arr[$barcodedata]["needle_drop_defect_count"];
						$total_dead_cotton_defect_count +=$sql_qc_data_arr[$barcodedata]["dead_cotton_defect_count"];
						$total_thick_thin_defect_count +=$sql_qc_data_arr[$barcodedata]["thick_thin_defect_count"];
						$total_needle_broken_mark_defect_count +=$sql_qc_data_arr[$barcodedata]["needle_broken_mark_defect_count"];
						$total_side_center_shade_defect_count +=$sql_qc_data_arr[$barcodedata]["side_center_shade_defect_count"];
						$total_bowing_defect_count +=$sql_qc_data_arr[$barcodedata]["bowing_defect_count"];
						$total_uneven_defect_count +=$sql_qc_data_arr[$barcodedata]["uneven_defect_count"];
						$total_dia_mark_defect_count +=$sql_qc_data_arr[$barcodedata]["dia_mark_defect_count"];
						$total_dust_defect_count +=$sql_qc_data_arr[$barcodedata]["dust_defect_count"];
						$total_hairy_defect_count +=$sql_qc_data_arr[$barcodedata]["hairy_defect_count"];
						$total_gsm_hole_defect_count +=$sql_qc_data_arr[$barcodedata]["gsm_hole_defect_count"];
						$total_running_shade_defect_count +=$sql_qc_data_arr[$barcodedata]["running_shade_defect_count"];
						$total_crease_mark_defect_count +=$sql_qc_data_arr[$barcodedata]["crease_mark_defect_count"];
						$total_loop_out_defect_count +=$sql_qc_data_arr[$barcodedata]["loop_out_defect_count"];
						$total_cut_hole_defect_count +=$sql_qc_data_arr[$barcodedata]["cut_hole_defect_count"];
						$totalroll++;
						$i++;
                      
					}
					?>
					</tbody>
					
				</table>
				</div>

				<!-- <table width="7785" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body" style="float:left;"> -->
				<table class="rpt_table" width="2050" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">

					<tfoot>
						<tr style="background-color:#CCCCCC;">
							<th width="40">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="40">&nbsp;</th>
							<th width="40">&nbsp;</th>
							<th width="40">&nbsp;</th>
							<th width="50" style="font-size: 15px;"><strong>Total :</strong></th>
							<th width="40" id="total_Inspected_Qty" style="font-size: 16px;"><? echo number_format($total_Inspected_Qty,2); ?></th>
							<th width="40" id="total_length" style="font-size: 16px;"><? echo number_format($total_length,2); ?></th>
							<th width="40" id="total_hole_defect" style="font-size: 16px;"><? echo number_format($total_hole_defect,2); ?></th>
							<th width="50" id="total_dye_defect" style="font-size: 16px;"><? echo number_format($total_dye_defect,2); ?></th>
							<th width="50" id="total_poly_defect" style="font-size: 16px;"><? echo number_format($total_poly_defect,2); ?></th>
							<th width="50" id="total_slub_defect_count" style="font-size: 16px;"><? echo number_format($total_slub_defect_count,2); ?></th>
							<th width="50" id="total_patta_defect_count" style="font-size: 16px;"><? echo number_format($total_patta_defect_count,2); ?></th>
							<th width="50" id="total_cut_defect_count" style="font-size: 16px;"><? echo number_format($total_cut_defect_count,2); ?></th>
							<th width="50" id="total_print_mis_defect_count" style="font-size: 16px;"><? echo number_format($total_print_mis_defect_count,2); ?></th>
							<th width="50" id="total_yarn_conta_defect_count" style="font-size: 16px;"><? echo number_format($total_yarn_conta_defect_count,2); ?></th>
							<th width="50" id="total_neps_defect_count" style="font-size: 16px;"><? echo number_format($total_neps_defect_count,2); ?></th>
							<th width="50" id="total_needle_drop_defect_count" style="font-size: 16px;"><? echo number_format($total_needle_drop_defect_count,2); ?></th>
							<th width="50" id="total_dead_cotton_defect_count" style="font-size: 16px;"><? echo number_format($total_dead_cotton_defect_count,2); ?></th>
							<th width="50" id="total_thick_thin_defect_count" style="font-size: 16px;"><? echo number_format($total_thick_thin_defect_count,2); ?></th>
							<th width="50" id="total_needle_broken_mark_defect_count" style="font-size: 16px;"><? echo number_format($total_needle_broken_mark_defect_count,2); ?></th>
							<th width="50" id="total_side_center_shade_defect_count" style="font-size: 16px;"><? echo number_format($total_side_center_shade_defect_count,2); ?></th>
							<th width="50" id="total_bowing_defect_count" style="font-size: 16px;"><? echo number_format($total_bowing_defect_count,2); ?></th>
							<th width="50" id="total_uneven_defect_count" style="font-size: 16px;"><? echo number_format($total_uneven_defect_count,2); ?></th>
							<th width="50" id="total_dia_mark_defect_count" style="font-size: 16px;"><? echo number_format($total_dia_mark_defect_count,2); ?></th>
							<th width="50" id="total_dust_defect_count" style="font-size: 16px;"><? echo number_format($total_dust_defect_count,2); ?></th>
							<th width="50" id="total_hairy_defect_count" style="font-size: 16px;"><? echo number_format($total_hairy_defect_count,2); ?></th>
							<th width="50" id="total_gsm_hole_defect_count" style="font-size: 16px;"><? echo number_format($total_gsm_hole_defect_count,2); ?></th>
							<th width="50" id="total_running_shade_defect_count" style="font-size: 16px;"><? echo number_format($total_running_shade_defect_count,2); ?></th>
                            <th width="50" id="total_crease_mark_defect_count" style="font-size: 16px;"><? echo number_format($total_crease_mark_defect_count,2); ?></th>
                            <th width="50" id="total_loop_out_defect_count" style="font-size: 16px;"><? echo number_format($total_loop_out_defect_count,2); ?></th>
							<th width="50" id="total_cut_hole_defect_count" style="font-size: 16px;"><? echo number_format($total_cut_hole_defect_count,2); ?></th>
							<th width="50" id="total_point" style="font-size: 16px;"><? echo number_format($total_point,2) ?></th>
							<th width="60"> &nbsp;</th>
							<th width="60" >&nbsp;</th>
                            <th width="60"> &nbsp;</th>
							<th width="60">&nbsp; </th>
							<th width="100">&nbsp;</th>
						</tr>
					</tfoot>
				
				</table>	
				
			</div>
		</fieldset>
		<br>			
		<table cellspacing="0" cellpadding="0"  rules="all" style="margin-left: 300px; margin-bottom:30px ">

			<td valign="top" style="padding-right: 50px; ">
				
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600px" class="rpt_table" >
					<thead>
					<tr>
						<th colspan="6"><strong>Shrinkage%</strong></th>
					</tr>
						<tr>
							<th width="160">Barcode No	</th>
							<th width="140">Length(%) </th>
							<th width="140">Width(%)</th>
							<th width="140">Twisting(%)	</th>
						</tr>
					</thead>
				
					<tbody>
						<tr>
							<td style="padding: 5px; text-align:center" width="160">&nbsp;</td>
							<td style="padding: 5px; text-align:center" width="140" >&nbsp;</td>
							<td style="padding: 5px; text-align:center" width="140" >&nbsp;</td>
							<td style="padding: 5px; text-align:center" width="140" >&nbsp;</td>
						</tr>
						<tr>
							<td style="padding: 5px; text-align:center" width="160" >&nbsp;</td>
							<td style="padding: 5px; text-align:center" width="140" >&nbsp;</td>
							<td style="padding: 5px; text-align:center" width="140" >&nbsp;</td>
							<td style="padding: 5px; text-align:center" width="140" >&nbsp;</td>
						</tr>
						<tr>
							<td style="padding: 5px; text-align:center" width="160" >&nbsp;</td>
							<td style="padding: 5px; text-align:center" width="140" >&nbsp;</td>
							<td style="padding: 5px; text-align:center" width="140" >&nbsp;</td>
							<td style="padding: 5px; text-align:center" width="140" >&nbsp;</td>
						</tr>
						<tr>
							<td style="padding: 5px; text-align:center" width="160" >&nbsp;</td>
							<td style="padding: 5px; text-align:center" width="140" >&nbsp;</td>
							<td style="padding: 5px; text-align:center" width="140" >&nbsp;</td>
							<td style="padding: 5px; text-align:center" width="140" >&nbsp;</td>
						</tr>
						<tr>
							<td style="padding: 5px; text-align:center" width="160" >&nbsp;</td>
							<td style="padding: 5px; text-align:center" width="140" >&nbsp;</td>
							<td style="padding: 5px; text-align:center" width="140" >&nbsp;</td>
							<td style="padding: 5px; text-align:center" width="140" >&nbsp;</td>
						</tr>
						<tr>
							<td style="padding: 5px; text-align:center" width="160" >&nbsp;</td>
							<td style="padding: 5px; text-align:center" width="140" >&nbsp;</td>
							<td style="padding: 5px; text-align:center" width="140" >&nbsp;</td>
							<td style="padding: 5px; text-align:center" width="140" >&nbsp;</td>
						</tr>
					</tbody>
				</table>
				</div>
			</td>			

			<td valign="top" style="padding-right: 50px; ">
				
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="300px" class="rpt_table" >
					<thead>
						<tr>
							<th colspan="6"><strong>Summary</strong></th>
						</tr>
					
					</thead>
				
					<tbody>
						<tr>
							<td style="padding: 5px;" width="160"><strong>Total Penalty Point</strong> </td>
							<td style="padding: 5px" width="140"><? echo number_format($total_penalty_point,2); ?></td>
						</tr>
						<tr>
							<td style="padding: 5px;" width="160"><strong>Total Defect Point</strong></td>
							<td style="padding: 5px" width="140"><? echo number_format($total_point,2); ?></td>
						</tr>
						<tr>
							<td style="padding: 5px;" width="160"><strong>Defect %</strong></td>
							<td style="padding: 5px" width="140"><? echo number_format($total_defect_percent,2); ?></td>
						</tr>
						<tr>
							<td style="padding: 5px;" width="160"><strong>No of Roll</strong></td>
							<td style="padding: 5px" width="140"><? echo number_format($totalroll,2); ?></td>
						</tr>
						<tr>
							<td style="padding: 5px;" width="160"><strong>Inspected Qty</strong>	</td>
							<td style="padding: 5px" width="140"><? echo number_format($total_Inspected_Qty,2); ?></td>
						</tr>
						<tr>
							<td style="padding: 5px;" width="160"> <strong>Reject Qty</strong></td>
							<td style="padding: 5px" width="140"><? echo number_format($total_reject_qty,2); ?></td>
						</tr>
					</tbody>
				</table>
				</div>
			</td>	

			<td valign="top" style="padding-right: 50px; ">
				
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="300px" class="rpt_table" >
					<thead>
						<tr>
							<th colspan="6"><strong> Four-Points System :</strong></th>
						</tr>
					
					</thead>
				
					<tbody>
						<tr>
							<td style="padding: 5px;" width="160"><strong>Size of Defect</strong> </td>
							<td style="padding: 5px" width="140">Penalty</td>
						</tr>
						<tr>
							<td style="padding: 5px;" width="160"><strong>3 inches or Less</strong></td>
							<td style="padding: 5px" width="140">1 Point</td>
						</tr>
						<tr>
							<td style="padding: 5px;" width="160"><strong>Over 3, but not over 6</strong></td>
							<td style="padding: 5px" width="140">2 Point</td>
						</tr>
						<tr>
							<td style="padding: 5px;" width="160"><strong>Over 6, but not over 9</strong></td>
							<td style="padding: 5px" width="140">3 Point</td>
						</tr>
						<tr>
							<td style="padding: 5px;" width="160"><strong>Over 9 Inches	</strong></td>
							<td style="padding: 5px" width="140">4 Point</td>
						</tr>
						<tr>
							<td style="padding: 5px;" width="160"> <strong>Hole</strong></td>
							<td style="padding: 5px" width="140">2 Point</td>
						</tr>
						<tr>
							<td style="padding: 5px;" width="160"> <strong>Hole>2Inches	</strong></td>
							<td style="padding: 5px" width="140">4 Point</td>
						</tr>
					</tbody>
				</table>
				</div>
			</td>

			<td valign="top" style="padding-right: 50px; ">
				
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="300px" class="rpt_table" >
					<thead>
						<tr>
							<th colspan="6"><strong> Acceptance Point RA (Ind. Roll)</strong></th>
						</tr>
					
					</thead>
				
					<tbody>
						<tr>
							<td style="padding: 5px;" width="200" align="center"><strong>UP to 20 Points = A</strong> </td>
						</tr>
						<tr>
							<td style="padding: 5px;" width="200" align="center"><strong>21 to 28 Points = B	</strong></td>
						</tr>
						<tr>
							<td style="padding: 5px;" width="200" align="center"><strong>ABOVE 28 Points = REJECT ( R)</strong></td>
						</tr>
					</tbody>
				</table>
				</div>
			</td>

		</table>

		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1650px" class="rpt_table" style="margin-left: 300px; margin-bottom:30px ">
			<tr>
				<td style="height: 150px; width:150px; vertical-align: middle; text-align:center"> <strong><h1>Note :</h1></strong></td>
				<td style="padding: 10px; font-size:14px"  width="1550">&nbsp;</td>
			</tr>		
		</table>

	


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
	echo "$total_data####$filename####$reportType";
	exit();
}


?>
