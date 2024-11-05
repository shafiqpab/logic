<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if($action=="lc_sc_search")
{		  
	echo load_html_head_contents("LC Search Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
    <script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
		
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
				
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
				var splitSTR = strCon.split("_");
				var str = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				var isLcSc = splitSTR[3];
				
				toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
				
				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );
					selected_no.push(str);					
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 ); 
					selected_no.splice( i, 1 );
				}
				var id = ''; var name = ''; var job = ''; var num='';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
					num += selected_no[i] + ','; 
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 );
				num 	= num.substr( 0, num.length - 1 ); 
				
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name ); 
				$('#txt_selected_no').val( num );
				$('#is_lc_or_sc').val( isLcSc );
		}
		
    </script>
    <body>
        <div align="center" style="width:100%;" >
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="740" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>                	 
						<th colspan=2>BTB Date Range</th>
						
 						<th>
                       		<input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  />
							<input type='hidden' id='txt_selected_id' />
							<input type='hidden' id='txt_selected' />
							<input type='hidden' id='txt_selected_no' />
							<input type='hidden' id='is_lc_or_sc' />
						</th>
					</tr>
				</thead>
				<tbody>
					<tr align="center">
						<td  align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px;" placeholder="From date"/>                                                              
						</td>
						<td>
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px;"placeholder="To date"/>
						</td>
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( <? echo $bank_name; ?>+'_'+<? echo $company; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_lcSc_search_list_view', 'search_div', 'submission_of_bill_of_entry_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />				
						</td>
					</tr>
					<tr>
						<td colspan="3" align="center"><? echo load_month_buttons(1);  ?></td>
					</tr>
 				</tbody>
			 </tr>         
			</table>
                <div align="center" valign="top" style="margin-top:5px" id="search_div"> </div> 
            </form>
        </div>
       <script>
        
       </script>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
	</html>
    <?
	exit();
}

if($action=="create_lcSc_search_list_view")
{
 	$ex_data = explode("_",$data);
	//$txt_search_by = $ex_data[0];
	//$txt_search_common = trim($ex_data[1]);
	$bank_id = trim($ex_data[0]);
	$company = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	
		$btb_date_cond="";

		$issuing_bank_cond=" and a.issuing_bank_id in($bank_id)";

		if($db_type==2)
		{
			if( $txt_date_from!="" &&  $txt_date_to!="") $btb_date_cond= " and a.lc_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";else  $btb_date_cond="";

		}
		else if($db_type==0)
		{
			if( $txt_date_from!="" &&  $txt_date_to!="" ) $btb_date_cond= " and a.lc_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";else $btb_date_cond="";
		}
		
		$sql = "select a.id,a.importer_id,a.lc_number,a.lc_value, 1 as lc_sc,a.issuing_bank_id,b.import_invoice_id,b.pi_id,b.current_acceptance_value,b.domestic_acceptance_value,c.invoice_no,c.invoice_date 
		from com_btb_lc_master_details a, com_import_invoice_dtls b, com_import_invoice_mst c 
		where a.id=b.btb_lc_id and b.import_invoice_id=c.id and a.importer_id=$company and a.status_active=1 and b.is_deleted=0 and c.status_active=1 $issuing_bank_cond $btb_date_cond"; //die;
	
	//echo $sql;die;
	$arr=array(0=>$company_arr, 2=>$bank_arr);
    echo create_list_view("list_view", "LC No,Lc Value,Invoice No,Invoice Value","120,100,120,100","600","260",0, $sql , "js_set_value", "id,lc_number,lc_value,invoice_no,current_acceptance_value", "", 1, "0,0,0,0,0", $arr, "lc_number,lc_value,invoice_no,current_acceptance_value", "","","0,0,0,2","",1) ;
    

	exit();	
}


