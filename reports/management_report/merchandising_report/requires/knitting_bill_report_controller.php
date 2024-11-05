<?php
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------

$user_name = $_SESSION['logic_erp']["user_id"];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group buy.id, buy.buyer_name  order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

if($action=="order_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../../", 1, 1,'','','');
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
		
		function js_set_value( str ) 
		{
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
			
			$('#hide_order_id').val( id );
			$('#hide_order_no').val( name );
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
                    <th id="search_by_td_up" width="170">Please Enter Order No</th>
                    <th>Shipment Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                    <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
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
                       		$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                        </td>	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_order_no_search_list_view', 'search_div', 'knitting_bill_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    	</td>
                    </tr>
                    <tr>
                        <td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
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

if($action=="create_order_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$data[1]";
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==1) $search_field="b.po_number"; 
	else if($search_by==2) $search_field="a.style_ref_no"; 	
	else $search_field="a.job_no";
		
	$start_date =$data[4];
	$end_date =$data[5];	
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date),"yyyy-mm-dd")."' and '".change_date_format(trim($end_date),"yyyy-mm-dd")."'";
		}
		else
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
		}
	}
	else $date_cond="";
	
	$company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$arr=array (0=>$company_short_arr,1=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql= "select b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond order by b.id, b.pub_shipment_date";
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "70,70,50,70,150,180","760","220",0, $sql , "js_set_value", "id,po_number","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date","",'','0,0,0,0,0,0,3','',1) ;
   exit(); 
}

