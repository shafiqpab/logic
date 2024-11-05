<?
	header('Content-type:text/html; charset=utf-8');
	session_start();
	include('../../../../includes/common.php');

	$user_id = $_SESSION['logic_erp']["user_id"];
	if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
	$permission=$_SESSION['page_permission'];
	$data=$_REQUEST['data'];
	$action=$_REQUEST['action'];


	if ($action=="load_multiselect_item_group")
	{
		//echo $data;die;
		$data=explode('_',$data);
		$com_id=$data[0];
		$cat_id=$data[1];
		$sql_cond="";
		//if($com_id!="") $sql_cond=" and a.company_id in($com_id)";
		if($cat_id!="") $sql_cond.=" and item_category in($cat_id)";
		
		echo create_drop_down( "cbo_item_group", 120, "SELECT id,item_name from  lib_item_group where status_active=1 and is_deleted=0 $sql_cond","id,item_name", 1, "--Select Item Group--", 1, "",0 );		
	}

	if ($action=="pi_dtls_popup")
	{
		echo load_html_head_contents("PI Details", "../../../../", 1, 1,$unicode,'','');
		extract($_REQUEST);
		//echo $pi_data."==".$prod_id;die;
		$porduct_data_ref=explode("*",$porduct_data_all);
		$pi_data_ref=explode("_",$pi_data);
		foreach($pi_data_ref as $value)
		{
			 $data_ref=explode("*",$value);
			 $all_piId[$data_ref[0]]= $data_ref[0];
			 $pi_ref_data[$data_ref[0]]+=$data_ref[1];
		}
		$sql_pipe_line="select b.pi_id, b.item_prod_id, b.quantity, a.pi_number, d.lc_number
		from com_pi_master_details a, com_pi_item_details b, com_btb_lc_pi c, com_btb_lc_master_details d 
		where a.id=b.pi_id and b.pi_id=c.pi_id and c.com_btb_lc_master_details_id=d.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and b.item_prod_id=$prod_id and b.pi_id in(".implode(",", $all_piId).")";
		//echo $sql_pipe_line;die;
		$data_array=sql_select($sql_pipe_line);
		?>
		<div style="width:730px;"> 
		<table align="center" cellspacing="0" width="730" border="1" rules="all" class="rpt_table" >
            <thead>
                <tr>   
                    <th width="30">SL</th>
                    <th width="100" >PI Number</th>
                    <th width="100" >LC Number</th>
                    <th width="100" >Item Category</th>
                    <th width="100" >Item Group</th>
                    <th width="150">Item Description</th>
                    <th width="80">PI Qty.</th>
                    <th>Balance Qty.</th>
                </tr>
            </thead>
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
				<tr bgcolor="<? echo $bgcolor;?>">
                    <td align="center"><? $i; ?></td>
                    <td align="center"><? echo $row[csf("pi_number")]; ?></td>
                    <td align="center"><? echo $row[csf("lc_number")]; ?></td>
                    <td><?  echo $porduct_data_ref[0]; ?></td>
                    <td><?  echo $porduct_data_ref[1]; ?></td>
                    <td><?  echo $porduct_data_ref[2]; ?></td>
                    <td align="right"><?  echo number_format($row[csf("quantity")],2,'.',''); ?></td>
                    <td align="right"><?  echo number_format($pi_ref_data[$row[csf("pi_id")]],2,'.',''); ?></td>
				</tr> 
				<? 
				$i++; 
            }
            ?>
            </tbody>
		</table>
        </div>
		<?
		exit();
	}

	if ($action=="item_account_popup")
	{
		echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
		extract($_REQUEST);
		$data=explode('_',$data);
		//print_r ($data);  
		?>	
	    <script>
		 var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
		 
		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
			
		function js_set_value(id)
		{
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			var strdt=str[2];
			str=str[1];
		
			if( jQuery.inArray(  str , selected_id ) == -1 ) {
				selected_id.push( str );
				selected_name.push( strdt );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#item_account_id').val( id );
			$('#item_account_val').val( ddd );
		} 
		</script>
	     <input type="hidden" id="item_account_id" />
	     <input type="hidden" id="item_account_val" />
	 	<?
			$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
			$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
			if ($data[2]==0) $item_name =""; else $item_name =" and item_group_id in($data[2])";
			
			$sql="SELECT id,item_account,item_category_id,item_group_id,item_description,supplier_id from  product_details_master where company_id in($data[0]) and item_category_id in($data[1]) $item_name and  status_active=1 and is_deleted=0"; 
			$arr=array(1=>$item_category,2=>$itemgroupArr,4=>$supplierArr);
			echo  create_list_view("list_view", "Item Account,Item Category,Item Group,Item Description,Supplier,Product ID", "70,110,150,150,100,70","780","400",0, $sql , "js_set_value", "id,item_description", "", 0, "0,item_category_id,item_group_id,0,supplier_id,0", $arr , "item_account,item_category_id,item_group_id,item_description,supplier_id,id", "",'setFilterGrid("list_view",-1);','0,0,0,0,0,0','',1) ;
			exit();
	}

	if($action=="generate_report")
	{
		$process = array(&$_POST);
		extract(check_magic_quote_gpc($process));
		$report_title=str_replace("'","",$report_title);
		$cbo_company_name=str_replace("'","",$cbo_company_name);
		$item_group_id=str_replace("'","",$cbo_item_group);
		$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
		$txt_item=str_replace("'","",$txt_item_acc); 
		$item_account_id=str_replace("'","",$txt_item_account_id); 
		$txt_date=str_replace("'","",$txt_date);
		$prev_date=add_date($txt_date,-90);
		if($db_type==2) $prev_date=change_date_format($prev_date,'','',1);
		if($db_type==0) 
		{
			$txt_date=change_date_format($txt_date,'yyyy-mm-dd');
		}
		else if($db_type==2) 
		{
			$txt_date=change_date_format($txt_date,'','',1);
		}
		
		$sql_cond="";		
		if ($cbo_item_category_id !="") $sql_cond= " and b.item_category_id in($cbo_item_category_id)"; else $sql_cond.=" and b.item_category_id in(5,6,7,19,20,22,23,39)";		

		if ($item_account_id !="") $item_des_cond=" and b.id in($item_account_id)"; 
		if ($cbo_item_group !="") $sql_cond=" and b.item_group_id in($cbo_item_group)";		
		if($db_type==0) 
		{
			$txt_date=change_date_format($txt_date,'yyyy-mm-dd');
		}
		else if($db_type==2) 
		{
			$txt_date=change_date_format($txt_date,'','',1);
		}
		if ($txt_date !="") $date_cond=" and a.transaction_date<'$txt_date'";		

		$sql="select a.company_id, a.prod_id, a.item_category, a.store_id, b.item_group_id, b.item_description, c.id as lib_item_group_id, c.item_name, b.unit_of_measure,
		sum((case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end) -
		(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as balance
	 	from inv_transaction a, product_details_master b, lib_item_group c
	 	where a.prod_id=b.id and b.item_group_id=c.id and a.company_id in($cbo_company_name) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond $item_des_cond $date_cond
	 	group by a.company_id, a.prod_id, a.item_category, a.store_id, b.item_group_id, b.item_description, c.id, c.item_name, b.unit_of_measure
	 	order by a.prod_id"; 
	 	//$item_category_id $group_id $store_name  $search_cond 
	 	
	 	//echo $sql;//die;
		$result = sql_select($sql); 
		
		$company_arr = sql_select("select id, company_name from lib_company where id in($cbo_company_name)");
		$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
		$store_name_arr=return_library_array("select id, store_name from  lib_store_location","id","store_name");

		$store_wise_qnty=array(); 
		$all_data=array();
		$all_company_ids=array();
		$all_store_ids=array();
		foreach($result as $row)
		{
			$all_company_ids[$row[csf("company_id")]]=$row[csf("company_id")];
			$all_store_ids[$row[csf("company_id")]][$row[csf("store_id")]]=$row[csf("store_id")];

			$all_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$all_data[$row[csf("prod_id")]]["item_category"]=$row[csf("item_category")];
			$all_data[$row[csf("prod_id")]]["item_group_id"]=$row[csf("item_group_id")];
			$all_data[$row[csf("prod_id")]]["item_description"]=$row[csf("item_description")];
			$all_data[$row[csf("prod_id")]]["item_group_name"]=$row[csf("item_name")];
			$all_data[$row[csf("prod_id")]]["cons_uom"]=$row[csf("unit_of_measure")];
			$all_data[$row[csf("prod_id")]]["stock"]+=$row[csf("balance")];

			$store_wise_qnty[$row[csf("prod_id")]][$row[csf("company_id")]][$row[csf("store_id")]]+=$row[csf("balance")];
		}
		
		
		if ($txt_date !="") $date_cond=" and a.transaction_date='$txt_date'";		
		$sql_cr="select a.prod_id, sum((case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end) - (case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as balance
	 	from inv_transaction a, product_details_master b, lib_item_group c
	 	where a.prod_id=b.id and b.item_group_id=c.id and a.company_id in($cbo_company_name) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond $item_des_cond $date_cond
	 	group by a.prod_id";
		$result_cr = sql_select($sql_cr);
		foreach($result_cr as $row)
		{
			$all_data[$row[csf("prod_id")]]["stock_cr"]+=$row[csf("balance")];
		}
		
		/*echo '<pre>';
  		print_r($all_store_ids);die;*/	
			
		$i=1;
		ob_start();
		$count_col=0;	
		foreach ($all_store_ids as $value) {
			foreach ($value as $val) {
				$count_col++;
			}
		}
		//echo $count_col;die;
		$table_with=920+(80*$count_col);
		$div_with=$table_with+20;
		?>
		<div align="center" style="height:auto; margin:0 auto; padding:0; width:<? echo $div_with; ?>px">
			<table width="<? echo $table_with; ?>" cellpadding="0" cellspacing="0" id="caption" align="left">
				<thead>
					<tr style="border:none;">
						<td colspan="10" align="center" class="form_caption" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td> 
					</tr>
					<tr style="border:none;">
						<td colspan="10" class="form_caption" align="center" style="border:none; font-size:14px;">
						   <b>Company Name : 
						   	<? 
						   		foreach ($company_arr as $company){
						   			echo chop($company[csf("company_name")].', ',",");
						   		}

					   		?></b>                               
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="10" align="center" class="form_caption" style="border:none;font-size:12px; font-weight:bold">
							<? if($txt_date!="") echo "Report Date : ".change_date_format($txt_date,'dd-mm-yyyy');?>
						</td>
					</tr>
				</thead>
			</table>
			<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $table_with; ?>" rules="all" id="rpt_table_header" align="left">
				<thead>
					<tr>
						<th rowspan="2" width="50">SL</th>
						<th rowspan="2" width="60">Prod. ID</th>
						<th rowspan="2" width="150">Item Category</th>
						<th rowspan="2" width="150">Item Group</th>
						<th rowspan="2" width="180">Item Description</th>
                        <th rowspan="2" width="100">UOM</th>
                        <?
                        foreach ($all_company_ids as $key => $value) 
                        {
                    		?>
                    		<th colspan="<? echo count($all_store_ids[$value]) ?>" ><? echo $company_library[$value]; ?></th>
                    		<?
                        }	
                        ?>
						<th rowspan="2" width="100">Opening Qty</th>
                        <th rowspan="2">Closing Qty</th>
					</tr> 	
					<tr>
						<?
						foreach ($all_company_ids as $com_id => $value)
						{
							foreach ($all_store_ids[$com_id] as $store_id_key => $store_id) 
							{
								?>
								<th width="80"><? echo $store_name_arr[$store_id]; ?></th>
								<?
							}
						}	
						?>	
					</tr>				
				</thead>
			</table>
			<div style="width:<? echo $div_with; ?>px; max-height:250px; overflow-y:scroll; overflow-x:hidden;" id="scroll_body"> 
			<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<? echo $table_with; ?>" rules="all" align="left">
			<?
				foreach($all_data as $prod_id => $row)
				{
					if(number_format($row[("stock")],4,'.','')!=0)
					{
						if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="50" align="center"><? echo $i; ?>&nbsp;</td>
							<td width="60" align="center"><? echo $row["prod_id"]; ?>&nbsp;</td>
							<td width="150"><? echo $item_category[$row[("item_category")]]; ?></td>
							<td width="150"><? echo $row[("item_group_name")]; ?></td>
							<td width="180"><? echo $row[("item_description")]; ?></td>
							<td width="100"><? echo $unit_of_measurement[$row[("cons_uom")]]; ?></td>
							<?	 
							$total_qty = ""; 
							foreach ($all_company_ids as $com_id => $value)
							{
								foreach ($all_store_ids[$com_id] as $store_id_key => $store_id) 
								{
									?>
									<td width="80" align="right"><? echo number_format($store_wise_qnty[$prod_id][$com_id][$store_id_key],4,'.',''); 
									$total_qty+=$store_wise_qnty[$prod_id][$com_id][$store_id_key];
									$store_id_total[$store_id]+=$store_wise_qnty[$prod_id][$com_id][$store_id_key];
									?></td>
									<?
								}						
							}
							?>						
							<td align="right" width="100"><? echo number_format($total_qty,4,'.',''); ?></td>
                            <td align="right"><? $closing_qnty=$total_qty+$row[("stock_cr")]; echo number_format($closing_qnty,4,'.',''); $grand_total_closing_qnty+=$closing_qnty; ?></td>
						</tr>
						<?
						$i++; 
					}
				}
			?>
			</table>
			</div>
			<table width="<? echo $table_with; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
				<tfoot>
					<tr>
						<th width="50">&nbsp; </th>
						<th width="60">&nbsp; </th>
						<th width="150">&nbsp; </th>
						<th width="150">&nbsp; </th>
						<th width="180">&nbsp; </th>
						<th width="100" style="text-align: right">Total: </th>
						<?	
						foreach ($all_company_ids as $com_id => $value)
						{
							foreach ($all_store_ids[$com_id] as $store_id_key => $store_id)
							{
								?>
								<th width="80" align="right"><? echo number_format($store_id_total[$store_id],4,'.',''); 
								$sub_total_qty+=$store_id_total[$store_id];?></th>
								<?
							}							
						}
						?>
						<th align="right" width="100"><? echo number_format($sub_total_qty,4,'.',''); ?>&nbsp; </th>
                        <th align="right"><? echo number_format($grand_total_closing_qnty,4,'.',''); ?>&nbsp; </th>				
					</tr>
				</tfoot>
			</table>
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
	if($action=="generate_report2") //show 2
	{
		$process = array(&$_POST);
		extract(check_magic_quote_gpc($process));
		$report_title=str_replace("'","",$report_title);
		$cbo_company_name=str_replace("'","",$cbo_company_name);
		$item_group_id=str_replace("'","",$cbo_item_group);
		$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
		$txt_item=str_replace("'","",$txt_item_acc); 
		$item_account_id=str_replace("'","",$txt_item_account_id); 
		$txt_date=str_replace("'","",$txt_date);
		$prev_date=add_date($txt_date,-90);
		if($db_type==2) $prev_date=change_date_format($prev_date,'','',1);
		if($db_type==0)
		{
			$txt_date=change_date_format($txt_date,'yyyy-mm-dd');
		}
		else if($db_type==2)
		{
			$txt_date=change_date_format($txt_date,'','',1);
		}
		
		$sql_cond="";
		if ($cbo_item_category_id !="") $sql_cond= " AND b.ITEM_CATEGORY_ID IN($cbo_item_category_id)"; else $sql_cond.=" AND b.ITEM_CATEGORY_ID IN(5,6,7,19,20,22,23,39)";
		if ($item_account_id !="") $item_des_cond=" AND b.ID IN($item_account_id)";
		if ($cbo_item_group !="") $sql_cond=" AND b.ITEM_GROUP_ID IN($cbo_item_group)";
		if($db_type==0)
		{
			$txt_date=change_date_format($txt_date,'yyyy-mm-dd');
		}
		else if($db_type==2)
		{
			$txt_date=change_date_format($txt_date,'','',1);
		}
		if ($txt_date !="") $date_cond=" AND a.TRANSACTION_DATE<'$txt_date'";
		
		$company_library=return_library_array( "SELECT ID,COMPANY_NAME FROM LIB_COMPANY", "ID", "COMPANY_NAME");
		$company_arr = sql_select("SELECT ID, COMPANY_NAME FROM LIB_COMPANY WHERE ID IN($cbo_company_name)");

		$sql = "SELECT b.ITEM_CATEGORY_ID, b.ITEM_GROUP_ID, b.ITEM_DESCRIPTION, c.ITEM_NAME, b.UNIT_OF_MEASURE, 
		listagg(cast(b.ID AS varchar(4000)),',') within group(ORDER BY b.ID) AS PROD_ID
		FROM PRODUCT_DETAILS_MASTER b, LIB_ITEM_GROUP c 
		WHERE b.ITEM_GROUP_ID=c.ID AND b.COMPANY_ID IN($cbo_company_name) AND b.STATUS_ACTIVE=1 AND b.IS_DELETED=0 
		AND c.STATUS_ACTIVE=1 AND c.IS_DELETED=0 and b.current_stock>0.00001 $sql_cond
		GROUP BY B.ITEM_CATEGORY_ID, B.ITEM_GROUP_ID, B.ITEM_DESCRIPTION, C.ITEM_NAME, B.UNIT_OF_MEASURE";	 	
	 	// echo $sql; die;
		$result = sql_select($sql);
		// echo "<pre>"; print_r($result); die;
		$all_data=array();
		foreach($result as $row)
		{
			$all_data[$row["ITEM_CATEGORY_ID"]][$row["ITEM_GROUP_ID"]][$row["ITEM_DESCRIPTION"]][$row["UNIT_OF_MEASURE"]]["PROD_ID"].=$row["PROD_ID"].",";
			$all_data[$row["ITEM_CATEGORY_ID"]][$row["ITEM_GROUP_ID"]][$row["ITEM_DESCRIPTION"]][$row["UNIT_OF_MEASURE"]]["ITEM_GROUP_NAME"]=$row["ITEM_NAME"];
		}
		// echo "<pre>"; print_r($all_data); die;

		$sql_cr = "SELECT b.COMPANY_ID, b.ITEM_CATEGORY_ID, b.ITEM_GROUP_ID, b.ITEM_DESCRIPTION, b.UNIT_OF_MEASURE, a.STORE_ID,
		SUM((case when a.TRANSACTION_TYPE IN(1,4,5) then a.CONS_QUANTITY else 0 end) - (case when a.TRANSACTION_TYPE IN(2,3,6) then a.CONS_QUANTITY else 0 end)) AS BALANCE 
		FROM INV_TRANSACTION a, PRODUCT_DETAILS_MASTER b
		WHERE a.PROD_ID=b.ID AND a.COMPANY_ID IN($cbo_company_name) AND a.STATUS_ACTIVE=1 AND a.IS_DELETED=0 AND b.STATUS_ACTIVE=1 AND b.IS_DELETED=0 and b.current_stock>0.00001 $sql_cond $date_cond
		GROUP BY B.COMPANY_ID, B.ITEM_CATEGORY_ID, B.ITEM_GROUP_ID, B.ITEM_DESCRIPTION, B.UNIT_OF_MEASURE, a.STORE_ID";
		$result_cr = sql_select($sql_cr);
		$all_store_ids=array();
		$all_store_data = array();
		foreach($result_cr as $row)
		{
			if($row["STORE_ID"])
			{
				$all_store_ids[$row["STORE_ID"]]=$row["STORE_ID"];
				$all_store_data[$row["ITEM_CATEGORY_ID"]][$row["ITEM_GROUP_ID"]][$row["ITEM_DESCRIPTION"]][$row["UNIT_OF_MEASURE"]][$row["STORE_ID"]]+=$row["BALANCE"];
			}
		}
		// echo "<pre>"; print_r($all_store_data); die;
		$sql_store="select ID, STORE_NAME, COMPANY_ID from LIB_STORE_LOCATION where ID IN(".implode(",", $all_store_ids).")";
		$result_store = sql_select($sql_store);
		$store_datas=array();
		foreach($result_store as $row)
		{
			$store_datas[$row["ID"]]["STORE_NAME"]=$row["STORE_NAME"];
			$store_datas[$row["ID"]]["COMPANY_ID"]=$row["COMPANY_ID"];
		}
		// echo "<pre>"; print_r($store_datas); die;
		$count_col=0;
		foreach ($all_store_ids as $value) {
			$count_col++;
		}
		// echo $count_col;die;
		$table_with=700+(80*$count_col);
		$div_with=$table_with+20;
		ob_start();
		?>
		<div align="center" style="height:auto; margin:0 auto; padding:0; width:<? echo $div_with; ?>px">
			<table width="<? echo $table_with; ?>" cellpadding="0" cellspacing="0" id="caption" align="left">
				<thead>
					<tr style="border:none;">
						<td colspan="10" align="center" class="form_caption" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td> 
					</tr>
					<tr style="border:none;">
						<td colspan="10" class="form_caption" align="center" style="border:none; font-size:14px;">
							<b>Company Name : 
							<? 
								foreach ($company_arr as $company){
									echo chop($company["COMPANY_NAME"].', ',",");
								}
							?></b>                               
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="10" align="center" class="form_caption" style="border:none;font-size:12px; font-weight:bold">
							<? if($txt_date!="") echo "Report Date : ".change_date_format($txt_date,'dd-mm-yyyy');?>
						</td>
					</tr>
				</thead>
			</table>
			<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<?= $table_with;?>" rules="all" id="rpt_table_header" align="left">
				<thead>
					<tr>
						<th rowspan="2" width="30">SL</th>
						<th rowspan="2" width="100">Prod. ID</th>
						<th rowspan="2" width="120">Item Category</th>
						<th rowspan="2" width="120">Item Group</th>
						<th rowspan="2" width="150">Item Description</th>
						<th rowspan="2" width="60">UOM</th>
						<?
						foreach ($all_store_ids as $value) {
							?>
							<th width="80"><? echo $company_library[$store_datas[$value]["COMPANY_ID"]]; ?></th>
							<?
						}
						?>
						<th rowspan="2">Total Qty</th>
					</tr>
					<tr>
						<?
						foreach ($all_store_ids as $value) {
							?>
							<th width="80"><? echo $store_datas[$value]["STORE_NAME"]; ?></th>
							<?
						}
						?>
					</tr>		
				</thead>
			</table>
			<div style="width:<? echo $div_with; ?>px; max-height:250px; overflow-y:scroll; overflow-x:hidden;" id="scroll_body"> 
				<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<? echo $table_with; ?>" rules="all" align="left">
					<tbody>
						<?
						$sl=1;
						$store_wise_total_data=array();
						foreach($all_data as $cat_id=>$cat_val)
						{
							foreach($cat_val as $group_id=>$group_val)
							{
								foreach($group_val as $description=>$des_val)
								{
									foreach($des_val as $uom=>$uom_val)
									{
										if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
											<td width="30" align="center"><? echo $sl; ?></td>
											<td width="100" align="center" style="word-break: break-all;"><? echo chop($uom_val["PROD_ID"],","); ?></td>
											<td width="120" align="left" style="word-break: break-all;"><? echo $item_category[$cat_id]; ?></td>
											<td width="120" align="left" style="word-break: break-all;"><? echo $uom_val["ITEM_GROUP_NAME"]; ?></td>
											<td width="150" align="left" style="word-break: break-all;"><? echo $description; ?></td>
											<td width="60" align="center" style="word-break: break-all;"><? echo $unit_of_measurement[$uom]; ?></td>
											<?
											$total_qty=0;
											foreach ($all_store_ids as $value) {
												?>
												<td align="right" width="80"><? echo number_format($all_store_data[$cat_id][$group_id][$description][$uom][$value],2);
												$total_qty+=$all_store_data[$cat_id][$group_id][$description][$uom][$value];
												?></td>
												<?
												$store_wise_total_data[$value]+=$all_store_data[$cat_id][$group_id][$description][$uom][$value];
											}
											?>
											<td align="right"><? echo number_format($total_qty,2); ?></td>
										</tr>
										<?
										$sl++;
									}
								}
							}					
						}
						?>
					</tbody>
				</table>
			</div>
			<table width="<? echo $table_with; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
				<tfoot>
					<tr>
						<th width="30">&nbsp; </th>
						<th width="100">&nbsp; </th>
						<th width="120">&nbsp; </th>
						<th width="120">&nbsp; </th>
						<th width="150">&nbsp; </th>
						<th width="60" style="text-align: right">Total: </th>
						<?
						$all_store_total=0;
						foreach ($all_store_ids as $value)
						{
							?>
							<th align="right" width="80"><? echo number_format($store_wise_total_data[$value],2); $all_store_total += $store_wise_total_data[$value]; ?>&nbsp; </th>
							<?
						}
						?>
						<th align="right"><? echo number_format($all_store_total,2); ?>&nbsp; </th>
					</tr>
				</tfoot>
			</table>
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