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
        <? 
        $data_array=sql_select($sql);
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
			<? 
			$i++; 
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
    	<? 
    	$data_array=sql_select($sql);
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
			<? 
			$i++; 
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
	$job_no        = str_replace("'","",$txt_job_no);
	$txt_style_ref = str_replace("'","",$txt_style_ref);
	$txt_order_no  = str_replace("'","",$txt_order_no);
	$year_id       = str_replace("'","",$cbo_year);
	$cbo_buyer_id  = str_replace("'","",$cbo_buyer_id);
	$cbo_process   = str_replace("'","",$cbo_process_id);
	//$date_from     = str_replace("'", "", trim($txt_date_from));
	//$date_to       = str_replace("'", "", trim($txt_date_to));

	if ($year_id!=0) $year_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id"; else $year_cond="";
	if ($cbo_buyer_id!=0) $buyer_id_cond=" and a.party_id='$cbo_buyer_id'"; else $buyer_id_cond="";		
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	if ($txt_style_ref!='') $style_ref_cond=" and b.cust_style_ref like '%$txt_style_ref%'"; else $style_ref_cond="";
	if ($txt_order_no!='') $order_no_cond=" and b.order_no like '%$txt_order_no%'"; else $order_no_cond="";
	if ($cbo_process==0) $process_id_cond=""; else $process_id_cond=" and b.main_process_id=$cbo_process_id";
	
	//if($date_from == "" && $date_to=="") $date_cond=""; else $date_cond=" and b.delivery_date between $txt_date_from and $txt_date_to";
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") {$date_cond="";} else {$date_cond=" and b.delivery_date between $txt_date_from and $txt_date_to";}
	$date_from = str_replace("'", "", $txt_date_from);
	$date_to = str_replace("'", "", $txt_date_to);		

	// MAIN QUERY 	
	$job_sql = "SELECT a.id as pic_id, a.job_no_prefix_num, a.subcon_job, a.party_id, b.order_no, b.order_quantity, b.amount, b.order_rcv_date, b.delivery_date, b.main_process_id, b.order_uom, b.id, b.cust_style_ref 
	from subcon_ord_mst a, subcon_ord_dtls b
	where a.subcon_job=b.job_no_mst and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_no_cond $style_ref_cond $date_cond $job_no_cond $process_id_cond $buyer_id_cond
	group by a.job_no_prefix_num, a.subcon_job, a.company_id, a.party_id, b.order_no, b.order_quantity, b.amount, b.order_rcv_date, b.delivery_date, b.main_process_id, b.order_uom, b.id, b.cust_style_ref, a.id  
	order by b.main_process_id, a.job_no_prefix_num, b.order_no, b.delivery_date";
	//echo $job_sql;
	$job_sql_result = sql_select($job_sql);
	if(count($job_sql_result)==0)
	{
		?>
		<div style="margin: 0 auto; font-size: 20px; color: red; text-align: center;">Data not found! Please try again.</div>
		<?
	}

	$order_id_array = array();
	foreach ($job_sql_result as $val) 
	{
		$order_id_array[$val[csf('id')]] = $val[csf('id')];
	}
	$ordeIDs = implode(",", $order_id_array);


	// FOR grey prod qty 
	$inventory_grey_prod_array=array();
	$inv_grey_sql="SELECT b.order_id, sum(b.quantity) as quantity from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.item_category_id=13 and b.order_id in($ordeIDs) group by b.order_id";
	
	$inv_grey_sql_result = sql_select($inv_grey_sql);
	foreach ($inv_grey_sql_result as $row)
	{
		$inventory_grey_prod_array[$row[csf('order_id')]]=$row[csf('quantity')];
	}

	// FOR DELIVERY QTY
	$del_date_cond = str_replace("b.delivery_date", "a.delivery_date", $date_cond);
	$delivery_array=array();
	$delivery_sql="SELECT b.order_id,
	sum(CASE WHEN b.process_id='1' THEN  b.delivery_qty else 0 END) AS cutting,
	sum(CASE WHEN b.process_id='1' THEN  b.reject_qty  else 0 END) AS cutting_rej,

	sum(CASE WHEN b.process_id='2' THEN  b.delivery_qty  else 0 END) AS kniting,
	sum(CASE WHEN b.process_id='2' THEN  b.reject_qty  else 0 END) AS kniting_rej,

	sum(CASE WHEN b.process_id='3' THEN  b.delivery_qty  else 0 END) AS dyeing,
	sum(CASE WHEN b.process_id='3' THEN  b.reject_qty  else 0 END) AS dyeing_rej,

	sum(CASE WHEN b.process_id='4' THEN  b.delivery_qty  else 0 END) AS finishing,
	sum(CASE WHEN b.process_id='4' THEN  b.reject_qty else 0  END) AS finishing_rej,

	sum(CASE WHEN b.process_id='5' THEN  b.delivery_qty  else 0 END) AS sewing,
	sum(CASE WHEN b.process_id='5' THEN  b.reject_qty  else 0 END) AS sewing_rej,

	sum(CASE WHEN b.process_id='6' THEN  b.delivery_qty else 0  END) AS fab_print,
	sum(CASE WHEN b.process_id='6' THEN  b.reject_qty else 0  END) AS fab_print_rej,

	sum(CASE WHEN b.process_id='7' THEN  b.delivery_qty  else 0 END) AS washing,
	sum(CASE WHEN b.process_id='7' THEN  b.reject_qty else 0  END) AS washing_rej,

	sum(CASE WHEN b.process_id='8' THEN  b.delivery_qty  else 0 END) AS printing,
	sum(CASE WHEN b.process_id='8' THEN  b.reject_qty else 0  END) AS printing_rej,

	sum(CASE WHEN b.process_id='9' THEN  b.delivery_qty else 0  END) AS Embroidery,
	sum(CASE WHEN b.process_id='9' THEN  b.reject_qty  else 0 END) AS Embroidery_rej,

	sum(CASE WHEN b.process_id='10' THEN  b.delivery_qty  else 0 END) AS Iron,
	sum(CASE WHEN b.process_id='10' THEN  b.reject_qty else 0  END) AS Iron_rej,

	sum(CASE WHEN b.process_id='11' THEN  b.delivery_qty else 0  END) AS Gmts_Finishing,
	sum(CASE WHEN b.process_id='11' THEN  b.reject_qty  else 0 END) AS Gmts_Finishing_rej,

	sum(CASE WHEN b.process_id='12' THEN  b.delivery_qty else 0  END) AS Gmts_Dyeing,
	sum(CASE WHEN b.process_id='12' THEN  b.reject_qty else 0  END) AS Gmts_Dyeing_rej,

	sum(CASE WHEN b.process_id='13' THEN  b.delivery_qty  else 0 END) AS Poly ,
	sum(CASE WHEN b.process_id='13' THEN  b.reject_qty else 0  END) AS Poly_rej 

	from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.order_id in($ordeIDs) group by b.order_id";
	//echo $delivery_sql;
	$delivery_sql_result = sql_select($delivery_sql);
	foreach ($delivery_sql_result as $row)
	{
		$delivery_array[$row[csf('order_id')]]['item_id']=$row[csf('item_id')];
		$delivery_array[$row[csf('order_id')]]['cutting']=$row[csf('cutting')];
		$delivery_array[$row[csf('order_id')]]['cutting_rej']=$row[csf('cutting_rej')];
		$delivery_array[$row[csf('order_id')]]['kniting']=$row[csf('kniting')];
		$delivery_array[$row[csf('order_id')]]['kniting_rej']=$row[csf('kniting_rej')];

		$delivery_array[$row[csf('order_id')]]['dyeing']=$row[csf('dyeing')];
		$delivery_array[$row[csf('order_id')]]['dyeing_rej']=$row[csf('dyeing_rej')];

		$delivery_array[$row[csf('order_id')]]['finishing']=$row[csf('finishing')];
		$delivery_array[$row[csf('order_id')]]['finishing_rej']=$row[csf('finishing_rej')];

		$delivery_array[$row[csf('order_id')]]['sewing']=$row[csf('sewing')];
		$delivery_array[$row[csf('order_id')]]['sewing_rej']=$row[csf('sewing_rej')];

		$delivery_array[$row[csf('order_id')]]['fab_print']=$row[csf('fab_print')];
		$delivery_array[$row[csf('order_id')]]['fab_print_rej']=$row[csf('fab_print_rej')];

		$delivery_array[$row[csf('order_id')]]['washing']=$row[csf('washing')];
		$delivery_array[$row[csf('order_id')]]['washing_rej']=$row[csf('washing_rej')];

		$delivery_array[$row[csf('order_id')]]['printing']=$row[csf('printing')];
		$delivery_array[$row[csf('order_id')]]['printing_rej']=$row[csf('printing_rej')];

		$delivery_array[$row[csf('order_id')]]['Embroidery']=$row[csf('Embroidery')];
		$delivery_array[$row[csf('order_id')]]['Embroidery_rej']=$row[csf('Embroidery_rej')];

		$delivery_array[$row[csf('order_id')]]['Iron']=$row[csf('Iron')];
		$delivery_array[$row[csf('order_id')]]['Iron_rej']=$row[csf('Iron_rej')];

		$delivery_array[$row[csf('order_id')]]['Gmts_Finishing']=$row[csf('Gmts_Finishing')];
		$delivery_array[$row[csf('order_id')]]['Gmts_Finishing_rej']=$row[csf('Gmts_Finishing_rej')];

		$delivery_array[$row[csf('order_id')]]['Gmts_Dyeing']=$row[csf('Gmts_Dyeing')];
		$delivery_array[$row[csf('order_id')]]['Gmts_Dyeing_rej']=$row[csf('Gmts_Dyeing_rej')];

		$delivery_array[$row[csf('order_id')]]['Poly']=$row[csf('Poly')];
		$delivery_array[$row[csf('order_id')]]['Poly_rej']=$row[csf('Poly_rej')];
		
	}

	// FOR FAB. PRODUCTION 
	$fab_production_array=array();
	$fab_production_sql="SELECT c.order_id,
	sum(CASE WHEN c.product_type='4' THEN  c.quantity END) AS finishing,
	sum(CASE WHEN c.product_type='8' THEN  c.quantity END) AS printing
	from  subcon_production_mst a, subcon_production_dtls b, subcon_production_qnty c where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1  and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.order_id in($ordeIDs) group by c.order_id";

	$fab_production_sql_result = sql_select($fab_production_sql);
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
	
	// FOR KNITING PRODUCTION 
	$knit_production_array=array();
	$knit_production_sql="SELECT b.order_id, sum(b.product_qnty) AS kniting
	from  subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and b.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.order_id in($ordeIDs) group by b.order_id";
	$knit_production_sql_result = sql_select($knit_production_sql);
	foreach ($knit_production_sql_result as $row)
	{
		$knit_production_array[$row[csf('order_id')]]['kniting']=$row[csf('kniting')];
	}	
	
	// FOR DYEING PRODUCTION 
	$dying_data_array=array();
	if ($db_type==0)
	{
		$dying_sql="SELECT c.po_id, sum(c.batch_qnty) as production_qnty from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.entry_form=38 and a.result=1 and a.load_unload_id=2 and b.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_id in($ordeIDs) group by c.po_id ";
	}
	elseif($db_type==2)
	{
		$dying_sql="SELECT c.po_id, sum(c.batch_qnty) as production_qnty from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.entry_form=38 and a.result=1 and a.load_unload_id=2 and b.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_id in($ordeIDs) group by c.po_id ";
	}
	$dying_sql_result = sql_select($dying_sql);
	foreach ($dying_sql_result as $row)
	{
		$dying_data_array[$row[csf('po_id')]]=$row[csf('production_qnty')];
	}
	
	
	// FOR GMTS PRODUCTION 
	$gmt_production_array=array();
	$gmt_production_sql="SELECT order_id,
	sum(CASE WHEN production_type='1' THEN  production_qnty END) AS cutting,
	sum(CASE WHEN production_type='2' THEN  production_qnty END) AS sewing,
	sum(CASE WHEN production_type='4' THEN  production_qnty END) AS Gmts_Finishing
	from subcon_gmts_prod_dtls where status_active=1 and is_deleted=0 and order_id in($ordeIDs) group by order_id";
	$gmt_production_sql_result=sql_select($gmt_production_sql);
	foreach ($gmt_production_sql_result as $row)
	{
		$gmt_production_array[$row[csf('order_id')]]['cutting']=$row[csf('cutting')];
		$gmt_production_array[$row[csf('order_id')]]['sewing']=$row[csf('sewing')];
		$gmt_production_array[$row[csf('order_id')]]['Gmts_Finishing']=$row[csf('Gmts_Finishing')];
	}
	

	// FOR BILL 
	$in_bill_qty_array=array();
	$in_bill_amnt_array=array(); $bill_order_arr=array(); $bill_amt_arr=array();
	$in_bill_sql="SELECT order_id, mst_id, sum(amount) as amount,
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
	
	from subcon_inbound_bill_dtls where status_active=1 and is_deleted=0 and order_id in($ordeIDs) group by order_id, mst_id";
	$in_bill_sql_result = sql_select($in_bill_sql);
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
		

	$order_wise_tot_paid_arr=array();
	$order_wise_tot_bill_arr2=array();
	$order_wise_tot_bill_arr=array();

	$order_wise_tot_paid="SELECT d.order_id, b.total_adjusted as rec_amount, sum(b.bill_amount) as bill_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.order_id in($ordeIDs) group by d.order_id,b.total_adjusted";

	$order_wise_tot_paid_result = sql_select($order_wise_tot_paid);
	foreach ($order_wise_tot_paid_result as $row)
	{
		$order_wise_tot_paid_arr[$row[csf('order_id')]]+=$row[csf('rec_amount')];
	}

	$order_wise_tot_bill="SELECT a.id, d.order_id, b.bill_id, b.bill_amount as bill_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.order_id in($ordeIDs) order by a.id asc";
	$order_wise_tot_bill_result = sql_select($order_wise_tot_bill);
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

	$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library",'master_tble_id','image_location');	
	
	ob_start();	
	?>
	
    <div style="margin-left: 5px;">
    	<style>
    		.wrd_brk{word-break: break-all;}.left{text-align: left;}
    		.center{text-align: center;}.right{text-align: right;}
		</style>
        <table width="1550" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
            <thead>
                <th width="50" class="wrd_brk">SL</th>
                <th width="100" class="wrd_brk">Job No</th>
                <th width="100" class="wrd_brk">Party</th>
                <th width="100" class="wrd_brk">Order No</th>                         
                <th width="100" class="wrd_brk">Style Name</th>
                <th width="100" class="wrd_brk">Order Quantity</th>
                <th width="100" class="wrd_brk">Order Value</th>
                <th width="100" class="wrd_brk">UOM</th>
                <th width="100" class="wrd_brk">Delivery Date</th>
                <th width="100" class="wrd_brk">Prod. Qty</th>
                <th width="100" class="wrd_brk">Delivery Qty</th>
                <th width="100" class="wrd_brk">Yet To Delv.</th>
                <th width="100" class="wrd_brk">Bill Qty</th>
                <th width="100" class="wrd_brk">Bill Amount</th>
                <th width="100" class="wrd_brk">Payment Rec.</th>
                <th width="100" class="wrd_brk">Rec. Balance</th>
            </thead>
        </table>
	    <div style="max-height:400px; overflow-y:scroll; width: 1570px;" id="scroll_body">
	        <table width="<? echo $tbl_width; ?>" border="1" class="rpt_table" rules="all" id="">
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
							<td colspan="5" align="right"><b>Process Total:</b></td>
							<td align="right"><? echo number_format($tot_order_qty); ?></td>
							<td align="right"><? echo number_format($tot_order_val); ?></td>
							<td align="right">&nbsp;</td>
							<td align="right">&nbsp;</td>
							<td align="right"><? echo number_format($tot_prod_qty); ?></td>
							<td align="right"><? echo number_format($tot_del_qty); ?></td>

		                    <td align="right"><? echo number_format($tot_yet_to_delv); ?></td>
							<td align="right"><? echo number_format($tot_bill_qty); ?></td>
							<td align="right"><? echo number_format($tot_bill_amnt); ?></td>
							<td align="right"><? echo number_format($tot_payment_amnt); ?></td>
		                    <td align="right"><? echo number_format($tot_balance); ?></td>
						</tr>
						<tr bgcolor="#dddddd">
							<td align="left" colspan="16" ><b>Process : <? echo $production_process[$row[csf("main_process_id")]]; ?></b></td>
						</tr>
						<?
						unset($tot_order_qty);
						unset($tot_order_val);
						unset($tot_prod_qty);
						unset($tot_del_qty);
						unset($tot_bill_qty);
						unset($tot_bill_amnt);
						unset($tot_payment_amnt);
						unset($tot_yet_to_delv);
						unset($tot_balance);
					}
					else
					{
						?>
						<tr bgcolor="#dddddd">
							<td align="left" colspan="16" ><b>Process : <? echo $production_process[$row[csf("main_process_id")]]; ?></b></td>
						</tr>
						<?
					}					
					$process_array[]=$row[csf('main_process_id')];            
					$k++;
				}

		        if ($i%2==0)  $bgcolor="#E9F3FF";else $bgcolor="#FFFFFF";
				$prod_qty=0;  $bill_qty=0; $bill_amnt=0; $pay_rec=0;

				if ($row[csf('main_process_id')]==1)
				{
					$prod_qty = $gmt_production_array[$row[csf('id')]]['cutting'];
					$del_qty  = $delivery_array[$row[csf('id')]]['cutting'];
					$bill_qty = $in_bill_qty_array[$row[csf('id')]]['cutting'];
					$bill_amnt= $in_bill_amnt_array[$row[csf('id')]]['am_cutting'];
				}
				else if ($row[csf('main_process_id')]==2)
				{
					$prod_qty = $knit_production_array[$row[csf('id')]]['kniting'];
					$del_qty  = $delivery_array[$row[csf('id')]]['kniting'];
					$bill_qty = $in_bill_qty_array[$row[csf('id')]]['kniting'];
					$bill_amnt= $in_bill_amnt_array[$row[csf('id')]]['am_kniting'];
				}
				else if ($row[csf('main_process_id')]==3)
				{
					$prod_qty = $dying_data_array[$row[csf('id')]];
					$del_qty  = $delivery_array[$row[csf('id')]]['dyeing'];
					$bill_qty = $in_bill_qty_array[$row[csf('id')]]['dyeing'];
					$bill_amnt= $in_bill_amnt_array[$row[csf('id')]]['am_dyeing'];
				}
				else if ($row[csf('main_process_id')]==4)
				{
					$prod_qty  = $fab_production_array[$row[csf('id')]]['finishing'];
					$del_qty   = $delivery_array[$row[csf('id')]]['finishing'];
					$bill_qty  = $in_bill_qty_array[$row[csf('id')]]['finishing'];
					$bill_amnt = $in_bill_amnt_array[$row[csf('id')]]['am_finishing'];
					$grey_prod = $inventory_grey_prod_array[$row[csf('id')]];
				}
				else if ($row[csf('main_process_id')]==5)
				{
					$prod_qty = $gmt_production_array[$row[csf('id')]]['sewing'];
					$del_qty  = $delivery_array[$row[csf('id')]]['sewing'];
					$bill_qty = $in_bill_qty_array[$row[csf('id')]]['sewing'];
					$bill_amnt= $in_bill_amnt_array[$row[csf('id')]]['am_sewing'];
				}
				else if ($row[csf('main_process_id')]==6)
				{
					$prod_qty = $gmt_production_array[$row[csf('id')]]['fab_print'];
					$del_qty  = $delivery_array[$row[csf('id')]]['fab_print'];
					$bill_qty = $in_bill_qty_array[$row[csf('id')]]['fab_print'];
					$bill_amnt= $in_bill_amnt_array[$row[csf('id')]]['am_fab_print'];
				}
				else if ($row[csf('main_process_id')]==7)
				{
					$prod_qty = $gmt_production_array[$row[csf('id')]]['washing'];
					$del_qty  = $delivery_array[$row[csf('id')]]['washing'];
					$bill_qty = $in_bill_qty_array[$row[csf('id')]]['washing'];
					$bill_amnt= $in_bill_amnt_array[$row[csf('id')]]['am_washing'];
				}
				else if ($row[csf('main_process_id')]==8)
				{
					$prod_qty = $gmt_production_array[$row[csf('id')]]['printing'];
					$del_qty  = $delivery_array[$row[csf('id')]]['printing'];
					$bill_qty = $in_bill_qty_array[$row[csf('id')]]['printing'];
					$bill_amnt= $in_bill_amnt_array[$row[csf('id')]]['am_printing'];
				}
				else if ($row[csf('main_process_id')]==9)
				{
					//$prod_qty=$gmt_production_array[$row[csf('id')]]['Embroidery'];
					$del_qty  = $delivery_array[$row[csf('id')]]['Embroidery'];
					$bill_qty = $in_bill_qty_array[$row[csf('id')]]['Embroidery'];
					$bill_amnt= $in_bill_amnt_array[$row[csf('id')]]['am_Embroidery'];
				}
				else if ($row[csf('main_process_id')]==10)
				{
					//$prod_qty=$gmt_production_array[$row[csf('id')]]['Iron'];
					$del_qty  = $delivery_array[$row[csf('id')]]['Iron'];
					$bill_qty = $in_bill_qty_array[$row[csf('id')]]['Iron'];
					$bill_amnt= $in_bill_amnt_array[$row[csf('id')]]['am_Iron'];
				}
				else if ($row[csf('main_process_id')]==11)
				{	
					$prod_qty = $gmt_production_array[$row[csf('id')]]['Gmts_Finishing'];
					$del_qty  = $delivery_array[$row[csf('id')]]['Gmts_Finishing'];
					$bill_qty = $in_bill_qty_array[$row[csf('id')]]['Gmts_Finishing'];
					$bill_amnt= $in_bill_amnt_array[$row[csf('id')]]['am_Gmts_Finishing'];
				}
				else if ($row[csf('main_process_id')]==12)
				{
					//$prod_qty=$gmt_production_array[$row[csf('id')]]['Gmts_Dyeing'];
					$del_qty  = $delivery_array[$row[csf('id')]]['Gmts_Dyeing'];
					$bill_qty = $in_bill_qty_array[$row[csf('id')]]['Gmts_Dyeing'];
					$bill_amnt= $in_bill_amnt_array[$row[csf('id')]]['am_Gmts_Dyeing'];
				}
				else if ($row[csf('main_process_id')]==13)
				{
					//$prod_qty=$gmt_production_array[$row[csf('id')]]['Poly'];
					$del_qty  = $delivery_array[$row[csf('id')]]['Poly'];
					$bill_qty = $in_bill_qty_array[$row[csf('id')]]['Poly'];
					$bill_amnt= $in_bill_amnt_array[$row[csf('id')]]['am_Poly'];
				}	
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">	
		        	<td width="50" bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
		            <td width="100" class="wrd_brk center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
		            <td width="100" class="wrd_brk"><? echo $party_arr[$row[csf('party_id')]]; ?></td>
		            <td width="100" class="wrd_brk"><? echo $row[csf('order_no')]; ?></td>
		            <td width="100" class="wrd_brk"><? echo $row[csf('cust_style_ref')]; ?></td>
		            
		            <td width="100" class="wrd_brk right"><? echo number_format($row[csf('order_quantity')],2); ?></td>
		            <td width="100" class="wrd_brk right"><? echo number_format($row[csf('amount')],2); ?></td>
		            <td width="100" class="wrd_brk center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
		            <td width="100" class="wrd_brk center"><? echo change_date_format($row[csf('delivery_date')]); ?></td> 
		            <td width="100" class="wrd_brk right"><? echo number_format($prod_qty,2); ?></td>

		            <td width="100" class="wrd_brk right"><a href="##" onclick="show_progress_report_details('delivery_qty_pop_up','<? echo $row[csf("id")]; ?>_<? echo $row[csf('main_process_id')]; ?>_<?echo $date_from;?>_<?echo $date_to;?>','850px')"><? echo number_format($del_qty,2); ?></a></td>

		            <td width="100" class="wrd_brk right"><? $yet_to_delv=$row[csf('order_quantity')]-$del_qty; echo  number_format($yet_to_delv,2); ?></td>
		            <td width="100" class="wrd_brk right"><? echo number_format($bill_qty,2); ?></td>
		            <td width="100" class="wrd_brk right"><? echo  number_format($bill_amnt,2); ?></td>
		            <td width="100" class="wrd_brk right">
		            	<? 
	            		$order_wise_payment_received = ($order_wise_tot_bill_arr[$row[csf('id')]]) ? ($order_wise_tot_paid_arr[$row[csf('id')]]/$order_wise_tot_bill_arr[$row[csf('id')]])*$bill_amnt : 0;
	            		echo number_format($order_wise_payment_received,2);
		            	?>
		            </td>
		            <td width="100" class="wrd_brk right"><? $balance=$bill_amnt-$order_wise_payment_received; echo  number_format($balance,2); ?></td>
		        </tr>
		        <?
				$i++;
				$tot_order_qty+=$row[csf('order_quantity')];
				$tot_order_val+=$row[csf('amount')];
				$tot_prod_qty+=$prod_qty;
				$tot_del_qty+=$del_qty;
				$tot_grey_prod+=$grey_prod;
				$tot_yet_to_delv+=$yet_to_delv;
				$tot_bill_qty+=$bill_qty;
				$tot_bill_amnt+=$bill_amnt;
				$tot_payment_amnt+=$order_wise_payment_received;
				$tot_balance+=$balance;				

				$tot_total_order_qty+=$row[csf('order_quantity')];
				$tot_total_order_val+=$row[csf('amount')];
				$tot_total_rec_qty+=$rec_qty;
				$tot_total_prod_qty+=$prod_qty;
				$tot_total_del_qty+=$del_qty;
				$tot_total_grey_prod+=$grey_prod;
				$tot_total_yet_to_delv+=$yet_to_delv;
				$tot_total_bill_qty+=$bill_qty;
				$tot_total_bill_amnt+=$bill_amnt;
				$tot_total_payment_amnt+=$order_wise_payment_received;
				$tot_total_balance+=$balance;
			}
			?>
		    	<tr class="tbl_bottom">
			        <td colspan="5" class="right"><b>Process Total:</b></td>
			        <td width="100" class="wrd_brk right"><? echo number_format($tot_order_qty); ?></td>
			        <td width="100" class="wrd_brk right"><? echo number_format($tot_order_val); ?></td>
			        <td width="100" class="wrd_brk right">&nbsp;</td>
			        <td width="100" class="wrd_brk right">&nbsp;</td>
			        <td width="100" class="wrd_brk right"><? echo number_format($tot_prod_qty); ?></td>
			        <td width="100" class="wrd_brk right"><? echo number_format($tot_del_qty); ?></td>
			        <td width="100" class="wrd_brk right"><? echo number_format($tot_yet_to_delv); ?></td>
			        <td width="100" class="wrd_brk right"><? echo number_format($tot_bill_qty); ?></td>
			        <td width="100" class="wrd_brk right"><? echo number_format($tot_bill_amnt); ?></td>
			        <td width="100" class="wrd_brk right"><? echo number_format($tot_payment_amnt); ?></td>
			        <td width="100" class="wrd_brk right"><? echo number_format($tot_balance); ?></td>
		    	</tr>
		    	<!-- bottom part -->
		        <tr class="tbl_bottom">
		            <td colspan="5" class="right">Grand Total:</td>
		            <td width="100" class="wrd_brk right"><? echo number_format($tot_total_order_qty); ?></td>            
		            <td width="100" class="wrd_brk right"><? echo number_format($tot_total_order_val); ?></td>
		            <td width="100" class="wrd_brk right">&nbsp;</td>
		            <td width="100" class="wrd_brk right">&nbsp;</td>
		            <td width="100" class="wrd_brk right"><? echo number_format($tot_total_prod_qty); ?></td>
		            <td width="100" class="wrd_brk right"><? echo number_format($tot_total_del_qty); ?></td>
		            <td width="100" class="wrd_brk right"><? echo number_format($tot_total_yet_to_delv); ?></td>
		            <td width="100" class="wrd_brk right"><? echo number_format($tot_total_bill_qty); ?></td>
		            <td width="100" class="wrd_brk right"><? echo number_format($tot_total_bill_amnt); ?></td>
		            <td width="100" class="wrd_brk right"><? echo number_format($tot_total_payment_amnt); ?></td>
		            <td width="100" class="wrd_brk right"><? echo number_format($tot_total_balance); ?></td>
		        </tr>
		    </table>        
	    </div>
    </div>
    <?	
}

