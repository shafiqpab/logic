<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_ip=$_SESSION['logic_erp']['user_ip'];

extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');
if($_SESSION['app_notification'] == 1){
	include('../../includes/class4/Notifications.php');
	$notification = new Notifications();
}

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];



 
function getSequence($parameterArr=array()){
	$lib_store_id_string=implode(',',(array_keys($parameterArr['lib_store_arr']))); 
	$lib_department_id_string=implode(',',(array_keys($parameterArr['lib_department_id_arr']))); 
	$lib_item_cat_id_string=implode(',',(array_keys($parameterArr['lib_item_cat_arr']))); 
	$lib_location_id_string=implode(',',(array_keys($parameterArr['lib_location_id_arr']))); 
	//User data.....................
	$sql_user="SELECT ID,store_location_id as STORE_ID FROM USER_PASSWD WHERE VALID=1";
	$sql_user_result=sql_select($sql_user);
	$userDataArr=array();
	foreach($sql_user_result as $rows){
		$userDataArr[$rows['ID']]=$rows;
	}
	
	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT as DEPARTMENT_ID,ITEM_CATEGORY as ITEM_ID,LOCATION as LOCATION_ID,GROUP_NO FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND PAGE_ID = {$parameterArr['page_id']} AND ENTRY_FORM = {$parameterArr['entry_form']} AND IS_DELETED = 0 order by SEQUENCE_NO";
	//echo $sql;die;
	$sql_result=sql_select($sql);
	$dataArr=array();
	foreach($sql_result as $rows){
		$rows['STORE_ID'] = $userDataArr[$rows['USER_ID']]['STORE_ID'];

		if($rows['ITEM_ID'] == ''){$rows['ITEM_ID'] = $lib_item_cat_id_string;}
		if($rows['DEPARTMENT_ID'] == ''){$rows['DEPARTMENT_ID'] = $lib_department_id_string;}
		if($rows['STORE_ID'] == ''){$rows['STORE_ID'] = $lib_store_id_string;}
		if($rows['LOCATION_ID'] == ''){$rows['LOCATION_ID'] = $lib_location_id_string;}
		

        $dataArr['user_by'][$rows['USER_ID']] = $rows;
		$dataArr['sequ_by'][$rows['SEQUENCE_NO']] = $rows;

		$dataArr['group_user_by'][$rows['GROUP_NO']][$rows['USER_ID']] = $rows;
		$dataArr['group_arr'][$rows['GROUP_NO']] = $rows['GROUP_NO'];
		$dataArr['group_seq_arr'][$rows['GROUP_NO']][$rows['SEQUENCE_NO']] = $rows['SEQUENCE_NO'];
		$dataArr['group_by_seq_arr'][$rows['SEQUENCE_NO']] = $rows['GROUP_NO'];

		$dataArr['bypass_seq_arr'][$rows['BYPASS']][$rows['SEQUENCE_NO']] = $rows['SEQUENCE_NO'];
		$dataArr['group_bypass_arr'][$rows['GROUP_NO']][$rows['BYPASS']] = $rows['BYPASS'];
	}
	
	return $dataArr;
}

function getFinalUser($parameterArr=array()){
	$lib_buyer_arr=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));
	$lib_brand_arr=implode(',',(array_keys($parameterArr['lib_brand_arr'])));
	$lib_store_arr=implode(',',(array_keys($parameterArr['lib_store_arr'])));
	$lib_department_id_string=implode(',',(array_keys($parameterArr['lib_department_id_arr']))); 
	$lib_location_id_string=implode(',',(array_keys($parameterArr['lib_location_id_arr'])));
	$lib_item_cat_id_string=implode(',',(array_keys($parameterArr['lib_item_cat_arr']))); 



	//User data.....................
	$sql_user="SELECT ID,STORE_LOCATION_ID as STORE_ID FROM USER_PASSWD WHERE VALID=1";
	$sql_user_result=sql_select($sql_user);
	$userDataArr=array();
	foreach($sql_user_result as $rows){
		$userDataArr[$rows['ID']]['STORE_ID']=$rows['STORE_ID'];
	}
	
	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT as DEPARTMENT_ID,LOCATION as LOCATION_ID ,ITEM_CATEGORY as ITEM_ID,GROUP_NO FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND PAGE_ID = {$parameterArr['page_id']} AND ENTRY_FORM = {$parameterArr['entry_form']} AND IS_DELETED = 0  order by SEQUENCE_NO";
	   //echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){


		if($userDataArr[$rows['USER_ID']]['STORE_ID']==''){
			$userDataArr[$rows['USER_ID']]['STORE_ID']=$lib_store_arr;
		}
		if($rows['DEPARTMENT_ID']==''){
			$rows['DEPARTMENT_ID']=$lib_department_id_string;
		}
		if($rows['LOCATION_ID']==''){
			$rows['LOCATION_ID']=$lib_location_id_string;
		}
		if($rows['ITEM_ID']==''){$rows['ITEM_ID']=$lib_item_cat_id_string;}

		
		$usersDataArr[$rows['USER_ID']]['STORE_ID']=explode(',',$userDataArr[$rows['USER_ID']]['STORE_ID']);
		$usersDataArr[$rows['USER_ID']]['DEPARTMENT_ID']=explode(',',$rows['DEPARTMENT_ID']);
		$usersDataArr[$rows['USER_ID']]['ITEM_ID']=explode(',',$rows['ITEM_ID']);
		$usersDataArr[$rows['USER_ID']]['LOCATION_ID']=explode(',',$rows['LOCATION_ID']);
		$userSeqDataArr[$rows['USER_ID']]=$rows['SEQUENCE_NO'];

        $userGroupDataArr[$rows['USER_ID']]=$rows['GROUP_NO'];
		$groupBypassNoDataArr[$rows['GROUP_NO']][$rows['BYPASS']][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
	
	}
	//echo '<pre>';print_r($usersDataArr);
	//echo '<pre>';print_r($parameterArr[match_data]);
	$finalSeq=array();
	foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){
		
		foreach($userSeqDataArr as $user_id=>$seq){

			$validation_check = true;
			if($parameterArr['category_mixing'] == 2){
				if(in_array($bbtsRows['item'],$usersDataArr[$user_id]['ITEM_ID']) || $bbtsRows['item']==0){$validation_check = true;}
				else{$validation_check = false;}
			}

			if(
				in_array($bbtsRows['store'],$usersDataArr[$user_id]['STORE_ID'])
				&& (in_array($bbtsRows['department'],$usersDataArr[$user_id]['DEPARTMENT_ID']) || $bbtsRows['department']==0)
				&& (in_array($bbtsRows['location_id'],$usersDataArr[$user_id]['LOCATION_ID']) || $bbtsRows['location_id']==0)
				&& ($validation_check == true)
				&&  $bbtsRows['store']>0
			){
				$finalSeq[$sys_id][$user_id]=$seq;
			}


		}
	}

	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr,'user_group'=>$userGroupDataArr,'group_bypass_no_data_arr'=>$groupBypassNoDataArr);
}




if ($action=="load_drop_down_store")
{
	$permitted_store_id=return_field_value("STORE_LOCATION_ID","user_passwd","id='".$user_id."'");
	if($permitted_store_id){$storCon=" and id in($permitted_store_id)";}
	echo create_drop_down( "cbo_store_id", 130, "select id,store_name from lib_store_location where status_active=1 and is_deleted=0 and company_id=$data $storCon order by store_name","id,store_name", 1, "-- All --","","load_drop_down( 'requires/purchase_requisition_approval_group_by_controller', this.value, 'load_drop_down_item', 'category_id' );",0,"","","","");
	exit();
}


if ($action=="load_drop_down_item")
{
	$permitted_buyer_id=return_field_value("ITEM_CATEGORY_ID","lib_store_location","id='".$data."'");
	if($permitted_buyer_id){$buyerCon=" and id in($permitted_buyer_id)";}
	echo create_drop_down( "cbo_item_category_id", 130, "select buy.category_id, buy.short_name from lib_item_category_list buy where buy.status_active =1 and buy.is_deleted=0 $buyerCon","category_id,short_name", 1, "-- All --","", "");
	exit();
}


if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
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
		// $sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$company_id  and a.id!=$user_id  and b.is_deleted=0  and b.entry_form=1 order by b.sequence_no";

		$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation,b.SEQUENCE_NO,b.GROUP_NO from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$company_id  and valid=1 and a.id!=$user_id  and a.is_deleted=0 and b.is_deleted=0 and b.entry_form=1 order by b.sequence_no";
		// 	 echo $sql;die;
		$arr=array (2=>$custom_designation,3=>$department_arr);
		echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department,Seq. no,Group no", "100,120,150,180,50,50","730","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id,sequence_no,group_no", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
		?>
	</form>
	<?
	exit();
}







