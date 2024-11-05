<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------- Start-------------------------------------//
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  
	exit();
}

if($action=="lcSc_popup_search")
{
	echo load_html_head_contents("Export Information Entry Form", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
	?>
     
	<script>
	
		function js_set_value(data)
		{
			var data=data.split("_");
			
			$('#hidden_lcSc_id').val(data[0]);
			$('#is_lcSc').val(data[1]);
			$('#company_id').val(data[2]);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:740px;">
	<form name="searchexportinformationfrm"  id="searchexportinformationfrm">
		<fieldset style="width:720px;">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="700" class="rpt_table" border="1" rules="all">
                <thead>
                    <th>Company</th>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th>Enter</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="hidden_lcSc_id" id="hidden_lcSc_id" value="" />
                        <input type="hidden" name="is_lcSc" id="is_lcSc" value="" />
                        <input type="hidden" name="company_id" id="company_id" value="" />
                    </th>
                </thead>
                <tr class="general">
                    <td>
                        <?
                            echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--- Select Company ---", 0, "load_drop_down( 'export_information_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td_id' );" );
                        ?>                        
                    </td>
                    <td id="buyer_td_id"> 
						<?
                           echo create_drop_down( "cbo_buyer_name", 162, $blank_array,"", 1, "-- Select Buyer --", 0, "" );
                        ?>
                    </td>
                    <td> 
                        <?
                            $arr=array(1=>'LC NO',2=>'SC No');
                            echo create_drop_down( "cbo_search_by", 100, $arr,"", 0, "", 0, "" );
                        ?> 
                    </td>						
                    <td>
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>                       
                     <td>
                        <input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'lcSc_search_list_view', 'search_div', 'export_information_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                     </td>
                </tr>
           </table>
            <div style="width:100%; margin-top:10px" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit(); 
 
}

if($action=="lcSc_search_list_view")
{
	$data=explode('**',$data); 
	
	$company_id=$data[0];
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$data[1]";
	}
	
	//if($data[1]==0) $buyer_id="%%"; else $buyer_id=$data[1];
	$search_by=$data[2];
	$search_text="%".trim($data[3])."%";
	
	if($db_type==0) $year_field="YEAR(insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	
	if($company_id==0){ echo "Please Select Company first"; die; }
	
	if($search_by==1)
	{
		$sql = "select id, beneficiary_name, $year_field export_lc_prefix_number as system_num, export_lc_system_id as system_id, export_lc_no as lc_sc, internal_file_no, beneficiary_name, buyer_name, lien_bank, 1 as type from com_export_lc where status_active=1 and is_deleted=0 and beneficiary_name=$company_id and export_lc_no like '$search_text' $buyer_id_cond order by id"; //and buyer_name like '$buyer_id'
		$lc_sc="LC No";
	}
	else
	{ 
		$sql = "select id, beneficiary_name, $year_field contact_prefix_number as system_num, contact_system_id as system_id, contract_no as lc_sc, internal_file_no, beneficiary_name, buyer_name, lien_bank,2 as type from com_sales_contract where status_active=1 and is_deleted=0 and beneficiary_name=$company_id and contract_no like '$search_text' $buyer_id_cond order by id";
		
		$lc_sc="SC No";
	}
	//echo $sql;
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	$arr=array (4=>$comp,5=>$buyer_arr,6=>$bank_arr);
	
	echo  create_list_view("list_view", "Year,System ID,File No,$lc_sc,Benificiary,Buyer,Lien Bank", "55,60,90,110,80,80,110","700","280",0, $sql, "js_set_value", "id,type,beneficiary_name", "", 1, "0,0,0,0,beneficiary_name,buyer_name,lien_bank", $arr , "year,system_num,internal_file_no,lc_sc,beneficiary_name,buyer_name,lien_bank", "",'','0,0,0,0,0,0,0');
	exit();
}

if($action=="populate_data_from_lcSc")
{
	$explode_data = explode("**",$data);
	$lcSc_id=$explode_data[0];
	$is_lcSc=$explode_data[1];
	$invoice_id=$explode_data[2];
	$company_id=str_replace("'","",$explode_data[3]);
	
	
	$variable_setting=return_field_value("cost_heads_status","variable_settings_commercial","company_name='".$company_id."' and  variable_list=18","cost_heads_status");
	
	if($variable_setting==1) $readonly_status=" readonly "; else $disable_status=" ";
	
	$exFactoryArr=return_library_array("select sum(ex_factory_qnty) as qnty,po_break_down_id from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id","po_break_down_id","qnty");
	$dealiingMarchantArr=return_library_array("select dealing_marchant,job_no from wo_po_details_master","job_no","dealing_marchant");
	$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
	
	if($db_type==0)
	{
		$articleNoArr=return_library_array("select group_concat(distinct(article_number)) as article_number, po_break_down_id from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and article_number<>'' group by po_break_down_id","po_break_down_id","article_number");
	}
	else
	{
		$articleNoArr=return_library_array("select LISTAGG(cast(article_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY article_number) as article_number, po_break_down_id from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and article_number is not null group by po_break_down_id","po_break_down_id","article_number");	
	}
	
	$po_invoice_data_array=array();
	$invoice_sql="SELECT a.po_breakdown_id, sum(a.current_invoice_qnty) as current_invoice_qnty, sum(a.current_invoice_value) as current_invoice_value FROM com_export_invoice_ship_dtls a, com_export_invoice_ship_mst b where a.mst_id=b.id and b.is_lc='$is_lcSc' and b.lc_sc_id='$lcSc_id' and a.status_active=1 and a.is_deleted=0 group by a.po_breakdown_id";
	$data_array_invoice=sql_select($invoice_sql);
	foreach($data_array_invoice as $row)
	{
		$po_invoice_data_array[$row[csf('po_breakdown_id')]]['qnty']=$row[csf('current_invoice_qnty')];
		$po_invoice_data_array[$row[csf('po_breakdown_id')]]['val']=$row[csf('current_invoice_value')];
	}
		
	if($is_lcSc== '1')
	{
		$sql= "SELECT id, export_lc_no as lc_sc, lien_bank, tolerance, applicant_name, beneficiary_name, buyer_name, shipping_mode, inco_term, inco_term_place, port_of_entry, port_of_loading, port_of_discharge, internal_file_no FROM com_export_lc where id= $lcSc_id";
		
		if($invoice_id==0 || $invoice_id=="")
		{
			$order_sql="SELECT a.id as order_id, a.job_no_mst, a.po_number, a.po_quantity, a.pub_shipment_date, b.attached_qnty, b.attached_rate, b.attached_value FROM com_export_lc_order_info b, wo_po_break_down a where b.com_export_lc_id=$lcSc_id and b.wo_po_break_down_id=a.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0"; 
		}
		else
		{
			$order_sql="SELECT a.id as order_id, a.job_no_mst, a.po_number, a.po_quantity, a.pub_shipment_date, b.attached_qnty, b.attached_rate, b.attached_value, c.id as dtls_id, c.mst_id, c.current_invoice_rate as rate, c.current_invoice_qnty, c.current_invoice_value, c.production_source, c.color_size_rate_data, c.actual_po_infos FROM wo_po_break_down a, com_export_lc_order_info b left join com_export_invoice_ship_dtls c on c.po_breakdown_id=b.wo_po_break_down_id and c.mst_id=$invoice_id and c.status_active=1 and c.is_deleted=0 where b.com_export_lc_id=$lcSc_id and b.wo_po_break_down_id=a.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0";
		}
	}
	else if($is_lcSc=='2')
	{
		$sql= "SELECT id, contract_no as lc_sc ,tolerance, lien_bank, applicant_name, beneficiary_name, buyer_name, shipping_mode, inco_term, inco_term_place, port_of_entry, port_of_loading, port_of_discharge, internal_file_no FROM com_sales_contract where id=$lcSc_id";
		
		if($invoice_id==0 || $invoice_id=="")
		{
			$order_sql="SELECT a.id as order_id, a.job_no_mst, a.po_number, a.po_quantity, a.pub_shipment_date, b.attached_qnty, b.attached_rate, b.attached_value FROM com_sales_contract_order_info b, wo_po_break_down a where b.com_sales_contract_id=$lcSc_id and b.wo_po_break_down_id=a.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		}
		else
		{
			$order_sql="SELECT a.id as order_id, a.job_no_mst, a.po_number, a.po_quantity, a.pub_shipment_date, b.attached_qnty, b.attached_rate, b.attached_value, c.id as dtls_id, c.mst_id, c.current_invoice_rate as rate, c.current_invoice_qnty, c.current_invoice_value, c.production_source, c.color_size_rate_data, c.actual_po_infos FROM wo_po_break_down a, com_sales_contract_order_info b left join com_export_invoice_ship_dtls c on c.po_breakdown_id=b.wo_po_break_down_id and c.mst_id=$invoice_id and c.status_active=1 and c.is_deleted=0 where b.com_sales_contract_id=$lcSc_id and b.wo_po_break_down_id=a.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0";
		}
	}
	//echo $sql;die;
	//echo $order_sql;die;
	$data_array=sql_select($sql);
	$data_array_po_attached=sql_select($order_sql);
	
 	foreach ($data_array as $row)
	{
		$row_number = count($data_array_po_attached);
		
		echo "document.getElementById('cbo_beneficiary_name').value			= '".$row[csf("beneficiary_name")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value 				= '".$row[csf("buyer_name")]."';\n";
		echo "document.getElementById('cbo_lien_bank').value 				= '".$row[csf("lien_bank")]."';\n";
		echo "document.getElementById('cbo_applicant_name').value 			= '".$row[csf("applicant_name")]."';\n";
		echo "document.getElementById('internal_file_no').value 			= '".$row[csf("internal_file_no")]."';\n";
		echo "document.getElementById('tot_row').value 						= '".$row_number."';\n";
		echo "document.getElementById('inco_term').value					= '".$row[csf("inco_term")]."';\n";
		
		if($invoice_id==0)
		{
			echo "document.getElementById('txt_lc_sc_no').value 				= '".$row[csf("lc_sc")]."';\n";
			echo "document.getElementById('lc_sc_id').value 					= '".$row[csf("id")]."';\n";
			echo "document.getElementById('is_lc_sc').value 					= '".$is_lcSc."';\n";
			echo "document.getElementById('inco_term_place').value 				= '".$row[csf("inco_term_place")]."';\n";
			echo "document.getElementById('shipping_mode').value 				= '".$row[csf("shipping_mode")]."';\n";
			echo "document.getElementById('port_of_entry').value 				= '".$row[csf("port_of_entry")]."';\n";
			echo "document.getElementById('port_of_loading').value 				= '".$row[csf("port_of_loading")]."';\n";
			echo "document.getElementById('port_of_discharge').value 			= '".$row[csf("port_of_discharge")]."';\n";
			
			echo "reset_form('','','txt_invoice_val*txt_discount*txt_discount_ammount*txt_bonus*txt_bonus_ammount*txt_claim*txt_claim_ammount*txt_invo_qnty*txt_commission*txt_commission_amt*txt_other_discount*txt_other_discount_amt*txt_upcharge*txt_net_invo_val');\n";
		
		
		}
		
		$table=""; $i=1;
		if($row_number==0)
		{
			$table=$table.'<tr class="general"><td colspan="13" align="center">Order Details is Not Available For This LC/SC </td></tr>';
			echo "$('#txt_invoice_val').removeAttr('disabled','disabled')".";\n";
			echo "$('#txt_invo_qnty').removeAttr('disabled','disabled')".";\n";
		} 
		else
		{
			foreach ($data_array_po_attached as $slectResult)
			{
				$tolerance_order_qty = $slectResult[csf('attached_qnty')]+($row[csf("tolerance")]/100*$slectResult[csf('attached_qnty')]);
				$total_tolerance_order_qty+=$tolerance_order_qty ;
				
				if($invoice_id==0)
				{
					$unit_price=$slectResult[csf('attached_rate')];
				}
				else
				{
					if($slectResult[csf('current_invoice_qnty')] > 0)
					{
						$unit_price=$slectResult[csf('rate')];
					}
					else 
					{
						$unit_price=$slectResult[csf('attached_rate')];
					}
				}
				
				$shipment_date = change_date_format($slectResult[csf('pub_shipment_date')]);
				
				$dealing_merchant_id=$dealiingMarchantArr[$slectResult[csf('job_no_mst')]];
				$dealing_merchant=$dealing_merchant_array[$dealing_merchant_id];
				
				$article_no=$articleNoArr[$slectResult[csf('order_id')]];
				$article_no=implode(",",array_unique(explode(",",$article_no)));
				
				$cumu_qty = $po_invoice_data_array[$slectResult[csf('order_id')]]['qnty'];
				$cumu_val = $po_invoice_data_array[$slectResult[csf('order_id')]]['val'];
				
				$po_balance_qnty=$slectResult[csf('attached_qnty')]-$cumu_qty;
				
				$total_cumu_qty+=$cumu_qty;
                $total_cumu_val+=$cumu_val;
				
				$total_value+=$slectResult[csf('current_invoice_value')];
                $total_qty+=$slectResult[csf('current_invoice_qnty')];
				
				$colorSize_infos=$slectResult[csf('color_size_rate_data')];
				$act_po_infos=$slectResult[csf('actual_po_infos')];
				
				if($slectResult[csf('current_invoice_qnty')]>0) $invc_qnty=$slectResult[csf('current_invoice_qnty')]; else $invc_qnty='';
				
				$ex_factory_qnty=$exFactoryArr[$slectResult[csf('order_id')]];
				$table=$table.'<tr align="center" id="tr_'.$i.'"><td width="115"><font style="display:none">'.$slectResult[csf('po_number')].'</font>\n<input type="hidden" id="order_id_'.$i.'" value="'.$slectResult[csf('order_id')].'"  /><input type="hidden" id="actual_po_infos_'.$i.'" value="'.$act_po_infos.'" /><input type="text" id="order_no_'.$i.'"  value="'.$slectResult[csf('po_number')].'" class="text_boxes" style="width:100px" readonly ondblclick="pop_entry_actual_po('.$i.')" id="order_no_'.$i.'"  /></td><td width="95"><font style="display:none">'.$article_no.'</font>\n<input type="text" id="article_no_'.$i.'"  value="'.$article_no.'" class="text_boxes" style="width:80px" disabled/></td><td width="85"><input type="text" id="shipment_date_'.$i.'" value="'.$shipment_date.'" class="datepicker" style="width:70px" disabled /></td><td width="80"><input type="hidden" disabled  id="tollerence_order_qty_'.$i.'" value="'.$tolerance_order_qty.'" /><input type="text" disabled id="order_qty_'.$i.'" value="'.$slectResult[csf('attached_qnty')].'" class="text_boxes_numeric" style="width:65px;"/></td><td width="70"><input type="text"  id="order_rate_'.$i.'" value="'.$unit_price.'" class="text_boxes_numeric" style="width:57px;" onKeyUp="calculate_value_rate('.$i.')" '.$readonly_status.' /></td><td width="95"><input type="text" id="curr_invo_qty_'.$i.'" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_value_rate('.$i.')" value="'.$invc_qnty.'" ondblclick="openpage_colorSize('.$i.')" /><input type="hidden"  id="curr_hide_invo_qty_'.$i.'" value="'.$slectResult[csf('current_invoice_qnty')].'" /><input type="hidden" id="colorSize_infos_'.$i.'" value="'.$colorSize_infos.'" /></td><td width="95"><input name="text" type="text" id="curr_invo_val_'.$i.'" class="text_boxes_numeric" value="'.$slectResult[csf('current_invoice_value')].'" style="width:80px;" disabled /><input type="hidden" id="curr_hide_invo_val_'.$i.'" value="'.$slectResult[csf('current_invoice_value')].'" /></td><td width="90"><input type="text" id="cum_invo_qty_'.$i.'" value="'.$cumu_qty.'" disabled class="text_boxes_numeric" style="width:70px;background: #D0EFC2;"/><input type="hidden" id="hide_cum_invo_qty_'.$i.'" value="'.$cumu_qty.'" /></td><td width="85"><input type="text" id="po_bl_qty_'.$i.'" value="'.$po_balance_qnty.'" disabled class="text_boxes_numeric" style="width:70px;"/></td><td width="95"><input type="text" id="cum_invo_val_'.$i.'"  value="'.$cumu_val.'" disabled  class="text_boxes_numeric" style="width:80px;" /><input type="hidden" id="hide_cum_invo_val_'.$i.'" value="'.$cumu_val.'" /></td><td width="80"><input type="text" id="ex_factory_qty_'.$i.'" value="'.$ex_factory_qnty.'" disabled class="text_boxes_numeric" style="width:65px;"/></td><td width="105"><input type="text" title="'.$dealing_merchant.'" value="'.$dealing_merchant.'" disabled class="text_boxes" style="width:90px;"/></td><td>'.create_drop_down( "cbo_production_source_$i", 90, $knitting_source,'', 0, '', $slectResult[csf('production_source')], '','','1,3' ).'</td></tr>';
				
				$i++;
			}
			
			$table=$table.'<tr class="tbl_bottom"><td colspan="5"><input type="hidden" id="total_tolerence_order_qty" value="'.$total_tolerance_order_qty.'" />Total</td><td><input type="text" disabled id="total_current_invoice_qty" value="'.$total_qty.'" disabled class="text_boxes_numeric" style="width:80px;" /></td><td><input type="text" disabled  id="total_current_invoice_val" value="'.$total_value.'" disabled class="text_boxes_numeric" style="width:80px;" /></td><td colspan="6"></td></tr>';
			
			echo "$('#txt_invoice_val').attr('disabled','disabled')".";\n";
			echo "$('#txt_invo_qnty').attr('disabled','disabled')".";\n";
		}
		
		echo "$('#tbl_order_list tbody tr').remove();\n";
		echo "$('#order_details').html('".$table."')".";\n";
		echo "active_inactive();\n";
		//echo "var tableFilters = {col_1:'none',col_2:'none',col_3:'none',col_4:'none',col_5:'none',col_6:'none',col_7:'none',col_8:'none',col_9:'none',col_10:'none'};\n";
		//if($row_number>0) echo "setFilterGrid('tbl_order_list',-1,tableFilters);\n";
		if($row_number>0) echo "setFilterGrid('tbl_order_list',-1);\n";
	}
}

if($action=="invoice_popup_search")
{
	echo load_html_head_contents("Export Information Entry Form", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
	?>
     
	<script>
	
		function js_set_value(data)
		{
			//alert(data);
			var data_string=data.split('_');
			$('#hidden_invoice_id').val(data_string[0]);
			$('#company_id').val(data_string[1]);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:740px;">
	<form name="searchexportinformationfrm"  id="searchexportinformationfrm">
		<fieldset style="width:720px;">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="700" class="rpt_table" border="1" rules="all">
            <input type="hidden" name="hidden_invoice_id" id="hidden_invoice_id" value="" />
            <input type="hidden" name="company_id" id="company_id" value="" />
                <thead>
                	
                    <th>Company</th>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th>Enter Invoice No</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        
                    </th>
                </thead>
                <tr class="general">
                    <td>
                        <?
                            echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--- Select Company ---", 0, "load_drop_down( 'export_information_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td_id' );" );
                        ?>                        
                    </td>
                    <td id="buyer_td_id"> 
						<?
                           echo create_drop_down( "cbo_buyer_name", 162, $blank_array,"", 1, "-- Select Buyer --", 0, "" );
                        ?>
                    </td>
                    <td> 
                        <?
                            $arr=array(1=>'Invoice NO');
                            echo create_drop_down( "cbo_search_by", 110, $arr,"", 0, "", 0, "" );
                        ?> 
                    </td>						
                    <td>
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>                       
                     <td>
                        <input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'invoice_search_list_view', 'search_div', 'export_information_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                     </td>
                </tr>
           </table>
            <div style="width:100%; margin-top:10px" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit(); 
}

if($action=="invoice_search_list_view")
{
	$data=explode('**',$data); 
	$company_id=$data[0];
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_id=$data[1]";
	}
	//$buyer_id=$data[1];
	$search_by=$data[2];
	$search_text="%".trim($data[3])."%";
	
	if($company_id==0){ echo "Please Select Company first"; die; }
	
	$sql = "select id, benificiary_id, buyer_id, invoice_no, invoice_date, is_lc, lc_sc_id, invoice_value from com_export_invoice_ship_mst where status_active=1 and is_deleted=0 and benificiary_id=$company_id and invoice_no like '$search_text' $buyer_id_cond";// and buyer_id=$buyer_id
	
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$lc_arr=return_library_array( "select id, export_lc_no from com_export_lc",'id','export_lc_no');
	$sc_arr=return_library_array( "select id, contract_no from com_sales_contract",'id','contract_no');
	?>
	<table width="720" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead> 
            <th width="40">SL</th>
            <th width="70">Company</th>
            <th width="70">Buyer</th>
            <th width="130">Invoice No</th>
            <th width="80">Invoice Date</th>
            <th width="130">LC/SC No</th>
            <th width="60">LC/SC</th>
            <th>Net Invoice Value</th>
        </thead>
     </table>
     <div style="width:720px; overflow-y:scroll; max-height:280px">  
     	<table width="702" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="list_view"> 
		<?
			$data_array=sql_select($sql);
            $i = 1; 
            foreach($data_array as $row)
            { 
                if ($i%2==0)  
                    $bgcolor="#FFFFFF";
                else
                    $bgcolor="#E9F3FF";	
				
				if($row[csf('is_lc')]==1)
				{
					$lc_sc_no=$lc_arr[$row[csf('lc_sc_id')]];//return_field_value("export_lc_no","com_export_lc","id=".$row[csf('lc_sc_id')]);
					$is_lc_sc="LC";
				}
				else
				{
					$lc_sc_no=$sc_arr[$row[csf('lc_sc_id')]];//return_field_value("contract_no","com_sales_contract","id=".$row[csf('lc_sc_id')]);
					$is_lc_sc="SC";
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value( '<? echo $row[csf('id')]; ?>_<? echo $row[csf('benificiary_id')]; ?>');" >                	<td width="40"><? echo $i; ?></td>
					<td width="70"><? echo $comp[$row[csf('benificiary_id')]]; ?></td>
					<td width="70"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
                    <td width="130"><p><? echo $row[csf('invoice_no')]; ?></p></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('invoice_date')]); ?></td>
                    <td width="130"><p><? echo $lc_sc_no; ?></p></td>
                    <td width="60" align="center"><? echo $is_lc_sc; ?></td>
					<td align="right"><? echo number_format($row[csf('invoice_value')],2); ?></td>
				</tr>
            <?
			$i++;
            }		
			?>
		</table>
    </div>
<?	
	exit();
}

if($action=="populate_data_from_invoice")
{
	$sql="SELECT id, invoice_no, invoice_date, buyer_id, location_id, is_lc, lc_sc_id, exp_form_no, exp_form_date, discount_in_percent, discount_ammount, bonus_in_percent, bonus_ammount, claim_in_percent, claim_ammount, invoice_quantity, invoice_value, commission, commission_percent, other_discount_percent, other_discount_amt, upcharge, net_invo_value, country_id, remarks, bl_no, bl_date, bl_rev_date, doc_handover, forwarder_name, etd, feeder_vessel, mother_vessel, etd_destination, eta_destination_place, ic_recieved_date, inco_term, inco_term_place, shipping_bill_n, shipping_mode, total_carton_qnty, port_of_entry, port_of_loading, port_of_discharge, actual_shipment_date, ex_factory_date, freight_amnt_by_supllier, freight_amm_by_buyer, category_no, hs_code, ship_bl_date, advice_date, advice_amount, paid_amount, color_size_rate,gsp_co_no,gsp_co_no_date,cons_per_pcs,cargo_delivery_to,place_of_delivery, insentive_applicable,main_mark,side_mark,net_weight, gross_weight,cbm_qnty,co_no,co_date from com_export_invoice_ship_mst mst WHERE id=$data";
	$data_array=sql_select($sql);
	
 	foreach ($data_array as $row)
	{
		if($row[csf('is_lc')]==1)
		{
			$lc_sc_no=return_field_value("export_lc_no","com_export_lc","id=".$row[csf('lc_sc_id')]);
		}
		else
		{
			$lc_sc_no=return_field_value("contract_no","com_sales_contract","id=".$row[csf('lc_sc_id')]);
		}
		$additional_info=$row[csf("cargo_delivery_to")].'_'.$row[csf("place_of_delivery")].'_'.$row[csf("main_mark")].'_'.$row[csf("side_mark")].'_'.$row[csf("net_weight")].'_'.$row[csf("gross_weight")].'_'.$row[csf("cbm_qnty")];
		/*if($row[csf("place_of_delivery")]!="")
		{ 
			$additional_info.='_'.$row[csf("place_of_delivery")];
		}*/
		
		if($row[csf('exp_form_date')]=='0000-00-00' || $row[csf('exp_form_date')]=='') $exp_form_date=""; else $exp_form_date=change_date_format($row[csf("exp_form_date")]);
		if($row[csf('bl_date')]=='0000-00-00' || $row[csf('bl_date')]=='') $bl_date=""; else $bl_date=change_date_format($row[csf("bl_date")]);
		if($row[csf('bl_rev_date')]=='0000-00-00' || $row[csf('bl_rev_date')]=='') $bl_rev_date=""; else $bl_rev_date=change_date_format($row[csf("bl_rev_date")]);
		if($row[csf('doc_handover')]=='0000-00-00' || $row[csf('doc_handover')]=='') $doc_handover=""; else $doc_handover=change_date_format($row[csf("doc_handover")]);
		if($row[csf('etd')]=='0000-00-00' || $row[csf('etd')]=='') $etd=""; else $etd=change_date_format($row[csf("etd")]);
		if($row[csf('ic_recieved_date')]=='0000-00-00' || $row[csf('ic_recieved_date')]=='') $ic_recieved_date=""; else $ic_recieved_date=change_date_format($row[csf("ic_recieved_date")]);
		if($row[csf('etd_destination')]=='0000-00-00' || $row[csf('etd_destination')]=='') $etd_destination=""; else $etd_destination=change_date_format($row[csf("etd_destination")]);
		if($row[csf('ship_bl_date')]=='0000-00-00' || $row[csf('ship_bl_date')]=='') $ship_bl_date=""; else $ship_bl_date=change_date_format($row[csf("ship_bl_date")]);
		if($row[csf('actual_shipment_date')]=='0000-00-00' || $row[csf('actual_shipment_date')]=='') $actual_shipment_date=""; else $actual_shipment_date=change_date_format($row[csf("actual_shipment_date")]);
		if($row[csf('ex_factory_date')]=='0000-00-00' || $row[csf('ex_factory_date')]=='') $ex_factory_date=""; else $ex_factory_date=change_date_format($row[csf("ex_factory_date")]);
		if($row[csf('total_carton_qnty')]=='0') $total_carton_qnty=""; else $total_carton_qnty=$row[csf("total_carton_qnty")];
		if($row[csf('freight_amnt_by_supllier')]=='0') $freight_amnt_by_supllier=""; else $freight_amnt_by_supllier=$row[csf("freight_amnt_by_supllier")];
		if($row[csf('freight_amm_by_buyer')]=='0') $freight_amm_by_buyer=""; else $freight_amm_by_buyer=$row[csf("freight_amm_by_buyer")];
		
		if($row[csf('advice_date')]=='0000-00-00' || $row[csf('advice_date')]=='') $advice_date=""; else $advice_date=change_date_format($row[csf("advice_date")]);
		if($row[csf('advice_amount')]=='0') $advice_amount=""; else $advice_amount=$row[csf("advice_amount")];
		if($row[csf('paid_amount')]=='0') $paid_amount=""; else $paid_amount=$row[csf("paid_amount")];
		if($row[csf('gsp_co_no_date')]=='0000-00-00' || $row[csf('gsp_co_no_date')]=='') $gsp_co_no_date=""; else $gsp_co_no_date=change_date_format($row[csf("gsp_co_no_date")]);
		if($row[csf('co_date')]=='0000-00-00' || $row[csf('co_date')]=='') $co_date=""; else $co_date=change_date_format($row[csf("co_date")]);
		
		echo "document.getElementById('txt_lc_sc_no').value 		= '".$lc_sc_no."';\n";
		echo "document.getElementById('lc_sc_id').value 			= '".$row[csf("lc_sc_id")]."';\n";
		echo "document.getElementById('is_lc_sc').value 			= '".$row[csf("is_lc")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value 		= '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_invoice_no').value 		= '".$row[csf("invoice_no")]."';\n";
		echo "document.getElementById('txt_invoice_date').value 	= '".change_date_format($row[csf("invoice_date")])."';\n";
		echo "document.getElementById('txt_exp_form_no').value 		= '".$row[csf("exp_form_no")]."';\n";
		echo "document.getElementById('txt_exp_form_date').value 	= '".$exp_form_date."';\n";
		echo "document.getElementById('cbo_location').value 		= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('cbo_country').value 			= '".$row[csf("country_id")]."';\n";
		echo "document.getElementById('txt_remarks').value 			= '".$row[csf("remarks")]."';\n";
		
		echo "document.getElementById('txt_invoice_val').value 		= '".$row[csf("invoice_value")]."';\n";
		echo "document.getElementById('txt_discount').value 		= '".$row[csf("discount_in_percent")]."';\n";
		echo "document.getElementById('txt_discount_ammount').value = '".$row[csf("discount_ammount")]."';\n";
		echo "document.getElementById('txt_bonus').value 			= '".$row[csf("bonus_in_percent")]."';\n";
		echo "document.getElementById('txt_bonus_ammount').value 	= '".$row[csf("bonus_ammount")]."';\n";
		echo "document.getElementById('txt_claim').value 			= '".$row[csf("claim_in_percent")]."';\n";
		echo "document.getElementById('txt_claim_ammount').value 	= '".$row[csf("claim_ammount")]."';\n";
		echo "document.getElementById('txt_invo_qnty').value 		= '".$row[csf("invoice_quantity")]."';\n";
		
		
		echo "document.getElementById('txt_commission').value 		= '".$row[csf("commission_percent")]."';\n";
		echo "document.getElementById('txt_commission_amt').value 		= '".$row[csf("commission")]."';\n";
		echo "document.getElementById('txt_other_discount').value 		= '".$row[csf("other_discount_percent")]."';\n";
		echo "document.getElementById('txt_other_discount_amt').value 		= '".$row[csf("other_discount_amt")]."';\n";
		echo "document.getElementById('txt_upcharge').value 	= '".$row[csf("upcharge")]."';\n";
		echo "document.getElementById('txt_net_invo_val').value 	= '".$row[csf("net_invo_value")]."';\n";
		
		echo "document.getElementById('bl_no').value				= '".$row[csf("bl_no")]."';\n";
		echo "document.getElementById('bl_date').value 				= '".$bl_date."';\n";
		echo "document.getElementById('bl_rev_date').value 			= '".$bl_rev_date."';\n";
		echo "document.getElementById('doc_handover').value 		= '".$doc_handover."';\n";
		echo "document.getElementById('forwarder_name').value 		= '".$row[csf("forwarder_name")]."';\n";
		echo "document.getElementById('etd').value 					= '".$etd."';\n";
		echo "document.getElementById('feeder_vessel').value 		= '".$row[csf("feeder_vessel")]."';\n";
		echo "document.getElementById('mother_vessel').value 		= '".$row[csf("mother_vessel")]."';\n";
		echo "document.getElementById('etd_destination').value		= '".$etd_destination."';\n";
		echo "document.getElementById('txt_eta_destination').value	= '".$row[csf("eta_destination_place")]."';\n";
		echo "document.getElementById('ic_recieved_date').value 	= '".$ic_recieved_date."';\n";
		echo "document.getElementById('inco_term').value			= '".$row[csf("inco_term")]."';\n";
		echo "document.getElementById('inco_term_place').value 		= '".$row[csf("inco_term_place")]."';\n";
		echo "document.getElementById('shipping_bill_no').value 	= '".$row[csf("shipping_bill_n")]."';\n";
		echo "document.getElementById('ship_bl_date').value 		= '".$ship_bl_date."';\n";
		echo "document.getElementById('port_of_entry').value 		= '".$row[csf("port_of_entry")]."';\n";
		echo "document.getElementById('port_of_loading').value 		= '".$row[csf("port_of_loading")]."';\n";
		echo "document.getElementById('port_of_discharge').value 	= '".$row[csf("port_of_discharge")]."';\n";
		echo "document.getElementById('shipping_mode').value 		= '".$row[csf("shipping_mode")]."';\n";
		echo "document.getElementById('freight_amnt_supplier').value= '".$freight_amnt_by_supllier."';\n";
		echo "document.getElementById('ex_factory_date').value 		= '".$ex_factory_date."';\n";
		echo "document.getElementById('actual_shipment_date').value	= '".$actual_shipment_date."';\n";
		echo "document.getElementById('freight_amnt_buyer').value 	= '".$freight_amm_by_buyer."';\n";
		echo "document.getElementById('total_carton_qnty').value 	= '".$total_carton_qnty."';\n";
		echo "document.getElementById('txt_category_no').value 		= '".$row[csf("category_no")]."';\n";
		echo "document.getElementById('txt_hs_code').value 			= '".$row[csf("hs_code")]."';\n";
		
		echo "document.getElementById('txt_advice_date').value 		= '".$advice_date."';\n";
		echo "document.getElementById('txt_advice_amnt').value 		= '".$advice_amount."';\n";
		echo "document.getElementById('txt_paid_amnt').value 		= '".$paid_amount."';\n";
		echo "document.getElementById('txt_gsp_co').value 			= '".$row[csf("gsp_co_no")]."';\n";
		echo "document.getElementById('txt_gsp_co_date').value 		= '".$gsp_co_no_date."';\n";
		echo "document.getElementById('txt_co_no').value 			= '".$row[csf("co_no")]."';\n";
		echo "document.getElementById('txt_co_date').value 		= '".$co_date."';\n";
		echo "document.getElementById('cbo_incentive').value 		= '".$row[csf("insentive_applicable")]."';\n";
		echo "document.getElementById('txt_cons').value 			= '".$row[csf("cons_per_pcs")]."';\n";
		
		echo "document.getElementById('additional_info').value 		= '".$additional_info."';\n";
		
		if($row[csf("color_size_rate")]==1)
		{
			echo "$('#chk_color_size_rate').attr('checked','checked');\n";
		}
		else
		{
			echo "$('#chk_color_size_rate').removeAttr('checked','checked');\n";
		}
		
		echo "document.getElementById('update_id').value 			= '".$row[csf("id")]."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_export_information_entry',1);\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_export_information_entry_shipping_info',2);\n";  
		
		exit();
	}
}

if ($action=="save_update_delete_mst")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$additional_info=explode("_",str_replace("'", '',$additional_info));
	
	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if(str_replace("'", '',$is_lc_sc)==1)
		{
			if (is_duplicate_field( "invoice_no", "com_export_invoice_ship_mst a, com_export_lc b", "a.lc_sc_id=b.id and a.is_lc=1 and a.invoice_no=$txt_invoice_no and a.benificiary_id=$cbo_beneficiary_name and b.buyer_name=$cbo_buyer_name and b.lien_bank=$cbo_lien_bank and b.id=$lc_sc_id" )==1)
			{
				echo "11**0"; 
				die;			
			}
		}
		else if(str_replace("'", '',$is_lc_sc)==2)
		{
			if (is_duplicate_field( "invoice_no", "com_export_invoice_ship_mst a, com_sales_contract b", "a.lc_sc_id=b.id and a.is_lc=2 and a.invoice_no=$txt_invoice_no and a.benificiary_id=$cbo_beneficiary_name and b.buyer_name=$cbo_buyer_name and b.lien_bank=$cbo_lien_bank and b.id=$lc_sc_id" )==1)
			{
				echo "11**0"; 
				die;			
			}
		}
		
		$flag=1;
		$id=return_next_id( "id", "com_export_invoice_ship_mst", 1 ) ;
				 
		$field_array="id, invoice_no, invoice_date, buyer_id, location_id, benificiary_id, is_lc, lc_sc_id, exp_form_no, exp_form_date, discount_in_percent, discount_ammount, bonus_in_percent, bonus_ammount, claim_in_percent, claim_ammount, invoice_quantity, invoice_value, commission_percent, commission, other_discount_percent, other_discount_amt, upcharge, net_invo_value, country_id, remarks, color_size_rate, cargo_delivery_to ,place_of_delivery, main_mark, side_mark, net_weight, gross_weight, cbm_qnty, inserted_by, insert_date";
		
		$data_array="(".$id.",".$txt_invoice_no.",".$txt_invoice_date.",".$cbo_buyer_name.",".$cbo_location.",".$cbo_beneficiary_name.",".$is_lc_sc.",".$lc_sc_id.",".$txt_exp_form_no.",".$txt_exp_form_date.",".$txt_discount.",".$txt_discount_ammount.",".$txt_bonus.",".$txt_bonus_ammount.",".$txt_claim.",".$txt_claim_ammount.",".$txt_invo_qnty.",".$txt_invoice_val.",".$txt_commission.",".$txt_commission_amt.",".$txt_other_discount.",".$txt_other_discount_amt.",".$txt_upcharge.",".$txt_net_invo_val.",".$cbo_country.",".$txt_remarks.",".$color_size_rate.",'".$additional_info[0]."','".$additional_info[1]."','".$additional_info[2]."','".$additional_info[3]."','".$additional_info[4]."','".$additional_info[5]."','".$additional_info[6]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		//echo "insert into com_export_invoice_ship_mst (".$field_array.") values ".$data_array;die;
		/*$rID=sql_insert("com_export_invoice_ship_mst",$field_array,$data_array,0);
		if($flag==1) 
		{
			if($rID) $flag=1; else $flag=0; 
		} */
		
		$field_array_dtls="id, mst_id, po_breakdown_id, current_invoice_rate, current_invoice_qnty, current_invoice_value, production_source, color_size_rate_data, actual_po_infos, inserted_by, insert_date";
		$field_array_actual_po="id, invoice_id, invoice_details_id, wo_po_breakdown_id, wo_po_act_id, po_no, po_qty, inserted_by, insert_date";
		$field_array_color_size_rate="id, invoice_id, invoice_details_id, po_breakdown_id, country_id, qnty, rate, amount, inserted_by, insert_date";
		
		$id_dtls = return_next_id( "id", "com_export_invoice_ship_dtls", 1 );
		$act_id = return_next_id( "id", "export_invoice_act_po" );
		if($tot_row==0)
		{
			$data_array_dtls="(".$id_dtls.",".$id.",0,0,".$txt_invo_qnty.",".$txt_invoice_val.",0,'','',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}
		else
		{
			for($j=1;$j<=$tot_row;$j++)
			{ 	
				$po_breakdown_id="order_id_".$j;
				$order_rate="order_rate_".$j;
				$curr_invo_qty="curr_invo_qty_".$j;
				$curr_invo_val="curr_invo_val_".$j;
				$cbo_production_source="cbo_production_source_".$j;
				$actual_po_infos="actual_po_infos_".$j;
				$colorSize_infos="colorSize_infos_".$j;
				
				if(str_replace("'",'',$$curr_invo_qty)>0)
				{
					if($data_array_dtls!="") $data_array_dtls.=",";
					$data_array_dtls.="(".$id_dtls.",".$id.",".$$po_breakdown_id.",".$$order_rate.",".$$curr_invo_qty.",".$$curr_invo_val.",".$$cbo_production_source.",".$$colorSize_infos.",".$$actual_po_infos.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$id_dtls = $id_dtls+1;
					
					if(str_replace("'", '',$$actual_po_infos)!="")
					{
						$actual_po=explode("**",str_replace("'", '',$$actual_po_infos));
						
						foreach($actual_po as $value)
						{
							$actual_po_val=explode('=',$value);
							$po_id = $actual_po_val[0];
							$po_qty = $actual_po_val[1];
							$po_num = $actual_po_val[2];
							
							if($data_array_actual_po!="") $data_array_actual_po.=","; 
							
							$data_array_actual_po.="(".$act_id.",".$id.",".$id_dtls.",".$$po_breakdown_id.",'".$po_id."','".$po_num."','".$po_qty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
							$act_id=$act_id+1;
						}
					}
					
					if($color_size_rate==1)
					{
						$colorSize_data=explode("**",str_replace("'", '',$$colorSize_infos));
						
						foreach($colorSize_data as $value)
						{
							$colorSize_val=explode('=',$value);
							$colorSize_id = $colorSize_val[0];
							$colorSize_qnty = $colorSize_val[1];
							$colorSize_rate = $colorSize_val[2];
							$colorSize_amnt = $colorSize_val[3];
							
							if($color_size_rate_id=="") $color_size_rate_id = return_next_id( "id", "export_invoice_clr_sz_rt" ); else $color_size_rate_id=$color_size_rate_id+1;
							
							if($data_array_color_size_rate!="") $data_array_color_size_rate.=","; 
							
							$data_array_color_size_rate.="(".$color_size_rate_id.",".$id.",".$id_dtls.",'".$colorSize_id."',".$cbo_country.",'".$colorSize_qnty."','".$colorSize_rate."','".$colorSize_amnt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
							
						}
					}
				}
			}
		}
		//echo "5**insert into com_export_invoice_ship_mst (".$field_array.") values ".$data_array."***".$flag;die;
		$rID=sql_insert("com_export_invoice_ship_mst",$field_array,$data_array,0);
		if($flag==1) 
		{
			if($rID) $flag=1; else $flag=0; 
		} 
		//echo "5**".$flag;die;
		//echo "5**insert into export_invoice_act_po (".$field_array_actual_po.") values ".$data_array_actual_po."***".$flag;die;
		if($data_array_actual_po!="")
		{
			$rID3=sql_insert("export_invoice_act_po",$field_array_actual_po,$data_array_actual_po,0);
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			} 
		}
		//echo "5**insert into export_invoice_clr_sz_rt (".$field_array_color_size_rate.") values ".$data_array_color_size_rate."***".$flag;die;
		if($data_array_color_size_rate!="")
		{
			$rID4=sql_insert("export_invoice_clr_sz_rt",$field_array_color_size_rate,$data_array_color_size_rate,0);
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			} 
		}
		
		$rID2=sql_insert("com_export_invoice_ship_dtls",$field_array_dtls,$data_array_dtls,1);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		//echo "10**$rID##$rID2##$rID3##$rID4";die;
		/*oci_rollback($con);*/
		//echo "5**0**0**".$flag."222";die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**1";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);   
				echo "0**".$id."**1";
			}
			else
			{
				oci_rollback($con);
				echo "5**0**0";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{ 
		$con = connect();
		
/*		if (is_duplicate_field( "invoice_no", "pro_ex_factory_mst", "invoice_no=$update_id and status_active=1 and is_deleted=0" )==1)
		{
			echo "101**".$txt_invoice_no; 
			die;			
		}
*/		
		
		if($db_type==0)
		{
			mysql_query("BEGIN");
		
			$sql="select a.id as id, a.bank_ref_no as bill_no, 'Submission' as type from com_export_doc_submission_mst a, com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and b.invoice_id=$update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.bank_ref_no
				union all
				select id, '' as bill_no, 'Proceed Realization' as type from com_export_proceed_realization where invoice_bill_id=$update_id and is_invoice_bill=2 and status_active=1 and is_deleted=0";
		}
		else
		{
			$sql="select a.id as id, a.bank_ref_no as bill_no, 'Submission' as type from com_export_doc_submission_mst a, com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and b.invoice_id=$update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.bank_ref_no
				union all
				select id, CAST (NULL AS NVARCHAR2(2)) as bill_no, 'Proceed Realization' as type from com_export_proceed_realization where invoice_bill_id=$update_id and is_invoice_bill=2 and status_active=1 and is_deleted=0";
		}
		$data=sql_select($sql);
		if(count($data)>0)
		{
			if($data[0][csf('bill_no')]!='') $invoice_realization=$data[0][csf('type')]."(System Id:".$data[0][csf('id')].", Bill No: ".$data[0][csf('bill_no')].")";
			else $invoice_realization=$data[0][csf('type')]."(System Id: ".$data[0][csf('id')].")";
			
			echo "14**".$invoice_realization."**1"; 
			die;
		}
		
		if(str_replace("'", '',$is_lc_sc)==1)
		{
			if (is_duplicate_field( "invoice_no", "com_export_invoice_ship_mst a, com_export_lc b", "a.lc_sc_id=b.id and a.is_lc=1 and a.invoice_no=$txt_invoice_no and a.benificiary_id=$cbo_beneficiary_name and b.buyer_name=$cbo_buyer_name and b.lien_bank=$cbo_lien_bank and b.id=$lc_sc_id and a.id<>$update_id" )==1)
			{
				echo "11**0"; 
				die;			
			}
		}
		else if(str_replace("'", '',$is_lc_sc)==2)
		{
			if (is_duplicate_field( "invoice_no", "com_export_invoice_ship_mst a, com_sales_contract b", "a.lc_sc_id=b.id and a.is_lc=2 and a.invoice_no=$txt_invoice_no and a.benificiary_id=$cbo_beneficiary_name and b.buyer_name=$cbo_buyer_name and b.lien_bank=$cbo_lien_bank and b.id=$lc_sc_id and a.id<>$update_id" )==1)
			{
				echo "11**0"; 
				die;			
			}
		}
		
		$flag=1;
		$field_array="invoice_no*invoice_date*buyer_id*location_id*benificiary_id*is_lc*lc_sc_id*exp_form_no*exp_form_date*discount_in_percent*discount_ammount*bonus_in_percent*bonus_ammount*claim_in_percent*claim_ammount*invoice_quantity*invoice_value*commission_percent*commission*other_discount_percent*other_discount_amt*upcharge*net_invo_value*country_id*remarks*color_size_rate*updated_by*update_date*cargo_delivery_to*place_of_delivery*main_mark*side_mark*net_weight*gross_weight*cbm_qnty";
		
		$data_array=$txt_invoice_no."*".$txt_invoice_date."*".$cbo_buyer_name."*".$cbo_location."*".$cbo_beneficiary_name."*".$is_lc_sc."*".$lc_sc_id."*".$txt_exp_form_no."*".$txt_exp_form_date."*".$txt_discount."*".$txt_discount_ammount."*".$txt_bonus."*".$txt_bonus_ammount."*".$txt_claim."*".$txt_claim_ammount."*".$txt_invo_qnty."*".$txt_invoice_val."*".$txt_commission."*".$txt_commission_amt."*".$txt_other_discount."*".$txt_other_discount_amt."*".$txt_upcharge."*".$txt_net_invo_val."*".$cbo_country."*".$txt_remarks."*".$color_size_rate."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'".$additional_info[0]."'*'".$additional_info[1]."'*'".$additional_info[2]."'*'".$additional_info[3]."'*'".$additional_info[4]."'*'".$additional_info[5]."'*'".$additional_info[6]."'";
		
		//echo "insert into com_export_invoice_ship_mst (".$field_array.") values ".$data_array;die;
		/*$rID=sql_update("com_export_invoice_ship_mst",$field_array,$data_array,"id",$update_id,0);
		if($flag==1) 
		{
			if($rID) $flag=1; else $flag=0; 
		} 
		
		$delete_dtls=execute_query( "delete from com_export_invoice_ship_dtls where mst_id=$update_id",0);
		if($flag==1) 
		{
			if($delete_dtls) $flag=1; else $flag=0; 
		} 

		$delete_actual=execute_query( "delete from export_invoice_act_po where invoice_id=$update_id",0);
		if($flag==1) 
		{
			if($delete_actual) $flag=1; else $flag=0; 
		} 
		
		$delete_color_size_rate=execute_query( "delete from export_invoice_clr_sz_rt where invoice_id=$update_id",0);
		if($flag==1) 
		{
			if($delete_color_size_rate) $flag=1; else $flag=0; 
		} */
		
		$field_array_dtls="id, mst_id, po_breakdown_id, current_invoice_rate, current_invoice_qnty, current_invoice_value, production_source, color_size_rate_data, actual_po_infos, inserted_by, insert_date";
		$field_array_actual_po="id, invoice_id, invoice_details_id, wo_po_breakdown_id, wo_po_act_id, po_no, po_qty, inserted_by, insert_date";
		$field_array_color_size_rate="id, invoice_id, invoice_details_id, po_breakdown_id, country_id, qnty, rate, amount, inserted_by, insert_date";
		$id_dtls=return_next_id( "id", "com_export_invoice_ship_dtls", 1 ) ;
		$act_id = return_next_id( "id", "export_invoice_act_po" );
		if($tot_row==0)
		{
			$data_array_dtls="(".$id_dtls.",".$update_id.",0,0,".$txt_invo_qnty.",".$txt_invoice_val.",0,'','',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}
		else
		{
			for($j=1;$j<=$tot_row;$j++)
			{ 	
				$po_breakdown_id="order_id_".$j;
				$order_rate="order_rate_".$j;
				$curr_invo_qty="curr_invo_qty_".$j;
				$curr_invo_val="curr_invo_val_".$j;
				$cbo_production_source="cbo_production_source_".$j;
				$actual_po_infos="actual_po_infos_".$j;
				$colorSize_infos="colorSize_infos_".$j;
				
				if(str_replace("'",'',$$curr_invo_qty)>0)
				{
					if($data_array_dtls!="") $data_array_dtls.=",";
					$data_array_dtls.="(".$id_dtls.",".$update_id.",".$$po_breakdown_id.",".$$order_rate.",".$$curr_invo_qty.",".$$curr_invo_val.",".$$cbo_production_source.",".$$colorSize_infos.",".$$actual_po_infos.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$id_dtls = $id_dtls+1;
					
					
					if(str_replace("'", '',$$actual_po_infos)!="")
					{
						$actual_po=explode("**",str_replace("'", '',$$actual_po_infos));
						
						foreach($actual_po as $value)
						{
							$actual_po_val=explode('=',$value);
							$po_id = $actual_po_val[0];
							$po_qty = $actual_po_val[1];
							$po_num = $actual_po_val[2];
							
							if($data_array_actual_po!="") $data_array_actual_po.=","; 
							
							$data_array_actual_po.="(".$act_id.",".$update_id.",".$id_dtls.",".$$po_breakdown_id.",'".$po_id."','".$po_num."','".$po_qty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
							$act_id=$act_id+1;
						}
					}
					
					if($color_size_rate==1)
					{
						$colorSize_data=explode("**",str_replace("'", '',$$colorSize_infos));
						
						foreach($colorSize_data as $value)
						{
							$colorSize_val=explode('=',$value);
							$colorSize_id = $colorSize_val[0];
							$colorSize_qnty = $colorSize_val[1];
							$colorSize_rate = $colorSize_val[2];
							$colorSize_amnt = $colorSize_val[3];
							
							if($color_size_rate_id=="") $color_size_rate_id = return_next_id( "id", "export_invoice_clr_sz_rt" ); else $color_size_rate_id=$color_size_rate_id+1;
							
							if($data_array_color_size_rate!="") $data_array_color_size_rate.=","; 
							
							$data_array_color_size_rate.="(".$color_size_rate_id.",".$update_id.",".$id_dtls.",'".$colorSize_id."',".$cbo_country.",'".$colorSize_qnty."','".$colorSize_rate."','".$colorSize_amnt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						}
					}
				}
			}
		}
		
		$rID=sql_update("com_export_invoice_ship_mst",$field_array,$data_array,"id",$update_id,0);
		if($flag==1) 
		{
			if($rID) $flag=1; else $flag=0; 
		} 
		
		$delete_dtls=execute_query( "delete from com_export_invoice_ship_dtls where mst_id=$update_id",0);
		if($flag==1) 
		{
			if($delete_dtls) $flag=1; else $flag=0; 
		} 

		$delete_actual=execute_query( "delete from export_invoice_act_po where invoice_id=$update_id",0);
		if($flag==1) 
		{
			if($delete_actual) $flag=1; else $flag=0; 
		} 
		
		$delete_color_size_rate=execute_query( "delete from export_invoice_clr_sz_rt where invoice_id=$update_id",0);
		if($flag==1) 
		{
			if($delete_color_size_rate) $flag=1; else $flag=0; 
		} 
		//echo "6**insert into export_invoice_act_po (".$field_array_actual_po.") values ".$data_array_actual_po."***".$flag;die;
		if($data_array_actual_po!="")
		{
			$rID3=sql_insert("export_invoice_act_po",$field_array_actual_po,$data_array_actual_po,0);
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			} 
		}
		
		//echo "6**insert into export_invoice_clr_sz_rt (".$field_array_color_size_rate.") values ".$data_array_color_size_rate."***".$flag;die;
		if($data_array_color_size_rate!="")
		{
			$rID4=sql_insert("export_invoice_clr_sz_rt",$field_array_color_size_rate,$data_array_color_size_rate,0);
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			} 
		}
		
		$rID2=sql_insert("com_export_invoice_ship_dtls",$field_array_dtls,$data_array_dtls,1);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		//echo "10** insert into export_invoice_act_po ($field_array_actual_po) values $data_array_actual_po";die;
		//echo "10**$rID##$rID2##$rID3##$rID4##$delete_dtls##$delete_actual##$delete_color_size_rate";die;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**1";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'", '', $update_id)."**1";
			}
			else
			{
				oci_rollback($con);
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		disconnect($con);
		die;
	}
	
}

