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
 

if($db_type==0) $year_cond="SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
else if($db_type==2) $year_cond="to_char(a.insert_date,'YYYY') as year";

if($db_type==0) $year_cond_groupby="SUBSTRING_INDEX(a.insert_date, '-', 1)";
else if($db_type==2) $year_cond_groupby="to_char(a.insert_date,'YYYY')";

$userCredential = sql_select("SELECT brand_id, single_user_id FROM user_passwd where id=$user_id");
$userbrand_id = $userCredential[0][csf('brand_id')];
$single_user_id = $userCredential[0][csf('single_user_id')];

$userbrand_idCond = ""; $filterBrandId = "";
if ($userbrand_id !='' && $single_user_id==1) {
    $userbrand_idCond = "and id in ($userbrand_id)";
	$filterBrandId=$userbrand_id;
}

if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season_id", 80, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-Select Season-", "", "" );
	exit();
}

if ($action=="load_drop_down_brand")
{
	echo create_drop_down( "cbo_brand", 80, "select id, brand_name from lib_buyer_brand where buyer_id='$data' and status_active =1 and is_deleted=0 $userbrand_idCond order by brand_name ASC","id,brand_name", 1, "-Brand-", $selected, "" );
	exit();
}

if($action=="load_drop_down_buyer"){
    echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) and b.TAG_COMPANY = $data group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/quick_costing_approval_woven_controller', this.value, 'load_drop_down_brand', 'brand_td'); load_drop_down('requires/quick_costing_approval_woven_controller', this.value, 'load_drop_down_season', 'season_td');" );
}


function getSequence($parameterArr=array()){
	$lib_buyer_id_string=implode(',',(array_keys($parameterArr['lib_buyer_arr']))); 
	$lib_brand_id_string=implode(',',(array_keys($parameterArr['lib_brand_arr']))); 

	$brandSql = "SELECT ID,BUYER_ID FROM LIB_BUYER_BRAND WHERE IS_DELETED = 0";
    $brandSqlRes=sql_select($brandSql);
    $brand_id_by_buyer_arr = array();
	foreach($brandSqlRes as $row){
        if($row['ID']){$brand_id_by_buyer_arr[$row['BUYER_ID']][$row['ID']] = $row['ID'];}
    }


	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,ENTRY_FORM,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['entry_form']} AND IS_DELETED = 0 order by SEQUENCE_NO";
	 //echo $sql;die;
	$sql_result=sql_select($sql);
	$dataArr=array();
	foreach($sql_result as $rows){

		$rows['BUYER_ID']=($rows['BUYER_ID']!='')?$rows['BUYER_ID']:$lib_buyer_id_string;
		if($rows['BRAND_ID'] == ''){
            $tmp_brand_id_arr = array();
            foreach(explode(',',$rows['BUYER_ID']) as $bid){
                if(count($brand_id_by_buyer_arr[$bid])){$tmp_brand_id_arr[$bid] = implode(',',$brand_id_by_buyer_arr[$bid]);}
            }
            $rows['BRAND_ID'] = implode(',',$tmp_brand_id_arr);
        }

		
		$dataArr['sequ_by'][$rows['SEQUENCE_NO']]=$rows;
		$dataArr['user_by'][$rows['USER_ID']]=$rows;
		$dataArr['sequ_arr'][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
	}
	

	return $dataArr;
}

function getFinalUser($parameterArr=array()){
	$lib_buyer_id_string=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));

    $brandSql = "SELECT ID,BUYER_ID FROM LIB_BUYER_BRAND WHERE IS_DELETED = 0";
    $brandSqlRes=sql_select($brandSql);
    $brand_id_by_buyer_arr = array();
	foreach($brandSqlRes as $row){
		if($row['ID']){$brand_id_by_buyer_arr[$row['BUYER_ID']][$row['ID']] = $row['ID'];}
    }
	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,ENTRY_FORM,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['entry_form']} AND IS_DELETED = 0  order by SEQUENCE_NO";
	  // echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){

        $rows['BUYER_ID']=($rows['BUYER_ID']!='')?$rows['BUYER_ID']:$lib_buyer_id_string;
		
        if($rows['BRAND_ID'] == ''){
            $tmp_brand_id_arr = array();
            foreach(explode(',',$rows['BUYER_ID']) as $bid){
                if(count($brand_id_by_buyer_arr[$bid])){$tmp_brand_id_arr[$bid] = implode(',',$brand_id_by_buyer_arr[$bid]);}
            }
            $rows['BRAND_ID'] = implode(',',$tmp_brand_id_arr);
        }
		
		$usersDataArr[$rows['USER_ID']]['BUYER_ID']=explode(',',$rows['BUYER_ID']);
		$usersDataArr[$rows['USER_ID']]['BRAND_ID']=explode(',',$rows['BRAND_ID']);
		
		$userSeqDataArr[$rows['USER_ID']]=$rows['SEQUENCE_NO'];
	
	}
 
	$finalSeq=array();
	foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){
		
		foreach($userSeqDataArr as $userId=>$seq){
			if(
				(in_array($bbtsRows['buyer'],$usersDataArr[$userId]['BUYER_ID']) || $bbtsRows['buyer']==0)
				&& (in_array($bbtsRows['brand'],$usersDataArr[$userId]['BRAND_ID']) || $bbtsRows['brand']==0)
			){
				$finalSeq[$sys_id][$userId]=$seq;
			}


		}
	}

	//print_r($usersDataArr[270]['BRAND_ID']);die;

	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr);
}

