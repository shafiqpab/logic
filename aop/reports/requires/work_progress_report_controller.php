<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
$imge_arr=return_library_array( "select id,master_tble_id,image_location from common_photo_library",'id','image_location');
$party_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');

if ($action=="load_drop_down_buyer")
{ 
	echo create_drop_down( "cbo_buyer_id", 125, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();   	 
} 

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
	<script>
		function js_set_value(id)
		{
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
    </script>
    <input type="hidden" id="selected_id" name="selected_id" /> 
	<?
	$year_job = str_replace("'","",$year);

    if($db_type==0) 
	{
		$year_field="year(a.insert_date) as year"; 
		 if(trim($year)!=0) $year_field_cond="and YEAR(a.insert_date)=$year_job";
	}
    else if($db_type==2) 
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
		 if(trim($year)!=0) $year_field_cond=" and to_char(a.insert_date,'YYYY')=$year_job";
	}
    else 
	{
		$year_field="";
		$year_field_cond="";
	}
    
    if($cbo_buyer_name==0) $buyer_cond=""; else $buyer_cond=" and a.party_id='$cbo_buyer_name'";
    
    $buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	
   $sql="select a.party_id, a.subcon_job, a.job_no_prefix_num, $year_field, b.order_no, b.cust_style_ref, b.main_process_id from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.company_id='$company_id' $buyer_cond $year_field_cond order by a.id desc";	
	
    ?>
    <table width="500" border="1" rules="all" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="80">Job no</th>
            <th width="70">Year</th>
            <th width="130">Buyer</th>
            <th width="110">Style</th>
            <th width="120">Po number</th>
       </thead>
    </table>
    <div style="max-height:340px; overflow:auto;">
    <table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
     <? $data_array=sql_select($sql);
        $i=1;
		 foreach($data_array as $row)
		 {
			 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		 ?>
			<tr bgcolor="<? echo $bgcolor; ?>"  onClick="js_set_value('<? echo $row[csf('job_no_prefix_num')]; ?>')" style="cursor:pointer;">
				<td width="30"><? echo $i; ?></td>
				<td align="center"  width="80"><? echo $row[csf('job_no_prefix_num')]; ?></td>
				<td align="center" width="70"><? echo $row[csf('year')]; ?></td>
				<td width="130"><? echo $buyer[$row[csf('party_id')]]; ?></td>
				<td width="110"><p><? echo $row[csf('cust_style_ref')]; ?></p></td>
				<td width="120"><p><? echo $row[csf('order_no')]; ?></p></td>
			</tr>
			<? $i++; 
			} 
		?>
    </table>
    </div>
	<script> setFilterGrid("table_body2",-1); </script>
    <?
	disconnect($con);
	exit();
}

