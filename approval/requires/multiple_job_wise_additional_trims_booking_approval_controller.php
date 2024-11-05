<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];
$user_ip=$_SESSION['logic_erp']['user_ip'];

include('../../includes/common.php');
//echo $action;die;

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
	exit();
	
}

function getSequence($parameterArr=array())
{
	$lib_buyer_id_string=implode(',',(array_keys($parameterArr['lib_buyer_arr']))); 
	//$lib_department_id_string=implode(',',(array_keys($parameterArr[lib_department_id_arr]))); 
	//User data.....................
	// $sql_user="SELECT ID,BUYER_ID,BRAND_ID,ITEM_CATE_ID,IS_DATA_LEVEL_SECURED,store_location_id as STORE_ID FROM USER_PASSWD WHERE VALID=1";
	// $sql_user_result=sql_select($sql_user);
	// $userDataArr=array();
	// foreach($sql_user_result as $rows){
	// 	$userDataArr[$rows[ID]]=$rows;
	// }
	
	 
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND entry_form = {$parameterArr['entry_form']} AND PAGE_ID = {$parameterArr['page_id']} AND IS_DELETED = 0 order by SEQUENCE_NO";
	 //echo $sql;die;
	$sql_result=sql_select($sql);
	$dataArr=array();
	foreach($sql_result as $rows){
		//$rows[BUYER_ID]=$userDataArr[$rows[USER_ID]][BUYER_ID];
		//if($rows[DEPARTMENT]==''){$rows[DEPARTMENT]=$lib_department_id_string;}
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
	//User data.....................
	$sql_user="SELECT ID,BUYER_ID,BRAND_ID,ITEM_CATE_ID as ITEM_ID,store_location_id as STORE_ID,IS_DATA_LEVEL_SECURED FROM USER_PASSWD WHERE VALID=1";
	$sql_user_result=sql_select($sql_user);
	$userDataArr=array();
	foreach($sql_user_result as $rows){
		$userDataArr[$rows['ID']]['BUYER_ID']=$rows['BUYER_ID'];
		//$userDataArr[$rows[ID]][BRAND_ID]=$rows[BRAND_ID];
	}
	
	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND entry_form = {$parameterArr['entry_form']} AND PAGE_ID = {$parameterArr['page_id']} AND IS_DELETED = 0  order by SEQUENCE_NO";
	   //echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){
		if($userDataArr[$rows['USER_ID']]['BUYER_ID']==''){
			$userDataArr[$rows['USER_ID']]['BUYER_ID']=$lib_buyer_arr;
		}
		
		$usersDataArr[$rows['USER_ID']]['BUYER_ID']=explode(',',$userDataArr[$rows['USER_ID']]['BUYER_ID']);
		$userSeqDataArr[$rows['USER_ID']]=$rows['SEQUENCE_NO'];	
	}

	$finalSeq=array();
	foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){
		
		foreach($userSeqDataArr as $user_id=>$seq){
			if (in_array($bbtsRows['BUYER'],$usersDataArr[$user_id]['BUYER_ID']) && $bbtsRows['BUYER']>0) {
				$finalSeq[$sys_id][$user_id]=$seq;
			}
		}
	}
	//echo '<pre>';
	 //print_r($userSeqDataArr);die;
	 //echo $finalSeq;die;
	//die;
	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr);
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
		$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$company_id  and a.id!=$user_id  and b.is_deleted=0  and b.page_id=$menu_id and b.entry_form=67 order by b.sequence_no";
		//echo $sql;
		$arr=array (2=>$custom_designation,3=>$department_arr);
		echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' );
		?>
	</form>
	<?
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));  
	$sequence_no='';
	$company_name=str_replace("'","",$cbo_company_name); 
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$booking_year=str_replace("'","",$cbo_booking_year);
    $cbo_get_upto=str_replace("'","",$cbo_get_upto);

    $approval_type = str_replace("'","",$cbo_approval_type);
	$alter_user_id = str_replace("'","",$txt_alter_user_id);
	$app_user_id=($alter_user_id!='') ? $alter_user_id : $user_id;

    $searchCon='';
    if($cbo_buyer_name > 0){$searchCon .=" and a.buyer_id=$cbo_buyer_name";}
    if($booking_year > 0) $searchCon .=" and to_char(a.insert_date,'YYYY')=$booking_year";
    if($txt_booking_no !="") $searchCon=" and a.booking_no like '%$txt_booking_no%'";

    $company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
    $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
    $buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	
	if(str_replace("'","",$txt_date)!="")
	{
		if($cbo_get_upto==1) $searchCon .=" and a.booking_date>$txt_date";
		else if($cbo_get_upto==2) $searchCon .=" and a.booking_date<=$txt_date";
		else if($cbo_get_upto==3) $searchCon .=" and a.booking_date=$txt_date";
	}

    $electronicDataArr=getSequence(array('company_id'=>$company_name,'entry_form'=>67,'page_id'=>$menu_id,'user_id'=>$app_user_id,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0,'lib_department_id_arr'=>0));

    if($approval_type==0) // Un-Approve
	{		
		//Match data..................................
		if($electronicDataArr['user_by'][$app_user_id]['BUYER_ID']) 
		{
			$where_con .= " and a.buyer_id in(".$electronicDataArr['user_by'][$app_user_id]['BUYER_ID'].",0)";
			$electronicDataArr['sequ_by'][0]['BUYER_ID']=$electronicDataArr['user_by'][$app_user_id]['BUYER_ID'];
		}
		
		$data_mas_sql = "select a.id as ID, a.BUYER_ID as BUYER_ID from wo_booking_mst a where a.status_active=1 and a.is_deleted=0 and a.is_approved<>1 and a.ready_to_approved=1 and a.booking_type=8 and a.entry_form=555 and a.company_id=$company_name $where_con $searchCon";
		//echo $data_mas_sql; 
				 
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
		//..........................................Match data;
	
		$sql='';		
		for($seq=0;$seq<=count($electronicDataArr['sequ_arr']); $seq++ )
		{			
 			$sys_con = where_con_using_array($tmp_sys_id_arr[$seq],0,'a.ID');
			if($tmp_sys_id_arr[$seq])
			{
				if($sql!=''){$sql .=" UNION ALL ";}
				$sql .= " select  a.id, a.entry_form, a.booking_no_prefix_num as prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.buyer_id, a.booking_type, a.is_short, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.entry_form, a.po_break_down_id, a.is_approved from wo_booking_mst a where a.status_active=1 and a.is_deleted=0 and a.is_approved<>1 and a.ready_to_approved=1 and a.booking_type=8 and a.entry_form=555 and a.approved_sequ_by=$seq $sys_con and a.company_id=$company_name $searchCon group by a.id, a.entry_form, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.buyer_id, a.booking_type, a.is_short, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.entry_form, a.po_break_down_id, a.is_approved";
			}		
		}		
	}
	else
	{		
		$sql = " select a.id, a.entry_form, a.booking_no_prefix_num as prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.buyer_id, a.booking_type, a.is_short, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.entry_form, a.po_break_down_id, a.is_approved from wo_booking_mst a, approval_mst c where a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and a.ready_to_approved=1 and a.booking_type=8 and a.entry_form=555 and a.company_id=$company_name and c.sequence_no={$electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO']} and a.approved_sequ_by=c.sequence_no $searchCon and c.entry_form=67 group by a.id, a.entry_form, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.buyer_id, a.booking_type, a.is_short, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.entry_form, a.po_break_down_id, a.is_approved";
    }
	//echo $sql;die;
		
	$mst_id_arr=array();
	$nameArray=sql_select( $sql );
	foreach ($nameArray as $row)
	{ 
		$mst_id_arr[$row[csf('id')]]=$row[csf('id')];
	}
  
	$hostory_sql=sql_select( "select mst_id as MST_ID, approved_by as APPROVED_BY, approved_date as APPROVED_DATE from approval_history where current_approval_status=1 and entry_form=67 and mst_id in (".implode(',',$mst_id_arr).")");
	foreach ($hostory_sql as $row)
	{ 
		$history_data['LAST_APP_DATE'][$row['MST_ID']]=$row['APPROVED_DATE'];
		$history_data['LAST_APP_BY'][$row['MST_ID']]=$row['APPROVED_BY'];
	}
   
	$user_arr=return_library_array( "select id, user_name from user_passwd", 'id', 'user_name' );

	$sql_cause="select booking_id, MAX(id) as id from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=67 and user_id={$_SESSION['logic_erp']['user_id']} and booking_id in (".implode(',',$mst_id_arr).") and approval_type=$cbo_approval_type and status_active=1 and is_deleted=0 group by booking_id";
	$nameArray_cause=sql_select($sql_cause);
	foreach($nameArray_cause as $row)
	{
		$app_cause_arr[$row[csf('booking_id')]]=return_field_value("approval_cause", "fabric_booking_approval_cause", "id='".$row[csf("id")]."' and status_active=1 and is_deleted=0");
	} 
	
	$sql_req="select booking_id, approval_cause from fabric_booking_approval_cause where entry_form=67 and booking_id in (".implode(',',$mst_id_arr).") and approval_type=2 and status_active=1 and is_deleted=0";			
	$nameArray_req=sql_select($sql_req);
	foreach($nameArray_req as $row)
	{
		$unappv_req_arr[$row[csf('booking_id')]]=$row[csf('approval_cause')];
	}
    //print_r($unappv_req_arr);
 
	?>
    <script>
	function openmypage_app_instrac(wo_id,app_type,i)
	{
		var txt_appv_instra = $("#txt_appv_instra_"+i).val();	
		var approval_id = $("#approval_id_"+i).val();
		
		var data=wo_id+"_"+app_type+"_"+txt_appv_instra+"_"+approval_id;
		
		var title = 'Approval Instruction';	
		var page_link = 'requires/multiple_job_wise_additional_trims_booking_approval_controller.php?data='+data+'&action=appinstra_popup';
		  
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
		var page_link = 'requires/multiple_job_wise_additional_trims_booking_approval_controller.php?data='+data+'&action=appcause_popup';
		  
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
		var page_link = 'requires/multiple_job_wise_additional_trims_booking_approval_controller.php?data='+data+'&action=unappcause_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=180px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var unappv_request=this.contentDoc.getElementById("hidden_unappv_request");
			
			$('#txt_unappv_req_'+i).val(unappv_request.value);
		}
	}
	</script>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:1075px; margin-top:10px">
        <legend>Multiple Job Wise Additional Trims Booking Approval</legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1075" class="rpt_table" >
                <thead>
                	<th width="50"></th>
                    <th width="40">SL</th>
                    <th width="80">MKT Cost</th>
                    <th width="130">Booking No</th>
                    <th width="80">Type</th>
                    <th width="100">Booking Date</th>
                    <th width="125">Buyer</th>
                    <th width="160">Supplier</th>
                    <th width="80">Delivery Date</th>
                    <?
					if($approval_type==0) echo "<th width='80'>Appv Instra</th>";
					if($approval_type==1) echo "<th width='80'>Un-appv request</th>"; 
					?>
                    <th><? if($approval_type==0) echo "Not Appv. Cause" ; else echo "Not Un-Appv. Cause"; ?></th>
                    
                </thead>
            </table>
            <div style="width:1077px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1059" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <? 
                            $i=1;
                            // echo $sql;
                           
                            foreach ($nameArray as $row)
                            {
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                if($row[csf('is_short')]==1) $booking_type="Short"; else $booking_type="Main";
								
								$unapprove_value_id=$row[csf('id')];
                                $bgcolor= ($i%2==0) ? "#E9F3FF" : "#FFFFFF";
                                $value=$row[csf('id')];                               
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" title="<?=$row[csf('entry_form')]?>">
                                	<td width="50" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i; ?>" />
                                        <input id="booking_id_<? echo $i;?>" name="booking_id[]" type="hidden" value="<? echo $row[csf('id')]; ?>" />
                                        <input id="booking_no_<? echo $i;?>" name="booking_no[]" type="hidden" value="<? echo $row[csf('booking_no')]; ?>" />
                                    </td>
									<td width="40" align="center"><? echo $i; ?></td>                                    
                                    <td width="80"><p><a href='##' style='color:#000' onClick="generate_comment_popup('<? echo $row[csf('booking_no')]; ?>',<? echo $row[csf('company_id')]; ?>,'show_trim_comment_report')">
									<? //echo $row[csf('booking_no')]; ?>View</a></p>
                                    </td>

									<td width="130"><p><a href='##' style='color:#000' onClick="generate_worder_report11('<? echo $row[csf('booking_no')]; ?>','<? echo $row[csf('company_id')]; ?>','<? echo $row[csf('is_approved')]; ?>','show_trim_booking_report',' <? echo $i; ?>')"><? echo $row[csf('booking_no')];?></p></td>

                                    <td width="80" align="center"><p><? echo $booking_type; ?></p></td>
									<td width="100" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                                    <td width="125"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
									<td width="160" style="word-break:break-all"><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></td>
									<td width="80" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
                                    <?
										if($approval_type==0) echo "<td align='center' width='80'><input name='txt_appv_instra[]' class='text_boxes' readonly placeholder='Please Browse' ID='txt_appv_instra_".$i."' style='width:70px' maxlength='50' onClick='openmypage_app_instrac(".$value.",".$approval_type.",".$i.")'></td>";
										if($approval_type==1) echo "<td align='center' width='80'><input name='txt_unappv_req[]' class='text_boxes' readonly placeholder='Please Browse' ID='txt_unappv_req_".$i."' value='".$unappv_req_arr[$row[csf('id')]]."' style='width:70px' maxlength='50' onClick='openmypage_unapp_request(".$value.",".$approval_type.",".$i.")'></td>"; 
                                    ?>
                                    <td align="center"><input name="txt_appv_cause[]" class="text_boxes" readonly placeholder="Please Browse" ID="txt_appv_cause_<? echo $i;?>" style="width:97px" value="<?=$app_cause_arr[$rows[csf('id')]];?>" maxlength="50" title="Maximum 50 Character" onClick="openmypage_app_cause(<? echo $value; ?>,<? echo $approval_type; ?>,<? echo $i; ?>,<?= $app_user_id; ?>)"></td>
								</tr>
								<?								 
								$i++;
							}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="1075" class="rpt_table">
				<tfoot>
                    <td width="50" align="center">
						<input type="checkbox" id="all_check" onClick="check_all('all_check')" />						
					</td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $i; ?>,<?= $approval_type; ?>)"/>

					<input type="button" value="Deny" class="formbutton" style="width:100px;<?= ($approval_type==1)?' display:none':''; ?>" onClick="submit_approved(<?=$i; ?>,5);"/>
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
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	$msg=''; $flag=''; $response='';

	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if($txt_alter_user_id!=""){$app_user_id=$txt_alter_user_id;}
	else{$app_user_id=$user_id;}
	$booking_ids=str_replace("'","",$booking_ids);
	$booking_nos=str_replace("'","",$booking_nos);
	$approval_type=str_replace("'","",$approval_type);
	$booking_ids_arr = explode(',',$booking_ids);
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

	$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=7 group by mst_id","mst_id","approved_no");

	$sql="select a.id as ID,a.IS_APPROVED, a.buyer_id as BUYER_ID from wo_booking_mst a where a.id in($booking_ids) and READY_TO_APPROVED=1";
	
	$sqlResult=sql_select( $sql );
	foreach ($sqlResult as $row)
	{
		$matchDataArr[$row['ID']]=array('BUYER'=>$row['BUYER_ID'],'brand'=>0,'item'=>0,'store'=>0,'department'=>0);
		$last_app_status_arr[$row['ID']] = $row['IS_APPROVED'];
	}
	
	//$matchDataArr[333]=array('buyer'=>0,'brand'=>0,'item'=>15,'store'=>358);
	$finalDataArr=getFinalUser(array('company_id'=>$company_name,'entry_form'=>67,'page_id'=>$menu_id,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0,'lib_department_id_arr'=>0,'match_data'=>$matchDataArr));
	
	$sequ_no_arr_by_sys_id =$finalDataArr['final_seq'];
	$user_sequence_no = $finalDataArr['user_seq'][$app_user_id];
	//---------------------

	//echo "10**$approval_type";die;
	if($approval_type==5)
	{

		$his_id=return_next_id( "id","approval_history", 1 ) ;
		foreach($booking_ids_arr as $mst_id)
		{
			$approved_no = $max_approved_no_arr[$mst_id]*1;
			if($i!=0) $data_array.=",";             
            $data_array.="(".$his_id.",67,".$mst_id.",".$approved_no.",'".$user_sequence_no."',0,".$app_user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,2)";
			$his_id++;

		}
		
		
		$rID1=sql_multirow_update("wo_booking_mst","IS_APPROVED*READY_TO_APPROVED*APPROVED_SEQU_BY",'2*0*0',"id",$booking_ids,0);
		if($rID1) $flag=1; else $flag=0;

		if($flag==1) 
        {
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date, is_signing,APPROVED";   
			$rID2=sql_insert("approval_history",$field_array,$data_array,0);
			if($rID2) $flag=1; else $flag=0;            
        }

				
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=67 and mst_id in ($booking_ids)";
			$rID3=execute_query($query,1); 
			if($rID3) $flag=1; else $flag=0; 
		}
		
		 //echo "191**".$rID1.",".$rID2.",".$rID3.",".$rID4;oci_rollback($con);die;
		
		$response=$booking_ids;
		if($flag==1) $msg='50'; else $msg='51';

	} 

	else if($approval_type==0)
	{      

		$approved_no_array=array();
		$id=return_next_id( "id","approval_mst", 1 ) ;
		$his_id=return_next_id( "id","approval_history", 1 ) ;
		foreach($booking_ids_arr as $mst_id)
		{
			$approved_no = $max_approved_no_arr[$mst_id]*1;
			$approved = (max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no)?1:3;
			
			if($mst_data_array!=''){$mst_data_array.=",";}
			$mst_data_array.="(".$id.",67,".$mst_id.",".$user_sequence_no.",".$app_user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."',".$approved.")";
			$id=$id+1;

			if($i!=0) $data_array.=",";             
            $data_array.="(".$his_id.",67,".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$app_user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,".$approved.")";
			$his_id++;
			
			//mst data.......................
			
			$mst_data_array_up[$mst_id] =explode(",",("".$approved.",".$user_sequence_no.",".$app_user_id.",'".$pc_date_time."'"));

			
			if($last_app_status_arr[$mst_id] == 0 || $last_app_status_arr[$mst_id] == 2){
				$approved_no = $approved_no+1;
				$approved_no_array[$mst_id] = $approved_no;
			}

		}
		
		 //print_r($data_array_up);die;
		
		
		$flag=1;

		if(count($approved_no_array)>0){
			$approved_string="";
			foreach($approved_no_array as $key=>$value)
			{
				$approved_string.=" WHEN $key THEN $value";
			}
			$approved_string_mst="CASE id ".$approved_string." END";
			$approved_string_dtls="CASE mst_id ".$approved_string." END";
		}

		if($flag==1) 
		{
			$mst_field_array="id, entry_form, mst_id, sequence_no, approved_by, approved_date, inserted_by, insert_date, user_ip,APPROVED";
			$rID=sql_insert("approval_mst",$mst_field_array,$mst_data_array,0);
			if($rID) $flag=1; else $flag=0; 
		}		
		
		if($flag==1) 
		{
			$mst_field_array_up="is_approved*approved_sequ_by*APPROVED_BY*APPROVED_DATE";
			$rID1=execute_query(bulk_update_sql_statement( "wo_booking_mst", "id", $mst_field_array_up, $mst_data_array_up, $booking_ids_arr ));
			if($rID1) $flag=1; else $flag=0;
		}
		
	    
        if($flag==1) 
        {
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=67 and mst_id in ($booking_ids)"; //die;       
			$rID2=execute_query($query,1);
            if($rID2) $flag=1; else $flag=0; 
        }
		
		//echo "insert into approval_history $field_array values($data_array)";die;
		
        if($flag==1) 
        {
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date, is_signing,APPROVED";   
			$rID3=sql_insert("approval_history",$field_array,$data_array,0);
			if($rID3) $flag=1; else $flag=0;            
        }
		
		if($flag==1) $msg='19'; else $msg='21';
		//...................................end History;		 
		 //echo '21**'.$rID.'**'.$rID1.'**'.$rID2.'**'.$rID3;oci_rollback($con);die;		 

	}
	else
	{    
		
		$his_id=return_next_id( "id","approval_history", 1 ) ;
		foreach($booking_ids_arr as $mst_id)
		{
			$approved_no = $max_approved_no_arr[$mst_id]*1;
			if($i!=0) $data_array.=",";             
            $data_array.="(".$his_id.",67,".$mst_id.",".$approved_no.",'".$user_sequence_no."',0,".$app_user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$his_id++;

		}
		
		
		$checkSql="select a.id,a.PI_NUMBER from com_pi_master_details a,com_pi_item_details b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.WORK_ORDER_ID in ($booking_ids)";

		//echo $checkSql;die();
		$checkSqlRes=sql_select($checkSql); 
		foreach($checkSqlRes as $rows)
		{
			 echo "25**Pi number Found in this Booking No.Unapprove Not Allowed";exit();
		}
		unset($checkSqlRes);

	 
		$flag=1;
		
		if($flag==1)
		{
			$rID=sql_multirow_update("wo_booking_mst","is_approved*ready_to_approved*approved_sequ_by","0*2*0","id",$booking_ids,0);
			if($rID) $flag=1; else $flag=0;
		}

		if($flag==1) 
        {
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date, is_signing,APPROVED";   
			$rID2=sql_insert("approval_history",$field_array,$data_array,0);
			if($rID2) $flag=1; else $flag=0;            
        }

		
		if($flag==1) 
		{
			$query="delete from approval_mst WHERE entry_form=67 and mst_id in ($booking_ids)";		
			$rID3=execute_query($query,1);
			if($rID3) $flag=1; else $flag=0; 
		}
		
 
		if($flag==1) $msg='20'; else $msg='22';
	}
	
	//echo "0**$rID**$rID2**$rID3**$rID4**$rID5";oci_rollback($con);die;
		
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
				http.open("POST","multiple_job_wise_additional_trims_booking_approval_controller.php",true);
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
				fnc_close();
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
			http.open("POST","multiple_job_wise_additional_trims_booking_approval_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=fnc_appv_mail_Reply_info;
			
		}
		
		function fnc_appv_mail_Reply_info()
		{
			if(http.readyState == 4) 
			{
				var response=trim(http.responseText).split('**');
				/*if(response[0]==222)
				{
					show_msg(reponse[0]);
				}*/
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
                            if($app_cause!='')
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

		$operation=0;
		if ($operation==0)  // Insert Here
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			
			$approved_no_history=return_field_value("approved_no","approval_history","entry_form=67 and mst_id=$wo_id and approved_by=$user_id");
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
				$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=67 and mst_id=$wo_id and approved_by=$user_id");
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
			
			$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=67 and mst_id=$wo_id and approved_by=$user_id");
			
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

?>