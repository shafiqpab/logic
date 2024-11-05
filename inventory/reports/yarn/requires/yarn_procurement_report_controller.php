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

if ($action == "generate_report")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_yarn_count=str_replace("'","",$cbo_yarn_count);
	$cbo_composition=str_replace("'","",$cbo_composition);
	$cbo_yarn_type=str_replace("'","",$cbo_yarn_type);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$search_day_diff=datediff('d',$txt_date_from,$txt_date_to);
	//echo $search_day_diff;die;
	$search_month=array();
	$p=0;
	for($i=0; $i<$search_day_diff;$i++)
	{
		if($i==0) $search_month_year=date('m',strtotime($txt_date_from))."-".date('y',strtotime($txt_date_from));
		else $search_month_year=date('m',strtotime(add_date($txt_date_from, $i)))."-".date('y',strtotime(add_date($txt_date_from, $i)));
		$search_month[$search_month_year]=$search_month_year;
		$p++;
	}
	//echo $p;
	//echo "<pre>";print_r($search_month);die;
	
	//echo $cbo_yarn_count."=".$cbo_composition."=".$cbo_yarn_type."=test";die;
	
	$companyArr 	= return_library_array("select id,company_name from lib_company where status_active=1", "id", "company_name");
	//$supplierArr 	= return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	//$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	
	$sql_cond="";
	if($cbo_yarn_count !="") $sql_cond .=" and c.yarn_count_id in($cbo_yarn_count)";
	if($cbo_composition !="") $sql_cond .=" and c.composition_id in($cbo_composition)";
	if($cbo_yarn_type !="") $sql_cond .=" and c.yarn_type in($cbo_yarn_type)";
	//and a.task_finish_date <='$txt_date_to'
	$sql_sales="select a.id, a.task_start_date, a.task_finish_date, b.id as po_id, c.yarn_count_id, c.composition_id, c.yarn_type, c.cons_qty
	from tna_process_mst a, fabric_sales_order_mst b, fabric_sales_order_yarn_dtls c 
	where a.po_number_id=b.id and b.id=c.mst_id and a.task_type=2 and a.task_number=212 and b.company_id=$cbo_company_name and (a.task_start_date between '$txt_date_from' and '$txt_date_to' or a.task_finish_date between '$txt_date_from' and '$txt_date_to') $sql_cond";
	//echo $sql_sales;//die;
	$sql_sales_result=sql_select($sql_sales);
	$all_datas=array();$all_count=array();$all_composition=array();$all_type=array();
	foreach($sql_sales_result as $row)
	{
		$all_count[$row[csf("yarn_count_id")]]=$row[csf("yarn_count_id")];
		$all_composition[$row[csf("composition_id")]]=$row[csf("composition_id")];
		$all_type[$row[csf("yarn_type")]]=$row[csf("yarn_type")];
		$prod_key=$row[csf("yarn_count_id")]."*".$row[csf("composition_id")]."*".$row[csf("yarn_type")];
		$prod_data=$yarn_count_arr[$row[csf("yarn_count_id")]]." ".$composition[$row[csf("composition_id")]]." ".$yarn_type[$row[csf("yarn_type")]];
		$item_datas[$prod_key]=$prod_data;
		$day_diff=datediff('d',$row[csf("task_start_date")],$row[csf("task_finish_date")]);
		$per_day_cons_qty=$row[csf("cons_qty")]/$day_diff;
		for($i=1; $i<=$day_diff;$i++)
		{
			if($i==1)
			{
				$test_data[$row[csf("po_id")]][$prod_key][change_date_format($row[csf("task_start_date")], '','',1)]=$per_day_cons_qty;
				$month_year=date('m',strtotime($row[csf("task_start_date")]))."-".date('y',strtotime($row[csf("task_start_date")]));
				$months_year=date('M',strtotime($row[csf("task_start_date")]))."-".date('y',strtotime($row[csf("task_start_date")]));
				$all_datas[$month_year][$prod_key]["req_qnty"]+=$per_day_cons_qty;
				$month_arr[$month_year]=$months_year;
			}
			else
			{
				$test_data[$row[csf("po_id")]][$prod_key][change_date_format(add_date($row[csf("task_start_date")], $i), '','',1)]=$per_day_cons_qty;
				$month_year=date('m',strtotime(add_date($row[csf("task_start_date")], $i)))."-".date('y',strtotime(add_date($row[csf("task_start_date")], $i)));
				$months_year=date('M',strtotime(add_date($row[csf("task_start_date")], $i)))."-".date('y',strtotime(add_date($row[csf("task_start_date")], $i)));
				$all_datas[$month_year][$prod_key]["req_qnty"]+=$per_day_cons_qty;
				$month_arr[$month_year]=$months_year;
			}
				
		}
		//$test_data.=$day_diff."=";
	}
	//echo "<pre>";print_r($test_data);echo "<pre>";print_r($all_datas);die;
	
	/*foreach($sql_sales_result as $row)
	{
		$current_req_qnty=$next_req_qnty=0;
		$date_div=(strtotime($row[csf("task_finish_date")])-strtotime($row[csf("task_start_date")]))/86400;
		$prod_key=$row[csf("yarn_count_id")]."*".$row[csf("composition_id")]."*".$row[csf("yarn_type")];
		$prod_data=$yarn_count_arr[$row[csf("yarn_count_id")]]." ".$composition[$row[csf("composition_id")]]." ".$yarn_type[$row[csf("yarn_type")]];
		$all_count[$row[csf("yarn_count_id")]]=$row[csf("yarn_count_id")];
		$all_composition[$row[csf("composition_id")]]=$row[csf("composition_id")];
		$all_type[$row[csf("yarn_type")]]=$row[csf("yarn_type")];
		
		if(date('m',strtotime($row[csf("task_start_date")])) == date('m',strtotime($row[csf("task_finish_date")])))
		{
			//echo date('m',strtotime($row[csf("task_start_date")])) ."=".date('m',strtotime($row[csf("task_finish_date")])) ."yes = $date_div <br>";
			//$month_arr[date('m',strtotime($row[csf("task_start_date")]))."-".date('y',strtotime($row[csf("task_start_date")]))]=date('M',strtotime($row[csf("task_start_date")]))."-".date('y',strtotime($row[csf("task_start_date")]));
			$current_month_day=$next_month_day="";
		}
		else
		{
			$current_month_day=(strtotime(date("Y-m-t", strtotime($row[csf("task_start_date")])))-strtotime($row[csf("task_start_date")]))/86400;
			$next_month_day=(strtotime($row[csf("task_finish_date")])-strtotime(date("Y-m-t", strtotime($row[csf("task_start_date")]))))/86400;
			//echo $current_month_day."=". $next_month_day."=".date('m',strtotime($row[csf("task_start_date")])) ."=".date('m',strtotime($row[csf("task_finish_date")])) ."no = $date_div <br>";
			//$month_arr[date('m',strtotime($row[csf("task_start_date")]))."-".date('y',strtotime($row[csf("task_start_date")]))]=date('M',strtotime($row[csf("task_start_date")]))."-".date('y',strtotime($row[csf("task_start_date")]));
			//$month_arr[date('m',strtotime($row[csf("task_finish_date")]))."-".date('y',strtotime($row[csf("task_finish_date")]))]=date('M',strtotime($row[csf("task_finish_date")]))."-".date('y',strtotime($row[csf("task_finish_date")]));
		}
		
		if(date('m',strtotime($txt_date_from)) == date('m',strtotime($txt_date_to)))
		{
			$month_arr[date('m',strtotime($txt_date_from))."-".date('y',strtotime($txt_date_from))]=date('M',strtotime($txt_date_from))."-".date('y',strtotime($txt_date_from));
		}
		else
		{
			$month_arr[date('m',strtotime($txt_date_from))."-".date('y',strtotime($txt_date_from))]=date('M',strtotime($txt_date_from))."-".date('y',strtotime($txt_date_from));
			$month_arr[date('m',strtotime($txt_date_to))."-".date('y',strtotime($txt_date_to))]=date('M',strtotime($txt_date_to))."-".date('y',strtotime($txt_date_to));
		}
		
		if($next_month_day=="")
		{
			$month_year=date('m',strtotime($row[csf("task_start_date")]))."-".date('y',strtotime($row[csf("task_start_date")]));
			if($po_id_check[$prod_key][$row[csf("po_id")]]=="")
			{
				$po_id_check[$prod_key][$row[csf("po_id")]]=$row[csf("po_id")];
				$all_datas[$month_year][$prod_key]["po_id"].=$row[csf("po_id")].",";
			}
			$all_datas[$month_year][$prod_key]["req_qnty"]+=$row[csf("cons_qty")];
		}
		else
		{
			$month_year=date('m',strtotime($row[csf("task_start_date")]))."-".date('y',strtotime($row[csf("task_start_date")]));
			$month_year_next=date('m',strtotime($row[csf("task_finish_date")]))."-".date('y',strtotime($row[csf("task_finish_date")]));
			if($po_id_check[$prod_key][$row[csf("po_id")]]=="")
			{
				$po_id_check[$prod_key][$row[csf("po_id")]]=$row[csf("po_id")];
				$all_datas[$month_year][$prod_key]["po_id"].=$row[csf("po_id")].",";
				$all_datas[$month_year_next][$prod_key]["po_id"].=$row[csf("po_id")].",";
			}
			$current_req_qnty=($row[csf("cons_qty")]/$date_div)*$current_month_day;
			$next_req_qnty=($row[csf("cons_qty")]/$date_div)*$next_month_day;
			$all_datas[$month_year][$prod_key]["req_qnty"]+=$current_req_qnty;
			$all_datas[$month_year_next][$prod_key]["req_qnty"]+=$next_req_qnty;
			$item_datas[$prod_key]=$prod_data;
		}
	}*/
	
	ksort($month_arr);
	unset($sql_sales_result);
	//echo "<pre>";print_r($month_arr);die;
	//echo $sql_sales;die; $all_count=array();$all_composition=array();$all_type=array();
	$pi_sql="select a.id as pi_id, b.work_order_id, b.count_name as yarn_count, b.yarn_composition_item1 as yarn_comp_type1st, b.yarn_type, sum(b.quantity) as wo_pi_qnty, 1 as type
	from com_pi_master_details a, com_pi_item_details b 
	where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.goods_rcv_status<>1 and a.goods_rcv_status<>1 and a.importer_id=$cbo_company_name and b.count_name in(".implode(",", $all_count).") and b.yarn_composition_item1 in(".implode(",", $all_composition).") and b.yarn_type in(".implode(",", $all_type).") 
	group by a.id, b.work_order_id, b.count_name, b.yarn_composition_item1, b.yarn_type";
	//echo $pi_sql;die;
	$pi_result=sql_select($pi_sql);
	$pi_data=array();
	foreach($pi_result as $row)
	{
		$key=$row[csf("yarn_count")]."*".$row[csf("yarn_comp_type1st")]."*".$row[csf("yarn_type")];
		$pi_data[$row[csf("pi_id")]][$key]=$row[csf("work_order_id")];
	}
	unset($pi_result);
	$opening_stock_sql="select b.id as prod_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_type, a.receive_basis, a.pi_wo_batch_no, a.id as trans_id, a.transaction_type, a.cons_quantity, a.order_qnty 
	from inv_transaction a, product_details_master b 
	where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=1 and b.item_category_id=1 and a.company_id=$cbo_company_name and b.company_id=$cbo_company_name and b.yarn_count_id in(".implode(",", $all_count).") and b.yarn_comp_type1st in(".implode(",", $all_composition).") and b.yarn_type in(".implode(",", $all_type).") order by a.id";
	//echo $opening_stock_sql;die;
	$opening_stock_result=sql_select($opening_stock_sql);
	$stock_data=array();$wo_pi_rcv=array();
	foreach($opening_stock_result as $row)
	{
		$prod_key=$row[csf("yarn_count_id")]."*".$row[csf("yarn_comp_type1st")]."*".$row[csf("yarn_type")];
		if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5)
		{
			$stock_data[$prod_key]+=$row[csf("cons_quantity")];
		}
		else
		{
			$stock_data[$prod_key]-=$row[csf("cons_quantity")];
		}
		
		if($row[csf("receive_basis")]==2 && $row[csf("transaction_type")]==1)
		{
			$wo_pi_rcv[$row[csf("pi_wo_batch_no")]][$prod_key]+=$row[csf("order_qnty")];
		}
		else if($row[csf("receive_basis")]==1 && $row[csf("transaction_type")]==1)
		{
			$wo_pi_rcv[$pi_data[$row[csf("pi_wo_batch_no")]][$key]][$prod_key]+=$row[csf("order_qnty")];
		}
	}
	//echo "<pre>";print_r($wo_pi_rcv);die;
	unset($opening_stock_result);
	
	$wo_sql="select a.id as wo_pi_id, b.yarn_count, b.yarn_comp_type1st, b.yarn_type, b.yarn_inhouse_date, b.delivery_end_date, sum(b.supplier_order_quantity) as wo_qnty, 2 as type
	from wo_non_order_info_mst a, wo_non_order_info_dtls b 
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$cbo_company_name and b.yarn_count in(".implode(",", $all_count).") and b.yarn_comp_type1st in(".implode(",", $all_composition).") and b.yarn_type in(".implode(",", $all_type).") and b.yarn_inhouse_date between '$txt_date_from' and '$txt_date_to' and b.delivery_end_date <='$txt_date_to'
	group by a.id, b.yarn_count, b.yarn_comp_type1st, b.yarn_type, b.yarn_inhouse_date, b.delivery_end_date";
	//echo $wo_sql;die;
	$wo_sql_result=sql_select($wo_sql);
	$pipe_line_data=array();
	foreach($wo_sql_result as $row)
	{
		$prod_key=$row[csf("yarn_count")]."*".$row[csf("yarn_comp_type1st")]."*".$row[csf("yarn_type")];
		$day_diff=datediff('d',$row[csf("yarn_inhouse_date")],$row[csf("delivery_end_date")]);
		$rest_wo_qtny=$row[csf("wo_qnty")]-$wo_pi_rcv[$row[csf("wo_pi_id")]][$prod_key];
		$per_day_wo_qty=$rest_wo_qtny/$day_diff;
		for($i=1; $i<=$day_diff;$i++)
		{
			if($i==1)
			{
				$month_year=date('m',strtotime($row[csf("yarn_inhouse_date")]))."-".date('y',strtotime($row[csf("yarn_inhouse_date")]));
				$wo_datas[$month_year][$prod_key]["wo_qnty"]+=$per_day_wo_qty;
			}
			else
			{
				$month_year=date('m',strtotime(add_date($row[csf("yarn_inhouse_date")], $i)))."-".date('y',strtotime(add_date($row[csf("yarn_inhouse_date")], $i)));
				$wo_datas[$month_year][$prod_key]["wo_qnty"]+=$per_day_wo_qty;
			}
				
		}
		
		/*$current_wo_qnty=$next_wo_qnty=0;
		$date_div=(strtotime($row[csf("delivery_end_date")])-strtotime($row[csf("yarn_inhouse_date")]))/86400;
		
		
		if(date('m',strtotime($row[csf("yarn_inhouse_date")])) == date('m',strtotime($row[csf("delivery_end_date")])))
		{
			$current_month_day=$next_month_day="";
		}
		else
		{
			$current_month_day=(strtotime(date("Y-m-t", strtotime($row[csf("yarn_inhouse_date")])))-strtotime($row[csf("yarn_inhouse_date")]))/86400;
			$next_month_day=(strtotime($row[csf("delivery_end_date")])-strtotime(date("Y-m-t", strtotime($row[csf("yarn_inhouse_date")]))))/86400;
		}
		$rest_wo_qtny=0;
		if($next_month_day=="")
		{
			$month_year=date('m',strtotime($row[csf("yarn_inhouse_date")]))."-".date('y',strtotime($row[csf("yarn_inhouse_date")]));
			$wo_datas[$month_year][$prod_key]["wo_qnty"]+=$row[csf("wo_qnty")]-$wo_pi_rcv[$row[csf("wo_pi_id")]][$prod_key];
		}
		else
		{
			
			$month_year=date('m',strtotime($row[csf("yarn_inhouse_date")]))."-".date('y',strtotime($row[csf("yarn_inhouse_date")]));
			$month_year_next=date('m',strtotime($row[csf("delivery_end_date")]))."-".date('y',strtotime($row[csf("delivery_end_date")]));
			$rest_wo_qtny=$row[csf("wo_qnty")]-$wo_pi_rcv[$row[csf("wo_pi_id")]][$prod_key];
			if($rest_wo_qtny>0)
			{
				$current_wo_qnty=($rest_wo_qtny/$date_div)*$current_month_day;
				$next_wo_qnty=($rest_wo_qtny/$date_div)*$next_month_day;
			}
			$wo_datas[$month_year][$prod_key]["wo_qnty"]+=$current_wo_qnty;
			$wo_datas[$month_year_next][$prod_key]["wo_qnty"]+=$next_wo_qnty;
		}*/
	}
	//echo "<pre>";print_r($wo_datas);die;
	unset($wo_sql_result);
	$sql_issue="select b.id as prod_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_type, a.receive_basis, a.pi_wo_batch_no, a.id as trans_id, a.transaction_type, a.cons_quantity, a.order_qnty 
	from inv_issue_master m, inv_transaction a, product_details_master b 
	where m.id=a.mst_id and a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=1 and b.item_category_id=1 and a.company_id=$cbo_company_name and b.company_id=$cbo_company_name and b.yarn_count_id in(".implode(",", $all_count).") and b.yarn_comp_type1st in(".implode(",", $all_composition).") and b.yarn_type in(".implode(",", $all_type).") and a.transaction_type=2 and m.issue_purpose=1 and a.transaction_date between '$txt_date_from' and '$txt_date_to' order by a.id ";
	//echo $sql_issue;die;
	$issue_result=sql_select($sql_issue);
	$issue_data=array();
	foreach($issue_result as $row)
	{
		$prod_key=$row[csf("yarn_count_id")]."*".$row[csf("yarn_comp_type1st")]."*".$row[csf("yarn_type")];
		$issue_data[$prod_key]+=$row[csf("cons_quantity")];
	}
	//echo "<pre>";print_r($month_arr);echo "<pre>";print_r($search_month);die;
	unset($issue_result);
	$selected_month=array();
	foreach ($month_arr as $month_id=>$month_data) 
	{
		if($search_month[$month_id])
		{
			$selected_month[$month_id]=$month_data;
		}
	}
	
	$width = (370+count($selected_month)*600);
	//echo $width;die;
	ob_start();
	?>
	<fieldset style="width:<? echo $width+20; ?>px;margin:5px auto;">
		<table width="<? echo $width+20; ?>" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
        	<tr class="form_caption" style="border:none;">
                <td colspan="16" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td>
            </tr>
            <tr style="border:none;">
                <td colspan="16" align="center" style="border:none; font-size:14px;">
                    <? echo ($cbo_company_name==0)?"All Company":"Company Name : ".$companyArr[str_replace("'", "", $cbo_company_name)]; ?>
                </td>
            </tr>
        </table>
        <table width="<? echo $width+20; ?>" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
			<thead>
				
				<tr>
					<th rowspan="2" width="50">SL</th>
					<th rowspan="2" width="200">Yarn Description</th>
					<?
					foreach ($month_arr as $month_id=>$month_data) 
					{
						if($search_month[$month_id])
						{
							?>
							<th colspan="6" width="600" title="<? echo $month_id."==".$month_data."==".$search_month[$month_id]; ?>"><? echo $month_data;?></th>
							<?
						}
					}
					?>
					<th rowspan="2">Total</th>
				</tr>
				<tr>
                	<?
					$p=1;
					foreach ($month_arr as $month_id=>$month_data) 
					{
						if($search_month[$month_id])
						{
							?>
							<th width="100">Required</th>
							<th width="100"><? if($p==1) echo "Opening Stock"; else echo "Tentative Stock";?></th>
							<th width="100">Pipe Line</th>
							<th width="100">Stock + Pipe Line</th>
							<th width="100">Issue</th>
							<th width="100">Short / Excess</th>
							<?
							$p++;
						}
					}
					?>
				</tr>
			</thead>
		</table>
		<div style="max-height:425px; overflow-y:auto; width:<? echo $width+20; ?>px;" >
			<table class="rpt_table" border="1" width="<? echo $width; ?>" rules="all" id="table_body">
				<tbody>
					<?
					$i=1;
					foreach ($item_datas as $prod_key=>$item_das) 
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						//$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
						//if ($row[csf("yarn_comp_type2nd")] != 0)
							//$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td align="center" width="50"><? echo $i;?></td>
							<td width="200"><p><? echo $item_das; ?></p></td>
                            <?
							$q=1;
							$tot_short_excess=0;
							foreach ($month_arr as $month_id=>$month_data) 
							{
								if($search_month[$month_id])
								{
									if($q==1)
									{
										$stock_qnty=$stock_data[$prod_key];
									}
									else
									{
										$stock_qnty=$short_excess;
									}
									$stock_pipeLine=$stock_qnty+$wo_datas[$month_id][$prod_key]["wo_qnty"];
									$short_excess=(($stock_pipeLine+$issue_data[$prod_key])-$all_datas[$month_id][$prod_key]["req_qnty"]);
									?>
									<td align="right" width="100" title="<? echo $month_id."=".$prod_key; ?>"><? echo number_format($all_datas[$month_id][$prod_key]["req_qnty"],2) ?></td>
									<td align="right" width="100" title="<? echo "stock qnty=".$stock_qnty.", wo qnty=".$wo_datas[$month_id][$prod_key]["wo_qnty"].", issue qnty=".$issue_data[$prod_key].", req qnty=".$all_datas[$month_id][$prod_key]["req_qnty"]; ?>"><? echo number_format($stock_qnty,2);?></td>
									<td align="right" width="100"><? echo number_format($wo_datas[$month_id][$prod_key]["wo_qnty"],2);?></td>
									<td align="right" width="100"><? echo number_format($stock_pipeLine,2);?></td>
									<td align="right" width="100"><? echo number_format($issue_data[$prod_key],2);?></td>
									<td align="right" width="100" title="((stock qnty+ wo qnty+issue qnty)-req qnty)"><? echo number_format($short_excess,2);?></td>
									<?
									$q++;
									$tot_short_excess+=$short_excess;
									unset($issue_data[$prod_key]);
								}
							}
							?>
							<td align="right"><p><? echo number_format($tot_short_excess,2); ?></p></td>
						</tr>
						<?
						$i++;
					}
					?>
				</tbody>
			</table>			
		</div>
	</fieldset>
	<?
	exit();
}

