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
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, '');
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
	
	$sql="SELECT id, item_category_id, product_name_details from product_details_master where item_category_id=$data[1] and status_active=1 and is_deleted=0"; 
	$arr=array(0=>$item_category);
	echo  create_list_view("list_view", "Item Category,Fabric Description,Product ID", "120,250,60","490","300",0, $sql, "js_set_value", "id,product_name_details", "", 1, "item_category_id,0,0", $arr, "item_category_id,product_name_details,id", "","setFilterGrid('list_view',-1);","0,0,0","",1) ;
	//echo create_list_view("tbl_list_search", "Party Name", "380","380","270",0, $sql , "js_set_value", "id,party_name", "", 1, "0", $arr , "party_name", "",'setFilterGrid("tbl_list_search",-1);','0','',1) ;

	exit();
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
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
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( ddd );
		} 

    </script>
</head>
<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:580px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th>
                    	<input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
                        <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    	<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                    </th> 					
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'reject_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>

		</fieldset>
	</form>
</div>
</body>           
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit(); 
}

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	//echo $month_id;
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
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
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	
	if($db_type==0)
	{
		if($year_id!=0) $year_search_cond=" and year(insert_date)=$year_id"; else $year_search_cond="";
		$year_cond= "year(insert_date)as year";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_search_cond=" and TO_CHAR(insert_date,'YYYY')=$year_id"; else $year_search_cond="";
		$year_cond= "TO_CHAR(insert_date,'YYYY') as year";
	}
		
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_cond from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_search_cond order by id DESC";
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","620","270",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;
   exit(); 
} 

