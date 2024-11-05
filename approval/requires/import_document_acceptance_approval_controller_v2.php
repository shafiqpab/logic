<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_ip=$_SESSION['logic_erp']['user_ip'];

extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');
	
$user_id = $_SESSION['logic_erp']['user_id'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$menu_id = $_SESSION['menu_id'];

$permissionSql = "SELECT approve_priv FROM user_priv_mst where user_id=$user_id AND main_menu_id = $menu_id";
$permissionCheck = sql_select( $permissionSql ); 
$approvePermission = $permissionCheck[0][csf('approve_priv')];

//==========================================================================================
$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );


if ($action=="load_supplier_dropdown")
{
	$data = explode('_',$data);	
	echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]'  and c.status_active=1 and c.is_deleted=0 order by   c.supplier_name ",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
	exit();
}    




if($action=="load_drop_down_buyer")
{
    echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
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


if ($action=="load_supplier_dropdown_pi_new")
{
	$data = explode('_',$data);	
	echo create_drop_down( "cbo_pi_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type =9 and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
	exit();
}

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




if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_name = str_replace("'","",$cbo_company_name);
    $txt_date = str_replace("'","",$txt_date);      
    $txt_invoice_no = str_replace("'","",$txt_invoice_no);
	$approval_type = str_replace("'","",$cbo_approval_type);
    $txt_alter_user_id = str_replace("'","",$txt_alter_user_id);
	$cbo_supplier_id = str_replace("'","",$cbo_supplier_id);
    $cbo_get_upto = str_replace("'","",$cbo_get_upto);
    $user_id_approval=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;

    $electronicDataArr=getSequence(array('company_id'=>$cbo_company_name,'ENTRY_FORM'=>38,'user_id'=>$user_id_approval,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'product_dept_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0));
       //print_r( $buyer_arr);

	
	if ($txt_invoice_no!="") $where_con .= " and a.INVOICE_NO='$txt_invoice_no'";	
	if ($company_name!=0) $where_con .= " and c.importer_id=$company_name";	
	if ($cbo_supplier_id!=0) $where_con .= " and c.supplier_id=$cbo_supplier_id";	
	if($cbo_supplier_id!=0){$where_con .=" and c.SUPPLIER_ID=".$cbo_supplier_id.""; }
	
	if($txt_date!="")
	{
		$txt_date=change_date_format($txt_date,"yyyy-mm-dd","-",1);
		if($cbo_get_upto==1) $where_con .=" and a.invoice_date>'".$txt_date."'";
		else if($cbo_get_upto==2) $where_con .=" and a.invoice_date<='".$txt_date."'";
		else if($cbo_get_upto==3) $where_con .=" and a.invoice_date='".$txt_date."'";

	}



    //$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
    $dealing_merchant_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info", 'id', 'team_member_name');
    $team_leader_arr = return_library_array("select id,team_leader_name from lib_marketing_team where project_type=1 and status_active =1 and is_deleted=0 order by team_leader_name", 'id', 'team_leader_name');
	

	if($approval_type==0) // unapproval process start
	{  
       	//Match data..................................
		// if($electronicDataArr['user_by'][$user_id_approval]['BUYER_ID']){
		// 	$where_con .= " and a.BUYER_ID in(".$electronicDataArr['user_by'][$user_id_approval]['BUYER_ID'].",0)";
		// 	$electronicDataArr['sequ_by'][0]['BUYER_ID']=$electronicDataArr['user_by'][$user_id_approval]['BUYER_ID'];
        
		// }

       $data_mast_sql="SELECT a.ID, C.SUPPLIER_ID
           from com_import_invoice_mst a, com_import_invoice_dtls b, com_btb_lc_master_details c 
           where c.id = a.btb_lc_id AND b.import_invoice_id = a.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.ready_to_approved=1 and a.APPROVED<>1 $where_con group by a.ID, C.SUPPLIER_ID";
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
				$sql .= "SELECT a.ID, a.INVOICE_NO, a.INVOICE_DATE, a.BANK_REF, a.BANK_ACC_DATE,a.COMPANY_ACC_DATE, a.SHIPMENT_DATE, a.NAGOTIATE_DATE, a.BILL_DATE, a.IS_LC, a.ACCEPTANCE_TIME, a.is_posted_account, c.LC_NUMBER, C.LC_TYPE_ID, c.LC_DATE, c.SUPPLIER_ID, c.IMPORTER_ID, c.PI_ID, c.PI_VALUE, c.MATURITY_FROM_ID, sum(b.current_acceptance_value) as CURRENT_ACCEPTANCE_VALUE
                from com_import_invoice_mst a, com_import_invoice_dtls b, com_btb_lc_master_details c 
                where c.id = a.btb_lc_id AND b.import_invoice_id = a.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.ready_to_approved=1  and a.APPROVED<>1  and a.APPROVED_SEQU_BY=$seq $where_con $sys_con group by a.ID, a.INVOICE_NO, a.INVOICE_DATE, a.BANK_REF, a.BANK_ACC_DATE,a.COMPANY_ACC_DATE, a.SHIPMENT_DATE, a.NAGOTIATE_DATE, a.BILL_DATE, a.IS_LC, a.ACCEPTANCE_TIME, a.is_posted_account, c.LC_NUMBER, C.LC_TYPE_ID, c.LC_DATE, c.SUPPLIER_ID, c.IMPORTER_ID, c.PI_ID, c.PI_VALUE, c.MATURITY_FROM_ID";
			}
		}
	}
	 else // 
     {
		$sql="SELECT a.ID, a.INVOICE_NO, a.INVOICE_DATE, a.BANK_REF, a.BANK_ACC_DATE,a.COMPANY_ACC_DATE, a.SHIPMENT_DATE, a.NAGOTIATE_DATE, a.BILL_DATE, a.IS_LC, a.ACCEPTANCE_TIME, a.is_posted_account, c.LC_NUMBER, C.LC_TYPE_ID, c.LC_DATE, c.SUPPLIER_ID, c.IMPORTER_ID, c.PI_ID, c.PI_VALUE, c.MATURITY_FROM_ID, sum(b.current_acceptance_value) as CURRENT_ACCEPTANCE_VALUE
        from com_import_invoice_mst a, com_import_invoice_dtls b, com_btb_lc_master_details c,APPROVAL_MST d 
        where c.id = a.btb_lc_id AND b.import_invoice_id = a.id  and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.ready_to_approved=1  and d.entry_form=38  and a.APPROVED<>0 and a.APPROVED_SEQU_BY=d.SEQUENCE_NO and d.SEQUENCE_NO={$electronicDataArr['user_by'][$user_id_approval]['SEQUENCE_NO']} $where_con  group by a.id, a.invoice_no, a.invoice_date, a.bank_ref, a.bank_acc_date,a.COMPANY_ACC_DATE, a.shipment_date, a.nagotiate_date, a.bill_date, a.is_lc, a.acceptance_time, a.is_posted_account, c.lc_number, c.lc_type_id, c.lc_date, c.supplier_id, c.importer_id, c.pi_id, c.pi_value, c.maturity_from_id ";
	 } 
	  
     
    //echo $sql; 



     $nameArray = sql_select( $sql );
	 foreach($nameArray as $rows){
		$pi_str_arr[]=$rows['PI_ID'];
	 }
	 // $pi_arr=implode(',',array_unique(explode(',',implode(',',$pi_str_arr))));
	
        $piid_arr_cond=array_chunk($pi_str_arr,1000, true);
        $pi_arr="";
        $pi_arr1="";
        $k=0;
        foreach($piid_arr_cond as $key=>$value)
        {
	   if($k==0)
	   {
		$pi_arr=" and a.id  in(".implode(",",$value).")";
        $pi_arr1=" and a.pi_id  in(".implode(",",$value).")";
	
	   }
	   else
	   {
		$pi_arr.=" or a.id  in(".implode(",",$value).")";
        $pi_arr1.=" or a.pi_id  in(".implode(",",$value).")";
		
	   }
	   $k++;
	 }



        $piSql="select a.ID,a.SUPPLIER_ID,a.PI_NUMBER,a.PI_DATE,a.TOTAL_AMOUNT,a.NET_TOTAL_AMOUNT from com_pi_master_details a where  a.IMPORTER_ID=$company_name  $pi_arr and a.STATUS_ACTIVE=1 and a.IS_DELETED=0";

        $piSqlResult = sql_select( $piSql );
        foreach($piSqlResult as $rows){
            $pi_data_arr['pi_number'][$rows['ID']]=$rows['PI_NUMBER'];
            $pi_data_arr['pi_amount'][$rows['ID']]=$rows['NET_TOTAL_AMOUNT'];
            $pi_data_arr['pi_date'][$rows['ID']]=$rows['PI_DATE'];
        }
	
	
        $sqlJob="select a.PI_ID, c.DEALING_MARCHANT,c.TEAM_LEADER
        from com_pi_item_details a, wo_booking_dtls b, wo_po_details_master c 
        where a.work_order_dtls_id = b.id and b.job_no = c.job_no  and a.is_deleted = 0 and a.status_active = 1 and b.is_deleted = 0 and b.status_active = 1 and c.is_deleted = 0 and c.status_active = 1 $pi_arr1";
        $sqlJobResult = sql_select( $sqlJob );
        foreach($sqlJobResult as $rows){
            $job_data_arr['dealing_marchant'][$rows['PI_ID']]=$rows['DEALING_MARCHANT'];
            $job_data_arr['team_leader'][$rows['PI_ID']]=$rows['TEAM_LEADER'];
        }
	
        $width=1480; 
        ob_start();   
        ?>
        
        <form name="piApproval_2" id="piApproval_2">
            <fieldset style="width:<? echo $width+21; ?>px; margin-top:10px">
                <legend>PI Approval</legend>	
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width; ?>" class="rpt_table" align="left">
                    <thead>
                        <th width="35"></th>
                        <th width="30">SL</th>
                        <th width="60">Invoice No</th>
                        <th width="70">Invoice Date</th>
                        <th width="60">Image/File</th>
                        <th width="100">LC No</th>
                        <th width="100">LC TYPE</th>
                        <th width="70">LC Date</th>
                        <th width="90">PI No</th>
                        <th width="80">PI Date</th>
                        <th width="100">Bank Ref No</th>
                        <th width="80">Maturity Date</th>
                        <th width="80">Bank Acceptance Date</th>
                        <th width="80">Company Acceptance Date</th>
                        <th width="70">Current Acceptance Value </th>
                        <th width="100">Supplier Name</th>
                        <th width="90">Acceptance Time</th>
                        <th width="100">Team Leader</th>
                        <th>Delling Merchant</th>
                    </thead>
                </table>            
                <div style="width:<? echo $width+21; ?>px; overflow-y:scroll; max-height:330px;" id="pi_approve_unapprove_list_view" >
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width; ?>" class="rpt_table" id="tbl_list_search" align="left">
                        <tbody>
                            <?
                                $i = 1; $all_approval_id = '';
                            
                                foreach ($nameArray as $row)
                                {
                                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                                                                
                                    $piNumberArr=array();$piDateArr=array();$piValueArr=array();
                                    $dealing_marchant='';$team_leader='';
                                    foreach(explode(',',$row['PI_ID']) as $pi_id){
                                        $piNumberArr[$pi_id]=$pi_data_arr['pi_number'][$pi_id];
                                        $piDateArr[$pi_id]=change_date_format($pi_data_arr['pi_date'][$pi_id]);
                                        $piValueArr[$pi_id]=$pi_data_arr['pi_amount'][$pi_id];
                                        
                                        $dealing_marchant=$dealing_merchant_arr[$job_data_arr['dealing_marchant'][$pi_id]];
                                        $team_leader=$team_leader_arr[$job_data_arr['team_leader'][$pi_id]];
                                    }

                                    $acceptance_date='';
                                    $acceptance_date_tultip='';
                                    if ($row['MATURITY_FROM_ID'] == 1)
                                    {
                                        $acceptance_date=change_date_format($row['BANK_ACC_DATE']);
                                        $acceptance_date_tultip='Acceptance Date';
                                    }
                                    else if ($row['MATURITY_FROM_ID'] == 2)
								{
									$acceptance_date=change_date_format($row['SHIPMENT_DATE']);
									$acceptance_date_tultip='Shipment Date';
								}
								else if ($row['MATURITY_FROM_ID'] == 3)
								{
									$acceptance_date=change_date_format($row['NAGOTIATE_DATE']);
									$acceptance_date_tultip='Negotiation Date';
								}
								else if ($row['MATURITY_FROM_ID'] == 4)
								{
									$acceptance_date=change_date_format($row['BILL_DATE']);
									$acceptance_date_tultip='B/L Date';
								}
								else if ($row['MATURITY_FROM_ID']==5)
								{
									$acceptance_date=change_date_format($row['SHIPMENT_DATE']);
									$acceptance_date_tultip='Delivery Challan Date';
								}	
								
								
								?>
                                <tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i;?>','<?= $bgcolor; ?>');" id="tr_<?= $i;?>"> 
                                    <td width="35" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<?= $i;?>" name="tbl[]" />
                                        <input id="invoice_id_<?= $i;?>" name="invoice_id[]" type="hidden" value="<?= $row['ID']; ?>" />
                                        <input id="is_posted_account_<?= $i;?>" name="is_posted_account[]" type="hidden" value="<?= $row['IS_POSTED_ACCOUNT']; ?>" />
                                        <input id="invoice_no_<?= $i;?>" name="invoice_no[]" type="hidden" value="<?= $row['INVOICE_NO']; ?>" />
                                    </td> 
                                    <td width="30" onClick="fn_check_uncheck(<?= $i;?>)" align="center"><?= $i;?></td>
                                    <td width="60" onClick="fn_check_uncheck(<?= $i;?>)" align="center"><p><?= $row['INVOICE_NO'];?></p></td>
                                    <td width="70" onClick="fn_check_uncheck(<?= $i;?>)" align="center"><?= change_date_format($row['INVOICE_DATE']); ?></a></td>
                                    <td width="60" align="center"><a href="javascript:void()" onClick="downloiadFile('<?= $row['ID']; ?>','<?= $row['IMPORTER_ID']; ?>');">View File</a></td>
                                    
                                    
                                    <td width="100" onClick="fn_check_uncheck(<?= $i;?>)"><p><?= $row['LC_NUMBER'];?></p></td>
                                    <td width="100" onClick="fn_check_uncheck(<?= $i;?>)"><p><?= $lc_type[$row['LC_TYPE_ID']];?></p></td>
                                    <td width="70" onClick="fn_check_uncheck(<?= $i;?>)" align="center"><?= change_date_format($row['LC_DATE']);?></td>
                                    <td width="90" onClick="fn_check_uncheck(<?= $i;?>)"><p><?= implode(',',$piNumberArr); ?></p></td>
                                    <td width="80" onClick="fn_check_uncheck(<?= $i;?>)" align="center"><p><?= implode(',',$piDateArr); ?></p></td>
                                     <td width="100" onClick="fn_check_uncheck(<?= $i;?>)" align="center"><p><?= $row['BANK_REF']; ?></p></td>
                                      <td width="80" onClick="fn_check_uncheck(<?= $i;?>)" align="center" title="<?= $acceptance_date_tultip; ?>"><p><?= $acceptance_date; ?></p></td>
                                      <td width="80" onClick="fn_check_uncheck(<?= $i;?>)" align="center"><p><?= change_date_format($row['BANK_ACC_DATE']); ?></p></td>
                                      <td width="80" onClick="fn_check_uncheck(<?= $i;?>)" align="center"><p><?= change_date_format($row['COMPANY_ACC_DATE']); ?></p></td>
                                    <td width="70" onClick="fn_check_uncheck(<?= $i;?>)" align="right"><?= $row['CURRENT_ACCEPTANCE_VALUE'];?>&nbsp;</td>
                                    <td width="100" onClick="fn_check_uncheck(<?= $i;?>)"><p><?= $supplier_arr[$row['SUPPLIER_ID']];?></p></td>
                                    <td width="90" onClick="fn_check_uncheck(<?= $i;?>)"><p><?= $acceptance_time[$row['ACCEPTANCE_TIME']];?></p></td>
                                    <td width="100" onClick="fn_check_uncheck(<?= $i;?>)"><p><?= $team_leader;?></p></td>
                                    <td onClick="fn_check_uncheck(<?= $i;?>)"><p><?= $dealing_marchant;?></p></td>
                                </tr>                               
                                <?
                                $i++;
							}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="<? echo $width+21; ?>" class="rpt_table" align="left">
				<tfoot>
                    <td width="35" align="center">
                    	<input type="checkbox" id="all_check" onClick="check_all('all_check')" />
                        <input type="hidden" name="hide_approval_type" id="hide_approval_type" value="<? echo $approval_type; ?>">
                        <font style="display:none"><? echo $all_approval_id; ?></font>
                    </td>
                    <td colspan="2" align="left"> <input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>,<? echo $approvePermission; ?>)"/></td>
				</tfoot>
			</table>
        </fieldset>
    </form>         
    <?
	$user_id=$_SESSION['logic_erp']['user_id'];
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
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

                $sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation,b.SEQUENCE_NO from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and valid=1 and a.id!=$user_id  and b.is_deleted=0  and b.entry_form=38 order by b.SEQUENCE_NO";
                //echo $sql;die();
             $arr=array (2=>$custom_designation,3=>$Department);
             echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department,Seq", "100,120,150,120,30,","620","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id,0", $arr , "user_name,user_full_name,designation,department_id,SEQUENCE_NO", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
            ?>
    </form>
    <script language="javascript" type="text/javascript">
      setFilterGrid("tbl_style_ref");
    </script>

    <?
}

