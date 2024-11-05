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

if ($action=="load_drop_down_buyer")
{
    if($data != 0)
    {
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); 
	exit();  
    }  
    else{
        echo create_drop_down( "cbo_buyer_name", 152, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); 
        exit(); 
    }	 
} 


$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );



function getSequence($parameterArr=array()){
	$lib_buyer_id_string=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));

	$brandSql = "select ID, BRAND_NAME,BUYER_ID from lib_buyer_brand where STATUS_ACTIVE=1 and IS_DELETED=0";
	$brandSqlRes=sql_select($brandSql);
	foreach($brandSqlRes as $row){
		$buyer_wise_brand_id_arr[$row['BUYER_ID']][$row['ID']] = $row['ID'];
	}
	
	//Electronic app setup data.....................
	$electronic_app_sql="SELECT COMPANY_ID,PAGE_ID,ENTRY_FORM,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT,GROUP_NO FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['entry_form']} AND IS_DELETED = 0 order by SEQUENCE_NO";
 	 //echo $electronic_app_sql;die;
	$electronic_app_sql_result=sql_select($electronic_app_sql);
	$dataArr=array();
	foreach($electronic_app_sql_result as $rows){

		if($rows['BUYER_ID']=='' || $rows['BUYER_ID'] == 0){$rows['BUYER_ID'] = $lib_buyer_id_string;}
		
		if($rows['BRAND_ID']=='' || $rows['BRAND_ID']==0){
			$tempBrandArr = array();
			foreach(explode(',',$rows['BUYER_ID']) as $buyer_id){
				if(count($buyer_wise_brand_id_arr[$buyer_id])){$tempBrandArr[]= implode(',',$buyer_wise_brand_id_arr[$buyer_id]);}
			}
			$rows['BRAND_ID']=implode(',',$tempBrandArr);
		}
	 
		$dataArr['user_by'][$rows['USER_ID']]=$rows;
		$dataArr['sequ_by'][$rows['SEQUENCE_NO']]=$rows;
		$dataArr['group_user_by'][$rows['GROUP_NO']][$rows['USER_ID']]=$rows;
		$dataArr['group_arr'][$rows['GROUP_NO']]=$rows['GROUP_NO'];
		$dataArr['group_seq_arr'][$rows['GROUP_NO']][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
		$dataArr['group_by_seq_arr'][$rows['SEQUENCE_NO']]=$rows['GROUP_NO'];

		$dataArr['bypass_seq_arr'][$rows['BYPASS']][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
		$dataArr['group_bypass_arr'][$rows['GROUP_NO']][$rows['BYPASS']]=$rows['BYPASS'];

		
		//$dataArr['bypass_by_group_arr'][$rows['GROUP_NO']][$rows['BYPASS']]=$rows['BYPASS'];

	}

	//print_r($buyer_wise_my_previous_group_arr);die;

	return $dataArr;
}


function getFinalUser($parameterArr=array()){
	$lib_buyer_arr=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));
	

	$brandSql = "select ID, BRAND_NAME,BUYER_ID from lib_buyer_brand where STATUS_ACTIVE=1 and IS_DELETED=0";
	$brandSqlRes=sql_select($brandSql);
	foreach($brandSqlRes as $row){
		$buyer_wise_brand_id_arr[$row['BUYER_ID']][$row['ID']] = $row['ID'];
	}
	
	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT,GROUP_NO FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['entry_form']} AND IS_DELETED = 0  order by SEQUENCE_NO";
	    //echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){
		$userDataArr[$rows['USER_ID']]['BUYER_ID']=$rows['BUYER_ID'];
		$userDataArr[$rows['USER_ID']]['BRAND_ID']=$rows['BRAND_ID'];
		$userDataArr[$rows['USER_ID']]['DEPARTMENT']=$rows['DEPARTMENT'];
		

		if($userDataArr[$rows['USER_ID']]['BUYER_ID']=='' || $userDataArr[$rows['USER_ID']]['BUYER_ID'] == 0){
			$userDataArr[$rows['USER_ID']]['BUYER_ID']=$lib_buyer_arr;
		}
		

		if($userDataArr[$rows['USER_ID']]['BRAND_ID']=='' || $userDataArr[$rows['USER_ID']]['BRAND_ID'] == 0){
			$tempBrandArr = array();
			foreach(explode(',',$userDataArr[$rows['USER_ID']]['BUYER_ID']) as $buyer_id){
				if(count($buyer_wise_brand_id_arr[$buyer_id])){$tempBrandArr[]= implode(',',$buyer_wise_brand_id_arr[$buyer_id]);}
			}
			$userDataArr[$rows['USER_ID']]['BRAND_ID']=implode(',',$tempBrandArr);
		}
		 

		// if($userDataArr[$rows['USER_ID']]['DEPARTMENT']==''){
		// 	$userDataArr[$rows['USER_ID']]['DEPARTMENT']=$product_dept_arr;
		// }
		
		
		$usersDataArr[$rows['USER_ID']]['BUYER_ID']=explode(',',$userDataArr[$rows['USER_ID']]['BUYER_ID']);
		$usersDataArr[$rows['USER_ID']]['BRAND_ID']=explode(',',$userDataArr[$rows['USER_ID']]['BRAND_ID']);
		$usersDataArr[$rows['USER_ID']]['DEPARTMENT']=explode(',',$userDataArr[$rows['USER_ID']]['DEPARTMENT']);
		
		$userSeqDataArr[$rows['USER_ID']]=$rows['SEQUENCE_NO'];
		$userGroupDataArr[$rows['USER_ID']]=$rows['GROUP_NO'];
		//$lastBypassNoDataArr[$rows['BYPASS']][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
		$groupBypassNoDataArr[$rows['GROUP_NO']][$rows['BYPASS']][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
	
	}

 
	
	$finalSeq=array();
	foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){
		
		foreach($userSeqDataArr as $user_id=>$seq){
			if(
				(in_array($bbtsRows['buyer'],$usersDataArr[$user_id]['BUYER_ID']) && $bbtsRows['buyer']>0)
			){
				$finalSeq[$sys_id][$user_id]=$seq;
			}
		 
		}
	}
	

	  //var_dump($buyer_wise_my_previous_group_arr);die;
	 //var_dump($usersDataArr[526]['DEPARTMENT']);die;
	
	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr,'user_group'=>$userGroupDataArr,'group_bypass_no_data_arr'=>$groupBypassNoDataArr);
}


