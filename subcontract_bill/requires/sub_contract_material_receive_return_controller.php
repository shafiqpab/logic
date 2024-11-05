<?php
include('../../includes/common.php');
session_start();

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=='load_drop_down_party')
{
	echo create_drop_down( 'cbo_party_name', 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data' $buyer_cond  and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buyer_name", 'id,buyer_name', 1, '-- Select Party --', $selected, '');
	exit();
} 

if ($action=='receive_popup') {
	echo load_html_head_contents('Popup Info', '../../', 1, 1, $unicode, '', '');
?>
	<script>
		var companyId = '<?php echo $data; ?>';
		function js_set_value(id) {
			document.getElementById('hidden_rcv_mst_id').value = id;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="searchreceivefrm_1"  id="searchreceivefrm_1" autocomplete="off">
                <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="6"><?php echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                    	<tr>
                            <th width="150">Party</th>
                            <th width="150">Search By</th>
                            <th width="80">Receive ID</th>
                            <th width="170">Date Range</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>         
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td id="buyer_td">
								<?php
									echo create_drop_down( 'cbo_party_name', 160, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name", 'id,buyer_name', 1, '-- Select Party --', $data, '' );
                                ?>
                            </td>
                            <td>
                                <?php
                                	$searchBy = [1 => 'Receive ID', 2 => 'Receive Challan','Issue Return','Job No','Order No'];
									echo create_drop_down( 'cbo_search_by', 160, $searchBy, 0, '', 0, '');
                                ?>
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width: 160px" placeholder="Recv Prefix no" />
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:65px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+companyId, 'create_receive_search_list_view', 'search_div', 'sub_contract_material_receive_return_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" />
                            </td>
                            <input type="hidden" name="hidden_rcv_mst_id" id="hidden_rcv_mst_id">
                        </tr>
                        <tr>
                            <td colspan="6" align="center" valign="middle">
								<?php echo load_month_buttons(1); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>  
                <div id="search_div"></div>  
            </form>
        </div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
if($action == 'create_receive_search_list_view') {
	$data=explode('_',$data);
	$party = $data[0];
	$dateFrom = $data[1];
	$dateTo = $data[2];
	$searchBy = $data[3];
	$searchText = $data[4];
	$search_type = $data[5];
	$company = $data[6];
	$search_cond = '';
	$search_cond2 = '';

	$company_cond = "and a.company_id=$company";
	if($party==0 && $searchText=="" && $dateFrom=="")
	{
		echo "<div align='center'> <b>Please select or write anyone in search panel.</b></div>";die;
	}

	// echo $search_type;
	if ($dateFrom=="" && $dateTo=="") {
		$dateFrom = '01-Jan-'.date('Y');
		$dateTo = '31-Dec-'.date('Y');
	}

	if ($party!=0) $party_cond = " and a.party_id=$party"; else $party_cond="";
	
	if($db_type==0)
	{ 
		if ($dateFrom!="" && $dateTo!="") $recieve_date = "and a.subcon_date between '".change_date_format($dateFrom,'yyyy-mm-dd')."' and '".change_date_format($dateTo,'yyyy-mm-dd')."'"; else $recieve_date ="";
	}
	else
	{
		if ($dateFrom!="" && $dateTo!="") $recieve_date = "and a.subcon_date between '".change_date_format($dateFrom, "", "",1)."' and '".change_date_format($dateTo, "", "",1)."'"; else $recieve_date ="";
	}

	if($search_type==1)
	{
		if ($searchBy == 1 && $searchText != '') $search_cond =" and a.prefix_no_num='$searchText'";
		if ($searchBy == 2 && $searchText != '') $search_cond =" and a.chalan_no='$searchText'";
		if ($searchBy == 3 && $searchText != '') $search_cond =" and a.prefix_no_num='$searchText'";
		if ($searchBy == 4 && $searchText != '') $search_cond2 =" and job_no_mst='$searchText'";
		if ($searchBy == 5 && $searchText != '') $search_cond2 =" and c.order_no='$searchText'";
	}
	else if($search_type==4 || $search_type==0)
	{
		// echo "$searchBy, $searchText";
		if ($searchBy == 1 && $searchText != '') $search_cond =" and a.prefix_no_num like '%$searchText%'";
		if ($searchBy == 2 && $searchText != '') $search_cond =" and a.chalan_no like '%$searchText%'";
		if ($searchBy == 3 && $searchText != '') $search_cond =" and a.prefix_no_num like '%$searchText%'";
		if ($searchBy == 4 && $searchText != '') $search_cond2 =" and job_no_mst like '%$searchText%'";
		if ($searchBy == 5 && $searchText != '') $search_cond2 =" and c.order_no like '%$searchText%'";
	}
	else if($search_type==2)
	{
		if ($searchBy == 1 && $searchText != '') $search_cond =" and a.prefix_no_num like '$searchText%'";
		if ($searchBy == 2 && $searchText != '') $search_cond =" and a.chalan_no like '$searchText%'";
		if ($searchBy == 3 && $searchText != '') $search_cond =" and a.prefix_no_num like '$searchText%'";
		if ($searchBy == 4 && $searchText != '') $search_cond2 =" and job_no_mst like '$searchText%'";
		if ($searchBy == 5 && $searchText != '') $search_cond2 =" and c.order_no like '$searchText%'";
	}
	else if($search_type==3)
	{
		if ($searchBy == 1 && $searchText != '') $search_cond =" and a.prefix_no_num like '%$searchText'";
		if ($searchBy == 2 && $searchText != '') $search_cond =" and a.chalan_no like '%$searchText'";
		if ($searchBy == 3 && $searchText != '') $search_cond =" and a.prefix_no_num like '%$searchText'";
		if ($searchBy == 4 && $searchText != '') $search_cond2 =" and job_no_mst like '%$searchText'";
		if ($searchBy == 5 && $searchText != '') $search_cond2 =" and c.order_no like '%$searchText'";
	}
	if($searchBy == 1 || $searchBy == 2){
		$trans_type=1;
	}
	else if($searchBy == 3){
		$trans_type=3;
	}
	else{
		$trans_type='1,3';
	}

	// $sub_order_sql="SELECT ID, JOB_NO_MST, ORDER_NO FROM SUBCON_ORD_DTLS WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 $search_cond2";
	//   echo $sub_order_sql;die;
	// $sub_order_sql_result = sql_select($sub_order_sql);
	// if (empty($sub_order_sql_result)) 
	// {
	// 	echo "Data Not Found";die;
	// }
	// $order_id_arr=array();
	// foreach ($sub_order_sql_result as $key => $val) 
	// {
	// 	$order_id_arr[$val['ID']]=$val['ID'];
	// 	$po_arr[$val['ID']]=$val['ORDER_NO'];
	// }
	// $subCon_order_id=implode(",", array_unique($order_id_arr));
	// // echo $subCon_order_id;
	// $order_job_cond="";
	// if (!empty($order_id_arr)) 
	// {
	// 	$order_job_cond= "and b.order_id in($subCon_order_id)";
	// }

	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	//$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
	$arr=array (2=>$party_arr,5=>$item_category);
	
	if($db_type==0)
	{
		$year_cond= "year(a.insert_date)as year";
	}
	else if($db_type==2)
	{
		$year_cond= "TO_CHAR(a.insert_date,'YYYY') as year";
	}

	$sql= "SELECT a.id, a.sys_no, a.prefix_no_num, $year_cond, a.location_id, a.party_id, a.subcon_date, a.chalan_no, a.remarks, b.order_id, sum(b.quantity) as quantity, a.entry_form, a.trans_type,c.order_no, b.id as dtls_id 
	from sub_material_mst a, sub_material_dtls b ,SUBCON_ORD_DTLS c
	where a.id=b.mst_id and b.order_id=c.id and a.entry_form in(288, 343, 344) and a.trans_type in ($trans_type) and a.status_active=1 and a.is_deleted=0 and  b.status_active=(CASE WHEN entry_form = 344 THEN 1 ELSE 2 END) and b.is_deleted=0 $recieve_date $company_cond $party_cond $search_cond $order_job_cond
	group by a.id, a.sys_no, a.prefix_no_num, a.insert_date, a.location_id, c.order_no,a.party_id, a.subcon_date, a.chalan_no, a.remarks, b.order_id, a.entry_form, a.trans_type, b.id order by a.prefix_no_num desc ";
	 //  echo $sql;

	$result = sql_select($sql);
	$rcv_array = array();

	foreach ($result as $row) 
	{
		$rcv_array[$row[csf('prefix_no_num')]]['id'] = $row[csf('id')];
		$rcv_array[$row[csf('prefix_no_num')]]['sys_no'] = $row[csf('sys_no')];
		$rcv_array[$row[csf('prefix_no_num')]]['prefix_no_num'] = $row[csf('prefix_no_num')];
		$rcv_array[$row[csf('prefix_no_num')]]['year'] = $row[csf('year')];
		$rcv_array[$row[csf('prefix_no_num')]]['location_id'] = $row[csf('location_id')];
		$rcv_array[$row[csf('prefix_no_num')]]['party_id'] = $row[csf('party_id')];
		$rcv_array[$row[csf('prefix_no_num')]]['subcon_date'] = $row[csf('subcon_date')];
		$rcv_array[$row[csf('prefix_no_num')]]['chalan_no'] = $row[csf('chalan_no')];
		$rcv_array[$row[csf('prefix_no_num')]]['remarks'] = $row[csf('remarks')];
		$rcv_array[$row[csf('prefix_no_num')]]['order_id'] = $row[csf('order_id')];
		$rcv_array[$row[csf('prefix_no_num')]]['trans_type'] = $row[csf('trans_type')];
		$rcv_array[$row[csf('prefix_no_num')]]['entry_form'] = $row[csf('entry_form')];
		$rcv_array[$row[csf('prefix_no_num')]]['dtls_id'] = $row[csf('dtls_id')];

		$po_arr[$row[csf('order_id')]] = $row[csf('order_no')];

		if($row[csf('entry_form')] == 288) {
			$rcv_array[$row[csf('prefix_no_num')]]['receive_qty'] += $row[csf('quantity')];
		} 
		else if($row[csf('entry_form')] == 344) {
			$rcv_array[$row[csf('prefix_no_num')]]['issue_rtn_qty'] += $row[csf('quantity')];
		}
		else {
			$rcv_array[$row[csf('prefix_no_num')]]['issue_qty'] += $row[csf('quantity')];
		}
		$rcv_dtls_id_arr[$row[csf('dtls_id')]]=$row[csf('dtls_id')];
	}
	$user_id=$_SESSION['logic_erp']['user_id'];
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1) and ENTRY_FORM=121");
	$con = connect();
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 121, 1, $rcv_dtls_id_arr, $empty_arr);
	
	$rcv_rtn_data=sql_select("SELECT b.receive_dtls_id, b.quantity from sub_material_return_mst a join sub_material_return_dtls b on a.id=b.mst_id join gbl_temp_engine c on b.receive_dtls_id=c.ref_val where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.ref_from=1 and c.entry_form=121 and c.user_id=$user_id");
	$rcv_rtn_qty_arr=array();
	if(count($rcv_rtn_data)>0){
		foreach($rcv_rtn_data as $row){
			$rcv_rtn_qty_arr[$row[csf('receive_dtls_id')]]+=$row[csf('quantity')];
		}
	}

	/*echo '<pre>';
	print_r($rcv_array);
	echo '</pre>';*/

	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="717" class="rpt_table">
            <thead>
                <th width="40">SL</th>
                <th width="70">Receive ID</th>
                <th width="70">Year</th>
                <th width="120">Supplier Name</th>
                <th width="100">Challan No</th>
                <th width="80">Receive Date</th>
                <th width="80">Receive Qty</th>
                <th>Balance Qty</th>
            </thead>
     	</table>
     <div style="width:720px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table" id="tbl_po_list">
			<?php
			$trans_type_arr=array(1=>'Receive',2=>'Issue', 3=>'Issue Return');
			$i=1;
            foreach( $rcv_array as $row ) {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($row['entry_form']==344){
					$rcvQty = $row['issue_rtn_qty'];
                	$balanceQty = $rcvQty - $rcv_rtn_qty_arr[$row['dtls_id']];
				}
				else{
					$rcvQty = $row['receive_qty'];
                	$balanceQty = $rcvQty - $row['issue_qty'];
				}
				$js_key=$row["id"]."_".$row["trans_type"];
                
				?>
					<tr bgcolor="<?php echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<?php echo $js_key; ?>');">
						<td width="40" align="center"><?php echo $i; ?></td>
						<td width="70" align="center"><?php echo $row['prefix_no_num']; ?></td>
                        <td width="70" align="center"><?php echo $row['year']; ?></td>
                        <td width="120" align="center"><?php echo $party_arr[$row['party_id']]; ?></td>		
						<td width="100" align="center"><?php echo $row['chalan_no']; ?></td>
						<td width="80"><?php echo change_date_format($row['subcon_date']); ?></td>
						<td width="80"><?php echo $rcvQty; ?></td>	
						<td><p><?php echo $balanceQty; ?></p></td>
					</tr>
				<?php 
				$i++;
            }
   		?>
			</table>
		</div>
     </div>
     <?	
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1) and ENTRY_FORM=121");
	oci_commit($con);
	disconnect($con);
	exit();
}

if ($action=='return_popup') {
	echo load_html_head_contents('Popup Info', '../../', 1, 1, $unicode, '', '');
	?>
	<script>
		var companyId = '<?php echo $data; ?>';
		function js_set_value(returnId,rcvId) {
			// $("#hidden_rcv_mst_id").val(id);
			
			 
			document.getElementById('hidden_return_mst_id').value = returnId;
			document.getElementById('hidden_recv_mst_id').value = rcvId;
			 
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="searchreceivefrm_1"  id="searchreceivefrm_1" autocomplete="off">
                <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="6"><?php echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                    	<tr>
                            <th width="150">Party</th>
                            <th width="80">Return No</th>
                            <th width="170">Date Range</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>         
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td id="buyer_td">
								<?php
									echo create_drop_down( 'cbo_party_name', 160, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name", 'id,buyer_name', 1, '-- Select Party --', $data, '' );
                                ?>
                            </td>
                            <td>
                                <input type="text" name="txt_return_no" id="txt_return_no" class="text_boxes" style="width: 160px" />
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:65px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_return_no').value+'_'+companyId, 'create_return_search_list_view', 'search_div', 'sub_contract_material_receive_return_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" />
                            </td>
                            <input type="hidden" name="hidden_return_mst_id" id="hidden_return_mst_id">
							<input type="hidden" name="hidden_recv_mst_id" id="hidden_recv_mst_id">
                        </tr>
                        <tr>
                            <td colspan="6" align="center" valign="middle">
								<?php echo load_month_buttons(1); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>  
                <div id="search_div"></div>
            </form>
        </div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=='load_mst_data_to_form') {
	$data = explode('***', $data);
	$searchId = $data[0];
	$type = $data[1];

	if($type == 1) {
		$nameArray=sql_select( "select id, sys_no, company_id, location_id, party_id, subcon_date, chalan_no, trans_type, entry_form from sub_material_mst where id=$searchId" );
	} else {
		$nameArray=sql_select( "select a.id, b.id as return_id, a.sys_no, b.sys_no as return_no, a.company_id, a.location_id, a.party_id, a.subcon_date, a.chalan_no, a.trans_type, a.entry_form, b.return_date, b.remarks from sub_material_mst a, sub_material_return_mst b where b.id=$searchId and a.id=b.sub_mat_recv_id and a.status_active=1 and b.status_active=1" );
	}
	
	foreach ($nameArray as $row)
	{
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_receive_no').value = '".$row[csf("sys_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		echo "$('#txt_receive_no').attr('disabled','true')".";\n";
		echo "$('#cbo_party_name').attr('disabled','true')".";\n";
		echo "$('#txt_receive_challan').attr('disabled','true')".";\n";
		echo "document.getElementById('cbo_party_name').value = '".$row[csf("party_id")]."';\n";
		echo "document.getElementById('txt_receive_challan').value = '".$row[csf("chalan_no")]."';\n";
	    echo "document.getElementById('hdn_mat_rcv_id').value = '".$row[csf("id")]."';\n";
	    echo "document.getElementById('hdn_location_id').value = '".$row[csf("location_id")]."';\n";
	    echo "document.getElementById('ref_trans_type').value = '".$row[csf("trans_type")]."';\n";
	    echo "document.getElementById('ref_entry_form').value = '".$row[csf("entry_form")]."';\n";

	    if($type == 2) {
	    	echo "document.getElementById('txt_return_no').value = '".$row[csf("return_no")]."';\n";
	    	echo "document.getElementById('txt_return_date').value = '".change_date_format($row[csf("return_date")])."';\n";
	    	echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
	    	echo "document.getElementById('hdn_update_id').value = '".$row[csf("return_id")]."';\n";
			echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_material_receive_return',1);\n";
	    }

	    // echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_material_receive_return',1);\n";
	}
	exit();
}

if($action=='subcontract_receive_stock_list_view') {
	//	echo load_html_head_contents('Popup Info', '../../../', 1, 1, $unicode, '', '');
	$data=explode('***',$data);
	$searchId = $data[0];
	$type = $data[1];
	if($type ==1) {
		$order_ids_arr = return_library_array("select id, order_id from sub_material_dtls where mst_id=$searchId and is_deleted=0", 'id', 'order_id');
	} else {
		$order_ids_arr = return_library_array("select id, order_id from sub_material_return_dtls where mst_id=$searchId and is_deleted=0", 'id', 'order_id');
	}
	
	$order_ids_arr = array_unique($order_ids_arr);
	$order_ids_str = implode(',', $order_ids_arr);
	
	  $sql_rcv_stock = "SELECT b.id, b.mst_id,b.lot_no, b.color_id,b.item_category_id, b.material_description, b.quantity,c.order_no,b.gsm,b.fin_dia,b.order_id, a.trans_type from sub_material_mst a, sub_material_dtls b left join subcon_ord_dtls c on b.order_id=c.id and c.is_deleted=0 where b.order_id in($order_ids_str) and b.mst_id in($searchId) and a.is_deleted=0 and a.trans_type in( 1,3) and a.entry_form in (288,344) and b.is_deleted=0 and a.id=b.mst_id order by b.order_id " ;
    
	$rcv_result = sql_select($sql_rcv_stock);
	$receive_arr = array();
	foreach ($rcv_result as $row) {
        $key = $row[csf('order_id')]."*".$row[csf('item_category_id')]."*".$row[csf('material_description')]."*".$row[csf('color_id')]."*".$row[csf('lot_no')];
        $receive_arr[$key]['id'] = $row[csf('id')].',';
        $receive_arr[$key]['item_category_id'] = $row[csf('item_category_id')];
		$receive_arr[$key]['order_no'] = $row[csf('order_no')];
		$receive_arr[$key]['gsm'] .= $row[csf('gsm')].',';;
		$receive_arr[$key]['fin_dia'] .= $row[csf('fin_dia')].',';;
		
        $receive_arr[$key]['material_description'] = $row[csf('material_description')];
        $receive_arr[$key]['trans_type'] = $row[csf('trans_type')];
        $receive_arr[$key]['receive_qty'] += $row[csf('quantity')];
		$receive_color_arr[$row[csf('id')]]['color_id'] = $row[csf('color_id')];
	}
	
     $sql_issue_stock = "select b.id, b.mst_id,b.lot_no, b.color_id,b.item_category_id, b.material_description, b.quantity,order_id from sub_material_mst a, sub_material_dtls b where order_id in($order_ids_str) and a.is_deleted=0 and a.trans_type = 2 and a.entry_form = 343 and b.is_deleted=0 and a.id=b.mst_id";
    $issue_result = sql_select($sql_issue_stock);
    $issue_arr = array();
    foreach ($issue_result as $row) {
		 $key =$row[csf('order_id')]."*".$row[csf('item_category_id')]."*".$row[csf('material_description')]."*".$row[csf('color_id')]."*".$row[csf('lot_no')];
        $issue_arr[$key]['issue_qty'] += $row[csf('quantity')];
    }

     $sql_issue_rtn_stock = "select b.id, b.mst_id,b.lot_no,b.color_id, b.item_category_id, b.material_description, b.quantity,order_id
	from sub_material_mst a, sub_material_dtls b
	where order_id in($order_ids_str) and a.is_deleted=0 and a.trans_type = 3 and a.entry_form = 344 and b.is_deleted=0 and a.id=b.mst_id";
    $issue_rtn_result = sql_select($sql_issue_rtn_stock);
    $issue_rtn_arr = array();

    foreach ($issue_rtn_result as $row) {
		//if($row[csf('color_id')]=="") $row[csf('color_id')]=0;
        $key = $row[csf('order_id')]."*".$row[csf('item_category_id')]."*".$row[csf('material_description')]."*".$row[csf('color_id')]."*".$row[csf('lot_no')];
        $issue_rtn_arr[$key]['issue_rtn_qty'] += $row[csf('quantity')];
    }

    $recv_returned_sql = "select a.item_category_id, a.receive_dtls_id,a.lot_no, a.material_description, a.quantity,a.order_id from sub_material_return_dtls a	where a.order_id in ($order_ids_str) and a.is_deleted = 0 and a.is_deleted = 0";

    $recv_returned_result = sql_select($recv_returned_sql); 
	$recv_return_arr = array();
	foreach ($recv_returned_result as $row) {
		$color_id=$receive_color_arr[$row[csf('receive_dtls_id')]]['color_id'];
        $key =  $row[csf('order_id')]."*".$row[csf('item_category_id')]."*".$row[csf('material_description')]."*".$color_id."*".$row[csf('lot_no')];
        $recv_return_arr[$key]['recv_return_quantity'] += $row[csf('quantity')];
	}
	$colorArr=return_library_array( "select id, color_name from lib_color",'id','color_name');
    ?>
	<table width="820" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="left">
		<thead>
			<tr>
				<th width="30">SL</th>
				<th width="110">Item Category</th>
				<th width="100">Order No</th>
				<th width="100">Color</th>
				<th width="250">Material Description</th>
				<th width="60">GSM</th>
				<th width="60">Lot No</th>
				<th width="60">Fin. Dia/ Width</th>          
				<th>Current Stock</th>
			</tr>
		</thead>
		<tbody>
		<?php
			$sl = 1;
			foreach ($receive_arr as $key => $row) {
				$keyArr=explode("*",$key);
				$keyArrRet=$keyArr[1].'*'.$keyArr[2];
				if($row['trans_type']==3){
					$stock =$issue_rtn_arr[$key]['issue_rtn_qty']-$recv_return_arr[$key]['recv_return_quantity'];
				}
				else{
					$stock =($row['receive_qty']+$issue_rtn_arr[$key]['issue_rtn_qty'])-($issue_arr[$key]['issue_qty']+$recv_return_arr[$key]['recv_return_quantity']);
				}
				
				if ($sl % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				?>
					<tr bgcolor="<?php echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" 	onClick="put_data_dtls_part('<?php echo trim($row['id'], ','); ?>', 1);">
						<td valign="middle" align="center"><?php echo $sl; ?></td>
						<td valign="middle"><?php echo $item_category[$row['item_category_id']]; ?></td>
						<td valign="middle"><?php echo rtrim($row['order_no'], ",");; ?></td>
						<td valign="middle"><?php echo $colorArr[$keyArr[3]]; ?></td>
						<td valign="middle"><?php echo $row['material_description']; ?></td>
						<td valign="middle"><?php echo rtrim($row['gsm'], ","); ?></td>
						<td valign="middle"><?php echo $keyArr[4]; ?></td>
						<td valign="middle"><?php echo rtrim($row['fin_dia'], ","); ?></td>
					
						<td valign="middle" align="right" title="Recv(<?=$row['receive_qty'];?>)+Issue Return(<?=$issue_rtn_arr[$key]['issue_rtn_qty'];?>)-Issue(<?=$issue_arr[$key]['issue_qty'];?>)-Rec Return(<?=$recv_return_arr[$key]['recv_return_quantity'];?>)"><?php echo $stock; ?>
							<input type="hidden" id="txt_balance_qnty" value="<?=$stock;?>">
						</td>
					</tr>
				<?php
				$sl++;
			}
		?>
		</tbody>
	</table>
	<?php
	exit();
}

if ($action == "show_rcv_listview") { 
	//	echo load_html_head_contents('Popup Info', '../../', 1, 1, $unicode, '', '');
	$data = explode('_',$data);
	$id = $data[0];
	$type = $data[1];
	$color_arrey=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$size_arrey=return_library_array( "select id,size_name from  lib_size where status_active=1 and is_deleted=0", 'id', 'size_name');

	if($type == 1) {
		$sql_rcv="SELECT a.trans_type, b.id,b.id as rec_dtls_id, b.mst_id, b.item_category_id, b.material_description, b.color_id, b.size_id, b.gsm, b.stitch_length, b.grey_dia, b.mc_dia, b.mc_gauge, b.fin_dia, b.dia_uom, b.rate, b.uom, b.subcon_roll, b.rec_cone,b.quantity, b.order_id, b.buyer_po_id, b.job_id, b.job_dtls_id, b.job_break_id, b.fabric_details_id, b.subcon_uom,b.lot_no,b.brand from sub_material_mst a, sub_material_dtls b where b.id in ($id) and a.is_deleted=0 and b.is_deleted=0 and a.id=b.mst_id";
	} else {
		$sql_rcv="SELECT a.id as return_dtls_id, b.id,b.id as rec_dtls_id, b.mst_id, b.item_category_id, b.material_description, b.color_id, b.size_id, b.gsm, b.stitch_length, b.grey_dia, b.mc_dia, b.mc_gauge, b.fin_dia, b.dia_uom, b.rate, b.uom, b.subcon_roll, b.rec_cone, b.order_id, b.buyer_po_id, b.job_id, b.job_dtls_id, b.job_break_id, b.fabric_details_id, a.quantity, b.subcon_uom,b.lot_no,b.brand, c.trans_type from sub_material_return_dtls a, sub_material_dtls b, sub_material_mst c where a.id=$id and c.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 and a.receive_dtls_id = b.id";
	}
	 //echo $sql_rcv;
	$rcv_result = sql_select($sql_rcv);
    $receive_data_arr = [];
	foreach($rcv_result as $data){
		$order_id_arr[$data[csf('order_id')]]=$data[csf('order_id')];
        $key = $data[csf('item_category_id')]."*".$data[csf('material_description')]."*".$data[csf('color_id')]."*".$data[csf('lot_no')];
        $receive_data_arr[$key]['order_id'] = $data[csf('order_id')];
        $receive_data_arr[$key]['buyer_po_id'] = $data[csf('buyer_po_id')];
        $receive_data_arr[$key]['job_id'] = $data[csf('job_id')];
        $receive_data_arr[$key]['job_dtls_id'] = $data[csf('job_dtls_id')];
        $receive_data_arr[$key]['job_break_id'] = $data[csf('job_break_id')];
        $receive_data_arr[$key]['id'] = $data[csf('id')];
        $receive_data_arr[$key]['mst_id'] = $data[csf('mst_id')];
        $receive_data_arr[$key]['fabric_details_id'] = $data[csf('fabric_details_id')];
        $receive_data_arr[$key]['return_dtls_id'] = $data[csf('return_dtls_id')];
        $receive_data_arr[$key]['item_category_id'] = $data[csf('item_category_id')];
        $receive_data_arr[$key]['material_description'] = $data[csf('material_description')];
        $receive_data_arr[$key]['color_id'] = $data[csf('color_id')];
        $receive_data_arr[$key]['size_id'] = $data[csf('size_id')];
        $receive_data_arr[$key]['gsm'] = $data[csf('gsm')];
        $receive_data_arr[$key]['stitch_length'] = $data[csf('stitch_length')];
        $receive_data_arr[$key]['grey_dia'] = $data[csf('grey_dia')];
        $receive_data_arr[$key]['mc_dia'] = $data[csf('mc_dia')];
        $receive_data_arr[$key]['mc_gauge'] = $data[csf('mc_gauge')];
        $receive_data_arr[$key]['fin_dia'] = $data[csf('fin_dia')];
        $receive_data_arr[$key]['dia_uom'] = $data[csf('dia_uom')];
        $receive_data_arr[$key]['subcon_roll'] = $data[csf('subcon_roll')];
        $receive_data_arr[$key]['fin_dia'] = $data[csf('fin_dia')];
        $receive_data_arr[$key]['rec_cone'] = $data[csf('rec_cone')];
        $receive_data_arr[$key]['rate'] = $data[csf('rate')];
        $receive_data_arr[$key]['subcon_uom'] = $data[csf('subcon_uom')];
		$receive_data_arr[$key]['lot_no'] = $data[csf('lot_no')];
		$receive_data_arr[$key]['brand'] = $data[csf('brand')];
		$receive_data_arr[$key]['trans_type'] = $data[csf('trans_type')];
		
		 $receive_color_arr[$data[csf('rec_dtls_id')]] = $data[csf('color_id')];
		  //  echo $type.'=D';
		  
        if($type = 2){
            $receive_data_arr[$key]['quantity'] += $data[csf('quantity')];
        }else{
            $receive_data_arr[$key]['quantity'] = 0;
        }
		$rcvIds .=$data[csf('rec_dtls_id')].",";
	}
	$order_ids_str= implode(", ",$order_id_arr);
	$rcvIds=rtrim($rcvIds,",");

    $sql_rcv_stock = "select a.entry_form, a.trans_type, b.id, b.mst_id, b.color_id,b.lot_no,b.item_category_id, b.material_description, b.quantity from sub_material_mst a, sub_material_dtls b where order_id in($order_ids_str) and b.id in ($rcvIds) and a.is_deleted=0 and a.trans_type in (1,3) and a.entry_form in (288,344) and b.is_deleted=0 and a.id=b.mst_id";
    $rcv_result = sql_select($sql_rcv_stock);
    $receive_arr = array();
    foreach ($rcv_result as $row) {
        $key = $row[csf('item_category_id')]."*".$row[csf('material_description')]."*".$row[csf('color_id')]."*".$row[csf('lot_no')];		
		if($row[csf('trans_type')]==1){
			$receive_arr[$key]['receive_qty'] += $row[csf('quantity')];
		}
		/* if($row[csf('trans_type')]==3){
			$receive_arr[$key]['issue_rtn_qty'] += $row[csf('quantity')];
		} */
        

    }

     $sql_issue_stock = "select b.id, b.mst_id,b.color_id,b.lot_no, b.item_category_id, b.material_description, b.quantity from sub_material_mst a, sub_material_dtls b where order_id in($order_ids_str) and a.is_deleted=0 and a.trans_type = 2 and a.entry_form = 343 and b.is_deleted=0 and a.id=b.mst_id";
    $issue_result = sql_select($sql_issue_stock);
    $issue_arr = array();
    foreach ($issue_result as $row) {
        $key = $row[csf('item_category_id')]."*".$row[csf('material_description')]."*".$row[csf('color_id')]."*".$row[csf('lot_no')];
        $issue_arr[$key]['issue_qty'] += $row[csf('quantity')];
    }

    $sql_issue_rtn_stock = "select b.id, b.mst_id, b.lot_no,b.color_id,b.item_category_id, b.material_description, b.quantity
	from sub_material_mst a, sub_material_dtls b
	where order_id in($order_ids_str) and a.is_deleted=0 and a.trans_type = 3 and a.entry_form = 344 and b.is_deleted=0 and a.id=b.mst_id";
    $issue_rtn_result = sql_select($sql_issue_rtn_stock);
    $issue_rtn_arr = array();

    foreach ($issue_rtn_result as $row) {
        $key = $row[csf('item_category_id')]."*".$row[csf('material_description')]."*".$row[csf('color_id')]."*".$row[csf('lot_no')];
        $issue_rtn_arr[$key]['issue_rtn_qty'] += $row[csf('quantity')];
    }


      $recv_returned_sql = "select a.item_category_id, a.receive_dtls_id,a.roll_bag,a.cone,a.lot_no, a.material_description, a.quantity, b.ref_trans_type, b.ref_entry_form from sub_material_return_dtls a join sub_material_return_mst b on b.id=a.mst_id	where a.order_id in ($order_ids_str) and a.is_deleted = 0 and a.is_deleted = 0";
	  //echo $recv_returned_sql; 

    $recv_returned_result = sql_select($recv_returned_sql);
    $recv_return_arr = array();
    foreach ($recv_returned_result as $row) {
		$color_id= $receive_color_arr[$row[csf('receive_dtls_id')]];
		if($color_id=="") $color_id=0;
        $key = $row[csf('item_category_id')]."*".$row[csf('material_description')]."*".$color_id."*".$row[csf('lot_no')];
		if($row[csf('ref_trans_type')]!=3){
			$recv_return_arr[$key]['recv_return_quantity'] += $row[csf('quantity')];
		}
		else{
			$recv_return_arr[$key]['issuerecv_return_quantity'] += $row[csf('quantity')];
		}
        
		$roll_bag_cone_arr[$key]['roll_bag'] = $row[csf('roll_bag')];
		$roll_bag_cone_arr[$key]['cone'] = $row[csf('cone')];
    }
	/* echo '<pre>';
	print_r($receive_data_arr); die; */
	foreach ($receive_data_arr as $key => $row) {
		$keyArr =explode("*",$key);
		if($row['trans_type']!=3){
			$stock =($receive_arr[$key]['receive_qty']+$issue_rtn_arr[$key]['issue_rtn_qty'])-($issue_arr[$key]['issue_qty']+$recv_return_arr[$key]['recv_return_quantity']);
		}
		else{
			$stock =$issue_rtn_arr[$key]['issue_rtn_qty']-$recv_return_arr[$key]['issuerecv_return_quantity'];
		}
		
		$roll_bag=$roll_bag_cone_arr[$key]['roll_bag'];
		$cone= $roll_bag_cone_arr[$key]['cone'];
		if($cone) $cone_cond=$cone;else  $cone_cond=$row['rec_cone'];
		if($roll_bag) $roll_bag_cond=$roll_bag;else  $roll_bag_cond=$row['subcon_roll'];
		
        ?>
	<tr>
        <td>
        	<input type="hidden" name="hdn_order_no_id" id="hdn_order_no_id" value="<?php echo $row['order_id']; ?>">
        	<input type="hidden" name="hdn_buyer_po_id" id="hdn_buyer_po_id" value="<?php echo $row['buyer_po_id']; ?>">
        	<input type="hidden" name="hdn_job_id" id="hdn_job_id" value="<?php echo $row['job_id']; ?>">
        	<input type="hidden" name="hdn_job_dtls_id" id="hdn_job_dtls_id" value="<?php echo $row['job_dtls_id']; ?>">
        	<input type="hidden" name="hdn_job_break_id" id="hdn_job_break_id" value="<?php echo $row['job_break_id']; ?>">
        	<input type="hidden" name="hdn_rcv_dtls_id" id="hdn_rcv_dtls_id" value="<?php echo $row['id']; ?>">
        	<input type="hidden" name="hdn_rcv_id" id="hdn_rcv_id" value="<?php echo $row['mst_id']; ?>">
        	<input type="hidden" name="hdn_fabric_dtls_id" id="hdn_fabric_dtls_id" value="<?php echo $row['fabric_details_id']; ?>">
        	<input type="hidden" name="hdn_return_dtls_id" id="hdn_return_dtls_id" value="<?php echo $row['return_dtls_id']; ?>">

            <?php
            	echo create_drop_down( 'cbo_item_category', 100, $item_category, '', 1, '--Select Item--', $row['item_category_id'], '', 1, '1,2,3,4,13,14,30' );
            ?>
        </td>
        <td>
            <input type="text" id="txt_material_description" name="txt_material_description" class="text_boxes" style="width:100px" value="<?php echo $row['material_description']; ?>" disabled="disabled" />
        </td>
        <td id="color_td">
            <?php
               echo create_drop_down( 'cbo_color', 100, $color_arrey, '', 1, "-Select-", $row['color_id'], '', 1, '' );
            ?>
        </td>
        <td>
        	<input type="text" id="txtsize" name="txtsize" class="text_boxes txt_size" style="width:60px" value="<?php echo $size_arrey[$row['size_id']]; ?>" disabled="disabled" />
        </td>
		<td>
            <input name="txt_lot_no" id="txt_lot_no" class="text_boxes" type="text" style="width:40px" value="<?php echo $row['lot_no']; ?>" disabled="disabled"/>
        </td>
		<td>
            <input name="txt_brand" id="txt_brand" class="text_boxes" type="text" style="width:40px" value="<?php echo $row['brand']; ?>"  disabled="disabled"/>
        </td>
        <td>
            <input name="txt_gsm" id="txt_gsm" class="text_boxes_numeric" type="text" style="width:40px" value="<?php echo $row['gsm']; ?>" disabled="disabled" />
        </td>
        <td>
            <input name="txt_stitch_length" id="txt_stitch_length" class="text_boxes" type="text" style="width:70px" value="<?php echo $row['stitch_length']; ?>" disabled="disabled" />
        </td>
        <td>
            <input name="txt_grey_dia" id="txt_grey_dia" class="text_boxes" type="text" style="width:40px" value="<?php echo $row['grey_dia']; ?>" disabled="disabled" />
        </td>
         <td>
            <input name="txt_mc_dia" id="txt_mc_dia" class="text_boxes" type="text" style="width:50px" value="<?php echo $row['mc_dia']; ?>" disabled="disabled" />
        </td>
         <td>
            <input name="txt_mc_gauge" id="txt_mc_gauge" class="text_boxes" type="text" style="width:50px" value="<?php echo $row['mc_gauge']; ?>" disabled="disabled" />
        </td>
        <td>
            <input name="txt_fin_dia" id="txt_fin_dia" class="text_boxes" type="text" style="width:40px" value="<?php echo $row['fin_dia']; ?>" disabled="disabled" />
        </td>
		
        <td>
        	<?php echo create_drop_down( 'cbo_dia_uom', 60, $unit_of_measurement, '','', '-Dia UOM-', $row['dia_uom'], '', 1, '25,29' ); ?>
        </td>
        <td>
            <input name="txt_roll" id="txt_roll" class="text_boxes_numeric" type="text" style="width:60px" value="<?php echo $roll_bag_cond; ?>"  />
        </td>
        <td>
            <input name="txt_return_quantity" previous_rec_qty="" id="txt_return_quantity" class="text_boxes_numeric" type="text" style="width:65px" value="<?php echo $row['quantity']; ?>" />
            <input type="hidden" name="previous_rec_qty" id="previous_rec_qty" value="<?=$row['quantity'];?>">
        </td>
        <td>
            <input name="txt_rec_rate" id="txt_rec_rate" class="text_boxes_numeric" type="text" style="width:55px" value="<?php echo $row['rate'];?>"  disabled="disabled" />
        </td>
		<td>
            <input name="txt_rec_balance" id="txt_rec_balance" class="text_boxes" type="text" style="width:40px" value="<?php echo $stock; ?>" disabled="disabled" />
        </td>
        <td>
            <?php echo create_drop_down( 'cbo_uom', 60, $unit_of_measurement, '', '', '-UOM-', $row['subcon_uom'], '', 1, '' ); ?>
        </td>
        <td>
            <input name="txt_cone" id="txt_cone" class="text_boxes_numeric" type="text" style="width:30px" value="<?php echo $cone_cond; ?>" />
        </td>
    </tr>
<?php
	}
	exit();
}

if($action=='get_balance_qnty')
{
	$dataArr=explode("**",$data);
	$sys_no=$dataArr[0];
	$recv_dtls_id=$dataArr[1];
	$sql_recv_id=sql_select("SELECT id from sub_material_mst where is_deleted=0 AND sys_no = '$sys_no' AND entry_form in( 288,343) order by id desc");
	//echo "SELECT id from sub_material_mst where is_deleted=0 AND sys_no = '$sys_no' AND entry_form in( 288,343) order by id desc";
	$recv_id='';
	if(count($sql_recv_id))
	{
		$recv_id=$sql_recv_id[0][csf('id')];
	}
	
	$sql_recv=sql_select("SELECT SUM (b.quantity) AS recv
			  FROM sub_material_mst a, sub_material_dtls b
			 WHERE     a.id = b.mst_id
			       AND a.is_deleted = 0
			       AND b.is_deleted = 0
			       AND a.id = $recv_id  AND b.id = $recv_dtls_id
			       ");
				
	$recv_qnty=0;
	if(count($sql_recv))
	{
		$recv_qnty=$sql_recv[0][csf('recv')];
	}

	$sql_recv_ret=sql_select("SELECT SUM (a.quantity) AS ret
		  FROM sub_material_return_dtls a, sub_material_dtls b
		 WHERE     a.is_deleted = 0
		       AND b.is_deleted = 0
		       AND a.receive_dtls_id = b.id
		      and b.mst_id in ($recv_id) and b.id in ($recv_dtls_id)");
	$recv_ret_qnty=0;
	if(count($sql_recv_ret))
	{
		$recv_ret_qnty=$sql_recv_ret[0][csf('ret')];
	}
	echo $recv_qnty-$recv_ret_qnty;

}



if($action == 'create_return_search_list_view') {
	$data=explode('_',$data);
	$search_type = $data[0];
	$party = $data[1];
	$dateFrom = $data[2];
	$dateTo = $data[3];
	$returnNo = $data[4];
	$company = $data[5];
	$search_cond = '';
	$company_cond = "and a.company_id=$company";

	// echo $search_type;
	if ($dateFrom=="" && $dateTo=="") {
		$dateFrom = '01-Jan-'.date('Y');
		$dateTo = '31-Dec-'.date('Y');
	}

	if ($party!=0) $party_cond = " and a.party_id=$party"; else $party_cond="";
	
	if($db_type==0)
	{ 
		if ($dateFrom!="" && $dateTo!="") $recieve_date = "and a.subcon_date between '".change_date_format($dateFrom,'yyyy-mm-dd')."' and '".change_date_format($dateTo,'yyyy-mm-dd')."'"; else $recieve_date ="";
	}
	else
	{
		if ($dateFrom!="" && $dateTo!="") $recieve_date = "and a.subcon_date between '".change_date_format($dateFrom, "", "",1)."' and '".change_date_format($dateTo, "", "",1)."'"; else $recieve_date ="";
	}

	if($search_type==1) {
		$search_cond =" and a.prefix_no_num='$returnNo'";
	}
	else if($search_type==4 || $search_type==0) {
		if ($returnNo != '') {
			$search_cond =" and a.prefix_no_num like '%$returnNo%'";
		}
	}
	else if($search_type==2) {
		$search_cond =" and a.prefix_no_num like '$returnNo%'";
	}
	else if($search_type==3) {
		$search_cond =" and a.prefix_no_num like '%$returnNo'";
	}
	
	/*$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
	$arr=array (2=>$party_arr,5=>$item_category);*/

	$sql = "select distinct a.id, a.prefix_no_num, a.sys_no, a.return_date, a.chalan_no, a.remarks, sum(b.quantity) as return_quantity,a.sub_mat_recv_id from sub_material_return_mst a, sub_material_return_dtls b where a.status_active=1 and a.is_deleted=0 and a.id=b.mst_id $company_cond $party_cond $search_cond group by a.id, a.prefix_no_num, a.sys_no, a.return_date, a.chalan_no, a.remarks,a.sub_mat_recv_id order by a.id desc";

	// echo $sql;
	$result = sql_select($sql);
	$rcv_array = array();

	foreach ($result as $row) {
		$rcv_array[$row[csf('id')]]['id'] = $row[csf('id')];
		$rcv_array[$row[csf('id')]]['sys_no'] = $row[csf('sys_no')];
		$rcv_array[$row[csf('id')]]['prefix_no_num'] = $row[csf('prefix_no_num')];
		$rcv_array[$row[csf('id')]]['return_date'] = $row[csf('return_date')];
		$rcv_array[$row[csf('id')]]['chalan_no'] = $row[csf('chalan_no')];
		$rcv_array[$row[csf('id')]]['remarks'] = $row[csf('remarks')];
		$rcv_array[$row[csf('id')]]['return_quantity'] = $row[csf('return_quantity')];
		$rcv_array[$row[csf('id')]]['sub_mat_recv_id'] = $row[csf('sub_mat_recv_id')];
	}

	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="717" class="rpt_table">
            <thead>
                <th width="40">SL</th>
                <th width="150">Return ID</th>
                <th width="70">Receive ID Number</th>
                <th width="100">Challan No</th>
                <th width="80">Return Date</th>
                <th>Return Qty</th>
            </thead>
     	</table>
     <div style="width:720px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table" id="tbl_po_list">
			<?php
			$i=1;
            foreach( $rcv_array as $row ) {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                $returnQty = $row['return_quantity'];
				/*$order_no='';
				$order_id=array_unique(explode(",",$row[csf("order_id")]));
				foreach($order_id as $val)
				{
					if($order_no=="") $order_no=$po_arr[$val]; else $order_no.=",".$po_arr[$val];
				}*/
				?>
					<tr bgcolor="<?php echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<?php echo $row["id"].",".$row['sub_mat_recv_id']; ?>);">
						<td width="40" align="center"><?php echo $i; ?></td>
						<td width="150" align="center"><?php echo $row['sys_no']; ?></td>
						<td width="70" align="center"><?php echo $row['prefix_no_num']; ?></td>
						<td width="100" align="center"><?php echo $row['chalan_no']; ?></td>
						<td width="80"><?php echo change_date_format($row['return_date']); ?></td>
						<td><p><?php echo $returnQty; ?></p></td>
					</tr>
				<?php 
				$i++;
            }
   		?>
			</table>
		</div>
     </div>
     <?	
	exit();
}

if($action == 'subcontract_return_stock_list_view') {
	$color_arrey=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$size_arrey=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", 'id', 'size_name');
	$size_arrey=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", 'id', 'size_name');
 

	 $sql = "select a.id,a.roll_bag as subcon_roll,a.cone as rec_cone, b.mst_id, b.item_category_id, b.material_description, b.color_id, b.size_id, b.gsm, b.stitch_length, b.grey_dia, b.mc_dia, b.mc_gauge, b.fin_dia, b.dia_uom, b.rate, b.subcon_uom, b.order_id, b.buyer_po_id, b.job_id, b.job_dtls_id, b.job_break_id, b.fabric_details_id,b.lot_no,b.brand, a.quantity,c.order_no
	from sub_material_return_dtls a, sub_material_dtls b left join subcon_ord_dtls c on b.order_id=c.id and c.is_deleted=0
	where a.mst_id=$data and a.is_deleted=0 and b.is_deleted=0 and a.receive_dtls_id = b.id";

	$arr=array(0=>$item_category,5=>$color_arrey,6=>$size_arrey,13=>$unit_of_measurement,17=>$unit_of_measurement);
	echo create_list_view('return_list_view', 'Item Category,Order No,Lot,Brand,Material Description,Color,GMTS Size,GSM,Stitch Length,Grey Dia/Width,M/C Dia,M/C Gauge,Fin. Dia/Width,Dia UOM,Roll/Bag,Return Qty,Rate,UOM,Cone', '80,100,80,80,60,60,80,130,70,60,60,60,60,60,60,70,60,60,60', '1540', '250', 0, $sql, 'put_data_dtls_part', 'id', '2', 1, 'item_category_id,0,0,0,0,color_id,size_id,0,0,0,0,0,0,dia_uom,0,0,0,subcon_uom,0', $arr, 'item_category_id,order_no,lot_no,brand,material_description,color_id,size_id,gsm,stitch_length,grey_dia,mc_dia,mc_gauge,fin_dia,dia_uom,subcon_roll,quantity,rate,subcon_uom,rec_cone', 'requires/sub_contract_material_receive_return_controller', '', '0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0','', '');
	// create_list_view($table_id, $tbl_header_arr, $td_width_arr, $tbl_width, $tbl_height, $tbl_border, $query, $onclick_fnc_name, $onclick_fnc_param_db_arr, $onclick_fnc_param_sttc_arr, $show_sl, $field_printed_from_array_arr, $data_array_name_arr, $qry_field_list_array, $controller_file_path, $filter_grid_fnc, $fld_type_arr, $summary_flds, $check_box_all, $new_conn)
	exit();
}

if($action=='save_update_delete') {
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	//=======******Validation*********=======================
	$rec_balance = str_replace("'", '', $txt_rec_balance);
	$order_no_id = str_replace("'", '', $hdn_order_no_id);
	$item_category_id = str_replace("'", '', $cbo_item_category);
	$material_desc = str_replace("'", '', $txt_material_description);
	$item_category_id = str_replace("'", '', $cbo_item_category);
	$hdnrcv_id = str_replace("'", '', $hdn_rcv_id);
	$lot_no = str_replace("'", '', $txt_lot_no);
	$cbo_color = str_replace("'", '', $cbo_color);
	$rcvdtls_id = str_replace("'", '', $hdn_rcv_dtls_id);
	$return_dtls_id = str_replace("'", '', $hdn_return_dtls_id);
	$return_quantity = str_replace("'", '', $txt_return_quantity);
	$ref_trans_type = str_replace("'", '', $ref_trans_type);
	$ref_entry_form = str_replace("'", '', $ref_entry_form);
//	if($color_id==0) $color_cond="";else $color_cond=" and ";
	if ($operation==0 || $operation==1) 
	{
		 $sql_rcv_stock = "SELECT b.id, b.mst_id,b.lot_no, b.color_id,b.item_category_id, b.material_description, b.quantity,c.order_no,b.gsm,b.fin_dia,b.order_id from sub_material_mst a, sub_material_dtls b left join subcon_ord_dtls c on b.order_id=c.id and c.is_deleted=0 where b.order_id in($order_no_id) and b.mst_id in($hdnrcv_id) and b.id=$rcvdtls_id and a.is_deleted=0 and a.trans_type in( 1,3) and a.entry_form in(288,344) and b.is_deleted=0 and a.id=b.mst_id order by b.order_id " ;
    
	$rcv_result = sql_select($sql_rcv_stock);
	$receive_arr = array();
	$tot_recv_qty=0;
	foreach ($rcv_result as $row) {
        
		$tot_recv_qty+=$row[csf('quantity')];
	}
	$update_cond="";
	 if($operation==1) 
	 {
		 $update_cond=" and a.id!=$return_dtls_id";
		$tot_rec_balance=$tot_recv_qty+$rec_balance;
	 }
	 else  if($operation==0) 
	 {
		  $tot_rec_balance=$rec_balance;
	 }
	
	$recv_returned_sql = "select a.item_category_id, a.receive_dtls_id,a.roll_bag,a.cone,a.lot_no, a.material_description, a.quantity from sub_material_return_dtls a	where a.order_id in ($order_no_id) and a.is_deleted = 0 and a.is_deleted = 0 and a.receive_id=$hdnrcv_id  and a.receive_dtls_id=$rcvdtls_id  and a.lot_no='$lot_no' and a.item_category_id=$item_category_id and a.material_description='$material_desc' $update_cond "; 
	//echo "10**".$recv_returned_sql;

    $recv_returned_result = sql_select($recv_returned_sql);
    $recv_return_arr = array();$recv_return_quantity=0;
    foreach ($recv_returned_result as $row) {
        $recv_return_quantity += $row[csf('quantity')];
       }
	   $tot_return_qty=$return_quantity+$recv_return_quantity;
			
	//    if($tot_return_qty>$tot_rec_balance)
	//    {
	// 	   echo "14**Recv Return Qty not allowed over than Balace/Return Qty,RecvBal=".$tot_rec_balance.',Ret='.$tot_return_qty;
	// 		disconnect($con);
	// 		die;
	//    }
	   
	}
	
	// Insert Start Here ----------------------------------------------------------
	if ($operation==0) {
		$con = connect();
		$id = str_replace("'", '', $hdn_update_id);	// update id is the mst id because master part data is already saved
		$returnNo = str_replace("'", '', $txt_return_no);
		$rID = 1;
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		// if no update id found. meaning: we need to save full master part data
		if($id == '') {
			if($db_type==0)
			{
				$new_return_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name),'', 'RTRN' , date("Y",time()), 5, "select id,prefix_no,prefix_no_num from sub_material_return_mst where company_id=$cbo_company_name and YEAR(insert_date)=".date('Y',time())." order by id desc", "prefix_no", "prefix_no_num" ));
			}
			else if($db_type==2)
			{
				$new_return_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name),'', 'RTRN' , date("Y",time()), 5, "select id,prefix_no,prefix_no_num from sub_material_return_mst where company_id=$cbo_company_name and TO_CHAR(insert_date,'YYYY')=".date('Y',time())." order by id desc", "prefix_no", "prefix_no_num" ));
			}
			$id=return_next_id('id', 'sub_material_return_mst', 1);
			$returnNo = $new_return_no[0];

			$field_array="id,prefix_no,prefix_no_num,sys_no,sub_mat_recv_id,company_id,location_id,party_id,chalan_no,return_date,remarks,trans_type,entry_form,ref_trans_type,ref_entry_form,inserted_by,insert_date";
			$data_array="(".$id.",'".$new_return_no[1]."','".$new_return_no[2]."','".$returnNo."',".$hdn_mat_rcv_id.",".$cbo_company_name.",".$hdn_location_id.",".$cbo_party_name.",".$txt_receive_challan.",".$txt_return_date.",".$txt_remarks.",4,646,".$ref_trans_type.",".$ref_entry_form.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			$rID=sql_insert('sub_material_return_mst', $field_array, $data_array, 0);
		}

		$dtls_id=return_next_id('id', 'sub_material_return_dtls', 1);
		$field_array_dtls="id,mst_id,order_id,item_category_id,material_description,quantity,job_dtls_id,job_id,job_break_id,buyer_po_id,receive_dtls_id,receive_id,fabric_details_id,lot_no,brand,roll_bag,cone,inserted_by,insert_date";
		$data_array_dtls="(".$dtls_id.",".$id.",".$hdn_order_no_id.",".$cbo_item_category.",".$txt_material_description.",".$txt_return_quantity.",".$hdn_job_dtls_id.",".$hdn_job_id.",".$hdn_job_break_id.",".$hdn_buyer_po_id.",".$hdn_rcv_dtls_id.",".$hdn_rcv_id.",".$hdn_fabric_dtls_id.",".$txt_lot_no.",".$txt_brand.",".$txt_roll.",".$txt_cone.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		//echo "10**insert into sub_material_return_dtls(".$field_array_dtls.") values ".$data_array_dtls; die;

		
		$rID2=sql_insert('sub_material_return_dtls', $field_array_dtls, $data_array_dtls, 0);
		
		$flag = ($rID && $rID2);
		
		// echo "10**".$rID."**".$rID2."**".$flag;die;		

		if($db_type==0)
		{
			if($flag)
			{
				mysql_query("COMMIT");  
				echo "0**".$returnNo.'**'.$id.'**'.$hdn_mat_rcv_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$returnNo;
			}
		}
		if($db_type==2)
		{
			if($flag==1) 
			{
				oci_commit($con);
				echo "0**".$returnNo.'**'.$id.'**'.str_replace("'", '',$hdn_mat_rcv_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".$returnNo;
			}
		}
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();

		$update_id = str_replace("'", '', $hdn_update_id);
		$dtls_update_id = str_replace("'", '', $hdn_return_dtls_id);
		$txt_return_no = str_replace("'",'',$txt_return_no);

		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
					
		$field_array="return_date*remarks*updated_by*update_date";
		$data_array="".$txt_return_date."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		
		//$color_id=return_id( $txt_color, $color_arr, "lib_color", "id,color_name");
		
		$field_array2="quantity*lot_no*brand*roll_bag*cone*updated_by*update_date";
		$data_array2="".$txt_return_quantity."*".$txt_lot_no."*".$txt_brand."*".$txt_roll."*".$txt_cone."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";  
		$flag=1;
		$rID=sql_update("sub_material_return_mst",$field_array,$data_array,"id",$update_id,0);

		$rID2=sql_update("sub_material_return_dtls",$field_array2,$data_array2,"id",$dtls_update_id,0);
		// echo "10**$rID**$rID2";die;
		// if($flag==1 && $rID2==1) $flag=1; else $flag=0;
		 
		if($db_type==0)
		{
			if( $rID && $rID2 )
			{
				mysql_query("COMMIT");  
				echo "1**".$txt_return_no."**".$update_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$txt_return_no."**".$update_id;
			}
		}
		if($db_type==2)
		{
			if( $rID && $rID2 )
			{
				oci_commit($con);
				echo "1**".$txt_return_no."**".str_replace("'", '',$update_id)."**".str_replace("'", '',$hdn_mat_rcv_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".$txt_return_no."**".$update_id;
			}
		}
	}
	else if ($operation==2)   // delete
	{
		$con = connect();
		
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$sys_no = return_field_value("sys_no as sys_no", "sub_material_mst a,sub_material_dtls b", "a.id=b.mst_id and b.rec_challan=$txt_receive_challan  and b.order_id=$order_no_id  and a.trans_type=2  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "sys_no");
		
		if($sys_no)
		{
			echo "13**Issue Found,Delete not allowed(Issue ID=$sys_no)";
			disconnect($con);
			die;
		}
		 //echo $zero_val;
		if ( $zero_val==1 )
		{
			$field_array="status_active*is_deleted*updated_by*update_date";
			$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			$data_array_dtls="1*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			
			if (str_replace("'",'',$cbo_status)==1)
			{
				$rID=sql_update("sub_material_dtls",$field_array,$data_array_dtls,"id",$update_id2,1); //die;
			}
			else
			{
				$rID=sql_update("sub_material_mst",$field_array,$data_array,"id",$update_id,0);  
				//echo "INSERT INTO sub_material_dtls (".$field_array.") VALUES ".$data_array_dtls; die;

				$rID=sql_update("sub_material_dtls",$field_array,$data_array_dtls,"mst_id",$update_id,1);  
			}
		}
		else
		{
			$rID=0;
		}
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id2);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id2);
			}
		}
		else if($db_type==2)
		{
			if($rID)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id2);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id2);
			}
		}
		disconnect($con); 
	}

	disconnect($con);
	die;
}

