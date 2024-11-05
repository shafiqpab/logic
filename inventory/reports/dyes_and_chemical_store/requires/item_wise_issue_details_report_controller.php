<?
	header('Content-type:text/html; charset=utf-8');
	session_start();
	include('../../../../includes/common.php');
	$user_id = $_SESSION['logic_erp']["user_id"];
	if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
	$permission=$_SESSION['page_permission'];
	$data=$_REQUEST['data'];
	$action=$_REQUEST['action'];

	if ($action=="issue_quantity_dtls_popup")
    {
        echo load_html_head_contents("Issue Quantity Details", "../../../../", 1, 1,$unicode,'','');
        extract($_REQUEST);
		
		
		//echo $issue_purpose; die;
        //echo $porduct_data_all."==".$prod_id;die;
        $transaction_date=explode("*",$transaction_date);
        $transaction_date_from= change_date_format($transaction_date[0],'','',1);
        $transaction_date_to  = change_date_format($transaction_date[1],'','',1);

		if($transaction_date_from !="" || $transaction_date_to !=""){
			$trns_date_cond = " and b.transaction_date between '$transaction_date_from' and
			'$transaction_date_to'";
		}else{
			$trns_date_cond= "";
		}

		if($issue_purpose==0)
		{
			$issue_purp_cond=" and a.issue_purpose=0 ";
		}else{
			//$issue_purp_cond="";
			$issue_purp_cond=" and a.issue_purpose=$issue_purpose";
		}
		
		 $sql_issue_dtls="select a.id, a.issue_date, a.issue_number_prefix_num as issue_num, a.issue_purpose,a.issue_basis, b.mst_id, b.order_qnty, b.prod_id,
					  b.transaction_type, b.cons_quantity as issue_qnty, b.item_category
		from inv_issue_master a, inv_transaction b
		where a.id=b.mst_id and b.prod_id=$prod_id and b.transaction_type=2 $trns_date_cond and a.entry_form in(5,21) $issue_purp_cond and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 order by b.mst_id";


        //echo $sql_issue_dtls;//die;
        $data_array=sql_select($sql_issue_dtls);
        ?>
        <div style="width:690px;">
            <table align="center" cellspacing="0" width="680" border="1" rules="all" class="rpt_table" >
                <thead>
                <tr>
                    <th width="50">SL</th>
                    <th width="80" >Prod ID</th>
                    <th width="100" >Issue Purpose</th>
                    <th width="90" >Basis</th>
                    <th width="100" >Issue Date</th>
                    <th width="80" >Issue No</th>
                    <th>Issue Quantity</th>
                </tr>
                </thead>
              </table> 
              <table width="680" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="list_view">
                <tbody>
                <?
                    $i=1;
                    foreach($data_array as $row)
                    {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td align="center" width="50"><? echo $i; ?></td>
                            <td align="right" width="80"><? echo $row[csf("prod_id")]; ?></td>
							<td align="right" width="100"><? echo $general_issue_purpose[$row[csf("issue_purpose")]]; ?></td>
							<?
							$yarn_dyeing_arr=array(5,6,7,22,23);
							if($row[csf("item_category")] != in_array($yarn_dyeing_arr) ){ ?>
                            	<td align="right" width="90"><? echo $receive_basis_arr[$row[csf("issue_basis")]]; ?></td>
							<?
							}
							else
							{
							?>
							<td align="right" width="90"><? echo $general_issue_purpose[$row[csf("issue_basis")]]; ?></td>
							<?
							}
							?>
                            <td align="right" width="100"><? echo $row[csf("issue_date")]; ?></td>
                            <td align="right" width="80"><? echo $row[csf("issue_num")]; ?></td>
                            <td align="right"><?  echo $row[csf("issue_qnty")]; ?></td>
                        </tr>
                        <?
                        $i++;
						$pord_total_issue += $row[csf("issue_qnty")];
                    }
                ?>
                </tbody>
				<tfoot>
					<tr>
						<td colspan="6" align="right"><strong>Total:</strong></td>
						<td align="right"><strong><? echo $pord_total_issue;?></strong></td>
					</tr>
				</tfoot>
            </table>
        </div>
        <script>
        setFilterGrid("list_view",-1)
        </script> 
        <?
        exit();
    }

	if($action=="generate_report")
	{
		$process = array(&$_POST);
		extract(check_magic_quote_gpc($process));
		$report_title=str_replace("'","",$report_title);
		$cbo_company_name=str_replace("'","",$cbo_company_name);
		$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
		$txt_date_from=str_replace("'","",$txt_date_from);
		$txt_date_to=str_replace("'","",$txt_date_to);
		$date_range = "$txt_date_from*$txt_date_to";

		if($db_type==0)
		{
			$txt_date_form=change_date_format($txt_date_from,'yyyy-mm-dd');
			$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
		}
		else if($db_type==2)
		{
			$txt_date_from=change_date_format($txt_date_from,'','',1);
			$txt_date_to=change_date_format($txt_date_to,'','',1);
		}

		if ($cbo_item_category_id !="") $category_cond= " and item_category in($cbo_item_category_id)"; else $category_cond=" and item_category in(5,6,7,19,20,22,23,39)";

		$issue_sql="select prod_id, cons_quantity from inv_transaction where status_active=1 and company_id in($cbo_company_name) and transaction_type=2 and transaction_date between '$txt_date_from' and '$txt_date_to' $category_cond ";
		//echo $issue_sql;//die;
		$issue_result=sql_select($issue_sql);
		$issue_data=array();
		foreach($issue_result as $row)
		{
			$issue_data[$row[csf("prod_id")]]+=$row[csf("cons_quantity")];
		}

		//echo $cbo_company_name."===".$cbo_store_name;die;

		$sql_cond=$date_cond="";

		if ($cbo_item_category_id !="") $sql_cond= " and b.item_category in($cbo_item_category_id)"; else $sql_cond.=" and b.item_category in(5,6,7,19,20,22,23,39)";
		if ($txt_date_from !="" && $txt_date_to !="") $date_cond= "   and b.transaction_date between '$txt_date_from' and '$txt_date_to'"; else $date_cond.=" and b.item_category in(5,6,7,19,20,22,23,39)";
		//if ($item_group_id !="") $sql_cond.=" and b.item_group_id='$item_group_id'";
		//if($cbo_store_name>0) $sql_cond.=" and a.store_id='$cbo_store_name'";

		$sql=" select   a.company_id, b.item_category, b.prod_id, c.item_group_id, c.sub_group_name, c.product_name_details, b.transaction_type, d.item_name, b.cons_quantity as issue_qty,  b.cons_amount   as issue_amount, a.issue_purpose
		from inv_issue_master a,  inv_transaction b,  product_details_master c,  lib_item_group d
	 	where a.id = b.mst_id  and b.prod_id = c.id  and c.item_group_id = d.id and b.transaction_type = 2 and a.entry_form in (5, 21) and a.company_id  in($cbo_company_name) $date_cond and a.status_active = 1  and a.is_deleted = 0  and b.status_active = 1  and b.is_deleted = 0  and c.status_active = 1  and c.is_deleted = 0  and d.status_active = 1  and d.is_deleted = 0 $sql_cond order by a.issue_purpose, b.prod_id";
	 	//$item_category_id $group_id $store_name  $search_cond

	 	//echo $sql;//die;
		$result = sql_select($sql);
		$all_data=array();
		$company_arr = sql_select("select id, company_name from lib_company where id in($cbo_company_name)");
		$all_prod_id=array();
		foreach($result as $row)
		{

			$all_prod_id[$row[csf("issue_purpose")]][$row[csf("prod_id")]]=$row[csf("prod_id")].",";
			$all_data[$row[csf("issue_purpose")]][$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$all_data[$row[csf("issue_purpose")]][$row[csf("prod_id")]]["item_category"]=$row[csf("item_category")];
			$all_data[$row[csf("issue_purpose")]][$row[csf("prod_id")]]["item_group_id"]=$row[csf("item_group_id")];
			$all_data[$row[csf("issue_purpose")]][$row[csf("prod_id")]]["sub_group_name"]=$row[csf("sub_group_name")];
			$all_data[$row[csf("issue_purpose")]][$row[csf("prod_id")]]["product_name_details"]=$row[csf("product_name_details")];
			$all_data[$row[csf("issue_purpose")]][$row[csf("prod_id")]]["item_group_name"]=$row[csf("item_name")];
			$all_data[$row[csf("issue_purpose")]][$row[csf("prod_id")]]["issue_qty"]+=$row[csf("issue_qty")];
			$all_data[$row[csf("issue_purpose")]][$row[csf("prod_id")]]["issue_amount"]+=$row[csf("issue_amount")];
			$all_data[$row[csf("issue_purpose")]][$row[csf("prod_id")]]["issue_purpose"]=$row[csf("issue_purpose")];

		}


		//echo "<pre>";print_r($all_prod_id);//die;


		//var_dump($sql_pipe_line);die;
		$i=1;
		ob_start();
		?>
		<div align="center" style="height:auto; margin:0 auto; padding:0; width:1050px">
			<table width="1030" cellpadding="0" cellspacing="0" id="caption" align="left">
				<thead>
					<tr style="border:none;">
						<td colspan="9" align="center" class="form_caption" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td>
					</tr>
					<tr style="border:none;">
						<td colspan="9" class="form_caption" align="center" style="border:none; font-size:14px;">
						   <b>Company Name :
						   	<?
						   		foreach ($company_arr as $company){
						   			echo chop($company[csf("company_name")].', ',",");
						   		}

					   		?></b>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="9" align="center" class="form_caption" style="border:none;font-size:12px; font-weight:bold">
							<? if($txt_date_from!="") echo "<span style='color:black;font-weight:bold;'>Report Date From :</span> <em>".change_date_format($txt_date_from,'dd-mm-yyyy')."</em>  <span style='color:black;font-weight:bold;'>To</span>  <em>".change_date_format($txt_date_to,'dd-mm-yyyy')."</em>";?>
						</td>
					</tr>
				</thead>
			</table>
			<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="1030" rules="all" id="rpt_table_header" align="left">
				<thead>
					<tr>
						<th width="50">SL</th>
						<th width="80">Purpose</th>
						<th width="140">Item Category</th>
						<th width="130">Item Group</th>
						<th width="130">Sub Group</th>
						<th width="170">Item Description</th>
                        <th width="100">Issue Qnty</th>
						<th width="100">Issue Avg Rate</th>
                        <th>Issue Amount(Tk)</th>
					</tr>
				</thead>
			</table>
			<div style="width:1050px; max-height:250px; overflow-y:scroll; overflow-x:hidden;" id="scroll_body">
			<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="1030" rules="all" align="left">
			<?

				foreach($all_data as $issue_purpose_data )
				{
					foreach ($issue_purpose_data as  $row) {

						if($i%2==0) $bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
						$rate = $row[("issue_amount")]/$row[("issue_qty")];
						$total_quantity += $row[("issue_qty")];
						$total_amount += $row[("issue_amount")];
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="50" align="center" ><? echo $i; ?>&nbsp;</td>
							<td width="80" align="center"><? echo $general_issue_purpose[$row["issue_purpose"]]; ?>
							</td>
							<td width="140"><? echo $item_category[$row[("item_category")]]; ?></td>
							<td width="130"><? echo $row[("item_group_name")]; ?></td>
							<td width="130"><? echo $row[("sub_group_name")]; ?></td>
							<td width="170"><? echo $row[("product_name_details")]; ?></td>
		                    <td width="100" align="right" title="<? echo "prod_ids == ".$row["prod_id"]; ?>">
								<a href="##"  onClick="issue_qnty_dtls(<? echo $row["prod_id"]; ?>, '<? echo $date_range?>', '<? echo $row["issue_purpose"];?>',
									'issue_quantity_dtls_popup')"><? echo number_format($row[("issue_qty")],4); ?></a>
							</td>
							<td width="100" align="right"><? echo number_format($rate,3);?></td>
		                    <td align="right"><? echo number_format($row[("issue_amount")],4); ?></td>
						</tr>

						<?
						$i++;
					}//#B3D97D
					?>
					<tr bgcolor="#C3D6BD">
						<td colspan="6" align="right" > <strong>Sub Total: </strong></td>
						<td align="right" > <strong><? echo number_format($total_quantity,4); ?> </strong></td>
						<td align="right" >&nbsp;</td>
						<td align="right" > <strong><? echo number_format($total_amount,4); ?> </strong></td>
					</tr>
					<?
					$grand_total_quantity+=$total_quantity;
					$grand_total_amount+=$total_amount;
					$total_quantity="";
					$total_amount="";

				}
			?>
			<tr bgcolor="#FFE599">
				<td colspan="6" align="right" > <strong>Grand Total: </strong></td>
				<td align="right" > <strong><? echo number_format($grand_total_quantity,4); ?> </strong></td>
				<td align="right" >&nbsp;</td>
				<td align="right" > <strong><? echo number_format($grand_total_amount,4); ?> </strong></td>
			</tr>
			</table>
			</div>
			<!-- <table width="930" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
				<tfoot>
					<tr>
						<th width="50">&nbsp; </th>
						<th width="80">&nbsp; </th>
						<th width="150">&nbsp; </th>
						<th width="150">&nbsp; </th>
						<th width="180" style="text-align: right">Total: </th>
						<th width="100"  style="text-align: right" id="value_tot_open_bl"><? echo number_format($all_data[$row[csf("prod_id")]]["opening_qty"],2); ?>&nbsp;</th>
						<th width="100" id="value_tot_pipe_qty" style="text-align: right"><? echo number_format($pipe_qty,2); ?>&nbsp;</th>
						<th id="value_tot_qty" style="text-align: right"><? echo number_format($tot_qnty,2); ?>&nbsp;</th>
					</tr>
				</tfoot>
			</table> -->
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
	    echo "$html**$filename**$report_type";
	    exit();
	}

	
?>
