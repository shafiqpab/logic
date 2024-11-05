<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_ip=$_SESSION['logic_erp']['user_ip'];

extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');
//require_once('../../mailer/class.phpmailer.php');
//$from_mail="PLATFORM-ERP@fakir.app";
	
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
	exit();
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
		//$Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');	;
		$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$company_id  and b.is_deleted=0  and b.entry_form=65 order by b.sequence_no";
		 //echo $sql;die;
		$arr=array (2=>$custom_designation,3=>$department_arr);
		echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/fabric_service_booking_approval_controller", 'setFilterGrid("list_view",-1);' ) ;
		?>
	</form>
	<?
	exit();
}

 
function getSequence($parameterArr=array())
{
	$lib_buyer_id_string=implode(',',(array_keys($parameterArr['lib_buyer_arr']))); 

	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND entry_form = {$parameterArr['entry_form']} AND IS_DELETED = 0 order by SEQUENCE_NO";
	 //echo $sql;die;
	$sql_result=sql_select($sql);
	$dataArr=array();
	foreach($sql_result as $rows){
		if($rows['BUYER_ID']==''){$rows['BUYER_ID']=$lib_buyer_id_string;}
		
		$dataArr['sequ_by'][$rows['SEQUENCE_NO']]=$rows;
		$dataArr['user_by'][$rows['USER_ID']]=$rows;
		$dataArr['sequ_arr'][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
	}
	return $dataArr;
}

function getFinalUser($parameterArr=array())
{
	$lib_buyer_arr=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));
 
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND entry_form = {$parameterArr['entry_form']} AND IS_DELETED = 0  order by SEQUENCE_NO";
	   //echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){
		$userDataArr[$rows['USER_ID']]['BUYER_ID'] = $rows['BUYER_ID'];

		if($userDataArr[$rows['USER_ID']]['BUYER_ID']==''){
			$userDataArr[$rows['USER_ID']]['BUYER_ID']=$lib_buyer_arr;
		}
		
		$usersDataArr[$rows['USER_ID']]['BUYER_ID']=explode(',',$userDataArr[$rows['USER_ID']]['BUYER_ID']);
		$userSeqDataArr[$rows['USER_ID']]=$rows['SEQUENCE_NO'];	
	}

	$finalSeq=array();
	foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){
		
		foreach($userSeqDataArr as $user_id=>$seq){
			if( in_array($bbtsRows['buyer_id'],$usersDataArr[$user_id]['BUYER_ID']) || $bbtsRows['buyer_id']==0 ){
				$finalSeq[$sys_id][$user_id]=$seq;
			}
			
		}
	}
	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr);
}



