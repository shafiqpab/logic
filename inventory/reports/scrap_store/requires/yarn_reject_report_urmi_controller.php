<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action==="item_account_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);  
    ?>	
    <script>
	var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	function check_all_data()
	{
		var tbl_row_count = document.getElementById('list_view' ).rows.length;
		tbl_row_count = tbl_row_count - 1;

		for( var i = 1; i <= tbl_row_count; i++ )
		{
			$('#tr_'+i).trigger('click'); 
		}
	}
	 
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
	function js_set_value(id)
	{
		//alert (id)
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
	
	$sql="SELECT id, item_category_id, product_name_details from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0"; 
	$arr=array(0=>$item_category);

	echo  create_list_view("list_view", "Item Category,Fabric Description,Product ID", "120,250,60","490","300",0, $sql , "js_set_value", "id,product_name_details", "", 1, "item_category_id,0,0", $arr, "item_category_id,product_name_details,id", "",'setFilterGrid("list_view",-1);','0,0,0','',1) ;
	exit();
}

if($action==="party_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//$data=explode('_',$data);
	//echo $companyID;
	?>
	<script>
		var selected_id = new Array; var selected_name = new Array;
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			
			if (str!="") str=str.split("_");
			 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
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
			
			$('#hide_party_id').val( id );
			$('#hide_party_name').val( name );
		}
    </script>
    <input type="hidden" name="hide_party_name" id="hide_party_name" value="" />
    <input type="hidden" name="hide_party_id" id="hide_party_id" value="" />
	<?

	$sql="select a.id, a.supplier_name as party_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where a.id=b.supplier_id and c.supplier_id=b.supplier_id and c.tag_company=$companyID and b.party_type in(2,3,20) and a.status_active=1  group by a.id, a.supplier_name order by a.supplier_name";
	echo create_list_view("tbl_list_search", "Party Name", "380","380","270",0, $sql , "js_set_value", "id,party_name", "", 1, "0", $arr , "party_name", "",'setFilterGrid("tbl_list_search",-1);','0','',1) ;
	
   exit(); 
} 