if($action=="material_receive_return_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$location=$data[4];
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	
	$party_library=return_library_array( "select id, supplier_name from lib_supplier", "id","supplier_name"  );
	$location_library=return_library_array( "select id, location_name from lib_location", "id","location_name"  );
	$party_library_arr=return_library_array("select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]'  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id","buyer_name");
	//$order_array=return_library_array( "select id, order_no from subcon_ord_dtls", "id","order_no"  );
	$sql_po=sql_select("select id, order_no,job_no_mst from subcon_ord_dtls");
	foreach($sql_po as $row)
	{
		$order_array[$row[csf('id')]]=$row[csf('order_no')];
		$job_array[$row[csf('id')]]=$row[csf('job_no_mst')];
	}
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	$sql_mst="SELECT sys_no, party_id, location_id, remarks, chalan_no, return_date from sub_material_return_mst where company_id=$data[0] and id='$data[1]' and status_active=1 and is_deleted=0";
	//echo $sql_mst;
	$dataArray=sql_select($sql_mst);
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
    <div style="width:930px;">
         <table width="930" cellspacing="0" align="right" border="0">
		    <tr>
				<td rowspan="3">
					<img src="../<? echo $com_dtls[2]; ?>" width="80" style="float:left;">
				</td>
				<td colspan="11" align="center" style="font-size:x-large">
					<strong><? echo $com_dtls[0]; ?></strong>
				</td>
			</tr>
			<tr>
	            <td colspan="11" align="center">
	                <?
	                	echo $com_dtls[1];
	                ?> 
	            </td>
	        </tr>
        	<tr>
                <td colspan="11" align="center" style="font-size:20px"><u>
                	<strong><? echo $data[3]; ?></strong></u>
                </td>
            </tr>
             <tr>
                <td><strong>Return To: </strong></td>
                <td> <? echo $party_library_arr[$dataArray[0][csf('party_id')]]; ?></td>
                <td><strong>Challan No: </strong></td>
                <td> <? echo $dataArray[0][csf('chalan_no')]; ?></td>
                <td width="130"><strong>Delivery Date:</strong></td>
                <td width="175"><? echo change_date_format($dataArray[0][csf('return_date')]); ?></td>
            </tr>
            <tr>
                <td width="130"><strong>Address :</strong></td>
                 <td width="175"><? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
                <td width="130">Transport Com.</td> <td width="175"></td>
                <td width="130">Forwarder</td> <td width="175"></td>
            </tr>
           
            <tr>
            	<td>Return No:</td><td><? echo $dataArray[0][csf('sys_no')]; ?></td>
                <td>Remarks:</td><td colspan="3"><? echo $dataArray[0][csf('remarks')]; ?></td>
            </tr>
        </table>
         <br>
        <div style="width:100%; margin-top: 30px">
		<table align="left" cellspacing="0" width="1120"  border="1" rules="all" class="rpt_table"  >
            <thead bgcolor="#dddddd">
                <th width="40">SL No</th>
                <th width="80">Order No</th>
                <th width="100">Item Category</th>
				<th width="75">Lot No</th>
				<th width="75">Brand</th>
                <th width="130">Material Description</th> 
                <th width="130">Color</th> 
                <th width="80">Return Qty</th>
                <th width="30">UOM</th>
                <th width="30">Roll/Bag</th>
                <th width="30">Cone</th>
            </thead>         
         <?	
			$sql_recv_arr=sql_select(" select a.chalan_no, b.order_id, b.material_description, b.stitch_length, b.color_id from sub_material_dtls b,sub_material_mst a where a.id=b.mst_id and b.status_active=2 and a.entry_form=288 and  a.is_deleted=0 and a.trans_type=1 and entry_form=288 group by a.chalan_no, b.order_id,b.material_description,b.stitch_length, b.color_id");
			foreach($sql_recv_arr as $row)
			{
			$recv_data_arr[$row[csf('order_id')]][$row[csf('chalan_no')]][$row[csf('material_description')]][$row[csf('color_id')]]['stitch_length'].=$row[csf('stitch_length')].',';
			//$recv_data_arr[$row[csf('order_id')]]['used_yarn_details']=$row[csf('used_yarn_details')];
			}
		 
			$i=1;
			$mst_id=$data[1];
			$dataArray=sql_select($sql_mst);
//roll_bag as subcon_roll,a.cone
			$sql_result = sql_select("select a.id,a.roll_bag,a.cone, b.mst_id, b.item_category_id, b.material_description, b.color_id, b.size_id, b.gsm, b.stitch_length, b.grey_dia, b.mc_dia, b.mc_gauge,a.lot_no,a.brand, b.fin_dia, b.dia_uom, b.rate, b.uom, b.subcon_roll, b.rec_cone, b.order_id, b.buyer_po_id, b.job_id, b.job_dtls_id, b.job_break_id, b.fabric_details_id, a.quantity from sub_material_return_dtls a, sub_material_dtls b where a.mst_id=$mst_id and a.is_deleted=0 and b.is_deleted=0 and a.receive_dtls_id = b.id");
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$ex_recchallan=array_unique(explode(",",$row[csf('rec_challan')]));
				
				$stitch_length='';
				foreach($ex_recchallan as $challan)
				{
					$rec_stitch_length="";
					$rec_stitch_length=implode(",",array_filter(array_unique(explode(",",$recv_data_arr[$row[csf('order_id')]][$challan][$row[csf('material_description')]][$row[csf('color_id')]]['stitch_length']))));
					if($stitch_length=="") $stitch_length=$rec_stitch_length; else $stitch_length.=', '.$rec_stitch_length;
				}
			?>
				<tr bgcolor="<? echo $bgcolor; ?>"> 
                    <td><? echo $i; ?></td>
                     <td><p><? echo $order_array[$row[csf('order_id')]]; ?></p></td>
                    <td><p><? echo $item_category[$row[csf('item_category_id')]]; ?></p></td>
					<td align="center"><p><? echo $row[csf('lot_no')]; ?></p></td>
					<td align="center"><p><? echo $row[csf('brand')]; ?></p></td>
                    <td><p><? echo $row[csf('material_description')]; if($row[csf('gsm')]!=""){ echo ", ".$row[csf('gsm')];} ?></p></td>
                    <td><p><? echo $color_arr[$row[csf('color_id')]];?></p></td>
                    <td align="right"><p><? echo $row[csf('quantity')]; ?></p></td>
                    <td><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
                    <td><p><? echo $row[csf('roll_bag')]; ?></p></td>
                    <td><p><? echo $row[csf('cone')]; ?></p></td>
                </tr>
                <?
                $i++;
                $tot_rcv_return_qty +=$row[csf('quantity')];
				$tot_rcv_return_roll +=$row[csf('roll_bag')];
				$tot_rcv_return_cone +=$row[csf('cone')];
			}
			?>
        	<tr> 
                <td align="right" colspan="7"><strong>Total</strong></td>
                <td align="right"><? echo number_format($tot_rcv_return_qty,2,'.',''); ?>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="center"><strong><? echo number_format($tot_rcv_return_roll,0,'.',''); ?></strong></td>
                <td align="center"><strong><? echo number_format($tot_rcv_return_cone,0,'.',''); ?></strong></td>
			</tr>
        </table>
        <br>
		 <?
            echo signature_table(296, $data[0], "1120px",'',"10px");
         ?>
   </div>
   </div>
	<?
}

