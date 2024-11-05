<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if($db_type==2 || $db_type==1 )
{
	$select_date=" to_char(a.insert_date,'YYYY')";
	$group_concat="wm_concat";
}
else if ($db_type==0)
{
	$select_date=" year(a.insert_date)";
	$group_concat="group_concat";
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );     	 
	exit();
}

//--------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------


if($action=="lc_sc_search")
{
	echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value( strCon ) 
		{
			var splitSTR = strCon.split("_");
			var lc_id = splitSTR[0];
			var lc_no = splitSTR[1];
			$('#lc_id').val( lc_id );
			$('#lc_no').val( lc_no );
			parent.emailwindow.hide();
		}
		
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Year</th>
                    <th>LC / SC NO</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                </thead>
                <tbody>
                	<tr>
                        <td align="center">	
                    		<input type="text" style="width:130px" class="text_boxes" name="txt_year" id="txt_year" />
                        </td>
                        <td align="center">
                        	 <input type="text" style="width:130px" class="text_boxes" name="txt_sc_sc_no" id="txt_sc_sc_no" value="" />
                        </td>                 
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+'<? echo $cbo_search_by; ?>'+'**'+'<? echo $cbo_buyer_name; ?>'+'**'+document.getElementById('txt_year').value+'**'+document.getElementById('txt_sc_sc_no').value, 'lc_sc_search_list_view', 'search_div', 'lc_sc_statement_custom_controller', 'setFilterGrid(\'list_view\',-1)');fn_selected();" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
		</fieldset>
            <div style="margin-top:15px" id="search_div"></div>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if($action=="lc_sc_search_list_view")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($company,$cbo_search_by,$cbo_buyer_name,$year,$sc_sc_no)=explode('**',$data);
	?>
    <input type='hidden' id='lc_id' />
    <input type='hidden' id='lc_no' />
    <?
	
	$company = str_replace("'","",$company);
	$cbo_search_by = str_replace("'","",$cbo_search_by);
	$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
	if($cbo_buyer_name>0) $str_cond=" and buyer_name=$cbo_buyer_name";
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$lien_bank_arr=return_library_array( "select id,bank_name from lib_bank where is_deleted=0 and status_active=1 and lien_bank=1",'id','bank_name');
	if($cbo_search_by==1)
	{
		if($year!=""){$search_con=" and lc_year=$year";}
		if($sc_sc_no!=""){$search_con .= " and export_lc_no like('%$sc_sc_no')";}
		$sql = "select id,beneficiary_name,buyer_name,lien_bank,export_lc_no,lc_value, 1 as lc_sc from com_export_lc where beneficiary_name=$company and status_active=1 and is_deleted=0  $str_cond $search_con"; 
		
	}
	else
	{
		if($year!=""){$search_con=" and sc_year=$year";}
		if($sc_sc_no!=""){$search_con .= " and contract_no like('%$sc_sc_no')";}
		$sql = "select id,beneficiary_name,buyer_name,lien_bank,contract_no as export_lc_no,contract_value as lc_value, 2 as lc_sc from  com_sales_contract where beneficiary_name=$company and status_active=1 and is_deleted=0  $str_cond $search_con"; 
	}
	//echo $sql;die;
	$arr=array(0=>$company_arr,1=>$buyer_arr,2=>$lien_bank_arr);
	//function create_list_view( $table_id, $tbl_header_arr, $td_width_arr, $tbl_width, $tbl_height, $tbl_border, $query, $onclick_fnc_name, $onclick_fnc_param_db_arr, $onclick_fnc_param_sttc_arr,  $show_sl, $field_printed_from_array_arr,  $data_array_name_arr, $qry_field_list_array, $controller_file_path, $filter_grid_fnc, $fld_type_arr, $summary_flds, $check_box_all ,$new_conn )
	echo create_list_view("list_view", "Company,Buyer,Lien Bank,Lc/Sc No,Value","120,120,120,100","600","330",0, $sql , "js_set_value", "id,export_lc_no", "", 1, "beneficiary_name,buyer_name,lien_bank,0,0", $arr, "beneficiary_name,buyer_name,lien_bank,export_lc_no,lc_value", "","setFilterGrid(\"list_view\",-1);","0,0,0,0,2","",0) ;
	exit();	
}







