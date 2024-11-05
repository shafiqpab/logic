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





function getSequence($parameterArr=array()){
	$lib_department_id_string=implode(',',(array_keys($parameterArr['lib_department_id_arr']))); 
	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND entry_form = {$parameterArr['entry_form']} AND IS_DELETED = 0 order by SEQUENCE_NO";
	 //echo $sql;die;
	$sql_result=sql_select($sql);
	$dataArr=array();
	foreach($sql_result as $rows){

        $rows['DEPARTMENT']=($rows['DEPARTMENT']!='')?$rows['DEPARTMENT']:$lib_department_id_string;
		
		$dataArr['sequ_by'][$rows['SEQUENCE_NO']]=$rows;
		$dataArr['user_by'][$rows['USER_ID']]=$rows;
		$dataArr['sequ_arr'][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
	}
	

	return $dataArr;
}

function getFinalUser($parameterArr=array()){
	$lib_department_id_string=implode(',',(array_keys($parameterArr['lib_department_id_arr'])));

	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['entry_form']} AND IS_DELETED = 0  order by SEQUENCE_NO";
	   // echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){

        $rows['DEPARTMENT']=($rows['DEPARTMENT']!='')?$rows['DEPARTMENT']:$lib_department_id_string; 
    
		$usersDataArr[$rows['USER_ID']]['DEPARTMENT']=explode(',',$rows['DEPARTMENT']);
		$userSeqDataArr[$rows['USER_ID']]=$rows['SEQUENCE_NO'];
	
	}

	//print_r($parameterArr['match_data']);die;
 
	$finalSeq=array();
	foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){
		
		foreach($userSeqDataArr as $user_id=>$seq){
			if( in_array($bbtsRows['department'],$usersDataArr[$user_id]['DEPARTMENT']) ){
				$finalSeq[$sys_id][$user_id]=$seq;
			}


		}
	}

	//var_dump($finalSeq);
	//die;
	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr);
}

$department_arr=return_library_array( "select ID, DEPARTMENT_NAME from LIB_DEPARTMENT where STATUS_ACTIVE=1 and IS_DELETED=0", "ID", "DEPARTMENT_NAME"  );