if($action=="return_multy_number_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>

	<script>

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var attrData=$('#tr_' +i).attr('onclick');
				var splitArr = attrData.split("'");
				js_set_value( splitArr[1] );
			}
		}


		var selected_id=Array();
		var selected_name=Array();

		function js_set_value(mrr)
		{
			var splitArr = mrr.split("_");
			$("#hidden_return_number").val(splitArr[0]); // mrr number
			$("#hidden_return_id").val(splitArr[1]);

			toggle( document.getElementById( 'tr_' + splitArr[0] ), '#FFFFCC' );

			if( jQuery.inArray(splitArr[2], selected_id ) == -1 ) {
				selected_id.push( splitArr[2]);
				selected_name.push(splitArr[1]);

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == splitArr[2]) break;
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

			$('#hidden_return_id').val(id);
			$('#hidden_return_number').val(name);
		}
	</script>

	</head>

	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="850" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<tr>
							<th width="120" class="must_entry_caption">Supplier</th>
							<th width="180">Search By</th>
							<th width="250" align="center" id="search_by_td_up">Enter Return Number</th>
							<th width="220">Date Range</th>
							<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								echo create_drop_down( "cbo_return_to", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) group by buy.id,buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select --", $cbo_return_to, "",0 );
								?>
							</td>
							<td align="center">
								<?
								$search_by = array(1=>'Return Number');
								echo create_drop_down( "cbo_search_by", 140, $search_by,"",0, "--Select--", "",1,0 );
								?>
							</td>
							<td width="" align="center" id="search_by_td">
								<input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" />&nbsp;&nbsp;&nbsp;
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" />
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_return_to').value+'_'+document.getElementById('cbo_year_selection').value, 'create_multy_return_search_list_view', 'search_div', 'sub_contract_material_receive_return_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td align="center" height="40" valign="middle" colspan="5">
								<? echo load_month_buttons(1);  ?>
								<!-- Hidden field here-->
								<!--END -->
							</td>
						</tr>
					</tbody>
				</tr>
			</table>
			<div align="center" style="margin-top:10px" valign="top" id="search_div"> </div>

		</form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_multy_return_search_list_view")
{
	echo '<input type="hidden" id="hidden_return_number" value="" /><input type="hidden" id="hidden_return_id" value="" />';


	$ex_data = explode("_",$data);
	$search_by = $ex_data[0];
	$search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
	$return_to = $ex_data[5];
	$year_selection = $ex_data[6];


	$sql_cond="";
	if($search_by==1)
	{
		if($search_common!="") $sql_cond .= " and a.sys_no like '%$search_common'";
	}

	if( $txt_date_from!="" && $txt_date_to!="" )
	{
		if($db_type==0)
		{
			$sql_cond .= " and a.return_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else
		{
			$sql_cond .= " and a.return_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
		}
	}

	if(trim($company)!="") $sql_cond .= " and a.company_id='$company'";

	if($db_type==0)
	{
		$year_cond=" and YEAR(a.return_date)=$year_selection";
	}
	else if($db_type==2)
	{
		$year_cond=" and to_char(a.return_date,'YYYY')=$year_selection";
	}
	else
	{
		$year_cond=""; $year_field="";
	}

	if($db_type==0) $year_field="YEAR(a.insert_date) as year,";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	else $year_field="";//defined Later

	if(str_replace("'","",$return_to==0)){echo "<p style='font-size:25px; color:#F00'>Please Select Supplier.</p>";die;}
	else{$supplier_con=" and a.party_id=$return_to";}

	$sql = "SELECT distinct a.id, $year_field a.prefix_no_num, a.sys_no, a.company_id, a.party_id, a.return_date, a.sub_mat_recv_id,  sum(b.quantity) as return_quantity from sub_material_return_mst a, sub_material_return_dtls b where a.status_active=1 and a.is_deleted=0 and a.id=b.mst_id $supplier_con $year_cond $sql_cond group by a.id, a.prefix_no_num, a.sys_no, a.company_id, a.party_id, a.return_date, a.sub_mat_recv_id, a.insert_date order by a.id desc";
	//echo $sql;
	$result = sql_select($sql);

	if(count($result)==0)
	{
		echo "<p style='font-size:25px; color:#F00'>No Data Found.</p>";die;
	}

	$allRcvIdArr = [];
	foreach($result as $row) 
	{
		array_push($allRcvIdArr,$row[csf('sub_mat_recv_id')]);
	}
	
	if(!empty($allRcvIdArr))
	{
		$mst_rcv_arr = return_library_array("SELECT a.id, a.sys_no from sub_material_mst a where a.status_active=1 and a.is_deleted=0 and a.trans_type in(1,3) and a.entry_form in(288,344) ".where_con_using_array($allRcvIdArr,0,'a.id')."","id","sys_no");
	}

	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$supplier_arr=return_library_array( "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) group by buy.id,buy.buyer_name order by buy.buyer_name",'id','buyer_name');

	$arr=array(2=>$company_arr,3=>$supplier_arr,6=>$mst_rcv_arr);

	echo create_list_view("list_view", "Return No, Year, Company Name, Returned To, Return Date,Return Qty,Receive MRR","70,60,150,170,80,100,150","850","230",0, $sql , "js_set_value", "sys_no,id", "1", 1, "0,0,company_id,party_id,0,0,sub_mat_recv_id", $arr, "prefix_no_num,year,company_id,party_id,return_date,return_quantity,sub_mat_recv_id","","","0,0,0,0,3,1,0","",1) ;
	exit();
	
}

if($action=="material_multi_receive_return_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$location=$data[4];

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$party_library=return_library_array( "select id, supplier_name from lib_supplier", "id","supplier_name"  );
	$location_library=return_library_array( "select id, location_name from lib_location", "id","location_name"  );
	$party_library_arr=return_library_array("select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data[0]) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id","buyer_name");
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");

	$sql_mst="SELECT sys_no, party_id, location_id, remarks, chalan_no, return_date from sub_material_return_mst where company_id in($data[0]) and id in($data[1]) and status_active=1 and is_deleted=0";
	//echo $sql_mst;

	$dataArray=sql_select($sql_mst);
	$com_dtls = fnc_company_location_address($company, $location, 2);

	?>
    <div style="width:930px;">
         <table width="930" cellspacing="0" align="right" border="0">
		    <tr>
				<td rowspan="3">
					<img src="../<? echo $com_dtls[2]; ?>" width="80" style="float:left;">
				</td>
				<td colspan="11" align="center" style="font-size:x-large">
					<strong><? echo $com_dtls[0]; ?></strong>
				</td>
			</tr>
			<tr>
	            <td colspan="11" align="center">
	                <?
	                	echo $com_dtls[1];
	                ?> 
	            </td>
	        </tr>
        	<tr>
                <td colspan="11" align="center" style="font-size:20px"><u>
                	<strong><? echo $data[3]; ?></strong></u>
                </td>
            </tr>
             <tr>
                <td><strong>Return To: </strong></td>
                <td> <? echo $party_library_arr[$dataArray[0][csf('party_id')]]; ?></td>
                <td><strong>Challan No: </strong></td>
                <td> <? echo $dataArray[0][csf('chalan_no')]; ?></td>
                <td width="130"><strong>Delivery Date:</strong></td>
                <td width="175"><? echo change_date_format($dataArray[0][csf('return_date')]); ?></td>
            </tr>
            <tr>
                <td width="130"><strong>Location :</strong></td>
                 <td width="175"><? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
                <td width="130">Transport Com.</td> <td width="175"></td>
                <td width="130">Forwarder</td> <td width="175"></td>
            </tr>
           
            <tr>
                <td>Remarks:</td><td colspan="3"><? echo $dataArray[0][csf('remarks')]; ?></td>
            </tr>
        </table>
         <br>
        <div style="width:100%; margin-top: 30px">
		<table align="left" cellspacing="0" width="1240"  border="1" rules="all" class="rpt_table"  >
            <thead bgcolor="#dddddd">
                <th width="40">SL No</th>
                <th width="120">Return No</th>
                <th width="80">Order No</th>
                <th width="100">Item Category</th>
				<th width="75">Lot No</th>
				<th width="75">Brand</th>
                <th width="180">Material Description</th> 
                <th width="80">Color</th> 
                <th width="80">Return Qty</th>
                <th width="30">UOM</th>
                <th width="30">Roll/Bag</th>
                <th width="30">Cone</th>
            </thead>         
         <?	
		 
			$i=1;
			$mst_id=$data[1];
			$dataArray=sql_select($sql_mst);
			
			$sql_result = sql_select("SELECT a.sys_no, b.id,b.roll_bag,b.cone, c.mst_id, c.item_category_id, c.material_description, c.color_id, c.size_id, c.gsm, c.stitch_length, c.grey_dia, c.mc_dia, c.mc_gauge,b.lot_no, b.brand, c.fin_dia, c.dia_uom, c.rate, c.uom, c.subcon_roll, c.rec_cone, c.order_id, c.buyer_po_id, c.job_id, c.job_dtls_id, c.job_break_id, c.fabric_details_id, b.quantity from sub_material_return_mst a, sub_material_return_dtls b, sub_material_dtls c where a.id=b.mst_id and b.receive_dtls_id = c.id and b.mst_id in($mst_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2) and c.is_deleted=0 ");

			$allOrderIdArr=[];
			foreach($sql_result as $row)
			{
				array_push($allOrderIdArr,$row[csf('order_id')]);
			}

			if(!empty($allOrderIdArr))
			{
				$sql_po=sql_select("SELECT id, order_no,job_no_mst from subcon_ord_dtls where status_active=1 and status_active=1 and is_deleted=0 ".where_con_using_array($allOrderIdArr,0,'id')."");

				$job_info_array=[];
				foreach($sql_po as $row)
				{
					$job_info_array[$row[csf('id')]]['order_no']=$row[csf('order_no')];
				}
			}
			
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>"> 
                    <td align="center"><? echo $i; ?></td>
                     <td align="center"><p><? echo $row[csf('sys_no')]; ?></p></td>
                     <td align="center"><p><? echo $job_info_array[$row[csf('order_id')]]['order_no']; ?></p></td>
                    <td align="center"><p><? echo $item_category[$row[csf('item_category_id')]]; ?></p></td>
					<td align="center"><p><? echo $row[csf('lot_no')]; ?></p></td>
					<td align="center"><p><? echo $row[csf('brand')]; ?></p></td>
                    <td align="center"><p><? echo $row[csf('material_description')]; if($row[csf('gsm')]!=""){ echo ", ".$row[csf('gsm')];} ?></p></td>
                    <td align="center"><p><? echo $color_arr[$row[csf('color_id')]];?></p></td>
                    <td align="right"><p><? echo number_format($row[csf('quantity')],2,'.',''); ?></p></td>
                    <td align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
                    <td align="center"><p><? echo $row[csf('roll_bag')]; ?></p></td>
                    <td align="center"><p><? echo $row[csf('cone')]; ?></p></td>
                </tr>
                <?
                $i++;
                $tot_rcv_return_qty +=$row[csf('quantity')];
				$tot_rcv_return_roll +=$row[csf('roll_bag')];
				$tot_rcv_return_cone +=$row[csf('cone')];
			}
			?>
        	<tr> 
                <td align="right" colspan="8"><strong>Total</strong></td>
                <td align="right"><? echo number_format($tot_rcv_return_qty,2,'.',''); ?>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="center"><strong><? echo number_format($tot_rcv_return_roll,0,'.',''); ?></strong></td>
                <td align="center"><strong><? echo number_format($tot_rcv_return_cone,0,'.',''); ?></strong></td>
			</tr>
        </table>
        <br>
		 <?
            echo signature_table(296, $data[0], "1120px",'',"10px");
         ?>
   </div>
   </div>
	<?
}
?>