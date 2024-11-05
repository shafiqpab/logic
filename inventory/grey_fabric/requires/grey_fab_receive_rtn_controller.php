<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//auto_update
//--------------------------------------------------------------------------------------------

$variable_setting_production=return_field_value("fabric_roll_level","variable_settings_production","company_name='$cbo_company_name' and item_category_id=13 and variable_list=3 and status_active=1","fabric_roll_level");


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
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_mrr_search_list_view', 'search_div', 'grey_fab_receive_rtn_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="5">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here-->
                     <input type="hidden" id="hidden_recv_number" value="" />
                    <!-- -END-->
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
	if($variable_setting_inventory==1) $entry_form_ref=" and a.entry_form in(2,22)"; else $entry_form_ref=" and a.entry_form in(22)";
	$sql = "select a.id,a.recv_number_prefix_num,a.recv_number,$year_field, a.challan_no,a.receive_date,a.receive_basis,sum(b.cons_quantity) as receive_qnty,a.entry_form from inv_transaction b, inv_receive_master a where a.id=b.mst_id $entry_form_ref  and a.status_active=1 $sql_cond group by a.id, a.recv_number_prefix_num ,a.recv_number,a.challan_no,a.receive_date,a.receive_basis,a.insert_date,a.entry_form order by a.id";
	//echo $sql;
	?>
    	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="720">
        	<thead>
                <tr>
                	<th width="50">SL</th>
                    <th width="100">MRR No</th>
                    <th width="100">Year</th>
                    <th width="150">Challan No</th>
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
				if ($i%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";

				?>
            	<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;" onClick="js_set_value('<? echo $row[csf("id")]; ?>_<? echo $row[csf("recv_number")]; ?>')">
                	<td width="50" align="center"><p><? echo $i; ?>&nbsp;</p></td>
                    <td width="100" align="center"><p><? echo $row[csf("recv_number_prefix_num")]; ?>&nbsp;</p></td>
                    <td width="100" align="center"><p><? echo $row[csf("year")]; ?>&nbsp;</p></td>
                    <td width="150" ><p><? echo $row[csf("challan_no")]; ?>&nbsp;</p></td>
                    <td width="100" align="center"><p><? if($row[csf("receive_date")]!="" && $row[csf("receive_date")]!="0000-00-00") echo change_date_format($row[csf("receive_date")]); ?>&nbsp;</p></td>
                    <td width="100"><p>
					<?
					if($row[csf("entry_form")]==22)
					{
						echo $receive_basis_arr[$row[csf("receive_basis")]]; 
					}
					else
					{
						$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
						echo $receive_basis[$row[csf("receive_basis")]];
					}
					 
					?>&nbsp;</p></td>
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
	$sql = "select id,recv_number,entry_form,company_id,receive_basis,receive_purpose,lc_no,knitting_source,knitting_company
			from inv_receive_master 
			where id='$data' and entry_form in(2,22)";
	//echo $sql;
	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#txt_received_id').val('".$row[csf("id")]."');\n";
		echo "$('#txt_mrr_no').val('".$row[csf("recv_number")]."');\n";
		echo "$('#cbo_company_name').val(".$row[csf("company_id")].");\n";
		
		$kniting_company=$row[csf("knitting_company")];
		$kniting_source=$row[csf("knitting_source")];
		$company_id=$row[csf("company_id")];
		//echo "select a.id,a.supplier_name from lib_supplier a where a.id=$kniting_company;\n";
		echo "load_drop_down( 'requires/grey_fab_receive_rtn_controller', $kniting_company+'_'+$kniting_source+'_'+$company_id, 'load_drop_down_knitting_com','knitting_com');\n";
		if($row[csf("receive_basis")]==1)
		{
			echo "$('#txt_pi_no').removeAttr('disabled','disabled');\n";
		}
		else
		{
			echo "$('#txt_pi_no').attr('disabled','disabled');\n";
		}
		
		
		//right side list view
		echo"show_list_view('".$row[csf("id")]."','show_product_listview','list_product_container','requires/grey_fab_receive_rtn_controller','');\n";
   	}	
	exit();	
}

if($action=="load_drop_down_knitting_com")
{
	$data = explode("_",$data);
	$kniting_company=$data[0];
	$company_id=$data[2];
	
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_return_to", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select Knit Company--", "$company_id", "",1 );
	}
	else if($data[1]==3)
	{	
		echo create_drop_down( "cbo_return_to", 152, "select id,supplier_name from lib_supplier  where id=$kniting_company","id,supplier_name",1, "--Select Knit Company--", "$kniting_company", "",1);
	}
	else
	{
		echo create_drop_down( "cbo_return_to", 152, $blank_array,"",1, "--Select Knit Company--", 1, "" ,1);
	}
	exit();
}




