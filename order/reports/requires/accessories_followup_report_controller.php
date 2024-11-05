<?

header('Content-type:text/html; charset=utf-8');

session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//--------------------------------------------------------------------------------------------------------------------
$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$costing_per_id_library=return_library_array( "select job_no, costing_per from wo_pre_cost_mst", "job_no", "costing_per");
$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
$color_name_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );   	 
	exit();
}
$tmplte=explode("**",$data);
if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;
if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name=str_replace("'","",$cbo_company_name);
	//if(str_replace("'","",$cbo_buyer_name)==0) $buyer_name="%%"; else $buyer_name=str_replace("'","",$cbo_buyer_name);
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}

	if(str_replace("'","",$cbo_item_group)=="")
	{
		$item_group_cond="";
	}
	else
	{
		$item_group_cond="and a.trim_group in(".str_replace("'","",$cbo_item_group).")";
	}
	//echo $item_group_cond;die;
	//print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));
	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$start_date=(str_replace("'","",$txt_date_from));
		$end_date=(str_replace("'","",$txt_date_to));
		/*if($db_type==0)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
		}
		else
		{
			$start_date=date("j-M-Y",strtotime(str_replace("'","",$txt_date_from)));

			$end_date=date("j-M-Y",strtotime(str_replace("'","",$txt_date_to)));
		}*/
		$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
	}
	//condition add
	if($db_type==0)
	{
		if(str_replace("'","",$cbo_year)!=0) $year_cond=" and year(a.insert_date)=".str_replace("'","",$cbo_year).""; else $year_cond="";
	}
	else
	{
		if(str_replace("'","",$cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=".str_replace("'","",$cbo_year).""; else $year_cond="";
	}
	if (str_replace("'","",$txt_job_no)=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in (".str_replace("'","",$txt_job_no).") ";
	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond="";
	$serch_by=str_replace("'","",$cbo_search_by);
  if(str_replace("'","",$cbo_search_by)==1)
  {
	$wo_qty_array=array();
	$wo_qty_summary_array=array(); //group by b.trim_group, b.po_break_down_id,a.booking_no
	$wo_sql="select a.booking_no, b.po_break_down_id, b.trim_group, (b.wo_qnty) as wo_qnty from wo_booking_mst a, wo_booking_dtls b 
	where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name  ";
	//echo $wo_sql;die;
	$dataArray=sql_select($wo_sql);
	foreach($dataArray as $row )
	{
		$wo_qty_array[$row[csf('po_break_down_id')]][$row[csf('trim_group')]]['booking_no']=$row[csf('booking_no')];
		$wo_qty_array[$row[csf('po_break_down_id')]][$row[csf('trim_group')]]['wo_qnty']+=$row[csf('wo_qnty')];
		$wo_qty_summary_array[$row[csf('trim_group')]]['wo_qnty']+=$row[csf('wo_qnty')];
	}
	unset ($dataArray);
	
	$trimsArray=sql_select("select  b.po_break_down_id,b.id,a.trim_group, a.cons_uom, a.apvl_req, a.brand_sup_ref, a.rate, b.cons 
	from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b 
	where a.id=b.wo_pre_cost_trim_cost_dtls_id and a.status_active=1 and a.is_deleted=0 $item_group_cond");
	$reference_arr=array();
	foreach($trimsArray as $row)
	{
		$reference_arr[$row[csf('po_break_down_id')]][$row[csf('id')]]['id']=$row[csf('id')];
		$reference_arr[$row[csf('po_break_down_id')]][$row[csf('id')]]['po_break_down_id']=$row[csf('po_break_down_id')];
		$reference_arr[$row[csf('po_break_down_id')]][$row[csf('id')]]['trim_group']=$row[csf('trim_group')];
		$reference_arr[$row[csf('po_break_down_id')]][$row[csf('id')]]['cons_uom']=$row[csf('cons_uom')];
		$reference_arr[$row[csf('po_break_down_id')]][$row[csf('id')]]['apvl_req']=$row[csf('apvl_req')];
		$reference_arr[$row[csf('po_break_down_id')]][$row[csf('id')]]['brand_sup_ref']=$row[csf('brand_sup_ref')];
		$reference_arr[$row[csf('po_break_down_id')]][$row[csf('id')]]['rate']=$row[csf('rate')];
		$reference_arr[$row[csf('po_break_down_id')]][$row[csf('id')]]['cons']=$row[csf('cons')];
	}
	unset ($trimsArray);
	$app_sql=sql_select("select po_break_down_id,accessories_type_id,approval_status from wo_po_trims_approval_info");
	$app_status_arr=array();
	foreach($app_sql as $row)
	{
		$app_status_arr[$row[csf("po_break_down_id")]][$row[csf("accessories_type_id")]]=$row[csf("approval_status")];
	}
	unset ($app_sql);
	//$inhouse_qnty=return_field_value("sum(a.cons_qnty)","inv_receive_master b, inv_trims_entry_dtls a","b.id=a.mst_id and a.item_group_id='$trim_id' and a.order_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=4");
	/*if($db_type==2)
	{
		$inhouse_qnty_sql=sql_select( " select a.item_group_id,LISTAGG(CAST(a.order_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.order_id) as order_id,sum(a.cons_qnty) as  cons_qnty from inv_receive_master b, inv_trims_entry_dtls a where b.id=a.mst_id and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=4 group by  a.item_group_id");
	}

	else if($db_type==0)

	{

		$inhouse_qnty_sql=sql_select( " select a.item_group_id,group_concat(a.order_id) as order_id,sum(a.cons_qnty) as  cons_qnty from inv_receive_master b, inv_trims_entry_dtls a where b.id=a.mst_id and a.status_active=1 and b.entry_form=24 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=4 group by  a.item_group_id");

	}

	$inhouse_qnty_arr=array();

	foreach($inhouse_qnty_sql as $row)

	{

		$order_id_arr=explode(",",implode(",",array_unique(explode(",",$row[csf("order_id")]))));

		for($i=0;$i<=count($order_id_arr);$i++)

		{

			$inhouse_qnty_arr[$order_id_arr[$i]][$row[csf("item_group_id")]]=$row[csf("cons_qnty")];

		}

	}*/

	$receive_qty_array=array();

	$issue_qty_array=array();

	$receive_qty_data=sql_select("select b.po_breakdown_id, a.item_group_id,sum(b.quantity) as quantity   from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, a.item_group_id");

		foreach($receive_qty_data as $row)

		{
			$receive_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['receive_qty']=$row[csf('quantity')];
		}
unset ($receive_qty_data);
	$issue_qty_data=sql_select("SELECT b.po_breakdown_id, a.item_group_id,sum(b.quantity) as quantity  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b where a.mst_id=d.id and a.prod_id=p.id and b.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, a.item_group_id");
		foreach($issue_qty_data as $row)
		{
			$issue_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['issue_qty']=$row[csf('quantity')];
			//$issue_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['rate']=$row[csf('rate')];
			//$receive_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][value]+=$row[csf('rate')];
		}
		unset ($issue_qty_data);
	if($db_type==2)
		{
		   $issue_qnty_sql=sql_select("select item_group_id,LISTAGG(CAST(order_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY order_id) as order_id,sum(issue_qnty) as issue_qnty from inv_trims_issue_dtls where status_active=1 and is_deleted=0 group by item_group_id");
		}
  	if($db_type==0)
		{
		   $issue_qnty_sql=sql_select("select item_group_id,group_concat(order_id,',') as order_id,sum(issue_qnty) as issue_qnty from inv_trims_issue_dtls where status_active=1 and is_deleted=0 group by item_group_id");
		}
	$issue_qnty_arr=array();
	foreach($issue_qnty_sql as $row)
	{
		$order_id_arr=explode(",",implode(",",array_unique(explode(",",$row[csf("order_id")]))));
		for($i=0;$i<=count($order_id_arr);$i++)

		{
			$issue_qnty_arr[$order_id_arr[$i]][$row[csf("item_group_id")]]=$row[csf("issue_qnty")];
		}
	}
	unset ($issue_qnty_sql);
	//var_dump($wo_qty_summary_array);
	if($template==1)
	{
		ob_start();
	?>
		<div style="width:1780px">
		<fieldset style="width:100%;">	
			<table width="1780">
				<tr class="form_caption">
					<td colspan="21" align="center">Accessories Followup Report (Budget-V1)</td>
				</tr>
				<tr class="form_caption">
					<td colspan="21" align="center"><? echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
			<table class="rpt_table" width="1740" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="30">SL</th>
					<th width="50">Buyer</th>
					<th width="100">Job No</th>
					<th width="100">Style Ref</th>
					<th width="90">Order No</th>
					<th width="80">Order Qnty</th>
					<th width="50">UOM</th>
					<th width="80">Qnty (Pcs)</th>
					<th width="80">Shipment Date</th>
					<th width="100">Trims Name</th>
					<th width="100">Brand/Sup Ref</th>
					<th width="60">Appr Req.</th>
					<th width="80">Approve Status</th>
					<th width="60">Trims UOM</th>
					<th width="100">Req Qnty</th>
					<th width="100">Pre Costing Value</th>
					<th width="90">WO Qnty</th>
					<th width="90">In-House Qnty</th>
					<th width="90">Receive Balane</th>
					<th width="90">Issue to Prod.</th>
					<th>Left Over</th>
				</thead>
			</table>
			<div style="width:1760px; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="1740" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">

				<?

				$i=1; $s=1; $total_order_qnty=0; $total_req_qnty=0; $total_order_qnty_in_pcs=0; $total_wo_qnty=0; $total_in_qnty=0; $item_array=array(); $uom_array=array(); $item_app_array=array();

				$sql="select a.job_no_prefix_num, a.job_no, a.buyer_name, a.order_uom, a.style_ref_no, a.total_set_qnty as ratio, b.id, b.po_number, b.pub_shipment_date, b.po_quantity, b.unit_price, b.po_total_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $buyer_id_cond $date_cond $year_cond $job_no_cond $style_ref_cond  order by b.id, b.pub_shipment_date"; //and a.buyer_name like '$buyer_name' and b.pub_shipment_date between '$start_date' and '$end_date'

				//echo $sql;die;

				$nameArray=sql_select($sql);

				$tot_rows=count($nameArray);

				foreach($nameArray as $row )

				{

					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					

					$dzn_qnty=0;

					if($costing_per_id_library[$row[csf('job_no')]]==1)

					{

						$dzn_qnty=12;

					}

					else if($costing_per_id_library[$row[csf('job_no')]]==3)

					{

						$dzn_qnty=12*2;

					}

					else if($costing_per_id_library[$row[csf('job_no')]]==4)

					{

						$dzn_qnty=12*3;

					}

					else if($costing_per_id_library[$row[csf('job_no')]]==5)

					{

						$dzn_qnty=12*4;

					}

					else

					{

						$dzn_qnty=1;

					}

					$dzn_qnty=$row[csf('ratio')]*$dzn_qnty;

					$k=1;

					

					$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];

					$total_order_qnty+=$row[csf('po_quantity')];

					$total_order_qnty_in_pcs+=$order_qnty_in_pcs;

					?>

					<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $s; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $s; ?>">

						<td width="30"><p><? echo $i; ?>&nbsp;</p></td>

						<td width="50"><p><? echo $buyer_short_name_library[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>

						<td width="100" align="center"><p><? echo $row[csf('job_no_prefix_num')]; ?>&nbsp;</p></td>

						<td width="100"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>

						<td width="90"><p><a href='#report_details' onclick="generate_report('<? echo $company_name; ?>','<? echo $row[csf('job_no')]; ?>','preCostRpt');"><? echo $row[csf('po_number')]; ?></a>&nbsp;</p></td>

						<td width="80" align="right"><p><? echo number_format($row[csf('po_quantity')],0,'.',''); ?>&nbsp;</p></td>

						<td width="50" align="center"><p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?>&nbsp;</p></td>

						<td width="80" align="right"><p><? echo number_format($order_qnty_in_pcs,0,'.',''); ?>&nbsp;</p></td>

						<td width="80" align="center"><p><? echo change_date_format($row[csf('pub_shipment_date')]); ?>&nbsp;</p></td>

					<?

					/*$trimsArray=sql_select("select a.trim_group, a.cons_uom, a.apvl_req, a.brand_sup_ref, a.rate, b.cons from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id and b.po_break_down_id='".$row[csf('id')]."' and a.job_no='".$row[csf('job_no')]."' and a.status_active=1 and a.is_deleted=0 $item_group_cond");*/

					foreach($reference_arr[$row[csf('id')]] as $selectResult)

					{

						$req_qnty=0; $req_value=0; $wo_qty=0;

						

						$req_qnty=($row[csf('po_quantity')]/$dzn_qnty)*$selectResult[('cons')];

						$total_req_qnty+=$req_qnty;

						//echo $total_req_qnty;

						$req_value=$req_qnty*$selectResult[('rate')];

						$total_pre_costing_value+=$req_value;

						if($wo_qty_array[$row[csf('id')]][$selectResult[('trim_group')]]['wo_qnty']>$req_qnty)

						{

						$color_wo="red";	

						}

						

						else if($req_qnty>0 && $wo_qty_array[$row[csf('id')]][$selectResult[('trim_group')]]['wo_qnty']==0)

						{

						$color_wo="yellow";		

						}

						

						else 

						{

						$color_wo="";	

						}

						if($selectResult[('apvl_req')]==1)

						{

							//$app_status=return_field_value("approval_status","wo_po_trims_approval_info","job_no_mst='".$row[csf('job_no')]."' and po_break_down_id='".$row[csf('id')]."' and accessories_type_id='".$selectResult[('trim_group')]."' and status_active=1 and is_deleted=0 and current_status=1");

							$app_status=$app_status_arr[$row[csf('id')]][$selectResult[('trim_group')]];

							

							$approved_status=$approval_status[$app_status];

							

							if(array_key_exists($selectResult[('trim_group')], $item_app_array)) 

							{

								$item_app_array[$selectResult[('trim_group')]]['all']+=1;

								

								if($app_status==3)

								{

									$item_app_array[$selectResult[('trim_group')]]['app']+=1;

								}

							}

							else

							{

								$item_app_array[$selectResult[('trim_group')]]['all']=1;

								

								if($app_status==3)

								{

									$item_app_array[$selectResult[('trim_group')]]['app']=1;

								}	

							}

						}

						else

						{

							$approved_status="";

						}

						

						if(!array_key_exists($selectResult[('trim_group')], $uom_array)) 

						{

							$uom_array[$selectResult[('trim_group')]]=$unit_of_measurement[$selectResult[('cons_uom')]];

						}

						

						if($k==1)

						{

						?>

								<td width="100">

									<p>

										<? echo $item_library[$selectResult[('trim_group')]]; ?>

									&nbsp;</p>

								</td>

								<td width="100">

									<p>

										<? echo $selectResult[('brand_sup_ref')]; ?>

									&nbsp;</p>

								</td>

								<td width="60" align="center"><p><? if($selectResult[('apvl_req')]==1) echo "Yes"; else echo "&nbsp;"; ?>&nbsp;</p></td>

								<td width="80" align="center"><p><? echo $approved_status; ?>&nbsp;</p></td>

								<td width="60" align="center"><p><? echo $unit_of_measurement[$selectResult[('cons_uom')]]; ?>&nbsp;</p></td>

								<td width="100" align="right"><p><? echo number_format($req_qnty,2,'.',''); ?>&nbsp;</p></td>

								<td width="100" align="right"><p><? echo number_format($req_value,2,'.',''); ?>&nbsp;</p></td>

								<td width="90" align="right" bgcolor="<? echo $color_wo;?>"><p><a href='#report_details' onclick="openmypage('<? echo $row[csf('id')]; ?>','<? echo $selectResult[('trim_group')]; ?>','booking_info');"><? echo number_format($wo_qty_array[$row[csf('id')]][$selectResult[('trim_group')]]['wo_qnty'],2,'.',''); ?></a>&nbsp;</p></td>

                                <?

									$trim_id=$selectResult[('trim_group')];

									$order_id=$row[csf('id')];

									//$inhouse_qnty=return_field_value("sum(a.cons_qnty)","inv_receive_master b, inv_trims_entry_dtls a","b.id=a.mst_id and a.item_group_id='$trim_id' and a.order_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=4");

									$inhouse_qnty=$receive_qty_array[$order_id][$trim_id]['receive_qty'];

									//$inhouse_qnty=$inhouse_qnty_arr[$order_id][$trim_id];

									$balance=$wo_qty_array[$row[csf('id')]][$selectResult[('trim_group')]]['wo_qnty']-$inhouse_qnty;

									

									//$issue_qnty=return_field_value("sum(issue_qnty)","inv_trims_issue_dtls","item_group_id='$trim_id' and order_id='$order_id' and status_active=1 and is_deleted=0");

									//$issue_qnty=$issue_qnty_arr[$order_id][$trim_id];

									$issue_qnty=$issue_qty_array[$order_id][$trim_id]['issue_qty'];

									$left_overqty=$inhouse_qnty-$issue_qnty;

								?>

								<td width="90" align="right"><p><a href='#report_details' onclick="openmypage_inhouse('<? echo $row[csf('id')]; ?>','<? echo $selectResult[('trim_group')]; ?>','booking_inhouse_info');"><? echo number_format($inhouse_qnty,2,'.',''); ?></a>&nbsp;</p></td>

								<td width="90" align="right"><p><? echo number_format($balance,2,'.',''); ?>&nbsp;</p></td>

								<td width="90" align="right"><p><a href='#report_details' onclick="openmypage_issue('<? echo $row[csf('id')]; ?>','<? echo $selectResult[('trim_group')]; ?>','booking_issue_info');"><? echo number_format($issue_qnty,2,'.',''); ?></a>&nbsp;</p></td>

								<td align="right"><p><? echo number_format($left_overqty,2,'.',''); ?>&nbsp;</p></td>

							</tr>

							<?
							$total_in_qnty+=$inhouse_qnty;

						}

						else

						{

						?>

							<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $s; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $s; ?>">

								<td width="30">

									<font style="display:none" color="<? echo $bgcolor; ?>">

										<p><? echo $i; ?>&nbsp;</p>

									</font>

								</td>

								<td width="50">

									<font style="display:none" color="<? echo $bgcolor; ?>">

										<p><? echo $buyer_short_name_library[$row[csf('buyer_name')]]; ?>&nbsp;</p>

									</font>

								</td>

								<td width="100">

									<font style="display:none" color="<? echo $bgcolor; ?>">

										<p><? echo $row[csf('job_no')]; ?>&nbsp;</p>

									</font>

								</td>

								<td width="100">

									<font style="display:none" color="<? echo $bgcolor; ?>">

										<p>

											<? echo $row[csf('style_ref_no')]; ?>

										&nbsp;</p>

									</font>

								</td>

								<td width="90">

									<font style="display:none" color="<? echo $bgcolor; ?>">

										<p>

											<? echo $row[csf('po_number')]; ?>

										&nbsp;</p>

									</font>

								</td>

								<td width="80" align="right">

									<font style="display:none" color="<? echo $bgcolor; ?>">

										<p><? //echo number_format($row[csf('po_quantity')],0,'.',''); ?>&nbsp;</p>

									</font>

								</td>

								<td width="50" align="center">

									<font style="display:none" color="<? echo $bgcolor; ?>">

										<p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?>&nbsp;</p>

									</font>

								</td>

								<td width="80" align="right">

									<font style="display:none" color="<? echo $bgcolor; ?>">

										<p><? //echo number_format($order_qnty_in_pcs,0,'.',''); ?>&nbsp;</p>

									</font>

								</td>

								<td width="80" align="center">

									<font style="display:none" color="<? echo $bgcolor; ?>">

										<p><? echo change_date_format($row[csf('pub_shipment_date')]); ?>&nbsp;</p>

									</font>

								</td>

								<td width="100">

									<p>

										<? echo $item_library[$selectResult[('trim_group')]]; ?>

									&nbsp;</p>

								</td>

								<td width="100">

									<p>

										<? echo $selectResult[('brand_sup_ref')]; ?>

									&nbsp;</p>

								</td>

								<td width="60" align="center"><p><? if($selectResult[('apvl_req')]==1) echo "Yes"; else echo "&nbsp;"; ?>&nbsp;</p></td>

								<td width="80" align="center"><p><? echo $approved_status; ?>&nbsp;</p></td>

								<td width="60" align="center"><p><? echo $unit_of_measurement[$selectResult[('cons_uom')]]; ?>&nbsp;</p></td>

								<td width="100" align="right"><p><? echo number_format($req_qnty,2,'.',''); ?>&nbsp;</p></td>

								<td width="100" align="right"><p><? echo number_format($req_value,2,'.',''); ?>&nbsp;</p></td>

								<td width="90" align="right" bgcolor="<? echo $color_wo;?>"><p><a href='#report_details' onclick="openmypage('<? echo $row[csf('id')]; ?>','<? echo $selectResult[('trim_group')]; ?>','booking_info');"><? echo number_format($wo_qty_array[$row[csf('id')]][$selectResult[('trim_group')]]['wo_qnty'],2,'.',''); ?></a>&nbsp;</p></td>

                                <?

									$trim_id=$selectResult[('trim_group')];

									$order_id=$row[csf('id')];

									//$inhouse_qnty=return_field_value("sum(a.cons_qnty)","inv_receive_master b, inv_trims_entry_dtls a","b.id=a.mst_id and a.item_group_id='$trim_id' and a.order_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=4");

									$inhouse_qnty=$receive_qty_array[$order_id][$trim_id]['receive_qty'];

									//$inhouse_qnty=$inhouse_qnty_arr[$order_id][$trim_id];

									$balance=$wo_qty_array[$row[csf('id')]][$selectResult[('trim_group')]]['wo_qnty']-$inhouse_qnty;

									

									//$issue_qnty=return_field_value("sum(issue_qnty)","inv_trims_issue_dtls","item_group_id='$trim_id' and order_id='$order_id' and status_active=1 and is_deleted=0");

									//$issue_qnty=$issue_qnty_arr[$order_id][$trim_id];

									$issue_qnty=$issue_qty_array[$order_id][$trim_id]['issue_qty'];

									$left_overqty=$inhouse_qnty-$issue_qnty;

								?>

                                <td width="90" align="right"><p><a href='#report_details' onclick="openmypage_inhouse('<? echo $row[csf('id')]; ?>','<? echo $selectResult[('trim_group')]; ?>','booking_inhouse_info');"><? echo number_format($inhouse_qnty,2,'.',''); ?></a>&nbsp;</p></td>

								<td width="90" align="right"><p><? echo number_format($balance,2,'.',''); ?>&nbsp;</p></td>

								<td width="90" align="right"><p><a href='#report_details' onclick="openmypage_issue('<? echo $row[csf('id')]; ?>','<? echo $selectResult[('trim_group')]; ?>','booking_issue_info');"><? echo number_format($issue_qnty,2,'.',''); ?></a>&nbsp;</p></td>

								<td align="right"><p><? echo number_format($left_overqty,2,'.',''); ?>&nbsp;</p></td>

							</tr>

						<?
						$total_in_qnty+=$inhouse_qnty;

						}

					$k++;

					$s++;	

                            $total_wo_qnty+=$wo_qty_array[$row[csf('id')]][$selectResult[('trim_group')]]['wo_qnty'];

                            

                            $rec_bal=$wo_qty_array[$row[csf('id')]][$selectResult[('trim_group')]]['wo_qnty']-$inhouse_qnty;

                            $total_rec_bal_qnty+=$balance;

                            

                            $total_issue_qnty+=$issue_qnty;

                            $total_leftover_qnty+=$left_overqty;

							

						$item_array[$selectResult[('trim_group')]]['req']+=$req_qnty;

						$item_array[$selectResult[('trim_group')]]['wo']+=$wo_qty_array[$row[csf('id')]][$selectResult[('trim_group')]]['wo_qnty'];

						$item_array[$selectResult[('trim_group')]]['in']+=$inhouse_qnty;

						$item_array[$selectResult[('trim_group')]]['issue']+=$issue_qnty;

						$item_array[$selectResult[('trim_group')]]['leftover']+=$left_overqty;

					}

					if(count($reference_arr[$row[csf('id')]])<1)

					{

					?>

							<td width="100">&nbsp;</td>

							<td width="100">&nbsp;</td>

							<td width="60" align="center">&nbsp;</td>

							<td width="80">&nbsp;</td>

							<td width="60" align="center">&nbsp;</td>

							<td width="100" align="right">&nbsp;</td>

							<td width="100" align="right">&nbsp;</td>

							<td width="90" align="right">&nbsp;</td>

                            <td width="90" align="right">&nbsp;</td>

							<td width="90" align="right">&nbsp;</td>

							<td width="90" align="right">&nbsp;</td>

							<td align="right">&nbsp;</td>

						</tr>

					<?

					} //$total_in_qnty+=$inhouse_qnty;

				$i++;

				}

				?>

				</table>

				<table class="rpt_table" width="1740" cellpadding="0" cellspacing="0" border="1" rules="all">

					<tfoot>

						<th width="30"></th>

						<th width="50"></th>

						<th width="100"></th>

						<th width="100"></th>

						<th width="90"></th>

						<th width="80" align="right" id="total_order_qnty"><? echo number_format($total_order_qnty,0); ?></th>

						<th width="50"></th>

						<th width="80" align="right" id="total_order_qnty_in_pcs"><? echo number_format($total_order_qnty_in_pcs,0); ?></th>

						<th width="80"></th>

						<th width="100"></th>

						<th width="100"></th>

						<th width="60"></th>

						<th width="80"></th>

						<th width="60"></th>

						<th width="100" align="right" id="value_req_qnty"><? //echo number_format($total_req_qnty,2); ?></th>

						<th width="100" align="right" id="value_pre_costing"><? echo number_format($total_pre_costing_value,2); ?></th>

						<th width="90" align="right" id="value_wo_qty"><? echo number_format($total_wo_qnty,2); ?></th>

                        <th width="90" align="right" id="value_in_qty"><? echo number_format($total_in_qnty,2); ?></th>

						<th width="90" align="right" id="value_rec_qty"><? echo number_format($total_rec_bal_qnty,2); ?></th>

						<th width="90" align="right" id="value_issue_qty"><? echo number_format($total_issue_qnty,2); ?></th>

						<th align="right" id="value_leftover_qty"><? echo number_format($total_leftover_qnty,2); ?></th>

					</tfoot>

				</table>

				</div>

				<table>

					<tr><td height="15"></td></tr>

				</table>

				<u><b>Summary</b></u>

				<table class="rpt_table" width="1200" cellpadding="0" cellspacing="0" border="1" rules="all">

					<thead>

						<th width="30">SL</th>

						<th width="110">Item</th>

						<th width="60">UOM</th>

						<th width="80">Approved %</th>

						<th width="110">Req Qty</th>

						<th width="110">WO Qty</th>

						<th width="80">WO %</th>

						<th width="110">In-House Qty</th>

						<th width="80">In-House %</th>

						<th width="110">In-House Balance Qty</th>

						<th width="110">Issue Qty</th>

						<th width="80">Issue %</th>

						<th>Left Over</th>

					</thead>

					<?

					$z=1; $tot_req_qnty_summary=0;

					foreach($item_array as $key=>$value)

					{

						if($z%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						//print_r ($value);

						$tot_req_qnty_summary+=$value['req'];

						$tot_wo_qnty_summary+=$value['wo'];

						$tot_in_qnty_summary+=$value['in'];

						$tot_issue_qnty_summary+=$value['issue'];

						$tot_leftover_qnty_summary+=$value['leftover'];

					?>

						<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr2_<? echo $z; ?>','<? echo $bgcolor;?>')" id="tr2_<? echo $z; ?>">

							<td width="30"><? echo $z; ?></td>

							<td width="110"><p><? echo $item_library[$key]; ?></p></td>

							<td width="60" align="center"><? echo $uom_array[$key]; ?></td>

							<td width="80" align="right"><? $app_perc=($item_app_array[$key]['app']*100)/$item_app_array[$key]['all']; echo number_format($app_perc,2); ?>&nbsp;</td>

							<td width="110" align="right"><? echo number_format($value['req'],2); ?>&nbsp;</td>

							<td width="110" align="right"><? echo number_format($value['wo'],2); ?>&nbsp;</td>

							<td width="80" align="right"><? $wo_per=$value['wo']/$value['req']*100; echo number_format($wo_per,2).'%'; ?>&nbsp;</td>

							<td width="110" align="right"><? echo number_format($value['in'],2); ?>&nbsp;</td>

							<td width="80" align="right"><? $in_per=$value['in']/$value['wo']*100; echo number_format($in_per,2).'%'; ?>&nbsp;</td>

							<td width="110" align="right"><? echo number_format($value['wo']-$value['in'],2); $in_house_bal+=($value['wo']-$value['in']); ?>&nbsp;</td>

							<td width="110" align="right"><? echo number_format($value['issue'],2); ?>&nbsp;</td>

							<td width="80" align="right"><? $wo_per=$value['issue']/$value['wo']*100; echo number_format($wo_per,2).'%'; ?>&nbsp;</td>

							<td align="right"><? echo number_format($value['leftover'],2); ?>&nbsp;</td>

						</tr>

					<?	

					$z++;

					}

					?>

					<tfoot>

						<th>&nbsp;</th>

						<th>&nbsp;</th>

						<th>&nbsp;</th>

						<th>&nbsp;</th>

						<th align="right"><? echo number_format($tot_req_qnty_summary,2); ?>&nbsp;</th>

						<th align="right"><? echo number_format($tot_wo_qnty_summary,2); ?>&nbsp;</th>

						<th>&nbsp;</th>

						<th align="right"><? echo number_format($tot_in_qnty_summary,2); ?>&nbsp;</th>

						<th>&nbsp;</th>

						<th align="right"><? echo number_format($in_house_bal,2); ?>&nbsp;</th>

						<th align="right"><? echo number_format($tot_issue_qnty_summary,2); ?>&nbsp;</th>

						<th>&nbsp;</th>

						<th align="right"><? echo number_format($tot_leftover_qnty_summary,2); ?>&nbsp;</th>

					</tfoot>   	

				</table>

			</fieldset>

		</div>

	<?

	

	}

	}

	

//===========================================================================================================================================================



  if(str_replace("'","",$cbo_search_by)==2)

  {

	$trim_group_arr=array();
	$trim_uom_arr=array();
 	$trimsArray=sql_select("select b.po_break_down_id,a.job_no,a.trim_group as trim_group, a.cons_uom,  a.rate from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id  and a.status_active=1 and a.is_deleted=0 $item_group_cond  
	union
	select b.po_breakdown_id,null,a.item_group_id as trim_group ,0,0 from  inv_receive_master d,inv_trims_entry_dtls a ,order_wise_pro_details b,product_details_master c where d.id=a.mst_id and a.trans_id=b.trans_id and b.trans_type=1 and b.entry_form=24 and d.receive_basis in (1,4,6) and a.prod_id=c.id and c.id=b.prod_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id,a.item_group_id,c.id
	");
	
	/*$trimsArray=sql_select("select  b.po_break_down_id,b.id,a.trim_group,a.insert_date as pre_date, a.cons_uom, a.apvl_req, a.brand_sup_ref, a.rate, b.cons,b.country_id,a.id as trim_dtls_id
	from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b 
	where a.id=b.wo_pre_cost_trim_cost_dtls_id and a.job_no=b.job_no and a.status_active=1 and  a.is_deleted=0  $item_group_cond group by  b.po_break_down_id,a.trim_group,b.id,a.cons_uom, a.apvl_req, a.brand_sup_ref, a.rate,b.cons,b.country_id,a.insert_date,a.id 
	
	union
	select  a.po_break_down_id,a.id,a.trim_group,null,null,null,null,null,null,null,null from wo_booking_mst b,wo_booking_dtls a where b.booking_no=a.booking_no and a.job_no=b.job_no and b.item_from_precost=2 and b.booking_type=2  $item_group_cond group by a.po_break_down_id,a.id,a.trim_group
	union
	select b.po_breakdown_id,c.id as prod_id,a.item_group_id as trim_group,null,null,null,null,null,null,null,null from  inv_receive_master d,inv_trims_entry_dtls a ,order_wise_pro_details b,product_details_master c where d.id=a.mst_id and a.trans_id=b.trans_id and b.trans_type=1 and b.entry_form=24 and d.receive_basis in (1,4,6) and a.prod_id=c.id and c.id=b.prod_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id,a.item_group_id,c.id
	 ");*/
	 
	foreach($trimsArray as $trim_val)
	{
		$trim_group_arr[$trim_val[csf("job_no")]][$trim_val[csf("trim_group")]]=$trim_val[csf("trim_group")];
		$trim_uom_arr[$trim_val[csf("job_no")]][$trim_val[csf("trim_group")]]["uom"]=$trim_val[csf("cons_uom")];

	}
	//print_r($trim_uom_arr);
	$rec_qty_arr=array();
	$sql_rev=sql_select(" select
	            o.po_breakdown_id,
				p.item_group_id,
				sum( o.quantity) AS receive_qty
				from
					order_wise_pro_details o, product_details_master p
				where 
						 o.prod_id=p.id and o.entry_form=24 group by  o.po_breakdown_id,
				p.item_group_id ");
		foreach($sql_rev as $re_val)
		{
			$rec_qty_arr[$re_val[csf("po_breakdown_id")]][$re_val[csf("item_group_id")]]+=$re_val[csf("receive_qty")];
		}
	  $iss_qty_arr=array();
	  $sql_iss=sql_select("select
			    a.id,
				p.item_group_id,
				o.quantity   AS iss_qty
				from
						wo_po_break_down a,wo_po_details_master b, inv_transaction t,order_wise_pro_details o, product_details_master p,inv_issue_master r
				where 
						a.job_no_mst=b.job_no and o.po_breakdown_id=a.id and t.id=o.trans_id and o.entry_form=25 and r.id=t.mst_id and t.prod_id=p.id ");
		foreach($sql_iss as $iss_val)
		{
			$iss_qty_arr[$iss_val[csf("id")]][$iss_val[csf("item_group_id")]]+=$iss_val[csf("iss_qty")];
		}
	//print_r($trim_group_arr);
	if($template==1)
	{
		ob_start();
	?>
		<div style="width:1780px">
		<fieldset style="width:100%;">	
			<table width="1250">
				<tr class="form_caption">
					<td colspan="12" align="center">Accessories Followup Report</td>
				</tr>
				<tr class="form_caption">
					<td colspan="12" align="center"><? echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
			<table class="rpt_table" width="1250" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="30">SL</th>
					<th width="100">Buyer Name</th>
					<th width="100">Job No</th>
					<th width="100">Style Ref</th>
					<th width="200">Order No</th>
					<th width="100">Order Qnty</th>
					<th width="100">Item Group</th>
					<th width="50">UOM</th>
					<th width="100">Rev. Qty</th>
					<th width="100">Issue Qty.</th>
                    <th width="100">Left Over</th>
					<th>Remarks</th>

				</thead>

			</table>

		<div style="width:1270px; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="1250" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<?
				$i=1; $s=1; $total_po_qnty=0; $total_req_qnty=0; $total_order_qnty_in_pcs=0; $total_wo_qnty=0; $total_in_qnty=0; $item_array=array(); $uom_array=array(); $item_app_array=array();
				if($db_type==2)

				{

				$sql_style="select max(a.job_no_prefix_num) as job_no_prefix_num,a.job_no, max(a.buyer_name) as buyer_name, a.style_ref_no,listagg(cast((b.id ||'**'||b.po_number) as varchar(4000)),',') within group (order by null) as po_num, sum(b.po_quantity)as po_qty from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_id_cond $date_cond $year_cond $job_no_cond $style_ref_cond group by  a.job_no,a.style_ref_no order by a.job_no";
				

				}
				else if($db_type==0)
				{
				$sql_style="select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no,group_concat(concat_ws('**',b.id,b.po_number)) as po_num, sum(b.po_quantity)as po_qty from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_id_cond $date_cond $year_cond $job_no_cond $style_ref_cond group by a.job_no,a.style_ref_no order by a.buyer_name,a.job_no"; //and a.buyer_name like '$buyer_name' and b.pub_shipment_date between '$start_date' and '$end_date'
				}//echo $sql_style;die;	
				$nameArray=sql_select($sql_style);
				$tot_rows=count($nameArray);
				foreach($nameArray as $row )
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$po_num_all=array_unique(explode(',',$row[csf('po_num')]));
					$po_num="";
					$po_dataArray=array();
					foreach( $po_num_all as $val_a)
					{
						$id_val=explode('**',$val_a);
						$po_id=$id_val[0];
						if($po_num=="") $po_num=$id_val[1]; else $po_num.=",".$id_val[1];
					}
					$k=1;
					?>
					<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $s; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $s; ?>">
						<td width="30"><? echo $i; ?></td>
						<td width="100"><? echo $buyer_short_name_library[$row[csf('buyer_name')]]; ?></td>
						<td width="100" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
						<td width="100"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
						<td width="200"><p><? echo $po_num; ?></p></td>
						<td width="100" align="right"><? $total_po_qnty+=$row[csf('po_qty')]; echo $row[csf('po_qty')]; ?></td>  
                        <?

						$k=1;
						//print_r($trim_group_arr[$row[csf('job_no')]]);
						foreach($trim_group_arr[$row[csf('job_no')]] as $val)
						{
						   $rev_qty=0;
						   $iss_qty=0;
						   foreach( $po_num_all as $val_b)
							{
								$po_val=explode('**',$val_b);
								$po_no_id=$po_val[0];
								$rev_qty+=$rec_qty_arr[$po_no_id][$val];
								$iss_qty+=$iss_qty_arr[$po_no_id][$val];
							}
							if($k==1)
							{
						?>
						<td width="100" align="center"><? echo $item_library[$val]; ?></td>
						<td width="50" align="center"><? echo $unit_of_measurement[ $trim_uom_arr[$row[csf('job_no')]][$val]["uom"]];?></td>
                        <td width="100" align="right"><? echo number_format($rev_qty,2,'.',''); ?></td>
						<td width="100" align="right"><? echo number_format($iss_qty,2,'.','') ?></td>
						<td width="100" align="right"><? echo number_format(($rev_qty-$iss_qty),2,'.',''); ?></td>
						<td  align="center"></td>
                        </tr>
                        <?
							}
						else
						{
						?>
							<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $s; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $s; ?>">
								<td width="30">
									<font style="display:none" color="<? echo $bgcolor; ?>">
										<? echo $i; ?>
									</font>
								</td>
								<td width="100">
									<font style="display:none" color="<? echo $bgcolor; ?>">
										<? echo $buyer_short_name_library[$row[csf('buyer_name')]]; ?>
									</font>
								</td>
								<td width="100">
									<font style="display:none" color="<? echo $bgcolor; ?>">
										<? echo $row[csf('job_no_prefix_num')]; ?>
									</font>
								</td>
								<td width="100">
									<font style="display:none" color="<? echo $bgcolor; ?>">

										<p>

											<? echo $row[csf('style_ref_no')]; ?>

										</p>

									</font>

								</td>

								<td width="200">

									<font style="display:none" color="<? echo $bgcolor; ?>">

										<p>

											<? echo $row[csf('po_number')]; ?>

										</p>

									</font>

								</td>

								<td width="100" align="right">
									<font style="display:none" color="<? echo $bgcolor; ?>">
										<? //echo number_format($row[csf('po_quantity')],0,'.',''); ?>
									</font>
								</td>
								<td width="100" align="center"><p><? echo  $item_library[$val]; ?></p></td>
								<td width="50" align="center"><? echo $unit_of_measurement[ $trim_uom_arr[$row[csf('job_no')]][$val]["uom"]]; ?></td>
                                <td width="100" align="right"><?  echo number_format($rev_qty,2,'.',''); ?></td>
								<td width="100" align="right"><? echo number_format($iss_qty,2,'.','') ?></td>
								<td width="100" align="right"><? echo number_format(($rev_qty-$iss_qty),2,'.',''); ?></td>
								<td align="right"></td>
							</tr>
						<?
						}
						 $k++;
					     $s++;	
                         $total_issue_qnty+=$iss_qty;
                         $total_receiv_qnty+=$rev_qty;

					}
				if(count($trim_group_arr[$row[csf('job_no')]])<1)
					{
					?>
						<td width="100" align="center"></td>
						<td width="50" align="center"></td>
                        <td width="100" align="right"></td>
						<td width="100" align="right"></td>
						<td width="100" align="right"></td>
						<td  align="center"></td>
                        </tr>
					<?
					} 
				$i++;
				}
				?>
			</table>
            <table class="rpt_table" width="1250" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tfoot>
                       <tr>
                            <th width="30"></th>
                            <th width="100"></th>
                            <th width="100" align="center"></th>
                            <th width="100"><p></p></th>
                            <th width="200"><p></p></th>
                            <th width="100" align="right" id="total_order_qnty"><? echo number_format($total_po_qnty,2); ?></th>
                            <th width="100" align="right" id="value_pre_costing"></th>
                            <th width="50" align="right" id=""></th>
                            <th width="100" align="right" id="value_rec_qty"><? echo number_format($total_receiv_qnty,2); ?></th>
                            <th width="100" align="right" id="value_issue_qty"><?  echo number_format($total_issue_qnty,2); ?></th>
                            <th width="100" align="right" id="value_leftover_qty"><? echo number_format(($total_receiv_qnty-$total_issue_qnty),2); ?></th>
                            <th align="right" id=""></th>
                           </tr>
					</tfoot>
				</table>
            </div>	
			</fieldset>
		</div>
	<?
	}

}





	foreach (glob("*.xls") as $filename) {

	//if( @filemtime($filename) < (time()-$seconds_old) )

	@unlink($filename);

	}

	//---------end------------//

	$name=time();

	$filename=$name.".xls";

	$create_new_doc = fopen($filename, 'w');	

	$is_created = fwrite($create_new_doc,ob_get_contents());

	echo "$total_data****$filename****$tot_rows";

	exit();	

}



if($action=="booking_info")

{

	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');

	extract($_REQUEST);

	?>

	<div style="width:780px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>

	<fieldset style="width:770px; margin-left:3px">

		<div id="scroll_body" align="center">

			<table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0" align="center">

				<thead>

                    <th width="30">Sl</th>

                    <th width="100">Wo No</th>

                    <th width="75">Wo Date</th>

                     <th width="200">Item Description</th>

                    <th width="80">Wo Qty</th>

                    <th width="100">Supplier</th>

				</thead>

                <tbody>

                <?

					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );

					$i=1;

					$item_description_arr=array();

					$wo_sql_trim=sql_select("select b.id,b.item_color,b.job_no, b.po_break_down_id, b.description,b.brand_supplier,b.item_size from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id=b.wo_trim_booking_dtls_id and a.is_deleted=0 and a.status_active=1 and a.job_no=b.job_no  group by b.id,b.po_break_down_id,b.job_no,b.description,b.brand_supplier,b.item_size,b.item_color");

					foreach($wo_sql_trim as $row_trim)

					{

					$item_description_arr[$row_trim[csf('po_break_down_id')]][$row_trim[csf('job_no')]]['job_no']=$row_trim[csf('job_no')];

					$item_description_arr[$row_trim[csf('po_break_down_id')]][$row_trim[csf('job_no')]]['description']=$row_trim[csf('description')];

					$item_description_arr[$row_trim[csf('po_break_down_id')]][$row_trim[csf('job_no')]]['size']=$row_trim[csf('item_size')];

					$item_description_arr[$row_trim[csf('po_break_down_id')]][$row_trim[csf('job_no')]]['supplier']=$row_trim[csf('supplier')];	

					$item_description_arr[$row_trim[csf('po_break_down_id')]][$row_trim[csf('job_no')]]['color']=$row_trim[csf('item_color')];		

	

					} //var_dump($item_description_arr);

					$wo_sql="select a.booking_no, a.booking_date, a.supplier_id,b.job_no, b.po_break_down_id,  sum(b.wo_qnty) as wo_qnty from wo_booking_mst a, wo_booking_dtls b where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.trim_group=$item_name and b.po_break_down_id='$po_id' group by b.job_no, a.booking_no, a.booking_date, a.supplier_id, b.po_break_down_id";

					$dtlsArray=sql_select($wo_sql);

					

					foreach($dtlsArray as $row)

					{

						if ($i%2==0)  

							$bgcolor="#E9F3FF";

						else

							$bgcolor="#FFFFFF";	

							$item_group=$item_library[$item_name];

							//$job_no=$row[csf('job_no')];

							$item_descrp=$item_description_arr[$row[csf('po_break_down_id')]][$row[csf('job_no')]]['description'];

							$item_size=$item_description_arr[$row[csf('po_break_down_id')]][$row[csf('job_no')]]['size'];

							$supplier=$item_description_arr[$row[csf('po_break_down_id')]][$row[csf('job_no')]]['supplier'];

							$item_color=$item_description_arr[$row[csf('po_break_down_id')]][$row[csf('job_no')]]['color'];

							$item_des=$item_group.','.$item_descrp.','.$item_size.','.$color_name_library[$item_color].','.$supplier;

							$item_d2=$item_size.','.$color_name_library[$item_color].','.$supplier;

						?>

						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">

							<td width="30"><p><? echo $i; ?></p></td>

                            <td width="100"><p><? echo $row[csf('booking_no')]; ?></p></td>

                            <td width="75"><p><? echo change_date_format($row[csf('booking_date')]); ?></p></td>

                             <td width="200" align="right"><p><? if($item_des!='') echo $item_des; else $item_d2; ?></p></td>

                            <td width="80" align="right"><p><? echo number_format($row[csf('wo_qnty')],2); ?></p></td>

                            <td width="100"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p></td>

                        </tr>

						<?

						$tot_qty+=$row[csf('wo_qnty')];

						$i++;

					}

				?>

                </tbody>

                <tfoot>

                	<tr class="tbl_bottom">

                    	<td colspan="4" align="right">Total</td>

                        <td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>

                        <td>&nbsp;</td>

                    </tr>

                </tfoot>

            </table>

        </div>

    </fieldset>

    <?

	exit();

}

disconnect($con);

?>

<?

if($action=="booking_inhouse_info")

{

	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');

	extract($_REQUEST);

	?>

	<div style="width:780px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>

	<fieldset style="width:770px; margin-left:3px">

		<div id="scroll_body" align="center">

			<table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0" align="center">

				<thead>

                    <th width="30">Sl</th>

                    <th width="80">Prod. ID</th>

                    <th width="100">Recv. ID</th>

                     <th width="100">Recv. Date</th>

                    <th width="80">Item Description.</th>

                    <th width="100">Recv. Qty.</th>

				</thead>

                <tbody>

                <?

					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );

					$i=1;

					//$wo_sql="select a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description,sum(a.cons_qnty) as cons_qnty  from inv_receive_master b, inv_trims_entry_dtls a where b.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=4 group by a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description";

					

					//echo $receive_qty_data=("select b.po_breakdown_id,c.id as prod_id,c.item_description,d.recv_number,d.receive_date, a.item_group_id,sum(b.quantity) as quantity from  inv_receive_master d,inv_trims_entry_dtls a ,order_wise_pro_details b,product_details_master c where d.id=a.mst_id and a.trans_id=b.trans_id and b.trans_type=1 and a.item_group_id='$item_name' and b.po_breakdown_id=$po_id and b.entry_form=24 and a.prod_id=c.id and c.id=b.prod_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id,a.item_group_id,c.item_description,d.recv_number,d.receive_date, a.item_group_id,c.id");

					$receive_qty_data=("select a.id, c.po_breakdown_id,b.item_group_id,b.prod_id as prod_id,b.item_description, a.recv_number, a.receive_date, SUM(c.quantity) as quantity

					from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c 

					where a.id=b.mst_id  and a.entry_form=24 and  a.item_category=4  and b.id=c.dtls_id and c.trans_type=1 and  c.po_breakdown_id in($po_id)  and b.item_group_id='$item_name' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by  c.po_breakdown_id,b.item_group_id,b.prod_id,a.id,b.item_description, a.recv_number, a.receive_date");



					$dtlsArray=sql_select($receive_qty_data);

					

					foreach($dtlsArray as $row)

					{

						if ($i%2==0)  

							$bgcolor="#E9F3FF";

						else

							$bgcolor="#FFFFFF";	

							

						?>

						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">

							<td width="30"><p><? echo $i; ?></p></td>

                            <td width="80"><p><? echo $row[csf('prod_id')]; ?></p></td>

                            <td width="100" align="center"><p><? echo $row[csf('recv_number')]; ?></p></td>

                             <td width="100" align="center"><p><? echo  change_date_format($row[csf('receive_date')]); ?></p></td>

                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>

                            <td width="100" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>

                        </tr>

						<?

						$tot_qty+=$row[csf('quantity')];

						$i++;

					}

				?>

                </tbody>

                <tfoot>

                	<tr class="tbl_bottom">

                    	<td colspan="4" align="right"></td>

                        <td align="right">Total</td>

                        <td><? echo number_format($tot_qty,2); ?></td>

                    </tr>

                </tfoot>

            </table>

        </div>

    </fieldset>

    <?

	exit();

}

disconnect($con);

?>



<?				

if($action=="booking_issue_info")

{

	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');

	extract($_REQUEST);

	?>

	<div style="width:780px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>

	<fieldset style="width:770px; margin-left:3px">

		<div id="scroll_body" align="center">

			<table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0" align="center">

				<thead>

                    <th width="30">Sl</th>

                    <th width="80">Prod. ID</th>

                    <th width="100">Issue. ID</th>

                     <th width="100">Issue. Date</th>

                    <th width="80">Item Description.</th>

                    <th width="100">Issue. Qty.</th>

				</thead>

                <tbody>

                <?

					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );

					$i=1;

					//$wo_sql="select a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description,sum(a.cons_qnty) as cons_qnty  from inv_receive_master b, inv_trims_entry_dtls a where b.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=4 group by a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description";

					

				 $mrr_sql=("select a.id, a.issue_number,b.prod_id, a.issue_date,b.item_description,SUM(c.quantity) as quantity

					from  inv_issue_master a,inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master p 

					where a.id=b.mst_id  and a.entry_form=25 and p.id=b.prod_id and b.id=c.dtls_id and c.trans_type=2 and a.is_deleted=0 and a.status_active=1 and

					b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id) and p.item_group_id='$item_name' group by c.po_breakdown_id,p.item_group_id,b.item_description,a.issue_number,a.id,a.issue_date,b.prod_id ");					

					

					$dtlsArray=sql_select($mrr_sql);

					

					foreach($dtlsArray as $row)

					{

						if ($i%2==0)  

							$bgcolor="#E9F3FF";

						else

							$bgcolor="#FFFFFF";	

							

						?>

						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">

							<td width="30"><p><? echo $i; ?></p></td>

                            <td width="80" align="center"><p><? echo $row[csf('prod_id')]; ?></p></td>

                            <td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>

                             <td width="100" align="center"><p><? echo  change_date_format($row[csf('issue_date')]); ?></p></td>

                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>

                            <td width="100" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>

                        </tr>

						<?

						$tot_qty+=$row[csf('quantity')];

						$i++;

					}

				?>

                </tbody>

                <tfoot>

                	<tr class="tbl_bottom">

                    	<td colspan="4" align="right"></td>

                        <td align="right">Total</td>

                        <td><? echo number_format($tot_qty,2); ?></td>

                    </tr>

                </tfoot>

            </table>

        </div>

    </fieldset>

    <?

	exit();

}

disconnect($con);

?>