<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
$menu_id=$_SESSION['menu_id'];



if($action=='user_popup')
{
	echo load_html_head_contents("Approval User Info","../../",1, 1,'',1,'');
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
		 	$Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');	;
			$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation,b.SEQUENCE_NO,b.GROUP_NO from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$company_id  and valid=1 and a.id!=$user_id  and a.is_deleted=0 and b.is_deleted=0 and b.entry_form=17 order by b.sequence_no";
		 	 //echo $sql;die;
		 	$arr=array (2=>$custom_designation,3=>$Department);
		 	echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department,Seq. no,Group no", "100,120,150,180,50,50","730","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id,sequence_no,group_no", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,7,7' ) ;
		?>
	</form>
	<?
	exit();
}

if ($action=="load_supplier_dropdown")
{
    $data = explode('_',$data); 
    echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type in(1,7) and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
    //and b.party_type =9
    exit();
}

function getSequence($parameterArr=array()){
	//$lib_item_arr=implode(',',(array_keys($parameterArr['lib_item_arr'])));
	$lib_item_id_string=implode(',',(array_keys($parameterArr['lib_item_arr']))); 
	//Electronic app setup data.....................
	$electronic_app_sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,GROUP_NO,ENTRY_FORM,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,SUPPLIER_ID,DEPARTMENT,ITEM_CATEGORY as ITEM_ID FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['ENTRY_FORM']} AND IS_DELETED = 0 order by SEQUENCE_NO";
	 //echo $electronic_app_sql;die;
     $electronic_app_sql_result=sql_select($electronic_app_sql);
     $dataArr=array();
     foreach($electronic_app_sql_result as $rows){	
		if($rows['ITEM_ID']=='' || $rows['ITEM_ID'] == 0){$rows['ITEM_ID'] = $lib_item_id_string;}

		$dataArr['user_by'][$rows['USER_ID']]=$rows;
		$dataArr['sequ_by'][$rows['SEQUENCE_NO']]=$rows;
		$dataArr['group_user_by'][$rows['GROUP_NO']][$rows['USER_ID']]=$rows;
		$dataArr['group_arr'][$rows['GROUP_NO']]=$rows['GROUP_NO'];
		$dataArr['group_seq_arr'][$rows['GROUP_NO']][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
		$dataArr['group_by_seq_arr'][$rows['SEQUENCE_NO']]=$rows['GROUP_NO'];

		$dataArr['bypass_seq_arr'][$rows['BYPASS']][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
		$dataArr['group_bypass_arr'][$rows['GROUP_NO']][$rows['BYPASS']]=$rows['BYPASS'];
	}
 
	return $dataArr;
}


function getFinalUser($parameterArr=array()){
	$lib_item_id_string=implode(',',(array_keys($parameterArr['lib_item_arr'])));

	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT,GROUP_NO,ITEM_CATEGORY as ITEM_ID FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['ENTRY_FORM']} AND IS_DELETED = 0  order by SEQUENCE_NO";
	  //echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){
        if($rows['ITEM_ID']=='' || $rows['ITEM_ID'] == 0){
            $rows['ITEM_ID'] = $lib_item_id_string;
        }

		$usersDataArr[$rows['USER_ID']]['ITEM_ID']=explode(',',$rows['ITEM_ID']);
		$userSeqDataArr[$rows['USER_ID']]=$rows['SEQUENCE_NO'];
		$userGroupDataArr[$rows['USER_ID']]=$rows['GROUP_NO'];
		$groupBypassNoDataArr[$rows['GROUP_NO']][$rows['BYPASS']][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
	}

	 //print_r($userDataArr[446]['BUYER_ID']);die;
	// print_r($buyer_wise_brand_id_arr[22]);die;
 
	$finalSeq=array();
	foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){
		foreach($userSeqDataArr as $user_id=>$seq){
			if(
				(in_array($bbtsRows['item_id'],$usersDataArr[$user_id]['ITEM_ID']) || $bbtsRows['item_id'] == 0)
			){
				$finalSeq[$sys_id][$user_id]=$seq;
			}
		 
		}
	}

	 //print_r($finalSeq[332]);die;
	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr,'user_group'=>$userGroupDataArr,'group_bypass_no_data_arr'=>$groupBypassNoDataArr);
}


//$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
//$item_cat_arr = return_library_array( "select id, SHORT_NAME from LIB_ITEM_CATEGORY_LIST", "id", "SHORT_NAME"  );

