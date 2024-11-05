<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$company_name_arr=return_library_array( "select id,company_name from lib_company",'id','company_name');
$imge_arr=return_library_array( "select id,master_tble_id,image_location from common_photo_library",'id','image_location');
$party_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

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
	ob_start();
	if ($type == 1) 
	{
		// echo"Imrul";
		//======================== GETTING FORM DATA ===========================
		$cbo_company_id=str_replace("'","",$cbo_company_id);
		$job_no=str_replace("'","",$txt_job_no);
		$txt_style_ref=str_replace("'","",$txt_style_ref);
		$txt_order_no=str_replace("'","",$txt_order_no);
		$year_id=str_replace("'","",$cbo_year);
		$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
		$txt_production_date = str_replace("'", "", $txt_production_date);
		// echo $cbo_company."_".$cbo_buyer_id."_".$year_id."_".$job_no."_".$txt_style_ref."_".$txt_order_no."_".$txt_production_date;die;

		if($cbo_company_id!=0) $company_cond=" and d.company_id='$cbo_company_id'"; else $cbo_company_id="";
		if($cbo_buyer_id!=0) $buyer_id_cond=" and a.party_id='$cbo_buyer_id'"; else $buyer_id_cond="";
		if($year_id!=0) $year_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id"; else $year_cond="";
		if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
		//if($txt_style_ref!="") $style_ref_cond="%".trim($txt_style_ref)."%"; else $style_ref_cond="%%";
		if ($txt_style_ref!='') $style_ref_cond=" and b.cust_style_ref like '%$txt_style_ref%'"; else $style_ref_cond="";
		//if(trim($txt_order_no)!="") $order_no_cond="%".trim($txt_order_no)."%"; else $order_no_cond="%%";
		if ($txt_order_no!='') $order_no_cond=" and b.order_no like '%$txt_order_no%'"; else $order_no_cond="";
		
		// ====================================== MAIN QUERY =====================================
		$sql = "SELECT a.party_id,
		b.order_no,
		b.cust_style_ref,
		c.color_id,
		a.subcon_job,
		a.job_no_prefix_num,
		c.qnty,
		b.delivery_date,
		b.id as po_id,
		sum(case when d.production_type=1 and e.production_type=1 then e.prod_qnty else 0 end ) as cutting_qnty,
		sum(case when d.production_type=7 and e.production_type=7 then e.prod_qnty else 0 end ) as sewing_input_qnty,
		sum(case when d.production_type=2 and e.production_type=2 then e.prod_qnty else 0 end ) as sewing_output_qnty,
		sum(case when d.production_type=3 and e.production_type=3 then e.prod_qnty else 0 end ) as iron_qnty,
		sum(case when d.production_type=4 and e.production_type=4 then e.prod_qnty else 0 end ) as finishing_qnty
        FROM subcon_ord_mst           a,
		subcon_ord_dtls          b,
		subcon_ord_breakdown     c,
		subcon_gmts_prod_dtls    d,
		subcon_gmts_prod_col_sz  e
        WHERE     a.id = b.mst_id
		AND b.id = c.order_id
		AND b.id = d.order_id
		AND d.id = e.dtls_id
		AND c.id = e.ord_color_size_id
		AND e.production_type in (1,2,3,4,7)
		AND a.status_active = '1'
		AND a.is_deleted = '0'
		AND b.status_active = '1'
		AND b.is_deleted = '0'
		AND c.status_active = '1'
		AND c.is_deleted = '0'
		AND d.status_active = '1'
		AND d.is_deleted = '0'
		AND e.production_date between '$txt_production_date' and '$txt_production_date'
		$company_cond $buyer_id_cond $year_cond $job_no_cond $style_ref_cond $order_no_cond
		group by 
		 a.party_id,
		b.order_no,
		b.cust_style_ref,
		c.color_id,
		a.subcon_job,
		a.job_no_prefix_num,
		c.qnty,
		b.delivery_date,
		b.id ";
		// echo "$sql";

		$sql_result=sql_select($sql);
		if(count($sql_result)==0)
		{
			?>
			<div style="margin: 0 auto;font-size: 20px;color: red;text-align: center;">Data not found! Please try again.</div>
			<?
		}
        $data_array =array();
		foreach($sql_result as $row)
		{
			$data_array[$row['SUBCON_JOB']][$row['ORDER_NO']][$row['COLOR_ID']]['PARTY_ID']= $row['PARTY_ID'];
			$data_array[$row['SUBCON_JOB']][$row['ORDER_NO']][$row['COLOR_ID']]['ORDER_NO']= $row['ORDER_NO'];
			$data_array[$row['SUBCON_JOB']][$row['ORDER_NO']][$row['COLOR_ID']]['CUST_STYLE_REF']= $row['CUST_STYLE_REF'];
			$data_array[$row['SUBCON_JOB']][$row['ORDER_NO']][$row['COLOR_ID']]['COLOR_ID']= $row['COLOR_ID'];
			$data_array[$row['SUBCON_JOB']][$row['ORDER_NO']][$row['COLOR_ID']]['JOB_NO_PREFIX_NUM']= $row['JOB_NO_PREFIX_NUM'];
			$data_array[$row['SUBCON_JOB']][$row['ORDER_NO']][$row['COLOR_ID']]['QNTY'] += $row['QNTY'];
			$data_array[$row['SUBCON_JOB']][$row['ORDER_NO']][$row['COLOR_ID']]['DELIVERY_DATE'] = $row['DELIVERY_DATE'];

			$data_array[$row['SUBCON_JOB']][$row['ORDER_NO']][$row['COLOR_ID']]['CUTTING_QNTY'] += $row['CUTTING_QNTY'];
			$data_array[$row['SUBCON_JOB']][$row['ORDER_NO']][$row['COLOR_ID']]['SEWING_INPUT_QNTY'] += $row['SEWING_INPUT_QNTY'];
			$data_array[$row['SUBCON_JOB']][$row['ORDER_NO']][$row['COLOR_ID']]['SEWING_OUTPUT_QNTY'] += $row['SEWING_OUTPUT_QNTY'];
			$data_array[$row['SUBCON_JOB']][$row['ORDER_NO']][$row['COLOR_ID']]['IRON_QNTY'] += $row['IRON_QNTY'];
			$data_array[$row['SUBCON_JOB']][$row['ORDER_NO']][$row['COLOR_ID']]['FINISHING_QNTY'] += $row['FINISHING_QNTY'];
		}
		// echo "<pre>";
		// print_r($data_array);

		// $order_id_array = array();
		// foreach ($job_sql_result as $val) 
		// {
		// 	$order_id_array[$val[csf('id')]] = $val[csf('id')];
		// }
		// $ordeIDs = implode(",", $order_id_array);
		// $order_cond=where_con_using_array(array_unique($order_id_array),0,"b.order_id");

		// ====================================== Production Total Query =====================================
		$sql_production_total = "SELECT a.party_id,
		b.order_no,
		b.cust_style_ref,
		c.color_id,
		a.subcon_job,
		a.job_no_prefix_num,
		c.qnty,
		b.delivery_date,
		b.id as po_id,
		sum(case when d.production_type=1 and e.production_type=1 and e.production_date <= '$txt_production_date'  then e.prod_qnty else 0 end ) as total_cutting_qnty,
		sum(case when d.production_type=7 and e.production_type=7 and e.production_date <= '$txt_production_date' then e.prod_qnty else 0 end ) as total_sewing_input_qnty,
		sum(case when d.production_type=2 and e.production_type=2 and e.production_date <= '$txt_production_date' then e.prod_qnty else 0 end ) as total_sewing_output_qnty,
		sum(case when d.production_type=3 and e.production_type=3 and e.production_date <= '$txt_production_date' then e.prod_qnty else 0 end ) as total_iron_qnty,
		sum(case when d.production_type=4 and e.production_type=4 and e.production_date <= '$txt_production_date' then e.prod_qnty else 0 end ) as total_finishing_qnty
        FROM subcon_ord_mst      a,
		subcon_ord_dtls          b,
		subcon_ord_breakdown     c,
		subcon_gmts_prod_dtls    d,
		subcon_gmts_prod_col_sz  e
        WHERE     a.id = b.mst_id
		AND b.id = c.order_id
		AND b.id = d.order_id
		AND d.id = e.dtls_id
		AND c.id = e.ord_color_size_id
		AND e.production_type in (1,2,3,4,7)
		AND a.status_active = '1'
		AND a.is_deleted = '0'
		AND b.status_active = '1'
		AND b.is_deleted = '0'
		AND c.status_active = '1'
		AND c.is_deleted = '0'
		AND d.status_active = '1'
		AND d.is_deleted = '0'
		$company_cond $buyer_id_cond $year_cond $job_no_cond $style_ref_cond $order_no_cond
		group by 
		 a.party_id,
		b.order_no,
		b.cust_style_ref,
		c.color_id,
		a.subcon_job,
		a.job_no_prefix_num,
		c.qnty,
		b.delivery_date,
		b.id ";
		// echo "$sql_production_total";

		$sql_production_total_result=sql_select($sql_production_total);

        $production_total_array =array();
		foreach($sql_production_total_result as $row)
		{
			$production_total_array[$row['SUBCON_JOB']][$row['ORDER_NO']][$row['COLOR_ID']]['TOTAL_CUTTING_QNTY'] += $row['TOTAL_CUTTING_QNTY'];
			$production_total_array[$row['SUBCON_JOB']][$row['ORDER_NO']][$row['COLOR_ID']]['TOTAL_SEWING_INPUT_QNTY'] += $row['TOTAL_SEWING_INPUT_QNTY'];
			$production_total_array[$row['SUBCON_JOB']][$row['ORDER_NO']][$row['COLOR_ID']]['TOTAL_SEWING_OUTPUT_QNTY'] += $row['TOTAL_SEWING_OUTPUT_QNTY'];
			$production_total_array[$row['SUBCON_JOB']][$row['ORDER_NO']][$row['COLOR_ID']]['TOTAL_IRON_QNTY'] += $row['TOTAL_IRON_QNTY'];
			$production_total_array[$row['SUBCON_JOB']][$row['ORDER_NO']][$row['COLOR_ID']]['TOTAL_FINISHING_QNTY'] += $row['TOTAL_FINISHING_QNTY'];
		}
		// echo "<pre>";
		// print_r($production_total_array);

		// ====================================== Garments Delivery Query (Ex-Factory) =====================================
		$sql_ex_factory ="SELECT 
		b.order_no,
		c.color_id,
		a.subcon_job,
		b.id
			AS po_id,
		h.breakdown_color_size_id,
			h.delivery_qty
        FROM subcon_ord_mst         a,
		subcon_ord_dtls        b,
		subcon_ord_breakdown   c,
		subcon_gmts_prod_dtls  d,
		subcon_gmts_prod_col_sz e,
		subcon_delivery_mst f,
		subcon_delivery_dtls g,
		subcon_gmts_delivery_dtls h
		
        WHERE     a.id = b.mst_id
		AND b.id = c.order_id
		AND b.id = d.order_id
		AND d.id = e.dtls_id
		AND c.id = e.ord_color_size_id
		
		AND b.id = g.order_id
		AND f.id = g.MST_ID 
		AND g.id = h.dtls_mst_id
		AND c.id = h.breakdown_color_size_id
		
		AND a.status_active = '1'
		AND a.is_deleted = '0'
		AND b.status_active = '1'
		AND b.is_deleted = '0'
		AND c.status_active = '1'
		AND c.is_deleted = '0'
		AND d.status_active = '1'
		AND d.is_deleted = '0'
		
		AND f.status_active = '1'
		AND f.is_deleted = '0'
		AND g.status_active = '1'
		AND g.is_deleted = '0'
		AND f.delivery_date BETWEEN '$txt_production_date' and '$txt_production_date'
		$company_cond $buyer_id_cond $year_cond $job_no_cond $style_ref_cond $order_no_cond
        GROUP BY 
		b.order_no,
		c.color_id,
		a.subcon_job,
		b.id,
		h.breakdown_color_size_id,
		h.delivery_qty";
		// echo "$sql_ex_factory";

		$sql_ex_factory_result=sql_select($sql_ex_factory);

        $ex_factory_qty_array =array();
		foreach($sql_ex_factory_result as $row)
		{
			$ex_factory_qty_array[$row['SUBCON_JOB']][$row['ORDER_NO']][$row['COLOR_ID']]['DELIVERY_QTY'] += $row['DELIVERY_QTY'];
		}
		// echo "<pre>";
		// print_r($ex_factory_qty_array);

		// ====================================== Garments Delivery Total Query (Ex-Factory) =====================================
		$sql_ex_factory_total ="SELECT 
		b.order_no,
		c.color_id,
		a.subcon_job,
		b.id
			AS po_id,
		h.breakdown_color_size_id,
			h.delivery_qty
        FROM subcon_ord_mst         a,
		subcon_ord_dtls        b,
		subcon_ord_breakdown   c,
		subcon_gmts_prod_dtls  d,
		subcon_gmts_prod_col_sz e,
		subcon_delivery_mst f,
		subcon_delivery_dtls g,
		subcon_gmts_delivery_dtls h
		
        WHERE     a.id = b.mst_id
		AND b.id = c.order_id
		AND b.id = d.order_id
		AND d.id = e.dtls_id
		AND c.id = e.ord_color_size_id
		
		AND b.id = g.order_id
		AND f.id = g.MST_ID 
		AND g.id = h.dtls_mst_id
		AND c.id = h.breakdown_color_size_id
		
		AND a.status_active = '1'
		AND a.is_deleted = '0'
		AND b.status_active = '1'
		AND b.is_deleted = '0'
		AND c.status_active = '1'
		AND c.is_deleted = '0'
		AND d.status_active = '1'
		AND d.is_deleted = '0'
		
		AND f.status_active = '1'
		AND f.is_deleted = '0'
		AND g.status_active = '1'
		AND g.is_deleted = '0'
		AND f.delivery_date <='$txt_production_date'
		$company_cond $buyer_id_cond $year_cond $job_no_cond $style_ref_cond $order_no_cond
        GROUP BY 
		b.order_no,
		c.color_id,
		a.subcon_job,
		b.id,
		h.breakdown_color_size_id,
		h.delivery_qty";
		// echo "$sql_ex_factory_total";

		$sql_ex_factory_total_result=sql_select($sql_ex_factory_total);

        $ex_factory_qty_total_array =array();
		foreach($sql_ex_factory_total_result as $row)
		{
			$ex_factory_qty_total_array[$row['SUBCON_JOB']][$row['ORDER_NO']][$row['COLOR_ID']]['DELIVERY_QTY'] += $row['DELIVERY_QTY'];
		}
		// echo "<pre>";
		// print_r($ex_factory_qty_total_array);


		?>
			<fieldset style="width:2190px">
					<table width="2190" cellpadding="0" cellspacing="0"> 
						<tr class="form_caption">
							<td align="center"><p style="font-size:18px; font-weight:bold;">Daily Order and Style Wise Sub Contract Production Report</p></td> 
						</tr>
						<tr class="form_caption">
							<td align="center"><p style="font-size:16px; font-weight:bold;"><? echo $company_name_arr[$cbo_company_id]; ?><p></td> 
						</tr>
						<tr class="form_caption">
							<td align="center"><p style="font-size:14px; font-weight:bold;"><? echo "Production Date : ".change_date_format( str_replace("'","",trim($txt_production_date)) ); ?></p></td> 
						</tr>
					</table>
					<br />
					<!-- ========= Details Part ======== -->
					<table class="rpt_table" width="2190" cellpadding="0" cellspacing="0" border="1" rules="all">
						<thead>
							<tr>
								<th width="40" rowspan="8">Sl.</th>
								<th width="100" rowspan="8">Buyer</th>
								<th width="100" rowspan="8">Order Number</th>
								<th width="100" rowspan="8">Customer Style</th>
								<th width="100" rowspan="8">Colour</th>
								<th width="100" rowspan="8">Job No</th>
								<th width="100" rowspan="8">Order Qty.</th>
								<th width="100" rowspan="8">Ship Date</th>

								<th colspan="3" width="240" align="center">Cutting</th>
								<th colspan="3" width="240" align="center">Sewing Input</th>
								<th colspan="3" width="240" align="center">Sewing Output</th>
								<th colspan="3" width="240" align="center">Iron</th>
								<th colspan="3" width="240" align="center">Finishing</th>
								<th colspan="3" width="240" align="center">Ex- Factory</th>
							</tr>
							<tr>

								<th width="80">Today</th>
								<th width="80">Total</th>
								<th width="80">WIP Bal.</th>
								<th width="80">Today</th>
								<th width="80">Total</th>
								<th width="80">WIP Bal.</th>
								<th width="80">Today</th>
								<th width="80">Total</th>
								<th width="80">WIP Bal.</th>
								<th width="80">Today</th>
								<th width="80">Total</th>
								<th width="80">WIP Bal.</th>
								<th width="80">Today</th>
								<th width="80">Total</th>
								<th width="80">WIP Bal.</th>
								<th width="80">Today</th>
								<th width="80">Total</th>
								<th width="80">WIP Bal.</th>
								
							</tr>
						</thead>
					</table>
					<table class="rpt_table" width="2190" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<tbody id="scroll_body">
							<?
							    $i=1;
								foreach($data_array as $job_key => $job_value)
								{
									foreach($job_value as $order_key => $order_value)
									{
										foreach($order_value as $color_key => $colo_value)
										{
											if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											?>
											<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
												<td width="40"><? echo $i;?></td>
												<td width="100" align="left"><? echo $buyer_name_arr[$colo_value['PARTY_ID']]; ?></td>
												<td width="100" align="left"><? echo $colo_value['ORDER_NO']; ?></td>
												<td width="100" align="left"><? echo $colo_value['CUST_STYLE_REF']; ?></td>
												<td width="100" align="left"><? echo $color_library[$colo_value['COLOR_ID']]; ?></td>
												<td width="100" align="center"><? echo $colo_value['JOB_NO_PREFIX_NUM']; ?></td>
												<td width="100" align="right"><? echo number_format($colo_value['QNTY'],0); ?></td>
												<td width="100" align="right"><? echo change_date_format($colo_value['DELIVERY_DATE']) ?></td>

												<td width="80" align="right"><? echo number_format($colo_value['CUTTING_QNTY'],0); ?></td>
												<td width="80" align="right">
													<? 
														$total_cutting_qty = $production_total_array[$job_key][$order_key][$color_key]['TOTAL_CUTTING_QNTY'];
														echo number_format($total_cutting_qty,0);
													?>
												</td>
												<td width="80" align="right">
													<? 
														$cutting_wip = $colo_value['QNTY']-$total_cutting_qty; 
														echo number_format($cutting_wip,0);
													?>
												</td>
												<td width="80" align="right"><? echo number_format($colo_value['SEWING_INPUT_QNTY'],0); ?></td>
												<td width="80" align="right">
													<? 
														$total_sewing_input_qty = $production_total_array[$job_key][$order_key][$color_key]['TOTAL_SEWING_INPUT_QNTY'];
														echo number_format($total_sewing_input_qty,0);
													?>
													</td>
												<td width="80" align="right">
												    <? 
														$sewing_input_wip = $colo_value['QNTY']-$total_sewing_input_qty; 
														echo number_format($sewing_input_wip,0);
													?>
												</td>
												<td width="80" align="right"><? echo number_format($colo_value['SEWING_OUTPUT_QNTY'],0); ?></td>
												<td width="80" align="right">
													<? 
														$total_sewing_output_qty = $production_total_array[$job_key][$order_key][$color_key]['TOTAL_SEWING_OUTPUT_QNTY'];
														echo number_format($total_sewing_output_qty,0);
													?>
												</td>
												<td width="80" align="right">
												    <? 
														$sewing_output_wip = $colo_value['QNTY']-$total_sewing_output_qty; 
														echo number_format($sewing_output_wip,0);
													?>
												</td>
												<td width="80" align="right"><? echo number_format($colo_value['IRON_QNTY'],0); ?></td>
												<td width="80" align="right">
													<? 
													    $total_iron_qty =$production_total_array[$job_key][$order_key][$color_key]['TOTAL_IRON_QNTY'];
														echo number_format($total_iron_qty,0);
													?>
												</td>
												<td width="80" align="right">
												    <? 
														$iron_wip = $colo_value['QNTY']-$total_iron_qty; 
														echo number_format($iron_wip,0);
													?>
												</td>
												<td width="80" align="right"><? echo number_format($colo_value['FINISHING_QNTY'],0); ?></td>
												<td width="80" align="right">
													<? 
													$total_finishing_qty = $production_total_array[$job_key][$order_key][$color_key]['TOTAL_FINISHING_QNTY'];
													echo number_format($total_finishing_qty,0);
													?>
												</td>
												<td width="80" align="right">
												    <? 
														$finishing_wip = $colo_value['QNTY']-$total_sewing_input_qty; 
														echo number_format($finishing_wip,0);
													?>
												</td>
												<td width="80" align="right"><? echo number_format($ex_factory_qty_array[$job_key][$order_key][$color_key]['DELIVERY_QTY'],0); ?></td>
												<td width="80" align="right"> 
													<? 
														$total_ex_factory_qty = $ex_factory_qty_total_array[$job_key][$order_key][$color_key]['DELIVERY_QTY'];
													    echo number_format($total_ex_factory_qty,0); 
													?> 
												</td>
												<td width="80" align="right">
												    <? 
														$ex_factory_wip = $total_finishing_qty-$total_ex_factory_qty; 
														echo number_format($ex_factory_wip,0);
													?>
												</td>
											</tr>
											<?
											$i++;
											$total_today_cutting_qnty       += $colo_value['CUTTING_QNTY'];
											$total_today_sewing_input_qnty  += $colo_value['SEWING_INPUT_QNTY'];
											$total_today_sewing_output_qnty += $colo_value['SEWING_OUTPUT_QNTY'];
											$total_today_iron_qnty          += $colo_value['IRON_QNTY'];
											$total_today_finishing_qnty     += $colo_value['FINISHING_QNTY'];
											$total_today_exfactory_qnty     += $ex_factory_qty_array[$job_key][$order_key][$color_key]['DELIVERY_QTY']; ;

											$total_cutting_qnty       += $total_cutting_qty;
											$total_sewing_input_qnty  += $total_sewing_input_qty;
											$total_sewing_output_qnty += $total_sewing_output_qty;
											$total_iron_qnty          += $total_iron_qty;
											$total_finishing_qnty     += $total_finishing_qty;
											$total_ex_factory_qnty    += $total_ex_factory_qty;

											$total_cutting_wip_qnty        += $cutting_wip;
											$total_sewing_input_wip_qnty   += $sewing_input_wip;
											$total_sewing_output_wip_qnty  += $sewing_output_wip;
											$total_iron_wip_qnty           += $iron_wip;
											$total_finishing_wip_qnty      += $finishing_wip;
											$total_ex_factory_wip_qnty      += $ex_factory_wip;
											
										}	
									}		
								}
							?>
							<tr>
								<td colspan="8" align="right" style="font-weight:bold;">Grand Total</td>
                                
								<td width="80" align="right" style="font-weight:bold;"><? echo number_format($total_today_cutting_qnty,0);?></td>
								<td width="80" align="right" style="font-weight:bold;"><? echo number_format($total_cutting_qnty,0);?></td>
								<td width="80" align="right" style="font-weight:bold;"><? echo number_format($total_cutting_wip_qnty,0);?></td>
								<td width="80" align="right" style="font-weight:bold;"><? echo number_format($total_today_sewing_input_qnty,0);?></td>
								<td width="80" align="right" style="font-weight:bold;"><? echo number_format($total_sewing_input_qnty,0);?></td>
								<td width="80" align="right" style="font-weight:bold;"><? echo number_format($total_sewing_input_wip_qnty,0);?></td>
								<td width="80" align="right" style="font-weight:bold;"><? echo number_format($total_today_sewing_output_qnty,0);?></td>
								<td width="80" align="right" style="font-weight:bold;"><? echo number_format($total_sewing_output_qnty,0);?></td>
								<td width="80" align="right" style="font-weight:bold;"><? echo number_format($total_sewing_output_wip_qnty,0);?></td>
								<td width="80" align="right" style="font-weight:bold;"><? echo number_format($total_today_iron_qnty,0);?></td>
								<td width="80" align="right" style="font-weight:bold;"><? echo number_format($total_iron_qnty,0);?></td>
								<td width="80" align="right" style="font-weight:bold;"><? echo number_format($total_iron_wip_qnty,0);?></td>
								<td width="80" align="right" style="font-weight:bold;"><? echo number_format($total_today_finishing_qnty,0);?></td>
								<td width="80" align="right" style="font-weight:bold;"><? echo number_format($total_finishing_qnty,0);?></td>
								<td width="80" align="right" style="font-weight:bold;"><? echo number_format($total_finishing_wip_qnty,0);?></td>
								<td width="80" align="right" style="font-weight:bold;"><? echo number_format($total_today_exfactory_qnty,0);?></td>
								<td width="80" align="right" style="font-weight:bold;"><? echo number_format($total_ex_factory_qnty,0);?></td>
								<td width="80" align="right" style="font-weight:bold;"><? echo number_format($total_ex_factory_wip_qnty,0);?></td>	
							</tr> 
						</tbody>                   
					</table>
			</fieldset>  
		<?
		
	}

	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename,'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename####$type";
	exit();      

}

?>