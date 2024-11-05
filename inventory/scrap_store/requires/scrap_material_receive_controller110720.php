<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$user_id=$_SESSION['logic_erp']['user_id'];
/**
 * User Credential start
 */
$userCredential = sql_select("select unit_id as company_id,item_cate_id,company_location_id,store_location_id from user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$location_credential_id = $userCredential[0][csf('company_location_id')];
$store_credential_id = $userCredential[0][csf('store_location_id')];

if ($company_id !='') {
    $company_credential_cond = "and comp.id in($company_id)";
}
if ($location_credential_id !='') {
    $location_credential_cond = "and id in($location_credential_id)";
}
if ($store_credential_id !='') {
    $store_credential_cond = "and a.id in($store_credential_id)";
}
//============= User credential end ==================
 //-------------------START ----------------------------------------
$company_arr = return_library_array("select id, company_name from lib_company","id","company_name");

$supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
$brand_arr = return_library_array("select id, brand_name from lib_brand","id","brand_name");
$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
$item_group_arr = return_library_array("select id, item_name from lib_item_group","id","item_name");

if($action == "load_drop_down_location")
{
	$data = explode('_',$data); 
    echo create_drop_down( "cbo_location", 170, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data[0]' $location_credential_cond order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/scrap_material_receive_controller', this.value+'_'+$data[0]+'_'+$data[1], 'load_drop_down_store', 'store_td');",0 );
	exit();
}

if ($action=="load_drop_down_store")
{
	$data = explode('_',$data);
	//echo "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[1]  and a.location_id =$data[0] and b.category_type=$data[2]  $store_credential_cond group by a.id,a.store_name order by a.store_name";
 	echo create_drop_down( "cbo_store", 170, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[1]  and a.location_id =$data[0] and b.category_type=$data[2]  $store_credential_cond group by a.id,a.store_name order by a.store_name","id,store_name",1, "-- Select Store --", "", 0, "", 1 );
	exit();
}

if ($action=="load_drop_down_uom_____")
{
	$data = explode('_',$data);
	//echo "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[1] group by a.id,a.store_name order by a.store_name";
 	echo create_drop_down( "cbo_uom", 160, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[1] group by a.id,a.store_name order by a.store_name","id,store_name","", 1, "-- Select Store --", 0, "", 1 );
	exit();
}
if($action=="check_conversion_rate") //Conversion Exchange Rate
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
	$currency_rate=set_conversion_rate( $data[0], $conversion_date );
	echo "1"."_".$currency_rate;
	exit();	
}
if ($action=="item_description_popup")
{
	echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
    ?>
	<script type="text/javascript">
		function js_set_value(id)
		{
			//alert (id);
			$('#hidden_prod_description').val(id);
			parent.emailwindow.hide();
		}
    
    	$(function(){
    		var tableFilters = { }
	    	setFilterGrid("html_search",-1,tableFilters);
    	});
    </script>
    </head>
    <body>
		<form action="" name="item_descrption_form " id="item_descrption_form">
			<input type="hidden" name="hidden_prod_description" id="hidden_prod_description" />
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" >
				<thead>
					<tr>
						<th colspan="2">Transaction Date Range</th>
						<th>&nbsp</th>
					</tr>                    
					<tr>
						<th width="380" style="text-align:right;"><span style="padding-right:50px;">From Date</span></th>
						<th width="380" style="text-align:left;"><span style="padding-left:50px;">To Date</span></th>
						
						<th width="140">
						<input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:80px;" onClick="reset_form('item_descrption_form','search_div','','','','');"></th>
					</tr>                    
				</thead>
				<tbody>
					<tr>
						<td align="right">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"  placeholder="From Date" style="width: 150px;" />
						</td>
						<td align="left">
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker"  placeholder="To Date"  style="width: 150px;"/>
						</td>
						
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onclick="show_list_view ( document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company_id?>+'_'+<? echo $cbo_category_id?>+'_'+<? echo $cbo_store_name?>+'_'+<? echo $cbo_receive_basis?>, 'create_scrap_receive_search_list_view', 'search_div', 'scrap_material_receive_controller', 'setFilterGrid(\'list_view\',0)');" style="width:80px; margin: 0 auto;">
						</td>
					</tr>
					<tr>
						<td colspan="4"><? echo load_month_buttons(1);  ?></td>
					</tr>
				</tbody>
			</table>
			<div valign="top" id="search_div" align="left"></div>
		</form>
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<?
	exit();
}

if($action=="mrr_popup")
{
	echo load_html_head_contents("MRR Popup", "../../../", 1, 1,'','','');
	extract($_REQUEST);
    ?>
	<script>
		function js_set_value(id,sys_num)
		{
			//alert (id);
			$('#hidden_system_id').val(id);
			$('#hidden_system_no').val(sys_num);
			parent.emailwindow.hide();
		}
    </script>
    </head>
	<input type="hidden" name="hidden_system_id" id="hidden_system_id" />
    <input type="hidden" name="hidden_system_no" id="hidden_system_no" />
	<?
		($company != 0) ? $diabled=1 : $diabled=0;
		($cbo_category_id != 0) ? $diabled_cate=1 : $diabled_cate=0;
		?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="510" class="rpt_table" >
			<thead>
				<tr>
					<th width="170">Company</th>
					<th width="170">Item Category</th>
					<th width="150">System ID</th>
					<th width="150" colspan="2">Receive Date Range</th>
					<th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;"></th>
				</tr>                    
			</thead>
			<tbody>
				<tr>
					<td>
						<? 
							echo create_drop_down( "cbo_company_name", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", $company, "",$diabled );
						?>
					</td>
					<td>
						<? 
							echo create_drop_down( "cbo_category_id", 170, $item_category,"",1, "-- Select Item --", $cbo_category_id, "", $diabled_cate, "");
						?>
					</td>
					<td>
						<input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric"  placeholder="System ID" />
					</td>
					<td>
						<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"  placeholder="From Date" />
					</td>
					<td>
						<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker"  placeholder="To Date" />
					</td>
					<td>
						<input type="button" name="button2" class="formbutton" value="Show" onclick="show_list_view ( document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company;?>+'_'+<? echo $cbo_category_id;?>+'_'+document.getElementById('txt_search_common').value, 'create_mrr_scrap_receive_search_list_view', 'mrr_search_div', 'scrap_material_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;">
					</td>
				</tr>
				<tr>
					<td colspan="5"><? echo load_month_buttons(1);  ?></td>
				</tr>
			</tbody>
		</table>
		<div valign="top" id="mrr_search_div" align="center"></div>
	<?
	exit();
}

if($action=="show_dtls_list_view")
{
	//$data=explode('_',$data);
	?>
	<div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table"  align="left">
            <thead>
                <th width="30">SL</th>
                <th width="50">Prod. ID</th>
                <th width="80">Group</th>
                <th width="150">Item Description</th>
                <th width="80">Lot</th>
                <th width="50">UOM</th>
                <th width="80">Receive Qty</th>
                <th width="70">Rate</th>
                <th width="70">Amount</th>
                <th>Remarks</th>
            </thead>
		</table>
	<div style="width:820;max-height:180px;" id="scrap_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table">
		<?
			$i=1;
			$sql_list_view = "select a.id, b.mst_id, b.receive_qnty, b.rate, b.amount, b.remarks, b.body_part, b.dia_type, b.book_currency, b.no_of_bags,b.trans_id, b.item_group_id, c.id as product_id, c.product_name_details, c.unit_of_measure, c.color,  b.lot, c.gsm, c.dia_width  from  inv_scrap_receive_mst a, inv_scrap_receive_dtls b, product_details_master c	where a.id=b.mst_id and b.product_id = c.id and b.mst_id='$data' and b.status_active=1 and b.status_active=1 and a.status_active=1 order by id desc";
			//echo $sql_list_view;
 			$sqlResult =sql_select($sql_list_view);

			foreach($sqlResult as $row)
			{
				($i%2==0) ? $bgcolor="#E9F3FF": $bgcolor="#FFFFFF";
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<? echo $row[csf('id')].'_'.$row[csf('trans_id')].'_'.$row[csf('product_id')]; ?>','populate_scrap_form_data','requires/scrap_material_receive_controller');" >
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="50" align="center"><p><? echo $row[csf('product_id')]; ?></p></td>
                    <td width="80" align="right"><? echo $item_group_arr[$row[csf('item_group_id')]]; ?></td>
                    <td width="150"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                    <td width="80" align="center"><p><? echo $row[csf('lot')]; ?></p></td>
                    <td width="50" align="center"><? echo $unit_of_measurement[$row[csf('unit_of_measure')]]; ?></td>
                    <td width="80" align="right"><? echo $row[csf('receive_qnty')]; ?></td>
                    <td width="70" align="right"><? echo number_format($row[csf('rate')],2); ?></td>
                    <td width="70" align="right"><? echo number_format($row[csf('amount')],2); ?></td>
                    <td align="center"><? echo $row[csf('remarks')]; ?></td>
                </tr>
				<?
				$i++;
			}
		?>
		</table>
	</div>
    </div>
<?
	exit();
}

if($action=="create_scrap_receive_search_list_view")
{
    $data=explode('_',$data);
	//print_r($data);
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$body_part_array=return_library_array( "select id, body_part_full_name from lib_body_part",'id','body_part_full_name');
	$prod_wise_body_part_array=return_library_array( "select id, prod_id, body_part_id from pro_grey_prod_entry_dtls",'prod_id','body_part_id');
	$previous_scrap_received_arr=return_library_array( "select a.id, a.trans_id from inv_scrap_receive_dtls a where a.trans_id>0 and a.trans_id is not null and a.status_active=1",'id','trans_id');
	//print_r($prod_wise_body_part_array);
	$item_category_id = $data[3];
	$receive_basis = $data[5];
	if ($data[2]!=0) $company=" and a.company_id='$data[2]'"; else $company="";
	if ($data[3]!=0) $category_cond=" and a.item_category='$data[3]'"; else $category_cond="";
	if ($data[4]!=0) $store_cond=" and a.store_id='$data[4]'"; else $store_cond="";
	//if ($data[5]!=0) $basis_cond=" and a.receive_basis='$data[5]'"; else $basis_cond="";

	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $transaction_date_cond = "and a.transaction_date between '".change_date_format($data[0],'yyyy-mm-dd')."' and '".change_date_format($data[1],'yyyy-mm-dd')."'"; else $transaction_date_cond ="";
	}
	else
	{
		if ($data[0]!="" &&  $data[1]!="") $transaction_date_cond = "and a.transaction_date between '".change_date_format($data[0], "", "",1)."' and '".change_date_format($data[1], "", "",1)."'"; else $transaction_date_cond ="";
	}

	if($receive_basis == 1){ //scrap receive basis -> Receive-Reject
		$transaction_type_cond = "  and a.transaction_type in(1,4,5) ";	
		$reject_qnty_cond = " and a.cons_reject_qnty > 0";
		$sql= "select a.id as trans_id, a.mst_id, a.transaction_date, a.cons_reject_qnty as cons_qnty,a.transaction_type, a.body_part_id, a.pi_wo_batch_no, b.id as prod_id, b.item_group_id, b.item_category_id, b.product_name_details, b.unit_of_measure, b.lot, b.current_stock, b.color, b.item_color, b.gsm, b.brand, b.dia_width, b.item_size 
		from inv_transaction a, product_details_master b
		where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 $transaction_type_cond and b.is_deleted =0  and b.status_active =1 $company $category_cond  $transaction_date_cond $store_cond $reject_qnty_cond and a.id not in(".implode(",",$previous_scrap_received_arr).")  order by b.id desc ";
	}elseif ($receive_basis == 2) {
		$transaction_type_cond = "  and a.transaction_type in(2,3,6) ";
		$reject_qnty_cond = " and a.cons_quantity > 0";
		$trns_mst_sql = "select listagg(id, ',') within group(order by id) as id from inv_issue_master where company_id=$data[2] and item_category = $data[3]  and issue_purpose in(26)"; //and store_id=$data[4]
		//echo $trns_mst_sql;
		$trans_mst_id = sql_select($trns_mst_sql);

		$sql= "select a.id as trans_id, a.mst_id, a.transaction_date, a.cons_quantity as cons_qnty,a.transaction_type, a.body_part_id, a.pi_wo_batch_no, b.id as prod_id, b.item_group_id, b.item_category_id, b.product_name_details, b.unit_of_measure, b.lot, b.current_stock, b.color, b.item_color, b.gsm, b.brand, b.dia_width, b.item_size 
		from inv_transaction a, product_details_master b, inv_issue_master c
		where a.prod_id=b.id and a.mst_id=c.id and a.status_active=1 and a.is_deleted=0 $transaction_type_cond and b.is_deleted =0  and b.status_active =1 $company $category_cond  $transaction_date_cond $store_cond $reject_qnty_cond and a.id not in(".implode(",",$previous_scrap_received_arr).") and a.mst_id in(".$trans_mst_id[0][csf('id')].")  order by b.id desc ";
	}elseif ($receive_basis == 3) {
		$transaction_type_cond = "  and a.transaction_type in(2,3,6) ";
		$reject_qnty_cond = " and a.cons_quantity > 0";
		$trans_mst_id = sql_select("select listagg(id, ',') within group(order by id) as id from inv_issue_master where company_id=$data[2] and item_category = $data[3]  and issue_purpose in(31)"); //and store_id=$data[4]

		$sql= "select a.id as trans_id, a.mst_id, a.transaction_date, a.cons_quantity as cons_qnty,a.transaction_type, a.body_part_id, a.pi_wo_batch_no, b.id as prod_id, b.item_group_id, b.item_category_id, b.product_name_details, b.unit_of_measure, b.lot, b.current_stock, b.color, b.item_color, b.gsm, b.brand, b.dia_width, b.item_size 
		from inv_transaction a, product_details_master b, inv_issue_master c
		where a.prod_id=b.id and a.mst_id=c.id and a.status_active=1 and a.is_deleted=0 $transaction_type_cond and b.is_deleted =0  and b.status_active =1 $company $category_cond  $transaction_date_cond $store_cond $reject_qnty_cond and a.id not in(".implode(",",$previous_scrap_received_arr).") and a.mst_id in(".$trans_mst_id[0][csf('id')].")  order by b.id desc ";
	}
	//if ($data[4]!='') $sys_id_cond=" and challan_no_prefix_num='$data[4]'"; else $sys_id_cond="";
	

	

	//$arr=array (2=>$item_category,4=>$party_arr,5=>$currency);
	
    
	//echo $sql;
	$description_resutl = sql_select($sql);
	foreach ($description_resutl as  $value) {
		if(1 == $value[csf("transaction_type")] || 4 ==  $value[csf("transaction_type")] || 5== $value[csf("transaction_type")])
		{
			$receive_id_arr[$value[csf("trans_id")]] = $value[csf("mst_id")];	
			$rcv_ids = implode(",", $receive_id_arr);
			
		}else{
			$issue_id_arr[$value[csf("trans_id")]] = $value[csf("mst_id")];
			$iss_ids = implode(",", $issue_id_arr);
		}
		$batch_ids_array[$value[csf("trans_id")]] =  $value[csf("pi_wo_batch_no")];
	}
	//print_r($iss_ids);die;
	$batch_ids =  implode(",", $batch_ids_array);
	$receive_ids_arr = return_library_array( "select id, recv_number from inv_receive_master where id in($rcv_ids)",'id','recv_number');
	$issue_ids_arr = return_library_array( "select id, issue_number from inv_issue_master where id in($iss_ids)",'id','issue_number');
	$batch_arr = return_library_array( "select id, BATCH_NO from PRO_BATCH_CREATE_MST where id in($batch_ids)",'id','BATCH_NO');
	//$issue_purpose_arr = return_library_array( "select id, issue_purpose from inv_issue_master where id in($iss_ids)",'id','issue_purpose');
	$sys_challan_no_array = $receive_ids_arr;
	foreach ($issue_ids_arr as $key => $value) {
		array_push($sys_challan_no_array, $key, $value);
	}
	//print_r($sys_challan_no_array);//die;
	?>

		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" id="html_search">
			<thead>
				<tr>
					<th width="40" align="center">SL</th>
					<th width="80" align="center">Product Id</th>
					<th width="100" align="center">System ID</th>
					<th width="100" align="center">Item Category</th>
					<th width="150" align="center">Description</th>
					<th width="80" align="center">Lot/Batch</th>
					<th width="60" align="center">UOM</th>
					<th width="70" align="center">Color</th>
					<th width="60" align="center">GSM</th>
					<th width="70" align="center">Brand</th>
					<th width="80" align="center">Transaction Date</th>
					<th align="right">Qty</th>
				</tr>
			</thead>
			<tbody id="list_view">
				<?
					
					$i=1;
					foreach ($description_resutl as $value) {
						($i%2==0)? $bgcolor = "#E9F3FF" : $bgcolor = "#FFFFFF" ;
						if($value[csf("item_category_id")] == 13){
							$body_part_val = $body_part_array[$prod_wise_body_part_array[$value[csf("prod_id")]]];
						}else if($value[csf("item_category_id")] == 2){
							$body_part_val = $body_part_array[$value[csf("body_part_id")]];
							$lot_batch = $batch_arr[$value[csf("pi_wo_batch_no")]];
						}else{
							$lot_batch = $value[csf("lot")];
						}
						?>
						<tr bgcolor="<?= $bgcolor; ?>" style="cursor:pointer;" onClick='js_set_value("<? echo $value[csf("product_name_details")].'__'.$lot_batch.'__'.$value[csf("unit_of_measure")].'__'.$color_arr[$value[csf("color")]].'__'.$item_group_arr[$value[csf("item_group_id")]].'__'.$value[csf("prod_id")].'__'.$value[csf("color")].'__'.$value[csf("item_group_id")].'__'.$value[csf("cons_qnty")].'__'.$value[csf("trans_id")].'__'.$sys_challan_no_array[$value[csf("mst_id")]].'__'.$value[csf("mst_id")].'__'.$value[csf("gsm")].'__'.$value[csf("dia_width")].'__'.$body_part_val;?>")'>
							<td align="center"><? echo $i;?></td>
							<td align="center"><? echo $value[csf("prod_id")];?></td>
							<td align="center">
								<? 
									if($value[csf("transaction_type")]== 1 || $value[csf("transaction_type")]== 4 || $value[csf("transaction_type")]== 5){
										echo $receive_ids_arr[$value[csf("mst_id")]];
									}else{
										echo $issue_ids_arr[$value[csf("mst_id")]];
									}
								?>
							</td>
							<td  align="center"><? echo $item_category[$value[csf("item_category_id")]];?></td>
							<td  align="center"><? echo $value[csf("product_name_details")];?></td>
							<td  align="center" style="word-wrap: break-all;"><? echo $lot_batch;?></td>
							<td  align="center"><? echo $unit_of_measurement[$value[csf("unit_of_measure")]];?></td>
							<td align="center"><? echo $color_arr[$value[csf("color")]];?></td>
							<td align="center"><? echo $value[csf("gsm")];?></td>
							<td align="center"><? echo $brand_arr[$value[csf("brand")]];?></td>
							<td align="center"><? echo change_date_format($value[csf("transaction_date")]);?></td>
							<td align="right"><? echo $value[csf("cons_qnty")];?></td>
						</tr>
						<?
						$i++;
					}
				?>
			</tbody>
		</table>
	<?
	exit();
}

if ($action=="populate_scrap_form_data")
{
	$data=explode('_',$data);

	$product_name_arr = return_library_array("select id, product_name_details from product_details_master","id","product_name_details");
	$sql_name_array = "select a.id, b.id as dtls_id, b.mst_id, b.receive_qnty, b.rate, b.amount, b.remarks, b.body_part, b.dia_type, b.book_currency, b.no_of_bags, c.item_group_id, c.id as product_id, c.product_name_details, c.unit_of_measure, c.color,  b.lot, c.gsm, c.dia_width  from  inv_scrap_receive_mst a, inv_scrap_receive_dtls b, product_details_master c	where a.id=b.mst_id and b.product_id = c.id and b.mst_id='$data[0]' and b.trans_id='$data[1]' and b.product_id='$data[2]' and b.status_active=1 and b.status_active=1 and a.status_active=1 order by id desc";
	//echo $sql_name_array;
	$nameArray=sql_select( $sql_name_array);
	foreach ($nameArray as $row)
	{
		echo "document.getElementById('txt_item_desc').value		= '".$row[csf("product_name_details")]."';\n";
		echo "document.getElementById('cbo_item_group').value		= '".$item_group_arr[$row[csf("item_group_id")]]."';\n";
		echo "document.getElementById('body_part').value		= '".$row[csf("body_part")]."';\n";
		echo "document.getElementById('txt_gsm').value		= '".$row[csf("gsm")]."';\n";
		echo "document.getElementById('dia_width').value		= '".$row[csf("dia_width")]."';\n";
		echo "document.getElementById('txt_lot').value	= '".$row[csf("lot")]."';\n";
		echo "document.getElementById('txt_remarks').value		= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('txt_receive_qty').value	= '".$row[csf("receive_qnty")]."';\n";
		echo "document.getElementById('hdn_receive_qty').value	= '".$row[csf("receive_qnty")]."';\n";
		echo "document.getElementById('txt_rate').value	= '".$row[csf("rate")]."';\n";
		echo "document.getElementById('txt_amount').value			= '".$row[csf("amount")]."';\n";
		echo "document.getElementById('cbo_uom').value	= '".$row[csf("unit_of_measure")]."';\n";
		echo "document.getElementById('txt_book_currency').value			= '".$row[csf("book_currency")]."';\n";
		//echo "document.getElementById('dia_width_type').value			= '".$row[csf("dia_type")]."';\n";
		echo "document.getElementById('txt_color').value			= '".$color_arr[$row[csf("color")]]."';\n";
		echo "document.getElementById('txt_no_of_bags').value			= '".$row[csf("no_of_bags")]."';\n";
		echo "document.getElementById('update_id').value			= '".$row[csf("id")]."';\n";
		echo "document.getElementById('update_dtls_id').value			= '".$row[csf("dtls_id")]."';\n";

		echo "$('#body_part').prop('disabled',true);\n";
		echo "$('#txt_gsm').prop('disabled',true);\n";
		echo "$('#dia_width').prop('disabled',true);\n";
		//echo "$('#dia_width_type').prop('disabled',true);\n";
		echo "$('#txt_item_desc').prop('disabled',true);\n";
		echo "$('#txt_receive_qty').prop('disabled',true);\n";
		echo "$('#cbo_location').prop('disabled',true);\n";
		echo "$('#cbo_store').prop('disabled',true);\n";
		echo "$('#txt_receive_date').prop('disabled',true);\n";
		echo "$('#cbo_item_group').prop('disabled',true);\n";
		echo "$('#txt_color').prop('disabled',true);\n";

		echo "set_button_status(1,'".$_SESSION['page_permission']."', 'fnc_scrap_material_receive_entry',1,1);\n";
	}
	exit();
}
if ($action=="populate_scrap_master_form_data")
{
	$sql_name_array = "select a.id, a.company_id, a.location, a.store_id, a.receive_date, a.receive_basis, a.item_category_id, b.id as dtls_id, b.product_id, b.trans_id  from  inv_scrap_receive_mst a, inv_scrap_receive_dtls b, product_details_master c	where a.id=b.mst_id and b.product_id = c.id and b.mst_id='$data' and b.status_active=1 and b.status_active=1 and a.status_active=1 order by id desc";
	$nameArray=sql_select( $sql_name_array);
	foreach ($nameArray as $row)
	{
		echo "load_drop_down( 'requires/scrap_material_receive_controller', '".$row[csf("location")]."_".$row[csf("company_id")]."_".$row[csf("item_category_id")]."', 'load_drop_down_store', 'store_td');\n";

		echo "document.getElementById('cbo_location').value		= '".$row[csf("location")]."';\n";
		echo "document.getElementById('cbo_store').value		= '".$row[csf("store_id")]."';\n";
		echo "document.getElementById('txt_trans_id').value		= '".$row[csf("trans_id")]."';\n";
		echo "document.getElementById('hidden_pord_id').value		= '".$row[csf("product_id")]."';\n";
		echo "document.getElementById('txt_receive_date').value	= '".change_date_format($row[csf("receive_date")],"yyyy-mm-dd")."';\n";
		echo "document.getElementById('cbo_receive_basis').value= '".$row[csf("receive_basis")]."';\n";
		echo "document.getElementById('update_id').value= '".$row[csf("id")]."';\n";
		echo "document.getElementById('update_dtls_id').value= '".$row[csf("dtls_id")]."';\n";


		echo "$('#body_part').prop('disabled',true);\n";
		echo "$('#txt_gsm').prop('disabled',true);\n";
		echo "$('#dia_width').prop('disabled',true);\n";
		//echo "$('#dia_width_type').prop('disabled',true);\n";
		echo "$('#txt_item_desc').prop('disabled',true);\n";
		echo "$('#cbo_location').prop('disabled',true);\n";
		echo "$('#cbo_store').prop('disabled',true);\n";
		echo "$('#txt_receive_date').prop('disabled',true);\n";
		echo "$('#cbo_receive_basis').prop('disabled',true);\n";
		echo "$('#cbo_category_id').prop('disabled',true);\n";
		echo "$('#cbo_company_name').prop('disabled',true);\n";
		echo "$('#cbo_item_group').prop('disabled',true);\n";

		echo "set_button_status(1,'".$_SESSION['page_permission']."', 'fnc_scrap_material_receive_entry',1,1);\n";
	}
	exit();
}


if($action=="create_mrr_scrap_receive_search_list_view")
{
	$data=explode('_',$data);
	$location_result = sql_select("select id, location_name from lib_location where company_id=$data[2] and status_active=1 and is_deleted=0");
	$receive_scrap_arra = array("-- Select Basis","Receive-Reject","Issue-Damage", "Issue-Scrape Store");
	foreach ($location_result as $value) {
		$location_arr[$value[csf("id")]] = $value[csf("location_name")];
	}
    //print_r($data);
	if ($data[2]!=0) $company_cond=" and a.company_id='$data[2]'"; else $company_cond="";
	if ($data[3]!=0) $category_cond=" and a.item_category_id='$data[3]'"; else $category_cond="";
	if ($data[4]!=0) $search_cond=" and a.receive_no_prefix_num='$data[4]'"; else $search_cond="";

	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $transaction_date_cond = "and a.receive_date between '".change_date_format($data[0],'yyyy-mm-dd')."' and '".change_date_format($data[1],'yyyy-mm-dd')."'"; else $transaction_date_cond ="";
	}
	else
	{
		if ($data[0]!="" &&  $data[1]!="") $transaction_date_cond = "and a.transaction_date between '".change_date_format($data[0], "", "",1)."' and '".change_date_format($data[1], "", "",1)."'"; else $transaction_date_cond ="";
	}

	
    $sql= "select a.id, a.sys_receive_no, a.company_id, a.item_category_id, a.location, a.store_id, a.receive_date, a.receive_basis, b.receive_qnty, b.product_id,b.lot
    from inv_scrap_receive_mst a, inv_scrap_receive_dtls b
    where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0  and b.is_deleted =0  and b.status_active=1 $company_cond $category_cond  $transaction_date_cond $search_cond order by b.id desc ";
		//echo $sql;
		$description_resutl = sql_select($sql);

	?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="760" class="rpt_table" id="html_search" align="left">
			<thead>
				<tr>
					<th width="40" align="center">SL</th>
					<th width="100" align="center">Receive Number</th>
					<th width="100" align="center">Product Id</th>
					<th width="100" align="center">Item Category</th>
					<th width="100" align="center">Lot/Batch</th>
					<th width="150" align="center">Location</th>
					<!-- <th width="80" align="center">Store</th> -->
					<th width="80" align="center">receive_date</th>
					<th align="center">receive_basis</th>
				</tr>
			</thead>
			<tbody>
				<?
					$i=1;
					foreach ($description_resutl as $value) {
						($i%2==0)? $bgcolor = "#E9F3FF" : $bgcolor = "#FFFFFF" ;
						?>
						<tr bgcolor="<?= $bgcolor; ?>" style="cursor:pointer;" onClick='js_set_value("<? echo $value[csf("id")];?>","<? echo $value[csf("sys_receive_no")];?>")'>
							<td align="center"><? echo $i;?></td>
							<td align="center"><? echo $value[csf("sys_receive_no")];?></td>
							<td align="center"><? echo $value[csf("product_id")];?></td>
							<td align="center"><? echo $item_category[$value[csf("item_category_id")]];?></td>
							<td align="center" style="word-wrap: break-all;"><? echo $value[csf("lot")];?></td>
							<td align="center"><? echo $location_arr[$value[csf("location")]];?></td>
							<!-- <td align="center"><? //echo $value[csf("store_id")];?></td> -->
							<td align="center"><? echo change_date_format($value[csf("receive_date")],'yyyy-mm-dd');?></td>
							<td align="center"><? echo $receive_scrap_arra[$value[csf("receive_basis")]];?></td>
						</tr>
						<?
						$i++;
					}
				?>
			</tbody>
		</table>
	<?
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	// echo "<pre>";
	// print_r($process);die;
	($update_id == "") ? $update_id = $txt_system_id : $update_id = $update_id;
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later

			$new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SRE', date("Y",time()), 5, "select receive_no_prefix, receive_no_prefix_num from inv_scrap_receive_mst where company_id=$cbo_company_name and $year_cond=".date('Y',time())." order by id desc ", "receive_no_prefix", "receive_no_prefix_num"));

			$id=return_next_id( "id", "inv_scrap_receive_mst", 1) ;
			$field_array="id, receive_no_prefix,receive_no_prefix_num,sys_receive_no,company_id,item_category_id,location,store_id,entry_form,receive_date,receive_basis,currency,exchange_rate,challan_no,mst_id,inserted_by,insert_date,status_active,is_deleted";

			$data_array="(".$id.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$cbo_company_name.",".$cbo_category_id.",".$cbo_location.",".$cbo_store.",0,".$txt_receive_date.",".$cbo_receive_basis.",".$cbo_currency.",".$txt_exchange_rate.",".$txt_system_challan_no.",".$txt_mst_id.",".$user_id.",'".$pc_date_time."',1,0)";
			//echo $data_array;die;cbo_purpose

			//echo "insert into inv_scrap_sales_mst (".$field_array.") values ".$data_array;die;
			/*$rID=sql_insert("inv_issue_master",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0; */

			$sys_challan_no=$new_system_id[0];
			$row_id=$id;
		}
		else
		{
			$field_array_update="item_category_id*receive_basis*currency*exchange_rate*updated_by*update_date";
			$data_array_update=$cbo_category_id."*".$cbo_receive_basis."*".$cbo_currency."*".$txt_exchange_rate."*".$user_id."*'".$pc_date_time."'";

			/*$rID=sql_update("inv_issue_master",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; */

			$sys_challan_no=str_replace("'","",$txt_system_no);
			$row_id=str_replace("'","",$update_id);
		}

		
		$id_dtls=return_next_id( "id", "inv_scrap_receive_dtls", 1) ;

		$field_array_dtls="id, mst_id, item_group_id, product_id, trans_id, receive_qnty, rate, amount, uom, color, remarks, body_part, lot, gsm, dia, book_currency, no_of_bags, inserted_by, insert_date, status_active, is_deleted";

		$data_array_dtls="(".$id_dtls.",".$row_id.",".$hidd_item_group_id.",".$hidden_pord_id.",".$txt_trans_id.",".$txt_receive_qty.",".$txt_rate.",".$txt_amount.",".$cbo_uom.",".$txt_color_id.",".$txt_remarks.",".$body_part.",".$txt_lot.",".$txt_gsm.",".$dia_width.",".$txt_book_currency.",".$txt_no_of_bags.",".$user_id.",'".$pc_date_time."',1,0)";

		//echo "insert into inv_scrap_receive_dtls (".$field_array_dtls.") values ".$data_array_dtls; die;
		//echo $rID = sql_insertss("inv_scrap_receive_mst",$field_array,$data_array,0);die;
		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("inv_scrap_receive_mst",$field_array,$data_array,0);			
		}
		else
		{
			//echo $a = sql_updatess("inv_scrap_receive_mst",$field_array_update,$data_array_update,"id",$row_id,1); die;
			$rID=sql_update("inv_scrap_receive_mst",$field_array_update,$data_array_update,"id",$row_id,1);
			
		}
		//echo $a = sql_insertss("inv_scrap_receive_mst",$field_array,$data_array,0); die;
		$rID2=sql_insert("inv_scrap_receive_dtls",$field_array_dtls,$data_array_dtls,0);
		//echo "10**".$rID."**".$rID2;die;
		if($rID) $flag=1; else $flag=0;
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "0**".$sys_challan_no."**".$row_id."**0";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "5**0**"."&nbsp;"."**0";
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".$sys_challan_no."**".$row_id."**0";
			}
			else
			{
				oci_rollback($con);
				echo "5**".$rID."**".$rID2;
			}
		}

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
		//echo "select id from inv_scrap_receive_dtls where id = $update_dtls_id and product_id='$hidden_pord_id' and trans_id='$txt_trans_id'  and status_active=1 and is_deleted=0 ";die;
		//$update_dtls_id=return_field_value("a.id as id"," inv_scrap_receive_dtls a","a.mst_id=$txt_system_id and a.product_id=$hidden_pord_id and a.trans_id=$txt_trans_id  and a.status_active=1 and a.is_deleted=0 ","id");
		//echo $update_dtls_id;die;
		$field_array_update_dtls="rate*amount*book_currency*no_of_bags*remarks*updated_by*update_date";
		$data_array_update_dtls=$txt_rate."*".$txt_amount."*".$txt_book_currency."*".$txt_no_of_bags."*".$txt_remarks."*".$user_id."*'".$pc_date_time."'";

		$sys_challan_no=str_replace("'","",$txt_system_no);
		$row_id=str_replace("'","",$update_id);
		$update_dtls_id=str_replace("'","",$update_dtls_id);


		//echo "update inv_scrap_receive_dtls set "
		$rID1=sql_update("inv_scrap_receive_dtls",$field_array_update_dtls,$data_array_update_dtls,"id",$update_dtls_id,0);
		//echo "55**".$rID1;die;
		if($rID1) $flag=1; else $flag=0;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".$sys_challan_no."**".$row_id."**".$update_dtls_id;
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
				echo "1**".$sys_challan_no."**".$row_id."**".$update_dtls_id;
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
	else if($operation ==2)
	{
		$con = connect(); 
		if($db_type==0)	{ mysql_query("BEGIN"); }
		// master table delete here---------------------------------------
		$mst_id = str_replace("'","",$update_id);
		$dtls_ids_arr = return_library_array( "select id, mst_id from inv_scrap_receive_dtls where mst_id in($mst_id)",'id','mst_id');
		//print_r($dtls_ids_arr);die(" with teddy bear");
		if($mst_id=="" || $mst_id==0)
		{ 
			echo "16**Delete not allowed. Problem occurred"; die;
		}
		else 
		{
			$update_id = str_replace("'","",$update_id);
			//$product_id = str_replace("'","",$current_prod_id);
			if( str_replace("'","",$update_id) == "" )
			{
				echo "16**Delete not allowed. Problem occurred";disconnect($con); die;
			}

			$sys_challan_no=str_replace("'","",$txt_system_no);
			//$row_id=str_replace("'","",$update_id);
			$row_id=str_replace("'","",$txt_system_id);
				
			$field_array_master="updated_by*update_date";
			$data_array_master="".$user_id."*'".$pc_date_time."'";

			$field_array_trans="updated_by*update_date*status_active*is_deleted";
			$data_array_trans="".$user_id."*'".$pc_date_time."'*0*1";
			
			if(count($dtls_ids_arr) > 1){
				$rID=sql_update("inv_scrap_receive_mst",$field_array_master,$data_array_master,"id",$update_id,1);
				$rID2=sql_update("inv_scrap_receive_dtls",$field_array_trans,$data_array_trans,"id",$update_dtls_id,1);
			}else{
				$rID=sql_update("inv_scrap_receive_mst",$field_array_trans,$data_array_trans,"id",$update_id,1);
				$rID2=sql_update("inv_scrap_receive_dtls",$field_array_trans,$data_array_trans,"mst_id",$update_id,1);
			}
			
		}

		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "2**".$sys_challan_no."**".$row_id."**".$update_dtls_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$sys_challan_no."**".$row_id."**".$update_dtls_id;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			
			if($rID && $rID2)
			{
				oci_commit($con);   
				echo "2**".$sys_challan_no."**".$row_id."**".$update_dtls_id;
			}
			else
			{
				oci_rollback($con);  
				echo "10**".$sys_challan_no."**".$row_id."**".$update_dtls_id;
			}
		}
		disconnect($con);
		die;
	}
}

?>
