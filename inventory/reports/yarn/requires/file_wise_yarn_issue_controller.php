<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');

if($action=="load_drop_down_file_year")
{
	$sql="select a.lc_year from com_export_lc a where a.status_active=1 and a.is_deleted=0 and a.beneficiary_name=$data group by a.lc_year";
 	echo create_drop_down( "cbo_file_year", 150, $sql,"lc_year,lc_year", 1, "---- Year ----", 0, "" );
	exit();
}

if($action=="file_search")
{
	
	echo load_html_head_contents("Export LC Form", "../../../../", 1, 1,'','1','');
	extract($_REQUEST);
	$sql="select a.internal_file_no, a.lc_year, 1 as type from com_export_lc a where a.status_active=1 and a.is_deleted=0 and a.beneficiary_name=$company_id and a.lc_year='$file_year' group by a.internal_file_no, a.lc_year
	union 
	select a.internal_file_no, a.sc_year as lc_year, 2 as type from com_sales_contract a where a.status_active=1 and a.is_deleted=0 and a.beneficiary_name=$company_id and a.sc_year='$file_year' group by a.internal_file_no, a.sc_year
	order by internal_file_no";
	
	
	
	?>
     
	<script>
		
	 var selected_id = new Array, selected_name = new Array();	
	 function check_all_data() {
			var tbl_row_count = document.getElementById( 'table_body' ).rows.length; 

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
		
		function js_set_value( str ) {
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
			var file=$("#td_"+str).html();
			if( jQuery.inArray(file, selected_id ) == -1 ) {
				selected_id.push(file);
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == file) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			
			$('#txt_selected_id').val( id );
		}
    </script>

</head>
<body>
    <div align="center" style="width:520px;">
        <form name="searchexportlcfrm" id="searchexportlcfrm">
            <fieldset style="width:500px; margin-left:3px">
            	<input type="hidden" id="txt_selected_id" >
                <table cellpadding="0" cellspacing="0" width="100%" class="rpt_table"  border="1" rules="all">
                	<thead>
                        <th width="50">Sl</th>
                        <th width="80">Year</th>
                        <th>File No</th>
                    </thead>
                </table>
                <div style="width:500px; max-height:290px; overflow:auto;" >
                	<table cellpadding="0" cellspacing="0" width="480" class="rpt_table" id="table_body" border="1" rules="all">
                        <tbody>
                        <?
						$sql_result=sql_select($sql);$i=1;
						foreach($sql_result as $row)
						{	if(!in_array($row[csf("internal_file_no")],$temp_file_arr)){
							$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
							?>
                        	<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $i; ?>');" style="cursor:pointer;" id="tr_<? echo $i;?>">
                                <td width="50" align="center"><? echo $i; ?></td>
                                <td width="80"><? echo $row[csf("lc_year")]; ?></td>
                                <td id="td_<? echo $i;?>"><? echo $row[csf("internal_file_no")]; ?></td>
                            </tr>
                            <?
							$i++;
							$temp_file_arr[]=$row[csf("internal_file_no")];
							}
						}
						?>
                        </tbody>
                    </table>
                    <script>setFilterGrid('table_body',-1);</script>
                </div>   
            </fieldset>
            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
            <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
        </form>
    </div>
</body>           
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit(); 

}



if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_file_year=str_replace("'","",$cbo_file_year);
	//$txt_internal_file_no=str_replace("'","",$txt_internal_file_no);
	$txt_internal_file_no_arr=explode(",",str_replace("'","",$txt_internal_file_no));
	$txt_internal_file_no="";
	foreach($txt_internal_file_no_arr as $file_no)
	{
		$txt_internal_file_no.="'".$file_no."',";
	}
	$txt_internal_file_no=chop($txt_internal_file_no,",");
	if($txt_internal_file_no){$file_con=" and g.internal_file_no in($txt_internal_file_no)";}else{$file_con="";}

	$sql="(SELECT
		a.id as issue_id,
		a.booking_id as pi_id,
		a.booking_no,
		a.remarks as issue_remarks,
		a.issue_number,
		a.issue_purpose,
		a.issue_date,
		a.supplier_id,
		a.knit_dye_company,
		a.challan_no,
		a.knit_dye_source as knit_src,
		d.quantity as issue_qty,
		d.returnable_qnty,
		b.order_rate as issue_rate,
		b.order_amount as issue_amount,
		b.store_id,
		c.product_name_details,
		c.lot,
		c.color,
		c.brand,
		c.yarn_type,
		c.yarn_count_id,
		d.po_breakdown_id,
		e.po_number,
		e.grouping,
		f.job_no,
		f.style_ref_no,
		f.buyer_name,
        g.internal_file_no
	FROM	
		inv_issue_master a,
		inv_transaction b,
		product_details_master c,
		order_wise_pro_details d,
		wo_po_break_down e, 
		wo_po_details_master f,
        com_export_lc g, 
        com_export_lc_order_info h 
	WHERE
		a.id=b.mst_id and
		b.prod_id=c.id and
		b.id=d.trans_id and
		d.po_breakdown_id=e.id and
		e.job_no_mst=f.job_no and
		a.entry_form=3 and
        e.id=h.wo_po_break_down_id and
        g.id=h.com_export_lc_id and
        g.lc_year='$cbo_file_year' and
		
		b.transaction_type=2 and 
		a.company_id=$cbo_company_name and
		a.company_id=1 and
		a.status_active=1 and
		d.entry_form=3 and 
		d.trans_type=2 and 
		a.is_deleted=0 and  
		b.is_deleted=0 and  
		c.is_deleted=0 and  
		d.is_deleted=0 and  
		e.is_deleted=0 and  
		e.status_active!=3 and
		f.is_deleted=0 and
		g.status_active=1 and 
		g.is_deleted=0
		$file_con
		)
    
  union all
    
   (SELECT
		a.id as issue_id,
		a.booking_id as pi_id,
		a.booking_no,
		a.remarks as issue_remarks,
		a.issue_number,
		a.issue_purpose,
		a.issue_date,
		a.supplier_id,
		a.knit_dye_company,
		a.challan_no,
		a.knit_dye_source as knit_src,
		d.quantity as issue_qty,
		d.returnable_qnty,
		b.order_rate as issue_rate,
		b.order_amount as issue_amount,
		b.store_id,
		c.product_name_details,
		c.lot,
		c.color,
		c.brand,
		c.yarn_type,
		c.yarn_count_id,
		d.po_breakdown_id,
		e.po_number,
		e.grouping,
		f.job_no,
		f.style_ref_no,
		f.buyer_name,
        g.internal_file_no
	FROM	
		inv_issue_master a,
		inv_transaction b,
		product_details_master c,
		order_wise_pro_details d,
		wo_po_break_down e, 
		wo_po_details_master f,
        com_sales_contract g, 
        com_sales_contract_order_info h 
	WHERE
		a.id=b.mst_id and
		b.prod_id=c.id and
		b.id=d.trans_id and
		d.po_breakdown_id=e.id and
		e.job_no_mst=f.job_no and
		a.entry_form=3 and
        e.id=h.wo_po_break_down_id and
        g.id=h.com_sales_contract_id and
        g.sc_year='$cbo_file_year' and

		b.transaction_type=2 and 
		a.company_id=$cbo_company_name and
		a.company_id=1 and
		a.status_active=1 and
		d.entry_form=3 and 
		d.trans_type=2 and 
		a.is_deleted=0 and  
		b.is_deleted=0 and  
		c.is_deleted=0 and  
		d.is_deleted=0 and  
		e.is_deleted=0 and 
		e.status_active!=3 and
		f.is_deleted=0 and
		g.status_active=1 and 
		g.is_deleted=0
		$file_con
		) order by internal_file_no,grouping";

		
		
	$result=sql_select($sql);
	//echo $sql;
	foreach($result as $row)
	{
		$key=$row[csf('issue_id')].'**'.$row[csf('po_breakdown_id')].'**'.$row[csf('lot')];
		$fk=$row[csf('internal_file_no')].$row[csf('grouping')];
		
		$issue_data_arr[$row[csf('knit_src')]][$fk][$key]['job_no']=$row[csf('job_no')];
		$issue_data_arr[$row[csf('knit_src')]][$fk][$key]['issue_date']=$row[csf('issue_date')];
		$issue_data_arr[$row[csf('knit_src')]][$fk][$key]['issue_number']=$row[csf('issue_number')];
		$issue_data_arr[$row[csf('knit_src')]][$fk][$key]['lot']=$row[csf('lot')];
		$issue_data_arr[$row[csf('knit_src')]][$fk][$key]['issue_qty']+=$row[csf('issue_qty')];
		$issue_data_arr[$row[csf('knit_src')]][$fk][$key]['returnable_qnty']+=$row[csf('returnable_qnty')];
		$issue_data_arr[$row[csf('knit_src')]][$fk][$key]['color']=$row[csf('color')];
		$issue_data_arr[$row[csf('knit_src')]][$fk][$key]['yarn_type']=$row[csf('yarn_type')];
		$issue_data_arr[$row[csf('knit_src')]][$fk][$key]['brand']=$row[csf('brand')];
		$issue_data_arr[$row[csf('knit_src')]][$fk][$key]['issue_purpose']=$row[csf('issue_purpose')];
		$issue_data_arr[$row[csf('knit_src')]][$fk][$key]['booking_no']=$row[csf('booking_no')];
		$issue_data_arr[$row[csf('knit_src')]][$fk][$key]['knit_dye_company']=$row[csf('knit_dye_company')];
		$issue_data_arr[$row[csf('knit_src')]][$fk][$key]['store_id']=$row[csf('store_id')];
		$issue_data_arr[$row[csf('knit_src')]][$fk][$key]['yarn_count_id']=$row[csf('yarn_count_id')];
		$issue_data_arr[$row[csf('knit_src')]][$fk][$key]['challan_no']=$row[csf('challan_no')];
		$issue_data_arr[$row[csf('knit_src')]][$fk][$key]['po_number']=$row[csf('po_number')];
		$issue_data_arr[$row[csf('knit_src')]][$fk][$key]['style_ref_no']=$row[csf('style_ref_no')];
		$issue_data_arr[$row[csf('knit_src')]][$fk][$key]['grouping']=$row[csf('grouping')];
		$issue_data_arr[$row[csf('knit_src')]][$fk][$key]['buyer_name']=$row[csf('buyer_name')];
		$issue_data_arr[$row[csf('knit_src')]][$fk][$key]['internal_file_no']=$row[csf('internal_file_no')];
		$issue_data_arr[$row[csf('knit_src')]][$fk][$key]['po_breakdown_id']=$row[csf('po_breakdown_id')];
		
		$sub_tot[$row[csf('knit_src')]][$fk]['issue_qty']+=$row[csf('issue_qty')];
		$sub_tot[$row[csf('knit_src')]][$fk]['returnable_qnty']+=$row[csf('returnable_qnty')];
		
		$grand_tot[$row[csf('knit_src')]]['issue_qty']+=$row[csf('issue_qty')];
		$grand_tot[$row[csf('knit_src')]]['returnable_qnty']+=$row[csf('returnable_qnty')];
		
		
		
		$po_id_arr[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];	
		
		
	}
	unset($result);
	
	
	
	
	$po_no_list_arr=array_chunk($po_id_arr,999);
	$p=1;
	foreach($po_no_list_arr as $po_id)
	{
		$po_id_string=implode(',',$po_id);
		if($p==1) $po_con2 =" and  ( b.po_break_down_id in($po_id_string)"; else  $po_con2 .=" or b.po_break_down_id in($po_id_string)";
		
		$p++;
	}
	$po_con2 .=")";
	
	
	$req_data_array=sql_select("select b.po_break_down_id, sum(b.grey_fab_qnty) as qnty, sum(b.amount*a.exchange_rate) as val from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and company_id=$cbo_company_name $po_con2 group by b.po_break_down_id");
	foreach($req_data_array as $row)
	{
		$req_qty_array[$row[csf('po_break_down_id')]]=$row[csf('qnty')];
		$req_val_array[$row[csf('po_break_down_id')]]=$row[csf('val')];
	}
	unset($req_data_array);

	ob_start();
	?>
    <!--<fieldset style="width:1120px">-->
    	<div style="width:100%; margin-left:10px;" align="left">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr><th colspan="22"><h2>Daily Yarn Issue Report</h2></th></tr>
                <tr><th colspan="22"><? echo $company_arr[$cbo_company_name];?></th></tr>
            </table>            
            <table width="2150" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                	<tr>
                        <th width="35">SL</th>
                        <th width="80">File No</th>
                        <th width="120">Ref. No</th>
                        <th width="80">Job No</th>
                        <th width="100">Buyer</th>
                        <th width="100">Style No</th>
                        <th width="100">Order No</th>
                        <th width="100">Issue No</th>
                        <th width="100">Issue Date</th>
                        <th width="100">Challan/Prog. No</th>
                        <th width="60">Count</th>  
                        <th width="100">Yarn Brand</th> 
                        <th width="100">Type</th> 
                        <th width="100">Color</th> 
                        <th width="100">Lot No</th> 
                        <th width="100">Issue Qty</th> 
                        <th width="100">Returnable Qty.</th>
                        <th width="80">Issue Purpose</th> 
                        <th width="100">Booking/ Reqn. No</th> 
                        <th width="100">Booking/ Reqn. Qty</th>
                        <th width="120">Issue To</th> 
                        <th>Store</th> 
                    </tr>
                </thead>
            </table>
            <div style="max-height:350px; overflow-y:auto; width:2170px;">
            <table width="2150" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="tbl_body">            	
			<? 	
			$i=1;
			// Inside.....................................
            if(count($issue_data_arr[1])>0)echo "<tr bgcolor='#A6C3EB'><td colspan='22'>&nbsp; <strong>Inside</strong></td></tr>";
			foreach($issue_data_arr[1] as $file_key=>$fileArr)
            {						
				foreach($fileArr as $row){
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF"; 
				?>
				<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
					<td width="35"><? echo $i; ?></td>
					<td width="80"><p><? echo $row['internal_file_no']; ?></p></td>
					<td width="120"><p><? echo $row['grouping']; ?></p></td>
					<td width="80" align="center"><? echo $row['job_no']; ?></td>
					<td width="100"><p><? echo $buyer_arr[$row['buyer_name']]; ?></p></td>
					<td width="100"><p><? echo $row['style_ref_no']; ?></p></td>
					<td width="100"><p>&nbsp;<? echo $row['po_number']; ?></p></td>
					<td width="100" ><? echo $row['issue_number']; ?></td>
					<td width="100"><? echo change_date_format($row['issue_date']); ?></td>
					<td width="100"><? echo $row['challan_no']; ?></td>
					<td width="60"><? echo $count_arr[$row['yarn_count_id']];?></td>
					<td width="100"><? echo $brand_arr[$row['brand']];?></td>
					<td width="100"><? echo $yarn_type[$row['yarn_type']];?></td>
					<td width="100"><? echo $color_arr[$row['color']];?></td>
					<td width="100"><? echo $row['lot'];?></td>
					<td width="100" align="right"><? echo $row['issue_qty'];?>&nbsp;</td>
					<td width="100" align="right"><? echo $row['returnable_qnty'];?>&nbsp;</td>
					<td width="80"><? echo $yarn_issue_purpose[$row['issue_purpose']];?></td>
					<td width="100" align="center"><? echo $row['booking_no'];?></td>
					<td width="100" align="right"><? echo $req_qty_array[$row['po_breakdown_id']];?>&nbsp;</td>
					<td width="120"><? echo $company_arr[$row['knit_dye_company']];?></td>
					<td><p><? echo $store_arr[$row['store_id']]; ?></p></td>
				</tr>
				<?
				$i++;
				}
			
				echo "<tr bgcolor='#EEE'>
					<td colspan='15' align='right'><strong> Sub Total:</strong>&nbsp;</td>
					<td align='right'>&nbsp;<strong>{$sub_tot[1][$file_key]['issue_qty']}</strong></td>
					<td align='right'>&nbsp;<strong>{$sub_tot[1][$file_key]['returnable_qnty']}</strong></td>
					<td colspan='5'>&nbsp;</td>
				</tr>";
			
			
			}
			echo "<tr bgcolor='#CCC'>
				<td colspan='15' align='right'><strong>Inside Total:</strong>&nbsp;</td>
				<td align='right'>&nbsp;<strong>{$grand_tot[1]['issue_qty']}</strong></td>
				<td align='right'>&nbsp;<strong>{$grand_tot[1]['returnable_qnty']}</strong></td>
				<td colspan='5'>&nbsp;</td>
			</tr>";
	

			// Outside....................................
            if(count($issue_data_arr[2])>0){
				echo "<tr bgcolor='#A6C3EB'><td colspan='22'>&nbsp; <strong>Outside</strong></td></tr>";
			}
			foreach($issue_data_arr[2] as $file_key=>$fileArr)
            {						
				foreach($fileArr as $row){
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF"; 
				?>
				<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
					<td width="35"><? echo $i; ?></td>
					<td width="80"><p><? echo $row['internal_file_no']; ?></p></td>
					<td width="120"><p><? echo $row['grouping']; ?></p></td>
					<td width="80" align="center"><? echo $row['job_no']; ?></td>
					<td width="100"><p><? echo $buyer_arr[$row['buyer_name']]; ?></p></td>
					<td width="100"><p><? echo $row['style_ref_no']; ?></p></td>
					<td width="100"><p>&nbsp;<? echo $row['po_number']; ?></p></td>
					<td width="100" ><? echo $row['issue_number']; ?></td>
					<td width="100"><? echo $row['issue_date']; ?></td>
					<td width="100"><? echo $row['challan_no']; ?></td>
					<td width="60"><? echo $count_arr[$row['yarn_count_id']];?></td>
					<td width="100"><? echo $brand_arr[$row['brand']];?></td>
					<td width="100"><? echo $yarn_type[$row['yarn_type']];?></td>
					<td width="100"><? echo $color_arr[$row['color']];?></td>
					<td width="100"><? echo $row['lot'];?></td>
					<td width="100" align="right"><? echo $row['issue_qty'];?>&nbsp;</td>
					<td width="100" align="right"><? echo $row['returnable_qnty'];?>&nbsp;</td>
					<td width="80"><? echo $yarn_issue_purpose[$row['issue_purpose']];?></td>
					<td width="100" align="center"><? echo $row['booking_no'];?></td>
					<td width="100" align="right"><? echo $req_qty_array[$row['po_breakdown_id']];?>&nbsp;</td>
					<td width="120"><? echo $company_arr[$row['knit_dye_company']];?></td>
					<td><p><? echo $store_arr[$row['store_id']]; ?></p></td>
				</tr>
				<?
				$i++;
				}
			
				echo "<tr bgcolor='#EEE'>
					<td colspan='15' align='right'><strong> Sub Total:</strong>&nbsp;</td>
					<td align='right'>&nbsp;<strong>{$sub_tot[2][$file_key]['issue_qty']}</strong></td>
					<td align='right'>&nbsp;<strong>{$sub_tot[2][$file_key]['returnable_qnty']}</strong></td>
					<td colspan='5'>&nbsp;</td>
				</tr>";
			
			}
            if(count($issue_data_arr[2])>0){
			echo "<tr bgcolor='#CCC'>
				<td colspan='15' align='right'><strong>Outside Total:</strong>&nbsp;</td>
				<td align='right'>&nbsp;<strong>{$grand_tot[2]['issue_qty']}</strong></td>
				<td align='right'>&nbsp;<strong>{$grand_tot[2]['returnable_qnty']}</strong></td>
				<td colspan='5'>&nbsp;</td>
			</tr>";
			}
	
            ?>
            </table>
            </div>
            <table width="2150" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">            	
                <tfoot>
                    <th width="35"></th>
                    <th width="80"></th>
                    <th width="120"></th>
                    <th width="80"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="60"></th>  
                    <th width="100"></th> 
                    <th width="100"></th> 
                    <th width="100"></th> 
                    <th width="100"><strong>Grand Total:</strong></th> 
                    <th width="100" align="right"><? echo $grand_tot[1]['issue_qty']+$grand_tot[2]['issue_qty'];?></th> 
                    <th width="100" align="right"><? echo $grand_tot[1]['returnable_qnty']+$grand_tot[2]['returnable_qnty'];?></th>
                    <th width="80"></th> 
                    <th width="100"></th> 
                    <th width="100"></th>
                    <th width="120"></th> 
                    <th>&nbsp;</th> 
                </tfoot>
            </table>

        	 <?
				echo signature_table(3, str_replace("'","",$cbo_company_name), "1100px");
			 ?>
        </div>      
    <!--</fieldset>-->      
	<?
    foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}
?>
