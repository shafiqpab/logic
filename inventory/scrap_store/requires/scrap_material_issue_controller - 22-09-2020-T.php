<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$user_id=$_SESSION['logic_erp']['user_id'];

// Credential condition
$userCredential = sql_select("select unit_id as company_id, item_cate_id, company_location_id, store_location_id from user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$location_credential_id = $userCredential[0][csf('company_location_id')];
$store_credential_id = $userCredential[0][csf('store_location_id')];

if ($company_id !='') {
    $company_credential_cond = "and id in($company_id)";
}
if ($location_credential_id !='') {
    $location_credential_cond = "and id in($location_credential_id)";
}
if ($store_credential_id !='') {
    $store_credential_cond = "and a.id in($store_credential_id)";
}

 //-------------------START ----------------------------------------

if($action === "load_drop_down_location")
{
	$ex_data = explode('_',$data);
    echo create_drop_down( "cbo_location", 160, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$ex_data[0]' $location_credential_cond order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/scrap_material_issue_controller', this.value+'_'+$ex_data[0]+'_'+$ex_data[1], 'load_drop_down_store', 'store_td');",0 );
	exit();
}

if ($action === "load_drop_down_store")
{
	$ex_data = explode("_",$data);
	echo create_drop_down( "cbo_store_id", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$ex_data[0] and a.location_id =$ex_data[1] and b.category_type=$ex_data[2] $store_credential_cond group by a.id, a.store_name order by a.store_name", "id,store_name", 1, "-- Select Store --", "", 0, "", 1 );
	exit();
}

if ($action=="itemDescription_popup")
{
	echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $cbo_purpose;die;
    ?>
    <script>
		
		var selected_id = new Array;		
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}
		
		function js_set_value( str ) 
		{
			if (str != '') str = str.split("_");
		 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );			
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
			}

			var id = ''; 
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			
			$('#hidden_prod_id').val( id );
		}
	
    </script>
    <script type="text/javascript">
    	$(function(){
    		var tableFilters = { }
	    	setFilterGrid("html_search",-1,tableFilters);
    	});
    </script>
	<input type="hidden" name="hidden_prod_id" id="hidden_prod_id" />
	<?
	$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
	$sql_issue = "select b.PROD_ID, sum(b.sales_qty) as ISSUE_QTY from inv_scrap_sales_mst a, inv_scrap_sales_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_id and a.item_category=$cbo_category_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.prod_id";
	$sql_issue_res = sql_select($sql_issue);
	$scrap_issue_qty_arr=array();
	foreach ($sql_issue_res as $row)
	{
		$scrap_issue_qty_arr[$row['PROD_ID']]=$row['ISSUE_QTY'];
	}

	$sql = "SELECT b.PRODUCT_ID, b.ITEM_GROUP_ID, b.UOM, b.COLOR, b.GSM, b.LOT, c.PRODUCT_NAME_DETAILS, sum(b.receive_qnty) as RECEIVE_QNTY
	FROM inv_scrap_receive_mst a, inv_scrap_receive_dtls b, product_details_master c
	WHERE a.id=b.mst_id and b.product_id=c.id and a.company_id=$cbo_company_id and a.item_category_id=$cbo_category_id and a.store_id=$cbo_store_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	GROUP BY b.product_id, b.item_group_id, b.uom, b.color, b.gsm, b.lot, c.product_name_details";
	$sql_res=sql_select($sql);
	//and a.store_id=$cbo_store_id
	?>

    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="770" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="100">Lot</th>
            <th width="80">Prod ID</th>
            <th width="200">Item Description</th>
            <th width="80">UOM</th>
            <th width="100">Color</th>
            <th width="80">GSM</th>
            <th>Stock</th>
        </thead>
    </table>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="770" class="rpt_table" id="html_search">
        <tbody>
        <?
        $i=1;
        foreach ($sql_res as $row)
        {
			$issue_qty = $scrap_issue_qty_arr[$row['PRODUCT_ID']];
			$stock_qty = $row['RECEIVE_QNTY']-$issue_qty;
			if($stock_qty>0)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; 
				else $bgcolor="#FFFFFF";
				?>
					<tr bgcolor="<?= $bgcolor; ?>" id="tr_<?= $i; ?>" style="text-decoration:none; cursor:pointer" onClick='js_set_value("<?= $i.'_'.$row['PRODUCT_ID']; ?>")'>
						<td width="30"><?= $i; ?></td>
						<td width="100" style="word-break: break-all;"><p><?= $row['LOT']; ?></p></td>
						<td width="80"><p><?= $row['PRODUCT_ID']; ?></p></td>
						<td width="200"><p><?= $row['PRODUCT_NAME_DETAILS']; ?></p></td>
						<td width="80" align="center"><p><?= $unit_of_measurement[$row['UOM']]; ?></p></td>
						<td width="100" align="center"><p><?= $color_arr[$row['COLOR']]; ?></p></td>
						<td width="80" align="center"><p><?= $row['GSM']; ?></p></td>
						<td align="right"><p><?= number_format($stock_qty,2); ?></p></td>
					</tr>
				<?
				$i++;
			}
        }
        ?>
        </tbody>        
    </table>

    <div style="width: 670px; text-align: center; padding-top: 5px;">
        <input type="submit" class="formbutton" id="close" style="width:80px" onClick="parent.emailwindow.hide();" value="Close"/>
    </div>    

	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<?
	exit();
}