function getData($ref_id,$user_id='')
{
	$sql = "SELECT A.ID,A.REQU_NO,B.COMPANY_NAME,C.STORE_NAME,A.REQUISITION_DATE,D.USER_FULL_NAME,A.DELIVERY_DATE,E.LOCATION_NAME,A.REQU_PREFIX_NUM,SUM (X.AMOUNT) AS AMOUNT,LISTAGG (Y.SHORT_NAME, ',') AS CATEGORY, A.PRIORITY_ID
			FROM INV_PURCHASE_REQUISITION_MST A
					LEFT JOIN INV_PURCHASE_REQUISITION_DTLS X ON A.ID = X.MST_ID AND X.IS_DELETED = 0
					LEFT JOIN LIB_ITEM_CATEGORY_LIST Y ON X.ITEM_CATEGORY = Y.CATEGORY_ID
					LEFT JOIN USER_PASSWD D ON A.INSERTED_BY = D.ID
					LEFT JOIN LIB_COMPANY B ON A.COMPANY_ID = B.ID
					LEFT JOIN LIB_STORE_LOCATION C ON A.STORE_NAME = C.ID
					LEFT JOIN LIB_LOCATION E ON A.LOCATION_ID = E.ID
			WHERE A.ID =  $ref_id
			GROUP BY A.ID, A.REQU_NO, B.COMPANY_NAME, C.STORE_NAME, A.REQUISITION_DATE, D.USER_FULL_NAME, A.DELIVERY_DATE, E.LOCATION_NAME, A.REQU_PREFIX_NUM, A.PRIORITY_ID";

	$result = sql_select($sql);

	$data_arr = array();

	$menu_id=return_field_value("page_id as menu_id","ELECTRONIC_APPROVAL_SETUP","entry_form=1","menu_id");
	if(!empty($user_id))
	{
	  $USER_FULL_NAME = return_field_value("USER_FULL_NAME","USER_PASSWD","id=$user_id","USER_FULL_NAME");
	}

	foreach ($result as $row)
	{
		if(!empty( $USER_FULL_NAME))  $USER_FULL_NAME ="\nForwarded By : ".$USER_FULL_NAME;
		else $USER_FULL_NAME ="\nInserted By : ".$row['USER_FULL_NAME'];
		$desc = "Company: ".$row['COMPANY_NAME'].", Loc: ".$row['LOCATION_NAME']."\nReq. No: ".$row['REQU_PREFIX_NUM'].", Req. date: ".date('d/m/Y',strtotime($row['REQUISITION_DATE']))."\nPriority: ".$priority_array[$row['PRIORITY_ID']].", Store : ".$row['STORE_NAME']."\nCategory: ".implode(",",array_unique(explode(",",$row['CATEGORY'])))."\nReq. For : ".$row['REQ_FOR']."\nReq. Value: ".number_format($row['AMOUNT'],2,".","").$USER_FULL_NAME;
		$data_arr = array(
			'ID' => $row['ID'],
			'DATE' => date('d/m/Y',strtotime($row['REQUISITION_DATE'])),
			'DELIVERY_DATE' => date('d/m/Y',strtotime($row['DELIVERY_DATE'])),
			'COMPANY' => $row['COMPANY_NAME'],
			'BUYER' => '',
			'SYS_NUMBER' => $row['REQU_NO'],
			'SYS_DEF' => '',
			'DESC' => $desc,
			'MENU_ID' => $menu_id
		);
	}
	
	return $data_arr;
}

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
//$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name"  );
$item_cat_arr=return_library_array( "select id, SHORT_NAME from LIB_ITEM_CATEGORY_LIST", "id", "SHORT_NAME"  );
$lib_store_arr=return_library_array( "select id, STORE_NAME from LIB_STORE_LOCATION", "id", "STORE_NAME"  );
$department_arr=return_library_array( "SELECT ID,DEPARTMENT_NAME FROM LIB_DEPARTMENT WHERE STATUS_ACTIVE=1 AND IS_DELETED=0",'ID','DEPARTMENT_NAME');
$location_arr=return_library_array( "SELECT ID,LOCATION_NAME FROM LIB_LOCATION WHERE STATUS_ACTIVE=1 AND IS_DELETED=0",'ID','LOCATION_NAME');

