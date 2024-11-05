<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_library=array();
$sql=sql_select("select id, company_short_name, company_name from lib_company");
foreach($sql as $row)
{
	$company_library[$row[csf('id')]]['short']=$row[csf('company_short_name')];
	$company_library[$row[csf('id')]]['full']=$row[csf('company_name')];
}

$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count"  );
$brand_details=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name"  );
//--------------------------------------------------------------------------------------------------------------------

if($action=="orderNo_search_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
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
				name += selected_name[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_po_id').val( id );
			$('#hide_po_no').val( name );
		}
		
		function hidden_field_reset()
		{
			$('#hide_po_id').val('');
			$('#hide_po_no').val( '' );
			selected_id = new Array();
			selected_name = new Array();
		}
	
    </script>

</head>

<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:780px;">
            <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th>Search</th>
                    <th>Shipment Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                    <input type="hidden" name="hide_po_id" id="hide_po_id" value="" />
                    <input type="hidden" name="hide_po_no" id="hide_po_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array('1'=>"Po No",2=>"Job No",3=>"Style Ref");
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", 2,'',0 );
						?>
                        </td>     
                        <td align="center">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_orderNo_search_list_view', 'search_div', 'daily_yarn_delivery_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);hidden_field_reset();');" style="width:100px;" />
                    	</td>
                    </tr>
                    <tr>
                        <td colspan="5" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:10px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_orderNo_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	if($data[1]==0) $buyer_name="%%"; else $buyer_name=$data[1];
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	$start_date =$data[4];
	$end_date =$data[5];

	if($search_by==1) $search_field="b.po_number";
	else if($search_by==2) $search_field="a.job_no";
	else if($search_by==3) $search_field="a.style_ref_no";
		
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";	}
		else
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date), '','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
		}
	}
	else $date_cond="";
	
	$arr=array(0=>$buyer_arr,5=>$unit_of_measurement);
		