if ($action==="show_product_dtls_listview")
{
	//echo 'system';die;
	list($company_id, $item_category_id, $prod_ids, $purpose) = explode("**", $data);
	//echo '<pre>';print_r($data);die;
	//$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
	$item_group_arr=return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$sql_issue = "select b.PROD_ID, sum(b.sales_qty) as ISSUE_QTY from inv_scrap_sales_mst a, inv_scrap_sales_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.item_category=$item_category_id and b.prod_id in($prod_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.prod_id";
	$sql_issue_res = sql_select($sql_issue);
	$scrap_issue_qty_arr=array();
	foreach ($sql_issue_res as $row)
	{
		$scrap_issue_qty_arr[$row['PROD_ID']]=$row['ISSUE_QTY'];
	}

	$sql = "SELECT b.PRODUCT_ID, b.ITEM_GROUP_ID, b.UOM, b.COLOR, b.GSM, c.PRODUCT_NAME_DETAILS, sum(b.receive_qnty) as RECEIVE_QNTY
	FROM inv_scrap_receive_mst a, inv_scrap_receive_dtls b, product_details_master c
	WHERE a.id=b.mst_id and b.product_id=c.id and a.company_id=$company_id and a.item_category_id=$item_category_id and b.PRODUCT_ID in($prod_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	GROUP BY b.product_id, b.item_group_id, b.uom, b.color, b.gsm, c.product_name_details";
	$sql_res=sql_select($sql);
	$i=1;

	foreach ($sql_res as $row) 
	{
		$issue_qty = $scrap_issue_qty_arr[$row['PRODUCT_ID']];
		$stock_qty = $row['RECEIVE_QNTY']-$issue_qty;
		?>
		<tr bgcolor="<?= $bgcolor; ?>" id="row_<?= $i; ?>">
    		
        	<td>                          
                <input type="text" name="txt_prodid[]" id="txt_prodid_<?= $i; ?>" class="text_boxes" style="width:80px" onDblClick="openmypage_ItemDescription()" placeholder="Browse" value="<?= $row['PRODUCT_ID']; ?>" readonly />
                <input type="hidden" name="dtls_update_id[]" id="dtls_update_id_<?= $i; ?>" value="<?= $row['PRODUCT_ID']; ?>"/>                      
            </td>

            <td>
            	
            	<input type="text" name="txt_itemgroup_txt[]" id="txt_itemgroup_txt_<?= $i; ?>" class="text_boxes" style="width:100px" value="<?= $item_group_arr[$row['ITEM_GROUP_ID']]; ?>" disabled="disabled" />
            	<input type="hidden" name="txt_itemgroup[]" id="txt_itemgroup_<?= $i; ?>" class="text_boxes"  value="<?= $row['ITEM_GROUP_ID']; ?>" disabled="disabled" />
            </td>

            <td><input type="text" name="txt_itemdes[]" id="txt_itemdes_<?= $i; ?>" class="text_boxes" style="width:150px" value="<?= $row['PRODUCT_NAME_DETAILS']; ?>" disabled="disabled" /></td>

            <td><? echo create_drop_down( "cbo_rejuomid_$i", 80, $unit_of_measurement, "", 1, "-Select-", $row['UOM'], "", 1, '','','','','','', "cbo_rejuomid[]"); ?></td>

            <td>
            	<input type="text" name="txt_stock[]" id="txt_stock_<?= $i; ?>" class="text_boxes_numeric" style="width:80px" value="<?= $stock_qty; ?>" disabled="disabled" />
            </td>

            <td>
            	<input type="text" name="txt_salesqty[]" id="txt_salesqty_<?= $i; ?>" class="text_boxes_numeric" style="width:80px" value="<?= $stock_qty; ?>" onkeyup="amount_calculation(<?= $i; ?>,<?= $purpose; ?>);"/>
            	<input type="hidden" name="hidden_salesqty[]" id="hidden_salesqty_<?= $i; ?>" value="<?= $stock_qty; ?>"/>
            </td><!-- onBlur="amount_calculation($('#txt_salesrate').val());" -->

            <?
            if ($purpose==1)
            {
            	?>
            	<td><input type="text" name="txt_salesrate[]" id="txt_salesrate_<?= $i; ?>" class="text_boxes_numeric" style="width:60px" onkeyup="amount_calculation(<?= $i; ?>,<?= $purpose; ?>);"/></td> 
            	 <td><input type="text" name="txt_salesamount[]" id="txt_salesamount_<?= $i; ?>" class="text_boxes_numeric" style="width:80px" disabled="disabled"/></td>
            	<?
            }
            else
            {
            	?>
            	<td><input type="text" name="txt_salesrate[]" id="txt_salesrate_<?= $i; ?>" class="text_boxes_numeric" style="width:60px" disabled="disabled"/></td>
            	 <td><input type="text" name="txt_salesamount[]" id="txt_salesamount_<?= $i; ?>" class="text_boxes_numeric" style="width:80px" disabled="disabled"/></td>
            	<?
            }	
            ?>

            <td><input type="text" name="txt_bag[]" id="txt_bag_<?= $i; ?>" class="text_boxes_numeric" style="width:60px;"/></td>

            <td><input type="text" name="txt_remarks_dtls[]" id="txt_remarks_dtls_<?= $i; ?>" class="text_boxes" style="width:100px;"/></td>
        </tr>
		<?
		$i++;
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	// echo "<pre>";
	// print_r($process);die;
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}


		$field_array="id, challan_no_prefix, challan_no_prefix_num, sys_challan_no, company_id, location, item_category, store_id, selling_date, purpose, pay_term, customer_id, currency_id, exchange_rate, remarks, inserted_by, insert_date";
		$data_array="";
		if(!empty(str_replace("'","",$update_id)))
		{
			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later

			$new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'SSE', date("Y",time()), 5, "select challan_no_prefix, challan_no_prefix_num from inv_scrap_sales_mst where company_id=$cbo_company_id and $year_cond=".date('Y',time())." order by id desc ", "challan_no_prefix", "challan_no_prefix_num"));

			$id = return_next_id( "id", "inv_scrap_sales_mst", 1);
			
			$data_array="(".$id.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$cbo_company_id.",".$cbo_location.",".$cbo_category_id.",".$cbo_store_id.",".$txt_selling_date.",".$cbo_purpose.",".$cbo_pay_term.",".$txt_customer_id.",".$cbo_currency_id.",".$txt_exchange_rate.",".$txt_remarks.",".$user_id.",'".$pc_date_time."')";
			//echo $data_array;die;cbo_purpose

			//echo "10**insert into inv_scrap_sales_mst (".$field_array.") values ".$data_array;die;
			/*$rID=sql_insert("inv_issue_master",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0; */

			$sys_challan_no=$new_system_id[0];
			$row_id=$id;
		}
		else
		{
			$field_array_update="company_id*location*item_category*store_id*selling_date*purpose*pay_term*customer_id*currency_id*exchange_rate*remarks*updated_by*update_date";
			$data_array_update=$cbo_company_id."*".$cbo_location."*".$cbo_category_id."*".$cbo_store_id."*".$txt_selling_date."*".$cbo_purpose."*".$cbo_pay_term."*".$txt_customer_id."*".$cbo_currency_id."*".$txt_exchange_rate."*".$txt_remarks."*".$user_id."*'".$pc_date_time."'";

			/*$rID=sql_update("inv_issue_master",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; */

			$sys_challan_no=str_replace("'","",$txt_system_id);
			$row_id=str_replace("'","",$update_id);
		}

		$cbo_currency_id=str_replace("'","",$cbo_currency_id);
		if ($cbo_currency_id != 1)
		{
			$nameArray=sql_select( "SELECT conversion_rate FROM currency_conversion_rate WHERE con_date <= ".$txt_selling_date." and currency=$cbo_currency_id  order by con_date DESC");
			$conversion_rate=$nameArray[0][csf("conversion_rate")];
		}

		$id_dtls=return_next_id( "id", "inv_scrap_sales_dtls", 1);

		$field_array_dtls="id, mst_id, prod_id, store_id, item_group_id, item_description, rej_uom, sales_qty, sales_rate, sales_amount, no_of_bag, remarks, sales_amount_taka, inserted_by, insert_date";
		
		for ($i=1; $i<=$row_num; $i++)
	    {
			$prod_id = "txt_prodid_".$i;
			$item_group = "txt_itemgroup_".$i;
			$item_des = "txt_itemdes_".$i;
			$rej_uomid = "cbo_rejuomid_".$i;			
			$sales_qty = "txt_salesqty_".$i;
			$sales_rate = "txt_salesrate_".$i;
			$sales_amount = "txt_salesamount_".$i;
			$bags = "txt_bag_".$i;
			$remarks_dtls = "txt_remarks_dtls_".$i;
			if ($cbo_currency_id !=1)
			    $sales_amount_taka = $conversion_rate*(str_replace("'","",$$sales_amount)*1);
			else $sales_amount_taka=(str_replace("'","",$$sales_amount)*1);
			//echo "10**$sales_amount_taka";
			if ($i != 1) $data_array_dtls .=",";
			$data_array_dtls .="(".$id_dtls.",".$row_id.",".$$prod_id.",".$cbo_store_id.",".$$item_group.",".$$item_des.",".$$rej_uomid.",".$$sales_qty.",".$$sales_rate.",".$$sales_amount.",".$$bags.",".$$remarks_dtls.",".$sales_amount_taka.",".$user_id.",'".$pc_date_time."')";

			$id_dtls = $id_dtls+1;
		}

		//echo "10**insert into inv_scrap_sales_dtls (".$field_array_dtls.") values ".$data_array_dtls; die;
		//echo "10**insert into inv_scrap_sales_dtls (".$field_array_dtls.") values ".$data_array_dtls; die;

		if(!empty(str_replace("'","",$update_id)))
		{
			$rID=sql_insert("inv_scrap_sales_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("inv_scrap_sales_mst",$field_array_update,$data_array_update,"id",$row_id,1);
			if($rID) $flag=1; else $flag=0;
		}
		
		$rID2=sql_insert("inv_scrap_sales_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}
		//echo "10**".$rID."**".$rID2;die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "0**".$sys_challan_no."**".$row_id."**0";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "5**0**"."&nbsp;"."**0";
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".$sys_challan_no."**".$row_id."**0";
			}
			else
			{
				oci_rollback($con);
				echo "5**0**"."&nbsp;"."**0";
			}
		}

		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array_update="location*item_category*selling_date*pay_term*customer_id*currency_id*remarks*purpose*updated_by*update_date";
		$data_array_update=$cbo_location."*".$cbo_category_id."*".$txt_selling_date."*".$cbo_pay_term."*".$txt_customer_id."*".$cbo_currency_id."*".$txt_remarks."*".$cbo_purpose."*".$user_id."*'".$pc_date_time."'";

		$sys_challan_no=str_replace("'","",$txt_system_id);
		$row_id=str_replace("'","",$update_id);

		$cbo_currency_id=str_replace("'","",$cbo_currency_id);
		if ($cbo_currency_id != 1)
		{
			$nameArray=sql_select( "SELECT conversion_rate FROM currency_conversion_rate WHERE con_date <= ".$txt_selling_date." and currency=$cbo_currency_id  order by con_date DESC");
			$conversion_rate=$nameArray[0][csf("conversion_rate")];
		}

		$dtls_update_id = str_replace("'","",$dtls_update_id_1);
		$field_array_update_dtls="prod_id*store_id*item_group_id*item_description*rej_uom*sales_qty*sales_rate*sales_amount*no_of_bag*remarks*sales_amount_taka*updated_by*update_date";
		
		for ($i=1; $i<=$row_num; $i++)
	    {
			$prod_id = "txt_prodid_".$i;
			$item_group = "txt_itemgroup_".$i;
			$item_des = "txt_itemdes_".$i;
			$rej_uomid = "cbo_rejuomid_".$i;			
			$sales_qty = "txt_salesqty_".$i;
			$sales_rate = "txt_salesrate_".$i;
			$sales_amount = "txt_salesamount_".$i;
			$bags = "txt_bag_".$i;
			$remarks_dtls = "txt_remarks_dtls_".$i;
			
			if ($cbo_currency_id !=1)
			    $sales_amount_taka = $conversion_rate*(str_replace("'","",$$sales_amount)*1);
			else $sales_amount_taka=(str_replace("'","",$$sales_amount)*1);

			$data_array_update_dtls =$$prod_id."*".$cbo_store_id."*".$$item_group."*".$$item_des."*".$$rej_uomid."*".$$sales_qty."*".$$sales_rate."*".$$sales_amount."*".$$bags."*".$$remarks_dtls."*'".$sales_amount_taka."'*".$user_id."*'".$pc_date_time."'";
		}
		//echo "10**$field_array_update_dtls.'=='.$data_array_update_dtls"; die;	

		$rID=sql_update("inv_scrap_sales_mst",$field_array_update,$data_array_update,"id",$update_id,0);
		if($rID) $flag=1; else $flag=0;
		
		$rID1=sql_update("inv_scrap_sales_dtls",$field_array_update_dtls,$data_array_update_dtls,"id",$dtls_update_id,0);
		if($rID1) $flag=1; else $flag=0;
		//echo "10**$rID1";die;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".$sys_challan_no."**".$row_id."**0";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**0**0**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".$sys_challan_no."**".$row_id."**0";
			}
			else
			{
				oci_rollback($con);
				echo "6**0**0**1";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		/*$field_array_delete="location*item_category*selling_date*pay_term*customer_id*currency_id*remarks*purpose*updated_by*update_date";
		$data_array_delete=$cbo_location."*".$cbo_category_id."*".$txt_selling_date."*".$cbo_pay_term."*".$txt_customer_id."*".$cbo_currency_id."*".$txt_remarks."*".$cbo_purpose."*".$user_id."*'".$pc_date_time."'";*/

		$sys_challan_no=str_replace("'","",$txt_system_id);
		$row_id=str_replace("'","",$update_id);

		$dtls_update_id = str_replace("'","",$dtls_update_id_1);		

		$sql_row_count = "select id from inv_scrap_sales_dtls where mst_id=$update_id and status_active=1 and is_deleted=0";
		$sql_row_count_res = sql_select($sql_row_count);
		if (count($sql_row_count_res) < 2)
		{
			$field_array_delete="updated_by*update_date*status_active*is_deleted";
			$data_array_delete="".$user_id."*'".$pc_date_time."'*0*1";
			$rID=sql_delete("inv_scrap_sales_mst",$field_array_delete,$data_array_delete,"id",$update_id,0);
			if($rID) $flag=1; else $flag=0;
		}

		$field_array_delete_dtls="updated_by*update_date*status_active*is_deleted";
		$data_array_delete_dtls="".$user_id."*'".$pc_date_time."'*0*1";
		$rID1=sql_delete("inv_scrap_sales_dtls",$field_array_delete_dtls,$data_array_delete_dtls,"id","".$dtls_update_id."",0);
		if($rID1) $flag=1;else $flag=0;
		//echo "10**$rID1";die;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "2**".$sys_challan_no."**".$row_id."**0";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**0**0**2";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "2**".$sys_challan_no."**".$row_id."**0";
			}
			else
			{
				oci_rollback($con);
				echo "6**0**0**2";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="customer_search_popup")
{
	echo load_html_head_contents("Customer Name Info Popup", "../../../", 1, 1,'','','');
	extract($_REQUEST);
    ?>
	<script>
		function js_set_value(id,name)
		{
			//alert (id);
			$('#hidden_customer_id').val(id);
			$('#hidden_customer_no').val(name);
			parent.emailwindow.hide();
		}
    </script>
    </head>
	<input type="hidden" name="hidden_customer_id" id="hidden_customer_id" />
    <input type="hidden" name="hidden_customer_no" id="hidden_customer_no" />
	<?
		$sql_customer="select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (80)) order by buy.buyer_name";
		$result_customer = sql_select($sql_customer); $i=1;
		?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="510" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="80">Customer ID</th>
                <th>Customer Nmae</th>
            </thead>
            <?
            $i=1;
            foreach ($result_customer as $row)
            {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick='js_set_value("<? echo $row[csf('id')]; ?>","<? echo $row[csf('buyer_name')]; ?>")' >
                        <td width="30"><? echo $i; ?></td>
                        <td width="80"><p><? echo $row[csf('id')]; ?></p></td>
                        <td><p><? echo $row[csf('buyer_name')]; ?></p></td>
                    </tr>
                <?
                $i++;
            }
            ?>
        </table>
	<?
	exit();
}

