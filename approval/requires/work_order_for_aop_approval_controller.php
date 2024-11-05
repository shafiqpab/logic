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

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$sequence_no = '';
	$company_name = str_replace("'","",$cbo_company_name);
	$txt_booking_no = str_replace("'","",$txt_booking_no);
	$txt_date = str_replace("'","",$txt_date);
	$txt_alter_user_id = str_replace("'","",$txt_alter_user_id);
	$user_id=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;
	
	/*if(str_replace("'","",$cbo_buyer_name)==0)
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
	}*/

	$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
	// if($cbo_buyer_id==0){$cbo_buyer_id="'%%'";}

	$approval_type = str_replace("'","",$cbo_approval_type);
    $user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0","seq");	
	
	$approval_necessity_setup=return_field_value("approval_need","approval_setup_mst a,approval_setup_dtls b","a.id=b.mst_id and a.company_id=$company_name and b.page_id=27 and a.status_active=1 and a.is_deleted=0  order by a.setup_date desc","approval_need");	
	
	//echo $approval_necessity_setup;
	
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

	$where_id_cond = '';
	if($txt_booking_no!='') 
	{		
		$where_id_cond.=" and a.booking_no_prefix_num ='".$txt_booking_no."'"; 
	}
	
	if($txt_date!='') 
	{		
		$where_id_cond.=" and a.booking_date='$txt_date'"; 
	}
	$buyer_id_cond = '';
	if($cbo_buyer_name != 0){
		$buyer_id_cond .= " and a.buyer_id =$cbo_buyer_name";
	}

	// echo $where_id_cond;die();
	
	if($approval_type==0) // Un-Approve
	{        
		
        if($db_type==0)
		{
			$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id='' and is_deleted=0","seq");
		}
		else
		{
			$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");                      
        }
		// echo $user_sequence_no.'=='.$min_sequence_no;
		if($user_sequence_no==$min_sequence_no) //  && $approval_necessity_setup==1
		{ 				
            $buyer_ids = $buyer_ids_array[$user_id]['u'];
			if(trim($buyer_ids) == "") $buyer_id_cond = ""; else $buyer_id_cond = " and a.buyer_id in($buyer_ids)";			
           	              
		 
			$sql= "SELECT a.id,a.booking_no_prefix_num as system_no,a.booking_no,a.buyer_id as buyer_name, a.supplier_id as supplier_name, a.is_approved,'0' as approval_id,
			a.job_no as po_job_no, a.booking_date, a.delivery_date ,a.tagged_booking_no
			FROM wo_booking_mst a
			WHERE a.booking_type=3 and a.company_id = $company_name and  a.status_active=1 and a.is_deleted=0 and a.process=35  and a.ready_to_approved = 1 and a.is_approved=$approval_type  $buyer_id_cond $where_id_cond
			
			order by a.booking_no_prefix_num";
		 
		   // echo $sql;
        }
		else if($sequence_no == "") //  && $approval_necessity_setup==1
		{               
            $buyer_ids=$buyer_ids_array[$user_id]['u'];
			if(trim($buyer_ids) == "") $buyer_id_cond = ""; else $buyer_id_cond = " and a.buyer_id in($buyer_ids)";
			
			if($db_type == 0)
			{
				$seqSql="SELECT group_concat(sequence_no) as sequence_no_by, group_concat(case when bypass=2 and buyer_id<>'' then buyer_id end) as buyer_ids 
 					FROM electronic_approval_setup 
 					WHERE company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
				$seqData=sql_select($seqSql);
				
				$sequence_no_by = $seqData[0][csf('sequence_no_by')];
				$buyerIds = $seqData[0][csf('buyer_ids')];
				
				if($buyerIds == "") $buyerIds_cond = ""; else $buyerIds_cond = " and a.buyer_id not in($buyerIds)";
			}
			else
			{   
				$seqSql="SELECT LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no_by, LISTAGG(case when bypass=2 and buyer_id is not null then buyer_id end, ',') WITHIN GROUP (ORDER BY null) as buyer_ids 
				FROM electronic_approval_setup 
				WHERE company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
				$seqData=sql_select($seqSql);
				
				$sequence_no_by=$seqData[0][csf('sequence_no_by')];
				$buyerIds=$seqData[0][csf('buyer_ids')];
				
				if($buyerIds == "") $buyerIds_cond = ""; else $buyerIds_cond = " and a.buyer_id not in($buyerIds)";				
			}

			
			
			$sql= "SELECT a.booking_no_prefix_num as system_no,a.booking_no,a.buyer_id as buyer_name,  a.supplier_id as supplier_name, a.job_no as po_job_no , a.booking_date, a.delivery_date,a.tagged_booking_no
			FROM wo_booking_mst a 
			WHERE  a.booking_type=3 and a.company_id = $company_name and  a.status_active=1 and a.is_deleted=0 and a.process=35  and a.ready_to_approved = 1 and a.is_approved=$approval_type  $buyer_id_cond $where_id_cond  order by a.booking_no_prefix_num";	 
			

		}
		else
		{   
            $buyer_ids = $buyer_ids_array[$user_id]['u'];
			if(trim($buyer_ids) == "") { 
                $buyer_id_cond="";                 
            } else {
                $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
            }
                      
			$user_sequence_no=$user_sequence_no-1;
			
            $sequence_no_by_pass=''; // understand 
			if($sequence_no == $user_sequence_no) //  && $approval_necessity_setup==1
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
                             

				$sql= "SELECT a.id,a.booking_no_prefix_num as system_no,a.booking_no,a.buyer_id as buyer_name,  a.supplier_id as supplier_name, a.is_approved,c.id as approval_id, a.job_no as po_job_no,a.booking_date, a.delivery_date,a.tagged_booking_no
				FROM wo_booking_mst a,  approval_history c  
				WHERE a.id=c.mst_id and c.entry_form=162 and c.current_approval_status=1 and  a.booking_type=3 and a.company_id = $company_name and  a.status_active=1 and a.is_deleted=0 and a.process=35  and a.ready_to_approved = 1 and a.is_approved=1  $where_id_cond  $sequence_no_cond order by a.booking_no_prefix_num";		 
			} 
			else 
			{         
                                
				//if($approval_necessity_setup==1){

				$sql= "SELECT a.id,a.booking_no_prefix_num as system_no,a.booking_no,a.buyer_id as buyer_name,  a.supplier_id as supplier_name, a.is_approved,c.id as approval_id, a.job_no as po_job_no, a.booking_date, a.delivery_date,a.tagged_booking_no
				FROM wo_booking_mst a, approval_history c  
				WHERE a.id=c.mst_id and c.entry_form=162 and c.current_approval_status=1 and a.booking_type=3 and a.company_id = $company_name and  a.status_active=1 and a.is_deleted=0 and a.process=35  and a.ready_to_approved = 1 and a.is_approved=1  $where_id_cond  $sequence_no_cond order by a.booking_no_prefix_num";
				//}
			
		    }
		} 

       // Un-Approve End       
	}
	else
	{
		$buyer_ids = $buyer_ids_array[$user_id]['u'];
		if (trim($buyer_ids)=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
		$sequence_no_cond=" and c.approved_by='$user_id'";
    	                           
   
			$sql= "SELECT a.id,a.booking_no_prefix_num as system_no,a.booking_no,a.buyer_id as buyer_name,  a.supplier_id as supplier_name, a.is_approved,c.id as approval_id, a.job_no as po_job_no,a.booking_date, a.delivery_date,a.tagged_booking_no
				FROM wo_booking_mst a, approval_history c  
				WHERE a.id=c.mst_id and c.entry_form=162 and c.current_approval_status=1 and a.booking_type=3 and a.company_id = $company_name and  a.status_active=1 and a.is_deleted=0 and a.process=35  and a.ready_to_approved = 1 and a.is_approved=1 $where_id_cond  $sequence_no_cond order by a.booking_no_prefix_num";		 
    }
	
	//echo $sql;
	$fset=905;
	$table1=905;    

	// $print_report_format_ids=return_field_value("format_id","lib_report_template","template_name=".$company_name."  and module_id=2 and report_id=12 and is_deleted=0 and status_active=1");
	// $format_ids=explode(",",$print_report_format_ids);

	$print_report_format=return_field_value("format_id"," lib_report_template","template_name =".$company_name."   and module_id=2 and report_id=49 and is_deleted=0 and status_active=1");

	$report_ids=explode(",",$print_report_format);




    ?>
    
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:<? echo $fset; ?>px;">
        <legend>Work Order for AOP Approval</legend>
        <div style="width:<? echo $table1+25; ?>px; margin:0 auto;">
        	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="left" >
                <thead>
                    <th width="25"></th>                   
                    <th width="50">SL</th>                   
                    <th width="80">System No</th>
                    <th width="125"> Booking No </th>
                    <th width="125">Job No</th>
                    <th width="150">Buyer</th> 
                    <th width="150">Supplier</th>                   
                    <th width="100">Booking Date</th>                   
                    <th width="100">Delivery Date</th>                   
                </thead>
            </table>            
            <div style="min-width:<? echo $table1+20; ?>px; float:left; overflow-y:auto; max-height:330px;" id="buyer_list_view">
                <table cellspacing="0" cellpadding="0" border="1" rules="all"  class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
                        <?		 
                        $i=1; $all_approval_id='';

						
                        $nameArray=sql_select( $sql );
                        foreach ($nameArray as $row)
                        {                                                   
                            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";

							/// echo $report_ids[0]."_________";
							

							if($report_ids[0]==163){ $action="show_trim_booking_report1";}
							else if($report_ids[0]==164){ $action="show_trim_booking_report2";}
							else if($report_ids[0]==16){ $action="show_trim_booking_report3";}
							else if($report_ids[0]==177){ $action="show_trim_booking_report4";}
							else if($report_ids[0]==176){ $action="show_trim_booking_report6";}
							else if($report_ids[0]==288){ $action="show_trim_booking_report5";}
							else{	$action="show_work_order_aop_report";}


									
							
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                                    <td width="25" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" name="tbl[]" onClick="check_last_update(<? echo $i;?>);" />
                                        <input type="hidden" id="target_id_<? echo $i;?>" name="target_id_[]"  value="<? echo $row[csf('id')]; ?>" />                                                  
                                        <input type="hidden" id="approval_id_<? echo $i;?>" name="approval_id[]"  value="<? echo $row[csf('approval_id')]; ?>" />
                                   </td> 
                                    <td width="50" align="center"><p><?php echo $i;?>&nbsp;</p></td>
                                    <td width="80"><p><? echo $row[csf('system_no')]; ?>&nbsp;</p></td>
                                    <td width="125" align="center"><a href='##' style='color:#000' onClick="generate_order_report('<? echo $row[csf('booking_no')]; ?>',<? echo $company_name; ?>,'<? echo $row[csf('is_approved')]; ?>','<? echo $row[csf('entry_form')]; ?>','<? echo $report_ids[0]; ?>','<? echo $row[csf('tagged_booking_no')]; ?>','<? echo $action; ?>')"><font color="blue"><b><? echo $row[csf('booking_no')]; ?></b></font></a></td>                                    
                                    <td width="125" align="center"><p><? echo $row[csf('po_job_no')]; ?>&nbsp;</p></td>	
                                    <td width="150"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?> &nbsp;</p></td>
                                    <td width="150"><p><? echo $supplier_arr[$row[csf('supplier_name')]]; ?>&nbsp;</p></td>				
                                    <td width="100"><p><? echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</p></td>
                                    <td width="100"><p><? echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</p></td>				                                  
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
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="<? echo $table1+25; ?>" class="rpt_table" align="left">
				<tfoot>
                    <td width="25" align="center" >
                    	<input type="checkbox" id="all_check" onClick="check_all('all_check')" />
                        <input type="hidden" name="hide_approval_type" id="hide_approval_type" value="<? echo $approval_type; ?>">
                        <font style="display:none"><? echo $all_approval_id; ?></font>
                    </td>
                    <td align="left">
                        &nbsp;<input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/>
						<input type="button" value="Deny" class="formbutton" style="width:100px;<?= ($approval_type==1)?' display:none':''; ?>" onClick="submit_approved(<?=$i; ?>,5);"/>
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
	
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$approved_user_id=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;
	
	
	if($approval_type==0)
	{   
 		$response=$target_ids;        
        $user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$approved_user_id and is_deleted=0");

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
			$data_array.="(".$id.",162,".$val.",".$approved_no.",'".$user_sequence_no."',1,".$approved_user_id.",'".$pc_date_time."','".$user_ip."')"; 
			$id=$id+1;
			$i++;
		}
		
		
		
        $rID = sql_multirow_update("wo_booking_mst","is_approved",1,"id",$target_ids,0); 
		if($rID) $flag=1; else $flag=0;

        $rID2=sql_insert("approval_history",$field_array,$data_array,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		}

       if($flag==1) $msg='19'; else $msg='21';
        
	}

	else if($approval_type==5)
	{
		$sqlBookinghistory="select id, mst_id from approval_history where current_approval_status=1 and entry_form=162 and mst_id in ($target_ids) ";
		//echo "10**".$sqlBookinghistory;
		$nameArray=sql_select($sqlBookinghistory); $bookidstr=""; $approval_id="";
		foreach ($nameArray as $row)
		{
			if($bookidstr=="") $bookidstr=$row[csf('mst_id')]; else $bookidstr.=','.$row[csf('mst_id')];
			if($approval_id=="") $approval_id=$row[csf('id')]; else $approval_id.=','.$row[csf('id')];
		}
		
		$appBookNoId=implode(",",array_filter(array_unique(explode(",",$bookidstr))));
		$approval_ids=implode(",",array_filter(array_unique(explode(",",$approval_id))));
		/*$book_ids=count(explode(",",$bookidstr)); $bookingidCond="";
		if($db_type==2 && $book_ids>1000)
		{
			$bookingidCond=" and (";
			$bookingnoIdArr=array_chunk(explode(",",$booknoId),999);
			foreach($bookingnoIdArr as $ids)
			{
				$ids=implode(",",$ids);
				$bookingidCond.=" mst_id in($ids) or"; 
			}
			$bookingidCond=chop($bookingidCond,'or ');
			$bookingidCond.=")";
		}
		else $bookingidCond=" and mst_id in($booknoId)";*/ 
		
		$rID=sql_multirow_update("wo_booking_mst","is_approved*ready_to_approved","0*0","id",$target_ids,0);
		if($rID) $flag=1; else $flag=0;

		//$rID2=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,0);
		if($approval_ids!="")
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$approved_user_id."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=162 and current_approval_status=1 and id in ($approval_ids)";
			//echo "10**".$query;
			$rID2=execute_query($query,1);
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;
			}
		}
		//echo "10**".$rID.'='.$rID2.'='.$rID4.'='.$flag; die;
		$response=$target_ids;
		if($flag==1) $msg='50'; else $msg='51';
	}
	else
	{              
		
		
		$rID=sql_multirow_update("wo_booking_mst","is_approved*ready_to_approved","0*0","id",$target_ids,0); 

        if($rID) $flag=1; else $flag=0;

		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=162 and mst_id in ($target_ids)";
		$rID2=execute_query($query,1); 
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		}

		$data=$approved_user_id."*'".$pc_date_time."'";
		$rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date",$data,"id",$approval_ids,1);
		
        if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		
		$response=$target_ids;
		
		if($flag==1) $msg='20'; else $msg='22';    
	}
	
	
	//echo "10**$rID**$rID2**$rID3";die;
	
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