//right side product list create here--------------------//
if($action=="show_product_listview")
{ 
 	$mrr_no = $data;
	$sql = "select c.product_name_details,c.current_stock,a.id as mrr_id,b.id as tr_id, c.id as prod_id,b.cons_quantity,b.balance_qnty
	from inv_receive_master a, inv_transaction b, product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and a.id='$mrr_no'";
	//echo $sql;
  	$result = sql_select($sql);
	$i=1; 
 	?>
    	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all">
        	<thead>
                <tr>
                	<th>SL</th>
                    <th>Product Name</th>
                    <th>Curr.Stock</th>
                </tr>
            </thead>
            <tbody>
            	<? foreach($result as $row)
				{ 
					if ($i%2==0)$bgcolor="#E9F3FF";						
					else $bgcolor="#FFFFFF";  
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("tr_id")];?>","item_details_form_input","requires/grey_fab_receive_rtn_controller")' style="cursor:pointer" >
					<td><? echo $i; ?></td>
					<td><? echo $row[csf("product_name_details")]; ?></td>
					<td align="right"><? echo $row[csf("balance_qnty")]; ?></td>
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
	$sql = "select a.id as trans_id, b.id as prod_id, b.product_name_details, b.gsm, b.dia_width, b.current_stock, a.mst_id, a.cons_uom, a.cons_rate, a.cons_quantity, a.cons_amount, a.balance_qnty, a.balance_amount, c.booking_without_order
			from inv_transaction a, product_details_master b, inv_receive_master c
 			where a.id=$data and a.status_active=1 and a.prod_id=b.id and c.id=a.mst_id and b.status_active=1 and a.transaction_type=1";
 	//echo $sql;die;
	$result = sql_select($sql);
	foreach($result as $row)
	{
		
 		echo "$('#txt_item_description').val('".$row[csf("product_name_details")]."');\n";
		echo "$('#txt_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#txt_gsm').val('".$row[csf("gsm")]."');\n";
		echo "$('#txt_dia').val('".$row[csf("dia_width")]."');\n";
		
		
		echo "$('#txt_return_qnty').val('');\n";
		echo "$('#txt_break_qnty').val('');\n";
		echo "$('#txt_break_roll').val('');\n";
		echo "$('#txt_order_id_all').val('');\n";
		
		if($db_type==2)
		{
			// floor_id, room, bin_box
			$lot_rack_self=return_field_value("(yarn_lot || '__' || yarn_count || '__' || rack || '__' || self || '__' || stitch_length) as rack_selt","pro_grey_prod_entry_dtls ","status_active=1 and trans_id='".$row[csf("trans_id")]."'","rack_selt" );
		}
		else if($db_type==0)
		{
			$lot_rack_self=return_field_value("concat(yarn_lot,'__',yarn_count,'__',rack,'__',self,'__',stitch_length) as rack_selt","pro_grey_prod_entry_dtls ","status_active=1 and trans_id='".$row[csf("trans_id")]."'","rack_selt" );
		}
		
		echo "$('#lot_count_rack_shelf').val('$lot_rack_self');\n";
		
		
		echo "$('#txt_fabric_received').val('".$row[csf("cons_quantity")]."');\n";
		echo "$('#hidden_receive_trans_id').val('".$row[csf("trans_id")]."');\n";
		if($row[csf("booking_without_order")]==1)
		{
			echo "$('#txt_return_qnty').removeAttr('placeholder').removeAttr('readonly').removeAttr('onDblClick').attr('placeholder','write');\n";
		}
		else
		{
			echo "$('#txt_return_qnty').removeAttr('placeholder').removeAttr('readonly').removeAttr('onDblClick').attr('placeholder','Double Click To Search').attr('readonly','').attr('onDblClick','openmypage_rtn_qty();');\n";
		}
		$cumilitive_rtn=return_field_value("sum(b.issue_qnty) as issue_qnty","inv_mrr_wise_issue_details b","b.status_active=1 and b.prod_id='".$row[csf("prod_id")]."' and b.recv_trans_id='".$row[csf("trans_id")]."'","issue_qnty" );
		
		$yet_to_issue=number_format($row[csf("cons_quantity")]-$cumilitive_rtn,2);
		$cumilitive_rtn=number_format($cumilitive_rtn,2);
		$yet_to_issue=number_format($yet_to_issue,2);
		echo "$('#cbo_uom').val('".$row[csf("cons_uom")]."');\n";
		echo "$('#txt_cumulative_issued').val('$cumilitive_rtn');\n";
		echo "$('#txt_yet_to_issue').val('$yet_to_issue');\n";
		echo "$('#txt_global_stock').val('".number_format($row[csf("current_stock")],2)."');\n";
		
	}
	exit();		
}