if($action=="report_generate")
{
	?>
	<script>
		function openmypage_app_cause(wo_id,app_type,i)
		{
			var txt_appv_cause = $("#txt_appv_cause_"+i).val();
			var approval_id = $("#approval_id_"+i).val();
			var data=wo_id+"_"+app_type+"_"+txt_appv_cause+"_"+approval_id+"_"+$("#txt_alter_user_id").val();
			var title = 'Approval Cause Info';
			var page_link = 'requires/purchase_requisition_approval_group_by_controller.php?data='+data+'&action=appcause_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=520px,height=250px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var appv_cause=this.contentDoc.getElementById("appv_cause");
				$('#txt_appv_cause_'+i).val(appv_cause.value);
			}
		}

		function openmypage_reqdetails(requ_id,requ_no)
		{
			var data=requ_id+"**"+requ_no;
			var title = 'Requisition Details Info';
			var page_link = 'requires/purchase_requisition_approval_group_by_controller.php?data='+data+'&action=reqdetails_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=250px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				/*var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
				$('#txt_appv_cause_'+i).val(appv_cause.value);*/
			}
		}
	</script>
	<?
	
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_name = str_replace("'","",$cbo_company_name);
	$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
	
	$cbo_item_category_id = str_replace("'","",$cbo_item_category_id);
	$cbo_store_id = str_replace("'","",$cbo_store_id);
	$cbo_req_year = str_replace("'","",$cbo_req_year);
	$txt_req_no = str_replace("'","",$txt_req_no);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	
	$approval_type = str_replace("'","",$cbo_approval_type);
	$alter_user_id = str_replace("'","",$txt_alter_user_id);
	$app_user_id=($alter_user_id!='') ? $alter_user_id:$user_id;
	
	if($cbo_item_category_id){$searchCon .=" and b.ITEM_CATEGORY=$cbo_item_category_id";}
	if($cbo_store_id){$searchCon .=" and a.STORE_NAME=$cbo_store_id";}
	
	if ($txt_req_no != ''){$searchCon .=" and a.REQU_PREFIX_NUM=$txt_req_no";}
	if ($txt_date_from != '' && $txt_date_to != '')
	{
        $txt_date_from = date("d-M-Y", strtotime($txt_date_from));
        $txt_date_to = date("d-M-Y", strtotime($txt_date_to));
        $searchCon .= " and a.requisition_date between '$txt_date_from' and '$txt_date_to'";
	}
	if ($cbo_req_year != 0) $searchCon.= " and TO_CHAR(a.insert_date,'YYYY')=$cbo_req_year";
			

	$category_mixing_variable = return_field_value("allocation","variable_settings_inventory","company_name=$company_name and variable_list=44 and status_active=1 and is_deleted=0 order by id desc ","allocation");

	$electronicDataArr = getSequence(array('company_id'=>$company_name,'page_id'=>$menu_id,'entry_form'=>1,'user_id'=>$app_user_id,'lib_buyer_arr'=>0,'lib_brand_arr'=>0,'lib_item_cat_arr'=>$item_cat_arr,'lib_store_arr'=>$lib_store_arr,'lib_department_id_arr'=>$department_arr,'category_mixing'=>$category_mixing_variable,'lib_location_id_arr'=>$location_arr));

   // print_r($electronicDataArr);die;
    
    $my_seq = $electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO'];
    $my_group = $electronicDataArr['user_by'][$app_user_id]['GROUP_NO'];
	$my_group_seq_arr = $electronicDataArr['group_seq_arr'][$my_group];
	$electronicDataArr['group_seq_arr'][0] = [0] + $electronicDataArr['group_seq_arr'][1];

	$my_previous_bypass_no_seq = 0;
	rsort($electronicDataArr['bypass_seq_arr'][2]);
	foreach($electronicDataArr['bypass_seq_arr'][2] as $uid => $seq){
		if($seq<$my_seq){$my_previous_bypass_no_seq = $seq;break;}
	}
  

	if($approval_type==0) // Un-Approve
	{
		
		//Match data..................................
		if($electronicDataArr['user_by'][$app_user_id]['ITEM_ID'] && $category_mixing_variable == 2){
			$where_con .= " and b.ITEM_CATEGORY in(".$electronicDataArr['user_by'][$app_user_id]['ITEM_ID'].",0)";
			$electronicDataArr['sequ_by'][0]['ITEM_ID']=$electronicDataArr['user_by'][$app_user_id]['ITEM_ID'];
		  }
		  if($electronicDataArr['user_by'][$app_user_id]['DEPARTMENT_ID']){
			$where_con .= " and a.DEPARTMENT_ID in(".$electronicDataArr['user_by'][$app_user_id]['DEPARTMENT_ID'].",0)";
			$electronicDataArr['sequ_by'][0]['DEPARTMENT_ID']=$electronicDataArr['user_by'][$app_user_id]['DEPARTMENT_ID'];
		  }
		  if($electronicDataArr['user_by'][$app_user_id]['STORE_ID']){
			$where_con .= " and a.STORE_NAME in(".$electronicDataArr['user_by'][$app_user_id]['STORE_ID'].",0)";
			$electronicDataArr['sequ_by'][0]['STORE_ID']=$electronicDataArr['user_by'][$app_user_id]['STORE_ID'];
		  }

		  if($electronicDataArr['user_by'][$app_user_id]['LOCATION_ID']){
			$where_con .= " and a.LOCATION_ID in(".$electronicDataArr['user_by'][$app_user_id]['LOCATION_ID'].",0)";
			$electronicDataArr['sequ_by'][0]['LOCATION_ID']=$electronicDataArr['user_by'][$app_user_id]['LOCATION_ID'];
		  }


			$data_mas_sql = " select a.ID,a.STORE_NAME,a.DEPARTMENT_ID,a.LOCATION_ID,a.APPROVED_SEQU_BY,a.APPROVED_GROUP_BY, b.ITEM_CATEGORY from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b where a.id=b.mst_id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.ENTRY_FORM=69 and a.is_approved<>1 and a.READY_TO_APPROVE=1  and a.COMPANY_ID=$company_name $where_con $searchCon";//and a.is_mixed_category=2
		    //echo $data_mas_sql; die;
           // print_r($electronicDataArr['group_seq_arr'][1]);die;

            $tmp_sys_id_arr=array();$sys_data_arr=array();
			$data_mas_sql_res=sql_select( $data_mas_sql );
			foreach ($data_mas_sql_res as $row)
			{ 

				$group_stage_arr = array();
				for($group = ($row['APPROVED_GROUP_BY'] == $my_group && $electronicDataArr['sequ_by'][$my_seq]['BYPASS'] == 2)?$my_group:$my_group-1;$group >= 0; $group-- ){
					
					krsort($electronicDataArr['group_seq_arr'][$group]);
					foreach($electronicDataArr['group_seq_arr'][$group] as $seq){
                      
                        if($seq < $my_seq){
                            $validation_check = true;
                            if($category_mixing_variable == 2){
                                if(in_array($row['ITEM_CATEGORY'],explode(',',$electronicDataArr['sequ_by'][$seq]['ITEM_ID'])) || $row['ITEM_CATEGORY']==0){$validation_check = true;}
                                else{$validation_check = false;}
                            }

                            if( 
                                $validation_check == true 
                                && (in_array($row['DEPARTMENT_ID'],explode(',',$electronicDataArr['sequ_by'][$seq]['DEPARTMENT_ID'])) || $row['DEPARTMENT_ID']==0) 
                                && in_array($row['STORE_NAME'],explode(',',$electronicDataArr['sequ_by'][$seq]['STORE_ID']))
                                && (in_array($row['LOCATION_ID'],explode(',',$electronicDataArr['sequ_by'][$seq]['LOCATION_ID'])) || $row['LOCATION_ID']==0)
                                && ($row['APPROVED_GROUP_BY']<=$group)
                            )
                            
                            {
                            
                                if($electronicDataArr['sequ_by'][$seq]['BYPASS']==1){
                                    $tmp_sys_id_arr[$group][$seq][$row['ID']]=$row['ID'];
                                }
                                else{
                                    $tmp_sys_id_arr[$group][$seq][$row['ID']]=$row['ID'];
									if(in_array($my_previous_bypass_no_seq,$electronicDataArr['group_seq_arr'][$my_group]) && $row['APPROVED_SEQU_BY'] < $my_previous_bypass_no_seq){
										unset($tmp_sys_id_arr[$group][$seq][$row['ID']]);
									}
                                    break 2; break; 
                                }
                            
                                //This condition user for only 1 parson approve from group if all user can pass yes. but those are mendatory if found can pass no. 
                                if( (in_array($row['APPROVED_SEQU_BY'],$electronicDataArr['group_seq_arr'][$my_group]) && ($row['APPROVED_SEQU_BY'] != $my_previous_bypass_no_seq ) && $electronicDataArr['group_bypass_arr'][$my_group][2] !=2 ) || (count($group_stage_arr[$row['ID']]) > 1) || ($my_previous_bypass_no_seq < $my_seq) && ($row['APPROVED_SEQU_BY'] < $my_previous_bypass_no_seq )  ){ 
                                    unset($tmp_sys_id_arr[$group][$seq][$row['ID']]);
                                    break; 
                                }

                                $group_stage_arr[$row['ID']][$group] = $group;
                            }
                        }
                    }

				 
				}//group loof;
				foreach($group_stage_arr as $sys_id => $gidArr){ 
					foreach($gidArr as $gid => $gid){	
						if(count($group_stage_arr[$sys_id])>1 && array_key_first($group_stage_arr[$sys_id]) != $gid ){
							unset($tmp_sys_id_arr[$gid]);
						}
					}
				}
			}
		//..........................................Match data;
		// || $electronicDataArr[sequ_by][$seq][BYPASS]==1
		
	 //print_r($tmp_sys_id_arr);die;
		
        $sql='';
		for($group=0;$group<=$my_group; $group++ ){
			foreach($electronicDataArr['group_seq_arr'][$group] as $seq){
		
				if(count($tmp_sys_id_arr[$group][$seq])){
					$sys_con = where_con_using_array($tmp_sys_id_arr[$group][$seq],0,'a.ID');
					if($sql != ''){$sql .= " UNION ALL ";}
				    $sql .= "select A.ID,A.LOCATION_ID, A.REQU_NO, A.REQU_PREFIX_NUM ,A.REMARKS, A.COMPANY_ID, TO_CHAR(a.insert_date,'YYYY') AS YEAR, listagg(b.item_category, ',') within group (order by b.item_category) as ITEM_CATEGORY_ID , A.REQUISITION_DATE, A.DELIVERY_DATE, 0 AS APPROVAL_ID, A.IS_APPROVED, A.DEPARTMENT_ID, SUM(B.AMOUNT) AS REQ_VALUE from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b where a.id=b.mst_id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.is_approved<>1 and a.READY_TO_APPROVE=1 and a.APPROVED_SEQU_BY=$seq and a.APPROVED_GROUP_BY=$group $sys_con and A.COMPANY_ID=$company_name and a.ENTRY_FORM=69  $searchCon group by a.id,a.remarks, a.company_id,A.LOCATION_ID, a.requ_no, a.requ_prefix_num ,TO_CHAR(a.insert_date,'YYYY'), a.requisition_date, a.delivery_date, a.is_approved, a.department_id";
			
				}
		
			}
		}
		
	}
	else
	{
		
		// if(count($electronicDataArr['group_seq_arr'])==2){
		// 	$searchCon .= " and d.SEQUENCE_NO={$electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO']}";
		// }
		// else{
		// 	$searchCon .= " and c.GROUP_NO={$electronicDataArr['user_by'][$app_user_id]['GROUP_NO']}";
		// }

	 
        
        $sql = " select A.ID, A.REQU_NO,A.LOCATION_ID, A.REQU_PREFIX_NUM ,A.REMARKS, A.COMPANY_ID, TO_CHAR(a.insert_date,'YYYY') AS YEAR, listagg(b.item_category, ',') within group (order by b.item_category) as ITEM_CATEGORY_ID , A.REQUISITION_DATE, A.DELIVERY_DATE, 0 AS APPROVAL_ID, A.IS_APPROVED, A.DEPARTMENT_ID, SUM(B.AMOUNT) AS REQ_VALUE from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b,APPROVAL_MST c where a.id=b.mst_id and c.mst_id=a.id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.READY_TO_APPROVE=1 and A.COMPANY_ID=$company_name and c.GROUP_NO={$electronicDataArr['user_by'][$app_user_id]['GROUP_NO']} and a.APPROVED_GROUP_BY=c.GROUP_NO and a.ENTRY_FORM=69 and c.entry_form=1  $searchCon  group by a.id,A.LOCATION_ID,a.remarks, a.company_id, a.requ_no, a.requ_prefix_num ,TO_CHAR(a.insert_date,'YYYY'), a.requisition_date, a.delivery_date, a.is_approved, a.department_id order by a.id";
		//and a.is_mixed_category=2		
    }
	   //echo $sql;
 
		
	$mst_id_arr=array();
	$nameArray=sql_select( $sql );
	foreach ($nameArray as $row)
	{ 
		$mst_id_arr[$row['ID']]=$row['ID'];
	}
  
	$hostory_sql=sql_select( "select MST_ID,APPROVED_BY, APPROVED_DATE from approval_history where current_approval_status=1 and entry_form=1 and mst_id in (".implode(',',$mst_id_arr).")");
	foreach ($hostory_sql as $row)
	{ 
		$history_data['LAST_APP_DATE'][$row['MST_ID']]=$row['APPROVED_DATE'];
		$history_data['LAST_APP_BY'][$row['MST_ID']]=$row['APPROVED_BY'];
	}
   
	$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$company_name." and module_id=6 and report_id =39 and is_deleted=0 and status_active=1");
    $format_ids=explode(",",$print_report_format);
    //echo "<pre>";print_r($format_ids);
	
	$user_arr=return_library_array( "select id, user_name from user_passwd", 'id', 'user_name' );	
	

	$width=1100;
    
    ?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:<?= $width+20; ?>px; margin-top:10px">
        <legend>PI Approval</legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width; ?>" class="rpt_table" align="left" >
                <thead>
                    <th width="20"></th>
                    <th width="35">SL</th>                   
                    <th width="120">Requisition No</th>
                    <th width="50">Year</th>
                    <th width="100">Item Category</th>
                    <th width="100">Depatment</th>                    
                    <th width="60">Requisition Value</th>                                       
                    <th width="80">Requisition Date</th> 
                    <th  width="130">Last Approval Date and Person</th>
					<th width="100">Un-approve request</th>
					<? if($approval_type==0){?><th width="120">Not Appv. Cause</th><? }?>
					<th>&nbsp;</th>
                                                         
                </thead>
            </table>            
            <div style="width:<?= $width+20; ?>px; overflow-y:scroll; max-height:330px; float:left;" id="buyer_list_view">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width; ?>" class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
                        <?						 
                        $i=1; $all_approval_id=''; $j=0;
                        foreach ($nameArray as $row)
                        {                                               
                             $unapprove_value_id=$row[ID];
							 $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
								if($approval_type==0)
								{
									$value=$row[csf('id')];
								}
								else
								{
									$app_id = sql_select("select id from approval_history where mst_id='".$row['ID']."' and entry_form='1'  order by id desc");									
									$value=$row[csf('id')]."**".$app_id[0][csf('id')];
								}

								$variable='';
								if ($format_ids[$j]==118) $type=1;// Print Report With Group
                                elseif($format_ids[$j]==119)  $type=2; // Print Report Without Group
								else if($format_ids[$j]==120) $type=3; // Print Report 
                                elseif($format_ids[$j]==121) $type=4; //Print Report 2
                                elseif($format_ids[$j]==122) $type=5; // Print Report 3
                                elseif($format_ids[$j]==123) $type=6; // Print Report 4
                                elseif($format_ids[$j]==129)  $type=7; // Print 5
                                elseif($format_ids[$j]==169) $type=8; // Print Report 6
                                elseif($format_ids[$j]==165)  $type=9; // Print Report 7
                                elseif($format_ids[$j]==227) $type=10; // Print Report 8                            
                                elseif($format_ids[$j]==241) $type=11; // Print Report 11 
                                elseif($format_ids[$j]==580)  $type=12; // Print Report 5
                                elseif($format_ids[$j]==28)  $type=13; // Print Report 13 
                                elseif($format_ids[$j]==280)  $type=14; // Print 14
                                elseif($format_ids[$j]==688) $type=15; // Re-Order Level
                                elseif($format_ids[$j]==243)  $type=16; // Item wise
                                elseif($format_ids[$j]==310) $type=17; // Category Wise
                                elseif($format_ids[$j]==304)  $type=18; // Print 15
                                elseif($format_ids[$j]==719) $type=19; // Print 16
                                elseif($format_ids[$j]==723) $type=20; // Print 17
                                elseif($format_ids[$j]==339)  $type=21; // Print 18
                                elseif($format_ids[$j]==370) $type=22; // Print 19
                                elseif($format_ids[$j]==382)  $type=23; //Print Out5
                                elseif($format_ids[$j]==235) $type=24; // Print 9                             
                                elseif($format_ids[$j]==768) $type=25; // Print 20  
                                elseif($format_ids[$j]==419) $type=26; // Print 22   
                                else  $type=0;

                                $variable="<a href='#'  title='".$format_ids[$j]."'  onclick=\"print_report('".$row['COMPANY_ID']."','".$row['ID']."','Purchase Requisition','".$row['IS_APPROVED']."','".$row['REMARKS']."','".$type."','".$row['IS_APPROVE']."','1')\"> ".$row['REQU_NO']." <a/>";
						   
						   		?>
                                <tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" id="tr_<?= $i; ?>"> 
                                    <td width="20" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<?= $i;?>" name="tbl[]"  />
                                        <input type="hidden" id="target_id_<?= $i;?>" name="target_id_[]"  value="<?=$row['ID']; ?>" />                                                  
                                    </td> 
                                    <td width="35" align="center"><p><?= $i;?></p></td>
                                    <td width="120" align="center"><p><?= $variable//$row[REQU_NO]; ?></p></td>
                                    <td width="50" align="center"><p><?= $row['YEAR']; ?></p></td>								
                                    <td width="100"><p>
									<?
									$item_name_arr=array();
									foreach(explode(',',$row['ITEM_CATEGORY_ID']) as $item_id){
										$item_name_arr[$item_id]=$item_category[$item_id];
									}
									echo implode(', ',$item_name_arr);?></p></td>                                  
                                    <td width="100" align="center"><p><?= $department_arr[$row['DEPARTMENT_ID']]; ?></p></td>      
                                    <td width="60" align="right"><?= $row['REQ_VALUE']; ?></td>      
                                    <td width="80" align="center"><?= change_date_format($row['REQUISITION_DATE']); ?></td>      
									<td width="130" align="center"><p><? echo $history_data['LAST_APP_DATE'][$row['ID']] .'<br>'.$user_arr[$history_data['LAST_APP_BY'][$row['ID']]]; ?></p></td>
                                    
                                    
									<td width="100" align="center"> 
									<p>
									<?
										if($approval_type==1)
										{
											$unapproved_request=$unapproved_request_arr[$row['ID']]; 
											if($unapproved_request!='')
											{
												$view_request='View';
											}
										}
										else
										{
											$unapproved_request=''; 
											$view_request='';
										}
										
									?>
									<a href="#report_details" onClick="openmypage('<? echo $unapproved_request; ?>','unapprove_request_action','Unapprove Request Details')"><? echo $view_request; ?></a>
									  </p>&nbsp;</td>
									  <? 
									if($approval_type==0)
									{
										?>
										 <td width="120" align="center" style="word-break:break-all">
	                                        	<input name="txt_appv_cause[]" class="text_boxes" placeholder="Please Browse" ID="txt_appv_cause_<?=$i;?>" style="width:100px" value="" maxlength="50" title="Maximum 50 Character" ondblclick="openmypage_app_cause(<?= $unapprove_value_id; ?>,<?=$approval_type; ?>,<?=$i;?>)">&nbsp;
	                                    </td>
										<? 
									}

									?>
									<td align="center"><input type="button" class="formbutton" id="reqdtls_<?= $i;?>" style="width:100px" value="Req. Details" onClick="openmypage_reqdetails(<?= $row['ID']; ?>, '<?= $row['REQU_NO']; ?>')"/></td>
                                    
                                       
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
                        <input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<?= $i; ?>,<?= $approval_type; ?>,<?= $user_id; ?>)"/>&nbsp;&nbsp;&nbsp;
						<input type="button" value="Deny" class="formbutton" style="width:100px; display:<?=($approval_type==1)?'none':'';?> " onClick="submit_approved(<?=$i; ?>,5,<?= $user_id; ?>);"/>
                    </td>
				</tfoot>
			</table>
        </fieldset>
    </form>
	<?
	exit();	
}