if($action=="report_generate")
{ 
	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$supplier_arr=return_library_array( "select supplier_name, id from lib_supplier where is_deleted=0 and status_active=1", "id", "supplier_name"  );
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_name = str_replace("'","",$cbo_company_name);
	$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);	
	$txt_booking_no = str_replace("'","",$txt_booking_no);
	$cbo_year = str_replace("'","",$cbo_year);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	$approval_type = str_replace("'","",$cbo_approval_type);
	$alter_user_id = str_replace("'","",$txt_alter_user_id);
	$app_user_id = ($alter_user_id!='') ? $alter_user_id : $user_id;
	
	if($cbo_buyer_name > 0){$searchCon .=" and a.buyer_id=$cbo_buyer_name";}
	if($txt_booking_no != '') {$searchCon .=" and a.booking_no_prefix_num='$txt_booking_no'";}
	if($cbo_year != 0){$searchCon .=" and a.booking_year='$cbo_year'";}
	
	if ($txt_date_from != '' && $txt_date_to != '')
	{
        $txt_date_from = date("d-M-Y", strtotime($txt_date_from));
        $txt_date_to = date("d-M-Y", strtotime($txt_date_to));
        $searchCon .= " and a.booking_date between '$txt_date_from' and '$txt_date_to'";
	}

	$service_booking_print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$company_name."' and module_id=2 and report_id=195 and is_deleted=0 and status_active=1");	
    $item_format_ids=explode(",",$service_booking_print_report_format);
    $print_btn=$item_format_ids[0];
 
	$electronicDataArr=getSequence(array('company_id'=>$company_name,'entry_form'=>65,'user_id'=>$app_user_id,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0,'lib_department_id_arr'=>0));
 

	
	if($approval_type==0) // Un-Approve
	{		
		//Match data..................................
		if($electronicDataArr['user_by'][$app_user_id]['BUYER_ID']) 
		{
			$where_con .= " and a.buyer_id in(".$electronicDataArr['user_by'][$app_user_id]['BUYER_ID'].",0)";
			$electronicDataArr['sequ_by'][0]['BUYER_ID']=$electronicDataArr['user_by'][$app_user_id]['BUYER_ID'];
		}
		
		$data_mas_sql = "select a.id as ID, a.buyer_id as BUYER_ID from wo_booking_mst a where a.status_active=1 and a.is_deleted=0 and a.is_approved<>1 and a.booking_type=3 and a.item_category=12  and a.ready_to_approved=1 and a.company_id=$company_name $where_con $searchCon";
		//echo $data_mas_sql; die;
				 
		$tmp_sys_id_arr=array();
		$data_mas_sql_res=sql_select( $data_mas_sql );
			
		foreach ($data_mas_sql_res as $row)
		{ 
			for($seq=($electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO']-1);$seq>=0; $seq-- )
			{
				if((in_array($row['BUYER_ID'],explode(',',$electronicDataArr['sequ_by'][$seq]['BUYER_ID'])) || $row['BUYER_ID']==0) )
				{
					if($electronicDataArr['sequ_by'][$seq]['BYPASS']==1)
					{
						$tmp_sys_id_arr[$seq][$row['ID']]=$row['ID'];
					}
					else
					{
						$tmp_sys_id_arr[$seq][$row['ID']]=$row['ID'];
						break;
					}
				}
			}
		}
		//print_r($tmp_sys_id_arr);die;
		//..........................................Match data;
	
		$sql='';		
		for($seq=0;$seq<=count($electronicDataArr['sequ_arr']); $seq++ )
		{			
 			$sys_con = where_con_using_array($tmp_sys_id_arr[$seq],0,'a.ID');
			if($tmp_sys_id_arr[$seq])
			{
				if($sql!=''){$sql .=" UNION ALL ";}
				$sql .= " select a.id as ID, a.booking_no_prefix_num as BOOKING_NO_PREFIX_NUM, a.booking_no as BOOKING_NO, a.booking_year as BOOKING_YEAR, a.booking_date as BOOKING_DATE, a.company_id as COMPANY_ID, a.buyer_id as BUYER_ID, a.supplier_id as SUPPLIER_ID from wo_booking_mst a where a.status_active=1 and a.is_deleted=0 and a.is_approved<>1 and a.ready_to_approved=1 and a.approved_sequ_by=$seq $sys_con and a.company_id=$company_name $searchCon and a.booking_type=3 and a.item_category=12  group by a.id, a.booking_no_prefix_num, a.booking_no, a.booking_year, a.booking_date, a.company_id, a.buyer_id, a.supplier_id";			
			}		
		}		
	}
	else
	{		
		$sql = " select a.id as ID, a.booking_no_prefix_num as BOOKING_NO_PREFIX_NUM, a.booking_no as BOOKING_NO, a.booking_year as BOOKING_YEAR, a.booking_date as BOOKING_DATE, a.company_id as COMPANY_ID, a.buyer_id as BUYER_ID, a.supplier_id as SUPPLIER_ID from wo_booking_mst a, approval_mst c where a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and a.ready_to_approved=1 and a.company_id=$company_name and c.sequence_no={$electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO']} and a.approved_sequ_by=c.sequence_no $searchCon and a.booking_type=3 and c.mst_id=a.id group by a.id, a.booking_no_prefix_num, a.booking_no, a.booking_year, a.booking_date, a.company_id, a.buyer_id, a.supplier_id order by a.id";	
    }
	 //echo $sql;die;
		
	$mst_id_arr=array();
	$nameArray=sql_select( $sql );
	foreach ($nameArray as $row)
	{ 
		$mst_id_arr[$row['ID']]=$row['ID'];
	}
  
 

    $user_arr=return_library_array( "select id, user_name from user_passwd", 'id', 'user_name' );
    $dealing_marchant_arr=return_library_array( "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name", 'id', 'team_member_name' );

    $job_sql=sql_select( "select b.booking_mst_id as BOOKING_ID,b.process as PROCESS , d.job_no as JOB_NO, d.dealing_marchant as DEALING_MARCHANT, d.STYLE_REF_NO as STYLE_REF_NO from wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d where b.po_break_down_id=c.id and c.job_id=d.id and b.is_deleted=0 and c.is_deleted=0 and d.is_deleted=0 and b.booking_mst_id in (".implode(',',$mst_id_arr).")");

	
    $job_data=array();
	foreach ($job_sql as $row)
	{ 
		$job_data[$row['BOOKING_ID']]['JOB_NO'].=$row['JOB_NO'].',';
		$job_data[$row['BOOKING_ID']]['DEALING_MARCHANT'].=$dealing_marchant_arr[$row['DEALING_MARCHANT']].',';
        $job_data[$row['BOOKING_ID']]['STYLE_REF_NO'].=$row['STYLE_REF_NO'].',';
        $job_data[$row['BOOKING_ID']]['PROCESS']=$row['PROCESS'];
	}  
 
	
	$width=1200;
    
    ?>
    <form name="exportlc_2" id="exportlc_2">
        <fieldset style="width:<?= $width+20; ?>px; margin-top:10px">
        <legend>LC Approval.</legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width; ?>" class="rpt_table" align="left" >
                <thead>
                    <th width="20"></th>
                    <th width="35">SL</th>                   
                    <th width="120">Service Booking No</th>
                    <th width="100">Booking Year</th>
					<th width="100">Fabric Service Booking Date</th>
					<th width="100">Services Type</th>
					<th width="100">Buyer</th>
                    <th width="100">Supplier</th>                    
                    <th width="120">Style Ref.</th>
                    <th width="120">Job No</th> 
                    <th>Dealing Merchant</th>                                                     
                </thead>
            </table>            
            <div style="width:<?= $width+20; ?>px; overflow-y:scroll; max-height:330px; float:left;" id="buyer_list_view">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width; ?>" class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
                        <?						 
                        $i=1; $all_approval_id=''; $j=0;
                        foreach ($nameArray as $row)
                        {                                               
                            $unapprove_value_id=$row['ID'];
							$bgcolor= ($i%2==0) ? "#E9F3FF" : "#FFFFFF";
							if($approval_type==0)
							{
								$value=$row[csf('id')];
							}
							else
							{
								$app_id = sql_select("select id from approval_history where mst_id='".$row['ID']."' and entry_form='1'  order by id desc");									
								$value=$row[csf('id')]."**".$app_id[0][csf('id')];
							}
							?>
							<tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" id="tr_<?= $i; ?>"> 
								<td width="20" align="center" valign="middle">
									<input type="checkbox" id="tbl_<?= $i;?>" name="tbl[]"  />
									<input type="hidden" id="target_id_<?= $i;?>" name="target_id_[]"  value="<?=$row[ID]; ?>" />                                                  
								</td> 
								<td width="35"><p><?= $i;?></p></td>							
								<td width="120" align="center"><p> <a href="##" onClick="fabric_booking_req_report('show_trim_booking_report2','<? echo  $row['BOOKING_NO'];?>','<? echo  $row['COMPANY_ID'];?>','<? echo  $row['SUPPLIER_ID'];?>','<? echo  $row['ID'];?>')" ><?= $row['BOOKING_NO_PREFIX_NUM']; ?></a> </p></td>
								
								<td width="100" align="center"><p><?= $row['BOOKING_YEAR']; ?></p></td>
								<td width="100" align="center"><p><?= change_date_format($row['BOOKING_DATE']); ?></p></td>
								<td width="100"><p><?=$conversion_cost_head_array[$job_data[$row['ID']]['PROCESS']];?></p></td>
								<td width="100"><p><?= $buyer_arr[$row['BUYER_ID']]; ?></p></td>
								<td width="100"><p><?= $supplier_arr[$row['SUPPLIER_ID']]; ?></p></td>
								<td width="120" style="word-break: break-all;"><p><?= implode(',',array_unique(explode(',',chop($job_data[$row['ID']]['STYLE_REF_NO'],',')))); ?></p></td>
								<td width="120" style="word-break: break-all;"><p><?= implode(',',array_unique(explode(',',chop($job_data[$row['ID']]['JOB_NO'],',')))); ?></p></td>
								<td style="word-break: break-all;"><p><?= implode(',',array_unique(explode(',',chop($job_data[$row['ID']]['DEALING_MARCHANT'],','))));; ?></p></td>
							</tr>
							<?
							$i++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
			
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width; ?>" class="rpt_table" align="left" >
				<tfoot>
                     <td width="20" align="center" valign="middle">
                    	<input type="checkbox" id="all_check" onClick="check_all('all_check')" />
                        <input type="hidden" name="hide_approval_type" id="hide_approval_type" value="<?= $approval_type; ?>">
                    </td>
                    <td align="left">
                        <input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<?= $i; ?>,<?= $approval_type; ?>,<?= $app_user_id; ?>)"/>
						<input type="button" value="Deny" class="formbutton" style="width:100px;<?= ($approval_type==1)?' display:none':''; ?>" onClick="submit_approved(<?=$i; ?>,5,<?= $app_user_id; ?>);"/>
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

	$company_name = str_replace("'","",$cbo_company_name);
	$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
	$approval_type = str_replace("'","",$approval_type);
	$target_ids = str_replace("'","",$target_ids);
	$target_app_id_arr = explode(',',$target_ids);
	$app_user_id = str_replace("'","",$approved_user_id);

	$max_approved_no_arr = return_library_array( "select ID,max(approved_no) as APPROVED_NO from approval_history where mst_id in($target_ids) and entry_form=65",'ID','APPROVED_NO');

	$sql="select a.id as ID, a.IS_APPROVED,a.BUYER_ID as BUYER_ID from wo_booking_mst a where a.READY_TO_APPROVED=1 and a.id in($target_ids)";
	$sqlResult=sql_select( $sql );
	foreach ($sqlResult as $row)
	{
		$matchDataArr[$row['ID']] = array('buyer_id'=>$row['BUYER_ID'],'brand'=>0,'item'=>0,'store'=>0,'department'=>0);
		$last_app_status_arr[$row['ID']] = $row['IS_APPROVED'];
	}

	$finalDataArr=getFinalUser(array('company_id'=>$company_name,'entry_form'=>65,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0,'lib_department_id_arr'=>0,'match_data'=>$matchDataArr));
		
	$sequ_no_arr_by_sys_id =$finalDataArr['final_seq'];
	$user_sequence_no = $finalDataArr['user_seq'][$app_user_id];


	//echo $target_ids;die;
    if($approval_type==5)
	{

		$hid = return_next_id("id","approval_history",1);
		foreach($target_app_id_arr as $key => $mst_id)
		{		
			$approved_no = $max_approved_no_arr[$mst_id]*1;
			if($last_app_status_arr[$mst_id] == 0 || $last_app_status_arr[$mst_id] == 2){
				$approved_no=$approved_no+1;
			}

            if($his_data_arrayi!=''){$his_data_array.=","; }           
            $his_data_array.="(".$hid.",65,".$mst_id.",".$approved_no.",'".$user_sequence_no."',0,".$app_user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,2)";        
            $hid++;
			
	
		}


		$flag=1;

		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=65 and mst_id in ($target_ids)";
			$rID1=execute_query($query,1); 
			if($rID1) $flag=1; else $flag=0; 
		}

        if($flag==1) 
        {
            $his_field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date, is_signing,approved";  
			$rID2=sql_insert("approval_history",$his_field_array,$his_data_array,0);
			if($rID2) $flag=1; else $flag=0; 
            
        }

		if($flag==1) 
		{
			$rID3=sql_multirow_update("WO_BOOKING_MST","IS_APPROVED*READY_TO_APPROVED*APPROVED_SEQU_BY",'2*0*0',"id",$target_ids,0);
			if($rID3) $flag=1; else $flag=0; 
		}
		
		$response=$target_ids;
		if($flag==1) $msg='50'; else $msg='51';

	} 
	else if($approval_type==0)
	{      

		$id = return_next_id("id","approval_mst", 1);
		$hid = return_next_id("id","approval_history",1);
		$approved_no_array=array();
		foreach($target_app_id_arr as $key => $mst_id)
		{		
			$approved_no = $max_approved_no_arr[$mst_id]*1;
			if($last_app_status_arr[$mst_id] == 0 || $last_app_status_arr[$mst_id] == 2){
				$approved_no=$approved_no+1;
				$approved_no_array[$mst_id]=$approved_no;    
			}

			$approved=(max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no)?1:3;

			if($mst_data_array!=''){$mst_data_array.=",";}
			$mst_data_array.="(".$id.",65,".$mst_id.",".$user_sequence_no.",".$app_user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."',".$approved.")"; 
			$id++;

            if($his_data_arrayi!=''){$his_data_array.=","; }           
            $his_data_array.="(".$hid.",65,".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$app_user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,".$approved.")";        
            $hid++;
			
			$mst_data_array_up[$mst_id] =explode(",",("".$approved.",".$user_sequence_no.",'".$pc_date_time."',".$app_user_id."")); 
		}


		$flag=1;
		if($flag==1) 
		{
			$mst_field_array="id, entry_form, mst_id, sequence_no, approved_by, approved_date, inserted_by, insert_date, user_ip,approved";
			$rID1=sql_insert("approval_mst",$mst_field_array,$mst_data_array,0);
			if($rID1) $flag=1; else $flag=0; 
		}
		
        if($flag==1) 
        {
            $his_field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date, is_signing,approved";  
			$rID2=sql_insert("approval_history",$his_field_array,$his_data_array,0);
			if($rID2) $flag=1; else $flag=0; 
            
        }

		
		if($flag==1) 
		{
			$mst_field_array_up="is_approved*approved_sequ_by*approved_date*approved_by"; 
			$rID3=execute_query(bulk_update_sql_statement( "wo_booking_mst", "id", $mst_field_array_up, $mst_data_array_up, $target_app_id_arr ));
			if($rID3) $flag=1; else $flag=0; 
		}
		
 
        
		if(count($approved_no_array)>0){
			$approved_string="";
			foreach($approved_no_array as $key=>$value)
			{
				$approved_string.=" WHEN $key THEN $value";
			}
			
			$approved_string_mst="CASE id ".$approved_string." END";
			$approved_string_dtls="CASE mst_id ".$approved_string." END";
		}

 	 
		// echo '21**'.$rID1.'**'.$rID2.'**'.$rID3;oci_rollback($con);die;		 
		if($flag==1) $msg='19'; else $msg='21';
	}
	else
	{              
		$hid = return_next_id("id","approval_history",1);
		foreach($target_app_id_arr as $key => $mst_id)
		{		
			$approved_no = $max_approved_no_arr[$mst_id]*1;
			if($last_app_status_arr[$mst_id] == 0 || $last_app_status_arr[$mst_id] == 2){
				$approved_no=$approved_no+1;
			}

            if($his_data_arrayi!=''){$his_data_array.=","; }           
            $his_data_array.="(".$hid.",65,".$mst_id.",".$approved_no.",'".$user_sequence_no."',0,".$app_user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";        
            $hid++;
		}

		//echo $his_data_array;die;
		$flag=1;

		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=65 and mst_id in ($target_ids)";
			$rID1=execute_query($query,1); 
			if($rID1) $flag=1; else $flag=0; 
		}

        if($flag==1) 
        {
            $his_field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date, is_signing,approved";  
			$rID2=sql_insert("approval_history",$his_field_array,$his_data_array,0);
			if($rID2) $flag=1; else $flag=0; 
            
        }

		if($flag==1) 
		{
			$rID3=sql_multirow_update("WO_BOOKING_MST","IS_APPROVED*READY_TO_APPROVED*APPROVED_SEQU_BY",'0*0*0',"id",$target_ids,0);
			if($rID3) $flag=1; else $flag=0; 
		}
		
	 //echo "22**$rID1**$rID2**$rID3";oci_rollback($con);die;
		if($flag==1) $msg='20'; else $msg='22';
	}
	
		
	if($flag==1)
	{
		oci_commit($con);
		echo "1**19";
	}
	else
	{
		oci_rollback($con);
		echo "0**21";
	}
	disconnect($con);
	die;	
}

?>