if ($action=="report_generate")
{
	extract($_REQUEST);
	//ob_start();
	//echo $cbo_company_id;
	$user_arr = return_library_array("select id,user_name from user_passwd ","id","user_name");
	$issueBankrArr = return_library_array("select id,bank_name from lib_bank ","id","bank_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	
	?>
	<div id=""  style="height:auto; width:750px; margin:0 auto; padding:0; text-align:left;">
		<fieldset style="text-align:center;">
			<table width="630px" align="left">
			<?
				$company_library=sql_select("select id, company_name, plot_no, level_no,road_no,city from lib_company where id=".$cbo_company_id."");
				foreach( $company_library as $row)
				{
				?>
					<tr>
						<td colspan="37" align="center" style="font-size:22px"><center><strong><? echo $row[csf('company_name')];?></strong></center></td>
					</tr>
				<?
				}
			?>
				<tr>
					<td colspan="37" align="center" style="font-size:18px"><center><strong><u><? echo $report_title; ?> Report</u></strong></center></td>
				</tr>
				<tr>
					<td><input type="button" name="print" id="print" value="Print" onClick="get_invoice_ids('tbl_body',1)" style="width:120px" class="formbutton" /></td>
                    <td><input type="button" name="print2" id="print2" value="Pad Print" onClick="get_invoice_ids('tbl_body',2)" style="width:120px" class="formbutton" /></td>
				</tr>
			</table>
			<div style="width:750px;">
				<table  cellspacing="0" width="660px"  border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
				<thead>			
					<th width="30" align="center">Check</th>
					<th width="50" align="center">SL</th>
					<th width="130" align="center">LC No</th>
					<th width="100" align="center">Bill Of Entry No</th>
					<th width="100" align="center">Bill of Entry Date</th>
					<th width="100" align="center">Bill Value</th>
					<th align="center"><p>Supplier</p></th>
					</thead>
				</table>
			</div>
			<div style="width:680px; overflow-y: scroll; max-height:300px;" id="scroll_body">
				<table cellspacing="0" width="660px"  border="1" rules="all" class="rpt_table" id="tbl_body" align="left">
					<?
					$cbo_company=str_replace("'","",$cbo_company_id);
					$cbo_issue=str_replace("'","",$cbo_issue_banking);
					$cbo_supplier=str_replace("'","",$cbo_supplier_id);
					$from_date=str_replace("'","",$txt_date_from);
					$to_date=str_replace("'","",$txt_date_to);
					
					$txt_lc_sc=str_replace("'","",$txt_lc_sc);
					$txt_lc_sc_id=str_replace("'","",$txt_lc_sc_id);
					//echo $txt_lc_sc.jahid;die;

					//echo $pending_type;die;

					if ($cbo_company==0) $company_id =""; else $company_id =" and d.importer_id=$cbo_company ";
					if ($cbo_issue==0) $issue_banking =""; else $issue_banking =" and d.issuing_bank_id=$cbo_issue ";

					if ($txt_lc_sc_id!="") $lc_sc_id_cond =" and d.id in($txt_lc_sc_id)";
					

					if($db_type==2)
					{
						if( $from_date!="" &&  $to_date!="") $btb_date_cond= " and d.lc_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";else  $btb_date_cond="";

					}
					else if($db_type==0)
					{
						if( $from_date!="" &&  $to_date!="" ) $btb_date_cond= " and d.lc_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";else $btb_date_cond="";
					}

					$i=1;
					if($db_type==0)
					{
						$sql="Select a.id,  a.invoice_no, a.invoice_date, a.company_acc_date, a.bank_acc_date, a.bank_ref, a.shipment_date, a.bill_no, a.bill_of_entry_no, a.bill_of_entry_date, sum(b.current_acceptance_value) as current_acceptance_value, group_concat(distinct b.import_invoice_id) as import_invoice_id, max(d.id) as btb_lc_id, max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, sum(d.lc_value) as lc_value, max(d.lc_expiry_date) as lc_expiry_date			from
						com_import_invoice_mst a, com_import_invoice_dtls b, com_btb_lc_master_details d 
						where
						a.id=b.import_invoice_id and b.btb_lc_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_id $issue_banking $btb_date_cond $lc_sc_id_cond
						group by
						a.id,a.invoice_no,a.invoice_date,a.company_acc_date, a.bank_acc_date, a.bank_ref, a.shipment_date, a.bill_no, a.bill_of_entry_no, a.bill_of_entry_date, a.bank_ref order by a.id";
					}
					else if($db_type==2)
					{
						$sql="Select a.id,  a.invoice_no, a.invoice_date, a.company_acc_date, a.bank_acc_date, a.bank_ref, a.shipment_date, a.bill_no, a.bill_of_entry_no, a.bill_of_entry_date, sum(b.current_acceptance_value) as current_acceptance_value,  listagg ( b.import_invoice_id, ',')  within group (order by import_invoice_id) as import_invoice_id, max(d.id) as btb_lc_id, max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, sum(d.lc_value) as lc_value, max(d.lc_expiry_date) as lc_expiry_date			
						from
						com_import_invoice_mst a, com_import_invoice_dtls b, com_btb_lc_master_details d 
						where
						a.id=b.import_invoice_id and b.btb_lc_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_id $issue_banking $btb_date_cond $lc_sc_id_cond
						group by
						a.id,a.invoice_no,a.invoice_date,a.company_acc_date, a.bank_acc_date, a.bank_ref, a.shipment_date, a.bill_no, a.bill_of_entry_no,a.bill_of_entry_date,a.bank_ref order by a.id";
					}
					//echo $sql;//die;

					
					$sql_data = sql_select($sql);
					foreach($sql_data as $row)
					{
						$result_arr[$row[csf('id')]]['id']=$row[csf('id')];
						$result_arr[$row[csf('id')]]['invoice_no']=$row[csf('invoice_no')];
						$result_arr[$row[csf('id')]]['invoice_date']=$row[csf('invoice_date')];
						$result_arr[$row[csf('id')]]['company_acc_date']=$row[csf('company_acc_date')];
						$result_arr[$row[csf('id')]]['bank_acc_date']=$row[csf('bank_acc_date')];
						$result_arr[$row[csf('id')]]['bank_ref']=$row[csf('bank_ref')];
						$result_arr[$row[csf('id')]]['shipment_date']=$row[csf('shipment_date')];
						$result_arr[$row[csf('id')]]['bill_no']=$row[csf('bill_no')];
						$result_arr[$row[csf('id')]]['bill_of_entry_no']=$row[csf('bill_of_entry_no')];
						$result_arr[$row[csf('id')]]['bank_ref']=$row[csf('bank_ref')];
						$result_arr[$row[csf('id')]]['current_acceptance_value']=$row[csf('current_acceptance_value')];
						$result_arr[$row[csf('id')]]['import_invoice_id']=$row[csf('import_invoice_id')];
						$result_arr[$row[csf('id')]]['btb_lc_id']=$row[csf('btb_lc_id')];
						$result_arr[$row[csf('id')]]['issuing_bank_id']=$row[csf('issuing_bank_id')];
						$result_arr[$row[csf('id')]]['lc_number']=$row[csf('lc_number')];
						$result_arr[$row[csf('id')]]['lc_date']=$row[csf('lc_date')];
						$result_arr[$row[csf('id')]]['supplier_id']=$row[csf('supplier_id')];
						$result_arr[$row[csf('id')]]['lc_value']=$row[csf('lc_value')];
						$result_arr[$row[csf('id')]]['lc_expiry_date']=$row[csf('lc_expiry_date')];
						$result_arr[$row[csf('id')]]['bill_of_entry_date']=$row[csf('bill_of_entry_date')];
							
					}

					$i=1;
					$lc_check=array();
					foreach( $result_arr as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						$import_invoice_id=$row[('import_invoice_id')];
						$suppl_id=$row[('supplier_id')];

						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30" title="invoice id = <? echo $row[('id')]; ?>">
								<input type="checkbox" name="row_check_<? echo $i;?>" id="row_check_<? echo $i;?>"  data-invoice_id="<? echo $row[('id')]; ?>"/>
								<!-- <input type="hidden" name="invoice_id[]" id="invoice_id_<? //echo $i;?>" value="<? //echo $row[('id')]; ?>" />					 -->
							</td>
							<td width="50" align="center"><p><? echo $i; ?></p></td>
							<td width="130"><p><? echo $row[('lc_number')]; ?> &nbsp;</p></td>
							
							<td width="100"><p><? echo $row[('bill_of_entry_no')]; ?></p></td>

							<td width="100"><p><? echo change_date_format($row[('bill_of_entry_date')]); ?></p></td>
							<td width="100" align="right"><p><? echo number_format($row[('current_acceptance_value')],2); $tot_bill_value+=$row[('current_acceptance_value')]; ?></p></td>

							<td><p><? echo $supplierArr[$row[('supplier_id')]]; ?></p></td>

						</tr>
						<?
						$i++;
					}
					?>
					</table>
					<table cellspacing="0" width="660px"  border="1" rules="all" class="rpt_table" id="report_table_footer" >
						<tfoot>
							<th width="30">&nbsp;</th>
							<th width="50">&nbsp;</th>
							<th width="130" >&nbsp;</th>
							<th width="100" >&nbsp;</th>
							<th width="100" align="right">Total:</th>
							<th width="100" align="right"><? echo number_format($tot_bill_value,2);?></th>
							<th>&nbsp;</th>
						</tfoot>
				</table>
		
				<div align="left" style="font-weight:bold; margin-left:30px;"><? echo "User Id : ". $user_arr[$user_id] ." , &nbsp; THIS IS SYSTEM GENERATED STATEMENT, NO SIGNATURE REQUIRED ."; ?></div>

			</div>
		</fieldset>
    </div>
	<?
}

if($action=="print_submission_bill_entry")
{
	extract($_REQUEST);
	$datas= explode("*",$data);
	//print_r($datas);
	$company_id = $datas[0];
	$cbo_issue_banking = $datas[1];
	$invoice_ids = chop($datas[2], ",");
	$all_invoice_ids = chop($datas[3], ",");
	$rpt_type=$datas[4];

	$address = sql_select("select id,plot_no,level_no,road_no,block_no,country_id,city,zip_code,irc_no,tin_number,vat_number,bang_bank_reg_no from lib_company where id = $company_id");
	foreach($address as $row){
		$company_add[$row[csf('id')]]['plot_no'] = $row[csf('plot_no')];
		$company_add[$row[csf('id')]]['level_no'] = $row[csf('level_no')];
		$company_add[$row[csf('id')]]['road_no'] = $row[csf('road_no')];
		$company_add[$row[csf('id')]]['block_no'] = $row[csf('block_no')];
		$company_add[$row[csf('id')]]['country_id'] = $row[csf('country_id')];
		$company_add[$row[csf('id')]]['city'] = $row[csf('city')];
		$company_add[$row[csf('id')]]['zip_code'] = $row[csf('zip_code')];
		$company_add[$row[csf('id')]]['irc_no'] = $row[csf('irc_no')];
		$company_add[$row[csf('id')]]['tin_number'] = $row[csf('tin_number')];
		$company_add[$row[csf('id')]]['vat_number'] = $row[csf('vat_number')];
		$company_add[$row[csf('id')]]['bang_bank_reg_no'] = $row[csf('bang_bank_reg_no')];
	}
	//print_r($company);

	if($invoice_ids!=''){
		$invoice_id_cond =" and a.id in($invoice_ids)";
	}else{
		$invoice_id_cond =" and a.id in($all_invoice_ids)";
	}

	
		$sql_invoice_details="Select a.id,  a.invoice_no, a.invoice_date,a.bill_of_entry_no, a.bill_of_entry_date, sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id,d.id as btb_lc_id, d.importer_id, d.issuing_bank_id, d.lc_number, d.lc_date, d.supplier_id, d.lc_value			
		from
		com_import_invoice_mst a, com_import_invoice_dtls b, com_btb_lc_master_details d 
		where
		a.id=b.import_invoice_id and b.btb_lc_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.importer_id=$company_id and d.issuing_bank_id=$cbo_issue_banking $invoice_id_cond and b.current_acceptance_value is not null	and b.current_acceptance_value >0 group by a.id,  a.invoice_no, a.invoice_date,a.bill_of_entry_no, a.bill_of_entry_date, b.import_invoice_id,d.id, d.importer_id, d.issuing_bank_id, d.lc_number, d.lc_date, d.supplier_id, d.lc_value order by a.id";

		//$sql_invoice_details;
		//echo $sql_com;
	$result=sql_select($sql_invoice_details);

	//echo'select id, contact_person  as "contact_person", bank_name, branch_name, address from lib_bank where id='.$cbo_issue_banking;
	$sql_bank_info=sql_select('select id, contact_person, bank_name, branch_name, address, designation from lib_bank where id='.$cbo_issue_banking);
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row[csf("id")]]["contact_person"]=$row[csf("contact_person")];
		$bank_dtls_arr[$row[csf("id")]]["bank_name"]=$row[csf("bank_name")];
		$bank_dtls_arr[$row[csf("id")]]["branch_name"]=$row[csf("branch_name")];
		$bank_dtls_arr[$row[csf("id")]]["address"]=$row[csf("address")];
		$bank_dtls_arr[$row[csf("id")]]["designation"]=$row[csf("designation")];
	}
	
	//print_r($bank_dtls_arr);

	$company_name = return_field_value("company_name","lib_company","id=".$result[0][csf("importer_id")],"company_name");
	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$result[0][csf("supplier_id")],"supplier_name");
	$designation_array = return_library_array("select id,custom_designation from lib_designation","id","custom_designation");
	$supplier_name_array = return_library_array("select id,supplier_name from lib_supplier","id","supplier_name");
	