if ($action == "approve")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$con = connect();

	$company_name = str_replace("'","",$cbo_company_name);
	$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
	$approval_type = str_replace("'","",$approval_type);
	$target_ids = str_replace("'","",$target_ids);
	$txt_appv_causes = str_replace("'","",$txt_appv_causes);
		
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$app_user_id=($txt_alter_user_id)?$txt_alter_user_id:$user_id;	
	$target_app_id_arr = explode(',',$target_ids);
	$txt_appv_cause_arr = explode('**',$txt_appv_causes);
	 //echo $target_ids;die;
	

	 $sql="select a.IS_APPROVED,a.READY_TO_APPROVE,a.APPROVED_SEQU_BY,A.ID,a.STORE_NAME,a.DEPARTMENT_ID,a.LOCATION_ID, b.ITEM_CATEGORY from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b where a.id=b.mst_id and a.id in($target_ids)";
		
	 $sqlResult=sql_select( $sql );
	 foreach ($sqlResult as $row)
	 {
		 if($row['READY_TO_APPROVE'] != 1){echo '25**Please select ready to approved yes for approved this Job';exit();}
		 $last_app_seq_arr[$row['ID']] = $row['APPROVED_SEQU_BY'];
		 $last_app_status_arr[$row['ID']] = $row['IS_APPROVED'];
		 
		 $matchDataArr[$row['ID']]=array('buyer'=>0,'brand'=>0,'item'=>$row['ITEM_CATEGORY'],'store'=>$row['STORE_NAME'],'department'=>$row['DEPARTMENT_ID'],'location_id'=>$row['LOCATION_ID']);
	 }


	 $max_approved_no_arr = return_library_array( "select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($target_ids) and entry_form=1 group by MST_ID",'mst_id','approved_no');

	 $category_mixing_variable = return_field_value("allocation","variable_settings_inventory","company_name=$company_name and variable_list=44 and status_active=1 and is_deleted=0 order by id desc ","allocation");

	 $finalDataArr=getFinalUser(array('company_id'=>$company_name,'page_id'=>$menu_id,'entry_form'=>1,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>$brand_arr,'lib_item_cat_arr'=>$item_cat_arr,'lib_store_arr'=>$lib_store_arr,'lib_department_id_arr'=>$department_arr,'match_data'=>$matchDataArr,'category_mixing'=>$category_mixing_variable,'lib_location_id_arr'=>$location_arr));
	 

    $sequ_no_arr_by_sys_id = $finalDataArr['final_seq'];
    $user_sequence_no = $finalDataArr['user_seq'][$app_user_id];
    $user_group_no = $finalDataArr['user_group'][$app_user_id];
    $max_group_no = max($finalDataArr['user_group']);
 
	
	
	if($approval_type==5)
	{
		
        $id = return_next_id( "id","approval_history", 1 ) ;
        foreach($target_app_id_arr as $key => $mst_id)
        {		
            $approved_no = $max_approved_no_arr[$mst_id];
            if($i!=0) $data_array .= ",";
            $data_array .= "(".$id.",1,".$mst_id.",".$approved_no.",'".$user_sequence_no."',0,'".$txt_appv_cause_arr[$key]."',".$app_user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,2)";
            $id++;
        }

        //echo $data_array;die;

        $flag=1;
        
        if($flag==1) 
        {
            $field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status,UN_APPROVED_REASON, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING,APPROVED"; 
            $rIDHistory = sql_insert("approval_history",$field_array,$data_array,0);
            if($rIDHistory){$flag = 1;}else{$flag = 0;} 
        } 
        
        if($flag==1) 
        {
            $rID=sql_multirow_update("inv_purchase_requisition_mst","IS_APPROVED*READY_TO_APPROVE*APPROVED_SEQU_BY*APPROVED_GROUP_BY*APPROVED_BY*APPROVED_DATE","2*0*0*0*$app_user_id*'$pc_date_time'","id",$target_ids,0); 
            if($rID) $flag=1; else $flag=0; 
        }


        if($flag==1) 
        {
            $query="delete from approval_mst  WHERE entry_form=1 and mst_id in ($target_ids)";
            $rID2=execute_query($query,1); 
            if($rID2) $flag=1; else $flag=0; 
        }

        if($flag==1) 
        {
            $query="UPDATE approval_history SET current_approval_status=0,IS_SIGNING=0 WHERE entry_form=1 and mst_id in ($target_ids)";
            $rID3=execute_query($query,1);
            if($rID3) $flag=1; else $flag=0; 
        } 

      // echo "10**$rID**$rID2**$rID3**$rIDHistory";oci_rollback($con);die;

        
        if($flag == 1 && $_SESSION['app_notification'] == 1)
        {
            $query="DELETE FROM APPROVAL_NOTIFICATION_ENGINE  WHERE ENTRY_FORM=1 AND REF_ID IN ($target_ids)";
            $rID5=execute_query($query,1);
            if($rID5) $flag=1; else $flag=0; 
        }
        if($flag == 1 && $_SESSION['app_notification'] == 1)
        {
            $reqs_ids=explode(",",$target_ids);
            foreach($reqs_ids as $ref_id)
            {
                $approval_data=getData($ref_id);
                $appr_data = array("USER_ID"=>'',"SEQUENCE_NO" =>'',"COMPANY_ID"=> '',"NOTIFICATION_TYPE"=>0,'approval_desc'=>'','approval_data'=>$approval_data);
                $notification->pushAll($ref_id,1,$appr_data);
            }
        }
                

	}

	else if($approval_type==0)
	{      
	
		$approved_no_array=array();
		
		
		$id = return_next_id( "id","approval_mst", 1 ) ;
		$his_id = return_next_id( "id","approval_history", 1 ) ;
		foreach($target_app_id_arr as $mst_id)
		{		
			if($user_sequence_no < $last_app_seq_arr[$mst_id] ){continue;}

            //mst data.......................
            $approved = ((max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no) || ( (max($finalDataArr['group_bypass_no_data_arr'][$max_group_no][2])==$user_sequence_no) && ($max_group_no == $user_group_no))  || ( (max($finalDataArr['group_bypass_no_data_arr'][$max_group_no][2]) == '') && ($max_group_no == $user_group_no) ) )?1:3;

			if($mst_data_array!=''){$mst_data_array.=",";}
			$mst_data_array.="(".$id.",1,".$mst_id.",".$user_sequence_no.",".$user_group_no.",".$app_user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
			$id=$id+1;

            $mst_data_array_up[$mst_id] = explode(",",("".$approved.",".$user_sequence_no.",".$user_group_no.",".$app_user_id.",'".$pc_date_time."'")); 

			//His.................
			$approved_no = $max_approved_no_arr[$mst_id];
			if($last_app_status_arr[$mst_id] == 0 || $last_app_status_arr[$mst_id] == 2){
				$approved_no = $max_approved_no_arr[$mst_id]+1;
				$approved_no_array[$mst_id] = $approved_no;
			}
			
			if($data_array != "") $data_array.=",";
            $data_array.="(".$his_id.",1,".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$app_user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,$approved)";
			$his_id++;
            


			if($approved == 1)
			{
				$is_mst_final_seq[$mst_id] = $mst_id;
			}
            

			
		}
		
		  //print_r($data_array);die;

		$flag = 1;
		if(count($approved_no_array)>0){

		
			$approved_string="";
			foreach($approved_no_array as $key=>$value)
			{
				$approved_string.=" WHEN $key THEN $value";
			}
			
			$approved_string_mst="CASE id ".$approved_string." END";
			$approved_string_dtls="CASE mst_id ".$approved_string." END";
			
			$sql_insert="INSERT into inv_pur_requisition_mst_hist(id, hist_mst_id, approved_no, entry_form, requ_no, requ_no_prefix, requ_prefix_num, company_id, item_category_id, supplier_id, location_id, division_id, department_id, section_id, requisition_date, store_name, pay_mode, source, cbo_currency, delivery_date, do_no, attention, remarks, terms_and_condition, manual_req, ready_to_approve, is_approved, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date) 
			select	
			'', id, $approved_string_mst, entry_form, requ_no, requ_no_prefix, requ_prefix_num, company_id, item_category_id, supplier_id, location_id, division_id, department_id, section_id, requisition_date, store_name, pay_mode, source, cbo_currency, delivery_date, do_no, attention, remarks, terms_and_condition, manual_req, ready_to_approve, is_approved, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date from  inv_purchase_requisition_mst where id in ($target_ids)";
			
			//echo $sql_insert;
			$sql_insert_dtls="INSERT into  inv_pur_requisition_dtls_hist(id, approved_no, mst_id, product_id, user_code_maintain, required_for, job_id, job_no, buyer_id, style_ref_no, color_id, count_id, composition_id, com_percent, yarn_type_id, yarn_inhouse_date, cons_uom, quantity, rate, amount, stock, remarks, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted) 
			select	
			'', $approved_string_dtls, mst_id, product_id, user_code_maintain, required_for, job_id, job_no, buyer_id, style_ref_no, color_id, count_id, composition_id, com_percent, yarn_type_id, yarn_inhouse_date, cons_uom, quantity, rate, amount, stock, remarks, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from  inv_purchase_requisition_dtls where mst_id in ($target_ids)";

			if($flag==1) 
			{
				$rID4=execute_query($sql_insert,0);
				if($rID4) $flag=1; else $flag=0; 
				
			}       

			if($flag==1) 
			{
				$rID5=execute_query($sql_insert_dtls,1);
				if($rID5) $flag=1; else $flag=0; 
				
			} 

		}

 
		
		if($flag==1) 
		{
			$mst_field_array="id, entry_form, mst_id,  sequence_no,group_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
            $rID=sql_insert("approval_mst",$mst_field_array,$mst_data_array,0);
			if($rID) $flag=1; else $flag=0; 
		}
		
		
		if($flag==1) 
		{
			$mst_field_array_up="is_approved*APPROVED_SEQU_BY*APPROVED_GROUP_BY*APPROVED_BY*APPROVED_DATE"; 
            $rID1=execute_query(bulk_update_sql_statement( "inv_purchase_requisition_mst", "id", $mst_field_array_up, $mst_data_array_up, $target_app_id_arr ));
			if($rID1) $flag=1; else $flag=0; 
		}
	   

        if($flag==1) 
        {
            $query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=1 and mst_id in ($target_ids)";
        	$rID2=execute_query($query,1);
			if($rID2) $flag=1; else $flag=0; 
        }
		
        if($flag==1) 
        {
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING,APPROVED"; 
			$rID3 = sql_insert("approval_history",$field_array,$data_array,0);
			if($rID3) $flag=1; else $flag=0; 
            
        }
        
        

		if($flag == 1 && $_SESSION['app_notification'] == 1)
		{
			$query="UPDATE APPROVAL_NOTIFICATION_ENGINE SET IS_APPROVED=1 WHERE ENTRY_FORM=1 and REF_ID in ($target_ids)"; //die;
			$rID6=execute_query($query,1);
			
			if($rID6) $flag=1; else $flag=0; 

			if($flag == 1)
			{
				
				foreach($target_app_id_arr as $req_id)
				{
					$approval_data=getData($req_id,$app_user_id);

					$approval_desc = "";
					if(!empty($approval_data['DESC']))
					{
						$approval_desc = $approval_data['DESC'];
					}
					if($category_mixing_variable == 2)
					{
						$approval_parameter = array('DEPARTMENT'=>$matchDataArr[$req_id]['department'],'LOCATION'=>$matchDataArr[$req_id]['LOCATION_ID'],'STORE_ID'=>$matchDataArr[$req_id]['store'],'ITEM_CATEGORY'=>$matchDataArr[$req_id]['item'],'approval_desc'=>$approval_desc,'approval_data'=>$approval_data,'title'=>'Pending Approval :: Purchase Requisition');
					}
					else
					{
						$approval_parameter = array('DEPARTMENT'=>$matchDataArr[$req_id]['department'],'LOCATION'=>$matchDataArr[$req_id]['LOCATION_ID'],'STORE_ID'=>$matchDataArr[$req_id]['store'],'ITEM_CATEGORY'=>'','approval_desc'=>$approval_desc,'approval_data'=>$approval_data,'title'=>'Pending Approval :: Purchase Requisition');
					}
					
					$not_res = $notification->notificationEngine($req_id,$company_name,1,$approval_parameter,$app_user_id);
					if($flag == 1)
					{
						$appr_data = array("USER_ID"=>$app_user_id,"SEQUENCE_NO" =>$user_sequence_no,"COMPANY_ID"=> $company_name,"NOTIFICATION_TYPE"=>0,'approval_desc'=>$approval_desc,'approval_data'=>$approval_data);
						$notification->pushAll($req_id,1,$appr_data);
					}
				}
			}

			
			if(count($is_mst_final_seq)> 0)
            {
                $query="DELETE APPROVAL_NOTIFICATION_ENGINE  WHERE entry_form = 1 and ref_id in (".implode(",",$is_mst_final_seq).") and user_id not in ( $app_user_id ) "; //die;
                $rID7=execute_query($query);
                if($rID7 == 1 && $flag == 1) $flag=1; else $flag=0; 
            }
		}


		// echo '21**'.$rID.'**'.$rID1.'**'.$rID2.'**'.$rID3.'**'.$rID4.'**'.$rID5;oci_rollback($con);die;
		
		
        
	}
	else
	{              
        $id = return_next_id( "id","approval_history", 1 ) ;
        foreach($target_app_id_arr as $key => $mst_id)
        {		
            $approved_no = $max_approved_no_arr[$mst_id];
            if($i!=0) $data_array .= ",";
            $data_array .= "(".$id.",1,".$mst_id.",".$approved_no.",'".$user_sequence_no."',0,'".$txt_appv_cause_arr[$key]."',".$app_user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,2)";
            $id++;
        }



		$flag=1;
		$rID=sql_multirow_update("inv_purchase_requisition_mst","is_approved*ready_to_approve*APPROVED_SEQU_BY*APPROVED_GROUP_BY*APPROVED_BY*APPROVED_DATE","0*0*0*0*$app_user_id*'$pc_date_time'","id",$target_ids,0); 
		if($flag==1) 
		{
			if($rID) $flag=1; else $flag=0; 
		}

		$query="delete from approval_mst  WHERE entry_form=1 and mst_id in ($target_ids)";
		$rID1=execute_query($query,1); 
		if($flag==1) 
		{
			if($rID1) $flag=1; else $flag=0; 
		}
		
		//-----------------------History

		$query="UPDATE approval_history SET current_approval_status=0,IS_SIGNING=0 WHERE entry_form=1 and mst_id in ($target_ids)";
		$rID2=execute_query($query,1);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
			
        if($flag==1) 
        {
            $field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status,UN_APPROVED_REASON, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING,APPROVED"; 
            $rID3 = sql_insert("approval_history",$field_array,$data_array,0);
            if($rID3){$flag = 1;}else{$flag = 0;} 
        }


       // echo "0**$rID**$rID1**$rID2**$rID3";oci_rollback($con);die;

		if($flag == 1 && $_SESSION['app_notification'] == 1)
		{
			$query="DELETE FROM APPROVAL_NOTIFICATION_ENGINE  WHERE ENTRY_FORM=1 AND REF_ID IN ($target_ids) ";
			$rID5=execute_query($query,1);
			if($rID5) $flag=1; else $flag=0; 
			if($flag == 1)
			{
				$reqs_ids=explode(",",$target_ids);
				foreach($reqs_ids as $ref_id)
				{
					$approval_data=getData($ref_id);
					$appr_data = array("USER_ID"=>'',"SEQUENCE_NO" =>'',"COMPANY_ID"=> '',"NOTIFICATION_TYPE"=>0,'approval_desc'=>'','approval_data'=>$approval_data);
					if(!empty($approval_data['approval_desc']))
					{
						$approval_desc = $approval_data['approval_desc'];
					}	
					$notification->pushAll($ref_id,1,$appr_data);
				}
			}
		}

		
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




