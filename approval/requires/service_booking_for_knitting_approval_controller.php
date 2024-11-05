<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_ip=$_SESSION['logic_erp']['user_ip'];

extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');
require_once('../../mailer/class.phpmailer.php');
	
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
	exit();
}

if ($action == "populate_data_booking") {
	
	
	
	
	$sql = " select a.item_category, a.company_id,a.po_break_down_id,a.fabric_source,a.is_approved,a.booking_no   from    wo_booking_mst a,wo_booking_dtls b  where b.job_no='".$data."' and a.booking_type=1 and a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.item_category, a.company_id,a.po_break_down_id,a.fabric_source,a.is_approved,a.booking_no";
	//echo $sql;
	$result=sql_select($sql);


	
	echo $result[0][csf('booking_no')]."#".$result[0][csf('is_approved')]."#".$result[0][csf('fabric_source')]."#".$result[0][csf('po_break_down_id')]."#".$result[0][csf('company_id')]."#".$result[0][csf('item_category')];
	exit();
}


$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$sequence_no = '';
	$company_name = str_replace("'","",$cbo_company_name);
	$txt_booking_no = str_replace("'","",$txt_booking_no);
	$txt_date = str_replace("'","",$txt_date);
	
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

	/*echo "SELECT sequence_no FROM electronic_approval_setup WHERE company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0";*/

    $user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0","seq");	
	
	$approval_necessity_setup=return_field_value("approval_need","approval_setup_mst a,approval_setup_dtls b","a.id=b.mst_id and a.company_id=$company_name and b.page_id=27 and a.status_active=1 and a.is_deleted=0  order by a.setup_date desc","approval_need");	
	
	// echo $approval_necessity_setup;
	if($approval_necessity_setup==0 || $approval_necessity_setup==2)
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>Approval Do Not Required.</font>";
		die;
	}
	// $approval_necessity_setup=1;
	$buyer_ids_array=array();
	$buyerData=sql_select("select user_id, sequence_no, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0 and bypass=2");
	foreach($buyerData as $row)
	{
		$buyer_ids_array[$row[csf('user_id')]]['u']=$row[csf('buyer_id')];
		$buyer_ids_array[$row[csf('sequence_no')]]['s']=$row[csf('buyer_id')];
	}

	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Service Booking.</font>";
		die;
	}
	
	if($db_type==0) 
	{		
		$year_field="YEAR(a.insert_date) as year"; 
        $orderBy_cond="IFNULL";
	}
	else if($db_type==2) 
	{		
		$year_field="to_char(a.insert_date,'YYYY') as year";
        $orderBy_cond="NVL";
	}
	else 
	{	
        $year_field="";//defined Later
		$orderBy_cond="ISNULL";
	}

	
	if($txt_booking_no!='') 
	{		
		$where_id_cond.=" and a.booking_no like('%".$txt_booking_no."')"; 
	}
	
	if($txt_date!='') 
	{		
		$where_id_cond.=" and a.booking_date='$txt_date'"; 
	}
	

	
	if($approval_type==0) // unapproval process start
	{
		
        if($db_type==0)
		{
			$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id='' and is_deleted=0","seq");
		}
		else
		{
			/*echo "SELECT max(sequence_no) as seq FROM electronic_approval_setup WHERE company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0";*/

			$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted=0","seq");                      
        }
		
		if($user_sequence_no==$min_sequence_no && $approval_necessity_setup==1) // First user
		{
            $buyer_ids = $buyer_ids_array[$user_id]['u'];
			if($buyer_ids == "") $buyer_id_cond = ""; else $buyer_id_cond = " and a.buyer_id in($buyer_ids)";			
		 
			$sql= "SELECT a.id,a.booking_no_prefix_num as system_no,a.booking_no,a.buyer_id as buyer_name,a.is_approved,'0' as approval_id,
			a.job_no as po_job_no,b.style_ref_no as style_ref from wo_booking_mst a, wo_po_details_master b  
			where a.job_no = b.job_no and a.booking_type=3 and a.company_id = $company_name and  a.status_active=1 and a.is_deleted=0 and a.process=1  and a.ready_to_approved = 1 and a.is_approved=$approval_type $where_id_cond $buyer_id_cond $buyer_id_cond2 $buyerIds_cond order by a.booking_no_prefix_num";		 
		 
		   //echo $sql;
        }
		else if($sequence_no == "" && $approval_necessity_setup==1) // Next user // bypass if previous user Yes
		{
			// echo "bypass Yes";die;
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

				$booking_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as master_id","wo_booking_mst a, approval_history b",
				"a.id=b.mst_id and a.company_id=$company_name  and b.sequence_no=$user_sequence_no and b.entry_form=29 and
				b.current_approval_status=1","master_id");
			}
			else
			{
				$seqSql="select LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no_by, LISTAGG(case when bypass=2 and buyer_id is not null then buyer_id end, ',') WITHIN GROUP (ORDER BY null) as buyer_ids from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
				$seqData=sql_select($seqSql);
				
				$sequence_no_by=$seqData[0][csf('sequence_no_by')];
				$buyerIds=$seqData[0][csf('buyer_ids')];
				
				if($buyerIds == "") $buyerIds_cond = ""; else $buyerIds_cond = " and a.buyer_id not in($buyerIds)";

				$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as master_id","wo_booking_mst a,
				approval_history b","a.id=b.mst_id and a.company_id=$company_name and b.sequence_no=$user_sequence_no and b.entry_form=29 and
				b.current_approval_status=1","master_id");
				$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));				
			} 
			$booking_id_cond="";
			if($booking_id_app_byuser!="") $booking_id_cond=" and a.id not in($booking_id_app_byuser)";
			// echo $booking_id_cond;die;

			$sql= "SELECT a.id, b.style_ref_no as style_ref,a.booking_no_prefix_num as system_no,a.booking_no,a.buyer_id as buyer_name, a.job_no as po_job_no, a.is_approved
			from wo_booking_mst a, wo_po_details_master b  
			where a.job_no=b.job_no and a.booking_type=3 and a.company_id = $company_name and  a.status_active=1 and a.is_deleted=0 and a.process=1 and a.ready_to_approved=1 and a.is_approved in(0,3) $buyer_id_cond $where_id_cond $buyer_id_cond2 $buyerIds_cond $booking_id_cond
			order by a.booking_no_prefix_num";	
			// echo $sql;die;		 
		}
		else // bypass No
		{
			// echo "bypass No";die;
            $buyer_ids = $buyer_ids_array[$user_id]['u'];
			if($buyer_ids == "") { 
                $buyer_id_cond="";                 
            } else {
                $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
            }
                      
			$user_sequence_no=$user_sequence_no-1;
			
            $sequence_no_by_pass=''; // understand 
			if($sequence_no == $user_sequence_no && $approval_necessity_setup==1) 
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
                             

				$sql= "SELECT a.id,a.booking_no_prefix_num as system_no,a.booking_no,a.buyer_id as buyer_name,a.is_approved,c.id as approval_id, a.job_no as po_job_no,b.style_ref_no as style_ref 
				from wo_booking_mst a, wo_po_details_master b, approval_history c  
				where a.id=c.mst_id and c.entry_form=29 and c.current_approval_status=1 and a.job_no=b.job_no and a.booking_type=3 and a.company_id=$company_name and a.status_active=1 and a.is_deleted=0 and a.process=1 and a.ready_to_approved=1 and a.is_approved=3 $buyer_id_cond $where_id_cond $buyer_id_cond2 $sequence_no_cond 
				order by a.booking_no_prefix_num";		 
			} 
			else 
			{
                                
				if($approval_necessity_setup==1)
				{
					$sql= "SELECT a.id,a.booking_no_prefix_num as system_no,a.booking_no,a.buyer_id as buyer_name,a.is_approved,c.id as approval_id, a.job_no as po_job_no,b.style_ref_no as style_ref 
					from wo_booking_mst a, wo_po_details_master b, approval_history c  
					where a.id=c.mst_id and c.entry_form=29 and c.current_approval_status=1 and a.job_no=b.job_no and a.booking_type=3 and a.company_id=$company_name and a.status_active=1 and a.is_deleted=0 and a.process=1  and a.ready_to_approved = 1 and a.is_approved=3 $buyer_id_cond $where_id_cond $buyer_id_cond2 $sequence_no_cond 
					order by a.booking_no_prefix_num";
				}
			
		    }
		} 

       // Un-Approve End       
	}
	else // approval process start
	{
		$buyer_ids = $buyer_ids_array[$user_id]['u'];
		if ($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
		$sequence_no_cond=" and c.approved_by='$user_id'";

			$sql= "SELECT a.id,a.booking_no_prefix_num as system_no,a.booking_no,a.buyer_id as buyer_name,a.is_approved,c.id as approval_id, a.job_no as po_job_no,b.style_ref_no as style_ref 
			FROM wo_booking_mst a, wo_po_details_master b, approval_history c  
			WHERE a.id=c.mst_id and c.entry_form=29 and c.current_approval_status=1 and a.job_no=b.job_no and a.booking_type=3 and a.company_id=$company_name and a.status_active=1 and a.is_deleted=0 and a.process=1 and a.ready_to_approved=1 and a.is_approved in(1,3) $buyer_id_cond $where_id_cond $buyer_id_cond2 $sequence_no_cond
			ORDER by a.booking_no_prefix_num";
    }
	
	// echo $sql;
	
	
	$fset=850;
	$table1=800; 
    

 		$print_report_format_ids=return_field_value("format_id","lib_report_template","template_name=".$company_name."  and module_id=2 and report_id=12 and is_deleted=0 and status_active=1");
		$format_ids=explode(",",$print_report_format_ids);

	
    ?>
    
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:<? echo $fset; ?>px;">
        <legend>GSD Approval</legend>
        <div style="width:<? echo $table1+25; ?>px; margin:0 auto;">
        	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="left" >
                <thead>
                    <th width="25"></th>                   
                    <th width="50">SL</th>                   
                    <th width="100">System No</th>
                    <th width="125"> Booking No </th>
                    <th width="125">Job No</th>
                    <th width="225">Style No</th>
                    <th width="145">Buyer</th>                    
                </thead>
            </table>            
            <div style="min-width:<? echo $table1+2; ?>px; float:left; overflow-y:scroll; max-height:330px;" id="buyer_list_view">
                <table cellspacing="0" cellpadding="0" border="1" rules="all"  class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
                        <?		 
                        $i=1; $all_approval_id='';
                        $nameArray=sql_select( $sql );
                        foreach ($nameArray as $row)
                        {                                                   
                            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                            $value='';
								if($approval_type==0)
								{
									$value=$row[csf('id')];
								}
								else
								{
									if($db_type==0)
									{
									$app_id=return_field_value("max(id) as id","approval_history","mst_id ='".$row[csf('id')]."' and entry_form='29' and approved_by = '$user_id' order by id desc limit 0,1","id");
									}
									if($db_type==2 || $db_type==1) 
									{
									$app_id=return_field_value("max(id) as id","approval_history","mst_id ='".$row[csf('id')]."' and entry_form='29' and approved_by = '$user_id' ","id"); //and ROWNUM=1
									}
									$value=$row[csf('id')]."**".$app_id;
								}
							
							if($format_ids[0]==13){$action="show_trim_booking_report";}
							else if($format_ids[0]==12){$action="show_trim_booking_report1";}
							else if($format_ids[0]==15){$action="show_trim_booking_report2";}
							else if($format_ids[0]==16){$action="show_trim_booking_report3";}
							else{$action="show_trim_booking_report3";}
							
							
							
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                                    <td width="25" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" name="tbl[]" onClick="check_last_update(<? echo $i;?>);" />
                                        <input type="hidden" id="target_id_<? echo $i;?>" name="target_id_[]"  value="<? echo $value; ?>" />                                                  
                                        <input type="hidden" id="approval_id_<? echo $i;?>" name="approval_id[]"  value="<? echo $row[csf('id')]; ?>" />
                                   </td> 
                                    <td width="50" align="center"><p><?php echo $i;?>&nbsp;</p></td>
                                    <td width="100"><p><? echo $row[csf('system_no')]; ?>&nbsp;</p></td>
                                    <td width="125" align="center"><p><a href="javascript:generate_trim_report('<? echo $action;?>','<? echo $row[csf('booking_no')].'**'.$company_name.'**'.$row[csf('is_approved')]; ?>')"><? echo $row[csf('booking_no')]; ?></a></p></td>
                                    <td width="125" align="center"><p><a href="javascript:generate_fabric_report('show_fabric_booking_report4','<? echo $row[csf('po_job_no')]; ?>')"><? echo $row[csf('po_job_no')]; ?></a></p></td>
                                    <td width="225"><p><? echo $row[csf('style_ref')]; ?>&nbsp;</p></td>								
                                    <td width="125"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?> &nbsp;</p></td>                                  
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
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="<? echo $table1+3; ?>" class="rpt_table" align="left">
				<tfoot>
                    <td width="25" align="center" >
                    	<input type="checkbox" id="all_check" onClick="check_all('all_check')" />
                        <input type="hidden" name="hide_approval_type" id="hide_approval_type" value="<? echo $approval_type; ?>">
                        <font style="display:none"><? echo $all_approval_id; ?></font>
                    </td>
                    <td align="left">
                        &nbsp;<input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/>
                    </td>
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
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	$msg=''; $flag=''; $response='';
	
	if($approval_type==0)
	{
 		$response=$target_ids;        
        $user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");

        $is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");
        /*echo "SELECT sequence_no from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0"; die;*/

        if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;
        // echo $partial_approval;die;

        $approved_no_array=array();
        
        $id=return_next_id( "id","approval_history", 1 ) ;
		$target_app_ids=explode(",",$target_ids);		
		$i=0;  
        
		foreach($target_app_ids as $val)
		{		
            $approved_no=return_field_value("max(approved_no)","approval_history","mst_id='$val'");
			$approved_no=$approved_no+1;
            $approved_no_array[$val]=$approved_no;
            
			if($i!=0) $data_array.=",";
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status,approved_by, approved_date,user_ip"; 
			$data_array.="(".$id.",29,".$val.",".$approved_no.",'".$user_sequence_no."',1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
			$id=$id+1;
			$i++;
		}
		
		
		
        /*$rID = sql_multirow_update("wo_booking_mst","is_approved",1,"id",$target_ids,0); 
		if($rID) $flag=1; else $flag=0;*/

		$rID=sql_multirow_update("wo_booking_mst","is_approved",$partial_approval,"id",$target_ids,0);    
        if($rID) $flag=1; else $flag=0;

        $query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=29 and mst_id in ($target_ids)";
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

		// echo "21**".$rID.'**'.$rID2;die;

       if($flag==1) $msg='19'; else $msg='21';
        
	}
	else
	{ 
		$target_id = explode(',',$target_ids); 
        // print_r($target_ids);die;
        $target_ids=''; $approval_ids='';
        
        foreach($target_id as $value)
        {
            $data = explode('**',$value);
            $target_id=$data[0];
            $approval_id=$data[1];
            
            if($target_ids=='') $target_ids=$target_id; else $target_ids.=",".$target_id;
            if($approval_ids=='') $approval_ids=$approval_id; else $approval_ids.=",".$approval_id;
        }
        // echo $approval_ids;die;
		$rID=sql_multirow_update("wo_booking_mst","is_approved*ready_to_approved","0*0","id",$target_ids,0); 

        if($rID) $flag=1; else $flag=0;

		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=29 and mst_id in ($target_ids)";
		$rID2=execute_query($query,1); 
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		}

		$data=$user_id."*'".$pc_date_time."'";
		$rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date",$data,"id",$approval_ids,1);
		
        if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		
		$response=$target_ids;
		
		if($flag==1) $msg='20'; else $msg='22';    
	}
	
	
	// echo "10**$rID**$rID2**$rID3";die;
	
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