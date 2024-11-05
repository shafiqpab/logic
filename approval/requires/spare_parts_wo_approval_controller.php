<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_name=str_replace("'","",$cbo_company_name);
	if(str_replace("'","",$cbo_item_category_id)==0) $item_category_id="(8,9,10)"; else $item_category_id="($cbo_item_category_id)";
	$approval_type=str_replace("'","",$cbo_approval_type);
	
	$sql="select id, company_name, wo_number_prefix_num, item_category, supplier_id, wo_date, delivery_date from wo_non_order_info_mst where company_name=$company_name and item_category in $item_category_id and is_approved=$approval_type and status_active=1 and is_deleted=0 order by id";
	//echo $sql;die;
	
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:800px; margin-top:10px">
        <legend>Purchase Requisition Approval</legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table" >
                <thead>
                	<th width="52"></th>
                    <th width="60">SL</th>
                    <th width="100">Work Order No</th>
                    <th width="120">Item Catagory</th>
                    <th width="140">Supplier</th>
                    <th width="100">Work Order Date</th>
                    <th width="116">Delivery Date</th>                </thead>
            </table>
            <div style="width:720px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <? 
                            $i=1;
							$supplier=return_library_array("select id, supplier_name from lib_supplier ",'id','supplier_name');
                            $nameArray=sql_select( $sql );
                            foreach ($nameArray as $row)
                            {
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								
								$value='';
								if($approval_type==0)
								{
									$value=$row[csf('id')];
								}
								else
								{
								if($db_type==0)
									{
									$app_id=return_field_value("id","approval_history","mst_id ='".$row[csf('id')]."' and entry_form='4' order by id desc limit 0,1");
									}
								if($db_type==2)
									{
									$app_id=return_field_value("id","approval_history","mst_id ='".$row[csf('id')]."' and entry_form='4' and ROWNUM=1 order by id desc");
									}
									
									
									$value=$row[csf('id')]."**".$app_id;
								}
								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                                	<td width="50" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="req_id_<? echo $i;?>" name="req_id[]" type="hidden" value="<? echo $value; ?>" />
                                        <input id="requisition_id_<? echo $i;?>" name="requisition_id[]" type="hidden" value="<? echo $row[csf('id')]; ?>" /><!--this is uesd for delete row-->
                                    </td>   
									<td width="60" align="center"><? echo $i; ?></td>
									<td width="100">
                                    	<p><a href='##' style='color:#000' onClick="print_report(<? echo $row[csf('company_name')]; ?>+'*'+<? echo $row[csf('id')]; ?>,'dyes_chemical_work_print', '../commercial/work_order/requires/dyes_and_chemical_work_order_controller')">
									<? echo $row[csf('wo_number_prefix_num')]; ?></a></p>
                                    </td>
                                    <td width="120"><p><? echo $item_category[$row[csf('item_category')]]; ?></p></td>
                                    <td width="140"><p><? echo $supplier[$row[csf('supplier_id')]]; ?></p></td>
									<td width="100" align="center"><? if($row[csf('wo_date')]!="0000-00-00") echo change_date_format($row[csf('wo_date')]); ?></td>
									<td width="100" align="center"><? if($row[csf('delivery_date')]!="0000-00-00") echo change_date_format($row[csf('delivery_date')]); ?></td>
								</tr>
								<?
								$i++;
							}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" ><input type="checkbox" id="all_check" onClick="check_all('all_check')" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/></td>
				</tfoot>
			</table>
        </fieldset>
    </form>         
<?
	exit();	
}







