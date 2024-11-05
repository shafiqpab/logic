<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];


if($action=='save_update_delete')
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	// echo $total_row.'test';die;

	if($operation==0) //insert
	{

		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$id = return_next_id("id", "shift_duration_entry", 1);
		//$txt_reporting_hour=str_replace("'","",$txtStratTime_1)." ".str_replace("'","",$txtEndTime_1);		
		//echo $txt_reporting_hour;die;
		//$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
		$field_array= "id, production_type, shift_name, start_time, end_time, cross_date, inserted_by, insert_date, status_active, is_deleted";

		for ($i=1; $i<=$total_row; $i++)
	    {
			$prod_type = "cboProTypeId_".$i;
			$shift_name_id = "cboShiftName_".$i;
			$start_time = "txtStratTime_".$i;
			$end_time = "txtEndTime_".$i;			
			$txtChkBox = "txtChkBox_".$i;			
			if ($i != 1) $data_array .=",";
			$data_array .="(".$id.",".$$prod_type.",".$$shift_name_id.",".$$start_time.",".$$end_time.",".$$txtChkBox.",".$user_id.",'".$pc_date_time."','1',0)";
			$id = $id+1;
		}
		//echo $total_row.'test2';die;
		//echo "10**INSERT INTO shift_duration_entry (".$field_array.") VALUES ".$data_array."";die;
		$rID = sql_insert("shift_duration_entry", $field_array, $data_array, 0);

		
		if($rID) $flag=1; else $flag=0;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'",'',$id)."**1";
				//echo "0**".$id_dtls.'';
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$id)."**1";
				//echo "0**".$id_dtls;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
			
	if($operation==1)  //update
	{
		//echo "10**";print_r($_REQUEST);die();
		//$update_id = str_replace("'","",$update_id);
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id = return_next_id("id", "shift_duration_entry", 1);
		$field_array= "id, production_type, shift_name, start_time, end_time, cross_date, inserted_by, insert_date, status_active, is_deleted";

		$field_array_up = "production_type*shift_name*start_time*end_time*cross_date*updated_by*update_date*status_active*is_deleted";
		//$data_array  ="".$id."*".$$prod_type."*".$$shift_name."*".$$start_time."*".$$end_time."*".$user_id."*'".$pc_date_time."'*1*'0'";

		for ($i=1; $i<=$total_row; $i++)
	    {
			$prod_type = "cboProTypeId_".$i;
			$shift_name_id = "cboShiftName_".$i;
			$start_time = "txtStratTime_".$i;
			$end_time = "txtEndTime_".$i;	
			$updateId= "updateId_".$i;	
			$txtChkBox = "txtChkBox_".$i;	
			if(str_replace("'",'',$$updateId)=="")
			{					
				if ($i != 1) $data_array .=",";
				$data_array .="(".$id.",".$$prod_type.",".$$shift_name_id.",".$$start_time.",".$$end_time.",".$$txtChkBox.",".$user_id.",'".$pc_date_time."','1',0)";
				$id = $id+1;
			}
			else
			{
				$updateID_array[]=str_replace("'",'',$$updateId); 
				$data_array_up[str_replace("'",'',$$updateId)]=explode("*",("".$$prod_type."*".$$shift_name_id."*".$$start_time."*".$$end_time."*".$$txtChkBox."*'".$user_id."'*'".$pc_date_time."'*1*0"));
			}
		}
		/*echo "10**";
		print_r($updateID_array);die;*/
		$sql = "select id from shift_duration_entry where status_active=1 and is_deleted=0 order by id"; 
		$all_data=sql_select($sql);
		$all_data_arr=array();
		foreach ($all_data as $key => $value) 
		{
			$all_data_arr[]=$value[csf('id')];
		}
		//print_r($all_data_arr);die;

		$arr_diff=array_diff($all_data_arr,$updateID_array);
		$deleted_id = implode(',', $arr_diff);
		/*echo "10**";
		print_r($deleted_id);die;*/

		if ($deleted_id!="") 
		{
			/*echo "10**";
			print_r($arr_diff);*/
			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			$statusChange=sql_multirow_update("shift_duration_entry",$field_array_status,$data_array_status,"id",$deleted_id,0);
			if($statusChange) $flag=1;else $flag=0;
		}

		if($data_array_up!="")
		{
			/*echo "10**";
			print_r($updateID_array);die;*/
			$rID2 = execute_query(bulk_update_sql_statement("shift_duration_entry", "id", $field_array_up, $data_array_up, $updateID_array));
			//echo "10**".bulk_update_sql_statement("shift_duration_entry", "id", $field_array_up, $data_array_up, $updateID_array);die;
			if($rID2) $flag=1;else $flag=0;
				//echo "B";
		}
		if($data_array!="") 
		{
			$rID = sql_insert("shift_duration_entry", $field_array, $data_array, 0);
			if ($flag == 1) 
			{
			 if ($rID) $flag = 1; else $flag = 0;
			}
	    }
		
		//echo "10**INSERT INTO shift_duration_entry (".$field_array.") VALUES ".$data_array."";die;
		//echo "10**".$rID.'=='.$rID2.'=='.$statusChange.'=='.$flag;die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".$update_id;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$update_id;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
		    if($flag==1)
			{
				oci_commit($con);
				echo "1**".$update_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$update_id;
			}
		}
		disconnect($con);
		die;
	}	

	if($operation==2) //delete
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="'".$user_id."'*'".$pc_date_time."'*0*1";
		$rID = execute_query("delete from shift_duration_entry where mst_id = $update_id",1);		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**".$update_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$update_id;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);   
				echo "2**".$update_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$update_id;
			}
		}
		disconnect($con);
		die;	
	}
}

