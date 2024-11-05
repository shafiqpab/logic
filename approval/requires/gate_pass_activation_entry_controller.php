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

if($db_type==0)
{
	$select_year="year";
	$year_format="";
	$group_concat="group_concat";
}
else if ($db_type==2)
{
	$select_year="to_char";
	$year_format=",'YYYY'";
	$group_concat="wm_concat";
}

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$kniting_company_arr=return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0 $company_cod order by company_name","id","company_name");

$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
$buyer_short_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );



if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$sequence_no='';
	$company_name=str_replace("'","",$cbo_company_name);
	$get_pass_basis=str_replace("'","",$cbo_get_pass_basis);
 	$approval_type=str_replace("'","",$cbo_approval_type);
 	$txt_gate_pass_no=str_replace("'","",$txt_gate_pass_no);
	$cbo_year=str_replace("'","",$cbo_year); 
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id); 
	if(!empty($txt_alter_user_id))
	{
		$user_id=$txt_alter_user_id;
	}

	if($db_type==0) $year_cond="SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
	else if($db_type==2) $year_cond="to_char(a.insert_date,'YYYY') as year";

	if ($cbo_year=="" || $cbo_year==0) $gatepass_year_cond="";
	else
	{
		if($db_type==2)
		{
			$gatepass_year_cond=" and to_char(a.insert_date,'YYYY')='".trim($cbo_year)."' ";
		}
		else {
			$gatepass_year_cond=" and YEAR(a.insert_date)='".trim($cbo_year)."' ";
		}
	}

	$date_cond='';
	if(str_replace("'","",$txt_date)!="")
	{
		if(str_replace("'","",$cbo_get_upto)==1) $date_cond=" and a.out_date>$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==2) $date_cond=" and a.out_date<=$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==3) $date_cond=" and a.out_date=$txt_date";
		else $date_cond='';
	}

 	if($txt_gate_pass_no!=0) $gate_pass_cond="and a.sys_number_prefix_num='".$txt_gate_pass_no."' ";else $gate_pass_cond="";
	if($get_pass_basis!=0) $basis_cond="and a.basis='$get_pass_basis' ";else $basis_cond="";
	
	$sql_gate=sql_select("select gate_pass_id from inv_gate_out_scan where  gate_pass_id is not null and status_active=1 and is_deleted=0");
	$gate_out_id="";
	foreach($sql_gate as $row)
	{
		if($gate_out_id!="") $gate_out_id.=","."'".$row[csf('gate_pass_id')]."'";
		else $gate_out_id="'".$row[csf('gate_pass_id')]."'";
	}
	$gate_out_ids=count(array_unique(explode(",",$gate_out_id)));
		//echo $gate_out_id;
		$gate_outIds=chop($gate_out_id,','); $outIds_cond="";
		//print_r($gate_outIds);
		if($gate_out_id!='' || $gate_out_id!=0)
		{
			if($db_type==2 && $gate_out_ids>999)
			{
				$outIds_cond=" and (";
				$outIdsArr=array_chunk(explode(",",$gate_outIds),999);
				//print_r($gate_outIds);
				foreach($outIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$outIds_cond.=" a.sys_number not in($ids) or ";
				}
				$outIds_cond=chop($outIds_cond,'or ');
				$outIds_cond.=")";
			}
			else
			{
				$outIds_cond=" and  a.sys_number not in($gate_out_id)";
			}
		}
		//echo $outIds_cond;
	//$user_id=3;
	//$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
	//$job_dealing_merchant_array = return_library_array("select job_no, dealing_marchant from wo_po_details_master","job_no","dealing_marchant");
	$sql_job=sql_select("select b.id,a.buyer_name,a.job_no_prefix_num, a.job_no from wo_po_details_master a,  wo_po_break_down b where a.job_no=b.job_no_mst");
	foreach($sql_job as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['buyer_name']=$row[csf("buyer_name")];
		$buyer_po_arr[$row[csf("id")]]['job_no_prefix_num']=$row[csf("job_no")];
	}
	
	$po_array=array();
	if($db_type==0)
	{
		$po_array=return_library_array("select a.mst_id, group_concat(b.po_breakdown_id) as po_breakdown_id from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category=1 and a.transaction_type=2 and b.entry_form=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by a.mst_id", "mst_id", "po_breakdown_id" );
	}
	else
	{
		$po_array=return_library_array("select a.mst_id, LISTAGG(CAST( b.po_breakdown_id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.po_breakdown_id) as po_breakdown_id from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category=1 and a.transaction_type=2 and b.entry_form=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by a.mst_id", "mst_id", "po_breakdown_id" );
	}
	
	 $user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
	 $min_sequence_no=return_field_value("min(sequence_no) as sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0","sequence_no");
	
	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Gate Pass Activation Approval.</font>";
		die;
	}
	 
	if($approval_type==0)
	{
		//echo "max(sequence_no) as sequence_no from electronic_approval_setup where page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted=0";
		$sequence_no=return_field_value("max(sequence_no) as sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted=0","sequence_no");
		//echo $sequence_no.'aaaa';
		if($user_sequence_no==$min_sequence_no)
		{
		// group_concat(distinct(case when a.issue_basis=1 then  a.booking_no end )) as booking_no, a.is_approved 
			$sql="select a.id,$year_cond,a.time_hour,a.time_minute,  a.within_group,a.sys_number_prefix_num,a.challan_no, a.sent_to,a.challan_no, a.company_id,a.out_date,'0' as approval_id from  inv_gate_pass_mst a, inv_gate_pass_dtls b where a.id=b.mst_id  and a.company_id=$company_name and a.is_approved=$approval_type  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and  a.sys_number not in(select gate_pass_id from inv_gate_out_scan where  gate_pass_id is not null and status_active=1 and is_deleted=0) $basis_cond $gate_pass_cond $date_cond $gatepass_year_cond  group by a.id,a.insert_date,a.time_hour,a.time_minute, a.within_group,a.challan_no, a.sent_to,a.sys_number_prefix_num,a.challan_no, a.company_id,a.out_date order by a.id desc";
		}
		else if($sequence_no=="")
		{
			
			if($db_type==0)
				{
					$group_concat="group_concat(sequence_no) ";
					$group_concat2="group_concat(mst_id) ";
					//$group_concat3="group_concat(mst_id) ";
				}
				else
				{
					$group_concat="LISTAGG(CAST( sequence_no  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no ";
					$group_concat2="LISTAGG(CAST( b.mst_id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.mst_id) as quotation_id";
					//$group_concat3="LISTAGG(CAST( mst_id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY mst_id) as quotation_id";
				}
			$quotation_id_app_byuser=return_field_value("$group_concat2","inv_gate_pass_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_name  and b.sequence_no=$user_sequence_no and b.entry_form=19 and b.current_approval_status=1","quotation_id");
			
			if($quotation_id_app_byuser!="") $quotation_id_cond=" and a.id not in($quotation_id_app_byuser)";
			else if($quotation_id!="") $quotation_id_cond.=" or (a.id in($quotation_id))";
			else $quotation_id_cond="";
			
			//echo "aziz";
			
			
				$sequence_no_by=return_field_value("$group_concat ","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0","sequence_no");
				if($sequence_no_by)
				{
					$quotation_id=return_field_value("$group_concat2","inv_gate_pass_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_name  and b.sequence_no in ($sequence_no_by) and b.entry_form=19 and b.current_approval_status=1","quotation_id");
				
				}
				
				//$quotation_id_app_byuser=return_field_value("$group_concat2","inv_gate_pass_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_name  and b.sequence_no=$user_sequence_no and b.entry_form=19 and b.current_approval_status=1","quotation_id");
				$quotation_id_app_byuser=implode(",",array_unique(explode(",",$quotation_id_app_byuser)));
				
				   $sql="select a.id,$year_cond,a.time_hour,a.time_minute,  a.sys_number_prefix_num,a.challan_no, a.sent_to,a.challan_no, a.company_id,a.out_date,'0' as approval_id from  inv_gate_pass_mst a, inv_gate_pass_dtls b where a.id=b.mst_id and a.company_id=$company_name  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and a.is_approved=$approval_type   and  a.sys_number not in(select gate_pass_id from inv_gate_out_scan where  gate_pass_id is not null and status_active=1 and is_deleted=0) $basis_cond $gate_pass_cond $date_cond  $quotation_id_cond $gatepass_year_cond group by a.id,a.insert_date,a.time_hour,a.time_minute, a.within_group,a.sys_number_prefix_num,a.challan_no, a.sent_to,a.challan_no, a.company_id,a.out_date order by a.id desc";
			
		}
		else
		{
			$user_sequence_no=$user_sequence_no-1;
			
			if($sequence_no==$user_sequence_no) $sequence_no_by_pass='';
			else
			{
				if($db_type==0)
				{
					$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and is_deleted=0");
				}
				else
				{
					$sequence_no_by_pass=return_field_value("LISTAGG(CAST( sequence_no  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and is_deleted=0","sequence_no");	
				}
			}
			
			if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$sequence_no'";
			else $sequence_no_cond=" and (c.sequence_no='$sequence_no' or c.sequence_no in ($sequence_no_by_pass))";
		
				 $sql="select a.id,$year_cond,a.time_hour,a.time_minute, a.sys_number_prefix_num, a.challan_no, a.sent_to,a.challan_no, a.company_id,a.out_date, a.is_approved   from  inv_gate_pass_mst a,  approval_history c, inv_gate_pass_dtls b where a.id=b.mst_id and a.id=c.mst_id  and a.company_id=$company_name  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.entry_form=19 and a.is_approved=1  and  a.sys_number not in(select gate_pass_id from inv_gate_out_scan where  gate_pass_id is not null and status_active=1 and is_deleted=0) $basis_cond $gate_pass_cond $date_cond $gatepass_year_cond group by a.id,a.insert_date,a.time_hour,a.time_minute,  a.challan_no, a.sent_to,a.sys_number_prefix_num,a.challan_no, a.company_id,a.out_date, a.is_approved order by a.id desc";
			
				
		}
	}
	else
	{
		if($user_sequence_no!="") $sequence_no_cond=" and c.sequence_no='$user_sequence_no'";else $sequence_no_cond="";
		if($db_type==0)
		{
			 $sql="select a.id,$year_cond,a.time_hour,a.time_minute, a.sys_number_prefix_num, a.challan_no,a.within_group, a.sent_to,a.company_id,a.out_date, group_concat(distinct c.id) as approval_id, a.is_approved from  inv_gate_pass_mst a, approval_history c, inv_gate_pass_dtls b where a.id=b.mst_id and b.mst_id=c.mst_id and a.company_id=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=1 and c.entry_form=19   and  a.sys_number not in(select gate_pass_id from inv_gate_out_scan where  gate_pass_id is not null and status_active=1 and is_deleted=0) $basis_cond $gate_pass_cond $date_cond $sequence_no_cond  $gatepass_year_cond
			group by a.id,a.insert_date,a.time_hour,a.time_minute, a.sent_to,a.sys_number_prefix_num,a.challan_no, a.company_id,a.out_date, a.is_approved order by a.id desc";
		}
		else if($db_type==2)
		{
			 $sql="select a.id,$year_cond, a.time_hour,a.time_minute,a.within_group, a.sent_to,a.sys_number_prefix_num,a.challan_no, a.company_id,a.out_date, a.is_approved, LISTAGG(CAST( c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as approval_id   from  inv_gate_pass_mst a,  approval_history c, inv_gate_pass_dtls b  where a.id=b.mst_id and a.id=c.mst_id and a.company_id=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=1  and c.entry_form=19  and  a.sys_number not in(select gate_pass_id from inv_gate_out_scan where  gate_pass_id is not null and status_active=1 and is_deleted=0) $basis_cond $gate_pass_cond $date_cond $sequence_no_cond  $gatepass_year_cond group by a.id, a.insert_date,a.time_hour,a.time_minute,  a.sent_to,a.sys_number_prefix_num,a.challan_no, a.company_id,a.out_date,a.within_group, a.is_approved order by a.id desc";
			//, LISTAGG(CAST( d.po_breakdown_id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY d.po_breakdown_id) as po_breakdown_id 
		//echo $sequence_no_cond;
		}
		
	}
	//echo $sql;
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:940px; margin-top:10px">
        <legend>Gate Pass Activation Approval</legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="920" class="rpt_table" >
                <thead>
                	<th width="50"></th>
                    <th width="40">SL</th>
                    <th width="100">Company</th>
                    <th width="80">Gate Pass No</th>
                    <th width="40">Year</th>
                    <th width="120">Challan No</th>
                    <th width="80">Gate Pass Time</th>
                    <th width="100">Gate Pass Date</th>
                    <th width="100">Within Group</th>
                    <th>Sent To</th>
                </thead>
            </table>
            <div style="width:920px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
						//echo $current_date=date('d-m-Y h:i:s');
						
                            $i=1;
                            $nameArray=sql_select( $sql );$report_title="Gate Pass Activation";
                            foreach ($nameArray as $row)
                            {
								/*$out_date=$row[csf("out_date")];
								$current_date=date('d-m-Y h:i:s');
								$gate_pass_time=$row[csf('time_hour')].':'. $row[csf('time_minute')].':'.'00';
								$gate_out_date=change_date_format($out_date,'dd-mm-yyyy','-').' '.$gate_pass_time;
								$total_time=datediff(n,$gate_out_date,$current_date);
								$total_hour=floor($total_time/60);*/


								$gate_pass_time=$row[csf('time_hour')].':'. $row[csf('time_minute')].':'.'00';
								
								$out_date=change_date_format($row[csf("out_date")]);
								$to_time=$out_date." ".$row[csf('time_hour')].':'. $row[csf('time_minute')].':'.'00';
								$to_time = strtotime("$to_time");
								$today_date=date("d-m-Y H:m:s ");
								$today_date = strtotime("$today_date");
								$date_difference=$today_date-$to_time;
								$total_hour=floor($date_difference/3600);
								
								$approvar_id=implode(",",array_unique(explode(",",$row[csf('approval_id')])));
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
									$app_id=return_field_value("id","approval_history","mst_id ='".$row[csf('id')]."' and entry_form='19' order by id desc");
									$value=$row[csf('id')]."**".$app_id;
								}
								$within_group=$row[csf('within_group')];
								if($within_group==2)
								{
									$sent_to=$row[csf('sent_to')];
								}
								else
								{
									$sent_to=$company_arr[$row[csf('sent_to')]];
								}
								if($total_hour>24)
								{
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" align="center"> 
                                	<td width="50" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="booking_id_<? echo $i;?>" name="booking_id[]"  style=" width:40px"  type="hidden" value="<? echo $value; ?>" />
                                        <input id="booking_no_<? echo $i;?>" name="booking_no]"  style=" width:40px"  type="hidden" value="<? echo $row[csf('id')]; ?>" />
                                        <input id="approval_id_<? echo $i;?>" name="approval_id[]"  style=" width:40px"  type="hidden" value="<? echo $approvar_id; ?>" />
                                    </td>   
									<td width="40" align="center"><? echo $i; ?></td>
									<td width="100">
                                    	<p>
                                        <?
                                        echo $company_arr[$row[csf('company_id')]];
										?>
                                       </p>
                                    </td>
                                    <td width="80" align="center"><p><? echo  $row[csf('sys_number_prefix_num')]; ?></p></td>
                                    <td width="40"><p><? echo $row[csf('year')]; ?>&nbsp;</p></td>
                                    <td width="120" align="center" style="max-width: 120px"><p><? echo  $row[csf('challan_no')]; ?></p></td>
                                    <td width="80"><p><?  echo $gate_pass_time;//$row[csf('time_hour')].':'. $row[csf('time_minute')]; ?>&nbsp;</p></td>
									<td width="100" align="left"><p><? if($row[csf('out_date')]!="0000-00-00") echo change_date_format($row[csf('out_date')]); ?>&nbsp;</p></td>
                                    <td width="100" align="center"><p><? echo $yes_no[$row[csf('within_group')]];?></p>&nbsp;</td>
                                    <td width="" align="left"><p><? echo $sent_to;//$row[csf('sent_to')]; ?>&nbsp;</p></td>
                                    
								</tr>
								<?
								$i++;
								}
							}
                        ?>
                    </tbody>
                </table>
                 <table cellspacing="0" cellpadding="0" border="1"  rules="all" width="870" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" ><input type="checkbox" id="all_check" onclick="check_all('all_check')" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onclick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/></td>
				</tfoot>
			</table>
            </div>
           
        </fieldset>
    </form>         
