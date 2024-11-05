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
    
    if($cbo_buyer_name==0) $buyer_cond=""; else $buyer_cond=" and a.buyer_name='$cbo_buyer_name'";
    
    $buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	

	$sql="Select a.job_no_prefix_num, a.buyer_name as party_id, a.style_ref_no as cust_style_ref, $year_field, b.id, b.po_number as order_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.company_name='$company_id' $year_field_cond $buyer_cond";


//    $sql="select a.party_id, a.subcon_job, a.job_no_prefix_num, $year_field, b.order_no, b.cust_style_ref, b.main_process_id from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.company_id='$company_id' $buyer_cond $year_field_cond order by a.id desc";	
	
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

		// echo"Imrul";
		//======================== GETTING FORM DATA ===========================
		$cbo_company_id=str_replace("'","",$cbo_company_id);
		$job_no=str_replace("'","",$txt_job_no);
		$txt_style_ref=str_replace("'","",$txt_style_ref);
		$txt_order_no=str_replace("'","",$txt_order_no);
		$year_id=str_replace("'","",$cbo_year);
		$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
		$txt_date_from = str_replace("'", "", $txt_date_from);
		$txt_date_to = str_replace("'", "", $txt_date_to);
		// echo $cbo_company."_".$cbo_buyer_id."_".$year_id."_".$job_no."_".$txt_style_ref."_".$txt_order_no."_".$txt_production_date;die;

		if($cbo_company_id!=0) $company_cond=" and a.company_id='$cbo_company_id'"; else $cbo_company_id="";
		if($cbo_buyer_id!=0) $buyer_id_cond=" and a.party_id='$cbo_buyer_id'"; else $buyer_id_cond="";
		if($year_id!=0) $year_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id"; else $year_cond="";
		
		if ($job_no==""){ $job_no_cond="";$job_no_cond2="";} else{ $job_no_cond2=" and d.job_no_prefix_num=$job_no ";$job_no_cond=" and e.job_no_prefix_num=$job_no";}
		//if($txt_style_ref!="") $style_ref_cond="%".trim($txt_style_ref)."%"; else $style_ref_cond="%%";
		if ($txt_style_ref!='') $style_ref_cond=" and b.cust_style_ref like '%$txt_style_ref%'"; else $style_ref_cond="";
		//if(trim($txt_order_no)!="") $order_no_cond="%".trim($txt_order_no)."%"; else $order_no_cond="%%";
		if ($txt_order_no!=''){ $order_no_cond=" and c.po_number like '%$txt_order_no%'"; $order_no_cond2=" and f.order_no like '%$txt_order_no%'";} else{ $order_no_cond="";$order_no_cond2="";}
		
		
		$date_cond="";$inbound_date_cond="";$dying_date_cond="";
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
		 	if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
				
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
				
			}
			$date_cond="and b.delivery_date between '$txt_date_from' and '$txt_date_to'";
			$inbound_date_cond="and a.bill_date between '$txt_date_from' and '$txt_date_to'";
			$dying_date_cond="and a.delivery_date between '$txt_date_from' and '$txt_date_to'";
		}





		
		// $data_sql=" select a.delivery_prefix_num as challan_no, b.width_dia_type, a.delivery_date, b.sub_process_id,sum(b.carton_roll) as carton_roll, b.color_id, b.batch_id, b.order_id, b.id as id, b.process_id as process_id,
		// b.item_id as item_id, sum(b.gray_qty) as gray_qty, sum(b.delivery_qty) as delivery_qty, 0 as type ,a.party_id from subcon_delivery_mst a, subcon_delivery_dtls b
		// where a.company_id='17' and a.id=b.mst_id and a.party_id='26' and a.process_id=4 and b.process_id in (3,4) and a.status_active=1  and a.bill_date BETWEEN '$txt_date_from' and '$txt_date_to' 
		// group by a.delivery_prefix_num, b.width_dia_type, a.delivery_date,b.sub_process_id, b.color_id, b.batch_id, b.order_id, b.id, b.process_id, b.item_id,a.party_id ";

		// $data=sql_select($data_sql);

		// foreach($data as $row){


		// }


			
		$determ_arr = return_library_array( "select id, construction from lib_yarn_count_determina_mst",'id','construction');



			// $datas=sql_select(" select a.delivery_prefix_num as challan_no, b.width_dia_type, a.delivery_date,b.sub_process_id,sum(b.carton_roll) as carton_roll, b.color_id, b.batch_id, b.order_id, b.id as id, b.process_id as process_id,b.item_id as item_id,sum(b.gray_qty) as gray_qty, sum(b.delivery_qty) as delivery_qty, 1 as type ,a.party_id,d.item_description	from subcon_delivery_mst a , subcon_delivery_dtls b,pro_batch_create_mst c ,pro_batch_create_dtls d 
			// where a.company_id='17' and a.id=b.mst_id and a.party_id='26' and c.id=b.batch_id and  c.id=d.mst_id and c.entry_form=36 and a.process_id=4 and b.process_id in (3,4) and b.id in (2159) and a.status_active=1 and b.bill_status=1 $year_cond	group by a.delivery_prefix_num, b.width_dia_type, a.delivery_date,b.sub_process_id, b.color_id, b.batch_id, b.order_id, b.id, b.process_id, b.item_id,a.party_id,d.item_description	order by type DESC");


			$job_order_arr=array();
			$sql_job="Select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $order_id_cond";
			//echo $sql_job;
			$sql_job_result =sql_select($sql_job);
			foreach($sql_job_result as $row)
			{
				$job_order_arr[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
				$job_order_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
				$job_order_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
				$job_order_arr[$row[csf('id')]]['po']=$row[csf('po_number')];
			}

			$currency_arr=return_library_array( "select job_no,exchange_rate from wo_pre_cost_mst", "job_no","exchange_rate"  );

			//===========================================================================================================
			$data_array=sql_select("select id, job_no, fabric_description, cons_process, process_loss, req_qnty, avg_req_qnty, is_apply_last_update, charge_unit, amount, color_break_down, charge_lib_id, status_active from wo_pre_cost_fab_conv_cost_dtls where   status_active=1 and is_deleted=0   order by id");

					if(count($data_array)>1){

						foreach ($data_array as $row) {
								if($row[csf('cons_process')]==31){
									$color_break_down=explode("_",$row[csf('color_break_down')]);
		
								$erp_buget_color_rate[$row[csf('job_no')]][$row[csf('cons_process')]][$color_break_down[0]]['charge_unit']=$color_break_down[1]*$currency_arr[$row[csf('job_no')]];
								}else{


								$erp_buget_rate[$row[csf('job_no')]]['amount']=$row[csf('amount')];
								$erp_buget_rate[$row[csf('job_no')]]['charge_unit']=$row[csf('charge_unit')]*$currency_arr[$row[csf('job_no')]];
							}	
						}			
					}

					// echo "<pre>";
					// print_r($erp_buget_color_rate);

				//=================================Sub-Contract Order Entry===============================================
				$sub_con_data=sql_select("select id,job_no_mst,order_no,order_quantity,order_uom,rate,amount,order_rcv_date,delivery_date,cust_buyer,cust_style_ref,main_process_id,status_active from subcon_ord_dtls where status_active<>3  and is_deleted=0");
				if(count($sub_con_data)>1){
					foreach ($sub_con_data as $row) {
						$erp_buget_rate[$row[csf('job_no_mst')]]['amount']+=$row[csf('amount')];
						$erp_buget_rate[$row[csf('job_no_mst')]]['charge_unit']=$row[csf('rate')];
					}						
				}





			//====================================================sub contact inbound===========================================
			$data=sql_select("select b.id as upd_id, b.delivery_id, b.delivery_date, b.challan_no, b.item_id, b.body_part_id, b.color_id, b.batch_id, b.febric_description_id, b.dia_width_type, b.add_process, b.delivery_qty, b.lib_rate_id, b.rate, b.add_rate_id, b.add_rate, b.amount, b.remarks, b.order_id,
			b.color_range_id,b.currency_id,d.item_description,a.party_id,a.company_id, f.order_no as po_number,e.subcon_job,f.cust_style_ref as style_ref_no, f.cust_buyer as buyer_name from subcon_inbound_bill_mst a,subcon_inbound_bill_dtls b,pro_batch_create_dtls d,subcon_ord_mst e, subcon_ord_dtls f where b.mst_id=a.id  and b.batch_id=d.mst_id and  e.subcon_job=f.job_no_mst and  f.id=b.order_id and b.status_active=1 and b.is_deleted=0 $inbound_date_cond $company_cond $year_cond $job_no_cond $order_no_cond2 group by 
			b.id , b.delivery_id, b.delivery_date, b.challan_no, b.item_id, b.body_part_id, b.color_id, b.batch_id, b.febric_description_id, b.dia_width_type,
			b.add_process, b.delivery_qty, b.lib_rate_id, b.rate, b.add_rate_id, b.add_rate, b.amount, b.remarks, b.order_id, b.color_range_id,b.currency_id,d.item_description,
			a.party_id,a.company_id, f.order_no ,e.subcon_job,f.cust_style_ref , f.cust_buyer  order by b.id ASC");

			



			$party_wise_data=array();
			foreach($data as $row){

					
					$outftype=explode(",",$row[csf('item_description')]);
					$party_wise_data[$row[csf('party_id')]][$row[csf('order_id')]][$row[csf('add_process')]][$row[csf('color_id')]][$outftype[0]]['grey_qty']+=$row[csf('delivery_qty')];
					$party_wise_data[$row[csf('party_id')]][$row[csf('order_id')]][$row[csf('add_process')]][$row[csf('color_id')]][$outftype[0]]['rate']=$row[csf('rate')]+$row[csf('add_rate')];
					$party_wise_data[$row[csf('party_id')]][$row[csf('order_id')]][$row[csf('add_process')]][$row[csf('color_id')]][$outftype[0]]['amount']=$row[csf('amount')];
					$party_wise_data[$row[csf('party_id')]][$row[csf('order_id')]][$row[csf('add_process')]][$row[csf('color_id')]][$outftype[0]]['party_name']=$buyer_name_arr[$row[csf('party_id')]];
					$party_wise_data[$row[csf('party_id')]][$row[csf('order_id')]][$row[csf('add_process')]][$row[csf('color_id')]][$outftype[0]]['po_number']=$row[csf('po_number')];
					$party_wise_data[$row[csf('party_id')]][$row[csf('order_id')]][$row[csf('add_process')]][$row[csf('color_id')]][$outftype[0]]['job_no']=$row[csf('subcon_job')];
					$party_wise_data[$row[csf('party_id')]][$row[csf('order_id')]][$row[csf('add_process')]][$row[csf('color_id')]][$outftype[0]]['style_ref_no']=$row[csf('style_ref_no')];
					$party_wise_data[$row[csf('party_id')]][$row[csf('order_id')]][$row[csf('add_process')]][$row[csf('color_id')]][$outftype[0]]['buyer_name']=$row[csf('buyer_name')];

			}


			//====================================================sub contact outbound===========================================
			$supplier_library_arr=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name"  );
		

			// $outbound_data=sql_select("select b.id, b.receive_id, b.receive_date, b.item_id, b.wo_num_id, b.receive_qty, b.uom, b.rate, b.amount,  b.currency_id, b.process_id, b.prod_mst_id, b.order_id, b.color_id, b.body_part_id,b.febric_description_id,b.batch_id, b.challan_no, b.rec_qty_pcs, b.source,a.supplier_id as  party_id ,d.job_no,d.buyer_name, d.style_ref_no,c.po_number,a.company_id from subcon_outbound_bill_mst a,subcon_outbound_bill_dtls b left join wo_po_break_down c on c.id=b.order_id join wo_po_details_master d on d.job_no=c.job_no_mst where a.id=b.mst_id $order_no_cond $job_no_cond2 $inbound_date_cond and a.status_active=1 and a.is_deleted=0 and b.process_id=4 order by b.id ASC");

			$outbound_data=sql_select("select b.id, b.receive_id, b.receive_date,e.cons_process, b.item_id, b.wo_num_id, b.receive_qty, b.uom, b.rate, b.amount, b.currency_id, b.process_id, b.prod_mst_id,
			b.order_id, b.color_id,b.body_part_id,b.febric_description_id,b.batch_id, b.challan_no, b.rec_qty_pcs, b.source,a.supplier_id as party_id ,d.job_no,d.buyer_name, d.style_ref_no,c.po_number,a.company_id from subcon_outbound_bill_mst a,subcon_outbound_bill_dtls b left join wo_po_break_down c on c.id=b.order_id join wo_po_details_master d on d.job_no=c.job_no_mst left join wo_pre_cost_fab_conv_cost_dtls e on d.job_no=e.job_no where a.id=b.mst_id $order_no_cond $job_no_cond2 $inbound_date_cond and a.status_active=1 and a.is_deleted=0 and b.process_id=4 and e.cons_process !=1 group by b.id, b.receive_id, b.receive_date,e.cons_process, b.item_id, b.wo_num_id, b.receive_qty, b.uom, b.rate, b.amount, b.currency_id, b.process_id, b.prod_mst_id,b.order_id, b.color_id,b.body_part_id,b.febric_description_id,b.batch_id, b.challan_no, b.rec_qty_pcs, b.source,a.supplier_id ,d.job_no,d.buyer_name, d.style_ref_no,c.po_number,a.company_id order by b.id ASC");

			
			

			
		
			foreach($outbound_data as $row){

			
					$ftype=$determ_arr[$row[csf('febric_description_id')]];
					$party_wise_data[$row[csf('party_id')]][$row[csf('order_id')]][$row[csf('cons_process')]][$row[csf('color_id')]][$ftype]['grey_qty']+=$row[csf('receive_qty')];
					$party_wise_data[$row[csf('party_id')]][$row[csf('order_id')]][$row[csf('cons_process')]][$row[csf('color_id')]][$ftype]['rate']=$row[csf('rate')];
					$party_wise_data[$row[csf('party_id')]][$row[csf('order_id')]][$row[csf('cons_process')]][$row[csf('color_id')]][$ftype]['amount']=$row[csf('amount')];
					$party_wise_data[$row[csf('party_id')]][$row[csf('order_id')]][$row[csf('cons_process')]][$row[csf('color_id')]][$ftype]['job_no']=$row[csf('job_no')];
					$party_wise_data[$row[csf('party_id')]][$row[csf('order_id')]][$row[csf('cons_process')]][$row[csf('color_id')]][$ftype]['buyer_name']=$row[csf('buyer_name')];
					$party_wise_data[$row[csf('party_id')]][$row[csf('order_id')]][$row[csf('cons_process')]][$row[csf('color_id')]][$ftype]['po_number']=$row[csf('po_number')];
					$party_wise_data[$row[csf('party_id')]][$row[csf('order_id')]][$row[csf('cons_process')]][$row[csf('color_id')]][$ftype]['style_ref_no']=$row[csf('style_ref_no')];
					$party_wise_data[$row[csf('party_id')]][$row[csf('order_id')]][$row[csf('cons_process')]][$row[csf('color_id')]][$ftype]['party_name']=$supplier_library_arr[$row[csf('party_id')]];

			}



			// echo "<pre>";
			// print_r($party_wise_data);


			//==============Dyeing And Finishing Bill Issue============================================
				$dying_data=sql_select("select a.delivery_prefix_num as challan_no, b.width_dia_type, a.delivery_date, b.sub_process_id,sum(b.carton_roll) as carton_roll, b.color_id, b.batch_id, b.order_id, b.id as id, b.process_id as process_id,
				b.item_id as item_id, sum(b.gray_qty) as gray_qty, b.delivery_qty,b.bill_status,d.item_description,a.party_id
				 from subcon_delivery_mst a, subcon_delivery_dtls b,pro_batch_create_mst c,pro_batch_create_dtls d 
				 where  a.id=b.mst_id  $company_cond and c.id=b.batch_id and c.id=d.mst_id  and c.entry_form=36
				 and a.status_active=1  and b.bill_status=1  group by a.delivery_prefix_num, b.width_dia_type,a.delivery_date,b.sub_process_id, b.color_id, b.batch_id, b.order_id, b.id, b.process_id, b.item_id,b.bill_status,d.item_description, b.delivery_qty,a.party_id");
				 

				foreach($dying_data as $row){

					$ftype=$row[csf('item_description')];
					$finish_qty_arr[$row[csf('party_id')]][$row[csf('order_id')]][$row[csf('color_id')]][$ftype]['finish_qty']+=$row[csf('delivery_qty')];


				}

			//==============Dyeing And Finishing Bill Issue===============out bound==========================
			$dying_data_out_bound=sql_select("SELECT a.id as mst_id, b.batch_id, b.prod_id, b.body_part_id, b.febric_description_id as fab_desc_id,b.color_id, sum(b.batch_issue_qty) as rec_qnty, sum(b.grey_used) as grey_qty, b.order_id as po_breakdown_id, b.booking_id, b.booking_no, b.process_id ,a.dyeing_company as party_id FROM inv_receive_mas_batchroll a, pro_grey_batch_dtls b WHERE a.id=b.mst_id and a.entry_form=92 $company_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by a.id, b.booking_id, b.batch_id, b.prod_id, b.body_part_id, b.febric_description_id, b.color_id, b.order_id, b.booking_no,b.process_id,a.dyeing_company  ");


			foreach($dying_data_out_bound as $row){

				$ftype=$determ_arr[$row[csf('fab_desc_id')]];				
				$finish_qty_arr[$row[csf('party_id')]][$row[csf('po_breakdown_id')]][$row[csf('color_id')]][$ftype]['finish_qty']+=$row[csf('rec_qnty')];


			}

				// echo "<pre>";
				// print_r($finish_qty_arr);

				


		?>
			<fieldset style="width:1370px">
					<table width="1370" cellpadding="0" cellspacing="0"> 
						<tr class="form_caption">
							<td align="center"><p style="font-size:18px; font-weight:bold;">
							Daily Order and Style Wise Sub Contract Production Report</p></td> 
						</tr>
						<tr class="form_caption">
							<td align="center"><p style="font-size:16px; font-weight:bold;">
							<? echo $company_name_arr[$cbo_company_id]; ?><p></td> 
						</tr>
					
					</table>
					<br />
					<!-- ========= Details Part ======== -->
					<table class="rpt_table" width="1360" cellpadding="0" cellspacing="0" border="1" rules="all">
						<thead>
							<tr>
								<th width="80" colspan="10"></th>					
								<th colspan="3" width="80" align="center">Dyeing Bill Rate</th>
								<th colspan="2" width="80" align="center">ERP Budget Rate</th>
								<th  width="80" align="center">Variance</th>
							</tr>
							<tr>
								<th width="80">Party name</th>
								<th width="80">Buyer</th>
								<th width="80">Job No</th>
								<th width="80">Order no</th>
								<th width="80">Style Number</th>
								<th width="80">F/Type</th>
								<th width="80">Process</th>
								<th width="80">Color</th>
								<th width="80">Grey kg</th>
								<th width="80">Finish kg</th>
								
								<th width="80">Bill Rate Tk</th>
								<th width="80">Bill Amount Tk</th>
								<th width="80">PL%</th>
								
								<th width="80">ERP Rate Tk</th>
								<th width="80">ERP Bill Amount Tk</th>
								<th width="80">Rate Variance</th>
								
							</tr>
						</thead>
					</table>
					<table class="rpt_table" width="1360" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<tbody id="scroll_body">
							<?
							    $i=1;
								// $party_wise_data[$row[csf('party_id')]][$row[csf('order_id')]][$row[csf('color_id')]][$ftype[0]]['grey_qty']

								$total_grey_qnty=0;$total_bill_amount=0;$total_erp_bill_amount=0;$total_varience=0;
								foreach($party_wise_data as $party_key => $party_value)
								{
									foreach($party_value as $order_key => $order_value)
									{
										foreach($order_value as $process_key => $process_value)
									    {
										foreach($process_value as $color_key => $color_value)
										{
											foreach($color_value as $ftype_key => $value)
											{
											if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											$finish_qty=$finish_qty_arr[$party_key][$order_key][$color_key][$ftype_key]['finish_qty'];
											?>
											<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
										
												<td width="80" align="left" title="<?=$party_key;?>"><? echo $value['party_name']; ?></td>
												<td width="80" align="left"><? echo $buyer_name_arr[$value['buyer_name']]; ?></td>
												<td width="80" align="left"><? echo $value['job_no']; ?></td>
												<td width="80" align="left" title="<?=$order_key;?>"><div style="word-wrap:break-word; width:80px"><? echo $value['po_number']; ?></div></td>
												<td width="80" align="center"><div style="word-wrap:break-word; width:80px"><? echo $value['style_ref_no']; ?></div></td>
												<td width="80" align="right"><? echo $ftype_key; ?></td>
												<td width="80" align="right" title="<?=$process_key;?>"><? echo $conversion_cost_head_array[$process_key]; ?></td>
												<td width="80" align="right" title="<?=$color_key;?>"><? echo $color_library[$color_key]; ?></td>

												<td width="80" align="right"><? echo number_format($value['grey_qty'],0); ?></td>
												<td width="80" align="right"><? echo number_format($finish_qty,0); ?></td>
												
												<td width="80" align="right">
												<? echo number_format($value['rate'],2); ?>
												</td>
												<td width="80" align="right"><? echo number_format($value['amount'],2);; ?></td>
												
												<td width="80" align="right"><?
												$pl=(($value['grey_qty']-$finish_qty)/$value['grey_qty'])*100;
												 echo number_format($pl,2); ?></td>
												<td width="80" align="right" title="ex_rate=<?=$currency_arr[$value['job_no']];?>">
												    <? 
												
													if($process_key==31){
															$erp_rate=$erp_buget_color_rate[$value['job_no']][$process_key][$color_key]['charge_unit'];
													}else{
														$erp_rate=$erp_buget_rate[$value['job_no']]['charge_unit'];
													}

														echo number_format($erp_rate,2);;
													?>
												</td>
												<td width="80" align="right" title="=Grey kg*ERP Rate Tk"><? echo $erp_rate*$value['grey_qty'];; ?></td>
												<td width="80" align="right" title="=Bill Rate Tk-ERP Rate Tk">
													<? 
													
														echo number_format($value['rate']-$erp_rate,0);
													?>
												</td>
												
											
											</tr>
											<?
											$i++;
											$total_grey_qnty+=$value['grey_qty'];
											$total_bill_amount+=$value['amount'];
											$total_finish_qnty+=$finish_qty;
											$total_erp_bill_amount+=$erp_rate*$value['grey_qty'];;
											$total_varience+=$value['rate']-$erp_rate;
											
										}	
									  }
									}
									}?>
									<tr>
							
								
								<td width="80" align="right" style="font-weight:bold;" colspan="8">Total</td>
								<td width="80" align="right" style="font-weight:bold;"><? echo number_format($total_grey_qnty,0);?></td>
								<td width="80" align="right" style="font-weight:bold;"><? echo number_format($total_finish_qnty,0);?></td>
								<td width="80" align="right" style="font-weight:bold;"></td>
								
								<td width="80" align="right" style="font-weight:bold;"><? echo number_format($total_bill_amount,0);?></td>
								<td width="80" align="right" style="font-weight:bold;"></td>
								<td width="80" align="right" style="font-weight:bold;"></td>
								<td width="80" align="right" style="font-weight:bold;"><? echo number_format($total_erp_bill_amount,0);?></td>
								<td width="80" align="right" style="font-weight:bold;"><? echo number_format($total_varience,0);?></td>
								
							
							</tr> 
								<?
								}
							?>
							<!-- <tr>
							
                                
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
							
							</tr>  -->
						</tbody>                   
					</table>
			</fieldset>  
		<?
		
	

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