if($action=="on_change_load_page")
{
	if($data==5 || $data==6)
	{
		$disbled="disabled='disabled'";
		$display="";
	}
	else 
	{
		$disbled='';
		$display='style="display:none"';
	}
?>
	<!-- <div style="width:650px; float:left; margin:auto" align="center">
		<table width="550" cellspacing="2" cellpadding="0" border="0">
        	<tr>
				<td align="center" class="must_entry_caption">Incurred Date</td>
				<td>
					<input type="text" name="txt_incurred_date" id="txt_incurred_date" class="datepicker" onChange="calculate_date()" style="width:140px" readonly/>	
				</td>
				<td align="center" class="must_entry_caption">Incurred Date To</td>
				<td>
					<input type="text" name="txt_incurred_to_date" id="txt_incurred_to_date" style="width:140px" class="datepicker" disabled/>	
				</td>
			</tr>
			<tr>
				<td align="center" class="must_entry_caption">Applying Period</td>
				<td>
					<input type="text" name="txt_applying_period_date" id="txt_applying_period_date" class="datepicker" style="width:140px" onChange="show_po_list()" readonly="readonly" <? echo $disbled; ?>/>	
				</td>
				<td align="center" class="must_entry_caption">Applying Period To</td>
				<td>
					<input type="text" name="txt_applying_period_to_date" id="txt_applying_period_to_date" style="width:140px" class="datepicker" onChange="show_po_list()" readonly="readonly" <? echo $disbled; ?>/>	
				</td>
			</tr>
            <tr>
				<td align="center" class="must_entry_caption">Exchange Rate</td>
				<td><input type="text" name="txt_exchange_rate_order" id="txt_exchange_rate_order" class="text_boxes_numeric" style="width:140px" disabled="disabled" /></td>
				<td align="center" class="must_entry_caption">Amount (TK.)</td>
				<td><input type="text" name="txt_amount" id="txt_amount" class="text_boxes_numeric"style="width:140px" onkeyup="calculate_balance(1)"/></td>
			</tr>
            <tr <? echo $display; ?>>
                <td align="center" class="must_entry_caption">Based On</td>
                <td>
                    <? 
						if($data==5)
						{
                        	echo create_drop_down( "cbo_based_on", 152, $based_on,'', '0', '---- Select ----', 1,"",''); 
						}
						else
						{
							echo create_drop_down( "cbo_based_on", 152, $based_on,'', '0', '---- Select ----', 1,"",'','1,2'); 	
						}
                    ?>	
                </td>
                <td align="center" style="padding-left:5px"><input type="button" class="formbuttonplasminus" id="details" name="details" value="Show Details" onclick="show_list_view_details();"/></td>
            </tr>
		</table>
	</div> -->
<?
	exit();
}

