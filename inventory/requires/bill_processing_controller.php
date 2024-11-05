<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_buyer")
{
	//echo $data;
	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	
}


if ($action=="mrr_popup")
{
	echo load_html_head_contents("MRR Info", "../../", 1, '','','','');
	extract($_REQUEST);
	// echo $allprodIds. "hlw";
	?> 
	<script>
		var allprodIds='<?php echo $allprodIds; ?>';
		if(allprodIds!='' && allprodIds!='undefined')
		{
			var allprodIds_arr=allprodIds.split(',');
			allprodIds_arr[0]+"="+allprodIds_arr[1];
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		function check_all_data(is_checked)
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				//js_set_value( i );
				if(is_checked==true)
				{
					document.getElementById( 'search' + i ).style.backgroundColor='yellow';
				}
				else
				{
					document.getElementById( 'search' + i ).style.backgroundColor='#FFFFCC';	
				}
			}
		}
		function js_set_value( str) 
		{
			for(var i in allprodIds_arr)
			{
				var pord_id=$('#prodIds'+str).val();
				if(allprodIds_arr[i]==pord_id)
				{
					alert("Already Selected");
					return;
				}
			}
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
		}
		
		function reset_hide_field()
		{
			$('#hidden_data').val( '' );
		}

		function set_receive_basis(recieve_basis)
		{
			$('#txt_search_common').val('');
			$('#txt_search_common').removeAttr('disabled','disabled');
			
			if(recieve_basis == 1)
			{
				$('#td_caption').text('Enter Program No');	
			}
			else if(recieve_basis == 2)
			{
				$('#td_caption').text('Enter Booking No');	
			}
			else if(recieve_basis == 3)
			{
				$('#td_caption').text('Enter PI No');	
			}
			else
			{
				$('#td_caption').text('');	
				$('#txt_search_common').attr('disabled','disabled');
			}
		}
		
		function set_search_by(type)
		{
			$('#txt_search_val').val('');
			
			if(type == 1)
			{
				$('#td_search').text('Enter Job No');	
			}
			else if(type == 2)
			{
				$('#td_search').text('Enter Order No');	
			}
			else if(type == 3)
			{
				$('#td_search').text('Enter File No');	
			}
			else
			{
				$('#td_search').text('Enter Ref. No');	
			}
		}
		
		function fnc_close()
		{
			var hidden_data='';
			
			$("#tbl_list_search").find('tr:not(:first)').each(function()
			{
				var tr_id = $(this).attr("id");
				var bgColor=document.getElementById(tr_id).style.backgroundColor;
				if(bgColor=='yellow')
				{
					var woNo=$(this).find('input[name="woNo[]"]').val();
					var woYear=$(this).find('input[name="woYear[]"]').val();
					var bookingNo=$(this).find('input[name="bookingNo[]"]').val();
					var woID=$(this).find('input[name="woID[]"]').val(); 
					var prodIds=$(this).find('input[name="prodIds[]"]').val();
					var supplierId=$(this).find('input[name="supplierId[]"]').val();
					var storeId=$(this).find('input[name="storeId[]"]').val();
					var receiveDate=$(this).find('input[name="receiveDate[]"]').val();
					var challanNo=$(this).find('input[name="challanNo[]"]').val();
					var challanDate=$(this).find('input[name="challanDate[]"]').val();
					var currencyId=$(this).find('input[name="currencyId[]"]').val();
					var source=$(this).find('input[name="source[]"]').val();
					var recvNo=$(this).find('input[name="recvNo[]"]').val();
					var recvBasis=$(this).find('input[name="recvBasis[]"]').val();
					var jobNo=$(this).find('input[name="jobNo[]"]').val();
					var styleRefNo=$(this).find('input[name="styleRefNo[]"]').val();
					var orderUom=$(this).find('input[name="orderUom[]"]').val();
					var woQty=$(this).find('input[name="woQty[]"]').val();
					var amount=$(this).find('input[name="amount[]"]').val();
					var itemCat=$(this).find('input[name="itemCat[]"]').val();
					var exchangeRate=$(this).find('input[name="exchangeRate[]"]').val();
					var recvId=$(this).find('input[name="recvId[]"]').val();
					var trimsDtlsId=$(this).find('input[name="trimsDtlsId[]"]').val();
					var po_id=$(this).find('input[name="po_id[]"]').val();
					var itemDes=$(this).find('input[name="itemDes[]"]').val();
					var itemGrpID=$(this).find('input[name="itemGrpID[]"]').val();
					var fullRecvqnty=$(this).find('input[name="fullRecvqnty[]"]').val();
					var paymentOverrecv=$(this).find('input[name="paymentOverrecv[]"]').val();
					var consRate=$(this).find('input[name="consRate[]"]').val();
					var mrrAmount=$(this).find('input[name="mrrAmount[]"]').val();
					var currencyIDs=$(this).find('input[name="currencyIDs[]"]').val();
					var data='';
					$(this).find('td:not(:first-child)').each (function() 
					{
						data+="**"+$(this).text();
					});
					if(hidden_data=="")																
					{
						hidden_data=woNo+"**"+woYear+"**"+bookingNo+"**"+supplierId+"**"+storeId+"**"+receiveDate+"**"+challanNo+"**"+challanDate+"**"+currencyId+"**"+source+"**"+recvNo+"**"+recvBasis+"**"+exchangeRate+"**"+recvId+"**"+itemDes+"**"+fullRecvqnty+"**"+paymentOverrecv+"**"+consRate+"**"+mrrAmount+"**"+jobNo+"**"+styleRefNo+"**"+orderUom+"**"+woQty+"**"+amount+"**"+itemGrpID+"**"+trimsDtlsId+"**"+woID+"**"+prodIds+"**"+itemCat+"**"+po_id+"**"+currencyIDs+data;  

					} 
					else
					{
						hidden_data+="_"+woNo+"**"+woYear+"**"+bookingNo+"**"+supplierId+"**"+storeId+"**"+receiveDate+"**"+challanNo+"**"+challanDate+"**"+currencyId+"**"+source+"**"+recvNo+"**"+recvBasis+"**"+exchangeRate+"**"+recvId+"**"+itemDes+"**"+fullRecvqnty+"**"+paymentOverrecv+"**"+consRate+"**"+mrrAmount+"**"+jobNo+"**"+styleRefNo+"**"+orderUom+"**"+woQty+"**"+amount+"**"+itemGrpID+"**"+trimsDtlsId+"**"+woID+"**"+prodIds+"**"+itemCat+"**"+po_id+"**"+currencyIDs+data;
					}
				}
			});
			$('#hidden_data').val( hidden_data );
			parent.emailwindow.hide();
		}
    </script>
	<script>
		$(document).ready(function() {
            setFilterGrid('tbl_list_search',-1);
			reset_hide_field();
        });
	</script>  
	</head>

	<body>
	<div align="center" style="width:900px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:975px; margin-left:3px">
			<legend>Enter search words</legend>   
				<table cellpadding="0" cellspacing="0" width="920" class="rpt_table" border="1" rules="all">
					<tr>
						<input type="hidden" name="hidden_data" id="hidden_data" class="text_boxes" value="">
						<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
						<input type="hidden" name="txt_wo_po_id" id="txt_wo_po_id" class="text_boxes" value="<? echo $wo_po_id; ?>">
						<input type="hidden" name="txt_wo_po_no" id="txt_wo_po_no" class="text_boxes" value="<? echo $wo_po_no; ?>">
						<input type="hidden" name="hidden_recv_id" id="hidden_recv_id" class="text_boxes" value="">  
					</tr>
				</table>
			<div style="width:100%; margin-top:5px;" id="search_div" align="left">
			<?        
				// CALL  "create_mrr_search_list_view" after opening this popup    
				$company_id =$cbo_company_id; 
				$supplier_id =$party_id; 
				$wo_po_id =$wo_po_id; 
				$wo_po_no =$wo_po_no;
				$item_cat_id =$item_cat_id;
				
				// $hidden_wo_nonwo_type =$hidden_wo_nonwo_type; 
				$hidden_reference_ids=$hidden_reference_ids;
				// echo "Hello-". $hidden_reference_ids; die;
				if($supplier_id==0) $supplier_name="%%"; else $supplier_name=$supplier_id;
				$supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
				if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
				else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
				else $year_field="";//defined Later
				$year_condition="";
				if($year_id>0)
				{
					if($db_type==0)
					{
						$year_condition=" and YEAR(a.insert_date)='$year_id'";
					}
					else
					{
						$year_condition=" and to_char(a.insert_date,'YYYY')='$year_id'";
					}
				}
				
				$sql_styleRef_no = sql_select("SELECT a.job_no,a.style_ref_no from wo_po_details_master a,wo_po_break_down b where  a.job_no=b.job_no_mst  and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id group by a.job_no,a.style_ref_no" );
				foreach ($sql_styleRef_no as $row)
				{  
					$styleRef_no_arr[$row[csf('job_no')]]['style_ref_no']=$row[csf('style_ref_no')];
				}
				// echo "<pre>"; print_r($styleRef_no_arr); die;
				if($item_cat_id == 2 || $item_cat_id == 3  || $item_cat_id == 13  || $item_cat_id == 14)
				{
					$sql_wo= sql_select("SELECT a.id, a.booking_no as booking_no, b.job_no as job_no ,b.wo_qnty as wo_qunty 
					from wo_booking_mst a ,wo_booking_dtls b 
					where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category = $item_cat_id and a.supplier_id='$party_id' and a.status_active=1 and a.is_deleted=0 
					union all
					SELECT a.id, a.booking_no, null as job_no ,NULL as wo_qunty 
					from wo_non_ord_samp_booking_mst a 
					where a.company_id=$company_id and a.item_category = $item_cat_id and a.supplier_id='$party_id' and a.status_active=1 and a.is_deleted=0");
				
				}
				else if($item_cat_id == 4)
				{
					$sql_wo= sql_select("SELECT a.id, a.booking_no as booking_no, b.job_no as job_no ,b.wo_qnty as wo_qunty 
					from wo_booking_mst a ,wo_booking_dtls b 
					where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category = $item_cat_id and a.supplier_id='$party_id' and a.status_active=1 and a.is_deleted=0 
					union all  
					SELECT a.id, a.wo_number as booking_no, null as job_no,NULL as wo_qunty 
					from wo_non_order_info_mst a,wo_non_order_info_dtls b 
					where a.id=b.mst_id and a.company_name=$company_id and b.item_category_id = $item_cat_id and a.supplier_id='$party_id' and a.status_active=1 and a.is_deleted=0 ");
				}
				else
				{
					$sql_wo= sql_select("SELECT a.id, a.wo_number as booking_no,b.supplier_order_quantity as wo_qunty  
					from wo_non_order_info_mst a,wo_non_order_info_dtls b where a.id=b.mst_id and a.company_name=$company_id and a.supplier_id='$party_id' and b.item_category_id in($item_cat_id) and a.status_active=1 and a.is_deleted=0 ");
				}
				// echo "<pre>"; print_r($sql_wo); die;
				foreach($sql_wo as $row)
				{  
					$job_no_arr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
					$booking_no_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
					$woQty_arr[$row[csf('id')]]['wo_qunty']+=$row[csf('wo_qunty')];
				}
				
				// echo "<pre>"; print_r($woQty_arr); die;
				// $sql_bill = sql_select("SELECT b.prod_id from inv_bill_processing_dtls b, inv_transaction c where b.receive_id=c.mst_id and b.is_deleted=0 and b.status_active=1 and b.wo_po_id in($wo_po_id) group by b.prod_id");

				$sql_bill = sql_select("SELECT b.prod_id from inv_bill_processing_dtls b where b.is_deleted=0 and b.status_active=1 and b.wo_po_id in($wo_po_id) group by b.prod_id");

				// echo "SELECT b.prod_id from inv_bill_processing_dtls b where b.is_deleted=0 and b.status_active=1 and b.wo_po_id in($wo_po_id) group by b.prod_id"; die;
				// echo "<pre>"; print_r($sql_bill); die;
				//$all_bill_mrr_id="";
				$all_bill_receive_id="";
				foreach ($sql_bill as $row)
				{ 
					// $all_bill_mrr_id.=$row[csf('receive_id')].",";	
					$all_bill_receive_id.=$row[csf('prod_id')].",";	
				}
				
				$all_bill_receive_id =chop($all_bill_receive_id,',');
				$all_bill_receive_ids='('.$all_bill_receive_id.')';
				// echo "<pre>"; print_r($all_bill_receive_ids); die;
				if( $all_bill_receive_id!=""){ $prod_id_cond="and b.prod_id not in $all_bill_receive_ids";} else {  $prod_id_cond="";}

				$sql =	"SELECT a.id as recv_id, a.recv_number_prefix_num, a.recv_number as system_no, to_char(a.insert_date,'YYYY') as year, a.booking_no, a.booking_id, a.supplier_id, a.store_id, a.source, a.currency_id, a.receive_date, a.challan_no, a.challan_date, a.receive_basis, b.item_category, sum(b.order_amount) as mrr_amount, sum(b.order_qnty) as receive_qnty, avg(b.order_rate) as rate, b.order_uom as uom, b.prod_id, e.item_group_id, e.item_description , 0 as  po_breakdown_id
				from inv_receive_master a, inv_transaction b, product_details_master e 
				where a.id=b.mst_id  and b.prod_id=e.id and a.company_id=$company_id and b.item_category in ($item_cat_id) and a.booking_id in($wo_po_id) and a.receive_basis=2  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $prod_id_cond  
				group by a.id, a.recv_number_prefix_num, a.recv_number, a.insert_date, a.booking_no, a.booking_id, a.supplier_id, a.store_id, a.source, a.currency_id, a.receive_date, a.challan_no, a.challan_date, a.receive_basis, b.item_category, b.order_uom, b.prod_id, e.item_group_id, e.item_description";
				// echo $sql; die;
				$result = sql_select($sql);
				// echo "<pre>";print_r($result); die;
				$order_job_arr=array();$all_order_id="";
				if($hidden_wo_nonwo_type==0)
				{
					foreach($result as $row)
					{
						$all_order_id.=$row[csf("po_breakdown_id")].",";
					}
					$all_order_id=chop($all_order_id,",");
					if($all_order_id!="")
					{
						$job_order_sql=sql_select("select b.id, a.job_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($all_order_id)");
						foreach($job_order_sql as $row)
						{
							$order_job_arr[$row[csf("id")]]=$row[csf("job_no")];
						}
					}
				}
				
				?>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1030" class="rpt_table">
					<thead>
						<th width="25">SL</th>
						<th width="60">Received No</th>
						<th width="80">Year</th>
						<th width="120">WO</th>
						<th width="100">Supplier</th>
						<th width="100">Item Desc</th>
						<th width="40">UOM</th>
						<th width="130">Store</th>
						<th width="80">Receive date</th>
						<th width="50">Challan No</th>
						<th width="80">Challan Date</th>
						<th width="50">Currency</th>
						<th>Source</th>
					</thead>
				</table>
				<div style="width:1030px; max-height:250px; overflow-y:scroll" id="list_container_batch" align="left">	 
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1010" class="rpt_table" id="tbl_list_search">  
						<?
						$store_arr = return_library_array("select id, store_name from lib_store_location","id","store_name");
						$itemGroup_arr = return_library_array("select id, item_name from lib_item_group","id","item_name");
						$i=1;
						foreach ($result as $row)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)"> 
							<td width="25">
								<? echo $i; ?>
								<input type="hidden" name="woNo[]" id="woNo<? echo $i; ?>" value="<? echo $row[csf('recv_number_prefix_num')]; ?>"/>
								<input type="hidden" name="woYear[]" id="woYear<? echo $i; ?>" value="<? echo $row[csf('year')]; ?>"/>
								<input type="hidden" name="bookingNo[]" id="bookingNo<? echo $i; ?>" value="<? echo $booking_no_arr[$row[csf('booking_id')]]['booking_no']; ?>"/>
								<input type="hidden" name="woID[]" id="woID<? echo $i; ?>" value="<? echo $row[csf('booking_id')]; ?>"/>
								<input type="hidden" name="supplierId[]" id="supplierId<? echo $i; ?>" value="<? echo $supplier_arr[$row[csf('supplier_id')]]; ?>"/>
								<input type="hidden" name="storeId[]" id="storeId<? echo $i; ?>" value="<? echo $store_arr[$row[csf('store_id')]]; ?>"/>
								<input type="hidden" name="receiveDate[]" id="receiveDate<? echo $i; ?>" value="<? echo change_date_format($row[csf('receive_date')]); ?>"/>
								<input type="hidden" name="challanNo[]" id="challanNo<? echo $i; ?>" value="<? echo $row[csf('challan_no')]; ?>"/>
								<input type="hidden" name="challanDate[]" id="challanDate<? echo $i; ?>" value="<? echo change_date_format($row[csf('challan_date')]); ?>"/>
								<input type="hidden" name="currencyId[]" id="currencyId<? echo $i; ?>" value="<? echo $currency[$row[csf('currency_id')]]; ?>"/>
								<input type="hidden" name="source[]" id="source<? echo $i; ?>" value="<? echo $source[$row[csf('source')]]; ?>"/>
								<input type="hidden" name="recvNo[]" id="recvNo<? echo $i; ?>" value="<? echo $row[csf('system_no')]; ?>"/>
								<input type="hidden" name="recvBasis[]" id="recvBasis<? echo $i; ?>" value="<? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?>"/>
								<input type="hidden" name="jobNo[]" id="jobNo<? echo $i; ?>" value="<? echo $job_no_arr[$row[csf('booking_id')]]['job_no']; ?>"/>
								<input type="hidden" name="styleRefNo[]" id="styleRefNo<? echo $i; ?>" value="<? echo $styleRef_no_arr[$job_no_arr[$row[csf('booking_id')]]['job_no']]['style_ref_no']; ?>"/>
								<input type="hidden" name="orderUom[]" id="orderUom<? echo $i; ?>" value="<? echo $unit_of_measurement[$row[csf('uom')]]; ?>"/> 
								<input type="hidden" name="woQty[]" id="woQty<? echo $i; ?>" value="<? echo $woQty_arr[$row[csf('booking_id')]]['wo_qunty'];//$row[csf('wo_qnty')]; ?>"/> 
								<input type="hidden" name="amount[]" id="amount<? echo $i; ?>" value="<? if($row[csf('currency_id')]==2){ echo number_format($row[csf('mrr_amount')],2,'.','');} else{}; ?>"/> 
								
								<input type="hidden" name="exchangeRate[]" id="exchangeRate<? echo $i; ?>" value="<? echo  $row[csf('mrr_amount')]; ?>"/>
								<input type="hidden" name="recvId[]" id="recvId<? echo $i; ?>" value="<? echo $row[csf('recv_id')]; ?>"/>
								<input type="hidden" name="prodIds[]" id="prodIds<? echo $i; ?>" value="<? echo $row[csf('prod_id')]; ?>"/>
								<input type="hidden" name="trimsDtlsId[]" id="trimsDtlsId<? echo $i; ?>" value="<? echo $row[csf('trims_dtls_id')]; ?>"/>
								<input type="hidden" name="po_id[]" id="po_id<? echo $i; ?>" value="<? echo $row[csf('po_breakdown_id')]; ?>"/>

								<input type="hidden" name="itemCat[]" id="itemCat<? echo $i; ?>" value="<? echo $item_category[$row[csf('item_category')]]; ?>"/>
								<input type="hidden" name="itemDes[]" id="itemDes<? echo $i; ?>" value="<? echo $row[csf('item_description')]; ?>"/>
								<input type="hidden" name="itemGrpID[]" id="itemGrpID<? echo $i; ?>" value="<? echo $itemGroup_arr[$row[csf('item_group_id')]]; ?>"/>
								<input type="hidden" name="fullRecvqnty[]" id="fullRecvqnty<? echo $i; ?>" value="<? echo $row[csf('receive_qnty')]; ?>"/>
								<input type="hidden" name="paymentOverrecv[]" id="paymentOverrecv<? echo $i; ?>" value="<? if($row[csf('payment_over_recv')]==0) {echo $row[csf('receive_qnty')];} else { echo "";}; ?>"/>
								<input type="hidden" name="consRate[]" id="consRate<? echo $i; ?>" value="<? echo $row[csf('rate')]; ?>"/>
								<input type="hidden" name="mrrAmount[]" id="mrrAmount<? echo $i; ?>" value="<? echo $row[csf('mrr_amount')]; ?>"/>
								<input type="hidden" name="currencyIDs[]" id="currencyIDs<? echo $i; ?>" value="<? echo $row[csf('currency_id')]; ?>"/>
							</td>
							<td width="60"><div style="word-wrap:break-word; width:60px"><? echo $row[csf('recv_number_prefix_num')]; ?></div></td>
							<td width="80"><div style="word-wrap:break-word; width:80px; text-align:center;"><? echo $row[csf('year')]; ?></div></td>
							<td width="120"><div style="word-wrap:break-word; width:120px"><? echo $booking_no_arr[$row[csf('booking_id')]]['booking_no']; ?></div></td>
							<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></div></td>
							<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('item_description')]; ?></div></td>
							<td width="40" align="center"><div style="word-wrap:break-word; width:40px"><? echo  $unit_of_measurement[$row[csf('uom')]]; ?></div></td>
							<td width="130"><div style="word-wrap:break-word; width:130px"><? echo $store_arr[$row[csf('store_id')]]; ?></div></td>
							<td width="80"><div style="word-wrap:break-word; width:80px"><? echo $row[csf('receive_date')]; ?></div></td>
							<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $row[csf('challan_no')]; ?></div></td>
							<td width="80"><div style="word-wrap:break-word; width:80px"><? echo $row[csf('challan_date')]; ?></div></td>
							<td width="50"><div style="word-wrap:break-word; width:50px; text-align:center;"><? echo $currency[$row[csf('currency_id')]]; ?></div></td>
							<td><div style="word-wrap:break-word; width:80px"><? echo $source[$row[csf('source')]]; ?></div></td>
						</tr>
						<?
							$i++;
							}
						?>
					</table>
				</div>
			<table width="900" cellspacing="0" cellpadding="0" border="1" align="center">
					<tr>
						<td align="center" height="30" valign="bottom">
							<div style="width:100%"> 
								<div style="width:45%; float:left" align="left">
									<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data(this.checked)" /> Check / Uncheck All
								</div>
								<div style="width:55%; float:left" align="left">
									<input type="button" name="close" onClick="fnc_close();" class="formbutton" value="Close" style="width:100px" />
								</div>
							</div>
						</td>
					</tr>
				</table>
				<?	
				exit();
			?>     
				</div>
			</fieldset>
		</form>
	</div>
	</body> 
				
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0) $year_cond="YEAR(insert_date)"; 
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		else $year_cond="";//defined Later
		
		$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'BP', date("Y",time()), 5, "select bill_number_prefix, bill_number_prefix_num from inv_bill_processing_mst where company_id=$cbo_company_id and $year_cond=".date('Y',time())." order by id desc ", "bill_number_prefix","bill_number_prefix_num"));
		$id=return_next_id( "id", "inv_bill_processing_mst", 1 ) ;
				 
		$field_array="id,bill_number_prefix,bill_number_prefix_num,bill_number,company_id,party_id,bill_no,bill_date,remarks,buyer_id,inserted_by,insert_date";
		$data_array="(".$id.",'".$new_mrr_number[1]."',".$new_mrr_number[2].",'".$new_mrr_number[0]."',".$cbo_company_id.",".$hidden_party_id.",".$txt_bill_no.",".$txt_bill_date.",".$txt_remarks.",".$cbo_buyer_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		$field_array_dtls="id, mst_id,receive_id,receive_no,accepted_qty,trims_dtls_id,wo_po_no,wo_po_id,prod_id,po_id,mrr_amount,currency_id,inserted_by, insert_date";
		$dtls_id = return_next_id( "id", "inv_bill_processing_dtls", 1 );

		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$receiveId="receiveId".$j;
			$receiveNo="receiveNo".$j;
			$acceptedQnty="acceptedQnty".$j;
			$trimsDtlsId="trimsDtlsId".$j;
			$woNumber="woNumber".$j;
			$woID="woID".$j;
			$prodIds="prodIds".$j;
			$po_id="po_id".$j;
			$mrrAmount="mrrAmount".$j;
			$currencyIDs="currencyIDs".$j;
			
			//if($$receiveBasis==1) $program_booking_pi_no=$$programNo; else $program_booking_pi_no=$$bookingNo;

			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$dtls_id.",".$id.",".$$receiveId.",'".$$receiveNo."','".$$acceptedQnty."','".$$trimsDtlsId."','".$$woNumber."','".$$woID."','".$$prodIds."','".$$po_id."','".$$mrrAmount."','".$$currencyIDs."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$dtls_id = $dtls_id+1;
		}
		//echo "10**insert into inv_bill_processing_mst (".$field_array.") values ".$data_array;die;
		//echo "10**insert into inv_bill_processing_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID=sql_insert("inv_bill_processing_mst",$field_array,$data_array,0);
		$rID2=sql_insert("inv_bill_processing_dtls",$field_array_dtls,$data_array_dtls,1);
		//oci_rollback($con);
		//echo "10**".$rID."&&".$rID2;die;

		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**".$new_mrr_number[0];
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);  
				echo "0**".$id."**".$new_mrr_number[0];
			}
			else
			{
				oci_rollback($con);
				echo "5**0**0";
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
		
		$field_array="party_id*bill_no*bill_date*remarks*buyer_id*updated_by*update_date";
		$data_array=$hidden_party_id."*".$txt_bill_no."*".$txt_bill_date."*".$txt_remarks."*".$cbo_buyer_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$hidden_bill_dtls_id=trim($hidden_bill_dtls_id,"'");
		if($hidden_bill_dtls_id=="")
		{ 
			
			$field_array_dtls="id, mst_id,receive_id,receive_no,accepted_qty,trims_dtls_id,wo_po_no,wo_po_id,prod_id,po_id,mrr_amount,currency_id, inserted_by, insert_date";
			$dtls_id = return_next_id( "id", "inv_bill_processing_dtls", 1 );
			//$id=return_next_id( "id", "inv_bill_processing_mst", -1 ) ;
			
			for($j=1;$j<=$tot_row;$j++)
			{ 	
				$receiveId="receiveId".$j;
				$receiveNo="receiveNo".$j;
				$acceptedQnty="acceptedQnty".$j;
				$trimsDtlsId="trimsDtlsId".$j;
				$woNumber="woNumber".$j;
				$woID="woID".$j; 
				$prodIds="prodIds".$j;
				$po_id="po_id".$j;
				$mrrAmount="mrrAmount".$j;
				$currencyIDs="currencyIDs".$j;
				
				//if($$receiveBasis==1) $program_booking_pi_no=$$programNo; else $program_booking_pi_no=$$bookingNo;
	
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$dtls_id.",".$update_id.",".$$receiveId.",'".$$receiveNo."','".$$acceptedQnty."','".$$trimsDtlsId."','".$$woNumber."','".$$woID."','".$$prodIds."','".$$po_id."','".$$mrrAmount."','".$$currencyIDs."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					
				$dtls_id = $dtls_id+1;
				
			}
			 $recvIDsHidenIDs=trim($recvIDsHidenID,"'");
			 $recvIDsHidenIDs=chop($recvIDsHidenIDs,"**");
			 $recvIDsHidenIDs=str_replace("**",",",$recvIDsHidenIDs);
			// $recvIDsHidenIDs="'".$recvIDsHidenIDs."'";
			 //echo $recvIDsHidenIDs; die;
			 //$recvIDsHidenID=explode('**', $recvIDsHidenID);
			 
			//echo "10**insert into inv_bill_processing_mst (".$field_array.") values ".$data_array;die;
			//echo "10**insert into inv_bill_processing_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
			//echo $recvIDsHidenIDs;
			//echo $data_array_dtls;die;
			//$query3 = execute_query("DELETE FROM inv_bill_processing_dtls WHERE mst_id=$update_id");
			if($recvIDsHidenIDs!="")
	  		{
	  			$query3 = execute_query("DELETE FROM inv_bill_processing_dtls WHERE receive_id in ($recvIDsHidenIDs)");
	  			$rID=sql_update("inv_bill_processing_mst",$field_array,$data_array,"id",$update_id,0);
	  			$rID2=sql_insert("inv_bill_processing_dtls",$field_array_dtls,$data_array_dtls,1);
	  			
	  			//$query3=true; $rID=true; $rID2=true;
	  			//oci_rollback($con);
	  			//echo "10**insert into inv_bill_processing_dtls (".$field_array_dtls.") values ".$data_array_dtls;
	  			//echo "10**".$query3."&&".$rID."&&".$rID2;die;
	  			
	  			if($db_type==0)
	  			{
	  				if( $query3 && $rID && $rID2)
	  				{
	  					mysql_query("COMMIT");  
	  					echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_no);
	  				}
	  				else
	  				{
	  					mysql_query("ROLLBACK"); 
	  					echo "6**".str_replace("'", '', $update_id)."**";
	  				}
	  			}
	  			else if($db_type==2 || $db_type==1 )
	  			{
	  				if($query3 && $rID &&  $rID2)
	  				{
	  					oci_commit($con);  
	  					echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_no);
	  				}
	  				else
	  				{
	  					oci_rollback($con);
	  					echo "6**".str_replace("'", '', $update_id)."**1";
	  				}
	  			}
	  			disconnect($con);
	  			die;
	  		}
			else
			{
				
	  			//$query3 = execute_query("DELETE FROM inv_bill_processing_dtls WHERE receive_id in ($recvIDsHidenIDs)");
	  			$rID=sql_update("inv_bill_processing_mst",$field_array,$data_array,"id",$update_id,0);
	  			$rID2=sql_insert("inv_bill_processing_dtls",$field_array_dtls,$data_array_dtls,1);
	  			
	  			//$query3=true; $rID=true; $rID2=true;
	  			//oci_rollback($con);
	  			//echo "10**insert into inv_bill_processing_dtls (".$field_array_dtls.") values ".$data_array_dtls;
	  			//echo "10**".$query3."&&".$rID."&&".$rID2;die;
	  			
	  			if($db_type==0)
	  			{
	  				if( $rID && $rID2)
	  				{
	  					mysql_query("COMMIT");  
	  					echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_no);
	  				}
	  				else
	  				{
	  					mysql_query("ROLLBACK"); 
	  					echo "6**".str_replace("'", '', $update_id)."**";
	  				}
	  			}
	  			else if($db_type==2 || $db_type==1 )
	  			{
	  				if($rID &&  $rID2)
	  				{
	  					oci_commit($con);  
	  					echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_no);
	  				}
	  				else
	  				{
	  					oci_rollback($con);
	  					echo "6**".str_replace("'", '', $update_id)."**1";
	  				}
	  			}
	  			disconnect($con);
	  			die;
	  		
			}
			
		}
		else
		{
			$field_array_dtls="mst_id*receive_id*receive_no*accepted_qty*trims_dtls_id*wo_po_no*wo_po_id*prod_id*po_id*updated_by*update_date";
			$dtls_id = return_next_id( "id", "inv_bill_processing_dtls", 1 );
			//$id=return_next_id( "id", "inv_bill_processing_mst", -1 ) ;
	
			for($j=1;$j<=$tot_row;$j++)
			{ 	
				$receiveId="receiveId".$j;
				$receiveNo="receiveNo".$j;
				$acceptedQnty="acceptedQnty".$j;
				$trimsDtlsId="trimsDtlsId".$j;
				$woNumber="woNumber".$j;
				$woID="woID".$j; 
				$prodIds="prodIds".$j;
				$po_id="po_id".$j;
				
				
				//if($$receiveBasis==1) $program_booking_pi_no=$$programNo; else $program_booking_pi_no=$$bookingNo;
				$data_array_dtls=$update_id."*".$$receiveId."*'".$$receiveNo."'*'".$$acceptedQnty."'*'".$$trimsDtlsId."'*'".$$woNumber."'*'".$$woID."'*'".$$prodIds."'*'".$$po_id."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				
					//$dtls_id = $dtls_id+1;
			}
			
			//echo "10**insert into inv_bill_processing_mst (".$field_array.") values ".$data_array;die;
			//echo "10**insert into pro_fab_reqn_for_batch_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
			
			$rID=sql_update("inv_bill_processing_mst",$field_array,$data_array,"id",$update_id,0);
			$query3=sql_update("inv_bill_processing_dtls",$field_array_dtls,$data_array_dtls,"id",$hidden_bill_dtls_id,0);
			//$rID2=1;
			
			//$query3=true; $rID=true; $rID2=true;
			//oci_rollback($con);
			//echo "10**insert into inv_bill_processing_dtls (".$field_array_dtls.") values ".$data_array_dtls;
			//echo "10**".$query3."&&".$rID."&&".$rID2;die;
			
			if($db_type==0)
			{
				if($rID && $query3)
				{
					mysql_query("COMMIT");  
					echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_no);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "6**".str_replace("'", '', $update_id)."**";
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID && $query3)
				{
					oci_commit($con);  
					echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_no);
				}
				else
				{
					oci_rollback($con);
					echo "6**".str_replace("'", '', $update_id)."**1";
				}
			}
			disconnect($con);
			die;
		}
	}
	else if ($operation==2)   // Delete Here
	{
		
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*0*1";
		$hidden_bill_dtls_id=trim($hidden_bill_dtls_id,"'");
		//echo $hidden_bill_dtls_id;
		$rID=sql_delete("inv_bill_processing_dtls",$field_array,$data_array,"id",$hidden_bill_dtls_id,1);
		//$rID=sql_delete("tbl_department_test",$field_array,$data_array,"id","$update_id",0);

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "1**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
	          if($rID )
			    {
					oci_commit($con);   
					echo "2**".$rID;
				}
			else{
					oci_rollback($con);
					echo "10**".$rID;
				}
		}
		disconnect($con);die;
	}
}

