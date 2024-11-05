<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_ip=$_SESSION['logic_erp']['user_ip'];

extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');
require_once('../../mailer/class.phpmailer.php');
$from_mail="PLATFORM-ERP@fakir.app";
	
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
	exit();
}

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$sequence_no='';
	$company_name=str_replace("'","",$cbo_company_name);
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") 
			{
				$buyer_id_cond2=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else 
			{
				$buyer_id_cond2="";
			}
		}
		else
		{
			$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond2=" and a.buyer_id=$cbo_buyer_name";
	}
	
	$approval_type = str_replace("'","",$cbo_approval_type);

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0","seq");
	
	$buyer_ids_array=array();
	$buyerData=sql_select("select user_id, sequence_no, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0 and bypass=2");
	foreach($buyerData as $row)
	{
		$buyer_ids_array[$row[csf('user_id')]]['u']=$row[csf('buyer_id')];
		$buyer_ids_array[$row[csf('sequence_no')]]['s']=$row[csf('buyer_id')];
	}
	
	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Fabric Booking.</font>";
		die;
	}
	
	if($db_type==0) 
	{		
		$orderBy_cond="IFNULL";
	}
	else if($db_type==2) 
	{		
		$orderBy_cond="NVL";
	}
	else 
	{		
		$orderBy_cond="ISNULL";
	}

	if($approval_type==0)
	{
		if($db_type==0)
		{
			$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id='' and is_deleted=0","seq");
		}
		else
		{
			$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
		}
		
		if($user_sequence_no==$min_sequence_no)
		{	
			$buyer_ids = $buyer_ids_array[$user_id]['u'];
			if($buyer_ids == "") $buyer_id_cond = ""; else $buyer_id_cond = " and a.buyer_id in($buyer_ids)";			
            $sql = "select a.id,a.company_id,a.buyer_id,b.buyer_name,a.system_no,a.po_job_no,a.style_ref,a.gmts_item_id,a.is_approved,'0' as approval_id from ppl_gsd_entry_mst a, wo_po_details_master b where a.po_job_no = b.job_no and a.company_id = $company_name and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved = 1 and a.is_approved=$approval_type $buyer_id_cond $buyer_id_cond2 $buyerIds_cond group by a.id,a.company_id,a.buyer_id,a.system_no,a.po_job_no,a.style_ref,a.gmts_item_id,a.is_approved,a.update_date, a.insert_date,b.buyer_name order by $orderBy_cond(a.update_date, a.insert_date) desc";	              
		}
		else if($sequence_no == "")
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids == "") $buyer_id_cond = ""; else $buyer_id_cond = " and a.buyer_id in($buyer_ids)";
			
			if($db_type == 0)
			{
				$seqSql="select group_concat(sequence_no) as sequence_no_by,
 group_concat(case when bypass=2 and buyer_id<>'' then buyer_id end) as buyer_ids from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
				$seqData=sql_select($seqSql);
				
				$sequence_no_by = $seqData[0][csf('sequence_no_by')];
				$buyerIds = $seqData[0][csf('buyer_ids')];
				
				if($buyerIds == "") $buyerIds_cond = ""; else $buyerIds_cond = " and a.buyer_id not in($buyerIds)";
			}
			else
			{
				$seqSql="select LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no_by, LISTAGG(case when bypass=2 and buyer_id is not null then buyer_id end, ',') WITHIN GROUP (ORDER BY null) as buyer_ids from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
				$seqData=sql_select($seqSql);
				
				$sequence_no_by=$seqData[0][csf('sequence_no_by')];
				$buyerIds=$seqData[0][csf('buyer_ids')];
				
				if($buyerIds == "") $buyerIds_cond = ""; else $buyerIds_cond = " and a.buyer_id not in($buyerIds)";				
			}

			if($db_type==0)
			{	                
                $sql = "select a.id,a.company_id,a.buyer_id,b.buyer_name,a.system_no,a.po_job_no,a.style_ref,a.gmts_item_id,a.is_approved,'0' as approval_id from ppl_gsd_entry_mst a, wo_po_details_master b where a.po_job_no = b.job_no and a.company_id = $company_name and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved = 1 and a.is_approved=$approval_type $buyer_id_cond $buyer_id_cond2 $buyerIds_cond group by a.id,a.company_id,a.buyer_id,a.system_no,a.po_job_no,a.style_ref,a.gmts_item_id,a.is_approved,a.update_date, a.insert_date,b.buyer_name order by $orderBy_cond(a.update_date, a.insert_date) desc";	              
            }
			else
			{               
                $sql = "select a.id,a.company_id,a.buyer_id,b.buyer_name,a.system_no,a.po_job_no,a.style_ref,a.gmts_item_id,a.is_approved,'0' as approval_id from ppl_gsd_entry_mst a, wo_po_details_master b where a.po_job_no = b.job_no and a.company_id = $company_name and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved = 1 and a.is_approved=$approval_type $buyer_id_cond $buyer_id_cond2 $buyerIds_cond group by a.id,a.company_id,a.buyer_id,a.system_no,a.po_job_no,a.style_ref,a.gmts_item_id,a.is_approved,a.update_date, a.insert_date,b.buyer_name order by $orderBy_cond(a.update_date, a.insert_date) desc";	              
            }
		}
		else
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
			
			$user_sequence_no=$user_sequence_no-1;
			
			if($sequence_no==$user_sequence_no) 
			{
				$sequence_no_by_pass='';
			}
			else
			{
				if($db_type==0)
				{
					$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");
				}
				else
				{
					$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");	
				}
				
				if($sequence_no_by_pass=="") {
                    $sequence_no_cond=" and c.sequence_no='$sequence_no'";
                }
				else {
                    $sequence_no_cond=" and c.sequence_no in ($sequence_no_by_pass)";
                }
                $sql = "select a.id,a.company_id,a.buyer_id,b.buyer_name,a.system_no,a.po_job_no,a.style_ref,a.gmts_item_id,a.is_approved, c.id as approval_id, c.sequence_no, c.approved_by from ppl_gsd_entry_mst a, wo_po_details_master b, approval_history c where a.id=c.mst_id and c.entry_form=23 and c.current_approval_status=1 and a.po_job_no = b.job_no and a.company_id = $company_name and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved = 1 and a.is_approved=$approval_type $buyer_id_cond $buyer_id_cond2 group by a.id,a.company_id,a.buyer_id,a.system_no,a.po_job_no,a.style_ref,a.gmts_item_id,a.is_approved,a.update_date, a.insert_date,b.buyer_name,c.id as approval_id, c.sequence_no, c.approved_by $sequence_no_cond order by $orderBy_cond(a.update_date, a.insert_date) desc";	                
			}
		}                                                                                                                                                                                                   
	}
	else
	{
		$buyer_ids = $buyer_ids_array[$user_id]['u'];
		if ($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
		$sequence_no_cond=" and c.approved_by='$user_id'";
        $sql = "select a.id,a.company_id,a.buyer_id,b.buyer_name,a.system_no,a.po_job_no,a.style_ref,a.gmts_item_id,a.is_approved,c.id as approval_id, c.sequence_no, c.approved_by from ppl_gsd_entry_mst a, wo_po_details_master b, approval_history c where a.id=c.mst_id and c.entry_form=23 and c.current_approval_status=1 and a.po_job_no = b.job_no and a.company_id = $company_name and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved = 1 and a.is_approved=$approval_type $buyer_id_cond $buyer_id_cond2 $sequence_no_cond group by a.id,a.company_id,a.buyer_id,a.system_no,a.po_job_no,a.style_ref,a.gmts_item_id,a.is_approved,a.update_date, a.insert_date,b.buyer_name order by $orderBy_cond(a.update_date, a.insert_date) desc";	                
        
    }
	//echo $sql;
    if($approval_type==0)
    {			
        $fset=850;
        $table1=800; 
        $table2=780; 
    }
    else if($approval_type==1)
    {
        $fset=850;
        $table1=800; 
        $table2=780; 
    }
    
    $print_report_format_ids2=return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name."  and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");
    $format_ids=explode(",",$print_report_format_ids2);	
	
    ?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:<? echo $fset; ?>px; margin-top:10px">
        <legend>GSD Approval</legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table1; ?>" class="rpt_table" >
                <thead>
                    <th width="50"></th>
                    <th width="50">SL</th>                   
                    <th width="50">System No</th>
                    <th width="50">Job No</th>
                    <th width="100">Style No</th>
                    <th width="125">Buyer</th>                    
                    <th width="100">Garments Items</th>                                       
                </thead>
            </table>            
            <div style="width:<? echo $table1; ?>px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table2; ?>" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?						 
                        $i=1; $all_approval_id='';
                        $nameArray=sql_select( $sql );
                        foreach ($nameArray as $row)
                        {                                                   
                            if ($i%2==0)  
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                                    <td width="50" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" name="tbl[]" onClick="check_last_update(<? echo $i;?>);" />
                                        <input type="hidden" id="target_id_<? echo $i;?>" name="target_id_[]"  value="<? echo $row[csf('id')]; ?>" />                                                  
                                        <input id="approval_id_<? echo $i;?>" name="approval_id[]" type="hidden" value="<? echo $row[csf('approval_id')]; ?>" />
                                   </td> 
                                    <td width="50" align="center"><p><?php echo $i;?>&nbsp;</p></td>
                                    <td width="50"><p><? echo $row[csf('system_no')]; ?>&nbsp;</p></td>
                                    <td width="50"><p><? echo $row[csf('po_job_no')]; ?>&nbsp;</p></td>
                                    <td width="100"><p><? echo $row[csf('style_ref')]; ?>&nbsp;</p></td>								
                                    <td width="125"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?> &nbsp;</p></td>                                  
                                    <td width="100" align="center"><p><?php echo $garments_item[$row[csf('gmts_item_id')]];?> &nbsp;</p></td>      
                                </tr>
                                <?
                                $i++;

                            if($all_approval_id!="")
                            {
                                $con = connect();
                                $rID=sql_multirow_update("approval_history","current_approval_status",0,"id",$all_approval_id,1);
                                disconnect($con);
                            }
                        }                           
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="<? echo $table1; ?>" class="rpt_table">
				<tfoot>
                    <td width="75" align="center" >
                    	<input type="checkbox" id="all_check" onClick="check_all('all_check')" />
                        <input type="hidden" name="hide_approval_type" id="hide_approval_type" value="<? echo $approval_type; ?>">
                        <font style="display:none"><? echo $all_approval_id; ?></font>
                    </td>
                    <td align="left">
                        &nbsp;<input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/>
                    </td>
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
		$response=$target_ids;
		$rID=sql_multirow_update("ppl_gsd_entry_mst","is_approved",1,"id",$target_ids,0);
		if($rID) $flag=1; else $flag=0;
		
		$target_ids=explode(",",$target_ids);
		$field_array="id, entry_form, mst_id, approved_no, approved_by, approved_date"; 
		$i=0;
		$id=return_next_id( "id","approval_history", 1 ) ;
		
		$approved_no_array=array();
		
		foreach($target_ids as $val)
		{
			$approved_no=return_field_value("max(approved_no)","approval_history","mst_id='$val'");
			$approved_no=$approved_no+1;
		
			if($i!=0) $data_array.=",";
			
			$data_array.="(".$id.",23,".$val.",".$approved_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
			
			$approved_no_array[$val]=$approved_no;
				
			$id=$id+1;
			$i++;
		}
		
		$rID2=sql_insert("approval_history",$field_array,$data_array,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		$approved_string="";
		
		foreach($approved_no_array as $key=>$value)
		{
			$approved_string.=" WHEN $key THEN $value";
		}
		
		$approved_string_mst="CASE id ".$approved_string." END";
		//$approved_string_dtls="CASE mst_id ".$approved_string." END";
			$sql_insert="insert into ppl_gsd_entry_mst_history(id, hist_mst_id, approved_no,company_id, po_dtls_id, po_job_no, po_break_down_id, working_hour, total_smv, allowance, sam_style, operation_count, pitch_time, man_power_1, man_power_2, per_hour_gmt_target, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, gmts_item_id, day_target, mc_operation_count, tot_mc_smv, opt_pitch_time, style_ref, buyer_id, tot_manual_smv, tot_finishing_smv, system_no_prefix, extention_no, system_no, extended_from, is_copied, is_approved, ready_to_approved)
			select	
			'', id, $approved_string_mst, company_id, po_dtls_id, po_job_no, po_break_down_id, working_hour, total_smv, allowance, sam_style, operation_count, pitch_time, man_power_1, man_power_2, per_hour_gmt_target, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, gmts_item_id, day_target, mc_operation_count, tot_mc_smv, opt_pitch_time, style_ref, buyer_id, tot_manual_smv, tot_finishing_smv, system_no_prefix, extention_no, system_no, extended_from, is_copied, is_approved, ready_to_approved from  ppl_gsd_entry_mst where id in ($target_ids)";
				
		$rID3=execute_query($sql_insert,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		
		/* $sql_insert_dtls="insert into  inv_pur_requisition_dtls_hist(id, approved_no, mst_id, product_id, user_code_maintain, required_for, job_id, job_no, buyer_id, style_ref_no, color_id, count_id, composition_id, com_percent, yarn_type_id, yarn_inhouse_date, cons_uom, quantity, rate, amount, stock, remarks, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted) 
			select	
			'', $approved_string_dtls, mst_id, product_id, user_code_maintain, required_for, job_id, job_no, buyer_id, style_ref_no, color_id, count_id, composition_id, com_percent, yarn_type_id, yarn_inhouse_date, cons_uom, quantity, rate, amount, stock, remarks, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from  inv_purchase_requisition_dtls where mst_id in ($req_nos)";
				
		$rID4=execute_query($sql_insert_dtls,1);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		}  */
		
		if($flag==1) $msg='19'; else $msg='21';
	}
	else
	{
		/* $target_ids = explode(',',$target_ids); 
		
		$reqs_ids=''; $app_ids='';
		
		foreach($req_nos as $value)
		{
			$data = explode('**',$value);
			$reqs_id=$data[0];
			$app_id=$data[1];
			
			if($reqs_ids=='') $reqs_ids=$reqs_id; else $reqs_ids.=",".$reqs_id;
			if($app_ids=='') $app_ids=$app_id; else $app_ids.=",".$app_id;
		}
		$rID=sql_multirow_update("inv_purchase_requisition_mst","is_approved",0,"id",$reqs_ids,0);
		if($rID) $flag=1; else $flag=0;
		
		$data=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID2=sql_multirow_update("approval_history","un_approved_by*un_approved_date",$data,"id",$app_ids,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		
		$response=$reqs_ids;
		
		if($flag==1) $msg='20'; else $msg='22'; */
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