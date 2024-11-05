<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------
$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');

if ($action=="load_variable_settings")
{
	$variable_setting_production=return_field_value("fabric_roll_level","variable_settings_production","company_name='$data' and item_category_id=3 and variable_list=3 and status_active=1","fabric_roll_level");
	/*if($variable_setting_production==1)
	{
		echo "$('#txt_roll').attr('readonly');\n";
	}
	else
	{*/
		echo "$('#txt_roll').removeAttr('readonly');\n";
	//}
 	exit();
}
if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/woven_finish_fab_receive_rtn_controller",$data);
}

if($action=="company_wise_report_button_setting"){
	
	extract($_REQUEST);

	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=6 and report_id=127 and is_deleted=0 and status_active=1");

	$print_report_format_arr=explode(",",$print_report_format);

	echo "$('#print').hide();\n";
		
	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==78){echo "$('#print').show();\n";}					
		}
	}
	else
	{		
		echo "$('#print').hide();\n";		
	}

	exit();
}

if($action=="mrr_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode); 
	extract($_REQUEST);  
?>
     
<script>
	function js_set_value(mrr)
	{
 		$("#hidden_recv_number").val(mrr); // mrr number
		parent.emailwindow.hide();
	}
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>                	 
                    <th>Search By</th>
                    <th align="center" id="search_by_td_up">Enter MRR Number</th>
                    <th>Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr class="general">
                    <td>
                        <?  
                            $search_by = array(1=>'MRR No',2=>'Challan No');
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 120, $search_by,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td width="" align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td>    
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" />
                    </td> 
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_mrr_search_list_view', 'search_div', 'woven_finish_fab_receive_rtn_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="5">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here -->
                     <input type="hidden" id="hidden_recv_number" value="" />
                    <!-- -END  -->
                </td>
            </tr>    
            </tbody>
         </tr>         
        </table>    
        <div align="center" style="margin-top:10px" valign="top" id="search_div"> </div> 
        </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_mrr_search_list_view")
{
	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$fromDate = $ex_data[2];
	$toDate = $ex_data[3];
	$company = $ex_data[4];
	
	$variable_setting_inventory=return_field_value("auto_update","variable_settings_production","company_name='$company' and variable_list=15 and status_active=1","auto_update");
	
	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==1) // for mrr
		{
			$sql_cond .= " and a.recv_number LIKE '%$txt_search_common'";	
		}
		else if(trim($txt_search_by)==2) // for chllan no
		{
			$sql_cond .= " and a.challan_no LIKE '%$txt_search_common%'";				
 		}		 
 	} 
	
	if( $fromDate!="" && $toDate!="" ) 
	{
		if($db_type==0)
		{
			$sql_cond .= " and a.receive_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
		}
		else
		{
			$sql_cond .= " and a.receive_date  between '".change_date_format($fromDate,'','',1)."' and '".change_date_format($toDate,'','',1)."'";
		}
	}
	if(trim($company)!="") $sql_cond .= " and a.company_id='$company'";
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	//if($variable_setting_inventory==1) $entry_form_ref=" and a.entry_form in(7,17)"; else $entry_form_ref=" and a.entry_form in(17)";
  	$entry_form_ref=" and a.entry_form in(17)";
	$sql = "select a.id,a.recv_number_prefix_num,a.recv_number,$year_field, a.challan_no,a.receive_date,a.receive_basis, a.knitting_source,booking_id,sum(b.cons_quantity) as receive_qnty from inv_transaction b, inv_receive_master a where a.id=b.mst_id $entry_form_ref and a.status_active=1 $sql_cond group by a.id, a.recv_number_prefix_num ,a.recv_number,a.challan_no,a.receive_date,a.receive_basis,a.insert_date,a.knitting_source,booking_id order by a.id";
	//echo $sql;
	?>
    	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="720">
        	<thead>
                <tr>
                	<th width="50">SL</th>
                    <th width="100">MRR No</th>
                    <th width="50">Year</th>
                    <th width="100">Challan No</th>
                    <th width="100">Receive Date</th>
                    <th width="100">Receive Basis</th>
                    <th>Receive Qnty</th>
                </tr>
            </thead>
        </table>
        
        <div style="width:720px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
    	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="700" id="list_view">
            <tbody>
            <?
			$i=1;
			$sql_result=sql_select($sql);
			foreach($sql_result as $row)
			{
				if ($k%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";

				?>
            	<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;" onClick="js_set_value('<? echo $row[csf("id")]; ?>_<? echo $row[csf("recv_number")]; ?>_<? echo $row[csf("booking_id")]; ?>')">
                	<td width="50" align="center"><p><? echo $i; ?>&nbsp;</p></td>
                    <td width="100" align="center"><p><? echo $row[csf("recv_number_prefix_num")]; ?>&nbsp;</p></td>
                    <td width="50" align="center"><p><? echo $row[csf("year")]; ?>&nbsp;</p></td>
                    <td width="100" ><p><? echo $row[csf("challan_no")]; ?>&nbsp;</p></td>
                    <td width="100" align="center"><p><? if($row[csf("receive_date")]!="" && $row[csf("receive_date")]!="0000-00-00") echo change_date_format($row[csf("receive_date")]); ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $receive_basis_arr[$row[csf("receive_basis")]]; ?>&nbsp;</p></td>
                    <td align="right"><p><? echo number_format($row[csf("receive_qnty")],2,".",""); ?>&nbsp;</p></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
        </table>
        </div>
    <?	
	exit();
	
}

if($action=="populate_data_from_data")
{
	
	$sql = "select id,recv_number,entry_form,company_id,receive_basis,receive_purpose,lc_no,knitting_source,knitting_company,dyeing_source,supplier_id 
			from inv_receive_master 
			where id='$data' and entry_form in(17)";
	//echo $sql;
	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#txt_received_id').val('".$row[csf("id")]."');\n";
		echo "$('#txt_mrr_no').val('".$row[csf("recv_number")]."');\n";
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		
		if($row[csf("supplier_id")]=="")
		{$kniting_company=0;}
		else
		{$kniting_company=$row[csf("supplier_id")];}
		$kniting_source=$row[csf("knitting_source")];
		$company_id=$row[csf("company_id")];
		//echo $kniting_company.'_'.$kniting_source.'_'.$company_id;
		echo "load_drop_down( 'requires/woven_finish_fab_receive_rtn_controller', $kniting_company+'_'+$kniting_source+'_'+$company_id, 'load_drop_down_knitting_com','knitting_com');\n";
		
		if($row[csf("receive_basis")]==1)
		{
			echo "$('#txt_pi_no').removeAttr('disabled','disabled');\n";
		}
		else
		{
			echo "$('#txt_pi_no').attr('disabled','disabled');\n";
		}
		
		//right side list view
		echo"show_list_view('".$row[csf("id")]."','show_product_listview','list_product_container','requires/woven_finish_fab_receive_rtn_controller','');\n";
   	}	
	exit();	
}

if($action=="load_drop_down_knitting_com")
{
	$data = explode("_",$data);
	$kniting_company=$data[0];
	$company_id=$data[2];
	//print_r($data);
	
	/*if($data[1]==1)
	{
		echo create_drop_down( "cbo_return_to", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select Knit Company--", "$company_id", "",1 );
	}
	else if($data[1]==0)
	{*/	
		echo create_drop_down( "cbo_return_to", 170, "select a.id,a.supplier_name from lib_supplier a","id,supplier_name", 1, "--Select--", "$kniting_company", "" ,1);
	/*	//echo $kniting_company;
	}
	else
	{
		echo create_drop_down( "cbo_return_to", 170, $blank_array,"",1, "--Select Knit Company--", 0, "" ,1);
	}*/
	exit();
}




//right side product list create here--------------------//
if($action=="show_product_listview")
{ 
	$batch_arr=return_library_array("select id, batch_no from pro_batch_create_mst","id","batch_no");
	
 	$mrr_no = $data;
	$sql = "select  a.id as mrr_id, c.id as prod_id, c.product_name_details,c.detarmination_id, b.batch_id, b.color_id, b.trans_id as tr_id, b.receive_qnty as receive_qnty, b.rack_no, b.shelf_no,b.uom,d.cutable_width,d.weight_type,d.weight_editable ,d.width_editable,a.booking_no,d.cons_rate,b.body_part_id,d.fabric_ref,d.rd_no,a.company_id,a.store_id    
        from inv_receive_master a, pro_finish_fabric_rcv_dtls b, product_details_master c,inv_transaction d 
        where a.id=b.mst_id and b.prod_id=c.id and c.id=d.prod_id and b.trans_id=d.id and a.id=d.mst_id and a.id='$mrr_no' and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0"; 

	//echo $sql;
  	$result = sql_select($sql);	
	$i=1; 
 	?>
    	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="730">
        	<thead>
                <tr>
                	<th width="30">SL</th>
                    <th width="40">Product Id</th>
                    <th width="140">Product Name</th>
                    <th width="70">Full Width</th>
                    <th width="70">Cutable Width</th>
                    <th width="70">Weight</th>
                    <th width="70">Weight Type</th>
                    <th width="70">Batch No.</th>
                    <th width="70">Color</th>
                    <th width="50">Rack</th>
                    <th width="50">Shelf</th>
                    <th>Curr.Stock</th>
                </tr>
            </thead>
            <tbody>
            	<? foreach($result as $row)
				{ 
					if ($i%2==0)$bgcolor="#E9F3FF";						
					else $bgcolor="#FFFFFF";
					$transactionID=$row[csf("tr_id")];
					$total_return=return_field_value("sum(issue_qnty) as issue_qnty","inv_mrr_wise_issue_details ","status_active=1 and prod_id='".$row[csf("prod_id")]."' and recv_trans_id ='".$row[csf("tr_id")]."'","issue_qnty" );
					$balance_qnty=$row[csf("receive_qnty")]-$total_return;
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("mrr_id")];?>,<? echo $row[csf("tr_id")];?>,<? echo $row[csf("batch_id")];?>,<? echo $row[csf("rd_no")];?>,<? echo $row[csf("fabric_ref")];?>,<? echo $row[csf("cons_rate")];?>,<? echo $row[csf("body_part_id")];?>,<? echo $row[csf("color_id")];?>,<? echo $row[csf("prod_id")];?>,<? echo $row[csf("detarmination_id")];?>,<? echo $row[csf("company_id")];?>,<? echo $row[csf("store_id")];?>","item_details_form_input","requires/woven_finish_fab_receive_rtn_controller")' style="cursor:pointer" >
					<td><? echo $i; ?></td>
                    <td align="center"><? echo $row[csf("prod_id")]; ?></td>
					<td><? echo $row[csf("product_name_details")]; ?></td>
					<td align="center"><? echo $row[csf("width_editable")]; ?></td>
					<td align="center"><? echo $row[csf("cutable_width")]; ?></td>
					<td align="center"><? echo $row[csf("weight_editable")]; ?></td>
					<td align="center"><? echo $fabric_weight_type[$row[csf("weight_type")]]; ?></td>
                    <td><? echo $batch_arr[$row[csf("batch_id")]]; ?></td>
                    <td><? echo $color_arr[$row[csf("color_id")]]; ?></td>
                    <td><? echo $row[csf("rack_no")]; ?></td>
                    <td><? echo $row[csf("shelf_no")]; ?></td>
					<td align="right"><? echo number_format($balance_qnty,0); ?></td>
					</tr>
					<? 
				$i++; 
				} ?>
            </tbody>
        </table>
     </fieldset>   
	<?	 
	exit();
}