if($action=="report_generate")
{ 
	$process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    
	$txt_alter_user_id = str_replace("'","",$txt_alter_user_id);
    $company_name=str_replace("'","",$cbo_company_name);
	$cbo_supplier_id = str_replace("'","",$cbo_supplier_id);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	$cbo_item_category_id = str_replace("'","",$cbo_item_category_id);
	$txt_wo_no = str_replace("'","",$txt_wo_no);
	$approval_type=str_replace("'","",$cbo_approval_type);
	$user_id_approval=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;

 
	$item_cat_arr=return_library_array( "select id, SHORT_NAME from LIB_ITEM_CATEGORY_LIST", "id", "SHORT_NAME"  );
	$company_arr = return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

 
	
   
	if($company_name>0){$all_company_arr[$company_name]=$company_name;}
	else{
		$all_company_arr=return_library_array( "select company_id, company_id from electronic_approval_setup where entry_form=17 and user_id=$user_id_approval and is_deleted=0",'company_id','company_id');
	}
	
	
	?>
	<form name="requisitionApproval_2" id="requisitionApproval_2">
    <fieldset style="width:940px; margin-top:10px">
    <legend>Other Purchase Work Order Approval</legend>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="940" class="rpt_table"  align="left" >
            <thead>
                <th width="50"></th>
                <th width="60">SL</th>
                <th width="60">Company</th>
                <th width="150">Work Order No</th>
                <th width="100">Item Category</th>
                <th width="120">Supplier</th>
                <th width="120">Work Order Date</th>
                <th width="80">Delivery Date</th>
                <th>Cause</th>
            </thead>
        </table>
        <div style="width:960px; overflow-y:scroll; max-height:330px; margin:0 auto;" id="buyer_list_view">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="940" class="rpt_table" id="tbl_list_search" align="left">
                <tbody>
     <?	
	
	$i=1;
	foreach($all_company_arr as $company_name=>$company){
       
		$where_con = '';
		if($txt_date_from!="" && $txt_date_to!=""){$where_con=" and a.wo_date between '$txt_date_from' and '$txt_date_to'";}
		if($cbo_supplier_id!=0){$where_con .= "and a.supplier_id=$cbo_supplier_id";}
		if($cbo_item_category_id!=0){$where_con .= "and b.item_category_id=$cbo_item_category_id";}
		if($txt_wo_no!=0){$where_con .= "and a.WO_NUMBER like('%$txt_wo_no')";}

        //echo 111;die;
		$electronicDataArr=getSequence(array('company_id'=>$company_name,'ENTRY_FORM'=>17,'user_id'=>$user_id_approval,'lib_item_arr'=>$item_cat_arr,'lib_brand_arr'=>0,'product_dept_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0));

	 
		$my_seq = $electronicDataArr['user_by'][$user_id_approval]['SEQUENCE_NO'];
		$my_group = $electronicDataArr['user_by'][$user_id_approval]['GROUP_NO'];

		$my_group_seq_arr = $electronicDataArr['group_seq_arr'][$my_group];
		$electronicDataArr['group_seq_arr'][0] = [0] + $electronicDataArr['group_seq_arr'][1];

		$my_previous_bypass_no_seq = 0;
		rsort($electronicDataArr['bypass_seq_arr'][2]);
		foreach($electronicDataArr['bypass_seq_arr'][2] as $uid => $seq){
			if($seq<$my_seq){$my_previous_bypass_no_seq = $seq;break;}
		}
 

 	if($approval_type==0) // Un-Approve
	{   
		if($electronicDataArr['user_by'][$user_id_approval]['ITEM_ID']){
			$where_con .= " and b.ITEM_CATEGORY_ID in(".$electronicDataArr['user_by'][$user_id_approval]['ITEM_ID'].",0)";
			$electronicDataArr['sequ_by'][0]['ITEM_ID']=$electronicDataArr['user_by'][$user_id_approval]['ITEM_ID'];
		 }
		// echo $where_con;die;
        
		 $data_mast_sql = "SELECT a.ID,b.ITEM_CATEGORY_ID,a.APPROVED_GROUP_BY,a.APPROVED_SEQU_BY from wo_non_order_info_mst a,wo_non_order_info_dtls b  where a.id=b.mst_id and a.company_name=$company_name  and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0  and a.IS_APPROVED<>1 and a.READY_TO_APPROVED=1  and a.entry_form=147 $where_con group by a.ID,b.ITEM_CATEGORY_ID,a.APPROVED_GROUP_BY,a.APPROVED_SEQU_BY ";
		//echo $data_mast_sql;die;



		$tmp_sys_id_arr = array(); $sys_data_arr = array();
		$data_mas_sql_res = sql_select( $data_mast_sql );
		foreach ($data_mas_sql_res as $row)
		{    

			$group_stage_arr = array();
			for($group = ($row['APPROVED_GROUP_BY'] == $my_group && $electronicDataArr['sequ_by'][$my_seq]['BYPASS'] == 2)?$my_group:$my_group-1;$group >= 0; $group-- ){
				
				krsort($electronicDataArr['group_seq_arr'][$group]);
				foreach($electronicDataArr['group_seq_arr'][$group] as $seq){

					if($seq<$my_seq){ 
						if(  
							(in_array($row['ITEM_CATEGORY_ID'],explode(',',$electronicDataArr['sequ_by'][$seq]['ITEM_ID'])) || $row['ITEM_CATEGORY_ID']==0) && ($row['APPROVED_GROUP_BY'] <= $group)
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
							
				
							if( (in_array($row['APPROVED_SEQU_BY'],$electronicDataArr['group_seq_arr'][$my_group]) && ($row['APPROVED_SEQU_BY'] != $my_previous_bypass_no_seq ) && $electronicDataArr['group_bypass_arr'][$my_group][2] !=2 ) || (count($group_stage_arr[$row['ID']]) > 1) || ($my_previous_bypass_no_seq < $my_seq) && ($row['APPROVED_SEQU_BY'] < $my_previous_bypass_no_seq ) ){ 
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
	 	
 
		
		$sql='';
		for($group=0;$group<=$my_group; $group++ ){
			foreach($electronicDataArr['group_seq_arr'][$group] as $seq){
		
				if(count($tmp_sys_id_arr[$group][$seq])){
					$sys_con = where_con_using_array($tmp_sys_id_arr[$group][$seq],0,'a.ID');
					if($sql!=''){$sql .=" UNION ALL ";}
					
					$sql .= "SELECT DISTINCT (a.id), a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.wo_number, a.item_category, a.currency_id, a.wo_basis_id, a.pay_mode, a.source, a.attention, a.requisition_no,  a.delivery_place, a.location_id FROM wo_non_order_info_mst a, wo_non_order_info_dtls b WHERE a.id = b.mst_id and a.entry_form=147 and a.company_name=$company_name and b.item_category_id not in(1,5,6,7,23)  and a.is_approved<>1 and a.ready_to_approved =1 and a.APPROVED_SEQU_BY=$seq $sys_con and a.APPROVED_GROUP_BY=$group and a.status_active=1 and a.is_deleted=0 group by a.id, a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.wo_number, a.item_category, a.currency_id, a.wo_basis_id, a.pay_mode, a.source, a.attention, a.requisition_no,  a.delivery_place, a.location_id ";
				}

				
			}
		}
		//$sql = "select * from ($sql) x  order by x.id DESC";
	}
	else
	{   
		if($electronicDataArr['user_by'][$user_id_approval]['ITEM_ID']){
			$where_con .= " and b.ITEM_CATEGORY_ID in(".$electronicDataArr['user_by'][$user_id_approval]['ITEM_ID'].",0)";
			$electronicDataArr['sequ_by'][0]['ITEM_ID']=$electronicDataArr['user_by'][$user_id_approval]['ITEM_ID'];
		}
		
		$sql = "SELECT a.ID, a.COMPANY_NAME, a.WO_NUMBER_PREFIX_NUM, a.SUPPLIER_ID, a.WO_DATE, a.DELIVERY_DATE, a.WO_NUMBER, a.item_category, a.currency_id, a.wo_basis_id, a.pay_mode, a.source, a.attention, a.requisition_no,  a.delivery_place, a.location_id FROM wo_non_order_info_mst a, wo_non_order_info_dtls b,APPROVAL_MST c 
		WHERE a.id = b.mst_id and a.entry_form=147 and a.company_name=$company_name and a.is_approved<>0 and b.item_category_id not in(1,5,6,7,23) and  a.ready_to_approved =1  and a.status_active=1 and a.is_deleted=0  and  a.APPROVED_GROUP_BY=c.GROUP_NO  and c.GROUP_NO={$electronicDataArr['user_by'][$user_id_approval]['GROUP_NO']} and c.entry_form=17 and c.mst_id=a.id $where_con group by a.id, a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.wo_number, a.item_category, a.currency_id, a.wo_basis_id, a.pay_mode, a.source, a.attention, a.requisition_no,  a.delivery_place, a.location_id"; 



    }
 	//echo $sql;die();


         $print_report_format=return_field_value("format_id","lib_report_template","template_name =".$company_name." and module_id=5 and report_id =30 and is_deleted=0 and status_active=1");
                    	 
						$format_idsArr=explode(",",$print_report_format);
						$format_ids=$format_idsArr[0];

                            // echo ($sql);
                            $j=0;
							$supplier=return_library_array("select id, supplier_name from lib_supplier ",'id','supplier_name');
                            $nameArray=sql_select( $sql );
                            foreach ($nameArray as $row)
                            {
								if ($i%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";

                                $variable='';
    							if($format_ids==84)
    							{
    								$action="spare_parts_work_order_print2";
									$variable="<a href='#'  title='".$format_ids."'  onClick=\"generate_worder_report_print('".$row[csf('wo_number')]."','".$row[csf('company_name')]."','".$row[csf('id')]."','".$action."')\"> ".$row[csf('wo_number')]." <a/>";
    							}
    							else if($format_ids==85)
    							{
    								$action="spare_parts_work_order_print3";
									$variable="<a href='#'  title='".$format_ids."'  onClick=\"generate_worder_report_print('".$row[csf('wo_number')]."','".$row[csf('company_name')]."','".$row[csf('id')]."','".$action."')\"> ".$row[csf('wo_number')]." <a/>";
    							}
    							else if($format_ids==134)
    							{
    								$action="spare_parts_work_order_po_print";
									$variable="<a href='#'  title='".$format_ids."'  onClick=\"generate_worder_report_print('".$row[csf('wo_number')]."','".$row[csf('company_name')]."','".$row[csf('id')]."','".$action."')\"> ".$row[csf('wo_number')]." <a/>";

    							}
								else if($format_ids==354)
    							{
    								$action="spare_parts_work_print_8";
									$variable="<a href='#'  title='".$format_ids."'  onClick=\"generate_worder_report_print('".$row[csf('wo_number')]."','".$row[csf('company_name')]."','".$row[csf('id')]."','".$action."')\"> ".$row[csf('wo_number')]." <a/>";

    							}
                                else if($format_ids==235)
                                {

                                    $action="spare_parts_work_order_print9";
                                    $variable="<a href='#'  title='".$format_ids."'  onClick=\"generate_worder_report_print('".$row[csf('wo_number')]."','".$row[csf('company_name')]."','".$row[csf('id')]."','".$action."')\"> ".$row[csf('wo_number')]." <a/>";
                                     
                                } else if($format_ids==274)
                                {

                                    $action="spare_parts_work_order_print10";
                                    $variable="<a href='#'  title='".$format_ids."'  onClick=\"generate_worder_report_print('".$row[csf('wo_number')]."','".$row[csf('company_name')]."','".$row[csf('id')]."','".$action."')\"> ".$row[csf('wo_number')]." <a/>";
                                     
                                }

                                else if($format_ids==227)
                                {

                                    $action="spare_parts_work_order_print8";
                                    $type=8;
                                    $variable="<a href='#'  title='".$format_ids."'  onClick=\"generate_worder_report_print_2('".$row[csf('wo_number')]."','".$row[csf('company_name')]."','".$row[csf('id')]."','".$action."',".$type.")\"> ".$row[csf('wo_number')]." <a/>";
                                     
                                }
                                else if($format_ids==117)
                                {

                                    $action="spare_parts_work_print_urmi";
                                    $type=7;
                                    $variable="<a href='#'  title='".$format_ids."'  onClick=\"generate_worder_report_print_2('".$row[csf('wo_number')]."','".$row[csf('company_name')]."','".$row[csf('id')]."','".$action."',".$type.")\"> ".$row[csf('wo_number')]." <a/>";
                                     
                                }
                                else if($format_ids==137)
                                {

                                    $action="spare_parts_work_print";
                                    $type=5;
                                    $variable="<a href='#'  title='".$format_ids."'  onClick=\"generate_worder_report_print_2('".$row[csf('wo_number')]."','".$row[csf('company_name')]."','".$row[csf('id')]."','".$action."',".$type.")\"> ".$row[csf('wo_number')]." <a/>";
                                     
                                }
                                else if($format_ids==732)
                                {

                                    $action="spare_parts_work_order_po_print";
                                    $variable="<a href='#'  title='".$format_ids."'  onClick=\"generate_worder_report_print('".$row[csf('wo_number')]."','".$row[csf('company_name')]."','".$row[csf('id')]."','".$action."')\"> ".$row[csf('wo_number')]." <a/>";
                                     
                                }
                                else if($format_ids==129)
                                {

                                    $action="spare_parts_work_print";
                                    $type=6;
                                    $variable="<a href='#'  title='".$format_ids."'  onClick=\"generate_worder_report_print_2('".$row[csf('wo_number')]."','".$row[csf('company_name')]."','".$row[csf('id')]."','".$action."',".$type.")\"> ".$row[csf('wo_number')]." <a/>";
                                     
                                }
                                else if($format_ids==430)
                                {

                                    $action="spare_parts_work_order_po_print2";
                                    $variable="<a href='#'  title='".$format_ids."'  onClick=\"generate_worder_report_print('".$row[csf('wo_number')]."','".$row[csf('company_name')]."','".$row[csf('id')]."','".$action."')\"> ".$row[csf('wo_number')]." <a/>";
                                     
                                }

                                else 
                                {
                                    $action="spare_parts_work_order_print2";
									$variable="<a href='#'  title='".$format_ids."'  onClick=\"generate_worder_report_print('".$row[csf('wo_number')]."','".$row[csf('company_name')]."','".$row[csf('id')]."','".$action."')\"> ".$row[csf('wo_number')]." <a/>";
                                }
								
								

								
								//echo "select max(id) as id from approval_history where mst_id ='".$row[csf('id')]."' and entry_form='17' and approved_by = '$user_id' ";
								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                	<td width="50" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="req_id_<? echo $i;?>" name="req_id[]" type="hidden" style="width:30px;" value="<? echo $row[csf('id')]; ?>" />
                                        <input id="requisition_id_<? echo $i;?>" name="requisition_id[]" type="hidden" value="<? echo $row[csf('id')]; ?>" /><!--this is uesd for delete row-->
                                        <input id="<? echo strtoupper($row[csf('wo_number_prefix_num')]); ?>" name="no_wo[]" type="hidden" value="<? echo $i;?>" />
                                        
                                        <input id="mst_id_company_id_<?=$i;?>" name="mst_id_company_id[]" type="hidden" value="<?=$row[csf('id')]."*".$app_id.'*'.$company_name; ?>" />
                                        
                                        
                                        
                                    </td>
									<td width="60" align="center"><? echo $i; ?></td>
                                    <td width="60"><?=$company_arr[$row[csf('company_name')]]; ?></td>
									<td width="150" align="center">
                                    	<p><? echo $variable; //$row[csf('wo_number_prefix_num')]; ?></p>
                                    </td>
                                    <td width="120"><p><? echo $supplier[$row[csf('supplier_id')]]; ?></p></td>
									<td width="120" align="center"><? if($row[csf('wo_date')]!="0000-00-00") echo change_date_format($row[csf('wo_date')]); ?></td>
									<td width="100"><?= $item_cat_arr[$row['ITEM_CATEGORY']]; ?></td>
									<td width="80"align="center"><? if($row[csf('delivery_date')]!="0000-00-00") echo change_date_format($row[csf('delivery_date')]); ?></td>
							
                                    <td  align="center"> <input style="width:160px;" type="text" class="text_boxes"  name="txtCause_<? echo $row[csf('id')];?>" id="txtCause_<? echo $row[csf('id')];?>" placeholder="Write" value=""/></td>
                                    
								</tr>
								<?
								$i++;$j;
							}
						}//end company loof
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="940" class="rpt_table" align="left">
				<tfoot>
                    <td width="50" align="center" ><input type="checkbox" id="all_check" onClick="check_all('all_check')" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/></td>
				</tfoot>
			</table>
        </fieldset>
    </form>
    <?
	exit();
}


if($action=="refusing_cause_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Refusing Cause Info","../../", 1, 1, $unicode);
	?>
    <script>
 	var permission='<? echo $permission; ?>';

	function set_values( cause )
	{
		var refusing_cause = document.getElementById('txt_refusing_cause').value;
		if(refusing_cause == '')
		{
			document.getElementById('txt_refusing_cause').value =refusing_cause;
			parent.emailwindow.hide();
		}
		else
		{
			document.getElementById('txt_refusing_cause').value = $("#txt_refusing_cause").val();;
			parent.emailwindow.hide();
		}

	}

	function fnc_cause_info( operation )
	{
		var refusing_cause=$("#txt_refusing_cause").val();
		var quo_id=$("#hidden_quo_id").val();
  		if (form_validation('txt_refusing_cause','Refusing Cause')==false)
		{
			return;
		}
		else
		{
			var data="action=save_update_delete_refusing_cause&operation="+operation+"&refusing_cause="+refusing_cause+"&quo_id="+quo_id;
			http.open("POST","other_purchase_work_order_approval_group_by_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_cause_info_reponse;
		}
	}
	function fnc_cause_info_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split('**');
			if(response[0]==0)
			{
				alert("Refusing Cause saved successfully");
				document.getElementById('txt_refusing_cause').value =response[1];
				parent.emailwindow.hide();
			}
			else
			{
				alert("Refusing Cause not saved");
				return;
			}
		}
	}

    </script>
    <body  onload="set_hotkey();">
    <div align="center" style="width:100%;">
	<fieldset style="width:470px;">
		<legend>Refusing Cause</legend>
		<form name="causeinfo_1" id="causeinfo_1"  autocomplete="off">
			<table cellpadding="0" cellspacing="2" width="470px">
			 	<tr>
					<td width="100" class="must_entry_caption">Refusing Cause</td>
					<td >
						<input type="text" name="txt_refusing_cause" id="txt_refusing_cause" class="text_boxes" style="width:320px;height: 100px;" value="" />
						<input type="hidden" name="hidden_quo_id" id="hidden_quo_id" value="<? echo $quo_id;?>">
					</td>
                  </tr>
                  <tr>
					<td colspan="4" align="center" class="button_container">
						<?
					    // echo load_submit_buttons( $permission, "fnc_cause_info", 0,0 ,"reset_form('causeinfo_1','','')",1);
				        ?>
				        <input type="button" class="formbutton" value="Close" name="close_buttons" id="close_buttons" onClick="set_values();" style="width:50px;">
 					</td>
				</tr>
				<tr>
					<td colspan="4" align="center">&nbsp;</td>
				</tr>
		   </table>
			</form>
		</fieldset>
	</div>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</body>
    <?
	exit();
}

if($action=="save_update_delete_refusing_cause")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $_REQUEST ));
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}		
		$entry_form=17;
		
		
		$get_history = sql_select("SELECT id from approval_history where mst_id='$quo_id' and entry_form =$entry_form and current_approval_status=1");
		$id=return_next_id( "id", "refusing_cause_history", 1);
		$field_array = "id,entry_form,mst_id,refusing_reason,inserted_by,insert_date";
		$data_array = "(".$id.",$entry_form,".$quo_id.",'".$refusing_cause."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		//echo "10**insert into refusing_cause_history (".$field_array.") values ".$data_array; die;
		$field_array_update ="un_approved_by*un_approved_date*current_approval_status*un_approved_reason* updated_by*update_date";
		$data_array_update = "".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*".$refusing_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_insert("refusing_cause_history",$field_array,$data_array,1);
		$rID2=execute_query("update wo_non_order_info_mst set ready_to_approved=0 ,IS_APPROVED=0, updated_by = ".$_SESSION['logic_erp']['user_id']." , update_date = '".$pc_date_time."' where id='$quo_id'");
		if(count($get_history)>0)
		{
			$rID3=execute_query("update approval_history set un_approved_by=".$_SESSION['logic_erp']['user_id']." ,un_approved_date='".$pc_date_time."', current_approval_status =0, un_approved_reason= '".$refusing_cause."', updated_by = ".$_SESSION['logic_erp']['user_id']." , update_date = '".$pc_date_time."' where mst_id='$quo_id' and entry_form =$entry_form and current_approval_status=1");
		}
		
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");
				echo "0**$refusing_cause";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);
				echo "0**$refusing_cause";
			}
			else{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
}




if ($action=="approve")
{ 
	$process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    $con = connect();

	$txt_alter_user_id = str_replace("'","",$txt_alter_user_id);
	$appv_causes = str_replace("'","",$appv_causes);
	$mst_id_company_ids = str_replace("'","",$mst_id_company_ids);
	$appv_causes_arr = explode('**',$appv_causes);
  
	$appCompanyArr=array();$appIdArr=array();$appNoArr=array();$appCauseArr=array();
	foreach(explode(',',$mst_id_company_ids) as $key => $ic){
		
		list($companyID,$reqId,$cause)=explode('###',$appv_causes_arr[$key]);
		$appCauseArr[$companyID][$reqId]=$cause;

		list($bno,$bid,$company)=explode('*',$ic);
		$appCompanyArr[$company]=$company;
		$appIdArr[$company][$bid]=$bid;
		$appNoArr[$company][$bno]=$bno;
		
	}

	//print_r($appCauseArr);die;

	 
	$flag=0;$msg='';  $response=$req_nos;
	foreach($appCompanyArr as $cbo_company_name)
	{
		$req_nos=implode(",",$appNoArr[$cbo_company_name]);
		$approval_ids=implode(',',$appIdArr[$cbo_company_name]);
		
		$user_id_approval = ($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;
		$target_app_id_arr = explode(',',$req_nos);	

		$item_cat_arr=return_library_array( "select id, SHORT_NAME from LIB_ITEM_CATEGORY_LIST", "id", "SHORT_NAME"  );
		//............................................................................
		
		$sql = "SELECT a.ID, b.ITEM_CATEGORY_ID,a.READY_TO_APPROVED,a.IS_APPROVED,a.APPROVED_SEQU_BY,a.APPROVED_GROUP_BY FROM  wo_non_order_info_mst a, wo_non_order_info_dtls b WHERE a.id = b.mst_id and  a.COMPANY_NAME=$cbo_company_name and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.READY_TO_APPROVED=1 and a.id in($req_nos)  group by a.ID, b.ITEM_CATEGORY_ID,a.READY_TO_APPROVED,a.IS_APPROVED,a.APPROVED_SEQU_BY,a.APPROVED_GROUP_BY";
		// echo $sql;die();
		$sqlResult=sql_select( $sql );
		$matchDataArr = array(); $last_app_status_arr = array();
		foreach ($sqlResult as $row)
		{
			if($row['READY_TO_APPROVED'] != 1){echo '21**Ready to approve Yes is mandatory';exit();}
			$matchDataArr[$row['ID']]=array('item_id'=>$row['ITEM_CATEGORY_ID'],'brand_id'=>0,'store'=>0);
			$last_app_status_arr[$row['ID']] = $row['IS_APPROVED'];
		}
		
		$finalDataArr = getFinalUser(array('company_id'=>$cbo_company_name,'ENTRY_FORM'=>17,'lib_item_arr'=>$item_cat_arr,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0,'product_dept_arr'=>0,'match_data'=>$matchDataArr));

		//print_r($finalDataArr);die;
	

		$sequ_no_arr_by_sys_id =$finalDataArr['final_seq'];
		$user_sequence_no = $finalDataArr['user_seq'][$user_id_approval];
		$user_group_no = $finalDataArr['user_group'][$user_id_approval];
		$max_group_no = max($finalDataArr['user_group']);

		$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($req_nos) and entry_form=17 group by mst_id","mst_id","approved_no");

		//echo $user_id_approval;die;
 
		//print_r($target_app_id_arr);die;
		$data_array = ''; $history_data_array = ''; $data_array_up = array(); $approved_no_array = array();
		if($approval_type==0)
		{ 

			$id=return_next_id( "id","approval_mst", 1 ) ;
			$ahid=return_next_id( "id","approval_history", 1 ) ;	
			

			foreach($target_app_id_arr as $key => $mst_id)
			{
			
				
				
				$approved = ((max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no) || ( (max($finalDataArr['group_bypass_no_data_arr'][$max_group_no][2])==$user_sequence_no) && ($max_group_no == $user_group_no))  || ( (max($finalDataArr['group_bypass_no_data_arr'][$max_group_no][2]) == '') && ($max_group_no == $user_group_no) ) )?1:3;

				//print_r($finalDataArr['last_bypass_no_data_arr']);die;

				$approved_no = $max_approved_no_arr[$mst_id]*1;
				if($last_app_status_arr[$mst_id] == 0 || $last_app_status_arr[$mst_id] == 2){
					$approved_no = $approved_no+1;
					$approved_no_array[$mst_id] = $approved_no;
				}

				if($data_array!=''){$data_array.=",";}
				$data_array.="(".$id.",17,'".$mst_id."','".$user_sequence_no."','".$user_group_no."',".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."',".$approved.")"; 
				$id=$id+1;
				
			
				if($history_data_array!="") $history_data_array.=",";
				$history_data_array.="(".$ahid.",17,".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$appCauseArr[$cbo_company_name][$mst_id]."','".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,".$approved.")";
				$ahid++;
				
				//mst data.......................
				$data_array_up[$mst_id] = explode(",",("".$approved.",".$user_sequence_no.",".$user_group_no.",'".$pc_date_time."',".$user_id_approval."")); 
			}
		
	
			$flag=1;
			if($flag==1) 
			{  
				$field_array="id, entry_form, mst_id,  sequence_no,group_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip,APPROVED";
				$rID1=sql_insert("approval_mst",$field_array,$data_array,0);
				if($rID1) $flag=1; else $flag=0; 
			}
			
			if($flag==1) 
			{
				$field_array_up="IS_APPROVED*APPROVED_SEQU_BY*APPROVED_GROUP_BY*APPROVED_DATE*APPROVED_BY"; 
				$usql = bulk_update_sql_statement( "wo_non_order_info_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr );
				$rID2=execute_query($usql);
				if($rID2) $flag=1; else $flag=0; 
			}

			if($flag==1)
			{
				$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=17 and mst_id in ($req_nos)";
				$rID3=execute_query($query,1);
				if($rID3) $flag=1; else $flag=0;
			}
			
			if($flag==1)
			{
				$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by,COMMENTS, approved_date, inserted_by, insert_date,IS_SIGNING,APPROVED";
				$rID4=sql_insert("approval_history",$field_array,$history_data_array,0);
				if($rID4) $flag=1; else $flag=0;
			}


			if(count($approved_no_array)>0 && $flag)
			{
				$approved_string="";

				foreach($approved_no_array as $key=>$value)
				{
					$approved_string.=" WHEN TO_NUMBER($key) THEN '".$value."'";
				}

				$approved_string_mst="CASE id ".$approved_string." END";
				$approved_string_dtls="CASE mst_id ".$approved_string." END";

				$sql_insert="insert into wo_non_order_info_mst_history(id, mst_id, approved_no, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, buyer_po, requisition_no, delivery_place, wo_date, supplier_id, attention, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode, terms_and_condition, is_approved, approved_by, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date)
				select
				'', id, $approved_string_mst, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, buyer_po, requisition_no, delivery_place, wo_date, supplier_id, attention, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode, terms_and_condition, is_approved, approved_by, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date from  wo_non_order_info_mst where id in ($req_nos)";

				//echo $sql_insert;die;

				//-----------------------------------------Booking dtls-------------------------------------	
				$sql_insert_dtls="insert into wo_non_order_info_dtls_history(id, approved_no, mst_id, requisition_dtls_id, po_breakdown_id, requisition_no, item_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, req_quantity, supplier_order_quantity, uom,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
				select
				'', $approved_string_dtls, mst_id, requisition_dtls_id, po_breakdown_id, requisition_no, item_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, req_quantity, supplier_order_quantity, uom,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_non_order_info_dtls where mst_id in ($req_nos)";

				if($flag==1)
				{
					$rID5=execute_query($sql_insert,0);
					if($rID5) $flag=1; else $flag=0;
				}

				if($flag==1)
				{
					$rID6=execute_query($sql_insert_dtls,1);
					if($rID6) $flag=1; else $flag=0;
				}
				
			}


			
			 // echo "21**".$rID1.",".$rID2.",".$rID3.",".$rID4.",".$rID5.",".$rID6;oci_rollback($con);die;
			
			if($flag==1) $msg='19'; else $msg='21';

			
		}
		else
		{              
			
			$ahid=return_next_id( "id","approval_history", 1 ) ;	
			foreach($target_app_id_arr as $key => $mst_id)
			{
				
				//print_r($appCauseArr[$cbo_company_name]);die;
				
				$approved_no = $max_approved_no_arr[$mst_id]*1;
				if($history_data_array!="") $history_data_array.=",";
				$history_data_array.="(".$ahid.",17,".$mst_id.",".$approved_no.",'".$user_sequence_no."',0,".$user_id_approval.",'".$appCauseArr[$cbo_company_name][$mst_id]."','".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$ahid++;
			}

			//echo $req_nos;die;

			$rID1=sql_multirow_update("wo_non_order_info_mst","IS_APPROVED*READY_TO_APPROVED*APPROVED_SEQU_BY*APPROVED_GROUP_BY",'0*0*0*0',"id",$req_nos,0);
			if($rID1) $flag=1; else $flag=0;

			if($flag==1)
			{
				$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=17 and mst_id in ($req_nos)";
				$rID2=execute_query($query,1);
				if($rID2) $flag=1; else $flag=0;
			}
			
			if($flag==1) 
			{
				$query="delete from approval_mst  WHERE entry_form=17 and mst_id in ($req_nos)";
				$rID3=execute_query($query,1); 
				if($rID3) $flag=1; else $flag=0; 
			}
			
			if($flag==1)
			{
				$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by,COMMENTS, approved_date, inserted_by, insert_date,IS_SIGNING,APPROVED";
				$rID4=sql_insert("approval_history",$field_array,$history_data_array,0);
				if($rID4) $flag=1; else $flag=0;
			}


			// echo "22**".$rID1.",".$rID2.",".$rID3.",".$rID4.$query;oci_rollback($con);die;
			
			$response=$req_nos;
			if($flag==1) $msg='20'; else $msg='22';
			
		}
		

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
	 

	}//end company if;	

	disconnect($con);
	die;

}


?>
