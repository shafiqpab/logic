<?
	header('Content-type:text/html; charset=utf-8');
	session_start();
	include('../../../../includes/common.php');

	$user_id = $_SESSION['logic_erp']["user_id"];
	if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
	$permission=$_SESSION['page_permission'];
	$data=$_REQUEST['data'];
	$action=$_REQUEST['action'];


	if ($action=="load_multiselect_item_group")
	{
		$data=explode('_',$data);
		$com_id=$data[0];
		$cat_id=$data[1];
		$sql_cond="";
		if($cat_id!="") $sql_cond.=" and item_category in($cat_id)";
		echo create_drop_down( "cbo_item_group", 120, "SELECT id,item_name from  lib_item_group where status_active=1 and is_deleted=0 $sql_cond","id,item_name", 1, "--Select Item Group--", 1, "",0 );
        exit;
	}

    if ($action=="load_location")
    {
        echo create_drop_down( "location_id", 150, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in ($data) order by location_name","id,location_name", 1, "-- Select Location --", "", "" );
    }

	if ($action=="pi_dtls_popup")
	{
		echo load_html_head_contents("PI Details", "../../../../", 1, 1,$unicode,'','');
		extract($_REQUEST);
		//echo $pi_data."==".$prod_id;die;
		$porduct_data_ref=explode("*",$porduct_data_all);
		$pi_data_ref=explode("_",$pi_data);
		foreach($pi_data_ref as $value)
		{
			 $data_ref=explode("*",$value);
			 $all_piId[$data_ref[0]]= $data_ref[0];
			 $pi_ref_data[$data_ref[0]]+=$data_ref[1];
		}
		$sql_pipe_line="select b.pi_id, b.item_prod_id, b.quantity, a.pi_number, d.lc_number
		from com_pi_master_details a, com_pi_item_details b, com_btb_lc_pi c, com_btb_lc_master_details d 
		where a.id=b.pi_id and b.pi_id=c.pi_id and c.com_btb_lc_master_details_id=d.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and b.item_prod_id=$prod_id and b.pi_id in(".implode(",", $all_piId).")";
		//echo $sql_pipe_line;die;
		$data_array=sql_select($sql_pipe_line);
		?>
		<div style="width:730px;"> 
		<table align="center" cellspacing="0" width="730" border="1" rules="all" class="rpt_table" >
            <thead>
                <tr>   
                    <th width="30">SL</th>
                    <th width="100" >PI Number</th>
                    <th width="100" >LC Number</th>
                    <th width="100" >Item Category</th>
                    <th width="100" >Item Group</th>
                    <th width="150">Item Description</th>
                    <th width="80">PI Qty.</th>
                    <th>Balance Qty.</th>
                </tr>
            </thead>
            <tbody>
			<?
			$i=1;
            foreach($data_array as $row)
            {
				if ($i%2==0)  
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor;?>">
                    <td align="center"><? $i; ?></td>
                    <td align="center"><? echo $row[csf("pi_number")]; ?></td>
                    <td align="center"><? echo $row[csf("lc_number")]; ?></td>
                    <td><?  echo $porduct_data_ref[0]; ?></td>
                    <td><?  echo $porduct_data_ref[1]; ?></td>
                    <td><?  echo $porduct_data_ref[2]; ?></td>
                    <td align="right"><?  echo number_format($row[csf("quantity")],2,'.',''); ?></td>
                    <td align="right"><?  echo number_format($pi_ref_data[$row[csf("pi_id")]],2,'.',''); ?></td>
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

	if ($action=="item_account_popup")
	{
		echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
		extract($_REQUEST);
		$data=explode('_',$data);
		//print_r ($data);  
		?>	
	    <script>
		 var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
		 
		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
			
		function js_set_value(id)
		{
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			var strdt=str[2];
			str=str[1];
		
			if( jQuery.inArray(  str , selected_id ) == -1 ) {
				selected_id.push( str );
				selected_name.push( strdt );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#item_account_id').val( id );
			$('#item_account_val').val( ddd );
		} 
		</script>
	     <input type="hidden" id="item_account_id" />
	     <input type="hidden" id="item_account_val" />
	 	<?
			$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
			$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
			if ($data[2]==0) $item_name =""; else $item_name =" and item_group_id in($data[2])";
			
			$sql="SELECT id,item_account,item_category_id,item_group_id,item_description,supplier_id from  product_details_master where company_id in($data[0]) and item_category_id in($data[1]) $item_name and  status_active=1 and is_deleted=0"; 
			$arr=array(1=>$item_category,2=>$itemgroupArr,4=>$supplierArr);
			echo  create_list_view("list_view", "Item Account,Item Category,Item Group,Item Description,Supplier,Product ID", "70,110,150,150,100,70","780","400",0, $sql , "js_set_value", "id,item_description", "", 0, "0,item_category_id,item_group_id,0,supplier_id,0", $arr , "item_account,item_category_id,item_group_id,item_description,supplier_id,id", "",'setFilterGrid("list_view",-1);','0,0,0,0,0,0','',1) ;
			exit();
	}

	if($action=="generate_report")
	{
		$process = array(&$_POST);
		extract(check_magic_quote_gpc($process));
		$report_title=str_replace("'","",$report_title);
		$cbo_company_name=str_replace("'","",$cbo_company_name);
		$location_id=str_replace("'","",$location_id);
		$item_group_id=str_replace("'","",$cbo_item_group);
		$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
		$txt_item=str_replace("'","",$txt_item_acc); 
		$txt_date_from=str_replace("'","",$txt_date_from); 
		$txt_date_to=str_replace("'","",$txt_date_to); 
		$txt_prod_id=str_replace("'","",$txt_prod_id); 
		$item_account_id=str_replace("'","",$txt_item_account_id); 

		$sql_cond="";		
		if ($cbo_item_category_id !="") $sql_cond= " and b.item_category_id in($cbo_item_category_id)"; else $sql_cond.=" and b.item_category_id in(5,6,7,19,20,22,23,39)";		

		if ($item_account_id !="") $item_des_cond=" and b.id in ($item_account_id)"; 
		if ($cbo_item_group !="") $sql_cond.=" and b.item_group_id in ($cbo_item_group)";		
		if ($txt_prod_id !="") $sql_cond.=" and a.prod_id in ($txt_prod_id)";
		if ($location_id !="") $sql_cond.=" and a.location_id in ($location_id)";			
		if ($txt_prod_id !="") $sql_condd=" and a.prod_id in($txt_prod_id)";		
			
	
		$date_cond = "and b.insert_date between '" . change_date_format($txt_date_from, "yyyy-mm-dd", "-", 1) . "' and '" . change_date_format($txt_date_to, "yyyy-mm-dd", "-", 1) . "'";

		$trans_sql="SELECT b.id as PROD_ID , a.ITEM_CATEGORY , a.transaction_date as TRANSACTION_DATE, a.transaction_type as TRANSACTION_TYPE, a.cons_quantity as CONS_QUANTITY, a.cons_amount as CONS_AMOUNT, a.batch_lot as LOT_NO, a.receive_basis as RECEIVE_BASIS , b.ITEM_GROUP_ID, b.ITEM_DESCRIPTION, c.id as LIB_ITEM_GROUP_ID, c.ITEM_NAME, b.UNIT_OF_MEASURE 
		from inv_transaction a, product_details_master b, lib_item_group c
		where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.prod_id=b.id and b.item_group_id=c.id and a.company_id in($cbo_company_name) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond $item_des_cond $date_cond order by b.id ASC";
		
		// echo $trans_sql;//die;
		$trnasactionData = sql_select($trans_sql);
		//echo count($trnasactionData).jahid;die;
		$data_array=array();
		foreach($trnasactionData as $row_p)
		{
			if($row_p["TRANSACTION_TYPE"]==1)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]]['rcv_total_opening']+=$row_p["CONS_QUANTITY"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					if($row_p["RECEIVE_BASIS"]==1){
						$data_array[$row_p["PROD_ID"]]['pi_purchase']+=$row_p["CONS_QUANTITY"];
					}else if($row_p["RECEIVE_BASIS"]==2){
						$data_array[$row_p["PROD_ID"]]['wo_purchase']+=$row_p["CONS_QUANTITY"];
					}else if($row_p["RECEIVE_BASIS"]==4){
						$data_array[$row_p["PROD_ID"]]['independent_purchase']+=$row_p["CONS_QUANTITY"];
					}else{
						$data_array[$row_p["PROD_ID"]]['purchase']+=$row_p["CONS_QUANTITY"];
					}
				}
				else{
					if($row_p["RECEIVE_BASIS"]==1){
						$data_array[$row_p["PROD_ID"]]['pi_purchase']+=$row_p["CONS_QUANTITY"];
					}else if($row_p["RECEIVE_BASIS"]==2){
						$data_array[$row_p["PROD_ID"]]['wo_purchase']+=$row_p["CONS_QUANTITY"];
					}else if($row_p["RECEIVE_BASIS"]==4){
						$data_array[$row_p["PROD_ID"]]['independent_purchase']+=$row_p["CONS_QUANTITY"];
					}else{
						$data_array[$row_p["PROD_ID"]]['purchase']+=$row_p["CONS_QUANTITY"];
					}
				}
			}
			else if($row_p["TRANSACTION_TYPE"]==2)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]]['iss_total_opening']+=$row_p["CONS_QUANTITY"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					if($row_p["RECEIVE_BASIS"]==5){
						$data_array[$row_p["PROD_ID"]]['batch_issue']+=$row_p["CONS_QUANTITY"];
					}else if($row_p["RECEIVE_BASIS"]==7){
						$data_array[$row_p["PROD_ID"]]['req_issue']+=$row_p["CONS_QUANTITY"];
					}else if($row_p["RECEIVE_BASIS"]==4){
						$data_array[$row_p["PROD_ID"]]['independent_issue']+=$row_p["CONS_QUANTITY"];
					}else{
						$data_array[$row_p["PROD_ID"]]['issue']+=$row_p["CONS_QUANTITY"];
					}
				}
				else{
					if($row_p["RECEIVE_BASIS"]==5){
						$data_array[$row_p["PROD_ID"]]['batch_issue']+=$row_p["CONS_QUANTITY"];
					}else if($row_p["RECEIVE_BASIS"]==7){
						$data_array[$row_p["PROD_ID"]]['req_issue']+=$row_p["CONS_QUANTITY"];
					}else if($row_p["RECEIVE_BASIS"]==4){
						$data_array[$row_p["PROD_ID"]]['independent_issue']+=$row_p["CONS_QUANTITY"];
					}else{
						$data_array[$row_p["PROD_ID"]]['issue']+=$row_p["CONS_QUANTITY"];
					}
				}
			}

			else if($row_p["TRANSACTION_TYPE"]==3)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]]['iss_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['iss_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]]['receive_return']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['receive_return_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
			else if($row_p["TRANSACTION_TYPE"]==4)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]]['rcv_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['rcv_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]]['issue_return']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['issue_return_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
			else if($row_p["TRANSACTION_TYPE"]==5)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]]['rcv_total_opening']+=$row_p["CONS_QUANTITY"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]]['item_transfer_receive']+=$row_p["CONS_QUANTITY"];
				}
				else{
					$data_array[$row_p["PROD_ID"]]['item_transfer_receive']+=$row_p["CONS_QUANTITY"];
				}
			}
			else if($row_p["TRANSACTION_TYPE"]==6)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]]['iss_total_opening']+=$row_p["CONS_QUANTITY"];
				}

				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]]['item_transfer_issue']+=$row_p["CONS_QUANTITY"];
				}else{
					$data_array[$row_p["PROD_ID"]]['item_transfer_issue']+=$row_p["CONS_QUANTITY"];
				}
			}
			
			/*if($batch_lot_check[$row_p["PROD_ID"]][$row_p["LOT_NO"]]=="" && $row_p["LOT_NO"] !="")
			{
				$batch_lot_check[$row_p["PROD_ID"]][$row_p["LOT_NO"]]=$row_p["LOT_NO"];
				$data_array[$row_p["PROD_ID"]]['lot_no'].=$row_p["LOT_NO"].",";
			}
			
			if($row_p["TRANSACTION_TYPE"]==1 &&($row_p["RECEIVE_BASIS"]==1 || $row_p["RECEIVE_BASIS"]==2))
			{
				$data_array[$row_p["PROD_ID"]]['rcv_total_wo']+=$row_p["CONS_QUANTITY"];
			}*/
		}
		

		/*$sql="select a.company_id, a.prod_id, a.item_category, a.store_id, b.item_group_id, b.item_description, c.id as lib_item_group_id, c.item_name, b.unit_of_measure,
		sum((case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end) -
		(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as balance
	 	from inv_transaction a, product_details_master b, lib_item_group c
	 	where a.prod_id=b.id and b.item_group_id=c.id and a.company_id in($cbo_company_name) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond $item_des_cond $date_cond
	 	group by a.company_id, a.prod_id, a.item_category, a.store_id, b.item_group_id, b.item_description, c.id, c.item_name, b.unit_of_measure
	 	order by a.prod_id"; 
		$result = sql_select($sql); */
		
		/*$company_arr = sql_select("select id, company_name from lib_company where id in($cbo_company_name)");
		$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
		$store_name_arr=return_library_array("select id, store_name from  lib_store_location","id","store_name");

		$store_wise_qnty=array(); 
		$all_data=array();
		$all_company_ids=array();
		$all_store_ids=array();*/
		foreach($trnasactionData as $row)
		{
			/*$all_company_ids[$row[csf("company_id")]]=$row[csf("company_id")];
			$all_store_ids[$row[csf("company_id")]][$row[csf("store_id")]]=$row[csf("store_id")];*/
			$all_data[$row[csf("PROD_ID")]]["prod_id"]=$row[csf("PROD_ID")];
			$all_data[$row[csf("PROD_ID")]]["item_category"]=$row[csf("ITEM_CATEGORY")];
			$all_data[$row[csf("PROD_ID")]]["item_group_id"]=$row[csf("ITEM_GROUP_ID")];
			$all_data[$row[csf("PROD_ID")]]["item_description"]=$row[csf("ITEM_DESCRIPTION")];
			$all_data[$row[csf("PROD_ID")]]["item_group_name"]=$row[csf("ITEM_NAME")];
			$all_data[$row[csf("PROD_ID")]]["cons_uom"]=$row[csf("UNIT_OF_MEASURE")];
			//$store_wise_qnty[$row[csf("prod_id")]][$row[csf("company_id")]][$row[csf("store_id")]]+=$row[csf("balance")];
		}
		unset($trnasactionData);
		if($from_date!="" && $to_date!="")
		{
			$sql_loan="Select a.prod_id as prod_id, sum(a.cons_quantity) as cons_quantity, 1 as type
			from inv_transaction a, inv_receive_master b
			where a.mst_id=b.id and a.transaction_type=1 and b.receive_purpose=5 and a.transaction_date between '".$from_date."' and '".$to_date."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_loan_cond  group by a.prod_id
			union all
			Select a.prod_id as prod_id, sum(a.cons_quantity) as cons_quantity, 2 as type
			from inv_transaction a, inv_issue_master b
			where a.mst_id=b.id and a.transaction_type=2 and b.issue_purpose=5 and a.transaction_date between '".$from_date."' and '".$to_date."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_loan_cond $sql_condd group by a.prod_id
			order by prod_id ASC";
		}
		//echo $sql_loan;//die;
		$sql_loan_result=sql_select($sql_loan);
		$loan_data=array();
		foreach($sql_loan_result as $row)
		{
			if($row[csf("type")]==1)
			{
				$loan_data[$row[csf("prod_id")]]["loan_rcv_qnty"]=$row[csf("cons_quantity")];
			}
			else
			{
				$loan_data[$row[csf("prod_id")]]["loan_issue_qnty"]=$row[csf("cons_quantity")];
			}
		}
		
		/*echo '<pre>';
  		print_r($data_array);die;*/	
			
		$i=1;
		ob_start();
		$count_col=0;	
		foreach ($all_store_ids as $value) {
			foreach ($value as $val) {
				$count_col++;
			}
		}
		//echo $count_col;die;
		$table_with=1800;
		$div_with=$table_with+20;
		?>
		<div align="center" style="height:auto; margin:0 auto; padding:0; width:<? echo $div_with; ?>px">
			<table width="<? echo $table_with; ?>" cellpadding="0" cellspacing="0" id="caption" align="left">
				<thead>
					<tr style="border:none;">
						<td colspan="20" align="center" class="form_caption" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td> 
					</tr>
					<tr style="border:none;">
						<td colspan="20" align="center" class="form_caption" style="border:none;font-size:12px; font-weight:bold">
							<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
						</td>
					</tr>
				</thead>
			</table>
			<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $table_with; ?>" rules="all" id="rpt_table_header" align="left">
				<thead>
					<tr>
						<th rowspan="2" width="50">SL</th>
						<th rowspan="2" width="60">Prod. ID</th>
						<th rowspan="2" width="150">Item Category</th>
						<th rowspan="2" width="150">Item Group</th>
						<th rowspan="2" width="180">Item Description</th>
                        <th rowspan="2" width="60">UOM</th>
                        <th rowspan="2" width="80">Opening Stock</th>
                        <th colspan="5">Receive Basis</th>
                        <th rowspan="2" width="80">Total Receive</th>
                        <th colspan="5">Issue Basis</th>
                        <th rowspan="2" width="80">Total Issue</th>
						<th rowspan="2">Balance</th>
					</tr> 	
					<tr>
						<th width="80">PI</th>
						<th width="80">WO</th>
                        <th width="80">Independent</th>
						<th width="80">Store-Store Transfer IN</th>
						<th width="80">Purpose Loan</th>
                        <th width="80">Batch Basis</th>
						<th width="80">Requisition</th>
                        <th width="80">Independent</th>
						<th width="80">Store-Store Transfer Out</th>
						<th width="80">Purpose Loan</th>
					</tr>				
				</thead>
			</table>
			<div style="width:<? echo $div_with; ?>px; max-height:250px; overflow-y:scroll; overflow-x:hidden;" id="scroll_body"> 
			<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<? echo $table_with; ?>" rules="all" align="left">
				<tbody>
				<?
					foreach($all_data as $prod_id => $row)
					{
						if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
						//   
						$loan_receive=$loan_data[$prod_id]["loan_rcv_qnty"];
						$pi_receive=$data_array[$prod_id]['pi_purchase'];
						$wo_receive=$data_array[$prod_id]['wo_purchase'];
						$ind_receive=$data_array[$prod_id]['independent_purchase']-$loan_receive;
						$trans_in_receive=$data_array[$prod_id]['item_transfer_receive'];
						$total_rcv=$pi_receive+$wo_receive+$ind_receive+$trans_in_receive+$loan_receive;
						
						$loan_issue=$loan_data[$prod_id]["loan_issue_qnty"];
						$batch_issue=$data_array[$prod_id]['batch_issue'];
						$req_issue=$data_array[$prod_id]['req_issue'];
						$ind_issue=$data_array[$prod_id]['independent_issue']-$loan_issue;
						$trans_out_issue=$data_array[$prod_id]['item_transfer_issue'];
						$total_issue=$batch_issue+$req_issue+$ind_issue+$trans_out_issue+$loan_issue;

						$openingBalance = $data_array[$prod_id]['rcv_total_opening']-$data_array[$prod_id]['iss_total_opening'];
						$balance=(($openingBalance+$total_rcv)-$total_issue);
						

						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="50" align="center"><? echo $i; ?>&nbsp;</td>
							<td width="60" align="center"><? echo $row["prod_id"]; ?>&nbsp;</td>
							<td width="150" style="word-wrap:break-word;" ><? echo $item_category[$row[("item_category")]]; ?></td>
							<td width="150" style="word-wrap:break-word;"><? echo $row[("item_group_name")]; ?></td>
							<td width="180" style="word-wrap:break-word;"><? echo $row[("item_description")]; ?></td>
							<td width="60"><? echo $unit_of_measurement[$row[("cons_uom")]]; ?></td>
							<td width="80" align="right"><? echo number_format($openingBalance,3,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($pi_receive,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($wo_receive,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($ind_receive,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($trans_in_receive,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($loan_receive,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($total_rcv,2,'.',''); ?></td>


							<td width="80" align="right"><? echo number_format($batch_issue,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($req_issue,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($ind_issue,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($trans_out_issue,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($loan_issue,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($total_issue,2,'.',''); ?></td>
							<td align="right"><? echo number_format($balance,3,'.',''); ?></td>
						</tr>
						<?
						$total_openingBalance+=$openingBalance;
						$i++; 				
					}
				?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="6">Total:</th>
						<th width="80"align="right"><? echo number_format($total_openingBalance,3,'.',''); ?></th>
						<th colspan="13">&nbsp;</th>		
					</tr>
				</tfoot>
			</table>
			</div>
			
        </div>
    	<?	
	    $html = ob_get_contents();
	    ob_clean();
	    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	    foreach (glob("*.xls") as $filename) {
	    //if( @filemtime($filename) < (time()-$seconds_old) )
	    @unlink($filename);
	    }
	    //---------end------------//
	    $name=time();
	    $filename=$user_id."_".$name.".xls";
	    $create_new_doc = fopen($filename, 'w');	
	    $is_created = fwrite($create_new_doc, $html);
	    echo "$html**$filename**$report_type"; 
	    exit();	
	}
	

?>