if($action=="check_conversion_rate")
{ 
	$data=explode("**",$data);
	if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$exchange_rate=set_conversion_rate( $data[0], $conversion_date );
	echo $exchange_rate;
	exit();	
}

if($action=='populate_data_from_actual_cost')
{
	$data=explode("**",$data);
	
	if($db_type==0)
	{
		$incurred_date=change_date_format(trim($data[2]), "yyyy-mm-dd", "-");
		$incurred_date_to=change_date_format(trim($data[3]), "yyyy-mm-dd", "-");
	}
	else
	{
		$incurred_date=change_date_format(trim($data[2]),'','',1);
		$incurred_date_to=change_date_format(trim($data[3]),'','',1);
	}

	$data_array=sql_select("select sum(amount) as amount, exchange_rate, applying_period_date, applying_period_to_date from wo_actual_cost_entry where company_id='$data[0]' and cost_head='$data[1]' and incurred_date='$incurred_date' and incurred_date_to='$incurred_date_to' and status_active=1 and is_deleted=0 group by exchange_rate, applying_period_date, applying_period_to_date");
	
	if(count($data_array)>0) 
	{
		$button_status=1;
		$exchange_rate=$data_array[0][csf("exchange_rate")];
	}
	else 
	{
		$exchange_rate=set_conversion_rate( 2, $incurred_date_to );
		$button_status=0;
	}
	
	echo "document.getElementById('txt_exchange_rate_order').value 			= '".$exchange_rate."';\n";
	echo "document.getElementById('txt_amount').value 						= '".$data_array[0][csf("amount")]."';\n";
	echo "document.getElementById('txt_applying_period_date').value 		= '".change_date_format($data_array[0][csf("applying_period_date")])."';\n";
	echo "document.getElementById('txt_applying_period_to_date').value 		= '".change_date_format($data_array[0][csf("applying_period_to_date")])."';\n";
	echo "set_button_status($button_status, '".$_SESSION['page_permission']."', 'fnc_actual_cost_entry',1,1);\n";  
	
	exit();
}

