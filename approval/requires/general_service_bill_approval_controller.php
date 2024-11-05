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
	$lib_buyer_id_string=implode(',',(array_keys($parameterArr['lib_buyer_arr']))); 
	$lib_brand_id_string=implode(',',(array_keys($parameterArr['lib_brand_arr']))); 
	$lib_item_cat_id_string=implode(',',(array_keys($parameterArr['lib_item_cat_arr']))); 
	//User data.....................
	$sql_user="SELECT ID,BUYER_ID,BRAND_ID,ITEM_CATE_ID as ITEM_ID,IS_DATA_LEVEL_SECURED,store_location_id as STORE_ID FROM USER_PASSWD WHERE VALID=1";
	$sql_user_result=sql_select($sql_user);
	$userDataArr=array();
	foreach($sql_user_result as $rows){
		$userDataArr[$rows['ID']]=$rows;
	}
	
	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND PAGE_ID = {$parameterArr['page_id']} AND IS_DELETED = 0 order by SEQUENCE_NO";
	 //echo $sql;die;
	$sql_result=sql_select($sql);
	$dataArr=array();
	foreach($sql_result as $rows){
		//$rows['STORE_ID']=$userDataArr[$rows['USER_ID']]['STORE_ID'];
		$rows['ITEM_ID']=$userDataArr[$rows['USER_ID']]['ITEM_ID'];
		
        $rows['ITEM_ID']=($rows['ITEM_ID']!='')?$rows['ITEM_ID']:$lib_item_cat_id_string;
		$rows['BUYER_ID']=($rows['BUYER_ID']!='')?$rows['BUYER_ID']:$lib_buyer_id_string;
		$rows['BRAND_ID']=($rows['BRAND_ID']!='')?$rows['BRAND_ID']:$lib_brand_id_string;
		
		$dataArr['sequ_by'][$rows['SEQUENCE_NO']]=$rows;
		$dataArr['user_by'][$rows['USER_ID']]=$rows;
		$dataArr['sequ_arr'][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
	}
	

	return $dataArr;
}

function getFinalUser($parameterArr=array()){
	$lib_buyer_id_string=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));
	$lib_brand_id_string=implode(',',(array_keys($parameterArr['lib_brand_arr'])));
	$lib_item_cat_id_string=implode(',',(array_keys($parameterArr['lib_item_cat_arr'])));

	//User data.....................
	$sql_user="SELECT ID,BUYER_ID,BRAND_ID,ITEM_CATE_ID as ITEM_ID,store_location_id as STORE_ID,IS_DATA_LEVEL_SECURED FROM USER_PASSWD WHERE VALID=1";
	$sql_user_result=sql_select($sql_user);
	$userDataArr=array();
	foreach($sql_user_result as $rows){
		$userDataArr[$rows['ID']]['BUYER_ID']=$rows['BUYER_ID'];
		$userDataArr[$rows['ID']]['BRAND_ID']=$rows['BRAND_ID'];
		$userDataArr[$rows['ID']]['ITEM_ID']=$rows['ITEM_ID'];
		$userDataArr[$rows['ID']]['STORE_ID']=$rows['STORE_ID'];
	}
	
	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND PAGE_ID = {$parameterArr['page_id']} AND IS_DELETED = 0  order by SEQUENCE_NO";
	   //echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){

        $rows['ITEM_ID']=$userDataArr[$rows['USER_ID']]['ITEM_ID'];
		
        $rows['ITEM_ID']=($rows['ITEM_ID']!='')?$rows['ITEM_ID']:$lib_item_cat_id_string;
		$rows['BUYER_ID']=($rows['BUYER_ID']!='')?$rows['BUYER_ID']:$lib_buyer_id_string;
		$rows['BRAND_ID']=($rows['BRAND_ID']!='')?$rows['BRAND_ID']:$lib_brand_id_string;  
        
		
		$usersDataArr[$rows['USER_ID']]['ITEM_ID']=explode(',',$rows['ITEM_ID']);
		$usersDataArr[$rows['USER_ID']]['BUYER_ID']=explode(',',$rows['BUYER_ID']);
		$usersDataArr[$rows['USER_ID']]['BRAND_ID']=explode(',',$rows['BRAND_ID']);
		
		$userSeqDataArr[$rows['USER_ID']]=$rows['SEQUENCE_NO'];
	
	}
 
	$finalSeq=array();
	foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){
		foreach($userSeqDataArr as $user_id=>$seq){
			$finalSeq[$sys_id][$user_id]=$seq;
		}
	}

	//var_dump($finalSeq);
	//die;
	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr);
}