if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	if ($cbo_company_id==0) $company_id =""; else $company_id =" and a.company_id='$cbo_company_id'";
	if ($txt_product_id=="") 
	{
		$item_account=""; 
		$prod_cond="";
	}
	else 
	{
		$item_account=" and a.prod_id in ($txt_product_id)";
		$prod_cond=" and a.id in ($txt_product_id)";
	}
	
	if ($cbo_store_id==0){ $store_id="";}else{$store_id=" and a.store_id='$cbo_store_id'";}
	
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
	if ($cbo_search_by_zero==1){ $search_by_zero_cond="";}else{$search_by_zero_cond=" and b.reject_fabric_receive <>0 ";}
	
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
	$determinaArr = return_library_array("select id,construction from lib_yarn_count_determina_mst where status_active=1 and is_deleted=0","id","construction");
	
	$data_array=array();
		
	$sql_scrap="select b.prod_id, sum(case when a.selling_date<'".$from_date."' then b.sales_qty else 0 end) as sales_total_opening, sum(case when a.selling_date between '".$from_date."' and '".$to_date."' then b.sales_qty else 0 end) as sales_qty from  inv_scrap_sales_mst a, inv_scrap_sales_dtls b where a.id=b.mst_id and a.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.prod_id";
	$sql_scrap_result=sql_select($sql_scrap);
	foreach($sql_scrap_result as $row)
	{
		$data_array[$row[csf('prod_id')]]['opening']=$row[csf('sales_total_opening')];
		$data_array[$row[csf('prod_id')]]['scrip']=$row[csf('sales_qty')];
	}
	
	
	//echo $rpt_type;die;
		
	ob_start();	
	if($rpt_type==1)
	{
		?>
		<div>
			<table style="width:1100px" border="1" cellpadding="2" cellspacing="0"  id="caption" rules="all"> 
				<thead>
					<tr class="form_caption" style="border:none;">
						<td colspan="11" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td> 
					</tr>
					<tr class="form_caption" style="border:none;">
						<td colspan="11" align="center" style="border:none; font-size:14px;">
						   <b>Company Name : <? echo $companyArr[$cbo_company_id]; ?></b>                               
						</td>
					</tr>
					<tr class="form_caption" style="border:none;">
						<td colspan="11" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? if($from_date!="" || $to_date!="") echo "From : ".change_date_format($from_date)." To : ".change_date_format($to_date)."" ;?>
						</td>
					</tr>
				</thead>
			</table>
			<table width="1120" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th rowspan="2" width="40">SL</th>
						<th rowspan="2" width="60">Prod.ID</th>
						<th colspan="4">Description</th>
						<th rowspan="2" width="110">Opening Stock</th>
						<th rowspan="2" width="100">Reject Qty.</th>
						<th rowspan="2" width="100">Scrap Out Qty</th>
						<th rowspan="2" width="100">Closing Stock</th>
						<th rowspan="2">Remarks</th>
					</tr>
					<tr>                         
						<th width="120">Construction</th>
						<th width="180">Composition</th>
						<th width="70">GSM</th>
						<th width="100">Dia/Width</th>
					</tr> 
				</thead>
			</table>
			<div style="width:1120px; max-height:350px; overflow-y:scroll" id="scroll_body" > 
				<table width="1100" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
				<?
					$composition_arr=array(); $i=1;
					$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
					$deterdata_array=sql_select($sql_deter);
					if(count($deterdata_array)>0)
					{
						foreach( $deterdata_array as $row )
						{
							if(array_key_exists($row[csf('id')],$composition_arr))
							{
								$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
							}
							else
							{
								$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
							}
						}
					}
					if($cbo_search_by_zero==2) // without Zero
					{
						$sql = "select *from (select a.id, a.detarmination_id, b.gsm, b.width, 
						sum(case when c.transaction_type=1 and c.transaction_date<'".$from_date."' then b.reject_fabric_receive else 0 end) as rej_total_opening,
						sum(case when c.transaction_type=1 and c.transaction_date between '".$from_date."' and '".$to_date."' then b.reject_fabric_receive else 0 end) as rej_qty 
						from product_details_master a, pro_grey_prod_entry_dtls b, inv_transaction c 
						where c.company_id='$cbo_company_id' and a.id=b.prod_id and b.trans_id=c.id and c.transaction_type=1 and b.reject_fabric_receive!=0 and c.cons_reject_qnty!=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category_id='$cbo_category_id' and c.item_category='$cbo_category_id' $search_by_zero_cond $prod_cond  group by a.id, a.detarmination_id, b.gsm, b.width order by a.id ASC) group by id, detarmination_id, gsm, width,  rej_total_opening,   rej_qty
	       					HAVING  rej_qty <>0";
					}
					else // with Zero
					{
						$sql = "select a.id, a.detarmination_id, b.gsm, b.width, 
					sum(case when c.transaction_type=1 and c.transaction_date<'".$from_date."' then b.reject_fabric_receive else 0 end) as rej_total_opening,
					sum(case when c.transaction_type=1 and c.transaction_date between '".$from_date."' and '".$to_date."' then b.reject_fabric_receive else 0 end) as rej_qty 
					from product_details_master a, pro_grey_prod_entry_dtls b, inv_transaction c 
					where c.company_id='$cbo_company_id' and a.id=b.prod_id and b.trans_id=c.id and c.transaction_type=1 and b.reject_fabric_receive!=0 and c.cons_reject_qnty!=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category_id='$cbo_category_id' and c.item_category='$cbo_category_id' $search_by_zero_cond $prod_cond  group by a.id, a.detarmination_id, b.gsm, b.width order by a.id ASC";
					}
						
					//$trans; die;
					$result = sql_select($sql);
					foreach($result as $row)
					{
						if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
		
						$opening=$row[csf("rej_total_opening")]-$data_array[$row[csf('id')]]['opening'];
						$reject_qty=$row[csf("rej_qty")];
						$scrap_out_qty=$data_array[$row[csf('id')]]['scrip'];
						
						$closingStock=$opening+$reject_qty-$scrap_out_qty;
						$tot_opening+=$opening;
						$tot_reject_qty+=$reject_qty;
						$tot_scrap_out_qty+=$scrap_out_qty;
						$tot_closingStock+=$closingStock;
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="40"><? echo $i; ?></td>	
								<td width="60" align="center"><p><? echo $row[csf("id")]; ?></p></td>
								<td width="120"><div style="word-wrap:break-word; width:120px"><? echo $determinaArr[$row[csf("detarmination_id")]]; ?></div></td>                                 
								<td width="180"><div style="word-wrap:break-word; width:180px"><? echo $composition_arr[$row[csf('detarmination_id')]]; ?></div></td>
								<td width="70"><p><? echo $row[csf("gsm")]; ?></p></td> 
								<td width="100"><p><? echo $row[csf("width")]; ?></p></td> 
								<td width="110" align="right"><p><? echo number_format($opening,2); ?></p></td>
								<td width="100" align="right"><p><? echo number_format($reject_qty,2); ?></p></td>
								<td width="100" align="right"><p><? echo number_format($scrap_out_qty,2); ?></p></td>
								<td width="100" align="right"><p><? echo number_format($closingStock,2); ?></p></td>
								<td>&nbsp;</td>
							</tr>
						<? 												
						 $i++; 				
						}
					?>
				</table>
			</div> 
			<table width="1120" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" > 
			   <tr>
					<td width="40" align="right">&nbsp;</th>
					<td width="60" align="right">&nbsp;</th>  
					<td width="120" align="right">&nbsp;</th>
					<td width="180" align="right">&nbsp;</th>
					<td width="70" align="right">&nbsp;</th>
					<td width="100" align="right">Total</th>
					<td width="110" align="right" id="value_tot_opening"><? echo number_format($tot_opening,2);  ?></td>
					<td width="100" align="right" id="value_tot_reject_qty"><? echo number_format($tot_reject_qty,2);  ?></td>
					<td width="100" align="right" id="value_tot_scrap_out_qty"><? echo number_format($tot_scrap_out_qty,2);  ?></td>
					<td width="100" align="right" id="value_tot_closingStock"><? echo number_format($tot_closingStock,2);  ?></td>
					<td align="right">&nbsp;</td>
				</tr>
			</table>
		</div>
		<?
	}
	else if ($rpt_type==2)
	{
	?>
	<div>
		<table style="width:1430px" border="1" cellpadding="2" cellspacing="0"  id="caption" rules="all"> 
			<thead>
				<tr class="form_caption" style="border:none;">
					<td colspan="11" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td> 
				</tr>
				<tr class="form_caption" style="border:none;">
					<td colspan="11" align="center" style="border:none; font-size:14px;">
					   <b>Company Name : <? echo $companyArr[$cbo_company_id]; ?></b>                               
					</td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td colspan="11" align="center" style="border:none;font-size:12px; font-weight:bold">
						<? if($from_date!="" || $to_date!="") echo "From : ".change_date_format($from_date)." To : ".change_date_format($to_date)."" ;?>
					</td>
				</tr>
			</thead>
		</table>
		<table width="1430" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<tr>
                    <th rowspan="2" width="40">SL</th>
                    <th rowspan="2" width="60">Job</th>
                    <th rowspan="2" width="60">Year</th>
                    <th rowspan="2" width="80">Buyer</th>
                    <th rowspan="2" width="80">Style</th>
                    <th rowspan="2" width="60">Prod.ID</th>
                    <th colspan="3">Description</th>
                    <th rowspan="2" width="120">Booking/Req.</th>
                    <th rowspan="2" width="110">Opening Stock</th>
                    <th rowspan="2" width="100">Reject Qty.</th>
                    <th rowspan="2" width="100">Scrap Out Qty</th>
                    <th rowspan="2" width="100">Closing Stock</th>
                    <th rowspan="2">Remarks</th>
                </tr>
                <tr>                         
                    <th width="180">Product Name</th>
                    <th width="100">Lot</th>
                    <th width="100">Dia Width</th>
                </tr> 
			</thead>
		</table>
		<div style="width:1420px; max-height:350px; overflow-y:scroll" id="scroll_body" > 
			<table width="1400" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<?
			$brandArr = return_library_array("select id,brand_name from lib_brand where status_active=1 and is_deleted=0","id","brand_name");
			
			if($db_type==0) $year_cond="YEAR(e.insert_date)"; 
			else if($db_type==2) $year_cond="to_char(e.insert_date,'YYYY')";
			
			$sql = "select a.id, a.product_name_details, b.gsm, b.width, c.booking_no, e.job_no, e.job_no_prefix_num, $year_cond as year, e.buyer_name, e.style_ref_no,
			sum(case when c.entry_form=22 and c.receive_date<'".$from_date."' then b.reject_fabric_receive else 0 end) as rej_total_opening,
			sum(case when c.entry_form=22 and c.receive_date between '".$from_date."' and '".$to_date."' then b.reject_fabric_receive else 0 end) as rej_qty 
			from product_details_master a, pro_grey_prod_entry_dtls b, inv_receive_master c, order_wise_pro_details d, wo_po_details_master e, wo_po_break_down f
			where c.company_id='$cbo_company_id' and a.id=b.prod_id and b.mst_id=c.id and c.id=d.dtls_id and d.po_breakdown_id=f.id and e.job_no=f.job_no_mst and c.receive_basis!=9 and c.entry_form in (2,22) and d.entry_form in (2,22) and b.reject_fabric_receive<>0 and b.trans_id!=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.item_category_id='$cbo_category_id' and c.item_category='$cbo_category_id' $prod_cond 
			group by a.id, a.product_name_details, b.gsm, b.width, c.booking_no, e.job_no, e.job_no_prefix_num, e.insert_date, e.buyer_name, e.style_ref_no order by a.id ASC";
				
				/*$sql = "(select a.id, a.product_name_details, b.gsm, b.width, c.booking_no, d.job_no, 
				sum(case when c.entry_form=22 and c.receive_date<'".$from_date."' then b.reject_fabric_receive else 0 end) as rej_total_opening,
				sum(case when c.entry_form=22 and c.receive_date between '".$from_date."' and '".$to_date."' then b.reject_fabric_receive else 0 end) as rej_qty 
				from product_details_master a, pro_grey_prod_entry_dtls b, inv_receive_master c, wo_booking_mst d
				where c.company_id='$cbo_company_id' and a.id=b.prod_id and b.mst_id=c.id and c.booking_no=d.booking_no and c.receive_basis=2 and c.entry_form=22 and b.reject_fabric_receive<>0 and b.trans_id!=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category_id='$cbo_category_id' and c.item_category='$cbo_category_id' $prod_cond 
				group by a.id, a.product_name_details, b.gsm, b.width, c.booking_no, d.job_no)
				union all
				(select a.id, a.product_name_details, b.gsm, b.width, c.booking_no, d.job_no, 
				sum(case when c.entry_form=2 and c.receive_date<'".$from_date."' then b.reject_fabric_receive else 0 end) as rej_total_opening,
				sum(case when c.entry_form=2 and c.receive_date between '".$from_date."' and '".$to_date."' then b.reject_fabric_receive else 0 end) as rej_qty 
				from product_details_master a, pro_grey_prod_entry_dtls b, inv_receive_master c, wo_booking_mst d
				where c.company_id='$cbo_company_id' and a.id=b.prod_id and b.mst_id=c.id and c.booking_no=d.booking_no and c.receive_basis=1 and c.entry_form=2 and b.reject_fabric_receive<>0 and b.trans_id!=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category_id='$cbo_category_id' and c.item_category='$cbo_category_id' $prod_cond 
				group by a.id, a.product_name_details, b.gsm, b.width, c.booking_no, d.job_no
				)
				union all
				(select a.id, a.product_name_details, b.gsm, b.width, c.booking_no, d.job_no, 
					sum(case when c.entry_form=2 and c.receive_date<'".$from_date."' then b.reject_fabric_receive else 0 end) as rej_total_opening,
					sum(case when c.entry_form=2 and c.receive_date between '".$from_date."' and '".$to_date."' then b.reject_fabric_receive else 0 end) as rej_qty 
					from product_details_master a, pro_grey_prod_entry_dtls b, inv_receive_master c, wo_booking_mst d, ppl_planning_info_entry_mst e, ppl_planning_info_entry_dtls f  
					where c.company_id='$cbo_company_id' and a.id=b.prod_id and b.mst_id=c.id and e.booking_no=d.booking_no and e.id=f.mst_id and c.booking_id=f.id
					
					
					and c.receive_basis=2 and c.entry_form=2 and b.reject_fabric_receive<>0 and b.trans_id!=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category_id='$cbo_category_id' and c.item_category='$cbo_category_id' $prod_cond 
					group by a.id, a.product_name_details, b.gsm, b.width, c.booking_no, d.job_no
				)
				
				 order by id ASC";*/
				//echo $sql;	
				$result = sql_select($sql); $i=1;
				foreach($result as $row)
				{
					if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
	
					$opening=$row[csf("rej_total_opening")]-$data_array[$row[csf('id')]]['opening'];
					$reject_qty=$row[csf("rej_qty")];
					$scrap_out_qty=$data_array[$row[csf('id')]]['scrip'];
					
					$closingStock=$opening+$reject_qty-$scrap_out_qty;
					$tot_opening+=$opening;
					$tot_reject_qty+=$reject_qty;
					$tot_scrap_out_qty+=$scrap_out_qty;
					$tot_closingStock+=$closingStock;
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>	
                            <td width="60" align="center"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
                            <td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                            <td width="80"><div style="word-wrap:break-word; width:80px"><? echo $brandArr[$row[csf('buyer_name')]]; ?></div></td>
                            <td width="80"><div style="word-wrap:break-word; width:80px"><? echo $row[csf('style_ref_no')]; ?></div></td>
							<td width="60" align="center"><p><? echo $row[csf("id")]; ?></p></td>
							<td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
							<td width="100"><p><? echo $row[csf("gsm")]; ?></p></td> 
							<td width="100"><p><? echo $row[csf("width")]; ?></p></td>
                            <td width="120"><p><? echo $row[csf("booking_no")]; ?></p></td>
							<td width="110" align="right"><p><? echo number_format($opening,2); ?></p></td>
							<td width="100" align="right"><p><? echo number_format($reject_qty,2); ?></p></td>
							<td width="100" align="right"><p><? echo number_format($scrap_out_qty,2); ?></p></td>
							<td width="100" align="right"><p><? echo number_format($closingStock,2); ?></p></td>
							<td>&nbsp;</td>
						</tr>
					<? 												
					 $i++; 				
					}
				?>
			</table>
		</div> 
		<table width="1430" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" > 
		   <tr>
				<td width="40" align="right">&nbsp;</th>
				<td width="60" align="right">&nbsp;</th>
                <td width="60" align="right">&nbsp;</th> 
                <td width="80" align="right">&nbsp;</th> 
                <td width="80" align="right">&nbsp;</th> 
                <td width="60" align="right">&nbsp;</th> 
				<td width="180" align="right">&nbsp;</th>
				<td width="100" align="right">&nbsp;</th>
				<td width="100" align="right">&nbsp;</th>
                <td width="120" align="right">Total</th>
				<td width="110" align="right" id="value_tot_opening"><? echo number_format($tot_opening,2);  ?></td>
				<td width="100" align="right" id="value_tot_reject_qty"><? echo number_format($tot_reject_qty,2);  ?></td>
				<td width="100" align="right" id="value_tot_scrap_out_qty"><? echo number_format($tot_scrap_out_qty,2);  ?></td>
				<td width="100" align="right" id="value_tot_closingStock"><? echo number_format($tot_closingStock,2);  ?></td>
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
    echo "$html**$filename**$rpt_type"; 
    exit();
}
?>