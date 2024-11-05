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


 
function getSequence($parameterArr=array())
{
	$lib_buyer_id_string=implode(',',(array_keys($parameterArr[lib_buyer_arr]))); 
	//$lib_department_id_string=implode(',',(array_keys($parameterArr[lib_department_id_arr]))); 
	//User data.....................
	$sql_user="SELECT ID,BUYER_ID,BRAND_ID,ITEM_CATE_ID,IS_DATA_LEVEL_SECURED,store_location_id as STORE_ID FROM USER_PASSWD WHERE VALID=1";
	$sql_user_result=sql_select($sql_user);
	$userDataArr=array();
	foreach($sql_user_result as $rows){
		$userDataArr[$rows[ID]]=$rows;
	}
	
	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr[company_id]} AND PAGE_ID = {$parameterArr[page_id]} AND IS_DELETED = 0 order by SEQUENCE_NO";
	 //echo $sql;die;
	$sql_result=sql_select($sql);
	$dataArr=array();
	foreach($sql_result as $rows){
		$rows[BUYER_ID]=$userDataArr[$rows[USER_ID]][BUYER_ID];
		//if($rows[DEPARTMENT]==''){$rows[DEPARTMENT]=$lib_department_id_string;}
		if($rows[BUYER_ID]==''){$rows[BUYER_ID]=$lib_buyer_id_string;}
		
		$dataArr[sequ_by][$rows[SEQUENCE_NO]]=$rows;
		$dataArr[user_by][$rows[USER_ID]]=$rows;
		$dataArr[sequ_arr][$rows[SEQUENCE_NO]]=$rows[SEQUENCE_NO];
	}
	return $dataArr;
}

function getFinalUser($parameterArr=array())
{
	$lib_buyer_arr=implode(',',(array_keys($parameterArr[lib_buyer_arr])));
	//User data.....................
	$sql_user="SELECT ID,BUYER_ID,BRAND_ID,ITEM_CATE_ID as ITEM_ID,store_location_id as STORE_ID,IS_DATA_LEVEL_SECURED FROM USER_PASSWD WHERE VALID=1";
	$sql_user_result=sql_select($sql_user);
	$userDataArr=array();
	foreach($sql_user_result as $rows){
		$userDataArr[$rows[ID]][BUYER_ID]=$rows[BUYER_ID];
		//$userDataArr[$rows[ID]][BRAND_ID]=$rows[BRAND_ID];
	}
	
	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr[company_id]} AND PAGE_ID = {$parameterArr[page_id]} AND IS_DELETED = 0  order by SEQUENCE_NO";
	   //echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){
		if($userDataArr[$rows[USER_ID]][BUYER_ID]==''){
			$userDataArr[$rows[USER_ID]][BUYER_ID]=$lib_buyer_arr;
		}
		
		$usersDataArr[$rows[USER_ID]][BUYER_ID]=explode(',',$userDataArr[$rows[USER_ID]][BUYER_ID]);
		$userSeqDataArr[$rows[USER_ID]]=$rows[SEQUENCE_NO];	
	}

	$finalSeq=array();
	foreach($parameterArr[match_data] as $sys_id=>$bbtsRows){
		
		foreach($userSeqDataArr as $user_id=>$seq){
			if (in_array($bbtsRows[BUYER],$usersDataArr[$user_id][BUYER_ID]) && $bbtsRows[BUYER]>0) {
				$finalSeq[$sys_id][$user_id]=$seq;
			}
		}
	}

	 //var_dump($finalSeq);die;
	 //echo $finalSeq;die;
	//die;
	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr);
}


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
		$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$company_id  and a.id!=$user_id  and b.is_deleted=0  and b.page_id=$menu_id order by b.sequence_no";
			// echo $sql;
		$arr=array (2=>$custom_designation,3=>$department_arr);
		echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
		?>
	</form>
	<?
	exit();
}
 