//child form data input here-----------------------------//
if($action=="item_details_form_input")
{
	$data_ref=explode(",",$data);
	$sql = "select a.id as mst_id, a.booking_without_order, a.store_id, b.company_id, b.id as trans_id,c.floor,c.room, c.rack_no, c.shelf_no,c.bin, c.body_part_id, b.cons_uom, b.cons_rate, b.cons_quantity, b.cons_amount, b.balance_qnty, b.balance_amount,weight_type,b.cutable_width,b.fabric_ref, c.batch_id, c.color_id, c.order_id, d.id as prod_id, d.product_name_details, d.gsm,d.weight, d.dia_width, d.current_stock,d.detarmination_id,c.booking_no as pi_basis_booking,a.booking_no as wo_basis_booking_no, a.receive_basis   
			from inv_receive_master a, inv_transaction b, pro_finish_fabric_rcv_dtls c, product_details_master d
 			where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and a.id=".$data_ref[0]." and b.id=".$data_ref[1]." and a.status_active=1 and b.status_active=1 and c.status_active=1"; 
 	//echo $sql;die;
	$result = sql_select($sql);
	//===================Global stock============
	$txt_fabric_ref_cond = ($data_ref[4]!="" && $data_ref[4]!=0) ? "and b.fabric_ref='$data_ref[4]'":"";
	$store_cond = ($data_ref[11]!="") ? "and b.store_id='$data_ref[11]'":"";
	if($data_ref[6]!=""){$bodyPartCond_2="and b.body_part_id=$data_ref[6]";}
	$issue_rtn_recv_qnty=sql_select("select sum(a.quantity) as qnty,c.booking_no,sum(b.cons_rate*a.quantity) as cons_amount  from inv_transaction b, product_details_master x,order_wise_pro_details a,wo_po_break_down y,wo_booking_dtls c,wo_po_details_master d,wo_pre_cost_fabric_cost_dtls e,lib_yarn_count_determina_mst f  where b.prod_id=x.id and x.id=a.prod_id and b.id=a.trans_id and a.po_breakdown_id=y.id and y.job_no_mst=d.job_no and d.job_no=c.job_no and d.job_no=e.job_no and c.pre_cost_fabric_cost_dtls_id=e.id  and e.lib_yarn_count_deter_id=f.id  and y.id=c.po_break_down_id and x.color='".$data_ref[7]."'  $bodyPartCond_2 and x.detarmination_id='".$data_ref[9]."' and b.pi_wo_batch_no=$data_ref[2]  and b.status_active=1 and a.entry_form=209 and b.is_deleted=0 and b.status_active=1 and x.is_deleted=0 and x.status_active=1 and a.is_deleted=0 and a.status_active=1 and b.item_category=3 and b.transaction_type=4  and b.prod_id=$data_ref[8] $txt_fabric_ref_cond  group by c.booking_no ");
	foreach($issue_rtn_recv_qnty as $row)
	{
		$cumu_issue_rtn_recv_arr[$row[csf('booking_no')]]['qnty']=$row[csf('qnty')];
		$cumu_issue_rtn_recv_arr[$row[csf('booking_no')]]['cons_amount']=$row[csf('cons_amount')];
	}

	/*if($update_id!="")
	{
		$existing_rev_rtn_id_cond="and b.id<> $update_id";
	}*/
	$prev_recv_rtn_global_sql=sql_select("select b.transaction_type,sum(a.quantity) as quantity,c.booking_no,sum(b.cons_rate*a.quantity) as cons_amount from inv_issue_master x,inv_transaction b,inv_mrr_wise_issue_details y, order_wise_pro_details a,wo_booking_dtls c,wo_po_details_master d,wo_pre_cost_fabric_cost_dtls e,lib_yarn_count_determina_mst f where  x.id=b.mst_id and b.id=y.issue_trans_id and y.issue_trans_id=a.trans_id and b.id=a.trans_id and a.po_breakdown_id=c.po_break_down_id and c.job_no=d.job_no and d.job_no=e.job_no and c.pre_cost_fabric_cost_dtls_id=e.id  and e.lib_yarn_count_deter_id=f.id and a.entry_form in (202) and b.transaction_type in (3) and a.trans_type in (3) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.item_category=3 and b.company_id=$data_ref[10] and b.prod_id=$data_ref[8] and a.prod_id=$data_ref[8] and b.pi_wo_batch_no=$data_ref[2] $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond $bodyPartCond_2  $existing_rev_rtn_id_cond and c.fabric_color_id=$data_ref[7] $txt_fabric_ref_cond and e.lib_yarn_count_deter_id=$data_ref[9] group by b.transaction_type,c.booking_no");
	foreach($prev_recv_rtn_global_sql as $row)
	{
		$cumu_recv_rtn_qty_global_arr[$row[csf('booking_no')]]['qnty']=$row[csf('quantity')];
		$cumu_recv_rtn_qty_global_arr[$row[csf('booking_no')]]['cons_amount']=$row[csf('cons_amount')];
	}
	$prev_recv_rtn_sql=sql_select("select a.po_breakdown_id as order_id, b.transaction_type,a.quantity as quantity,c.booking_no from inv_issue_master x,inv_transaction b,inv_mrr_wise_issue_details y, order_wise_pro_details a,wo_booking_dtls c,wo_po_details_master d,wo_pre_cost_fabric_cost_dtls e,lib_yarn_count_determina_mst f where  x.id=b.mst_id and b.id=y.issue_trans_id and y.issue_trans_id=a.trans_id and b.id=a.trans_id and a.po_breakdown_id=c.po_break_down_id and c.job_no=d.job_no and d.job_no=e.job_no and c.pre_cost_fabric_cost_dtls_id=e.id  and e.lib_yarn_count_deter_id=f.id and y.recv_trans_id='$data_ref[1]' and a.entry_form in (202) and b.transaction_type in (3) and a.trans_type in (3) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.item_category=3 and b.company_id=$data_ref[10] and b.prod_id=$data_ref[8] and a.prod_id=$data_ref[8] and b.pi_wo_batch_no=$data_ref[2] $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond $bodyPartCond_2  $existing_rev_rtn_id_cond and c.fabric_color_id=$data_ref[7] $txt_fabric_ref_cond and e.lib_yarn_count_deter_id=$data_ref[9]");
	foreach($prev_recv_rtn_sql as $row)
	{
		$cumu_recv_rtn_qty_arr[$row[csf('booking_no')]]['qnty']+=$row[csf('quantity')];
	}

	//$prev_issue_sql=sql_select("select b.transaction_type,sum(a.quantity) as quantity,sum(b.cons_rate*a.quantity) as cons_amount,c.booking_no from inv_issue_master x,inv_wvn_finish_fab_iss_dtls y,inv_transaction b, order_wise_pro_details a,wo_booking_dtls c,wo_po_details_master d,wo_pre_cost_fabric_cost_dtls e,lib_yarn_count_determina_mst f where x.id=y.mst_id and y.trans_id=b.id and b.id=a.trans_id and a.po_breakdown_id=c.po_break_down_id and c.job_no=d.job_no and d.job_no=e.job_no and c.pre_cost_fabric_cost_dtls_id=e.id  and e.lib_yarn_count_deter_id=f.id and a.entry_form in (19) and b.transaction_type in (2) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.item_category=3 and b.company_id=$data_ref[10] and b.prod_id=$data_ref[8] and a.prod_id=$data_ref[8]  and b.batch_id=$data_ref[2] $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond $bodyPartCond_2 and c.fabric_color_id=$data_ref[7]  $txt_fabric_ref_cond  and e.lib_yarn_count_deter_id=$data_ref[9] group by b.transaction_type,c.booking_no");
	$prev_issue_sql=sql_select("select b.transaction_type,a.quantity,b.cons_rate,c.booking_no from inv_issue_master x,inv_wvn_finish_fab_iss_dtls y,inv_transaction b, order_wise_pro_details a,wo_booking_dtls c,wo_po_details_master d,wo_pre_cost_fabric_cost_dtls e,lib_yarn_count_determina_mst f where x.id=y.mst_id and y.trans_id=b.id and b.id=a.trans_id and a.po_breakdown_id=c.po_break_down_id and c.job_no=d.job_no and d.job_no=e.job_no and c.pre_cost_fabric_cost_dtls_id=e.id  and e.lib_yarn_count_deter_id=f.id and a.entry_form in (19) and b.transaction_type in (2) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.item_category=3 and b.company_id=$data_ref[10] and b.prod_id=$data_ref[8] and a.prod_id=$data_ref[8]  and b.batch_id=$data_ref[2] $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond $bodyPartCond_2 and c.fabric_color_id=$data_ref[7]  $txt_fabric_ref_cond  and e.lib_yarn_count_deter_id=$data_ref[9] group by b.transaction_type,a.quantity,b.cons_rate,c.booking_no");
	foreach($prev_issue_sql as $row)
	{
		$cumu_issue_qty_arr[$row[csf('booking_no')]]['qnty']+=$row[csf('quantity')];
		$cumu_issue_qty_arr[$row[csf('booking_no')]]['cons_amount']+=$row[csf('cons_rate')]*$row[csf('quantity')];
	}

	$prev_transIn_sql=sql_select("select b.transaction_type,sum(a.quantity) as quantity,sum(b.cons_rate*a.quantity) as cons_amount,c.booking_no from inv_transaction b, order_wise_pro_details a,wo_booking_dtls c,wo_po_details_master d,wo_pre_cost_fabric_cost_dtls e,lib_yarn_count_determina_mst f where b.id=a.trans_id and a.po_breakdown_id=c.po_break_down_id and c.job_no=d.job_no and d.job_no=e.job_no and c.pre_cost_fabric_cost_dtls_id=e.id  and e.lib_yarn_count_deter_id=f.id and a.entry_form in (258) and b.transaction_type in (6) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.item_category=3 and b.company_id=$data_ref[10]  and b.prod_id=$data_ref[8] and a.prod_id=$data_ref[8] and b.pi_wo_batch_no=$data_ref[2] $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond $bodyPartCond_2  and c.fabric_color_id=$data_ref[7] $txt_fabric_ref_cond and e.lib_yarn_count_deter_id=$data_ref[9] group by b.transaction_type,c.booking_no");
	foreach($prev_transIn_sql as $row)
	{
		$cumu_transIn_qty_arr[$row[csf('booking_no')]]['qnty']=$row[csf('quantity')];
		$cumu_transIn_qty_arr[$row[csf('booking_no')]]['cons_amount']=$row[csf('cons_amount')];
	}

	$prev_transOut_sql=sql_select("select b.transaction_type,sum(a.quantity) as quantity,sum(b.cons_rate*a.quantity) as cons_amount,c.booking_no from inv_transaction b, order_wise_pro_details a,wo_booking_dtls c,wo_po_details_master d,wo_pre_cost_fabric_cost_dtls e,lib_yarn_count_determina_mst f where b.id=a.trans_id and a.po_breakdown_id=c.po_break_down_id and c.job_no=d.job_no and d.job_no=e.job_no and c.pre_cost_fabric_cost_dtls_id=e.id  and e.lib_yarn_count_deter_id=f.id and a.entry_form in (258) and b.transaction_type in (6) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.item_category=3 and b.company_id=$data_ref[10]  and b.prod_id=$data_ref[8] and a.prod_id=$data_ref[8] and b.pi_wo_batch_no=$data_ref[2] $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond $bodyPartCond_2  $txt_width_cond $txt_weight_cond and c.fabric_color_id=$data_ref[7]  $txt_fabric_ref_cond and e.lib_yarn_count_deter_id=$data_ref[9] group by b.transaction_type,c.booking_no");
	foreach($prev_transOut_sql as $row)
	{
		$cumu_transOut_qty_arr[$row[csf('booking_no')]]['qnty']=$row[csf('quantity')];
		$cumu_transOut_qty_arr[$row[csf('booking_no')]]['cons_amount']=$row[csf('cons_amount')];
	}

	$recv_global_qnty_sql = sql_select("select  sum(c.quantity) as quantity,a.booking_no,sum(b.cons_rate*c.quantity) as cons_amount    
	from pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e 
	where a.id=b.batch_id and b.id = c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and b.item_category=3 and b.transaction_type in(1) and c.trans_type in(1) and c.entry_form in(17) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	and c.status_active=1 and c.is_deleted=0 and b.batch_id>0 and b.company_id=$data_ref[10]  and b.store_id='$data_ref[11]' and b.batch_id='$data_ref[2]' and b.prod_id=$data_ref[8] and a.id='$data_ref[2]' $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond $bodyPartCond_2 
	group by a.booking_no");

	foreach($recv_global_qnty_sql as $row)
	{
		$prev_recv_qnty_arr[$row[csf('booking_no')]]['qnty']=$row[csf('quantity')];
		$prev_recv_qnty_arr[$row[csf('booking_no')]]['cons_amount']=$row[csf('cons_amount')];
	}
	//=====================End global stock===========

	foreach($result as $row)
	{
		$batch_no=return_field_value("batch_no","pro_batch_create_mst","id='".$row[csf("batch_id")]."'","batch_no" );
		
		$variable_setting_production=return_field_value("fabric_roll_level","variable_settings_production","company_name='".$row[csf("company_id")]."'  and item_category_id=3 and variable_list=3 and status_active=1","fabric_roll_level");
		
 		echo "$('#txt_item_description').val('".$row[csf("product_name_details")]."');\n";
 		echo "$('#hidden_fabrication_id').val('".$row[csf("detarmination_id")]."');\n";
 		echo "$('#hidden_fab_ref').val('".$row[csf("fabric_ref")]."');\n";
		echo "$('#txt_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#txt_weight').val('".$row[csf("weight")]."');\n";
		echo "$('#cbo_weight_type').val('".$row[csf("weight_type")]."');\n";
		echo "$('#txt_width').val('".$row[csf("dia_width")]."');\n";
		echo "$('#txt_cutable_width').val('".$row[csf("cutable_width")]."');\n";
		echo "$('#hidden_rd_no').val('".$data_ref[3]."');\n";
		
		
		echo "$('#txt_batch_no').val('".$batch_no."');\n";
		echo "$('#cbo_body_part').val('".$row[csf("body_part_id")]."');\n";

		echo "load_room_rack_self_bin('requires/woven_finish_fab_receive_rtn_controller*3', 'store','store_td', '".$row[csf('company_id')]."','"."',this.value);\n";
		echo "$('#cbo_store_name').val('".$row[csf("store_id")]."');\n";

		echo "load_room_rack_self_bin('requires/woven_finish_fab_receive_rtn_controller', 'floor','floor_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."',this.value);\n";
		echo "$('#cbo_floor').val('".$row[csf("floor")]."');\n";
		echo "load_room_rack_self_bin('requires/woven_finish_fab_receive_rtn_controller', 'room','room_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor')]."',this.value);\n";
		echo "$('#cbo_room').val('".$row[csf("room")]."');\n";
		echo "load_room_rack_self_bin('requires/woven_finish_fab_receive_rtn_controller', 'rack','rack_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor')]."','".$row[csf('room')]."',this.value);\n";
		echo "$('#txt_rack').val('".$row[csf("rack_no")]."');\n";
		echo "load_room_rack_self_bin('requires/woven_finish_fab_receive_rtn_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor')]."','".$row[csf('room')]."','".$row[csf('rack_no')]."',this.value);\n";	
		echo "$('#txt_shelf').val('".$row[csf("shelf_no")]."');\n";
		echo "load_room_rack_self_bin('requires/woven_finish_fab_receive_rtn_controller', 'bin','bin_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor')]."','".$row[csf('room')]."','".$row[csf('rack_no')]."','".$row[csf('shelf_no')]."',this.value);\n";	
		echo "$('#cbo_bin').val('".$row[csf("bin")]."');\n";

		/*echo "$('#txt_rack').val('".$row[csf("rack_no")]."');\n";
		echo "$('#txt_shelf').val('".$row[csf("shelf_no")]."');\n";
		echo "$('#cbo_store_name').val('".$row[csf("store_id")]."');\n";*/
		
		echo "$('#txt_return_qnty').val('');\n";
		echo "$('#txt_break_qnty').val('');\n";
		echo "$('#txt_break_roll').val('');\n";
		echo "$('#txt_order_id_all').val('');\n";
		echo "$('#txt_roll').val('');\n";
		
		
		echo "$('#hidden_batch_id').val('".$row[csf("batch_id")]."');\n";
		
		if($row[csf("order_id")]=="")
		{
			echo "$('#txt_return_qnty').removeAttr('placeholder').removeAttr('readonly').removeAttr('onDblClick').attr('placeholder','Write');\n";
		}
		else
		{
			echo "$('#txt_return_qnty').removeAttr('placeholder').removeAttr('readonly').removeAttr('onDblClick').attr('placeholder','Double Click To Search').attr('readonly','').attr('onDblClick','openmypage_rtn_qty();');\n";
		}
		
		/*if($variable_setting_production==1)
		{
			echo "$('#txt_roll').attr('readonly');\n";
		}
		else
		{*/
			echo "$('#txt_roll').removeAttr('readonly');\n";
		//}
		
		echo "$('#cbo_uom').val('".$row[csf("cons_uom")]."');\n";
		echo "$('#txt_color_name').val('".$color_arr[$row[csf("color_id")]]."');\n";
		echo "$('#hidden_color_id').val('".$row[csf("color_id")]."');\n";

		if($row[csf("receive_basis")]==2 || $row[csf("receive_basis")]==4)
		{
			$cumu_issue_rtn_recv=$cumu_issue_rtn_recv_arr[$row[csf('wo_basis_booking_no')]]['qnty'];
			$cumu_recv_rtn_qty=$cumu_recv_rtn_qty_global_arr[$row[csf('wo_basis_booking_no')]]['qnty'];
			$cumu_issue_qty=$cumu_issue_qty_arr[$row[csf('wo_basis_booking_no')]]['qnty'];
			$cumu_transIn_qty=$cumu_transIn_qty_arr[$row[csf('wo_basis_booking_no')]]['qnty'];
			$cumu_transOut_qty=$cumu_transOut_qty_arr[$row[csf('wo_basis_booking_no')]]['qnty'];
			$prev_recv_qnty=$prev_recv_qnty_arr[$row[csf('wo_basis_booking_no')]]['qnty'];
			//cons Amount
			$cumu_issue_rtn_recv_amount=$cumu_issue_rtn_recv_arr[$row[csf('wo_basis_booking_no')]]['cons_amount'];
			$cumu_recv_rtn_amount=$cumu_recv_rtn_qty_global_arr[$row[csf('wo_basis_booking_no')]]['cons_amount'];
			$cumu_issue_amount=$cumu_issue_qty_arr[$row[csf('wo_basis_booking_no')]]['cons_amount'];
			$cumu_transIn_amount=$cumu_transIn_qty_arr[$row[csf('wo_basis_booking_no')]]['cons_amount'];
			$cumu_transOut_amount=$cumu_transOut_qty_arr[$row[csf('wo_basis_booking_no')]]['cons_amount'];
			$prev_recv_amount=$prev_recv_qnty_arr[$row[csf('wo_basis_booking_no')]]['cons_amount'];

		}
		else
		{
			$cumu_issue_rtn_recv=$cumu_issue_rtn_recv_arr[$row[csf('pi_basis_booking')]]['qnty'];
			$cumu_recv_rtn_qty=$cumu_recv_rtn_qty_global_arr[$row[csf('pi_basis_booking')]]['qnty'];
			$cumu_issue_qty=$cumu_issue_qty_arr[$row[csf('pi_basis_booking')]]['qnty'];
			$cumu_transIn_qty=$cumu_transIn_qty_arr[$row[csf('pi_basis_booking')]]['qnty'];
			$cumu_transOut_qty=$cumu_transOut_qty_arr[$row[csf('pi_basis_booking')]]['qnty'];
			$prev_recv_qnty=$prev_recv_qnty_arr[$row[csf('pi_basis_booking')]]['qnty'];
			//cons Amount
			$cumu_issue_rtn_recv_amount=$cumu_issue_rtn_recv_arr[$row[csf('pi_basis_booking')]]['cons_amount'];
			$cumu_recv_rtn_amount=$cumu_recv_rtn_qty_global_arr[$row[csf('pi_basis_booking')]]['cons_amount'];
			$cumu_issue_amount=$cumu_issue_qty_arr[$row[csf('pi_basis_booking')]]['cons_amount'];
			$cumu_transIn_amount=$cumu_transIn_qty_arr[$row[csf('pi_basis_booking')]]['cons_amount'];
			$cumu_transOut_amount=$cumu_transOut_qty_arr[$row[csf('pi_basis_booking')]]['cons_amount'];
			$prev_recv_amount=$prev_recv_qnty_arr[$row[csf('pi_basis_booking')]]['cons_amount'];
		}
		$availableRecAmount=($prev_recv_amount+$cumu_issue_rtn_recv_amount+$cumu_transIn_amount)-($cumu_issue_amount+$cumu_transOut_amount+$cumu_recv_rtn_amount);
		$availableRecQnty=($prev_recv_qnty+$cumu_issue_rtn_recv+$cumu_transIn_qty)-($cumu_issue_qty+$cumu_transOut_qty+$cumu_recv_rtn_qty);
		$title="(Global prev recv qnty=".$prev_recv_qnty." + Global Prev issue return qnty=$cumu_issue_rtn_recv"." + Global transfer IN qnty=$cumu_transIn_qty".")-("." Global prev issue qnty=$cumu_issue_qty + Global transfer Out qnty=$cumu_transOut_qty + Global prev recv return=$cumu_recv_rtn_qty )";


		$avgRate=$availableRecAmount/$availableRecQnty;

		echo "$('#hidden_rate').val('".$avgRate."');\n";

		/*echo "select sum(b.issue_qnty) as issue_qnty from inv_mrr_wise_issue_details b where b.status_active=1 and b.prod_id='".$row[csf("prod_id")]."' and b.recv_trans_id='".$row[csf("trans_id")]."'";*/
		$cumilitive_rtn=return_field_value("sum(b.issue_qnty) as issue_qnty","inv_mrr_wise_issue_details b","b.status_active=1 and b.prod_id='".$row[csf("prod_id")]."' and b.recv_trans_id='".$row[csf("trans_id")]."'","issue_qnty" );
		$yet_to_issue=$row[csf("cons_quantity")]-$cumilitive_rtn;

		echo "$('#hidden_receive_trans_id').val('".$row[csf("trans_id")]."');\n";
		echo "$('#txt_fabric_received').val('".$row[csf("cons_quantity")]."');\n";
		//echo "$('#txt_global_stock').val('".$row[csf("current_stock")]."');\n";
		echo "$('#txt_global_stock').val('".$availableRecQnty."');\n";
		echo "$('#txt_cumulative_issued').val('$cumilitive_rtn');\n"; // running
		echo "$('#txt_yet_to_issue').val('$yet_to_issue');\n";

		echo "$('#cbo_store_name').attr('disabled','disabled');\n";
		echo "$('#cbo_floor').attr('disabled','disabled');\n";
		echo "$('#cbo_room').attr('disabled','disabled');\n";
		echo "$('#txt_rack').attr('disabled','disabled');\n";
		echo "$('#txt_shelf').attr('disabled','disabled');\n";
		echo "$('#cbo_bin').attr('disabled','disabled');\n";
		
	}
	exit();		
}

if($action=="return_po_popup_booking_wise")
{
	echo load_html_head_contents("Receive Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$txt_received_id=str_replace("'","",$txt_received_id);
	$txt_prod_id=str_replace("'","",$txt_prod_id);
	$hidden_receive_trans_id=str_replace("'","",$hidden_receive_trans_id);
	$update_id=str_replace("'","",$update_id);
	
	$variable_setting_production=return_field_value("fabric_roll_level","variable_settings_production","company_name='$cbo_company_id'  and item_category_id=3 and variable_list=3 and status_active=1","fabric_roll_level");
	//echo $variable_setting_production.Fuad;//die;
	/*if($variable_setting_production==1)
	{
		$table_width=600;
		$txt_break_roll=explode("_",$txt_break_roll);
		foreach($txt_break_roll as $val)
		{
			$txt_break_roll_data=explode("**",$val);
			$po_id=$txt_break_roll_data[0];
			$roll_no=$txt_break_roll_data[1];
			$qty=$txt_break_roll_data[2];
			$roll_id=$txt_break_roll_data[3];
			
			$order_wise_qnty_arr[$po_id][$roll_id]=$qty;
		}
	}
	else
	{*/
		$table_width=1000;
		$txt_break_qnty=explode("_",$txt_break_qnty);
		foreach($txt_break_qnty as $val)
		{
			$txt_break_qnty_data=explode("**",$val);
			$po_id=$txt_break_qnty_data[0];
			$qty=$txt_break_qnty_data[1];
			
			$order_wise_qnty_arr[$po_id]=$qty;
		}
	//}
	//print_r($order_wise_qnty_arr);
	/*if($update_id>0)
	{
		$order_sql=sql_select("select po_breakdown_id,quantity from order_wise_pro_details where trans_id=$update_id and trans_type=3 and entry_form=46 and status_active=1");
		foreach($order_sql as $row)
		{
			$order_wise_qnty_arr[$row[csf("po_breakdown_id")]]=$row[csf("quantity")];
		}
	}
	
	if($variable_setting_production==1)
	{
		$table_width=600;
	}
	else
	{
		$table_width=500;
	}*/
	?>
<script>
	function js_set_value()
	{
		var save_data=''; var tot_issue_qnty='';
		var po_id_array = new Array(); var po_no='';
		var chk_status=0;var tot_row=0;
		$("#pop_table tbody").find('tr').each(function()
		{
			var row_id=$(this).find('input[name="txtPoId[]"]').attr('id');
			var row_id_split=row_id.split("_");
			var row_id_sl=row_id_split[1]*1;

			var txtIssueQntyx = $('#issueqnty_'+row_id_sl).val();
			if(txtIssueQntyx>0)
			{
				var txtHdnPoRcvRatio= $('#txtHdnPoRcvRatio_'+row_id_sl).val();
				tot_row++;
			}
			//alert(txtHdnPoRcvRatio);				

			var recvQnty=($("#recevqnty_"+row_id_sl).val()*1);
			var cumu_qnty=($("#cumulativeIssue_"+row_id_sl).val()*1);
			var issue_qnty=($("#issueqnty_"+row_id_sl).val()*1);
			var hiddenissue_qnty=($("#hiddenissueqnty_"+row_id_sl).val()*1);
			var balacne_issue=recvQnty-cumu_qnty;

			//if(balacne_issue<issue_qnty)
			if(recvQnty<issue_qnty)
			{
				alert("Receive Return Qnty Exceeds Balance Qnty. Balance = " + (balacne_issue) );
				$('#issueqnty_'+row_id_sl).val(0);
				chk_status+=1;
			}


			var txtPoId=$(this).find('input[name="txtPoId[]"]').val();
			var txtPoName=$(this).find('input[name="txtPoName[]"]').val();
			var txtIssueQnty=$(this).find('input[name="issueqnty[]"]').val()*1;

			if(txtIssueQnty*1>0)
			{
				if(txtIssueQntyx>0)
				{

					var txtHdnPoRcvRatio= $('#txtHdnPoRcvRatio_'+row_id_sl).val();
					var txtHdnPoRevRat=txtHdnPoRcvRatio.split(",");
						var totOrdReqQnty=0;var po_req_ref=new Array();
						for(var j=0; j<txtHdnPoRevRat.length; j++)
						{
							var txtHdnPo=txtHdnPoRevRat[j].split("=");
							var txtPoIdx=txtHdnPo[0];
							totOrdReqQnty+=txtHdnPo[1]*1;
							po_req_ref[txtPoIdx]=txtHdnPo[1]*1;
						}
						for(var k=0; k<txtHdnPoRevRat.length; k++)
						{
							var txtHdnPo=txtHdnPoRevRat[k].split("=");
							var txtPoIdx=txtHdnPo[0];
							var txtIssueQuantity=(po_req_ref[txtPoIdx]/totOrdReqQnty)*txtIssueQntyx;
							if(save_data=="")
							{
								save_data=txtPoIdx+"_"+txtIssueQuantity;
							}
							else
							{
								save_data+=","+txtPoIdx+"_"+txtIssueQuantity;
							}
						}

					

				}


				/*if(save_data=="")
				{
					save_data=txtPoId+"_"+txtIssueQnty;
				}
				else
				{
					save_data+=","+txtPoId+"_"+txtIssueQnty;
				}*/

				if( jQuery.inArray(txtPoId, po_id_array) == -1 )
				{
					po_id_array.push(txtPoId);
					if(po_no=="") po_no=txtPoName; else po_no+=","+txtPoName;
				}

				

				tot_issue_qnty=tot_issue_qnty*1+txtIssueQnty*1;
			}
		});
		if(chk_status>0)
		{
			return;
		}
		$('#save_data').val( save_data );
		$('#tot_issue_qnty').val(tot_issue_qnty);
		$('#all_po_id').val( po_id_array );
		$('#all_po_no').val( po_no );
		//return;
		parent.emailwindow.hide();
	}
	
	/*function js_set_value()
	{
		var table_legth=$('#pop_table tbody tr').length;
		var break_qnty=break_roll=break_id="";
		var tot_qnty=0; var tot_roll='';
		for(var i=1; i<=table_legth; i++)
		{
			//if(i!=1) break_qnty+="_";
			tot_qnty +=($("#issueqnty_"+i).val()*1);
			if(break_qnty!="") break_qnty +="_";
			break_qnty+=($("#poId_"+i).val()*1)+'**'+($("#issueqnty_"+i).val()*1);
			if(break_roll!="") break_roll +="_";
			break_roll+=($("#poId_"+i).val()*1)+'**'+($("#roll_"+i).val()*1)+'**'+($("#issueqnty_"+i).val()*1)+'**'+($("#rollId_"+i).val()*1);
			if(break_id!="") break_id +=",";
			break_id+=($("#poId_"+i).val()*1);
			
			if($("#issueqnty_"+i).val()*1>0 && $("#rollId_"+i).val()*1>0)
			{
				tot_roll+=1;
			}
		}
		$("#tot_qnty").val(tot_qnty);
		$("#break_qnty").val(break_qnty);
		$("#break_roll").val(break_roll);
		$("#break_order_id").val(break_id);
		$("#tot_roll").val(tot_roll);
		parent.emailwindow.hide();
	}*/
	
	function fn_calculate(id)
	{
		var placeholder_value =$('#issueqnty_'+id).attr('placeholder')*1;
		var issued_qnty =$('#hideQnty_'+ id).val()*1;
		var qnty =$('#issueqnty_'+id).val()*1;
		var global_available_qnty =$('#recevqnty_'+id).val()*1;
		
		/*if(qnty>(placeholder_value+issued_qnty))
		{
			alert("Return Quantity Can not be Greater Than Receive Quantity" );
			if(issued_qnty==0) issued_qnty='';
			$('#issueqnty_'+id).val(0);
		}*/

		if(qnty>(placeholder_value+issued_qnty) || qnty>(global_available_qnty+issued_qnty))
		{
			alert("Return Quantity Can not be Greater Than Receive Quantity" );
			if(issued_qnty==0) issued_qnty='';
			$('#issueqnty_'+id).val(0);
		}
	}
	
	
</script>

</head>

<body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" id="pop_table">
                <thead>
                    <tr>  
                    	<th width="140">Job No </th>
                        <th width="140">Style Ref.</th>
                        <th width="140">Booking No</th>              	 
                        <th width="100">Receive Quantity</th>
                        <th width="100">Available Quantity</th>
                        <th width="100">Cumulative Receive Return</th>
                        <?
						/*if($variable_setting_production==1)
						{
							?>
							<th>Roll</th>
							<?
						}*/
						?>           
                        <th width="120">Return Quantity</th>


                    </tr>
                </thead>
                <tbody id="pop_table">
                <?
	                if($cbo_body_part!=""){$bodyPartCond_2="and b.body_part_id=$cbo_body_part";}

	                $store_cond = ($cbo_store_name!="") ? "and b.store_id='$cbo_store_name'":"";
					$floor_cond = ($txt_floor!="" && $txt_floor!=0) ? "and b.floor_id='$txt_floor'":"";
					$room_cond 	= ($txt_room!="" && $txt_room!=0) ? "and b.room='$txt_room'":"";
					$rack_cond 	= ($txt_rack!="" && $txt_rack!=0) ? "and b.rack='$txt_rack'":"";
					$shelf_cond = ($txt_shelf!="" && $txt_shelf!=0) ? "and b.self='$txt_shelf'":"";
					$bin_cond = ($txt_bin!="" && $txt_bin!=0) ? "and b.bin_box='$txt_bin'":"";
					$txt_fabric_ref_cond = ($hidden_fab_ref!="" && $hidden_fab_ref!=0) ? "and b.fabric_ref='$hidden_fab_ref'":"";

					/*if($variable_setting_production==1)
					{
						$cumu_iss_data_arr = sql_select("select d.po_breakdown_id, d.roll_id, d.qnty from inv_mrr_wise_issue_details b, order_wise_pro_details c, pro_roll_details d where b.issue_trans_id=c.trans_id and c.trans_id=d.dtls_id and c.status_active=1 and b.recv_trans_id='$hidden_receive_trans_id' and c.entry_form=202 and d.entry_form=202 group by d.po_breakdown_id, d.roll_id, d.qnty");
						foreach($cumu_iss_data_arr as $rowR)
						{
							$cumu_iss_arr[$rowR[csf('po_breakdown_id')]][$rowR[csf('roll_id')]]+=$rowR[csf('qnty')];
						}
					}
					else
					{*/

					$issue_rtn_recv_qnty=sql_select("select b.id, a.po_breakdown_id,a.quantity as qnty,y.job_no_mst ,d.style_ref_no,c.booking_no,c.job_no from inv_transaction b, product_details_master x,order_wise_pro_details a,wo_po_break_down y,wo_booking_dtls c,wo_po_details_master d,wo_pre_cost_fabric_cost_dtls e,lib_yarn_count_determina_mst f  where b.prod_id=x.id and x.id=a.prod_id and b.id=a.trans_id and a.po_breakdown_id=y.id and y.job_no_mst=d.job_no and d.job_no=c.job_no and d.job_no=e.job_no and c.pre_cost_fabric_cost_dtls_id=e.id  and e.lib_yarn_count_deter_id=f.id  and y.id=c.po_break_down_id and x.color='".$hidden_color_id."'  $bodyPartCond_2 and x.detarmination_id='".$fabric_desc_id."' and b.pi_wo_batch_no=$txt_batch_id $txt_width_cond $txt_weight_cond and b.status_active=1 and a.entry_form=209 and b.is_deleted=0 and b.status_active=1 and x.is_deleted=0 and x.status_active=1 and a.is_deleted=0 and a.status_active=1 and b.item_category=3 and b.transaction_type=4  and b.prod_id=$txt_prod_id $txt_fabric_ref_cond $txt_rd_no_cond $cbo_weight_type_cond $txt_cutable_width_cond  group by b.id, a.po_breakdown_id,a.quantity,y.job_no_mst,d.style_ref_no,c.booking_no,c.job_no");
					foreach($issue_rtn_recv_qnty as $row)
					{
						$cumu_issue_rtn_recv[$row[csf('order_id')]]+=$row[csf('quantity')];
						$cumu_issue_rtn_recv_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]['qnty']+=$row[csf('qnty')];
					}

					if($update_id!="")
					{
						$existing_rev_rtn_id_cond="and b.id<> $update_id";
					}
					/*$mrr_wise_prev_recv_rtn_sql=sql_select("select a.quantity as quantity,b.order_rate as rate,c.job_no,c.booking_no ,d.style_ref_no from inv_issue_master x,inv_transaction b,inv_mrr_wise_issue_details y, order_wise_pro_details a,wo_booking_dtls c,wo_po_details_master d,wo_pre_cost_fabric_cost_dtls e,lib_yarn_count_determina_mst f where  x.id=b.mst_id and b.id=y.issue_trans_id and y.issue_trans_id=a.trans_id and b.id=a.trans_id and a.po_breakdown_id=c.po_break_down_id and c.job_no=d.job_no and d.job_no=e.job_no and c.pre_cost_fabric_cost_dtls_id=e.id  and e.lib_yarn_count_deter_id=f.id and y.recv_trans_id='$hidden_receive_trans_id' and a.entry_form in (202) and b.transaction_type in (3) and a.trans_type in (3) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.item_category=3 and b.company_id=$cbo_company_id and b.prod_id=$txt_prod_id and a.prod_id=$txt_prod_id and b.pi_wo_batch_no=$txt_batch_id $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond $bodyPartCond_2  $existing_rev_rtn_id_cond $txt_width_cond $txt_weight_cond and c.fabric_color_id=$hidden_color_id and c.rate=$hidden_rate $txt_fabric_ref_cond $txt_rd_no_cond $cbo_weight_type_cond $txt_cutable_width_cond and e.lib_yarn_count_deter_id=$fabric_desc_id");
					foreach($mrr_wise_prev_recv_rtn_sql as $row)
					{
						$mrr_wise_cumu_recv_rtn_qty_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]][$row[csf('rate')]]['qnty']+=$row[csf('quantity')];
					}
					*/
					$prev_recv_rtn_global_sql=sql_select("select a.po_breakdown_id as order_id, b.transaction_type,a.quantity as quantity,c.job_no,c.booking_no ,d.style_ref_no from inv_issue_master x,inv_transaction b,inv_mrr_wise_issue_details y, order_wise_pro_details a,wo_booking_dtls c,wo_po_details_master d,wo_pre_cost_fabric_cost_dtls e,lib_yarn_count_determina_mst f where  x.id=b.mst_id and b.id=y.issue_trans_id and y.issue_trans_id=a.trans_id and b.id=a.trans_id and a.po_breakdown_id=c.po_break_down_id and c.job_no=d.job_no and d.job_no=e.job_no and c.pre_cost_fabric_cost_dtls_id=e.id  and e.lib_yarn_count_deter_id=f.id  and a.entry_form in (202) and b.transaction_type in (3) and a.trans_type in (3) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.item_category=3 and b.company_id=$cbo_company_id and b.prod_id=$txt_prod_id and a.prod_id=$txt_prod_id and b.pi_wo_batch_no=$txt_batch_id $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond $bodyPartCond_2  $existing_rev_rtn_id_cond $txt_width_cond $txt_weight_cond and c.fabric_color_id=$hidden_color_id $txt_fabric_ref_cond $txt_rd_no_cond $cbo_weight_type_cond $txt_cutable_width_cond and e.lib_yarn_count_deter_id=$fabric_desc_id");

					foreach($prev_recv_rtn_global_sql as $row)
					{
						$cumu_recv_rtn_global_qty_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]['qnty']+=$row[csf('quantity')];
					}
					$prev_recv_rtn_sql=sql_select("select a.po_breakdown_id as order_id, b.transaction_type,a.quantity as quantity,c.job_no,c.booking_no ,d.style_ref_no from inv_issue_master x,inv_transaction b,inv_mrr_wise_issue_details y, order_wise_pro_details a,wo_booking_dtls c,wo_po_details_master d,wo_pre_cost_fabric_cost_dtls e,lib_yarn_count_determina_mst f where  x.id=b.mst_id and b.id=y.issue_trans_id and y.issue_trans_id=a.trans_id and b.id=a.trans_id and a.po_breakdown_id=c.po_break_down_id and c.job_no=d.job_no and d.job_no=e.job_no and c.pre_cost_fabric_cost_dtls_id=e.id  and e.lib_yarn_count_deter_id=f.id and y.recv_trans_id='$hidden_receive_trans_id' and a.entry_form in (202) and b.transaction_type in (3) and a.trans_type in (3) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.item_category=3 and b.company_id=$cbo_company_id and b.prod_id=$txt_prod_id and a.prod_id=$txt_prod_id and b.pi_wo_batch_no=$txt_batch_id $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond $bodyPartCond_2  $existing_rev_rtn_id_cond $txt_width_cond $txt_weight_cond and c.fabric_color_id=$hidden_color_id  $txt_fabric_ref_cond $txt_rd_no_cond $cbo_weight_type_cond $txt_cutable_width_cond and e.lib_yarn_count_deter_id=$fabric_desc_id");
					foreach($prev_recv_rtn_sql as $row)
					{
						$cumu_issue_qty[$row[csf('order_id')]]+=$row[csf('quantity')];
						$cumu_recv_rtn_qty_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]['qnty']+=$row[csf('quantity')];
					}
					$prev_issue_sql=sql_select("select a.po_breakdown_id as order_id, b.transaction_type,a.quantity as quantity,c.job_no,c.booking_no ,d.style_ref_no from inv_issue_master x,inv_wvn_finish_fab_iss_dtls y,inv_transaction b, order_wise_pro_details a,wo_booking_dtls c,wo_po_details_master d,wo_pre_cost_fabric_cost_dtls e,lib_yarn_count_determina_mst f where x.id=y.mst_id and y.trans_id=b.id and b.id=a.trans_id and a.po_breakdown_id=c.po_break_down_id and c.job_no=d.job_no and d.job_no=e.job_no and c.pre_cost_fabric_cost_dtls_id=e.id  and e.lib_yarn_count_deter_id=f.id and a.entry_form in (19) and b.transaction_type in (2) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.item_category=3 and b.company_id=$cbo_company_id and b.prod_id=$txt_prod_id and a.prod_id=$txt_prod_id and b.batch_id=$txt_batch_id $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond $bodyPartCond_2  $txt_width_cond $txt_weight_cond and c.fabric_color_id=$hidden_color_id  $txt_fabric_ref_cond $txt_rd_no_cond $cbo_weight_type_cond $txt_cutable_width_cond and e.lib_yarn_count_deter_id=$fabric_desc_id group by a.po_breakdown_id , b.transaction_type,a.quantity,c.job_no,c.booking_no ,d.style_ref_no ");
					foreach($prev_issue_sql as $row)
					{
						$cumu_issue_qty[$row[csf('order_id')]]+=$row[csf('quantity')];
						$cumu_issue_qty_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]['qnty']+=$row[csf('quantity')];
					}

					$prev_transIn_sql=sql_select("select a.po_breakdown_id as order_id, b.transaction_type,a.quantity as quantity,c.job_no,c.booking_no ,d.style_ref_no from inv_transaction b, order_wise_pro_details a,wo_booking_dtls c,wo_po_details_master d,wo_pre_cost_fabric_cost_dtls e,lib_yarn_count_determina_mst f where b.id=a.trans_id and a.po_breakdown_id=c.po_break_down_id and c.job_no=d.job_no and d.job_no=e.job_no and c.pre_cost_fabric_cost_dtls_id=e.id  and e.lib_yarn_count_deter_id=f.id and a.entry_form in (258) and b.transaction_type in (6) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.item_category=3 and b.company_id=$cbo_company_id and b.prod_id=$txt_prod_id and a.prod_id=$txt_prod_id and b.pi_wo_batch_no=$txt_batch_id $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond $bodyPartCond_2  $txt_width_cond $txt_weight_cond and c.fabric_color_id=$hidden_color_id  $txt_fabric_ref_cond $txt_rd_no_cond $cbo_weight_type_cond $txt_cutable_width_cond and e.lib_yarn_count_deter_id=$fabric_desc_id");
					foreach($prev_transIn_sql as $row)
					{
						$cumu_transIn_qty[$row[csf('order_id')]]+=$row[csf('quantity')];
						$cumu_transIn_qty_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]['qnty']+=$row[csf('quantity')];
					}
						
					$prev_transOut_sql=sql_select("select a.po_breakdown_id as order_id, b.transaction_type,a.quantity as quantity,c.job_no,c.booking_no ,d.style_ref_no from inv_transaction b, order_wise_pro_details a,wo_booking_dtls c,wo_po_details_master d,wo_pre_cost_fabric_cost_dtls e,lib_yarn_count_determina_mst f where b.id=a.trans_id and a.po_breakdown_id=c.po_break_down_id and c.job_no=d.job_no and d.job_no=e.job_no and c.pre_cost_fabric_cost_dtls_id=e.id  and e.lib_yarn_count_deter_id=f.id and a.entry_form in (258) and b.transaction_type in (6) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.item_category=3 and b.company_id=$cbo_company_id and b.prod_id=$txt_prod_id and a.prod_id=$txt_prod_id and b.pi_wo_batch_no=$txt_batch_id $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond $bodyPartCond_2  $txt_width_cond $txt_weight_cond and c.fabric_color_id=$hidden_color_id  $txt_fabric_ref_cond $txt_rd_no_cond $cbo_weight_type_cond $txt_cutable_width_cond and e.lib_yarn_count_deter_id=$fabric_desc_id");
					foreach($prev_transOut_sql as $row)
					{
						$cumu_transOut_qty[$row[csf('order_id')]]+=$row[csf('quantity')];
						$cumu_transOut_qty_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]['qnty']+=$row[csf('quantity')];
					}

					//}
					//$po_no_arr = return_library_array("select id,po_number from wo_po_break_down","id","po_number");
					/*if($variable_setting_production==1)
					{
						$sql="select c.id as roll_id, c.po_breakdown_id, c.roll_no, c.qnty as receive_qnty from inv_transaction b, order_wise_pro_details a, pro_roll_details c where b.id=a.trans_id and a.dtls_id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_id' and b.mst_id='$txt_received_id' and b.id='$hidden_receive_trans_id' and b.prod_id='$txt_prod_id' and a.entry_form in(17) and c.entry_form in(17) and a.trans_type in(1,4) and b.transaction_type in(1,4) group by c.id, c.po_breakdown_id, c.roll_no, c.qnty";
					}
					else
					{*/
						//$sql="select a.po_breakdown_id, sum(a.quantity) as receive_qnty from  order_wise_pro_details a, inv_transaction b where a.trans_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_id' and b.mst_id='$txt_received_id' and b.prod_id='$txt_prod_id' and a.entry_form in(17) and b.id='$hidden_receive_trans_id' and b.transaction_type in(1,4) and a.trans_type in(1,4) group by a.po_breakdown_id";

					
						/*+cbo_company_id+'&txt_received_id='+txt_received_id+'&txt_prod_id='+txt_prod_id+'&hidden_receive_trans_id='+hidden_receive_trans_id+'&txt_break_qnty='+txt_break_qnty+'&txt_break_roll='+txt_break_roll+'&txt_batch_lot='+txt_batch_lot +'&txt_batch_id='+ txt_batch_id+'&hidden_color_id='+hidden_color_id+'&cbo_body_part='+cbo_body_part+'&cbo_store_name='+cbo_store_name+'&txt_width='+txt_width+'&txt_weight='+txt_weight+'&cbo_weight_type='+cbo_weight_type+'&txt_cutable_width='+txt_cutable_width+'&txt_floor='+txt_floor+'&txt_room='+txt_room+'&txt_rack='+txt_rack+'&txt_shelf='+txt_shelf+'&txt_bin='+txt_bin+'&cbouom='+cbouom+'&action='+actionName+'&fabric_desc_id='+fabric_desc_id+'&update_id='+update_id;*/

					 /*union all 
						 select  d.id,e.job_no, sum(c.quantity) as quantity,sum(e.total_set_qnty*d.po_quantity) as po_qnty_in_pcs, d.po_number,e.style_ref_no,a.booking_no,b.order_rate as rate   
						 from pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e 
						 where a.id=b.pi_wo_batch_no and b.id = c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and b.item_category=3 and b.transaction_type=5 and c.entry_form in(258) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
						 and c.status_active=1 and c.is_deleted=0 and b.pi_wo_batch_no>0 and b.company_id=$cbo_company_id $bodyPartCond_2 and b.store_id='$cbo_store_name' and b.prod_id=$txt_prod_id and a.id='$txt_batch_id' $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond  
						 group by d.id,b.pi_wo_batch_no,e.job_no, d.po_number,e.style_ref_no,a.booking_no,b.order_rate  
						 ) x
						 group by x.id,x.job_no, x.po_number,x.po_qnty_in_pcs,x.style_ref_no,x.booking_no,x.rate*/


					$mrr_wise_recv=sql_select("select  e.job_no, sum(c.quantity) as quantity,e.style_ref_no,a.booking_no  
						 from pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e 
						 where a.id=b.batch_id and b.id = c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and b.item_category=3 and b.transaction_type in(1) and c.trans_type in(1) and c.entry_form in(17) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
						 and c.status_active=1 and c.is_deleted=0 and b.batch_id>0 and b.company_id=$cbo_company_id and b.store_id='$cbo_store_name' and b.batch_lot='$txt_batch_lot' and b.prod_id=$txt_prod_id and a.id='$txt_batch_id' $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond $bodyPartCond_2 and b.mst_id=$txt_received_id 
						 group by e.job_no, d.po_number,e.style_ref_no,a.booking_no"); 
						foreach($mrr_wise_recv as $row)
						{
						 	$mrrWiseRecvQnty[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]["recv_qnty"]+=$row[csf('quantity')];
						}
					

					$sql = "select  d.id,e.job_no, sum(c.quantity) as quantity,sum(e.total_set_qnty*d.po_quantity) as po_qnty_in_pcs, d.po_number,e.style_ref_no,a.booking_no   
						 from pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e 
						 where a.id=b.batch_id and b.id = c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and b.item_category=3 and b.transaction_type in(1) and c.trans_type in(1) and c.entry_form in(17) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
						 and c.status_active=1 and c.is_deleted=0 and b.batch_id>0 and b.company_id=$cbo_company_id and b.store_id='$cbo_store_name' and b.batch_lot='$txt_batch_lot' and b.prod_id=$txt_prod_id and a.id='$txt_batch_id' $floor_cond $room_cond $rack_cond $shelf_cond $bin_cond  $store_cond $bodyPartCond_2 
						 group by d.id,e.job_no, d.po_number,e.style_ref_no,a.booking_no 
						 "; 
					$nameArray=sql_select($sql);
					$poIDS="";$bookingNos="";$rev_req_qnty_total=0;
					foreach($nameArray as $row)
					{
						$rev_req_qnty_total+=$row[csf('quantity')];
						$dataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]["po_id"].=$row[csf('id')].",";
						//if ($chk_pi_id[$row[csf('pi_id')]]=="") 
						//{
							$dataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]["recv_qnty"]+=$row[csf('quantity')];
							$dataArr2[$row[csf('booking_no')]]+=$row[csf('quantity')];
							$prev_recv_qnty_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]['qnty']+=$row[csf('quantity')];
							$prev_recv_qnty_arr2[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]][$row[csf('id')]]['qnty']+=$row[csf('quantity')];
						//}
						$poIDS.=$row[csf('id')].",";
						$bookingNos.="'".$row[csf('booking_no')]."',";
					}
					$poIDS=implode(",",array_unique(explode(",", $poIDS))); 
					$bookingNos=implode(",",array_unique(explode(",", $bookingNos))); 
					$poIDS=chop($poIDS,",");
					$bookingNos=chop($bookingNos,",");
				//}

					$i=1; $tot_po_qnty=0; $issue_qnty_array=array();
					$explSaveData = explode(",",$save_data);
					for($z=0;$z<count($explSaveData);$z++)
					{
						$po_wise_data = explode("_",$explSaveData[$z]);
						$order_id=$po_wise_data[0];
						$issueQnty=$po_wise_data[1];

						$issue_qnty_array[$order_id]=$issueQnty;
					}


					foreach($dataArr as $jobNo => $job_data)
					{
						foreach($job_data as $bookingNo => $booking_data)
						{
							foreach($booking_data as $styleRef => $row)
							{
								//foreach($style_data as $rate => $row)
								//{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

									$hideQnty=$hide_qty_array[$row[csf('id')]];
									//echo $cumu_recv_rtn_qty[$row[csf('id')]]."+".$cumu_trans_out_qty[$row[csf('id')]]."-".$cumu_issue_ret_qty[$row[csf('id')]]."<br/>";


									$mrrWiseRecvQty=$mrrWiseRecvQnty[$jobNo][$bookingNo][$styleRef]["recv_qnty"];
									$prevIssueQnty=$cumu_issue_qty_arr[$jobNo][$bookingNo][$styleRef]["qnty"];
									$prevIssueRtnQnty=$cumu_issue_rtn_recv_arr[$jobNo][$bookingNo][$styleRef]["qnty"];


									$transIn=$cumu_transIn_qty_arr[$jobNo][$bookingNo][$styleRef]["qnty"];
									$transOut=$cumu_transOut_qty_arr[$jobNo][$bookingNo][$styleRef]["qnty"];
									$totallRecQnty=($prev_recv_qnty_arr[$jobNo][$bookingNo][$styleRef]['qnty']+$prevIssueRtnQnty+$transIn);

									$prevRecRtnQnty=$cumu_recv_rtn_qty_arr[$jobNo][$bookingNo][$styleRef]["qnty"];
									$prevRecRtnQntyGlobal=$cumu_recv_rtn_global_qty_arr[$jobNo][$bookingNo][$styleRef]["qnty"];
									//echo $prevIssueRtnQnty;
									//echo "(".$prev_recv_qnty_arr[$jobNo][$bookingNo][$styleRef][$rate]['qnty']."+".$prevIssueRtnQnty."+".$transIn.")-(".$prevIssueQnty."+".$transOut."+".$prevRecRtnQnty.")";
									if($update_id!="")
									{
										$availableRecQnty=($prev_recv_qnty_arr[$jobNo][$bookingNo][$styleRef]['qnty']+$prevIssueRtnQnty+$transIn)-($prevIssueQnty+$transOut+$prevRecRtnQntyGlobal);

										$title="(Global prev recv qnty=".$prev_recv_qnty_arr[$jobNo][$bookingNo][$styleRef]['qnty']." + Global Prev issue return qnty=$prevIssueRtnQnty"." + Global transfer IN qnty=$transIn".")-("." Global prev issue qnty=$prevIssueQnty + Global transfer Out qnty=$transOut + Global prev recv return=$prevRecRtnQntyGlobal )";

										$mrrWiseAvailableRecQnty=$mrrWiseRecvQty-$prevRecRtnQnty;

										$title_balance="MRR wise recv qnty=$mrrWiseRecvQty - MRR wise prev return qnty=$prevRecRtnQnty";
									}
									else
									{
										$availableRecQnty=($prev_recv_qnty_arr[$jobNo][$bookingNo][$styleRef]['qnty']+$prevIssueRtnQnty+$transIn)-($prevIssueQnty+$transOut+$prevRecRtnQntyGlobal);
										$title="(Global prev recv qnty=".$prev_recv_qnty_arr[$jobNo][$bookingNo][$styleRef]['qnty']." + Global Prev issue return qnty=$prevIssueRtnQnty"." + Global transfer IN qnty=$transIn".")-("." Global prev issue qnty=$prevIssueQnty + Global transfer Out qnty=$transOut + Global prev recv return=$prevRecRtnQntyGlobal )";

										$mrrWiseAvailableRecQnty=$mrrWiseRecvQty-$prevRecRtnQnty;
										$title_balance="MRR wise recv qnty=$mrrWiseRecvQty - MRR wise prev return qnty=$prevRecRtnQnty";
									}

									$po_arr_uniq=array_unique(explode(",", chop($row['po_id'],",") ));
									$hdn_po_recv_ratio_ref="";$iss_qty="";
									foreach ($po_arr_uniq as $poID) {
										$orderRecvRequiredQnty=$prev_recv_qnty_arr2[$jobNo][$bookingNo][$styleRef][$poID]['qnty'];
										$hdn_po_recv_ratio_ref.=$poID."=".$orderRecvRequiredQnty.",";

										//$po_ratio=$orderRecvRequiredQnty*1/$rev_req_qnty_total*1;
										$iss_qty+=$issue_qnty_array[$poID];
									}
									$hdn_po_recv_ratio_ref=chop($hdn_po_recv_ratio_ref,",");
									$po_ids=implode(",", $po_arr_uniq);
									//$cumul_balance=($availableRecQnty-$prevRecRtnQnty);
									//$cumul_balance=($availableRecQnty);
									$cumul_balance=($mrrWiseAvailableRecQnty);
									?>
										<tr>
											<td align="center"><p><? echo $jobNo; ?></p></td>
					                		<td align="center"><p><? echo $styleRef; ?></p></td>
					                		<td align="center"><p><? echo $bookingNo; ?>
					                			<input type="hidden" id="poId_<? echo $i; ?>" name="poId[]" class="text_boxes" style="width:140px" value="<? echo $row[csf("po_breakdown_id")];  ?>"  readonly disabled >

				                                <input type="hidden" name="txtHdnPoRcvRatio[]" id="txtHdnPoRcvRatio_<? echo $i; ?>" value="<? echo $hdn_po_recv_ratio_ref; ?>">

				                                <input type="hidden" name="hidden_cummulative_rcv_qnty[]" id="hidden_cummulative_rcv_qnty_<? echo $i; ?>" value="<? echo $availableRecQnty; ?>">

				                                <input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $po_ids; ?>">
				                                <input type="hidden" name="txtPoName[]" id="txtPoName_<? echo $i; ?>" value="<? echo $row[csf('po_number')]; ?>">
				                                <input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $row[csf('po_qnty_in_pcs')]; ?>">
					                			</p>
					                		</td>
					                    	<td align="center" title="MRR wise recv qnty=<? echo $mrrWiseRecvQty; ?>"> <input type="text" id="totalRecevqnty_<? echo $i; ?>" name="totalRecevqnty[]" class="text_boxes_numeric" style="width:100px" value="<? echo number_format($mrrWiseRecvQty,2,".","");  ?>" readonly disabled ></td>
					                        <td align="center" title="Product ref. wise available qnty: <? echo $title; ?>"> <input type="text" id="recevqnty_<? echo $i; ?>" name="recevqnty[]" class="text_boxes_numeric" style="width:100px" value="<? echo number_format($availableRecQnty,2,".","");  ?>" readonly disabled ></td>
					                        <td align="center" title="MRR wise previous recv return=<? echo $prevRecRtnQnty; ?>">
					                       		<input type="text" id="cumulativeIssue_<? echo $i; ?>" name="cumulativeIssue[]" value="<? echo number_format($prevRecRtnQnty,2,".",""); ?>" class="text_boxes_numeric" style="width:100px" readonly disabled >
					                        </td>
				                            <td align="center" title="<? echo $title_balance; ?>">
				                                <input type="text" id="issueqnty_<? echo $i; ?>" name="issueqnty[]" onKeyUp="fn_calculate(<? echo $i; ?>);" class="text_boxes_numeric" value="<? if($iss_qty>0){echo $iss_qty;} ?>" style="width:100px" placeholder="<? echo $cumul_balance; ?>" >
				                                <input type="hidden" id="hiddenissueqnty_<? echo $i; ?>" name="hiddenissueqnty[]" class="text_boxes_numeric" value="<? echo $iss_qty; ?>">
				                                <input type="hidden" name="hideQnty[]" id="hideQnty_<? echo $i; ?>" value="<? echo $hideQnty; ?>">
				                            </td>
										</tr>
									<?
			                     	$i++;
								//}
							}
						}
					}
				?>
                </tbody>
        </table>
        <table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0" border="0" rules="all" align="center">
            <tr>
                <td align="center"> 
	                <input type="button" id="btn_close" name="" value="Close" onClick="js_set_value();" style="width:150px;" class="formbutton" >
	                <input type="hidden" id="tot_qnty" name="tot_qnty" >
	                <input type="hidden" id="break_qnty" name="break_qnty" >
	                <input type="hidden" id="break_roll" name="break_roll" >
	                <input type="hidden" id="break_order_id" name="break_order_id" >
	                <input type="hidden" id="tot_roll" name="tot_roll" >

		            <input type="hidden" name="save_data" id="save_data" class="text_boxes" value="">
		            <input type="hidden" name="tot_issue_qnty" id="tot_issue_qnty" class="text_boxes" value="">
		            <input type="hidden" name="all_po_id" id="all_po_id" class="text_boxes" value="">
		            <input type="hidden" name="all_po_no" id="all_po_no" class="text_boxes" value="">
                </td>
            </tr>
        </table>
    </form>
    </div>
