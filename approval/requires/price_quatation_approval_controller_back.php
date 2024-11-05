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

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
	exit();	
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$sequence_no='';
	$company_name=str_replace("'","",$cbo_company_name);
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_id=$cbo_buyer_name";
	}
	
	$date_cond='';
	if(str_replace("'","",$txt_date)!="")
	{
		if(str_replace("'","",$cbo_get_upto)==1) $date_cond=" and a.quot_date>$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==2) $date_cond=" and a.quot_date<=$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==3) $date_cond=" and a.quot_date=$txt_date";
		else $date_cond='';
	}

	$approval_type=str_replace("'","",$cbo_approval_type);
	//$user_id=3;
	//$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
	//$job_dealing_merchant_array = return_library_array("select job_no, dealing_marchant from wo_po_details_master","job_no","dealing_marchant");

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");
	
	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Price Quotation.</font>";
		die;
	}
	
	if($approval_type==0)
	{
		$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted=0");
		
		if($user_sequence_no==$min_sequence_no)
		{
			//$sql="select a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type $buyer_id_cond $date_cond group by a.id";
			
						$sql="select a.id,  a.company_id,  a.buyer_id, a.style_ref, a.style_desc, a.quot_date,a.est_ship_date, '0' as approval_id,  a.approved,a.inserted_by  from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.company_id=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved=$approval_type $buyer_id_cond $date_cond group by a.id,  a.company_id,  a.buyer_id, a.style_ref, a.style_desc, a.quot_date, a.est_ship_date, a.approved, a.inserted_by";
			
		}
		else if($sequence_no=="")
		{
			$sequence_no_by=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0");
			//$booking_id==$quotation_id
			$quotation_id=return_field_value("group_concat(distinct(mst_id)) as quotation_id","wo_price_quotation a, approval_history b","a.id=b.mst_id and a.company_id=$company_name  and b.sequence_no in ($sequence_no_by) and b.entry_form=10 and b.current_approval_status=1","quotation_id");//booking_id
			
			$quotation_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as quotation_id","wo_price_quotation a, approval_history b","a.id=b.mst_id and a.company_id=$company_name and b.sequence_no=$user_sequence_no and b.entry_form=10 and b.current_approval_status=1","quotation_id");
			//$booking_id_app_byuser==$quotation_id_app_byuser
			//$booking_id_cond==$quotation_id_cond
			if($quotation_id_app_byuser!="") $quotation_id_cond=" and a.id not in($quotation_id_app_byuser)";
			else if($quotation_id!="") $quotation_id_cond.=" or (a.id in($quotation_id))";
			else $quotation_id_cond="";
			
			//$sql="select a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type $booking_id_cond $buyer_id_cond $date_cond group by a.id";
			$sql="select a.id,  a.company_id,  a.buyer_id, a.style_ref, a.style_desc, a.quot_date,a.est_ship_date, '0' as approval_id,  a.approved , a.inserted_by from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.company_id=$company_name  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved=$approval_type $buyer_id_cond  $quotation_id_cond $date_cond  group by a.id, a.company_id, a.buyer_id, a.style_ref, a.style_desc, a.quot_date,a.est_ship_date, a.approved , a.inserted_by";
		}
		else
		{
			$user_sequence_no=$user_sequence_no-1;
			
			if($sequence_no==$user_sequence_no) $sequence_no_by_pass='';
			else
			{
				$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and is_deleted=0");
			}

			
			if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
			else $sequence_no_cond=" and (b.sequence_no='$sequence_no' or b.sequence_no in ($sequence_no_by_pass))";
			
			$sql="select a.id,  a.company_id,  a.buyer_id,  a.style_ref, a.style_desc, a.quot_date,a.est_ship_date, a.approved,a.inserted_by, b.id as approval_id from wo_price_quotation a, approval_history b where a.id=b.mst_id and b.entry_form=10 and a.company_id=$company_name  and a.status_active=1 and a.is_deleted=0 and current_approval_status=1 and a.ready_to_approved=1 and  a.approved=1 $buyer_id_cond $sequence_no_cond $date_cond";
		}
	}
	else
	{
		$sequence_no_cond=" and b.sequence_no='$user_sequence_no'";
		$sql="select a.id,  a.company_id, a.buyer_id, a.style_ref, a.style_desc, a.quot_date,a.est_ship_date, a.approved, a.inserted_by, b.id as approval_id from wo_price_quotation a, approval_history b where a.id=b.mst_id and b.entry_form=10 and a.company_id=$company_name and a.status_active=1 and a.is_deleted=0 and a.ready_to_approved=1 and b.current_approval_status=1 and a.approved=1 $buyer_id_cond $sequence_no_cond $date_cond";
	}

