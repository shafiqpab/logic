<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];
$menu_id=$_SESSION['menu_id'];
$user_id=$_SESSION['logic_erp']['user_id'];
include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
//$approval_setup=is_duplicate_field( "page_id", "electronic_approval_setup", "page_id=$menu_id and is_deleted=0" );
function getSequence($parameterArr=array()){
	$lib_buyer_arr=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,ENTRY_FORM,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['ENTRY_FORM']} AND IS_DELETED = 0 order by SEQUENCE_NO";
	 //echo $sql;die;
	$sql_result=sql_select($sql);
	$dataArr=array();
	foreach($sql_result as $rows){		
		if($rows['BUYER_ID']==''){$rows['BUYER_ID']=$lib_buyer_arr;}
		$dataArr['sequ_by'][$rows['SEQUENCE_NO']]=$rows;
		$dataArr['user_by'][$rows['USER_ID']]=$rows;
		$dataArr['sequ_arr'][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
	}
 
	return $dataArr;
}

function getFinalUser($parameterArr=array()){
	$lib_buyer_arr=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));

	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,ENTRY_FORM,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['ENTRY_FORM']} AND IS_DELETED = 0  order by SEQUENCE_NO";
	//echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){
		if($rows['BUYER_ID']==''){$rows['BUYER_ID']=$lib_buyer_arr;}
		$usersDataArr[$rows['USER_ID']]['BUYER_ID']=explode(',',$rows['BUYER_ID']);
		$userSeqDataArr[$rows['USER_ID']]=$rows['SEQUENCE_NO'];
	
	}

	$finalSeq=array();
	foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){
		foreach($userSeqDataArr as $user_id=>$seq){
			//if( in_array($bbtsRows['buyer_id'],$usersDataArr[$user_id]['BUYER_ID']) &&  $bbtsRows['buyer_id']>0 ){
				$finalSeq[$sys_id][$user_id]=$seq;
			//}
		}
	}

 
	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr);
}


