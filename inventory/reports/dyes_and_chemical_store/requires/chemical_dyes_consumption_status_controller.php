<?
	header('Content-type:text/html; charset=utf-8');
	session_start();
	include('../../../../includes/common.php');
	if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
	$permission=$_SESSION['page_permission'];
	$data=$_REQUEST['data'];
	$action=$_REQUEST['action'];
	$user_id = $_SESSION['logic_erp']["user_id"];
	if ($action=="load_drop_down_store")
	{
		echo create_drop_down( "cbo_store_name", 180, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id in($data) and b.category_type in(5,6,7,23) group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select Store--", 1, "",0 );		
	}

	if($action=="generate_report")
	{ 
		$process = array(&$_POST);
		extract(check_magic_quote_gpc($process));
		$report_title=str_replace("'","",$report_title);
		$rptType=str_replace("'","",$rptType);
		$cbo_company_name=str_replace("'","",$cbo_company_name);
		$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
		$cbo_store_name=str_replace("'","",$cbo_store_name);
		$txt_date_from=str_replace("'","",$txt_date_from);
		$txt_date_to=str_replace("'","",$txt_date_to);
		if($db_type==0) 
		{
			$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
		}
		else if($db_type==2) 
		{
			$txt_date_from=change_date_format($txt_date_from,'','',1);
			$txt_date_to=change_date_format($txt_date_to,'','',1);
		}

		if ($rptType==1) // Show
		{		
			$item_group_arr = return_library_array("select id,item_name from lib_item_group where status_active=1 and is_deleted=0 and item_category in(5,6,7,23)","id","item_name");
			$supplier_arr = return_library_array("select id,supplier_name from lib_supplier","id","supplier_name");
			//$item_group_arr = return_library_array("select id,item_name from lib_item_group where status_active=1 and is_deleted=0 and item_category in(5,6,7,23)","id","item_name");
			//$sql_smv="select po_job_no, gmts_item_id, max(total_smv) keep (dense_rank last order by id) as total_smv from ppl_gsd_entry_mst where po_job_no='".$result[0][csf('job_no')]."' and status_active=1 and is_deleted=0 group by  po_job_no, gmts_item_id";
			$sql_recv="select PROD_ID, SUPPLIER_ID from INV_TRANSACTION where item_category in(5,6,7,23) and transaction_type=1 and status_active=1 and id in(select max(id) as m_id  from INV_TRANSACTION where item_category in(5,6,7,23) and transaction_type=1 and status_active=1 group by PROD_ID) and PROD_ID>0 and SUPPLIER_ID>0";
			$receive_result=sql_select($sql_recv);
			$rcv_sup_arr=array();
			foreach($receive_result as $row)
			{
				$rcv_sup_arr[$row["PROD_ID"]]=$row["SUPPLIER_ID"];
			}
			$company_name=return_field_value("company_name","lib_company","id=$cbo_company_name","company_name");
			//echo "<pre>";print_r($item_group_arr);die;
			
			$sql_cond="";
			if($cbo_item_category_id>0) $sql_cond=" and b.item_category_id=$cbo_item_category_id"; else $sql_cond=" and b.item_category_id in(5,6,7,23)";
			if($cbo_store_name>0)  $sql_cond.=" and a.store_id=$cbo_store_name";

			$issue_sql="select b.ID as PROD_ID, b.ITEM_DESCRIPTION, b.ITEM_GROUP_ID, b.UNIT_OF_MEASURE, b.ITEM_CATEGORY_ID, b.ITEM_CATEGORY_ID, b.AVG_RATE_PER_UNIT, sum(a.CONS_QUANTITY) as ISSUE_QNTY 
			from INV_TRANSACTION a, PRODUCT_DETAILS_MASTER b 
			where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.company_id in($cbo_company_name) and a.transaction_type=2 and a.transaction_date between '$txt_date_from' and '$txt_date_to' $sql_cond
			group by b.ID, b.ITEM_DESCRIPTION, b.ITEM_GROUP_ID, b.UNIT_OF_MEASURE, b.ITEM_CATEGORY_ID, b.AVG_RATE_PER_UNIT
			order by b.UNIT_OF_MEASURE, b.ID ";
			//echo $issue_sql; die;
			$issue_result=sql_select($issue_sql);
			$i=1;$k=1;
			ob_start();	
			?>
			<div align="center" style="height:auto; margin:0 auto; padding:0; width:950px">
				<table width="950" cellpadding="0" cellspacing="0" id="caption" align="left">
					<thead>
						<tr style="border:none;">
							<td colspan="9" align="center" class="form_caption" style="border:none;font-size:16px; font-weight:bold" ><?=$report_title;?></td> 
						</tr>
						<tr style="border:none;">
							<td colspan="9" class="form_caption" align="center" style="border:none; font-size:14px;"><b>Company Name : <?=$company_name;?></b></td>
						</tr>
	                    <tr style="border:none;">
							<td colspan="9" class="form_caption" align="center" style="border:none; font-size:14px;"><b>Date From <?= change_date_format($txt_date_from);?> To <?= change_date_format($txt_date_to);?></b></td>
						</tr>
					</thead>
				</table>
				<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="950" rules="all" id="rpt_table_header" align="left">
					<thead>
						<tr>
							<th width="40">SL</th>
							<th width="120">Item Category</th>
							<th width="150">Product name</th>
							<th width="150">Category/Function</th>
							<th width="70">UOM</th>
							<th width="100">Dosing</th>
	                        <th width="100">Consumption</th>
							<th width="100">Price(TK)</th>
	                        <th>Last Supplier Name</th>
						</tr> 					
					</thead>
	                <tbody>
	                <?
	                $sub_total_arr=array();$total_avg_rate=0;
	                $abc=0;
	                $subTotal=0;
					foreach($issue_result as $val)
					{
						if($i==1){
							$abc= $val["UNIT_OF_MEASURE"];
							$subTotal+=$val["ISSUE_QNTY"];
						}else if($abc== $val["UNIT_OF_MEASURE"]){

							$subTotal+=$val["ISSUE_QNTY"];
						}else{
							?>
							<tr class="tbl_bottom">
			                    <td colspan="6"><strong>Sub. Total : </strong></td>
			                    <td align="right"><? echo number_format($subTotal,2); ?></td>
			                    <td></td>
			                    <td></td>
			                </tr>
							<?
							$abc = $val["UNIT_OF_MEASURE"];
							$subTotal=$val["ISSUE_QNTY"];
						}
									
						$total_avg_rate+=$val["ISSUE_QNTY"];
						if($i%2==0) $bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
						?>
		                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
		                	<td align="center"><?= $i;?></td>
		                    <td><p><? echo $item_category[$val["ITEM_CATEGORY_ID"]]; ?>&nbsp;</p></td>
		                    <td><p><? echo $val["ITEM_DESCRIPTION"]; ?>&nbsp;</p></td>
		                    <td><p><? echo $item_group_arr[$val["ITEM_GROUP_ID"]]; ?>&nbsp;</p></td>
		                    <td align="center"><p><? echo $unit_of_measurement[$val["UNIT_OF_MEASURE"]]; ?>&nbsp;</p></td>
		                    <td></td>
		                    <td align="right"><? echo number_format($val["ISSUE_QNTY"],2); ?></td>
		                    <td align="right"><?  echo number_format($val["AVG_RATE_PER_UNIT"],2); ?></td>
		                    <td><? echo $supplier_arr[$rcv_sup_arr[$val["PROD_ID"]]];?></td>
		                </tr>
		                <?
		                
						$i++;

								
					}
					?>
					<tr class="tbl_bottom">
	                    <td colspan="6"><strong>Sub. Total : </strong></td>
	                    <td align="right"><? echo number_format($subTotal,2); ?></td>
	                    <td></td>
	                    <td></td>
	                </tr>
	             <!--    <tr class="tbl_bottom">
	                    <td colspan="6"><strong>Grand Total : </strong></td>
	                    <td align="right"><? //echo number_format($total_avg_rate,2); ?></td>
	                    <td></td>
	                    <td></td>
	                </tr> -->
							                                
			                
			                
							
	                </tbody>
				</table>
	        </div>
	        <?
	    }
	    else  // Show 2  
	    {
	    	$item_group_arr = return_library_array("select id,item_name from lib_item_group where status_active=1 and is_deleted=0 and item_category in(5,6,7,23)","id","item_name");
			$supplier_arr = return_library_array("select id,supplier_name from lib_supplier","id","supplier_name");
			//$item_group_arr = return_library_array("select id,item_name from lib_item_group where status_active=1 and is_deleted=0 and item_category in(5,6,7,23)","id","item_name");
			//$sql_smv="select po_job_no, gmts_item_id, max(total_smv) keep (dense_rank last order by id) as total_smv from ppl_gsd_entry_mst where po_job_no='".$result[0][csf('job_no')]."' and status_active=1 and is_deleted=0 group by  po_job_no, gmts_item_id";
			$sql_recv="select prod_id as PROD_ID, supplier_id as SUPPLIER_ID from inv_transaction where item_category in(5,6,7,23) and transaction_type=1 and status_active=1 and id in(select max(id) as M_ID from inv_transaction where item_category in(5,6,7,23) and transaction_type=1 and status_active=1 group by prod_id) and prod_id>0 and supplier_id>0";
			$receive_result=sql_select($sql_recv);
			$rcv_sup_arr=array();
			foreach($receive_result as $row)
			{
				$rcv_sup_arr[$row["PROD_ID"]]=$row["SUPPLIER_ID"];
			}
			$company_name=return_field_value("company_name","lib_company","id=$cbo_company_name","company_name");
			//echo "<pre>";print_r($item_group_arr);die;
			
			$sql_cond="";
			if ($cbo_item_category_id>0) $sql_cond=" and b.item_category_id=$cbo_item_category_id"; 
			else $sql_cond=" and b.item_category_id in(5,6,7,23)";
			if($cbo_store_name>0)  $sql_cond.=" and a.store_id=$cbo_store_name";

			$issue_sql="select b.ID as PROD_ID, b.item_description as ITEM_DESCRIPTION, b.item_group_id as ITEM_GROUP_ID, b.unit_of_measure as UNIT_OF_MEASURE, b.item_category_id as ITEM_CATEGORY_ID, b.avg_rate_per_unit as AVG_RATE_PER_UNIT, sum(a.cons_quantity) as ISSUE_QNTY, sum(a.cons_amount) as ISSUE_AMT  
			from inv_issue_master p, inv_transaction a, product_details_master b 
			where p.id=a.mst_id and a.prod_id=b.id and p.status_active=1 and p.is_deleted=0 and p.entry_form=298 and p.issue_purpose in(13,32,33,40,58,61,83) and a.status_active=1 and a.is_deleted=0 and b.company_id in($cbo_company_name) and a.transaction_type=2 and a.transaction_date between '$txt_date_from' and '$txt_date_to' $sql_cond
			group by b.id, b.item_description, b.item_group_id, b.unit_of_measure, b.item_category_id, b.avg_rate_per_unit
			order by b.unit_of_measure, b.id";
			//echo $issue_sql; die;
			$issue_result=sql_select($issue_sql);
			$i=1;$k=1;
			ob_start();	
			?>
			<div align="center" style="height:auto; margin:0 auto; padding:0; width:950px">
				<table width="950" cellpadding="0" cellspacing="0" id="caption" align="left">
					<thead>
						<tr style="border:none;">
							<td colspan="9" align="center" class="form_caption" style="border:none;font-size:16px; font-weight:bold" ><?=$report_title;?></td> 
						</tr>
						<tr style="border:none;">
							<td colspan="9" class="form_caption" align="center" style="border:none; font-size:14px;"><b>Company Name : <?=$company_name;?></b></td>
						</tr>
	                    <tr style="border:none;">
							<td colspan="9" class="form_caption" align="center" style="border:none; font-size:14px;"><b>Date From <?= change_date_format($txt_date_from);?> To <?= change_date_format($txt_date_to);?></b></td>
						</tr>
						<tr style="border:none;">
							<td colspan="9" class="form_caption" align="center" style="border:none; font-size:14px; color: red;"><b>This Button is only for these  Issue Purpose : Gmt wash, Rnd, Lab Test, WTP, ETP and Machine Wash</b></td>
						</tr>
					</thead>
				</table>
				<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="950" rules="all" id="rpt_table_header" align="left">
					<thead>
						<tr>
							<th width="40">SL</th>
							<th width="120">Item Category</th>
							<th width="150">Item Group</th>
							<th width="180">Item Description</th>
							<th width="70">UOM</th>
							<th width="100">Issue Qnty</th>
	                        <th width="100">Issue Avg Rate(TK)</th>
							<th>Issue Amount(Tk)</th>
						</tr> 					
					</thead>
	                <tbody>
	                <?
	                $sub_total_arr=array();$total_avg_rate=0;
	                $abc=0;
	                $subTotal=0;
	                $subTotalAmount=0;
					foreach($issue_result as $val)
					{
						$val["AVG_RATE_PER_UNIT"]=$val["ISSUE_AMT"]/$val["ISSUE_QNTY"];
						if($i==1)
						{
							$abc= $val["UNIT_OF_MEASURE"];
							$subTotal+=$val["ISSUE_QNTY"];
							$subTotalAmount+=$val["ISSUE_QNTY"]*$val["AVG_RATE_PER_UNIT"];
						}
						else if($abc== $val["UNIT_OF_MEASURE"])
						{
							$subTotal+=$val["ISSUE_QNTY"];
							$subTotalAmount+=$val["ISSUE_QNTY"]*$val["AVG_RATE_PER_UNIT"];
						}
						else
						{
							?>
							<tr class="tbl_bottom">
			                    <td colspan="5"><strong>Sub. Total : </strong></td>
			                    <td align="right"><? echo number_format($subTotal,2); ?></td>
			                    <td></td>
			                    <td align="right"><? echo number_format($subTotalAmount,2); ?></td>
			                </tr>
							<?
							$abc = $val["UNIT_OF_MEASURE"];
							$subTotal=$val["ISSUE_QNTY"];
							$subTotalAmount=$val["ISSUE_QNTY"]*$val["AVG_RATE_PER_UNIT"];
						}
									
						$total_avg_rate+=$val["ISSUE_QNTY"];
						if($i%2==0) $bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
						?>
		                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
		                	<td align="center"><?= $i;?></td>
		                    <td><p><? echo $item_category[$val["ITEM_CATEGORY_ID"]]; ?>&nbsp;</p></td>
		                    <td><p><? echo $item_group_arr[$val["ITEM_GROUP_ID"]]; ?>&nbsp;</p></td>
		                    <td><p><? echo $val["ITEM_DESCRIPTION"]; ?>&nbsp;</p></td>
		                    <td align="center"><p><? echo $unit_of_measurement[$val["UNIT_OF_MEASURE"]]; ?>&nbsp;</p></td>
		                    <td align="right"><? echo number_format($val["ISSUE_QNTY"],2); ?></td>
		                    <td align="right"><? echo number_format($val["AVG_RATE_PER_UNIT"],2); ?></td>
		                    <td align="right"><? echo number_format(($val["ISSUE_QNTY"]*$val["AVG_RATE_PER_UNIT"]),2); ?></td>
		                </tr>
		                <?		                
						$i++;								
					}
					?>
					<tr class="tbl_bottom">
	                    <td colspan="5"><strong>Sub. Total : </strong></td>
	                    <td align="right"><? echo number_format($subTotal,2); ?></td>
	                    <td></td>
	                    <td align="right"><? echo number_format($subTotalAmount,2); ?></td>
	                </tr>						
	                </tbody>
				</table>
	        </div>	
	    	<?
	    }
	
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