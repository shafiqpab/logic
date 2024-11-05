<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];


if ($action=="load_drop_down_location")
{    	 
	echo create_drop_down( "cbo_location", 130, "select id,location_name from lib_location where company_id='$data' $company_location_credential_cond and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_drop_down('requires/monthly_purchase_requisition_controller', this.value+'_'+$data, 'load_drop_down_store','store_td');" );
	exit();
}

if ($action=="load_drop_down_store")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_store_name", 130, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[1]' and a.location_id = $data[0] and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1,"--Select store--",0,"");
	exit();
}

if ($action=="purchase_requisition_popup")
{
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	?>
	<script>
		  function js_set_value(id)
		  {
			  document.getElementById('selected_req').value=id;
			  parent.emailwindow.hide();
		  }
	</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	<form name="purchaserequisition_2"  id="purchaserequisition_2" autocomplete="off">
	<table width="900" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" width="900">
                    <thead>
                        <th width="180">Company Name</th>
                        <th width="50" style="display:none">Item Category</th>
                        <th width="100">Store Name</th>
                        <th width="100">Requisition Year</th>
						<th width="100">Requisition No</th>
                        <th width="200">Date Range</th>
                        <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                    </thead>
        			<tr class="general">
                    	<td align="center"> 
							<input type="hidden" id="selected_req">
							<?
								echo create_drop_down( "cbo_company_name", 152, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "-- Select Company --", $cbo_company_name, "",1);
                            ?>
                    	</td>
                   		<td style="display:none">
							<?
								echo create_drop_down( "cbo_item_category_id", 50,$item_category,"", 1, "-- Select --", $selected, "","","","","","1,2,3,12,13,14,24,25");
                            ?>
                        </td>
                        <td align="center">
							<?
								 echo create_drop_down( "cbo_store_name", 160,"select a.id,a.store_name from lib_store_location a, lib_store_location_category b  where a.id=b.store_location_id and a.is_deleted=0 and a.company_id='$cbo_company_name' and a.status_active=1 and b.category_type not in(1,2,3,12,13,14,24,25) $store_location_credential_cond group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select --", $selected, "" );
                            ?>
                        </td>
						<td  align="center">
							<?
								echo create_drop_down( "txt_year", 80, $year,"", 1, "All",date("Y"));
							?>
						</td>
                        <td align="center">

                            <input name="txt_requisition_no" id="txt_requisition_no" class="text_boxes" style="width:100px">
					 	</td>
                    	<td align="center">
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 	</td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_item_category_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_requisition_no').value+'_'+document.getElementById('cbo_store_name').value+'_'+document.getElementById('txt_year').value, 'purchase_requisition_list_view', 'search_div', 'monthly_purchase_requisition_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                       </td>
        			</tr>
             	</table>
          	</td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle">
				<? echo load_month_buttons(1);  ?>
            </td>
        </tr>        
    </table>
    <div style="width:100%; margin-top:10px" id="search_div" align="left"></div>
    </form>
    </div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="purchase_requisition_list_view")
{
	$data=explode('_',$data);

	if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $item_category_id=" and item_category_id='$data[1]'"; else $item_category_id="";
	$requisition_no=trim(str_replace("'","",$data[4]));
	if(str_replace("'","",$requisition_no)!="") $get_cond .= "and requ_no  like '%$requisition_no'  "; else  $get_cond="";
	$store_cond = ($data[5]) ? " and store_name = '" . $data[5] ."'" :  "";
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!=""){ $order_rcv_date = " and requisition_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'";} else { $order_rcv_date =""; }
	}
	if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!=""){ $order_rcv_date = " and requisition_date between '".change_date_format($data[2], 'mm-dd-yyyy','/',1)."' and '".change_date_format($data[3], 'mm-dd-yyyy','/',1)."'"; } else { $order_rcv_date =""; }
	}

	$requisition_year= $data[6];
	if ($requisition_year != 0){
		if($db_type==2) { $requisition_year_cond=" and extract(year from requisition_date)=$requisition_year";}
		if($db_type==0) {$requisition_year_cond=" and SUBSTRING_INDEX(requisition_date, '-', 1)=$requisition_year";}
	 }

	$sql= "SELECT id as ID, requ_no as REQU_NO, requ_prefix_num as REQU_PREFIX_NUM, requisition_date as REQUISITION_DATE, company_id as COMPANY_ID, item_category_id as ITEM_CATEGORY_ID, location_id as LOCATION_ID, department_id as DEPARTMENT_ID, section_id as SECTION_ID, manual_req as MANUAL_REQ, store_name as STORE_NAME, inserted_by as INSERTED_BY, ready_to_approve as READY_TO_APPROVE, is_approved as IS_APPROVED from inv_purchase_requisition_mst where status_active=1 and is_deleted=0 and entry_form=69 $company  $item_category_id  $order_rcv_date $get_cond $credientian_cond $store_cond $requisition_year_cond order by id desc";
	//echo $sql;
	$sql_res=sql_select($sql);

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location_arr=return_library_array("select id,location_name from lib_location",'id','location_name');
	$department_arr=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section_arr=return_library_array("select id,section_name from lib_section",'id','section_name');
	$store_arr=return_library_array("select id,store_name from lib_store_location where company_id='$data[0]' and status_active=1",'id','store_name');
	$user_library = return_library_array("select id, user_name from user_passwd", "id", "user_name");
	$is_approved_arr=array(0=>'No', 1=>'Yes', 2=>'No', 3=>'Partial Approved');

	?>
	<div>
		<table width="1000" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" align="left">
	        <thead>
	            <th width="30">SL</th>
	            <th width="60">Requisition No</th>
	            <th width="60">Requisition Date</th>
	            <th width="100">Company</th>
	            <th width="100">Location</th>
	            <th width="100">Department</th>
	            <th width="100">Section</th>
	            <th width="120">Store Name</th>
	            <th width="80">Manual Req</th>
	            <th width="80">Insert By</th>
	            <th width="60">Ready To Approve</th>
	            <th>Approval Status</th>
	        </thead>
	     </table>
	     <div style="width:1020px; overflow-y:scroll; max-height:270px">
	     	<table width="1000" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="list_view">
				<?
				$i = 1;
	            foreach($sql_res as $row)
	            {
	                if($i%2==0) $bgcolor="#E9F3FF"; 
	                else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value('<? echo $row['ID']."__".$row['REQU_NO']; ?>');">
	                	<td width="30"><? echo $i; ?></td>
			            <td width="60"><? echo $row["REQU_PREFIX_NUM"]; ?></td>
			            <td width="60"><? echo change_date_format($row["REQUISITION_DATE"]); ?></td>
			            <td width="100"><? echo $company_arr[$row["COMPANY_ID"]]; ?></td>
			            <td width="100"><? echo $location_arr[$row["LOCATION_ID"]]; ?></td>
			            <td width="100"><? echo $department_arr[$row["DEPARTMENT_ID"]]; ?></td>
			            <td width="100"><? echo $section_arr[$row["SECTION_ID"]]; ?></td>
			            <td width="120"><? echo $store_arr[$row["STORE_NAME"]]; ?></td>
			            <td width="80"><? echo $row["MANUAL_REQ"]; ?></td>
			            <td width="80"><? echo $user_library[$row["INSERTED_BY"]]; ?></td>
			            <td width="60"><? if ($row["READY_TO_APPROVE"]==1) echo 'Yes'; else echo 'No'; ?></td>
			            <td><? echo $is_approved_arr[$row["IS_APPROVED"]]; ?></td>
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

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_location=str_replace("'","",$cbo_location);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$cbo_req_year=str_replace("'","",$cbo_req_year);
	$txt_req_no=trim(str_replace("'","",$txt_req_no));
	$txt_date_from=trim(str_replace("'","",$txt_date_from));
	$txt_date_to=trim(str_replace("'","",$txt_date_to));
	$txt_req_id=str_replace("'","",$txt_req_id);

	// $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$department_arr=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section_arr=return_library_array("select id,section_name from lib_section",'id','section_name');
	$store_arr=return_library_array("select id,store_name from lib_store_location where company_id='$cbo_company_name' and status_active=1",'id','store_name');
	$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
	$item_arr= return_library_array("select id, item_name from lib_item_group", "id", "item_name");

	if($db_type==0)
	{
		$from_date=change_date_format($txt_date_from,'yyyy-mm-dd');
		$to_date=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$from_date=change_date_format($txt_date_from,'','',-1);
		$to_date=change_date_format($txt_date_to,'','',-1);
	}

	$search_cond='';
	if(!empty($txt_req_id))
	{
		$search_cond.=" and a.id=$txt_req_id ";
	}
	else
	{
		$search_cond.=" and a.requ_prefix_num='$txt_req_no' ";
	}
	if($cbo_location){$search_cond.=" and a.location_id=$cbo_location ";}
	if($cbo_store_name){$search_cond.=" and a.store_name=$cbo_store_name ";}
	if($db_type==0){ $search_cond.=" and year(a.insert_date)=".$cbo_req_year.""; }
	else{ $search_cond.=" and to_char(a.insert_date,'YYYY')=".$cbo_req_year.""; }
	
	$mst_sql= "SELECT a.id as ID, a.requ_no as REQU_NO, a.requisition_date as REQUISITION_DATE,a.location_id as LOCATION_ID, a.department_id as DEPARTMENT_ID, a.section_id as SECTION_ID, a.store_name as STORE_NAME, a.cbo_currency as CBO_CURRENCY,a.remarks as REMARKS,a.inserted_by as INSERTED_BY from inv_purchase_requisition_mst a where a.status_active=1 and a.is_deleted=0 and a.entry_form=69 $search_cond order by a.id desc";
	// echo $mst_sql;
	$mst_result=sql_select($mst_sql);

	$mst_id=$mst_result[0]['ID'];
	$requ_no=$mst_result[0]['REQU_NO'];
	$requisition_date=$mst_result[0]['REQUISITION_DATE'];
	$department_id=$mst_result[0]['DEPARTMENT_ID'];
	$section_id=$mst_result[0]['SECTION_ID'];
	$location_name=$mst_result[0]['LOCATION_ID'];
	$store_name=$mst_result[0]['STORE_NAME'];
	$currency_id=$mst_result[0]['CBO_CURRENCY'];
	$mst_remarks=$mst_result[0]['REMARKS'];
	$inserted_by=$mst_result[0]['INSERTED_BY'];

	$dtls_sql="SELECT  a.id as PROD_ID, a.item_category_id as ITEM_CATEGORY_ID, a.item_group_id as ITEM_GROUP_ID, a.item_description as ITEM_DESCRIPTION, a.item_size as ITEM_SIZE, a.order_uom as UOM, b.id as DTLS_ID, a.item_number as ITEM_NUMBER, a.model as MODEL, b.brand_name as BRAND_NAME, b.quantity as QUANTITY, b.rate as RATE, b.amount as AMOUNT, b.remarks as REMARKS
	from product_details_master a, inv_purchase_requisition_dtls b
	where a.id=b.product_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.mst_id='$mst_id'  
	union all
	SELECT  0 as PROD_ID, b.ITEM_CATEGORY as ITEM_CATEGORY_ID, 0 as ITEM_GROUP_ID, b.SERVICE_DETAILS as ITEM_DESCRIPTION, null as ITEM_SIZE, b.CONS_UOM as UOM, b.id as DTLS_ID, null as ITEM_NUMBER, b.MODEL as MODEL, b.brand_name as BRAND_NAME, b.quantity as QUANTITY, b.rate as RATE, b.amount as AMOUNT, b.remarks as REMARKS
	from inv_purchase_requisition_dtls b
	where b.product_id=0 and b.status_active=1 and b.is_deleted=0 and b.mst_id='$mst_id'
	order by ITEM_CATEGORY_ID, DTLS_ID";
	//echo $dtls_sql;
	$dtls_result=sql_select($dtls_sql);

	$prod_id_arr=array();$category_count=array();
	foreach($dtls_result as $row)
	{
		$all_data_arr[$row['ITEM_CATEGORY_ID']][$row['ITEM_GROUP_ID']][]=$row;
		$item_count[$row['ITEM_CATEGORY_ID']][$row['ITEM_GROUP_ID']]++;
		$prod_id_arr[$row['PROD_ID']]=$row['PROD_ID'];
	}
	
	//echo "<pre>";print_r($all_data_arr);die;
	
		
	$prod_id_in=where_con_using_array($prod_id_arr,0,'a.prod_id');

	
	$inv_sql="SELECT a.prod_id as PROD_ID,
	sum(case when a.transaction_date<'$from_date' and a.transaction_type in (1,4,5) then a.cons_quantity else 0 end) as OPENING_TOTAL_RECEIVE,
	sum(case when a.transaction_date<'$from_date' and a.transaction_type in (1,4,5) then a.cons_amount else 0 end) as OPENING_TOTAL_RECEIVE_AMT,
	sum(case when a.transaction_date<'$from_date' and a.transaction_type in (2,3,6) then a.cons_quantity else 0 end) as OPENING_TOTAL_ISSUE,
	sum(case when a.transaction_date<'$from_date' and a.transaction_type in (2,3,6) then a.cons_amount else 0 end) as OPENING_TOTAL_ISSUE_AMT,
	sum(case when a.transaction_type in (1) and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as TOTAL_RCV_QNTY,
	sum(case when a.transaction_type in (1) and a.transaction_date between '$from_date' and '$to_date' then a.cons_amount else 0 end) as TOTAL_RCV_AMT_VALUE,
	sum(case when a.transaction_type in (2) and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as TOTAL_ISS_QNTY,
	sum(case when a.transaction_type in (2) and a.transaction_date between '$from_date' and '$to_date' then a.cons_amount else 0 end) as TOTAL_ISS_AMT_VALUE,
	sum(case when a.transaction_type in (3) and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as TOTAL_REC_RETURN_QNTY,
	sum(case when a.transaction_type in (3) and a.transaction_date between '$from_date' and '$to_date' then a.cons_amount else 0 end) as TOTAL_REC_RETURN_VALUE,
	sum(case when a.transaction_type in (4) and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as TOTAL_ISS_RETURN_QNTY,
	sum(case when a.transaction_type in (4) and a.transaction_date between '$from_date' and '$to_date' then a.cons_amount else 0 end) as TOTAL_ISS_RETURN_VALUE,
	sum(case when a.transaction_type in (5) and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as TOTAL_TRANS_IN_QTY,
	sum(case when a.transaction_type in (5) and a.transaction_date between '$from_date' and '$to_date' then a.cons_amount else 0 end) as TOTAL_TRANS_IN_VALUE,
	sum(case when a.transaction_type in (6) and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as TOTAL_TRANS_OUT_QTY,
	sum(case when a.transaction_type in (6) and a.transaction_date between '$from_date' and '$to_date' then a.cons_amount else 0 end) as TOTAL_TRANS_OUT_VALUE
	from inv_transaction a
	where a.transaction_type in (1,2,3,4,5,6) and store_id=$store_name and a.status_active=1 and a.is_deleted=0 $prod_id_in group by a.prod_id";
	//echo $inv_sql;
	$inv_result=sql_select($inv_sql);

	$prod_info=array();
	foreach($inv_result as $row)
	{
		$prod_info[$row['PROD_ID']]['opening_total_receive']=$row['OPENING_TOTAL_RECEIVE'];
		$prod_info[$row['PROD_ID']]['opening_total_receive_amt']=$row['OPENING_TOTAL_RECEIVE_AMT'];
		$prod_info[$row['PROD_ID']]['opening_total_issue']=$row['OPENING_TOTAL_ISSUE'];
		$prod_info[$row['PROD_ID']]['opening_total_issue_amt']=$row['OPENING_TOTAL_ISSUE_AMT'];
		$prod_info[$row['PROD_ID']]['total_rcv_qnty']=$row['TOTAL_RCV_QNTY'];
		$prod_info[$row['PROD_ID']]['total_rcv_amt_value']=$row['TOTAL_RCV_AMT_VALUE'];
		$prod_info[$row['PROD_ID']]['total_rec_return_qnty']=$row['TOTAL_REC_RETURN_QNTY'];
		$prod_info[$row['PROD_ID']]['total_rec_return_value']=$row['TOTAL_REC_RETURN_VALUE'];
		$prod_info[$row['PROD_ID']]['total_iss_return_qnty']=$row['TOTAL_ISS_RETURN_QNTY'];
		$prod_info[$row['PROD_ID']]['total_iss_return_value']=$row['TOTAL_ISS_RETURN_VALUE'];
		$prod_info[$row['PROD_ID']]['total_iss_qnty']=$row['TOTAL_ISS_QNTY'];
		$prod_info[$row['PROD_ID']]['total_iss_amt_value']=$row['TOTAL_ISS_AMT_VALUE'];
		$prod_info[$row['PROD_ID']]['total_trans_in_qty']=$row['TOTAL_TRANS_IN_QTY'];
		$prod_info[$row['PROD_ID']]['total_trans_in_value']=$row['TOTAL_TRANS_IN_VALUE'];
		$prod_info[$row['PROD_ID']]['total_trans_out_qty']=$row['TOTAL_TRANS_OUT_QTY'];
		$prod_info[$row['PROD_ID']]['total_trans_out_value']=$row['TOTAL_TRANS_OUT_VALUE'];
	}

	$inv_rec_sql="SELECT a.prod_id as PROD_ID,
	sum(case when a.transaction_type=1 and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as TOTAL_LOAN_REC_QNTY, 
	sum(case when a.transaction_type=1 and a.transaction_date between '$from_date' and '$to_date' then a.cons_amount else 0 end) as TOTAL_LOAN_REC_AMT_VALUE
	from inv_transaction a, inv_receive_master b
	where a.mst_id=b.id and a.transaction_type=1 and b.receive_purpose=5 and a.store_id=$store_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $prod_id_in group by a.prod_id";

	$inv_rec_result=sql_select($inv_rec_sql);
	foreach($inv_rec_result as $row)
	{
		$prod_info[$row['PROD_ID']]['total_loan_rec_qnty']+=$row['TOTAL_LOAN_REC_QNTY'];
		$prod_info[$row['PROD_ID']]['total_loan_rec_amt_value']+=$row['TOTAL_LOAN_REC_AMT_VALUE'];	
	}
	unset($inv_rec_result);


	$inv_issue_sql="SELECT a.prod_id as PROD_ID,
	sum(case when a.transaction_type=2 and a.transaction_date between '$from_date' and '$to_date' then a.cons_quantity else 0 end) as TOTAL_LOAN_ISS_QNTY,
	sum(case when a.transaction_type=2 and a.transaction_date between '$from_date' and '$to_date' then a.cons_amount else 0 end) as TOTAL_LOAN_ISS_AMT_VALUE
	from inv_transaction a, inv_issue_master b
	where a.mst_id=b.id and a.transaction_type=2 and b.issue_purpose=5 and a.store_id=$store_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $prod_id_in group by a.prod_id";

	$inv_issue_result=sql_select($inv_issue_sql);
	foreach($inv_issue_result as $row)
	{
		$prod_info[$row['PROD_ID']]['total_loan_iss_qnty']+=$row['TOTAL_LOAN_ISS_QNTY'];
		$prod_info[$row['PROD_ID']]['total_loan_iss_amt_value']+=$row['TOTAL_LOAN_ISS_AMT_VALUE'];	
	}
	unset($inv_issue_result);

	//echo '<pre>';print_r($prod_info);
	/*$nameArray=sql_select( "select plot_no,road_no,block_no,city from lib_company where id=$cbo_company_name");
	foreach ($nameArray as $result)
	{
		$com_add='Plot No: '.$result[csf('plot_no')].', '.$result[csf('road_no')].', '.$result[csf('block_no')].', '.$result[csf('city')];
	}*/
	$com_info=fnc_company_location_address($cbo_company_name, $location_name, 1);
	$table_width=2480;
	ob_start();
	?>
	<style>
		.wrd_brk{word-break: break-all;}
		.left{text-align: left;}
		.center{text-align: center;}
		.right{text-align: right;}
		.fontSize table tr td, .fontSize table tr td strong{font-size: 16px;}
		table tbody tr td {padding: 8px 0px;}
		@media print{
			.pageBreak{ page-break-after: always;}
		}
	</style>

	<body>
		<div style="width:100%">
			<table width="<?=$table_width;?>" cellspacing="0" cellpadding="0" class="set_table_width">
				<thead>
					<tr>
						<td colspan="30" style="font-size:24px" width="100%" align="center"><strong><?=$report_title;?></strong></td>
					</tr>
					<tr>
						<td colspan="30" style="font-size:20px" width="100%" align="center"><strong><?=$com_info[0]; ?></strong></td>
					</tr>
					<tr>
						<td colspan="30" style="font-size:16px" width="100%" align="center"><strong><?=$com_info[1];?></strong></td>
					</tr>					
				</thead>
			</table>
			<br>
			<table border="1" class="rpt_table set_table_width" rules="all" width="<?=$table_width; ?>" cellpadding="0" cellspacing="0">
				<body>
					<tr>
						<td colspan="6" style="font-size:18px" ><strong>Req No: <?=$requ_no;?></strong></td>
						<td colspan="4" style="font-size:18px" ><strong>Req. Date: <?=change_date_format($requisition_date);?></strong></td>
						<td colspan="4" style="font-size:18px" ><strong>Department: <?echo $department_arr[$department_id];?></strong></td>
						<td colspan="6" style="font-size:18px"><strong>Section: <?echo $section_arr[$section_id];?></strong></td>
						<td colspan="5" style="font-size:18px" ><strong>Store Name: <?=$store_arr[$store_name];?></strong></td>
						<td colspan="5" style="font-size:18px" ><strong>Currency: <?echo $currency[$currency_id];?></strong></td>
					</tr>
				</body>				
			</table>
			<br>
			<table border="1" class="rpt_table set_table_width" rules="all" width="<?=$table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
					<tr>
						<th style="font-size:16px" rowspan="2" width="30">SL</th>
						<th style="font-size:16px" rowspan="2" width="100">Item Group</th>
						<th style="font-size:16px" rowspan="2" width="180">Name of item</th>
						<th style="font-size:16px" rowspan="2" width="80">Size/MSR</th>
						<th style="font-size:16px" rowspan="2" width="80">Model/Article</th>
						<th style="font-size:16px" rowspan="2" width="80">Brand</th>
						<th style="font-size:16px" rowspan="2" width="50">UOM</th>               
						<th style="font-size:16px" colspan="3" >Opening stock</th>
						<th style="font-size:16px" colspan="3" >Received</th>              	
						<th style="font-size:16px" rowspan="2" width="80" class="column_hide">Loan Received</th>
						<th style="font-size:16px" rowspan="2" width="80" class="column_hide">Issue Return</th>
						<th style="font-size:16px" rowspan="2" width="80" class="column_hide">Transfer In</th>
						<th style="font-size:16px" width="80" >OP+Rcv</th>
						<th style="font-size:16px" colspan="3" >Consumption</th>
						<th style="font-size:16px" rowspan="2" width="80" class="column_hide">As Loan</th>
						<th style="font-size:16px" rowspan="2" width="80" class="column_hide">Receive Return</th>
						<th style="font-size:16px" rowspan="2" width="80" class="column_hide">Transfer Out</th>
						<th style="font-size:16px" colspan="3" >Closing stock</th>
						<th style="font-size:16px" colspan="3" >Monthly Requisition</th>
						<th style="font-size:16px" rowspan="2">Remark</th>
					</tr>
					<tr>
						<th style="font-size:16px" width="80">Qty</th>
						<th style="font-size:16px" width="80">Avg. Rate</th>
						<th style="font-size:16px" width="80">Amount</th>
						<th style="font-size:16px" width="80">Qty</th>
						<th style="font-size:16px" width="80">Avg. Rate</th>
						<th style="font-size:16px" width="80">Amount</th>
						<th style="font-size:16px" width="80">Total Qty</th>
						<th style="font-size:16px" width="80">Qty</th>
						<th style="font-size:16px" width="80">Avg. Rate</th>
						<th style="font-size:16px" width="80">Amount</th>
						<th style="font-size:16px" width="80">Qty</th>
						<th style="font-size:16px" width="80">Avg. Rate</th>
						<th style="font-size:16px" width="80">Amount</th>
						<th style="font-size:16px" width="80">Qty</th>
						<th style="font-size:16px" width="80">Rate</th>
						<th style="font-size:16px" width="80">Amount</th>
					</tr>
				</thead>	
				<tbody>
					<?
						$item_chk=array();
						$i=1;$k=1;
						$total_in_amount=$total_out_amount=0;
						$total_open_amount=$total_rcv_amount=0;
						$total_issue_amount=$total_closing_amount=$total_req_amount=0;
						foreach($all_data_arr as $category_id=>$category_val)
						{
							?>
								<tr bgcolor="#FCF7B6"><td style="font-size:15px" colspan="30"><strong><?echo "Category: ".$item_category[$category_id];?></strong></td></tr>
							<?
							foreach($category_val as $item_group_key=>$item_group_val)
							{
								foreach($item_group_val as $row)
								{
									if($i%20==0){$pageBreak="pageBreak";}else{$pageBreak="";}
									if($i%2==0){ $bgcolor="#E9F3FF"; } else{ $bgcolor="#FFFFFF"; }
									$item_rowspan=$item_count[$row['ITEM_CATEGORY_ID']][$row['ITEM_GROUP_ID']];
									$open_rcv_qnty=$prod_info[$row['PROD_ID']]['opening_total_receive'];
									$open_issue_qnty=$prod_info[$row['PROD_ID']]['opening_total_issue'];
									$open_rcv_amount=$prod_info[$row['PROD_ID']]['opening_total_receive_amt'];
									$open_issue_amount=$prod_info[$row['PROD_ID']]['opening_total_issue_amt'];

									$open_blance_qnty=$open_rcv_qnty-$open_issue_qnty;

									$open_blance_amount=$open_rcv_amount-$open_issue_amount;
									$open_blance_rate=$open_blance_amount/$open_blance_qnty;
									// $closing_qnty=$open_blance_qnty+$prod_info[$row['PROD_ID']]['total_receive_qnty']-$prod_info[$row['PROD_ID']]['total_issue_qnty'];
									$total_rcv_qnty=$prod_info[$row['PROD_ID']]['total_rcv_qnty']-$prod_info[$row['PROD_ID']]['total_loan_rec_qnty'];
									$total_rcv_amt=$prod_info[$row['PROD_ID']]['total_rcv_amt_value']-$prod_info[$row['PROD_ID']]['total_loan_rec_amt_value'];

									$total_iss_qnty=$prod_info[$row['PROD_ID']]['total_iss_qnty']-$prod_info[$row['PROD_ID']]['total_loan_iss_qnty'];
									$total_iss_amt=$prod_info[$row['PROD_ID']]['total_iss_amt_value']-$prod_info[$row['PROD_ID']]['total_loan_iss_amt_value'];

									$closing_qnty=$open_blance_qnty+$prod_info[$row['PROD_ID']]['total_rcv_qnty']+$prod_info[$row['PROD_ID']]['total_iss_return_qnty']+$prod_info[$row['PROD_ID']]['total_trans_in_qty']-$prod_info[$row['PROD_ID']]['total_iss_qnty']-$prod_info[$row['PROD_ID']]['total_rec_return_qnty']-$prod_info[$row['PROD_ID']]['total_trans_out_qty'];
									
									// $closing_amount=$open_blance_amount+$prod_info[$row['PROD_ID']]['total_receive_amt_value']-$prod_info[$row['PROD_ID']]['total_issue_amt_value'];
									$closing_amount=$open_blance_amount+$prod_info[$row['PROD_ID']]['total_rcv_amt_value']+$prod_info[$row['PROD_ID']]['total_iss_return_value']+$prod_info[$row['PROD_ID']]['total_trans_in_value']-$prod_info[$row['PROD_ID']]['total_iss_amt_value']-$prod_info[$row['PROD_ID']]['total_rec_return_value']-$prod_info[$row['PROD_ID']]['total_trans_out_value'];
									$closing_rate=$closing_amount/ number_format($closing_qnty,5);
									$model_artical='';
									if($row["ITEM_NUMBER"]!='' && $row["MODEL"]!='' )
									{
										$model_artical=$row["MODEL"]."/".$row["ITEM_NUMBER"];
									}
									else
									{
										$model_artical=$row["MODEL"]."".$row["ITEM_NUMBER"];
									}							
									?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor;?>')" id="tr_<?= $i;?>" style="text-decoration:none; cursor:pointer" class="<?echo $pageBreak;?>">
											<td style="font-size:15px" class="wrd_brk center"><?=$i;?></td>
											<?
												if(!in_array($row['ITEM_CATEGORY_ID'].'__'.$row['ITEM_GROUP_ID'],$item_chk))
												{
													$item_chk[]=$row['ITEM_CATEGORY_ID'].'__'.$row['ITEM_GROUP_ID'];
													?>
														<td style="font-size:15px" rowspan="<?=$item_rowspan;?>" class="wrd_brk"><?echo $item_arr[$row['ITEM_GROUP_ID']];?></td>
													<?
												}
											?>
											<td style="font-size:15px" class="wrd_brk" title="<? echo $row['PROD_ID']; ?>"><?echo $row['ITEM_DESCRIPTION'];?></td>
											<td style="font-size:15px" class="wrd_brk"><?echo $row['ITEM_SIZE'];?></td>
											<td style="font-size:15px" class="wrd_brk"><?echo $model_artical;?></td>
											<td style="font-size:15px" class="wrd_brk"><?echo $row['BRAND_NAME'];?></td>
											<td style="font-size:15px" class="wrd_brk center"><?echo $unit_of_measurement[$row['UOM']];?></td>
											<td style="font-size:15px" class="wrd_brk right"><?echo number_format($open_blance_qnty,2);?></td>
											<td style="font-size:15px" class="wrd_brk right"><?echo fn_number_format($open_blance_rate,2);?></td>
											<td style="font-size:15px" class="wrd_brk right"><?echo number_format($open_blance_amount,2);?></td>
											<td style="font-size:15px" class="wrd_brk right"><?echo number_format($total_rcv_qnty,2); ?></td>
											<td style="font-size:15px" class="wrd_brk right"><?echo fn_number_format(($total_rcv_amt/$total_rcv_qnty),2);?></td>
											<td style="font-size:15px" class="wrd_brk right"><?echo number_format($total_rcv_amt,2);?></td>

											<td style="font-size:15px" align="right" class="column_hide"><? echo number_format($prod_info[$row['PROD_ID']]['total_loan_rec_qnty'],2); ?></td>
											<td style="font-size:15px" align="right" class="column_hide"><? echo number_format($prod_info[$row['PROD_ID']]['total_iss_return_qnty'],2); ?></td>
											<td style="font-size:15px" align="right" class="column_hide"><? echo number_format($prod_info[$row['PROD_ID']]['total_trans_in_qty'],2); ?></td>

											<td style="font-size:15px" class="wrd_brk right"><?echo number_format($open_blance_qnty+$total_rcv_qnty,2);?></td>
											<td style="font-size:15px" class="wrd_brk right" title="Issue - As Loan"><?echo number_format($total_iss_qnty,2);?></td>
											<td style="font-size:15px" class="wrd_brk right"><?echo fn_number_format(($total_iss_amt/$total_iss_qnty),2);?></td>
											<td style="font-size:15px" class="wrd_brk right"><?echo number_format($total_iss_amt,2);?></td>
											<td style="font-size:15px" align="right" class="column_hide"><? echo number_format($prod_info[$row['PROD_ID']]['total_loan_iss_qnty'],2); ?></td>
											<td style="font-size:15px" align="right" class="column_hide"><? echo number_format($prod_info[$row['PROD_ID']]['total_rec_return_qnty'],2); ?></td>
											<td style="font-size:15px" align="right" class="column_hide"><? echo number_format($prod_info[$row['PROD_ID']]['total_trans_out_qty'],2); ?></td>
											<td style="font-size:15px" class="wrd_brk right" title="<?=$row['PROD_ID'];?>"><? echo number_format($closing_qnty,2);?></td>											
											<td style="font-size:15px" class="wrd_brk right"><?echo fn_number_format($closing_rate,2);?></td>
											<td style="font-size:15px" class="wrd_brk right"><?echo number_format($closing_amount,2);?></td>											
											<td style="font-size:15px" class="wrd_brk right"><?echo number_format($row['QUANTITY'],2);?></td>
											<td style="font-size:15px" class="wrd_brk right"><?echo number_format($row['RATE'],2);?></td>
											<td style="font-size:15px" class="wrd_brk right"><?echo number_format($row['AMOUNT'],2);?></td>
											<td style="font-size:15px" class="wrd_brk">&nbsp;<?echo $row['REMARKS'];?></td>
										</tr>
										
									<?
									$i++;
									$total_open_amount+=$open_blance_amount;
									$total_rcv_amount+=$total_rcv_amt;
									$total_issue_amount+=$total_iss_amt;
									$total_closing_amount+=$closing_amount;
									$total_req_amount+=$row['AMOUNT'];
								}
							}
						}
					?>
					<tr bgcolor="#DBDBDB">
						<td style="font-size:16px"  class="left" colspan="7" class="left"><strong>Total Amount</strong></td>
						<td ></td>
						<td ></td>
						<td style="font-size:16px" class="right"><strong><?echo number_format($total_open_amount,2);?></strong></td>
						<td ></td>
						<td ></td>
						<td style="font-size:16px" class="right"><strong><?echo number_format($total_rcv_amount,2);?></strong></td>
						<td class="column_hide"></td>
						<td class="column_hide"></td>
						<td class="column_hide"></td>
						<td ></td>
						<td ></td>
						<td ></td>
						<td style="font-size:16px" class="right"><strong><?echo number_format($total_issue_amount,2);?></strong></td>
						<td class="column_hide"></td>
						<td class="column_hide"></td>
						<td class="column_hide"></td>
						<td ></td>
						<td ></td>
						<td style="font-size:16px" class="right"><strong><?echo number_format($total_closing_amount,2);?></strong></td>
						<td ></td>
						<td ></td>
						<td style="font-size:16px" class="right"><strong><?echo number_format($total_req_amount,2);?></strong></td>
						<td ></td>
					</tr>
					<tr bgcolor="#DBDBDB">
						<td style="font-size:16px" colspan="30" class="left"><strong>Monthly Requisition Total Amount In Word: </strong><?echo number_to_words(number_format($total_req_amount,2),$currency[$currency_id]).' Only';?></td>
					</tr>
					<tr bgcolor="#DBDBDB">
						<td style="font-size:16px" colspan="30" class="left"><strong>Remarks: <?echo $mst_remarks;?></strong></td>
					</tr>
				</tbody>	
			</table>
			<br>
			<div>
				<?	echo signature_table(25, $cbo_company_name, "2000px", $cbo_template_id, 50, $user_lib_name[$inserted_by], "", 8);?>
			</div>
		</div>
	</body>	
	<?
		
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
	echo "$total_data****$filename";
	exit();
}

disconnect($con);
?>