</body>    
	<?
}
if($action=="return_po_popup")
{
	echo load_html_head_contents("Receive Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$txt_received_id=str_replace("'","",$txt_received_id);
	$txt_prod_id=str_replace("'","",$txt_prod_id);
	$hidden_receive_trans_id=str_replace("'","",$hidden_receive_trans_id);
	$update_id=str_replace("'","",$update_id);
	
	$variable_setting_production=return_field_value("fabric_roll_level","variable_settings_production","company_name='$cbo_company_id'  and item_category_id=3 and variable_list=3 and status_active=1","fabric_roll_level");
	//echo $variable_setting_production.Fuad;//die;
	/*if($variable_setting_production==1)
	{
		$table_width=600;
		$txt_break_roll=explode("_",$txt_break_roll);
		foreach($txt_break_roll as $val)
		{
			$txt_break_roll_data=explode("**",$val);
			$po_id=$txt_break_roll_data[0];
			$roll_no=$txt_break_roll_data[1];
			$qty=$txt_break_roll_data[2];
			$roll_id=$txt_break_roll_data[3];
			
			$order_wise_qnty_arr[$po_id][$roll_id]=$qty;
		}
	}
	else
	{*/
		$table_width=500;
		$txt_break_qnty=explode("_",$txt_break_qnty);
		foreach($txt_break_qnty as $val)
		{
			$txt_break_qnty_data=explode("**",$val);
			$po_id=$txt_break_qnty_data[0];
			$qty=$txt_break_qnty_data[1];
			
			$order_wise_qnty_arr[$po_id]=$qty;
		}
	//}
	//print_r($order_wise_qnty_arr);
	/*if($update_id>0)
	{
		$order_sql=sql_select("select po_breakdown_id,quantity from order_wise_pro_details where trans_id=$update_id and trans_type=3 and entry_form=46 and status_active=1");
		foreach($order_sql as $row)
		{
			$order_wise_qnty_arr[$row[csf("po_breakdown_id")]]=$row[csf("quantity")];
		}
	}
	
	if($variable_setting_production==1)
	{
		$table_width=600;
	}
	else
	{
		$table_width=500;
	}*/
	?>
<script>
	
	
	function js_set_value()
	{
		var table_legth=$('#pop_table tbody tr').length;
		var break_qnty=break_roll=break_id="";
		var tot_qnty=0; var tot_roll='';
		for(var i=1; i<=table_legth; i++)
		{
			//if(i!=1) break_qnty+="_";
			tot_qnty +=($("#issueqnty_"+i).val()*1);
			if(break_qnty!="") break_qnty +="_";
			break_qnty+=($("#poId_"+i).val()*1)+'**'+($("#issueqnty_"+i).val()*1);
			if(break_roll!="") break_roll +="_";
			break_roll+=($("#poId_"+i).val()*1)+'**'+($("#roll_"+i).val()*1)+'**'+($("#issueqnty_"+i).val()*1)+'**'+($("#rollId_"+i).val()*1);
			if(break_id!="") break_id +=",";
			break_id+=($("#poId_"+i).val()*1);
			
			if($("#issueqnty_"+i).val()*1>0 && $("#rollId_"+i).val()*1>0)
			{
				tot_roll+=1;
			}
		}
		$("#tot_qnty").val(tot_qnty);
		$("#break_qnty").val(break_qnty);
		$("#break_roll").val(break_roll);
		$("#break_order_id").val(break_id);
		$("#tot_roll").val(tot_roll);
		parent.emailwindow.hide();
	}
	
	function fn_calculate(id)
	{
		var recv_qnty=($("#recevqnty_"+id).val()*1);
		var cumu_qnty=($("#cumulativeIssue_"+id).val()*1);
		var issue_qnty=($("#issueqnty_"+id).val()*1);
		var hiddenissue_qnty=($("#hiddenissueqnty_"+id).val()*1);
		if(((cumu_qnty*1)+(issue_qnty*1))>((recv_qnty*1)+(hiddenissue_qnty*1))) 
		{
			alert("Return Quantity Can not be Greater Than Receive Quantity.");
			$("#issueqnty_"+id).val(0);
		}
	}
	
	
</script>

</head>

<body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" id="pop_table">
                <thead>
                    <tr>                	 
                        <th width="140">Order No</th>
                        <th width="120">Receive Quantity</th>
                        <th width="120">Cumulative Issue</th>
                        <?
						/*if($variable_setting_production==1)
						{
							?>
							<th>Roll</th>
							<?
						}*/
						?>           
                        <th width="120">Return Quantity</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$cumu_iss_arr=array();
				/*if($variable_setting_production==1)
				{
					$cumu_iss_data_arr = sql_select("select d.po_breakdown_id, d.roll_id, d.qnty from inv_mrr_wise_issue_details b, order_wise_pro_details c, pro_roll_details d where b.issue_trans_id=c.trans_id and c.trans_id=d.dtls_id and c.status_active=1 and b.recv_trans_id='$hidden_receive_trans_id' and c.entry_form=202 and d.entry_form=202 group by d.po_breakdown_id, d.roll_id, d.qnty");
					foreach($cumu_iss_data_arr as $rowR)
					{
						$cumu_iss_arr[$rowR[csf('po_breakdown_id')]][$rowR[csf('roll_id')]]+=$rowR[csf('qnty')];
					}
				}
				else
				{*/
					$cumu_iss_arr = return_library_array("select c.po_breakdown_id, sum(c.quantity) as cumu_qnty from inv_mrr_wise_issue_details b, order_wise_pro_details c where b.issue_trans_id=c.trans_id and c.status_active=1 and b.recv_trans_id='$hidden_receive_trans_id' and c.entry_form=202 group by c.po_breakdown_id","po_breakdown_id","cumu_qnty");
				//}
				$po_no_arr = return_library_array("select id,po_number from wo_po_break_down","id","po_number");
				/*if($variable_setting_production==1)
				{
					$sql="select c.id as roll_id, c.po_breakdown_id, c.roll_no, c.qnty as receive_qnty from inv_transaction b, order_wise_pro_details a, pro_roll_details c where b.id=a.trans_id and a.dtls_id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_id' and b.mst_id='$txt_received_id' and b.id='$hidden_receive_trans_id' and b.prod_id='$txt_prod_id' and a.entry_form in(17) and c.entry_form in(17) and a.trans_type in(1,4) and b.transaction_type in(1,4) group by c.id, c.po_breakdown_id, c.roll_no, c.qnty";
				}
				else
				{*/
					$sql="select a.po_breakdown_id, sum(a.quantity) as receive_qnty from  order_wise_pro_details a, inv_transaction b where a.trans_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_id' and b.mst_id='$txt_received_id' and b.prod_id='$txt_prod_id' and a.entry_form in(17) and b.id='$hidden_receive_trans_id' and b.transaction_type in(1,4) and a.trans_type in(1,4) group by a.po_breakdown_id";
				//}
				//echo $sql;
				$sql_result=sql_select($sql);
				$i=1;
				foreach($sql_result as $row)
				{
					/*if($variable_setting_production==1)
					{
						$cumilitive_issue=$cumu_iss_arr[$row[csf('po_breakdown_id')]][$row[csf('roll_id')]];
					}
					else
					{*/
						$cumilitive_issue=$cumu_iss_arr[$row[csf('po_breakdown_id')]];
						//$cumilitive_issue=return_field_value("sum(c.quantity) as cumu_qnty","inv_transaction a, inv_mrr_wise_issue_details b, order_wise_pro_details c","a.id=b.issue_trans_id and a.id=c.trans_id and c.status_active=1 and b.recv_trans_id='$hidden_receive_trans_id' and c.po_breakdown_id='".$row[csf('po_breakdown_id')]."' and c.entry_form=46","cumu_qnty");
					//}
					
					
					?>
                	<tr>
                    	<td align="center"><input type="text" id="poNo_<? echo $i; ?>" name="poNo_<? echo $i; ?>" class="text_boxes" style="width:140px" value="<? echo $po_no_arr[$row[csf("po_breakdown_id")]];  ?>"  readonly disabled >
                        <input type="hidden" id="poId_<? echo $i; ?>" name="poId_<? echo $i; ?>" class="text_boxes" style="width:140px" value="<? echo $row[csf("po_breakdown_id")];  ?>"  readonly disabled >
                        </td>
                        <td align="center"> <input type="text" id="recevqnty_<? echo $i; ?>" name="recevqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:110px" value="<? echo number_format($row[csf("receive_qnty")],2,".","");  ?>" readonly disabled ></td>
                        <td align="center">
                        <input type="text" id="cumulativeIssue_<? echo $i; ?>" name="cumulativeIssue_<? echo $i; ?>" value="<? echo number_format($cumilitive_issue,2,".",""); ?>" class="text_boxes_numeric" style="width:110px" readonly disabled >
                        </td>
                       <?php /*?> <?
						if($variable_setting_production==1)
						{
							?>
							<td align="center">
                                <input type="text" id="roll_<? echo $i; ?>" name="roll_<? echo $i; ?>" value="<? echo $row[csf("roll_no")]; ?>" class="text_boxes_numeric" style="width:80px" readonly disabled >
                                <input type="hidden" id="rollId_<? echo $i; ?>" name="rollId_<? echo $i; ?>" value="<? echo $row[csf("roll_id")]; ?>">
                            </td>
                            <td align="center">
                                <input type="text" id="issueqnty_<? echo $i; ?>" name="issueqnty_<? echo $i; ?>" onKeyUp="fn_calculate(<? echo $i; ?>);" class="text_boxes_numeric" value="<? echo $order_wise_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("roll_id")]]; ?>" style="width:110px" >
                                <input type="hidden" id="hiddenissueqnty_<? echo $i; ?>" name="hiddenissueqnty_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $order_wise_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("roll_id")]]; ?>">
                            </td>
						<?
						}
						else
						{
						?><?php */?>
							<td align="center" style="display:none;">
                            	<input type="text" id="roll_<? echo $i; ?>" name="roll_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" >
                                <input type="hidden" id="rollId_<? echo $i; ?>" name="rollId_<? echo $i; ?>" value="">
                            </td>
                            <td align="center">
                                <input type="text" id="issueqnty_<? echo $i; ?>" name="issueqnty_<? echo $i; ?>" onKeyUp="fn_calculate(<? echo $i; ?>);" class="text_boxes_numeric" value="<? echo $order_wise_qnty_arr[$row[csf("po_breakdown_id")]]; ?>" style="width:110px" >
                                <input type="hidden" id="hiddenissueqnty_<? echo $i; ?>" name="hiddenissueqnty_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $order_wise_qnty_arr[$row[csf("po_breakdown_id")]]; ?>">
                            </td>
						<?
						//}
						?> 
                    </tr>
                    <?
					$i++;
				}
				?>
                </tbody>
        </table>
        <table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0" border="0" rules="all" align="center">
            <tr>
                <td align="center"> 
                <input type="button" id="btn_close" name="" value="Close" onClick="js_set_value();" style="width:150px;" class="formbutton" >
                <input type="hidden" id="tot_qnty" name="tot_qnty" >
                <input type="hidden" id="break_qnty" name="break_qnty" >
                <input type="hidden" id="break_roll" name="break_roll" >
                <input type="hidden" id="break_order_id" name="break_order_id" >
                <input type="hidden" id="tot_roll" name="tot_roll" >
                </td>
            </tr>
        </table>
    </form>
    </div>
</body>    
	<?
}



//data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
    $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id = $txt_prod_id and store_id=$cbo_store_name and status_active=1 and is_deleted=0 and transaction_type in (1,4,5)", "max_date"); 
    if($max_recv_date !="")
    {     
		$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
		$return_date = date("Y-m-d", strtotime(str_replace("'","",$txt_return_date)));
		if ($return_date < $max_recv_date) 
	    {
	        echo "20**Return Date Can not Be Less Than Last Receive Date Of This Lot";
	       	die;
		}
	}
	$variable_setting_production=return_field_value("fabric_roll_level","variable_settings_production","company_name=$cbo_company_id  and item_category_id=3 and variable_list=3 and status_active=1","fabric_roll_level");
	
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
 		
		//---------------Check Duplicate product in Same return number ------------------------//
		/*$duplicate = is_duplicate_field("b.id","inv_issue_master a, inv_transaction b","a.id=b.mst_id and a.id=$issue_mst_id and b.prod_id=$txt_prod_id and b.transaction_type=3"); 
		if($duplicate==1) 
		{
			echo "20**Duplicate Product is Not Allow in Same Return Number.";
			check_table_status( $_SESSION['menu_id'], 0 );
			die;
		}*/
		//------------------------------Check Duplicate END---------------------------------------//
		$txt_return_qnty=str_replace("'","",$txt_return_qnty);
		$txt_global_stock=str_replace("'","",$txt_global_stock);
		$txt_batch_no=str_replace("'","",$txt_batch_no);
		if($txt_return_qnty>$txt_global_stock)
		{
			echo "30**Return Quantity Not Over Global Stock.";
			disconnect($con);die;
		}
		
		
 		if(str_replace("'","",$issue_mst_id)!="")
		{
			$new_return_number[0] = str_replace("'","",$txt_return_no);
			$id=str_replace("'","",$issue_mst_id);
			//issue master table UPDATE here START----------------------//		
 			$field_array_mst="company_id*issue_date*received_id*received_mrr_no*booking_id*updated_by*update_date";
			$data_array_mst=$cbo_company_id."*".$txt_return_date."*".$txt_received_id."*".$txt_mrr_no."*".$pi_id."*'".$user_id."'*'".$pc_date_time."'";
			//echo $field_array."<br>".$data_array;die;
		}
		else  	
		{	 
			//issue master table entry here START---------------------------------------//		
			//$id=return_next_id("id", "inv_issue_master", 1);
			
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
					
			//$new_return_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'FRR', date("Y",time()), 5, "select issue_number_prefix,issue_number_prefix_num from inv_issue_master where company_id=$cbo_company_id and entry_form=202 and $year_cond=".date('Y',time())." order by id DESC ", "issue_number_prefix", "issue_number_prefix_num" ));
			
			$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
			$new_return_number = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,str_replace("'","",$cbo_company_id),'WFRR',202,date("Y",time())));
			
 			$field_array_mst="id, issue_number_prefix, issue_number_prefix_num, issue_number, entry_form, item_category, company_id, issue_date, received_id, received_mrr_no, booking_id,supplier_id, inserted_by, insert_date"; 
			$data_array_mst="(".$id.",'".$new_return_number[1]."','".$new_return_number[2]."','".$new_return_number[0]."',202,3,".$cbo_company_id.",".$txt_return_date.",".$txt_received_id.",".$txt_mrr_no.",".$pi_id.",".$cbo_return_to.",'".$user_id."','".$pc_date_time."')";
			//echo "20**".$field_array."<br>".$data_array;die;
		}
		$hidden_rate_avg=str_replace("'","",$hidden_rate);
		$txt_return_qnty = str_replace("'","",$txt_return_qnty);

		//adjust product master table START-------------------------------------//
		$sql = sql_select("select product_name_details,last_purchased_qnty,current_stock,stock_value,avg_rate_per_unit,color from product_details_master where id=$txt_prod_id and item_category_id=3");
		$presentStock=$available_qnty=0;$color_id=0;$stockValue=0;$avgRate=0;
		$product_name_details="";
		$consAmount=$hidden_rate_avg*$txt_return_qnty;
		$field_array_prod="last_issued_qnty*current_stock*available_qnty*avg_rate_per_unit*stock_value*updated_by*update_date";

		foreach($sql as $result)
		{
			$presentStock			=$result[csf("current_stock")];
			$product_name_details 	=$result[csf("product_name_details")];
			$available_qnty			=$result[csf("available_qnty")];
			$color_id				=$result[csf("color")];	
			$stockValue				=$result[csf("stock_value")];	
			$avgRate				=$result[csf("avg_rate_per_unit")];	
		}
		$nowStock 		= $presentStock-$txt_return_qnty;
		$available_qnty = $available_qnty-$txt_return_qnty;
		//$availableAmount = $nowStock*$avgRate;
		$availableAmount = $stockValue-$consAmount;
		$avgRateNow=$availableAmount/$nowStock;
		if($nowStock<=0)
		{
			$available_qnty=0;
			$avgRateNow=0;
			$availableAmount=0;
		}
		$data_array_prod=$txt_return_qnty."*".$nowStock."*".$available_qnty."*".$avgRateNow."*".$availableAmount."*'".$user_id."'*'".$pc_date_time."'";
		
		//transaction table insert here START--------------------------------//cbo_uom 
		//$transactionID = return_next_id("id", "inv_transaction", 1);
		//$orderAmnt=str_replace("'","",$hidden_rate)*$txt_return_qnty;
		
		$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con); 				
		$field_array_trans = "id,mst_id,company_id,prod_id,batch_id_from_fissuertn,pi_wo_batch_no,batch_id,item_category,transaction_type,transaction_date,store_id,body_part_id,floor_id,room,rack,self,bin_box,order_uom,order_qnty,cons_uom,cons_quantity,cons_rate,cons_amount,no_of_roll,remarks,batch_lot,weight_type,cutable_width,fabric_ref,rd_no,width_editable,weight_editable,inserted_by,insert_date";
 		$data_array_trans = "(".$transactionID.",".$id.",".$cbo_company_id.",".$txt_prod_id.",".$hidden_batch_id.",".$hidden_batch_id.",".$hidden_batch_id.",3,3,".$txt_return_date.",".$cbo_store_name.",".$cbo_body_part.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_uom.",".$txt_return_qnty.",".$cbo_uom.",".$txt_return_qnty.",".$hidden_rate.",".$consAmount.",".$txt_roll.",".$txt_remarks.",'".$txt_batch_no."',".$cbo_weight_type.",".$txt_cutable_width.",".$hidden_fab_ref.",".$hidden_rd_no.",".$txt_width.",".$txt_weight.",'".$user_id."','".$pc_date_time."')"; 

 		//echo $field_array."<br>".$data_array;die;
		//$transID = sql_insert("inv_transaction",$field_array_trans,$data_array_trans,1);
		//transaction table insert here END ---------------------------------//
		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die; 



		//order_wise_pro_detail table insert here
		
		$txt_break_qnty=str_replace("'","",$txt_break_qnty);
		$txt_break_roll=str_replace("'","",$txt_break_roll);
		$txt_order_id_all=str_replace("'","",$txt_order_id_all);
		$ordr_wise_rtn_qnty_arr=explode("_",$txt_break_qnty);
		$ordr_wise_rtn_roll_arr=explode("_",$txt_break_roll);
		$ordr_id_arr=explode(",",$txt_order_id_all);
		//$proportion_id = return_next_id("id", "order_wise_pro_details", 1);
		
		//$roll_id = return_next_id("id", "pro_roll_details", 1);
		
		$field_array_proportion="id,trans_id,trans_type,entry_form,po_breakdown_id,prod_id,quantity,color_id,inserted_by,insert_date";
	
		$data_array_proportion=$data_array_roll="";
		$po_popup_patern_variable=str_replace("'","",$po_popup_patern_variable);
		if($po_popup_patern_variable==1)
		{
			if(!empty($txt_break_qnty))
			{
				/*if($variable_setting_production==1)
				{
					$order_array=array();
					$field_array_roll="id,mst_id,dtls_id,po_breakdown_id,entry_form,roll_no,roll_id,qnty,inserted_by,insert_date";
					foreach($ordr_wise_rtn_roll_arr as $val)
					{
						$order_roll_arr=explode("**",$val);
						$po_id=$order_roll_arr[0];
						$roll_no=$order_roll_arr[1];
						$qty=$order_roll_arr[2];
						$rollId=$order_roll_arr[3];
						
						if($qty>0)
						{
							$roll_id = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
							if($data_array_roll!="") $data_array_roll.=", ";
							$data_array_roll.="(".$roll_id.",".$id.",".$transactionID.",".$po_id.",202,'".$roll_no."','".$rollId."',".$qty.",'".$user_id."','".$pc_date_time."')";
							//$roll_id++;
							
							$order_array[$po_id]+=$qty;
						}
					}
					
					foreach($order_array as $po_id=>$po_qty)
					{
						$proportion_id = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
						if($data_array_proportion!="") $data_array_proportion.=", ";
						$data_array_proportion.="(".$proportion_id.",".$transactionID.",3,202,".$po_id.",".$txt_prod_id.",".$po_qty.",".$color_id.",'".$user_id."','".$pc_date_time."')";
						//$proportion_id++;
					}
				}
				else
				{*/
					foreach($ordr_wise_rtn_qnty_arr as $val)
					{
						$order_qnty_arr=explode("**",$val);
						if($order_qnty_arr[1]>0)
						{
							$proportion_id = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
							if($data_array_proportion!="") $data_array_proportion.=", ";
							$data_array_proportion.="(".$proportion_id.",".$transactionID.",3,202,".$order_qnty_arr[0].",".$txt_prod_id.",".$order_qnty_arr[1].",".$color_id.",'".$user_id."','".$pc_date_time."')";
							//$proportion_id++;
						}
					}
				//}
			}
		}
		else
		{
			$save_datas=explode(",",str_replace("'","",$save_data));
			for($i=0;$i<count($save_datas);$i++)
			{
				$proportion_id = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$order_dtls=explode("_",$save_datas[$i]);
				$order_id=$order_dtls[0];
				$order_qnty=$order_dtls[1];

				if($i==0) $add_comma=""; else $add_comma=",";

				$data_array_proportion.="$add_comma(".$proportion_id.",".$transactionID.",3,202,'".$order_id."',".$txt_prod_id.",'".$order_qnty."','".$color_id."','".$user_id."','".$pc_date_time."')";
			}
		

		}
		
		//echo "10**".$data_array_proportion;print_r($ordr_wise_rtn_roll_arr);die;
		
		/*for($i=0;$i<count($ordr_id_arr);$i++)
		{
			if($data_array_proportion!="") $data_array_proportion.=", ";
			$data_array_proportion.="(".$proportion_id.",".$transactionID.",3,45,".$ordr_id_arr[$i].",".$txt_prod_id.",".$ordr_wise_rtn_qnty_arr[$i].",'".$user_id."','".$pc_date_time."')";
			$proportion_id++;
			
			if($ordr_wise_rtn_roll_arr[$i]>0)
			{
				if($data_array_roll!="") $data_array_roll.=", ";
				$data_array_roll.="(".$roll_id.",".$id.",".$transactionID.",".$ordr_id_arr[$i].",45,".$ordr_wise_rtn_roll_arr[$i].",".$ordr_wise_rtn_qnty_arr[$i].",'".$user_id."','".$pc_date_time."')";
				$roll_id++;
			}
			
			
		}*/
		

		$hidden_receive_trans_id=str_replace("'","",$hidden_receive_trans_id);
		//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1); 
		$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con); 
		$field_array = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,order_save_string,inserted_by,insert_date";
		$data_array = "(".$mrrWiseIsID.",".$hidden_receive_trans_id.",".$transactionID.",202,".$txt_prod_id.",".$txt_return_qnty.",".$save_data.",'".$user_id."','".$pc_date_time."')";
		$update_array = "balance_qnty*updated_by*update_date";
		$sql_receive=sql_select("select id,balance_qnty from inv_transaction where id=$hidden_receive_trans_id and balance_qnty>0");
		$balance_qnty = $sql_receive[0][csf("balance_qnty")];
		$issueQntyBalance=$balance_qnty-$txt_return_qnty;
		$update_data="".$issueQntyBalance."*'".$user_id."'*'".$pc_date_time."'";
		
 		  
		$rID=$transID=$prodUpdate=$propoId=$rollId=$mrrWiseIssueID=$upTrID=true;
		if(str_replace("'","",$txt_return_no)!="")
		{
			$rID=sql_update("inv_issue_master",$field_array_mst,$data_array_mst,"id",$id,1);
		}
		else
		{
			$rID=sql_insert("inv_issue_master",$field_array_mst,$data_array_mst,1);
		}
		//echo "10**Insert into inv_transaction ($field_array_trans) values  $data_array_trans"; die;
		$transID = sql_insert("inv_transaction",$field_array_trans,$data_array_trans,1);
		$prodUpdate = sql_update("product_details_master",$field_array_prod,$data_array_prod,"id",$txt_prod_id,1);
		if($data_array_proportion!="")
		{
			//echo "10**Insert into order_wise_pro_details ($field_array_proportion) values  $data_array_proportion"; die;
			$propoId=sql_insert("order_wise_pro_details",$field_array_proportion,$data_array_proportion,1);
			/*if($variable_setting_production==1)
			{
				$rollId=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,1);
			}*/
		}
		//mrr wise issue data insert here----------------------------//
		if($data_array!="")
		{	
			//echo "10**Insert into inv_mrr_wise_issue_details ($field_array) values  $data_array"; die;
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array,$data_array,1);
		}
		
		//transaction table stock update here------------------------//
		if($balance_qnty>0)
		{
 			$upTrID=sql_update("inv_transaction",$update_array,$update_data,"id",$hidden_receive_trans_id,1);
		}
		
		//echo "10**$rID && $transID && $prodUpdate && $propoId && $rollId && $mrrWiseIssueID && $upTrID";die;
		
		if($db_type==0)
		{
			//if( $rID && $transID && $prodUpdate && $propoId && $rollId && $mrrWiseIssueID && $upTrID)
			if( $rID && $transID && $prodUpdate && $propoId && $mrrWiseIssueID && $upTrID)

			{
				mysql_query("COMMIT");  
				echo "0**".$new_return_number[0]."**".$id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_return_number[0];
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			//if( $rID && $transID && $prodUpdate && $propoId && $rollId && $mrrWiseIssueID && $upTrID)
			if( $rID && $transID && $prodUpdate && $propoId && $mrrWiseIssueID && $upTrID)

			{
				oci_commit($con);  
				echo "0**".$new_return_number[0]."**".$id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$new_return_number[0];
			}
		}
		disconnect($con);
		die;
				
	}	
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here 
		$issue_mst_id= str_replace("'","",$issue_mst_id);
		//check update id
		if( str_replace("'","",$update_id) == "" )
		{
			echo "10";disconnect($con);die; 
		}
		 
		$txt_return_qnty=str_replace("'","",$txt_return_qnty);
		$txt_global_stock=str_replace("'","",$txt_global_stock);
		$prev_return_qnty = str_replace("'","",$prev_return_qnty);
		if($txt_return_qnty>($txt_global_stock+$prev_return_qnty))
		{
			echo "30**Return Quantity Not Over Global Stock.";
			disconnect($con);die;
		}

		
		//****************************************** BEFORE ENTRY ADJUST START *****************************************//
		//product master table information
		//before stock update
		$sql = sql_select( "select a.id,a.current_stock,a.stock_value,a.avg_rate_per_unit, b.cons_quantity,b.cons_amount from product_details_master a, inv_transaction b where a.id=b.prod_id and a.id=$before_prod_id and b.id=$update_id and a.item_category_id=3 and b.item_category=3 and b.transaction_type=3" );
		$before_prod_id=$before_issue_qnty=$before_stock_qnty=$before_avgRate=$before_stock_amount=0;
		foreach($sql as $result)
		{
			$before_prod_id 	= $result[csf("id")];
 			$before_stock_qnty 	= $result[csf("current_stock")];
			//before quantity and stock value
			$before_issue_qnty	= $result[csf("cons_quantity")];

			$before_avgRate 	= $result[csf("avg_rate_per_unit")];
 			$before_stock_amount = $result[csf("stock_value")];
 			$before_cons_amount = $result[csf("cons_amount")];
		}
		$hidden_rate_avg=str_replace("'","",$hidden_rate);
		$cons_amount=$hidden_rate_avg*str_replace("'","",$txt_return_qnty);
		//current product ID
		$txt_prod_id = str_replace("'","",$txt_prod_id);
		$txt_return_qnty = str_replace("'","",$txt_return_qnty);
		$before_prod_id= str_replace("'","",$before_prod_id);
		//$curr_stock_qnty=return_field_value("current_stock","product_details_master","id=$txt_prod_id and item_category_id=3");

		$sql_prod = sql_select("select current_stock,stock_value,avg_rate_per_unit from product_details_master where id=$txt_prod_id and item_category_id=3");
		$curr_stock_qnty=$stockValue=$avgRate=0;
		foreach($sql_prod as $result)
		{
			$curr_stock_qnty =$result[csf("current_stock")];
			$stockValue		 =$result[csf("stock_value")];	
			$avgRate		 =$result[csf("avg_rate_per_unit")];
		}

		//max transaction id VALIDATION its VERY IMPORTANT for Rate, amount calculation// issue id:3510
		$sql_max_trans_id = sql_select("select max(a.id) as max_trans_id from inv_transaction a, product_details_master b where a.prod_id=$txt_prod_id and a.prod_id=b.id and b.item_category_id =3 and a.item_category=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id asc");
		$max_trans_id=$sql_max_trans_id[0]['MAX_TRANS_ID'];
		if (str_replace("'", "", trim($update_id))!=$max_trans_id) {
			echo "20**Found next transaction against this product ID";
			disconnect($con);
			die;
		}

		
		
		$receive_purpose=return_field_value("receive_purpose","inv_receive_master","id=$txt_received_id");
 		//echo $receive_purpose;die;
		//weighted and average rate START here------------------------//
		//product master table data UPDATE START----------------------//		

		$update_array_prod= "last_issued_qnty*current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";

		if($before_prod_id==$txt_prod_id)
		{
			$adj_stock_qnty = $curr_stock_qnty+$before_issue_qnty-$txt_return_qnty; // CurrentStock + Before Issue Qnty - Current Issue Qnty
			//$adj_amount 	= $adj_stock_qnty*$avgRate;
			$adj_amount=$stockValue+str_replace("'", '',$hidden_update_amount)-($txt_return_qnty*$hidden_rate_avg);
			$adj_avg_rate 	= $adj_amount/$adj_stock_qnty;
			if($adj_stock_qnty<0) //Aziz
			{
				echo "30**Stock cannot be less than zero.";disconnect($con);die;
			}
			if($adj_stock_qnty<=0)
			{
				$adj_avg_rate=0;
				$adj_amount=0;
			}
			
			$data_array_prod= $txt_return_qnty."*".$adj_stock_qnty."*".$adj_avg_rate."*".$adj_amount."*'".$user_id."'*'".$pc_date_time."'";

			
			//if($query1) echo "20**OK"; else echo "20**ERROR";die;
			//now current stock
			$curr_stock_qnty 	= $adj_stock_qnty;
		}
		else
		{
			$updateIdprod_array = $update_dataProd = array();
			//before product adjust
			$adj_before_stock_qnty 	= $before_stock_qnty+$prev_return_qnty; // CurrentStock + Before Issue Qnty

			$beforeAvgRate 			=$before_avgRate; 	
 			$nowBeforeStockAmount 	=$before_stock_amount+ str_replace("'","",$hidden_update_amount);
 			$nowBeforeAvgRate 		=$nowBeforeStockAmount/$adj_before_stock_qnty;

			if($adj_before_stock_qnty<0) //Aziz
			{
				echo "30**Stock cannot be less than zero.";disconnect($con);die;
			}
			if($adj_before_stock_qnty<=0)
			{
				$nowBeforeAvgRate 		=0; 	
 				$nowBeforeStockAmount 	=0;
			}
			$updateIdprod_array[]=$before_prod_id;
			$update_dataProd[$before_prod_id]=explode("*",("".$prev_return_qnty."*".$adj_before_stock_qnty."*".$nowBeforeAvgRate."*".$nowBeforeStockAmount."*'".$user_id."'*'".$pc_date_time."'"));
			
			//current product adjust
			$adj_curr_stock_qnty = 	$curr_stock_qnty-$txt_return_qnty; // CurrentStock + Before Issue Qnty
			$stock_valueAmount=$adj_curr_stock_qnty*$hidden_rate_avg;

			$avgRate=$stock_valueAmount/$adj_curr_stock_qnty;
			
			$updateIdprod_array[]=$txt_prod_id;

			if($adj_curr_stock_qnty<=0)
			{
				$avgRate=0;
				$stock_valueAmount=0;
				
			}
			
			$update_dataProd[$txt_prod_id]=explode("*",("".$txt_return_qnty."*".$adj_curr_stock_qnty."*".$avgRate."*".$stock_valueAmount."*'".$user_id."'*'".$pc_date_time."'"));
			
			//now current stock
			$curr_stock_qnty 	= $adj_curr_stock_qnty;
		}
		
		
	
		//****************************************** BEFORE ENTRY ADJUST END *****************************************//
		 
  		$id=$issue_mst_id;
		//yarn master table UPDATE here START----------------------//		
		$field_array_mst="company_id*issue_date*received_id*booking_id*received_mrr_no*supplier_id*updated_by*update_date";
		$data_array_mst=$cbo_company_id."*".$txt_return_date."*".$txt_received_id."*".$pi_id."*".$txt_mrr_no."*".$cbo_return_to."*'".$user_id."'*'".$pc_date_time."'";
		
		if($before_prod_id==$txt_prod_id)
		{
			$consAvgRate=$adj_avg_rate;
			$consAvgAmount=$adj_avg_rate*$txt_return_qnty;
		}
		else
		{
			$consAvgRate=$nowBeforeAvgRate;
			$consAvgAmount=$nowBeforeAvgRate*$txt_return_qnty;
		}

 		$field_array_trans="company_id*prod_id*batch_id_from_fissuertn*pi_wo_batch_no*batch_id*item_category*transaction_type*transaction_date*store_id*body_part_id*floor_id*room*rack*self*bin_box*order_uom*order_qnty*cons_uom*cons_quantity*cons_rate*cons_amount*no_of_roll*remarks*batch_lot*updated_by*update_date";
 		$data_array_trans= "".$cbo_company_id."*".$txt_prod_id."*".$hidden_batch_id."*".$hidden_batch_id."*".$hidden_batch_id."*3*3*".$txt_return_date."*".$cbo_store_name."*".$cbo_body_part."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$cbo_uom."*".$txt_return_qnty."*".$cbo_uom."*".$txt_return_qnty."*".$consAvgRate."*".$consAvgAmount."*".$txt_roll."*".$txt_remarks."*".$txt_batch_no."*'".$user_id."'*'".$pc_date_time."'"; 
		//echo "10**".$field_array_trans."<br>".$data_array_trans;die;
		$update_id = str_replace("'","",$update_id);
		$hidden_receive_trans_id = str_replace("'","",$hidden_receive_trans_id);
		$before_receive_trans_id = str_replace("'","",$before_receive_trans_id);
		$prev_return_qnty = str_replace("'","",$prev_return_qnty);
		$update_array_trans = "balance_qnty*updated_by*update_date";
		if($before_prod_id==$txt_prod_id)
		{
			if($hidden_receive_trans_id>0)
			{
				$sql_receive = sql_select("select a.id,a.balance_qnty from inv_transaction a where a.id=$hidden_receive_trans_id and a.transaction_type =1"); 
				$adjBalance = ($sql_receive[0][csf("balance_qnty")]+$prev_return_qnty)-$txt_return_qnty;
				$update_data_trans="".$adjBalance."*'".$user_id."'*'".$pc_date_time."'";
			}
		}
		else
		{
			if($before_receive_trans_id>0)
			{
				$sql_receive_before = sql_select("select a.id,a.balance_qnty from inv_transaction a where a.id=$before_receive_trans_id and a.transaction_type =1");  
				$adjBalance = ($sql_receive_before[0][csf("balance_qnty")]+$prev_return_qnty);
				$update_data_before_trans="".$adjBalance."*'".$user_id."'*'".$pc_date_time."'";
			}
			
			if($hidden_receive_trans_id>0)
			{
				$sql_receive = sql_select("select a.id,a.balance_qnty from inv_transaction a where a.id=$hidden_receive_trans_id and a.transaction_type =1"); 
				$adjBalance = $sql_receive[0][csf("balance_qnty")]-$txt_return_qnty;
				$update_data_trans="".$adjBalance."*'".$user_id."'*'".$pc_date_time."'";
			}
			
		}
		//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1); 
		$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con); 
		$field_array_mrr = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,order_save_string,inserted_by,insert_date";
		$data_array_mrr = "(".$mrrWiseIsID.",".$hidden_receive_trans_id.",".$update_id.",202,".$txt_prod_id.",".$txt_return_qnty.",".$save_data.",'".$user_id."','".$pc_date_time."')";
		
		//order_wise_pro_detail table insert here
		
		$txt_break_qnty=str_replace("'","",$txt_break_qnty);
		$txt_break_roll=str_replace("'","",$txt_break_roll);
		$txt_order_id_all=str_replace("'","",$txt_order_id_all);
		$ordr_wise_rtn_qnty_arr=explode("_",$txt_break_qnty);
		$ordr_wise_rtn_roll_arr=explode("_",$txt_break_roll);
		$ordr_id_arr=explode(",",$txt_order_id_all);
		//$proportion_id = return_next_id("id", "order_wise_pro_details", 1);
		
		
		//$roll_id = return_next_id("id", "pro_roll_details", 1);
		$field_array_proportion="id,trans_id,trans_type,entry_form,po_breakdown_id,prod_id,quantity,color_id,inserted_by,insert_date";
		$color_id=return_field_value("color","product_details_master","id=$txt_prod_id and item_category_id=3");
		 
		$data_array_proportion=$data_array_roll="";
		if($po_popup_patern_variable==1)
		{
			if(!empty($txt_break_qnty))
			{
				/*if($variable_setting_production==1)
				{
					$order_array=array();
					$field_array_roll="id,mst_id,dtls_id,po_breakdown_id,entry_form,roll_no,roll_id,qnty,inserted_by,insert_date";
					foreach($ordr_wise_rtn_roll_arr as $val)
					{
						$order_roll_arr=explode("**",$val);
						$po_id=$order_roll_arr[0];
						$roll_no=$order_roll_arr[1];
						$qty=$order_roll_arr[2];
						$rollId=$order_roll_arr[3];
						
						if($qty>0)
						{
							$roll_id = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
							if($data_array_roll!="") $data_array_roll.=", ";
							$data_array_roll.="(".$roll_id.",".$id.",".$update_id.",".$po_id.",202,'".$roll_no."','".$rollId."',".$qty.",'".$user_id."','".$pc_date_time."')";
							//$roll_id++;
							
							$order_array[$po_id]+=$qty;
						}
					}
					
					foreach($order_array as $po_id=>$po_qty)
					{
						$proportion_id = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
						if($data_array_proportion!="") $data_array_proportion.=", ";
						$data_array_proportion.="(".$proportion_id.",".$update_id.",3,202,".$po_id.",".$txt_prod_id.",".$po_qty.",".$color_id.",'".$user_id."','".$pc_date_time."')";
						//$proportion_id++;
					}
				}
				else
				{*/
					foreach($ordr_wise_rtn_qnty_arr as $val)
					{
						$order_qnty_arr=explode("**",$val);
						if($order_qnty_arr[1]>0)
						{
							$proportion_id = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
							if($data_array_proportion!="") $data_array_proportion.=", ";
							$data_array_proportion.="(".$proportion_id.",".$update_id.",3,202,".$order_qnty_arr[0].",".$txt_prod_id.",".$order_qnty_arr[1].",".$color_id.",'".$user_id."','".$pc_date_time."')";
							//$proportion_id++;
						}
					}
				//}
			}
		}
		else
		{
			$save_datas=explode(",",str_replace("'","",$save_data));
			for($i=0;$i<count($save_datas);$i++)
			{
				$proportion_id = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$order_dtls=explode("_",$save_datas[$i]);
				$order_id=$order_dtls[0];
				$order_qnty=$order_dtls[1];

				if($i==0) $add_comma=""; else $add_comma=",";

				$data_array_proportion.="$add_comma(".$proportion_id.",".$update_id.",3,202,'".$order_id."',".$txt_prod_id.",'".$order_qnty."','".$color_id."','".$user_id."','".$pc_date_time."')";
			}
		

		}
		
		/*if(!empty($txt_break_qnty))
		{
			foreach($ordr_wise_rtn_qnty_arr as $val)
			{
				$order_qnty_arr=explode("**",$val);
				if($order_qnty_arr[1]>0)
				{
					if($data_array_proportion!="") $data_array_proportion.=", ";
					$data_array_proportion.="(".$proportion_id.",".$update_id.",3,46,".$order_qnty_arr[0].",".$txt_prod_id.",".$order_qnty_arr[1].",".$color_id.",'".$user_id."','".$pc_date_time."')";
					$proportion_id++;
				}
			}
			
			if($variable_setting_production==1)
			{
				$field_array_roll="id,mst_id,dtls_id,po_breakdown_id,entry_form,roll_no,qnty,inserted_by,insert_date";
				
				foreach($ordr_wise_rtn_roll_arr as $val)
				{
					$order_roll_arr=explode("**",$val);
					
					if($order_roll_arr[2]>0)
					{
						if($data_array_roll!="") $data_array_roll.=", ";
						$data_array_roll.="(".$roll_id.",".$id.",".$update_id.",".$order_roll_arr[0].",46,".$order_roll_arr[1].",".$order_roll_arr[2].",'".$user_id."','".$pc_date_time."')";
						$roll_id++;
					}
				}
			}
		}*/
		
		//echo "10**insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
 		$query1=$query2=$query3=$query4=$query5=$rID=$transID=$propoId=$rollId=$mrrWiseIssueID=$upTrID=true;
		
		
		
		if($before_prod_id==$txt_prod_id)
		{
			//echo "10**$update_array_prod"."=".$data_array_prod;die;
			$query1= sql_update("product_details_master",$update_array_prod,$data_array_prod,"id",$before_prod_id,1);
			$rID=sql_update("inv_issue_master",$field_array_mst,$data_array_mst,"id",$id,1);
			
			$transID = sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_id,1);
			if($hidden_receive_trans_id>0)
			{
				$query3 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_id and entry_form=202");
				$query2 = sql_update("inv_transaction",$update_array_trans,$update_data_trans,"id",$hidden_receive_trans_id,1); 
				$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
			}
			if($data_array_proportion!="")
			{
				$query4 = execute_query("DELETE FROM order_wise_pro_details WHERE trans_id=$update_id and entry_form=202");
				$propoId=sql_insert("order_wise_pro_details",$field_array_proportion,$data_array_proportion,1);
				/*if($variable_setting_production==1)
				{
					$query5 = execute_query("DELETE FROM pro_roll_details WHERE dtls_id=$update_id and entry_form=202");
					$rollId=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,1);
				}*/
			}
			//mrr wise issue data insert here----------------------------//
		}
		else
		{
			$query1=execute_query(bulk_update_sql_statement("product_details_master","id",$update_array_prod,$update_dataProd,$updateIdprod_array));
			
			$rID=sql_update("inv_issue_master",$field_array_mst,$data_array_mst,"id",$id,1);
			$transID = sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_id,1); 
			if($hidden_receive_trans_id>0)
			{
				$query3 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_id and entry_form=202");
				$query2 = sql_update("inv_transaction",$update_array_trans,$update_data_trans,"id",$hidden_receive_trans_id,1);
				$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
			}
			if($before_receive_trans_id>0)
			{
				$upTrID = sql_update("inv_transaction",$update_array_trans,$update_data_before_trans,"id",$before_receive_trans_id,1);
			}
			if($data_array_proportion!="")
			{
				$query4 = execute_query("DELETE FROM order_wise_pro_details WHERE trans_id=$update_id and entry_form=202");
				$propoId=sql_insert("order_wise_pro_details",$field_array_proportion,$data_array_proportion,1);
				/*if($variable_setting_production==1000)
				{
					$query5 = execute_query("DELETE FROM pro_roll_details WHERE dtls_id=$update_id and entry_form=202");
					$rollId=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,1);
				}*/
			}
			//mrr wise issue data insert here----------------------------//
			
		}
		//echo "10**".$query1."&&".$query2."&&".$query3."&&".$query4 ."&&". $query5 ."&&".$rID."&&".$transID."&&".$propoId."&&".$rollId."&&".$mrrWiseIssueID."&&".$upTrID;	oci_rollback($con);die;

		if($db_type==0)
		{
			if($query1 && $query2 && $query3 && $query4 && $rID && $transID && $propoId  && $mrrWiseIssueID && $upTrID)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_return_no)."**".$issue_mst_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_return_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if( $query1 && $query2 && $query3 && $query4  && $rID && $transID && $propoId  && $mrrWiseIssueID  && $upTrID)
			{
				oci_commit($con);   
				echo "1**".str_replace("'","",$txt_return_no)."**".$issue_mst_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_return_no);
			}
		}
		disconnect($con);
		die;
 	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		 //no operation
	}		
}