<?
	exit();	
}


if ($action=="approve")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//$user_id=23;
	//echo $booking_nos;die;
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	$msg=''; $flag=''; $response='';
	
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");
	
	if($type==0)
	{
		$approval_ids=str_replace("'","",$approval_ids);
		$booking_ids=str_replace("'","",$booking_ids);
		//echo $approval_ids.'=='.$booking_ids;die;
		$response=$booking_ids;
		$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date"; 
		$id=return_next_id( "id","approval_history", 1 ) ;
		
		$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=19 group by mst_id","mst_id","approved_no");
		
		$approved_status_arr = return_library_array("select id, is_approved from inv_gate_pass_mst where id in($booking_ids)","id","is_approved");
		

		//$rID=sql_multirow_update("inv_issue_master","is_approved",1,"id",$booking_ids,0);
		//if($rID) $flag=1; else $flag=0;
		
		/*if($approval_ids!="")
		{
			$rIDapp=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,0);
			if($flag==1) 
			{
				if($rIDapp) $flag=1; else $flag=0; 
			} 
		}*/
		
		/*if($user_sequence_no==$min_sequence_no)
		{
			$rID=sql_multirow_update("wo_booking_mst","is_approved",1,"id",$booking_ids,0);
			if($rID) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,0);
			if($rID) $flag=1; else $flag=0;
		}*/
		
		$approved_no_array=array();
		$booking_ids_all=explode(",",$booking_ids);
		$booking_nos_all=str_replace("'","",explode(",",$booking_nos));
		$book_nos='';
		//print_r($booking_nos_all);die;
		
		for($i=0;$i<count($booking_nos_all);$i++)
		{
			$val=$booking_nos_all[$i];
			$booking_id=$booking_ids_all[$i];

			$approved_no=$max_approved_no_arr[$booking_id];
			$approved_status=$approved_status_arr[$booking_id];
			
			if($approved_status==0)
			{
				$approved_no=$approved_no+1;
				$approved_no_array[$val]=$approved_no;
				if($book_nos=="") $book_nos=$val; else $book_nos.=",".$val;
				
			}
			//echo $approved_status;die;
			if($data_array!="") $data_array.=",";
			
			$data_array.="(".$id.",19,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id.",'".$pc_date_time."')"; 
				
			$id=$id+1;
			
		}
		//echo $data_array;die;
		//echo "insert into approval_history (".$field_array.") Values ".$data_array."**".$book_nos."**".$booking_nos;die;
		/*$rID2=sql_insert("approval_history",$field_array,$data_array,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		}*/ 
		//print_r($approved_no_array);die;
		if(count($approved_no_array)>0)
		{
			$approved_string="";
			//print_r( $approved_no_array);die;
			foreach($approved_no_array as $key=>$value)
			{
				$approved_string.=" WHEN ".str_replace("'","",$key)." THEN $value";
			}
			
			$approved_string_mst="CASE id ".$approved_string." END";
			$approved_string_dtls="CASE mst_id ".$approved_string." END";
			//$approved_string_dtls_ppropor="CASE mst_id ".$approved_string." END";
			
			/*$sql_insert="insert into  inv_issue_master_history(id,approve_no,issue_id,issue_number_prefix,issue_number_prefix_num,issue_number,issue_basis,issue_purpose,entry_form,item_category,company_id,location_id,supplier_id,store_id,buyer_id,buyer_job_no,style_ref,booking_id,booking_no,req_no,batch_no,issue_date,sample_type,knit_dye_source,knit_dye_company,challan_no,loan_party,lap_dip_no,gate_pass_no,item_color,color_range,remarks,received_id,received_mrr_no,other_party,order_id,is_approved,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted) 
				select	
				'', $approved_string_mst, id,issue_number_prefix,issue_number_prefix_num,issue_number,issue_basis,issue_purpose,entry_form,item_category,company_id,location_id,supplier_id,store_id,buyer_id,buyer_job_no,style_ref,booking_id,booking_no,req_no,batch_no,issue_date,sample_type,knit_dye_source,knit_dye_company,challan_no,loan_party,lap_dip_no,gate_pass_no,item_color,color_range,remarks,received_id,received_mrr_no,other_party,order_id,is_approved,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from  inv_issue_master where id in ($book_nos)";
			//echo $sql_insert;die;		
			/*$rID3=execute_query($sql_insert,0);
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			}*/ 
			
			$sql_insert_dtls="insert into  inv_transaction_history (id, approve_no, transaction_id, mst_id, requisition_no, receive_basis,pi_wo_batch_no,company_id,supplier_id,prod_id,product_code,item_category,
transaction_type,transaction_date,store_id,order_id,brand_id,order_uom,order_qnty,order_rate,order_ile,order_ile_cost,
order_amount,cons_uom,cons_quantity,return_qnty,cons_reject_qnty,cons_rate,cons_ile,cons_ile_cost,cons_amount,balance_qnty,
balance_amount,floor_id,machine_id,machine_category,no_of_bags,cone_per_bag,weight_per_bag,weight_per_cone,room,rack,self,bin_box,
expire_date,dyeing_sub_process,issue_challan_no,remarks,batch_lot,location_id,department_id,section_id,job_no,dyeing_color_id,inserted_by,
insert_date,updated_by,update_date,status_active,is_deleted) 
				select	
				'', $approved_string_dtls, id, mst_id, requisition_no, receive_basis,pi_wo_batch_no,company_id,supplier_id,prod_id,product_code,item_category,transaction_type,transaction_date,store_id,
