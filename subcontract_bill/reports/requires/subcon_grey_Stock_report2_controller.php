<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name");
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
$order_arr=return_library_array( "select id, order_no from  subcon_ord_dtls", "id", "order_no");
$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");

/*if ($action=="load_drop_down_buyer")
{ 
	echo create_drop_down( "cbo_party_id", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "--Select Party--", $selected, "" );
	exit();   	 
}*/

if($action=="buyer_no_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script type="text/javascript">
		function js_set_value(id)
		{ 
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
	<input type="hidden" id="selected_id" name="selected_id" /> 
	<?
	$buyer = str_replace("'","",$buyer_name);
	$year = str_replace("'","",$year);
	$buyer = str_replace("'","",$buyer_name);
	$job_no = str_replace("'","",$job_no);

    if($db_type==0) 
	{
		$year_field="year(a.insert_date) as year"; 
		 if(trim($year)!=0) $year_field_cond="and YEAR(a.insert_date)=$year";  else $year_field_cond="";
	}
    else if($db_type==2) 
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
		 if(trim($year)!=0) $year_field_cond=" and to_char(a.insert_date,'YYYY')=$year"; else $year_field_cond="";
	}
    else 
	{
		$year_field="";
		$year_field_cond="";
	}
	
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num=$job_no";
	//echo $buyer;die;
	
	if(trim($buyer)==0) $sub_buyer_name_cond=""; else $sub_buyer_name_cond=" and a.party_id=$buyer";
	
	 $sql="select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$company_id'  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name";
	
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	?>
	<table width="300" border="1" rules="all" class="rpt_table">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="100">Buyer</th>
            </tr>
        </thead>
    </table>
    <div style="max-height:340px; overflow:auto;">
        <table id="table_body2" width="300" border="1" rules="all" class="rpt_table">
			<? 
			$rows=sql_select($sql);
            $i=1;
            foreach($rows as $data)
            {
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value('<? echo $data[csf('id')].'_'.$data[csf('buyer_name')]; ?>')" style="cursor:pointer;">
                    <td width="30"><? echo $i; ?></td>
                    <td width="100"><p><? echo $data[csf('buyer_name')]; ?></p></td>
				</tr>
				<? $i++; 
			} ?>
		</table>
	</div>
	<script> setFilterGrid("table_body2",-1); </script>
	<?
	exit();
}

if($action=="order_no_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script type="text/javascript">
		function js_set_value(id)
		{ 
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
	<input type="hidden" id="selected_id" name="selected_id" /> 
	<?
	$buyer = str_replace("'","",$buyer_name);
	$year = str_replace("'","",$year);
	$buyer = str_replace("'","",$buyer_name);
	$job_no = str_replace("'","",$job_no);

    if($db_type==0) 
	{
		$year_field="year(a.insert_date) as year"; 
		 if(trim($year)!=0) $year_field_cond="and YEAR(a.insert_date)=$year";  else $year_field_cond="";
	}
    else if($db_type==2) 
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
		 if(trim($year)!=0) $year_field_cond=" and to_char(a.insert_date,'YYYY')=$year"; else $year_field_cond="";
	}
    else 
	{
		$year_field="";
		$year_field_cond="";
	}
	
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num=$job_no";
	//echo $buyer;die;
	
	if(trim($buyer)==0) $sub_buyer_name_cond=""; else $sub_buyer_name_cond=" and a.party_id=$buyer";
	
	$sql="select distinct b.id, b.job_no_mst as job_no ,a.party_id as buyer_name, b.order_no as po_number, a.job_no_prefix_num as job_prefix, $year_field from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where b.job_no_mst=a.subcon_job and a.id=c.mst_id and b.id=c.order_id and a.company_id=$company_id $sub_buyer_name_cond $job_no_cond $year_field_cond and a.is_deleted =0 group by b.id, a.party_id, b.job_no_mst, b.order_no, a.job_no_prefix_num, a.company_id, a.insert_date";	
	
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	?>
	<table width="500" border="1" rules="all" class="rpt_table">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="100">Order Number</th>
                <th width="50">Job no</th>
                <th width="80">Buyer</th>
                <th width="40">Year</th>
            </tr>
        </thead>
    </table>
    <div style="max-height:340px; overflow:auto;">
        <table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
			<? 
			$rows=sql_select($sql);
            $i=1;
            foreach($rows as $data)
            {
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value('<? echo $data[csf('id')].'_'.$data[csf('po_number')]; ?>')" style="cursor:pointer;">
                    <td width="30"><? echo $i; ?></td>
                    <td width="100"><p><? echo $data[csf('po_number')]; ?></p></td>
                    <td width="50"><p><? echo $data[csf('job_prefix')]; ?></p></td>
                    <td width="80"><p><? echo $buyer[$data[csf('buyer_name')]]; ?></p></td>
                    <td width="40" align="center"><p><? echo $data[csf('year')]; ?></p></td>
				</tr>
				<? $i++; 
			} ?>
		</table>
	</div>
	<script> setFilterGrid("table_body2",-1); </script>
	<?
	exit();
}

