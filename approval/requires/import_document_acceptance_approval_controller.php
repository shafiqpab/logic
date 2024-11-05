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

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_name = str_replace("'","",$cbo_company_name);
    $txt_date = str_replace("'","",$txt_date);      
    $txt_invoice_no = str_replace("'","",$txt_invoice_no);
	$approval_type = str_replace("'","",$cbo_approval_type);
    $txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$cbo_supplier_id = str_replace("'","",$cbo_supplier_id);

	
	if ($txt_invoice_no!="") $invoice_no_cond = " and a.invoice_no='$txt_invoice_no'";	
	if ($company_name!=0) $company_cond = " and c.importer_id=$company_name";	
	if ($cbo_supplier_id!=0) $supplier_cond = " and c.supplier_id=$cbo_supplier_id";	
	
	
	if($txt_date!="")
	{
		if($db_type==0){$txt_date=change_date_format($txt_date,"yyyy-mm-dd");}
		else{$txt_date=change_date_format($txt_date,"yyyy-mm-dd","-",1);}
		
		if(str_replace("'","",$cbo_get_upto)==1) $date_cond=" and a.invoice_date>'".$txt_date."'";
		else if(str_replace("'","",$cbo_get_upto)==2) $date_cond=" and a.invoice_date<='".$txt_date."'";
		else if(str_replace("'","",$cbo_get_upto)==3) $date_cond=" and a.invoice_date='".$txt_date."'";
		else $date_cond = '';
	}


	//if($previous_approved==1 && $approval_type==1){$previous_approved_type=1;}
    if($txt_alter_user_id!=""){ $user_id=$txt_alter_user_id;}

	
	
	if($cbo_supplier_id==0){$cbo_supplier_id="'%%'";}
	
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");  
    $min_sequence_no=return_field_value("min(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0","seq"); 
	
	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Import Document Acceptance.</font>";
		die;
	}


 

    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
    $dealing_merchant_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info", 'id', 'team_member_name');
    $team_leader_arr = return_library_array("select id,team_leader_name from lib_marketing_team where project_type=1 and status_active =1 and is_deleted=0 order by team_leader_name", 'id', 'team_leader_name');
	

	if($approval_type==0) // unapproval process start
	{
		 
		if($user_sequence_no==$min_sequence_no) // First user
		{
           $sql="SELECT a.ID, a.INVOICE_NO, a.INVOICE_DATE, a.BANK_REF, a.BANK_ACC_DATE,a.COMPANY_ACC_DATE, a.SHIPMENT_DATE, a.NAGOTIATE_DATE, a.BILL_DATE, a.IS_LC, a.ACCEPTANCE_TIME, a.is_posted_account, c.LC_NUMBER, C.LC_TYPE_ID, c.LC_DATE, c.SUPPLIER_ID, c.IMPORTER_ID, c.PI_ID, c.PI_VALUE, c.MATURITY_FROM_ID, sum(b.current_acceptance_value) as CURRENT_ACCEPTANCE_VALUE
           from com_import_invoice_mst a, com_import_invoice_dtls b, com_btb_lc_master_details c 
           where a.id=b.import_invoice_id and c.id=b.btb_lc_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 $date_cond $invoice_no_cond $company_cond $supplier_cond 
		   and a.id not in(SELECT d.mst_id FROM com_import_invoice_mst a, com_import_invoice_dtls b, approval_history d, com_btb_lc_master_details c WHERE a.id=b.import_invoice_id and a.id=d.mst_id and c.id=b.btb_lc_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.current_approval_status=1 and d.entry_form=38 and d.approved_by= $user_id $date_cond $invoice_no_cond $company_cond $supplier_cond)
		   group by a.id, a.invoice_no, a.invoice_date, a.bank_ref, a.bank_acc_date,a.COMPANY_ACC_DATE, a.shipment_date, a.nagotiate_date, a.bill_date, a.is_lc, a.acceptance_time, a.is_posted_account, c.lc_number, c.lc_type_id, c.lc_date, c.supplier_id, c.importer_id, c.pi_id, c.pi_value, c.maturity_from_id";
		}
		else // Next user
        {
			
			$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted = 0");

            if($sequence_no=="") // bypass if previous user Yes
            {
               
    			$invoice_id_app_byuser_arr = return_library_array("select mst_id, mst_id from com_import_invoice_mst a, approval_history b where a.id=b.mst_id and  b.sequence_no=$user_sequence_no and b.entry_form=38 and b.current_approval_status=1", 'mst_id', 'mst_id');
				$invoice_id_app_byuser=implode(',',$invoice_id_app_byuser_arr);
				
				$invoice_id_cond="";
                if($invoice_id_app_byuser!="") $invoice_id_cond=" and a.id not in($invoice_id_app_byuser)";

               $sql="SELECT a.ID, a.INVOICE_NO, a.INVOICE_DATE, a.BANK_REF, a.BANK_ACC_DATE,a.COMPANY_ACC_DATE, a.SHIPMENT_DATE, a.NAGOTIATE_DATE, a.BILL_DATE, a.IS_LC, a.ACCEPTANCE_TIME, a.is_posted_account, c.LC_NUMBER, C.LC_TYPE_ID, c.LC_DATE, c.SUPPLIER_ID, c.IMPORTER_ID, c.MATURITY_FROM_ID, sum(b.current_acceptance_value) as CURRENT_ACCEPTANCE_VALUE 
               from com_import_invoice_mst a, com_import_invoice_dtls b, com_btb_lc_master_details c 
               where a.id=b.import_invoice_id and c.id=b.btb_lc_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 $date_cond $invoice_no_cond $company_cond $supplier_cond $supplier_cond
			   and a.id not in(SELECT d.mst_id FROM com_import_invoice_mst a, com_import_invoice_dtls b, approval_history d, com_btb_lc_master_details c WHERE a.id=b.import_invoice_id and a.id=d.mst_id and c.id=b.btb_lc_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.current_approval_status=1 and d.entry_form=38 and d.approved_by= $user_id $date_cond $invoice_no_cond $company_cond $supplier_cond)
			   group by a.id, a.invoice_no, a.invoice_date, a.bank_ref, a.bank_acc_date,a.COMPANY_ACC_DATE, a.shipment_date, a.nagotiate_date, a.bill_date, a.is_lc, a.acceptance_time, a.is_posted_account, c.lc_number, c.lc_type_id, c.lc_date, c.supplier_id, c.importer_id, c.maturity_from_id";
                  
            }
            else // bypass No
            {
                $user_sequence_no=$user_sequence_no-1;

    			$sequence_no_by_pass_arr = return_library_array("select sequence_no, sequence_no from electronic_approval_setup where page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and is_deleted=0", 'sequence_no', 'sequence_no');
				
				$sequence_no_by_pass=implode(',',$sequence_no_by_pass_arr);
				
				if($sequence_no_by_pass=="") $sequence_no_cond=" and d.sequence_no='$user_sequence_no'";
                else $sequence_no_cond=" and (d.sequence_no='$sequence_no' or d.sequence_no in ($sequence_no_by_pass))";
                
              	$sql="SELECT a.ID, a.INVOICE_NO, a.INVOICE_DATE, a.BANK_REF, a.BANK_ACC_DATE,a.COMPANY_ACC_DATE, a.SHIPMENT_DATE, a.NAGOTIATE_DATE, a.BILL_DATE, a.IS_LC, a.ACCEPTANCE_TIME, a.is_posted_account, c.LC_NUMBER, C.LC_TYPE_ID, c.LC_DATE, c.SUPPLIER_ID, c.IMPORTER_ID, c.PI_ID, c.PI_VALUE, c.MATURITY_FROM_ID, sum(distinct b.current_acceptance_value) as CURRENT_ACCEPTANCE_VALUE 
              	from com_import_invoice_mst a, com_import_invoice_dtls b, com_btb_lc_master_details c, approval_history d 
              	where a.id=b.import_invoice_id and c.id=b.btb_lc_id and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 $date_cond $sequence_no_cond $invoice_no_cond $company_cond $supplier_cond
				and a.id not in(SELECT d.mst_id FROM com_import_invoice_mst a, com_import_invoice_dtls b, approval_history d, com_btb_lc_master_details c WHERE a.id=b.import_invoice_id and a.id=d.mst_id and c.id=b.btb_lc_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.current_approval_status=1 and d.entry_form=38 and d.approved_by= $user_id $date_cond $invoice_no_cond $company_cond $supplier_cond)
				group by a.id, a.invoice_no, a.invoice_date, a.bank_ref, a.bank_acc_date,a.COMPANY_ACC_DATE, a.shipment_date, a.nagotiate_date, a.bill_date, a.is_lc, a.acceptance_time, a.is_posted_account, c.lc_number , c.lc_type_id, c.lc_date, c.supplier_id, c.importer_id, c.pi_id, c.pi_value, c.maturity_from_id";             
            }
		}
	}
	else // approval process start
    {
		$sql="SELECT a.ID, a.INVOICE_NO, a.INVOICE_DATE, a.BANK_REF, a.BANK_ACC_DATE,a.COMPANY_ACC_DATE, a.SHIPMENT_DATE, a.NAGOTIATE_DATE, a.BILL_DATE, a.IS_LC, a.ACCEPTANCE_TIME, a.is_posted_account, c.LC_NUMBER, C.LC_TYPE_ID, c.LC_DATE, c.SUPPLIER_ID, c.IMPORTER_ID, c.PI_ID, c.PI_VALUE, c.MATURITY_FROM_ID, sum(b.current_acceptance_value) as CURRENT_ACCEPTANCE_VALUE 
		FROM com_import_invoice_mst a, com_import_invoice_dtls b, approval_history d, com_btb_lc_master_details c 
		WHERE a.id=b.import_invoice_id and a.id=d.mst_id and c.id=b.btb_lc_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.current_approval_status=1 and d.entry_form=38 and d.approved_by= $user_id $date_cond $invoice_no_cond $company_cond $supplier_cond
		group by a.id, a.invoice_no, a.invoice_date, a.bank_ref, a.bank_acc_date,a.COMPANY_ACC_DATE, a.shipment_date, a.nagotiate_date, a.bill_date, a.is_lc, a.acceptance_time, a.is_posted_account, c.lc_number , c.lc_type_id, c.lc_date, c.supplier_id, c.importer_id, c.pi_id, c.pi_value, c.maturity_from_id";
	} 
	//  echo $sql;
	
    $nameArray = sql_select( $sql );
	foreach($nameArray as $rows){
		$pi_str_arr[]=$rows[PI_ID];
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



	 $piSql="select a.ID,a.SUPPLIER_ID, a.ITEM_CATEGORY_ID,a.PI_NUMBER,a.ENTRY_FORM,a.EXPORT_PI_ID,a.PI_DATE,a.TOTAL_AMOUNT,a.NET_TOTAL_AMOUNT from com_pi_master_details a where  a.IMPORTER_ID=$company_name  $pi_arr and a.STATUS_ACTIVE=1 and a.IS_DELETED=0";

     //echo $piSql;die;

	$piSqlResult = sql_select( $piSql );
	foreach($piSqlResult as $rows){
		$pi_data_arr['pi_number'][$rows[ID]]=$rows[PI_NUMBER];
		$pi_data_arr['pi_amount'][$rows[ID]]=$rows[NET_TOTAL_AMOUNT];
		$pi_data_arr['entry_form'][$rows[ID]]=$rows[ENTRY_FORM];
		$pi_data_arr['item_category_id'][$rows[ID]]=$rows[ITEM_CATEGORY_ID];
		$pi_data_arr['export_pi_id'][$rows[ID]]=$rows[EXPORT_PI_ID];
	}

   // print_r($pi_data_arr);
	
	
	$sqlJob="select a.PI_ID, c.DEALING_MARCHANT,c.TEAM_LEADER
    from com_pi_item_details a, wo_booking_dtls b, wo_po_details_master c 
    where a.work_order_dtls_id = b.id and b.job_no = c.job_no  and a.is_deleted = 0 and a.status_active = 1 and b.is_deleted = 0 and b.status_active = 1 and c.is_deleted = 0 and c.status_active = 1 $pi_arr1";
	$sqlJobResult = sql_select( $sqlJob );
	foreach($sqlJobResult as $rows){
		$job_data_arr['dealing_marchant'][$rows[PI_ID]]=$rows[DEALING_MARCHANT];
		$job_data_arr['team_leader'][$rows[PI_ID]]=$rows[TEAM_LEADER];
	}

    $print_report_format=return_field_value("format_id","lib_report_template","template_name =".$company_name." and module_id=5 and report_id =183 and is_deleted=0 and status_active=1");
    $format_ids=explode(",",$print_report_format);

    //print_r($format_ids);die;
	
    $width=1480; 
    ob_start();   
	?>
    <script>
        function open_print_btn_popup(data){
            var title = 'Show Print Options';
            var page_link = 'requires/import_document_acceptance_approval_controller.php?action=print_button_variable&print_data='+data;
            emailwindow=dhtmlmodal.open('ShowPrint', 'iframe', page_link, title, 'width=650px,height=100px,center=1,resize=1,scrolling=0','');
            emailwindow.onclose=function()
            {
                
            }
        }
    </script>
    
    
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
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width; ?>" class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
                        <?
                            $i = 1; $all_approval_id = '';
                           
                            foreach ($nameArray as $row)
                            {
                                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
								                              
                                $piNumberArr=array();$piDateArr=array();$piValueArr=array();$piEntryArr=array();$piItemArr=array();$piExportArr=array();
								$dealing_marchant='';$team_leader='';
								foreach(explode(',',$row[PI_ID]) as $pi_id){
									$piNumberArr[$pi_id]=$pi_data_arr['pi_number'][$pi_id];
									$piDateArr[$pi_id]=change_date_format($pi_data_arr['pi_date'][$pi_id]);
									$piValueArr[$pi_id]=$pi_data_arr['pi_amount'][$pi_id];
									$piEntryArr[$pi_id]=$pi_data_arr['entry_form'][$pi_id];

                                    
									$piItemArr[$pi_id]=$pi_data_arr['item_category_id'][$pi_id];
									$piExportArr[$pi_id]=$pi_data_arr['export_pi_id'][$pi_id];

                                    
                                    $entry_form= implode(',',$piEntryArr);
                                    $item_category= implode(',',$piItemArr);
                                    $export_pi_id= implode(',',$piExportArr);
                                    $pi_no= implode(',',$piNumberArr);
									
									$dealing_marchant=$dealing_merchant_arr[$job_data_arr['dealing_marchant'][$pi_id]];
									$team_leader=$team_leader_arr[$job_data_arr['team_leader'][$pi_id]];
								}

                              
                                $variable='';
								if($format_ids[$j]==85) // Print 3
                                {
                                    $variable="<a href='##' onclick=\"print_report('".$row[csf('importer_id')]."*".$row[csf('id')]."*".$item_category."','print_sf','../commercial/import_details/requires/pi_print_urmi')\"><font color='blue'><b>".$pi_no."</b></font><a/>";
                                }
                               
                                else if($format_ids[$j]==86) // Print 
                                {  
                                    $variable="<a href='##' onclick=\"print_report('".$row[csf('importer_id')]."*".$row[csf('id')]."*". $entry_form."*".$item_category."','print','../commercial/import_details/requires/pi_print_urmi')\"><font color='blue'><b>".$pi_no."</b></font><a/>";
                                }
                                else if($format_ids[$j]==116) // Print 2 
                                {
                                    $variable="<a href='##' onclick=\"print_report('".$row[csf('importer_id')]."*".$row[csf('id')]."*".$item_category."','print_wf','../commercial/import_details/requires/pi_print_urmi')\"><font color='blue'><b>".$pi_no."</b></font><a/>";
                                }
                                else // PI-Print
                                {
                                    $variable="<a href='##' onclick=\"print_report('".$row[csf('importer_id')]."*".$row[csf('id')]."*".$item_category."','print_pi','../commercial/import_details/requires/pi_print_urmi')\"><font color='blue'><b>".$pi_no."</b></font><a/>";
                                }

                              

								$acceptance_date='';
								$acceptance_date_tultip='';
								if ($row[MATURITY_FROM_ID] == 1)
								{
									$acceptance_date=change_date_format($row[BANK_ACC_DATE]);
									$acceptance_date_tultip='Acceptance Date';
								}
								else if ($row[MATURITY_FROM_ID] == 2)
								{
									$acceptance_date=change_date_format($row[SHIPMENT_DATE]);
									$acceptance_date_tultip='Shipment Date';
								}
								else if ($row[MATURITY_FROM_ID] == 3)
								{
									$acceptance_date=change_date_format($row[NAGOTIATE_DATE]);
									$acceptance_date_tultip='Negotiation Date';
								}
								else if ($row[MATURITY_FROM_ID] == 4)
								{
									$acceptance_date=change_date_format($row[BILL_DATE]);
									$acceptance_date_tultip='B/L Date';
								}
								else if ($row[MATURITY_FROM_ID]==5)
								{
									$acceptance_date=change_date_format($row[SHIPMENT_DATE]);
									$acceptance_date_tultip='Delivery Challan Date';
								}	
								
								
								?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_<? echo $i; ?>"> 
                                    <td width="35" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" name="tbl[]" />
                                        <input id="invoice_id_<? echo $i;?>" name="invoice_id[]" type="hidden" value="<? echo $row[ID]; ?>" />
                                        <input id="is_posted_account_<? echo $i;?>" name="is_posted_account[]" type="hidden" value="<? echo $row[IS_POSTED_ACCOUNT]; ?>" />
                                        <input id="invoice_no_<? echo $i;?>" name="invoice_no[]" type="hidden" value="<? echo $row[INVOICE_NO]; ?>" />
                                    </td> 
                                    <td width="30" onClick="fn_check_uncheck(<? echo $i; ?>)" align="center"><? echo $i; ?></td>
                                    <td width="60" onClick="fn_check_uncheck(<? echo $i; ?>)" align="center"><p><? echo $row[INVOICE_NO];?></p></td>
                                    <td width="70" onClick="fn_check_uncheck(<? echo $i; ?>)" align="center"><? echo change_date_format($row[INVOICE_DATE]); ?></a></td>
                                    <td width="60" align="center"><a href="javascript:void()" onClick="downloiadFile('<? echo $row[ID]; ?>','<? echo $row[IMPORTER_ID]; ?>');">View File</a></td>
                                    
                                    
                                    <td width="100" onClick="fn_check_uncheck(<? echo $i; ?>)"><p><? echo $row[LC_NUMBER];?></p></td>
                                    <td width="100" onClick="fn_check_uncheck(<? echo $i; ?>)"><p><? echo $lc_type[$row[LC_TYPE_ID]];?></p></td>
                                    <td width="70" onClick="fn_check_uncheck(<? echo $i; ?>)" align="center"><? echo change_date_format($row[LC_DATE]);?></td>

                                    <td width="90" align="center" style="word-break:break-all;">
                                    <?php 
                                   
                                   echo  $variable;
                                    
                                    ?>
                                 
                                    
                                
                                    </td>

                                    <td width="80" onClick="fn_check_uncheck(<? echo $i; ?>)" align="center"><p><? echo implode(',',$piDateArr); ?></p></td>
                                     <td width="100" onClick="fn_check_uncheck(<? echo $i; ?>)" align="center"><p><? echo $row[BANK_REF]; ?></p></td>
                                      <td width="80" onClick="fn_check_uncheck(<? echo $i; ?>)" align="center" title="<?= $acceptance_date_tultip; ?>"><p><? echo $acceptance_date; ?></p></td>
                                      <td width="80" onClick="fn_check_uncheck(<? echo $i; ?>)" align="center"><p><? echo change_date_format($row[BANK_ACC_DATE]); ?></p></td>
                                      <td width="80" onClick="fn_check_uncheck(<? echo $i; ?>)" align="center"><p><? echo change_date_format($row[COMPANY_ACC_DATE]); ?></p></td>
                                    <td width="70" onClick="fn_check_uncheck(<? echo $i; ?>)" align="right"><? echo $row[CURRENT_ACCEPTANCE_VALUE];?>&nbsp;</td>
                                    <td width="100" onClick="fn_check_uncheck(<? echo $i; ?>)"><p><? echo $supplier_arr[$row[csf('supplier_id')]];?></p></td>
                                    <td width="90" onClick="fn_check_uncheck(<? echo $i; ?>)"><p><? echo $acceptance_time[$row[csf('ACCEPTANCE_TIME')]];?></p></td>
                                    <td width="100" onClick="fn_check_uncheck(<? echo $i; ?>)"><p><? echo $team_leader;?></p></td>
                                    <td onClick="fn_check_uncheck(<? echo $i; ?>)"><p><? echo $dealing_marchant;?></p></td>
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

                $sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and valid=1 and a.id!=$user_id  and b.is_deleted=0  and b.page_id=$menu_id";
             $arr=array (2=>$custom_designation,3=>$Department);
             echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
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
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}

	$msg=''; $flag=''; $response='';    
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);

    if ($txt_alter_user_id !="" ) { $user_id_approval=$txt_alter_user_id; }
    else { $user_id_approval=$user_id; }

    //echo "select sequence_no from electronic_approval_setup where page_id=$menu_id and user_id=$user_id and company_id = $cbo_company_name and is_deleted = 0";
    $user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","page_id=$menu_id and user_id=$user_id and company_id = $cbo_company_name and is_deleted = 0");
    $min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");

    
	if($approval_type == 0)
	{		
		$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($invoice_ids) and entry_form=38 group by mst_id","mst_id","approved_no");	
		$approved_status_arr = return_library_array("select id, approved from com_import_invoice_mst where id in($invoice_ids)","id","approved");

		$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0","sequence_no");

		if ($is_not_last_user =='') $partial_approval=1;
        else $partial_approval=3;

		$id = return_next_id( "id","approval_history", 1 );
       	$field_array = "id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, user_ip, comments, inserted_by, insert_date";

        $rID=sql_multirow_update("com_import_invoice_mst","approved","$partial_approval","id",$invoice_ids,0);
        //sql_multirow_update($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues, $commit)
		if($rID) $flag=1; else $flag=0;

		//echo $flag.'system';		
		$approved_no_array = array();
		$invoice_ids_all = explode(",",$invoice_ids);
		
		$book_nos = '';
		for($i=0;$i<count($invoice_ids_all);$i++)
		{
			$invoice_id = $invoice_ids_all[$i];
			$approved_no = $max_approved_no_arr[$invoice_id];
			$approved_status = $approved_status_arr[$invoice_id];
			
			if($approved_status==0)
			{
				$approved_no = $approved_no+1;
				//$approved_no_array[$val] = $approved_no;
			}
			
			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",38,".$invoice_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."','".$user_ip."','',".$user_id.",'".$pc_date_time."')"; 
			$id=$id+1;			
		}

		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=38 and mst_id in ($invoice_ids)";
        $rIDapp=execute_query($query,0);
        if($flag==1)
		{
			if($rIDapp) $flag=1; else $flag=0;
		}
		//echo $flag.'system';
		//echo "10**INSERT INTO approval_history (".$field_array.") VALUES ".$data_array;die;
		
		$rID2 = sql_insert("approval_history",$field_array,$data_array,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		
		if($flag==1) $msg='19'; else $msg='21';	
	
	}
	else
	{		
        $at_sight_payment_sql = "select b.INVOICE_NO,sum(a.ACCEPTED_AMMOUNT) as ACCEPTED_AMMOUNT from COM_IMPORT_PAYMENT_COM a,COM_IMPORT_INVOICE_MST b where b.id=a.INVOICE_ID and a.INVOICE_ID in($invoice_ids) and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 group by b.INVOICE_NO";
	    $at_sight_payment_sql_res = sql_select( $at_sight_payment_sql );
	    $at_sight_payment_data_arr=array();
	    foreach($at_sight_payment_sql_res as $rows){
		    $at_sight_payment_data_arr[$rows[INVOICE_NO]]=$rows[INVOICE_NO];
	    }
		
	    if(count($at_sight_payment_data_arr)>0){echo "50**".implode(',',$at_sight_payment_data_arr);oci_rollback($con);die;}
	   
	    //$update_data="0*".$user_id_approval."*'".$pc_date_time."'*".$user_id."*'".$pc_date_time."'";
        //$update_fill="current_approval_status*un_approved_by*un_approved_date*updated_by*update_date";
		//$rID3=sql_multirow_update("approval_history",$update_fill,$update_data,"mst_id",$invoice_ids,1);

		$rID=sql_multirow_update("com_import_invoice_mst","approved*ready_to_approved","0*0","id",$invoice_ids,0);

		if($rID) $flag=1; else $flag=0;
		
		if ($flag==1){
			$query="UPDATE approval_history SET current_approval_status=0,un_approved_by=".$user_id_approval.",un_approved_date='".$pc_date_time."',updated_by=".$user_id.",update_date='".$pc_date_time."' WHERE entry_form=38 and mst_id in ($invoice_ids)";
			$rID3=execute_query($query,0);
			if($rID3) $flag=1; else $flag=0;
		}
		$response = $invoice_ids;
		if($flag==1) $msg='20'; else $msg='22';		
	}
	
	if($db_type==0)
	{ 
		if($flag==1)
		{
			mysql_query("COMMIT");  
			echo $msg."**".$response;
		}
		else
		{
			mysql_query("ROLLBACK"); 
			echo $msg."**".$response;
		}
	}
	else if($db_type==2 || $db_type==1 )
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

if($action=='check_import_payment'){
 	$data_arr = return_library_array("select a.INVOICE_ID,a.SYSTEM_NUMBER from COM_IMPORT_PAYMENT_MST a,COM_IMPORT_PAYMENT b where a.id=b.mst_id and a.INVOICE_ID in($data) and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0","INVOICE_ID","SYSTEM_NUMBER");	
	echo implode(',',$data_arr);
exit();	
}

if($action=="print_button_variable")
{ 
    echo load_html_head_contents("Print Button Options", "../../", 1, 1,'','','');
    extract($_REQUEST);
    list($company_id, $sys_id, $entryForm, $cbo_item_category_id, $export_pi_id) = explode('*', $print_data);
  
    ?>

    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
        function fnc_pi_approval_mst( operation )
        {
            var pi_approval_mst_values = $("#pi_approval_mst_values").val();
            var approval_mst_value = pi_approval_mst_values.split("*");
            var company_id =  approval_mst_value[0];
            var sys_id = approval_mst_value[1];
            var entry_form = approval_mst_value[2];
            var cbo_item_category_id = approval_mst_value[3];
            var export_pi_id = approval_mst_value[4];
            var cbo_goods_rcv_status = 2;
            var cbo_pi_basis_id = 1;
            var is_approved = '';

            if(sys_id=="")
            {
                alert("Something went wrong");
                return;
            }
            // print
            if(operation==1)
            {  export_pi_id="";
                if((cbo_item_category_id==74 || cbo_item_category_id==102 || cbo_item_category_id==103 || cbo_item_category_id==104)  && export_pi_id=="")
                {
                    alert("This Category Not Allow Without Export PI");return;
                }
                
                if((cbo_item_category_id==102 || cbo_item_category_id==103 || cbo_item_category_id==104 || cbo_item_category_id==31 || cbo_item_category_id==115) && cbo_goods_rcv_status==1)
                {
                    alert("After Goods Receive Status Not Allow For This Category");return;
                }

                var good_rece_data_source_arr = JSON.parse('<? echo json_encode($good_receive_data_source_arr); ?>');
                if(cbo_item_category_id==25 && cbo_goods_rcv_status==1 && good_rece_data_source_arr[cbo_importer_id]!=1)
                {
                    alert("After Goods Receive Status Only Allow For Varriable Setting After Good Receive Data Source always Work Order This Category.");return;
                }

                if(operation!=4)
                {
                    if(is_approved==1 || is_approved==3)
                    {
                        alert("PI is Approved. So Change Not Allowed");
                        return;
                    }
                }

                if(cbo_pi_basis_id==2 && cbo_goods_rcv_status==1)
                {
                    alert("Goods Rcv Status (After Goods Rcv) Not Allow For PI Basis (Independent)");
                    return;
                }
                   
                if( cbo_item_category_id == "1")
                {
                    entry_form = "165";
                }
                else if( cbo_item_category_id == "2" ||  cbo_item_category_id == "3" ||  cbo_item_category_id == "13" ||  cbo_item_category_id == "14")
                {
                    entry_form = "166";
                }
                else if( cbo_item_category_id == "4")
                {
                    entry_form = "167";
                }
                else if( cbo_item_category_id == "12")
                {
                    entry_form = "168";
                }
                else if( cbo_item_category_id == "24")
                {
                    entry_form = "169";
                }
                else if( cbo_item_category_id == "25" || cbo_item_category_id == "102" || cbo_item_category_id == "103")
                {
                    entry_form = "170";
                }
                else if( cbo_item_category_id == "30")
                {
                    entry_form = "197";
                }
                else if( cbo_item_category_id == "31")
                {
                    entry_form = "171";
                }
                else if( cbo_item_category_id == "5" ||  cbo_item_category_id == "6" ||  cbo_item_category_id == "7" ||  cbo_item_category_id == "23")
                {
                    entry_form = "227";
                }
                else
                {
                    entry_form = "172";
                } 
                print_report(company_id+'*'+sys_id+'*'+entry_form+'*'+cbo_item_category_id, "print", "../../commercial/import_details/requires/pi_print_urmi");
                return;
              
            }
            // print-2
            if(operation==2){
                if(cbo_item_category_id==3)
                {
                    print_report(company_id+'*'+sys_id+'*'+cbo_item_category_id, "print_wf", "../../commercial/import_details/requires/pi_print_urmi");
                }
                else
                {
                    alert("Only Woven Fabrics Item Print Allowed.");
                    return;
                }
            }
            // print-3
            if(operation==3)
            {
                if(cbo_item_category_id==12)
                {
                    print_report(company_id+'*'+sys_id+'*'+cbo_item_category_id, "print_sf", "../../commercial/import_details/requires/pi_print_urmi");
                }
                else
                {
                    alert("Only Services Fabrics Item Print Allowed.");
                    return;
                }
            }
            // PI-print
            if(operation==4){
                if(cbo_item_category_id==4)
                {
                    print_report(company_id+'*'+sys_id+'*'+cbo_item_category_id, "print_pi", "../../commercial/import_details/requires/pi_print_urmi");
                }
                else
                {
                    alert("Only Accessories Item Print Allowed.");
                    return;
                }
            }
            // Print-5
            if(operation==5){
                if(cbo_item_category_id==4)
                {
                    print_report(company_id+'*'+sys_id+'*'+cbo_item_category_id, "print_f", "../../commercial/import_details/requires/pi_print_urmi");
                }
                else
                {
                    alert("Only Accessories Item Print Allowed.");
                    return;
                }
            }
        } 
    </script>

    <?php
    $buttonHtml='';
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$company_id."' and module_id=5 and report_id=183 and is_deleted=0 and status_active=1");
   	$printButton=explode(',',$print_report_format);
    $buttonHtml.='<div align="center">';
        foreach($printButton as $id){
            if($id==86)$buttonHtml.='
            <input type="hidden" name="printBtn4" id="pi_approval_mst_values" value="'.$print_data.'"/>
            <input type="button" name="printBtn4" id="printBtn4" value="Print" onClick="fnc_pi_approval_mst(1)" style="width:100px" class="formbutton"/>';

            if($id==116)$buttonHtml.='<input type="button" name="printBtn2" id="printBtn2" value="Print 2" onClick="fnc_pi_approval_mst(2)" style="width:100px" class="formbutton">';
            if($id==85)$buttonHtml.='<input type="button" name="printBtn3" id="printBtn3" value="Print 3" onClick="fnc_pi_approval_mst(3)" style="width:100px" class="formbutton">';	
            if($id==751)$buttonHtml.='<input type="button" name="printBtn" id="printBtn" value="PI-Print" onClick="fnc_pi_approval_mst(4)" style="width:100px" class="formbutton" />';	
            if($id==479)$buttonHtml.='<input type="button" name="printBtn" id="printBtn" value="Acc." onClick="fnc_pi_approval_mst(5)" style="width:100px" class="formbutton" />';
        }
    $buttonHtml.='</div>';
    echo $buttonHtml;
    exit();
} 



?>