if ($action=="save_update_delete_appv_cause")
{
	//$approval_id
	$approval_type=str_replace("'","",$appv_type);

	if($approval_type==0)
	{

   		$process = array( &$_POST );
		extract(check_magic_quote_gpc( $process ));

		if ($operation==0|| $operation==1)  // Insert Here
		{
			$con = connect();
		
			$approved_no_history = return_field_value("approved_no","approval_history","entry_form=1 and mst_id = $wo_id and approved_by=$app_user_id");

			$approved_no_cause=return_field_value("approval_no","fabric_booking_approval_cause","entry_form=1 and booking_id=$wo_id and user_id=$app_user_id and approval_type=$appv_type");

			//echo "shajjad_".$approved_no_history.'_'.$approved_no_cause; die;

			if($approved_no_history=="" && $approved_no_cause=="")
			{
				//echo "insert"; die;

				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id_mst.",".$page_id.",1,".$app_user_id.",".$wo_id." ,".$appv_type.",0,".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
				//echo $rID; die;

				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT");
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$app_user_id);
					}
					else
					{
						mysql_query("ROLLBACK");
						echo "10**".$rID;
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($rID )
					{
						oci_commit($con);
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$app_user_id);
					}
					else
					{
						oci_rollback($con);
						echo "10**".$rID;
					}
				}
				disconnect($con);
				die;
			}
			else if($approved_no_history=="" && $approved_no_cause!="")
			{
				$con = connect();
				if($db_type==0)
				{
					mysql_query("BEGIN");
				}

				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=1 and booking_id=$wo_id and user_id=$app_user_id and approval_type=$appv_type");

				$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
				$data_array="".$page_id."*1*".$app_user_id."*".$wo_id."*".$appv_type."*0*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

				 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);

				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT");
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$app_user_id);
					}
					else
					{
						mysql_query("ROLLBACK");
						echo "10**".$rID;
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($rID )
					{
						oci_commit($con);
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$app_user_id);
					}
					else
					{
						oci_rollback($con);
						echo "10**".$rID;
					}
				}
				disconnect($con);
				die;
			}
			else if($approved_no_history!="" && $approved_no_cause!="")
			{
				$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=1 and mst_id=$wo_id and approved_by=$app_user_id");
				$max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=1 and booking_id=$wo_id and user_id=$app_user_id and approval_type=$appv_type");

				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id_mst.",".$page_id.",1,".$app_user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
					//echo $rID; die;

					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT");
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$app_user_id);
						}
						else
						{
							mysql_query("ROLLBACK");
							echo "10**".$rID;
						}
					}
					else if($db_type==2 || $db_type==1 )
					{
						if($rID )
						{
							oci_commit($con);
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$app_user_id);
						}
						else
						{
							oci_rollback($con);
							echo "10**".$rID;
						}
					}
					disconnect($con);
					die;
				}
				else if($max_appv_no_his==$max_appv_no_cause)
				{
					$con = connect();
					if($db_type==0)
					{
						mysql_query("BEGIN");
					}

					$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=1 and booking_id=$wo_id and user_id=$app_user_id and approval_type=$appv_type");

					$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
					$data_array="".$page_id."*1*".$app_user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

					 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);

					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT");
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$app_user_id);
						}
						else
						{
							mysql_query("ROLLBACK");
							echo "10**".$rID;
						}
					}
					else if($db_type==2 || $db_type==1 )
					{
						if($rID )
						{
							oci_commit($con);
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$app_user_id);
						}
						else
						{
							oci_rollback($con);
							echo "10**".$rID;
						}
					}
					disconnect($con);
					die;
				}
			}
			else if($approved_no_history!="" && $approved_no_cause=="")
			{
				$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=1 and mst_id=$wo_id and approved_by=$app_user_id");
				$max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=1 and booking_id=$wo_id and user_id=$app_user_id and approval_type=$appv_type");

				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id_mst.",".$page_id.",1,".$app_user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
					//echo $rID; die;

					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT");
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$app_user_id);
						}
						else
						{
							mysql_query("ROLLBACK");
							echo "10**".$rID;
						}
					}
					else if($db_type==2 || $db_type==1 )
					{
						if($rID )
						{
							oci_commit($con);
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$app_user_id);
						}
						else
						{
							oci_rollback($con);
							echo "10**".$rID;
						}
					}
					disconnect($con);
					die;
				}
				else if($max_appv_no_his==$max_appv_no_cause)
				{
					$con = connect();
					if($db_type==0)
					{
						mysql_query("BEGIN");
					}

					$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=1 and booking_id=$wo_id and user_id=$app_user_id and approval_type=$appv_type");

					$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
					$data_array="".$page_id."*1*".$app_user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

					 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);

					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT");
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$app_user_id);
						}
						else
						{
							mysql_query("ROLLBACK");
							echo "10**".$rID;
						}
					}
					else if($db_type==2 || $db_type==1 )
					{
						if($rID )
						{
							oci_commit($con);
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$app_user_id);
						}
						else
						{
							oci_rollback($con);
							echo "10**".$rID;
						}
					}
					disconnect($con);
					die;
				}
			}

		}

	}//type=0	
}


