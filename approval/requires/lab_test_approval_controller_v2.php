<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');
//echo $action;die;

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];

if($action=="approval_setupCheck")
{
	$ex_data=explode("__",$data);
	$approval_setup=is_duplicate_field( "page_id", "electronic_approval_setup", "company_id='$ex_data[0]' and page_id='$ex_data[1]' and is_deleted=0" );
	echo $approval_setup;
	exit();
}

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");

function getSequence($parameterArr=array()){
	$lib_buyer_arr=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));
	//$lib_brand_arr=implode(',',(array_keys($parameterArr['lib_brand_arr'])));
	$buyer_brand_arr=$parameterArr['lib_brand_arr'];

	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND entry_form = {$parameterArr['entry_form']} AND IS_DELETED = 0 order by SEQUENCE_NO";
	// echo $sql;die;
	$sql_result=sql_select($sql);
	$dataArr=array();
	foreach($sql_result as $rows){		
		
		if($rows['BUYER_ID']==''){$rows['BUYER_ID']=$lib_buyer_arr;}
		$temp_brand_arr=array(0=>0);
		foreach(explode(',',$rows['BUYER_ID']) as $buyer_id){
			if(count($parameterArr['lib_brand_arr'][$buyer_id])){
				$temp_brand_arr[]=implode(',',(array_keys($parameterArr['lib_brand_arr'][$buyer_id])));
			}
		}
		if($rows['BRAND_ID']==''){$rows['BRAND_ID']=implode(',',explode(',',implode(',',$temp_brand_arr)));}


		$dataArr['sequ_by'][$rows['SEQUENCE_NO']]=$rows;
		$dataArr['user_by'][$rows['USER_ID']]=$rows;
		$dataArr['sequ_arr'][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
	}
 
	return $dataArr;
}