if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$sequence_no='';
	$cbo_company_name=str_replace("'","",$cbo_company_name);
    $cbo_basis=str_replace("'","",$cbo_basis);
    $txt_system_id=str_replace("'","",$txt_system_id);
    $txt_date_from=str_replace("'","",trim($txt_date_from));
	$txt_date_to=str_replace("'","",trim($txt_date_to));
    $txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
    $approval_type=str_replace("'","",$cbo_approval_type);
    $cbo_department_id=str_replace("'","",$cbo_department_id);
 

    $app_user_id=($txt_alter_user_id)?$txt_alter_user_id:$user_id;


	$gate_pass_print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$cbo_company_name."' and module_id=6 and report_id=38 and is_deleted=0 and status_active=1");	
    $gate_format_ids=explode(",",$gate_pass_print_report_format);
    $print_btn=$gate_format_ids[0];


	
 
  

	if($cbo_department_id != 0){$where_con .= " and a.DEPARTMENT_ID=$cbo_department_id";}
	if($txt_system_id != ""){$where_con .= " and a.SYS_NUMBER like('%$txt_system_id')";}
	if($cbo_basis > 0){$where_con .= " and a.basis=$cbo_basis";}
	if($txt_date_from !="" && $txt_date_to!=""){$where_con .= " and a.out_date between '".$txt_date_from."' and '".$txt_date_to."'";} 
	
 
	
    
 

    $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$sample_supplier=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
    //$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
    //$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );

    
    $electronicDataArr=getSequence(array('company_id'=>$cbo_company_name,'page_id'=>$menu_id,'entry_form'=>59,'user_id'=>$app_user_id, 'lib_department_id_arr'=>$department_arr));

    //print_r($electronicDataArr);die;




	//$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted = 0");

	//$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted = 0");
	
	// if($user_sequence_no=="")
	// {
	// 	echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority.</font>";die;
	// }

    if($approval_type==0){

        //Match data..................................
		if($electronicDataArr['user_by'][$app_user_id]['DEPARTMENT']){
			$where_con .= " and a.DEPARTMENT_ID in(".$electronicDataArr['user_by'][$app_user_id]['DEPARTMENT'].",0)";
			$electronicDataArr['sequ_by'][0]['DEPARTMENT']=$electronicDataArr['user_by'][$app_user_id]['DEPARTMENT'];
		}

			
        $data_mas_sql="SELECT a.ID, a.DEPARTMENT_ID from INV_GATE_PASS_MST a
            where  a.COMPANY_ID=$cbo_company_name and a.is_deleted=0 and a.status_active=1  and a.ready_to_approved=1 and a.approved<>1 $where_con";     
          
		      //echo $data_mas_sql;die;

		$tmp_sys_id_arr=array();
		$data_mas_sql_res=sql_select( $data_mas_sql );
			
		foreach ($data_mas_sql_res as $row)
		{ 
			for($seq=($electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO']-1);$seq>=0; $seq-- ){
				
				if( (in_array($row['DEPARTMENT_ID'],explode(',',$electronicDataArr['sequ_by'][$seq]['DEPARTMENT'])))
				
				)
				{
					if($electronicDataArr['sequ_by'][$seq]['BYPASS']==1){
						$tmp_sys_id_arr[$seq][$row['ID']]=$row['ID'];
					}
					else{
						$tmp_sys_id_arr[$seq][$row['ID']]=$row['ID'];
						break;
					}
				}
			}
		}
		//..........................................Match data;
 
		//ALTER TABLE PLATFORMERPV3.INV_GATE_PASS_MST ADD (APPROVED_SEQU_BY  NUMBER(11)                  DEFAULT 0);
		
		
		$sql='';
		for($seq=0;$seq<=count($electronicDataArr['sequ_arr']); $seq++ ){
 			$sys_con = where_con_using_array($tmp_sys_id_arr[$seq],0,'a.ID');
			
			if($tmp_sys_id_arr[$seq]){
				if($sql!=''){$sql .=" UNION ALL ";}

				$sql .= "SELECT A.ID,a.WITHIN_GROUP,a.DEPARTMENT_ID,a.SENT_TO, A.SYS_NUMBER_PREFIX_NUM, A.SYS_NUMBER, A.BASIS, A.COMPANY_ID, $SELECT_YEAR(A.INSERT_DATE $YEAR_FORMAT) AS YEAR, A.OUT_DATE, 0 AS APPROVAL_ID, A.APPROVED , A.COM_LOCATION_ID ,A.CHALLAN_NO,A.RETURNABLE
				from inv_gate_pass_mst a
				where a.company_id=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and a.approved<>1 and a.APPROVED_SEQU_BY=$seq $sys_con and a.ready_to_approved=1 $basis_cond $system_id_cond $date_cond 
				group by a.id,a.WITHIN_GROUP,a.DEPARTMENT_ID,a.SENT_TO,a.sys_number_prefix_num, a.sys_number, a.basis, a.company_id, a.insert_date, a.out_date,  a.approved,a.com_location_id ,a.challan_no,a.returnable";

			}
		
		}
		
    }
    else {
        
		$sql=" SELECT A.ID,a.WITHIN_GROUP,a.DEPARTMENT_ID,a.SENT_TO, A.SYS_NUMBER_PREFIX_NUM, A.SYS_NUMBER, A.BASIS, A.COMPANY_ID, $SELECT_YEAR(A.INSERT_DATE $YEAR_FORMAT) AS YEAR,  A.OUT_DATE, A.APPROVED, 0 AS APPROVAL_ID, A.COM_LOCATION_ID,A.CHALLAN_NO,A.RETURNABLE
		from inv_gate_pass_mst a, APPROVAL_MST c 
		where a.id=c.mst_id and c.entry_form=59 and a.company_id=$cbo_company_name and a.is_deleted=0 and a.status_active=1
		and a.ready_to_approved=1 and a.approved in (1,3) and a.APPROVED_SEQU_BY=c.SEQUENCE_NO  and c.SEQUENCE_NO={$electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO']}  $where_con
		GROUP by a.id,a.WITHIN_GROUP, a.DEPARTMENT_ID,a.SENT_TO,a.sys_number_prefix_num, a.sys_number, a.basis, a.company_id, a.insert_date,  a.out_date, a.approved,  a.com_location_id, a.challan_no,a.returnable
		order by a.id";


    }

	// echo $sql;
	
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:1020px; margin-top:10px">
        <legend>Gate Pass List View</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" align="left" >
                <thead>
                	<th width="30"></th>
                    <th width="30">SL</th>
                    <th width="100">Company</th>
                    <th width="120">Gate Pass Id</th>
                    <th width="120">System Challan No</th>
                    <th width="180">Department</th>
                    <th width="150">Basis</th>
                    <th  width="150">Gate Pass Date</th>
                    <th>Supplier Name</th>
                </thead>
            </table>
            <div style="width:1020px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="left">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?						
                            $i=1;
                            $nameArray=sql_select( $sql );
                           
                            foreach ($nameArray as $row)
                            {
								//$approval_id=implode(",",array_unique(explode(",",$row[csf('approval_id')])));
								$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
								
								?>
								<tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" id="tr_<?= $i; ?>" align="center"> 
                                	<td width="30" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<?= $i;?>" />
                                        <input id="gate_id_<?= $i;?>" name="gate_id[]" type="hidden" value="<?= $row['ID']; ?>" />
                                        <input id="gatePass_id_<?= $i;?>" name="gatePass_id[]" type="hidden" value="<?= $row['ID']; ?>" />
                                        <input id="approval_id_<?= $i;?>" name="approval_id[]" type="hidden" value="<?= $approval_id; ?>" />
                                        <input id="<?= strtoupper($row['SYS_NUMBER_PREFIX_NUM']); ?>" name="no_gate_pass[]" type="hidden" value="<?= $i;?>" />
                                    </td>   
									<td width="30" align="center"><?= $i; ?></td>
									<td width="100"><?= $company_arr[$row['COMPANY_ID']]; ?></td>
                                    <td width="120" align="center"> <a href="##" onClick="generate_trims_print_report('<?= $row['COMPANY_ID']?>','<?= $row['SYS_NUMBER']?>','<?= $print_btn ?>','<?= $row['COM_LOCATION_ID']?>','<?= $row['CHALLAN_NO']?>','<?= $row['BASIS']?>','<?= $row['RETURNABLE']?>')"><?= $row['SYS_NUMBER']; ?></a></td>
									<td width="120"><?=$row['CHALLAN_NO']; ?></td>

									<td width="180" align="center"><?= $department_arr[$row['DEPARTMENT_ID']]; ?></td>                             
                                    <td width="150" align="center"><?= $get_pass_basis[$row['BASIS']]; ?></td>
                                    <td width="150" align="center"><?= change_date_format($row['OUT_DATE']); ?></td>
                                     <td ><? if($row[csf('WITHIN_GROUP')]==1){
									?>
										<?= $company_arr[$row['SENT_TO']]; ?>
									<?
									}
									
									else {echo $row['SENT_TO'];}
									?></td>		
								</tr>
								<?
								$i++;
							}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="730" class="rpt_table" align="left">
				<tfoot>
                    <td width="30" align="center" >
						<input type="checkbox" id="all_check" onclick="check_all('all_check')" />
					</td>
                    <td colspan="2" align="left">
						<input type="button" value="<? if($approval_type==1) echo "Un-Approved"; else echo "Approved"; ?>" class="formbutton" style="width:100px" onclick="submit_approved(<?= $i; ?>,<?= $approval_type; ?>)"/>
						<? if($approval_type==0){ ?>
						<input type="button" value="Deny" class="formbutton" style="width:100px;" onClick="submit_approved(<?=$i; ?>,5);"/>
						<? } ?>
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

	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
    $approval_type=str_replace("'","",$approval_type);
    $cbo_company_name=str_replace("'","",$cbo_company_name);
    $gatePass_ids=str_replace("'","",$gatePass_ids);

    $app_user_id=($txt_alter_user_id)?$txt_alter_user_id:$user_id;

	$sql="SELECT a.ID, a.DEPARTMENT_ID,a.READY_TO_APPROVED from inv_gate_pass_mst a  where a.COMPANY_ID=$cbo_company_name and a.is_deleted=0 and a.status_active=1  and a.id in($gatePass_ids)";
	//echo $sql;die; 

    $sqlResult=sql_select( $sql );
    foreach ($sqlResult as $row)
    {
        if($row['READY_TO_APPROVED'] != 1 ){echo "Ready to approved NO is not allow";die;}
		$matchDataArr[$row['ID']]=array('buyer'=>0,'brand'=>0,'item'=>0,'store'=>0,'department'=>$row['DEPARTMENT_ID']);
    } 
   
    $finalDataArr=getFinalUser(array('company_id'=>$cbo_company_name,'page_id'=>$menu_id,'entry_form'=>59,'lib_buyer_arr'=>0,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0,'lib_department_id_arr'=>$department_arr,'match_data'=>$matchDataArr));
     //print_r($finalDataArr);die;
		
    $sequ_no_arr_by_sys_id = $finalDataArr['final_seq'];
    $user_sequence_no = $finalDataArr['user_seq'][$app_user_id];


	$msg=''; $flag=''; $response='';	
	if($approval_type==0)
	{

        $max_approved_no_arr = return_library_array("SELECT mst_id, max(approved_no) as approved_no from approval_history where mst_id in($gatePass_ids) and entry_form=59 group by mst_id","mst_id","approved_no");
		
		$id=return_next_id( "id","approval_history", 1 ) ;
		$appid=return_next_id( "id","approval_mst", 1 ) ;

		$gatePass_ids_all=explode(",",$gatePass_ids);
		//$officeNote_nos_all=explode(",",$officeNote_nos);
		
		// ======================================================================== New
		for($i=0;$i<count($gatePass_ids_all);$i++)
		{
		
			$gatePass_id=$gatePass_ids_all[$i];
			$approved_no=$max_approved_no_arr[$gatePass_id]+1;
 			//History data.......................
			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",59,".$gatePass_id.",".$approved_no.",'".$user_sequence_no."',1,".$app_user_id.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')"; 
			$id=$id+1;

			//App mst data.......................
			if($app_data_array!=''){$app_data_array.=",";}
			$app_data_array.="(".$appid.",59,".$gatePass_id.",".$user_sequence_no.",".$app_user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
			$appid++;

			//Update mst data...........................
			$approved=(max($finalDataArr['final_seq'][$gatePass_id])==$user_sequence_no)?1:3;
			$mst_data_array_up[$gatePass_id] = explode(",",("".$approved.",".$user_sequence_no.",".$app_user_id.",'".$pc_date_time."'")); 

		}

		
		 //echo $gatePass_ids;die;
		$flag=1;

		if($flag==1) 
		{
			$mst_field_array_up="approved*approved_sequ_by*APPROVED_BY*APPROVED_DATE"; 
            $rID=execute_query(bulk_update_sql_statement( "inv_gate_pass_mst", "id", $mst_field_array_up, $mst_data_array_up, $gatePass_ids_all ));
            if($rID) $flag=1; else $flag=0; 
		}

 	 
		if($flag==1) 
		{
			$query="update approval_history set current_approval_status=0  WHERE entry_form=59 and mst_id in ($gatePass_ids)";
			$rIDapp=execute_query($query,1);
			if($rIDapp) $flag=1; else $flag=0; 
		} 

		if($flag==1) 
		{	
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date"; 
			$rID2=sql_insert("approval_history",$field_array,$data_array,1);
			if($rID2) $flag=1; else $flag=0; 
		}
	

		if($flag==1) 
		{
			$field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
			$rID3=sql_insert("approval_mst",$field_array,$app_data_array,0);
			if($rID3) $flag=1; else $flag=0; 
		}

		if($flag==1) $msg='19'; else $msg='21';

	}
	else if($approval_type==5){
		
		$rID=sql_multirow_update("inv_gate_pass_mst","APPROVED*READY_TO_APPROVED*APPROVED_SEQU_BY","2*0*0","id",$gatePass_ids,1);
		if($rID) $flag=1; else $flag=0;

		if($flag==1) 
		{
			$data=$app_user_id."*'".$pc_date_time."'*".$user_id."*'".$pc_date_time."'*0";
			$rID1=sql_multirow_update("approval_history","un_approved_by*un_approved_date*updated_by*update_date*current_approval_status",$data,"mst_id",$gatePass_ids,1,1);
			if($rID1) $flag=1; else $flag=0; 
		}

		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=59 and mst_id in ($gatePass_ids)";
			$rID2=execute_query($query,1); 
			if($rID2) $flag=1; else $flag=0; 
		}


		if($flag==1) $msg='37'; else $msg='5';
	}
	else
	{

		$rID=sql_multirow_update("inv_gate_pass_mst","APPROVED*READY_TO_APPROVED*APPROVED_SEQU_BY","0*0*0","id",$gatePass_ids,1);
		if($rID) $flag=1; else $flag=0;

		if($flag==1) 
		{
			$data=$app_user_id."*'".$pc_date_time."'*".$user_id."*'".$pc_date_time."'*0";
			$rID1=sql_multirow_update("approval_history","un_approved_by*un_approved_date*updated_by*update_date*current_approval_status",$data,"mst_id",$gatePass_ids,1,1);
			if($rID1) $flag=1; else $flag=0; 
		}

		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=59 and mst_id in ($gatePass_ids)";
			$rID2=execute_query($query,1); 
			if($rID2) $flag=1; else $flag=0; 
		}

		//echo $flag;oci_rollback($con); ;die;	
	 
		if($flag==1) $msg='20'; else $msg='22';
	}
	
	
	
	
	if($flag==1)
	{
		oci_commit($con);  
		echo $msg."**".$gatePass_ids;
	}
	else
	{
		oci_rollback($con); 
		echo $msg."**".$gatePass_ids;
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
			 $Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');
			
			$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation,b.sequence_no from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name  and valid=1 and a.id!=$user_id  and b.is_deleted=0  and b.entry_form=59 order by sequence_no";
			$arr=array (2=>$custom_designation,3=>$Department);

			 echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department,App. Seq", "100,120,130,140,30","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id,sequence_no", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
			?>
	        
	</form>
	<script language="javascript" type="text/javascript">
	  setFilterGrid("tbl_style_ref");
	</script>
	<?
}

?>