$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));


	$company_id = str_replace("'","",$cbo_company_id);
	$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
	$cbo_brand=str_replace("'","",$cbo_brand);
	$cbo_season_id=str_replace("'","",$cbo_season_id);
	$cbo_season_year=str_replace("'","",$cbo_season_year);
	$cbo_year=str_replace("'","",$cbo_year);
    $approval_type=str_replace("'","",$cbo_approval_type);
    $txt_date=str_replace("'","",$txt_date);
    $cbo_get_upto=str_replace("'","",$cbo_get_upto);
    $txt_costshit_no=str_replace("'","",$txt_costshit_no);
    $txt_style_ref=str_replace("'","",$txt_style_ref);

    $txt_alter_user_id = str_replace("'","",$txt_alter_user_id);
    $app_user_id = ($txt_alter_user_id!="")?$txt_alter_user_id:$user_id;

    
    if($cbo_buyer_name){$where_con = " and a.buyer_id=$cbo_buyer_name";}
    if($cbo_brand){$where_con .= " and a.brand_id in($cbo_brand)";}
    if($cbo_season_id){$where_con .= " and a.season_id=$cbo_season_id";}
    if($cbo_season_year){$where_con .= " and a.season_year=$cbo_season_id";}
    if($cbo_year){$where_con .= " and YEAR(a.insert_date)=$cbo_year";}
    if($txt_style_ref){$where_con .= " and a.style_ref like('%$txt_style_ref%')";}
    if($txt_costshit_no){$where_con .= " and a.cost_sheet_no like('%$txt_costshit_no')";}
	if($txt_date!="")
	{
		if($cbo_get_upto==1) $where_con .= " and a.costing_date > '$txt_date'";
		else if($cbo_get_upto==2) $where_con .= " and a.costing_date <= '$txt_date'";
		else if($cbo_get_upto==3) $where_con .= " and a.costing_date = '$txt_date'";
	}





    $electronicDataArr = getSequence(array('company_id'=>$company_id,'entry_form'=>45,'user_id'=>$app_user_id,'lib_buyer_arr'=>$buyer_arr,'lib_department_id_arr'=>0));

    //print_r($electronicDataArr);die;

	$year="TO_CHAR(a.insert_date,'YYYY')";

	
    if($approval_type==2)
    {
    
        //Match data..................................

        if($electronicDataArr['user_by'][$app_user_id]['BUYER_ID']){
			$where_con .= " and a.BUYER_ID in(".$electronicDataArr['user_by'][$app_user_id]['BUYER_ID'].",0)";
			$electronicDataArr['sequ_by'][0]['BUYER_ID']=$electronicDataArr['user_by'][$app_user_id]['BUYER_ID'];
		}

        if($electronicDataArr['user_by'][$app_user_id]['BRAND_ID']){
			$where_con .= " and a.BRAND_ID in(".$electronicDataArr['user_by'][$app_user_id]['BRAND_ID'].",0)";
			$electronicDataArr['sequ_by'][0]['BRAND_ID']=$electronicDataArr['user_by'][$app_user_id]['BRAND_ID'];
		}
		 
			
        $data_mas_sql="SELECT a.ID, a.COMPANY_ID, a.BUYER_ID,a.BRAND_ID from qc_mst a,qc_confirm_mst b where a.qc_no=b.cost_sheet_id and a.COMPANY_ID=$company_id and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.READY_TO_APPROVE=1 and a.approved<>1  $where_con"; 

       //echo $data_mas_sql;die;


        $tmp_sys_id_arr=array();
        $data_mas_sql_res=sql_select( $data_mas_sql );
        foreach ($data_mas_sql_res as $row)
        { 
            for($seq=($electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO']-1);$seq>=0; $seq-- ){
                if( 
                    (in_array($row['BUYER_ID'],explode(',',$electronicDataArr['sequ_by'][$seq]['BUYER_ID'])) || $row['BUYER_ID']==0)
                    && (in_array($row['BRAND_ID'],explode(',',$electronicDataArr['sequ_by'][$seq]['BRAND_ID'])) || $row['BRAND_ID']==0)
                
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
 
		
		
		
        $sql='';
        for($seq=0;$seq<=count($electronicDataArr['sequ_arr']); $seq++ ){
            $sys_con = where_con_using_array($tmp_sys_id_arr[$seq],0,'a.ID');
            
            if($tmp_sys_id_arr[$seq]){
                if($sql!=''){$sql .=" UNION ALL ";}
                $sql .= "select a.id, a.qc_no, a.inquery_id, b.tot_fob_cost, a.brand_id, a.season_id, a.season_year, a.cost_sheet_id, a.cost_sheet_no, a.entry_form, $year as year, a.style_ref, a.buyer_id, a.delivery_date, a.exchange_rate, a.offer_qty, a.costing_date, a.revise_no, a.option_id, 0 as approval_id, a.approved, a.inserted_by, a.revise_no, a.option_id, c.job_id, c.id as confirm_id from qc_mst a, qc_tot_cost_summary b, qc_confirm_mst c where a.qc_no=b.mst_id and b.mst_id=c.cost_sheet_id and a.status_active=1 and a.is_deleted=0  and a.approved<>1 and a.APPROVED_SEQU_BY=$seq and c.ready_to_approve=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $sys_con";
            }
        
        }
		
    }
    else
    {
		//echo $electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO'];die;
		$sql = "select a.id, a.qc_no, a.inquery_id, b.tot_fob_cost, a.brand_id, a.season_id, a.season_year, a.cost_sheet_id, a.cost_sheet_no, a.entry_form, $year as year, a.style_ref, a.buyer_id, a.delivery_date, a.exchange_rate, a.offer_qty, a.costing_date, a.revise_no, a.option_id, 0 as approval_id, a.approved, a.inserted_by, a.revise_no, a.option_id, c.job_id, c.id as confirm_id from qc_mst a, qc_tot_cost_summary b, qc_confirm_mst c, APPROVAL_MST d where a.qc_no=b.mst_id and b.mst_id=c.cost_sheet_id and a.id=d.mst_id and d.entry_form=45 and a.status_active=1 and a.is_deleted=0  and a.approved in (1,3) and c.ready_to_approve=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.APPROVED_SEQU_BY=d.SEQUENCE_NO  and a.APPROVED_SEQU_BY={$electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO']}  $where_con";

    }
  
	 //echo $sql;die;

	?>
    <script>
		function openmypage_app_cause(wo_id,app_type,i)
		{
			var txt_appv_cause = $("#txt_appv_cause_"+i).val();
			var approval_id = $("#approval_id_"+i).val();
			var data=wo_id+"_"+app_type+"_"+txt_appv_cause+"_"+approval_id;
			var title = 'Approval Cause Info';
			var page_link = 'requires/quick_costing_approval_woven_controller.php?data='+data+'&action=appcause_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
				$('#txt_appv_cause_'+i).val(appv_cause.value);
			}
		}

		function openmypage_app_instrac(wo_id,app_type,i)
		{
			var txt_appv_instra = $("#txt_appv_instra_"+i).val();
			var approval_id = $("#approval_id_"+i).val();
			var data=wo_id+"_"+app_type+"_"+txt_appv_instra+"_"+approval_id;
			var title = 'Approval Instruction';
			var page_link = 'requires/quick_costing_approval_woven_controller.php?data='+data+'&action=appinstra_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
				$('#txt_appv_instra_'+i).val(appv_cause.value);
			}
		}

		function openmypage_unapp_request(wo_id,app_type,i)
		{
			var data=wo_id;
			var title = 'Un Approval Request';
			var page_link = 'requires/quick_costing_approval_woven_controller.php?data='+data+'&action=unappcause_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=180px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var unappv_request=this.contentDoc.getElementById("hidden_unappv_request");
				$('#txt_unappv_req_'+i).val(unappv_request.value);
			}
		}
	</script>
    <?
		$print_report_format_ids = return_field_value("format_id", "lib_report_template", "template_name=" . $cbo_company_id . " and module_id=2 and report_id = 291 and is_deleted=0 and status_active=1");
		$print_format_ids = explode(",", $print_report_format_ids);

		$print_row_id=$print_format_ids[0];
	    //print_r($print_row_id);
	
	 if($print_row_id == 53){
		$print_report_action	="quick_costing_print5";
	}


	//print_r($format_ids);
	$print_report_format=return_field_value("format_id","lib_report_template","module_id=2 and report_id=291 and is_deleted=0 and status_active=1");
	//echo $print_report_format;die;
	$format_ids=explode(",",$print_report_format);
	
	$row_id=$format_ids[0];
	if($row_id == 45){
		$report_action	=	"quick_costing_print4";
	}else if($row_id == 2){
		$report_action	=	"quick_costing_print";
	}else if($row_id == 6){
		$report_action	=	"quick_costing_print3";
	}
	else if($row_id == 53){
		$report_action	=	"quick_costing_print3";
	}
	
	
	$sql_request="select booking_id, approval_cause from fabric_booking_approval_cause where entry_form=28 and approval_type=2 and status_active=1 and is_deleted=0 order by id Desc";
	$unappRequest_arr=array();

	$nameArray_request=sql_select($sql_request);
	foreach($nameArray_request as $approw)
	{
		$unappRequest_arr[$approw[csf("booking_id")]]=$approw[csf("approval_cause")];
	}
	$seasonArr = return_library_array("select id,season_name from lib_buyer_season ","id","season_name");
	$brandArr = return_library_array("select id,brand_name from lib_buyer_brand ","id","brand_name");
	$concernMarchantArr=return_library_array( "select id, concern_marchant from wo_quotation_inquery where entry_form=434", "id", "concern_marchant");
	$teamMemberinfoArr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
	//echo $report_action;die;
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:1450px; margin-top:10px">
        <legend>Quick Costing Approval [WVN]</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1450" class="rpt_table" >
                <thead>
                	<th width="30">&nbsp;</th>
                    <th width="30">SL</th>
                    <th width="120">Buyer</th>
                    <th width="120">Master Style</th>
                    <th width="80">Brand</th>
                    <th width="80">Season</th>
                    <th width="50">Season Year</th>
                    <th width="100">Cost Sheet No</th>
                    <th width="50">Year</th>
                   	<th width="70">Revise No</th>
                   	<th width="70">Option No</th>
                    <th width="65">Costing Date</th>
                    <th width="100">Insert By</th>
                    <th width="70">Offer Qty.</th>
                    <th width="70">FOB Cost</th>
                   	<th width="70">Concern Merchant</th>
                    <th width="70">Approved Date</th>
                    <?
					if($approval_type==0) echo "<th width='80'>Appv Instra</th>";
					if($approval_type==1) echo "<th width='80'>Un-Appv Request</th>";
					?>
                    <th><? if($approval_type==0) echo "Not Appv. Cause" ; else echo "Not Un-Appv. Cause"; ?></th>
                </thead>
            </table>
            <div style="width:1450px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1432" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
                        $i=1;
						$nameArray=sql_select( $sql );
						// print ($sql);die;
						$ref_no=""; $file_numbers="";
						foreach ($nameArray as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if($approval_type==2) $value=$row[csf('id')]; else $value=$row[csf('id')]."**".$row[csf('approval_id')]."**".$row[csf('confirm_id')];;
							
							$fob_cost=$row[csf('tot_fob_cost')];
							if($fob_cost=='' || $fob_cost==0) $fob_cost=0; else $fob_cost=$fob_cost;
							if($fob_cost<0 || $fob_cost==0) $td_color="#F00"; else $td_color="";
							
							?>
							<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>" align="center">
								<td width="30" align="center" valign="middle">
									<input type="checkbox" id="tbl_<?=$i;?>" />
									<input id="booking_id_<?=$i;?>" name="booking_id[]" type="hidden" value="<?=$value; ?>" />
									<input id="confirm_id_<?=$i;?>" name="confirm_id[]" type="hidden" value="<?=$row[csf('confirm_id')]; ?>" />
									<input id="booking_no_<?=$i;?>" name="booking_no[]" type="hidden" value="<?=$row[csf('qc_no')]; ?>" />
									<input id="approval_id_<?=$i;?>" name="approval_id[]" type="hidden" value="<?=$row[csf('approval_id')]; ?>" />
									<input id="<?=strtoupper($row[csf('cost_sheet_no')]); ?>" name="no_joooob[]" type="hidden" value="<?=$i;?>" />
									<input id="cm_cost_id_<?=$i;?>" name="cm_cost_id[]" style="width:20px;" type="hidden" value="<?=$fob_cost; ?>" />
								</td>
								<td width="30" align="center"><?=$i; ?></td>
                                <td width="120" style="word-break:break-all"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</td>
                                <td width="120" align="center" style="word-break:break-all"><a href='##' onClick="fnc_print_report(<?=$row[csf('qc_no')];?>,<?=$row[csf('cost_sheet_no')]; ?>,<?=$row[csf('entry_form')]; ?>,'<?=$report_action; ?>' )"><?=$row[csf('style_ref')]; ?></a></td>
                                
                                <td width="80" style="word-break:break-all"><?=$brandArr[$row[csf('brand_id')]]; ?></td>
                                <td width="80" style="word-break:break-all"><?=$seasonArr[$row[csf('season_id')]]; ?></td>
                                <td width="50" style="word-break:break-all"><?=$row[csf('season_year')]; ?></td>
                                
                                <td width="100" style="word-break:break-all;"><?=$row[csf('cost_sheet_no')]; ?></td>
                                <td width="50" style="word-break:break-all" align="center"><?=$row[csf('year')]; ?>&nbsp;</td>
                                <td width="70" style="word-break:break-all"><a href='##' onClick="fnc_print_report2(<?=$row[csf('qc_no')];?>,<?=$row[csf('cost_sheet_no')]; ?>,<?=$company_id; ?>,<?=$row[csf('revise_no')]; ?>,'<?=$print_report_action; ?>' )"><?=$row[csf('revise_no')]; ?></td>

								<td width="70" style="word-break:break-all"><a href='##' onClick="fnc_print_report2(<?=$row[csf('qc_no')];?>,<?=$row[csf('cost_sheet_no')]; ?>,<?=$company_id; ?>,<?=$row[csf('revise_no')]; ?>,'<?=$print_report_action; ?>' )"><?=$row[csf('option_id')]; ?></td>
                                <td width="65" align="center"><? if($row[csf('costing_date')]!="0000-00-00") echo change_date_format($row[csf('costing_date')]); ?>&nbsp;</td>
                                <td width="100" style="word-break:break-all"><?=ucfirst($user_arr[$row[csf('inserted_by')]]); ?>&nbsp;</td>
								<td width="70" style="word-break:break-all" align="right"><?=$row[csf('offer_qty')]; ?></td>
								<td width="70" align="right"><p style="color:<?=$td_color; ?>"><?=number_format($fob_cost,2); ?>&nbsp;</p></td>
								<td width="70" style="word-break:break-all"><?=$teamMemberinfoArr[$concernMarchantArr[$row[csf('inquery_id')]]]; ?></td>
								<td width="70" align="center"><? if($row[csf('approved_date')]!="0000-00-00") echo $row[csf('approved_date')]; ?>&nbsp;</td>
								<?
									if($approval_type==0)echo "<td align='center' width='80'>
										<Input name='txt_appv_instra[]' class='text_boxes' readonly placeholder='Please Browse' ID='txt_appv_instra_".$i."' style='width:70px' maxlength='50' onClick='openmypage_app_instrac(".$row[csf('qc_no')].",".$approval_type.",".$i.")' ></td>";
									if($approval_type==1)echo "<td align='center' width='80'>
										<Input name='txt_unappv_req[]' class='text_boxes' readonly placeholder='Please Browse' ID='txt_unappv_req_".$i."' style='width:70px' maxlength='50' onClick='openmypage_unapp_request(".$row[csf('qc_no')].",".$approval_type.",".$i.")' value='".$unappRequest_arr[$row[csf('qc_no')]]."'></td>";
								?>
								<td align="center">
									<Input name="txt_appv_cause[]" class="text_boxes" readonly placeholder="Please Browse" ID="txt_appv_cause_<?=$i; ?>" style="width:70px" maxlength="50" title="Maximum 50 Character" onClick="openmypage_app_cause(<?=$row[csf('qc_no')]; ?>,<?=$approval_type; ?>,<?=$i; ?>)">&nbsp;</td>
							</tr>
							<?
							$i++;
						}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="1450" class="rpt_table">
				<tfoot>
                    <td width="30" align="center" ><input type="checkbox" id="all_check" onClick="check_all('all_check');" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<?=$i; ?>,<?=$approval_type; ?>)"/></td>
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

	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$user_id_approval=($txt_alter_user_id!="") ? $txt_alter_user_id:$user_id;


	$sql="SELECT a.ID, a.COMPANY_ID,a.APPROVED, a.BUYER_ID,a.BRAND_ID, b.READY_TO_APPROVE from qc_mst a,qc_confirm_mst b where a.qc_no=b.cost_sheet_id and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.READY_TO_APPROVE=1 and a.COMPANY_ID=$cbo_company_id and a.id in($booking_ids)"; 
	//echo $sql;die;

	$sqlResult=sql_select( $sql );
	foreach ($sqlResult as $row)
	{	if($row['READY_TO_APPROVE'] ==2 ){echo "Ready to approved NO is not allow";die;}
		$matchDataArr[$row['ID']]=array('buyer'=>$row['BUYER_ID'],'brand'=>$row['BRAND_ID'],'item'=>0,'store'=>0);
		$approved_status_arr[$row['ID']] = $row['APPROVED'];
	}


	$brand_arr[0]=0;
	$finalDataArr=getFinalUser(array('company_id'=>$cbo_company_id,'entry_form'=>45,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>$brand_arr,'lib_item_cat_arr'=>$item_cat_arr,'lib_store_arr'=>$lib_store_arr,'product_dept_arr'=>$product_dept,'match_data'=>$matchDataArr));

	//print_r($finalDataArr);die;
	
	$sequ_no_arr_by_sys_id = $finalDataArr['final_seq'];
	$user_sequence_no = $finalDataArr['user_seq'][$user_id_approval];

	//echo $sequ_no_arr_by_sys_id;die;

	if($approval_type==2)
	{
		$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and APPROVED_BY=$user_id_approval and entry_form=45 group by mst_id","mst_id","approved_no");
		//$max_precost_his_approved_no_arr = return_library_array("select pre_cost_mst_id, max(approved_no) as approved_no from wo_pre_cost_mst_histry where pre_cost_mst_id in($booking_ids)  group by pre_cost_mst_id","pre_cost_mst_id","approved_no");

		$id=return_next_id( "id","approval_mst", 1 ) ;
		$ahid=return_next_id( "id","approval_history", 1 ) ;

		$target_app_id_arr = explode(',',$booking_ids);
		$booking_nos_arr = explode(',',$booking_nos);	
		$confirm_ids_arr=explode(",",$confirm_ids);

		$approved_no_array=array();

		


		$i=0;
        foreach($target_app_id_arr as $mst_id)
        {		
			$approved=(max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no)?1:3;
			$approved_no=($max_approved_no_arr[$mst_id] == '') ? 1 : $max_approved_no_arr[$mst_id]+1;

			$approved_no_array[$booking_nos_arr[$i]]=$approved_no;

			//App History...................
			if($app_history_data_array!="") $app_history_data_array.=",";
			$app_history_data_array.="(".$ahid.",45,".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$approved.")";
			$ahid++;
			
			//App Mst...................
			if($app_mst_data_array!=''){$app_mst_data_array.=",";}
			$app_mst_data_array.="(".$id.",45,".$mst_id.",".$user_sequence_no.",".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
			$id=$id+1;

			//qc mst Table ...................
			$data_array_up[$mst_id] = explode(",",("".$approved.",".$user_sequence_no.",".$user_id_approval."")); 
			
			//Confirm Table ...................
			$confirm_id = $confirm_ids_arr[$i];
			$confirm_id_arr[] = $confirm_id;
			$data_array_confirm_update[$confirm_id] = explode("*",($approved));
	
			$i++;
        }

 
		$flag=1;
		if($flag==1) 
		{
			$field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,USER_IP";
			$rIDArr['approval_mst']=sql_insert("approval_mst",$field_array,$app_mst_data_array,0);
			if($rIDArr['approval_mst']) $flag=1; else $flag=0; 
		}

		

		if($flag==1) 
		{
			$field_array_booking_update = "approved";
			$rIDArr['qc_confirm_mst']=execute_query(bulk_update_sql_statement( "qc_confirm_mst", "id", $field_array_booking_update, $data_array_confirm_update, $confirm_id_arr));
			if($rIDArr['qc_confirm_mst']) $flag=1; else $flag=0; 
		}

		if($flag==1) 
		{
			$field_array_up="APPROVED*APPROVED_SEQU_BY*APPROVED_BY"; 
			$rIDArr['qc_mst']=execute_query(bulk_update_sql_statement( "qc_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr ));
			if($rIDArr['qc_mst']) $flag=1; else $flag=0; 
		}
	
		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=45 and mst_id in ($booking_ids)";
			$rIDArr['approval_history']=execute_query($query,1);
			if($rIDArr['approval_history']) $flag=1; else $flag=0;
		}
		 
		if($flag==1)
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,APPROVED";
			$rIDArr['approval_history']=sql_insert("approval_history",$field_array,$app_history_data_array,0);
			if($rIDArr['approval_history']) $flag=1; else $flag=0;
		}

		//-------------------------

		

		if(count($approved_no_array)>0)
		{
			$approved_string="";
			foreach($approved_no_array as $key=>$value)
			{
				$approved_string.=" WHEN $key THEN '".$value."'";
			}
		 

			$approved_string_mst="CASE qc_no ".$approved_string." END";
			$approved_string_dtls="CASE mst_id ".$approved_string." END";
			$approved_string_confirm="CASE cost_sheet_id ".$approved_string." END";

			$confirm_mst_sql="insert into qc_confirm_mst_history(id, approved_no, confirm_mst_id,cost_sheet_id, lib_item_id, confirm_style, confirm_order_qty, confirm_fob, deal_merchant, ship_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, job_id, approved,  approved_by, approved_date) 
				select '', $approved_string_confirm, id, cost_sheet_id, lib_item_id, confirm_style, confirm_order_qty, confirm_fob,  deal_merchant, ship_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, job_id, approved, approved_by, approved_date from qc_confirm_mst where cost_sheet_id in ($booking_nos)";

			$confirm_dtls_sql="insert into qc_confirm_dtls_history( id, approved_no, confirm_dtls_id, mst_id, cost_sheet_id, item_id, fab_cons_kg, fab_cons_yds, fab_amount, sp_oparation_amount, acc_amount, fright_amount, lab_amount, misce_amount, other_amount, comm_amount, fob_amount, cm_amount,  rmg_ratio, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, fab_cons_mtr, cppm_amount, smv_amount) 
			select '', $approved_string_confirm, id, mst_id, cost_sheet_id, item_id, fab_cons_kg, fab_cons_yds, fab_amount, sp_oparation_amount, acc_amount,  fright_amount, lab_amount, misce_amount, other_amount, comm_amount, fob_amount, cm_amount, rmg_ratio, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, fab_cons_mtr, cppm_amount, smv_amount from qc_confirm_dtls where cost_sheet_id in ($booking_nos)";

			$sql_insert_cons_rate="insert into  qc_cons_rate_dtls_histroy( id,approved_no, cons_rate_dtls_id, mst_id, item_id, type, particular_type_id, formula, consumption, unit, is_calculation, rate, rate_data, value, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, ex_percent)
			select '',$approved_string_dtls, id, mst_id, item_id, type, particular_type_id, formula, consumption, unit, is_calculation, rate, rate_data, value, inserted_by, insert_date, updated_by,update_date, status_active, is_deleted, ex_percent from qc_cons_rate_dtls where mst_id in ($booking_nos) ";
			//echo $sql_insert;die;

			$sql_fabric_dtls="insert into  qc_fabric_dtls_history(id,approved_no, fabric_dtls_id, mst_id,  item_id, body_part, des,value, alw, inserted_by,insert_date, updated_by, update_date, status_active, is_deleted, uniq_id)
			select '', $approved_string_dtls, id, mst_id, item_id, body_part, des, value, alw, inserted_by, insert_date, updated_by, update_date, status_active,  is_deleted, uniq_id from qc_fabric_dtls where mst_id in ($booking_nos)";
			//echo $sql_fabric_dtls;die;
			//-------------qc_item_cost_summary_his---------------------------------------------

			$sql_item_cost_dtls="insert into qc_item_cost_summary_his(id, approved_no, item_sum_id, mst_id, item_id, fabric_cost, sp_operation_cost,accessories_cost, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost,fob_pcs, inserted_by, insert_date, updated_by, update_date, status_active,is_deleted, rmg_ratio, cpm)
				select '', $approved_string_dtls, id, mst_id, item_id, fabric_cost, sp_operation_cost, accessories_cost, smv, efficiency, cm_cost,frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, fob_pcs, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, rmg_ratio, cpm from qc_item_cost_summary where mst_id in ($booking_nos)";
			//echo $sql_item_cost_dtls;die;

			//--------------------------------------qc_meeting_mst_history---------------------------------------------------------------------------
			$sql_meeting_mst="insert into qc_meeting_mst_history(id, approved_no, metting_mst_id, mst_id, meeting_no, buyer_agent_id, location_id, meeting_date, meeting_time, remarks, inserted_by, insert_date, updated_by,  update_date, status_active, is_deleted)
				select '', $approved_string_dtls, id, mst_id, meeting_no, buyer_agent_id, location_id, meeting_date, meeting_time, remarks, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from qc_meeting_mst where  mst_id in ($booking_nos)";
				//echo $sql_meeting_mst;die;

			//----------------------------------qc_mst_history----------------------------------------
			$sql_qc_mst="insert into qc_mst_history( id, approved_no, qc_mst_id, cost_sheet_id, cost_sheet_no, temp_id, style_des, buyer_id, cons_basis, season_id, style_ref, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, lib_item_id, pre_cost_sheet_id, revise_no, option_id, buyer_remarks, option_remarks, meeting_no, qc_no, uom, approved, approved_by, approved_date, from_client)
				select '', $approved_string_mst, id, cost_sheet_id, cost_sheet_no, temp_id, style_des, buyer_id, cons_basis, season_id, style_ref, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, inserted_by, insert_date, updated_by, update_date,  status_active, is_deleted, lib_item_id, pre_cost_sheet_id, revise_no, option_id, buyer_remarks, option_remarks, meeting_no, qc_no, uom, approved, approved_by, approved_date, from_client from qc_mst where  qc_no in ($booking_nos)";
				//echo $sql_qc_mst;die;

			//-------------------------------------qc_tot_cost_summary_history-------------------------------------------
			$sql_tot_cost="insert into qc_tot_cost_summary_history( id, approved_no, tot_sum_id, mst_id, buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted,  tot_rmg_ratio)
			select '', $approved_string_dtls, id, mst_id, buyer_agent_id,location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost,  tot_cost, tot_fob_cost, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, tot_rmg_ratio from qc_tot_cost_summary where mst_id in ($booking_nos)";
			//	echo $sql_tot_cost;die;

			if(count($confirm_mst_sql)>0)
			{
				if($flag==1)
				{
					$rIDArr['qc_confirm_mst_history']=execute_query($confirm_mst_sql,1);
					if($rIDArr['qc_confirm_mst_history']) $flag=1; else $flag=130;
				}
			}

			if(count($confirm_dtls_sql)>0)
			{
				if($flag==1)
				{
					$rIDArr['qc_confirm_dtls_history']=execute_query($confirm_dtls_sql,1);
					if($rIDArr['qc_confirm_dtls_history']) $flag=1; else $flag=120;
				}
			}

			if(count($sql_insert_cons_rate)>0)
			{
				if($flag==1)
				{
					$rIDArr['qc_cons_rate_dtls_histroy']=execute_query($sql_insert_cons_rate,1);
					if($rIDArr['qc_cons_rate_dtls_histroy']) $flag=1; else $flag=110;
				}
			}

			if(count($sql_fabric_dtls)>0)
			{
				if($flag==1)
				{
					$rIDArr['qc_fabric_dtls_history']=execute_query($sql_fabric_dtls,0);
					if($rIDArr['qc_fabric_dtls_history']) $flag=1; else $flag=100;
				}
			}
		
			if(count($sql_item_cost_dtls)>0)
			{
				if($flag==1)
				{
					$rIDArr['qc_item_cost_summary_his']=execute_query($sql_item_cost_dtls,1);
					if($rIDArr['qc_item_cost_summary_his']) $flag=1; else $flag=90;
				}
			}

			if(count($sql_meeting_mst)>0)
			{
				if($flag==1)
				{
					$rIDArr['qc_meeting_mst_history']=execute_query($sql_meeting_mst,1);
					if($rIDArr['qc_meeting_mst_history']) $flag=1; else $flag=80;
				}
			}

			if(count($sql_qc_mst)>0)
			{
				if($flag==1)
				{
					$rIDArr['qc_mst_history']=execute_query($sql_qc_mst,1);
					if($rIDArr['qc_mst_history']) $flag=1; else $flag=70;
				}
			}

			if(count($sql_tot_cost)>0)
			{
				if($flag==1)
				{
					$rIDArr['qc_tot_cost_summary_history']=execute_query($sql_tot_cost,1);
					if($rIDArr['qc_tot_cost_summary_history']) $flag=1; else $flag=60;
				}
			}
		}


 

		 // print_r($rIDArr);
		 //echo $confirm_mst_sql;
		 //oci_rollback($con);die;


		if($flag==1) $msg='19'; else $msg='21';



	}
	elseif($approval_type == 1)
	{
		//echo "10**".$booking_ids."**".$approval_ids;die;
		$booking_ids_all=explode(",",$booking_ids);

		$booking_ids=''; $app_ids='';

		foreach($booking_ids_all as $value)
		{
			$data = explode('**',$value);
			$booking_id=$data[0];
			$app_id=$data[1];
			$confirm_id=$data[2];

			if($booking_ids=='') $booking_ids=$booking_id; else $booking_ids.=",".$booking_id;
			if($app_ids=='') $app_ids=$app_id; else $app_ids.=",".$app_id;
			if($confirm_ids=='') $confirm_ids=$confirm_id; else $confirm_ids.=",".$confirm_id;
		}
		
		$flag=1;

		if($flag == 1){
			$rIDArr['qc_mst'] = sql_multirow_update("qc_mst","approved*APPROVED_SEQU_BY",'2*0',"id",$booking_ids,0);
			if($rIDArr['qc_mst']) $flag=1; else $flag=0;
		}


		if($flag == 1){
			$rIDArr['qc_confirm_mst']=sql_multirow_update("qc_confirm_mst","approved*ready_to_approve",'2*2',"id",$confirm_ids,0);
			if($rIDArr['qc_confirm_mst']) $flag=1; else $flag=0;
		}


		if($flag == 1){
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=45 and current_approval_status=1 and mst_id in ($confirm_ids)";
			$rIDArr['approval_history']=execute_query($query,1);
			if($rIDArr['approval_history']) $flag=1; else $flag=0;
		}

				
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=45 and mst_id in ($booking_ids)";
			$rIDArr['approval_mst']=execute_query($query,1); 
			if($rIDArr['approval_mst']) $flag=1; else $flag=0; 
		}

		$response=$booking_ids;
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
	
	disconnect($con);
	die;
}