if($action=="reqdetails_popup")
{ 
	echo load_html_head_contents("Requ. Details","../../", 1, 1, $unicode,1);
	extract($_REQUEST);
	$ex_data=explode("**",$data);

	$sql="SELECT a.id, b.id as dtls_id, b.quantity, b.product_id, c.item_category_id, c.item_account, c.item_description, c.item_size, c.item_group_id, c.sub_group_name, c.order_uom as unit_of_measure, d.item_name,b.rate,  b.stock
	from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c, lib_item_group d
	where a.id=b.mst_id and b.product_id=c.id and c.item_group_id=d.id and a.id=$ex_data[0] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0";
	//echo $sql;die();
	$sql_res=sql_select($sql);

	// foreach($sql_res as $row)
	// {

	// 	$all_prod_ids.=$row['PRODUCT_ID'].",";
		
	// }

	// $all_prod_ids=implode(",",array_unique(explode(",",chop($all_prod_ids,","))));


	// if($all_prod_ids=="") $all_prod_ids=0;
	/* 		 $rec_sql="SELECT b.id as ID,b.item_category as ITEM_CATEGORY, b.prod_id as PROD_ID, b.transaction_date as TRANSACTION_DATE,b.supplier_id as SUPPLIER_ID, b.cons_quantity as REC_QTY, b.cons_rate as CONS_RATE 
		 from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id in($all_prod_ids) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_basis not in(6) order by  b.prod_id,b.id";
		 //echo  $rec_sql;

		 $rec_sql_result= sql_select($rec_sql);
		 foreach($rec_sql_result as $row)
		 {
			 $receive_array[$row['PROD_ID']]['transaction_date']=$row['TRANSACTION_DATE'];
			 $receive_array[$row['PROD_ID']]['rec_qty']=$row['REC_QTY'];
			 $receive_array[$row['CONS_RATE']]=$row['CONS_RATE'];
			 $receive_array[$row['PROD_ID']]['supplier_id']=$row['SUPPLIER_ID'];
		 }
	echo $receive_array[$row['CONS_RATE']] */
		//  echo "<pre>";
		//  print_r($receive_array); 
		//    echo "</pre>";die();
		 
	?>
	<script>
		var permission='<? echo $permission; ?>';
		function js_set_value()
		{
			parent.emailwindow.hide(); 
		}

		function fn_requisition_details_qtyupdate(operation)
		{
			var tot_row=$('#tbl_details tbody tr').length;
			var data_all="";
			for(var i=1; i<=tot_row; i++)
			{
				if (form_validation('txtqty_'+i,'Quantity')==false)
				{
					return;
				}
				data_all = data_all+get_submitted_data_string('txtqty_'+i+'*req_dtls_id_'+i,"../");				
			}

			var data="action=save_update_delete_requ_qty&tot_row="+tot_row+data_all;
			//alert (data);//return;
			freeze_window(operation);
			http.open("POST","purchase_requisition_approval_group_by_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=fn_requisition_details_qtyupdate_reponse;					
		}

		function fn_requisition_details_qtyupdate_reponse()
		{
			if(http.readyState == 4)
			{				
				var reponse=http.responseText.split('**');
				if (reponse[0]==1) alert("Data is updated Successfully");
				else alert("Data is not updated Successfully");
				release_freezing();
			}
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
        <? echo load_freeze_divs ("../../",$permission,1); ?>
    	<fieldset style="width:970px; margin-top:10px;">
        <legend>Purchase Requisition Details</legend>
        <form name="purchaserequisition_2" id="purchaserequisition_2" autocomplete="off">
        	<table class="rpt_table" width="950" cellspacing="0" cellpadding="0" align="center" id="tbl_details">
                <thead>
                    <tr>
                    	<th width="100">Item Account</th>
	                    <th width="100">Item Category</th>
	                    <th width="100">Item Group</th>
	                    <th width="100">Item Sub. Group</th>
	                    <th width="150">Item Description.</th>
	                    <th width="100">Item Size</th>
	                    <th width="60">Order UOM</th>
	                    <th class="must_entry_caption" title="Must Entry Field." width="80"> <font color="blue">Quantity</font></th>
	                    <th width="50">Rate</th>
	                    <th width="50">Last Rate</th>
	                    <th width="50">Stock</th>
                	</tr>
            	</thead>               
                <tbody>
                	<?
                	$i=1;
                	$bgcolor="#E9F3FF";
                	foreach ($sql_res as $row) 
                	{    
	                	?>
	                    <tr bgcolor="<?= $bgcolor; ?>">
	                    	 <input type="hidden" name="req_dtls_id[]" id="req_dtls_id_<?= $i; ?>" value="<?= $row[csf('dtls_id')]; ?>">
	                        <td><?= $row[csf('item_account')]; ?></td>
	                        <td><?= $item_category[$row[csf('item_category_id')]]; ?></td>
	                        <td><?= $row[csf('item_name')]; ?></td>
	                        <td><?= $row[csf('sub_group_name')]; ?></td>
	                        <td><?= $row[csf('item_description')]; ?></td>
	                        <td><?= $row[csf('item_size')]; ?></td>
	                        <td><?= $unit_of_measurement[$row[csf('unit_of_measure')]]; ?></td>
	                        <td align="right"><input type="text" name="txtqty[]" id="txtqty_<?= $i; ?>" style="width:80px" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" /></td>
							<td align="right"><?= number_format($row[csf('rate')],2); ?></td>
							<td align="right"><?= number_format($row[csf('rate')],2)?></td>
							<td align="right"><?= number_format($row[csf('stock')],2); ?></td>
	                    </tr>
	                    <?
	                    $i++;
	                }
	                ?>    
                </tbody>
            </table>

                <table width="100%">
                	<tr>
                        <td colspan="22" height="20" valign="middle" align="center" class="button_container"> 
                            <input type="button" class="formbutton" id="updateqtyid" name="updateqtyid" value="Update" onClick="fn_requisition_details_qtyupdate(1)" style="width:80px" />                           
                        </td>    
                    </tr>
                </table>
        </form>
	    </fieldset>
        </div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?	
}

if ($action=="appcause_popup")
{
	echo load_html_head_contents("Approval Cause Info", "../../", 1, 1,'','','');
	extract($_REQUEST);

	list($wo_id,$app_type,$app_cause,$approval_id,$txt_alter_user_id)=explode('_',$data);
	$app_user_id = ($txt_alter_user_id) ? $txt_alter_user_id : $user_id;


	if($app_cause=="")
	{
		$sql_cause="select MAX(id) as id from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=1 and user_id='$app_user_id' and booking_id='$wo_id' and approval_type=$app_type and status_active=1 and is_deleted=0";
		 //echo $sql_cause; //die;
		$nameArray_cause=sql_select($sql_cause);
		if(count($nameArray_cause)>0){
			foreach($nameArray_cause as $row)
			{
				$app_cause1=return_field_value("approval_cause", "fabric_booking_approval_cause", "id='".$row[csf("id")]."' and status_active=1 and is_deleted=0");
				$app_cause = str_replace(array("\r", "\n"), ' ', $app_cause1);
			}
		}
	}

	?>
    <script>
		$( document ).ready(function() {
			document.getElementById("appv_cause").value='<? echo $app_cause; ?>';
		});

		var permission='<? echo $permission; ?>';
		function fnc_appv_entry(operation)
		{
			var appv_cause = $('#appv_cause').val();

			if (form_validation('appv_cause','Approval Cause')==false)
			{
				if (appv_cause=='')
				{
					alert("Please write cause.");
				}
				return;
			}
			else
			{
				var data="action=save_update_delete_appv_cause&operation="+operation+get_submitted_data_string('appv_cause*wo_id*appv_type*page_id*app_user_id*approval_id',"../../");
				//alert (data);return;
				freeze_window(operation);
				http.open("POST","purchase_requisition_approval_group_by_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange=fnc_appv_entry_Reply_info;
			}
		}

		function fnc_appv_entry_Reply_info()
		{
			if(http.readyState == 4)
			{
				var reponse=http.responseText.split('**');
				show_msg(reponse[0]);
				set_button_status(1, permission, 'fnc_appv_entry',1);
				release_freezing();
				appv_cause= $("#appv_cause").val();
				document.getElementById('hidden_appv_cause').value=appv_cause;
				parent.emailwindow.hide();
			}
		}

		
		
    </script>
    <body>
		<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
                    	<textarea name="appv_cause" id="appv_cause" class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character"><?php echo $app_cause;?></textarea>
                        <input type="hidden" name="wo_id" class="text_boxes" ID="wo_id" value="<? echo $wo_id; ?>" style="width:30px" />
                        <input type="hidden" name="appv_type" class="text_boxes" ID="appv_type" value="<? echo $app_type; ?>" style="width:30px" />
                        <input type="hidden" name="page_id" class="text_boxes" ID="page_id" value="<? echo $menu_id; ?>" style="width:30px" />
                        <input type="hidden" name="app_user_id" class="text_boxes" ID="app_user_id" value="<? echo $app_user_id; ?>" style="width:30px" />
                        <input type="hidden" name="approval_id" class="text_boxes" ID="approval_id" value="<? echo $approval_id; ?>" style="width:30px" />
                    </td>
                </tr>
            </table>

            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >

                <tr>
                    <td align="center" class="button_container">
                        <?
                            // if(!empty($app_cause))
                            // {
                            //     echo load_submit_buttons($permission, "fnc_appv_entry", 1,0,"reset_form('size_1','','','','','');",1);
                            // }
                            // else
                            // {
                            //     echo load_submit_buttons($permission, "fnc_appv_entry", 0,0,"reset_form('size_1','','','','','');",1);
                            // }
                        ?>
                        <input type="hidden" name="hidden_appv_cause" id="hidden_appv_cause" class="text_boxes /">

                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px" />
                    </td>
                </tr>
            </table>
            </fieldset>
            </form>
        </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}


if ($action=="save_update_delete_requ_qty")
{	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	for ($i=1; $i<=$tot_row; $i++)
    {
		$req_dtls_id = "req_dtls_id_".$i;
		if(str_replace("'","",$$req_dtls_id)) $all_req_dtls_ids[str_replace("'","",$$req_dtls_id)]=str_replace("'","",$$req_dtls_id);
	}
	
	if(count($all_req_dtls_ids)>0)
	{
		$requisition_arr=return_library_array( "select id, rate from inv_purchase_requisition_dtls where id in(".implode(",",$all_req_dtls_ids).") ", 'id', 'rate' );
	}
	//echo "10**<pre>";print_r($requisition_arr);oci_rollback($con);disconnect($con);die;
	$field_array_up = "quantity*amount*updated_by*update_date";
	for ($i=1; $i<=$tot_row; $i++)
    {
		$txtqty = "txtqty_".$i;
		$req_dtls_id = "req_dtls_id_".$i;
		$amount=str_replace("'",'',$$txtqty)*$requisition_arr[str_replace("'",'',$$req_dtls_id)];
		$updateID_array[] = str_replace("'",'',$$req_dtls_id);
		$data_array_up[str_replace("'",'',$$req_dtls_id)] = explode("*",("".$$txtqty."*'".$amount."'*".$user_id."*'".$pc_date_time."'"));	
	}

	//echo bulk_update_sql_statement("inv_purchase_requisition_dtls","id",$field_array_up,$data_array_up,$updateID_array);
	$dtlsrID=execute_query(bulk_update_sql_statement("inv_purchase_requisition_dtls","id",$field_array_up,$data_array_up,$updateID_array),1);

	if($db_type==0)
	{
		if($dtlsrID)
		{
			mysql_query("COMMIT");
			echo "1**";
		}
		else
		{
			mysql_query("ROLLBACK");
			echo "10**";
		}
	}
	if($db_type==2 || $db_type==1)
	{
	    if($dtlsrID)
		{
			oci_commit($con);
			echo "1**";
		}
		else
		{
			oci_rollback($con);
			echo "10**";
		}
	}
	disconnect($con);
	die;
}

if ($action=="unapprove_request_action")
{
	echo load_html_head_contents("Un Approval Request","../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data_all=explode('_',$data);
	$requ_unapprove=$data_all[1];
		//$unapp_request=$data_all[1];
	?>
	<div align="center" style="width:100%;">
			<? echo load_freeze_divs ("../",$permission,1); ?>
			<form name="size_1" id="size_1">
				<fieldset style="width:450px;">
				<table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
					<tr id="row_1">
						<td width="150" align="center" >
							<textarea name="unappv_request" id="unappv_request" readonly class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character"><? echo $requ_unapprove;?> </textarea>
							<Input type="hidden" name="wo_id" class="text_boxes" ID="wo_id" value="<? echo $wo_id; ?>" style="width:30px" />
						
						</td>
					</tr>
				</table> 
				
				<table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >
					
				
					<tr>
						<td align="center">
							<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px" />
						</td>	  
					</tr>
				</table>
				</fieldset>
				</form>
				<script src="../includes/functions_bottom.js" type="text/javascript"></script>
			</div>

	<?

exit();
}


if ($action=="send_requisition_app_mail")
{

	include('../../mailer/class.phpmailer.php');
	include('../../auto_mail/setting/mail_setting.php');
	
	$user_maill_arr=return_library_array("select id,USER_EMAIL from USER_PASSWD","id","USER_EMAIL");
	$department_arr=return_library_array( "select id, department_name from lib_department", 'id', 'department_name' );
	$user_arr=return_library_array( "select id, user_name from user_passwd", 'id', 'user_name' );	

	list($sysId,$app_user_id,$company_id,$type)=explode('__',$data);
	$sysIdArr = explode('*',$sysId);


	if($mailId)$mailToArr[]=str_replace('*',',',$mailId);
	//  echo $data;
	foreach($sysIdArr as $sysId){
		$sql = " select A.COMPANY_ID,A.ID, A.REQU_NO, A.REQU_PREFIX_NUM ,A.REMARKS, A.COMPANY_ID, TO_CHAR(a.insert_date,'YYYY') AS YEAR, listagg(b.item_category, ',') within group (order by b.item_category) as ITEM_CATEGORY_ID , A.REQUISITION_DATE, A.DELIVERY_DATE, 0 AS APPROVAL_ID, A.IS_APPROVED, A.DEPARTMENT_ID, SUM(B.AMOUNT) AS REQ_VALUE,a.INSERTED_BY,a.APPROVED_BY,a.APPROVED_DATE from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b where a.id=b.mst_id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.ENTRY_FORM=69 and a.id in($sysId) group by A.COMPANY_ID,a.id,a.remarks, a.company_id, a.requ_no, a.requ_prefix_num ,TO_CHAR(a.insert_date,'YYYY'), a.requisition_date, a.delivery_date, a.is_approved, a.department_id,a.INSERTED_BY,a.APPROVED_BY,a.APPROVED_DATE";
		 //echo $sql;
		$sql_dtls=sql_select($sql);

		if($sql_dtls[0]['IS_APPROVED'] == 1){$massage = "Full Approved";}
		else if($sql_dtls[0]['IS_APPROVED'] == 2){$massage = "Deny";}
		else if($sql_dtls[0]['IS_APPROVED'] == 3){$massage = "Partial Approved";}
		else{$massage = "Unapproved";}
		$subject = "This Purchase Requisition is $massage";


		ob_start();	
		?>
		<p><b>Dear Concern,</b>	<br />			
		Below  Purchase Requisition is <?= $massage;?>.</p>
		<table rules="all" border="1">
			<tr bgcolor="#CCCCCC">
				<td>SL</td>
				<td>Requisition No</td>
				<td>Year</td>
				<td>Item Category</td>
				<td>Depatment</td>
				<td>Requisition Value</td>
				<td>Requisition Date</td>
				<td>Last Approval Date Time & Person</td>
			</tr>
			<?php 
			$i=1;
			foreach($sql_dtls as $row){ 
				if($user_maill_arr[$row['INSERTED_BY']]){$mailToArr[$row['INSERTED_BY']]=$user_maill_arr[$row['INSERTED_BY']];}
				if($user_maill_arr[$app_user_id]){$mailToArr[$app_user_id]=$user_maill_arr[$app_user_id];}
			?>
			<tr>
				<td><?=$i;?></td>
				<td><?=$row['REQU_NO'];?></td>
				<td><?=$row['YEAR'];?></td>
				<td>
				<?
					$item_name_arr=array();
					foreach(explode(',',$row['ITEM_CATEGORY_ID']) as $item_id){
						$item_name_arr[$item_id]=$item_category[$item_id];
					}
					echo implode(', ',$item_name_arr);
				?>                
                </td>
				<td><?= $department_arr[$row['DEPARTMENT_ID']]; ?></td>
				<td><?= $row['REQ_VALUE']; ?></td>
				<td><?= change_date_format($row['REQUISITION_DATE']); ?></td>
				<td align="center"><? echo $row['APPROVED_DATE'] .'<br>'.$row['APPROVED_BY']; ?></td>
			</tr>
			<?php $i++;} ?>
		</table>
		<?	
			
			$message=ob_get_contents();
			ob_clean();
			$header=mailHeader();
			$to=implode(',',$mailToArr);
			
			if($to!="")echo sendMailMailer( $to, $subject, $message, $from_mail);
			echo $to. $message;

	}
	exit();	

}
?>