if($action=="return_number_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);  
?>
     
<script>
	function js_set_value(mrr)
	{
 		$("#hidden_return_number").val(mrr); // mrr number
		parent.emailwindow.hide();
	}
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <tr>                	 
                    <th width="180">Search By</th>
                    <th width="200" align="center" id="search_by_td_up">Enter Return Number</th>
                    <th width="220">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr>                    
                    <td align="center">
                        <?  
                            $search_by = array(1=>'Return Number',2=>"Batch No");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 140, $search_by,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td width="" align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td>    
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" />&nbsp;&nbsp;&nbsp;
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" />
                    </td> 
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_return_search_list_view', 'search_div', 'woven_finish_fab_receive_rtn_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="5">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here-------->
                     <input type="hidden" id="hidden_return_number" value="" />
                    <!-- ---------END------------->
                </td>
            </tr>    
            </tbody>
         </tr>         
        </table>    
        <div align="center" style="margin-top:10px" valign="top" id="search_div"> </div> 
        </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}


if($action=="create_return_search_list_view")
{
	
	$ex_data = explode("_",$data);
	$search_by = $ex_data[0];
	$search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
	
	$sql_cond="";
	if($search_by==1)
	{
		if($search_common!="") $sql_cond .= " and a.issue_number like '%$search_common'";
	}
	else
	{
		if($search_common!="") $sql_cond .= " and c.batch_no like '%$search_common'";
	}
		 
	if( $txt_date_from!="" && $txt_date_to!="" ) 
	{
		if($db_type==0)
		{
			$sql_cond .= " and a.issue_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else
		{
			$sql_cond .= " and a.issue_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
		}
	}
	
	if(trim($company)!="") $sql_cond .= " and a.company_id='$company'";
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	if($db_type==0)
	{
		$sql = "select a.id, YEAR(a.insert_date) as year, a.issue_number_prefix_num, a.issue_number, a.company_id, a.supplier_id,a.issue_date, a.item_category, a.received_id,a.received_mrr_no, sum(b.cons_quantity)as cons_quantity, group_concat(c.batch_no) as batch_no
		from inv_issue_master a, inv_transaction b, pro_batch_create_mst c
		where a.id=b.mst_id and b.batch_id_from_fissuertn=c.id and b.transaction_type=3 and a.status_active=1 and a.item_category=3 and b.item_category=3 and a.entry_form=202 $sql_cond 
		group by a.id, a.issue_number_prefix_num, a.issue_number, a.company_id, a.supplier_id, a.issue_date, a.item_category, a.received_id, a.received_mrr_no, a.insert_date order by a.id";
	}
	else
	{
		$sql = "select a.id, to_char(a.insert_date,'YYYY') as year, a.issue_number_prefix_num, a.issue_number, a.company_id, a.supplier_id,a.issue_date, a.item_category, a.received_id,a.received_mrr_no, sum(b.cons_quantity)as cons_quantity, listagg(cast(c.batch_no as varchar(4000)), ',' ) within group(order by c.batch_no) as batch_no
		from inv_issue_master a, inv_transaction b, pro_batch_create_mst c
		where a.id=b.mst_id and b.batch_id_from_fissuertn=c.id and b.transaction_type=3 and a.status_active=1 and a.item_category=3 and b.item_category=3 and a.entry_form=202 $sql_cond 
		group by a.id, a.issue_number_prefix_num, a.issue_number, a.company_id, a.supplier_id, a.issue_date, a.item_category, a.received_id, a.received_mrr_no, a.insert_date order by a.id";
	}
	
	//echo $sql;
	$sql_result = sql_select($sql);
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	?>
    <table class="rpt_table" border="1" cellpadding="2" cellspacing="0" width="750" rules="all">
        <thead>
            <tr>
                <th width="40">SL</th>
                <th width="60">Return No</th>
                <th width="50">Year</th>
                <th width="130">Company Name</th>
                <th width="80">Return Date</th>
                <th width="100">Return Qty</th>
                <th width="120">Receive MRR</th>
                <th>Batch NO</th>
            </tr>
        </thead>
    </table>
    <div style="width:750px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
    <table class="rpt_table" border="1" cellpadding="2" cellspacing="0" width="730" rules="all" id="list_view">
    	<tbody>
        	<?
			$i=1;
			foreach($sql_result as $row)
			{
				 if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('issue_number')]."_".$row[csf('received_id')]; ?>');">
                    <td width="40" align="center"><? echo $i; ?></td>
                    <td width="60" align="center"><p><? echo $row[csf("issue_number_prefix_num")]; ?>&nbsp;</p></td>
                    <td width="50" align="center"><p><? echo $row[csf("year")]; ?>&nbsp;</p></td>
                    <td width="130"><p><? echo $company_arr[$row[csf("company_id")]]; ?>&nbsp;</p></td>
                    <td width="80" align="center"><? if($row[csf("issue_date")]!="" && $row[csf("issue_date")]!="0000-00-00") echo change_date_format($row[csf("issue_date")]); ?></td>
                    <td width="100" align="right"><? echo number_format($row[csf("cons_quantity")],2); ?></td>
                    <td width="120" align="center"><p><? echo $row[csf("received_mrr_no")]; ?>&nbsp;</p></td>
                    <td><p><? echo implode(",",array_unique(explode(",",$row[csf("batch_no")]))); ?>&nbsp;</p></td>
                </tr>
                <?
				$i++;
			}
			?>
    	</tbody>
    </table>
    </div>
	<?	
 	exit();
}

 