if($action=='user_popup')
{
    echo load_html_head_contents("Popup Info","../../",1, 1,'',1,'');
    ?>  
    <script>
      function js_set_value(id)
      { 
        document.getElementById('selected_id').value=id;
      	parent.emailwindow.hide();
      }
    </script>

    <form>
            <input type="hidden" id="selected_id" name="selected_id" /> 
           <?php
            $custom_designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');  
             $Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');   ;
             $sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and valid=1 and a.id!=$user_id  and b.is_deleted=0";
                //echo $sql;
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


if ($action=="show_work_order_aop_report")
{
	// var_dump($_REQUEST);die();
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1 and master_tble_id='$cbo_company_name'",'master_tble_id','image_location');
	
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$com_supplier_id=return_field_value( "supplier_id as supplier_id", "wo_booking_mst","booking_no=$txt_booking_no","supplier_id");
	$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=162"); 
	list($nameArray_approved_row)=$nameArray_approved;
	
	//==================================================================	


	?>
	<div style="width:1540px; margin: 0 auto" align="center">       
    										<!--    Header Company Information         --> 
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100"> 
               <img  src='../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1000">                                     
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php      
                                    echo $company_arr[$cbo_company_name];
                              ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name"); 
							//echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name";
                            foreach ($nameArray as $result)
                            { 
                            	?>
                                Plot No: <? echo $result[csf('plot_no')]; ?> 
                                Level No: <? echo $result[csf('level_no')]?>
                                Road No: <? echo $result[csf('road_no')]; ?> 
                                Block No: <? echo $result[csf('block_no')];?> 
                                City No: <? echo $result[csf('city')];?> 
                                Zip Code: <? echo $result[csf('zip_code')]; ?> 
                                Province No: <?php echo $result[csf('province')];?> 
                                Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
                                Email Address: <? echo $result[csf('email')];?> 
                                Website No: <? echo $result[csf('website')];
											
                            }

							$sup_addres=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$com_supplier_id");  	
							foreach ($sup_addres as $row)
                            { 
				 				if($row[csf('plot_no')]!='') $plot_no=$row[csf('plot_no')].',';
								if($row[csf('level_no')]!='') $level_no=$row[csf('level_no')].',';
								if($row[csf('road_no')]!='') $road_no=$row[csf('road_no')].',';
								if($row[csf('block_no')]!='') $road_no=$row[csf('block_no')].',';
								if($row[csf('block_no')]!='') $road_no=$row[csf('block_no')].',';
								if($row[csf('country_id')]!=0) $country_name=$country_arr[$row[csf('country_id')]].',';
								if($row[csf('block_no')]!='') $block_no=$row[csf('block_no')].',';
								if($row[csf('province')]!='') $province=$row[csf('province')].',';
								if($row[csf('city')]!='') $city=$row[csf('city')].',';
								if($row[csf('zip_code')]!='') $zip_code=$row[csf('zip_code')].',';
								if($row[csf('email')]!='') $email=$row[csf('email')].',';
								if($row[csf('website')]!='') $website=$row[csf('website')];
								
								$company_address=$plot_no.$level_no.$road_no.$country_name.$block_no.$province.$city.$zip_code.$email.$website;
							}
                                ?>   
                                         
                               </td> 
                            </tr>
                            <tr>
                             
                            <td align="center" style="font-size:20px">  
                                <strong><? if($report_title !=""){echo $report_title;} else {echo "Work Order for AOP Approval";}?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
                             </td>
                             
                             <td style="font-size:20px"> 
                              <?
								 if($nameArray_approved_row[csf('approved_no')]>1)
								 {
								 ?>
								 <strong> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></strong>
                                  <br/>
								 
								  <?
								 }
							  	?>
                             </td>
                              
                            </tr>
                      </table>
                </td>
                      
            </tr>
       </table>
       <?
                
		$fabric_source='';
		$booking_info=sql_select( "select id,buyer_id,booking_no,booking_date,supplier_id,exchange_rate,currency_id,attention,delivery_date,fabric_source,entry_form from wo_booking_mst where booking_no=$txt_booking_no"); 

		foreach ($booking_info as $result)
		{
			$fabric_source=$result[csf('fabric_source')];
			
			$varcode_booking_no=$result[csf('booking_no')];
							
			?>
		   <table width="100%" style="border:1px solid black">                    	
		        <tr>
		            <td colspan="6" valign="top"></td>                             
		        </tr>                                                
		        <tr>
		            <td width="100" style="font-size:12px"><b>Booking No </b>   </td>
		            <td width="110">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
		            <td width="100" style="font-size:12px"><b>Booking Date</b></td>
		            <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>		
		            <td width="100"><span style="font-size:12px"><b>Delivery Date</b></span></td>
		            <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>	
		           			
		        </tr>
		        <tr>
		            
		            <td width="100"><span style="font-size:12px"><b>Buyer Name</b></span></td>
		            <td width="110">:&nbsp;<? echo $buyer_name_arr[$result[csf('buyer_id')]]; ?></td>
		            <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
		            <td width="110">:&nbsp;<? 
					if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3)
					{
						echo $company_library[$result[csf('supplier_id')]];
					}
					else
					{
						echo $supplier_name_arr[$result[csf('supplier_id')]];
					}
					?>    </td>
		            <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
		           	<td width="110">:&nbsp;
					<? 
					if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3)
					{
						echo $company_address;
					}
					else
					{
						echo $supplier_address_arr[$result[csf('supplier_id')]];
					}
					?>
		            
		            </td> 
		        </tr>
		        
		        
		        <tr>
		            <td width="100" style="font-size:12px"><b>Currency</b></td>
		            <td width="110">:&nbsp;<? echo $currency[$result[csf('currency_id')]]; ?></td>
		         
		            <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
		            <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
		            <td  width="100" style="font-size:12px"><b>Attention</b></td>
		            <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
		           
		        </tr> 
		    </table>  
		    <?
		}
		?>
            
      <br/>  
      <? 
	  
	if($db_type==0)
	{
		$sql= sql_select("select a.booking_no, a.booking_type, a.booking_date, a.delivery_date, a.buyer_id, a.supplier_id, a.job_no, a.item_category, a.source, a.attention, a.process, b.dia_width, b.uom, a.pay_mode, a.exchange_rate, a.currency_id, b.wo_qnty, b.amount, b.pre_cost_remarks FROM wo_booking_mst a, wo_booking_dtls b  WHERE a.booking_no=$txt_booking_no and a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 order by a.id"); 
	}
	if($db_type==2)
	{
		$sql= sql_select("select a.booking_no, a.booking_type, a.booking_date, a.delivery_date, a.buyer_id, a.supplier_id, a.job_no, a.item_category, a.source, a.attention, a.process, b.dia_width, b.uom, a.pay_mode, a.exchange_rate, a.currency_id, b.wo_qnty, b.amount, b.pre_cost_remarks FROM wo_booking_mst a, wo_booking_dtls b  WHERE a.booking_no=$txt_booking_no and a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 order by a.id"); 

	}
	
	?>
	<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
		<thead>
			<th width="50">Sl</th>
			<th width="150">Booking No</th>
			<th width="100">Booking Type</th>
			<th width="100">Booking Date</th>
			<th width="100">Delivery Date</th>
			<th width="130">Buyer</th>
			<th width="130">Supplier</th>
			<th width="100">Job No</th>
			<th width="100">Item Category</th>
			<th width="80">Source</th>
			<th width="50">Attention</th>
			<th width="50">Process</th>
			<th width="50">Dia/ Width</th>
			<th width="60">UOM</th>
			<th width="50">Pay Mood</th>
			<th width="80">Currency</th>
			<th width="50">Ex. Rate</th>
			<th width="60">Quantity</th>	
			<th width="80">Amount</th>
			<th width="100">Remarks</th>
		</thead>
	<?

	$toatl_quattity=0;
	$total_amount=0;

	$i=1;
	foreach ($sql as $row)
	{		
		?>
		<tr>
		<td width="50"><? echo $i; ?></td>
		<td width="150"><? echo $row[csf('booking_no')]; ?></td>
		<td width="100"><? echo $row[csf('booking_type')]; ?></td>
		<td width="100"><? echo $row[csf('booking_date')]; ?></td>
		<td width="100"><? echo $row[csf('delivery_date')]; ?></td>
		<td width="130"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
		<td width="130"><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></td>
		<td width="100"><? echo $row[csf('job_no')]; ?></td>
		<td width="100"><? echo $row[csf('item_category')]; ?></td>
		<td width="80" align="center"><? echo $row[csf('source')]; ?></td>
		
		<td width="50" align="center"><? echo $row[csf('attention')]; ?></td>
		<td width="50" align="center"><? echo $row[csf('process')]; ?></td>
		<td width="50" align="center"><? echo $row[csf('dia_width')]; ?></td>
		<td width="60" align="center"><? echo $row[csf('uom')]; ?></td>
		<td width="50" align="center"><? echo $pay_mode[$row[csf('pay_mode')]]; ?></td>

		<td width="80" align="center"><? echo $currency[$row[csf('currency_id')]]; ?></td>
		<td width="50" align="right"><? echo number_format($row[csf('exchange_rate')],2);?></td>
		<td width="60" align="right"><? echo number_format($row[csf('wo_qnty')],2); ?></td>
		<td width="80" align="right"><? echo number_format($row[csf('amount')],2); ?></td>
		<td width="100"><? echo $row[csf('pre_cost_remarks')]; ?></td>
		
		</tr>
		<?
		$i++;
		$toatl_quattity += $row[csf('wo_qnty')];
		$total_amount += $row[csf('amount')];
	}
	?>
		<tr>
			<th width="50" colspan="17" align="right">Total </th>
			
			<th width="60" align="right"><? echo number_format($toatl_quattity,2);?></th>
			<th width="80" align="right"><? echo number_format($total_amount,2); ?></th>
			<th width="100" align="right"><? ?></th>
		</tr>
	</table>
        
        <br/> 

        <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="100%">
                    <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
                	<thead>
                    <tr>
                        	<th width="30" colspan="4" align="center">Approval Status</th>
                            
                        </tr>
                    	<tr>
                        	<th width="30">Sl   <? $bookingId=$nameArray[0][csf('id')]?> </th>
                            <th width="250">Name/Designation</th>
                            <th width="150">Approval Date</th>
                            <th width="80">Approval No</th>
                             
                        </tr>
                    </thead>
                    <tbody>

                    <?

 					$user_arr=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
 					$desg_arr=return_library_array( "select id, designation from user_passwd", "id", "designation"  );
 					$desg_name=return_library_array( "select id, custom_designation from lib_designation", "id", "custom_designation"  );
 					$sel=sql_select("select a.approved_by, a.approved_date, a.id as app_no from approval_history a, electronic_approval_setup b where a.mst_id in(select id from wo_booking_mst where id=$bookingId) and a.entry_form=162 and a.approved_by=b.user_id and b.company_id=$cbo_company_name and b.is_deleted=0 group by a.approved_by, a.approved_date, a.id, b.sequence_no order by b.sequence_no asc");
					//echo "select a.approved_by, a.approved_date, a.id as app_no from approval_history a, electronic_approval_setup b where a.mst_id in(select id from wo_non_ord_samp_booking_mst where id=$bookingId) and a.entry_form=9 and a.approved_by=b.user_id and b.company_id=$cbo_company_name and b.is_deleted=0 group by a.approved_by, a.approved_date, a.approved_no, b.sequence_no order by b.sequence_no asc";
					$i=1;
					$approval_arr=array(); 
					$app_id_arr=array();
 					foreach ($sel as $rows) {
						$app_id_arr[$rows[csf('approved_by')]][$rows[csf('app_no')]]=$rows[csf('app_no')];
						$approval_arr[$rows[csf('approved_by')]]['date'].=$rows[csf('approved_date')].',';
					}
					
					foreach($approval_arr as $approved_by=>$val)
					{
						$app_date="";
						$exapp_date="";
						$exapp_date=array_filter(array_unique(explode(",",$val['date'])));
						foreach($exapp_date as $apdate)
						{
							if($app_date=="") $app_date=$apdate; else $app_date.=', '.$apdate;
						}
						$count_no=count($app_id_arr[$approved_by]);
						
						?>
						<tr id="settr_1" align="">
                            <td width="30"><? echo $i ?></td>
                            <td width="250"><? echo $user_arr[$approved_by]." /".$desg_name[$desg_arr[$approved_by]]; ?></td>
                            <td width="150"><? echo $app_date; ?></td>
                            <td width="80"><? echo $count_no; ?></td>
						</tr>
						<?
						$i++;
					}
					?>  
					
                </tbody>
                </table>
                </td>
                
            </tr>
        </table>
        
        <br/>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="100%">
                    <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
                	<thead>
                    	<tr>
                        	<th width="3%">Sl</th><th width="97%" align="left">Spacial Instruction</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" align="">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <? echo $row[csf('terms')]; ?>
                                    </td>
                                </tr>
                            <?
						}
					}
					?>
                </tbody>
                </table>
                </td>
                
            </tr>
        </table>
         
       </div>
		<script type="text/javascript" src="../js/jquery.js"></script>
        <script type="text/javascript" src="../js/jquerybarcode.js"></script>
        <script>
        fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
        </script>
       <?
	   exit();

}
?>