if($action=="show_po_listview")
{
	$data=explode("**",$data);
	$company_id=$data[0];
	$cost_head=$data[1];
	
	if($db_type==0)
	{
		$applying_period_date=change_date_format(trim($data[2]), "yyyy-mm-dd", "-");
		$applying_period_to_date=change_date_format(trim($data[3]), "yyyy-mm-dd", "-");
		$incurred_date=change_date_format(trim($data[4]), "yyyy-mm-dd", "-");
		$incurred_date_to=change_date_format(trim($data[5]), "yyyy-mm-dd", "-");
	}
	else
	{
		$applying_period_date=change_date_format(trim($data[2]),'','',1);
		$applying_period_to_date=change_date_format(trim($data[3]),'','',1);
		$incurred_date=change_date_format(trim($data[4]),'','',1);
		$incurred_date_to=change_date_format(trim($data[5]),'','',1);
	}
	
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$amnt_arr=return_library_array( "select po_id, amount from wo_actual_cost_entry where company_id='$company_id' and cost_head='$cost_head' and incurred_date='$incurred_date' and incurred_date_to='$incurred_date_to' and status_active=1 and is_deleted=0",'po_id','amount');
	?>
    <table cellspacing="0" width="820" class="rpt_table" border="1" rules="all">
        <thead>
            <th colspan="8" width="">&nbsp;</th>
            <th align="right" width="160" style="color:#F00">Remaining Amount :</th>
            <th id="tot_remain" width="110" style="color:#F00">0</th>
        </thead>
    </table>            
    <table cellspacing="0" width="820" class="rpt_table" border="1" rules="all">
        <thead>
            <th width="30">SL</th>
            <th width="60">Buyer Name</th>
            <th width="60">Order Status</th>
            <th width="80">PO Number</th>
            <th width="90">Job Number</th>
            <th width="100">Style Name</th>
            <th width="120">Item Name</th>
            <th width="80">Shipment Date</th>
            <th width="80">Order Quantity</th>
            <th>Amount(TK.)</th>
        </thead>
    </table>
    <div style="width:820px; overflow-y:scroll; max-height:250px;" id="search_div">
    	<table cellspacing="0" width="800" class="rpt_table" border="1" rules="all" id="table_body"> 
		<?
		$select_field='';
		if($cost_head==1) $select_field='lab_test';
		else if($cost_head==2) $select_field='freight';
		else if($cost_head==3) $select_field='inspection';
		else $select_field='currier_pre_cost';
		$fabriccostDataArray=sql_select("select job_no, $select_field from wo_pre_cost_dtls where status_active=1 and is_deleted=0");
		foreach($fabriccostDataArray as $fabRow)
		{
			 $fabriccostArray[$fabRow[csf('job_no')]]=$fabRow[csf($select_field)];
		}
		
        $sql="select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, a.total_set_qnty, b.id as po_id, b.po_number, b.po_quantity, b.is_confirmed, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_id' and b.pub_shipment_date between '$applying_period_date' and '$applying_period_to_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.pub_shipment_date, a.id";
		$result=sql_select($sql);
        $i=1; $tot_po_qty=0; $tot_amount=0;
        foreach($result as $row)
        {
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$po_qty=$row[csf("total_set_qnty")]*$row[csf("po_quantity")];
			$tot_po_qty+=$po_qty; 
			$amount=$amnt_arr[$row[csf("po_id")]];
			$tot_amount+=$amount;
			
			if($fabriccostArray[$row[csf('job_no')]]>0) {$bgcolor="yellow";}
		?>
            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30"><? echo $i; ?></td>
                <td width="60"><p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
                <td width="60"><? echo $order_status[$row[csf("is_confirmed")]]; ?></td>
              	<td width="80"><p><? echo $row[csf("po_number")]; ?></p>
                    <input type="hidden" name="po_id_<? echo $i; ?>" id="po_id_<? echo $i; ?>" value="<? echo $row[csf("po_id")]; ?>">
                </td>
                <td width="90" id="job_no_<? echo $i; ?>"><p><? echo $row[csf("job_no")] ?></p></td>
                <td width="100"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
                <td width="120">
                    <p>
						<? 
							$gmts_item='';
							$gmts_item_id=explode(",",$row[csf("gmts_item_id")]);
							foreach($gmts_item_id as $item_id)
							{
								$gmts_item.=$garments_item[$item_id].",";
							}
							$gmts_item=substr($gmts_item,0,-1); 
                        	echo $gmts_item; 
                        ?>
                    </p>
                </td>
                <td width="80" align="center"><p><? echo change_date_format($row[csf("shipment_date")]); ?></p></td>
                <td width="80" align="right"><p><? echo $po_qty; ?></p></td>
                <td align="center">
                    <input type="text" name="txt_amount_<? echo $i; ?>" id="txt_amount_<? echo $i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? echo $amount; ?>" onkeyup="calculate_balance(<? echo $i; ?>);">
                </td>
            </tr>
        <?	
            $i++;
		}
		?>
		</table>
    </div>
    <table cellspacing="0" width="820" class="rpt_table" border="1" rules="all">
        <tfoot>	 
           <th colspan="8">Total</th>
           <th align="right" width="80"><? echo $tot_po_qty; ?></th>
           <th align="center" width="108"><input type="text" name="tot_amount" id="tot_amount" style="width:70px;" class="text_boxes_numeric" value="<? echo $tot_amount; ?>" readonly="readonly">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>	
         </tfoot>	
    </table>
<?
	exit();
}