if($action=="report_generate")
{
	// var_dump($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

    $company_id = str_replace("'","",$cbo_company_name);
    $cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
    $cbo_get_upto = str_replace("'","",$cbo_get_upto);
    $txt_date_from = str_replace("'","",$txt_date_from);
    $txt_date_to = str_replace("'","",$txt_date_to);
	$txt_mkt_no=str_replace("'","",$txt_mkt_no);
    $txt_team_member = str_replace("'","",$txt_team_member);
    $txt_price_quotation_id = str_replace("'","",$txt_price_quotation_id);
    $approval_type = str_replace("'","",$cbo_approval_type);
    $txt_alter_user_id = str_replace("'","",$txt_alter_user_id);

    $app_user_id = ($txt_alter_user_id !='') ? $txt_alter_user_id : $user_id;

	if ($txt_mkt_no=="") $mkt_no_cond=""; else $mkt_no_cond =" and a.MKT_NO='$txt_mkt_no'";
	
    if($cbo_buyer_name != 0){$where_con = "and a.buyer_id in ($cbo_buyer_name)";}
	
	if($txt_date_from!="" && $txt_date_to!="")
	{
		$where_con .= " and a.quot_date between '$txt_date_from' and '$txt_date_to'";

	}
	if($txt_team_member != ""){ $where_con .= "and a.team_member LIKE '%$txt_team_member%'";}
	if($txt_price_quotation_id != ""){ $where_con .= " and a.id = $txt_price_quotation_id";}

    //echo $where_con;die;

    $buyer_arr[0]=0;
	$electronicDataArr = getSequence(array('company_id'=>$company_id,'entry_form'=>10,'user_id'=>$app_user_id,'lib_buyer_arr'=>$buyer_arr,'product_dept_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0));
	 
	$my_seq = $electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO'];
	$my_group = $electronicDataArr['user_by'][$app_user_id]['GROUP_NO'];
	$my_group_seq_arr = $electronicDataArr['group_seq_arr'][$my_group];
	$electronicDataArr['group_seq_arr'][0] = [0] + $electronicDataArr['group_seq_arr'][1];

	$my_previous_bypass_no_seq = 0;
	rsort($electronicDataArr['bypass_seq_arr'][2]);
	foreach($electronicDataArr['bypass_seq_arr'][2] as $uid => $seq){
		if($seq<$my_seq){$my_previous_bypass_no_seq = $seq;break;}
	}
	if($my_seq == ''){echo "<u><h2 style='color:red;'>You have no approval permission.</h2></u>";die();}


    if($electronicDataArr['user_by'][$app_user_id]['BUYER_ID']){
		$where_con .= " and a.buyer_id in(".$electronicDataArr['user_by'][$app_user_id]['BUYER_ID'].",0)";
		$electronicDataArr['sequ_by'][0]['BUYER_ID']=$electronicDataArr['user_by'][$app_user_id]['BUYER_ID'];
	}
 
    if($approval_type == 0) // Un-Approve
	{

        //Match data..................................
        $data_mas_sql = "SELECT a.ID,a.BUYER_ID,a.APPROVED_BY,a.APPROVED_SEQU_BY,a.APPROVED_GROUP_BY  from WO_PRICE_QUOTATION a where a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1 and a.approved <> 1 $where_con";
		//echo $data_mas_sql;die;

		$tmp_sys_id_arr=array();$sys_data_arr=array();
		$data_mas_sql_res=sql_select( $data_mas_sql );
		foreach ($data_mas_sql_res as $row)
		{ 
			$group_stage_arr = array();
			for($group = ($row['APPROVED_GROUP_BY'] == $my_group && $electronicDataArr['sequ_by'][$my_seq]['BYPASS'] == 2)?$my_group:$my_group-1;$group >= 0; $group-- ){
				
				krsort($electronicDataArr['group_seq_arr'][$group]);
				foreach($electronicDataArr['group_seq_arr'][$group] as $seq){
					
					if($seq<$my_seq){
						if(
							(in_array($row['BUYER_ID'],explode(',',$electronicDataArr['sequ_by'][$seq]['BUYER_ID'])) || $row['BUYER_ID']==0) 
							&& ($row['APPROVED_GROUP_BY']<=$group)
							)
						{ 
						
							// print_r($electronicDataArr['group_seq_arr'][$my_group]);

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
        //..........................................Match data;

        $sql='';
		for($group=0;$group<=$my_group; $group++ ){
			foreach($electronicDataArr['group_seq_arr'][$group] as $seq){
		
				if(count($tmp_sys_id_arr[$group][$seq])){
					$sys_con = where_con_using_array($tmp_sys_id_arr[$group][$seq],0,'a.ID');
					if($sql!=''){$sql .=" UNION ALL ";}
                    $sql .= "SELECT a.ID, a.COMPANY_ID, b.MARGIN_DZN_PERCENT, b.COSTING_PER_ID, b.PRICE_WITH_COMMN_DZN, b.COMMISSION, b.TOTAL_COST, a.BUYER_ID, a.STYLE_REF, a.STYLE_DESC, a.QUOT_DATE, a.EST_SHIP_DATE, a.APPROVED, a.INSERTED_BY, a.GARMENTS_NATURE, a.MKT_NO from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id  and a.company_id=$company_id  and a.APPROVED_SEQU_BY = $seq and a.APPROVED_GROUP_BY=$group $sys_con and a.status_active=1 and a.is_deleted=0 and a.ready_to_approved=1  and a.approved <> 1 $where_con $mkt_no_cond";
				}
		
			}
		}

       // echo $sql;die;

    }
    else if($approval_type==1) // Approve
    {
			
		$where_con .= "and c.GROUP_NO={$electronicDataArr['user_by'][$app_user_id]['GROUP_NO']}";

		$sql = "select a.ID, a.COMPANY_ID, b.MARGIN_DZN_PERCENT, b.COSTING_PER_ID, b.PRICE_WITH_COMMN_DZN, b.COMMISSION, b.TOTAL_COST, a.BUYER_ID, a.STYLE_REF, a.STYLE_DESC, a.QUOT_DATE, a.EST_SHIP_DATE, a.APPROVED, a.INSERTED_BY, a.GARMENTS_NATURE, a.MKT_NO
		from wo_price_quotation a, wo_price_quotation_costing_mst b,APPROVAL_MST c 
		where a.id=b.quotation_id  and a.APPROVED_GROUP_BY=c.GROUP_NO and c.mst_id=a.id and  c.mst_id=b.quotation_id and a.company_id=$company_id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.READY_TO_APPROVED=1  and a.APPROVED_SEQU_BY=c.SEQUENCE_NO and c.ENTRY_FORM=10 and a.APPROVED<>0 $where_con $mkt_no_cond ";

    }

	//echo $sql;die;

	
		$tbl_width = ($approval_type==1) ? 1180 : 1000;
		?>
		<form name="requisitionApproval_2" id="requisitionApproval_2">
			<fieldset style="width:<?= $tbl_width+20;?>px; margin-top:10px">
			<legend>Price Quotation Approval</legend>
				<table align="left" cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $tbl_width;?>" class="rpt_table" >
					<thead>
						<th width="30"></th>
						<th width="40">SL</th>
						<th width="70">Quotation No</th>
						<th width="60">Mkt_no</th>
						<th width="100">Buyer</th>
						<th width="100">Style Ref.</th>
						<th width="80">Quotation Date</th>
						<th width="80">Est. Ship Date</th>
						<th width="50">Image</th>
						<th width="80">Margin%</th>
						<th width="80">Approved Date</th>
						<? if($approval_type==1){?>
							<th width="100">Insert By</th>
							<th width="150">Unapproved Request</th>
							<th width=""><input type="checkbox" name="copy_basis" id="copy_basis"/>Un-approved Reason</th>
						<? }else{ ?>
							<th width="100">Refusing Cause</th>
							<th width="">Insert By</th>
						<? } ?>
					</thead>
				</table>
				<div style="width:<?= $tbl_width+20;?>px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="left">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $tbl_width;?>" class="rpt_table" id="tbl_list_search">
						<tbody>
							<?
								//echo $sql; die;
								$i=1;
								$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$company_id." and module_id=2 and report_id=32 and is_deleted=0 and status_active=1");
								$report_id = explode(',', $print_report_format);

								$nameArray=sql_select( $sql );
								foreach ($nameArray as $row)
								{
									$bgcolor = ($i%2==0) ? "#E9F3FF" : "#FFFFFF";
				
									$value=$row[csf('id')];
									$gmt_nature=$row[csf('garments_nature')];
									$date = change_date_format($row[csf("approved_date")]);
									
									$quot_date=$row[csf("quot_date")];
									$txt_quotation_date=change_date_format($quot_date, "yyyy-mm-dd", "-",1)	;	
									$asking_profit=return_field_value("asking_profit", "lib_standard_cm_entry", "company_id=$company_id  and '$txt_quotation_date' between applying_period_date and applying_period_to_date  and status_active=1 and is_deleted=0");
									if($asking_profit=="") $asking_profit=0;
									$costing_per_id=$row[csf("costing_per_id")];
									$final_cost_dzn=$row[csf("total_cost")];
							
									if($costing_per_id==1) { $final_cost_psc=$final_cost_dzn/12; $order_price_per_dzn=12; }
									else if($costing_per_id==2) { $final_cost_psc=$final_cost_dzn/1; $order_price_per_dzn=1; }
									else if($costing_per_id==3) { $final_cost_psc=$final_cost_dzn/(2*12); $order_price_per_dzn=24; }
									else if($costing_per_id==4) { $final_cost_psc=$final_cost_dzn/(3*12); $order_price_per_dzn=36; }
									else if($costing_per_id==5) { $final_cost_psc=$final_cost_dzn/(4*12); $order_price_per_dzn=48; }
									$margin_method=1-($asking_profit/100);
									$net_asking_profit=($final_cost_psc/$margin_method)-$final_cost_psc;
									$net_asking_profit=$net_asking_profit*$order_price_per_dzn;
									$cost_dzn=$final_cost_dzn+$net_asking_profit+$row[csf("commission")];
									$margin_dzn=$row[csf("price_with_commn_dzn")]-$cost_dzn;

									//Print button.................................
									if($report_id[0] == 90) $quotation_rep = 'preCostRpt';
									else if($report_id[0] == 91) $quotation_rep = 'preCostRpt2';
									else if($report_id[0] == 92) $quotation_rep = 'preCostRpt3';
									else if($report_id[0] == 219) $quotation_rep = 'preCostRpt4';
									else if($report_id[0] == 239) $quotation_rep = 'summary2';
									else if($report_id[0] == 406) $quotation_rep = 'buyerSubmitSummery';
									else if($report_id[0] == 217) $quotation_rep = 'lc_cost_details';
									else if($report_id[0] == 275) $quotation_rep = 'preCostRpt5';
									else if($report_id[0] == 414) $quotation_rep = 'preCostRpt6';
									else if($report_id[0] == 137) $quotation_rep = 'preCostRpt11';
									else if($report_id[0] == 191) $quotation_rep = 'preCostRpt12';
									else if($report_id[0] == 220) $quotation_rep = 'preCostRpt13';	

									$dataStr = $quotation_rep.'**'.$row[csf('id')].'**'.$company_id.'**'.$row[csf('buyer_id')].'**'.$row[csf('style_ref')].'**'.$row[csf('quot_date')];
									$fn = "generate_worder_report('$dataStr')";
									//.................................end;




									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" align="center">
										<td width="30" align="center" valign="middle">
											<input type="checkbox" id="tbl_<? echo $i;?>" />
											<input id="booking_id_<? echo $i;?>" name="booking_id[]" type="hidden" value="<? echo $value; ?>" />
											<input id="booking_no_<? echo $i;?>" name="booking_no]" type="hidden" value="<? echo $row[csf('id')]; ?>" />
											<input id="approval_id_<? echo $i;?>" name="approval_id[]" type="hidden" value="<? echo $row[csf('approval_id')]; ?>" />
											<input id="<? echo strtoupper($row[csf('id')]); ?>" name="no_quot[]" type="hidden" value="<? echo $i;?>" />
										</td>
										<td width="40" align="center"><? echo $i; ?></td>
										<td width="70"><p><a href='##' style='color:#000' onClick="<?= $fn;?>"><? echo $row[csf('id')]; ?></a></p></td>
										<td width="60"><p><? echo $row[csf('mkt_no')]; ?>&nbsp;</p></td>
										<td width="100"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
										<td width="100" align="left"><p><? echo $row[csf('style_ref')]; ?>&nbsp;</p></td>
										<td width="80" align="center"><? if($row[csf('quot_date')]!="0000-00-00") echo change_date_format($row[csf('quot_date')]); ?>&nbsp;</td>
										<td align="center" width="80"><? if($row[csf('est_ship_date')]!="0000-00-00") echo change_date_format($row[csf('est_ship_date')]); ?>&nbsp;</td>
										<td width="50" align="center"><a href="##" onClick="openImgFile('<? echo $row[csf('id')];?>','img');">View</a></td>
	
										<td width="80"><p><?  echo number_format($row[csf("MARGIN_DZN_PERCENT")],2); ?></p></td>
										<td align="center" width="80"><? if($row[csf('approved_date')]!="0000-00-00") echo change_date_format($row[csf('approved_date')]); ?>&nbsp;</td>
										<? if($approval_type==1){?>
											<td width="100"><p><? echo ucfirst ($user_arr[$row[csf('inserted_by')]]); ?>&nbsp;</p></td>
											<td align="center" width="150"><? echo $unapproved_request_arr[$value]; ?>&nbsp;</td>
											<td><input type="text" name="unapprove_reason[]" id="unapprove_reason_<?echo $i;?>"  onChange="copy_value(this.value,'unapprove_reason_',<?php echo $i; ?>); "  name="unapprove_reason_<?php echo $i; ?>" class="text_boxes"></td>
										<? }else{ ?>
											<td width="100"> <input style="width:80px;" type="text" class="text_boxes"  name="txtCause_<? echo $row[csf('id')];?>" id="txtCause_<? echo $row[csf('id')];?>" placeholder="browse" onClick="openmypage_refusing_cause('requires/price_quatation_approval_group_by_controller.php?action=refusing_cause_popup','Refusing Cause','<? echo $row[csf('id')];?>');" value="<? echo $row[csf('refusing_cause')];?>"/></td>
											<td><p><? echo ucfirst($user_arr[$row['INSERTED_BY']]); ?>&nbsp;</p></td>
										<? } ?>
									</tr>
									<?
									$i++;
								}
							?>
						</tbody>
					</table>
				</div>
				<table align="left" cellspacing="0" cellpadding="0" border="0" rules="all" width="<? echo $tbl_width;?>" class="rpt_table">
					<tfoot>
						<td width="30" align="center" >
							 <input type="checkbox" style="cursor: pointer;" id="all_check" onClick="check_all('all_check')" />
						</td>
						<td colspan="2" align="left">
							<input type="button" value="<? if($approval_type==1 || $previous_approved_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<?= $i; ?>,<?= $approval_type; ?>)"/>

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

// ********************************** Approve and Unapprove process start ********************************************
if ($action=="approve")
{
	// var_dump($_REQUEST);die();
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$con = connect();

	$company_id = str_replace("'","",$cbo_company_name);
    $cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
    $cbo_get_upto = str_replace("'","",$cbo_get_upto);
    $txt_date_from = str_replace("'","",$txt_date_from);
    $txt_date_to = str_replace("'","",$txt_date_to);
    $txt_team_member = str_replace("'","",$txt_team_member);
    $txt_price_quotation_id = str_replace("'","",$txt_price_quotation_id);
    $approval_type = str_replace("'","",$approval_type);
    $txt_alter_user_id = str_replace("'","",$txt_alter_user_id);
	$user_id_approval=($txt_alter_user_id)?$txt_alter_user_id:$user_id;

 

	$sql="select A.ID,a.APPROVED,A.BUYER_ID,A.READY_TO_APPROVED from wo_price_quotation a where a.id in($booking_ids)";
	 //echo $sql;die;
	$sqlResult=sql_select( $sql );
	foreach ($sqlResult as $row)
	{
		if($row['READY_TO_APPROVED'] != 1){echo '25**Please select ready to approved yes for approved this quatation';exit();}
		$matchDataArr[$row['ID']]=array('buyer'=>$row['BUYER_ID'],'brand'=>$row['BRAND_ID'],'item'=>0,'store'=>0);
		$approved_status_arr[$row['ID']] = $row['APPROVED'];
	}


	$entry_form = 10;
	$finalDataArr=getFinalUser(array('company_id'=>$company_id,'entry_form'=>$entry_form,'lib_buyer_arr'=>$buyer_arr,'lib_item_cat_arr'=>0,'lib_store_arr'=>0,'product_dept_arr'=>0,'match_data'=>$matchDataArr));
	
	$sequ_no_arr_by_sys_id =$finalDataArr['final_seq'];
	$user_sequence_no = $finalDataArr['user_seq'][$user_id_approval];
	$user_group_no = $finalDataArr['user_group'][$user_id_approval];
	$max_group_no = max($finalDataArr['user_group']);

	


	if($approval_type==0) // approve process start
	{

		$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and APPROVED_BY=$user_id_approval and entry_form=10 group by mst_id","mst_id","approved_no");
		
		$id=return_next_id( "id","approval_mst", 1 ) ;
		$ahid=return_next_id( "id","approval_history", 1 ) ;
		
		//$booking_nos_arr = explode(',',$booking_nos);	
		$target_app_id_arr = explode(',',$booking_ids);
        foreach($target_app_id_arr as $key => $mst_id)
        {		
			

			//$approved = ((max($finalDataArr['final_seq'][$mst_id]) == $user_sequence_no) || (max($finalDataArr['last_bypass_no_data_arr'][2]) == $user_sequence_no) || (max($finalDataArr['last_bypass_no_data_arr'][2]) =='' && $max_group_no == $user_group_no)) ? 1 : 3;

			$approved = ((max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no) || ( (max($finalDataArr['group_bypass_no_data_arr'][$max_group_no][2])==$user_sequence_no) && ($max_group_no == $user_group_no))  || ( (max($finalDataArr['group_bypass_no_data_arr'][$max_group_no][2]) == '') && ($max_group_no == $user_group_no) ) )?1:3;

			//echo $approved;die;

			$approved_no = $max_approved_no_arr[$mst_id]*1;
			$approved_status = $approved_status_arr[$mst_id]*1;

			//echo $approved_status;die;
			if($approved_status==2 || $approved_status==0)
			{	
				$approved_no = $approved_no+1;
				$approved_no_array[$mst_id] = $approved_no;
			}
	


			if($data_array!=''){$data_array.=",";}
			$data_array.="(".$id.",".$entry_form.",".$mst_id.",".$user_sequence_no.",".$user_group_no.",".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
			$id=$id+1;
			

			if($history_data_array!="") $history_data_array.=",";
			$history_data_array.="(".$ahid.",".$entry_form.",".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,".$approved.")";
			$ahid++;
			
			//mst data.......................
			$mst_data_array_up[$mst_id] = explode(",",("".$approved.",".$user_sequence_no.",".$user_group_no.",".$user_id_approval.",'".date('d-M-Y',strtotime($date_time))."'")); 
        }
	
 
			
		$flag=1;
		if($flag==1) 
		{
			$field_array="ID, ENTRY_FORM, MST_ID,  SEQUENCE_NO,GROUP_NO,APPROVED_BY, APPROVED_DATE,INSERTED_BY,INSERT_DATE,USER_IP";
			$rID12=sql_insert("approval_mst",$field_array,$data_array,0);
			if($rID12) $flag=1; else $flag=0; 
		}
 
		
		if($flag==1) 
		{
			$field_array_up="APPROVED*APPROVED_SEQU_BY*APPROVED_GROUP_BY*APPROVED_BY*APPROVED_DATE"; 
			$rID13=execute_query(bulk_update_sql_statement( "wo_price_quotation", "id", $field_array_up, $mst_data_array_up, $target_app_id_arr ));
			if($rID13) $flag=1; else $flag=0; 
		}
	
		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=$entry_form and mst_id in ($booking_ids)";
			$rID14=execute_query($query,1);
			if($rID14) $flag=1; else $flag=0;
		}
		 
		if($flag==1)
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING,APPROVED";
			$rID15=sql_insert("approval_history",$field_array,$history_data_array,0);
			if($rID15) $flag=1; else $flag=0;
		}
		//echo "insert into approval_history $field_array value $history_data_array";oci_rollback($con); die;
		//echo "21**".$flag;oci_rollback($con); die;
		
		if(count($approved_no_array)>0)
		{
			$approved_string="";

			foreach($approved_no_array as $key=>$value)
			{
				$approved_string.=" WHEN $key THEN $value";
			}

			$approved_string_mst="CASE id ".$approved_string." END";
			$approved_string_dtls="CASE quotation_id ".$approved_string." END";

			$sql_insert="insert into wo_price_quotation_his( id, approved_no, quotation_id, company_id, buyer_id, style_ref, revised_no, pord_dept, product_code, style_desc, currency, agent, offer_qnty, region, color_range, incoterm, incoterm_place, machine_line, prod_line_hr, fabric_source, costing_per, quot_date, est_ship_date, factory, remarks, garments_nature, order_uom, gmts_item_id, set_break_down, total_set_qnty, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, efficiency_wastage_percent, season, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, op_date, inquery_id, m_list_no, bh_marchant, ready_to_approved)
				select
				'', $approved_string_mst, id, company_id, buyer_id, style_ref, revised_no, pord_dept, product_code, style_desc, currency, agent, offer_qnty, region, color_range, incoterm, incoterm_place, machine_line, prod_line_hr, fabric_source, costing_per, quot_date, est_ship_date, factory, remarks, garments_nature, order_uom, gmts_item_id, set_break_down, total_set_qnty, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, efficiency_wastage_percent, season, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, op_date, inquery_id, m_list_no, bh_marchant, ready_to_approved
				from wo_price_quotation where id in ($booking_ids)";
			//echo $sql_insert;die;

			$sql_insert2="insert into wo_price_quot_costing_mst_his(id, quot_mst_id, quotation_id, approved_no, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, commission, commission_percent, final_cost_dzn, final_cost_dzn_percent, final_cost_pcs, final_cost_set_pcs_rate, a1st_quoted_price, a1st_quoted_price_percent, a1st_quoted_price_date, revised_price, revised_price_date, confirm_price, confirm_price_set_pcs_rate, confirm_price_dzn, confirm_price_dzn_percent, margin_dzn, margin_dzn_percent, price_with_commn_dzn, price_with_commn_percent_dzn, price_with_commn_pcs, price_with_commn_percent_pcs, confirm_date, asking_quoted_price, asking_quoted_price_percent, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, terget_qty, depr_amor_pre_cost, depr_amor_po_price, interest_pre_cost, interest_po_price, income_tax_pre_cost, income_tax_po_price)
				select
				'', id, quotation_id, $approved_string_dtls, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, commission, commission_percent, final_cost_dzn, final_cost_dzn_percent, final_cost_pcs, final_cost_set_pcs_rate, a1st_quoted_price, a1st_quoted_price_percent, a1st_quoted_price_date, revised_price, revised_price_date, confirm_price, confirm_price_set_pcs_rate, confirm_price_dzn, confirm_price_dzn_percent, margin_dzn, margin_dzn_percent, price_with_commn_dzn, price_with_commn_percent_dzn, price_with_commn_pcs, price_with_commn_percent_pcs, confirm_date, asking_quoted_price, asking_quoted_price_percent, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, terget_qty, depr_amor_pre_cost, depr_amor_po_price, interest_pre_cost, interest_po_price, income_tax_pre_cost, income_tax_po_price from wo_price_quotation_costing_mst where quotation_id in ($booking_ids)";
			//echo $sql_insert2;die;

			$sql_insert3="insert into wo_price_quot_set_details_his(id, approved_no, quot_set_dlts_id, quotation_id, gmts_item_id, set_item_ratio)
				select
				'', $approved_string_dtls, id, quotation_id, gmts_item_id, set_item_ratio from wo_price_quotation_set_details where quotation_id in ($booking_ids)";
			//echo $sql_insert3;die;

			$sql_insert4="insert into wo_pri_quo_comm_cost_dtls_his(id, approved_no, quo_comm_dtls_id, quotation_id, item_id, base_id,  rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted)
				select
				'', $approved_string_dtls, id, quotation_id, item_id, base_id, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_pri_quo_comarcial_cost_dtls where quotation_id in ($booking_ids)";
			//echo $sql_insert4;die;

			$sql_insert5="insert into wo_pri_quo_commiss_dtls_his(id, approved_no, quo_commiss_dtls_id, quotation_id, particulars_id, commission_base_id, commision_rate, commission_amount, inserted_by, insert_date, updated_by, update_date,  status_active, is_deleted)
				select
				'', $approved_string_dtls, id, quotation_id, particulars_id, commission_base_id, commision_rate, commission_amount, inserted_by, insert_date, updated_by,  update_date, status_active, is_deleted from wo_pri_quo_commiss_cost_dtls where quotation_id in ($booking_ids)";
			//echo $sql_insert5;die;

			$sql_insert6="insert into wo_pri_quo_embe_cost_dtls_his(id, approved_no, quo_emb_dtls_id, quotation_id, emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted)
				select
				'', $approved_string_dtls, id, quotation_id, emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_pri_quo_embe_cost_dtls where quotation_id in ($booking_ids)";
			//echo $sql_insert6;die;

			$sql_insert7="insert into wo_pri_quo_fab_cost_dtls_his(id, approved_no, quo_fab_dtls_id, quotation_id, item_number_id, body_part_id,  fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, company_id, costing_per, fab_cons_in_quotat_varia, process_loss_method, yarn_breack_down, width_dia_type, cons_breack_down, msmnt_break_down, marker_break_down)
				select
				'', $approved_string_dtls, id, quotation_id, item_number_id, body_part_id, fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, company_id, costing_per, fab_cons_in_quotat_varia, process_loss_method, yarn_breack_down, width_dia_type, cons_breack_down, msmnt_break_down, marker_break_down from wo_pri_quo_fabric_cost_dtls where quotation_id in ($booking_ids)";
			//echo $sql_insert7;die;

			$sql_insert8="insert into wo_pri_quo_fab_conv_dtls_his (id, approved_no, quo_fab_conv_dtls_id, quotation_id, cost_head, cons_type, req_qnty, charge_unit, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, process_loss)
				select
				'', $approved_string_dtls, id, quotation_id, cost_head, cons_type, req_qnty, charge_unit, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, process_loss from wo_pri_quo_fab_conv_cost_dtls where quotation_id in ($booking_ids)";
			//echo $sql_insert8;die;

			$sql_insert9="insert into wo_pri_quo_fab_co_avg_con_his (id, approved_no, quo_fab_avg_co_dtls_id, wo_pri_quo_fab_co_dtls_id, quotation_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs, body_length, body_sewing_margin, body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin, front_rise_length, front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin, total, marker_dia, marker_yds, marker_inch, gmts_pcs, marker_length, net_fab_cons)
				select
				'', $approved_string_dtls, id, wo_pri_quo_fab_co_dtls_id, quotation_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs, body_length, body_sewing_margin, body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin, front_rise_length, front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin, total, marker_dia, marker_yds, marker_inch, gmts_pcs, marker_length, net_fab_cons from wo_pri_quo_fab_co_avg_con_dtls where quotation_id in ($booking_ids)";
			//echo $sql_insert9;die;

			$sql_insert10="insert into wo_pri_quo_fab_yarn_dtls_his(id, approved_no, quo_yarn_dtls_id, quotation_id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, supplier_id)
				select
				'', $approved_string_dtls, id, quotation_id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, supplier_id from wo_pri_quo_fab_yarn_cost_dtls where quotation_id in ($booking_ids)";
			//echo $sql_insert10;die;

			$sql_insert11="insert into wo_pri_quo_sum_dtls_his( id, approved_no, quo_sum_dtls_id, quotation_id, fab_yarn_req_kg, fab_woven_req_yds, fab_knit_req_kg, fab_amount, yarn_cons_qnty, yarn_amount, conv_req_qnty, conv_charge_unit, conv_amount, trim_cons, trim_rate, trim_amount, emb_amount, wash_amount, comar_rate, comar_amount, commis_rate, commis_amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted)
				select
				'', $approved_string_dtls, id, quotation_id, fab_yarn_req_kg, fab_woven_req_yds, fab_knit_req_kg, fab_amount, yarn_cons_qnty, yarn_amount, conv_req_qnty, conv_charge_unit, conv_amount, trim_cons, trim_rate, trim_amount, emb_amount, wash_amount, comar_rate, comar_amount, commis_rate, commis_amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_pri_quo_sum_dtls where quotation_id in ($booking_ids)";
			//echo $sql_insert11;die;

			$sql_insert12="insert into wo_pri_quo_trim_cost_dtls_his( id, approved_no, quo_trim_dtls_id, quotation_id, trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted)
				select
				'', $approved_string_dtls, id, quotation_id, trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_pri_quo_trim_cost_dtls where quotation_id in ($booking_ids)";

			if($flag==1)
			{
				$rID1=execute_query($sql_insert,1);
				if($rID1) $flag=1; else $flag=0;
			}

			if($flag==1)
			{
				$rID2=execute_query($sql_insert2,1);
				if($rID2) $flag=1; else $flag=0;
			}

			if($flag==1)
			{
				$rID3=execute_query($sql_insert3,1);
				if($rID3) $flag=1; else $flag=0;
			}

			if($flag==1)
			{
				$rID4=execute_query($sql_insert4,1);
				if($rID4) $flag=1; else $flag=0;
			}

			if($flag==1)
			{
				$rID7=execute_query($sql_insert5,1);
				if($rID7) $flag=1; else $flag=0;
			}

			if($flag==1)
			{
				$rID5=execute_query($sql_insert6,1);
				if($rID5) $flag=1; else $flag=0;
			}

			if($flag==1)
			{
				$rID6=execute_query($sql_insert7,1);
				if($rID6) $flag=1; else $flag=0;
			}

			
			if($flag==1)
			{
				$rID7=execute_query($sql_insert8,1);
				if($rID7) $flag=1; else $flag=0;
			}

			if($flag==1)
			{
				$rID8=execute_query($sql_insert9,1);
				if($rID8) $flag=1; else $flag=0;
			}
			
			if($flag==1)
			{
				$rID9=execute_query($sql_insert10,1);
				if($rID9) $flag=1; else $flag=0;
			}

			if($flag==1)
			{
				$rID10=execute_query($sql_insert11,1);
				if($rID10) $flag=1; else $flag=0;
			}

			if($flag==1)
			{
				$rID11=execute_query($sql_insert12,1);
				if($rID11) $flag=1; else $flag=0;
			}
			//echo $sql_insert12;die;
		}

		if($flag==1) $msg='19'; else $msg='21';
	}
	else
	{
		$flag=1;
		if($flag == 1){
			$rID1=sql_multirow_update("wo_price_quotation","APPROVED*READY_TO_APPROVED*APPROVED_SEQU_BY*APPROVED_GROUP_BY*APPROVED_BY",'0*0*0*0*0',"id",$booking_ids,0);
			if($rID1) $flag=1; else $flag=0;
		}

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0,APPROVED=0,IS_SIGNING=0 WHERE entry_form=$entry_form and mst_id in ($booking_ids) and approved_by <> $user_id_approval ";
			$rID2=execute_query($query,1);
			if($rID2) $flag=1; else $flag=0;
		}

		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form = $entry_form and mst_id in ($booking_ids)";
			$rID3=execute_query($query,1); 
			if($rID3) $flag=1; else $flag=0; 
		}
		
		// if($flag==1)
		// {
		// 	$unapproved_status="UPDATE fabric_booking_approval_cause SET status_active=0,is_deleted=1 WHERE entry_form=$entry_form and approval_type=2 and is_deleted=0 and status_active=1 and booking_id in ($booking_ids)";
		// 	$rID4=execute_query($unapproved_status,1);
		// 	if($rID4) $flag=1; else $flag=0;
		// }

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0,IS_SIGNING=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id_approval."', update_date='".$pc_date_time."' ,APPROVED=0 WHERE entry_form=$entry_form and current_approval_status=1 and mst_id in ($booking_ids)";
			$rID5=execute_query($query,1);
			if($rID5) $flag=1; else $flag=0;
		}	


		if($flag==1) $msg='20'; else $msg='22';
	}

	if($flag==1)
	{
		oci_commit($con); 
		echo $msg."**".$booking_ids;
	}
	else
	{
		oci_rollback($con); 
		echo $msg."**".$booking_ids;
	}

	disconnect($con);
	die;
	
	


}

if ($action=="img"){
	$sql = "select IMAGE_LOCATION,REAL_FILE_NAME from common_photo_library where is_deleted=0 and MASTER_TBLE_ID='$id' and FORM_NAME='quotation_entry'";
	$sqlRes = sql_select($sql);
	foreach($sqlRes as $rows){
		echo '<img style="width:50%" src="../../'.$rows['IMAGE_LOCATION'].'" alt="">';
	}

	exit();
}

if($action=='user_popup')
{
	echo load_html_head_contents("Popup Info","../../",1, 1,'',1,'');
	?>	
	<form>
	    <input type="hidden" id="selected_id" name="selected_id" /> 
	    <?php
	        $custom_designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');	
			$Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');	;
			$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation,b.sequence_no,b.GROUP_NO from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.entry_form=10 and valid=1 and a.id!=$user_id and a.is_deleted=0 and b.is_deleted=0  order by sequence_no ASC";
				 //echo $sql;
			 $arr=array (2=>$custom_designation,3=>$Department);
			 echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department,Seq,Group", "100,120,150,150,50,50","730","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id,0,0", $arr , "user_name,user_full_name,designation,department_id,sequence_no,GROUP_NO", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
		?>
	</form>
	<script language="javascript" type="text/javascript">
		function js_set_value(id)
		{ 
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
	  	setFilterGrid("tbl_style_ref");
	</script>
	<?
}// action SystemIdPopup end;


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
			alert("Please save refusing cause first or empty");
			return;
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
			http.open("POST","price_quatation_approval_group_by_controller.php",true);
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
				alert("Data saved successfully");
				document.getElementById('txt_refusing_cause').value =response[1];
				parent.emailwindow.hide();
			}
			else
			{
				alert("data not saved");
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
					<td >
						<input type="text" name="txt_refusing_cause" id="txt_refusing_cause" class="text_boxes" style="width:95%;height: 100px;" value="<?= $refusing_cause;?>" placeholder="Write Refusing Cause" />
						<input type="hidden" name="hidden_quo_id" id="hidden_quo_id" value="<? echo $quo_id;?>">
					</td>
                  </tr>
                  <tr>
					<td align="center" class="button_container">
						<?
					     echo load_submit_buttons( $permission, "fnc_cause_info", 0,0 ,"reset_form('causeinfo_1','','')",1);
				        ?>
				        <input type="button" class="formbutton" value="Close" name="close_buttons" id="close_buttons" onClick="set_values();" style="width:50px;">
 					</td>
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

		$get_history = sql_select("SELECT id from approval_history where mst_id='$quo_id' and entry_form =10 and current_approval_status=1");
		$id=return_next_id( "id", "refusing_cause_history", 1);

		$con = connect();

		$field_array = "id,entry_form,mst_id,refusing_reason,inserted_by,insert_date";
		$data_array = "(".$id.",10,".$quo_id.",'".$refusing_cause."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		$rID=sql_insert("refusing_cause_history",$field_array,$data_array,1);
		

		if($rID)
		{
			oci_commit($con);
			echo "0**$refusing_cause";
		}
		else{
			oci_rollback($con);
			echo "10**";
		}
	
		disconnect($con);
		die;
	}
}
 
?>