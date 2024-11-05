<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']["user_id"];
$permission=$_SESSION['page_permission'];

//--------------------------- Start-------------------------------------//

if ($action=="system_popup")
{

	echo load_html_head_contents("Popup Info", "../../../", 1, 1,$unicode,'1','');
	extract($_REQUEST);
	?>
	<script>
		
	
		function js_set_value( id )
		{
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
		function fn_show()
		{
			
			if(form_validation('cbo_company_name*cbo_type_name','Company*C&F Type')==false){
					return;
				}
			show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_type_name').value+'_'+document.getElementById('cbo_candf_name').value+'_'+document.getElementById('txt_invoice_no').value+'_'+document.getElementById('txt_bill_no').value+'_'+document.getElementById('cbo_based_on').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_system_search_list_view', 'search_div', 'cnf_bill_entry_controller', 'setFilterGrid(\'list_view\',-1)')
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="950" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
            	<tr>
                	<th colspan="10" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",1 ); ?></th>
                </tr>
                <tr>
                    <th>Company Name</th>
                    <th>Buyer Name</th>
                    <th>C&F Type</th>
                    <th>C&F Name</th>
                    <th>Invoice No</th>
                    <th>Bill NO</th>
                    <th>Based On </th>
                    <th colspan="2">Date Range</th>
					<th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
					<input type="hidden" name="id_field" id="id_field" value="" /></th>
                </tr>        
            </thead>
            <tr class="general">
                <td> 
                    <input type="hidden" id="selected_id">
					<? echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"",'' ); ?>
                </td>
        		<td id="buyer_pop_td">
				<?
				echo create_drop_down("cbo_buyer_name", 120, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "--- Select Buyer ---", $selected, "");
				?>
				</td>
        		<td>
				<?
                echo create_drop_down( "cbo_type_name",100,array(1=>"Export",2=>"Import"),'',1,'--Select--',$cbo_type_name,"",'1');
                ?>
                <td>
				<?
                echo create_drop_down( "cbo_candf_name",100,"select a.id, a.supplier_name FROM lib_supplier a , lib_supplier_party_type b WHERE a.id= b.supplier_id and b.party_type=30 and a.STATUS_ACTIVE=1 AND a.IS_DELETED=0","ID,supplier_name", 1, "-- Select --", 0, "" );
                ?>
				</td>                
                <td>
				<input type="text" name="txt_invoice_no" id="txt_invoice_no" style="width:80px" class="text_boxes" />
				</td>
                <td ><input type="text" name="txt_bill_no" id="txt_bill_no" style="width:80px" class="text_boxes" ></td>
                <td>
				<?
                echo create_drop_down( "cbo_based_on",100,array(1=>"Invoice Date",2=>"Bill Date"),'',1,'--Select--',0,"",0);
                ?>
				</td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"></td> 
                <td align="center">
                	<input type="button" name="button2" class="formbutton" value="Show" onClick="fn_show()" style="width:100px;" /></td>
        	</tr>
            <tr>
                <td align="center" colspan="10"><? echo load_month_buttons(1); ?></td>
            </tr>
        </table>
        <div id="search_div"></div>
    </form>
    </div>
    </body>
      <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_system_search_list_view")
{
	//echo $data;die;

	list($company_id, $buyer_id, $cnf_type, $cnf_supplier_id, $invoice_num, $bill_num, $based_on, $invoice_start_date, $invoice_end_date, $search_string) = explode('_', $data);
	if ($company_id!=0) {$company=" and company_id=$company_id";} else { echo "Please Select Company First."; die;}
	if($cnf_type!=0){$cnf_id="and cnf_type= $cnf_type";}
	if(str_replace("'","",$buyer_id)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") {$buyer=" and buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";} else {$buyer="";}
		}
		else {$buyer="";}
	}
	else {$buyer=" and buyer_id=$buyer_id'";}
	
	//if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
			
	 $search_text="";$date_cond ="";
	if ($invoice_num != '')
	{
		if($search_string==1)
			$search_text="and invoice_no like '".trim($invoice_num)."'";
		else if ($search_string==2) 
			$search_text="and invoice_no like '".trim($invoice_num)."%'";
		else if ($search_string==3)
			$search_text="and invoice_no like '%".trim($invoice_num)."'";
		else if ($search_string==4 || $search_string==0)
			$search_text="and invoice_no like '%".trim($invoice_num)."%'";
	}

	if ($invoice_start_date != '' && $invoice_end_date != '') 
	{
		if($based_on==1){
        if ($db_type == 0) {
            $date_cond = "and invoice_date between '" . change_date_format($invoice_start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($invoice_end_date, 'yyyy-mm-dd') . "'";
        } else if ($db_type == 2) {
            $date_cond = "and invoice_date between '" . change_date_format($invoice_start_date, '', '', 1) . "' and '" . change_date_format($invoice_end_date, '', '', 1) . "'";
		}
		}
		if($based_on==2){
        if ($db_type == 0) {
            $date_cond = "and bill_date between '" . change_date_format($invoice_start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($invoice_end_date, 'yyyy-mm-dd') . "'";
        } else if ($db_type == 2) {
            $date_cond = "and bill_date between '" . change_date_format($invoice_start_date, '', '', 1) . "' and '" . change_date_format($invoice_end_date, '', '', 1) . "'";
		}
		}
    } 
    else 
    {
        $date_cond = '';
	}
	if($cnf_supplier_id!=0) {$cnf_supplier="and cnf_name_id= $cnf_supplier_id";}
	if($bill_num!='') {$bill_no="and bill_no= $bill_num";}
	 $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	 $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	$arr=array(0=>$company_arr,1=>$supplier_arr);
	$sql= "select id, company_id, cnf_type, cnf_name_id,sys_number, invoice_no, invoice_date, invoice_value, invoice_value_bdt, bill_no, bill_date, buyer_id from cnf_bill_mst where status_active=1 and is_deleted=0 $cnf_id $cnf_supplier $date_cond $company $bill_no $buyer $search_text order by id DESC";

	// echo $sql;die;
	echo  create_list_view("list_view", "Company,C&F Name,System ID,Invoice No,Invoice Date, Bill NO, Bill Date, Invoice Value,Inv. Value BDT", "120,120,120,120,90,90,90,90,90","1000","300",0, $sql , "js_set_value", "id", "", 1, "company_id,cnf_name_id,0,0,0,0,0,0,0", $arr , "company_id,cnf_name_id,sys_number,invoice_no,invoice_date,bill_no,bill_date,invoice_value,invoice_value_bdt", "",'','0,0,0,0,3,0,3,0,0,0');
	exit();
} 

if ($action=="populate_data_from_landing_charge")
{
	extract($_REQUEST);
	$ex_data=explode('**', $data);
	$company_id=$ex_data[0];
	$cnf_type=$ex_data[1];
	$gross_weight=$ex_data[2];

	$sql="select a.company_id, a.cnf_type, a.slub_name, b.from_unit, b.to_unit, b.charge from lib_cnf_bill_slub_mst a, lib_cnf_bill_slub_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.cnf_type=$cnf_type and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
	$data_array=sql_select($sql);
	foreach ($data_array as $row) 
	{
		if ($row[csf('from_unit')]<=$gross_weight  && $row[csf('to_unit')]>=$gross_weight) {
			echo "document.getElementById('txtamount_6').value = '".$row[csf('charge')]."';\n";
		}
	}
	exit();
}

if ($action=="populate_data_from_search_popup")
{
	// echo "select id, sys_number_prefix, sys_number, sys_number_prefix_num, company_id, cnf_type, cnf_name_id, ex_rate, invoice_no, invoice_date, invoice_value, invoice_value_bdt, invoice_qty, pack_qty, gross_weight, bill_no, bill_date, buyer_id, sb_no, job_no, ship_mod, container_id, container_rate, remarks from cnf_bill_mst where id='$data'";die;
	$data_array=sql_select("select id, sys_number, company_id, cnf_type, cnf_name_id, ex_rate, invoice_no,invoice_id, invoice_date, invoice_value, invoice_value_bdt, invoice_qty, pack_qty, gross_weight, bill_no, bill_date, buyer_id, sb_no, job_no, ship_mod, container_id, container_rate, remarks, total_amount,ready_to_approve, is_posted_account from cnf_bill_mst where id='$data' and is_deleted=0 and status_active=1");

	
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_system_id').value = '".$row[csf("sys_number")]."';\n";  
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('cbo_type_name').value = '".$row[csf("cnf_type")]."';\n";  
		echo "document.getElementById('cbo_candf_name').value = '".$row[csf("cnf_name_id")]."';\n";  
		echo "document.getElementById('txt_ex_rate').value = '".$row[csf("ex_rate")]."';\n";  
		echo "document.getElementById('txt_invoice_no').value = '".$row[csf("invoice_no")]."';\n";
		echo "document.getElementById('invoice_id').value = '".$row[csf("invoice_id")]."';\n";
		echo "document.getElementById('txt_invoice_date').value = '".change_date_format($row[csf("invoice_date")])."';\n";
		echo "document.getElementById('txt_invoice_value').value = '".$row[csf("invoice_value")]."';\n";  
		echo "document.getElementById('txt_value_bdt').value = '".$row[csf("invoice_value_bdt")]."';\n"; 
		echo "document.getElementById('txt_value_qty').value = '".$row[csf("invoice_qty")]."';\n"; 
		echo "document.getElementById('txt_pack_qty').value = '".$row[csf("pack_qty")]."';\n";  
		echo "document.getElementById('txt_gross').value = '".$row[csf("gross_weight")]."';\n";  
		echo "document.getElementById('txt_bill_no').value = '".$row[csf("bill_no")]."';\n";  
		echo "document.getElementById('txt_bill_date').value = '".change_date_format($row[csf("bill_date")])."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";  
		echo "document.getElementById('txt_sb_no').value = '".$row[csf("sb_no")]."';\n";  
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";  
		echo "document.getElementById('cbo_shipment_id').value = '".$row[csf("ship_mod")]."';\n";
		echo "document.getElementById('cbo_container_name').value = '".$row[csf("container_id")]."';\n";
		echo "document.getElementById('txt_container_rate').value = '".$row[csf("container_rate")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('txtamount_tot').value = '".$row[csf("total_amount")]."';\n";
		echo "document.getElementById('cbo_approve_status').value = '".$row[csf("ready_to_approve")]."';\n";
		
		echo "document.getElementById('hidden_posted_in_account').value = '".$row[csf("is_posted_account")]."';\n";
		if($row[csf("is_posted_account")]==1)
		{
			echo "$('#is_posted_accounts').text('Already Posted In Accounts');\n";
		}
		else
		{
			echo "$('#is_posted_accounts').text('');\n";
		}
		
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n"; 
		echo "$('#cbo_company_name').attr('disabled',true);\n"; 
		echo "$('#cbo_type_name').attr('disabled',true);\n"; 
		echo "$('#cbo_candf_name').attr('disabled',true);\n"; 
		echo "$('#txt_invoice_no').attr('disabled',true);\n"; 
		if($row[csf("invoice_id")]!='')
		{
			echo "$('#txt_value_qty').attr('readonly',true);\n"; 
		}
	}

	$dtls_arr=sql_select("select id, mst_id, description_id, charge_id, cost_percent, cost_per_tk, amount from cnf_bill_dtls where mst_id='$data' and is_deleted=0 and status_active=1 order by id asc");
	foreach ($dtls_arr as $row)
	{
		echo "document.getElementById('cbo_formula_".$row[csf("description_id")]."').value = '".$row[csf("charge_id")]."';\n";  
		echo "document.getElementById('txtcost_".$row[csf("description_id")]."').value = '".$row[csf("cost_percent")]."';\n";  
		echo "document.getElementById('txtcostper_".$row[csf("description_id")]."').value = '".$row[csf("cost_per_tk")]."';\n";  
		echo "document.getElementById('txtamount_".$row[csf("description_id")]."').value = '".$row[csf("amount")]."';\n";  
	}

	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	
		$mst_id=return_next_id("id", "CNF_BILL_MST", 1);
		
		if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
		else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
		
		$new_sys_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'CNF', date("Y",time()), 5, "select sys_number_prefix,sys_number_prefix_num from CNF_BILL_MST where company_id=$cbo_company_name $insert_date_con order by id desc ", "sys_number_prefix", "sys_number_prefix_num" ));

		$field_array_mst="id, sys_number, sys_number_prefix, sys_number_prefix_num, company_id, cnf_type, cnf_name_id, ex_rate, invoice_no,invoice_id, invoice_date, invoice_value, invoice_value_bdt, invoice_qty, pack_qty, gross_weight, bill_no, bill_date, buyer_id, sb_no, job_no, ship_mod, container_id, container_rate, remarks,total_amount,ready_to_approve, inserted_by, insert_date, status_active, is_deleted";
		$data_array_mst="(".$mst_id.",'".$new_sys_no[0]."','".$new_sys_no[1]."','".$new_sys_no[2]."',".$cbo_company_name.",".$cbo_type_name.",".$cbo_candf_name.",".$txt_ex_rate.",".$txt_invoice_no.",".$invoice_id.",".$txt_invoice_date.",".$txt_invoice_value.",".$txt_value_bdt.",".$txt_value_qty.",".$txt_pack_qty.",".$txt_gross.",".$txt_bill_no.",".$txt_bill_date.",".$cbo_buyer_name.",".$txt_sb_no.",".$txt_job_no.",".$cbo_shipment_id.",".$cbo_container_name.",".$txt_container_rate.",".$txt_remarks.",".$txtamount_tot.",".$cbo_approve_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

		// echo "10**INSERT INTO CNF_BILL_MST (".$field_array_mst.") VALUES ".$data_array_mst; oci_rollback($con);disconnect($con);die;
		// die;

		$field_array_dtls="id, mst_id, description_id, charge_id, cost_percent, cost_per_tk, amount,deduction,payable, inserted_by, insert_date, is_deleted, status_active";

		$id_dtls=return_next_id("id", "CNF_BILL_dtls", 1);
		$row_num_arr = explode(',',$row_num_arr);
		$data_array_dtls='';
		for($m=0; $m<sizeof($row_num_arr); $m++)
		{
			$mm=$row_num_arr[$m];
			$txtbillid="txtbillid_".$mm;
			$cbo_formula="cbo_formula_".$mm;
			$txtcost="txtcost_".$mm;
			$txtcostper="txtcostper_".$mm;
			$txtamount="txtamount_".$mm;
			$txtdaducation="txtdaducation_".$mm;
			$txtpaybale="txtpaybale_".$mm;
			if($$txtamount>0)
			{
				if ($data_array_dtls!='') {$data_array_dtls .=",";}
				$data_array_dtls .="(".$id_dtls.",".$mst_id.",'".$$txtbillid."','".$$cbo_formula."','".$$txtcost."','".$$txtcostper."','".$$txtamount."','".$$txtdaducation."','".$$txtpaybale."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";
				$id_dtls++;
			}
		}
		//echo "INSERT INTO CNF_BILL_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls; disconnect($con);die;
		$rID=sql_insert("CNF_BILL_MST",$field_array_mst,$data_array_mst,0);
		$rID1=sql_insert("cnf_bill_dtls",$field_array_dtls,$data_array_dtls,0);	
		//echo '10**'.$rID.'**'.$rID1;oci_rollback($con);disconnect($con);die;
		
		if($db_type==0)
		{
			if($rID==1 && $rID1==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$new_sys_no[0]."**".$mst_id."**".$id_dtls;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1 && $rID1==1)
			{
				oci_commit($con);  
				echo "0**".$new_sys_no[0]."**".$mst_id."**".$id_dtls;
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
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array_mst="company_id*cnf_type*cnf_name_id*ex_rate*invoice_no*invoice_id*invoice_date*invoice_value*invoice_value_bdt*invoice_qty*pack_qty*gross_weight*bill_no*bill_date*buyer_id*sb_no*job_no*ship_mod*container_id*container_rate*remarks*total_amount*ready_to_approve*updated_by*update_date";
		$data_array_mst="".$cbo_company_name."*".$cbo_type_name."*".$cbo_candf_name."*".$txt_ex_rate."*".$txt_invoice_no."*".$invoice_id."*".$txt_invoice_date."*".$txt_invoice_value."*".$txt_value_bdt."*".$txt_value_qty."*".$txt_pack_qty."*".$txt_gross."*".$txt_bill_no."*".$txt_bill_date."*".$cbo_buyer_name."*".$txt_sb_no."*".$txt_job_no."*".$cbo_shipment_id."*".$cbo_container_name."*".$txt_container_rate."*".$txt_remarks."*".$txtamount_tot."*".$cbo_approve_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$field_array_dtls="id, mst_id, description_id, charge_id, cost_percent, cost_per_tk, amount, inserted_by, insert_date, is_deleted, status_active";
		$id_dtls=return_next_id("id", "CNF_BILL_dtls", 1);
		$row_num_arr = explode(',',$row_num_arr);
		$data_array_dtls='';
		for($m=0; $m<sizeof($row_num_arr); $m++)
		{
			$mm=$row_num_arr[$m];
			$txtbillid="txtbillid_".$mm;
			$cbo_formula="cbo_formula_".$mm;
			$txtcost="txtcost_".$mm;
			$txtcostper="txtcostper_".$mm;
			$txtamount="txtamount_".$mm;
			if($$txtamount>0)
			{
				if ($data_array_dtls!='') {$data_array_dtls .=",";}
				$data_array_dtls .="(".$id_dtls.",".$update_id.",'".$$txtbillid."','".$$cbo_formula."','".$$txtcost."','".$$txtcostper."','".$$txtamount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";
				$id_dtls++;
			}
		}

		$rID=sql_update("cnf_bill_mst",$field_array_mst,$data_array_mst,"id","".$update_id."",0);
		$rID1=execute_query("delete from cnf_bill_dtls where mst_id =".$update_id."",0);
		$rID2=sql_insert("cnf_bill_dtls",$field_array_dtls,$data_array_dtls,0);	
	
		// echo "10**".$rID.'='.$rID1.'='.$rID2."</br>"; die;
		if($db_type==0)
		{
			if($rID==1 && $rID1==1 && $rID2==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1 && $rID1==1 && $rID2==1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
	
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here----------------------------------------------------------  
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID=sql_delete("CNF_BILL_MST",$field_array,$data_array,"id","".$update_id."",0);
		$rID1=sql_delete("cnf_bill_dtls",$field_array,$data_array,"mst_id","".$update_id."",0);
	// echo "10**".$rID.'='.$rID1."</br>"; die;
		if($db_type==0)
		{
			if($rID==1 && $rID1==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id);;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1 && $rID1==1)
			{
				oci_commit($con);  
				echo "2**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id);;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		
		disconnect($con);
	}// Delete Here End ----------------------------------------------------------
	
}

if ($action=="load_bill_table")
{
	if($data ==1)
	{
		?>
		<div id="aaa">
		<table class="rpt_table" width="650px" cellspacing="1" rules="all" id="tbl_panel">
		<thead>
			<tr>
			<th width="30px">SL</th>
			<th width="200px">Bill Description</th>
			<th width="50px">Charge For</th>
			<th width="40px">% Of Cost</th>
			<th width="50px">Cost Per (Tk)</th>
			<th width="80px">Amount (Tk)</th>
			<th width="80px">Deduction (TK)</th>
			<th width="80px">Payable (TK)</th>
			</tr>
		</thead>
					<tbody>
					
					<?
					$i = 0;
					foreach($cnf_export_bill_head_arr as $key=>$value)
					{
						$i++;
						if($key!=4)
						{
							if( $i % 2 == 0 ) $bgcolor="#E9F3FF"; else $bgcolor = "#FFFFFF";
						}
						else
						{
							$bgcolor = "#999999";
						}
						
						?>
						<tr align="center" id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $i; ?></td>
							<td align="left"><? echo $value; ?><input type="hidden" id="txtbillid_<? echo $key; ?>" value="<? echo $key; ?>" ></td>
							<td align="center"><?
							echo create_drop_down( "cbo_formula_$key",70,array(1=>"Fixed",2=>"Formula"),'',0,'--Select--',0,"fn_charge($key,this.value)",0);
							?></td>
							<td align="center"><input type="text" id="txtcost_<? echo $key; ?>" onkeyup='fn_formula(<? echo $key; ?>)' style="width: 30px;" name="" class="text_boxes_numeric" disabled ></td>
							<td align="center"><input type="text" id="txtcostper_<? echo $key; ?>" onkeyup='fn_formula(<? echo $key; ?>)'  style="width: 50px;" name="" class="text_boxes_numeric" disabled></td>
                        	<td align="center"><input type="text" id="txtamount_<? echo $key; ?>"  style="width: 70px;" name="" class="text_boxes_numeric" onkeyup='fn_total(<?echo $i ;?>)'></td>
                        	<td align="center"><input type="text" id="txtdaducation_<? echo $key; ?>"  style="width: 70px;" name="" class="text_boxes_numeric" onkeyup='fn_total(<?echo $i ;?>)'></td>
                        	<td align="center"><input type="text" id="txtpaybale_<? echo $key; ?>"  style="width: 70px;" name="" class="text_boxes_numeric" onkeyup='fn_total(<?echo $i ;?>)'></td>
						</tr>
						<?
					}
					?>
						<tr>
							<td colspan='5' align="right" style="font-weight:bold;">Total &nbsp;&nbsp;&nbsp;</td>
							<td  align="center" style="font-weight:bold;"><input type="text" id="txtamount_tot" name="txtminute_tot" style="width:70px; font-weight:bold;" class="text_boxes_numeric" readonly></td>
							<td  align="center" style="font-weight:bold;"><input type="text" id="txtdaducation_tot" name="txtdaducation_tot" style="width:70px; font-weight:bold;" class="text_boxes_numeric" readonly></td>
							<td  align="center" style="font-weight:bold;"><input type="text" id="txtpaybale_tot" name="txtpaybale_tot" style="width:70px; font-weight:bold;" class="text_boxes_numeric" readonly></td>
					   </tr>
				 </tbody>
				</table>
				</div>
				<?
			
	}
	else if($data ==2)
	{
		?>
		<table class="rpt_table" width="650px" cellspacing="1" rules="all" id="tbl_panel">
            <thead>
                <tr>
                <th width="30px">SL</th>
                <th width="200px">Bill Description</th>
                <th width="50px">Charge For</th>
                <th width="40px">% Of Cost</th>
                <th width="50px">Cost Per (Tk)</th>
                <th width="80px">Amount (Tk)</th>
				<th width="80px">Deduction (TK)</th>
				<th width="80px">Payable (TK)</th>
                </tr>
            </thead>
            <tbody>
            <?
            $i = 0;
            foreach($cnf_import_bill_head_arr as $key=>$value)
            {
                $i++;
                if( $i % 2 == 0 ) $bgcolor="#E9F3FF"; else $bgcolor = "#FFFFFF";
            	?>
                <tr align="center"  id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>">
                    <td align="center"><? echo $i; ?></td>
                    <td align="left"><? echo $value; ?><input type="hidden" id="txtbillid_<? echo $key; ?>" value="<? echo $key; ?>" ></td>
                    <td align="center"><?
					echo create_drop_down( "cbo_formula_$key",70,array(1=>"Fixed",2=>"Formula"),'',0,'--Select--',0,"fn_charge($key,this.value)",0);
					?></td>
                    <td align="center"><input type="text" id="txtcost_<? echo $key; ?>" onkeyup='fn_formula(<? echo $key; ?>)'  style="width: 30px;" name="" class="text_boxes_numeric" disabled ></td>
                    <td align="center"><input type="text" id="txtcostper_<? echo $key; ?>" onkeyup='fn_formula(<? echo $key; ?>)'  style="width: 50px;" name="" class="text_boxes_numeric" disabled></td>
                    <td align="center"><input type="text" id="txtamount_<? echo $key; ?>"  style="width: 70px;" name="" class="text_boxes_numeric" onkeyup='fn_total(<?echo $i ;?>)'>
					<!-- <input type="hidden" id="txtdaducation_<? echo $key; ?>"  style="width: 70px;" name="" class="text_boxes_numeric">
					<input type="hidden" id="txtpaybale_<? echo $key; ?>"  style="width: 70px;" name="" class="text_boxes_numeric" > -->
					</td>
					<td align="center"><input type="text" id="txtdaducation_<? echo $key; ?>"  style="width: 70px;" name="" class="text_boxes_numeric" onkeyup='fn_total(<?echo $i ;?>)'></td>
                    <td align="center"><input type="text" id="txtpaybale_<? echo $key; ?>"  style="width: 70px;" name="" class="text_boxes_numeric" onkeyup='fn_total(<?echo $i ;?>)'></td>
                </tr>
            <?
            }
            ?>
                <tr>
					<td colspan='5' align="right" style="font-weight:bold;">Total &nbsp;&nbsp;&nbsp;</td>
					<td  align="center" ><input type="text" id="txtamount_tot" name="txtminute_tot" style="width:70px; font-weight:bold;" class="text_boxes_numeric" readonly></td>
					<td  align="center" style="font-weight:bold;"><input type="text" id="txtdaducation_tot" name="txtdaducation_tot" style="width:70px; font-weight:bold;" class="text_boxes_numeric" readonly></td>
					<td  align="center" style="font-weight:bold;"><input type="text" id="txtpaybale_tot" name="txtpaybale_tot" style="width:70px; font-weight:bold;" class="text_boxes_numeric" readonly></td>
				</tr>
         </tbody>
        </table>
        <?
	}
	exit();
}

if($action=="invoice_popup_search")
{
	echo load_html_head_contents("Popup Info", "../../../", 1, 1,$unicode,'1','');
	extract($_REQUEST);
	?>

	<script>
	function js_set_value(data)
	{
		$('#hidden_info').val(data);
		// var data_string=data.split('_');
		// $('#hidden_invoice_id').val(data_string[0]);
		// $('#company_id').val(data_string[5]);
		// $('#buyer_name').val(data_string[1]);
		// $('#invoice_no').val(data_string[2]);
		// $('#invoice_date').val(data_string[3]);
		// $('#invoice_value').val(data_string[4]);
		// $('#exp_form_no').val(data_string[6]);
		// $('#exp_form_date').val(data_string[7]);
		// $('#btb_lc_no').val(data_string[8]);
		// $('#supplier_id').val(data_string[9]);
		
		//  alert(data_string[0]+'='+data_string[1]+'='+data_string[2]+'='+data_string[3]+'='+data_string[4]+'='+data_string[5]);
		parent.emailwindow.hide();
	}


	function fn_show_list()
	{
		var txt_invoice_search = document.getElementById('txt_invoice_search').value;
		var cbo_type_name = document.getElementById('cbo_type_name').value;
		if(txt_invoice_search=='')
		{
			if(cbo_type_name==1)
			{
				if(form_validation('txt_date_from*txt_date_to','Invoice Date Range*Invoice Date Range')==false && form_validation('txt_invoice_search','Enter Invoice No')==false)
				{
					return;
				}
			}
			else
			{
				if(form_validation('txt_date_from*txt_date_to','Invoice Date Range*Invoice Date Range')==false && form_validation('txt_invoice_search','Enter Invoice No')==false && form_validation('txt_btb_search','BTB LC NO')==false)
				{
					return;
				}
			}
		}

	
		show_list_view ( document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_invoice_search').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_string_search_type').value+'**'+document.getElementById('cbo_type_name').value+'**'+document.getElementById('txt_btb_search').value,'invoice_search_list_view', 'search_div', 'cnf_bill_entry_controller', 'setFilterGrid(\'list_view\',-1)')
	}

    </script>

	</head>

	<body>
	<div align="center" style="width:900px;">
		<form name="searchexportinformationfrm"  id="searchexportinformationfrm">
			<fieldset style="width:100%;">
				<table cellpadding="0" cellspacing="0" width="880" class="rpt_table" border="1" rules="all">
				<input type="hidden" name="hidden_info" id="hidden_info" value="" />
				<!-- <input type="hidden" name="hidden_invoice_id" id="hidden_invoice_id" value="" />
				<input type="hidden" name="company_id" id="company_id" value="" />
				<input type="hidden" name="buyer_name" id="buyer_name" value="" />
				<input type="hidden" name="invoice_no" id="invoice_no" value="" />
				<input type="hidden" name="invoice_date" id="invoice_date" value="" />
				<input type="hidden" name="invoice_value" id="invoice_value" value="" />
                <input type="hidden" name="exp_form_no" id="exp_form_no" value="" />
				<input type="hidden" name="exp_form_date" id="exp_form_date" value="" />
                <input type="hidden" name="btb_lc_no" id="btb_lc_no" value="" />
				<input type="hidden" name="supplier_id" id="supplier_id" value="" /> -->
				<input type="hidden" name="cbo_type_name" id="cbo_type_name" value="<?= $cbo_type_name;?>" />
					<thead>
						<tr>
                            <th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",1 ); ?></th>
                        </tr>
						<tr>
							<th>Company</th>
							<th>Buyer</th>
							<th>Invoice Date Range</th>
							<?
								if($cbo_type_name==2)
								{
									?>
										<th>BTB LC NO</th>
									<?
								}
							?>
							<th>Enter Invoice No</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							</th>
						</tr>
					</thead>
					<tr class="general">
						<td>
							
							<?
								echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--- Select Company ---", $cbo_country_id, "" );
							?>
						</td>
						<td id="buyer_td_id">
							<?
							echo create_drop_down("cbo_buyer_name", 162, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "--- Select Buyer ---", $selected, "");
							?>
						</td>
						<td>
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;" placeholder="From Date"/>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;" placeholder="To Date"/>
						</td>
						<?
							if($cbo_type_name==2)
							{
								?>
									<td><input type="text" style="width:100px" class="text_boxes"  name="txt_btb_search" id="txt_btb_search" /></td>
								<?
							}
							else
							{
								?>
									<input type="hidden" style="width:100px" class="text_boxes"  name="txt_btb_search" id="txt_btb_search" />
								<?
							}
						?>
						<td>
							<input type="text" style="width:130px" class="text_boxes"  name="txt_invoice_search" id="txt_invoice_search" />
						</td>
						<td>
							<input type="button" id="search_button" class="formbutton" value="Show" onClick="fn_show_list()" style="width:100px;" />
						</td>
					</tr> 
					<tr>
						<td align="center" colspan="10"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="width:100%; margin-top:10px" id="search_div" align="left"></div>
				
			</fieldset>
		</form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action==='invoice_search_list_view')
{
	list($company_id, $buyer_id, $invoice_num, $invoice_start_date, $invoice_end_date, $search_string, $type_name, $btb_no) = explode('**', $data);
	//echo $type_name.st;die;
    if($type_name==1)
	{
		if($buyer_id==0)
		{
			if ($_SESSION['logic_erp']['data_level_secured']==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!='') $buyer_id_cond=" and buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else
			{
				$buyer_id_cond='';
			}
		}
		else
		{
			$buyer_id_cond=" and buyer_id=$buyer_id";
		}

		$search_text=''; $company_cond ='';
		if($company_id !=0) $company_cond = "and benificiary_id=$company_id";
	
		if ($invoice_num != '')
		{
			if($search_string==1)
				$search_text="and invoice_no like '".trim($invoice_num)."'";
			else if ($search_string==2) 
				$search_text="and invoice_no like '".trim($invoice_num)."%'";
			else if ($search_string==3)
				$search_text="and invoice_no like '%".trim($invoice_num)."'";
			else if ($search_string==4 || $search_string==0)
				$search_text="and invoice_no like '%".trim($invoice_num)."%'";
		}
	
		if ($invoice_start_date != '' && $invoice_end_date != '') 
		{
			if ($db_type == 0) {
				$date_cond = "and invoice_date between '" . change_date_format($invoice_start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($invoice_end_date, 'yyyy-mm-dd') . "'";
			} else if ($db_type == 2) {
				$date_cond = "and invoice_date between '" . change_date_format($invoice_start_date, '', '', 1) . "' and '" . change_date_format($invoice_end_date, '', '', 1) . "'";
			}
		} 
		else 
		{
			$date_cond = '';
		}
	
		$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
		$comp_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
		/*$invoice_sql=("SELECT INVOICE_ID from cnf_bill_mst where cnf_type=1 and status_active=1 and is_deleted=0 ");
		$invoice_data=sql_select($invoice_sql);
		foreach($invoice_data as $row)	{
			if($row['INVOICE_ID']){$invoice_id_arr[$row['INVOICE_ID']]=$row['INVOICE_ID'];}
		}
		$invoice_id_in=where_con_using_array($invoice_id_arr,0,'id not');*/
		$sql = "SELECT id, benificiary_id, buyer_id, invoice_no, invoice_date, is_lc, lc_sc_id, invoice_value, net_invo_value, import_btb, is_posted_account, exp_form_no, exp_form_date, invoice_quantity, total_carton_qnty, carton_gross_weight, shipping_bill_n, shipping_mode
		from com_export_invoice_ship_mst 
		where status_active=1 and is_deleted=0 $company_cond $search_text $buyer_id_cond $date_cond and id not in(SELECT INVOICE_ID from cnf_bill_mst where cnf_type=1 and status_active=1 and is_deleted=0 and INVOICE_ID is not null) 
		order by invoice_date desc";
		//echo $sql;
		$data_array=sql_select($sql);		

		$lc_arr=return_library_array( "select id, export_lc_no from com_export_lc",'id','export_lc_no');
		$sc_arr=return_library_array( "select id, contract_no from com_sales_contract",'id','contract_no');
		?>
		<table width="880" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
			<thead>
				<th width="40">SL</th>
				<th width="100">Company</th>
				<th width="100">Buyer</th>
				<th width="150">Invoice No</th>
				<th width="100">Invoice Date</th>
				<th width="150">LC/SC No</th>
				<th width="100">LC/SC</th>
				<th>Net Invoice Value</th>
			</thead>
		 </table>
		 <div style="width:900px; overflow-y:scroll; max-height:250px">
			<table width="880" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="list_view">
			<?			
				$i = 1;
				foreach($data_array as $row)
				{
					if ($i%2==0)
						$bgcolor="#FFFFFF";
					else
						$bgcolor="#E9F3FF";
	
					if($row[csf('is_lc')]==1)
					{
						$lc_sc_no=$lc_arr[$row[csf('lc_sc_id')]];
						$is_lc_sc='LC';
					}
					else
					{
						$lc_sc_no=$sc_arr[$row[csf('lc_sc_id')]];
						$is_lc_sc='SC';
					}
	
					if($row[csf('import_btb')]==1) $buyer=$comp_arr[$row[csf('buyer_id')]]; else $buyer=$buyer_arr[$row[csf('buyer_id')]];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value( '<? echo $row[csf('id')]; ?>_<? echo $row[csf('buyer_id')]; ?>_<? echo $row[csf('invoice_no')]; ?>_<? echo  change_date_format($row[csf('invoice_date')]); ?>_<? echo number_format($row[csf('net_invo_value')],2,".",""); ?>_<? echo  $row[csf('benificiary_id')]; ?>_<? echo  $row[csf('exp_form_no')]; ?>_<? echo  change_date_format($row[csf('exp_form_date')]); ?>_<? echo $row[csf('invoice_quantity')]; ?>_<? echo $row[csf('total_carton_qnty')]; ?>_<? echo $row[csf('carton_gross_weight')]; ?>_<? echo $row[csf('shipping_bill_n')]; ?>_<? echo $row[csf('shipping_mode')]; ?>_<? echo $lc_sc_no; ?>');" >
						<td width="40"><? echo $i; ?></td>
						<td width="100"><p><? echo $comp_arr[$row[csf('benificiary_id')]]; ?></p></td>
						<td width="100"><p><? echo $buyer; ?></p></td>
						<td width="150"><p><? echo $row[csf('invoice_no')]; ?></p></td>
						<td width="100" align="center"><p><? echo change_date_format($row[csf('invoice_date')]); ?></td>
						<td width="150"><p><? echo $lc_sc_no; ?></p></td>
						<td width="100" align="center"><p><? echo $is_lc_sc; ?></p></td>
						<td align="right"><p><?
						echo number_format($row[csf('net_invo_value')],2);?></p></td>
					</tr>
				<?
				$i++;
				}
				?>
			</table>
		</div>
		<?

    }
	elseif($type_name==2)
	{
		$search_text=''; $company_cond ='';
		if($company_id !=0) $company_cond = "and a.importer_id ='".$company_id."'";
		if($btb_no !='') $btb_cond = "and a.lc_number ='".$btb_no."'";
	
		if ($invoice_num != '')
		{
			if($search_string==1)
			{
				$search_text="and b.invoice_no like '".trim($invoice_num)."'";
				$search_text_cnf="and q.invoice_no like '".trim($invoice_num)."'";
			}
			else if ($search_string==2)
			{ 
				$search_text="and b.invoice_no like '".trim($invoice_num)."%'";
				$search_text_cnf="and q.invoice_no like '".trim($invoice_num)."%'";
			}
			else if ($search_string==3)
			{
				$search_text="and b.invoice_no like '%".trim($invoice_num)."'";
				$search_text_cnf="and q.invoice_no like '%".trim($invoice_num)."'";
			}
			else if ($search_string==4 || $search_string==0)
			{
				$search_text="and b.invoice_no like '%".trim($invoice_num)."%'";
				$search_text_cnf="and q.invoice_no like '%".trim($invoice_num)."%'";
			}
		}
	
		if ($invoice_start_date != '' && $invoice_end_date != '') 
		{
			if ($db_type == 0) {
				$date_cond = "and b.invoice_date between '" . change_date_format($invoice_start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($invoice_end_date, 'yyyy-mm-dd') . "'";
			} else if ($db_type == 2) 
			{
				$date_cond = "and b.invoice_date between '" . change_date_format($invoice_start_date, '', '', 1) . "' and '" . change_date_format($invoice_end_date, '', '', 1) . "'";
				$date_cond_cnf = "and q.invoice_date between '" . change_date_format($invoice_start_date, '', '', 1) . "' and '" . change_date_format($invoice_end_date, '', '', 1) . "'";
			}
		} 
		else 
		{
			$date_cond = '';
		}
	
		$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
		$comp_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
		/*$invoice_sql=("SELECT INVOICE_ID from cnf_bill_mst where cnf_type=2 and status_active=1 and is_deleted=0 ");
		$invoice_data=sql_select($invoice_sql);
		foreach($invoice_data as $row){
			if($row['INVOICE_ID']){$invoice_id_arr[$row['INVOICE_ID']]=$row['INVOICE_ID'];}
		}
		$invoice_id_in=where_con_using_array($invoice_id_arr,0,'b.id not');*/
		
		//###### accourding to bereesh vai this code off and is_lc=1
		
		$sql = "SELECT a.importer_id, a.supplier_id, a.lc_number, a.lc_value, a.lc_date, a.payterm_id, b.invoice_no, b.document_value, b.invoice_date, b.document_value, b.id, a.id as lc_id, b.is_posted_account 
		FROM com_btb_lc_master_details a, com_import_invoice_mst b 
		WHERE a.id=b.btb_lc_id $company_cond $buyer_id_cond $date and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 $search_text $date_cond $btb_cond and b.id not in(SELECT p.INVOICE_ID from cnf_bill_mst p, com_import_invoice_mst q where p.INVOICE_ID=q.id and p.cnf_type=2 and p.status_active=1 and p.is_deleted=0 $search_text_cnf $date_cond_cnf) order by b.invoice_date desc";
		//echo $sql;
		$data_array=sql_select($sql);	
		?>
		<table width="980" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="100">Company</th>
                <th width="100">Buyer</th>
                <th width="100">BTB LC NO</th>
                <th width="150">Invoice No</th>
                <th width="100">Invoice Date</th>
                <th width="150">Pay Term</th>
                <th width="100">LC Date</th>
                <th>Net Invoice Value</th>
            </thead>
     	</table>
     	<div style="width:980px; overflow-y:scroll; max-height:250px">
     	<table width="960" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="list_view">
		<?			
            $i = 1;
            foreach($data_array as $row)
            {
                if ($i%2==0)
                    $bgcolor="#FFFFFF";
                else
                    $bgcolor="#E9F3FF";

				if($row[csf('import_btb')]==1) $buyer=$comp_arr[$row[csf('buyer_id')]]; else $buyer=$buyer_arr[$row[csf('buyer_id')]];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value( '<? echo $row[csf('id')]; ?>_<? echo $row[csf('buyer_id')]; ?>_<? echo $row[csf('invoice_no')]; ?>_<? echo  change_date_format($row[csf('invoice_date')]); ?>_<? echo number_format($row[csf('document_value')],2,".",""); ?>_<? echo  $row[csf('importer_id')]; ?>________<? echo  $row[csf('lc_number')]; ?>_<? echo  $row[csf('supplier_id')]; ?>');" >  
					<td width="40"><? echo $i; ?></td>
					<td width="100"><p><? echo $comp_arr[$row[csf('importer_id')]]; ?></p></td>
					<td width="100"><p><? echo $buyer; ?></p></td>
					<td width="100"><p><? echo $row[csf('lc_number')]; ?></p></td>
                    <td width="150"><p><? echo $row[csf('invoice_no')]; ?></p></td>
					<td width="100" align="center"><p><? echo change_date_format($row[csf('invoice_date')]); ?></td>
                    <td width="150"><p><? echo $pay_term[$row[csf('payterm_id')]];?></p></td>
                    <td width="100" align="center"><p><? echo change_date_format($row[csf('lc_date')],2);?></p></td>
					<td align="right"><p><?
					echo number_format($row[csf('document_value')],2);?></p></td>
				</tr>
				<?
                $i++;
            }
			?>
		</table>
    	</div>
		<?
    }
	exit();
}

if($action=="ex_rate_lib")
{
	extract($_REQUEST);
	$sql = "SELECT CONVERSION_RATE,con_date as con_date FROM currency_conversion_rate 
	WHERE company_id=$data and currency=2 and status_active = 1 and is_deleted = 0 order by con_date desc";

	$data_array=sql_select($sql);
	echo "$('#txt_ex_rate').val(".$data_array[0]['CONVERSION_RATE'].");\n";  
	exit;
}

