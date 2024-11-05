<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$order_arr=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number"  );
$brand_arr=return_library_array( "select id,brand_name from lib_brand", "id", "brand_name"  );
$knitting_source_arr = array(1=>'In-house',3=>'Out-bound Subcontract');

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 180, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$data $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_cpa_booking_no=str_replace("'","",$txt_cpa_booking_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$type=str_replace("'","",$type);

	//$exchange_rate = return_field_value("conversion_rate", "(SELECT conversion_rate FROM currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 ORDER BY id DESC)", "ROWNUM = 1");

	if($type==0 || $type==1)
	{
		$buyer_cond=$booking_cond="";
		if($cbo_buyer_name!=0) $buyer_cond=" and a.buyer_id=$cbo_buyer_name";
		if($txt_cpa_booking_no!="") $booking_cond=" and a.booking_no_prefix_num in(".$txt_cpa_booking_no.")";
		$sql_date_cond	= "";
		if ($db_type == 0) {
			if ($txt_date_from != "" && $txt_date_to != "")
				$sql_date_cond .= " and a.issue_date  between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
				$sql_date_cond1 .= " and a.receive_date  between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
		}
		else {
			if ($txt_date_from != "" && $txt_date_to != "")
				$sql_date_cond .= " and a.issue_date between '" . date("j-M-Y", strtotime($txt_date_from)) . "' and '" . date("j-M-Y", strtotime($txt_date_to)) . "'";
				$sql_date_cond1 .= " and a.receive_date between '" . date("j-M-Y", strtotime($txt_date_from)) . "' and '" . date("j-M-Y", strtotime($txt_date_to)) . "'";
		}

		$dataArray_booking=sql_select( "select a.id, a.booking_no, a.booking_no_prefix_num, a.job_no,a.is_short, a.buyer_id,b.po_break_down_id as po_id,b.fin_fab_qnty from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=1  and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $buyer_cond $booking_cond" );
		$booking_data_arr=$boking_check=$bookingIdChk=$bookingIdArr=array();
		foreach($dataArray_booking as $row)
		{
			if($row[csf('is_short')]==1)
			{
				$boking_check[$row[csf('id')]]=$row[csf('id')];
				$booking_data_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
				$booking_data_arr[$row[csf('id')]]['booking_no_prefix_num']=$row[csf('booking_no_prefix_num')];
				$booking_data_arr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
				$booking_data_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_id')];
				if($row[csf('fin_fab_qnty')]>0)
				{
				$booking_qty_data_arr[$row[csf('po_id')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
				}

				/* if($bookingIdChk[$row[csf('id')]] == "")
				{
					$bookingIdChk[$row[csf('id')]] = $row[csf('id')];
					array_push($bookingIdArr,$row[csf('id')]);
				} */
			}
			else
			{

				if($row[csf('fin_fab_qnty')]>0)
				{
				$booking_qty_data_arr[$row[csf('po_id')]]['main_fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
				}
			}
		}

		/* if(!empty($bookingIdArr))
		{
			$bookingIdCond = "".where_con_using_array($bookingIdArr,0,'a.booking_id')."";
		} */

		$dataArray_requisition=sql_select("select a.id, a.booking_no, a.booking_no_prefix_num, a.job_no, a.buyer_id, d.requisition_no from wo_booking_mst a, ppl_planning_info_entry_mst b, ppl_planning_info_entry_dtls c, ppl_yarn_requisition_entry d where a.booking_no=b.booking_no and b.id=c.mst_id and c.id=d.knit_id and a.booking_type=1 and a.is_short=1 and a.company_id=$cbo_company_name $buyer_cond $booking_cond group by d.requisition_no, a.id, a.booking_no, a.booking_no_prefix_num, a.job_no, a.buyer_id");
		$req_data_arr=$req_check=$reqChk=$reqArr=array();
		foreach($dataArray_requisition as $row)
		{
			$req_check[$row[csf('requisition_no')]]=$row[csf('requisition_no')];
			$req_data_arr[$row[csf('requisition_no')]]['booking_no']=$row[csf('booking_no')];
			$req_data_arr[$row[csf('requisition_no')]]['booking_no_prefix_num']=$row[csf('booking_no_prefix_num')];
			$req_data_arr[$row[csf('requisition_no')]]['job_no']=$row[csf('job_no')];
			$req_data_arr[$row[csf('requisition_no')]]['buyer']=$row[csf('buyer_id')];

			/* if($reqChk[$row[csf('requisition_no')]] == "")
			{
				$reqChk[$row[csf('requisition_no')]] = $row[csf('requisition_no')];
				array_push($reqArr,$row[csf('requisition_no')]);
			} */

		}

		/* if(!empty($reqArr))
		{
			$reqNoCond = "".where_con_using_array($reqArr,0,'b.requisition_no')."";
		} */
		$product_data=sql_select("select id, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_type, color from product_details_master where status_active=1 and item_category_id=1");
		$product_data_arr=array();
		foreach($product_data as $val)
		{
			$product_data_arr[$val[csf("id")]]['prod_id']=$val[csf("id")];
			$product_data_arr[$val[csf("id")]]['yarn_count_id']=$val[csf("yarn_count_id")];
			$product_data_arr[$val[csf("id")]]['yarn_comp_type1st']=$val[csf("yarn_comp_type1st")];
			$product_data_arr[$val[csf("id")]]['yarn_comp_percent1st']=$val[csf("yarn_comp_percent1st")];
			$product_data_arr[$val[csf("id")]]['yarn_type']=$val[csf("yarn_type")];
			$product_data_arr[$val[csf("id")]]['color']=$val[csf("color")];
		}

		$sql_return = sql_select("SELECT a.id as mst_id, b.issue_id, c.po_breakdown_id as po_id, c.prod_id,a.booking_id, c.quantity as return_qty from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.item_category=1 and a.entry_form=9 and a.company_id=$cbo_company_name $sql_date_cond1 and b.item_category=1 and b.transaction_type=4 and a.receive_basis in(1,3) and b.receive_basis in(1,3) and a.knitting_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");//and a.receive_date between '$txt_date_from' and '$txt_date_to'
		$return_qty_arr = array();
		foreach ($sql_return as $row)
		{
			$return_qty_arr[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['return_qty']+=$row[csf("return_qty")];
		}

	    //print_r($return_qty_arr);
		ob_start();

		$divWidth = 1660;
		$tblWidth = 1640;


		?>
	    <div style="width:<?php echo $divWidth; ?>px" align="left">
	    <fieldset style="width:100%;">
	        <table cellpadding="0" cellspacing="0" width="<?php echo $tblWidth; ?>">
	            <tr>
	               <td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
	            </tr>
	            <tr>
	               <td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $company_arr[$cbo_company_name]; ?></strong></td>
	            </tr>
	        </table>
            <br>
            <?
			 $sql_inside="SELECT a.id as mst_id, a.buyer_id, a.booking_id, a.knit_dye_source, a.knit_dye_company, a.remarks, b.id as trans_id,b.cons_uom, b.requisition_no, c.po_breakdown_id, c.prod_id, c.quantity, d.grouping,b.receive_basis, b.cons_rate from inv_issue_master a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d where a.id=b.mst_id and b.id=c.trans_id and c.po_breakdown_id=d.id and a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name  $sql_date_cond and b.item_category=1 and b.transaction_type=2 and a.issue_basis in(1,3) and a.issue_purpose=1 and b.receive_basis in(1,3) and a.knit_dye_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.knit_dye_source,a.issue_number, a.issue_date";
			//echo $sql_inside; die; //and a.issue_date between '$txt_date_from' and '$txt_date_to'//$reqNoCond $bookingIdCond
			$result=sql_select($sql_inside);
			//$result=$result;
			foreach($result as $row)
			{

				$main_fin_fab_qnty=$booking_qty_data_arr[$row[csf('po_breakdown_id')]]['main_fin_fab_qnty'];
				if($boking_check[$row[csf('booking_id')]]!="" && $row[csf('receive_basis')]==1)
				{
				$buyer_id=$booking_data_arr[$row[csf('booking_id')]]['buyer'];
				$fin_fab_qnty=$booking_qty_data_arr[$row[csf('po_breakdown_id')]]['fin_fab_qnty'];

				//echo $main_fin_fab_qnty.'='.$row[csf('po_breakdown_id')].', ';
				$buyer_wise_arr[$buyer_id]['quantity']+=$row[csf('quantity')];
				$buyer_wise_arr[$buyer_id]['short_fin_fab_qnty']+=$fin_fab_qnty;
				$buyer_wise_arr[$buyer_id]['main_fin_fab_qnty']+=$main_fin_fab_qnty;
				$buyer_wise_arr[$buyer_id]['cons_uom']=$unit_of_measurement[$row[csf('cons_uom')]];
				}
				else if($req_check[$row[csf('requisition_no')]]!="" && $row[csf('receive_basis')]==3)
				{
					//req_data_arr[$row[csf('requisition_no')]]['buyer']
					$buyerID=$req_data_arr[$row[csf('requisition_no')]]['buyer'];
					$buyer_wise_arr[$buyerID]['main_fin_fab_qnty']+=$main_fin_fab_qnty;
					$fin_fab_qnty=$booking_qty_data_arr[$row[csf('po_breakdown_id')]]['fin_fab_qnty'];
					$buyer_wise_arr[$buyerID]['short_fin_fab_qnty']+=$fin_fab_qnty;
					$buyer_wise_arr[$buyerID]['quantity']+=$row[csf('quantity')];
					$buyer_wise_arr[$buyerID]['cons_uom']=$unit_of_measurement[$row[csf('cons_uom')]];
				}
			}
		// Outbound
			$sql_return_subcon = sql_select("SELECT a.id as mst_id, b.issue_id, c.po_breakdown_id as po_id, c.prod_id, c.quantity as return_qty from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.item_category=1 and a.entry_form=9 and a.company_id=$cbo_company_name $sql_date_cond1 and b.item_category=1 and b.transaction_type=4 and a.receive_basis in(1,3) and b.receive_basis in(1,3) and a.knitting_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");//and a.receive_date between '$txt_date_from' and '$txt_date_to'
			$return_qty_arr_subcon = array();
			foreach ($sql_return_subcon as $row)
			{
				$return_qty_arr_subcon[$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['return_qty']+=$row[csf("return_qty")];
			}
			//print_r($return_qty_arr);

			$sql_subcon="select a.id as mst_id, a.buyer_id, a.booking_id, a.knit_dye_source, a.knit_dye_company, a.remarks, b.cons_uom,b.id as trans_id, b.requisition_no, c.po_breakdown_id, c.prod_id, c.quantity, d.grouping, b.cons_rate from inv_issue_master a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d
			where a.id=b.mst_id and b.id=c.trans_id and c.po_breakdown_id=d.id and a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name  $sql_date_cond  and b.item_category=1 and b.transaction_type=2 and a.issue_basis in(1,3) and a.issue_purpose=1 and b.receive_basis in(1,3) and a.knit_dye_source=3 and a.issue_purpose <> 2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
			order by a.knit_dye_source,a.issue_number, a.issue_date";
			//echo $sql_subcon;die; // and a.issue_date between '$txt_date_from' and '$txt_date_to'
			$result_subcon=sql_select($sql_subcon);
			foreach($result_subcon as $row)
			{
					if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					 $main_fin_fab_qnty=$booking_qty_data_arr[$row[csf('po_breakdown_id')]]['main_fin_fab_qnty'];
					// echo $main_fin_fab_qnty.'='.$row[csf('quantity')].',';
					if($boking_check[$row[csf('booking_id')]]!="")
					{
						$buyer_wise_arr[$buyer_id]['quantity']+=$row[csf('quantity')];
						$buyer_wise_arr[$buyer_id]['cons_uom']=$unit_of_measurement[$row[csf('cons_uom')]];
						$fin_fab_qnty=$booking_qty_data_arr[$row[csf('po_breakdown_id')]]['fin_fab_qnty'];
						$buyer_wise_arr[$buyer_id]['main_fin_fab_qnty']+=$main_fin_fab_qnty;
						$buyer_wise_arr[$buyer_id]['short_fin_fab_qnty']+=$fin_fab_qnty;
					}
					else if($req_check[$row[csf('requisition_no')]]!="")
					{
						//echo $row[csf('quantity')].',';
						$buyerID=$req_data_arr[$row[csf('requisition_no')]]['buyer'];
						$buyer_wise_arr[$buyerID]['quantity']+=$row[csf('quantity')];
						$buyer_wise_arr[$buyerID]['cons_uom']=$unit_of_measurement[$row[csf('cons_uom')]];
						$fin_fab_qnty=$booking_qty_data_arr[$row[csf('po_breakdown_id')]]['fin_fab_qnty'];
						$buyer_wise_arr[$buyerID]['main_fin_fab_qnty']+=$main_fin_fab_qnty;
						$buyer_wise_arr[$buyerID]['short_fin_fab_qnty']+=$fin_fab_qnty;
					}
			}

			if($type==1)
			{
            $width_summ=600;
			?>
            <table width="<?php echo $width_summ; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
	            <thead>
	                <th width="30">SL</th>
	                <th width="100">Buyer Name</th>
	                <th width="100">Booking Qty.</th>
	                <th width="70">Unit</th>
	                <th width="100">Short Qty.</th>
	                <th width="100">Yarn Issue Against Short Booking</th>
	                <th width="">Remarks</th>
	            </thead>
                <tbody>
                <?
				$i=1;$tot_main_fab_qty=$tot_short_fab_qty=$tot_issue_qty=0;
                foreach($buyer_wise_arr as $buyer_id=>$row)
				{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
                <tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('trsum<? echo $i;?>','<? echo $bgcolor;?>')" id="trsum<? echo $i;?>">
                    <tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="100" ><p><? echo $buyer_arr[$buyer_id]; ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($row[("main_fin_fab_qnty")],0); ?></p></td>
                    <td width="70"><p><? echo $row[('cons_uom')]; ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($row[('short_fin_fab_qnty')],0); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($row[("quantity")],0); ?></p></td>
                    <td width=""><p></p></td>
                </tr>
                <?
				$i++;
				$tot_main_fab_qty+=$row[("main_fin_fab_qnty")];
				$tot_short_fab_qty+=$row[("short_fin_fab_qnty")];
				$tot_issue_qty+=$row[("quantity")];
				}
				?>
                </tbody>
                <tfoot>

                <th colspan="2"> Total  </th>
                 <th width=""><? echo number_format($tot_main_fab_qty,0); ?></th>
                  <th width=""><? //echo number_format($tot_main_fab_qty,0); ?></th>
                 <th width=""><? echo number_format($tot_short_fab_qty,0); ?></th>
                 <th width=""><? echo number_format($tot_issue_qty,0); ?></th>
                  <th width=""><? //echo number_format($tot_main_fab_qty,0); ?></th>

                </tfoot>
	        </table>
            <?
			}
			?>
            <br>
	        <div style="font-size:16px; font-weight:bold">In-house</div>
	        <table width="<?php echo $tblWidth+20; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
	            <thead>
	                <th width="30">SL</th>
	                <th width="100">Buyer Name</th>
	                <th width="120">Order No</th>
	                <th width="100">Job No</th>
	                <th width="100">Int. Ref. No</th>
	                <th width="100">CPA NO</th>
	                <th width="150">Issue Party Name</th>
	                <th width="60">YarnCount</th>
	                <th width="120">Composition</th>
	                <th width="100">Yarn Type</th>
					<th width="80">Yarn Rate</th>
	                <th width="100">Colour</th>
	                <th width="80">Yarn Issued Qty</th>
	                <th width="80">Return Qty</th>
	                <th width="80">Net Issue</th>
	                <th width="80">Yarn Value</th>
	                <th width="160">Remarks</th>
	            </thead>
	        </table>
	        <div style="width:<?php echo $divWidth+20; ?>px; overflow-y: scroll; max-height:280px;" id="scroll_body">
				<table width="<?php echo $tblWidth; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="tbl_body">
	                <tbody>
	                <?

						$i=1;$issue_qnty_inhouse=0;$tot_return_qnty=0;$tot_net_issue=$tot_net_issue_val=0;
						foreach($result as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$composition_id="";
							$composition_id = $product_data_arr[$row[csf('prod_id')]]['yarn_comp_type1st'];
							$return_qty = $return_qty_arr[$row[csf("requisition_no")]][$row[csf("mst_id")]][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]['return_qty'];
							// $return_qty = $return_qty_arr[$row[csf("mst_id")]][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]['return_qty'];
							//echo $row[csf("mst_id")].'='.$row[csf("po_breakdown_id")].'='.$row[csf("prod_id")].'<br>';
							if($boking_check[$row[csf('booking_id')]]!="" && $row[csf('receive_basis')]==1)
							{
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="30"><? echo $i; ?></td>
									<td width="100" ><p><? echo $buyer_arr[$booking_data_arr[$row[csf('booking_id')]]['buyer']]; ?></p></td>
									<td width="120"><p><? echo $order_arr[$row[csf("po_breakdown_id")]]; ?></p></td>
									<td width="100"><p><? echo $booking_data_arr[$row[csf('booking_id')]]['job_no']; ?></p></td>
									<td width="100"><p><? echo $row[csf('grouping')]; ?></p></td>
									<td width="100"><p><? echo $booking_data_arr[$row[csf('booking_id')]]['booking_no']; ?></p></td>
									<td width="150"><div style="word-break:break-all"><? echo $company_arr[$row[csf('knit_dye_company')]]; ?></div></td>
									<td width="60" align="center"><p><? echo $count_arr[$product_data_arr[$row[csf('prod_id')]]['yarn_count_id']]; ?></p></td>
									<td width="120"><div style="word-break:break-all"><? if($composition_id!="")  echo $composition[$composition_id]." ".$product_data_arr[$row[csf('prod_id')]]['yarn_comp_percent1st']."%"; ?></div></td>
									<td width="100"><div style="word-break:break-all"><? echo $yarn_type[$product_data_arr[$row[csf('prod_id')]]['yarn_type']]; ?></div></td>
									<td width="80" align="right" ><p><? echo $row[csf('cons_rate')]; ?></p></td>
									<td width="100" align="center"><div style="word-break:break-all"><? echo $color_arr[$product_data_arr[$row[csf('prod_id')]]['color']]; ?></div></td>
									<td width="80" align="right"><a href="##" onclick="openmypage('<? echo $row[csf("po_breakdown_id")]; ?>','yarn_issue','<? echo $row[csf("trans_id")]; ?>','<? echo $row[csf("prod_id")]; ?>')"><? echo number_format($row[csf('quantity')],0); ?></a></td>

									<td width="80" align="right" title="<? echo $row[csf("mst_id")]."_".$row[csf("po_breakdown_id")]."_".$row[csf("prod_id")];?>"><p><? echo number_format($return_qty,0); ?></p></td>
									<td width="80" align="right"><p><? $net_issue = $row[csf('quantity')] - $return_qty; echo number_format($net_issue,0); ?></p></td>
									<td width="80" align="right" title="Yarn Rate*Net Issue"><p><? $net_issue_val = $net_issue*$row[csf('cons_rate')]; echo number_format($net_issue_val,2); ?></p></td>
									<td ><p><? echo $row[csf('remarks')]; ?></p></td>
								</tr>
								<?
								$i++;
								$issue_qnty_inhouse += $row[csf('quantity')];
								$tot_return_qnty += $return_qty;
								$tot_net_issue += $net_issue;
								$tot_net_issue_val += $net_issue_val;
							}
							else if($req_check[$row[csf('requisition_no')]]!="" && $row[csf('receive_basis')]==3)
							{
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="30"><? echo $i; ?></td>
									<td width="100" ><p><? echo $buyer_arr[$req_data_arr[$row[csf('requisition_no')]]['buyer']]; ?></p></td>
									<td width="120"><p><? echo $order_arr[$row[csf("po_breakdown_id")]]; ?></p></td>
									<td width="100"><p><? echo $req_data_arr[$row[csf('requisition_no')]]['job_no']; ?></p></td>
									<td width="100"><p><? echo $row[csf('grouping')]; ?></p></td>
									<td width="100"><p><? echo $req_data_arr[$row[csf('requisition_no')]]['booking_no']; ?></p></td>
									<td width="150"><div style="word-break:break-all"><? echo $company_arr[$row[csf('knit_dye_company')]]; ?></div></td>
									<td width="60" align="center"><div style="word-break:break-all"><? echo $count_arr[$product_data_arr[$row[csf('prod_id')]]['yarn_count_id']]; ?></div></td>
									<td width="120"><div style="word-break:break-all"><? if($composition_id!="")  echo $composition[$composition_id]." ".$product_data_arr[$row[csf('prod_id')]]['yarn_comp_percent1st']."%"; ?></div></td>
									<td width="100"><div style="word-break:break-all"><? echo $yarn_type[$product_data_arr[$row[csf('prod_id')]]['yarn_type']]; ?></div></td>
									<td width="80" align="right" ><p><? echo $row[csf('cons_rate')]; ?></p></td>
									<td width="100" align="center"><div style="word-break:break-all"><? echo $color_arr[$product_data_arr[$row[csf('prod_id')]]['color']]; ?></div></td>
									<td width="80" align="right"><a href="##" onclick="openmypage('<? echo $row[csf("po_breakdown_id")]; ?>','yarn_issue','<? echo $row[csf("trans_id")]; ?>','<? echo $row[csf("prod_id")]; ?>')"><? echo number_format($row[csf('quantity')],0); ?></a></td>
									<td width="80" align="right" title="<? echo $row[csf("mst_id")]."_".$row[csf("po_breakdown_id")]."_".$row[csf("prod_id")];?>"><p><? echo number_format($return_qty,0); ?></p></td>
									<td width="80" align="right"><p><? $net_issue = $row[csf('quantity')] - $return_qty; echo number_format($net_issue,0); ?></p></td>
									<td width="80" align="right" title="Yarn Rate*Net Issue"><p><? $net_issue_val = $net_issue*$row[csf('cons_rate')]; echo number_format($net_issue_val,2); ?></p></td>
									<td ><p><? echo $row[csf('remarks')]; ?></p></td>
								</tr>
								<?
								$i++;
								$issue_qnty_inhouse += $row[csf('quantity')];
								$tot_return_qnty += $return_qty;
								$tot_net_issue += $net_issue;
								$tot_net_issue_val += $net_issue_val;
							}

						}
						?>
	            	</tbody>
	            </table>
	        </div>
	        <table width="<?php echo $tblWidth; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
	            <tfoot>
	                <th width="30">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="120">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="150">&nbsp;</th>
	                <th width="60">&nbsp;</th>
	                <th width="120">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="80">&nbsp;</th>
	                <th width="100">Sub Total:</th>
	                <th width="80"><? echo number_format($issue_qnty_inhouse,0); ?></th>
	                <th width="80"><? echo number_format($tot_return_qnty,0); ?></th>
	                <th width="80"><? echo number_format($tot_net_issue,0); ?></th>
					<th width="80"><? echo number_format($tot_net_issue_val,2); ?></th>
	                <th>&nbsp;</th>
	            </tfoot>
	        </table>
	        <br />
	        <div style="font-size:16px; font-weight:bold">Out-bound Subcontract</div>
	        <table width="<?php echo $tblWidth; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
	            <thead>
	                <th width="30">SL</th>
	                <th width="100">Buyer Name</th>
	                <th width="120">Order No</th>
	                <th width="100">Job No</th>
	                <th width="100">Int. Ref. No</th>
	                <th width="100">CPA NO</th>
	                <th width="150">Issue Party Name</th>
	                <th width="60">Yarn Count</th>
	                <th width="120">Composition</th>
	                <th width="100">Yarn Type</th>
	                <th width="80">Yarn Rate</th>
	                <th width="100">Colour</th>
	                <th width="80">Yarn Issued Qty</th>
	                <th width="80">Return Qty</th>
	                <th width="80">Net Issue</th>
	                <th width="80">Yarn Value</th>
	                <th width="160">Remarks</th>
	            </thead>
	        </table>
	        <div style="width:<?php echo $divWidth; ?>px; overflow-y: scroll; max-height:280px;" id="scroll_body2">
				<table width="<?php echo $tblWidth; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="tbl_body2">
	                <tbody>
	                <?

						$j=1;$issue_qnty_subcon=0;$return_qnty_subcon=0;$tot_net_issue_subcon=$tot_net_issue_subcon_val=0;$i=1;
						foreach($result_subcon as $row)
						{
							if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                    	$composition_id="";
							if($boking_check[$row[csf('booking_id')]]!="")
							{
								$composition_id=$product_data_arr[$row[csf('prod_id')]]['yarn_comp_type1st'];
								$return_qty = $return_qty_arr_subcon[$row[csf("mst_id")]][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]['return_qty'];
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="30"><? echo $j; ?></td>
									<td width="100"><p><? echo $buyer_arr[$booking_data_arr[$row[csf('booking_id')]]['buyer']]; ?></p></td>
									<td width="120"><p><? echo $order_arr[$row[csf("po_breakdown_id")]]; ?></p></td>
									<td width="100"><p><? echo $booking_data_arr[$row[csf('booking_id')]]['job_no']; ?></p></td>
									<td width="100"><p><? echo $row[csf('grouping')]; ?></p></td>
									<td width="100" align="center"><p><? echo $booking_data_arr[$row[csf('booking_id')]]['booking_no']; ?></p></td>
									<td width="150"><p><? echo $supplier_arr[$row[csf('knit_dye_company')]]; ?></p></td>
									<td width="60" align="center"><p><? echo $count_arr[$product_data_arr[$row[csf('prod_id')]]['yarn_count_id']]; ?></p></td>
									<td width="120"><p><? if($composition_id!="")  echo $composition[$composition_id]." ".$product_data_arr[$row[csf('prod_id')]]['yarn_comp_percent1st']."%"; ?></p></td>
									<td width="100"><p><? echo $yarn_type[$product_data_arr[$row[csf('prod_id')]]['yarn_type']]; ?></p></td>
									<td width="80" align="right" ><p><? echo $row[csf('cons_rate')]; ?></p></td>
									<td width="100" align="center"><p><? echo $color_arr[$product_data_arr[$row[csf('prod_id')]]['color']]; ?></p></td>
									<td width="80" align="right"><a href="##" onclick="openmypage('<? echo $row[csf("po_breakdown_id")]; ?>','yarn_issue','<? echo $row[csf("trans_id")]; ?>','<? echo $row[csf("prod_id")]; ?>')"><? echo number_format($row[csf('quantity')],0); ?></a></td>
									<td width="80" align="right"><p><? echo number_format($return_qty,0); ?></p></td>
									<td width="80" align="right"><p><? $net_issue_subcon = $row[csf('quantity')] - $return_qty; echo number_format($net_issue_subcon,0); ?></p></td>
									<td width="80" align="right" title="Yarn Rate*Net Issue"><p><? $net_issue_subcon_val = $net_issue_subcon*$row[csf('cons_rate')]; echo number_format($net_issue_subcon_val,2); ?></p></td>
									<td width="160"><p><? echo $row[csf('remarks')]; ?></p></td>
								</tr>
								<?
								$j++;
								$issue_qnty_subcon += $row[csf('quantity')];
								$return_qnty_subcon += $return_qty;
								$tot_net_issue_subcon += $net_issue_subcon;
								$tot_net_issue_subcon_val += $net_issue_subcon_val;
							}
							else if($req_check[$row[csf('requisition_no')]]!="")
							{
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="30"><? echo $j; ?></td>
									<td width="100" ><p><? echo $buyer_arr[$req_data_arr[$row[csf('requisition_no')]]['buyer']]; ?></p></td>
									<td width="120"><p><? echo $order_arr[$row[csf("po_breakdown_id")]]; ?></p></td>
									<td width="100"><p><? echo $req_data_arr[$row[csf('requisition_no')]]['job_no']; ?></p></td>
									<td width="100"><p><? echo $row[csf('grouping')]; ?></p></td>
									<td width="100" align="center"><p><? echo $req_data_arr[$row[csf('requisition_no')]]['booking_no_prefix_num']; ?></p></td>
									<td width="150"><p><? echo $supplier_arr[$row[csf('knit_dye_company')]]; ?></p></td>
									<td width="60" align="center"><p><? echo $count_arr[$product_data_arr[$row[csf('prod_id')]]['yarn_count_id']]; ?></p></td>
									<td width="120"><p><? if($composition_id!="")  echo $composition[$composition_id]." ".$product_data_arr[$row[csf('prod_id')]]['yarn_comp_percent1st']."%"; ?></p></td>
									<td width="100"><p><? echo $yarn_type[$product_data_arr[$row[csf('prod_id')]]['yarn_type']]; ?></p></td>
									<td width="80" align="right" ><p><? echo $row[csf('cons_rate')]; ?></p></td>
									<td width="100" align="center"><p><? echo $color_arr[$product_data_arr[$row[csf('prod_id')]]['color']]; ?></p></td>
									<td width="80" align="right" ><a href="##" onclick="openmypage('<? echo $row[csf("po_breakdown_id")]; ?>','yarn_issue','<? echo $row[csf("trans_id")]; ?>','<? echo $row[csf("prod_id")]; ?>')" ><? echo number_format($row[csf('quantity')],0); ?></a></td>
									<td width="80" align="right"><p><? echo number_format($return_qty,0); ?></p></td>
									<td width="80" align="right"><p><? $net_issue_subcon = $row[csf('quantity')] - $return_qty; echo number_format($net_issue_subcon,0); ?></p></td>
									<td width="80" align="right" title="Yarn Rate*Net Issue"><p><? $net_issue_subcon_val = $net_issue_subcon*$row[csf('cons_rate')]; echo number_format($net_issue_subcon_val,2); ?></p></td>
									<td width="160"><p><? echo $row[csf('remarks')]; ?></p></td>
								</tr>
								<?
								$j++;
								$issue_qnty_subcon += $row[csf('quantity')];
								$return_qnty_subcon += $return_qty;
								$tot_net_issue_subcon += $net_issue_subcon;
								$tot_net_issue_subcon_val += $net_issue_subcon_val;
							}
							$i++;
						}
						$gt_qnty = $issue_qnty_inhouse+$issue_qnty_subcon;
						$gt_return_qnty = $tot_return_qnty+$return_qnty_subcon;
						$gt_net_issue_qnty = $tot_net_issue+$tot_net_issue_subcon;
						$gt_net_issue_subcon_val += $net_issue_subcon_val;
						?>
	            	</tbody>

	            </table>
	        </div>
	        <table width="<?php echo $tblWidth; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
	            <tfoot>
	                <th width="30">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="120">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="150">&nbsp;</th>
	                <th width="60">&nbsp;</th>
	                <th width="120">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="80">&nbsp;</th>
	                <th width="100">Sub Total:</th>
	                <th width="80"><? echo number_format($issue_qnty_subcon,0); ?></th>
	                <th width="80"><? echo number_format($return_qnty_subcon,0); ?></th>
	                <th width="80"><? echo number_format($tot_net_issue_subcon,0); ?></th>
	                <th width="80"><? echo number_format($tot_net_issue_subcon_val,2); ?></th>
	                <th width="160"></th>
	            </tfoot>
	        </table>
	        <table width="<?php echo $tblWidth; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
	            <tfoot>
	                <th width="30">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="120">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="150">&nbsp;</th>
	                <th width="60">&nbsp;</th>
	                <th width="120">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="80">&nbsp;</th>
	                <th width="100">Grand Total:</th>
	                <th width="80"><? echo number_format($gt_qnty,0); ?></th>
	                <th width="80"><? echo number_format($gt_return_qnty,0); ?></th>
	                <th width="80"><? echo number_format($gt_net_issue_qnty,0); ?></th>
					<th width="80"><? echo number_format($gt_net_issue_subcon_val,2); ?></th>
	                <th width="160"></th>
	            </tfoot>
	        </table>
	    </fieldset>
	    </div>
	    <?
	}
	elseif ($type==2)
	{
		$buyer_cond=$booking_cond="";
		if($cbo_buyer_name!=0) $buyer_cond=" and a.buyer_id=$cbo_buyer_name";
		//if($txt_cpa_booking_no!="") $booking_cond=" and a.booking_no like '%$txt_cpa_booking_no'";
		if($txt_cpa_booking_no!="") $booking_cond=" and a.booking_no_prefix_num in(".$txt_cpa_booking_no.")";
		$sql_date_cond	= "";
		if ($db_type == 0) {
			if ($txt_date_from != "" && $txt_date_to != "")
				$sql_date_cond .= " and a.issue_date  between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
				$sql_date_cond1 .= " and a.receive_date  between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
		}
		else {
			if ($txt_date_from != "" && $txt_date_to != "")
				$sql_date_cond .= " and a.issue_date between '" . date("j-M-Y", strtotime($txt_date_from)) . "' and '" . date("j-M-Y", strtotime($txt_date_to)) . "'";
				$sql_date_cond1 .= " and a.receive_date between '" . date("j-M-Y", strtotime($txt_date_from)) . "' and '" . date("j-M-Y", strtotime($txt_date_to)) . "'";
		}

		$dataArray_booking=sql_select( "SELECT a.id, a.booking_no, a.booking_no_prefix_num, a.job_no, a.buyer_id from wo_booking_mst a where a.booking_type=1 and a.is_short=1 and a.company_id=$cbo_company_name $buyer_cond $booking_cond" );//a.booking_type=1 and a.is_short=1 and
		$booking_data_arr=$boking_check=array();
		foreach($dataArray_booking as $row)
		{
			$boking_check[$row[csf('id')]]=$row[csf('id')];
			$booking_data_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
			$booking_data_arr[$row[csf('id')]]['booking_no_prefix_num']=$row[csf('booking_no_prefix_num')];
			$booking_data_arr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$booking_data_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_id')];
		}
		$bookingId = implode(",", $boking_check);
		// print_r($boking_check);


		$dataArray_requisition=sql_select("SELECT a.id, a.booking_no, a.booking_no_prefix_num, a.job_no, a.buyer_id, d.requisition_no from wo_booking_mst a, ppl_planning_info_entry_mst b, ppl_planning_info_entry_dtls c, ppl_yarn_requisition_entry d where a.booking_no=b.booking_no and a.booking_type=1 and a.is_short=1 and b.id=c.mst_id and c.id=d.knit_id  and a.company_id=$cbo_company_name $buyer_cond $booking_cond group by d.requisition_no, a.id, a.booking_no, a.booking_no_prefix_num, a.job_no, a.buyer_id");//and a.booking_type=1 and a.is_short=1
		$req_data_arr=$req_check=array();
		foreach($dataArray_requisition as $row)
		{
			$req_check[$row[csf('requisition_no')]]=$row[csf('requisition_no')];
			$req_data_arr[$row[csf('requisition_no')]]['booking_no']=$row[csf('booking_no')];
			$req_data_arr[$row[csf('requisition_no')]]['booking_no_prefix_num']=$row[csf('booking_no_prefix_num')];
			$req_data_arr[$row[csf('requisition_no')]]['job_no']=$row[csf('job_no')];
			$req_data_arr[$row[csf('requisition_no')]]['buyer']=$row[csf('buyer_id')];
		}
		if(count($dataArray_booking)==0 && count($dataArray_requisition)==0)
		{
			echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">Data not found!.</div>';
			die();
		}
		// print_r($req_check);
		$reqId = implode(",", $req_check);
		//$bookingIdCond = ($bookingId !="") ? " and a.booking_id in($bookingId)" : '';
		//$reqIdCond = ($reqId !="") ? " and b.requisition_no in($reqId)" : '';
		$bo_id_list_arr=array_chunk($boking_check,999);
		$bookingIdCond = " and ";
		$p=1;
		foreach($bo_id_list_arr as $bookingIdids)
	    {
	    	if($p==1)
			{
				$bookingIdCond .="  ( a.booking_id in(".implode(',',$bookingIdids).")";
			}
	        else
	        {
	          $bookingIdCond .=" or a.booking_id in(".implode(',',$bookingIdids).")";
	      	}
	        $p++;
	    }
	    $bookingIdCond .=")";

		$req_id_list_arr=array_chunk($req_check,999);
		$reqIdCond = " and ";
		$p=1;
		foreach($req_id_list_arr as $reqIdids)
	    {
	    	if($p==1)
			{
				$reqIdCond .="  ( b.requisition_no in(".implode(',',$reqIdids).")";
			}
	        else
	        {
	          $reqIdCond .=" or b.requisition_no in(".implode(',',$reqIdids).")";
	      	}
	        $p++;
	    }
	    $reqIdCond .=")";

		$product_data=sql_select("SELECT id, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_type, color from product_details_master where status_active=1 and item_category_id=1");
		$product_data_arr=array();
		foreach($product_data as $val)
		{
			$product_data_arr[$val[csf("id")]]['prod_id']=$val[csf("id")];
			$product_data_arr[$val[csf("id")]]['yarn_count_id']=$val[csf("yarn_count_id")];
			$product_data_arr[$val[csf("id")]]['yarn_comp_type1st']=$val[csf("yarn_comp_type1st")];
			$product_data_arr[$val[csf("id")]]['yarn_comp_percent1st']=$val[csf("yarn_comp_percent1st")];
			$product_data_arr[$val[csf("id")]]['yarn_type']=$val[csf("yarn_type")];
			$product_data_arr[$val[csf("id")]]['color']=$val[csf("color")];
		}
		ob_start();
		?>
	    <div style="width:1320px" align="left">
	    <fieldset style="width:100%;">
	        <table cellpadding="0" cellspacing="0" width="1300">
	            <tr>
	               <td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
	            </tr>
	            <tr>
	               <td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $company_arr[$cbo_company_name]; ?></strong></td>
	            </tr>
	        </table>
	        <!-- ======================================== INHOUSE START =====================================  -->
	        <div style="font-size:16px; font-weight:bold">In-house</div>
	        <table width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
	            <thead>
	                <th width="30">SL</th>
	                <th width="100">Buyer Name</th>
	                <th width="120">Order No</th>
	                <th width="100">Job No</th>
	                <th width="100">CPA NO</th>
	                <th width="150">Issue Party Name</th>
	                <th width="60">Yarn Count</th>
	                <th width="120">Composition</th>
	                <th width="100">Yarn Type</th>
	                <th width="100">Colour</th>
	                <th width="80">Yarn Issued Qty</th>
	                <th width="80">Return Qty</th>
	                <th>Remarks</th>
	            </thead>
	        </table>
	        <div style="width:1320px; overflow-y: scroll; max-height:280px;" id="scroll_body">
				<table width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="tbl_body">
	                <tbody>
	                <?
						 $sql_inside="SELECT a.id as mst_id, a.buyer_id, a.booking_id, a.knit_dye_source, a.knit_dye_company, a.remarks, b.id as trans_id, b.requisition_no, c.po_breakdown_id, c.prod_id, c.quantity,b.receive_basis from inv_issue_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name $sql_date_cond and b.item_category=1 and b.transaction_type=2 and a.issue_basis in(1,3) and a.issue_purpose=1 and b.receive_basis in(1,3) and a.knit_dye_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  order by a.knit_dye_source,a.issue_number, a.issue_date";//$bookingIdCond $reqIdCond// and a.issue_date between '$txt_date_from' and '$txt_date_to'
						// echo $sql_inside;
						$result=sql_select($sql_inside);
						$data_array = array();
						foreach ($result as $val)
						{
							// $prod_id_arr[$val['PROD_ID']] = $val['PROD_ID'];
							if($val[csf("booking_id")] !="" && $val[csf("receive_basis")]==1)
							{
								if(isset($boking_check[$val[csf("booking_id")]]))
								{
								$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['issue_qty'] += $val[csf('quantity')];
								$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['knit_dye_company'] = $val[csf('knit_dye_company')];
								$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['trans_id'] = $val[csf('trans_id')];
								$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['remarks'] = $val[csf('remarks')];
								$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['buyer'] = $buyer_arr[$booking_data_arr[$val[csf('booking_id')]]['buyer']];
								$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['job_no'] = $booking_data_arr[$val[csf('booking_id')]]['job_no'];
								$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['booking_no'] = $booking_data_arr[$val[csf('booking_id')]]['booking_no'];
								}
							}
							else if($val[csf("requisition_no")] !="" && $val[csf("receive_basis")]==3)
							{
								if(isset($req_check[$val[csf("requisition_no")]]))
								{
								$data_array[$val[csf("requisition_no")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['issue_qty'] += $val[csf('quantity')];
								$data_array[$val[csf("requisition_no")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['knit_dye_company'] = $val[csf('knit_dye_company')];
								$data_array[$val[csf("requisition_no")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['trans_id'] = $val[csf('trans_id')];
								$data_array[$val[csf("requisition_no")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['remarks'] = $val[csf('remarks')];
								$data_array[$val[csf("requisition_no")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['buyer'] = $buyer_arr[$req_data_arr[$val[csf('requisition_no')]]['buyer']];
								$data_array[$val[csf("requisition_no")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['job_no'] = $req_data_arr[$val[csf('requisition_no')]]['job_no'];
								$data_array[$val[csf("requisition_no")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['booking_no'] = $req_data_arr[$val[csf('requisition_no')]]['booking_no'];
								}
							}
						}
						// print_r($data_array);die();
						// $prodId = implode(",", $prod_id_arr);
						//================================ issue return =======================================
						$sql_return = "SELECT a.id as mst_id,a.buyer_id, a.knitting_company as knit_dye_company, a.remarks, b.issue_id, c.po_breakdown_id as po_id, c.prod_id,a.booking_id,c.trans_id,a.receive_basis, b.requisition_no, c.quantity as return_qty from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.item_category=1 and a.entry_form=9 and a.company_id=$cbo_company_name $sql_date_cond1 and b.item_category=1 and b.transaction_type=4 and a.receive_basis in(1,3) and b.receive_basis in(1,3) and a.knitting_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ";//and a.receive_date between '$txt_date_from' and '$txt_date_to'  and b.prod_id in($prodId)
						// echo $sql_return;
						$sql_return_res = sql_select($sql_return);
						$return_qty_arr = array();
						foreach ($sql_return_res as $row)
						{
							if($row[csf("receive_basis")] ==3 && $row[csf("requisition_no")] !="")
							{
								if(isset($req_check[$row[csf("requisition_no")]]) || isset($req_check[$row[csf("booking_id")]]))
								{
									$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['return_qty']+=$row[csf("return_qty")];
									$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['knit_dye_company'] = $row[csf('knit_dye_company')];
									$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['trans_id2'] .= $row[csf('trans_id')].",";
									$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['remarks'] = $row[csf('remarks')];
									$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['buyer'] = $buyer_arr[$req_data_arr[$row[csf('booking_id')]]['buyer']];
									$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['job_no'] = $req_data_arr[$row[csf('booking_id')]]['job_no'];
									$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['booking_no'] = $req_data_arr[$row[csf('booking_id')]]['booking_no'];
								}
							}
							else if($row[csf("receive_basis")] ==1 && $row[csf("booking_id")] !="")
							{
								if(isset($boking_check[$row[csf("booking_id")]]) || isset($req_check[$row[csf("requisition_no")]]))
								{
									$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['return_qty']+=$row[csf("return_qty")];
									$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['knit_dye_company'] = $row[csf('knit_dye_company')];
									$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['trans_id2'] .= $row[csf('trans_id')].",";
									$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['remarks'] = $row[csf('remarks')];
									$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['buyer'] = $buyer_arr[$booking_data_arr[$row[csf('booking_id')]]['buyer']];
									$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['job_no'] = $booking_data_arr[$row[csf('booking_id')]]['job_no'];
									$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['booking_no'] = $booking_data_arr[$row[csf('booking_id')]]['booking_no'];
								}
							}
						}
						// echo "<pre>";
					 //   	print_r($data_array);die();

						$i=1;$issue_qnty_inhouse=$tot_return_qnty=$tot_net_issue=$gt_qnty=$gt_return_qnty=0;
						foreach ($data_array as $booking_id => $bookingData)
						{
							foreach ($bookingData as $issue_id => $issueData)
							{
								foreach ($issueData as $po_id => $poData)
								{
									foreach ($poData as $prod_id => $row)
									{
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										$composition_id="";
										$composition_id = $product_data_arr[$prod_id]['yarn_comp_type1st'];
										?>
											<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
												<td width="30"><? echo $i; ?></td>
												<td width="100" ><p><? echo $row['buyer']; ?></p></td>
												<td width="120"><p><? echo $order_arr[$po_id]; ?></p></td>
												<td width="100"><p><? echo $row['job_no']; ?></p></td>

												<td width="100"><p><? echo $row['booking_no']; ?></p></td>

												<td width="150"><p><? echo $company_arr[$row['knit_dye_company']]; ?></p></td>
												<td width="60" align="center"><p><? echo $count_arr[$product_data_arr[$prod_id]['yarn_count_id']]; ?></p></td>
												<td width="120" ><p><? if($composition_id!="")  echo $composition[$composition_id]." ".$product_data_arr[$prod_id]['yarn_comp_percent1st']."%"; ?></p></td>
												<td width="100"><p><? echo $yarn_type[$product_data_arr[$prod_id]['yarn_type']]; ?></p></td>
												<td width="100" align="center"><p><? echo $color_arr[$product_data_arr[$prod_id]['color']]; ?></p></td>
												<td width="80" align="right"><a href="##" onclick="openmypage('<? echo $po_id; ?>','yarn_issue2','<? echo $row["trans_id"]; ?>','<? echo $prod_id; ?>')"><? echo number_format($row['issue_qty'],2); ?></a></td>

												<td width="80" align="right" title="<? echo $issue_id."_".$po_id."_".$prod_id;?>">
													<a href="##" onclick="openmypage('<? echo $po_id; ?>','yarn_issue_return2','<? echo chop($row["trans_id2"],','); ?>','<? echo $prod_id; ?>')"><? echo number_format($row['return_qty'],2); ?>

													</a>
												</td>

												<td ><p><? echo $row['remarks']; ?></p></td>
											</tr>
										<?
										$i++;
										$issue_qnty_inhouse+=	$row['issue_qty'];
										$tot_return_qnty+=	$row['return_qty'];
										$gt_qnty +=	$row['issue_qty'];
										$gt_return_qnty += $row['return_qty'];
									}
								}
							}
						}
						?>
	            	</tbody>
	            </table>
	        </div>
	        <table width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
	            <tfoot>
	                <th width="30">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="120">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="150">&nbsp;</th>
	                <th width="60">&nbsp;</th>
	                <th width="120">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="100">Sub Total:</th>
	                <th width="80" align="right"><? echo number_format($issue_qnty_inhouse,2); ?></th>
	                <th width="80" align="right"><? echo number_format($tot_return_qnty,2); ?></th>
	                <th>&nbsp;</th>
	            </tfoot>
	        </table>
	        <br />

	        <!-- ======================================== SUBCONTACT START =====================================  -->
	        <?
			$sql_subcon="SELECT a.id as mst_id, a.buyer_id,a.buyer_job_no,a.booking_no, a.booking_id, a.knit_dye_source, a.knit_dye_company, a.remarks, b.id as trans_id, b.requisition_no, c.po_breakdown_id as po_id, c.prod_id, c.quantity,b.receive_basis from inv_issue_master a, inv_transaction b, order_wise_pro_details c
			where a.id=b.mst_id and b.id=c.trans_id and a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name $sql_date_cond and b.item_category=1 and b.transaction_type=2 and a.issue_basis in(1,3) and a.issue_purpose=1 and b.receive_basis in(1,3) and a.knit_dye_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
			order by a.knit_dye_source,a.issue_number, a.issue_date";
			// echo $sql_subcon;die;// and a.issue_date between '$txt_date_from' and '$txt_date_to'
			$result_subcon=sql_select($sql_subcon);
			$subcon_data_array = array();
			foreach ($result_subcon as $row)
			{
				$subcon_data_array[$row[csf("mst_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['issue_qty']+=$row[csf("quantity")];
				$subcon_data_array[$row[csf("mst_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['buyer_id']=$row[csf("buyer_id")];
				$subcon_data_array[$row[csf("mst_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['buyer_job_no']=$row[csf("buyer_job_no")];
				$subcon_data_array[$row[csf("mst_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['booking_no']=$row[csf("booking_no")];
				$subcon_data_array[$row[csf("mst_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['knit_dye_company']=$row[csf("knit_dye_company")];
				$subcon_data_array[$row[csf("mst_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['remarks']=$row[csf("remarks")];
				$subcon_data_array[$row[csf("mst_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['receive_basis']=$row[csf("receive_basis")];
				$subcon_data_array[$row[csf("mst_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['booking_id']=$row[csf("booking_id")];
				$subcon_data_array[$row[csf("mst_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['trans_id']=$row[csf("trans_id")];
				$subcon_data_array[$row[csf("mst_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['requisition_no']=$row[csf("requisition_no")];
			}
			// print_r($subcon_data_array);die();
			// ================================= return qty ======================================
			$sql_return_subcon = "SELECT a.id as mst_id, b.issue_id, c.po_breakdown_id as po_id, c.prod_id,a.receive_basis, c.quantity as return_qty,c.reject_qty from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.item_category=1 and a.entry_form=9 and a.company_id=$cbo_company_name $sql_date_cond1 and b.item_category=1 and b.transaction_type=4 and a.receive_basis in(1,3) and b.receive_basis in(1,3) and a.knitting_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ";//and a.receive_date between '$txt_date_from' and '$txt_date_to'
			// echo $sql_return_subcon;
			$return_subcon_res = sql_select($sql_return_subcon);
			$return_qty_arr_subcon = array();
			foreach ($return_subcon_res as $row)
			{
				$subcon_data_array[$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['return_qty']+=$row[csf("return_qty")] + $row[csf("reject_qty")];
				$subcon_data_array[$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['receive_basis']=$row[csf("receive_basis")];
			}
		    // echo "<pre>";print_r($subcon_data_array);
		   ?>
	        <div style="font-size:16px; font-weight:bold">Out-bound Subcontract</div>
	        <table width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
	            <thead>
	                <th width="30">SL</th>
	                <th width="100">Buyer Name</th>
	                <th width="120">Order No</th>
	                <th width="100">Job No</th>
	                <th width="100">CPA NO</th>
	                <th width="150">Issue Party Name</th>
	                <th width="60">Yarn Count</th>
	                <th width="120">Composition</th>
	                <th width="100">Yarn Type</th>
	                <th width="100">Colour</th>
	                <th width="80">Yarn Issued Qty</th>
	                <th width="80">Return Qty</th>
	                <th width="160">Remarks</th>
	            </thead>
	        </table>
	        <div style="width:1320px; overflow-y: scroll; max-height:280px;" id="scroll_body2">
				<table width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="tbl_body2">
	                <tbody>
	                	<?
						$j=1;
						$issue_qnty_subcon=0;
						$return_qnty_subcon=0;
						$tot_net_issue_subcon=0;

						foreach($subcon_data_array as $issue_id=>$issue_data)
						{
							foreach ($issue_data as $po_id => $po_data)
							{
								foreach ($po_data as $prod_id => $row)
								{
									if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			                    	$composition_id="";
			                    	// echo $boking_check[$row['booking_id']]."==".$row['booking_id']."<br>";
									if($row['booking_id']!="" && $row['receive_basis']==1)
									{
										if($boking_check[$row['booking_id']]!="")
										{
											$composition_id=$product_data_arr[$prod_id]['yarn_comp_type1st'];
											// $return_qty = $return_qty_arr_subcon[$row[csf("mst_id")]][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]['return_qty'];
											?>
											<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
												<td width="30"><? echo $j; ?></td>
												<td width="100"><p><? echo $buyer_arr[$row['buyer_id']];//$buyer_arr[$booking_data_arr[$row[csf('booking_id')]]['buyer']]; ?></p></td>
												<td width="120"><p><? echo $order_arr[$po_id]; ?></p></td>
												<td width="100" title="Booking ID=<? echo $row['booking_id'];?>"><p><? echo $row['buyer_job_no'];//$booking_data_arr[$row[csf('booking_id')]]['job_no']; ?></p></td>
												<td width="100" align="center"><p><? echo $row['booking_no'];//$booking_data_arr[$row[csf('booking_id')]]['booking_no']; ?></p></td>
												<td width="150"><p><? echo $supplier_arr[$row['knit_dye_company']]; ?></p></td>
												<td width="60" align="center"><p><? echo $count_arr[$product_data_arr[$prod_id]['yarn_count_id']]; ?></p></td>
												<td width="120"><p><? if($composition_id!="")  echo $composition[$composition_id]." ".$product_data_arr[$prod_id]['yarn_comp_percent1st']."%"; ?></p></td>
												<td width="100"><p><? echo $yarn_type[$product_data_arr[$prod_id]['yarn_type']]; ?></p></td>
												<td width="100" align="center"><p><? echo $color_arr[$product_data_arr[$prod_id]['color']]; ?></p></td>
												<td width="80" align="right"><a href="##" onclick="openmypage('<? echo $po_id; ?>','yarn_issue','<? echo $row["trans_id"]; ?>','<? echo $prod_id; ?>')"><? echo number_format($row['issue_qty'],0); ?></a></td>
												<td width="80" align="right"><p><? echo number_format($row['return_qty'],0); ?></p></td>
												<td width="160"><p><? echo $row['remarks']; ?></p></td>
											</tr>
											<?
											$j++;$i++;
											$issue_qnty_subcon += $row['issue_qty'];
											$return_qnty_subcon += $row['return_qty'];
											$tot_net_issue_subcon += $net_issue_subcon;
										}

									} // echo $req_check[$row['requisition_no']]."==".$row['booking_id']."<br>";
									else if($row['requisition_no']!="" && $row['receive_basis']==3)
									{
										if($req_check[$row['requisition_no']]!="")
										{
											// $return_qty = $return_qty_arr_subcon[$row[csf("mst_id")]][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]['return_qty'];
											?>
											<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
												<td width="30"><? echo $j; ?></td>
												<td width="100" ><p><? echo $buyer_arr[$req_data_arr[$row['requisition_no']]['buyer']]; ?></p></td>
												<td width="120"><p><? echo $order_arr[$po_id]; ?></p></td>
												<td width="100"><p><? echo $req_data_arr[$row['requisition_no']]['job_no']; ?></p></td>
												<td width="100" align="center"><p><? echo $req_data_arr[$row['requisition_no']]['booking_no_prefix_num']; ?></p></td>
												<td width="150"><p><? echo $supplier_arr[$row['knit_dye_company']]; ?></p></td>
												<td width="60" align="center"><p><? echo $count_arr[$product_data_arr[$prod_id]['yarn_count_id']]; ?></p></td>
												<td width="120"><p><? if($composition_id!="")  echo $composition[$composition_id]." ".$product_data_arr[$prod_id]['yarn_comp_percent1st']."%"; ?></p></td>
												<td width="100"><p><? echo $yarn_type[$product_data_arr[$prod_id]['yarn_type']]; ?></p></td>
												<td width="100" align="center"><p><? echo $color_arr[$product_data_arr[$prod_id]['color']]; ?></p></td>
												<td width="80" align="right"><a href="##" onclick="openmypage('<? echo $po_id; ?>','yarn_issue','<? echo $row["trans_id"]; ?>','<? echo $prod_id; ?>')"><? echo number_format($row['issue_qty'],0); ?></a></td>
												<td width="80" align="right"><p><? echo number_format($row['return_qty'],0); ?></p></td>
												<td width="160"><p><? echo $row['remarks']; ?></p></td>
											</tr>
											<?
											$j++;
											$i++;
											$issue_qnty_subcon += $row['issue_qty'];
											$return_qnty_subcon += $row['return_qty'];
											$tot_net_issue_subcon += $net_issue_subcon;
										}
									}
									$gt_qnty =	$issue_qnty_subcon;
									$gt_return_qnty = $return_qnty_subcon;
								}
							}
						}
						?>
	            	</tbody>

	            </table>
	        </div>
	        <table width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
	            <tfoot>
	                <th width="30">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="120">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="150">&nbsp;</th>
	                <th width="60">&nbsp;</th>
	                <th width="120">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="100">Sub Total:</th>
	                <th width="80"><? echo number_format($issue_qnty_subcon,0); ?></th>
	                <th width="80"><? echo number_format($return_qnty_subcon,0); ?></th>
	                <th width="160"></th>
	            </tfoot>
	        </table>
	        <table width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
	            <tfoot>
	                <th width="30">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="120">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="150">&nbsp;</th>
	                <th width="60">&nbsp;</th>
	                <th width="120">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="100">Grand Total:</th>
	                <th width="80"><? echo number_format($gt_qnty,0); ?></th>
	                <th width="80"><? echo number_format($gt_return_qnty,0); ?></th>
	                <th width="160"></th>
	            </tfoot>
	        </table>
	    </fieldset>
	    </div>
		<?
	}
	elseif ($type==22) // 06-10-2020
	{

		$buyer_cond=$booking_cond="";
		if($cbo_buyer_name!=0) $buyer_cond=" and a.buyer_id=$cbo_buyer_name";
		// if($txt_cpa_booking_no!="") $booking_cond=" and a.booking_id in(".$txt_cpa_booking_no.")";
		if($txt_cpa_booking_no!="") $booking_cond=" and a.booking_no like '%$txt_cpa_booking_no'";


		$dataArray_booking=sql_select( "select a.id, a.booking_no, a.booking_no_prefix_num, a.job_no, a.buyer_id from wo_booking_mst a where a.booking_type=1 and a.is_short=1 and a.company_id=$cbo_company_name $buyer_cond $booking_cond" );//a.booking_type=1 and a.is_short=1 and
		$booking_data_arr=$boking_check=array();
		foreach($dataArray_booking as $row)
		{
			$boking_check[$row[csf('id')]]=$row[csf('id')];
			$booking_data_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
			$booking_data_arr[$row[csf('id')]]['booking_no_prefix_num']=$row[csf('booking_no_prefix_num')];
			$booking_data_arr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$booking_data_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_id')];
		}
		$bookingId = implode(",", $boking_check);
		// print_r($boking_check);
		$dataArray_requisition=sql_select("SELECT a.id, a.booking_no, a.booking_no_prefix_num, a.job_no, a.buyer_id, d.requisition_no from wo_booking_mst a, ppl_planning_info_entry_mst b, ppl_planning_info_entry_dtls c, ppl_yarn_requisition_entry d where a.booking_no=b.booking_no and a.booking_type=1 and a.is_short=1 and b.id=c.mst_id and c.id=d.knit_id  and a.company_id=$cbo_company_name $buyer_cond $booking_cond group by d.requisition_no, a.id, a.booking_no, a.booking_no_prefix_num, a.job_no, a.buyer_id");//and a.booking_type=1 and a.is_short=1
		$req_data_arr=$req_check=array();
		foreach($dataArray_requisition as $row)
		{
			$req_check[$row[csf('requisition_no')]]=$row[csf('requisition_no')];
			$req_data_arr[$row[csf('requisition_no')]]['booking_no']=$row[csf('booking_no')];
			$req_data_arr[$row[csf('requisition_no')]]['booking_no_prefix_num']=$row[csf('booking_no_prefix_num')];
			$req_data_arr[$row[csf('requisition_no')]]['job_no']=$row[csf('job_no')];
			$req_data_arr[$row[csf('requisition_no')]]['buyer']=$row[csf('buyer_id')];
		}
		if(count($dataArray_booking)==0 && count($dataArray_requisition)==0)
		{
			echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">Data not found!.</div>';
			die();
		}
		// print_r($req_check);
		$reqId = implode(",", $req_check);
		//$bookingIdCond = ($bookingId !="") ? " and a.booking_id in($bookingId)" : '';
		//$reqIdCond = ($reqId !="") ? " and b.requisition_no in($reqId)" : '';
		$bo_id_list_arr=array_chunk($boking_check,999);
		$bookingIdCond = " and ";
		$p=1;
		foreach($bo_id_list_arr as $bookingIdids)
	    {
	    	if($p==1)
			{
				$bookingIdCond .="  ( a.booking_id in(".implode(',',$bookingIdids).")";
			}
	        else
	        {
	          $bookingIdCond .=" or a.booking_id in(".implode(',',$bookingIdids).")";
	      	}
	        $p++;
	    }
	    $bookingIdCond .=")";

		$req_id_list_arr=array_chunk($req_check,999);
		$reqIdCond = " and ";
		$p=1;
		foreach($req_id_list_arr as $reqIdids)
	    {
	    	if($p==1)
			{
				$reqIdCond .="  ( b.requisition_no in(".implode(',',$reqIdids).")";
			}
	        else
	        {
	          $reqIdCond .=" or b.requisition_no in(".implode(',',$reqIdids).")";
	      	}
	        $p++;
	    }
	    $reqIdCond .=")";

		$product_data=sql_select("SELECT id, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_type, color from product_details_master where status_active=1 and item_category_id=1");
		$product_data_arr=array();
		foreach($product_data as $val)
		{
			$product_data_arr[$val[csf("id")]]['prod_id']=$val[csf("id")];
			$product_data_arr[$val[csf("id")]]['yarn_count_id']=$val[csf("yarn_count_id")];
			$product_data_arr[$val[csf("id")]]['yarn_comp_type1st']=$val[csf("yarn_comp_type1st")];
			$product_data_arr[$val[csf("id")]]['yarn_comp_percent1st']=$val[csf("yarn_comp_percent1st")];
			$product_data_arr[$val[csf("id")]]['yarn_type']=$val[csf("yarn_type")];
			$product_data_arr[$val[csf("id")]]['color']=$val[csf("color")];
		}


		ob_start();
		?>
	    <div style="width:1320px" align="left">
	    <fieldset style="width:100%;">
	        <table cellpadding="0" cellspacing="0" width="1300">
	            <tr>
	               <td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
	            </tr>
	            <tr>
	               <td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $company_arr[$cbo_company_name]; ?></strong></td>
	            </tr>
	        </table>
	        <div style="font-size:16px; font-weight:bold">In-house</div>
	        <table width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
	            <thead>
	                <th width="30">SL</th>
	                <th width="100">Buyer Name</th>
	                <th width="120">Order No</th>
	                <th width="100">Job No</th>
	                <th width="100">CPA NO</th>
	                <th width="150">Issue Party Name</th>
	                <th width="60">Yarn Count</th>
	                <th width="120">Composition</th>
	                <th width="100">Yarn Type</th>
	                <th width="100">Colour</th>
	                <th width="80">Yarn Issued Qty</th>
	                <th width="80">Return Qty</th>
	                <th>Remarks</th>
	            </thead>
	        </table>
	        <div style="width:1320px; overflow-y: scroll; max-height:280px;" id="scroll_body">
				<table width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="tbl_body">
	                <tbody>
	                <?
						 $sql_inside="SELECT a.id as mst_id, a.buyer_id, a.booking_id, a.knit_dye_source, a.knit_dye_company, a.remarks, b.id as trans_id, b.requisition_no, c.po_breakdown_id, c.prod_id, c.quantity,b.receive_basis from inv_issue_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name and a.issue_date between '$txt_date_from' and '$txt_date_to' and b.item_category=1 and b.transaction_type=2 and a.issue_basis in(1,3) and a.issue_purpose=1 and b.receive_basis in(1,3) and a.knit_dye_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  order by a.knit_dye_source,a.issue_number, a.issue_date";//$bookingIdCond $reqIdCond
						// echo $sql_inside;
						$result=sql_select($sql_inside);
						$data_array = array();
						foreach ($result as $val)
						{
							// $prod_id_arr[$val['PROD_ID']] = $val['PROD_ID'];
							if($val[csf("booking_id")] !="" && $val[csf("receive_basis")]==1)
							{
								if(isset($boking_check[$val[csf("booking_id")]]))
								{
								$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['issue_qty'] += $val[csf('quantity')];
								$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['knit_dye_company'] = $val[csf('knit_dye_company')];
								$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['trans_id'] = $val[csf('trans_id')];
								$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['remarks'] = $val[csf('remarks')];
								$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['buyer'] = $buyer_arr[$booking_data_arr[$val[csf('booking_id')]]['buyer']];
								$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['job_no'] = $booking_data_arr[$val[csf('booking_id')]]['job_no'];
								$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['booking_no'] = $booking_data_arr[$val[csf('booking_id')]]['booking_no'];
								}
							}
							else if($val[csf("requisition_no")] !="" && $val[csf("receive_basis")]==3)
							{
								if(isset($req_check[$val[csf("requisition_no")]]))
								{
								$data_array[$val[csf("requisition_no")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['issue_qty'] += $val[csf('quantity')];
								$data_array[$val[csf("requisition_no")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['knit_dye_company'] = $val[csf('knit_dye_company')];
								$data_array[$val[csf("requisition_no")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['trans_id'] = $val[csf('trans_id')];
								$data_array[$val[csf("requisition_no")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['remarks'] = $val[csf('remarks')];
								$data_array[$val[csf("requisition_no")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['buyer'] = $buyer_arr[$req_data_arr[$val[csf('requisition_no')]]['buyer']];
								$data_array[$val[csf("requisition_no")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['job_no'] = $req_data_arr[$val[csf('requisition_no')]]['job_no'];
								$data_array[$val[csf("requisition_no")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['booking_no'] = $req_data_arr[$val[csf('requisition_no')]]['booking_no'];
								}
							}
						}
						// print_r($data_array);die();
						// $prodId = implode(",", $prod_id_arr);
						//================================ issue return =======================================
						$sql_return = "SELECT a.id as mst_id,a.buyer_id, a.knitting_company as knit_dye_company, a.remarks, b.issue_id, c.po_breakdown_id as po_id, c.prod_id,a.booking_id,c.trans_id,a.receive_basis, b.requisition_no, c.quantity as return_qty from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.item_category=1 and a.entry_form=9 and a.company_id=$cbo_company_name  and b.item_category=1 and b.transaction_type=4 and a.receive_basis in(1,3) and b.receive_basis in(1,3) and a.knitting_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.receive_date between '$txt_date_from' and '$txt_date_to'";//and a.receive_date between '$txt_date_from' and '$txt_date_to'  and b.prod_id in($prodId)
						// echo $sql_return;
						$sql_return_res = sql_select($sql_return);
						$return_qty_arr = array();
						foreach ($sql_return_res as $row)
						{
							if($row[csf("receive_basis")] ==3 && $row[csf("requisition_no")] !="")
							{
							if(isset($req_check[$row[csf("requisition_no")]]) || isset($req_check[$row[csf("booking_id")]]))
							{
								$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['return_qty']+=$row[csf("return_qty")];
								$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['knit_dye_company'] = $row[csf('knit_dye_company')];
								$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['trans_id2'] .= $row[csf('trans_id')].",";
								$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['remarks'] = $row[csf('remarks')];
								$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['buyer'] = $buyer_arr[$req_data_arr[$row[csf('booking_id')]]['buyer']];
								$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['job_no'] = $req_data_arr[$row[csf('booking_id')]]['job_no'];
								$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['booking_no'] = $req_data_arr[$row[csf('booking_id')]]['booking_no'];
							}
							}
							else if($row[csf("receive_basis")] ==1 && $row[csf("booking_id")] !="")
							{
								if(isset($boking_check[$row[csf("booking_id")]]) || isset($req_check[$row[csf("requisition_no")]]))
								{
								$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['return_qty']+=$row[csf("return_qty")];
								$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['knit_dye_company'] = $row[csf('knit_dye_company')];
								$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['trans_id2'] .= $row[csf('trans_id')].",";
								$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['remarks'] = $row[csf('remarks')];
								$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['buyer'] = $buyer_arr[$booking_data_arr[$row[csf('booking_id')]]['buyer']];
								$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['job_no'] = $booking_data_arr[$row[csf('booking_id')]]['job_no'];
								$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['booking_no'] = $booking_data_arr[$row[csf('booking_id')]]['booking_no'];
								}
							}
						}
						// echo "<pre>";
					 //   	print_r($data_array);die();

						$i=1;$issue_qnty_inhouse=0;$tot_return_qnty=0;$tot_net_issue=0;
						foreach ($data_array as $booking_id => $bookingData)
						{
							foreach ($bookingData as $issue_id => $issueData)
							{
								foreach ($issueData as $po_id => $poData)
								{
									foreach ($poData as $prod_id => $row)
									{
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										$composition_id="";
										$composition_id = $product_data_arr[$prod_id]['yarn_comp_type1st'];
										?>
											<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
												<td width="30"><? echo $i; ?></td>
												<td width="100" ><p><? echo $row['buyer']; ?></p></td>
												<td width="120"><p><? echo $order_arr[$po_id]; ?></p></td>
												<td width="100"><p><? echo $row['job_no']; ?></p></td>

												<td width="100"><p><? echo $row['booking_no']; ?></p></td>

												<td width="150"><p><? echo $company_arr[$row['knit_dye_company']]; ?></p></td>
												<td width="60" align="center"><p><? echo $count_arr[$product_data_arr[$prod_id]['yarn_count_id']]; ?></p></td>
												<td width="120" ><p><? if($composition_id!="")  echo $composition[$composition_id]." ".$product_data_arr[$prod_id]['yarn_comp_percent1st']."%"; ?></p></td>
												<td width="100"><p><? echo $yarn_type[$product_data_arr[$prod_id]['yarn_type']]; ?></p></td>
												<td width="100" align="center"><p><? echo $color_arr[$product_data_arr[$prod_id]['color']]; ?></p></td>
												<td width="80" align="right"><a href="##" onclick="openmypage('<? echo $po_id; ?>','yarn_issue2','<? echo $row["trans_id"]; ?>','<? echo $prod_id; ?>')"><? echo number_format($row['issue_qty'],2); ?></a></td>

												<td width="80" align="right" title="<? echo $issue_id."_".$po_id."_".$prod_id;?>">
													<a href="##" onclick="openmypage('<? echo $po_id; ?>','yarn_issue_return2','<? echo chop($row["trans_id2"],','); ?>','<? echo $prod_id; ?>')"><? echo number_format($row['return_qty'],2); ?>

													</a>
												</td>

												<td ><p><? echo $row['remarks']; ?></p></td>
											</tr>
										<?
										$i++;
										$issue_qnty_inhouse+=	$row['issue_qty'];
										$tot_return_qnty+=	$row['return_qty'];
									}
								}
							}
						}
						?>
	            	</tbody>
	            </table>
	        </div>
	        <table width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
	            <tfoot>
	                <th width="30">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="120">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="150">&nbsp;</th>
	                <th width="60">&nbsp;</th>
	                <th width="120">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="100">Sub Total:</th>
	                <th width="80" align="right"><? echo number_format($issue_qnty_inhouse,2); ?></th>
	                <th width="80" align="right"><? echo number_format($tot_return_qnty,2); ?></th>
	                <th>&nbsp;</th>
	            </tfoot>
	        </table>
	        <br />
	        <!-- ======================================== subcontact start =====================================  -->


	        <!-- ======================================== SUBCONTACT START =====================================  -->
	        <div style="font-size:16px; font-weight:bold">Out-bound Subcontract</div>
	        <table width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
	            <thead>
	                <th width="30">SL</th>
	                <th width="100">Buyer Name</th>
	                <th width="120">Order No</th>
	                <th width="100">Job No</th>
	                <th width="100">CPA NO</th>
	                <th width="150">Issue Party Name</th>
	                <th width="60">Yarn Count</th>
	                <th width="120">Composition</th>
	                <th width="100">Yarn Type</th>
	                <th width="100">Colour</th>
	                <th width="80">Yarn Issued Qty</th>
	                <th width="80">Return Qty</th>
	                <th width="160">Remarks</th>
	            </thead>
	        </table>
	        <div style="width:1320px; overflow-y: scroll; max-height:280px;" id="scroll_body2">
				<table width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="tbl_body2">
	                <tbody>
	                <?
	                    $sql_return_subcon = sql_select("SELECT a.id as mst_id, b.issue_id, c.po_breakdown_id as po_id, c.prod_id, c.quantity as return_qty from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.item_category=1 and a.entry_form=9 and a.company_id=$cbo_company_name  and b.item_category=1 and b.transaction_type=4 and a.receive_basis in(1,3) and b.receive_basis in(1,3) and a.knitting_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.receive_date between '$txt_date_from' and '$txt_date_to'");//and a.receive_date between '$txt_date_from' and '$txt_date_to'
						$return_qty_arr_subcon = array();
						foreach ($sql_return_subcon as $row)
						{
							$return_qty_arr_subcon[$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['return_qty']+=$row[csf("return_qty")];
						}
					   //print_r($return_qty_arr);

						$sql_subcon="SELECT a.id as mst_id, a.buyer_id,a.buyer_job_no,a.booking_no, a.booking_id, a.knit_dye_source, a.knit_dye_company, a.remarks, b.id as trans_id, b.requisition_no, c.po_breakdown_id, c.prod_id, c.quantity,b.receive_basis from inv_issue_master a, inv_transaction b, order_wise_pro_details c
						where a.id=b.mst_id and b.id=c.trans_id and a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name and a.issue_date between '$txt_date_from' and '$txt_date_to' and b.item_category=1 and b.transaction_type=2 and a.issue_basis in(1,3) and a.issue_purpose=1 and b.receive_basis in(1,3) and a.knit_dye_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
						order by a.knit_dye_source,a.issue_number, a.issue_date";
						// echo $sql_subcon;die;
						$result_subcon=sql_select($sql_subcon);
						$j=1;$issue_qnty_subcon=0;$return_qnty_subcon=0;$tot_net_issue_subcon=0;
						foreach($result_subcon as $row)
						{
							if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                    	$composition_id="";
							if($boking_check[$row[csf('booking_id')]]!="" && $row[csf('receive_basis')]==1)
							{
								$composition_id=$product_data_arr[$row[csf('prod_id')]]['yarn_comp_type1st'];
								$return_qty = $return_qty_arr_subcon[$row[csf("mst_id")]][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]['return_qty'];
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="30"><? echo $j; ?></td>
									<td width="100"><p><? echo $buyer_arr[$row[csf('buyer_id')]];//$buyer_arr[$booking_data_arr[$row[csf('booking_id')]]['buyer']]; ?></p></td>
									<td width="120"><p><? echo $order_arr[$row[csf("po_breakdown_id")]]; ?></p></td>
									<td width="100" title="Booking ID=<? echo $row[csf('booking_id')];?>"><p><? echo $row[csf('buyer_job_no')];//$booking_data_arr[$row[csf('booking_id')]]['job_no']; ?></p></td>
									<td width="100" align="center"><p><? echo $row[csf('booking_no')];//$booking_data_arr[$row[csf('booking_id')]]['booking_no']; ?></p></td>
									<td width="150"><p><? echo $supplier_arr[$row[csf('knit_dye_company')]]; ?></p></td>
									<td width="60" align="center"><p><? echo $count_arr[$product_data_arr[$row[csf('prod_id')]]['yarn_count_id']]; ?></p></td>
									<td width="120"><p><? if($composition_id!="")  echo $composition[$composition_id]." ".$product_data_arr[$row[csf('prod_id')]]['yarn_comp_percent1st']."%"; ?></p></td>
									<td width="100"><p><? echo $yarn_type[$product_data_arr[$row[csf('prod_id')]]['yarn_type']]; ?></p></td>
									<td width="100" align="center"><p><? echo $color_arr[$product_data_arr[$row[csf('prod_id')]]['color']]; ?></p></td>
									<td width="80" align="right"><a href="##" onclick="openmypage('<? echo $row[csf("po_breakdown_id")]; ?>','yarn_issue','<? echo $row[csf("trans_id")]; ?>','<? echo $row[csf("prod_id")]; ?>')"><? echo number_format($row[csf('quantity')],0); ?></a></td>
									<td width="80" align="right"><p><? echo number_format($return_qty,0); ?></p></td>
									<td width="160"><p><? echo $row[csf('remarks')]; ?></p></td>
								</tr>
								<?
								$j++;$i++;
								$issue_qnty_subcon += $row[csf('quantity')];
								$return_qnty_subcon += $return_qty;
								$tot_net_issue_subcon += $net_issue_subcon;
							}
							else if($req_check[$row[csf('requisition_no')]]!="" && $row[csf('receive_basis')]==3)
							{
								$return_qty = $return_qty_arr_subcon[$row[csf("mst_id")]][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]['return_qty'];
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="30"><? echo $j; ?></td>
									<td width="100" ><p><? echo $buyer_arr[$req_data_arr[$row[csf('requisition_no')]]['buyer']]; ?></p></td>
									<td width="120"><p><? echo $order_arr[$row[csf("po_breakdown_id")]]; ?></p></td>
									<td width="100"><p><? echo $req_data_arr[$row[csf('requisition_no')]]['job_no']; ?></p></td>
									<td width="100" align="center"><p><? echo $req_data_arr[$row[csf('requisition_no')]]['booking_no_prefix_num']; ?></p></td>
									<td width="150"><p><? echo $supplier_arr[$row[csf('knit_dye_company')]]; ?></p></td>
									<td width="60" align="center"><p><? echo $count_arr[$product_data_arr[$row[csf('prod_id')]]['yarn_count_id']]; ?></p></td>
									<td width="120"><p><? if($composition_id!="")  echo $composition[$composition_id]." ".$product_data_arr[$row[csf('prod_id')]]['yarn_comp_percent1st']."%"; ?></p></td>
									<td width="100"><p><? echo $yarn_type[$product_data_arr[$row[csf('prod_id')]]['yarn_type']]; ?></p></td>
									<td width="100" align="center"><p><? echo $color_arr[$product_data_arr[$row[csf('prod_id')]]['color']]; ?></p></td>
									<td width="80" align="right"><a href="##" onclick="openmypage('<? echo $row[csf("po_breakdown_id")]; ?>','yarn_issue','<? echo $row[csf("trans_id")]; ?>','<? echo $row[csf("prod_id")]; ?>')"><? echo number_format($row[csf('quantity')],0); ?></a></td>
									<td width="80" align="right"><p><? echo number_format($return_qty,0); ?></p></td>
									<td width="160"><p><? echo $row[csf('remarks')]; ?></p></td>
								</tr>
								<?
								$j++;
								$issue_qnty_subcon += $row[csf('quantity')];
								$return_qnty_subcon += $return_qty;
								$tot_net_issue_subcon += $net_issue_subcon;
							}
						}
						$gt_qnty = $issue_qnty_inhouse+$issue_qnty_subcon;
						$gt_return_qnty = $tot_return_qnty+$return_qnty_subcon;
						$gt_net_issue_qnty = $tot_net_issue+$tot_net_issue_subcon;
						?>
	            	</tbody>

	            </table>
	        </div>
	        <table width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
	            <tfoot>
	                <th width="30">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="120">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="150">&nbsp;</th>
	                <th width="60">&nbsp;</th>
	                <th width="120">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="100">Sub Total:</th>
	                <th width="80"><? echo number_format($issue_qnty_subcon,0); ?></th>
	                <th width="80"><? echo number_format($return_qnty_subcon,0); ?></th>
	                <th width="160"></th>
	            </tfoot>
	        </table>
	        <table width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
	            <tfoot>
	                <th width="30">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="120">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="150">&nbsp;</th>
	                <th width="60">&nbsp;</th>
	                <th width="120">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="100">Grand Total:</th>
	                <th width="80"><? echo number_format($gt_qnty,0); ?></th>
	                <th width="80"><? echo number_format($gt_return_qnty,0); ?></th>
	                <th width="160"></th>
	            </tfoot>
	        </table>
	    </fieldset>
	    </div>
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
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$type";
	exit();
}

/******* 23-01-2023 MD Didarul Alam  ******/
if ($action == "generate_report_only_excel")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_cpa_booking_no=str_replace("'","",$txt_cpa_booking_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$type=str_replace("'","",$type);

	if ($type==3)
	{
		$buyer_cond=$booking_cond=$company_cond="";
		if($cbo_company_name!=0) $company_cond=" and a.company_id=$cbo_company_name";
		if($cbo_buyer_name!=0) $buyer_cond=" and a.buyer_id=$cbo_buyer_name";
		if($txt_cpa_booking_no!="") $booking_cond=" and a.booking_no like '%$txt_cpa_booking_no'";

		$dataArray_booking=sql_select( "SELECT a.id, a.booking_no, a.booking_no_prefix_num, a.job_no, a.buyer_id from wo_booking_mst a where a.booking_type=1 and a.is_short=1 $company_cond $buyer_cond $booking_cond" );

		$booking_data_arr=$boking_check=array();
		foreach($dataArray_booking as $row)
		{
			$boking_check[$row[csf('id')]]=$row[csf('id')];
			$booking_data_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
			$booking_data_arr[$row[csf('id')]]['booking_no_prefix_num']=$row[csf('booking_no_prefix_num')];
			$booking_data_arr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$booking_data_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_id')];
		}
		$bookingId = implode(",", $boking_check);
		// print_r($boking_check);
		$dataArray_requisition=sql_select("SELECT a.id, a.booking_no, a.booking_no_prefix_num, a.job_no, a.buyer_id, d.requisition_no from wo_booking_mst a, ppl_planning_info_entry_mst b, ppl_planning_info_entry_dtls c, ppl_yarn_requisition_entry d where a.booking_no=b.booking_no and a.booking_type=1 and a.is_short=1 and b.id=c.mst_id and c.id=d.knit_id  $company_cond $buyer_cond $booking_cond group by d.requisition_no, a.id, a.booking_no, a.booking_no_prefix_num, a.job_no, a.buyer_id");

		$req_data_arr=$req_check=array();
		foreach($dataArray_requisition as $row)
		{
			$req_check[$row[csf('requisition_no')]]=$row[csf('requisition_no')];
			$req_data_arr[$row[csf('requisition_no')]]['booking_no']=$row[csf('booking_no')];
			$req_data_arr[$row[csf('requisition_no')]]['booking_no_prefix_num']=$row[csf('booking_no_prefix_num')];
			$req_data_arr[$row[csf('requisition_no')]]['job_no']=$row[csf('job_no')];
			$req_data_arr[$row[csf('requisition_no')]]['buyer']=$row[csf('buyer_id')];
		}
		if(count($dataArray_booking)==0 && count($dataArray_requisition)==0)
		{
			echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">Data not found.</div>';
			die();
		}
		// print_r($req_check);
		$reqId = implode(",", $req_check);
		$bo_id_list_arr=array_chunk($boking_check,999);
		$bookingIdCond = " and ";
		$p=1;
		foreach($bo_id_list_arr as $bookingIdids)
	    {
	    	if($p==1)
			{
				$bookingIdCond .="  ( a.booking_id in(".implode(',',$bookingIdids).")";
			}
	        else
	        {
	          $bookingIdCond .=" or a.booking_id in(".implode(',',$bookingIdids).")";
	      	}
	        $p++;
	    }
	    $bookingIdCond .=")";

		$req_id_list_arr=array_chunk($req_check,999);
		$reqIdCond = " and ";
		$p=1;
		foreach($req_id_list_arr as $reqIdids)
	    {
	    	if($p==1)
			{
				$reqIdCond .="  ( b.requisition_no in(".implode(',',$reqIdids).")";
			}
	        else
	        {
	          $reqIdCond .=" or b.requisition_no in(".implode(',',$reqIdids).")";
	      	}
	        $p++;
	    }
	    $reqIdCond .=")";

		$product_data=sql_select("SELECT a.id,a.lot,a.brand,a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_type, a.color from product_details_master a where a.status_active=1 and a.item_category_id=1 $company_cond");
		$product_data_arr=array();
		foreach($product_data as $val)
		{
			$product_data_arr[$val[csf("id")]]['prod_id']=$val[csf("id")];
			$product_data_arr[$val[csf("id")]]['yarn_count_id']=$val[csf("yarn_count_id")];
			$product_data_arr[$val[csf("id")]]['yarn_comp_type1st']=$val[csf("yarn_comp_type1st")];
			$product_data_arr[$val[csf("id")]]['yarn_comp_percent1st']=$val[csf("yarn_comp_percent1st")];
			$product_data_arr[$val[csf("id")]]['yarn_type']=$val[csf("yarn_type")];
			$product_data_arr[$val[csf("id")]]['color']=$val[csf("color")];
			$product_data_arr[$val[csf("id")]]['brand']=$val[csf("brand")];
			$product_data_arr[$val[csf("id")]]['lot']=$val[csf("lot")];
		}


	    $html = "";
	    $html .= '
        <table width="2100" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="100">Company</th>
                <th width="100">Buyer Name</th>
                <th width="120">Order No</th>
                <th width="100">Job No</th>
                <th width="100">CPA NO</th>
                <th width="100">Issue Source</th>
                <th width="150">Issue To</th>
                <th width="100">Issue Id</th>
                <th width="100">Yarn Issue Date</th>
                <th width="100">Yarn Brand</th>
                <th width="60">Yarn Count</th>
                <th width="120">Composition</th>
                <th width="100">Yarn Type</th>
                <th width="100">Colour</th>
                <th width="100">Lot</th>
                <th width="80">Issue Qty</th>
                <th width="80">Returnable Qty.</th>
                <th width="80">Return Qty</th>
                <th width="80">Net Issue Qty.</th>
                <th width="80">Rate/Kg</th>
                <th>Net Issue Amount</th>
            </thead>
        </table>';

        $html .='
		<table width="2100" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="tbl_body">
            <tbody>';
            	$date_condition='';
            	if( $txt_date_from!="" && $txt_date_to!='')
            	{
            		$date_condition =" and a.issue_date between '$txt_date_from' and '$txt_date_to'";
            	}

				$sql_issue="SELECT a.company_id,a.id as mst_id, a.buyer_id, a.booking_id, a.knit_dye_source, a.knit_dye_company, a.issue_number,a.issue_date, b.id as trans_id, b.requisition_no,b.return_qnty as returnable_qty,b.cons_rate,c.po_breakdown_id, c.prod_id, c.quantity,b.receive_basis from inv_issue_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.item_category=1 and a.entry_form=3 and b.item_category=1 and b.transaction_type=2 and a.issue_basis in(1,3) and a.issue_purpose=1 and b.receive_basis in(1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_cond $date_condition order by a.company_id,a.knit_dye_source,a.issue_number, a.issue_date";//and a.knit_dye_source=1 $bookingIdCond $reqIdCond
				//echo $sql_issue; die;
				$result=sql_select($sql_issue);
				$data_array = array();
				foreach ($result as $val)
				{
					if($val[csf("booking_id")] !="" && $val[csf("receive_basis")]==1)
					{
						if(isset($boking_check[$val[csf("booking_id")]]))
						{
							$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['issue_qty'] += $val[csf('quantity')];
							$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['returnable_qty'] += $val[csf('returnable_qty')];
							$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['cons_rate'] = $val[csf('cons_rate')];

							$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['knit_dye_company'] = $val[csf('knit_dye_company')];
							$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['trans_id'] = $val[csf('trans_id')];

							$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['company_id'] = $val[csf('company_id')];
							$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['issue_number'] = $val[csf('issue_number')];
							$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['issue_date'] = $val[csf('issue_date')];
							$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['knit_dye_source'] = $val[csf('knit_dye_source')];

							$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['buyer'] = $buyer_arr[$booking_data_arr[$val[csf('booking_id')]]['buyer']];
							$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['job_no'] = $booking_data_arr[$val[csf('booking_id')]]['job_no'];
							$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['booking_no'] = $booking_data_arr[$val[csf('booking_id')]]['booking_no'];
						}

					}
					else if($val[csf("requisition_no")] !="" && $val[csf("receive_basis")]==3)
					{
						if(isset($req_check[$val[csf("requisition_no")]]))
						{
							$data_array[$val[csf("requisition_no")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['issue_qty'] += $val[csf('quantity')];
							$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['returnable_qty'] += $val[csf('returnable_qty')];
							$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['cons_rate'] = $val[csf('cons_rate')];

							$data_array[$val[csf("requisition_no")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['knit_dye_company'] = $val[csf('knit_dye_company')];
							$data_array[$val[csf("requisition_no")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['trans_id'] = $val[csf('trans_id')];
							$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['company_id'] = $val[csf('company_id')];
							$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['issue_number'] = $val[csf('issue_number')];
							$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['issue_date'] = $val[csf('issue_date')];
							$data_array[$val[csf("booking_id")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['knit_dye_source'] = $val[csf('knit_dye_source')];
							$data_array[$val[csf("requisition_no")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['buyer'] = $buyer_arr[$req_data_arr[$val[csf('requisition_no')]]['buyer']];
							$data_array[$val[csf("requisition_no")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['job_no'] = $req_data_arr[$val[csf('requisition_no')]]['job_no'];
							$data_array[$val[csf("requisition_no")]][$val[csf("mst_id")]][$val[csf("po_breakdown_id")]][$val[csf("prod_id")]]['booking_no'] = $req_data_arr[$val[csf('requisition_no')]]['booking_no'];
						}
					}
				}
				// print_r($data_array);die();
				// $prodId = implode(",", $prod_id_arr);
				//================================ issue return =======================================
				$sql_return = "SELECT a.id as mst_id,a.buyer_id, a.knitting_company as knit_dye_company, a.remarks, b.issue_id, c.po_breakdown_id as po_id, c.prod_id,a.booking_id,c.trans_id,a.receive_basis, b.requisition_no, c.quantity as return_qty from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.item_category=1 and a.entry_form=9 and b.item_category=1 and b.transaction_type=4 and a.receive_basis in(1,3) and b.receive_basis in(1,3) and a.knitting_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_cond";
				//echo $sql_return; die();
				$sql_return_res = sql_select($sql_return);
				$return_qty_arr = array();
				foreach ($sql_return_res as $row)
				{
					if($row[csf("receive_basis")] ==3 && $row[csf("requisition_no")] !="")
					{
						if(isset($req_check[$row[csf("requisition_no")]]) || isset($req_check[$row[csf("booking_id")]]))
						{
							$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['return_qty']+=$row[csf("return_qty")];
							$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['knit_dye_company'] = $row[csf('knit_dye_company')];
							$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['trans_id2'] .= $row[csf('trans_id')].",";
							$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['remarks'] = $row[csf('remarks')];
							$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['buyer'] = $buyer_arr[$req_data_arr[$row[csf('booking_id')]]['buyer']];
							$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['job_no'] = $req_data_arr[$row[csf('booking_id')]]['job_no'];
							$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['booking_no'] = $req_data_arr[$row[csf('booking_id')]]['booking_no'];
						}
					}
					else if($row[csf("receive_basis")] ==1 && $row[csf("booking_id")] !="")
					{
						if(isset($boking_check[$row[csf("booking_id")]]) || isset($req_check[$row[csf("requisition_no")]]))
						{
							$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['return_qty']+=$row[csf("return_qty")];
							$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['knit_dye_company'] = $row[csf('knit_dye_company')];
							$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['trans_id2'] .= $row[csf('trans_id')].",";
							$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['remarks'] = $row[csf('remarks')];
							$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['buyer'] = $buyer_arr[$booking_data_arr[$row[csf('booking_id')]]['buyer']];
							$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['job_no'] = $booking_data_arr[$row[csf('booking_id')]]['job_no'];
							$data_array[$row[csf("booking_id")]][$row[csf("issue_id")]][$row[csf("po_id")]][$row[csf("prod_id")]]['booking_no'] = $booking_data_arr[$row[csf('booking_id')]]['booking_no'];
						}
					}
				}

				//echo "<pre>";
			 	//print_r($data_array);die();

				$i=1;$issue_qnty_inhouse=$tot_return_qnty=$tot_net_issue=$gt_qnty=$gt_return_qnty=0;
				foreach ($data_array as $booking_id => $bookingData)
				{
					foreach ($bookingData as $issue_id => $issueData)
					{
						foreach ($issueData as $po_id => $poData)
						{
							foreach ($poData as $prod_id => $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$composition_id="";
								$composition_id = $product_data_arr[$prod_id]['yarn_comp_type1st'];

								if($composition_id!="")  {
									$yarn_composition =  $composition[$composition_id]." ".$product_data_arr[$prod_id]['yarn_comp_percent1st']."%";
								}

								if($row['knit_dye_source']==1)
								{
									$knitting_party = $company_arr[$row['knit_dye_company']];
								}
								else{
									$knitting_party = $supplier_arr[$row['knit_dye_company']];
								}

								$html .='
									<tr>
										<td>'.$i. ' </td>
										<td>'.$company_arr[$row['company_id']].'</td>
										<td>'.$row['buyer'].'</td>
										<td>'.$order_arr[$po_id].'</td>
										<td>'.$row['job_no'].'</td>
										<td>'.$row['booking_no'].'</td>
										<td>'.$knitting_source_arr[$row['knit_dye_source']].'</td>
										<td>'.$knitting_party.'</td>
										<td>'.$row['issue_number'].'</td>
										<td>'.change_date_format($row['issue_date']).'</td>
										<td>'.$brand_arr[$product_data_arr[$prod_id]['brand']].'</td>
										<td>'.$count_arr[$product_data_arr[$prod_id]['yarn_count_id']].'</td>
										<td>'.$yarn_composition.'</td>
										<td>'.$yarn_type[$product_data_arr[$prod_id]["yarn_type"]].'</td>
										<td>'.$color_arr[$product_data_arr[$prod_id]['color']].'</td>
										<td>'.$product_data_arr[$prod_id]['lot'].'</td>
										<td>'.number_format($row['issue_qty'],2).'</td>
										<td>'.number_format($row['returnable_qty'],2).'</td>
										<td>'.number_format($row['return_qty'],2).'</td>
										<td>'.number_format( ($row['issue_qty']-$row['return_qty']) ,2).'</td>
										<td>'.number_format($row['cons_rate'],2).'</td>
										<td>'.number_format( (($row['issue_qty']-$row['return_qty'])*$row['cons_rate']),4).'</td>
									</tr>';

								$i++;
								$total_issue_qnty+=	$row['issue_qty'];
								$total_returnable_qty+=	$row['returnable_qty'];
								$tot_return_qnty+=	$row['return_qty'];
								$tot_net_issue_qnty+=	($row['issue_qty']-$row['return_qty']);
								$tot_net_issue_amount+= (($row['issue_qty']-$row['return_qty'])*$row['cons_rate']);
							}
						}
					}
				}

				$html .='<table width="2100" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
		            <tfoot>
		                <th>&nbsp;</th>
		                <th>&nbsp;</th>
		                <th>&nbsp;</th>
		                <th>&nbsp;</th>
		                <th>&nbsp;</th>
		                <th>&nbsp;</th>
		                <th>&nbsp;</th>
		                <th>&nbsp;</th>
		                <th>&nbsp;</th>
		                <th>&nbsp;</th>
		                <th>&nbsp;</th>
		                <th>&nbsp;</th>
		                <th>&nbsp;</th>
		                <th>&nbsp;</th>
		                <th>&nbsp;</th>
		                <th>Grand Total:</th>
		                <th>'.number_format($total_issue_qnty,0).'</th>
		                <th>'.number_format($total_returnable_qty,0).'</th>
		                <th>'.number_format($tot_return_qnty,0).'</th>
		                <th>'.number_format($tot_net_issue_qnty,0).'</th>
		                <th>&nbsp;</th>
		                <th>'.number_format($tot_net_issue_amount,0).'</th>
		            </tfoot>
	        	</table>

        	</tbody>
        </table>';
	}

	foreach (glob("*.xls") as $filename) {
		@unlink($filename);
	}
	//---------end------------//
	$name = time();
	$filename = $user_id . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html####$filename####$type";
	exit();
}

if($action=="yarn_issue")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$brand_array=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name");
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_details=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
	$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name");

	$sqlWO="select a.fabric_source,b.po_break_down_id,a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($order_id)";
	 $resultWo=sql_select($sqlWO);
	 $fab_source_ids="";
		foreach($resultWo as $woRow)
		{
			 $fab_source_ids.=$woRow[csf('fabric_source')].',';
		}
		$fab_source=rtrim($fab_source_ids,',');
		$fab_source_id=array_unique(explode(",",$fab_source));
	?>
	<script>

		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

			d.close();
		}

	</script>
		<div style="width:870px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
		<fieldset style="width:865px; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
	            	<thead>
						<th colspan="10"><b>Yarn Issue</b></th>
					</thead>
					<thead>
	                    <th width="105">Issue Id</th>
	                    <th width="90">Issue To</th>
	                    <th width="105">Booking No</th>
	                    <th width="80">Store</th>
	                    <th width="70">Brand</th>
	                    <th width="60">Lot No</th>
	                    <th width="75">Issue Date</th>
	                    <th width="80">Yarn Type</th>
	                    <th width="90">Issue Qty (In)</th>
	                    <th>Issue Qty (Out)</th>
					</thead>
	                <?
	                $i=1; $total_yarn_issue_qnty=0; $total_yarn_issue_qnty_out=0;

					$sql="select a.id as issue_id, a.issue_number, a.issue_date, a.store_id, a.knit_dye_source, a.knit_dye_company, a.booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id, a.issue_basis, d.requisition_no from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and c.item_category_id=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and b.trans_id='$trans_id' and c.id='$prod_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.issue_number, a.issue_date, a.store_id, a.knit_dye_source, a.knit_dye_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id, a.issue_basis, d.requisition_no order by a.issue_date DESC";
	                $result=sql_select($sql);
	                foreach($result as $row)
					{
						if($row[csf('issue_basis')] == 3){
							$requisition_no_arr[$row[csf("requisition_no")]] = $row[csf("requisition_no")];
						}
						$issue_id_arr[$row[csf("issue_id")]] = $row[csf("issue_id")];
					}
					$requisition_no_arr = array_filter($requisition_no_arr);

					if(!empty($requisition_no_arr))
					{
						$requ_booking_no_arr = return_library_array("select a.requisition_no, c.booking_no from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst c where a.knit_id = b.id and b.mst_id = c.id and a.status_active=1 and a.requisition_no in (".implode(",", $requisition_no_arr).") group by a.requisition_no, c.booking_no","requisition_no","booking_no");
					}

					foreach($result as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$issue_to="";
						if($row[csf('knit_dye_source')]==1) $issue_to=$company_library[$row[csf('knit_dye_company')]]; else $issue_to=$supplier_details[$row[csf('knit_dye_company')]];

	                    foreach($fab_source_id as $fsid)
						{
							if($fsid==1) $yarn_issued=$row[csf('issue_qnty')];
						}
	                    ?>
	                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                    	<td width="105"><p><? echo $row[csf('issue_number')]; ?></p></td>
	                        <td width="90"><p><? echo $issue_to; ?></p></td>
	                        <td width="105">
	                        	<p>
	                        	<?
	                        		if($row[csf('issue_basis')] == 3) echo $requ_booking_no_arr[$row[csf("requisition_no")]];
									else if($row[csf('issue_basis')] == 1) echo $row[csf('booking_no')];
	                        	?>
	                        	&nbsp;
	                        	</p>
	                        </td>
	                        <td width="80" style="word-break:break-all"><? echo $store_arr[$row[csf('store_id')]]; ?>&nbsp;</td>
	                        <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
	                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
	                        <td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
	                        <td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
	                        <td align="right" width="90">
								<?
									if($row[csf('knit_dye_source')]!=3)
									{
										echo number_format($yarn_issued,2);
										$total_yarn_issue_qnty+=$yarn_issued;
									}
									else echo "&nbsp;";
	                            ?>
	                        </td>
	                        <td align="right">
								<?
									if($row[csf('knit_dye_source')]==3)
									{
										echo number_format($yarn_issued,2);
										$total_yarn_issue_qnty_out+=$yarn_issued;
									}
									else echo "&nbsp;";
	                            ?>
	                        </td>
	                    </tr>
	                <?
	                $total_issue = $total_yarn_issue_qnty+$total_yarn_issue_qnty_out;
	                $i++;
	                }
					unset($result);
	                ?>
	                <tr style="font-weight:bold">
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td align="right">Total</td>
	                    <td align="right"><? echo number_format($total_yarn_issue_qnty,2);?></td>
	                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out,2);?></td>
	                </tr>
	                <tr style="font-weight:bold">
	                    <td align="right" colspan="9">Issue Total</td>
	                    <td align="right"><? echo number_format($total_issue,2);?></td>
	                </tr>
	                <thead>
	                    <th colspan="10"><b>Yarn Return</b></th>
	                </thead>
	                <thead>
	                	<th width="105">Return Id</th>
	                    <th width="90">Return From</th>
	                    <th width="105">Booking No</th>
	                    <th width="80">Challan No</th>
	                    <th width="70">Brand</th>
	                    <th width="60">Lot No</th>
	                    <th width="75">Return Date</th>
	                    <th width="80">Yarn Type</th>
	                    <th width="90">Return Qnty (In)</th>
	                    <th>Return Qnty (Out)</th>
	               </thead>
	                <?
	                $total_yarn_return_qnty=0; $total_yarn_return_qnty_out=0;

					if(!empty($issue_id_arr))
					{
						$issue_id_cond = " and a.issue_id in (".implode(',', $issue_id_arr).") ";
					}
	                $sql="select a.recv_number, a.receive_date, a.store_id, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id, a.receive_basis from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and c.item_category_id=1 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.id='$prod_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 $issue_id_cond group by a.id, c.id, a.recv_number, a.receive_date, a.store_id, a.knitting_source, a.knitting_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id, a.receive_basis order by a.receive_date DESC";
	                $result=sql_select($sql);
					foreach($result as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$return_from="";
						if($row[csf('knitting_source')]==1) $return_from=$company_library[$row[csf('knitting_company')]]; else $return_from=$supplier_details[$row[csf('knitting_company')]];

	                    $yarn_returned=$row[csf('returned_qnty')];
	                    ?>
	                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                    	<td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
	                        <td width="90"><p><? echo $return_from; ?></p></td>
	                        <td width="105">
	                        	<p>
	                        	<?
	                        		if($row[csf('receive_basis')] == 3) echo $requ_booking_no_arr[$row[csf("booking_no")]];
									else if($row[csf('receive_basis')] == 1) echo $row[csf('booking_no')];
	                        	?>
	                        	&nbsp;
	                        	</p>
	                        </td>
	                        <td width="80" style="word-break:break-all"><? echo $store_arr[$row[csf('store_id')]]; ?>&nbsp;</td>
	                        <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
	                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
	                        <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
	                        <td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
	                        <td align="right" width="90">
								<?
									if($row[csf('knitting_source')]!=3)
									{
										echo number_format($yarn_returned,2);
										$total_yarn_return_qnty+=$yarn_returned;
									}
									else echo "&nbsp;";
	                            ?>
	                        </td>
	                        <td align="right">
								<?
									if($row[csf('knitting_source')]==3)
									{
										echo number_format($yarn_returned,2);
										$total_yarn_return_qnty_out+=$yarn_returned;
									}
									else echo "&nbsp;";
	                            ?>
	                        </td>
	                    </tr>
						<?
	                    $return_qnty = $total_yarn_return_qnty+$total_yarn_return_qnty_out;
	                    $i++;
	                }
					unset($result);
	                $total_balence = $total_issue-$return_qnty;
	                ?>
	                <tr style="font-weight:bold">
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td align="right">Total</td>
	                    <td align="right"><? echo number_format($total_yarn_return_qnty,2);?></td>
	                    <td align="right"><? echo number_format($total_yarn_return_qnty_out,2);?></td>
	                </tr>
	                <tr style="font-weight:bold">
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td align="right">Return Total</td>
	                    <td align="right" colspan="2"><? number_format($return_qnty,2);?></td>
	                </tr>
	                <tfoot>
	                    <tr>
	                        <th align="right" colspan="9">Total Balance</th>
	                        <th align="right"><? echo number_format($total_balence,2);?></th>
	                    </tr>
	                </tfoot>
	            </table>
			</div>
		</fieldset>
		<?
	    exit();
}

if($action=="yarn_issue2")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$brand_array=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name");
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_details=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
	$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name");

	$sqlWO="select a.fabric_source,b.po_break_down_id,a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($order_id)";
	 $resultWo=sql_select($sqlWO);
	 $fab_source_ids="";
		foreach($resultWo as $woRow)
		{
			 $fab_source_ids.=$woRow[csf('fabric_source')].',';
		}
		$fab_source=rtrim($fab_source_ids,',');
		$fab_source_id=array_unique(explode(",",$fab_source));
	?>
	<script>

		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

			d.close();
		}

	</script>
		<div style="width:870px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
		<fieldset style="width:865px; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
	            	<thead>
						<th colspan="10"><b>Yarn Issue</b></th>
					</thead>
					<thead>
	                    <th width="105">Issue Id</th>
	                    <th width="90">Issue To</th>
	                    <th width="105">Booking No</th>
	                    <th width="80">Store</th>
	                    <th width="70">Brand</th>
	                    <th width="60">Lot No</th>
	                    <th width="75">Issue Date</th>
	                    <th width="80">Yarn Type</th>
	                    <th width="90">Issue Qty (In)</th>
	                    <th>Issue Qty (Out)</th>
					</thead>
	                <?
	                $i=1; $total_yarn_issue_qnty=0; $total_yarn_issue_qnty_out=0;

					$sql="select a.id as issue_id, a.issue_number, a.issue_date, a.store_id, a.knit_dye_source, a.knit_dye_company, a.booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id, a.issue_basis, d.requisition_no from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and c.item_category_id=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and b.trans_id='$trans_id' and c.id='$prod_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.issue_number, a.issue_date, a.store_id, a.knit_dye_source, a.knit_dye_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id, a.issue_basis, d.requisition_no order by a.issue_date DESC";
	                $result=sql_select($sql);
	                foreach($result as $row)
					{
						if($row[csf('issue_basis')] == 3){
							$requisition_no_arr[$row[csf("requisition_no")]] = $row[csf("requisition_no")];
						}
						$issue_id_arr[$row[csf("issue_id")]] = $row[csf("issue_id")];
					}
					$requisition_no_arr = array_filter($requisition_no_arr);

					if(!empty($requisition_no_arr))
					{
						$requ_booking_no_arr = return_library_array("select a.requisition_no, c.booking_no from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst c where a.knit_id = b.id and b.mst_id = c.id and a.status_active=1 and a.requisition_no in (".implode(",", $requisition_no_arr).") group by a.requisition_no, c.booking_no","requisition_no","booking_no");
					}

					foreach($result as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$issue_to="";
						if($row[csf('knit_dye_source')]==1) $issue_to=$company_library[$row[csf('knit_dye_company')]]; else $issue_to=$supplier_details[$row[csf('knit_dye_company')]];

	                    foreach($fab_source_id as $fsid)
						{
							if($fsid==1) $yarn_issued=$row[csf('issue_qnty')];
						}
	                    ?>
	                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                    	<td width="105"><p><? echo $row[csf('issue_number')]; ?></p></td>
	                        <td width="90"><p><? echo $issue_to; ?></p></td>
	                        <td width="105">
	                        	<p>
	                        	<?
	                        		if($row[csf('issue_basis')] == 3) echo $requ_booking_no_arr[$row[csf("requisition_no")]];
									else if($row[csf('issue_basis')] == 1) echo $row[csf('booking_no')];
	                        	?>
	                        	&nbsp;
	                        	</p>
	                        </td>
	                        <td width="80" style="word-break:break-all"><? echo $store_arr[$row[csf('store_id')]]; ?>&nbsp;</td>
	                        <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
	                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
	                        <td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
	                        <td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
	                        <td align="right" width="90">
								<?
									if($row[csf('knit_dye_source')]!=3)
									{
										echo number_format($yarn_issued,2);
										$total_yarn_issue_qnty+=$yarn_issued;
									}
									else echo "&nbsp;";
	                            ?>
	                        </td>
	                        <td align="right">
								<?
									if($row[csf('knit_dye_source')]==3)
									{
										echo number_format($yarn_issued,2);
										$total_yarn_issue_qnty_out+=$yarn_issued;
									}
									else echo "&nbsp;";
	                            ?>
	                        </td>
	                    </tr>
	                <?
	                $i++;
	                }
					unset($result);
	                ?>
	                <tfoot>
	                    <tr>
	                        <th align="right" colspan="8">Total</th>
	                    	<th align="right"><? echo number_format($total_yarn_issue_qnty,2);?></th>
	                    	<th align="right"><? echo number_format($total_yarn_issue_qnty_out,2);?></th>
	                    </tr>
	                </tfoot>
	            </table>
			</div>
		</fieldset>
		<?
	    exit();
}

if($action=="yarn_issue_return")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$brand_array=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name");
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_details=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
	$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name");

	$sqlWO="select a.fabric_source,b.po_break_down_id,a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($order_id)";
	 $resultWo=sql_select($sqlWO);
	 $fab_source_ids="";
		foreach($resultWo as $woRow)
		{
			 $fab_source_ids.=$woRow[csf('fabric_source')].',';
		}
		$fab_source=rtrim($fab_source_ids,',');
		$fab_source_id=array_unique(explode(",",$fab_source));
	?>
	<script>

		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

			d.close();
		}

	</script>
		<div style="width:870px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
		<fieldset style="width:865px; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
	                <thead>
	                    <th colspan="10"><b>Yarn Return</b></th>
	                </thead>
	                <thead>
	                	<th width="105">Return Id</th>
	                    <th width="90">Return From</th>
	                    <th width="105">Booking No</th>
	                    <th width="80">Challan No</th>
	                    <th width="70">Brand</th>
	                    <th width="60">Lot No</th>
	                    <th width="75">Return Date</th>
	                    <th width="80">Yarn Type</th>
	                    <th width="90">Return Qnty (In)</th>
	                    <th>Return Qnty (Out)</th>
	               </thead>
	                <?
	                $total_yarn_return_qnty=0; $total_yarn_return_qnty_out=0;

					if(!empty($issue_id_arr))
					{
						$issue_id_cond = " and a.issue_id in (".implode(',', $issue_id_arr).") ";
					}
	                $sql="SELECT a.recv_number, a.receive_date, a.store_id, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id, a.receive_basis from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and c.item_category_id=1 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.id='$prod_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 $issue_id_cond group by a.id, c.id, a.recv_number, a.receive_date, a.store_id, a.knitting_source, a.knitting_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id, a.receive_basis order by a.receive_date DESC";
	                $result=sql_select($sql);
					foreach($result as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$return_from="";
						if($row[csf('knitting_source')]==1) $return_from=$company_library[$row[csf('knitting_company')]]; else $return_from=$supplier_details[$row[csf('knitting_company')]];

	                    $yarn_returned=$row[csf('returned_qnty')];
	                    ?>
	                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                    	<td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
	                        <td width="90"><p><? echo $return_from; ?></p></td>
	                        <td width="105">
	                        	<p>
	                        	<?
	                        		if($row[csf('receive_basis')] == 3) echo $requ_booking_no_arr[$row[csf("booking_no")]];
									else if($row[csf('receive_basis')] == 1) echo $row[csf('booking_no')];
	                        	?>
	                        	&nbsp;
	                        	</p>
	                        </td>
	                        <td width="80" style="word-break:break-all"><? echo $store_arr[$row[csf('store_id')]]; ?>&nbsp;</td>
	                        <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
	                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
	                        <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
	                        <td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
	                        <td align="right" width="90">
								<?
									if($row[csf('knitting_source')]!=3)
									{
										echo number_format($yarn_returned,2);
										$total_yarn_return_qnty+=$yarn_returned;
									}
									else echo "&nbsp;";
	                            ?>
	                        </td>
	                        <td align="right">
								<?
									if($row[csf('knitting_source')]==3)
									{
										echo number_format($yarn_returned,2);
										$total_yarn_return_qnty_out+=$yarn_returned;
									}
									else echo "&nbsp;";
	                            ?>
	                        </td>
	                    </tr>
						<?
	                    $i++;
	                }
					unset($result);
	                ?>
	                <tfoot>
	                    <tr>
	                        <th align="right" colspan="8">Total</th>
	                    	<th align="right"><? echo number_format($total_yarn_return_qnty,2);?></th>
	                    	<th align="right"><? echo number_format($total_yarn_return_qnty_out,2);?></th>
	                    </tr>
	                </tfoot>
	            </table>
			</div>
		</fieldset>
		<?
	    exit();
}

if($action=="yarn_issue_return2")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$brand_array=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name");
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_details=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
	$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name");

	$sqlWO="SELECT a.fabric_source,b.po_break_down_id,a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($order_id)";
	 $resultWo=sql_select($sqlWO);
	 $fab_source_ids="";
		foreach($resultWo as $woRow)
		{
			 $fab_source_ids.=$woRow[csf('fabric_source')].',';
		}
		$fab_source=rtrim($fab_source_ids,',');
		$fab_source_id=array_unique(explode(",",$fab_source));
	?>
	<script>

		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

			d.close();
		}

	</script>
		<div style="width:870px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
		<fieldset style="width:865px; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
	                <thead>
	                    <th colspan="10"><b>Yarn Return</b></th>
	                </thead>
	                <thead>
	                	<th width="105">Return Id</th>
	                    <th width="90">Return From</th>
	                    <th width="105">Booking No</th>
	                    <th width="80">Challan No</th>
	                    <th width="70">Brand</th>
	                    <th width="60">Lot No</th>
	                    <th width="75">Return Date</th>
	                    <th width="80">Yarn Type</th>
	                    <th width="90">Return Qnty (In)</th>
	                    <th>Return Qnty (Out)</th>
	               </thead>
	                <?
	                $total_yarn_return_qnty=0; $total_yarn_return_qnty_out=0;

					if(!empty($issue_id_arr))
					{
						$issue_id_cond = " and a.issue_id in (".implode(',', $issue_id_arr).") ";
					}
	                $sql="SELECT a.recv_number, a.receive_date, a.store_id, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id, a.receive_basis from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and c.item_category_id=1 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.id='$prod_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.id in($trans_id) $issue_id_cond group by a.id, c.id, a.recv_number, a.receive_date, a.store_id, a.knitting_source, a.knitting_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id, a.receive_basis order by a.receive_date DESC";//and b.issue_purpose!=2
	                // echo $sql;
	                $result=sql_select($sql);
					foreach($result as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$return_from="";
						if($row[csf('knitting_source')]==1) $return_from=$company_library[$row[csf('knitting_company')]]; else $return_from=$supplier_details[$row[csf('knitting_company')]];

	                    $yarn_returned=$row[csf('returned_qnty')];
	                    ?>
	                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                    	<td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
	                        <td width="90"><p><? echo $return_from; ?></p></td>
	                        <td width="105">
	                        	<p>
	                        	<?
	                        		if($row[csf('receive_basis')] == 3) echo $requ_booking_no_arr[$row[csf("booking_no")]];
									else if($row[csf('receive_basis')] == 1) echo $row[csf('booking_no')];
	                        	?>
	                        	&nbsp;
	                        	</p>
	                        </td>
	                        <td width="80" style="word-break:break-all"><? echo $store_arr[$row[csf('store_id')]]; ?>&nbsp;</td>
	                        <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
	                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
	                        <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
	                        <td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
	                        <td align="right" width="90">
								<?
									if($row[csf('knitting_source')]!=3)
									{
										echo number_format($yarn_returned,2);
										$total_yarn_return_qnty+=$yarn_returned;
									}
									else echo "&nbsp;";
	                            ?>
	                        </td>
	                        <td align="right">
								<?
									if($row[csf('knitting_source')]==3)
									{
										echo number_format($yarn_returned,2);
										$total_yarn_return_qnty_out+=$yarn_returned;
									}
									else echo "&nbsp;";
	                            ?>
	                        </td>
	                    </tr>
						<?
	                    $i++;
	                }
					unset($result);
	                ?>
	                <tfoot>
	                    <tr>
	                        <th align="right" colspan="8">Total</th>
	                    	<th align="right"><? echo number_format($total_yarn_return_qnty,2);?></th>
	                    	<th align="right"><? echo number_format($total_yarn_return_qnty_out,2);?></th>
	                    </tr>
	                </tfoot>
	            </table>
			</div>
		</fieldset>
		<?
	    exit();
}
?>
