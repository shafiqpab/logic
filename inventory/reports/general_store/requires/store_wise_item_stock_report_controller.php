<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
//--------------------------------------------------------------------------------------------

//load drop down supplier
/*if ($action == "load_drop_down_supplier") {
	if($data){$companyCon=" and a.tag_company='$data'";}
	else{$companyCon="";}
	echo create_drop_down("cbo_supplier", 120, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id $companyCon and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 0, "-- Select --", 0, "", 0);
	exit();
}

if ($action == "eval_multi_select") {
	echo "set_multiselect('cbo_supplier','0','0','','0');\n";
	exit();
}*/

$dyes_chemical_item_category=array(5=>"Chemicals",6=>"Dyes",7=>"Auxilary Chemicals",23=>'Dyes Chemicals & Auxilary Chemicals');
$general_dyes_item_category=$general_item_category+$dyes_chemical_item_category;
//echo "<pre>";print_r($general_dyes_item_category);die;

if ($action == "generate_report")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$type=str_replace("'","",$type);
	//echo $type;die;
	//cbo_company_name="+cbo_company_name+"&cbo_item_category="+cbo_item_category+"&cbo_item_group="+cbo_item_group+"&txt_description_id="+txt_description_id+"&cbo_store="+cbo_store+"&txt_date_from="+txt_date_from+"&value_with="+value_with+"&type="+type;
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_item_category=str_replace("'","",$cbo_item_category);
	$cbo_item_group=str_replace("'","",$cbo_item_group);
	$txt_description_id=str_replace("'","",$txt_description_id);
	$cbo_store=str_replace("'","",$cbo_store);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$value_with=str_replace("'","",$value_with);
	$type=str_replace("'","",$type);
	//echo $cbo_item_category;die;

	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1", "id", "company_name");
	$itemGroup 	= return_library_array("select id, item_name from lib_item_group", "id", "item_name");
	//$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	//$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$search_cond=$trans_store_cond="";
	$company_cond = ($cbo_company_name!="")?" and a.company_id in($cbo_company_name)":"";
	$search_cond .= ($cbo_item_category!="")?" and a.item_category_id in($cbo_item_category)":"";
	$search_cond .= ($cbo_item_group!="")?" and a.item_group_id in($cbo_item_group)":"";
	$search_cond .= ($txt_description_id!="")?" and a.id in($txt_description_id)":"";
	//$search_cond .= ($cbo_store!="")?" and b.store_id in($cbo_store)":"";
	$trans_store_cond .= ($cbo_store!="")?" and b.store_id in($cbo_store)":"";
	

	if ($db_type == 0) {
		$from_date = change_date_format($txt_date_from, 'yyyy-mm-dd');
	} else if ($db_type == 2) {
		$from_date = change_date_format($txt_date_from, '', '', 1);
	} else {
		$from_date = "";
	}

	if ($from_date != "")
		$date_cond = " and a.transaction_date<='$from_date'";

	
	if($cbo_item_category !="") $store_cond.=" and b.category_type in($cbo_item_category)";
	else $store_cond.=" and b.category_type in(".implode(",",array_keys($general_dyes_item_category)).")";
	$store_cond.=($cbo_store!="")?" and a.id in($cbo_store)":"";
	/*echo "select a.id, a.company_id, c.company_name, c.company_short_name, a.store_name 
	from lib_store_location a, lib_store_location_category b, lib_company c 
	where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0
	$company_cond  $store_cond
	group by  a.id, a.company_id, c.company_name, c.company_short_name, a.store_name
	order by a.company_id, a.store_name asc";die;*/
	//and b.category_type not in(1,2,3,5,6,7,12,13,14,23,24,25,28,30,31,42,43,51,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,95,96,97,98,102,103,104,105) 
	$stores = sql_select("select a.id, a.company_id, c.company_name, c.company_short_name, a.store_name 
	from lib_store_location a, lib_store_location_category b, lib_company c 
	where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0
	$company_cond  $store_cond
	group by  a.id, a.company_id, c.company_name, c.company_short_name, a.store_name
	order by a.company_id, a.store_name asc");
	$num_of_store=0;
	foreach ($stores as $store)
	{
		$company_id_arr[$store[csf("company_id")]]["company"] = $store[csf("company_name")];
		$company_id_arr[$store[csf("company_id")]]["company_colspan"]++;
		$num_of_store++;
	}
	
	$sql_receive = "Select a.company_id, a.item_category_id, a.item_group_id, a.item_description, a.sub_group_name, a.unit_of_measure, b.store_id,
	sum(case when b.transaction_type in (1,4,5) and b.transaction_date<='$from_date' then b.cons_quantity else 0 end) as rcv_total,
	sum(case when b.transaction_type in (1,4,5) and b.transaction_date<='$from_date' then b.cons_amount else 0 end) as rcv_total_amt,
	sum(case when b.transaction_type in (2,3,6) and b.transaction_date<='$from_date' then b.cons_quantity else 0 end) as issue_total,
	sum(case when b.transaction_type in (2,3,6) and b.transaction_date<='$from_date' then b.cons_amount else 0 end) as issue_total_amt
	from product_details_master a, inv_transaction b
	where a.id=b.prod_id and b.transaction_type in (1,2,3,4,5,6) and a.item_category_id in(".implode(",",array_keys($general_dyes_item_category)).") and b.item_category in(".implode(",",array_keys($general_dyes_item_category)).") and a.entry_form<>24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.cons_quantity>0 $company_cond $search_cond $trans_store_cond
	group by a.company_id, a.item_category_id, a.item_group_id, a.item_description, a.sub_group_name, a.unit_of_measure, b.store_id";
	//echo $sql_receive;die;
	$result_sql_receive = sql_select($sql_receive);
	foreach ($result_sql_receive as $row) {
		$receive_array[$row[csf("company_id")]][$row[csf("item_category_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]][$row[csf("unit_of_measure")]][$row[csf("store_id")]]['rcv_total'] += $row[csf("rcv_total")];
		$receive_array[$row[csf("company_id")]][$row[csf("item_category_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]][$row[csf("unit_of_measure")]][$row[csf("store_id")]]['rcv_total_amt'] += $row[csf("rcv_total_amt")];
		$issue_array[$row[csf("company_id")]][$row[csf("item_category_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]][$row[csf("unit_of_measure")]][$row[csf("store_id")]]['issue_total'] += $row[csf("issue_total")];
		$issue_array[$row[csf("company_id")]][$row[csf("item_category_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]][$row[csf("unit_of_measure")]][$row[csf("store_id")]]['issue_total_amt'] += $row[csf("issue_total_amt")];
		
		$stock_arr[$row[csf("item_category_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]][$row[csf("unit_of_measure")]]['stock_qnty']+=$row[csf("rcv_total")]-$row[csf("issue_total")];
		$stock_arr[$row[csf("item_category_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]][$row[csf("unit_of_measure")]]['stock_amt']+=$row[csf("rcv_total_amt")]-$row[csf("issue_total_amt")];
		
	}

	unset($result_sql_receive);
	//echo "<pre>";print_r($receive_array); echo "<pre>";print_r($issue_array);die;
	$width = (1000+($num_of_store*100));
	ob_start();
	?>
    <p style="font-size:18px; font-weight:bold; color:#F00;">Dynamic Columns Not Allow For HTML Search</p>
	<fieldset style="width:<? echo $width+20; ?>px;margin:5px auto;">
		<table width="<? echo $width; ?>" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
			<thead>
				<tr class="form_caption" style="border:none;">
					<td colspan="16" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td>
				</tr>
				<tr style="border:none;">
					<td colspan="16" align="center" style="border:none; font-size:14px;">
						<? echo ($cbo_company_name==0)?"All Company":"Company Name : ".$companyArr[str_replace("'", "", $cbo_company_name)]; ?>
					</td>
				</tr>
				<tr style="border:none;">
					<td colspan="16" align="center" style="border:none;font-size:12px; font-weight:bold">
						<? if ($txt_date_from != "" && $txt_date_from != "") echo "From " . change_date_format($txt_date_from, 'dd-mm-yyyy') . " To " . change_date_format($txt_date_from, 'dd-mm-yyyy') . ""; ?>
					</td>
				</tr>
				<tr>
					<th rowspan="2" width="40">SL</th>
					<th colspan="6">Description</th>
                    <th rowspan="2" width="100">Group Total Qnty.</th>
					<th rowspan="2" width="100">Group Total Value</th>
					<?
					$company_span=0;
					foreach ($company_id_arr as $company_id=>$company_row) {
						$company_span = $company_id_arr[$company_id]["company_colspan"];
						?>
						<th colspan="<? echo $company_span;?>"><? echo $company_row['company'];?></th>
						<?
					}
					?>
				</tr>
				<tr>
					<th width="120">Item Category</th>
					<th width="120">Item Group</th>
					<th width="80">Item Sub-group</th>
					<th width="150">Item Description</th>
					<th width="80">Item Size</th>
					<th width="80">UOM</th>
					<?
					foreach ($stores as $store) {
						?>
						<th width="100" title="<? echo $store[csf("id")];?>" style="word-break:break-all;"><? echo $store[csf("store_name")];?></th>
						<?
					}
					?>
				</tr>
			</thead>
		</table>
		<div style="max-height:325px; overflow-y:auto; width:<? echo $width+20; ?>px;" >
			<table class="rpt_table" border="1" width="<? echo $width; ?>" rules="all" id="table_body">
				<tbody>
					<?
					$sql = "select a.company_id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure
					from product_details_master a where a.status_active=1 and a.is_deleted=0 and a.item_category_id in(".implode(",",array_keys($general_dyes_item_category)).") and a.entry_form<>24 $company_cond $search_cond 
					group by a.company_id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure";
					//echo $sql;die;
					$result = sql_select($sql);
					$prod_data=array();
					foreach($result as $row)
					{
						if( $value_with==0 ) $stock_arr[$row[csf("item_category_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]][$row[csf("unit_of_measure")]]['stock_qnty']=1;
						if(number_format($stock_arr[$row[csf("item_category_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]][$row[csf("unit_of_measure")]]['stock_qnty'],2,'.','') >0 )
						{
							$prod_data[$row[csf("item_category_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]][$row[csf("unit_of_measure")]]["item_category_id"]=$row[csf("item_category_id")];
							$prod_data[$row[csf("item_category_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]][$row[csf("unit_of_measure")]]["item_group_id"]=$row[csf("item_group_id")];
							$prod_data[$row[csf("item_category_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]][$row[csf("unit_of_measure")]]["sub_group_name"]=$row[csf("sub_group_name")];			
							$prod_data[$row[csf("item_category_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]][$row[csf("unit_of_measure")]]["item_description"]=$row[csf("item_description")];
							$prod_data[$row[csf("item_category_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]][$row[csf("unit_of_measure")]]["item_size"]=$row[csf("item_size")];
							$prod_data[$row[csf("item_category_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]][$row[csf("unit_of_measure")]]["unit_of_measure"]=$row[csf("unit_of_measure")];
						}
					}
					
					//echo "<pre>";print_r($prod_data);die;
					$i = 1;
					$store_wise_recv_total=array();
					$store_wise_balance_total=$store_wise_amt_balance_total=0;
					foreach($prod_data as $item_cat=>$cat_data)
					{
						$cat_group_sub_total=$cat_group_sub_total_amt=0;
						foreach($cat_data as $group_id=>$group_data)
						{
							foreach($group_data as $descrip_dtls=>$des_data)
							{
								foreach($des_data as $uom=>$row)
								{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td align="center" width="40"><? echo $i;?></td>
										<td width="120" style="word-break:break-all;"><p><? echo $general_dyes_item_category[$row[("item_category_id")]]; ?></p></td>
										<td width="120" style="word-break:break-all;"><p><? echo $itemGroup[$row[("item_group_id")]]; ?></p></td>
										<td width="80" style="word-break:break-all;"><p><? echo $row[("sub_group_name")]; ?></p></td>
										<td width="150" style="word-break:break-all;"><p><? echo $row[("item_description")]; ?></p></td>
										<td width="80" title="Product ID=<? echo $row[csf('prod_id')]; ?>"><p><? echo $row[("item_size")]; ?></p></td>
										<td width="80" style="word-break:break-all;" align="center"><? echo $unit_of_measurement[$row[("unit_of_measure")]]; ?></td>
										<?
										$group_sub_total=$group_sub_total_amt=$total_receive=$total_receive_amt=$total_issue=$total_issue_amt=$store_wise_balance=$store_wise_amt_balance=0;
										foreach ($stores as $store) {
											$total_receive = $receive_array[$store[csf("company_id")]][$row[("item_category_id")]][$row[("item_group_id")]][$row[("item_description")]][$row[("unit_of_measure")]][$store[csf("id")]]['rcv_total'];
											$total_receive_amt = $receive_array[$store[csf("company_id")]][$row[("item_category_id")]][$row[("item_group_id")]][$row[("item_description")]][$row[("unit_of_measure")]][$store[csf("id")]]['rcv_total_amt'];
											$total_issue = $issue_array[$store[csf("company_id")]][$row[("item_category_id")]][$row[("item_group_id")]][$row[("item_description")]][$row[("unit_of_measure")]][$store[csf("id")]]['issue_total'];
											$total_issue_amt = $issue_array[$store[csf("company_id")]][$row[("item_category_id")]][$row[("item_group_id")]][$row[("item_description")]][$row[("unit_of_measure")]][$store[csf("id")]]['issue_total_amt'];
		
											$store_wise_balance = number_format(($total_receive - $total_issue),2,".","");
											$store_wise_amt_balance = number_format(($total_receive_amt - $total_issue_amt),2,".","");
											$group_sub_total += $store_wise_balance;
											$group_sub_total_amt += $store_wise_amt_balance;
											$cat_group_sub_total+=$store_wise_balance;
											$cat_group_sub_total_amt+=$store_wise_amt_balance;
										}
										
										?>
										<td width="100" align="right"><? echo number_format($group_sub_total,2,".",""); ?></td>
										<td align="right" width="100"><? echo number_format($group_sub_total_amt,2,".",""); ?></td>
										<?
										$total_receive=$total_receive_amt=$total_issue=$total_issue_amt=0;
										foreach ($stores as $store) {
											$total_receive = $receive_array[$store[csf("company_id")]][$row[("item_category_id")]][$row[("item_group_id")]][$row[("item_description")]][$row[("unit_of_measure")]][$store[csf("id")]]['rcv_total'];
											$total_receive_amt = $receive_array[$store[csf("company_id")]][$row[("item_category_id")]][$row[("item_group_id")]][$row[("item_description")]][$row[("unit_of_measure")]][$store[csf("id")]]['rcv_total_amt'];
											$total_issue = $issue_array[$store[csf("company_id")]][$row[("item_category_id")]][$row[("item_group_id")]][$row[("item_description")]][$row[("unit_of_measure")]][$store[csf("id")]]['issue_total'];
											$total_issue_amt = $issue_array[$store[csf("company_id")]][$row[("item_category_id")]][$row[("item_group_id")]][$row[("item_description")]][$row[("unit_of_measure")]][$store[csf("id")]]['issue_total_amt'];
		
											$store_wise_balance = number_format(($total_receive - $total_issue),2,".","");
											$store_wise_amt_balance = number_format(($total_receive_amt - $total_issue_amt),2,".","");
											?>
											<td width="100" align="right" title="<? echo "Store=".$store[csf("store_name")]."\nTotal Receive=".number_format($total_receive,2,".","")."\nTotal Issue=".number_format($total_issue,2,".","");?>"><? echo $store_wise_balance;?></td>
											<?
											$store_wise_recv_total[$store[csf("id")]] += $store_wise_balance;
											$cat_store_wise_recv_total[$item_cat][$store[csf("id")]] += $store_wise_balance;
											$store_wise_balance_total += $store_wise_balance;
											$store_wise_amt_balance_total += $store_wise_amt_balance;
										}
										?>
									</tr>
									<?
									$i++;
								}
								
							}
						}
						/*
						?>
                        <tr bgcolor="#CCCCCC">
                            <td colspan="7" align="right"><strong>Category Total= </strong></td>
                            <td align="right" id="cat_grp_total_qnty"><? echo number_format($cat_group_sub_total,2,".","");?></td>
                            <td align="right" id="cat_grp_total_value"><? echo number_format($cat_group_sub_total_amt,2,".","");?></td>
                            <?
                            $r=1;
                            foreach ($stores as $store) {
                                ?>
                                <td align="right" id="store_total_<? echo $r;?>"><? echo number_format($cat_store_wise_recv_total[$item_cat][$store[csf("id")]],2,".","");?></td>
                                <?
                                $r++;
                            }
                            ?>
                        </tr>
                        <?
						*/
					}
					?>
				</tbody>
			</table>
		</div>
		<input type="hidden" id="num_of_store" value="<? echo $num_of_store;?>">
		<table class="rpt_table" border="1" width="<? echo $width; ?>" rules="all" id="table_footer">
			<tr bgcolor="#CCCCCC">
				<td width="675" align="right"><strong>Grand Total= </strong></td>
                <td align="right" width="100" id="value_grp_total_qnty"><? echo number_format($store_wise_balance_total,2,".","");?></td>
				<td align="right" width="100" id="value_grp_total_value"><? echo number_format($store_wise_amt_balance_total,2,".","");?></td>
				<?
				$t=1;
				foreach ($stores as $store) {
					?>
					<td width="100" align="right" id="store_total_<? echo $t;?>"><? echo number_format($store_wise_recv_total[$store[csf("id")]],2,".","");?></td>
					<?
					$t++;
				}
				?>
			</tr>
		</table>
	</fieldset>
	<?
	
	exit();
}

if($action == "description_popup")
{
	echo load_html_head_contents("Composition Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array(); var selected_name = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'table_body' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function set_all()
		{
			var old=document.getElementById('selected_ids').value;
			if(old!="")
			{
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{
					js_set_value( old[k] )
				}
			}
		}

		function js_set_value( str )
		{

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}

			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#selected_id').val(id);
			$('#selected_name').val(name);
		}
	</script>
</head>
<fieldset style="width:490px">
	<legend>Item Details</legend>
	<input type="hidden" name="selected_name" id="selected_name" value="">
	<input type="hidden" name="selected_id" id="selected_id" value="">
	<table width="470" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
		<thead>
			<tr>
				<th width="30">SL</th>
				<th width="40">Product Id</th>
                <th width="140">Item Category</th>
                <th width="">Item Description</th>
			</tr>
		</thead>
	</table>
	<div style="width:490px; overflow-y:scroll; max-height:300px" id="scroll_body">
		<table width="470" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<?
			$i = 1;
			$cbo_item_category=str_replace("'","",$cbo_item_category);
			$cbo_item_group=str_replace("'","",$cbo_item_group);
			$txt_description_id=str_replace("'","",$txt_description_id);
			$sql_cond="";
			if($cbo_item_category!="") $sql_cond.=" and item_category_id in($cbo_item_category)";
			else $sql_cond.=" and item_category_id in(".implode(",",array_keys($general_dyes_item_category)).") and item_category_id<>4";
			if($cbo_item_group!="") $sql_cond.=" and item_group_id in($cbo_item_group)";
			if($cbo_company_name !="") $sql_cond.=" and company_id in($cbo_company_name) ";
			
			$sql="select id, item_category_id, product_name_details, item_description from product_details_master where status_active=1 and is_deleted=0 $sql_cond";
			//echo $sql;
			$result=sql_select($sql);
			$description_id_arr=explode(",",$txt_description_id);
			foreach ($result as $row)
			{
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				if(in_array($row[csf("id")],$description_id_arr))
				{
					if($description_ids=="") $description_ids=$i; else $description_ids.=",".$i;
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
					<td width="30" align="center">
						<? echo $i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
						<input type="hidden" name="txt_individual" id="txt_individual<? echo $i ?>" value="<? echo $row[csf("item_description")]; ?>"/>
					</td>
					<td width="40" align="center"><p><? echo $row[csf("id")]; ?></p></td>
                    <td width="140"><p><? echo $general_dyes_item_category[$row[csf("item_category_id")]]; ?></p></td>
                    <td width=""><p><? echo $row[csf("item_description")]; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
			<input type="hidden" name="selected_ids" id="selected_ids" value="<? echo $description_ids; ?>"/>
		</table>
	</div>
	<table width="490" cellspacing="0" cellpadding="0" style="border:none" align="center">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%">
					<div style="width:50%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
					</div>
					<div style="width:50%; float:left" align="left">
						<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
					</div>
				</div>
			</td>
		</tr>
	</table>
</fieldset>
<script type="text/javascript">
	setFilterGrid('table_body',-1);
	set_all();
</script>
<?
}

if($action == "company_popup")
{
	echo load_html_head_contents("Composition Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array(); var selected_name = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'table_body' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function set_all()
		{
			var old=document.getElementById('selected_ids').value;
			if(old!="")
			{
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{
					js_set_value( old[k] )
				}
			}
		}

		function js_set_value( str )
		{

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}

			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#selected_id').val(id);
			$('#selected_name').val(name);
		}
	</script>
</head>
<fieldset style="width:390px">
	<legend>Item Details</legend>
	<input type="hidden" name="selected_name" id="selected_name" value="">
	<input type="hidden" name="selected_id" id="selected_id" value="">
	<table width="390" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
		<thead>
			<tr>
				<th width="50">SL</th>
                <th width="">Company Name</th>
			</tr>
		</thead>
	</table>
	<div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
		<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<?
			$i = 1;
			$cbo_company_name=str_replace("'","",$cbo_company_name);
			
			$sql="select id, company_name from lib_company where status_active=1 and is_deleted=0";
			//echo $sql;
			$result=sql_select($sql);
			$selected_id_arr=explode(",",$cbo_company_name);
			foreach ($result as $row)
			{
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				if(in_array($row[csf("id")],$selected_id_arr))
				{
					if($selected_ids=="") $selected_ids=$i; else $selected_ids.=",".$i;
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
					<td width="50" align="center">
						<? echo $i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
						<input type="hidden" name="txt_individual" id="txt_individual<? echo $i ?>" value="<? echo $row[csf("company_name")]; ?>"/>
					</td>
                    <td width=""><p><? echo $row[csf("company_name")]; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
			<input type="hidden" name="selected_ids" id="selected_ids" value="<? echo $selected_ids; ?>"/>
		</table>
	</div>
	<table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%">
					<div style="width:50%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
					</div>
					<div style="width:50%; float:left" align="left">
						<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
					</div>
				</div>
			</td>
		</tr>
	</table>
</fieldset>
<script type="text/javascript">
	setFilterGrid('table_body',-1);
	set_all();
</script>
<?
}

if($action == "category_popup")
{
	echo load_html_head_contents("Composition Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array(); var selected_name = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'table_body' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function set_all()
		{
			var old=document.getElementById('selected_ids').value;
			if(old!="")
			{
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{
					js_set_value( old[k] )
				}
			}
		}

		function js_set_value( str )
		{

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}

			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#selected_id').val(id);
			$('#selected_name').val(name);
		}
	</script>
</head>
<fieldset style="width:390px">
	<legend>Item Details</legend>
	<input type="hidden" name="selected_name" id="selected_name" value="">
	<input type="hidden" name="selected_id" id="selected_id" value="">
	<table width="390" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
		<thead>
			<tr>
				<th width="50">SL</th>
                <th width="">Company Name</th>
			</tr>
		</thead>
	</table>
	<div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
		<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<?
			$i = 1;
			$cbo_item_category=str_replace("'","",$cbo_item_category);
			
			$selected_id_arr=explode(",",$cbo_item_category);
			foreach ($general_dyes_item_category as $key=>$val)
			{
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				if(in_array($key,$selected_id_arr))
				{
					if($selected_ids=="") $selected_ids=$i; else $selected_ids.=",".$i;
				}
				
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                    <td width="50" align="center">
                        <? echo $i; ?>
                        <input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $key; ?>"/>
                        <input type="hidden" name="txt_individual" id="txt_individual<? echo $i ?>" value="<? echo $val; ?>"/>
                    </td>
                    <td width=""><p><? echo $val; ?></p></td>
                </tr>
                <?
                $i++;
			}
			?>
			<input type="hidden" name="selected_ids" id="selected_ids" value="<? echo $selected_ids; ?>"/>
		</table>
	</div>
	<table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%">
					<div style="width:50%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
					</div>
					<div style="width:50%; float:left" align="left">
						<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
					</div>
				</div>
			</td>
		</tr>
	</table>
</fieldset>
<script type="text/javascript">
	setFilterGrid('table_body',-1);
	set_all();
</script>
<?
}

if($action == "item_group_popup")
{
	echo load_html_head_contents("Composition Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array(); var selected_name = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'table_body' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function set_all()
		{
			var old=document.getElementById('selected_ids').value;
			if(old!="")
			{
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{
					js_set_value( old[k] )
				}
			}
		}

		function js_set_value( str )
		{

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}

			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#selected_id').val(id);
			$('#selected_name').val(name);
		}
	</script>
</head>
<fieldset style="width:390px">
	<legend>Item Details</legend>
	<input type="hidden" name="selected_name" id="selected_name" value="">
	<input type="hidden" name="selected_id" id="selected_id" value="">
	<table width="390" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
		<thead>
			<tr>
				<th width="30">SL</th>
                <th width="150">Item Category</th>
                <th width="">Item Group Name</th>
			</tr>
		</thead>
	</table>
	<div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
		<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<?
			$i = 1;
			$cbo_item_group=str_replace("'","",$cbo_item_group);
			$cbo_item_category=str_replace("'","",$cbo_item_category);
			$sql_cond="";
			if($cbo_item_category!="") $sql_cond=" and item_category in($cbo_item_category)";
			else $sql_cond.=" and item_category in(".implode(",",array_keys($general_dyes_item_category)).") and item_category<>4";
			$sql="select id, item_category, item_name from lib_item_group where status_active=1 and is_deleted=0 $sql_cond";
			//echo $sql;
			$result=sql_select($sql);
			$selected_id_arr=explode(",",$cbo_item_group);
			foreach ($result as $row)
			{
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				if(in_array($row[csf("id")],$selected_id_arr))
				{
					if($selected_ids=="") $selected_ids=$i; else $selected_ids.=",".$i;
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
					<td width="30" align="center">
						<? echo $i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
						<input type="hidden" name="txt_individual" id="txt_individual<? echo $i ?>" value="<? echo $row[csf("item_name")]; ?>"/>
					</td>
                    <td width="150"><p><? echo $general_dyes_item_category[$row[csf("item_category")]]; ?></p></td>
                    <td width=""><p><? echo $row[csf("item_name")]; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
			<input type="hidden" name="selected_ids" id="selected_ids" value="<? echo $selected_ids; ?>"/>
		</table>
	</div>
	<table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%">
					<div style="width:50%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
					</div>
					<div style="width:50%; float:left" align="left">
						<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
					</div>
				</div>
			</td>
		</tr>
	</table>
</fieldset>
<script type="text/javascript">
	setFilterGrid('table_body',-1);
	set_all();
</script>
<?
}

if($action == "store_popup")
{
	echo load_html_head_contents("Composition Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array(); var selected_name = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'table_body' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function set_all()
		{
			var old=document.getElementById('selected_ids').value;
			if(old!="")
			{
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{
					js_set_value( old[k] )
				}
			}
		}

		function js_set_value( str )
		{

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}

			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#selected_id').val(id);
			$('#selected_name').val(name);
		}
	</script>
</head>
<fieldset style="width:390px">
	<legend>Item Details</legend>
	<input type="hidden" name="selected_name" id="selected_name" value="">
	<input type="hidden" name="selected_id" id="selected_id" value="">
	<table width="390" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
		<thead>
			<tr>
				<th width="30">SL</th>
                <th width="">Store Name</th>
			</tr>
		</thead>
	</table>
	<div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
		<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<?
			$i = 1;
			$cbo_item_group=str_replace("'","",$cbo_item_group);
			$cbo_item_category=str_replace("'","",$cbo_item_category);
			$sql_cond="";
			//echo $cbo_item_category.test;die;
			if($cbo_item_category !="") $sql_cond.=" and category_type in($cbo_item_category)";
			else $sql_cond.=" and category_type in(".implode(",",array_keys($general_dyes_item_category)).") and category_type<>4";
			if($cbo_company_name!="") $sql_cond.=" and a.company_id in($cbo_company_name)";
			$sql="select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0  $sql_cond group by a.id, a.store_name";
			//echo $sql;
			$result=sql_select($sql);
			$selected_id_arr=explode(",",$cbo_store);
			foreach ($result as $row)
			{
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				if(in_array($row[csf("id")],$selected_id_arr))
				{
					if($selected_ids=="") $selected_ids=$i; else $selected_ids.=",".$i;
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
					<td width="30" align="center">
						<? echo $i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
						<input type="hidden" name="txt_individual" id="txt_individual<? echo $i ?>" value="<? echo $row[csf("store_name")]; ?>"/>
					</td>
                    <td width=""><p><? echo $row[csf("store_name")]; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
			<input type="hidden" name="selected_ids" id="selected_ids" value="<? echo $selected_ids; ?>"/>
		</table>
	</div>
	<table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%">
					<div style="width:50%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
					</div>
					<div style="width:50%; float:left" align="left">
						<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
					</div>
				</div>
			</td>
		</tr>
	</table>
</fieldset>
<script type="text/javascript">
	setFilterGrid('table_body',-1);
	set_all();
</script>
<?
}
?>