//echo $sql;
	
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:940px; margin-top:10px">
        <legend>Fabric Booking Approval</legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="920" class="rpt_table" >
                <thead>
                	<th width="50"></th>
                    <th width="40">SL</th>
                    <th width="130">Quatation No</th>
                    
                   
                    <th width="125">Buyer</th>
                    <th width="160">Style Ref.</th>
                    <th width="100">Quatation Date</th>
                    <th width="100">Est. Ship Date</th>
                    <th width="50">Image</th>
                    <th width="">Insert By</th>
                </thead>
            </table>
            <div style="width:920px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="902" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
						
                            $i=1;
                            $nameArray=sql_select( $sql );
                            foreach ($nameArray as $row)
                            {
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								
								$value=$row[csf('id')];
								/*if($approval_type==0)
								{
									$value=$row[csf('id')];
								}
								else
								{
									$app_id=return_field_value("id","approval_history","mst_id ='".$row[csf('id')]."' and entry_form='10' order by id desc limit 0,1");
									$value=$row[csf('id')]."**".$app_id;
								}*/
								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" align="center"> 
                                	<td width="50" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="booking_id_<? echo $i;?>" name="booking_id[]" type="hidden" value="<? echo $value; ?>" />
                                        <input id="booking_no_<? echo $i;?>" name="booking_no]" type="hidden" value="<? echo $row[csf('id')]; ?>" />
                                        <input id="approval_id_<? echo $i;?>" name="approval_id[]" type="hidden" value="<? echo $row[csf('approval_id')]; ?>" />
                                        <input id="<? echo strtoupper($row[csf('id')]); ?>" name="no_quot[]" type="hidden" value="<? echo $i;?>" />
                                    </td>   
									<td width="40" align="center"><? echo $i; ?></td>
									<td width="130">
                                    	<p><a href='##' style='color:#000' onclick="generate_worder_report('<? echo "preCostRpt2"; ?>',<? echo $row[csf('id')]; ?>,<? echo $row[csf('company_id')]; ?>,<? echo $row[csf('buyer_id')]; ?>,'<? echo $row[csf('style_ref')];?>','<? echo $row[csf('quot_date')]; ?>')"><? echo $row[csf('id')]; ?></a></p>
                                    </td>
                                    
									
                                    <td width="125"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
									<td width="160" align="left"><p><? echo $row[csf('style_ref')]; ?>&nbsp;</p></td>
                                    
                                  
                                   
                                    <td width="100" align="center"><? if($row[csf('quot_date')]!="0000-00-00") echo change_date_format($row[csf('quot_date')]); ?>&nbsp;</td>
									<td align="center" width="100"><? if($row[csf('est_ship_date')]!="0000-00-00") echo change_date_format($row[csf('est_ship_date')]); ?>&nbsp;</td>
                                    <td width="50" align="center"><a href="##" onClick="openImgFile('<? echo $row[csf('id')];?>','img');">View</a></td>
                                    <td><p><? echo ucfirst ($user_arr[$row[csf('inserted_by')]]); ?>&nbsp;</p></td>
								</tr>
								<?
								$i++;
							}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="920" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" ><input type="checkbox" id="all_check" onclick="check_all('all_check')" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onclick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/></td>
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
	//$user_id=23;
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	$msg=''; $flag=''; $response='';
	
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");

	if($approval_type==0)
	{
		$response=$booking_ids;
		$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date"; 
		$id=return_next_id( "id","approval_history", 1 ) ;
		
		$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=10 group by mst_id","mst_id","approved_no");
		
		$approved_status_arr = return_library_array("select id, approved from wo_price_quotation where id in($booking_ids)","id","approved");

		$approved_no_array=array();
		$booking_ids_all=explode(",",$booking_ids);
		$book_nos='';
		
		for($i=0;$i<count($booking_ids_all);$i++)
		{
			$booking_id=$booking_ids_all[$i];

			$approved_no=$max_approved_no_arr[$booking_id];
			$approved_status=$approved_status_arr[$booking_id];
			
			if($approved_status==0)
			{
				$approved_no=$approved_no+1;
				$approved_no_array[$booking_id]=$approved_no;
				if($book_nos=="") $book_nos=$booking_id; else $book_nos.=",".$booking_id;
			}
			
			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",10,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id.",'".$pc_date_time."')"; 
				
			$id=$id+1;
			
		}
		//print_r($booking_ids_all);
		//echo "insert into approval_history (".$field_array.") Values ".$data_array."**".$book_nos."**".$booking_nos;die;
		
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
		from wo_price_quotation where id in ($book_nos)";
			//echo $sql_insert;die;	
		
			$sql_insert2="insert into wo_price_quot_costing_mst_his(id, quot_mst_id, quotation_id, approved_no, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, commission, commission_percent, final_cost_dzn, final_cost_dzn_percent, final_cost_pcs, final_cost_set_pcs_rate, a1st_quoted_price, a1st_quoted_price_percent, a1st_quoted_price_date, revised_price, revised_price_date, confirm_price, confirm_price_set_pcs_rate, confirm_price_dzn, confirm_price_dzn_percent, margin_dzn, margin_dzn_percent, price_with_commn_dzn, price_with_commn_percent_dzn, price_with_commn_pcs, price_with_commn_percent_pcs, confirm_date, asking_quoted_price, asking_quoted_price_percent, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, terget_qty, depr_amor_pre_cost, depr_amor_po_price, interest_pre_cost, interest_po_price, income_tax_pre_cost, income_tax_po_price)
				select	
				'', id, quotation_id, $approved_string_dtls, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, commission, commission_percent, final_cost_dzn, final_cost_dzn_percent, final_cost_pcs, final_cost_set_pcs_rate, a1st_quoted_price, a1st_quoted_price_percent, a1st_quoted_price_date, revised_price, revised_price_date, confirm_price, confirm_price_set_pcs_rate, confirm_price_dzn, confirm_price_dzn_percent, margin_dzn, margin_dzn_percent, price_with_commn_dzn, price_with_commn_percent_dzn, price_with_commn_pcs, price_with_commn_percent_pcs, confirm_date, asking_quoted_price, asking_quoted_price_percent, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, terget_qty, depr_amor_pre_cost, depr_amor_po_price, interest_pre_cost, interest_po_price, income_tax_pre_cost, income_tax_po_price from wo_price_quotation_costing_mst where quotation_id in ($book_nos)";
			//echo $sql_insert2;die;
			
			$sql_insert3="insert into wo_price_quot_set_details_his(id, approved_no, quot_set_dlts_id, quotation_id, gmts_item_id, set_item_ratio)
				select	
				'', $approved_string_dtls, id, quotation_id, gmts_item_id, set_item_ratio from wo_price_quotation_set_details where quotation_id in ($book_nos)";
			//echo $sql_insert3;die;
		
			$sql_insert4="insert into wo_pri_quo_comm_cost_dtls_his(id, approved_no, quo_comm_dtls_id, quotation_id, item_id, base_id,  rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted)
				select	
				'', $approved_string_dtls, id, quotation_id, item_id, base_id, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_pri_quo_comarcial_cost_dtls where quotation_id in ($book_nos)";
			//echo $sql_insert4;die;
		
			$sql_insert5="insert into wo_pri_quo_commiss_dtls_his(id, approved_no, quo_commiss_dtls_id, quotation_id, particulars_id, commission_base_id, commision_rate, commission_amount, inserted_by, insert_date, updated_by, update_date,  status_active, is_deleted)
				select	
				'', $approved_string_dtls, id, quotation_id, particulars_id, commission_base_id, commision_rate, commission_amount, inserted_by, insert_date, updated_by,  update_date, status_active, is_deleted from wo_pri_quo_commiss_cost_dtls where quotation_id in ($book_nos)";
			//echo $sql_insert5;die;
		
			$sql_insert6="insert into wo_pri_quo_embe_cost_dtls_his(id, approved_no, quo_emb_dtls_id, quotation_id, emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted)
				select	
				'', $approved_string_dtls, id, quotation_id, emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_pri_quo_embe_cost_dtls where quotation_id in ($book_nos)";
			//echo $sql_insert6;die;
			
			$sql_insert7="insert into wo_pri_quo_fab_cost_dtls_his(id, approved_no, quo_fab_dtls_id, quotation_id, item_number_id, body_part_id,  fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, company_id, costing_per, fab_cons_in_quotat_varia, process_loss_method, yarn_breack_down, width_dia_type, cons_breack_down, msmnt_break_down, marker_break_down)
				select	
				'', $approved_string_dtls, id, quotation_id, item_number_id, body_part_id, fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, company_id, costing_per, fab_cons_in_quotat_varia, process_loss_method, yarn_breack_down, width_dia_type, cons_breack_down, msmnt_break_down, marker_break_down from wo_pri_quo_fabric_cost_dtls where quotation_id in ($book_nos)";
			//echo $sql_insert7;die;
		
			$sql_insert8="insert into wo_pri_quo_fab_conv_dtls_his (id, approved_no, quo_fab_conv_dtls_id, quotation_id, cost_head, cons_type, req_qnty, charge_unit, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, process_loss)
				select	
				'', $approved_string_dtls, id, quotation_id, cost_head, cons_type, req_qnty, charge_unit, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, process_loss from wo_pri_quo_fab_conv_cost_dtls where quotation_id in ($book_nos)";
			//echo $sql_insert8;die;
		
			$sql_insert9="insert into wo_pri_quo_fab_co_avg_con_his (id, approved_no, quo_fab_avg_co_dtls_id, wo_pri_quo_fab_co_dtls_id, quotation_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs, body_length, body_sewing_margin, body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin, front_rise_length, front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin, total, marker_dia, marker_yds, marker_inch, gmts_pcs, marker_length, net_fab_cons)
				select	
				'', $approved_string_dtls, id, wo_pri_quo_fab_co_dtls_id, quotation_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs, body_length, body_sewing_margin, body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin, front_rise_length, front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin, total, marker_dia, marker_yds, marker_inch, gmts_pcs, marker_length, net_fab_cons from wo_pri_quo_fab_co_avg_con_dtls where quotation_id in ($book_nos)";
			//echo $sql_insert9;die;
		
			$sql_insert10="insert into wo_pri_quo_fab_yarn_dtls_his(id, approved_no, quo_yarn_dtls_id, quotation_id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, supplier_id)
				select	
				'', $approved_string_dtls, id, quotation_id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, supplier_id from wo_pri_quo_fab_yarn_cost_dtls where quotation_id in ($book_nos)";
			//echo $sql_insert10;die;
			
			$sql_insert11="insert into wo_pri_quo_sum_dtls_his( id, approved_no, quo_sum_dtls_id, quotation_id, fab_yarn_req_kg, fab_woven_req_yds, fab_knit_req_kg, fab_amount, yarn_cons_qnty, yarn_amount, conv_req_qnty, conv_charge_unit, conv_amount, trim_cons, trim_rate, trim_amount, emb_amount, wash_amount, comar_rate, comar_amount, commis_rate, commis_amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted)
				select	
				'', $approved_string_dtls, id, quotation_id, fab_yarn_req_kg, fab_woven_req_yds, fab_knit_req_kg, fab_amount, yarn_cons_qnty, yarn_amount, conv_req_qnty, conv_charge_unit, conv_amount, trim_cons, trim_rate, trim_amount, emb_amount, wash_amount, comar_rate, comar_amount, commis_rate, commis_amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_pri_quo_sum_dtls where quotation_id in ($book_nos)";
			//echo $sql_insert11;die;

			$sql_insert12="insert into wo_pri_quo_trim_cost_dtls_his( id, approved_no, quo_trim_dtls_id, quotation_id, trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted)
				select	
				'', $approved_string_dtls, id, quotation_id, trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_pri_quo_trim_cost_dtls where quotation_id in ($book_nos)";
			//echo $sql_insert12;die;
		}
		
		$rID=sql_multirow_update("wo_price_quotation","approved",1,"id",$booking_ids,0);
		if($rID) $flag=1; else $flag=0;
		
		if($approval_ids!="")
		{
			$rIDapp=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,0);
			if($flag==1) 
			{
				if($rIDapp) $flag=1; else $flag=0; 
			} 
		}
		
		$rID2=sql_insert("approval_history",$field_array,$data_array,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		
		if(count($approved_no_array)>0)
		{
			$rID3=execute_query($sql_insert,1);
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			} 
			
			$rID4=execute_query($sql_insert2,1);
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			} 
			
			$rID5=execute_query($sql_insert3,1);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			} 
			
			$rID6=execute_query($sql_insert4,1);
			if($flag==1) 
			{
				if($rID6) $flag=1; else $flag=0; 
			}
			
			$rID7=execute_query($sql_insert5,1);
			if($flag==1) 
			{
				if($rID7) $flag=1; else $flag=0; 
			}  
			
			$rID8=execute_query($sql_insert6,1);
			if($flag==1) 
			{
				if($rID8) $flag=1; else $flag=0; 
			} 
			
			$rID9=execute_query($sql_insert7,1);
			if($flag==1) 
			{
				if($rID9) $flag=1; else $flag=0; 
			} 
			
			$rID10=execute_query($sql_insert8,1);
			if($flag==1) 
			{
				if($rID10) $flag=1; else $flag=0; 
			}
			
			$rID11=execute_query($sql_insert9,1);
			if($flag==1) 
			{
				if($rID11) $flag=1; else $flag=0; 
			} 
			
			$rID12=execute_query($sql_insert10,1);
			if($flag==1) 
			{
				if($rID12) $flag=1; else $flag=0; 
			}
			
			$rID13=execute_query($sql_insert11,1);
			if($flag==1) 
			{
				if($rID13) $flag=1; else $flag=0; 
			} 
			
			$rID14=execute_query($sql_insert12,1);
			if($flag==1) 
			{
				if($rID14) $flag=1; else $flag=0; 
			}   
		}
		//echo "21**".$flag;die;
		if($flag==1) $msg='19'; else $msg='21';
	}
	else
	{
		$booking_ids_all=explode(",",$booking_ids);
		
		$booking_ids=''; $app_ids='';
		
		foreach($booking_ids_all as $value)
		{
			$data = explode('**',$value);
			$booking_id=$data[0];
			$app_id=$data[1];
			
			if($booking_ids=='') $booking_ids=$booking_id; else $booking_ids.=",".$booking_id;
			if($app_ids=='') $app_ids=$app_id; else $app_ids.=",".$app_id;
		}
		
		$rID=sql_multirow_update("wo_price_quotation","approved*ready_to_approved",'0*0',"id",$booking_ids,0);
		if($rID) $flag=1; else $flag=0;
		
		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=10 and mst_id in ($booking_ids)";
		$rID2=execute_query($query,1);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		
		$data=$user_id."*'".$pc_date_time."'";
		$rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date",$data,"id",$approval_ids,1);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		//echo $flag;die;
		$response=$booking_ids;
		
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
                    $sql="select image_location from common_photo_library where master_tble_id='$id' and form_name='quotation_entry' and file_type=1";
                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
						$i++;
                    ?>
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

?>