function getFinalUser($parameterArr=array()){
	$lib_buyer_arr=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));
	$lib_brand_arr=implode(',',(array_keys($parameterArr['lib_brand_arr'])));

	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND entry_form = {$parameterArr['entry_form']} AND IS_DELETED = 0  order by SEQUENCE_NO";
	   //echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){

		if($rows['BUYER_ID']==''){$rows['BUYER_ID']=$lib_buyer_arr;}
		if($rows['BRAND_ID']==''){$rows['BRAND_ID']=$lib_brand_arr;}

		$usersDataArr[$rows['USER_ID']]['BUYER_ID']=explode(',',$rows['BUYER_ID']);
		$usersDataArr[$rows['USER_ID']]['BRAND_ID']=explode(',',$rows['BRAND_ID']);
		$userSeqDataArr[$rows['USER_ID']]=$rows['SEQUENCE_NO'];
	
	}

	

	$finalSeq=array();
	foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){
		
		foreach($userSeqDataArr as $user_id=>$seq){
			if(
				(in_array($bbtsRows['buyer_id'],$usersDataArr[$user_id]['BUYER_ID'])
				||  $bbtsRows['buyer_id'] == 0)
			){
				$finalSeq[$sys_id][$user_id]=$seq;
			}


		}
	}

	  
 
	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr);
}


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));  

	$company_name=str_replace("'","",$cbo_company_name); 
	$cbo_booking_type=str_replace("'","",$cbo_booking_type);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
    $approval_type=str_replace("'","",$cbo_approval_type);
	$booking_year=str_replace("'","",$cbo_booking_year);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
    $user_id_approval=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;
	$menu_id=str_replace("'","",$active_menu_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);

 
	
	$electronicDataArr=getSequence(array('company_id'=>$cbo_company_name,'entry_form'=>78,'user_id'=>$user_id_approval,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>$buyer_brand_arr,'product_dept_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0));


	if ($booking_year!=0) {$where_con=" and YEAR(a.insert_date)='".trim($booking_year)."'";}
	if($txt_date_from!="" && $txt_date_to!=""){$where_con .= " and a.wo_date between '$txt_date_from' and '$txt_date_to'";}
	if($txt_booking_no!=""){$where_con .= " and a.LABTEST_NO like('%$txt_booking_no')";}
	if($cbo_booking_type == 1){$where_con .= " and a.entry_form=79";}
	if($cbo_booking_type == 2){$where_con .= " and a.entry_form=179";}
	
    
	
	//var_dump($electronicDataArr);die;
    if($approval_type==0) // Un-Approve
	{  
		if($electronicDataArr['user_by'][$user_id_approval]['BUYER_ID']){
			$where_con .= " and c.BUYER_NAME in(".$electronicDataArr['user_by'][$user_id_approval]['BUYER_ID'].",0)";
			$electronicDataArr['sequ_by'][0]['BUYER_ID']=$electronicDataArr['user_by'][$user_id_approval]['BUYER_ID'];
		}
 
		$data_mast_sql = "select a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date, 1 as type,a.currency,c.BUYER_NAME from wo_labtest_mst a,wo_labtest_dtls b, wo_po_details_master c where a.id=b.mst_id and b.job_no=c.job_no  and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and a.IS_APPROVED<>1 and a.READY_TO_APPROVED=1 and a.COMPANY_ID=$cbo_company_name $where_con";
		// echo $data_mast_sql;die;
    


		 $tmp_sys_id_arr=array();
		 $data_mas_sql_res=sql_select( $data_mast_sql );
		 foreach ($data_mas_sql_res as $row)
		 { 
			 for($seq=($electronicDataArr['user_by'][$user_id_approval]['SEQUENCE_NO']-1);$seq>=0; $seq-- ){
				 
				 if($electronicDataArr['sequ_by'][$seq]['BUYER_ID']==''){$electronicDataArr['sequ_by'][$seq]['BUYER_ID']=0;}
				 
				 if((in_array($row['BUYER_NAME'],explode(',',$electronicDataArr['sequ_by'][$seq]['BUYER_ID'])) || $row['BUYER_NAME'] == 0))
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
	 
	 // print_r($tmp_sys_id_arr);die;
		
		$sql='';
		for($seq=0;$seq<=count($electronicDataArr['sequ_arr']); $seq++ ){
			$sys_con = where_con_using_array($tmp_sys_id_arr[$seq],0,'a.ID');

			if($tmp_sys_id_arr[$seq]){
				if($sql!=''){$sql .=" UNION ALL ";}
				$sql .= "select a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date, 1 as type,a.currency,c.BUYER_NAME
                from wo_labtest_mst a,wo_labtest_dtls b,wo_po_details_master c   where a.id=b.mst_id and b.job_no=c.job_no and b.STATUS_ACTIVE=1 and b.IS_DELETED=0
                and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and a.IS_APPROVED<>1 and a.READY_TO_APPROVED=1 and a.APPROVED_SEQU_BY=$seq $sys_con group by a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date,a.currency,c.BUYER_NAME ";
			}
		}
	}
	else
	{   

		$sql = "select a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date, 1 as type,a.currency,c.BUYER_NAME
        from wo_labtest_mst a,wo_labtest_dtls b,wo_po_details_master c,APPROVAL_MST d  where a.id=b.mst_id and  b.job_no=c.job_no  and b.STATUS_ACTIVE=1 and b.IS_DELETED=0
        and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and a.IS_APPROVED<>0 and a.READY_TO_APPROVED=1 and d.SEQUENCE_NO={$electronicDataArr['user_by'][$user_id_approval]['SEQUENCE_NO']}  and a.APPROVED_SEQU_BY=d.SEQUENCE_NO and d.entry_form=78 and d.mst_id=a.id $where_con group by a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date,a.currency,c.BUYER_NAME ";

    }
	//echo $sql;die();
		
	
	
	
	?>
    <script>
	function openmypage_app_instrac(wo_id,app_type,i)
	{
		var txt_appv_instra = $("#txt_appv_instra_"+i).val();	
		var approval_id = $("#approval_id_"+i).val();
		
		var data=wo_id+"_"+app_type+"_"+txt_appv_instra+"_"+approval_id;
		
		var title = 'Approval Instruction';	
		var page_link = 'requires/lab_test_approval_controller_v2.php?data='+data+'&action=appinstra_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
			
			$('#txt_appv_instra_'+i).val(appv_cause.value);
		}
	}
	
	function openmypage_app_cause(wo_id,app_type,i)
	{
		var txt_appv_cause = $("#txt_appv_cause_"+i).val();	
		var approval_id = $("#approval_id_"+i).val();
		
		var data=wo_id+"_"+app_type+"_"+txt_appv_cause+"_"+approval_id;
		
		var title = 'Approval Cause Info';	
		var page_link = 'requires/lab_test_approval_controller_v2.php?data='+data+'&action=appcause_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
			$('#txt_appv_cause_'+i).val(appv_cause.value);
		}
	}
	
	function openmypage_unapp_request(wo_id,app_type,i)
	{
		var data=wo_id;
		
		var title = 'Un Approval Request';	
		var page_link = 'requires/lab_test_approval_controller_v2.php?data='+data+'&action=unappcause_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=180px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var unappv_request=this.contentDoc.getElementById("hidden_unappv_request");
			
			$('#txt_unappv_req_'+i).val(unappv_request.value);
		}
	}
	function print_lab_test(company_name,labtest_system_id,currency,labtest_work_order_date)
	{
		print_report( company_name+'*'+labtest_system_id+'*'+currency+'*'+labtest_work_order_date+'*'+'Lab Test Work Order', "show_trim_booking_report_new",
		"../order/woven_order/requires/labtest_work_order_controller" ) ;
		return;
	}
	</script>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:900px; margin-top:10px">
        <legend>Lab Test WO Approval</legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" >
                <thead>
                	<th width="50">&nbsp;</th>
                    <th width="40">SL</th>
                    <th width="130">WO No</th>
                    <th width="80">Type</th>
                    <th width="100">WO Date</th>
                    <th width="125">Buyer</th>
                    <th width="160">Supplier</th>
                    <?
					if($approval_type==0) echo "<th width='80'>Appv Instra</th>";
					if($approval_type==1) echo "<th width='80'>Un-appv request</th>"; 
					?>
                    <th><? if($approval_type==0) echo "Not Appv. Cause" ; else echo "Not Un-Appv. Cause"; ?></th>
                    
                </thead>
            </table>
            <div style="width:900px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <? 
                            $i=1;// echo $sql;
                            $nameArray=sql_select($sql);
                            foreach ($nameArray as $row)
                            {
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								
								$value='';
								if($approval_type==0) $value=$row[csf('id')]; else $value=$row[csf('id')]."**".$row[csf('approval_id')]; 
								
								$value2=$row[csf('id')];
								
								if($cbo_booking_type==1) //With Order
								{
									$booking_type="Order"; 
									$buyer_string=$buyer_arr[$row[csf('BUYER_NAME')]];;
								}
								else
								{
									$booking_type="None Order";
									$buyer_string="";
								}
								
								$buyer_string=implode(",",array_filter(array_unique(explode(",",$buyer_string))));
								
								$supplierName="";
								$supplierName=$supplier_arr[$row[csf('supplier_id')]];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?= $i; ?>"> 
                                	<td width="50" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<?= $i;?>" name="tbl[]" />
                                        <input id="booking_id_<? echo $i;?>" name="booking_id[]" type="hidden" value="<? echo $value; ?>" />
                                        <input id="booking_no_<? echo $i;?>" name="booking_no[]" type="hidden" value="<? echo $row[csf('labtest_no')]; ?>" />
										<input type="hidden" id="target_id_<?= $i;?>" name="target_id_[]"  value="<?= $row['ID']; ?>"   
                                        <input id="<? echo strtoupper($row[csf('labtest_no')]); ?>" name="no_booook[]" type="hidden" value="<? echo $i;?>" />
                                    
                                    </td>   
									<td width="40" align="center"><? echo $i; ?></td>
                                    
                                    
									<td width="130">
										 
										<a href="#" onclick="print_lab_test(<?=$cbo_company_name?>,<?=$row[csf('id')]?>,<?=$row[csf('currency')]?>,<?=change_date_format($row[csf('wo_date')])?>)">
											<?=$row[csf('labtest_no')];?>
												
										</a>
									</td>
                                    <td width="80" align="center"><p><? echo $booking_type; ?></p></td>
									<td width="100" align="center"><? if($row[csf('wo_date')]!="0000-00-00") echo change_date_format($row[csf('wo_date')]); ?>&nbsp;</td>

                                    <td width="125" style="word-break:break-all"><?=$buyer_string; ?>&nbsp;</td>
									<td width="160" style="word-break:break-all"><?=$supplierName; ?>&nbsp;</td>
                                      <?
										if($approval_type==0)echo "<td align='center' width='80'>
                                        		<Input name='txt_appv_instra[]' class='text_boxes' readonly placeholder='Please Browse' ID='txt_appv_instra_".$i."' style='width:70px' maxlength='50' onClick='openmypage_app_instrac(".$value2.",".$approval_type.",".$i.")'></td>";
											if($approval_type==1)echo "<td align='center' width='80'>
                                        		<Input name='txt_unappv_req[]' class='text_boxes' readonly placeholder='Please Browse' ID='txt_unappv_req_".$i."' style='width:70px' maxlength='50' onClick='openmypage_unapp_request(".$value2.",".$approval_type.",".$i.")'></td>"; 
                                        ?>
                                        <td align="center">
                                        	<Input name="txt_appv_cause[]" class="text_boxes" readonly placeholder="Please Browse" ID="txt_appv_cause_<? echo $i;?>" style="width:97px" maxlength="50" title="Maximum 50 Character" onClick="openmypage_app_cause(<? echo $value2; ?>,<? echo $approval_type; ?>,<? echo $i;?>)">&nbsp;</td>
                                            
								</tr>
								<?
								$i++;
							}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="900" class="rpt_table">
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
				$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation,b.SEQUENCE_NO from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and valid=1 and a.id!=$user_id  and b.is_deleted=0 order by b.SEQUENCE_NO";
				// echo $sql;
			$arr=array (2=>$custom_designation,3=>$Department);
			echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department,App. Seq", "100,120,120,130,50","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id,SEQUENCE_NO", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
			?>
			
	</form>
	<script language="javascript" type="text/javascript">
	setFilterGrid("tbl_style_ref");
	</script>
	<?
	exit();
}