if($action=="report_generate")
{ 
	//echo "su..re"; die;
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$costing_per_arr=return_library_array( "select job_no, costing_per_id from wo_pre_cost_dtls where status_active=1 and is_deleted=0", "job_no", "costing_per_id");
	$company_name=str_replace("'","",$cbo_company_name);
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	if(trim($txt_job_no)!="") $job_no=trim($txt_job_no); else $job_no="%%";
	
	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		else $year_cond="";
	}
	else $year_cond="";
	
	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$date_cond=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
	}
	
	$po_id_cond="";
	if(trim(str_replace("'","",$txt_order_no))!="")
	{
		if(str_replace("'","",$hide_order_id)!="") $po_id_cond=" and b.id in(".str_replace("'","",$hide_order_id).")";
		else $po_id_cond=" and b.po_number like '".trim(str_replace("'","",$txt_order_no))."%'";
	}
	
	if(trim(str_replace("'","",$txt_inter_ref))!="") $inter_refCond="and b.grouping=$txt_inter_ref"; else $inter_refCond="";
	
	$shipping_status_cond='';
	if(str_replace("'","",$shipping_status)!=0) $shipping_status_cond=" and b.shiping_status=$shipping_status";
	
	$exchange_rate = 76;
	if( str_replace( "'", "", $txt_exchange_rate ) != '' )
	{
		$exchange_rate = str_replace( "'", "", $txt_exchange_rate );
	}
		 
	$po_ids_array=array();
	$ex_factory_arr=return_library_array( "select po_break_down_id, sum(ex_factory_qnty) as qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "qnty");	
		
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
		
	$sql="select a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id, b.po_number, b.pub_shipment_date, b.grouping, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.job_no_prefix_num like '$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0 $date_cond $buyer_id_cond $po_id_cond $shipping_status_cond $year_cond $inter_refCond order by b.pub_shipment_date, a.job_no_prefix_num, b.id";
	
	//echo $sql;
	
	//$sql="select b.id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.job_no_prefix_num like '$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $buyer_id_cond $po_id_cond $shipping_status_cond $year_cond order by b.pub_shipment_date, a.job_no_prefix_num, b.id";	
	
	//echo $sql; die;
	$result=sql_select($sql);
	ob_start();

	foreach($result as $row)
	{
		$po_ids_array[]=$row[csf('id')];
	}
	$po_id_arr=array_chunk(array_unique($po_ids_array),999);
	
	$sql_subcon_bill="select b.order_id, sum(b.amount) AS knit_bill, sum(b.delivery_qty) as qty from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_source=1 and a.process_id=2 ";
	$p=1;
	if(!empty($po_id_arr))
	{
		foreach($po_id_arr as $po_ids)
		{
			if($p==1) $sql_subcon_bill .=" and (b.order_id in(".implode(',',$po_ids).")"; else $sql_subcon_bill .=" or b.order_id in(".implode(',',$po_ids).")";
			$p++;
		}
		$sql_subcon_bill .=" )  group by b.order_id";
	}
	else $sql_subcon_bill .="  group by b.order_id";

	$subconInBillDataArray=sql_select($sql_subcon_bill);
	foreach($subconInBillDataArray as $subRow)
	{
		$subconCostArray[$subRow[csf('order_id')]]['amnt']=$subRow[csf('knit_bill')];
		$subconCostArray[$subRow[csf('order_id')]]['qty']=$subRow[csf('qty')];
	}
	
	$sql_subcon_out_bill="select b.order_id, sum(b.amount) AS knit_bill, sum(b.receive_qty) as qty from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.process_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	
	$p=1;
	if(!empty($po_id_arr))
	{
		foreach($po_id_arr as $po_ids)
		{
			if($p==1) $sql_subcon_out_bill .=" and (b.order_id in(".implode(',',$po_ids).")"; else $sql_subcon_out_bill .=" or b.order_id in(".implode(',',$po_ids).")";
			$p++;
		}
		$sql_subcon_out_bill .=" )   group by b.order_id";
	}
	else $sql_subcon_out_bill .="   group by b.order_id";
	
	$subconOutBillDataArray=sql_select($sql_subcon_out_bill);
	foreach($subconOutBillDataArray as $subRow)
	{
		$subconCostArray[$subRow[csf('order_id')]]['amnt']+=$subRow[csf('knit_bill')];
		$subconCostArray[$subRow[csf('order_id')]]['qty']+=$subRow[csf('qty')];
	}
	
	$sql_booking="select b.po_break_down_id, sum(b.grey_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
	$p=1;
	if(!empty($po_id_arr))
	{
		foreach($po_id_arr as $po_ids)
		{
			if($p==1) $sql_booking .=" and (b.po_break_down_id in(".implode(',',$po_ids).")"; else $sql_booking .=" or b.po_break_down_id in(".implode(',',$po_ids).")";
			$p++;
		}
		$sql_booking .=" )   group by b.po_break_down_id";
	}
	else $sql_booking .="  group by b.po_break_down_id";
	
	$bookingArray=return_library_array( $sql_booking, "po_break_down_id", "qnty");
	
	$sql_gery_prod="select po_breakdown_id, sum(quantity) as qnty from order_wise_pro_details where entry_form=2 and status_active=1 and is_deleted=0 ";
	
	$p=1;
	if(!empty($po_id_arr))
	{
		foreach($po_id_arr as $po_ids)
		{
			if($p==1) $sql_gery_prod .=" and (po_breakdown_id in(".implode(',',$po_ids).")"; else $sql_gery_prod .=" or po_breakdown_id in(".implode(',',$po_ids).")";
			$p++;
		}
		$sql_gery_prod .=" ) group by po_breakdown_id";
	}
	else $sql_gery_prod .="  group by po_breakdown_id";
	
	$greyProdArray=return_library_array( $sql_gery_prod, "po_breakdown_id", "qnty");
	
	$knitCostArray=return_library_array( "select job_no, sum(amount) AS knit_charge from wo_pre_cost_fab_conv_cost_dtls where cons_process=1 and status_active=1 and is_deleted=0 group by job_no", "job_no", "knit_charge");
	
	?>
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="main_body" style="width:1260px;">
        <fieldset style="width:1263px;">
            <table width="1260">
                <tr class="form_caption">
                    <td align="center"><strong>Knitting Bill Report</strong></td>
                </tr>
                <tr class="form_caption">
                    <td align="center"><strong><? echo $company_arr[$company_name];?></strong>
                    <br>
                    <strong>
                    <? echo change_date_format(str_replace("'","",$txt_date_from)) .' To '. change_date_format(str_replace("'","",$txt_date_to)); ?>
                    </strong>
                    </td>
                </tr>
            </table>
        	<table class="rpt_table" width="1260" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="30">SL</th>
                    <th width="60">Buyer</th>
                    <th width="80">PO No</th>
                    <th width="50">Year</th>
                    <th width="60">Job No</th>
                    <th width="70">Internal Ref.</th>
                    <th width="80">Style Name</th>
                    <th width="110">Gmts Item</th>
                    <th width="90">Order Qty</th>
                    <th width="80">Booking Qty</th>
                    <th width="80">Grey Prod.</th>
                    <th width="90">Knitting Cost [As per Budget]</th>
                    <th width="80">Fabric Bill Qty</th>
                    <th width="80">Unbilled Qnty</th>
                    <th width="100">Bill Amount [Tk]</th>
                    <th>Bill Amount (USD)</th>
                </thead>
            </table>	
            <div style="width:1260px; max-height:290px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="1240" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					$i=1; $tot_po_qnty=0; $tot_booking_qnty=0; $tot_greyProd_qnty=0; $tot_knitCost=0; $tot_knitbill=0; $tot_knitQty=0; 
					$tot_unbilled=0; $tot_knitbill_tk=0;
					foreach($result as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$gmts_item='';
						$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
						}
						
						$dzn_qnty=0;
						$costing_per_id=$costing_per_arr[$row[csf('job_no')]];
						if($costing_per_id==1) $dzn_qnty=12;
						else if($costing_per_id==3) $dzn_qnty=12*2;
						else if($costing_per_id==4) $dzn_qnty=12*3;
						else if($costing_per_id==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;
						
						$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
						$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
						$tot_po_qnty+=$order_qnty_in_pcs;
						$bookingQty=$bookingArray[$row[csf('id')]];
						$tot_booking_qnty+=$bookingQty;
						$greyProdQty=$greyProdArray[$row[csf('id')]];
						$tot_greyProd_qnty+=$greyProdQty;
						$knitCost=($order_qnty_in_pcs/$dzn_qnty)*$knitCostArray[$row[csf('job_no')]];
						$tot_knitCost+=$knitCost;
						$knitQty=$subconCostArray[$row[csf('id')]]['qty'];
						$knitbill=$subconCostArray[$row[csf('id')]]['amnt']/$exchange_rate;
						$tot_knitQty+=$knitQty;
						$tot_knitbill+=$knitbill;
						
						$td_color_bill_qty='';
						if($bookingQty<$knitQty) $td_color_bill_qty="red";
						$td_color_bill_amt='';
						if($knitCost<$knitbill) $td_color_bill_amt="red";

						$unbilled=$greyProdQty-$knitQty;
						$tot_unbilled+=$unbilled;

						$knitbill_tk=$subconCostArray[$row[csf('id')]]['amnt'];
						$tot_knitbill_tk+=$knitbill_tk;
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="60" style="word-break:break-all;"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                            <td width="80" style="word-break:break-all;"><? echo $row[csf('po_number')]; ?></td>
                            <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                            <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                            <td width="70" style="word-break:break-all;"><? echo $row[csf('grouping')]; ?></td>
                            <td width="80" style="word-break:break-all;"><? echo $row[csf('style_ref_no')]; ?></td>
                            <td width="110" style="word-break:break-all;"><? echo $gmts_item; ?></td>
                            <td width="90" align="right"><? echo $order_qnty_in_pcs; ?></td>
							<td width="80" align="right"><? echo number_format($bookingQty,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($greyProdQty,2,'.',''); ?></td>
                            <td width="90" align="right"><? echo number_format($knitCost,2,'.',''); ?></td>
							<td width="80" align="right" bgcolor="<? echo $td_color_bill_qty; ?>"><? echo number_format($knitQty,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($unbilled,2,'.',''); ?></td>

							<td width="100" align="right"><? echo number_format($knitbill_tk,2,'.',''); ?></td>
							<td align="right" bgcolor="<? echo $td_color_bill_amt; ?>"><a href="##" onClick="openmypage_bill('<? echo $row[csf('id')]."_".$exchange_rate; ?>','knitting_bill','Knitting bill Details')"><? echo number_format($knitbill,2,'.',''); ?></a></td>
						</tr>
					<?
					$i++;
					}
					?>
                	<tfoot>
                        <th colspan="8">Total</th>
                        <th><? echo $tot_po_qnty; ?></th>
                        <th><? echo number_format($tot_booking_qnty,2,'.',''); ?></th>
                        <th><? echo number_format($tot_greyProd_qnty,2,'.',''); ?></th>
                        <th><? echo number_format($tot_knitCost,2,'.',''); ?></th>
                        <th><? echo number_format($tot_knitQty,2,'.',''); ?></th>
                        <th><? echo number_format($tot_unbilled,2,'.',''); ?></th>
                        <th><? echo number_format($tot_knitbill_tk,2,'.',''); ?></th>
                        <th><? echo number_format($tot_knitbill,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
	<?
	foreach (glob("../../../../ext_resource/tmp_report/$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$html=ob_get_contents();
	$name=time();
	$filename="../../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	$filename="../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	echo "$total_data****$filename";
	exit();
}

if($action=="knitting_bill")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	
	$inboundChallanCond="";
	$outboundChallanCond="";
	$inboundRecId="";
	$outboundRecId="";
	
	if($db_type==0)
	{
		$inboundChallanCond="group_concat(b.challan_no)";
		$outboundChallanCond="group_concat(b.challan_no)";
		$inboundRecId="group_concat(b.delivery_id)"; 
		$outboundRecId="group_concat(b.receive_id)";
	}
	else
	{
		$inboundChallanCond="rtrim(xmlagg(xmlelement(e,b.challan_no,',').extract('//text()') order by b.challan_no).GetClobVal(),',')";
		$outboundChallanCond="rtrim(xmlagg(xmlelement(e,b.challan_no,',').extract('//text()') order by b.challan_no).GetClobVal(),',')";
		$inboundRecId="rtrim(xmlagg(xmlelement(e,b.delivery_id,',').extract('//text()') order by b.delivery_id).GetClobVal(),',')"; 
		$outboundRecId="rtrim(xmlagg(xmlelement(e,b.receive_id,',').extract('//text()') order by b.receive_id).GetClobVal(),',')";
	}
	
	$subconInBillDataArray=sql_select("select a.id as mst_id, a.bill_no, a.company_id, a.party_source, a.party_id, $inboundChallanCond as syschallan_no, $inboundRecId as party_challan, sum(b.delivery_qty) as qty, sum(b.amount) AS knit_bill from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_source=1 and a.process_id=2 group by a.id, a.bill_no, a.company_id, a.party_source, a.party_id");// b.order_id, b.currency_id
	
	$subconOutBillDataArray=sql_select("select a.id as mst_id, a.bill_no, a.supplier_id, $outboundChallanCond as syschallan_no, $outboundRecId as party_challan, sum(b.receive_qty) as qty, sum(b.amount) AS knit_bill from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.process_id=2 and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.bill_no, a.supplier_id");// b.order_id, b.currency_id

	//$bookingArray=return_library_array( "select b.po_break_down_id, sum(b.grey_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id) and a.item_category in(2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id", "po_break_down_id", "qnty");
	
	//$greyProdArray=return_library_array( "select po_breakdown_id, sum(quantity) as qnty from order_wise_pro_details where po_breakdown_id in($po_id) and entry_form=2 and status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "qnty");
	
	//$knitCostArray=return_library_array( "select job_no, sum(amount) AS knit_charge from wo_pre_cost_fab_conv_cost_dtls where cons_process=1 and status_active=1 and is_deleted=0 group by job_no", "job_no", "knit_charge");
	
	$rcvMasterIdArr = array();
	foreach($subconInBillDataArray as $row)
	{
		if($db_type==2)
		{
			$row[csf('party_challan')]=$row[csf('party_challan')]->load();
		}
		$expartyChallan=explode(",",$row[csf('party_challan')]);
		foreach($expartyChallan as $pChallan)
		{
			$rcvMasterIdArr[$pChallan] = $pChallan;
		}
	}
	
	foreach($subconOutBillDataArray as $row)
	{
		if($db_type==2)
		{
			$row[csf('party_challan')]=$row[csf('party_challan')]->load();
		}
		$expartyChallan=explode(",",$row[csf('party_challan')]);
		foreach($expartyChallan as $pChallan)
		{
			$rcvMasterIdArr[$pChallan] = $pChallan;
		}
	}
	
	//$exchange_rate = 76;
	//$costing_per_arr=return_library_array( "select job_no, costing_per_id from wo_pre_cost_dtls where status_active=1 and is_deleted=0", "job_no", "costing_per_id");
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name" );
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
	$recChallan=return_library_array( "select id, challan_no from inv_receive_master where id in(".implode(",",$rcvMasterIdArr).")", "id", "challan_no");
	?>
	<script>
    function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		document.getElementById('scroll_body2').style.overflow="auto";
		document.getElementById('scroll_body2').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('main_body').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="290px";
		document.getElementById('scroll_body2').style.overflowY="scroll";
		document.getElementById('scroll_body2').style.maxHeight="290px";
	}
	
	function print_report(data,party_source)
	{
		//alert("su..re");
		var report_title="Knitting Bill";
		var show_val_column='';
		if(party_source==1)
		{
			var r=confirm("Press \"OK\" to open with Order Comments\nPress \"Cancel\" to open without Order Comments");
			if (r==true) show_val_column="1";
			else show_val_column="0";
		}
		else show_val_column="0";
		var data=data+"*"+report_title+"*"+show_val_column;
		window.open("../../../../subcontract_bill/requires/knitting_bill_issue_controller.php?data="+data+'&action=knitting_bill_print', true );
	}
	
    </script>
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="main_body" style="width:770px;">
        <fieldset style="width:760px;">
        <legend>In Bound Bill</legend>
        	<table class="rpt_table" width="760" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="30">SL</th>
                    <th width="110">Bill No</th>
                    <th width="150">Party</th>
                    <th width="130">System Challan</th>
                    <th width="130">Party Challan</th>
                    <th width="80">Bill Quantity</th>
                    <th>Bill Amount (USD)</th>
                </thead>
            </table>	
            <div style="width:760px; max-height:290px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="740" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					$i=1;
					$tot_bill_qnty=$tot_bill_amt=0;
					foreach($subconInBillDataArray as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$knitbill=$row[csf('knit_bill')]/$exchange_rate;
						$partyName="";
						if($row[csf('party_source')]==1)
							$partyName=$company_arr[$row[csf('party_id')]];
						else if($row[csf('party_source')]==2)
							$partyName=$buyer_arr[$row[csf('party_id')]];
						
						if($db_type==2)
						{
							$row[csf('syschallan_no')]=$row[csf('syschallan_no')]->load();
						}
							
						$syschallan_no=implode(",",array_filter(array_unique(explode(",",$row[csf('syschallan_no')]))));
						
						if($db_type==2)
						{
							$row[csf('party_challan')]=$row[csf('party_challan')]->load();
						}
							
						$expartyChallan=explode(",",$row[csf('party_challan')]);
						$party_challan='';
						foreach($expartyChallan as $pChallan)
						{
							if($party_challan=="")
								$party_challan=$recChallan[$pChallan];
							else
								$party_challan.=','.$recChallan[$pChallan];
						}
						$party_challan=implode(",",array_filter(array_unique(explode(",",$party_challan))));
						?>
						<tr bgcolor="<?=$bgcolor; ?>">
							<td width="30"><?=$i; ?></td>
							<td width="110" style="word-break:break-all;"><a href="##" onClick="print_report('<?php echo $row[csf('company_id')]."*".$row[csf('mst_id')]."*".$row[csf('bill_no')]; ?>','<?php echo $row[csf('party_source')]; ?>')"><?=$row[csf('bill_no')]; ?></a></td>
                            <td width="150" style="word-break:break-all;"><?=$partyName; ?></td>
                            <td width="130" style="word-break:break-all;"><?=$syschallan_no; ?></td>
                            <td width="130" style="word-break:break-all;"><?=$party_challan; ?></td>
                            <td width="80" align="right"><?=number_format($row[csf('qty')],2,'.',''); $tot_bill_qnty+=$row[csf('qty')]; ?></td>
							<td align="right"><?=number_format($knitbill,2,'.',''); $tot_bill_amt+=$knitbill; ?></td>
						</tr>
						<?
                        $i++;
					}
					?>
                	<tfoot>
                        <th colspan="5">Total</th>
                        <th><?=number_format($tot_bill_qnty,2,'.',''); ?></th>
                        <th><?=number_format($tot_bill_amt,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
            <br>
        <legend>Out Bound Bill</legend>
        	<table class="rpt_table" width="760" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="30">SL</th>
                    <th width="110">Bill No</th>
                    <th width="150">Party</th>
                    <th width="130">System Challan</th>
                    <th width="130">Party Challan</th>
                    <th width="80">Bill Quantity</th>
                    <th>Bill Amount (USD)</th>
                </thead>
            </table>	
            <div style="width:760px; max-height:290px; overflow-y:scroll" id="scroll_body2">
                <table class="rpt_table" width="740" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					$i=1;
					$tot_bill_qnty=0;
					$tot_bill_amt=0;
					foreach($subconOutBillDataArray as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$knitbill=$row[csf('knit_bill')]/$exchange_rate;	
						$partyName="";
						$partyName=$supplier_arr[$row[csf('supplier_id')]];
						
						if($db_type==2)
						{
							$row[csf('syschallan_no')]=$row[csf('syschallan_no')]->load();
						}
							
						$syschallan_no=implode(",",array_filter(array_unique(explode(",",$row[csf('syschallan_no')]))));
						
						if($db_type==2)
						{
							$row[csf('party_challan')]=$row[csf('party_challan')]->load();
						}
							
						$expartyChallan=explode(",",$row[csf('party_challan')]);
						$party_challan='';
						foreach($expartyChallan as $pChallan)
						{
							if($party_challan=="")
								$party_challan=$recChallan[$pChallan];
							else
								$party_challan.=','.$recChallan[$pChallan];
						}
						$party_challan=implode(",",array_filter(array_unique(explode(",",$party_challan))));
						?>
						<tr bgcolor="<?=$bgcolor; ?>">
							<td width="30"><?=$i; ?></td>
							<td width="110" style="word-break:break-all;"><? echo $row[csf('bill_no')]; ?></td>
                            <td width="150" style="word-break:break-all;"><?=$partyName; ?></td>
                            <td width="130" style="word-break:break-all;"><?=$syschallan_no; ?></td>
                            <td width="130" style="word-break:break-all;"><?=$party_challan; ?></td>
                            <td width="80" align="right"><?=number_format($row[csf('qty')],2,'.',''); $tot_bill_qnty+=$row[csf('qty')]; ?></td>
							<td align="right"><?=number_format($knitbill,2,'.',''); $tot_bill_amt+=$knitbill; ?></td>
						</tr>
						<?
                        $i++;
					}
					?>
                	<tfoot>
                        <th colspan="5">Total</th>
                        <th><?=number_format($tot_bill_qnty,2,'.',''); ?></th>
                        <th><?=number_format($tot_bill_amt,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
	<?
	//costing_per_arr
	exit();
}
disconnect($con);
?>