if($action == "composition_popup")
{
	echo load_html_head_contents("Composition Info","../../../../", 1, 1, '','1','');
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
			var old=document.getElementById('txt_pre_composition_row_id').value;
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

			$('#hidden_composition_id').val(id);
			$('#hidden_composition').val(name);
		}
	</script>
</head>
<fieldset style="width:390px">
	<legend>Yarn Receive Details</legend>
	<input type="hidden" name="hidden_composition" id="hidden_composition" value="">
	<input type="hidden" name="hidden_composition_id" id="hidden_composition_id" value="">
	<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
		<thead>
			<tr>
				<th colspan="2">
					<? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?>
				</th>
			</tr>
			<tr>
				<th width="50">SL</th>
				<th width="">Composition Name</th>
			</tr>
		</thead>
	</table>
	<div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
		<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<?
			$i = 1;

			$result=sql_select("select id,composition_name from  lib_composition_array where status_active=1 and is_deleted=0 order by composition_name");
			$pre_composition_id_arr=explode(",",$pre_composition_id);
			foreach ($result as $row)
			{
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				if(in_array($row[csf("id")],$pre_composition_id_arr))
				{
					if($pre_composition_ids=="") $pre_composition_ids=$i; else $pre_composition_ids.=",".$i;
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
					<td width="50">
						<? echo $i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
						<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf("composition_name")]; ?>"/>
					</td>
					<td width=""><p><? echo $row[csf("composition_name")]; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
			<input type="hidden" name="txt_pre_composition_row_id" id="txt_pre_composition_row_id" value="<?php echo $pre_composition_ids; ?>"/>
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