if($action=="bill_popup")
{
	echo load_html_head_contents("Bill Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
?> 
	<script>
		function js_set_value(data)
		{
			$('#hidden_bill_id').val(data);
			parent.emailwindow.hide();
		}
	
    </script>

</head>
<body>
<div align="center" style="width:760px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:760px; margin-left:2px">
            <table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Party</th>
                    <th>Bill Date Range</th>
                    <th id="search_by_td_up" width="120">Bill No</th>
                    <th width="120">System No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                    	<input type="hidden" name="hidden_bill_id" id="hidden_bill_id">  
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">
                    	 <? 
						 $sql_party = "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and b.party_type=4 and a.tag_company='$cbo_company_id' and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name"; 
						 echo create_drop_down( "cbo_party_id", 150, $sql_party,'id,supplier_name', 1, '-- Select Party --',0,"",0); ?>        
                    </td>
                    <td align="center">
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" readonly>To
					  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" readonly>
					</td>
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:100px" class="text_boxes"  name="txt_bill_no" id="txt_bill_no" />	
                    </td> 
                    <td align="center">				
                        <input type="text" style="width:100px" class="text_boxes"  name="txt_system_no" id="txt_system_no" />	
                    </td>						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_bill_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_party_id').value+'_'+document.getElementById('txt_system_no').value+'_'+<? echo $cbo_company_id; ?>, 'create_bill_search_list_view', 'search_div', 'bill_processing_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
           <div style="width:100%; margin-top:5px;" id="search_div" align="center"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_bill_search_list_view")
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0]). "%"; 
	$start_date =$data[1];
	$end_date =$data[2];
	$party_id =$data[3]; 
	$bill_system_no ="%".trim($data[4]). "%"; 
	$company_id =$data[5]; 

	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.bill_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.bill_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	$search_field_cond="";
	if(trim($data[0])!="")
	{
		$search_field_cond="and a.bill_no like '$search_string'";
	}
	$search_system_no_cond="";
	if(trim($data[4])!="")
	{
		$search_system_no_cond="and a.bill_number like '$bill_system_no'";
	}
	
	$party_cond="";
	if($party_id>0)
	{
		$party_cond="and a.party_id=$party_id";
	}
	
	if($db_type==0) 
	{
		$year_field="YEAR(a.insert_date) as year,";
	}
	else if($db_type==2) 
	{
		$year_field="to_char(a.insert_date,'YYYY') as year,";
	}
	else $year_field="";//defined Later

	if($db_type==2) $group_concat="listagg(CAST(b.receive_id as VARCHAR(4000)),',') within group (order by b.receive_id) as receive_id";
	else  $group_concat="group_concat(b.receive_id) as receive_id";
	
	  $sql = "select a.id,a.bill_number,a.company_id, $year_field a.party_id,a.bill_no, a.bill_date, $group_concat from inv_bill_processing_mst a,inv_bill_processing_dtls b  where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond $party_cond $date_cond $search_system_no_cond  
	 group by a.id,a.bill_number,a.company_id, to_char(a.insert_date,'YYYY'), a.party_id,a.bill_no, a.bill_date  
	 order by id"; 
	 
	 
	$party_arr = return_library_array("select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and b.party_type=4 and a.tag_company='$company_id' and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id","supplier_name");
	
	$arr=array(1=>$party_arr);
	echo create_list_view("tbl_list_search", "System No,Party, Year, Bill No, Bill Date", "120,250,70,130","700","200",0, $sql, "js_set_value", "id,company_id,party_id,receive_id", "", 1, "0,party_id,0,0,0", $arr, "bill_number,party_id,year,bill_no,bill_date","","",'0,0,0,0,3','');
	exit();
}

