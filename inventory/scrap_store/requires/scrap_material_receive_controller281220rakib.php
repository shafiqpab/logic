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
$userCredential = sql_select("select unit_id as company_id, item_cate_id, company_location_id, store_location_id from user_passwd where id=$user_id");
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
 //-------------------START --------------------------
$company_arr   = return_library_array("select id, company_name from lib_company","id","company_name");
$supplier_arr  = return_library_array("select id, short_name from lib_supplier","id","short_name");
$brand_arr     = return_library_array("select id, brand_name from lib_brand","id","brand_name");
$color_arr     = return_library_array("select id, color_name from lib_color","id","color_name");
$item_group_arr= return_library_array("select id, item_name from lib_item_group","id","item_name");

if($action == "load_drop_down_location")
{
	$data = explode('_',$data); 
    echo create_drop_down( "cbo_location", 170, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data[0]' $location_credential_cond order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/scrap_material_receive_controller', this.value+'_'+$data[0]+'_'+$data[1], 'load_drop_down_store', 'store_td');",0 );
	exit();
}

if ($action=="load_drop_down_store")
{
	$data = explode('_',$data);
 	echo create_drop_down( "cbo_store", 170, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[1]  and a.location_id =$data[0] and b.category_type=$data[2]  $store_credential_cond group by a.id,a.store_name order by a.store_name","id,store_name",1, "-- Select Store --", "", 0, "", 1 );
	exit();
}

if ($action=="load_drop_down_from_store")
{
 	echo create_drop_down( "cbo_from_store", 170, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and b.category_type=$data $store_credential_cond group by a.id,a.store_name order by a.store_name","id,store_name",1, "-- Select Store --", "", 0, "", 1 );
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
	if($db_type==0){
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	} else {
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
			//alert (id);return;
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
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company_id; ?>+'_'+<? echo $cbo_category_id; ?>+'_'+<? echo $cbo_from_store_name; ?>+'_'+<? echo $cbo_receive_basis; ?>, 'create_scrap_receive_search_list_view', 'search_div', 'scrap_material_receive_controller', 'setFilterGrid(\'list_view\',0)');" style="width:80px; margin: 0 auto;">
						</td>
					</tr>
					<tr>
						<td colspan="4"><? echo load_month_buttons(1);  ?></td>
					</tr>
				</tbody>
			</table>
			<br>
			<div valign="top" id="search_div" align="left"></div>
		</form>
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<?
	exit();
}

if($action=="create_scrap_receive_search_list_view")
{
    $data=explode('_',$data);
	//echo '<pre>';print_r($data);
	$brand_arr=return_library_array("select id, brand_name from lib_brand",'id','brand_name');
	$store_arr=return_library_array("select id, store_name from lib_store_location",'id','store_name');
	$item_group_arr=return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$body_part_array=return_library_array("select id, body_part_full_name from lib_body_part",'id','body_part_full_name');
	$prod_wise_body_part_array=return_library_array("select id, prod_id, body_part_id from pro_grey_prod_entry_dtls",'prod_id','body_part_id');
	$previous_scrap_received_arr=return_library_array("select a.id, a.trans_id from inv_scrap_receive_dtls a where a.trans_id>0 and a.trans_id is not null and a.status_active=1",'id','trans_id');
	/*$previous_scrap_received_arr_cond='';
	if (!empty($previous_scrap_received_arr))
		$previous_scrap_received_arr_cond = "and a.id not in(".implode(",",$previous_scrap_received_arr).")";*/
	//print_r($prod_wise_body_part_array);
	if (!empty($previous_scrap_received_arr))
    {     
        $previous_scrap_received_cond = '';
        if($db_type==2 && count($previous_scrap_received_arr)>999)
        {
            $scrap_receivedIds = array_keys($previous_scrap_received_arr);
            $previous_scrap_received_cond = ' and (';

            $scrap_receivedIdArr = array_chunk($scrap_receivedIds,999);
            foreach($scrap_receivedIdArr as $ids)
            {
                $ids = implode(',',$ids);
                $previous_scrap_received_cond .= " a.id not in($ids) and ";
            }
            
            $previous_scrap_received_cond = rtrim($previous_scrap_received_cond,'and ');
            $previous_scrap_received_cond .= ')';
        }
        else
        {
            $scrap_receivedIds = implode(',',array_keys($previous_scrap_received_arr));
            $previous_scrap_received_cond = " and a.id not in ($scrap_receivedIds) ";
        }
    }
	$item_category_id = $data[3];
	$receive_basis = $data[5];
	if ($data[2]!=0) $company=" and a.company_id='$data[2]'"; else $company="";
	if ($data[3]!=0) $category_cond=" and a.item_category='$data[3]'"; else $category_cond="";
	if ($data[4]!=0) $store_cond=" and a.store_id='$data[4]'"; else $store_cond="";
	
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $transaction_date_cond = "and a.transaction_date between '".change_date_format($data[0],'yyyy-mm-dd')."' and '".change_date_format($data[1],'yyyy-mm-dd')."'"; else $transaction_date_cond ="";
	}
	else
	{
		if ($data[0]!="" &&  $data[1]!="") $transaction_date_cond = "and a.transaction_date between '".change_date_format($data[0], "", "",1)."' and '".change_date_format($data[1], "", "",1)."'"; else $transaction_date_cond ="";
	}
	// Lot exits only 1=>Yarn, 5=>Chemicals, 6=>Dyes, 7=>Auxilary Chemicals, 23=>Dyes Chemicals & Auxilary Chemicals 
	// 5,6,7,23 batch_lot field of inv_transaction and 1 lot field of product_details_master
	if ($item_category_id==5 || $item_category_id==6 || $item_category_id==7 || $item_category_id==23)
	{
		$selet_lot_cond="a.batch_lot";
	} else $selet_lot_cond="b.lot";

	if($receive_basis == 1) //scrap receive basis -> Receive-Reject
	{
		$transaction_type_cond = "  and a.transaction_type in(1,4,5)";	
		$reject_qnty_cond = " and a.cons_reject_qnty > 0";
		$sql= "SELECT a.id as TRANS_ID, a.MST_ID, a.STORE_ID, a.TRANSACTION_DATE, a.cons_reject_qnty as CONS_QNTY, a.TRANSACTION_TYPE, a.BODY_PART_ID, a.PI_WO_BATCH_NO, $selet_lot_cond as LOT, b.id as PROD_ID, b.ITEM_GROUP_ID, b.ITEM_CATEGORY_ID, b.PRODUCT_NAME_DETAILS, b.UNIT_OF_MEASURE, b.CURRENT_STOCK, b.COLOR, b.ITEM_COLOR, b.GSM, b.BRAND, b.DIA_WIDTH, b.ITEM_SIZE, b.AVG_RATE_PER_UNIT
		from inv_transaction a, product_details_master b
		where b.id=a.prod_id $transaction_type_cond $company $category_cond $transaction_date_cond $store_cond $reject_qnty_cond $previous_scrap_received_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id desc";  //b.LOT,
	} 
	elseif ($receive_basis == 2) //scrap receive basis -> Issue Damage
	{
		$transaction_type_cond = "  and a.transaction_type in(2,3,6) ";
		$reject_qnty_cond = " and a.cons_quantity > 0";
		/*$trns_mst_sql = "select listagg(id, ',') within group(order by id) as id from inv_issue_master where company_id=$data[2] and item_category = $data[3] and issue_purpose in(26)"; //and store_id=$data[4]
		//echo $trns_mst_sql;
		$trans_mst_id = sql_select($trns_mst_sql);*/
		$sql= "SELECT a.id as TRANS_ID, a.MST_ID, a.STORE_ID, a.TRANSACTION_DATE, a.cons_quantity as CONS_QNTY,a.TRANSACTION_TYPE, a.BODY_PART_ID, a.PI_WO_BATCH_NO, b.id as PROD_ID, b.ITEM_GROUP_ID, b.ITEM_CATEGORY_ID, b.PRODUCT_NAME_DETAILS, b.UNIT_OF_MEASURE, $selet_lot_cond as LOT, b.CURRENT_STOCK, b.COLOR, b.ITEM_COLOR, b.GSM, b.BRAND, b.DIA_WIDTH, b.ITEM_SIZE, b.AVG_RATE_PER_UNIT 
		from inv_issue_master c, inv_transaction a, product_details_master b 
		where c.id=a.mst_id and a.prod_id=b.id and c.issue_purpose=1 $transaction_type_cond $company $category_cond  $transaction_date_cond $store_cond $reject_qnty_cond $previous_scrap_received_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by b.id desc";
	} 
	elseif ($receive_basis == 3) //scrap receive basis -> Issue Scrap store
	{
		$transaction_type_cond = "  and a.transaction_type in(2,3,6)";
		$reject_qnty_cond = " and a.cons_quantity > 0";
		/*$trans_mst_id = sql_select("select listagg(id, ',') within group(order by id) as id from inv_issue_master where company_id=$data[2] and item_category = $data[3]  and and issue_purpose in(31)");*/ //and store_id=$data[4]

		$sql= "SELECT a.id as TRANS_ID, a.MST_ID, a.STORE_ID, a.TRANSACTION_DATE, a.cons_quantity as CONS_QNTY,a.TRANSACTION_TYPE, a.BODY_PART_ID, a.PI_WO_BATCH_NO, b.id as PROD_ID, b.ITEM_GROUP_ID, b.ITEM_CATEGORY_ID, b.PRODUCT_NAME_DETAILS, b.UNIT_OF_MEASURE, $selet_lot_cond as LOT, b.CURRENT_STOCK, b.COLOR, b.ITEM_COLOR, b.GSM, b.BRAND, b.DIA_WIDTH, b.ITEM_SIZE, b.AVG_RATE_PER_UNIT 
		from inv_issue_master c, inv_transaction a, product_details_master b
		where c.id=a.mst_id and a.prod_id=b.id and c.issue_purpose=31 $transaction_type_cond $company $category_cond  $transaction_date_cond $store_cond $reject_qnty_cond $previous_scrap_received_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by b.id desc ";
	}    
	//echo $sql;
	$description_resutl = sql_select($sql);
	foreach ($description_resutl as  $value) {
		if($value["TRANSACTION_TYPE"] == 1 || $value["TRANSACTION_TYPE"] == 4 || $value["TRANSACTION_TYPE"] == 5)
		{
			$receive_id_arr[$value["TRANS_ID"]] = $value["MST_ID"];	
			$rcv_ids = implode(",", $receive_id_arr);
			
		}else{
			$issue_id_arr[$value["TRANS_ID"]] = $value["MST_ID"];
			$iss_ids = implode(",", $issue_id_arr);
		}
		$batch_ids_array[$value["TRANS_ID"]] =  $value["PI_WO_BATCH_NO"];
	}
	//print_r($iss_ids);die;
	$batch_ids =  implode(",", $batch_ids_array);
	$receive_ids_arr = return_library_array( "select id, recv_number from inv_receive_master where id in($rcv_ids)",'id','recv_number');
	$issue_ids_arr = return_library_array( "select id, issue_number from inv_issue_master where id in($iss_ids)",'id','issue_number');
	$batch_arr = return_library_array( "select id, batch_no from PRO_BATCH_CREATE_MST where id in($batch_ids)",'id','BATCH_NO');
	//$issue_purpose_arr = return_library_array( "select id, issue_purpose from inv_issue_master where id in($iss_ids)",'id','issue_purpose');
	$sys_challan_no_array = $receive_ids_arr;
	foreach ($issue_ids_arr as $key => $value) {
		array_push($sys_challan_no_array, $key, $value);
	}
	//print_r($sys_challan_no_array);//die;
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100" class="rpt_table" id="html_search">
		<thead>
			<tr>
				<th width="40" align="center">SL</th>
				<th width="80" align="center">Product Id</th>
				<th width="100" align="center">System ID</th>
				<th width="100" align="center">Item Category</th>
				<th width="150" align="center">Description</th>
				<th width="100" align="center">Store</th>
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
			foreach ($description_resutl as $value) 
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if ($value["ITEM_CATEGORY_ID"] == 13) {
					$body_part_val = $body_part_array[$prod_wise_body_part_array[$value["PROD_ID"]]];
				} else if ($value["ITEM_CATEGORY_ID"] == 2) {
					$body_part_val = $body_part_array[$value["BODY_PART_ID"]];
					$lot_batch = $batch_arr[$value["PI_WO_BATCH_NO"]];
				} else {
					$lot_batch = $value["LOT"];
				}
				$avg_rate_per_unit=number_format($value["AVG_RATE_PER_UNIT"],2,'.','');
				?>
				<tr bgcolor="<?= $bgcolor; ?>" style="cursor:pointer;" onClick='js_set_value("<? echo $value["PRODUCT_NAME_DETAILS"].'__'.$lot_batch.'__'.$value["UNIT_OF_MEASURE"].'__'.$color_arr[$value["COLOR"]].'__'.$item_group_arr[$value["ITEM_GROUP_ID"]].'__'.$value["PROD_ID"].'__'.$value["COLOR"].'__'.$value["ITEM_GROUP_ID"].'__'.$value["CONS_QNTY"].'__'.$value["TRANS_ID"].'__'.$sys_challan_no_array[$value["MST_ID"]].'__'.$value["MST_ID"].'__'.$value["GSM"].'__'.$value["DIA_WIDTH"].'__'.$body_part_val.'__'.$value["STORE_ID"].'__'.$avg_rate_per_unit;?>")'>
					<td width="40" align="center"><?= $i;?></td>
					<td width="80" align="center"><p><?= $value["PROD_ID"]; ?></p></td>
					<td width="100" align="center">
						<? 
							if ($value["TRANSACTION_TYPE"]== 1 || $value["TRANSACTION_TYPE"]== 4 || $value["TRANSACTION_TYPE"]== 5){
								echo $receive_ids_arr[$value["MST_ID"]];
							}else{
								echo $issue_ids_arr[$value["MST_ID"]];
							}
						?>
					</td>
					<td width="100" align="center"><p><?= $item_category[$value["ITEM_CATEGORY_ID"]]; ?></p></td>
					<td width="150" align="center"><p><?= $value["PRODUCT_NAME_DETAILS"]; ?></p></td>
					<td width="100" align="center"><p><?= $store_arr[$value["STORE_ID"]]; ?></p></td>
					<td width="80" align="center" style="word-wrap: break-all;"><p><?= $lot_batch; ?></p></td>
					<td width="60" align="center"><p><?= $unit_of_measurement[$value["UNIT_OF_MEASURE"]]; ?></p></td>
					<td width="70" align="center"><p><?= $color_arr[$value["COLOR"]]; ?></p></td>
					<td width="60" align="center"><p><?= $value["GSM"]; ?></p></td>
					<td width="70" align="center"><p><?= $brand_arr[$value["BRAND"]]; ?></p></td>
					<td width="80" align="center"><p><?= change_date_format($value["TRANSACTION_DATE"]); ?></p></td>
					<td align="right"><p><?= $value["CONS_QNTY"]; ?></p></td>
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

			$id=return_next_id( "id", "inv_scrap_receive_mst", 1);
			$field_array="id, receive_no_prefix,receive_no_prefix_num,sys_receive_no,company_id,item_category_id,location,store_id,from_store_id,entry_form,receive_date,receive_basis,currency,exchange_rate,challan_no,mst_id,inserted_by,insert_date,status_active,is_deleted";

			$data_array="(".$id.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$cbo_company_name.",".$cbo_category_id.",".$cbo_location.",".$cbo_store.",".$cbo_from_store.",0,".$txt_receive_date.",".$cbo_receive_basis.",".$cbo_currency.",".$txt_exchange_rate.",".$txt_system_challan_no.",".$txt_mst_id.",".$user_id.",'".$pc_date_time."',1,0)";
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

		$field_array_dtls="id, mst_id, item_group_id, product_id, trans_id, receive_qnty, rate, amount, uom, color, remarks, body_part, lot, gsm, dia, book_currency, no_of_bags, from_store_id, inserted_by, insert_date, status_active, is_deleted";

		$data_array_dtls="(".$id_dtls.",".$row_id.",".$hidd_item_group_id.",".$hidden_pord_id.",".$txt_trans_id.",".$txt_receive_qty.",".$txt_rate.",".$txt_amount.",".$cbo_uom.",".$txt_color_id.",".$txt_remarks.",".$body_part.",".$txt_lot.",".$txt_gsm.",".$dia_width.",".$txt_book_currency.",".$txt_no_of_bags.",".$hidden_store_from_id.",".$user_id.",'".$pc_date_time."',1,0)";

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
			echo "16**Delete not allowed. Problem occurred"; disconnect($con);die;
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
			$sql_list_view = "SELECT a.ID, b.MST_ID, b.RECEIVE_QNTY, b.RATE, b.AMOUNT, b.REMARKS, b.BODY_PART, b.DIA_TYPE, b.BOOK_CURRENCY, b.NO_OF_BAGS, b.TRANS_ID, b.ITEM_GROUP_ID, c.id as PRODUCT_ID, c.PRODUCT_NAME_DETAILS, c.UNIT_OF_MEASURE, c.COLOR, b.LOT, c.GSM, c.DIA_WIDTH  from  inv_scrap_receive_mst a, inv_scrap_receive_dtls b, product_details_master c	where a.id=b.mst_id and b.product_id = c.id and b.mst_id='$data' and b.status_active=1 and b.status_active=1 and a.status_active=1 order by id desc";
			//echo $sql_list_view;
 			$sqlResult =sql_select($sql_list_view);

			foreach($sqlResult as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
                <tr bgcolor="<?= $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<? echo $row['ID'].'_'.$row['TRANS_ID'].'_'.$row['PRODUCT_ID']; ?>','populate_scrap_form_data','requires/scrap_material_receive_controller');" >
                    <td width="30" align="center"><?= $i; ?></td>
                    <td width="50" align="center"><p><?= $row['PRODUCT_ID']; ?></p></td>
                    <td width="80" align="right"><p><?= $item_group_arr[$row['ITEM_GROUP_ID']]; ?></p></td>
                    <td width="150"><p><?= $row['PRODUCT_NAME_DETAILS']; ?></p></td>
                    <td width="80" align="center"><p><?= $row['LOT']; ?></p></td>
                    <td width="50" align="center"><p><?= $unit_of_measurement[$row['UNIT_OF_MEASURE']]; ?></p></td>
                    <td width="80" align="right"><p><?= $row['RECEIVE_QNTY']; ?></p></td>
                    <td width="70" align="right"><p><?= number_format($row['RATE'],2); ?></p></td>
                    <td width="70" align="right"><p><?= number_format($row['AMOUNT'],2); ?></p></td>
                    <td align="center"><p><?= $row['REMARKS']; ?></p></td>
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

if ($action=="populate_scrap_form_data")
{
	$data=explode('_',$data);
	$product_name_arr = return_library_array("select id, product_name_details from product_details_master","id","product_name_details");
	$sql_name_array = "SELECT a.ID, b.id as DTLS_ID, b.MST_ID, b.RECEIVE_QNTY, b.RATE, b.AMOUNT, b.FROM_STORE_ID, b.REMARKS, b.BODY_PART, b.DIA_TYPE, b.BOOK_CURRENCY, b.NO_OF_BAGS, c.ITEM_GROUP_ID, c.id as PRODUCT_ID, c.PRODUCT_NAME_DETAILS, c.UNIT_OF_MEASURE, c.COLOR, b.LOT, c.GSM, c.DIA_WIDTH from inv_scrap_receive_mst a, inv_scrap_receive_dtls b, product_details_master c	where a.id=b.mst_id and b.product_id=c.id and b.mst_id='$data[0]' and b.trans_id='$data[1]' and b.product_id='$data[2]' and b.status_active=1 and b.status_active=1 and a.status_active=1 and c.status_active=1 and c.is_deleted=0 order by id desc";
	//echo $sql_name_array;
	$nameArray=sql_select( $sql_name_array);
	foreach ($nameArray as $row)
	{

		$txt_rate=number_format($row["RATE"],2,'.','');
		echo "document.getElementById('txt_item_desc').value	 = '".$row["PRODUCT_NAME_DETAILS"]."';\n";
		echo "document.getElementById('cbo_item_group').value	 = '".$item_group_arr[$row["ITEM_GROUP_ID"]]."';\n";
		echo "document.getElementById('body_part').value		 = '".$row["BODY_PART"]."';\n";
		echo "document.getElementById('txt_gsm').value		     = '".$row["GSM"]."';\n";
		echo "document.getElementById('dia_width').value		 = '".$row["DIA_WIDTH"]."';\n";
		echo "document.getElementById('txt_lot').value	         = '".$row["LOT"]."';\n";
		echo "document.getElementById('txt_remarks').value		 = '".$row["REMARKS"]."';\n";
		echo "document.getElementById('txt_receive_qty').value	 = '".$row["RECEIVE_QNTY"]."';\n";
		echo "document.getElementById('hdn_receive_qty').value	 = '".$row["RECEIVE_QNTY"]."';\n";
		echo "document.getElementById('txt_rate').value	         = '".$txt_rate."';\n";
		echo "document.getElementById('txt_amount').value		 = '".$row["AMOUNT"]."';\n";
		echo "document.getElementById('cbo_uom').value	         = '".$row["UNIT_OF_MEASURE"]."';\n";
		echo "document.getElementById('txt_book_currency').value = '".$row["BOOK_CURRENCY"]."';\n";
		//echo "document.getElementById('dia_width_type').value	 = '".$row["dia_type"]."';\n";
		echo "document.getElementById('txt_color').value		 = '".$color_arr[$row["COLOR"]]."';\n";
		echo "document.getElementById('txt_no_of_bags').value	 = '".$row["NO_OF_BAGS"]."';\n";
		echo "document.getElementById('update_id').value		 = '".$row["ID"]."';\n";
		echo "document.getElementById('update_dtls_id').value	 = '".$row["DTLS_ID"]."';\n";
		echo "document.getElementById('hidden_store_from_id').value = '".$row["FROM_STORE_ID"]."';\n";

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
	$sql_name_array = "SELECT a.ID, a.COMPANY_ID, a.LOCATION, a.STORE_ID, a.FROM_STORE_ID, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.ITEM_CATEGORY_ID, b.id as DTLS_ID, b.PRODUCT_ID, b.TRANS_ID  from  inv_scrap_receive_mst a, inv_scrap_receive_dtls b, product_details_master c	where a.id=b.mst_id and b.product_id=c.id and b.mst_id='$data' and b.status_active=1 and b.status_active=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by id desc";
	$nameArray=sql_select( $sql_name_array);
	foreach ($nameArray as $row)
	{
		echo "load_drop_down( 'requires/scrap_material_receive_controller', '".$row["LOCATION"]."_".$row["COMPANY_ID"]."_".$row["ITEM_CATEGORY_ID"]."', 'load_drop_down_store', 'store_td');\n";

		echo "document.getElementById('cbo_location').value		= '".$row["LOCATION"]."';\n";
		echo "document.getElementById('cbo_store').value		= '".$row["STORE_ID"]."';\n";
		echo "document.getElementById('cbo_from_store').value	= '".$row["FROM_STORE_ID"]."';\n";
		echo "document.getElementById('txt_trans_id').value		= '".$row["TRANS_ID"]."';\n";
		echo "document.getElementById('hidden_pord_id').value	= '".$row["PRODUCT_ID"]."';\n";
		echo "document.getElementById('txt_receive_date').value	= '".change_date_format($row["RECEIVE_DATE"],"yyyy-mm-dd")."';\n";
		echo "document.getElementById('cbo_receive_basis').value= '".$row["RECEIVE_BASIS"]."';\n";
		echo "document.getElementById('update_id').value        = '".$row["ID"]."';\n";
		echo "document.getElementById('update_dtls_id').value   = '".$row["DTLS_ID"]."';\n";


		echo "$('#body_part').prop('disabled',true);\n";
		echo "$('#txt_gsm').prop('disabled',true);\n";
		echo "$('#dia_width').prop('disabled',true);\n";
		//echo "$('#dia_width_type').prop('disabled',true);\n";
		echo "$('#txt_item_desc').prop('disabled',true);\n";
		echo "$('#cbo_location').prop('disabled',true);\n";
		echo "$('#cbo_store').prop('disabled',true);\n";
		echo "$('#cbo_from_store').prop('disabled',true);\n";
		echo "$('#txt_receive_date').prop('disabled',true);\n";
		echo "$('#cbo_receive_basis').prop('disabled',true);\n";
		echo "$('#cbo_category_id').prop('disabled',true);\n";
		echo "$('#cbo_company_name').prop('disabled',true);\n";
		echo "$('#cbo_item_group').prop('disabled',true);\n";

		echo "set_button_status(1,'".$_SESSION['page_permission']."', 'fnc_scrap_material_receive_entry',1,1);\n";
	}
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
						<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company;?>+'_'+<? echo $cbo_category_id;?>+'_'+document.getElementById('txt_search_common').value, 'create_mrr_scrap_receive_search_list_view', 'mrr_search_div', 'scrap_material_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;">
					</td>
				</tr>
				<tr>
					<td colspan="5"><? echo load_month_buttons(1);  ?></td>
				</tr>
			</tbody>
		</table>
		<br>
		<div valign="top" id="mrr_search_div" align="center"></div>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>	
	<?
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
		if ($data[2]!="" &&  $data[3]!="") $receive_date_cond = "and a.receive_date between '".change_date_format($data[0],'yyyy-mm-dd')."' and '".change_date_format($data[1],'yyyy-mm-dd')."'"; else $receive_date_cond ="";
	}
	else
	{
		if ($data[0]!="" &&  $data[1]!="") $receive_date_cond = "and a.receive_date between '".change_date_format($data[0], "", "",1)."' and '".change_date_format($data[1], "", "",1)."'"; else $receive_date_cond ="";
	}

	
    $sql= "SELECT a.ID, a.SYS_RECEIVE_NO, a.COMPANY_ID, a.ITEM_CATEGORY_ID, a.LOCATION, a.STORE_ID, a.RECEIVE_DATE, a.RECEIVE_BASIS, b.RECEIVE_QNTY, b.PRODUCT_ID, b.LOT
    from inv_scrap_receive_mst a, inv_scrap_receive_dtls b
    where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0  and b.is_deleted =0  and b.status_active=1 $company_cond $category_cond $receive_date_cond $search_cond order by b.id desc ";
	//echo $sql;
	$description_resutl = sql_select($sql);

	?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table" id="html_search" align="left">
			<thead>
				<tr>
					<th width="40" align="center">SL</th>
					<th width="120" align="center">Receive Number</th>
					<th width="100" align="center">Product Id</th>
					<th width="120" align="center">Item Category</th>
					<th width="120" align="center">Lot/Batch</th>
					<th width="120" align="center">Location</th>
					<!-- <th width="80" align="center">Store</th> -->
					<th width="80" align="center">receive_date</th>
					<th align="center">receive_basis</th>
				</tr>
			</thead>
			<tbody>
				<?
					$i=1;
					foreach ($description_resutl as $value) 
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<?= $bgcolor; ?>" style="cursor:pointer;" onClick='js_set_value("<?= $value["ID"]; ?>","<?= $value["SYS_RECEIVE_NO"]; ?>")'>
							<td width="40" align="center"><?= $i; ?></td>
							<td width="120" align="center"><p><?= $value["SYS_RECEIVE_NO"]; ?></p></td>
							<td width="100" align="center"><p><?= $value["PRODUCT_ID"]; ?></p></td>
							<td width="120" align="center"><p><?= $item_category[$value["ITEM_CATEGORY_ID"]]; ?></p></td>
							<td width="120" align="center" style="word-wrap: break-all;"><p><?= $value["LOT"]; ?></p></td>
							<td width="120" align="center"><p><?= $location_arr[$value["LOCATION"]]; ?></p></td>
							<!-- <td align="center"><p><? //echo $value["store_id"]; ?></p></td> -->
							<td width="80" align="center"><p><?= change_date_format($value["RECEIVE_DATE"],'yyyy-mm-dd'); ?></p></td>
							<td align="center"><p><?= $receive_scrap_arra[$value["RECEIVE_BASIS"]]; ?></p></td>
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

?>
