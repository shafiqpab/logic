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

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );
	exit();
}

if($action=="load_drop_down_buyer_new_user")
{
	$data=explode("_",$data);
	$log_sql = sql_select("SELECT user_level,buyer_id,unit_id,is_data_level_secured FROM user_passwd WHERE id = '$data[1]' AND valid = 1");
	foreach($log_sql as $r_log)
	{
		if($r_log[csf('IS_DATA_LEVEL_SECURED')]==1)
		{
			if($r_log[csf('BUYER_ID')]!="") $buyer_cond=" and buy.id in (".$r_log[csf('BUYER_ID')].")"; else $buyer_cond="";
		}
		else $buyer_cond="";
	}
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );
	exit();
}


$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );

function getSequence($parameterArr=array()){
	$lib_buyer_arr=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,ENTRY_FORM,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,SUPPLIER_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['ENTRY_FORM']} AND IS_DELETED = 0 order by SEQUENCE_NO";
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
	$sql="SELECT COMPANY_ID,PAGE_ID,ENTRY_FORM,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,SUPPLIER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['ENTRY_FORM']} AND IS_DELETED = 0  order by SEQUENCE_NO";
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
			if( in_array($bbtsRows['buyer_id'],$usersDataArr[$user_id]['BUYER_ID']) &&  $bbtsRows['buyer_id']>0 ){
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

	$job_no = str_replace("'","",$txt_job_no);
	$style_ref = str_replace("'","",$txt_style_ref);
	$job_year = str_replace("'","",$cbo_year);
	$txt_alter_user_id = str_replace("'","",$txt_alter_user_id);
    $approval_type = str_replace("'","",$cbo_approval_type);
	$company_name = str_replace("'","",$cbo_company_name);
	$user_id_approval = ($txt_alter_user_id!='') ? $txt_alter_user_id : $user_id;
   
	if($company_name>0){
		$app_company_arr[$company_name]=$company_name;
	}
	else{
		$app_company_arr=return_library_array( "select company_id, company_id from electronic_approval_setup where page_id=$menu_id and entry_form=86 and user_id=$user_id and is_deleted=0",'company_id','company_id');
	}

	?>
	<form name="sourcingApproval_2" id="sourcingApproval_2">
    <fieldset style="width:1330px; margin-top:10px">
    <legend>Sourcing Higher Authority Approval</legend>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1320" class="rpt_table" align="left" >
            <thead>
                <th width="40">&nbsp;</th>
                <th width="30">SL</th>
                <th width="50">Company</th>
                <th width="50">Job No</th>
				<th width="100">Last Version</th>
                <th width="60">CM Cost</th>
                <th width="60">EPM</th>
                <th width="60">SMV</th>
                <th width="40">Year</th>
                <th width="110">Buyer</th>
                <th width="130">Style Ref.</th>
                <th width="70">Sourcing Date</th>
                <th width="70">IMG</th>
                <th width="140">Unapproved Request</th>
                <th width="65">Insert By</th>
                <th width="80">Approved Date</th>
                <th>Refusing Cause</th>
            </thead>
        </table>
    	<div style="width:1340px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1320" class="rpt_table" id="tbl_list_search" align="left">
            <tbody>
         <? 
		$i=1;
		foreach($app_company_arr as $company_name){

			$electronicDataArr=getSequence(array('company_id'=>$company_name,'ENTRY_FORM'=>86,'user_id'=>$user_id_approval,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'product_dept_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0));

            $where_con = '';
			if ($style_ref != ""){$where_con = " and b.style_ref_no='".trim($style_ref)."'";}
			if ($job_no != ""){$where_con .= " and b.job_no_prefix_num='".trim($job_no)."'";}
			if($job_year !=0 ){$where_con .= " and YEAR(a.insert_date)='".trim($job_year)."'";}
			if(str_replace("'","",$txt_date)!="")
			{
				if(str_replace("'","",$cbo_get_upto)==1) $where_con .= " and a.sourcing_date>$txt_date";
				else if(str_replace("'","",$cbo_get_upto)==2) $where_con .= " and a.sourcing_date<=$txt_date";
				else if(str_replace("'","",$cbo_get_upto)==3) $where_con .= " and a.sourcing_date=$txt_date";
			}

			if($approval_type==2) // Un-Approve
			{  
				if($electronicDataArr['user_by'][$user_id_approval]['BUYER_ID']){
					$where_con .= " and b.BUYER_NAME in(".$electronicDataArr['user_by'][$user_id_approval]['BUYER_ID'].",0)";
					$electronicDataArr['sequ_by'][0]['BUYER_ID']=$electronicDataArr['user_by'][$user_id_approval]['BUYER_ID'];
				}


				$data_mast_sql = "select 
				a.ID,a.SOURCING_READY_TO_APPROVED,a.SOURCING_HA_APPROVED,b.BUYER_NAME,b.COMPANY_NAME
				from wo_pre_cost_mst a,wo_po_details_master b where b.id=a.job_id and a.status_active=1 and a.is_deleted=0 and a.sourcing_ready_to_approved=1 and b.status_active=1 and b.is_deleted=0 and a.SOURCING_HA_APPROVED<>1 and b.COMPANY_NAME=$company_name $where_con";
 				 //echo $data_mast_sql;die;

				$tmp_sys_id_arr=array();
				$data_mas_sql_res=sql_select( $data_mast_sql );
				foreach ($data_mas_sql_res as $row)
				{ 
					for($seq=($electronicDataArr['user_by'][$user_id_approval]['SEQUENCE_NO']-1);$seq>=0; $seq-- ){
						
						if($electronicDataArr['sequ_by'][$seq]['BUYER_ID']==''){$electronicDataArr['sequ_by'][$seq]['BUYER_ID']=0;}
						
						if(in_array($row['BUYER_NAME'],explode(',',$electronicDataArr['sequ_by'][$seq]['BUYER_ID'])))
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
			
				//print_r($tmp_sys_id_arr);die;
				 
				
				$sql='';
				for($seq=0;$seq<=count($electronicDataArr['sequ_arr']); $seq++ ){
					$sys_con = where_con_using_array($tmp_sys_id_arr[$seq],0,'a.ID');
		
					if($tmp_sys_id_arr[$seq]){
						if($sql!=''){$sql .=" UNION ALL ";}
						$sql.="select a.JOB_ID,A.ID,B.SET_SMV AS SEW_SMV,TO_CHAR(A.INSERT_DATE,'YYYY') AS YEAR, B.QUOTATION_ID, B.JOB_NO_PREFIX_NUM,B.ID AS JOB_ID, B.JOB_NO, B.BUYER_NAME, B.STYLE_REF_NO, A.SOURCING_DATE AS COSTING_DATE, 0 AS APPROVAL_ID, A.APPROVED, A.INSERTED_BY, A.SOURCINNG_REFUSING_CAUSE, A.ENTRY_FROM, MIN(D.SHIPMENT_DATE) AS MINSHIP_DATE, MAX(D.SHIPMENT_DATE) AS MAXSHIP_DATE, B.JOB_QUANTITY, (B.JOB_QUANTITY*B.TOTAL_SET_QNTY) AS JOB_QTY_PCS, B.TOTAL_PRICE from wo_pre_cost_mst a, wo_po_details_master b,wo_po_break_down d  where b.id=a.job_id and b.id=d.job_id and b.COMPANY_NAME=$company_name and a.HA_APP_SEQU_BY_SOURCE=$seq $sys_con  and  b.is_deleted=0 and b.status_active=1 and a.status_active=1 and a.is_deleted=0 and a.sourcing_ready_to_approved=1 and a.SOURCING_HA_APPROVED<>1 group by a.JOB_ID,a.ID, b.quotation_id, b.job_no_prefix_num, b.id, b.job_no, b.BUYER_NAME, b.style_ref_no, a.sourcing_date,a.approved, a.inserted_by, a.sourcinng_refusing_cause,a.insert_date, a.entry_from, b.job_quantity, b.total_set_qnty, b.total_price, b.set_smv";
		
					}
				}
			}
			else
			{   
				$sql="select a.JOB_ID,A.ID,B.SET_SMV AS SEW_SMV,TO_CHAR(A.INSERT_DATE,'YYYY') AS YEAR, B.QUOTATION_ID, B.JOB_NO_PREFIX_NUM,B.ID AS JOB_ID, B.JOB_NO, B.BUYER_NAME, B.STYLE_REF_NO, A.SOURCING_DATE AS COSTING_DATE, '0' AS APPROVAL_ID, A.APPROVED, A.INSERTED_BY, A.SOURCINNG_REFUSING_CAUSE, A.ENTRY_FROM, MIN(D.SHIPMENT_DATE) AS MINSHIP_DATE, MAX(D.SHIPMENT_DATE) AS MAXSHIP_DATE, B.JOB_QUANTITY, (B.JOB_QUANTITY*B.TOTAL_SET_QNTY) AS JOB_QTY_PCS, B.TOTAL_PRICE from wo_pre_cost_mst a, wo_po_details_master b,wo_po_break_down d,APPROVAL_MST c  where b.id=a.job_id and b.id=d.job_id and c.mst_id=a.id and b.COMPANY_NAME=$company_name $where_con and  b.is_deleted=0 and b.status_active=1 and a.status_active=1 and a.is_deleted=0 and a.sourcing_ready_to_approved=1 and a.SOURCING_HA_APPROVED in(1,3)  and c.SEQUENCE_NO={$electronicDataArr['user_by'][$user_id_approval]['SEQUENCE_NO']} and a.HA_APP_SEQU_BY_SOURCE=c.SEQUENCE_NO and c.entry_form=86 group by a.JOB_ID,a.ID, b.quotation_id, b.job_no_prefix_num, b.id, b.job_no, b.BUYER_NAME, b.style_ref_no, a.sourcing_date,  a.approved, a.inserted_by, a.sourcinng_refusing_cause,a.insert_date, a.entry_from, b.job_quantity, b.total_set_qnty, b.total_price, b.set_smv";
			}

		
		    //echo $sql;die();


			// echo $sql; die;
			$nameArray=sql_select( $sql );
			$jobFobValue_arr=array(); $jobIds="";
			foreach ($nameArray as $row)
			{
				$jobFobValue_arr[$row[csf('job_no')]]=$row[csf('total_price')];
				$job_id_arr[$row[csf('job_id')]]=$row[csf('job_id')];
			}

			
			$preSql ="select job_no, costing_per_id as costing_per, job_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, commission, commission_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, common_oh, common_oh_percent, total_cost, deffdlc_cost, deffdlc_percent, interest_cost, interest_percent, incometax_cost, incometax_percent, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, price_pcs_or_set, depr_amor_pre_cost, depr_amor_po_price, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche, margin_pcs_bom from wo_pre_cost_dtls where status_active=1 and is_deleted=0 ".where_con_using_array($job_id_arr,0,'job_id')."";
			//echo $preSql;die;
			$pre_data_array = sql_select($preSql);
				
			$margin_pcs_arr=array();$pre_cost_dat_by_job=array();
			foreach( $pre_data_array as $row )
			{ 
				$cm_cost_arr[$row[csf('job_no')]]['cm_cost']+=$row[csf('cm_cost')];
				if($row[csf("costing_per")]==1){$order_price_per_dzn=12;}
				else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;}
				else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;}
				else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;}
				else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;}
				else {$order_price_per_dzn=0;}
				
				$fabric_cost_dzn=$row[csf("fabric_cost")];
				$trims_cost_dzn=$row[csf("trims_cost")];
				$embel_cost_dzn=$row[csf("embel_cost")];
				$wash_cost_dzn=$row[csf("wash_cost")];
				$comm_cost_dzn=$row[csf("comm_cost")];
				
				$comm_cost_dzn=$row[csf("comm_cost")];
				$cm_cost_dzn=$row[csf("cm_cost")];
				$price_dzn=$row[csf("price_dzn")];
				$total_cost_dzn=$row[csf("total_cost")];
				$deffdlc_cost_dzn=$row[csf("deffdlc_cost")];
				$interest_cost_dzn=$row[csf("interest_cost")];
				$incometax_cost_dzn=$row[csf("incometax_cost")];
				$commission_dzn=$row[csf("commission")];
				$operatin_expense_dzn=$row[csf("common_oh")];
				$lab_test_dzn=$row[csf("lab_test")];
				$inspection_dzn=$row[csf("inspection")];
				$cm_cost_dzn =$row[csf("cm_cost")];
				$common_oh_dzn =$row[csf("common_oh")];
				$freight_dzn =$row[csf("freight")];
				$currier_pre_cost_dzn = $row[csf("currier_pre_cost")];
				$certificate_pre_cost_dzn = $row[csf("certificate_pre_cost")];
				$deffdlc_cost_dzn = $row[csf("deffdlc_cost")];
				$depr_amor_pre_cost_dzn = $row[csf("depr_amor_pre_cost")];
				$interest_cost_dzn=$row[csf("interest_cost")];
				$interest_cost_percent=$row[csf("interest_percent")];
				$incometax_cost_dzn=$row[csf("incometax_cost")];
				$studio_cost_dzn=$row[csf("studio_cost")];
				$design_cost_dzn=$row[csf("design_cost")];
					
				$depr_amor_po_price=$row[csf("depr_amor_po_price")];
				$depr_amor_pre_cost_dzn=$row[csf("depr_amor_pre_cost")];
				$margin_pcs_bom=$commission_costing_arr[$job_no];
				$lab_test=$lab_test_cost;
				$inspection=$inspection_cost;
				$cm_cost=$cm_cost_dzn;
				$freight=$freight_cost;
				$currier_pre_cost=$currier_cost;
				$certificate_pre_cost=$certificate_cost;
				
				$material_service_cost_dzn=$fabric_cost_dzn+$trims_cost_dzn+$embel_cost_dzn+$wash_cost_dzn+$deffdlc_cost_dzn+$inspection_dzn+$lab_test_dzn+$freight_dzn+$currier_pre_cost_dzn+$certificate_pre_cost_dzn;
				
				$net_fob_value_dzn=$price_dzn-$commission_dzn;
				$contributions_value_dzn=$net_fob_value_dzn-$material_service_cost_dzn-$comm_cost_dzn;
				$job_epm_arr[$row[csf('job_no')]]=$contributions_value_dzn/$order_price_per_dzn;
				$job_epm_contribute_margin_arr[$row[csf('job_no')]]=$contributions_value_dzn.', CostPer='.$order_price_per_dzn;
				$margin_pcs_arr[$row[csf('job_no')]]['marginpcs']=$row[csf('margin_pcs_set')];
				$margin_pcs_arr[$row[csf('job_no')]]['marginpcsbom']=$row[csf('margin_pcs_bom')];
				
				$pre_cost_dat_by_job[$row[csf('job_no')]]['cm_val']=$row[csf("cm_cost")];
				$pre_cost_dat_by_job[$row[csf('job_no')]]['cm_per']=$row[csf("cm_cost_percent")];
				$pre_cost_dat_by_job[$row[csf('job_no')]]['marg_val']=$row[csf("margin_pcs_set")];
				$pre_cost_dat_by_job[$row[csf('job_no')]]['marg_per']=$row[csf("margin_pcs_set_percent")];
				
			}



			$sql_unapproved=sql_select("select booking_id,approval_cause from fabric_booking_app_cause_source where  entry_form=86 and approval_type=2 and is_deleted=0 and status_active=1 and VALID=1");
			$unapproved_request_arr=array();
			foreach($sql_unapproved as $rowu)
			{
				$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
			}

			//Pre cost button---------------------------------
			$print_report_format_ids = return_field_value("format_id","lib_report_template","template_name=".$company_name." and module_id=2 and report_id in (141) and is_deleted=0 and status_active=1");
			$format_ids=explode(",",$print_report_format_ids);
			$row_id=$format_ids[0];
			// echo $row_id.'d';

			//Order Wise Budget Report button---------------------------------
			$print_report_format_ids2 = return_field_value("format_id","lib_report_template","template_name=".$company_name." and module_id=11 and report_id=18 and is_deleted=0 and status_active=1");
			$format_ids2=explode(",",$print_report_format_ids2);
			$row_id2=$format_ids2[0];

            $sql_revised=sql_select("select a.garments_nature,b.entry_from, b.sourcing_date as costing_date,to_char(b.sourcing_inserted_date,'YYYY') as year,(select max(h.approved_no) from approval_history h where b.id=h.mst_id and h.entry_form =86 ) as revised_no from  wo_pre_cost_mst b, wo_po_details_master a where b.job_no=a.job_no and a.COMPANY_NAME=$company_name $date_cond $job_no_cond $job_year_cond $styleref_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.sourcing_ready_to_approved=1 and b.sourcing_inserted_by>0   group by a.garments_nature,b.entry_from, b.sourcing_date, b.sourcing_inserted_date,b.id");
            foreach ($sql_revised as $row)
            {
                $revised_no=$row[csf('revised_no')];
                $costing_date=$row[csf('costing_date')];
                $entry_from=$row[csf('entry_from')];
                $garments_nature=$row[csf('garments_nature')];
            }

                foreach ($nameArray as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    $value=$row[csf('id')];
                    if($row[csf('approval_id')]==0) $print_cond=1;
                    else
                    {
                        if($duplicate_array[$row[csf('id')]][$row[csf('sequence_no')]][$row[csf('approved_by')]]=="")
                        {
                            $duplicate_array[$row[csf('id')]][$row[csf('sequence_no')]][$row[csf('approved_by')]]=$row[csf('approval_id')];
                            $print_cond=1;
                        }
                        else
                        {
                            if($all_approval_id=="") $all_approval_id=$row[csf('approval_id')]; else $all_approval_id.=",".$row[csf('approval_id')];
                            $print_cond=0;
                        }
                    }
                    if($row_id2==23){$type=1;/*Summary;*/}
                    else if($row_id2==24){$type=2;}
                    else if($row_id2==25){$type=3;/*Budget Report2;*/}
                    else if($row_id2==26){$type=4;/*Quote Vs Budget;*/}
                    else if($row_id2==27){$type=5;/*Budget On Shipout;*/}
                    else if($row_id2==29){$type=6;/*C.Date Budget On Shipout;*/}
                    else if($row_id2==182){$type=7;/*Budget Report 3;*/}

                    
                    //{$row[csf('buyer_name')]}
                    if($print_cond==1)
                    {
                        if($row_id==313){$action='mkt_source_cost'; } //MKT Vs Source
                        else if($row_id==323){$action='app_final_cost';} //Final App
                        

                        $function="generate_worder_report('".$action."','".$row[csf('job_no')]."',".$company_name.",".$row[csf('buyer_name')].",'".$row[csf('style_ref_no')]."','".$row[csf('costing_date')]."',".$row[csf('entry_from')].",'".$row[csf('quotation_id')]."');"; 
                        $function2="generate_worder_report('mkt_source_cost','".$row[csf('job_no')]."',".$company_name.",".$row[csf('buyer_name')].",'".$row[csf('style_ref_no')]."','".$row[csf('costing_date')]."',".$row[csf('entry_from')].",'".$row[csf('quotation_id')]."');";
                        
                        
                        ?>
                        <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>" align="center">
                            <td width="40" align="center" valign="middle">
                                <input type="checkbox" id="tbl_<?=$i;?>" />
                                <input id="booking_id_<?=$i;?>" name="booking_id[]" type="hidden" value="<?=$value; ?>" />
                                <input id="booking_no_<?=$i;?>" name="booking_no[]" type="hidden" value="<?=$row[csf('job_no')]; ?>" />
                                <input id="approval_id_<?=$i;?>" name="approval_id[]" type="hidden" value="<?=$row[csf('approval_id')]; ?>" />
                                <input id="unapprov_msg_<?=$i;?>" name="unapprov_msg[]" type="hidden" value="<?=$unapproved_request_arr[$value]; ?>" />
                                <input id="<?=strtoupper($row[csf('job_no')]); ?>" name="no_joooob[]" type="hidden" value="<?=$i;?>" />
                                <input id="mst_id_company_id_<?=$i;?>" name="mst_id_company_id[]" type="hidden" value="<?=$row[csf('job_no')].'*'.$value.'*'.$company_name.'*'.$row[csf('job_id')]; ?>" />
                        
                            </td>
                            <td width="30" align="center"><?=$i; ?></td>
                            <td width="50" align="center"><?=$company_arr[$company_name];?></td>
                            <td width="50"><a href='##' onClick="<?=$function; ?>"><?=$row[csf('job_no_prefix_num')]; ?></a></td>
                            <?
                                //=====================revise no===================================	
                        

                                $function3="";
                                if($revised_no>0)
                                {
                                    for($q=1; $q<=$revised_no; $q++)
                                    {
                                        if($function3=="") $function3="<a href='#' onClick=\"history_budget_sheet(".$company_name.",'".$row[csf('job_no')]."',".$row[csf('buyer_name')].",'".$row[csf('style_ref_no')]."','".$costing_date."','".mkt_source_cost."',".$entry_from.",".$garments_nature.",'".$q."'".")\"> ".$q."<a/>";
                                        else $function3.=", "."<a href='#' onClick=\"history_budget_sheet(".$company_name.",'".$row[csf('job_no')]."',".$row[csf('buyer_name')].",'".$row[csf('style_ref_no')]."','".$costing_date."','".mkt_source_cost."',".$entry_from.",".$garments_nature.",'".$q."'".")\"> ".$q."<a/>";
                                        
                                    }
                                }
                                
                            //=====================revise no===================================	
                            
                            ?>
                            <td width="100" style="word-break:break-all"><?=$function3;?>&nbsp;</td>
                            <td width="60" align="right"><p style="color:<?=$td_color; ?>"><?= number_format($cm_cost,4); ?></p></td>
                            <td width="60"  title="Contribution Margin/Costing Per/SMV(<?= $job_epm_contribute_margin_arr[$row[csf('job_no')]];?>)" align="right"><p><?= number_format($job_epm_arr[$row[csf('job_no')]]/$row[csf('sew_smv')],4); ?></p></td>
                            <td width="60" align="right"><?= $row[csf('sew_smv')]; ?></td>							
                            

                            <td width="40" style="word-break:break-all;"><?=$row[csf('year')]; ?></td>
                            <td width="110" style="word-break:break-all;"><?=$buyer_arr[$row[csf('buyer_name')]]; ?></td>
                            <td width="130" align="center" style="word-break:break-all;"><a href='##' onClick="<?=$function2; ?>"><?=$row[csf('style_ref_no')]; ?></a></td>
                            <td width="70" align="center"><? if($row[csf('costing_date')]!="0000-00-00") echo change_date_format($row[csf('costing_date')]); ?>&nbsp;</td>
                            <td align="center" width="70"><a href="##" onClick="openImgFile('<?=$row[csf('job_no')]; ?>','img');">View</a></td>
                            <td width="140" style="word-break:break-all"><? if($approval_type==1) echo $unapproved_request_arr[$value]; ?> </td>
                            <td width="65" style="word-break:break-all;"><? echo ucfirst ($user_arr[$row[csf('inserted_by')]]); ?>&nbsp;</td>
                            <td width="80" align="center"><? if($row[csf('approved_date')]!="0000-00-00") echo change_date_format($row[csf('approved_date')]); ?>&nbsp;</td>
                            <td> <input style="width:150px;" type="text" class="text_boxes"  name="txtCause_<?=$i; ?>" id="txtCause_<?=$i; ?>" placeholder="Browse" onClick="openmypage_refusing_cause('requires/sourcing_higher_authority_approval_controller.php?action=refusing_cause_popup','Refusing Cause','<?=$row[csf('id')]; ?>','<?=$row[csf('sourcinng_refusing_cause')]; ?>');" value="<?=$row[csf('sourcinng_refusing_cause')]; ?>"/></td>
                        </tr>
                        <?
                        $i++;
                    }

                    if($all_approval_id!="")
                    {
                        $con = connect();
                        $rID=sql_multirow_update("approval_history","current_approval_status",0,"id",$all_approval_id,1);
                        //echo $rID."**";
                        
                            if($rID==1)
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
                    
                }
                $denyBtn="";
                if($approval_type==2) $denyBtn=""; else $denyBtn=" display:none";
		
        }//end company loof;
			
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="1320" class="rpt_table" align="left">
                <tfoot>
                    <td width="40" align="center"><input type="checkbox" id="all_check" onClick="check_all('all_check');" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<?=$i; ?>,<?=$approval_type; ?>);"/>&nbsp;&nbsp;&nbsp;<input type="button" value="<? if($approval_type==2) echo "Deny"; ?>" class="formbutton" style="width:100px;<?=$denyBtn; ?>" onClick="submit_approved(<?=$i; ?>,5);"/></td>
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
    //$user_id=7;
    $con = connect();

	$txt_alter_user_id = str_replace("'","",$txt_alter_user_id);
	$company_name=str_replace("'","",$cbo_company_name);
	$user_id_approval=($txt_alter_user_id=='')?$user_id:$txt_alter_user_id;
  
	$appCompanyArr=array();$appIdArr=array();$appNoArr=array();$appJobIdArr=array();
	foreach(explode(',',$mst_id_company_ids) as $ic){
		list($bno,$bid,$company,$job_id)=explode('*',$ic);
		$appCompanyArr[$company]=$company;
		$appIdArr[$company][$bid]=$bid;
		$appNoArr[$company][$bno]=$bno;
		$appJobIdArr[$company][$bid]=$job_id;
	}
	
	foreach($appCompanyArr as $cbo_company_name){
		$booking_nos="'".implode("','",$appNoArr[$cbo_company_name])."'";
		$booking_ids=implode(',',$appIdArr[$cbo_company_name]);
		$job_ids=implode(',',$appJobIdArr[$cbo_company_name]);

		
	
		$msg=''; $flag=''; $response='';
	

		$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=86 group by mst_id", "mst_id", "approved_no");

		//............................................................................
		
		$sql = "select a.ID,a.SOURCING_HA_APPROVED,b.BUYER_NAME,a.SOURCING_READY_TO_APPROVED  from wo_pre_cost_mst a, wo_po_details_master b  where a.job_no=b.job_no and  b.COMPANY_NAME=$cbo_company_name  and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.SOURCING_READY_TO_APPROVED=1 and a.id in($booking_ids)";
		//echo $sql;die();
		$sqlResult=sql_select( $sql );
		foreach ($sqlResult as $row)
		{
			$matchDataArr[$row['ID']]=array('buyer_id'=>$row['BUYER_NAME'],'brand_id'=>0,'store'=>0);
			$approve_status_arr[$row['ID']] = $row['SOURCING_HA_APPROVED'];
		}
		
		$finalDataArr=getFinalUser(array('company_id'=>$cbo_company_name,'ENTRY_FORM'=>86,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0,'product_dept_arr'=>0,'match_data'=>$matchDataArr));
		
	
		$sequ_no_arr_by_sys_id = $finalDataArr['final_seq'];
		$user_sequence_no = $finalDataArr['user_seq'][$user_id_approval];
	//print_r($user_sequence_no) ;die;

	
 	if($approval_type==2)
	{ 
 		
		$id=return_next_id( "id","approval_mst", 1 ) ;
		$ahid=return_next_id( "id","approval_history", 1 ) ;	
		
		$target_app_id_arr = explode(',',$booking_ids);	
        //print_r($target_app_id_arr);
        foreach($target_app_id_arr as $mst_id)
        {
			if($data_array!=''){$data_array.=",";}
			$data_array.="(".$id.",86,'".$mst_id."','".$user_sequence_no."',".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
			$id=$id+1;
			
			if($approve_status_arr[$mst_id] == 0 || $approve_status_arr[$mst_id] == 2){
				$approved_no = $max_approved_no_arr[$mst_id]+1;
				$approved_no_array[$appJobIdArr[$cbo_company_name][$mst_id]]=$approved_no;
				
			}
			else{
				$approved_no = $max_approved_no_arr[$mst_id];
			}
			


			if($history_data_array!="") $history_data_array.=",";
			$history_data_array.="(".$ahid.",86,".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
			$ahid++;
			
			//mst data.......................
			$approved=(max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no)?1:3;
			$data_array_up[$mst_id] = explode(",",("".$approved.",".$user_sequence_no.",'".$pc_date_time."',".$user_id_approval."")); 
        }
	 

		//print_r($approved_no_array);die;
 

        $flag=1;
		if($flag==1) 
		{  
			$field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
			//echo "10**insert into approval_mst($field_array) values $data_array";die;
			$rID1=sql_insert("approval_mst",$field_array,$data_array,0);
			if($rID1) $flag=1; else $flag=0; 
		}
		
		if($flag==1) 
		{
			$field_array_up="SOURCING_HA_APPROVED*HA_APP_SEQU_BY_SOURCE*APPROVED_DATE_SOURCE*APPROVED_BY_SOURCE"; 
			$rID2=execute_query(bulk_update_sql_statement( "wo_pre_cost_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr ));
			if($rID2) $flag=1; else $flag=0; 
		}
		
		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=86 and mst_id in ($booking_ids)";
			$rID3=execute_query($query,1);
			if($rID3) $flag=1; else $flag=0;
		}
		 
		if($flag==1)
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING";
			$rID4=sql_insert("approval_history",$field_array,$history_data_array,0);
			if($rID4) $flag=1; else $flag=0;
		}

		

        if(count($approved_no_array)>0)
        {
            $approved_string="";

            
            foreach($approved_no_array as $key=>$value)
            {
                $approved_string.=" WHEN $key THEN $value ";
            }

            //echo "10**";
            $approved_string_mst="CASE job_id ".$approved_string." END";
            $approved_string_dtls="CASE job_id ".$approved_string." END";
            
            $approved_string_mst2="CASE job_id ".$approved_string." END";
            $approved_string_dtls2="CASE job_id ".$approved_string." END";
            $approved_string_dtls3="CASE id ".$approved_string." END";
            
            //------------wo_po_dtls_mst_his----------------------------------
            $sqljob="insert into wo_po_dtls_mst_his (id, job_id, approved_no, approval_page, garments_nature, job_no_prefix, job_no_prefix_num, job_no, quotation_id, order_repeat_no, company_name, buyer_name, style_ref_no, product_dept, product_code, location_name, style_description, ship_mode, region, team_leader, dealing_marchant, remarks, job_quantity, avg_unit_price, currency_id, total_price, packing, agent_name, product_category, order_uom, gmts_item_id, set_break_down, total_set_qnty, set_smv, is_deleted, status_active, inserted_by, insert_date, updated_by, update_date, pro_sub_dep, client_id, item_number_id, factory_marchant, qlty_label, is_excel, style_owner, booking_meeting_date, bh_merchant, copy_from, season_buyer_wise, is_repeat, repeat_job_no, ready_for_budget, working_location_id, gauge, fabric_composition, design_source_id, yarn_quality, season_year, brand_id, inquiry_id, body_wash_color, sustainability_standard, fab_material, quality_level, requisition_no, working_company_id, fit_id)
                select '', id, $approved_string_dtls3, 86, garments_nature, job_no_prefix, job_no_prefix_num, job_no, quotation_id, order_repeat_no, company_name, buyer_name, style_ref_no, product_dept, product_code, location_name, style_description, ship_mode, region, team_leader, dealing_marchant, remarks, job_quantity, avg_unit_price, currency_id, total_price, packing, agent_name, product_category, order_uom, gmts_item_id, set_break_down, total_set_qnty, set_smv, is_deleted, status_active, inserted_by, insert_date, updated_by, update_date, pro_sub_dep, client_id, item_number_id, factory_marchant, qlty_label, is_excel, style_owner, booking_meeting_date, bh_merchant, copy_from, season_buyer_wise, is_repeat, repeat_job_no, ready_for_budget, working_location_id, gauge, fabric_composition, design_source_id, yarn_quality, season_year, brand_id, inquiry_id, body_wash_color, sustainability_standard, fab_material, quality_level, requisition_no, working_company_id, fit_id
            from wo_po_details_master where id in ($job_ids)";
            //echo "10**".$sqljob;die;
            
            //------------wo_po_dtls_item_set_his----------------------------------
            $sqlsetitem="insert into wo_po_dtls_item_set_his (id, approval_page, set_dtls_id, approved_no, job_no, gmts_item_id, set_item_ratio, smv_pcs, smv_set, smv_pcs_precost, smv_set_precost, complexity, embelishment, cutsmv_pcs, cutsmv_set, finsmv_pcs, finsmv_set, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, quot_id, aop, aopseq, ws_id, job_id, bush, bushseq, peach, peachseq, yd, ydseq, printdiff, embrodiff, washdiff, spwdiff)
                select '', 86, id, $approved_string_mst, job_no, gmts_item_id, set_item_ratio, smv_pcs, smv_set, smv_pcs_precost, smv_set_precost, complexity, embelishment, cutsmv_pcs, cutsmv_set, finsmv_pcs, finsmv_set, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, quot_id, aop, aopseq, ws_id, job_id, bush, bushseq, peach, peachseq, yd, ydseq, printdiff, embrodiff, washdiff, spwdiff from wo_po_details_mas_set_details where job_id in ($job_ids)";
            //echo "10**".$sqlsetitem;die;
            
            //------------wo_po_break_down_his----------------------------------
            $sqlpo="insert into wo_po_break_down_his (id, approval_page, po_id, approved_no, job_no_mst, po_number, pub_shipment_date, excess_cut, po_received_date, po_quantity, unit_price, plan_cut, country_name, po_total_price, shipment_date, is_deleted, is_confirmed, details_remarks, delay_for, packing, grouping, projected_po_id, tna_task_from_upto, inserted_by, insert_date, updated_by, update_date, status_active, shiping_status, original_po_qty, factory_received_date, original_avg_price, pp_meeting_date, matrix_type, no_of_carton, actual_po_no, round_type, doc_sheet_qty, up_charge, pack_price, sc_lc, with_qty, extended_ship_date, sewing_company_id, sewing_location_id, extend_ship_mode, sea_discount, air_discount, job_id, pack_handover_date, etd_ldd, file_year, file_no, rfi_date)
                select '', 86, id, $approved_string_mst2, job_no_mst, po_number, pub_shipment_date, excess_cut, po_received_date, po_quantity, unit_price, plan_cut, country_name, po_total_price, shipment_date, is_deleted, is_confirmed, details_remarks, delay_for, packing, grouping, projected_po_id, tna_task_from_upto, inserted_by, insert_date, updated_by, update_date, status_active, shiping_status, original_po_qty, factory_received_date, original_avg_price, pp_meeting_date, matrix_type, no_of_carton, actual_po_no, round_type, doc_sheet_qty, up_charge, pack_price, sc_lc, with_qty, extended_ship_date, sewing_company_id, sewing_location_id, extend_ship_mode, sea_discount, air_discount, job_id, pack_handover_date, txt_etd_ldd, file_year, file_no, rfi_date from wo_po_break_down where job_id in ($job_ids) and is_deleted=0";
            //echo "10**".$sqlpo;die;
            
            //------------wo_po_color_size_his----------------------------------
            $sqlcolorsize="insert into wo_po_color_size_his (id, approval_page, color_size_id, approved_no, po_break_down_id, job_no_mst, color_mst_id, size_mst_id, item_mst_id, country_mst_id, article_number, item_number_id, country_id, size_number_id, color_number_id, order_quantity, order_rate, order_total, excess_cut_perc, plan_cut_qnty, is_deleted, is_used, inserted_by, insert_date, updated_by, update_date, status_active, is_locked, cutup_date, cutup, country_ship_date, shiping_status, country_remarks, country_type, packing, color_order, size_order, ultimate_country_id, code_id, ul_country_code, pack_qty, pcs_per_pack, pack_type, pcs_pack, assort_qty, solid_qty, barcode_suffix_no, barcode_year, barcode_no, job_id, extended_ship_date, proj_qty, proj_amt, country_avg_rate)
                Select '', 86, id, $approved_string_mst2, po_break_down_id, job_no_mst, color_mst_id, size_mst_id, item_mst_id, country_mst_id, article_number, item_number_id, country_id, size_number_id, color_number_id, order_quantity, order_rate, order_total, excess_cut_perc, plan_cut_qnty, is_deleted, is_used, inserted_by, insert_date, updated_by, update_date, status_active, is_locked, cutup_date, cutup, country_ship_date, shiping_status, country_remarks, country_type, packing, color_order, size_order, ultimate_country_id, code_id, ul_country_code, pack_qty, pcs_per_pack, pack_type, pcs_pack, assort_qty, solid_qty, barcode_suffix_no, barcode_year, barcode_no, job_id, extended_ship_date, proj_qty, proj_amt, country_avg_rate from wo_po_color_size_breakdown where job_id in ($job_ids) and is_deleted=0 ";	
            //echo "10**".$sqlcolorsize;die;
            
            //------------wo_pre_cost_mst_histry----------------------------------
            $sqlBom="insert into wo_pre_cost_mst_histry(id, approved_no, pre_cost_mst_id, garments_nature, job_no, costing_date, incoterm, incoterm_place, machine_line, prod_line_hr, costing_per, remarks, copy_quatation, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, efficiency_wastage_percent, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, ready_to_approved, budget_minute, sew_efficiency_source, entry_from, job_id, refusing_cause, sourcing_date, sourcing_inserted_date, sourcing_inserted_by, sourcing_update_date, sourcing_updated_by, sourcing_ready_to_approved, sourcing_approved, sourcing_remark, main_fabric_co, sourcinng_refusing_cause, approved_sequ_by, isorder_change, ready_to_source, approval_page)
                select '', $approved_string_mst, id, garments_nature, job_no, costing_date, incoterm, incoterm_place, machine_line, prod_line_hr, costing_per, remarks, copy_quatation, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, efficiency_wastage_percent, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, ready_to_approved, budget_minute, sew_efficiency_source, entry_from, job_id, refusing_cause, sourcing_date, sourcing_inserted_date, sourcing_inserted_by, sourcing_update_date, sourcing_updated_by, sourcing_ready_to_approved, sourcing_approved, sourcing_remark, main_fabric_co, sourcinng_refusing_cause, approved_sequ_by, isorder_change, ready_to_source, 86
            from wo_pre_cost_mst where job_id in ($job_ids)";
           // echo "10**".$sqlBom;die;
            
            //------------wo_pre_cost_dtls_histry----------------------------------
            $sql_bom_dtls="insert into wo_pre_cost_dtls_histry(id, approved_no, pre_cost_dtls_id, job_no, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, commission, commission_percent,	lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, cost_pcs_set, cost_pcs_set_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche, margin_pcs_bom, margin_bom_per, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, depr_amor_pre_cost, depr_amor_po_price, interest_cost, interest_percent, incometax_cost, incometax_percent, deffdlc_cost, deffdlc_percent, design_cost, design_percent, studio_cost, studio_percent, job_id, sourcing_inserted_date, sourcing_inserted_by, sourcing_update_date, sourcing_updated_by, sourcing_fabric_cost, sourcing_trims_cost, sourcing_embel_cost, sourcing_wash_cost, approval_page)
                    select '', $approved_string_dtls, id, job_no, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, commission, commission_percent,	lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, cost_pcs_set, cost_pcs_set_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche, margin_pcs_bom, margin_bom_per, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, depr_amor_pre_cost, depr_amor_po_price, interest_cost, interest_percent, incometax_cost, incometax_percent, deffdlc_cost, deffdlc_percent, design_cost, design_percent, studio_cost, studio_percent, job_id, sourcing_inserted_date, sourcing_inserted_by, sourcing_update_date, sourcing_updated_by, sourcing_fabric_cost, sourcing_trims_cost, sourcing_embel_cost, sourcing_wash_cost, 86 from wo_pre_cost_dtls where job_id in ($job_ids)";
            //echo "10**".$sql_bom_dtls;die;

            //------------wo_pre_cost_fabric_cost_dtls_h----------------------------------
            $sql_fabric_cost_dtls="insert into wo_pre_cost_fabric_cost_dtls_h(id, approved_no, pre_cost_fabric_cost_dtls_id, job_no, item_number_id, body_part_id, fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, color_size_sensitive, color, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, company_id, costing_per, consumption_basis, process_loss_method, cons_breack_down, msmnt_break_down, color_break_down, yarn_breack_down, marker_break_down, width_dia_type, avg_cons_yarn, gsm_weight_yarn, uom, body_part_type, sample_id, job_id, gsm_weight_type, nominated_supp_multi, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, sourcing_nominated_supp, quotdtlsid, budget_on, source_id, is_synchronized, approval_page)
                select '', $approved_string_dtls, id, job_no, item_number_id, body_part_id, fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, color_size_sensitive, color, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, company_id, costing_per, consumption_basis, process_loss_method, cons_breack_down, msmnt_break_down, color_break_down, yarn_breack_down, marker_break_down, width_dia_type, avg_cons_yarn, gsm_weight_yarn, uom, body_part_type, sample_id, job_id, gsm_weight_type, nominated_supp_multi, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, sourcing_nominated_supp, quotdtlsid, budget_on, source_id, is_synchronized, 86 from wo_pre_cost_fabric_cost_dtls where job_id in ($job_ids)";
            //echo "10**".$sql_fabric_cost_dtls;die;
            
            //------------WO_PRE_FAB_AVG_CON_DTLS_H----------------------------------
            $sql_fabric_cons_dtls="insert into wo_pre_fab_avg_con_dtls_h(id, approved_no, fab_con_id, pre_cost_fabric_cost_dtls_id, job_no, po_break_down_id, color_number_id, gmts_sizes, dia_width, item_size, cons, process_loss_percent, requirment, pcs, color_size_table_id, body_length, body_sewing_margin, body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin, front_rise_length, front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin, total, marker_dia, marker_yds, marker_inch, gmts_pcs, marker_length, net_fab_cons, rate, amount, remarks, length, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, length_sleeve, width_sleeve, job_id, fina_char, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, cons_pcs, item_color, approval_page)
                select '', $approved_string_dtls, id,  pre_cost_fabric_cost_dtls_id, job_no, po_break_down_id, color_number_id, gmts_sizes, dia_width, item_size, cons, process_loss_percent, requirment, pcs, color_size_table_id, body_length, body_sewing_margin, body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin, front_rise_length, front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin, total, marker_dia, marker_yds, marker_inch, gmts_pcs, marker_length, net_fab_cons, rate, amount, remarks, length, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, length_sleeve, width_sleeve, job_id, fina_char, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, cons_pcs, item_color, 86 from wo_pre_cos_fab_co_avg_con_dtls where job_id in ($job_ids)";
            //echo "10**"$sql_fabric_cons_dtls;die;
            
            //-------------wo_pre_fab_concolor_dtls_h-----------------------------------------------
            $sql_concolor_cst="insert into wo_pre_fab_concolor_dtls_h (id, approved_no, contrast_id, pre_cost_fabric_cost_dtls_id, job_no, gmts_color_id, gmts_color, contrast_color_id, job_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, approval_page)
                select
                '', $approved_string_dtls, id, pre_cost_fabric_cost_dtls_id, job_no, gmts_color_id, gmts_color, contrast_color_id, job_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, 86 from wo_pre_cos_fab_co_color_dtls where job_id in ($job_ids)";
            //echo "10**".$sql_concolor_cst;die;
            
            //-------------wo_pre_stripe_color_h-----------------------------------------------
            $sql_stripecolor_cst="insert into wo_pre_stripe_color_h (id, approved_no, stripe_id, job_no, item_number_id, pre_cost_fabric_cost_dtls_id, color_number_id, stripe_color, measurement, uom, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, totfidder, fabreq, fabreqtotkg, yarn_dyed, sales_dtls_id, size_number_id, po_break_down_id, lenth, width, sample_color, sample_per, cons, excess_per, job_id, stripe_type, approval_page)
                select
                '', $approved_string_dtls, id, job_no, item_number_id, pre_cost_fabric_cost_dtls_id, color_number_id, stripe_color, measurement, uom, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, totfidder, fabreq, fabreqtotkg, yarn_dyed, sales_dtls_id, size_number_id, po_break_down_id, lenth, width, sample_color, sample_per, cons, excess_per, job_id, stripe_type, 86 from wo_pre_stripe_color where job_id in ($job_ids)";
            //echo "10**".$sql_stripecolor_cst;die;

            //-------------wo_pre_cost_fab_yarn_cst_dtl_h-----------------------------------------------
            $sql_precost_fab_yarn_cst="insert into wo_pre_cost_fab_yarn_cst_dtl_h (id, approved_no, pre_cost_fab_yarn_cost_dtls_id, fabric_cost_dtls_id, job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, avg_cons_qnty, supplier_id, color, consdznlbs, rate_dzn, job_id, yarn_finish, yarn_spinning_system, certification, approval_page)
                select
                '', $approved_string_dtls, id, fabric_cost_dtls_id, job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, avg_cons_qnty, supplier_id, color, consdznlbs, rate_dzn, job_id, yarn_finish, yarn_spinning_system, certification, 86 from wo_pre_cost_fab_yarn_cost_dtls where job_id in ($job_ids)";
                //echo "10**".$sql_precost_fab_yarn_cst;die;
                
            //-----------------------------------------wo_pre_cost_fab_con_cst_dtls_h-----------------------------------------
            $sql_precost_fab_con_cst_dtls="insert into  wo_pre_cost_fab_con_cst_dtls_h(id, approved_no, pre_cost_fab_conv_cst_dtls_id, job_no, fabric_description, cons_process, req_qnty, charge_unit, amount, color_break_down, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, avg_req_qnty, process_loss, job_id, approval_page)
                select '', $approved_string_dtls, id, job_no, fabric_description, cons_process, req_qnty, charge_unit, amount, color_break_down, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, avg_req_qnty, process_loss, job_id, 86 from wo_pre_cost_fab_conv_cost_dtls where job_id in ($job_ids)";
            //echo "10**".$sql_precost_fab_con_cst_dtls;die;
                
            //-------------------  WO_PRE_CONV_COLOR_DTLS_H------------------------------------------------------------
            $sql_conv_color_dtls="insert into wo_pre_conv_color_dtls_h(id, approved_no, conv_color_id, fabric_cost_dtls_id, conv_cost_dtls_id, job_no, job_id, convchargelibraryid, gmts_color_id, fabric_color_id, cons, unit_charge, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, approval_page)
                select '', $approved_string_dtls, id, fabric_cost_dtls_id, conv_cost_dtls_id, job_no, job_id, convchargelibraryid, gmts_color_id, fabric_color_id, cons, unit_charge, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, 86 from wo_pre_cos_conv_color_dtls where job_id in ($job_ids)";
            //echo "10**".$sql_conv_color_dtls;die;
                
            //------------wo_pre_cost_trim_cost_dtls_his------------------------------	----------------------
            $sql_precost_trim_cost_dtls="insert into  wo_pre_cost_trim_cost_dtls_his(id, approved_no, pre_cost_trim_cost_dtls_id, job_no, trim_group, description, brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, cons_breack_down, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, remark, country, calculatorstring, unit_price, inco_term, add_price, seq, job_id, nominated_supp_multi, material_source, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, sourcing_nominated_supp, tot_cons, ex_per, quotdtlsid, source_id, item_print, is_synchronized, approval_page)
                select '', $approved_string_dtls, id, job_no, trim_group, description, brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, cons_breack_down, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, remark, country, calculatorstring, unit_price, inco_term, add_price, seq, job_id, nominated_supp_multi, material_source, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, sourcing_nominated_supp, tot_cons, ex_per, quotdtlsid, source_id, item_print, is_synchronized, 86 from wo_pre_cost_trim_cost_dtls  where job_id in ($job_ids)";
            //echo "10**".$sql_precost_trim_cost_dtls;die;

            //---------------------------wo_pre_cost_trim_co_cons_dtl_h--------------------------------------------------
            $sql_precost_trim_co_cons_dtl="insert into wo_pre_cost_trim_co_cons_dtl_h( id, approved_no, pre_cost_trim_co_cons_dtls_id, wo_pre_cost_trim_cost_dtls_id, job_no, po_break_down_id, item_size, cons, place, pcs, country_id, excess_per, tot_cons, ex_cons, item_number_id, color_number_id, item_color_number_id, size_number_id, rate, amount, gmts_pcs, color_size_table_id, job_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, rate_cal_data, sourcing_rate, sourcing_amount, sourcing_update_date, sourcing_updated_by, sourcing_inserted_date, sourcing_inserted_by, cons_pcs, approval_page)
                select '', $approved_string_dtls, id, wo_pre_cost_trim_cost_dtls_id, job_no, po_break_down_id, item_size, cons, place, pcs, country_id, excess_per, tot_cons, ex_cons, item_number_id, color_number_id, item_color_number_id, size_number_id, rate, amount, gmts_pcs, color_size_table_id, job_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, rate_cal_data, sourcing_rate, sourcing_amount, sourcing_update_date, sourcing_updated_by, sourcing_inserted_date, sourcing_inserted_by, cons_pcs, 86 from wo_pre_cost_trim_co_cons_dtls where job_id in ($job_ids)";
            //echo "10**".$sql_precost_trim_co_cons_dtl;die;

            //-------------------  wo_pre_cost_embe_cost_dtls_his------------------------------------------------------------
            $sql_precost_embe_cost_dtls="insert into wo_pre_cost_embe_cost_dtls_his(id, approved_no, pre_cost_embe_cost_dtls_id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, budget_on, country, body_part_id, job_id, nominated_supp_multi, sourcing_nominated_supp, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, quotdtlsid, is_synchronized, approval_page)
                select '', $approved_string_dtls, id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, budget_on, country, body_part_id, job_id, nominated_supp_multi, sourcing_nominated_supp, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, quotdtlsid, is_synchronized, 86 from wo_pre_cost_embe_cost_dtls where job_id in ($job_ids)";
            //echo "10**".$sql_precost_embe_cost_dtls;die;
                
            //-------------------  WO_PRE_EMB_AVG_CON_DTLS_H------------------------------------------------------------
            $sql_embe_cons_dtls="insert into wo_pre_emb_avg_con_dtls_h(id, approved_no, emb_cons_id, pre_cost_emb_cost_dtls_id, job_no, po_break_down_id, item_number_id, color_number_id, size_number_id, requirment, rate, amount, gmts_pcs, color_size_table_id, rate_lib_id, country_id, job_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, approval_page)
                select '', $approved_string_dtls, id, pre_cost_emb_cost_dtls_id, job_no, po_break_down_id, item_number_id, color_number_id, size_number_id, requirment, rate, amount, gmts_pcs, color_size_table_id, rate_lib_id, country_id, job_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, 86 from wo_pre_cos_emb_co_avg_con_dtls where job_id in ($job_ids)";
            //echo "10**".$sql_embe_cons_dtls;die;
            
            //----------------------------------wo_pre_cost_comarc_cost_dtls_h----------------------------------------
            $sql_comarc_cost_dtls="insert into wo_pre_cost_comarc_cost_dtls_h( id, approved_no, pre_cost_comarci_cost_dtls_id, job_no, item_id, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, job_id, approval_page)
                select '', $approved_string_dtls, id, job_no, item_id, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, job_id, 86 from wo_pre_cost_comarci_cost_dtls where job_id in ($job_ids)";
            //echo "10**".$sql_comarc_cost_dtls;die;

            //-------------------------------------wo_pre_cost_commis_cost_dtls_h-------------------------------------------
            $sql_commis_cost_dtls="insert into wo_pre_cost_commis_cost_dtls_h (id, approved_no, pre_cost_commiss_cost_dtls_id, job_no, particulars_id, commission_base_id, commision_rate, commission_amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, job_id, approval_page)
                select '', $approved_string_dtls, id, job_no, particulars_id, commission_base_id, commision_rate, commission_amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, job_id, 86 from wo_pre_cost_commiss_cost_dtls where job_id in ($job_ids)";
            //echo "10**".$sql_commis_cost_dtls;die;
            
            //-------------------------------------wo_pre_cost_sum_dtls_histroy-------------------------------------------
            $sql_sum_dtls="insert into wo_pre_cost_sum_dtls_histroy (id, approved_no, pre_cost_sum_dtls_id, job_no, fab_yarn_req_kg, fab_woven_req_yds, fab_knit_req_kg, fab_amount, avg, yarn_cons_qnty, yarn_amount, conv_req_qnty, conv_charge_unit, conv_amount, trim_cons, trim_rate, trim_amount, emb_amount, comar_rate, comar_amount, commis_rate, commis_amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, fab_woven_fin_req_yds, fab_knit_fin_req_kg, job_id, approval_page)
            select '', $approved_string_dtls, id, job_no, fab_yarn_req_kg, fab_woven_req_yds, fab_knit_req_kg, fab_amount, avg, yarn_cons_qnty, yarn_amount, conv_req_qnty, conv_charge_unit, conv_amount, trim_cons, trim_rate, trim_amount, emb_amount, comar_rate, comar_amount, commis_rate, commis_amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, fab_woven_fin_req_yds, fab_knit_fin_req_kg, job_id, 86 from wo_pre_cost_sum_dtls where job_id in ($job_ids)";
            //echo "10**".$sql_sum_dtls;die;
            
            if($flag == 1)//JOB
            {
                $rID=execute_query($sqljob,1);
                if($rID == 1) $flag=1; else $flag=0;
            }
            
            if($flag == 1)//JOB SET ITEM
            {
                $rID0=execute_query($sqlsetitem,1);
                if($rID0==1) $flag=1; else $flag=0;
            }
            
            if($flag == 1)//JOB PO
            {
                $rID1=execute_query($sqlpo,1);
                if($rID1==1) $flag=1; else $flag=0;
            }
            
            if($flag == 1)//JOB PO COLOR SIZE
            {
                $rID2=execute_query($sqlcolorsize,1);
                if($rID2==1) $flag=1; else $flag=0;
            }
            
            if($flag == 1)//BOM MST
            {
                $rID3=execute_query($sqlBom,1);
                if($rID3==1) $flag=1; else $flag=0;
            }
            
            if($flag == 1)//BOM DTLS
            {
                $rID4=execute_query($sql_bom_dtls,1);
                if($rID4==1) $flag=1; else $flag=0;
            }
            
            if($flag == 1)//BOM FABRIC DTLS
            {
                $rID5=execute_query($sql_fabric_cost_dtls,1);
                if($rID5==1) $flag=1; else $flag=0;
            }

            //echo $sql_fabric_cost_dtls;die;
            
            if($flag == 1)//BOM FABRIC CONS
            {
                $rID6=execute_query($sql_fabric_cons_dtls,1);
                if($rID6==1) $flag=1; else $flag=0;
            }
            
            if($flag == 1)//BOM FABRIC CONTRAST COLOR
            {
                $rID7=execute_query($sql_concolor_cst,1);
                if($rID7==1) $flag=1; else $flag=0;
            }
            
            if($flag == 1)//BOM FABRIC STRIPE COLOR
            {
                $rID8=execute_query($sql_stripecolor_cst,1);
                if($rID8==1) $flag=1; else $flag=0;
            }
            
            if($flag == 1)//BOM YARN
            {
                $rID9=execute_query($sql_precost_fab_yarn_cst,1);
                if($rID9==1) $flag=1; else $flag=0;
            }
            
            if($flag == 1)//BOM CONV COST
            {
                $rID10=execute_query($sql_precost_fab_con_cst_dtls,1);
                if($rID10==1) $flag=1; else $flag=0;
            }
            
            if($flag == 1)//BOM CONV COLOR
            {
                $rID11=execute_query($sql_conv_color_dtls,1);
                if($rID11==1) $flag=1; else $flag=0;
            }
            
            if($flag == 1)//BOM TRIM
            {
                $rID12=execute_query($sql_precost_trim_cost_dtls,1);
                if($rID12==1) $flag=1; else $flag=0;
            }
            
            if($flag == 1)//BOM TRIM CONS
            {
                $rID13=execute_query($sql_precost_trim_co_cons_dtl,1);
                if($rID13==1) $flag=1; else $flag=0;
            }
            
            if($flag == 1)//BOM EMB
            {
                $rID14=execute_query($sql_precost_embe_cost_dtls,1);
                if($rID14==1) $flag=1; else $flag=0;
            }
            
            if($flag == 1)//BOM EMB CONS
            {
                $rID15=execute_query($sql_embe_cons_dtls,1);
                if($rID15==1) $flag=1; else $flag=0;
            }
            
            if($flag == 1)//BOM COMMARCIAL
            {
                $rID16=execute_query($sql_comarc_cost_dtls,1);
                if($rID16==1) $flag=1; else $flag=0;
            }
            
            if($flag == 1)//BOM COMMISION
            {
                $rID17=execute_query($sql_commis_cost_dtls,1);
                if($rID17==1) $flag=1; else $flag=0;
            }
            if($flag == 1)//BOM SUM DTLS
            {
                $rID18=execute_query($sql_sum_dtls,1);
                if($rID18==1) $flag=1; else $flag=0;
            }
        }
		
		// echo "21**".$rID1.",".$rID2.",".$rID3.",".$rID4;oci_rollback($con);die;
		
		if($flag==1) $msg='19'; else $msg='21';

        
	}
	else
	{            
		
		$next_user_app = sql_select("select id from approval_history where mst_id in($booking_ids) and entry_form=86 and sequence_no >$user_sequence_no and current_approval_status=1 group by id");
				
		if(count($next_user_app)>0)
		{
			echo "25**unapproved"; 
			disconnect($con);
			die;
		}

		$rID1=sql_multirow_update("wo_pre_cost_mst","SOURCING_HA_APPROVED*SOURCING_APPROVED*SOURCING_READY_TO_APPROVED*APPROVED_SEQU_BY_SOURCE*HA_APP_SEQU_BY_SOURCE",'0*0*0*0*0',"id",$booking_ids,0);
		if($rID1) $flag=1; else $flag=0;


		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form in(47,86) and mst_id in ($booking_ids)";
			$rID2=execute_query($query,1);
			if($rID2) $flag=1; else $flag=0;
		}

		
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form in(47,86) and mst_id in ($booking_ids)";
			$rID3=execute_query($query,1); 
			if($rID3) $flag=1; else $flag=0; 
		}
		

		
		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by=".$user_id_approval.", un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=86 and current_approval_status=1 and mst_id in ($booking_ids)";
			$rID4=execute_query($query,1);
			//echo $rID4;
			if($rID4) $flag=1; else $flag=0;
		}

		if($flag==1)
		{
			$query="UPDATE fabric_booking_app_cause_source SET VALID=0 WHERE entry_form=86 and VALID=1 and approval_type=2 and BOOKING_ID in ($booking_ids)";
			$rID5=execute_query($query,1);
			if($rID5) $flag=1; else $flag=0;
		}

 		
		// echo "5**".$rID12.",".$rID13.",".$rID14.",".$rID15.$flag;oci_rollback($con);die;
		
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
                    	<!--<td align="center"><?//=$row[csf('image_location')];?></td>-->
                    	<td align="center"><img width="300px" height="180px" src="../../<?=$row[csf('image_location')]; ?>" /></td>
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
		$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation,b.sequence_no from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and valid=1   and b.is_deleted=0  and  b.entry_form=86  order by b.sequence_no";
		 // echo $sql;die;
		$arr=array (2=>$custom_designation,3=>$Department);
		echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department,Seq", "100,120,150,150,30,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id,0", $arr , "user_name,user_full_name,designation,department_id,sequence_no", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
		?>
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
			http.open("POST","sourcing_higher_authority_approval_controller.php",true);
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
				alert("Data not saved");
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
						<input type="text" name="txt_refusing_cause" id="txt_refusing_cause" class="text_boxes" style="width:320px;height: 100px;" value="<?=$sourcinng_refusing_cause; ?>" />
						<input type="hidden" name="hidden_quo_id" id="hidden_quo_id" value="<? echo $quo_id;?>">
					</td>
                  </tr>
                  <tr>
					<td colspan="4" align="center" class="button_container">
						<?
					     echo load_submit_buttons( $permission, "fnc_cause_info", 0,0 ,"reset_form('causeinfo_1','','')",1);
				        ?> </br>
				        <input type="button" class="formbutton" value="Close" name="close_buttons" id="close_buttons" onClick="set_values();" style="width:50px;height: 35px;">
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
		
		$get_history = sql_select("SELECT id from approval_history where mst_id='$quo_id' and entry_form =86 and current_approval_status=1");
		$id=return_next_id( "id", "refusing_cause_history", 1);
		$field_array = "id, entry_form, mst_id, refusing_reason,inserted_by, insert_date";
		$data_array = "(".$id.",86,".$quo_id.",'".$refusing_cause."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		//echo "10**insert into refusing_cause_history (".$field_array.") values ".$data_array; die;
		$field_array_update ="un_approved_by*un_approved_date*current_approval_status*un_approved_reason* updated_by*update_date";
		$data_array_update = "".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*".$refusing_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_insert("refusing_cause_history",$field_array,$data_array,1);
		$rID2=execute_query("update wo_pre_cost_mst set sourcing_ready_to_approved=0, sourcing_approved=0, sourcinng_refusing_cause='".$refusing_cause."',HA_APP_SEQU_BY_SOURCE=0, sourcing_updated_by=".$_SESSION['logic_erp']['user_id'].", sourcing_update_date = '".$pc_date_time."' where id='$quo_id'");
		//echo "10**update wo_pre_cost_mst set sourcing_ready_to_approved=0, sourcing_approved=0, sourcinng_refusing_cause='".$refusing_cause."', sourcing_updated_by=".$_SESSION['logic_erp']['user_id'].", sourcing_update_date = '".$pc_date_time."' where id='$quo_id'"; die;
		$rID3=1;
		if(count($get_history)>0)
		{
			$rID3=execute_query("update approval_history set un_approved_by=".$_SESSION['logic_erp']['user_id'].", un_approved_date='".$pc_date_time."', current_approval_status =0, un_approved_reason= '".$refusing_cause."', updated_by = ".$_SESSION['logic_erp']['user_id'].", update_date = '".$pc_date_time."' where mst_id='$quo_id' and entry_form =86 and current_approval_status=1");
		}

		 
		$query="delete from approval_mst  WHERE entry_form=86 and mst_id in ($quo_id)";
		$rID3=execute_query($query,1); 
		if($rID3) $flag=1; else $flag=0; 
		 

        if($rID && $rID2)
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


if($action=="populate_cm_compulsory")
{
	$cm_cost_compulsory=return_field_value("cm_cost_compulsory","variable_order_tracking","company_name ='".$data."' and variable_list=22 and is_deleted=0 and status_active=1");
	echo $cm_cost_compulsory;
	exit();
}
?>