if ($action=="save_update_delete_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	//txt_co_no*txt_co_date*txt_eta_destination
	$field_array="bl_no*bl_date*bl_rev_date*doc_handover*forwarder_name*etd*feeder_vessel*mother_vessel*etd_destination*eta_destination_place*ic_recieved_date*inco_term*inco_term_place*shipping_bill_n*shipping_mode*total_carton_qnty*port_of_entry*port_of_loading*port_of_discharge*actual_shipment_date*ex_factory_date*freight_amnt_by_supllier*freight_amm_by_buyer*category_no*hs_code*ship_bl_date*advice_date*advice_amount*paid_amount*gsp_co_no*gsp_co_no_date*co_no*co_date*cons_per_pcs*insentive_applicable*updated_by*update_date";
		
	$data_array=$bl_no."*".$bl_date."*".$bl_rev_date."*".$doc_handover."*".$forwarder_name."*".$etd."*".$feeder_vessel."*".$mother_vessel."*".$etd_destination."*".$txt_eta_destination."*".$ic_recieved_date."*".$inco_term."*".$inco_term_place."*".$shipping_bill_no."*".$shipping_mode."*".$total_carton_qnty."*".$port_of_entry."*".$port_of_loading."*".$port_of_discharge."*".$actual_shipment_date."*".$ex_factory_date."*".$freight_amnt_supplier."*".$freight_amnt_buyer."*".$txt_category_no."*".$txt_hs_code."*".$ship_bl_date."*".$txt_advice_date."*".$txt_advice_amnt."*".$txt_paid_amnt."*".$txt_gsp_co."*".$txt_gsp_co_date."*".$txt_co_no."*".$txt_co_date."*".$txt_cons."*".$cbo_incentive."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
	
	$rID=sql_update("com_export_invoice_ship_mst",$field_array,$data_array,"id",$update_id,1);
	
	if($operation==0)
	{
		$msg="5"; $button_staus="0";
	}
	else if ($operation==1)
	{
		$msg="6"; $button_staus="1";
	}
	
	if($db_type==0)
	{
		if($rID)
		{
			mysql_query("COMMIT");  
			echo "1**1";
		}
		else
		{
			mysql_query("ROLLBACK"); 
			echo $msg."**".$button_staus;
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID)
		{
			oci_commit($con);   
			echo "1**1";
		}
		else
		{
			oci_rollback($con);
			echo $msg."**".$button_staus;
		}
	}
	
	disconnect($con);
	die;
	
}