if($action=="party_popup")
{
	echo load_html_head_contents("Party Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
?> 
	<script>
		function js_set_value(data)
		{
			$('#party_id').val(data);
			parent.emailwindow.hide();
		}
    </script>
 </head>	
  <body>
    <div align="center" style="width:330px" >
    <fieldset style="width:320px"> 
        <form name="party_popup_1"  id="party_popup_1">
            <?
			$sql = "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company_id' and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name"; 
		
			echo create_list_view("list_view", "Party Name", "200","300","340",0, $sql, "js_set_value", "id,supplier_name", "", 1, "", $arr, "supplier_name","",'setFilterGrid("list_view",-1);');
			?>
        <input type="hidden" id="party_id" />
        </form>
    </fieldset>
    </div>
  </body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=='populate_data_from_bill')
{
	$data=explode("*",$data);
	
	$partyLibArr = return_library_array("select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and b.party_type=4 and a.tag_company='$data[1]' and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id","supplier_name"); 
	$data_array=sql_select("select id, bill_number, company_id, party_id, bill_date,bill_no,remarks,buyer_id from inv_bill_processing_mst where id='$data[0]'");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_system_no').value 			= '".$row[csf("bill_number")]."';\n";
		echo "document.getElementById('cbo_company_id').value 			= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value 			= '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_party').value 				= '".$partyLibArr[$row[csf("party_id")]]."';\n";
		echo "document.getElementById('hidden_party_id').value 			= '".$row[csf("party_id")]."';\n";
		echo "document.getElementById('txt_bill_date').value 			= '".change_date_format($row[csf("bill_date")])."';\n";
		echo "document.getElementById('txt_bill_no').value 				= '".$row[csf("bill_no")]."';\n";
		echo "document.getElementById('txt_remarks').value 				= '".$row[csf("remarks")]."';\n";
		//echo "document.getElementById('txt_reference_no').value 		= '".$row[csf("wo_po_no")]."';\n";
		//echo "document.getElementById('hidden_reference_id').value 		= '".$row[csf("wo_po_id")]."';\n";
		echo "document.getElementById('update_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_party').setAttribute('disabled','disabled')\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_bill_processing',1);\n"; 
		echo "document.getElementById('cbo_company_id').setAttribute('disabled','disabled')\n"; 
		exit();
	}
}