if($action==="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$lot_no = str_replace("'","",$txt_lot_no);
	$txt_supplier_id = str_replace("'","",$txt_supplier_id);

	$company_id=$supplier_cond=$lot_no_cond=$prod_id_cond=$search_by_zero_cond='';

	if ($cbo_company_id != 0) $company_id = " and a.company_id=$cbo_company_id";
	if ($txt_supplier_id != 0) $supplier_cond = " and c.supplier_id in($txt_supplier_id)";
	if ($lot_no != '') $lot_no_cond = "and lot='$lot_no'";
	if ($txt_product_id != '') $prod_id_cond =" and b.product_id in ($txt_product_id)";
	//echo $lot_no_cond.'system';

	if($db_type==0) 
	{
		$from_date=change_date_format($from_date,'yyyy-mm-dd');
		$to_date=change_date_format($to_date,'yyyy-mm-dd');
	}
	else
	{
		$from_date=change_date_format($from_date,'','',1);
		$to_date=change_date_format($to_date,'','',1);
	}
	//echo $from_date;die;

	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$supplierArr = return_library_array("select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");

	$sql = "select b.PRODUCT_ID, c.PRODUCT_NAME_DETAILS, c.LOT, c.SUPPLIER_ID,
		sum(case when a.receive_date<'".$from_date."' then b.receive_qnty else 0 end) as RECV_TOTAL_OPENING,
		sum(case when a.receive_date between '".$from_date."' and '".$to_date."' then b.receive_qnty else 0 end) as REJECT_QTY
		from inv_scrap_receive_mst a, inv_scrap_receive_dtls b, product_details_master c 
		where a.id=b.mst_id and b.product_id=c.id and a.company_id=$cbo_company_id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $lot_no_cond $prod_id_cond $supplier_cond
		group by b.product_id, c.product_name_details, c.lot, c.supplier_id";	 
	//echo $sql;die;	
	$sql_res = sql_select($sql);
	$tot_rows=0;
	foreach ($sql_res as $val) {
		$prod_ids .= $val['PRODUCT_ID'].',';
		$tot_rows++;
	}

	if ($prod_ids != '')
	{
		$prod_Ids = array_flip(array_flip(explode(',', rtrim($prod_ids,','))));
		$prod_ids_in_cond=$prod_ids_in_cond2='';

		if($db_type==2 && $tot_rows>1000)
		{
			$prod_ids_in_cond = ' and (';
			$prod_idArr = array_chunk($prod_Ids,999);
			foreach($prod_idArr as $ids)
			{
				$ids = implode(',',$ids);
				$prod_ids_in_cond .= " b.prod_id in($ids) or ";
			}
			$prod_ids_in_cond = rtrim($prod_ids_in_cond,'or ');
			$prod_ids_in_cond .= ')';
		}
		else
		{
			$prod_Ids = implode(',', $prod_Ids);
			$prod_ids_in_cond = " and b.prod_id in ($prod_Ids)";
		}
	}
	//echo $prod_ids_in_cond;
	$data_array=array();		
	$sql_scrap = "select b.PROD_ID, 
	    sum(case when a.selling_date between '".$from_date."' and '".$to_date."' then b.sales_qty else 0 end) as SALES_QTY 
	    from inv_scrap_sales_mst a, inv_scrap_sales_dtls b 
	    where a.id=b.mst_id and a.company_id=$cbo_company_id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $prod_ids_in_cond
	    group by b.prod_id";
	//echo $sql_scrap;die;    
	$sql_scrap_res=sql_select($sql_scrap);
	foreach($sql_scrap_res as $row)	{
		$scrap_qty_arr[$row['PROD_ID']]['SALES_QTY']=$row['SALES_QTY'];
	}
	
	$sql_yrecv = "select b.PROD_ID, min(b.TRANSACTION_DATE) as TRANSACTION_DATE, avg(b.order_rate) as ORDER_RATE_USD, avg(b.cons_rate) as CONS_RATE_TK, sum(b.order_amount) as ORDER_AMOUNT_USD, sum(b.cons_amount) as CONS_AMOUNT_TK
		from inv_receive_master a, inv_transaction b 
		where a.company_id=$cbo_company_id and a.id=b.mst_id and b.transaction_type=1 and a.item_category=1 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $prod_ids_in_cond  
		group by b.prod_id";
	//echo $sql_yrecv;die; 	
	$sql_yrecv_res = sql_select($sql_yrecv);
	foreach($sql_yrecv_res as $row)
	{
		$yarn_recv_arr[$row['PROD_ID']]['ORDER_RATE_USD']=$row['ORDER_RATE_USD'];
		$yarn_recv_arr[$row['PROD_ID']]['CONS_RATE_TK']=$row['CONS_RATE_TK'];
		$yarn_recv_arr[$row['PROD_ID']]['ORDER_AMOUNT_USD']=$row['ORDER_AMOUNT_USD'];
		$yarn_recv_arr[$row['PROD_ID']]['CONS_AMOUNT_TK']=$row['CONS_AMOUNT_TK'];
		$yarn_recv_arr[$row['PROD_ID']]['TRANSACTION_DATE']=$row['TRANSACTION_DATE'];
	}
	
	ob_start();
	?>

	<div>
		<table style="width:1550px" border="1" cellpadding="2" cellspacing="0"  id="caption" rules="all"> 
			<thead>
				<tr class="form_caption" style="border:none;">
					<td colspan="15" align="center" style="border:none;font-size:16px; font-weight:bold" ><?= $report_title; ?></td> 
				</tr>
				<tr class="form_caption" style="border:none;">
					<td colspan="15" align="center" style="border:none; font-size:14px;">
					   <b>Company Name : <?= $companyArr[$cbo_company_id]; ?></b>                               
					</td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td colspan="10" align="center" style="border:none;font-size:12px; font-weight:bold">
						<? if($from_date != '' || $to_date != '') echo "From : ".change_date_format($from_date)." To : ".change_date_format($to_date) ;?>
					</td>
				</tr>
			</thead>
		</table>
		<table width="1520" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th rowspan="2" width="40">SL</th>
					<th rowspan="2" width="60">Prod.ID</th>
					<th colspan="3">Description</th>
					<th rowspan="2" width="110">Opening Stock</th>
					<th rowspan="2" width="100">Reject Qty.</th>
                    <th rowspan="2" width="100">Scrap Sales Qty</th>
					<th rowspan="2" width="100">Closing Stock</th>
                    <th rowspan="2" width="100">Avg. Purchase Rate(Tk)</th>
                    <th rowspan="2" width="100">Avg. Purchase Value(Tk)</th>
                    <th rowspan="2" width="100">Avg. Purchase Rate(USD)</th>
                    <th rowspan="2" width="100">Avg. Purchase Value(USD)</th>
                    <th rowspan="2" width="80">Original Age</th>                       
					<th rowspan="2">Remarks</th>
				</tr>
				<tr>                         
					<th width="180">Product Name</th>
					<th width="100">Lot</th>
					<th width="100">Supplier</th>
				</tr> 
			</thead>
		</table>
		<div style="width:1540px; max-height:350px; overflow-y:scroll" id="scroll_body" > 
			<table width="1520" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			    <?
				$i=1;
				$tot_recv_order_amount_tk=$tot_recv_order_amount_usd=0;
				$tot_opening_stock=$tot_reject_qty=$tot_scrap_out_qty=$tot_closingStock=0;
				$result = sql_select($sql);
				foreach($sql_res as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; 
					else $bgcolor="#FFFFFF"; 
					$closingStock=0;	
					$opening_stock = $row['RECV_TOTAL_OPENING'];
					$reject_qty    = $row['REJECT_QTY'];
					$scrap_out_qty = $scrap_qty_arr[$row['PRODUCT_ID']]['SALES_QTY'];
					$closingStock  = $opening_bal+$reject_qty-$scrap_out_qty;
					$avg_tk_rate   = $yarn_recv_arr[$row['PRODUCT_ID']]['CONS_RATE_TK'];
					$avg_usd_rate  = $yarn_recv_arr[$row['PRODUCT_ID']]['ORDER_RATE_USD'];
					
					$recv_order_amount_tk  = $closingStock*$avg_tk_rate;
					$recv_order_amount_usd = $closingStock*$avg_usd_rate;

					$original_age = '';
					$transaction_date = $yarn_recv_arr[$row['PRODUCT_ID']]['TRANSACTION_DATE'];
					$transactiondate  = date("d-m-Y",strtotime($transaction_date));
					$today_date       = date("d-m-Y");
					if($transaction_date != '')
					 	$original_age = datediff("d", $transactiondate , $today_date);

					$tot_opening_stock += $opening_stock;
					$tot_reject_qty    += $reject_qty;
					$tot_scrap_out_qty += $scrap_out_qty;
					$tot_closingStock  += $closingStock;
					$tot_recv_order_amount_tk  += $recv_order_amount_tk;
					$tot_recv_order_amount_usd += $recv_order_amount_usd;

					if ($cbo_search_by_zero != 1 && $opening_stock != 0)
					{			
						?>
						<tr bgcolor="<?= $bgcolor; ?>" <?= $stylecolor; ?> onclick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" id="tr_<?= $i; ?>">
							<td width="40"><?= $i; ?></td>
							<td width="60" align="center"><p><?= $row['PRODUCT_ID']; ?></p></td>
							<td width="180"><p><?= $row['PRODUCT_NAME_DETAILS']; ?></p></td>
							<td width="100"><p><?= $row['LOT']; ?></p></td> 
							<td width="100"><div style="word-break:break-word; width:100px"><?= $supplierArr[$row['SUPPLIER_ID']]; ?></div></td> 
							<td width="110" align="right"><p><?= number_format($opening_stock,2); ?></p></td>
							<td width="100" align="right">
								<p><a href="#report_details" onClick="openmypage_yarn('<?= $row['PRODUCT_ID']; ?>','<?= $cbo_company_id;?>','yarn_reject_qty_popup','Yarn Reject Details','1100px','<?= $from_date;?>','<?= $to_date;?>')"><?= number_format($reject_qty,2); ?></a></p>
	                        </td>
	                        <td width="100" align="right"><p><a href="#report_details" onClick="openmypage_yarn('<?= $row['PRODUCT_ID']; ?>','<?= $cbo_company_id; ?>','scrap_qty_popup','Scrap Out Details','700px','<?= $from_date; ?>','<?= $to_date; ?>')"><?= number_format($scrap_out_qty,2); ?></a></p>
	                        </td>
							<td width="100" align="right"><p><?= number_format($closingStock,2); ?></p></td>
	                        
	                        <td width="100" align="right"><p><?= number_format($avg_tk_rate,2); ?></p></td>
	                        <td width="100" align="right"><p><?= number_format($recv_order_amount_tk,2); ?></p></td>
	                        <td width="100" align="right"><p><?= number_format($avg_usd_rate,2); ?></p></td>
	                        <td width="100" align="right"><p><?= number_format($recv_order_amount_usd,2); ?></p></td>
	                        <td width="80" align="right" title="Trans Date= <?=  $transaction_date;?>"><p><?= $original_age; ?></p></td>
							<td>&nbsp;</td>
						</tr>
						<?
						$i++;
					}
					else if ($cbo_search_by_zero == 1)
					{
						?>
						<tr bgcolor="<?= $bgcolor; ?>" <?= $stylecolor; ?> onclick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" id="tr_<?= $i; ?>">
							<td width="40"><?= $i; ?></td>
							<td width="60" align="center"><p><?= $row['PRODUCT_ID']; ?></p></td>
							<td width="180"><p><?= $row['PRODUCT_NAME_DETAILS']; ?></p></td>
							<td width="100"><p><?= $row['LOT']; ?></p></td> 
							<td width="100"><div style="word-break:break-word; width:100px"><?= $supplierArr[$row['SUPPLIER_ID']]; ?></div></td> 
							<td width="110" align="right"><p><?= number_format($opening_stock,2); ?></p></td>
							<td width="100" align="right">
								<p><a href="#report_details" onClick="openmypage_yarn('<?= $row['PRODUCT_ID']; ?>','<?= $cbo_company_id;?>','yarn_reject_qty_popup','Yarn Reject Details','1200px','<?= $from_date;?>','<?= $to_date;?>')"><?= number_format($reject_qty,2); ?></a></p>
	                        </td>
	                        <td width="100" align="right"><p><a href="#report_details" onClick="openmypage_yarn('<?= $row['PRODUCT_ID']; ?>','<?= $cbo_company_id; ?>','scrap_qty_popup','Scrap Out Details','700px','<?= $from_date; ?>','<?= $to_date; ?>')"><?= number_format($scrap_out_qty,2); ?></a></p>
	                        </td>
							<td width="100" align="right"><p><?= number_format($closingStock,2); ?></p></td>
	                        
	                        <td width="100" align="right"><p><?= number_format($avg_tk_rate,2); ?></p></td>
	                        <td width="100" align="right"><p><?= number_format($recv_order_amount_tk,2); ?></p></td>
	                        <td width="100" align="right"><p><?= number_format($avg_usd_rate,2); ?></p></td>
	                        <td width="100" align="right"><p><?= number_format($recv_order_amount_usd,2); ?></p></td>
	                        <td width="80" align="right" title="Trans Date= <?=  $transaction_date;?>"><p><?= $original_age; ?></p></td>
							<td>&nbsp;</td>
						</tr>
						<?
						$i++;
					}									 				
				}
				?>
			</table>
			<table width="1520" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
				<tfoot>
				   <tr>
						<th width="40" align="right">&nbsp;</th>
						<th width="60" align="right">&nbsp;</th>
						<th width="180" align="right">&nbsp;</th>
						<th width="100" align="right">&nbsp;</th>
						<th width="100" align="right">Total&nbsp;</th>
						<th width="110" align="right" id="value_tot_opening_stock"><?= number_format($tot_opening_stock,2); ?></th>
						<th width="100" align="right" id="value_tot_reject_qty"><?= number_format($tot_reject_qty,2); ?></th>
		                <th width="100" align="right" id="value_tot_scrap_out_qty"><?= number_format($tot_scrap_out_qty,2); ?></th>
						<th width="100" align="right" id="value_tot_closingStock"><?= number_format($tot_closingStock,2); ?></th>		                
		                <th width="100" align="right" id="">&nbsp;</th>
		                <th width="100" align="right" id=""><?= number_format($tot_recv_order_amount_tk,2);  ?></th>
		                <th width="100" align="right" id="">&nbsp;</th>
		                <th width="100" align="right" id=""><?= number_format($tot_recv_order_amount_usd,2);  ?></th>
		                <th width="80" align="right">&nbsp;</th>
						<th align="right">&nbsp;</th>
					</tr>
				</tfoot>
			</table>
		</div>		
	</div>
	<?
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    	@unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$type"; 
    exit();
}

