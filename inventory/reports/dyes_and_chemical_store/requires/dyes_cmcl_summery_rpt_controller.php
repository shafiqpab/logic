<?
	header('Content-type:text/html; charset=utf-8');
	session_start();
	include('../../../../includes/common.php');

	$user_id = $_SESSION['logic_erp']["user_id"];
	if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
	$permission=$_SESSION['page_permission'];
	$data=$_REQUEST['data'];
	$action=$_REQUEST['action'];


	if ($action=="load_drop_down_store")
	{
		//echo $data;die;
		$data=explode('_',$data);
		$com_id=$data[0];
		$cat_id=$data[1];
		$sql_cond="";
		if($com_id!="") $sql_cond=" and a.company_id in($com_id)";
		if($cat_id!="") $sql_cond.=" and b.category_type in($cat_id)";
		
		echo create_drop_down( "cbo_store_name", 120, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 $sql_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select Store--", 1, "",0 );		
		
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
		$cbo_store_name=str_replace("'","",$cbo_store_name);
		$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
		$item_group_id=str_replace("'","",$item_group_id);
		$txt_item=str_replace("'","",$txt_item_acc); 
		$item_account_id=str_replace("'","",$txt_item_account_id);
		$cbo_store_wise=str_replace("'","",$cbo_store_wise);
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

		if ($cbo_item_category_id !="") $category_cond= " and item_category in($cbo_item_category_id)"; else $category_cond=" and item_category in(5,6,7,19,20,22,23,39)";

		$issue_sql="select prod_id, cons_quantity from inv_transaction where status_active=1 and company_id in($cbo_company_name) and transaction_type=2 and transaction_date between '$prev_date' and '$txt_date' $category_cond ";
		//echo $issue_sql;//die;
		$issue_result=sql_select($issue_sql);
		$issue_data=array();
		foreach($issue_result as $row)
		{
			$issue_data[$row[csf("prod_id")]]+=$row[csf("cons_quantity")];
		}

		//echo $cbo_company_name."===".$cbo_store_name;die;
		
		$sql_cond="";
		
		if ($cbo_item_category_id !="") $sql_cond= " and b.item_category_id in($cbo_item_category_id)"; else $sql_cond.=" and b.item_category_id in(5,6,7,19,20,22,23,39)";
		if ($item_group_id !="") $sql_cond.=" and b.item_group_id='$item_group_id'";
		if($cbo_store_name>0) $sql_cond.=" and a.store_id='$cbo_store_name'";
		
		if ($item_account_id !="") $item_des_cond=" and b.id in($item_account_id)"; 

		if ($cbo_store_name==0)  $store_id="";  else  $store_id=" and a.store_id=$cbo_store_name "; 
		$sql="select a.id, a.prod_id, a.item_category, a.store_id, a.pi_wo_batch_no, a.receive_basis, b.item_group_id, b.item_description, b.sub_group_name, c.id as lib_item_group_id, c.item_name, a.transaction_type,
		(case when a.transaction_date<'".$txt_date."' and a.transaction_type in(1,4,5) then a.cons_quantity else 0 end) as opening_rcv_qty,
		(case when a.transaction_date<'".$txt_date."' and a.transaction_type in(2,3,6) then a.cons_quantity else 0 end) as opening_issue_qty, 
		(case when a.transaction_type=1 then a.cons_quantity else 0 end) as cons_quantity
	 	from inv_transaction a, product_details_master b, lib_item_group c
	 	where a.prod_id=b.id and b.item_group_id=c.id and a.company_id in($cbo_company_name) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond  $item_des_cond order by  a.prod_id"; 
	 	//$item_category_id $group_id $store_name  $search_cond 
	 	
	 	//echo $sql;//die;
		$result = sql_select($sql);
		$all_pi_id=''; 
		$mrr_pi_qnty=array();
		$all_data=array();
		$company_arr = sql_select("select id, company_name from lib_company where id in($cbo_company_name)");
		$all_prod_id=array(); 
		foreach($result as $row)
		{

			if($row[csf("receive_basis")]==1 && $row[csf("transaction_type")]==1) 
			{
				$mrr_pi_qnty[$row[csf("pi_wo_batch_no")]][$row[csf("prod_id")]]+=$row[csf("cons_quantity")];
				//$mrr_pi_qnty[$row[csf("prod_id")]]+=$row[csf("cons_quantity")];
				if($pi_check[$row[csf("pi_wo_batch_no")]]=="")
				{							
					$pi_check[$row[csf("pi_wo_batch_no")]]=$row[csf("pi_wo_batch_no")];
					$all_pi_id.=$row[csf("pi_wo_batch_no")].",";
					$all_data[$row[csf("prod_id")]]["pi_wo_batch_no"].=$row[csf("pi_wo_batch_no")].",";
				}
			}
			
			$all_prod_id[$row[csf("prod_id")]]=$row[csf("prod_id")];
			
			$all_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$all_data[$row[csf("prod_id")]]["item_category"]=$row[csf("item_category")];
			$all_data[$row[csf("prod_id")]]["item_group_id"]=$row[csf("item_group_id")];
			$all_data[$row[csf("prod_id")]]["item_description"]=$row[csf("item_description")];
			$all_data[$row[csf("prod_id")]]["item_group_name"]=$row[csf("item_name")];
			$all_data[$row[csf("prod_id")]]["sub_group_name"]=$row[csf("sub_group_name")];
			$all_data[$row[csf("prod_id")]]["opening_qty"]+=$row[csf("opening_rcv_qty")]-$row[csf("opening_issue_qty")];
			$all_data[$row[csf("prod_id")]]["tot_cons_quantity"]+=$row[csf("cons_quantity")];

		}
		
		//echo "<pre>";print_r($all_data);die;
		$all_pi_id=chop($all_pi_id,",");
		if(count($all_prod_id)>0)
		{
			$prod_cond=" and (";
			if(count($all_prod_id)>999 && $db_type==2)
			{
				$prod_id_chank=array_chunk($all_prod_id,999);
				foreach($prod_id_chank as $prod_id)
				{
					$prod_cond.=" a.item_prod_id in(".implode(",",$prod_id).") or";
				}
				$prod_cond=chop($prod_cond,"or");
			}
			else
			{
				$prod_cond.=" a.item_prod_id in(".implode(",",$all_prod_id).")";
			}
			$prod_cond.=")";
			$sql_pipe_line="select a.pi_id, a.item_prod_id, a.quantity from com_pi_item_details a, com_btb_lc_pi b where a.pi_id=b.pi_id and a.status_active=1 and b.status_active=1 $prod_cond";
			//echo $sql_pipe_line;
			$pipe_line_result=sql_select($sql_pipe_line);
			foreach($pipe_line_result as $row)
			{
				$pipeLine_qty[$row[csf("pi_id")]][$row[csf("item_prod_id")]]+=$row[csf("quantity")];
				$pipeLine_piId[$row[csf("item_prod_id")]].=$row[csf("pi_id")].",";
			}
		}	
		
		//var_dump($sql_pipe_line);die;		
		$i=1;
		ob_start();	
		?>
		<div align="center" style="height:auto; margin:0 auto; padding:0; width:1250px">
			<table width="1230" cellpadding="0" cellspacing="0" id="caption" align="left">
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
			<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="1230" rules="all" id="rpt_table_header" align="left">
				<thead>
					<tr>
						<th width="40">SL</th>
						<th width="60">Prod. ID</th>
						<th width="150">Item Category</th>
						<th width="150">Item Group</th>
						<th width="150">Sub Group Name</th>
						<th width="150">Name Of Item</th>
                        <th width="100">Opening Qty</th>
						<th width="100">Pipe Line</th>
                        <th width="100">Total Qty</th>
						<th width="100">Last 3 Months<br/> Used Qty</th>
						<th>Surplus/<br/>Short Qty</th>
					</tr> 					
				</thead>
			</table>
			<div style="width:1250px; max-height:250px; overflow-y:scroll; overflow-x:hidden;" id="scroll_body"> 
			<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="1230" rules="all" align="left">
			<?

				foreach($all_data as $row)
				{
					if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
					//print_r($row);
					$pi_id_arr=	explode(",",chop($row["pi_wo_batch_no"],","));
					//print_r($pi_id_arr); echo "<br/>";
					$pipe_qty=0;$popup_piId="";
					$pi_id_all=explode(",",chop($pipeLine_piId[$row["prod_id"]],","));
					foreach($pi_id_all as $pi_id)
					{
						if($mrr_pi_qnty[$pi_id][$row["prod_id"]]<$pipeLine_qty[$pi_id][$row["prod_id"]])
						{
							$bal_qnty=$pipeLine_qty[$pi_id][$row["prod_id"]]-$mrr_pi_qnty[$pi_id][$row["prod_id"]];
							$popup_piId.=$pi_id."*".$bal_qnty."*".$row["prod_id"]."_";
						}
						if($pipeLine_qty[$pi_id][$row["prod_id"]])
						{
							$pipe_qty+=$pipeLine_qty[$pi_id][$row["prod_id"]]-$mrr_pi_qnty[$pi_id][$row["prod_id"]];
						}
					}
					//$pipe_qty+=$pipeLine_qty[$row["prod_id"]]-$mrr_pi_qnty[$row["prod_id"]];
					if($pipe_qty<0) $pipe_qty=0;
					$tot_qnty=$row[("opening_qty")]+$pipe_qty;
					$iss_qnty=$issue_data[$row["prod_id"]];
					$surplus=$tot_qnty-$iss_qnty;
					$porduct_data_all=$item_category[$row[("item_category")]]."*".$row[("item_group_name")]."*".$row[("item_description")];
					?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="40" align="center" title="<? echo $pipeLine_qty[$row["prod_id"]]."==".$mrr_pi_qnty[$row["prod_id"]]; ?>"><? echo $i; ?>&nbsp;</td>
						<td width="60" align="center"><? echo $row["prod_id"]; ?>&nbsp;</td>
						<td width="150"><? echo $item_category[$row[("item_category")]]; ?></td>
						<td width="150"><? echo $row[("item_group_name")]; ?></td>
						<td width="150"><? echo $row[("sub_group_name")]; ?></td>
						<td width="150"><? echo $row[("item_description")]; ?></td>
	                    <td width="100" align="right"><? echo number_format($row[("opening_qty")],2); ?></td>
						<td width="100" align="right" title="<? echo chop($pipeLine_piId[$row["prod_id"]],",")."==".$popup_piId."==".$row["prod_id"]; ?>"><a href="##"  onClick="pipeline_dtls('<? echo chop($popup_piId,"_"); ?>','<? echo $row["prod_id"]; ?>','<? echo $porduct_data_all; ?>','pi_dtls_popup')"><? echo number_format($pipe_qty,2); ?></a></td>
	                    <td width="100" align="right"><? echo number_format($tot_qnty,2); ?></td>
						<td width="100" align="right"><? echo number_format($iss_qnty,2); ?></td>
						<td align="right"><? echo number_format($surplus,2); ?></td>
					</tr>
					<?
					$i++; 				
				}
			?>
			</table>
			</div>
			<table width="1230" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
				<tfoot>
					<tr>
						<th width="40">&nbsp; </th>
						<th width="60">&nbsp; </th>
						<th width="150">&nbsp; </th>
						<th width="150">&nbsp; </th>
						<th width="150">&nbsp; </th>
						<th width="150" style="text-align: right">Total: </th>
						<th width="100"  style="text-align: right" id="value_tot_open_bl"><? echo number_format($all_data[$row[csf("prod_id")]]["opening_qty"],2); ?>&nbsp;</th>
						<th width="100" id="value_tot_pipe_qty" style="text-align: right"><? echo number_format($pipe_qty,2); ?>&nbsp;</th>
						<th width="100" id="value_tot_qty" style="text-align: right"><? echo number_format($tot_qnty,2); ?>&nbsp;</th>
						<th width="100" id="value_tot_issue_qty" style="text-align: right"><? echo number_format($iss_qnty,2); ?>&nbsp;</th>
						<th id="value_tot_surplus_qty" style="text-align: right"><? echo number_format($surplus,2); ?>&nbsp;</th>
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


	if ($action=="item_group_popup")
	{
		echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
		extract($_REQUEST);
		$data=explode('_',$data);
		//print_r ($data);
		?>
		<script>
			function js_set_value(id)
			{
				document.getElementById('item_name_id').value=id;
				parent.emailwindow.hide();
			}
		</script>
		<input type="hidden" id="item_name_id" />
		<?
		if ($data[0]==0) $company_cond =""; else $company_cond =" and b.company_id in($data[0])";
		if ($data[1]==0) $item_category =""; else $item_category =" and a.item_category in($data[1]) and b.item_category_id in($data[1])";
		// $item_category;
		$sql="select a.id,a.item_name, b.sub_group_name from lib_item_group a, product_details_master b where a.id=b.item_group_id and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $item_category $company_cond"; //id=$data[1] and
	
		echo  create_list_view("list_view", "Item Name, Sub Group Name", "280,200","500","330",0, $sql , "js_set_value", "id,item_name", "", 1, "0", $arr , "item_name,sub_group_name", 		"periodical_purchase_report_controller",'setFilterGrid("list_view",-1);','0') ;
		exit();
	}
?>