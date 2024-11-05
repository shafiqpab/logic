<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == '' ) header('location:login.php');
require_once('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if($action=="invoice_popup_search")
{
	echo load_html_head_contents("Popup Info", "../../../", 1, 1,$unicode,'1','');
	extract($_REQUEST);
	?>

	<script>

		function js_set_value(data)
		{
			// alert(data);
			var data_string=data.split('_');
            $('#invoice_no').val(data_string[1]);
			$('#hidden_invoice_id').val(data_string[0]);
			$('#company_id').val(data_string[2]);
			// $('#buyer_name').val(data_string[1]);
			// $('#invoice_no').val(data_string[2]);
			// $('#invoice_date').val(data_string[3]);
			// $('#invoice_value').val(data_string[4]);
			//  alert(data_string[0]+'='+data_string[1]+'='+data_string[2]+'='+data_string[3]+'='+data_string[4]+'='+data_string[5]);
		  parent.emailwindow.hide();
		}


	function fn_show_list(){
		var invoice_no=$('#txt_invoice_search').val();
		if(invoice_no=='')
		{
			if(form_validation('txt_date_from*txt_date_to','Invoice Date Range*Invoice Date Range')==false)
			{
				return;
			}
		}

		show_list_view ( document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_invoice_search').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_string_search_type').value+'**'+document.getElementById('cbo_type_name').value,'invoice_search_list_view', 'search_div', 'cnf_bill_report_controller', 'setFilterGrid(\'list_view\',-1)')
	}
    

    </script>

	</head>

	<body>
	<div align="center" style="width:900px;">
		<form name="searchexportinformationfrm"  id="searchexportinformationfrm">
			<fieldset style="width:880px;">
				<table cellpadding="0" cellspacing="0" width="860" class="rpt_table" border="1" rules="all">
				<input type="hidden" name="hidden_invoice_id" id="hidden_invoice_id" value="" />
				<input type="hidden" name="company_id" id="company_id" value="" />
				<!-- <input type="hidden" name="buyer_name" id="buyer_name" value="" /> -->
				<input type="hidden" name="invoice_no" id="invoice_no" value="" />
				<!-- <input type="hidden" name="invoice_date" id="invoice_date" value="" />
				<input type="hidden" name="invoice_value" id="invoice_value" value="" /> -->
				<input type="hidden" name="cbo_type_name" id="cbo_type_name" value="<?= $cbo_type_name;?>" />
					<thead>
						<tr>
                            <th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",1 ); ?></th>
                        </tr>
						<tr>
							<th>Company</th>
							<th>Buyer</th>
							<th>Invoice Date Range</th>
							<th>Enter Invoice No</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							</th>
						</tr>
					</thead>
					<tr class="general">
						<td>
							
							<?
								echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--- Select Company ---", $cbo_country_id, "" );
							?>
						</td>
						<td id="buyer_td_id">
							<?
							echo create_drop_down("cbo_buyer_name", 162, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "--- Select Buyer ---", $selected, "");
							?>
						</td>
						<td>
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;" placeholder="From Date"/>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;" placeholder="To Date"/>
						</td>
						<td>
							<input type="text" style="width:130px" class="text_boxes"  name="txt_invoice_search" id="txt_invoice_search" />
						</td>
						<td>
							<input type="button" id="search_button" class="formbutton" value="Show" onClick="fn_show_list()" style="width:100px;" />
						</td>
					</tr> 
					<tr>
						<td align="center" colspan="10"><? echo load_month_buttons(1); ?></td>
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

if($action==='invoice_search_list_view')
{
	list($company_id, $buyer_id, $invoice_num, $invoice_start_date, $invoice_end_date, $search_string, $type_name) = explode('**', $data);

	
    if($type_name==1){
		if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']['data_level_secured']==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!='') $buyer_id_cond=" and buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond='';
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_id=$buyer_id";
	}

	$search_text=''; $company_cond ='';
	if($company_id !=0) $company_cond = "and benificiary_id=$company_id";

	if ($invoice_num != '')
	{
		if($search_string==1)
			$search_text="and invoice_no like '".trim($invoice_num)."'";
		else if ($search_string==2) 
			$search_text="and invoice_no like '".trim($invoice_num)."%'";
		else if ($search_string==3)
			$search_text="and invoice_no like '%".trim($invoice_num)."'";
		else if ($search_string==4 || $search_string==0)
			$search_text="and invoice_no like '%".trim($invoice_num)."%'";
	}

	if ($invoice_start_date != '' && $invoice_end_date != '') 
	{
        if ($db_type == 0) {
            $date_cond = "and invoice_date between '" . change_date_format($invoice_start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($invoice_end_date, 'yyyy-mm-dd') . "'";
        } else if ($db_type == 2) {
            $date_cond = "and invoice_date between '" . change_date_format($invoice_start_date, '', '', 1) . "' and '" . change_date_format($invoice_end_date, '', '', 1) . "'";
        }
    } 
    else 
    {
        $date_cond = '';
    }

    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
		$sql = "select id, benificiary_id, buyer_id, invoice_no, invoice_date, is_lc, lc_sc_id, invoice_value, net_invo_value, import_btb, is_posted_account from com_export_invoice_ship_mst where status_active=1 and is_deleted=0 $company_cond $search_text $buyer_id_cond $date_cond order by invoice_date desc";
		
		$data_array=sql_select($sql);		

		$lc_arr=return_library_array( "select id, export_lc_no from com_export_lc",'id','export_lc_no');
		$sc_arr=return_library_array( "select id, contract_no from com_sales_contract",'id','contract_no');
	?>
	<table width="880" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead>
            <th width="40">SL</th>
            <th width="100">Company</th>
            <th width="100">Buyer</th>
            <th width="150">Invoice No</th>
            <th width="100">Invoice Date</th>
            <th width="150">LC/SC No</th>
            <th width="100">LC/SC</th>
            <th>Net Invoice Value</th>
        </thead>
     </table>
     <div style="width:900px; overflow-y:scroll; max-height:280px">
     	<table width="880" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="list_view">
		<?			
            $i = 1;
            foreach($data_array as $row)
            {
                if ($i%2==0)
                    $bgcolor="#FFFFFF";
                else
                    $bgcolor="#E9F3FF";

				if($row[csf('is_lc')]==1)
				{
					$lc_sc_no=$lc_arr[$row[csf('lc_sc_id')]];
					$is_lc_sc='LC';
				}
				else
				{
					$lc_sc_no=$sc_arr[$row[csf('lc_sc_id')]];
					$is_lc_sc='SC';
				}

				if($row[csf('import_btb')]==1) $buyer=$comp_arr[$row[csf('buyer_id')]]; else $buyer=$buyer_arr[$row[csf('buyer_id')]];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value( '<? echo $row[csf('id')]; ?>_<? echo $row[csf('invoice_no')]; ?>_<? echo  $row[csf('benificiary_id')]; ?>');" >  
					<td width="40"><? echo $i; ?></td>
					<td width="100"><p><? echo $comp_arr[$row[csf('benificiary_id')]]; ?></p></td>
					<td width="100"><p><? echo $buyer; ?></p></td>
                    <td width="150"><p><? echo $row[csf('invoice_no')]; ?></p></td>
					<td width="100" align="center"><p><? echo change_date_format($row[csf('invoice_date')]); ?></td>
                    <td width="150"><p><? echo $lc_sc_no; ?></p></td>
                    <td width="100" align="center"><p><? echo $is_lc_sc; ?></p></td>
					<td align="right"><p><?
					echo number_format($row[csf('net_invo_value')],2);?></p></td>
				</tr>
            <?
			$i++;
            }
			?>
		</table>
    </div>
	<?

    }elseif($type_name==2){

	$search_text=''; $company_cond ='';
	if($company_id !=0) $company_cond = "and a.importer_id ='".$company_id."'";

	if ($invoice_num != '')
	{
		if($search_string==1)
			$search_text="and b.invoice_no like '".trim($invoice_num)."'";
		else if ($search_string==2) 
			$search_text="and b.invoice_no like '".trim($invoice_num)."%'";
		else if ($search_string==3)
			$search_text="and b.invoice_no like '%".trim($invoice_num)."'";
		else if ($search_string==4 || $search_string==0)
			$search_text="and b.invoice_no like '%".trim($invoice_num)."%'";
	}

	if ($invoice_start_date != '' && $invoice_end_date != '') 
	{
        if ($db_type == 0) {
            $date_cond = "and b.invoice_date between '" . change_date_format($invoice_start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($invoice_end_date, 'yyyy-mm-dd') . "'";
        } else if ($db_type == 2) {
            $date_cond = "and b.invoice_date between '" . change_date_format($invoice_start_date, '', '', 1) . "' and '" . change_date_format($invoice_end_date, '', '', 1) . "'";
        }
    } 
    else 
    {
        $date_cond = '';
    }

    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

		$sql = "SELECT a.importer_id,a.supplier_id,a.lc_number,a.lc_value,a.lc_date,a.payterm_id,b.invoice_no,b.document_value,b.invoice_date ,b.document_value,b.id,a.id as lc_id,b.is_posted_account FROM com_btb_lc_master_details a, com_import_invoice_mst b WHERE a.id=b.btb_lc_id $company_cond $buyer_id_cond and is_lc=1 $date and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 $search_text $date_cond order by b.invoice_date desc";

	
		$data_array=sql_select($sql);	
		?>

		<table width="880" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead>
            <th width="40">SL</th>
            <th width="100">Company</th>
            <th width="100">Buyer</th>
            <th width="150">Invoice No</th>
            <th width="100">Invoice Date</th>
            <th width="150">Pay Term</th>
            <th width="100">LC Date</th>
            <th>Net Invoice Value</th>
        </thead>
     </table>
     <div style="width:900px; overflow-y:scroll; max-height:280px">
     	<table width="880" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="list_view">
		<?			
            $i = 1;
            foreach($data_array as $row)
            {
                if ($i%2==0)
                    $bgcolor="#FFFFFF";
                else
                    $bgcolor="#E9F3FF";

				if($row[csf('import_btb')]==1) $buyer=$comp_arr[$row[csf('buyer_id')]]; else $buyer=$buyer_arr[$row[csf('buyer_id')]];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value( '<? echo $row[csf('id')]; ?>_<? echo $row[csf('invoice_no')]; ?>_<? echo  $row[csf('importer_id')]; ?>');" >  
					<td width="40"><? echo $i; ?></td>
					<td width="100"><p><? echo $comp_arr[$row[csf('importer_id')]]; ?></p></td>
					<td width="100"><p><? echo $buyer; ?></p></td>
                    <td width="150"><p><? echo $row[csf('invoice_no')]; ?></p></td>
					<td width="100" align="center"><p><? echo change_date_format($row[csf('invoice_date')]); ?></td>
                    <td width="150"><p><? echo $pay_term[$row[csf('payterm_id')]];?></p></td>
                    <td width="100" align="center"><p><? echo change_date_format($row[csf('lc_date')],2);?></p></td>
					<td align="right"><p><?
					echo number_format($row[csf('document_value')],2);?></p></td>
				</tr>
            <?
			$i++;
            }
			?>
			
		</table>
    </div>
		<?

    }
	exit();
}

if($action=="report_generate")
{

	$process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    $company_id     = str_replace("'","",$cbo_company_name);
    $buyer_id       = str_replace("'","",$cbo_buyer_name);   
    $cnf_type       = str_replace("'","",$cbo_type_name);    
    $cnf_supplier_id= str_replace("'","",$cbo_candf_name);   
    $invoice_num    = str_replace("'","",$txt_invoice_no);   
    $bill_num       = str_replace("'","",$txt_bill_no);      
    $based_on       = str_replace("'","",$cbo_based_on);     
    $start_date     = str_replace("'","",$txt_date_from);    
    $end_date       = str_replace("'","",$txt_date_to);      
    
	if ($company_id!=0) {$company=" and company_id=$company_id";} else { echo "Please Select Company First."; die;}
	if($cnf_type!=0){$cnf_id="and cnf_type= $cnf_type";}
	if(str_replace("'","",$buyer_id)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") {$buyer=" and buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";} else {$buyer="";}
		}
		else {$buyer="";}
	}
	else {$buyer=" and buyer_id=$buyer_id";}
				
	 $search_text="";$date_cond ="";
	if ($invoice_num != '')
	{
        $search_text="and invoice_no like '".trim($invoice_num)."'";
	}

	if ($start_date != '' && $end_date != '') 
	{
		if($based_on==1){
        if ($db_type == 0) {
            $date_cond = "and invoice_date between '" . change_date_format($start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($end_date, 'yyyy-mm-dd') . "'";
        } else if ($db_type == 2) {
            $date_cond = "and invoice_date between '" . change_date_format($start_date, '', '', 1) . "' and '" . change_date_format($end_date, '', '', 1) . "'";
		}
		}
		if($based_on==2){
        if ($db_type == 0) {
            $date_cond = "and bill_date between '" . change_date_format($start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($end_date, 'yyyy-mm-dd') . "'";
        } else if ($db_type == 2) {
            $date_cond = "and bill_date between '" . change_date_format($start_date, '', '', 1) . "' and '" . change_date_format($end_date, '', '', 1) . "'";
		}
		}
    } 
    else 
    {
        $date_cond = '';
	}
	if($cnf_supplier_id!=0) {$cnf_supplier="and cnf_name_id= $cnf_supplier_id";}
	if($bill_num!='') {$bill_no="and bill_no= $bill_num";}
	 $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	 $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	 $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $cnf_type_arr=array(1=>"Export",2=>"Import");
	if($cnf_type==1)
	{
		$width_tbl=2850;
		$colspan_tbl=40;
	}
	if($cnf_type==2)
	{
		$width_tbl=4950;
		$colspan_tbl=57;
	}
	$sql= "SELECT id, company_id, cnf_type, cnf_name_id,sys_number, invoice_no, invoice_date, invoice_value, invoice_value_bdt, bill_no, bill_date, buyer_id,job_no,sb_no,total_amount,remarks from cnf_bill_mst where status_active=1 and is_deleted=0 $cnf_id $cnf_supplier $date_cond $company $bill_no $buyer $search_text order by id DESC";
  //  echo $sql ;
    $data_sql=sql_select($sql);
	$mst_id='';
	foreach($data_sql as $row)
	{
		$mst_id.=$row[csf('id')].',';
	}
	$dtls_sql="SELECT id as ID,mst_id as MST_ID,description_id as DESCRIPTION_ID,amount as AMOUNT from cnf_bill_dtls where status_active=1 and is_deleted=0 and mst_id in(".chop($mst_id,',').")";

	$dtls_result=sql_select($dtls_sql);
	$dtls_arr=array();
	foreach($dtls_result as $row)
	{
		$dtls_arr[$row['MST_ID']][$row['DESCRIPTION_ID']]=$row['AMOUNT'];
	}
    ?>
        <table class="rpt_table" border="1" rules="all" width="<?=$width_tbl;?>" cellpadding="0" cellspacing="0" id="table_body">
        <thead>
			<tr>
				<th colspan="<?=$colspan_tbl;?>" ><p style="font-weight:bold; font-size:20px">C and F Bill Statement</p></th>
			</tr>				
				<tr>
					<th width="50">SL No.</th>
					<th width="100">Company</th>
					<th width="110">Buyer</th>
					<th width="50">C&F Type</th>
                    <th width="110">C&F Name</th>
                    <th width="80">Invoice No</th>
                    <th width="60">Invoice Date</th>
                    <th width="80">Bill NO</th>
                    <th width="60">Bill Date</th>
                    <th width="80">Job No.</th>
                    <th width="80">S/B No</th>
                    <th width="80">Invoice Value</th>
                    <th width="80">Inv. Value BDT</th>
                    <th width="80">Bill Amount</th>
					<?
						if($cnf_type==1)
						{
							foreach($cnf_export_bill_head_arr as $key=>$val)
							{
								?>
								<th width="100"><?=$val;?></th>
								<?
							}
						}
						else
						{
							foreach($cnf_import_bill_head_arr as $key=>$val)
							{
								?>
								<th width="100"><?=$val;?></th>
								<?
							}
						}
					?>
                    <th  width="80">Bill Total</th>
                    <th >Remarks</th>
				</tr>
			</thead>
	
		        <tbody rules="all">
                <?
                    $i=1;
                    $tot_invoice_value=0;
					$bill_total_data=0;
                    $tot_invoice_value_bdt=0;
                    $tot_amount=0;				
					$total_bill_arr=array();
		        	foreach ($data_sql as $row)
		        	{
		        		if ($i%2==0) $bgcolor="#E9F3FF";
						else $bgcolor="#FFFFFF";
						$total_bill='';
		        		?>
                        <tr bgcolor="<?= $bgcolor; ?>">
			        		<td align="center"><p><?= $i; ?></p></td>
			        		<td align="center"><p><?= $company_arr[$row[csf('company_id')]]; ?></p></td>
			        		<td align="center"><p><?= $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
                            <td align="center"><p><?= $cnf_type_arr[$row[csf('cnf_type')]]; ?></p></td>
			        		<td align="center"><p><?= $supplier_arr[$row[csf('cnf_name_id')]]; ?></p></td>
			        		<td align="center"><p><?= $row[csf('invoice_no')]; ?></p></td>
			        		<td align="center"><p><?= change_date_format($row[csf('invoice_date')]); ?></p></td>
			        		<td align="center"><p><?= $row[csf('bill_no')]; ?></p></td>
			        		<td align="center"><p><?= change_date_format($row[csf('bill_date')]); ?></p></td>
			        		<td align="center"><p><?= $row[csf('job_no')]; ?></p></td>
			        		<td align="center"><p><?= $row[csf('sb_no')]; ?></p></td>
			        		<td align="right"><p><?= $row[csf('invoice_value')]; ?></p></td>
			        		<td align="right"><p><?= number_format($row[csf('invoice_value_bdt')],2,'.',''); ?></p></td>
			        		<td align="right"><p><?= number_format($row[csf('total_amount')],2,'.',''); ?></p></td>
							<?
								if($cnf_type==1)
								{
									foreach($cnf_export_bill_head_arr as $key=>$val)
									{
										?>
											<td align="right"><p><?echo number_format($dtls_arr[$row[csf('id')]][$key],2,'.','');$total_bill_arr[$key]+=$dtls_arr[$row[csf('id')]][$key]; ?></p></td>
										<?
									}
								}
								else
								{
									foreach($cnf_import_bill_head_arr as $key=>$val)
									{
										?>
											<td align="right"><p><?echo number_format($dtls_arr[$row[csf('id')]][$key],2,'.','');$total_bill_arr[$key]+=$dtls_arr[$row[csf('id')]][$key]; ?></p></td>
										<?
									}
								}

								if($cnf_type==1){

									foreach($cnf_export_bill_head_arr as $key=>$val)
									{										
											$total_bill+=$dtls_arr[$row[csf('id')]][$key]; ?>
										<?
									}
								}
								else{
									foreach($cnf_import_bill_head_arr as $key=>$val)
									{
										$total_bill+=$dtls_arr[$row[csf('id')]][$key]; ?>
										<?
									}
								}
							?>
			        		<td  width="80" align="center"><p><?= $total_bill; ?></p></td>
			        		<td align="center"><p><?= $row[csf('remarks')]; ?></p></td>
			        		</tr>
                            <?
                            $i++;
                            $tot_invoice_value+=$row[csf('invoice_value')];
                            $tot_invoice_value_bdt+=$row[csf('invoice_value_bdt')];
                            $tot_amount+=$row[csf('total_amount')];
							$bill_total_data += $total_bill ; 

                    }
                            ?>
                            <tr class="tbl_bottom">
		                <td colspan="11" align="right">Total:&nbsp;</td>
		                <td ><?= number_format($tot_invoice_value,2); ?></td>
		                <td ><?= number_format($tot_invoice_value_bdt,2); ?></td>
		                <td ><?= number_format($tot_amount,2); ?></td>
						<?
							if($cnf_type==1)
							{
								foreach($cnf_export_bill_head_arr as $key=>$val)
								{
									?>
									<td width="100"><?echo number_format($total_bill_arr[$key],2);?></td>
									<?
								}
							}
							else
							{
								foreach($cnf_import_bill_head_arr as $key=>$val)
								{
									?>
									<td width="100"><?echo number_format($total_bill_arr[$key],2);?></td>
									<?
								}
							}
						?>
		                <td  width="80"><? echo $bill_total_data ; ?></td>
		                <td >&nbsp;</td>
		            </tr>
                </tbody>
                </table>
    <?
	foreach (glob("$user_id*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename";
	exit();
} 