if ($action=="approve")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$con = connect();

	$msg=''; $flag=''; $response='';    
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
    $cbo_company_name = str_replace("'","",$cbo_company_name);
    $invoice_ids = str_replace("'","",$invoice_ids);

    $user_id_approval=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;
	

	//............................................................................
	
	$sql = "select a.ID,a.READY_TO_APPROVED  from com_import_invoice_mst a where  a.STATUS_ACTIVE=1 and a.IS_DELETED=0  and a.id in($invoice_ids)";
	//echo $sql;die();
	$sqlResult=sql_select( $sql );
	foreach ($sqlResult as $row)
	{
		if($row['READY_TO_APPROVED'] != 1){echo '21**Ready to approve yes is mandatory';exit();}
		$matchDataArr[$row['ID']]=array('buyer_id'=>0,'brand_id'=>0,'supplier_id'=>0,'store'=>0);
	}
	
	$finalDataArr=getFinalUser(array('company_id'=>$cbo_company_name,'ENTRY_FORM'=>38,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0,'product_dept_arr'=>0,'match_data'=>$matchDataArr));
	
 
	$sequ_no_arr_by_sys_id =$finalDataArr['final_seq'];
	$user_sequence_no = $finalDataArr['user_seq'][$user_id_approval];
 

	
 	if($approval_type==0)
	{ 
 		
		$id=return_next_id( "id","approval_mst", 1 ) ;
		$ahid=return_next_id( "id","approval_history", 1 ) ;	
		
		$target_app_id_arr = explode(',',$invoice_ids);	
        //print_r($target_app_id_arr);
        foreach($target_app_id_arr as $mst_id)
        {
			if($data_array!=''){$data_array.=",";}
			$data_array.="(".$id.",38,'".$mst_id."','".$user_sequence_no."',".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
			$id=$id+1;
			
			$approved_no=$max_approved_no_arr[$mst_id][$user_id_approval]+1;
			if($history_data_array!="") $history_data_array.=",";
			$history_data_array.="(".$ahid.",38,".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
			$ahid++;
			
			//mst data.......................
			$approved=(max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no)?1:3;
			$data_array_up[$mst_id] = explode(",",("".$approved.",".$user_sequence_no.",'".$pc_date_time."',".$user_id_approval."")); 
        }
	 
 

        $flag=1;
		if($flag==1) 
		{  
			$field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
			$rID1=sql_insert("approval_mst",$field_array,$data_array,0);
			if($rID1) $flag=1; else $flag=0; 
		}
		   
		if($flag==1) 
		{
			$field_array_up="APPROVED*APPROVED_SEQU_BY*APPROVED_DATE*APPROVED_BY"; 
			$rID2=execute_query(bulk_update_sql_statement( "com_import_invoice_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr ));
			if($rID2) $flag=1; else $flag=0; 
		}

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=38 and mst_id in ($invoice_ids)";
			$rID3=execute_query($query,1);
			if($rID3) $flag=1; else $flag=0;
		}
		 
		if($flag==1)
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING";
			$rID4=sql_insert("approval_history",$field_array,$history_data_array,0);
			if($rID4) $flag=1; else $flag=0;
		}
		
		//echo "24444**".$rID1.",".$rID2.",".$rID3.",".$rID4;oci_rollback($con);die;
		
		if($flag==1) $msg='19'; else $msg='21';

        
	}
	else
	{      
        
        
        $checkSql="select a.INVOICE_ID,a.SYSTEM_NUMBER from COM_IMPORT_PAYMENT_MST a,COM_IMPORT_PAYMENT b where a.id=b.mst_id and a.INVOICE_ID in($invoice_ids) and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 ";
		//echo $checkSql;die();
		$checkSqlRes=sql_select($checkSql); 
		foreach($checkSqlRes as $rows)
		{
			 echo "25**Unapprove Not Allow. Import Payment Found";exit();
		}
		unset($checkSqlRes);	
		
		$next_user_app = sql_select("select id from approval_history where mst_id in($invoice_ids) and entry_form=38 and sequence_no >$user_sequence_no and current_approval_status=1 group by id");
				
		if(count($next_user_app)>0)
		{
			echo "25**unapproved"; 
			disconnect($con);
			die;
		}

		$rID1=sql_multirow_update("com_import_invoice_mst","APPROVED*READY_TO_APPROVED*APPROVED_SEQU_BY",'0*0*0',"id",$invoice_ids,0);
		if($rID1) $flag=1; else $flag=0;


		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=38 and mst_id in ($invoice_ids)";
			$rID2=execute_query($query,1);
			if($rID2) $flag=1; else $flag=0;
		}

		
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=38 and mst_id in ($invoice_ids)";
			$rID3=execute_query($query,1); 
			if($rID3) $flag=1; else $flag=0; 
		}
		

		
		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=38 and current_approval_status=1 and mst_id in ($invoice_ids)";
			$rID4=execute_query($query,1);
			if($rID4) $flag=1; else $flag=0;
		}
 		
		// echo "5**".$rID1.",".$rID2.",".$rID3.",".$rID4.$flag;oci_rollback($con);die;
		
		$response=$invoice_ids;
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
if($action=="get_user_pi_file")
{
    // var_dump($_REQUEST);
    extract($_REQUEST);
  
    $img_sql = "SELECT id,image_location,master_tble_id,real_file_name from common_photo_library where form_name='importdocumentacceptance_1' and master_tble_id='$id'";
    $img_sql_res = sql_select($img_sql);
    if(count($img_sql_res)==0){ echo "<div style='text-align:center;color:red;font-size:18px;'>Image/File is not available.</div>";die();}
    foreach($img_sql_res as $img)
    {
        echo '<p style="display:inline-block;word-wrap:break-word;word-break:break-all;width:115px;padding-right:10px;vertical-align:top;margin:0px;"><a href="?action=downloiadFile&file='.urlencode($img[csf("image_location")]).'"><img src="../../file_upload/blank_file.png" width="89px" height="97px"></a><br>'.$img[csf("real_file_name")].'</p>';   
    }
}