if($action=="style_no_popup")
{
	echo load_html_head_contents("Style Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
	<script>
		function js_set_value(id)
		{
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
    </script>
    <input type="hidden" id="selected_id" name="selected_id" /> 
	<?
	$year_job = str_replace("'","",$year);

    if($db_type==0) 
	{
		$year_field="year(a.insert_date) as year"; 
		 if(trim($year)!=0) $year_field_cond="and YEAR(a.insert_date)=$year_job";
	}
    else if($db_type==2) 
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
		 if(trim($year)!=0) $year_field_cond=" and to_char(a.insert_date,'YYYY')=$year_job";
	}
    else 
	{
		$year_field="";
		$year_field_cond="";
	}
    
    if($cbo_buyer_name==0) $buyer_cond=""; else $buyer_cond=" and a.party_id='$cbo_buyer_name'";
    
    $buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	
   $sql="select a.party_id, a.subcon_job, a.job_no_prefix_num, $year_field, b.order_no, b.cust_style_ref, b.main_process_id from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.company_id='$company_id' $buyer_cond $year_field_cond order by a.id desc";	
	
    ?>
    <table width="500" border="1" rules="all" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="80">Job no</th>
            <th width="70">Year</th>
            <th width="130">Buyer</th>
            <th width="110">Style</th>
            <th width="120">Po number</th>
       </thead>
    </table>
    <div style="max-height:340px; overflow:auto;">
    <table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
     <? $data_array=sql_select($sql);
        $i=1;
		 foreach($data_array as $row)
		 {
			 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		 ?>
			<tr bgcolor="<? echo $bgcolor; ?>"  onClick="js_set_value('<? echo $row[csf('cust_style_ref')]; ?>')" style="cursor:pointer;">
				<td width="30"><? echo $i; ?></td>
				<td align="center"  width="80"><? echo $row[csf('job_no_prefix_num')]; ?></td>
				<td align="center" width="70"><? echo $row[csf('year')]; ?></td>
				<td width="130"><? echo $buyer[$row[csf('party_id')]]; ?></td>
				<td width="110"><p><? echo $row[csf('cust_style_ref')]; ?></p></td>
				<td width="120"><p><? echo $row[csf('order_no')]; ?></p></td>
			</tr>
			<? $i++; 
			} 
		?>
    </table>
    </div>
	<script> setFilterGrid("table_body2",-1); </script>
    <?
	disconnect($con);
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

if($action=="report_generate")
{ 
	//'cbo_company_id*cbo_buyer_id*cbo_process_id*cbo_search_by*cbo_year*txt_job_no*txt_style_ref*txt_order_no*txt_date_from*txt_date_to'
	$job_no=str_replace("'","",$txt_job_no);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$year_id=str_replace("'","",$cbo_year);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$cbo_process=str_replace("'","",$cbo_process_id);
	if($year_id!=0) $year_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id"; else $year_cond="";
	if($cbo_buyer_id!=0) $buyer_id_cond=" and a.party_id='$cbo_buyer_id'"; else $buyer_id_cond="";
	
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	//if($txt_style_ref!="") $style_ref_cond="%".trim($txt_style_ref)."%"; else $style_ref_cond="%%";
	if ($txt_style_ref!='') $style_ref_cond=" and b.cust_style_ref like '%$txt_style_ref%'"; else $style_ref_cond="";
	//if(trim($txt_order_no)!="") $order_no_cond="%".trim($txt_order_no)."%"; else $order_no_cond="%%";
	if ($txt_order_no!='') $order_no_cond=" and b.order_no like '%$txt_order_no%'"; else $order_no_cond="";
	if ($cbo_process==0) $process_id_cond=""; else $process_id_cond=" and b.main_process_id=$cbo_process_id";
	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond=""; else $date_cond=" and b.delivery_date between $txt_date_from and $txt_date_to";
	
	$inventory_array=array();
	$inventory_sql="select b.order_id, sum(b.quantity) as quantity from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 and a.status_active=1 and b.is_deleted=0 and b.status_active=2 and b.is_deleted=0 group by b.order_id";
	
	$inventory_sql_result=sql_select($inventory_sql);
	foreach ($inventory_sql_result as $row)
	{
		$inventory_array[$row[csf('order_id')]]['quantity']=$row[csf('quantity')];
	}
	
	$inv_iss_array=array();
	$inv_iss_sql="select b.order_id, sum(b.quantity) as quantity from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=2 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id";
	$inv_iss_sql_result=sql_select($inv_iss_sql);
	foreach ($inv_iss_sql_result as $row)
	{
		$inv_iss_array[$row[csf('order_id')]]['quantity']=$row[csf('quantity')];
	}	
	$inventory_ret_array=array();
	$inv_ret_sql="select b.order_id, sum(b.quantity) as quantity from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=3 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id";
	
	$inv_ret_sql_result=sql_select($inv_ret_sql);
	foreach ($inv_ret_sql_result as $row)
	{
		$inventory_ret_array[$row[csf('order_id')]]=$row[csf('quantity')];
	}	//var_dump($inventory_array);
	$delivery_array=array();
	$delivery_sql="select b.order_id,
	sum(CASE WHEN b.process_id='1' THEN  b.delivery_qty END) AS cutting,
	sum(CASE WHEN b.process_id='2' THEN  b.delivery_qty END) AS kniting,
	sum(CASE WHEN b.process_id='3' THEN  b.delivery_qty END) AS dyeing,
	sum(CASE WHEN b.process_id='4' THEN  b.delivery_qty END) AS finishing,
	sum(CASE WHEN b.process_id='5' THEN  b.delivery_qty END) AS sewing,
	sum(CASE WHEN b.process_id='6' THEN  b.delivery_qty END) AS fab_print,
	sum(CASE WHEN b.process_id='7' THEN  b.delivery_qty END) AS washing,
	sum(CASE WHEN b.process_id='8' THEN  b.delivery_qty END) AS printing,
	sum(CASE WHEN b.process_id='9' THEN  b.delivery_qty END) AS Embroidery,
	sum(CASE WHEN b.process_id='10' THEN  b.delivery_qty END) AS Iron,
	sum(CASE WHEN b.process_id='11' THEN  b.delivery_qty END) AS Gmts_Finishing,
	sum(CASE WHEN b.process_id='12' THEN  b.delivery_qty END) AS Gmts_Dyeing,
	sum(CASE WHEN b.process_id='13' THEN  b.delivery_qty END) AS Poly
	from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 group by b.order_id";
	$delivery_sql_result=sql_select($delivery_sql);
	foreach ($delivery_sql_result as $row)
	{
		$delivery_array[$row[csf('order_id')]]['item_id']=$row[csf('item_id')];
		$delivery_array[$row[csf('order_id')]]['cutting']=$row[csf('cutting')];
		$delivery_array[$row[csf('order_id')]]['kniting']=$row[csf('kniting')];
		$delivery_array[$row[csf('order_id')]]['dyeing']=$row[csf('dyeing')];
		$delivery_array[$row[csf('order_id')]]['finishing']=$row[csf('finishing')];
		$delivery_array[$row[csf('order_id')]]['sewing']=$row[csf('sewing')];
		$delivery_array[$row[csf('order_id')]]['fab_print']=$row[csf('fab_print')];
		$delivery_array[$row[csf('order_id')]]['washing']=$row[csf('washing')];
		$delivery_array[$row[csf('order_id')]]['printing']=$row[csf('printing')];

		$delivery_array[$row[csf('order_id')]]['Embroidery']=$row[csf('Embroidery')];
		$delivery_array[$row[csf('order_id')]]['Iron']=$row[csf('Iron')];
		$delivery_array[$row[csf('order_id')]]['Gmts_Finishing']=$row[csf('Gmts_Finishing')];
		$delivery_array[$row[csf('order_id')]]['Gmts_Dyeing']=$row[csf('Gmts_Dyeing')];
		$delivery_array[$row[csf('order_id')]]['Poly']=$row[csf('Poly')];
	}
	
	$fab_production_array=array();
	$fab_production_sql="select c.order_id,
	sum(CASE WHEN c.product_type='4' THEN  c.quantity END) AS finishing,
	sum(CASE WHEN c.product_type='8' THEN  c.quantity END) AS printing
	from  subcon_production_mst a, subcon_production_dtls b, subcon_production_qnty c where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1  and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by c.order_id";
	$fab_production_sql_result=sql_select($fab_production_sql);
	foreach ($fab_production_sql_result as $row)
	{
		$order_id=explode(',',$row[csf('order_id')]);
		foreach ($order_id as $val)
		{
			$fab_production_array[$val]['fabric_description']=$row[csf('fabric_description')];
			$fab_production_array[$val]['finishing']=$row[csf('finishing')];
			$fab_production_array[$val]['printing']=$row[csf('printing')];
		}
	}
	
	$knit_production_array=array();
	$knit_production_sql="select b.order_id, sum(b.product_qnty) AS kniting
	from  subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and b.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id";
	$knit_production_sql_result=sql_select($knit_production_sql);
	foreach ($knit_production_sql_result as $row)
	{
		$knit_production_array[$row[csf('order_id')]]['kniting']=$row[csf('kniting')];
	}	
	//var_dump ($fab_production_array);
	$dying_data_array=array();
	if ($db_type==0)
	{
		$dying_sql="select c.po_id, sum(c.batch_qnty) as production_qnty from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.entry_form=38 and a.result=1 and a.load_unload_id=2 and b.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_id ";
	}
	elseif($db_type==2)
	{
		$dying_sql="select c.po_id, sum(c.batch_qnty) as production_qnty from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.entry_form=38 and a.result=1 and a.load_unload_id=2 and b.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_id ";
	}
	$dying_sql_result=sql_select($dying_sql);
	foreach ($dying_sql_result as $row)
	{
		$dying_data_array[$row[csf('po_id')]]=$row[csf('production_qnty')];
	}
	//var_dump($dying_data_array);
	
	$gmt_production_array=array();
	$gmt_production_sql="select order_id,
	sum(CASE WHEN production_type='1' THEN  production_qnty END) AS cutting,
	sum(CASE WHEN production_type='2' THEN  production_qnty END) AS sewing,
	sum(CASE WHEN production_type='4' THEN  production_qnty END) AS Gmts_Finishing
	from subcon_gmts_prod_dtls where status_active=1 and is_deleted=0 group by order_id";
	$gmt_production_sql_result=sql_select($gmt_production_sql);
	foreach ($gmt_production_sql_result as $row)
	{
		$gmt_production_array[$row[csf('order_id')]]['cutting']=$row[csf('cutting')];
		$gmt_production_array[$row[csf('order_id')]]['sewing']=$row[csf('sewing')];
		$gmt_production_array[$row[csf('order_id')]]['Gmts_Finishing']=$row[csf('Gmts_Finishing')];
	}
	//var_dump($gmt_production_array);
	$in_bill_qty_array=array();
	$in_bill_amnt_array=array(); $bill_order_arr=array(); $bill_amt_arr=array();
	$in_bill_sql="select order_id, mst_id, sum(amount) as amount,
	sum(CASE WHEN process_id='1' THEN  delivery_qty END) AS cutting,
	sum(CASE WHEN process_id='2' THEN  delivery_qty END) AS kniting,
	sum(CASE WHEN process_id='3' THEN  delivery_qty END) AS dyeing,
	sum(CASE WHEN process_id='4' THEN  delivery_qty END) AS finishing,
	sum(CASE WHEN process_id='5' THEN  delivery_qty END) AS sewing,
	sum(CASE WHEN process_id='6' THEN  delivery_qty END) AS fab_print,
	sum(CASE WHEN process_id='7' THEN  delivery_qty END) AS washing,
	sum(CASE WHEN process_id='8' THEN  delivery_qty END) AS printing,
	sum(CASE WHEN process_id='9' THEN  delivery_qty END) AS Embroidery,
	sum(CASE WHEN process_id='10' THEN  delivery_qty END) AS Iron,
	sum(CASE WHEN process_id='11' THEN  delivery_qty END) AS Gmts_Finishing,
	sum(CASE WHEN process_id='12' THEN  delivery_qty END) AS Gmts_Dyeing,
	sum(CASE WHEN process_id='13' THEN  delivery_qty END) AS Poly,
	
	sum(CASE WHEN process_id='1' THEN  amount END) AS am_cutting,
	sum(CASE WHEN process_id='2' THEN  amount END) AS am_kniting,
	sum(CASE WHEN process_id='3' THEN  amount END) AS am_dyeing,
	sum(CASE WHEN process_id='4' THEN  amount END) AS am_finishing,
	sum(CASE WHEN process_id='5' THEN  amount END) AS am_sewing,
	sum(CASE WHEN process_id='6' THEN  amount END) AS am_fab_print,
	sum(CASE WHEN process_id='7' THEN  amount END) AS am_washing,
	sum(CASE WHEN process_id='8' THEN  amount END) AS am_printing,
	sum(CASE WHEN process_id='9' THEN  amount END) AS am_Embroidery,
	sum(CASE WHEN process_id='10' THEN  amount END) AS am_Iron,
	sum(CASE WHEN process_id='11' THEN  amount END) AS am_Gmts_Finishing,
	sum(CASE WHEN process_id='12' THEN  amount END) AS am_Gmts_Dyeing,
	sum(CASE WHEN process_id='13' THEN  amount END) AS am_Poly
	
	from subcon_inbound_bill_dtls where status_active=1 and is_deleted=0 group by order_id, mst_id";
	$in_bill_sql_result=sql_select($in_bill_sql);
	foreach ($in_bill_sql_result as $row)
	{
		$in_bill_qty_array[$row[csf('order_id')]]['cutting']+=$row[csf('cutting')];
		$in_bill_qty_array[$row[csf('order_id')]]['kniting']+=$row[csf('kniting')];
		$in_bill_qty_array[$row[csf('order_id')]]['dyeing']+=$row[csf('dyeing')];
		$in_bill_qty_array[$row[csf('order_id')]]['finishing']+=$row[csf('finishing')];
		$in_bill_qty_array[$row[csf('order_id')]]['sewing']+=$row[csf('sewing')];
		$in_bill_qty_array[$row[csf('order_id')]]['fab_print']+=$row[csf('fab_print')];
		$in_bill_qty_array[$row[csf('order_id')]]['washing']+=$row[csf('washing')];
		$in_bill_qty_array[$row[csf('order_id')]]['printing']+=$row[csf('printing')];

		$in_bill_qty_array[$row[csf('order_id')]]['Embroidery']+=$row[csf('Embroidery')];
		$in_bill_qty_array[$row[csf('order_id')]]['Iron']+=$row[csf('Iron')];
		$in_bill_qty_array[$row[csf('order_id')]]['Gmts_Finishing']+=$row[csf('Gmts_Finishing')];
		$in_bill_qty_array[$row[csf('order_id')]]['Gmts_Dyeing']+=$row[csf('Gmts_Dyeing')];
		$in_bill_qty_array[$row[csf('order_id')]]['Poly']+=$row[csf('Poly')];
		
		$in_bill_amnt_array[$row[csf('order_id')]]['am_cutting']+=$row[csf('am_cutting')];
		$in_bill_amnt_array[$row[csf('order_id')]]['am_kniting']+=$row[csf('am_kniting')];
		$in_bill_amnt_array[$row[csf('order_id')]]['am_dyeing']+=$row[csf('am_dyeing')];
		$in_bill_amnt_array[$row[csf('order_id')]]['am_finishing']+=$row[csf('am_finishing')];
		$in_bill_amnt_array[$row[csf('order_id')]]['am_sewing']+=$row[csf('am_sewing')];
		$in_bill_amnt_array[$row[csf('order_id')]]['am_fab_print']+=$row[csf('am_fab_print')];
		$in_bill_amnt_array[$row[csf('order_id')]]['am_washing']+=$row[csf('am_washing')];
		$in_bill_amnt_array[$row[csf('order_id')]]['am_printing']+=$row[csf('am_printing')];

		$in_bill_amnt_array[$row[csf('order_id')]]['am_Embroidery']+=$row[csf('am_Embroidery')];
		$in_bill_amnt_array[$row[csf('order_id')]]['am_Iron']+=$row[csf('am_Iron')];
		$in_bill_amnt_array[$row[csf('order_id')]]['am_Gmts_Finishing']+=$row[csf('am_Gmts_Finishing')];
		$in_bill_amnt_array[$row[csf('order_id')]]['am_Gmts_Dyeing']+=$row[csf('am_Gmts_Dyeing')];
		$in_bill_amnt_array[$row[csf('order_id')]]['am_Poly']+=$row[csf('am_Poly')];

		$bill_order_arr[$row[csf('mst_id')]].=$row[csf('order_id')].',';
		$bill_amt_arr[$row[csf('order_id')]]+=$row[csf('amount')];
	}
	
	/*$pay_rec_array=array();
	$pay_rec_sql="select bill_id, sum(total_adjusted) as rec_amount from subcon_payment_receive_dtls where status_active=1 and is_deleted=0 group by bill_id";
	$pay_rec_sql_result=sql_select($pay_rec_sql);
	foreach ($pay_rec_sql_result as $row)
	{
		$pay_rec_array[$row[csf('bill_id')]]=$row[csf('rec_amount')];
	}
	

	$po_wise_payRec_arr=array();
	foreach($bill_order_arr as $mst_id=>$allpo_id)
	{
		$pay_rec_amt=$pay_rec_array[$mst_id];
		$ex_po_id=array_filter(explode(',',$allpo_id));
		foreach($ex_po_id as $po_id)
		{
			$bill_amt=0;
			$bill_amt=$pay_rec_amt-$bill_amt_arr[$po_id];
			$n_pay_amt=$pay_rec_amt-$bill_amt;
			if($bill_amt_arr[$po_id]>=$pay_rec_amt)
				$po_wise_payRec_arr[$po_id]=$bill_amt_arr[$po_id];
			else
				$po_wise_payRec_arr[$po_id]=$n_pay_amt;
		}
	}*/

	$order_wise_tot_paid_arr=array();
	$order_wise_tot_bill_arr2=array();
	$order_wise_tot_bill_arr=array();
	//$order_wise_tot_paid="select d.order_id, sum(b.total_adjusted) as rec_amount, sum(b.bill_amount) as bill_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by d.order_id";

	$order_wise_tot_paid="select d.order_id, b.total_adjusted as rec_amount, sum(b.bill_amount) as bill_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by d.order_id,b.total_adjusted";

	$order_wise_tot_paid_result=sql_select($order_wise_tot_paid);
	foreach ($order_wise_tot_paid_result as $row)
	{
		//$order_wise_tot_paid_arr[$row[csf('order_id')]]=$row[csf('rec_amount')];
		//$order_wise_tot_bill_arr[$row[csf('order_id')]]=$row[csf('bill_amount')];
		$order_wise_tot_paid_arr[$row[csf('order_id')]]+=$row[csf('rec_amount')];
	}

	$order_wise_tot_bill="select a.id, d.order_id, b.bill_id, b.bill_amount as bill_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 order by a.id asc";
	$order_wise_tot_bill_result=sql_select($order_wise_tot_bill);
	foreach ($order_wise_tot_bill_result as $row)
	{
		$order_wise_tot_bill_arr2[$row[csf('order_id')]][$row[csf('bill_id')]][$row[csf('id')]]=$row[csf('bill_amount')];
	}

	$sum=0;
	foreach ($order_wise_tot_bill_arr2 as $key=>$value) 
	{
		foreach ($value as $val) 
		{
			foreach ($val as $val2) 
			{
				 $sum+=$val2;
				 break;
			}
		}
		$order_wise_tot_bill_arr[$key]=$sum;
		$sum=0;
	}

	//var_dump($po_wise_payRec_arr);
	
	$batch_qty_array=array();
	$sql_batch="Select b.po_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_id";
	$sql_batch_result=sql_select($sql_batch);
	foreach ($sql_batch_result as $row)
	{
		$batch_qty_array[$row[csf('po_id')]]=$row[csf('batch_qnty')];
	}
	
	$job_sql = "select a.id as pic_id,a.job_no_prefix_num, a.subcon_job, a.party_id, b.order_no, b.order_quantity, b.amount, b.order_rcv_date, b.delivery_date, b.main_process_id, b.order_uom, b.id, b.cust_style_ref 
	from subcon_ord_mst a, subcon_ord_dtls b
	where a.subcon_job=b.job_no_mst and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_no_cond $style_ref_cond $date_cond $job_no_cond $process_id_cond $buyer_id_cond
		group by a.job_no_prefix_num, a.subcon_job, a.company_id, a.party_id, b.order_no, b.order_quantity, b.amount, b.order_rcv_date, b.delivery_date, b.main_process_id, b.order_uom, b.id, b.cust_style_ref,a.id  order by b.main_process_id, a.job_no_prefix_num, b.order_no, b.delivery_date ";
		//echo $job_sql;
	$job_sql_result=sql_select($job_sql);
	ob_start();
	if ($cbo_process==4)
	{
		$tbl_width=1950;
		$col_span=23;
	}
	else
	{
		$tbl_width=1790;
		$col_span=21;
	}
	$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library",'master_tble_id','image_location');
	?>
    <div>
        <table width="<? echo $tbl_width; ?>" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="70">Job No</th>
                <th width="100">Party</th>
                <th width="80">Order no</th>
                <th width="50">Image</th>                            
                <th width="100">Style Name</th>
                <th width="90">Order Quantity</th>
                <th width="100">Order Value</th>
                <th width="60">UOM</th>
                <th width="90">Delivery Date</th>
                <th width="60">Days in Hand</th>
                <th width="120">Material Receive</th>
                <th width="120">Material Issue</th>
                <th width="120">Material Balance</th>
                <?
					if ($cbo_process==4)
					{
						?>
                        <th width="80">Batch Qty</th>
                        <th width="80">Dyeing Qty</th>
                        <?
					}
				?>
                <th width="80">Prod. Qty</th>
                <th width="80">Delivery Qty</th>
                <? if ($type==1)
				{
					?>
                <th width="80">Yet To Delv.</th>
                <? } ?>
                <th width="80">Bill Qty</th>
                <th width="80">Bill Amount</th>
                <? if ($type==2)
				{
					?>
                <th width="80">Yet To Bill</th>
                <? } ?>
                <th width="100">Payment Rec.</th>
                <th>Rec. Balance</th>
            </thead>
        </table>
    <div style="max-height:400px; overflow-y:scroll; width:<? echo $tbl_width+20; ?>px" id="scroll_body">
        <table width="<? echo $tbl_width; ?>" border="1" class="rpt_table" rules="all" id="table_body">
    <?
	$process_array=array();
	$i=1; $k=1;
	foreach ($job_sql_result as $row)
	{
		if (!in_array($row[csf("main_process_id")],$process_array) )
		{
			if($k!=1)
			{
			?>
				<tr class="tbl_bottom">
					<td colspan="6" align="right"><b>Process Total:</b></td>
					<td align="right"><? echo number_format($tot_order_qty); ?></td>
					<td align="right"><? echo number_format($tot_order_val); ?></td>
					<td align="right">&nbsp;</td>
					<td align="right">&nbsp;</td>
					<td align="right">&nbsp;</td>
					<td align="right"><? echo number_format($tot_rec_qty); ?></td>
                    <td align="right"><? echo number_format($tot_issue_qty); ?></td>
                    <td align="right">
                    	<? 
                    		$tot_material_blce=$tot_rec_qty-$tot_issue_qty;
                    		echo $tot_material_blce;
                     	?>
                     </td>
                    <?
						if ($cbo_process==4)
						{
							?>
                                <td><? echo number_format($tot_batch_qty); ?></td>
                                <td><? echo number_format($tot_dyeing_qty); ?></td>
							<?
						}
					?>
					<td align="right"><? echo number_format($tot_prod_qty); ?></td>
					<td align="right"><? echo number_format($tot_del_qty); ?></td>
                    <? if ($type==1)
					{
					?>
                    <td align="right"><? echo number_format($tot_yet_to_delv); ?></td>
                    <? } ?>
					<td align="right"><? echo number_format($tot_bill_qty); ?></td>
					<td align="right"><? echo number_format($tot_bill_amnt); ?></td>
                    <? if ($type==2)
					{
					?>
                    <td align="right"><? echo number_format($tot_yet_to_bill); ?></td>
                    <? } ?>
					<td align="right"><? echo number_format($tot_payment_amnt); ?></td>
                    <td align="right"><? echo number_format($tot_balance); ?></td>
				</tr>
				<tr bgcolor="#dddddd">
					<td colspan="<? echo $col_span; ?>" align="left" ><b>Process : <? echo $production_process[$row[csf("main_process_id")]]; ?></b></td>
				</tr>
			<?
				unset($tot_order_qty);
				unset($tot_order_val);
				unset($tot_rec_qty);
				unset($tot_issue_qty);
				unset($tot_material_blce);
				if ($cbo_process==4)
				{
					unset($tot_batch_qty);
					unset($tot_dyeing_qty);
				}
				unset($tot_prod_qty);
				unset($tot_del_qty);
				unset($tot_bill_qty);
				unset($tot_bill_amnt);
				unset($tot_payment_amnt);
				if ($type==1)
				{
					unset($tot_yet_to_delv);
				}
				else if ($type==2)
				{
					unset($tot_yet_to_bill);
				}
				unset($tot_balance);
			}
			else
			{
				?>
				<tr bgcolor="#dddddd">
					<td colspan="<? echo $col_span; ?>" align="left" ><b>Process : <? echo $production_process[$row[csf("main_process_id")]]; ?></b></td>
				</tr>
				<?
			}					
			$process_array[]=$row[csf('main_process_id')];            
			$k++;
		}
        if ($i%2==0)  $bgcolor="#E9F3FF";else $bgcolor="#FFFFFF";
		$prod_qty=0; $del_qty=0; $bill_qty=0; $bill_amnt=0; $pay_rec=0;
		if ($row[csf('main_process_id')]==1)
		{
			$prod_qty=$gmt_production_array[$row[csf('id')]]['cutting'];
			$del_qty=$delivery_array[$row[csf('id')]]['cutting'];
			$bill_qty=$in_bill_qty_array[$row[csf('id')]]['cutting'];
			$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_cutting'];
			$batch_qty=""; $dyeing_qty="";
			//$pay_rec=
		}
		else if ($row[csf('main_process_id')]==2)
		{
			$prod_qty=$knit_production_array[$row[csf('id')]]['kniting'];
			$del_qty=$delivery_array[$row[csf('id')]]['kniting'];
			$bill_qty=$in_bill_qty_array[$row[csf('id')]]['kniting'];
			$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_kniting'];
			$batch_qty=""; $dyeing_qty="";
			//$pay_rec=
		}
		else if ($row[csf('main_process_id')]==3)
		{
			$prod_qty=$dying_data_array[$row[csf('id')]];
			$del_qty=$delivery_array[$row[csf('id')]]['dyeing'];
			$bill_qty=$in_bill_qty_array[$row[csf('id')]]['dyeing'];
			$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_dyeing'];
			$batch_qty=""; $dyeing_qty="";
			//$pay_rec=
		}
		else if ($row[csf('main_process_id')]==4)
		{
			$prod_qty=$fab_production_array[$row[csf('id')]]['finishing'];
			$del_qty=$delivery_array[$row[csf('id')]]['finishing'];
			$bill_qty=$in_bill_qty_array[$row[csf('id')]]['finishing'];
			$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_finishing'];
			$batch_qty=$batch_qty_array[$row[csf('id')]];
			$dyeing_qty=$dying_data_array[$row[csf('id')]];
			//$pay_rec=
		}
		else if ($row[csf('main_process_id')]==5)
		{
			$prod_qty=$gmt_production_array[$row[csf('id')]]['sewing'];
			$del_qty=$delivery_array[$row[csf('id')]]['sewing'];
			$bill_qty=$in_bill_qty_array[$row[csf('id')]]['sewing'];
			$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_sewing'];
			$batch_qty=""; $dyeing_qty="";
			//$pay_rec=
		}
		else if ($row[csf('main_process_id')]==6)
		{
			$prod_qty=$gmt_production_array[$row[csf('id')]]['fab_print'];
			$del_qty=$delivery_array[$row[csf('id')]]['fab_print'];
			$bill_qty=$in_bill_qty_array[$row[csf('id')]]['fab_print'];
			$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_fab_print'];
			$batch_qty=""; $dyeing_qty="";
			//$pay_rec=
		}
		else if ($row[csf('main_process_id')]==7)
		{
			$prod_qty=$gmt_production_array[$row[csf('id')]]['washing'];
			$del_qty=$delivery_array[$row[csf('id')]]['washing'];
			$bill_qty=$in_bill_qty_array[$row[csf('id')]]['washing'];
			$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_washing'];
			$batch_qty=""; $dyeing_qty="";
			//$pay_rec=
		}
		else if ($row[csf('main_process_id')]==8)
		{
			$prod_qty=$gmt_production_array[$row[csf('id')]]['printing'];
			$del_qty=$delivery_array[$row[csf('id')]]['printing'];
			$bill_qty=$in_bill_qty_array[$row[csf('id')]]['printing'];
			$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_printing'];
			$batch_qty=""; $dyeing_qty="";
			//$pay_rec=
		}

		else if ($row[csf('main_process_id')]==9)
		{
			//$prod_qty=$gmt_production_array[$row[csf('id')]]['Embroidery'];
			$del_qty=$delivery_array[$row[csf('id')]]['Embroidery'];
			$bill_qty=$in_bill_qty_array[$row[csf('id')]]['Embroidery'];
			$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_Embroidery'];
			$batch_qty=""; $dyeing_qty="";
		}

		else if ($row[csf('main_process_id')]==10)
		{
			//$prod_qty=$gmt_production_array[$row[csf('id')]]['Iron'];
			$del_qty=$delivery_array[$row[csf('id')]]['Iron'];
			$bill_qty=$in_bill_qty_array[$row[csf('id')]]['Iron'];
			$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_Iron'];
			$batch_qty=""; $dyeing_qty="";
		}

		else if ($row[csf('main_process_id')]==11)
		{	
			$prod_qty=$gmt_production_array[$row[csf('id')]]['Gmts_Finishing'];
			$del_qty=$delivery_array[$row[csf('id')]]['Gmts_Finishing'];
			$bill_qty=$in_bill_qty_array[$row[csf('id')]]['Gmts_Finishing'];
			$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_Gmts_Finishing'];
			$batch_qty=""; $dyeing_qty="";
		}

		else if ($row[csf('main_process_id')]==12)
		{
			//$prod_qty=$gmt_production_array[$row[csf('id')]]['Gmts_Dyeing'];
			$del_qty=$delivery_array[$row[csf('id')]]['Gmts_Dyeing'];
			$bill_qty=$in_bill_qty_array[$row[csf('id')]]['Gmts_Dyeing'];
			$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_Gmts_Dyeing'];
			$batch_qty=""; $dyeing_qty="";
		}

		else if ($row[csf('main_process_id')]==13)
		{
			//$prod_qty=$gmt_production_array[$row[csf('id')]]['Poly'];
			$del_qty=$delivery_array[$row[csf('id')]]['Poly'];
			$bill_qty=$in_bill_qty_array[$row[csf('id')]]['Poly'];
			$bill_amnt=$in_bill_amnt_array[$row[csf('id')]]['am_Poly'];
			$batch_qty=""; $dyeing_qty="";
		}
		
		
		$rec_qty=$inventory_array[$row[csf('id')]]['quantity']-$inventory_ret_array[$row[csf('id')]];
		$issue_qty=$inv_iss_array[$row[csf('id')]]['quantity'];
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">	
        	<td width="30" bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
            <td width="70" align="center"><p><? echo $row[csf('job_no_prefix_num')]; ?></td>
            <td width="100"><p><? echo $party_arr[$row[csf('party_id')]]; ?></p></td>
            <td width="80"><p><? echo $row[csf('order_no')]; ?></p></td>
            <td width="50"><img onclick="openImageWindow( <? echo $row[csf('pic_id')];//$row[csf('job_no_prefix_num')]; ?> )" src='../../<? echo $imge_arr[$row[csf('pic_id')]]; ?>' height='25' width='30' /></td>
            <td width="100"><p><? echo $row[csf('cust_style_ref')]; ?></p></td>
            
            <td width="90" align="right"><p>
            	<a href="##" onclick="show_progress_report_details('order_desc_popup','<? echo $row[csf("id")]; ?>','850px')">
            	<? echo number_format($row[csf('order_quantity')],2); ?>
            	</a>
            </p></td>

            <td width="100" align="right"><p><? echo number_format($row[csf('amount')],2); ?></p></td>
            <td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></p></td>
            <td width="90"><p><? echo change_date_format($row[csf('delivery_date')]); ?></p></td>
            <td width="60" align="center"><? $daysOnHand = datediff("d",date("Y-m-d"),$row[csf('delivery_date')]); echo $daysOnHand; ?> </td>
            <td width="120" align="right"><p><a href="##" onclick="show_progress_report_details('material_desc_popup','<? echo $row[csf("id")]; ?>','850px')"><? echo number_format($rec_qty,2); ?></a></p></td>
            <td width="120" align="right"><p><a href="##" onclick="show_progress_report_details('material_desc_iss_popup','<? echo $row[csf("id")]; ?>','850px')"><? echo number_format($issue_qty,2); ?></a></p></td>
            <td width="120" align="right">
            	<?
            		$mat_blnce_qty=$rec_qty-$issue_qty;
            		echo number_format($mat_blnce_qty,2);
            	?>
            </td>
			<?
                if ($cbo_process==4)
                {
                    ?>
                    <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('batch_qty_pop_up','<? echo $row[csf("id")]; ?>','850px')"><? echo number_format($batch_qty,2); ?></a></p></td>
                    <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('product_qty_pop_up','<? echo $row[csf("id")]; ?>_<? echo '3'; ?>','850px')"><? echo number_format($dyeing_qty,2); ?></a></p></td>
                    <?
                }
            ?>
            <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('product_qty_pop_up','<? echo $row[csf("id")]; ?>_<? echo $row[csf('main_process_id')]; ?>','850px')"><? echo number_format($prod_qty,2); ?></a></p></td>
            <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('delivery_qty_pop_up','<? echo $row[csf("id")]; ?>_<? echo $row[csf('main_process_id')]; ?>','850px')"><? echo number_format($del_qty,2); ?></a></p></td>
            <? if ($type==1)
			{
			?>
            <td width="80" align="right"><? $yet_to_delv=$row[csf('order_quantity')]-$del_qty; echo  number_format($yet_to_delv,2); ?></td>
            <? } ?>
            <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('bill_qty_pop_up','<? echo $row[csf("id")]; ?>_<? echo $row[csf('main_process_id')]; ?>','850px')"><? echo number_format($bill_qty,2); ?></a></p></td>
            <td width="80" align="right"><? echo  number_format($bill_amnt,2); ?></td>
            <? if ($type==2)
			{
			?>
            <td width="80" align="right"><? $yet_to_bill=$row[csf('order_quantity')]-$bill_qty; echo  number_format($yet_to_bill,2); ?></td>
            <? } ?>
            

            <td width="100" align="right">
            	<p><a href="##" onclick="show_progress_report_details('payment_rec_pop_up','<? echo $row[csf("id")].'_'.$bill_amnt; ?>_<? echo $row[csf('main_process_id')]; ?>','850px')">

            		<? 

            		$order_wise_payment_received = ($order_wise_tot_paid_arr[$row[csf('id')]]/$order_wise_tot_bill_arr[$row[csf('id')]])*$bill_amnt;
            		echo number_format($order_wise_payment_received,2);

            		//echo number_format($order_wise_tot_paid_arr[$row[csf('id')]],2); 

            		?>
            	</a></p>
            </td>
            

            <td width="80" align="right"><? $balance=$bill_amnt-$order_wise_payment_received; echo  number_format($balance,2); ?></td>
        </tr>
        <?
		$i++;
		$tot_order_qty+=$row[csf('order_quantity')];
		$tot_order_val+=$row[csf('amount')];
		$tot_rec_qty+=$rec_qty;
		$tot_issue_qty+=$issue_qty;
		$tot_material_blce+=$mat_blnce_qty;
		$tot_prod_qty+=$prod_qty;
		$tot_del_qty+=$del_qty;
		if ($type==1)
		{
		$tot_yet_to_delv+=$yet_to_delv;
		}
		else if ($type==2)
		{
			$tot_yet_to_bill+=$yet_to_bill;
		}
		$tot_bill_qty+=$bill_qty;
		$tot_bill_amnt+=$bill_amnt;
		$tot_payment_amnt+=$order_wise_payment_received;
		$tot_balance+=$balance;
		
		if ($cbo_process==4)
		{
			$tot_batch_qty+=$batch_qty;
			$tot_dyeing_qty+=$dyeing_qty;
			
			$tot_tottal_batch_qty+=$batch_qty;
			$tot_total_dyeing_qty+=$dyeing_qty;
		}
		
		$tot_total_order_qty+=$row[csf('order_quantity')];
		$tot_total_order_val+=$row[csf('amount')];
		$tot_total_rec_qty+=$rec_qty;
		$tot_total_issue_qty+=$issue_qty;
		$tot_total_material_blce+=$mat_blnce_qty;
		$tot_total_prod_qty+=$prod_qty;
		$tot_total_del_qty+=$del_qty;
		if ($type==1)
		{
			$tot_total_yet_to_delv+=$yet_to_delv;
		}
		else if ($type==2)
		{
			$tot_total_yet_to_bill+=$yet_to_bill;
		}
		$tot_total_bill_qty+=$bill_qty;
		$tot_total_bill_amnt+=$bill_amnt;
		$tot_total_payment_amnt+=$order_wise_payment_received;
		$tot_total_balance+=$balance;
	}
	?>
    <tr class="tbl_bottom">
        <td colspan="6" align="right"><b>Process Total:</b></td>
        <td align="right"><? echo number_format($tot_order_qty); ?></td>
        <td align="right"><? echo number_format($tot_order_val); ?></td>
        <td align="right">&nbsp;</td>
        <td align="right">&nbsp;</td>
        <td align="right">&nbsp;</td>
        <td align="right"><? echo number_format($tot_rec_qty); ?></td>
        <td align="right"><? echo number_format($tot_issue_qty); ?></td>
        <td align="right"><? echo number_format($tot_material_blce) ?></td>
        <?
			if ($cbo_process==4)
			{
				?>
				<td><? echo number_format($tot_batch_qty); ?></td>
				<td><? echo number_format($tot_dyeing_qty); ?></td>
				<?
			}
        ?>
        <td align="right"><? echo number_format($tot_prod_qty); ?></td>
        <td align="right"><? echo number_format($tot_del_qty); ?></td>
		<? if ($type==1)
        {
        ?>
        <td align="right"><? echo number_format($tot_yet_to_delv); ?></td>
        <? } ?>
        <td align="right"><? echo number_format($tot_bill_qty); ?></td>
        <td align="right"><? echo number_format($tot_bill_amnt); ?></td>
        <? if ($type==2)
        {
        ?>
        <td align="right"><? echo number_format($tot_yet_to_bill); ?></td>
        <? } ?>
        <td align="right"><? echo number_format($tot_payment_amnt); ?></td>
        <td align="right"><? echo number_format($tot_balance); ?></td>
    </tr>
        <tr class="tbl_bottom">
            <td colspan="6" align="right">Grand Total:</td>
            <td align="right"><? echo number_format($tot_total_order_qty); ?></td>                            
            <td align="right"><? echo number_format($tot_total_order_val); ?></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><? echo number_format($tot_total_rec_qty); ?></td>
            <td><? echo number_format($tot_total_issue_qty); ?></td>
            <td><? echo number_format($tot_total_material_blce) ?></td>
            <?
                if ($cbo_process==4)
                {
                    ?>
                    <td><? echo number_format($tot_tottal_batch_qty); ?></td>
                    <td><? echo number_format($tot_total_dyeing_qty); ?></td>
                    <? 
                }
            ?>
            <td><? echo number_format($tot_total_prod_qty); ?></td>
            <td><? echo number_format($tot_total_del_qty); ?></td>
			<? if ($type==1)
            {
            ?>
            <td><? echo number_format($tot_total_yet_to_delv); ?></td>
            <? } ?>
            <td><? echo number_format($tot_total_bill_qty); ?></td>
            <td><? echo number_format($tot_total_bill_amnt); ?></td>
            <? if ($type==2)
            {
            ?>
            <td><? echo number_format($tot_total_yet_to_bill); ?></td>
            <? } ?>
            <td><? echo number_format($tot_total_payment_amnt); ?></td>
            <td><? echo number_format($tot_total_balance); ?></td>
        </tr>
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
    echo "$html**$filename"; 
    exit();
}

if($action=="material_desc_popup")
{
	echo load_html_head_contents("Material Description Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="11">Receive Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Receive ID</th>
                        <th width="70">Challan No</th>
                        <th width="70">Rec. Date</th>
                        <th width="60">Party</th>
                        <th width="80">Order No</th>
                        <th width="80">Category</th>
                        <th width="150">Description</th>
                        <th width="80">Receive Qty</th>
                        <th width="80">Bag/ Roll</th>
                        <th>Cone</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $party_arr=return_library_array( "select id, short_name from  lib_buyer",'id','short_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
				$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $i=0;
                
                $sql= "select a.sys_no, a.prefix_no_num, a.chalan_no, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia, sum(b.quantity) as quantity, sum(b.subcon_roll) as subcon_roll, sum(b.rec_cone) as rec_cone from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=2 and b.is_deleted=0 and a.trans_type=1 group by a.sys_no, a.prefix_no_num, a.chalan_no, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia order by a.sys_no, a.subcon_date";
               //echo $sql;
                $material_sql= sql_select($sql);
                foreach( $material_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					//if ($row[csf("color_id")]!=0 && $row[csf("gsm")]!=0  && $row[csf("grey_dia")]!=0  && $row[csf("fin_dia")]!=0 )
					//{
						$material_name=$row[csf("material_description")].', '.$color_arr[$row[csf("color_id")]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
					//}
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("prefix_no_num")];?> </td>
                    <td width="70"><? echo $row[csf("chalan_no")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("subcon_date")]);?> </td> 
                    <td width="60"><p><? echo $party_arr[$row[csf("party_id")]]; ?></p></td>
                    <td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                    <td align="center" width="150"><? echo $material_name; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
                    <td align="center" width="80"><? echo $row[csf("subcon_roll")];; ?></td>
                    <td><p><? echo $row[csf("rec_cone")]; ?></p></td>
                </tr>
                <? 
                $tot_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
        </div> 
	</fieldset>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="11">Return Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Return ID</th>
                        <th width="70">Challan No</th>
                        <th width="70">Return Date</th>
                        <th width="60">Party</th>
                        <th width="80">Order No</th>
                        <th width="80">Category</th>
                        <th width="150">Description</th>
                        <th width="80">Return Qty</th>
                        <th width="80">Bag/ Roll</th>
                        <th>Cone</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $party_arr=return_library_array( "select id, short_name from  lib_buyer",'id','short_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
				$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $i=0;
                
                $sql_ret= "select a.sys_no, a.prefix_no_num, a.chalan_no, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia, sum(b.quantity) as quantity, sum(b.subcon_roll) as subcon_roll, sum(b.rec_cone) as rec_cone from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trans_type=3 group by a.sys_no, a.prefix_no_num, a.chalan_no, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia order by a.sys_no, a.subcon_date";
               //echo $sql;
                $material_ret_sql= sql_select($sql_ret);
                foreach( $material_ret_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					//if ($row[csf("color_id")]!=0 && $row[csf("gsm")]!=0  && $row[csf("grey_dia")]!=0  && $row[csf("fin_dia")]!=0 )
					//{
					$material_name=$row[csf("material_description")].', '.$color_arr[$row[csf("color_id")]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
					//}
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("prefix_no_num")];?> </td>
                    <td width="70"><? echo $row[csf("chalan_no")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("subcon_date")]);?> </td> 
                    <td width="60"><p><? echo $party_arr[$row[csf("party_id")]]; ?></p></td>
                    <td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                    <td align="center" width="150"><? echo $material_name; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
                    <td align="center" width="80"><? echo $row[csf("subcon_roll")];; ?></td>
                    <td><p><? echo $row[csf("rec_cone")]; ?></p></td>
                </tr>
                <? 
                $tot_ret_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_ret_qty,2); ?></p></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
        </div> 
	</fieldset>
 </div> 
<?
exit();
}

if($action=="material_desc_iss_popup")
{
	echo load_html_head_contents("Material Issue Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="11">Issue Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Issue ID</th>
                        <th width="70">Challan No</th>
                        <th width="70">Issue Date</th>
                        <th width="60">Issue To</th>
                        <th width="80">Order No</th>
                        <th width="80">Category</th>
                        <th width="150">Description</th>
                        <th width="80">Issue Qty</th>
                        <th width="80">Bag/ Roll</th>
                        <th>Cone</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
				$company_array=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
				$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $i=0;
                
                $sql= "select a.sys_no, a.prefix_no_num, a.chalan_no, a.prod_source, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia, sum(b.quantity) as quantity, sum(b.subcon_roll) as subcon_roll, sum(b.rec_cone) as rec_cone from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trans_type=2 group by a.sys_no, a.prefix_no_num, a.chalan_no, a.prod_source, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia order by a.sys_no, a.subcon_date";
               //echo $sql;
                $material_sql= sql_select($sql);
                foreach( $material_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					//if ($row[csf("color_id")]!=0 && $row[csf("gsm")]!=0  && $row[csf("grey_dia")]!=0  && $row[csf("fin_dia")]!=0 )
					//{
						$material_name=$row[csf("material_description")].', '.$color_arr[$row[csf("color_id")]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
					//}
					$issue_to="";
					if($row[csf("prod_source")]==1) $issue_to=$company_array[$row[csf("party_id")]]; else if($row[csf("prod_source")]==3) $issue_to=$supplier_array[$row[csf("party_id")]]; else $issue_to="";
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("prefix_no_num")];?> </td>
                    <td width="70"><? echo $row[csf("chalan_no")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("subcon_date")]);?> </td> 
                    <td width="60"><p><? echo  $issue_to; ?></p></td>
                    <td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                    <td align="center" width="150"><? echo $material_name; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
                    <td align="center" width="80"><? echo $row[csf("subcon_roll")];; ?></td>
                    <td><p><? echo $row[csf("rec_cone")]; ?></p></td>
                </tr>
                <? 
                $tot_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
        </div> 
	</fieldset>
 </div> 
<?
exit();
}

if($action=="product_qty_pop_up")
{
	echo load_html_head_contents("Production Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	$order_id=$expData[0];
	$process_id=$expData[1];
?>
        <fieldset style="width:820px">
            <div style="width:100%;" align="center">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                    <thead>
                    	<tr>
                            <th width="30">SL</th>
                            <th width="60"><? if ($process_id==3) echo "Batch NO"; else echo "Sys ID" ?></th>
                            <th width="70">Prod. Date</th>
                            <th width="100">Party</th>
                            <th width="80">Order No</th>
                            <th width="130">Process</th>
                            <th width="150">Description</th>
                            <th width="">Prod. Qty</th>
                    	</tr>
                    </thead>
                </table>
            </div>  
            <div style="width:100%; max-height:330px; overflow-y:scroll" align="left">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                    <?
					$party_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
					$kniting_item_arr=return_library_array( "select id, const_comp from lib_subcon_charge",'id','const_comp');
					$po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
					$po_party_arr=return_library_array( "select b.id, a.party_id from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst",'id','party_id');
					$color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
                    $i=0;
					if ($process_id==1)
					{
						 $sql = "select '' as sys_id, production_date, order_id, gmts_item_id as item_id, '' as process, sum(production_qnty) as production_qnty from subcon_gmts_prod_dtls where order_id='$order_id' and production_type=1 group by production_date, order_id, gmts_item_id";
					}
					else if ($process_id==5)
					{
						$sql = "select '' as sys_id, production_date, order_id, gmts_item_id as item_id, '' as process, sum(production_qnty) as production_qnty from subcon_gmts_prod_dtls where order_id='$order_id' and production_type=2 group by production_date, order_id, gmts_item_id";

					}
					else if ($process_id==11)
					{
						$sql = "select '' as sys_id, production_date, order_id, gmts_item_id as item_id, '' as process, sum(production_qnty) as production_qnty from subcon_gmts_prod_dtls where order_id='$order_id' and production_type=4 group by production_date, order_id, gmts_item_id";

					}
					else if ($process_id==2)
					{
						$sql="select a.prefix_no_num as sys_id, a.product_no, a.party_id, a.product_date as production_date, b.order_id, b.process, b.cons_comp_id as item_id, b.color_id, sum(b.no_of_roll) as roll_qty, sum(b.product_qnty) as production_qnty from subcon_production_mst a, subcon_production_dtls b where b.order_id='$order_id' and b.product_type=2 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.prefix_no_num, a.product_no, a.party_id, a.product_date, b.order_id, b.process, b.cons_comp_id, b.color_id order by b.color_id";
					}
					else if($process_id==3)
					{
						if($db_type==0)
						{
							$sql="select b.batch_no as sys_id, a.process_end_date as production_date, c.po_id as order_id, c.item_description as item_id, a.process_id as process, sum(c.batch_qnty) as production_qnty from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.entry_form=38 and a.result=1 and a.load_unload_id=2 and b.entry_form=36 and c.po_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.batch_no, a.process_end_date, a.process_id, c.po_id, c.item_description ";
						}
						elseif($db_type==2)
						{
							$sql="select b.batch_no as sys_id, a.process_end_date as production_date, c.po_id as order_id, c.item_description as item_id, a.process_id as process, sum(c.batch_qnty) as production_qnty from pro_fab_subprocess a, pro_batch_create_mst b,  pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.entry_form=38 and a.result=1 and a.load_unload_id=2 and b.entry_form=36 and c.po_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.batch_no, a.process_end_date, a.process_id, c.po_id, c.item_description ";
						}
					}
					else if($process_id==4)
					{
						$sql = "select a.prefix_no_num as sys_id, a.product_no, a.product_date as production_date, a.party_id, c.order_id, b.process as process, b.fabric_description as item_id, sum(c.quantity) as production_qnty from subcon_production_mst a, subcon_production_dtls b, subcon_production_qnty c where a.id=b.mst_id and b.id=c.dtls_id and c.order_id in ($order_id) and b.product_type='$process_id' group by a.prefix_no_num, a.product_no, a.party_id, a.product_date, c.order_id, b.process, b.fabric_description";
					}
                   //echo $sql;
					$production_sql= sql_select($sql); $color_array=array(); $k=1;
					foreach( $production_sql as $row )
                    {
                        $i++;
                        if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						
						if ($process_id==1 || $process_id==5 || $process_id==11)
						{
							$item_name=$garments_item[$row[csf('item_id')]];
							$party_name=$party_arr[$po_party_arr[$row[csf("order_id")]]];
						}
						else if ($process_id==2)
						{
							$party_name=$party_arr[$row[csf("party_id")]];
							$process_name=$conversion_cost_head_array[$row[csf("process")]];
							$item_name=$kniting_item_arr[$row[csf('item_id')]];
						}
						else if ($process_id==3)
						{
							$party_name=$party_arr[$po_party_arr[$row[csf("order_id")]]];
							$process_name="";
							$process_id=explode(',',$row[csf('process')]);
							foreach($process_id as $val)
							{
								if($process_name=="") $process_name=$conversion_cost_head_array[$val]; else $process_name.=','.$conversion_cost_head_array[$val];
							}
							$item_name=$row[csf('item_id')];
						}
						else if ($process_id==4)
						{
							$party_name=$party_arr[$row[csf("party_id")]];
							$process_name="";
							$process_id=explode(',',$row[csf('process')]);
							foreach($process_id as $val)
							{
								if($process_name=="") $process_name=$conversion_cost_head_array[$val]; else $process_name.=','.$conversion_cost_head_array[$val];
							}
							$item_name=$row[csf('item_id')];
						}
						else
						{
							$item_name=$row[csf('item_id')];
						}
						if ($process_id==2)
						{
							if (!in_array($row[csf("color_id")],$color_array) )
							{
								if($k!=1)
								{
								?>
									<tr class="tbl_bottom">
										<td colspan="7" align="right"><b>Color Total:</b></td>
										<td align="right"><? echo number_format($color_qty); ?></td>
									</tr>
									<tr bgcolor="#dddddd">
										<td colspan="8" align="left" ><b>Color : <? echo $color_arr[$row[csf("color_id")]]; ?></b></td>
									</tr>
								<?
									unset($color_qty);
								}
								else
								{
									?>
									<tr bgcolor="#dddddd">
										<td colspan="8" align="left" ><b>Color : <? echo $color_arr[$row[csf("color_id")]]; ?></b></td>
									</tr>
									<?
								}					
								$color_array[]=$row[csf('color_id')];            
								$k++;
							}							
						   ?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="60" align="center"><? echo $row[csf("sys_id")];?> </td>
								<td width="70"><? echo change_date_format($row[csf("production_date")]);?> </td> 
								<td width="100"><p><? echo $party_name; ?></p></td>
								<td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
								<td align="center" width="130"><p><? echo $process_name; ?></p></td>
								<td align="center" width="150"><p><? echo $item_name; ?></p></td>
								<td align="right" width=""><? echo number_format($row[csf("production_qnty")],2); ?></td>
							</tr>
							<? 
							$color_qty+=$row[csf("production_qnty")];
							$tot_qty+=$row[csf("production_qnty")];
							
						}
						else
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="60" align="center"><? echo $row[csf("sys_id")];?> </td>
								<td width="70"><? echo change_date_format($row[csf("production_date")]);?> </td> 
								<td width="100"><p><? echo $party_name; ?></p></td>
								<td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
								<td align="center" width="130"><p><? echo $process_name; ?></p></td>
								<td align="center" width="150"><p><? echo $item_name; ?></p></td>
								<td align="right" width=""><? echo number_format($row[csf("production_qnty")],2); ?></td>
							</tr>
							<?
							$tot_qty+=$row[csf("production_qnty")];
						}
					}
					if ($process_id==2)
					{ 
                    ?>
                        <tr class="tbl_bottom">
                            <td colspan="7" align="right"><b>Color Total:</b></td>
                            <td align="right"><? echo number_format($color_qty); ?></td>
                        </tr>
					<? } ?>
                    <tr class="tbl_bottom">
                    	<td colspan="7" align="right">Total: </td>
                        <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    </tr>
                </table>
            </div> 
	</fieldset>
 </div> 
<?
exit();
}

if($action=="delivery_qty_pop_up")
{
	echo load_html_head_contents("Delivery Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
?>
        <fieldset style="width:820px">
            <div style="width:100%;" align="center">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                    <thead>
                    	<tr>
                            <th width="30">SL</th>
                            <th width="60">Delivery ID</th>
                            <th width="70">Delivery Date</th>
                            <th width="80">Batch No</th>
                            <th width="80">Order No</th>
                            <th width="80">Category</th>
                            <th width="150">Description</th>
                            <th width="">Delivery Qty</th>
                    	</tr>
                    </thead>
                </table>
            </div>  
            <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                    <?
					$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
					$po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
					$color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
					$kniting_item_arr=return_library_array( "select id, const_comp from lib_subcon_charge",'id','const_comp');
					$dye_fin_item_arr=return_library_array( "select id, item_description from pro_batch_create_dtls",'id','item_description');
                    $i=0;
                    $sql= "select a.delivery_prefix_num, a.delivery_date, a.party_id, b.order_id, b.process_id, b.item_id, b.color_id, b.batch_id, sum(b.delivery_qty) as quantity from  subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and b.process_id='$expData[1]' and a.status_active=1 and a.is_deleted=0 group by a.delivery_prefix_num, a.delivery_date, a.party_id, b.order_id, b.process_id, b.batch_id, b.item_id, b.color_id order by a.delivery_prefix_num, a.delivery_date";
                    //echo $sql;
					$production_sql= sql_select($sql); $color_array=array(); $k=1; $process_id=0;
					foreach( $production_sql as $row )
                    {
                        $i++;
                        if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						$process_id=$row[csf("process_id")];
						if ($row[csf("process_id")]==1 || $row[csf("process_id")]==5)
						{
							$item_name=$garments_item[$row[csf('item_id')]];
						}
						else if ($row[csf("process_id")]==2)
						{
							$item_name=$kniting_item_arr[$row[csf('item_id')]];
						}
						else if ($row[csf("process_id")]==3 || $row[csf("process_id")]==4)
						{
							$item_name=$dye_fin_item_arr[$row[csf('item_id')]];
						}
						
						if ($row[csf("process_id")]==2)
						{
							if (!in_array($row[csf("color_id")],$color_array) )
							{
								if($k!=1)
								{
								?>
									<tr class="tbl_bottom">
										<td colspan="7" align="right"><b>Color Total:</b></td>
										<td align="right"><? echo number_format($color_qty); ?></td>
									</tr>
									<tr bgcolor="#dddddd">
										<td colspan="8" align="left" ><b>Color : <? echo $color_arr[$row[csf("color_id")]]; ?></b></td>
									</tr>
								<?
									unset($color_qty);
								}
								else
								{
									?>
									<tr bgcolor="#dddddd">
										<td colspan="8" align="left" ><b>Color : <? echo $color_arr[$row[csf("color_id")]]; ?></b></td>
									</tr>
									<?
								}					
								$color_array[]=$row[csf('color_id')];            
								$k++;
							}							
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="60"><? echo $row[csf("delivery_prefix_num")];?> </td>
								<td width="70"><? echo change_date_format($row[csf("delivery_date")]);?> </td> 
								<td width="80"><p><? echo $batch_arr[$row[csf("batch_id")]]; ?></p></td>
								<td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
								<td align="center" width="80"><? echo $production_process[$row[csf("process_id")]]; ?></td>
								<td align="center" width="150"><p><? echo $item_name; ?></p></td>
								<td align="right" width=""><? echo number_format($row[csf("quantity")],2); ?></td>
							</tr>
							<? 
							$color_qty+=$row[csf("quantity")];
							$tot_qty+=$row[csf("quantity")];
						}
						else
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="60"><? echo $row[csf("delivery_prefix_num")];?> </td>
								<td width="70"><? echo change_date_format($row[csf("delivery_date")]);?> </td> 
								<td width="80"><p><? echo $batch_arr[$row[csf("batch_id")]]; ?></p></td>
								<td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
								<td align="center" width="80"><? echo $production_process[$row[csf("process_id")]]; ?></td>
								<td align="center" width="150"><p><? echo $item_name; ?></p></td>
								<td align="right" width=""><? echo number_format($row[csf("quantity")],2); ?></td>
							</tr>
							<? 
							$tot_qty+=$row[csf("quantity")];
						}
					} 
					if($process_id==2)
					{
					?>
                        <tr class="tbl_bottom">
                            <td colspan="7" align="right"><b>Color Total:</b></td>
                            <td align="right"><? echo number_format($color_qty); ?></td>
                        </tr>
                    <?
					}
					?>
                    <tr class="tbl_bottom">
                    	<td colspan="7" align="right">Total: </td>
                        <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    </tr>
                </table>
            </div> 
	</fieldset>
 </div> 
<?
exit();
}

if($action=="bill_qty_pop_up")
{
	echo load_html_head_contents("Delivery Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Bill ID</th>
                        <th width="70">Bill Date</th>
                        <th width="100">Party</th>
                        <th width="80">Order No</th>
                        <th width="80">Category</th>
                        <th width="150">Description</th>
                        <th width="80">Bill Qty</th>
                        <th>Amount</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $party_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
				$kniting_item_arr=return_library_array( "select id, const_comp from lib_subcon_charge",'id','const_comp');
				$dye_fin_item_arr=return_library_array( "select id, item_description from pro_batch_create_dtls",'id','item_description');
                $i=0;
                $sql= "select a.bill_no, a.bill_date, a.party_id, b.order_id, b.process_id, b.item_id, sum(b.delivery_qty) as quantity, sum(b.amount) as amount from  subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and b.process_id='$expData[1]' and a.status_active=1 and a.is_deleted=0  group by a.bill_no, a.bill_date, a.party_id, b.order_id, b.process_id, b.item_id order by a.bill_no, a.bill_date";
                //echo $sql;
                $production_sql= sql_select($sql);
                foreach( $production_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					if ($row[csf("process_id")]==1 || $row[csf("process_id")]==5)
					{
						$item_name=$garments_item[$row[csf('item_id')]];
					}
					else if ($row[csf("process_id")]==2)
					{
						$item_name=$kniting_item_arr[$row[csf('item_id')]];
					}
					else if ($row[csf("process_id")]==3 || $row[csf("process_id")]==4)
					{
						$item_name=$dye_fin_item_arr[$row[csf('item_id')]];
					}
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="100"><? echo $row[csf("bill_no")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("bill_date")]);?> </td> 
                    <td width="100"><p><? echo $party_arr[$row[csf("party_id")]]; ?></p></td>
                    <td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $production_process[$row[csf("process_id")]]; ?></td>
                    <td align="center" width="150"><? echo $item_name; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
                    <td align="right" width=""><? echo number_format($row[csf("amount")],2); ?></td>
                </tr>
                <? 
                $tot_qty+=$row[csf("quantity")];
                $tot_amount+=$row[csf("amount")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="7" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    <td align="right"><p><? echo number_format($tot_amount,2); ?></p></td>
                </tr>
            </table>
        </div> 
	</fieldset>
 </div> 
<?
exit();
}

if($action=="image_view_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Work Progress Info","../../../", 1, 1, $unicode);
	//echo "select master_tble_id,image_location from common_photo_library where form_name='sub_contract_order_entry' and file_type=1 and master_tble_id='$id'";

	$imge_data=sql_select("select id,master_tble_id,image_location from common_photo_library where form_name='sub_contract_order_entry' and file_type=1 and master_tble_id='$id'");
	?>
	<table>
        <tr>
			<?
            foreach($imge_data as $row)
            {
				?>
                    <td><img src='../../../<? echo $imge_arr[$row[csf("id")]]; ?>' height='100px' width='100px' /></td>
				<?
            }
            ?>
        </tr>
	</table>
	<?
	exit();
}

if($action=="batch_qty_pop_up")
{
	echo load_html_head_contents("Batch Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	$order_id=$expData[0];
	//$process_id=$expData[1];
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
	?>
    <fieldset style="width:800px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="80">Batch No</th>
                        <th width="30">Ext.</th>
                        <th width="65">Batch Date</th>
                        <th width="100">Color</th>
                        <th width="100">Order</th>
                        <th width="100">Rec. Challan</th>
                        <th width="180">Description</th>
                        <th width="">Batch Qty</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:330px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >    
			<?
				$sql_batch="Select a.batch_no, a.extention_no, a.batch_date, a.color_id, b.po_id, b.item_description, b.rec_challan, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form=36 and b.po_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.batch_no, a.extention_no, a.batch_date, a.color_id, b.po_id, b.item_description, b.rec_challan";
				$sql_batch_result=sql_select($sql_batch); $i=0;
				foreach ($sql_batch_result as $row)
				{
					$i++;
					if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="30"><? echo $i; ?></td>
						<td width="80" align="center"><? echo $row[csf("batch_no")];?> </td>
						<td width="30" align="center"><? echo $row[csf("extention_no")];?> </td>
						<td width="65"><? echo change_date_format($row[csf("batch_date")]);?> </td> 
						<td width="100"><p><? echo $color_arr[$row[csf("color_id")]];?></p></td>
						<td width="100"><? echo $po_arr[$row[csf("po_id")]]; ?></td>
						<td width="100"><p><? echo $row[csf("rec_challan")]; ?></p></td>
						<td width="180"><p><? echo $row[csf("item_description")]; ?></p></td>
						<td align="right" width=""><? echo number_format($row[csf("batch_qnty")],2); ?></td>
					</tr>
					<?
					$tot_batch_qnty+=$row[csf("batch_qnty")];
				}
				?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_batch_qnty,2); ?></p></td>
                </tr>
            </table>
        </div> 
	</fieldset>
 </div> 
<?
exit();
}

if($action=="payment_rec_pop_up")
{
	echo load_html_head_contents("Payment Receive Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	$order_id=$expData[0];
	$order_bill_amount=$expData[1];
	//$process_id=$expData[1];
	$po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Rec. No</th>
                        <th width="120">Party</th>
                        <th width="65">Rec. Date</th>
                        <th width="80">Instrument</th>
                        <th width="60">Currency</th>
                        <th width="120">Bill No</th>
                        <th width="80">Order No</th>
                        <th width="65">Bill Date</th>
                        <th width="">Rec. Amount</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:330px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >    
			<?
			$order_wise_tot_bill="select a.id, d.order_id, b.bill_id, b.bill_amount as bill_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.order_id='$order_id' and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 order by a.id asc";
			$order_wise_tot_bill_result=sql_select($order_wise_tot_bill);
			foreach ($order_wise_tot_bill_result as $row)
			{
				$order_wise_tot_bill_arr2[$row[csf('order_id')]][$row[csf('bill_id')]][$row[csf('id')]]=$row[csf('bill_amount')];
			}

			$sum=0;
			foreach ($order_wise_tot_bill_arr2 as $key=>$value) 
			{
				foreach ($value as $val) 
				{
					foreach ($val as $val2) 
					{
						 $sum+=$val2;
						 break;
					}
				}
				$order_wise_tot_bill_arr[$key]=$sum;
				$sum=0;
			}

				//$payment_sql="select a.prefix_no_num, a.receive_no, a.party_name, a.receipt_date, a.instrument_id, a.currency_id, c.bill_no, c.bill_date, d.order_id, sum(b.total_adjusted) as rec_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and d.order_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by a.prefix_no_num, a.receive_no, a.party_name, a.receipt_date, a.instrument_id, a.currency_id, c.bill_no, c.bill_date, d.order_id";

			$payment_sql="select a.prefix_no_num, a.receive_no, a.party_name, a.receipt_date, a.instrument_id, a.currency_id, c.bill_no, c.bill_date, d.order_id, b.total_adjusted as rec_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and d.order_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by a.prefix_no_num, a.receive_no, a.party_name, a.receipt_date, a.instrument_id, a.currency_id, c.bill_no, c.bill_date, d.order_id,b.total_adjusted";

				$payment_sql_result=sql_select($payment_sql); $i=0;
				foreach ($payment_sql_result as $row)
				{
					$i++;
					if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="30"><? echo $i; ?></td>
						<td width="100" align="center"><? echo $row[csf("receive_no")];?> </td>
						<td width="120" align="center"><? echo $buyer_arr[$row[csf("party_name")]];?> </td>
						<td width="65"><? echo change_date_format($row[csf("receipt_date")]);?> </td> 
						<td width="80"><p><? echo $instrument_payment[$row[csf("instrument_id")]];?></p></td>
						<td width="60"><? echo $currency[$row[csf("currency_id")]]; ?></td>
						<td width="120"><p><? echo $row[csf("bill_no")]; ?></p></td>
                        <td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
						<td width="65"><? echo change_date_format($row[csf("bill_date")]);?> </td> 
						<td align="right" width="">
							<? 
							$received_amount = ($row[csf("rec_amount")]/$order_wise_tot_bill_arr[$order_id])*$order_bill_amount;
							echo number_format($received_amount,2); 
							
							//echo number_format($row[csf("rec_amount")],2); 
							?>
						</td>
					</tr>
					<?
					$tot_rec_amount+=$received_amount;
				}
				?>
                <tr class="tbl_bottom">
                    <td colspan="9" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_rec_amount,2); ?></p></td>
                </tr>
            </table>
        </div> 
	</fieldset>
 </div> 
<?
exit();
}


if($action=="order_desc_popup")
{
	echo load_html_head_contents("Order Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="11">Order Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Order No</th>
                        <th width="70">Category</th>
                        <th width="120">Item Description </th>
                        <th width="80">Color</th>
                        <th width="60">Size</th>
                        <th width="80">Receive Date</th>
                        <th width="50">Rate</th>
                        <th width="93">Quantity</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
				//$company_array=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

                $item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');	
				$size_arr=return_library_array( "select id,size_name from  lib_size",'id','size_name');
				$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $i=0;

                $sql="select a.party_id, b.order_no, b.order_rcv_date, b.main_process_id, c.item_id, c.color_id, c.size_id, c.qnty, c.rate, c.gsm, c.grey_dia, c.finish_dia, c.dia_width_type from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where c.mst_id=a.id and c.order_id=b.id and a.subcon_job=b.job_no_mst and b.id=$expData[0] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
              
                $order_dtls_sql= sql_select($sql);
                foreach( $order_dtls_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";

                    $process_id=$row[csf('main_process_id')];
					
						//$material_name=$row[csf("material_description")].', '.$color_arr[$row[csf("color_id")]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("order_no")];?> </td>
                    <td align="center" width="70"><? echo $production_process[$row[csf("main_process_id")]];?> </td>
                    <td width="120">
                    	<? 
			                if($process_id==2 || $process_id==3 || $process_id==4 || $process_id==6 || $process_id==7)
							{
								echo $item_arr[$row[csf('item_id')]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("finish_dia")];	
							}
							else
							{
								echo $garments_item[$row[csf('item_id')]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("finish_dia")];
							}
                    	?> 
                    </td> 
                    <td align="center" width="80"><p><? echo  $color_arr[$row[csf("color_id")]]; ?></p></td>
                    <td align="center" width="60"><? echo $size_arr[$row[csf("size_id")]]; ?></td>
                    <td align="center" width="80"><? echo change_date_format($row[csf("order_rcv_date")]); ?></td>
                    
                    <td align="right" width="50"><? echo $row[csf("rate")]; ?> &nbsp; </td>
                    <td align="right" width="80"><? echo number_format($row[csf("qnty")]); ?> &nbsp;</td>
                   
                </tr>
                <? 
                $tot_qty+=$row[csf("qnty")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: &nbsp;</td>
                    <td align="right"><p><? echo number_format($tot_qty); ?> &nbsp; </p></td>
                </tr>
            </table>
        </div> 
	</fieldset>
 </div> 
<?
exit();
}

?>