if($action=="report_generate")
{
	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$lien_bank_arr=return_library_array( "select (bank_name || ' (' || branch_name || ')' ) as bank_name, id from lib_bank where is_deleted=0 and status_active=1 and lien_bank=1 order by bank_name", "id", "bank_name"  );
	?>

	<script>
	</script>
	<?
	
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_name = str_replace("'","",$cbo_company_name);
	$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);	
	$cbo_get_upto = str_replace("'","",$cbo_get_upto);
	$txt_sc_no = str_replace("'","",$txt_sc_no);
	$txt_system_id = str_replace("'","",$txt_system_id);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	
	$approval_type = str_replace("'","",$cbo_approval_type);
	$alter_user_id = str_replace("'","",$txt_alter_user_id);
	$user_id=($alter_user_id!='') ? $alter_user_id : $user_id;
	
	if($cbo_buyer_name > 0){$searchCon .=" and a.buyer_name=$cbo_buyer_name";}
	if($txt_system_id != '') {$searchCon .=" and a.contact_system_id='$txt_system_id'";}
	if($txt_sc_no != ''){$searchCon .=" and a.contract_no='$txt_sc_no'";}
	
	if ($txt_date_from != '' && $txt_date_to != '')
	{
		if($db_type==0)
		{
			$txt_date_from = date("Y-m-d", strtotime($txt_date_from));
			$txt_date_to = date("Y-m-d", strtotime($txt_date_to));
			$searchCon .= " and a.contract_date between '$txt_date_from' and '$txt_date_to'";
		}
		else
		{
			$txt_date_from = date("d-M-Y", strtotime($txt_date_from));
			$txt_date_to = date("d-M-Y", strtotime($txt_date_to));
			$searchCon .= " and a.contract_date between '$txt_date_from' and '$txt_date_to'";
		}	
	}
	
	//813=>Purchase Requisition Approval
	$electronicDataArr=getSequence(array('company_id'=>$company_name,'page_id'=>$menu_id,'user_id'=>$user_id,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0,'lib_department_id_arr'=>0));
	//echo '<pre>';print_r($electronicDataArr);die;  

	
	if($approval_type==0) // Un-Approve
	{
		//Match data..................................
		if($electronicDataArr['user_by'][$user_id]['BUYER_ID']) 
		{
			$where_con .= " and a.buyer_name in(".$electronicDataArr['user_by'][$user_id]['BUYER_ID'].",0)";
			$electronicDataArr['sequ_by'][0]['BUYER_ID']=$electronicDataArr['user_by'][$user_id]['BUYER_ID'];
		}
		
		$data_mas_sql = "select a.id as ID, a.buyer_name as BUYER_NAME from com_sales_contract a where a.status_active=1 and a.is_deleted=0 and a.approved<>1 and a.ready_to_approved=1 and a.beneficiary_name=$company_name $where_con $searchCon";
		//echo $data_mas_sql; 
				 
		$tmp_sys_id_arr=array();
		$data_mas_sql_res=sql_select( $data_mas_sql );
			
		foreach ($data_mas_sql_res as $row)
		{ 
			for($seq=($electronicDataArr['user_by'][$user_id]['SEQUENCE_NO']-1);$seq>=0; $seq-- )
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
		//..........................................Match data;
	
		$sql='';		
		for($seq=0;$seq<=count($electronicDataArr[sequ_arr]); $seq++ )
		{			
 			$sys_con = where_con_using_array($tmp_sys_id_arr[$seq],0,'a.ID');
			if($tmp_sys_id_arr[$seq])
			{
				if($sql!=''){$sql .=" UNION ALL ";}
				$sql .= " select a.id as ID, a.contact_system_id as SYSTEM_ID, a.contract_no as SC_NO, a.contract_date as SC_DATE, a.beneficiary_name as COMPANY_NAME, a.buyer_name as BUYER_NAME, a.lien_bank as LIEN_BANK, a.contract_value as SC_VALUE, a.internal_file_no as INTERNAL_FILE_NO, a.pay_term as PAY_TERM, a.inserted_by as INSERTED_BY, a.last_shipment_date as LAST_SHIPMENT_DATE, a.expiry_date as EXPIRY_DATE from com_sales_contract a where a.status_active=1 and a.is_deleted=0 and a.approved<>1 and a.ready_to_approved=1 and a.approved_sequ_by=$seq $sys_con and a.beneficiary_name=$company_name $searchCon group by a.id, a.contact_system_id, a.contract_no, a.contract_date, a.beneficiary_name, a.buyer_name, a.lien_bank, a.contract_value, a.internal_file_no, a.pay_term, a.inserted_by, a.last_shipment_date, a.expiry_date";	
			}		
		}		
	}
	else
	{		
		$sql = " select a.id as ID, a.contact_system_id as SYSTEM_ID, a.contract_no as SC_NO, a.contract_date as SC_DATE, a.beneficiary_name as COMPANY_NAME, a.buyer_name as BUYER_NAME, a.lien_bank as LIEN_BANK, a.contract_value as SC_VALUE, a.internal_file_no as INTERNAL_FILE_NO, a.pay_term as PAY_TERM, a.inserted_by as INSERTED_BY,a.last_shipment_date as LAST_SHIPMENT_DATE, a.expiry_date as EXPIRY_DATE from com_sales_contract a, approval_mst c where a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and a.ready_to_approved=1 and a.beneficiary_name=$company_name and c.sequence_no={$electronicDataArr[user_by][$user_id][SEQUENCE_NO]} and a.approved_sequ_by=c.sequence_no $searchCon group by a.id, a.contact_system_id, a.contract_no, a.contract_date, a.beneficiary_name, a.buyer_name, a.lien_bank, a.contract_value, a.internal_file_no, a.pay_term, a.inserted_by, a.last_shipment_date, a.expiry_date	order by a.id";		
    }
	//echo $sql;//die;
		
	$mst_id_arr=array();
	$nameArray=sql_select( $sql );
	foreach ($nameArray as $row)
	{ 
		$mst_id_arr[$row[ID]]=$row[ID];
	}
  
	$hostory_sql=sql_select( "select mst_id as MST_ID, approved_by as APPROVED_BY, approved_date as APPROVED_DATE from approval_history where current_approval_status=1 and entry_form=63 and mst_id in (".implode(',',$mst_id_arr).")");
	foreach ($hostory_sql as $row)
	{ 
		$history_data[LAST_APP_DATE][$row[MST_ID]]=$row[APPROVED_DATE];
		$history_data[LAST_APP_BY][$row[MST_ID]]=$row[APPROVED_BY];
	}
   
	$user_arr=return_library_array( "select id, user_name from user_passwd", 'id', 'user_name' );
	
	$width=1400;

	$report_id = 0;
	$print_report_format = return_field_value("format_id", "lib_report_template", "template_name ='".$company_name."' and module_id=5 and report_id=155 and is_deleted=0 and status_active=1");
	$printButton = explode(',', $print_report_format);
	$report_id = $printButton[0];
 
    ?>
    <form name="exportlc_2" id="exportlc_2">
        <fieldset style="width:<?= $width+20; ?>px; margin-top:10px">
        <legend>LC Approval</legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width; ?>" class="rpt_table" align="left" >
                <thead>
                    <th width="20"></th>
                    <th width="35">SL</th>                   
                    <th width="120">System ID</th>
                    <th width="120">SC No</th>
                    <th width="100">File</th>
                    <th width="100">SC Date</th>
					<th width="100">Last Shipment Date</th>
					<th width="100">Expiry Date</th>
                    <th width="100">Internal File No</th>                    
                    <th width="120">Buyer Name</th>                                       
                    <th width="120">Lien Bank</th> 
                    <th width="100">SC Value</th>
					<th width="100">Pay Term</th>
					<th>Insert By</th>                                                       
                </thead>
            </table>            
            <div style="width:<?= $width+20; ?>px; overflow-y:scroll; max-height:330px; float:left;" id="buyer_list_view">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width; ?>" class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
                        <?						 
                        $i=1; $all_approval_id=''; $j=0;
						$img_val =  return_field_value("master_tble_id","common_photo_library","form_name='sales_contract'","master_tble_id");
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
									<input type="hidden" id="target_id_<?= $i;?>" name="target_id_[]"  value="<?=$row['ID']; ?>" />                                                  
								</td> 
								<td width="35"><p><?= $i;?></p></td>
								<td width="120" onClick="sales_contract_approval_report('<?= $row['ID'];?>','<?= $row['SYSTEM_ID'];?>','<?= $company_name;?>','<?= $report_id;?>');">
								   <a href="javascript:void(0)"><?= $row['SYSTEM_ID']; ?></a>
							    </td>
								<td width="120"><p><?= $row['SC_NO']; ?></p></td>

								<td width="100" align="center"><a href="javascript:void()" onClick="downloiadFile('<? echo $row[csf('id')]; ?>','<? echo $company_name; ?>');">
                                    <? if ($img_val != '') echo 'View File'; ?></a></td>

								<td width="100" align="center"><p><?= change_date_format($row['SC_DATE']); ?></p></td>
								<td width="100" align="center"><p><?= change_date_format($row['LAST_SHIPMENT_DATE']); ?></p></td>
								<td width="100" align="center"><p><?= change_date_format($row['EXPIRY_DATE']); ?></p></td>
								<td width="100"><p><?= $row['INTERNAL_FILE_NO']; ?></p></td>
								<td width="120"><p><?= $buyer_arr[$row['BUYER_NAME']]; ?></p></td>
								<td width="120"><p><?= $lien_bank_arr[$row['LIEN_BANK']]; ?></p></td>
								<td width="100" align="right"><p><?= $row['SC_VALUE']; ?></p></td>
								<td width="100"><p><?= $pay_term[$row['PAY_TERM']]; ?></p></td>
								<td><p><?= $user_arr[$row['INSERTED_BY']]; ?></p></td>
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
                        <input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<?= $i; ?>,<?= $approval_type; ?>,<?= $user_id; ?>)"/>
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
	$user_id = str_replace("'","",$approved_user_id);
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	 //echo $target_ids;die;

	if($approval_type==0)
	{      
		
		//$electronicDataArr=getSequence(array(company_id=>$company_name,page_id=>$menu_id,user_id=>$user_id,lib_buyer_arr=>$buyer_arr,lib_brand_arr=>$brand_arr,lib_item_cat_arr=>$item_cat_arr,lib_store_arr=>$lib_store_arr));

		
		//echo $max_sequ_no;die;
		//------------------
		$sql="select a.id as ID, a.buyer_name as BUYER_NAME from com_sales_contract a where a.id in($target_ids)";
		
		$sqlResult=sql_select( $sql );
		foreach ($sqlResult as $row)
		{
			$matchDataArr[$row[ID]]=array('BUYER'=>$row['BUYER_NAME'],'brand'=>0,'item'=>0,'store'=>0,'department'=>0);
		}
		
		//$matchDataArr[333]=array('buyer'=>0,'brand'=>0,'item'=>15,'store'=>358);
		$finalDataArr=getFinalUser(array(company_id=>$company_name,page_id=>$menu_id,lib_buyer_arr=>$buyer_arr,lib_brand_arr=>0,lib_item_cat_arr=>0,lib_store_arr=>0,lib_department_id_arr=>0,match_data=>$matchDataArr));
		//echo "<pre>";print_r($finalDataArr);die;
		
		$sequ_no_arr_by_sys_id =$finalDataArr[final_seq];
		$user_sequence_no = $finalDataArr[user_seq][$user_id];
	    //---------------------
		
		
		$mst_field_array="id, entry_form, mst_id, sequence_no, approved_by, approved_date, inserted_by, insert_date, user_ip";
		$mst_field_array_up="approved*approved_sequ_by"; 
		$id=return_next_id( "id","approval_mst", 1 ) ;
		foreach($target_app_id_arr as $mst_id)
		{		
			if($mst_data_array!=''){$mst_data_array.=",";}
			$mst_data_array.="(".$id.",63,".$mst_id.",".$user_sequence_no.",".$user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
			$id=$id+1;
			
			//mst data.......................
			$approved=(max($finalDataArr[final_seq][$mst_id])==$user_sequence_no)?1:3;
			$mst_data_array_up[$mst_id] =explode(",",("".$approved.",".$user_sequence_no."")); 
		}
		
		 //print_r($data_array_up);die;
		
		
		$flag=1;
		
		//---------------------------------------------------------------History
		$reqs_ids=explode(",",$target_ids);

		$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date, is_signing";  
		
		$i=0;
        $id=return_next_id( "id","approval_history", 1 ) ;
        
        $approved_no_array=array();
		$data_array='';
		foreach($reqs_ids as $val)
        {
            $approved_no=return_field_value("max(approved_no) as approved_no","approval_history","mst_id='$val' and entry_form=63","approved_no");
            $approved_no=$approved_no+1;
        
            if($i!=0) $data_array.=",";             
            $data_array.="(".$id.",63,".$val.",".$approved_no.",'".$user_sequence_no."',1,".$user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
            
            $approved_no_array[$val]=$approved_no;                
            $id=$id+1;
            $i++;
        }
		//echo $data_array;die;
		
		
		$approved_string="";
        foreach($approved_no_array as $key=>$value)
        {
            $approved_string.=" WHEN $key THEN $value";
        }
        
        $approved_string_mst="CASE id ".$approved_string." END";
        $approved_string_dtls="CASE mst_id ".$approved_string." END";
		
		       
		$rID=sql_insert("approval_mst",$mst_field_array,$mst_data_array,0);
		if($flag==1) 
		{
			if($rID) $flag=1; else $flag=0; 
		}		
		
		if($flag==1) 
		{
			$rID1=execute_query(bulk_update_sql_statement( "com_sales_contract", "id", $mst_field_array_up, $mst_data_array_up, $target_app_id_arr ));
			if($rID1) $flag=1; else $flag=0; 
		}
		
	    $query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=63 and mst_id in ($target_ids)"; //die;        
        if($flag==1) 
        {
			$rID2=execute_query($query,1);
            if($rID2) $flag=1; else $flag=0; 
        }
		
		//echo "insert into approval_history $field_array values($data_array)";die;
		$rID3=sql_insert("approval_history",$field_array,$data_array,0);
        if($flag==1) 
        {
            if($rID3) $flag=1; else $flag=0; 
            
        }        
		//...................................end History;		 
		// echo '21**'.$rID.'**'.$rID1.'**'.$rID2.'**'.$rID3.'**'.$rID4.'**'.$rID5;oci_rollback($con);die;		 
		//echo $data_array;die;
	}
	else
	{              
		$history_data_arr=return_library_array( "select id, id from approval_history where current_approval_status=1 and entry_form=63 and mst_id in ($target_ids)",'id','id');
		$app_ids= implode(',',$history_data_arr);
		 //echo "select id, id from approval_history where current_approval_status=1 and entry_form=1 and mst_id in ($target_ids)";die;
		//echo $app_ids;die;
		$flag=1;
		$rID=sql_multirow_update("com_sales_contract","approved*ready_to_approved*approved_sequ_by","0*2*0","id",$target_ids,0);
		if($flag==1)
		{
			if($rID) $flag=1; else $flag=0;
		}

		$query="delete from approval_mst WHERE entry_form=63 and mst_id in ($target_ids)";
		if($flag==1) 
		{			
			$rID2=execute_query($query,1);
			if($rID2) $flag=1; else $flag=0;
		}
		
		//-----------------------History
		
		$query="UPDATE approval_history SET current_approval_status=0,IS_SIGNING=0 WHERE entry_form=63 and mst_id in ($target_ids)";	
		if($flag==1) 
		{
			$rID4=execute_query($query,1);
			if($rID4) $flag=1; else $flag=0; 
		} 
			
		$data=$user_id."*'".$pc_date_time."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";		
		if($flag==1) 
		{
			$rID5=sql_multirow_update("approval_history","un_approved_by*un_approved_date*updated_by*update_date",$data,"id",$app_ids,1);
			if($rID5) $flag=1; else $flag=0;
		}		
	}
	//echo "0**$rID**$rID2**$rID3**$rID4**$rID5";oci_rollback($con);die;	
		
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