if($action=="show_dtls_listview")
{
	$sql ="SELECT a.ID, b.id as DTLS_ID, b.PROD_ID, b.ITEM_GROUP_ID, b.ITEM_DESCRIPTION, b.rej_uom as UOM, b.sales_qty as ISSUE_QTY, b.sales_rate as ISSUE_RATE, b.sales_amount as AMOUNT, b.NO_OF_BAG, b.REMARKS from inv_scrap_sales_mst a, inv_scrap_sales_dtls b where a.id=b.mst_id and b.mst_id='$data' and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 order by b.id desc";
	$sql_res = sql_select($sql);
	?>
	<div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="80">Prod. ID</th>
                <th width="100">Item Group</th>
                <th width="180">Item Description</th>
                <th width="80">UOM</th>
                <th width="80">Issue Qty</th>
                <th width="80">Issue Rate</th>
                <th width="80">Amount</th>
                <th width="60">No. Of Bag</th>
                <th>Remarks</th>
            </thead>
		</table>
		<div style="width:1020; max-height:180px;" id="scrap_list_view" align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table">
			<?
				$i=1;
				foreach($sql_res as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; 
					else $bgcolor="#FFFFFF";
					?>
	                <tr bgcolor="<?= $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<?= $row['ID'].'**'.$row['DTLS_ID'].'**'.$row['PROD_ID']; ?>','populate_data_from_dtls','requires/scrap_material_issue_controller');" >
	                    <td width="30"><?= $i; ?></td>
	                    <td width="80"><p><?= $row['PROD_ID']; ?></p></td>
	                    <td width="100"><p><?= $row['ITEM_GROUP_ID']; ?></p></td>
	                    <td width="180"><p><?= $row['ITEM_DESCRIPTION']; ?></p></td>
	                    <td width="80" align="center"><p><?= $unit_of_measurement[$row['UOM']]; ?></p></td>
	                    <td width="80" align="right"><p><?= number_format($row['ISSUE_QTY'],2); ?></p></td>
	                    <td width="80" align="right"><p><?= number_format($row['ISSUE_RATE'],4); ?></p></td>
	                    <td width="80" align="right"><p><?= number_format($row['AMOUNT'],2); ?></p></td>
	                    <td width="60" align="right"><p><?= $row['NO_OF_BAG']; ?></p></td>
	                    <td><p><?= $row['REMARKS']; ?></p></td>
	                </tr>
					<?
					$i++;
				}
				?>
			</table>
		</div>
    </div>
	<?
	exit();
}

