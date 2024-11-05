<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');
$user_id=$_SESSION['logic_erp']['user_id'];

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
	
    $sql="select a.party_id, a.subcon_job, a.job_no_prefix_num, $year_field, b.order_no, b.cust_style_ref, b.main_process_id,a.id from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.company_id='$company_id' $buyer_cond $year_field_cond order by a.id desc";	
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
			<tr bgcolor="<? echo $bgcolor; ?>"  onClick="js_set_value('<? echo $row[csf('subcon_job')]."_".$row[csf('id')]; ?>')" style="cursor:pointer;">
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
if($action=="programe_no_popup")
{
	echo load_html_head_contents("Programe Info", "../../../", 1, 1,'','','');
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
		$year_field="year(c.insert_date) as year"; 
		if(trim($year)!=0) $year_field_cond="and YEAR(c.insert_date)=$year_job";
	}
    else if($db_type==2) 
	{
		$year_field="to_char(c.insert_date,'YYYY') as year";
		if(trim($year)!=0) $year_field_cond=" and to_char(c.insert_date,'YYYY')=$year_job";
	}
    else 
	{
		$year_field="";
		$year_field_cond="";
	}
    
    if($cbo_buyer_name==0) $buyer_cond=""; else $buyer_cond=" and a.party_id='$cbo_buyer_name'";
    
    $buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	if($job_id =="")$job_cond=""; else $job_cond=" and c.mst_id='$job_id'";
    	
			$sql=" SELECT c.job_no_mst,a.party_id, $year_field, c.cust_style_ref,d.dtls_id as programe_no,c.mst_id as job_id , c.order_no 
			FROM  subcon_ord_mst a,
			   	  subcon_ord_dtls c, 
				  subcon_planning_plan_dtls d 
			where a.id=c.mst_id and 
				  c.id=d.po_id and 
				  c.id=d.po_id  and 
				  d.status_active = 1 and 
				  d.is_deleted = 0 
			$job_cond $buyer_cond $year_field_cond
			GROUP BY c.job_no_mst,c.insert_date, c.cust_style_ref,d.dtls_id ,c.mst_id  , c.order_no ,a.party_id ";	
		//  echo $sql;
    ?>
    <table width="580" border="1" rules="all" class="rpt_table">
        <thead>
            <th width="30">SL</th>
			<th width="80">Programe No</th>
            <th width="80">Job no</th>
            <th width="70">Year</th>			
            <th width="130">Buyer</th>
            <th width="110">Style</th>
            <th width="120">Po number</th>
       </thead>
    </table>
    <div style="max-height:340px; overflow:auto;">
    <table id="table_body2" width="580" border="1" rules="all" class="rpt_table">
        <? 
        $data_array=sql_select($sql);
        $i=1;
		foreach($data_array as $row)
		{
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		    ?>
			<tr bgcolor="<? echo $bgcolor; ?>"  onClick="js_set_value('<? echo $row[csf('programe_no')]."_".$row[csf('job_id')]."_".$row[csf('job_no_mst')]; ?>')" style="cursor:pointer;">
				<td width="30"><? echo $i; ?></td>
				<td align="center"  width="80"><? echo $row[csf('programe_no')]; ?></td>
				<td align="center"  width="80"><? echo $row[csf('job_no_mst')]; ?></td>
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
	$job_id        = str_replace("'","",$txt_job_id);
	$txt_style_ref = str_replace("'","",$txt_style_ref);
	$txt_order_no  = str_replace("'","",$txt_order_no);
	$year_id       = str_replace("'","",$cbo_year);
	$cbo_buyer_id  = str_replace("'","",$cbo_buyer_id);
	$program_no   = str_replace("'","",$txt_program_no);
	$company_id   = str_replace("'","",$cbo_company_id);
	$machine_arr = get_machine_array();
	
	//$date_from     = str_replace("'", "", trim($txt_date_from));
	//$date_to       = str_replace("'", "", trim($txt_date_to));
	$color_lib=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
	if ($year_id!=0) $year_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id"; else $year_cond="";
	if ($program_no !='') $program_no_cond=" and d.dtls_id=$program_no"; else $program_no_cond="";
	if ($cbo_buyer_id!=0) $buyer_id_cond=" and a.party_id='$cbo_buyer_id'"; else $buyer_id_cond="";		

	if ($txt_style_ref!='') $style_ref_cond=" and b.cust_style_ref like '%$txt_style_ref%'"; else $style_ref_cond="";
	if ($txt_order_no!='') $order_no_cond=" and b.order_no like '%$txt_order_no%'"; else $order_no_cond="";
	
	$fabric_details = return_library_array( "SELECT id, const_comp FROM lib_subcon_charge", "id", "const_comp");
	//if($date_from == "" && $date_to=="") $date_cond=""; else $date_cond=" and b.delivery_date between $txt_date_from and $txt_date_to";
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") {$date_cond="";} else {$date_cond=" and b.delivery_date between $txt_date_from and $txt_date_to";}
	$date_from = str_replace("'", "", $txt_date_from);
	$date_to = str_replace("'", "", $txt_date_to);		
	$company_lib=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	

	
	 	 $job_sql="SELECT f.distribution_qnty,d.po_id AS order_tbl_id,d.program_qnty, e.job_no, f.yarn_details_breakdown,c.order_no,f.spandex_stitch_length,f.start_date ,f.end_date,d.dtls_id,c.job_no_mst,a.party_id,f.machine_gg,f.machine_id,f.color_id,f.color_range,f.machine_dia,f.stitch_length,f.fabric_dia,g.item_id, g.gsm  
		 FROM   subcon_ord_mst a,
				subcon_ord_dtls c INNER JOIN subcon_ord_breakdown g ON c.id = g.order_id, 
				subcon_planning_plan_dtls d ,
				subcon_planning_mst e left join 
				subcon_planning_dtls f on e.id=f.mst_id  
		 WHERE  a.id=c.mst_id and 
				c.job_no_mst = '$job_no' $program_no_cond and 
				
				c.id=d.po_id and 
				d.mst_id=e.id and 
				d.status_active = 1 and 
				d.is_deleted = 0 
		 group by  f.distribution_qnty,d.po_id,d.program_qnty, e.job_no, f.yarn_details_breakdown,c.order_no,f.spandex_stitch_length,f.start_date ,f.end_date,d.dtls_id,c.job_no_mst,a.party_id,f.machine_gg,f.machine_id,f.color_id,f.color_range,f.machine_dia,f.stitch_length,f.fabric_dia,g.item_id, g.gsm ";
	
		 $job_sql_data=sql_select($job_sql);
		 
		foreach ($job_sql_data as $val) 
		{
			
		
				$yarn_details_arr=explode("__",$val[csf('yarn_details_breakdown')]);
				$y=1;
				foreach($yarn_details_arr as $row){
					$yarn_arr=explode("_",$row);
					if($y==1){
						$ycount.=$yarn_arr[0];
						$ylot.=$yarn_arr[1];
						$ybrand.=$yarn_arr[2];
						$yqnty.=$yarn_arr[3];
						$y++;
					}else{
						$ycount.=",".$yarn_arr[0];
						$ylot.=",".$yarn_arr[1];
						$ybrand.=",".$yarn_arr[2];
						$yqnty.=",".$yarn_arr[3];
					}
				}
			$job_no=$val[csf('job_no_mst')];		
			$party_id=$val[csf('party_id')];
			$start_date=$val[csf('start_date')];
			$finish_date=$val[csf('end_date')];
			$distribution_qnty+=$val[csf('distribution_qnty')];		
			$brand_id[$ybrand] =$ybrand;
			$count[$ycount] =$ycount;
			$machine_gg[$val[csf('machine_gg')]] =$val[csf('machine_gg')];
			$machineArr[$machine_arr[$val[csf('machine_id')]]]=$machine_arr[$val[csf('machine_id')]];
			$color_id[$val[csf('color_id')]]=$color_lib[$val[csf('color_id')]];
			$colorType[$val[csf('color_range')]]=$color_range[$val[csf('color_range')]];
			$gsm_arr[$val[csf('gsm')]] =$val[csf('gsm')];
			$machine_dia[$val[csf('machine_dia')]]=$val[csf('machine_dia')];
			$stitch_len[$val[csf('stitch_length')]]=$val[csf('stitch_length')];
			$dia_width[$val[csf('fabric_dia')]] =$val[csf('fabric_dia')];
			$fabric_type[$fabric_details[$val[csf('item_id')]]]=$fabric_details[$val[csf('item_id')]];
			$spandex_stitch_length[$val[csf('spandex_stitch_length')]] =$val[csf('spandex_stitch_length')];
			$order_no=$val[csf('order_no')];
			$program_no=$val[csf('dtls_id')];
			$order_id_array[$val[csf('order_tbl_id')]] = $val[csf('order_tbl_id')];
		}
		
		$ordeIDs = implode(",", $order_id_array);

	
		 $job_sql2="SELECT a.production_basis,a.location_id,a.party_id,a.knitting_source,a.knitting_company,a.knit_location_id, 
		b.id,b.process, b.fabric_description, b.cons_comp_id, b.gsm, b.dia_width, b.dia_width_type,b.machine_dia, b.machine_gg,
		 b.no_of_roll,b.product_qnty,b.reject_qnty, b.uom_id, b.yarn_lot, b.yrn_count_id, b.floor_id, b.machine_id, 
		 b.brand, b.shift, b.stitch_len, b.color_range, b.color_id, b.remarks,b.operator_name, c.order_no
	      FROM subcon_production_mst a,
		  	  subcon_production_dtls b,
			 subcon_ord_dtls c
		WHERE b.job_no = '$job_no'  and
			 a.id=b.mst_id and 
			 c.id=b.order_id and 
			 a.entry_form = 159 AND 
			 a.production_basis = 2 AND 
			 a.status_active = 1 AND 
			 a.is_deleted = 0 AND 
			 b.status_active = 1 AND 
			 b.is_deleted = 0 and
			 c.id in ($ordeIDs)
		group by a.production_basis,a.location_id,a.party_id,a.knitting_source,a.knitting_company,a.knit_location_id, 
		b.id,b.process, b.fabric_description, b.cons_comp_id, b.gsm, b.dia_width, b.dia_width_type,b.machine_dia, b.machine_gg,
		 b.no_of_roll,b.product_qnty,b.reject_qnty, b.uom_id, b.yarn_lot, b.yrn_count_id, b.floor_id, b.machine_id, 
		 b.brand, b.shift, b.stitch_len, b.color_range, b.color_id, b.remarks,b.operator_name, c.order_no  ";
		 $job_sql_data2=sql_select($job_sql2);
		 
		foreach ($job_sql_data2 as $val) 
		{
			$product_qnty+=$val[csf('product_qnty')];//subcon_production_dtls
			$floor_arr[$val[csf('floor_id')]] =$val[csf('floor_id')];//subcon_production_dtls
		
	
		}
		


	$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');

	$sql_data=sql_select("  SELECT c.JOB_NO_MST,b.no_of_roll,b.product_qnty,b.reject_qnty,b.shift,b.operator_name,a.product_date, b.remarks
	FROM subcon_production_mst a INNER JOIN subcon_production_dtls b ON a.id = b.mst_id INNER JOIN subcon_ord_dtls c ON b.order_id = c.id
	 WHERE  a.entry_form = 159 and  b.job_no ='$job_no'   AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 
	 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 ");

	ob_start();	
	?>
	
    <div style="margin-left: 5px;">
    	<style>
    		.wrd_brk{word-break: break-all;}.left{text-align: left;}
    		.center{text-align: center;}.right{text-align: right;}
		</style>
		 <table width="950" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="table_header_1">
			 <tr>
				 <td width="100" class="wrd_brk"  align="center"> 
					<img  src='../../<? echo $imge_arr[$company_id]; ?>' height='100%' width='100%' /></td>
			    <td width="100" class="wrd_brk" colspan="8" align="center" height="30" ><p ><b style="font-size: 25px;"><?=$company_lib[$company_id];?></b></p><br>
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_id"); 
					if($txt_job_no!="")
					{
					$location=return_field_value( "location_name", "wo_po_details_master","job_no='$job_no'");
					}
					else
					{
					$location="";	
					}
					foreach ($nameArray as $result)
                            {
								echo $location_name_arr[$location];
 
                            ?>
                               Plot No: <? echo $result[csf('plot_no')]; ?> 
                                Level No: <? echo $result[csf('level_no')]?>
                                Road No: <? echo $result[csf('road_no')]; ?> 
                                Block No: <? echo $result[csf('block_no')];?> 
                                City No: <? echo $result[csf('city')];?> 
                                Zip Code: <? echo $result[csf('zip_code')]; ?> 
                                Province No: <?php echo $result[csf('province')];?> 
                                Country: <? echo $country_arr[$result[csf('country_id')]]; ?> <br>
                                Email Address: <? echo $result[csf('email')];?> 
                                Website No:<? echo $result[csf('website')];
							
                            }
                            ?>   
			
			</td>
               
            </tr>
            <tr>
			    <td width="135" class="wrd_brk"><b>PROGRAM NO</b></td>
                <td width="150" class="wrd_brk" align="center"><b><?=$program_no;?></b></td>
                <td width="120" class="wrd_brk"><b>Start Date :<br>Finish Date:</b></td>
                <td width="100" class="wrd_brk"><?=$start_date."<br>".$finish_date;?> </td>
				<td width="60" class="wrd_brk" rowspan="5"></td>
                <td width="150" class="wrd_brk" colspan="2" ><b>Number of Active Feeder </b></td>
                <td width="100" class="wrd_brk" colspan="2"></td>         
            </tr>
			<tr>
			    <td width="120" class="wrd_brk"><b>BUYER</b></td>
                <td width="150" class="wrd_brk"><?=$buyer_name_arr[$party_id];?></td>
                <td width="120" class="wrd_brk"><b>Job No :</b></td>
                <td width="100" class="wrd_brk"><?=$job_no;?></td>
   
                <td width="150" class="wrd_brk" colspan="2"><b>Number of Needles</b></td>
                <td width="100" class="wrd_brk" colspan="2"></td>
            </tr>
			<tr>
			    <td width="120" class="wrd_brk"><b>Floor Name</b></td>
                <td width="150" class="wrd_brk"><?=implode(",",$floor_arr);?></td>
                <td width="120" class="wrd_brk"><b>M/C NO</b></td>
                <td width="100" class="wrd_brk"><?=implode(",",$machineArr);?></td>
          
                <td width="150" class="wrd_brk" colspan="2"><b>M/C RPM:</b></td>
                <td width="100" class="wrd_brk" colspan="2"></td>
            </tr>
			<tr>
			    <td width="120" class="wrd_brk"><b>M/C DIA & GG</b></td>
                <td width="150" class="wrd_brk"><?=implode(",",$machine_dia)."&".implode(",",$machine_gg);?></td>
                <td width="120" class="wrd_brk"><b>F.DIA</b></td>
                <td width="150" class="wrd_brk"><?=implode(",",$dia_width);?></td>
                <td width="150" class="wrd_brk" colspan="2"><b>Counter:</b></td>
                <td width="100" class="wrd_brk" colspan="2"></td>
            </tr>
			<tr>
			    <td width="120" class="wrd_brk"><b>F.TYPE</b></td>
                <td width="100" class="wrd_brk" colspan="3"><?=implode(",",$fabric_type);?></td>    
                <td width="150" class="wrd_brk" colspan="2"><b>Target/ Shift</b></td>
                <td width="100" class="wrd_brk" colspan="2"></td>
            </tr>
			<tr>
			    <td width="100" class="wrd_brk"><b>GSM</b></td>
                <td width="100" class="wrd_brk"><?=implode(",",$gsm_arr);?></td>
                <td width="100" class="wrd_brk"><b>COLOR</b></td>
                <td width="100" class="wrd_brk"><?=implode(",",$color_id);?></td>
              
            </tr>
			<tr>
			    <td width="100" class="wrd_brk"><b>Color TYPE</b></td>
                <td width="100" class="wrd_brk"><?=implode(",",$colorType);;?></td>
                <td width="100" class="wrd_brk"><b>SL</b></td>
                <td width="100" class="wrd_brk"><?=implode(",",$stitch_len);;?></td>
              
            </tr>
			<tr>
			    <td width="100" class="wrd_brk"><b>COUNT</b></td>
                <td width="100" class="wrd_brk" colspan="3"><?=implode(",",$count);;?></td>
        
            </tr>
			<tr>
			    <td width="100" class="wrd_brk"><b>BRAND</b></td>
                <td width="100" class="wrd_brk" colspan="3"><?=implode(",",$brand_id);?></td>
            </tr>
			<tr>
			    <td width="100" class="wrd_brk"><b>Spandex CM</b></td>
                <td width="100" class="wrd_brk"><?=implode(",",$spandex_stitch_length);?></td>
                <td width="140" class="wrd_brk"><b>M/C Distrb. Qty</b></td>
                <td width="100" class="wrd_brk"><?=$distribution_qnty;?></td>
                
            </tr>
			<tr>
			    <td width="100" class="wrd_brk"><b>P. QTY. (Kg)</b></td>
                <td width="100" class="wrd_brk"><?=$product_qnty;?></td>
                <td width="100" class="wrd_brk"><b>No Of Ply</b></td>
                <td width="100" class="wrd_brk"></td>
               
            </tr>
			<tr>
			    <td width="100" class="wrd_brk"><b>Remarks</b></td>
                <td width="100" class="wrd_brk" colspan="3"></td>
            </tr>
			<tr>
			    <td width="100" class="wrd_brk" colspan="10" rowspan="2">&nbsp;</td>            
            </tr>
			<tr></tr>
			<tr>
			    <td width="120" class="wrd_brk" rowspan="2" align="center"><b>Date</b></td>
                <td width="120" class="wrd_brk" colspan="2" align="center"><b>Shift</b></td>
                <td width="100" class="wrd_brk" rowspan="2" align="center"><b>No. of Roll</b></td>
                <td width="150" class="wrd_brk" rowspan="2" align="center"><b>Production qty</b></td>
                <td width="100"  class="wrd_brk" rowspan="2" align="center"><b>Reject qty</b> </td>
                <td width="100" class="wrd_brk" rowspan="2" align="center"><b>Balance Qty</b></td>
                <td width="100" class="wrd_brk"  rowspan="2" align="center"><b>Operator Id</b></td>
				<td width="100" class="wrd_brk"  rowspan="2" align="center"><b>Remarks</b></td>
            </tr>
			<tr>
                <td width="120" class="wrd_brk" height="30"><b> &nbsp;</b></td>
				<td width="120" class="wrd_brk" height="30"><b>&nbsp; </b></td>
            </tr>
			
			<tr>
			    <td width="120" class="wrd_brk" height="30">&nbsp;</td>
                <td width="120" class="wrd_brk">&nbsp;</td>
				<td width="120" class="wrd_brk">&nbsp;</td>
                <td width="100" class="wrd_brk">&nbsp;</td>
                <td width="100" class="wrd_brk">&nbsp;</td>
                <td width="100"  class="wrd_brk">&nbsp;</td>
                <td width="100" class="wrd_brk">&nbsp;</td>
                <td width="100" class="wrd_brk">&nbsp;</td>
				<td width="100" class="wrd_brk">&nbsp;</td>
            </tr>
			<tr>
			    <td width="120" class="wrd_brk" height="30">&nbsp;</td>
                <td width="120" class="wrd_brk">&nbsp;</td>
				<td width="120" class="wrd_brk">&nbsp;</td>
                <td width="100" class="wrd_brk">&nbsp;</td>
                <td width="100" class="wrd_brk">&nbsp;</td>
                <td width="100"  class="wrd_brk">&nbsp;</td>
                <td width="100" class="wrd_brk">&nbsp;</td>
                <td width="100" class="wrd_brk">&nbsp;</td>
				<td width="100" class="wrd_brk">&nbsp;</td>
            </tr>
			<tr>
			    <td width="120" class="wrd_brk" height="30">&nbsp;</td>
                <td width="120" class="wrd_brk">&nbsp;</td>
				<td width="120" class="wrd_brk">&nbsp;</td>
                <td width="100" class="wrd_brk">&nbsp;</td>
                <td width="100" class="wrd_brk">&nbsp;</td>
                <td width="100"  class="wrd_brk">&nbsp;</td>
                <td width="100" class="wrd_brk">&nbsp;</td>
                <td width="100" class="wrd_brk">&nbsp;</td>
				<td width="100" class="wrd_brk">&nbsp;</td>
            </tr>
			<tr>
			    <td width="120" class="wrd_brk" height="30">&nbsp;</td>
                <td width="120" class="wrd_brk">&nbsp;</td>
				<td width="120" class="wrd_brk">&nbsp;</td>
                <td width="100" class="wrd_brk">&nbsp;</td>
                <td width="100" class="wrd_brk">&nbsp;</td>
                <td width="100"  class="wrd_brk">&nbsp;</td>
                <td width="100" class="wrd_brk">&nbsp;</td>
                <td width="100" class="wrd_brk">&nbsp;</td>
				<td width="100" class="wrd_brk">&nbsp;</td>
            </tr>
			<tr>
			    <td width="120" class="wrd_brk" height="30">&nbsp;</td>
                <td width="120" class="wrd_brk">&nbsp;</td>
				<td width="120" class="wrd_brk">&nbsp;</td>
                <td width="100" class="wrd_brk">&nbsp;</td>
                <td width="100" class="wrd_brk">&nbsp;</td>
                <td width="100"  class="wrd_brk">&nbsp;</td>
                <td width="100" class="wrd_brk">&nbsp;</td>
                <td width="100" class="wrd_brk">&nbsp;</td>
				<td width="100" class="wrd_brk">&nbsp;</td>
            </tr>
			<tr>
			    <td width="120" class="wrd_brk" height="30">&nbsp;</td>
                <td width="120" class="wrd_brk">&nbsp;</td>
				<td width="120" class="wrd_brk">&nbsp;</td>
                <td width="100" class="wrd_brk">&nbsp;</td>
                <td width="100" class="wrd_brk">&nbsp;</td>
                <td width="100"  class="wrd_brk">&nbsp;</td>
                <td width="100" class="wrd_brk">&nbsp;</td>
                <td width="100" class="wrd_brk">&nbsp;</td>
				<td width="100" class="wrd_brk">&nbsp;</td>
            </tr>
			<tr>
			    <td width="120" class="wrd_brk" height="30">&nbsp;</td>
                <td width="120" class="wrd_brk">&nbsp;</td>
				<td width="120" class="wrd_brk">&nbsp;</td>
                <td width="100" class="wrd_brk">&nbsp;</td>
                <td width="100" class="wrd_brk">&nbsp;</td>
                <td width="100"  class="wrd_brk">&nbsp;</td>
                <td width="100" class="wrd_brk">&nbsp;</td>
                <td width="100" class="wrd_brk">&nbsp;</td>
				<td width="100" class="wrd_brk">&nbsp;</td>
            </tr>

			
			
        </table>
		<table width="950" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="table_header_2">
			<tr>
			    <td width="120" class="wrd_brk" height="20" colspan="9">&nbsp;</td>              
            </tr>
			<tr>
			    <td width="120" class="wrd_brk" rowspan="2" align="center"><b>ADVICE:</b></td>
                <td width="120" class="wrd_brk" rowspan="2"  align="center"><b>&nbsp;</b></td>
                <td width="100" class="wrd_brk" rowspan="10" align="center"><b>&nbsp;</b></td>
                <td width="150" class="wrd_brk" rowspan="2" align="center" colspan="3"><b>Machine Wise Plan Distribution Qty</b></td>  
				<td width="140" class="wrd_brk" rowspan="10" align="center"><b>&nbsp;</b></td>           
                <td width="140" class="wrd_brk"  rowspan="2" align="center" colspan="2"><b>Please Mark The Role The Each Role as Follows</b></td>
				
            </tr>
			<tr></tr>
			<tr>
			    <td width="120" class="wrd_brk" height="30" colspan="2">Please Strictly Avoid The Following Faults….</td>              
				<td width="120" class="wrd_brk">MC No</td>
                <td width="100" class="wrd_brk">M/C. Dia && GG</td>
                <td width="100" class="wrd_brk">Prog. Qty</td>  
                <td width="140" class="wrd_brk">1. Manufacturing Factory Name</td>
                <td width="140" class="wrd_brk">6. Fabrics Type</td>
			
            </tr>
			<tr>
			    <td width="120" class="wrd_brk" height="30" >1. Patta</td>              
				<td width="120" class="wrd_brk">8. Sinker mark</td>
                <td width="100" class="wrd_brk"> </td>
                <td width="100" class="wrd_brk"> </td>  
				<td width="100" class="wrd_brk"> </td>  
                <td width="140" class="wrd_brk">2. Company Name. </td>
                <td width="140" class="wrd_brk">7. Finished Dia</td>
            </tr>
			<tr>
			    <td width="120" class="wrd_brk" height="30" >2. Loop</td>              
				<td width="120" class="wrd_brk">9. Needle mark</td>
                <td width="100" class="wrd_brk"> </td>
                <td width="100" class="wrd_brk"> </td>  
				<td width="100" class="wrd_brk"> </td>  
                <td width="140" class="wrd_brk">3. Buyer, Style,Order no.</td>
                <td width="140" class="wrd_brk">8. Finished Gsm & Color</td>
			
            </tr>
			<tr>
			    <td width="120" class="wrd_brk" height="30" >3. Hole</td>              
				<td width="120" class="wrd_brk">10. Oil mark</td>
                <td width="100" class="wrd_brk"> </td>
                <td width="100" class="wrd_brk"> </td>  
				<td width="100" class="wrd_brk"> </td>  
                <td width="140" class="wrd_brk">4. Yarn Count, Lot & Brand </td>
                <td width="140" class="wrd_brk">9. Yarn Composition</td>
			
            </tr>
			<tr>
			    <td width="120" class="wrd_brk" height="30" >4. Star marks</td>              
				<td width="120" class="wrd_brk">11. Dia mark/Crease Mark</td>
                <td width="100" class="wrd_brk"> </td>
                <td width="100" class="wrd_brk"> </td>  
				<td width="100" class="wrd_brk"> </td>  
                <td width="140" class="wrd_brk">5. M/C No., Dia, Stitch Length </td>
                <td width="140" class="wrd_brk">10. Knit Program No</td>
			
            </tr>
			<tr>
			    <td width="120" class="wrd_brk" height="30" >5. Barre</td>              
				<td width="120" class="wrd_brk">12. Wheel Free</td>
                <td width="100" class="wrd_brk"> </td>
                <td width="100" class="wrd_brk"> </td>  
				<td width="100" class="wrd_brk"> </td>  
		
			
            </tr>
			<tr>
			    <td width="120" class="wrd_brk" height="30" >6. Drop Stitch</td>              
				<td width="120" class="wrd_brk">13. Slub</td>
                <td width="100" class="wrd_brk"> </td>
                <td width="100" class="wrd_brk"> </td> 
				<td width="100" class="wrd_brk"> </td>   
				
			
            </tr>
			<tr>
			    <td width="120" class="wrd_brk" height="30" >7. Lot mixing</td>              
				<td width="120" class="wrd_brk">14. Other contamination</td>
                <td width="100" class="wrd_brk" colspan="2" align="right"><b>Total:</b></td>  
				<td width="100" class="wrd_brk"> </td>         
				
			
            </tr>
			<tr>
			    <td width="120" class="wrd_brk"  colspan="9">&nbsp;</td>              
            </tr>
			<tr>
			    <td width="120" class="wrd_brk" height="30" colspan="9"><b>বিঃ দ্রঃ</b></td>            
            </tr>
			<tr>
			    <td width="120" class="wrd_brk" height="40" colspan="9">
					<b>* প্রোগ্রামের শেষ পর্যায়ে অবশ্যই সূতা মিল করে চালাতে হবে।<br>
					* সূতা মেশিনে উঠানোর সময় ১/১ করে উঠতে হবে।<br>
					* রোল মার্কিং সঠিকভাবে করতে হবে।<br>
					* রোলে কোন প্রকার কাটা-কাটি বা ভুল লেখা চলবেনা।<br>
					* রোলের গায়ে তারিখ/ অপারেটরের নাম এবং শিফট অবশ্যই লিখতে হবে।</b>
				</td>            
            </tr>
			
		
			<tr>
			    <td width="120" class="wrd_brk" height="50" colspan="9" align="center">&nbsp;</td> 
            </tr>
			<tr>
			    <td width="120" class="wrd_brk"  height="30" colspan="2" align="center"><b>Prepared By</b></td> 
				<td width="120" class="wrd_brk"  height="30" colspan="3" align="center"><b>Production Officer</b></td>            
				<td width="120" class="wrd_brk"  height="30" colspan="2" align="center"><b>Q.C. Officer</b></td> 
				<td width="120" class="wrd_brk" height="30" colspan="2" align="center"><b>Knitting Manager</b></td> 
            </tr>
			
		</table>
       
    </div>
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
			</scrip>
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