if($action=="show_details_listview")
{
	if($data=='0') $data='';
?>
    <table cellspacing="0" width="750" class="rpt_table" border="1" rules="all">
        <thead>
            <th width="50">SL</th>
            <th width="70">Company</th>
            <th width="90">Cost Head</th>
            <th width="90">Based On</th>
            <th width="90">Incurred Date</th>
            <th width="90">Incurred Date To</th>
            <th width="90">Period From</th>
            <th width="90">Period To</th>
            <th>Amount(TK.)</th>
        </thead>
    </table>
    <div style="width:770px; overflow-y:scroll; max-height:250px;" id="search_div">
    	<table cellspacing="0" width="750" class="rpt_table" border="1" rules="all" id="table_body"> 
		<?
        $sql="select a.id, a.company_short_name, b.cost_head, b.incurred_date, b.incurred_date_to, b.applying_period_date, b.applying_period_to_date, b.based_on, sum(b.amount) as amount from lib_company a, wo_actual_cost_entry b where a.id=b.company_id and a.company_name like '%".$data."%' and b.cost_head in(5,6) and a.status_active=1 and a.is_deleted=0 group by a.id, a.company_short_name, b.cost_head, b.incurred_date, b.incurred_date_to, b.applying_period_date, b.applying_period_to_date, b.based_on";
		$result=sql_select($sql);
        $i=1;
        foreach($result as $row)
        {
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		?>
            <tr bgcolor="<? echo $bgcolor; ?>" onClick="get_php_form_data('<? echo $row[csf("id")]."_".$row[csf("cost_head")]."_".$row[csf("incurred_date")]."_".$row[csf("incurred_date_to")]; ?>', 'populate_data_from_cm_cost','requires/actual_cost_entry_controller');" id="tr_<? echo $i; ?>" style="cursor:pointer" >
            	<td width="50"><? echo $i; ?></td>
                <td width="70"><p><? echo $row[csf("company_short_name")]; ?></p></td>
                <td width="90"><? echo $actual_cost_heads[$row[csf("cost_head")]]; ?></td>
                <td width="90" align="center"><? echo $based_on[$row[csf("based_on")]]; ?></td>
                <td width="90" align="center"><? echo change_date_format($row[csf("incurred_date")]); ?></td>
                <td width="90" align="center"><? echo change_date_format($row[csf("incurred_date_to")]); ?></td>
                <td width="90" align="center"><? echo change_date_format($row[csf("applying_period_date")]); ?></td>
                <td width="90" align="center"><? echo change_date_format($row[csf("applying_period_to_date")]); ?></td>
                <td align="right"><? echo number_format($row[csf("amount")],2,'.',''); ?></td>
            </tr>
        <?	
            $i++;
		}
		?>
		</table>
    </div>
<?
	exit();
}

if($action=='populate_data_from_cm_cost')
{
	$data=explode("_",$data);
	$data_array=sql_select("select max(based_on) as based_on, max(exchange_rate) as exchange_rate, max(applying_period_date) as applying_period_date, max(applying_period_to_date) as applying_period_to_date, sum(amount) as amount from wo_actual_cost_entry where company_id='$data[0]' and cost_head='$data[1]' and incurred_date='$data[2]' and incurred_date_to='$data[3]' and status_active=1 and is_deleted=0");
	
	echo "document.getElementById('cbo_company_id').value 					= '".$data[0]."';\n";
	echo "$('#cbo_company_id').attr('disabled','true')".";\n";
	echo "document.getElementById('cbo_cost_head').value 					= '".$data[1]."';\n";
	echo "document.getElementById('cbo_based_on').value 					= '".$data_array[0][csf("based_on")]."';\n";
	echo "document.getElementById('txt_exchange_rate_order').value 			= '".$data_array[0][csf("exchange_rate")]."';\n";
	echo "document.getElementById('txt_amount').value 						= '".number_format($data_array[0][csf("amount")],2,'.','')."';\n";
	echo "document.getElementById('txt_incurred_date').value 				= '".change_date_format($data[2])."';\n";
	echo "document.getElementById('txt_incurred_to_date').value 			= '".change_date_format($data[3])."';\n";
	echo "document.getElementById('txt_applying_period_date').value 		= '".change_date_format($data_array[0][csf("applying_period_date")])."';\n";
	echo "document.getElementById('txt_applying_period_to_date').value 		= '".change_date_format($data_array[0][csf("applying_period_to_date")])."';\n";
	
	echo "$('#cm_commercial_list_view_details').html('')".";\n";
	echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_actual_cost_entry',1,1);\n";  
	
	exit();
}