if($action=="challan_no_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script type="text/javascript">
		function js_set_value(id)
		{ 
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
	<input type="hidden" id="selected_id" name="selected_id" /> 
	<?
	$buyer = str_replace("'","",$buyer_name);
	$year = str_replace("'","",$year);
	$buyer = str_replace("'","",$buyer_name);
    if($db_type==0) 
	{
		$year_field="year(a.insert_date) as year"; 
		 if(trim($year)!=0) $year_field_cond="and YEAR(a.insert_date)=$year";  else $year_field_cond="";
	}
    else if($db_type==2) 
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
		 if(trim($year)!=0) $year_field_cond=" and to_char(a.insert_date,'YYYY')=$year"; else $year_field_cond="";
	}
    else 
	{
		$year_field="";
		$year_field_cond="";
	}
	
	if(trim($buyer)==0) $sub_buyer_name_cond=""; else $sub_buyer_name_cond=" and a.party_id=$buyer";
	
	//$sql="select distinct b.id, b.job_no_mst as job_no ,a.party_id as buyer_name, b.order_no as po_number, a.job_no_prefix_num as job_prefix, $year_field from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where b.job_no_mst=a.subcon_job and a.id=c.mst_id and b.id=c.order_id and a.company_id=$company_id $sub_buyer_name_cond $job_no_cond $year_field_cond and a.is_deleted =0 group by b.id, a.party_id, b.job_no_mst, b.order_no, a.job_no_prefix_num, a.company_id, a.insert_date";	
	$order_noArr=return_library_array("select id,order_no from  subcon_ord_dtls where status_active =1 and is_deleted=0","id","order_no");
	  $sql= "select a.id,a.chalan_no,a.subcon_date, a.party_id,b.order_id, b.item_category_id,$year_field $sub_buyer_name_cond from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.status_active=1 and  a.is_deleted=0 and a.trans_type in (1,2,3)  group by a.id,a.chalan_no,a.subcon_date,a.party_id,b.order_id, b.item_category_id,a.insert_date";
	
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	?>
	<table width="500" border="1" rules="all" class="rpt_table">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="50">chalan Number</th>
                <th width="50">Order Number</th>
                <th width="50">Item Category</th>
                <th width="80">Buyer</th>
                <th width="40">Year</th>
            </tr>
        </thead>
    </table>
    <div style="max-height:340px; overflow:auto;">
        <table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
			<? 
			$rows=sql_select($sql);
            $i=1;
            foreach($rows as $data)
            {
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value('<? echo $data[csf('id')].'_'.$data[csf('chalan_no')]; ?>')" style="cursor:pointer;">
                    <td width="30"><? echo $i; ?></td>
                    <td width="50"><p><? echo $data[csf('chalan_no')]; ?></p></td>
                     <td width="50"><p><? echo $order_noArr[$data[csf('order_id')]]; ?></p></td>
                    <td width="50"><p><? echo $item_category[$data[csf('item_category_id')]]; ?></p></td>
                    <td width="80"><p><? echo $buyer[$data[csf('party_id')]]; ?></p></td>
                    <td width="40" align="center"><p><? echo $data[csf('year')]; ?></p></td>
				</tr>
				<? $i++; 
			} ?>
		</table>
	</div>
	<script> setFilterGrid("table_body2",-1); </script>
	<?
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_challan_no=str_replace("'","",$txt_challan_no);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	
	ob_start();
	?>
	<div align="center">
	 
		<table cellpadding="0" cellspacing="0" width="1330">
			<tr  class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="8" style="font-size:20px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="8" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="8" style="font-size:12px">
					<? if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") echo "From ".change_date_format(str_replace("'","",$txt_date_from),'dd-mm-yyyy')." To ".change_date_format(str_replace("'","",$txt_date_to),'dd-mm-yyyy')."" ;?>
				</td>
			</tr>
		</table>
		<table width="1327" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
			<thead>
                <tr>
                    <th width="40">SL</th>
                    <th width="150">Customer</th>
                    <th width="110">Item Category</th>
                    <th width="110">Order No</th>
                    <th width="110">Challan No</th>
                    <th width="210">Items Description</th>
                    <th width="110">UOM</th>
                    <th width="100">Receive Qty</th>                            
                    <th width="100">Issue Qty</th>
                    <th width="100">Return Qty</th>
                    <th>Balance</th>
                </tr>
			</thead>
		</table>
	<div style="max-height:300px; overflow-y:scroll; width:1330px" id="scroll_body">
		<table width="1310" border="1" class="rpt_table" rules="all" id="table_body">
		<?
			if($txt_order_no!='') $order_cond="and order_no like '%$txt_order_no%'";
			else $order_cond="";
			
				$sql_po="select id , order_no from  subcon_ord_dtls where status_active =1 and is_deleted=0 $order_cond";
				$sql_po_result=sql_select($sql_po);
				foreach ($sql_po_result as $row)
				{
					$po_idArr[$row[csf('id')]]=$row[csf('id')];
					$order_noArr[$row[csf('id')]]=$row[csf('order_no')];
				}
			
				$po_cond_for_in=where_con_using_array($po_idArr,0,"b.order_id"); 
				
			//$order_noArr=return_library_array("select id,order_no from  subcon_ord_dtls where status_active =1 and is_deleted=0","id","order_no");
			
			if(str_replace("'","",$cbo_party_id)==0) $party_rec_cond=""; else  $party_rec_cond=" and a.party_id=$cbo_party_id";
			if(str_replace("'","",$cbo_party_id)==0) $party_bill_cond=""; else  $party_bill_cond=" and a.party_id=$cbo_party_id";
			if(str_replace("'","",$cbo_party_id)==0) $party_lib_cond=""; else  $party_lib_cond=" and buy.id=$cbo_party_id";

			if(str_replace("'","",$cbo_item_category)==0) $item_category_cond=""; else  $item_category_cond=" and b.item_category_id=$cbo_item_category";
			
			$orderID = $order_noArr[$txt_order_no] ;
			//echo $orderID;die;
			if ($txt_order_no!=''){
				if ($orderID!='') $order_no_cond=" and b.order_id like '%$orderID%'"; else $order_no_cond=" and b.order_id like '$orderID'";
			}
			
			
			if ($txt_challan_no!='') $challan_no_cond=" and a.chalan_no like '%$txt_challan_no%'"; else $challan_no_cond="";
			
			if($db_type==0)
			{
				if( $date_from==0 && $date_to==0 ) $receive_date_cond=""; else $receive_date_cond= " and a.subcon_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
			}
			else if($db_type==2)
			{
				if( $date_from==0 && $date_to==0 ) $receive_date_cond=""; else $receive_date_cond= " and a.subcon_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
			}
			
			if($date_from=="") $bill_date=""; else $bill_date= " and a.bill_date <".$txt_date_from."";
			if($date_from=="") $receive_date_cond=""; else $receive_cond= " and a.subcon_date <".$txt_date_from."";
			
			 $sql_iss_ret= "select a.id, a.trans_type,a.chalan_no, b.order_id,b.subcon_roll,b.color_id,b.color_id, b.item_category_id, b.material_description, b.subcon_uom, sum(b.quantity) as quantity from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.is_deleted=0 and a.trans_type in (2,3) $po_cond_for_in $receive_date_cond $challan_no_cond $item_category_cond  group by  a.id,a.chalan_no, a.trans_type, b.order_id,b.subcon_roll,b.color_id,b.color_id, b.item_category_id, b.material_description, b.subcon_uom ";
			 //group by a.id, a.trans_type, b.order_id, b.item_category_id, b.material_description, b.subcon_uom
			$sql_iss_ret_arr=sql_select($sql_iss_ret); $iss_ret_qty_arr=array();
			foreach ($sql_iss_ret_arr as $row)
			{
				//if($row[csf('trans_type')]==2)
				$iss_ret_qty_arr[$row[csf('trans_type')]][$row[csf('chalan_no')]][$row[csf('color_id')]][$row[csf('order_id')]][$row[csf('item_category_id')]][$row[csf('material_description')]][$row[csf('subcon_uom')]]+=$row[csf('quantity')];
			}
			//print_r($iss_ret_qty_arr);
			unset($sql_iss_ret_arr);
			
			$partyArr=return_library_array("select buy.id,buy.buyer_name from  lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id","id","buyer_name");
			//and b.order_id=$orderID
		     $sql= "select a.id, a.chalan_no, a.subcon_date, a.party_id, b.order_id, b.color_id,b.item_category_id, b.material_description, b.subcon_uom, sum(b.quantity) as quantity from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.is_deleted=0 and a.trans_type=1  $party_rec_cond $receive_date_cond $challan_no_cond $item_category_cond $po_cond_for_in group by a.id, a.chalan_no, a.subcon_date, a.party_id, b.order_id,b.color_id, b.item_category_id, b.material_description, b.subcon_uom";
		
			
			$sql_result=sql_select($sql);
			$i=1;
			foreach ($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$issue_qty=0; $return_qty=0; $receive_qty=0; $balance_qty=0;
				$receive_qty=$row[csf("quantity")];
				$issue_qty=$iss_ret_qty_arr[2][$row[csf('chalan_no')]][$row[csf('color_id')]][$row[csf('order_id')]][$row[csf('item_category_id')]][$row[csf('material_description')]][$row[csf('subcon_uom')]];
				//echo $issue_qty.'<br>';
				$return_qty=$iss_ret_qty_arr[3][$row[csf('chalan_no')]][$row[csf('color_id')]][$row[csf('order_id')]][$row[csf('item_category_id')]][$row[csf('material_description')]][$row[csf('subcon_uom')]];
				$balance_qty=($receive_qty+$return_qty)-$issue_qty
				
				?>
				<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="40" ><p><? echo $i; ?></p></td>
                    <td width="150" ><p><? echo $partyArr[$row[csf('party_id')]]; ?></p></td>
                    <td width="110"><p><? echo $item_category[$row[csf('item_category_id')]];?></p></td>
                    <td width="110"><p><? echo $order_noArr[$row[csf('order_id')]]?></p></td>
                    <td width="110"><p><? echo $row[csf('chalan_no')]?></p></td>
                    <td width="210"><p><? echo $row[csf('material_description')]; ?></p></td>
                    <td width="110"><p><? echo $unit_of_measurement[$row[csf('subcon_uom')]]; ?></p></td>
                    <td width="100" align="right" ><p><? echo number_format($receive_qty,2,'.',','); ?></p></td>
                    <td width="100" align="right" ><p><? echo number_format($issue_qty,2,'.',','); ?></p></td>
                    <td width="100" align="right" ><p><? echo number_format($return_qty,2,'.',','); ?></p></td>
                    <td align="right" title="<? echo $issue_qty.'='.$receive_qty; ?>" ><p><? echo number_format($balance_qty,2,'.',','); ?></p></td>
				</tr>
               
				<?	
				$i++;
				$tot_receive+=$receive_qty;
				$tot_issue_qty+=$issue_qty;
				$tot_return_qty+=$return_qty;
				$tot_balance_qty+=$balance_qty;
			} 
			?>
            </table>
            <table width="1310" border="1" class="rpt_table" rules="all" >
			<tr class="tbl_bottom">
                <td  width="40"><b>&nbsp;</b></td>
                <td  width="150"><b>&nbsp;</b></td>
                <td  width="110"><b>&nbsp;</b></td>
                <td  width="110"><b>&nbsp;</b></td>
                <td  width="110"><b>&nbsp;</b></td>
                <td  width="210"><b>&nbsp;</b></td>
				<td  width="110" align="right"><b>Total:</b></td>
				<td  width="100" align="right" id="receive_quantity"><b><? echo number_format($tot_receive,2); ?></b></td>
				<td  width="100"align="right" id="issue_quantity"><b><? echo number_format($tot_issue_qty,2); ?></b></td>
				<td  width="100" align="right" id="return_quantity"><b><? echo number_format($tot_return_qty,2); ?></b></td>
				<td  align="right" id="balance"><b><? echo number_format($tot_balance_qty,2); ?></b></td>
			</tr>
		</table>
		</div>
		</div>
	<?
	exit();
}
if($action=="report_generate2") //23-05-2022-ISD-9602-MD MAMUN AHMED SAGOR -SHOW 2
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_challan_no=str_replace("'","",$txt_challan_no);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	
	ob_start();
	?>
	<div align="center">
	 
		<table cellpadding="0" cellspacing="0" width="1630">
			<tr  class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="8" style="font-size:20px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="8" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="8" style="font-size:12px">
					<? if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") echo "From ".change_date_format(str_replace("'","",$txt_date_from),'dd-mm-yyyy')." To ".change_date_format(str_replace("'","",$txt_date_to),'dd-mm-yyyy')."" ;?>
				</td>
			</tr>
		</table>
		<table width="1627" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
			<thead>
                <tr>
                    <th width="40">SL</th>
                    <th width="150">Customer</th>
                    <th width="110">Item Category</th>
                    <th width="110">Order No</th>
                    <th width="110">Challan No</th>
                    <th width="210">Items Description</th>
					<th width="100">Color</th> 
					<th width="100">Lot</th> 
                    <th width="110">UOM</th>
                    <th width="100">Receive Qty</th> 
					<th width="100">Return Qty</th>     
					<th width="100">Rcvd Balance</th>                       
                    <th width="100">Issue Qty</th>                   
                    <th>Balance</th>
                </tr>
			</thead>
		</table>
	<div style="max-height:300px; overflow-y:scroll; width:1630px" id="scroll_body">
		<table width="1610" border="1" class="rpt_table" rules="all" id="table_body">
		<?
			if($txt_order_no!='') $order_cond="and order_no like '%$txt_order_no%'";
			else $order_cond="";
			
				$sql_po="select id , order_no from  subcon_ord_dtls where status_active =1 and is_deleted=0 $order_cond";
				$sql_po_result=sql_select($sql_po);
				foreach ($sql_po_result as $row)
				{
					$po_idArr[$row[csf('id')]]=$row[csf('id')];
					$order_noArr[$row[csf('id')]]=$row[csf('order_no')];
				}
			
				$po_cond_for_in=where_con_using_array($po_idArr,0,"b.order_id"); 
				
			//$order_noArr=return_library_array("select id,order_no from  subcon_ord_dtls where status_active =1 and is_deleted=0","id","order_no");
			
			if(str_replace("'","",$cbo_party_id)==0) $party_rec_cond=""; else  $party_rec_cond=" and a.party_id=$cbo_party_id";
			if(str_replace("'","",$cbo_party_id)==0) $party_bill_cond=""; else  $party_bill_cond=" and a.party_id=$cbo_party_id";
			if(str_replace("'","",$cbo_party_id)==0) $party_lib_cond=""; else  $party_lib_cond=" and buy.id=$cbo_party_id";

			if(str_replace("'","",$cbo_item_category)==0) $item_category_cond=""; else  $item_category_cond=" and b.item_category_id=$cbo_item_category";
			
			$orderID = $order_noArr[$txt_order_no] ;
			//echo $orderID;die;
			if ($txt_order_no!=''){
				if ($orderID!='') $order_no_cond=" and b.order_id like '%$orderID%'"; else $order_no_cond=" and b.order_id like '$orderID'";
			}
			
			
			if ($txt_challan_no!='') $challan_no_cond=" and a.chalan_no like '%$txt_challan_no%'"; else $challan_no_cond="";
			
			if($db_type==0)
			{
				if( $date_from==0 && $date_to==0 ) $receive_date_cond=""; else $receive_date_cond= " and a.subcon_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
			}
			else if($db_type==2)
			{
				if( $date_from==0 && $date_to==0 ) $receive_date_cond=""; else $receive_date_cond= " and a.subcon_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
			}
			
			if($date_from=="") $bill_date=""; else $bill_date= " and a.bill_date <".$txt_date_from."";
			if($date_from=="") $receive_date_cond=""; else $receive_cond= " and a.subcon_date <".$txt_date_from."";
			
			 $sql_iss_ret= "select a.id, a.trans_type,a.chalan_no, b.order_id,b.subcon_roll,b.color_id,b.color_id, b.item_category_id, b.material_description, b.subcon_uom, (b.quantity) as quantity from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.is_deleted=0 and a.trans_type in (1,2,3) $po_cond_for_in $receive_date_cond $challan_no_cond $item_category_cond ";
			 //group by a.id, a.trans_type, b.order_id, b.item_category_id, b.material_description, b.subcon_uom
			//  echo  $sql_iss_ret;
			$sql_iss_ret_arr=sql_select($sql_iss_ret); $iss_ret_qty_arr=array();
			foreach ($sql_iss_ret_arr as $row)
			{
				//if($row[csf('trans_type')]==2)
				$iss_ret_qty_arr[$row[csf('trans_type')]][$row[csf('chalan_no')]][$row[csf('color_id')]][$row[csf('order_id')]][$row[csf('item_category_id')]][$row[csf('material_description')]][$row[csf('subcon_uom')]]+=$row[csf('quantity')];
			}
			//print_r($iss_ret_qty_arr);
			unset($sql_iss_ret_arr);


			$rcv_return_sql_data=sql_select("select a.id as return_dtls_id, b.id, b.mst_id, b.item_category_id, b.material_description, b.color_id, b.size_id, b.gsm, b.stitch_length, b.grey_dia, b.mc_dia,
				b.mc_gauge, b.fin_dia, b.dia_uom, b.rate, b.uom,b.subcon_roll, b.rec_cone, b.order_id, b.buyer_po_id, b.job_id, b.job_dtls_id, b.job_break_id, b.fabric_details_id, a.quantity ,c.trans_type, b.subcon_uom , c.party_id, c.chalan_no  from sub_material_return_dtls a, sub_material_dtls b,sub_material_mst c where c.id=b.mst_id  and a.is_deleted=0 and b.is_deleted=0 and a.receive_dtls_id = b.id and c.trans_type=1");
				$rcv_return_qty_arr=array();
				
			   foreach ($rcv_return_sql_data as $row)
			   {

					$string =$row[csf('mst_id')].'*'.$row[csf('item_category_id')].'*'.$row[csf('order_id')].'*'.$row[csf('material_description')].'*'.$row[csf('color_id')].'*'.$row[csf('subcon_uom')];
				
					$rcv_return_qty_arr[$string]+=$row[csf('quantity')];
					
			   }

		


			
			$partyArr=return_library_array("select buy.id,buy.buyer_name from  lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id","id","buyer_name");
			//and b.order_id=$orderID
		   $sql= "select a.id, a.chalan_no, a.subcon_date, a.party_id, b.order_id, b.color_id,b.item_category_id, b.material_description, b.subcon_uom, sum(b.quantity) as quantity,b.lot_no from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.is_deleted=0 and a.trans_type=1  $party_rec_cond $receive_date_cond $challan_no_cond $item_category_cond $po_cond_for_in group by a.id, a.chalan_no, a.subcon_date, a.party_id, b.order_id,b.color_id, b.item_category_id, b.material_description, b.subcon_uom,b.lot_no";
		
			
			$sql_result=sql_select($sql);
			$i=1;
			$string2="";
			$receive_qty=0; 	$issue_qty=0; $return_qty=0; $balance_qty=0;$tot_return_qty=0;
			foreach ($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
				$receive_qty=$row[csf("quantity")];
				$issue_qty=$iss_ret_qty_arr[2][$row[csf('chalan_no')]][$row[csf('color_id')]][$row[csf('order_id')]][$row[csf('item_category_id')]][$row[csf('material_description')]][$row[csf('subcon_uom')]];
				
				$string2 =$row[csf('id')].'*'.$row[csf('item_category_id')].'*'.$row[csf('order_id')].'*'.$row[csf('material_description')].'*'.$row[csf('color_id')].'*'.$row[csf('subcon_uom')];
				$rcv_return_qty=$rcv_return_qty_arr[$string2];
				$rcv_balance_qty=$receive_qty-$rcv_return_qty;
				$balance_qty=$rcv_balance_qty-$issue_qty;
				
				?>
				<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="40" title=<?=$row[csf('id')];?>><p><? echo $i; ?></p></td>
                    <td width="150" ><p><? echo $partyArr[$row[csf('party_id')]]; ?></p></td>
                    <td width="110"><p><? echo $item_category[$row[csf('item_category_id')]];?></p></td>
                    <td width="110"><p><? echo $order_noArr[$row[csf('order_id')]]?></p></td>
                    <td width="110"><p><? echo $row[csf('chalan_no')]?></p></td>
                    <td width="210"><p><? echo $row[csf('material_description')]; ?></p></td>
					<td width="100" ><p><? echo $color_library[$row[csf('color_id')]]; ?></p></td>   
					<td width="100"  ><p><? echo $row[csf('lot_no')]; ?></p></td>   
                    <td width="110"><p><? echo $unit_of_measurement[$row[csf('subcon_uom')]]; ?></p></td>
                    <td width="100" align="right" ><p><? echo number_format($receive_qty,2,'.',','); ?></p></td>
					<td width="100" align="right" ><p><? echo number_format($rcv_return_qty,2,'.',','); ?></p></td>
					<td width="100" align="right" ><p><? echo number_format($rcv_balance_qty,2,'.',','); ?></p></td>         
                    <td width="100" align="right" ><p><? echo number_format($issue_qty,2,'.',','); ?></p></td>                    
                    <td align="right" ><p><? echo number_format($balance_qty,2,'.',','); ?></p></td>
				</tr>
               
				<?	
				$i++;
				$tot_receive+=$receive_qty;
				$tot_issue_qty+=$issue_qty;
				$tot_return_qty+=$rcv_return_qty;
				$tot_balance_qty+=$balance_qty;
				$tot_rcv_balance_qty+=$rcv_balance_qty;
			} 
			?>
            </table>
            <table width="1610" border="1" class="rpt_table" rules="all" >
			<tr class="tbl_bottom">
                <td  width="40"><b>&nbsp;</b></td>
                <td  width="150"><b>&nbsp;</b></td>
                <td  width="110"><b>&nbsp;</b></td>
                <td  width="110"><b>&nbsp;</b></td>
				<td  width="110"><b>&nbsp;</b></td>
				<td  width="210"><b>&nbsp;</b></td>
				<td  width="100"><b>&nbsp;</b></td>
				<td  width="100"><b>&nbsp;</b></td>
               
               
				<td  width="110" align="right"><b>Total:</b></td>
				<td  width="100" align="right" ><b><? echo number_format($tot_receive,2); ?></b></td>
				<td  width="100" align="right" ><b><? echo number_format($tot_return_qty,2); ?></b></td>
				<td  width="100" align="right" ><b><? echo number_format($tot_rcv_balance_qty,2); ?></b></td>
				<td  width="100"align="right" ><b><? echo number_format($tot_issue_qty,2); ?></b></td>	
				<td  align="right"  ><b><? echo number_format($tot_balance_qty,2); ?></b></td>
			</tr>
		</table>
		</div>
		</div>
	<?
	exit();
}
?>