if($action=="report_generate")
{
	$process = array( &$_POST );

	extract(check_magic_quote_gpc( $process )); 
	$cbo_company_name=str_replace("'","",$cbo_company_name);
    $txt_bill_no=str_replace("'","",$txt_bill_no);
    $txt_date_from=str_replace("'","",trim($txt_date_from));
	$txt_date_to=str_replace("'","",trim($txt_date_to));
	$approval_type=str_replace("'","",$cbo_approval_type);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
    $app_user_id=($txt_alter_user_id)?$txt_alter_user_id:$user_id;


	if($txt_bill_no){$where_con .= " and a.bill_no like('%".$txt_bill_no."')"; }
    if($txt_date_from !="" && $txt_date_to!=""){
		$where_con .=" and a.bill_date between '".$txt_date_from."' and '".$txt_date_to."'";
	}


    $company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
    $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
    //$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );

	//$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=19 and report_id=206 and is_deleted=0 and status_active=1");
    //$format_ids=explode(",",$print_report_format);

	//$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted = 0");
    //print_r($user_sequence_no);
	//$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted = 0");
	

	

	$electronicDataArr=getSequence(array('company_id'=>$cbo_company_name,'page_id'=>$menu_id,'user_id'=>$app_user_id,'lib_buyer_arr'=>0,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0,'lib_department_id_arr'=>0));


	if($approval_type==0) 
	{
		
		//Match data..................................
			
          $data_mas_sql="SELECT a.ID from subcon_outbound_bill_mst a
            where a.company_id=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and a.READY_TO_APPROVE=1 and a.is_approved<>1 $where_con";     
          		
			 //echo $data_mas_sql;die;
			$tmp_sys_id_arr=array();
			$data_mas_sql_res=sql_select( $data_mas_sql );
			foreach ($data_mas_sql_res as $row)
			{ 
				for($seq=($electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO']-1);$seq>=0; $seq-- ){
					
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


			$sql='';
			for($seq=0;$seq<=count($electronicDataArr['sequ_arr']); $seq++ ){
				$sys_con = where_con_using_array($tmp_sys_id_arr[$seq],0,'a.ID');
				
				if($tmp_sys_id_arr[$seq]){
					if($sql!=''){$sql .=" UNION ALL ";}
					$sql .= "SELECT a.ID,a.SUPPLIER_ID, a.ENTRY_FORM, a.PREFIX_NO, a.PREFIX_NO_NUM, a.BILL_NO,a.SERVICE_WO_NUM, a.COMPANY_ID, a.LOCATION_ID, a.BILL_DATE, a.SUPPLIER_ID, a.PAY_MODE,a.EXCHANGE_RATE, a.PARTY_BILL_NO,a.TRANS_FROM_DATE,a.CURRENCY_ID,a.WO_NON_ORDER_INFO_MST_ID,a.TENOR,a.REMARKS,a.IS_POSTED_ACCOUNT FROM subcon_outbound_bill_mst a  WHERE  a.company_id=$cbo_company_name and a.entry_form=483 and a.is_deleted=0 and a.status_active=1 and a.ready_to_approve=1  and a.is_approved<>1 and a.APPROVED_SEQU_BY=$seq $sys_con";	 
				}
			
			}
	 
	
	}
	else
	{
		 
        $sql="SELECT a.ID,a.SUPPLIER_ID, a.ENTRY_FORM, a.PREFIX_NO, a.PREFIX_NO_NUM, a.BILL_NO,a.SERVICE_WO_NUM, a.COMPANY_ID, a.LOCATION_ID, a.BILL_DATE, a.SUPPLIER_ID, a.PAY_MODE,a.EXCHANGE_RATE, a.PARTY_BILL_NO,a.TRANS_FROM_DATE,a.CURRENCY_ID,a.WO_NON_ORDER_INFO_MST_ID,a.TENOR,a.REMARKS,a.IS_POSTED_ACCOUNT  FROM subcon_outbound_bill_mst a, APPROVAL_MST c 
        WHERE a.id=c.mst_id and c.entry_form=71 and a.company_id=$cbo_company_name and a.entry_form=483  and a.status_active=1 and a.is_deleted=0  and a.ready_to_approve=1 and a.is_approved in (1,3) and a.APPROVED_SEQU_BY=c.SEQUENCE_NO  and c.SEQUENCE_NO={$electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO']}  $where_con";

		
		//echo $sql;die;
	}
	 //echo $sql; 
    

     $nameArray=sql_select($sql);

	$tWidth=520;
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:<?=$tWidth+20;?>px; margin-top:10px">
        <legend>General Service Bill List View</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?=$tWidth+20;?>" class="rpt_table" >
                <thead>
                	<th width="50"></th>
                    <th width="40">SL</th>
                    <th width="120">Bill No</th>
                    <th width="120">WO Num</th>
                    <th width="120">Supplier</th>
                    <th>Bill Date</th>
                </thead>
            </table>
            <div style="width:<?=$tWidth+20;?>px; overflow-y:scroll; max-height:400px;" id="buyer_list_view">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?=$tWidth;?>" class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
                        <?						
                            $i=1;
                            foreach ($nameArray as $row)
                            {
                            ?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" align="center">
                                	<td width="50" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="target_id_<? echo $i;?>" name="target_id[]" type="hidden" value="<?=$row['ID']; ?>" />
                                    </td>   
									<td width="40" align="center"><?echo $i;?></td>
                                    <td width="120" align="center"><a href="#" onclick="generate_bill_entry_report('<? echo $row[csf('company_id')]; ?>','<? echo $row[csf('id')]; ?>','<? echo $row[csf('bill_no')]; ?>','General Service Bill Entry','general_service_bill_entry_print');"><? echo $row[csf('bill_no')]; ?></a></td>
                                    <td width="120" align="center"><? echo $row['SERVICE_WO_NUM']; ?></td>
                                    <td width="120" align="center"><?= $supplier_arr[$row['SUPPLIER_ID']]; ?></td>
                                    <td align="center"><? echo $row[csf('bill_date')]; ?></td>
								</tr>
                            <?
								$i++;
							}
                           ?>
								
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="<?=$tWidth+20;?>" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" ><input type="checkbox" id="all_check" onclick="check_all('all_check')" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approved"; else echo "Approved"; ?>" class="formbutton" style="width:100px" onclick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/></td>
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

	$cbo_company_name=str_replace("'","",$cbo_company_name);
    $txt_bill_no=str_replace("'","",$txt_bill_no);
    $txt_date_from=str_replace("'","",trim($txt_date_from));
	$txt_date_to=str_replace("'","",trim($txt_date_to));
	$approval_type=str_replace("'","",$cbo_approval_type);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$target_ids=str_replace("'","",$target_ids);
    $app_user_id=($txt_alter_user_id)?$txt_alter_user_id:$user_id;

	//$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","page_id=$menu_id and user_id=$user_id_approval and company_id = $cbo_company_name and is_deleted = 0");
	// echo $user_sequence_no;die;

	//$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","page_id=$menu_id and is_deleted = 0");



	
	$sql="SELECT a.ID from subcon_outbound_bill_mst a  where a.company_id=$cbo_company_name and a.is_deleted=0 and a.status_active=1  and a.id in($target_ids)";    

    $sqlResult=sql_select( $sql );
    foreach ($sqlResult as $row)
    {
        $matchDataArr[$row['ID']]=array('buyer'=>0,'brand'=>0,'item'=>0,'store'=>0,'department'=>0);
    } 
   
    $finalDataArr=getFinalUser(array('company_id'=>$cbo_company_name,'page_id'=>$menu_id,'lib_buyer_arr'=>0,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0,'lib_department_id_arr'=>0,'match_data'=>$matchDataArr));
	
	$sequ_no_arr_by_sys_id =$finalDataArr['final_seq'];
    $user_sequence_no = $finalDataArr['user_seq'][$app_user_id];

	if($approval_type==0)
	{
		 
		$target_app_id_arr = explode(",",$target_ids);

        $approved_no_arr=return_library_array( "select mst_id, max(approved_no) as approved_no from approval_history and entry_form=71 and mst_id in($target_ids)", "mst_id", "approved_no"  );
        //print_r($approved_no_arr);die;
        

        $id = return_next_id( "id","approval_history", 1) ;
        $app_mst_id=return_next_id( "id","approval_mst", 1 ) ;
        $data_array="";$mst_data_array="";
		foreach($target_app_id_arr as $mst_id)
		{
			$approved_no=$approved_no_arr[$mst_id]+1;
            $approved_no_array[$mst_id] = $approved_no;
            //History......................
			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",71,".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$app_user_id.",'".$pc_date_time."','".$user_ip."','".$app_instru."',".$user_id.",'".$pc_date_time."')"; 
			$id++;	
            //App mst...............................
            if($mst_data_array!=''){$mst_data_array.=",";}
			$mst_data_array.="(".$app_mst_id.",71,".$mst_id.",".$user_sequence_no.",".$app_user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
			$app_mst_id++;
            
            //Mst data...........................
			$approved=(max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no)?1:3;
			$mst_data_array_up[$mst_id] =explode(",",("".$approved.",".$user_sequence_no."")); 

		}
		//echo $mst_data_array;die;

		$flag=1;

		if($flag==1) 
		{
			$mst_field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,inserted_by,insert_date,user_ip";
            $rID1=sql_insert("approval_mst",$mst_field_array,$mst_data_array,0);
            if($rID1) $flag=1; else $flag=0; 
		}

        if($flag==1) 
        {
            $rID2=execute_query("UPDATE approval_history SET current_approval_status=0 WHERE entry_form=71 and mst_id in ($target_ids)",1);
            if($rID2) $flag=1; else $flag=0; 
        }

        if($flag==1) 
        {
            $field_array = "id, entry_form, mst_id, approved_no,sequence_no, current_approval_status, approved_by, approved_date, user_ip,comments,inserted_by,insert_date"; 
            $rID3=sql_insert("approval_history",$field_array,$data_array,0);
            if($rID3) $flag=1; else $flag=0; 
            
        }

		if($flag==1) 
		{
			$mst_field_array_up="is_approved*approved_sequ_by"; 
            $rID4=execute_query(bulk_update_sql_statement( "subcon_outbound_bill_mst", "id", $mst_field_array_up, $mst_data_array_up, $target_app_id_arr ));
            if($rID4) $flag=1; else $flag=0; 
		}


        //echo  "0**21,".$rID1.','.$rID2.','.$rID3.','.$rID4. ','.$rID6;oci_rollback($con);die;

	}
	else if($approval_type==1)
	{
		
		$history_data_arr=return_library_array( "select id, id from approval_history where current_approval_status=1 and entry_form=71 and mst_id in ($target_ids)",'id','id');
		$app_ids= implode(',',$history_data_arr);
		
		$flag=1;
		
		if($flag==1) 
		{
			$rID1=sql_multirow_update("subcon_outbound_bill_mst","is_approved*READY_TO_APPROVE*APPROVED_SEQU_BY","0*0*0","id",$target_ids,0); 
            if($rID1) $flag=1; else $flag=0; 
		}

 
		
		if($flag==1) 
		{
			$rID2=execute_query("delete from approval_mst  WHERE entry_form=71 and mst_id in ($target_ids)",1); 
            if($rID2) $flag=1; else $flag=0; 
		}
		
		
		if($flag==1) 
		{
			$rID3=execute_query("UPDATE approval_history SET current_approval_status=0,IS_SIGNING=0 WHERE entry_form=71 and mst_id in ($target_ids)",1);
            if($rID3) $flag=1; else $flag=0; 
		} 
			

		if($flag==1) 
		{
            $data=$app_user_id."*'".$pc_date_time."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
            $rID4=sql_multirow_update("approval_history","un_approved_by*un_approved_date*updated_by*update_date",$data,"id",$app_ids,1);
            if($rID4) $flag=1; else $flag=0; 
		} 

       // echo  "0**21,".$rID1.','.$rID2.','.$rID3.','.$rID4;oci_rollback($con);die;	
	}
	elseif($approval_type == 5){

    }
	
	if($flag==1)
	{
		oci_commit($con);  
		echo "19**".$target_ids;
	}
	else
	{
		oci_rollback($con); 
		echo "21**".$target_ids;
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
			 	//$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_importer_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
				$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and valid=1 and a.id!=$user_id  and b.is_deleted=0  and b.page_id=$menu_id order by sequence_no";
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