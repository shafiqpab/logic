<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 150, "select id, location_name from lib_location where status_active =1 and is_deleted=0 and company_id=$data order by location_name","id,location_name", 1, "--Select Location--", $selected, "","" );
	exit();
}

if ($action=="load_drop_down_store")
{
	$data=explode("_",$data);
	$sql_cat_cond="";
	if($data[1]) $sql_cat_cond=" and b.category_type in($data[1])";
	echo create_drop_down( "cbo_store_id", 150, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id in($data[0]) $sql_cat_cond and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1,"--Select store--",0,"");	
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_location_id=str_replace("'","",$cbo_location_id);
	$cbo_item_category=str_replace("'","",$cbo_item_category);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$cbo_value_with = str_replace("'","",$cbo_value_with);
	$cbo_job_year = str_replace("'","",$cbo_job_year);
	$cbo_store_id = str_replace("'","",$cbo_store_id);	
	
	$rack_shalf_bin_library=return_library_array( "select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name"  );
	$store_library=return_library_array( "select id, store_name from lib_store_location", "id", "store_name"  );
	$brand_library=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name"  );
	$season_library=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name"  );
	$floor_library=return_library_array( "select id, floor_name from lib_prod_floor", "id", "floor_name"  );
	$floor_library2 = return_library_array("select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name", "floor_id", "floor_room_rack_name");
	//echo "select id, floor_room_rack_name from pro_batch_create_mst where status_active=1 and company_id=$cbo_company_id and id=1024";die;
	$batch_library=return_library_array( "select id, batch_no from pro_batch_create_mst where status_active=1 and company_id=$cbo_company_id", "id", "batch_no"  );
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	if($cbo_item_category==2 || $cbo_item_category==4)// 4.accessories and 2. knit finish fab. 
	{
		$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
		//and b.company_name=$cbo_company_id
		$job_cond="";$job_order_id="";
		if($txt_job_no!="") $job_cond.=" and b.job_no like '%$txt_job_no'";
		if($txt_order_no!="") $job_cond.=" and a.PO_NUMBER like '%$txt_order_no%'";
		if($txt_style_ref!="") $job_cond.=" and b.style_ref_no like '%$txt_style_ref%'";
		if($cbo_job_year) $job_cond.=" and to_char(a.insert_date,'YYYY')='$cbo_job_year'"; 
		$po_sql=sql_select("SELECT a.id, a.po_number, b.job_no, b.buyer_name, b.style_ref_no, b.season_buyer_wise, b.season_year, b.brand_id  from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $job_cond");
		$job_ord_data=array();
		foreach($po_sql as $row)
		{
			if($txt_job_no!="" || $txt_style_ref!="")  $job_order_id.=$row[csf("id")].",";
			$job_ord_data[$row[csf("id")]]["po_number"]=$row[csf("po_number")];
			$job_ord_data[$row[csf("id")]]["job_no"]=$row[csf("job_no")];
			$job_ord_data[$row[csf("id")]]["buyer_name"]=$row[csf("buyer_name")];
			$job_ord_data[$row[csf("id")]]["style_ref_no"]=$row[csf("style_ref_no")];
			$job_ord_data[$row[csf("id")]]["season_buyer_wise"]=$row[csf("season_buyer_wise")];
			$job_ord_data[$row[csf("id")]]["season_year"]=$row[csf("season_year")];
			$job_ord_data[$row[csf("id")]]["brand_id"]=$row[csf("brand_id")];
		}
		$job_order_id=chop( $job_order_id,",");
	}
	
	$all_store_id="";
	if($cbo_location_id>0)
	{
		$store_location_sql="select a.id as store_id from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.location_id=$cbo_location_id and b.category_type=$cbo_item_category";
		$store_location_result=sql_select($store_location_sql);
		foreach($store_location_result as $row)
		{
			$all_store_id.=$row[csf("store_id")].",";
		}
		$all_store_id=chop($all_store_id,",");
	}
	$store_cond="";
	if($cbo_store_id>0) $store_cond=" and b.store_id=$cbo_store_id";
	else if($all_store_id!="") $store_cond=" and b.store_id in($all_store_id)";
	
	if($txt_date_from !="" && $txt_date_to  !="") $store_cond .=" and b.transaction_date between '$txt_date_from' and '$txt_date_to'";
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group where status_active=1", "id", "item_name"  );
	if($cbo_item_category==2)// knit finish fabric
	{
		//echo $job_order_id.test;die;
		$order_cond="";
		if($txt_job_no!="") $order_cond.=" and e.job_no like '%$txt_job_no'";
		if($txt_style_ref!="") $order_cond.=" and e.style_ref_no like '%$txt_style_ref%'";
		if($cbo_job_year) $order_cond.=" and to_char(e.insert_date,'YYYY')='$cbo_job_year'"; 
		if($txt_order_no!="") $order_cond.=" and d.PO_NUMBER like '%$txt_order_no%'";
		
		$sql="SELECT a.id as prod_id, a.item_group_id, a.item_description as product_name_details, a.unit_of_measure, a.avg_rate_per_unit, b.pi_wo_batch_no, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, c.po_breakdown_id, sum(case when c.trans_type in(1,4,5) then c.quantity else 0 end) as rcv_qnty, sum(case when c.trans_type in(2,3,6) then c.quantity else 0 end) as issue_qnty
		from product_details_master a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e 
		where a.id=b.prod_id and b.id=c.trans_id and b.prod_id=c.prod_id and a.item_category_id=$cbo_item_category and b.item_category=$cbo_item_category and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$cbo_company_id $store_cond $order_cond and d.id=c.po_breakdown_id and d.job_id=e.id
		group by a.id, a.item_group_id, a.item_description, a.unit_of_measure, a.avg_rate_per_unit, b.pi_wo_batch_no, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, c.po_breakdown_id
		order by a.id";
		//echo $sql;//die;
	}
	else if ($cbo_item_category==4)//accessories
	{
		$order_cond="";
		if($txt_job_no!="") $order_cond.=" and e.job_no like '%$txt_job_no'";
		if($txt_style_ref!="") $order_cond.=" and e.style_ref_no like '%$txt_style_ref%'";
		if($cbo_job_year) $order_cond.=" and to_char(e.insert_date,'YYYY')='$cbo_job_year'";
		if($txt_order_no!="") $order_cond.=" and d.PO_NUMBER like '%$txt_order_no%'"; 
		
		$sql="SELECT a.id as prod_id, a.item_group_id, a.item_description as product_name_details, a.unit_of_measure, a.avg_rate_per_unit, a.item_group_id, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, c.po_breakdown_id, sum(case when c.trans_type in(1,4,5) then c.quantity else 0 end) as rcv_qnty, sum(case when c.trans_type in(2,3,6) then c.quantity else 0 end) as issue_qnty, sum(case when c.trans_type in(1,4,5) then c.order_amount else 0 end) as rcv_amt, min(b.transaction_date) as transaction_date
		from product_details_master a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e
		where a.id=b.prod_id and b.id=c.trans_id and b.prod_id=c.prod_id and a.item_category_id=$cbo_item_category and b.item_category=$cbo_item_category and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$cbo_company_id $store_cond $order_cond and d.id=c.po_breakdown_id and d.job_id=e.id
		group by a.id, a.item_group_id, a.item_description, a.unit_of_measure, a.avg_rate_per_unit, a.item_group_id, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, c.po_breakdown_id
		order by a.id";
	}
	else
	{
		$sql="SELECT a.id as prod_id, a.item_group_id, a.item_description as product_name_details, a.unit_of_measure, a.avg_rate_per_unit, nvl(b.pi_wo_batch_no,0), b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, 0 as po_breakdown_id, sum(case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end) as rcv_qnty, sum(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end) as issue_qnty
		from product_details_master a, inv_transaction b
		where a.id=b.prod_id and a.item_category_id=$cbo_item_category and b.item_category=$cbo_item_category and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_id $store_cond
		group by a.id, a.item_group_id, a.item_description, a.unit_of_measure, a.avg_rate_per_unit, nvl(b.pi_wo_batch_no,0), b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box
		order by a.id";
	}
	//echo $sql;//die;
	$result=sql_select($sql);
	if ($cbo_item_category==4)
	{
		$table_width=2050;
	}else{
		$table_width=1750;
	}
	ob_start();
	?>
	<div style="width:<?=$table_width+20;?>px;">
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr><td style="font-size:20px; font-weight:bold; text-align:center;"><? echo $company_library[$cbo_company_id]; ?></td></tr>
			<tr><td>&nbsp;</td></tr>
		</table>
		<table border="1" class="rpt_table" rules="all" width="<?=$table_width;?>" cellpadding="0" cellspacing="0" align="left">
			<thead>
				<tr>
                	<th width="40">SL</th>
                    <th width="60">Product ID</th>
					<th width="100">Store</th>
                    <th width="100">Floor</th>
                    <th width="70">Room</th>
					<th width="70">Rack No</th>
					<th width="70">Shelf No</th>
                    <th width="70">Bin/Box</th>
					<th width="80">Job</th>
                    <th width="100">Order</th>
					<th width="100">Style Ref.</th>
					<th width="100">Buyer</th>
					<th width="80">Brand</th>
					<th width="80">Season</th>
					<th width="50">Season Year</th>
					<th width="100">Item Group</th>
                    <th width="120">Item Description</th>
					<?
					if ($cbo_item_category!=4)
					{	
					    ?>
						<th width="80">Batch</th>
						<?
					}
					?> 
					<th width="80">Receive Qnty</th>
					<th width="80">Issue Qnty</th>
					<th>In Hand</th>
                    <?
					if ($cbo_item_category==4)
					{	
					    ?>
						<th width="80">Rate</th>
                        <th width="80">Receive Value</th>
                        <th width="80">Issue Value</th>
                        <th width="80">Stock Value</th>
                        <th width="80">Age</th>
						<?
					}
					?>
                    
				</tr>
			</thead>
		</table>
		<!--<p style="font-size:16px; font-weight:bold;">Item Category : Knit Finish Fabrics</p>-->
		<div style="overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden; width:<?=$table_width+20;?>px;" id="scroll_body" align="left" >
		<table border="1" class="rpt_table" rules="all" width="<?=$table_width;?>" cellpadding="0" cellspacing="0" align="left" id="table_body">
			<tbody>
				<?
				$i=1;$p=1;
				foreach($result as $val)
				{
					if($val[csf('floor_id')]>0 && $val[csf('rack')]>0 && $val[csf('self')]>0)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$in_hand = $val[csf('rcv_qnty')]-$val[csf('issue_qnty')];
						if ($cbo_item_category==4)
						{
							$rate= $val[csf('rcv_amt')]/$val[csf('rcv_qnty')];
							$ageOfDays = datediff("d", $val[csf('transaction_date')], date("Y-m-d"));
						}
						
						if ($cbo_value_with==1)
						{
							if(number_format($in_hand,2,'.','')>0)
							{
								?>
		                        <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                                	<td width="40" align="center"><? echo $p; ?></td>
                                    <td width="60" align="center" style="word-break:break-all" title="<? echo $val[csf('prod_id')]; ?>"><p><? echo $val[csf('prod_id')]; ?>&nbsp;</p></td>
                                    <td width="100" align="center" style="word-break:break-all" title="<? echo $val[csf('store_id')]; ?>"><p><? echo $store_library[$val[csf('store_id')]]; ?>&nbsp;</p></td>
									<? if($cbo_item_category==2 || $cbo_item_category==4 || $cbo_item_category==15){?>
		                            <td width="100" align="center" style="word-break:break-all" title="<? echo $val[csf('floor_id')]; ?>"><p><? echo $floor_library2[$val[csf('floor_id')]]; ?>&nbsp;</p></td>
                                    <? }else{ ?>
                                    <td width="100" align="center" style="word-break:break-all" title="<? echo $val[csf('floor_id')]; ?>"><p><? echo $floor_library[$val[csf('floor_id')]]; ?>&nbsp;</p></td>
                                    <? } ?>
                                    <td width="70" align="center" style="word-break:break-all" title="<? echo $val[csf('room')]; ?>"><p><? echo $rack_shalf_bin_library[$val[csf('room')]]; ?>&nbsp;</p></td>
		                            <td width="70" align="center" style="word-break:break-all" title="<? echo $val[csf('rack')]; ?>"><p><? echo $rack_shalf_bin_library[$val[csf('rack')]]; ?>&nbsp;</p></td>
		                            <td width="70" align="center" style="word-break:break-all" title="<? echo $val[csf('self')]; ?>"><p><? echo $rack_shalf_bin_library[$val[csf('self')]]; ?>&nbsp;</p></td>
                                    <td width="70" align="center" style="word-break:break-all" title="<? echo $val[csf('bin_box')]; ?>"><p><? echo $rack_shalf_bin_library[$val[csf('bin_box')]]; ?>&nbsp;</p></td>
		                            <td width="80" style="word-break:break-all" title="<? echo "order id=".$val[csf('po_breakdown_id')];?>"><p><? echo $job_ord_data[$val[csf('po_breakdown_id')]]["job_no"];  ?>&nbsp;</p></td>
                                    <td width="100" style="word-break:break-all" title="<? echo "order id=".$val[csf('po_breakdown_id')];?>"><p><? echo $job_ord_data[$val[csf('po_breakdown_id')]]["po_number"];  ?>&nbsp;</p></td>
									<td width="100" style="word-break:break-all"><p><? echo $job_ord_data[$val[csf('po_breakdown_id')]]["style_ref_no"];  ?>&nbsp;</p></td>
		                            <td width="100" style="word-break:break-all" title="<? echo "order id=".$val[csf('po_breakdown_id')];?>"><p><? echo $buyer_library[$job_ord_data[$val[csf('po_breakdown_id')]]["buyer_name"]]; ?>&nbsp;</p></td>
									<td width="80" style="word-break:break-all"><p><? echo $brand_library[$job_ord_data[$val[csf('po_breakdown_id')]]["brand_id"]];  ?>&nbsp;</p></td>
									<td width="80" style="word-break:break-all"><p><? echo $season_library[$job_ord_data[$val[csf('po_breakdown_id')]]["season_buyer_wise"]];  ?>&nbsp;</p></td>
									<td width="50" style="word-break:break-all" align="center"><p><? echo $job_ord_data[$val[csf('po_breakdown_id')]]["season_year"];  ?>&nbsp;</p></td>
									<td width="100" style="word-break:break-all" title="<?= $val[csf('item_group_id')]; ?>"><p><? echo $item_group_arr[$val[csf('item_group_id')]];  ?>&nbsp;</p></td>
                                    <td width="120" style="word-break:break-all"><p><? echo $val[csf('product_name_details')];  ?>&nbsp;</p></td>
		                            <?
		                            if ($cbo_item_category!=4)
									{	
									   ?> 
		                            	<td width="80" style="word-break:break-all" title="<? echo "batch id=".$val[csf('pi_wo_batch_no')]." and item cat id =".$cbo_item_category;?>"><p><? if($cbo_item_category==2) echo $batch_library[$val[csf('pi_wo_batch_no')]]; else echo "";  ?>&nbsp;</p></td>
		                            	<?
		                            }
		                            ?>
		                            <td title="<? echo number_format($val[csf('rcv_qnty')],8);?>" width="80" align="right"><? echo number_format($val[csf('rcv_qnty')],2,'.',''); ?></td>
		                            <td title="<? echo number_format($val[csf('issue_qnty')],8);?>" width="80" align="right"><? echo number_format($val[csf('issue_qnty')],2,'.',''); ?></td>
		                            <td title="<? echo number_format($in_hand,8);?>" align="right"><? echo number_format($in_hand,2,'.',''); ?></td>
                                    <?
									if ($cbo_item_category==4)
									{
										?>
										<td title="<? echo $val[csf('rcv_amt')]."=".$val[csf('rcv_qnty')]."=".number_format($rate,8);?>" width="80" align="right"><? echo number_format($rate,2,'.',''); ?></td>
										<td width="80" align="right"><? $rcv_amt=$val[csf('rcv_qnty')]*$rate; echo number_format($rcv_amt,2,'.',''); ?></td>
										<td align="right" width="80"><? $issue_amt=$val[csf('issue_qnty')]*$rate; echo number_format($issue_amt,2,'.',''); ?></td>
										<td width="80" align="right"><? $stock_amt=$in_hand*$rate; echo number_format($stock_amt,2,'.',''); ?></td>
										<td width="80" align="center"><? echo $ageOfDays; ?></td>
										<?
										$tot_rcv_amt+=$rcv_amt;
										$tot_issue_amt+=$issue_amt;
										$tot_stock_amt+=$stock_amt;
									}
									?>
		                        </tr>
		                        <?
								$p++;
							}
		                    
	                    }
	                    else
	                    {
	                    	?>
	                        <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="40" align="center"><? echo $p; ?></td>
                                <td width="60" style="word-break:break-all" align="center" title="<? echo $val[csf('prod_id')]; ?>"><p><? echo $val[csf('prod_id')]; ?>&nbsp;</p></td>
                                <td width="100" style="word-break:break-all" align="center" title="<? echo $val[csf('store_id')]; ?>"><p><? echo $store_library[$val[csf('store_id')]]; ?>&nbsp;</p></td>
								<? if($cbo_item_category==2 || $cbo_item_category==4 || $cbo_item_category==15){?>
		                            <td width="100" style="word-break:break-all" align="center" title="<? echo $val[csf('floor_id')]; ?>"><p><? echo $floor_library2[$val[csf('floor_id')]]; ?>&nbsp;</p></td>
                                    <? }else{ ?>
                                    <td width="100" style="word-break:break-all" align="center" title="<? echo $val[csf('floor_id')]; ?>"><p><? echo $floor_library[$val[csf('floor_id')]]; ?>&nbsp;</p></td>
                                    <? } ?>
	                            <td width="70" style="word-break:break-all" align="center" title="<? echo $val[csf('room')]; ?>"><p><? echo $rack_shalf_bin_library[$val[csf('room')]]; ?>&nbsp;</p></td>
                                <td width="70" style="word-break:break-all" align="center" title="<? echo $val[csf('rack')]; ?>"><p><? echo $rack_shalf_bin_library[$val[csf('rack')]]; ?>&nbsp;</p></td>
	                            <td width="70" style="word-break:break-all" align="center" title="<? echo $val[csf('self')]; ?>"><p><? echo $rack_shalf_bin_library[$val[csf('self')]]; ?>&nbsp;</p></td>
                                <td width="70" style="word-break:break-all" align="center" title="<? echo $val[csf('bin_box')]; ?>"><p><? echo $rack_shalf_bin_library[$val[csf('bin_box')]]; ?>&nbsp;</p></td>
	                            <td width="80" style="word-break:break-all" title="<? echo "order id=".$val[csf('po_breakdown_id')];?>"><p><? echo $job_ord_data[$val[csf('po_breakdown_id')]]["job_no"];  ?>&nbsp;</p></td>
                                <td width="100" style="word-break:break-all" title="<? echo "order id=".$val[csf('po_breakdown_id')];?>"><p><? echo $job_ord_data[$val[csf('po_breakdown_id')]]["po_number"];  ?>&nbsp;</p></td>
								<td width="100" style="word-break:break-all"><p><? echo $job_ord_data[$val[csf('po_breakdown_id')]]["style_ref_no"];  ?>&nbsp;</p></td>
	                            <td width="100" style="word-break:break-all" title="<? echo "order id=".$val[csf('po_breakdown_id')];?>"><p><? echo $buyer_library[$job_ord_data[$val[csf('po_breakdown_id')]]["buyer_name"]]; ?>&nbsp;</p></td>
								<td width="80" style="word-break:break-all"><p><? echo $brand_library[$job_ord_data[$val[csf('po_breakdown_id')]]["brand_id"]];  ?>&nbsp;</p></td>
								<td width="80" style="word-break:break-all"><p><? echo $season_library[$job_ord_data[$val[csf('po_breakdown_id')]]["season_buyer_wise"]];  ?>&nbsp;</p></td>
								<td width="50" align="center"><p><? echo $job_ord_data[$val[csf('po_breakdown_id')]]["season_year"];  ?>&nbsp;</p></td>
								<td width="100" style="word-break:break-all" title="<?= $val[csf('item_group_id')]; ?>"><p><? echo $item_group_arr[$val[csf('item_group_id')]];  ?>&nbsp;</p></td>
                                <td width="120" style="word-break:break-all"><p><? echo $val[csf('product_name_details')];  ?>&nbsp;</p></td>
	                            <?
	                            if ($cbo_item_category!=4)
								{	
								    ?>
	                            	<td width="80" style="word-break:break-all" title="<? echo "batch id=".$val[csf('pi_wo_batch_no')]." and item cat id =".$cbo_item_category;?>"><p><? if($cbo_item_category==2) echo $batch_library[$val[csf('pi_wo_batch_no')]]; else echo "";  ?>&nbsp;</p></td>
	                            	<?
	                            }
	                            ?>
	                            <td title="<? echo number_format($val[csf('rcv_qnty')],8);?>" width="80" align="right"><p><? echo number_format($val[csf('rcv_qnty')],2,'.',''); ?></p></td>
	                            <td title="<? echo number_format($val[csf('issue_qnty')],8);?>" width="80" align="right"><p><? echo number_format($val[csf('issue_qnty')],2,'.',''); ?></p></td>
	                            <td title="<? echo number_format($in_hand,8);?>" align="right"><p><? echo number_format($in_hand,2,'.',''); ?></p></td>
                                <?
								if ($cbo_item_category==4)
								{
									?>
									<td title="<? echo number_format($rate,8);?>" width="80" align="right"><? echo number_format($rate,2,'.',''); ?></td>
									<td width="80" align="right"><? $rcv_amt=$val[csf('rcv_qnty')]*$rate; echo number_format($rcv_amt,2,'.',''); ?></td>
									<td align="right" width="80"><? $issue_amt=$val[csf('issue_qnty')]*$rate; echo number_format($issue_amt,2,'.',''); ?></td>
									<td width="80" align="right"><? $stock_amt=$in_hand*$rate; echo number_format($stock_amt,2,'.',''); ?></td>
									<td width="80" align="center"><? echo $ageOfDays; ?></td>
									<?
									$tot_rcv_amt+=$rcv_amt;
									$tot_issue_amt+=$issue_amt;
									$tot_stock_amt+=$stock_amt;
								}
								?>
	                        </tr>
	                        <?
							$p++;
	                    }    
                        $i++;
					}
				}
				?>
			</tbody>
		</table>
		</div>
        <table border="1" class="rpt_table" rules="all" width="<?=$table_width;?>" cellpadding="0" cellspacing="0" align="left">
			<tfoot>
				<tr>
                	<th width="40">&nbsp;</th>
                    <th width="60">&nbsp;</th>
					<th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="70">&nbsp;</th>
					<th width="70">&nbsp;</th>
					<th width="70">&nbsp;</th>
                    <th width="70">&nbsp;</th>
					<th width="80">&nbsp;</th>
                    <th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="50">&nbsp;</th>
					<th width="100">&nbsp;</th>
                    <th width="120">&nbsp;</th>
					<?
					if ($cbo_item_category!=4)
					{	
					    ?>
						<th width="80">&nbsp;</th>
						<?
					}
					?> 
					<th width="80">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th>&nbsp;</th>
                    <?
					if ($cbo_item_category==4)
					{	
					    ?>
						<th width="80">Total:</th>
                        <th width="80" id="value_tot_rcv_amt"><? echo number_format($tot_rcv_amt,2) ?></th>
                        <th width="80" id="value_tot_issue_amt"><? echo number_format($tot_issue_amt,2) ?></th>
                        <th width="80" id="value_tot_stock_amt"><? echo number_format($tot_stock_amt,2) ?></th>
                        <th width="80">&nbsp;</th>
						<?
					}
					?> 
                    
				</tr>
			</tfoot>
		</table>
	</div>
	<?
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("$user_id*.xls") as $filename) {
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html####$filename####$rpt_type"; 
	exit();
}

