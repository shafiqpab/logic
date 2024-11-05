<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="item_account_popup")
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
if($action=="party_popup")
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
if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$lot_no=str_replace("'","",$txt_lot_no);
	$txt_supplier_id=str_replace("'","",$txt_supplier_id);
	
	if ($cbo_company_id==0) $company_id =""; else $company_id =" and a.company_id='$cbo_company_id'";
	if ($txt_supplier_id==0) $supplier_cond =""; else $supplier_cond =" and b.supplier_id in($txt_supplier_id)";
	if ($lot_no!="") $lot_no_cond ="and lot='$lot_no'"; else $lot_no_cond ="";
	//echo $lot_no_cond.'gggggggggggg';
	if ($txt_product_id=="") 
	{
		$prod_id_cond=""; $prod_id_cond2=""; 
	}
	else 
	{
		$prod_id_cond=" and c.prod_id in ($txt_product_id)";
		$prod_id_cond2=" and b.prod_id in ($txt_product_id)";
	}
	
	//if ($cbo_store_id==0){ $store_id="";}else{$store_id=" and a.store_id='$cbo_store_id'";}
	
	if($db_type==0) 
	{
		$from_date=change_date_format($from_date,'yyyy-mm-dd');
		$to_date=change_date_format($to_date,'yyyy-mm-dd');
	}
	else if($db_type==2) 
	{
		$from_date=change_date_format($from_date,'','',1);
		$to_date=change_date_format($to_date,'','',1);
	}
	if ($cbo_search_by_zero==1){ $search_by_zero_cond="";}else{$search_by_zero_cond=" and c.reject_qty <>0 ";}

	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
	$buyerArr = return_library_array("select id,short_name from lib_buyer where status_active=1 and is_deleted=0","id","short_name"); 
	$brandArr = return_library_array("select id,brand_name from lib_brand where status_active=1 and is_deleted=0","id","brand_name");
	$supplierArr = return_library_array("select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name"); 
	$determinaArr = return_library_array("select id,construction from lib_yarn_count_determina_mst where status_active=1 and is_deleted=0","id","construction");
	
	$data_array=array();
		
	$sql_scrap="select b.prod_id, sum(case when a.selling_date<'".$from_date."' then b.sales_qty else 0 end) as sales_total_opening, sum(case when a.selling_date between '".$from_date."' and '".$to_date."' then b.sales_qty else 0 end) as sales_qty from  inv_scrap_sales_mst a, inv_scrap_sales_dtls b where a.id=b.mst_id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.prod_id";
	$sql_scrap_result=sql_select($sql_scrap);
	foreach($sql_scrap_result as $row)
	{
		$data_array[$row[csf('prod_id')]]['opening']=$row[csf('sales_total_opening')];
		$data_array[$row[csf('prod_id')]]['scrip']=$row[csf('sales_qty')];
	}
	
	$product_array=array();
		
	$sql_prod="select id, product_name_details, lot, yarn_count_id, yarn_type, brand, supplier_id from product_details_master where company_id='$cbo_company_id' and item_category_id=1 and status_active=1 and is_deleted=0 $lot_no_cond";
	$sql_prod_result=sql_select($sql_prod);
	$all_prod_ids="";
	foreach($sql_prod_result as $row)
	{
		$product_array[$row[csf('id')]]['dtls']=$row[csf('product_name_details')];
		$product_array[$row[csf('id')]]['lot']=$row[csf('lot')];
		$product_array[$row[csf('id')]]['yarn_count_id']=$row[csf('yarn_count_id')];
		$product_array[$row[csf('id')]]['yarn_type']=$row[csf('yarn_type')];
		$product_array[$row[csf('id')]]['brand']=$row[csf('brand')];
		$product_array[$row[csf('id')]]['supplier_id']=$row[csf('supplier_id')];
		if($all_prod_ids!="") $all_prod_ids.=",".$row[csf('id')];else $all_prod_ids=$row[csf('id')];
		
	}
	if($lot_no!="")
	{
		$prodIds=chop($all_prod_ids,',');
		//echo $jobIds;die;
		$prod_cond_for_in="";
		$prod_ids=count(array_unique(explode(",",$all_job_id)));
			if($db_type==2 && $prod_ids>1000)
			{
				$prod_cond_for_in=" and (";
				$prodIdsArr=array_chunk(explode(",",$prodIds),999);
				foreach($prodIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$prod_cond_for_in.=" b.prod_id in($ids) or"; 
				}
				$prod_cond_for_in=chop($prod_cond_for_in,'or ');
				$prod_cond_for_in.=")";
			}
			else
			{
				$prodIds=implode(",",array_unique(explode(",",$all_prod_ids)));
				$prod_cond_for_in=" and b.prod_id in($prodIds)";
			}
	}
		
	//echo $prod_cond_for_in;
	ob_start();	
	
	if($type==1)
	{
		?>
		<div>
			<table style="width:1600px" border="1" cellpadding="2" cellspacing="0"  id="caption" rules="all"> 
				<thead>
					<tr class="form_caption" style="border:none;">
						<td colspan="15" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td> 
					</tr>
					<tr class="form_caption" style="border:none;">
						<td colspan="15" align="center" style="border:none; font-size:14px;">
						   <b>Company Name : <? echo $companyArr[$cbo_company_id]; ?></b>                               
						</td>
					</tr>
					<tr class="form_caption" style="border:none;">
						<td colspan="15" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? if($from_date!="" || $to_date!="") echo "From : ".change_date_format($from_date)." To : ".change_date_format($to_date)."" ;?>
						</td>
					</tr>
				</thead>
			</table>
			<table width="1600" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th rowspan="2" width="40">SL</th>
						<th rowspan="2" width="60">Job</th>
						<th rowspan="2" width="60">Year</th>
						<th rowspan="2" width="80">Buyer</th>
						<th rowspan="2" width="80">Style</th>
                        <th rowspan="2" width="80">Order No</th>
						<th rowspan="2" width="60">Prod.ID</th>
						<th colspan="3">Description</th>
						<th rowspan="2" width="120">Booking/Req.</th>
						<th rowspan="2" width="110">Opening Stock</th>
						<th rowspan="2" width="100">Reject Qty.</th>
						<th rowspan="2" width="100">Closing Stock</th>
						<th rowspan="2">Remarks</th>
					</tr>
					<tr>                         
						<th width="180">Product Name</th>
						<th width="100">Lot</th>
						<th width="100">Supplier</th>
					</tr> 
				</thead>
			</table>
			<div style="width:1600px; max-height:350px; overflow-y:scroll" id="scroll_body" > 
				<table width="1582" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
				<?
					/*$sql = "select a.booking_id, a.booking_no, c.po_breakdown_id, c.prod_id,
					sum(case when b.transaction_date<'".$from_date."' then c.reject_qty else 0 end) as rej_total_opening,
					sum(case when b.transaction_date between '".$from_date."' and '".$to_date."' then c.reject_qty else 0 end) as reject_qty
					from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.company_id='$cbo_company_id' and a.id=b.mst_id and b.id=c.trans_id and c.trans_type=4 and b.transaction_type=4 and a.entry_form=9 and c.entry_form=9 and a.item_category=1 and b.item_category=1 and c.reject_qty!=0 $prod_id_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.booking_id, a.booking_no, c.po_breakdown_id, c.prod_id order by c.prod_id";*/
					if($db_type==0) $year_cond="YEAR(d.insert_date)"; 
					else if($db_type==2) $year_cond="to_char(d.insert_date,'YYYY')";

					$sql = "select a.booking_id, a.booking_no,
					d.job_no_prefix_num, d.job_no, $year_cond as year, d.buyer_name, d.style_ref_no, e.id, e.po_number, c.prod_id,
					sum(case when b.transaction_date<'".$from_date."' then c.reject_qty else 0 end) as rej_total_opening,
					sum(case when b.transaction_date between '".$from_date."' and '".$to_date."' then c.reject_qty else 0 end) as reject_qty
					from inv_receive_master a, inv_transaction b, order_wise_pro_details c, wo_po_details_master d, wo_po_break_down e
					
					where a.company_id='$cbo_company_id' and a.id=b.mst_id and b.id=c.trans_id and c.po_breakdown_id=e.id and d.job_no=e.job_no_mst
					and c.trans_type=4 and b.transaction_type=4 and a.entry_form=9 and c.entry_form=9 and a.item_category=1 and b.item_category=1 and c.reject_qty!=0 $prod_id_cond 
					and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $prod_cond_for_in  $supplier_cond
					
					group by a.booking_id, a.booking_no, d.job_no_prefix_num, d.job_no, d.insert_date, d.buyer_name, d.style_ref_no, e.id, e.po_number, c.prod_id order by c.prod_id";	
					//echo $sql;
					$i=1;
					$result = sql_select($sql);
					foreach($result as $row)
					{
						if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
						$closingStock=0;
		
						$opening=$row[csf("rej_total_opening")];
						$reject_qty=$row[csf("reject_qty")];
						
						$closingStock=$opening+$reject_qty;//-$scrap_out_qty;
						$tot_opening+=$opening;
						$tot_reject_qty+=$reject_qty;
						//$tot_scrap_out_qty+=$scrap_out_qty;
						$tot_closingStock+=$closingStock;
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="40"><? echo $i; ?></td>
								<td width="60" align="center"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
								<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
								<td width="80"><div style="word-wrap:break-word; width:80px"><? echo $buyerArr[$row[csf('buyer_name')]]; ?></div></td>
								<td width="80"><div style="word-wrap:break-word; width:80px"><? echo $row[csf('style_ref_no')]; ?></div></td>
                                <td width="80"><div style="word-wrap:break-word; width:80px"><? echo $row[csf('po_number')]; ?></div></td>
								<td width="60" align="center"><p><? echo $row[csf("prod_id")]; ?></p></td>
								<td width="180"><p><? echo $product_array[$row[csf('prod_id')]]['dtls']; ?></p></td>
								<td width="100"><p><? echo $product_array[$row[csf('prod_id')]]['lot']; ?></p></td> 
								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $supplierArr[$product_array[$row[csf('prod_id')]]['supplier_id']]; ?></div></td> 
								<td width="120"><p><? echo $row[csf("booking_no")]; ?></p></td>
								<td width="110" align="right"><p><? echo number_format($opening,2); ?></p></td>
								<td width="100" align="right"><p><? echo number_format($reject_qty,2); ?></p></td>
								<td width="100" align="right"><p><? echo number_format($closingStock,2); ?></p></td>
								<td>&nbsp;</td>
							</tr>
						 <? 												
						 $i++; 				
					}
					?>
				</table>
			</div> 
			<table width="1600" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" > 
			   <tr>
					<td width="40" align="right">&nbsp;</th>
					<td width="60" align="right">&nbsp;</th>
					<td width="60" align="right">&nbsp;</th>
					<td width="80" align="right">&nbsp;</th>
					<td width="80" align="right">&nbsp;</th>
                    <td width="80" align="right">&nbsp;</th>
					<td width="60" align="right">&nbsp;</th>
					<td width="180" align="right">&nbsp;</th>
					<td width="100" align="right">&nbsp;</th>
					<td width="100" align="right">&nbsp;</th>
					<td width="120" align="right">Total</th>
					<td width="110" align="right" id="value_tot_opening"><? echo number_format($tot_opening,2);  ?></td>
					<td width="100" align="right" id="value_tot_reject_qty"><? echo number_format($tot_reject_qty,2);  ?></td>
					<td width="100" align="right" id="value_tot_closingStock"><? echo number_format($tot_closingStock,2);  ?></td>
					<td align="right">&nbsp;</td>
				</tr>
			</table>
		</div>
		<?
	}
	else if ($type==2)
	{
		?>
		<div>
			<table style="width:1400px" border="1" cellpadding="2" cellspacing="0"  id="caption" rules="all"> 
				<thead>
					<tr class="form_caption" style="border:none;">
						<td colspan="15" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td> 
					</tr>
					<tr class="form_caption" style="border:none;">
						<td colspan="15" align="center" style="border:none; font-size:14px;">
						   <b>Company Name : <? echo $companyArr[$cbo_company_id]; ?></b>                               
						</td>
					</tr>
					<tr class="form_caption" style="border:none;">
						<td colspan="15" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? if($from_date!="" || $to_date!="") echo "From : ".change_date_format($from_date)." To : ".change_date_format($to_date)."" ;?>
						</td>
					</tr>
				</thead>
			</table>
			<table width="1400" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th rowspan="2" width="40">SL</th>
                        <th rowspan="2" width="70">Sample ID</th>
                        <th rowspan="2" width="70">Year</th>
                        <th rowspan="2" width="100">Buyer</th>
                        <th rowspan="2" width="100">Style</th>
                        <th rowspan="2" width="120">Fab. Booking No.</th>
						<th rowspan="2" width="60">Prod.ID</th>
						<th colspan="3">Description</th>
						<th rowspan="2" width="110">Opening Stock</th>
						<th rowspan="2" width="100">Reject Qty.</th>
						<th rowspan="2" width="100">Closing Stock</th>
						<th rowspan="2">Remarks</th>
					</tr>
					<tr>                         
						<th width="180">Product Name</th>
						<th width="100">Lot</th>
						<th width="100">Supplier</th>
					</tr> 
				</thead>
			</table>
			<div style="width:1400px; max-height:350px; overflow-y:scroll" id="scroll_body" > 
				<table width="1382" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
				<?
					/*$sql = "select a.booking_id, a.booking_no, b.prod_id,
					sum(case when b.transaction_date<'".$from_date."' then b.cons_reject_qnty else 0 end) as rej_total_opening,
					sum(case when b.transaction_date between '".$from_date."' and '".$to_date."' then b.cons_reject_qnty else 0 end) as reject_qty
					from inv_receive_master a, inv_transaction b where a.company_id='$cbo_company_id' and a.id=b.mst_id and b.transaction_type=4 and a.entry_form=9 and a.item_category=1 and b.item_category=1 and b.cons_reject_qnty!=0 $prod_id_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_id, a.booking_no, b.prod_id order by b.prod_id";*/
					if($db_type==0) $sample_year_cond="Year(a.insert_date)";
					else if($db_type==2) $sample_year_cond="TO_CHAR(a.insert_date,'YYYY')";
					else $sample_year_cond="";
					
					$sample_array=array();
					$sample_sql=sql_select("select a.booking_no, a.buyer_id, $sample_year_cond as year, b.style_id, b.style_des from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1");
					foreach($sample_sql as $row)
					{
						$sample_array[$row[csf("booking_no")]]["buyer_id"]=$row[csf("buyer_id")];
						$sample_array[$row[csf("booking_no")]]["year"]=$row[csf("year")];
						$sample_array[$row[csf("booking_no")]]["style_id"]=$row[csf("style_id")];
						$sample_array[$row[csf("booking_no")]]["style_des"]=$row[csf("style_des")];
					}
					
					if($txt_product_id=="") $prod_cond=""; else $prod_cond=" and b.prod_id in ($txt_product_id)";
					
					$sql = "select a.booking_id, a.booking_no, b.prod_id,
					sum(case when b.transaction_date<'".$from_date."' then b.cons_reject_qnty else 0 end) as rej_total_opening,
					sum(case when b.transaction_date between '".$from_date."' and '".$to_date."' then b.cons_reject_qnty else 0 end) as reject_qty
					from inv_receive_master a, inv_transaction b
					
					where a.company_id='$cbo_company_id' and a.id=b.mst_id and a.booking_without_order=1
					and b.transaction_type=4 and a.entry_form=9 and a.item_category=1 and b.item_category=1 and b.cons_reject_qnty!=0 $prod_cond 
					and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   $prod_cond_for_in  $supplier_cond
					
					group by a.booking_id, a.booking_no, b.prod_id order by b.prod_id";	
					//echo $sql;
					$i=1;
					$result = sql_select($sql);
					foreach($result as $row)
					{
						if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
						$closingStock=0;
		
						$opening=$row[csf("rej_total_opening")];
						$reject_qty=$row[csf("reject_qty")];
						$closingStock=$opening+$reject_qty;//-$scrap_out_qty;
						$tot_opening+=$opening;
						$tot_reject_qty+=$reject_qty;
						//$tot_scrap_out_qty+=$scrap_out_qty;
						$tot_closingStock+=$closingStock;
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="40"><? echo $i; ?></td>
                                <td width="70" align="center"><p><? echo $sample_array[$row[csf("booking_no")]]["style_id"]; ?></p></td>
                                <td width="70" align="center"><p><? echo $sample_array[$row[csf("booking_no")]]["year"]; ?></p></td>
                                <td width="100" align="center"><p><? echo $buyerArr[$sample_array[$row[csf("booking_no")]]["buyer_id"]]; ?></p></td>
                                <td width="100" align="center"><p><? echo $sample_array[$row[csf("booking_no")]]["style_des"]; ?></p></td>
								<td width="120"><p><? echo $row[csf("booking_no")]; ?></p></td>
								<td width="60" align="center"><p><? echo $row[csf("prod_id")]; ?></p></td>
								<td width="180"><p><? echo $product_array[$row[csf('prod_id')]]['dtls']; ?></p></td>
								<td width="100"><p><? echo $product_array[$row[csf('prod_id')]]['lot']; ?></p></td> 
								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $supplierArr[$product_array[$row[csf('prod_id')]]['supplier_id']]; ?></div></td> 
								<td width="110" align="right"><p><? echo number_format($opening,2); ?></p></td>
								<td width="100" align="right"><p><? echo number_format($reject_qty,2); ?></p></td>
								<td width="100" align="right"><p><? echo number_format($closingStock,2); ?></p></td>
								<td>&nbsp;</td>
							</tr>
						 <? 												
						 $i++; 				
					}
					?>
				</table>
			</div> 
			<table width="1400" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" > 
			   <tr>
					<td width="40" align="right">&nbsp;</th>
                    <td width="70" align="right">&nbsp;</th>
                    <td width="70" align="right">&nbsp;</th>
                    <td width="100" align="right">&nbsp;</th>
                    <td width="100" align="right">&nbsp;</th>
					<td width="120" align="right">Total</th>
					<td width="60" align="right">&nbsp;</th>
					<td width="180" align="right">&nbsp;</th>
					<td width="100" align="right">&nbsp;</th>
					<td width="100" align="right">&nbsp;</th>
					<td width="110" align="right" id="value_tot_opening"><? echo number_format($tot_opening,2);  ?></td>
					<td width="100" align="right" id="value_tot_reject_qty"><? echo number_format($tot_reject_qty,2);  ?></td>
					<td width="100" align="right" id="value_tot_closingStock"><? echo number_format($tot_closingStock,2);  ?></td>
					<td align="right">&nbsp;</td>
				</tr>
			</table>
		</div>
		<?
	}
	else if ($type==3)
	{
		?>
		<div>
			<table style="width:1550px" border="1" cellpadding="2" cellspacing="0"  id="caption" rules="all"> 
				<thead>
					<tr class="form_caption" style="border:none;">
						<td colspan="15" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td> 
					</tr>
					<tr class="form_caption" style="border:none;">
						<td colspan="15" align="center" style="border:none; font-size:14px;">
						   <b>Company Name : <? echo $companyArr[$cbo_company_id]; ?></b>                               
						</td>
					</tr>
					<tr class="form_caption" style="border:none;">
						<td colspan="10" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? if($from_date!="" || $to_date!="") echo "From : ".change_date_format($from_date)." To : ".change_date_format($to_date)."" ;?>
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
					/*$sql = "select a.booking_id, a.booking_no, b.prod_id,
					sum(case when b.transaction_date<'".$from_date."' then b.cons_reject_qnty else 0 end) as rej_total_opening,
					sum(case when b.transaction_date between '".$from_date."' and '".$to_date."' then b.cons_reject_qnty else 0 end) as reject_qty
					from inv_receive_master a, inv_transaction b where a.company_id='$cbo_company_id' and a.id=b.mst_id and b.transaction_type=4 and a.entry_form=9 and a.item_category=1 and b.item_category=1 and b.cons_reject_qnty!=0 $prod_id_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_id, a.booking_no, b.prod_id order by b.prod_id";*/	
					
				$sql_yrecv = "select b.prod_id,min(b.transaction_date) as transaction_date,avg(b.order_rate) as order_rate_usd,avg(b.cons_rate) as cons_rate_tk,sum(b.order_amount) as order_amount_usd,sum(b.cons_amount) as cons_amount_tk
					from inv_receive_master a, inv_transaction b where a.company_id='$cbo_company_id' and a.id=b.mst_id and b.transaction_type=1 and a.item_category=1 and b.item_category=1 $prod_id_cond2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.prod_id order by b.prod_id";
					$result_yRecv = sql_select($sql_yrecv);
					foreach($result_yRecv as $row)
					{
						$yarn_recv_arr[$row[csf('prod_id')]]['usd_rate']=$row[csf('order_rate_usd')];
						$yarn_recv_arr[$row[csf('prod_id')]]['tk_rate']=$row[csf('cons_rate_tk')];
						$yarn_recv_arr[$row[csf('prod_id')]]['order_amount_usd']=$row[csf('order_amount_usd')];
						$yarn_recv_arr[$row[csf('prod_id')]]['cons_amount_tk']=$row[csf('cons_amount_tk')];
						$yarn_recv_arr[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
					}
					
					if($cbo_search_by_zero==2) // without Zero
					{
						$sql = "select * from (select  c.prod_id,
						sum(case when b.transaction_date<'".$from_date."' then c.reject_qty else 0 end) as rej_total_opening,
						sum(case when b.transaction_date between '".$from_date."' and '".$to_date."' then c.reject_qty else 0 end) as reject_qty
						from inv_receive_master a, inv_transaction b, order_wise_pro_details c
						
						where a.company_id='$cbo_company_id' and a.id=b.mst_id and b.id=c.trans_id 
						and c.trans_type=4 and b.transaction_type=4 and a.entry_form=9 and c.entry_form=9 and a.item_category=1 and b.item_category=1 and c.reject_qty!=0 $prod_id_cond 
						and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $search_by_zero_cond $prod_cond_for_in  $supplier_cond
						group by c.prod_id order by c.prod_id) group by prod_id,rej_total_opening,  reject_qty HAVING  reject_qty <>0";	
					}
					else{ //with zeor
						$sql = "select  c.prod_id,
						sum(case when b.transaction_date<'".$from_date."' then c.reject_qty else 0 end) as rej_total_opening,
						sum(case when b.transaction_date between '".$from_date."' and '".$to_date."' then c.reject_qty else 0 end) as reject_qty
						from inv_receive_master a, inv_transaction b, order_wise_pro_details c
						
						where a.company_id='$cbo_company_id' and a.id=b.mst_id and b.id=c.trans_id 
						and c.trans_type=4 and b.transaction_type=4 and a.entry_form=9 and c.entry_form=9 and a.item_category=1 and b.item_category=1 and c.reject_qty!=0 $prod_id_cond 
						and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $prod_cond_for_in  $supplier_cond
						
						group by c.prod_id order by c.prod_id";	
					}
					
					//echo $sql;
					$i=1;$tot_recv_order_amount_tk=$tot_recv_order_amount_usd=0;
					$result = sql_select($sql);
					foreach($result as $row)
					{
						if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
						$closingStock=0;
		
						$opening=$row[csf("rej_total_opening")];
						$reject_qty=$row[csf("reject_qty")];
						$scrap_out_qty=$data_array[$row[csf('prod_id')]]['scrip'];
						$closingStock=$opening+$reject_qty-$scrap_out_qty;
						$avg_tk_rate=$yarn_recv_arr[$row[csf('prod_id')]]['tk_rate'];
						$avg_usd_rate=$yarn_recv_arr[$row[csf('prod_id')]]['usd_rate'];
						
						$recv_order_amount_tk=$closingStock*$avg_tk_rate;//$yarn_recv_arr[$row[csf('prod_id')]]['cons_amount_tk'];
						$recv_order_amount_usd=$closingStock*$avg_usd_rate;//$yarn_recv_arr[$row[csf('prod_id')]]['order_amount_usd'];
						$transaction_date=$yarn_recv_arr[$row[csf('prod_id')]]['transaction_date'];
						 
						 $transactiondate=date("d-m-Y",strtotime($transaction_date));
						// echo  $transaction_date.'dd';
						 $today_date=date("d-m-Y");
						 if($transaction_date!='')
						 {
						 		$original_age=datediff( "d", $transactiondate , $today_date);
						 }
						 else
						 {
								$original_age=''; 
						 }
						 
						$tot_recv_order_amount_tk+=$recv_order_amount_tk;
						$tot_recv_order_amount_usd+=$recv_order_amount_usd;
						
						
						$tot_opening+=$opening;
						$tot_reject_qty+=$reject_qty;
						$tot_scrap_out_qty+=$scrap_out_qty;
						$tot_closingStock+=$closingStock;
						$lotno=$product_array[$row[csf('prod_id')]]['lot'];
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="40"><? echo $i; ?></td>
								<td width="60" align="center"><p><? echo $row[csf("prod_id")]; ?></p></td>
								<td width="180"><p><? echo $product_array[$row[csf('prod_id')]]['dtls']; ?></p></td>
								<td width="100"><p><? echo $product_array[$row[csf('prod_id')]]['lot']; ?></p></td> 
								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $supplierArr[$product_array[$row[csf('prod_id')]]['supplier_id']]; ?></div></td> 
								<td width="110" align="right"><p><? echo number_format($opening,2); ?></p></td>
								<td width="100" align="right"><p> 
                                <a href="#report_details" onClick="openmypage_yarn('<? echo $row[csf('prod_id')];?>','<? echo $cbo_company_id;?>','yarn_reject_qty_popup','Yarn Reject Details','1100px','<? echo $from_date;?>','<? echo $to_date;?>')"><? echo number_format($reject_qty,2); ?></a>
                                </p>
                                </td>
                                <td width="100" align="right"><p>  <a href="#report_details" onClick="openmypage_yarn('<? echo $row[csf('prod_id')];?>','<? echo $cbo_company_id;?>','scrap_qty_popup','Scrap Out Details','700px','<? echo $from_date;?>','<? echo $to_date;?>')"><? echo number_format($scrap_out_qty,2); ?></a><? //echo number_format($scrap_out_qty,2); ?></p></td>
								<td width="100" align="right"><p><? echo number_format($closingStock,2); ?></p></td>
                                
                                <td width="100" align="right"><p><? echo number_format($avg_tk_rate,2); ?></p></td>
                                <td width="100" align="right"><p><? echo number_format($recv_order_amount_tk,2); ?></p></td>
                                <td width="100" align="right"><p><? echo number_format($avg_usd_rate,2); ?></p></td>
                                <td width="100" align="right"><p><? echo number_format($recv_order_amount_usd,2); ?></p></td>
                                <td width="80" align="right" title="Trans Date= <? echo  $transaction_date;?>"><p><? echo $original_age; ?></p></td>
								<td>&nbsp;</td>
							</tr>
						 <? 												
						 $i++; 				
					}
					?>
				</table>
			</div> 
			<table width="1520" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all"> 
			   <tr>
					<td width="40" align="right">&nbsp;</th>
					<td width="60" align="right">&nbsp;</th>
					<td width="180" align="right">&nbsp;</th>
					<td width="100" align="right">&nbsp;</th>
					<td width="100" align="right">Total</th>
					<td width="110" align="right" id="value_tot_opening"><? echo number_format($tot_opening,2);  ?></td>
					<td width="100" align="right" id="value_tot_reject_qty"><? echo number_format($tot_reject_qty,2);  ?></td>
                    <td width="100" align="right" id="value_tot_scrap_out_qty"><? echo number_format($tot_scrap_out_qty,2);  ?></td>
					<td width="100" align="right" id="value_tot_closingStock"><? echo number_format($tot_closingStock,2);  ?></td>
                    
                    <td width="100" align="right" id=""><? //echo number_format($tot_closingStock,2);  ?></td>
                    <td width="100" align="right" id=""><? echo number_format($tot_recv_order_amount_tk,2);  ?></td>
                    <td width="100" align="right" id=""><? //echo number_format($tot_closingStock,2);  ?></td>
                    <td width="100" align="right" id=""><? echo number_format($tot_recv_order_amount_usd,2);  ?></td>
                    <td width="80" align="right" >&nbsp;</td>
					<td align="right">&nbsp;</td>
				</tr>
			</table>
		</div>
		<?
	}
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
    echo "$html**$filename**$type"; 
    exit();
}