if($action=="populate_master_from_data")
{  
	$sql = "select id,issue_number,company_id,supplier_id,issue_date,item_category,received_id,received_mrr_no,pi_id,booking_id   
			from inv_issue_master 
			where id='$data' and item_category=3 and entry_form=202";
	//echo $sql;
	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#txt_return_no').val('".$row[csf("issue_number")]."');\n";
		echo "$('#issue_mst_id').val('".$row[csf("id")]."');\n";
 		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		echo "$('#txt_return_date').val('".change_date_format($row[csf("issue_date")])."');\n";
		echo "$('#txt_mrr_no').val('".$row[csf("received_mrr_no")]."');\n";
		echo "$('#txt_received_id').val('".$row[csf("received_id")]."');\n";
		
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "$('#txt_mrr_no').attr('disabled','disabled');\n";
		
		$receive_basis=return_field_value("receive_basis","inv_receive_master","id=".$row[csf("received_id")]);
		if($receive_basis==1)
		{
			$pi_no=return_field_value("pi_number","com_pi_master_details","id='".$row[csf("pi_id")]."'");
			echo "$('#txt_pi_no').removeAttr('disabled','disabled');\n";
			echo "$('#txt_pi_no').val('".$pi_no."');\n";
			echo "$('#pi_id').val('".$row[csf("pi_id")]."');\n";
		}
		else
		{
			echo "$('#txt_pi_no').attr('disabled','disabled');\n";
			echo "$('#txt_pi_no').val('');\n";
			echo "$('#pi_id').val('');\n";
		}
		echo "$('#pi_id').val('".$row[csf("booking_id")]."');\n";
		//right side list view
		//echo "show_list_view('".$row[csf("received_id")]."','show_product_listview','list_product_container','requires/woven_finish_fab_receive_rtn_controller','');\n";
   	}	
	exit();	
}