if($action=="confirmStyle_popup")
{
	echo load_html_head_contents("Confirm Style PopUp","../../", 1, 1, '','1','');
	extract($_REQUEST);
	$permission=$_SESSION['page_permission'];
	//echo $data;
	$exdata=explode('__',$data);
	$qc_no=$exdata[0];
	$updateid=$exdata[1];
	$user_id=$_SESSION['logic_erp']['user_id'];
	$user_level=$_SESSION['logic_erp']["user_level"];
	$sql_data=sql_select("Select cost_sheet_no, buyer_id, season_id, department_id, temp_id, lib_item_id, style_ref, offer_qty, revise_no, option_id, delivery_date, uom from qc_mst where qc_no='$qc_no' ");
	
	$uom=$sql_data[0][csf('uom')];
	
	$lib_temp_arr=return_library_array("select id, item_name from lib_qc_template","id","item_name");
	if($db_type==0) $concat_cond="group_concat(lib_item_id)";
	else if($db_type==2) $concat_cond="listagg(cast(lib_item_id as varchar2(4000)),',') within group (order by lib_item_id)";
	else $concat_cond="";
	$sql_tmp="select temp_id, $concat_cond as lib_item_id from qc_template where status_active=1 and is_deleted=0 group by temp_id order by temp_id ASC";
	$sql_tmp_res=sql_select($sql_tmp);
	//print_r($sql_tmp_res);die;
	$template_name_arr=array();
	foreach($sql_tmp_res as $row)
	{
		$lib_temp_id='';
		
		$ex_temp_id=explode(',',$sql_data[0][csf('lib_item_id')]);
		foreach($ex_temp_id as $lib_id)
		{
			if($lib_temp_id=="") $lib_temp_id=$lib_temp_arr[$lib_id]; else $lib_temp_id.=','.$lib_temp_arr[$lib_id];
		}
		$template_name_arr[$sql_data[0][csf('temp_id')]]=$lib_temp_id;
	}
	$gmt_type_arr=array(1=>'Pcs',2=>'Set');
	$gmt_itm_count=count(explode(',',$template_name_arr[$sql_data[0][csf('temp_id')]]));
	$selected_gmt_type=0;
	if($gmt_itm_count>1) $selected_gmt_type=2; else $selected_gmt_type=1;
	
	$sql_summ=sql_select("select no_of_pack, tot_fob_cost from qc_tot_cost_summary where mst_id='$qc_no' and status_active=1 and is_deleted=0");
	//$sql_cons_rate="select id, item_id, type, particular_type_id, consumption, unit, is_calculation, rate, rate_data, value from qc_cons_rate_dtls where mst_id='$data[0]' and status_active=1 and is_deleted=0 order by id asc";
	$sql_cons=sql_select("select item_id, sum(CASE WHEN particular_type_id in (1,20,4,6,7,998) THEN consumption ELSE 0 END) as qty_kg, sum(CASE WHEN particular_type_id=999 THEN consumption ELSE 0 END) as qty_yds from qc_cons_rate_dtls where mst_id='$qc_no' and type=1 group by item_id");//type ='1' and
	$item_wise_cons_arr=array();
	foreach($sql_cons as $cRow)
	{
		if($uom==12)
		{
			$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_kg']=$cRow[csf("qty_kg")];
			$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_mtr']=0;
			$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_yds']=$cRow[csf("qty_yds")];
		}
		else if($uom==23)
		{
			$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_kg']=0;
			$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_mtr']=$cRow[csf("qty_kg")];
			$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_yds']=$cRow[csf("qty_yds")];
		}
		else if($uom==27)
		{
			$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_kg']=0;
			$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_mtr']=0;
			$item_wise_cons_arr[$cRow[csf("item_id")]]['qty_yds']=$cRow[csf("qty_kg")]+$cRow[csf("qty_yds")];
		}
	}
	//$sql_result_summ=sql_select($sql_summ);
	//print_r($item_wise_cons_arr);
	

	$team_dtls_sql=sql_select("select a.user_tag_id from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and b.user_tag_id='$user_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.user_tag_id");
	if(count($team_dtls_sql)==1) $team_dtls_arr[$user_id]=$team_dtls_sql[0][csf('user_tag_id')];
	else $team_dtls_arr[$user_id]='';
	//print_r($team_dtls_arr);
	$disable="";
	if($user_level==2 || $team_dtls_arr[$user_id]!="") $disable=""; else $disable="disabled";
	
	$isteam_leader=return_field_value("user_tag_id","lib_marketing_team","user_tag_id='$user_id' and is_deleted=0 and status_active=1","user_tag_id");
	//echo $user_level.'-'.$isteam_leader;
	if($user_level==2 || $isteam_leader!='') $admin_or_leader="";  else $admin_or_leader="none";
	
	?>
    <script>
		var permission='<? echo $permission; ?>'; 
		
		
		function js_set_value( )
		{
			parent.emailwindow.hide();
		}
		
		function fnc_openJobPopup()
		{
			var cbo_approved_status=$('#cbo_approved_status').val();
			if(cbo_approved_status==1)
			{
				alert("This Option (QC) is Approved.");
				release_freezing();
				return;
			}
			var data=document.getElementById('cbo_buyer_id').value;
			page_link='quick_costing_controller.php?action=style_tag_popup&data='+data;
			emailwindow=dhtmlmodal.open('EmailBox','iframe',page_link,'Job and Style Popup', 'width=780px, height=380px, center=1, resize=0, scrolling=0','../../');
			emailwindow.onclose=function()
			{
				var theemail=this.contentDoc.getElementById("hidd_job_data");  
				//alert (theemail.value);return;
				var job_val=theemail.value.split("_");
				if (theemail.value!="")
				{
					$("#txt_job_id").val(job_val[0]);
					$("#txt_job_style").val(job_val[1]);
					$("#txt_style_job").val(job_val[2]);
					fnc_bom_data_load();
				}
			}
		}
		
		function fnc_bom_data_load()
		{
			var job_no=$("#txt_job_style").val();
			if(job_no!="")
			{
				var str_data=return_global_ajax_value( job_no, 'budgete_cost_validate', '', 'quick_costing_controller');
				
				var spdata=str_data.split("##");
				var fab_cons_kg=spdata[0]; var fab_cons_mtr=spdata[1]; var fab_cons_yds=spdata[2]; var fab_amount=spdata[3]; var sp_oparation_amount=spdata[4]; var acc_amount=spdata[5]; var fright_amount=spdata[6]; var lab_amount=spdata[7]; var misce_amount=spdata[8]; var other_amount=spdata[9]; var comm_amount=spdata[10]; var fob_amount=spdata[11]; var cm_amount=spdata[12]; var rmg_ratio=spdata[13];
				
				$("#txtFabConkg_bom").val(fab_cons_kg);
				$("#txtFabConmtr_bom").val(fab_cons_mtr);
				$("#txtFabConyds_bom").val(fab_cons_yds);
				$("#txtFabCst_bom").val(fab_amount);
				$("#txtSpOpa_bom").val(sp_oparation_amount);
				$("#txtAcc_bom").val(acc_amount);
				$("#txtFrightCst_bom").val(fright_amount);
				$("#txtLabCst_bom").val(lab_amount);
				$("#txtMiscCst_bom").val(misce_amount);
				$("#txtOtherCst_bom").val(other_amount);
				$("#txtCommCst_bom").val(comm_amount);
				$("#txtFobDzn_bom").val(fob_amount);
				$("#txtCmCst_bom").val(cm_amount);
				$("#txtPack_bom").val(rmg_ratio);
			}
		}
		
		function fnc_total_calculate()
		{
			var temp_id=$('#txtItem_id').val();
			var split_tmep_id=temp_id.split(',');
			var ab=0;
			var qc_fab_kg=0; var qc_fab_mtr=0; var qc_fab_yds=0; var qc_fab_amt=0; var qc_sp_amt=0; var qc_acc_amt=0; var qc_fri_amt=0; var qc_lab_amt=0; var qc_misce_amt=0; var qc_other_amt=0; var qc_comm_amt=0; var qc_fob_amt=0; var qc_cpm_amt=0; var qc_smv_amt=0; var qc_cm_amt=0; var qc_rmg_amt=0;
			for(j=1; j<=split_tmep_id.length; j++)
			{
				var item_tot_amount=0; var item_tot_cm=0;
				var itm_id=trim(split_tmep_id[ab]);
				
				qc_fab_kg+=$("#txtFabConkg_"+itm_id).val()*1;
				qc_fab_mtr+=$("#txtFabConmtr_"+itm_id).val()*1;
				qc_fab_yds+=$("#txtFabConyds_"+itm_id).val()*1;
				qc_fab_amt+=$("#txtFabCst_"+itm_id).val()*1;
				qc_sp_amt+=$("#txtSpOpa_"+itm_id).val()*1;
				qc_acc_amt+=$("#txtAcc_"+itm_id).val()*1;
				qc_fri_amt+=$("#txtFrightCst_"+itm_id).val()*1;
				qc_lab_amt+=$("#txtLabCst_"+itm_id).val()*1;
				qc_misce_amt+=$("#txtMiscCst_"+itm_id).val()*1;
				qc_other_amt+=$("#txtOtherCst_"+itm_id).val()*1;
				qc_comm_amt+=$("#txtCommCst_"+itm_id).val()*1;
				qc_fob_amt+=$("#txtFobDzn_"+itm_id).val()*1;
				
				qc_cpm_amt+=$("#txtCpm_"+itm_id).val()*1;
				qc_smv_amt+=$("#txtSmv_"+itm_id).val()*1;
				
				qc_cm_amt+=$("#txtCmCst_"+itm_id).val()*1;
				qc_rmg_amt+=$("#txtPack_"+itm_id).val()*1;
				
				item_tot_amount=($("#txtFabCst_"+itm_id).val()*1)+($("#txtSpOpa_"+itm_id).val()*1)+($("#txtAcc_"+itm_id).val()*1)+($("#txtFrightCst_"+itm_id).val()*1)+($("#txtLabCst_"+itm_id).val()*1)+($("#txtMiscCst_"+itm_id).val()*1)+($("#txtOtherCst_"+itm_id).val()*1)+($("#txtCommCst_"+itm_id).val()*1);
				
				item_tot_cm=($("#txtFobDzn_"+itm_id).val()*1)-item_tot_amount;
				
				$("#txtCmCst_"+itm_id).val( number_format(item_tot_cm,2,'.',''))
				
				ab++;
			}
			
			$("#txtFabConkg_qc").val( number_format(qc_fab_kg,2,'.','') );
			$("#txtFabConmtr_qc").val( number_format(qc_fab_mtr,2,'.','') );
			$("#txtFabConyds_qc").val( number_format(qc_fab_yds,2,'.','') );
			$("#txtFabCst_qc").val( number_format(qc_fab_amt,2,'.','') );
			$("#txtSpOpa_qc").val( number_format(qc_sp_amt,2,'.','') );
			$("#txtAcc_qc").val( number_format(qc_acc_amt,2,'.','') );
			$("#txtFrightCst_qc").val( number_format(qc_fri_amt,2,'.','') );
			$("#txtLabCst_qc").val( number_format(qc_lab_amt,2,'.','') );
			$("#txtMiscCst_qc").val( number_format(qc_misce_amt,2,'.','') );
			$("#txtOtherCst_qc").val( number_format(qc_other_amt,2,'.','') );
			$("#txtCommCst_qc").val( number_format(qc_comm_amt,2,'.','') );
			$("#txtFobDzn_qc").val( number_format(qc_fob_amt,2,'.','') );
			
			$("#txtCpm_qc").val( number_format(qc_cpm_amt,4,'.','') );
			$("#txtSmv_qc").val( number_format(qc_smv_amt,2,'.','') );
			
			$("#txtPack_qc").val( number_format(qc_rmg_amt,2,'.','') );
			
			var total_amount=qc_fab_amt+qc_sp_amt+qc_acc_amt+qc_fri_amt+qc_lab_amt+qc_misce_amt+qc_other_amt+qc_comm_amt;
			var cal_cm=qc_fob_amt-total_amount;
			$("#txtCmCst_qc").val( number_format(cal_cm,2,'.','') );
		}
		
		function fnc_select()
		{
			$(document).ready(function() {
				$("input:text").focus(function() { $(this).select(); } );
			});
		}
		
		function fnc_confirm()
		{
			var job_no=$('#txt_job_style').val();
			
			if(job_no=="")
			{
				alert("Please Add Job no with this option.");
				return;
			}
			else
			{
				fnc_confirm_entry(3);
			}
		}
		
		function fnc_cppm_cal(item_id)
		{
			var txtSmv=$("#txtSmv_"+item_id).val()*1;
			var txtCm=$("#txtCmCst_"+item_id).val()*1;
			
			var cppm=( txtCm/txtSmv);
			var cppm_nf=number_format((cppm/12),4,'.','');
			if(cppm_nf=="nan") cppm_nf=0;
			$("#txtCpm_"+item_id).val( cppm_nf );
			
			fnc_total_calculate();
		}
		
	</script>
	</head>
	<body>
    <div id="confirm_style_details" align="center">  
    <div style="display:none"><? echo load_freeze_divs ("../../../",$permission);  ?></div>       
        <form name="confirmStyle_1" id="confirmStyle_1" autocomplete="off">
        	<table width="850">
                <tr>
                    <td width="90"><strong>Buyer</strong><input style="width:40px;" type="hidden" class="text_boxes" name="txt_costSheet_id" id="txt_costSheet_id" value="<? echo $qc_no; ?>" /><input style="width:40px;" type="hidden" class="text_boxes" name="txtConfirm_id" id="txtConfirm_id" value="" /><input style="width:40px;" type="hidden" class="text_boxes" name="txtItem_id" id="txtItem_id" value="<? echo $sql_data[0][csf('lib_item_id')]; ?>" /></td>
                    <td width="120"><? echo create_drop_down( "cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-Select Buyer-", $sql_data[0][csf('buyer_id')], "load_drop_down( 'quick_costing_controller', this.value, 'load_drop_down_season_conf', 'season_conf_td'); load_drop_down( 'quick_costing_controller',this.value, 'load_drop_down_sub_depConf', 'subConf_td' );",1 ); ?></td>
                    <td width="90">&nbsp;&nbsp;<strong>Season</strong></td>
                    <td width="100" id="season_conf_td"><? echo create_drop_down( "cbo_season_id", 100, "select id, season_name from lib_buyer_season where buyer_id='".$sql_data[0][csf('buyer_id')]."' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-Select Season-",$sql_data[0][csf('season_id')], "",1 ); ?></td>
                    <td width="90">&nbsp;&nbsp;<strong>Department</strong></td>
                    <td width="100" id="subConf_td"><? echo create_drop_down( "cbo_subDept_id", 100, "select id, sub_department_name from lib_pro_sub_deparatment where buyer_id='".$sql_data[0][csf('buyer_id')]."' and status_active=1 and is_deleted=0 order by sub_department_name","id,sub_department_name", 1, "-- Select Dept--",$sql_data[0][csf('department_id')], "",1 ); ?></td>
                    <td colspan="3" align="center">&nbsp;</td>
                </tr>
                <tr>
                    <td><strong>Style Type</strong></td>
                    <td><? echo create_drop_down( "cbo_style_type", 120, $template_name_arr,"", 1, "-Select-", $selected, "",1 ); ?> </td>
                    <td>&nbsp;&nbsp;<strong>Gmts Type</strong></td>
                    <td><? echo create_drop_down( "cbo_gmts_type", 100, $gmt_type_arr,'', 1, "-Gmts Type-",$selected_gmt_type, "" ,1); ?></td>
                    
                    <td>&nbsp;&nbsp;<strong>Revise No</strong></td>
                    <td><? echo create_drop_down( "cbo_revise", 100, "select revise_no from qc_mst where cost_sheet_no='".$sql_data[0][csf('cost_sheet_no')]."' and status_active=1 and is_deleted=0 order by cost_sheet_no Desc","revise_no,revise_no", 0, "-Select-", $sql_data[0][csf('revise_no')], "",1 ); ?> </td>
                    <td width="90">&nbsp;&nbsp;<strong>Option</strong></td>
                    <td width="100"><? echo create_drop_down( "cbo_option", 100, "select option_id from qc_mst where cost_sheet_no='".$sql_data[0][csf('cost_sheet_no')]."' and status_active=1 and is_deleted=0 order by cost_sheet_no Desc","option_id,option_id", 0, "-Select-",$sql_data[0][csf('option_id')], "" ,1); ?></td>
                </tr>
                <tr>
                    <td><strong>Estimate Style</strong></td>
                    <td><input style="width:110px;" type="text" class="text_boxes" name="txt_style_ref" id="txt_style_ref" value="<? echo $sql_data[0][csf('style_ref')]; ?>" disabled /></td>
                    <td>&nbsp;&nbsp;<strong>Cofirm Style</strong></td>
                    <td><input style="width:90px;" type="text" class="text_boxes" name="txt_confirm_style" id="txt_confirm_style" value="<? echo $sql_data[0][csf('style_ref')]; ?>" <? echo $disable; ?> /></td>
                    <td>&nbsp;&nbsp;<strong>Order Qty.</strong></td>
                    <td><input style="width:90px;" type="text" class="text_boxes_numeric" name="txt_order_qty" id="txt_order_qty" value="<? echo $sql_data[0][csf('offer_qty')]; ?>" <? echo $disable; ?> /></td>
                    <td>&nbsp;&nbsp;<strong>Cofirm FOB</strong></td>
                    <td><input style="width:90px;" type="text" class="text_boxes_numeric" name="txt_confirm_fob" id="txt_confirm_fob" value="<? echo $sql_summ[0][csf('tot_fob_cost')]; ?>" <? echo $disable; ?> /></td>
                </tr>
                <tr>
                	<td><strong>Ship Date</strong></td>
                    <td><input style="width:110px;" type="text" class="datepicker" name="txt_ship_date" id="txt_ship_date" value="<? echo change_date_format($sql_data[0][csf('delivery_date')]); ?>" readonly <? echo $disable; ?>/></td>
                    <td>&nbsp;&nbsp;<strong>Job No</strong></td>
                    <td><input style="width:90px;" type="text" class="text_boxes" name="txt_job_style" id="txt_job_style" placeholder="Browse Job" onDblClick="fnc_openJobPopup();" readonly /><input style="width:40px;" type="hidden" class="text_boxes" name="txt_job_id" id="txt_job_id" /></td>
                    <td>&nbsp;&nbsp;<strong>Master Style</strong></td>
                    <td><input style="width:90px;" type="text" class="text_boxes" name="txt_style_job" id="txt_style_job" disabled /></td>
                    <td>&nbsp;&nbsp;<strong>Approved</strong></td>
                	<td><? echo create_drop_down( "cbo_approved_status", 100, $yes_no,"", 0, "", 2, "",1,"" ); ?></td> 
                    <td>&nbsp;</td>
                </tr>
            </table>
            <table width="400" cellspacing="0" class="" border="0">
                <tr>
                    <td align="center" width="100%" class="button_container">
						<input type="button" class="formbutton" value="Close" style="width:80px" onClick="js_set_value();"/>
                    </td> 
                </tr>
            </table>
            <div id="confirm_data_div">
            <table width="925" cellspacing="0" border="1" rules="all" class="rpt_table">
        	<thead>
            	<th width="80">Item</th>
                <th width="50">Fab. Cons. Kg</th>
                <th width="50">Fab. Cons. Mtr</th>
                <th width="50">Fab. Cons. Yds</th>
                <th width="50">Fab. Amount</th>
                <th width="50">Special Opera.</th>
                <th width="50">Access.</th>
                <th width="50">Frieght Cost</th>
                <th width="50">Lab - Test</th>
                <th width="50">Misce.</th>
                <th width="50">Other Cost</th>
                <th width="50">Commis.</th>
                <th width="50">FOB ($/DZN)</th>
                <th width="50" title="((CPM*100)/Efficiency)">CPPM</th>
                <th width="50">SMV</th>
                <th width="50">CM</th>
                <th>RMG Qty(Pcs)</th>
            </thead>
			<?
            $sql_item_summ="select item_id, fabric_cost, sp_operation_cost, accessories_cost, cpm, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, fob_pcs from qc_item_cost_summary where mst_id='$qc_no' and status_active=1 and is_deleted=0";
            $sql_result_item_summ=sql_select($sql_item_summ); $z=1;
            foreach($sql_result_item_summ as $rowItemSumm)
            {
				if ($z%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if ($z%2==0) $bgcolorN="#E9F50F"; else $bgcolorN="#D078F6";
				
				$cppm=0;
				if($rowItemSumm[csf("efficiency")]!=0 && $rowItemSumm[csf("cpm")]!=0) $cppm=(($rowItemSumm[csf("cpm")]*100)/$rowItemSumm[csf("efficiency")]);
				
				if($cppm=="nan") $cppm=0;
                ?>
                <tr id="trVal_<? echo $z; ?>" bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $lib_temp_arr[$rowItemSumm[csf("item_id")]]; ?></td>
                    <td align="right"><? echo number_format($item_wise_cons_arr[$rowItemSumm[csf("item_id")]]['qty_kg'],4); ?></td>
                    <td align="right"><? echo number_format($item_wise_cons_arr[$rowItemSumm[csf("item_id")]]['qty_mtr'],4); ?></td>
                    <td align="right"><? echo number_format($item_wise_cons_arr[$rowItemSumm[csf("item_id")]]['qty_yds'],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("fabric_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("sp_operation_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("accessories_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("frieght_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("lab_test_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("miscellaneous_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("other_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("commission_cost")],4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("fob_pcs")],4); ?></td>
                    
                    <td align="right" title="((CPM*100)/Efficiency)"><? if($cppm==0) echo "0"; else echo number_format($cppm,4); ?></td>
                    <td align="right"><? echo number_format($rowItemSumm[csf("smv")],4); ?></td>
                    
                    <td align="right"><? echo number_format($rowItemSumm[csf("cm_cost")],4); ?></td>
                    <td align="right">&nbsp;<? //echo number_format($rowItemSumm[csf("sp_operation_cost")],4); ?></td>
                </tr>
                <tr id="tr_<? echo $z; ?>" bgcolor="<? echo $bgcolorN; ?>">
                    <td>QC BOM Limit<input style="width:40px;" type="hidden" name="txtitemid_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtitemid_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo $rowItemSumm[csf("item_id")]; ?>" /><input style="width:40px;" type="hidden" name="txtdtlsupid_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtdtlsupid_<? echo $rowItemSumm[csf("item_id")]; ?>" value="" /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtFabConkg_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtFabConkg_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($item_wise_cons_arr[$rowItemSumm[csf("item_id")]]['qty_kg'],4); ?>" onChange="fnc_total_calculate();" <? echo $disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtFabConmtr_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtFabConmtr_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($item_wise_cons_arr[$rowItemSumm[csf("item_id")]]['qty_mtr'],4); ?>" onChange="fnc_total_calculate();" <? echo $disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtFabConyds_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtFabConyds_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($item_wise_cons_arr[$rowItemSumm[csf("item_id")]]['qty_yds'],4); ?>" onChange="fnc_total_calculate();" <? echo $disable; ?>/></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtFabCst_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtFabCst_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("fabric_cost")],4); ?>" onChange="fnc_total_calculate();" <? echo $disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtSpOpa_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtSpOpa_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("sp_operation_cost")],4); ?>" onChange="fnc_total_calculate();" <? echo $disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtAcc_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtAcc_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("accessories_cost")],4); ?>" onChange="fnc_total_calculate();" <? echo $disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtFrightCst_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtFrightCst_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("frieght_cost")],4); ?>" onChange="fnc_total_calculate();" <? echo $disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtLabCst_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtLabCst_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("lab_test_cost")],4); ?>" onChange="fnc_total_calculate();" <? echo $disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtMiscCst_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtMiscCst_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("miscellaneous_cost")],4); ?>" onChange="fnc_total_calculate();" <? echo $disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtOtherCst_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtOtherCst_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("other_cost")],4); ?>" onChange="fnc_total_calculate();" <? echo $disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtCommCst_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtCommCst_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("commission_cost")],4); ?>" onChange="fnc_total_calculate();" <? echo $disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtFobDzn_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtFobDzn_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("fob_pcs")],4); ?>" onChange="fnc_total_calculate();" <? echo $disable; ?> /></td>
                    
                    <td title="((CPM*100)/Efficiency)"><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtCpm_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtCpm_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? if($cppm==0) echo "0"; else echo number_format($cppm,4); ?>" onChange="fnc_total_calculate();" disabled <? //echo $disable; ?> /></td>
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtSmv_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtSmv_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("smv")],4); ?>" onChange="fnc_total_calculate();" onBlur="fnc_cppm_cal(<? echo $rowItemSumm[csf("item_id")]; ?>);" <? echo $disable; ?> /></td>
                    
                    <td><input style="width:38px;" type="text" class="text_boxes_numeric" name="txtCmCst_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtCmCst_<? echo $rowItemSumm[csf("item_id")]; ?>" value="<? echo number_format($rowItemSumm[csf("cm_cost")],4); ?>" onChange="fnc_total_calculate();" <? echo $disable; ?> /></td>
                    <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtPack_<? echo $rowItemSumm[csf("item_id")]; ?>" id="txtPack_<? echo $rowItemSumm[csf("item_id")]; ?>" value="" onChange="fnc_total_calculate();" <? echo $disable; ?> />&nbsp;</td>
                </tr>
                <?
				$z++;
            }
			$sql="select fab_cons_kg, fab_cons_yds, fab_amount, sp_oparation_amount, acc_amount, fright_amount, lab_amount, misce_amount, other_amount, comm_amount, fob_amount, cm_amount, rmg_ratio from qc_confirm_dtls where cost_sheet_id='$cost_sheet_id' and status_active=1 and is_deleted=0";
			$dataArr=sql_select($sql);
            ?>
        </table>
        
        <table width="925" cellspacing="0" border="1" rules="all" class="rpt_table">
        	<tr id="tr_qc" bgcolor="#CCFFCC">
                <td width="80"><font color="#0000FF">QC Limit Total</font></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFabConkg_qc" id="txtFabConkg_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFabConmtr_qc" id="txtFabConmtr_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFabConyds_qc" id="txtFabConyds_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFabCst_qc" id="txtFabCst_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtSpOpa_qc" id="txtSpOpa_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtAcc_qc" id="txtAcc_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFrightCst_qc" id="txtFrightCst_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtLabCst_qc" id="txtLabCst_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtMiscCst_qc" id="txtMiscCst_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtOtherCst_qc" id="txtOtherCst_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtCommCst_qc" id="txtCommCst_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFobDzn_qc" id="txtFobDzn_qc" value="" disabled /></td>
                
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtCpm_qc" id="txtCpm_qc" value="" disabled /></td>
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtSmv_qc" id="txtSmv_qc" value="" disabled /></td>
                
                <td width="50"><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtCmCst_qc" id="txtCmCst_qc" value="" disabled /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtPack_qc" id="txtPack_qc" value="" disabled />&nbsp;</td>
            </tr>
        	<tr id="tr_bom" bgcolor="#CCCCCC">
                <td width="80"><font color="#FF0000">Current BOM</font></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFabConkg_bom" id="txtFabConkg_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFabConmtr_bom" id="txtFabConmtr_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFabConyds_bom" id="txtFabConyds_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFabCst_bom" id="txtFabCst_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtSpOpa_bom" id="txtSpOpa_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtAcc_bom" id="txtAcc_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFrightCst_bom" id="txtFrightCst_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtLabCst_bom" id="txtLabCst_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtMiscCst_bom" id="txtMiscCst_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtOtherCst_bom" id="txtOtherCst_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtCommCst_bom" id="txtCommCst_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtFobDzn_bom" id="txtFobDzn_bom" value="" readonly /></td>
                
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtCpm_bom" id="txtCpm_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtSmv_bom" id="txtSmv_bom" value="" readonly /></td>
                
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtCmCst_bom" id="txtCmCst_bom" value="" readonly /></td>
                <td><input style="width:35px;" type="text" class="text_boxes_numeric" name="txtPack_bom" id="txtPack_bom" value="" readonly />&nbsp;</td>
            </tr>
        </table>
            </div>
        </form>
	</div>
    </body> 
    <script>get_php_form_data($('#txt_costSheet_id').val()+'__'+$('#txtItem_id').val(),'populate_confirm_style_form_data','quick_costing_controller'); fnc_bom_data_load(); fnc_total_calculate(); fnc_select();</script>          
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();	
}