if($action=="return_po_popup")
{
	echo load_html_head_contents("Receive Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_received_id=str_replace("'","",$txt_received_id);
	$txt_prod_id=str_replace("'","",$txt_prod_id);
	$hidden_receive_trans_id=str_replace("'","",$hidden_receive_trans_id);
	$update_id=str_replace("'","",$update_id);
	$txt_return_qnty=str_replace("'","",$txt_return_qnty);
	
	if($update_id>0 && $txt_return_qnty>0)
	{
		$order_sql=sql_select("select po_breakdown_id,quantity from order_wise_pro_details where trans_id=$update_id and trans_type=3 and entry_form=45 and status_active=1");
		foreach($order_sql as $row)
		{
			$order_wise_qnty_arr[$row[csf("po_breakdown_id")]]=$row[csf("quantity")];
		}
	}
	//echo $variable_setting_production.Fuad;die;
	
	
	if($variable_setting_production==1)
	{
		$table_width=600;
	}
	else
	{
		$table_width=500;
	}
	?>
<script>
	
	
	function js_set_value()
	{
		var table_legth=$('#pop_table tbody tr').length;
		var break_qnty=break_roll=break_id="";
		var tot_qnty=0;
		for(var i=1; i<=table_legth; i++)
		{
			//if(i!=1) break_qnty+="_";
			tot_qnty +=($("#issueqnty_"+i).val()*1);
			if(break_qnty!="") break_qnty +="_";
			break_qnty+=($("#poId_"+i).val()*1)+'**'+($("#issueqnty_"+i).val()*1);
			if(break_roll!="") break_roll +="_";
			break_roll+=($("#poId_"+i).val()*1)+'**'+($("#roll_"+i).val()*1)+'**'+($("#issueqnty_"+i).val()*1);
			if(break_id!="") break_id +=",";
			break_id+=($("#poId_"+i).val()*1);
		}
		$("#tot_qnty").val(tot_qnty);
		$("#break_qnty").val(break_qnty);
		$("#break_roll").val(break_roll);
		$("#break_order_id").val(break_id);
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
						if($variable_setting_production==1)
						{
							?>
							<th>Roll</th>
							<?
						}
						?>           
                        <th width="120">Return Quantity</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$po_no_arr = return_library_array("select id,po_number from wo_po_break_down","id","po_number");

				$sql=sql_select("select a.prod_id, a.po_breakdown_id, sum(a.quantity) as receive_qnty, b.mst_id from  order_wise_pro_details a, inv_transaction b where a.trans_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_name' and b.mst_id='$txt_received_id' and b.prod_id='$txt_prod_id' and a.entry_form in(2,22) and b.transaction_type in(1,4) group by b.mst_id, a.prod_id, a.po_breakdown_id");
				$i=1;
				foreach($sql as $row)
				{
					$cumilitive_issue=return_field_value("sum(c.quantity) as cumu_qnty","inv_transaction a, inv_mrr_wise_issue_details b,  order_wise_pro_details c","a.id=b.issue_trans_id and a.id=c.trans_id and c.status_active=1 and b.recv_trans_id='$hidden_receive_trans_id' and c.po_breakdown_id='".$row[csf('po_breakdown_id')]."'","cumu_qnty");
					?>
                	<tr>
                    	<td align="center"><input type="text" id="poNo_<? echo $i; ?>" name="poNo_<? echo $i; ?>" class="text_boxes" style="width:140px" value="<? echo $po_no_arr[$row[csf("po_breakdown_id")]];  ?>"  readonly disabled >
                        <input type="hidden" id="poId_<? echo $i; ?>" name="poId_<? echo $i; ?>" class="text_boxes" style="width:140px" value="<? echo $row[csf("po_breakdown_id")];  ?>"  readonly disabled >
                        </td>
                        <td align="center"> <input type="text" id="recevqnty_<? echo $i; ?>" name="recevqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:110px" value="<? echo number_format($row[csf("receive_qnty")],2);  ?>" readonly disabled ></td>
                        <td align="center"><input type="text" id="cumulativeIssue_<? echo $i; ?>" name="cumulativeIssue_<? echo $i; ?>" value="<? echo number_format($cumilitive_issue,2); ?>" class="text_boxes_numeric" style="width:110px" readonly disabled ></td>
                        <?
						if($variable_setting_production==1)
						{
							?>
							<td align="center"><input type="text" id="roll_<? echo $i; ?>" name="roll_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" ></td>
							<?
						}
						else
						{
							?>
							<td align="center" style="display:none;"><input type="text" id="roll_<? echo $i; ?>" name="roll_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" ></td>
							<?
						}
						?> 
                        <td align="center">
                        <input type="text" id="issueqnty_<? echo $i; ?>" name="issueqnty_<? echo $i; ?>" onKeyUp="fn_calculate(<? echo $i; ?>);" class="text_boxes_numeric" value="<? echo $order_wise_qnty_arr[$row[csf("po_breakdown_id")]]; ?>" style="width:110px" >
                        <input type="hidden" id="hiddenissueqnty_<? echo $i; ?>" name="hiddenissueqnty_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $order_wise_qnty_arr[$row[csf("po_breakdown_id")]]; ?>">
                        </td>
                        
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
	$lot_count_rack_shelf=str_replace("'","",$lot_count_rack_shelf);

	$mrr_store_id = return_field_value("store_id", "inv_receive_master", "id=$txt_received_id and status_active = 1 and is_deleted=0", "store_id"); 

	$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$txt_prod_id and store_id =$mrr_store_id and transaction_type in (1,4,5) and status_active = 1 and is_deleted=0", "max_date");      
	if($max_recv_date != "")
    {
        $max_recv_date = date("Y-m-d", strtotime($max_recv_date));
        $return_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_return_date)));
        if ($return_date < $max_recv_date) 
        {
            echo "20**Return Date Can not Be Less Than Last Receive Date Of This Lot";
            die;
        }
    }
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
 		
		//---------------Check Duplicate product in Same return number ------------------------//
		$duplicate = is_duplicate_field("b.id","inv_issue_master a, inv_transaction b","a.id=b.mst_id and a.id=$issue_mst_id and b.prod_id=$txt_prod_id and b.transaction_type=3"); 
		if($duplicate==1) 
		{
			echo "20**Duplicate Product is Not Allow in Same Return Number.";disconnect($con);
			//check_table_status( $_SESSION['menu_id'], 0 );
			die;
		}
		//------------------------------Check Duplicate END---------------------------------------//
		
		$txt_return_qnty=str_replace("'","",$txt_return_qnty);
		$txt_global_stock=str_replace("'","",$txt_global_stock);
		if($txt_return_qnty>$txt_global_stock)
		{
			echo "30**Return Quantity Not Over Global Stock.";disconnect($con);
			die;
		}
		
		
 		if(str_replace("'","",$issue_mst_id)!="")
		{
			$new_return_number[0] = str_replace("'","",$txt_return_no);
			$id=$issue_mst_id;
			//issue master table UPDATE here START----------------------//		
 			$field_array_mst="company_id*issue_date*received_id*received_mrr_no*pi_id*updated_by*update_date";
			$data_array_mst=$cbo_company_name."*".$txt_return_date."*".$txt_received_id."*".$txt_mrr_no."*".$pi_id."*'".$user_id."'*'".$pc_date_time."'";
			//echo $field_array."<br>".$data_array;die;
		}
		else  	
		{	 
			//issue master table entry here START---------------------------------------//		
			//$id=return_next_id("id", "inv_issue_master", 1);
			
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
					
			//$new_return_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'GRR', date("Y",time()), 5, "select issue_number_prefix,issue_number_prefix_num from inv_issue_master where company_id=$cbo_company_name and entry_form=45 and $year_cond=".date('Y',time())." order by id DESC ", "issue_number_prefix", "issue_number_prefix_num" ));
			$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
			$new_return_number = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,$cbo_company_name,'GRR',45,date("Y",time()),13 ));
			
 			$field_array_mst="id, issue_number_prefix, issue_number_prefix_num, issue_number, entry_form, item_category, company_id, issue_date, received_id, received_mrr_no, pi_id, inserted_by, insert_date";
			$data_array_mst="(".$id.",'".$new_return_number[1]."','".$new_return_number[2]."','".$new_return_number[0]."',45,13,".$cbo_company_name.",".$txt_return_date.",".$txt_received_id.",".$txt_mrr_no.",".$pi_id.",'".$user_id."','".$pc_date_time."')";
			//echo "20**".$field_array."<br>".$data_array;die;
		}
		
		//transaction table insert here START--------------------------------//cbo_uom
		$lot_count_rack_shelf_arr=explode("__",$lot_count_rack_shelf);
		
		$txt_return_qnty = str_replace("'","",$txt_return_qnty);
		//$transactionID = return_next_id("id", "inv_transaction", 1); 		
		$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);		
		$field_array_trans = "id,mst_id,company_id,prod_id,item_category,transaction_type,transaction_date,order_uom,order_qnty,cons_uom,cons_quantity,remarks,batch_lot,yarn_count,rack,self,stitch_length,store_id,inserted_by,insert_date";
 		$data_array_trans = "(".$transactionID.",".$id.",".$cbo_company_name.",".$txt_prod_id.",13,3,".$txt_return_date.",".$cbo_uom.",".$txt_return_qnty.",".$cbo_uom.",".$txt_return_qnty.",".$txt_remarks.",'".$lot_count_rack_shelf_arr[0]."','".$lot_count_rack_shelf_arr[1]."','".$lot_count_rack_shelf_arr[2]."','".$lot_count_rack_shelf_arr[3]."','".$lot_count_rack_shelf_arr[4]."','".$mrr_store_id."','".$user_id."','".$pc_date_time."')"; 
		
		//echo $field_array."<br>".$data_array;die;
		//$transID = sql_insert("inv_transaction",$field_array_trans,$data_array_trans,1);
		//transaction table insert here END ---------------------------------//
		 
		//adjust product master table START-------------------------------------//
		$sql = sql_select("select product_name_details,last_purchased_qnty,current_stock from product_details_master where id=$txt_prod_id");
		$presentStock=$available_qnty=0;
		$product_name_details="";
		foreach($sql as $result)
		{
			$presentStock			=$result[csf("current_stock")];
			$product_name_details 	=$result[csf("product_name_details")];
			$available_qnty			=$result[csf("available_qnty")];	
		}
		$nowStock 		= $presentStock-$txt_return_qnty;
		$available_qnty = $available_qnty-$txt_return_qnty;
			
		$field_array_prod="last_issued_qnty*current_stock*available_qnty*updated_by*update_date";
		$data_array_prod=$txt_return_qnty."*".$nowStock."*".$available_qnty."*'".$user_id."'*'".$pc_date_time."'";
		
		
		
		//order_wise_pro_detail table insert here
		
		$txt_break_qnty=str_replace("'","",$txt_break_qnty);
		$txt_break_roll=str_replace("'","",$txt_break_roll);
		$txt_order_id_all=str_replace("'","",$txt_order_id_all);
		$ordr_wise_rtn_qnty_arr=explode("_",$txt_break_qnty);
		$ordr_wise_rtn_roll_arr=explode("_",$txt_break_roll);
		$ordr_id_arr=explode(",",$txt_order_id_all);
		//$proportion_id = return_next_id("id", "order_wise_pro_details", 1);
		
		//$roll_id = return_next_id("id", "pro_roll_details", 1);
		
		$field_array_proportion="id,trans_id,trans_type,entry_form,po_breakdown_id,prod_id,quantity,inserted_by,insert_date";
	
		 
		$data_array_proportion=$data_array_roll="";
		if(!empty($txt_break_qnty))
		{
			foreach($ordr_wise_rtn_qnty_arr as $val)
			{
				$order_qnty_arr=explode("**",$val);
				if($order_qnty_arr[1]>0)
				{
					$proportion_id = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					if($data_array_proportion!="") $data_array_proportion.=", ";
					$data_array_proportion.="(".$proportion_id.",".$transactionID.",3,45,".$order_qnty_arr[0].",".$txt_prod_id.",".$order_qnty_arr[1].",'".$user_id."','".$pc_date_time."')";
					//$proportion_id++;
				}
			}
			
			if($variable_setting_production==1)
			{
				$field_array_roll="id,mst_id,dtls_id,po_breakdown_id,entry_form,roll_no,qnty,inserted_by,insert_date";
				
				foreach($ordr_wise_rtn_roll_arr as $val)
				{
					$order_roll_arr=explode("**",$val);
					
					if($order_roll_arr[1]>0)
					{
						$roll_id = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
						if($data_array_roll!="") $data_array_roll.=", ";
						$data_array_roll.="(".$roll_id.",".$id.",".$transactionID.",".$order_roll_arr[0].",45,".$order_roll_arr[1].",".$order_roll_arr[2].",'".$user_id."','".$pc_date_time."')";
						//$roll_id++;
					}
				}
			}
		}
		
		
		
		
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
		$field_array = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,inserted_by,insert_date";
		$data_array = "(".$mrrWiseIsID.",".$hidden_receive_trans_id.",".$transactionID.",45,".$txt_prod_id.",".$txt_return_qnty.",'".$user_id."','".$pc_date_time."')";
		$update_array = "balance_qnty*updated_by*update_date";
		$sql_receive=sql_select("select id,balance_qnty from inv_transaction where id=$hidden_receive_trans_id and balance_qnty>0");
		$balance_qnty = $sql_receive[0][csf("balance_qnty")];
		$issueQntyBalance=$balance_qnty-$txt_return_qnty;
		$update_data="'".$issueQntyBalance."'*'".$user_id."'*'".$pc_date_time."'";
		
 		  
		$rID=$transID=$prodUpdate=$propoId=$rollId=$mrrWiseIssueID=$upTrID=true;
		if(str_replace("'","",$txt_return_no)!="")
		{
			$rID=sql_update("inv_issue_master",$field_array_mst,$data_array_mst,"id",$id,1);
		}
		else
		{
			$rID=sql_insert("inv_issue_master",$field_array_mst,$data_array_mst,1);
		}
		$transID = sql_insert("inv_transaction",$field_array_trans,$data_array_trans,1);
		$prodUpdate = sql_update("product_details_master",$field_array_prod,$data_array_prod,"id",$txt_prod_id,1);
		if($data_array_proportion!="")
		{
			$propoId=sql_insert("order_wise_pro_details",$field_array_proportion,$data_array_proportion,1);
			if($variable_setting_production==1)
			{
				$rollId=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,1);
			}
		}
		//mrr wise issue data insert here----------------------------//
		if($data_array!="")
		{		
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array,$data_array,1);
		}
		
		//transaction table stock update here------------------------//
		if($balance_qnty>0)
		{
 			$upTrID=sql_update("inv_transaction",$update_array,$update_data,"id",$hidden_receive_trans_id,1);
		}
		
		
		if($db_type==0)
		{
			if( $rID && $transID && $prodUpdate && $propoId && $rollId && $mrrWiseIssueID && $upTrID)
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
			if( $rID && $transID && $prodUpdate && $propoId && $rollId && $mrrWiseIssueID && $upTrID)
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
			echo "30**Return Quantity Not Over Global Stock.";disconnect($con);
			die;
		}
		
		
		//****************************************** BEFORE ENTRY ADJUST START *****************************************//
		//product master table information
		//before stock update
		$sql = sql_select( "select a.id,a.current_stock, b.cons_quantity from product_details_master a, inv_transaction b where a.id=b.prod_id and a.id=$before_prod_id and b.id=$update_id and a.item_category_id=13 and b.item_category=13 and b.transaction_type=3" );
		$before_prod_id=$before_issue_qnty=$before_stock_qnty=0;
		foreach($sql as $result)
		{
			$before_prod_id 	= $result[csf("id")];
 			$before_stock_qnty 	= $result[csf("current_stock")];
			//before quantity and stock value
			$before_issue_qnty	= $result[csf("cons_quantity")];
		}
		
		//current product ID
		$txt_prod_id = str_replace("'","",$txt_prod_id);
		$txt_return_qnty = str_replace("'","",$txt_return_qnty);
		$before_prod_id= str_replace("'","",$before_prod_id);
		$curr_stock_qnty=return_field_value("current_stock","product_details_master","id=$txt_prod_id and item_category_id=13");
		
 		//echo $receive_purpose;die;
		//weighted and average rate START here------------------------//
		//product master table data UPDATE START----------------------//		
		$update_array_prod= "last_issued_qnty*current_stock*updated_by*update_date";
		if($before_prod_id==$txt_prod_id)
		{
			$adj_stock_qnty = $curr_stock_qnty+$before_issue_qnty-$txt_return_qnty; // CurrentStock + Before Issue Qnty - Current Issue Qnty
			
			if($adj_stock_qnty<0) //Aziz
			{
				echo "30**Stock cannot be less than zero.";disconnect($con);die;
			}
			 
			$data_array_prod= $txt_return_qnty."*".$adj_stock_qnty."*'".$user_id."'*'".$pc_date_time."'";
			//if($query1) echo "20**OK"; else echo "20**ERROR";die;
			//now current stock
			$curr_stock_qnty 	= $adj_stock_qnty;
		}
		else
		{
			$updateIdprod_array = $update_dataProd = array();
			//before product adjust
			$adj_before_stock_qnty 	= $before_stock_qnty+$prev_return_qnty; // CurrentStock + Before Issue Qnty
			if($adj_before_stock_qnty<0) //Aziz
			{
				echo "30**Stock cannot be less than zero.";disconnect($con);die;
			}
			 
			$updateIdprod_array[]=$before_prod_id;
			$update_dataProd[$before_prod_id]=explode("*",("".$prev_return_qnty."*".$adj_before_stock_qnty."*'".$user_id."'*'".$pc_date_time."'"));
			
			//current product adjust
			$adj_curr_stock_qnty = 	$curr_stock_qnty-$txt_return_qnty; // CurrentStock + Before Issue Qnty
			
			$updateIdprod_array[]=$txt_prod_id;
			$update_dataProd[$txt_prod_id]=explode("*",("".$txt_return_qnty."*".$adj_curr_stock_qnty."*'".$user_id."'*'".$pc_date_time."'"));
			//$query1=execute_query(bulk_update_sql_statement("product_details_master","id",$update_array_prod,$update_dataProd,$updateIdprod_array));
			
			//now current stock
			$curr_stock_qnty 	= $adj_curr_stock_qnty;
		}
		
		
		$lot_count_rack_shelf_arr=explode("__",$lot_count_rack_shelf);
  		$id=return_field_value("id","inv_issue_master","id=$issue_mst_id");
		//yarn master table UPDATE here START----------------------//	

		/*#### Stop not eligible field from update operation start ####*/
		//company_id*received_id*received_mrr_no*
		//$cbo_company_name."*".$txt_received_id."*".$txt_mrr_no."*'".
		/*#### Stop not eligible field from update operation end ####*/

		$field_array_mst="issue_date*pi_id*updated_by*update_date";
		$data_array_mst=$txt_return_date."*".$pi_id."*".$user_id."*'".$pc_date_time."'";
		
 		$field_array_trans="company_id*prod_id*item_category*transaction_type*transaction_date*order_uom*order_qnty*cons_uom*cons_quantity*remarks*batch_lot*yarn_count*rack*self*stitch_length*updated_by*update_date";
 		$data_array_trans= "".$cbo_company_name."*".$txt_prod_id."*13*3*".$txt_return_date."*".$cbo_uom."*".$txt_return_qnty."*".$cbo_uom."*".$txt_return_qnty."*".$txt_remarks."*'".$lot_count_rack_shelf_arr[0]."'*'".$lot_count_rack_shelf_arr[1]."'*'".$lot_count_rack_shelf_arr[2]."'*'".$lot_count_rack_shelf_arr[3]."'*'".$lot_count_rack_shelf_arr[4]."'*'".$user_id."'*'".$pc_date_time."'"; 
		//echo $field_array."<br>".$data_array;die;
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
		$field_array_mrr = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,inserted_by,insert_date";
		$data_array_mrr = "(".$mrrWiseIsID.",".$hidden_receive_trans_id.",".$update_id.",45,".$txt_prod_id.",".$txt_return_qnty.",'".$user_id."','".$pc_date_time."')";
		
		//order_wise_pro_detail table insert here
		
		$txt_break_qnty=str_replace("'","",$txt_break_qnty);
		$txt_break_roll=str_replace("'","",$txt_break_roll);
		$txt_order_id_all=str_replace("'","",$txt_order_id_all);
		$ordr_wise_rtn_qnty_arr=explode("_",$txt_break_qnty);
		$ordr_wise_rtn_roll_arr=explode("_",$txt_break_roll);
		$ordr_id_arr=explode(",",$txt_order_id_all);
		//$proportion_id = return_next_id("id", "order_wise_pro_details", 1);
		//$roll_id = return_next_id("id", "pro_roll_details", 1);
		$field_array_proportion="id,trans_id,trans_type,entry_form,po_breakdown_id,prod_id,quantity,inserted_by,insert_date";
		 
		$data_array_proportion=$data_array_roll="";
		if(!empty($txt_break_qnty))
		{
			foreach($ordr_wise_rtn_qnty_arr as $val)
			{
				$order_qnty_arr=explode("**",$val);
				if($order_qnty_arr[1]>0)
				{
					$proportion_id = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					if($data_array_proportion!="") $data_array_proportion.=", ";
					$data_array_proportion.="(".$proportion_id.",".$update_id.",3,45,".$order_qnty_arr[0].",".$txt_prod_id.",".$order_qnty_arr[1].",'".$user_id."','".$pc_date_time."')";
					//$proportion_id++;
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
						$roll_id = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
						if($data_array_roll!="") $data_array_roll.=", ";
						$data_array_roll.="(".$roll_id.",".$id.",".$update_id.",".$order_roll_arr[0].",45,".$order_roll_arr[1].",".$order_roll_arr[2].",'".$user_id."','".$pc_date_time."')";
						//$roll_id++;
					}
				}
			}
		}
		
		
 		$query1=$query2=$query3=$query4=$query5=$rID=$transID=$propoId=$rollId=$mrrWiseIssueID=$upTrID=true;
		
		
		
		if($before_prod_id==$txt_prod_id)
		{
			$query1= sql_update("product_details_master",$update_array_prod,$data_array_prod,"id",$before_prod_id,1);
			$rID=sql_update("inv_issue_master",$field_array_mst,$data_array_mst,"id",$id,1);
			$transID = sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_id,1); 
			if($hidden_receive_trans_id>0)
			{
				$query3 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_id and entry_form=45");
				$query2 = sql_update("inv_transaction",$update_array_trans,$update_data_trans,"id",$hidden_receive_trans_id,1); 
				$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
			}
			
			
			if($data_array_proportion!="")
			{
				$query4 = execute_query("DELETE FROM order_wise_pro_details WHERE trans_id=$update_id and entry_form=45");
				$propoId=sql_insert("order_wise_pro_details",$field_array_proportion,$data_array_proportion,1);
				if($variable_setting_production==1)
				{
					$rollId=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,1);
					$query5 = execute_query("DELETE FROM pro_roll_details WHERE dtls_id=$update_id and entry_form=45");
				}
			}
			
		}
		else
		{
			$query1=execute_query(bulk_update_sql_statement("product_details_master","id",$update_array_prod,$update_dataProd,$updateIdprod_array));
			
			$rID=sql_update("inv_issue_master",$field_array_mst,$data_array_mst,"id",$id,1);
			$transID = sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_id,1);
			if($hidden_receive_trans_id>0)
			{
				$query3 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_id and entry_form=45");
				$query2 = sql_update("inv_transaction",$update_array_trans,$update_data_trans,"id",$hidden_receive_trans_id,1); 
				$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
			}
			if($before_receive_trans_id>0)
			{
				$upTrID = sql_update("inv_transaction",$update_array_trans,$update_data_before_trans,"id",$before_receive_trans_id,1);
			}
			if($data_array_proportion!="")
			{
				$query4 = execute_query("DELETE FROM order_wise_pro_details WHERE trans_id=$update_id and entry_form=45");
				$propoId=sql_insert("order_wise_pro_details",$field_array_proportion,$data_array_proportion,1);
				if($variable_setting_production==1)
				{
					$query5 = execute_query("DELETE FROM pro_roll_details WHERE dtls_id=$update_id and entry_form=45");
					$rollId=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,1);
				}
			}
		}
		if($db_type==0)
		{
			if($query1 && $query2 && $query3 && $query4 && $query5 && $rID && $transID && $propoId && $rollId && $mrrWiseIssueID && $upTrID)
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
			if($query1 && $query2 && $query3 && $query4 && $query5 && $rID && $transID && $propoId && $rollId && $mrrWiseIssueID && $upTrID)
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
		//alert(mrr)
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
                            $search_by = array(1=>'Return Number');
							//$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 140, $search_by,"",0, "--Select--", "",1,0 );
                        ?>
                    </td>
                    <td width="" align="center" id="search_by_td">				
                        <input type="text" style="width:200px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td>    
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" />&nbsp;&nbsp;&nbsp;
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" />
                    </td> 
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_return_search_list_view', 'search_div', 'grey_fab_receive_rtn_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="5">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here-->
                     <input type="hidden" id="hidden_return_number" value="" />
                    <!-- -END -->
                </td>
            </tr>    
            </tbody>
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
	
	$sql = "select a.id, $year_field a.issue_number_prefix_num, a.issue_number, a.company_id, a.supplier_id,a.issue_date, a.item_category, a.received_id,a.received_mrr_no, sum(b.cons_quantity)as cons_quantity,a.is_posted_account
			from inv_issue_master a, inv_transaction b
			where a.id=b.mst_id and b.transaction_type=3 and a.status_active=1 and a.item_category=13 and b.item_category=13 and a.entry_form=45 $sql_cond group by a.id, a.issue_number_prefix_num, a.issue_number, a.company_id, a.supplier_id, a.issue_date, a.item_category, a.received_id, a.received_mrr_no, a.insert_date, a.is_posted_account order by a.id";
	//echo $sql;
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$arr=array(2=>$company_arr);
 	echo create_list_view("list_view", "Return No, Year, Company Name, Return Date,Return Qty,Receive MRR","70,60,150,80,100,150","700","230",0, $sql , "js_set_value", "id,issue_number,received_id,is_posted_account", "", 1, "0,0,company_id,0,0,0", $arr, "issue_number_prefix_num,year,company_id,issue_date,cons_quantity,received_mrr_no","","",'0,0,0,3,1,0') ;	
 	exit();
}

 