if($action=="show_dtls_list_view")
{
	
	$sql = "select a.id as issue_id, a.issue_number, a.company_id, a.supplier_id, a.issue_date, a.item_category, a.received_id, a.received_mrr_no, b.id as trans_id, b.cons_quantity, b.cons_uom, b.cons_rate, b.cons_amount, c.product_name_details, c.id as prod_id, c.color as color_id   
			from inv_issue_master a, inv_transaction b left join product_details_master c on b.prod_id=c.id
			where a.id=b.mst_id and b.item_category=3 and b.transaction_type=3 and a.id=$data";
	//echo $sql;
	$result = sql_select($sql);
	$i=1;
	$rettotalQnty=0;
	$rcvtotalQnty=0;
	$totalAmount=0;
	?> 
     	<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" width="800" rules="all">
        	<thead>
            	<tr>
                	<th width="50">SL</th>
                    <th width="130">Return No</th>
                    <th width="50">Product ID</th>
                    <th width="200">Item Description</th>
                    <th width="130">Color</th>
                    <th width="130">Received No</th>
                    <th>Return Qnty</th>
                </tr>
            </thead>
            <tbody>
            	<? 
				foreach($result as $row)
				{					
					if($i%2==0)
					$bgcolor="#E9F3FF";
					else 
					$bgcolor="#FFFFFF";
					
					/*echo "select b.balance_qnty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id=".$row[csf("prod_id")]." and b.item_category=1 and b.transaction_type=1 and a.recv_number='".$row[csf("received_mrr_no")]."'";*/
					if($row[csf("prod_id")]!="")
					{	
					$sqlTr = sql_select("select b.balance_qnty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id=".$row[csf("prod_id")]." and b.item_category=3 and b.transaction_type in (1,4) and a.id='".$row[csf("received_id")]."'");
					}
					$rcvQnty = $sqlTr[0][csf('balance_qnty')];
					
					$rettotalQnty +=$row[csf("cons_quantity")];
					//$rcvtotalQnty +=$rcvQnty;
					$totalAmount +=$row[csf("cons_amount")];
					
					
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("trans_id")];?>,<? echo $rcvQnty;?>,<? echo $row[csf("issue_id")];?>","child_form_input_data","requires/woven_finish_fab_receive_rtn_controller")' style="cursor:pointer" >
                        <td><? echo $i; ?></td>
                        <td><p><? echo $row[csf("issue_number")]; ?></p></td>
                        <td align="center"><p><? echo $row[csf("prod_id")]; ?></p></td>
                        <td ><p><? echo $row[csf("product_name_details")]; ?></p></td>
                        <td ><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
                        <td ><p><? echo $row[csf("received_mrr_no")]; ?></p></td>
                        <td align="right"><p><? echo number_format($row[csf("cons_quantity")],0); ?></p></td>
					</tr>
					<? 
					$i++; 
				} 
				?>
                	<tfoot>
                        <th colspan="6">Total</th>                         
                        <th><? echo $rettotalQnty; ?></th> 
                   </tfoot>
            </tbody>
        </table>
    <?
	exit();
}