if($action=="show_details_listview_po")
{
	$data=explode("**",$data);
	$company_id=$data[0];
	$cost_head=$data[1];
	$cbo_based_on=$data[2];
	
	if($db_type==0)
	{
		$incurred_date=change_date_format(trim($data[3]), "yyyy-mm-dd", "-");
		$incurred_date_to=change_date_format(trim($data[4]), "yyyy-mm-dd", "-");
	}
	else
	{
		$incurred_date=change_date_format(trim($data[3]),'','',1);
		$incurred_date_to=change_date_format(trim($data[4]),'','',1);
	}
	$sql="select a.job_no, a.buyer_name, a.style_ref_no, a.total_set_qnty, a.order_uom, a.set_break_down, b.po_number, b.po_quantity, b.is_confirmed, b.pub_shipment_date as shipment_date, c.amount, c.gmts_item_id, c.production_qty, c.item_smv, c.smv_produced, c.based_on from wo_po_details_master a, wo_po_break_down b, wo_actual_cost_entry c where a.job_no=b.job_no_mst and b.id=c.po_id and c.company_id='$company_id' and c.cost_head='$cost_head' and c.incurred_date='$incurred_date' and c.incurred_date_to='$incurred_date_to' and c.status_active=1 and c.is_deleted=0 order by b.pub_shipment_date, a.id";
	$result=sql_select($sql);
	$cbo_based_on=$result[0][csf('based_on')];
	//echo $cbo_based_on;
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	
	if($cbo_based_on==3)
	{
	?>
        <table cellspacing="0" width="1000" class="rpt_table" border="1" rules="all">
            <thead>
                <th colspan="13" width=""><? echo $actual_cost_heads[$cost_head]; ?> Cost Details</th>
            </thead>
        </table>            
        <table cellspacing="0" width="1000" class="rpt_table" border="1" rules="all">
            <thead>
                <th width="30">SL</th>
                <th width="60">Buyer Name</th>
                <th width="60">Order Status</th>
                <th width="80">PO Number</th>
                <th width="90">Job Number</th>
                <th width="100">Style Name</th>
                <th width="110">Item Name</th>
                <th width="80">Shipment Date</th>
                <th width="80">Order Qty.</th>
                <th width="50">SMV</th>
                <th width="80">Sewing Qty.</th>
                <th width="80">Produce Minute</th>
                <th>Amount(TK.)</th>
            </thead>
        </table>
        <div style="width:1020px; overflow-y:scroll; max-height:250px;" id="search_div">
            <table cellspacing="0" width="1000" class="rpt_table" border="1" rules="all" id="table_body"> 
            <?
            $i=1; $tot_po_qty=0; $tot_amount=0;
            foreach($result as $row)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                
				if($row[csf("order_uom")]==1)
				{
                	$po_qty=$row[csf("po_quantity")];
				}
				else
				{
					$ratio=1;
					$exp_grmts_item = explode("__",$row[csf("set_break_down")]);
					foreach($exp_grmts_item as $value)
					{
						$grmts_item_qty = explode("_",$value);
						if($row[csf('gmts_item_id')]==$grmts_item_qty[0])
						{
							$ratio=$grmts_item_qty[1];
							break;
						}
					}
					$po_qty=$row[csf("po_quantity")]*$ratio;	
				}
				
                $tot_po_qty+=$po_qty; 
                
                $qty=$row[csf("production_qty")];
                $item_smv=$row[csf("item_smv")];
                $produce_min=$row[csf("smv_produced")];
                $amount=$row[csf("amount")];
                
                $tot_prod_qty+=$qty;
                $tot_produce_min+=$produce_min;
                $tot_amount+=$amount;
                
            ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
                    <td width="60"><? echo $order_status[$row[csf("is_confirmed")]]; ?></td>
                    <td width="80"><p><? echo $row[csf("po_number")]; ?></p></td>
                    <td width="90" id="job_no_<? echo $i; ?>"><p><? echo $row[csf("job_no")] ?></p></td>
                    <td width="100"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
                    <td width="110"><p><?  echo $garments_item[$row[csf("gmts_item_id")]]; ?></p></td>
                    <td width="80" align="center"><p><? echo change_date_format($row[csf("shipment_date")]); ?></p></td>
                    <td width="80" align="right"><p><? echo $po_qty; ?></p></td>
                    <td width="50" align="right"><p><? echo $item_smv; ?></p></td>
                    <td width="80" align="right"><p><? echo $qty; ?></p></td>
                    <td width="80" align="right"><p><? echo $produce_min; ?></p></td>
                    <td align="right"><? echo number_format($amount,2,'.',''); ?></td>
                </tr>
            <?	
                $i++;
            }
            ?>
            </table>
        </div>
        <table cellspacing="0" width="1020" class="rpt_table" border="1" rules="all">
            <tfoot>	 
               <th colspan="8">Total</th>
               <th align="right" width="80"><? echo $tot_po_qty; ?></th>
               <th align="right" width="50">&nbsp;</th>
               <th align="right" width="80"><? echo $tot_prod_qty; ?></th>
               <th align="right" width="80"><? echo number_format($tot_produce_min,2,'.',''); ?></th>
               <th align="right" width="106"><? echo number_format($tot_amount,2,'.',''); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>	
             </tfoot>	
        </table>
	<?
	}
	else
	{
	?>
		<table cellspacing="0" width="1000" class="rpt_table" border="1" rules="all">
            <thead>
                <th colspan="13" width=""><? echo $actual_cost_heads[$cost_head]; ?> Cost Details</th>
            </thead>
        </table>            
        <table cellspacing="0" width="1000" class="rpt_table" border="1" rules="all">
            <thead>
                <th width="30">SL</th>
                <th width="70">Buyer Name</th>
                <th width="70">Order Status</th>
                <th width="100">PO Number</th>
                <th width="90">Job Number</th>
                <th width="110">Style Name</th>
                <th width="120">Item Name</th>
                <th width="80">Shipment Date</th>
                <th width="100">Order Qty.</th>
                <th width="100"><? if($cbo_based_on==1) echo  "Ex-Factory"; else echo "Sewing"; ?> Qty.</th>
                <th>Amount(TK.)</th>
            </thead>
        </table>
        <div style="width:1020px; overflow-y:scroll; max-height:250px;" id="search_div">
            <table cellspacing="0" width="1000" class="rpt_table" border="1" rules="all" id="table_body"> 
            <?
            $i=1; $tot_po_qty=0; $tot_amount=0;
            foreach($result as $row)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                
				if($row[csf("order_uom")]==1)
				{
                	$po_qty=$row[csf("po_quantity")];
				}
				else
				{
					$ratio=1;
					$exp_grmts_item = explode("__",$row[csf("set_break_down")]);
					foreach($exp_grmts_item as $value)
					{
						$grmts_item_qty = explode("_",$value);
						if($row[csf('gmts_item_id')]==$grmts_item_qty[0])
						{
							$ratio=$grmts_item_qty[1];
							break;
						}
					}
					$po_qty=$row[csf("po_quantity")]*$ratio;	
				}
                $tot_po_qty+=$po_qty; 
                
                $qty=$row[csf("production_qty")];
                $amount=$row[csf("amount")];
                
                $tot_prod_qty+=$qty;
                $tot_amount+=$amount;
                
            ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="70"><p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
                    <td width="70"><? echo $order_status[$row[csf("is_confirmed")]]; ?></td>
                    <td width="100"><p><? echo $row[csf("po_number")]; ?></p></td>
                    <td width="90" id="job_no_<? echo $i; ?>"><p><? echo $row[csf("job_no")] ?></p></td>
                    <td width="110"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
                    <td width="120"><p><?  echo $garments_item[$row[csf("gmts_item_id")]]; ?></p></td>
                    <td width="80" align="center"><p><? echo change_date_format($row[csf("shipment_date")]); ?></p></td>
                    <td width="100" align="right"><p><? echo $po_qty; ?></p></td>
                    <td width="100" align="right"><p><? echo $qty; ?></p></td>
                    <td align="right"><? echo number_format($amount,2,'.',''); ?></td>
                </tr>
            <?	
                $i++;
            }
            ?>
            </table>
        </div>
        <table cellspacing="0" width="1020" class="rpt_table" border="1" rules="all">
            <tfoot>	 
               <th colspan="8">Total</th>
               <th align="right" width="100"><? echo $tot_po_qty; ?></th>
               <th align="right" width="100"><? echo $tot_prod_qty; ?></th>
               <th align="right" width="138"><? echo number_format($tot_amount,2,'.',''); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>	
             </tfoot>	
        </table>
	<?	
	}
	exit();
}
?>