order_id,brand_id,order_uom,order_qnty,order_rate,order_ile,order_ile_cost,order_amount,cons_uom,cons_quantity,return_qnty,cons_reject_qnty,cons_rate,
cons_ile,cons_ile_cost,cons_amount,balance_qnty,balance_amount,floor_id,machine_id,machine_category,no_of_bags,cone_per_bag,weight_per_bag,weight_per_cone,
room,rack,self,bin_box,expire_date,dyeing_sub_process,issue_challan_no,remarks,batch_lot,location_id,department_id,section_id,job_no,dyeing_color_id,
inserted_by,insert_date,updated_by,update_date,status_active,is_deleted   from inv_transaction where mst_id in ($book_nos) and transaction_type=2  and status_active=1";
			//echo $sql_insert_dtls;die;		*/
/*			$rID4=execute_query($sql_insert_dtls,1);
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			}
*/			 
			
			/*$dtls_trans_id=return_field_value("$group_concat(distinct id) as id"," inv_gate_pass_dtls","mst_id in($book_nos)   and status_active=1","id");

			
			$sql_insert_dtls_propor="insert into  order_wise_pro_detail_history (id,approve_no, proportionate_id,trans_id,trans_type,entry_form,dtls_id,po_breakdown_id,prod_id,color_id,quantity,issue_purpose,returnable_qnty,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted) 
				select	
				'', 1, id,trans_id,trans_type,entry_form,dtls_id,po_breakdown_id,prod_id,color_id,quantity,issue_purpose,returnable_qnty,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted  from order_wise_pro_details where trans_id in ($trans_id)";*/
			//echo $sql_insert_dtls_propor;die;	
			//echo $booking_ids;die;
			$rID=sql_multirow_update("inv_gate_pass_mst","is_approved",1,"id",$booking_ids,1);
			if($rID) $flag=1; else $flag=0;
			//echo $flag;die;
			//echo $approval_ids;die;
			if($approval_ids!="")
			{
				$rIDapp=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,1);
				if($flag==1) 
				{
					if($rIDapp) $flag=1; else $flag=0; 
					//echo $flag;die;
				} 
			}
			$rID2=sql_insert("approval_history",$field_array,$data_array,1);
			//echo $rID2;return;
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
				//echo  $flag;die;
			}
			/*$rID3=execute_query($sql_insert,1);
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			}
			
			$rID4=execute_query($sql_insert_dtls,1);
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			}
				
			$rID5=execute_query($sql_insert_dtls_propor,1);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			}*/
			//echo $flag;die;	
		}
			//echo $sql_insert_dtls_propor;die;
		//echo $flag;die;
		if($flag==1) $msg='19'; else $msg='21';
	}
	else
	{
		//echo($booking_ids);die;
		
		//$booking_ids=str_replace("'","",$booking_ids);
		//print_r($booking_ids);die;
		$booking_ids_all=explode(",",$booking_ids);
		//print_r($booking_ids_all);die;
		$booking_ids=''; $app_ids='';
		foreach($booking_ids_all as $value)
		{
			$data = explode('**',$value);
			$booking_id=$data[0];
			$app_id=$data[1];
			if($booking_ids=='') $booking_ids=$booking_id; else $booking_ids.=",".$booking_id;
			if($app_ids=='') $app_ids=$app_id; else $app_ids.=",".$app_id;
		}
		//echo $approval_ids;die;
		$data=$user_id."*'".$pc_date_time."'";
		$rID=sql_multirow_update("inv_gate_pass_mst","is_approved",0,"id",$booking_ids,1);
		if($rID) $flag=1; else $flag=0;
		//echo $flag;die;
		if($approval_ids!="")
			{
				$rID2=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,1);
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0;
					
				} 
			}
		//echo $flag;die;
		if($app_ids!="")
		{
			$rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date",$data,"id",$app_ids,1);
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			}
		}
		
		//echo $app_ids;die;
		$response=$booking_ids;
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
	//release lock table   oci_commit($con); oci_rollback($con); 
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
		 	//$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
			$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and valid=1 and a.id!=$user_id  and b.is_deleted=0  and b.page_id=$menu_id order by b.sequence_no";
			//echo $sql;
		 $arr=array (2=>$custom_designation,3=>$Department);
		 echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
		?>

	</form>
	<script language="javascript" type="text/javascript">
	  setFilterGrid("tbl_style_ref");
	</script>


	<?
}
?>