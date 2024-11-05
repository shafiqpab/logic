<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

include('../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$permission=$_SESSION['page_permission'];

if($action=="load_report_format")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id=56 and is_deleted=0 and status_active=1");
	echo trim($print_report_format);
	exit();

}

if($action=="load_drop_down_sewing_output")
{
	$explode_data = explode("**",$data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];

	if($data==3)
	{
		if($db_type==0)
		{
			echo create_drop_down( "working_company_id", 100, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,party_type) order by supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "load_drop_down( 'requires/gmt_finishing_receive_controller', this.value, 'load_drop_down_working_location', 'wc_location_td' );load_drop_down( 'requires/gmt_finishing_receive_controller', document.getElementById('wc_location_id').value+'_'+document.getElementById('working_company_id').value, 'load_drop_down_wc_company_location_wise_floor', 'wc_floor_td' );",0,0 );
		}
		else
		{
			echo create_drop_down( "working_company_id", 100, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=22 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "load_drop_down( 'requires/gmt_finishing_receive_controller', this.value, 'load_drop_down_working_location', 'wc_location_td' );load_drop_down( 'requires/gmt_finishing_receive_controller', document.getElementById('wc_location_id').value+'_'+document.getElementById('working_company_id').value, 'load_drop_down_wc_company_location_wise_floor', 'wc_floor_td' );",0,0 );
		}
	}
	else if($data==1)
	{
 		echo create_drop_down( "working_company_id", 100, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "--- Select ---", "",  "load_drop_down( 'requires/gmt_finishing_receive_controller', this.value, 'load_drop_down_working_location', 'wc_location_td' );load_drop_down( 'requires/gmt_finishing_receive_controller', document.getElementById('wc_location_id').value+'_'+document.getElementById('working_company_id').value, 'load_drop_down_wc_company_location_wise_floor', 'wc_floor_td' )",0,0 );

	}
 	else
	{
		echo create_drop_down( "working_company_id", 100, $blank_array,"", 1, "--- Select ---", $selected, "",0,0 );
	}

	exit();
}
if ($action=="load_drop_down_working_location")
{
	//echo $data;die;
	echo create_drop_down( "wc_location_id", 100, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/gmt_finishing_receive_controller', document.getElementById('wc_location_id').value+'_'+document.getElementById('working_company_id').value, 'load_drop_down_wc_company_location_wise_floor', 'wc_floor_td' )" );
	exit();

}
if($action=="load_drop_down_lc_company_location"){
	//echo $data;die;
	$data=explode("***", $data);
	if(count($data)==1)
	{
		echo create_drop_down( "lc_location_id", 90, "select id,location_name from lib_location where company_id=$data[0] and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/gmt_finishing_receive_controller', document.getElementById('lc_location_id').value+'_'+document.getElementById('lc_company_id').value, 'load_drop_down_fini_company_location_wise_floor', 'fini_floor_td' )" );
	}
	else
	{

		echo create_drop_down( "lc_location_id", 90, "select id,location_name from lib_location where company_id=$data[0] and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", '', "load_drop_down( 'requires/gmt_finishing_receive_controller', document.getElementById('lc_location_id').value+'_'+document.getElementById('cbo_company_mst').value, 'load_drop_down_fini_company_location_wise_floor', 'fini_floor_pop_td' )" );

	}
	exit();
}
if($action=="load_drop_down_buyer"){
	//echo $data;die;
	echo create_drop_down( "cbo_buyer_id", 90, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data'  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
	exit();
}

if($action=="load_drop_down_wc_company_location_wise_floor"){
	//echo $data;die;
	$data=explode("_", $data);
	$company_id=$data[1];
	$location_id=$data[0];
	$sql="select id,floor_name from lib_prod_floor where company_id=$company_id and location_id=$location_id and status_active =1 and is_deleted=0 and production_process =5 order by floor_name";
	//echo $sql;die;
	echo create_drop_down("wc_floor", 90, $sql,"id,floor_name", 1, "-- Select Location --", $selected, "" );
	exit();
}

if($action=="load_drop_down_fini_company_location_wise_floor"){
	//echo $data;die;
	$data=explode("_", $data);
	$company_id=$data[1];
	$location_id=$data[0];
	$sql="select id,floor_name from lib_prod_floor where company_id=$company_id and location_id=$location_id and status_active =1 and is_deleted=0 and production_process =11 order by floor_name";
	//echo $sql;die;
	echo create_drop_down("finishing_floor", 90, $sql,"id,floor_name", 1, "-- Select Floor --", $selected, "" );
	exit();
}

if($action=="generate_report")
{
	//echo "test";die;
	//echo load_html_head_contents("Finish Barcode Generate", "../../", 1, 1, '', '');

	$process = array( &$_POST );
	//print_r($process);die;
	extract(check_magic_quote_gpc( $process ));
	$working_company_id = str_replace("'", "", $working_company_id);
	$wc_location_id = str_replace("'", "", $wc_location_id);
	$lc_company_id = str_replace("'", "", $lc_company_id);
	$lc_location_id = str_replace("'", "", $lc_location_id);
	$wc_floor = str_replace("'", "", $wc_floor);
	$txt_line_no_hidden = str_replace("'", "", $txt_line_no_hidden);
	$txt_line_no = str_replace("'", "", $txt_line_no);
	$cbo_buyer_id = str_replace("'", "", $cbo_buyer_id);
	$txt_search_text = str_replace("'", "", $txt_search_text);
	$cbo_search_by = str_replace("'", "", $cbo_search_by);
	$cbo_source = str_replace("'", "", $cbo_source);
	$bundle_level = str_replace("'", "", $cbo_bundle_level);

	$wc_location_condition='';
	//echo $txt_date_to.'__'.$txt_date_from;die;

	if ($wc_location_id != 0)
	{
		$wc_location_condition = " and a.location = ".$wc_location_id." ";
	}
	else
	{
		$wc_location_condition = "";
	}

	$working_company_condition = '';
	if ($working_company_id != 0)
	{
		$working_company_condition = " and a.serving_company = ".$working_company_id." ";
	}else{
		$working_company_condition="";
	}



	$lc_company_condition = '';
	if ($lc_company_id != 0)
	{
		//$lc_company_condition = " and a.company_id = ".$lc_company_id." ";
	}else{
		$lc_company_condition="";
	}

	$production_date_condition='';


	if (str_replace("'", "", $txt_date_from) != "" || str_replace("'", "", $txt_date_to) != "") {
		if ($db_type == 0) {
			$start_date = change_date_format(str_replace("'", "", $txt_date_from), "yyyy-mm-dd", "");
			$end_date = change_date_format(str_replace("'", "", $txt_date_to), "yyyy-mm-dd", "");
		} else if ($db_type == 2) {
			$start_date = change_date_format(str_replace("'", "", $txt_date_from), "", "", 1);
			$end_date = change_date_format(str_replace("'", "", $txt_date_to), "", "", 1);
		}
		$production_date_condition = " and a.production_date between '$start_date' and '$end_date'";

	}


	$wc_floor_condition='';

	if ($wc_floor != 0)
	{
		$wc_floor_condition = " and a.floor_id = ".$wc_floor." ";
	}else{
		$wc_floor_condition="";
	}
	$buyer_cond='';
	if(!empty($cbo_buyer_id))
	{
		$buyer_cond=" and f.buyer_name=$cbo_buyer_id";
	}

	$search_cond='';
	if(!empty($txt_search_text))
	{
		 if($cbo_search_by==1)
		{

			$search_cond=" and d.po_number='$txt_search_text'";
		}
		else if($cbo_search_by==2 )
		{
			$search_cond=" and f.style_ref_no='$txt_search_text'";
		}
		else if($cbo_search_by==3)
		{

			$search_cond=" and d.grouping='$txt_search_text'";
		}
	}


	//echo $sql;die;
	$date_cond=($db_type==2)? " TO_CHAR(a.production_hour,'HH24:MI') as production_hour " : " TIME_FORMAT( production_hour, '%H:%i' ) as production_hour ";
	if($db_type == 0){

		$sql = "SELECT a.production_date,a.po_break_down_id,a.challan_no, a.serving_company,a.location,d.grouping ,f.style_ref_no,d.po_number,a.item_number_id,a.floor_id,b.color_type_id ,c.color_number_id,a.country_id,sum(b.production_qnty) as production_qnty,c.size_number_id,a.company_id,f.buyer_name,a.production_source
	from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c, wo_po_break_down d,wo_po_details_master f
	where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=d.id and d.id=a.po_break_down_id and d.job_no_mst=f.job_no and a.production_type=5 and a.entry_break_down_type=3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.size_number_id is not null and length(c.color_number_id)>0 AND a.production_source in ($cbo_source)
	$working_company_condition	$wc_location_condition $wc_floor_condition $lc_company_condition  $production_date_condition $buyer_cond $search_cond
	group by a.production_date,a.po_break_down_id,a.challan_no, a.serving_company,a.location,f.style_ref_no,d.po_number,a.item_number_id,a.floor_id,b.color_type_id ,c.color_number_id,a.country_id,c.size_number_id,d.grouping,a.company_id,f.buyer_name,a.production_source";
	}
	else if($db_type == 2)
	{
		$bundle_column = "";
		$bundle_cod = "";
		if ($bundle_level==2) 
		{
			$bundle_column = ",b.bundle_no,b.barcode_no";
			$bundle_cod = " and b.barcode_no is not null";
		}	
			$sql = "SELECT a.production_date,a.po_break_down_id,a.challan_no, a.serving_company,a.location,d.grouping ,f.style_ref_no,d.po_number,a.item_number_id,a.floor_id,b.color_type_id ,c.color_number_id,a.country_id,sum(b.production_qnty) as production_qnty,c.size_number_id,a.company_id,f.buyer_name,a.production_source $bundle_column
		from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c, wo_po_break_down d,wo_po_details_master f
		where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=d.id and d.id=a.po_break_down_id and d.job_no_mst=f.job_no and a.production_type=5 and a.entry_break_down_type=3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.size_number_id is not null and c.color_number_id is not null   AND a.production_source in ($cbo_source)
		$working_company_condition	$wc_location_condition $wc_floor_condition $lc_company_condition  $production_date_condition $buyer_cond $search_cond $bundle_cod
		group by a.production_date,a.po_break_down_id,a.challan_no, a.serving_company,a.location,f.style_ref_no,d.po_number,a.item_number_id,a.floor_id,b.color_type_id ,c.color_number_id,a.country_id,c.size_number_id,d.grouping,a.company_id,f.buyer_name,a.production_source $bundle_column";
		 
	
	}
	// echo $sql;die;
	$result=sql_select($sql);



	$working_company_condition = '';
	if ($working_company_id != 0)
	{
		$working_company_condition = " and a.company_id = ".$working_company_id." ";
	}else{
		$working_company_condition="";
	}

	if ($wc_location_id != 0)
	{
		$wc_location_condition = " and a.location_id = ".$wc_location_id." ";
	}
	else
	{
		$wc_location_condition = "";
	}
	if ($bundle_level==2) 
	{
		$bundle_column = ",a.bundle_no,a.barcode_no"; 
	}
	$sql_recv="SELECT a.production_date,
				       a.po_break_down_id,
				       a.challan_no,
				       a.company_id,
				       a.location_id,
				       a.item_id,
				       a.floor_id,
				       a.color_type_id,
				       a.color_id,
				       a.country_id,
				       a.size_id,
				       a.qc_pass_qnty,
				       a.lc_company_id ,
				       a.fin_receive_qnty $bundle_column from gmt_finishing_receive_dtls a,wo_po_break_down d,wo_po_details_master f where a.po_break_down_id=d.id and d.job_no_mst=f.job_no and f.is_deleted=0 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_company_condition	$wc_location_condition $wc_floor_condition $lc_company_condition  $production_date_condition $search_cond";
					//    echo $sql_recv; die;
	$res_recv=sql_select($sql_recv);

	$res_recv_data=array();
	foreach ($res_recv as $row) {
		$barcode = "";
		if ($bundle_level==2) 
		{
			$barcode = "***".$row['BARCODE_NO']; 
		}

		$key=$row[csf('production_date')]."***".$row[csf('po_break_down_id')]."***".$row[csf('challan_no')]."***".$row[csf('company_id')]."***".$row[csf('location_id')]."***".$row[csf('item_id')]."***".$row[csf('floor_id')]."***".$row[csf('color_type_id')]."***".$row[csf('color_id')]."***".$row[csf('country_id')]."***".$row[csf('size_id')]."***".$row[csf('lc_company_id')]."***".$cbo_source.$barcode;
		$res_recv_data[$key]+=$row[csf('fin_receive_qnty')];

	}
	// echo "<pre>";print_r($res_recv_data); die;

	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
	$supplier_library=return_library_array( "SELECT id,supplier_name from lib_supplier", "id", "supplier_name"  );
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$floor_arr=return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$item_arr=return_library_array( "select id, item_name from lib_garment_item",'id','item_name');
	$company_short_arr=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	?>
	<div align="center" style="width:100%;">
		<fieldset style="width:1470px;">
    		<table cellpadding="0" width="1440" cellspacing="0" border="1"  class="rpt_table" rules="all" style="text-align: center;">
    		    <input type="hidden" name="row_count" id="row_count" value="<?=count($result)?>">
    			<thead>
	                <th width="30" style="word-break: break-all;">SL</th>
	                <th width="60" style="word-break: break-all;">Production<br>Date</th>
					<? 
						if ($bundle_level==2) 
						{
							?>
								<th width="70" style="word-break: break-all;">Bundle No.</th>
							<?	 
						}
					?>
	                <th width="50" style="word-break: break-all;">Ch. No</th>
	                <th width="65"  style="word-break: break-all;">Sew.<br>Company</th>
	                <th width="70" style="word-break: break-all;">Sew. Com. <br>Location</th>

	                <th width="65" style="word-break: break-all;">Lc<br>Company</th>
	                <th width="80" style="word-break: break-all;">Buyer</th>
	                <th width="80" style="word-break: break-all;">Internal <br>Reff.</th>
	                <th width="110" style="word-break: break-all;">Style Reff.</th>
	                <th width="100" style="word-break: break-all;">Order No</th>

	                <th width="100" style="word-break: break-all;">Item Name</th>
	                <th width="70" style="word-break: break-all;">Country</th>
	                <th width="60" style="word-break: break-all;">Color Type</th>
	                <th width="70" style="word-break: break-all;">GMT Color</th>
	                <th width="50" style="word-break: break-all;">Floor</th>

	                <th width="50" style="word-break: break-all;">GMT Size</th>
	                <th width="70" style="word-break: break-all;">QC Pass. qty</th>
	                <th width="70" style="word-break: break-all;">Fin. Receive</th>
	                <th style="word-break: break-all;">Remark</th>
            	</thead>
            </table>
            <div style="max-height: 250px;overflow-y: auto;overflow-x: hidden;" id="div_overflow">
	            <table cellpadding="0" width="1440" cellspacing="0" border="1" id="scanning_tbl" class="rpt_table" rules="all" style="text-align: center;margin-left: 15px;">
	        		<tbody>
	        			<?
	        				$i=1;
	        				foreach ($result as $row)
							{
	        					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	        					$po_break_down_id=$row[csf('po_break_down_id')];
	        					$challan_no=$row[csf('challan_no')];
	        					$country_id=$row[csf('country_id')];
	        					$company_id=$row[csf('serving_company')];
	        					$location_id=$row[csf('location')];
	        					$floor_id=$row[csf('floor_id')];
	        					$size_id=$row[csf('size_number_id')];
	        					$color_type_id=$row[csf('color_type_id')];
	        					$color_id= $row[csf('color_number_id')];
	        					$item_id=$row[csf('item_number_id')];
	        					$lc_company_id=$row[csf('company_id')];
	        					$sewing_line='';


	        					$production_date=$row[csf('production_date')];
	        					$production_hour=$row[csf('production_hour')];
	        					$qnty=$row[csf('production_qnty')];

								$barcode2 = "";
								if ($bundle_level==2) 
								{
									$barcode2 = "***".$row['BARCODE_NO']; 
								}
	        					$key=$row[csf('production_date')]."***".$row[csf('po_break_down_id')]."***".$row[csf('challan_no')]."***".$row[csf('serving_company')]."***".$row[csf('location')]."***".$row[csf('item_number_id')]."***".$row[csf('floor_id')]."***".$row[csf('color_type_id')]."***".$row[csf('color_number_id')]."***".$row[csf('country_id')]."***".$row[csf('size_number_id')]."***".$row[csf('company_id')]."***".$cbo_source.$barcode2;
								$res_recv_data[$key];

		        				$view_disabled=false;
		        				$generate_disabled=false;
		        				if($row[csf('production_qnty')]-$res_recv_data[$key]>0)
		        				{
			        				?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td style="word-break: break-all;" width="30"><p><? echo $i;?></p></td>
											<td style="word-break: break-all;" width="60"><p><?php echo change_date_format($row[csf('production_date')]);?>&nbsp;</p></td>
											
											<? 
												if ($bundle_level==2) 
												{
													?>
														<td align="left" width="70" style="word-break: break-all;"><?= $row['BUNDLE_NO'] ?></td>
													<?	 
												}
											?>
											<td style="word-break: break-all;" width="50"><p><?php echo $row[csf('challan_no')];?></p></td>
											<td style="word-break: break-all;" width="65">
												<p>
													<?
														if($row[csf('production_source')]==3){
															echo $supplier_library[$row[csf('serving_company')]];
														}
														else echo $company_short_arr[$row[csf('serving_company')]];
													?>
												</p>
											</td>
											<td style="word-break: break-all;" width="70"><p><?php echo $location_arr[$row[csf('location')]];?></p></td>
											<td style="word-break: break-all;" width="65"><p><?php echo $company_short_arr[$row[csf('company_id')]];?></p></td>
											<td style="word-break: break-all;" width="80"><p><?php echo $buyer_arr[$row[csf('buyer_name')]];?></p></td>
											<td style="word-break: break-all;" width="80"><p><?php echo $row[csf('grouping')];?></p></td>
											<td style="word-break: break-all;" width="110"><p><?php echo $row[csf('style_ref_no')];?></p></td>
											<td style="word-break: break-all;" width="100"><p><?php echo $row[csf('po_number')];?></p></td>
											<td style="word-break: break-all;" width="100"><p><?php echo $item_arr[$row[csf('item_number_id')]];?></p></td>
											<td style="word-break: break-all;" width="70"><p><? echo $country_library[$row[csf('country_id')]];?></p></td>
											<td style="word-break: break-all;" width="60"><p><? echo $color_type[$row[csf('color_type_id')]]; ?></p></td>
											<td style="word-break: break-all;" width="70"><p><?php echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
											<td style="word-break: break-all;" width="50"><p><?php echo $floor_arr[$row[csf('floor_id')]]; ?></p></td>

											<td style="word-break: break-all;" width="50"><p><? echo $size_arr[$row[csf('size_number_id')]];?></p></td>
											<td style="word-break: break-all;" width="70"><p><? echo $row[csf('production_qnty')]; ?></p></td>
											<td style="word-break: break-all;" width="70">
												<p>
													<input type="text" name="txtRecvQnty[]" class="text_boxes_numeric" placeholder="<?=$row[csf('production_qnty')]-$res_recv_data[$key];?>" id="txtRecvQnty_<?=$i;?>" onchange="compare_with_qc_pass(<?=$i?>)"  style="word-break: break-all;width:50px; ">
												</p>

												<input type="hidden" name="productionDate[]" id="productionDate_<?=$i;?>" value="<?=$production_date;?>" />
												<input type="hidden" name="txtQcPassQnty[]" id="txtQcPassQnty_<?=$i;?>" value="<?=$row[csf('production_qnty')]-$res_recv_data[$key];?>" />
												<input type="hidden" name="poBreakDownId[]" id="poBreakDownId_<?=$i;?>" value="<?=$po_break_down_id;?>" />
												<input type="hidden" name="challanNo[]" id="challanNo_<?=$i;?>" value="<?=$challan_no;?>"/>
												<input type="hidden" name="companyId[]" id="companyId_<?=$i;?>" value="<?=$company_id;?>"/>
												<input type="hidden" name="lcCompanyId[]" id="lcCompanyId_<?=$i;?>" value="<?=$lc_company_id;?>"/>
												<input type="hidden" name="locationId[]" id="locationId_<?=$i;?>" value="<?=$location_id;?>" />
												<input type="hidden" name="itemId[]" id="itemId_<?=$i;?>" value="<?=$item_id;?>" />
												<input type="hidden" name="flooId[]" id="floorId_<?=$i;?>" value="<?=$floor_id;?>"/>
												<input type="hidden" name="colorTypeId[]" id="colorTypeId_<?=$i;?>" value="<?=$color_type_id;?>" />
												<input type="hidden" name="colorId[]" id="colorId_<?=$i;?>" value="<?=$color_id;?>" />
												<input type="hidden" name="lineId[]" id="lineId_<?=$i;?>" value="0" />
												<input type="hidden" name="countryId[]" id="countryId_<?=$i;?>" value="<?=$country_id;?>" />
												<input type="hidden" name="productionHour[]" id="productionHour_<?=$i;?>" value="" />
												<input type="hidden" name="sizeId[]" id="sizeId_<?=$i;?>" value="<?=$size_id;?>" />
												<input type="hidden" name="sourceId[]" id="sourceId_<?=$i;?>" value="<?=$cbo_source;?>" />
												<input type="hidden" name="dtlsId[]" id="dtlsId_<?=$i;?>" value="" />
												<input type="hidden" name="bundleNo[]" id="bundleNo_<?=$i;?>" value="<?= $row['BUNDLE_NO'] ?>" />
												<input type="hidden" name="barcodeNo[]" id="barcodeNo_<?=$i;?>" value="<?= $row['BARCODE_NO'] ?>" />
											</td>
											<td style="word-break: break-all;">
												<input type="text" name="txtRemark[]" class="text_boxes" placeholder="wirte"  style="word-break: break-all;width: 80px;" id="txtRemark_<?=$i;?>" >
											</td>
										</tr>
				        			<? $i++;
									$total_qc_pass_qnty += $row[csf('production_qnty')];
									$total_fin_receive_qnty +=$row[csf('fin_receive_qnty')];
				        		}
			        		}
			        	?>
	        		</tbody>
	    	   </table>
            </div>
    	   <table>
        		<tfoot>
					<tr>
						<th width="1070" style="word-break: break-all; text-align: right;" colspan="16">Total</th>
						<th style="word-break: break-all; text-align: center;" width="70"><p><? echo $total_qc_pass_qnty; ?></p></th>
						<th width="70" style="word-break: break-all; text-align: center;">
							<input type="text" id="total_fin_receive_qnty" name="total_fin_receive_qnty" class="text_boxes_numeric" style="width:50px;" value="<? if($total_fin_receive_qnty !=''){ echo $total_fin_receive_qnty;} else{ echo 0;} ?>" readonly />
						</th>
						<th>&nbsp;</th>
					</tr>
        			<tr>
        				<td style="justify-content: center;text-align: center;" colspan="19">
        					<?
		        				if($i>1)
				        	    {
		    						echo load_submit_buttons($permission, "save_update_delete", 0, "", "fnc_reset_form()", 1);
				        	    }
			        	    ?>
		        	    </td>
        			</tr>
        		</tfoot>
    	   </table>
    	</fieldset>
    </div>

    <script type="text/javascript">
    	setFilterGrid("scanning_tbl",-1);
    </script>
  <?
  exit();

}
if($action=="populate_data_from_search_popup")
{
	$ex_data=explode("_",$data);
	$data_array=sql_select("SELECT RECEIVE_DATE,FLOOR_ID,REPORTING_TIME,COMPANY_ID,FINI_COMPANY_ID,FINI_LOCATION_ID,BUYER_ID from gmt_finishing_receive_mst where status_active=1 and  id='$ex_data[0]'");
	foreach ($data_array as $row)
	{


		echo "document.getElementById('working_company_id').value = '".$row[csf("COMPANY_ID")]."';\n";
		echo "document.getElementById('txt_recv_date').value = '".change_date_format($row[csf("RECEIVE_DATE")])."';\n";
		echo "document.getElementById('txt_reporting_time').value = '".$row[csf("REPORTING_TIME")]."';\n";
		echo "document.getElementById('finishing_floor').value = '".$row[csf("FLOOR_ID")]."';\n";
		echo "document.getElementById('lc_company_id').value = '".$row[csf("FINI_COMPANY_ID")]."';\n";
		echo "document.getElementById('lc_location_id').value = '".$row[csf("FINI_LOCATION_ID")]."';\n";
		echo "document.getElementById('cbo_buyer_id').value = '".$row[csf("BUYER_ID")]."';\n";

	}
	exit();
}
if($action=="populate_dtls_data")
{
	//echo "test";die;
	//echo load_html_head_contents("Finish Barcode Generate", "../../", 1, 1, '', '');

	$process = array( &$_POST );
	//print_r($process);die;

	 $id = str_replace("'", "", $data);
	 $sql="SELECT a.id,a.sys_no,b.job_no,b.style_ref_no,c.po_number,c.grouping,a.company_id,d.location_id, d.item_id,d.floor_id, d.color_type_id,d.color_id,d.country_id,d.size_id,d.qc_pass_qnty, d.fin_receive_qnty,d.po_break_down_id,d.challan_no,d.id as dtls_id,d.production_date,d.lc_company_id,d.remark,b.buyer_name,a.production_source,d.company_id as sew_company_id,d.bundle_no,d.barcode_no,a.is_bundle_level
		from gmt_finishing_receive_mst a , wo_po_details_master b,wo_po_break_down c, gmt_finishing_receive_dtls d
		where a.id=d.mst_id and d.po_break_down_id=c.id and b.job_no=c.job_no_mst and a.status_active=1 and d.status_active=1 and c.status_active=1 and d.status_active=1  and a.id=$id
		";
	// echo $sql;die;
	$result = sql_select($sql);
	$bundle_no = $result[0]['BUNDLE_NO'];
	$barcode_no = $result[0]['BARCODE_NO'];
	$is_bundle_level = $result[0]['IS_BUNDLE_LEVEL'];

	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
	$supplier_library=return_library_array( "SELECT id,supplier_name from lib_supplier", "id", "supplier_name"  );
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$floor_arr=return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$item_arr=return_library_array( "select id, item_name from lib_garment_item",'id','item_name');
	$company_short_arr=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	?>


	<div align="center" style="width:100%;">
		<fieldset style="width:1470px;">
    		<table cellpadding="0" width="1457" cellspacing="0" border="1"  class="rpt_table" rules="all" style="text-align: center;">
    			<input type="hidden" name="row_count" id="row_count" value="<?=count($result)?>">
    			<thead>
	                <th width="30" style="word-break: break-all;">SL</th>
	                <th width="60" style="word-break: break-all;">Production<br>Date</th>
					<? 
						if ($is_bundle_level) 
						{
							?>
								<th width="70" style="word-break: break-all;">Bundle No.</th>
							<?	 
						}
					?>
											
	                <th width="50" style="word-break: break-all;">Ch. Nooo </th>
	                <th width="65"  style="word-break: break-all;">Sew.<br>Company</th>
	                <th width="70" style="word-break: break-all;">Sew. Com. <br>Location</th>
	                <th width="65" style="word-break: break-all;">Lc<br>Company</th>
	                <th width="80" style="word-break: break-all;">Buyer</th>
	                <th width="80" style="word-break: break-all;">Internal <br>Reff.</th>
	                <th width="110" style="word-break: break-all;">Style Reff.</th>
	                <th width="100" style="word-break: break-all;">Order No</th>
	                <th width="100" style="word-break: break-all;">Item Name</th>
	                <th width="70" style="word-break: break-all;">Country</th>
	                <th width="60" style="word-break: break-all;">Color Type</th>
	                <th width="70" style="word-break: break-all;">GMT Color</th>
	                <th width="50" style="word-break: break-all;">Floor</th>

	                <th width="50" style="word-break: break-all;">GMT Size</th>
	                <th width="70" style="word-break: break-all;">QC Pass. qty</th>

	                <th width="80" style="word-break: break-all;">Fin. Receive</th>
	                <th style="word-break: break-all;">Remark</th>
            	</thead>
            </table>
            <div style="max-height: 250px;overflow-y: auto;overflow-x: hidden;" id="div_overflow">
	            <table cellpadding="0" width="1440" cellspacing="0" border="1" id="scanning_tbl" class="rpt_table" rules="all" style="text-align: center;">
        		<tbody>
        			<?php
        				$i=1;
        				foreach ($result as $row) {
        					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
        					$po_break_down_id=$row[csf('po_break_down_id')];
        					$challan_no=$row[csf('challan_no')];
        					$country_id=$row[csf('country_id')];
        					$company_id=$row[csf('sew_company_id')];
        					$location_id=$row[csf('location_id')];
        					$floor_id=$row[csf('floor_id')];
        					$size_id=$row[csf('size_id')];
        					$color_type_id=$row[csf('color_type_id')];
        					$color_id= $row[csf('color_id')];
        					$item_id=$row[csf('item_id')];
							$line_id=$row[csf('line_id')];
							$dtls_id=$row[csf('dtls_id')];
							$sewing_line='';

							$line_number=explode(",",$row[csf('line_id')]);
							foreach($line_number as $val)
							{
								if($sewing_line==''){
									$sewing_line=$sewing_line_arr[$val];
								}
								else {
									$sewing_line.=",".$sewing_line_arr[$val];
								}
							}

        					$production_date=$row[csf('production_date')];
        					$production_hour=$row[csf('production_hour')];
        					$qnty=$row[csf('qc_pass_qnty')];
        					$lc_company_id=$row[csf('lc_company_id')];
        					$remark=$row[csf('remark')];
        					$cbo_source=$row[csf('production_source')];
	        				?>

		        			<tr bgcolor="<? echo $bgcolor; ?>"  onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
		        				<td style="word-break: break-all;" width="30"><p><? echo $i;?></p></td>
		        				<td style="word-break: break-all;" width="60"><p><?php echo change_date_format($row[csf('production_date')]);?>&nbsp;</p></td>
								<? 
									if ($is_bundle_level) 
									{
										?>
											<td align="left" width="70" style="word-break: break-all;"><?= $row['BUNDLE_NO'] ?></td>
										<?	 
									}
								?>
		        				<td style="word-break: break-all;" width="50"><p><?php echo $row[csf('challan_no')];?></p></td>
		        				<td style="word-break: break-all;" width="65"><p><?php
				        			if($row[csf('production_source')]==3){
				        				echo $supplier_library[$row[csf('sew_company_id')]];
				        			}
				        			else echo $company_short_arr[$row[csf('sew_company_id')]];?>
				        			<?php //echo $company_short_arr[$row[csf('company_id')]];?></p></td>
		        				<td style="word-break: break-all;" width="70"><p><?php echo $location_arr[$row[csf('location')]];?></p></td>
		        				<td style="word-break: break-all;" width="65"><p><?php echo $company_short_arr[$row[csf('lc_company_id')]];?></p></td>
		        				<td style="word-break: break-all;" width="80"><p><?php echo $buyer_arr[$row[csf('buyer_name')]];?></p></td>
		        				<td style="word-break: break-all;" width="80"><p><?php echo $row[csf('grouping')];?></p></td>
		        				<td style="word-break: break-all;" width="110"><p><?php echo $row[csf('style_ref_no')];?></p></td>
		        				<td style="word-break: break-all;" width="100"><p><?php echo $row[csf('po_number')];?></p></td>
		        				<td style="word-break: break-all;" width="100"><p><?php echo $item_arr[$row[csf('item_id')]];?></p></td>
		        				<td style="word-break: break-all;" width="70"><p><? echo $country_library[$row[csf('country_id')]];?></p></td>
		        				<td style="word-break: break-all;" width="60"><p><? echo $color_type[$row[csf('color_type_id')]]; ?></p></td>
		        				<td style="word-break: break-all;" width="70"><p><?php echo $color_arr[$row[csf('color_id')]]; ?></p></td>
		                        <td style="word-break: break-all;" width="50"><p><?php echo $floor_arr[$row[csf('floor_id')]]; ?></p></td>

		                        <td style="word-break: break-all;" width="50"><p><? echo $size_arr[$row[csf('size_id')]];?></p></td>
		                        <td style="word-break: break-all;" width="70"><p><? echo $row[csf('qc_pass_qnty')] ?></p></td>
		                        <td style="word-break: break-all;" width="70">
		                        	<p>
		                        		<input type="text" name="txtRecvQnty[]" class="text_boxes_numeric" value="<?=$row[csf('fin_receive_qnty')];?>" style="width: 65px;" id="txtRecvQnty_<?=$i;?>" onchange="compare_with_total_recv(<?=$i?>)">
		                       		</p>
		                            <input type="hidden" name="productionDate[]" id="productionDate_<?=$i;?>" value="<?=$production_date;?>" />
		                            <input type="hidden" name="txtQcPassQnty[]" id="txtQcPassQnty_<?=$i;?>" value="<?=$row[csf('qc_pass_qnty')];?>" />
		                            <input type="hidden" name="poBreakDownId[]" id="poBreakDownId_<?=$i;?>" value="<?=$po_break_down_id;?>" />
		                            <input type="hidden" name="challanNo[]" id="challanNo_<?=$i;?>" value="<?=$challan_no;?>"/>
		                            <input type="hidden" name="companyId[]" id="companyId_<?=$i;?>" value="<?=$company_id;?>"/>
		                             <input type="hidden" name="lcCompanyId[]" id="lcCompanyId_<?=$i;?>" value="<?=$lc_company_id;?>"/>
		                            <input type="hidden" name="locationId[]" id="locationId_<?=$i;?>" value="<?=$location_id;?>" />
		                            <input type="hidden" name="itemId[]" id="itemId_<?=$i;?>" value="<?=$item_id;?>" />
		                            <input type="hidden" name="flooId[]" id="floorId_<?=$i;?>" value="<?=$floor_id;?>"/>
		                            <input type="hidden" name="colorTypeId[]" id="colorTypeId_<?=$i;?>" value="<?=$color_type_id;?>" />
		                            <input type="hidden" name="colorId[]" id="colorId_<?=$i;?>" value="<?=$color_id;?>" />
		                            <input type="hidden" name="lineId[]" id="lineId_<?=$i;?>" value="0" />
		                            <input type="hidden" name="countryId[]" id="countryId_<?=$i;?>" value="<?=$country_id;?>" />
		                            <input type="hidden" name="productionHour[]" id="productionHour_<?=$i;?>" value=">" />
		                            <input type="hidden" name="sizeId[]" id="sizeId_<?=$i;?>" value="<?=$size_id;?>" />
		                            <input type="hidden" name="sourceId[]" id="sourceId_<?=$i;?>" value="<?=$cbo_source;?>" />
		                            <input type="hidden" name="dtlsId[]" id="dtlsId_<?=$i;?>" value="<?=$dtls_id;?>" />
									<input type="hidden" name="bundleNo[]" id="bundleNo_<?=$i;?>" value="<?= $row['BUNDLE_NO'] ?>" />
									<input type="hidden" name="barcodeNo[]" id="barcodeNo_<?=$i;?>" value="<?= $row['BARCODE_NO'] ?>" />

		                        </td>
		                        <td  style="word-break: break-all;">
			                        <input type="text" name="txtRemark[]" class="text_boxes" value="<?=$remark;?>"  style="word-break: break-all;width:80px;" id="txtRemark_<?=$i;?>" >
			                    </td>

		        			</tr>
		        			<? $i++;
							$total_qc_pass_qnty +=$row[csf('qc_pass_qnty')];
							$total_fin_receive_qnty +=$row[csf('fin_receive_qnty')];
		        		}
		        		?>
        			</tbody>
				</table>
			</div>
    	   <table>
			<tfoot>
				<tr>
					<th width="1080" style="word-break: break-all; text-align: right;" colspan="16">Total</th>
					<th style="word-break: break-all; text-align: center;" width="70"><p><? echo $total_qc_pass_qnty; ?></p></th>
					<th width="70" style="word-break: break-all; text-align: center;">
						<input type="text" id="total_fin_receive_qnty" name="total_fin_receive_qnty" class="text_boxes_numeric" style="width:65px;" value="<? if($total_fin_receive_qnty !=''){ echo $total_fin_receive_qnty;} else{ echo 0;} ?>" readonly />
					</th>
					<th>&nbsp;</th>
				</tr>
				<tr>
					<td style="justify-content: center;text-align: center;" colspan="18">
						<?
							if($i>1)
							{
								echo load_submit_buttons($permission, "save_update_delete", 1, "", "fnc_reset_form()", 1);
							}
						?>
					</td>
				</tr>
			</tfoot>
				
    	   </table>
    </fieldset>

    </div>

     <script type="text/javascript">
    	setFilterGrid("scanning_tbl",-1);
    </script>
  <?php
  exit();

}


if($action=="save_update_delete")
{
	$process = array(&$_POST);
	//print_r($process);die;
	extract(check_magic_quote_gpc($process));
	$txt_recv_date=str_replace("'", "", $txt_recv_date);
	$txt_reporting_time=str_replace("'", "", $txt_reporting_time);
	$update_id=str_replace("'", "", $update_id);
	$working_company_id=str_replace("'", "", $working_company_id);
	$lc_company_id=str_replace("'", "", $lc_company_id);
	$finishing_floor=str_replace("'", "", $finishing_floor);



	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		if($db_type==0)
		{
			$year_cond=" and YEAR(insert_date)";
		}
		else if($db_type==2)
		{
			$year_cond=" and TO_CHAR(insert_date,'YYYY')";
		}

		$id = return_next_id_by_sequence("gmt_finishing_receive_mst_seq", "gmt_finishing_receive_mst", $con);

		$new_mrr_number=explode("*",return_mrr_number( $lc_company_id, '', 'GFR', date("Y",time()), 5, "select id, sys_number_prefix, sys_number_prefix_num from gmt_finishing_receive_mst where company_id=$lc_company_id and status_active=1 $year_cond=".date('Y',time())." order by id desc ", "sys_number_prefix", "sys_number_prefix_num"));

		$field_array = "id, sys_number_prefix, sys_number_prefix_num, sys_no, receive_date, company_id,fini_company_id,fini_location_id,buyer_id, floor_id, reporting_time,production_source,is_bundle_level,inserted_by, insert_date, status_active, is_deleted";



		$data_array = "(" . $id . ",'" . $new_mrr_number[1] . "'," . $new_mrr_number[2] . ",'" . $new_mrr_number[0] . "','" . $txt_recv_date . "'," . $lc_company_id. ",'" . str_replace("'", "", $lc_company_id). "','" . str_replace("'", "", $lc_location_id). "','" . str_replace("'", "", $cbo_buyer_id) . "'," . $finishing_floor . ",'" . $txt_reporting_time. "','" . str_replace("'", "", $cbo_source) . "','" . str_replace("'", "", $cbo_bundle_level) . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";
		//echo "10**insert into barcode_issue_to_finishing_mst (".$field_array.") values ".$data_array;die;

		$field_array_dtls = "id, mst_id, production_date,po_break_down_id,challan_no,company_id,location_id,item_id,floor_id,color_type_id,color_id,line_id,country_id,production_hour,size_id,qc_pass_qnty,fin_receive_qnty,lc_company_id,remark,bundle_no,barcode_no,inserted_by, insert_date,status_active,is_deleted";

		$barcodeNos = '';

		for ($j = 1; $j <= $tot_row; $j++) {
			$dtls_id = return_next_id_by_sequence("gmt_finishing_receive_dtls_seq", "gmt_finishing_receive_dtls", $con);
			$productionDate="productionDate_".$j;
			$poBreakDownId="poBreakDownId_".$j;
			$challanNo="challanNo_".$j;
			$companyId="companyId_".$j;
			$locationId="locationId_".$j;
			$itemId="itemId_".$j;
			$colorId="colorId_".$j;
			$sizeId="sizeId_".$j;
			$countryId="countryId_".$j;
			$lineId="lineId_".$j;
			$colorTypeId="colorTypeId_".$j;
			$productionHour="productionHour_".$j;
			$flooId="flooId_".$j;
			$txtRecvQnty="txtRecvQnty_".$j;
			$txtQcPassQnty="txtQcPassQnty_".$j;
			$lcCompanyId="lcCompanyId_".$j;
			$lcCompanyId=str_replace("'", "",$$lcCompanyId);
			$txtRemark="txtRemark_".$j;
			$bundleNo="bundleNo_".$j;
			$barcodeNo="barcodeNo_".$j;
			$txtRemark=str_replace("'", "",$$txtRemark);
			$bundleNo=str_replace("'", "",$$bundleNo);
			$barcodeNo=str_replace("'", "",$$barcodeNo);

			if ($data_array_dtls != "") $data_array_dtls .= ",";
			$data_array_dtls .= "(" . $dtls_id . "," . $id . ",'" . $$productionDate . "'," . $$poBreakDownId . ",'" . $$challanNo . "'," . $$companyId . ",'" . $$locationId . "'," . $$itemId . ",'" . $$flooId. "','" . $$colorTypeId. "'," . $$colorId. ",'" . $$lineId. "','" . $$countryId . "','" . $$productionHour. "'," . $$sizeId. "," . $$txtQcPassQnty. "," . $$txtRecvQnty . ",'" .$lcCompanyId . "','" .$txtRemark.  "','" .$bundleNo.  "','" .$barcodeNo.  "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";


		}
		//echo $barcodeNos;die;

		//echo "10**insert into barcode_issue_to_finishing_mst (".$field_array.") values ".$data_array;
		// echo "10**insert into gmt_finishing_receive_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;

		$rID = $rID2 = true;
		$rID = sql_insert("gmt_finishing_receive_mst", $field_array, $data_array, 0);
		$rID2 = sql_insert("gmt_finishing_receive_dtls", $field_array_dtls, $data_array_dtls, 0);




		if ($db_type == 0) {
			if ($rID && $rID2 ) {
				mysql_query("COMMIT");
				echo "0**" . $id . "**" . $new_mrr_number[0] ;
			} else {
				mysql_query("ROLLBACK");
				echo "10**$rID**$rID2";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID2 ) {
				oci_commit($con);
				echo "0**" . $id . "**" . $new_mrr_number[0] ;
			} else {
				oci_rollback($con);
				echo "10**$rID**$rID2**insert into gmt_finishing_receive_mst (".$field_array.") values ".$data_array."**insert into gmt_finishing_receive_dtls (".$field_array_dtls.") values ".$data_array_dtls;
			}
		}
		//check_table_status($_SESSION['menu_id'], 0);
		disconnect($con);
		die;
	}
	else if ($operation == 1)   // Update Here
	{
		//echo "test";die;
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		/*if (check_table_status($_SESSION['menu_id'], 1) == 0) {
			echo "15**1";
			die;
		}*/
		$update_id=str_replace("'", "", $update_id);



		$field_array = "receive_date*floor_id*reporting_time*updated_by*update_date";



		$data_array = "'" . $txt_recv_date . "'*" . $finishing_floor . "*'" . $txt_reporting_time . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";


		$field_array_dtls = "fin_receive_qnty*updated_by*update_date";


		$id_arr=[];
		for ($j = 1; $j <= $tot_row; $j++) {
			$productionDate="productionDate_".$j;
			$poBreakDownId="poBreakDownId_".$j;
			$challanNo="challanNo_".$j;
			$companyId="companyId_".$j;
			$locationId="locationId_".$j;
			$itemId="itemId_".$j;
			$colorId="colorId_".$j;
			$sizeId="sizeId_".$j;
			$countryId="countryId_".$j;
			$lineId="lineId_".$j;
			$colorTypeId="colorTypeId_".$j;
			$productionHour="productionHour_".$j;
			$flooId="flooId_".$j;
			$txtRecvQnty="txtRecvQnty_".$j;
			$txtQcPassQnty="txtQcPassQnty_".$j;
			$dtlsId="dtlsId_".$j;

			if ($data_array_dtls != "") $data_array_dtls .= ",";
			$data_array_dtls .= "(" . $$txtRecvQnty .  "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "')";

			$id_arr[]=str_replace("'",'',$$dtlsId);
			$dataArrDtlsUp[str_replace("'",'',$$dtlsId)] =explode("*",("'".$$txtRecvQnty."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));


		}


		$rID = $rID2 = true;

		$rID=sql_update("gmt_finishing_receive_mst",$field_array,$data_array,"id","".$update_id."",0);

		$rID2=execute_query(bulk_update_sql_statement("gmt_finishing_receive_dtls", "id",$field_array_dtls,$dataArrDtlsUp,$id_arr ));

		if ($db_type == 0) {
			if ($rID && $rID2 ) {
				mysql_query("COMMIT");
				echo "0**" . $update_id . "**" . $new_mrr_number[0] ;
			} else {
				mysql_query("ROLLBACK");
				echo "10**$rID**$rID2";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID2 ) {
				oci_commit($con);
				echo "0**" . $update_id . "**" . $new_mrr_number[0] ;
			} else {
				oci_rollback($con);
				echo "10**$rID**$rID2**".$data_array."**".bulk_update_sql_statement("gmt_finishing_receive_dtls", "id",$field_array_dtls,$dataArrDtlsUp,$id_arr );
			}
		}
		//check_table_status($_SESSION['menu_id'], 0);
		disconnect($con);
		die;

	}
	else if($operation==2) // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("gmt_finishing_receive_mst",$field_array,$data_array,"id","".$update_id."",0);
		$rID2=sql_delete("gmt_finishing_receive_dtls",$field_array,$data_array,"mst_id","".$update_id."",0);
		if($db_type==0)
		{
			if($rID && $rID2 ){
				mysql_query("COMMIT");
				echo "2**".$update_id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$update_id);
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 ){
				oci_commit($con);
				echo "2**".$update_id;
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
		die;
	}

}

if($action=="recv_qnty_total")
{

	$data=explode("**", $data);
	$poBreakDownId=$data[0];
	$companyId=$data[1];
	$locationId=$data[2];
	$challanNo=$data[3];
	$itemId=$data[4];
	$colorId=$data[5];
	$colorTypeId=$data[6];
	$sizeId=$data[7];
	$countryId=$data[8];
	$floorId=$data[9];
	$dtlsId=$data[10];

	 $sql="SELECT sum(fin_receive_qnty) as qnty
		from  gmt_finishing_receive_dtls d
		where status_active=1  and po_break_down_id=$poBreakDownId and company_id='$companyId' and location_id='$locationId' and challan_no='$challanNo' and item_id='$itemId' and color_id='$colorId' and color_type_id='$colorTypeId' and size_id='$sizeId' and country_id='$countryId' and floor_id='$floorId' and id not in ($dtlsId)
		";
	//echo $sql;
	$result = sql_select($sql);
	if(count($result))
	{
		echo $result[0][csf('qnty')];
	}
	else echo 0;
	exit();
}

if($action=="system_no_popup")
{
	echo load_html_head_contents("System No","../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(work_order_no)
		{
			document.getElementById('selected_work_order').value=work_order_no;
			parent.emailwindow.hide();
		}
    </script>
    </script>
	</head>

	<body>
		<div align="center" style="width:750;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="700" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
			        <thead>
			            <tr>
			                <th width="140">Fini. Company</th>
			                <th width="100">Fini. Location</th>
			                <th width="100">Fini. Floor</th>
			                <th width="120">Search By</th>
			                <th id="search_by_td_up" width="140">Please Enter System No</th>
			                <th width="130" colspan="2">Receive Date</th>
			                <th>&nbsp;</th>
			            </tr>
			        </thead>
			        <tbody>
			            <tr class="general">
			                <td align="center"> <input type="hidden" id="selected_work_order">
			                    <?
			                    if($company_id!="" && $company_id!=0){
									$on=1;
								}else{
									$on=0;
								}
			                    echo create_drop_down( "cbo_company_mst", 130, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company_id, "load_drop_down( 'gmt_finishing_receive_controller', this.value+'***pop', 'load_drop_down_lc_company_location', 'fn_location_pop_td' );",$on);
			                    ?>
			                </td>

			                 <td id="fn_location_pop_td">
                                    <?
                                        $arr=array();
                                     echo create_drop_down("lc_location_id", 90, "select id,location_name from lib_location where company_id=$company_id and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_drop_down( 'gmt_finishing_receive_controller', document.getElementById('lc_location_id').value+'_'+document.getElementById('cbo_company_mst').value, 'load_drop_down_fini_company_location_wise_floor', 'fini_floor_pop_td' )", '');
                                    ?>

                                </td>
                                 <td id="fini_floor_pop_td">
                                     <?
                                       $floor_arr=return_library_array( "select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and production_process =11",'id','floor_name');
                                        echo create_drop_down("finishing_floor", 90, $floor_arr, "", 1, "-- Select Floor --", 0, "", 0);
                                    ?>
                                </td>

			                <th align="center">
								<?
								$search_by_arr = array(1 => "System No", 2 => "Job No",3=>"Style Ref",4=>"Po No",5=>"Interl Ref");
								$dd = "change_search_event(this.value, '0*0*0*0*0', '0*0*0*0*0', '../../../') ";
								echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "", "", $dd, 0);
								?>
							</th>
			                <td align="center" id="search_by_td">
								<input type="text"  class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>


			                <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width: 60px;"  placeholder="From" /></td>
			                <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width: 60px;" placeholder="To" /></td>
			                <td align="center">
				                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('lc_location_id').value+'_'+document.getElementById('finishing_floor').value, 'create_work_order_search_list_view', 'search_div', 'gmt_finishing_receive_controller', 'setFilterGrid(\'list_view\',-1)') "  />
				            </td>
			            </tr>
			            <tr>
			                <th align="center" valign="middle" colspan="7"><? echo load_month_buttons(1); ?> </th>
			            </tr>
			        </tbody>
			    </table>
    			<div id="search_div"> </div>
    		</form>
   		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();

}
if($action=="create_work_order_search_list_view")
{
	$data=explode('_',$data);
	// echo "<pre>";
	// print_r($data);
	// echo "</pre>";


	$search_field_cond = '';
	$search_string=$data[4];
	$location=$data[5];
	$floor=$data[6];
	if ($data[4] != "") {
		if ($data[3] == 1) {
			$search_field_cond = " and a.sys_number_prefix_num =" . $search_string . "";
		}
		else if($data[3] == 2)
		{
			$search_field_cond = " and LOWER(b.job_no) like LOWER('%" . $search_string . "%')";
		}
		else if($data[3] == 3)
		{
			$search_field_cond = " and LOWER(b.style_ref_no) like LOWER('%" . $search_string . "%')";

		}
		else if($data[3]==4)
		{
			$search_field_cond = " and LOWER(c.po_number) like LOWER('%" . $search_string . "%')";
		}else{
			$search_field_cond = " and LOWER(c.grouping) like LOWER('%" . $search_string . "%')";
		}
	}
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }

	if($db_type==0)
	{
		if ($data[1]!="" &&  $data[2]!="") $issue_date  = "and a.receive_date  between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; else $issue_date ="";
	}

	if($db_type==2)
	{
		if ($data[1]!="" &&  $data[2]!="") $issue_date  = "and a.receive_date  between '".change_date_format($data[1], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."'"; else $issue_date ="";
	}
	$location_cond='';
	if(!empty($location))
	{
		$location_cond=" and a.fini_location_id =$location";

	}
	$floor_cond='';
	if(!empty($floor))
	{
		$floor_cond=" and a.floor_id =$floor";

	}



	 $sql="SELECT a.id,a.sys_no,b.job_no,b.style_ref_no,c.po_number,c.grouping,a.company_id,a.receive_date,d.color_id,d.size_id,sum(d.fin_receive_qnty) as qnty,a.fini_location_id,a.floor_id
		from gmt_finishing_receive_mst a , wo_po_details_master b,wo_po_break_down c, gmt_finishing_receive_dtls d
		where a.id=d.mst_id and d.po_break_down_id=c.id and b.job_no=c.job_no_mst and a.status_active=1 and d.status_active=1 and c.status_active=1 and d.status_active=1  $search_field_cond  $company  $issue_date $location_cond $floor_cond
		group by   a.id,a.sys_no,b.job_no,b.style_ref_no,c.po_number,c.grouping,a.company_id,a.receive_date,d.color_id,d.size_id,a.fini_location_id,a.floor_id ";
	//echo $sql;
	$result = sql_select($sql);

	?>
	<table class="rpt_table" id="rpt_tablelist_view" rules="all" width="1050" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr>
                <th width="35" style="word-break:break-all">SL No</th>
                <th width="50" style="word-break:break-all">Company<br>Name</th>
                <th width="115" style="word-break:break-all">System No</th>
                <th width="65" style="word-break:break-all">Fin.<br>Location</th>
                <th width="65" style="word-break:break-all">Fin.<br>Floor</th>
                <th width="70" style="word-break:break-all">Rcv. Date</th>
                <th width="115" style="word-break:break-all">Job No</th>
                <th width="110" style="word-break:break-all">Style Ref.</th>
                <th width="120" style="word-break:break-all">Po No</th>
                <th width="100" style="word-break:break-all">Internal Ref.</th>
                <th width="70" style="word-break:break-all">Gmts.<br>Color</th>
                <th width="70" style="word-break:break-all">Gmts.<br>Size</th>
                <th style="word-break:break-all">Rcv.<br>Qty</th>
            </tr>
        </thead>
    </table>
    <table class="rpt_table" id="list_view" rules="all" width="1050" cellspacing="0" cellpadding="0" border="0">
        <tbody>
			<?
			$i=0;

			$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
			$location_arr = return_library_array("select id,location_name  from lib_location", 'id', 'location_name');
			$floor_arr = return_library_array("select id,floor_name  from lib_prod_floor", 'id', 'floor_name');
			$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
			$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

			foreach($result as $row )
			{
				$i++;
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				$id=$row[csf('id')]."***".$row[csf('sys_no')];




            ?>
	            <tr onClick="js_set_value('<? echo $id; ?>')" style="cursor:pointer" id="tr_<? echo $i; ?>" height="20" bgcolor="<? echo $bgcolor; ?>">
	                <td width="35" style="word-break:break-all"><? echo $i; ?></td>

	                <td width="50" style="word-break:break-all"><p><? echo $comp[$row[csf('company_id')]]; ?></p></td>

	                <td width="115" style="word-break:break-all"><? echo $row[csf('sys_no')]; ?></td>
	                <td width="65" style="word-break:break-all"><? echo $location_arr[$row[csf('fini_location_id')]]; ?></td>
	                <td width="65" style="word-break:break-all"><? echo $floor_arr[$row[csf('floor_id')]]; ?></td>
	                <td width="70" style="word-break:break-all"><? echo change_date_format($row[csf('receive_date')]); ?></td>
	                <td width="115" style="word-break:break-all"><? echo $row[csf('job_no')]; ?></td>
	                <td width="110" style="word-break:break-all"><? echo $row[csf('style_ref_no')]; ?></td>

	                <td width="120" style="word-break:break-all"><? echo $row[csf('po_number')]; ?></td>

	                <td width="100" style="word-break:break-all"><? echo $row[csf('grouping')]; ?></td>
	                <td width="70" style="word-break:break-all"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
	                <td width="70" style="word-break:break-all"><? echo $size_arr[$row[csf('size_id')]]; ?></td>
	                <td style="word-break:break-all" align="right"><? echo fn_number_format($row[csf('qnty')],0,".",","); ?></td>
	            </tr>
            <?
			}
			?>
        </tbody>
    	</table>

	<?
	exit();
}


if ($action == "load_drop_down_po")
{
	$data = explode("**", $data);
	$booking_no = $data[0];
	$color_id = $data[1];
	$is_sales = $data[2];
	$sales_id = $data[3];
	if($is_sales == 1)
	{
		echo create_drop_down("cboPoNo_1", 130, "SELECT id, job_no FROM fabric_sales_order_mst WHERE id='$sales_id' and status_active=1 and is_deleted=0", "id,job_no", 1, "-- Select Po Number --", '0', "load_item_desc(this.value,this.id );", '', "", "", "", "", "", "", "cboPoNo[]");
	}
	else
	{
		echo create_drop_down("cboPoNo_1", 130, "SELECT a.id, a.po_number FROM wo_po_break_down a, wo_booking_dtls b WHERE a.id=b.po_break_down_id and b.booking_no='$booking_no' and b.fabric_color_id='$color_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number", "id,po_number", 1, "-- Select Po Number --", '0', "load_item_desc(this.value,this.id );", '', "", "", "", "", "", "", "cboPoNo[]");
	}

	exit();
}
?>