if ($action=="actual_po_info_popup")
{
	echo load_html_head_contents("Actual PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

?> 
	<script>
	
		
		/*function set_values()
		{
			var actual_po_infos  = '<?php// echo $actual_po_infos; ?>';
			var arr = actual_po_infos.split('**');
			var i=0;
			$(arr).each(function(index, element) {
				index++;
				var po_infos = this.split('=');
				var po_num  = po_infos[0];
				var po_qty  = po_infos[1];
				if(index != 1)  add_break_down_tr(i);			
				$('#poNo_'+index).val(po_num);
				$('#poQnty_'+index).val(po_qty);
				i++;
			});
		}*/
		
		/*function add_break_down_tr( i )
		{ 
			var row_num=$('#tbl_list_search tbody tr').length;
			row_num++;
			
			var clone= $("#tr_"+i).clone();
			clone.attr({
				id: "tr_" + row_num,
			});
			
			clone.find("input,select").each(function(){
				  
			$(this).attr({ 
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
			  'name': function(_, name) { return name },
			  'value': function(_, value) { return '' }              
			});
			 
			}).end();
			
			$("#tr_"+i).after(clone);
			
			$('#increase_'+row_num).removeAttr("value").attr("value","+");
			$('#decrease_'+row_num).removeAttr("value").attr("value","-");
			$('#increase_'+row_num).removeAttr("onclick").attr("onclick","add_break_down_tr("+row_num+");");
			$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
		}*/
		
		/*function fn_deleteRow(rowNo) 
		{ 
			var numRow = $('#tbl_list_search tbody tr').length; 
		
			if(numRow!=1)
			{
				$("#tr_"+rowNo).remove();
			}
		}*/
		
		/*function fnc_close()
		{
			var save_string='';	
			$("#tbl_list_search").find('tr').each(function()
			{
				var poNo=$(this).find('input[name="poNo[]"]').val();
				var poQnty=$(this).find('input[name="poQnty[]"]').val();
				
				if(poQnty*1>0 && poNo!="")
				{
					if(save_string=="")
					{
						save_string=poNo+"="+poQnty;
					}
					else
					{
						save_string+="**"+poNo+"="+poQnty;
					}
				}
			});
			
			$('#actual_po_infos').val( save_string );
			
			parent.emailwindow.hide();
		}*/
		
		function fnc_close()
		{
			var save_string='';	
			$("#tbl_list_search").find('tr').each(function()
			{
				var poActId=$(this).find('input[name="poActId[]"]').val();
				var invQnty=$(this).find('input[name="invQnty[]"]').val();
				var poNo=$(this).find('input[name="poNo[]"]').val();
				
				if(invQnty*1>0)
				{
					if(save_string=="")
					{
						save_string=poActId+"="+invQnty+"="+poNo;
					}
					else
					{
						save_string+="**"+poActId+"="+invQnty+"="+poNo;
					}
				}
			});
			
			$('#actual_po_infos').val( save_string );
			
			parent.emailwindow.hide();
		}
    </script>

</head>

<body> <!--onLoad="set_values();"-->
<div align="center">
	<fieldset style="width:360px">
        <table width="360" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
            <thead>
                <th>PO Number</th>
                <th>PO Quantity</th>
                <th>Invoice Quantity</th>
            </thead>
            <tbody>
            <?
				$save_string=explode("**",$actual_po_infos); $actual_po_data_array=array(); $i=0;
				foreach($save_string as $value)
				{
					$value=explode("=",$value);
					$actual_po_data_array[$value[0]]=$value[1];
				}

            	$sql="select id, acc_po_no, acc_po_qty from wo_po_acc_po_info where po_break_down_id='$order_id' and is_deleted=0 and status_active=1";
				$result=sql_select($sql);
				foreach($result as $row)
				{
					$i++;
					if($i%2==0) $bgcolor="#FFFFFF"; else $bgcolor="#E9F3FF";	
					$invcQnty=$actual_po_data_array[$row[csf('id')]];
				?>
                    <tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>">
                        <td>
                        	<input type="text" id="poNo_<? echo $i; ?>" name="poNo[]" class="text_boxes" style="width:110px" value="<? echo $row[csf('acc_po_no')]; ?>" disabled />
                            <input type="hidden" id="poActId_<? echo $i; ?>" name="poActId[]" value="<? echo $row[csf('id')]; ?>">
                        </td>
                        <td>
                        	<input type="text" id="poQnty_<? echo $i; ?>" name="poQnty[]" class="text_boxes_numeric" value="<? echo $row[csf('acc_po_qty')]; ?>" style="width:100px" disabled/>
                        </td>
                        <td>
                        	<input type="text" id="invQnty_<? echo $i; ?>" name="invQnty[]" class="text_boxes_numeric" value="<? echo $invcQnty; ?>" style="width:100px"/> 
                        </td>
                    </tr>
                <?	
				}
				?>
               <!-- <tr class="general" id="tr_1">
                    <td align="center"><input type="text" id="poNo_1" name="poNo[]" class="text_boxes" style="width:130px" /></td>
                    <td align="center"><input type="text" id="poQnty_1" name="poQnty[]" class="text_boxes_numeric" style="width:120px"/></td>
                    <td width="70">
                        <input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(1)" />
                        <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(1);" />
                    </td>
                </tr>-->
            </tbody>
        </table>
        <div align="center" style="margin-top:10px">
            <input type="button" class="formbutton" onClick="fnc_close()" value="Close" style="width:100px"/>
            <input type="hidden" id="actual_po_infos" />
        </div>
	</fieldset>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action=="colorSize_infos_popup")
{
	echo load_html_head_contents("Color & Size Rate Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

?> 
	<script>

		function fnc_close()
		{
			var save_string='';	
			var tot_row=$("#tbl_list_search tbody tr").length;
			for(var i=1;i<=tot_row;i++)
			{
				var txtInvcQnty=parseInt($('#txtInvcQnty_'+i).val());
				
				if(txtInvcQnty>0)
				{
					if(save_string=="")
					{
						save_string=$('#colorSizeId_'+i).val()+"="+$('#txtInvcQnty_'+i).val()+"="+$('#txtInvcRate_'+i).val()+"="+$('#txtInvcAmount_'+i).val();
					}
					else
					{
						save_string+="**"+$('#colorSizeId_'+i).val()+"="+$('#txtInvcQnty_'+i).val()+"="+$('#txtInvcRate_'+i).val()+"="+$('#txtInvcAmount_'+i).val();
					}
				}
			}
			
			$('#colorSize_infos').val(save_string );
			
			parent.emailwindow.hide();
		}
		
		function calculate(row_id)
		{
			var invc_qnty=$('#txtInvcQnty_'+row_id).val()*1;
			var invc_rate=$('#txtInvcRate_'+row_id).val()*1;
			var invc_amnt=invc_qnty*invc_rate;
			$('#txtInvcAmount_'+row_id).val(invc_amnt);//.toFixed(2)
			calculate_total();
		}
		
		function calculate_total()
		{
			var tot_row=$("#tbl_list_search tbody tr").length;
			var ddd={ dec_type:2, comma:0, currency:''}
			math_operation( "totInvcQnty", "txtInvcQnty_", "+", tot_row );
			math_operation( "totInvcAmount", "txtInvcAmount_", "+", tot_row, ddd );
			
			var tot_invc_qnty=$('#totInvcQnty').val()*1;
			var tot_invc_amnt=$('#totInvcAmount').val()*1;
			var avg_invc_rate=tot_invc_amnt/tot_invc_qnty;
			$('#InvcAvgRate').val(avg_invc_rate);//.toFixed(2)
		}
    </script>

</head>

<body>
<div align="center">
	<fieldset style="width:890px">
        <table width="450" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
            <thead>
                <th>PO Number</th>
                <th>Country</th>
            </thead>
            <tr bgcolor="#EFEFEF">
                <td align="center"><? echo $order_no=return_field_value( "po_number","wo_po_break_down","id='$order_id'"); ?></td>
                <td align="center"><? echo $country_name=return_field_value( "country_name","lib_country","id='$country_id'"); ?></td>
            </tr>
        </table>
        <br />
        <table width="870" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
        	<thead>
                <th width="140">Gmts Item</th>
                <th width="80">Article No.</th>
                <th width="100">Color</th>
                <th width="70">Size</th>
                <th width="80">Order Qnty</th>
                <th width="70">Rate</th>
                <th width="80">Amount</th>
                <th width="80">Invoice Qnty</th>
                <th width="70">Invoice Rate</th>
                <th>Invoice Amount</th>
        	</thead>
            <tbody>
            <?
				$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
				$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
				
				$save_string=explode("**",$colorSize_infos); $color_size_data_array=array(); $i=0;
				foreach($save_string as $value)
				{
					$value=explode("=",$value);
					$color_size_data_array[$value[0]]['qnty']=$value[1];
					$color_size_data_array[$value[0]]['rate']=$value[2];
					$color_size_data_array[$value[0]]['amnt']=$value[3];
				}
				
				$sql="select id, item_number_id, article_number, size_number_id, color_number_id, order_quantity, order_rate, order_total from wo_po_color_size_breakdown where po_break_down_id='$order_id' and country_id='$country_id' and is_deleted=0 and status_active=1";
				$result=sql_select($sql);
				foreach($result as $row)
				{
					$i++;
					
					if ($i%2==0)  
						$bgcolor="#FFFFFF";
					else
						$bgcolor="#E9F3FF";	
					
					$invcQnty=$color_size_data_array[$row[csf('id')]]['qnty'];
					$invcRate=$color_size_data_array[$row[csf('id')]]['rate'];
					$invcAmnt=$color_size_data_array[$row[csf('id')]]['amnt'];
					
					if($invcRate=="") $invcRate=$row[csf('order_rate')];
				?>
                    <tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>">
                        <td>
                        	<?  echo create_drop_down( "cboGmts_".$i, 132, $garments_item,"", 0, '','', '','1',$row[csf('item_number_id')]); ?>
                        </td>
                        <td>
                        	<input type="text" id="txtArticleno_<? echo $i; ?>" name="txtArticleno_<? echo $i; ?>" value="<? echo $row[csf('article_number')]; ?>" class="text_boxes" style="width:70px" disabled> 
                        </td>
                        <td>
                        	<input type="text" id="txtColor_<? echo $i;?>" name="txtColor_<? echo $i;?>" value="<? echo $color_arr[$row[csf('color_number_id')]]; ?>" class="text_boxes" style="width:90px" disabled>
                        </td>
                        <td>
                        	<input type="text" id="txtSize_<? echo $i; ?>" name="txtSize_<? echo $i; ?>" value="<? echo $size_arr[$row[csf('size_number_id')]]; ?>" class="text_boxes" style="width:55px" disabled> 
                        </td>
                        <td>
                        	<input type="text" id="txtOrderQnty_<? echo $i; ?>" name="txtOrderQnty_<? echo $i; ?>" value="<? echo $row[csf('order_quantity')]; ?>" class="text_boxes_numeric" style="width:70px" disabled> 
                        </td>
                        <td>
                        	<input type="text" id="txtOrderRate_<? echo $i; ?>" value="<? echo $row[csf('order_rate')]; ?>" name="txtOrderRate_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" disabled> 
                        </td>
                        <td>
                        	<input type="text" id="txtOrderAmount_<? echo $i; ?>" name="txtOrderAmount_<? echo $i; ?>"  value="<? echo $row[csf('order_total')]; ?>" class="text_boxes_numeric" style="width:70px" disabled> 
                        </td>
                        <td>
                        	<input type="text" id="txtInvcQnty_<? echo $i; ?>" name="txtInvcQnty_<? echo $i; ?>" value="<? echo $invcQnty; ?>" class="text_boxes_numeric" style="width:70px" onKeyUp="calculate(<? echo $i; ?>);"> 
                        </td>
                        <td>
                        	<input type="text" id="txtInvcRate_<? echo $i; ?>" name="txtInvcRate_<? echo $i; ?>" value="<? echo $invcRate; ?>" class="text_boxes_numeric" style="width:60px" onKeyUp="calculate(<? echo $i; ?>);"> 
                        </td>
                        <td>
                        	<input type="text" id="txtInvcAmount_<? echo $i; ?>" name="txtInvcAmount_<? echo $i; ?>"  value="<? echo $invcAmnt; ?>" class="text_boxes_numeric" style="width:70px" disabled> 
                            <input type="hidden" id="colorSizeId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
                        </td>
                    </tr>
                <?	
					$totOrderQnty+=$row[csf('order_quantity')];
					$totOrderAmount+=$row[csf('order_total')];
					$totInvcQnty+=$invcQnty;
					$totInvcAmount+=$invcAmnt;
				}
				
				$avgRate=number_format($totOrderAmount/$totOrderQnty,4,'.','');
				//$avgRate=$totOrderAmount/$totOrderQnty;
				$avgInvcRate=$totInvcAmount/$totInvcQnty;
			?>
            </tbody>     
            <tfoot>
            	<th colspan="4">Total</th>
                <th>
                    <input type="text" id="totOrderQnty" name="totOrderQnty" value="<? echo $totOrderQnty; ?>" class="text_boxes_numeric" style="width:70px" disabled> 
                </th>
                <th>
                    <input type="text" id="txtAvgRate" name="txtAvgRate" value="<? echo $avgRate; ?>" class="text_boxes_numeric" style="width:60px" disabled> 
                </th>
                <th>
                    <input type="text" id="totOrderAmount" name="totOrderAmount" value="<? echo $totOrderAmount; ?>" class="text_boxes_numeric" style="width:70px" disabled> 
                </th>
                <th>
                    <input type="text" id="totInvcQnty" name="totInvcQnty" value="<? echo $totInvcQnty; ?>" class="text_boxes_numeric" style="width:70px" disabled> 
                </th>
                <th>
                    <input type="text" id="InvcAvgRate" name="InvcAvgRate" value="<? echo $avgInvcRate; ?>" class="text_boxes_numeric" style="width:60px" disabled> 
                </th>
                <th>
                    <input type="text" id="totInvcAmount" name="totInvcAmount"  value="<? echo $totInvcAmount; ?>" class="text_boxes_numeric" style="width:70px" disabled> 
                </th>
            </tfoot>
        </table>
        <div style="width:880px;margin-top:10px" align="center">
            <input type="button" class="formbutton" onClick="fnc_close()" value="Close" style="width:100px"/>
            <input type="hidden" id="colorSize_infos" />
        </div>
	</fieldset>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="additional_info_popup")
{
	echo load_html_head_contents("Invoice Additional Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//$data=explode('*',$data);
?>
	<script>
	
		var additional_info='<?  echo $data; ?>';
	
		if(additional_info != "")
		{ 
			additional_info=additional_info.split('_');
			
			$(document).ready(function(e) {
				$('#cargo_delivery_to').val( additional_info[0]);
				$('#place_of_delivery').val( additional_info[1]);
				$('#txt_main_mark').val( additional_info[2]);
				$('#txt_side_mark').val( additional_info[3]);
				$('#txt_net_weight').val( additional_info[4]);
				$('#txt_gross_weight').val( additional_info[5]);
				$('#txt_cbm').val( additional_info[6]);
			}); 
		}
	
	
		function submit_additional_info()
		{
			var additional_infos =   $('#cargo_delivery_to').val()+ '_'+$('#place_of_delivery').val()+ '_'+$('#txt_main_mark').val()+ '_'+$('#txt_side_mark').val()+ '_'+$('#txt_net_weight').val()+ '_'+$('#txt_gross_weight').val()+ '_'+$('#txt_cbm').val();
			var additional_infos_arr=additional_infos_data="";
			additional_infos_arr=additional_infos.split("_");
			for(var i=0;i<additional_infos_arr.length;i++)
			{
				additional_infos_data+=additional_infos_arr[i];
			}
			if(additional_infos_data!="") additional_infos=additional_infos; else additional_infos=additional_infos_data;
			$('#additional_infos').val( additional_infos );
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
	<div align="center" style="width:100%;" >
	<form name="invoiceadditionalinfo_1"  id="invoiceadditionalinfo_1" autocomplete="off">
		<table width="690" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
    		<input type="hidden" name="additional_infos" id="additional_infos" value="">
    
            <tr>
                <td width="150" align="right">Cargo Delivery To: &nbsp;</td>
                <td width="200">
                    <input type="text" name="cargo_delivery_to" id="cargo_delivery_to" value="" class="text_boxes" style="width:190px;"/>
                </td>
                <td width="150" align="right">Place of Delivery: &nbsp;</td>
                <td>
                    <input type="text" name="place_of_delivery" id="place_of_delivery" class="text_boxes" style="width:190px;"/>
                </td>			
            </tr>
            <tr>
            	<td align="right">Main Mark: &nbsp;</td>
                <td><input type="text" name="txt_main_mark" id="txt_main_mark" value="" class="text_boxes" style="width:190px;"/></td>
                <td align="right">Side Mark: &nbsp;</td>
                <td><input type="text" name="txt_side_mark" id="txt_side_mark" value="" class="text_boxes" style="width:190px;"/></td>
            </tr>
            <tr>
            	<td align="right">Net Weight: &nbsp;</td>
                <td><input type="text" name="txt_net_weight" id="txt_net_weight" value="" class="text_boxes_numeric" style="width:190px;"/></td>
                <td align="right">Gross Weight: &nbsp;</td>
                <td><input type="text" name="txt_gross_weight" id="txt_gross_weight" value="" class="text_boxes_numeric" style="width:190px;"/></td>
            </tr>
            <tr>
            	<td align="right">CBM: &nbsp;</td>
                <td><input type="text" name="txt_cbm" id="txt_cbm" value="" class="text_boxes_numeric" style="width:190px;"/></td>
                <td align="right">&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            
            <tr>
                <td align="center" colspan="4" class="button_container">
                    <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="submit_additional_info();" style="width:100px" />
                </td>	  
            </tr>
    	</table>
    </form>
	</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}


if ($action=="print_invoice")
{
    extract($_REQUEST);
	$company_name_sql=sql_select( "select id, company_name, company_short_name, contract_person, plot_no, level_no, road_no, block_no, city, country_id from lib_company");
	foreach($company_name_sql as $row)
	{
		$company_name_arr[$row[csf("id")]]["company_name"]=$row[csf("company_name")];
		$company_name_arr[$row[csf("id")]]["company_short_name"]=$row[csf("company_short_name")];
		$company_name_arr[$row[csf("id")]]["contract_person"]=$row[csf("contract_person")];
		$company_name_arr[$row[csf("id")]]["plot_no"]=$row[csf("plot_no")];
		$company_name_arr[$row[csf("id")]]["level_no"]=$row[csf("level_no")];
		$company_name_arr[$row[csf("id")]]["road_no"]=$row[csf("road_no")];
		$company_name_arr[$row[csf("id")]]["block_no"]=$row[csf("block_no")];
		$company_name_arr[$row[csf("id")]]["city"]=$row[csf("city")];
		$company_name_arr[$row[csf("id")]]["country_id"]=$row[csf("country_id")];
		
	}
	
	//var_dump($company_name_arr[1]);
	
	$applicant_sql=sql_select( "select a.id, a.buyer_name, a.short_name, a.address_1, b.party_type from lib_buyer a,  lib_buyer_party_type b where a.id=b.buyer_id and b.party_type in(22,23,4,5,6,100)");
	foreach($applicant_sql as $row)
	{
		$buyer_name_arr[$row[csf("id")]]["buyer_name"]=$row[csf("buyer_name")];
		$buyer_name_arr[$row[csf("id")]]["address_1"]=$row[csf("address_1")];
	}
	
	
	$inv_master_data=sql_select("select id, benificiary_id, buyer_id, location_id, invoice_no, invoice_date, exp_form_no, exp_form_date, is_lc, lc_sc_id,  bl_no, feeder_vessel, inco_term, inco_term_place, shipping_mode, port_of_entry, port_of_loading, port_of_discharge, main_mark, side_mark, net_weight, gross_weight, cbm_qnty, discount_ammount, bonus_ammount, commission, total_carton_qnty, bl_date from com_export_invoice_ship_mst where id=$data");
	if($inv_master_data[0][csf("is_lc")]==1)
	{
		$lc_sc_data=sql_select("select id, export_lc_no, lc_date, notifying_party, consignee, issuing_bank_name, negotiating_bank, lien_bank, pay_term, applicant_name from com_export_lc where id='".$inv_master_data[0][csf("lc_sc_id")]."' ");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_id=$row[csf("id")];
			$lc_sc_no=$row[csf("export_lc_no")];
			$lc_sc_date=change_date_format($row[csf("lc_date")]);
			$notifying_party=$row[csf("notifying_party")];
			$consignee=$row[csf("consignee")];//also notify party
			$issuing_bank_name=$row[csf("issuing_bank_name")];
			$negotiating_bank=$row[csf("lien_bank")];
			$pay_term_id=$row[csf("pay_term")];
			$applicant_name=$row[csf("applicant_name")];
		}
		
			$cate_hs_sql=sql_select("select wo_po_break_down_id, fabric_description, category_no, hs_code from com_export_lc_order_info where com_export_lc_id='".$inv_master_data[0][csf("lc_sc_id")]."'");
			foreach($cate_hs_sql as $row)
			{
				$order_la_data[$row[csf("wo_po_break_down_id")]]["category_no"]=$row[csf("category_no")];
				$order_la_data[$row[csf("wo_po_break_down_id")]]["hs_code"]=$row[csf("hs_code")];
				$order_la_data[$row[csf("wo_po_break_down_id")]]["fabric_description"]=$row[csf("fabric_description")];
				$all_order_id.=$row[csf("wo_po_break_down_id")].", ";
			}
	}
	else
	{
		$lc_sc_data=sql_select("select id, contract_no, contract_date, notifying_party, consignee, lien_bank, pay_term, applicant_name from com_sales_contract where id='".$inv_master_data[0][csf("lc_sc_id")]."'  and status_active=1");
		foreach($lc_sc_data as $row)
		{
			$lc_sc_id=$row[csf("id")];
			$lc_sc_no=$row[csf("contract_no")];
			$lc_sc_date=change_date_format($row[csf("contract_date")]);
			$notifying_party=$row[csf("notifying_party")];
			$consignee=$row[csf("consignee")];//also notify party
			$issuing_bank_name="";
			$negotiating_bank=$row[csf("lien_bank")];
			$pay_term_id=$row[csf("pay_term")];
			$applicant_name=$row[csf("applicant_name")];
		}
		
		$cate_hs_sql=sql_select("select wo_po_break_down_id, fabric_description, category_no, hs_code from com_sales_contract_order_info where com_sales_contract_id='".$inv_master_data[0][csf("lc_sc_id")]."' and status_active=1");
		foreach($cate_hs_sql as $row)
		{
			$order_la_data[$row[csf("wo_po_break_down_id")]]["category_no"]=$row[csf("category_no")];
			$order_la_data[$row[csf("wo_po_break_down_id")]]["hs_code"]=$row[csf("hs_code")];
			$order_la_data[$row[csf("wo_po_break_down_id")]]["fabric_description"]=$row[csf("fabric_description")];
			$all_order_id.=$row[csf("wo_po_break_down_id")].", ";
		}
	}
	
	$all_order_id=chop($all_order_id, " , ");
	
	$ex_ctn_arr=return_library_array( "select po_break_down_id, sum(total_carton_qnty) as total_carton_qnty from pro_ex_factory_mst where po_break_down_id in($all_order_id) and status_active=1 group by po_break_down_id",'po_break_down_id','total_carton_qnty');

	if($all_order_id!="")
	{
		if($db_type==0)
		{
			$art_num_arr=return_library_array( "select po_break_down_id, min(article_number) as article_number from wo_po_color_size_breakdown where po_break_down_id in($all_order_id) and article_number!='' group by po_break_down_id",'po_break_down_id','article_number');
		}
		else
		{
			$art_num_arr=return_library_array( "select po_break_down_id, min(article_number) as article_number from wo_po_color_size_breakdown where po_break_down_id in($all_order_id) and article_number is not null group by po_break_down_id",'po_break_down_id','article_number');
		}	
	}
			
	//$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');

	?>
    <table id="" cellspacing="0" cellpadding="0" width="690" border="0">
        <tr>
            <td colspan="5" align="center" style="font-size:18px; font-weight:bold"><? echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_name"]; ?></td>
        </tr>
        <tr>
            <td colspan="5" align="center" style="font-size:14px; font-weight:bold"><? echo "Commercial Invoice"; ?></td>
        </tr>
        <tr>
            <td colspan="5" align="center">&nbsp;</td>
        </tr>
    </table>
    <table id="" cellspacing="0" cellpadding="0" width="690" border="0"  style="font-size:11px;">
        <tr>
            <td width="90" align="right" valign="top">Shipper :&nbsp;</td>
            <td width="250" align="left" valign="top">
			<?
            $comany_details="";
            echo $company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["company_name"]."<br>";
            $plot_no=$company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["plot_no"];
            $level_no=$company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["level_no"];
            $road_no=$company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["road_no"];
            $block_no=$company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["block_no"];
            $city=$company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["city"];
            $country_id=$company_name_arr[$inv_master_data[0][csf("benificiary_id")]]["country_id"];
            if($plot_no!="")  $comany_details= $plot_no.", ";
			if($level_no!="")  $comany_details.= $level_no.", ";
			if($road_no!="")  $comany_details.= $road_no.", ";
			if($block_no!="")  $comany_details.= $block_no.", ";
			if($city!="")  $comany_details.= "<br>".$city.", ";
			if($country_id!="")  $comany_details.=return_field_value( "country_name","lib_country","id='$country_id'")." . ";
			echo $comany_details;
            ?>
            </td>
            <td width="120" valign="top" align="right">
            Invoice No: &nbsp;<br> 
            Lc/Sc No: &nbsp;<br> 
            EXP NO: &nbsp; 
            </td>
            <td width="140" valign="top" align="left">
			<? 
				echo $inv_master_data[0][csf("invoice_no")]."<br>";
				echo $lc_sc_no."<br>";
				echo $inv_master_data[0][csf("exp_form_no")];
			?>
            </td>
            <td valign="top" align="left">
            Date: <? echo change_date_format($inv_master_data[0][csf("invoice_date")]);?><br>
            Date: <? echo $lc_sc_date;?><br>
            Date: <? if($inv_master_data[0][csf("exp_form_date")]!="" && $inv_master_data[0][csf("exp_form_date")]!="0000-00-00" ) echo change_date_format($inv_master_data[0][csf("exp_form_date")]);?><br>
           </td>
        </tr>
        <tr>
        	<td valign="top" align="right">Applicant :&nbsp;</td>
            <td valign="top"  align="left">
			<? 
			echo $buyer_name_arr[$applicant_name]["buyer_name"]."<br>"; 
			echo $buyer_name_arr[$applicant_name]["address_1"]."<br>";
			?>
            </td>
            <td valign="top" align="right">LC Issue Bank: &nbsp;</td>
            <td colspan="2" valign="top"  align="left">
			<?
				echo $issuing_bank_name; 
			?></td>
        </tr>
        <tr>
        	<td valign="top" align="right">Notify :&nbsp;</td>
            <td valign="top"  align="left" colspan="4">
			<? 
			/*$notifying_party_all="";
			$notifying_party_arr=explode(",",$notifying_party);
			foreach($notifying_party_arr as $bank_id)
			{
				$notifying_party_all.=$buyer_name_arr[$bank_id][2]["buyer_name"]." ";
				if($buyer_name_arr[$bank_id][2]["address_1"]!="")
				{
					$notifying_party_all.=$buyer_name_arr[$bank_id][2]["address_1"].".";
				}
				else
				{
					$notifying_party_all.="<br>";
				}
			}*/
			$notifying_party_all="";
			$notifying_party_arr=explode(",",$notifying_party);
			foreach($notifying_party_arr as $buyer_id)
			{
				if($buyer_name_arr[$buyer_id]["buyer_name"]!="")
				{
					$notifying_party_all.=$buyer_name_arr[$buyer_id]["buyer_name"]."<br>";
					if($buyer_name_arr[$buyer_id]["address_1"]!="")
					{
						$notifying_party_all.=$buyer_name_arr[$buyer_id]["address_1"]." ";
					}
					$notifying_party_all.="<br>";
				}
			}
			$notifying_party_all=chop($notifying_party_all, " <br> ");
			echo $notifying_party_all;
			?>
            </td>
            
        </tr>
        <tr>
        	<td valign="top" align="right">Also Notify :&nbsp;</td>
            <td valign="top"  align="left" colspan="4">
			<? 
			$notifying_also_party_all="";
			$consignee_arr=explode(",",$consignee);
			foreach($consignee_arr as $buyer_con_id)
			{
				if($buyer_name_arr[$buyer_con_id]["buyer_name"]!="")
				{
					$notifying_also_party_all.=$buyer_name_arr[$buyer_con_id]["buyer_name"]."<br>";
					if($buyer_name_arr[$buyer_con_id]["address_1"]!="")
					{
						$notifying_also_party_all.=$buyer_name_arr[$buyer_con_id]["address_1"]." ";
					}
					$notifying_also_party_all.="<br>";
				}
			}
			$notifying_also_party_all=chop($notifying_also_party_all, " <br> ");
			echo $notifying_also_party_all;
			?>
            </td>
        </tr>
        <tr>
        	<td valign="top" align="right">Country Of Orgin :&nbsp; </td>
            <td valign="top"  align="left">
			<?  echo "Bangladesh";
			?>
            </td>
            <td valign="top" align="right">Negotiating Bank: &nbsp;</td>
            <td colspan="2" valign="top"  align="left">
			<?
			$bank_details=sql_select("select id,bank_name,address from lib_bank where id in($negotiating_bank)");
			echo $bank_details[0][csf("bank_name")]." ".$bank_details[0][csf("address")]; 
			?></td>
        </tr>
        <tr>
        	<td valign="top" align="right">HAWB/BL No :&nbsp;</td>
            <td valign="top"  align="left">
			<? 
				echo $inv_master_data[0][csf("bl_no")];
			?>
            </td>
            <td valign="top" align="right">Inco Term: &nbsp;</td>
            <td colspan="2" valign="top"  align="left">
			<?
				echo $incoterm[$inv_master_data[0][csf("inco_term")]]; 
			?></td>
           
        </tr>
        <tr>
        	<td valign="top" align="right">Port Of Loading: &nbsp;</td>
            <td valign="top"  align="left">
			<?
				echo $inv_master_data[0][csf("port_of_loading")]; 
			?></td>
            <td valign="top" align="right">HAWB/BL Date :&nbsp;</td>
            <td valign="top" colspan="2"  align="left">
			<? 
				if($inv_master_data[0][csf("bl_date")]!="" && $inv_master_data[0][csf("bl_date")]!="0000-00-00") echo change_date_format($inv_master_data[0][csf("bl_date")]);
			?>
            </td>
        	
        </tr>
        <tr>
        	<td valign="top" align="right">
            Port Of Discharg: &nbsp;
            </td>
            <td valign="top"  align="left">
			<?
				echo $inv_master_data[0][csf("port_of_discharge")]."<br>"; 
			?>
            </td>
        	<td valign="top" align="right">Fedder Vessel :&nbsp;</td>
            <td valign="top" colspan="2" align="left">
			<? 
				echo $inv_master_data[0][csf("feeder_vessel")];
			?>
            </td>
        </tr>
        <tr>
        	<td valign="top" align="right">Payment Terms :&nbsp;</td>
            <td valign="top"  align="left"><? echo $pay_term[$pay_term_id];?></td>
            <td valign="top" align="right">Mode Of Shipment: &nbsp;</td>
            <td valign="top"  align="left"><? echo $shipment_mode[$inv_master_data[0][csf("shipping_mode")]];?></td>
        </tr>
    </table>
    <br>
    <table id="" cellspacing="0" cellpadding="0" border="1" rules="all" width="690" class="rpt_table"  style="font-size:9px;" >
        <thead>
            <tr>
                <th width="90" rowspan="2">Shipping Mark</th>
                <th colspan="3">Description</th>
                <th width="65" rowspan="2">Style No.</th>
                <th width="45" rowspan="2">Art No.</th>
                <th width="30" rowspan="2" >Category</th>
                <th width="30" rowspan="2">Hs Code</th>
                <th colspan="2">Qnty</th>
                <th width="40" rowspan="2">Ctns Qnty</th>
                <th width="30" rowspan="2">Unit Price</th>
                <th rowspan="2">Amount</th>
            </tr>
            <tr>
            	<th width="65">Po No</th>
                <th width="75">Description</th>
                <th width="75">Description</th>
                <th width="40">Qnty</th>
                <th width="20">UOM</th>
            </tr>
        </thead>
        <tbody>
        <?
		
		$dtls_sql="select a.id as dtls_id, a.po_breakdown_id, a.current_invoice_rate, a.current_invoice_qnty, a.current_invoice_value, b.po_number, c.style_ref_no, c.gmts_item_id, c.order_uom from  com_export_invoice_ship_dtls a,  wo_po_break_down b, wo_po_details_master c where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 and a.mst_id=$data";
		
		
		//echo $dtls_sql; die;
		$result=sql_select($dtls_sql);
		$row_span=count($result)+2;
		if($inv_master_data[0][csf("discount_ammount")]>0) $row_span=$row_span+1;
		if($inv_master_data[0][csf("bonus_ammount")]>0)  $row_span=$row_span+1;
		if($inv_master_data[0][csf("commission")]>0)  $row_span=$row_span+1;
		$i=1;
		$main_mark_arr=explode(",",$inv_master_data[0][csf("main_mark")]);
		$side_mark_arr=explode(",",$inv_master_data[0][csf("side_mark")]);
		foreach($result as $row)
		{
			?>
            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<?
				if($i==1)
				{
					?> 
                	<td width="90" rowspan="<? echo $row_span; ?>" valign="top"><p><span style="text-decoration:underline;">Main Mark</span><br>
					<?
					$all_main_mark="";
					foreach($main_mark_arr as $val)
					{
						$all_main_mark.=$val."<br>";
					}
					$all_main_mark=chop($all_main_mark, " <br> ");
					echo  $all_main_mark;
					?><br><span style="text-decoration:underline;">Side Mark</span><br>
					<? 
					$all_side_mark="";
					foreach($side_mark_arr as $val)
					{
						$all_side_mark.=$val."<br>";
					}
					$all_side_mark=chop($all_side_mark, " <br> ");
					echo  $all_side_mark;
					?></p></td>
                	<?
                }
				?>
                <td width="65"><p><? echo $row[csf("po_number")]; ?>&nbsp;</p></td>
                <td width="75"><p><? echo $garments_item[$row[csf("gmts_item_id")]]; ?>&nbsp;</p></td>
                <td width="75"><p><? echo $order_la_data[$row[csf("po_breakdown_id")]]["fabric_description"]; ?>&nbsp;</p></td>
                <td width="65"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
                <td width="45"><p><? echo $art_num_arr[$row[csf('po_breakdown_id')]]; ?>&nbsp;</p></td>
                <td width="30" align="center"><p><? echo $order_la_data[$row[csf('po_breakdown_id')]]["category_no"]; ?>&nbsp;</p></td>
                <td width="30" align="center"><p><? echo  $order_la_data[$row[csf('po_breakdown_id')]]["hs_code"]; ?>&nbsp;</p></td>
                <td width="40" align="right"><? echo number_format($row[csf('current_invoice_qnty')],2); ?></td>
                <td  width="27" style="padding-left:3px;"><p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></p></td>
                <td align="right" width="40"><? echo number_format($ex_ctn_arr[$row[csf('po_breakdown_id')]],2); ?></td>
                <td align="right" width="30"><? echo number_format($row[csf("current_invoice_rate")],2); ?></td>
                <td align="right"><? echo number_format($row[csf("current_invoice_value")],2); ?></td>
            </tr>
            <?
			$total_value+=$row[csf("current_invoice_value")];
			$total_qnty+=$row[csf("current_invoice_qnty")];
			$last_uom=$unit_of_measurement[$row[csf('order_uom')]];
			$total_carton_qnty+=$ex_ctn_arr[$row[csf('po_breakdown_id')]];
			$i++;
		}
		?>
        	<tr bgcolor="#FFFFCC">
                <td width="65"><p>&nbsp;</p></td>
                <td width="75"><p>&nbsp;</p></td>
                <td width="75"><p>&nbsp;</p></td>
                <td width="65"><p>&nbsp;</p></td>
                <td width="45"><p>&nbsp;</p></td>
                <td width="30"><p>&nbsp;</p></td>
                <td width="30"><p>&nbsp;</p></td>
                <td width="40"><p>&nbsp;</p></td>
                <td align="right" width="30"><p>&nbsp;</p></td>
                <td align="right" width="30" colspan="2">Total Value</td>
                <td align="right"><? echo number_format($total_value,2); ?></td>
            </tr>
            <?
			if($inv_master_data[0][csf("discount_ammount")]>0)
			{
				?>
                <tr>
                    <td width="65"><p>&nbsp;</p></td>
                    <td width="75"><p>&nbsp;</p></td>
                    <td width="75"><p>&nbsp;</p></td>
                    <td width="65"><p>&nbsp;</p></td>
                    <td width="45"><p>&nbsp;</p></td>
                    <td width="30"><p>&nbsp;</p></td>
                    <td width="30"><p>&nbsp;</p></td>
                    <td width="40"><p>&nbsp;</p></td>
                    <td align="right" width="30"><p>&nbsp;</p></td>
                    <td align="right" width="30" colspan="2">Total Discount</td>
                    <td align="right"><? echo number_format($inv_master_data[0][csf("discount_ammount")],2); ?></td>
                </tr>
                <?
				$total_value=$total_value-$inv_master_data[0][csf("discount_ammount")];
			}
			
			if($inv_master_data[0][csf("bonus_ammount")]>0)
			{
				?>
                <tr>
                    <td width="65"><p>&nbsp;</p></td>
                    <td width="75"><p>&nbsp;</p></td>
                    <td width="75"><p>&nbsp;</p></td>
                    <td width="65"><p>&nbsp;</p></td>
                    <td width="45"><p>&nbsp;</p></td>
                    <td width="30"><p>&nbsp;</p></td>
                    <td width="30"><p>&nbsp;</p></td>
                    <td width="40"><p>&nbsp;</p></td>
                    <td align="right" width="30"><p>&nbsp;</p></td>
                    <td align="right" width="30" colspan="2">Total Bonus</td>
                    <td align="right"><? echo number_format($inv_master_data[0][csf("bonus_ammount")],2); ?></td>
                </tr>
                <?
				$total_value=$total_value-$inv_master_data[0][csf("bonus_ammount")];
			}
			if($inv_master_data[0][csf("commission")]>0)
			{
				?>
                <tr>
                    <td width="65"><p>&nbsp;</p></td>
                    <td width="75"><p>&nbsp;</p></td>
                    <td width="75"><p>&nbsp;</p></td>
                    <td width="65"><p>&nbsp;</p></td>
                    <td width="45"><p>&nbsp;</p></td>
                    <td width="30"><p>&nbsp;</p></td>
                    <td width="30"><p>&nbsp;</p></td>
                    <td width="40"><p>&nbsp;</p></td>
                    <td align="right" width="30"><p>&nbsp;</p></td>
                    <td align="right" width="30" colspan="2">Total Commission</td>
                    <td align="right"><? echo number_format($inv_master_data[0][csf("commission")],2); ?></td>
                </tr>
                <?
				$total_value=$total_value-$inv_master_data[0][csf("commission")];
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td width="65"><p>&nbsp;</p></td>
                <td width="75"><p>&nbsp;</p></td>
                <td width="75"><p>&nbsp;</p></td>
                <td width="65"><p>&nbsp;</p></td>
                <td width="45"><p>&nbsp;</p></td>
                <td width="30"><p>&nbsp;</p></td>
                <td width="30"><p>&nbsp;</p></td>
                <td width="40"><p>&nbsp;</p></td>
                <td align="right" width="30"><p>&nbsp;</p></td>
                <td align="right" width="30" colspan="2">Net Total</td>
                <td align="right"><? echo number_format($total_value,2); ?></td>
            </tr>
        </tbody>
    </table>
    <br>
    <table id="" cellspacing="0" cellpadding="0" border="1" rules="all" width="400" class="rpt_table"  style="font-size:10px;" >
    	<tr>
        	<td width="130" align="right">Total Ctns:&nbsp;</td>
            <td width="200" align="right"><? echo number_format($total_carton_qnty,2); ?></td>
            <td>Ctns</td>
        </tr>
        <tr>
        	<td  align="right">Total Quantity:&nbsp;</td>
            <td align="right"><? echo number_format($total_qnty,2); ?></td>
            <td><? echo $last_uom; ?></td>
        </tr>
        <tr>
        	<td align="right">Total Net Wt:&nbsp;</td>
            <td align="right"><? echo number_format($inv_master_data[0][csf("net_weight")],2); ?></td>
            <td>KG</td>
        </tr>
        <tr>
        	<td align="right">Total Gross Wt:&nbsp;</td>
            <td align="right"><? echo number_format($inv_master_data[0][csf("gross_weight")],2); ?></td>
            <td>KG</td>
        </tr>
        <tr>
        	<td align="right">Total CBM:&nbsp;</td>
            <td align="right"><? echo number_format($inv_master_data[0][csf("cbm_qnty")],2); ?></td>
            <td>CBM</td>
        </tr>
    </table>
    
<?
exit();
}

?>


 