if($action=="get_user_pi_file")
{
    // var_dump($_REQUEST);
    extract($_REQUEST);
  
    $img_sql = "SELECT id,image_location,master_tble_id,real_file_name,FILE_TYPE from common_photo_library where form_name='sales_contract' and master_tble_id='$id'";

	// echo "SELECT id,image_location,master_tble_id,real_file_name,FILE_TYPE from common_photo_library where form_name='sales_contract' and master_tble_id='$id'";die;
    $img_sql_res = sql_select($img_sql);
    if(count($img_sql_res)==0){ echo "<div style='text-align:center;color:red;font-size:18px;'>Image/File is not available.</div>";die();}
    foreach($img_sql_res as $img)
    {
        if($img[FILE_TYPE]==1){
            echo '<p style="display:inline-block;word-wrap:break-word;word-break:break-all;width:115px;padding-right:10px;vertical-align:top;margin:0px;">
            <a href="?action=downloiadFile&file='.urlencode($img[csf("image_location")]).'">
                <img src="../../' . $img[csf("image_location")] . '" width="89px" height="97px">
            </a><br>' . $img[csf("real_file_name")] . '
          </p>';
		}
        else{
            echo '<p style="display:inline-block;word-wrap:break-word;word-break:break-all;width:115px;padding-right:10px;vertical-align:top;margin:0px;">
            <a href="?action=downloiadFile&file='.urlencode($img[csf("image_location")]).'">
                <img src="../../' . $img[csf("image_location")] . '" width="89px" height="97px">
            </a><br>' . $img[csf("real_file_name")] . '
          </p>'; 
        }
    }
}

if($action=="downloiadFile")
{
    if(isset($_REQUEST["file"]))
    {        
        $file = urldecode($_REQUEST["file"]); // Decode URL-encoded string   
        
       // echo $file;die;
		
		$filepath = "../../" . $file;    
        // Process download
        if(file_exists($filepath)) {
              ob_end_clean();
            header("Content-Type: {$mime}");
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            flush(); // Flush system output buffer
            readfile($filepath);
            exit();
        }
    }
}

?>