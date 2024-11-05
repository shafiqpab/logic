<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

include('../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

$company_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
$floor_arr=return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');
$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
$sewing_line_arr=return_library_array( "select id, line_name from lib_sewing_line",'id','line_name');
$item_arr=return_library_array( "select id, item_name from lib_garment_item",'id','item_name');
$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );


if ($action=="load_drop_down_finishing_location")
{
	
	echo create_drop_down( "fc_location_id", 130, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/barcode_receive_by_finishing_controller', this.value, 'load_drop_down_fc_floor', 'fc_floor_td' );" );		
	exit(); 

}

if ($action=="load_drop_down_fc_floor")
{
	echo create_drop_down( "fc_floor_id", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (5) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );
	exit();
}

if($action=="load_drop_down_sewing_input")
{
	$explode_data = explode("**",$data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];

	if($data==3)
	{
		if($db_type==0)
		{
 			echo create_drop_down( "s_company_id", 130, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,party_type) order by supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "",0,0 );
		}
		else
		{
			echo create_drop_down( "s_company_id", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=22 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select--", $selected, "" );
		}
	}
 	else if($data==1)
  		echo create_drop_down( "s_company_id", 130, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "--- Select ---", "", "load_drop_down( 'requires/barcode_receive_by_finishing_controller', this.value, 'load_drop_down_sc_location', 'sc_location_td' );fnc_company_check(document.getElementById('cbo_knitting_source').value);",0,0 );
 	else
 		echo create_drop_down( "s_company_id", 130, $blank_array,"", 1, "--- Select ---", $selected, "",0,0 );
 	exit();
}
if ($action=="load_drop_down_sc_location")
{
	echo create_drop_down( "sc_location_id", 130, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",0 );
	exit();
}
if($action=="production_process_control")
{
	echo $data;die;
	echo "$('#hidden_variable_cntl').val('0');\n";
	echo "$('#hidden_preceding_process').val('0');\n";
    $control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=28 and company_name='$data'");
    if(count($control_and_preceding)>0)
    {
      echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf("is_control")]."');\n";
	  echo "$('#hidden_preceding_process').val('".$control_and_preceding[0][csf("preceding_page_id")]."');\n";
    }

	exit();
}
if ($action == "populate_barcode_data")
{
	//echo $data;die;
	$data=explode("**", $data);
	$qrcode=$data[0];
	$w_company_id=$data[1];
	$mst_id=$data[1];
	$barcodeData = '';
	$po_ids_arr = array();
	$po_details_array = array();
	$barcodeDataArr = array();
	$barcodeBuyerArr = array();



	$sql="select a.qrcode,a.item_id,a.country_id,a.color_id,a.size_id,a.mst_id,a.po_break_down_id,b.id,b.company_id,b.challan_no,b.location_id,b.fining_source,b.finishing_company_id,b.finishing_location_id,b.finishing_floor_id,extract(year from b.challan_date) as year,c.job_no_mst,c.grouping ,d.style_ref_no,c.po_number,d.buyer_name,e.line_id from barcode_issue_to_finishing_dtls a , barcode_issue_to_finishing_mst b , wo_po_break_down c, wo_po_details_master d ,finish_barcode e where a.mst_id=b.id and a.po_break_down_id=c.id and c.job_no_mst=d.job_no and a.qrcode=e.qrcode and  a.status_active=1 and a.is_deleted=0  and a.qrcode=$qrcode order by a.qrcode";
	//echo $sql;die;
	$data_array = sql_select($sql);	
	//print_r($data_array);die;
	$roll_details_array = array();
	$barcode_array = array();

	foreach ($data_array as $row)
	{
		$line_ids=explode(",", $row[csf('line_id')]);
		$line_id='';
		$line_name='';
		for($i=0;$i<count($line_ids);$i++){
			$line_id.=$line_ids[$i].',';
			$line_name.=$sewing_line_arr[$line_ids[$i]].',';
		}
		$line_id=chop($line_id,',');
		$line_name=chop($line_name,',');
		$barcodeData = $row[csf("qrcode")]."**".$row[csf("challan_no")]."**".$row[csf('finishing_company_id')]."**".$company_arr[$row[csf('finishing_company_id')]]."**".$row[csf('finishing_location_id')]."**".$location_arr[$row[csf('finishing_location_id')]]."**".$row[csf('finishing_floor_id')]."**".$floor_arr[$row[csf('finishing_floor_id')]]."**".$line_id."**".$line_name."**".$row[csf('year')]."**".$row[csf('job_no_mst')]."**".$row[csf('buyer_name')]."**".$buyer_arr[$row[csf('buyer_name')]]."**".$row[csf('style_ref_no')]."**".$row[csf('grouping')]."**".$row[csf('po_break_down_id')]."**".$row[csf('po_number')]."**".$row[csf('item_id')]."**".$item_arr[$row[csf('item_id')]]."**".$row[csf('country_id')]."**".$country_arr[$row[csf('country_id')]]."**".$row[csf('color_id')]."**".$color_arr[$row[csf('color_id')]]."**".$row[csf('size_id')]."**".$size_arr[$row[csf('size_id')]] ."**".$row[csf('company_id')]."**".$row[csf('location_id')]."**".$row[csf('fining_source')];
           
		break;
	}
	if($mst_id==0){
		$received_qrcode=sql_select("select qrcode from barcode_receive_dtls where status_active=1 and is_deleted=0  and qrcode=$qrcode");
		if(count($received_qrcode)){
			echo "Already Received";
			exit();
		}
	}
	
	
	echo $barcodeData;

	exit();
}
if($action=="save_update_delete"){
	$process = array(&$_POST);
	//print_r($process);die;
	extract(check_magic_quote_gpc($process));
	$s_company_id=str_replace("'", "", $s_company_id);
	$sc_location_id=str_replace("'", "", $sc_location_id);
	$cbo_knitting_source=str_replace("'", "", $cbo_knitting_source);
	$f_company_id=str_replace("'", "", $f_company_id);
	$fc_location_id=str_replace("'", "", $fc_location_id);
	$fc_floor_id=str_replace("'", "", $fc_floor_id);
	//echo $challan_date;die;
	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		
		

		$id = return_next_id_by_sequence("barcode_receive_mst_seq", "barcode_receive_mst", $con);
		
		$new_mrr_number = explode("*", return_next_id_by_sequence("barcode_receive_mst_seq", "barcode_receive_mst",$con,1,$f_company_id,date("Y",time()) ));
		
		$field_array = "id,mrr_number_prefix,mrr_number_prefix_num,mrr_no,sew_company_id,sew_location_id,source,finishing_company_id,finishing_location_id,finishing_floor_id,inserted_by,insert_date,is_deleted";
		
		$data_array = "(" . $id . ",'" . $new_mrr_number[1] . "'," . $new_mrr_number[2] . ",'" . $new_mrr_number[0] . "'," . $s_company_id . "," . $sc_location_id . "," . $cbo_knitting_source . "," . $f_company_id . "," . $fc_location_id . "," . $fc_floor_id . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',0)";
		//echo "10**insert into barcode_issue_to_finishing_mst (".$field_array.") values ".$data_array;die;

		$field_array_dtls = "id, mst_id, po_break_down_id, color_id,size_id, country_id, item_id,line_id, qrcode,inserted_by, insert_date";

		$barcodeNos = '';
		$dtls_id = return_next_id_by_sequence("barcode_receive_dtls_seq", "barcode_receive_dtls", $con);
		for ($j = 1; $j <= $tot_row; $j++) {
			
			$jobNo="jobNo_".$j;
			$orderId="orderId_".$j;
			$itemId="itemId_".$j;
			$colorId="colorId_".$j;
			$sizeId="sizeId_".$j;
			$barcodeNo="barcodeNo_".$j;
			$buyerId="buyerId_".$j;
			$countryId="countryId_".$j;
			$lineId="lineId_".$j;
			$sql_check="select qrcode from barcode_issue_to_finishing_dtls where status_active=1 and is_deleted=0 and qrcode=".$$barcodeNo."";
			$res_check=sql_select($sql_check);
			if(count($res_check))
			{
				if ($data_array_dtls != "") $data_array_dtls .= ",";
				$data_array_dtls .= "(" . $dtls_id . "," . $id . "," . $$orderId . "," . $$colorId . "," . $$sizeId . "," . $$countryId . "," . $$itemId . ",'" .$$lineId."',". $$barcodeNo .  "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				$barcodeNos .= $$barcodeNo . "__" . $dtls_id . "__";

				$dtls_id = $dtls_id + 1;
			}else
			{
				if ($db_type == 0)
				{
					
					mysql_query("ROLLBACK");
					echo "121**".$$barcodeNo." is not valid**0";
					
				} 
				else if ($db_type == 2 || $db_type == 1) 
				{
					
					oci_rollback($con);
					echo "121**".$$barcodeNo." is not valid**0";
					
				}
				disconnect($con);
				die;

			}
			unset($sql_check);
			unset($res_check);
			
			
			
		}
		//echo $barcodeNos;die;

		//echo "10**insert into barcode_receive_mst (".$field_array.") values ".$data_array;
		//echo "10**insert into barcode_receive_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;

		$rID = $rID2 = true;
		$rID = sql_insert("barcode_receive_mst", $field_array, $data_array, 0);
		$rID2 = sql_insert("barcode_receive_dtls", $field_array_dtls, $data_array_dtls, 0);
		

		//echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$statusUsed;die;

		if ($db_type == 0) {
			if ($rID && $rID2 ) {
				mysql_query("COMMIT");
				echo "0**" . $id . "**" . $new_mrr_number[0] . "**" . substr($barcodeNos, 0, -1);
			} else {
				mysql_query("ROLLBACK");
				echo "5**0**0";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID2 ) {
				oci_commit($con);
				echo "0**" . $id . "**" . $new_mrr_number[0] . "**" . substr($barcodeNos, 0, -1);
			} else {
				oci_rollback($con);
				echo "5**0**0";
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
		$pre_barcode_res=sql_select("select * from barcode_receive_dtls where mst_id=$update_id and status_active=1 and is_deleted=0");
		$pre_barcode_arr=array();
		$receive_barcode_arr=array();
		$ok=true;
		foreach ($pre_barcode_res as $row) {
			array_push($pre_barcode_arr,$row[csf('qrcode')]);
		}

		
		$insert_field_array_dtls = "id, mst_id, po_break_down_id, color_id,size_id, country_id, item_id,line_id, qrcode,updated_by, update_date";
		

		$barcodeNos = '';
		$dtls_id = return_next_id_by_sequence("barcode_receive_dtls_seq", "barcode_receive_dtls", $con);
		$insert_data_array_dtls="";
		$statusChange=true;
		$field_array_status = "updated_by*update_date*status_active*is_deleted";
		$data_array_status = $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1";
		$barcode_cur=array();
		for ($j = 1; $j <= $tot_row; $j++) {
			
			$jobNo="jobNo_".$j;
			$orderId="orderId_".$j;
			$itemId="itemId_".$j;
			$colorId="colorId_".$j;
			$sizeId="sizeId_".$j;
			$barcodeNo="barcodeNo_".$j;
			$buyerId="buyerId_".$j;
			$countryId="countryId_".$j;
			$countryId="countryId_".$j;
			$lineId="lineId_".$j;
			array_push($barcode_cur, $$barcodeNo);

			if(!in_array($$barcodeNo, $pre_barcode_arr) )
			{
				$sql_check="select qrcode from barcode_issue_to_finishing_dtls where status_active=1 and is_deleted=0 and qrcode=".$$barcodeNo."";
				$check_res=sql_select($sql_check);
				if(count($check_res)){
					if ($insert_data_array_dtls != "") $insert_data_array_dtls .= ",";
					$insert_data_array_dtls .= "(" . $dtls_id . "," . $update_id . "," . $$orderId . "," . $$colorId . "," . $$sizeId . "," . $$countryId . "," . $$itemId .",'".$$lineId. "'," . $$barcodeNo .  "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
					$barcodeNos .= $$barcodeNo . "__" . $dtls_id . "__";
					
					$dtls_id = $dtls_id + 1;
				}else{
					if ($db_type == 0) {
						mysql_query("ROLLBACK");
						echo "6**" . str_replace("'", '', $update_id) . "**";
						
					} else if ($db_type == 2 || $db_type == 1) {
						oci_rollback($con);
						echo "6**" . str_replace("'", '', $update_id) . "**1";
					}

				}
				unset($sql_check);
				unset($check_res);
				
			}
		}
		
		$rID2 = true;
		foreach ($pre_barcode_arr as  $value) {
			if(!in_array($value,$barcode_cur)){
				$statusChange = sql_multirow_update("barcode_receive_dtls", $field_array_status, $data_array_status, "qrcode", $value, 0);
			}
			
			if($statusChange==false){
				$rID2=false;
			}
		}
		//echo "test";die;
		

		//echo "insert into com_export_proceed_rlzn_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID1 = true;

		
		if($insert_data_array_dtls!=""){
			$rID1 = sql_insert("barcode_receive_dtls", $insert_field_array_dtls, $insert_data_array_dtls, 0);
		
		}
	

		if ($db_type == 0) {
			if ($rID1 && $rID2 ) {
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", '', $update_id) . "**" . str_replace("'", '', $txt_mrr_no) . "**" . substr($barcodeNos, 0, -1);
			} else {
				mysql_query("ROLLBACK");
				echo "6**" . str_replace("'", '', $update_id) . "**";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID1 && $rID2 ) {
				oci_commit($con);
				echo "1**" . str_replace("'", '', $update_id) . "**" . str_replace("'", '', $txt_mrr_no) . "**" . substr($barcodeNos, 0, -1);
			} else {
				oci_rollback($con);
				echo "6**" . str_replace("'", '', $update_id) . "**1";
			}
		}
		//check_table_status($_SESSION['menu_id'], 0);
		disconnect($con);
		die;
	}

	exit();
}

if ($action == "check_if_barcode_scanned")
{
	$barcodeData = '';
	$po_ids_arr = array();
	$po_details_array = array();
	$barcodeDataArr = array();
	$barcodeBuyerArr = array();
	
	
	

	$sql="select a.id,a.mrr_no
	from barcode_receive_mst a, barcode_receive_dtls b 
	WHERE a.id=b.mst_id   and b.status_active=1 and b.is_deleted=0 and b.qrcode =$data";
	//echo $sql;die;
	$data_array = sql_select($sql);
	//print_r($data_array);die;
	$roll_details_array = array();
	$barcode_array = array();

	foreach ($data_array as $row)
	{
		$barcodeData = $row[csf("mrr_no")]  ;
		break;
	}
	echo $barcodeData;

	exit();
}


if ($action == "mrr_popup")
{
	echo load_html_head_contents("MRR Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>

		function js_set_value(data, barcode_nos) {
			$('#hidden_data').val(data);
			$('#hidden_barcode_nos').val(barcode_nos);
			parent.emailwindow.hide();
		}
		

	</script>

	</head>

	<body>
		<div align="center" style="width:860px;">
			<form name="searchwofrm" id="searchwofrm">
				<fieldset style="width:860px; margin-left:2px">
					<table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table">
						<thead>
							<th>Finishing Company</th>
							
							<th>Search By</th>
							<th id="search_by_td_up" width="180">Please Enter MRR No</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
								class="formbutton"/>
								<input type="hidden" name="hidden_data" id="hidden_data">
								<input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">
							</th>
						</thead>
						<tr class="general">
							<td align="center">
								<? 
								if($company_id!="" && $company_id!=0){
									$on=1;
								}else{
									$on=0;
								}
								echo create_drop_down("w_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", 'id,company_name',1, '-- Select Company --', $company_id, "", $on); ?>
							</td>
							
							<td align="center">
								<?
								$search_by_arr = array(1 => "MRR No", 2 => "Barcode No");
								$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../'); ";
								echo create_drop_down("cbo_search_by", 100, $search_by_arr, "", 0, "--Select--", 1, $dd, 0);
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show"
								onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('w_company_id').value, 'create_challan_search_list_view', 'search_div', 'barcode_receive_by_finishing_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
								style="width:100px;"/>
							</td>
						</tr>
						
					</table>
					<div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
<?
}

if ($action == "create_challan_search_list_view")
{
	//echo $data;die;
	$data = explode("_", $data);
	$search_string = $data[0];
	$search_by = $data[1];
	$company_id = $data[2];
	
	

	if ($company_id == 0) {
		echo "Please Select Company First.";
		die;
	}

	$search_field_cond = "";
	if (trim($data[0]) != "")
	{	$barcode_no = trim($data[0]);
		if ($search_by == 1) $search_field_cond = "and mrr_number_prefix_num like '$search_string'";
		else if ($search_by == 2) $search_field_cond = "and b.qrcode=$barcode_no";
	}

	if ($db_type == 0) {
		
		$barcode_arr = return_library_array("select mst_id, group_concat(qrcode order by id desc) as qrcode from barcode_issue_to_finishing_dtls where  status_active=1 and is_deleted=0 group by mst_id", 'mst_id', 'qrcode');
	} else if ($db_type == 2) {
		
		$barcode_arr = return_library_array("select mst_id, LISTAGG(qrcode, ',') WITHIN GROUP (ORDER BY id desc) as qrcode from barcode_receive_dtls where  status_active=1 and is_deleted=0 group by mst_id", 'mst_id', 'qrcode');
	} 
	$sql = "select a.id, mrr_number_prefix_num, a.mrr_no, a.source, a.finishing_company_id, a.finishing_location_id,a.sew_company_id, a.sew_location_id,a.finishing_floor_id from barcode_receive_mst a,barcode_receive_dtls b  where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.finishing_company_id=$company_id and b.status_active=1 and b.is_deleted=0  and a.finishing_company_id=$company_id $search_field_cond   
	  group by a.id, mrr_number_prefix_num, a.mrr_no, a.source, a.finishing_company_id, a.finishing_location_id,a.sew_company_id, a.sew_location_id,a.finishing_floor_id order by mrr_number_prefix_num asc";
 	//echo $sql;die;
	$result = sql_select($sql);

	$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="930" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="130">Sew Company</th>
			<th width="100">SC. Location</th>
			<th width="70">Mrr No</th>
			<th width="90"> Source</th>
			<th width="130">Finishing Company</th>
			<th width="100">FC Location</th>
			<th width="100">FC Floor</th>
			
		</thead>
		<tbody id="tbl_list_search">
			
		
		 
		<?
		$i = 1;
		foreach ($result as $row) {
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

			$knit_comp = "&nbsp;";
			if ($row[csf('source')] == 1)
				$knit_comp = $company_arr[$row[csf('source')]];
			else
				$knit_comp = $supllier_arr[$row[csf('source')]];

			$data = $row[csf('id')] . "**" . $row[csf('mrr_no')] . "**" . $row[csf('sew_company_id')] . "**" . $row[csf('sew_location_id')] . "**" . $row[csf('source')] . "**" . $row[csf('finishing_company_id')] . "**" . $knit_comp . "**"   . $row[csf('finishing_location_id')] . "**" . $row[csf('finishing_floor_id')];
			$barcode_nos = $barcode_arr[$row[csf('id')]];
			//print_r($barcode_arr);
			//echo  $row[csf('id')];die;
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="js_set_value('<? echo $data; ?>','<? echo $barcode_nos; ?>');">
				<td  align="center"><? echo $i; ?></td>
				<td  align="center"><? echo $company_arr[$row[csf('sew_company_id')]]; ?></td>
				<td ><? echo $location_arr[$row[csf('sew_location_id')]]; ?></td>
				<td >
					<? echo $row[csf('mrr_no')];?>
				</td>
				<td  align="center"><? echo $knit_comp; ?></td>
				<td  align="center"><? echo $company_arr[$row[csf('finishing_company_id')]]; ?></td>
				
				<td  align="center"><? echo $location_arr[$row[csf('finishing_location_id')]]; ?></td>
				<td  align="right"><? echo $floor_arr[$row[csf('finishing_floor_id')]]; ?></td>
				
			</tr>
			<?
			$i++;
		}
		?>
		</tbody>
	</table>
	</div>
	<?
	exit();
}


?>