if ($action=="system_popup")
{
	echo load_html_head_contents("System Info Popup", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(id)
		{
			$("#hidden_mst_id").val(id);
			//document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}
	</script>
	</head>
        <div align="center" style="width:100%;">
            <form name="searchreceivefrm_1"  id="searchreceivefrm_1" autocomplete="off">
                <table width="780" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                    	<tr>
                            <th width="160">Company Name</th>
                            <th width="160">Customer Name</th>
                            <th width="100">System ID</th>
                            <th width="210">Issue Date Range</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:60px;" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
								<?
									echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company_id, ""); //load_drop_down( 'scrap_material_issue_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_td' ); ?>
                            </td>
                            <td id="buyer_td">
								<?
									echo create_drop_down( "cbo_party_name", 160, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$company_id' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (80)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", '', "" );
                                ?>
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:95px" placeholder="System ID" />
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:90px" placeholder="From Date">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:90px" placeholder="To Date">
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value, 'create_issue_search_list_view', 'search_div', 'scrap_material_issue_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:60px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="6" align="center" height="40" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                        <table>
	                        <tr>
	                            <td colspan="6" align="center" valign="top" id=""><div id="search_div"></div></td>
	                        </tr>
                        </table>
                    </tbody>
                </table>
            </form>
        </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_issue_search_list_view")
{
	list($company_id, $buyer_id, $date_from, $date_to, $search_common) = explode("_", $data);
	if ($company_id != 0) $company_cond=" and company_id='$company_id'"; 
	else { echo "Please Select Company First."; die; }

	$buyer_cond=$issue_date_cond=$sys_id_cond='';
	if ($buyer_id !=0 ) $buyer_cond=" customer_id='$buyer_id'";

	if($db_type==0)
	{
		$year_cond= "year(insert_date)";
		if ($date_from != '' &&  $date_to != '')  $issue_date_cond = " and selling_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
	}
	else
	{
		$year_cond= "TO_CHAR(insert_date,'YYYY')";
		if ($date_from != '' &&  $date_to != '') $issue_date_cond = "and selling_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";
	}

	if ($search_common != '') $sys_id_cond=" and challan_no_prefix_num='$search_common'";


	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	//$arr=array (2=>$item_category,4=>$party_arr,5=>$currency);
	//id, challan_no_prefix, challan_no_prefix_num, sys_challan_no, company_id, item_category, selling_date, pay_term, customer_id, currency_id, remarks

	$sql= "select ID, CHALLAN_NO_PREFIX_NUM, $year_cond as YEAR, ITEM_CATEGORY, selling_date as ISSUE_DATE, PAY_TERM, customer_id as BUYER_ID, CURRENCY_ID, REMARKS from inv_scrap_sales_mst where status_active=1 and is_deleted=0 $company_cond $buyer_cond $issue_date_cond $sys_id_cond order by selling_date DESC";
	$sql_res=sql_select($sql);
	?>
	<table width="780" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
        <thead>
        	<tr>
                <th width="50">SL</th>
                <th width="100">System No</th>
                <th width="80">Year</th>
                <th width="150">Item Category</th>               
                <th width="100">Issue Date</th>                
                <th width="150">Customer</th>                
                <th>Currency</th>                
            </tr>
        </thead>
    </table>
    <div style="width:780px; overflow-y:scroll; max-height:220px;" id="buyer_list_view" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="760" class="rpt_table" id="list_view"> 
        	<?
        	$i=1;
        	foreach ($sql_res as $row) 
        	{
	        	?>
	            <tr style="text-decoration:none; cursor:pointer" onclick="js_set_value('<?= $row['ID']; ?>');">
	                <td width="50"><?= $i; ?></td>
	                <td width="100" align="right"><p><?= $row['CHALLAN_NO_PREFIX_NUM']; ?></p></td>
	                <td width="80" align="center"><p><?= $row['YEAR']; ?></p></td>
	                <td width="150"><p><?= $item_category[$row['ITEM_CATEGORY']]; ?></p></td>
	                <td width="100" align="center"><p><?= change_date_format($row['ISSUE_DATE']); ?></p></td>
	                <td width="150"><p><?= $buyer_arr[$row['BUYER_ID']]; ?></p></td>
	                <td align="center"><p><?= $currency[$row['CURRENCY_ID']]; ?></p></td>
	            <tr>
	        	<?
	        	$i++;
	        }	
	        ?>
        </table>
    </div>
	<?
	exit();
}

if ($action=="populate_data_from_mst")
{
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$sql = "SELECT a.ID, a.SYS_CHALLAN_NO, a.COMPANY_ID, a.LOCATION, a.ITEM_CATEGORY, a.STORE_ID, a.selling_date as ISSUE_DATE, a.PURPOSE, a.PAY_TERM, a.CUSTOMER_ID, a.CURRENCY_ID, a.EXCHANGE_RATE, a.REMARKS from inv_scrap_sales_mst a where a.id='$data' and a.status_active=1 and a.is_deleted=0";
	$nameArray=sql_select($sql);
	foreach ($nameArray as $row)
	{
		echo "document.getElementById('txt_system_id').value 		= '".$row["SYS_CHALLAN_NO"]."';\n";
		echo "document.getElementById('cbo_company_id').value 		= '".$row["COMPANY_ID"]."';\n";
		echo "document.getElementById('cbo_location').value 		= '".$row["LOCATION"]."';\n";		
		echo "document.getElementById('cbo_category_id').value		= '".$row["ITEM_CATEGORY"]."';\n";

		echo "load_drop_down( 'requires/scrap_material_issue_controller', '".$row["COMPANY_ID"]."_".$row["LOCATION"]."_".$row["ITEM_CATEGORY"]."', 'load_drop_down_store', 'store_td');\n";
		echo "document.getElementById('cbo_store_id').value		= '".$row["STORE_ID"]."';\n";

		echo "document.getElementById('txt_selling_date').value		= '".change_date_format($row["ISSUE_DATE"])."';\n";
		echo "document.getElementById('cbo_purpose').value			= '".$row["PURPOSE"]."';\n";
		echo "document.getElementById('txt_customer_no').value 		= '".$buyer_arr[$row["CUSTOMER_ID"]]."';\n";
		echo "document.getElementById('txt_customer_id').value 		= '".$row["CUSTOMER_ID"]."';\n";
		echo "document.getElementById('cbo_pay_term').value 		= '".$row["PAY_TERM"]."';\n";
		echo "document.getElementById('cbo_currency_id').value 		= '".$row["CURRENCY_ID"]."';\n";
		echo "document.getElementById('txt_exchange_rate').value 		= '".$row["EXCHANGE_RATE"]."';\n";
		echo "document.getElementById('txt_remarks').value 			= '".$row["REMARKS"]."';\n";
	    echo "document.getElementById('update_id').value            = '".$row["ID"]."';\n";

	    echo "$('#cbo_company_id').attr('disabled',true)".";\n";
	    echo "$('#cbo_location').attr('disabled',true)".";\n";
	    echo "$('#cbo_category_id').attr('disabled',true)".";\n";
	    echo "$('#cbo_store_id').attr('disabled',true)".";\n";
	    echo "$('#txt_selling_date').attr('disabled',true)".";\n";
	    echo "$('#cbo_currency_id').attr('disabled',true)".";\n";
	    echo "$('#txt_customer_no').attr('disabled',true)".";\n";
	    echo "$('#cbo_pay_term').attr('disabled',true)".";\n";
	    echo "$('#cbo_purpose').attr('disabled',true)".";\n";
		echo "set_button_status(0,'".$_SESSION['page_permission']."', 'fnc_material_issue',1,1);\n";
	}
	exit();
}

if ($action=="populate_data_from_dtls")
{
	list($mst_id, $dtls_id, $prod_id) = explode("**", $data);
	$sql = "SELECT a.ID, a.SYS_CHALLAN_NO, a.COMPANY_ID, a.LOCATION, a.ITEM_CATEGORY, a.STORE_ID, a.selling_date as ISSUE_DATE, a.PURPOSE, a.PAY_TERM, a.CUSTOMER_ID, a.CURRENCY_ID, a.EXCHANGE_RATE, a.REMARKS, b.id as DTLS_ID, b.PROD_ID, b.ITEM_GROUP_ID, b.ITEM_DESCRIPTION, b.rej_uom as UOM, b.sales_qty as ISSUE_QTY, b.sales_rate as ISSUE_RATE, b.sales_amount as AMOUNT, b.NO_OF_BAG, b.remarks as REMARKS_DTLS from inv_scrap_sales_mst a, inv_scrap_sales_dtls b where a.id=b.mst_id and a.id='$mst_id' and b.id='$dtls_id' and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 order by b.id desc";
	$nameArray=sql_select($sql);
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$store_arr=return_library_array( "select ID, STORE_NAME from LIB_STORE_LOCATION",'ID','STORE_NAME');


	$sql_iss = "select b.PROD_ID, sum(b.sales_qty) as ISSUE_QTY from inv_scrap_sales_mst a, inv_scrap_sales_dtls b where a.id=b.mst_id and b.prod_id in($prod_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.prod_id";
	$sql_iss_res = sql_select($sql_iss);
	$scrap_iss_qty_arr=array();
	foreach ($sql_iss_res as $row)
	{
		$scrap_iss_qty_arr[$row['PROD_ID']]=$row['ISSUE_QTY'];
	}

	$sql_receive = "SELECT b.PRODUCT_ID, sum(b.receive_qnty) as RECEIVE_QNTY
	FROM inv_scrap_receive_mst a, inv_scrap_receive_dtls b
	WHERE a.id=b.mst_id and b.PRODUCT_ID in($prod_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	GROUP BY b.product_id";
	$sql_receive_res=sql_select($sql_receive);
	$scrap_receive_qty_arr=array();
	foreach ($sql_receive_res as $row)
	{
		$scrap_receive_qty_arr[$row['PRODUCT_ID']]=$row['RECEIVE_QNTY'];
	}

	//print_r($scrap_receive_qty_arr);
	$sql_res=sql_select($sql);
	
	foreach ($nameArray as $row)
	{
		//echo $scrap_receive_qty_arr[$row['PROD_ID']].'**'.$scrap_iss_qty_arr[$row['PROD_ID']];
		$stock_qty = $scrap_receive_qty_arr[$row['PROD_ID']]-$scrap_iss_qty_arr[$row['PROD_ID']];

		echo "document.getElementById('txt_system_id').value 		= '".$row["SYS_CHALLAN_NO"]."';\n";
		echo "document.getElementById('cbo_company_id').value 		= '".$row["COMPANY_ID"]."';\n";
		echo "$('#cbo_company_id').attr('disabled',true)".";\n";

		echo "document.getElementById('cbo_location').value 		= '".$row["LOCATION"]."';\n";
		echo "$('#cbo_location').attr('disabled',true)".";\n";

		echo "document.getElementById('cbo_category_id').value		= '".$row["ITEM_CATEGORY"]."';\n";
		echo "$('#cbo_category_id').attr('disabled',true)".";\n";

		echo "load_drop_down( 'requires/scrap_material_issue_controller', '".$row["COMPANY_ID"]."_".$row["LOCATION"]."_".$row["ITEM_CATEGORY"]."', 'load_drop_down_store', 'store_td');\n";		
		echo "document.getElementById('cbo_store_id').value		= '".$row["STORE_ID"]."';\n";
		echo "$('#cbo_store_id').attr('disabled',true)".";\n";

		echo "document.getElementById('txt_selling_date').value		= '".change_date_format($row["ISSUE_DATE"])."';\n";
		echo "$('#txt_selling_date').attr('disabled',true)".";\n";

		echo "document.getElementById('cbo_purpose').value			= '".$row["PURPOSE"]."';\n";
		echo "$('#cbo_purpose').attr('disabled',true)".";\n";

		echo "document.getElementById('txt_customer_no').value 		= '".$buyer_arr[$row["CUSTOMER_ID"]]."';\n";
		echo "$('#txt_customer_no').attr('disabled',true)".";\n";

		echo "document.getElementById('txt_customer_id').value 		= '".$row["CUSTOMER_ID"]."';\n";
		echo "document.getElementById('cbo_pay_term').value 		= '".$row["PAY_TERM"]."';\n";
		echo "$('#cbo_pay_term').attr('disabled',true)".";\n";

		echo "document.getElementById('cbo_currency_id').value 		= '".$row["CURRENCY_ID"]."';\n";
		echo "$('#cbo_currency_id').attr('disabled',true)".";\n";

		echo "document.getElementById('txt_exchange_rate').value 		= '".$row["EXCHANGE_RATE"]."';\n";
		echo "document.getElementById('txt_remarks').value 			= '".$row["REMARKS"]."';\n";
	    echo "document.getElementById('update_id').value            = '".$row["ID"]."';\n";

	    echo "document.getElementById('txt_prodid_1').value            = '".$row["PROD_ID"]."';\n";
	    echo "$('#txt_prodid_1').attr('disabled',true);\n";

	    echo "document.getElementById('dtls_update_id_1').value            = '".$row["DTLS_ID"]."';\n";
	    echo "document.getElementById('txt_itemgroup_1').value            = '".$row["ITEM_GROUP_ID"]."';\n";
	    echo "document.getElementById('txt_itemdes_1').value            = '".$row["ITEM_DESCRIPTION"]."';\n";
	    echo "document.getElementById('cbo_rejuomid_1').value            = '".$row["UOM"]."';\n";
	    echo "document.getElementById('txt_stock_1').value            = '".$stock_qty."';\n";

	    echo "document.getElementById('txt_salesqty_1').value            = '".$row["ISSUE_QTY"]."';\n";
	    echo "document.getElementById('hidden_salesqty_1').value            = '".$row["ISSUE_QTY"]."';\n";
	    //echo "$('#hidden_salesqty_1').attr('disabled',false);\n";

	    echo "document.getElementById('txt_salesrate_1').value            = '".$row["ISSUE_RATE"]."';\n";
	    echo "document.getElementById('txt_salesamount_1').value            = '".$row["AMOUNT"]."';\n";

	    echo "document.getElementById('txt_bag_1').value            = '".$row["NO_OF_BAG"]."';\n";
	    echo "document.getElementById('txt_remarks_dtls_1').value            = '".$row["REMARKS_DTLS"]."';\n";

		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_material_issue',1,1);\n";
	}	
	exit();
}

if($action=="scrap_material_challan_print")
{	
	list($company_id,$update_id,$location_id,$report_title)=explode('**',$data);
	$company_library=return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
	$buyer_library=return_library_array("select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
	$item_group_arr=return_library_array("select id, item_name from lib_item_group where status_active=1 and is_deleted=0", "id", "item_name");
	$category_arr=return_library_array("select category_id, short_name from lib_item_category_list where status_active=1 and is_deleted=0", "category_id", "short_name");
	$store_arr=return_library_array("select id, store_name from lib_store_location where status_active=1 and is_deleted=0", "id", "store_name");
	 
	$purpose_array=array(1=>"Sales", 2=>"Disposal");

	$sql_mst="SELECT COMPANY_ID, SYS_CHALLAN_NO, ITEM_CATEGORY, STORE_ID, CUSTOMER_ID, selling_date as ISSUE_DATE, PURPOSE, PAY_TERM, CURRENCY_ID, REMARKS from inv_scrap_sales_mst where id=$update_id and company_id=$company_id and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql_mst);
	$com_dtls = fnc_company_location_address($company_id, $location_id, 2);
	?>
	<div style="width:930px;">
    <table width="930" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:24px"><strong><?= $company_library[$company_id]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">
				<?= $com_dtls[1]; ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:20px"><u><strong><?= $report_title; ?></strong></u></td>
        </tr>
        <tr>
			<?
                $buyer_add = $dataArray[0]['CUSTOMER_ID'];
                $nameArray = sql_select( "select ADDRESS_1, WEB_SITE, BUYER_EMAIL, COUNTRY_ID from lib_buyer where id=$buyer_add and status_active=1 and is_deleted=0");
                foreach ($nameArray as $result)
                {
                    $address="";
                    if($result!="") $address=$result['ADDRESS_1'];
                }
            ?>        	
        	<td width="125"><strong>Issue To :</strong></td>
            <td width="175px"><?= $buyer_library[$buyer_add]; ?></td>
            <td width="125"><strong>Challan No :</strong></td>
            <td width="175px"><?= $dataArray[0]['SYS_CHALLAN_NO']; ?></td>
            <td width="125"><strong>Issue Date :</strong></td>
            <td width="175px"><?= change_date_format($dataArray[0]['ISSUE_DATE']); ?></td>
        </tr>
        <tr>
        	<td colspan="2"><?= $address; ?></td>
            <td width="125"><strong>Item Category :</strong></td>
            <td width="175px"><?= $category_arr[$dataArray[0]['ITEM_CATEGORY']]; ?></td>
        	<td width="125"><strong>Store Name :</strong></td>
        	<td width="175px"><?= $store_arr[$dataArray[0]['STORE_ID']]; ?></td>
        </tr>
        <tr>
        	<td width="125"><strong>Issue Purpose :</strong></td>
        	<td width="175px"><?= $purpose_array[$dataArray[0]['PURPOSE']]; ?></td>
        	<td width="125"><strong>Pay Term :</strong></td>
        	<td width="175px"><?= $pay_mode[$dataArray[0]['PAY_TERM']]; ?></td>
        	<td width="125"><strong>Currency :</strong></td>
        	<td width="175px"><?= $currency[$dataArray[0]['CURRENCY_ID']]; ?></td>
        </tr>
         <tr>
        	<td width="125"><strong>Remarks :</strong></td>
        	<td colspan="5"><?= $dataArray[0]['REMARKS']; ?></td>        	
        </tr>        
    </table>    
	<?
	$sql_dtls ="SELECT b.PROD_ID, b.ITEM_GROUP_ID, b.ITEM_DESCRIPTION, b.rej_uom as UOM, b.sales_qty as ISSUE_QTY, b.sales_rate as ISSUE_RATE, b.sales_amount as AMOUNT, b.NO_OF_BAG, b.REMARKS from inv_scrap_sales_dtls b where b.mst_id=$update_id and b.status_active=1 and b.is_deleted=0 order by b.id desc";
	$sql_dtls_res=sql_select($sql_dtls);
	?>
	<div>
    <table cellspacing="0" width="930"  border="1" rules="all" class="rpt_table" align="left" style="margin-top: 10px;">
        <thead bgcolor="#dddddd">
            <th width="30">SL</th>
            <th width="100">Prod. ID</th>
            <th width="100">Item Group</th>
            <th width="200">Item Description</th>
            <th width="60">UOM</th>
            <th width="80">Issue Qty</th>
            <th width="80">Rate</th>
            <th width="80">Amount</th>
            <th width="80">No. Of Bag</th>
            <th>Remarks</th>
        </thead>
        <tbody>
        <?
		$i=1;
		foreach($sql_dtls_res as $row)
		{
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
            <tr bgcolor="<?= $bgcolor; ?>">
            	<td width="30"><?= $i;  ?></td>
                <td width="100"><p><?= $row['PROD_ID']; ?></p></td>
                <td width="100"><p><?= $item_group_arr[$row['ITEM_GROUP_ID']]; ?></p></td>
                <td width="200"><p><?= $row['ITEM_DESCRIPTION']; ?></p></td>
                <td width="60" align="center"><p><?= $unit_of_measurement[$row['UOM']]; ?></p></td>
                <td width="80" align="right"><p><? echo number_format($row['ISSUE_QTY'],2,'.',''); ?>&nbsp;</p></td>
                <td width="80" align="right"><p><? echo number_format($row['ISSUE_RATE'],4,'.',''); ?>&nbsp;</p></td>
                <td width="80" align="right"><p><? echo number_format($row['AMOUNT'],2,'.',''); ?>&nbsp;</p></td>
                <td width="80" align="right"><p><?= $row['NO_OF_BAG']; ?>&nbsp;</p></td>
                <td><p><?= $row['REMARKS']; ?></p></td>
            </tr>
            <?
            $i++;
			$tot_issue_qty += $row['ISSUE_QTY'];
			$tot_amount += $row['AMOUNT'];
			$tot_bag += $row['NO_OF_BAG'];			
		}
		?>
        </tbody>
        <tfoot>
        	<tr bgcolor="<?= $bgcolor; ?>">
                <td align="right" colspan="5" ><strong>Total:&nbsp;</strong></td>
                <td align="right"><?= number_format($tot_issue_qty,2,'.',''); ?>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><?= number_format($tot_amount,2,'.',''); ?>&nbsp;</td>
                <td align="right"><?= $tot_bag; ?>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </tfoot>
    </table>
    <div>  
    <?
	exit();
}

?>