if($action=="delivery_qty_pop_up")
{
	echo load_html_head_contents("Delivery Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	if($expData[2] == "")
	{
		?>
		<div id="data_panel" align="center" style="width:100%">
			<script>
				function new_window()
				{
					// $(".flt").css("display","none");
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
					// $(".flt").css("display","block");
				}
			</script>
			<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
		</div>
		<div id="details_reports" style="width: 780px;">
	        <fieldset style="width:780px">
	            <div style="width:100%;" align="left">
	                <table cellpadding="0" width="780" class="rpt_table" rules="all" border="1">
	                	<caption><div  style="text-align: center;font-weight: bold;">Work Progress Report Details </div></caption>
	                    <thead>
	                    	<tr>
	                            <th width="30">SL</th>
	                            <th width="100">Delivery ID</th>
	                            <th width="100">Delivery Date</th>
	                            <th width="100">Batch No</th>
	                            <th width="100">Order No</th>
	                            <th width="100">Category</th>
	                            <th width="150">Description</th>
	                            <th width="100">Delivery Qty</th>
	                    	</tr>
	                    </thead>
	                </table>
	            </div>  
	            <div style="width:100%; max-height:230px; overflow-y:auto;" align="left">
	                <table cellpadding="0" width="780" class="rpt_table" rules="all" border="1" >
	                    <?
						$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
						$po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
						$color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
						$kniting_item_arr=return_library_array( "select id, const_comp from lib_subcon_charge",'id','const_comp');
						$dye_fin_item_arr=return_library_array( "select id, item_description from pro_batch_create_dtls",'id','item_description');
	                    $i=0;
	                    $sql= "SELECT a.delivery_prefix_num, a.delivery_date, a.party_id, b.order_id, b.process_id, b.item_id, b.color_id, b.batch_id, sum(b.delivery_qty) as quantity, sum(b.gray_qty) as gray_qty from  subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and b.process_id='$expData[1]' and a.status_active=1 and a.is_deleted=0 group by a.delivery_prefix_num, a.delivery_date, a.party_id, b.order_id, b.process_id, b.batch_id, b.item_id, b.color_id order by a.delivery_prefix_num, a.delivery_date";
	                    // echo $sql;
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
											<td width="100" align="right"><? echo number_format($color_qty); ?></td>
										</tr>
										<tr bgcolor="#dddddd">
											<td colspan="8" align="left" ><b>Color : <? echo $color_arr[$row[csf("color_id")]]; ?></b></td>
										</tr>
									<?
										unset($color_qty);
										unset($color_process_loss);
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
								$process_loss = (($row[csf("gray_qty")] - $row[csf("quantity")]) / $row[csf("quantity")])*100;							
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="30"><? echo $i; ?></td>
									<td width="100"><? echo $row[csf("delivery_prefix_num")];?> </td>
									<td width="100"><? echo change_date_format($row[csf("delivery_date")]);?> </td> 
									<td width="100"><p><? echo $batch_arr[$row[csf("batch_id")]]; ?></p></td>
									<td width="100"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
									<td align="center" width="100"><? echo $production_process[$row[csf("process_id")]]; ?></td>
									<td align="center" width="150"><p><? echo $item_name; ?></p></td>
									<td width="100" align="right"><? echo number_format($row[csf("quantity")],2); ?></td>
								</tr>
								<? 
								$color_qty+=$row[csf("quantity")];
								$tot_qty+=$row[csf("quantity")];
								$color_process_loss += $process_loss;
								$tot_process_loss += $process_loss;
							}
							else
							{
								$process_loss = (($row[csf("gray_qty")] - $row[csf("quantity")]) / $row[csf("quantity")])*100;
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="30"><? echo $i; ?></td>
									<td width="100"><? echo $row[csf("delivery_prefix_num")];?> </td>
									<td width="100"><? echo change_date_format($row[csf("delivery_date")]);?> </td> 
									<td width="100"><p><? echo $batch_arr[$row[csf("batch_id")]]; ?></p></td>
									<td width="100"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
									<td align="center" width="100"><? echo $production_process[$row[csf("process_id")]]; ?></td>
									<td align="center" width="150"><p><? echo $item_name; ?></p></td>
									<td width="100" align="right"><? echo number_format($row[csf("quantity")],2); ?></td>
								</tr>
								<? 
								$tot_qty+=$row[csf("quantity")];
								$tot_process_loss += $process_loss;
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
	}
	else
	{
		?>
		<div id="data_panel" align="center" style="width:100%">
			<script>
				function new_window()
				{
					// $(".flt").css("display","none");
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
					// $(".flt").css("display","block");
				}
			</script>
			<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
		</div>
		<div id="details_reports" style="width: 780px;">
	        <fieldset style="width:780px">
	        	<caption><div  style="text-align: center;font-weight: bold;">Work Progress Report Details </div></caption>
	            <div style="width:100%;" align="left">
	                <table cellpadding="0" width="780" class="rpt_table" rules="all" border="1">
	                    <thead>
	                    	<tr>
	                            <th width="30">SL</th>
	                            <th width="100">Delivery ID</th>
	                            <th width="100">Delivery Date</th>
	                            <th width="100">Batch No</th>
	                            <th width="100">Order No</th>
	                            <th width="100">Category</th>
	                            <th width="150">Description</th>
	                            <th width="100">Delivery Qty</th>
	                    	</tr>
	                    </thead>
	                </table>
	            </div>  
	            <div style="width:100%; max-height:230px; overflow-y:auto;" align="left">
	                <table cellpadding="0" width="780" class="rpt_table" rules="all" border="1" >
	                    <?
						$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
						$po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
						$color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
						$kniting_item_arr=return_library_array( "select id, const_comp from lib_subcon_charge",'id','const_comp');
						$dye_fin_item_arr=return_library_array( "select id, item_description from pro_batch_create_dtls",'id','item_description');

						$knit_production_array=array();
						$knit_production_sql="SELECT b.order_id, sum(b.product_qnty) AS kniting
						from  subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and b.order_id = '$expData[0]' and b.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id";
						// echo $knit_production_sql;
						$knit_production_sql_result=sql_select($knit_production_sql);
						foreach ($knit_production_sql_result as $row)
						{
							$knit_production_array[$row[csf('order_id')]]['kniting']=$row[csf('kniting')];
						}	
						// var_dump ($knit_production_array);

	                    $i=0;
	                    $sql= "SELECT a.delivery_prefix_num, a.delivery_date, a.party_id, b.order_id, b.process_id, b.item_id, b.color_id, b.batch_id, sum(b.delivery_qty) as quantity, sum(b.gray_qty) as gray_qty from  subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and b.process_id='$expData[1]' and a.delivery_date between '$expData[2]' and '$expData[3]' and a.status_active=1 and a.is_deleted=0 group by a.delivery_prefix_num, a.delivery_date, a.party_id, b.order_id, b.process_id, b.batch_id, b.item_id, b.color_id order by a.delivery_prefix_num, a.delivery_date";
	                    /*$sql="SELECT a.prefix_no_num as sys_id, a.product_no, a.party_id, a.product_date as production_date, b.order_id, b.process, b.cons_comp_id as item_id, b.color_id, sum(b.no_of_roll) as roll_qty
						from subcon_production_mst a, subcon_production_dtls b 
						where b.order_id='$expData[1]' and b.product_type=2 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
						group by a.prefix_no_num, a.product_no, a.party_id, a.product_date, b.order_id, b.process, b.cons_comp_id, b.color_id order by b.color_id";*/
	                    // echo $sql;
						$production_sql= sql_select($sql); 
						$color_array=array(); 
						$k=1; 
						$process_id=0;
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
							$process_loss = (($row[csf("gray_qty")] - $row[csf("quantity")]) / $row[csf("quantity")])*100;
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="100"><? echo $row[csf("delivery_prefix_num")];?> </td>
								<td width="100"><? echo change_date_format($row[csf("delivery_date")]);?> </td> 
								<td width="100"><p><? echo $batch_arr[$row[csf("batch_id")]]; ?></p></td>
								<td width="100"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
								<td align="center" width="100"><? echo $production_process[$row[csf("process_id")]]; ?></td>
								<td align="center" width="150"><p><? echo $item_name; ?></p></td>
								<td width="100" align="right" ><? echo $row[csf("quantity")]; ?></td>

							</tr>
							<? 
							$tot_qty+=$row[csf("quantity")];
							$tot_gray_qty+=$row[csf("gray_qty")];
							$tot_process_loss += $process_loss;
						} 
					
						?>
	                    <tr class="tbl_bottom">
	                    	<td colspan="7" align="right">Total: </td>
	                        <td align="right"><p><? echo number_format($tot_qty); ?></p></td>
	                    </tr>
	                </table>
	            </div> 
			</fieldset>
		</div>
		<?
	}
	
	
	exit();
}

?>