if($action=="downloiadFile")
{
    if(isset($_REQUEST["file"]))
    {        
        $file = urldecode($_REQUEST["file"]); // Decode URL-encoded string   
        
        $filepath = "../../" . $file;    
        // Process download
        if(file_exists($filepath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            flush(); // Flush system output buffer
            readfile($filepath);
            exit;
        }
    }
}

if($action=="check_booking_last_update")
{
	$last_update = return_field_value("is_apply_last_update","com_pi_master_details","id='".trim($data)."'");
	echo $last_update;
	exit();	
}

if ($action == "cross_check_popup") 
{
    echo load_html_head_contents("Cross Check Details", "../../", 1, 1,'','','');
    extract($_REQUEST);

    $pi_cross_check_array = array(1=>"Yarn Price checked", 2=>"Trims Price Checked", 3=>"Consumption Checked", 4=>"Pilling Test", 5=>"Shrinkage test", 6=>"High Risk Analysis test");
    
    if($pi_id != "")
    {
        $cross_check_items = return_field_value("cross_check_activity_ids","com_cross_check_activity","pi_id=$pi_id and status_active=1","cross_check_activity_ids");
        $id = return_field_value("id","com_cross_check_activity","pi_id=$pi_id and status_active=1","id");
    }
    //echo $cross_check_items;die;
    $cross_check_items = explode(",", chop($cross_check_items,","));
    //echo $id;die;

    ?>
    <script>
        var activity_id = "<? echo $id;?>";
            function set_cross_check_value(){
               
                parent.emailwindow.hide();
            }

    </script>
    </head>
    <body>
        <div align="center" style="width:300px;" >
            <form name="crosscheckform" id="crosscheckform"  autocomplete="off">
            <table cellpadding="0" cellspacing="0" width="300" border="1" class="rpt_table" rules="all" align="center">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="70">Checked/<br/>Unchecked</th>
                        <th>Checked/Unchecked Item Details</th>
                    </tr>
                </thead>
                <tbody>
            <?
            $i=1;
            foreach ($pi_cross_check_array as $key => $value) {
                if($i%2==0) $bg_color = "#E9F3FF"; 
                    else $bg_color = "#FFFFFF";
            ?>		
                <tr bgcolor="<? echo $bg_color;?>" onClick="set_checkbox_value(<? echo $i;?>)" style="cursor:pointer">
                    <td align="center" width="30"><? echo $i;?></td>
                    <td align="center" width="70">
                    <input type="checkbox" name="cross_checked_item_<? echo $i;?>" id="cross_checked_item_<? echo $i;?>" class="cross_check_item" value="<? echo $key;?>" onClick="js_set_value(this);" readonly/> </td>
                    <td style="padding-left: 5px;"> <? echo $value; ?></td>
                </tr>		
            <?
            $i++;
            }

            ?>
                </tbody>
                <tfoot>
                    <tr><td colspan="3">&nbsp;</td></tr>
                    <tr>
                        <td align="left" colspan="3">
                            <input type="button" name="close" onClick="set_cross_check_value()" class="formbutton" value="Close" style="width:100px" />
                            
                        </td>
                    </tr>
                </tfoot>
            </table>
            <script>
            <?
            foreach ($cross_check_items as $value) {
                ?>
                
                    var id = "#cross_checked_item_"+<? echo $value;?>;
                    $(id).attr("checked",true).val(<? echo $value;?>);
                    //$("#cross_checked_item_"+val).val(val);
               
                <?
            }
            ?>
             </script>
            </form>
        </div>
    </body>
    </html>
        <?
        exit();
}

// if($action=='check_import_payment'){
//  	$data_arr = return_library_array("select a.INVOICE_ID,a.SYSTEM_NUMBER from COM_IMPORT_PAYMENT_MST a,COM_IMPORT_PAYMENT b where a.id=b.mst_id and a.INVOICE_ID in($data) and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0","INVOICE_ID","SYSTEM_NUMBER");	
// 	echo implode(',',$data_arr);
// exit();	
// }



?>