if ($action=="approve")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	$msg=''; $flag=''; $response='';
	
	if($approval_type==0)
	{
		$response=$req_nos;
		/*$rID=sql_multirow_update("wo_non_order_info_mst","is_approved",1,"id",$req_nos,0);
		if($rID) $flag=1; else $flag=0;*/
		
		$reqs_ids=explode(",",$req_nos);
		$field_array="id, entry_form, mst_id, approved_no, approved_by, approved_date"; 
		$i=0;
		$id=return_next_id( "id","approval_history", 1 ) ;
		
		$approved_no_array=array();
		
		foreach($reqs_ids as $val)
		{
			$approved_no=return_field_value("max(approved_no)","approval_history","mst_id='$val'");
			$approved_no=$approved_no+1;
		
			if($i!=0) $data_array.=",";
			
			$data_array.="(".$id.",4,".$val.",".$approved_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
			
			$approved_no_array[$val]=$approved_no;
				
			$id=$id+1;
			$i++;
		}
		
		//echo "insert into approval_history (".$field_array.") Values ".$data_array."";die;
	/*	$rID2=sql_insert("approval_history",$field_array,$data_array,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		}*/ 
		
		$approved_string="";
		
		foreach($approved_no_array as $key=>$value)
		{
			$approved_string.=" WHEN $key THEN $value";
		}
		
		$approved_string_mst="CASE id ".$approved_string." END";
		$approved_string_dtls="CASE mst_id ".$approved_string." END";
		
		$sql_insert="insert into wo_non_order_info_mst_history(id, mst_id, approved_no, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, buyer_po, requisition_no, delivery_place, wo_date, supplier_id, attention, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode, terms_and_condition, is_approved, approved_by, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date) 
			select	
			'', id, $approved_string_mst, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, buyer_po, requisition_no, delivery_place, wo_date, supplier_id, attention, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode, terms_and_condition, is_approved, approved_by, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date from  wo_non_order_info_mst where id in ($req_nos)";
				
		/*$rID3=execute_query($sql_insert,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} */
		
		$sql_insert_dtls="insert into wo_non_order_info_dtls_history(id, approved_no, mst_id, requisition_dtls_id, po_breakdown_id, requisition_no, item_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, req_quantity, supplier_order_quantity, uom,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted) 
			select	
			'', $approved_string_dtls, mst_id, requisition_dtls_id, po_breakdown_id, requisition_no, item_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, req_quantity, supplier_order_quantity, uom,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_non_order_info_dtls where mst_id in ($req_nos)";
		
		
		$rID=sql_multirow_update("wo_non_order_info_mst","is_approved",1,"id",$req_nos,0);
		if($rID) $flag=1; else $flag=0;
		$rID2=sql_insert("approval_history",$field_array,$data_array,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		}
		$rID3=execute_query($sql_insert,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		
		$rID4=execute_query($sql_insert_dtls,1);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		} 
		
		if($flag==1) $msg='19'; else $msg='21';
	}
	else
	{
		
		$req_nos = explode(',',$req_nos); 
		
		$reqs_ids=''; $app_ids='';
		
		foreach($req_nos as $value)
		{
			$data = explode('**',$value);
			$reqs_id=$data[0];
			$app_id=$data[1];
			
			if($reqs_ids=='') $reqs_ids=$reqs_id; else $reqs_ids.=",".$reqs_id;
			if($app_ids=='') $app_ids=$app_id; else $app_ids.=",".$app_id;
		}
		$rID=sql_multirow_update("wo_non_order_info_mst","is_approved",0,"id",$reqs_ids,0);
		if($rID) $flag=1; else $flag=0;
		
		$data=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID2=sql_multirow_update("approval_history","un_approved_by*un_approved_date",$data,"id",$app_ids,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		
		$response=$reqs_ids;
		
		if($flag==1) $msg='20'; else $msg='22';
	}
	
	if($db_type==0)
	{ 
		if($flag==1)
		{
			mysql_query("COMMIT");  
			echo $msg."**".$response;
		}
		else
		{
			mysql_query("ROLLBACK"); 
			echo $msg."**".$response;
		}
	}
	
	if($db_type==2 || $db_type==1 )
	{
		if($flag==1)
		{
			oci_commit($con);
			echo $msg."**".$response;
		}
		else
		{
			oci_rollback($con);
			echo $msg."**".$response;
		}
	}
	disconnect($con);
	die;
	
}

?>