if($action=="img")
{
	echo load_html_head_contents("Image View", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:600px; margin-left:5px">
		<div style="width:100%; word-wrap:break-word" id="scroll_body">
             <table border="0" rules="all" width="100%" cellpadding="2" cellspacing="2">
             	<tr>
					<?
					$i=0;
                    $sql="select image_location from common_photo_library where master_tble_id='$id' and form_name='knit_order_entry' and file_type=1";

                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
						$i++;
                    ?>
                    <!--<td align="center"><? echo $row[csf('image_location')];?></td>-->
                    	<td align="center"><img width="300px" height="180px" src="../../<? echo $row[csf('image_location')];?>" /></td>
                    <?
						if($i%2==0) echo "</tr><tr>";
                    }
                    ?>
                </tr>
            </table>
        </div>
	</fieldset>
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
		$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_id and b.page_id=$menu_id and valid=1 and a.id!=$user_id  and b.is_deleted=0  and b.page_id=$menu_id order by b.sequence_no";
			//echo $sql;
		$arr=array (2=>$custom_designation,3=>$Department);
		echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
		?>
	</form>
	<script language="javascript" type="text/javascript">
	  setFilterGrid("tbl_style_ref");
	</script>
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
		$sql_cause="select MAX(id) as id from fabric_booking_approval_cause where entry_form=28 and user_id='$user_id' and booking_id='$wo_id' and status_active=1 and is_deleted=0 and NOT_APPROVAL_CAUSE is not null";
		//echo $sql_cause; die;page_id='$menu_id' and 
		$nameArray_cause=sql_select($sql_cause);
		if(count($nameArray_cause)>0){
			foreach($nameArray_cause as $row)
			{
				$app_cause1=return_field_value("NOT_APPROVAL_CAUSE", "fabric_booking_approval_cause", "id='".$row[csf("id")]."' and status_active=1 and is_deleted=0 and NOT_APPROVAL_CAUSE is not null ");
				$app_cause = str_replace(array("\r", "\n"), ' ', $app_cause1);
			}
		}
		else $app_cause = '';
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
				http.open("POST","quick_costing_approval_woven_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange=fnc_appv_entry_Reply_info;
			}
		}

		function fnc_appv_entry_Reply_info()
		{
			if(http.readyState == 4)
			{
				var reponse=trim(http.responseText).split('**');
				show_msg(reponse[0]);
				//set_button_status(1, permission, 'fnc_appv_entry',1);
				release_freezing();
				//generate_worder_mail(reponse[2],reponse[3],reponse[4],reponse[5]);
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
			http.open("POST","quick_costing_approval_woven_controller.php",true);
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
		
		
	function mail_send(){
		
	   if (confirm('Mail Send?')==false)
		{
			return;
		}
		else
		{
			get_php_form_data('<?=$data;?>','quick_approvail_mail','../../auto_mail/woven/quick_costing_app_mail');
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
                        <input style="width:80px;" type="button" id="copy_btn" class="formbutton" value="Mail Send" onClick="mail_send();" />		

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

if ($action=="unappcause_popup")
{
	echo load_html_head_contents("Un Approval Request", "../../", 1, 1,'','','');
	extract($_REQUEST);
	$wo_id=$data;
	$sql_req="select approval_cause,approval_no from fabric_booking_approval_cause where entry_form=28 and booking_id='$wo_id' and approval_type=2 and status_active=1 and is_deleted=0 order by approval_no ";
	//echo $sql_req;
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
                    	<textarea name="unappv_req" id="unappv_req" class="text_area" style="width:430px; height:100px;"></textarea>
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

if ($action=="save_update_delete_appv_cause")
{
	//$approval_id
	$approval_type=str_replace("'","",$appv_type);
		
	if($approval_type==2)
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

			$approved_no_history=return_field_value("approved_no","approval_history","entry_form=28 and mst_id=$wo_id and approved_by=$user_id");
			$approved_no_cause=return_field_value("approval_no","fabric_booking_approval_cause","entry_form=28 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");
			 //echo "10**".$approved_no_history.'='.$approved_no_cause; die;
			if($approved_no_cause==0){$approved_no_cause="";}
			
			if($approved_no_history=="" && $approved_no_cause=="")
			{
				//echo "insert"; die;

				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,NOT_APPROVAL_CAUSE,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id_mst.",".$page_id.",28,".$user_id.",".$wo_id." ,".$appv_type.",0,".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
				 //echo $rID; die;
				//echo "10**INSERT INTO fabric_booking_approval_cause (".$field_array.") VALUES ".$data_array; die;
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

				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=28 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

				$field_array="user_id*booking_id*approval_type*approval_no*NOT_APPROVAL_CAUSE*updated_by*update_date*status_active*is_deleted";
				$data_array="".$user_id."*".$wo_id."*".$appv_type."*0*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

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
				$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=28 and mst_id=$wo_id and approved_by=$user_id");
				$max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=28 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,NOT_APPROVAL_CAUSE,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id_mst.",".$page_id.",28,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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

					$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=28 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

					$field_array="user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
					$data_array="".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

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
			$unapproved_cause_id=return_field_value("id","fabric_booking_approval_cause","entry_form=28 and user_id=$user_id and booking_id=$wo_id and approval_type=$appv_type and approval_history_id=$approval_id");
			
			$mst_id=return_field_value("id","qc_mst","qc_no=$wo_id and status_active=1 and is_deleted=0");
			$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=45 and mst_id=$mst_id and approved_by=$user_id");
			if($unapproved_cause_id==0){$unapproved_cause_id="";}
			
			if($unapproved_cause_id=="")
			{
				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1);

				$field_array="id, page_id, entry_form, user_id, booking_id, approval_type, approval_no, approval_history_id, NOT_APPROVAL_CAUSE, inserted_by, insert_date, status_active, is_deleted";
				$data_array="(".$id_mst.",".$page_id.",28,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$approval_id.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

				$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
				//echo $rID; 
				 //echo "10**INSERT INTO fabric_booking_approval_cause (".$field_array.") VALUES ".$data_array; die;	

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
				//echo "10**entry_form=7 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id";die;
				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=28 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");

				$field_array="user_id*booking_id*approval_type*approval_no*approval_history_id*NOT_APPROVAL_CAUSE*updated_by*update_date*status_active*is_deleted";
				$data_array="".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$approval_id."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

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
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	?>
        <table width="800" cellpadding="0" cellspacing="0" border="1">
            <tr>
                <td valign="top" align="center"><strong><font size="+2">Subject : Quick Costing &nbsp;<?  if($appvtype==0) echo "Approval Request"; else echo "Un-Approval Request"; ?>&nbsp;Refused</font></strong></td>
            </tr>
            <tr>
                <td valign="top">
                    Dear Mr. <?
								$to="";

								$sql ="SELECT c.team_member_name FROM wo_booking_mst a, wo_po_details_master b, lib_mkt_team_member_info c where b.job_no=a.job_no and b.dealing_marchant=c.id and a.id=$woid";
								$result=sql_select($sql);
								foreach($result as $row)
								{
									if ($to=="")  $to=$row[csf('team_member_name')]; else $to=$to.", ".$row[csf('team_member_name')];
								}
								echo $to;
							?>
                            <br> Your Cost Sheet No. &nbsp;
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
						$user_name=return_field_value("user_name","user_passwd","id=$user");
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

if($action=="populate_cm_compulsory")
{
	$cm_cost_compulsory=return_field_value("cm_cost_compulsory","variable_order_tracking","company_name ='".$data."' and variable_list=22 and is_deleted=0 and status_active=1");
	echo $cm_cost_compulsory;
	exit();
}


if($action=="quick_costing_approval_mail___________")
{

	include('../../auto_mail/setting/mail_setting.php');
    $userSql = "select ID,USER_EMAIL,USER_NAME,USER_FULL_NAME from USER_PASSWD";
    $userSqlRes=sql_select($userSql);
    foreach($userSqlRes as $urserRow){
        $user_maill_arr[$urserRow['ID']] =$urserRow['USER_EMAIL']; 
        $user_name_arr[$urserRow['ID']] =$urserRow['USER_NAME']; 
        $user_full_name_arr[$urserRow['ID']] =$urserRow['USER_FULL_NAME']; 
    }

 
 
    
    //echo $sql;die;
    foreach($sql_dtls as $row){ 

        $toArr = array();
        if($mailId){$toArr[]=str_replace('*',',',$mailId);}

		$team_email_arr = return_library_array("
		select ID,TEAM_MEMBER_EMAIL from LIB_MKT_TEAM_MEMBER_INFO where team_id in(select a.id from LIB_MARKETING_TEAM a,LIB_MKT_TEAM_MEMBER_INFO b where a.id=b.team_id and b.USER_TAG_ID={$row['INSERTED_BY']} and TEAM_MEMBER_EMAIL is not null)  and STATUS_ACTIVE=1 and IS_DELETED=0 and TEAM_MEMBER_EMAIL is not null",'ID','TEAM_MEMBER_EMAIL');
		if(count($team_email_arr)){
			$team_email_str = implode(',',$team_email_arr);
			$toArr[$team_email_str]=$team_email_str;
		}


    
        $user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","entry_form=45 and user_id=$app_user_id and company_id ={$row['IMPORTER_ID']} and is_deleted = 0");

        $elcetronicSql = "SELECT a.BUYER_ID,a.SEQUENCE_NO,a.BYPASS,b.USER_EMAIL,b.ITEM_CATE_ID   from electronic_approval_setup a join user_passwd b on a.user_id=b.id where b.valid=1  AND a.entry_form=27 and a.SEQUENCE_NO>$user_sequence_no and a.company_id={$row['IMPORTER_ID']} order by a.SEQUENCE_NO"; //and a.page_id=2150 and a.entry_form=46
        // echo $elcetronicSql;die;
    
        $elcetronicSqlRes=sql_select($elcetronicSql);
        foreach($elcetronicSqlRes as $rows){
            
            if($rows['ITEM_CATE_ID'] != ''){
                $ITEM_CATE_ID_ARR = explode(',',$rows['ITEM_CATE_ID']);
                foreach($ITEM_CATE_ID_ARR as $item_cat_id){  
                    if($rows['USER_EMAIL']!='' && $item_cat_id == $row['ITEM_CATEGORY_ID']){
                        $toArr[$rows['USER_EMAIL']]=$rows['USER_EMAIL'];
                    } 
                }
                if($rows['BYPASS']==2){break;}
            }
            else{
                if($rows['USER_EMAIL']){$toArr[$rows['USER_EMAIL']]=$rows['USER_EMAIL'];}
                if($rows['BYPASS']==2){break;}
            }

        }
      
        if($row['APPROVED'] ==1){$statusMsg = 'Full Approved';}
        else if($row['APPROVED'] ==3){$statusMsg = 'Partial Approved';}
        else if($row['APPROVED'] == 0){$statusMsg = 'Unapproved';}
        else if($type ==5){$statusMsg = 'Denied';}



        ob_start();	
        ?>
        <b>Dear Concerned,</b>				
        <table rules="all" border="1">
            <tr>
                <td colspan="7"><b>This PI has been <?= $statusMsg; ?>, Please Proceed.</b></td>
                <td colspan="7" align="right"><b><?= $statusMsg; ?> by <?= $user_full_name_arr[$row['APPROVED_BY']] ;?></b></td>
            </tr>
            <tr bgcolor="#CCCCCC">
                <td>SL</td>
                <td>Company</td>
                <td>System ID</td>
                <td>Item Category</td>
                <td>PI Receive Date</td>
                <td>PI No</td>
                <td>PI Value</td>
                <td>Supplier</td>
                <td>Internal File No</td>
                <td>LC/SC</td>
                <td>Pay Term</td>
                <td>Pi For</td>
                <td>Priority</td>
                <td>Remarks</td>
            </tr>
            <?php 
            $i=1;
                if($user_maill_arr[$row['INSERTED_BY']]){$mailArr[$row['INSERTED_BY']]=$user_maill_arr[$row['INSERTED_BY']];}
                if($user_maill_arr[$app_user_id]){$mailArr[$app_user_id]=$user_maill_arr[$app_user_id];}
            ?>
            <tr>
                <td><?= $i;?></td>
                <td><?= $lib_com_arr[$row['IMPORTER_ID']];?></td>
                <td><?= $row['ID'];?></td>
                <td><?= $item_category[$row['ITEM_CATEGORY_ID']];?></td>
                <td><?= change_date_format($row['PI_DATE']);?></td>
                <td><?= $row['PI_NUMBER']?></td>
                <td><?= number_format($row['NET_TOTAL_AMOUNT'],4);?></td>
                <td><?= $supplier_arr[$row['SUPPLIER_ID']];?></td>
                <td><?= $row['INTERNAL_FILE_NO'];?></td>
                <td><?= $row['LC_SC_NO'];?></td>
                <td><?= $pay_term[$row['PAY_TERM']];?></td>
                <td><?= $piFor_array[$row['PI_FOR']];?></td>
                <td><?= $piFor_array[$row['PRIORITY_ID']];?></td>
                <td><?= $row['REMARKS'];?></td>
            </tr>
        </table>

        <?php 
        $message=ob_get_contents();
        ob_clean();
  
        $sysId = str_replace(",","','",$sysId);
        $image_arr = return_library_array("select ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='proforma_invoice' and MASTER_TBLE_ID ='".$row['ID']."'",'ID','IMAGE_LOCATION');
 
        $att_file_arr=array();
        foreach($image_arr as $file){
            $att_file_arr[] = '../../'.$file.'**'.$file;
        }

        $header=mailHeader();
        $to = implode(',',array_unique(explode(',',implode(',',$toArr))));
    
         echo $message."<br>".implode(',',$att_file_arr)."<br>".$to."<br><br>"; 
        $subject = "Pi Approval";
        if($to!="")echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr);
    } 

    exit();	
}

if($action=="quick_costing_approval_mail")
{
	extract(check_magic_quote_gpc($_REQUEST));

	include('../../auto_mail/setting/mail_setting.php');
	
	list($sys_id,$mail,$type,$app_user_id) = explode('__',$data);

	$sys_id_arr = explode('***',$sys_id);
	foreach($sys_id_arr as $dataStr){
		list($qc_id) = explode('*',$dataStr);
		$qcSql = "select COMPANY_ID,OPTION_ID,QC_NO, BUYER_ID,BRAND_ID,APPROVED,APPROVED_BY,COST_SHEET_NO,INSERTED_BY  from qc_mst where id=$qc_id and status_active=1 and is_deleted=0 order by cost_sheet_no Desc";
		$qcSqlRes = sql_select($qcSql);
		$option_arr = array();
		foreach($qcSqlRes as $row){
			$option_arr[$row['OPTION_ID']] = $row['OPTION_ID'];
			$company = $row['COMPANY_ID'];
			$qc_no = $row['QC_NO'];
			$BUYER_ID = $row['BUYER_ID'];
			$BRAND_ID = $row['BRAND_ID'];
			$APPROVED = $row['APPROVED'];
			$APPROVED_BY = $row['APPROVED_BY'];
			$COST_SHEET_NO = $row['COST_SHEET_NO'];
			$INSERTED_BY = $row['INSERTED_BY'];
		}


		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	
		if($db_type==0) $concat_cond="group_concat(lib_item_id)";
		else if($db_type==2) $concat_cond="listagg(cast(lib_item_id as varchar2(4000)),',') within group (order by lib_item_id)";
		else $concat_cond="";
		$sql_tmp="select temp_id, $concat_cond as lib_item_id from qc_template where status_active=1 and is_deleted=0 group by temp_id order by temp_id ASC";
		$sql_tmp_res=sql_select($sql_tmp);
		$template_name_arr=array();
		foreach($sql_tmp_res as $row)
		{
			$lib_temp_id='';
			
			$ex_temp_id=explode(',',$sqlMst_res[0][csf('lib_item_id')]);
			foreach($ex_temp_id as $lib_id)
			{
				if($lib_temp_id=="") $lib_temp_id=$lib_temp_arr[$lib_id]; else $lib_temp_id.=','.$lib_temp_arr[$lib_id];
			}
			$template_name_arr[$sqlMst_res[0][csf('temp_id')]]=$lib_temp_id;
		}
		
		$mstSql = "SELECT a.id, a.cost_sheet_id, a.cost_sheet_no, a.temp_id, a.lib_item_id, a.inquery_id, a.style_ref, a.buyer_id, a.uom, a.cons_basis, a.season_id, a.season_year, a.brand_id, a.style_des, a.prod_dept, a.delivery_date, a.exchange_rate, a.offer_qty, a.quoted_price, a.tgt_price, a.stage_id, a.costing_date, a.pre_cost_sheet_id, a.revise_no, a.option_id, a.buyer_remarks, a.option_remarks, a.body_color_id,a.inserted_by, a.margin, c.gmts_item, c.department_name, c.est_ship_date, b.confirm_style, b.approved from qc_mst a left join wo_quotation_inquery c on a.inquery_id=c.id and c.status_active=1 and c.is_deleted=0 left join qc_confirm_mst b on a.qc_no=b.cost_sheet_id where a.qc_no=$qc_no and a.status_active=1 and a.is_deleted=0";
		// echo $mstSql;  die;
		$master_attribute=array('id', 'cost_sheet_id', 'cost_sheet_no', 'temp_id', 'lib_item_id', 'inquery_id', 'style_ref', 'buyer_id', 'uom', 'cons_basis', 'season_id', 'season_year', 'brand_id', 'style_des', 'prod_dept', 'delivery_date', 'exchange_rate', 'offer_qty', 'quoted_price', 'tgt_price', 'stage_id', 'costing_date', 'pre_cost_sheet_id', 'revise_no', 'option_id', 'buyer_remarks', 'option_remarks', 'body_color_id','inserted_by', 'gmts_item', 'department_name', 'est_ship_date', 'confirm_style', 'approved','margin');
		$master_data=sql_select($mstSql);
		foreach($master_data as $row){
			$buyer_name=$row[csf('buyer_id')];
			$mstconuom=$unit_of_measurement[$row[csf('uom')]];
			foreach($master_attribute as $attr){
				$$attr = $row[csf($attr)];
			}		
		}
		//$cost_summary=sql_select("SELECT smv, fob_pcs, cm_cost  from qc_item_cost_summary where mst_id='$qc_no' and status_active=1 and is_deleted=0");
		
		$cost_summary=sql_select("SELECT a.smv,  a.cm_cost, a.cpm, a.efficiency, b.tot_fob_cost as fob_pcs  from qc_item_cost_summary a join qc_tot_cost_summary b on a.mst_id=b.mst_id where a.mst_id='$qc_no' and a.status_active=1 and a.is_deleted=0");

		foreach($cost_summary as $row){
			$smv_cost=$row[csf('smv')];
			$fob_pcs=$row[csf('fob_pcs')];
			$cm_cost=$row[csf('cm_cost')];
			$cpm=$row[csf('cpm')];
			$efficiency=$row[csf('efficiency')];
		}

		if($master_data[0][csf('buyer_id')]!=0) $addseasonYear='-'.$master_data[0][csf('season_year')]; else $addseasonYear="";
		
	
		
		$userSql = "select ID,USER_EMAIL,USER_NAME,USER_FULL_NAME from USER_PASSWD";
		$userSqlRes=sql_select($userSql);
		foreach($userSqlRes as $urserRow){
			$user_maill_arr[$urserRow['ID']] =$urserRow['USER_EMAIL']; 
			$user_name_arr[$urserRow['ID']] =$urserRow['USER_NAME']; 
			$user_full_name_arr[$urserRow['ID']] =$urserRow['USER_FULL_NAME']; 
		}

		if($APPROVED ==1){$statusMsg = 'Full Approved';}
        else if($APPROVED ==3){$statusMsg = 'Partial Approved';}
        else if($APPROVED ==2){$statusMsg = 'Denied';}
        else if($APPROVED == 0){$statusMsg = 'Unapproved';}
        else if($type ==5){$statusMsg = 'Denied';}
		
		
		ob_start();	
	?>


		<b>Dear Concerned,</b>	<br>
		<b>This quick costing has been <?= $statusMsg; ?>, Please proceed.</b>			
        <table rules="all" border="1">
			<thead>
				<tr>
					<td colspan="16" align="right"><b><?= $statusMsg; ?> by:</b> <?= $user_full_name_arr[$APPROVED_BY] ;?></td>
				</tr>
				<tr bgcolor="#CCC">
					<th>Company</th>	
					<th>Buyer</th>	
					<th>Style No</th>	
					<th>Cost Sheet No</th>	
					<th>Style Offer Qty -Pcs</th>	
					<th>FOB -Pcs</th>	
					<th>SMV/Pcs [Minutes]</th>
					<th>Material Cost</th>	
					<th>Calculative CM</th>	
					<th>Actual CM</th>
					<th>Buyer Cost</th>	
					<th>CM/Pcs [USD]</th>	
					<th>Profit Margin of CM</th>	
					<th>Marketing Cost/Pcs</th>	
					<th>Buffer</th>	
					<th>E.P.M [USD]</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?= $company_arr[$company]; ?></td>	
					<td><?= $buyer_arr[$buyer_name]; ?></td>	
					<td><?= $style_ref; ?></td>	
					<td><?= $COST_SHEET_NO; ?></td>	
					<td align="right"><?= $offer_qty; ?></td>	
					<td align="right"><?= $fob_pcs; ?></td>	
					<td align="right"><?= $smv_cost; ?></td>
					
					<td align="right" title="<?= $fob_pcs.'-('.$cm_cost.'+'.$margin.')';?>"><?= $buyer_cost=number_format($fob_pcs-($cm_cost+$margin),4) ?></td>
					<td align="right"><? $calculative_cm=(($cpm*$smv_cost)/$efficiency)*100; echo number_format($calculative_cm,4) ?></td>
					<td align="right"><?= $cm_cost ?></td>

					<td align="right"><?= $buyer_cost=$fob_pcs-($cm_cost+$margin); ?></td>	
					<td align="right"><?= $cm_cost; ?></td>	
					<td align="right"><?= $margin; ?></td>	
					<td align="right"><?= $marketing_cost=$buyer_cost+$cm_cost+$margin; ?></td>	
					<td align="right"><?= $buffer=$fob_pcs-$marketing_cost; ?></td>	
					<td align="right"><? $epm=$cm_cost/$smv_cost; echo number_format($epm,2);  ?></td>
				</tr>
				
			</tbody>
		</table>

	<?

		$message=ob_get_contents();
		ob_clean();
		$toArr = array();

		$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","entry_form=45 and user_id=$app_user_id and company_id =$company and is_deleted = 0");

        $elcetronicSql = "SELECT a.BUYER_ID,a.BRAND_ID,a.SEQUENCE_NO,a.BYPASS,b.USER_EMAIL,b.ITEM_CATE_ID   from electronic_approval_setup a join user_passwd b on a.user_id=b.id where a.entry_form=45 and a.SEQUENCE_NO > $user_sequence_no and a.company_id=$company order by a.SEQUENCE_NO"; //and a.page_id=2150 and a.entry_form=46
      //echo $elcetronicSql;die;


        $elcetronicSqlRes=sql_select($elcetronicSql);
        foreach($elcetronicSqlRes as $rows){
            
            if($rows['BUYER_ID'] != ''){
				if($rows['BUYER_ID']==''){$BUYER_ID_ARR = array_keys($buyer_arr);}
				else{$BUYER_ID_ARR = explode(',',$rows['BUYER_ID']);}
                //$BRAND_ID_ARR = explode(',',$rows['BRAND_ID']);
                foreach($BUYER_ID_ARR as $bid){  
                    if($rows['USER_EMAIL']!='' && $bid == $BUYER_ID){
                        $toArr[$rows['USER_EMAIL']]=$rows['USER_EMAIL'];
						if($rows['BYPASS']==2){break;}
                    } 
                }
                
            }
            else{
                if($rows['USER_EMAIL']){$toArr[$rows['USER_EMAIL']]=$rows['USER_EMAIL'];}
                if($rows['BYPASS']==2){break;}
            }

        }

		if($user_maill_arr[$INSERTED_BY] && ($APPROVED ==1 || $type ==5 || $APPROVED ==2)){
			$toArr[$user_maill_arr[$INSERTED_BY]]=$user_maill_arr[$INSERTED_BY];
		}
      
  

		// $sysId = str_replace(",","','",$sysId);
		// $image_arr = return_library_array("select ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='proforma_invoice' and MASTER_TBLE_ID ='".$row['ID']."'",'ID','IMAGE_LOCATION');

		// $att_file_arr=array();
		// foreach($image_arr as $file){
		// 	$att_file_arr[] = '../../'.$file.'**'.$file;
		// }

		// $header=mailHeader();
		$to = implode(',',array_unique(explode(',',implode(',',$toArr))));

		  //echo $message;die; 
		// $subject = "Pi Approval";
		if($to!="")echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr);

	}
	

	exit();
}



?>