if($action=="populate_master_from_data")
{  
	$sql = "select id,issue_number,company_id,supplier_id,issue_date,item_category,received_id,received_mrr_no,pi_id   
			from inv_issue_master 
			where id='$data' and item_category=13 and entry_form=45";
	//echo $sql;
	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#txt_return_no').val('".$row[csf("issue_number")]."');\n";
		echo "$('#issue_mst_id').val('".$row[csf("id")]."');\n";
 		echo "$('#cbo_company_name').val(".$row[csf("company_id")].");\n";
		echo "$('#txt_return_date').val('".change_date_format($row[csf("issue_date")])."');\n";
		echo "$('#txt_mrr_no').val('".$row[csf("received_mrr_no")]."');\n";
		echo "$('#txt_received_id').val('".$row[csf("received_id")]."');\n";
		
		echo "$('#cbo_company_name').attr('disabled','disabled');\n";
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
		//right side list view
		//echo "show_list_view('".$row[csf("received_id")]."','show_product_listview','list_product_container','requires/grey_fab_receive_rtn_controller','');\n";
   	}	
	exit();	
}



if($action=="show_dtls_list_view")
{
	
	$sql = "select a.id as issue_id, a.issue_number, a.company_id, a.supplier_id, a.issue_date, a.item_category, a.received_id, a.received_mrr_no, b.id as trans_id, b.cons_quantity, b.cons_uom, b.cons_rate, b.cons_amount, c.product_name_details, c.id as prod_id   
			from inv_issue_master a, inv_transaction b left join product_details_master c on b.prod_id=c.id
			where a.id=b.mst_id and b.item_category=13 and b.transaction_type=3 and a.id=$data";
	//echo $sql;
	$result = sql_select($sql);
	$i=1;
	$rettotalQnty=0;
	$rcvtotalQnty=0;
	$totalAmount=0;
	?> 
     	<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" style="width:800px" >
        	<thead>
            	<tr>
                	<th>SL</th>
                    <th>Return No</th>
                    <th>Item Description</th>
                    <th>Product ID</th>
                    <th>Received No</th>
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
					$sqlTr = sql_select("select b.balance_qnty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id=".$row[csf("prod_id")]." and b.item_category=13 and b.transaction_type in (1,4) and a.id='".$row[csf("received_id")]."'");
					}
					$rcvQnty = $sqlTr[0][csf('balance_qnty')];
					
					$rettotalQnty +=$row[csf("cons_quantity")];
					//$rcvtotalQnty +=$rcvQnty;
					$totalAmount +=$row[csf("cons_amount")];
					
					
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("trans_id")];?>,<? echo $rcvQnty;?>,<? echo $row[csf("issue_id")];?>","child_form_input_data","requires/grey_fab_receive_rtn_controller")' style="cursor:pointer" >
                        <td width="30"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf("issue_number")]; ?></p></td>
                        <td width="200"><p><? echo $row[csf("product_name_details")]; ?></p></td>
                        <td width="70"><p><? echo $row[csf("prod_id")]; ?></p></td>
                        <td width="100"><p><? echo $row[csf("received_mrr_no")]; ?></p></td>
                        <td width="70" align="right"><p><? echo number_format($row[csf("cons_quantity")],2); ?></p></td>
					</tr>
					<? 
					$i++; 
				} 
				?>
                	<tfoot>
                        <th colspan="5">Total</th>                         
                        <th><? echo number_format($rettotalQnty,2); ?></th> 
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
	
 	$sql = "select b.id as prod_id, b.product_name_details, b.current_stock, b.gsm, b.dia_width, a.id as tr_id, a.cons_uom, a.cons_quantity, a.cons_amount, a.remarks
			from inv_transaction a, product_details_master b
 			where a.id=$data and a.status_active=1 and a.item_category=13 and transaction_type=3 and a.prod_id=b.id and b.status_active=1";
 	//echo $sql;die;
	$result = sql_select($sql);
	foreach($result as $row)
	{
 		echo "$('#txt_item_description').val('".$row[csf("product_name_details")]."');\n";
		echo "$('#txt_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#before_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#txt_gsm').val('".$row[csf("gsm")]."');\n";
		echo "$('#txt_dia').val('".$row[csf("dia_width")]."');\n";
		echo "$('#cbo_store_name').val('".$row[csf("store_id")]."');\n";
		echo "$('#prev_return_qnty').val('".number_format($row[csf("cons_quantity")],2)."');\n";	
		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		$rcvQnty = $rcvQnty+$row[csf("cons_quantity")];
		echo "$('#txt_receive_qnty').val('".number_format($rcvQnty,2)."');\n";
		echo "$('#cbo_uom').val(".$row[csf("cons_uom")].");\n";
		$recv_trans_id=return_field_value("recv_trans_id","inv_mrr_wise_issue_details","issue_trans_id='".$row[csf('tr_id')]."'","recv_trans_id" );
		if($recv_trans_id=="") $recv_trans_id=0;
		$booking_without_order=return_field_value("a.booking_without_order","inv_receive_master a,inv_transaction b","a.id=b.mst_id and b.id=$recv_trans_id","booking_without_order" );
		if($booking_without_order==1)
		{
			echo "$('#txt_return_qnty').removeAttr('placeholder').removeAttr('readonly').removeAttr('onDblClick').attr('placeholder','write');\n";
		}
		else
		{
			echo "$('#txt_return_qnty').removeAttr('placeholder').removeAttr('readonly').removeAttr('onDblClick').attr('placeholder','Double Click To Search').attr('readonly','').attr('onDblClick','openmypage_rtn_qty();');\n";
		}
		
		echo "$('#txt_return_qnty').val('".$row[csf("cons_quantity")]."');\n";
		
		if($db_type==2)
		{
			$lot_rack_self=return_field_value("(yarn_lot || '__' || yarn_count || '__' || rack || '__' || self || '__' || stitch_length) as rack_selt","pro_grey_prod_entry_dtls ","status_active=1 and trans_id='".$recv_trans_id."'","rack_selt" );
		}
		else if($db_type==0)
		{
			$lot_rack_self=return_field_value("concat(yarn_lot,'__',yarn_count,'__',rack,'__',self,'__',stitch_length) as rack_selt","pro_grey_prod_entry_dtls ","status_active=1 and trans_id='".$recv_trans_id."'","rack_selt" );
		}
		
		echo "$('#lot_count_rack_shelf').val('$lot_rack_self');\n";	

		
		
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
		if($variable_setting_production==1)
		{
			$roll_sql=sql_select("select po_breakdown_id, roll_no, qnty from  pro_roll_details where mst_id='$issue_id' and dtls_id='".$row[csf("tr_id")]."'");
			$roll_ref="";
			foreach($roll_sql as $row_roll)
			{
				if($roll_ref!="") $roll_ref .="_";
				$roll_ref .=$row_roll[csf("po_breakdown_id")]."**".$row_roll[csf("roll_no")]."**".$row_roll[csf("qnty")];
			}
		}
		
		echo "$('#txt_break_qnty').val('$po_wise_qnty');\n";
		echo "$('#txt_break_roll').val('$roll_ref');\n";
		echo "$('#txt_order_id_all').val('$po_id_all');\n";
		
		$yet_to_iss=number_format($receive_quantity-$cumilitive_rtn,2);
		$receive_quantity=number_format($receive_quantity,2);
		$cumilitive_rtn=number_format($cumilitive_rtn,2);
		echo "$('#txt_fabric_received').val('$receive_quantity');\n";	
		echo "$('#txt_cumulative_issued').val('$cumilitive_rtn');\n";
		echo "$('#txt_yet_to_issue').val('$yet_to_iss');\n";	
		echo "$('#hidden_receive_trans_id').val('$recv_trans_id');\n";
		echo "$('#before_receive_trans_id').val('$recv_trans_id');\n";	
		echo "$('#txt_global_stock').val('".number_format($row[csf("current_stock")],2)."');\n";
		echo "$('#update_id').val('".$row[csf("tr_id")]."');\n";
	}
	
 	echo "set_button_status(1, permission, 'fnc_yarn_receive_return_entry',1,1);\n";
	//echo "$('#tbl_master').find('input,select').attr('disabled', false);\n";
	//echo "disable_enable_fields( 'cbo_company_name*txt_mrr_no', 1, '', '');\n";
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
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_wopi_search_list_view', 'search_div', 'grey_fab_receive_rtn_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
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
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
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
			a.item_category_id = 13 and
			a.status_active=1 and a.is_deleted=0
			$sql_cond order by a.id";
	//echo $sql;
	$result = sql_select($sql);
	$arr=array(3=>$currency,4=>$source);
	
	echo  create_list_view("list_view", "PI No, LC ,Date, Currency, Source","150,200,100,100","750","230",0, $sql , "js_set_value", "id,pi_number", "", 1, "0,0,0,currency_id,source", $arr, "pi_number,lc_number,pi_date,currency_id,source", "",'','0,0,3,1,0') ;
	exit();	
}