if ($action=="approve")
{  //echo 1;die();
	$process = array( &$_POST );

	extract(check_magic_quote_gpc( $process )); 
	$con = connect();

	$company_name=str_replace("'","",$cbo_company_name); 
	$cbo_booking_type=str_replace("'","",$cbo_booking_type);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
    $approval_type=str_replace("'","",$cbo_approval_type);
	$booking_year=str_replace("'","",$cbo_booking_year);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
    $user_id_approval=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;
	$menu_id=str_replace("'","",$active_menu_id);
	$target_ids=str_replace("'","",$target_ids);
	$appv_causes=str_replace("'","",$appv_causes);
	$target_app_id_arr = explode(',',$target_ids);
	$appv_cause_arr = explode('**',$appv_causes);
	


	//............................................................................
	
	$sql = "select a.ID,a.BUYER_ID,a.IS_APPROVED  from wo_labtest_mst a where a.COMPANY_ID=$cbo_company_name and a.STATUS_ACTIVE=1 and a.IS_DELETED=0  and a.id in($target_ids) and READY_TO_APPROVED=1";
	//echo $sql;die();
	$sqlResult=sql_select( $sql );
	foreach ($sqlResult as $row)
	{
		$matchDataArr[$row['ID']]=array('buyer_id'=>$row['BUYER_ID'],'brand_id'=>0,'supplier_id'=>0,'store'=>0);
		$last_app_status_arr[$row['ID']] = $row['IS_APPROVED'];
	}
	
	$finalDataArr=getFinalUser(array('company_id'=>$cbo_company_name,'entry_form'=>78,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'lib_item_cat_arr'=>$item_cat_arr,'lib_store_arr'=>0,'product_dept_arr'=>0,'match_data'=>$matchDataArr));
	
 
	$sequ_no_arr_by_sys_id =$finalDataArr['final_seq'];
	$user_sequence_no = $finalDataArr['user_seq'][$user_id_approval];

	$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($target_ids) and entry_form=78 group by mst_id","mst_id","approved_no");
 

	
 	if($approval_type==0)
	{  
 		
		$id=return_next_id( "id","approval_mst", 1 ) ;
		$ahid=return_next_id( "id","approval_history", 1 ) ;	
		
		
        foreach($target_app_id_arr as $key => $mst_id)
        {		
			$approved=(max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no)?1:3;
			
			$approved_no = $max_approved_no_arr[$mst_id]*1;
			if($last_app_status_arr[$mst_id] == 0 || $last_app_status_arr[$mst_id] == 2){
				$approved_no = $max_approved_no_arr[$mst_id]+1;
			}
			

			if($data_array!=''){$data_array.=",";}
			$data_array.="(".$id.",78,'".$mst_id."',".$user_sequence_no.",".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."',".$approved.")"; 
			$id=$id+1;
			
			
			if($history_data_array!="") $history_data_array.=",";
			$history_data_array.="(".$ahid.",78,".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,".$approved.",'".$appv_cause_arr[$key]."')";
			$ahid++;
			
			//mst data.......................
			
			$data_array_up[$mst_id] = explode(",",("".$approved.",".$user_sequence_no.",".$user_id_approval.",'".$pc_date_time."'")); 
        }
	 
 

        $flag=1;
		if($flag==1) 
		{
			$field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip,approved";
			//echo "10**insert into approval_mst($field_array) values $data_array";die;
			$rID1=sql_insert("approval_mst",$field_array,$data_array,0);
			if($rID1) $flag=1; else $flag=0; 
		}
		
		if($flag==1) 
		{
			$field_array_up="IS_APPROVED*APPROVED_SEQU_BY*APPROVED_BY*APPROVED_DATE"; 
			$rID2=execute_query(bulk_update_sql_statement( "wo_labtest_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr ));
			if($rID2) $flag=1; else $flag=0; 
		}

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=78 and mst_id in ($target_ids)";
			$rID3=execute_query($query,1);
			if($rID3) $flag=1; else $flag=0;
		}
		 
		if($flag==1)
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING,approved,COMMENTS";
			$rID4=sql_insert("approval_history",$field_array,$history_data_array,0);
			if($rID4) $flag=1; else $flag=0;
		}
		
		//echo "24444**".$rID1.",".$rID2.",".$rID3.",".$rID4;oci_rollback($con);die;
		
		if($flag==1) $msg='19'; else $msg='21';

        
	}
	else
	{              
		
		$ahid=return_next_id( "id","approval_history", 1 ) ;	
        foreach($target_app_id_arr as $key => $mst_id)
        {		
			$approved_no = $max_approved_no_arr[$mst_id]*1;
			if($last_app_status_arr[$mst_id] == 0 || $last_app_status_arr[$mst_id] == 2){
				$approved_no = $max_approved_no_arr[$mst_id]+1;
			}
			
			if($history_data_array!="") $history_data_array.=",";
			$history_data_array.="(".$ahid.",78,".$mst_id.",".$approved_no.",'".$user_sequence_no."',0,".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,'".$appv_cause_arr[$key]."')";
			$ahid++;
        }
		
		
		$flag=1;
		if($flag==1) 
		{
			$rID1=sql_multirow_update("wo_labtest_mst","IS_APPROVED*READY_TO_APPROVED*APPROVED_SEQU_BY",'0*0*0',"id",$target_ids,0);
			if($rID1) $flag=1; else $flag=0;
		}
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=78 and mst_id in ($target_ids)";
			$rID2=execute_query($query,1); 
			if($rID2) $flag=1; else $flag=0; 
		}
		
		if($flag==1)
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING,approved,COMMENTS";
			$rID3=sql_insert("approval_history",$field_array,$history_data_array,0);
			if($rID3) $flag=1; else $flag=0;
		}
 		
		 //echo "5**".$rID1.",".$rID2.",".$rID3.",".$rID4.$flag;oci_rollback($con);die;
		
		$response=$target_ids;
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


