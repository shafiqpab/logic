<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];

if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "");
	}
	else if($data[1]==2)
	{
		echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",0, "" );
	}	
	exit();	 
} 

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$sequence_no='';
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_within_group=str_replace("'","",$cbo_within_group);
	$cbo_party_name=str_replace("'","",$cbo_party_name);
	$txt_sys_id=str_replace("'","",$txt_sys_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);

	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if ($txt_alter_user_id !="") $user_id=$txt_alter_user_id;

	$approval_type=str_replace("'","",$cbo_approval_type);
	if ($previous_approved==1 && $approval_type==1)
	{
		$previous_approved_type=1;
	}

	if ($cbo_company_name != 0) $company_cond=" and a.company_id=$cbo_company_name";
	if ($cbo_within_group != 0) $within_group_cond=" and a.within_group=$cbo_within_group";
	if ($cbo_party_name != 0) $party_id_cond=" and a.party_id=$cbo_party_name";
	if ($txt_sys_id !='') $sys_no_cond=" and a.job_no_prefix_num=$txt_sys_id";

	if ($approval_type == 1) $approved_cond=" and a.approved in (1)";
	else $approved_cond=" and a.approved in (0,2)";

	if ($txt_date_from != '' && $txt_date_to != '')
	{
		if($db_type==0)
		{
			$txt_date_from = date("Y-m-d", strtotime($txt_date_from));
			$txt_date_to = date("Y-m-d", strtotime($txt_date_to));
			$rcv_date_cond = " and a.receive_date between '".$txt_date_from."' and '".$txt_date_to."'";
		}
		else
		{
			$txt_date_from = date("d-M-Y", strtotime($txt_date_from));
			$txt_date_to = date("d-M-Y", strtotime($txt_date_to));
			$rcv_date_cond = " and a.receive_date between '".$txt_date_from."' and '".$txt_date_to."'";
		}	
	}

	$company_library=return_library_array("select id, company_name from lib_company","id","company_name");
	$buyer_library=return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");
	$team_leader_library=return_library_array("select id, team_leader_name from lib_marketing_team","id","team_leader_name");
	$team_member_library=return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
	// $user_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
	$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=17 and report_id =173 and is_deleted=0 and status_active=1");
    $format_ids=explode(",",$print_report_format);

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");

	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted = 0");
	// echo "select sequence_no from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0"."**".$user_sequence_no."**".$min_sequence_no."**".$menu_id;

	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority.</font>";die;
	}

	if($previous_approved==1 && $approval_type==1)	//approval process with prevous approve start
	{
		$sequence_no_cond=" and c.sequence_no<'$user_sequence_no'";

		$sql="SELECT a.id as ID, a.job_no_prefix_num as JOB_NO_PREFIX_NUM, a.within_group as WITHIN_GROUP, a.party_id as PARTY_ID, a.order_no as ORDER_NO, a.currency_id as CURRENCY_ID, a.receive_date as RECEIVE_DATE, a.rec_start_date as REC_START_DATE, a.team_leader as TEAM_LEADER, a.team_member as TEAM_MEMBER, sum(b.amount) as AMOUNT, c.id as APPROVAL_ID, c.approved_date as APPROVED_DATE 
		from subcon_ord_mst a, subcon_ord_dtls b, approval_history c
		where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.order_no=b.order_no and a.id=c.mst_id and a.approved in (1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=51 and c.current_approval_status=1 $company_cond $within_group_cond $party_id_cond $sys_no_cond $rcv_date_cond $sequence_no_cond
		group by a.id, a.job_no_prefix_num, a.within_group, a.party_id, a.order_no, a.currency_id, a.receive_date, a.rec_start_date, a.team_leader, a.team_member, c.id, c.approved_date 
	   	order by a.id";
		//echo "$sql";
	}
	else if($approval_type==0)	// unapproval process start
	{

		if($user_sequence_no==$min_sequence_no)  // First user
		{
		 	$sql="SELECT a.id as ID, a.job_no_prefix_num as JOB_NO_PREFIX_NUM, a.within_group as WITHIN_GROUP, a.party_id as PARTY_ID, a.order_no as ORDER_NO, a.currency_id as CURRENCY_ID, a.receive_date as RECEIVE_DATE, a.rec_start_date as REC_START_DATE, a.team_leader as TEAM_LEADER, a.team_member as TEAM_MEMBER, sum(b.amount) as AMOUNT
		 	from subcon_ord_mst a, subcon_ord_dtls b
		 	where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.order_no=b.order_no and a.approved=$approval_type and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond $within_group_cond $party_id_cond $sys_no_cond $rcv_date_cond
			group by a.id, a.job_no_prefix_num, a.within_group, a.party_id, a.order_no, a.currency_id, a.receive_date, a.rec_start_date, a.team_leader, a.team_member
			order by a.id";
			// echo $sql;
		}
		else // Next user
		{
			$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted = 0");

			if($sequence_no=="")  // bypass if previous user Yes
			{
				if($db_type==0)
				{
					$seqSql="select group_concat(sequence_no) as sequence_no_by from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
					$seqData=sql_select($seqSql);
					$sequence_no_by=$seqData[0][csf('sequence_no_by')];
					
					$req_comp_id=return_field_value("group_concat(distinct(b.mst_id)) as req_comp_id","inv_purchase_requisition_mst a, approval_history b","a.id=b.mst_id and a.company_id=$cbo_company_name and b.sequence_no in ($sequence_no_by) and a.ready_to_approve=1 and a.approved in (3,1) and a.is_deleted=0 and b.entry_form=51 and b.current_approval_status=1 $rcv_date_cond","req_comp_id");
					$req_comp_id=implode(",",array_unique(explode(",",$req_comp_id)));

					$req_comp_id_app_byuser=return_field_value("group_concat(distinct(b.mst_id)) as req_comp_id","inv_purchase_requisition_mst a, approval_history b","a.id=b.mst_id and a.company_id=$cbo_company_name and a.ready_to_approve=1 and a.approved in (3,1) and a.is_deleted=0 and b.sequence_no=$user_sequence_no and a.ready_to_approve=1 and b.entry_form=51 and b.current_approval_status=1","req_comp_id");
					$req_comp_id_app_byuser=implode(",",array_unique(explode(",",$req_comp_id_app_byuser)));
				}
				else
				{
					$seqSql="select LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no_by from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
					$seqData=sql_select($seqSql);
					$sequence_no_by=$seqData[0][csf('sequence_no_by')];
					
					$req_comp_id=return_field_value("LISTAGG(b.mst_id, ',') WITHIN GROUP (ORDER BY b.mst_id) as req_comp_id","inv_purchase_requisition_mst a, approval_history b","a.id=b.mst_id and a.company_id=$cbo_company_name and b.sequence_no in ($sequence_no_by) and a.ready_to_approve=1 and a.approved in (3,1) and a.is_deleted=0 and b.entry_form=51 and b.current_approval_status=1 $rcv_date_cond","req_comp_id");
					$req_comp_id=implode(",",array_unique(explode(",",$req_comp_id)));
					
					$req_comp_id_app_byuser=return_field_value("LISTAGG(b.mst_id, ',') WITHIN GROUP (ORDER BY b.mst_id) as req_comp_id","inv_purchase_requisition_mst a, approval_history b","a.id=b.mst_id and a.company_id=$cbo_company_name and a.ready_to_approve=1 and a.approved in (3,1) and a.is_deleted=0 and b.sequence_no=$user_sequence_no and a.ready_to_approve=1 and b.entry_form=51 and b.current_approval_status=1","req_comp_id");
					$req_comp_id_app_byuser=implode(",",array_unique(explode(",",$req_comp_id_app_byuser)));
				}

				$result=array_diff(explode(',',$req_comp_id),explode(',',$req_comp_id_app_byuser));
				$req_comp_id=implode(",",$result);

				if($req_comp_id!="")
				{	
					$sql="SELECT a.id as ID, a.job_no_prefix_num as JOB_NO_PREFIX_NUM, a.within_group as WITHIN_GROUP, a.party_id as PARTY_ID, a.order_no as ORDER_NO, a.currency_id as CURRENCY_ID, a.receive_date as RECEIVE_DATE, a.rec_start_date as REC_START_DATE, a.team_leader as TEAM_LEADER, a.team_member as TEAM_MEMBER, sum(b.amount) as AMOUNT
					from subcon_ord_mst a, subcon_ord_dtls b
					where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.order_no=b.order_no and a.approved=$approval_type and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond $within_group_cond $party_id_cond $sys_no_cond $rcv_date_cond
					group by a.id, a.job_no_prefix_num, a.within_group, a.party_id, a.order_no, a.currency_id, a.receive_date, a.rec_start_date, a.team_leader, a.team_member
					UNION ALL
					SELECT a.id as ID, a.job_no_prefix_num as JOB_NO_PREFIX_NUM, a.within_group as WITHIN_GROUP, a.party_id as PARTY_ID, a.order_no as ORDER_NO, a.currency_id as CURRENCY_ID, a.receive_date as RECEIVE_DATE, a.rec_start_date as REC_START_DATE, a.team_leader as TEAM_LEADER, a.team_member as TEAM_MEMBER, sum(b.amount) as AMOUNT
					from subcon_ord_mst a
					where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.order_no=b.order_no and a.approved in(1,3) and a.id in ($req_comp_id) and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond $within_group_cond $party_id_cond $sys_no_cond $rcv_date_cond
					group by a.id, a.job_no_prefix_num, a.within_group, a.party_id, a.order_no, a.currency_id, a.receive_date, a.rec_start_date, a.team_leader, a.team_member
				    order by a.id";	
					//echo $sql;
				}
				else
				{ 
					$sql="SELECT a.id as ID, a.job_no_prefix_num as JOB_NO_PREFIX_NUM, a.within_group as WITHIN_GROUP, a.party_id as PARTY_ID, a.order_no as ORDER_NO, a.currency_id as CURRENCY_ID, a.receive_date as RECEIVE_DATE, a.rec_start_date as REC_START_DATE, a.team_leader as TEAM_LEADER, a.team_member as TEAM_MEMBER, sum(b.amount) as AMOUNT
					from subcon_ord_mst a, subcon_ord_dtls b
					where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.order_no=b.order_no and a.approved=$approval_type and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond $within_group_cond $party_id_cond $sys_no_cond $rcv_date_cond
					group by a.id, a.job_no_prefix_num, a.within_group, a.party_id, a.order_no, a.currency_id, a.receive_date, a.rec_start_date, a.team_leader, a.team_member
					order by a.id";
					//echo $sql;
				}
				//echo $sql;
			}			
			else // if previous user bypass No 
			{
				$user_sequence_no=$user_sequence_no-1;
				if($sequence_no==$user_sequence_no) 
				{
					$sequence_no_by_pass=$sequence_no;
					$sequence_no_cond=" and c.sequence_no in ($sequence_no_by_pass)";
				}
				else
				{
					if($db_type==0) 
					{
						$sequence_no_by_pass=return_field_value("group_concat(sequence_no) as sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
					}
					else if($db_type==2) 
					{
						$sequence_no_by_pass=return_field_value("listagg(sequence_no,',') within group (order by sequence_no) as sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
					}
					
					if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$sequence_no'";
					else $sequence_no_cond=" and c.sequence_no in ($sequence_no_by_pass)";
				}
				$sql="SELECT a.id as ID, a.job_no_prefix_num as JOB_NO_PREFIX_NUM, a.within_group as WITHIN_GROUP, a.party_id as PARTY_ID, a.order_no as ORDER_NO, a.currency_id as CURRENCY_ID, a.receive_date as RECEIVE_DATE, a.rec_start_date as REC_START_DATE, a.team_leader as TEAM_LEADER, a.team_member as TEAM_MEMBER, sum(b.amount) as AMOUNT, c.id as APPROVAL_ID, c.approved_date as APPROVED_DATE 
				from subcon_ord_mst a, subcon_ord_dtls b, approval_history c
				where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.order_no=b.order_no and a.id=c.mst_id and a.approved in (1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=51 and c.current_approval_status=1 $company_cond $within_group_cond $party_id_cond $sys_no_cond $rcv_date_cond $sequence_no_cond
				group by a.id, a.job_no_prefix_num, a.within_group, a.party_id, a.order_no, a.currency_id, a.receive_date, a.rec_start_date, a.team_leader, a.team_member, c.id, c.approved_date 
				order by a.id";
				//echo $sql;
			}
		}	

	}
	else // approval process start
	{
		$sequence_no_cond=" and c.sequence_no='$user_sequence_no'";

		$sql="SELECT a.id as ID, a.job_no_prefix_num as JOB_NO_PREFIX_NUM, a.within_group as WITHIN_GROUP, a.party_id as PARTY_ID, a.order_no as ORDER_NO, a.currency_id as CURRENCY_ID, a.receive_date as RECEIVE_DATE, a.rec_start_date as REC_START_DATE, a.team_leader as TEAM_LEADER, a.team_member as TEAM_MEMBER, sum(b.amount) as AMOUNT, c.id as APPROVAL_ID, c.approved_date as APPROVED_DATE 
		from subcon_ord_mst a, subcon_ord_dtls b, approval_history c
		where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.order_no=b.order_no and a.id=c.mst_id and a.approved=$approval_type and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=51 and c.current_approval_status=1 $company_cond $within_group_cond $party_id_cond $sys_no_cond $rcv_date_cond $sequence_no_cond
		group by a.id, a.job_no_prefix_num, a.within_group, a.party_id, a.order_no, a.currency_id, a.receive_date, a.rec_start_date, a.team_leader, a.team_member, c.id, c.approved_date 
		order by a.id";
	}
	// echo $sql;
	?>
	<style type="text/css">
		.wrd_brk{.word-break: break-all; word-wrap: break-word;}
	</style>
    <form name="csApproval_2" id="csApproval_2">
        <fieldset style="width:1050px; margin-top:10px">
        <legend>Trims Order Rcv Approval</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1030" class="rpt_table" >
                <thead>
                	<th width="40"></th>
                    <th width="70">Within Group</th>
                    <th width="120">Party Name</th>
                    <th width="120">System ID</th>
                    <th width="100">WO No</th>
                    <th width="60">Currency</th>                    
                    <th width="70">Value</th>
                    <th width="70">Order Rcv.Date</th>
                    <th width="70">Target Delv. Date</th>
                    <th width="120">Team Leader</th>
                    <th >Team Member</th>
                </thead>
            </table>
            <div style="width:1030px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1012" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <? 
                            $i=1;
                            $nameArray=sql_select( $sql );
                            foreach ($nameArray as $row)
                            {
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								
								if($format_ids[0]==86) // Print 
								{
									$type=86;
								}
								elseif($format_ids[0]==84) // Print2 
								{
									$type=84;
								}
								elseif($format_ids[0]==377) // Print In-House
								{	
									$type=377;
								}
								else
								{
									$type='';
								}
                                $variable="<a href='#'  title='".$format_ids[$j]."'  onclick=\"print_report('".$row[csf('company_id')]."','".$row['ID']."','Trims Order Receive','".$row['WITHIN_GROUP']."','".$type."')\"> ".$row['JOB_NO_PREFIX_NUM']." <a/>";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                                	<td width="40" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="rcv_id_<? echo $i;?>" name="rcv_id[]" type="hidden" value="<? echo $row['ID']; ?>" /> 
										<input id="approval_id_<?=$i;?>" name="approval_id[]" type="hidden" value="<? echo $row['APPROVAL_ID']; ?>" />
                                        
                                    </td>
									<td width="70" align="center"><? echo $yes_no[$row['WITHIN_GROUP']]; ?></td>
                                    <td width="120" class="wrd_brk">
										<? 
											if($row['WITHIN_GROUP']==1)
											{
												echo $company_library[$row['PARTY_ID']];
											}
											else
											{
												echo $buyer_library[$row['PARTY_ID']];
											}
										?>
									</td>
                                    <td width="120"><? echo $variable; ?></td>
                                    <td width="100" class="wrd_brk"><? echo $row['ORDER_NO']; ?></td>
                                    <td width="60" align="center"><? echo $currency[$row['CURRENCY_ID']]; ?></p></td>
									<td width="70" align="right"><? echo number_format($row['AMOUNT'],4);?>&nbsp;</td>
									<td width="70" align="center"><? echo change_date_format($row['RECEIVE_DATE']); ?>&nbsp;</td>
									<td width="70" align="center"><? echo change_date_format($row['REC_START_DATE']); ?></td>
									<td width="120" class="wrd_brk"><? echo $team_leader_library[$row['TEAM_LEADER']]; ?></td>
									<td class="wrd_brk"><? echo $team_member_library[$row['TEAM_MEMBER']]; ?></td>
								</tr>
								<?
								$i++;
							}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1030" class="rpt_table">
				<tfoot>
                    <td width="40" align="center" ><input type="checkbox" id="all_check" onClick="check_all('all_check')" /></td>
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
	if ($txt_alter_user_id!="") $user_id_approval=$txt_alter_user_id; 
	else $user_id_approval=$user_id;

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup"," page_id=$menu_id and user_id=$user_id_approval and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","page_id=$menu_id and is_deleted=0");

	if($approval_type == 0)
	{
		//echo $booking_ids;die;
		$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");		

		if ($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;
		
		$id = return_next_id( "id","approval_history", 1);
		$field_array = "id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date"; 
			
		$rcv_ids_all = implode(",",array_unique(explode(",",$rcv_ids)));
		$rcv_ids_allArr = explode(",",$rcv_ids_all);

		$max_approved_no_arr = return_library_array("SELECT mst_id, max(approved_no) as approved_no from approval_history where mst_id in($rcv_ids_all) and entry_form=51 group by mst_id","mst_id","approved_no");
		$approved_status_arr = return_library_array("SELECT id, approved from subcon_ord_mst where id in($rcv_ids_all)","id","approved");

		//$booking_ids_all = implode(",",array_unique(explode(",",$booking_ids)));
		//echo '<pre>';print_r($booking_ids_all);
		for($i=0; $i<count($rcv_ids_allArr); $i++)
		{
			$booking_id = $rcv_ids_allArr[$i];
			$approved_no=$max_approved_no_arr[$booking_id];
			$approved_status=$approved_status_arr[$booking_id];

			if($approved_status==0)
			{
				$approved_no=$approved_no+1;
			}

			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",51,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')"; 
			$id=$id+1;
		}

		$field_array_up="approved*approved_by*approved_date";
		$data_array_up=$partial_approval."*".$user_id."*'".$pc_date_time."'";
			
		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=51 and mst_id in ($rcv_ids_all)";
		$rIDapp=execute_query($query,1);

		if($rIDapp) $flag=1; else $flag=0;

	    //echo "10** insert into approval_history($field_array)values".$data_array;die;
		$rID = sql_insert("approval_history",$field_array,$data_array,0);
		if($flag==1)
		{
			if($rID) $flag=1; else $flag=0;
		}

		$rID2=sql_multirow_update("subcon_ord_mst",$field_array_up,$data_array_up,"id",$rcv_ids_all,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0;
		}

        if($flag==1) 
		{
			if($rID5) $flag=1; else $flag=0;
		}
		//echo "10**$flag".'system';die; 

		$response=$rcv_ids_all;
		if($flag==1) $msg='19'; else $msg='21';	
	}
	else
	{
		$rcv_ids_all = implode(",",array_unique(explode(",",$rcv_ids)));
		$approval_ids_all = implode(",",array_unique(explode(",",$approval_ids)));

	    $rID=sql_multirow_update("subcon_ord_mst","approved*ready_to_approved","0*0","id",$rcv_ids_all,0);

		$data="0*".$user_id_approval."*'".$pc_date_time."'*".$user_id."*'".$pc_date_time."'";
		$rID2=sql_multirow_update("approval_history","current_approval_status*un_approved_by*un_approved_date*updated_by*update_date",$data,"id",$approval_ids_all,1);

		$response=$rcv_ids_all;
	}
		
	if($db_type==0)
	{
		if($rID==1 && $rID2==1)
		{
			mysql_query("COMMIT");
			echo "20**".$response;
		}
		else
		{
			mysql_query("ROLLBACK");
			echo "22**".$response;
		}
	}
	else
	{
		if($rID==1 && $rID2==1)
		{
			oci_commit($con);
			echo "20**".$response;
		}
		else
		{
			oci_rollback($con);
			echo "22**".$response;
		}
	}
	disconnect($con);
	die;
}

if($action=='user_popup')
{
	echo load_html_head_contents("Popup Info","../../",1, 1,'',1,'');
	?>
	<script>
	// flowing script for multy select data------------------------------------------------------------------------------start;
	function js_set_value(id)
	{
		// alert(id)
		document.getElementById('selected_id').value=id;
		parent.emailwindow.hide();
	}
	// avobe script for multy select data------------------------------------------------------------------------------end;
	</script>
	<form>
        <input type="hidden" id="selected_id" name="selected_id" />
        <?php
        $custom_designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');
		$Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');	;
		$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a, electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and valid=1 and a.id!=$user_id  and b.is_deleted=0  and b.page_id=$menu_id order by b.sequence_no";;
			// echo $sql;
		$arr=array (2=>$custom_designation,3=>$Department);
		echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
		?>
	</form>
	<?
	exit();
}

?>