if ($action=="yarn_receive_return_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	 $sql=" select id, issue_number, received_id, issue_date, supplier_id from  inv_issue_master where issue_number='$data[1]' and entry_form=45 and item_category=13 and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql);

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr = return_library_array("select id,country_name from lib_country","id","country_name");
	$receive_arr = return_library_array("select id,recv_number from inv_receive_master","id","recv_number");
	
	$sql_recv=" select id, knitting_source, knitting_company from  inv_receive_master where recv_number='".$receive_arr[$dataArray[0][csf('received_id')]]."' and entry_form=22 and item_category=13 and status_active=1 and is_deleted=0";
	$dataArray_recv=sql_select($sql_recv);
	$knitting_source=$dataArray_recv[0][csf('knitting_source')];
	$knitting_company=$dataArray_recv[0][csf('knitting_company')];
	if($knitting_source==1)
	{
		$supplier=$company_library[$knitting_company];	
	}
	else
	{
		$supplier=$supplier_library[$knitting_company];
	}
	?>
	<div style="width:900px;">
    <table width="880" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">  
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
					?>
						Plot No: <? echo $result['plot_no']; ?> 
						Level No: <? echo $result['level_no']?>
						Road No: <? echo $result['road_no']; ?> 
						Block No: <? echo $result['block_no'];?> 
						City No: <? echo $result['city'];?> 
						Zip Code: <? echo $result['zip_code']; ?> 
						Province No: <? echo $result['province'];?> 
						Country: <? echo $country_arr[$result['country_id']]; ?><br> 
						Email Address: <? echo $result['email'];?> 
						Website No: <? echo $result['website'];
					}
                ?> 
            </td>  
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><u>Knit Grey Fabric Receive Return</u></strong></td>
        </tr>
        <tr>
        	<td width="120"><strong>Return Number:</strong></td><td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
            <td width="110"><strong>Receive ID:</strong></td><td width="175px"><? echo $receive_arr[$dataArray[0][csf('received_id')]]; ?></td>
            <td width="100"><strong>Return To :</strong></td> <td width="175px"><? echo $supplier;//$supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
        </tr>
        <tr>
            <td width="110"><strong>Return Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
            <td colspan="4">&nbsp;</td>
        </tr>
    </table>
	<div style="width:100%;">
		<table align="right" cellspacing="0" width="880"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="50">SL</th>
                <th width="250" align="center">Item Description</th>
                <th width="70" align="center">UOM</th> 
                <th width="80" align="center">Return Qnty.</th>
                <th width="100" align="center">Store</th>
            </thead>