if($action=="show_trim_comment_report")
{
	echo load_html_head_contents("Approval Cause Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	//$last_update=return_field_value("is_apply_last_update","wo_booking_mst","booking_no='".trim($data)."'");
	//echo $last_update;
	
	$sql_lib_item_group_array=array();
	$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
	foreach($sql_lib_item_group as $row_sql_lib_item_group)
	{
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
	}
	
	
	$sql="select job_no,po_break_down_id,pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls where booking_no='$booking_no' and status_active=1 and is_deleted=0 group by job_no,po_break_down_id,pre_cost_fabric_cost_dtls_id,trim_group";
	$exchange_rate=return_field_value("exchange_rate", " wo_booking_mst", "booking_no='".$booking_no."'");
	
?>
<body>
<div>
<table width="990"   cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
 <thead>
   <tr align="center">
    <th colspan="13"><b>Trim Comments</b></th>
    </tr>
    
    <tr>
    <th width="30" rowspan="2">Sl</th>
     <th width="100" rowspan="2">Item Name</th>
    <th width="120" rowspan="2">Po NO</th>
    <th width="70" rowspan="2">Ship Date</th>  
    <th width="80" rowspan="2">As Merketing</th>
    <th width="70" rowspan="2">As Budget</th>
    <th width="70" rowspan="2">Mn.Book Val</th>
    <th width="70" rowspan="2">Sht.Book Val</th>
    <th width="70" rowspan="2">Smp.Book Val</th>
    <th  width="70" rowspan="2">Tot.Book Val</th>
    <th colspan="2">Balance</th>
    <th width="" rowspan="2">Comments On Budget</th>
    </tr>
    <tr>
    <th width="70">As Mkt.</th>
    <th width="70">As Budget</th>
    </tr>
     </thead>
</table>
<?

	 $po_qty_arr=array(); $pre_cost_data_arr=array();$pre_cu_data_arr=array();$trim_qty_data_arr=array();$trim_sam_qty_data_arr=array();$trim_price_cost_arr=array();	
	 $fab_sql=sql_select("select  a.po_break_down_id  as po_id,a.trim_group,
	sum(case a.is_short when 2 then a.amount else 0 end) as main_amount,
	sum(case a.is_short when 1 then a.amount else 0 end) as short_amount
	from    wo_booking_dtls a, wo_trim_book_con_dtls b   where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no 
   and a.booking_type=2
   and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0 group by a.po_break_down_id,a.trim_group  ");
		foreach($fab_sql as $row_data)
		{
		$trim_qty_data_arr[$row_data[csf('po_id')]][$row_data[csf('trim_group')]]['main_amount']=$row_data[csf('main_amount')];
		$trim_qty_data_arr[$row_data[csf('po_id')]][$row_data[csf('trim_group')]]['short_amount']=$row_data[csf('short_amount')];
		}  //var_dump($trim_qty_data_arr);
	 $sam_sql=sql_select("select d.po_break_down_id  as po_id,d.trim_group,
	sum(case c.is_short when 2 then d.amount else 0 end) as sam_with_amount,
	sum(case c.is_short when 1 then d.amount else 0 end) as sam_without_amount
	from   wo_booking_mst c,wo_booking_dtls d where c.booking_no=d.booking_no and c.booking_type=5  and c.booking_no='$booking_no' and   c.company_id='$company'  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by d.po_break_down_id ,d.trim_group ");
		foreach($sam_sql as $row_data)
		{
		$trim_sam_qty_data_arr[$row_data[csf('po_id')]][$row_data[csf('trim_group')]]['sam_with']=$row_data[csf('sam_with_amount')];
		$trim_sam_qty_data_arr[$row_data[csf('po_id')]][$row_data[csf('trim_group')]]['sam_without']=$row_data[csf('sam_without_amount')];
		} 
	 
	 $sql_po_qty=sql_select("select b.id as po_id,b.pub_shipment_date,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id   and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty,b.pub_shipment_date");
		foreach( $sql_po_qty as $row)
		{
			$po_qty_arr[$row[csf("po_id")]]['order_quantity']=$row[csf("order_quantity_set")];
			$po_qty_arr[$row[csf("po_id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
		}
		
		$sql_cons_data=sql_select("select a.id as pre_cost_fabric_cost_dtls_id,b.po_break_down_id as po_id,a.rate,b.cons from wo_pre_cost_trim_cost_dtls a , wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id   and a.is_deleted=0  and a.status_active=1");
						 
		foreach($sql_cons_data as $row)
		{
			$pre_cost_data_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("po_id")]]['cons']=$row[csf("cons")];
			$pre_cost_data_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("po_id")]]['rate']=$row[csf("rate")];
		}
			
			$sql_cu_woq=sql_select("select sum(amount) as amount,po_break_down_id as po_id,pre_cost_fabric_cost_dtls_id  from wo_booking_dtls where  booking_type=2 and status_active=1 and is_deleted=0");
			
		foreach($sql_cu_woq as $row)
		{
			$pre_cu_data_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("po_id")]]['amount']=$row[csf("amount")];
			
		}	
		
		$sql_price_trim=sql_select("select quotation_id,trim_group,sum(amount) as amount  from wo_pri_quo_trim_cost_dtls where   status_active=1 and is_deleted=0 group by quotation_id,trim_group");
		
		foreach($sql_price_trim as $row)
		{
			$trim_price_cost_arr[$row[csf("quotation_id")]][$row[csf("trim_group")]]['amount']=$row[csf("amount")];
			
		}				
	//$item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no ='$job_no'", "gmts_item_id", "set_item_ratio");
	$total_pre_cost=0;
	$total_booking_qnty_main=0;
	$total_booking_qnty_short=0;
	$total_booking_qnty_sample=0;
	$total_tot_bok_qty=0;
	$tot_balance=0;
					

?>
<div style="width:1010px; max-height:400px; overflow-y:scroll" id="scroll_body">
<table width="990"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
<?
$k=0;
$total_amount=0;$total_booking_qnty_main=0;$total_booking_qnty_short=0;$pre_cost=0;$total_booking_qnty_sample=0;$total_booking_qnty_sample=0;$total_tot_bok_qty=0
;$tot_mkt_balance=0;$tot_pre_cost=0;
$nameArray=sql_select( $sql );
foreach ($nameArray as $selectResult)
	{
		 if ($k%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
			
		 $quotation_id = return_field_value(" quotation_id as quotation_id"," wo_po_details_master ","job_no='".$selectResult[csf('job_no')]."'","quotation_id");  
		$tot_mkt_cost  = $trim_price_cost_arr[$quotation_id][$selectResult[csf("trim_group")]]['amount'];
		//return_field_value(" sum(b.fabric_cost) as mkt_cost","wo_price_quotation a,wo_price_quotation_costing_mst b"," a.id=b.quotation_id and a.id='".$quotation_id."'","mkt_cost");
	// $tot_mkt_cost;
			 $costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$selectResult[csf('job_no')]."'");
			if($costing_per==1)
			{
				$costing_per_qty=12;
			}
			else if($costing_per==2)
			{
				$costing_per_qty=1;
			}
			else if($costing_per==3)
			{
				$costing_per_qty=24;
			}
			else if($costing_per==4)
			{
				$costing_per_qty=36;
			}
			else if($costing_per==5)
			{
				$costing_per_qty=48;
			} 
			//$selectResult[csf('trim_group')]
			$main_fab_cost=$trim_qty_data_arr[$selectResult[csf('po_break_down_id')]][$selectResult[csf('trim_group')]]['main_amount'];
			$short_fab_cost=$trim_qty_data_arr[$selectResult[csf('po_break_down_id')]][$selectResult[csf('trim_group')]]['short_amount'];
			$sam_trim_with=$trim_sam_qty_data_arr[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['sam_with'];
			$sam_trim_without=$trim_sam_qty_data_arr[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['sam_without'];
			$po_qty=$po_qty_arr[$selectResult[csf('po_break_down_id')]]['order_quantity'];
			$po_ship_date=$po_qty_arr[$selectResult[csf('po_break_down_id')]]['pub_shipment_date'];
			$pre_rate=$pre_cost_data_arr[$selectResult[csf("pre_cost_fabric_cost_dtls_id")]][$selectResult[csf("po_break_down_id")]]['rate'];
			$pre_cons=$pre_cost_data_arr[$selectResult[csf("pre_cost_fabric_cost_dtls_id")]][$selectResult[csf("po_break_down_id")]]['cons'];
			$pre_req_qnty=def_number_format(($pre_cons*($po_qty/$costing_per_qty))/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],5,"");
			$pre_amount=$pre_req_qnty*$pre_rate;
			 $tot_grey_req_as_price_cost=($tot_mkt_cost/$costing_per_qty)*$po_qty;
			 
	$k++;
	
	?>
<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $k; ?>">
    <td width="30"> <? echo $k; ?> </td> 
    <td width="100"><p><? echo 	$trim_group[$selectResult[csf('trim_group')]];?></p>  </td>
    <td width="120"><p><? echo $po_number_arr[$selectResult[csf('po_break_down_id')]];?></p>  </td>
    <td width="70" align="right"><? echo change_date_format($po_ship_date,"dd-mm-yyyy",'-'); ?> </td>
    <td width="80" align="right"><? $total_price_mkt_cost+=$tot_grey_req_as_price_cost; echo number_format($tot_grey_req_as_price_cost,2);?> </td>
    <td width="70" align="right"><?  echo number_format($pre_amount,2); $pre_cost+=$pre_amount;?> </td>
    <td width="70" align="right"><? echo number_format($main_fab_cost,2); $total_booking_qnty_main+=$main_fab_cost;?> </td>
    <td width="70" align="right"> <? echo number_format($short_fab_cost,2); $total_booking_qnty_short+=$short_fab_cost;?></td>
    <td width="70" align="right"><? echo number_format($sam_trim_with,2); $total_booking_qnty_sample+=$sam_trim_with;?></td>
    <td width="70" align="right">	<? $tot_bok_qty=$main_fab_qty+$short_fab_qty+$total_booking_qnty_sample; echo number_format($tot_bok_qty,2); $total_tot_bok_qty+=$tot_bok_qty;?> </td>
    <td width="70" align="right"> <? $balance_mkt= def_number_format($tot_grey_req_as_price_cost-$tot_bok_qty,2,""); echo number_format($balance_mkt,2); $tot_mkt_balance+= $balance_mkt; ?></td>
    <td width="70" align="right"> <? $total_pre_cost=$pre_amount-$tot_bok_qty;$tot_pre_cost+=$total_pre_cost; echo number_format($total_pre_cost,2);?></td>
    <td width="">
     <? 
	if( $total_pre_cost>0)
		{
		echo "Less Booking";
		}
	else if ($total_pre_cost<0) 
		{
		echo "Over Booking";
		} 
	else if ($pre_amount==$tot_bok_qty) 
		{
			echo "As Per";
		} 
	else
		{
		echo "";
		}
	?></td>
</tr>
<?
	}
?>
<tfoot>
    <tr>
        <td colspan="4">Total:</td>
        <td align="right"><? echo number_format($total_price_mkt_cost,2); ?></td>
        <td align="right"><? echo number_format($pre_cost,2); ?></td>
        <td align="right"><? echo number_format($total_booking_qnty_main,2); ?></td>
        <td align="right"><? echo number_format($total_booking_qnty_short,2); ?></td>
        <td align="right"><? echo number_format($total_booking_qnty_sample,2); ?></td>
        <td align="right"><? echo number_format($total_tot_bok_qty,2); ?></td>
        <td align="right"><? echo number_format($tot_mkt_balance,2); ?></td>
        <td align="right"><? echo number_format($tot_pre_cost,2); ?></td>
    </tr>
    </tfoot>
</table>
</div>
</div>
 <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</body>
<?	
	
	exit();	

	
}
if ($action=="appcause_popup")
{
	echo load_html_head_contents("Approval Cause Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$data_all=explode('_',$data);
	$wo_id=$data_all[0];
	$app_type=$data_all[1];
	$app_cause=$data_all[2];
	$approval_id=$data_all[3];
	
	if($app_cause=="")
	{	
		$sql_cause="select MAX(id) as id from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=8 and user_id='$user_id' and booking_id='$wo_id' and approval_type='$app_type' and status_active=1 and is_deleted=0";				
		$nameArray_cause=sql_select($sql_cause);
		foreach($nameArray_cause as $row)
		{
			$app_cause=return_field_value("approval_cause", "fabric_booking_approval_cause", "id='".$row[csf("id")]."' and status_active=1 and is_deleted=0");
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
				
				var data="action=save_update_delete_appv_cause&operation="+operation+get_submitted_data_string('appv_cause*wo_id*appv_type*page_id*user_id*approval_id',"../../");
				//alert (data);return;
				freeze_window(operation);
				http.open("POST","lab_test_approval_controller_v2.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange=fnc_appv_entry_Reply_info;
			}
		}
		
		function fnc_appv_entry_Reply_info()
		{
			if(http.readyState == 4) 
			{
				//release_freezing();	
				//alert(http.responseText);return;
			
				var reponse=trim(http.responseText).split('**');	
				show_msg(reponse[0]);
				
				set_button_status(1, permission, 'fnc_appv_entry',1);
				release_freezing();
				
				generate_worder_mail(reponse[2],reponse[3],reponse[4],reponse[5]);
			}
		}
		
		function fnc_close()
		{	
			appv_cause= $("#appv_cause").val();
			document.getElementById('hidden_appv_cause').value=appv_cause;
			parent.emailwindow.hide();
		}
		
		function generate_worder_mail(woid,mail,appvtype,user)
		{
			var data="action=app_cause_mail&woid="+woid+'&mail='+mail+'&appvtype='+appvtype+'&user='+user;
			//alert (data);return;
			freeze_window(6);
			http.open("POST","lab_test_approval_controller_v2.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=fnc_appv_mail_Reply_info;
		}
		
		function fnc_appv_mail_Reply_info()
		{
			if(http.readyState == 4) 
			{
				var response=trim(http.responseText).split('**');
				release_freezing();
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
                    	<textarea name="appv_cause" id="appv_cause" class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character"></textarea>
                        <Input type="hidden" name="wo_id" class="text_boxes" ID="wo_id" value="<? echo $wo_id; ?>" style="width:30px" />
                        <Input type="hidden" name="appv_type" class="text_boxes" ID="appv_type" value="<? echo $app_type; ?>" style="width:30px" />
                        <Input type="hidden" name="page_id" class="text_boxes" ID="page_id" value="<? echo $menu_id; ?>" style="width:30px" />
                        <Input type="hidden" name="user_id" class="text_boxes" ID="user_id" value="<? echo $user_id; ?>" style="width:30px" />
                        <Input type="hidden" name="approval_id" class="text_boxes" ID="approval_id" value="<? echo $approval_id; ?>" style="width:30px" />
                    </td>
                </tr>
            </table> 
              
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >
                
                <tr>
                    <td align="center" class="button_container">
                        <? 
						//print_r ($id_up_all);
                            if($id_up!='')
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 1,0,"reset_form('size_1','','','','','');",1);
                            }
                            else
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 0,0,"reset_form('size_1','','','','','');",1);
                            }
                        ?>
                        <input type="hidden" name="hidden_appv_cause" id="hidden_appv_cause" class="text_boxes /">
                        
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
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
if ($action=="unappcause_popup")
{
	echo load_html_head_contents("Un Approval Request", "../../", 1, 1,'','','');
	extract($_REQUEST);
	$wo_id=$data;
	$sql_req="select approval_cause from fabric_booking_approval_cause where entry_form=8 and booking_id='$wo_id' and approval_type=2 and status_active=1 and is_deleted=0";				
	$nameArray_req=sql_select($sql_req);
	foreach($nameArray_req as $row)
	{
		$unappv_req=$row[csf('approval_cause')];
	}
	?>
    <script>
	
		//var permission='<?// echo $permission; ?>';
		
		$( document ).ready(function() {
			document.getElementById("unappv_req").value='<? echo $unappv_req; ?>';
		});
		
		
		function fnc_close()
		{	
			unappv_request= $("#unappv_req").val();
			document.getElementById('hidden_unappv_request').value=unappv_request;
			parent.emailwindow.hide();
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
                    	<textarea name="unappv_req" id="unappv_req" readonly class="text_area" style="width:430px; height:100px;"></textarea>
                    </td>
                </tr>
            </table> 
              
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >
                <tr>
                    <td align="center">
                    	<input type="hidden" name="hidden_unappv_request" id="hidden_unappv_request" class="text_boxes /">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
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
if ($action=="appinstra_popup")
{
	echo load_html_head_contents("Approval Cause Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$data_all=explode('_',$data);
	$wo_id=$data_all[0];
	$app_type=$data_all[1];
	$app_cause=$data_all[2];
	$approval_id=$data_all[3];
	?>
    <script>
	
		$( document ).ready(function() {
			document.getElementById("appv_cause").value='<? echo $app_cause; ?>';
		});
		
		var permission='<? echo $permission; ?>';
		
		function fnc_close()
		{	
			appv_cause= $("#appv_cause").val();
			
			document.getElementById('hidden_appv_cause').value=appv_cause;
			
			parent.emailwindow.hide();
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
                    	<textarea name="appv_cause" id="appv_cause" class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character"></textarea>
                        <Input type="hidden" name="wo_id" class="text_boxes" ID="wo_id" value="<? echo $wo_id; ?>" style="width:30px" />
                        <Input type="hidden" name="appv_type" class="text_boxes" ID="appv_type" value="<? echo $app_type; ?>" style="width:30px" />
                        <Input type="hidden" name="page_id" class="text_boxes" ID="page_id" value="<? echo $menu_id; ?>" style="width:30px" />
                        <Input type="hidden" name="user_id" class="text_boxes" ID="user_id" value="<? echo $user_id; ?>" style="width:30px" />
                        <Input type="hidden" name="approval_id" class="text_boxes" ID="approval_id" value="<? echo $approval_id; ?>" style="width:30px" />
                    </td>
                </tr>
            </table> 
              
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >
                
                <tr>
                    <td align="center" class="button_container">
                        <? 
						//print_r ($id_up_all);
                            /*if($id_up!='')
                            {
                                echo load_submit_buttons($permission, "fnc_appv_instru_entry", 1,0,"reset_form('size_1','','','','','');",1);
                            }
                            else
                            {
                                echo load_submit_buttons($permission, "fnc_appv_instru_entry", 0,0,"reset_form('size_1','','','','','');",1);
                            }*/
                        ?>
                        <input type="hidden" name="hidden_appv_cause" id="hidden_appv_cause" class="text_boxes"/>
                        
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
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

if ($action=="save_update_delete_appv_cause")
{
	
	//$approval_id
	$approval_type=str_replace("'","",$appv_type);
	
	
	if($approval_type==0)
	{
			
   		$process = array( &$_POST );
		extract(check_magic_quote_gpc( $process )); 

		if ($operation==0)  // Insert Here
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			
			$approved_no_history=return_field_value("approved_no","approval_history","entry_form=8 and mst_id=$wo_id and approved_by=$user_id");
			$approved_no_cause=return_field_value("approval_no","fabric_booking_approval_cause","entry_form=8 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");
			
			//echo "shajjad_".$approved_no_history.'_'.$approved_no_cause; die;
			
			if($approved_no_history=="" && $approved_no_cause=="")
			{
				//echo "insert"; die;
				
				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;
			
				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id_mst.",".$page_id.",8,".$user_id.",".$wo_id." ,".$appv_type.",0,".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
				//echo $rID; die;
				
				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT");
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
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
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
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
				
				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=8 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");
				
				$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
				$data_array="".$page_id."*8*".$user_id."*".$wo_id."*".$appv_type."*0*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
				
				 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);
				
				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT"); 
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id); 
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
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id); 
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
				$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=8 and mst_id=$wo_id and approved_by=$user_id");
				$max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=8 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");
				
				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;
			
					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id_mst.",".$page_id.",8,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
					//echo $rID; die;
					
					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT");
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
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
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
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
					
					$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=8 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");
					
					$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
					$data_array="".$page_id."*8*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
					
					 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);
					
					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT"); 
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id); 
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
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id); 
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
	
		if ($operation==1)  // Update Here
		{	
			
		}
	
	}//type=0
	if($approval_type==1)
	{
		$process = array( &$_POST );
		extract(check_magic_quote_gpc( $process )); 

		if ($operation==0)  // Insert Here
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
		
			$unapproved_cause_id=return_field_value("id","fabric_booking_approval_cause","entry_form=8 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");
			
			$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=8 and mst_id=$wo_id and approved_by=$user_id");
			
			if($unapproved_cause_id=="")
			{
			
				//echo "10**"."=shajjad_".$unapproved_cause_id; die;
		
				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;
			
				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_history_id,approval_cause,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id_mst.",".$page_id.",8,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$approval_id.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				
				$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
				//echo $rID; die;
		
				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT");
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
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
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
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
			else
			{
				
				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=8 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");
				
				$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_history_id*approval_cause*updated_by*update_date*status_active*is_deleted";
				$data_array="".$page_id."*8*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$approval_id."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
				
				 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);
				
				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT"); 
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id); 
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
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id); 
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
		
	}//type=1

	
}
if ( $action=="app_cause_mail" )
{
	//echo $woid.'_'.$mail.'_'.$appvtype; die;
	ob_start();
	?>
    
        <table width="800" cellpadding="0" cellspacing="0" border="1">
            <tr>
                <td valign="top" align="center"><strong><font size="+2">Subject : Fabric Booking &nbsp;<?  if($appvtype==0) echo "Approval Request"; else echo "Un-Approval Request"; ?>&nbsp;Refused</font></strong></td>
            </tr>
            <tr>
                <td valign="top">
                    Dear Mr. <?   
								$to="";
								
								$sql ="SELECT c.team_member_name FROM wo_booking_mst a,wo_po_details_master b,lib_mkt_team_member_info c where b.job_no=a.job_no and b.dealing_marchant=c.id and a.id=$woid";
								$result=sql_select($sql);
								foreach($result as $row)
								{
									if ($to=="")  $to=$row[csf('team_member_name')]; else $to=$to.", ".$row[csf('team_member_name')]; 
								}
								echo $to;  
							?>
                            <br> Your Fabric Booking No. &nbsp;
							<?
								$sql1 ="SELECT booking_no,buyer_id FROM wo_booking_mst where id=$woid";
								$result1=sql_select($sql1);
								foreach($result1 as $row1)
								{
									$wo_no=$row1[csf('booking_no')]; 
									$buyer=$row1[csf('buyer_id')]; 
								}
								
								
							?>&nbsp;<?  echo $wo_no;  ?>,&nbsp; <? echo $buyer_arr[$buyer]; ?>&nbsp;of buyer has been refused due to following reason. 
                </td>
            </tr>
            <tr>
                <td valign="top">
                    <?  echo $mail; ?>
                </td>
            </tr>
            <tr>
                <td valign="top">
                    Thanks,<br>
					<?
						$user_name=return_field_value("user_name","user_passwd","id=$user_id"); 
						echo $user_name;  
					?>
                </td>
            </tr>
        </table>
    <?
	
	$to="";
	
	$sql2 ="SELECT c.team_member_email FROM wo_booking_mst a,wo_po_details_master b,lib_mkt_team_member_info c where b.job_no=a.job_no and b.dealing_marchant=c.id and a.id=$woid";
		
		$result2=sql_select($sql2);
		foreach($result2 as $row2)
		{
			if ($to=="")  $to=$row2[csf('team_member_email')]; else $to=$to.", ".$row2[csf('team_member_email')]; 
		}
		
 		$subject="Approval Status";
    	$message="";
    	$message=ob_get_contents();
    	ob_clean();
		
		//echo $message;
		 //$to='akter.babu@gmail.com,saeed@fakirapparels.com,akter.hossain@fakirapparels.com,bdsaeedkhan@gmail.com,shajjadhossain81@gmail.com';
		//$to='shajjad@logicsoftbd.com';
		//$to='shajjadhossain81@gmail.com';
		$header=mail_header();
		
		echo send_mail_mailer( $to, $subject, $message, $header );
		
		/*if (mail($to,$subject,$message,$header))
			echo "****Mail Sent.---".date("Y-m-d");
		else
			echo "****Mail Not Sent.---".date("Y-m-d");*/
		
		//echo "222**".$woid;
		exit();
		
}
?>