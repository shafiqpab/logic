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


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$unlocal_status_arr=array(1=>"First Time",2=>"Second Time",3=>"Third Time",4=>"fourth Time",5=>"Fifth Time",6=>"Sixth Time",7=>"Seventh Time",8=>"Eight Time",9=>"Ninth Time",10=>"Tenth Time");
	
	$company_id=str_replace("'","",$cbo_company_name);
	$integration_point_id=str_replace("'","",$cbo_integration_point);
	$integration_point_search=$integration_point_search;
	$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
	
	if($integration_point_id==1)
	{
		
		$sql="select b.id,a.sys_number from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and a.company_id='$company_id' AND a.sys_number=$integration_point_search";
		
	}
	else if($integration_point_id==2)
	{
		if($db_type==0)
		{
			$sql="SELECT a.id, a.invoice_no  FROM com_import_invoice_mst a,com_btb_lc_master_details b WHERE a.btb_lc_id = CONVERT( b.id, CHAR( 50 ) ) and b.importer_id='$company_id' and a.invoice_no ='$integration_point_search' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 GROUP BY a.id, a.invoice_no";
		}
		else
		{
			$sql="SELECT a.id, a.invoice_no FROM com_import_invoice_mst a,com_btb_lc_master_details b WHERE a.btb_lc_id = TO_NCHAR(b.id) and b.importer_id='$company_id' and a.invoice_no ='$integration_point_search' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0	GROUP BY a.id, a.invoice_no"; 
		}
	}
	else if($integration_point_id==3)
	{
			 
		$sql="SELECT a.id,a.system_number, a.loan_date, b.loan_type, b.loan_number, b.bank_account_id, b.loan_amount
		FROM com_pre_export_finance_mst a, com_pre_export_finance_dtls b
		WHERE a.id=b.mst_id AND a.beneficiary_id='$company_id' AND b.loan_number='$integration_point_search' AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0";
	}
	else if($integration_point_id==4)
	{
		
		$sql="SELECT id,invoice_no,invoice_date,bl_date,is_lc,lc_sc_id,invoice_value,buyer_id FROM com_export_invoice_ship_mst WHERE benificiary_id='$company_id' AND invoice_no='$integration_point_search' AND  status_active=1 AND is_deleted=0";	
			
	}
	else if($integration_point_id==5)
	{
	$sql="SELECT a.lc_currency,a.id, a.bank_ref_no, a.lien_bank, a.buyer_id, a.negotiation_date,a.submit_type
	FROM com_export_doc_submission_mst a
	where a.company_id='$company_id' AND a.bank_ref_no='$integration_point_search' AND a.status_active=1 and a.is_deleted=0 
	group by a.lc_currency,a.id, a.bank_ref_no, a.lien_bank, a.buyer_id, a.negotiation_date,a.submit_type";
	}
	else if($integration_point_id==6)
	{
		$sql="SELECT a.id,a.buyer_id, a.invoice_bill_id, a.is_invoice_bill, a.received_date FROM  com_export_proceed_realization a, com_export_doc_submission_mst b WHERE a.invoice_bill_id=b.id and a.benificiary_id='$company_id' AND b.bank_ref_no='$integration_point_search' AND a.status_active=1 AND a.is_deleted=0
		union all
		SELECT a.id,a.buyer_id, a.invoice_bill_id, a.is_invoice_bill, a.received_date FROM  com_export_proceed_realization a, com_export_invoice_ship_mst b WHERE a.invoice_bill_id=b.id and a.benificiary_id='$company_id' AND b.invoice_no='$integration_point_search' AND a.status_active=1 AND a.is_deleted=0";
		//echo $sql;
		//AND a.invoice_bill_id='$integration_point_search'
		
	}
	else if($integration_point_id==7)
	{
		$sql="SELECT m.id, m.system_number, b.bank_ref, m.payment_date, c.issuing_bank_id, c.currency_id,c.supplier_id 
		FROM com_import_payment_mst m,  com_import_payment a, com_import_invoice_mst b, com_btb_lc_master_details c 
		where m.id=a.mst_id and a.invoice_id =b.id and c.id=a.lc_id and c.importer_id='$company_id' AND b.bank_ref='$integration_point_search' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
		group by m.id, m.system_number, b.bank_ref, m.payment_date, c.issuing_bank_id, c.currency_id,c.supplier_id";

	}
	else if($integration_point_id==8)
	{
		$sql="SELECT a.id,a.recv_number FROM inv_receive_master a where a.company_id='$company_id' and a.recv_number='$integration_point_search'  and a.status_active=1 and a.is_deleted=0"; 
	}
	else if($integration_point_id==9)
	{
		$sql="SELECT a.id,a.issue_number,a.issue_number_prefix_num,a.issue_date FROM inv_issue_master a where a.company_id='$company_id' and a.issue_number='$integration_point_search' and a.status_active=1 and a.is_deleted=0";
	}
	else if($integration_point_id==10)
	{
		$sql="SELECT a.id,a.issue_number FROM inv_issue_master a  where a.company_id='$company_id' and a.issue_number='$integration_point_search' and a.status_active=1 and a.is_deleted=0"; 
	}
	else if($integration_point_id==11)
	{
		$sql="SELECT a.id,a.recv_number FROM inv_receive_master a where a.company_id='$company_id' and a.recv_number='$integration_point_search' and a.status_active=1 and a.is_deleted=0"; 
	}
	else if($integration_point_id==12)
	{
		
		$sql="SELECT a.id,a.transfer_system_id,a.transfer_date FROM inv_item_transfer_mst a, inv_transaction b where a.id=b.mst_id and a.company_id='$company_id' and a.transfer_system_id='$integration_point_search' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.transfer_system_id,a.transfer_date"; 
	}
	else if($integration_point_id==15)
	{
		
		if($db_type==0)
		{
			$year_cond= "year(a.insert_date)as year";
		}
		else if($db_type==2)
		{
			$year_cond= "TO_CHAR(a.insert_date,'YYYY') as year";
		}
		
		$sub_del_challan_arr=array();
		$sql_sub_challan="select a.challan_no, b.id from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and status_active=1 and is_deleted=0 $recChallan_cond";
		$sql_sub_challan_result = sql_select($sql_sub_challan);
		foreach ($sql_sub_challan_result as $row)
		{
			$sub_del_challan_arr[$row[csf("id")]]=$row[csf("challan_no")];
		}
		unset($sql_sub_challan_result);
	
		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$location_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
		$party_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	
	 
		$sql= "select a.id, a.bill_no, a.prefix_no_num, $year_cond, a.location_id, a.bill_date, a.party_id, a.party_source, a.bill_for , ( nvl(b.unlocak_number,0)+1) as unlock_total from subcon_inbound_bill_mst a left join unlock_history b on a.id=b.mst_id and integration_point=15 and b.last_unlock_status=1 where a.company_id='$company_id' AND a.bill_no=$integration_point_search AND a.status_active=1 AND a.is_deleted=0 and a.is_posted_account=1 and a.post_integration_unlock=0 ";
		
	//echo $sql;
		?>
        <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:1080px; margin-top:10px">
        <legend></legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1160" class="rpt_table" >
                <thead>
                	<th width="30"></th>
                    <th width="30">SL</th>
                    <th width="110">Bill No</th>
                    <th width="40">Prifix</th>
                    <th width="40">Year</th>
                    <th width="110">Location</th>
                    <th width="110">Source</th>
                    <th width="60">Bill Date</th>
                    <th width="120">Party</th>
                    <th width="80">Bill For</th>
                    <th width="80">Challan No</th>
                    <th width="80">Unlock Status</th>
                    <th width="100">User</th>
                    <th width="">Remarks</th>
                </thead>
            </table>
            <div style="width:1160px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1142" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
                        	$i=1;
                            $nameArray=sql_select( $sql );
							// print ($sql);die;
							$ref_no = "";
							$file_numbers = "";
                            foreach ($nameArray as $row)
                            {
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								
								$challan_no=""; $bill_company="";
								if($row[csf("party_source")]==1) 
								{
									$bill_company=$company_arr[$row[csf("party_id")]];
									//$challan_no=$rec_man_challan_arr[$row[csf("delivery_id")]];
									$ex_del_id=explode(",",$row[csf("delivery_id")]);
									foreach($ex_del_id as $del_id)
									{
										if ($challan_no=="") $challan_no=$rec_man_challan_arr[$del_id]; else $challan_no.=','.$rec_man_challan_arr[$del_id];
									}
								}
								else 
								{
									$bill_company=$party_arr[$row[csf("party_id")]];
									$ex_del_id=explode("_",$row[csf("delivery_id")]);
									foreach($ex_del_id as $del_id)
									{
										if ($challan_no=="") $challan_no=$sub_del_challan_arr[$del_id]; else $challan_no.=','.$sub_del_challan_arr[$del_id];
									}
								}
								$unique_challan=implode(",",array_unique(explode(',',$challan_no)));
								?>
								<tr bgcolor="<? echo $bgcolor; ?>"  id="tr_<? echo $i; ?>" align="center">  <!--onClick="change_color('tr_<? //echo $i; ?>','<? //echo $bgcolor; ?>')" -->
                                	<td width="30" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="mstId_<? echo $i;?>" name="mstId[]" type="hidden" value="<? echo $row[csf('id')]; ?>" />
                                        <input id="unlockId_<? echo $i;?>" name="unlockId[]" type="hidden" value="<? echo $row[csf('unlock_total')]; ?>" />
  
                                    </td>   
									<td width="30" align="center"><? echo  $i; ?></td>
									<td width="110"><? echo $row[csf("bill_no")]; ?></td>
                                    <td width="40"><? echo $row[csf("prefix_no_num")]; ?></td>
                                    <td width="40"><? echo $row[csf("year")]; ?></td>		
                                    <td width="110"><? echo $location_arr[$row[csf("location_id")]];  ?></td>	
                                    <td width="110"><? echo $knitting_source[$row[csf("party_source")]];  ?></td>
                                    <td width="60"><? echo change_date_format($row[csf("bill_date")]); ?></td>
                                    <td width="120"><? echo $bill_company;?> </td>	
                                    <td width="80"><? echo $bill_for[$row[csf("bill_for")]]; ?></td>
                                    <td width="80" style="word-wrap:break-word; word-break: break-all;"><? echo $unique_challan; ?>&nbsp;</td>
                                    <td width="80"><? echo $unlocal_status_arr[$row[csf("unlock_total")]]; ?></td>
                                    <td width="100">
									<? 
										echo create_drop_down( "cbo_requested_user_".$i, 100,$user_arr,"", 1, "-- Select --", $selected, "","","","","",""); 
									 ?>
                                    </td>
                                    <td width="">
                                    	<input type="text" id="txt_remarks_<? echo $i;?>" name="txt_remarks[]" value="" style="width:100px" class="text_boxes" />
                                    </td>
								</tr>
								<?
								$i++;
						}
							
                        ?>
                    </tbody>
                    <tfoot>
                   
                    <td colspan="13" align="left"><input type="button" value="<? echo "Approve"; ?>" class="formbutton" style="width:100px" onclick="submit_approved(<? echo $i; ?>)"/></td>
				</tfoot>
                </table>
            </div>
           
        </fieldset>
    </form>  
	<?php
	exit();
    }
	else if($integration_point_id==16)
	{
		$sql="SELECT id,receive_no FROM subcon_payment_receive_mst WHERE company_id='$company_id' AND receive_no='$integration_point_search' AND status_active=1 AND is_deleted=0";   
	}
	else if($integration_point_id==17)
	{
		$sql="SELECT a.id,a.bill_no FROM subcon_outbound_bill_mst a WHERE a.company_id='$company_id' AND a.bill_no='$integration_point_search' AND a.status_active=1 AND a.is_deleted=0";
	}
	else if($integration_point_id==33)
	{
		$sql="SELECT a.id,b.lc_number FROM com_lc_charge a, com_btb_lc_master_details b WHERE a.btb_lc_id=b.id AND b.importer_id='$company_id' AND b.lc_number='$integration_point_search' AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0";
	}


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

	$response=$booking_ids;
	$field_array="id, integration_point, company_id, user_id, mst_id, remarks, last_unlock_status, unlocak_number, inserted_by,insert_date"; 
	$id=return_next_id( "id","approval_history", 1 ) ;

	
	$approved_no_array=array();
	$approved_data_arr=explode(",",$approved_data);
	$id=return_next_id("id", "unlock_history", 1);
	for($i=0;$i<count($approved_data_arr);$i++)
	{
		$single_row_data=explode("_",$approved_data_arr[$i]);
		$mst_id_arr[]=$single_row_data[0];
		if($data_array!="") $data_array.=",";
		$data_array.="(".$id.",".$cbo_integration_point.",".$cbo_company_name.",'".$single_row_data[2]."','".$single_row_data[0]."','".$single_row_data[3]."',1,'".$single_row_data[1]."',".$user_id.",'".$pc_date_time."')"; 
		$id=$id+1;
	}
	
	$flag=1;
	$rID=sql_multirow_update("subcon_inbound_bill_mst","post_integration_unlock",1,"id",implode(",",$mst_id_arr),0);
	if($flag==1) 
	{
		if($rID) $flag=1; else $flag=0;
	}
	
	$query="UPDATE unlock_history SET last_unlock_status=0 WHERE integration_point=$cbo_integration_point and mst_id in (".implode(",",$mst_id_arr).")";
	$rIDapp=execute_query($query,1);
	if($flag==1) 
	{
		if($rIDapp) $flag=1; else $flag=0; 
	} 
	
	$rID2=sql_insert("unlock_history",$field_array,$data_array,0);
	if($flag==1) 
	{
		if($rID2) $flag=1; else $flag=0; 
	}
	
	if($flag==1) $msg='19'; else $msg='21';
	
	
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



?>