<?
	$mrr_no =$dataArray[0][csf('issue_number')];
	//$up_id =$data[1];
	$cond="";
	if($mrr_no!="") $cond .= " and c.issue_number='$mrr_no'";
	//if($up_id!="") $cond .= " and a.id='$up_id'";
	 $i=1;
 	$sql_dtls = "select b.id as prod_id, b.product_name_details, a.id as tr_id, a.store_id, a.cons_uom, a.cons_quantity
			from inv_transaction a, product_details_master b, inv_issue_master c
 			where c.id=a.mst_id and a.status_active=1 and a.company_id='$data[0]' and c.issue_number='$data[1]' and a.item_category=13 and transaction_type=3 and a.prod_id=b.id and b.status_active=1 ";
			$sql_result= sql_select($sql_dtls);
			
	foreach($sql_result as $row)
	{
		if ($i%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
		$qnty+=$row[csf('cons_quantity')];
		?>

			<tr bgcolor="<? echo $bgcolor; ?>">
                <td><? echo $i; ?></td>
                <td><? echo $row[csf('product_name_details')]; ?></td>
                <td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
                <td align="right"><? echo $row[csf('cons_quantity')]; ?></td>
                <td><? echo $store_library[$row[csf('store_id')]]; ?></td>
			</tr>
	<?
    $i++;
    }
    ?>
        	<tr> 
                <td align="right" colspan="3" >Total</td>
                <td align="right"><? echo number_format($qnty,0,'',','); ?></td>
                <td align="right">&nbsp;</td>
			</tr>
		</table>
        <br>
		 <?
           echo signature_table(85, $data[0], "880px");
         ?>
      </div>
	</div> 
	<?
    exit();
}
?>