if($action==="yarn_reject_qty_popup")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	
	$company_arr = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$location_arr = return_library_array("select id, location_name from lib_location where company_id=$company_id and status_active=1 and is_deleted=0","id","location_name");
	$store_arr = return_library_array("select id, store_name from lib_store_location where company_id=$company_id and status_active=1 and is_deleted=0","id","store_name");
	$receive_scrap_arra = array(1=>"Receive-Reject",2=>"Issue-Damage", 3=>"Issue-Scrape Store");

	$sql = "select a.COMPANY_ID, a.SYS_RECEIVE_NO, a.LOCATION, a.STORE_ID, a.RECEIVE_DATE, a.RECEIVE_BASIS, b.RECEIVE_QNTY, b.RATE, b.AMOUNT, b.COLOR, b.LOT, b.BOOK_CURRENCY, c.PRODUCT_NAME_DETAILS 
	    from inv_scrap_receive_mst a, inv_scrap_receive_dtls b, product_details_master c
	    where a.id=b.mst_id and b.product_id=c.id and a.company_id=$company_id and c.id=$prod_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	    order by receive_date desc";
	//echo $sql;die;    
	$sql_res = sql_select($sql);

    ?>
    <div>
        <table width="1190" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <caption><strong>Yarn Reject Detail</strong></caption>
			<thead>
			    <tr>
			        <th width="30">SL</th>
			        <th width="120">Company Name</th>
			        <th width="80">Location</th>
			        <th width="100">Store Name</th>
			        <th width="90">System ID</th>
			        <th width="70">Receive Date</th>
			        <th width="100">Receive Basis</th>
			        <th width="150">Item Description</th>
			        <th width="80">Color</th>
			        <th width="100">Lot/Batch</th>
			        <th width="70">Receive Qty</th>
			        <th width="50">Rate</th>
			        <th width="70">Amount</th>
			        <th width="">Book Currency</th>
			    </tr>
			</thead>
	        <tbody id="table_body_popup">
                <?
				$k=1;
				$tot_receive_qnty=$tot_amount=0;
				foreach($sql_res as $row)
				{
					if($k%2==0) $bgcolor="#E9F3FF";  
					else $bgcolor="#FFFFFF";
		            ?>
		            <tr bgcolor="<?= $bgcolor; ?>" <?= $stylecolor; ?> onclick="change_color('tr_<?= $k; ?>','<?= $bgcolor; ?>')" id="tr_<?= $k; ?>">
			            <td width="30"><?= $k; ?></td>
			            <td width="120"><p><?= $company_arr[$row['COMPANY_ID']]; ?></p></td>
			            <td width="80"><p><?= $location_arr[$row['LOCATION']]; ?></p></td>
			            <td width="100"><p><?= $store_arr[$row['STORE_ID']]; ?></p></td>
			            <td width="90"><p><?= $row['SYS_RECEIVE_NO']; ?></p></td> 
			            <td width="70"><p><?= change_date_format($row['RECEIVE_DATE']); ?></p></td>
			            <td width="100"><p><?= $receive_scrap_arra[$row['RECEIVE_BASIS']]; ?></p></td>
			            <td width="150"><p><?= $row['PRODUCT_NAME_DETAILS']; ?></p></td>
			            <td width="80"><p><?= $color_arr[$row['COLOR']]; ?></p></td>
			            <td width="100"><p><?= $row['LOT']; ?></p></td>
			            <td width="70" align="right"><p><?= number_format($row['RECEIVE_QNTY'],2); ?></p></td>
			            <td width="50" align="right"><p><?= number_format($row['RATE'],2); ?></p></td>
			            <td width="70" align="right"><p><?= number_format($row['AMOUNT'],2); ?></p></td>
			            <td width="" align="center"><p><?= $row['BOOK_CURRENCY']; ?></p></td>
		            </tr>
		            <?
		            $k++;
					$tot_receive_qnty +=$row['RECEIVE_QNTY'];
					$tot_amount += $row['AMOUNT'];					
				}
				?>
        		<tfoot>
        			<tr>
        				<th colspan="10">Total&nbsp;</th>
        				<th><? echo number_format($tot_receive_qnty,2); ?></th>
        				<th>&nbsp;</th>
         				<th><? echo number_format($tot_amount,2); ?></th>
        				<th>&nbsp;</th>
        			</tr>
        		</tfoot>
 			</tbody>
		</table>
		<script>setFilterGrid("table_body_popup",-1);</script>
	</div>
    <?	
}

