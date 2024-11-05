<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

	include('../../../includes/common.php');
	require_once('../../../includes/class3/class.conditions.php');
	require_once('../../../includes/class3/class.reports.php');
	require_once('../../../includes/class3/class.fabrics.php');

	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');

	$data=$_REQUEST['data'];
	$action=$_REQUEST['action'];

//========== user credential start ========
	$user_id = $_SESSION['logic_erp']['user_id'];
	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, company_location_id, item_cate_id FROM user_passwd where id=$user_id");
//echo "SELECT unit_id as company_id,store_location_id,item_cate_id FROM user_passwd where id=$user_id";
	$company_id = $userCredential[0][csf('company_id')];
	$company_location_id = $userCredential[0][csf('company_location_id')];
	$store_location_id = $userCredential[0][csf('store_location_id')];

	$item_cate_id = $userCredential[0][csf('item_cate_id')];
//var_dump($item_cate_id);
	$company_credential_cond = $com_location_credential_cond = $store_location_credential_cond = $item_cate_credential_cond = "";

	if ($company_id >0) {
		$company_credential_cond = " and comp.id in($company_id)";
	}
	if ($company_location_id !='') {
		$com_location_credential_cond = " and id in($company_location_id)";
	}
	/*if ($store_location_id !='') {
		$store_location_credential_cond = "and a.id in($store_location_id)";
	}*/
	if($item_cate_id !='') {
		$item_cate_credential_cond = $item_cate_id ;
	}
	if ($store_location_id != '') {$store_location_credential_cond = "and a.id in($store_location_id)";} else { $store_location_credential_cond = "";}

//========== user credential end ==========

	if ($action == "check_conversion_rate") {
		$data = explode("**", $data);
		if ($db_type == 0) {
			$conversion_date = change_date_format($data[1], "Y-m-d", "-", 1);
		} else {
			$conversion_date = change_date_format($data[1], "d-M-y", "-", 1);
		}
		$exchange_rate = set_conversion_rate($data[0], $conversion_date, $data[2]);
		echo $exchange_rate;
		exit();
	}

	if ($action=="upto_variable_settings")
	{
		extract($_REQUEST);
		/*echo "select store_method from variable_settings_inventory where company_name='$cbo_company_id' and item_category_id=13 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1";die;*/
		echo $variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name='$cbo_company_id' and item_category_id=2 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
		exit();
	}
	//====================Location ACTION========
	if ($action=="load_drop_down_buyer")
	{
		if($data != 0)
		{
			echo create_drop_down("cbo_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", 0, "");
		}
		else
		{
			echo create_drop_down( "cbo_buyer_name", 162, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
			exit();
		}
	}

	if ($action=="load_drop_down_po_body_part")
	{
		$data = explode("_", $data);
		$order_no = $data[0];
		$booking_no = $data[1];
		$booking_without_order = $data[2];
		if($booking_without_order == 0){
			$body_part_sql = sql_select("SELECT a.body_part_id from wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0  and b.status_active = 1 and b.is_deleted = 0 and b.pre_cost_fabric_cost_dtls_id = a.id and b.po_break_down_id in ($order_no) and b.booking_no='$booking_no' and b.booking_type =1  group by a.body_part_id union all select b.body_part_id from wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_booking_dtls c where b.job_no=c.job_no and a.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.pre_cost_fabric_cost_dtls_id = a.id and c.po_break_down_id in ($order_no) and c.booking_no='$booking_no' and a.fabric_description = b.id and c.booking_type = 4 group by b.body_part_id ");
		}else{
			$body_part_sql = sql_select("select a.booking_no, b.fabric_color, b.body_part as body_part_id from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no = b.booking_no and b.status_active=1 and b.booking_no = '$booking_no'");
		}

		foreach ($body_part_sql as $row)
		{
			$body_part_arr[$row[csf("body_part_id")]] = $row[csf("body_part_id")];
		}
		$body_part_ids = implode(",",array_filter($body_part_arr));

		echo create_drop_down( "cbo_body_part", 182, $body_part,"", 1, "-- Select Body Part --", 0, "",0,$body_part_ids );

		exit();
	}

	if ($action=="load_drop_down_color")
	{
		$data = explode("_", $data);
		$order_no = $data[0];
		$booking_no = $data[1];
		$booking_without_order = $data[2];
		$colorID = $data[3];
		if($booking_without_order == 0){
			$color_sql = sql_select("select b.id,b.color_name from wo_booking_dtls a, lib_color b where a.fabric_color_id = b.id and a.po_break_down_id in ($order_no) and a.booking_no='$booking_no' and b.status_active=1 group by b.id,b.color_name order by b.color_name");
		}
		else
		{
			$color_sql = sql_select("select b.id,b.color_name from wo_non_ord_samp_booking_dtls a, lib_color b where a.fabric_color = b.id and a.booking_no='$booking_no' and b.status_active=1 group by b.id,b.color_name order by b.color_name");
		}

		foreach ($color_sql as $row)
		{
			if($colorID==$row[csf("id")])
			{
				$colorName=	$row[csf("color_name")];
			}
			$color_dataArray[$row[csf("color_name")]] = $row[csf("color_name")];
		}
		$color_dataArray = array_filter($color_dataArray);

		//echo create_drop_down( "txt_color", 180, $color_arr,"", 1, "-- Select Color --", 0, "",0,"" );
		echo create_drop_down( "txt_color", 180, $color_dataArray,"", 0, "-- Select Color --", $colorName, "",0,"" );

		exit();
	}

	if ($action=="load_drop_down_supplier")
	{
		echo create_drop_down( "cbo_buyer_name", 162, "select a.supplier_name, a.id from lib_supplier a, lib_supplier_tag_company b where a.id = b.supplier_id  and a.status_active =1 and b.tag_company = $data group by a.supplier_name, a.id","id,supplier_name", 1, "-- Select Supplier --", 0, "" );

		exit();
	}


if($action=="lcSc_popup_search")
{
		echo load_html_head_contents("Knit Finish Fabric Receive By garments Entry Form", "../../../", 1, 1,'','1','');
		extract($_REQUEST);
		?>

		<script>

			function js_set_value(data)
			{
				var data=data.split("_");

				$('#hidden_lcSc_id').val(data[0]);
				$('#is_lcSc').val(data[1]);
				$('#company_id').val(data[2]);

				if(data[3]=="") { data[3]=0; }
				$('#import_btb').val(data[3]);
				$('#lc_sc').val(data[4]);
				parent.emailwindow.hide();
			}
			function fnc_load_buyer_supplier(search_with)
			{
				var cbo_company_name = $("#cbo_company_name").val();
				if(search_with == 2)
				{
					load_drop_down( 'knit_finish_fabric_receive_by_garments_controller', cbo_company_name, 'load_drop_down_buyer', 'buyer_td_id' );
					$("#search_by_title").html("Buyer");
				}else if(search_with == 1){
					load_drop_down( 'knit_finish_fabric_receive_by_garments_controller', cbo_company_name, 'load_drop_down_supplier', 'buyer_td_id' );
					$("#search_by_title").html("Supplier");
				}
			}

		</script>

	</head>

	<body>
		<div align="center" style="width:740px;">
			<form name="searchexportinformationfrm"  id="searchexportinformationfrm">
				<fieldset style="width:720px;">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" width="700" class="rpt_table" border="1" rules="all">
						<thead>
							<th>Company</th>
							<th>Search By</th>
							<th id="search_by_title">Supplier</th>
							<th>Enter</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
								<input type="hidden" name="hidden_lcSc_id" id="hidden_lcSc_id" value="" />
								<input type="hidden" name="is_lcSc" id="is_lcSc" value="" />
								<input type="hidden" name="company_id" id="company_id" value="" />
								<input type="hidden" name="import_btb" id="import_btb" value="" />
								<input type="hidden" name="lc_sc" id="lc_sc" value="" />
							</th>
						</thead>
						<tr class="general">
							<td>
								<?
								echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--- Select Company ---", $companyID, "fnc_load_buyer_supplier(document.getElementById('cbo_search_by').value)" );
								?>
							</td>
							<td>
								<?
								$arr=array(1=>'LC NO',2=>'SC No');
								echo create_drop_down( "cbo_search_by", 100, $arr,"", 0, "", 0, "fnc_load_buyer_supplier(this.value)" );
								?>
							</td>
							<td id="buyer_td_id">
								<?
								echo create_drop_down("cbo_buyer_name", 162, "select a.supplier_name, a.id from lib_supplier a, lib_supplier_tag_company b where a.id = b.supplier_id  and a.status_active =1 and b.tag_company = $companyID group by a.supplier_name, a.id", "id,supplier_name", 1, "-- Select Supplier --", $selected, "");
								?>
							</td>
							<td>
								<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
							</td>
							<td>
								<input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'lcSc_search_list_view', 'search_div', 'knit_finish_fabric_receive_by_garments_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
							</td>
						</tr>
					</table>
					<div style="width:100%; margin-top:10px" id="search_div" align="left"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="lcSc_search_list_view")
{
	$data=explode('**',$data);

	$company_id=$data[0];
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$data[1]";
	}

	//if($data[1]==0) $buyer_id="%%"; else $buyer_id=$data[1];
	$search_by=$data[2];
	if($search_by==1){
		if($data[3]!='') $search_text="and a.lc_number like '%".trim($data[3])."%'"; else $search_text=" ";
	}
	else{
		if($data[3]!='') $search_text="and contract_no like '%".trim($data[3])."%'"; else $search_text=" ";
	}


	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

	if($search_by==1)
	{
		if($company_id !=0 ) $company_cond ="and a.importer_id=$company_id"; else $company_cond =" ";
		if($db_type==0) $year_field="YEAR(a.insert_date) as year,";
		else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";

		$sql ="select a.id,$year_field a.lc_number, a.btb_system_id, a.issuing_bank_id, a.supplier_id, a.importer_id, 1 as type
		from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details d
		where a.id = b.com_btb_lc_master_details_id and b.pi_id = d.pi_id and d.item_category_id = 2 $company_cond $search_text
		group by a.id,a.lc_number, a.btb_system_id, a.issuing_bank_id, a.supplier_id, a.importer_id,a.insert_date
		order by a.id";

		$arr=array (3=>$comp,4=>$supplier_arr,5=>$bank_arr);

		echo  create_list_view("list_view", "Year,System ID,LC No,Importer,Supplier,Issuing Bank", "55,110,110,80,80,110","700","280",0, $sql, "js_set_value", "id,type,importer_id,import_btb,lc_number", "", 1, "0,0,0,importer_id,supplier_id,issuing_bank_id", $arr , "year,btb_system_id,lc_number,importer_id,supplier_id,issuing_bank_id", "",'','0,0,0,0,0,0');
	}
	else
	{
		if($company_id !=0 ) $company_cond ="and beneficiary_name=$company_id"; else $company_cond =" ";
		if($db_type==0) $year_field="YEAR(insert_date) as year,";
		else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year,";
		else $year_field="";//defined Later

		$sql = "select id, beneficiary_name, $year_field contact_prefix_number as system_num, contact_system_id as system_id, contract_no as lc_sc, internal_file_no, beneficiary_name, buyer_name, lien_bank,2 as type, 0 as import_btb from com_sales_contract where export_item_category in(1,10) and status_active=1 and is_deleted=0 $company_cond $search_text $buyer_id_cond order by id";
		$id_buyer = "buyer_name";
		$lc_sc="SC No";
		$arr=array (4=>$comp,5=>$buyer_arr,6=>$bank_arr);

		echo  create_list_view("list_view", "Year,System ID,File No,$lc_sc,Benificiary,Buyer,Lien Bank", "55,60,90,110,80,80,110","700","280",0, $sql, "js_set_value", "id,type,beneficiary_name,import_btb,lc_sc", "", 1, "0,0,0,0,beneficiary_name,$id_buyer,lien_bank", $arr , "year,system_num,internal_file_no,lc_sc,beneficiary_name,$id_buyer,lien_bank", "",'','0,0,0,0,0,0,0');
	}

	exit();
}



if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_dyeing_location", 162, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 $com_location_credential_cond order by location_name","id,location_name", 1, "-- Select Location --", 0, "" );
	exit();
}

if ($action=="load_drop_down_location_lc")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location", 162, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 $com_location_credential_cond order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_drop_down('requires/knit_finish_fabric_receive_by_garments_controller', this.value+'_'+$data[0], 'load_drop_down_store','store_td');" );
	exit();
}

if ($action=="load_drop_down_pre_body_part")
{
	//$data=explode("_",$data);
	echo create_drop_down( "cbo_body_part", 182, $body_part,"", 1, "-- Select Body Part --", 0, "","",$data );
	exit();
}
if ($action=="load_room_rack_self_bin")
{
	$explodeData = explode('*', $data);
	$customFnc = array( 'store_update_upto_disable()' );
	array_splice( $explodeData, 11, 0, $customFnc );
	$data=implode('*', $explodeData);
	//echo $data;
	load_room_rack_self_bin("requires/knit_finish_fabric_receive_by_garments_controller",$data);
}
if ($action=="load_drop_down_location_dyeing")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_dyeing_location", 162, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "" );
	exit();
}
if ($action=="load_drop_down_store")
{
	$data=explode("_",$data);
	if ($data[0] != "" && $data[0] > 0) {$location_cond = "and a.location_id='$data[0]'";} else { $location_cond = "";}
	echo create_drop_down( "cbo_store_name", 162, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[1]' and b.category_type=2 and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond $location_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1,"--Select store--",0,"load_room_rack_self_bin('requires/knit_finish_fabric_receive_by_garments_controller','floor','floor_td', $('#cbo_company_id').val(), $('#cbo_location').val(), this.value);store_update_upto_disable();");
	exit();
}

if($action=="load_drop_down_dyeing_com")
{
	$data = explode("_",$data);
	$company_id=$data[1];

	if($data[0]==1)
	{
		echo create_drop_down( "cbo_dyeing_company", 162, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond $company_credential_cond order by comp.company_name","id,company_name",1, "--Select Dyeing Company--", "$company_id", "load_location();","" );
	}
	else if($data[0]==3)
	{
		echo create_drop_down( "cbo_dyeing_company", 162, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id  and a.status_active=1 and b.party_type in(9,21) group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select Dyeing Company--", 1, "load_location();" );
		//and b.party_type in (9,21,2)
	}
	else
	{
		echo create_drop_down( "cbo_dyeing_company", 162, $blank_array,"",1, "--Select Dyeing Company--", 1, "load_location();" );
	}
	exit();
}


if($action=="wo_pi_production_popup")
{
	echo load_html_head_contents("WO/PI/Production Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(id,no,type,buyer_id,data,knit_company,multi_job,currency_id,pi_basis)
		{
			/*if($('#cbo_receive_basis').val()==2 && type==0 && multi_job>1)
			{
				alert("Multiple Job Mixed Not Allowed.");
				return;
			}*/
			$('#hidden_wo_pi_production_id').val(id);
			$('#hidden_wo_pi_production_no').val(no);
			$('#booking_without_order').val(type);
			$('#hidden_buyer_id').val(buyer_id);
			$('#hidden_production_data').val(data);
			$('#hidden_knitting_company').val(knit_company);
			$('#hidden_currency_id').val(currency_id);
			$('#hidden_pi_basis_id').val(pi_basis);
			parent.emailwindow.hide();
		}
	</script>

</head>

<body>
	<div align="center" style="width:1350px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:1340px; margin-left:3px">
				<legend>Enter search words</legend>
				<? if($receive_basis==9)
				{
					$orderDisplay ="";
					$fileNoDisplay ="";
					$refNoDisplay ="";
					$batchNoDisplay = "";
					if($receive_basis == 9)
					{
						$prodDateDisplay = "";
					}
					else
					{
						$prodDateDisplay="style='display:none'";
					}
				}
				else if($receive_basis==2 || $receive_basis==14)
				{
					$orderDisplay="";
					$fileNoDisplay="style='display:none'";
					$prodDateDisplay="style='display:none'";
					if($receive_basis==14)
					{
						$refNoDisplay="style='display:none'";
					}
					else
					{
						$refNoDisplay="";
					}
					$batchNoDisplay="style='display:none'";
				}
				else
				{
					$orderDisplay="style='display:none'";
					$fileNoDisplay="style='display:none'";
					$refNoDisplay="style='display:none'";
					$batchNoDisplay="style='display:none'";
					$prodDateDisplay="style='display:none'";
				}

				//if($receive_basis==2) $dispaly2=""; else $dispaly2="style='display:none'";
				?>
				<table cellpadding="0" cellspacing="0" border="1" rules="all" width="1060" class="rpt_table">
					<thead>
						<tr>
             		   		<th colspan="11" align="center"><? echo create_drop_down( "cbo_search_category", 150, $string_search_type,'', 1, "-- Searching Type --",4 ); ?></th>
          			  	</tr>
          			  	<tr>
          			  		<th>Search By</th>
							<th>Enter WO/PI/Pro. Recv. No/Sale Booking</th>
							<th>Style Ref. No</th>
							<th <? echo $orderDisplay; ?>> Order No</th>
							<th <? echo $fileNoDisplay; ?>>File No</th>
							<th <? echo $refNoDisplay; ?>>Ref. No</th>
							<th <? echo $batchNoDisplay; ?>>Batch No</th>
							<th <? echo $prodDateDisplay; ?> >Production Date</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton" />
								<input type="hidden" name="txt_company_id" id="txt_company_id" value="<? echo $cbo_company_id; ?>">
								<input type="hidden" name="hidden_wo_pi_production_id" id="hidden_wo_pi_production_id" value="">
								<input type="hidden" name="hidden_wo_pi_production_no" id="hidden_wo_pi_production_no" value="">
								<input type="hidden" name="booking_without_order" id="booking_without_order" value="">
								<input type="hidden" name="hidden_buyer_id" id="hidden_buyer_id" value="">
								<input type="hidden" name="hidden_production_data" id="hidden_production_data" value="">
								<input type="hidden" name="hidden_knitting_company" id="hidden_knitting_company" value="">
								<input type="hidden" name="hidden_currency_id" id="hidden_currency_id" value="">
								<input type="hidden" name="hidden_pi_basis_id" id="hidden_pi_basis_id" value="">
							</th>
          			  	</tr>
					</thead>
					<tr>
						<td align="center">
							<? echo create_drop_down("cbo_receive_basis",152,$receive_basis_arr,"",1,"-- Select --",$receive_basis,"","1","1,2,4,6,9,11,14,16"); ?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td align="center">
							<input type="text" style="width:130px" class="text_boxes"  name="txt_style_no" id="txt_style_no" />
						</td>

						<td align="center" <? echo $orderDisplay; ?> >
							<input type="text" style="width:70px" class="text_boxes"  name="txt_order_no" id="txt_order_no" />
						</td>

						<td align="center" <? echo $fileNoDisplay; ?>>
							<input type="text" style="width:70px" class="text_boxes"  name="txt_file_no" id="txt_file_no" />
						</td>
						<td align="center" <? echo $refNoDisplay; ?>>
							<input type="text" style="width:75px" class="text_boxes"  name="txt_ref_no" id="txt_ref_no" />
						</td>
						<td align="center" <? echo $batchNoDisplay; ?>>
							<input type="text" style="width:95px" class="text_boxes"  name="txt_search_batch" id="txt_search_batch" />
						</td>
						<td valign="middle" align="center" <? echo $prodDateDisplay; ?>>
                             <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:70px" placeholder="From Date"/>
                             To
                             <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:70px" placeholder="To Date"/>
                        </td>

						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_receive_basis').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('txt_search_batch').value+'_'+document.getElementById('txt_order_no').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('txt_style_no').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $process_costing_maintain; ?>'+'_'+'<? echo $var_allow_fin_rcv_NR_hdn; ?>', 'create_wo_pi_production_search_list_view', 'search_div', 'knit_finish_fabric_receive_by_garments_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:70px;" />
						</td>
					</tr>
				</table>
				<div style="margin-top:10px;" id="search_div" align="center"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action == "create_wo_pi_production_search_list_view")
{
	$data = explode("_",$data);

	$search_string = "%".trim($data[0])."%";
	$receive_basis = $data[1];
	$company_id = $data[2];
	$batch_search =trim($data[3]);
	$order_no = trim($data[4]);
	$file_no = trim($data[5]);
	$ref_no = trim($data[6]);
	$style_no = trim($data[7]);
	$searching_type = trim($data[8]);
	$from_date = $data[9];
	$to_date = $data[10];
	$process_costing_maintain = trim($data[11]);
	$var_allow_fin_rcv_NR_hdn = trim($data[12]);

	//var_dump($to_date);
	if($db_type==0){
		$date_from=change_date_format($from_date,'yyyy-mm-dd');
		$date_to=change_date_format($to_date,'yyyy-mm-dd');
	}
	else if($db_type==2){
		$date_from=change_date_format($from_date,'','',1);
		$date_to=change_date_format($to_date,'','',1);
	}

	if($date_from!="" && $date_to!="")
	{
		$dateCond = "and a.receive_date between '$date_from' and '$date_to'";
	}

	$company_arr = return_library_array("select id, company_name from lib_company","id","company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	if($receive_basis==1)
	{
		if(trim($data[0])!="")
		{
			$search_field_cond="and a.pi_number like '$search_string'";
		}
		else
		{
			$search_field_cond="";
		}

		$approval_status_cond="";
		if($db_type==0)
		{
			$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'),'yyyy-mm-dd')."' and company_id='$company_id')) and page_id=18 and status_active=1 and is_deleted=0";
		}
		else
		{
			$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'), "", "",1)."' and company_id='$company_id')) and page_id=18 and status_active=1 and is_deleted=0";
		}
		$approval_status=sql_select($approval_status);
		if($approval_status[0][csf('approval_need')]==1)
		{
			$approval_status_cond= "and a.approved = 1";
		}

		$sql = "select a.id, a.pi_number, a.supplier_id, a.pi_date, a.last_shipment_date, a.pi_basis_id, a.internal_file_no, a.currency_id, a.source,b.booking_without_order from com_pi_master_details a,com_pi_item_details b where a.id=b.pi_id and a.item_category_id=2 and a.goods_rcv_status=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.importer_id=$company_id $search_field_cond $approval_status_cond group by a.id, a.pi_number, a.supplier_id, a.pi_date, a.last_shipment_date, a.pi_basis_id, a.internal_file_no, a.currency_id, a.source,b.booking_without_order order by a.id desc";

		$result = sql_select($sql);
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="125">PI No</th>
				<th width="80">PI Date</th>
				<th width="110">PI Basis</th>
				<th width="160">Supplier</th>
				<th width="100">Last Shipment Date</th>
				<th width="100">Internal File No</th>
				<th width="80">Currency</th>
				<th>Source</th>
			</thead>
		</table>
		<div style="width:928px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table" id="tbl_list_search">
				<?
				$i=1;
				foreach ($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('pi_number')]; ?>','<? echo $row[csf('booking_without_order')]; ?>','0','','<? echo $row[csf('supplier_id')]; ?>','','<? echo $row[csf('currency_id')]; ?>','<? echo $row[csf('pi_basis_id')];?>');">
						<td width="30"><? echo $i; ?></td>
						<td width="125"><p><? echo $row[csf('pi_number')]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('pi_date')]); ?></td>
						<td width="110"><p><? echo $pi_basis[$row[csf('pi_basis_id')]]; ?>&nbsp;</p></td>
						<td width="160"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
						<td width="100" align="center"><? echo change_date_format($row[csf('last_shipment_date')]); ?>&nbsp;</td>
						<td width="100"><p><? echo $row[csf('internal_file_no')]; ?>&nbsp;</p></td>
						<td width="80"><p><? echo $currency[$row[csf('currency_id')]]; ?>&nbsp;</p></td>
						<td><p><? echo $source[$row[csf('source')]]; ?>&nbsp;</p></td>
					</tr>
					<?
					$i++;
				}
				?>
			</table>
		</div>
		<?
	}
	else if($receive_basis==2)
	{
		if($file_no!="") $file_cond="and b.file_no=$file_no"; else $file_cond="";
		if($ref_no!="") $ref_cond="and b.grouping='$ref_no'"; else $ref_cond="";
		if($order_no!="") $order_cond="and b.po_number='$order_no'"; else $order_cond="";

		$po_arr=array();
		$po_data=sql_select("select b.id, b.po_number,b.file_no,b.grouping as ref, b.pub_shipment_date, b.po_quantity, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id");
		foreach($po_data as $row)
		{
			$po_arr[$row[csf('id')]]=$row[csf('po_number')]."**".$row[csf('pub_shipment_date')]."**".$row[csf('po_quantity')]."**".$row[csf('po_qnty_in_pcs')]."**".$row[csf('file_no')]."**".$row[csf('ref')];
		}

		if(trim($data[0])!="")
		{
			if($searching_type==1){
				if (str_replace("'","",$data[0])!="") $search_field_cond=" and a.booking_no_prefix_num='$data[0]'"; else  $search_field_cond="";
				if (str_replace("'","",$data[0])!="") $search_field_cond_sample=" and s.booking_no_prefix_num='$data[0]'    "; else  $search_field_cond_sample="";
				//if (str_replace("'","",$data[4])!="") $job_cond=" and b.job_no_prefix_num='$data[4]'  "; else  $job_cond="";
			}
			else if($searching_type==2){
				if (str_replace("'","",$data[0])!="") $search_field_cond=" and a.booking_no_prefix_num like '$data[0]%'"; else  $search_field_cond="";
				if (str_replace("'","",$data[0])!="") $search_field_cond_sample=" and s.booking_no_prefix_num like '$data[0]%'  "; else  $search_field_cond_sample="";
			}
			else if($searching_type==3){
				if (str_replace("'","",$data[0])!="") $search_field_cond=" and a.booking_no_prefix_num like '%$data[0]'"; else  $search_field_cond="";
				if (str_replace("'","",$data[0])!="") $search_field_cond_sample=" and s.booking_no_prefix_num like '%$data[0]'  "; else  $search_field_cond_sample="";

			}
			else if($searching_type==4 || $searching_type==0){
				if (str_replace("'","",$data[0])!="") $search_field_cond=" and a.booking_no_prefix_num like '%$data[0]%'"; else  $search_field_cond="";
				if (str_replace("'","",$data[0])!="") $search_field_cond_sample=" and s.booking_no_prefix_num like '%$data[0]%'"; else  $search_field_cond_sample="";

			}

			//$search_field_cond="and a.booking_no like '$search_string'";
			//$search_field_cond_sample="and s.booking_no like '$search_string'";
		}
		else
		{
			$search_field_cond="";
		}

		$approval_status_cond="";$approval_status_cond2="";
		if($db_type==0)
		{
			$approval_status_SQL="select approval_need,page_id,allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'),'yyyy-mm-dd')."' and company_id='$company_id')) and page_id in (5,8) and status_active=1 and is_deleted=0";
		}
		else
		{
			$approval_status_SQL="select approval_need,page_id,allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'), "", "",1)."' and company_id='$company_id')) and page_id in (5,8) and status_active=1 and is_deleted=0";
		}

		$approval_status_result=sql_select($approval_status_SQL);
		foreach($approval_status_result as $approval_val)
		{
			if($approval_val[csf('page_id')]==5 && $approval_val[csf('approval_need')]==1 && $approval_val[csf('allow_partial')]==1)
			{
				$approval_status_cond= " and a.is_approved in(1,3)";
			}
			else if($approval_val[csf('page_id')]==5 && $approval_val[csf('approval_need')]==1 && $approval_val[csf('allow_partial')]!=1)
			{
				$approval_status_cond= " and a.is_approved in(1)";
			}


			if($approval_val[csf('page_id')]==8 && $approval_val[csf('approval_need')]==1 && $approval_val[csf('allow_partial')]==1)
			{
				$approval_status_cond= " and a.is_approved in(1,3)";
			}
			else if($approval_val[csf('page_id')]==8 && $approval_val[csf('approval_need')]==1 && $approval_val[csf('allow_partial')]!=1)
			{
				$approval_status_cond= " and a.is_approved in(1)";
			}
		}

		if($receive_basis==2 && $process_costing_maintain==1 && $var_allow_fin_rcv_NR_hdn==1)
		{
			$fabric_source_cond="and a.fabric_source in (1)";
			$payment_cond="and a.pay_mode in(3,5)";

			$samp_fabric_source_cond=" and s.fabric_source in (1)";
			$samp_payment_cond=" and s.pay_mode in(3,5)";
		}
		else
		{
			$fabric_source_cond="and a.fabric_source in (2,3)";
			$payment_cond="and a.pay_mode=1";

			$samp_fabric_source_cond=" and s.fabric_source in (2,3)";
			$samp_payment_cond=" and s.pay_mode=1";
		}

		$sql = "SELECT a.id,a.supplier_id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.currency_id, a.buyer_id, a.fabric_source, b.id po_break_down_id, a.item_category, a.delivery_date, c.job_no as job_no_mst, 0 as type,b.po_number
		from wo_booking_mst a,wo_booking_dtls c, wo_po_break_down b where a.booking_no=c.booking_no and c.job_no=b.job_no_mst and a.company_id=$company_id and a.item_category=2 $payment_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $fabric_source_cond $search_field_cond $order_cond $file_cond $ref_cond $approval_status_cond
		group by a.id,a.supplier_id, a.booking_no_prefix_num, a.booking_no, a.booking_date,a.currency_id, a.buyer_id, a.fabric_source, b.id, a.item_category, a.delivery_date, c.job_no,b.po_number
		union all
		SELECT s.id,s.supplier_id, s.booking_no_prefix_num, s.booking_no, s.booking_date,s.currency_id, s.buyer_id, s.fabric_source, null as po_break_down_id, s.item_category, s.delivery_date, null as job_no_mst, 1 as type, null as po_number FROM wo_non_ord_samp_booking_mst s WHERE s.company_id=$company_id and s.status_active =1 and s.is_deleted=0 $samp_payment_cond $samp_fabric_source_cond and s.item_category=2 $search_field_cond_sample $approval_status_cond2 order by type,id desc";
		// echo $sql;
		$booking_job_arr=array();
		$result = sql_select($sql);

		// for checking Reference Closing

		$sql_2 = "SELECT DISTINCT INV_PUR_REQ_MST_ID, CLOSING_STATUS FROM INV_REFERENCE_CLOSING WHERE     REFERENCE_TYPE = 108 AND status_active = 1 AND is_deleted = 0 AND insert_date IN (  SELECT MAX (insert_date) FROM INV_REFERENCE_CLOSING WHERE     REFERENCE_TYPE = 108 AND status_active = 1 AND is_deleted = 0 GROUP BY INV_PUR_REQ_MST_ID)";
		// echo $sql_2;
			
		$result_2 = sql_select($sql_2);

		foreach($result as $key=> $row)
		{
			foreach($result_2 as $val)
			{
				if(($row['ID'] == $val['INV_PUR_REQ_MST_ID']) && ($val['CLOSING_STATUS']==1)) {
					// echo $row['ID']."  ";
					unset($result[$key]);
				}
			}
		}



		foreach($result as $val)
		{
			if($val[csf('type')]==0)
			{
				$booking_job_arr[$val[csf('booking_no')]][$val[csf('job_no_mst')]]=$val[csf('job_no_mst')];
			}
		}
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1050" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="115">Booking No</th>
				<th width="80">Booking Date</th>
				<th width="100">Buyer</th>
				<th width="85">Item Category</th>
				<th width="80">Delivary date</th>
				<th width="90">Job No</th>
				<th width="90">Order Qnty</th>
				<th width="80">Shipment Date</th>
				<th width="160">Order No</th>
				<th width="70">File No</th>
				<th width="">Ref No</th>
			</thead>
		</table>
		<div style="width:1068px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1050" class="rpt_table" id="tbl_list_search">
				<?
				$i=1;
				foreach ($result as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					$po_qnty_in_pcs=''; $po_no=''; $file_nos=''; $ref_nos=''; $min_shipment_date='';

					if($row[csf('po_break_down_id')]!="" && $row[csf('type')]==0)
					{
						$po_id=explode(",",$row[csf('po_break_down_id')]);
						foreach ($po_id as $id)
						{
							$po_data=explode("**",$po_arr[$id]);
							$po_number=$po_data[0];
							$pub_shipment_date=$po_data[1];
							$po_qnty=$po_data[2];
							$poQntyPcs=$po_data[3];
							$files_no=$po_data[4];
							$refs_no=$po_data[5];

							if($po_no=="") $po_no=$po_number; else $po_no.=",".$po_number;
							if($file_nos=="") $file_nos=$files_no; else $file_nos.=",".$files_no;
							if($ref_nos=="") $ref_nos=$refs_no; else $ref_nos.=",".$refs_no;

							if($min_shipment_date=='')
							{
								$min_shipment_date=$pub_shipment_date;
							}
							else
							{
								if($pub_shipment_date<$min_shipment_date) $min_shipment_date=$pub_shipment_date; else $min_shipment_date=$min_shipment_date;
							}

							$po_qnty_in_pcs+=$poQntyPcs;
						}
					}

					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('booking_no')]; ?>','<? echo $row[csf('type')]; ?>','0','','<?php echo $row[csf('supplier_id')]; ?>','<?php echo count($booking_job_arr[$row[csf('booking_no')]]); ?>','<?php echo $row[csf('currency_id')]; ?>','');">
						<td width="30"><? echo $i; ?></td>
						<td width="115"><p><? echo $row[csf('booking_no')]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
						<td width="100"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
						<td width="85"><p><? echo $item_category[$row[csf('item_category')]]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</td>
						<td width="90"><p><? echo $row[csf('job_no_mst')]; ?>&nbsp;</p></td>
						<td width="90" align="right"><? echo $po_qnty_in_pcs; ?>&nbsp;</td>
						<td width="80" align="center"><? echo change_date_format($min_shipment_date); ?>&nbsp;</td>
						<td width="160"><p><? echo $po_no; ?>&nbsp;</p></td>
						<td width="70" align="center"><? echo implode(",",array_unique(explode(",",$file_nos))); ?>&nbsp;</td>
						<td width="" align="center"><? echo implode(",",array_unique(explode(",",$ref_nos))); ?>&nbsp;</td>
					</tr>
					<?
					$i++;
				}
				?>
			</table>
		</div>
		<?
	}
	else if($receive_basis==11)
	{
		$color_arr = return_library_array("select id, color_name from lib_color where status_active=1","id","color_name");
		$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
		$buyer_short_arr = return_library_array("select id, short_name from lib_buyer","id","short_name");
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		if($file_no!="") $file_cond="and b.file_no=$file_no"; else $file_cond="";
		if($ref_no!="") $ref_cond="and b.grouping='$ref_no'"; else $ref_cond="";
		if($order_no!="") $order_cond="and b.po_number='$order_no'"; else $order_cond="";

		$po_arr=array();
		$po_data=sql_select("select b.id, b.po_number, b.pub_shipment_date, b.po_quantity, b.grouping, b.file_no, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id");
		foreach($po_data as $row)
		{
			$po_arr[$row[csf('id')]]=$row[csf('po_number')]."**".$row[csf('pub_shipment_date')]."**".$row[csf('po_quantity')]."**".$row[csf('po_qnty_in_pcs')]."**".$row[csf('grouping')]."**".$row[csf('file_no')];
		}

		$search_field_cond="";

		if(trim($data[0])!="")
		{
			$search_field_cond .=" and a.booking_no like '$search_string'";
			$search_field_cond_booking .=" and s.booking_no like '$search_string'";
		}

		if(trim($style_no)!="")
		{
			$search_field_cond.=" and e.style_ref_no like '$style_no'";
			$union_cond="";
		}
		else
		{
			if($db_type==0)
			{
				$union_cond="union all
				select s.id, s.prefix_num as booking_no_prefix_num, s.booking_no, s.booking_date, s.currency_id, s.buyer_id, s.supplier_id, '' as po_break_down_id, s.item_category, '' as delivery_date, '' as job_no_mst, 1 as type,'' as style,0 as color,d.process_id from wo_non_ord_knitdye_booking_mst s,wo_non_ord_knitdye_booking_dtl d where s.id=d.mst_id and d.process_id not in(1) and d.status_active=1 and d.is_deleted=0 and s.company_id=$company_id and s.status_active=1 and s.is_deleted=0  $search_field_cond_sample $search_field_cond_booking
				order by type, id desc";
			}
			else
			{
				$union_cond ="union all
				select s.id, s.prefix_num as booking_no_prefix_num, s.booking_no, s.booking_date, s.currency_id, s.buyer_id, s.supplier_id, null as po_break_down_id, s.item_category, null as delivery_date, null as job_no_mst, 1 as type,null as style,0 as color,d.process_id from wo_non_ord_knitdye_booking_mst s,wo_non_ord_knitdye_booking_dtl d where s.id=d.mst_id and d.process_id not in(1) and d.status_active=1 and d.is_deleted=0 and s.company_id=$company_id and s.status_active=1 and s.is_deleted=0  $search_field_cond_sample $search_field_cond_booking
				order by type, id desc";
			}
		}

		//echo $search_field_cond;die;
		//and c.process in (31,25,26,32,33,34,35,36,37,38,39,40,60,61,62,64,67,68,69,70,71,72,73,74,75,77,78,79,80,81,82,83,84,85,86,87,88,89,92,93,94,100,125,127,128,129,132,133,134,135,136,137,138,63,31,30,65,66,76,90,91,140)         AS PER Anamul

		if($db_type==0)
		{
			$sql = "SELECT a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date,a.currency_id, a.buyer_id, a.supplier_id, b.id po_break_down_id, a.item_category, a.delivery_date, c.job_no as job_no_mst, 0 as type ,
			e.style_ref_no as style, c.gmts_color_id color,c.process as process_id
			from wo_booking_mst a, wo_booking_dtls c,wo_po_break_down b,wo_po_details_master e
			where a.company_id=$company_id and a.booking_type=3 and a.item_category=12 and a.status_active=1 and a.is_deleted=0
			and a.booking_no=c.booking_no and c.po_break_down_id=b.id and b.job_id=e.id
			and c.process not in (1,2,3,4) and b.status_active=1 and b.is_deleted=0 $search_field_cond $file_cond $ref_cond $order_cond
			group by a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date,a.currency_id, a.buyer_id, b.id, a.item_category, a.delivery_date, c.job_no, a.supplier_id ,e.style_ref_no,c.gmts_color_id,c.process $union_cond";
		}
		//echo $sql;die;
		else
		{
			$sql = "SELECT a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date,a.currency_id, a.buyer_id, a.supplier_id, b.id po_break_down_id, a.item_category, a.delivery_date, c.job_no as job_no_mst, 0 as type ,
			e.style_ref_no as style, c.gmts_color_id color,c.process as process_id
			from wo_booking_mst a, wo_booking_dtls c,wo_po_break_down b,wo_po_details_master e
			where a.company_id=$company_id and a.booking_type=3 and a.item_category=12 and a.status_active=1 and a.is_deleted=0
			and a.booking_no=c.booking_no and c.po_break_down_id=b.id and b.job_id=e.id
			and c.process not in (1,2,3,4) and b.status_active=1 and b.is_deleted=0 $search_field_cond $file_cond $ref_cond $order_cond
			group by a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date,a.currency_id, a.buyer_id, b.id, a.item_category, a.delivery_date, c.job_no, a.supplier_id ,e.style_ref_no,c.gmts_color_id,c.process $union_cond";
		}

		//echo $sql;
		$result = sql_select($sql);
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1440" class="rpt_table">
			<thead>
			<tr>
				<th width="40">SL</th>
				<th width="115">Booking No</th>
				<th width="85">Booking Date</th>
				<th width="90">Buyer</th>
				<th width="110">Item Category</th>
				<th width="110">Process Name</th>
				<th width="85">Delivary date</th>
				<th width="85">Style Name</th>
				<th width="85">Color Name</th>
				<th width="85">Party Name</th>
				<th width="100">Job No</th>
				<th width="80">Order Qnty</th>
				<th width="75">Shipment Date</th>
				<th width="100">Order No</th>
				<th width="70">File No</th>
				<th >Ref No</th>
			</tr>
		</thead>

		</table>
		<div style="width:1465px; max-height:280px; overflow-y:scroll;margin-left: 17px;" id="list_container_batch" >
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1440" class="rpt_table" id="tbl_list_search">
				<tbody>
				<?
				$i=1;
				foreach ($result as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					$po_qnty_in_pcs=''; $po_no=''; $min_shipment_date=''; $internal_ref=''; $file_no='';

					$po_id=explode(",",$row[csf('po_break_down_id')]);
					foreach ($po_id as $id)
					{
						$po_data=explode("**",$po_arr[$id]);
						$po_number=$po_data[0];
						$pub_shipment_date=$po_data[1];
						$po_qnty=$po_data[2];
						$poQntyPcs=$po_data[3];
						$internalRef=$po_data[4];
						$fileNo=$po_data[5];

						if($po_no=="") $po_no=$po_number; else $po_no.=",".$po_number;
						if($internal_ref=='') $internal_ref=$internalRef; else $internal_ref.=",".$internalRef;
						if($file_no=='') $file_no=$fileNo; else $file_no.=",".$fileNo;

						if($min_shipment_date=='')
						{
							$min_shipment_date=$pub_shipment_date;
						}
						else
						{
							if($pub_shipment_date<$min_shipment_date) $min_shipment_date=$pub_shipment_date; else $min_shipment_date=$min_shipment_date;
						}

						$po_qnty_in_pcs+=$poQntyPcs;

					}

					$internal_ref=implode(",",array_unique(explode(",",$internal_ref)));
					$file_no=implode(",",array_unique(explode(",",$file_no)));
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('booking_no')]; ?>',<? echo $row[csf('type')]; ?>,'0','<? echo $row[csf('job_no_mst')]; ?>','<? echo $row[csf('supplier_id')]; ?>','','<? echo $row[csf('currency_id')]; ?>','');">
						<td width="40"><? echo $i; ?></td>
						<td width="115"><p><? echo $row[csf('booking_no')]; ?></p></td>
						<td width="85" align="center"><? echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</td>
						<td width="90"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
						<?
						if($row[csf('type')]==0)
						{
							$category_name=$item_category[$row[csf('item_category')]];
						}
						else
						{
							$category_name=$conversion_cost_head_array[$row[csf('item_category')]];
						}
						?>
						<td width="110"><p><? echo $category_name; ?></p></td>
						<td width="110" ><? echo $conversion_cost_head_array[$row[csf('process_id')]]; ?></td>
						<td width="85" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</td>
						<td width="85"><p><? echo $row[csf('style')]; ?></p></td>
						<td width="85"><p><? echo $color_arr[$row[csf('color')]]; ?></p></td>
						<td width="85"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p></td>
						<td width="100"><p><? echo $row[csf('job_no_mst')]; ?>&nbsp;</p></td>
						<td width="80" align="right"><? echo $po_qnty_in_pcs; ?>&nbsp;</td>
						<td width="75" align="center"><? echo change_date_format($min_shipment_date); ?>&nbsp;</td>

						<td width="100"><p><? echo $po_no; ?>&nbsp;</p></td>
						<td width="70" align="right"><? echo $file_no; ?>&nbsp;</td>
						<td  align="right"><? echo $ref_no; ?>&nbsp;</td>
					</tr>


					<?
					$i++;
				}
				?>
			</tbody>
			</table>
		</div>
		<?
	}
	else if($receive_basis==9)
	{

		if(trim($data[0])!="")
		{
			$search_field_cond="and a.recv_number like '$search_string'";
		}
		else
		{
			$search_field_cond="";
		}
		if($file_no!="") $file_cond="and file_no=$file_no"; else $file_cond="";
		if($ref_no!="") $ref_cond="and grouping='$ref_no'"; else $ref_cond="";
		$store_arr = return_library_array("select id, store_name from lib_store_location","id","store_name");
		$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst","id","batch_no");

		$po_array=array();$all_po_id='';
		$po_sql=sql_select("select a.style_ref_no, b.id,b.file_no,b.grouping as ref, b.po_number from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and company_name='$company_id'");
		foreach($po_sql as $row)
		{
			$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['file']=$row[csf('file_no')];
			$po_array[$row[csf('id')]]['ref']=$row[csf('ref')];
		}

		$batch_cond=''; $batch_id="";
		if($batch_search!='')
		{
			$batchArr=return_library_array("SELECT id, batch_no from pro_batch_create_mst where batch_no like '%$batch_search%' and entry_form in(0,7) and status_active=1 and is_deleted=0","id","batch_no");
			foreach($batchArr as $key=>$val)
			{
				if($batch_id=="") $batch_id=$key; else $batch_id.=','.$key;
			}

			//if($batch_id!=""){ $batch_cond=" and b.batch_id in (".$batch_id.")";}

			$batch_id= chop($batch_id,",");
			$batch_id = implode(",",array_unique(explode(",",$batch_id)));
			$batch_id=explode(",",$batch_id);
			$batch_id=array_chunk($batch_id,999);
			if($batch_id!="")
			{
				$batch_cond=" and";
				foreach($batch_id as $batchId)
				{
					if($batch_cond==" and")  $batch_cond.="(b.batch_id in(".implode(',',$batchId).")"; else $batch_cond.=" or b.batch_id in(".implode(',',$batchId).")";
				}
				$batch_cond.=")";
			}
		}

		//echo $batch_cond;die;

		if($db_type==0)
		{
			if($order_no!='')
			{
				$order_id=return_field_value("group_concat(id) as po_id","wo_po_break_down","status_active=1 and is_deleted=0 and po_number like '".$order_no."%' $ref_cond $file_cond","po_id");
				if($order_id=="") $order_id=0;

				$sql = "select a.id, a.recv_number_prefix_num, a.recv_number, YEAR(a.insert_date) as year, a.store_id, a.knitting_source, a.knitting_company, a.knitting_location_id, a.receive_date, a.challan_no, group_concat(b.order_id) as order_id, group_concat(b.batch_id) as batch_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_id and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form=7 and c.po_breakdown_id in($order_id) $search_field_cond $batch_cond group by a.id, a.recv_number_prefix_num, a.recv_number, a.insert_date, a.store_id, a.knitting_source, a.knitting_company, a.knitting_location_id, a.receive_date, a.challan_no order by a.id desc";
			}
			else
			{
				$sql = "select a.id, a.recv_number_prefix_num, a.recv_number, YEAR(a.insert_date) as year, a.store_id, a.knitting_source, a.knitting_company, a.knitting_location_id, a.receive_date, a.challan_no, group_concat(b.order_id) as order_id, group_concat(b.batch_id) as batch_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $batch_cond group by a.id, a.recv_number_prefix_num, a.recv_number, a.insert_date, a.store_id, a.knitting_source, a.knitting_company, a.knitting_location_id, a.receive_date, a.challan_no order by a.id desc";
			}
		}
		else if($db_type==2)
		{
			if($order_no!='' || $file_no!='' || $ref_no!='')
			{
				$order_id=return_field_value("LISTAGG(cast(id as varchar2(4000)),',') WITHIN GROUP (ORDER BY id) as po_id","wo_po_break_down","status_active=1 and is_deleted=0 and po_number like '".$order_no."%' $ref_cond $file_cond","po_id");
				if($order_id=="") $order_id=0;

				$sql="SELECT a.id, a.recv_number_prefix_num, a.recv_number, to_char(a.insert_date,'YYYY') as year, a.store_id, a.knitting_source, a.knitting_company, a.knitting_location_id, a.receive_date, a.challan_no, LISTAGG(cast(b.order_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.order_id) as order_id, LISTAGG(cast(b.batch_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.batch_id) as batch_id
				from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c
				where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_id and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form=7 and c.po_breakdown_id in($order_id) $search_field_cond $batch_cond $dateCond
				group by a.id, a.recv_number_prefix_num, a.recv_number, a.insert_date, a.store_id, a.knitting_source, a.knitting_company, a.knitting_location_id, a.receive_date, a.challan_no
				order by a.id desc";
			}
			else
			{
				$sql="SELECT a.id, a.recv_number_prefix_num, a.recv_number, to_char(a.insert_date,'YYYY') as year, a.store_id, a.knitting_source, a.knitting_company, a.knitting_location_id, a.receive_date, a.challan_no, LISTAGG(cast(b.order_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.order_id) as order_id, LISTAGG(cast(b.batch_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.batch_id) as batch_id
				from inv_receive_master a, pro_finish_fabric_rcv_dtls b
				where a.id=b.mst_id and a.company_id=$company_id and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $batch_cond $dateCond
				group by a.id, a.recv_number_prefix_num, a.recv_number, a.insert_date, a.store_id, a.knitting_source, a.knitting_company, a.knitting_location_id, a.receive_date, a.challan_no
				order by a.id desc";
			}
			//echo $sql;
		}

		$result = sql_select($sql);
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1050" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="50">Prod. No</th>
				<th width="40">Year</th>
				<th width="70">Production Date</th>
				<th width="70">Challan No</th>
				<th width="90">Dyeing Source</th>
				<th width="110">Dyeing Company</th>
				<th width="110">Store</th>
				<th width="110">Style Ref.</th>
				<th width="110">Order No</th>
				<th width="70">File No</th>
				<th width="70">Ref. No</th>
				<th>Batch No</th>
			</thead>
		</table>
		<div style="width:1068px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1050" class="rpt_table" id="tbl_list_search">
				<?
				$i=1;
				foreach ($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					if($row[csf('knitting_source')]==1)	$knit_comp=$company_arr[$row[csf('knitting_company')]]; else $knit_comp=$supplier_arr[$row[csf('knitting_company')]];

					$order_id=array_unique(explode(",",$row[csf('order_id')]));
					$order_no=''; $style_ref='';$file_no='';$ref_no='';
					foreach($order_id as $value)
					{
						if($order_no=='') $order_no=$po_array[$value]['no']; else $order_no.=",".$po_array[$value]['no'];
						if($style_ref=='') $style_ref=$po_array[$value]['style']; else $style_ref.=",".$po_array[$value]['style'];
						if($file_no=='') $file_no=$po_array[$value]['file']; else $file_no.=",".$po_array[$value]['file'];
						if($ref_no=='') $ref_no=$po_array[$value]['ref']; else $ref_no.=",".$po_array[$value]['ref'];
					}
					if($order_no=='') $order_no="&nbsp;";

					$style_ref=implode(",",array_unique(explode(",",$style_ref)));
					if($style_ref=='') $style_ref="&nbsp;";

					$batch_id=array_unique(explode(",",$row[csf('batch_id')]));
					$batch_no="";
					foreach($batch_id as $val)
					{
						if($batch_no=='') $batch_no=$batch_arr[$val]; else $batch_no.=",".$batch_arr[$val];
					}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('recv_number')]; ?>','0','<? echo $row[csf('buyer_id')]; ?>','<?php echo $row[csf('knitting_source')]."**".$row[csf('knitting_company')]."**".$row[csf('knitting_location_id')]; ?>','');">
						<td width="30"><? echo $i; ?></td>
						<td width="50">&nbsp;&nbsp;<? echo $row[csf('recv_number_prefix_num')]; ?></td>
						<td width="40" align="center"><? echo $row[csf('year')]; ?></td>
						<td width="70" align="center"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
						<td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
						<td width="90"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
						<td width="110"><p><? echo $knit_comp; ?></p></td>
						<td width="110"><p><? echo $store_arr[$row[csf('store_id')]]; ?></p></td>
						<td width="110"><p><? echo $style_ref; ?></p></td>
						<td width="110"><p><? echo $order_no; ?></p></td>
						<td width="70"><p><? echo implode(",",array_unique(explode(",",$file_no))); ?></p></td>
						<td width="70"><p><? echo implode(",",array_unique(explode(",",$ref_no)));   ?></p></td>
						<td><p><? echo $batch_no; ?></p></td>
					</tr>
					<?
					$i++;
				}
				?>
			</table>
		</div>
		<?
	}
	else
	{
		//Delivery From Store
		if(trim($data[0])!="")
		{
			$search_field_cond="and a.recv_number like '$search_string'";
		}
		else
		{
			$search_field_cond="";
		}
		if($file_no!="") $file_cond="and file_no=$file_no"; else $file_cond="";
		if($ref_no!="") $ref_cond="and grouping='$ref_no'"; else $ref_cond="";
		$store_arr = return_library_array("select id, store_name from lib_store_location","id","store_name");
		$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst","id","batch_no");

		$po_array=array();$all_po_id='';
		$po_sql=sql_select("select a.style_ref_no, b.id,b.file_no,b.grouping as ref, b.po_number from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and company_name='$company_id'");
		foreach($po_sql as $row)
		{
			$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['file']=$row[csf('file_no')];
			$po_array[$row[csf('id')]]['ref']=$row[csf('ref')];
		}

		$batch_cond=''; $batch_id="";
		if($batch_search!='')
		{
			$batchArr=return_library_array("SELECT id, batch_no from pro_batch_create_mst where batch_no like '%$batch_search%' and entry_form in(0,7) and status_active=1 and is_deleted=0","id","batch_no");
			foreach($batchArr as $key=>$val)
			{
				if($batch_id=="") $batch_id=$key; else $batch_id.=','.$key;
			}

			//if($batch_id!=""){ $batch_cond=" and b.batch_id in (".$batch_id.")";}

			$batch_id= chop($batch_id,",");
			$batch_id = implode(",",array_unique(explode(",",$batch_id)));
			$batch_id=explode(",",$batch_id);
			$batch_id=array_chunk($batch_id,999);
			if($batch_id!="")
			{
				$batch_cond=" and";
				foreach($batch_id as $batchId)
				{
					if($batch_cond==" and")  $batch_cond.="(b.batch_id in(".implode(',',$batchId).")"; else $batch_cond.=" or b.batch_id in(".implode(',',$batchId).")";
				}
				$batch_cond.=")";
			}
		}

		//echo $batch_cond;die;

		if($db_type==0)
		{
			if($order_no!='')
			{
				$order_id=return_field_value("group_concat(id) as po_id","wo_po_break_down","status_active=1 and is_deleted=0 and po_number like '".$order_no."%' $ref_cond $file_cond","po_id");
				if($order_id=="") $order_id=0;

				$sql = "select a.id, a.recv_number_prefix_num, a.recv_number, YEAR(a.insert_date) as year, a.store_id, a.knitting_source, a.knitting_company, a.knitting_location_id, a.receive_date, a.challan_no, group_concat(b.order_id) as order_id, group_concat(b.batch_id) as batch_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_id and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form=7 and c.po_breakdown_id in($order_id) $search_field_cond $batch_cond group by a.id, a.recv_number_prefix_num, a.recv_number, a.insert_date, a.store_id, a.knitting_source, a.knitting_company, a.knitting_location_id, a.receive_date, a.challan_no order by a.id desc";
			}
			else
			{
				$sql = "select a.id, a.recv_number_prefix_num, a.recv_number, YEAR(a.insert_date) as year, a.store_id, a.knitting_source, a.knitting_company, a.knitting_location_id, a.receive_date, a.challan_no, group_concat(b.order_id) as order_id, group_concat(b.batch_id) as batch_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $batch_cond group by a.id, a.recv_number_prefix_num, a.recv_number, a.insert_date, a.store_id, a.knitting_source, a.knitting_company, a.knitting_location_id, a.receive_date, a.challan_no order by a.id desc";
			}
		}
		else if($db_type==2)
		{
			if($order_no!='' || $file_no!='' || $ref_no!='')
			{
				$order_id=return_field_value("LISTAGG(cast(id as varchar2(4000)),',') WITHIN GROUP (ORDER BY id) as po_id","wo_po_break_down","status_active=1 and is_deleted=0 and po_number like '".$order_no."%' $ref_cond $file_cond","po_id");
				if($order_id=="") $order_id=0;

				$sql="SELECT a.id, a.recv_number_prefix_num, a.recv_number, to_char(a.insert_date,'YYYY') as year, a.store_id, a.knitting_source, a.knitting_company, a.knitting_location_id, a.receive_date, a.challan_no, LISTAGG(cast(b.order_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.order_id) as order_id, LISTAGG(cast(b.batch_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.batch_id) as batch_id
				from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c
				where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_id and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form=7 and c.po_breakdown_id in($order_id) $search_field_cond $batch_cond
				group by a.id, a.recv_number_prefix_num, a.recv_number, a.insert_date, a.store_id, a.knitting_source, a.knitting_company, a.knitting_location_id, a.receive_date, a.challan_no
				order by a.id desc";
			}
			else
			{
				$sql="SELECT a.id, a.recv_number_prefix_num, a.recv_number, to_char(a.insert_date,'YYYY') as year, a.store_id, a.knitting_source, a.knitting_company, a.knitting_location_id, a.receive_date, a.challan_no, LISTAGG(cast(b.order_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.order_id) as order_id, LISTAGG(cast(b.batch_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.batch_id) as batch_id
				from inv_receive_master a, pro_finish_fabric_rcv_dtls b
				where a.id=b.mst_id and a.company_id=$company_id and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $batch_cond
				group by a.id, a.recv_number_prefix_num, a.recv_number, a.insert_date, a.store_id, a.knitting_source, a.knitting_company, a.knitting_location_id, a.receive_date, a.challan_no
				order by a.id desc";
			}
			//echo $sql;
		}

		$result = sql_select($sql);
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1050" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="50">Prod. No</th>
				<th width="40">Year</th>
				<th width="70">Production Date</th>
				<th width="70">Challan No</th>
				<th width="90">Dyeing Source</th>
				<th width="110">Dyeing Company</th>
				<th width="110">Store</th>
				<th width="110">Style Ref.</th>
				<th width="110">Order No</th>
				<th width="70">File No</th>
				<th width="70">Ref. No</th>
				<th>Batch No</th>
			</thead>
		</table>
		<div style="width:1068px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1050" class="rpt_table" id="tbl_list_search">
				<?
				$i=1;
				foreach ($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					if($row[csf('knitting_source')]==1)	$knit_comp=$company_arr[$row[csf('knitting_company')]]; else $knit_comp=$supplier_arr[$row[csf('knitting_company')]];

					$order_id=array_unique(explode(",",$row[csf('order_id')]));
					$order_no=''; $style_ref='';$file_no='';$ref_no='';
					foreach($order_id as $value)
					{
						if($order_no=='') $order_no=$po_array[$value]['no']; else $order_no.=",".$po_array[$value]['no'];
						if($style_ref=='') $style_ref=$po_array[$value]['style']; else $style_ref.=",".$po_array[$value]['style'];
						if($file_no=='') $file_no=$po_array[$value]['file']; else $file_no.=",".$po_array[$value]['file'];
						if($ref_no=='') $ref_no=$po_array[$value]['ref']; else $ref_no.=",".$po_array[$value]['ref'];
					}
					if($order_no=='') $order_no="&nbsp;";

					$style_ref=implode(",",array_unique(explode(",",$style_ref)));
					if($style_ref=='') $style_ref="&nbsp;";

					$batch_id=array_unique(explode(",",$row[csf('batch_id')]));
					$batch_no="";
					foreach($batch_id as $val)
					{
						if($batch_no=='') $batch_no=$batch_arr[$val]; else $batch_no.=",".$batch_arr[$val];
					}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('recv_number')]; ?>','0','<? echo $row[csf('buyer_id')]; ?>','<?php echo $row[csf('knitting_source')]."**".$row[csf('knitting_company')]."**".$row[csf('knitting_location_id')]; ?>','');">
						<td width="30"><? echo $i; ?></td>
						<td width="50">&nbsp;&nbsp;<? echo $row[csf('recv_number_prefix_num')]; ?></td>
						<td width="40" align="center"><? echo $row[csf('year')]; ?></td>
						<td width="70" align="center"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
						<td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
						<td width="90"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
						<td width="110"><p><? echo $knit_comp; ?></p></td>
						<td width="110"><p><? echo $store_arr[$row[csf('store_id')]]; ?></p></td>
						<td width="110"><p><? echo $style_ref; ?></p></td>
						<td width="110"><p><? echo $order_no; ?></p></td>
						<td width="70"><p><? echo implode(",",array_unique(explode(",",$file_no))); ?></p></td>
						<td width="70"><p><? echo implode(",",array_unique(explode(",",$ref_no)));   ?></p></td>
						<td><p><? echo $batch_no; ?></p></td>
					</tr>
					<?
					$i++;
				}
				?>
			</table>
		</div>
		<?
	}
	exit();
}


if($action=='populate_data_from_booking')
{
	$data=explode("**",$data);
	$booking_id=$data[0];
	$is_sample=$data[1];

	if($is_sample==0)
	{
		$sql="select id as booking_id, booking_no, booking_no as fabric_booking, buyer_id, job_no from wo_booking_mst where id='$booking_id'";
	}
	else
	{
		//$sql="select fab_booking_id as booking_id, booking_no, buyer_id, '' as job_no from wo_non_ord_knitdye_booking_mst where id='$booking_id'";

		//$sql="select a.fab_booking_id as booking_id, a.booking_no, a.buyer_id, '' as job_no, b.booking_no as fabric_booking from wo_non_ord_knitdye_booking_mst a, wo_non_ord_samp_booking_mst b where  a.fab_booking_id = b.id and a.id='$booking_id'";

		$sql="select a.id as booking_id, a.booking_no, a.buyer_id, '' as job_no, b.booking_no as fabric_booking from wo_non_ord_knitdye_booking_mst a, wo_non_ord_samp_booking_mst b where  a.fab_booking_id = b.id and a.id='$booking_id'";
	}


	$data_array=sql_select($sql);
	foreach ($data_array as $row)
	{
		echo "document.getElementById('txt_booking_no').value 				= '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('txt_booking_no_id').value 			= '".$row[csf("booking_id")]."';\n";
		echo "document.getElementById('txt_sales_booking_no').value 		= '".$row[csf("fabric_booking")]."';\n";
		echo "document.getElementById('booking_without_order').value 		= '".$is_sample."';\n";
		exit();
	}
}

if ($action=="po_search_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	?>
	<script>

		function fn_show_check()
		{
			var booking_type=$('#cbo_search_by').val();
			var txt_search_common=$('#txt_search_common').val();
			if (booking_type!=4 && txt_search_common=="")
			{
				if( form_validation('cbo_buyer_name','Buyer Name')==false )
				{
					return;
				}
			}
			else
			{
				if( form_validation('txt_search_common','Booking No')==false )
				{
					return;
				}
			}
			show_list_view ( $('#txt_search_common').val()+'_'+$('#cbo_search_by').val()+'_'+<? echo $cbo_company_id; ?>+'_'+$('#cbo_buyer_name').val()+'_'+'<? echo $hidden_order_id; ?>'+'_'+'<? echo $receive_basis; ?>', 'create_po_search_list_view', 'search_div', 'knit_finish_fabric_receive_by_garments_controller', 'setFilterGrid(\'tbl_list_search\',-1);hidden_field_reset();');
			set_all();
		}

		var selected_id = new Array(); var selected_name = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function set_all()
		{
			var recv_basis ='<? echo $receive_basis; ?>';
			var old=document.getElementById('txt_po_row_id').value;
			if(old!="")
			{
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{
					//recv_basis.val();
					if (recv_basis!=4 || recv_basis!=6)
					{
						js_set_value( old[k] );
					}
				}
			}
		}

		/*function js_set_value( str )
		{

			var recv_basis=$('#txt_receive_basis' + str).val();
			if (recv_basis==4 || recv_basis==6)
			{
				var id=$('#txt_individual_id' + str).val();
				var name=$('#txt_individual' + str).val();
				var hidden_bookingNo=$('#txt_booking' + str).val();

				$('#hidden_order_id').val(id);
				$('#hidden_order_no').val(name);
				$('#hidden_bookingNo').val(hidden_bookingNo);
				parent.emailwindow.hide();
			}
			else
			{
				toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

				if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
					selected_id.push( $('#txt_individual_id' + str).val() );
					selected_name.push( $('#txt_individual' + str).val() );

				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
				}

				var id = ''; var name = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
				}

				id = id.substr( 0, id.length - 1 );
				name = name.substr( 0, name.length - 1 );

				$('#hidden_order_id').val(id);
				$('#hidden_order_no').val(name);
			}
		}*/
		function js_set_value( str )
		{
			var recv_basis=$('#txt_receive_basis' + str).val();

			var any_selected = $('#hidden_order_id').val();
			if(any_selected=="")
			{
				booking_no_arr_chk = [];
			}

			var txt_booking_no = $('#txt_booking' + str).val();
			var txt_booking_id = $('#txt_booking_id' + str).val();
			if(booking_no_arr_chk.length==0)
			{
				booking_no_arr_chk.push( txt_booking_no );
			}
			else if( jQuery.inArray( txt_booking_no, booking_no_arr_chk )==-1 &&  booking_no_arr_chk.length>0)
			{
				alert("Booking Mixed is Not Allowed");
				return;
			}

			$('#hidden_bookingNo').val(txt_booking_no);
			$('#hidden_bookingId').val(txt_booking_id);

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );

			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}

			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hidden_order_id').val(id);
			$('#hidden_order_no').val(name);

		}

		function hidden_field_reset()
		{
			$('#hidden_order_id').val('');
			$('#hidden_order_no').val( '' );
			selected_id = new Array();
			selected_name = new Array();
		}

	</script>

</head>
<body>

	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:620px;margin-left:5px">
			<input type="hidden" name="hidden_order_id" id="hidden_order_id" class="text_boxes" value="">
			<input type="hidden" name="hidden_order_no" id="hidden_order_no" class="text_boxes" value="">
			<input type="hidden" name="hidden_bookingNo" id="hidden_bookingNo" class="text_boxes" value="">
			<input type="hidden" name="hidden_bookingId" id="hidden_bookingId" class="text_boxes" value="">
			<table cellpadding="0" cellspacing="0" width="620" class="rpt_table">
				<thead>
					<th>Buyer</th>
					<th>Search By</th>
					<th>Search</th>
					<th>
						<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
						<input type="hidden" name="po_id" id="po_id" value="">
					</th>
				</thead>
				<tr class="general">
					<td align="center">
						<?
						echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $buyer_name, "","" );
						?>
					</td>
					<td align="center">
						<?
						$search_by_arr=array(1=>"PO No",2=>"Job No",3=>"Internal Ref. No",4=>"Booking");

						echo create_drop_down( "cbo_search_by", 170, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
					</td>
					<td align="center">
						<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
					</td>
					<td align="center">
						<input type="button" name="button2" class="formbutton" value="Show" onClick="fn_show_check()" style="width:100px;" />
					</td>
				</tr>
			</table>
			<div id="search_div" style="margin-top:10px"></div>
		</fieldset>
	</form>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_po_search_list_view")
{
	$data = explode("_",$data);

	$search_string=trim($data[0]);
	$search_by=$data[1];

	$search_con="";
	if($search_by==1 && $search_string!="")
		$search_con = " and b.po_number like '%$search_string%'";
	else if($search_by==2 && $search_string!="")
		$search_con =" and a.job_no like '%$search_string%'";
	else if($search_by==3 && $search_string!="")
		$search_con =" and b.grouping like '%$search_string%'";

	if($search_by==4 && $search_string!=""){
		$search_booking_con =" and a.booking_no like '%$search_string%'";
		$search_booking_con2 =" and d.booking_no_prefix_num='$search_string'";
	}



	$company_id =$data[2];
	$buyer_id =$data[3];
	$all_po_id=$data[4];
	$receiveBasis=$data[5];
	$hidden_po_id=explode(",",$all_po_id);


	if($buyer_id==0 && $search_string=="") { echo "<b>Please Select Buyer First</b>"; die; }
	$buyer_condition = ($buyer_id>0)?" and a.buyer_name=$buyer_id":"";
	$buyer_condition2 = ($buyer_id>0)?" and a.buyer_id=$buyer_id":"";


	//if($buyer_id==0 && $search_by!=4) { echo "<b>Please Select Buyer First</b>"; die; }
	/*$sql_booking_no = sql_select("select c.id,c.po_number,a.booking_no from wo_booking_mst a,wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no and a.job_no=c.job_no_mst and a.company_id=$company_id $buyer_condition2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by c.id,c.po_number,a.booking_no");

	foreach ($sql_booking_no as $row) {
		$booking_no_arr[$row[csf('id')]]["booking_no"]=$row[csf('booking_no')];
	}*/
	if($search_by==4)
	{

		$sql_booking = sql_select("select c.id from wo_booking_mst a,wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.company_id=$company_id $buyer_condition2 $search_booking_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active!=0 and c.is_deleted!=1");

		foreach ($sql_booking as $row) {
			$po_ids.=$row[csf('id')].",";
		}
		$po_ids= chop($po_ids,",");
		$po_ids = implode(",",array_unique(explode(",",$po_ids)));
		$po_ids=explode(",",$po_ids);
		$po_ids=array_chunk($po_ids,999);
		$po_id_cond=" and";
		foreach($po_ids as $poIds)
		{
			if($po_id_cond==" and")  $po_id_cond.="(b.id in(".implode(',',$poIds).")"; else $po_id_cond.=" or b.id in(".implode(',',$poIds).")";
		}
		$po_id_cond.=")";
	}

	/*if ($receiveBasis==4 || $receiveBasis==6) {
		$sql = "select b.id,a.job_no, a.style_ref_no, a.buyer_name, a.order_uom, b.id,b.grouping as ref_no, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date,c.booking_no
		from wo_po_details_master a, wo_po_break_down b,wo_booking_mst c where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and a.company_name=$company_id $buyer_condition $search_con $po_id_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	}
	else
	{
		$sql = "select b.id,a.job_no, a.style_ref_no, a.buyer_name, a.order_uom, b.id,b.grouping as ref_no, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date
		from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id $buyer_condition $search_con $po_id_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	}*/

	$sql = "select b.id,a.job_no, a.style_ref_no, a.buyer_name, a.order_uom, b.grouping as ref_no, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date,c.booking_no, d.id as booking_id from wo_po_details_master a, wo_po_break_down b,wo_booking_dtls c, wo_booking_mst d where a.id=b.job_id and c.po_break_down_id=b.id and c.booking_no = d.booking_no and a.company_name=$company_id $buyer_condition $search_con $search_booking_con2 $po_id_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.booking_type in (1,4) group by b.id,a.job_no, a.style_ref_no, a.buyer_name, a.order_uom, b.grouping , b.po_number,a.total_set_qnty,b.po_quantity,  b.pub_shipment_date,c.booking_no, d.id order by a.job_no ASC";

	//echo $sql;
	?>
	<div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table" >
			<thead>
				<th width="40">SL</th>
				<th width="100">Job No</th>
				<th width="90">Style No</th>
				<th width="100">PO No</th>
				<th width="80">Ref. No</th>
				<th width="60">PO Quantity</th>
				<th width="110">Booking</th>
				<th width="50">UOM</th>
				<th>Shipment Date</th>
			</thead>
		</table>
		<div style="width:718px; overflow-y:scroll; max-height:220px;" id="buyer_list_view" align="center">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table" id="tbl_list_search" >
				<?
				$i=1; $po_row_id='';
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					if(in_array($selectResult[csf('id')],$hidden_po_id))
					{
						if($po_row_id=="") $po_row_id=$i; else $po_row_id.=",".$i;
					}
					$bookingNo=$selectResult[csf('booking_no')];
					$bookingId=$selectResult[csf('booking_id')];
					/*if ($receiveBasis==4 || $receiveBasis==6) {
						$bookingNo=$selectResult[csf('booking_no')];
					}
					else
					{
						$bookingNo=$booking_no_arr[$selectResult[csf('id')]]["booking_no"];
					}*/
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
						<td width="40" align="center"><?php echo "$i"; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
							<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $selectResult[csf('po_number')]; ?>"/>
							<input type="hidden" name="txt_buyer" id="txt_buyer<?php echo $i ?>" value="<? echo $selectResult[csf('buyer_name')]; ?>"/>
							<input type="hidden" name="txt_styleRef" id="txt_styleRef<?php echo $i ?>" value="<? echo $selectResult[csf('style_ref_no')]; ?>"/>
							<input type="hidden" name="txt_receive_basis" id="txt_receive_basis<?php echo $i ?>" value="<? echo $receiveBasis; ?>"/>
							<input type="hidden" name="txt_booking" id="txt_booking<?php echo $i ?>" value="<? echo $bookingNo; ?>"/>
							<input type="hidden" name="txt_booking_id" id="txt_booking_id<?php echo $i ?>" value="<? echo $bookingId; ?>"/>
						</td>
						<td width="100"><p><? echo $selectResult[csf('job_no')]; ?></p></td>
						<td width="90"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
						<td width="100"><p style="word-break: break-all; word-wrap: break-word;"><? echo $selectResult[csf('po_number')]; ?></p></td>
						<td width="80"><p><? echo $selectResult[csf('ref_no')]; ?></p></td>
						<td width="60" align="right"><? echo $selectResult[csf('po_qnty_in_pcs')]; ?></td>
						<td width="110" align="center"><p><? echo $bookingNo; ?></p></td>
						<td width="50" align="center"><p><? echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></p></td>
						<td align="center"><? echo change_date_format($selectResult[csf('pub_shipment_date')]); ?></td>
					</tr>
					<?
					$i++;
				}
				?>
				<input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<?php echo $po_row_id; ?>"/>
			</table>
		</div>
		<table width="720" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:50%; float:left" align="left">
							<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
						</div>
						<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
						</div>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<?

	exit();
}

if ($action=="sample_booking_search_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function fn_show_check()
		{

			if ($('#cbo_buyer_name').val()==0 && $('#txt_search_common').val()=="" ) {
				if( form_validation('cbo_buyer_name','Buyer Name')==false )
				{
					return;
				}

			}
			show_list_view ( $('#txt_search_common').val()+'_'+<? echo $cbo_company_id; ?>+'_'+$('#cbo_buyer_name').val()+'_'+$('#cbo_year').val()+'_'+<? echo $receive_basis; ?>, 'create_sample_booking_search_list_view', 'search_div', 'knit_finish_fabric_receive_by_garments_controller', 'setFilterGrid(\'tbl_list_search\',-1);');
		}

		var selected_id = new Array(); var selected_name = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str )
		{
			var multiCount=selected_id.length;
			if (multiCount>0) {return;}
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}

			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			$('#hidden_booking_id').val(id);
			$('#hidden_booking_no').val(name);
		}

		function hidden_field_reset()
		{
			$('#hidden_booking_id').val('');
			$('#hidden_booking_no').val( '' );
			selected_id = new Array();
			selected_name = new Array();
		}

	</script>
</head>
<body>
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:620px;margin-left:5px;">
			<input type="hidden" name="hidden_booking_id" id="hidden_booking_id" class="text_boxes" value="">
			<input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes" value="">
			<table cellpadding="0" cellspacing="0" width="620" class="rpt_table" align="center">
				<thead>
					<th>Year</th>
					<th>Buyer</th>
					<th>Sample Booking(WO)</th>
					<th>
						<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
					</th>
				</thead>
				<tr class="general">
					<td>
						<?
						echo create_drop_down( "cbo_year", 70, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
						?>
					</td>
					<td align="center">
						<?
						echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $buyer_name, "","" );
						?>
					</td>
					<td align="center">
						<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
					</td>
					<td align="center">
						<input type="button" name="button2" class="formbutton" value="Show" onClick="fn_show_check()" style="width:100px;" />
					</td>
				</tr>
			</table>
			<div id="search_div" style="margin:10px auto;text-align:center;"></div>
		</fieldset>
	</form>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_sample_booking_search_list_view")
{
	$data = explode("_",$data);
	$company_id =$data[1];
	$buyer_id =$data[2];
	$cbo_year =$data[3];
	$receive_basis=$data[4];
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	$search_string=trim($data[0]);
	if($search_string!="")
		$search_field_cond_sample="and s.booking_no like '%$search_string%'";
	$approval_status_cond2= " and s.is_approved in(1)";

	//if($buyer_id==0) { echo "<b>Please Select Buyer First</b>"; die; }

	if($buyer_id!=0) $buyer_cond=" and s.buyer_id =$buyer_id"; else $buyer_cond="";

	$year_id=str_replace("'","",$cbo_year);
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(s.insert_date)=$year_id"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_cond=" and TO_CHAR(s.insert_date,'YYYY')=$year_id"; else $year_cond="";
	}
	if($receive_basis==4 || $receive_basis==6) {
		$sql = "SELECT s.id,s.supplier_id, s.booking_no_prefix_num, s.booking_no, s.booking_date, s.buyer_id, null as po_break_down_id, s.item_category, s.delivery_date, null as job_no_mst, 1 as type, null as po_number FROM wo_non_ord_samp_booking_mst s WHERE s.company_id=$company_id and s.status_active =1 and s.is_deleted=0 and s.item_category=2 $search_field_cond_sample $buyer_cond $year_cond order by type,id";
	}
	else
	{
		$sql = "SELECT s.id,s.supplier_id, s.booking_no_prefix_num, s.booking_no, s.booking_date, s.buyer_id, null as po_break_down_id, s.item_category, s.delivery_date, null as job_no_mst, 1 as type, null as po_number FROM wo_non_ord_samp_booking_mst s WHERE s.company_id=$company_id and s.status_active =1 and s.is_deleted=0 and s.pay_mode=1 and s.fabric_source in (2,3) and s.item_category=2 $search_field_cond_sample $approval_status_cond2 $buyer_cond $year_cond order by type,id";
	}
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="525" class="rpt_table" style="margin:auto;">
		<thead>
			<th width="30">SL</th>
			<th width="115">Booking No</th>
			<th width="80">Booking Date</th>
			<th width="100">Buyer</th>
			<th width="100">Item Category</th>
			<th>Delivary date</th>
		</thead>
	</table>
	<div style="width:525px; max-height:280px;margin:0 auto; overflow:scroll;" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="525" class="rpt_table" id="tbl_list_search" style="">
			<?
			$i=1;
			foreach ($result as $row)
			{
				if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>);">
					<td width="30">
						<? echo $i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>"/>
						<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf('booking_no')]; ?>"/>
					</td>
					<td width="115"><p><? echo $row[csf('booking_no')]; ?></p></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="100"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
					<td width="100"><p><? echo $item_category[$row[csf('item_category')]]; ?></p></td>
					<td align="center"><? echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<table width="720" cellspacing="0" cellpadding="0" style="border:none" align="center">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%">
					<!-- <div style="width:50%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
					</div>
					<div style="width:50%; float:left" align="left"> -->
						<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
						<!-- </div> -->
					</div>
				</td>
			</tr>
		</table>
		<?
		exit();
	}

	if ($action=="po_popup")
	{
		echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
		extract($_REQUEST);

		if($roll_maintained==1)
		{
			$disable_drop_down=1;
			$prev_distribution_method=2;
			$disabled="disabled='disabled'";

			if($receive_basis==2 || $receive_basis==9 || $receive_basis==11) $width="1300"; else $width="1145";
			$roll_arr=return_library_array("select po_breakdown_id, max(roll_no) as roll_no from pro_roll_details where entry_form in(7,37) group by po_breakdown_id",'po_breakdown_id','roll_no');
		}
		else
		{
			$prev_distribution_method=1;
			$disabled="";
			$disable_drop_down=0;
			if($receive_basis==1 || $receive_basis==2 || $receive_basis==9 || $receive_basis==11  || $receive_basis==10) $width="1070"; else $width="1020";
		}
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$cbo_company_id and variable_list=23 and category in (2,13) and status_active =1 and is_deleted=0 order by id");

		$over_receive_limit=0;
		$over_receive_limit_grey_used=0;
		foreach ($variable_set_invent as $var )
		{
			if($var[csf("category")] ==2)
			{
				$over_receive_limit =$var[csf('over_rcv_percent')];
			}
			else if($var[csf("category")] ==13)
			{
				//$over_receive_limit_grey_used =$var[csf('over_rcv_percent')];
			}
		}
		//$over_receive_limit = !empty($variable_set_invent) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;

		$style_wise_popup=0;
		$style_wise_popup=return_field_value("production_entry","variable_settings_production","company_name=$cbo_dyeing_company and variable_list=72 and is_deleted=0 and status_active=1");
		if($style_wise_popup==1) $style_wise_popup=$style_wise_popup; else $style_wise_popup=0;
		?>
		<script>
			var receive_basis="<? echo $receive_basis; ?>";
			var roll_maintained="<? echo $roll_maintained; ?>";
			var style_wise_popup="<? echo $style_wise_popup; ?>";
			var txt_payment_overrcv_qty="<? echo $txt_payment_overrcv_qty; ?>";
			function distribute_qnty(str)
			{
				if(str==1)
				{
					var tot_po_qnty=$('#tot_po_qnty').val()*1;
					var tot_req_qnty=$('#tot_req_qnty').val()*1;
					var txt_prop_finish_qnty=$('#txt_prop_finish_qnty').val()*1;
					var tblRow = $("#tbl_list_search tr").length;
					var len=totalFinish=0;

					$("#tbl_list_search").find('tr').each(function()
					{
						var txtreqqty=$(this).find('input[name="txtreqqty[]"]').val()*1;
						if(txtreqqty>0)
						{
							len=len+1;
							var po_qnty=$(this).find('input[name="txtPoQnty[]"]').val()*1;
							var txtbalanceqnty=$(this).find('input[name="txtbalanceqnty[]"]').val()*1;
							var hidden_cummulative_rcv_qnty=$(this).find('input[name="hidden_cummulative_rcv_qnty[]"]').val()*1;

							var perc=(txtreqqty/tot_req_qnty)*100;


							var finish_qnty=((perc*txt_prop_finish_qnty)/100);
							totalFinish = (totalFinish*1+finish_qnty*1).toFixed(2);

							var balance_qty= txtreqqty-(hidden_cummulative_rcv_qnty + finish_qnty);

							//if last row then quantity will adjust with balance
							if(tblRow==len)
							{
								var balance = (txt_prop_finish_qnty-totalFinish);
								if(balance > 0){
									finish_qnty = (finish_qnty*1 + balance*1);
								}else{
									finish_qnty = (finish_qnty*1 - balance*1);
								}
								if(balance!=0) totalFinish=totalFinish*1+(balance*1);
							}

							$(this).find('input[name="txtfinishQnty[]"]').val(finish_qnty.toFixed(2));
							$(this).find('input[name="txtbalanceqnty[]"]').val(balance_qty.toFixed(2));

							var row_id_str = $(this).find('input[name="txtfinishQnty[]"]').attr("id");
							var row_id_arr = row_id_str.split('_');
							var rowNo = row_id_arr[1];
							//alert(rowNo);
							//fn_check_balance(rowNo);
						}
					});
				}
				else
				{
					$('#txt_prop_finish_qnty').val('');
					$("#tbl_list_search").find('tr').each(function()
					{
						$(this).find('input[name="txtfinishQnty[]"]').val('');
						$(this).find('input[name="txtbalanceqnty[]"]').val('');
					});
				}
			}

			function textile_delivery_distribute_qnty(str)
			{
				if(str==1)
				{
					//34376**69874,69873,69872,69871__34377**69878,69877,69876,69875
					var job_po_string=$('#job_po_string').val();

					var txt_prop_finish_qnty=$('#txt_prop_finish_qnty').val()*1;
					var tot_req_qnty=$('#tot_delivery_req_qnty').val()*1;
					var jobnos_arr = job_po_string.split("__");

					//alert(jobnos_arr.length);

					for(var i = 0; i < jobnos_arr.length; i++)
					{
						var jobPoNos_arr= jobnos_arr[i].split("**");
						var poIdsArr = jobPoNos_arr[1].split(",");
						var totalFinish=0;

						var txtJobId = jobPoNos_arr[0];
						var txtJobWiseTotalDeliveryQnty=$('#txtJobWiseTotalDeliveryQnty'+txtJobId).val()*1;

						//Job wise proportionate quantity
						var job_perc = (txtJobWiseTotalDeliveryQnty/tot_req_qnty)* 100;
						var job_dist_qnty =  (job_perc*txt_prop_finish_qnty)/100;

						for(var j = 0; j < poIdsArr.length; j++)
						{
							var txtreqqty=$('#txtJobShipDateQnty_'+poIdsArr[j]).val()*1;

							if(txtreqqty>0)
							{
								var perc=(txtreqqty/txtJobWiseTotalDeliveryQnty)*100;
								var finish_qnty=((perc*job_dist_qnty)/100);
								finish_qnty = finish_qnty.toFixed(2)*1;
								totalFinish = (totalFinish*1+finish_qnty*1);//.toFixed(2);

								//alert(job_dist_qnty +'<'+ totalFinish)
								//Last row of this job with be adjusted for quantity
								if(poIdsArr.length== j+1)
								{
									if(job_dist_qnty < totalFinish)
									{
										finish_qnty = finish_qnty- (totalFinish - job_dist_qnty );
									}
									else if(job_dist_qnty > totalFinish)
									{
										finish_qnty = finish_qnty + (job_dist_qnty - totalFinish);
									}
								}

								$('#txtJobShipDateQnty_'+poIdsArr[j]).closest("td").find('input[name="txtfinishQnty[]"]').val(finish_qnty.toFixed(2));

							}

						}
					}

					$("#tbl_list_search").find('tr').each(function()
					{
						var txtreqqty=$(this).find('input[name="txtJobShipDateQnty[]"]').val()*1;
						var txtBookingReqqty=$(this).find('input[name="txtreqqty[]"]').val()*1;
						var txtfinishQnty = $(this).find('input[name="txtfinishQnty[]"]').val()*1;
						var hidden_cummulative_rcv_qnty=$(this).find('input[name="hidden_cummulative_rcv_qnty[]"]').val()*1;
						if(txtreqqty>0)
						{
							var balance_qty= txtBookingReqqty-(hidden_cummulative_rcv_qnty + txtfinishQnty);
							$(this).find('input[name="txtbalanceqnty[]"]').val(balance_qty.toFixed(2));
						}


					});
				}
				else
				{
					$('#txt_prop_finish_qnty').val('');
					$("#tbl_list_search").find('tr').each(function()
					{
						$(this).find('input[name="txtfinishQnty[]"]').val('');
						$(this).find('input[name="txtbalanceqnty[]"]').val('');
					});
				}

			}


			function grey_distribute_qnty(str)
			{
				if(str==1)
				{
					//var tot_po_qnty=$('#tot_po_qnty').val()*1;
					var tot_req_qnty=$('#tot_req_qnty').val()*1;
					var txt_prop_grey_qnty=$('#txt_prop_grey_qnty').val()*1;
					var tblRow = $("#tbl_list_search tr").length;
					var len=totalGrey=0;

					$("#tbl_list_search").find('tr').each(function()
					{
						var txtreqqty=$(this).find('input[name="txtreqqty[]"]').val()*1;
						if(txtreqqty>0)
						{
							len=len+1;
							//var po_qnty=$(this).find('input[name="txtPoQnty[]"]').val()*1;
							//var txtbalanceqnty=$(this).find('input[name="txtbalanceqnty[]"]').val()*1;
							//var hidden_cummulative_rcv_qnty=$(this).find('input[name="hidden_cummulative_rcv_qnty[]"]').val()*1;

							var perc=(txtreqqty/tot_req_qnty)*100;


							var grey_qnty=((perc*txt_prop_grey_qnty)/100);
							totalGrey = (totalGrey*1+grey_qnty*1).toFixed(2);
							//totalFinish = totalFinish;
							//var balance_qty= txtreqqty-(hidden_cummulative_rcv_qnty + grey_qnty);

							if(tblRow==len)
							{
								var balance = (txt_prop_grey_qnty-totalGrey);
								if(balance > 0){
									grey_qnty = (grey_qnty*1 + balance*1);
								}else{
									grey_qnty = (grey_qnty*1 - balance*1);
								}
								if(balance!=0) totalGrey=totalGrey*1+(balance*1);
							}

							$(this).find('input[name="txtgreyqty[]"]').val(grey_qnty.toFixed(2));
							//$(this).find('input[name="txtbalanceqnty[]"]').val(balance_qty.toFixed(2));

							/* var row_id_str = $(this).find('input[name="txtgreyQnty[]"]').attr("id");
							var row_id_arr = row_id_str.split('_');
							var rowNo = row_id_arr[1];
							//alert(rowNo);
							fn_check_balance(rowNo) */
						}
					});
				}
				else
				{
					$('#txt_prop_grey_qnty').val('');
					$("#tbl_list_search").find('tr').each(function()
					{
						$(this).find('input[name="txtgreyQnty[]"]').val('');
					});
				}
			}

			function wo_distribute_qnty(str)
			{
				if(str==1)
				{
					var tot_po_qnty=$('#tot_po_qnty').val()*1;
					var tot_req_qnty=$('#tot_req_qnty').val()*1;
					var txt_prop_finish_qnty=$('#txt_prop_finish_qnty').val()*1;
					var tblRow = $("#tbl_list_search tr").length;
					var balance =txt_prop_finish_qnty;
					var len=totalFinish=0;
					var tot_required_req_qnty=0;
					$("#tbl_list_search").find('tr').each(function()
					{
						var txtreqqty=$(this).find('input[name="txtreqqty[]"]').val()*1;
						var hidden_cummulative_rcv_qnty=$(this).find('input[name="hidden_cummulative_rcv_qnty[]"]').val()*1;
						var required_req_qnty =txtreqqty - hidden_cummulative_rcv_qnty;
						tot_required_req_qnty = tot_required_req_qnty + required_req_qnty*1;
					});
					if(txt_prop_finish_qnty>tot_required_req_qnty)
					{
						var exceeds_qty=txt_prop_finish_qnty-tot_required_req_qnty;
						alert("Total Production Qty Exceeds Total Required Qty (By "+exceeds_qty+" Qty).");
						$('#txt_prop_finish_qnty').val('');

						return;
					}

					$("#tbl_list_search").find('tr').each(function()
					{
						var txtreqqty=$(this).find('input[name="txtreqqty[]"]').val()*1;
						var hidden_cummulative_rcv_qnty=$(this).find('input[name="hidden_cummulative_rcv_qnty[]"]').val()*1;

						var required_req_qnty =txtreqqty - hidden_cummulative_rcv_qnty;

						if(balance>0)
						{
							if(required_req_qnty<0) required_req_qnty=0;
							if(balance>required_req_qnty)
							{
								var finish_qnty=required_req_qnty;
								balance=balance-required_req_qnty;
							}
							else
							{
								var finish_qnty=balance;
								balance=0;
							}

							if(tblRow==len)
							{
								var issue_qnty=txt_prop_finish_qnty-totalFinish;
							}

							totalFinish = totalFinish*1+finish_qnty*1;

							var balance_qty= txtreqqty-(hidden_cummulative_rcv_qnty + finish_qnty);

							$(this).find('input[name="txtfinishQnty[]"]').val(finish_qnty.toFixed(2));
							$(this).find('input[name="txtbalanceqnty[]"]').val(balance_qty.toFixed(2));
						}
						else
						{
							$(this).find('input[name="txtfinishQnty[]"]').val('');
						}
					});
				}
				else
				{
					$('#txt_prop_finish_qnty').val('');
					$("#tbl_list_search").find('tr').each(function()
					{
						$(this).find('input[name="txtfinishQnty[]"]').val('');
						$(this).find('input[name="txtbalanceqnty[]"]').val('');
					});
				}
			}

			function fnc_close()
			{
				var save_string='';	 var tot_finish_qnty=0; var tot_grey_qnty=0; var tot_cum_rec=0; var tot_reqqty=0;var tot_balanceqnty=0; var order_nos=''; var no_of_roll=''; var tot_reject_qnty=0; var tot_required_qnty = 0;
				var po_id_array = new Array(); var buyer_id_array = new Array(); var buyer_name_array = new Array();var tot_balance="";
				var hdn_delivery_qnty=$('#hdn_delivery_qnty').val()*1;
				var hidden_cummu_deli_rcv_qnty=$('#hidden_cummu_deli_rcv_qnty').val()*1;
				var hdn_dtls_id=$('#hdn_dtls_id').val();
				var hdnRequiredQnty=""; var hiddenCummulativeRcvQnty="";
				var overRecLim="<? echo $over_receive_limit; ?>";
				var overRecLimGreyUsed="<? echo $over_receive_limit_grey_used; ?>";
				var receiveBasis="<? echo $receive_basis; ?>";
				var overValue = 0;  var total_txt_finish_and_reject_qnty= 0;
				var overValueJobWise = 0;
				var overValueDeliveryWise = 0;
				var overValueGreyUsed = 0;
				var job_shipdate_qnty = new Array();
				var overed_po="";
				$("#tbl_list_search").find('tr').each(function()
				{
					var txtPoId=$(this).find('input[name="txtPoId[]"]').val();
					var txtPoNo=$(this).find('input[name="txtPoNo[]"]').val();
					var txtfinishQnty=$(this).find('input[name="txtfinishQnty[]"]').val();
					var txtrejectQnty=$(this).find('input[name="txtrejectqnty[]"]').val();
					var txtfabmeterqnty=$(this).find('input[name="txtfabmeterqnty[]"]').val();
					var txtreqqty=$(this).find('input[name="txtreqqty[]"]').val();
					var txtbalanceqnty=$(this).find('input[name="txtbalanceqnty[]"]').val();
					var buyerId=$(this).find('input[name="buyerId[]"]').val();
					var buyerName=$(this).find('input[name="buyerName[]"]').val();
					var txtRoll=$(this).find('input[name="txtRoll[]"]').val();
					var txtRollId=$(this).find('input[name="txtRollId[]"]').val();
					var txtBarcodeNo=$(this).find('input[name="txtBarcodeNo[]"]').val();
					var hdn_cumm_receive=$(this).find('input[name="hdn_cumm_receive[]"]').val();
					hdnRequiredQnty=$(this).find('input[name="txtreqqty[]"]').val();
					hiddenCummulativeRcvQnty=$(this).find('input[name="hidden_cummulative_rcv_qnty[]"]').val()*1;
					var txtgreyqty=$(this).find('input[name="txtgreyqty[]"]').val()*1;
					var hdnCummiGreyUsedQty=$(this).find('input[name="hdnCummiGreyUsedQty[]"]').val()*1;

					var txtDeliveryReqQty=$(this).find('input[name="txtJobShipDateQnty[]"]').val()*1;

					if(isNaN(txtgreyqty))
					{
						txtgreyqty=0;
					}

					tot_finish_qnty=tot_finish_qnty*1+txtfinishQnty*1;
					tot_grey_qnty=tot_grey_qnty*1+txtgreyqty*1;
					tot_reject_qnty=tot_reject_qnty*1+txtrejectQnty*1;
					tot_required_qnty+=txtreqqty*1;

					total_txt_finish_and_reject_qnty += txtfinishQnty*1 + txtrejectQnty*1;

					if(overValue==0)
					{
						if(txtfinishQnty*1 > 0)
						{
							//alert( ((overRecLim*hdnRequiredQnty*1)/100 +hdnRequiredQnty*1) + '<' + '('+hiddenCummulativeRcvQnty*1 +'+'+ txtfinishQnty*1+')');
							var required_qnty_with_overpercentage = number_format( ((overRecLim*hdnRequiredQnty*1)/100 +hdnRequiredQnty*1),2,'.','')*1; 
							if( required_qnty_with_overpercentage < (hiddenCummulativeRcvQnty*1 + txtfinishQnty*1))
							{
								overValue =1;
								overed_po = txtPoNo;
							}
						}
					}

					if (receiveBasis==11 && overValueGreyUsed==0)
					{
						if(number_format(txtgreyqty,2,'.','')*1)
						{
							if( ((overRecLimGreyUsed*number_format(hdnRequiredQnty,2,'.','')*1)/100 +hdnRequiredQnty*1) < (number_format(hdnCummiGreyUsedQty,2,'.','')*1 + number_format(txtgreyqty,2,'.','')*1) )
							{
								overValueGreyUsed=1;
							}
						}
					}

					if(overValueDeliveryWise==0)
					{
						if(txtfinishQnty*1 > 0)
						{
							if( number_format(txtDeliveryReqQty,2,'.','')*1 < (number_format(hiddenCummulativeRcvQnty,2,'.','')*1 + number_format(txtfinishQnty,2,'.','')*1) && ((number_format(hiddenCummulativeRcvQnty,2,'.','')*1 + number_format(txtfinishQnty,2,'.','')*1) - number_format(txtDeliveryReqQty,2,'.','')*1) > .1 )
							{
								//alert(txtDeliveryReqQty*1 +'< ('+hiddenCummulativeRcvQnty*1 +'+'+ txtfinishQnty*1 +')');
								//alert( ((number_format(hiddenCummulativeRcvQnty,2,'.','')*1 + number_format(txtfinishQnty,2,'.','')*1) - number_format(txtDeliveryReqQty,2,'.','')*1));
								//overValueDeliveryWise =1;
							}
						}
					}


					if(hdn_dtls_id!=""){
						tot_cum_rec+=hiddenCummulativeRcvQnty*1+(hdn_cumm_receive*1+(txtfinishQnty*1-hdn_cumm_receive*1));
					}else{

						tot_cum_rec+=hiddenCummulativeRcvQnty*1+txtfinishQnty*1;
					}

					tot_balance=tot_balance*1+txtbalanceqnty*1;

					if(txtRoll*1>0)
					{
						no_of_roll=no_of_roll*1+1;
					}

					if(save_string=="")
					{
						save_string=txtPoId+"**"+txtfinishQnty*1+"**"+txtRoll+"**"+txtRollId+"**"+txtBarcodeNo+"**"+txtrejectQnty+"**"+tot_balance+"**"+txtfabmeterqnty+"**"+txtgreyqty;
					}
					else
					{
						save_string+=","+txtPoId+"**"+txtfinishQnty*1+"**"+txtRoll+"**"+txtRollId+"**"+txtBarcodeNo+"**"+txtrejectQnty+"**"+tot_balance+"**"+txtfabmeterqnty+"**"+txtgreyqty;
					}

					if( jQuery.inArray( txtPoId, po_id_array) == -1 )
					{
						if(txtfinishQnty>0){
							po_id_array.push(txtPoId);
							if(order_nos=='') order_nos=txtPoNo; else order_nos+=","+txtPoNo;
						}
					}

					if( jQuery.inArray( buyerId, buyer_id_array) == -1 )
					{
						buyer_id_array.push(buyerId);
						buyer_name_array.push(buyerName);
					}
				});

				var overRecLimt=(overRecLim*hdnRequiredQnty)/100;
				if (receiveBasis==10)
				{
					if(overValue==1)
					{
						alert("Quantity not available.\nPo No: "+overed_po);
						return;
					}


					var balance = hdn_delivery_qnty-hidden_cummu_deli_rcv_qnty;
					if( balance < number_format(total_txt_finish_and_reject_qnty,2,'.','') )
					{
						alert("Receive quantity can not be greater than Delivery quantity.\nDelivery quantity balance= " + balance);
						return;

					}

					/* if(overValueDeliveryWise==1 && style_wise_popup ==1)
					{
						alert("Receive quantity can not be greater than Delivery quantity.");
						return;
					} */

				}
				else if(txt_payment_overrcv_qty==0)
				{
					//N. B. if payment for 'YES' then other basis will validate with over receive quantity
					if (receiveBasis==1 || receiveBasis==2)
					{
						if(receiveBasis==2)
						{
							if(overValue==1)
							{
								alert("Quantity not available");
								return;
							}
						}
						else
						{
							/*if((overRecLimt*1+tot_required_qnty*1) < (tot_cum_rec*1 + tot_reject_qnty*1))
							{
								alert("Quantity not available");
								return;
							}*/
							if(overValue==1)
							{
								alert("Quantity not available");
								return;
							}
						}
					}
					else
					{

						/*if(overRecLimt-hiddenCummulativeRcvQnty < (tot_cum_rec*1 + tot_reject_qnty*1))
						{
							alert("Quantity not available");
							return;
						}*/
						if(overValue==1)
						{
							alert("Quantity not available");
							return;
						}

						if(overValueGreyUsed==1 && receiveBasis==11)
						{
							alert("Grey Quantity not available");
							return;
						}


					}
				}
				
				//alert(overValue);return;
				$('#save_string').val( save_string );
				$('#tot_finish_qnty').val(tot_finish_qnty.toFixed(2));
				$('#tot_grey_qnty').val(tot_grey_qnty.toFixed(2));
				$('#tot_reject_qnty').val(tot_reject_qnty);
				$('#tot_balance_qnty').val(tot_balanceqnty);
				$('#all_po_id').val( po_id_array );
				$('#order_nos').val( order_nos );
				$('#buyer_id').val( buyer_id_array );
				$('#number_of_roll').val( no_of_roll );
				$('#buyer_name').val( buyer_name_array );
				$('#distribution_method').val( $('#cbo_distribiution_method').val());

				parent.emailwindow.hide();
			}

			function roll_duplication_check(row_id)
			{
				var row_num=$('#tbl_list_search tr').length;
				var po_id=$('#txtPoId_'+row_id).val();
				var roll_no=$('#txtRoll_'+row_id).val();

				if(roll_no*1>0)
				{
					for(var j=1; j<=row_num; j++)
					{
						if(j==row_id)
						{
							continue;
						}
						else
						{
							var po_id_check=$('#txtPoId_'+j).val();
							var roll_no_check=$('#txtRoll_'+j).val();

							if(po_id==po_id_check && roll_no==roll_no_check)
							{
								alert("Duplicate Roll No.");
								$('#txtRoll_'+row_id).val('');
								return;
							}
						}
					}

					var txtRollId=$('#txtRollId_'+row_id).val();
					var data=po_id+"**"+roll_no+"**"+txtRollId;
					var response=return_global_ajax_value( data, 'roll_duplication_check', '', 'knit_finish_fabric_receive_by_garments_controller');
					var response=response.split("_");

					if(response[0]!=0)
					{
						var po_number=$('#tr_'+row_id).find('td:first').text();
						alert("This Roll Already Used. Duplicate Not Allowed");
						$('#txtRoll_'+row_id).val('');
						return;
					}
				}
			}

			function add_break_down_tr( i )
			{
				var cbo_distribiution_method=$('#cbo_distribiution_method').val();
				var isDisbled=$('#txtRoll_'+i).is(":disabled");

				if(cbo_distribiution_method==2 && isDisbled==false)
				{
					var row_num=$('#txt_tot_row').val();
					row_num++;

					var clone= $("#tr_"+i).clone();
					clone.attr({
						id: "tr_" + row_num,
					});

					clone.find("input,select").each(function(){

						$(this).attr({
							'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
							'name': function(_, name) { return name },
							'value': function(_, value) { return value }
						});

					}).end();

					$("#tr_"+i).after(clone);

					$('#txtOrginal_'+row_num).removeAttr("value").attr("value","0");
					$('#txtRoll_'+row_num).removeAttr("value").attr("value","");
					$('#txtfinishQnty_'+row_num).removeAttr("value").attr("value","");
					$('#txtrejectqnty_'+row_num).removeAttr("value").attr("value","");
					$('#txtbalanceqnty_'+row_num).removeAttr("value").attr("value","");
					$('#txtRoll_'+row_num).removeAttr("onBlur").attr("onBlur","roll_duplication_check("+row_num+");");
					$('#txtRollId_'+row_num).removeAttr("value").attr("value","");
					$('#txtBarcodeNo_'+row_num).removeAttr("value").attr("value","");

					$('#increase_'+row_num).removeAttr("value").attr("value","+");
					$('#decrease_'+row_num).removeAttr("value").attr("value","-");
					$('#increase_'+row_num).removeAttr("onclick").attr("onclick","add_break_down_tr("+row_num+");");
					$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");

					$('#txt_tot_row').val(row_num);
					set_all_onclick();
				}
			}

			function fn_deleteRow(rowNo)
			{
				var txtOrginal=$('#txtOrginal_'+rowNo).val()*1;
				var txtRollId=$('#txtRollId_'+rowNo).val();
				var txt_deleted_id=$('#hide_deleted_id').val();
				var selected_id='';
				if(txtOrginal==0)
				{
					if(txtRollId!='')
					{
						if(txt_deleted_id=='') selected_id=txtRollId; else selected_id=txt_deleted_id+','+txtRollId;
						$('#hide_deleted_id').val( selected_id );
					}
					$("#tr_"+rowNo).remove();
				}
			}

			function fn_check_balance(rowNo)
			{
				var receiveBasis="<? echo $receive_basis; ?>";
				if(receiveBasis ==10 && style_wise_popup ==1)
				{
					var txtPoDeliveryQnty = $('#txtJobShipDateQnty_' + rowNo).val() * 1;
					var txtPoReceiveQnty = $('#hidden_cummulative_rcv_qnty_' + rowNo).val() * 1;
					var txtfinishQnty = $('#txtfinishQnty_' + rowNo).val() * 1;
					//alert("("+txtfinishQnty +"+"+ txtPoReceiveQnty +") >"+ txtPoDeliveryQnty)
					if ((txtfinishQnty + txtPoReceiveQnty ) > txtPoDeliveryQnty) {
						alert("Receive qnty can not greater than delivery qnty.\nprevious recieve: "+txtPoReceiveQnty+",\ndelivery qnty: "+txtPoDeliveryQnty);
						$('#txtfinishQnty_' + rowNo).val('');
						return;
					}
				}
			}
		</script>
	</head>
	<body>
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:<? echo $width; ?>px;margin-left:5px">
				<input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
				<input type="hidden" name="tot_finish_qnty" id="tot_finish_qnty" class="text_boxes" value="">
				<input type="hidden" name="tot_grey_qnty" id="tot_grey_qnty" class="text_boxes" value="">
				<input type="hidden" name="tot_reject_qnty" id="tot_reject_qnty" class="text_boxes" value="">
				<input type="hidden" name="tot_balance_qnty" id="tot_balance_qnty" class="text_boxes" value="">
				<input type="hidden" name="all_po_id" id="all_po_id" class="text_boxes" value="">
				<input type="hidden" name="order_nos" id="order_nos" class="text_boxes" value="">
				<input type="hidden" name="number_of_roll" id="number_of_roll" class="text_boxes" value="">
				<input type="hidden" name="buyer_id" id="buyer_id" class="text_boxes" value="">
				<input type="hidden" name="buyer_name" id="buyer_name" class="text_boxes" value="">
				<input type="hidden" name="distribution_method" id="distribution_method" class="text_boxes" value="">
				<input type="hidden" name="hide_deleted_id" id="hide_deleted_id" class="text_boxes" value="<? echo $txt_deleted_id; ?>">
				<input type="hidden" name="hdn_delivery_qnty" id="hdn_delivery_qnty" value="<? echo number_format($hdn_delivery_qnty,2,'.',''); ?>">
				<input type="hidden" name="hdn_dtls_id" id="hdn_dtls_id" value="<? echo $update_dtls_id; ?>">
				<?
				$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$cbo_company_id and variable_list=23 and category = 2 and status_active =1 and is_deleted=0 order by id");
				$over_receive_limit = !empty($variable_set_invent) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;

				$txt_dia_width = strtoupper($txt_dia_width); //For avoiding case sensitivity of dia. 05/08/2023
				$txt_original_dia_width = strtoupper($txt_original_dia_width);

				$req_qty_array=array();
				if($receive_basis==1)
				{
					$po_dtls_cond = ($pi_dtls_id!="") ? " and b.id=$pi_dtls_id" : "";
					$sql_pi="select b.work_order_no,sum(b.quantity) as quantity,a.pi_basis_id from com_pi_master_details a, com_pi_item_details b where a.pi_basis_id in(1,2) and a.id='$txt_booking_no_id' and a.id=b.pi_id and b.determination_id=$fabric_desc_id $po_dtls_cond  and b.uom=$cbouom and b.status_active=1 and b.is_deleted=0 group by b.work_order_no,a.pi_basis_id";
					$sql_pi_res=sql_select($sql_pi);
					foreach($sql_pi_res as $pi)
					{
						$pi_req_qty=$pi[csf('quantity')];
						$pi_basis_id=$pi[csf('pi_basis_id')];
						$work_order_no=$pi[csf('work_order_no')];
					}

					if($pi_basis_id==1)
					{
						$dia_width_cond="";
						if(str_replace("'","",$txt_original_dia_width)=="")
						{
							if($db_type==0)
							{
								$dia_width_cond = " and a.dia_width = '$txt_original_dia_width'";
							}
							else
							{
								$dia_width_cond = " and a.dia_width is null";
							}
						}
						else
						{
							$dia_width_cond = " and a.dia_width = '$txt_original_dia_width'";
						}

						$reqQnty_fin = " select a.po_break_down_id,b.uom, sum(a.fin_fab_qnty) as fabric_qty
						from wo_booking_dtls a,wo_pre_cost_fabric_cost_dtls b
						where a.status_active=1 and a.is_deleted=0 and a.pre_cost_fabric_cost_dtls_id=b.id
						and a.booking_no='$work_order_no'
						$dia_width_cond and a.fabric_color_id='$fabricColorId' and b.gsm_weight='$txt_gsm'
						and b.uom=$cbouom and b.lib_yarn_count_deter_id=$fabric_desc_id and b.status_active=1 and b.is_deleted=0
						group by a.po_break_down_id,b.uom ";

						$reqQnty_fin_res = sql_select($reqQnty_fin);
						foreach($reqQnty_fin_res as $req_val)
						{
							$req_qty_array[$req_val[csf('po_break_down_id')]]=$req_val[csf('fabric_qty')];
							$po_ids_arr[$req_val[csf('po_break_down_id')]] = $req_val[csf('po_break_down_id')];
						}
					}
					else
					{
						$pi_req_qty=$pi_req_qty;
					}

					$uom_cond = ($cbouom!="")?"and b.uom=$cbouom":"";
					$sql_cuml=" select c.id,a.po_breakdown_id,c.booking_id,b.id dtls_id,b.pi_wo_dtls_id,sum(a.quantity) as qnty from order_wise_pro_details a,pro_finish_fabric_rcv_dtls b,inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id and po_breakdown_id in (".implode(",",$po_ids_arr).") and a.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.fabric_description_id=$fabric_desc_id  $uom_cond and b.color_id=$fabricColorId and b.pi_wo_dtls_id=$pi_dtls_id group by c.id,a.po_breakdown_id,c.booking_id,b.id,b.pi_wo_dtls_id";
					//and b.body_part_id=$body_part_id

					$sql_result_cuml=sql_select($sql_cuml);
					foreach($sql_result_cuml as $row)
					{
						if($update_dtls_id!="" && $update_dtls_id==$row[csf('dtls_id')]){
							$this_challan_rec_qty[$row[csf('po_breakdown_id')]]+=$row[csf('qnty')];
						}else{
							$cumu_rec_qty[$row[csf('po_breakdown_id')]]+=$row[csf('qnty')];
						}
						$receive_mst_id_arr[$row[csf("id")]] = $row[csf("id")];
					}

					$sql_return="select a.po_breakdown_id, sum(a.quantity) as qnty from order_wise_pro_details a, inv_finish_fabric_issue_dtls b, product_details_master c, inv_issue_master d, inv_receive_master e where a.entry_form =46 and a.dtls_id=b.id and b.prod_id=c.id and e.booking_no = '$txt_booking_no' and c.color =$fabricColorId and c.detarmination_id =$fabric_desc_id $uom_cond and a.po_breakdown_id in (".implode(",",$po_ids_arr).") and b.body_part_id =$body_part_id and b.mst_id = d.id and d.received_id = e.id and e.entry_form = 37 and c.gsm=$txt_gsm  and d.received_id  in (".implode(',', $receive_mst_id_arr).") group by a.po_breakdown_id";

					$sql_result_return=sql_select($sql_return);
					foreach($sql_result_return as $row)
					{
						$cumu_return_qty[$row[csf('po_breakdown_id')]] += $row[csf('qnty')];
					}
				}
				else if($receive_basis==2)
				{
					$booking_cond = "and booking_no='$txt_booking_no'";

					$reqQnty = "SELECT a.po_break_down_id,b.lib_yarn_count_deter_id as determination_id, b.body_part_id, a.fabric_color_id as color_id, sum(a.fin_fab_qnty) as fabric_qty,sum(a.grey_fab_qnty) as grey_fabric_qty,a.pre_cost_fabric_cost_dtls_id, a.fabric_source as main_fabric_source, b.fabric_source as dtls_fabric_source from wo_booking_dtls a,wo_pre_cost_fabric_cost_dtls b where a.pre_cost_fabric_cost_dtls_id=b.id and a.status_active=1 and a.is_deleted=0 and b.lib_yarn_count_deter_id = $fabric_desc_id and b.body_part_id=$body_part_id and a.fabric_color_id=$fabricColorId $booking_cond group by a.po_break_down_id,b.lib_yarn_count_deter_id, b.body_part_id, a.fabric_color_id,a.pre_cost_fabric_cost_dtls_id, a.fabric_source, b.fabric_source";

					$reqQnty_res = sql_select($reqQnty);
					foreach($reqQnty_res as $req_val)
					{
						if($req_val[csf("main_fabric_source")]==2 || $req_val[csf("dtls_fabric_source")]==2)
						{
							$req_qty_array[$req_val[csf("po_break_down_id")]][$req_val[csf("pre_cost_fabric_cost_dtls_id")]] += $req_val[csf("grey_fabric_qty")];
						}
						else
						{
							$req_qty_array[$req_val[csf("po_break_down_id")]][$req_val[csf("pre_cost_fabric_cost_dtls_id")]] += $req_val[csf("fabric_qty")];
						}
						
						$po_ids_arr[$req_val[csf('po_break_down_id')]] = $req_val[csf('po_break_down_id')];
					}

				}
				else if($receive_basis==11)
				{
					if( $booking_without_order==0)
					{

						if($all_po_id=="")
						{
							$all_po_sql = sql_select("select po_break_down_id from wo_booking_dtls where status_active=1 and is_deleted=0 and booking_no='$txt_booking_no' group by po_break_down_id");
							foreach($all_po_sql as $row)
							{
								$all_po_id.=$row[csf("po_break_down_id")].",";
							}
							$all_po_id=chop($all_po_id,",");
						}

						$reqQnty="select a.po_break_down_id,b.lib_yarn_count_deter_id as determination_id, b.body_part_id, b.gsm_weight,a.dia_width, a.pre_cost_fabric_cost_dtls_id,a.fabric_color_id as color_id, sum(a.wo_qnty) as qnty, avg(a.rate) as rate , b.uom
						from wo_booking_dtls a, wo_pre_cost_fab_conv_cost_dtls c, wo_pre_cost_fabric_cost_dtls b
						where a.pre_cost_fabric_cost_dtls_id=c.id and c.fabric_description=b.id and a.booking_no='$txt_booking_no' and a.po_break_down_id in($all_po_id) and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.lib_yarn_count_deter_id = $fabric_desc_id and b.body_part_id=$body_part_id and a.fabric_color_id=$fabricColorId $booking_cond and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and a.wo_qnty>0  group by a.po_break_down_id,b.lib_yarn_count_deter_id, b.body_part_id, b.gsm_weight,a.dia_width,a.pre_cost_fabric_cost_dtls_id, a.fabric_color_id, b.uom";

						$reqQnty_res = sql_select($reqQnty);

						foreach($reqQnty_res as $rows)
						{
							$req_qty_array[$rows[csf("po_break_down_id")]][$rows[csf("pre_cost_fabric_cost_dtls_id")]]=$rows[csf("qnty")];
							$poId = $rows[csf("po_break_down_id")];
							$po_ids_arr[$rows[csf("po_break_down_id")]] = $rows[csf("po_break_down_id")];
						}

					}
					else
					{

						$reqQnty = "select a.id as id, sum(b.finish_fabric) as fabric_qty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b  where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id='$txt_booking_no_id' group by a.id";
						$reqQnty_res = sql_select($reqQnty);
						foreach($reqQnty_res as $row)
						{
							$req_qty_array[$row[csf("id")]]=$row[csf("fabric_qty")];
						}
					}

				}
				else if($receive_basis==9)
				{
					$prod_batch_booking_sql=sql_select("select booking_no from  pro_batch_create_mst where id='$txt_batch_id' and status_active=1 and is_deleted=0 group by booking_no");
					$batch_booking=$prod_batch_booking_sql[0][csf('booking_no')];
					if($batch_booking!='')
					{
						//$reqQnty = "select po_break_down_id, sum(fin_fab_qnty) as fabric_qty from wo_booking_dtls where status_active=1 and is_deleted=0 and booking_no='$batch_booking' group by po_break_down_id";

						$reqQnty="SELECT b.po_break_down_id,sum(b.fin_fab_qnty) as fabric_qty from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and c.lib_yarn_count_deter_id=$fabric_desc_id and b.booking_no='$batch_booking' and c.status_active=1 and c.is_deleted=0 and a.is_deleted = 0 and b.fabric_color_id=$fabricColorId
						and a.status_active = 1 and b.is_deleted = 0 and b.status_active = 1 group by b.po_break_down_id";
						//echo $reqQnty;
						$reqQnty_res = sql_select($reqQnty);
						// if(!empty($reqQnty_res)){
						foreach($reqQnty_res as $req_val)
						{
							$req_qty_array[$req_val[csf("po_break_down_id")]]=$req_val[csf("fabric_qty")];
						}
						// }else{

							// N.B: if fabrication changed in booking then Batch quantity is considered as fabric quantity
						$fabric_desc = $feb_construction.",".trim($feb_composition);
						$batch_qnty=sql_select("select b.po_id,sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b,product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.id=$txt_batch_id and a.color_id =$fabricColorId and c.detarmination_id=$fabric_desc_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 group by b.po_id");
						foreach($batch_qnty as $req_val)
						{
							$req_qty_batch_array[$req_val[csf("po_id")]]=$req_val[csf("batch_qnty")];
						}
						// }
					}
					/*if($db_type==0)
					{
						$all_batch_po_sql=sql_select("select group_concat(b.po_id) as po_id from  pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.booking_no='$batch_booking' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no");
					}
					else if($db_type==2)
					{
						$all_batch_po_sql=sql_select("select listagg(b.po_id,',') within group (order by b.po_id) as po_id from  pro_batch_create_mst a, pro_batch_create_dtls b where  a.id=b.mst_id and a.booking_no='$batch_booking' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no");
					}
					$batch_po_id=$all_batch_po_sql[0][csf('po_id')];*/

					$cumu_rec_qty=array();

					//$sql_cuml="select po_breakdown_id, sum(quantity) as qnty from order_wise_pro_details where po_breakdown_id in ($batch_po_id) and entry_form=37 and status_active=1 and is_deleted=0 group by po_breakdown_id";

					/*$sql_cuml=" select a.po_breakdown_id, sum(a.quantity) as qnty from order_wise_pro_details a,product_details_master b
					where a.prod_id=b.id and a.po_breakdown_id in ($batch_po_id)
					and a.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.detarmination_id=$fabric_desc_id group by a.po_breakdown_id ";*/

					$sql_cuml="select a.po_breakdown_id, sum(a.quantity) as qnty from pro_batch_create_mst c, pro_finish_fabric_rcv_dtls d,order_wise_pro_details a,product_details_master b
					where c.id=d.batch_id and d.id=a.dtls_id and a.prod_id=b.id and c.booking_no='$batch_booking' and c.color_id =$fabricColorId
					and a.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.detarmination_id=$fabric_desc_id group by a.po_breakdown_id ";

					$sql_result_cuml=sql_select($sql_cuml);

					foreach($sql_result_cuml as $row)
					{
						$cumu_rec_qty[$row[csf('po_breakdown_id')]]=$row[csf('qnty')];
					}

					$sql_cuml_batch="select a.po_breakdown_id, sum(a.quantity) as qnty from pro_batch_create_mst c, pro_finish_fabric_rcv_dtls d,order_wise_pro_details a, product_details_master b
					where c.id=d.batch_id and d.id=a.dtls_id and a.prod_id=b.id and c.booking_no='$batch_booking' and c.id=$txt_batch_id and c.color_id =$fabricColorId
					and a.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.detarmination_id=$fabric_desc_id group by a.po_breakdown_id ";

					$sql_result_cuml_batch=sql_select($sql_cuml_batch);

					foreach($sql_result_cuml_batch as $row)
					{
						$cumu_rec_with_batch_qty[$row[csf('po_breakdown_id')]]=$row[csf('qnty')];
					}
				}
				else if($receive_basis==10)
				{
					$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=$fabric_desc_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id asc";
					$data_array=sql_select($sql_deter);
					$feb_composition=$feb_construction="";
					foreach( $data_array as $row )
					{
						$feb_construction=$row[csf('construction')];
						if($feb_composition=="") $feb_composition=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%"; else $feb_composition.=" ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					}

					if($db_type==0)
					{
						$select_req = " (sum(ifnull(fin_fab_qnty,0)) + sum(ifnull(adjust_qty,0))) ";
					}
					else{
						$select_req = " (sum(nvl(fin_fab_qnty,0)) + sum(nvl(adjust_qty,0))) ";
					}

					$reqQnty = "select a.po_break_down_id, $select_req as fabric_qty FROM wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b WHERE a.booking_no ='$txt_sales_booking_no' and a.status_active=1 and a.pre_cost_fabric_cost_dtls_id=b.id and b.construction='$feb_construction' and b.composition ='$feb_composition' and a.is_deleted=0 and b.uom=$cbouom group by po_break_down_id";

					$nameArrayReqQnty=sql_select($reqQnty);
					if(empty($nameArrayReqQnty))
					{
						$reqQnty = "select a.po_break_down_id, $select_req as fabric_qty  FROM wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b WHERE a.booking_no ='$txt_sales_booking_no' and a.status_active=1 and a.pre_cost_fabric_cost_dtls_id=b.id and a.is_deleted=0 and b.uom=$cbouom group by po_break_down_id";

					}

					$reqQnty_res = sql_select($reqQnty);
					foreach($reqQnty_res as $req_val)
					{
						$req_qty_array[$req_val[csf('po_break_down_id')]]=$req_val[csf('fabric_qty')];
						$po_ids_arr[$req_val[csf('po_break_down_id')]] = $req_val[csf('po_break_down_id')];
					}

					$cumu_rec_qty=array();

					$po_id_cond = ($all_po_id!="")?" and po_breakdown_id in (".$all_po_id.")":"";
					$sql_cuml=" select a.po_breakdown_id,c.booking_id,b.id dtls_id,sum(a.quantity) as qnty from order_wise_pro_details a,pro_finish_fabric_rcv_dtls b,inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id $po_id_cond and a.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.fabric_description_id=$fabric_desc_id and b.body_part_id=$body_part_id $update_dtls_id_cond $uom_cond and b.color_id=$txt_color_id and b.fabric_shade=$txt_fabric_shade and b.batch_id=$txt_batch_id and b.gsm = '$txt_gsm' and b.width = '$txt_dia_width' group by a.po_breakdown_id,c.booking_id,b.id";
						//and a.is_sales=1

					$sql_result_cuml=sql_select($sql_cuml);
					foreach($sql_result_cuml as $row)
					{
						if($update_dtls_id!="" && $update_dtls_id==$row[csf('dtls_id')]){
							$this_challan_rec_qty[$row[csf('po_breakdown_id')]]+=$row[csf('qnty')];
						}else{
							$cumu_rec_qty[$row[csf('po_breakdown_id')]]+=$row[csf('qnty')];
							$delivery_wise_rcv_qnty[$row[csf('booking_id')]]+=$row[csf('qnty')];
						}
					}
					//}
				}
				else if($receive_basis==4 || $receive_basis==6)
				{
					$booking_cond = ($txt_po_booking_no!="")?" and a.booking_no='$txt_po_booking_no'":"";

					$reqQnty = "SELECT a.po_break_down_id, sum(a.fin_fab_qnty) as fabric_qty
					from wo_booking_dtls a,wo_pre_cost_fabric_cost_dtls b
					where a.pre_cost_fabric_cost_dtls_id=b.id and a.status_active=1 and a.is_deleted=0 and b.lib_yarn_count_deter_id = $fabric_desc_id and b.body_part_id=$body_part_id $booking_cond 
					group by a.po_break_down_id";

					$reqQnty_res = sql_select($reqQnty);
					foreach($reqQnty_res as $req_val)
					{

						$req_qty_array[$req_val[csf("po_break_down_id")]] += $req_val[csf("fabric_qty")];
						$po_ids_arr[$req_val[csf('po_break_down_id')]] = $req_val[csf('po_break_down_id')];
					}


					$cumu_rec_qty=array();

					$po_id_cond = ($all_po_id!="")?" and po_breakdown_id in (".$all_po_id.")":"";
					$sql_cuml=" select a.po_breakdown_id,c.booking_id,b.id dtls_id,sum(a.quantity) as qnty from order_wise_pro_details a, pro_finish_fabric_rcv_dtls b,inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id $po_id_cond and a.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.fabric_description_id=$fabric_desc_id and b.body_part_id=$body_part_id $uom_cond group by a.po_breakdown_id,c.booking_id,b.id";

					$sql_result_cuml=sql_select($sql_cuml);
					foreach($sql_result_cuml as $row)
					{
						if($update_dtls_id!="" && $update_dtls_id==$row[csf('dtls_id')]){
							$this_challan_rec_qty[$row[csf('po_breakdown_id')]]+=$row[csf('qnty')];
						}else{
							$cumu_rec_qty[$row[csf('po_breakdown_id')]]+=$row[csf('qnty')];
						}
					}
				}

				if($receive_basis==9)
				{
					$dispaly="";
				}
				else
				{
					$dispaly="style='display:none'";
				}

				if($receive_basis==1 || $receive_basis==4 || $receive_basis==6 || $receive_basis==9 || $receive_basis==10)
				{
					if($receive_basis == 10 && $style_wise_popup==1)
					{
						$delivery_onchange = "textile_delivery_distribute_qnty(document.getElementById('cbo_distribiution_method').value)";
					}
					else
					{
						$delivery_onchange = "distribute_qnty(document.getElementById('cbo_distribiution_method').value)";
					}
					?>
					<div id="search_div" style="margin-top:10px">
						<div style="width:<? echo $width; ?>px; margin-top:10px" align="center">
							<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300" align="center">
								<thead>
									<th>Total Receive Qnty</th>
									<th>Distribution Method</th>
								</thead>
								<tr class="general">
									<td>
										<input type="text" name="txt_prop_finish_qnty" id="txt_prop_finish_qnty" class="text_boxes_numeric" value="<? if($prev_distribution_method==1) echo $txt_production_qty; ?>" style="width:120px"  onBlur="<? echo $delivery_onchange;?>" <? echo $disabled; ?>/></td>
									<td>
										<?
										$distribiution_method=array(1=>"Proportionately",2=>"Manually");
										echo create_drop_down( "cbo_distribiution_method", 160, $distribiution_method,"",0,"--Select--",$prev_distribution_method,"distribute_qnty(this.value);",$disable_drop_down );

										?>
									</td>
								</tr>
							</table>
						</div>
						<div style="margin-left:10px; margin-top:10px">
							<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $width; ?>">
								<thead>
									<th width="80">Style ref. No </th>
									<th width="80">Job No</th>
									<th width="80">PO No</th>
									<?
									if($receive_basis==10)
									{
										?>
										<th width="80">Booking No</th>
										<?
									}else{
										?>
										<th width="80">File No</th>
										<th width="80">Ref No</th>
										<? } ?>
										<th width="80">Ship Date</th>
										<th width="80">PO Qty.</th>
										<th width="80">Req. Qty.</th>
										<?
										/* if($receive_basis==1 || $receive_basis==2 || $receive_basis==9 || $receive_basis==10 || $receive_basis==11)
										{
											?>
											<th width="80">Req. Qty.</th>
											<?
										} */

										if($receive_basis==1 || $receive_basis==4 || $receive_basis==6 || $receive_basis==9 || $receive_basis==10)
										{
											echo '<th width="80">Cumu. Receive Qty.</th>';
										}
										?>
										<th width="80">Finish Qty.</th>
										<th width="80">Reject Qty.</th>
										<th width="80">Fab Meter Qty.</th>
										<th width="80">Balance</th>
										<?
										/* if($receive_basis==1 || $receive_basis==2 || $receive_basis==9 || $receive_basis==10 || $receive_basis==11)
										{
											?>
											<th width="80">Balance</th>
											<?
										} */

										if($roll_maintained==1)
										{
											?>
											<th width="80">Roll</th>
											<th width="80">Barcode No.</th>
											<th width="80"></th>
											<?
										}
										?>
									</thead>
									<tbody id="tbl_list_search">
										<?
										$i=1; $tot_po_qnty=0; $po_array=array(); $po_data_array=array();
										if($roll_maintained==1)
										{
											if($save_data!="")
											{
												if($hidden_order_id!="")
												{
													$po_sql="select b.id,a.style_ref_no,b.job_no_mst,b.file_no,b.grouping as ref,b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date, a.buyer_name from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and b.id in ($hidden_order_id)";
													$nameArray=sql_select($po_sql);
													foreach($nameArray as $row)
													{
														$po_data_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
														$po_data_array[$row[csf('id')]]['job_no_mst']=$row[csf('job_no_mst')];
														$po_data_array[$row[csf('id')]]['po_no']=$row[csf('po_number')];
														$po_data_array[$row[csf('id')]]['file']=$row[csf('file_no')];
														$po_data_array[$row[csf('id')]]['ref']=$row[csf('ref')];
														$po_data_array[$row[csf('id')]]['qty']=$row[csf('po_qnty_in_pcs')];
														$po_data_array[$row[csf('id')]]['date']=$row[csf('pub_shipment_date')];
														$po_data_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
													}
												}

												$explSaveData = explode(",",$save_data);
												foreach($explSaveData as $val)
												{
													$order_data = explode("**",$val);
													$order_id=$order_data[0];
													$fin_qty=$order_data[1];
													$roll_no=$order_data[2];
													$roll_id=$order_data[3];
													$barcode_no=$order_data[4];
													$reject_qnty=$order_data[5];
													$fab_meter_qnty=$order_data[7];

													if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

													if(!(in_array($order_id,$po_array)))
													{
														$tot_po_qnty+=$po_data_array[$order_id]['qty'];
														$orginal_val=1;
														$po_array[]=$order_id;
													}
													else
													{
														$orginal_val=0;
													}
													?>
													<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
														<td width="80" align="center"><p><? echo $po_data_array[$order_id]['style_ref_no']; ?></p></td>
														<td width="80" align="center"><p><? echo $po_data_array[$order_id]['job_no_mst']; ?></p></td>
														<td width="80">
															<p><? echo $po_data_array[$order_id]['po_no']; ?></p>
															<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $order_id; ?>">
															<input type="hidden" name="txtPoNo[]" id="txtPoNo_<? echo $i; ?>" value="<? echo $po_data_array[$order_id]['po_no']; ?>">
															<input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" class="text_boxes" value="<? echo $po_data_array[$order_id]['buyer']; ?>">
															<input type="hidden" name="buyerName[]" id="buyerName_<? echo $i; ?>" class="text_boxes" value="<? echo $buyer_arr[$po_data_array[$order_id]['buyer']];?>">
															<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
															<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
														</td>
														<td width="80" align="center"><? echo $po_data_array[$order_id]['file']; ?></td>
														<td width="80" align="center"><? echo $po_data_array[$order_id]['ref']; ?></td>
														<td width="80" align="center"><p><? echo change_date_format($po_data_array[$order_id]['date']); ?></p></td>
														<td width="80" align="right">
															<? echo $po_data_array[$order_id]['qty']; ?>
															<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_data_array[$order_id]['qty']; ?>">
														</td>
														<?
														if($receive_basis==1)
														{
															if($pi_basis_id==2)
															{
																$req_qty=$pi_req_qty;
															}
															else {
																$req_qty=$req_qty_array[$order_id];
															}
														}
														else
														{

															$req_qty=$req_qty_array[$order_id];
														}

														if($receive_basis==1 || $receive_basis==2 || $receive_basis==9|| $receive_basis==11)
														{
															?>
															<td width="80" align="right">
																<? echo number_format($req_qty,2,'.',''); ?>
																<input type="hidden" name="txtreqqty[]" id="txtreqqty_<? echo $i; ?>" value="<? echo $req_qty; ?>">
															</td>
															<?
														}
														if($receive_basis==1 || $receive_basis==9 || $receive_basis==10)
														{
															?>
															<td width="80" align="right">
																<? echo number_format($cumu_rec_qty[$order_id],2,'.',''); $cumul_balance=$req_qty_array[$order_id]-$cumu_rec_qty[$order_id]; ?>
															</td>
															<?
														}
														?>
														<td align="center" width="80">
															<input type="text" name="txtfinishQnty[]" id="txtfinishQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" placeholder="<? echo number_format($cumul_balance,2,'.',''); ?>" value="<? echo $fin_qty; ?>"/>
														</td>
														<td align="center" width="80">
															<input type="text" name="txtrejectqnty[]" id="txtrejectqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:67px" value="<? echo $reject_qnty; ?>"/>
														</td>
														<td align="center" width="80">
															<input type="text" name="txtfabmeterqnty[]" id="txtfabmeterqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:67px" value="<? echo $fab_meter_qnty; ?>"/>
														</td>
														<?
														if($receive_basis==1 || $receive_basis==2 || $receive_basis==9|| $receive_basis==11)
														{
															?>
															<td align="center" width="80">
																<input type="text" name="txtbalanceqnty[]" id="txtbalanceqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" value="<? echo number_format($req_qty_array[$order_id]-$fin_qty,2,'.',''); ?>" disabled/>
															</td>
															<?
														}
														?>
														<td width="80" align="center">
															<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" value="<? if($roll_no!=0) echo $roll_no; ?>" <? echo $disable; ?> placeholder="<? echo $roll_arr[$order_id]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" />
														</td>
														<td width="80"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? echo $barcode_no; ?>" disabled/></td>
														<td width="80">
															<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:27px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
															<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:27px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
														</td>
													</tr>
													<?
													$i++;
												}
											}
											else
											{
												if($hidden_order_id!="")
												{
													$po_sql="select b.id,a.style_ref_no,b.job_no_mst,b.file_no,b.grouping as ref, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date, a.buyer_name from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and b.id in ($hidden_order_id)";
												}
												$po_data_array=array();
												$explSaveData = explode(",",$save_data);
												foreach($explSaveData as $val)
												{
													$finQnty = explode("**",$val);
													$po_data_array[$finQnty[0]]=$finQnty[1];
												}

												$nameArray=sql_select($po_sql);
												foreach($nameArray as $row)
												{
													if ($i%2==0)
														$bgcolor="#E9F3FF";
													else
														$bgcolor="#FFFFFF";

													$tot_po_qnty+=$row[csf('po_qnty_in_pcs')];
													$qnty = $po_data_array[$row[csf('id')]];
													$orginal_val=1;
													?>
													<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
														<td width="80" align="center"><? echo $row[csf('style_ref_no')]; ?></td>
														<td width="80" align="center"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
														<td width="80">
															<p><? echo $row[csf('po_number')]; ?></p>
															<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
															<input type="hidden" name="txtPoNo[]" id="txtPoNo_<? echo $i; ?>" value="<? echo $row[csf('po_number')]; ?>">
															<input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('buyer_name')]; ?>">
															<input type="hidden" name="buyerName[]" id="buyerName_<? echo $i; ?>" class="text_boxes" value="<? echo $buyer_arr[$row[csf('buyer_name')]];?>">
															<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
															<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
														</td>

														<td width="80" align="center"><? echo $row[csf('file_no')]; ?></td>
														<td width="80" align="center"><? echo $row[csf('ref')]; ?></td>
														<td width="80" align="center"><p><? echo change_date_format($row[csf('pub_shipment_date')]); ?></p></td>
														<td width="80" align="right">
															<? echo $row[csf('po_qnty_in_pcs')]; ?>
															<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $row[csf('po_qnty_in_pcs')]; ?>">
														</td>
														<?
														if($receive_basis==1)
														{
															if($pi_basis_id==2)
															{
																$req_qty=$pi_req_qty;
															}
															else {
																$req_qty=$req_qty_array[$row[csf('id')]];
															}
														}
														else
														{

															$req_qty=$req_qty_array[$row[csf('id')]];
														}

														if($receive_basis==1 || $receive_basis==2 || $receive_basis==9|| $receive_basis==11)
														{
															?>

															<td width="80" align="right">
																<? echo number_format($req_qty,2,'.',''); ?>
																<input type="hidden" name="txtreqqty[]" id="txtreqqty_<? echo $i; ?>" value="<? echo $req_qty; ?>">
															</td>
															<?
														}
														if($receive_basis==1 || $receive_basis==9 || $receive_basis==10)
														{
															?>

															<td width="80" align="right">
																<? echo number_format($cumu_rec_qty[$row[csf('id')]],2,'.',''); $cumul_balance=$req_qty_array[$row[csf('id')]]-$cumu_rec_qty[$row[csf('id')]]; ?>
															</td>
															<?
														}
														?>
														<td align="center" width="80">
															<input type="text" name="txtfinishQnty[]" id="txtfinishQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" placeholder="<? echo number_format($cumul_balance,2,'.',''); ?>" value="<? echo $qnty; ?>"/>
														</td>
														<td align="center" width="80">
															<input type="text" name="txtrejectqnty[]" id="txtrejectqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:67px" />
														</td>
														<td align="center" width="80">
															<input type="text" name="txtfabmeterqnty[]" id="txtfabmeterqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:67px" />
														</td>
														<?
														if($receive_basis==1 || $receive_basis==2 || $receive_basis==9|| $receive_basis==11)
														{
															?>
															<td align="center" width="80">
																<input type="text" name="txtbalanceqnty[]" id="txtbalanceqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" value="<? echo number_format($cumul_balance,2,'.','');//number_format($req_qty_array[$row[csf('id')]]-$qnty,2,'.',''); ?>" disabled/>
															</td>
															<?
														}
														?>
														<td width="80" align="center">
															<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" value="<? if($roll_no!=0) echo $roll_no; ?>" <? echo $disable; ?> placeholder="<? echo $roll_arr[$order_id]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" />
														</td>
														<td width="80"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? echo $barcode_no; ?>" disabled/></td>
														<td width="80">
															<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
															<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
														</td>
													</tr>
													<?
													$i++;
												}
											}
										}
										else
										{
											if($receive_basis==10)
											{
												if($update_dtls_id )
												{
													$up_cond = " and b.id<>$update_dtls_id";
												}
												$sql_job_ship_wise_rcv=sql_select("SELECT d.pub_shipment_date, e.id as job_id, sum(a.quantity) as qnty from order_wise_pro_details a,pro_finish_fabric_rcv_dtls b,inv_receive_master c, wo_po_break_down d, wo_po_details_master e where a.dtls_id=b.id and b.mst_id=c.id and a.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.fabric_description_id=$fabric_desc_id and b.body_part_id=$body_part_id and b.color_id=$txt_color_id and c.booking_id= $txt_booking_no_id and a.po_breakdown_id=d.id and d.job_id= e.id $up_cond group by d.pub_shipment_date, e.id");

												foreach ($sql_job_ship_wise_rcv as $value)
												{
													$job_wise_receive_qnty_arr[$value[csf('job_id')]][change_date_format($value[csf('pub_shipment_date')])]+=$value[csf('qnty')];
												}


												$sql_delivery_job=sql_select("SELECT job_id, pub_shipment_date, job_wise_qnty, dtls_id
												from order_wise_pro_details a, inv_finish_fabric_issue_dtls b, product_details_master c
												where a.dtls_id=b.id and b.prod_id=c.id and a.entry_form=224 and a.job_id is not null and a.is_sales=0 and b.mst_id=$txt_booking_no_id and c.detarmination_id=$fabric_desc_id and c.color=$txt_color_id and b.body_part_id=$body_part_id and b.status_active=1 and a.status_active=1 and job_wise_qnty !=0");


												foreach ($sql_delivery_job as $value)
												{
													$delivery_job[$value[csf("job_id")]] =$value[csf("job_id")];
													$job_wise_delivery_qnty_arr[$value[csf('job_id')]]+=$value[csf('job_wise_qnty')];
												}
												if(!empty($delivery_job)){
													$delivery_job_cond = " and a.id in (".implode(",",$delivery_job).")";
												}


												$job_no = "'".implode("','",explode(",", str_replace("'", "", $job_no)))."'";
												$po_sql="select b.id, a.id as job_id, a.style_ref_no,b.job_no_mst, b.file_no,b.grouping as ref,b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date, a.buyer_name from wo_po_details_master a, wo_po_break_down b,wo_booking_dtls c where a.job_no in ($job_no) and a.status_active!=0 and a.is_deleted!=1 and a.id=b.job_id and b.id=c.po_break_down_id and  b.status_active!=0 and b.is_deleted!=1 and c.booking_no='$txt_sales_booking_no' $delivery_job_cond group by b.id, a.id, a.style_ref_no,b.job_no_mst, b.file_no,b.grouping,b.po_number, a.total_set_qnty,b.po_quantity, b.pub_shipment_date, a.buyer_name order by b.job_no_mst, b.pub_shipment_date, a.style_ref_no asc";
												$nameArray=sql_select($po_sql);

												foreach($nameArray as $row)
												{
													$total_required_quantity[$row[csf('job_id')]] += $req_qty_array[$row[csf('id')]];
												}
											}
											else
											{
												$booking_cond = ($txt_po_booking_no!="")?" and c.booking_no='$txt_po_booking_no'":"";
												$hidden_order_id = explode("_", $hidden_order_id);

												if($receive_basis==1)
												{
													$po_sql="SELECT x.id, x.job_id, x.style_ref_no,x.job_no_mst, x.file_no,x.ref,x.po_number,x.po_qnty_in_pcs, x.pub_shipment_date, x.buyer_name
													from
													(
													select b.id, a.id as job_id, a.style_ref_no,b.job_no_mst, b.file_no,b.grouping as ref,b.po_number,(a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date, a.buyer_name
													from wo_po_details_master a, wo_po_break_down b,wo_booking_dtls c, wo_pre_cost_fabric_cost_dtls d
													where a.id=b.job_id and b.id=c.po_break_down_id and b.id in ($hidden_order_id[0]) and a.status_active!=0 and a.is_deleted!=1
													and b.status_active!=0 and b.is_deleted!=1 $booking_cond and c.pre_cost_fabric_cost_dtls_id=d.id and d.body_part_id=$body_part_id and c.booking_type =1
													group by b.id, a.id, a.style_ref_no,b.job_no_mst, b.file_no,b.grouping,b.po_number, a.total_set_qnty,b.po_quantity, b.pub_shipment_date, a.buyer_name
													union all
													select b.id, a.id as job_id, a.style_ref_no,b.job_no_mst, b.file_no,b.grouping as ref,b.po_number,(a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date, a.buyer_name
													from wo_po_details_master a, wo_po_break_down b,wo_booking_dtls c, wo_pre_cost_fab_conv_cost_dtls d, wo_pre_cost_fabric_cost_dtls e
													where a.id=b.job_id and b.id=c.po_break_down_id and b.id in ($hidden_order_id[0]) and a.status_active!=0 and a.is_deleted!=1
													and b.status_active!=0 and b.is_deleted!=1 $booking_cond and c.pre_cost_fabric_cost_dtls_id = d.id and d.fabric_description = e.id and e.body_part_id=$body_part_id and c.booking_type = 4
													group by b.id, a.id, a.style_ref_no,b.job_no_mst, b.file_no,b.grouping,b.po_number, a.total_set_qnty,b.po_quantity, b.pub_shipment_date, a.buyer_name
													) x
													group by  x.id, x.job_id, x.style_ref_no,x.job_no_mst, x.file_no,x.ref,x.po_number,x.po_qnty_in_pcs, x.pub_shipment_date, x.buyer_name";
												}
												else
												{
													$po_sql=" SELECT b.id, a.id as job_id, a.style_ref_no,b.job_no_mst, b.file_no,b.grouping as ref,b.po_number,(a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date, a.buyer_name
													from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c
													where a.id=b.job_id and b.id=c.po_break_down_id and b.id in ($hidden_order_id[0]) and a.status_active!=0 and a.is_deleted!=1 and b.status_active!=0 and b.is_deleted!=1 $booking_cond
													group by b.id, a.id, a.style_ref_no,b.job_no_mst, b.file_no,b.grouping,b.po_number, a.total_set_qnty,b.po_quantity, b.pub_shipment_date, a.buyer_name";
												}  

												$nameArray=sql_select($po_sql);
											}

											$po_data_array=array();
											$explSaveData = explode(",",$save_data);
											foreach($explSaveData as $val)
											{
												$finQnty = explode("**",$val);
												$po_data_array[$finQnty[0]]['qnty']=$finQnty[1];
												$po_data_array[$finQnty[0]]['reject_qnty']=$finQnty[5];
												$po_data_array[$finQnty[0]]['fab_meter_qnty']=$finQnty[7];
												$po_data_array[$finQnty[0]]['grey_qnty']=$finQnty[8];
											}

											foreach($nameArray as $row)
											{
												if ($i%2==0)
													$bgcolor="#E9F3FF";
												else
													$bgcolor="#FFFFFF";

												$tot_po_qnty+=$row[csf('po_qnty_in_pcs')];
												$qnty = $po_data_array[$row[csf('id')]]['qnty'];
												$reject_qnty = $po_data_array[$row[csf('id')]]['reject_qnty'];
												$fab_meter_qnty = $po_data_array[$row[csf('id')]]['fab_meter_qnty'];
												$grey_qnty = $po_data_array[$row[csf('id')]]['grey_qnty'];
												$orginal_val=1;
												?>
												<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
													<td width="80" align="center"><? echo $row[csf('style_ref_no')]; ?></td>
													<td width="80" align="center"><? echo $row[csf('job_no_mst')]; ?></td>
													<td width="80" title="<? echo $row[csf('id')]; ?>">
														<p><? echo $row[csf('po_number')]; ?></p>
														<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
														<input type="hidden" name="txtPoNo[]" id="txtPoNo_<? echo $i; ?>" value="<? echo $row[csf('po_number')]; ?>">
														<input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('buyer_name')]; ?>">
														<input type="hidden" name="buyerName[]" id="buyerName_<? echo $i; ?>" class="text_boxes" value="<? echo $buyer_arr[$row[csf('buyer_name')]];?>">
														<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
														<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
													</td>
													<?
													if($receive_basis==10)
													{
														?>
														<td width="80" align="center"><? echo $txt_sales_booking_no; ?></td>
														<?
													}
													else
													{
														?>
														<td width="80" align="center"><? echo $row[csf('file_no')]; ?></td>
														<td width="80" align="center"><? echo $row[csf('ref')]; ?></td>
														<?
													}
													?>
														<td width="80" align="center">
															<p>
																<? echo change_date_format($row[csf('pub_shipment_date')]); ?>
																<input type="hidden" name="txtPubShipDate[]" id="txtPubShipDate_<? echo $i; ?>" value="<? echo change_date_format($row[csf('pub_shipment_date')]); ?>">

																<?
																	$job_wise_del_qnty = $job_wise_delivery_qnty_arr[$row[csf('job_id')]][change_date_format($row[csf('pub_shipment_date')])];
																	$job_wise_rcv_qnty = $job_wise_receive_qnty_arr[$row[csf('job_id')]][change_date_format($row[csf('pub_shipment_date')])];
																?>

																<input type="hidden" name="txtJobId[]" id="txtJobId_<? echo $i; ?>" value="<? echo $row[csf('job_id')]; ?>">
																<input type="hidden" name="txtJobNo[]" id="txtJobNo_<? echo $i; ?>" value="<? echo $row[csf('job_no_mst')]; ?>">
															</p>
														</td>
														<td width="80" align="right">
															<? echo $row[csf('po_qnty_in_pcs')]; ?>
															<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $row[csf('po_qnty_in_pcs')]; ?>">
														</td>
														<?
														if($receive_basis==1)
														{
															if($pi_basis_id==2)
															{
																$req_qty=$pi_req_qty;
															}
															else
															{
																$req_qty = $req_qty_array[$row[csf('id')]] > 0 ? $req_qty_array[$row[csf('id')]] : $req_qty_batch_array[$row[csf('id')]];
															}
														}
														else
														{
															//$req_qty = $req_qty_array[$row[csf('id')]] > 0 ? $req_qty_array[$row[csf('id')]] : $req_qty_batch_array[$row[csf('id')]];

															if($req_qty_array[$row[csf('id')]] > 0)
															{
																$req_qty = $req_qty_array[$row[csf('id')]];
																$is_required_from_booking=1;
															}
															else
															{
																$req_qty = $req_qty_batch_array[$row[csf('id')]];
																$is_required_from_booking=2;
															}
														}

														$hidden_cummulative_rcv_qnty=0;
														$overRecvLimit= ($req_qty*$over_receive_limit)/100;

														$cummilativeReturnQnty=0;
														if($receive_basis==1)
														{
															$cummilativeReturnQnty=$cumu_return_qty[$row[csf('id')]];
														}

														//if($receive_basis==1 || $receive_basis==2 || $receive_basis==9 || $receive_basis==10 || $receive_basis==11)	{
															?>
															<td align="right" width="80" title="<? echo "Quantity with Over receive limit = ". number_format($overRecvLimit,2,'.',''); ?>">
																<? echo number_format($req_qty,2,'.',''); ?>
																<input type="hidden" name="txtreqqty[]" id="txtreqqty_<? echo $i; ?>" value="<? echo $req_qty; ?>"/>
															</td>
															<?
															$hidden_cummulative_rcv_qnty = number_format(($cumu_rec_qty[$row[csf('id')]]-$cummilativeReturnQnty),2,'.','');
														//}

														if($receive_basis==1 || $receive_basis==4 || $receive_basis==6 || $receive_basis==9 || $receive_basis==10)
														{
															$this_challan_rec = number_format($this_challan_rec_qty[$row[csf('id')]],2,".","");
															?>
															<td width="80" align="right" id="cumul_balance_td">
																<?
																//$cumul_balance=$req_qty_array[$row[csf('id')]]-$cumu_rec_qty[$row[csf('id')]];
																
																if($receive_basis==9)
																{
																	if($is_required_from_booking==1) 
																	{
																		// required comes from booking so receive quantity will validate with booking
																		$cummilative_receive_qnty= $cumu_rec_qty[$row[csf('id')]];

																	}else
																	{
																		// required comes from batch so receive quanity will validate with batch also  

																		$cummilative_receive_qnty= $cumu_rec_with_batch_qty[$row[csf('id')]];
																	}
																}
																else
																{
																	$cummilative_receive_qnty= $cumu_rec_qty[$row[csf('id')]];
																}

																//$cumul_balance=$req_qty_array[$row[csf('id')]]-$cumu_rec_qty[$row[csf('id')]];
																$cumul_balance=$req_qty-($cummilative_receive_qnty-$cummilativeReturnQnty);
																echo number_format(($cummilative_receive_qnty-$cummilativeReturnQnty),2,'.','');
																?>
																<input type="hidden" name="hdn_cumm_receive[]" id="hdn_cumm_receive_<? echo $i; ?>" value="<? echo $this_challan_rec; ?>"/>
															</td>
															<?
															$hidden_cummulative_rcv_qnty = number_format(($cummilative_receive_qnty-$cummilativeReturnQnty),2,'.','');
														}


														if($receive_basis==10)
														{
															$delivery_required = ($req_qty/$total_required_quantity[$row[csf('job_id')]]) * $job_wise_delivery_qnty_arr[$row[csf('job_id')]];
															//$delivery_required = number_format($delivery_required,2,".","");
															$total_delevery_required +=$delivery_required;

															$all_job_pono_id[$row[csf('job_id')]] .=$row[csf('id')].",";
														}

														//echo "(".$req_qty."   /   ".$total_required_quantity[$row[csf('job_id')]] ." ) *  ". $job_wise_delivery_qnty_arr[$row[csf('job_id')]]."  =  ".$delivery_required."<br>";
														?>


														<td align="center" width="80">
															<input type="text" name="txtfinishQnty[]" id="txtfinishQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" placeholder="<? echo number_format($cumul_balance,2,'.',''); ?>" value="<? echo $qnty; ?>" onChange="fn_check_balance(<? echo $i; ?>)"/>

															<input type="hidden" name="hidden_cummulative_rcv_qnty[]" id="hidden_cummulative_rcv_qnty_<? echo $i; ?>" value="<? echo $hidden_cummulative_rcv_qnty;// - $qnty; ?>" />
															<input type="hidden" name="txtJobShipDateQnty[]" id="txtJobShipDateQnty_<? echo $row[csf('id')]; ?>" value="<? echo $delivery_required; ?>">
															<input type="hidden" name="txtJobWiseTotalDeliveryQnty[]" id="txtJobWiseTotalDeliveryQnty<? echo $row[csf('job_id')]; ?>" value="<? echo $job_wise_delivery_qnty_arr[$row[csf('job_id')]]; ?>">
														</td>
														<td align="center" width="80">

															<input type="text" name="txtrejectqnty[]" id="txtrejectqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px"  value="<? echo $reject_qnty; ?>"/>
														</td>
														<td align="center" width="80">

															<input type="text" name="txtfabmeterqnty[]" id="txtfabmeterqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px"  value="<? echo $fab_meter_qnty; ?>"/>
														</td>
														<?
														//if($receive_basis==1 || $receive_basis==2 || $receive_basis==9 || $receive_basis==10 || $receive_basis==11)
														//{
															?>
															<td align="center" width="80">
																<input type="text" name="txtbalanceqnty[]" id="txtbalanceqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px"  value="<? echo number_format($cumul_balance,2,'.','');  ?>"/>
															</td>
															<?
														//}
													?>
												</tr>
													<?
													$i++;
													$tot_req_qnty+=$req_qty;
											}
										}

											if(!empty($all_job_pono_id))
											{
												//print_r($all_job_pono_id);
												$job_po_string="";
												foreach ($all_job_pono_id as $job => $po) {
													$job_po_string .= $job."**".chop($po,",")."__";
												}

												$job_po_string = chop($job_po_string,"__");
												//echo $job_po_string ;
											}

											?>
											<input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes" value="<? echo $tot_po_qnty; ?>">
											<input type="hidden" name="tot_req_qnty" id="tot_req_qnty" class="text_boxes" value="<? echo $tot_req_qnty; ?>">
											<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="<? echo $i-1; ?>">
											<input type="hidden" name="hidden_cummu_deli_rcv_qnty" id="hidden_cummu_deli_rcv_qnty" value="<? echo $delivery_wise_rcv_qnty[$txt_booking_no_id]; ?>" />

											<input type="hidden" name="tot_delivery_req_qnty" id="tot_delivery_req_qnty" value="<? echo $total_delevery_required; ?>" />
											<input type="hidden" name="job_po_string" id="job_po_string" value="<? echo $job_po_string; ?>" />
									</tbody>
							</table>
						</div>
						<table width="<? echo $width; ?>" id="table_id">
							<tr>
								<td align="center" >
									<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
								</td>
							</tr>
						</table>
					</div>
					</div>
					<?
				}
				else
				{
					?>
					<div style="width:<? echo $width; ?>px; margin-top:10px" align="center">
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300" align="center">
							<thead>
								<th>Total Receive Qnty</th>
								<th>Distribution Method </th>
								<?
								if($process_costing_maintain == 0 && $receive_basis==11)
								{
								?>
								<th>Total Grey Qnty</th>
								<th>Distribution Method </th>
								<?
								}
								?>
							</thead>
							<tr class="general">
							<?
								if($receive_basis==2)
								{
									//N.B in WO basis qnty distributed by lowest shipment date.
								?>
								<td><input type="text" name="txt_prop_finish_qnty" id="txt_prop_finish_qnty" class="text_boxes_numeric" value="<? if($prev_distribution_method==1) echo $txt_production_qty; ?>" style="width:120px" onBlur="wo_distribute_qnty(document.getElementById('cbo_distribiution_method').value)" <? echo $disabled; ?> /></td>
								<td>
									<?
									$distribiution_method=array(1=>"Distribute by lowest shipment date",2=>"Manually");
									echo create_drop_down("cbo_distribiution_method", 160, $distribiution_method,"",0,"--Select--",$prev_distribution_method,"wo_distribute_qnty(this.value);",$disable_drop_down);
									?>
								</td>
								<?
								}
								else
								{
									?>
									<td><input type="text" name="txt_prop_finish_qnty" id="txt_prop_finish_qnty" class="text_boxes_numeric" value="<? if($prev_distribution_method==1) echo $txt_production_qty; ?>" style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)" <? echo $disabled; ?> /></td>
									<td>
										<?
										$distribiution_method=array(1=>"Proportionately",2=>"Manually");
										echo create_drop_down("cbo_distribiution_method", 160, $distribiution_method,"",0,"--Select--",$prev_distribution_method,"distribute_qnty(this.value);",$disable_drop_down);
										?>
									</td>
									<?
								}

								if($process_costing_maintain == 0 && $receive_basis==11)
								{
								?>
								<td><input type="text" name="txt_prop_grey_qnty" id="txt_prop_grey_qnty" class="text_boxes_numeric" value="<? //if($prev_distribution_method==1) echo $txt_production_qty; ?>" style="width:120px" onBlur="grey_distribute_qnty(document.getElementById('cbo_distribiution_method_grey').value)" <? echo $disabled; ?> /></td>
								<td>
									<?
									$distribiution_method=array(1=>"Proportionately",2=>"Manually");
									echo create_drop_down("cbo_distribiution_method_grey", 160, $distribiution_method,"",0,"--Select--",$prev_distribution_method,"grey_distribute_qnty(this.value);",$disable_drop_down);
									?>
								</td>
								<?
								}
								?>
							</tr>
						</table>
					</div>
					<div style="margin-left:10px; margin-top:10px; width: 1380px<? //echo $width+20;?>">
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="1360 <? //echo $width; ?>">
							<thead>
								<?
								if($receive_basis==11 && $booking_without_order==1)
								{
									?>
									<th width="80">Style Ref </th>
									<th width="80">Job No</th>
									<th width="80">Booking No</th>
									<th width="80">File No</th>
									<th width="80">Ref. No</th>
									<th width="80">Booking Date</th>
									<th width="80">Booking Qty.</th>
									<th width="80">Req. Qty.</th>
									<?
								}
								else
								{
									?>
									<th width="80">Style ref. No </th>
									<th width="80">Job No</th>
									<th width="80">PO No</th>
									<?
									if($receive_basis!=14)
									{
										?>
										<th width="80">File No</th>
										<th width="80">Ref. No</th>
										<?
									}
									?>
									<th width="80">Shipment Date</th>
									<th width="80">PO Qty.</th>
									<?
									if($receive_basis==2 || $receive_basis==14 || $receive_basis==11)
									{
										?>
										<th width="80">Req. Qty.</th>
										<?
									}
								}
								?>
								<th width="80">Cumu. Recv. Qty</th>
								<th width="80">Finish Qnty</th>
								<?
									if($process_costing_maintain == 0 && $receive_basis==11)
									{
										?>
										<th width="80">Grey Qnty</th>
										<?
									}
								?>
								<th width="80">Reject Qty.</th>
								<th width="80">Fab Meter Qty.</th>
								<?
								if($receive_basis==2 || $receive_basis==14 || $receive_basis==11)
								{
									?>
									<th width="80">Balance</th>
									<?
								}
								if($roll_maintained==1)
								{
									?>
									<th width="80">Roll</th>
									<th width="80">Barcode No.</th>
									<th width="80"></th>
									<?
								}
								?>
							</thead>
						</table>
						<div style="width: 1360<? //echo $width+20; ?>px; max-height:280px; overflow-y:scroll" id="list_container" align="left">
							<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="1360<? //echo $width; ?>" id="tbl_list_search">
								<?
								$i=1; $tot_po_qnty=0; $po_data_array=array(); $cumu_rec_qty=array(); $po_array=array();

								if($receive_basis==11 && $booking_without_order==1)
								{
									$sql_cuml="select po_breakdown_id, sum(qnty) as qnty from pro_roll_details where entry_form=37 and status_active=1 and is_deleted=0 and booking_without_order=1 group by po_breakdown_id";

									$sql_result_cuml=sql_select($sql_cuml);
									foreach($sql_result_cuml as $row)
									{
										$cumu_rec_qty[$row[csf('po_breakdown_id')]]=$row[csf('qnty')];
									}
								}
								else
								{
									if(!empty($po_ids_arr))
									{
										if($receive_basis==11 || $receive_basis==2){$booking_cond = " and c.booking_no='$txt_booking_no'";}else{}
										$uom_cond = ($cbouom!="")?"and b.uom=$cbouom":"";

										if($txt_original_dia_width )
										{
											$original_dia_cond = " and b.original_width= '$txt_original_dia_width'";
										}
										else
										{
											if($db_type==2)
											{
												$original_dia_cond =" and b.original_width is null";
											}
											else
											{
												$original_dia_cond =" and b.original_width=''";
											}
										}

										$sql_cuml=" select a.po_breakdown_id,b.id dtls_id,b.pre_cost_fabric_cost_dtls_id,sum(a.quantity) as qnty, sum(a.grey_used_qty) as grey_used_qty, b.prod_id from order_wise_pro_details a,pro_finish_fabric_rcv_dtls b,inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id and po_breakdown_id in (".implode(",",$po_ids_arr).") and a.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.fabric_description_id=$fabric_desc_id and b.body_part_id=$body_part_id $booking_cond $uom_cond and b.color_id=$fabricColorId and b.gsm=$txt_gsm $original_dia_cond group by a.po_breakdown_id,b.id,b.pre_cost_fabric_cost_dtls_id, b.prod_id";

										//N.B here in receive query gsm and original dia is added and these prod_id will be in receive return condition as because receive return does not have original dia or pre_cost_fabric_cost_id

										$sql_result_cuml=sql_select($sql_cuml);
										foreach($sql_result_cuml as $row)
										{
											if($update_dtls_id!="" && $update_dtls_id==$row[csf('dtls_id')]){

												$this_challan_rec_qty[$row[csf('po_breakdown_id')]][$row[csf('pre_cost_fabric_cost_dtls_id')]] += $row[csf('qnty')];
											}else{
												$cumu_rec_qty[$row[csf('po_breakdown_id')]][$row[csf('pre_cost_fabric_cost_dtls_id')]] += $row[csf('qnty')];
												$cumu_grey_used_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('pre_cost_fabric_cost_dtls_id')]] += $row[csf('grey_used_qty')];
											}

											$rcv_prod_id_for_return[$row[csf('prod_id')]]=$row[csf('prod_id')];
										}

										if($receive_basis==11 || $receive_basis==2)
										{
											/* $sql_return="select a.po_breakdown_id, sum(a.quantity) as qnty from order_wise_pro_details a, inv_finish_fabric_issue_dtls b, product_details_master c, inv_issue_master d, inv_receive_master e where a.entry_form =46 and a.dtls_id=b.id and b.prod_id=c.id and e.booking_no = '$txt_booking_no' and c.color =$fabricColorId and c.detarmination_id =$fabric_desc_id $uom_cond and a.po_breakdown_id in (".implode(",",$po_ids_arr).") and b.body_part_id =$body_part_id and b.mst_id = d.id and d.received_id = e.id and e.entry_form = 37 group by a.po_breakdown_id"; */

											$sql_return="select a.po_breakdown_id, sum(a.quantity) as qnty from order_wise_pro_details a, inv_finish_fabric_issue_dtls b, product_details_master c, inv_issue_master d, inv_receive_master e where a.entry_form =46 and a.dtls_id=b.id and b.prod_id=c.id and e.booking_no = '$txt_booking_no' and c.color =$fabricColorId and c.detarmination_id =$fabric_desc_id $uom_cond and a.po_breakdown_id in (".implode(",",$po_ids_arr).") and b.body_part_id =$body_part_id and b.mst_id = d.id and d.received_id = e.id and e.entry_form = 37 and c.gsm=$txt_gsm  and c.id in (". implode(',',$rcv_prod_id_for_return).") group by a.po_breakdown_id";

											$sql_result_return=sql_select($sql_return);
											foreach($sql_result_return as $row)
											{
												$cumu_return_qty[$row[csf('po_breakdown_id')]] += $row[csf('qnty')];
											}

										}
									}
								}

								if($roll_maintained==1)
								{
									if($save_data!="")
									{
										if($receive_basis==11 && $booking_without_order==1)
										{
											$po_sql="select 0 as buyer_name, a.id as book_id, a.fab_booking_id as id, null as file_no, null as ref, a.booking_no as po_number, a.booking_date as pub_shipment_date, 1 as total_set_qnty, sum(b.wo_qty) as po_quantity
											from wo_non_ord_knitdye_booking_mst a, wo_non_ord_knitdye_booking_dtl b
											where a.id=b.mst_id and a.booking_no='$txt_booking_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
											group by a.id, a.fab_booking_id, a.booking_no, a.booking_date order by a.id";
										}
										else
										{

											if($receive_basis==14)
											{
												$within_group=return_field_value("within_group","fabric_sales_order_mst","job_no='$txt_booking_no'");
												if($within_group == 2)
												{
													$po_sql="select a.id,a.job_no po_number,a.buyer_id buyer_name, a.within_group,a.style_ref_no,a.delivery_date,b.fabric_desc,b.grey_qty req_quantity from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.job_no='$txt_booking_no' and b.determination_id=$fabric_desc_id";//and b.id=$fso_dtls_id
												}
												else
												{
													$booking_cond = " and c.booking_no='$txt_sales_booking_no'";
													$po_sql="select a.buyer_name,a.style_ref_no,b.job_no_mst,b.id,b.file_no,b.grouping as ref, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date from wo_po_details_master a, wo_booking_dtls c, wo_po_break_down b where a.id=b.job_id and b.id=c.po_break_down_id $booking_cond group by b.id,b.file_no,b.grouping, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date, a.buyer_name,a.style_ref_no,b.job_no_mst";
												}
											}
											else
											{
												$booking_cond = " and c.booking_no='$txt_booking_no'";

												$po_sql="select a.buyer_name,a.style_ref_no,b.job_no_mst,b.id,b.file_no,b.grouping as ref, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date,c.pre_cost_fabric_cost_dtls_id,c.fabric_color_id from wo_po_details_master a, wo_booking_dtls c, wo_po_break_down b where a.id=b.job_id and b.id=c.po_break_down_id $booking_cond and c.pre_cost_fabric_cost_dtls_id=$pre_cost_fab_conv_cost_dtls_id group by b.id,b.file_no,b.grouping,b.po_number,a.total_set_qnty,b.po_quantity, b.pub_shipment_date,a.buyer_name,a.style_ref_no,b.job_no_mst,c.pre_cost_fabric_cost_dtls_id,c.fabric_color_id";
											}
										}

										$nameArray=sql_select($po_sql);
										foreach($nameArray as $row)
										{
											$po_data_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
											$po_data_array[$row[csf('id')]]['job_no_mst']=$row[csf('job_no_mst')];
											$po_data_array[$row[csf('id')]]['po_no']=$row[csf('po_number')];
											$po_data_array[$row[csf('id')]]['file']=$row[csf('file_no')];
											$po_data_array[$row[csf('id')]]['ref']=$row[csf('ref')];
											$po_data_array[$row[csf('id')]]['qty']=$row[csf('total_set_qnty')]*$row[csf('po_quantity')];
											$po_data_array[$row[csf('id')]]['date']=$row[csf('pub_shipment_date')];
											$po_data_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
										}

										$explSaveData = explode(",",$save_data);
										foreach($explSaveData as $val)
										{
											$order_data = explode("**",$val);
											$order_id=$order_data[0];
											$fin_qty=$order_data[1];
											$roll_no=$order_data[2];
											$roll_id=$order_data[3];
											$barcode_no=$order_data[4];
											$reject_qnty=$order_data[5];
											$fab_meter_qnty=$order_data[7];

											if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

											if(!(in_array($order_id,$po_array)))
											{
												$tot_po_qnty+=$po_data_array[$order_id]['qty'];
												$orginal_val=1;
												$po_array[]=$order_id;
											}
											else
											{
												$orginal_val=0;
											}


											?>
											<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
												<td width="80" align="center"><? echo $po_data_array[$order_id]['style_ref_no']; ?></td>
												<td width="80" align="center"><? echo $po_data_array[$order_id]['job_no_mst']; ?></td>
												<td width="80" align="center">
													<p><? echo $po_data_array[$order_id]['po_no']; ?></p>
													<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $order_id; ?>">
													<input type="hidden" name="txtPoNo[]" id="txtPoNo_<? echo $i; ?>" value="<? echo $po_data_array[$order_id]['po_no']; ?>">
													<input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" class="text_boxes" value="<? echo $po_data_array[$order_id]['buyer']; ?>">
													<input type="hidden" name="buyerName[]" id="buyerName_<? echo $i; ?>" class="text_boxes" value="<? echo $buyer_arr[$po_data_array[$order_id]['buyer']];?>">
													<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
													<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
												</td>
												<?
												if($receive_basis!=14)
												{
													?>
													<td width="80" align="center"><? echo $row[csf('file_no')]; ?></td>
													<td width="80" align="center"><? echo $row[csf('ref')];  ?></td>
													<?
												}
												?>
												<td width="80" align="center"><p><? echo change_date_format($po_data_array[$order_id]['date']); ?></p></td>
												<td width="80" align="right">
													<? echo $po_data_array[$order_id]['qty']; ?>
													<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_data_array[$order_id]['qty']; ?>">
												</td>
												<?
												if($receive_basis==1 || $receive_basis==2 || $receive_basis==9 || $receive_basis==11 || $receive_basis==14)
												{
													$cumul_balance = $req_qty_array[$order_id][$pre_cost_fab_conv_cost_dtls_id]-$cumu_rec_qty[$order_id][$pre_cost_fab_conv_cost_dtls_id][$fabricColorId];
													?>
													<td width="80" align="right"><? echo number_format($req_qty_array[$order_id][$pre_cost_fab_conv_cost_dtls_id],2,'.',''); ?>
														<input type="hidden" name="txtreqqty[]" id="txtreqqty_<? echo $i; ?>" value="<? echo $req_qty_array[$order_id][$pre_cost_fab_conv_cost_dtls_id]; ?>">
													</td>
													<?
												}
												?>
												<td width="80" align="right"><? echo number_format($cumu_rec_qty[$order_id][$pre_cost_fab_conv_cost_dtls_id][$fabricColorId],2,'.',''); ?></td>
												<td align="center" width="80">&nbsp;
													<input type="text" name="txtfinishQnty[]" id="txtfinishQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" placeholder="<? echo number_format($cumul_balance,2,'.',''); ?>" value="<? echo $fin_qty; ?>"/>
												</td>
												<? //echo "aaaaaaaaaaaaaa".$receive_basis; ?>
												<? if($receive_basis==11 && $process_costing_maintain==0){ ?>

													<td align="center" width="80"></td>
												<?
												} 
												?>
												
												<td align="center" width="80">&nbsp;
													<input type="text" name="txtrejectqnty[]" id="txtrejectqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" value="<? echo $reject_qnty; ?>"/>
												</td>
												<td align="center" width="80">&nbsp;
													<input type="text" name="txtfabmeterqnty[]" id="txtfabmeterqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" value="<? echo $fab_meter_qnty; ?>"/>
												</td>
												<?
												if($receive_basis==1 || $receive_basis==2 || $receive_basis==9|| $receive_basis==11 || $receive_basis==14)
												{
													if($receive_basis==14)
													{
														$balance_qnty = ($within_group == 1)?($req_qty_array[$order_id][$fabric_desc_id]-$fin_qty):($row[csf('req_quantity')]-$fin_qty);
													}
													else
													{
														$balance_qnty = $req_qty_array[$order_id][$pre_cost_fab_conv_cost_dtls_id]-$cumu_rec_qty[$order_id][$pre_cost_fab_conv_cost_dtls_id][$fabricColorId];
													}
													?>
													<td align="center" width="80">
														<input type="text" name="txtbalanceqnty[]" id="txtbalanceqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" value="<? echo number_format($balance_qnty,2,'.',''); ?>"/>
													</td>
													<?
												}
												?>
												<td width="80" align="center">
													<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" value="<? if($roll_no!=0) echo $roll_no; ?>" <? echo $disable; ?> placeholder="<? echo $roll_arr[$order_id]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" />
												</td>
												<td width="80"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? echo $barcode_no; ?>" disabled/></td>
												<td width="80">
													<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
													<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
												</td>
											</tr>
											<?
											$i++;
										}

										foreach($po_data_array as $order_id=>$val)
										{
											if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											$orginal_val=1;
											if(!(in_array($order_id,$po_array)))
											{
												$tot_po_qnty+=$val['qty'];
												$orginal_val=1;
												?>
												<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
													<td width="80" align="center"><? echo $row[csf('style_ref_no')]; ?></td>
													<td width="80" align="center"><? echo $row[csf('job_no_mst')]; ?></td>
													<td width="80" align="center">
														<p><? echo $po_data_array[$order_id]['po_no']; ?></p>
														<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $order_id; ?>">
														<input type="hidden" name="txtPoNo[]" id="txtPoNo_<? echo $i; ?>" value="<? echo $val['po_no']; ?>">
														<input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" class="text_boxes" value="<? echo $val['buyer']; ?>">
														<input type="hidden" name="buyerName[]" id="buyerName_<? echo $i; ?>" class="text_boxes" value="<? echo $buyer_arr[$val['buyer']];?>">
														<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
														<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
													</td>
													<td width="80" align="center"><? echo $po_data_array[$order_id]['file'] ?></td>
													<td width="80" align="center"><? echo $po_data_array[$order_id]['ref']; ?></td>
													<td width="80" align="center"><p><? echo change_date_format($val['date']); ?></p></td>
													<td width="80" align="right">
														<? echo $val['qty']; ?>
														<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $val['qty']; ?>">
													</td>
													<?
													if($receive_basis==1 || $receive_basis==2 || $receive_basis==9|| $receive_basis==11)
													{
														?>
														<td width="80" align="right"><? echo number_format($req_qty_array[$order_id],2,'.',''); ?>
															<input type="hidden" name="txtreqqty[]" id="txtreqqty_<? echo $i; ?>" value="<? echo $req_qty_array[$order_id]; ?>">
														</td>
														<?
													}
													?>
													<td width="80" align="right"><? echo number_format($cumu_rec_qty[$order_id],2,'.',''); ?></td>
													<td align="center">
														<input type="text" name="txtfinishQnty[]" id="txtfinishQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" placeholder="<? echo number_format($cumul_balance,2,'.',''); ?>" value="<? echo $fin_qty; ?>"/>
													</td>
													<td align="center" width="80">
														<input type="text" name="txtrejectqnty[]" id="txtrejectqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:67px" />
													</td>
													
													<?
													if($receive_basis==1 || $receive_basis==2 || $receive_basis==9|| $receive_basis==11)
													{
														?>
														<td align="center" width="80">&nbsp;
															<input type="text" name="txtfabmeterqnty[]" id="txtfabmeterqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" value="<? echo $fab_meter_qnty; ?>"/>
														</td>
														<td align="center" width="80">
															<input type="text" name="txtbalanceqnty[]" id="txtbalanceqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" value="<? echo number_format($req_qty_array[$order_id]-$fin_qty,2,'.',''); ?>" />
														</td>
														<?
													}
													?>


													<td width="80" align="center">
														<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" value="<? if($roll_no!=0) echo $roll_no; ?>" <? echo $disable; ?> placeholder="<? echo $roll_arr[$order_id]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" />
													</td>
													<td width="80"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? echo $barcode_no; ?>" disabled/></td>
													<td width="80">
														<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
														<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
													</td>
												</tr>
												<?
												$i++;
											}
										}
									}
									else
									{

										if($receive_basis==11 && $booking_without_order==1)
										{
											$po_sql="select 0 as buyer_name, a.id as book_id, a.fab_booking_id as id, null as file_no, null as ref, a.booking_no as po_number, a.booking_date as pub_shipment_date, 1 as total_set_qnty, sum(b.wo_qty) as po_quantity
											from wo_non_ord_knitdye_booking_mst a, wo_non_ord_knitdye_booking_dtl b
											where a.id=b.mst_id and a.booking_no='$txt_booking_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
											group by a.id, a.fab_booking_id, a.booking_no, a.booking_date order by a.id";
										}
										else
										{
											if($receive_basis==14)
											{

												$po_sql = "select a.id, a.style_ref_no,a.delivery_date pub_shipment_date,a.job_no po_number, a.within_group,a.job_no_prefix_num job_no_mst, b.body_part_id, b.color_type_id, b.determination_id, b.gsm_weight, dia as dia_width, b.width_dia_type as dia_width_type, b.color_id, b.color_range_id, b.finish_qty, b.grey_qty, b.pre_cost_fabric_cost_dtls_id, b.grey_qnty_by_uom qnty from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and b.determination_id=$fabric_desc_id and b.body_part_id=$body_part_id and b.color_id=$fabricColorId and b.status_active=1 and b.is_deleted=0";

											}else
											{
												$booking_cond = " and c.booking_no='$txt_booking_no'";
												$po_sql="select a.buyer_name,a.style_ref_no,b.job_no_mst,b.id,b.file_no,b.grouping as ref, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date,c.pre_cost_fabric_cost_dtls_id,c.fabric_color_id from wo_po_details_master a, wo_booking_dtls c, wo_po_break_down b where a.id=b.job_id and b.id=c.po_break_down_id and c.fabric_color_id =$fabricColorId and c.pre_cost_fabric_cost_dtls_id=$pre_cost_fab_conv_cost_dtls_id $booking_cond  group by b.id,b.file_no,b.grouping,b.po_number,a.total_set_qnty,b.po_quantity, b.pub_shipment_date,a.buyer_name,a.style_ref_no,b.job_no_mst,c.pre_cost_fabric_cost_dtls_id,c.fabric_color_id";

											}

										}

										$explSaveData = explode(",",$save_data);
										foreach($explSaveData as $val)
										{
											$finQnty = explode("**",$val);
											$po_data_array[$finQnty[0]]['qnty']=$finQnty[1];
											$po_data_array[$finQnty[0]]['reject_qnty']=$finQnty[5];
											$po_data_array[$finQnty[0]]['fab_meter_qnty']=$finQnty[7];
										}

										$nameArray=sql_select($po_sql);

										foreach($nameArray as $row)
										{
											if ($i%2==0)
												$bgcolor="#E9F3FF";
											else
												$bgcolor="#FFFFFF";

											$po_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
											$tot_po_qnty+=$po_qnty_in_pcs;
											$qnty = $po_data_array[$row[csf('id')]]['qnty'];
											$reject_qnty = $po_data_array[$row[csf('id')]]['reject_qnty'];
											$fab_meter_qnty = $po_data_array[$row[csf('id')]]['fab_meter_qnty'];
											if($receive_basis==14)
											{
												$cumu_balance = ($within_group == 1)?($cumu_balance = $req_qty_array[$row[csf('id')]][$fabric_desc_id]-$cumu_rec_qty[$row[csf('id')]]):($row[csf('req_quantity')]-$cumu_rec_qty[$row[csf('id')]]);
											}
											else
											{
												$cumu_balance = $req_qty_array[$row[csf('id')]][$pre_cost_fab_conv_cost_dtls_id]-$cumu_rec_qty[$row[csf('id')]][$pre_cost_fab_conv_cost_dtls_id][$fabricColorId];
											}

											?>
											<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
												<td width="80" align="center"><? echo $row[csf('style_ref_no')]; ?></td>
												<td width="80" align="center"><? echo $row[csf('job_no_mst')]; ?></td>
												<td width="80" align="center">
													<p><? echo $row[csf('po_number')]; ?>FS</p>
													<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
													<input type="hidden" name="txtPoNo[]" id="txtPoNo_<? echo $i; ?>" value="<? echo $row[csf('po_number')]; ?>">
													<input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('buyer_name')]; ?>">
													<input type="hidden" name="buyerName[]" id="buyerName_<? echo $i; ?>" class="text_boxes" value="<? echo $buyer_arr[$row[csf('buyer_name')]];?>">

													<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
													<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
												</td>
												<td width="80" align="center"><? echo $row[csf('file_no')]; ?></td>
												<td width="80" align="center"><? echo $row[csf('ref')]; ?></td>
												<td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
												<td width="80" align="right">
													<? echo $po_qnty_in_pcs; ?>
													<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_qnty_in_pcs; ?>">
												</td>
												<?
												if($receive_basis==1 || $receive_basis==2 || $receive_basis==9 || $receive_basis==11)
												{
													?>
													<td width="80" align="right"><? echo number_format($req_qty_array[$row[csf('id')]][$pre_cost_fab_conv_cost_dtls_id],2,'.',''); ?>
														<input type="hidden" name="txtreqqty[]" id="txtreqqty_<? echo $i; ?>" value="<? echo $req_qty_array[$row[csf('id')]][$pre_cost_fab_conv_cost_dtls_id]; ?>">
													</td>
													<?
												}
												?>
												<td width="80" align="right"><? echo number_format($cumu_rec_qty[$row[csf('id')]][$pre_cost_fab_conv_cost_dtls_id][$fabricColorId],2,'.',''); ?></td>
												<td align="center" width="80">
													<input type="text" name="txtfinishQnty[]" id="txtfinishQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" placeholder="<? echo number_format($cumu_balance,2,'.',''); ?>" value="<? echo $qnty;?>"/>
												</td>
												<td align="center" width="80">
													<input type="text" name="txtrejectqnty[]" id="txtrejectqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:67px"  value="<? echo $reject_qnty;?>" />
												</td>
												<td align="center" width="80">
													<input type="text" name="txtfabmeterqnty[]" id="txtfabmeterqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:67px"  value="<? echo $fab_meter_qnty;?>" />
												</td>
												<?
												if($receive_basis==1 || $receive_basis==2 || $receive_basis==9|| $receive_basis==11 || $receive_basis==14)
												{
													if($receive_basis==14)
													{
														$balance_qnty = ($within_group == 1)?($req_qty_array[$row[csf('id')]][$fabric_desc_id]-$qnty):($row[csf('req_quantity')]-$qnty);
													}else{
														$balance_qnty = $req_qty_array[$row[csf('id')]][$pre_cost_fab_conv_cost_dtls_id]-$cumu_rec_qty[$row[csf('id')]][$pre_cost_fab_conv_cost_dtls_id][$fabricColorId];
													}
													?>
													<td align="center" width="80">
														<input type="text" name="txtbalanceqnty[]" id="txtbalanceqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px"  value="<? echo  number_format($balance_qnty,2,'.','');?>" />
													</td>
													<?
												}
												?>
												<td width="80" align="center">
													<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" value="<? if($roll_no!=0) echo $roll_no; ?>" <? echo $disable; ?> placeholder="<? echo $roll_arr[$order_id]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" />
												</td>
												<td width="80"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? echo $barcode_no; ?>" disabled/></td>
												<td width="80">
													<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
													<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
												</td>
											</tr>
											<?
											$i++;
										}
									}
								}
								else
								{
									if($receive_basis==14)
									{
										$po_sql = "select a.id, a.style_ref_no,a.delivery_date pub_shipment_date,a.job_no po_number, a.within_group,a.job_no_prefix_num job_no_mst, b.body_part_id, b.color_type_id, b.determination_id, b.gsm_weight, dia as dia_width, b.width_dia_type as dia_width_type, b.color_id, b.color_range_id, b.finish_qty, b.grey_qty, b.pre_cost_fabric_cost_dtls_id, b.grey_qnty_by_uom qnty from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and b.determination_id=$fabric_desc_id and b.body_part_id=$body_part_id and b.color_id=$fabricColorId and b.status_active=1 and b.is_deleted=0";

										$po_res=sql_select($po_sql);

										foreach($po_res as $row)
										{
											$salseId .= $row[csf('id')].",";
										}

										$salseId = implode(",",array_unique(explode(",",chop($salseId,','))));

										$sql_cuml= "select b.order_id, sum(b.receive_qnty) as qnty,sum(b.reject_qty)as reject_qty,b.pre_cost_fabric_cost_dtls_id,b.fabric_description_id,b.body_part_id,b.color_id
										from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form=37 and b.order_id in ('$salseId')
										and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id,b.pre_cost_fabric_cost_dtls_id,b.fabric_description_id,b.body_part_id,b.color_id";

										$sql_result_cuml=sql_select($sql_cuml);
										foreach($sql_result_cuml as $row)
										{
											$cumu_rec_qty[$row[csf('order_id')]][$row[csf('fabric_description_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]] = $row[csf('qnty')];
											$cumu_reject_qty[$row[csf('order_id')]][$row[csf('fabric_description_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]] = $row[csf('reject_qty')];

										}

									}
									else
									{
										$booking_cond = " and c.booking_no='$txt_booking_no'";
										$po_sql="select a.buyer_name,a.style_ref_no,b.job_no_mst,b.id,b.file_no,b.grouping as ref, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date,c.fabric_color_id from wo_po_details_master a, wo_booking_dtls c, wo_po_break_down b where a.id=b.job_id and b.id=c.po_break_down_id and c.fabric_color_id=$fabricColorId $booking_cond group by b.id,b.file_no,b.grouping, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date, a.buyer_name,a.style_ref_no,b.job_no_mst,c.fabric_color_id order by b.pub_shipment_date asc";
									}

									$explSaveData = explode(",",$save_data);
									foreach($explSaveData as $val)
									{
										$finQnty = explode("**",$val);
										$po_data_array[$finQnty[0]]['qnty']=$finQnty[1];
										$po_data_array[$finQnty[0]]['reject_qnty']=$finQnty[5];
										$po_data_array[$finQnty[0]]['fab_meter_qnty']=$finQnty[7];
										$po_data_array[$finQnty[0]]['grey_qnty']=$finQnty[8];
									}
									$nameArray=sql_select($po_sql);

									foreach($nameArray as $row)
									{
										if ($i%2==0)
											$bgcolor="#E9F3FF";
										else
											$bgcolor="#FFFFFF";

										if($receive_basis==14)
										{
											$tot_req_qnty+=$row[csf('qnty')];
											$po_qnty_in_pcs = $row[csf('qnty')];
											$tot_po_qnty+=$po_qnty_in_pcs;

											$cumu_recieved_qty = $cumu_rec_qty[$row[csf('id')]][$row[csf('determination_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]];
											$cumu_balance =  ($row[csf('qnty')]- $cumu_recieved_qty);
											$reject_qnty = $cumu_reject_qty[$row[csf('id')]][$row[csf('determination_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]];
										}
										else if($receive_basis==11 || $receive_basis==2)
										{
											$tot_req_qnty+=$req_qty_array[$row[csf('id')]][$pre_cost_fab_conv_cost_dtls_id];
											$po_qnty_in_pcs = $row[csf('po_quantity')]*$row[csf('total_set_qnty')];
											$tot_po_qnty+=$po_qnty_in_pcs;
											$cumu_balance = $req_qty_array[$row[csf('id')]][$pre_cost_fab_conv_cost_dtls_id]-$cumu_rec_qty[$row[csf('id')]][$pre_cost_fab_conv_cost_dtls_id] + $cumu_return_qty[$row[csf('id')]];

											$cumu_recieved_qty = $cumu_rec_qty[$row[csf('id')]][$pre_cost_fab_conv_cost_dtls_id] - $cumu_return_qty[$row[csf('id')]];
											$cumu_grey_used_qty = $cumu_grey_used_qty_arr[$row[csf('id')]][$pre_cost_fab_conv_cost_dtls_id];

											$cumu_grey_balance = $req_qty_array[$row[csf('id')]][$pre_cost_fab_conv_cost_dtls_id]-$cumu_grey_used_qty;

										}
										else
										{
											$tot_req_qnty+=$req_qty_array[$row[csf('id')]][$pre_cost_fab_conv_cost_dtls_id];
											$po_qnty_in_pcs = $row[csf('po_quantity')]*$row[csf('total_set_qnty')];
											$tot_po_qnty+=$po_qnty_in_pcs;
											$cumu_balance = $req_qty_array[$row[csf('id')]][$pre_cost_fab_conv_cost_dtls_id]-$cumu_rec_qty[$row[csf('id')]][$pre_cost_fab_conv_cost_dtls_id];
											$cumu_recieved_qty = $cumu_rec_qty[$row[csf('id')]][$pre_cost_fab_conv_cost_dtls_id];

										}

										$qnty = $po_data_array[$row[csf('id')]]['qnty'];
										$reject_qnty = $po_data_array[$row[csf('id')]]['reject_qnty'];
										$fab_meter_qnty = $po_data_array[$row[csf('id')]]['fab_meter_qnty'];
										$grey_qnty = $po_data_array[$row[csf('id')]]['grey_qnty'];
										if($req_qty_array[$row[csf('id')]][$pre_cost_fab_conv_cost_dtls_id]>0)
										{
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
												<td width="80" align="center"><? echo $row[csf('style_ref_no')]; ?></td>
												<td width="80" align="center"><? echo $row[csf('job_no_mst')]; ?></td>
												<td width="80" align="center" title="<? echo $row[csf('id')]; ?>">
													<p><? echo $row[csf('po_number')]; ?></p>
													<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
													<input type="hidden" name="txtPoNo[]" id="txtPoNo_<? echo $i; ?>" value="<? echo $row[csf('po_number')]; ?>">
													<input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('buyer_name')]; ?>">
													<input type="hidden" name="buyerName[]" id="buyerName_<? echo $i; ?>" class="text_boxes" value="<? echo $buyer_arr[$row[csf('buyer_name')]];?>">
													<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="">
													<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="">
													<input type="hidden" name="hdnCummiGreyUsedQty[]" id="hdnCummiGreyUsedQty_<? echo $i; ?>" value="<? echo $cumu_grey_used_qty;?>">
												</td>
												<?
												if($receive_basis!=14)
												{
													?>
													<td width="80" align="center"><? echo $row[csf('file_no')]; ?></td>
													<td width="80" align="center"><? echo $row[csf('ref')];  ?></td>
													<?
												}
												?>
												<td width="80" align="center"><p><? echo change_date_format($row[csf('pub_shipment_date')]); ?></p></td>
												<td width="80" align="right">
													<? echo $po_qnty_in_pcs; ?>
													<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_qnty_in_pcs; ?>">
												</td>
												<?
												if($receive_basis==2 || $receive_basis==11)
												{
													?>
													<td width="80" align="right"><? echo number_format($req_qty_array[$row[csf('id')]][$pre_cost_fab_conv_cost_dtls_id],2,'.',''); ?>
														<input type="hidden" name="txtreqqty[]" id="txtreqqty_<? echo $i; ?>" value="<? echo $req_qty_array[$row[csf('id')]][$pre_cost_fab_conv_cost_dtls_id]; ?>">
													</td>
													<?
												}
												if($receive_basis==14)
												{
													?>
													<td width="80" align="right"><? echo number_format($row[csf('qnty')],2,'.',''); ?>
														<input type="hidden" name="txtreqqty[]" id="txtreqqty_<? echo $i; ?>" value="<? echo $row[csf('qnty')]; ?>">
													</td>
													<?
												}
												?>
												<td width="80" align="right">
													<? echo number_format($cumu_recieved_qty,2,'.',''); ?>
													<input type="hidden" name="hidden_cummulative_rcv_qnty[]" id="hidden_cummulative_rcv_qnty_<? echo $i; ?>" value="<? echo number_format($cumu_recieved_qty,2,'.',''); ?>" />
												</td>
												<td align="center" width="80">
													<input type="text" name="txtfinishQnty[]" id="txtfinishQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" placeholder="<? echo number_format($cumu_balance,2,'.',''); ?>" value="<? echo $qnty;?>"/>
												</td>
												<?
												if($process_costing_maintain==0 && $receive_basis==11)
												{
													?>
													<td width="80" align="right">
														<input type="text" name="txtgreyqty[]" id="txtgreyqty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" placeholder="<? echo $cumu_grey_balance; ?>" value="<? echo $grey_qnty; ?>">
													</td>
													<?
												}
												?>
												<td align="center" width="80">
													<input type="text" name="txtrejectqnty[]" id="txtrejectqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:67px" value="<? echo $reject_qnty;?>" />
												</td>
												<td align="center" width="80">
													<input type="text" name="txtfabmeterqnty[]" id="txtfabmeterqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:67px" value="<? echo $fab_meter_qnty;?>" />
												</td>
												<?
												if($receive_basis!=6 || $receive_basis!=4)
												{
													if($receive_basis==14)
													{
														$balance_qnty = ($row[csf('qnty')]- $cumu_rec_qty[$row[csf('id')]][$row[csf('determination_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]]);
													}
													else if($receive_basis==11 || $receive_basis==2)
													{
														$balance_qnty = ($req_qty_array[$row[csf('id')]][$pre_cost_fab_conv_cost_dtls_id])-($cumu_rec_qty[$row[csf('id')]][$pre_cost_fab_conv_cost_dtls_id]+$qnty) + $cumu_return_qty[$row[csf('id')]];
													}
													else
													{
														$balance_qnty = ($req_qty_array[$row[csf('id')]][$pre_cost_fab_conv_cost_dtls_id])-($cumu_rec_qty[$row[csf('id')]][$pre_cost_fab_conv_cost_dtls_id]+$qnty);
													}
													?>
													<td align="center" width="80">
														<input type="text" name="txtbalanceqnty[]" id="txtbalanceqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" value="<? echo number_format($balance_qnty,2,'.','');?>" />
													</td>
													<?
												}
												?>
											</tr>
											<?
											$i++;
										}
									}
								}
								?>
								<input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes" value="<? echo $tot_po_qnty; ?>">
								<input type="hidden" name="tot_req_qnty" id="tot_req_qnty" class="text_boxes" value="<? echo $tot_req_qnty; ?>">
								<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="<? echo $i-1; ?>">

								<input type="hidden" name="hidden_cummu_deli_rcv_qnty" id="hidden_cummu_deli_rcv_qnty" value="<? echo $delivery_wise_rcv_qnty[$txt_booking_no_id]; ?>" />
							</table>
						</div>
						<table width="<? echo $width; ?>" id="table_id">
							<tr>
								<td align="center" >
									<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
								</td>
							</tr>
						</table>
					</div>
					<?
				}
				?>
				</fieldset>
			</form>
		</body>
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
		</html>
		<?
		exit();
	}

	if ($action=="po_popup_delivery_store")
	{
		echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
		extract($_REQUEST);

		if($roll_maintained==1)
		{
			$disable_drop_down=1;
			$prev_distribution_method=2;
			$disabled="disabled='disabled'";

			if($receive_basis==2 || $receive_basis==9 || $receive_basis==11) $width="1220"; else $width="1065";
			$roll_arr=return_library_array("select po_breakdown_id, max(roll_no) as roll_no from pro_roll_details where entry_form in(7,37) group by po_breakdown_id",'po_breakdown_id','roll_no');
		}
		else
		{
			$prev_distribution_method=1;
			$disabled="";
			$disable_drop_down=0;
			if($receive_basis==1 || $receive_basis==2 || $receive_basis==9 || $receive_basis==11  || $receive_basis==10) $width="990"; else $width="940";
		}
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$cbo_company_id and variable_list=23 and category = 2 and status_active =1 and is_deleted=0 order by id");
		$over_receive_limit = !empty($variable_set_invent) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;
		?>
		<script>
			var receive_basis=<? echo $receive_basis; ?>;
			var roll_maintained=<? echo $roll_maintained; ?>;
			function distribute_qnty(str)
			{
				if(str==1)
				{
					var tot_po_qnty=$('#tot_po_qnty').val()*1;
					var tot_req_qnty=$('#tot_req_qnty').val()*1;
					var txt_prop_finish_qnty=$('#txt_prop_finish_qnty').val()*1;
					var tblRow = $("#tbl_list_search tr").length;
					var len=totalFinish=0;

					$("#tbl_list_search").find('tr').each(function()
					{
						var txtreqqty=$(this).find('input[name="txtreqqty[]"]').val()*1;
						if(txtreqqty>0)
						{
							len=len+1;
							var po_qnty=$(this).find('input[name="txtPoQnty[]"]').val()*1;
							var txtbalanceqnty=$(this).find('input[name="txtbalanceqnty[]"]').val()*1;
							var hidden_cummulative_rcv_qnty=$(this).find('input[name="hidden_cummulative_rcv_qnty[]"]').val()*1;

							if(receive_basis == 14 || receive_basis == 10){
								var perc=(txtreqqty/tot_req_qnty)*100;
							}else{
								var perc=(po_qnty/tot_po_qnty)*100;
							}

							var finish_qnty=((perc*txt_prop_finish_qnty)/100);
							totalFinish = (totalFinish*1+finish_qnty*1).toFixed(2);
							//totalFinish = totalFinish;
							var balance_qty= txtreqqty-(hidden_cummulative_rcv_qnty + finish_qnty);

							if(tblRow==len)
							{
								var balance = (txt_prop_finish_qnty-totalFinish);
								if(balance > 0){
									finish_qnty = (finish_qnty*1 + balance*1);
								}else{
									finish_qnty = (finish_qnty*1 - balance*1);
								}
								if(balance!=0) totalFinish=totalFinish*1+(balance*1);
							}

							$(this).find('input[name="txtfinishQnty[]"]').val(finish_qnty.toFixed(2));
							$(this).find('input[name="txtbalanceqnty[]"]').val(balance_qty.toFixed(2));
						}
					});
				}
				else
				{
					$('#txt_prop_finish_qnty').val('');
					$("#tbl_list_search").find('tr').each(function()
					{
						$(this).find('input[name="txtfinishQnty[]"]').val('');
						$(this).find('input[name="txtbalanceqnty[]"]').val('');
					});
				}
			}

			function fnc_close()
			{
				var save_string='';	 var tot_finish_qnty=0; var tot_cum_rec=0; var tot_reqqty=0;var tot_balanceqnty=0; var order_nos=''; var no_of_roll=''; var tot_reject_qnty=0; var tot_required_qnty = 0;
				var po_id_array = new Array(); var buyer_id_array = new Array(); var buyer_name_array = new Array();var tot_balance="";
				var hdn_delivery_qnty=$('#hdn_delivery_qnty').val()*1;
				var hidden_cummu_deli_rcv_qnty=$('#hidden_cummu_deli_rcv_qnty').val()*1;
				var hdn_dtls_id=$('#hdn_dtls_id').val();
				var hdnRequiredQnty=""; var hiddenCummulativeRcvQnty="";
				var overRecLim="<? echo $over_receive_limit; ?>";
				var receiveBasis="<? echo $receive_basis; ?>";
				var overValue = 0; var total_txt_finish_and_reject_qnty= 0;
				$("#tbl_list_search").find('tr').each(function()
				{
					var txtPoId=$(this).find('input[name="txtPoId[]"]').val();
					var txtPoNo=$(this).find('input[name="txtPoNo[]"]').val();
					var txtfinishQnty=$(this).find('input[name="txtfinishQnty[]"]').val();
					var txtrejectQnty=$(this).find('input[name="txtrejectqnty[]"]').val();
					var txtreqqty=$(this).find('input[name="txtreqqty[]"]').val();

					var buyerId=$(this).find('input[name="buyerId[]"]').val();
					var buyerName=$(this).find('input[name="buyerName[]"]').val();

					var hdn_cumm_receive=$(this).find('input[name="hdn_cumm_receive[]"]').val();
					hdnRequiredQnty=$(this).find('input[name="txtreqqty[]"]').val();


					tot_finish_qnty=tot_finish_qnty*1+txtfinishQnty*1;
					tot_reject_qnty=tot_reject_qnty*1+txtrejectQnty*1;
					tot_required_qnty+=txtreqqty*1;


					if(save_string=="")
					{
						save_string=txtPoId+"_"+txtfinishQnty*1;
					}
					else
					{
						save_string+=","+txtPoId+"_"+txtfinishQnty*1;
					}

					if( jQuery.inArray( txtPoId, po_id_array) == -1 )
					{
						if(txtfinishQnty>0){
							po_id_array.push(txtPoId);
							if(order_nos=='') order_nos=txtPoNo; else order_nos+=","+txtPoNo;
						}
					}

					if( jQuery.inArray( buyerId, buyer_id_array) == -1 )
					{
						buyer_id_array.push(buyerId);
						buyer_name_array.push(buyerName);
					}
				});



				$('#save_string').val( save_string );
				$('#tot_finish_qnty').val(tot_finish_qnty.toFixed(2));

				$('#all_po_id').val( po_id_array );
				$('#order_nos').val( order_nos );
				$('#buyer_id').val( buyer_id_array );

				$('#buyer_name').val( buyer_name_array );
				$('#distribution_method').val( $('#cbo_distribiution_method').val());

				parent.emailwindow.hide();
			}

			function roll_duplication_check(row_id)
			{
				var row_num=$('#tbl_list_search tr').length;
				var po_id=$('#txtPoId_'+row_id).val();
				var roll_no=$('#txtRoll_'+row_id).val();

				if(roll_no*1>0)
				{
					for(var j=1; j<=row_num; j++)
					{
						if(j==row_id)
						{
							continue;
						}
						else
						{
							var po_id_check=$('#txtPoId_'+j).val();
							var roll_no_check=$('#txtRoll_'+j).val();

							if(po_id==po_id_check && roll_no==roll_no_check)
							{
								alert("Duplicate Roll No.");
								$('#txtRoll_'+row_id).val('');
								return;
							}
						}
					}

					var txtRollId=$('#txtRollId_'+row_id).val();
					var data=po_id+"**"+roll_no+"**"+txtRollId;
					var response=return_global_ajax_value( data, 'roll_duplication_check', '', 'knit_finish_fabric_receive_by_garments_controller');
					var response=response.split("_");

					if(response[0]!=0)
					{
						var po_number=$('#tr_'+row_id).find('td:first').text();
						alert("This Roll Already Used. Duplicate Not Allowed");
						$('#txtRoll_'+row_id).val('');
						return;
					}
				}
			}

			function add_break_down_tr( i )
			{
				var cbo_distribiution_method=$('#cbo_distribiution_method').val();
				var isDisbled=$('#txtRoll_'+i).is(":disabled");

				if(cbo_distribiution_method==2 && isDisbled==false)
				{
					var row_num=$('#txt_tot_row').val();
					row_num++;

					var clone= $("#tr_"+i).clone();
					clone.attr({
						id: "tr_" + row_num,
					});

					clone.find("input,select").each(function(){

						$(this).attr({
							'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
							'name': function(_, name) { return name },
							'value': function(_, value) { return value }
						});

					}).end();

					$("#tr_"+i).after(clone);

					$('#txtOrginal_'+row_num).removeAttr("value").attr("value","0");
					$('#txtRoll_'+row_num).removeAttr("value").attr("value","");
					$('#txtfinishQnty_'+row_num).removeAttr("value").attr("value","");
					$('#txtrejectqnty_'+row_num).removeAttr("value").attr("value","");
					$('#txtbalanceqnty_'+row_num).removeAttr("value").attr("value","");
					$('#txtRoll_'+row_num).removeAttr("onBlur").attr("onBlur","roll_duplication_check("+row_num+");");
					$('#txtRollId_'+row_num).removeAttr("value").attr("value","");
					$('#txtBarcodeNo_'+row_num).removeAttr("value").attr("value","");

					$('#increase_'+row_num).removeAttr("value").attr("value","+");
					$('#decrease_'+row_num).removeAttr("value").attr("value","-");
					$('#increase_'+row_num).removeAttr("onclick").attr("onclick","add_break_down_tr("+row_num+");");
					$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");

					$('#txt_tot_row').val(row_num);
					set_all_onclick();
				}
			}

			function fn_deleteRow(rowNo)
			{
				var txtOrginal=$('#txtOrginal_'+rowNo).val()*1;
				var txtRollId=$('#txtRollId_'+rowNo).val();
				var txt_deleted_id=$('#hide_deleted_id').val();
				var selected_id='';
				if(txtOrginal==0)
				{
					if(txtRollId!='')
					{
						if(txt_deleted_id=='') selected_id=txtRollId; else selected_id=txt_deleted_id+','+txtRollId;
						$('#hide_deleted_id').val( selected_id );
					}
					$("#tr_"+rowNo).remove();
				}
			}
		</script>
	</head>
	<body>
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:<? echo $width; ?>px;margin-left:5px">
				<input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
				<input type="hidden" name="tot_finish_qnty" id="tot_finish_qnty" class="text_boxes" value="">
				<input type="hidden" name="tot_reject_qnty" id="tot_reject_qnty" class="text_boxes" value="">
				<input type="hidden" name="tot_balance_qnty" id="tot_balance_qnty" class="text_boxes" value="">
				<input type="hidden" name="all_po_id" id="all_po_id" class="text_boxes" value="">
				<input type="hidden" name="order_nos" id="order_nos" class="text_boxes" value="">
				<input type="hidden" name="number_of_roll" id="number_of_roll" class="text_boxes" value="">
				<input type="hidden" name="buyer_id" id="buyer_id" class="text_boxes" value="">
				<input type="hidden" name="buyer_name" id="buyer_name" class="text_boxes" value="">
				<input type="hidden" name="distribution_method" id="distribution_method" class="text_boxes" value="">
				<input type="hidden" name="hide_deleted_id" id="hide_deleted_id" class="text_boxes" value="<? echo $txt_deleted_id; ?>">
				<input type="hidden" name="hdn_delivery_qnty" id="hdn_delivery_qnty" value="<? echo number_format($hdn_delivery_qnty,2,'.',''); ?>">
				<input type="hidden" name="hdn_dtls_id" id="hdn_dtls_id" value="<? echo $update_dtls_id; ?>">
				<?
				$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$cbo_company_id and variable_list=23 and category = 2 and status_active =1 and is_deleted=0 order by id");
				$over_receive_limit = !empty($variable_set_invent) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;

				$req_qty_array=array();
				$prod_batch_booking_sql=sql_select("select booking_no from  pro_batch_create_mst where id='$txt_batch_id' and status_active=1 and is_deleted=0 group by booking_no");
				$batch_booking=$prod_batch_booking_sql[0][csf('booking_no')];
				if($batch_booking!='')
				{
					$reqQnty="select b.po_break_down_id,sum(b.fin_fab_qnty) as fabric_qty from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and c.lib_yarn_count_deter_id=$fabric_desc_id and b.booking_no='$batch_booking' and c.status_active=1 and c.is_deleted=0 group by b.po_break_down_id";
					//echo $reqQnty;
					$reqQnty_res = sql_select($reqQnty);

					foreach($reqQnty_res as $req_val)
					{
						$req_qty_array[$req_val[csf("po_break_down_id")]]=$req_val[csf("fabric_qty")];
					}

					// N.B: if fabrication changed in booking then Batch quantity is considered as fabric quantity
					$fabric_desc = $feb_construction.",".trim($feb_composition);
					$batch_qnty=sql_select("select b.po_id,sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b,product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.id=$txt_batch_id and c.detarmination_id=$fabric_desc_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 group by b.po_id");
					foreach($batch_qnty as $req_val)
					{
						$req_qty_batch_array[$req_val[csf("po_id")]]=$req_val[csf("batch_qnty")];
					}
				}
				if($db_type==0)
				{
					$all_batch_po_sql=sql_select("select group_concat(b.po_id) as po_id from  pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.booking_no='$batch_booking' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no");
				}
				else if($db_type==2)
				{
					$all_batch_po_sql=sql_select("select listagg(b.po_id,',') within group (order by b.po_id) as po_id from  pro_batch_create_mst a, pro_batch_create_dtls b where  a.id=b.mst_id and a.booking_no='$batch_booking' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no");
				}
				$batch_po_id=$all_batch_po_sql[0][csf('po_id')];


				?>
				<div id="search_div" style="margin-top:10px">
					<div style="width:<? echo $width; ?>px; margin-top:10px" align="center">
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300" align="center">
							<thead>
								<th>Total Receive Qnty</th>
								<th>Distribution Method</th>
							</thead>
							<tr class="general">
								<td><input type="text" name="txt_prop_finish_qnty" id="txt_prop_finish_qnty" class="text_boxes_numeric" value="<? if($prev_distribution_method==1) echo $txt_production_qty; ?>" style="width:120px"  onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)" disabled/></td>
								<td>
									<?
									$distribiution_method=array(1=>"Proportionately",2=>"Manually");
									echo create_drop_down( "cbo_distribiution_method", 160, $distribiution_method,"",0,"--Select--",$prev_distribution_method,"distribute_qnty(this.value);",1 );

									?>
								</td>
							</tr>
						</table>
					</div>
					<div style="margin-left:10px; margin-top:10px">
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $width; ?>">
							<thead>
								<th width="80">Style ref. No </th>
								<th width="80">Job No</th>
								<th width="80">PO No</th>
								<th width="70">File No</th>
								<th width="100">Ref No</th>
								<th width="80">Ship Date</th>
								<th width="80">PO Qty.</th>
								<th width="80">Req. Qty.</th>
								<th width="80">Finish Qty.</th>
							</thead>
								<tbody id="tbl_list_search">
									<?
									$i=1; $tot_po_qnty=0; $po_array=array(); $po_data_array=array();
									$booking_cond = ($txt_po_booking_no!="")?" and c.booking_no='$txt_po_booking_no'":"";
									$hidden_order_id = explode("_", $hidden_order_id);
									$po_sql=" select b.id,a.style_ref_no,b.job_no_mst, b.file_no,b.grouping as ref,b.po_number,(a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date, a.buyer_name
									from wo_po_details_master a, wo_po_break_down b,wo_booking_dtls c
									where a.id=b.job_id and b.id=c.po_break_down_id and b.id in ($hidden_order_id[0]) and a.status_active!=0 and a.is_deleted!=1 and b.status_active!=0 and b.is_deleted!=1 $booking_cond
									group by b.id,a.style_ref_no,b.job_no_mst, b.file_no,b.grouping,b.po_number, a.total_set_qnty,b.po_quantity, b.pub_shipment_date, a.buyer_name";


									$po_data_array=array();
									$OrderQntyData = explode(",",$save_data);
									foreach ($OrderQntyData as $value)
									{
										$finQnty = explode("_",$value);
										$po_data_array[$finQnty[0]]['qnty']+=$finQnty[1];
									}

									$nameArray=sql_select($po_sql);
									foreach($nameArray as $row)
									{
										$po_ref_arr[$row[csf('id')]]["po_number"] =$row[csf('po_number')];
										$po_ref_arr[$row[csf('id')]]["po_qnty_in_pcs"] =$row[csf('po_qnty_in_pcs')];
										$po_ref_arr[$row[csf('id')]]["style_ref_no"] =$row[csf('style_ref_no')];
										$po_ref_arr[$row[csf('id')]]["job_no_mst"] =$row[csf('job_no_mst')];
										$po_ref_arr[$row[csf('id')]]["file_no"] =$row[csf('file_no')];
										$po_ref_arr[$row[csf('id')]]["ref"] =$row[csf('ref')];
										$po_ref_arr[$row[csf('id')]]["pub_shipment_date"] =$row[csf('pub_shipment_date')];
										$po_ref_arr[$row[csf('id')]]["buyer_name"] =$row[csf('buyer_name')];
									}

									foreach($po_data_array as $po_id=>$row)
									{
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

										$po_number = $po_ref_arr[$po_id]["po_number"];
										$po_qnty_in_pcs = $po_ref_arr[$po_id]["po_qnty_in_pcs"];
										$style_ref_no = $po_ref_arr[$po_id]["style_ref_no"];
										$job_no_mst = $po_ref_arr[$po_id]["job_no_mst"];
										$file_no = $po_ref_arr[$po_id]["file_no"];
										$ref = $po_ref_arr[$po_id]["ref"];
										$pub_shipment_date = $po_ref_arr[$po_id]["pub_shipment_date"];
										$buyer_name = $po_ref_arr[$po_id]["buyer_name"];

										$qnty = $row['qnty'];
										$orginal_val=1;
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
											<td width="80" align="center"><? echo $style_ref_no; ?></td>
											<td width="80" align="center"><? echo $job_no_mst; ?></td>
											<td width="80" title="<? echo $po_id; ?>" align="center">
												<p><? echo $po_number; ?></p>
												<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $po_id; ?>">
												<input type="hidden" name="txtPoNo[]" id="txtPoNo_<? echo $i; ?>" value="<? echo $po_number; ?>">
												<input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" class="text_boxes" value="<? echo $buyer_name; ?>">
												<input type="hidden" name="buyerName[]" id="buyerName_<? echo $i; ?>" class="text_boxes" value="<? echo $buyer_arr[$buyer_name];?>">
												<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
												<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
											</td>
											<td width="70" align="center"><? echo $file_no; ?></td>
											<td width="100" align="center"><? echo $ref; ?></td>

											<td width="80" align="center"><p><? echo change_date_format($pub_shipment_date); ?></p></td>
											<td width="80" align="right">
												<? echo $po_qnty_in_pcs; ?>
												<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_qnty_in_pcs; ?>">
											</td>
											<?

											$req_qty= $req_qty_array[$po_id] > 0 ? $req_qty_array[$po_id] : $req_qty_batch_array[$po_id];

											$overRecvLimit= ($req_qty*$over_receive_limit)/100;
											?>
											<td align="right" width="80" title="<? echo "Quantity with Over receive limit = ". number_format($overRecvLimit,2,'.',''); ?>">
												<? echo number_format($req_qty,2,'.',''); ?>
												<input type="hidden" name="txtreqqty[]" id="txtreqqty_<? echo $i; ?>" value="<? echo $req_qty; ?>"/>
											</td>
											<td align="center" width="80">
												<input type="text" name="txtfinishQnty[]" id="txtfinishQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" placeholder="<? echo number_format($cumul_balance,2,'.',''); ?>" value="<? echo $qnty; ?>" disabled/>
											</td>

										</tr>
										<?
										$i++;
										$tot_req_qnty+=$req_qty;
									}

									?>
									<input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes" value="<? echo $tot_po_qnty; ?>">
									<input type="hidden" name="tot_req_qnty" id="tot_req_qnty" class="text_boxes" value="<? echo $tot_req_qnty; ?>">
									<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="<? echo $i-1; ?>">
									<input type="hidden" name="hidden_cummu_deli_rcv_qnty" id="hidden_cummu_deli_rcv_qnty" value="<? echo $delivery_wise_rcv_qnty[$txt_booking_no_id]; ?>" />
								</tbody>
							</table>
						</div>
						<table width="<? echo $width; ?>" id="table_id">
							<tr>
								<td align="center" >
									<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
								</td>
							</tr>
						</table>
					</div>
				</div>
				</fieldset>
			</form>
		</body>
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
		</html>
		<?
		exit();
	}

if($action=='show_fabric_desc_listview')
{
	$data=explode("**",$data);
	$fabric_mstid = $data[0];
	$booking_pi_production_no=$data[3];
	$is_sample=$data[1];
	$receive_basis=$data[2];
	$process_costing_maintain=$data[4];
	$var_allow_fin_rcv_NR_hdn=$data[5];
	$company_id=$data[6];

	$batch_arr=return_library_array("select id, batch_no from pro_batch_create_mst","id","batch_no");
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$color_arr=return_library_array("select id, color_name from lib_color where status_active=1","id","color_name");

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 order by b.id asc";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}

	if($receive_basis==1) // PI basis
	{
		$sql="SELECT a.pi_number,a.currency_id,a.pi_basis_id,b.id pi_dtls_id,b.work_order_id,b.work_order_no,b.booking_without_order,b.fabric_construction, b.fabric_composition, b.color_id, b.gsm, b.dia_width, b.determination_id, '' as body_part_id, b.gsm as gsm_weight, b.dia_width, b.quantity as qnty, b.rate as rate, b.uom, 1 as type
		from com_pi_master_details a, com_pi_item_details b
		where a.id=b.pi_id and a.pi_number='$booking_pi_production_no' and a.pi_basis_id=1 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=2
		union all
		select a.pi_number,a.currency_id,a.pi_basis_id,b.id pi_dtls_id,b.work_order_id,b.work_order_no,b.booking_without_order,b.fabric_construction, b.fabric_composition, b.color_id, b.gsm, b.dia_width, b.determination_id, '' as body_part_id, b.gsm as gsm_weight, b.dia_width, b.quantity as qnty, b.rate as rate, b.uom, 2 as type
		from com_pi_master_details a, com_pi_item_details b
		where a.id=b.pi_id and a.pi_number='$booking_pi_production_no' and a.pi_basis_id=2 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=2";

		$data_array=sql_select($sql);

		foreach($data_array as $row)
		{
			$booking_no_arr[$row[csf('work_order_no')]] = "'".$row[csf('work_order_no')]."'";
		}

		if($is_sample != 1)
		{
			//This block for short booking...........
			$chk_short_booking=sql_select("select c.booking_no,c.booking_type,c.is_short from wo_booking_mst c,wo_booking_dtls a,wo_po_break_down b where c.booking_no in(".implode(",",$booking_no_arr).") and c.booking_no=a.booking_no and a.po_break_down_id=b.id and a.status_active=1 and b.status_active in (1,3) group by c.booking_no,c.booking_type,c.is_short");
			foreach ($chk_short_booking as $row)
			{
				if($row[csf("booking_type")]==1 && $row[csf("is_short")]==1)
				{
					$booking_no_arr_short[$row[csf('booking_no')]] = "'".$row[csf('booking_no')]."'";
				}
				else
				{
					$booking_no_arr_all[$row[csf('booking_no')]] = "'".$row[csf('booking_no')]."'";
				}
			}

			if(!empty($booking_no_arr_short))
			{
				$booking_ord_sql=sql_select("SELECT c.booking_no,c.buyer_id, d.construction, d.composition as copmposition, a.fabric_color_id, d.gsm_weight, a.dia_width,a.job_no, b.id as po_id,b.po_number as po_number, b.grouping, e.style_ref_no,a.color_type
					from wo_booking_mst c,wo_booking_dtls a, wo_po_break_down b ,wo_pre_cost_fabric_cost_dtls d, wo_po_details_master e where c.booking_no in(".implode(",",$booking_no_arr_short).") and c.booking_no=a.booking_no and a.po_break_down_id=b.id and a.pre_cost_fabric_cost_dtls_id=d.id and b.job_id=e.id and a.status_active=1 and b.status_active in (1,3)
					group by c.booking_no,c.buyer_id, d.construction,d.composition,a.fabric_color_id,d.gsm_weight,a.dia_width,a.job_no,b.id,b.po_number, b.grouping, e.style_ref_no,a.color_type");

				$book_order_data=array();$int_ref_arr=array();$color_type_arr=array();
				foreach($booking_ord_sql as $row)
				{
					$book_order_data[$row[csf("booking_no")]][$row[csf("construction")]][$row[csf("copmposition")]][$row[csf("fabric_color_id")]][$row[csf("gsm_weight")]][$row[csf("dia_width")]]["all_po_id"].= $row[csf("po_id")].",";
					$book_order_data[$row[csf("booking_no")]][$row[csf("construction")]][$row[csf("copmposition")]][$row[csf("fabric_color_id")]][$row[csf("gsm_weight")]][$row[csf("dia_width")]]["all_po_num"].= $row[csf("po_number")].",";
					$book_order_data[$row[csf("booking_no")]][$row[csf("construction")]][$row[csf("copmposition")]][$row[csf("fabric_color_id")]][$row[csf("gsm_weight")]][$row[csf("dia_width")]]["job_no"].= $row[csf("job_no")].",";

					$job_no_arr[$row[csf("job_no")]]="'".$row[csf("job_no")]."'";
					$work_order_job[$row[csf("booking_no")]] .=$row[csf("job_no")].",";
					$work_order_buyer[$row[csf("booking_no")]]=$row[csf("buyer_id")];

					$int_ref_arr[$row[csf("booking_no")]].=$row[csf("grouping")].',';
					$style_ref_arr[$row[csf("booking_no")]].=$row[csf("style_ref_no")].',';
					$color_type_arr[$row[csf("booking_no")]].=$color_type[$row[csf("color_type")]].',';
				}
				//End block for short booking........
			}

			if(!empty($booking_no_arr_all))
			{
				$booking_ord_sql=sql_select("SELECT c.booking_no,c.buyer_id, a.construction, a.copmposition, a.fabric_color_id, a.gsm_weight, a.dia_width,a.job_no, b.id as po_id,b.po_number as po_number, b.grouping, e.style_ref_no,a.color_type
				from wo_booking_mst c,wo_booking_dtls a, wo_po_break_down b, wo_po_details_master e
				where c.booking_no in(".implode(",",$booking_no_arr_all).") and c.booking_no=a.booking_no and a.po_break_down_id=b.id and b.job_id=e.id and a.status_active=1 and b.status_active in (1,3)
				group by c.booking_no,c.buyer_id, a.construction,a.copmposition,a.fabric_color_id,a.gsm_weight,a.dia_width,a.job_no,b.id,b.po_number,b.grouping, e.style_ref_no,a.color_type");

				foreach($booking_ord_sql as $row)
				{
					$book_order_data[$row[csf("booking_no")]][$row[csf("construction")]][$row[csf("copmposition")]][$row[csf("fabric_color_id")]][$row[csf("gsm_weight")]][$row[csf("dia_width")]]["all_po_id"].= $row[csf("po_id")].",";
					$book_order_data[$row[csf("booking_no")]][$row[csf("construction")]][$row[csf("copmposition")]][$row[csf("fabric_color_id")]][$row[csf("gsm_weight")]][$row[csf("dia_width")]]["all_po_num"].= $row[csf("po_number")].",";
					$book_order_data[$row[csf("booking_no")]][$row[csf("construction")]][$row[csf("copmposition")]][$row[csf("fabric_color_id")]][$row[csf("gsm_weight")]][$row[csf("dia_width")]]["job_no"].= $row[csf("job_no")].",";

					$job_no_arr[$row[csf("job_no")]]="'".$row[csf("job_no")]."'";
					$work_order_job[$row[csf("booking_no")]] .=$row[csf("job_no")].",";
					$work_order_buyer[$row[csf("booking_no")]]=$row[csf("buyer_id")];

					$int_ref_arr[$row[csf("booking_no")]].=$row[csf("grouping")].',';
					$style_ref_arr[$row[csf("booking_no")]].=$row[csf("style_ref_no")].',';
					$color_type_arr[$row[csf("booking_no")]].=$color_type[$row[csf("color_type")]].',';
				}
			}

			$sql_body_part_id="SELECT a.id,a.job_no,a.body_part_id,a.gsm_weight,a.construction,a.composition,b.fabric_color_id
			from wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls b
			where a.job_no=b.job_no and a.job_no in(".implode(",",$job_no_arr).") and b.booking_no in(".implode(",",$booking_no_arr).") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.pre_cost_fabric_cost_dtls_id=a.id
			group by a.id,a.job_no,a.body_part_id,a.gsm_weight,a.construction,a.composition,b.fabric_color_id order by a.id";
			$sql_body_part_id=sql_select($sql_body_part_id);
			$body_part_id_Arr=array();
			$body_part_id='';
			foreach($sql_body_part_id as $roww)
			{
				$body_part_id_Arr[$roww[csf('job_no')]][$roww[csf('gsm_weight')]][$roww[csf('construction')]][$roww[csf('composition')]][$roww[csf('fabric_color_id')]].=$roww[csf('body_part_id')].",";
			}
		}
		else
		{
			//$sql_body_part_id="select booking_no, construction, composition, gsm_weight, body_part, fabric_color from wo_non_ord_samp_booking_dtls where booking_no in (".implode(",",$booking_no_arr).")";
			$sql_body_part_id="select a.booking_no, a.construction, a.composition, a.gsm_weight, a.body_part, a.fabric_color, b.buyer_id from wo_non_ord_samp_booking_dtls a, wo_non_ord_samp_booking_mst b  where a.booking_no=b.booking_no and b.booking_no in (".implode(",",$booking_no_arr).") and b.status_active=1 and b.is_deleted=0";
			$sql_body_part_id=sql_select($sql_body_part_id);
			$body_part_id_Arr=array();
			$body_part_id='';
			foreach($sql_body_part_id as $roww)
			{
				$body_part_id_Arr[$roww[csf('booking_no')]][$roww[csf('gsm_weight')]][$roww[csf('construction')]][$roww[csf('composition')]][$roww[csf('fabric_color')]].=$roww[csf('body_part')].",";
				$work_order_buyer[$roww[csf("booking_no")]]=$roww[csf("buyer_id")];
			}
		}
	}
	else if($receive_basis==2) // WO/Booking basis
	{
		if($is_sample==0)
		{
			if($process_costing_maintain==1 && $var_allow_fin_rcv_NR_hdn==1)
			{
				$bookingQntyCond="sum(a.fin_fab_qnty) as qnty";
			}
			else
			{
				$bookingQntyCond="sum(a.grey_fab_qnty) as qnty";
			}
			$sql="SELECT a.job_no,c.buyer_id,b.lib_yarn_count_deter_id as determination_id, b.body_part_id, b.gsm_weight,a.pre_cost_fabric_cost_dtls_id as pre_cost_fab_conv_cost_dtls_id, a.dia_width, a.fabric_color_id as color_id, b.uom ,$bookingQntyCond , avg(a.rate) as rate
			from wo_booking_mst c,wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b
			where c.booking_no=a.booking_no and a.pre_cost_fabric_cost_dtls_id=b.id and a.booking_no='$booking_pi_production_no' and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 group by a.job_no,c.buyer_id,b.lib_yarn_count_deter_id, b.body_part_id, b.gsm_weight, pre_cost_fabric_cost_dtls_id, a.dia_width, a.fabric_color_id, b.uom";
			$exchange_rate=return_field_value("exchange_rate","wo_booking_mst","booking_no='$booking_pi_production_no'");

			$booking_ord_sql=sql_select("SELECT c.booking_no, c.buyer_id, a.construction, a.copmposition, a.fabric_color_id, a.gsm_weight, a.dia_width,a.job_no, b.id as po_id,b.po_number as po_number, b.grouping,d.style_ref_no,a.color_type, e.body_part_id, e.lib_yarn_count_deter_id, a.pre_cost_fabric_cost_dtls_id
			from wo_booking_mst c, wo_booking_dtls a, wo_po_break_down b,wo_po_details_master d, wo_pre_cost_fabric_cost_dtls e
			where c.booking_no='$booking_pi_production_no' and c.booking_no=a.booking_no and b.job_id=d.id and a.po_break_down_id=b.id and a.pre_cost_fabric_cost_dtls_id=e.id and a.status_active=1 and b.status_active=1
			group by c.booking_no,c.buyer_id, a.construction,a.copmposition,a.fabric_color_id,a.gsm_weight,a.dia_width,a.job_no,b.id,b.po_number,b.grouping,d.style_ref_no,a.color_type, e.body_part_id, e.lib_yarn_count_deter_id");

			$int_ref_arr=array();
			foreach ($booking_ord_sql as $key => $row)
			{
				$int_ref_arr[$row[csf("job_no")]].=$row[csf("grouping")].',';
				$style_ref_arr[$row[csf("job_no")]].=$row[csf("style_ref_no")].',';
				//$color_type_arr[$row[csf("job_no")]].=$color_type[$row[csf("color_type")]].',';
				$color_type_fabrication_body_arr[$row[csf("job_no")]][$row[csf("fabric_color_id")]][$row[csf("body_part_id")]][$row[csf("lib_yarn_count_deter_id")]].=$color_type[$row[csf("color_type")]].',';

				$order_against_pre_costing[$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("job_no")]]=$row[csf("job_no")];
			}
		}
		else
		{
			$sql="SELECT lib_yarn_count_deter_id as determination_id, body_part as body_part_id, gsm_weight, dia_width, construction, composition, fabric_color as color_id, sum(grey_fabric) as qnty, avg(rate) as rate, uom
			from wo_non_ord_samp_booking_dtls where booking_no='$booking_pi_production_no' and status_active=1 and is_deleted=0
			group by lib_yarn_count_deter_id, body_part, gsm_weight, dia_width, construction, composition, fabric_color,uom";
			$exchange_rate=return_field_value("exchange_rate","wo_non_ord_samp_booking_mst","booking_no='$booking_pi_production_no'");
		}
	}
	else if($receive_basis==11)
	{
		if($is_sample==1)
		{
		//$sql="select c.lib_yarn_count_deter_id as determination_id, c.body_part as body_part_id, c.gsm_weight, c.dia_width, c.fabric_color as color_id, b.wo_qty as qnty, b.rate, b.uom from wo_non_ord_knitdye_booking_mst a, wo_non_ord_knitdye_booking_dtl b, wo_non_ord_samp_booking_dtls c where a.id=b.mst_id and b.fab_des_id=c.id and b.fabric_source=1 and b.process_id not in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no='$fabric_mstid'";

			$sql="select c.lib_yarn_count_deter_id as determination_id, c.body_part as body_part_id, c.gsm_weight, c.dia_width, c.fabric_color as color_id, sum(b.wo_qty) as qnty, sum(b.wo_qty*b.rate) as amount, b.uom
			from wo_non_ord_knitdye_booking_mst a, wo_non_ord_knitdye_booking_dtl b, wo_non_ord_samp_booking_dtls c
			where a.id=b.mst_id and b.fab_des_id=c.id and b.fabric_source=1 and b.process_id not in(1) and a.status_active=1
			and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no='$fabric_mstid' and b.wo_qty>0
			group by c.lib_yarn_count_deter_id, c.body_part, c.gsm_weight, c.dia_width, c.fabric_color, b.uom";
		}
		else
		{
			$sql="select b.lib_yarn_count_deter_id as determination_id, b.body_part_id, b.gsm_weight, a.pre_cost_fabric_cost_dtls_id as pre_cost_fab_conv_cost_dtls_id, a.dia_width, a.fabric_color_id as color_id, sum(a.wo_qnty) as qnty, avg(a.rate) as rate , b.uom,a.booking_no, b.color_type_id
			from wo_booking_dtls a, wo_pre_cost_fab_conv_cost_dtls c, wo_pre_cost_fabric_cost_dtls b
			where a.pre_cost_fabric_cost_dtls_id=c.id and c.fabric_description=b.id and a.booking_no='$fabric_mstid' and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and a.wo_qnty>0 group by b.lib_yarn_count_deter_id, b.body_part_id, b.gsm_weight,a.pre_cost_fabric_cost_dtls_id,a.dia_width, a.fabric_color_id, b.uom,a.booking_no, b.color_type_id";

			$booking_ord_sql=sql_select("SELECT c.booking_no, c.buyer_id, a.construction, a.copmposition, a.fabric_color_id, a.gsm_weight, a.dia_width,a.job_no, b.id as po_id,b.po_number as po_number, b.grouping,d.style_ref_no
			from wo_booking_mst c, wo_booking_dtls a, wo_po_break_down b,wo_po_details_master d
			where  c.booking_no=a.booking_no and b.job_id=d.id and a.po_break_down_id=b.id and a.status_active=1 and b.status_active=1 and a.booking_no='$fabric_mstid'
			group by c.booking_no,c.buyer_id, a.construction,a.copmposition,a.fabric_color_id,a.gsm_weight,a.dia_width,a.job_no,b.id,b.po_number,b.grouping,d.style_ref_no");
			$int_ref_arr=array();
			foreach ($booking_ord_sql as $key => $row)
			{
				$int_ref_arr[$row[csf("booking_no")]].=$row[csf("grouping")].',';
				$style_ref_arr[$row[csf("booking_no")]].=$row[csf("style_ref_no")].',';
				$service_job_arr[$row[csf("booking_no")]].=$row[csf("job_no")].',';
			}
		}
	}
	else if($receive_basis==14)
	{
		$sql = "SELECT a.company_id,a.within_group,a.buyer_id,b.id as details_id, b.mst_id, b.job_no_mst, b.body_part_id, b.color_type_id, b.determination_id, b.fabric_desc, b.gsm_weight, dia as dia_width, b.width_dia_type as dia_width_type, b.color_id, b.color_range_id, b.finish_qty, b.avg_rate, b.amount, b.process_loss, b.grey_qty, b.work_scope, b.yarn_data,b.order_uom,b.rmg_qty, b.pre_cost_fabric_cost_dtls_id, b.item_number_id, b.grey_qnty_by_uom qnty, b.cons_uom uom,b.avg_rate rate
		from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and b.mst_id=$fabric_mstid and b.status_active=1 and b.is_deleted=0";
	}
	else if($receive_basis==10) // Delivery from Textile
	{
		$sql="select a.company_id,b.mst_id,b.sys_dtls_id,b.batch_id,b.order_id, b.bodypart_id body_part_id,b.determination_id,b.gsm gsm_weight,b.dia dia_width,b.uom,b.color_id,b.width_type dia_width_type,b.fabric_shade, sum(b.current_delivery) qnty,b.product_id,d.job_no,d.within_group,d.po_buyer buyer_id from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, fabric_sales_order_mst d where a.id=b.mst_id and b.order_id=d.id and a.id=$fabric_mstid group by a.company_id,b.mst_id,b.sys_dtls_id,b.batch_id,b.order_id,b.bodypart_id,b.determination_id,b.gsm,b.dia,b.uom,b.color_id, b.width_type,b.fabric_shade,b.product_id,d.job_no,d.within_group,d.po_buyer order by batch_id ASC";

		$data_array=sql_select($sql);

		foreach($data_array as $row)
		{
			$sys_dtls_id .= $row[csf('sys_dtls_id')].",";
			$sales_order_id .= $row[csf('order_id')].",";
		}

		$sys_dtls_id = chop($sys_dtls_id,",");
		$sales_order_id = chop($sales_order_id,",");

		if($sys_dtls_id!="")
		{
			$sql_production="SELECT id,fabric_shade,no_of_roll FROM pro_finish_fabric_rcv_dtls WHERE status_active=1 and is_deleted=0 and id in($sys_dtls_id)";

			$sql_result =sql_select($sql_production);

			$productionData = array();
			foreach($sql_result as $row)
			{
				$productionData[$row[csf('id')]]['fabric_shade'] = $row[csf('fabric_shade')];
				$productionData[$row[csf('id')]]['no_of_roll'] = $row[csf('no_of_roll')];
			}
		}
		$sales_rate_arr=array();
		if($sales_order_id!="")
		{
			$sales_order_info = sql_select("select mst_id,body_part_id,determination_id,color_id,avg_rate from fabric_sales_order_dtls where mst_id in($sales_order_id) and status_active=1 and is_deleted=0");
			foreach ($sales_order_info as $row) {
				$sales_rate_arr[$row[csf('mst_id')]][$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('color_id')]] = $row[csf('avg_rate')];
			}
		}
	}
	else
	{
		$cons_comps_arr = return_library_array("select id, item_description from product_details_master where item_category_id=2","id","item_description");
		$sql="select id, prod_id, batch_id, fabric_description_id as determination_id, body_part_id, color_id, gsm as gsm_weight, width as dia_width, receive_qnty as qnty,dia_width_type, uom, fabric_shade from pro_finish_fabric_rcv_dtls where mst_id='$booking_pi_production_no' and status_active=1 and is_deleted=0 order by batch_id ASC";
	}

	if($receive_basis==1 || $receive_basis==2 || $receive_basis==9 || $receive_basis==11 )
	{

		if($receive_basis==11){
			$booking_no_condition = " and c.booking_no='$fabric_mstid'";
		}
		else if($receive_basis==9){
			$booking_no_condition = " and c.booking_id='$booking_pi_production_no'";
		}
		else{
			$booking_no_condition = " and c.booking_no='$booking_pi_production_no'";
		}


		$pre_rcv_sql="SELECT c.id, b.prod_id, b.body_part_id, b.fabric_description_id, b.pre_cost_fabric_cost_dtls_id, b.original_width, b.gsm, b.uom, b.width, b.pi_wo_dtls_id, b.batch_id, d.color, b.fabric_shade, b.dia_width_type, sum(b.receive_qnty) as qnty from pro_finish_fabric_rcv_dtls b, inv_receive_master c, product_details_master d where b.mst_id=c.id and b.prod_id=d.id and c.receive_basis=$receive_basis and c.entry_form=37 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $booking_no_condition group by c.id, b.prod_id, b.body_part_id, b.fabric_description_id, b.pre_cost_fabric_cost_dtls_id, b.uom, b.original_width, b.gsm, b.width, b.pi_wo_dtls_id, b.batch_id, d.color, b.fabric_shade, b.dia_width_type";

		$pre_rcv_sql = sql_select($pre_rcv_sql);
		foreach ($pre_rcv_sql as $val)
		{
			/* if ($val[csf("original_width")]==0 || $val[csf("original_width")]=="")
			{
				$val[csf("original_width")]=$val[csf("width")];
			} */

			if($val[csf("original_width")] =="")
			{
				$val[csf("original_width")]=0;
			}

			if($receive_basis==1)
			{
				$pre_rcv_arr[$val[csf("fabric_description_id")]][$val[csf("pi_wo_dtls_id")]][$val[csf("original_width")]][$val[csf("gsm")]][$val[csf('uom')]][$val[csf("color")]]['qnty'] += $val[csf("qnty")];
			}
			else if ($receive_basis ==2 || $receive_basis ==11)
			{
				$pre_rcv_arr[$val[csf("body_part_id")]][$val[csf("fabric_description_id")]][$val[csf("pre_cost_fabric_cost_dtls_id")]][$val[csf("original_width")]][$val[csf("gsm")]][$val[csf("uom")]][$val[csf("color")]]['qnty'] += $val[csf("qnty")];
			}
			else if($receive_basis==9)
			{
				$pre_rcv_arr[$val[csf("body_part_id")]][$val[csf("fabric_description_id")]][$val[csf("batch_id")]][$val[csf("width")]][$val[csf("gsm")]][$val[csf("uom")]][$val[csf("color")]][$val[csf("dia_width_type")]][$val[csf("fabric_shade")]]['qnty'] += $val[csf("qnty")];
			}
			else
			{
				$pre_rcv_arr[$val[csf("body_part_id")]][$val[csf("fabric_description_id")]][$val[csf("original_width")]][$val[csf("gsm")]][$val[csf("uom")]][$val[csf("color")]]['qnty'] += $val[csf("qnty")];
			}


			$receive_mst_id_arr[$val[csf("id")]] = $val[csf("id")];

			$pre_rcv_arr[$val[csf("id")]][$val[csf("body_part_id")]][$val[csf("prod_id")]][$val[csf('batch_id')]][$val[csf('fabric_shade')]]['original_width'] = $val[csf("original_width")];
		}

		if($receive_basis==2 && $is_sample==0)
		{
			$pre_return_sql = sql_select("SELECT a.received_id, b.prod_id, b.uom, b.body_part_id, c.detarmination_id, c.gsm, c.dia_width, c.color, b.batch_id, b.fabric_shade, b.issue_qnty, b.width_type, d.quantity, e.job_no_mst from inv_issue_master a, inv_finish_fabric_issue_dtls b, product_details_master c, order_wise_pro_details d, wo_po_break_down e where a.id=b.mst_id and a.entry_form=46 and b.prod_id=c.id and b.trans_id=d.trans_id and d.entry_form=46 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.po_breakdown_id=e.id and a.received_id in (".implode(',', $receive_mst_id_arr).")");
		}
		else
		{
			$pre_return_sql = sql_select("SELECT a.received_id, b.prod_id, b.uom, b.body_part_id, c.detarmination_id, c.gsm, c.dia_width, c.color, b.batch_id, b.fabric_shade, b.issue_qnty, b.width_type  from inv_issue_master a, inv_finish_fabric_issue_dtls b, product_details_master c where a.id=b.mst_id and a.entry_form=46 and b.prod_id=c.id and b.status_active=1 and b.is_deleted=0 and a.received_id in (".implode(',', $receive_mst_id_arr).")");
		}


		foreach ($pre_return_sql as $val)
		{
			if(isset($pre_rcv_arr[$val[csf("received_id")]][$val[csf("body_part_id")]][$val[csf("prod_id")]][$val[csf("batch_id")]][$val[csf("fabric_shade")]]['original_width']))
			{
				$original_width = $pre_rcv_arr[$val[csf("received_id")]][$val[csf("body_part_id")]][$val[csf("prod_id")]][$val[csf("batch_id")]][$val[csf("fabric_shade")]]['original_width'];

				if($receive_basis==1)
				{
					$pre_rcv_arr[$val[csf("detarmination_id")]][$original_width][$val[csf("gsm")]][$val[csf("uom")]][$val[csf("color")]]['ret_qnty']+= $val[csf("issue_qnty")];
				}
				else if($receive_basis==2 && $is_sample==0)
				{
					//$pre_rcv_arr[$val[csf("body_part_id")]][$val[csf("detarmination_id")]][$original_width][$val[csf("gsm")]][$val[csf("uom")]][$val[csf("color")]]['ret_qnty']+= $val[csf("issue_qnty")];
					
					$pre_rcv_arr_job_wise[$val[csf("body_part_id")]][$val[csf("detarmination_id")]][$original_width][$val[csf("gsm")]][$val[csf("uom")]][$val[csf("color")]][$val[csf("job_no_mst")]]['ret_qnty']+= $val[csf("quantity")];
				}
				else
				{
					$pre_rcv_arr[$val[csf("body_part_id")]][$val[csf("detarmination_id")]][$original_width][$val[csf("gsm")]][$val[csf("uom")]][$val[csf("color")]]['ret_qnty']+= $val[csf("issue_qnty")];
				}
			}
		}
	}


	/*echo "<pre>";
	print_r($pre_rcv_arr);
	die;*/




	$data_array=sql_select($sql);

	if($receive_basis==1)
	{
		$batch_dispaly="style='display:none'";
		$dia_w_type_dispaly="style='display:none'";
		$shade_display = "style='display:none'";
		$wo_pi_dispaly="";

	}else if($receive_basis==9)
	{
		$batch_dispaly="";
		$dia_w_type_dispaly = "";
		$wo_pi_dispaly="style='display:none'";
	}else if($receive_basis ==14)
	{
		$dia_w_type_dispaly = "";
		$batch_dispaly="style='display:none'";
		$wo_pi_dispaly="style='display:none'";
	}else if($receive_basis ==10)
	{
		$dia_w_type_dispaly = "";
		$batch_dispaly="";
		$shade_display = "";
		$wo_pi_dispaly="style='display:none'";
	}
	else
	{
		$batch_dispaly="style='display:none'";
		$dia_w_type_dispaly="style='display:none'";
		$shade_display = "style='display:none'";
		$wo_pi_dispaly="style='display:none'";
	}

	$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$company_id and variable_list=23 and category in (2) and status_active =1 and is_deleted=0 order by id desc");
	$over_receive_limit =$variable_set_invent[0][csf('over_rcv_percent')]*1;

	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="660">
		<thead>
			<th width="30">SL</th>
			<th width="70" <? echo $batch_dispaly; ?>>Batch</th>
			<th width="70" <? echo $wo_pi_dispaly; ?>>WO/PI</th>
			<th width="200">Fabric Description</th>
			<th width="40">UOM</th>
			<th width="70" <? echo $dia_w_type_dispaly; ?>>Dia/ W. Type</th>
			<th width="50" <? echo $shade_display; ?>>Shade</th>
			<th width="85">Fabric Color</th>
			<th width="80">Color Type</th>
			<th width="80">Int. Ref.</th>
			<th width="80">Style. Ref.</th>
			<th width="50">Qnty</th>
			<?
			if($receive_basis!=9)
			{
			?>
			<th title="Net Receive=Cum Recv-Return" width="50">Net Receive</th>
			<th title="Bal=Qnty-Net Receive" width="50">Bal</th>
			<?
			}
			?>
		</thead>
	</table>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="660" >
		<tbody id="tbl_list_search_view">
			<?
			$i=1;
			foreach($data_array as $row)
			{
				if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
				$pi_basis_id=$row[csf('pi_basis_id')];
				$work_order_no=$row[csf('work_order_no')];
				$pi_number=$row[csf('pi_number')];

				//$job_no=$work_order_job[$work_order_no];
				$pi_job_nos= array_unique(explode(",",chop($work_order_job[$work_order_no],",")));
				//$job_no= implode(",",$pi_job_nos);

				$fabric_desc=''; $rate=0;
				if($receive_basis!=1)
				{
					$fabric_desc=$body_part[$row[csf('body_part_id')]].", ";
				}

				if($receive_basis==2 && $is_sample==1 && ($row[csf('determination_id')]==0 || $row[csf('determination_id')]==""))
				{
					$fabric_desc.=$row[csf('construction')].", ".$row[csf('composition')].", ".$row[csf('gsm_weight')];
				}
				else if($receive_basis==9)
				{
					if($row[csf('determination_id')]==0 || $row[csf('determination_id')]=="")
					{
						$fabric_desc.=$cons_comps_arr[$row[csf('prod_id')]].", ".$row[csf('gsm_weight')];
					}
					else
					{
						$fabric_desc.=$composition_arr[$row[csf('determination_id')]].", ".$row[csf('gsm_weight')];
					}
				}
				else
				{
					$fabric_desc.=$composition_arr[$row[csf('determination_id')]].", ".$row[csf('gsm_weight')];
				}

				if($row[csf('dia_width')]!="")
				{
					$fabric_desc.=", ".$row[csf('dia_width')];
				}

				if($receive_basis==9)
				{
					$data=$row[csf('id')];
				}
				else
				{
					if($receive_basis==1)
					{
						// echo "string1";
						$rate=$row[csf('rate')];
						$txt_order_rate=$row[csf('rate')];
					}
					else
					{
						if($receive_basis==11)
						{
							$rate='';
							if($is_sample ==1)
							{
								$service_rate= $row[csf('amount')]/$row[csf('qnty')];
							}
							else
							{
								$service_rate=$row[csf('rate')];
							}
						}
						else
						{
							$txt_order_rate=$row[csf('rate')];
							$rate=$row[csf('rate')];
						}
					}
					$all_po_id=$all_po_num="";
					if($receive_basis==2 && $is_sample==1 && ($row[csf('determination_id')]==0 || $row[csf('determination_id')]==""))
					{
						$cons_comp=$row[csf('construction')].", ".$row[csf('composition')];
						$data=$row[csf('body_part_id')]."**".$cons_comp."**".$row[csf('gsm_weight')]."**".$row[csf('dia_width')]."**".$row[csf('determination_id')]."**".$rate."**".$row[csf('currency_id')]."******".$row[csf('uom')]."**".$color_arr[$row[csf('color_id')]]."****".$txt_order_rate;
					}
					else
					{
						// echo "string2";
						$int_ref="";
						if($receive_basis==2)
						{
							$job_no=$row[csf("job_no")];
							$buyer_id = $row[csf("buyer_id")];
							$int_ref=implode(",", array_unique(explode(",", chop($int_ref_arr[$row[csf("job_no")]],","))));
							$style_ref=implode(",", array_unique(explode(",", chop($style_ref_arr[$row[csf("job_no")]],","))));
							//$color_type_ref=implode(",", array_unique(explode(",", chop($color_type_arr[$row[csf("job_no")]],","))));

							$color_type_ref= implode(",", array_unique(explode(",", chop($color_type_fabrication_body_arr[$row[csf("job_no")]][$row[csf("color_id")]][$row[csf("body_part_id")]][$row[csf("determination_id")]],","))));
						}
						else if($receive_basis==11)
						{
							$int_ref=implode(",", array_unique(explode(",", chop($int_ref_arr[$row[csf("booking_no")]],","))));
							$style_ref=implode(",", array_unique(explode(",", chop($style_ref_arr[$row[csf("booking_no")]],","))));
							$job_no=implode(",", array_unique(explode(",", chop($service_job_arr[$row[csf("booking_no")]],","))));
							$color_type_ref=$color_type[$row[csf("color_type_id")]];
							
						}
						else{
							$buyer_id = $work_order_buyer[$work_order_no];
							$int_ref=implode(",", array_unique(explode(",", chop($int_ref_arr[$work_order_no],","))));
							$style_ref=implode(",", array_unique(explode(",", chop($style_ref_arr[$work_order_no],","))));
							$color_type_ref=implode(",", array_unique(explode(",", chop($color_type_arr[$work_order_no],","))));
						}

						if($receive_basis==1 && $row[csf("type")]==1)
						{
							$all_po_id= implode(",",array_unique(explode(",",chop($book_order_data[$row[csf("work_order_no")]][$row[csf("fabric_construction")]][$row[csf("fabric_composition")]][$row[csf("color_id")]][$row[csf("gsm")]][$row[csf("dia_width")]]["all_po_id"],","))));

							$all_po_num=implode(",",array_unique(explode(",",chop($book_order_data[$row[csf("work_order_no")]][$row[csf("fabric_construction")]][$row[csf("fabric_composition")]][$row[csf("color_id")]][$row[csf("gsm")]][$row[csf("dia_width")]]["all_po_num"],","))));
							$job_no=implode(",",array_unique(explode(",",chop($book_order_data[$row[csf("work_order_no")]][$row[csf("fabric_construction")]][$row[csf("fabric_composition")]][$row[csf("color_id")]][$row[csf("gsm")]][$row[csf("dia_width")]]["job_no"],","))));
						}

						if($receive_basis==1 && $row[csf("type")]==1 && $is_sample ==1)
						{
							$body_part_ids=$body_part_id_Arr[$row[csf("work_order_no")]][$row[csf('gsm')]][$row[csf('fabric_construction')]][$row[csf('fabric_composition')]];
						}
						else
						{
							//$body_part_ids=$body_part_id_Arr[$job_no][$row[csf('gsm')]][$row[csf('fabric_construction')]][$row[csf('fabric_composition')]];

							$body_part_ids="";
							foreach ($pi_job_nos as $JOBNO) {
								if($body_part_ids=="")
								{
									$body_part_ids =$body_part_id_Arr[$JOBNO][$row[csf('gsm')]][$row[csf('fabric_construction')]][$row[csf('fabric_composition')]][$row[csf('color_id')]];
								}else{
									$body_part_ids .=$body_part_id_Arr[$JOBNO][$row[csf('gsm')]][$row[csf('fabric_construction')]][$row[csf('fabric_composition')]][$row[csf('color_id')]].",";
								}

							}

						}

						//print_r($body_part_ids);
						$body_part_id = implode(",",array_unique(explode(",",chop($body_part_ids,","))));

						$data=$row[csf('body_part_id')]."**".$composition_arr[$row[csf('determination_id')]]."**".$row[csf('gsm_weight')]."**".$row[csf('dia_width')]."**".$row[csf('determination_id')]."**".$rate."**".$row[csf('currency_id')]."**".$all_po_id."**".$all_po_num."**".$row[csf('uom')]."**".$color_arr[$row[csf('color_id')]]."**".$body_part_id."**".$txt_order_rate."**".$pi_basis_id."**".$row[csf('dia_width_type')]."**".$row[csf('pre_cost_fab_conv_cost_dtls_id')]."**".$row[csf('color_id')]."**".$row[csf("work_order_no")]."**".$row[csf("work_order_id")]."**".$row[csf("pi_dtls_id")]."**".$row[csf("fabric_construction")]."**".$row[csf("fabric_composition")]."**".$buyer_id."**".$buyer_arr[$buyer_id]."**".$job_no."**".$service_rate;
					}

					if($receive_basis==14) // fabric Sales order
					{
						$data=$row[csf('body_part_id')]."**".$composition_arr[$row[csf('determination_id')]]."**".$row[csf('gsm_weight')]."**".$row[csf('dia_width')]."**".$row[csf('determination_id')]."**".$rate."**".$row[csf('currency_id')]."**".$all_po_id."**".$all_po_num."**".$row[csf('uom')]."**".$color_arr[$row[csf('color_id')]]."**".$body_part_id."**".$txt_order_rate."**".$row[csf('mst_id')]."**".$row[csf('dia_width_type')]."**".$row[csf('company_id')]."**".$row[csf('within_group')]."**".$row[csf('buyer_id')]."**".$row[csf('color_id')];
					}
					if($receive_basis==10) // Delivery From Textile(fabric Sales order)
					{

						//echo '=========';
						$is_sales = 1;
						$avg_rate = $sales_rate_arr[$row[csf('order_id')]][$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]*1;
						$data=$row[csf('body_part_id')]."**".$composition_arr[$row[csf('determination_id')]]."**".$row[csf('gsm_weight')]."**".$row[csf('dia_width')]."**".$row[csf('determination_id')]."**".$rate."**".$row[csf('currency_id')]."**".$all_po_id."**".$all_po_num."**".$row[csf('uom')]."**".$color_arr[$row[csf('color_id')]]."**".$row[csf('body_part_id')]."**".$txt_order_rate."**".$row[csf('mst_id')]."**".$row[csf('dia_width_type')]."**".$row[csf('company_id')]."**".$row[csf('within_group')]."**".$row[csf('buyer_id')]."**".$batch_arr[$row[csf('batch_id')]]."**".$row[csf('batch_id')]."**".$is_sales."**".$row[csf('product_id')]."**".$row[csf('order_id')]."**".$row[csf('color_id')]."**".$productionData[$row[csf('sys_dtls_id')]]['fabric_shade']."**".$productionData[$row[csf('sys_dtls_id')]]['no_of_roll']."**".$row[csf('fabric_shade')]."**".$avg_rate;
					}
				}



				if($receive_basis==1 || $receive_basis==2 || $receive_basis==9 || $receive_basis==11)
				{
					if($row[csf("dia_width")] ==""){
						$dia_for_rcv_check = 0;
					}else{
						$dia_for_rcv_check = $row[csf("dia_width")];
					}

					if($receive_basis==1 )
					{
						$receive_qnty = $pre_rcv_arr[$row[csf("determination_id")]][$row[csf("pi_dtls_id")]][$dia_for_rcv_check][$row[csf("gsm_weight")]][$row[csf("uom")]][$row[csf("color_id")]]['qnty'];
						$return_qnty = $pre_rcv_arr[$row[csf("determination_id")]][$dia_for_rcv_check][$row[csf("gsm_weight")]][$row[csf("uom")]][$row[csf("color_id")]]['ret_qnty'];
					}
					else if($receive_basis==2 || $receive_basis ==11)
					{
						$receive_qnty = $pre_rcv_arr[$row[csf("body_part_id")]][$row[csf("determination_id")]][$row[csf("pre_cost_fab_conv_cost_dtls_id")]][$dia_for_rcv_check][$row[csf("gsm_weight")]][$row[csf("uom")]][$row[csf("color_id")]]['qnty'];
						//$return_qnty = $pre_rcv_arr[$row[csf("body_part_id")]][$row[csf("determination_id")]][$dia_for_rcv_check][$row[csf("gsm_weight")]][$row[csf("uom")]][$row[csf("color_id")]]['ret_qnty'];

						$return_qnty=0;
						if($receive_basis==2 && $is_sample==0)
						{
							foreach ($order_against_pre_costing[$row[csf("pre_cost_fab_conv_cost_dtls_id")]] as $jobkey => $jobvalue) 
							{
								$return_qnty += $pre_rcv_arr_job_wise[$row[csf("body_part_id")]][$row[csf("determination_id")]][$dia_for_rcv_check][$row[csf("gsm_weight")]][$row[csf("uom")]][$row[csf("color_id")]][$jobkey]['ret_qnty'];
							}
						}
						else
						{
							$return_qnty = $pre_rcv_arr[$row[csf("body_part_id")]][$row[csf("determination_id")]][$dia_for_rcv_check][$row[csf("gsm_weight")]][$row[csf("uom")]][$row[csf("color_id")]]['ret_qnty'];
						}
					}
					else if($receive_basis ==9)
					{
						//echo $row[csf("body_part_id")]."][".$row[csf("determination_id")]."][".$row[csf("batch_id")]."][".$dia_for_rcv_check."][".$row[csf("gsm_weight")]."][".$row[csf("uom")]."][".$row[csf("color_id")]."][".$row[csf("dia_width_type")]."][".$row[csf("fabric_shade")];

						//echo "__".$pre_rcv_arr[1][69][13731][55][180][12][4466][1][0]['qnty'];

						$receive_qnty = $pre_rcv_arr[$row[csf("body_part_id")]][$row[csf("determination_id")]][$row[csf("batch_id")]][$dia_for_rcv_check][$row[csf("gsm_weight")]][$row[csf("uom")]][$row[csf("color_id")]][$row[csf("dia_width_type")]][$row[csf("fabric_shade")]]['qnty'];

						$return_qnty = 0;
					}
					else
					{
						$receive_qnty = $pre_rcv_arr[$row[csf("body_part_id")]][$row[csf("determination_id")]][$dia_for_rcv_check][$row[csf("gsm_weight")]][$row[csf("uom")]][$row[csf("color_id")]]['qnty'];
						$return_qnty = $pre_rcv_arr[$row[csf("body_part_id")]][$row[csf("determination_id")]][$dia_for_rcv_check][$row[csf("gsm_weight")]][$row[csf("uom")]][$row[csf("color_id")]]['ret_qnty'];
					}

					//echo $row[csf("body_part_id")]."][".$row[csf("determination_id")]."][".$row[csf("pre_cost_fab_conv_cost_dtls_id")]."][".$dia_for_rcv_check."][".$row[csf("gsm_weight")]."][".$row[csf("uom")]."][".$row[csf("color_id")]."<br>";
					$net_receive = $receive_qnty - $return_qnty;
				}

				$required_quantity_with_percantase=$row[csf('qnty')];
				if($receive_basis ==1 || $receive_basis ==2 || $receive_basis==11)
				{
					$required_quantity_with_percantase = $row[csf('qnty')] + ($row[csf('qnty')]*$over_receive_limit)/100;
				}

				$balance_quantity = $required_quantity_with_percantase-$net_receive;

				//N. B. In receive basis production balance quantity will  be omitted
				//if($balance_quantity >0 || $receive_basis==9)
				//{
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $data; ?>","<?php echo $row[csf('details_id')]; ?>");' style="cursor:pointer" >
						<td width="30" align="center"><? echo $i; ?></td>
						<?
						if($receive_basis==9 || $receive_basis==10)
						{
							?>
							<td width="70" <? echo $batch_dispaly; ?>><p><? if($receive_basis==9 || $receive_basis==10) echo $batch_arr[$row[csf('batch_id')]]; ?></p></td>
							<?
						}

						if($receive_basis==1)
						{
							?>
							<td width="70" title="pi_basis_id: <?echo $pi_basis_id;?>"><p><? echo ($pi_basis_id==2) ? $pi_number : $work_order_no ; //echo $work_order_no; ?></p></td>
							<?
						}
						?>
						<td width="200"><p><? echo $fabric_desc; ?></p></td>
						<td width="40" align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
						<?
						if($receive_basis==9 || $receive_basis==14 || $receive_basis==10)
						{
							?>
							<td width="70" <? echo $dia_w_type_dispaly; ?> align="center"><? if($receive_basis==9 || $receive_basis == 14 || $receive_basis==10) echo $fabric_typee[$row[csf('dia_width_type')]]; ?></td>

							<?
						}
						if($receive_basis==10 || $receive_basis==9)
						{
							?>
							<td width="50" align="center" <? echo $shade_display; ?>> <?php echo $fabric_shade[$row[csf('fabric_shade')]]; ?> </td>

							<?
						}
						?>

						<td width="85" align="center"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
						<td width="80" align="center"><? echo $color_type_ref; ?></td>
						<td width="80" align="center"><p><? echo $int_ref; ?></p></td>
						<td width="80" align="center"><p><? echo $style_ref; ?></p></td>
						<td width="50" align="right" title='<? echo "with over percentage : ".$required_quantity_with_percantase ?>' ><? echo number_format($row[csf('qnty')],2); ?></td>
						<?
						if($receive_basis!=9)
						{
							//N. B. in production basis balance will not show because specific production id's balance will differ from PO POPUP (required quantity - cummulatative receive quantity) balance
						?>
						<td width="50" align="right" title="<? echo 'rcv='.$receive_qnty.', return='.$return_qnty; ?>"><? echo number_format($net_receive,2); ?></td>
						<td width="50" align="right"><? echo number_format($balance_quantity,2); ?></td>
						<?
						}
						?>
					</tr>
					<?
				$i++;
				//}
			}
			?>
		</tbody>
	</table>
	<?
	exit();
}

if($action=='populate_data_from_production')
{
	$data=explode("**",$data);
	$id=$data[0];
	$roll_maintained=$data[1];
	$process_costing_maintain=$data[2];
	//$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst","id","batch_no");

	$batch_sql  = sql_select("select id, batch_no, booking_no, booking_no_id from pro_batch_create_mst ");
	foreach ($batch_sql as $val)
	{
		$batch_arr[$val[csf("id")]] = $val[csf("batch_no")];
		$batch_booking_arr[$val[csf("id")]]["booking_no"] = $val[csf("booking_no")];
		$batch_booking_arr[$val[csf("id")]]["booking_no_id"] = $val[csf("booking_no_id")];
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');

	$data_array=sql_select("select id, trans_id, prod_id, batch_id, body_part_id, fabric_description_id, gsm, width, color_id, receive_qnty, reject_qty, no_of_roll, machine_no_id, rack_no, shelf_no,room,floor, order_id, buyer_id,dia_width_type,grey_used_qty,fabric_shade,uom from pro_finish_fabric_rcv_dtls where id='$id' and is_sales=0");


	foreach($data_array as $row)
	{
		$buyer_name='';
		$buyer=explode(",",$row[csf('buyer_id')]);
		foreach($buyer as $val )
		{
			if($buyer_name=='') $buyer_name=$buyer_arr[$val]; else $buyer_name.=",".$buyer_arr[$val];
		}

		$booking_without_order=return_field_value("booking_without_order","pro_batch_create_mst","id=".$row[csf("batch_id")]."");
		if($booking_without_order==1)
		{
			echo "$('#txt_production_qty').removeAttr('readonly','readonly');\n";
			echo "$('#txt_production_qty').removeAttr('onClick','onClick');\n";
			echo "$('#txt_production_qty').removeAttr('placeholder','placeholder');\n";


			echo "$('#txt_used_qty').removeAttr('placeholder','placeholder');\n";
			echo "$('#txt_used_qty').removeAttr('onClick','onClick');\n";
			echo "document.getElementById('txt_used_qty').value = '".$row[csf("grey_used_qty")]."';\n";
		}
		else
		{
			echo "$('#txt_production_qty').attr('readonly','readonly');\n";
			echo "$('#txt_production_qty').attr('onClick','openmypage_po();');\n";
			echo "$('#txt_production_qty').attr('placeholder','Single Click');\n";

			echo "document.getElementById('txt_used_qty').value = '';\n";
			if($process_costing_maintain==1)
			{
				echo "$('#txt_used_qty').attr('readonly','readonly');\n";
				echo "$('#txt_used_qty').attr('onClick','proces_costing_popup();');\n";
				echo "$('#txt_used_qty').attr('placeholder','Browse');\n";
			}
			else
			{
				echo "$('#txt_used_qty').removeAttr('readonly','readonly');\n";
				echo "$('#txt_used_qty').removeAttr('onClick','onClick');\n";
				echo "$('#txt_used_qty').removeAttr('placeholder','Browse');\n";
			}
		}

		$comp='';
		if($row[csf('fabric_description_id')]==0 || $row[csf('fabric_description_id')]=="")
		{
			$comp = return_field_value("item_description","product_details_master","id=".$row[csf('prod_id')]);
		}
		else
		{
			$determination_sql=sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=".$row[csf('fabric_description_id')]." order by b.id asc");

			if($determination_sql[0][csf('construction')]!="")
			{
				$comp=$determination_sql[0][csf('construction')].", ";
			}

			foreach( $determination_sql as $d_row )
			{
				$comp.=$composition[$d_row[csf('copmposition_id')]]." ".$d_row[csf('percent')]."% ";
			}
		}
		if($row[csf("order_id")]!="")
		{
			if($db_type==0)
			{
				$order_nos=return_field_value("group_concat(po_number)","wo_po_break_down","id in(".$row[csf("order_id")].")");
			}
			else
			{
				$order_nos=return_field_value("LISTAGG(cast(po_number as varchar2(4000)),',') WITHIN GROUP (ORDER BY id) as po_number","wo_po_break_down","id in(".$row[csf("order_id")].")","po_number");
			}
		}

		echo "document.getElementById('fin_prod_dtls_id').value 			= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_batch_id').value 				= '".$row[csf("batch_id")]."';\n";
		echo "document.getElementById('txt_batch_no').value 				= '".$batch_arr[$row[csf("batch_id")]]."';\n";
		echo "document.getElementById('cbo_body_part').value 				= '".$row[csf("body_part_id")]."';\n";
		echo "document.getElementById('txt_fabric_desc').value 				= '".$comp."';\n";
		echo "document.getElementById('fabric_desc_id').value 				= '".$row[csf("fabric_description_id")]."';\n";
		echo "document.getElementById('txt_color').value 					= '".$color_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('txt_color_id').value 				= '".$row[csf("color_id")]."';\n";
		echo "document.getElementById('txt_gsm').value 						= '".$row[csf("gsm")]."';\n";
		echo "document.getElementById('txt_dia_width').value 				= '".$row[csf("width")]."';\n";

		echo "document.getElementById('txt_original_dia_width').value 		= '".$row[csf("width")]."';\n";
		echo "document.getElementById('txt_dia_width_show').value 			= '".$row[csf("width")]."';\n";

		echo "document.getElementById('product_id').value 					= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('txt_production_qty').value 			= '".$row[csf("receive_qnty")]."';\n";
		echo "document.getElementById('check_production_qty').value 		= '".$row[csf("receive_qnty")]."';\n";
		echo "document.getElementById('txt_reject_qty').value 				= '".$row[csf("reject_qty")]."';\n";
		echo "document.getElementById('txt_no_of_roll').value 				= '".$row[csf("no_of_roll")]."';\n";
		echo "document.getElementById('buyer_name').value 					= '".$buyer_name."';\n";
		echo "document.getElementById('buyer_id').value 					= '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('cbo_machine_name').value 			= '".$row[csf("machine_no_id")]."';\n";
		echo "document.getElementById('cbo_floor').value 					= '".$row[csf("floor")]."';\n";
		echo "document.getElementById('cbo_room').value 					= '".$row[csf("room")]."';\n";
		echo "document.getElementById('txt_rack').value 					= '".$row[csf("rack_no")]."';\n";
		echo "document.getElementById('txt_shelf').value 					= '".$row[csf("shelf_no")]."';\n";
		echo "document.getElementById('all_po_id').value 					= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('hidden_order_id').value 				= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('cbo_dia_width_type').value 			= '".$row[csf("dia_width_type")]."';\n";
		echo "document.getElementById('txt_order_no').value 				= '".$order_nos."';\n";
		echo "document.getElementById('txt_fabric_color_id').value 			= '".$row[csf("color_id")]."';\n";
		echo "document.getElementById('cbo_fabric_type').value 				= '".$row[csf("fabric_shade")]."';\n";
		echo "document.getElementById('cbouom').value 						= '".$row[csf("uom")]."';\n";
		echo "document.getElementById('txt_po_booking_no').value 			= '".$batch_booking_arr[$row[csf("batch_id")]]["booking_no"]."';\n";
		echo "document.getElementById('hdn_booking_id').value 				= '".$batch_booking_arr[$row[csf("batch_id")]]["booking_no_id"]."';\n";

		echo "document.getElementById('booking_without_order').value 		= '".$booking_without_order."';\n";

		//echo "document.getElementById('finish_production_dtls_id').value 	= '".$row[csf("id")]."';\n";

		echo "$('#txt_gsm').attr('disabled','disabled');\n";
		echo "$('#txt_dia_width_show').attr('disabled','disabled');\n";
		echo "$('#txt_dia_width').attr('disabled','disabled');\n";
		echo "$('#cbouom').attr('disabled','disabled');\n";
		echo "$('#cbo_fabric_type').attr('disabled','disabled');\n";
		echo "$('#txt_color').attr('disabled','disabled');\n";

		$save_string='';
		//$all_po_id='';
		$dataPoArray=sql_select("select po_breakdown_id, quantity from order_wise_pro_details where dtls_id='$id' and entry_form=7 and status_active=1 and is_deleted=0 and is_sales=0");
		foreach($dataPoArray as $row_po)
		{
			if($save_string=="")
			{
				$save_string=$row_po[csf("po_breakdown_id")]."**".$row_po[csf("quantity")];
			}
			else
			{
				$save_string.=",".$row_po[csf("po_breakdown_id")]."**".$row_po[csf("quantity")];
			}
		}
		if($row[csf("order_id")]!="")
		{
			$job_no=return_field_value("a.job_no_mst","wo_po_break_down a", " a.id in (".$row[csf("order_id")].") ","job_no_mst");
			echo "document.getElementById('txt_job_no').value 				= '".$job_no."';\n";
		}
		/*if($roll_maintained==1)
		{
			$data_roll_array=sql_select("select id, po_breakdown_id, qnty,roll_no from pro_roll_details where dtls_id='$id' and entry_form=4 and status_active=1 and is_deleted=0");
			foreach($data_roll_array as $row_roll)
			{
				if($save_string=="")
				{
					$save_string=$row_roll[csf("po_breakdown_id")]."**".$row_roll[csf("qnty")]."**".$row_roll[csf("roll_no")];
				}
				else
				{
					$save_string.=",".$row_roll[csf("po_breakdown_id")]."**".$row_roll[csf("qnty")]."**".$row_roll[csf("roll_no")];
				}
			}
		}
		else
		{
			$data_po_array=sql_select("select po_breakdown_id, quantity from order_wise_pro_details where dtls_id='$id' and entry_form=7 and status_active=1 and is_deleted=0");
			foreach($data_po_array as $row_po)
			{
				if($save_string=="")
				{
					$save_string=$row_po[csf("po_breakdown_id")]."**".$row_po[csf("quantity")];
				}
				else
				{
					$save_string.=",".$row_po[csf("po_breakdown_id")]."**".$row_po[csf("quantity")];
				}
			}
		}*/

		echo "document.getElementById('save_data').value 				= '".$save_string."';\n";


		exit();
	}
}

if ($action=="fabricDescription_popup")
{
	echo load_html_head_contents("Fabric Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>

		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});

		function js_set_value(comp,gsm,detarmination_id,dia)
		{
			$('#hidden_desc_no').val(comp);
			$('#hidden_gsm').val(gsm);
			$('#fabric_desc_id').val(detarmination_id);
			$('#dia_width').val(dia);
			parent.emailwindow.hide();
		}

	</script>

</head>

<body>
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:570px;margin-left:10px">
			<input type="hidden" name="hidden_desc_no" id="hidden_desc_no" class="text_boxes" value="">
			<input type="hidden" name="hidden_gsm" id="hidden_gsm" class="text_boxes" value="">
			<input type="hidden" name="hidden_dia_width" id="hidden_dia_width" class="text_boxes" value="">
			<input type="hidden" name="fabric_desc_id" id="fabric_desc_id" class="text_boxes" value="">
			<input type="hidden" name="dia_width" id="dia_width" class="text_boxes" value="">

			<div style="margin-left:10px; margin-top:10px">
				<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="530">
					<thead>
						<th width="40">SL</th>
						<th width="120">Construction</th>
						<th>Composition</th>
						<th width="100">GSM/Weight</th>
						<th width="50">Dia Width</th>
					</thead>
				</table>
				<div style="width:550px; max-height:300px; overflow-y:scroll" id="list_container" align="left">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="530" id="tbl_list_search">
						<?
						$i=1;
						$composition_arr=array();
						$compositionData=sql_select("select mst_id, copmposition_id, percent from lib_yarn_count_determina_dtls where status_active=1 and is_deleted=0");
						foreach( $compositionData as $row )
						{
							$composition_arr[$row[csf('mst_id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
						}

						//$data_array=sql_select("select id, construction, gsm_weight from lib_yarn_count_determina_mst where fab_nature_id=2 and status_active=1 and is_deleted=0");

						if($all_po_id)
						{
							$data_array=sql_select("select x.* from (select  d.id, d.construction, d.gsm_weight, b.dia_width from wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b , lib_yarn_count_determina_mst d where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0  and b.status_active = 1 and b.is_deleted = 0 and b.pre_cost_fabric_cost_dtls_id = a.id and b.po_break_down_id in ($all_po_id) and b.booking_no = '$txt_po_booking_no' and b.booking_type =1 and a.lib_yarn_count_deter_id = d.id and d.status_active =1 and d.is_deleted =0 and d.fab_nature_id=2 union all select d.id, d.construction, d.gsm_weight, c.dia_width from wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_booking_dtls c, lib_yarn_count_determina_mst d where b.job_no=c.job_no and a.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.pre_cost_fabric_cost_dtls_id = a.id and c.po_break_down_id in ($all_po_id) and c.booking_no = '$txt_po_booking_no' and a.fabric_description = b.id and c.booking_type = 4 and b.lib_yarn_count_deter_id = d.id and d.status_active =1 and d.is_deleted =0 and d.fab_nature_id=2 ) x group by x.id, x.construction, x.gsm_weight, x.dia_width");
						}
						else
						{
							$data_array=sql_select("select d.id, d.construction, d.gsm_weight, a.dia_width
								from wo_non_ord_samp_booking_dtls a, lib_yarn_count_determina_mst d
								where a.lib_yarn_count_deter_id = d.id and a.booking_no= '$txt_sample_booking_no'");
						}

						foreach($data_array as $row)
						{
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";

	                            /*$construction=$row[csf('construction')]; $comp='';
	                            $determ_sql=sql_select("select copmposition_id, percent from lib_yarn_count_determina_dtls where mst_id=".$row[csf('id')]." and status_active=1 and is_deleted=0");
	                            foreach( $determ_sql as $d_row )
	                            {
	                                $comp.=$composition[$d_row[csf('copmposition_id')]]." ".$d_row[csf('percent')]."% ";
	                            }
	                            $cons_comp=$construction.", ".$comp;*/

	                            $construction=$row[csf('construction')];
	                            $comp=$composition_arr[$row[csf('id')]];
	                            $cons_comp=$construction.", ".$comp;
	                            ?>
	                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $cons_comp; ?>','<? echo $row[csf('gsm_weight')]; ?>','<? echo $row[csf('id')]; ?>','<? echo $row[csf('dia_width')]?>')" style="cursor:pointer" >
	                            	<td width="40"><? echo $i; ?></td>
	                            	<td width="120"><p><? echo $construction; ?></p></td>
	                            	<td><p><? echo $comp; ?></p></td>
	                            	<td width="100"><? echo $row[csf('gsm_weight')]; ?></td>
	                            	<td width="50"><? echo $row[csf('dia_width')]; ?></td>
	                            </tr>
	                            <?
	                            $i++;
	                        }
	                        ?>
	                    </table>
	                </div>
	            </div>
	        </fieldset>
	    </form>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	//$variable_set_invent=return_field_value("user_given_code_status","variable_settings_inventory","company_name=$cbo_company_id and variable_list=19 and item_category_id=2","user_given_code_status");

	$vari_sql_inventory = sql_select("select variable_list, item_category_id, auto_transfer_rcv, user_given_code_status
	from variable_settings_inventory where company_name=$cbo_company_id and variable_list in (19,42)
	and status_active=1");

	foreach($vari_sql_inventory as $row)
	{
		if($row[csf("variable_list")] ==19 && $row[csf("item_category_id")]==2)
		{
			$variable_set_invent = $row[csf("user_given_code_status")];
		}
		if($row[csf("variable_list")] ==42)
		{
			$mrr_wise_balancing_maintain = $row[csf("auto_transfer_rcv")];
		}
	}

	if(	$mrr_wise_balancing_maintain !=1)
	{
		$mrr_wise_balancing_maintain=0;
	}

	if(str_replace("'",'',$update_id) !="" && $mrr_wise_balancing_maintain==1)
	{
		$sql_pre_batch = sql_select("select batch_id from pro_finish_fabric_rcv_dtls where mst_id=$update_id and status_active=1 and is_deleted=0");

		$noOfDtlsSavedInMRR = 0;
		foreach ($sql_pre_batch as  $val)
		{
			$pre_batch_arr[$val[csf("batch_id")]] = $val[csf("batch_id")];
			$noOfDtlsSavedInMRR +=1;
		}
	}

	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');

	$cbo_bin = str_replace("'", "", $cbo_bin);
	$txt_shelf = str_replace("'", "", $txt_shelf);
	$txt_rack = str_replace("'", "", $txt_rack);
	$cbo_room = str_replace("'", "", $cbo_room);
	$cbo_floor = str_replace("'", "", $cbo_floor);
	$hdn_is_sales = str_replace("'", "", $hdn_is_sales);


	$txt_dia_width = strtoupper($txt_dia_width); //For avoiding case sensitivity of dia. 05/08/2023
	$txt_original_dia_width = strtoupper($txt_original_dia_width); 

	if($cbo_bin==""){$cbo_bin=0;}
	if($txt_shelf==""){$txt_shelf=0;}
	if($txt_rack==""){$txt_rack=0;}
	if($cbo_room==""){$cbo_room=0;}
	if($cbo_floor==""){$cbo_floor=0;}
	if($hdn_is_sales==""){$hdn_is_sales=0;}
	$hdn_is_sales=0;

	if (str_replace("'",'',$update_id) !='')
	{
		$is_audited=return_field_value("is_audited","inv_receive_master","id=".str_replace("'",'',$update_id)." and status_active=1 and is_deleted=0","is_audited");
		//echo "10**$is_audited".'rakib';die;
		if($is_audited==1) {
			echo "60**This MRR is Audited. Save, Update and Delete Not Allowed..";
			die;
		}

		//bill check start
		$sql_d_n_f_bill = sql_select("SELECT a.bill_no 
		from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b, order_wise_pro_details c, pro_finish_fabric_rcv_dtls d, inv_receive_master e
		where a.id = b.mst_id and a.process_id =4 and b.status_active =1 and a.bill_for=1 and b.RECEIVE_ID=c.id and c.trans_id=d.TRANS_ID and c.DTLS_ID=d.ID and d.mst_id=e.id and e.entry_form=37 and e.id=$update_id
		union all
		select a.bill_no 
		from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b, pro_finish_fabric_rcv_dtls c, inv_receive_master e where a.id = b.mst_id and a.process_id =4 and b.status_active =1 and a.bill_for=3 and b.RECEIVE_ID=c.id and c.mst_id=e.id and e.entry_form=37 and e.id=$update_id");
		if($sql_d_n_f_bill[0][csf("bill_no")] != "")
		{
			echo "14**Dyeing and Finishing Bill Found. Update Not Allow.\nBill no: ".$sql_d_n_f_bill[0][csf("bill_no")];disconnect($con);
			die;
		}
		//bill check end
	}

	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=$fabric_desc_id and b.status_active=1 and b.is_deleted=0 order by b.id asc";
	$data_array=sql_select($sql_deter);
	$feb_composition=$feb_construction="";
	foreach( $data_array as $row )
	{
		$feb_construction=$row[csf('construction')];
		if($feb_composition=="") $feb_composition=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%"; else $feb_composition.=" ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
	}

	$all_po_idss=explode("_",str_replace("'","",$all_po_id));
	if((str_replace("'","",$cbo_receive_basis)==2 || str_replace("'","",$cbo_receive_basis)==4 || str_replace("'","",$cbo_receive_basis)==6 || str_replace("'","",$cbo_receive_basis)==9 || str_replace("'","",$cbo_receive_basis)==11) && str_replace("'","",$booking_without_order)==1)
	{
		if(str_replace("'","",$cbo_receive_basis)==2 )
		{
			/*$reqQnty = "select a.id as po_break_down_id, sum(b.finish_fabric) as fabric_qty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.construction='$feb_construction' and b.composition='$feb_composition' and a.id=$txt_booking_no_id group by a.id";*/
			$reqQnty = "SELECT a.id as po_break_down_id, sum(b.finish_fabric) as fabric_qty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.lib_yarn_count_deter_id=$fabric_desc_id and a.id=$txt_booking_no_id group by a.id";
		}
		else if(str_replace("'","",$cbo_receive_basis)==9 )
		{
			//$reqQnty = "select a.id as po_break_down_id, sum(b.finish_fabric) as fabric_qty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.construction='$feb_construction' and b.composition='$feb_composition' and a.id=$hdn_booking_id group by a.id";

			$reqQnty = "select a.id as po_break_down_id, sum(b.finish_fabric) as fabric_qty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.lib_yarn_count_deter_id=$fabric_desc_id and a.id=$hdn_booking_id group by a.id";



		}
		else if(str_replace("'","",$cbo_receive_basis)==11)
		{
			//$reqQnty = "select a.id as po_break_down_id, sum(b.wo_qty) as fabric_qty from wo_non_ord_knitdye_booking_mst a, wo_non_ord_knitdye_booking_dtl b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.construction='$feb_construction' and b.composition='$feb_composition' and a.id=$txt_booking_no_id and a.booking_no = $txt_booking_no group by a.id";

			if($db_type ==0)
			{
				$service_dia_cond = " and upper(c.dia_width) =".$txt_original_dia_width;
			}
			else
			{
				if(str_replace("'", "", $txt_original_dia_width) != ""){
					$service_dia_cond = " and upper(c.dia_width) =".$txt_original_dia_width;
				}else{
					$service_dia_cond = " and c.dia_width is null";
				}
			}

			$reqQnty = "select a.id as po_break_down_id, sum(b.wo_qty) as fabric_qty
			from wo_non_ord_knitdye_booking_mst a, wo_non_ord_knitdye_booking_dtl b, wo_non_ord_samp_booking_dtls c
			where a.id=b.mst_id and b.fab_des_id=c.id and b.fabric_source=1 and b.process_id not in(1) and a.status_active=1
			and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no=$txt_booking_no and a.id = $txt_booking_no_id and c.lib_yarn_count_deter_id = $fabric_desc_id and c.body_part =$cbo_body_part and c.gsm_weight= $txt_gsm $service_dia_cond and b.uom = $cbouom and c.fabric_color =$fabric_color_id
			group by a.id";

		}
		else
		{
			//$reqQnty = "select a.id as po_break_down_id, sum(b.finish_fabric) as fabric_qty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.construction='$feb_construction' and b.composition='$feb_composition' and a.id=$hidden_sample_booking_id and a.booking_no = $txt_sample_booking_no group by a.id";
			$reqQnty = "select a.id as po_break_down_id, sum(b.finish_fabric) as fabric_qty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.lib_yarn_count_deter_id=$fabric_desc_id and a.id=$hidden_sample_booking_id and a.booking_no = $txt_sample_booking_no group by a.id";
			//echo "10**".$reqQnty;die;

		}
	}
	else
	{
		if(str_replace("'","",$cbo_receive_basis)==11)
		{
			$reqQnty = "select a.po_break_down_id, sum(a.wo_qnty) as fabric_qty
			from wo_booking_dtls a, wo_pre_cost_fab_conv_cost_dtls c, wo_pre_cost_fabric_cost_dtls b
			where a.pre_cost_fabric_cost_dtls_id=c.id and c.fabric_description=b.id  and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and c.id = $pre_cost_fab_conv_cost_dtls_id	and a.po_break_down_id in ($all_po_idss[0]) and b.lib_yarn_count_deter_id = $fabric_desc_id and b.body_part_id = $cbo_body_part and a.fabric_color_id=$fabric_color_id
			group by a.po_break_down_id";
			//and b.gsm_weight = $txt_gsm and a.dia_width = $txt_dia_width
		}
		else if(str_replace("'","",$cbo_receive_basis)==1)
		{
			$po_dtls_cond = ($pi_dtls_id!="") ? " and b.id=$pi_dtls_id" : "";
			$sql_pi="select b.work_order_no,sum(b.quantity) as quantity,a.pi_basis_id from com_pi_master_details a, com_pi_item_details b where a.pi_basis_id in(1,2) and a.id=$txt_booking_no_id and a.id=b.pi_id and b.determination_id=$fabric_desc_id $po_dtls_cond  and b.uom=$cbouom and b.status_active=1 and b.is_deleted=0 group by b.work_order_no,a.pi_basis_id";
			//echo "10**".$sql_pi;die;
			$sql_pi_res=sql_select($sql_pi);
			foreach($sql_pi_res as $pi)
			{
				$pi_req_qty=$pi[csf('quantity')];
				$pi_basis_id=$pi[csf('pi_basis_id')];
				$work_order_no=$pi[csf('work_order_no')];
			}

			if($pi_basis_id==1 && str_replace("'","",$booking_without_order)==0)
			{
				$reqQnty = " select a.po_break_down_id,b.uom, sum(a.fin_fab_qnty) as fabric_qty
				from wo_booking_dtls a,wo_pre_cost_fabric_cost_dtls b
				where a.status_active=1 and a.is_deleted=0 and a.pre_cost_fabric_cost_dtls_id=b.id
				and a.booking_no='$work_order_no'
				and upper(a.dia_width)=$txt_original_dia_width and a.fabric_color_id=$fabric_color_id and b.gsm_weight=$txt_gsm
				and b.uom=$cbouom and b.lib_yarn_count_deter_id=$fabric_desc_id and b.status_active=1 and b.is_deleted=0
				group by a.po_break_down_id,b.uom ";

				/*$reqQnty_fin_res = sql_select($reqQnty_fin);
				foreach($reqQnty_fin_res as $req_val)
				{
					$req_qty_array[$req_val[csf('po_break_down_id')]]=$req_val[csf('fabric_qty')];
				}*/
			}
			else if ($pi_basis_id==1 && str_replace("'","",$booking_without_order)==1)
			{
				//echo "10**Here1";die;
				//$reqQnty = "select a.id as po_break_down_id,sum(b.finish_fabric) as fabric_qty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no = b.booking_no and a.booking_no = '$work_order_no' and b.dia_width =$txt_original_dia_width and b.fabric_color =$fabric_color_id and b.gsm_weight=$txt_gsm and b.uom=$cbouom and b.lib_yarn_count_deter_id=$fabric_desc_id and b.status_active=1 and b.is_deleted=0 group by a.id";

				$reqQnty = "SELECT a.id as po_break_down_id,sum(b.finish_fabric) as fabric_qty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where (a.entry_form_id !=140 or a.entry_form_id is null) and (b.entry_form_id !=140 or b.entry_form_id is null) and a.booking_no=b.booking_no and a.booking_no='$work_order_no' and upper(b.dia_width) =$txt_dia_width and b.fabric_color =$fabric_color_id and b.gsm_weight=$txt_gsm and b.uom=$cbouom and b.lib_yarn_count_deter_id=$fabric_desc_id and b.status_active=1 and b.is_deleted=0 group by a.id
				union all select a.id as po_break_down_id,sum(b.finish_fabric) as fabric_qty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.entry_form_id=140 and b.entry_form_id=140 and a.booking_no=b.booking_no and a.booking_no = '$work_order_no' and upper(b.dia) =$txt_dia_width and b.fabric_color =$fabric_color_id and b.gsm_weight=$txt_gsm and b.uom=$cbouom and b.lib_yarn_count_deter_id=$fabric_desc_id and b.status_active=1 and b.is_deleted=0 group by a.id";

				/*$reqQnty_fin_res = sql_select($reqQnty_fin);
				foreach($reqQnty_fin_res as $req_val)
				{
					$req_qty_array[$req_val[csf('po_break_down_id')]]=$req_val[csf('fabric_qty')];
				}*/
				//echo "10**".$reqQnty;die;

			}
			else
			{
				$req_qty_array[str_replace("'","",$txt_booking_no_id)]=$pi_req_qty;
			}
		}
		else
		{
			if(str_replace("'","",$cbo_receive_basis)==2)
			{
				$booking_cond = ($txt_booking_no!="")?" and b.booking_no=$txt_booking_no":"";
			}else{
				$booking_cond = ($txt_po_booking_no!="")?" and b.booking_no=$txt_po_booking_no":"";
			}
			//echo "10**";
			$reqQnty="select b.po_break_down_id,sum(b.fin_fab_qnty) as fabric_qty from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and b.po_break_down_id in($all_po_idss[0]) and c.construction='$feb_construction' and c.composition='".trim($feb_composition)."' $booking_cond and c.status_active=1 and c.is_deleted=0 group by b.po_break_down_id";
		}
	}

	//echo "10**".$reqQnty;die;
	$reqQnty_res = sql_select($reqQnty);

	if(!empty($reqQnty_res))
	{
		foreach($reqQnty_res as $row)
		{
			$req_qty_array[$row[csf("po_break_down_id")]]=$row[csf("fabric_qty")];
		}
	}
	else
	{
		if(str_replace("'","",$cbo_receive_basis)==9 || str_replace("'","",$cbo_receive_basis)==10)
		{
			// N.B: if fabrication changed in booking then Batch quantity is considered as fabric quantity
			$fabric_desc = $feb_construction.",".trim($feb_composition);

			/*$batch_qnty=sql_select("select b.po_id,sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b,product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.id=$txt_batch_id and c.detarmination_id=$fabric_desc_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 group by b.po_id");*/

			$batch_qnty=sql_select("select b.po_id,sum(b.batch_qnty) as batch_qnty
			from pro_batch_create_mst a, pro_batch_create_dtls b,product_details_master c
			where a.id=b.mst_id and b.prod_id=c.id and a.id=$txt_batch_id and c.detarmination_id=$fabric_desc_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and booking_no not Like '%SMN%'
			group by b.po_id
			union all
			select d.id as po_id,sum(b.batch_qnty) as batch_qnty
			from pro_batch_create_mst a, pro_batch_create_dtls b,product_details_master c , wo_non_ord_samp_booking_mst d
			where a.id=b.mst_id and b.prod_id=c.id and a.id=$txt_batch_id and c.detarmination_id=$fabric_desc_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1
			and a.booking_no= d.booking_no and d.booking_type in (1,4)
			and a.booking_no like '%SMN%'
			group by d.id");

			foreach($batch_qnty as $row)
			{
				$req_qty_array[$row[csf("po_id")]]=$row[csf("batch_qnty")];
			}
		} // other basis also need to be addressed
	}

	if(str_replace("'","",$cbo_receive_basis)==10)
	{
		$delivery_qnty = sql_select("select sum(c.quantity) delivery_qnty from inv_issue_master a,inv_transaction f,inv_finish_fabric_issue_dtls b,order_wise_pro_details c,product_details_master d where a.id=$txt_booking_no_id and a.entry_form in (224,318) and a.id=f.mst_id and f.id=b.trans_id and b.id=c.dtls_id and c.prod_id=d.id  and c.entry_form in (224,318) and c.status_active=1 and d.detarmination_id = $fabric_desc_id and d.gsm = $txt_gsm and upper(d.dia_width) = $txt_dia_width and c.color_id = $txt_color_id and b.width_type =$cbo_dia_width_type and d.unit_of_measure = $cbouom and b.fabric_shade=$cbo_fabric_type and b.body_part_id = $cbo_body_part and b.batch_id =$txt_batch_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1  and b.is_deleted=0 and c.status_active=1  and c.is_deleted=0");

		if( str_replace("'","",$update_dtls_id)  !="") $update_cond=" and b.id<>$update_dtls_id";
		$receive_qnty = sql_select("SELECT sum(b.receive_qnty) as receive_qnty
			FROM pro_finish_fabric_rcv_dtls b,inv_receive_master c
			WHERE b.mst_id=c.id and c.entry_form=37 and c.receive_basis =10 and c.entry_form=37 and b.status_active=1 and b.is_deleted=0 and c.booking_id = $txt_booking_no_id and b.fabric_description_id=$fabric_desc_id and b.gsm=$txt_gsm and upper(b.width) = $txt_dia_width and b.color_id = $txt_color_id and b.dia_width_type =$cbo_dia_width_type and b.uom = $cbouom and b.fabric_shade = $cbo_fabric_type and b.body_part_id=$cbo_body_part and b.batch_id = $txt_batch_id and b.status_active=1  and b.is_deleted=0 and c.status_active=1  and c.is_deleted=0 $update_cond");
		// and c.booking_without_order = 1
		if($delivery_qnty[0][csf('delivery_qnty')] < ($receive_qnty[0][csf('receive_qnty')] + str_replace("'","",$txt_production_qty) ))
		{
			echo "50**Delivery Quantity Exceeds Receive Quantity.\n Delivery Quantity=". $delivery_qnty[0][csf('delivery_qnty')] .", Receive Quantity=". ($receive_qnty[0][csf('receive_qnty')] + str_replace("'","",$txt_production_qty)); die;
		}


		//Job wise validation  here

		$style_wise_popup=0;
		$style_wise_popup=return_field_value("production_entry","variable_settings_production","company_name=$cbo_dyeing_company and variable_list=72 and is_deleted=0 and status_active=1");
		if($style_wise_popup==1) $style_wise_popup=$style_wise_popup; else $style_wise_popup=0;

		if($style_wise_popup ==1)
		{
			$delivery_qnty_job_wise = sql_select("SELECT job_id, pub_shipment_date, job_wise_qnty from order_wise_pro_details a, inv_finish_fabric_issue_dtls b, product_details_master c where a.dtls_id=b.id and b.prod_id=c.id and a.entry_form=224 and a.job_id is not null and a.is_sales=0 and b.mst_id= $txt_booking_no_id and b.body_part_id=$cbo_body_part  and c.detarmination_id=$fabric_desc_id and c.color=$txt_color_id and b.status_active=1 and a.status_active=1");

			if($delivery_qnty_job_wise)
			{

				foreach ($delivery_qnty_job_wise as $value)
				{
					$job_wise_qnty_arr[$value[csf('job_id')]]['delivery']+=$value[csf('job_wise_qnty')];
				}

				if($update_dtls_id )
				{
					$up_cond_rcv = " and b.id<>$update_dtls_id";
				}
				$sql_job_ship_wise_rcv=sql_select("SELECT d.pub_shipment_date, e.id as job_id, sum(a.quantity) as qnty from order_wise_pro_details a,pro_finish_fabric_rcv_dtls b,inv_receive_master c, wo_po_break_down d, wo_po_details_master e where a.dtls_id=b.id and b.mst_id=c.id and a.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.fabric_description_id=$fabric_desc_id and b.body_part_id=$cbo_body_part and b.color_id=$fabric_color_id and c.booking_id= $txt_booking_no_id and a.po_breakdown_id=d.id and d.job_id= e.id $up_cond_rcv group by d.pub_shipment_date, e.id");

				foreach ($sql_job_ship_wise_rcv as $value)
				{
					$job_wise_qnty_arr[$value[csf('job_id')]]['previous_rcv']+=$value[csf('qnty')];
				}

				$save_string= array_filter(explode(",", str_replace("'","",trim($save_data))));
				for($i=0;$i<count($save_string);$i++)
				{
					$order_dtls=explode("**",$save_string[$i]);
					$order_id=$order_dtls[0];
					$order_qnty=$order_dtls[1];
					$all_Order[$order_id]=$order_id;
					$Order_wise_delivery_arr[$order_id]=$order_qnty;
				}

				if(!empty($all_Order)){
					$job_query = sql_select("SELECT d.pub_shipment_date, e.id as job_id, d.id as order_id, e.job_no from wo_po_break_down d, wo_po_details_master e where d.job_id= e.id and d.id in (".implode(',',$all_Order).") group by d.pub_shipment_date, e.id, d.id, e.job_no");

					foreach ($job_query as $val)
					{
						$job_wise_qnty_arr[$val[csf('job_id')]]['receive']+=$Order_wise_delivery_arr[$val[csf('order_id')]];

						$job_number_arr[$val[csf('job_id')]] = $val[csf('job_no')];
					}
				}

				foreach ($job_wise_qnty_arr as $job_id => $row)
				{
					if($row['delivery'] < number_format( ($row['receive'] + $row['previous_rcv']) , 2,'.','' )  )
					{
						echo "50**Delivery Quantity Exceeds Receive Quantity.\n Delivery Quantity=". $row['delivery'] .", Receive Quantity=". number_format( ($row['previous_rcv'] + $row['receive']), 2,'.','' )."\n Job no:". $job_number_arr[$job_id]; die;
					}
				}
			}
		}

	}

	//echo "10**Hi";die;
	$up_trans_id=str_replace("'","",$update_trans_id);

	if((str_replace("'","",$cbo_receive_basis)==1 || str_replace("'","",$cbo_receive_basis)==2 || str_replace("'","",$cbo_receive_basis)==4 || str_replace("'","",$cbo_receive_basis)==6 || str_replace("'","",$cbo_receive_basis)==9 || str_replace("'","",$cbo_receive_basis)==11) && str_replace("'","",$booking_without_order) == 1)
	{
		if($up_trans_id!="") $up_cond=" and b.trans_id<>$up_trans_id";
		if(str_replace("'","",$cbo_receive_basis)==1)
		{
			$sql_cuml= "select b.booking_id, sum(b.receive_qnty) as qnty from  pro_finish_fabric_rcv_dtls b,inv_receive_master c where  b.mst_id=c.id and c.entry_form=37 and c.status_active=1 and b.fabric_description_id=$fabric_desc_id and c.booking_without_order = 1 and c.entry_form=37  and b.status_active=1 and b.is_deleted=0 and b.body_part_id=$cbo_body_part and c.booking_id = $txt_booking_no_id and b.color_id=$fabric_color_id $up_cond group by b.booking_id";
		}
		else if(str_replace("'","",$cbo_receive_basis)==2 || str_replace("'","",$cbo_receive_basis)==11)
		{
			$sql_cuml= "select c.booking_id, sum(b.receive_qnty) as qnty, sum(b.grey_used_qty) as grey_qnty from  pro_finish_fabric_rcv_dtls b,inv_receive_master c where  b.mst_id=c.id and c.entry_form=37 and c.status_active=1 and b.fabric_description_id=$fabric_desc_id and c.booking_without_order = 1 and c.entry_form=37  and b.status_active=1 and b.is_deleted=0 and b.body_part_id=$cbo_body_part and c.booking_id = $txt_booking_no_id and c.booking_no =$txt_booking_no and b.color_id=$fabric_color_id  and b.gsm= $txt_gsm $up_cond group by c.booking_id";

		}
		else if(str_replace("'","",$cbo_receive_basis)==9 )
		{
			$sql_cuml= "select b.booking_id, sum(b.receive_qnty) as qnty from  pro_finish_fabric_rcv_dtls b,inv_receive_master c where  b.mst_id=c.id and c.entry_form=37 and c.status_active=1 and b.fabric_description_id=$fabric_desc_id and c.booking_without_order = 1 and c.entry_form=37  and b.status_active=1 and b.is_deleted=0 and b.body_part_id=$cbo_body_part and b.booking_id = $hdn_booking_id and b.booking_no =$txt_po_booking_no and b.color_id=$fabric_color_id  $up_cond group by b.booking_id";
		}
		else
		{
			$sql_cuml= "select c.booking_id, sum(b.receive_qnty) as qnty from  pro_finish_fabric_rcv_dtls b,inv_receive_master c where  b.mst_id=c.id and c.entry_form=37 and c.status_active=1 and b.fabric_description_id=$fabric_desc_id and c.booking_without_order = 1 and c.entry_form=37  and b.status_active=1 and b.is_deleted=0 and b.body_part_id=$cbo_body_part and c.booking_id = $hidden_sample_booking_id and c.booking_no =$txt_sample_booking_no  $up_cond group by c.booking_id";

		}

		//echo "10**".$sql_cuml;die;
		$sql_result_cuml=sql_select($sql_cuml);

		foreach($sql_result_cuml as $row)
		{
			$prev_rec_qty_non_order[$row[csf('booking_id')]]=$row[csf('qnty')];
			$prev_grey_rec_qty_non_order[$row[csf('booking_id')]]=$row[csf('grey_qnty')];
		}

	}
	else
	{
		if($up_trans_id!="") $up_cond=" and a.trans_id<>$up_trans_id";
		$sql_cuml="select a.po_breakdown_id, sum(a.quantity) as qnty, sum(a.grey_used_qty) as grey_qnty from order_wise_pro_details a, pro_finish_fabric_rcv_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id and c.entry_form=37 and c.status_active=1 and a.trans_id>0 and a.po_breakdown_id in ($all_po_idss[0]) and b.fabric_description_id=$fabric_desc_id and a.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.body_part_id=$cbo_body_part and b.batch_id=$txt_batch_id $up_cond group by a.po_breakdown_id";
		//echo "10**=".$sql_cuml;die;
		$sql_result_cuml=sql_select($sql_cuml);
		foreach($sql_result_cuml as $row)
		{
			$prev_rec_qty[$row[csf('po_breakdown_id')]]=$row[csf('qnty')];
			$prev_grey_rec_qty[$row[csf('po_breakdown_id')]]=$row[csf('grey_qnty')];
		}
	}

	foreach($req_qty_array as $po_id=>$req_qnty)
	{
		//$cumu_rec_qty[$po_id]=$req_qnty-$prev_rec_qty[$po_id];
		$required_qty[$po_id]=$req_qnty;
	}

	/*echo "10**<pre>";
	print_r($cumu_rec_qty);die;*/

	$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$cbo_company_id and variable_list=23 and category = 2 and status_active =1 and is_deleted=0 order by id");
	$over_receive_limit = !empty($variable_set_invent) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;

	if(str_replace("'","",$txt_gsm) =="")
	{
		echo "30**GSM not found";
		disconnect($con);
		die;
	}

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//---------------Check Receive control on Gate Entry according to variable settings inventory---------------------------//
		if($variable_set_invent==1)
		{
			$challan_no=str_replace("'","",$txt_challan_no);
			if($challan_no!="")
			{
				$variable_set_invent=return_field_value("a.id as id"," inv_gate_in_mst a,  inv_gate_in_dtl b","a.id=b.mst_id and a.company_id=$cbo_company_id and a.challan_no='$challan_no' and b.item_category_id=2  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","id");
				if(empty($variable_set_invent))
				{
					echo "30** This Item Not Found In Gate Entry. \n Please Gate Entry First.";
					disconnect($con);
					die;
				}
			}
		}

		if(str_replace("'",'',$update_id) !="" && $mrr_wise_balancing_maintain==1)
		{
			if(str_replace("'","",$cbo_receive_basis)==9 || str_replace("'","",$cbo_receive_basis)==10 || str_replace("'","",$cbo_receive_basis)==16)
			{
				if($pre_batch_arr[str_replace("'","",$txt_batch_id)] =="")
				{
					echo "20**Batch mixing not allowed.";
					disconnect($con);
					die;
				}
			}
			//other basis's batch id is created below. so other basis batch validation has written there.
		}

		$receive_qnty=str_replace("'","",$txt_production_qty);
		if(str_replace("'","",$cbo_receive_basis)==14)
		{
			$privious_receive_qnty=return_field_value("sum(receive_qnty) as receive_qnty","inv_receive_master a, pro_finish_fabric_rcv_dtls b","a.id = b.mst_id and a.booking_no=$txt_booking_no and a.receive_basis=14 and b.fabric_description_id=$fabric_desc_id and b.pre_cost_fabric_cost_dtls_id=$pre_cost_fab_conv_cost_dtls_id and a.status_active=1 and b.status_active=1","receive_qnty");

			$privious_receive_qnty = ($privious_receive_qnty!="" && $privious_receive_qnty>0)?$privious_receive_qnty:0;

			$reqQnty=return_field_value("sum(grey_qnty_by_uom) fabric_qty","fabric_sales_order_dtls","status_active=1 and is_deleted=0 and job_no_mst=$txt_booking_no and determination_id=$fabric_desc_id","fabric_qty");

			$allowed_qnty = ($privious_receive_qnty+$receive_qnty)+(($over_receive_limit / 100) * ($privious_receive_qnty+$receive_qnty));
			if($allowed_qnty > $reqQnty){
				echo "30**Over receive is Allowed up to = ".$over_receive_limit . "%\nRequired quantity = ".$reqQnty."\nPrevious Receive=".($privious_receive_qnty);
				disconnect($con);
				die;
			}
		}

		//---------------End Check Receive control on Gate Entry---------------------------//
		//---------------Check Receive control when receive basis production according to production qnty---------------------------//

		if(str_replace("'","",$cbo_receive_basis)==9)
		{
			$production_qnty=return_field_value("receive_qnty","pro_finish_fabric_rcv_dtls","id=$fin_prod_dtls_id and status_active=1 and is_deleted=0","receive_qnty");
			//$privious_receive_qnty=return_field_value("sum(receive_qnty) as receive_qnty","pro_finish_fabric_rcv_dtls","fin_prod_dtls_id=$fin_prod_dtls_id and status_active=1 and is_deleted=0","receive_qnty");
			$previous_rcv_sql = sql_select("select sum(a.receive_qnty) as receive_qnty from pro_finish_fabric_rcv_dtls a, inv_receive_master b where a.fin_prod_dtls_id=$fin_prod_dtls_id and a.status_active=1 and a.is_deleted=0 and a.mst_id=b.id and b.entry_form=37 and b.receive_basis=9");
			$privious_receive_qnty=$previous_rcv_sql[0][csf("receive_qnty")];

			$current_rcv_qnty=$receive_qnty+$privious_receive_qnty;
			$cu_production=$production_qnty+(($production_qnty/100)*$over_receive_limit);
			if($current_rcv_qnty>$cu_production)
			{
				echo "50** Receive Quantity Not Allow Over Then Production Quantity.\nProduction quantity = $production_qnty.\Previous receive = $privious_receive_qnty";
				disconnect($con);
				die;
			}
		}

		if(str_replace("'","",$hidden_sample_booking_id)!="" && (str_replace("'","",$cbo_receive_basis)==4 || str_replace("'","",$cbo_receive_basis)==6)){
			$txt_booking_no_id 	= $hidden_sample_booking_id;
			$txt_booking_no 	= $txt_sample_booking_no;
		}

		//---------------End Check Receive control when receive basis production---------------------------//
		$finish_recv_num=''; $finish_update_id='';
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";

			$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
			$new_finish_recv_system_id = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,$cbo_company_id,'FFGE',37,date("Y",time()),2 ));

			/*############ Note ##############
			when receive basis service booking without order booking_id = smaple booking id and booking_no=service booking without order no
			else booking_id = booking id and booking_no=booking no
			*/
			$txt_gate_entry_no = str_replace("'", "", $txt_gate_entry_no);

			$field_array="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category, receive_basis, company_id, booking_id, booking_no, booking_without_order, receive_date, challan_no,yarn_issue_challan_no, store_id, location_id, knitting_source, knitting_company,knitting_location_id,qc_name,emp_id, inserted_by, insert_date, currency_id, exchange_rate, lc_sc_id, lc_sc_no,challan_date,gate_entry_no,gate_entry_date";

			$data_array="(".$id.",'".$new_finish_recv_system_id[1]."',".$new_finish_recv_system_id[2].",'".$new_finish_recv_system_id[0]."',37,2,".$cbo_receive_basis.",".$cbo_company_id.",".$txt_booking_no_id.",".$txt_booking_no.",".$booking_without_order.",".$txt_receive_date.",".$txt_challan_no.",".$txt_grey_issue_challan_no.",".$cbo_store_name.",".$cbo_location.",".$cbo_dyeing_source.",".$cbo_dyeing_company.",".$cbo_dyeing_location.",".$txt_qc_name.",".$txt_hidden_qc_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_currency.",".$txt_exchange_rate.",".$hidden_lc_sc_id.",".$txt_lc_sc_no.",".$txt_challan_date.",'".$txt_gate_entry_no."',".$txt_gate_entry_date.")";
			$finish_recv_num=$new_finish_recv_system_id[0];
			$finish_update_id=$id;
		}
		else
		{
			$field_array_update="receive_basis*booking_id*booking_no*booking_without_order*receive_date*challan_no*yarn_issue_challan_no*store_id*location_id*knitting_source*knitting_company*knitting_location_id*qc_name*emp_id*updated_by*update_date*currency_id*exchange_rate*lc_sc_id*lc_sc_no*challan_date*gate_entry_no*gate_entry_date";

			$data_array_update=$cbo_receive_basis."*".$txt_booking_no_id."*".$txt_booking_no."*".$booking_without_order."*".$txt_receive_date."*".$txt_challan_no."*".$txt_grey_issue_challan_no."*".$cbo_store_name."*".$cbo_location."*".$cbo_dyeing_source."*".$cbo_dyeing_company."*".$cbo_dyeing_location."*".$txt_qc_name."*".$txt_hidden_qc_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_currency."*".$txt_exchange_rate."*".$hidden_lc_sc_id."*".$txt_lc_sc_no."*".$txt_challan_date."*".$txt_gate_entry_no."*".$txt_gate_entry_date;

			$finish_recv_num=str_replace("'","",$txt_system_id);
			$finish_update_id=str_replace("'","",$update_id);
		}
		//|| str_replace("'","",$cbo_receive_basis)==1
		if(str_replace("'","",$cbo_receive_basis)==4 || str_replace("'","",$cbo_receive_basis)==6 || str_replace("'","",$cbo_receive_basis)==2  || str_replace("'","",$cbo_receive_basis)==11)
		{
			if (str_replace("'", "", trim($txt_color)) != "") {
				if (!in_array(str_replace("'", "", trim($txt_color)),$new_array_color)){
					$color_id = return_id( str_replace("'", "", trim($txt_color)), $color_arr, "lib_color", "id,color_name","37");
					$new_array_color[$color_id]=str_replace("'", "", trim($txt_color));
				}
				else $color_id =  array_search(str_replace("'", "", trim($txt_color)), $new_array_color);
			} else $color_id = 0;

			/*$color_id=return_id( $txt_color, $color_arr, "lib_color", "id,color_name");
			if ($color_id=='') {
				$color_id=0;
			}*/

		}else if(str_replace("'","",$cbo_receive_basis)==10 || str_replace("'","",$cbo_receive_basis)==16) {
			$color_id=str_replace("'","",$txt_color_id);
			if ($color_id=='') {
				$color_id=0;
			}
		}
		else
		{
			$color_id=$fabric_color_id;
			if ($color_id=='') {
				$color_id=0;
			}
		}

		$ItemDesc=str_replace("'","",$txt_fabric_desc).", ".str_replace("'","",$txt_gsm).", ".str_replace("'","",$txt_dia_width);

		$txt_exchange_rate=str_replace("'","",$txt_exchange_rate);
		/*$order_rate 	= number_format((str_replace("'","",$txt_rate)*1),2,".","");
		$order_amount 	= number_format((str_replace("'","",$txt_amount)*1),2,".","");
		$cons_rate 	= number_format(($txt_exchange_rate*$order_rate),2,".","");
		$cons_amount 	= number_format((str_replace("'","",$txt_production_qty)*$cons_rate),2,".","");*/
		if(str_replace("'","",$cbo_receive_basis)==4 || str_replace("'","",$cbo_receive_basis)==6)
		{
			$order_rate 	= str_replace("'","",$txt_rate)*1;
			$order_amount 	= str_replace("'","",$txt_amount)*1;
		}
		else
		{
			$order_rate 	= str_replace("'","",$txt_hidden_rate)*1;
			$order_amount 	= str_replace("'","",$txt_hidden_amount)*1;
		}
		$cons_rate 		= $txt_exchange_rate*$order_rate;
		$cons_amount 	= str_replace("'","",$txt_production_qty)*$cons_rate;

		if(str_replace("'","",$cbo_receive_basis)==9 || str_replace("'","",$cbo_receive_basis)==16)
		{
			$batch_id 		= str_replace("'","",$txt_batch_id);
			$prod_id 		= str_replace("'","",$product_id);
			$stockData 		= sql_select("select current_stock, avg_rate_per_unit, stock_value from product_details_master where id=$prod_id");

			$stock 			= $stockData[0][csf('current_stock')];
			$avg_rate 	 	= $stockData[0][csf('avg_rate_per_unit')];
			$stock_value 	= $stockData[0][csf('stock_value')];
			$cur_st_qnty 	= $stock+str_replace("'","",$txt_production_qty);
			$cur_st_value 	= $stock_value+str_replace("'","",$cons_amount);

			$cur_st_rate = number_format($cur_st_value/$cur_st_qnty,$dec_place[3],'.','');

			$cur_st_qnty = number_format($cur_st_qnty,2,'.','');

			$field_array_prod_update = "current_stock*avg_rate_per_unit*stock_value";
			$data_array_prod_update  = $cur_st_qnty."*".$cur_st_rate."*".$cur_st_value;

		}
		else if(str_replace("'","",$cbo_receive_basis)==10)
		{
			$batch_id 		= str_replace("'","",$txt_batch_id);
			$row_prod=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id and item_category_id=2 and detarmination_id=$fabric_desc_id and gsm=$txt_gsm and upper(dia_width)=$txt_dia_width and color='$color_id' and unit_of_measure=$cbouom and status_active=1 and is_deleted=0 and is_gmts_product=1");
			if(count($row_prod)>0)
			{
				$prod_id 	= $row_prod[0][csf('id')];
				$stock_qnty = $row_prod[0][csf('current_stock')];
				$avg_rate 	= $row_prod[0][csf('avg_rate_per_unit')];
				$stock_value= $row_prod[0][csf('stock_value')];

				$curr_stock_qnty = $stock_qnty+str_replace("'", '',$txt_production_qty);
				$stock_value = $stock_value+str_replace("'","",$cons_amount);

				$avg_rate_per_unit = number_format($stock_value/$curr_stock_qnty,$dec_place[3],'.','');

				$curr_stock_qnty = number_format($curr_stock_qnty,2,'.','');

				$field_array_prod_update="store_id*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*color*updated_by*update_date";
				$data_array_prod_update=$cbo_store_name."*".$avg_rate_per_unit."*".$txt_production_qty."*".$curr_stock_qnty."*".$stock_value."*".$color_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			}
			else
			{
				$prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				$stock_qnty=str_replace("'","",$txt_production_qty); $last_purchased_qnty=$txt_production_qty; $avg_rate_per_unit=$txt_rate; $stock_value=$txt_amount;

				$prod_name_dtls=trim(str_replace("'","",$txt_fabric_desc)).", ".trim(str_replace("'","",$txt_gsm)).", ".trim(str_replace("'","",$txt_dia_width));
				$field_array_prod="id, company_id, store_id, item_category_id, detarmination_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, color, gsm, dia_width, inserted_by, insert_date,is_gmts_product";

				$stock_qnty = number_format($stock_qnty,2,'.','');

				$data_array_prod="(".$prod_id.",".$cbo_company_id.",".$cbo_store_name.",2,".$fabric_desc_id.",".$txt_fabric_desc.",'".$prod_name_dtls."',".$cbouom.",".$avg_rate_per_unit.",".$last_purchased_qnty.",".$stock_qnty.",".$stock_value.",".$color_id.",".$txt_gsm.",".$txt_dia_width.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
			}
		}
		else
		{
			if(str_replace("'", '',$cbo_receive_basis)==1)
			{
				$booking_id=$hdn_booking_id;
				$booking_no=str_replace("'", '',$txt_po_booking_no);
			}
			else if(str_replace("'", '',$cbo_receive_basis)==2 || str_replace("'","",$cbo_receive_basis)==11)
			{
				$booking_id=$txt_booking_no_id;
				$booking_no=str_replace("'", '',$txt_booking_no);
			}
			else
			{
				if(str_replace("'","",$hidden_sample_booking_id)!="" && (str_replace("'","",$cbo_receive_basis)==4 || str_replace("'","",$cbo_receive_basis)==6))
				{
					$booking_id 	= $hidden_sample_booking_id;
					$booking_no 	= str_replace("'", '',$txt_sample_booking_no);
				}
				else if(str_replace("'","",$hdn_booking_id)!="" && (str_replace("'","",$cbo_receive_basis)==4 || str_replace("'","",$cbo_receive_basis)==6))
				{
					$booking_id 	= $hdn_booking_id;
					$booking_no 	= str_replace("'", '',$txt_po_booking_no);
				}
				else{
					$booking_id=0;
					$booking_no='';
				}
			}
			$batchData=sql_select("select a.id, a.batch_weight,a.booking_no from pro_batch_create_mst a where a.batch_no=$txt_batch_no and a.color_id='$color_id' and a.company_id =$cbo_company_id and a.status_active=1 and a.is_deleted=0 and a.entry_form=37 group by a.id, a.batch_weight,a.booking_no");
			if(count($batchData)>0)
			{
				if($batchData[0][csf('booking_no')] != $booking_no)
				{
					echo "50**This Batch has another booking no.\nBooking No : ".$batchData[0][csf('booking_no')];
					disconnect($con);die;
				}

				$batch_id=$batchData[0][csf('id')];
				$curr_batch_weight=$batchData[0][csf('batch_weight')]+str_replace("'", '',$txt_production_qty);
				$field_array_batch_update="booking_no_id*booking_no*batch_weight*updated_by*update_date";
				$data_array_batch_update=$booking_id."*'".$booking_no."'*".$curr_batch_weight."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			}
			if(empty($batchData))
			{
				$batch_id = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
				$field_array_batch="id,batch_no,entry_form,batch_date,company_id,booking_no_id,booking_no,booking_without_order,color_id,batch_weight,inserted_by,insert_date";

				$data_array_batch="(".$batch_id.",".$txt_batch_no.",37,".$txt_receive_date.",".$cbo_company_id.",".$booking_id.",'".$booking_no."',".$booking_without_order.",'".$color_id."',".$txt_production_qty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}

			if(str_replace("'","",$fabric_desc_id)=="") $fabric_desc_id=0;

			if(str_replace("'","",$txt_dia_width) == "" && $db_type==2)
			{
				$prod_dia_cond = " and dia_width is null";
			}
			else
			{
				$prod_dia_cond = " and upper(dia_width) = $txt_dia_width";
			}
			if(str_replace("'","",$cbo_receive_basis)==2 && str_replace("'","",$booking_without_order)==1 && str_replace("'","",$fabric_desc_id)==0)
			{
				$fabric_description=trim(str_replace("'","",$txt_fabric_desc));
				$row_prod=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id and item_category_id=2 and detarmination_id=$fabric_desc_id and item_description='$fabric_description' and gsm=$txt_gsm $prod_dia_cond and color='$color_id' and unit_of_measure=$cbouom and is_gmts_product=1 and status_active=1 and is_deleted=0");
				//and dia_width=$txt_dia_width
			}
			else
			{
				$row_prod=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id and item_category_id=2 and detarmination_id=$fabric_desc_id and gsm=$txt_gsm $prod_dia_cond and color='$color_id' and unit_of_measure=$cbouom and is_gmts_product=1 and status_active=1 and is_deleted=0");
				//and dia_width=$txt_dia_width
			}

			if(count($row_prod)>0)
			{
				$prod_id=$row_prod[0][csf('id')];
				$stock_qnty=$row_prod[0][csf('current_stock')];
				$avg_rate=$row_prod[0][csf('avg_rate_per_unit')];
				$stock_value=$row_prod[0][csf('stock_value')];

				$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_production_qty);
				$stock_value=$stock_value+str_replace("'","",$cons_amount);

				$avg_rate_per_unit=number_format($stock_value/$curr_stock_qnty,$dec_place[3],'.','');

				$curr_stock_qnty=number_format($curr_stock_qnty,2,'.','');

				$field_array_prod_update="store_id*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*color*updated_by*update_date";
				$data_array_prod_update=$cbo_store_name."*".$avg_rate_per_unit."*".$txt_production_qty."*".$curr_stock_qnty."*".$stock_value."*".$color_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			}
			else
			{
				$prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				$stock_qnty=str_replace("'","",$txt_production_qty); $last_purchased_qnty=$txt_production_qty; $avg_rate_per_unit=$txt_rate; $stock_value=$txt_amount;

				$prod_name_dtls=trim(str_replace("'","",$txt_fabric_desc)).", ".trim(str_replace("'","",$txt_gsm)).", ".trim(str_replace("'","",$txt_dia_width));
				$field_array_prod="id, company_id, store_id, item_category_id, detarmination_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, color, gsm, dia_width, is_gmts_product, inserted_by, insert_date";

				$stock_qnty=number_format($stock_qnty,2,'.','');

				$data_array_prod="(".$prod_id.",".$cbo_company_id.",".$cbo_store_name.",2,".$fabric_desc_id.",".$txt_fabric_desc.",'".$prod_name_dtls."',".$cbouom.",".$avg_rate_per_unit.",".$last_purchased_qnty.",".$stock_qnty.",".$stock_value.",".$color_id.",".$txt_gsm.",".$txt_dia_width. ",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
		}

		//N.B this validation is for others receive basis without (9,10,16)
		if(str_replace("'",'',$update_id) !="" && $mrr_wise_balancing_maintain==1)
		{
			if($pre_batch_arr[$batch_id] =="")
			{
				echo "20**Batch mixing not allowed.";
				disconnect($con);
				die;
			}
		}

		if($prod_id==0 || $prod_id=="")
		{
			echo "20**Product Not Found.";
			disconnect($con);
			die;
		}

		//---------------Check Receive date with Last Transaction date-------------//
		$max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$prod_id and store_id=$cbo_store_name  and status_active = 1", "max_date");
		if($max_transaction_date != "")
		{
			$max_transaction_date = date("Y-m-d", strtotime($max_transaction_date));
			$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_receive_date)));
			if ($receive_date < $max_transaction_date)
			{
				echo "20**Receive Date Can not Be Less Than Last Transaction Date Of This Lot";
				disconnect($con);
				die;
			}
		}

		$dyeing_charge_string=explode("*",str_replace("'","",$knitting_charge_string));
		$dyeing_charge=$dyeing_charge_string[0];
		if(str_replace("'","",$cbo_receive_basis)==1 || str_replace("'","",$cbo_receive_basis)==2)
		{
			$grey_fabric_rate=$order_rate;
		}
		else
		{
			$grey_fabric_rate=$dyeing_charge_string[1];
		}


		$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$field_array_trans="id, mst_id, receive_basis, pi_wo_batch_no, company_id, prod_id, item_category, transaction_type, transaction_date, store_id, order_uom, order_qnty, order_rate, order_amount, cons_uom, cons_quantity, cons_reject_qnty, cons_rate, cons_amount, balance_qnty, balance_amount, machine_id, rack, self,floor_id,room,bin_box,fabric_shade, body_part_id, inserted_by, insert_date,pi_wo_dtls_id";

		$data_array_trans="(".$id_trans.",".$finish_update_id.",".$cbo_receive_basis.",".$batch_id.",".$cbo_company_id.",".$prod_id.",2,1,".$txt_receive_date.",".$cbo_store_name.",".$cbouom.",".$txt_production_qty.",".$order_rate.",".$order_amount.",".$cbouom.",".$txt_production_qty.",".$txt_reject_qty.",".$cons_rate.",".$cons_amount.",".$txt_production_qty.",".$cons_amount.",".$cbo_machine_name.",".$txt_rack.",".$txt_shelf.",".$cbo_floor.",".$cbo_room.",".$cbo_bin.",".$cbo_fabric_type.",".$cbo_body_part.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$pi_dtls_id.")";

		$id_dtls = return_next_id_by_sequence("PRO_FIN_FAB_RCV_DTLS_PK_SEQ", "pro_finish_fabric_rcv_dtls", $con);
		$field_array_dtls="id, mst_id, trans_id, prod_id, batch_id, fin_prod_dtls_id,fabric_shade,body_part_id, fabric_description_id, gsm, width, color_id, receive_qnty, reject_qty, no_of_roll, order_id, buyer_id, machine_no_id, rack_no, shelf_no,floor,room,bin,dia_width_type,rate, amount,dyeing_charge,grey_fabric_rate,grey_used_qty, uom,pre_cost_fabric_cost_dtls_id,inserted_by, insert_date,is_sales,job_no,booking_no,booking_id,pi_wo_dtls_id,remarks,original_width,yarn_issue_rate,knitting_rate,fabric_dyeing_rate,aop_rate,rate_string,payment_over_rcv_status";
		if($pre_cost_fab_conv_cost_dtls_id == ""){
			$pre_cost_fab_conv_cost_dtls_id = 0;
		}

		if(str_replace("'","",$cbo_receive_basis)==2 && str_replace("'","",$var_allow_fin_rcv_NR_hdn)==1 && str_replace("'","",$var_finish_rate_come_hdn)==2)
		{
			$cons_rate=str_replace("'","",$txt_hidden_rate)*1;

			$txt_hidden_yarn_issue_rate=str_replace("'","",$txt_hidden_yarn_issue_rate)*1;
			$txt_hidden_knitting_rate=str_replace("'","",$txt_hidden_knitting_rate)*1;
			$txt_hidden_dyeing_rate=str_replace("'","",$txt_hidden_dyeing_rate)*1;
			$txt_hidden_aop_rate=str_replace("'","",$txt_hidden_aop_rate)*1;
		}
		$data_array_dtls="(".$id_dtls.",".$finish_update_id.",".$id_trans.",".$prod_id.",".$batch_id.",".$fin_prod_dtls_id.",".$cbo_fabric_type.",".$cbo_body_part.",".$fabric_desc_id.",".$txt_gsm.",".$txt_dia_width.",".$color_id.",".$txt_production_qty.",".$txt_reject_qty.",".$txt_no_of_roll.",".$all_po_id.",".$buyer_id.",".$cbo_machine_name.",".$txt_rack.",".$txt_shelf.",".$cbo_floor.",".$cbo_room.",".$cbo_bin.",".$cbo_dia_width_type.",".$cons_rate.", ".$cons_amount.",'".$dyeing_charge."','".$grey_fabric_rate."',".$txt_used_qty.",".$cbouom.",".$pre_cost_fab_conv_cost_dtls_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hdn_is_sales.",".$txt_job_no.",".$txt_po_booking_no.",".$hdn_booking_id.",".$pi_dtls_id.",".$txt_remarks.",".$txt_original_dia_width.",".$txt_hidden_yarn_issue_rate.",".$txt_hidden_knitting_rate.",".$txt_hidden_dyeing_rate.",".$txt_hidden_aop_rate.",".$hdn_rate_string.",".$txt_payment_overrcv_qty.")";

		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, returnable_qnty, fab_meter_qnty, grey_used_qty, inserted_by, insert_date,is_sales";

		$field_array_batch_dtls="id, mst_id, po_id, prod_id, item_description, roll_no, roll_id, barcode_no, batch_qnty, dtls_id,width_dia_type, inserted_by, insert_date";

		$barcode_year=date("y");
		//$barcode_suffix_no = explode("*",return_next_id_by_sequence("ROLL_BARCODE_SUFFIX_NO_SEQ", "pro_roll_details",$con,1,0,'',2,date("Y",time()),2 ));
		//$barcode_no=$barcode_year."37".str_pad($barcode_suffix_no[2],7,"0",STR_PAD_LEFT);

		$field_array_roll="id, barcode_year,barcode_suffix_no,barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,qc_pass_qnty, reject_qnty, fab_meter_qnty, grey_used_qty, roll_no, booking_without_order, inserted_by, insert_date";
		$field_array_roll_for_batch="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, qc_pass_qnty, roll_no, roll_id, booking_without_order, inserted_by, insert_date";
		$basis_arr = array(9,10);
		$save_string= array_filter(explode(",", str_replace("'","",trim($save_data))));
		if(str_replace("'","",$roll_maintained)==1)
		{
			$po_array=array();
			for($i=0;$i<count($save_string);$i++)
			{
				$order_dtls=explode("**",$save_string[$i]);
				$order_id=$order_dtls[0];
				$order_qnty_roll_wise=$order_dtls[1];
				$roll_no=$order_dtls[2];
				$roll_id=$order_dtls[3];
				$order_reject_qnty_roll_wise=$order_dtls[5];
				$fabMeterQntyRollwise=$order_dtls[7];
				$greyQntyRollwise=$order_dtls[8];

				if($data_array_roll!="") $data_array_roll.=",";

				/*############ Note ##############
				when receive basis service booking without order booking_id = smaple booking id and booking_no=service booking without order no
				else booking_id = booking id and booking_no=booking no
				*/
				$barcode_suffix_no = explode("*", return_next_id_by_sequence("ROLL_BARCODE_SUFFIX_NO_SEQ", "pro_roll_details",$con,1,0,'',2,date("Y",time()),2 ));

				$barcode_no=$barcode_year."37".str_pad($barcode_suffix_no[2],7,"0",STR_PAD_LEFT);


				if(str_replace("'","",$booking_without_order)==1)
				{
					$po_or_booking_no = $booking_id;
					$roll_quantity = str_replace("'","",$txt_production_qty);
				}
				else
				{
					$po_or_booking_no = $order_id;
					$roll_quantity = $order_qnty_roll_wise;
				}

				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
				$rollId=$id_roll;

				$data_array_roll.="(".$id_roll.",".$barcode_year.",'".$barcode_suffix_no[2]."','".$barcode_no."',".$finish_update_id.",".$id_dtls.",".$po_or_booking_no.",37,'".$roll_quantity."','".$roll_quantity."','".$order_reject_qnty_roll_wise."','".$fabMeterQntyRollwise."','".$greyQntyRollwise."','".$roll_no."',".$booking_without_order.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				if($data_array_batch_dtls!="" ) $data_array_batch_dtls.=",";
				$data_array_batch_dtls.="(".$id_dtls_batch.",'".$batch_id."','".$order_id."','".$prod_id."','".$ItemDesc."','".$roll_no."','".$id_roll."','".$barcode_no."',".$order_qnty_roll_wise.",".$id_dtls.",".$cbo_dia_width_type.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				/*$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				if($data_array_roll_for_batch!="") $data_array_roll_for_batch.= ",";
				$data_array_roll_for_batch.="(".$id_roll.",'".$barcode_no."',".$batch_id.",".$id_dtls_batch.",".$po_or_booking_no.",37,'".$roll_quantity."','".$roll_quantity."','".$roll_no."','".$rollId."',".$booking_without_order.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";*/

				// ###### only barcode creation entry form 2 asign for barcode suffix

				$po_array[$order_id]['qnty']+=$order_qnty_roll_wise;
				$po_array[$order_id]['reject_qnty']+=$order_reject_qnty_roll_wise;
				$po_array[$order_id]['fab_meter_qnty']+=$fabMeterQntyRollwise;
				$po_array[$order_id]['grey_qnty']+=$greyQntyRollwise;
			}

			foreach($po_array as $key=>$val)
			{
				$order_id=$key;
				$order_qnty=$val['qnty'];
				$cu_receive=$required_qty[$order_id]+(($required_qty[$order_id]/100)*$over_receive_limit) - $prev_rec_qty[$order_id];
				if($order_qnty>$cu_receive)
				{
					//echo "10**"; die('test');
					echo "50**Recv. Qnty exceeds Req. quantity for this fabric.\nRequired quantity balance = ".$cu_receive;
					disconnect($con);
					die;
				}
				$order_reject_qnty=$val['reject_qnty'];
				$fabMeterQntyPOwise=$val['fab_meter_qnty'];
				$greyQntyPOwise=$val['grey_qnty'];
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);

				if($data_array_prop!="") $data_array_prop.= ",";
				$data_array_prop.="(".$id_prop.",".$id_trans.",1,37,".$id_dtls.",'".$order_id."','".$prod_id."','".$color_id."','".$order_qnty."','".$order_reject_qnty."','".$fabMeterQntyPOwise."','".$greyQntyPOwise."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hdn_is_sales.")";
			}
		}
		else
		{

			if(str_replace("'","",$booking_without_order)==1 && (str_replace("'","",$cbo_receive_basis) == 1 || str_replace("'","",$cbo_receive_basis) == 2 || str_replace("'","",$cbo_receive_basis) == 4 || str_replace("'","",$cbo_receive_basis) == 6 || str_replace("'","",$cbo_receive_basis) == 9 || str_replace("'","",$cbo_receive_basis) == 11))
			{

				if(str_replace("'","",$cbo_receive_basis) == 1 || str_replace("'","",$cbo_receive_basis) == 9)
				{
					$cumu_receive  = $prev_rec_qty_non_order[str_replace("'","",$hdn_booking_id)]+str_replace("'","",$txt_production_qty);
					$order_qnty = $required_qty[str_replace("'","",$hdn_booking_id)] + ($required_qty[str_replace("'","",$hdn_booking_id)]/100)*$over_receive_limit;
					//echo "10**".$order_qnty."==".$cumu_receive;die;
				}
				else if(str_replace("'","",$cbo_receive_basis) == 2 || str_replace("'","",$cbo_receive_basis) == 11)
				{

					$cumu_receive  = $prev_rec_qty_non_order[str_replace("'","",$txt_booking_no_id)]+str_replace("'","",$txt_production_qty);
					$order_qnty = $required_qty[str_replace("'","",$txt_booking_no_id)] + ($required_qty[str_replace("'","",$txt_booking_no_id)]/100)*$over_receive_limit;
				}
				else
				{
					$cumu_receive  = $prev_rec_qty_non_order[str_replace("'","",$hidden_sample_booking_id)]+str_replace("'","",$txt_production_qty);
					$order_qnty = $required_qty[str_replace("'","",$hidden_sample_booking_id)] + ($required_qty[str_replace("'","",$hidden_sample_booking_id)]/100)*$over_receive_limit;
				}

				$required_balance =  $order_qnty - ($cumu_receive - str_replace("'","",$txt_production_qty));

				//str_replace("'","",$hidden_sample_booking_id)
				//echo "10**".$order_qnty."=".$cumu_receive;die;
				if( $cumu_receive> $order_qnty && str_replace("'","",$txt_payment_overrcv_qty)==0)
				{
					echo "50**Recv. Qnty exceeds Req. quantity for this fabric.\nRequired quantity balance = ".number_format($required_balance,2,".","");
					disconnect($con);
					die;
				}

				if(str_replace("'","",$process_costing_maintain)==0 && str_replace("'","",$cbo_receive_basis) == 11)
				{
					$cumu_grey_receive  = $prev_grey_rec_qty_non_order[str_replace("'","",$txt_booking_no_id)]+str_replace("'","",$txt_used_qty);
					$order_qnty = $required_qty[str_replace("'","",$txt_booking_no_id)] ;//+ ($required_qty[str_replace("'","",$txt_booking_no_id)]/100)*$over_receive_limit;
					$required_balance =  $order_qnty - ($cumu_grey_receive - str_replace("'","",$txt_used_qty));
					if( $cumu_grey_receive> $order_qnty && str_replace("'","",$txt_payment_overrcv_qty)==0)
					{
						echo "50**Grey Qnty exceeds Req. quantity for this fabric.\nRequired quantity balance = ".number_format($required_balance,2,".","");
						disconnect($con);
						die;
					}
				}
			}
			else if(str_replace("'","",$cbo_receive_basis) == 16 && str_replace("'","",$booking_without_order)==0)
			{
				for($i=0;$i<count($save_string);$i++)
				{
					$order_dtls=explode("_",$save_string[$i]);
					$order_id=$order_dtls[0];
					$order_qnty=$order_dtls[1];
					$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					if($data_array_prop!="" ) $data_array_prop.=",";
					$data_array_prop.="(".$id_prop.",".$id_trans.",1,37,".$id_dtls.",'".$order_id."','".$prod_id."','".$color_id."','".$order_qnty."','0','0','0',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hdn_is_sales.")";
				}
			}
			else
			{
				$total_order_qnty=0;
				for($i=0;$i<count($save_string);$i++)
				{
					$order_dtls=explode("**",$save_string[$i]);
					$order_id=$order_dtls[0];
					$order_qnty=$order_dtls[1];
					$order_reject_qnty=$order_dtls[5];
					$fabMeterQntyPOwise=$order_dtls[7];
					$greyQntyPOwise=$order_dtls[8];
					$cu_receive=$required_qty[$order_id]+(($required_qty[$order_id]/100)*$over_receive_limit) - $prev_rec_qty[$order_id];
					//echo "50** Receive Quantity Not Allow Over Req Quantity. $order_qnty = $cu_receive";

					if(str_replace("'","",$cbo_receive_basis) == 1 && $pi_basis_id == 2)
					{
						$total_order_qnty += $order_qnty;
					}
					else
					{
						// THIS VALIDATION IS OFF ONLY FOR TEXTILE BUSINESS
						/*if($order_qnty>$cu_receive)
						{
							echo "50**Recv. Qnty exceeds Req. quantity for this fabric.\nRequired quantity balance = ".$cu_receive;
							disconnect($con);
							die;
						}*/
					}

					if(str_replace("'","",$process_costing_maintain)==0 && str_replace("'","",$cbo_receive_basis) == 11)
					{
						$cumu_grey_receive  = $prev_grey_rec_qty[$order_id]+$greyQntyPOwise;
						$order_required = $required_qty[$order_id] ;//+ ($required_qty[$order_id]/100)*$over_receive_limit;
						$required_balance =  $order_required - ($cumu_grey_receive - $greyQntyPOwise);
						if( $cumu_grey_receive> $order_required && str_replace("'","",$txt_payment_overrcv_qty)==0)
						{
							echo "50**Grey Qnty exceeds Req. quantity for this fabric.\nRequired quantity balance = ".number_format($required_balance,2,".","");
							disconnect($con);
							die;
						}
					}

					$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);

					if($data_array_prop!="" ) $data_array_prop.=",";
					$data_array_prop.="(".$id_prop.",".$id_trans.",1,37,".$id_dtls.",'".$order_id."','".$prod_id."','".$color_id."','".$order_qnty."','".$order_reject_qnty."','".$fabMeterQntyPOwise."','".$greyQntyPOwise."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hdn_is_sales.")";
					//$id_prop = $id_prop+1;

					if(!in_array(str_replace("'","",$cbo_receive_basis), $basis_arr))
					//if(str_replace("'","",$cbo_receive_basis)!=9 || str_replace("'","",$cbo_receive_basis)!=10)
					{
						$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
						if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
						$data_array_batch_dtls.="(".$id_dtls_batch.",'".$batch_id."','".$order_id."','".$prod_id."','".$ItemDesc."',".$txt_no_of_roll.",0,0,'".$order_qnty."',".$id_dtls.",".$cbo_dia_width_type.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					}
				}

				if($total_order_qnty > 0)
				{
					// THIS VALIDATION IS OFF ONLY FOR TEXTILE BUSINESS
					/*$cumu_receive=$required_qty[str_replace("'","",$txt_booking_no_id)]+(($required_qty[str_replace("'","",$txt_booking_no_id)]/100)*$over_receive_limit) - $prev_rec_qty[str_replace("'","",$txt_booking_no_id)];
					if($total_order_qnty>$cumu_receive)
					{
						echo "50**Recv. Qnty exceeds Req. quantity for this fabric.\nRequired quantity balance = ".$cumu_receive;
						disconnect($con);
						die;
					}*/
				}
			}
		}

		if(str_replace("'","",$booking_without_order)==1 && (!in_array(str_replace("'","",$cbo_receive_basis), $basis_arr)))
		{
			$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
			$data_array_batch_dtls="(".$id_dtls_batch.",'".$batch_id."',0,'".$prod_id."','".$ItemDesc."',".$txt_no_of_roll.",0,0,".$txt_production_qty.",".$id_dtls.",".$cbo_dia_width_type.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}

		if(str_replace("'","",$process_costing_maintain)==1)
		{
			if(str_replace("'","",$cbo_receive_basis)==9 || str_replace("'","",$cbo_receive_basis)==10 || str_replace("'","",$cbo_receive_basis)==11 || str_replace("'","",$cbo_receive_basis)==16)
			{
				$field_array_material="id,mst_id,dtls_id,entry_form,prod_id,item_category,used_qty,rate,amount,inserted_by,insert_date,
				status_active, is_deleted";
				$process_string=explode("*",str_replace("'","",$process_string));
				if($process_string[0]!="" || $process_string[0]!=0)
				{
					$id_material_used = return_next_id_by_sequence("PRO_MATERIAL_USED_DTLS_PK_SEQ", "pro_material_used_dtls", $con);
					$net_used=str_replace("'","",$txt_used_qty);//number_format(,4,".","");
					$gray_prod_id=str_replace("'","",$process_string[0]);
					$gray_rate=str_replace("'","",$process_string[2]);
					$gray_rate=number_format($gray_rate,4,".","");
					$used_amount=$gray_rate*$net_used;
					$used_amount=number_format($used_amount,4,".","");
					$data_array_material_used="(".$id_material_used.",".$finish_update_id.",".$id_dtls.",37,'".$gray_prod_id."',13,'".$net_used."','".$gray_rate."','".$used_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				}
			}
		}

		$rID=$rIDBatch=$rID2=$rID3=$rID4=$rID5=$rID6=$rID7=$rID8=$rID9=true;
		if(str_replace("'","",$update_id)=="")
		{
			//echo "5**insert into inv_receive_master (".$field_array.") values ".$data_array;die;
			$rID=sql_insert("inv_receive_master",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0;
		}


		if(str_replace("'","",$cbo_receive_basis)==9  || str_replace("'","",$cbo_receive_basis)==16)
		{
			$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;
			}
		}else if(str_replace("'","",$cbo_receive_basis)==10)
		{
			if(count($row_prod)>0)
			{
				$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,0);
				if($flag==1)
				{
					if($rID2) $flag=1; else $flag=0;
				}
			}
			else
			{
				//echo "10**insert into product_details_master (".$field_array_prod.") values ".$data_array_prod;die;
				$rID2=sql_insert("product_details_master",$field_array_prod,$data_array_prod,0);
				if($flag==1)
				{
					if($rID2) $flag=1; else $flag=0;
				}
			}
		}
		else
		{
			if(count($batchData)>0)
			{
				$rIDBatch=sql_update("pro_batch_create_mst",$field_array_batch_update,$data_array_batch_update,"id",$batch_id,0);
			}
			else
			{
				 //echo "10**insert into pro_batch_create_mst (".$field_array_batch.") values ".$data_array_batch;die;
				$rIDBatch=sql_insert("pro_batch_create_mst",$field_array_batch,$data_array_batch,0);
			}

			if($flag==1)
			{
				if($rIDBatch) $flag=1; else $flag=0;
			}

			if(count($row_prod)>0)
			{
				$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,0);
				if($flag==1)
				{
					if($rID2) $flag=1; else $flag=0;
				}
			}
			else
			{
				//echo "10**insert into product_details_master (".$field_array_prod.") values ".$data_array_prod;die;
				$rID2=sql_insert("product_details_master",$field_array_prod,$data_array_prod,0);
				if($flag==1)
				{
					if($rID2) $flag=1; else $flag=0;
				}
			}
		}

		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;oci_rollback($con);die;
		$rID3=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1)
		{
			if($rID3) $flag=1; else $flag=0;
		}

		//echo "10**insert into pro_finish_fabric_rcv_dtls (".$field_array_dtls.") values ".$data_array_dtls;oci_rollback($con);die;
		$rID4=sql_insert("pro_finish_fabric_rcv_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1)
		{
			if($rID4) $flag=1; else $flag=0;
		}

		//echo "10**";

		//echo "10**insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;oci_rollback($con);die;
		if($data_array_prop!="" && str_replace("'","",$booking_without_order)!=1)
		{

			$rID5=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
			if($flag==1)
			{
				if($rID5) $flag=1; else $flag=0;
			}
		}

		//echo "5**insert into pro_batch_create_dtls (".$field_array_batch_dtls.") values ".$data_array_batch_dtls;oci_rollback($con);die;
		if($data_array_batch_dtls!="")
		{
			//echo "5**insert into pro_batch_create_dtls (".$field_array_batch_dtls.") values ".$data_array_batch_dtls;die;
			$rID6=sql_insert("pro_batch_create_dtls",$field_array_batch_dtls,$data_array_batch_dtls,1);
			if($flag==1)
			{
				if($rID6) $flag=1; else $flag=0;
			}
		}

		if($data_array_roll!="" && str_replace("'","",$roll_maintained)==1)
		{
			//echo "5**insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
			$rID7=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
			if($flag==1)
			{
				if($rID7) $flag=1; else $flag=0;
			}
		}


		//echo "5**insert into pro_roll_details (".$field_array_roll_for_batch.") values ".$data_array_roll_for_batch;die;
		/*if($data_array_roll_for_batch!="" && str_replace("'","",$booking_without_order)!=1 && str_replace("'","",$roll_maintained)==1)
		{
			$rID8=sql_insert("pro_roll_details",$field_array_roll_for_batch,$data_array_roll_for_batch,0);
			if($flag==1)
			{
				if($rID8) $flag=1; else $flag=0;
			}
		}*/


		if(str_replace("'","",$process_costing_maintain)==1)
		{
			if($data_array_material_used!="")
			{
				//echo "5**insert into pro_material_used_dtls (".$field_array_material.") values ".$data_array_material_used;die;
				$rID9=sql_insert("pro_material_used_dtls",$field_array_material,$data_array_material_used,0);
				if($flag==1)
				{
					if($rID9) $flag=1; else $flag=0;
				}
			}
		}

		//echo "10**".$flag ."**".$rID."**".$rIDBatch."**".$rID2."**".$rID3."**".$rID4."**".$rID5."**".$rID6."**".$rID7."**".$rID8."**".$rID9;
		//oci_rollback($con); die;
		//echo "5**".$flag;die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "0**".$finish_update_id."**".$finish_recv_num."**0";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".$finish_update_id."**".$finish_recv_num."**0";
			}
			else
			{
				oci_rollback($con);
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		//---------------Check Receive control on Gate Entry according to variable settings inventory---------------------------//
		$hdn_is_sales=0;
		if($variable_set_invent==1)
		{
			$challan_no=str_replace("'","",$txt_challan_no);
			if($challan_no!="")
			{
				$variable_set_invent=return_field_value("a.id as id"," inv_gate_in_mst a,  inv_gate_in_dtl b","a.id=b.mst_id and a.company_id=$cbo_company_id and a.challan_no='$challan_no' and b.item_category_id=2  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","id");
				if(empty($variable_set_invent))
				{
					echo "30** This Item Not Found In Gate Entry. \n Please Gate Entry First.";
					disconnect($con);
					die;
				}
			}
		}

		// =========== Transaction level next transaction found validation start ================
		$recv_trans_sql="SELECT pi_wo_batch_no as batch_id, prod_id, body_part_id, store_id, floor_id, room, rack, self, bin_box, fabric_shade
		from inv_transaction where id=$update_trans_id and item_category=2 and status_active=1 and is_deleted=0 and transaction_type=1";
		// echo $recv_trans_sql;
		$recv_trans_sql_data=sql_select($recv_trans_sql);

		$batch_id 		= $recv_trans_sql_data[0][csf('batch_id')];
		$prod_id 		= $recv_trans_sql_data[0][csf('prod_id')];
		$body_part_id 	= $recv_trans_sql_data[0][csf('body_part_id')];
		$store_id 		= $recv_trans_sql_data[0][csf('store_id')];
		$floor_id 		= $recv_trans_sql_data[0][csf('floor_id')];
		$room 			= $recv_trans_sql_data[0][csf('room')];
		$rack 			= $recv_trans_sql_data[0][csf('rack')];
		$self 			= $recv_trans_sql_data[0][csf('self')];
		$bin_box 		= $recv_trans_sql_data[0][csf('bin_box')];
		$fabric_shade 	= $recv_trans_sql_data[0][csf('fabric_shade')];

		if($floor_id==""){$floor_id=0;}
		if($room==""){$room=0;}
		if($rack==""){$rack=0;}
		if($self==""){$self=0;}
		if($bin_box==""){$bin_box=0;}

		$max_trans_query = sql_select("SELECT max(id) as max_id from inv_transaction where pi_wo_batch_no=$batch_id and prod_id=$prod_id and body_part_id=$body_part_id and store_id=$store_id and floor_id=$floor_id and room=$room and rack=$rack and self=$self and bin_box=$bin_box and fabric_shade=$fabric_shade and item_category=2 and transaction_type in (2,3,6) and status_active=1");
		$max_trans_id = $max_trans_query[0][csf('max_id')];

		if($max_trans_id > str_replace("'", "", $update_trans_id))
		{
			echo "30**Next transaction found update not allowed.";die;
		}
		// echo "30**checking";die;
		// =========== Transaction level next transaction found validation end ================

		$mrr_issue_check = return_field_value("sum(issue_qnty) issue_qnty", "inv_mrr_wise_issue_details", "recv_trans_id=$update_trans_id and status_active=1 and is_deleted=0", "issue_qnty");
		if (str_replace("'", "", $txt_production_qty) < $mrr_issue_check) {
			echo "30**Receive quantity can not be less than Issue quantity.\nIssue quantity = $mrr_issue_check";
			disconnect($con);
			die;
		}

		//echo "10**";
		$receive_qnty=str_replace("'","",$txt_production_qty);
		if(str_replace("'","",$cbo_receive_basis)==14)
		{
			$privious_receive_qnty=return_field_value("sum(receive_qnty) as receive_qnty","inv_receive_master a, pro_finish_fabric_rcv_dtls b","a.id = b.mst_id and a.booking_no=$txt_booking_no and a.receive_basis=14 and b.fabric_description_id=$fabric_desc_id and b.pre_cost_fabric_cost_dtls_id=$pre_cost_fab_conv_cost_dtls_id and a.status_active=1 and b.status_active=1","receive_qnty");

			$privious_receive_qnty = ($privious_receive_qnty!="" && $privious_receive_qnty>0)?$privious_receive_qnty:0;

			$reqQnty=return_field_value("sum(grey_qnty_by_uom) fabric_qty","fabric_sales_order_dtls","status_active=1 and is_deleted=0 and job_no_mst=$txt_booking_no and determination_id=$fabric_desc_id","fabric_qty");

			$allowed_qnty = ($privious_receive_qnty+$receive_qnty)+(($over_receive_limit / 100) * ($privious_receive_qnty+$receive_qnty));
			if($allowed_qnty > $reqQnty){
				echo "30**Over receive is Allowed up to = ".$over_receive_limit . "%\nRequired quantity = ".$reqQnty."\nPrevious Receive=".($privious_receive_qnty);
				disconnect($con);
				die;
			}
		}
		//---------------End Check Receive control on Gate Entry---------------------------//

		//---------------Check Receive control when receive basis production according to production qnty---------------------------//

		if(str_replace("'","",$cbo_receive_basis)==9)
		{
			$production_qnty=return_field_value("receive_qnty","pro_finish_fabric_rcv_dtls","id=$fin_prod_dtls_id and status_active=1 and is_deleted=0","receive_qnty");
			$privious_receive_qnty=return_field_value("sum(receive_qnty) as receive_qnty","pro_finish_fabric_rcv_dtls","fin_prod_dtls_id=$fin_prod_dtls_id and id<>$update_dtls_id and status_active=1 and is_deleted=0","receive_qnty");
			$cu_production=$production_qnty+(($production_qnty/100)*$over_receive_limit);
			$current_rcv_qnty=$receive_qnty+$privious_receive_qnty;
			if($current_rcv_qnty>$cu_production)
			{
				echo "50** Receive Quantity Not Allow Over Then Production Quantity.\nProduction quantity = $production_qnty";
				disconnect($con);
				die;
			}
		}


		//---------------End Check Receive control when receive basis production---------------------------//

		if(str_replace("'","",$hidden_sample_booking_id)!="" && (str_replace("'","",$cbo_receive_basis)==4 || str_replace("'","",$cbo_receive_basis)==6)){
			$txt_booking_no_id 	= $hidden_sample_booking_id;
			$txt_booking_no 	= $txt_sample_booking_no;
		}
		$txt_gate_entry_no=str_replace("'","",$txt_gate_entry_no);
		$field_array_update="receive_basis*booking_id*booking_no*booking_without_order*receive_date*challan_no*yarn_issue_challan_no*store_id*location_id*knitting_source*knitting_company*knitting_location_id*qc_name*emp_id*updated_by*update_date*currency_id*exchange_rate*lc_sc_id*lc_sc_no*challan_date*gate_entry_no*gate_entry_date";

		$data_array_update=$cbo_receive_basis."*".$txt_booking_no_id."*".$txt_booking_no."*".$booking_without_order."*".$txt_receive_date."*".$txt_challan_no."*".$txt_grey_issue_challan_no."*".$cbo_store_name."*".$cbo_location."*".$cbo_dyeing_source."*".$cbo_dyeing_company."*".$cbo_dyeing_location."*".$txt_qc_name."*".$txt_hidden_qc_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_currency."*".$txt_exchange_rate."*".$hidden_lc_sc_id."*".$txt_lc_sc_no."*".$txt_challan_date."*'".$txt_gate_entry_no."'*".$txt_gate_entry_date;

		/*if(str_replace("'","",$cbo_receive_basis)==10) {
			$color_id=str_replace("'","",$txt_color_id);
			if ($color_id=='') {
				$color_id=0;
			}
		}else{
			$color_id=return_id( $txt_color, $color_arr, "lib_color", "id,color_name");
		}*/


		if(str_replace("'","",$cbo_receive_basis)==4 || str_replace("'","",$cbo_receive_basis)==6 || str_replace("'","",$cbo_receive_basis)==2  || str_replace("'","",$cbo_receive_basis)==11)
		{
			if (str_replace("'", "", trim($txt_color)) != "") {
				if (!in_array(str_replace("'", "", trim($txt_color)),$new_array_color)){
					$color_id = return_id( str_replace("'", "", trim($txt_color)), $color_arr, "lib_color", "id,color_name","37");
					$new_array_color[$color_id]=str_replace("'", "", trim($txt_color));
				}
				else $color_id =  array_search(str_replace("'", "", trim($txt_color)), $new_array_color);
			} else $color_id = 0;
			//$color_id=return_id( $txt_color, $color_arr, "lib_color", "id,color_name");
			//if ($color_id=='') {
				//$color_id=0;
			//}
		}else if(str_replace("'","",$cbo_receive_basis)==10 || str_replace("'","",$cbo_receive_basis)==16) {
			$color_id=str_replace("'","",$txt_color_id);
			if ($color_id=='') {
				$color_id=0;
			}
		}
		else
		{
			$color_id=$fabric_color_id;
			if ($color_id=='') {
				$color_id=0;
			}
		}


		$ItemDesc 	= str_replace("'","",$txt_fabric_desc).", ".str_replace("'","",$txt_gsm).", ".str_replace("'","",$txt_dia_width);

		$stockData 	= sql_select("select current_stock, avg_rate_per_unit, stock_value from product_details_master where id=$previous_prod_id");
		$stock 		= $stockData[0][csf('current_stock')];
		$avgRate 	= $stockData[0][csf('avg_rate_per_unit')];
		$stockValue = $stockData[0][csf('stock_value')];

		$txt_exchange_rate=str_replace("'","",$txt_exchange_rate);
		/*$order_rate 	= str_replace("'","",$txt_rate)*1;
		$order_amount 	= str_replace("'","",$txt_amount)*1;*/
		if(str_replace("'","",$cbo_receive_basis)==4 || str_replace("'","",$cbo_receive_basis)==6)
		{
			$order_rate 	= str_replace("'","",$txt_rate)*1;
			$order_amount 	= str_replace("'","",$txt_amount)*1;
		}
		else
		{
			$order_rate 	= str_replace("'","",$txt_hidden_rate)*1;
			$order_amount 	= str_replace("'","",$txt_hidden_amount)*1;
		}
		$cons_rate 		= $txt_exchange_rate*$order_rate;
		$cons_amount 	= str_replace("'","",$txt_production_qty)*$cons_rate;

		$rate=$txt_rate; $amount=$txt_amount;
		//$cons_rate=$txt_rate; $cons_amount=$txt_amount; $order_amount=$txt_amount; $order_rate=$txt_rate;
		$batch_id=str_replace("'","",$txt_batch_id);

		$dyeing_charge_data=explode("*",str_replace("'","",$knitting_charge_string));
		//$dyeing_charge=number_format($dyeing_charge_data[0],2,".","");
		$dyeing_charge=$dyeing_charge_data[0];
		//$grey_fabric_rate=number_format($dyeing_charge_data[1],2,".","");
		$grey_fabric_rate=$dyeing_charge_data[1];
		$material_deleted_id=$dyeing_charge_data[2];

		if(str_replace("'","",$cbo_receive_basis)==9 || str_replace("'","",$cbo_receive_basis)==16)
		{
			$prod_id=str_replace("'","",$product_id);

			if($prod_id==str_replace("'","",$previous_prod_id))
			{
				$cur_st_value=0; $cur_st_rate=0;
				$cur_st_qnty=$stock- (str_replace("'", '',$hidden_receive_qnty) - str_replace("'", '',$txt_production_qty));
				$cur_st_value=$stockValue -(str_replace("'", '',$hidden_receive_amnt) -str_replace("'", '',$cons_amount));
				if($cur_st_qnty>0){
					$cur_st_rate=number_format($cur_st_value/$cur_st_qnty,$dec_place[3],'.','');
				}else{
					$cur_st_rate=0;
					$cur_st_value=0;
				}

				$cur_st_qnty=number_format($cur_st_qnty,2,'.','');

				$field_array_prod_update="current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";
				$data_array_prod_update=$cur_st_qnty."*".$cur_st_rate."*".$cur_st_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

				if($cur_st_qnty<0)
				{
					echo "30**Stock cannot be less than zero.";
					disconnect($con);
					die;
				}
			}
			else
			{

				$adjust_curr_stock=$stock-str_replace("'", '',$hidden_receive_qnty);
				$adj_cur_st_value=$stockValue-str_replace("'", '',$hidden_receive_amnt);
				if($adjust_curr_stock>0){
					$adj_cur_st_rate=number_format($adj_cur_st_value/$adjust_curr_stock,$dec_place[3],'.','');
				}else{
					$adj_cur_st_rate=0;
					$adj_cur_st_value=0;
				}

				$adjust_curr_stock=number_format($adjust_curr_stock,2,'.','');
				$field_array_adjust="current_stock*avg_rate_per_unit*stock_value";
				$data_array_adjust=$adjust_curr_stock."*".$cur_st_rate."*".$cur_st_value;

				$currStockData=sql_select("select current_stock, avg_rate_per_unit, stock_value from product_details_master where id=$prod_id");
				$current_stock=$currStockData[0][csf('current_stock')];
				$current_stock_value=$currStockData[0][csf('stock_value')];
				$cur_st_qnty=$current_stock+str_replace("'", '',$txt_production_qty);
				$cur_st_value=$current_stock_value+str_replace("'", '',$cons_amount);
				if($cur_st_qnty>0){
					$cur_st_rate=number_format($cur_st_value/$cur_st_qnty,$dec_place[3],'.','');
				}else{
					$cur_st_rate=0;
					$cur_st_value=0;
				}
				$cur_st_qnty=number_format($cur_st_qnty,2,'.','');
				$field_array_prod_update="current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";
				$data_array_prod_update=$cur_st_qnty."*".$cur_st_rate."*".$cur_st_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

				if($adjust_curr_stock<0)
				{
					echo "30**Stock cannot be less than zero.";
					disconnect($con);
					die;
				}
			}
		}
		else if(str_replace("'","",$cbo_receive_basis)==10 )
		{
			// DELIVERY FROM TEXTILE
			if(str_replace("'","",$fabric_desc_id)=="") $fabric_desc_id=0;
			$prod_id = str_replace("'","",$product_id);

			// CHECK IF FABRICATION IS CHANGED
			if($prod_id == str_replace("'","",$previous_prod_id))
			{
				$row_prod=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id=$prod_id and status_active=1 and is_deleted=0 and is_gmts_product=1");

				$stock_qnty 		= $row_prod[0][csf('current_stock')];
				$stock_value 		= $row_prod[0][csf('stock_value')];

				$curr_stock_qnty 	= $stock_qnty - (str_replace("'", '',$hidden_receive_qnty) - str_replace("'", '',$txt_production_qty));
				$curr_stock_value 	= $stock_value - (str_replace("'", '',$hidden_receive_amnt) - str_replace("'", '',$cons_amount));
				if($curr_stock_qnty>0){
					$avg_rate_per_unit 	= number_format($curr_stock_value/$curr_stock_qnty,$dec_place[3],'.','');
				}else{
					$avg_rate_per_unit 	=0;
					$curr_stock_value 	=0;
				}

				if($curr_stock_qnty<0)
				{
					echo "30**Stock cannot be less than zero.";
					disconnect($con);
					die;
				}
				// UPDATE EXISTING PRODUCT
				$curr_stock_qnty=number_format($curr_stock_qnty,2,'.','');
				$field_array_prod_update = "store_id*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*color*updated_by*update_date";
				$data_array_prod_update  = $cbo_store_name."*".$avg_rate_per_unit."*".$txt_production_qty."*".$curr_stock_qnty."*".$curr_stock_value."*".$color_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			}
			else
			{
				// CHECK IF PRODUCT ALREADY EXIST
				$row_prod = sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id and item_category_id=2 and detarmination_id=$fabric_desc_id and gsm=$txt_gsm and upper(dia_width)=$txt_dia_width and color='$color_id' and unit_of_measure=$cbouom and status_active=1 and is_deleted=0 and is_gmts_product=1");
				if(count($row_prod) > 0)
				{
					// UPDATE PREVIOUS PRODUCT STOCK
					$adjust_curr_stock 	= $stock-str_replace("'", '',$hidden_receive_qnty);
					$cur_st_value 		= $stockValue-str_replace("'", '',$hidden_receive_amnt);
					if($adjust_curr_stock>0){
						$cur_st_rate 		= number_format($cur_st_value/$adjust_curr_stock,$dec_place[3],'.','');
					}else{
						$cur_st_rate 		=0;
						$cur_st_value  		=0;
					}

					$adjust_curr_stock=number_format($adjust_curr_stock,2,'.','');
					$field_array_adjust = "current_stock*avg_rate_per_unit*stock_value";
					$data_array_adjust 	= $adjust_curr_stock."*".$cur_st_rate."*".$cur_st_value;

					if($adjust_curr_stock<0)
					{
						echo "30**Stock cannot be less than zero.";
						disconnect($con);
						die;
					}
					// UPDATE NEW PRODUCT STOCK
					$stock_qnty 		= $row_prod[0][csf('current_stock')];
					$current_stock_value= $row_prod[0][csf('stock_value')];

					$curr_stock_qnty 	= $stock_qnty+str_replace("'", '',$txt_production_qty);
					$stock_value 		= $current_stock_value+str_replace("'", '',$cons_amount);
					if($curr_stock_qnty>0){
						$avg_rate_per_unit 	= number_format($stock_value/$curr_stock_qnty,$dec_place[3],'.','');
					}else{
						$avg_rate_per_unit 	=0;
						$stock_value=0;
					}

					$curr_stock_qnty=number_format($curr_stock_qnty,2,'.','');
					$field_array_prod_update = "store_id*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*color*updated_by*update_date";
					$data_array_prod_update  = $cbo_store_name."*".$avg_rate_per_unit."*".$txt_production_qty."*".$curr_stock_qnty."*".$stock_value."*".$color_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				}else{
					// UPDATE PREVIOUS PRODUCT AND CREATE NEW PRODUCT
					$adjust_curr_stock = $stock-str_replace("'", '',$hidden_receive_qnty);
					$cur_st_value 	   = $stockValue-str_replace("'", '',$hidden_receive_amnt);

					if($adjust_curr_stock >0)
					{
						$cur_st_rate = number_format($cur_st_value/$adjust_curr_stock,$dec_place[3],'.','');
					}else{
						$cur_st_rate = 0;
						$cur_st_value=0;
					}

					// STOCK VALIDATION
					if($adjust_curr_stock<0)
					{
						echo "30**Stock cannot be less than zero.";
						disconnect($con);
						die;
					}

					// PREVIOUS PRODUCT UPDATE
					$adjust_curr_stock=number_format($adjust_curr_stock,2,'.','');
					$field_array_adjust = "current_stock*avg_rate_per_unit*stock_value";
					$data_array_adjust 	= $adjust_curr_stock."*'".$cur_st_rate."'*".$cur_st_value;

					// NEW PRODUCT CREATE
					$prod_id 			= return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
					$stock_qnty 		= $txt_production_qty;
					$last_purchased_qnty= $txt_production_qty;
					$avg_rate_per_unit 	= $cons_rate;
					$stock_value 		= $cons_amount;

					$prod_name_dtls 	= trim(str_replace("'","",$txt_fabric_desc)).", ".trim(str_replace("'","",$txt_gsm)).", ".trim(str_replace("'","",$txt_dia_width));

					$stock_qnty=number_format($stock_qnty,2,'.','');
					$field_array_prod="id, company_id, store_id, item_category_id, detarmination_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, color, gsm, dia_width, inserted_by, insert_date,is_gmts_product";

					$data_array_prod="(".$prod_id.",".$cbo_company_id.",".$cbo_store_name.",2,".$fabric_desc_id.",".$txt_fabric_desc.",'".$prod_name_dtls."',".$cbouom.",".$avg_rate_per_unit.",".$last_purchased_qnty.",".$stock_qnty.",".$stock_value.",".$color_id.",".$txt_gsm.",".$txt_dia_width.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
			}
		}
		else
		{
			if(str_replace("'", '',$cbo_receive_basis)==1)
			{
				$booking_id=$hdn_booking_id;
				$booking_no=str_replace("'", '',$txt_po_booking_no);
			}
			else if(str_replace("'", '',$cbo_receive_basis)==2 || str_replace("'", '',$cbo_receive_basis)==11)
			{
				$booking_id=$txt_booking_no_id;
				$booking_no=str_replace("'", '',$txt_booking_no);
			}
			else
			{
				if(str_replace("'","",$hidden_sample_booking_id)!="" && (str_replace("'","",$cbo_receive_basis)==4 || str_replace("'","",$cbo_receive_basis)==6)){
					$booking_id 	= $hidden_sample_booking_id;
					$booking_no 	= str_replace("'", '',$txt_sample_booking_no);
				}
				else if(str_replace("'","",$hdn_booking_id)!="" && (str_replace("'","",$cbo_receive_basis)==4 || str_replace("'","",$cbo_receive_basis)==6)){
					$booking_id 	= $hdn_booking_id;
					$booking_no 	= str_replace("'", '',$txt_po_booking_no);
				}
				else{
					$booking_id=0;
					$booking_no='';
				}
			}

			$batchData=sql_select("select a.id, a.batch_weight from pro_batch_create_mst a where a.batch_no=$txt_batch_no and a.color_id='$color_id' and a.company_id= $cbo_company_id and a.status_active=1 and a.is_deleted=0 and a.entry_form=37 group by a.id, a.batch_weight");
			if(count($batchData)>0)
			{
				$batch_id=$batchData[0][csf('id')];
				if($batch_id==str_replace("'","",$txt_batch_id))
				{
					$curr_batch_weight=$batchData[0][csf('batch_weight')]+str_replace("'",'',$txt_production_qty)-str_replace("'",'',$hidden_receive_qnty);
					$field_array_batch_update="booking_no_id*booking_no*batch_weight*updated_by*update_date";
					$data_array_batch_update=$booking_id."*'".$booking_no."'*".$curr_batch_weight."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				}
				else
				{
					$batch_weight= return_field_value("batch_weight","pro_batch_create_mst","id=$txt_batch_id");
					$adjust_batch_weight=$batch_weight-str_replace("'", '',$hidden_receive_qnty);

					$curr_batch_weight=$batchData[0][csf('batch_weight')]+str_replace("'", '',$txt_production_qty);
					$field_array_batch_update="booking_no_id*booking_no*batch_weight*updated_by*update_date";
					$data_array_batch_update=$booking_id."*'".$booking_no."'*".$curr_batch_weight."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				}
			}
			else
			{
				$batch_weight= return_field_value("batch_weight","pro_batch_create_mst","id=$txt_batch_id");
				$adjust_batch_weight=$batch_weight-str_replace("'", '',$hidden_receive_qnty);

				//$batch_id=return_next_id( "id", "pro_batch_create_mst", 1 ) ;
				$batch_id = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
				$field_array_batch="id,batch_no,entry_form,batch_date,company_id,booking_no_id,booking_no,booking_without_order,color_id,batch_weight,inserted_by,insert_date";

				$data_array_batch="(".$batch_id.",".$txt_batch_no.",37,".$txt_receive_date.",".$cbo_company_id.",".$booking_id.",'".$booking_no."',".$booking_without_order.",'".$color_id."',".$txt_production_qty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}

			if(str_replace("'","",$fabric_desc_id)=="") $fabric_desc_id=0;

			if(str_replace("'","",$txt_dia_width) == "" && $db_type==2)
			{
				$prod_dia_cond = " and dia_width is null";
			}
			else
			{
				$prod_dia_cond = " and upper(dia_width) = $txt_dia_width";
			}

			if(str_replace("'","",$cbo_receive_basis)==2 && str_replace("'","",$booking_without_order)==1 && str_replace("'","",$fabric_desc_id)==0)
			{
				$fabric_description=trim(str_replace("'","",$txt_fabric_desc));
				$row_prod=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id and item_category_id=2 and detarmination_id=$fabric_desc_id and item_description='$fabric_description' and gsm=$txt_gsm $prod_dia_cond and color='$color_id' and unit_of_measure=$cbouom and is_gmts_product=1 and status_active=1 and is_deleted=0"); //and dia_width=$txt_dia_width
			}
			else
			{
				$row_prod=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id and item_category_id=2 and detarmination_id=$fabric_desc_id and gsm=$txt_gsm $prod_dia_cond and color='$color_id'  and unit_of_measure=$cbouom and is_gmts_product=1 and status_active=1 and is_deleted=0");
				//and dia_width=$txt_dia_width

				//echo "10**"."select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id and item_category_id=2 and detarmination_id=$fabric_desc_id and gsm=$txt_gsm $prod_dia_cond and color='$color_id' and status_active=1 and is_deleted=0";
				//oci_rollback($conn);die;
			}

			if(count($row_prod)>0)
			{
				$prod_id=$row_prod[0][csf('id')];

				//echo "1**".$prod_id."==".str_replace("'","",$previous_prod_id); die();

				if($prod_id==str_replace("'","",$previous_prod_id))
				{
					$stock_qnty=$row_prod[0][csf('current_stock')];
					$stock_value=$row_prod[0][csf('stock_value')];

					$curr_stock_qnty=$stock_qnty - (str_replace("'", '',$hidden_receive_qnty) - str_replace("'", '',$txt_production_qty));
					$curr_stock_value=$stock_value - (str_replace("'", '',$hidden_receive_amnt) - str_replace("'", '',$cons_amount));
					if($curr_stock_qnty>0){
						$avg_rate_per_unit=number_format($curr_stock_value/$curr_stock_qnty,$dec_place[3],'.','');
					}else{
						$avg_rate_per_unit=0;
						$curr_stock_value=0;
					}

					$curr_stock_qnty=number_format($curr_stock_qnty,2,'.','');
					$field_array_prod_update="store_id*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*color*updated_by*update_date";
					$data_array_prod_update=$cbo_store_name."*".$avg_rate_per_unit."*".$txt_production_qty."*".$curr_stock_qnty."*".$curr_stock_value."*".$color_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

					//echo "10**". $stock_qnty ."-".str_replace("'", '',$hidden_receive_qnty) ."-". str_replace("'", '',$txt_production_qty)."<br>".$curr_stock_qnty; die;

					if($curr_stock_qnty<0)
					{
						echo "30**Stock cannot be less than zero.";
						check_table_status( $_SESSION['menu_id'],0);
						disconnect($con);
						die;
					}
				}
				else
				{
					$adjust_curr_stock=$stock-str_replace("'", '',$hidden_receive_qnty);
					$cur_st_value=$stockValue-str_replace("'", '',$hidden_receive_amnt);
					if($adjust_curr_stock>0){
						$cur_st_rate=number_format($cur_st_value/$adjust_curr_stock,$dec_place[3],'.','');
					}else{
						$cur_st_rate=0;
						$cur_st_value=0;
					}

					$adjust_curr_stock=number_format($adjust_curr_stock,2,'.','');
					$field_array_adjust="current_stock*avg_rate_per_unit*stock_value";
					$data_array_adjust=$adjust_curr_stock."*".$cur_st_rate."*".$cur_st_value;

					if($adjust_curr_stock<0)
					{
						echo "30**Stock cannot be less than zero.";
						disconnect($con);
						die;
					}

					$stock_qnty=$row_prod[0][csf('current_stock')];
					$current_stock_value=$row_prod[0][csf('stock_value')];

					$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_production_qty);
					$stock_value=$current_stock_value+str_replace("'", '',$cons_amount);
					$avg_rate_per_unit=number_format($stock_value/$curr_stock_qnty,$dec_place[3],'.','');

					$curr_stock_qnty=number_format($curr_stock_qnty,2,'.','');
					$field_array_prod_update="store_id*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*color*updated_by*update_date";
					$data_array_prod_update=$cbo_store_name."*".$avg_rate_per_unit."*".$txt_production_qty."*".$curr_stock_qnty."*".$stock_value."*".$color_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

				}
			}
			else
			{
				$adjust_curr_stock=$stock-str_replace("'", '',$hidden_receive_qnty);
				$cur_st_value=$stockValue-str_replace("'", '',$hidden_receive_amnt);

				if($adjust_curr_stock > 0)
				{
					$cur_st_rate=number_format($cur_st_value/$adjust_curr_stock,$dec_place[3],'.','');
				}else{
					$cur_st_rate = 0;
					$cur_st_value=0;
				}


				if($adjust_curr_stock<0)
				{
					echo "30**Stock cannot be less than zero.";
					disconnect($con);
					die;
				}

				$adjust_curr_stock=number_format($adjust_curr_stock,2,'.','');
				$field_array_adjust="current_stock*avg_rate_per_unit*stock_value";
				$data_array_adjust=$adjust_curr_stock."*'".$cur_st_rate."'*".$cur_st_value;

				//$prod_id=return_next_id( "id", "product_details_master", 1 ) ;
				$prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				$stock_qnty=$txt_production_qty;
				$last_purchased_qnty=$txt_production_qty;
				$avg_rate_per_unit=$cons_rate;
				$stock_value=$cons_amount;

				$prod_name_dtls=trim(str_replace("'","",$txt_fabric_desc)).", ".trim(str_replace("'","",$txt_gsm)).", ".trim(str_replace("'","",$txt_dia_width));
				$field_array_prod="id, company_id, store_id, item_category_id, detarmination_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, color, gsm, dia_width, is_gmts_product, inserted_by, insert_date";

				$stock_qnty=number_format($stock_qnty,2,'.','');
				$data_array_prod="(".$prod_id.",".$cbo_company_id.",".$cbo_store_name.",2,".$fabric_desc_id.",".$txt_fabric_desc.",'".$prod_name_dtls."',".$cbouom.",".$avg_rate_per_unit.",".$last_purchased_qnty.",".$stock_qnty.",".$stock_value.",".$color_id.",".$txt_gsm.",".$txt_dia_width.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
		}

		if($prod_id==0 || $prod_id=="")
		{
			echo "20**Product Not Found.";
			disconnect($con);
			die;
		}

		//---------------Check Receive Date with Transaction Date -----------//
		$max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$prod_id and store_id= $cbo_store_name and status_active = 1 and id <> $update_trans_id", "max_date");
		if($max_transaction_date != "")
		{
			$max_transaction_date = date("Y-m-d", strtotime($max_transaction_date));
			$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_receive_date)));
			if ($receive_date < $max_transaction_date)
			{
				echo "20**Receive Date Can not Be Less Than Last Transaction Date Of This Lot";
				disconnect($con);
				die;
			}
		}

		//Batch mixing validation here
		if($mrr_wise_balancing_maintain==1)
		{
			//if multiple saved details found then batch_id should match with any previous one. otherwise single saved details can be updated with new batch.
			if($noOfDtlsSavedInMRR>1)
			{
				if($pre_batch_arr[$batch_id] =="")
				{
					echo "20**Batch mixing not allowed.";
					disconnect($con);
					die;
				}
			}
		}


		$sqlBl = sql_select("select cons_quantity,cons_amount,balance_qnty,balance_amount from inv_transaction where id=$update_trans_id");
		$before_receive_qnty	= $sqlBl[0][csf("cons_quantity")];
		$beforeAmount			= $sqlBl[0][csf("cons_amount")];
		$beforeBalanceQnty		= $sqlBl[0][csf("balance_qnty")];
		$beforeBalanceAmount	= $sqlBl[0][csf("balance_amount")];

		$adjBalanceQnty		=$beforeBalanceQnty-$before_receive_qnty+str_replace("'", '',$txt_production_qty);
		$adjBalanceAmount	=$beforeBalanceAmount-$beforeAmount+$cons_amount;

		$field_array_trans_update="receive_basis*pi_wo_batch_no*prod_id*transaction_date*store_id*order_qnty*order_rate*order_amount*cons_quantity*cons_reject_qnty*cons_rate*cons_amount*balance_qnty*balance_amount*machine_id*rack*self*floor_id*room*bin_box*fabric_shade*body_part_id*updated_by*update_date*pi_wo_dtls_id";

		$data_array_trans_update=$cbo_receive_basis."*'".$batch_id."'*".$prod_id."*".$txt_receive_date."*".$cbo_store_name."*".$txt_production_qty."*".$order_rate."*".$order_amount."*".$txt_production_qty."*".$txt_reject_qty."*".$cons_rate."*".$cons_amount."*".$adjBalanceQnty."*".$adjBalanceAmount."*".$cbo_machine_name."*".$txt_rack."*".$txt_shelf."*".$cbo_floor."*".$cbo_room."*".$cbo_bin."*".$cbo_fabric_type."*".$cbo_body_part."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$pi_dtls_id;

		$field_array_dtls_update="prod_id*batch_id*fabric_shade*body_part_id*fabric_description_id*gsm*width*color_id*receive_qnty*reject_qty*no_of_roll*order_id*buyer_id*machine_no_id*rack_no*shelf_no*floor*room*bin*dia_width_type*rate*amount*dyeing_charge*grey_fabric_rate*grey_used_qty*booking_no*remarks*updated_by*update_date*booking_id*pi_wo_dtls_id*original_width*yarn_issue_rate*knitting_rate*fabric_dyeing_rate*aop_rate*rate_string*payment_over_rcv_status";
		if(str_replace("'","",$cbo_receive_basis)==2 && str_replace("'","",$var_allow_fin_rcv_NR_hdn)==1 && str_replace("'","",$var_finish_rate_come_hdn)==2)
		{
			$cons_rate=str_replace("'","",$txt_hidden_rate)*1;

			$txt_hidden_yarn_issue_rate=str_replace("'","",$txt_hidden_yarn_issue_rate)*1;
			$txt_hidden_knitting_rate=str_replace("'","",$txt_hidden_knitting_rate)*1;
			$txt_hidden_dyeing_rate=str_replace("'","",$txt_hidden_dyeing_rate)*1;
			$txt_hidden_aop_rate=str_replace("'","",$txt_hidden_aop_rate)*1;
		}

		$data_array_dtls_update=$prod_id."*'".$batch_id."'*".$cbo_fabric_type."*".$cbo_body_part."*".$fabric_desc_id."*".$txt_gsm."*".$txt_dia_width."*".$color_id."*".$txt_production_qty."*".$txt_reject_qty."*".$txt_no_of_roll."*".$all_po_id."*".$buyer_id."*".$cbo_machine_name."*".$txt_rack."*".$txt_shelf."*".$cbo_floor."*".$cbo_room."*".$cbo_bin."*".$cbo_dia_width_type."*".$cons_rate."*".$cons_amount."*'".$dyeing_charge."'*'".$grey_fabric_rate."'*".$txt_used_qty."*".$txt_po_booking_no."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$hdn_booking_id."*".$pi_dtls_id."*".$txt_original_dia_width."*".$txt_hidden_yarn_issue_rate."*".$txt_hidden_knitting_rate."*".$txt_hidden_dyeing_rate."*".$txt_hidden_aop_rate."*".$hdn_rate_string."*".$txt_payment_overrcv_qty;

		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, returnable_qnty, fab_meter_qnty, grey_used_qty, inserted_by, insert_date,is_sales";

		$field_array_batch_dtls="id, mst_id, po_id, prod_id, item_description, roll_no, roll_id, barcode_no, batch_qnty, dtls_id,width_dia_type, inserted_by, insert_date";

		$barcode_year=date("y");
		$barcode_suffix_no=return_field_value("max(barcode_suffix_no) as suffix_no","pro_roll_details","barcode_year=$barcode_year","suffix_no")+1;// and entry_form=2
		$barcode_no=$barcode_year."37".str_pad($barcode_suffix_no,7,"0",STR_PAD_LEFT);

		$field_array_roll="id, barcode_year,barcode_suffix_no,barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,qc_pass_qnty, reject_qnty, fab_meter_qnty, grey_used_qty, roll_no, booking_without_order, inserted_by, insert_date";

		$field_array_roll_update="po_breakdown_id*qnty*reject_qnty*roll_no*updated_by*update_date";

		$field_array_roll_for_batch="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, qc_pass_qnty, roll_no, roll_id, inserted_by, insert_date";

		$save_string=explode(",",str_replace("'","",$save_data));
		$basis_arr=array(9,10);
		if(str_replace("'","",$roll_maintained)==1)
		{
			$po_array=array();
			for($i=0;$i<count($save_string);$i++)
			{
				$order_dtls=explode("**",$save_string[$i]);
				$order_id=$order_dtls[0];
				$order_qnty_roll_wise=$order_dtls[1];
				$roll_no=$order_dtls[2];
				$roll_id=$order_dtls[3];
				$barcodeNo=$order_dtls[4];
				$order_reject_qnty_roll_wise=$order_dtls[5];
				$fabMeterQntyRollwise=$order_dtls[7];
				$greyQntyRollwise=$order_dtls[8];

				if($roll_id=="" || $roll_id==0)
				{
					$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
					if($data_array_roll!="") $data_array_roll.=",";
					$data_array_roll.="(".$id_roll.",".$barcode_year.",'".$barcode_suffix_no."','".$barcode_no."',".$update_id.",".$update_dtls_id.",'".$order_id."',37,'".$order_qnty_roll_wise."','".$fabMeterQntyRollwise."','".$greyQntyRollwise."','".$order_qnty_roll_wise."','".$order_reject_qnty_roll_wise."','".$roll_no."',".$booking_without_order.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$rollId=$id_roll;

					// ###### only barcode creation entry form 2 asign for barcode suffix
					$barcode_suffix_no = explode("*", return_next_id_by_sequence("ROLL_BARCODE_SUFFIX_NO_SEQ", "pro_roll_details",$con,1,0,'',2,date("Y",time()),2 ));
					$barcodeNo=$barcode_no;
					//$barcode_suffix_no=$barcode_suffix_no+1;
					$barcode_no=$barcode_year."37".str_pad($barcode_suffix_no[2],7,"0",STR_PAD_LEFT);
					//$id_roll = $id_roll+1;
				}
				else
				{
					$roll_id_arr[]=$roll_id;
					$roll_data_array_update[$roll_id]=explode("*",($order_id."*'".$order_qnty_roll_wise."'*'".$order_reject_qnty_roll_wise."'*'".$roll_no."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					$rollId=$roll_id;
				}

				$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);

				if($data_array_batch_dtls!="" ) $data_array_batch_dtls.=",";
				$data_array_batch_dtls.="(".$id_dtls_batch.",'".$batch_id."','".$order_id."','".$prod_id."','".$ItemDesc."','".$roll_no."','".$rollId."',".$barcodeNo.",".$order_qnty_roll_wise.",".$update_dtls_id.",".$cbo_dia_width_type.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				/*if($data_array_roll_for_batch!="") $data_array_roll_for_batch.= ",";
				$data_array_roll_for_batch.="(".$id_roll.",'".$barcodeNo."',".$batch_id.",".$id_dtls_batch.",'".$order_id."',37,'".$order_qnty_roll_wise."','".$order_qnty_roll_wise."','".$roll_no."','".$rollId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";*/
				//$id_roll = $id_roll+1;

				//$id_dtls_batch = $id_dtls_batch+1;
				$po_array[$order_id]['qnty']+=$order_qnty_roll_wise;
				$po_array[$order_id]['reject_qnty']+=$order_reject_qnty_roll_wise;
				$po_array[$order_id]['fab_meter_qnty']+=$fabMeterQntyRollwise;
				$po_array[$order_id]['grey_qnty']+=$greyQntyRollwise;
			}

			foreach($po_array as $key=>$val)
			{
				$order_id=$key;
				$order_qnty=$val['qnty'];

				$cu_receive=$required_qty[$order_id]+(($required_qty[$order_id]/100)*$over_receive_limit) - $prev_rec_qty[$order_id];
				// THIS VALIDATION IS OFF ONLY FOR TEXTILE BUSINESS
				/*if($order_qnty>$cu_receive)
				{
					echo "50**Recv. Qnty exceeds Req. quantity for this fabric.\nRequired quantity balance = ".$cu_receive;
					disconnect($con);
					die;
				}*/

				$order_reject_qnty=$val['reject_qnty'];
				$fabMeterQntyPOwise=$val['fab_meter_qnty'];
				$grey_qnty=$val['grey_qnty'];
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				if($data_array_prop!="") $data_array_prop.= ",";
				$data_array_prop.="(".$id_prop.",".$update_trans_id.",1,37,".$update_dtls_id.",'".$order_id."','".$prod_id."','".$color_id."','".$order_qnty."','".$order_reject_qnty."','".$fabMeterQntyPOwise."','".$grey_qnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hdn_is_sales.")";
				//$id_prop = $id_prop+1;
			}
		}
		else
		{
			if(str_replace("'","",$booking_without_order)==1 && (str_replace("'","",$cbo_receive_basis) == 1 || str_replace("'","",$cbo_receive_basis) == 2 || str_replace("'","",$cbo_receive_basis) == 4 || str_replace("'","",$cbo_receive_basis) == 6 || str_replace("'","",$cbo_receive_basis) == 9 || str_replace("'","",$cbo_receive_basis) == 11))
			{

				if(str_replace("'","",$cbo_receive_basis) == 1 || str_replace("'","",$cbo_receive_basis) == 9)
				{
					$cumu_receive  = $prev_rec_qty_non_order[str_replace("'","",$hdn_booking_id)]+str_replace("'","",$txt_production_qty);
					$order_qnty = $required_qty[str_replace("'","",$hdn_booking_id)] + ($required_qty[str_replace("'","",$hdn_booking_id)]/100)*$over_receive_limit;
				}
				else if(str_replace("'","",$cbo_receive_basis) == 2 || str_replace("'","",$cbo_receive_basis) == 11)
				{
					$cumu_receive  = $prev_rec_qty_non_order[str_replace("'","",$txt_booking_no_id)]+str_replace("'","",$txt_production_qty);
					$order_qnty = $required_qty[str_replace("'","",$txt_booking_no_id)] + ($required_qty[str_replace("'","",$txt_booking_no_id)]/100)*$over_receive_limit;
				}
				else
				{
					$cumu_receive  = $prev_rec_qty_non_order[str_replace("'","",$hidden_sample_booking_id)]+str_replace("'","",$txt_production_qty);
					$order_qnty = $required_qty[str_replace("'","",$hidden_sample_booking_id)] + ($required_qty[str_replace("'","",$hidden_sample_booking_id)]/100)*$over_receive_limit;
				}

				//echo "10**".$order_qnty."=".$cumu_receive;die;
				// THIS VALIDATION IS OFF ONLY FOR TEXTILE BUSINESS
				if( $cumu_receive> $order_qnty && str_replace("'","",$txt_payment_overrcv_qty)==0)
				{
					echo "50**Recv. Qnty exceeds Req. quantity for this fabric.\nRequired quantity balance = ".$order_qnty;
					disconnect($con);
					die;
				}

				if(str_replace("'","",$process_costing_maintain)==0 && str_replace("'","",$cbo_receive_basis) == 11)
				{
					$cumu_grey_receive  = $prev_grey_rec_qty_non_order[str_replace("'","",$txt_booking_no_id)]+str_replace("'","",$txt_used_qty);
					$order_qnty = $required_qty[str_replace("'","",$txt_booking_no_id)] ;//+ ($required_qty[str_replace("'","",$txt_booking_no_id)]/100)*$over_receive_limit;
					$required_balance =  $order_qnty - ($cumu_grey_receive - str_replace("'","",$txt_used_qty));
					if( $cumu_grey_receive> $order_qnty && str_replace("'","",$txt_payment_overrcv_qty)==0)
					{
						echo "50**Grey Qnty exceeds Req. quantity for this fabric.\nRequired quantity balance = ".number_format($required_balance,2,".","");
						disconnect($con);
						die;
					}
				}

			}
			else if(str_replace("'","",$cbo_receive_basis) == 16 && str_replace("'","",$booking_without_order)==0)
			{
				for($i=0;$i<count($save_string);$i++)
				{
					$order_dtls=explode("_",$save_string[$i]);
					$order_id=$order_dtls[0];
					$order_qnty=$order_dtls[1];
					$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					if($data_array_prop!="" ) $data_array_prop.=",";
					$data_array_prop.="(".$id_prop.",".$update_trans_id.",1,37,".$update_dtls_id.",'".$order_id."','".$prod_id."','".$color_id."','".$order_qnty."','0','0',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0)";
				}
			}
			else
			{
				for($i=0;$i<count($save_string);$i++)
				{
					$order_dtls=explode("**",$save_string[$i]);
					$order_id=$order_dtls[0];
					$order_qnty=$order_dtls[1];
					$order_reject_qnty=$order_dtls[5];
					$fabMeterQntyPOwise=$order_dtls[7];
					$greyQntyPOwise=$order_dtls[8];
					//$cu_receive=$cumu_rec_qty[$order_id]+(($cumu_rec_qty[$order_id]/100)*$over_receive_limit) - $prev_rec_qty[$order_id];
					//(required_qnty + over_recv_perc)

					$cu_receive=$required_qty[$order_id]+(($required_qty[$order_id]/100)*$over_receive_limit) - $prev_rec_qty[$order_id];
					//echo "50** Receive Quantity Not Allow Over Req Quantity. $order_qnty = $cu_receive";
					//(248.93+2,240,370,000‬) - 273.62

					// THIS VALIDATION IS OFF ONLY FOR TEXTILE BUSINESS
					/*if($order_qnty>$cu_receive)
					{
						echo "50**Recv. Qnty exceeds Req. quantity for this fabric.\nRequired quantity balance = ".$cu_receive;
						disconnect($con);
						die;
					}*/

					if(str_replace("'","",$process_costing_maintain)==0 && str_replace("'","",$cbo_receive_basis) == 11)
					{
						$cumu_grey_receive  = $prev_grey_rec_qty[$order_id]+$greyQntyPOwise;
						$order_required = $required_qty[$order_id] ;//+ ($required_qty[$order_id]/100)*$over_receive_limit;
						$required_balance =  $order_required - ($cumu_grey_receive - $greyQntyPOwise);
						if( $cumu_grey_receive> $order_required && str_replace("'","",$txt_payment_overrcv_qty)==0)
						{
							echo "50**Grey Qnty exceeds Req. quantity for this fabric.\nRequired quantity balance = ".number_format($required_balance,2,".","");
							disconnect($con);
							die;
						}
					}

					$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);

					if($data_array_prop!="" ) $data_array_prop.=",";
					$data_array_prop.="(".$id_prop.",".$update_trans_id.",1,37,".$update_dtls_id.",'".$order_id."','".$prod_id."','".$color_id."','".$order_qnty."','".$order_reject_qnty."','".$fabMeterQntyPOwise."','".$greyQntyPOwise."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hdn_is_sales.")";

					$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, returnable_qnty, fab_meter_qnty, grey_used_qty, inserted_by, insert_date,is_sales";

					if(!in_array(str_replace("'","",$cbo_receive_basis), $basis_arr))
					//if(str_replace("'","",$cbo_receive_basis)!=9)
					{
						$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);

						if($data_array_batch_dtls!="" ) $data_array_batch_dtls.=",";
						$data_array_batch_dtls.="(".$id_dtls_batch.",'".$batch_id."','".$order_id."','".$prod_id."','".$ItemDesc."',".$txt_no_of_roll.",0,0,'".$order_qnty."',".$update_dtls_id.",".$cbo_dia_width_type.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					}
				}
			}
		}

		$basis_arr=array(9,10);

		if(str_replace("'","",$booking_without_order)==1 && (!in_array(str_replace("'","",$cbo_receive_basis), $basis_arr)))
		//if(str_replace("'","",$booking_without_order)==1 && (str_replace("'","",$cbo_receive_basis)!=9 || str_replace("'","",$cbo_receive_basis)!=10))
		{
			$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
			$data_array_batch_dtls="(".$id_dtls_batch.",'".$batch_id."',0,'".$prod_id."','".$ItemDesc."',".$txt_no_of_roll.",0,0,".$txt_production_qty.",".$update_dtls_id.",".$cbo_dia_width_type.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}

		if($db_type==0)
		{
			$batch_dtls_id_for_delete=return_field_value("group_concat(id) as dtls_id","pro_batch_create_dtls","mst_id=$txt_batch_id and dtls_id=$update_dtls_id","dtls_id");
		}
		else
		{
			$batch_dtls_id_for_delete=return_field_value("LISTAGG(id,',') WITHIN GROUP (ORDER BY id) as dtls_id","pro_batch_create_dtls","mst_id=$txt_batch_id and dtls_id=$update_dtls_id","dtls_id");
		}

		if(str_replace("'","",$process_costing_maintain)==1)
		{
			$field_array_material="id,mst_id,dtls_id,entry_form,prod_id,item_category,used_qty,rate,amount,inserted_by,insert_date,status_active, is_deleted";
			$field_array_material_update="used_qty*rate*amount*updated_by*update_date";
			$product_dtls=explode("*",str_replace("'","",$process_string));

			$used_prod_id=$product_dtls[0];
			$net_used=number_format($product_dtls[1],4,".","");
			$gray_rate=number_format($product_dtls[2],4,".","");
			$used_amount=number_format($gray_rate*$net_used,4,".","");
			$material_update_id=$product_dtls[3];
			if($material_update_id>0)
			{
				$material_id_arr[]=$material_update_id;
				$material_data_array_update[$material_update_id]=explode("*",("'".$net_used."'*'".$gray_rate."'* '".$used_amount."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			else
			{
				$id_material_used = return_next_id_by_sequence("PRO_MATERIAL_USED_DTLS_PK_SEQ", "pro_material_used_dtls", $con);
				$data_array_material_used="(".$id_material_used.",".$update_id.",".$update_dtls_id.",37,'".$used_prod_id."',13,'".$net_used."','".$gray_rate."','".$used_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

			}
		}

		$rID=$rID_adjust=$rID2=$rID6=$rID_batch_adjust=$delete_batch_dtls=$delete_batch_roll=$rID_adjust=$rID3=$rID4=$delete_prop=$rID5=$rollUpdate=$statusChange=$rID7=$rID8=$deletedMaterial=true;

		$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;

		if(str_replace("'","",$cbo_receive_basis)==9 || str_replace("'","",$cbo_receive_basis)==10 || str_replace("'","",$cbo_receive_basis)==16)
		{
			if(str_replace("'","",$product_id)==str_replace("'","",$previous_prod_id))
			{
				$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
				if($flag==1)
				{
					if($rID2) $flag=1; else $flag=0;
				}
			}
			else
			{
				$rID_adjust=sql_update("product_details_master",$field_array_adjust,$data_array_adjust,"id",$previous_prod_id,0);
				if($flag==1)
				{
					if($rID_adjust) $flag=1; else $flag=0;
				}

				$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
				if($flag==1)
				{
					if($rID2) $flag=1; else $flag=0;
				}
			}
		}
		else
		{
			if(count($batchData)>0)
			{
				if($batch_id==str_replace("'","",$txt_batch_id))
				{
					//echo "1**".$batch_id ."==". $txt_batch_id; die();
					//echo "1**test"; die();
					$rID6=sql_update("pro_batch_create_mst",$field_array_batch_update,$data_array_batch_update,"id",$batch_id,0);
					if($flag==1)
					{
						if($rID6) $flag=1; else $flag=0;
					}
				}
				else
				{
					//echo "1**test"; die();
					$rID_batch_adjust=sql_update("pro_batch_create_mst","batch_weight",$adjust_batch_weight,"id",$txt_batch_id,0);
					if($flag==1)
					{
						if($rID_batch_adjust) $flag=1; else $flag=0;
					}
					$rID6=sql_update("pro_batch_create_mst",$field_array_batch_update,$data_array_batch_update,"id",$batch_id,0);
					if($flag==1)
					{
						if($rID6) $flag=1; else $flag=0;
					}
				}
			}
			else
			{
				//echo "1**ddd"; die();
				$rID_batch_adjust=sql_update("pro_batch_create_mst","batch_weight",$adjust_batch_weight,"id",$txt_batch_id,0);
				if($flag==1)
				{
					if($rID_batch_adjust) $flag=1; else $flag=0;
				}
				//echo "10**insert into pro_batch_create_mst (".$field_array_batch.") values ".$data_array_batch;die;
				$rID6=sql_insert("pro_batch_create_mst",$field_array_batch,$data_array_batch,0);
				if($flag==1)
				{
					if($rID6) $flag=1; else $flag=0;
				}
			}

			$delete_batch_dtls=execute_query( "delete from pro_batch_create_dtls where mst_id=$txt_batch_id and dtls_id=$update_dtls_id",0);
			if($flag==1)
			{
				if($delete_batch_dtls) $flag=1; else $flag=0;
			}


			/*if($batch_dtls_id_for_delete!="")
			{
				$delete_batch_roll=execute_query("delete from pro_roll_details where mst_id=$txt_batch_id and dtls_id in ($batch_dtls_id_for_delete) and entry_form=37",0);
				if($flag==1)
				{
					if($delete_batch_roll) $flag=1; else $flag=0;
				}
			}*/

			if(count($row_prod)>0)
			{
				if($prod_id==str_replace("'","",$previous_prod_id))
				{
					$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,0);
					if($flag==1)
					{
						if($rID2) $flag=1; else $flag=0;
					}
				}
				else
				{
					$rID_adjust=sql_update("product_details_master",$field_array_adjust,$data_array_adjust,"id",$previous_prod_id,0);
					if($flag==1)
					{
						if($rID_adjust) $flag=1; else $flag=0;
					}

					$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,0);
					if($flag==1)
					{
						if($rID2) $flag=1; else $flag=0;
					}
				}
			}
			else
			{

				$rID_adjust=sql_update("product_details_master",$field_array_adjust,$data_array_adjust,"id",$previous_prod_id,0);
				if($flag==1)
				{
					if($rID_adjust) $flag=1; else $flag=0;
				}

				$rID2=sql_insert("product_details_master",$field_array_prod,$data_array_prod,0);
				if($flag==1)
				{
					if($rID2) $flag=1; else $flag=0;
				}
			}
		}


		$rID3=sql_update("inv_transaction",$field_array_trans_update,$data_array_trans_update,"id",$update_trans_id,0);
		if($flag==1)
		{
			if($rID3) $flag=1; else $flag=0;
		}


		$rID4=sql_update("pro_finish_fabric_rcv_dtls",$field_array_dtls_update,$data_array_dtls_update,"id",$update_dtls_id,0);
		if($flag==1)
		{
			if($rID4) $flag=1; else $flag=0;
		}



		$delete_prop=execute_query( "delete from order_wise_pro_details where dtls_id=$update_dtls_id and trans_id=$update_trans_id and entry_form=37",0);
		if($flag==1)
		{
			if($delete_prop) $flag=1; else $flag=0;
		}
		//echo "10**insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;oci_rollback($con);die;
		if($data_array_prop!="" && str_replace("'","",$booking_without_order)!=1)
		{
			$rID5=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
			if($flag==1)
			{
				if($rID5) $flag=1; else $flag=0;
			}
		}


		$rID6=true;
		if($data_array_batch_dtls!="")
		{
			//echo "6**insert into pro_batch_create_dtls (".$field_array_batch_dtls.") values ".$data_array_batch_dtls;die;

			$rID6=sql_insert("pro_batch_create_dtls",$field_array_batch_dtls,$data_array_batch_dtls,1);
			if($flag==1)
			{
				if($rID6) $flag=1; else $flag=0;
			}
		}
		//echo "10**=".$flag;die;


		if(str_replace("'","",$roll_maintained)==1)
		{
			$txt_deleted_id=str_replace("'","",$txt_deleted_id);
			if($txt_deleted_id!="")
			{
				$field_array_status="updated_by*update_date*status_active*is_deleted";
				$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";

				$statusChange=sql_multirow_update("pro_roll_details",$field_array_status,$data_array_status,"id",$txt_deleted_id,0);
				if($flag==1)
				{
					if($statusChange) $flag=1; else $flag=0;
				}
			}

			if(count($roll_data_array_update)>0  && str_replace("'","",$roll_maintained)==1)
			{
				$rollUpdate=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_update, $roll_data_array_update, $roll_id_arr ));
				if($flag==1)
				{
					if($rollUpdate) $flag=1; else $flag=0;
				}
			}

			if($data_array_roll!=""  && str_replace("'","",$roll_maintained)==1)
			{
				//echo "6**insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
				$rID6=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
				if($flag==1)
				{
					if($rID6) $flag=1; else $flag=0;
				}
			}

			/*if($data_array_roll_for_batch!="" && str_replace("'","",$booking_without_order)!=1)
			{
				//echo "insert into pro_roll_details (".$field_array_roll_for_batch.") values ".$data_array_roll_for_batch;die;
				$rID7=sql_insert("pro_roll_details",$field_array_roll_for_batch,$data_array_roll_for_batch,0);
				if($flag==1)
				{
					if($rID7) $flag=1; else $flag=0;
				}
			}*/
		}



		if(str_replace("'","",$process_costing_maintain)==1)
		{
			if(count($material_data_array_update)>0)
			{
				//echo bulk_update_sql_statement( "pro_material_used_dtls", "id", $field_array_material_update, $material_data_array_update, $material_id_arr );
				$materialUpdate=execute_query(bulk_update_sql_statement( "pro_material_used_dtls", "id", $field_array_material_update, $material_data_array_update, $material_id_arr ));
				if($flag==1)
				{
					if($materialUpdate) $flag=1; else $flag=0;
				}
			}

			if($data_array_material_used!="")
			{
				//echo "insert into pro_material_used_dtls (".$field_array_material.") values ".$data_array_material_used;die;
				$rID8=sql_insert("pro_material_used_dtls",$field_array_material,$data_array_material_used,0);
				if($flag==1)
				{
					if($rID8) $flag=1; else $flag=0;
				}
			}
			if($material_deleted_id!="")
			{
				$deletedMaterial=execute_query( "delete from pro_material_used_dtls where id in($material_deleted_id) ",0);
				if($flag==1)
				{
					if($deletedMaterial) $flag=1; else $flag=0;
				}
			}
		}

		//echo "6**$data_array_material_used";die; $rID6 $rID6
		//echo "6**".$rID_batch_adjust; die();
		//echo "10**$flag##$rID## $rID_adjust ## $rID2 ## $rID6 ## $rID_batch_adjust ## $delete_batch_dtls ## $delete_batch_roll ## $rID_adjust ## $rID3 ## $rID4 ## $delete_prop ## $rID5 ## $rID6 ## $rollUpdate ## $statusChange ## $rID7 ## $rID8 ## $deletedMaterial##$test";	oci_rollback($con); die;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**0";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**0**0**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "6**0**0**1";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$batch_id=str_replace("'","",$txt_batch_id);
		$update_id=str_replace("'","",$update_id);

		if ($update_id=="")
		{
			echo "30**Delete not allowed";disconnect($con);die;
		}
		$sql = sql_select("SELECT a.prod_id,a.cons_quantity,a.cons_rate,a.cons_amount,b.avg_rate_per_unit,b.current_stock,b.stock_value, a.store_id, a.pi_wo_batch_no from inv_transaction a, product_details_master b where a.status_active=1 and a.id=$update_trans_id and a.mst_id=$update_id and a.prod_id=b.id");
		if (empty($sql))
		{
			echo "30**Delete not allowed";disconnect($con);die;
		}
		$before_prod_id=$before_receive_qnty=$before_rate=$beforeAmount=$beforeAvgRate="";
		$beforeStock=$beforeStockValue=$trans_batch_id=$trans_store_id=0;
		foreach( $sql as $row)
		{
			$before_prod_id 		= $row[csf("prod_id")];
			$before_receive_qnty 	= $row[csf("cons_quantity")]; //stock qnty
			$before_rate 			= $row[csf("cons_rate")];
			$beforeAmount			= $row[csf("cons_amount")]; //stock value
			$beforeStock			= $row[csf("current_stock")];
			$beforeStockValue		= $row[csf("stock_value")];
			$beforeAvgRate			= $row[csf("avg_rate_per_unit")];
			$trans_batch_id			= $row[csf("pi_wo_batch_no")];
			$trans_store_id			= $row[csf("store_id")];
		}
		//stock value minus here---------------------------//
		$adj_beforeStock			= $beforeStock-$before_receive_qnty;
		$adj_beforeStockValue		= $beforeStockValue-$beforeAmount;
		if($adj_beforeStockValue>0 && $adj_beforeStock>0)
		{
			$adj_beforeAvgRate		= number_format(($adj_beforeStockValue/$adj_beforeStock),$dec_place[3],'.','');
		}
		else
		{
			$adj_beforeAvgRate		= 0;
			$adj_beforeStockValue	= 0;
		}

		$max_trans_query = sql_select("SELECT max(case when transaction_type in (2,3,6) then transaction_date else null end) as max_date, max(id) as max_id from inv_transaction where prod_id =$before_prod_id and store_id=$trans_store_id and pi_wo_batch_no=$trans_batch_id and item_category=2 and status_active=1");
		$max_trans_id = $max_trans_query[0][csf('max_id')];

		if($max_trans_id > str_replace("'", "", $update_trans_id))
		{
			echo "30**Next transaction found of this store and product. update/delete not allowed.";
			die;
		}

		/*$chk_next_transaction=return_field_value("id","inv_transaction","transaction_type in(2,3,6) and prod_id=$before_prod_id and store_id=$trans_store_id and pi_wo_batch_no=$trans_batch_id and item_category=2 and status_active=1 and is_deleted=0 and id >$update_id ","id");
		if($chk_next_transaction !="")
		{
			echo "30**Delete not allowed.This item is used in another transaction";disconnect($con);die;
		}*/
		else
		{
			$update_trans_id = str_replace("'","",$update_trans_id);
			$product_id = str_replace("'","",$product_id);
			if( str_replace("'","",$update_trans_id) == "" )
			{
				echo "30**Delete not allowed.";disconnect($con); die;
			}
			else
			{
				if(str_replace("'","",$process_costing_maintain)==1)
				{
					$material_table_id=return_field_value("id","pro_material_used_dtls","mst_id=$update_id and status_active=1 and is_deleted=0 and dtls_id=$update_dtls_id ","id");
				}

				$batchData=sql_select("SELECT a.id, a.batch_weight,a.booking_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=$trans_batch_id and a.dtls_id=$update_dtls_id and a.company_id =$cbo_company_id and a.status_active=1 and a.is_deleted=0 and a.entry_form=37");

				$field_array = "updated_by*update_date*status_active*is_deleted";
				$data_array = "'" . $user_id . "'*'" . $pc_date_time . "'*0*1";
				$checkTransaction = sql_select("SELECT id from pro_finish_fabric_rcv_dtls where status_active=1 and is_deleted=0 and mst_id = ".$update_id." and id !=".$update_dtls_id."");
				if(count($checkTransaction) == 0)
				{
					$is_mst_del = sql_update("inv_receive_master", $field_array, $data_array, "id", $update_id, 1);
					if($is_mst_del) $flag=1; else $flag=0;
				}

				$rID=sql_update("inv_transaction",$field_array,$data_array,"id",$update_trans_id,1);
				if($rID) $flag=1; else $flag=0;

				$rID3=sql_update("pro_finish_fabric_rcv_dtls",$field_array,$data_array,"id",$update_dtls_id,1);
				if($rID3) $flag=1; else $flag=0;

				if(str_replace("'","",$process_costing_maintain)==1 && $material_table_id!="")
				{
					$rID4=sql_update("pro_material_used_dtls",$field_array,$data_array,"id",$material_table_id,1);
					if($rID4) $flag=1; else $flag=0;
				}
				//$rID5=1;
				if(str_replace("'","",$roll_maintained)==1 && str_replace("'","",$save_data)!="")
				{
					$rID5=sql_update("pro_roll_details",$field_array,$data_array,"dtls_id*entry_form","$update_dtls_id*37",1);
					if($rID5) $flag=1; else $flag=0;

					/*$rID6=sql_update("pro_roll_details",$field_array,$data_array,"mst_id*dtls_id*entry_form","$batch_id*$update_dtls_id*37",1);
					if($rID6) $flag=1; else $flag=0;*/
				}
				//$rID6=1;
				if(str_replace("'","",$save_data)!="")
				{
					$rID7=sql_update("order_wise_pro_details",$field_array,$data_array,"dtls_id*trans_id*entry_form","$update_dtls_id*$update_trans_id*37",1);
					if($rID7) $flag=1; else $flag=0;
				}

				if (!empty($batchData))
				{
					$curr_batch_weight=$batchData[0][csf('batch_weight')]-str_replace("'", '',$before_receive_qnty);
					$field_array_batch_update="batch_weight*updated_by*update_date";
					$data_array_batch_update=$curr_batch_weight."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
					$rIDBatch=sql_update("pro_batch_create_mst",$field_array_batch_update,$data_array_batch_update,"id",$trans_batch_id,0);
					if($rIDBatch) $flag=1; else $flag=0;

					$batchDtls=sql_update("pro_batch_create_dtls",$field_array,$data_array,"mst_id*dtls_id","$trans_batch_id*$update_dtls_id",1);
					if($batchDtls) $flag=1; else $flag=0;
				}

				$field_array_product="avg_rate_per_unit*current_stock*stock_value*updated_by*update_date";
				$data_array_product = "".$adj_beforeAvgRate."*".$adj_beforeStock."*".number_format($adj_beforeStockValue,$dec_place[4],'.','')."*'".$user_id."'*'".$pc_date_time."'";
				$rID2=sql_update("product_details_master",$field_array_product,$data_array_product,"id",$before_prod_id,1);
				if($rID2) $flag=1; else $flag=0;
			}
		}
		// echo "10**$rID##$rID2##$rID3##$rID4##$rID5##$rID7##$rIDBatch##$batchDtls##$is_mst_del**$flag";oci_rollback($con);disconnect($con);die;
		// ﻿﻿﻿﻿ 10**1##1##1##########1##1##**1 $roll_maintained == 0
		// ﻿10**1##1##1####1##1##1##1##1##**1 $roll_maintained==1
		// batch table common, order table common

		if($db_type==0)
		{
			// if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6)
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**0"."**".$is_mst_del;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**1";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			// if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6)
			if($flag==1)
			{
				oci_commit($con);
				echo "2**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**0"."**".$is_mst_del;
			}
			else
			{
				oci_rollback($con);
				echo "10**1";
			}
		}
		disconnect($con);
		die;
	}

}

//====================SYSTEM ID POPUP========
if ($action=="systemId_popup")
{
	echo load_html_head_contents("System ID Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(id,booking_no,is_posted_acc)
		{
			$('#hidden_sys_id').val(id);
			$("#hidden_booking_no").val(booking_no);
			$("#hidden_posted_in_account").val(is_posted_acc);
			parent.emailwindow.hide();
		}
	</script>
</head>

<body>
	<div align="center" style="width:980px;">
		<form name="searchsystemidfrm"  id="searchsystemidfrm">
			<fieldset style="width:970px;">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="800" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Receive Date Range</th>
						<th>Buyer</th>
						<th>Search By</th>
						<th id="search_by_td_up">Please Enter System Id</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
							<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
							<input type="hidden" name="hidden_sys_id" id="hidden_sys_id" class="text_boxes" value="">
							<input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes" value="">
							<input type="hidden" name="hidden_posted_in_account" id="hidden_posted_in_account" class="text_boxes" value="">
						</th>
					</thead>
					<tr class="general">
						<td>
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;">To<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;">
						</td>
						<td id="">
							<?
							echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
							?>
						</td>
						<td>
							<?
							$search_by_arr=array(1=>"System ID",2=>"Challan No",3=>"Batch No",4=>"Style Ref.",5=>"WO No");
							$dd="change_search_event(this.value, '0*0*0*0*0', '0*0*0*0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td id="search_by_td">
							<input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_buyer_name').value, 'create_finish_search_list_view', 'search_div', 'knit_finish_fabric_receive_by_garments_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="margin-top:10px; margin-left:3px;" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_finish_search_list_view")
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$buyer_id =$data[5];

	if($data[0]=="" && $start_date=="" && $end_date=="" && $buyer_id==0 )
	{
		echo "<p style='text-align:center;color:red;font-size:20px;font-weight:bold;'>"."Please specify at least one search term"."</p>"; die;
	}
	
	if($buyer_id!=0) $buyer_cond="and b.buyer_id like '%$buyer_id%'";
	else $buyer_cond="";

	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}

	if(trim($data[0])!="")
	{
		if($search_by==1)
			$search_field_cond="and a.recv_number_prefix_num =".trim($data[0])."";
		else if($search_by==2)
			$search_field_cond="and a.challan_no like '$search_string'";
		else if($search_by==3)
			$search_field_cond="and c.batch_no like '$search_string'";
		else if($search_by==4)
			//$search_field_cond="and c.id in($all_batch_id)";
			$search_field_cond="and f.style_ref_no like '$search_string'";
		else
			$search_field_cond="and a.booking_no like '$search_string'";
	}
	else
	{
		$search_field_cond="";
	}

	if($db_type==0)
	{
		$year_field="YEAR(a.insert_date)";
		$batch_field="group_concat(c.batch_no)";
		$order_id_field="group_concat(b.order_id)";
	}
	else if($db_type==2)
	{
		$year_field="to_char(a.insert_date,'YYYY')";
		$batch_field="LISTAGG(c.batch_no, ',') WITHIN GROUP (ORDER BY c.id)";
		$order_id_field="LISTAGG(b.order_id, ',') WITHIN GROUP (ORDER BY b.order_id)";
	}
	else
	{
		$year_field="null";
		$batch_field="null";
		$order_id_field="null";
	}

	$sql ="select a.id, a.recv_number, a.recv_number_prefix_num, a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company, a.receive_date, a.challan_no, a.is_posted_account, b.buyer_id, to_char(a.insert_date,'YYYY') as year, b.receive_qnty as recv_qty,b.grey_used_qty, c.batch_no, b.batch_id, f.style_ref_no, b.id as dtls_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b left join order_wise_pro_details d on b.id=d.dtls_id and d.entry_form= 37 and d.status_active=1 left join wo_po_break_down e on D.PO_BREAKDOWN_ID=e.id left join wo_po_details_master f on e.job_id=f.id , pro_batch_create_mst c where a.id=b.mst_id and b.batch_id=c.id and a.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id $search_field_cond $date_cond $buyer_cond group by a.id,a.recv_number, a.recv_number_prefix_num, a.receive_basis, a.is_posted_account, b.buyer_id, b.batch_id, b.receive_qnty,b.grey_used_qty, a.booking_no, a.knitting_source, a.knitting_company, a.receive_date, a.challan_no, a.insert_date, c.batch_no, b.batch_id, f.style_ref_no, b.id order by a.id desc ";
	$result = sql_select($sql);
	foreach ($result as $row)
	{
		$data_array[$row[csf("id")]]["id"] 						= $row[csf("id")];
		$data_array[$row[csf("id")]]["recv_number"] 			= $row[csf("recv_number")];
		$data_array[$row[csf("id")]]["recv_number_prefix_num"] 	= $row[csf("recv_number_prefix_num")];
		$data_array[$row[csf("id")]]["receive_basis"] 			= $row[csf("receive_basis")];
		$data_array[$row[csf("id")]]["booking_no"] 				= $row[csf("booking_no")];
		$data_array[$row[csf("id")]]["knitting_source"] 		= $row[csf("knitting_source")];
		$data_array[$row[csf("id")]]["knitting_company"] 		= $row[csf("knitting_company")];
		$data_array[$row[csf("id")]]["receive_date"] 			= $row[csf("receive_date")];
		$data_array[$row[csf("id")]]["challan_no"] 				= $row[csf("challan_no")];
		$data_array[$row[csf("id")]]["is_posted_account"] 		= $row[csf("is_posted_account")];
		$data_array[$row[csf("id")]]["buyer_id"] 				= $row[csf("buyer_id")];
		$data_array[$row[csf("id")]]["year"] 					= $row[csf("year")];
		if($dtls_arr[$row[csf("dtls_id")]]=="")
		{
			$dtls_arr[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
			$data_array[$row[csf("id")]]["recv_qty"] 				+= $row[csf("recv_qty")];
			$data_array[$row[csf("id")]]["grey_used_qty"] 			+= $row[csf("grey_used_qty")];
		}
		$data_array[$row[csf("id")]]["batch_no"] 				.= $row[csf("batch_no")].",";
		$data_array[$row[csf("id")]]["style_ref_no"] 			.= $row[csf("style_ref_no")].",";
	}

	//$sql = "select a.id, a.recv_number, a.recv_number_prefix_num, a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company, a.receive_date, a.challan_no, a.is_posted_account, b.buyer_id, $year_field as year, sum(b.receive_qnty) as recv_qty, $batch_field as batch_no, b.batch_id, $order_id_field as order_id from inv_receive_master a,pro_finish_fabric_rcv_dtls b,pro_batch_create_mst c where a.id=b.mst_id and b.batch_id=c.id and a.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id $search_field_cond $date_cond $buyer_cond group by a.id,a.recv_number, a.recv_number_prefix_num, a.receive_basis, a.is_posted_account, b.buyer_id, b.batch_id, a.booking_no, a.knitting_source, a.knitting_company, a.receive_date, a.challan_no, a.insert_date order by a.id desc";

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1220" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="50">Year</th>
			<th width="70">Received ID</th>
			<th width="100">Receive Basis</th>
			<th width="115">WO/PI/Prod. No</th>
			<th width="100">Buyer</th>
			<th width="100">Style Ref</th>
			<th width="90">Dyeing Source</th>
			<th width="110">Dyeing Company</th>
			<th width="80">Receive date</th>
			<th width="80">Receive Qnty</th>
			<th width="80">Grey Qnty</th>
			<th width="80">Challan No</th>
			<th>Batch No</th>
		</thead>
	</table>
	<div style="width:1240px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1220" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;
			foreach ($data_array as $sys_id => $row)
			//foreach ($result as $row)
			{
				if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";

				if($row['knitting_source']==1)
					$dye_comp=$company_arr[$row['knitting_company']];
				else
					$dye_comp=$supllier_arr[$row['knitting_company']];

				$recv_qnty=$row['recv_qty'];
				$grey_used_qnty=$row['grey_used_qty'];
				$buyer_name='';
				$buyer=explode(",",$row['buyer_id']);
				foreach($buyer as $val )
				{
					if($buyer_name=='') $buyer_name=$buyer_arr[$val]; else $buyer_name.=",".$buyer_arr[$val];
				}
				$style_ref='';
				$style_ref =implode(",",array_unique(explode(",", chop($row['style_ref_no'],',')  )));
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row['id']; ?>,'<? echo $row['booking_no']; ?>','<? echo $row['is_posted_account']; ?>');">
					<td width="40"><? echo $i; ?></td>
					<td width="50" align="center"><p><? echo $row['year']; ?></p></td>
					<td width="70"><p>&nbsp;<? echo $row['recv_number_prefix_num']; ?></p></td>
					<td width="100"><p><? echo $receive_basis_arr[$row['receive_basis']]; ?></p></td>
					<td width="115"><p><? echo $row['booking_no']; ?>&nbsp;</p></td>
					<td width="100"><p><? echo $buyer_name; ?>&nbsp;</p></td>
					<td width="100"><p><? echo $style_ref; ?>&nbsp;</p></td>
					<td width="90"><p><? echo $knitting_source[$row['knitting_source']]; ?></p></td>
					<td width="110"><p><? echo $dye_comp; ?></p></td>
					<td width="80" align="center"><? echo change_date_format($row['receive_date']); ?></td>
					<td width="80" align="right"><? echo number_format($recv_qnty,2); ?>&nbsp;</td>
					<td width="80" align="right"><? echo number_format($grey_used_qnty,2); ?>&nbsp;</td>
					<td width="80"><p><? echo $row['challan_no']; ?>&nbsp;</p></td>
					<td><p><? echo implode(",",array_unique(explode(",",chop($row['batch_no'],',')))); ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<?
	exit();
}

if($action=='populate_data_from_finish_fabric')
{
	$data_array=sql_select("select id, recv_number, company_id, receive_basis,currency_id,exchange_rate, booking_id, booking_no, booking_without_order, store_id, location_id, knitting_source, knitting_company,knitting_location_id, receive_date, challan_no,yarn_issue_challan_no,qc_name,emp_id, lc_sc_no,lc_sc_id, store_id, is_audited, challan_date,gate_entry_no,gate_entry_date from inv_receive_master where id='$data'");

	$company_id=$data_array[0][csf("company_id")];
	$variable_inventory_sql=sql_select("select store_method, rack_balance from variable_settings_inventory  where company_name=$company_id and item_category_id=2 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$store_method=$variable_inventory_sql[0][csf("store_method")];

	foreach ($data_array as $row)
	{
		if($row[csf("receive_basis")] == 14)
		{
			$sales_order_arr[]="'".$row[csf("booking_no")]."'";
		}
		if($row[csf("receive_basis")] == 11)
		{
			$bookingID=$row[csf("booking_id")];
			if($row[csf("booking_without_order")]==0)
			{
				$sql="select id as booking_id, booking_no, booking_no as fabric_booking, buyer_id, job_no from wo_booking_mst where id='$bookingID'";
			}
			else
			{
				$sql="select a.id as booking_id, a.booking_no, a.buyer_id, '' as job_no, b.booking_no as fabric_booking from wo_non_ord_knitdye_booking_mst a, wo_non_ord_samp_booking_mst b where  a.fab_booking_id = b.id and a.id='$bookingID'";
			}
			$data_array_booking=sql_select($sql);
			foreach ($data_array_booking as $row)
			{
				echo "document.getElementById('txt_sales_booking_no').value 	= '".$row[csf("fabric_booking")]."';\n";
			}
		}
	}
	if(!empty($sales_order_arr))
	{
		$sales_sql = "select job_no,sales_booking_no from fabric_sales_order_mst where job_no in(".implode(",",$sales_order_arr).") and status_active=1";
		$sales_order_rs = sql_select($sales_sql);
		foreach ($sales_order_rs as $sales_row) {
			$sales_arr[$sales_row[csf("job_no")]] = $sales_row[csf("sales_booking_no")];
		}

	}
	foreach ($data_array as $row)
	{
		echo "document.getElementById('txt_system_id').value 					= '".$row[csf("recv_number")]."';\n";
		echo "document.getElementById('cbo_receive_basis').value 				= '".$row[csf("receive_basis")]."';\n";
		echo "document.getElementById('cbo_company_id').value 					= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('store_update_upto').value 				= '" . $store_method . "';\n";
		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		echo "set_receive_basis();\n";
		if($row[csf("receive_basis")]==4 || $row[csf("receive_basis")]==6){
			echo "document.getElementById('hidden_sample_booking_id').value 	= '".$row[csf("booking_id")]."';\n";
			echo "document.getElementById('txt_sample_booking_no').value 		= '".$row[csf("booking_no")]."';\n";
			echo "$('#txt_sample_booking_no').attr('disabled','true')".";\n";
		}else{
			echo "document.getElementById('txt_booking_no_id').value 			= '".$row[csf("booking_id")]."';\n";
			echo "document.getElementById('txt_booking_no').value 				= '".$row[csf("booking_no")]."';\n";
		}
		echo "document.getElementById('cbo_currency').value 					= '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value 				= '".$row[csf("exchange_rate")]."';\n";
		if($row[csf("receive_basis")] != 11)
		{
			echo "document.getElementById('txt_sales_booking_no').value 		= '".$sales_arr[$row[csf("booking_no")]]."';\n";
		}
		echo "document.getElementById('booking_without_order').value 			= '".$row[csf("booking_without_order")]."';\n";

		echo "$('#txt_booking_no').attr('disabled','true')".";\n";
		echo "$('#cbo_receive_basis').attr('disabled','true')".";\n";
		if($row[csf("booking_without_order")]==1)
		{
			echo "$('#txt_production_qty').removeAttr('readonly','readonly');\n";
			echo "$('#txt_production_qty').removeAttr('onClick','onClick');\n";
			echo "$('#txt_production_qty').removeAttr('placeholder','placeholder');\n";
		}
		else
		{
			echo "$('#txt_production_qty').attr('readonly','readonly');\n";
			echo "$('#txt_production_qty').attr('onClick','openmypage_po();');\n";
			echo "$('#txt_production_qty').attr('placeholder','Single Click');\n";
		}

		echo "document.getElementById('txt_receive_date').value 			= '".change_date_format($row[csf("receive_date")])."';\n";
		echo "$('#txt_receive_date').attr('disabled','true')".";\n";
		echo "document.getElementById('cbo_dyeing_source').value 			= '".$row[csf("knitting_source")]."';\n";

		echo "load_drop_down('requires/knit_finish_fabric_receive_by_garments_controller', ".$row[csf("knitting_source")]."+'_'+".$row[csf("company_id")].", 'load_drop_down_dyeing_com', 'dyeingcom_td' );\n";

		echo "document.getElementById('cbo_dyeing_company').value 			= '".$row[csf("knitting_company")]."';\n";
		echo "load_location();\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_grey_issue_challan_no').value 	= '".$row[csf("yarn_issue_challan_no")]."';\n";
		echo "document.getElementById('cbo_location').value 				= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('cbo_dyeing_location').value 			= '".$row[csf("knitting_location_id")]."';\n";
		echo "document.getElementById('txt_challan_date').value 			= '".change_date_format($row[csf("challan_date")])."';\n";
		echo "document.getElementById('txt_gate_entry_no').value 			= '".$row[csf("gate_entry_no")]."';\n";
		echo "document.getElementById('txt_gate_entry_date').value 			= '".change_date_format($row[csf("gate_entry_date")])."';\n";

		if($row[csf("receive_basis")]==16){
			echo "$('#cbo_dyeing_company').attr('disabled','true')".";\n";
			echo "$('#cbo_dyeing_source').attr('disabled','true')".";\n";
			echo "$('#cbo_dyeing_location').attr('disabled','true')".";\n";
			echo "$('#cbo_location').attr('disabled','true')".";\n";
		}

		echo "load_room_rack_self_bin('requires/knit_finish_fabric_receive_by_garments_controller*2', 'store','store_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."',this.value);\n";

		echo "store_update_upto_disable()\n";

		// Check Audited
		if($row[csf("is_audited")]==1) echo "$('#audited').text('Audited');\n";
		else echo "$('#audited').text('');\n";

		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("store_id")]."';\n";
		echo "document.getElementById('txt_qc_name').value 				= '".$row[csf("qc_name")]."';\n";
		echo "document.getElementById('txt_hidden_qc_name').value 				= '".$row[csf("emp_id")]."';\n";
		echo "document.getElementById('txt_lc_sc_no').value 				= '".$row[csf("lc_sc_no")]."';\n";
		echo "document.getElementById('hidden_lc_sc_id').value 				= '".$row[csf("lc_sc_id")]."';\n";
		echo "document.getElementById('update_id').value 					= '".$row[csf("id")]."';\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_finish_receive_entry',1,1);\n";
		exit();
	}
}

$for_side_list_view="";
if($action=="show_finish_fabric_listview")
{
	$for_side_list_view=$data;
	//echo $for_side_list_view;
	$machine_arr = return_library_array("select id, machine_no as machine_name from lib_machine_name","id","machine_name");
	$fabric_desc_arr=return_library_array("select id, item_description from product_details_master where item_category_id=2","id","item_description");
	//$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst","id","batch_no");


	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id order by b.id asc";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}

	$sql="select a.receive_basis,b.id, b.prod_id, b.batch_id, c.batch_no, b.body_part_id, b.fabric_description_id, b.gsm, b.width, b.color_id, b.receive_qnty, b.reject_qty, b.machine_no_id,b.order_id,b.remarks from  inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c where a.id=b.mst_id and b.batch_id=c.id and b.mst_id='$data' and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 ";
	$result=sql_select($sql);

	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="920" class="rpt_table" style="float: left;">
		<thead>
			<th width="80">Batch</th>
			<th width="100">Body Part</th>
			<th width="150">Fabric Description</th>
			<th width="60">GSM</th>
			<th width="70">Dia / Width</th>
			<th width="85">Fabric Color</th>
			<th width="80">QC Pass Qty</th>
			<th width="80">Reject Qty</th>
			<th width="80">Machine No</th>
			<th>Remarks</th>
		</thead>
	</table>
	<div style="width:920px; max-height:200px; overflow-y:scroll; float: left;" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" id="list_view" style="float: left;">
			<?
			$i=1;
			foreach($result as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if($row[csf('fabric_description_id')]==0 || $row[csf('fabric_description_id')]=="")
					$fabric_desc=$fabric_desc_arr[$row[csf('prod_id')]];
				else
					$fabric_desc=$composition_arr[$row[csf('fabric_description_id')]];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_data_dtls_part(<? echo $row[csf('id')]; ?>,'populate_finish_details_form_data', 'requires/knit_finish_fabric_receive_by_garments_controller');">
					<td width="80"><p><? echo $row[csf('batch_no')]; ?></p></td>
					<td width="100"><p><? echo $body_part[$row[csf('body_part_id')]]; ?>&nbsp;</p></td>
					<td width="150"><p><? echo $fabric_desc; ?></p></td>
					<td width="60"><p><? echo $row[csf('gsm')]; ?>&nbsp;</p></td>
					<td width="70"><p><? echo $row[csf('width')]; ?>&nbsp;</p></td>
					<td width="85" align="center"><p><? echo $color_arr[$row[csf('color_id')]]; ?>&nbsp;</p></td>
					<td width="80" align="right"><? echo number_format($row[csf('receive_qnty')],2); ?></td>
					<td width="80" align="right"><? echo number_format($row[csf('reject_qty')],2); ?>&nbsp;</td>
					<td width="80"><p><? echo $machine_arr[$row[csf('machine_no_id')]]; ?>&nbsp;</p></td>
					<td><? echo $row[csf('remarks')]; ?></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<?
	exit();
}

if($action=='populate_finish_details_form_data')
{
	$data=explode("**",$data);
	$id=$data[0];
	$roll_maintained=$data[1];
	$process_costing_maintain=$data[2];
	$var_finish_rate_come_hdn=$data[3];
	$var_allow_fin_rcv_NR_hdn=$data[4];

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	$data_array=sql_select("SELECT a.company_id,a.location_id,a.booking_no as non_order_booking, a.booking_id,a.receive_basis,a.qc_name,a.emp_id,a.store_id, a.is_posted_account, b.id, b.trans_id,b.fabric_shade, a.yarn_issue_challan_no,b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id, b.gsm, b.width, b.color_id, b.receive_qnty, b.reject_qty, b.no_of_roll, b.machine_no_id, rack_no, shelf_no,b.floor,b.room,b.bin, b.order_id, b.buyer_id,b.dia_width_type,c.order_rate rate,c.order_amount amount,b.fin_prod_dtls_id, b.uom ,b.grey_used_qty,b.pre_cost_fabric_cost_dtls_id,b.is_sales,b.fabric_shade,b.job_no,b.booking_no,b.booking_id booking_no_id,b.pi_wo_dtls_id,b.remarks, a.booking_without_order, original_width,b.rate as rate_charge_nr,b.yarn_issue_rate,b.knitting_rate,b.fabric_dyeing_rate,b.aop_rate,b.rate_string, b.payment_over_rcv_status from inv_receive_master a, inv_transaction c,pro_finish_fabric_rcv_dtls b where a.id=c.mst_id and c.id=b.trans_id and b.id=$id and a.item_category=2 and a.entry_form=37 and a.status_active=1 and c.status_active=1 and b.status_active=1");

	foreach ($data_array as $row)
	{
		$order_id = explode("_",$row[csf("order_id")]);
		$order_ids[$order_id[0]]= $order_id[0];
		$trans_ids[$order_id[0]]=$row[csf("trans_id")];
		$delivery_ids[$row[csf("booking_id")]]=$row[csf("booking_id")];
		$batch_ids[$row[csf("batch_id")]]=$row[csf("batch_id")];
		$color_ids[$row[csf("color_id")]]=$row[csf("color_id")];
		$booking_no_arr[$row[csf('booking_no')]] = "'".$row[csf('booking_no')]."'";
		$is_sample=$row[csf('booking_without_order')];
		$job_no_arr[$row[csf("job_no")]]="'".$row[csf("job_no")]."'";
		$receive_basis=$row[csf('receive_basis')];
	}
	if($receive_basis==1)
	{
		if($is_sample!=1)
		{
			$sql_body_part_id="select a.id, a.job_no, a.body_part_id, a.lib_yarn_count_deter_id, a.construction, a.composition from wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls b where a.job_no=b.job_no and a.job_no in(".implode(",",$job_no_arr).") and b.booking_no in(".implode(",",$booking_no_arr).") and a.status_active=1 and a.is_deleted=0 group by a.id,a.job_no,a.body_part_id,a.lib_yarn_count_deter_id,a.construction,a.composition order by a.id";
			$sql_body_part_id=sql_select($sql_body_part_id);
			$body_part_id_Arr=array();
			$body_part_id='';
			foreach($sql_body_part_id as $roww)
			{
				$body_part_id_Arr[$roww[csf('job_no')]][$roww[csf('lib_yarn_count_deter_id')]].=$roww[csf('body_part_id')].",";
			}
		}
		else
		{
			$sql_body_part_id="select booking_no, construction, composition, lib_yarn_count_deter_id, body_part from wo_non_ord_samp_booking_dtls where booking_no in (".implode(",",$booking_no_arr).")";
			$sql_body_part_id=sql_select($sql_body_part_id);
			$body_part_id_Arr=array();
			$body_part_id='';
			foreach($sql_body_part_id as $roww)
			{
				$body_part_id_Arr[$roww[csf('booking_no')]][$roww[csf('lib_yarn_count_deter_id')]].=$roww[csf('body_part')].",";
			}
		}
	}

	if(!empty($batch_ids)){
		$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst where id in(".implode(",",$batch_ids).")","id","batch_no");
	}
	if(!empty($color_ids)){
		$color_arr=return_library_array( "select id, color_name from lib_color where id in(".implode(",",$color_ids).")",'id','color_name');
	}
	$delivery_sql="select a.id,b.prod_id,b.pi_wo_batch_no, b.fabric_shade,d.body_part_id, c.detarmination_id,c.color,c.unit_of_measure uom , sum(b.cons_quantity ) issue_qnty from inv_issue_master a, inv_transaction b,inv_finish_fabric_issue_dtls d, product_details_master c where a.id=b.mst_id and b.id=d.trans_id and d.prod_id=c.id and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in(".implode(",",$delivery_ids).") and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by a.id,b.prod_id,b.pi_wo_batch_no,b.fabric_shade,d.body_part_id,c.detarmination_id,c.color,c.unit_of_measure";
	$delivery_data = sql_select($delivery_sql);
	//$delivery_data=sql_select("select a.id,b.prod_id,b.fabric_shade,sum(b.cons_quantity ) issue_qnty from inv_issue_master a, inv_transaction b where a.id=b.mst_id and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in(".implode(",",$delivery_ids).") group by a.id,b.prod_id,b.fabric_shade");
	$delivery_qnty_arr=array();
	if(!empty($delivery_data)){
		foreach ($delivery_data as $delivery) {
			$delivery_qnty_arr[$delivery[csf("id")]][$delivery[csf("pi_wo_batch_no")]][$delivery[csf("body_part_id")]][$delivery[csf("detarmination_id")]][$delivery[csf("color")]][$delivery[csf("uom")]][$delivery[csf("fabric_shade")]] += $delivery[csf("issue_qnty")];
		}
	}

	$job_no=$order_nos="";
	$order_ids = array_filter($order_ids);
	if(!empty($order_ids)){
		if($db_type==0)
		{
			$po_info=sql_select("select job_no_mst,group_concat(po_number) as po_number from wo_po_break_down where id in(".implode(",",$order_ids).") group by job_no_mst");
		}
		else
		{
			//print_r($order_ids);
			$po_info=sql_select("select job_no_mst,listagg(cast(po_number as varchar2(4000)), ',') within group (order by id) as po_number from wo_po_break_down where id in(".implode(",",$order_ids).") group by job_no_mst");
		}
		foreach ($po_info as $po) {
			$job_no .= $po[csf("job_no_mst")].",";
			$order_nos .= $po[csf("po_number")].",";
		}
	}
	$job_nos=rtrim($job_no,", ");
	$order_nos=rtrim($order_nos,", ");

	if(!empty($trans_ids)){
		$trans_info=sql_select("select b.id,a.dyeing_charge,b.order_rate,b.order_amount,a.grey_fabric_rate from pro_finish_fabric_rcv_dtls a,inv_transaction b where a.trans_id=b.id and b.id in(".implode(",",$trans_ids).")");
		foreach ($trans_info as $trans) {
			$trans_arr[$trans[csf("id")]]["dye_charge"]=$trans[csf("dyeing_charge")];
			$trans_arr[$trans[csf("id")]]["order_rate"]=$trans[csf("order_rate")];
			$trans_arr[$trans[csf("id")]]["order_amount"]=$trans[csf("order_amount")];
			$trans_arr[$trans[csf("id")]]["grey_fabric_rate"]=$trans[csf("grey_fabric_rate")];
		}
	}

	foreach ($data_array as $row)
	{
		$buyer_name='';
		$buyer=explode(",",$row[csf('buyer_id')]);
		foreach($buyer as $val )
		{
			if($buyer_name=='') $buyer_name=$buyer_arr[$val]; else $buyer_name.=",".$buyer_arr[$val];
		}

		//$delivery_qnty = $delivery_qnty_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][$row[csf("fabric_shade")]];
		$delivery_qnty = $delivery_qnty_arr[$row[csf("booking_id")]][$row[csf("batch_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description_id")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("fabric_shade")]];
		if($row[csf('receive_basis')]==10)
		{
			echo "document.getElementById('hdn_delivery_qnty').value 				= '".$delivery_qnty."';\n";
		}

		if($row[csf('receive_basis')]==9 || $row[csf('receive_basis')]==11)
		{
			$booking_without_order=return_field_value("booking_without_order","pro_batch_create_mst","id=".$row[csf("batch_id")]."");
			//if($booking_without_order==1 && $row[csf('receive_basis')]==9)
			if($booking_without_order==1 )
			{
				echo "$('#txt_production_qty').removeAttr('readonly','readonly');\n";
				echo "$('#txt_production_qty').removeAttr('onClick','onClick');\n";
				echo "$('#txt_production_qty').removeAttr('placeholder','placeholder');\n";
			}
			else
			{
				echo "$('#txt_production_qty').attr('readonly','readonly');\n";
				echo "$('#txt_production_qty').attr('onClick','openmypage_po();');\n";
				echo "$('#txt_production_qty').attr('placeholder','Single Click');\n";
			}
		}

		$comp='';
		if($row[csf('fabric_description_id')]==0 || $row[csf('fabric_description_id')]=="")
		{
			$comp=return_field_value("item_description","product_details_master","id=".$row[csf('prod_id')]);
		}
		else
		{
			$determination_sql=sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=".$row[csf('fabric_description_id')]." and b.status_active=1 and b.is_deleted=0 order by b.id asc");

			if($determination_sql[0][csf('construction')]!="")
			{
				$comp=$determination_sql[0][csf('construction')].", ";
			}

			foreach( $determination_sql as $d_row )
			{
				$comp.=$composition[$d_row[csf('copmposition_id')]]." ".$d_row[csf('percent')]."% ";
			}
		}

		$order_ids = explode("_",$row[csf("order_id")]);
		$order_id=rtrim($order_ids[0],',');
		echo "document.getElementById('txt_batch_id').value 				= '".$row[csf("batch_id")]."';\n";
		echo "document.getElementById('txt_batch_no').value 				= '".$batch_arr[$row[csf("batch_id")]]."';\n";
		if($row[csf('receive_basis')]==16)
		{
			echo "$('#txt_batch_no').attr('disabled','disabled');\n";
			echo "$('#cbo_dia_width_type').attr('disabled','disabled');\n";
			echo "$('#cbo_fabric_type').attr('disabled','disabled');\n";
		}

		$body_part_ids="";
		if($row[csf('receive_basis')]==1 && $is_sample!=1)
		{
			$ex_job=explode(",",$job_nos);
			foreach($ex_job as $jobNo)
			{
				//echo $jobNo.'-'.$row[csf('fabric_description_id')];
				if($body_part_ids=="") $body_part_ids=$body_part_id_Arr[$jobNo][$row[csf('fabric_description_id')]]; else $body_part_ids.=','.$body_part_id_Arr[$jobNo][$row[csf('fabric_description_id')]];
			}
		}
		else
		{
			$body_part_ids=$body_part_id_Arr[$row[csf("booking_no")]][$row[csf('fabric_description_id')]];
		}

		$body_part_id = implode(",",array_unique(explode(",",chop($body_part_ids,","))));

		if($row[csf('receive_basis')] == 1)
		{
			echo "load_drop_down( 'requires/knit_finish_fabric_receive_by_garments_controller','".$body_part_id."', 'load_drop_down_pre_body_part', 'body_td' );\n";
		}
		echo "document.getElementById('cbo_body_part').value 				= '".$row[csf("body_part_id")]."';\n";
		echo "document.getElementById('txt_fabric_desc').value 				= '".$comp."';\n";
		echo "document.getElementById('fabric_desc_id').value 				= '".$row[csf("fabric_description_id")]."';\n";

		if($order_id!="")
		{
			if($row[csf('receive_basis')] == 6 || $row[csf('receive_basis')] == 4)
			{
				$order_id1 = $order_id."_".$row[csf("booking_no")]."_".$row[csf("booking_without_order")]."_".$row[csf("color_id")];
				echo "load_drop_down( 'requires/knit_finish_fabric_receive_by_garments_controller','".$order_id1."', 'load_drop_down_color', 'color_td' );\n";
			}
			else
			{
				echo "get_php_form_data('".$order_id."', 'load_color', 'requires/knit_finish_fabric_receive_by_garments_controller' );\n";
			}
		}
		else if($row[csf("booking_without_order")] ==1 && ($row[csf('receive_basis')] == 6 || $row[csf('receive_basis')] == 4))
		{
			$order_id1 = $order_id."_".$row[csf("non_order_booking")]."_".$row[csf("booking_without_order")]."_".$row[csf("color_id")];
			echo "load_drop_down( 'requires/knit_finish_fabric_receive_by_garments_controller','".$order_id1."', 'load_drop_down_color', 'color_td' );\n";
		}

		echo "document.getElementById('txt_gsm').value 						= '".$row[csf("gsm")]."';\n";
		echo "document.getElementById('txt_dia_width').value 				= '".$row[csf("width")]."';\n";
		echo "document.getElementById('txt_original_dia_width').value 		= '".$row[csf("original_width")]."';\n";
		echo "document.getElementById('txt_dia_width_show').value 			= '".$row[csf("original_width")]."';\n";
		if($row[csf('receive_basis')] != 6 && $row[csf('receive_basis')] != 4)
		{
			echo "document.getElementById('txt_color').value 					= '".$color_arr[$row[csf("color_id")]]."';\n";
			echo "$('#txt_color').attr('disabled','disabled');\n";
			echo "document.getElementById('txt_color_id').value 				= '".$row[csf("color_id")]."';\n";

			echo "$('#txt_gsm').attr('disabled','disabled');\n";
			echo "$('#txt_dia_width').attr('disabled','disabled');\n";
		}

		echo "$('#txt_dia_width_show').attr('disabled','disabled');\n";

		echo "document.getElementById('cbo_fabric_type').value 				= '".$row[csf("fabric_shade")]."';\n";

		echo "document.getElementById('txt_fabric_shade').value 			= '".$row[csf("fabric_shade")]."';\n";

		echo "document.getElementById('txt_production_qty').value 			= '".$row[csf("receive_qnty")]."';\n";
		echo "document.getElementById('txt_reject_qty').value 				= '".$row[csf("reject_qty")]."';\n";
		echo "document.getElementById('txt_no_of_roll').value 				= '".$row[csf("no_of_roll")]."';\n";
		echo "document.getElementById('buyer_name').value 					= '".$buyer_name."';\n";
		echo "document.getElementById('buyer_id').value 					= '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('cbo_machine_name').value 			= '".$row[csf("machine_no_id")]."';\n";
		echo "document.getElementById('cbo_location').value 				= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('txt_po_booking_no').value 			= '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('hdn_booking_id').value 				= '".$row[csf("booking_no_id")]."';\n";
		echo "document.getElementById('pi_dtls_id').value 					= '".$row[csf("pi_wo_dtls_id")]."';\n";
		echo "document.getElementById('is_posted_account').value 			= '".$row[csf("is_posted_account")]."';\n";
		//echo "$('#cbo_fabric_type').attr('disabled','true')".";\n";

		echo "load_room_rack_self_bin('requires/knit_finish_fabric_receive_by_garments_controller*2', 'store','store_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."',this.value);\n";

		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("store_id")]."';\n";
		echo "load_room_rack_self_bin('requires/knit_finish_fabric_receive_by_garments_controller', 'floor','floor_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$row[csf('store_id')]."',this.value);\n";
		echo "document.getElementById('cbo_floor').value 					= '".$row[csf("floor")]."';\n";
		echo "load_room_rack_self_bin('requires/knit_finish_fabric_receive_by_garments_controller', 'room','room_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$row[csf('store_id')]."','".$row[csf('floor')]."',this.value);\n";
		echo "document.getElementById('cbo_room').value 					= '".$row[csf("room")]."';\n";
		echo "load_room_rack_self_bin('requires/knit_finish_fabric_receive_by_garments_controller', 'rack','rack_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$row[csf('store_id')]."','".$row[csf('floor')]."','".$row[csf('room')]."',this.value);\n";
		echo "document.getElementById('txt_rack').value 					= '".$row[csf("rack_no")]."';\n";
		echo "load_room_rack_self_bin('requires/knit_finish_fabric_receive_by_garments_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$row[csf('store_id')]."','".$row[csf('floor')]."','".$row[csf('room')]."','".$row[csf('rack_no')]."',this.value);\n";
		echo "document.getElementById('txt_shelf').value 					= '".$row[csf("shelf_no")]."';\n";
		echo "load_room_rack_self_bin('requires/knit_finish_fabric_receive_by_garments_controller', 'bin','bin_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$row[csf('store_id')]."','".$row[csf('floor')]."','".$row[csf('room')]."','".$row[csf('rack_no')]."','".$row[csf('shelf_no')]."',this.value);\n";
		echo "document.getElementById('cbo_bin').value 						= '".$row[csf("bin")]."';\n";

		echo "document.getElementById('hidden_receive_qnty').value 			= '".$row[csf("receive_qnty")]."';\n";
		echo "document.getElementById('all_po_id').value 					= '".$order_id."';\n";
		echo "document.getElementById('previous_prod_id').value 			= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('product_id').value 					= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('update_trans_id').value 				= '".$row[csf("trans_id")]."';\n";
		echo "document.getElementById('cbo_dia_width_type').value 			= '".$row[csf("dia_width_type")]."';\n";

		echo "document.getElementById('txt_fabric_color_id').value 			= '".$row[csf("color_id")]."';\n";
		echo "document.getElementById('txt_pre_cost_fab_conv_cost_dtls_id').value = '".$row[csf("pre_cost_fabric_cost_dtls_id")]."';\n";

		echo "document.getElementById('hdn_is_sales').value 			= '".$row[csf("is_sales")]."';\n";

		if($process_costing_maintain==1 && $var_finish_rate_come_hdn==2 && $var_allow_fin_rcv_NR_hdn==1)
		{
			echo "document.getElementById('txt_rate').value 				    = '".number_format($row[csf("rate_charge_nr")],4,".","")."';\n";
			echo "document.getElementById('txt_hidden_rate').value 				= '".$row[csf("rate_charge_nr")]."';\n";
			echo "document.getElementById('txt_hidden_yarn_issue_rate').value 	= '".$row[csf("yarn_issue_rate")]."';\n";
			echo "document.getElementById('txt_hidden_knitting_rate').value 	= '".$row[csf("knitting_rate")]."';\n";
			echo "document.getElementById('txt_hidden_dyeing_rate').value 		= '".$row[csf("fabric_dyeing_rate")]."';\n";
			echo "document.getElementById('txt_hidden_aop_rate').value 			= '".$row[csf("aop_rate")]."';\n";
			echo "document.getElementById('hdn_rate_string').value 				= '".$row[csf("rate_string")]."';\n";
		}
		else
		{
			echo "document.getElementById('txt_rate').value 				    = '".number_format($row[csf("rate")],4,".","")."';\n";
			echo "document.getElementById('txt_hidden_rate').value 				= '".$row[csf("rate")]."';\n";
		}

		echo "document.getElementById('txt_amount').value 			        = '".number_format($row[csf("amount")],2,".","")."';\n";
		echo "document.getElementById('txt_hidden_amount').value 			= '".$row[csf("amount")]."';\n";
		echo "document.getElementById('hidden_receive_amnt').value 			= '".number_format($row[csf("amount")],2,".","")."';\n";
		echo "document.getElementById('fin_prod_dtls_id').value 			= '".$row[csf("fin_prod_dtls_id")]."';\n";
		echo "document.getElementById('cbouom').value 						= '".$row[csf("uom")]."';\n";
		if($row[csf('receive_basis')] == 6 || $row[csf('receive_basis')] == 4 || $row[csf('receive_basis')] == 16)
		{
			echo "$('#cbouom').attr('disabled','disabled');\n";
		}
		echo "document.getElementById('txt_qc_name').value 					= '".$row[csf("qc_name")]."';\n";
		echo "document.getElementById('txt_hidden_qc_name').value 			= '".$row[csf("emp_id")]."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";

		if($process_costing_maintain==1)
		{
			$dyeing_charge=$trans_arr[$row[csf("trans_id")]]["dye_charge"];

			$material_data=sql_select("select * from pro_material_used_dtls where dtls_id='$id' and entry_form=37 ");
			foreach($material_data as $value)
			{
				$grey_used=$value[csf('used_qty')];
				$material_update_id=$value[csf('id')];
				$grey_rate=$value[csf('rate')];
				$grey_amount=$value[csf('amount')];
				$grey_grey_product_id=$value[csf('prod_id')];
				$process_string="$grey_grey_product_id*$grey_used*$grey_rate*$material_update_id";
				echo "document.getElementById('process_string').value 	= '".$process_string."';\n";
			}
			if($row[csf('receive_basis')]==11)
			{
				$total_rate=$trans_arr[$row[csf("trans_id")]]["grey_fabric_rate"];
			}
			else
			{
				$total_rate=$row[csf("rate")]-$dyeing_charge;
			}

			$total_amount=($row[csf("rate")])*$row[csf("receive_qnty")];
			$knitting_charge_string="$dyeing_charge*$total_rate";
			$save_rate_string="$grey_rate*$dyeing_charge";
			echo "document.getElementById('knitting_charge_string').value 	= '".$knitting_charge_string."';\n";
			echo "document.getElementById('save_rate_string').value 	= '".$save_rate_string."';\n";
			echo "document.getElementById('txt_used_qty').value 	    = '".number_format($grey_used,2,".","")."';\n";
			echo "document.getElementById('hidden_dying_charge').value 	= '".number_format($dyeing_charge,2,".","")."';\n";

			if($row[csf('receive_basis')]==11)
			{
				if($row[csf("booking_without_order")] ==1 )
				{
					$dyeing_charge=sql_select("select  sum(b.wo_qty) as qnty, sum(b.wo_qty*b.rate) as amount
					from wo_non_ord_knitdye_booking_mst a, wo_non_ord_knitdye_booking_dtl b, wo_non_ord_samp_booking_dtls c
					where a.id=b.mst_id and b.fab_des_id=c.id and b.fabric_source=1 and b.process_id not in(1) and a.status_active=1
					and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.wo_qty>0
					and a.booking_no = '".$row[csf("non_order_booking")]."'
					and c.lib_yarn_count_deter_id='".$row[csf("fabric_description_id")]."'
					and c.body_part ='".$row[csf("body_part_id")]."'
					and c.fabric_color='".$row[csf("color_id")]."' and b.uom='".$row[csf("uom")]."' and c.gsm_weight='".$row[csf("gsm")]."'
					group by c.lib_yarn_count_deter_id, c.body_part, c.gsm_weight, c.dia_width, c.fabric_color, b.uom");


					$hidden_dying_charge_2nd = $dyeing_charge[0][csf("amount")]/ $dyeing_charge[0][csf("qnty")];
					echo "document.getElementById('hidden_dying_charge_2nd').value 	= '".$hidden_dying_charge_2nd."';\n";
				}
				else
				{
					$dyeing_charge=sql_select("select b.lib_yarn_count_deter_id as determination_id, b.body_part_id, b.gsm_weight, a.pre_cost_fabric_cost_dtls_id as pre_cost_fab_conv_cost_dtls_id, a.dia_width, a.fabric_color_id as color_id, sum(a.wo_qnty) as qnty, avg(a.rate) as rate , b.uom
					from wo_booking_dtls a, wo_pre_cost_fab_conv_cost_dtls c, wo_pre_cost_fabric_cost_dtls b
					where a.pre_cost_fabric_cost_dtls_id=c.id and c.fabric_description=b.id and a.booking_no='".$row[csf("non_order_booking")]."' and b.lib_yarn_count_deter_id='".$row[csf("fabric_description_id")]."' and  b.body_part_id ='".$row[csf("body_part_id")]."' and a.fabric_color_id='".$row[csf("color_id")]."' and b.uom='".$row[csf("uom")]."' and a.pre_cost_fabric_cost_dtls_id='".$row[csf("pre_cost_fabric_cost_dtls_id")]."' and b.gsm_weight='".$row[csf("gsm")]."' and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 group by b.lib_yarn_count_deter_id, b.body_part_id, b.gsm_weight,a.pre_cost_fabric_cost_dtls_id,a.dia_width, a.fabric_color_id, b.uom");
					echo "document.getElementById('hidden_dying_charge_2nd').value 	= '".$dyeing_charge[0][csf("rate")]."';\n";
				}
			}
		}
		echo "document.getElementById('txt_job_no').value 				= '".$job_nos."';\n";
		if(trim($row[csf('grey_used_qty')]))
		{
			echo "document.getElementById('txt_used_qty').value 	    = '".number_format($row[csf('grey_used_qty')],2,".","")."';\n";
		}

		$save_string='';
		if($roll_maintained==1)
		{
			$data_roll_array=sql_select("select id, po_breakdown_id, qnty, roll_no, barcode_no, reject_qnty, fab_meter_qnty, grey_used_qty from pro_roll_details where dtls_id='$id' and entry_form=37 and status_active=1 and is_deleted=0 order by id");
			foreach($data_roll_array as $row_roll)
			{
				if($save_string=="")
				{
					$save_string=$row_roll[csf("po_breakdown_id")]."**".$row_roll[csf("qnty")]."**".$row_roll[csf("roll_no")]."**".$row_roll[csf("id")]."**".$row_roll[csf("barcode_no")]."**".$row_roll[csf("reject_qnty")]."**".""."**".$row_roll[csf("fab_meter_qnty")]."**".$row_roll[csf("grey_used_qty")];
				}
				else
				{
					$save_string.=",".$row_roll[csf("po_breakdown_id")]."**".$row_roll[csf("qnty")]."**".$row_roll[csf("roll_no")]."**".$row_roll[csf("id")]."**".$row_roll[csf("barcode_no")]."**".$row_roll[csf("reject_qnty")]."**".""."**".$row_roll[csf("fab_meter_qnty")]."**".$row_roll[csf("grey_used_qty")];
				}
			}
		}
		else
		{
			$dataPoArray=sql_select("select po_breakdown_id,quantity,returnable_qnty, fab_meter_qnty, grey_used_qty from order_wise_pro_details where dtls_id='$id' and entry_form=37 and status_active=1 and is_deleted=0");
			foreach($dataPoArray as $row_po)
			{
				if($row[csf('receive_basis')]==16)
				{
					if($save_string=="")
					{
						$save_string=$row_po[csf("po_breakdown_id")]."_".$row_po[csf("quantity")];
					}
					else
					{
						$save_string.=",".$row_po[csf("po_breakdown_id")]."_".$row_po[csf("quantity")];
					}
				}
				else
				{
					if($save_string=="")
					{
						$save_string=$row_po[csf("po_breakdown_id")]."**".$row_po[csf("quantity")]."********".$row_po[csf("returnable_qnty")]."**".""."**".$row_po[csf("fab_meter_qnty")]."**".$row_po[csf("grey_used_qty")];
					}
					else
					{
						$save_string.=",".$row_po[csf("po_breakdown_id")]."**".$row_po[csf("quantity")]."********".$row_po[csf("returnable_qnty")]."**".""."**".$row_po[csf("fab_meter_qnty")]."**".$row_po[csf("grey_used_qty")];
					}
				}
			}
		}
		echo "store_update_upto_disable()\n";
		echo "document.getElementById('hidden_order_id').value 			= '".$order_id."';\n";
		echo "document.getElementById('txt_order_no').value 			= '".$order_nos."';\n";
		echo "document.getElementById('save_data').value 				= '".$save_string."';\n";
		echo "document.getElementById('txt_payment_overrcv_qty').value 	= '".$row[csf("payment_over_rcv_status")]."';\n";
		echo "document.getElementById('hdn_saved_payment_overrcv_qty').value 	= '".$row[csf("hdn_saved_payment_overrcv_qty")]."';\n";
		echo "$('#txt_payment_overrcv_qty').attr('disabled','disabled');\n";

		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_finish_receive_entry',1,1);\n";
		exit();
	}
}

if($action=="roll_maintained")
{
	$roll_maintained=0; $process_cost_maintain=0;$finish_rate_come_hdn=0;
	$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name='$data' and item_category_id=2 and variable_list=3 and is_deleted=0 and status_active=1 and page_upto_id=2");
	if($roll_maintained==1) $roll_maintained=$roll_maintained; else $roll_maintained=0;

	$varialbe_production_sql = sql_select("select variable_list, distribute_qnty, process_costing_maintain from variable_settings_production where company_name ='$data' and variable_list in(34,57) and is_deleted=0 and status_active=1");

	$varialbe_production_allow_fin_fab_NR=return_field_value("allow_fin_fab_rcv","variable_settings_production","company_name='$data' and variable_list=75 and is_deleted=0 and status_active=1");


	foreach ($varialbe_production_sql as $row)
	{
		if ($row[csf('variable_list')] == 34)
		{
			$process_cost_maintain = $row[csf('process_costing_maintain')];
		}
		if ($row[csf('variable_list')] == 57)
		{
			$finish_rate_come_hdn = $row[csf('distribute_qnty')];
		}
	}


	if($process_cost_maintain==1) $process_cost_maintain=$process_cost_maintain; else $process_cost_maintain=0;

	echo "document.getElementById('roll_maintained').value 				= '".$roll_maintained."';\n";
	echo "document.getElementById('process_costing_maintain').value 	= '".$process_cost_maintain."';\n";

	if($process_cost_maintain==1){
		echo "$('#txt_used_qty').attr('readonly','readonly');\n";
	}
	else{
		echo "$('#txt_used_qty').removeAttr('readonly','readonly');\n";
	}

	echo "reset_form('finishFabricEntry_1','list_fabric_desc_container','','','set_receive_basis();','cbo_company_id*cbo_receive_basis*txt_production_date*txt_challan_no*roll_maintained*process_costing_maintain*cbouom*txt_receive_date');\n";

	if($process_cost_maintain==1)
	{
		echo "document.getElementById('var_finish_rate_come_hdn').value='" . $finish_rate_come_hdn . "';\n";
		echo "document.getElementById('var_allow_fin_rcv_NR_hdn').value='" . $varialbe_production_allow_fin_fab_NR . "';\n";
	}
	exit();
}

if($action=="load_color")
{
	$sql = "select b.color_name from wo_booking_dtls a, lib_color b where a.fabric_color_id = b.id
	and a.po_break_down_id in (".trim($data).") and b.status_active=1 group by b.color_name order by b.color_name";
	//echo $sql;die;

	echo "var str_color = [". substr(return_library_autocomplete( $sql, "color_name" ), 0, -1). "];\n";
	echo "$('#txt_color').autocomplete({
		source: str_color
	});\n";
	exit();
}

if($action=="load_color_service_booking")
{
	$sql="select c.color_name from wo_booking_mst a, wo_booking_dtls b, lib_color c where a.booking_no=b.booking_no and b.fabric_color_id=c.id and b.po_break_down_id in($data) and a.item_category=12 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 group by b.fabric_color_id, c.color_name";
	//echo $sql;die;
	echo "var str_color = [". substr(return_library_autocomplete( $sql, "color_name" ), 0, -1). "];\n";
	echo "$('#txt_color').autocomplete({
		source: str_color
	});\n";
	exit();
}

if($action=="roll_duplication_check")
{
	$data=explode("**",$data);
	$po_id=$data[0];
	$roll_no=trim($data[1]);
	$roll_id=$data[2];

	if($roll_id=="" || $roll_id=="0")
	{
		$sql="select a.recv_number from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and b.po_breakdown_id='$po_id' and b.roll_no='$roll_no' and a.is_deleted=0 and a.status_active=1 and b.entry_form in(7,37) and b.is_deleted=0 and b.status_active=1";
	}
	else
	{
		$sql="select a.recv_number from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and b.po_breakdown_id='$po_id' and b.roll_no='$roll_no' and a.is_deleted=0 and a.status_active=1 and b.entry_form in(7,37) and b.id<>$roll_id and b.is_deleted=0 and b.status_active=1";
	}
	//echo $sql;
	$data_array=sql_select($sql,1);
	if(count($data_array)>0)
	{
		echo "1"."_".$data_array[0][csf('recv_number')];
	}
	else
	{
		echo "0_";
	}

	exit();
}

if ($action=="finish_fabric_receive_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);

	$sql="SELECT id, recv_number, company_id, receive_basis, booking_id, booking_no, booking_without_order, store_id, location_id, knitting_source, knitting_company, receive_date, challan_no, store_id, lc_no, currency_id, exchange_rate, source,challan_date,gate_entry_no,gate_entry_date 
	from inv_receive_master where id='$data[1]' and company_id='$data[0]' and item_category=2 and entry_form=37";

	$dataArray=sql_select($sql);
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name");
	$storeArr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");
	$location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name");
	$machineArr=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

	$company_arr = return_library_array("select id, company_name from lib_company","id","company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$user_id 		= return_library_array("select id,user_name from user_passwd", "id", "user_name");
	$user_name 		= return_library_array("select id,user_full_name from user_passwd", "id", "user_full_name");

	if($db_type==0)
	{
		$sql_dtls = "SELECT a.id, a.batch_id, a.prod_id, a.receive_qnty, a.grey_used_qty, a.reject_qty, a.no_of_roll, a.machine_no_id, a.rack_no, a.shelf_no,  a.buyer_id,a.remarks, group_concat(b.po_breakdown_id) as order_id, a.uom
		from pro_finish_fabric_rcv_dtls a left join order_wise_pro_details b on b.dtls_id = a.id and b.entry_form=37 and quantity >0 and b.status_active=1
		where a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0
		group by a.id, a.batch_id, a.prod_id, a.receive_qnty, a.reject_qty, a.grey_used_qty, a.no_of_roll, a.machine_no_id, a.rack_no, a.shelf_no, a.buyer_id,a.remarks, a.uom";
	}
	else
	{
		$sql_dtls = "SELECT a.id, a.batch_id, a.prod_id, a.receive_qnty, a.grey_used_qty, a.reject_qty, a.no_of_roll, a.machine_no_id, a.rack_no, a.shelf_no,  a.buyer_id,a.remarks, listagg(b.po_breakdown_id,',') within group (order by b.po_breakdown_id) as order_id, a.uom
		from pro_finish_fabric_rcv_dtls a left join order_wise_pro_details b on b.dtls_id = a.id and b.entry_form = 37 and quantity >0 and b.status_active=1
		where a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted= 0
		group by a.id, a.batch_id, a.prod_id, a.receive_qnty, a.grey_used_qty, a.reject_qty, a.no_of_roll, a.machine_no_id, a.rack_no, a.shelf_no, a.buyer_id,a.remarks, a.uom";
	}
	//echo $sql_dtls;
	$sql_result= sql_select($sql_dtls);

	foreach ($sql_result as $val)
	{
		$all_po_arr[$val[csf("order_id")]] = $val[csf("order_id")];
		$all_batch_ids .= $val[csf("batch_id")].",";
		$all_prod_ids .= $val[csf("prod_id")].",";
	}
	$all_po_ids = implode(",", array_filter($all_po_arr));

	if(!empty($all_po_ids))
	{
		$po_array=array();
		$po_sql = sql_select("select id, po_number, file_no, grouping as ref from wo_po_break_down where  status_active=1 and is_deleted=0 and id in ($all_po_ids)");
		foreach($po_sql as $row)
		{
			$po_array[$row[csf("id")]]['po']=$row[csf("po_number")];
			$po_array[$row[csf("id")]]['file']=$row[csf("file_no")];
			$po_array[$row[csf("id")]]['ref']=$row[csf("ref")];
		}
	}

	$all_batch_ids = chop($all_batch_ids,",");
	if(!empty($all_batch_ids))
	{
		$batch_sql = sql_select("select id, batch_no, color_id from pro_batch_create_mst where id in ($all_batch_ids)");
		foreach ($batch_sql as $val)
		{
			$batch_arr[$val[csf("id")]] = $val[csf("batch_no")];
			$batch_color[$val[csf("id")]] = $val[csf("color_id")];
		}
		$color_ids = implode(",", $batch_color);
		if(!empty($color_ids))
		{
			$color_arr=return_library_array( "select id, color_name from lib_color where id in ($color_ids)",'id','color_name');
		}
	}

	$all_prod_ids = chop($all_prod_ids,",");
	if(!empty($all_prod_ids))
	{
		$product_array=array();
		$product_sql = sql_select("select id, product_name_details, gsm, dia_width from product_details_master where item_category_id=2 and status_active=1 and is_deleted=0 and id in ($all_prod_ids)");
		foreach($product_sql as $row)
		{
			$product_array[$row[csf("id")]]['product_name_details']=$row[csf("product_name_details")];
			$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
			$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
		}
	}

	$lib_room_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "room_id","floor_room_rack_name" );
	$lib_rack_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "rack_id","floor_room_rack_name" );
	$lib_shelf_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "shelf_id","floor_room_rack_name" );
	$lib_bin_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "bin_id","floor_room_rack_name" );
	?>
	<div style="width:1200px;">
		<table width="1170" cellspacing="0" align="right">
			<tr>
				<td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_arr[$data[0]]; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">
					<?
					$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
					foreach ($nameArray as $result)
					{
						?>
						Plot No: <? echo $result['plot_no']; ?>
						Level No: <? echo $result['level_no']?>
						Road No: <? echo $result['road_no']; ?>
						Block No: <? echo $result['block_no'];?>
						City No: <? echo $result['city'];?>
						Zip Code: <? echo $result['zip_code']; ?>
						Province No: <?php echo $result['province'];?>
						Country: <? echo $country_arr[$result['country_id']]; ?><br>
						Email Address: <? echo $result['email'];?>
						Website No: <? echo $result['website'];
					}
					?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><u><? echo $data[2]; ?> Challan</u></strong></td>
			</tr>
			<tr>
				<td width="120"><strong>Receive ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
				<td width="130"><strong>Receive Basis:</strong></td> <td width="175px"><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
				<td width="125"><strong>Receive Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Challan No:</strong></td><td><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>WO/PI/Production:</strong></td><td><? echo $dataArray[0][csf('booking_no')]; ?></td>
				<td><strong>Store Name:</strong></td><td><? echo $storeArr[$dataArray[0][csf('store_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Dyeing Source:</strong></td><td><? echo $knitting_source[$dataArray[0][csf('knitting_source')]]; ?></td>
				<td><strong>Dyeing Company:</strong></td><td><p><? if ($dataArray[0][csf('knitting_source')]==1) echo $company_arr[$dataArray[0][csf('knitting_company')]]; else if ($dataArray[0][csf('knitting_source')]==3) echo $supplier_arr[$dataArray[0][csf('knitting_company')]]; ?></p></td>
				<td><strong>Location:</strong></td><td><? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>L/C No:</strong></td><td><? echo $lc_arr[$dataArray[0][csf('lc_no')]]; ?></td>
				<td><strong>Exchange Rate:</strong></td><td><? echo $dataArray[0][csf('exchange_rate')]; ?></td>
				<td><strong>Challan Date:</strong></td><td><? echo change_date_format($dataArray[0][csf('challan_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Gate Entry No:</strong></td><td><? echo $dataArray[0][csf('gate_entry_no')]; ?></td>
				<td><strong>Gate Entry Date:</strong></td><td><? echo  change_date_format($dataArray[0][csf('gate_entry_date')]); ?></td>
			</tr>
			<tr style=" height:20px">
				<td  colspan="3" id="barcode_img_id"></td>
			</tr>
			<tr style=" height:20px">
				<td colspan="6">&nbsp;</td>
			</tr>
		</table>
		<div style="width:100%; margin-top:20px;">
			<table align="right" cellspacing="0" cellpadding="0" width="1170"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="60">Batch No</th>
					<th width="70">Color</th>
					<th width="100">Order No</th>
					<th width="70">File No</th>
					<th width="70">Ref. No</th>
					<th width="150">Fabric Des.</th>
					<th width="70" >Buyer Name</th>
					<th width="50">GSM</th>
					<th width="60">Dia/Width</th>
					<th width="60">UOM</th>
					<th width="80">QC Pass Qty</th>
					<th width="80">Grey Used Qty</th>
					<th width="60">Reject Qty</th>
					<th width="60">Roll</th>
					<th width="50">Rack</th>
					<th width="50">Shelf</th>
					<th width="100">Machine</th>
					<th>Remarks</th>
				</thead>
				<tbody>
					<?
					$i=1;$totalGreyUsedQty=0;
					foreach($sql_result as $row)
					{
						if ($i%2==0)$bgcolor="#E9F3FF";	 else $bgcolor="#FFFFFF";

						$po_no=array_unique(explode(",",$row[csf("order_id")]));
						$order_nos="";	$file_nos="";	$ref_nos="";
						foreach($po_no as $val)
						{
							if ($order_nos=="") $order_nos=$po_array[$val]['po']; else $order_nos.=", ".$po_array[$val]['po'];
							if ($file_nos=="") $file_nos=$po_array[$val]['file']; else $file_nos.=", ".$po_array[$val]['file'];
							if ($ref_nos=="") $ref_nos=$po_array[$val]['ref']; else $ref_nos.=", ".$po_array[$val]['ref'];
						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">

							<td align="center"><? echo $i; ?></td>
							<td><? echo $batch_arr[$row[csf("batch_id")]]; ?></td>
							<td><? echo $color_arr[$batch_color[$row[csf("batch_id")]]]; ?></td>
							<td><div style="width:120px; word-wrap:break-word"><? echo $order_nos; ?></div></td>
							<td><div style="width:70px; word-wrap:break-word"><? echo $file_nos; ?></div></td>
							<td><div style="width:70px; word-wrap:break-word"><? echo $ref_nos; ?></div></td>
							<td><div style="width:150px; word-wrap:break-word"><? echo $product_array[$row[csf("prod_id")]]['product_name_details']; ?></div></td>
							<td><? echo $buyer_library[$row[csf("buyer_id")]]; ?></td>
							<td align="center"><? echo $product_array[$row[csf("prod_id")]]['gsm']; ?></td>
							<td align="center"><? echo $product_array[$row[csf("prod_id")]]['dia_width']; ?></td>
							<td align="right"><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
							<td align="right"><? echo number_format($row[csf("receive_qnty")],2); ?></td>
							<td align="right"><? echo number_format($row[csf("grey_used_qty")],2); ?></td>
							<td align="right"><? echo number_format($row[csf("reject_qty")],2); ?></td>
							<td align="right"><? echo number_format($row[csf("no_of_roll")]); ?></td>
							<td><? echo $lib_rack_arr[$row[csf("rack_no")]]; ?></td>
							<td><? echo $lib_shelf_arr[$row[csf("shelf_no")]]; ?></td>
							<td><? echo $machineArr[$row[csf("machine_no_id")]]; ?></td>
							<td><p><? echo $row[csf("remarks")]; ?></p></td>
						</tr>
						<? $i++;
						$totalRecQnty +=$row[csf("receive_qnty")];
						$totalGreyUsedQty +=$row[csf("grey_used_qty")];
						$totalRejQnty +=$row[csf("reject_qty")];
						$totalRoll +=$row[csf("no_of_roll")];
					} ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="11" align="right"><strong>Total :</strong></td>
						<td align="right"><?php echo number_format($totalRecQnty,2); ?></td>
						<td align="right"><?php echo number_format($totalGreyUsedQty,2); ?></td>
						<td align="right"><?php echo number_format($totalRejQnty,2); ?></td>
						<td align="right"><?php echo number_format($totalRoll,2); ?></td>
						<td align="right" colspan="4"><?php // echo $totalAmount; ?></td>
					</tr>
				</tfoot>
			</table>
			<br>
			<?
			echo signature_table(149, $data[0], "1110px");
			?>
		</div>
	</div>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
			// alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();

			var settings = {
				output:renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize:5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {code:value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $data[3]; ?>');
	</script>
	<?
	exit();
}

if ($action=="finish_fabric_receive_print_2")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);

	$sql="select id, recv_number, company_id, receive_basis, booking_id, booking_no, booking_without_order, store_id, location_id, knitting_source, knitting_company, receive_date, challan_no, store_id from inv_receive_master where id='$data[1]' and company_id='$data[0]' and item_category=2 and entry_form=37";

	//echo $sql;die;
	$dataArray=sql_select($sql);
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name");
	$storeArr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");
	$location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name");
	$batch_color=return_library_array( "select id, color_id from pro_batch_create_mst", "id", "color_id");
	$machineArr=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no");

	$company_arr = return_library_array("select id, company_name from lib_company","id","company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst","id","batch_no");
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');

	$product_array=array();
	$product_sql = sql_select("select id, product_name_details, gsm, dia_width from product_details_master where item_category_id=2 and status_active=1 and is_deleted=0");
	foreach($product_sql as $row)
	{
		$product_array[$row[csf("id")]]['product_name_details']=$row[csf("product_name_details")];
		$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
	}
	$po_array=array();
	//$po_sql = sql_select("select id, po_number, file_no, grouping as ref from wo_po_break_down where  status_active=1 and is_deleted=0"); // old
	$po_sql = sql_select("select a.id, a.po_number, a.file_no, a.grouping as ref,b.job_no,b.style_ref_no from wo_po_break_down a,wo_po_details_master b where  a.status_active=1 and a.is_deleted=0 and a.job_id=b.id"); // new

	foreach($po_sql as $row)
	{
		$po_array[$row[csf("id")]]['po']=$row[csf("po_number")];
		$po_array[$row[csf("id")]]['file']=$row[csf("file_no")];
		$po_array[$row[csf("id")]]['ref']=$row[csf("ref")];
		$po_array[$row[csf("id")]]['job']=$row[csf("job_no")];
		$po_array[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
	}

	//$po_array=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");

	$lib_room_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "room_id","floor_room_rack_name" );
	$lib_rack_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "rack_id","floor_room_rack_name" );
	$lib_shelf_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "shelf_id","floor_room_rack_name" );
	$lib_bin_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "bin_id","floor_room_rack_name" );
	?>
	<div style="width:1070px;">
		<table width="1040" cellspacing="0" align="right">
			<tr>
				<td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_arr[$data[0]]; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">
					<?
					$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
					foreach ($nameArray as $result)
					{
						?>
						Plot No: <? echo $result['plot_no']; ?>
						Level No: <? echo $result['level_no']?>
						Road No: <? echo $result['road_no']; ?>
						Block No: <? echo $result['block_no'];?>
						City No: <? echo $result['city'];?>
						Zip Code: <? echo $result['zip_code']; ?>
						Province No: <?php echo $result['province'];?>
						Country: <? echo $country_arr[$result['country_id']]; ?><br>
						Email Address: <? echo $result['email'];?>
						Website No: <? echo $result['website'];
					}
					?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><u><? echo $data[2]; ?> Challan</u></strong></td>
			</tr>
			<tr>
				<td width="120"><strong>Receive ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
				<td width="130"><strong>Receive Basis:</strong></td> <td width="175px"><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
				<td width="125"><strong>Receive Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Challan No:</strong></td><td><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>WO/PI/Production:</strong></td><td><? echo $dataArray[0][csf('booking_no')]; ?></td>
				<td><strong>Store Name:</strong></td><td><? echo $storeArr[$dataArray[0][csf('store_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Dyeing Source:</strong></td><td><? echo $knitting_source[$dataArray[0][csf('knitting_source')]]; ?></td>
				<td><strong>Dyeing Company:</strong></td><td><p><? if ($dataArray[0][csf('knitting_source')]==1) echo $company_arr[$dataArray[0][csf('knitting_company')]]; else if ($dataArray[0][csf('knitting_source')]==3) echo $supplier_arr[$dataArray[0][csf('knitting_company')]]; ?></p></td>
				<td><strong>Location:</strong></td><td><? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
			</tr>
			<tr style=" height:20px">
				<td  colspan="3" id="barcode_img_id"></td>
			</tr>
			<tr style=" height:20px">
				<td colspan="6">&nbsp;</td>
			</tr>
		</table>

		<div style="width:108%; margin-top:20px;">
			<table align="right" cellspacing="0" cellpadding="0" width="1160"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="60">Batch No</th>
					<th width="70">Color</th>
					<th width="75">Job No</th>
					<th width="75">Style No</th>
					<th width="100">Order No</th>
	<!-- <th width="70">File No</th>
		<th width="70">Ref. No</th>-->
		<th width="150">Fabric Des.</th>
		<th width="50">GSM</th>
		<th width="60">Dia/Width</th>
		<th width="80">QC Pass Qty</th>
		<th width="60">Reject Qty</th>
		<th width="60">Roll</th>
		<th width="50">Rack</th>
		<th width="50">Shelf</th>
		<th width="100">Machine</th>
		<th>Remarks</th>
	</thead>
	<tbody>
		<?
		$sql_dtls="select id, batch_id, prod_id, receive_qnty, reject_qty, no_of_roll, machine_no_id, rack_no, shelf_no, order_id, buyer_id from pro_finish_fabric_rcv_dtls where mst_id='$data[1]' and status_active=1 and is_deleted= 0";

		$sql_result= sql_select($sql_dtls);
		$i=1;
		foreach($sql_result as $row)
		{
			if ($i%2==0)$bgcolor="#E9F3FF";	 else $bgcolor="#FFFFFF";

			$po_no=array_unique(explode(",",$row[csf("order_id")]));
			$order_nos="";	$file_nos="";	$ref_nos=""; $job_nos=""; $style_nos="";
			foreach($po_no as $val)
			{
				if ($order_nos=="") $order_nos=$po_array[$val]['po']; else $order_nos.=", ".$po_array[$val]['po'];
				if ($file_nos=="") $file_nos=$po_array[$val]['file']; else $file_nos.=", ".$po_array[$val]['file'];
				if ($ref_nos=="") $ref_nos=$po_array[$val]['ref']; else $ref_nos.=", ".$po_array[$val]['ref'];

				if ($job_nos=="") $job_nos=$po_array[$val]['job']; else $job_nos.=", ".$po_array[$val]['job'];
				if ($style_nos=="") $style_nos=$po_array[$val]['style']; else $style_nos.=", ".$po_array[$val]['style'];
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td align="center"><? echo $i; ?></td>
				<td><? echo $batch_arr[$row[csf("batch_id")]]; ?></td>
				<td><? echo $color_arr[$batch_color[$row[csf("batch_id")]]]; ?></td>

				<td><div style="width:60px; word-wrap:break-word"><? echo $job_nos; ?></div></td>
				<td><div style="width:60px; word-wrap:break-word"><? echo $style_nos; ?></div></td>

				<td><div style="width:120px; word-wrap:break-word"><? echo $order_nos; ?></div></td>
		<!--  <td><div style="width:70px; word-wrap:break-word"><? //echo $file_nos; ?></div></td>
			<td><div style="width:70px; word-wrap:break-word"><? //echo $ref_nos; ?></div></td>-->
			<td><div style="width:150px; word-wrap:break-word"><? echo $product_array[$row[csf("prod_id")]]['product_name_details']; ?></div></td>
			<td align="center"><? echo $product_array[$row[csf("prod_id")]]['gsm']; ?></td>
			<td align="center"><? echo $product_array[$row[csf("prod_id")]]['dia_width']; ?></td>
			<td align="right"><? echo number_format($row[csf("receive_qnty")],2); ?></td>
			<td align="right"><? echo number_format($row[csf("reject_qty")],2); ?></td>
			<td align="right"><? echo number_format($row[csf("no_of_roll")]); ?></td>
			<td><? echo $lib_rack_arr[$row[csf("rack_no")]]; ?></td>
			<td><? echo $lib_shelf_arr[$row[csf("shelf_no")]]; ?></td>
			<td><? echo $machineArr[$row[csf("machine_no_id")]]; ?></td>
			<td><p><? //echo $row[csf("remarks")]; ?></p></td>
		</tr>
		<? $i++;
		$totalRecQnty +=$row[csf("receive_qnty")];
		$totalRejQnty +=$row[csf("reject_qty")];
		$totalRoll +=$row[csf("no_of_roll")];
	} ?>
</tbody>
<tfoot>
	<tr>
		<td colspan="9" align="right"><strong>Total :</strong></td>
		<td align="right"><?php echo number_format($totalRecQnty,2); ?></td>
		<td align="right"><?php echo number_format($totalRejQnty,2); ?></td>
		<td align="right"><?php echo number_format($totalRoll,2); ?></td>
		<td align="right" colspan="4"><?php // echo $totalAmount; ?></td>
	</tr>
</tfoot>
</table>
<br>
<?
echo signature_table(66, $data[0], "1040px");
?>
</div>
</div>
<script type="text/javascript" src="../../../js/jquery.js"></script>
<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
<script>
	function generateBarcode( valuess )
	{
			var value = valuess;//$("#barcodeValue").val();
			// alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();

			var settings = {
				output:renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize:5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {code:value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $data[3]; ?>');
	</script>
	<?
	exit();

}

if($action=="check_update_finishing_bill")
{
	$data=explode("**",$data);
	//print_r($data);die;
	$company_id=$data[0];
	$dyeing_source=$data[1];
	$dyeing_company=$data[2];
	$location_id=$data[3];
	$system_id=$data[4];
	$po_id=$data[5];
	$fabric_desc_id=$data[6];
	$body_part_id=$data[7];
	$product_id=$data[8];
	$color_id=$data[9];
	$batch_id=$data[10];

	$recv_chalan_num=return_field_value("recv_number_prefix_num"," inv_receive_master ","recv_number='$system_id' and company_id='$company_id' and entry_form=37 and status_active=1 and is_deleted=0","recv_number_prefix_num");

	//and a.bill_no='".trim($system_id)."'
	if ($dyeing_company=="") $dyeing_company_cond=""; else $dyeing_company_cond=" and a.party_id='$dyeing_company' ";
	if ($recv_chalan_num=="") $challan_no_cond=""; else $challan_no_cond=" and b.challan_no='$recv_chalan_num' ";
	if ($product_id=="") $product_id_cond=""; else $product_id_cond=" and b.item_id='$product_id' ";
	if ($po_id=="") $po_id_cond=""; else $po_id_cond=" and b.order_id='$po_id' ";
	if ($fabric_desc_id=="") $fabric_desc_id_cond=""; else $fabric_desc_id_cond=" and b.febric_description_id='$fabric_desc_id' ";
	if ($body_part_id=="") $body_part_id_cond=""; else $body_part_id_cond=" and b.body_part_id='$body_part_id' ";
	if ($color_id=="") $color_id_cond=""; else $color_id_cond=" and b.color_id='$color_id' ";
	if ($batch_id=="") $batch_id_cond=""; else $batch_id_cond=" and b.batch_id='$batch_id' ";
	//b.order_id='".$po_id."' and b.febric_description_id='".$fabric_desc_id."' and b.body_part_id='".$body_part_id."' and b.challan_no='$recv_chalan_num' and b.item_id='$product_id' and b.color_id='$color_id' and  a.party_source=1 and a.party_id='$dyeing_company' and

	if($dyeing_source==1)
	{
		$sql="select a.id, a.company_id,a.bill_no from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b  where a.id=b.mst_id  and  a.company_id='$company_id' $dyeing_company_cond  $challan_no_cond $product_id_cond $po_id_cond $fabric_desc_id_cond $body_part_id_cond $color_id_cond $batch_id_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by a.id desc";
	}

	$data_array=sql_select($sql,1);
	if(count($data_array)>0)
	{
		echo "1"."_".$data_array[0][csf('id')]."_".$data_array[0][csf('bill_no')];
	}
	else
	{
		echo "0"."_".$data_array[0][csf('id')]."_".$data_array[0][csf('bill_no')];
	}
	exit();
}

if($action=="issue_challan_no_popup")
{
	echo load_html_head_contents("Issue Challan Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(id)
		{
			$('#issue_challan').val(id);
			parent.emailwindow.hide();
		}
	</script>
	<input type="hidden" name="issue_challan" id="issue_challan" value="" />
	<?
	if($db_type==0)
	{
		$year_cond="year(insert_date)as year";
	}
	else if ($db_type==2)
	{
		$year_cond="TO_CHAR(insert_date,'YYYY') as year";
	}
	$sql="SELECT issue_number_prefix_num, issue_number, $year_cond from inv_issue_master where company_id=$cbo_company_id and entry_form in(16,61) and status_active=1 and is_deleted=0 order by id DESC";

	echo create_list_view("tbl_list_search", "System ID, Challan No,Year", "150,80,70","380","350",0, $sql , "js_set_value", "issue_number", "", 1, "0,0,0", $arr , "issue_number,issue_number_prefix_num,year", "",'setFilterGrid("tbl_list_search",-1);','0,0,0','',0) ;
	exit();
}
if($action=="qc_name_popup")
{

	echo load_html_head_contents("System ID Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(id)
		{

			$('#qc_name').val(id);
			parent.emailwindow.hide();
		}
	</script>
	<input type="hidden" name="qc_name" id="qc_name" value="" />
	<style type="text/css">
	#search_div{  margin-left:200px !important;}

</style>
</head>

<body>
	<div align="center" style="width:800px;">
		<form name="searchsystemidfrm"  id="searchsystemidfrm">
			<fieldset style="width:790px;">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="790" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Id Card No.</th>
						<th>Name</th>
						<th>Designation</th>
						<th>Department</th>
						<th>Status</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
							<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
							<input type="hidden" name="hidden_sys_id" id="hidden_sys_id" class="text_boxes" value="">
						</th>
					</thead>
					<tr class="general">
						<td>
							<input type="text" name="txt_id_card_no" id="txt_id_card_no" class="text_boxes" value="">
						</td>
						<td id="">
							<input type="text" name="txt_emp_name" id="txt_emp_name" class="text_boxes" value="">
						</td>

						<td>
							<?
							echo create_drop_down( "cbo_desination", 162, "select id,custom_designation from lib_designation where status_active=1 and is_deleted=0 order by custom_designation asc","id,custom_designation", 1, "-- Select Designation--", $selected );
							?>
						</td>
						<td>

							<?
							echo create_drop_down( "cbo_dept_name", 165, "select id,department_name from lib_department where status_active=1 and is_deleted=0","id,department_name",1, "-- Select Department --", $selected );
							?>
							<input type="hidden" name="company_id" id="company_id" class="text_boxes" value="<? echo $cbo_company_id;?>">
						</td>
						<td>
							<?
							echo create_drop_down( "cbo_status", 142, $row_status,"", 1, "-- Select status--", 0, "","" );
							?>
						</td>

						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_id_card_no').value+'_'+document.getElementById('txt_emp_name').value+'_'+document.getElementById('cbo_desination').value+'_'+document.getElementById('cbo_dept_name').value+'_'+document.getElementById('cbo_status').value+'_'+'_'+document.getElementById('company_id').value, 'create_list_qc_name', 'search_div', 'knit_finish_fabric_receive_by_garments_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>

				</table>
				<div style="margin-top:5px; margin-left:3px !important;" id="search_div" ></div>
			</fieldset>
		</form>

	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>

	<?

}

if($action=="create_list_qc_name")
{
	$data=explode("_",$data);

	//if($data[0]!="") $empcode="and emp_code='$data[0]'";  else $empcode="";
	if($data[0]!="") $id_card_no=" and id_card_no='$data[0]'";  else $id_card_no="";
	if($data[1]!="") $empname="and first_name like '%$data[1]%'"; else $empname="";
	if($data[2]!=0) $designation_id="and designation_id ='$data[2]'"; else $designation_id="";
	if($data[3]!=0) $department_id="and department_id = '$data[3]'"; else $department_id="";
	if($data[4]!=0) $status_active="and status_active='$data[4]'"; else $status_active="";

	$division_arr=return_library_array( "select id, division_name from lib_division",'id','division_name');
	$department_arr=return_library_array( "select id, department_name from lib_department",'id','department_name');
	$section_arr=return_library_array( "select id, section_name from lib_section",'id','section_name');
	$line_no_arr=return_library_array( "select id, line_name from  lib_sewing_line",'id','line_name');
	$designation_arr=return_library_array( "select id, custom_designation from lib_designation",'id','custom_designation');
	$designation_arr=return_library_array( "select id, custom_designation from lib_designation",'id','custom_designation');

	$arr=array(2=>$designation_arr,3=>$department_arr,4=>$row_status);


	$sql = "select emp_code,id_card_no, first_name as emp_name, designation_id, line_no, company_id, location_id, division_id,department_id,section_id,status_active,id_card_no from lib_employee where company_id=$data[6] $id_card_no $empname $designation_id $department_id $status_active ";

	echo create_list_view("tbl_list_search", "Emp Code,ID Card No.,Employee Name,Designation,Department,Status", "80,80,150,100,100","630","350",0, $sql , "js_set_value", "emp_code,emp_name", "", 1, "0,0,designation_id,department_id,status_active", $arr , "emp_code,id_card_no,emp_name,designation_id,department_id,status_active", "",'setFilterGrid("tbl_list_search",-1);','0,0,0,0','',0) ;
	exit();
}

if($action=="issue_num_check")
{
	//echo $data;die;
	//echo "issue_number_prefix_num as issue_number_prefix_num","inv_issue_master","status_active=1 and is_deleted=0 and entry_form=16 and issue_number_prefix_num=$data";
	$issue_no=return_field_value("issue_number","inv_issue_master","status_active=1 and is_deleted=0 and entry_form in(16,61) and issue_number='$data'","issue_number");
	echo $issue_no;
	exit();
}

if($action=="yarn_lot_popup")
{
	echo load_html_head_contents("Yarn Lot Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$save_data=explode(",",$save_data);
	foreach($save_data as $data_arr)
	{
		$data_arr=explode("**",$data_arr);
		$po_arr[]=$data_arr[0];
	}
	$po_id_all=implode(",",$po_arr);
	if($po_id_all=="") $po_id_all=0;
	$row_cond="";
	$row_limit="";
	if($db_type==0) {$txt_receive_date=change_date_format($txt_receive_date,'yyyy-mm-dd','-'); $row_limit=" limit 1";}
	else { $txt_receive_date=change_date_format($txt_receive_date,'yyyy-mm-dd','-',1); $row_cond=" and rownum=1";}
	?>
	<script>
		function fnc_process_cost()
		{
			var process_string="";
			var knitting_rate_string="";
			var all_deleted_id='';
			var receive_qty=<? echo $txt_receive_qnty; ?>;
			var rate_with_knitting_charge=0;
			var total_amount=0;
			var knitting_charge=$("#txt_knitting_charge").val()*1;
			var total_used_qty=0;

			var swo_qnty=$("#swo_qnty").val()*1;
			var pre_rcv_swo_qnty=$("#pre_rcv_swo_qnty").val()*1;
			var txt_grey_issued=0;

			$("#tbl_lot_list").find('tr').each(function()
			{
				var txt_used=$(this).find('input[name="txt_used_qty[]"]').val()*1;
				txt_grey_issued += $(this).find('input[name="txt_grey_issued[]"]').val()*1;

				if(txt_used>0)
				{
					total_used_qty=total_used_qty+txt_used;
					var txt_prod_id=$(this).find('input[name="txt_prod_id[]"]').val();
					var txt_cons_rate=$(this).find('input[name="txt_cons_rate[]"]').val();
					var txt_net_used=$(this).find('input[name="txt_net_used[]"]').val();
					var txt_material_update_id=$(this).find('input[name="update_material_id[]"]').val();

					if(txt_net_used==0) txt_net_used=txt_used;

					var txt_grey_cost=txt_cons_rate*txt_used;
					total_amount+=txt_grey_cost;
					var grey_rate=txt_grey_cost/txt_net_used;
					process_string=txt_prod_id+"*"+txt_used+"*"+txt_cons_rate+"*"+txt_material_update_id;
					knitting_charge=(knitting_charge*txt_used)/txt_net_used;
				}
				else
				{
					if($(this).find('input[name="update_material_id[]"]').val()>0)
					{
						all_deleted_id=$(this).find('input[name="update_material_id[]"]').val();
					}
				}
			});

			var total_rate=total_amount/receive_qty;
			if( total_used_qty<receive_qty ){
				alert("Total Used Qty Must be Greater or Equal to Receive Qty.");
				return;
			}

			if(txt_grey_issued >0 && swo_qnty>0)
			{
				//Only for service booking without order
				if(txt_grey_issued > swo_qnty)
				{
					if(pre_rcv_swo_qnty+ total_used_qty > swo_qnty){
						alert("Total Used Qty Can not be Greater than total Wo order.\nTotal Wo order: "+swo_qnty+'\nPre received: '+pre_rcv_swo_qnty+'\ncurrent using: '+total_used_qty);
						return;
					}
				}
				else if(txt_grey_issued < swo_qnty)
				{
					if(pre_rcv_swo_qnty+ total_used_qty > txt_grey_issued){
						alert("Total Used Qty Can not be Greater than total grey issue.\nTotal issued grey: "+txt_grey_issued+'\nPre received: '+pre_rcv_swo_qnty+'\ncurrent using: '+total_used_qty);
						return;
					}
				}
			}

			knitting_rate_string=knitting_charge+"*"+total_rate+"*"+all_deleted_id;
			$('#hidden_process_string').val( process_string );
			$('#hidden_knitting_rate').val( knitting_rate_string );
			parent.emailwindow.hide();
		}

		function fnc_ommit_data(id)
		{
			var tr_length=$("#txt_lot_row_id").val();
			for(var j=1;j<tr_length; j++)
			{
				if(j!=id) $("#txt_used_qty_"+j).val('');
			}
		}

	</script>
	<input type="hidden" name="hidden_process_string" id="hidden_process_string" value="" />
	<input type="hidden" name="hidden_knitting_rate" id="hidden_knitting_rate" value="" />
	<div>
		<?php
		$color_id 		= return_field_value("id","lib_color", "color_name='$name_color'");
		$yarn_count_arr = return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
		$brand_arr 		= return_library_array( "select id,brand_name  from  lib_brand",'id','brand_name ');
		if($recieve_basis==9 || $recieve_basis==10)
		{
			// GET PROCESS IDS FROM FINISH FABRIC PRODUCTION
			if($recieve_basis==10){
				$grey_sys_id = sql_select("select LISTAGG(cast(grey_sys_id as varchar2(4000)),',') WITHIN GROUP (ORDER BY grey_sys_id) as grey_sys_id from PRO_GREY_PROD_DELIVERY_DTLS where batch_id=$txt_batch_id");
				$booking_id = $grey_sys_id[0][csf("grey_sys_id")];
			}

			$process_id_result = sql_select("select process_id,booking_id,booking_no from pro_finish_fabric_rcv_dtls where mst_id=$booking_id and fabric_description_id=$fabric_description_id and color_id=$color_id and status_active=1");
			foreach ($process_id_result as $process) {
				$process_ids .= $process[csf("process_id")] . ",";
				$sbooking_id = $process[csf("booking_id")];
			}

			$process_ids = rtrim($process_ids,", ");

			if($sbooking_id!=""){
				// SERVICE BOOKING CHARGE
				$service_booking_charge = sql_select("select a.booking_no,a.material_id, b.gmts_color_id,d.body_part_id, d.lib_yarn_count_deter_id,a.currency_id,a.exchange_rate,sum(b.amount)/sum(b.wo_qnty) as rate from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_fab_conv_cost_dtls c,wo_pre_cost_fabric_cost_dtls d where a.id=$sbooking_id and a.status_active=1 and a.is_deleted=0 and a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and c.fabric_description=d.id and d.lib_yarn_count_deter_id=$fabric_description_id and b.gmts_color_id=$color_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.booking_type=3 group by a.booking_no,a.material_id, b.gmts_color_id,d.body_part_id, d.lib_yarn_count_deter_id,a.currency_id,a.exchange_rate");
				foreach ($service_booking_charge as $charge_value) {
					$service_charge+=$charge_value[csf('rate')]*$charge_value[csf('exchange_rate')];
					$material_id = $charge_value[csf('material_id')];
				}

				// WITH MATERIAL OR WITHOUT MATERIAL
				if($material_id == 1){
					$dyeing_charge = $service_charge;
				}else{
					// GET RATE FROM ISSUE BY BATCH
					$dyes_chemical_issue=sql_select("select sum(b.cons_amount) cons_amount from inv_issue_master a, inv_transaction b where a.id=b.mst_id and b.transaction_type=2 and a.entry_form=5 and b.item_category in (5,6,7,23) and a.batch_no='$txt_batch_id'");
					$dyeing_charge = $service_charge+$process_overhead_rate+ ($dyes_chemical_issue[0][csf("cons_amount")]/$txt_receive_qnty);
				}

			}else{
				// GET PROCESS IDS FROM DATABASE BY ENTRY FORM
				$get_all_inserted_process_ids=sql_select("select process_id from pro_fab_subprocess where entry_form in(30,31,32,33,34,35,47,48) and batch_id=$txt_batch_id and status_active=1 and is_deleted=0");
				foreach ($get_all_inserted_process_ids as $process) {
					$process_ids .= ",".$process[csf("process_id")];
				}
					//30 => 'Slitting/Squeezing', 31 => 'Drying', 32 => 'Heat Setting', 33 => 'Compacting', 34 => 'Special Finish', 35 => 'Dyeing Production'
					//47 => "Singeing", 48 => "Stentering"
				$process_ids = implode(",",array_unique(explode(",",rtrim($process_ids,", "))));

				// GET PROCESS WISE OVERHEAD FROM LIBRARY
				$get_process_overhead_from_library = sql_select("select rate,process_id from lib_finish_process_charge where process_id in($process_ids) and cons_comp_id=$fabric_description_id and status_active=1");
				$process_overhead_rate = 0;
				foreach ($get_process_overhead_from_library as $process_rate) {
					$process_overhead_rate += $process_rate[csf("rate")];
					$process_name_arr[$conversion_cost_head_array[$process_rate[csf("process_id")]]]+=$process_rate[csf("rate")];
				}
				// GET RATE FROM ISSUE BY BATCH
				$dyes_chemical_issue=sql_select("select sum(b.cons_amount) cons_amount from inv_issue_master a, inv_transaction b where a.id=b.mst_id and b.transaction_type=2 and a.entry_form=5 and b.item_category in (5,6,7,23) and a.batch_no='$txt_batch_id'");
				$dyeing_charge = $process_overhead_rate+ ($dyes_chemical_issue[0][csf("cons_amount")]/$txt_receive_qnty);

				foreach ($process_name_arr as $pname => $prate) {
					$process_info_str .= $pname ."=".number_format($prate,2).", ";
				}
				$process_info_str=chop($process_info_str,",");
				if($process_info_str!="")
				{
					if(($dyes_chemical_issue[0][csf("cons_amount")]/$txt_receive_qnty)>0)
					{
						$process_info_str .=", Dyes and Chemical= ".number_format(($dyes_chemical_issue[0][csf("cons_amount")]/$txt_receive_qnty),2);
					}

				}


				$dyeing_charge_info_title =$process_info_str;
			}
		}
		else
		{
			if($recieve_basis==11)
			{
				if($cbo_currency==1)
				{
					$dyeing_charge = $kitting_charge_2nd;
				}
				else
				{
					$dyeing_charge = $kitting_charge_2nd*$txt_exchange_rate;
				}

				$dyeing_charge_info_title="Service Booking Charge = ". $dyeing_charge;
				// need to address disscuss with CTO
			}
			else
			{
				$conversition_cost_sql=sql_select("select b.process, a.currency_id,a.exchange_rate,sum(b.amount)/sum(b.wo_qnty) as rate from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fab_conv_cost_dtls c,wo_pre_cost_fabric_cost_dtls d where a.booking_no=b.booking_no and a.id=".$booking_id." and b.fabric_color_id=".$color_id." and b.pre_cost_fabric_cost_dtls_id=c.id and c.fabric_description=d.id and c.job_no=d.job_no and b.process in (31,25,26,32,33,34,35,36,37,38,39,40,60,61,62,64,67,68,69,70,71,72,73,74,75,77,78,79,80,81,82,83,84,85,86,87,88,89,92,93,94,100,125,127,128,129,132,133,134,135,136,137,138,63,65,66,76,90,91,156) and lib_yarn_count_deter_id=".$fabric_description_id." group by a.currency_id,a.exchange_rate,b.process ");
				$dyeing_charge=0;
				foreach($conversition_cost_sql as $charge_value)
				{
					$process_name_arr[$conversion_cost_head_array[$charge_value[csf("process")]]]+=$charge_value[csf('rate')]*$charge_value[csf('exchange_rate')];
					$dyeing_charge+=$charge_value[csf('rate')]*$charge_value[csf('exchange_rate')];
				}
				foreach ($process_name_arr as $pname => $prate)
				{
					$process_info_str .= $pname ."=".number_format($prate,2).", ";
				}
				$process_info_str=chop($process_info_str,",");
				$dyeing_charge_info_title =$process_info_str;
			}

		}

		$processloss_sql=sql_select("select sum(process_loss) as process_loss from conversion_process_loss where  mst_id=".$fabric_description_id." and process_id in(31,25,26,32,33,34,35,36,37,38,39,40,60,61,62,64,67,68,69,70,71,72,73,74,75,77,78,79,80,81,82,83,84,85,86,87,88,89,92,93,94,100,125, 127,128, 129,132,133,134,135,136,137,138,63,31,30,65,66,76,90,91)");

		$process_loss=$processloss_sql[0][csf('process_loss')];
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id order by b.id asc";
		$data_array=sql_select($sql_deter);
		if(count($data_array)>0)
		{
			foreach( $data_array as $row )
			{
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
			}
		}
		?>
		<table cellspacing="0" cellpadding="0" border="0" rules="all" width="840" class="" align="center">
			<tr>
				<td colspan="5" align="center" style="font-size:16px">
					<strong>Dyeing process loss <?php echo $process_loss." %";?></strong>
				</td>
				<td colspan="5" align="center" style="font-size:16px" title="<? echo $dyeing_charge_info_title; ?>">
					<strong>Dyeing Charge <?php echo number_format($dyeing_charge,2)."Tk./Kg";?></strong>
				</td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="60">Prod Id</th>
				<th width="80">Lot</th>
				<th width="200">Fabric Description</th>
				<th width="80">Brand</th>
				<th width="100">Avg Grey Fabric Rate /Kg (Tk.) </th>
				<th width="70">Net Qty</th>
				<th width="70">Cum. Used Qty</th>
				<th >Used Qty</th>
			</thead>
		</table>
		<div style="width:840px; max-height:280px; overflow-y:scroll">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table" id="tbl_lot_list">
				<?php
				$i=1;
				$sql_cond="";
				if($serch_count_arr>0) $sql_cond=" and c.yarn_count_id in (".implode(",",$serch_count_arr).") ";
				if($serch_composition_arr>0) $sql_cond.=" and c.yarn_comp_type1st in (".implode(",",$serch_composition_arr).") ";
				if($serch_type_arr>0) $sql_cond.=" and c.yarn_type in (".implode(",",$serch_type_arr).") ";

				$grey_fabric_in_roll_lvl =return_field_value("fabric_roll_level","variable_settings_production","company_name='$cbo_company_id' and item_category_id=13 and variable_list=3 and is_deleted=0 and status_active=1");

				if($grey_fabric_in_roll_lvl==1) $grey_fabric_in_roll_lvl=$grey_fabric_in_roll_lvl; else $grey_fabric_in_roll_lvl=0;

				if($recieve_basis==11 && $booking_without_order==1)
				{
					/*
					$sql="select c.detarmination_id ,c.id,c.lot,c.brand, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type,sum(d.qnty) issue_qty,sum(d.amount) cons_amount
					from inv_issue_master a, inv_grey_fabric_issue_dtls b, product_details_master c, pro_roll_details d
					where a.id=b.mst_id and b.prod_id=c.id and b.id=d.dtls_id and a.id=d.mst_id and a.entry_form in(16,61) and b.trans_id>0 and a.item_category=13 and d.entry_form in (16,61) and d.po_breakdown_id in(".$booking_id.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.detarmination_id=".$fabric_description_id."  group by c.id,c.lot,c.brand,c.supplier_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type,c.detarmination_id";*/

					$swo_sql = sql_select("select a.id as swo_id, a.booking_no as swo_no, d.booking_no, d.ID as po_breakdown_id, sum(b.wo_qty) as fabric_qty from wo_non_ord_knitdye_booking_mst a, wo_non_ord_knitdye_booking_dtl b, wo_non_ord_samp_booking_dtls c, wo_non_ord_samp_booking_mst d where a.id=b.mst_id and b.fab_des_id=c.id and b.fabric_source=1 and b.process_id not in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.booking_no = d.booking_no and a.id in (".$booking_id.") and c.lib_yarn_count_deter_id = ".$fabric_description_id." group by a.id, d.id, a.booking_no, d.booking_no");

					foreach ($swo_sql as  $val)
					{
						$sample_booking_id = $val[csf('po_breakdown_id')];
						$swo_qnty += $val[csf('fabric_qty')];
						$booking_no = $val[csf('booking_no')];
					}

					$pre_swo_sql=sql_select("select a.id, a.prod_id, a.used_qty, a.rate, a.amount, b.booking_without_order, b.booking_no, b.receive_basis, c.fabric_description_id, a.mst_id, a.dtls_id from pro_material_used_dtls a, inv_receive_master b, pro_finish_fabric_rcv_dtls c where a.mst_id=b.id and a.dtls_id=c.id and b.id =c.mst_id and b.entry_form=37 and c.fabric_description_id=$fabric_description_id and b.booking_id in (".$booking_id.") and b.booking_without_order=1 and b.receive_basis=11 and c.status_active=1 and c.is_deleted=0");

					$update_data_arr=array(); $pre_rcv_swo_qnty=$this_rcv_swo_qnty=0;
					foreach($pre_swo_sql as $val)
					{
						if($val[csf('mst_id')]==$update_id && $val[csf('dtls_id')]==$update_dtls_id)
						{
							$this_rcv_swo_qnty +=$val[csf('used_qty')];
						}else{
							$pre_rcv_swo_qnty +=$val[csf('used_qty')];
						}
					}

					if($grey_fabric_in_roll_lvl==1)
					{
						$sql="select c.detarmination_id ,c.id,c.lot,c.brand, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type,sum(d.qnty) issue_qty,sum(d.amount) cons_amount
						from inv_issue_master a, inv_grey_fabric_issue_dtls b, product_details_master c, pro_roll_details d
						where a.id=b.mst_id and b.prod_id=c.id and b.id=d.dtls_id and a.id=d.mst_id and a.entry_form in(61) and b.trans_id>0 and a.item_category=13 and d.entry_form in (61) and d.po_breakdown_id in(".$sample_booking_id.") and d.booking_without_order=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.detarmination_id=".$fabric_description_id."  group by c.id,c.lot,c.brand,c.supplier_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type,c.detarmination_id";

					}
					else
					{
						$sql="select c.detarmination_id ,c.id,c.lot,c.brand, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type,sum(b.issue_qnty) as issue_qty, sum(b.amount) as cons_amount from inv_issue_master a, inv_grey_fabric_issue_dtls b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id  and a.entry_form in(16) and b.trans_id>0 and a.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.detarmination_id=".$fabric_description_id." and a.booking_no ='$booking_no' group by c.id,c.lot,c.brand,c.supplier_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type,c.detarmination_id";

					}

				}
				else
				{
					$sales_cond = ($is_sales==1)?" and d.is_sales=1":"";
					$sql="SELECT c.detarmination_id ,c.id,c.lot,c.brand, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type,sum(b.cons_quantity) issue_qty,sum(b.cons_amount) cons_amount from  order_wise_pro_details d, product_details_master c, inv_transaction b , inv_issue_master a where    d.po_breakdown_id in(".$po_id_all.") and  d.trans_type = 2 AND d.entry_form IN (16, 61)  and d.prod_id=c.id  and c.detarmination_id=".$fabric_description_id."   AND  b.mst_id  = a.id  AND b.id = d.trans_id  AND b.prod_id = d.prod_id     and a.entry_form in (16,61)   and a.item_category=13 and b.id=d.trans_id      and a.status_active=1    and b.item_category=13 and b.status_active=1   and c.detarmination_id=".$fabric_description_id." $sales_cond group by c.id,c.lot,c.brand,c.supplier_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type,c.detarmination_id";
				}

				if($recieve_basis==11)
				{
					$pre_swo_sql=sql_select("select a.id, a.prod_id, a.used_qty, a.rate, a.amount, b.booking_without_order, b.booking_no, b.receive_basis, c.fabric_description_id, a.mst_id, a.dtls_id from pro_material_used_dtls a, inv_receive_master b, pro_finish_fabric_rcv_dtls c where a.mst_id=b.id and a.dtls_id=c.id and b.id =c.mst_id and b.entry_form=37 and c.fabric_description_id=$fabric_description_id and b.booking_id in (".$booking_id.")  and b.receive_basis=11 and c.status_active=1 and c.is_deleted=0");

					$update_data_arr=array(); $pre_used_qty=$this_used_qty=0;
					foreach($pre_swo_sql as $val)
					{
						if($val[csf('mst_id')]==$update_id && $val[csf('dtls_id')]==$update_dtls_id)
						{
							$this_used_qty +=$val[csf('used_qty')];
						}else{
							$pre_used_qty +=$val[csf('used_qty')];
						}
					}
				}
				//echo $sql;
				if($update_dtls_id!="")
				{
					$update_sql=sql_select("select id,prod_id,used_qty,rate,amount from pro_material_used_dtls where mst_id=$update_id and dtls_id =$update_dtls_id");
					$update_data_arr=array();
					foreach($update_sql as $val)
					{
						$update_data_arr[$val[csf('prod_id')]]['prod_id']=$val[csf('prod_id')];
						$update_data_arr[$val[csf('prod_id')]]['id']=$val[csf('id')];
						$update_data_arr[$val[csf('prod_id')]]['used_qty']=$val[csf('used_qty')];
						$update_data_arr[$val[csf('prod_id')]]['rate']=$val[csf('rate')];
						$update_data_arr[$val[csf('prod_id')]]['amount']=$val[csf('amount')];
						$check_arr[]=$val[csf('prod_id')];
					}
				}
				//echo $sql;
				$nameArray=sql_select($sql);
				foreach ($nameArray as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					$composition_string = $composition_arr[$row[csf('detarmination_id')]];
					$net_used=$txt_receive_qnty;
					$process_loss_used=($net_used*100)/(100-$process_loss);
					//echo $process_loss_used;
					if(in_array($row[csf("id")], $check_arr))
					{
						?>
						<tr bgcolor="#FFFF99" style="text-decoration:none;" id="search<? echo $i;?>">
							<td width="30" align="center">
								<?php echo "$i"; ?>
								<input type="hidden" name="update_material_id[]" id="update_material_id<?php echo $i; ?>" value="<?php echo $update_data_arr[$row[csf('id')]]['id']; ?>"/>
								<input type="hidden" name="txt_net_used[]" id="txt_net_used<?php echo $i; ?>" value="<?php  echo $net_used; ?>"/>
								<input type="hidden" name="txt_cons_rate[]" id="txt_cons_rate<?php echo $i; ?>" value="<?php echo $update_data_arr[$row[csf('id')]]['rate']; ?>"/>
								<input type="hidden" name="txt_grey_issued[]" id="txt_grey_issued<?php echo $i;?>" value="<?php echo $row[csf('issue_qty')];?>"/>
								<input type="hidden" name="txt_prod_id[]" id="txt_prod_id<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>" />
								<input type="hidden" name="txt_yarn_count_id[]" id="txt_yarn_count_id<?php echo $i; ?>" value="<?php echo $row[csf('yarn_count_id')]; ?>" />
								<input type="hidden" name="txt_brand[]" id="txt_brand<?php echo $i; ?>" value="<?php echo $brand_arr[$row[csf('brand')]]; ?>" />	         <input type="hidden" name="txt_lot[]" id="txt_lot<?php echo $i; ?>" value="<?php echo $row[csf('lot')]; ?>" />
							</td>
							<td width="60"><p><?php  echo $row[csf('id')];?></p></td>
							<td width="80"><p><?php  echo $row[csf('lot')];?></p></td>
							<td width="250"><p><?php echo $composition_string;?></p></td>

							<td width="80"><p><?php  echo $brand_arr[$row[csf('brand')]];?></p></td>
							<td width="100" align="right"><p><?php  echo $update_data_arr[$row[csf('id')]]['rate'];?></p></td>
							<td width="70" align="right"><p><?php   echo $net_used;?></p></td>
							<td width="70" align="right"><p><?php   echo $pre_used_qty;?></p></td>
							<td><input type="text" id="txt_used_qty_<? echo $i;  ?>" name="txt_used_qty[]"  style="width:80px" class="text_boxes_numeric" value="<?

							echo  $update_data_arr[$row[csf('id')]]['used_qty'];
							//if($process_loss_used==0) { $process_loss_used=$update_data_arr[$row[csf('id')]]['used_qty'];} echo number_format($process_loss_used,2,".","");

							?>" placeholder="<?  echo number_format($process_loss_used,2,".",""); ?>" onKeyUp="fnc_ommit_data(<? echo $i;  ?>)"/></td>
						</tr>
						<?
						$i++;
					}
					else
					{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;" id="search<? echo $i;?>">
							<td width="30" align="center"><?php echo "$i"; ?>
								<input type="hidden" name="update_material_id[]" id="update_material_id<?php echo $i; ?>" value="0"/>
								<input type="hidden" name="txt_net_used[]" id="txt_net_used<?php echo $i; ?>" value="<?php  echo $net_used; ?>"/>
								<input type="hidden" name="txt_cons_rate[]" id="txt_cons_rate<?php echo $i; ?>" value="<?php echo $row[csf('cons_amount')]/$row[csf('issue_qty')]; ?>"/>
								<input type="hidden" name="txt_grey_issued[]" id="txt_grey_issued<?php echo $i;?>" value="<?php echo $row[csf('issue_qty')];?>"/>
								<input type="hidden" name="txt_prod_id[]" id="txt_prod_id<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>" />
								<input type="hidden" name="txt_yarn_count_id[]" id="txt_yarn_count_id<?php echo $i; ?>" value="<?php echo $row[csf('yarn_count_id')]; ?>" />
								<input type="hidden" name="txt_brand[]" id="txt_brand<?php echo $i; ?>" value="<?php echo $brand_arr[$row[csf('brand')]]; ?>" />
								<input type="hidden" name="txt_lot[]" id="txt_lot<?php echo $i; ?>" value="<?php echo $row[csf('lot')]; ?>" />
							</td>
							<td width="60"><p><?php echo $row[csf('id')];?></p></td>
							<td width="80"><p><?php echo $row[csf('lot')];?></p></td>
							<td width="250"><p><?php echo $composition_string;?></p></td>
							<td width="80"><p><?php echo $brand_arr[$row[csf('brand')]];?></p></td>
							<td width="100" align="right"><p><?php echo $row[csf('cons_amount')]/$row[csf('issue_qty')];?></p></td>
							<td width="70" align="right"><p><?php echo $net_used;?></p></td>
							<td width="70" align="right"><p><?php   echo $pre_used_qty;?></p></td>
							<td><input type="text" id="txt_used_qty_<? echo $i; ?>" name="txt_used_qty[]"  style="width:80px" class="text_boxes_numeric" value="<? if($i==1 && $update_dtls_id=="") echo number_format($process_loss_used,2,".",""); ?>" placeholder="<?  echo number_format($process_loss_used,2,".",""); ?>" onKeyUp="fnc_ommit_data(<? echo $i;  ?>)"/></td>
						</tr>
						<?
						$i++;
					}
				}
				?>
				<input type="hidden" name="txt_lot_row_id" id="txt_lot_row_id" value="<?php echo $i; ?>"/>
			</table>
		</div>
		<table width="840" cellspacing="0" cellpadding="0" border="1" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:100%; float:left" align="center">
							<input type="hidden" name="swo_qnty" id="swo_qnty" value="<?php echo $swo_qnty; ?>"/>
							<input type="hidden" name="pre_rcv_swo_qnty" id="pre_rcv_swo_qnty" value="<?php echo $pre_rcv_swo_qnty; ?>"/>
							<input type="hidden" name="txt_knitting_charge" id="txt_knitting_charge" value="<?php echo $dyeing_charge; ?>"/>
							<input type="button" name="close" onClick="fnc_process_cost();" class="formbutton" value="Close" style="width:100px" />
						</div>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<?
	exit();
}

if($action=="yarn_lot_popup_backup")
{
	echo load_html_head_contents("Yarn Lot Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$save_data=explode(",",$save_data);
	foreach($save_data as $data_arr)
	{
		$data_arr=explode("**",$data_arr);
		$po_arr[]=$data_arr[0];
	}
	$po_id_all=implode(",",$po_arr);
	if($po_id_all=="") $po_id_all=0;
	$row_cond="";
	$row_limit="";
	if($db_type==0) {$txt_receive_date=change_date_format($txt_receive_date,'yyyy-mm-dd','-'); $row_limit=" limit 1";}
	else { $txt_receive_date=change_date_format($txt_receive_date,'yyyy-mm-dd','-',1); $row_cond=" and rownum=1";}
	?>
	<script>

		function fnc_process_cost()
		{
			var process_string="";
			var knitting_rate_string="";
			var all_deleted_id='';
			var receive_qty=<? echo $txt_receive_qnty; ?>;
			var rate_with_knitting_charge=0;
			var total_amount=0;
			var knitting_charge=$("#txt_knitting_charge").val()*1;
			var total_used_qty=0;
			$("#tbl_lot_list").find('tr').each(function()
			{
				var txt_used=$(this).find('input[name="txt_used_qty[]"]').val()*1;
				if(txt_used>0)
				{
					total_used_qty=total_used_qty+txt_used;
					var txt_prod_id=$(this).find('input[name="txt_prod_id[]"]').val();
					var txt_cons_rate=$(this).find('input[name="txt_cons_rate[]"]').val();
					var txt_net_used=$(this).find('input[name="txt_net_used[]"]').val();
					var txt_material_update_id=$(this).find('input[name="update_material_id[]"]').val();

					if(txt_net_used==0) txt_net_used=txt_used;

					var txt_grey_cost=txt_cons_rate*txt_used;
					total_amount+=txt_grey_cost;
					var grey_rate=txt_grey_cost/txt_net_used;
					process_string=txt_prod_id+"*"+txt_used+"*"+txt_cons_rate+"*"+txt_material_update_id;
					knitting_charge=(knitting_charge*txt_used)/txt_net_used;
				}
				else
				{
					if($(this).find('input[name="update_material_id[]"]').val()>0)
					{
						all_deleted_id=$(this).find('input[name="update_material_id[]"]').val();
					}
				}
			});

			var total_rate=total_amount/receive_qty;
			if( total_used_qty<receive_qty ){
				alert("Total Used Qty Must be Greater or Equal to Receive Qty.");
				return;
			}

			knitting_rate_string=knitting_charge+"*"+total_rate+"*"+all_deleted_id;
			$('#hidden_process_string').val( process_string );
			$('#hidden_knitting_rate').val( knitting_rate_string );
			parent.emailwindow.hide();
		}

		function fnc_ommit_data(id)
		{
			var tr_length=$("#txt_lot_row_id").val();
			for(var j=1;j<tr_length; j++)
			{
				if(j!=id) $("#txt_used_qty_"+j).val('');
			}
		}

	</script>
	<input type="hidden" name="hidden_process_string" id="hidden_process_string" value="" />
	<input type="hidden" name="hidden_knitting_rate" id="hidden_knitting_rate" value="" />
	<div>
		<?php
		$color_id = return_field_value("id","lib_color", "color_name='$name_color'");
		$yarn_count_arr = return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
		$brand_arr = return_library_array( "select id,brand_name  from  lib_brand",'id','brand_name ');
		if($recieve_basis==9)
		{
			$precost_exchange_rate=return_field_value("exchange_rate","wo_pre_cost_mst", "job_no='$txt_job_no'");
			$conversion_cost=sql_select("select b.id,sum(a.charge_unit) as charge_unit from  wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b where a.job_no='$txt_job_no' and a.fabric_description=b.id and a.job_no=b.job_no and b.lib_yarn_count_deter_id=".$fabric_description_id." and a.cons_process in(25,26,32,33,34,35,36,37,38,39,40,60,61,62,64,67,68,69,70,71,72,73,74,75,77,78,79,80,81,82,83,84,85,86,87,88,89,92,93,94,100,125,127,128,129,132,133,134,135,136,137,138,63,65,66,76,90,91) group by b.id");

			$fabricnyarn_dyeing=sql_select("select b.id,a.color_break_down from  wo_pre_cost_fab_conv_cost_dtls a,wo_pre_cost_fabric_cost_dtls b where a.job_no='$txt_job_no' and a.fabric_description=b.id and a.job_no=b.job_no and b.lib_yarn_count_deter_id=".$fabric_description_id." and a.cons_process in (31)");//and a.cons_process in (30,31)
			$color_dyeing_cost=array();
			foreach($fabricnyarn_dyeing as $inf)
			{
				$color_breakdown=explode("_",$inf[csf('color_break_down')]);
				$color_dyeing_cost[$color_breakdown[0]]=$color_breakdown[1];
			}
			$fabric_dyeing_charge=$color_dyeing_cost[$color_id]*$precost_exchange_rate;
			$other_charge=$conversion_cost[0][csf('charge_unit')]*$precost_exchange_rate;
			$dyeing_charge=$fabric_dyeing_charge+$other_charge;
		}
		else
		{
			$conversition_cost_sql=sql_select("select b.process, a.currency_id,a.exchange_rate,sum(b.amount)/sum(b.wo_qnty) as rate     from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fab_conv_cost_dtls c,wo_pre_cost_fabric_cost_dtls d where a.booking_no=b.booking_no and a.id=".$booking_id." and b.fabric_color_id=".$color_id." and b.pre_cost_fabric_cost_dtls_id=c.id and c.fabric_description=d.id and c.job_no=d.job_no and b.process in (31,25,26,32,33,34,35,36,37,38,39,40,60,61,62,64,67,68,69,70,71,72,73,74,75,77,78,79,80,81,82,83,84,85,86,87,88,89,92,93,94,100,125,127,128,129,132,133,134,135,136,137,138,63,65,66,76,90,91) and lib_yarn_count_deter_id=".$fabric_description_id." group by a.currency_id,a.exchange_rate,b.process ");
			$dyeing_charge=0;
			foreach($conversition_cost_sql as $charge_value)
			{
				$dyeing_charge+=$charge_value[csf('rate')]*$charge_value[csf('exchange_rate')];
			}
		}

		$processloss_sql=sql_select("select sum(process_loss) as process_loss from conversion_process_loss   where  mst_id=".$fabric_description_id." and process_id in(31,25,26,32,33,34,35,36,37,38,39,40,60,61,62,64,67,68,69,70,71,72,73,74,75,77,78,79,80,81,82,83,84,85,86,87,88,89,92,93,94,100,125, 127,128, 129,132,133,134,135,136,137,138,63,31,30,65,66,76,90,91)");

		$process_loss=$processloss_sql[0][csf('process_loss')];
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id order by b.id asc";
		$data_array=sql_select($sql_deter);
		if(count($data_array)>0)
		{
			foreach( $data_array as $row )
			{
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
			}
		}
		?>
		<table cellspacing="0" cellpadding="0" border="0" rules="all" width="840" class="" align="center">
			<tr>
				<td colspan="5" align="center" style="font-size:16px">
					<strong>Dyeing process loss <?php echo $process_loss." %";?></strong>
				</td>
				<td colspan="5" align="center" style="font-size:16px">
					<strong>Dyeing Charge <?php echo number_format($dyeing_charge,2)."Tk./Kg";?></strong>
				</td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="60">Prod Id</th>
				<th width="80">Lot</th>
				<th width="250">Fabric Description</th>
				<th width="80">Brand</th>
				<th width="100">Avg Grey Fabric Rate /Kg (Tk.) </th>
				<th width="70">Net Qty</th>
				<th >Used Qty</th>
			</thead>
		</table>
		<div style="width:840px; max-height:280px; overflow-y:scroll">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table" id="tbl_lot_list">
				<?php
				$i=1;
				$sql_cond="";
				if($serch_count_arr>0) $sql_cond=" and c.yarn_count_id in (".implode(",",$serch_count_arr).") ";
				if($serch_composition_arr>0) $sql_cond.=" and c.yarn_comp_type1st in (".implode(",",$serch_composition_arr).") ";
				if($serch_type_arr>0) $sql_cond.=" and c.yarn_type in (".implode(",",$serch_type_arr).") ";
				if($recieve_basis==11 && $booking_without_order==1)
				{
					$sql="select c.detarmination_id ,c.id,c.lot,c.brand, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type,sum(d.qnty) issue_qty,sum(d.amount) cons_amount
					from inv_issue_master a, inv_grey_fabric_issue_dtls b, product_details_master c, pro_roll_details d
					where a.id=b.mst_id and b.prod_id=c.id and b.id=d.dtls_id and a.id=d.mst_id and a.entry_form in(16,61) and b.trans_id>0 and a.item_category=13 and d.entry_form in (16,61) and d.po_breakdown_id in(".$booking_id.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.detarmination_id=".$fabric_description_id."  group by c.id,c.lot,c.brand,c.supplier_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type,c.detarmination_id";
				}
				else
				{
					$sales_cond = ($is_sales==1)?" and d.is_sales=1":"";
					$sql="select c.detarmination_id ,c.id,c.lot,c.brand, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type,sum(b.cons_quantity) issue_qty,sum(b.cons_amount) cons_amount
					from inv_issue_master a, inv_transaction b, product_details_master c,order_wise_pro_details d
					where a.id=b.mst_id  and a.entry_form in (16,61) and b.prod_id=c.id and a.item_category=13 and b.id=d.trans_id and b.prod_id=d.prod_id and d.trans_type=2 and d.entry_form in (16,61) and d.po_breakdown_id in(".$po_id_all.") and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.item_category=13 and b.status_active=1 and b.is_deleted=0 and c.detarmination_id=".$fabric_description_id." $sales_cond group by c.id,c.lot,c.brand,c.supplier_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type,c.detarmination_id";
				}
				//echo $sql;
				if($update_dtls_id!="")
				{
					$update_sql=sql_select("select id,prod_id,used_qty,rate,amount from pro_material_used_dtls where mst_id=$update_id and dtls_id =$update_dtls_id");
					$update_data_arr=array();
					foreach($update_sql as $val)
					{
						$update_data_arr[$val[csf('prod_id')]]['prod_id']=$val[csf('prod_id')];
						$update_data_arr[$val[csf('prod_id')]]['id']=$val[csf('id')];
						$update_data_arr[$val[csf('prod_id')]]['used_qty']=$val[csf('used_qty')];
						$update_data_arr[$val[csf('prod_id')]]['rate']=$val[csf('rate')];
						$update_data_arr[$val[csf('prod_id')]]['amount']=$val[csf('amount')];
						$check_arr[]=$val[csf('prod_id')];
					}
				}

				$nameArray=sql_select($sql);
				foreach ($nameArray as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					$composition_string = $composition_arr[$row[csf('detarmination_id')]];
					$net_used=$txt_receive_qnty;
					$process_loss_used=($net_used*100)/(100-$process_loss);
					if(in_array($row[csf("id")], $check_arr))
					{
						?>
						<tr bgcolor="#FFFF99" style="text-decoration:none;" id="search<? echo $i;?>">
							<td width="30" align="center">
								<?php echo "$i"; ?>
								<input type="hidden" name="update_material_id[]" id="update_material_id<?php echo $i; ?>" value="<?php echo $update_data_arr[$row[csf('id')]]['id']; ?>"/>
								<input type="hidden" name="txt_net_used[]" id="txt_net_used<?php echo $i; ?>" value="<?php  echo $net_used; ?>"/>
								<input type="hidden" name="txt_cons_rate[]" id="txt_cons_rate<?php echo $i; ?>" value="<?php echo $update_data_arr[$row[csf('id')]]['rate']; ?>"/>
								<input type="hidden" name="txt_prod_id[]" id="txt_prod_id<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>" />
								<input type="hidden" name="txt_yarn_count_id[]" id="txt_yarn_count_id<?php echo $i; ?>" value="<?php echo $row[csf('yarn_count_id')]; ?>" />
								<input type="hidden" name="txt_brand[]" id="txt_brand<?php echo $i; ?>" value="<?php echo $brand_arr[$row[csf('brand')]]; ?>" />	         <input type="hidden" name="txt_lot[]" id="txt_lot<?php echo $i; ?>" value="<?php echo $row[csf('lot')]; ?>" />
							</td>
							<td width="60"><p><?php  echo $row[csf('id')];?></p></td>
							<td width="80"><p><?php  echo $row[csf('lot')];?></p></td>
							<td width="250"><p><?php echo $composition_string;?></p></td>

							<td width="80"><p><?php  echo $brand_arr[$row[csf('brand')]];?></p></td>
							<td width="100" align="right"><p><?php  echo $update_data_arr[$row[csf('id')]]['rate'];?></p></td>
							<td width="70" align="right"><p><?php   echo $net_used;?></p></td>
							<td><input type="text" id="txt_used_qty_<? echo $i;  ?>" name="txt_used_qty[]"  style="width:80px" class="text_boxes_numeric" value="<? if($process_loss_used==0) { $process_loss_used=$update_data_arr[$row[csf('id')]]['used_qty'];} echo number_format($process_loss_used,2); ?>" placeholder="<?  echo number_format($process_loss_used,2,".",""); ?>" onKeyUp="fnc_ommit_data(<? echo $i;  ?>)"/></td>
						</tr>
						<?
						$i++;
					}
					else
					{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;" id="search<? echo $i;?>">
							<td width="30" align="center"><?php echo "$i"; ?>
								<input type="hidden" name="update_material_id[]" id="update_material_id<?php echo $i; ?>" value="0"/>
								<input type="hidden" name="txt_net_used[]" id="txt_net_used<?php echo $i; ?>" value="<?php  echo $net_used; ?>"/>
								<input type="hidden" name="txt_cons_rate[]" id="txt_cons_rate<?php echo $i; ?>" value="<?php echo $row[csf('cons_amount')]/$row[csf('issue_qty')]; ?>"/>
								<input type="hidden" name="txt_prod_id[]" id="txt_prod_id<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>" />
								<input type="hidden" name="txt_yarn_count_id[]" id="txt_yarn_count_id<?php echo $i; ?>" value="<?php echo $row[csf('yarn_count_id')]; ?>" />
								<input type="hidden" name="txt_brand[]" id="txt_brand<?php echo $i; ?>" value="<?php echo $brand_arr[$row[csf('brand')]]; ?>" />
								<input type="hidden" name="txt_lot[]" id="txt_lot<?php echo $i; ?>" value="<?php echo $row[csf('lot')]; ?>" />
							</td>
							<td width="60"><p><?php echo $row[csf('id')];?></p></td>
							<td width="80"><p><?php echo $row[csf('lot')];?></p></td>
							<td width="250"><p><?php echo $composition_string;?></p></td>
							<td width="80"><p><?php echo $brand_arr[$row[csf('brand')]];?></p></td>
							<td width="100" align="right"><p><?php echo $row[csf('cons_amount')]/$row[csf('issue_qty')];?></p></td>
							<td width="70" align="right"><p><?php echo $net_used;?></p></td>
							<td><input type="text" id="txt_used_qty_<? echo $i; ?>" name="txt_used_qty[]"  style="width:80px" class="text_boxes_numeric" value="<? if($i==1 && $update_dtls_id=="") echo number_format($process_loss_used,2,".",""); ?>" placeholder="<?  echo number_format($process_loss_used,2); ?>" onKeyUp="fnc_ommit_data(<? echo $i;  ?>)"/></td>
						</tr>
						<?
						$i++;
					}
				}
				?>
				<input type="hidden" name="txt_lot_row_id" id="txt_lot_row_id" value="<?php echo $i; ?>"/>
			</table>
		</div>
		<table width="840" cellspacing="0" cellpadding="0" border="1" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:100%; float:left" align="center">
							<input type="hidden" name="txt_knitting_charge" id="txt_knitting_charge" value="<?php echo number_format($dyeing_charge,2); ?>"/>
							<input type="button" name="close" onClick="fnc_process_cost();" class="formbutton" value="Close" style="width:100px" />
						</div>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<?
	exit();
}

if($action=="check_conversion_rate")
{
	$data=explode("**",$data);
	if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$exchange_rate=set_conversion_rate( $data[0], $conversion_date, $data[2] );
	echo $exchange_rate;
	exit();
}

if($action=="show_roll_listview")
{
	$data=explode("**",str_replace("'","",$data));
	$dtls_id=$data[0];
	$barcode_generation=$data[1];
	$booking_without_order=$data[2];
	if($booking_without_order==1)
	{
		$query="select id,roll_no,barcode_no,po_breakdown_id,qnty,booking_no as po_number from pro_roll_details  where dtls_id=$dtls_id and entry_form=37 and roll_id=0 and status_active=1 and is_deleted=0";
			//$caption="Booking No.";
	}
	else
	{
		$query="select a.id, a.roll_no, a.barcode_no, a.po_breakdown_id, a.qnty, b.po_number, a.booking_without_order from pro_roll_details a, wo_po_break_down b where a.po_breakdown_id=b.id and a.dtls_id=$dtls_id and a.entry_form=37 and roll_id=0 and a.status_active=1 and a.is_deleted=0 order by a.id";
			//$caption="PO No.";
	}
	?>
	<div align="center">
		<?
			/*if($barcode_generation==2)
			{*/
				?>
				<!--<input type="button" id="btn_send_to_printer" name="btn_send_to_printer" value="Send To Printer" class="formbutton" onClick="fnc_send_printer_text()"/>-->
				<?
			/*}
			else
			{*/
				?>
				<!--<input type="button" id="btn_barcode_generation" name="btn_barcode_generation" value="Barcode Generation" class="formbutton" onClick="fnc_barcode_generation()"/>-->
				<?
			//}

				?>
				<input type="button" id="btn_send_to_printer" name="btn_send_to_printer" value="Send To Printer" class="formbutton" onClick="fnc_send_printer_text()"/>
			</div>
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="100%">
				<thead>
					<th width="90">PO No</th>
					<th width="45">Roll No</th>
					<th width="60">Roll Qnty</th>
					<th width="85">Barcode No.</th>
					<th>Check All <input type="checkbox" name="check_all"  id="check_all" onClick="check_all_report()"></th>
				</thead>
			</table>
			<div style="width:100%; max-height:200px; overflow-y:scroll" id="list_container" align="left">
				<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="100%" id="tbl_list_search">
					<?
					$i=1;
				//$query="select a.id, a.roll_no, a.barcode_no, a.po_breakdown_id, a.qnty, b.po_number, a.booking_without_order from pro_roll_details a, wo_po_break_down b where a.po_breakdown_id=b.id and a.dtls_id=$dtls_id and a.status_active=1 and a.is_deleted=0 order by a.id";
					$result=sql_select($query);
					foreach($result as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
							<td width="90">
								<p><? if($row[csf('booking_without_order')]!=1) echo $row[csf('po_number')]; ?></p>
								<input type="hidden" name="txtRollTableId[]" id="txtRollTableId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
							</td>
							<td width="43" style="padding-left:2px"><? echo $row[csf('roll_no')]; ?></td>
							<td align="right" width="58" style="padding-right:2px"><? echo $row[csf('qnty')]; ?></td>
							<td width="85" style="padding-left:2px"><? echo $row[csf('barcode_no')]; ?></td>
							<td align="center" valign="middle">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input id="chkBundle_<? echo $i;  ?>" type="checkbox" name="chkBundle"></td>
						</tr>
						<?
						$i++;
					}
					?>
				</table>
			</div>
			<?
			exit();
		}

		if ($action == "report_barcode_text_file") {
			$data = explode("***", $data);
		// For "Grey Fabric Bar-code Striker Export Report" report page

		// For "Grey Fabric Bar-code Striker Export Report" report page (end)
			$booking_no=$data[2];
			$color_arr = return_library_array("select id, color_name from lib_color where status_active=1", 'id', 'color_name');
			$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
			$machine_no_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');
	$machine_brand_arr = return_library_array("select id, brand from lib_machine_name", 'id', 'brand'); // Temporary

	$sql = "select a.company_id, a.recv_number, a.location_id, a.receive_basis, a.booking_id, a.booking_no, a.booking_without_order, a.within_group, a.receive_date, a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm, b.width,b.dia_width_type, b.machine_no_id, b.color_id, b.fabric_description_id, b.shift_name, b.insert_date  from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and b.id=$data[1]";
	//echo $sql;die;
	$result = sql_select($sql);
	$party_name = '';
	$prod_date = '';
	$order_id = '';
	$buyer_name = '';
	$grey_dia = '';
	$booking_without_order = '';
	$gsm = '';
	$finish_dia = '';
	foreach ($result as $row) {
		if ($row[csf('knitting_source')] == 1) {
			$party_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('knitting_company')]);
		} else if ($row[csf('knitting_source')] == 3) {
			$party_name = return_field_value("short_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
		}


		$tube_type = $fabric_typee[$row[csf('dia_width_type')]];

		$booking_without_order= $row[csf('booking_without_order')];

		//$prod_date=date("d-m-Y",strtotime($row[csf('insert_date')]));
		//$prod_time=date("H:i",strtotime($row[csf('insert_date')]));
		$prod_date = date("d-m-Y", strtotime($row[csf('receive_date')]));
		$order_id = $row[csf('order_id')];
		$gsm = $row[csf('gsm')];
		$finish_dia = $row[csf('width')];
		$shiftName = $shift_name[$row[csf('shift_name')]];
		$colorRange = $color_range[$row[csf('color_range_id')]];

		//$color=$color_arr[$row[csf('color_id')]];
		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');
		if (trim($color) != "") {
			//$color=", ".$color;
			//$color="".$color;
		}

		//$stitch_length = $row[csf('stitch_length')];
		//$yarn_lot = $row[csf('yarn_lot')];
		//$brand = $brand_arr[$row[csf('brand_id')]];
		/*$yarn_count = '';
		$count_id = explode(",", $row[csf('yarn_count')]);
		foreach ($count_id as $val) {
			if ($val > 0) {
				if ($yarn_count == "") $yarn_count = $count_arr[$val]; else $yarn_count .= "," . $count_arr[$val];
			}
		}
		*/

		$machine_data = sql_select("select machine_no, dia_width, gauge,brand from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
		$machine_name = $machine_data[0][csf('machine_no')];
		//$machine_dia_width=$machine_data[0][csf('dia_width')];
		//$machine_gauge=$machine_data[0][csf('gauge')];
		$machine_dia_width = $machine_data[0][csf('dia_width')];
		$machine_gauge = $machine_data[0][csf('gauge')];
		$machine_brand = $machine_data[0][csf('brand')];


		$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);


		$comp = '';
		if ($row[csf('febric_description_id')] == 0 || $row[csf('febric_description_id')] == "") {
			$comp = return_field_value("item_description", "product_details_master", "id=" . $row[csf('prod_id')]);
		} else {
			$determination_sql = sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=" . $row[csf('febric_description_id')]." order by b.id asc");

			if ($determination_sql[0][csf('construction')] != "") {
				$comp = $determination_sql[0][csf('construction')] . ", ";
				$construction = $determination_sql[0][csf('construction')];
			}

			foreach ($determination_sql as $d_row) {
				$comp .= $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";
				$composi .= $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";
			}
		}
	}


	//echo "select a.job_no,a.job_no_prefix_num,b.id,b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($order_id)";
	//echo $booking_id;die;
	$po_array = array();
	$booking_no_prefix = '';
	if ($booking_without_order != 1) {
		$po_sql = sql_select("select a.job_no, a.style_ref_no, a.buyer_name, a.job_no_prefix_num, b.id, b.po_number, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and b.id in($order_id)");
		foreach ($po_sql as $row) {
			$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
			$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
			$po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
			$po_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
			$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
			$buyer_name = $buyer_arr[$row[csf('buyer_name')]];
		}
	}
	foreach (glob("" . "*.zip") as $filename) {
		@unlink($filename);
	}
	//echo $within_group;
	//exit;
	$i = 1;
	$zip = new ZipArchive();            // Load zip library
	$filename = str_replace(".sql", ".zip", 'norsel_bundle.sql');            // Zip name
	if ($zip->open($filename, ZIPARCHIVE::CREATE) !== TRUE) {        // Opening zip file to load files
		$error .= "* Sorry ZIP creation failed at this time<br/>";
		echo $error;
	}

	$i = 1;
	$year = date("y");
	$query = "select a.id, a.roll_no, a.po_breakdown_id, a.barcode_no, a.qnty,a.reject_qnty from pro_roll_details a  where a.id in($data[0]) and a.entry_form=37 and roll_id=0 order by a.barcode_no asc";
	//echo	$booking_without_order;die;
	//echo $query;die;
	$res = sql_select($query);
	$split_data_arr = array();
	foreach ($res as $row) {
		$split_roll_id = $row[csf('id')];
		$roll_split_query = sql_select("select a.barcode_no, a.qnty, a.id, a.roll_split_from from pro_roll_details a where a.roll_id = $split_roll_id and a.roll_split_from != 0");
		$file_name = "NORSEL-IMPORT_" . $i;
		$myfile = fopen($file_name . ".txt", "w") or die("Unable to open file!");
		$txt = "Norsel_imp\r\n1\r\n";
		if ($booking_without_order == 1) {
			$txt .= $party_name . ",";
			$txt .= "Job No.".$booking_no_prefix . "\r\n";
			$txt .= $machine_name . "-" . $machine_dia_width . "X" . $machine_gauge . "\r\n";
			$full_job_no = $full_booking_no;
			//$txt .=$party_name." Booking No.".$booking_no_prefix." M/C:".$machine_name."-".$machine_dia_width."X".$machine_gauge."\r\n";
		} else {
			$txt .= $party_name;
			$txt .= ",Job No.".$po_array[$row[csf('po_breakdown_id')]]['prefix'] . "\r\n";
			$txt .= $machine_name . "-" . $machine_dia_width . "X" . $machine_gauge . "\r\n";
			$full_job_no = $po_array[$row[csf('po_breakdown_id')]]['job_no'];
		}

		if (!empty($roll_split_query)) {
			$qnty = number_format($roll_split_query[0]['qnty'], 2, '.', '');
			$barcode = $roll_split_query[0]['barcode_no'];
		} else {
			$qnty = number_format($row[csf('QNTY')], 2, '.', '');
			$barcode = $row[csf('barcode_no')];
		}
		$txt .= $barcode . "\r\n";
		//$txt .="Barcode No: ".$row[csf('barcode_no')]."\r\n";
		$txt .= "ID:".$barcode . "\r\n";
		$txt .= "Booking/PI No:".$booking_no . "\r\n";
		$txt .= "D:" . $prod_date . "\r\n";
		$txt .= "Order No: " . $po_array[$row[csf('po_breakdown_id')]]['no'] . "\r\n";//ok
		$txt .= $comp . "\r\n";//ok
		$txt .= "Buyer: ".$buyer_name . "\r\n";
		$txt.="Finish Dia:".$finish_dia."\r\n";
		$txt.="Dia type:".$tube_type."\r\n";
		$txt .= "GSM: " . $gsm . "\r\n";
		$txt .= "Yarn Count: ".$yarn_count . "\r\n";//.$brand." Lot:".$yarn_lot."\r\n";
		$txt .= "RollWt:".$qnty . "Kg\r\n";
		$txt .= "Roll No:" . $row[csf('roll_no')] . "\r\n";
		$txt .="Color:". trim($color) . "\r\n";
		$txt .= "Style Ref.: " . $po_array[$row[csf('po_breakdown_id')]]['style_ref'] . "\r\n";
		fwrite($myfile, $txt);
		fclose($myfile);

		$i++;
	}
	foreach (glob("" . "*.txt") as $filenames) {
		$zip->addFile($file_folder . $filenames);
	}
	$zip->close();

	foreach (glob("" . "*.txt") as $filename) {
		@unlink($filename);
	}
	echo "norsel_bundle";
	exit();
}

if($action=="fabric_sales_order_popup")
{
	echo load_html_head_contents("Fabric Sales Order Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(booking_data) {
			document.getElementById('hidden_booking_data').value = booking_data;
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center" style="width:1100px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:1000px; margin-left:3px">
				<legend>Enter search words</legend>

				<table cellpadding="0" cellspacing="0" border="1" rules="all" width="960" class="rpt_table">
					<thead>
						<th>Search By</th>
						<th>Within Group</th>
						<th>Sales Order No</th>
						<th>Booking No</th>
						<th>Style Ref. No</th>
						<th>Sales Date Range</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton" />
							<input type="hidden" name="hidden_booking_data" id="hidden_booking_data" value="">
							<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
						</th>
					</thead>
					<tr>
						<td align="center">
							<? echo create_drop_down("cbo_receive_basis",152,$receive_basis_arr,"",1,"-- Select --",$receive_basis,"","1","1,2,4,6,9,11,14"); ?>
						</td>
						<td align="center">
							<?
							echo create_drop_down("cbo_within_group", 150, $yes_no, "", 1, "--Select--", "", $dd, 0);
							?>
						</td>
						<td align="center">
							<input type="text" style="width:130px" class="text_boxes"  name="txt_sale_order_no" id="txt_sale_order_no" />
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes"  name="txt_booking_no" id="txt_booking_no" />
						</td>
						<td align="center">
							<input type="text" style="width:130px" class="text_boxes"  name="txt_style_no" id="txt_style_no" />
						</td>
						<td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
							style="width:70px" readonly>To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker"
							style="width:70px" readonly>
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_booking_no').value+'_'+document.getElementById('cbo_receive_basis').value+'_'+document.getElementById('txt_company_id').value+'_'+'_'+document.getElementById('txt_sale_order_no').value+'_'+document.getElementById('txt_style_no').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_fabric_sales_order_search_list_view', 'search_div', 'knit_finish_fabric_receive_by_garments_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:70px;" />
						</td>
					</tr>
					<tr>
						<td colspan="7" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="margin-top:10px;" id="search_div" align="center"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_fabric_sales_order_search_list_view")
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0])."%";
	$receive_basis=$data[1];
	$company_id =$data[2];
	$date_from = trim($data[7]);
	$date_to = trim($data[8]);

	if($receive_basis==14)
	{
		$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
		$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
		$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
		$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');

		$search_booking_string = trim($data[0]); // booking
		$search_style_string = trim($data[5]);
		$search_jobno_string = trim($data[4]);
		$within_group = trim($data[6]);

		if(trim($data[0])!="" || $data[4]!="" ||  trim($data[5])!="")
		{
			$search_field_cond .= ($search_booking_string != "")?" and sales_booking_no like '%" . $search_booking_string . "'":"";
			$search_field_cond .= ($search_jobno_string!= "")?" and job_no_prefix_num=$search_jobno_string":"";
			$search_field_cond .= ($search_style_string != "")?" and style_ref_no like '%" . $search_style_string . "%'":"";
		} else {
			$search_field_cond = '';
		}

		$date_cond = '';
		if ($date_from != "" && $date_to != "") {
			if ($db_type == 0) {
				$date_cond = "and insert_date between '" . change_date_format(trim($date_from), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($date_to), "yyyy-mm-dd", "-") . "'";
			} else {
				$date_cond = "and insert_date between '" . change_date_format(trim($date_from), '', '', 1) . "' and '" . change_date_format(trim($date_to), '', '', 1) . "'";
			}
		}

		if ($within_group == 0) $within_group_cond = ""; else $within_group_cond = " and within_group=$within_group";

		if ($db_type == 0) $year_field = "YEAR(insert_date) as year";
		else if ($db_type == 2) $year_field = "to_char(insert_date,'YYYY') as year";
		else $year_field = "";

		$sql = "select id, $year_field, job_no_prefix_num, job_no, within_group, sales_booking_no, booking_id,booking_without_order, booking_date, buyer_id, style_ref_no, location_id,company_id from fabric_sales_order_mst where status_active=1 and is_deleted=0 and company_id=$company_id $within_group_cond $search_field_cond $date_cond order by id desc";
		$result = sql_select($sql);
		foreach ($result as $row)
		{
			$booking_no_arr[] = "'".$row[csf("sales_booking_no")]."'";
		}

		$booking_cond = (!empty($booking_no_arr))?" and a.booking_no in(".implode(",",$booking_no_arr).")":"";
		$booking_arr = array();
		$booking_info = sql_select("select a.id,a.booking_no, a.booking_type, a.company_id, a.entry_form, a.fabric_source, a.item_category, a.job_no, a.po_break_down_id, a.is_approved, is_short from wo_booking_mst a where a.is_deleted = 0 and a.status_active=1 and a.company_id=$company_id $booking_cond");
		foreach ($booking_info as $row) {
			$booking_arr[$row[csf('booking_no')]]['id'] = $row[csf('id')];
			$booking_arr[$row[csf('booking_no')]]['booking_no'] = $row[csf('booking_no')];
			$booking_arr[$row[csf('booking_no')]]['booking_type'] = $row[csf('booking_type')];
			$booking_arr[$row[csf('booking_no')]]['company_id'] = $row[csf('company_id')];
			$booking_arr[$row[csf('booking_no')]]['entry_form'] = $row[csf('entry_form')];
			$booking_arr[$row[csf('booking_no')]]['fabric_source'] = $row[csf('fabric_source')];
			$booking_arr[$row[csf('booking_no')]]['item_category'] = $row[csf('item_category')];
			$booking_arr[$row[csf('booking_no')]]['job_no'] = $row[csf('job_no')];
			$booking_arr[$row[csf('booking_no')]]['po_break_down_id'] = $row[csf('po_break_down_id')];
			$booking_arr[$row[csf('booking_no')]]['is_approved'] = $row[csf('is_approved')];
			$booking_arr[$row[csf('booking_no')]]['is_short'] = $row[csf('is_short')];
		}
		?>
		<style type="text/css">
		.rpt_table tr{ text-decoration:none; cursor:pointer; }
		.rpt_table tr td{ text-align: center; }
	</style>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="90">Sales Order No</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="70">Buyer</th>
			<th width="120">Booking No</th>
			<th width="80">Booking date</th>
			<th width="110">Style Ref.</th>
			<th>Location</th>
		</thead>
	</table>
	<div style="width:800px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		if(!empty($result)){
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

				if ($row[csf('within_group')] == 1)
					$buyer = $company_arr[$row[csf('buyer_id')]];
				else
					$buyer = $buyer_arr[$row[csf('buyer_id')]];

				$booking_data = $row[csf('id')]. "**" . $row[csf('sales_booking_no')]."**".$row[csf('company_id')]."**".$row[csf('within_group')]."**".$row[csf('booking_id')]."**".$row[csf('booking_without_order')]."**".$row[csf('buyer_id')]."**".$row[csf('job_no')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $booking_data; ?>');">
					<td width="40"><? echo $i; ?></td>
					<td width="90"><? echo $row[csf('job_no_prefix_num')]; ?></td>
					<td width="60"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="80"><p><? echo $yes_no[$row[csf('within_group')]]; ?></p></td>
					<td width="70"><p><? echo $buyer; ?></p></td>
					<td width="120"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
					<td width="80"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					<td><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
				</tr>
				<?
				$i++;
			}
		}else{
			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<th colspan="9">No data found</th>
			</tr>
			<?
		}
		?>
	</table>
</div>
<?
exit();
}

}

if($action=="challan_popup")
{
	echo load_html_head_contents("Challan Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>

		function js_set_value(data,id)
		{
			$('#hidden_challan_no').val(data);
			$('#hidden_challan_id').val(id);
			parent.emailwindow.hide();
		}

	</script>

</head>
<body>
	<div align="center" style="width:760px;" >
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:760px; margin-left:2px">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<th>Company</th>
						<th>Delivery Date Range</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="180">Please Enter Challan No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							<input type="hidden" name="hidden_challan_no" id="hidden_challan_no">
							<input type="hidden" name="hidden_challan_id" id="hidden_challan_id">
						</th>
					</thead>
					<tr class="general">
						<td align="center">
							<? echo create_drop_down( "cbo_company_id", 150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '-- Select Company --',$cbo_company_id,"",1); ?>
						</td>
						<td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
						</td>
						<td align="center">
							<?
							$search_by_arr=array(1=>"Delivery Challan",2=>"Group Delivery Challan");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_company_id').value, 'create_challan_search_list_view', 'search_div', 'knit_finish_fabric_receive_by_garments_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_challan_search_list_view")
{
	$data = explode("_",$data);
	$search_string=trim($data[0]);
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4]; 

	$roll_maintained_upto_textile=return_field_value("fabric_roll_level","variable_settings_production","company_name='$company_id' and item_category_id=2 and variable_list=3 and is_deleted=0 and status_active=1 and page_upto_id=1 and fabric_roll_level=1");

	if($roll_maintained_upto_textile==1)
	{
		$source_entry_form="318";
	}
	else
	{
		$source_entry_form="224";
	}

	if($company_id==0) { echo "Please Select Company First."; die; }
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.issue_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date),"yyyy-mm-dd", "-")."'";
			$date_cond2="and d.issue_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date),"yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.issue_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
			$date_cond2="and d.issue_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond=$date_cond2="";
	}

	if($db_type==0)
	{
		$year_field=" YEAR(a.insert_date) as year";
	}
	else if($db_type==2)
	{
		$year_field=" to_char(a.insert_date,'YYYY') as year";
	}
	else $year_field="";

	if($search_by==1)
	{
		if($search_string!="") $search_field_cond="a.issue_number_prefix_num=$search_string and ";
		$sql="SELECT a.id,a.company_id,a.issue_number sys_number,a.booking_no,$year_field,a.issue_date delevery_date, a.location_id, a.knit_dye_source, a.knit_dye_company, sum(c.quantity) delivery_qty,c.is_sales, d.job_no as fso_no, d.po_job_no, e.batch_no from inv_issue_master a,inv_finish_fabric_issue_dtls b,order_wise_pro_details c, fabric_sales_order_mst d, pro_batch_create_mst e  where $search_field_cond a.supplier_id=$company_id and a.entry_form=$source_entry_form and a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.within_group=1 and b.batch_id = e.id and a.status_active='1' and a.is_deleted='0' and b.status_active=1 and c.entry_form=$source_entry_form and c.status_active=1 $date_cond and c.is_sales=1 group by a.id,a.company_id,a.issue_number, a.booking_no,a.insert_date, a.issue_date, a.location_id, a.knit_dye_source, a.knit_dye_company, c.is_sales, d.job_no, d.po_job_no, e.batch_no";
	}else{
		if($search_string!="") $search_field_cond="a.sys_number_prefix_num=$search_string and ";
		$sql="SELECT b.booking_no,d.id,d.issue_number sys_number,d.company_id,d.knit_dye_source,d.knit_dye_company,d.issue_number_prefix_num sys_number_prefix_num,d.issue_date delevery_date,d.location_id,d.buyer_id, to_char(d.insert_date,'YYYY') as year,sum(c.issue_qnty) as delivery_qty,e.is_sales, f.batch_no, g.job_no as fso_no, g.po_job_no from pro_fin_deli_multy_challa_dtls b,inv_finish_fabric_issue_dtls c,order_wise_pro_details e,inv_issue_master d, pro_batch_create_mst f, fabric_sales_order_mst g where $search_field_cond b.delivery_dtls_id=c.id and c.id=e.dtls_id and c.mst_id=d.id and d.entry_form=224 and d.supplier_id=$company_id and b.status_active=1 and c.status_active=1 and e.entry_form=224 and d.status_active=1 and b.within_group=1 and c.batch_id = f.id and e.po_breakdown_id=g.id $date_cond2 and e.is_sales=1 group by b.booking_no,d.id,d.issue_number,d.company_id, d.knit_dye_source,d.knit_dye_company, d.issue_number_prefix_num, d.issue_date, d.location_id,d.buyer_id, d.insert_date,e.is_sales, f.batch_no, g.job_no, g.po_job_no";
	}
	//echo $sql;
	$result = sql_select($sql);
	$data_array= array();
	foreach ($result as $row)
	{
		$booking_no_arr[$row[csf("booking_no")]] = "'".$row[csf("booking_no")]."'";



		$data_array[$row[csf("sys_number")]]['sys_number']=$row[csf("sys_number")];
		$data_array[$row[csf("sys_number")]]['company_id']=$row[csf("company_id")];
		$data_array[$row[csf("sys_number")]]['location_id']=$row[csf("location_id")];
		$data_array[$row[csf("sys_number")]]['knit_dye_source']=$row[csf("knit_dye_source")];
		$data_array[$row[csf("sys_number")]]['is_sales']=$row[csf("is_sales")];
		$data_array[$row[csf("sys_number")]]['booking_no']=$row[csf("booking_no")];
		$data_array[$row[csf("sys_number")]]['year']=$row[csf("year")];
		$data_array[$row[csf("sys_number")]]['delivery_qty']=$row[csf("delivery_qty")];
		$data_array[$row[csf("sys_number")]]['delevery_date']=$row[csf("delevery_date")];
		$data_array[$row[csf("sys_number")]]['id']=$row[csf("id")];
		$data_array[$row[csf("sys_number")]]['batch_no'].=$row[csf("batch_no")].',';
		$data_array[$row[csf("sys_number")]]['fso_no'].=$row[csf("fso_no")].',';
		$data_array[$row[csf("sys_number")]]['po_job_no'].=$row[csf("po_job_no")].',';



	}

	if(!empty($booking_no_arr)){
		$booking_sql=sql_select("select booking_no,currency_id,exchange_rate from wo_booking_mst where booking_no in(".implode(",",$booking_no_arr).") union all select booking_no,currency_id,exchange_rate from wo_non_ord_samp_booking_mst where booking_no in(".implode(",",$booking_no_arr).") ");
		foreach ($booking_sql as $booking_row) {
			$booking_info[$booking_row[csf("booking_no")]]["currency_id"]=$booking_row[csf("currency_id")];
			$booking_info[$booking_row[csf("booking_no")]]["exchange_rate"]=$booking_row[csf("exchange_rate")];
		}
	}

	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="110">Company</th>
			<th width="120">Delivery Challan No</th>
			<th width="70">Year</th>
			<th width="100">Job No.</th>
			<th width="100">Booking No</th>
			<th width="100">FSO No</th>
			<th width="100">Batch No</th>
			<th width="90">Delivery date</th>
			<th width="80">Delivery Qty</th>
		</thead>
	</table>
	<div style="width:930px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;
			foreach ($data_array as $system_no=> $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$knit_comp="&nbsp;";
				$currency_id   = $booking_info[$row["booking_no"]]["currency_id"];
				$exchange_rate = $booking_info[$row["booking_no"]]["exchange_rate"];


				$batch_no=implode(",",array_unique(explode(",",chop($row["batch_no"],','))));
				$fso_no=implode(",",array_unique(explode(",",chop($row["fso_no"],','))));
				$po_job_no=implode(",",array_unique(explode(",",chop($row["po_job_no"],','))));

				$data_all=$row['sys_number']."_".$row['company_id']."_".$row['location_id']."_".$row['knit_dye_source']."_".$row['is_sales']."_".$currency_id."_".$exchange_rate;
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $data_all; ?>','<? echo $row['id']; ?>');">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="110" align="center"><p><? echo $company_arr[$row['company_id']]; ?></p></td>
					<td width="120" align="center"><p><? echo $row['sys_number']; ?></p></td>
					<td width="70" align="center"><p><? echo $row['year']; ?></p></td>
					<td width="100" align="center"><p><? echo $po_job_no; ?></p></td>
					<td width="100" align="center"><p><? echo $row["booking_no"]; ?></p></td>
					<td width="100" align="center"><p><? echo $fso_no; ?></p></td>
					<td width="100" align="center"><p><? echo $batch_no; ?></p></td>
					<td width="90" align="center"><? echo change_date_format($row['delevery_date']); ?></td>
					<td width="80" align="right"><p><? echo number_format($row['delivery_qty']); ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<?
	exit();
}

if($action=='finish_item_details')
{
	$data=explode("_",$data);

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id asc";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}


	if($data[2]==1) $issune_cond=" a.issue_number='".$data[0]."' and " ;
	else 			$issune_cond=" a.id='".$data[0]."' and " ;

	$sql=" SELECT a.id, a.issue_number, a.entry_form, a.company_id as knitting_company, 1 as knitting_source,a.buyer_id,a.location_id,b.body_part_id bodypart_id, d.unit_of_measure as uom, b.width_type,b.batch_id,sum(b.no_of_roll) as no_of_roll,b.fabric_shade,sum(c.quantity) delivery_qnty,c.is_sales,c.po_breakdown_id order_id,0 as product_id,d.detarmination_id determination_id,d.gsm,d.dia_width dia,d.color color_id,e.job_no,e.po_buyer,e.po_job_no,f.order_rate, e.booking_without_order
	from inv_issue_master a,inv_transaction f,inv_finish_fabric_issue_dtls b,order_wise_pro_details c,product_details_master d,fabric_sales_order_mst e
	where $issune_cond a.entry_form in (224,318) and a.id=f.mst_id and f.id=b.trans_id and b.id=c.dtls_id and c.prod_id=d.id and c.po_breakdown_id=e.id and a.status_active='1' and a.is_deleted='0' and b.status_active=1 and c.entry_form in (224,318) and c.status_active=1
	group by a.id, a.issue_number, a.entry_form, a.company_id,a.buyer_id,a.location_id,b.body_part_id, d.unit_of_measure, b.width_type, b.batch_id, b.fabric_shade, c.is_sales, c.po_breakdown_id, d.detarmination_id, d.gsm, d.dia_width, d.color, e.job_no, e.po_buyer, e.po_job_no, f.order_rate, e.booking_without_order"; //a.knit_dye_company,a.knit_dye_source, c.prod_id
	//echo $sql;
	$data_array=sql_select($sql);
	$batch_id_arr = $color_id_arr = $sales_id_arr = array(); $roll_delivery_challan="";
	foreach($data_array as $row)
	{
		$batch_id_arr[] = $row[csf("batch_id")];
		$color_id_arr[] = $row[csf("color_id")];
		$buyer_id_arr[] = $row[csf("po_buyer")];
		$order_id_arr[] = $row[csf("order_id")];
		$delivery_arr[$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("product_id")]][$row[csf("fabric_shade")]] += $row[csf("delivery_qnty")];

		if($row[csf("entry_form")]==318)
		{
			$roll_delivery_challan = $row[csf("issue_number")];
		}
	}

	if($roll_delivery_challan !="")
	{
		$pre_roll_rcv_sql="SELECT recv_number from inv_receive_master where entry_form=68 and challan_no='".$roll_delivery_challan."' and status_active=1 and is_deleted=0";
		$pre_roll_rcv_res = sql_select($pre_roll_rcv_sql);
		if(!empty($pre_roll_rcv_res))
		{
			echo "Challan Already Received.\nReceive number ".$pre_roll_rcv_res[0][csf("recv_number")];
			die;
		}
	}
	
	if(!empty($order_id_arr)){
		$salesData = array();
		$sales_sql = sql_select("select id,sales_booking_no,booking_id from fabric_sales_order_mst where id in(".implode(",",$order_id_arr).")");
		foreach($sales_sql as $row)
		{
			$salesData[$row[csf('id')]]['booking_no'] = $row[csf('sales_booking_no')];
			$salesData[$row[csf('id')]]['booking_id'] = $row[csf('booking_id')];
		}
	}

	if(!empty($batch_id_arr)){
		$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst where id in(".implode(",",$batch_id_arr).")","id","batch_no");
	}
	$color_arr=array();
	if(!empty($color_id_arr)){
		$color_arr=return_library_array( "select id, color_name from lib_color where id in(".implode(",",$color_id_arr).")",'id','color_name');
	}

	$buyer_arr=array();
	if(!empty($buyer_id_arr)){
		$buyer_arr=return_library_array( "select id, short_name from lib_buyer where id in(".implode(",",$buyer_id_arr).")",'id','short_name');
	}

	$pre_rcv_sql="SELECT c.id, b.prod_id, b.body_part_id, b.fabric_description_id, b.original_width, b.gsm, b.uom, b.width,  b.dia_width_type, b.batch_id, d.color, b.fabric_shade, sum(b.receive_qnty) as qnty from pro_finish_fabric_rcv_dtls b, inv_receive_master c, product_details_master d where b.mst_id=c.id and b.prod_id=d.id and c.receive_basis=10 and c.entry_form=37 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.booking_id='$data[0]' group by c.id, b.prod_id, b.body_part_id, b.fabric_description_id, b.uom, b.original_width, b.gsm, b.width, b.dia_width_type, b.batch_id, d.color, b.fabric_shade";

	$pre_rcv_sql = sql_select($pre_rcv_sql);
	foreach ($pre_rcv_sql as $val)
	{
		$pre_rcv_arr[$val[csf("body_part_id")]][$val[csf("fabric_description_id")]][$val[csf("original_width")]][$val[csf("gsm")]][$val[csf("uom")]][$val[csf("batch_id")]][$val[csf("fabric_shade")]][$val[csf("dia_width_type")]]['qnty'] += $val[csf("qnty")];
		$receive_mst_id_arr[$val[csf(id)]] = $val[csf(id)];
	}

	$pre_return_sql = sql_select("SELECT a.received_id, b.prod_id, b.uom, b.body_part_id, c.detarmination_id, c.gsm, c.dia_width, c.color, b.batch_id, b.fabric_shade, b.width_type, b.issue_qnty from inv_issue_master a, inv_finish_fabric_issue_dtls b, product_details_master c where a.id=b.mst_id and a.entry_form=46 and b.prod_id=c.id and b.status_active=1 and b.is_deleted=0 and a.received_id  in (".implode(',', $receive_mst_id_arr).")");

	foreach ($pre_return_sql as $val)
	{
		$pre_rcv_arr[$val[csf("body_part_id")]][$val[csf("detarmination_id")]][$val[csf("dia_width")]][$val[csf("gsm")]][$val[csf("uom")]][$val[csf("batch_id")]][$val[csf("fabric_shade")]][$val[csf("width_type")]]['ret_qnty']+= $val[csf("issue_qnty")];
	}
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="500">
		<thead>
			<th width="30">SL</th>
			<th width="70">Batch</th>
			<th>Fabric Description</th>
			<th width="40">UOM</th>
			<th width="70">Dia/ W. Type</th>
			<th width="70">Fabric Shade</th>
			<th width="60">Color</th>
			<th width="50">Qnty</th>
			<th width="50">Balance</th>
		</thead>
		<tbody id="tbl_list_search_view">
			<?
			$i=1;
			foreach($data_array as $row)
			{
				$delivery_qnty = $row[csf("delivery_qnty")];
				//$delivery_qnty = $delivery_arr[$row[csf("order_id")]][$row[csf("batch_id")]][$row[csf("product_id")]][$row[csf('fabric_shade')]];

				$recv_qnty = $pre_rcv_arr[$row[csf("bodypart_id")]][$row[csf("determination_id")]][$row[csf("dia")]][$row[csf("gsm")]][$row[csf("uom")]][$row[csf("batch_id")]][$row[csf("fabric_shade")]][$row[csf('width_type')]]['qnty'];
				$return_qnty = $pre_rcv_arr[$row[csf("bodypart_id")]][$row[csf("determination_id")]][$row[csf("dia")]][$row[csf("gsm")]][$row[csf("uom")]][$row[csf("batch_id")]][$row[csf("fabric_shade")]][$row[csf('width_type')]]['ret_qnty'];

				$net_receive_qnty = $recv_qnty-$return_qnty;
				$balance_qnty = $delivery_qnty - $net_receive_qnty;

				//if($balance_qnty >0)
				//{
					$data=$row[csf('bodypart_id')]."**".$composition_arr[$row[csf('determination_id')]]."**".$row[csf('gsm')]."**".$row[csf('dia')]."**".$row[csf('determination_id')]."**".$color_arr[$row[csf('color_id')]]."**".$row[csf('width_type')]."**".$batch_arr[$row[csf('batch_id')]]."**".$row[csf('batch_id')]."**".$row[csf('is_sales')]."**".$row[csf('color_id')]."**".$delivery_qnty."**".$row[csf("product_id")]."**".$row[csf('knitting_company')]."**".$row[csf("knitting_source")]."**".$row[csf("po_job_no")]."**".$salesData[$row[csf('order_id')]]['booking_no']."**".$row[csf("id")]."**".$row[csf("po_buyer")]."**".$buyer_arr[$row[csf("po_buyer")]]."**".$salesData[$row[csf('order_id')]]['booking_id']."**".$row[csf('order_id')]."**".$row[csf('location_id')]."**".$row[csf('uom')]."**".$row[csf('no_of_roll')]."**".$row[csf('fabric_shade')]."**".number_format($row[csf('order_rate')],2,".","")."**".$row[csf('job_no')]."**".$row[csf('booking_without_order')];

					$fab_desc = $body_part[$row[csf('bodypart_id')]].", ".$composition_arr[$row[csf('determination_id')]].", ".$row[csf('gsm')].", ".$row[csf('dia')];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $data; ?>")' style="cursor:pointer" >
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="70" <? echo $batch_dispaly; ?>><? echo $batch_arr[$row[csf('batch_id')]]; ?></td>
						<td ><? echo $fab_desc; ?></td>
						<td width="40" align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
						<td width="70" <? echo $dia_w_type_dispaly; ?> align="center"><? echo $fabric_typee[$row[csf('width_type')]]; ?></td>
						<td width="60" align="center"><? echo $fabric_shade[$row[csf('fabric_shade')]]; ?></td>
						<td width="60" align="center"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
						<td width="50" align="right"><? echo number_format($delivery_qnty,2); ?></td>
						<td width="50" align="right" title="<? echo 'receive='.$recv_qnty.', return='.$return_qnty;?>"><? echo number_format($balance_qnty,2); ?></td>
					</tr>
					<?
					$i++;
				//}
			}
			?>
		</tbody>
	</table>
	<?
	exit;
}

if($action=="delivery_challan_popup")
{
	echo load_html_head_contents("Challan Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>

		function js_set_value(data,id)
		{
			$('#hidden_challan_no').val(data);
			$('#hidden_challan_id').val(id);
			parent.emailwindow.hide();
		}

	</script>

</head>
<body>
	<div align="center" style="width:760px;" >
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:760px; margin-left:2px">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<th>Company</th>
						<th>Delivery Date Range</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="180">Please Enter Challan No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							<input type="hidden" name="hidden_challan_no" id="hidden_challan_no">
							<input type="hidden" name="hidden_challan_id" id="hidden_challan_id">
						</th>
					</thead>
					<tr class="general">
						<td align="center">
							<? echo create_drop_down( "cbo_company_id", 150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '-- Select Company --',$cbo_company_id,"",1); ?>
						</td>
						<td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
						</td>
						<td align="center">
							<?
							$search_by_arr=array(1=>"Delivery Challan");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_company_id').value+'_'+'<? echo $location_id;?>', 'create_delivery_challan_search_list_view', 'search_div', 'knit_finish_fabric_receive_by_garments_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_delivery_challan_search_list_view")
{
	$data = explode("_",$data);
	$search_string=trim($data[0]);
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$location_id =$data[5];

	if($company_id==0) { echo "Please Select Company First."; die; }
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond=" and a.delevery_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date),"yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond=" and a.delevery_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}

	if($db_type==0)
	{
		$year_field=" YEAR(a.insert_date) as year";
	}
	else if($db_type==2)
	{
		$year_field=" to_char(a.insert_date,'YYYY') as year";
	}
	else $year_field="";

	if($search_string!="") $search_field_cond=" and a.sys_number_prefix_num=$search_string ";
	if($location_id) $location_cond=" and a.location_id=$location_id ";
	$sql="select a.id, a.company_id, a.location_id, a.knitting_company, a.knitting_source, a.knitting_location, a.sys_number, a.delevery_date, sum(b.current_delivery) as delivery_qty from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b where a.id=b.mst_id and a.entry_form=54 and b.is_sales=0 and a.company_id=$company_id $location_cond $search_field_cond $date_cond group by a.id, a.company_id, a.location_id, a.knitting_company, a.knitting_source, a.knitting_location, a.sys_number, a.delevery_date";
	//echo $sql;
	$result = sql_select($sql);

	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="140">Company</th>
			<th width="140">Delivery Challan No</th>
			<th width="70">Year</th>
			<th>Delivery date</th>
			<th width="140">Delivery Qty</th>
		</thead>
	</table>
	<div style="width:740px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;
			foreach ($result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$knit_comp="&nbsp;";

				$data_all=$row[csf('sys_number')]."_".$row[csf('company_id')]."_".$row[csf('location_id')]."_".$row[csf('knitting_source')]."_".$row[csf('knitting_company')]."_".$row[csf('knitting_location')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $data_all; ?>','<? echo $row[csf('id')]; ?>');">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="140" align="center"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
					<td width="140" align="center"><p><? echo $row[csf('sys_number')]; ?></p></td>
					<td width="70" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td align="center"><? echo change_date_format($row[csf('delevery_date')]); ?></td>
					<td width="140" align="right"><p><? echo number_format($row[csf('delivery_qty')]); ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<?
	exit();
}

if($action=='delivery_finish_item_details')
{
	$data=explode("_",$data);

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id order by b.id asc";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}

	$receive_sql = sql_select("select a.id, a.recv_number, b.batch_id, b.body_part_id, b.fabric_description_id, b.gsm, b.fabric_shade, b.dia_width_type, b.uom, sum(b.receive_qnty) as qnty,b.width from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id = b.mst_id and a.entry_form =37 and a.receive_basis =16 and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.booking_id = '$data[0]' group by a.id, a.recv_number, b.batch_id, b.body_part_id, b.fabric_description_id, b.gsm, b.fabric_shade, b.dia_width_type, b.uom,b.width");

	foreach ($receive_sql as $val)
	{
		$receive_arr[$val[csf("batch_id")]][$val[csf("body_part_id")]][$val[csf("fabric_description_id")]][$val[csf("gsm")]][$val[csf("fabric_shade")]][$val[csf("dia_width_type")]][$val[csf("uom")]][$val[csf("width")]] += $val[csf("qnty")];
	}


	$sql ="select a.knitting_company, a.knitting_source, a.knitting_location, a.company_id,b.mst_id,b.sys_dtls_id, b.batch_id, b.bodypart_id, b.determination_id, b.gsm, b.dia, c.uom,b.color_id, b.width_type, b.fabric_shade, sum(b.current_delivery) as delivery_qnty, c.rate, b.product_id, b.order_id, 0 as is_sales from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, pro_finish_fabric_rcv_dtls c where a.id=b.mst_id and b.sys_dtls_id= c.id and b.is_sales=0 and a.id='$data[0]' group by a.knitting_company, a.knitting_source, a.knitting_location, a.company_id, b.mst_id, b.sys_dtls_id, b.batch_id, b.bodypart_id, b.determination_id, b.gsm, b.dia, c.uom, b.color_id, b.width_type, b.fabric_shade, c.rate, b.product_id, b.order_id";


	$data_array=sql_select($sql);
	$batch_id_arr = $color_id_arr = $sales_id_arr = array();
	foreach($data_array as $row)
	{
		if($receive_arr[$row[csf("batch_id")]][$row[csf("bodypart_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("fabric_shade")]][$row[csf("width_type")]][$row[csf("uom")]][$row[csf("dia")]] == "")
		{
			$batch_id_arr[$row[csf("batch_id")]] = $row[csf("batch_id")];
			$color_id_arr[$row[csf("color_id")]] = $row[csf("color_id")];
			$order_id_arr[$row[csf("order_id")]] = $row[csf("order_id")];

			$delivery_str = $row[csf("bodypart_id")]."*".$row[csf("batch_id")]."*".$row[csf("product_id")]."*".$row[csf("width_type")]."*".$row[csf("fabric_shade")]."*".$row[csf("rate")];

			$delivery_arr[$delivery_str]["knitting_company"] = $row[csf("knitting_company")];
			$delivery_arr[$delivery_str]["knitting_source"] = $row[csf("knitting_source")];
			$delivery_arr[$delivery_str]["knitting_location"] = $row[csf("knitting_location")];
			$delivery_arr[$delivery_str]["determination_id"] = $row[csf("determination_id")];
			$delivery_arr[$delivery_str]["gsm"] = $row[csf("gsm")];
			$delivery_arr[$delivery_str]["dia"] = $row[csf("dia")];
			$delivery_arr[$delivery_str]["uom"] = $row[csf("uom")];
			$delivery_arr[$delivery_str]["color_id"] = $row[csf("color_id")];
			$delivery_arr[$delivery_str]["product_id"] = $row[csf("product_id")];
			$delivery_arr[$delivery_str]["batch_id"] = $row[csf("batch_id")];
			$delivery_arr[$delivery_str]["width_type"] = $row[csf("width_type")];
			$delivery_arr[$delivery_str]["fabric_shade"] = $row[csf("fabric_shade")];
			$delivery_arr[$delivery_str]["bodypart_id"] = $row[csf("bodypart_id")];
			$delivery_arr[$delivery_str]["rate"] = $row[csf("rate")];
			$delivery_arr[$delivery_str]["delivery_qnty"] += $row[csf("delivery_qnty")];
			$delivery_arr[$delivery_str]["order_ids"] .= $row[csf("order_id")].",";
			$delivery_arr[$delivery_str]["order_qnty"].= $row[csf("order_id")]."_".$row[csf("delivery_qnty")].",";
		}
	}

    $batch_id_arr = array_filter($batch_id_arr);
    $all_batch_ids = implode(",", $batch_id_arr);
    $batch_cond="";	$batchCond="";
    if($db_type==2 && count($batch_id_arr)>999)
    {
    	$all_batch_id_arr_chunk=array_chunk($batch_id_arr,999);
    	foreach($all_batch_id_arr_chunk as $chunk_arr)
    	{
    		$chunk_arr_value=implode(",",$chunk_arr);
    		$batchCond.=" a.id in($chunk_arr_value) or ";
    	}

    	$batch_cond.=" and (".chop($batchCond,'or ').")";
    }
    else
    {
    	$batch_cond=" and a.id in($all_batch_ids)";
    }

	if(!empty($batch_id_arr))
	{
		$batch_sql = sql_select("select a.id, a.batch_no, a.booking_no_id, a.booking_no, a.booking_without_order from pro_batch_create_mst a where a.status_active =1 $batch_cond group by a.id, a.batch_no, a.booking_no_id, a.booking_no, a.booking_without_order");

		foreach ($batch_sql as $val)
		{
			$batch_arr[$val[csf('id')]]['batch_no'] = $val[csf('batch_no')];
			$batch_arr[$val[csf('id')]]['booking_id'] = $val[csf('booking_no_id')];
			$batch_arr[$val[csf('id')]]['booking_no'] = $val[csf('booking_no')];
			$batch_arr[$val[csf('id')]]['booking_without_order'] = $val[csf('booking_without_order')];
		}
	}

	$order_id_arr = array_filter($order_id_arr);
	if(!empty($order_id_arr))
	{
		$batch_sql = sql_select("select  c.id, c.po_number, c.job_no_mst, d.buyer_name as buyer_id, e.buyer_name from  wo_po_break_down c, wo_po_details_master d, lib_buyer e where  c.job_id= d.id  and d.buyer_name = e.id and c.id in (".implode(',', $order_id_arr).") group by c.id, c.po_number, c.job_no_mst, d.buyer_name, e.buyer_name");
		foreach ($batch_sql as $val)
		{
			$po_arr[$val[csf('id')]]['buyer_id'] = $val[csf('buyer_id')];
			$po_arr[$val[csf('id')]]['buyer_name'] = $val[csf('buyer_name')];
			$po_arr[$val[csf('id')]]['job_no'] = $val[csf('job_no_mst')];
			$po_arr[$val[csf('id')]]['po_number'] = $val[csf('po_number')];
		}
	}

	$color_arr=array();
	if(!empty($color_id_arr)){
		$color_arr=return_library_array( "select id, color_name from lib_color where id in(".implode(",",$color_id_arr).")",'id','color_name');
	}

	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="450">
		<thead>
			<th width="30">SL</th>
			<th width="70">Batch</th>
			<th>Fabric Description</th>
			<th width="40">UOM</th>
			<th width="70">Dia/ W. Type</th>
			<th width="70">Fabric Shade</th>
			<th width="60">Color</th>
			<th width="50">Qnty</th>
		</thead>
		<tbody>
			<?
			$i=1;
			foreach($delivery_arr as $str_data => $row)
			{
				$po_id_array = array_filter(explode(",",chop($row['order_ids'],",")));
				$order_ids = implode(",",$po_id_array);
				$po_numbers="";
				foreach ($po_id_array as $val)
				{
					//N.B. assume job will not mix in batch
					$buyer_id = $po_arr[$val]['buyer_id'];
					$buyer_name = $po_arr[$val]['buyer_name'];
					$job_no = $po_arr[$val]['job_no'];
					if($po_numbers=="") $po_numbers= $po_arr[$val]['po_number']; else $po_numbers .= ",". $po_arr[$val]['po_number'];
				}

				$data=$row['bodypart_id']."**".$composition_arr[$row['determination_id']]."**".$row['gsm']."**".$row['dia']."**".$row['determination_id']."**".$color_arr[$row['color_id']]."**".$row['width_type']."**".$batch_arr[$row['batch_id']]['batch_no'] ."**".$row['batch_id']."**".$row['is_sales']."**".$row['color_id']."**".$row['delivery_qnty']."**".$row["product_id"]."**".$row['knitting_company']."**".$row['knitting_source']."**".$job_no."**".$batch_arr[$row['batch_id']]['booking_no']."**".$row[csf("id")]."**".$buyer_id."**".$buyer_name."**".$batch_arr[$row['batch_id']]['booking_id']."**".$order_ids."**".$row['knitting_location']."**".$row['uom']."**".$row['no_of_roll']."**".$row[csf('fabric_shade')]."**".number_format($row['rate'],2,".","")."**".$po_numbers."**".$batch_arr[$row['batch_id']]['booking_without_order']."**".chop($row["order_qnty"],",");

				$fab_desc = $body_part[$row['bodypart_id']].", ".$composition_arr[$row['determination_id']].", ".$row['gsm'].", ".$row['dia'];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $data; ?>")' style="cursor:pointer" >
					<td width="30" align="center"><? echo $i; ?></td>
					<td width="70" <? echo $batch_dispaly; ?>><? echo $batch_arr[$row['batch_id']]['batch_no'] ; ?></td>
					<td ><? echo $fab_desc; ?></td>
					<td width="40" align="center"><? echo $unit_of_measurement[$row['uom']]; ?></td>
					<td width="70" <? echo $dia_w_type_dispaly; ?> align="center"><? echo $fabric_typee[$row['width_type']]; ?></td>
					<td width="60" align="center"><? echo $fabric_shade[$row['fabric_shade']]; ?></td>
					<td width="60" align="center"><? echo $color_arr[$row['color_id']]; ?></td>
					<td width="50" align="right"><? echo number_format($row['delivery_qnty'],2); ?></td>
				</tr>
				<?
				$i++;
			}
			?>
		</tbody>
	</table>
	<?
	exit;
}

if ($action=="finish_fabric_receive_print_3")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r($data);
	$sql="SELECT id, recv_number, company_id, receive_basis, booking_id, booking_no, booking_without_order, store_id, location_id, knitting_source, knitting_company, receive_date, challan_no, store_id from inv_receive_master where id='$data[1]' and company_id='$data[0]' and item_category=2 and entry_form=37";

	$dataArray=sql_select($sql);
	$rec_basis=$dataArray[0][csf("receive_basis")];
	$country_arr=return_library_array( "SELECT id, country_name from  lib_country", "id", "country_name");
	$storeArr=return_library_array( "SELECT id, store_name from  lib_store_location", "id", "store_name");
	$location_library=return_library_array( "SELECT id, location_name from lib_location", "id", "location_name");
	$batch_color=return_library_array( "SELECT id, color_id from pro_batch_create_mst", "id", "color_id");
	$machineArr=return_library_array( "SELECT id, machine_no from lib_machine_name", "id", "machine_no");

	$company_arr = return_library_array("SELECT id, company_name from lib_company","id","company_name");
	$buyer_arr = return_library_array("SELECT id, buyer_name from lib_buyer","id","buyer_name");

	$supplier_arr = return_library_array("SELECT id, supplier_name from lib_supplier","id","supplier_name");
	$batch_arr = return_library_array("SELECT id, batch_no from pro_batch_create_mst","id","batch_no");
	$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active=1",'id','color_name');


	$product_array=array();
	$product_sql = sql_select("SELECT id, product_name_details, gsm, dia_width from product_details_master where item_category_id=2 and status_active=1 and is_deleted=0");
	foreach($product_sql as $row)
	{
		$product_array[$row[csf("id")]]['product_name_details']=$row[csf("product_name_details")];
		$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
	}

	// CRM ID: 24589
	/*$po_array=array();
	$po_sql = sql_select("SELECT id, po_number, file_no, grouping as ref from wo_po_break_down where  status_active=1 and is_deleted=0");
	foreach($po_sql as $row)
	{
		$po_array[$row[csf("id")]]['po']=$row[csf("po_number")];
		$po_array[$row[csf("id")]]['file']=$row[csf("file_no")];
		$po_array[$row[csf("id")]]['ref']=$row[csf("ref")];
	}*/


	?>
	<div style="width:1170px;">
		<table width="1140" cellspacing="0" align="right">
			<tr>
				<td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_arr[$data[0]]; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">
					<?
					$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
					foreach ($nameArray as $result)
					{
						?>
						Plot No: <? echo $result['plot_no']; ?>
						Level No: <? echo $result['level_no']?>
						Road No: <? echo $result['road_no']; ?>
						Block No: <? echo $result['block_no'];?>
						City No: <? echo $result['city'];?>
						Zip Code: <? echo $result['zip_code']; ?>
						Province No: <?php echo $result['province'];?>
						Country: <? echo $country_arr[$result['country_id']]; ?><br>
						Email Address: <? echo $result['email'];?>
						Website No: <? echo $result['website'];
					}
					?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><u>Textile <? echo $data[2]; ?> Challan</u></strong></td>
			</tr>
			<tr>
				<td width="120"><strong>Company</strong></td>
				<td width="175px"><? echo $company_arr[$dataArray[0][csf('company_id')]]; ?></td>
				<td width="120"><strong>Receive ID :</strong></td>
				<td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
				<td width="130"><strong>Receive Basis:</strong></td>
				<td width="175px"><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
				<td width="125"><strong>Receive Date:</strong></td>
				<td width="175px"><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Challan No:</strong></td>
				<td><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>WO/PI/Production:</strong></td>
				<td><? echo $sales_ords= $dataArray[0][csf('booking_no')]; ?></td>
				<td><strong>Store Name:</strong></td>
				<td><? echo $storeArr[$dataArray[0][csf('store_id')]]; ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><strong>Dyeing Source:</strong></td>
				<td><? echo $knitting_source[$dataArray[0][csf('knitting_source')]]; ?></td>
				<td><strong>Dyeing Company:</strong></td>
				<td><p><? if ($dataArray[0][csf('knitting_source')]==1) echo $company_arr[$dataArray[0][csf('knitting_company')]]; else if ($dataArray[0][csf('knitting_source')]==3) echo $supplier_arr[$dataArray[0][csf('knitting_company')]]; ?></p></td>
				<td><strong>Location:</strong></td>
				<td><? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr style=" height:20px">
				<td  colspan="3" id="barcode_img_id"></td>


			</tr>
			<tr style=" height:20px">
				<td colspan="8">&nbsp;</td>
			</tr>
		</table>

		<div style="width:100%; margin-top:20px;">
			<table align="right" cellspacing="0" cellpadding="0" width="1140"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th width="25">SL</th>
						<th width="100">Buyer</th>
						<th width="100">Style No</th>
						<th width="110">Booking No</th>
						<th width="120">Sales Order No</th>
						<th width="140">Fabric Des.</th>
						<th width="30">GSM</th>
						<th width="60">Dia/Width</th>
						<th width="70">Batch No</th>
						<th width="50">Color</th>
						<th width="60">Fabric Shade</th>
						<th width="30">UOM</th>
						<th width="60">Receive Qty</th>
						<th width="60">Reject Rcv Qty</th>
						<th width="60">No.of Roll</th>
						<th>Remarks</th>

					</tr>

				</thead>
				<tbody>
					<?

					$sql_dtls="SELECT id, batch_id, prod_id, receive_qnty, reject_qty, no_of_roll, machine_no_id, rack_no, shelf_no, order_id, buyer_id,fabric_shade,uom,booking_no from pro_finish_fabric_rcv_dtls where mst_id='$data[1]' and status_active=1 and is_deleted= 0";

					$sql_result= sql_select($sql_dtls);
					$order_ids_arr=array();

					foreach($sql_result as $v)
					{
						$order_ids_arr[$v[csf("order_id")]]=$v[csf("order_id")];
					}
					$ids=implode(",", $order_ids_arr);
					if(!$ids)$ids=0;

					$sales_order_arr=array();
					if($rec_basis==10)
					{
						/*$sales_order_sql="SELECT  id,sales_booking_no,job_no,style_ref_no  from fabric_sales_order_mst where id in($ids)";
						foreach(sql_select($sales_order_sql) as $values)
						{
							$sales_order_arr[$values[csf("id")]]["booking"]=$values[csf("sales_booking_no")];
							$sales_order_arr[$values[csf("id")]]["sales_order_no"]=$values[csf("job_no")];
							$sales_order_arr[$values[csf("id")]]["style_ref_no"]=$values[csf("style_ref_no")];
						}*/

						// CRM ID: 24589, As per discuss with Tofael vai
						$po_sql="SELECT b.id, c.buyer_name, c.style_ref_no,a.booking_no, b.po_number
						from wo_booking_dtls a, wo_po_break_down b, wo_po_details_master c
						where b.id in($ids) and a.po_break_down_id=b.id and b.job_id=c.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.booking_type in(1,4) and a.is_short in(1,2)
						group by b.id, c.buyer_name, c.style_ref_no,a.booking_no, b.po_number";
						$po_sql_result=sql_select($po_sql);
						foreach($po_sql_result as $row)
						{
							$po_array[$row[csf("id")]]['po']=$row[csf("po_number")];
							$po_array[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
							$po_array[$row[csf("id")]]['booking']=$row[csf("booking_no")];
							$po_array[$row[csf("id")]]['file']=$row[csf("file_no")];
							$po_array[$row[csf("id")]]['ref']=$row[csf("ref")];
						}

					}
					else if($rec_basis==14)
					{
						$within_group=return_field_value("within_group","fabric_sales_order_mst","job_no='$sales_ords' and status_active=1 ");
						$sales_booking=return_field_value("sales_booking_no","fabric_sales_order_mst","job_no='$sales_ords' and status_active=1 ");
						if($within_group==1)
						{
							$sales_order_sql="SELECT  b.id,a.job_no,a.style_ref_no  from wo_po_details_master a,wo_po_break_down b  where a.id=b.job_id and b.id in($ids)";
							foreach(sql_select($sales_order_sql) as $values)
							{

								$sales_order_arr[$values[csf("id")]]["sales_order_no"]=$values[csf("job_no")];
								$sales_order_arr[$values[csf("id")]]["style_ref_no"]=$values[csf("style_ref_no")];
							}




						}
						else
						{
							$sales_order_sql="SELECT  id,sales_booking_no,job_no,style_ref_no  from fabric_sales_order_mst where id in($ids)";
							foreach(sql_select($sales_order_sql) as $values)
							{
								$sales_order_arr[$values[csf("id")]]["booking"]=$values[csf("sales_booking_no")];
								$sales_order_arr[$values[csf("id")]]["sales_order_no"]=$values[csf("job_no")];
								$sales_order_arr[$values[csf("id")]]["style_ref_no"]=$values[csf("style_ref_no")];
							}

						}
					}
					else if($rec_basis==11 && $dataArray[0][csf('knitting_source')]==3)
					{

						// for service booking and source Outbound/ issue id 11748
						$po_sql="SELECT b.id, c.buyer_name, c.style_ref_no,a.booking_no, b.po_number
						from wo_booking_dtls a, wo_po_break_down b, wo_po_details_master c
						where b.id in($ids) and a.po_break_down_id=b.id and b.job_id=c.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.booking_type in(3) 
						group by b.id, c.buyer_name, c.style_ref_no,a.booking_no, b.po_number";
						$po_sql_result=sql_select($po_sql);
						foreach($po_sql_result as $row)
						{
							$po_array[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
						}
					}


					$i=1;
					foreach($sql_result as $row)
					{
						if ($i%2==0)$bgcolor="#E9F3FF";	 else $bgcolor="#FFFFFF";

						$po_no=array_unique(explode(",",$row[csf("order_id")]));
						$order_nos=""; $style_ref_nos=""; $booking_nos=""; $file_nos=""; $ref_nos="";
						foreach($po_no as $val)
						{
							if ($order_nos=="") $order_nos=$po_array[$val]['po']; else $order_nos.=", ".$po_array[$val]['po'];
							if ($style_ref_nos=="") $style_ref_nos=$po_array[$val]['style']; else $style_ref_nos.=", ".$po_array[$val]['style'];
							if ($booking_nos=="") $booking_nos=$po_array[$val]['booking']; else $booking_nos.=", ".$po_array[$val]['booking'];
							if ($file_nos=="") $file_nos=$po_array[$val]['file']; else $file_nos.=", ".$po_array[$val]['file'];
							if ($ref_nos=="") $ref_nos=$po_array[$val]['ref']; else $ref_nos.=", ".$po_array[$val]['ref'];
						}
						?>

						<tr>
							<td width="25" align="center"><? echo $i; ?></td>
							<td width="100" align="center"><? echo $buyer_arr[$row[csf("buyer_id")]]; ?></td>
							<td width="100" align="center"><? echo $style_ref_nos;//$sales_order_arr[$row[csf("order_id")]]["style_ref_no"];?></td>
							<td width="110" align="center"><?
							if($within_group==1)
							{
								echo $sales_booking;
							}
							else
							{
								if ($rec_basis==11 && $dataArray[0][csf('knitting_source')]==3) 
								{
									echo $dataArray[0][csf('booking_no')];
								}
								else
								{
									echo $row[csf("booking_no")];//$booking_nos;//$sales_order_arr[$row[csf("order_id")]]["booking"];
								}
							}?></td>
							<td width="120" align="center"><?
							if($rec_basis==10)
							{
								echo $order_nos;//$sales_order_arr[$row[csf("order_id")]]["sales_order_no"];
							}
							else
							{
								if ($rec_basis!=11 && $dataArray[0][csf('knitting_source')]!=3) 
								{
									echo $dataArray[0][csf('booking_no')];
								}
							}?></td>
							<td width="140" align="center"><? echo $product_array[$row[csf("prod_id")]]['product_name_details']; ?></td>
							<td width="30" align="center"><? echo $product_array[$row[csf("prod_id")]]['gsm']; ?></td>
							<td width="60" align="center"><? echo $product_array[$row[csf("prod_id")]]['dia_width']; ?></td>
							<td width="70" align="center"><? echo $batch_arr[$row[csf("batch_id")]]; ?></td>
							<td width="50" align="center"><? echo $color_arr[$batch_color[$row[csf("batch_id")]]]; ?></td>
							<td width="60" align="center"><? echo $fabric_shade[$row[csf("fabric_shade")]];?></td>
							<td width="30" align="center"><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
							<td width="60" align="right"><? echo number_format($row[csf("receive_qnty")],2); ?></td>
							<td width="60"  align="right"><? echo number_format($row[csf("reject_qty")],2); ?></td>
							<td width="60"  align="right"><? echo number_format($row[csf("no_of_roll")]); ?></td>
							<td><? echo $row[csf("remarks")]; ?></td>
						</tr>


						<? $i++;
						$totalRecQnty +=$row[csf("receive_qnty")];
						$totalRejQnty +=$row[csf("reject_qty")];
						$totalRoll +=$row[csf("no_of_roll")];
					} ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="12" align="right"><strong>Total :</strong></td>
						<td align="right"><?php echo number_format($totalRecQnty,2); ?></td>
						<td align="right"><?php echo number_format($totalRejQnty,2); ?></td>
						<td align="right"><?php echo number_format($totalRoll,2); ?></td>
						<td align="right">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
			<br>
			<?
			echo signature_table(149, $data[0], "1140px");
			?>
		</div>
	</div>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
			// alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();

			var settings = {
				output:renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize:5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {code:value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $data[3]; ?>');
	</script>
	<?
	exit();
}

if ($action=="finish_fabric_receive_print_4")
{
	extract($_REQUEST);
	$data=explode('*',$data);

	$payment_yes_no=array(0=>"Yes",1=>"No");

	$sql="select id, recv_number, company_id, receive_basis, booking_id, booking_no, booking_without_order, store_id, location_id, knitting_source, knitting_company, receive_date, challan_no, store_id, lc_sc_no, exchange_rate, audit_by, audit_date, is_audited from inv_receive_master where id='$data[1]' and company_id='$data[0]' and item_category=2 and entry_form=37";

	$dataArray=sql_select($sql);
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name");
	$storeArr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");
	$location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name");

	$company_arr = return_library_array("select id, company_name from lib_company","id","company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");

	if ($db_type==0)
	{
		$sql_dtls="SELECT a.id, a.trans_id, a.batch_id, a.prod_id, a.receive_qnty, a.reject_qty, a.no_of_roll,  a.buyer_id, a.uom, a.rate as amountothers, a.amount as amountintk, a.fabric_shade, b.order_rate, group_concat(c.po_breakdown_id) as order_id, a.payment_over_rcv_status
		from pro_finish_fabric_rcv_dtls a, inv_transaction b left join order_wise_pro_details c on b.id = c.trans_id and c.entry_form = 37 and c.quantity>0 and c.status_active=1
		where a.trans_id=b.id and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id, a.trans_id, a.batch_id, a.prod_id, a.receive_qnty, a.reject_qty, a.no_of_roll, a.buyer_id, a.uom, a.rate, a.amount, a.fabric_shade, b.order_rate, a.payment_over_rcv_status";
	}
	else
	{
		$sql_dtls="SELECT a.id, a.trans_id, a.batch_id, a.prod_id, a.fabric_description_id, a.receive_qnty, a.reject_qty, a.no_of_roll, a.buyer_id, a.uom, a.rate as amountothers, a.amount as amountintk, a.fabric_shade, a.remarks, b.order_rate, listagg(c.po_breakdown_id,',') within group (order by c.po_breakdown_id) as order_id, a.payment_over_rcv_status
		from pro_finish_fabric_rcv_dtls a, inv_transaction b left join order_wise_pro_details c on b.id = c.trans_id and c.entry_form = 37 and c.quantity>0 and c.status_active=1
		where a.trans_id=b.id and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id, a.trans_id, a.batch_id, a.prod_id, a.fabric_description_id, a.receive_qnty, a.reject_qty, a.no_of_roll, a.buyer_id, a.uom, a.rate, a.amount, a.fabric_shade, a.remarks, b.order_rate, a.payment_over_rcv_status";
	}
	// echo $sql_dtls;
	$sql_result= sql_select($sql_dtls);

	foreach ($sql_result as $val)
	{
		$all_po_arr[$val[csf("order_id")]] = $val[csf("order_id")];
		$all_batch_ids .= $val[csf("batch_id")].",";
		$all_prod_ids .= $val[csf("prod_id")].",";
	}
	$all_po_ids = implode(",", array_filter($all_po_arr));

	if(!empty($all_po_ids))
	{
		$po_array=array();
		$po_sql = sql_select("select a.id, a.po_number, a.file_no, a.grouping as ref,b.job_no,b.style_ref_no from wo_po_break_down a,wo_po_details_master b where  a.status_active=1 and a.is_deleted=0 and a.job_id=b.id and a.id in ($all_po_ids)");
		foreach($po_sql as $row)
		{
			$po_array[$row[csf("id")]]['po']=$row[csf("po_number")];
			$po_array[$row[csf("id")]]['file']=$row[csf("file_no")];
			$po_array[$row[csf("id")]]['ref']=$row[csf("ref")];
			$po_array[$row[csf("id")]]['job']=$row[csf("job_no")];
			$po_array[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		}
	}

	$all_batch_ids = chop($all_batch_ids,",");
	if(!empty($all_batch_ids))
	{
		$batch_sql = sql_select("select id, batch_no, color_id from pro_batch_create_mst where id in ($all_batch_ids)");
		foreach ($batch_sql as $val)
		{
			$batch_arr[$val[csf("id")]] = $val[csf("batch_no")];
			$batch_color[$val[csf("id")]] = $val[csf("color_id")];
		}
		$color_ids = implode(",", $batch_color);
		if(!empty($color_ids))
		{
			$color_arr=return_library_array( "select id, color_name from lib_color where id in ($color_ids)",'id','color_name');
		}
	}

	$all_prod_ids = chop($all_prod_ids,",");
	if(!empty($all_prod_ids))
	{
		$product_array=array();
		$product_sql = sql_select("select id, product_name_details, gsm, dia_width from product_details_master where item_category_id=2 and status_active=1 and is_deleted=0 and id in ($all_prod_ids)");
		foreach($product_sql as $row)
		{
			$product_array[$row[csf("id")]]['product_name_details']=$row[csf("product_name_details")];
			$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
			$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
		}
	}

	if($data[4]==1){
			$booking_qty_sql="SELECT c.batch_id, c.prod_id, c.booking_id, d.construction, d.copmposition, d.gsm_weight, d.dia_width, d.fin_fab_qnty 
			FROM  inv_receive_master a, pro_finish_fabric_rcv_dtls c, wo_booking_dtls d 
			WHERE a.id=c.mst_id and c.booking_no=d.booking_no and a.entry_form = 37 and a.recv_number='$data[3]' AND a.status_active = 1 AND a.is_deleted = 0  AND c.status_active = 1 AND c.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 and a.receive_basis=1
			";
		}
		else{
			$booking_qty_sql="SELECT c.batch_id, c.prod_id, b.booking_id, d.construction, d.copmposition, d.gsm_weight, d.dia_width, d.fin_fab_qnty
			from pro_finish_fabric_rcv_dtls c, inv_receive_master a, inv_issue_master b, wo_booking_dtls d
			where c.mst_id=a.id and a.booking_id=b.id and b.booking_id=d.booking_mst_id
			and a.entry_form=37 and b.entry_form=224 and a.id=$data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0";
		}
	
	// echo $booking_qty_sql;
	$booking_qty_sql_rst=sql_select($booking_qty_sql);
	$booking_qty_arr=array();
	foreach ($booking_qty_sql_rst as $key => $row) 
	{
		//echo $row[csf("batch_id")].']['.$row[csf("construction")].']['.$row[csf("copmposition")].']['.$row[csf("gsm_weight")].']['.$row[csf("dia_width")].'<br>';
		$booking_qty_arr[$row[csf("batch_id")]][$row[csf("construction")]][$row[csf("copmposition")]][$row[csf("gsm_weight")]][$row[csf("dia_width")]]+=$row[csf("fin_fab_qnty")];
	}
	// echo "<pre>";print_r($booking_qty_arr);die;

	/*$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id order by b.id asc";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}*/

	?>
	<div style="width:1230px; font-family: Arial Narrow;">
		<table width="1200" cellspacing="0" align="right">
			<tr>
				<td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_arr[$data[0]]; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">
					<?
					$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
					foreach ($nameArray as $result)
					{
						echo $result[csf('plot_no')].', ';
						echo $result[csf('level_no')].', ';
						echo $result[csf('road_no')].', ';
						echo $result[csf('block_no')].', ';
						echo $result[csf('city')].', ';
						echo $result[csf('zip_code')].', ';
						echo $result[csf('province')].', ';
						echo $country_arr[$result[csf('country_id')]].'<br>';
						echo $result[csf('email')].', ';
						echo $result[csf('website')];
					}
					?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><u><? echo $data[2]; ?> Challan</u></strong></td>
			</tr>
			<tr>
				<td width="120"><strong>Receive ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
				<td width="130"><strong>Receive Basis:</strong></td> <td width="175px"><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
				<td width="125"><strong>Receive Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Company:</strong></td><td><p><? if ($dataArray[0][csf('knitting_source')]==1) echo $company_arr[$dataArray[0][csf('knitting_company')]]; else if ($dataArray[0][csf('knitting_source')]==3) echo $supplier_arr[$dataArray[0][csf('knitting_company')]]; ?></p></td>
				<td><strong>WO/PI/Production:</strong></td><td><? echo $dataArray[0][csf('booking_no')]; ?></td>
				<td><strong>Store Name:</strong></td><td><? echo $storeArr[$dataArray[0][csf('store_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Source:</strong></td><td><? echo $knitting_source[$dataArray[0][csf('knitting_source')]]; ?></td>
				<td><strong>Location:</strong></td><td><? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
				<td><strong>Challan No:</strong></td><td><? echo $dataArray[0][csf('challan_no')]; ?></td>
			</tr>
			<tr>
				<td><strong>LC/SC NO:</td><td><?= $dataArray[0][csf('lc_sc_no')]; ?></td>
				<td><strong>Exchange Rate:</td><td><?= $dataArray[0][csf('exchange_rate')]; ?></td>
			</tr>
			<tr style=" height:20px">
				<td  colspan="3" id="barcode_img_id"></td>
			</tr>
			<tr style=" height:20px">
				<td colspan="6">&nbsp;</td>
			</tr>
		</table>

		<div style="width:108%; margin-top:20px;">
			<table align="left" cellspacing="0" cellpadding="0" width="1330"  border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="60">Batch No</th>
					<th width="110">Buyer Name</th>
					<th width="70">Color</th>
					<th width="70">IR/IB</th>
					<th width="75">Job No</th>
					<th width="75">Style No</th>
					<th width="100">Order No</th>
					<th width="150">Fabric Des.</th>
					<th width="50">GSM</th>
					<th width="60">Dia/Width</th>
					<th width="60">UOM</th>
					<th width="80">Booking Qty</th>
					<th width="80">QC Pass Qty</th>
					<th width="60">Reject Qty</th>
					<th width="60">Roll</th>
					<th width="60">Payment For</th>
					<th width="50">Rate</th>
					<th width="50">Amount</th>
					<th width="100">Amount(Tk)</th>
					<th width="50">Fab. Shade</th>
					<th>Remarks</th>
				</thead>
				<tbody>
					<?
					$i=1;
					foreach($sql_result as $row)
					{
						if ($i%2==0)$bgcolor="#E9F3FF";	 else $bgcolor="#FFFFFF";

						// if($row[csf('fabric_description_id')]==0 || $row[csf('fabric_description_id')]=="")
						// 	$fabric_desc=$fabric_desc_arr[$row[csf('prod_id')]];
						// else
						// 	$fabric_desc=$composition_arr[$row[csf('fabric_description_id')]];
						//$cons_comp=explode(",", $fabric_desc);
						//echo $cons_comp[0].'='.$cons_comp[1];
						$fabric_desc=$product_array[$row[csf("prod_id")]]['product_name_details'];
						$cons_comp=explode(",", $fabric_desc);
						//echo $cons_comp[0].'='.$cons_comp[1];
						$gsm=$product_array[$row[csf("prod_id")]]['gsm'];
						$dia=$product_array[$row[csf("prod_id")]]['dia_width'];

						$title=$row[csf("batch_id")].']['.$cons_comp[0].']['.trim($cons_comp[1]).']['.$gsm.']['.$dia;
						$booking_qty=$booking_qty_arr[$row[csf("batch_id")]][$cons_comp[0]][trim($cons_comp[1])][$gsm][$dia];

						$po_no=array_unique(explode(",",$row[csf("order_id")]));
						$order_nos="";	$file_nos="";	$ref_nos=""; $job_nos=""; $style_nos="";
						foreach($po_no as $val)
						{
							if ($order_nos=="") $order_nos=$po_array[$val]['po']; else $order_nos.=", ".$po_array[$val]['po'];
							if ($file_nos=="") $file_nos=$po_array[$val]['file']; else $file_nos.=", ".$po_array[$val]['file'];
							if ($ref_nos=="") $ref_nos=$po_array[$val]['ref']; else $ref_nos.=", ".$po_array[$val]['ref'];

							if ($job_nos=="") $job_nos=$po_array[$val]['job']; else $job_nos.=",".$po_array[$val]['job'];
							if ($style_nos=="") $style_nos=$po_array[$val]['style']; else $style_nos.=",".$po_array[$val]['style'];
						}

						$job_nos = implode(",",array_filter(array_unique(explode(",", $job_nos))));
						$style_nos = implode(",",array_filter(array_unique(explode(",", $style_nos))));
						$ref_nos = implode(",",array_filter(array_unique(explode(",", $ref_nos))));

						if($row[csf("payment_over_rcv_status")]==1)
						{
							$order_rate =0;
							$amount = 0;
							$amount_taka=0;
						}
						else
						{
							$order_rate = $row[csf("order_rate")];
							$amount = $row[csf("order_rate")]*$row[csf("receive_qnty")];
							$amount_taka=$row[csf("amountintk")];
						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $i; ?></td>
							<td><? echo $batch_arr[$row[csf("batch_id")]]; ?></td>
							<td><? echo $buyer_arr[$row[csf("buyer_id")]]; ?></td>
							<td><? echo $color_arr[$batch_color[$row[csf("batch_id")]]]; ?></td>
							<td><? echo $ref_nos; ?></td>
							<td><div style="width:60px; word-wrap:break-word"><? echo $job_nos; ?></div></td>
							<td><div style="width:60px; word-wrap:break-word"><? echo $style_nos; ?></div></td>
							<td><div style="width:120px; word-wrap:break-word"><? echo $order_nos; ?></div></td>
							<td><div style="width:150px; word-wrap:break-word"><? 
							//echo $fabric_desc;
							echo $product_array[$row[csf("prod_id")]]['product_name_details']; ?></div></td>
							<td align="center"><? echo $gsm; ?></td>
							<td align="center"><? echo $dia; ?></td>
							<td align="center"><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
							<td align="center" title="<?=$title;?>"><? echo $booking_qty; ?></td>
							<td align="right"><? echo number_format($row[csf("receive_qnty")],4); ?></td>
							<td align="right"><? echo $row[csf("reject_qty")]; ?></td>
							<td align="right"><? echo number_format($row[csf("no_of_roll")]); ?></td>
							<td align="right"><? echo $payment_yes_no[$row[csf("payment_over_rcv_status")]]; ?></td>
							<td align="right"><? echo number_format($order_rate,4); ?></td>
							<td align="right"><? echo number_format($amount,4); ?></td>
							<td align="right"><? echo number_format($amount_taka,4); ?></td>
							<td align="center"><p><? echo $fabric_shade[$row[csf('fabric_shade')]]; ?></p></td>
							<td align="center"><p><? echo $row[csf('remarks')]; ?></p></td>
						</tr>
						<? $i++;
						$totalRecQnty +=$row[csf("receive_qnty")];
						$totalRejQnty +=$row[csf("reject_qty")];
						$totalRoll +=$row[csf("no_of_roll")];
						$totalorder_rate +=$order_rate;
						$totamountothers +=$amount;
						$totamountintk +=$amount_taka;
					} ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="12" align="right"><strong>Total :</strong></td>
						<td align="right"><strong><?php echo number_format($booking_qty,2); ?></strong></td>
						<td align="right"><strong><?php echo number_format($totalRecQnty,2); ?></strong></td>
						<td align="right"><strong><?php echo number_format($totalRejQnty,2); ?></strong></td>
						<td align="right"><strong><?php echo number_format($totalRoll); ?></strong></td>
						<td></td>
						<td align="right"><strong><?php echo number_format($totalorder_rate,4); ?></strong></td>
						<td align="right"><strong><?php echo number_format($totamountothers,4); ?></strong></td>
						<td align="right"><strong><?php echo number_format($totamountintk,4); ?></strong></td>
						<td colspan="2"></td>
					</tr>
				</tfoot>
			</table>
			<table width="1330">
				<tr>
					<?php

					if($dataArray[0][csf("is_audited")]==1){
						?>
						<td><? echo 'Audited By &nbsp;'.$user_name[$dataArray[0][csf("audit_by")]].'&nbsp;'.$dataArray[0][csf("audit_date")]; ?></td>
					<?php
					}
					?>
				</tr>
			</table>
			<br>
			<?
			echo signature_table(149, $data[0], "1330px","",1);
			?>
		</div>
	</div>
	<script type="text/javascript" src="../../../js/jquery.js"></script>

	<?
	exit();
}

if ($action=="finish_fabric_receive_print_5")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r($data);
	$sql="SELECT id, recv_number, company_id, receive_basis, booking_id, booking_no, booking_without_order, store_id, location_id, knitting_source, knitting_company, receive_date, challan_no, store_id from inv_receive_master where id='$data[1]' and company_id='$data[0]' and item_category=2 and entry_form=37";

	$dataArray=sql_select($sql);
	$rec_basis=$dataArray[0][csf("receive_basis")];
	$country_arr=return_library_array( "SELECT id, country_name from  lib_country", "id", "country_name");
	$storeArr=return_library_array( "SELECT id, store_name from  lib_store_location", "id", "store_name");
	$location_library=return_library_array( "SELECT id, location_name from lib_location", "id", "location_name");
	$machineArr=return_library_array( "SELECT id, machine_no from lib_machine_name", "id", "machine_no");

	$company_arr = return_library_array("SELECT id, company_name from lib_company","id","company_name");
	$buyer_arr = return_library_array("SELECT id, buyer_name from lib_buyer","id","buyer_name");

	$supplier_arr = return_library_array("SELECT id, supplier_name from lib_supplier","id","supplier_name");
	$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active=1",'id','color_name');


	$sql_dtls="SELECT b.id, b.batch_id, b.prod_id, b.receive_qnty, b.reject_qty, b.no_of_roll, b.machine_no_id, b.rack_no, b.shelf_no, b.order_id, b.buyer_id, b.fabric_shade, b.uom, b.grey_used_qty, d.po_breakdown_id, a.receive_basis, c.booking_no, c.batch_no, c.color_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b left join order_wise_pro_details d on b.id=d.dtls_id and d.entry_form =37, pro_batch_create_mst c where a.id=b.mst_id and b.batch_id=c.id and a.entry_form=37 and a.id= '$data[1]'";

	$sql_result= sql_select($sql_dtls);
	foreach($sql_result as $v)
	{
		$order_ids_arr[$v[csf("po_breakdown_id")]]=$v[csf("po_breakdown_id")];
		$product_ids_arr[$v[csf("prod_id")]]=$v[csf("prod_id")];
	}


	$product_array=array();
	$product_sql = sql_select("SELECT id, product_name_details, gsm, dia_width from product_details_master where item_category_id=2 and status_active=1 and is_deleted=0 and id in (".implode(',', $product_ids_arr).")");
	foreach($product_sql as $row)
	{
		$product_array[$row[csf("id")]]['product_name_details']=$row[csf("product_name_details")];
		$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
	}

	$po_array=array();
	if(!empty($order_ids_arr))
	{
		$po_sql = sql_select("SELECT a.id, po_number, file_no, grouping as ref, b.style_ref_no from wo_po_break_down a, wo_po_details_master b where a.job_id=b.id and b.status_active=1 and b.is_deleted=0 and a.id in (".implode(',', $order_ids_arr).")");
		foreach($po_sql as $row)
		{
			$po_array[$row[csf("id")]]['po']=$row[csf("po_number")];
			$po_array[$row[csf("id")]]['file']=$row[csf("file_no")];
			$po_array[$row[csf("id")]]['ref']=$row[csf("ref")];
			$po_array[$row[csf("id")]]['style_ref_no']=$row[csf("style_ref_no")];
		}

	}

	?>
	<div style="width:1170px;">
		<table width="1140" cellspacing="0" align="right">
			<tr>
				<td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_arr[$data[0]]; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">
					<?
					$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
					foreach ($nameArray as $result)
					{
						?>
						Plot No: <? echo $result['plot_no']; ?>
						Level No: <? echo $result['level_no']?>
						Road No: <? echo $result['road_no']; ?>
						Block No: <? echo $result['block_no'];?>
						City No: <? echo $result['city'];?>
						Zip Code: <? echo $result['zip_code']; ?>
						Province No: <?php echo $result['province'];?>
						Country: <? echo $country_arr[$result['country_id']]; ?><br>
						Email Address: <? echo $result['email'];?>
						Website No: <? echo $result['website'];
					}
					?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><u>Textile <? echo $data[2]; ?> Challan</u></strong></td>
			</tr>
			<tr>
				<td width="120"><strong>Company</strong></td>
				<td width="175px"><? echo $company_arr[$dataArray[0][csf('company_id')]]; ?></td>
				<td width="120"><strong>Receive ID :</strong></td>
				<td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
				<td width="130"><strong>Receive Basis:</strong></td>
				<td width="175px"><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
				<td width="125"><strong>Receive Date:</strong></td>
				<td width="175px"><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Challan No:</strong></td>
				<td><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>WO/PI/Production:</strong></td>
				<td><? echo $sales_ords= $dataArray[0][csf('booking_no')]; ?></td>
				<td><strong>Store Name:</strong></td>
				<td><? echo $storeArr[$dataArray[0][csf('store_id')]]; ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><strong>Dyeing Source:</strong></td>
				<td><? echo $knitting_source[$dataArray[0][csf('knitting_source')]]; ?></td>
				<td><strong>Dyeing Company:</strong></td>
				<td><p><? if ($dataArray[0][csf('knitting_source')]==1) echo $company_arr[$dataArray[0][csf('knitting_company')]]; else if ($dataArray[0][csf('knitting_source')]==3) echo $supplier_arr[$dataArray[0][csf('knitting_company')]]; ?></p></td>
				<td><strong>Location:</strong></td>
				<td><? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr style=" height:20px">
				<td  colspan="3" id="barcode_img_id"></td>


			</tr>
			<tr style=" height:20px">
				<td colspan="8">&nbsp;</td>
			</tr>
		</table>

		<div style="width:100%; margin-top:20px;">
			<table align="right" cellspacing="0" cellpadding="0" width="1140"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th width="25">SL</th>
						<th width="100">Buyer</th>
						<th width="100">Style No</th>
						<th width="110">Booking No</th>
						<th width="120">Order No</th>
						<th width="140">Fabric Des.</th>
						<th width="30">GSM</th>
						<th width="60">Dia/Width</th>
						<th width="70">Batch No</th>
						<th width="50">Color</th>
						<th width="60">Fabric Shade</th>
						<th width="30">UOM</th>
						<th width="60">Receive Qty</th>
						<th width="60">Reject Rcv Qty</th>
						<th width="60">Grey Used Qty</th>
						<th width="60">No.of Roll</th>
						<th>Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?

					$i=1;
					foreach($sql_result as $row)
					{
						if ($i%2==0)$bgcolor="#E9F3FF";	 else $bgcolor="#FFFFFF";

						$po_no=$row[csf("po_breakdown_id")];

						$order_no=$po_array[$po_no]['po'];
						$file_no=$po_array[$po_no]['file'];
						$ref_no=$po_array[$po_no]['ref'];

						?>
						<tr>
							<td width="25" align="center"><? echo $i; ?></td>
							<td width="100" align="center"><? echo $buyer_arr[$row[csf("buyer_id")]]; ?></td>
							<td width="100" align="center"><? echo $po_array[$po_no]['style_ref_no'];?></td>
							<td width="110" align="center">
							<?
							if($row[csf("receive_basis")] !=11)
							{
								echo $row[csf("booking_no")];
							}
							?>
							</td>
							<td width="120" align="center"><? echo $order_no;?></td>
							<td width="140" align="center"><? echo $product_array[$row[csf("prod_id")]]['product_name_details']; ?></td>
							<td width="30" align="center"><? echo $product_array[$row[csf("prod_id")]]['gsm']; ?></td>
							<td width="60" align="center"><? echo $product_array[$row[csf("prod_id")]]['dia_width']; ?></td>
							<td width="70" align="center"><? echo $row[csf("batch_no")]; ?></td>
							<td width="50" align="center"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
							<td width="60" align="center"><? echo $fabric_shade[$row[csf("fabric_shade")]];?></td>
							<td width="30" align="center"><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
							<td width="60" align="right"><? echo number_format($row[csf("receive_qnty")],2); ?></td>
							<td width="60"  align="right"><? echo number_format($row[csf("reject_qty")],2); ?></td>
							<td width="60"  align="right"><? echo number_format($row[csf("grey_used_qty")],2); ?></td>
							<td width="60"  align="right"><? echo number_format($row[csf("no_of_roll")]); ?></td>
							<td><? echo $row[csf("remarks")]; ?></td>
						</tr>


						<? $i++;
						$totalRecQnty +=$row[csf("receive_qnty")];
						$totalRejQnty +=$row[csf("reject_qty")];
						$totalgrey_used_qty +=$row[csf("grey_used_qty")];
						$totalRoll +=$row[csf("no_of_roll")];
					} ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="12" align="right"><strong>Total :</strong></td>
						<td align="right"><?php echo number_format($totalRecQnty); ?></td>
						<td align="right"><?php echo number_format($totalRejQnty); ?></td>
						<td align="right"><?php echo number_format($totalgrey_used_qty); ?></td>
						<td align="right"><?php echo number_format($totalRoll); ?></td>
						<td align="right">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
			<br>
			<?
			echo signature_table(149, $data[0], "1140px");
			?>
		</div>
	</div>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
			// alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();

			var settings = {
				output:renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize:5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {code:value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $data[3]; ?>');
	</script>
	<?
	exit();
}

if ($action=="finish_fabric_receive_print_6")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r($data);
	$sql="SELECT id, recv_number, company_id, receive_basis, booking_id, booking_no, booking_without_order, store_id, location_id, knitting_source, knitting_company, receive_date, challan_no, store_id from inv_receive_master where id='$data[1]' and company_id='$data[0]' and item_category=2 and entry_form=37";



	$dataArray=sql_select($sql);
	$rec_basis=$dataArray[0][csf("receive_basis")];
	$country_arr=return_library_array( "SELECT id, country_name from  lib_country", "id", "country_name");
	$storeArr=return_library_array( "SELECT id, store_name from  lib_store_location", "id", "store_name");
	$location_library=return_library_array( "SELECT id, location_name from lib_location", "id", "location_name");
	$machineArr=return_library_array( "SELECT id, machine_no from lib_machine_name", "id", "machine_no");

	$company_arr = return_library_array("SELECT id, company_name from lib_company","id","company_name");
	$buyer_arr = return_library_array("SELECT id, buyer_name from lib_buyer","id","buyer_name");
	$buyer_id = return_field_value("buyer_id", "pro_finish_fabric_rcv_dtls", "mst_id=" . $data[1]);




	$supplier_arr = return_library_array("SELECT id, supplier_name from lib_supplier","id","supplier_name");
	$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active=1",'id','color_name');

						$sql_dtls="SELECT b.id, b.batch_id, b.prod_id, b.receive_qnty, b.reject_qty, b.no_of_roll, b.machine_no_id, b.rack_no, b.floor,b.room, b.shelf_no,b.bin, b.order_id, b.buyer_id, b.fabric_shade, b.uom, b.grey_used_qty, d.po_breakdown_id, a.receive_basis, c.booking_no, c.batch_no, c.color_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b left join order_wise_pro_details d on b.id=d.dtls_id and d.entry_form =37, pro_batch_create_mst c where a.id=b.mst_id and b.batch_id=c.id and a.entry_form=37 and a.id= '$data[1]'";
	//echo $sql_dtls;die;//

	$sql_result= sql_select($sql_dtls);
	foreach($sql_result as $v)
	{
		$order_ids_arr[$v[csf("po_breakdown_id")]]=$v[csf("po_breakdown_id")];
		$product_ids_arr[$v[csf("prod_id")]]=$v[csf("prod_id")];
	}


	$product_array=array();
	$product_sql = sql_select("SELECT id, product_name_details, gsm, dia_width from product_details_master where item_category_id=2 and status_active=1 and is_deleted=0 and id in (".implode(',', $product_ids_arr).")");
	foreach($product_sql as $row)
	{
		$product_array[$row[csf("id")]]['product_name_details']=$row[csf("product_name_details")];
		$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
	}

	$po_array=array();
	if(!empty($order_ids_arr))
	{
            $po_array[$row[csf("id")]]['job']=$row[csf("job_no")];
		$po_sql = sql_select("SELECT a.id, po_number, file_no, grouping as ref, b.style_ref_no,b.job_no from wo_po_break_down a, wo_po_details_master b where a.job_id=b.id and b.status_active=1 and b.is_deleted=0 and a.id in (".implode(',', $order_ids_arr).")");
		foreach($po_sql as $row)
		{

            $po_array[$row[csf("id")]]['job']=$row[csf("job_no")];

			$po_array[$row[csf("id")]]['po']=$row[csf("po_number")];
			$po_array[$row[csf("id")]]['file']=$row[csf("file_no")];
			$po_array[$row[csf("id")]]['ref']=$row[csf("ref")];
			$po_array[$row[csf("id")]]['style_ref_no']=$row[csf("style_ref_no")];
		}

	}
	$lib_floor_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "floor_id","floor_room_rack_name" );
	//print_r($lib_floor_arr);
	$lib_room_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "room_id","floor_room_rack_name" );
	$lib_rack_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "rack_id","floor_room_rack_name" );
	$lib_shelf_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "shelf_id","floor_room_rack_name" );
	$lib_bin_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "bin_id","floor_room_rack_name" );

	?>
	<div style="width:1300px;">
		<table width="1250" cellspacing="0" >
		<tr>
					<td rowspan="3" colspan="4" valign="middle">
						<?
						$data_array2=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
						foreach($data_array2 as $img_row)
						{
							?>
							<img src='../../../<? echo $img_row[csf('image_location')]; ?>' height='60' width='170' align="middle" />
							<?
						}
						?>
					</td>
					<td colspan="2" align="center" style="font-size:22px"><strong><? echo   $company_arr[$data[0]]; ?></strong></td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<?
                //echo show_company($data[0],'','');
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
						foreach ($nameArray as $result)
						{
							echo $result[csf('plot_no')];
							if($result[csf('plot_no')]!="") echo ", ";
							echo $result[csf('level_no')];
							if($result[csf('level_no')]!="") echo ", ";
							echo $result[csf('road_no')];
							if($result[csf('road_no')]!="") echo ", ";
							echo $result[csf('block_no')];
							if($result[csf('block_no')]!="") echo ", ";
							echo $result[csf('city')];
							if($result[csf('city')]!="") echo ", ";
							echo $result[csf('zip_code')];
							if($result[csf('zip_code')]!="") echo ", ";
							echo $result[csf('country_id')];
							if($result[csf('country_id')]!="") echo ", ";
							echo "<br> ";
							if($result[csf('email')]!="") echo "Email Address: ".$result[csf('email')];
							if($result[csf('website')]!="") echo "Website No: ".$result[csf('website')];
						}
						?>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center" style="font-size:18px"><strong><p> Finish Fabric Receive Challan</p></strong></td>
				</tr>
			<tr>
				<td width="120"><strong>Company :</strong></td>
				<td width="175px"><? echo $company_arr[$dataArray[0][csf('company_id')]]; ?></td>
				<td width="120"><strong>Receive ID :</strong></td>
				<td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
				<td width="130"><strong>Receive Basis:</strong></td>
				<td width="175px"><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
				<td width="125"><strong>Receive Date:</strong></td>
				<td width="175px"><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Challan No:</strong></td>
				<td><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>WO/PI/Production:</strong></td>
				<td><? echo $sales_ords= $dataArray[0][csf('booking_no')]; ?></td>
				<td><strong>Store Name:</strong></td>
				<td><? echo $storeArr[$dataArray[0][csf('store_id')]]; ?></td>
				<td><strong>Buyer Name:</strong></td>
				<td><? echo  $buyer_arr[$buyer_id]; ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><strong> Source:</strong></td>
				<td><? echo $knitting_source[$dataArray[0][csf('knitting_source')]]; ?></td>
				<td><strong>Supplier Company:</strong></td>
				<td><p><? if ($dataArray[0][csf('knitting_source')]==1) echo $company_arr[$dataArray[0][csf('knitting_company')]]; else if ($dataArray[0][csf('knitting_source')]==3) echo $supplier_arr[$dataArray[0][csf('knitting_company')]]; ?></p></td>
				<!-- <td><strong>Location:</strong></td>
				<td><? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
				<td>&nbsp;</td> -->
				<td>&nbsp;</td>
			</tr>
			<!-- <tr style=" height:20px">
				<td  colspan="3" id="barcode_img_id"></td>


			</tr> -->
			<tr style=" height:20px">
				<td colspan="8">&nbsp;</td>
			</tr>
		</table>

		<div style="width:100%; margin-top:20px;">
			<table  cellspacing="0" cellpadding="0" width="1150"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th width="25">SL</th>
						<th width="100">Job No</th>
						<th width="100">Style No</th>
						<th width="110">Int. Ref  No</th>
						<th width="120">Order No</th>
						<th width="140">Fabric Des.</th>
						<th width="30">GSM</th>
						<th width="60">Dia/Width</th>
						<th width="70">Batch No</th>
						<th width="50">Color</th>
						<th width="60">Fabric Shade</th>
						<th width="30">UOM</th>
						<th width="60">Receive Qty</th>
						<th width="60">Reject Rcv Qty</th>

						<th width="60">No.of Roll</th>
						<th width="60">Floor</th>
						<th width="60">Room</th>
						<th width="60">Rack</th>
						<th width="60">Self</th>
						<th width="60">Bin/Box</th>
						 <th>Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?



					$i=1;
					foreach($sql_result as $row)
					{
						if ($i%2==0)$bgcolor="#E9F3FF";	 else $bgcolor="#FFFFFF";


						$po_no=$row[csf("po_breakdown_id")];


						$job_no=$po_array[$po_no]['job'];
						$order_no=$po_array[$po_no]['po'];
						$file_no=$po_array[$po_no]['file'];
						$ref_no=$po_array[$po_no]['ref'];


						?>
						<tr>
							<td width="25" align="center"><? echo $i; ?></td>
							<td width="100" align="center"><?echo $po_array[$po_no]['job']; ?></td>
							<td width="100" align="center"><? echo $po_array[$po_no]['style_ref_no'];?></td>
							<td width="100" align="center"><? echo $po_array[$po_no]['ref'];?></td>
							<!-- <td width="110" align="center">
							<?
							if($row[csf("receive_basis")] !=11)
							{
								echo $row[csf("booking_no")];
							}
							?>
							</td> -->
							<td width="120" align="center"><? echo $order_no;?></td>
							<td width="140" align="center"><? echo $product_array[$row[csf("prod_id")]]['product_name_details']; ?></td>
							<td width="30" align="center"><? echo $product_array[$row[csf("prod_id")]]['gsm']; ?></td>
							<td width="60" align="center"><? echo $product_array[$row[csf("prod_id")]]['dia_width']; ?></td>
							<td width="70" align="center"><? echo $row[csf("batch_no")]; ?></td>
							<td width="50" align="center"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
							<td width="60" align="center"><? echo $fabric_shade[$row[csf("fabric_shade")]];?></td>
							<td width="30" align="center"><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
							<td width="60" align="right"><? echo number_format($row[csf("receive_qnty")],2); ?></td>
							<td width="60"  align="right"><? echo number_format($row[csf("reject_qty")],2); ?></td>

							<td width="60"  align="right"><? echo number_format($row[csf("no_of_roll")]); ?></td>
							<td  width="60"  align="right"><? echo $lib_floor_arr[$row[csf("floor")]]; ?></td>
							<td width="60"  align="center"><? echo $lib_room_arr[$row[csf("room")]]; ?></td>
							<td width="60"  align="center"><? echo $lib_rack_arr[$row[csf("rack_no")]]; ?></td>
							<td width="60"  align="center"><? echo $lib_shelf_arr[$row[csf("shelf_no")]]; ?></td>
							<td width="60"  align="center"><? echo $lib_bin_arr[$row[csf("bin")]]; ?></td>
							<td><? echo $row[csf("remarks")]; ?></td>
						</tr>


						<? $i++;
						$totalRecQnty +=$row[csf("receive_qnty")];
						$totalRejQnty +=$row[csf("reject_qty")];
						$totalgrey_used_qty +=$row[csf("grey_used_qty")];
						$totalRoll +=$row[csf("no_of_roll")];
					} ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="12" align="right"><strong>Total :</strong></td>
						<td align="right"><?php echo number_format($totalRecQnty); ?></td>
						<td align="right"><?php echo number_format($totalRejQnty); ?></td>

						<td align="right"><?php echo number_format($totalRoll); ?></td>

					</tr>
				</tfoot>
			</table>
			<br>
			<?
			echo signature_table(149, $data[0], "1140px");
			?>
		</div>
	</div>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
			// alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();

			var settings = {
				output:renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize:5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {code:value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $data[3]; ?>');
	</script>
	<?
	exit();
}

if ($action=="finish_fabric_receive_bin_card")
{
	extract($_REQUEST);
	$data=explode('*',$data);

	$sql="SELECT id, recv_number, company_id, receive_basis, booking_id, booking_no, booking_without_order, store_id, location_id, knitting_source, knitting_company, receive_date, challan_no, store_id, lc_no, currency_id, exchange_rate, source, store_id
	from inv_receive_master where id='$data[1]' and company_id='$data[0]' and item_category=2 and entry_form=37";

	$dataArray=sql_select($sql);
	$storeArr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");
	$location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$company_arr = return_library_array("select id, company_name from lib_company","id","company_name");
	// $color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$lib_shelf_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "shelf_id","floor_room_rack_name" );

	$sql_dtls = "SELECT c.receive_date, a.id, a.batch_id, a.prod_id, a.receive_qnty, a.grey_used_qty, a.reject_qty, a.no_of_roll, a.shelf_no, a.buyer_id, a.order_id as order_id_dtls, a.remarks, a.color_id, a.fabric_description_id as detar_id, b.po_breakdown_id as order_id
	from INV_RECEIVE_MASTER c, pro_finish_fabric_rcv_dtls a left join order_wise_pro_details b on b.dtls_id = a.id and b.entry_form = 37 and quantity >0 and b.status_active=1
	where c.id=a.mst_id and a.mst_id='$data[1]' and c.entry_form = 37 and a.status_active=1 and a.is_deleted= 0";
	// echo $sql_dtls;
	$sql_result= sql_select($sql_dtls);

	foreach ($sql_result as $val)
	{
		$all_po_arr .= $val[csf("order_id")].",";
		$all_batch_ids .= $val[csf("batch_id")].",";
		$all_prod_ids .= $val[csf("prod_id")].",";
		$all_detar_id .= $val[csf("detar_id")].",";
		$buyer_id_ids .= $val[csf("buyer_id")].",";
		$shelf_ids .= $val[csf("shelf_no")].",";
		$color_ids .= $val[csf("color_id")].",";

		if ($dtls_id_chk[$val[csf("id")]]=="")
		{
			$dtls_id_chk[$val[csf("id")]]=$val[csf("id")];

			$data_arr[$val[csf("batch_id")]][$val[csf("detar_id")]]['receive_date']=$val[csf("receive_date")];
			$data_arr[$val[csf("batch_id")]][$val[csf("detar_id")]]['receive_qnty']+=$val[csf("receive_qnty")];
			$data_arr[$val[csf("batch_id")]][$val[csf("detar_id")]]['no_of_roll']+=$val[csf("no_of_roll")];
			$data_arr[$val[csf("batch_id")]][$val[csf("detar_id")]]['prod_id']+=$val[csf("prod_id")];
		}
	}
	$all_po_arr=explode(",", chop($all_po_arr,","));
	// echo '<pre>';print_r($all_po_arr);
	$all_po_ids = implode(",", array_filter( array_unique($all_po_arr)));
	// echo chop($all_po_ids,',');die;
	if(!empty($all_po_ids))
	{
		$po_array=array();
		$po_sql = sql_select("SELECT a.id, po_number, file_no, grouping as ref, b.style_ref_no,b.job_no from wo_po_break_down a, wo_po_details_master b where a.job_id=b.id and b.status_active=1 and b.is_deleted=0 and a.id in ($all_po_ids)");
		foreach($po_sql as $row)
		{
            $po_array[$row[csf("id")]]['job']=$row[csf("job_no")];
			$po_array[$row[csf("id")]]['po']=$row[csf("po_number")];
			$po_array[$row[csf("id")]]['file']=$row[csf("file_no")];
			$int_ref.=$row[csf("ref")].',';
			$style_no.=$row[csf("style_ref_no")].',';
		}
	}
	$int_ref = implode(",", array_filter( array_unique(explode(",", chop($int_ref,",")))));
	$style_no = implode(",", array_filter( array_unique(explode(",", chop($style_no,",")))));
	$buyer_id_ids = implode(",", array_filter( array_unique(explode(",", chop($buyer_id_ids,",")))));
	$shelf_arr = array_filter( array_unique(explode(",", chop($shelf_ids,","))));
	$color_id_arr = array_filter( array_unique(explode(",", chop($color_ids,","))));

    $shelfs="";
    foreach ($shelf_arr as $shelf)
    {
        $shelfs .= $lib_shelf_arr[$shelf] . ",";
    }
    $shelfs = rtrim($shelfs, ", ");

	$all_batch_ids = chop($all_batch_ids,",");
	if(!empty($all_batch_ids))
	{
		$batch_sql = sql_select("select id, batch_no, color_id from pro_batch_create_mst where id in ($all_batch_ids)");
		foreach ($batch_sql as $val)
		{
			$batch_arr[$val[csf("id")]] = $val[csf("batch_no")];
			$batch_color[$val[csf("id")]] = $val[csf("color_id")];
		}
		$color_ids = implode(",", $batch_color);
		if(!empty($color_ids))
		{
			$color_name_arr=return_library_array( "select id, color_name from lib_color where id in ($color_ids)",'id','color_name');
		}

		// ============= Issue Start ===============
		$issue_sql="SELECT b.issue_date, a.pi_wo_batch_no as batch_id, d.detarmination_id, c.no_of_roll, a.cons_quantity as issue_qnty
		from inv_issue_master b,inv_transaction a,inv_finish_fabric_issue_dtls c, product_details_master d
		where b.entry_form=18 and b.id=a.mst_id and a.id=c.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=2 and a.transaction_type=2 and c.status_active=1 and c.is_deleted=0
		and c.prod_id=d.id and d.item_category_id=2 and b.status_active=1 and b.is_deleted=0 and b.company_id=$data[0] and a.pi_wo_batch_no in ($all_batch_ids)";
		// echo $issue_sql;die;
		$issData = sql_select($issue_sql);
		foreach($issData as $row)
		{
			$issue_qty_array[$row[csf('batch_id')]][$row[csf('detarmination_id')]]['issue_qnty']+=$row[csf('issue_qnty')];
			$issue_qty_array[$row[csf('batch_id')]][$row[csf('detarmination_id')]]['no_of_roll']+=$row[csf('no_of_roll')];
			$issue_qty_array[$row[csf('batch_id')]][$row[csf('detarmination_id')]]['max_issue_date']=$row[csf('issue_date')];
		}
		// echo '<pre>';print_r($issue_qty_array);die;

		// ============= Recv Return Start ===============
		$receiveReturn_sql="SELECT a.batch_id_from_fissuertn as batch_id, a.no_of_roll, c.detarmination_id, a.cons_quantity as recvrqnty
		from inv_transaction a, inv_finish_fabric_issue_dtls b,product_details_master c, inv_issue_master d
		where a.id=b.trans_id and a.mst_id=d.id and a.status_active=1 and a.is_deleted=0 and a.item_category=2 and a.transaction_type in(3) and d.entry_form=46 and B.PROD_ID =c.id and b.status_active=1 and b.is_deleted=0 and a.company_id=$data[0] and a.batch_id_from_fissuertn in ($all_batch_ids)";
		$receiveReturnData = sql_select($receiveReturn_sql);
		foreach($receiveReturnData as $row)
		{
			$recvRt_qty_array[$row[csf('batch_id')]][$row[csf('detarmination_id')]]['recvRtnQnty']+=$row[csf('recvrqnty')];
			$recvRt_qty_array[$row[csf('batch_id')]][$row[csf('detarmination_id')]]['no_of_roll']+=$row[csf('no_of_roll')];
		}

		//
		$transOut_sql ="SELECT b.batch_id, a.detarmination_id, b.no_of_roll, b.transfer_qnty as trans_out_qnty
		from inv_transaction c, order_wise_pro_details d, inv_item_transfer_dtls b, product_details_master a
		where  c.id=d.trans_id and d.dtls_id=b.id and c.company_id = $data[0]  and c.transaction_type = 6
		and c.item_category = 2 and b.from_prod_id = a.id  and c.status_active =1 and c.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and d.status_active = 1 and d.is_deleted =0 and d.entry_form in (14,306) and d.trans_type=6 and b.active_dtls_id_in_transfer = 1 and b.batch_id in ($all_batch_ids)";
		$transOutData = sql_select($transOut_sql);
		foreach($transOutData as $row)
		{
			$trans_out_qnty_array[$row[csf('batch_id')]][$row[csf('detarmination_id')]]['trans_out_qnty']+=$row[csf('trans_out_qnty')];
			$trans_out_qnty_array[$row[csf('batch_id')]][$row[csf('detarmination_id')]]['no_of_roll']+=$row[csf('no_of_roll')];
		}
		// echo '<pre>';print_r($trans_out_qnty_array);die;
	}

	$colors="";
    foreach ($color_id_arr as $color)
    {
        $colors .= $color_name_arr[$color] . ",";
    }
    $colors = rtrim($colors, ", ");

	$all_detar_id = chop($all_detar_id,",");
	if(!empty($all_detar_id))
	{
		$composition_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id in($all_detar_id) order by b.id asc";
		$data_array=sql_select($sql_deter);
		if(count($data_array)>0)
		{
			foreach( $data_array as $row )
			{
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
				$constuction_arr[$row[csf('id')]]=$row[csf('construction')];
			}
		}
	}

	$com_dtls = fnc_company_location_address($data[0], $location, 2);
	?>
	<div style="width:950px;">
		<!-- ===== Company Info Start ==== -->
		<table  width="980" cellspacing="0" align="center" border="0">
	        <tr>
	            <td  align="left"><img src="../../../<? echo $com_dtls[2]; ?>" height="100" width="100"></td>
	            <td colspan="2" align="center"  style="font-size:xx-large; text-align:center;"><strong><? echo $com_dtls[0]; ?></strong>
	            	<p style="font-size:15px;">
	            		<? echo $com_dtls[1]; //$storeArr[$dataArray[0][csf('store_id')]]?>
	            	</p>
	            </td>
	            <td colspan="2" align="center"  style="font-size:xx-large; text-align:right;">
		            <div style="float:right;width:24px; margin-right:80px; text-align:right">
						<div id="qrcode"></div>
		            </div>
	            </td>
	        </tr>
	        <tr class="form_caption">
	            <td></td>
	            <td colspan="3" align="center" style="font-size:18px; padding-right: 70px;"><?echo $storeArr[$dataArray[0][csf('store_id')]];?></td>
	            <td colspan="2" align="center" style="font-size:18px; width: 100px;"><strong>Scan Me</strong></td>
	        </tr>
	 	</table>
	 	<!-- ===== Master Part Start ==== -->
		<table width="980" cellspacing="0" align="left">
			<tr class="form_caption">
	            <td width="285" align="right"></td>
	            <td colspan="3" align="center" style="font-size:18px; padding-left: 100px;"><strong><u>Bin CARD</u></strong></td>
	            <td width="285px"></td>
	            <td colspan="2" align="right" style="font-size:18px;"><strong><?echo $dataArray[0][csf('recv_number')];?></strong></td>
	        </tr>
	    </table>
	    <table width="980" cellspacing="0" align="left" style="border: 1px solid; margin-bottom: 10px;">
			<tr>
				<td width="285" align="right">Buyer :</td>
				<td width="285px"><? echo $buyer_library[$buyer_id_ids]; ?></td>
				<td width="285"></td>
				<td width="285px"></td>
				<td width="285" align="right">Color :</td>
				<td width="285px"><? echo $colors; ?></td>
			</tr>
			<tr>
				<td align="right">Style :</td>
				<td><?=$style_no;?></td>
				<td></td>
				<td></td>
				<td align="right">Rack Location :</td>
				<td width="175px" colspan="2"><? echo $shelfs; ?></td>
			</tr>
			<tr>
				<td align="right">Internal Referance :</td>
				<td><?=$int_ref;?></td>
				<td></td>
				<td></td>
				<td align="right">Remarks :</td>
				<td width="175px" colspan="2"></td>
			</tr>
			<tr style=" height:20px">
				<td colspan="6">&nbsp;</td>
			</tr>
		</table>
		<!-- ===== Details Start ==== -->
		<div style="width:100%; margin-top:20px;">
			<table align="center" cellspacing="0" cellpadding="0" width="980"  border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th colspan="7">Fabric Receive</th>
						<th colspan="2">Fabric Issue</th>
						<th colspan="2">Fabric Stock</th>
					</tr>
					<tr>
						<th width="30">SL</th>
						<th width="60">Recived Date</th>
						<th width="70">Fabric Type</th>
						<th width="150">Fabric Compositon</th>
						<th width="70">Batch No</th>
						<th width="70">Number of Roll</th>
						<th width="50">Fabric Weight [Kg]</th>
						<th width="70" >Last Issue Date</th>
						<th width="50">Total Issue fabric Weight [Kg]</th>
						<th width="60">Stock Roll </th>
						<th width="80">Stock Weight [Kg]</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					foreach ($data_arr as $batch_id_key => $batch_id_value)
					{
						foreach ($batch_id_value as $dter_id => $row)
						{
							if ($i%2==0)$bgcolor="#E9F3FF";	 else $bgcolor="#FFFFFF";
							$issue_qty=$issue_qty_array[$batch_id_key][$dter_id]['issue_qnty'];
							$issue_roll=$issue_qty_array[$batch_id_key][$dter_id]['no_of_roll'];
							$max_issue_date=$issue_qty_array[$batch_id_key][$dter_id]['max_issue_date'];

							$recvRtn_qty=$recvRt_qty_array[$batch_id_key][$dter_id]['recvRtnQnty'];
							$recvRtn_roll=$recvRt_qty_array[$batch_id_key][$dter_id]['no_of_roll'];

							$trans_out_qty=$trans_out_qnty_array[$batch_id_key][$dter_id]['trans_out_qnty'];
							$trans_out_roll=$trans_out_qnty_array[$batch_id_key][$dter_id]['no_of_roll'];

							$all_issue_qty=$issue_qty+$recvRtn_qty+$trans_out_qty;
							$all_issue_roll=$issue_roll+$recvRtn_roll+$trans_out_roll;

							$title="Issue qty:".$issue_qty.', recvRtn_qty:'.$recvRtn_qty.', trans_out_qty:'.$trans_out_qty;

							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td align="center"><? echo $i; ?></td>
								<td><? echo change_date_format($row["receive_date"]); ?></td>
								<td title="<?=$dter_id;?>"><? echo $constuction_arr[$dter_id]; ?></td>
								<td><? echo $composition_arr[$dter_id]; ?></td>
								<td title="<?=$batch_id_key;?>"><? echo $batch_arr[$batch_id_key]; ?></td>
								<td><? echo $row["no_of_roll"]; ?></td>
								<td align="right"><? echo number_format($row["receive_qnty"],2); ?></td>
								<td><? echo change_date_format($max_issue_date); ?></td>
								<td align="right" title="<?=$title;?>"><? echo number_format($all_issue_qty,2); ?></td>
								<td align="right"><? echo $all_issue_roll; ?></td>
								<td align="right"><? echo $stock_weight=number_format($row["receive_qnty"]-$all_issue_qty,2); ?></td>
							</tr>
							<? $i++;
							$totalRecQnty +=$row["receive_qnty"];
							$totalIssue_qty +=$all_issue_qty;
							$totalstock_weight +=$stock_weight;
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="6" align="right"><strong>Total :</strong></td>
						<td align="right"><?php echo number_format($totalRecQnty); ?></td>
						<td align="right"></td>
						<td align="right"><?php echo number_format($totalIssue_qty); ?></td>
						<td align="right"></td>
						<td align="right"><?php echo number_format($totalstock_weight); ?></td>
					</tr>
				</tfoot>
			</table>
			<br>
			<?
			//echo signature_table(66, $data[0], "1110px");
			?>
		</div>
	</div>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquery.qrcode.min.js"></script>
	<script>
		var main_value='<? echo $dataArray[0][csf('recv_number')]; ?>';
		//alert(main_value);
		$('#qrcode').qrcode(main_value);
	</script>
	<?
	exit();
}


if ($action=="finish_fabric_receive_print_8") //  Finish Fabric Receive By Roll Wise
{
	extract($_REQUEST);
	$data=explode('*',$data);

	$sql="SELECT id, recv_number, company_id, receive_basis, booking_id, booking_no, booking_without_order, store_id, location_id, knitting_source, knitting_company, receive_date, challan_no, store_id, lc_no, currency_id, exchange_rate, source,challan_date,gate_entry_no,gate_entry_date 
	from inv_receive_master where id='$data[1]' and company_id='$data[0]' and item_category=2 and entry_form=37";
	$dataArray=sql_select($sql);

	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name");
	$storeArr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");
	$location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name");
	$machineArr=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$company_arr = return_library_array("select id, company_name from lib_company","id","company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$user_id 		= return_library_array("select id,user_name from user_passwd", "id", "user_name");
	$user_name 		= return_library_array("select id,user_full_name from user_passwd", "id", "user_full_name");
	$color_arr 		= return_library_array("select id,color_name from lib_color", "id", "color_name");


	$sql_query=  " select  b.buyer_id, a.booking_no ,b.color_id , b.gsm , b.dia_width_type, e.batch_no , c.roll_no , b.uom, b.receive_qnty, c.reject_qnty , f.item_description , b.fabric_shade, a.challan_no , b.width , g.po_number, h.style_ref_no, c.qnty, b.remarks from inv_receive_master a,pro_finish_fabric_rcv_dtls b, pro_roll_details c,  pro_batch_create_dtls d ,pro_batch_create_mst e , product_details_master f , wo_po_break_down g , wo_po_details_master h where a.id=$data[1] and a.company_id=$data[0] and a.item_category=2 and a.entry_form=37 and c.entry_form=37 and g.id = c.po_breakdown_id and g.job_id=h.id and a.id=b.mst_id and b.id=c.dtls_id and f.id= b.prod_id and c.dtls_id=d.dtls_id and d.po_id=c.po_breakdown_id and c.barcode_no=d.barcode_no and d.mst_id=e.id and e.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and g.status_active=1 and g.is_deleted=0 and h.status_active=1 and h.is_deleted=0 ORDER BY e.batch_no, c.roll_no ASC ";
	// echo $sql_query;
	$sql_result= sql_select($sql_query);
	// var_dump($sql_result);
	$remarks=array();
	$batch_count=array();

	foreach($sql_result as $row){
		$remarks[$row[csf('batch_no')]] = $row[csf('remarks')];
		$batch_count[$row[csf('batch_no')]] += 1;

	}


	?>
	<div style="width:1200px; " >
		<div style="width:100%; margin-top:20px; margin-left:20px;" align="center">
			<table cellspacing="0" align="right" >
				<tr >
					<td width="1200" colspan="6" align="center" style="font-size:22px"><strong><? echo $company_arr[$data[0]]; ?></strong></td>
				</tr>
				<tr class="form_caption" >
					<td width="1200" colspan="6" align="center" style="font-size:14px">
						<?
						$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
						foreach ($nameArray as $result)
						{
							?>
							Plot No: <? echo $result['plot_no']; ?>
							Level No: <? echo $result['level_no']?>
							Road No: <? echo $result['road_no']; ?>
							Block No: <? echo $result['block_no'];?>
							City No: <? echo $result['city'];?>
							Zip Code: <? echo $result['zip_code']; ?>
							Province No: <?php echo $result['province'];?>
							Country: <? echo $country_arr[$result['country_id']]; ?><br>
							Email Address: <? echo $result['email'];?>
							Website No: <? echo $result['website'];
						}
						?>
					</td>
				</tr>
				<tr >
					<td width="1200" colspan="6" align="center" style="font-size:18px"><strong><u><? echo "Finish Fabric Receive / GRN Report"; ?></u></strong></td>
				</tr>
				<tr>
					<td width="1200" colspan="6">&nbsp;</td>
				</tr>
				<tr style="margin-top:20px;">

					<td width="130"><strong>Company :</strong></td><td width="175px"><? echo $company_arr[$data[0]]; ?></td>
					<td width="130"><strong>Receive ID :</strong></td> <td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
					<td width="130"><strong>Receive Basis :</strong></td><td width="175px"><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
					<td width="130"><strong>Receive Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
					<td></td>
					<td></td>

				</tr>
				<tr >
					<td><strong>Challan No:</strong></td><td><? echo $dataArray[0][csf('challan_no')]; ?></td>
					<td><strong>WO/PI/Production:</strong></td><td><? echo $dataArray[0][csf('booking_no')]; ?></td>
					<td><strong>Store Name:</strong></td><td><? echo $storeArr[$dataArray[0][csf('store_id')]]; ?></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr >
					<td><strong>Dyeing Source:</strong></td><td><? echo $knitting_source[$dataArray[0][csf('knitting_source')]]; ?></td>
					<td><strong>Dyeing Company:</strong></td><td><p><? if ($dataArray[0][csf('knitting_source')]==1) echo $company_arr[$dataArray[0][csf('knitting_company')]]; else if ($dataArray[0][csf('knitting_source')]==3) echo $supplier_arr[$dataArray[0][csf('knitting_company')]]; ?></p></td>
					<td><strong>Location:</strong></td><td><? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td colspan="6">&nbsp;</td>
				</tr>
				<tr style=" height:20px">
					<td  colspan="3" id="barcode_img_id"></td>
				</tr>
				<tr style=" height:20px" >
					<td colspan="6">&nbsp;</td>
				</tr>
			</table>
		</div>

		<div style="width:100%; margin-top:20px; margin-left:20px;">
			<table align="right" cellspacing="0" cellpadding="0" width="1190"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="80">Buyer</th>
					<th width="100">Style No</th>
					<th width="100">Booking No</th>
					<th width="100">Order No</th>
					<th width="200">Fabric Des.</th>
					<th width="50">GSM</th>
					<th width="50" >Dia/ Width</th>
					<th width="80">Batch No</th>
					<th width="80">Color</th>
					<th width="50">Fabric Shade</th>
					<th width="50">Roll No</th>
					<th width="50">UOM</th>
					<th width="50">Receive Qty</th>
					<th width="50">Reject Rcv Qty</th>
					<th width="50">Grey Used Qty</th>
					<th>Remarks</th>
				</thead>
				<tbody>
					<?
					$batch="";
					$receive_sum=0;
					$reject_sum=0;
					$uom=0;
					$tmp=0;
					

					foreach($sql_result as $key => $row)
					{
						if ($key%2==0)$bgcolor="#E9F3FF";	 else $bgcolor="#FFFFFF";

						$cou += 1;

						if($key==0){
							$batch = $row[csf('batch_no')];
						}

						if( ($batch != $row[csf('batch_no')])){
							$batch = $row[csf('batch_no')];
							$tmp=0;
							
							?>
								<tr style="background-color:#D0CECE;">
									<td colspan="13" align="right"><strong>Total :</strong></td>
									<td align="right"><strong><? echo $receive_sum;  ?></strong></td>
									<td align="right"><strong><? echo $reject_sum; ?></strong></td>
									<td align="right">
										<strong>
											<?
												if($uom==12){
													echo $receive_sum - $reject_sum;;
												}else{
													echo 0;
												}
											?> 
										</strong>
									</td> 
									<td></td>
								</tr>
							<?
							 $receive_sum=0;
							 $reject_sum=0;
						}
						
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">

							<td align="center"><? echo $key+1; ?></td>
							<td align="center"><? echo $buyer_library[$row[csf('buyer_id')]]; ?></td>
							<td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
							<td align="center"><? echo $row[csf('booking_no')]; ?></td>
							<td align="center"><? echo $row[csf('po_number')]; ?></td> 
							<td align="center"><? echo $row[csf('item_description')]; ?></td>
							<td align="center"><? echo $row[csf('gsm')]; ?></td>
							<td align="center"><? echo $row[csf('width')]; ?></td>
							<td align="center"><? echo $row[csf('batch_no')]; ?></td>
							<td align="center"><? echo $color_arr[ $row[csf('color_id')] ]; ?></td>
							<td align="center"><? echo $fabric_shade[$row[csf('fabric_shade')]]; ?></td>
							<td align="center"><? echo $row[csf('roll_no')]; ?></td>
							<td align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; $uom=$row[csf('uom')];  ?></td>
							<td align="center"><? echo $row[csf('qnty')]; $receive_sum += $row[csf('qnty')]; ?></td>
							<td align="center"><? echo $row[csf('REJECT_QNTY')]; $reject_sum += $row[csf('REJECT_QNTY')]; ?></td>
							<td align="center">
								<?
									if($row[csf('uom')]==12){
										echo $row[csf('qnty')] - $row[csf('REJECT_QNTY')];
									}else{
										echo 0;
									}
								?>
							</td>

							 <?
								
								if($tmp == 0)

								{
									$tmp=1;
									?> 
										
											<td rowspan="<? echo $batch_count[$row[csf('batch_no')]] ?>" align="center"><? echo $remarks[$row[csf('batch_no')]];  ?></td>
											
											
									<?
									
								}
								
							 
							 ?> 

						</tr>
						<?
					}
					?>

				</tbody>

				<tfoot>
					<tr style="background-color:#D0CECE;">
						<td colspan="13" align="right"><strong>Total :</strong></td>
						<td align="right"><strong><? echo $receive_sum;  ?></strong></td>
						<td align="right"><strong><? echo $reject_sum; ?></strong></td>
						<td align="right">
							<strong>
							<?
									if($uom==12){
										echo $receive_sum - $reject_sum;;
									}else{
										echo 0;
									}
							?> 
							</strong>
						</td> 
						<td></td>
					</tr>
				</tfoot>
			</table>
			<br>
			<?
			echo signature_table(149, $data[0], "1110px");
			?>
		</div>
	</div>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
			// alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();

			var settings = {
				output:renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize:5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {code:value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $data[3]; ?>');
	</script>
	<?
	exit();
}



if ($action == "action_rate") {
	$data = explode("**", $data);
	$booking_id = $data[0];
	$booking_no = $data[1];
	$company_id = $data[2];
	$all_po_id = $data[3];
	$body_part = $data[4];
	$fabric_description = $data[5];
	$fabric_desc_id = $data[6];
	$is_sample = $data[7];
	$receive_basis = $data[8];
	$var_finish_rate_come_hdn = $data[9];

	if ($receive_basis == 2)
	{
		if ($is_sample == 0)
		{
			if($var_finish_rate_come_hdn==2)
			{
				//$yarn_issue_sql=sql_select("select a.id,b.cons_rate,c.po_breakdown_id,a.issue_date from inv_issue_master a,inv_transaction b,order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.issue_basis=1 and a.entry_form=3 and c.entry_form=3 and a.booking_id=$booking_id and c.po_breakdown_id in($all_po_id) and a.item_category=1 and b.item_category=1 and b.transaction_type=2");

				$yarn_issue_sql=sql_select("select c.quantity, c.quantity*b.cons_rate as amount, a.issue_date from inv_issue_master a,inv_transaction b,order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.issue_basis=1 and a.entry_form=3 and c.entry_form=3 and a.booking_id=$booking_id and c.po_breakdown_id in($all_po_id) and a.item_category=1 and b.item_category=1 and b.transaction_type=2 ");

				foreach ($yarn_issue_sql as $row) {
					$total_yarn_issue_amount += $row[csf("amount")];
					$total_yarn_issue_quantity += $row[csf("quantity")];
				}

				$yarn_issue_rate = $total_yarn_issue_amount/$total_yarn_issue_quantity;

				/*$pre_costing_conversion_cost_sql=sql_select("select c.charge_unit from wo_booking_dtls x, wo_po_details_master a,wo_po_break_down b,wo_pre_cost_fab_conv_cost_dtls c  where x.job_no=a.job_no and a.id=b.job_id and b.job_id=c.job_id and booking_no='$booking_no' and b.id in($all_po_id) and c.cons_process=1 and  x.pre_cost_fabric_cost_dtls_id=c.fabric_description and x.po_break_down_id=b.id group by c.charge_unit");*/

				$pre_costing_conversion_cost_sql=sql_select("select c.charge_unit from wo_booking_dtls x,wo_pre_cost_fabric_cost_dtls y, wo_po_details_master a,wo_po_break_down b,wo_pre_cost_fab_conv_cost_dtls c  where  x.pre_cost_fabric_cost_dtls_id=y.id and y.job_no=a.job_no and x.job_no=a.job_no and a.id=b.job_id and b.job_id=c.job_id and booking_no='$booking_no' and y.body_part_id in($body_part) and b.id in($all_po_id) and c.cons_process=1 and  x.pre_cost_fabric_cost_dtls_id=c.fabric_description and x.po_break_down_id=b.id group by c.charge_unit");


				//Fabric Dyeing -31 + Dyeing and Finishing -287 + DYEING FINISHING -158  // issue id: 11147
				$fabric_dyeing_pre_costing_conversion_cost_sql=sql_select("select c.charge_unit from wo_booking_dtls x,wo_pre_cost_fabric_cost_dtls y, wo_po_details_master a,wo_po_break_down b,wo_pre_cost_fab_conv_cost_dtls c  where  x.pre_cost_fabric_cost_dtls_id=y.id and y.job_no=a.job_no and x.job_no=a.job_no and a.id=b.job_id and b.job_id=c.job_id and booking_no='$booking_no' and y.body_part_id in($body_part) and b.id in($all_po_id) and c.cons_process in (31,287,158) and  x.pre_cost_fabric_cost_dtls_id=c.fabric_description and x.po_break_down_id=b.id and x.status_active=1 and x.is_deleted=0 and y.status_active=1 and y.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.charge_unit");
				$totalFinishingCharge=0;
				foreach ($fabric_dyeing_pre_costing_conversion_cost_sql as $rowData) 
				{
					$totalFinishingCharge+=$rowData[csf('charge_unit')];
				}

				$aop_pre_costing_conversion_cost_sql=sql_select("select c.charge_unit from wo_booking_dtls x,wo_pre_cost_fabric_cost_dtls y, wo_po_details_master a,wo_po_break_down b,wo_pre_cost_fab_conv_cost_dtls c  where  x.pre_cost_fabric_cost_dtls_id=y.id and y.job_no=a.job_no and x.job_no=a.job_no and a.id=b.job_id and b.job_id=c.job_id and booking_no='$booking_no' and y.body_part_id in($body_part) and b.id in($all_po_id) and c.cons_process=35 and  x.pre_cost_fabric_cost_dtls_id=c.fabric_description and x.po_break_down_id=b.id group by c.charge_unit");



				if($db_type==0)
				{
					$issueDate=change_date_format($yarn_issue_sql[0][csf("issue_date")],'yyyy-mm-dd');
				}
				else if($db_type==2)
				{
					$issueDate=change_date_format($yarn_issue_sql[0][csf("issue_date")],'','',-1);
				}
				$exchange_rate=return_field_value("conversion_rate","currency_conversion_rate"," currency=2 and company_id=$company_id and con_date<='$issueDate' and status_active=1 and is_deleted=0 order by id desc","conversion_rate");


				//$yarnIssueRate=$yarn_issue_sql[0][csf("cons_rate")]/$exchange_rate;
				$yarnIssueRate=$yarn_issue_rate/$exchange_rate;
				if(is_nan($yarnIssueRate)){$yarnIssueRate=0;}
				$preCostConverRate=$pre_costing_conversion_cost_sql[0][csf("charge_unit")]; //knitting charge
				//$fabric_dyeing_preCostConverRate=$fabric_dyeing_pre_costing_conversion_cost_sql[0][csf("charge_unit")]; //Fabric dyieng charge
				$fabric_dyeing_preCostConverRate=$totalFinishingCharge; //(Fabric dyieng charge+Dyeing and Finishing+DYEING FINISHING)

				$aop_preCostConverRate=$aop_pre_costing_conversion_cost_sql[0][csf("charge_unit")]; //AOP charge
				$yarnIssueString="1_$yarnIssueRate".','."2_$preCostConverRate".','."3_$fabric_dyeing_preCostConverRate".','."4_$aop_preCostConverRate";
			}
		}
		else
		{
			//$sql = "select c.color_name from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, lib_color c where a.booking_no=b.booking_no and b.fabric_color=c.id and a.id=$booking_id and a.item_category=2 and b.fabric_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.fabric_color, c.color_name";
		}

		$totalRate=$yarnIssueRate+$preCostConverRate+$fabric_dyeing_preCostConverRate+$aop_preCostConverRate;
	}
	//echo $sql;die;
	echo "document.getElementById('txt_rate').value 					= '" . number_format($totalRate,4) . "';\n";
	echo "document.getElementById('txt_hidden_rate').value 				= '" . $totalRate . "';\n";
	echo "document.getElementById('txt_hidden_yarn_issue_rate').value 	= '" . $yarnIssueRate . "';\n";
	echo "document.getElementById('txt_hidden_knitting_rate').value 	= '" . $preCostConverRate . "';\n";
	echo "document.getElementById('txt_hidden_dyeing_rate').value 		= '" . $fabric_dyeing_preCostConverRate . "';\n";
	echo "document.getElementById('txt_hidden_aop_rate').value 			= '" . $aop_preCostConverRate . "';\n";
	echo "document.getElementById('hdn_rate_string').value 				= '" . $yarnIssueString . "';\n";

	exit();
}
if ($action == "rate_popup") {
	echo load_html_head_contents("Rate Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="440" class="rpt_table">
			<thead>

			<?php
			$stringArr=explode(",", $hdn_rate_string);
			$pageName=array(1=>"Yarn Issue",2=>"Knitting Rate",3=>"Fabric Dyeing Rate",4=>"AOP Rate");
			foreach ($stringArr as $selectResult) {
				$pageWiseRate=explode("_",$selectResult);
				?>
				<th><? echo $pageName[$pageWiseRate[0]]; ?></th>
				<?
			}
			?>
			<th>Total Rate</th>
			</thead>
			<tr style="background-color:#E9F3FF;">
				<?php
				$stringArr=explode(",", $hdn_rate_string);
				foreach ($stringArr as $selectResult) {
					$pageWiseRate=explode("_",$selectResult);
					?>
						<td align="center"><?php echo number_format($pageWiseRate[1],4); ?></td>
					<?
					$totRate+=$pageWiseRate[1];
				}
				?>
				<td align="center"><? echo number_format($totRate,4); ?></td>
			</tr>
		</table>

		<table width="440" cellspacing="0" cellpadding="0" border="1" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:100%;">
							<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton"
							value="Close" style="width:100px"/>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<?
	exit();
}

if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);

	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."'  and module_id=6 and report_id=258 and is_deleted=0 and status_active=1");
	//echo $print_report_format;die;
	//$field_name, $table_name, $query_cond, $return_fld_name, $new_conn
	$print_report_format_arr=explode(",",$print_report_format);
	echo "$('#Print').hide();\n";
	echo "$('#Print2').hide();\n";
	echo "$('#Print3').hide();\n";
	echo "$('#Print4').hide();\n";
	echo "$('#Print5').hide();\n";
	echo "$('#Print6').hide();\n";
	echo "$('#bin_card').hide();\n";
	echo "$('#Print8').hide();\n";



	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==78){echo "$('#Print').show();\n";}
			if($id==66){echo "$('#Print2').show();\n";}
			if($id==85){echo "$('#Print3').show();\n";}
			if($id==137){echo "$('#Print4').show();\n";}
			if($id==129){echo "$('#Print5').show();\n";}
			if($id==161){echo "$('#Print6').show();\n";}
			if($id==819){echo "$('#bin_card').show();\n";}
			if($id==161){echo "$('#Print8').show();\n";}


		}
	}
	/* else
	{
		echo "$('#Print').show();\n";
		echo "$('#Print2').show();\n";
		echo "$('#Print3').show();\n";
		echo "$('#Print4').show();\n";
		echo "$('#Print5').show();\n";
		echo "$('#Print6').show();\n";
		echo "$('#bin_card').show();\n";

	} */
	exit();
}

?>