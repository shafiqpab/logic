<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];
$user_id=$_SESSION['logic_erp']['user_id'];

if($action=='user_popup')
{
	echo load_html_head_contents("Popup Info","../../",1, 1,'',1,'');
	?>
	<script>

	    // flowing script for multy select data------------------------start;
	    function js_set_value(id)
	    {
		    document.getElementById('selected_id').value=id;
		    parent.emailwindow.hide();
	    }
	    // above script for multy select data----------------------------end;
	</script>
	<form>
        <input type="hidden" id="selected_id" name="selected_id" />
       	<?
		$custom_designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');
		$Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');	;
		//$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
		$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and valid=1 and a.id!=$user_id  and b.is_deleted=0  and b.page_id=$menu_id";
		$arr=array (2=>$custom_designation,3=>$Department);
		echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
		?>
	</form>
	<script language="javascript" type="text/javascript">
	  setFilterGrid("tbl_style_ref");
	</script>
	<?
	exit();
}

if ($action=="load_supplier_dropdown")
{
	$data = explode('_',$data);	
	echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type=2 and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
	//and b.party_type =9
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$sequence_no='';
	$company_name=str_replace("'","",$cbo_company_name);
	$txt_wo_no=str_replace("'","",$txt_wo_no);
	$approval_type=str_replace("'","",$cbo_approval_type);
	
	if($previous_approved==1 && $approval_type==1) $previous_approved_type=1;
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if($txt_alter_user_id!="") $user_id=$txt_alter_user_id;

	$supplier_cond='';
	$cbo_supplier_id = str_replace("'","",$cbo_supplier_id);	
	if($cbo_supplier_id>0) $supplier_cond=" and a.supplier_id=$cbo_supplier_id";

	$txt_date=str_replace("'","",$txt_date);
	$date_cond = '';
	if($txt_date!="")
	{
		if($db_type==0) $txt_date=change_date_format($txt_date,"yyyy-mm-dd"); 
		else $txt_date=change_date_format($txt_date,"yyyy-mm-dd","-",1);
		
		if(str_replace("'","",$cbo_get_upto)==1) $date_cond=" and a.wo_date>'".$txt_date."'";
		else if(str_replace("'","",$cbo_get_upto)==2) $date_cond=" and a.wo_date<='".$txt_date."'";
		else if(str_replace("'","",$cbo_get_upto)==3) $date_cond=" and a.wo_date='".$txt_date."'";
		else $date_cond = '';
	}
	// echo $date_cond;die;

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0","seq");
    $is_not_last_user = return_field_value("max(sequence_no)","electronic_approval_setup","page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");

	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Sample Or Additional Yarn WO.</font>";
		die;
	}

	if($previous_approved==1 && $approval_type==1)
	{
		$sequence_no_cond=" and c.sequence_no<'$user_sequence_no'";
		$sql="SELECT a.id as ID, a.company_name as COMPANY_NAME, a.wo_number_prefix_num as WO_NUMBER_PREFIX_NUM, a.supplier_id as SUPPLIER_ID, a.wo_date as WO_DATE, a.delivery_date as DELIVERY_DATE, a.is_approved as IS_APPROVED, a.source as SOURCE, a.payterm_id as PAYTERM_ID, a.inserted_by as INSERTED_BY, a.updated_by as UPDATED_BY, a.wo_basis_id as WO_BASIS_ID, a.booking_type as BOOKING_TYPE 
		from wo_non_order_info_mst a, wo_non_order_info_dtls b, approval_history c 
		where a.id=b.mst_id and a.id=c.mst_id and a.company_name=$company_name $date_cond $supplier_cond and a.wo_number_prefix_num LIKE '%$txt_wo_no%' and a.entry_form=284 and a.is_approved in(1,3) and a.status_active=1 and a.is_deleted=0 and b.item_category_id=1 and b.status_active=1 and b.is_deleted=0 and c.entry_form=53 and c.current_approval_status=1 $sequence_no_cond 
		group by  a.id, a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.is_approved, a.source, a.payterm_id, a.inserted_by, a.updated_by, a.wo_basis_id, a.booking_type 
		order by a.id";
		//echo $sql;
	}
	else if($approval_type==0)
	{
		$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted=0 and company_id=$company_name");
		if($user_sequence_no==$min_sequence_no) // first approval authority
		{
			$sql="SELECT a.id as ID, a.company_name as COMPANY_NAME, a.wo_number_prefix_num as WO_NUMBER_PREFIX_NUM, a.supplier_id as SUPPLIER_ID, a.wo_date as WO_DATE, a.delivery_date as DELIVERY_DATE, a.is_approved as IS_APPROVED, a.source as SOURCE, a.payterm_id as PAYTERM_ID, a.inserted_by as INSERTED_BY, a.updated_by as UPDATED_BY, a.wo_basis_id as WO_BASIS_ID, a.booking_type as BOOKING_TYPE
			from wo_non_order_info_mst a
			where a.company_name=$company_name $date_cond $supplier_cond and a.wo_number_prefix_num LIKE '%$txt_wo_no%' and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1 and a.is_approved=0 and a.entry_form=284";
            // echo $sql;//die("with sumon");
		}
		else if($sequence_no=="") //last approval authority having bypass=no previlages // Next User bypass Yes
		{

			if($db_type==0)
			{
				$sequence_no_by=return_field_value("group_concat(sequence_no)","electronic_approval_setup","page_id=$menu_id
				and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0");
				$booking_id=return_field_value("group_concat(distinct(mst_id)) as batch_id","wo_non_order_info_mst a, approval_history b","a.id=b.mst_id
				and a.company_name=$company_name and b.sequence_no in ($sequence_no_by) and b.entry_form=53 and b.current_approval_status=1","batch_id");
				$booking_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as batch_id","wo_non_order_info_mst a, approval_history b",
				"a.id=b.mst_id and a.company_name=$company_name  and b.sequence_no=$user_sequence_no and b.entry_form=53 and
				b.current_approval_status=1","batch_id");
			}
			else
			{
				$sequence_no_by=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no",
				"electronic_approval_setup","page_id=$menu_id and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0","sequence_no");

				$booking_id=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as batch_id","wo_non_order_info_mst a,
				approval_history b","a.id=b.mst_id and a.company_name=$company_name  and b.sequence_no in ($sequence_no_by) and b.entry_form=53
				and b.current_approval_status=1","batch_id");
				$booking_id=implode(",",array_unique(explode(",",$booking_id)));

				$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as batch_id","wo_non_order_info_mst a,
				approval_history b","a.id=b.mst_id and a.company_name=$company_name and b.sequence_no=$user_sequence_no and b.entry_form=53 and
				b.current_approval_status=1","batch_id");
				$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
			}

			$result=array_diff(explode(',',$booking_id),explode(',',$booking_id_app_byuser));
			$booking_id=implode(",",$result);

			$booking_id_cond="";
			if($booking_id_app_byuser!="") $booking_id_cond=" and a.id not in($booking_id_app_byuser)";

			$sql="SELECT a.id as ID, a.company_name as COMPANY_NAME, a.wo_number_prefix_num as WO_NUMBER_PREFIX_NUM, a.supplier_id as SUPPLIER_ID, a.wo_date as WO_DATE, a.delivery_date as DELIVERY_DATE, a.is_approved as IS_APPROVED, a.source as SOURCE, a.payterm_id as PAYTERM_ID, a.inserted_by as INSERTED_BY, a.updated_by as UPDATED_BY, a.wo_basis_id as WO_BASIS_ID, a.booking_type as BOOKING_TYPE
			from wo_non_order_info_mst a
			where  a.company_name=$company_name $date_cond $supplier_cond and a.wo_number_prefix_num LIKE '%$txt_wo_no%' and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1 and a.is_approved in (0,3) and a.entry_form=284 $booking_id_cond"; 
		}
		else // bypass No
		{

			$user_sequence_no=$user_sequence_no-1;

			if($db_type==0)
			{
				$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","page_id=$menu_id and
				sequence_no between $sequence_no and $user_sequence_no and bypass=1 and is_deleted=0");
			}
			else
			{
				$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no",
				"electronic_approval_setup","page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and
				is_deleted=0","sequence_no");
			}


			if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$user_sequence_no'";
			else $sequence_no_cond=" and (c.sequence_no='$sequence_no' or c.sequence_no in ($sequence_no_by_pass))";
            //echo $sequence_no;die;
			$sql="SELECT a.id as ID, a.company_name as COMPANY_NAME, a.wo_number_prefix_num as WO_NUMBER_PREFIX_NUM, a.supplier_id as SUPPLIER_ID, a.wo_date as WO_DATE, a.delivery_date as DELIVERY_DATE, a.is_approved as IS_APPROVED, a.source as SOURCE, a.payterm_id as PAYTERM_ID, a.inserted_by as INSERTED_BY, a.updated_by as UPDATED_BY, a.wo_basis_id as WO_BASIS_ID, a.booking_type as BOOKING_TYPE
			from wo_non_order_info_mst a, approval_history c
			where a.id=c.mst_id and a.company_name=$company_name $date_cond $supplier_cond and a.wo_number_prefix_num LIKE '%$txt_wo_no%' and a.is_deleted=0 and a.status_active=1 and	a.ready_to_approved=1 and a.is_approved in(1,3) and a.entry_form=284 and c.entry_form=53 and c.current_approval_status=1 $sequence_no_cond";
            //echo $sql;//die("with kakku");
		}
	}
	else
	{
		$sequence_no_cond=" and b.approved_by='$user_id'";
		$sql="SELECT a.id as ID, a.company_name as COMPANY_NAME, a.wo_number_prefix_num as WO_NUMBER_PREFIX_NUM, a.supplier_id as SUPPLIER_ID, a.wo_date as WO_DATE, a.delivery_date as DELIVERY_DATE, a.is_approved as IS_APPROVED, a.source as SOURCE, a.payterm_id as PAYTERM_ID, a.inserted_by as INSERTED_BY, a.updated_by as UPDATED_BY, a.wo_basis_id as WO_BASIS_ID, a.booking_type as BOOKING_TYPE
		from wo_non_order_info_mst a, approval_history b
		where a.id=b.mst_id and a.company_name=$company_name $date_cond $supplier_cond and a.wo_number_prefix_num LIKE '%$txt_wo_no%' and a.is_approved  in(1,3) and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and a.entry_form=284 and b.entry_form=53 and b.current_approval_status=1 $sequence_no_cond 
		order by a.id";
		// echo $sql;die("Tipu");
	}
		
	$submittedByArr=return_library_array( "select id, user_full_name from user_passwd ",'id','user_full_name');
	$nonOrder_booking_type = array(1 => "Aditional", 2=>"Compensative");
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:920px; margin-top:10px">
        <legend>Sample Or Additional Yarn WO Approval</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="920" class="rpt_table" >
                <thead>
                	<th width="50">&nbsp;</th>
                    <th width="40">SL</th>
                    <th width="70">Work Order No</th>
                    <th width="140">Supplier</th>
                    <th width="70">Work Order Date</th>
                    <th width="70">Delivery Date</th>
                    <th width="100">WO Basis</th>
                    <th width="100">Booking Type</th>
                    <th width="80">Source</th>
                    <th width="80">Pay Term</th>
                    <th>Submitted By</th>
                </thead>
            </table>
            <div style="width:920px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
                            $i=1;
							$supplier=return_library_array("select id, supplier_name from lib_supplier ",'id','supplier_name');
                            $nameArray=sql_select( $sql );
                            
                            foreach ($nameArray as $row)
                            {
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$value='';
								if($approval_type==0) $value=$row['ID'];
								else
								{
									$app_id=return_field_value("id","approval_history","mst_id ='".$row['ID']."' and entry_form='53' and un_approved_by=0 order by id desc");
									$value=$row['ID']."**".$app_id;
								}								

								if($row['UPDATED_BY']=="" || $row['UPDATED_BY']==0) $row['UPDATED_BY']=$row['INSERTED_BY'];							
                            	?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                	<td width="50" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="req_id_<? echo $i;?>" name="req_id[]" type="hidden" value="<? echo $value; ?>" />
                                        <input id="requisition_id_<? echo $i;?>" name="requisition_id[]" type="hidden" value="<? echo $row['ID']; ?>" /><!--this is uesd for delete row-->
                                        <input id="<? echo strtoupper($row['WO_NUMBER_PREFIX_NUM']); ?>" name="no_wo[]" type="hidden" value="<? echo $i;?>" />
                                    </td>
									<td width="40" align="center"><? echo $i; ?></td>
									<td width="70" align="center" style="color:#000; text-decoration: underline; cursor: pointer;" onclick="print_button_link('<? echo $company_name; ?>','<? echo $row['ID']; ?>');"><? echo $row['WO_NUMBER_PREFIX_NUM']; ?></td>
                                    <td width="140" style="word-break:break-all"><? echo $supplier[$row['SUPPLIER_ID']]; ?></td>
									<td width="70" align="center"><? if($row['WO_DATE']!="0000-00-00") echo change_date_format($row['WO_DATE']); ?></td>
									<td width="70" align="center"><? if($row['DELIVERY_DATE']!="0000-00-00") echo change_date_format($row['DELIVERY_DATE']); ?></td>
                                    
                                    <td width="100" style="word-break:break-all"><? echo $sample_wo_basis[$row['WO_BASIS_ID']]; ?></td>
                                    <? 
                                    if ($row['BOOKING_TYPE']==1)
                                    {	
                                    	?>
                                    	<td width="100" style="word-break:break-all; background-color: yellow;"><? echo $nonOrder_booking_type[$row['BOOKING_TYPE']]; ?></td>
                                    	<?
                                    }
                                    else
                                    {	
                                    	?>
                                    	<td width="100" style="word-break:break-all"><? echo $nonOrder_booking_type[$row['BOOKING_TYPE']]; ?></td>
                                    	<?
                                    }
                                    ?>
                                    <td width="80" style="word-break:break-all"><? echo $source[$row['SOURCE']]; ?></td>
                                    <td width="80" style="word-break:break-all"><? echo $pay_term[$row['PAYTERM_ID']]; ?></td>
                                    <td style="word-break:break-all"><? echo $submittedByArr[$row['UPDATED_BY']]; ?></td>
								</tr>
								<?
								$i++;
							}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table">
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

	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if($_REQUEST['txt_alter_user_id']!="") 	$user_id_approval=$_REQUEST['txt_alter_user_id'];
	else $user_id_approval=$user_id;

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id_approval and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");


	if($approval_type==0) //Approve button
	{
		$response=$req_nos;

        $is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0","sequence_no");
        //echo $is_not_last_user;die;
        if($is_not_last_user =="") $partial_approval=1;
        else $partial_approval=3;

		$rID=sql_multirow_update("wo_non_order_info_mst","is_approved","$partial_approval","id",$req_nos,0);

		if($rID) $flag=1; else $flag=0;


		$reqs_ids=explode(",",$req_nos);
		$field_array="id, entry_form, mst_id, approved_no, approved_by, approved_date, sequence_no, current_approval_status, inserted_by, insert_date";
		$i=0;
		$id=return_next_id( "id","approval_history", 1 ) ;

		$approved_no_array=array();

		foreach($reqs_ids as $val)
		{
			$approved_no=return_field_value("max(approved_no)","approval_history","mst_id='$val'");
			$approved_no=$approved_no+1;

			if($i!=0) $data_array.=",";
			$data_array.="(".$id.",53,".$val.",".$approved_no.",".$user_id_approval.",'".$pc_date_time."',".$user_sequence_no.",1,".$user_id.",'".$pc_date_time."')";
			$approved_no_array[$val]=$approved_no;
			$id=$id+1;
			$i++;
		}

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

		$rID3=execute_query($sql_insert,0);

		if($flag==1)
		{
			if($rID3) $flag=1; else $flag=0;
		}

		$sql_insert_dtls="insert into wo_non_order_info_dtls_history(id, approved_no, mst_id, requisition_dtls_id, po_breakdown_id, requisition_no, item_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, req_quantity, supplier_order_quantity, uom,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
		select
		'', $approved_string_dtls, mst_id, requisition_dtls_id, po_breakdown_id, requisition_no, item_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, req_quantity, supplier_order_quantity, uom,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_non_order_info_dtls where mst_id in ($req_nos)";

		$rID4=execute_query($sql_insert_dtls,1);

		if($flag==1)
		{
			if($rID4) $flag=1; else $flag=0;
		}

        $query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=53 and mst_id in ($req_nos)";

		$rIDapp=execute_query($query,1);

		if($flag==1)
		{
			if($rIDapp) $flag=1; else $flag=0;
		}

        $rID2=sql_insert("approval_history",$field_array,$data_array,0);

		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}

		if($flag==1) $msg='19'; else $msg='21';
	}
	else  // Un-Approve button
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

		$rID=sql_multirow_update("wo_non_order_info_mst","is_approved*ready_to_approved","0*0","id",$reqs_ids,0);

		if($rID) $flag=1; else $flag=0;

		$data=$user_id_approval."*'".$pc_date_time."'*0*".$user_id."*'".$pc_date_time."'";
		$rID2=sql_multirow_update("approval_history","un_approved_by*un_approved_date*current_approval_status*updated_by*update_date",$data,"id",$app_id,0);

		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}

		$response=$reqs_ids;
		if($flag==1) $msg='20'; else $msg='22';
	}

	//echo "10**".$rID.'**'.$rID2.'**'.$rID3.'**'.$rID4.'**'.$app_ids;die;

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
	else if($db_type==2 || $db_type==1 )
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