if($action=="yarn_reject_qty_popup")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	
	$buyerArr = return_library_array("select id,short_name from lib_buyer where status_active=1 and is_deleted=0","id","short_name"); 
	$company_nameArr = return_library_array("select id,company_name from  lib_company where status_active=1 and is_deleted=0","id","company_name");
	$supplierArr = return_library_array("select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name"); 
	$booking_noArr = return_library_array("select booking_no,buyer_id from  wo_booking_mst where status_active=1 and is_deleted=0","booking_no","buyer_id");
	
	 $sql_po= "select b.id,b.po_number,a.buyer_name from wo_po_details_master a, wo_po_break_down b where  a.job_no=b.job_no_mst and b.status_active=1 and b.is_deleted=0 ";
	$result_po=sql_select($sql_po);
	foreach($result_po as $row)
	{
		$po_numArr[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		$po_numArr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
	}
	
	 $sql_sales= "select a.id,a.sales_booking_no,a.job_no,a.within_group,a.buyer_id from  fabric_sales_order_mst a where  a.status_active=1 and a.is_deleted=0 ";
	$result_sales=sql_select($sql_sales);
	foreach($result_sales as $row)
	{
		$sales_numArr[$row[csf('sales_booking_no')]]['within_group']=$row[csf('within_group')];
		$sales_numArr[$row[csf('sales_booking_no')]]['job_no']=$row[csf('job_no')];
		$sales_numArr[$row[csf('sales_booking_no')]]['buyer_id']=$row[csf('buyer_id')];
	}
	//print_r($po_numArr);
?>
<div>
<table width="1080" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
<caption><strong>Yarn Reject Detail</strong> </caption>
<thead>
    <tr>
        <th  width="30">SL</th>
        <th  width="100">Sys. ID</th>
        <th width="70">Rtn. Date</th>
        <th width="120">Rtn. Party</th>
        <th width="120">Buyer</th>
        <th width="120">PO/FSO No</th>
        <th width="120">Booking No</th>
        <th  width="100">Rtn. Qty</th>
         <th  width="100">Reject. Qty</th>
        <th  width="">Remark</th>
   </tr>
 </thead>
	 <tbody  id="table_body_popup">
     <?
     	$sql = "select a.recv_number,a.knitting_source,a.knitting_company,a.receive_date,a.supplier_id,a.receive_basis,b.remarks,a.booking_no, a.buyer_id,
		sum(case when b.transaction_date between '".$from_date."' and '".$to_date."' then c.quantity else 0 end) as quantity,
		sum(case when b.transaction_date between '".$from_date."' and '".$to_date."' then c.reject_qty else 0 end) as reject_qty,
		c.po_breakdown_id
					from inv_receive_master a, inv_transaction b, order_wise_pro_details c
					where a.company_id='$company_id' and a.id=b.mst_id and b.id=c.trans_id 
					and c.trans_type=4 and b.transaction_type=4 and a.entry_form=9 and c.entry_form=9 and a.item_category=1 and b.item_category=1 and c.reject_qty!=0 
					and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.prod_id=$prod_id 
					group by a.recv_number,a.receive_date,a.supplier_id,b.remarks,a.booking_no,a.knitting_source,a.knitting_company, a.buyer_id,c.po_breakdown_id,a.receive_basis order by a.recv_number";	
					//echo $sql;
					$k=1;$tot_quantity_qty=$tot_reject_qty=0;
					$result = sql_select($sql);
					foreach($result as $row)
					{
						if($k%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
						
						$ret_qty=$row[csf('quantity')];$reject_qty=$row[csf('reject_qty')];
						if($row[csf("knitting_source")]==1) //Sales
						{
							$party_name=$company_nameArr[$row[csf("knitting_company")]];
						}
						else
						{
							$party_name=$supplierArr[$row[csf("knitting_company")]];
						}
						
						if($row[csf("receive_basis")]==4) //Sales
						{
							$po_number=$po_numArr[$row[csf('po_breakdown_id')]]['po_number'];
							$buyer_name=$buyerArr[$po_numArr[$row[csf('po_breakdown_id')]]['buyer_name']];
							
							$booking_no=return_field_value("booking_no as booking_no","wo_booking_mst a,wo_booking_dtls b "," a.booking_no=b.booking_no and b.po_break_down_id =".$row[csf('po_breakdown_id')]." and b.is_deleted=0 and b.status_active=1","booking_no");
							
						}
						else if($row[csf("receive_basis")]==3) //Requisition
						{
							 $knit_id=return_field_value("knit_id as knit_id","ppl_yarn_requisition_entry","requisition_no ='".$row[csf('booking_no')]."' and is_deleted=0 and status_active=1","knit_id");
							
							$booking_no=return_field_value("booking_no","ppl_planning_entry_plan_dtls","dtls_id =".$knit_id." and is_deleted=0 and status_active=1 and is_sales=1","booking_no");

							//$sales_numArr[$row[csf('sales_booking_no')]]['job_no'];
							$sales_order_fso_no=$sales_numArr[$booking_no]['job_no'];
							//echo $sales_order_fso_no.'dddd';
							if($sales_order_fso_no!='')
							{
								$within_group=$sales_numArr[$booking_no]['within_group'];
								$po_number=$sales_order_fso_no;
								//echo $sales_numArr[$booking_no]['buyer_id'].'ssd';
								//echo $within_group.'dsss';
								if($within_group==1) //Yes
								{
									//$booking_no=return_field_value("booking_no as booking_no","wo_booking_mst a,wo_booking_dtls b "," a.booking_no=b.booking_no and b.po_break_down_id =".$row[csf('po_breakdown_id')]." and b.is_deleted=0 and b.status_active=1","booking_no");
									//$booking_noArr;
									//echo $booking_noArr[$booking_no];
									$buyer_name=$buyerArr[$booking_noArr[$booking_no]];
								}
								else
								{
									$buyer_name=$buyerArr[$sales_numArr[$booking_no]['buyer_id']];
								}
							}
							else
							{
								$po_number=$po_numArr[$row[csf('po_breakdown_id')]]['po_number'];
								$buyer_name=$buyerArr[$po_numArr[$row[csf('po_breakdown_id')]]['buyer_name']];
							}
						}
						else
						{
						
							$po_number=$po_numArr[$row[csf('po_breakdown_id')]]['po_number'];
							$buyer_name=$buyerArr[$po_numArr[$row[csf('po_breakdown_id')]]['buyer_name']];
							$booking_no=$row[csf("booking_no")];
						}
		if($row[csf('reject_qty')]>0)
		{
        ?>
         <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
            <td width="30"><? echo $k; ?></td>
            <td width="100" align="center" title="<? echo $issue_basis[$row[csf("receive_basis")]]; ?>"><p><? echo $row[csf("recv_number")]; ?></p></td>
            <td width="70"><p><? echo change_date_format($row[csf("receive_date")]); ?></p></td>
            <td width="120"><p><? echo $party_name; ?></p></td> 
            <td width="120"><div style="word-wrap:break-word; width:120px"><? echo $buyer_name; ?></div></td> 
            <td width="120" align="right"><p><? echo $po_number; ?></p></td>
            <td width="120" align="right"><p> <? echo $booking_no; ?> </p></td>
            <td width="100" align="right"><p> <? echo number_format($ret_qty,2); ?> </p></td>
            <td width="100" align="right"><p> <? echo number_format($reject_qty,2); ?> </p></td>
            <td width=""><p> <? echo $row[csf("remarks")];?></p></td>
            </tr>
            <?
				$tot_quantity_qty+=$ret_qty;
				$tot_reject_qty+=$reject_qty;
				$k++;
				}
		}
			?>
            <tfoot>
            <tr>
            <th colspan="7"> Total </th>
            <th> <? echo number_format($tot_quantity_qty,2);?> </th>
             <th> <? echo number_format($tot_reject_qty,2);?> </th>
            <th>  </th>
            </tr>
            </tfoot>
	 </tbody>
 </table>
   <script>   setFilterGrid("table_body_popup",-1);</script>
</div>

<?
	
}
if($action=="scrap_qty_popup")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	
	$buyerArr = return_library_array("select id,short_name from lib_buyer where status_active=1 and is_deleted=0","id","short_name"); 
	$company_nameArr = return_library_array("select id,company_name from  lib_company where status_active=1 and is_deleted=0","id","company_name");
	$supplierArr = return_library_array("select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name"); 
	
?>
<div>
<table width="650" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
<caption><strong>Scrap Out Detail</strong> </caption>
<thead>
    <tr>
        <th  width="30">SL</th>
        <th  width="120">Sys. ID</th>
        <th width="70">Selling Date</th>
        <th width="100">Sold Qty.</th>
        <th width="80">Selling Rate</th>
        <th width="100">Sales Amount</th>
        <th  width="">Remark</th>
   </tr>
 </thead>
	 <tbody  id="table_body_popup">
     <?
     				$sql_scrap="select a.remarks,a.sys_challan_no,a.selling_date,b.prod_id,
					 sum(b.sales_qty) as scrap_qty,sum(b.sales_amount) as sales_amount,
					 sum(case when a.selling_date between '".$from_date."' and '".$to_date."' then b.sales_qty else 0 end) as scrap_qty,
					 sum(case when a.selling_date between '".$from_date."' and '".$to_date."' then b.sales_amount else 0 end) as sales_amount,
					 avg(b.sales_rate) as sales_rate
					  from  inv_scrap_sales_mst a, inv_scrap_sales_dtls b where a.id=b.mst_id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.prod_id=$prod_id group by b.prod_id, a.remarks,a.sys_challan_no,a.selling_date";
	$sql_scrap_result=sql_select($sql_scrap);
					$k=1;$tot_scrap_qty=$tot_sold_qty=$tot_sales_amount=0;
					//$result = sql_select($sql);
					foreach($sql_scrap_result as $row)
					{
						if($k%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
						
						$scrap_qty=$row[csf('scrap_qty')];
						//$sold_qty=$row[csf('sold_qty')];
						$sales_rate=$row[csf('sales_rate')];
						$sales_amount=$row[csf('sales_amount')];
						
						$tot_sold_qty+=$sold_qty;
						$tot_scrap_qty+=$scrap_qty;
						$tot_sales_amount+=$sales_amount;
						
			if($row[csf('scrap_qty')]>0)
			{
        ?>
         <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
            <td width="30"><? echo $k; ?></td>
            <td width="120" align="center"><p><? echo $row[csf("sys_challan_no")]; ?></p></td>
            <td width="70"><p><? echo change_date_format($row[csf("selling_date")]); ?></p></td>
            <td width="100" align="right"><p><? echo number_format($scrap_qty,2); ?></p></td> 
            <td width="80" align="right"><div style="word-wrap:break-word; width:80px"><? echo number_format($sales_rate,2); ?></div></td> 
            <td width="100" align="right"><p><? echo number_format($sales_amount,2); ?></p></td>
          
            <td width=""><p> <? echo $row[csf("remarks")];?></p></td>
            </tr>
            <?
				$k++;
				}
			}
			?>
            <tfoot>
            <tr>
            <th colspan="3"> Total </th>
            <th align="right"> <? echo number_format($tot_scrap_qty,2);?> </th>
            <th> <? //echo number_format($tot_scrap_qty,2);?> </th>
            <th align="right"> <? echo number_format($tot_sales_amount,2);?> </th>
            <th>  </th>
            </tr>
            </tfoot>
	 </tbody>
 </table>
   <script>   setFilterGrid("table_body_popup",-1);</script>
</div>

<?
	
}
?>