if($action=="child_form_input_data")
{
	$ex_data = explode(",",$data);
	$data = $ex_data[0]; 	// transaction id
	$rcvQnty = $ex_data[1]; 
	$issue_id=str_replace("'","",$ex_data[2]); 
	
 	$sql = "select a.company_id, b.id as prod_id, b.product_name_details, b.current_stock,b.weight, b.gsm, b.dia_width, b.color, a.id as tr_id, a.cons_uom, a.cons_quantity, a.cons_amount, a.batch_id_from_fissuertn, a.remarks,a.store_id,a.body_part_id,a.floor_id,a.room,a.bin_box, a.rack, a.self, a.no_of_roll,a.weight_type ,a.cutable_width,c.order_save_string,a.cons_rate,b.detarmination_id,a.fabric_ref,a.rd_no from inv_transaction a, product_details_master b,inv_mrr_wise_issue_details c where a.id=$data and a.status_active=1 and a.item_category=3 and transaction_type=3 and a.prod_id=b.id and b.id=c.prod_id and a.id=c.issue_trans_id and c.entry_form=202 and b.status_active=1";
 	//echo $sql;die;
	$result = sql_select($sql);
	foreach($result as $row)
	{
		$variable_setting_production=return_field_value("fabric_roll_level","variable_settings_production","company_name='".$row[csf("company_id")]."'  and item_category_id=3 and variable_list=3 and status_active=1","fabric_roll_level");
		
 		echo "$('#txt_item_description').val('".$row[csf("product_name_details")]."');\n";
 		echo "$('#hidden_fab_ref').val('".$row[csf("fabric_ref")]."');\n";
 		echo "$('#hidden_rd_no').val('".$row[csf("rd_no")]."');\n";
		echo "$('#txt_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#before_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#txt_weight').val('".$row[csf("weight")]."');\n";
		echo "$('#txt_width').val('".$row[csf("dia_width")]."');\n";
		echo "$('#cbo_weight_type').val('".$row[csf("weight_type")]."');\n";
		echo "$('#txt_cutable_width').val('".$row[csf("cutable_width")]."');\n";
		echo "$('#prev_return_qnty').val('".$row[csf("cons_quantity")]."');\n";	
		echo "$('#cbo_uom').val(".$row[csf("cons_uom")].");\n";
		echo "$('#txt_color_name').val('".$color_arr[$row[csf("color")]]."');\n";
		echo "$('#hidden_color_id').val('".$row[csf("color")]."');\n";
		echo "$('#hidden_rate').val('".$row[csf("cons_rate")]."');\n";
		echo "$('#hidden_update_amount').val('".$row[csf("cons_amount")]."');\n";
		echo "$('#hidden_fabrication_id').val('".$row[csf("detarmination_id")]."');\n";
		echo "$('#txt_roll').val('".$row[csf("no_of_roll")]."');\n";
		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		echo "$('#save_data').val('".$row[csf("order_save_string")]."');\n";
		//echo "select recv_trans_id from inv_mrr_wise_issue_details where issue_trans_id='".$row[csf('tr_id')]."'"; pro_finish_fabric_rcv_dtls
		$recv_trans_id=return_field_value("recv_trans_id as recv_trans_id","inv_mrr_wise_issue_details","issue_trans_id='".$row[csf('tr_id')]."'","recv_trans_id" );
		if($recv_trans_id=="") $recv_trans_id=0;
		if($row[csf("batch_id_from_fissuertn")]>0)
		{
			$recv_batch_id=$row[csf("batch_id_from_fissuertn")];
			echo "$('#hidden_batch_id').val(".$row[csf("batch_id_from_fissuertn")].");\n";
		}
		else
		{
			$recv_batch_id=return_field_value("b.batch_id as batch_id","inv_mrr_wise_issue_details a, pro_finish_fabric_rcv_dtls b","a.recv_trans_id=b.trans_id and a.recv_trans_id='".$recv_trans_id."'","batch_id" );
			echo "$('#hidden_batch_id').val(".$recv_batch_id.");\n";
		}
		
		$order_id_string=return_field_value("order_id","pro_finish_fabric_rcv_dtls ","trans_id=$recv_trans_id","order_id" );
		if($order_id_string=="")
		{
			echo "$('#txt_return_qnty').removeAttr('placeholder').removeAttr('readonly').removeAttr('onDblClick').attr('placeholder','Write');\n";
		}
		else
		{
			echo "$('#txt_return_qnty').removeAttr('placeholder').removeAttr('readonly').removeAttr('onDblClick').attr('placeholder','Double Click To Search').attr('readonly','').attr('onDblClick','openmypage_rtn_qty();');\n";
		}
		
		echo "$('#txt_return_qnty').val('".$row[csf("cons_quantity")]."');\n";	
	
		$batch_no=return_field_value("batch_no","pro_batch_create_mst","id='".$recv_batch_id."'","batch_no" );
		echo "$('#txt_batch_no').val('".$batch_no."');\n";
		echo "$('#cbo_body_part').val('".$row[csf("body_part_id")]."');\n";
		

		echo "load_room_rack_self_bin('requires/woven_finish_fab_receive_rtn_controller*3', 'store','store_td', '".$row[csf('company_id')]."','"."',this.value);\n";
		echo "$('#cbo_store_name').val('".$row[csf("store_id")]."');\n";

		echo "load_room_rack_self_bin('requires/woven_finish_fab_receive_rtn_controller', 'floor','floor_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."',this.value);\n";
		echo "$('#cbo_floor').val('".$row[csf("floor_id")]."');\n";
		echo "load_room_rack_self_bin('requires/woven_finish_fab_receive_rtn_controller', 'room','room_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."',this.value);\n";
		echo "$('#cbo_room').val('".$row[csf("room")]."');\n";
		echo "load_room_rack_self_bin('requires/woven_finish_fab_receive_rtn_controller', 'rack','rack_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		echo "$('#txt_rack').val('".$row[csf("rack")]."');\n";
		echo "load_room_rack_self_bin('requires/woven_finish_fab_receive_rtn_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";	
		echo "$('#txt_shelf').val('".$row[csf("self")]."');\n";
		echo "load_room_rack_self_bin('requires/woven_finish_fab_receive_rtn_controller', 'bin','bin_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."','".$row[csf('self')]."',this.value);\n";	
		echo "$('#cbo_bin').val('".$row[csf("bin_box")]."');\n";

		
		$receive_quantity=return_field_value("cons_quantity","inv_transaction ","id=$recv_trans_id","cons_quantity" );
		$cumilitive_rtn=return_field_value("sum(b.issue_qnty) as issue_qnty","inv_mrr_wise_issue_details b","b.status_active=1 and b.prod_id='".$row[csf("prod_id")]."' and b.recv_trans_id='$recv_trans_id'","issue_qnty" );
		
		$propotion_sql=sql_select("select po_breakdown_id, quantity from order_wise_pro_details where trans_id='".$row[csf("tr_id")]."'");
		$po_wise_qnty="";$po_id_all="";
		foreach($propotion_sql as $row_order)
		{
			if($po_wise_qnty!="") $po_wise_qnty .="_";
			$po_wise_qnty .=$row_order[csf("po_breakdown_id")]."**".$row_order[csf("quantity")];
			if($po_id_all!="") $po_id_all .=",";
			$po_id_all .=$row_order[csf("po_breakdown_id")];
		}
		
		/*if($variable_setting_production==1)
		{
			$roll_sql=sql_select("select po_breakdown_id, roll_no, roll_id, qnty from  pro_roll_details where mst_id='$issue_id' and dtls_id='".$row[csf("tr_id")]."'");
			$roll_ref="";
			foreach($roll_sql as $row_roll)
			{
				if($roll_ref!="") $roll_ref .="_";
				$roll_ref .=$row_roll[csf("po_breakdown_id")]."**".$row_roll[csf("roll_no")]."**".$row_roll[csf("qnty")]."**".$row_roll[csf("roll_id")];
			}
		}*/
		echo "$('#cbo_store_name').attr('disabled','disabled');\n";
		echo "$('#cbo_floor').attr('disabled','disabled');\n";
		echo "$('#cbo_room').attr('disabled','disabled');\n";
		echo "$('#txt_rack').attr('disabled','disabled');\n";
		echo "$('#txt_shelf').attr('disabled','disabled');\n";
		echo "$('#cbo_bin').attr('disabled','disabled');\n";
		
		echo "$('#txt_break_qnty').val('$po_wise_qnty');\n";
		echo "$('#txt_break_roll').val('$roll_ref');\n";
		echo "$('#txt_order_id_all').val('$po_id_all');\n";
		
		$yet_to_iss=$receive_quantity-$cumilitive_rtn;
		echo "$('#txt_fabric_received').val('$receive_quantity');\n";	
		echo "$('#txt_cumulative_issued').val('$cumilitive_rtn');\n";
		echo "$('#txt_yet_to_issue').val('$yet_to_iss');\n";	
		echo "$('#hidden_receive_trans_id').val('$recv_trans_id');\n";
		echo "$('#before_receive_trans_id').val('$recv_trans_id');\n";	
		echo "$('#txt_global_stock').val('".$row[csf("current_stock")]."');\n";	
		echo "$('#update_id').val('".$row[csf("tr_id")]."');\n";
	}
	
 	echo "set_button_status(1, permission, 'fnc_yarn_receive_return_entry',1,1);\n";
	//echo "$('#tbl_master').find('input,select').attr('disabled', false);\n";
	//echo "disable_enable_fields( 'cbo_company_id*txt_mrr_no', 1, '', '');\n";
  	exit();
}