if( $action == 'populate_list_view' ) 
{
	extract($_REQUEST);
	//echo $data;
	// 2_3_313_D n C-TB-17-00519_8204_21378,21379,21379
	$datas=explode('_',$data);
	$bill_id=$datas[0];
	$company_id=$datas[1];
	$party_id=$datas[2];
	$wo_po_no=$datas[3];
	$wo_id=$datas[4];
	$recv_id=$datas[5];
	$item_category_id=$datas[6];
	$po_id=$datas[7]; 
	$po_id_expld=explode(',',$po_id);
	
	//if($party_id!=0){ $supplier_name="and a.supplier_id=$party_id";} else {  $supplier_name="";}
	//if($recv_id!=0){ $recvId_cond="and a.id=$recv_id";} else {  $recvId_cond="";}
	
	$supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
	$itemGroup_arr = return_library_array("select id, item_name from lib_item_group","id","item_name");
	
	if($item_category_id == 2 || $item_category_id == 3  || $item_category_id == 13  || $item_category_id == 14)
	{
		
		$sql_wo= sql_select("select a.id as po_break_down_id, a.booking_no as booking_no, b.job_no as job_no ,b.wo_qnty as wo_qunty 
				from wo_booking_mst a ,wo_booking_dtls b 
				where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category = $item_category_id and a.supplier_id='$party_id' and a.status_active=1 and a.is_deleted=0 
		union all
		 select a.id as po_break_down_id, a.booking_no, null as job_no ,NULL as wo_qunty 
		 from wo_non_ord_samp_booking_mst a where a.company_id=$company_id and a.item_category = $item_category_id and a.supplier_id='$party_id' and a.status_active=1 and a.is_deleted=0");
		
	}
	else if($item_category_id == 4)
	{
		$sql_wo= sql_select("select a.id, a.booking_no as booking_no, b.job_no as job_no ,b.wo_qnty as wo_qunty,b.po_break_down_id as po_break_down_id  
				from wo_booking_mst a ,wo_booking_dtls b 
				where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category = $item_category_id and a.supplier_id='$party_id' and a.status_active=1 and a.is_deleted=0 
		 union all  
		select a.id, a.wo_number as booking_no, null as job_no,NULL as wo_qunty,b.po_breakdown_id as po_break_down_id  
		from wo_non_order_info_mst a,wo_non_order_info_dtls b 
		where a.id=b.mst_id and a.company_name=$company_id and b.item_category_id = $item_category_id and a.supplier_id='$party_id' and a.status_active=1 and a.is_deleted=0 ");
	}
	else
	{
		
		$sql_wo= sql_select("select a.id, a.wo_number as booking_no,b.supplier_order_quantity as wo_qunty,b.po_breakdown_id as po_break_down_id   
				from wo_non_order_info_mst a,wo_non_order_info_dtls b where a.id=b.mst_id and a.company_name=$company_id and a.supplier_id='$party_id' and b.item_category_id=$item_category_id and a.status_active=1 and a.is_deleted=0 ");
	}
	
	
	/*if($item_category_id == 2 || $item_category_id == 3  || $item_category_id == 13  || $item_category_id == 14 || $po_id_expld[0]==0)
	{
		foreach($sql_wo as $row)
		{  
			$job_no_arr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$booking_no_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
			$woQty_arr[$row[csf('id')]]['wo_qunty']+=$row[csf('wo_qunty')];
		}
	}
	else
	{*/
	  	$job_no_arr=array();
		$booking_no_arr=array();
		$woQty_arr=array();
		foreach($sql_wo as $row)
		{  
			$job_no_arr[$row[csf('po_break_down_id')]]['job_no']=$row[csf('job_no')];
			if($item_category_id == 2 || $item_category_id == 3  || $item_category_id == 13  || $item_category_id == 14)
			{
				$booking_no_arr[$row[csf('po_break_down_id')]]['booking_no']=$row[csf('booking_no')]; 
			}
			else
			{
				$booking_no_arr[$row[csf('id')]][$row[csf('po_break_down_id')]]['booking_no']=$row[csf('booking_no')];
			}
			$woQty_arr[$row[csf('po_break_down_id')]]['wo_qunty']+=$row[csf('wo_qunty')]; 
		}
	//}
	$sql_styleRef_no = sql_select("select a.job_no,a.style_ref_no from wo_po_details_master a,wo_po_break_down b where  a.job_no=b.job_no_mst  and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id group by a.job_no,a.style_ref_no" );
	foreach ($sql_styleRef_no as $row)
  	{  
		$styleRef_no_arr[$row[csf('job_no')]]['style_ref_no']=$row[csf('style_ref_no')];
	}
	

	
	if($po_id_expld[0]>0)
	{
		
		$sql =	"select a.id as recv_id, a.recv_number_prefix_num, a.recv_number as system_no, to_char(a.insert_date,'YYYY') as year, a.booking_no, a.booking_id, a.supplier_id, a.store_id, a.source, a.currency_id, a.receive_date, a.challan_no, a.challan_date, a.receive_basis, a.item_category, sum(distinct y.mrr_amount) as mrr_amount, sum(c.quantity) as receive_qnty,b.order_rate, c.quantity*b.order_rate/c.quantity as rate, b.order_uom as uom, b.prod_id, e.item_group_id, e.item_description, c.po_breakdown_id, y.accepted_qty, y.wo_po_id,y.po_id,y.wo_po_no  
				from inv_bill_processing_mst x, inv_bill_processing_dtls y, inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master e 
				where x.id=y.mst_id and x.id=$bill_id and y.mst_id=$bill_id and x.company_id=$company_id and a.id=b.mst_id and b.id=c.trans_id and b.prod_id=e.id and c.prod_id=e.id and y.receive_id=a.id and y.prod_id=b.prod_id and y.po_id=c.po_breakdown_id and a.company_id=$company_id and b.item_category in ($item_category_id) and a.booking_id=$wo_id and a.receive_basis=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and x.status_active=1 and x.is_deleted=0 and y.status_active=1 and y.is_deleted=0 
				group by a.id, a.recv_number_prefix_num, a.recv_number, a.insert_date, a.booking_no, a.booking_id, a.supplier_id, a.store_id, a.source, a.currency_id, a.receive_date, a.challan_no, a.challan_date, a.receive_basis, a.item_category, b.order_uom, b.prod_id, e.item_group_id, e.item_description, c.po_breakdown_id, y.accepted_qty, y.wo_po_id,y.po_id,y.wo_po_no,b.order_rate,c.quantity";
				
				
					/*echo	$sql =	"select a.id as recv_id, a.recv_number_prefix_num, a.recv_number as system_no, to_char(a.insert_date,'YYYY') as year, a.booking_no, a.booking_id, a.supplier_id, a.store_id, a.source, a.currency_id, a.receive_date, a.challan_no, a.challan_date, a.receive_basis, a.item_category, y.mrr_amount as mrr_amount, sum(c.quantity) as receive_qnty, sum((c.quantity*b.order_rate)/c.quantity) as rate, b.order_uom as uom, b.prod_id, e.item_group_id, e.item_description, c.po_breakdown_id, y.accepted_qty, y.wo_po_id,y.po_id,y.wo_po_no  
				from inv_bill_processing_mst x, inv_bill_processing_dtls y, inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master e 
				where x.id=y.mst_id and x.id=$bill_id and y.mst_id=$bill_id and x.company_id=$company_id and a.id=b.mst_id and b.id=c.trans_id and b.prod_id=e.id and c.prod_id=e.id and y.receive_id=a.id and y.prod_id=b.prod_id and y.po_id=c.po_breakdown_id and a.company_id=$company_id and b.item_category in ($item_category_id) and a.booking_id=$wo_id and a.receive_basis=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and x.status_active=1 and x.is_deleted=0 and y.status_active=1 and y.is_deleted=0 
				group by a.id, a.recv_number_prefix_num, a.recv_number, a.insert_date, a.booking_no, a.booking_id, a.supplier_id, a.store_id, a.source, a.currency_id, a.receive_date, a.challan_no, a.challan_date, a.receive_basis, a.item_category, b.order_uom, b.prod_id, e.item_group_id, e.item_description, c.po_breakdown_id, y.accepted_qty, y.wo_po_id,y.po_id,y.wo_po_no, y.mrr_amount";*/
	}
	elseif($po_id_expld[0]==0)
	{
		$sql = "select a.id as recv_id, a.recv_number_prefix_num, a.recv_number as system_no, to_char(a.insert_date,'YYYY') as year, a.booking_no, a.booking_id, a.supplier_id, a.store_id, a.source, a.currency_id, a.receive_date, a.challan_no, a.challan_date, a.receive_basis, b.item_category, sum(y.mrr_amount) as mrr_amount, sum(b.order_qnty) as receive_qnty, avg(b.order_rate) as rate, b.order_uom as uom, b.prod_id, e.item_group_id, e.item_description , 0 as  po_breakdown_id, y.accepted_qty, y.wo_po_id,y.po_id,y.wo_po_no   
				from inv_bill_processing_mst x, inv_bill_processing_dtls y, inv_receive_master a, inv_transaction b, product_details_master e 
				where x.id=y.mst_id and x.id=$bill_id and y.mst_id=$bill_id and x.company_id=$company_id and a.id=b.mst_id  and b.prod_id=e.id and y.receive_id=a.id and y.prod_id=b.prod_id and a.company_id=$company_id and b.item_category in ($item_category_id) and a.booking_id=$wo_id and a.receive_basis=2  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0  and x.status_active=1 and x.is_deleted=0 and y.status_active=1 and y.is_deleted=0 
				group by a.id, a.recv_number_prefix_num, a.recv_number, a.insert_date, a.booking_no, a.booking_id, a.supplier_id, a.store_id, a.source, a.currency_id, a.receive_date, a.challan_no, a.challan_date, a.receive_basis, b.item_category, b.order_uom, b.prod_id, e.item_group_id, e.item_description,0, y.accepted_qty, y.wo_po_id,y.po_id,y.wo_po_no";
	}
	else
	{
		
		$sql = "select a.id as recv_id, a.recv_number_prefix_num, a.recv_number as system_no, to_char(a.insert_date,'YYYY') as year, a.booking_no, a.booking_id, a.supplier_id, a.store_id, a.source, a.currency_id, a.receive_date, a.challan_no, a.challan_date, a.receive_basis, b.item_category, sum(b.order_amount) as mrr_amount, sum(b.order_qnty) as receive_qnty, avg(b.order_rate) as rate, b.order_uom as uom, b.prod_id, e.item_group_id, e.item_description , 0 as  po_breakdown_id, y.accepted_qty, y.wo_po_id,y.po_id,y.wo_po_no from inv_bill_processing_mst x, inv_bill_processing_dtls y, inv_receive_master a, inv_transaction b, product_details_master e 
				where x.id=y.mst_id and x.id=$bill_id and y.mst_id=$bill_id and x.company_id=$company_id and a.id=b.mst_id  and b.prod_id=e.id and y.receive_id=a.id and y.prod_id=b.prod_id and a.company_id=$company_id and b.item_category in ($item_category_id) and a.booking_id=$wo_id and a.receive_basis=2  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and x.status_active=1 and x.is_deleted=0 and y.status_active=1 and y.is_deleted=0  
				group by a.id, a.recv_number_prefix_num, a.recv_number, a.insert_date, a.booking_no, a.booking_id, a.supplier_id, a.store_id, a.source, a.currency_id, a.receive_date, a.challan_no, a.challan_date, a.receive_basis, b.item_category, b.order_uom, b.prod_id, e.item_group_id, e.item_description,0, y.accepted_qty, y.wo_po_id,y.po_id,y.wo_po_no";
		
	}
	
	//echo $sql;
	//die;
	$result = sql_select($sql);
    
     		$i=1;
			foreach ($result as $row)
			{  
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
		         <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>"> 
                   <td width="30"><? echo $i; ?></td>
		            <td width="150" style="word-break:break-all;">
					<? 
					if($item_category_id == 2 || $item_category_id == 3  || $item_category_id == 13  || $item_category_id == 14)
					{
						echo $booking_no_arr[$row[csf('wo_po_id')]]['booking_no']; 
					}
					else
					{
						//echo $booking_no_arr[$row[csf('wo_po_id')]][$row[csf('po_id')]]['booking_no']; //old
						echo $row[csf('wo_po_no')]; 
					}
					?>
                    </td>
		            <td width="70" style="word-break:break-all;"><? echo $row[csf('receive_date')]; ?></td>
		            <td width="100" style="word-break:break-all;"><? echo $row[csf('system_no')]; ?></td>
		            <td width="60"><p><? echo $row[csf('challan_no')]; ?></p></td>
		            <td width="130" id="job<? echo $i; ?>"><p><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></p></td>
                                        
		            <td width="75"><p>
					<? 
					if($item_category_id == 2 || $item_category_id == 3  || $item_category_id == 13  || $item_category_id == 14)
					{
						echo $job_no_arr[$row[csf('wo_po_id')]]['job_no']; 
					}
					else
					{
						echo $job_no_arr[$row[csf('po_id')]]['job_no']; 
					}
					?></p>
                    </td>
		            <td width="100"><p>
					<? 
					if($item_category_id == 2 || $item_category_id == 3  || $item_category_id == 13  || $item_category_id == 14)
					{
						echo $styleRef_no_arr[$job_no_arr[$row[csf('wo_po_id')]]['job_no']]['style_ref_no']; 
					}
					else
					{
						echo $styleRef_no_arr[$job_no_arr[$row[csf('po_id')]]['job_no']]['style_ref_no']; 
					}
					?>
                    </p></td>
                    <td width="125"><p><? echo $item_category[$row[csf('item_category')]]; ?></p></td>
		            <td width="100" id="gsm<? echo $i; ?>"><p><? echo $itemGroup_arr[$row[csf('item_group_id')]]; ?></p></td>
		            <td width="100" id="dia<? echo $i; ?>"><p><? echo $row[csf('item_description')]; ?></p></td>
		            <td width="70" align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
		            <td width="70" align="right">
					<? 
					if($item_category_id == 2 || $item_category_id == 3  || $item_category_id == 13  || $item_category_id == 14)
					{
						echo $woQty_arr[$row[csf('wo_po_id')]]['wo_qunty']; 
					}
					else
					{
						echo $woQty_arr[$row[csf('po_id')]]['wo_qunty']; 
					}
					
					?>
                    </td>
		            <td width="120" align="right"><? echo $row[csf('receive_qnty')]; ?></td>
                    
                    <td width="80" align="right"><input type="text" value="<? echo number_format($row[csf('rate')],4); ?>" class="text_boxes_numeric" style="width:40px" id="rateID<? echo $i; ?>" name="rateID[]"/ readonly></td>
		           <?php /*?> <td width="90" align="right"><? echo number_format($row[csf('mrr_amount')],2,'.',''); ?></td><?php */?>
                   
				   <td width="90" align="right"><input type="text" value="<? echo $row[csf('accepted_qty')]; //if($row[csf('payment_over_recv')]==0){ echo $row[csf('full_recvqnty')]; } else{ echo "";}  ?>" class="text_boxes_numeric" style="width:70px" id="paymentOverrecv<? echo $i; ?>" name="paymentOverrecv[]" onKeyUp="fnc_chng_mrr_amnt(<? echo $i; ?>);"/></td>

                    <td width="50" align="right"><input type="text" value="<? echo number_format($row[csf('mrr_amount')],4,'.',''); ?>" class="text_boxes_numeric" style="width:70px" id="mrr_amnt_acpt<? echo $i; ?>" name="mrr_amnt_acpt[]" readonly/></td>

		            <td width="100" align="center" id="prodId<? echo $i; ?>"><? echo $currency[$row[csf('currency_id')]]; ?>
		                <input type="hidden" value="<? echo $row[csf('recv_id')]; ?>" id="recvId<? echo $i; ?>" name="recvId[]"/>
		                <input type="hidden" value="<? echo $row[csf('system_no')]; ?>" id="recvNo<? echo $i; ?>" name="recvNo[]"/>
                        <input type="hidden" value="<? //echo $row[csf('trims_dtls_id')]; ?>" id="trimsDtlsId<? echo $i; ?>" name="trimsDtlsId[]"/>
                        <input type="hidden" value="<? 
						if($item_category_id == 2 || $item_category_id == 3  || $item_category_id == 13  || $item_category_id == 14)
	  					{
	  						echo $booking_no_arr[$row[csf('booking_id')]]['booking_no']; 
	  					}
	  					else
	  					{
	  						//echo $booking_no_arr[$row[csf('wo_po_id')]][$row[csf('po_id')]]['booking_no']; //old
							echo $row[csf('wo_po_no')]; 
	  					}
						
						?>" id="woNumber<? echo $i; ?>" name="woNumber[]"/>
		                <input type="hidden" value="<? echo $row[csf('booking_id')]; ?>" id="woID<? echo $i; ?>" name="woID[]"/>
                        <input type="hidden" value="<? echo $row[csf('prod_id')]; ?>" id="prodIds<? echo $i; ?>" name="prodIds[]"/>
                        <input type="hidden" value="<? echo $row[csf('po_breakdown_id')]; ?>" id="po_id<? echo $i; ?>" name="po_id[]"/>
                        <input type="hidden" value="<? echo $row[csf('mrr_amount')];//number_format($row[csf('mrr_amount')],2,'.',''); ?>" id="mrrAmount<? echo $i; ?>" name="mrrAmount[]"/>
                        <input type="hidden" value="<? echo $row[csf('currency_id')]; ?>" id="currencyIDs<? echo $i; ?>" name="currencyIDs[]"/>
		               
		            </td>
                    <td>
                    	<input type="button" value="-" class="text_boxes_numeric" style="width:30px;text-align:center;" id="decrease_<? echo $i; ?>" onClick="javascript:fn_deletebreak_down_tr('<? echo $i; ?>')" />
                    </td>
		        </tr>  
			<?
			$i++;
			}
	exit();
}

if($action=="print_bill_processing_action")
{
	extract($_REQUEST);
	//echo $data;
	$ex_data=explode('*',$data);
	$company_id=$ex_data[0];
	$update_id=$ex_data[1];
	$txt_system_no=$ex_data[2];
	$hidden_party_id=$ex_data[3];
	$txt_reference_no=$ex_data[4];
	$hidden_reference_id=$ex_data[5];
	$recvIDs=chop($ex_data[6],",");
	$txt_party=$ex_data[7];
	$txt_bill_no=$ex_data[8];
	$txt_bill_date=$ex_data[9];
	$report_title=$ex_data[10];
	$buyer_id=$ex_data[11];
	//print_r($ex_data);
	if($recvIDs!=""){ $recvId_cond="and a.id in ($recvIDs)";} else {  $recvId_cond="";}
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");

	//$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library",'master_tble_id','image_location');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$itemGroup_arr = return_library_array("select id, item_name from lib_item_group","id","item_name");
	$sql_mst="Select id, reqn_number,location_id,reqn_date from pro_fab_reqn_for_batch_mst where company_id=$ex_data[0] and id='$ex_data[1]' and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql_mst);
	
	$wo_po_ID="";
	$sql_wo_id = sql_select("select wo_po_id from inv_bill_processing_dtls where mst_id=$update_id and  status_active=1 and is_deleted=0 " );
	foreach ($sql_wo_id as $row)
  	{  
		$wo_po_ID.=$row[csf('wo_po_id')].',';
	}
	$wo_po_ID=chop($wo_po_ID,",");
	//echo $wo_po_ID;
	
		$sql_styleRef_no = sql_select("select a.job_no,a.style_ref_no,b.po_number from wo_po_details_master a,wo_po_break_down b where  a.job_no=b.job_no_mst  and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id group by a.job_no,a.style_ref_no,b.po_number" );
	foreach ($sql_styleRef_no as $row)
  	{  
		$styleRef_no_arr[$row[csf('job_no')]]['style_ref_no']=$row[csf('style_ref_no')];
		$po_no_no_arr[$row[csf('job_no')]]['po_number']=$row[csf('po_number')];
	}
	// echo "<pre>"; print_r($po_no_no_arr); die;
	
	?>
    <div style="width:1360px;">
    <table width="100%" cellpadding="0" cellspacing="0" >
        <tr>
            <td width="70" align="right"> 
            	<img  src='../../<? echo $imge_arr[$ex_data[0]]; ?>' height='100%' width='100%' />
            </td>
            <td>
                <table width="800" cellspacing="0" align="center">
                    <tr class="form_caption">
                    	<td align="center" style="font-size:18px"><strong ><? echo $company_library[$ex_data[0]]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td align="center" style="font-size:14px"><strong> <? //echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td align="center" style="font-size:14px"><? echo show_company($ex_data[0],'',''); ?> </td>  
                    </tr>
                    <tr class="form_caption">
                    	<td align="center" style="font-size:16px"><u><strong><? echo $report_title; ?></strong></u></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table width="100%" cellspacing="0" align="" border="0">
        <tr>
            <td width="170"><strong>Company Name: </strong></td> <td style="width:220px;"><? echo $company_library[$ex_data[0]]; ?></td>
            <td width="130"><strong>Buyer Name:</strong></td><td width="150px"> <? echo $buyer_library[$buyer_id]; ?></td>
            <td width="130"><strong>Party Name:</strong></td><td width="400px"> <? echo $txt_party; ?></td>
            <td width="130"><strong>Bill Name:</strong></td><td width="150px"> <? echo $txt_bill_no; ?></td>
            <td width="130"><strong>Bill Date:</strong></td><td width="150px"> <? echo change_date_format($txt_bill_date); ?></td>
            
            <td width="130"><strong>System No:</strong></td> <td width="150"><? echo $txt_system_no; ?></td>
        </tr>
    </table>
    <br>
	<div style="width:100%;">
		<table align="left" cellspacing="0" width="1755"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" style="font-size:13px">
            
                <th width="30">SL</th>
                <th width="150">WO</th>
                <th width="70">Receive Date</th>
                <th width="100">MRR/System ID</th>
                <th width="60">Challan No</th> 
                <th width="80">Receive Basis </th>
                <th width="120">Job No</th>
                <th width="100">Style No</th>
                <th width="125">Category</th>
                <th width="100">PO No</th>
                <th width="100">Item Group</th>
                <th width="100">Item Des.</th>
                <th width="70">UOM</th>
                <th width="70">W/O Qty.</th>
                <th width="120">Full Receive Qty. </th>
                <th width="70">Accepted Qty</th>  
                <th width="80">Rate (TK)</th>
                <th width="80">MRR Amount (TK)</th>
                <th width="80">Rate</th>
                <th width="90">	MRR Amount</th>
                <th>WO Currency</th>
                <!--<th>Yarn Lot/  Count</th>-->
            </thead>
            <tbody>
            <?
	
				/* $sql = "SELECT a.id as recv_id, a.recv_number_prefix_num, a.recv_number, to_char(a.insert_date,'YYYY') as year, a.booking_no, a.booking_id, a.supplier_id, a.store_id, a.source, a.currency_id, a.receive_date, a.challan_no, a.challan_date, a.receive_basis,a.exchange_rate, b.item_category, sum(distinct y.mrr_amount) as mrr_amount, sum(b.order_qnty) as receive_qnty, avg(b.order_rate) as rate, b.order_uom as uom, b.prod_id, e.item_group_id, e.item_description , 0 as  po_breakdown_id, y.accepted_qty, y.wo_po_id,y.po_id,y.wo_po_no   
				from inv_bill_processing_mst x, inv_bill_processing_dtls y, inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master e 
				where x.id=y.mst_id and x.id=$update_id and y.mst_id=$update_id and x.company_id=$company_id and a.id=b.mst_id  and b.prod_id=e.id and y.receive_id=a.id and y.prod_id=b.prod_id  and a.company_id=$company_id and a.receive_basis=2  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and x.status_active=1 and x.is_deleted=0 and y.status_active=1 and y.is_deleted=0 and b.id=c.trans_id and c.prod_id=e.id and y.po_id=c.po_breakdown_id  and c.status_active=1 and c.is_deleted=0  
				group by a.id, a.recv_number_prefix_num, a.recv_number, a.insert_date, a.booking_no, a.booking_id, a.supplier_id, a.store_id, a.source, a.currency_id, a.receive_date, a.challan_no, a.challan_date, a.receive_basis,a.exchange_rate, b.item_category, b.order_uom, b.prod_id, e.item_group_id, e.item_description,0, y.accepted_qty, y.wo_po_id,y.po_id,y.wo_po_no order by y.wo_po_id "; */

				$sql = "SELECT a.id as recv_id, a.recv_number_prefix_num, a.recv_number, to_char(a.insert_date,'YYYY') as year, a.booking_no, a.booking_id, a.supplier_id, a.store_id, a.source, a.currency_id, a.receive_date, a.challan_no, a.challan_date, a.receive_basis,a.exchange_rate, b.item_category, sum(distinct y.mrr_amount) as mrr_amount, sum(b.order_qnty) as receive_qnty, avg(b.order_rate) as rate, b.order_uom as uom, b.prod_id, e.item_group_id, e.item_description , 0 as  po_breakdown_id, y.accepted_qty, y.wo_po_id,y.po_id,y.wo_po_no   
				from inv_bill_processing_mst x, inv_receive_master a, inv_transaction b, product_details_master e, inv_bill_processing_dtls y
				left join order_wise_pro_details c on y.po_id=c.po_breakdown_id and y.prod_id=c.prod_id 
				where x.id=y.mst_id and x.id=$update_id and y.mst_id=$update_id and x.company_id=$company_id and a.id=b.mst_id  and b.prod_id=e.id and y.receive_id=a.id and y.prod_id=b.prod_id and a.company_id=$company_id and a.receive_basis=2  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and x.status_active=1 and x.is_deleted=0 and y.status_active=1 and y.is_deleted=0
				group by a.id, a.recv_number_prefix_num, a.recv_number, a.insert_date, a.booking_no, a.booking_id, a.supplier_id, a.store_id, a.source, a.currency_id, a.receive_date, a.challan_no, a.challan_date, a.receive_basis,a.exchange_rate, b.item_category, b.order_uom, b.prod_id, e.item_group_id, e.item_description,0, y.accepted_qty, y.wo_po_id,y.po_id,y.wo_po_no order by y.wo_po_id ";
		
				// echo $sql; die;
		  		/*$sql = "select a.id as recv_id, a.recv_number_prefix_num, a.recv_number, to_char(a.insert_date,'YYYY') as year, a.booking_no, a.booking_id, a.supplier_id, a.store_id, a.source, a.currency_id, a.receive_date, a.challan_no, a.challan_date, a.receive_basis,a.exchange_rate, b.item_category, sum(y.mrr_amount) as mrr_amount, sum(b.order_qnty) as receive_qnty, avg(b.order_rate) as rate, b.order_uom as uom, b.prod_id, e.item_group_id, e.item_description , 0 as  po_breakdown_id, y.accepted_qty, y.wo_po_id,y.po_id   
				from inv_bill_processing_mst x, inv_bill_processing_dtls y, inv_receive_master a, inv_transaction b, product_details_master e 
				where x.id=y.mst_id and x.id=$update_id and y.mst_id=$update_id and x.company_id=$company_id and a.id=b.mst_id  and b.prod_id=e.id and y.receive_id=a.id and y.prod_id=b.prod_id  and a.company_id=$company_id and a.receive_basis=2  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and x.status_active=1 and x.is_deleted=0 and y.status_active=1 and y.is_deleted=0 
				group by a.id, a.recv_number_prefix_num, a.recv_number, a.insert_date, a.booking_no, a.booking_id, a.supplier_id, a.store_id, a.source, a.currency_id, a.receive_date, a.challan_no, a.challan_date, a.receive_basis,a.exchange_rate, b.item_category, b.order_uom, b.prod_id, e.item_group_id, e.item_description,0, y.accepted_qty, y.wo_po_id,y.po_id order by y.wo_po_id "; *///old - 30-12-17
	
				/*$sql = "select a.id as recv_id, a.recv_number_prefix_num, a.recv_number, to_char(a.insert_date,'YYYY') as year, a.booking_no, a.booking_id, a.supplier_id, a.store_id, a.source, a.currency_id, a.receive_date, a.challan_no, a.challan_date, a.receive_basis,a.exchange_rate, b.item_category, sum(b.order_amount) as mrr_amount, sum(c.quantity) as receive_qnty, avg(b.order_rate) as rate, b.order_uom as uom, b.prod_id, e.item_group_id, e.item_description , 0 as  po_breakdown_id, y.accepted_qty, y.wo_po_id,y.po_id   
				from inv_bill_processing_mst x, inv_bill_processing_dtls y, inv_receive_master a, inv_transaction b,order_wise_pro_details c, product_details_master e 
				where x.id=y.mst_id and x.id=$update_id and y.mst_id=$update_id and x.company_id=$company_id and a.id=b.mst_id  and b.prod_id=e.id and y.receive_id=a.id and y.prod_id=b.prod_id and b.id=c.trans_id  and c.prod_id=e.id  and y.po_id=c.po_breakdown_id and c.status_active=1 and c.is_deleted=0 and a.company_id=$company_id and a.receive_basis=2  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 
				group by a.id, a.recv_number_prefix_num, a.recv_number, a.insert_date, a.booking_no, a.booking_id, a.supplier_id, a.store_id, a.source, a.currency_id, a.receive_date, a.challan_no, a.challan_date, a.receive_basis,a.exchange_rate, b.item_category, b.order_uom, b.prod_id, e.item_group_id, e.item_description,0, y.accepted_qty, y.wo_po_id,y.po_id order by y.wo_po_id "; //old*/
			
				
	  		$result_report = sql_select($sql);
			$grnd_balance=0; $grnd_balance_usd=0;
     		$i=1;
			foreach ($result_report as $row)
			{  
			
				$item_cat_id =$row[csf('item_category')];
				
				if($item_cat_id == 2 || $item_cat_id == 3  || $item_cat_id == 13  || $item_cat_id == 14)
				{
					
					$sql_wo= sql_select("select a.id as po_break_down_id, a.booking_no as booking_no, b.job_no as job_no ,b.wo_qnty as wo_qunty 
					from wo_booking_mst a ,wo_booking_dtls b 
					where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category = $item_cat_id and a.supplier_id='$hidden_party_id' and a.status_active=1 and a.is_deleted=0 
					union all
					 select a.id as po_break_down_id, a.booking_no, null as job_no ,NULL as wo_qunty 
					 from wo_non_ord_samp_booking_mst a 
					 where a.company_id=$company_id and a.item_category = $item_cat_id and a.supplier_id='$hidden_party_id' and a.status_active=1 and a.is_deleted=0");
				
				}
				else if($item_cat_id == 4)
				{
				  	$sql_wo= sql_select("select a.id, a.booking_no as booking_no, b.job_no as job_no ,b.wo_qnty as wo_qunty,b.po_break_down_id as po_break_down_id  
					from wo_booking_mst a ,wo_booking_dtls b 
					where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category = $item_cat_id and a.supplier_id='$hidden_party_id' and a.status_active=1 and a.is_deleted=0 
					union all  
					select a.id, a.wo_number as booking_no, null as job_no,NULL as wo_qunty,b.po_breakdown_id as po_break_down_id  
					from wo_non_order_info_mst a,wo_non_order_info_dtls b 
					where a.id=b.mst_id and a.company_name=$company_id and b.item_category_id = $item_cat_id and a.supplier_id='$hidden_party_id' and a.status_active=1 and a.is_deleted=0 ");
								
				}
				else
				{
					
					$sql_wo= sql_select("select a.id, a.wo_number as booking_no,b.supplier_order_quantity as wo_qunty,b.po_breakdown_id as po_break_down_id   
					from wo_non_order_info_mst a,wo_non_order_info_dtls b where a.id=b.mst_id and a.company_name=$company_id and a.supplier_id='$hidden_party_id' and b.item_category_id=$item_cat_id and a.status_active=1 and a.is_deleted=0 ");
					
				}
				// echo "<pre>"; print_r($sql_wo); die;
				foreach($sql_wo as $rows)
			    {  
			  		$job_no_arr[$rows[csf('po_break_down_id')]]['job_no']=$rows[csf('job_no')];
					$booking_no_arr[$rows[csf('po_break_down_id')]]['booking_no']=$rows[csf('booking_no')];
					$woQty_arr[$rows[csf('po_break_down_id')]]['wo_qunty']=$rows[csf('wo_qunty')];
			  	}
				// echo "<pre>"; print_r($job_no_arr[$rows[csf('po_break_down_id')]]['job_no']); die;
				// echo "<pre>"; print_r($job_no_arr['25991']['job_no']); die;
			
				?>
				 <tr bgcolor="<? echo $bgcolor; ?>" style="font-size:13px">
					<td width="30"><? echo $i; ?></td>
		            <td width="150" style="word-break:break-all;">
					<? 
					if($item_cat_id == 2 || $item_cat_id == 3  || $item_cat_id == 13  || $item_cat_id == 14)
					{
						echo $booking_no_arr[$row[csf('wo_po_id')]]['booking_no'];
					}
					else
					{
						echo $row[csf('wo_po_no')];
						//echo $booking_no_arr[$row[csf('po_id')]]['booking_no'];
					}
					?>
                    </td>
		            <td width="70" style="word-break:break-all;"><? echo $row[csf('receive_date')]; ?></td>
		            <td width="100" style="word-break:break-all;"><? echo $row[csf('recv_number')]; ?></td>
		            <td width="60"><p><? echo $row[csf('challan_no')]; ?></p></td>
		            <td width="80" id="job<? echo $i; ?>"><p><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></p></td>
		            <td width="120"><p>
					<? 
					if($item_cat_id == 2 || $item_cat_id == 3  || $item_cat_id == 13  || $item_cat_id == 14)
					{
						echo $job_no_arr[$row[csf('wo_po_id')]]['job_no'];
					}
					else
					{
						echo $job_no_arr[$row[csf('po_id')]]['job_no'];
					}
					?>
                    </p></td>
		            <td width="100"><p>
					<? 
					if($item_cat_id == 2 || $item_cat_id == 3  || $item_cat_id == 13  || $item_cat_id == 14)
					{
						echo $styleRef_no_arr[$job_no_arr[$row[csf('wo_po_id')]]['job_no']]['style_ref_no']; 
					}
					else
					{
						echo $styleRef_no_arr[$job_no_arr[$row[csf('po_id')]]['job_no']]['style_ref_no']; 
					}
					?>
                    </p></td>
                    <td width="100"><p><? echo $item_category[$row[csf('item_category')]]; ?></p></td>
                    <td width="100"><p><? echo $po_no_no_arr[$job_no_arr[$row[csf('wo_po_id')]]['job_no']]['po_number']; ?></p></td>
                    <!-- <td width="100"><p><? //echo $po_no_no_arr['FAL-17-00019']['po_number']; ?></p></td> -->
		            <td width="100" id="gsm<? echo $i; ?>"><p><? echo $itemGroup_arr[$row[csf('item_group_id')]]; ?></p></td>
		            <td width="100" id="dia<? echo $i; ?>"><p><? echo $row[csf('item_description')]; ?></p></td>
		            <td width="70"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
		            <td width="70" align="right">
					<?
					if($item_cat_id == 2 || $item_cat_id == 3  || $item_cat_id == 13  || $item_cat_id == 14)
					{
						echo $woQty_arr[$row[csf('wo_po_id')]]['wo_qunty'];// $row[csf('wo_qnty')]; 
					}
					else
					{
						echo $woQty_arr[$row[csf('po_id')]]['wo_qunty'];// $row[csf('wo_qnty')]; 
					} 
					
					?>
                    </td>
		            <td width="120" align="right"><? echo $row[csf('receive_qnty')]; ?></td>
                    <td width="70" align="center"><? echo $row[csf('accepted_qty')]; //if($row[csf('payment_over_recv')]==0){ echo $row[csf('full_recvqnty')]; } else{ echo "";} ?></td>
                    
                    <td width="80" align="center"><? if($row[csf('currency_id')]==2){ $rate=$row[csf('rate')] * $row[csf('exchange_rate')]; echo  number_format($rate,4,'.','');} else{ echo  number_format($row[csf('rate')],4,'.',''); } ?></td>
		            
                    <td width="90" align="center">
					<? 
					if($row[csf('currency_id')]==2)
					{ 
						//$mrrAmount=$row[csf('receive_qnty')] * $rate; echo  number_format($mrrAmount,4,'.','');
						$mrrAmount=$row[csf('accepted_qty')] * $rate; echo  number_format($mrrAmount,4,'.','');
					}
					else{ 
						//$mrrAmount=$row[csf('receive_qnty')]* $row[csf('rate')]; echo number_format($mrrAmount,4,'.',''); 
						$mrrAmount=$row[csf('accepted_qty')]* $row[csf('rate')]; echo number_format($mrrAmount,4,'.',''); 
					} 
					?></td>
                   
		            <td width="65" id="programNo<? echo $i; ?>"><p><? if($row[csf('currency_id')]==2){ echo number_format($row[csf('rate')],4,'.','');} else {} ?></p></td>
		            <td width="60" id="bookingNo<? echo $i; ?>"><p><? 
					if($row[csf('currency_id')]==2) 
					{ 
						//$mrr_amount_usd=$row[csf('accepted_qty')]*$row[csf('rate')]; // old -30-12-2017
						$mrr_amount_usd=$row[csf('mrr_amount')]; // modified at 30-12-2017
						
					} 
					else 
					{} 
					if($mrr_amount_usd!='')
					{
						echo number_format($mrr_amount_usd,4,'.','');
					} 
					else 
					{ echo ''; } ?></p>
                    
                    </td>
		            <td id="prodId<? echo $i; ?>"><? echo $currency[$row[csf('currency_id')]]; ?></td>
				</tr>
				<?

				$grnd_balance+=$mrrAmount;
				if($row[csf('currency_id')]==2)
				{
					$grnd_balance_usd+=$mrr_amount_usd;
				}
				$i++;
			}
			?>
            </tbody>
            <tfoot bgcolor="#dddddd" style="font-size:13px">
            	<tr>
                	<td colspan="17" align="right"><strong>Total :</strong></td>
                    <td><strong><? echo number_format($grnd_balance,4); ?></strong></td>
                    <td></td>
                    <td><strong><? echo number_format($grnd_balance_usd,4); ?></strong></td>
                     <td></td>
                </tr>
            </tfoot>
        </table>
        </div>
        <br>  
        
    
  </div>
  	<table width="1760">
	    <tr>
		    <td>
			  	 <div style="width:100%; margin-top:20px; float:right;">
					<table align="right" cellspacing="0" width="430"  border="1" rules="all" class="rpt_table" style="margin-top:20px;" >
			            <thead bgcolor="#dddddd" style="font-size:13px">
			                <th width="30">SL</th>
			                <th width="150">Currency</th>
			                <th width="70">Bill Amount</th>
			                <th width="100">Adjustment</th>
			                <th width="60">Net Amount</th> 
			            </thead>           
			            <tbody>
			            <?
				            $sql_adj = "select z.adj_currency_id, z.amount,z.adj_amount,z.adj_type_id,z.net_amount
							from inv_bill_processing_mst x, inv_bill_processing_dtls y,inv_bill_processing_adjustment z,inv_receive_master a, inv_transaction b, product_details_master e 
							where  x.id=y.mst_id and x.id=z.bill_mst_id and y.mst_id=z.bill_mst_id and y.currency_id=z.adj_currency_id and x.id=$update_id and y.mst_id=$update_id and x.company_id=$company_id and a.id=b.mst_id  and b.prod_id=e.id and y.receive_id=a.id and y.prod_id=b.prod_id and a.company_id=$company_id and a.receive_basis=2  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 
							group by  z.adj_currency_id , z.amount,z.adj_amount,z.adj_type_id,z.net_amount"; // 6-3-2018
							
						/*	$sql_adj = "select d.adj_currency_id, d.amount,d.adj_amount,d.adj_type_id,d.net_amount   
				from inv_bill_processing_mst x, inv_bill_processing_dtls y, inv_receive_master a, inv_transaction
 b, order_wise_pro_details c, product_details_master e,inv_bill_processing_adjustment d 
				where x.id=y.mst_id and x.id=$update_id and y.mst_id=$update_id and x.company_id=$company_id and a.id=b.mst_id and b.id=c
.trans_id and b.prod_id=e.id and c.prod_id=e.id and y.receive_id=a.id and y.prod_id=b.prod_id and y.po_id
=c.po_breakdown_id and a.company_id=$company_id  and a.receive_basis
=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active
=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and x.status_active=1 and x.is_deleted
=0 and y.status_active=1 and y.is_deleted=0  and x.id=d.bill_mst_id and y.currency_id=d.adj_currency_id 
				group by d.adj_currency_id, d.amount,d.adj_amount,d.adj_type_id,d.net_amount";*/
							
							
					  		$result_report_adj = sql_select($sql_adj);
							//$grnd_balance=0; $grnd_balance_usd=0;
				     		$incre=1;
							foreach ($result_report_adj as $row)
							{ 
							?>
			            	 <tr bgcolor="<? echo $bgcolor; ?>" style="font-size:13px">
								<td width="30"><? echo $incre; ?></td>
					            <td width="100" align="center"><? echo  $currency[$row[csf('adj_currency_id')]]; ?></td>
			                    <td width="100" align="right"><? echo  $row[csf('amount')]; ?></td>
			                    <td width="100" align="right"><? 
								if($row[csf('adj_amount')]>0)
								{ 
									if($row[csf('adj_type_id')]==1)
									{ $adj_type='-';}
									else{ $adj_type='+';} 
									echo $adj_type . $row[csf('adj_amount')];
								}; ?></td>
			                    <td width="100" align="right"><? echo  $row[csf('net_amount')]; ?></td>
			                 </tr>
			              <?
						  $incre++;
							}
			             ?>   
			            </tbody>
			            </table>
					 <?
			           // echo signature_table(93, $ex_data[0], "1060px");
			         ?>
			    </div>
		    </table>
		    </td>
	    </tr>
    <?
	
	if(count($result_report_adj)==0)
	{
		?>
		 <h3 align="left">In Words <? echo  $currency[$row[csf('currency_id')]]; ?>: &nbsp;
		 <? 
			if($row[csf('currency_id')]==1){
				echo number_to_words($grnd_balance,"Taka", "Paisa");
			}
			else
			{
				echo number_to_words($grnd_balance_usd,"USD", "CENTS");
			}
		 ?>
		 </h3>
		<?
	} 
	//number_to_words("55555555250", "USD", "CENTS");
		
    foreach ($result_report_adj as $row)
    { 
	  	if($row[csf('adj_currency_id')]==2)
	  	{
	  		 $currencyFraction="CENTS";
	  	}
	  	else
	  	{
	  		$currencyFraction="Paisa";
	  	}
		?>
		 <h3 align="left">In Words <? echo  $currency[$row[csf('adj_currency_id')]]; ?>: &nbsp;<? echo number_to_words($row[csf('net_amount')],$currency[$row[csf('adj_currency_id')]], $currencyFraction);?></h3>
		<?
	}
	echo signature_table(206,$company_id,1760,"",0,$user_lib_name[$inserted_by]);
	exit();
}

if($action=="wo_po_popup")
{
  	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);
?>   
<script>
	var selected_id = new Array;
	var selected_name = new Array;
	var selected_no = new Array;
	var item_cat = new Array;

	function check_all_data() {
		var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
		tbl_row_count = tbl_row_count - 1;
		for( var i = 1; i <= tbl_row_count; i++ ) {
			var onclickString = $('#tr_' + i).attr('onclick');
			var paramArr = onclickString.split("'");
			var functionParam = paramArr[1];
			js_set_value( functionParam );
			
		}
	}

	function toggle( x, origColor ) {
		var newColor = 'yellow';
		if ( x.style ) { 
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}

	function js_set_value(strCon)
	{
		// alert(strCon); return;
 		// $("#hidden_sys_number").val(strCon); // mrr number
		// parent.emailwindow.hide();
		var splitSTR = strCon.split("_");
		var str = splitSTR[0];
		var selectID = splitSTR[1];
		var selectDESC = splitSTR[2];
		var selectCAT = splitSTR[3];
		// alert(str); return;
		toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
		
		if( jQuery.inArray( str, selected_no ) == -1 ) {
			selected_id.push( selectID );
			selected_name.push( selectDESC );
			selected_no.push(str);	
			item_cat.push(selectCAT);	
		}
		else
		{
			for( var i = 0; i < selected_no.length; i++ ) {
				if( selected_no[i] == str ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i, 1 );
			selected_no.splice( i, 1 );
		}
		var id = ''; var name = ''; var job = ''; var num=''; var cat='';
		for( var i = 0; i < selected_no.length; i++ ) {
			id += selected_id[i] + ',';
			name += selected_name[i] + ',';
			num += selected_no[i] + ',';
			cat += item_cat[i] + ',';
		}
		id 		= id.substr( 0, id.length - 1 );
		name 	= name.substr( 0, name.length - 1 );
		num 	= num.substr( 0, num.length - 1 );
		cat 	= cat.substr( 0, cat.length - 1 );
		$('#txt_selected_id').val( id );
		$('#txt_selected_no').val( name );
		$('#txt_selected_sl').val( num );
		$('#txt_selected_cat').val( cat );
	}
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="780" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>                	 
                    <th class="must_entry_caption">Company</th>
                    <th>Party</th>
                    <th>Item Category</th>
                   <!-- <th >PO/WO</th>-->
                    <th id="search_by_td_up" >Please Enter WO</th>
                    <th >Year</th>
                    <th>
						<input type="reset" name="re_button" id="re_button" value="Reset" style="width:90px" class="formbutton"  />
						<input type='hidden' id='txt_selected_id' />
						<input type='hidden' id='txt_selected_no' />
						<input type='hidden' id='txt_selected_sl' />
						<input type='hidden' id='txt_selected_cat' />
					</th>           
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center">
                        <?
						 echo create_drop_down( "cbo_company_id", 172, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "",1 );
						?>
                    </td>
                    <td align="center">
                        <?
							$sql_party= "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name"; 

						 echo create_drop_down( "cbo_wo_party_id", 172, $sql_party,"id,supplier_name", 1, "-- Select Party --", $party, "",1 );
						?>
                    </td>
                    
                    <td align="center">
                   		<?
						//24,25,31,71,72,73,74,75,76,77,78,79
						//function create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name, $additionalClass, $additionalAttributes ) 
						  	if($buyer_id>0)
							{
								 echo create_drop_down( "cbo_itemcategory", 120,$item_category,"",1, "-- Select --",4 , "","","4", "","","24,25,31,71,72,73,74,75,76,77,78,79" ); 
							}
							else
							{
								 echo create_drop_down( "cbo_itemcategory", 120,$item_category,"",1, "-- Select --",$row[csf('item_category_id')] , "","","", "","","24,25,31,71,72,73,74,75,76,77,78,79" ); 
							}
                           
                        ?>
                    </td>
                   <!-- <td align="center">	
                    	<?
                       		/*$search_by_arr=array(1=>"PO",2=>"WO");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 120, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );*/
						?>
                    </td>-->
                    <td align="center">
                    	<input type="text" style="width:120px" class="text_boxes"  name="txt_reference_id" id="txt_reference_id" />
                    </td>


                    <td align="center">
						<?
							$selected_year=date("Y"); 
							echo create_drop_down( "cbo_job_year_id", 120, $year,"", 1, "--Year--", $selected_year, "",0,"","");
						?>
                    </td>	

					<input type="hidden" value="<? echo $buyer_id; ?>" id="hidden_buyer_id" name="hidden_buyer_id">

                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_itemcategory').value+'_'+document.getElementById('cbo_wo_party_id').value+'_'+document.getElementById('txt_reference_id').value+'_'+document.getElementById('cbo_job_year_id').value+'_'+document.getElementById('hidden_buyer_id').value, 'create_wo_po_search_list_view', 'search_div', 'bill_processing_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:90px;" />				
                    </td>
                
	              </tr>
	              <tr>                  
	              	<td>
	  					  <input type="hidden" id="hidden_sys_number" value="hidden_sys_number" />
	                  </td>
	              </tr> 

            </tbody>
         </tr>         
        </table> 
        <br>   
        <div align="center" valign="top" id="search_div"> </div> 
        </form>
   </div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_wo_po_search_list_view")
{
	$ex_data = explode("_",$data);
	//print_r($ex_data); die;
	$company 		= trim($ex_data[0]);
	$item_cat_ref 	= trim($ex_data[1]);
	$cbo_party_id 	= trim($ex_data[2]);
	$txt_refe 		= trim($ex_data[3]);
	$cbo_year 		= trim($ex_data[4]);
	$hidden_buyer_id = trim($ex_data[5]);
	if($hidden_buyer_id>0){ $buyer_cond_1= "and a.buyer_id=$hidden_buyer_id"; }else{$buyer_cond_1="";}
	if($hidden_buyer_id>0){ $buyer_cond_2= "and b.buyer_id=$hidden_buyer_id"; }else{$buyer_cond_2="";}
	// if($item_cat_ref<1) { echo "Please Select Item Category.";die;}
	
	$year_cond="";
	if($cbo_year>0)
	{
		if($db_type==0)
		{
			$year_cond=" and year(a.insert_date)='$cbo_year'";
		}
		else
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
		}
	}
	//echo $year_cond;die;
	$booking_cond=$nonOrder_booking_cond="";
	if($txt_refe!="")
	{
		$booking_cond=" and a.booking_no_prefix_num='$txt_refe'";
		$nonOrder_booking_cond=" and a.wo_number_prefix_num='$txt_refe'";
	}
	if($item_cat_ref == "" || $item_cat_ref == 0)
	{
		$sql= "SELECT a.id, a.wo_number_prefix_num as booking_no_prefix_num, a.wo_number as booking_no,  a.wo_date as booking_date, a.supplier_id, b.item_category_id as item_category, a.currency_id, a.source
		from wo_non_order_info_mst a,wo_non_order_info_dtls b where a.id=b.mst_id and a.company_name=$company  and a.supplier_id='$cbo_party_id' and a.status_active=1 and a.is_deleted=0 and a.source <> 0 $year_cond $nonOrder_booking_cond group by a.id, a.wo_number_prefix_num, a.wo_number,  a.wo_date, a.supplier_id, b.item_category_id, a.currency_id, a.source";
	}
	else if($item_cat_ref == 2 || $item_cat_ref == 3  || $item_cat_ref == 13  || $item_cat_ref == 14)
	{
		
		$sql= "SELECT a.id, a.booking_no_prefix_num, a.booking_no,  a.booking_date, a.supplier_id, a.item_category, a.currency_id, a.source
		from wo_booking_mst a where a.company_id=$company and a.item_category = $item_cat_ref and a.supplier_id='$cbo_party_id' and a.status_active=1 and a.is_deleted=0 $year_cond $booking_cond group by a.id, a.booking_no_prefix_num, a.booking_no,  a.booking_date, a.supplier_id, a.item_category, a.currency_id, a.source
		union all
		SELECT a.id, a.booking_no_prefix_num, a.booking_no,  a.booking_date, a.supplier_id, a.item_category, a.currency_id, a.source
		from wo_non_ord_samp_booking_mst a where a.company_id=$company and a.item_category = $item_cat_ref and a.supplier_id='$cbo_party_id' and a.status_active=1 and a.is_deleted=0 $year_cond $booking_cond group by a.id, a.booking_no_prefix_num, a.booking_no,  a.booking_date, a.supplier_id, a.item_category, a.currency_id, a.source";
	}
	else if($item_cat_ref == 4)
	{
	  	$sql= "SELECT a.id,a.booking_no_prefix_num as booking_no_prefix_num, a.booking_no as booking_no, a.booking_date as booking_date,a.supplier_id as supplier_id, a.item_category, a.currency_id, a.source
 		from wo_booking_mst a 
 		where a.company_id=$company and a.item_category = $item_cat_ref and a.supplier_id='$cbo_party_id' $buyer_cond_1 and a.status_active=1 and a.is_deleted=0 $year_cond $booking_cond group by a.id,a.booking_no_prefix_num, a.booking_no, a.booking_date,a.supplier_id, a.item_category, a.currency_id, a.source
 		union all  
 		SELECT a.id, a.wo_number_prefix_num as booking_no_prefix_num, a.wo_number as booking_no, a.wo_date as booking_date,TO_NUMBER(a.supplier_id) as supplier_id, a.item_category, a.currency_id, a.source
 		from wo_non_order_info_mst a,wo_non_order_info_dtls b 
		where a.id=b.mst_id and a.company_name=$company and b.item_category_id = $item_cat_ref and a.supplier_id='$cbo_party_id' $buyer_cond_2 and a.status_active=1 and a.is_deleted=0 $year_cond $nonOrder_booking_cond group by a.id, a.wo_number_prefix_num, a.wo_number, a.wo_date, a.supplier_id, a.item_category, a.currency_id, a.source";
	}
	else
	{
		
		$sql= "SELECT a.id, a.wo_number_prefix_num as booking_no_prefix_num, a.wo_number as booking_no,  a.wo_date as booking_date, a.supplier_id, b.item_category_id as item_category, a.currency_id, a.source
		from wo_non_order_info_mst a,wo_non_order_info_dtls b where a.id=b.mst_id and a.company_name=$company and b.item_category_id = $item_cat_ref and a.supplier_id='$cbo_party_id' and a.status_active=1 and a.is_deleted=0 $year_cond $nonOrder_booking_cond group by a.id, a.wo_number_prefix_num, a.wo_number,  a.wo_date, a.supplier_id, b.item_category_id, a.currency_id, a.source";
		// echo $sql;die;
	}
	// echo $sql; die;
	$supplier_arr=return_library_array( "SELECT id, supplier_name from lib_supplier",'id','supplier_name');
	// $order_noorder_arr=array(0=>"With Order",1=>"Without Order");
	$arr=array(2=>$supplier_arr,3=>$currency,4=>$source,5=>$item_category);
	echo  create_list_view("list_view", "WO No, Date, Supplier, Currency, Source,Item Category","100,120,140,120,120","800","260",0, $sql , "js_set_value", "id,booking_no,item_category", "", 1, "0,0,supplier_id,currency_id,source,item_category", $arr, "booking_no_prefix_num,booking_date,supplier_id,currency_id,source,item_category", "","","0,0,0,0,0,0","",1) ;
	exit();
}

if($action=="short_list_view")
{
	extract($_REQUEST);
	$datas=explode('_',$data);
	$bill_id=$datas[0];
	$company_id=$datas[1];
	$party_id=$datas[2];
	$recv_id=$datas[3];
	
	?>
	<fieldset style="width:570px; margin-top:10px">
		<legend>List View</legend>
		<table width="570" cellspacing="2" cellpadding="0" border="0">
			<tr>
				<td colspan="3">
				<div id="list_view_div">
					<?
						if($db_type==0)
						{
							$sql="SELECT b.wo_po_no, group_concat(b.receive_id) as receive_id,group_concat(b.receive_no) as receive_no, c.currency_id,a.id, a.company_id, a.party_id,b.wo_po_id, sum(distinct b.mrr_amount) as mrr_amount,group_concat(b.po_id) as po_id , d.item_category 
							from inv_bill_processing_mst a, inv_bill_processing_dtls b, inv_receive_master c, inv_transaction d 
							where a.id=b.mst_id and b.receive_id=c.id and c.id=d.mst_id and a.company_id=$company_id and a.id=$bill_id and a.party_id=$party_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.prod_id=d.prod_id 
							group by b.wo_po_no, c.currency_id, a.id,a.company_id, a.party_id, b.wo_po_id, d.item_category";
						}
						else
						{
							/*$sql="select  b.wo_po_no, LISTAGG(b.receive_id, ',') WITHIN GROUP (ORDER BY b.receive_id) as receive_id, LISTAGG(b.receive_no, ',') WITHIN GROUP (ORDER BY b.receive_no) as receive_no, c.currency_id, a.id, a.company_id, a.party_id, b.wo_po_id, sum(b.mrr_amount) as mrr_amount, b.po_id, d.item_category 
							from inv_bill_processing_mst a, inv_bill_processing_dtls b, inv_receive_master c, inv_transaction d 
							where a.id=b.mst_id and b.receive_id=c.id and c.id=d.mst_id and a.company_id=$company_id and a.id=$bill_id and a.party_id=$party_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.prod_id=d.prod_id 
							group by b.wo_po_no, c.currency_id, a.id, a.company_id, a.party_id, b.wo_po_id, b.po_id, d.item_category";*/
						
							$sql="SELECT b.wo_po_no, LISTAGG(b.receive_id, ',') WITHIN GROUP (ORDER BY b.receive_id) as receive_id, LISTAGG(b.receive_no, ',') WITHIN GROUP (ORDER BY b.receive_no) as receive_no, c.currency_id, a.id, a.company_id, a.party_id, b.wo_po_id, sum(distinct b.mrr_amount) as mrr_amount, LISTAGG(b.po_id, ',') WITHIN GROUP (ORDER BY b.po_id) as po_id, d.item_category 
							from inv_bill_processing_mst a, inv_bill_processing_dtls b, inv_receive_master c, inv_transaction d 
							where a.id=b.mst_id and b.receive_id=c.id and c.id=d.mst_id and a.company_id=$company_id and a.id=$bill_id and a.party_id=$party_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.prod_id=d.prod_id 
							group by b.wo_po_no, c.currency_id, a.id, a.company_id, a.party_id, b.wo_po_id, d.item_category";
						}
						// echo $sql; die;
						$sql_bill="SELECT a.id, a.company_id, a.party_id, b.wo_po_id,b.wo_po_no,  sum(b.mrr_amount) as mrr_amount   
						from inv_bill_processing_mst a, inv_bill_processing_dtls b
						where a.id=b.mst_id and a.company_id=$company_id and a.id=$bill_id and a.party_id=$party_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 
						group by a.id, a.company_id, a.party_id, b.wo_po_id,b.wo_po_no";
						$sql_bill_mrr=sql_select($sql_bill);
						foreach($sql_bill_mrr as $row)
						{
							$arr_mrr_amount_bill[$row[csf("id")]][$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("wo_po_id")]][$row[csf("wo_po_no")]]["mrr_amount"]=$row[csf("mrr_amount")];
						}
						
						$poNumber = return_library_array("SELECT id, po_number from wo_po_break_down","id","po_number");
						?>
						<table cellpadding="0" width="730" cellspacing="0" border="1"  class="rpt_table" rules="all">
							<thead>
							<th width="25">SL</th>
							<th width="200">WO No</th>
							<th width="200">Receive Ref</th>
							<th width="60">Receive Amount</th>
							<th width="100">Currency</th>
							<th>PO No</th>
						</thead>
						</table>
					<div style="width:730px; max-height:250px; overflow-y:scroll" align="left">
						<table cellpadding="0" cellspacing="0" width="710" border="1" rules="all" class="rpt_table" id="tblBody_id">
						<?
						$sql_quary_list_view=sql_select($sql); 
						$sl=1; 	
						foreach($sql_quary_list_view as $row)
						{
							$rcv_uniqNo=implode(",",array_unique(explode(",",$row[csf("receive_no")])));
							$poID=implode(",",array_unique(explode(",",$row[csf("po_id")])));
							$rcvID=implode(",",array_unique(explode(",",$row[csf("receive_id")])));
							
							$pos="";
							$poNumbers=array_unique(explode(",",$row[csf("po_id")]));
							foreach($poNumbers as $po)
							{
								$pos.=$poNumber[$po].',';
							}
							// echo "<pre>"; print_r($poNumbers); die;
							?> 
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="populate_list_view_send('<? echo $row[csf("id")].'_'.$row[csf("company_id")].'_'.$row[csf("party_id")].'_'.$row[csf("wo_po_no")].'_'.$row[csf("wo_po_id")].'_'.$rcvID.'_'.$row[csf("item_category")].'_'.$poID ?>')">
								<td width="25"><? echo $sl; ?></td>
								<td width="200"><? echo $row[csf("wo_po_no")]; ?></td>
								<td width="200"><p><? echo $rcv_uniqNo; ?></p></td>
								<td width="60" align="right"><? echo $arr_mrr_amount_bill[$row[csf("id")]][$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("wo_po_id")]][$row[csf("wo_po_no")]]["mrr_amount"];//$row[csf("mrr_amount")]; ?></td>
								<td width="100" align="center"><? echo $currency[$row[csf("currency_id")]]; ?></td>
								<td><p><? echo  chop($pos,','); ?></p></td>
							</tr>
							<?
							$sl++;	
						}
						?>
						</table>
					</div>
						<?
					/* $arr=array (3=>$currency);
						echo create_list_view ( "list_view", "WO No,Receive Ref,Receive Amount,Currency,PO ID", "150,150,100,100","650","220",0, $sql, "populate_list_view_send", "id,company_id,party_id,wo_po_no,wo_po_id,receive_id,item_category,po_id", "", 1, "0,0,0,currency_id,0", $arr , "wo_po_no,receive_no,mrr_amount,currency_id,po_id", "requires/bill_processing_controller", 'setFilterGrid("list_view",-1);','0,0,5,0,0');*/
						
						?>
				</div>
				</td>
			</tr>
		</table>
	</fieldset>	
	<?
}

if($action=="adjustment_popup")
{
	echo load_html_head_contents("Adjustment Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	$datas=explode('_',$data);
	$supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
	$itemGroup_arr = return_library_array("select id, item_name from lib_item_group","id","item_name");
	$chkAdj_arr = return_library_array("select id, bill_mst_id from inv_bill_processing_adjustment","id","bill_mst_id");
	
	$sql_adj_bill_id="";
	$sql_adj_id= sql_select("select bill_mst_id from inv_bill_processing_adjustment where bill_mst_id=$bill_id group by bill_mst_id");
	foreach ($sql_adj_id as $row)
	{  
		$sql_adj_bill_id=$row[csf('bill_mst_id')];
	}
	if($sql_adj_bill_id>0)
	{
		//$sql =	"select a.currency_id,b.amount as mrr_amount,b.adj_amount,b.net_amount from inv_bill_processing_mst x,inv_bill_processing_dtls y,inv_receive_master a,inv_bill_processing_adjustment b where x.id=y.mst_id and y.receive_id = a.id and x.id=$bill_id and y.mst_id=$bill_id and x.company_id=$company_id and x.id=b.bill_mst_id and x.status_active=1 and x.is_deleted=0 group by  a.currency_id,b.amount,b.adj_amount,b.net_amount";
		
		$sql =	"select y.currency_id,sum(y.mrr_amount) as mrr_amount,b.adj_amount,b.net_amount,b.adj_type_id from inv_bill_processing_mst x,inv_bill_processing_dtls y,inv_bill_processing_adjustment b where x.id=y.mst_id and x.id=b.bill_mst_id and y.currency_id=b.adj_currency_id and x.id=$bill_id and y.mst_id=$bill_id and x.company_id=$company_id and x.status_active=1 and x.is_deleted=0 group by  y.currency_id,b.amount,b.adj_amount,b.net_amount,b.adj_type_id";// //6-3-2018
		
		/*$sql =	"select y.currency_id,sum(y.mrr_amount) as mrr_amount,d.adj_amount,d.net_amount,d.adj_type_id   
				from inv_bill_processing_mst x, inv_bill_processing_dtls y, inv_receive_master a, inv_transaction
 b, order_wise_pro_details c, product_details_master e,inv_bill_processing_adjustment d 
				where x.id=y.mst_id and x.id=$bill_id and y.mst_id=$bill_id and x.company_id=$company_id and a.id=b.mst_id and b.id=c
.trans_id and b.prod_id=e.id and c.prod_id=e.id and y.receive_id=a.id and y.prod_id=b.prod_id and y.po_id
=c.po_breakdown_id and a.company_id=$company_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active
=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and x.status_active=1 and x.is_deleted
=0 and y.status_active=1 and y.is_deleted=0  and x.id=d.bill_mst_id and y.currency_id=d.adj_currency_id 
				group by y.currency_id,d.amount,d.adj_amount,d.net_amount,d.adj_type_id ";*/ //new
		
	}
	else
	{
 		$sql =	"select  a.currency_id,sum(y.mrr_amount) as mrr_amount  from inv_bill_processing_mst x,inv_bill_processing_dtls y,inv_receive_master a  where x.id=y.mst_id and y.receive_id=a.id and  x.id=$bill_id and y.mst_id=$bill_id and x.company_id=$company_id  and x.status_active=1 and x.is_deleted=0 group by  a.currency_id"; 	
	}
	//echo $sql;
?> 

<script>

	function js_set_value(data)
	{
		//$('#party_id').val(data);
		parent.emailwindow.hide();
	}

function calculate_amount(row_id)
{
	//alert(row_id); //return;
	if($('#cbo_adj_arr_id'+row_id).val()*1==1)
	{
		var net_amount=($('#main_amnt'+row_id).val()*1)-($('#adj_amnt'+row_id).val()*1);
		$('#adj_netamnt'+row_id).val(net_amount);
	}
	else
	{
		var net_amount=($('#main_amnt'+row_id).val()*1)+($('#adj_amnt'+row_id).val()*1);
		$('#adj_netamnt'+row_id).val(net_amount);
	}	
}
function save_adj_fnc(operation)
{
	var row_num=$('#adjs_tbl tbody tr').length;
	var hidden_bill_mst_id=$('#hidden_bill_mst_id').val();
	var dataString=""; var j=0;
	for (var i=1; i<=row_num; i++)
	{
		var main_amnt=$('#main_amnt'+i).val();
		var adj_amnt=$('#adj_amnt'+i).val();
		var cbo_adj_arr_id=$('#cbo_adj_arr_id'+i).val();
		var adj_netamnt=$('#adj_netamnt'+i).val();
		var txt_currency_id=$('#txt_currency_id'+i).val();
		
		j++;
		dataString+='&main_amnt' + j + '=' + main_amnt + '&adj_amnt' + j + '=' + adj_amnt + '&cbo_adj_arr_id' + j + '=' + cbo_adj_arr_id + '&adj_netamnt' + j + '=' + adj_netamnt + '&txt_currency_id' + j + '=' + txt_currency_id;
	}
	//alert(dataString); return;
	var data="action=save_update_adjustment&operation="+operation+'&tot_row='+j+'&hidden_bill_mst_id='+hidden_bill_mst_id+dataString;
	//alert(data);
	//freeze_window(operation);
		
	http.open("POST","bill_processing_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange =fnc_bill_processing_Reply_info;
}
function fnc_bill_processing_Reply_info()
{
	if(http.readyState == 4) 
	{
		//release_freezing();return;
		var permission='<? echo $_SESSION['page_permission']; ?>';
		var response=trim(http.responseText).split('**');
		show_msg(response[0]);
		
		if((response[0]==0 || response[0]==1))
		{
			document.getElementById('hidden_bill_mst_id').value = response[1];
			set_button_status(1, permission, 'save_adj_fnc',1);
			if(response[0]==0)
			{
				alert('Save Successfully');
			}
			else
			{
				alert('Update Successfully');
			}
		}
	}
}
</script>
 </head>	
  <body>
    <div align="center" style="width:330px" >
    <fieldset style="width:520px"> 
        <form name="adjuestment_popup_1"  id="adjuestment_popup_1">
            <table cellpadding="0" width="530" cellspacing="0" border="1" id="adjs_tbl_top" class="rpt_table" rules="all">
	            <thead>
	                <th width="30">SL</th>
	                <th width="80">Currency</th>
	                <th width="80">Amount</th>
                    <th width="80">Adjustment</th>
	                <th width="120">Discount/Upcharge</th>
                    <th>Net Amount</th>
	            </thead>
	         </table>
             <div style="width:530px; max-height:250px; overflow-y:scroll" align="left">
                <table cellpadding="0" cellspacing="0" width="510" border="1" id="adjs_tbl" rules="all" class="rpt_table">
                    <tbody>
                   <? 
                    $result = sql_select($sql);
		       		$i=1;
		  			foreach ($result as $row)
		  			{  
		  				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		  			?>
		         		<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>"> 
                   			<td width="30"><? echo $i; ?></td>
				            <td width="80" style="word-break:break-all;">
							<? echo $currency[$row[csf('currency_id')]]; ?>
							<input type="hidden" value="<? echo $row[csf('currency_id')]; ?>" class="text_boxes_numeric" style="width:65px; text-align:center;" id="txt_currency_id<? echo $i; ?>" name="txt_currency_id[]" readonly/>                          
                            </td>
				            <td width="80"> <input type="text" value="<? echo $row[csf('mrr_amount')]; ?>" class="text_boxes_numeric" style="width:65px" id="main_amnt<? echo $i; ?>" name="main_amnt[]" onKeyUp="calculate_amount(<? echo $i; ?>);" readonly/></td>
				            <td width="80"><input type="text" value="<? echo $row[csf('adj_amount')]; ?>" class="text_boxes_numeric" style="width:65px" id="adj_amnt<? echo $i; ?>" name="adj_amnt[]" onKeyUp="calculate_amount(<? echo $i; ?>);"/></td>
                            <td width="120"><?
							 echo create_drop_down( "cbo_adj_arr_id".$i, 115, $bill_disupcharge,"", "", "", $row[csf('adj_type_id')], "calculate_amount($i);" );
							?></td>
                            <td><input type="text" value="<? if($sql_adj_bill_id>0){echo $row[csf('net_amount')];}else {echo $row[csf('mrr_amount')];}  ?>" class="text_boxes_numeric" style="width:95px" id="adj_netamnt<? echo $i; ?>" name="adj_netamnt[]" readonly/>
                            </td>
                    	</tr>
                        
                     <?
					 $i++;
					}
					?>
                      <input type="hidden" value="<? echo $bill_id;  ?>" class="text_boxes_numeric" style="width:95px" id="hidden_bill_mst_id" name="hidden_bill_mst_id" readonly/>
                    </tbody>
                </table>
                <br/>
               	<!--<input  name="save_adjusment_btn" id="save_adjusment_btn" class="formbutton" value="Save" style="width:100px; margin-left:200px;" onClick="save_adj_fnc();" type="button">-->
                <div style="margin-left:100px;">
                <?
				$chkAdj_arrs = return_library_array("select id, bill_mst_id from inv_bill_processing_adjustment","id","bill_mst_id");
				if(in_array($bill_id,$chkAdj_arrs))
				{
					echo load_submit_buttons($_SESSION['page_permission'],"save_adj_fnc",1,"","");
				}
				else
				{
					echo load_submit_buttons($_SESSION['page_permission'],"save_adj_fnc",0,"","");
				}
				?>
				</div>
            </div>
        </form>
    </fieldset>
    </div>
  </body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if ($action=="save_update_adjustment")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array_dtls="id, bill_mst_id,amount,adj_amount,adj_type_id,net_amount,adj_currency_id,inserted_by, insert_date";
		$adj_id = return_next_id( "id", "inv_bill_processing_adjustment", 1 );

		for($j=1;$j<=$tot_row;$j++)
		{ 
			$main_amnt="main_amnt".$j;
			$adj_amnt="adj_amnt".$j;
			$cbo_adj_arr_id="cbo_adj_arr_id".$j;
			$adj_netamnt="adj_netamnt".$j;
			$txt_currency_id="txt_currency_id".$j;
			
			
			//if($$receiveBasis==1) $program_booking_pi_no=$$programNo; else $program_booking_pi_no=$$bookingNo;

			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$adj_id.",".$hidden_bill_mst_id.",".$$main_amnt.",'".$$adj_amnt."','".$$cbo_adj_arr_id."','".$$adj_netamnt."','".$$txt_currency_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$adj_id = $adj_id+1;
		}
		//echo "10**insert into inv_bill_processing_mst (".$field_array.") values ".$data_array;die;
		//echo "10**insert into inv_bill_processing_adjustment (".$field_array_dtls.") values ".$data_array_dtls;die;
		//$rID=sql_insert("inv_bill_processing_mst",$field_array,$data_array,0);
		$rID=sql_insert("inv_bill_processing_adjustment",$field_array_dtls,$data_array_dtls,1);
		//oci_rollback($con);
		//echo "10**".$rID."&&".$rID2;die;

		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "0**".$hidden_bill_mst_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);  
				echo "0**".$hidden_bill_mst_id;
			}
			else
			{
				oci_rollback($con);
				echo "5**0";
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

		$field_array_dtls="id, bill_mst_id,amount,adj_amount,adj_type_id,net_amount,adj_currency_id,inserted_by, insert_date";
		$adj_id = return_next_id( "id", "inv_bill_processing_adjustment", 1 );

		for($j=1;$j<=$tot_row;$j++)
		{ 
			$main_amnt="main_amnt".$j;
			$adj_amnt="adj_amnt".$j;
			$cbo_adj_arr_id="cbo_adj_arr_id".$j;
			$adj_netamnt="adj_netamnt".$j;
			$txt_currency_id="txt_currency_id".$j;
			

			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$adj_id.",".$hidden_bill_mst_id.",".$$main_amnt.",'".$$adj_amnt."','".$$cbo_adj_arr_id."','".$$adj_netamnt."','".$$txt_currency_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$adj_id = $adj_id+1;
		}
		//echo "10**insert into inv_bill_processing_mst (".$field_array.") values ".$data_array;die;
		//echo "10**insert into inv_bill_processing_adjustment (".$field_array_dtls.") values ".$data_array_dtls;die;
		//$rID=sql_insert("inv_bill_processing_mst",$field_array,$data_array,0);
		$query3 = execute_query("DELETE FROM inv_bill_processing_adjustment WHERE bill_mst_id=$hidden_bill_mst_id");
		$rID=sql_insert("inv_bill_processing_adjustment",$field_array_dtls,$data_array_dtls,1);
		//oci_rollback($con);
		//echo "10**".$rID."&&".$rID2;die;
			
			if($db_type==0)
			{
				if($query3 && $rID )
				{
					mysql_query("COMMIT");  
					echo "1**".str_replace("'", '', $hidden_bill_mst_id);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "6**".str_replace("'", '', $hidden_bill_mst_id);
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($query3 && $rID)
				{
					oci_commit($con);  
					echo "1**".str_replace("'", '', $hidden_bill_mst_id);
				}
				else
				{
					oci_rollback($con);
					echo "6**".str_replace("'", '', $hidden_bill_mst_id)."**1";
				}
			}
			disconnect($con);
			die;
	}
}
	
?>