$sql = "select a.job_no, a.style_ref_no, a.buyer_name, a.order_uom, b.id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id and buyer_name like '$buyer_name' and $search_field like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond"; 
		
	echo create_list_view("tbl_list_search", "Buyer Name,Job No,Style Ref.,PO NO,PO Qnty, UOM, Shipment Date", "110,110,110,110,110,60","770","210",0, $sql , "js_set_value", "id,po_number", "", 1, "buyer_name,0,0,0,0,order_uom,0", $arr , "buyer_name,job_no,style_ref_no,po_number,po_qnty_in_pcs,order_uom,pub_shipment_date", "",'','0,0,0,0,1,0,3','',1) ;
	
   exit(); 
} 

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
 	if($template==1)
	{
		$order_id = str_replace("'","",$hide_order_id);
		$company_name=$cbo_company_name;

		$po_array=array();
		
		$costing_sql=sql_select("select a.job_no, a.style_ref_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_name and b.id in($order_id)");
		foreach($costing_sql as $row)
		{
			$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')]; 
			$po_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')]; 
			$po_array[$row[csf('id')]]['buyer_name']=$buyer_arr[$row[csf('buyer_name')]]; 
		}
		
		$product_details_arr=array();
		$pro_sql=sql_select("select id, yarn_count_id, lot, color, brand from product_details_master where company_id=$company_name and item_category_id=1");
		foreach($pro_sql as $row)
		{
			$product_details_arr[$row[csf('id')]]['count']=$yarn_count_details[$row[csf('yarn_count_id')]];
			$product_details_arr[$row[csf('id')]]['lot']=$row[csf('lot')]; 
			$product_details_arr[$row[csf('id')]]['color']=$color_library[$row[csf('color')]];
			$product_details_arr[$row[csf('id')]]['brand']=$brand_details[$row[csf('brand')]];
		}
		
		$reqsn_details_arr=array();	
		$sql=sql_select("select prod_id, requisition_no, sum(yarn_demand_qnty) as demand_qnty from ppl_yarn_demand_reqsn_dtls group by prod_id, requisition_no");
		foreach($sql as $row)
		{
			$reqsn_details_arr[$row[csf('prod_id')]][$row[csf('requisition_no')]]=$row[csf('demand_qnty')];
		}
		
		ob_start();
		?>
        <fieldset style="width:1720px;">
        	<table cellpadding="0" cellspacing="0" width="100%">
            	<tr>
                    <td align="center" width="100%" colspan="17" style="font-size:14px"><strong><? echo $company_library[str_replace("'","",$company_name)]['full']; ?></strong></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="17" style="font-size:12px"><strong>Daily Yarn Delivery Status</strong></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="17" style="font-size:12px"><strong>Knitting Department</strong></td>
                </tr>
            </table>	
            <table id="table_header_1" cellspacing="0" cellpadding="0" border="1" rules="all" width="1700" class="rpt_table" >
                <thead>
                    <th width="40">SL</th>
                    <th width="80">Buyer</th>
                    <th width="130">Order No</th>
                    <th width="110"><? echo $company_library[str_replace("'","",$company_name)]['short']; ?></th>
                    <th width="90">Reqsn. No.</th>
                    <th width="80">Yarn Count</th>
                    <th width="100">Yarn Brand</th>
                    <th width="100">Lot No</th>
                    <th width="100">Color</th>
                    <th width="110">Reqsn. Qty</th>
                    <th width="110">Demand Qty</th>
                    <th width="110">Delivery Qty</th>
                    <th width="110">Balance Qty</th>
                    <th width="100">CTN/ Bag</th>
                    <th width="90">Cone</th>
                    <th width="100">Weight</th>
                    <th>Remarks</th>
                </thead>
            </table>
			<div style="width:1718px; overflow-y:scroll; max-height:330px;" id="scroll_body">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1700" class="rpt_table" id="table_body">
                    <tbody>
                        <? 											
							$i=1; $tot_reqsn_qnty=0; $tot_demand_qnty=0; $tot_delivery_qnty=0; $tot_balance=0;
							if($db_type==0)
							{
								$sql="select c.id, group_concat(distinct(a.po_id)) as po_id, c.requisition_no as reqs_no, c.prod_id, c.no_of_cone, c.yarn_qnty as reqsn_qnty from ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c where b.id=a.dtls_id and b.id=c.knit_id and a.po_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.id order by c.requisition_no";
							}
							if($db_type==2)
							{
						 		$sql="select c.id, LISTAGG(a.po_id, ',') WITHIN GROUP (ORDER BY a.po_id) as po_id, c.requisition_no as reqs_no, c.prod_id, c.no_of_cone, c.yarn_qnty as reqsn_qnty from ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c where b.id=a.dtls_id and b.id=c.knit_id and a.po_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.id,c.requisition_no, c.prod_id, c.no_of_cone, c.yarn_qnty order by c.requisition_no";
							}
							//echo $sql;
                            $nameArray=sql_select( $sql );
                            foreach ($nameArray as $row)
                            {
                                if ($i%2==0)  
                                    $bgcolor="#E9F3FF";
                                else
                                    $bgcolor="#FFFFFF";
								
								$yarn_iss_data=sql_select("select sum(b.cons_quantity) as delivery_qnty, sum(b.no_of_bags) as no_of_bags from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.issue_basis=3 and a.item_category=1 and a.entry_form=3 and b.receive_basis=3 and b.transaction_type=2 and b.item_category=1 and b.requisition_no=".$row[csf('reqs_no')]." and b.prod_id=".$row[csf('prod_id')]." and b.status_active=1 and b.is_deleted=0");
								
								$delivery_qnty=$yarn_iss_data[0][csf('delivery_qnty')];
								$no_of_bags=$yarn_iss_data[0][csf('no_of_bags')];
							
								$demand_qnty=$reqsn_details_arr[$row[csf('prod_id')]][$row[csf('reqs_no')]];
								$balance_qnty=$row[csf('reqsn_qnty')]-$delivery_qnty;
								$po_id=array_unique(explode(",",$row[csf('po_id')]));
								$po_no=''; $buyer='';
								
								foreach($po_id as $val)
								{
									if($po_no=='') $po_no=$po_array[$val]['no']; else $po_no.=",".$po_array[$val]['no'];
									if($buyer=='') $buyer=$po_array[$val]['buyer_name'];
								}
								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
									<td width="40"><? echo $i; ?></td>
									<td width="80"><p><? echo  $buyer; ?></p></td>
									<td width="130"><p><? echo $po_no; ?></p></td>
									<td width="110"><p><? echo $row[csf('po_id')]; ?></p></td>
									<td width="90" align="center"><? echo $row[csf('reqs_no')]; ?></td>
									<td width="80"><p><? echo $product_details_arr[$row[csf('prod_id')]]['count']; ?></p></td>
                                    <td width="100"><? echo $product_details_arr[$row[csf('prod_id')]]['brand']; ?></td>
                                    <td width="100"><p><? echo $product_details_arr[$row[csf('prod_id')]]['lot']; ?></p></td>
                                    <td width="100"><p><? echo $product_details_arr[$row[csf('prod_id')]]['color']; ?></p></td>
                                    <td align="right" width="110"><? echo number_format($row[csf('reqsn_qnty')],2); ?></td>
                                    <td align="right" width="110"><? echo number_format($demand_qnty,2); ?></td>
                                    <td align="right" width="110"><? echo number_format($delivery_qnty,2); ?></td>
                                    <td align="right" width="110"><? echo number_format($balance_qnty,2); ?></td>
                                    <td align="right" width="100"><? echo number_format($no_of_bags); ?></td>
                                    <td align="right" width="90">&nbsp;</td>
                                    <td align="right" width="100">&nbsp;</td>
									<td><p>&nbsp;</p></td>
								</tr>
								<?
								
								$tot_reqsn_qnty+=$row[csf('reqsn_qnty')];
								$tot_demand_qnty+=$demand_qnty;
								$tot_delivery_qnty+=$delivery_qnty;
								$tot_balance+=$balance_qnty;
								
								$i++;
							}
                        	?>
                    </tbody>
                </table> 
                <table class="rpt_table" width="1700" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tfoot>
                        <th width="40">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="130">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100" align="right">Total</th>
                        <th width="110" align="right" id="value_tot_reqsn_qnty"><? echo number_format($tot_reqsn_qnty,2,'.',''); ?></th>
                        <th width="110" align="right" id="value_tot_demand_qnty"><? echo number_format($tot_demand_qnty,2,'.',''); ?></th>
                        <th width="110" align="right" id="value_tot_delivery_qnty"><? echo number_format($tot_delivery_qnty,2,'.',''); ?></th>
                        <th width="110" align="right" id="value_tot_balance"><? echo number_format($tot_balance,2,'.',''); ?></th>
                        <th width="100">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
			</div>
      	</fieldset>      
	<?
	}
	
	disconnect($con);
	exit();
}
?>