//print_r($supplier_name);
	foreach($result as $row)
	{
		$result_arr[$row[csf('id')]]['id']=$row[csf('id')];
		$result_arr[$row[csf('id')]]['invoice_no']=$row[csf('invoice_no')];
		$result_arr[$row[csf('id')]]['invoice_date']=$row[csf('invoice_date')];
		$result_arr[$row[csf('id')]]['company_acc_date']=$row[csf('company_acc_date')];
		$result_arr[$row[csf('id')]]['bank_acc_date']=$row[csf('bank_acc_date')];
		$result_arr[$row[csf('id')]]['bank_ref']=$row[csf('bank_ref')];
		$result_arr[$row[csf('id')]]['shipment_date']=$row[csf('shipment_date')];
		$result_arr[$row[csf('id')]]['bill_no']=$row[csf('bill_no')];
		$result_arr[$row[csf('id')]]['bill_of_entry_no']=$row[csf('bill_of_entry_no')];
		$result_arr[$row[csf('id')]]['bank_ref']=$row[csf('bank_ref')];
		$result_arr[$row[csf('id')]]['current_acceptance_value']=$row[csf('current_acceptance_value')];
		$result_arr[$row[csf('id')]]['import_invoice_id']=$row[csf('import_invoice_id')];
		$result_arr[$row[csf('id')]]['btb_lc_id']=$row[csf('btb_lc_id')];
		$result_arr[$row[csf('id')]]['issuing_bank_id']=$row[csf('issuing_bank_id')];
		$result_arr[$row[csf('id')]]['lc_number']=$row[csf('lc_number')];
		$result_arr[$row[csf('id')]]['lc_date']=$row[csf('lc_date')];
		$result_arr[$row[csf('id')]]['supplier_id']=$row[csf('supplier_id')];
		$result_arr[$row[csf('id')]]['lc_value']=$row[csf('lc_value')];
		$result_arr[$row[csf('id')]]['lc_expiry_date']=$row[csf('lc_expiry_date')];
		$result_arr[$row[csf('id')]]['bill_of_entry_date']=$row[csf('bill_of_entry_date')];
			
	}

	?>

    <table width="700" cellpadding="0" align="left" cellspacing="0" border="0">
        <?
		if($rpt_type==1)
		{
			?>
            <tr>
                <td width="26"></td>
                <td width="650" align="center">
                    <h1><? echo $company_name;?></h1>
                    <h3><? echo "Plot No:-".$company_add[$company_id]['plot_no'].",".$company_add[$company_id]['level_no'].",<br/> ".$company_add[$company_id]['road_no'].", ".$company_add[$company_id]['city'].", ".$country_array[$company_add[$company_id]['country_id']]; ?></h3>
                </td>
                <td width="25" ></td>
            </tr>
            <?
		}
		else
		{
			?>
            <tr>
                <td colspan="3" height="120">&nbsp;</td>
            </tr>
            <?
		}
		?>
        <tr>
            <td width="26"></td>
			<td width="650" align="left">Date : <? echo change_date_format($result[0][csf("application_date")]); 
			// echo $result[0][csf("btb_system_id")]; ?></td>
            <td width="25" ></td>
        </tr>
        <tr>
            <td colspan="3" height="5">&nbsp;</td>
        </tr>
        <tr>
            <td width="26" ></td>
            <td width="650" align="left">To </td>
            <td width="25" ></td>
        </tr>
        <tr>
            <td colspan="3" height="10">&nbsp;</td>
        </tr>
        <tr>
            <td width="26" ></td>
            <td width="650" align="left">
            <?
			echo $designation_array[$bank_dtls_arr[$cbo_issue_banking]["designation"]];
			if($designation_array[$bank_dtls_arr[$cbo_issue_banking]["designation"]]!="") echo "<br>";
			echo $bank_dtls_arr[$cbo_issue_banking]["contact_person"];
			if($bank_dtls_arr[$cbo_issue_banking]["contact_person"]!="") echo "<br>";
			echo $bank_dtls_arr[$cbo_issue_banking]["bank_name"];
			if($bank_dtls_arr[$cbo_issue_banking]["bank_name"]!="") echo "<br>";
			echo $bank_dtls_arr[$cbo_issue_banking]["branch_name"];
			if($bank_dtls_arr[$cbo_issue_banking]["branch_name"]!="") echo "<br>";
			echo $bank_dtls_arr[$cbo_issue_banking]["address"];
				//echo $designation_array[$bank_dtls_arr[$cbo_issue_banking]["designation"]]."<br>".$bank_dtls_arr[$cbo_issue_banking]["contact_person"]."<br>".$bank_dtls_arr[$cbo_issue_banking]["bank_name"]."<br>".$bank_dtls_arr[$cbo_issue_banking]["branch_name"]."<br>".$bank_dtls_arr[$cbo_issue_banking]["address"];
			?>

            </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="30"></td>
        </tr>
        <tr>
            <td width="26" ></td>
            <td width="650" align="left">Subject: Submission of Bill of Entries</td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="20"></td>
        </tr>
        <tr>
            <td width="26" ></td>
            <td width="650" align="left"> Dear Sir, </td>
            <td width="25" ></td>
        </tr>
        <tr>
            <td width="26" ></td>
            <td width="650" align="left">
				We are pleased to enclose copy of Bill of Entries as below for your acknowledgement and receipt.
            </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="15"></td>
        </tr>
        <tr>
            <td width="26" ></td>
            <td width="650" align="left">
			
			<div style="width:650px;">
				<table  cellspacing="0" width="660px"  border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
					<thead>	
						<th width="50" align="center">SL</th>
						<th width="130" align="center">LC No</th>
						<th width="100" align="center">Bill Of Entry No</th>
						<th width="100" align="center">Bill of Entry Date</th>
						<th width="100" align="center">Bill Value</th>
						<th align="center"><p>Supplier</p></th>
						</thead>
					</table>
				</div>
				<!--<div class="scroll_hidden" style="width:660px; max-height:300px; overflow:hidden;">				
					<div style="width:680px; overflow-y: scroll; max-height:300px; overflow-x:hidden;" id="scroll_body">-->
                   <!-- <div class="scroll_hidden" style="width:660px; overflow:hidden;">	-->			
					<!--<div style="width:680px; overflow-y: scroll;  overflow-x:hidden;" id="scroll_body">-->
						<table cellspacing="0" width="660px"  border="1" rules="all" class="rpt_table" id="tbl_body" align="left">
						<tbody>
							<?
								$i=1;
								$lc_check=array();
								foreach( $result_arr as $row)
								{
									if ($i%2==0)
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";
			
									$import_invoice_id=$row[('import_invoice_id')];
									$suppl_id=$supplier_name_array[$row[('supplier_id')]];
									
									//$result_arr[$row[csf('id')]]['supplier_id']
			
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="50" align="center"><p><? echo $i; ?></p></td>
										<td width="130"><p><? echo $row[('lc_number')]; ?> &nbsp;</p></td>
										
										<td width="100"><p><? echo $row[('bill_of_entry_no')]; ?></p></td>
			
										<td width="100"><p><? echo change_date_format($row[('bill_of_entry_date')]); ?></p></td>
										<td width="100" align="right"><p>
										<? echo number_format($row[('current_acceptance_value')],2); 
										$tot_bill_value+=$row[('current_acceptance_value')]; ?></p></td>
			
										<td><p><? echo $suppl_id;//$supplier_name_array[$suppl_id];//$supplier_name;//$supplierArr[$row[('supplier_id')]]; ?></p></td>
			
									</tr>
									<?
									$i++;
								}
							?>
						</tbody>
						<!-- <tfoot>
							<th width="50">&nbsp;</th>
							<th width="130" >&nbsp;</th>
							<th width="100" >&nbsp;</th>
							<th width="100" align="right">Total:</th>
							<th width="100" align="right"><? //echo $tot_bill_value;?></th>
							<th>&nbsp;</th>
							</tfoot> -->
						</table>
					<!--</div>-->
				<!--</div>-->
            </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="15"></td>
        </tr>

        <tr>
        	<td colspan="3" height="15"></td>
        </tr>
        
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
			Your earliest action will be highly appreciated in this regard.<br/><br/>
            Thanking You,
            </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="80"></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
			<?

			//echo $company_name;

			?></td>
            <td width="25" ></td>
        </tr>
		<tr>
			<td width="25" ></td>
			<td width="650" align="left">
			----------------------------
			</td>
			<td width="25" ></td>
		</tr>
		<tr>
			<td width="25" ></td>
			<td width="650" align="left">
			Authorized Signature
			</td>
			<td width="25" ></td>
		</tr>
       <tr>
        	<td colspan="3" height="50"></td>
       </tr>
    </table>
    <?
	exit();
}
?>