//report generated here--------------------//
if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$txt_lc_sc=str_replace("'","",$txt_lc_sc);
	$txt_lc_sc_id=str_replace("'","",$txt_lc_sc_id);
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	/*$bank_sql=sql_select("select id, bank_name, address  from lib_bank where is_deleted=0 and status_active=1 and lien_bank=1");
	$bank_details=array();
	foreach($bank_sql as $row)
	{
		$bank_details[$row[csf("id")]]["bank_name"]=$row[csf("bank_name")];
		$bank_details[$row[csf("id")]]["address"]=$row[csf("address")];
	}*/
	ob_start();	
	?>
	<div style="width:950px;"> 
        <table width="900" cellpadding="0" cellspacing="0" id="caption">
            <tr>
                <td align="center" width="100%" colspan="20" class="form_caption" ><strong style="font-size:18px">Company Name:<? echo " ". $company_arr[$cbo_company_name]; ?></strong></td>
                </tr> 
                <tr>  
                <td align="center" width="100%" colspan="20" class="form_caption" ><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
            </tr>
        </table>
        <table width="700" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
            <thead>
                <th width="200">LC/SC No</th>
                <th width="150">LC/SC Date</th>
                <th width="150">LC/SC Value</th>
                <th >Gmts Qnty (Pcs)</th>
            </thead>
            <tbody>
         	<?	
		 	$str_cond="";
		 	if($cbo_buyer_name>0) $str_cond=" and a.buyer_name=$cbo_buyer_name";
            if($cbo_search_by==1)
            {
                $sql_lc_sc="select a.id as lc_sc_id, a.export_lc_no as lc_sc_no, a.lc_date as lc_sc_date, a.lc_value as lc_sc_value, sum(b.attached_qnty) as garments_qnty 
				from com_export_lc a,  com_export_lc_order_info b
				where a.id=b.com_export_lc_id and a.beneficiary_name='$cbo_company_name' and a.id in($txt_lc_sc_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
				group by a.id, a.export_lc_no, a.lc_date ,a.lc_value";
            }
            else
            {
                $sql_lc_sc="select a.id as lc_sc_id, a.contract_no as lc_sc_no, a.contract_date as lc_sc_date, a.contract_value as lc_sc_value, sum(b.attached_qnty) as garments_qnty  
				from com_sales_contract a,  com_sales_contract_order_info b
				where a.id=b.com_sales_contract_id and a.beneficiary_name='$cbo_company_name'and a.id in($txt_lc_sc_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
				group by a.id, a.contract_no, a.contract_date , a.contract_value";
            }
            //echo $sql_lc_sc;die;
            $result_lc_sc=sql_select($sql_lc_sc);
            $i=1; $tot_lc_value=0;
            foreach($result_lc_sc as $row)
            {
                ?>
                <tr>
                    <td><? echo $row[csf("lc_sc_no")]; ?></td>
                    <td align="center"><? echo change_date_format($row[csf("lc_sc_date")]); ?></td>
                    <td align="right"><? echo number_format($row[csf("lc_sc_value")],2); ?></td>
                    <td align="right"><? echo number_format($row[csf("garments_qnty")],2); ?></td>
                </tr>
                <?	
                $i++;
            }
            ?>
            <tbody>
        </table>
        <br>
        <table width="700"  border="0" align="left">
        	<tr>
            	<td>&nbsp;</td>
            </tr>
        </table>
        <br >
        <table width="700" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
            <thead>
            	<th>SL</th>
                <th width="200">BTB LC No</th>
                <th width="150">BTB LC Date</th>
                <th width="150">BTB LC Value</th>
                <th >Item Category</th>
            </thead>
            <tbody>
         	<?	
            if($cbo_search_by==1)
            {
                $sql_btb="select a.id as btb_id, a.lc_number as btb_lc_no, a.lc_date as lc_sc_date, a.item_category_id, sum(a.lc_value) as lc_sc_value
				from com_btb_lc_master_details a,  com_btb_export_lc_attachment b
				where a.id=b.import_mst_id and b.is_lc_sc=0 and a.importer_id='$cbo_company_name' and b.lc_sc_id in($txt_lc_sc_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
				group by a.id, a.lc_number, a.lc_date, a.item_category_id";
            }
            else
            {
                 $sql_btb="select a.id as btb_id, a.lc_number as btb_lc_no, a.lc_date as lc_sc_date, a.item_category_id, sum(a.lc_value) as lc_sc_value
				from com_btb_lc_master_details a,  com_btb_export_lc_attachment b
				where a.id=b.import_mst_id and b.is_lc_sc=1 and a.importer_id='$cbo_company_name' and b.lc_sc_id in($txt_lc_sc_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
				group by a.id, a.lc_number, a.lc_date, a.item_category_id";
            }
            //echo $sql_btb;die;
            $result_btb=sql_select($sql_btb);
            $i=1;
            foreach($result_btb as $row)
            {
                 if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td align="center"><? echo $i; ?></td>
                    <td><? echo $row[csf("btb_lc_no")]; ?></td>
                    <td align="center"><? echo change_date_format($row[csf("lc_sc_date")]); ?></td>
                    <td align="right"><? echo number_format($row[csf("lc_sc_value")],2); $total_btb_value+= $row[csf("lc_sc_value")];?></td>
                    <td><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                </tr>
                <?	
                $i++;
            }
            ?>
            </tbody>
            <tfoot>
            	<tr>
                	<th></th>
                    <th></th>
                    <th align="right">Total:</th>
                    <th align="right"><? echo number_format($total_btb_value,2); ?></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
        <br>
        <table width="700"  border="0" align="left">
        	<tr>
            	<td>&nbsp;</td>
            </tr>
        </table>
        <br >
        <table width="1510" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
            <thead>
            	<th width="30">SL</th>
                <th width="130">Invoice No</th>
                <th width="70">Invoice Date</th>
                <th width="200">Exp No</th>
                <th width="70">Exp Date</th>
                <th width="200">Order No</th>
                <th width="70">Invoice Qnty</th>
                <th width="70">Net Wgt</th>
                <th width="70">Gross Wgt</th>
                <th width="80">Invoice Value</th>
                <th width="70">Bill of Entry</th>
                <th width="70">Bill of Entry Date </th>
                <th width="60">Yarn Cons./Pcs</th>
                <th width="80">Total Yarn Used</th>
                <th width="100">FDBC/FDBP</th>
                <th >Remarks</th>
            </thead>
        </table>
		<div style="width:1510px; overflow-y:scroll; max-height:250px" id="scroll_body"> 
            <table width="1492" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="left" id="table_body">
                <tbody>  
                <?
				$order_num=return_library_array("select id, po_number from wo_po_break_down where status_active=1","id","po_number");
                if($cbo_search_by==1)
                {
					$fdbc_arr=return_library_array("select b.invoice_id, a.bank_ref_no from com_export_doc_submission_mst a,  com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and a.entry_form=40 and b.lc_sc_id=$txt_lc_sc_id and b.is_lc=1 and b.status_active=1 and a.status_active=1","invoice_id","bank_ref_no");	
					if($db_type==0)
					{
						$sql="select a.id as lc_sc_id, b.id as invoice_id, b.invoice_no, b.invoice_date, b.cons_per_pcs, b.remarks,b.exp_form_no,b.exp_form_date,b.shipping_bill_n,b.ship_bl_date,b.gross_weight,b.net_weight, sum(c.current_invoice_qnty) as invoice_qnty, sum(c.current_invoice_value) as invoice_value, group_concat(c.po_breakdown_id) as po_id_all
						from  com_export_lc a, com_export_invoice_ship_mst b, com_export_invoice_ship_dtls c 
						where a.id=b.lc_sc_id and b.id=c.mst_id and b.is_lc=1 and a.id in($txt_lc_sc_id)
						group by a.id, b.id, b.invoice_no, b.invoice_date, b.cons_per_pcs, b.remarks,b.exp_form_no,b.exp_form_date,b.shipping_bill_n,b.ship_bl_date,b.gross_weight,b.net_weight";
					}
					else
					{
						$sql="select a.id as lc_sc_id, b.id as invoice_id, b.invoice_no, b.invoice_date, b.cons_per_pcs, b.remarks,b.exp_form_no,b.exp_form_date,b.shipping_bill_n,b.ship_bl_date,b.gross_weight,b.net_weight, sum(c.current_invoice_qnty) as invoice_qnty, sum(c.current_invoice_value) as invoice_value, listagg(cast(c.po_breakdown_id as varchar(4000)), ',') within group(order by c.po_breakdown_id)  as po_id_all
						from  com_export_lc a, com_export_invoice_ship_mst b, com_export_invoice_ship_dtls c 
						where a.id=b.lc_sc_id and b.id=c.mst_id and b.is_lc=1 and a.id in($txt_lc_sc_id)
						group by a.id, b.id, b.invoice_no, b.invoice_date, b.cons_per_pcs, b.remarks,b.exp_form_no,b.exp_form_date,b.shipping_bill_n,b.ship_bl_date,b.gross_weight,b.net_weight";
					}
                }
                else
                {
					$fdbc_arr=return_library_array("select b.invoice_id, a.bank_ref_no from com_export_doc_submission_mst a,  com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and a.entry_form=40 and b.lc_sc_id=$txt_lc_sc_id and b.is_lc=2 and b.status_active=1 and a.status_active=1","invoice_id","bank_ref_no");
					if($db_type==0)
					{
						$sql="select a.id as lc_sc_id, b.id as invoice_id, b.invoice_no, b.invoice_date, b.cons_per_pcs, b.remarks,b.exp_form_no,b.exp_form_date,b.shipping_bill_n,b.ship_bl_date,b.gross_weight,b.net_weight, sum(c.current_invoice_qnty) as invoice_qnty, sum(c.current_invoice_value) as invoice_value, group_concat(c.po_breakdown_id) as po_id_all
						from  com_sales_contract a, com_export_invoice_ship_mst b, com_export_invoice_ship_dtls c 
						where a.id=b.lc_sc_id and b.id=c.mst_id and b.is_lc=2 and a.id in($txt_lc_sc_id)
						group by a.id, b.id, b.invoice_no, b.invoice_date, b.cons_per_pcs, b.remarks,b.exp_form_no,b.exp_form_date,b.shipping_bill_n,b.ship_bl_date,b.gross_weight,b.net_weight";
					}
					else
					{
						$sql="select a.id as lc_sc_id, b.id as invoice_id, b.invoice_no, b.invoice_date, b.cons_per_pcs, b.remarks,b.exp_form_no,b.exp_form_date,b.shipping_bill_n,b.ship_bl_date,b.gross_weight,b.net_weight, sum(c.current_invoice_qnty) as invoice_qnty, sum(c.current_invoice_value) as invoice_value, listagg(cast(c.po_breakdown_id as varchar(4000)), ',') within group(order by c.po_breakdown_id)  as po_id_all
						from  com_sales_contract a, com_export_invoice_ship_mst b, com_export_invoice_ship_dtls c 
						where a.id=b.lc_sc_id and b.id=c.mst_id and b.is_lc=2 and a.id in($txt_lc_sc_id)
						group by a.id, b.id, b.invoice_no, b.invoice_date, b.cons_per_pcs, b.remarks,b.exp_form_no,b.exp_form_date,b.shipping_bill_n,b.ship_bl_date,b.gross_weight,b.net_weight";
					}
                }
                //echo $sql;die;
                $result=sql_select($sql);
                $i=1; $tot_lc_value=0;
                foreach($result as $row)
                {
					if ($i%2==0)  
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="30"><? echo $i; ?></td>
                        <td width="130"><p><? echo $row[csf("invoice_no")]; ?>&nbsp;</p></td>
                        <td width="70" align="center"><p><? if($row[csf("invoice_date")]!="" && $row[csf("invoice_date")]!="0000-00-00") echo change_date_format($row[csf("invoice_date")]); ?>&nbsp;</p></td>
                        <td width="200" align="left"><? echo $row[csf("exp_form_no")]; ?></td>
                        <td width="70" align="center"><?  echo change_date_format($row[csf("exp_form_date")]); ?></td>
                        <td width="200"><p>
						<?
						$order_id_arr=array_unique(explode(",",$row[csf("po_id_all")]));
						$po_data="";
						foreach($order_id_arr as $po_id)
						{
							$po_data.=$order_num[$po_id].",";
						}
						$po_data=chop($po_data," , ");
						echo $po_data; 
						?>&nbsp;</p></td>
                        <td width="70" align="right"><? echo number_format($row[csf("invoice_qnty")],2); $total_invoice_qnty+=$row[csf("invoice_qnty")]; ?></td>
                        <td width="70" align="right"><? echo $row[csf("net_weight")]; $total_net_weight+=$row[csf("net_weight")]; ?></td>
                        <td width="70" align="right"><? echo $row[csf("gross_weight")]; $total_gross_weight+=$row[csf("gross_weight")]; ?></td>
                        <td width="80" align="right"><? echo number_format($row[csf("invoice_value")],2); $total_invoice_value+=$row[csf("invoice_value")]; ?></td>
                        <td width="70" align="left"><?  echo $row[csf("shipping_bill_n")]; ?></td>
                        <td width="70" align="center"><?  echo change_date_format($row[csf("ship_bl_date")]); ?></td>
                        <td width="60" align="right"><? echo number_format($row[csf("cons_per_pcs")],2); $total_cons_per_pcs+=$row[csf("cons_per_pcs")]; ?></td>
                        <td width="80" align="right"><? $yarn_used=$row[csf("invoice_qnty")]*$row[csf("cons_per_pcs")]; echo number_format($yarn_used,2); $total_yarn_used+=$yarn_used;  ?></td>
                        <td width="100"><p><? echo $fdbc_arr[$row[csf("invoice_id")]]; ?>&nbsp;</p></td>
                        <td ><p><? echo $row[csf("remarks")]; ?>&nbsp;</p></td>
					</tr>
					<?	
					$i++;
                }
                ?>
                </tbody>
            </table>
		</div>
        <table width="1510" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
            <tfoot>
            	<th width="30">&nbsp;</th>
                <th width="130">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="200">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="200" align="right">Total:</th>
                <th width="70" align="right" id="value_total_invoice_qnty"><? echo number_format($total_invoice_qnty,2); ?></th>
                <th width="70" align="right" id="value_total_net_weight"><? echo number_format($total_net_weight,2); ?></th>
                <th width="70" align="right" id="value_total_gross_weight"><? echo number_format($total_gross_weight,2); ?></th>
                <th width="80" align="right" id="value_total_invoice_value"><? echo number_format($total_invoice_value,2); ?></th>
                <th width="70">&nbsp;</th>
  				<th width="70">&nbsp;</th>
                <th width="60" align="right" id="value_total_cons_per_pcs"><? echo number_format($total_cons_per_pcs,2); ?></th>
                <th width="80" align="right" id="value_total_yarn_used"><? echo number_format($total_yarn_used,2); ?></th>
                <th width="100">&nbsp;</th>
                <th >&nbsp;</th>
            </tfoot>
        </table>
		
	</div>    
	<?	 
	
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

disconnect($con);
}
?>