// pi popup here----------------------// 
if ($action=="pi_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);  
?>
     
<script>
	function js_set_value(str)
	{
		var splitData = str.split("_");		 
		$("#hidden_tbl_id").val(splitData[0]); // pi id
		$("#hidden_pi_number").val(splitData[1]); // pi number
		parent.emailwindow.hide();
	}
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <tr>                	 
                    <th align="center" id="search_by_th_up">Enter PI Number</th>
                    <th>Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr class="general">
                    <td width="180" align="center" id="search_by_td">				
                        <input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td>    
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                     </td> 
                     <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_wopi_search_list_view', 'search_div', 'woven_finish_fab_receive_rtn_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="4">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here-------->
                    <input type="hidden" id="hidden_tbl_id" value="" />
                    <input type="hidden" id="hidden_pi_number" value="hidden_pi_number" />
                    <!-- ---------END------------->
                </td>
            </tr>    
            </tbody>
         </tr>         
        </table>    
        <div align="center" style="margin-top:5px" valign="top" id="search_div"> </div> 
        </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_wopi_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_search_common = trim($ex_data[0]);
	$txt_date_from = $ex_data[1];
	$txt_date_to = $ex_data[2];
	$company = $ex_data[3];
 	
	$sql_cond="";
	$sql_cond .= " and a.pi_number LIKE '%$txt_search_common%'";
	if(trim($company)!=0) $sql_cond .= " and a.importer_id='$company'";
	
	if($txt_date_from!="" && $txt_date_to!="" )
	{
		if($db_type==0)
		{
			$sql_cond .= " and a.pi_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else
		{
			$sql_cond .= " and a.pi_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
		}
	}
	
	$sql = "select a.id, a.pi_number, a.pi_date, a.supplier_id, a.currency_id, a.source, c.lc_number as lc_number
			from com_pi_master_details a 
			left join com_btb_lc_pi b on a.id=b.pi_id 
			left join com_btb_lc_master_details c on b.com_btb_lc_master_details_id=c.id
			where 
			a.item_category_id = 3 and
			a.status_active=1 and a.is_deleted=0
			$sql_cond order by a.id";
	//echo $sql;
	$result = sql_select($sql);
	$arr=array(3=>$currency,4=>$source);
	
	echo  create_list_view("list_view", "PI No, LC ,Date, Currency, Source","150,200,100,100","750","230",0, $sql , "js_set_value", "id,pi_number", "", 1, "0,0,0,currency_id,source", $arr, "pi_number,lc_number,pi_date,currency_id,source", "",'','0,0,3,1,0') ;
	exit();	
}

if ($action=="yarn_receive_return_print") //query not ok 
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);//die;
	$company_name_arr=return_library_array( "select id, company_name from  lib_company",'id','company_name');
	$supplier_name_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	//echo $sql_buyer_style;
	$sql=" select id, issue_number, received_id, issue_date, supplier_id from  inv_issue_master where id='$data[1]' and entry_form=202 and item_category=3 and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql);
	
	 $sql_rretrn_to= sql_select("select id,recv_number,entry_form,company_id,receive_basis,receive_purpose,lc_no,knitting_source,knitting_company from inv_receive_master 
			where id='".$dataArray[0][csf('received_id')]."' and entry_form in(17)");
		$kniting_company=$sql_rretrn_to[0][csf('knitting_company')];
		$kniting_source=$sql_rretrn_to[0][csf('knitting_source')];
		$rcv_num=$sql_rretrn_to[0][csf('recv_number')];
		//$company_id=$row[csf("company_id")];
		if($kniting_source==1)
		{
			$company_nam=$company_name_arr[$kniting_company];
		}
		else if($kniting_source==3)
		{	
			$company_nam=$supplier_name_arr[$kniting_company];
		}
		

	$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst", "id", "batch_no");
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr = return_library_array("select id,country_name from lib_country","id","country_name");
	//$receive_arr = return_library_array("select id,recv_number from inv_receive_master","id","recv_number");
	$sql_buyer_style=sql_select("select d.buyer_name, d.style_ref_no from inv_transaction a, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d where a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.mst_id='$data[1]' and a.transaction_type=3 and b.entry_form=202 group by d.buyer_name, d.style_ref_no");
	$buyer_name=$style_ref="";
	foreach($sql_buyer_style as $row)
	{
		$buyer_name.=$buyer_arr[$row[csf("buyer_name")]]." , ";
		$style_ref.=$row[csf("style_ref_no")]." , ";
	}
	$buyer_name=chop($buyer_name, " , ");
	$style_ref=chop($style_ref, " , ");
	?>
	<div style="width:1130px;">
    <table width="1130" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:20px"><strong><? echo $company_name_arr[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<?
            $data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
                ?>
            <td  align="left">
                <?
                foreach($data_array as $img_row)
                {
					?>
                    <img src='../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50' align="middle" />	
                    <? 
                }
                ?>
           </td>
        	<td colspan="4" align="center" style="font-size:14px">  
				<?
					//echo show_company($data[0],'','');//Aziz
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
					?>
						<? echo $result[csf('plot_no')]; ?> 
						 <? echo $result[csf('level_no')]?>
						 <? echo $result[csf('road_no')]; ?> 
						 <? echo $result[csf('block_no')];?> 
						 <? echo $result[csf('city')];?> 
						 <? echo $result[csf('zip_code')]; ?> 
						 <? echo $result[csf('province')];?> 
						<? echo $country_arr[$result[csf('country_id')]]; ?><br> 
						Email Address: <? echo $result[csf('email')];?> 
						Website No: <? echo $result[csf('website')];
					}
                ?> 
            </td>  
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:16px"><strong><u> <? echo $data[3]; ?></u></strong></td>
        </tr>
        <tr>
        	<td width="120"><strong>Return Number:</strong></td>
            <td width="175"><? echo $dataArray[0][csf('issue_number')]; ?></td>
            <td width="110"><strong>Receive ID:</strong></td>
            <td width="175"><? echo $rcv_num; //echo $receive_arr[$dataArray[0][csf('received_id')]]; ?></td>
            <td width="100"><strong>Return To :</strong></td> 
            <td width="175"><? echo $company_nam; echo $supplier_name_arr[$data[2]]; ?></td>
        </tr>
        <tr>
        	<?php /*?><td ><strong>Buyer:</strong></td>
            <td ><? echo $buyer_name; ?></td>
            <td ><strong>Style:</strong></td>
            <td ><? echo $style_ref; ?></td><?php */?>
            <td><strong>Return Date:</strong></td>
            <td ><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
            
        </tr>
    </table>
	<div style="width:100%;">
		<table align="right" cellspacing="0" width="1130" border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="180">Item Description</th>
                <th width="70">Buyer</th>
                <th width="70">Style Ref.</th>
                <th width="70">Order No</th>
                <th width="70">Color</th>
                <th width="70">Width</th>
                <th width="70">Weight</th>
                <th width="50">No of Roll</th>
                <th width="80">Batch</th> 
                <th width="50">UOM</th>
                <th width="80">Return Qty.</th>
                <th>Remarks</th>
            </thead>
	<?
	$mrr_no =$dataArray[0][csf('issue_number')];;
	//$up_id =$data[1];
	$cond="";
	if($mrr_no!="") $cond .= " and c.issue_number='$mrr_no'";
	//if($up_id!="") $cond .= " and a.id='$up_id'";
	
	$sql_dtls = "select b.id as prod_id, b.product_name_details, a.id as tr_id, a.batch_id_from_fissuertn, a.store_id, a.cons_uom, a.cons_quantity, b.color as color_id, a.no_of_roll,a.remarks ,d.po_breakdown_id, sum(d.quantity) as receive_qnty,b.dia_width, b.weight 
	from inv_transaction a, product_details_master b, inv_issue_master c,order_wise_pro_details d 
	where c.id=a.mst_id  and d.trans_id=a.id  and a.status_active=1 and a.company_id='$data[0]' and c.id='$data[1]' and a.item_category=3 and transaction_type=3 and a.prod_id=b.id and b.status_active=1  and d.status_active=1 and d.is_deleted=0 group by  b.id, b.product_name_details, a.id, a.batch_id_from_fissuertn, a.store_id, a.cons_uom, a.cons_quantity,b.color, a.no_of_roll, a.remarks ,d.po_breakdown_id,b.dia_width, b.weight";
			
  	$sql_result= sql_select($sql_dtls);
	$poIds="";
	foreach($sql_result as $row)
	{
		$poIds.=$row[csf('po_breakdown_id')].',';
	}
	 $poId=chop($poIds,",");
	$sql_wo_po_details=sql_select("select b.id,a.style_ref_no,a.buyer_name,b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$data[0]' and b.id in($poId) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($sql_wo_po_details as $row)
	{
		$po_wise_data[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$po_wise_data[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$po_wise_data[$row[csf('id')]]['po_number']=$row[csf('po_number')];
	}
	 $i=1;		
	foreach($sql_result as $row)
	{
		if ($i%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
		//$qnty+=$row[csf('cons_quantity')];
		$qnty+=$row[csf('receive_qnty')];
		$roll_qnty+=$row[csf('no_of_roll')];
		?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td><? echo $i; ?></td>
                <td><? echo $row[csf('product_name_details')]; ?></td>
                <td><? echo $buyer_arr[$po_wise_data[$row[csf('po_breakdown_id')]]['buyer_name']]; ?></td>
                <td><? echo $po_wise_data[$row[csf('po_breakdown_id')]]['style_ref_no']; ?></td>
                <td><? echo $po_wise_data[$row[csf('po_breakdown_id')]]['po_number']; ?></td>
                <td align="center"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
                <td><? echo $row[csf('dia_width')]; ?></td>
                <td><? echo $row[csf('weight')]; ?></td>
                <td align="right"><? echo $row[csf('no_of_roll')]; ?></td>
                <td align="center"><? echo $batch_arr[$row[csf("batch_id_from_fissuertn")]]; ?></td>
                <td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
                <td align="right"><? echo number_format($row[csf('receive_qnty')],2); //number_format($row[csf('cons_quantity')],2);   ?></td>
                <td><? echo $row[csf('remarks')]; ?></td>
			</tr>
	<?
    $i++;
    }
    ?>
        	<tr> 
                <td align="right" colspan="11" >Total</td>
                <?php /*?><td align="right"><? echo number_format($roll_qnty,0,'',','); ?></td><?php */?>
                <td align="right"><? echo number_format($qnty,2); ?></td>
                <td align="right">&nbsp;</td>
                
			</tr>
		</table>
        <br>
		 <?
            echo signature_table(130, $data[0], "1130px");
         ?>
      </div>
	</div> 
	<?
    exit();
}
if ($action=="yarn_receive_return_print_2") //query not ok 
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);//die;
	$company_name_arr=return_library_array( "select id, company_name from  lib_company",'id','company_name');
	$supplier_name_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	//echo $sql_buyer_style;
	$sql=" select id, issue_number, received_id, issue_date, supplier_id from  inv_issue_master where id='$data[1]' and entry_form=202 and item_category=3 and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql);
	
	 $sql_rretrn_to= sql_select("select id,recv_number,entry_form,company_id,receive_basis,receive_purpose,lc_no,knitting_source,knitting_company from inv_receive_master 
			where id='".$dataArray[0][csf('received_id')]."' and entry_form in(17)");
		$kniting_company=$sql_rretrn_to[0][csf('knitting_company')];
		$kniting_source=$sql_rretrn_to[0][csf('knitting_source')];
		$rcv_num=$sql_rretrn_to[0][csf('recv_number')];
		//$company_id=$row[csf("company_id")];
		if($kniting_source==1)
		{
			$company_nam=$company_name_arr[$kniting_company];
		}
		else if($kniting_source==3)
		{	
			$company_nam=$supplier_name_arr[$kniting_company];
		}
		

	$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst", "id", "batch_no");
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr = return_library_array("select id,country_name from lib_country","id","country_name");
	//$receive_arr = return_library_array("select id,recv_number from inv_receive_master","id","recv_number");
	$sql_buyer_style=sql_select("select d.buyer_name, d.style_ref_no from inv_transaction a, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d where a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.mst_id='$data[1]' and a.transaction_type=3 and b.entry_form=202 group by d.buyer_name, d.style_ref_no");
	$buyer_name=$style_ref="";
	foreach($sql_buyer_style as $row)
	{
		$buyer_name.=$buyer_arr[$row[csf("buyer_name")]]." , ";
		$style_ref.=$row[csf("style_ref_no")]." , ";
	}
	$buyer_name=chop($buyer_name, " , ");
	$style_ref=chop($style_ref, " , ");
	?>
	<div style="width:1470px;">
    <table width="1470" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:20px"><strong><? echo $company_name_arr[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<?
            $data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
                ?>
            <td  align="left">
                <?
                foreach($data_array as $img_row)
                {
					?>
                    <img src='../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50' align="middle" />	
                    <? 
                }
                ?>
           </td>
        	<td colspan="4" align="center" style="font-size:14px">  
				<?
					//echo show_company($data[0],'','');//Aziz
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
					?>
						<? echo $result[csf('plot_no')]; ?> 
						 <? echo $result[csf('level_no')]?>
						 <? echo $result[csf('road_no')]; ?> 
						 <? echo $result[csf('block_no')];?> 
						 <? echo $result[csf('city')];?> 
						 <? echo $result[csf('zip_code')]; ?> 
						 <? echo $result[csf('province')];?> 
						<? echo $country_arr[$result[csf('country_id')]]; ?><br> 
						Email Address: <? echo $result[csf('email')];?> 
						Website No: <? echo $result[csf('website')];
					}
                ?> 
            </td>  
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:16px"><strong><u> <? echo $data[3]; ?></u></strong></td>
        </tr>
        <tr>
        	<td width="120"><strong>Return Number:</strong></td>
            <td width="175"><? echo $dataArray[0][csf('issue_number')]; ?></td>
            <td width="110"><strong>Receive ID:</strong></td>
            <td width="175"><? echo $rcv_num; //echo $receive_arr[$dataArray[0][csf('received_id')]]; ?></td>
            <td width="100"><strong>Return To :</strong></td> 
            <td width="175"><? echo $company_nam; echo $supplier_name_arr[$data[2]]; ?></td>
        </tr>
        <tr>
        	<?php /*?><td ><strong>Buyer:</strong></td>
            <td ><? echo $buyer_name; ?></td>
            <td ><strong>Style:</strong></td>
            <td ><? echo $style_ref; ?></td><?php */?>
            <td><strong>Return Date:</strong></td>
            <td ><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
            
        </tr>
    </table>
	<div style="width:100%;">
		<table align="right" cellspacing="0" width="1470" border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="180">Item Description</th>
                <th width="100">RD No</th>
                <th width="100">Fabric Ref</th>
                <th width="70">Buyer</th>
                <th width="70">Style Ref.</th>
                <th width="70">Order No</th>
                <th width="70">Color</th>
                <th width="70">Width</th>
                <th width="70">Cutable Width</th>
                <th width="70">Weight</th>
                <th width="70">Weight Type</th>
                <th width="50">No of Roll</th>
                <th width="80">Batch</th> 
                <th width="50">UOM</th>
                <th width="80">Return Qty.</th>
                <th>Remarks</th>
            </thead>
	<?
	$mrr_no =$dataArray[0][csf('issue_number')];;
	//$up_id =$data[1];
	$cond="";
	if($mrr_no!="") $cond .= " and c.issue_number='$mrr_no'";
	//if($up_id!="") $cond .= " and a.id='$up_id'";
	
	$sql_dtls = "select b.id as prod_id, b.product_name_details, a.id as tr_id, a.batch_id_from_fissuertn, a.store_id, a.cons_uom, a.cons_quantity, b.color as color_id, a.no_of_roll,a.remarks ,d.po_breakdown_id, sum(d.quantity) as receive_qnty,b.dia_width, b.weight,a.cutable_width,a.fabric_ref,a.rd_no,a.weight_type 
	from inv_transaction a, product_details_master b, inv_issue_master c,order_wise_pro_details d 
	where c.id=a.mst_id  and d.trans_id=a.id  and a.status_active=1 and a.company_id='$data[0]' and c.id='$data[1]' and a.item_category=3 and transaction_type=3 and a.prod_id=b.id and b.status_active=1  and d.status_active=1 and d.is_deleted=0 group by  b.id, b.product_name_details, a.id, a.batch_id_from_fissuertn, a.store_id, a.cons_uom, a.cons_quantity,b.color, a.no_of_roll, a.remarks ,d.po_breakdown_id,b.dia_width, b.weight,a.cutable_width,a.fabric_ref,a.rd_no,a.weight_type";
			
  	$sql_result= sql_select($sql_dtls);
	$poIds="";
	foreach($sql_result as $row)
	{
		$poIds.=$row[csf('po_breakdown_id')].',';
	}
	 $poId=chop($poIds,",");
	$sql_wo_po_details=sql_select("select b.id,a.style_ref_no,a.buyer_name,b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$data[0]' and b.id in($poId) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($sql_wo_po_details as $row)
	{
		$po_wise_data[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$po_wise_data[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$po_wise_data[$row[csf('id')]]['po_number']=$row[csf('po_number')];
	}
	 $i=1;		
	foreach($sql_result as $row)
	{
		if ($i%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
		//$qnty+=$row[csf('cons_quantity')];
		$qnty+=$row[csf('receive_qnty')];
		$roll_qnty+=$row[csf('no_of_roll')];
		?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td><? echo $i; ?></td>
                <td><? echo $row[csf('product_name_details')]; ?></td>
                <td align="center"><? echo $row[csf('rd_no')]; ?></td>
                <td align="left"><? echo $row[csf('fabric_ref')]; ?></td>
                <td><? echo $buyer_arr[$po_wise_data[$row[csf('po_breakdown_id')]]['buyer_name']]; ?></td>
                <td><? echo $po_wise_data[$row[csf('po_breakdown_id')]]['style_ref_no']; ?></td>
                <td><? echo $po_wise_data[$row[csf('po_breakdown_id')]]['po_number']; ?></td>
                <td align="center"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
                <td align="center"><? echo $row[csf('dia_width')]; ?></td>
                <td align="center"><? echo $row[csf('cutable_width')]; ?></td>
                <td align="center"><? echo $row[csf('weight')]; ?></td>
                <td align="center"><? echo $fabric_weight_type[$row[csf('weight_type')]]; ?></td>
                <td align="right"><? echo $row[csf('no_of_roll')]; ?></td>
                <td align="center"><? echo $batch_arr[$row[csf("batch_id_from_fissuertn")]]; ?></td>
                <td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
                <td align="right"><? echo number_format($row[csf('receive_qnty')],2); //number_format($row[csf('cons_quantity')],2);   ?></td>
                <td><? echo $row[csf('remarks')]; ?></td>
			</tr>
	<?
    $i++;
    }
    ?>
        	<tr> 
                <td align="right" colspan="15" >Total</td>
                <?php /*?><td align="right"><? echo number_format($roll_qnty,0,'',','); ?></td><?php */?>
                <td align="right"><? echo number_format($qnty,2); ?></td>
                <td align="right">&nbsp;</td>
                
			</tr>
		</table>
        <br>
		 <?
            echo signature_table(130, $data[0], "1470px");
         ?>
      </div>
	</div> 
	<?
    exit();
}


?>