if($action==="scrap_qty_popup")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
    ?>
    <div>
        <table width="690" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <caption><strong>Scrap Out Details</strong> </caption>
			<thead>
			    <tr>
			        <th width="30">SL</th>
			        <th width="120">Sys. ID</th>
			        <th width="100">Selling Date</th>
			        <th width="100">Sold Qty.</th>
			        <th width="80">Selling Rate</th>
			        <th width="100">Sales Amount</th>
			        <th width="">Remark</th>
			    </tr>
            </thead>
	        <tbody id="table_body_popup">
                <?
     			$sql_scrap = "select b.PROD_ID, a.SYS_CHALLAN_NO, a.SELLING_DATE, a.REMARKS,
					sum(case when a.selling_date between '".$from_date."' and '".$to_date."' then b.sales_qty else 0 end) as SCRAP_QTY,
					sum(case when a.selling_date between '".$from_date."' and '".$to_date."' then b.sales_amount else 0 end) as SALES_AMOUNT,
					avg(b.sales_rate) as SALES_RATE
					from inv_scrap_sales_mst a, inv_scrap_sales_dtls b 
					where a.id=b.mst_id and company_id=$company_id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.prod_id=$prod_id 
					group by b.prod_id, a.sys_challan_no, a.selling_date, a.remarks";
				//echo $sql_scrap;die;		
	            $sql_scrap_res=sql_select($sql_scrap);
				$k=1;
				$tot_scrap_qty=$tot_sold_qty=$tot_sales_amount=0;
				foreach($sql_scrap_res as $row)
				{
					if($k%2==0)$bgcolor="#E9F3FF";  
					else $bgcolor="#FFFFFF"; 
					
					$scrap_qty   = $row['SCRAP_QTY'];
					$sales_rate  = $row['SALES_RATE'];
					$sales_amount= $row['SALES_AMOUNT'];				
						
			        if($row['SCRAP_QTY']>0)
			        {
                        ?>
				        <tr bgcolor="<?= $bgcolor; ?>" <?= $stylecolor; ?> onclick="change_color('tr_<?= $k; ?>','<?= $bgcolor; ?>')" id="tr_<?= $k; ?>">
				            <td width="30"><?= $k; ?></td>
				            <td width="120" align="center"><p><?= $row['SYS_CHALLAN_NO']; ?></p></td>
				            <td width="100"><p><?= change_date_format($row['SELLING_DATE']); ?></p></td>
				            <td width="100" align="right"><p><?= number_format($scrap_qty,2); ?></p></td> 
				            <td width="80" align="right"><div style="word-break:break-word; width:80px"><?= number_format($sales_rate,2); ?></div></td> 
				            <td width="100" align="right"><p><?= number_format($sales_amount,2); ?></p></td>    
				            <td><p><?= $row['REMARKS']; ?></p></td>
				        </tr>
				        <?
						$k++;
						$tot_sold_qty    += $sold_qty;
						$tot_scrap_qty   += $scrap_qty;
						$tot_sales_amount+= $sales_amount;
					}
				}
				?>
	            <tfoot>
	                <tr>
	                    <th colspan="3">Total&nbsp;</th>
	                    <th align="right"><?= number_format($tot_scrap_qty,2); ?></th>
	                    <th><?= number_format($tot_scrap_qty,2); ?></th>
	                    <th align="right"><?= number_format($tot_sales_amount,2); ?></th>
	                    <th>&nbsp;</th>
	                </tr>
	            </tfoot>
		    </tbody>
        </table>
        <script>setFilterGrid("table_body_popup",-1);</script>
    </div>
    <?	
}

?>