$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_name=str_replace("'","",$cbo_company_name);
	$approval_type=str_replace("'","",$cbo_approval_type);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
    $txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$user_id_approval=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;

	//echo $menu_id;die;

    $electronicDataArr=getSequence(array('company_id'=>$cbo_company_name,'ENTRY_FORM'=>20,'user_id'=>$user_id_approval,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'product_dept_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0));

    if($approval_type==0) // Un-Approve
	 {  
		//Match data..................................
		// if($electronicDataArr['user_by'][$user_id_approval]['BUYER_ID']){
		// 	$where_con .= " and a.BUYER_ID in(".$electronicDataArr['user_by'][$user_id_approval]['BUYER_ID'].",0)";
		// 	$electronicDataArr['sequ_by'][0]['BUYER_ID']=$electronicDataArr['user_by'][$user_id_approval]['BUYER_ID'];
		// }
        

		$data_mast_sql = "SELECT a.ID, a.company_id, a.requ_prefix_num, a.requ_no, a.supplier_id,  a.requisition_date, a.delivery_date, a.is_approved, listagg(b.buyer_id, ',') within group (order by b.buyer_id) buyer_id
        from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b
        where a.id=b.mst_id and a.company_id=$cbo_company_name and a.item_category_id=1  and a.is_deleted=0 and a.status_active=1 and a.ready_to_approve=1 and a.IS_APPROVED<>1  group by a.ID, a.company_id, a.requ_prefix_num, a.requ_no, a.requisition_date, a.delivery_date, a.supplier_id, a.is_approved
          order by a.requisition_date desc ";
		 //echo $data_mast_sql;die;


		$tmp_sys_id_arr=array();
		$data_mast_sql_res=sql_select( $data_mast_sql );
		foreach ($data_mast_sql_res as $row)
		{ 
			for($seq=($electronicDataArr['user_by'][$user_id_approval]['SEQUENCE_NO']-1);$seq>=0; $seq-- ){
				if($electronicDataArr['sequ_by'][$seq]['BYPASS']==1){
					$tmp_sys_id_arr[$seq][$row['ID']]=$row['ID'];
				}
				else{
					$tmp_sys_id_arr[$seq][$row['ID']]=$row['ID'];
					break;
				}
			}
		}
		//..........................................Match data;		
	
		//print_r($tmp_sys_id_arr);die;
		
		$sql='';
		for($seq=0;$seq<=count($electronicDataArr['sequ_arr']); $seq++ ){
			$sys_con = where_con_using_array($tmp_sys_id_arr[$seq],0,'a.ID');

			if($tmp_sys_id_arr[$seq]){
				if($sql!=''){$sql .=" UNION ALL ";}
				$sql .= "SELECT a.ID,a.basis, a.company_id, a.requ_prefix_num, a.requ_no, a.supplier_id,  a.requisition_date, a.delivery_date, a.is_approved, listagg(b.buyer_id, ',') within group (order by b.buyer_id) buyer_id
                from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b
                where a.id=b.mst_id and a.company_id=$cbo_company_name and a.item_category_id=1  and a.is_deleted=0 and a.status_active=1 and a.ready_to_approve=1 and a.IS_APPROVED<>1  and a.APPROVED_SEQU_BY=$seq $sys_con group by a.ID,a.basis, a.company_id, a.requ_prefix_num, a.requ_no, a.requisition_date, a.delivery_date, a.supplier_id, a.is_approved
                  order by a.requisition_date desc ";
			}
		}
	}     
	else
	{     

		$sql = "SELECT a.ID,a.basis, a.company_id, a.requ_prefix_num, a.requ_no, a.supplier_id,  a.requisition_date, a.delivery_date, a.is_approved, listagg(b.buyer_id, ',') within group (order by b.buyer_id) buyer_id
        from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b,APPROVAL_MST c
        where a.id=b.mst_id and a.company_id=$cbo_company_name and a.item_category_id=1  and a.is_deleted=0 and a.status_active=1 and a.ready_to_approve=1 and a.IS_APPROVED<>0 and  c.SEQUENCE_NO={$electronicDataArr['user_by'][$user_id_approval]['SEQUENCE_NO']}  and a.APPROVED_SEQU_BY=c.SEQUENCE_NO and c.entry_form=20 and c.mst_id=a.id  group by a.ID,a.basis, a.company_id, a.requ_prefix_num, a.requ_no, a.requisition_date, a.delivery_date, a.supplier_id, a.is_approved
          order by a.requisition_date desc ";

    }

	//echo $sql;die();


	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:650px; margin-top:10px">
        <legend>Yarn Requisition Approval.</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="620" class="rpt_table" >
                <thead>
                	<th width="50"></th>
                    <th width="60">SL</th>
                    <th width="150">Requisition No</th>
                    <th width="120">Supplier</th>
                    <th width="120">Requisition Date</th>
                    <th>Delivery Date</th>
                </thead>
            </table>
            <div style="width:620px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
                            $i=1;
							$supplier=return_library_array("select id, supplier_name from lib_supplier ",'id','supplier_name');

							$nameArray=sql_select($sql);
                            foreach ($nameArray as $row)
                            {
								if ($i%2==0) $bgcolor="#E9F3FF";									
								else $bgcolor="#FFFFFF";
								$value='';
								if($approval_type==0)
								{
									$value=$row[csf('id')];
								}
								else
								{
									//$app_id=return_field_value("id","approval_history","mst_id ='".$row[csf('id')]."' and entry_form='20' and current_approval_status=1 ");
									$value=$row[csf('id')]."**".$row[csf('approval_id')];
								}

								$print_report_format=0;
							    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$company_name."' and module_id=5 and report_id=69 and is_deleted=0 and status_active=1");
							    $printButton=explode(',',$print_report_format);
							    $first_report=$printButton[0];
							    if ($first_report==134) $buttonHtml='<a href="../commercial/work_order/requires/yarn_requisition_entry_controller.php?data='.$row[csf('company_id')].'*'.$row[csf('id')].'*Yarn Purchase Requisition*'.$row[csf('is_approved')].'&action=yarn_requisition_print" style="color:#000" target="_blank">'.$row[csf('requ_prefix_num')].'</a>';
							    else if($first_report==135) $buttonHtml='<a href="../commercial/work_order/requires/yarn_requisition_entry_controller.php?data='.$row[csf('company_id')].'*'.$row[csf('id')].'*Yarn Purchase Requisition*'.$row[csf('basis')].'&action=yarn_requisition_print_2" style="color:#000" target="_blank">'.$row[csf('requ_prefix_num')].'</a>';
							    else if($first_report==136) $buttonHtml='<a href="../commercial/work_order/requires/yarn_requisition_entry_controller.php?data='.$row[csf('company_id')].'*'.$row[csf('id')].'*Yarn Purchase Requisition*7*'.$row[csf('basis')].'&action=yarn_requisition_print_3" style="color:#000" target="_blank">'.$row[csf('requ_prefix_num')].'</a>';
							    else if($first_report==137) $buttonHtml='<a href="../commercial/work_order/requires/yarn_requisition_entry_controller.php?data='.$row[csf('company_id')].'*'.$row[csf('id')].'*Yarn Purchase Requisition*&action=yarn_requisition_print_4" style="color:#000" target="_blank">'.$row[csf('requ_prefix_num')].'</a>';
							    else if($first_report==64) $buttonHtml='<a href="../commercial/work_order/requires/yarn_requisition_entry_controller.php?data='.$row[csf('company_id')].'*'.$row[csf('id')].'*Yarn Purchase Requisition*&action=yarn_requisition_print_5" style="color:#000" target="_blank">'.$row[csf('requ_prefix_num')].'</a>';
								else if($first_report==72) $buttonHtml='<a href="../commercial/work_order/requires/yarn_requisition_entry_controller.php?data='.$row[csf('company_id')].'*'.$row[csf('id')].'*Yarn Purchase Requisition*&action=yarn_requisition_print_6" style="color:#000" target="_blank">'.$row[csf('requ_prefix_num')].'</a>';
								else if($first_report==777) $buttonHtml='<a href="../commercial/work_order/requires/yarn_requisition_entry_controller.php?data='.$row[csf('company_id')].'*'.$row[csf('id')].'*Yarn Purchase Requisition*&action=yarn_requisition_print_fso" style="color:#000" target="_blank">'.$row[csf('requ_prefix_num')].'</a>';
								else $buttonHtml='<a href="../commercial/work_order/requires/yarn_requisition_entry_controller.php?data='.$row[csf('company_id')].'*'.$row[csf('id')].'*Yarn Purchase Requisition*&action=yarn_requisition_print_7" style="color:#000" target="_blank">'.$row[csf('requ_prefix_num')].'</a>';
								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                	<td width="50" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="req_id_<? echo $i;?>" name="req_id[]" type="hidden" value="<? echo $row[csf('id')]; ?>" />
                                        <input id="requisition_id_<? echo $i;?>" name="requisition_id[]" type="hidden" value="<? echo $row[csf('id')]; ?>" /><!--this is uesd for delete row-->
                                    </td>
									<td width="60" align="center"><? echo $i; ?></td>
									<td width="150"><? echo $buttonHtml?></td>
                                    <td width="120"><p><? echo $supplier[$row[csf('supplier_id')]]; ?></p></td>
									<td width="120" align="center"><? if($row[csf('requisition_date')]!="0000-00-00") echo change_date_format($row[csf('requisition_date')]); ?></td>
									<td align="center"><? if($row[csf('delivery_date')]!="0000-00-00") echo change_date_format($row[csf('delivery_date')]); ?></td>
								</tr>
								<?
								$i++;
							}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="620" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" ><input type="checkbox" id="all_check" onClick="check_all('all_check')" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/>

					<input type="button" value="Deny" class="formbutton" style="width:100px;<?= ($approval_type==1)?' display:none':''; ?>" onClick="submit_approved(<?=$i; ?>,5);"/>
				
				</td>
					
				</tfoot>
			</table>
        </fieldset>
    </form>
<?
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
             $Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');   ;
             $sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation,b.SEQUENCE_NO from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.entry_form=20 and valid=1 and a.id!=$user_id  and b.is_deleted=0 order by b.SEQUENCE_NO";
                //echo $sql;die();
             $arr=array (2=>$custom_designation,3=>$Department);
             echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department,Seq. No", "100,120,130,120,50,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id,0", $arr , "user_name,user_full_name,designation,department_id,SEQUENCE_NO", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
            ?>
    </form>
    <script language="javascript" type="text/javascript">
      setFilterGrid("tbl_style_ref");
    </script>
	<?
	exit();
}
if ($action=="approve")
{  
	$process = array( &$_POST );

	extract(check_magic_quote_gpc( $process )); 
	$con = connect();
    $company_name=str_replace("'","",$cbo_company_name);
	//$approval_type=str_replace("'","",$cbo_approval_type);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$user_id_approval=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;





	//echo "10**".'zdhgdsfgsgf';die;

	//............................................................................
	
	$sql = "select a.ID  from inv_purchase_requisition_mst a where a.COMPANY_ID=$cbo_company_name and a.STATUS_ACTIVE=1 and a.IS_DELETED=0  and a.id in($req_nos)";
    //echo $sql;die();
	$sqlResult=sql_select( $sql );
	foreach ($sqlResult as $row)
	{
        //if($row['READY_TO_APPROVE'] != 1){echo '21**Ready to approve yes is mandatory';exit();}
		$matchDataArr[$row['ID']]=array('buyer_id'=>0,'brand_id'=>0,'supplier_id'=>0,'store'=>0);
	}
    $finalDataArr=getFinalUser(array('company_id'=>$cbo_company_name,'ENTRY_FORM'=>20,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0,'product_dept_arr'=>0,'match_data'=>$matchDataArr));
	
 
	$sequ_no_arr_by_sys_id =$finalDataArr['final_seq'];
	$user_sequence_no = $finalDataArr['user_seq'][$user_id_approval];
 

	if($approval_type==5)
	{

		$rID1=sql_multirow_update("inv_purchase_requisition_mst","IS_APPROVED*READY_TO_APPROVE*APPROVED_SEQU_BY",'0*0*0',"id",$req_nos,0);
		if($rID1) $flag=1; else $flag=0;

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=20 and current_approval_status=1 and mst_id in ($req_nos)";
			$rID2=execute_query($query,1);
			if($rID2) $flag=1; else $flag=0;
		}

				
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=20 and mst_id in ($req_nos)";
			$rID3=execute_query($query,1); 
			if($rID3) $flag=1; else $flag=0; 
		}
		
		 // echo "191**".$rID1.",".$rID2.",".$rID3.",".$rID4;oci_rollback($con);die;
		
		$response=$req_nos;
		if($flag==1) $msg='50'; else $msg='51';

	} 
	else if($approval_type==0)
	{      
 		
		$id=return_next_id( "id","approval_mst", 1 ) ;
		$ahid=return_next_id( "id","approval_history", 1 ) ;	
		
		$target_app_id_arr = explode(',',$req_nos);	
        foreach($target_app_id_arr as $mst_id)
        {		
			if($data_array!=''){$data_array.=",";}
			$data_array.="(".$id.",20,".$mst_id.",".$user_sequence_no.",".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
			$id=$id+1;
			
			$approved_no=$max_approved_no_arr[$mst_id][$user_id_approval]+1;
			if($history_data_array!="") $history_data_array.=",";
			$history_data_array.="(".$ahid.",20,".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
			$ahid++;
			
			//mst data.......................
			$approved=(max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no)?1:3;
			$data_array_up[$mst_id] = explode(",",("".$approved.",".$user_sequence_no."")); 
        }
	 
 

        $flag=1;
		if($flag==1) 
		{
			$field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
			$rID1=sql_insert("approval_mst",$field_array,$data_array,0);
            //echo "10**insert into approval_mst ($field_array) values" . $data_array;die;


			if($rID1) $flag=1; else $flag=0; 
		}
		
		if($flag==1) 
		{
			$field_array_up="IS_APPROVED*APPROVED_SEQU_BY"; 
			$rID2=execute_query(bulk_update_sql_statement( "inv_purchase_requisition_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr ));
			if($rID2) $flag=1; else $flag=0; 
		}

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=20 and mst_id in ($req_nos)";
			$rID3=execute_query($query,1);
			if($rID3) $flag=1; else $flag=0;
		}
		 
		if($flag==1)
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING";
			$rID4=sql_insert("approval_history",$field_array,$history_data_array,0);
			if($rID4) $flag=1; else $flag=0;
		}
		
		//echo "21**".$rID1.",".$rID2.",".$rID3.",".$rID4;oci_rollback($con);die;
		
		if($flag==1) $msg='19'; else $msg='21';

        
	}
	else
	{              
		
		$next_user_app = sql_select("select id from approval_history where mst_id in($req_nos) and entry_form=20 and sequence_no >$user_sequence_no and current_approval_status=1 group by id");
				
		if(count($next_user_app)>0)
		{
			echo "25**unapproved"; 
			disconnect($con);
			die;
		}

		$rID1=sql_multirow_update("inv_purchase_requisition_mst","is_approved*READY_TO_APPROVE*APPROVED_SEQU_BY",'0*0*0',"id",$req_nos,0);
		if($rID1) $flag=1; else $flag=0;


		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=20 and mst_id in ($req_nos)";
			$rID2=execute_query($query,1);
			if($rID2) $flag=1; else $flag=0;
		}

		
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=20 and mst_id in ($req_nos)";
			$rID3=execute_query($query,1); 
			if($rID3) $flag=1; else $flag=0; 
		}
		

		
		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=20 and current_approval_status=1 and mst_id in ($req_nos)";
			$rID4=execute_query($query,1);
			if($rID4) $flag=1; else $flag=0;
		}
 		
		//echo "5**".$rID1.",".$rID2.",".$rID3.",".$rID4.$flag;oci_rollback($con);die;
		
		$response=$req_nos;
		if($flag==1) $msg='20'; else $msg='22';
		
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

