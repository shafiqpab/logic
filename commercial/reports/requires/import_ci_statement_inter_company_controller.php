<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$seource_des_array=array(3=>"Non EPZ",4=>"Non EPZ",5=>"Abroad",6=>"Abroad",11=>"EPZ",12=>"EPZ");

if($action == "supplier_list_popup")
{
	echo load_html_head_contents("Supplier List", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	?>
	<script>

		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});

		function js_set_value( str )
		{
			var id = $('#txt_individual_id' + str).val()
			var name= $('#txt_individual' + str).val()
			$('#hidden_supplier_id').val(id);
			$('#hidden_supplier_name').val(name);
			parent.emailwindow.hide();
		}
    </script>

	</head>
	<?
		$catWiseParty= array(
		0=>"0", 1=>"1,2", 2=>"1,9", 3=>"1,9", 13=>"1,9", 14=>"1,9", 4=>"1,4,5", 5=>"1,3",
		6=>"1,3",
		7=>"1,3",
		9=>"1,6",
		10=>"1,6",
		11=>"1,8",
		12=>"1,20,21,22,23,24,30,31,32,35,36,37,38,39",
		24=>"1,20,21,22,23,24,30,31,32,35,36,37,38,39",
		25=>"1,20,21,22,23,24,30,31,32,35,36,37,38,39",
		31=>"1,26",
		32=>"1,92"
		);

		if($catWiseParty[$category] != "")
		{
			$party_type = $catWiseParty[$category];
		}else{
			$party_type = "1,7";
		}

		$result = sql_select("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id  and c.tag_company in('$company') and b.party_type in ($party_type) and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name");
	?>
	<body>
	<div align="center">
		<fieldset style="width:370px;margin-left:10px">
	    	<input type="hidden" name="hidden_supplier_id" id="hidden_supplier_id" class="text_boxes" value="">
	    	<input type="hidden" name="hidden_supplier_name" id="hidden_supplier_name" class="text_boxes" value="">
	        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
	            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
	                <thead>
	                    <th width="50">SL</th>
	                    <th>Supplier Name</th>
	                </thead>
	            </table>
	            <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
	                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
	                <?
	                    $i=1;
	                    foreach($result as $row)
	                    {
	                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                        ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
                                <td width="50" align="center"><?php echo "$i"; ?>
                                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>"/>
                                    <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf('supplier_name')]; ?>"/>
                                </td>
                                <td><p><? echo $row[csf('supplier_name')]; ?></p></td>
	                        </tr>
	                        <?
	                        $i++;
	                    }
	                ?>
	                </table>
	            </div>
	        </form>
	    </fieldset>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="load_drop_down_supplier")
{


	$data = explode('_',$data);

	//category => party_type
	$catWiseParty= array(
	0=>"0", 1=>"1,2", 2=>"1,9", 3=>"1,9", 13=>"1,9", 14=>"1,9", 4=>"1,4,5", 5=>"1,3",
	6=>"1,3",
	7=>"1,3",
	9=>"1,6",
	10=>"1,6",
	11=>"1,8",
	12=>"1,20,21,22,23,24,30,31,32,35,36,37,38,39",
	24=>"1,20,21,22,23,24,30,31,32,35,36,37,38,39",
	25=>"1,20,21,22,23,24,30,31,32,35,36,37,38,39",
	31=>"1,26",
	32=>"1,92"
	);

	if($catWiseParty[$data[0]] != "")
	{
		$party_type = $catWiseParty[$data[0]];
	}else{
		$party_type = "1,7";
	}

	echo create_drop_down( "cbo_supplier_id", 90, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id  and c.tag_company in('$data[1]') and b.party_type in ($party_type) and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
	exit();
}

if ($action=="report_generate")
{
	extract($_REQUEST);
	ob_start();
	//echo $cbo_company_id;
	$user_arr = return_library_array("select id,user_name from user_passwd ","id","user_name");
	$issueBankrArr = return_library_array("select id,bank_name from lib_bank ","id","bank_name");
	//$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$supplierArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");

	$lc_sc_attach_sql=sql_select("select import_mst_id,lc_sc_id,is_lc_sc from com_btb_export_lc_attachment");
	$exp_lc_sc_data=array();
	foreach($exp_lc_sc_data as $row)
	{
		$exp_lc_sc_data[$row[csf("import_mst_id")]]["lc_sc_id"]=$row[csf("lc_sc_id")];
		$exp_lc_sc_data[$row[csf("import_mst_id")]]["is_lc_sc"]=$row[csf("is_lc_sc")];
	}

	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$file_reference_lc_arr = return_library_array("select f.import_mst_id,h.internal_file_no from com_btb_export_lc_attachment f,com_export_lc h where f.lc_sc_id=h.id and f.is_lc_sc=0","import_mst_id","internal_file_no");
	$file_reference_sales_arr = return_library_array("select f.import_mst_id,h.internal_file_no from com_btb_export_lc_attachment f,com_sales_contract h where f.lc_sc_id=h.id and f.is_lc_sc=1 ","import_mst_id","internal_file_no");

	$pay_data_sql=sql_select("select invoice_id,max(payment_date) as payment_date, sum(accepted_ammount)  as accepted_ammount from com_import_payment where status_active=1 group by invoice_id");
	$pay_data_arr=array();
	foreach($pay_data_sql as $row)
	{
		$pay_data_arr[$row[csf("invoice_id")]]["payment_date"]=$row[csf("payment_date")];
		$pay_data_arr[$row[csf("invoice_id")]]["accepted_ammount"]=$row[csf("accepted_ammount")];
	}


	$receive_sql=sql_select("select pi_wo_batch_no, item_category, sum(cons_quantity) as cons_quantity, sum(cons_amount) as cons_amount  from  inv_transaction where transaction_type=1 group by pi_wo_batch_no, item_category");
	$receive_data_arr=array();
	foreach($receive_sql as $row)
	{
		$receive_data_arr[$row[csf("pi_wo_batch_no")]][$row[csf("item_category")]]["cons_quantity"]=$row[csf("cons_quantity")];
		$receive_data_arr[$row[csf("pi_wo_batch_no")]][$row[csf("item_category")]]["cons_amount"]=$row[csf("cons_amount")];
	}

	$btb_lc_val_arr = return_library_array("select id, lc_value as lc_value from com_btb_lc_master_details where  status_active=1 and is_deleted=0","id","lc_value");
	?>
	<div id="" align="center" style="height:auto; width:2685px; margin:0 auto; padding:0;">
	<table width="2660px" align="center">
	<?
	$company_library=sql_select("select id, company_name, plot_no, level_no,road_no,city from lib_company where id=".$cbo_company_id."");
	foreach( $company_library as $row)
	{
	?>
        <tr>
            <td colspan="25" align="center" style="font-size:22px"><center><strong><? echo $row[csf('company_name')];?></strong></center></td>
        </tr>
	<?
	}
	?>
        <tr>
            <td colspan="25" align="center" style="font-size:18px"><center><strong><u><? echo $report_title; ?> Report</u></strong></center></td>
        </tr>
    </table>
    <!-- <div style="width:2660px;"> -->
    <table  cellspacing="0" width="2660px"  border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
       <thead>
            <th width="30">SL</th>
            <th width="100" align="center">Company Name</th>
            <th width="100" align="center">LC No</th>
            <th width="80" align="center">LC value</th>
            <th width="100" align="center">File Ref. No.</th>
            <th width="120" align="center">Issuing Bank</th>
            <th width="120" align="center">Buyer name</th>
            <th width="100" align="center">Invoice No</th>
            <th width="75" align="center">Invoice Date</th>
            <th width="75" align="center">Lc Date</th>
            <th width="70" align="center">Import Source</th>
            <th width="100" align="center">Supplier Name</th>
            <th width="50" align="center"><p>PI No & Date</p></th>
            <th width="100" align="center">Item Category</th>
            <th width="90" align="center">LCA No</th>
            <th width="50" align="center">Tenor</th>
            <th width="40" align="center"><p>Curr.</p></th>
            <th width="70" align="center">com. Accep. Date</th>
            <th width="70" align="center">Bank Accep. Date</th>
            <th width="70" align="center">Bank Ref</th>
            <th width="80" align="center">Bill Value</th>
            <th width="90" align="center">Paid Amount</th>
            <th width="80" align="center">Out Standing</th>
            <th width="70" align="center">Maturity Date</th>
            <th width="70" align="center">Month</th>
            <th width="70" align="center">Last Pay Date</th>
            <th width="70" align="center"><p>Shipment Date</p></th>
            <th width="70" align="center">Expiry Date</th>
            <th width="80" align="center">Lc Type</th>
            <th width="70" align="center">ETD</th>
            <th width="70" align="center">ETA</th>
            <th width="60" align="center">Goods in House Date</th>
            <th align="center">Received Value</th>
       </thead>
    </table>
    <!-- </div> -->
    <div style="width:2680px; overflow-y: scroll; max-height:300px; float: left;" id="scroll_body">
    <table cellspacing="0" width="2660"  border="1" rules="all" class="rpt_table" id="tbl_body" style="float: left;">
	<?
		$cbo_company=str_replace("'","",$cbo_company_id);
		$cbo_issue=str_replace("'","",$cbo_issue_banking);
		$cbo_supplier=str_replace("'","",$cbo_supplier_id);
		$search_by=str_replace("'","",$search_by_id);
		$from_date=str_replace("'","",$txt_date_from);
		$to_date=str_replace("'","",$txt_date_to);
		$pending_type=str_replace("'","",$pending_type);
		$txt_pending_date=str_replace("'","",$txt_pending_date);

		//echo $pending_type;die;

		if ($cbo_company==0) $company_id =""; else $company_id =" and d.importer_id=$cbo_company ";
		if ($cbo_issue==0) $issue_banking =""; else $issue_banking =" and d.issuing_bank_id=$cbo_issue ";
		if ($cbo_supplier==0) $supplier_id =""; else $supplier_id =" and d.supplier_id=$cbo_supplier ";

		if($db_type==0) $txt_pending_date=change_date_format($txt_pending_date,"yyyy-mm-dd"); else if($db_type==2) $txt_pending_date=date("j-M-Y",strtotime($txt_pending_date));
		if($pending_type==0) $pending_cond="";
		if($pending_type==1) $pending_cond="";
		if($pending_type==2) { if($txt_pending_date!="") $pending_cond="and a.maturity_date>'$txt_pending_date'";}
		if($pending_type==3) { if($txt_pending_date!="")  $pending_cond="and a.maturity_date<='$txt_pending_date'";}
		//echo $pending_cond;die;

		if($db_type==2)
		{
			if($search_by==0) $search_by_cond="";
			if($search_by==1) { if($search_by!="") $search_by_cond=" and d.lc_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";}
			if($search_by==2) { if($search_by!="") $search_by_cond=" and a.maturity_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";}
			if($search_by==3) { if($search_by!="") $search_by_cond=" and a.company_acc_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";}
			if($search_by==4) { if($search_by!="") $search_by_cond=" and a.bank_acc_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";}
			if($search_by==5) { if($search_by!="") $payment_date=" and e.payment_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";}

			//echo $search_by_cond.'Test';die;

		}
		else if($db_type==0)
		{
			if($search_by==0) $search_by_cond="";
			if($search_by==1) { if($search_by!="") $search_by_cond=" and d.lc_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";}
			if($search_by==2) { if($search_by!="") $search_by_cond=" and a.maturity_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";}
			if($search_by==3) { if($search_by!="") $search_by_cond=" and a.company_acc_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";}
			if($search_by==4) { if($search_by!="") $search_by_cond=" and a.bank_acc_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";}
			if($search_by==5) { if($search_by!="") $payment_date=" and e.payment_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";}			
		}

		$i=1;
		if($db_type==0)
		{
			if( $payment_date=="")
			{
				$sql="SELECT a.id,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,
				sum(b.current_acceptance_value) as current_acceptance_value, group_concat(distinct b.import_invoice_id) as import_invoice_id, group_concat(distinct b.pi_id) as pi_id,
				group_concat(distinct c.pi_number) as pi_number, group_concat(distinct c.pi_date) as pi_date,  group_concat(distinct  c.id )as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( d.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category
				from
				com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d
				where
				a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id=c.id and c.import_pi=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_id $issue_banking $supplier_id $search_by_cond $pending_cond 
				GROUP BY
				a.id ,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref";

			}
			else
			{
				$sql="SELECT a.id,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,
				sum(b.current_acceptance_value) as current_acceptance_value, group_concat(distinct b.import_invoice_id) as import_invoice_id, group_concat(distinct b.pi_id) as pi_id,
				group_concat(distinct c.pi_number) as pi_number, group_concat(distinct c.pi_date) as pi_date,  group_concat(distinct  c.id )as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( d.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category
				from
				com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d, com_import_payment e
				where
				a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id=c.id and a.id=e.invoice_id and c.import_pi=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_id $issue_banking $supplier_id $search_by_cond $payment_date $pending_cond
				GROUP BY
				a.id ,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref";
			}
		}
		else if($db_type==2)
		{
			if( $payment_date=="")
			{
				$sql="SELECT a.id,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,
				sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id,
				LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( d.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category
				from
						com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d
				where
						a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id=c.id and c.import_pi=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_id $issue_banking $supplier_id $search_by_cond $pending_cond
				GROUP BY
						a.id,a.invoice_no, a.invoice_date,a.company_acc_date,a.bank_acc_date,a.bank_ref,a.shipment_date,a.eta_date,a.bill_no,a.feeder_vessel,a.mother_vessel,a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,b.import_invoice_id";
				//echo $sql;//die; // and c.export_pi_id!=0
			}
			else
			{
				$sql="SELECT a.id,  a.invoice_no, a.invoice_date,   a.company_acc_date ,  a.bank_acc_date,  a.bank_ref, a.shipment_date, a.eta_date, a.bill_no, a.feeder_vessel, a.mother_vessel, a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,
				sum(b.current_acceptance_value) as current_acceptance_value, b.import_invoice_id,
				LISTAGG(CAST( c.pi_number  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_number) as pi_number, LISTAGG(CAST( c.pi_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.pi_date) as pi_date, LISTAGG(CAST(c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as pi_mst_id, max(d.id) as btb_lc_id, max(d.lc_type_id), max(d.issuing_bank_id) as issuing_bank_id, max(d.lc_number) as lc_number, max(d.lc_date) as lc_date, max(d.supplier_id) as supplier_id, max(d.lca_no) as lca_no,  max( d.item_category_id) as item_category_id, max(d.tenor) as tenor, sum(d.lc_value) as lc_value, max(d.currency_id) as currency_id, max(d.lc_expiry_date) as lc_expiry_date, max(d.lc_type_id) as lc_type_id, max(d.etd_date) as etd_date, max(d.lc_category) as lc_category
				from
				com_import_invoice_mst a, com_import_invoice_dtls b, com_pi_master_details c, com_btb_lc_master_details d ,com_import_payment e
				where
				a.id=b.import_invoice_id and b.btb_lc_id=d.id and b.pi_id = c.id and a.id=e.invoice_id and c.import_pi=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_id $issue_banking $supplier_id $search_by_cond $payment_date $pending_cond
				GROUP BY
				a.id,a.invoice_no, a.invoice_date,a.company_acc_date,a.bank_acc_date,a.bank_ref,a.shipment_date,a.eta_date,a.bill_no,a.feeder_vessel,a.mother_vessel,a.container_no, a.pkg_quantity, a.bill_of_entry_no, a.maturity_date, a.acceptance_time, a.copy_doc_receive_date, a.doc_to_cnf, a.bank_ref,b.import_invoice_id";
			}
		}
		//echo $sql;//die;

		if($db_type==2)
		{
			$lc_item_category_sql=sql_select("Select  LISTAGG( c.item_category_id, ',') WITHIN GROUP (ORDER BY c.item_category_id) as item_category_id , d.id from  com_btb_lc_master_details d,com_btb_lc_pi b, com_pi_item_details c where d.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and d.status_active=1 and b.status_active=1 and c.status_active=1  $company_id group by d.id");
		}
		else if($db_type==0)
		{
			$lc_item_category_sql=sql_select("Select  group_concat( c.item_category_id) as item_category_id , d.id from  com_btb_lc_master_details d,com_btb_lc_pi b, com_pi_item_details c where d.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and d.status_active=1 and b.status_active=1 and c.status_active=1 $company_id group by d.id");
		}
		$item_category_data=array();
		foreach($lc_item_category_sql as $row)
		{
			$item_category_data[$row[csf("id")]]['item_category_id']=$row[csf("item_category_id")];
		}
		//print_r($item_category_data);

		$buyer_sql="SELECT a.id as pi_id, d.buyer_id , 1 as type
		from com_pi_master_details a, com_pi_item_details b, fabric_sales_order_mst c, wo_booking_mst d
		where a.id=b.pi_id and b.work_order_no=c.job_no and c.sales_booking_no=d.booking_no and a.import_pi=1 and c.within_group=1
		group by a.id, d.buyer_id
		union all
		select a.id as pi_id, c.buyer_id, 2 as type 
		from com_pi_master_details a, com_pi_item_details b, fabric_sales_order_mst c
		where a.id=b.pi_id and b.work_order_no=c.job_no and a.import_pi=1 and c.within_group<>1
		group by a.id, c.buyer_id";
		$buyer_data = sql_select($buyer_sql);
		$buyer_arr=array();
		foreach($buyer_data as $row)
		{
			$buyer_arr[$row[csf('pi_id')]]['buyer_id']=$buyerArr[$row[csf('buyer_id')]];
		}
		/*echo "<pre>";
		print_r($buyer_arr);
		echo "</pre>";*/

		$sql_data = sql_select($sql);
		foreach($sql_data as $row)
		{
			if($pending_type>0)
			{
				if($row[csf('current_acceptance_value')]>$pay_data_arr[$row[csf('id')]]["accepted_ammount"])
				{
					$result_arr[$row[csf('id')]]['id']=$row[csf('id')];
					$result_arr[$row[csf('id')]]['invoice_no']=$row[csf('invoice_no')];
					$result_arr[$row[csf('id')]]['invoice_date']=$row[csf('invoice_date')];
					$result_arr[$row[csf('id')]]['company_acc_date']=$row[csf('company_acc_date')];
					$result_arr[$row[csf('id')]]['bank_acc_date']=$row[csf('bank_acc_date')];
					$result_arr[$row[csf('id')]]['bank_ref']=$row[csf('bank_ref')];
					$result_arr[$row[csf('id')]]['shipment_date']=$row[csf('shipment_date')];
					$result_arr[$row[csf('id')]]['eta_date']=$row[csf('eta_date')];
					$result_arr[$row[csf('id')]]['bill_no']=$row[csf('bill_no')];
					$result_arr[$row[csf('id')]]['feeder_vessel']=$row[csf('feeder_vessel')];
					$result_arr[$row[csf('id')]]['mother_vessel']=$row[csf('mother_vessel')];
					$result_arr[$row[csf('id')]]['container_no']=$row[csf('container_no')];
					$result_arr[$row[csf('id')]]['pkg_quantity']=$row[csf('pkg_quantity')];
					$result_arr[$row[csf('id')]]['bill_of_entry_no']=$row[csf('bill_of_entry_no')];
					$result_arr[$row[csf('id')]]['maturity_date']=$row[csf('maturity_date')];
					$result_arr[$row[csf('id')]]['acceptance_time']=$row[csf('acceptance_time')];
					$result_arr[$row[csf('id')]]['copy_doc_receive_date']=$row[csf('copy_doc_receive_date')];
					$result_arr[$row[csf('id')]]['doc_to_cnf']=$row[csf('doc_to_cnf')];
					$result_arr[$row[csf('id')]]['bank_ref']=$row[csf('bank_ref')];
					$result_arr[$row[csf('id')]]['current_acceptance_value']=$row[csf('current_acceptance_value')];
					$result_arr[$row[csf('id')]]['import_invoice_id']=$row[csf('import_invoice_id')];
					$result_arr[$row[csf('id')]]['pi_number']=$row[csf('pi_number')];
					$result_arr[$row[csf('id')]]['pi_date']=$row[csf('pi_date')];
					$result_arr[$row[csf('id')]]['pi_mst_id']=$row[csf('pi_mst_id')];
					$result_arr[$row[csf('id')]]['btb_lc_id']=$row[csf('btb_lc_id')];
					$result_arr[$row[csf('id')]]['lc_type_id']=$row[csf('lc_type_id')];
					$result_arr[$row[csf('id')]]['issuing_bank_id']=$row[csf('issuing_bank_id')];
					$result_arr[$row[csf('id')]]['lc_number']=$row[csf('lc_number')];
					$result_arr[$row[csf('id')]]['lc_date']=$row[csf('lc_date')];
					$result_arr[$row[csf('id')]]['supplier_id']=$row[csf('supplier_id')];
					$result_arr[$row[csf('id')]]['lca_no']=$row[csf('lca_no')];
					$result_arr[$row[csf('id')]]['item_category_id']=$row[csf('item_category_id')];
					$result_arr[$row[csf('id')]]['tenor']=$row[csf('tenor')];
					$result_arr[$row[csf('id')]]['lc_value']=$row[csf('lc_value')];
					$result_arr[$row[csf('id')]]['currency_id']=$row[csf('currency_id')];
					$result_arr[$row[csf('id')]]['lc_expiry_date']=$row[csf('lc_expiry_date')];
					$result_arr[$row[csf('id')]]['etd_date']=$row[csf('etd_date')];
					$result_arr[$row[csf('id')]]['lc_category']=$row[csf('lc_category')];
				}
			}
			else
			{
				$result_arr[$row[csf('id')]]['id']=$row[csf('id')];
				$result_arr[$row[csf('id')]]['invoice_no']=$row[csf('invoice_no')];
				$result_arr[$row[csf('id')]]['invoice_date']=$row[csf('invoice_date')];
				$result_arr[$row[csf('id')]]['company_acc_date']=$row[csf('company_acc_date')];
				$result_arr[$row[csf('id')]]['bank_acc_date']=$row[csf('bank_acc_date')];
				$result_arr[$row[csf('id')]]['bank_ref']=$row[csf('bank_ref')];
				$result_arr[$row[csf('id')]]['shipment_date']=$row[csf('shipment_date')];
				$result_arr[$row[csf('id')]]['eta_date']=$row[csf('eta_date')];
				$result_arr[$row[csf('id')]]['bill_no']=$row[csf('bill_no')];
				$result_arr[$row[csf('id')]]['feeder_vessel']=$row[csf('feeder_vessel')];
				$result_arr[$row[csf('id')]]['mother_vessel']=$row[csf('mother_vessel')];
				$result_arr[$row[csf('id')]]['container_no']=$row[csf('container_no')];
				$result_arr[$row[csf('id')]]['pkg_quantity']=$row[csf('pkg_quantity')];
				$result_arr[$row[csf('id')]]['bill_of_entry_no']=$row[csf('bill_of_entry_no')];
				$result_arr[$row[csf('id')]]['maturity_date']=$row[csf('maturity_date')];
				$result_arr[$row[csf('id')]]['acceptance_time']=$row[csf('acceptance_time')];
				$result_arr[$row[csf('id')]]['copy_doc_receive_date']=$row[csf('copy_doc_receive_date')];
				$result_arr[$row[csf('id')]]['doc_to_cnf']=$row[csf('doc_to_cnf')];
				$result_arr[$row[csf('id')]]['bank_ref']=$row[csf('bank_ref')];
				$result_arr[$row[csf('id')]]['current_acceptance_value']=$row[csf('current_acceptance_value')];
				$result_arr[$row[csf('id')]]['import_invoice_id']=$row[csf('import_invoice_id')];
				$result_arr[$row[csf('id')]]['pi_number']=$row[csf('pi_number')];
				$result_arr[$row[csf('id')]]['pi_date']=$row[csf('pi_date')];
				$result_arr[$row[csf('id')]]['pi_mst_id']=$row[csf('pi_mst_id')];
				$result_arr[$row[csf('id')]]['btb_lc_id']=$row[csf('btb_lc_id')];
				$result_arr[$row[csf('id')]]['lc_type_id']=$row[csf('lc_type_id')];
				$result_arr[$row[csf('id')]]['issuing_bank_id']=$row[csf('issuing_bank_id')];
				$result_arr[$row[csf('id')]]['lc_number']=$row[csf('lc_number')];
				$result_arr[$row[csf('id')]]['lc_date']=$row[csf('lc_date')];
				$result_arr[$row[csf('id')]]['supplier_id']=$row[csf('supplier_id')];
				$result_arr[$row[csf('id')]]['lca_no']=$row[csf('lca_no')];
				$result_arr[$row[csf('id')]]['item_category_id']=$row[csf('item_category_id')];
				$result_arr[$row[csf('id')]]['tenor']=$row[csf('tenor')];
				$result_arr[$row[csf('id')]]['lc_value']=$row[csf('lc_value')];
				$result_arr[$row[csf('id')]]['currency_id']=$row[csf('currency_id')];
				$result_arr[$row[csf('id')]]['lc_expiry_date']=$row[csf('lc_expiry_date')];
				$result_arr[$row[csf('id')]]['etd_date']=$row[csf('etd_date')];
				$result_arr[$row[csf('id')]]['lc_category']=$row[csf('lc_category')];
			}
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
			$item_id=$row[('item_category_id')];
			$curr_id=$row[('currency_id')];
			$pi_id=$row[('pi_mst_id')];

			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30"><p><? echo $i; ?></p></td>
                <td width="100"><p><? echo $company_arr[$cbo_company]; ?></p></td>
                <td width="100"><p><? echo $row[('lc_number')]; ?> &nbsp;</p></td>
                <?
				if($lc_check[$row[('btb_lc_id')]]=="")
				{
					$lc_check[$row[('btb_lc_id')]]=$row[('btb_lc_id')];
					?>
                    <td width="80" align="right"><? echo number_format($btb_lc_val_arr[$row[('btb_lc_id')]],2); $tot_lc_value+=$btb_lc_val_arr[$row[('btb_lc_id')]];//echo number_format($row[('lc_value')],2); //btb_lc_id?></td>
                    <?
				}
				else
				{
					?>
                    <td width="80" align="right"><? echo "<span style='color:white'>'</span>".number_format($btb_lc_val_arr[$row[('btb_lc_id')]],2)."<span style='color:white'>'</span>";?><!--<span style="color:white">_</span>--></td>
                    <?
				}
				?>
				<td width="100"><p>
				<?
				$exp_lc_sc_id =$exp_lc_sc_data[$row[('btb_lc_id')]]["lc_sc_id"];
				$is_lc_sc =$exp_lc_sc_data[$row[('btb_lc_id')]]["is_lc_sc"];
				if($is_lc_sc==0)
				$file_reference_no=$file_reference_lc_arr[$row[('btb_lc_id')]];
				else
				$file_reference_no=$file_reference_sales_arr[$row[('btb_lc_id')]];
				echo $file_reference_no; 
				?></p></td>  
				<td width="120"><p><? echo $issueBankrArr[$row[('issuing_bank_id')]]; ?></p></td> 
				<td width="120"><p><? echo $buyer_arr[$pi_id]['buyer_id']; ?></p></td> 
				<td width="100"><p><? echo $row[('invoice_no')]; ?></p></td>
				<td width="75"><p><? if($row[('invoice_date')]!="0000-00-00") echo change_date_format($row[('invoice_date')]); else echo "";?></p></td>
				<td width="75"><p><? if($row[('lc_date')]!="0000-00-00") echo change_date_format($row[('lc_date')]); else echo ""; ?></p></td>  
				<td width="70"><p><? echo $supply_source[$row[('lc_category')]*1]; ?></p></td>      
				<td width="100"><p><? echo $supplierArr[$row[('supplier_id')]]; ?></p></td>     
				<td width="50" align="center"><p><? echo "<a href='#report_details' style='color:#000' onclick= \"openmypage_pi_date('$import_invoice_id','$suppl_id','$item_id','$pi_id','$curr_id','pi_details','PI Details');\">"."View"."</a>";//$row[("pi_id")]; ?></p></td>
				<td width="100"><p>
				<?
				$itemCategory="";
				$l=1;
				$cat_id_arr=array_unique(explode(",",$item_category_data[$row[('btb_lc_id')]]['item_category_id']));
				foreach($cat_id_arr as $cat_id)
				{
					if($l!=1) $itemCategory .=", ";
					$itemCategory .=$item_category[$cat_id];
					$l++;
				}
				echo $itemCategory; 
				?></p></td>
				<td width="90"><p><? echo $row[('lca_no')]; ?></p></td>
                <td width="50"><p><? echo $row[('tenor')]; ?></p></td>
                <td width="40"><p><? echo $currency[$row[('currency_id')]]; ?></p></td>
                <td width="70" style="word-wrap:break-word; word-break: break-all;"><p><? if($row[('company_acc_date')]!="0000-00-00") echo change_date_format($row[('company_acc_date')]); else echo ""; ?></p></td>
                <td width="70" style="word-wrap:break-word; word-break: break-all;"><p><? if($row[('bank_acc_date')]!="0000-00-00") echo change_date_format($row[('bank_acc_date')]); ?></p></td>
                <td width="70"><p><? echo $row[('bank_ref')]; ?></p></td>
                <td width="80" align="right"><p><? echo number_format($row[('current_acceptance_value')],2); $tot_bill_value+=$row[('current_acceptance_value')]; ?></p></td>
                <td width="90" align="right"><p><? echo number_format($pay_data_arr[$row[('id')]]["accepted_ammount"],2); $gt_total_paid+=$pay_data_arr[$row[('id')]]["accepted_ammount"]; ?></p></td>
                <td width="80" align="right"><p><? $out_standing=$row[('current_acceptance_value')]-$pay_data_arr[$row['id']]["accepted_ammount"]; echo number_format($out_standing,2); $total_out_standing +=$out_standing ?></p></td>
                <td width="70" style="word-wrap:break-word; word-break: break-all;"><p><? if($row[('maturity_date')]!="0000-00-00") echo change_date_format($row[('maturity_date')]); else echo ""; ?></p></td>
                <td width="70"><p>
				<?
					if($row[('maturity_date')]=="00-00-0000")
					{
						$month="";
					}
					else
					{
						$month = date('F', strtotime($row[('maturity_date')]));
					}
					echo $month;

				 ?></p></td>
                <td width="70" style="word-wrap:break-word; word-break: break-all;"><p><? if($pay_data_arr[$row[('id')]]["payment_date"]!="" && $pay_data_arr[$row[('id')]]["payment_date"]!="0000-00-00") echo change_date_format($pay_data_arr[$row[('id')]]["payment_date"]); ?></p></td>
                <td width="70" style="word-wrap:break-word; word-break: break-all;"><p><? if($row[('shipment_date')]!="0000-00-00") echo change_date_format($row[('shipment_date')]); else echo ""; ?></p></td>
                <td width="70" style="word-wrap:break-word; word-break: break-all;"><p><? if($row[('lc_expiry_date')]!="0000-00-00")  echo change_date_format($row[('lc_expiry_date')]); else echo ""; ?></p></td>
                <td width="80"><p><? echo $lc_type[$row[('lc_type_id')]]; ?></p></td>
                <td width="70" style="word-wrap:break-word; word-break: break-all;"><p><? if($row[('etd_date')]!="0000-00-00") echo change_date_format($row[('etd_date')]); else echo ""; ?></p></td>
                <td width="70" style="word-wrap:break-word; word-break: break-all;"><p><? if($row[('eta_date')]!="0000-00-00") echo change_date_format($row[('eta_date')]); else echo ""; ?></p></td>
                <?
				$pi_id_all=explode(",",$row[('pi_mst_id')]);
				$z=1;
				$itemCategoryids="";
				$receive_qnty=0;
				$receive_value=0;
				foreach($pi_id_all as $piid)
				{
					foreach($cat_id_arr as $cat_id)
					{
						if($z!=1) $itemCategoryids .=", ";
						$itemCategoryids .=$cat_id;						
						$receive_qnty += $receive_data_arr[$piid][$cat_id]["cons_quantity"];
						$receive_value += $receive_data_arr[$piid][$cat_id]["cons_amount"];
						$z++;
					}
				}
				?>
                <td width="60" align="center"><p><? echo "<a href='#report_details' style='color:#000' onclick= \"openmypage_inHouse_date('$pi_id','$receive_value','$receive_qnty','$itemCategoryids','pi_rec_details','PI Details');\">"."View"."</a>"; //$row[("pi_id")]; ?></p></td>
				<td align="right" ><p><? echo number_format($receive_value,2);  $total_receive_value+=$receive_value; ?></p></td>
            </tr>
            <?
			$i++;
		}
		?>
        </table>
        <table cellspacing="0" width="2660"  border="1" rules="all" class="rpt_table" id="report_table_footer" style="float: left;">
            <tfoot>
            	<th width="30">&nbsp;</th>
                <th width="100" >&nbsp;</th>
                <th width="100" align="right">Total : </th>
                <th width="80" align="right" id="value_tot_lc_value"><? echo number_format($tot_lc_value,2); ?></th>
                <th width="100" >&nbsp;</th>
                <th width="120" >&nbsp;</th>
                <th width="120" >&nbsp;</th>
                <th width="100" >&nbsp;</th>
                <th width="75" ><p>&nbsp;</p></th>
                <th width="75" ><p>&nbsp;</p></th>
                <th width="70" >&nbsp;</th>
                <th width="100" >&nbsp;</th>
                <th width="50"></th>
                <th width="100"></th>
                <th width="90" align="center">&nbsp;</th>
                <th width="50" align="center">&nbsp;</th>
                <th width="40" align="center">&nbsp;</th>
                <th width="70" align="center">&nbsp;</th>
                <th width="70"></th>
                <th width="70"></th>
                <th width="80" align="right" id="value_tot_bill_value"><? echo number_format($tot_bill_value,2); ?></th>
                <th width="90" align="right" id="value_gt_total_paid"><? echo number_format($gt_total_paid,2); ?></th>
                <th width="80" align="right" id="value_total_out_standing"><? echo number_format($total_out_standing,2); ?></th>
                <th width="70" align="center">&nbsp;</th>
                <th width="70" align="center">&nbsp;</th>
                <th width="70" align="center">&nbsp;</th>
                <th width="70" align="center">&nbsp;</th>
                <th width="70" align="center">&nbsp;</th>
                <th width="80" align="center">&nbsp;</th>
                <th width="70" align="center">&nbsp;</th>
                <th width="70"></th>
                <th width="60" align="center">&nbsp;</th>
                <th align="right" id="value_total_receive"><? echo number_format($total_receive_value,2); ?></th>
            </tfoot>
		</table>
    </div>

    <div align="left" style="font-weight:bold; margin-left:30px;"><? echo "User-Id : ". $user_arr[$user_id] ." , &nbsp; THIS IS SYSTEM GENERATED STATEMENT, NO SIGNATURE REQUIRED ."; ?></div>
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
	$create_new_doc = fopen($filename, 'w'); //or die('canot open');
	$is_created = fwrite($create_new_doc,ob_get_contents()); //or die('canot write');
	echo "$html****$filename";
	exit();
}

if($action=="pi_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//print_r ($import_invoice_id);

	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	//$composition;
	//$yarn_type;
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	?>
	<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
		document.getElementById('scroll_body').style.overflowY="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
	}

	function window_close()
	{
		parent.emailwindow.hide();
	}

	</script>
	<div style="width:800px" align="center" id="scroll_body" >
	<fieldset style="width:100%; margin-left:10px" >
	<!--<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>&nbsp;&nbsp;&nbsp;&nbsp;
	--><input type="button" value="Close" onClick="window_close()" style="width:100px"  class="formbutton"/>
         <div id="report_container" align="center" style="width:100%" >
             <div style="width:780px">
                <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
                    <thead>
                    	<tr>
                            <th colspan="4" align="center"><? echo $companyArr[$company_name]; ?></th>
                        </tr>
                        <tr>
                            <th width="150"><strong>Supplier</strong></th>
                            <th width="150"><strong>Item Category</strong></th>
                            <th width="150"><strong>Currency</strong></th>
                        </tr>
                    </thead>
                    <tbody>
                    	<tr>
                        	<td><? echo $supplierArr[$suppl_id]; ?></td>
                            <td><? echo $item_category[$item_id]; ?></td>
                            <td><? echo $currency[$curr_id]; ?></td>
                        </tr>
                    </tbody>
                </table>
                <br />
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                	 <thead bgcolor="#dddddd">
                     	<tr>
                            <th width="30">SL</th>
                            <th width="70">PI No.</th>
                            <th width="70">PI Date</th>
                            <th width="100">Item Group</th>
                            <th width="130">Item Description</th>
                            <th width="80">Qnty</th>
                            <th width="70">Rate</th>
                            <th width="80">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                <?
		$i=1;
		//if ($pi_id==0) $piId =""; else $piId =" and a.id in ($pi_id)";
		$sql="Select a.id, a.pi_number,a.pi_date, b.item_prod_id, b.determination_id, b.item_group, b.item_description,a.item_category_id, b.size_id,b.color_id,b.count_name,b.yarn_type,b.yarn_composition_item1, b.yarn_composition_percentage1, b.quantity, b.rate, b.amount from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.id in ($pi_id) order by a.pi_number";
		//echo $sql;
		$result=sql_select($sql);

		$pi_arr=array();
		foreach( $result as $row)
		{
			$total_qnt+=$row[csf("quantity")];

			$total_amount+=$row[csf("amount")];

			if ($i%2==0)
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
			?>
            <tr bgcolor="<? echo $bgcolor ; ?>">
            	<td><? echo $i; ?></td>
                <td><? echo $row[csf("pi_number")]; ?></td>
                <td><? echo change_date_format($row[csf("pi_date")]); ?></td>
                <td><? echo $itemgroupArr[$row[csf("item_group")]]; ?></td>
                <?
	                if($row[csf("item_category_id")]==1)
	                {
	                	$description = $yarn_count_arr[$row[csf("count_name")]]." ".$composition[$row[csf("yarn_composition_item1")]]." ".$row[csf("yarn_composition_percentage1")]."% ".$yarn_type[$row[csf("yarn_type")]]." ".$color_name_arr[$row[csf("color_id")]];
	                }
	                else
	                {
	                	$description = $row[csf("item_description")];
	                }
                ?>
                <td><? echo $description; ?></td>

                <td align="right"><? echo $row[csf("quantity")]; ?></td>
                <td align="right"><? echo $row[csf("rate")]; ?></td>
                <td align="right"><? echo number_format($row[csf("amount")],2); ?></td>
            </tr>
          </tbody>
		<?
        $i++;
        }
		   ?>
             <tfoot>
                <th colspan="5" align="right">Total : </th>
                <th align="right"><? echo number_format($total_qnt,0); ?></th>
                <th>&nbsp;</th>
                <th align="right"><? echo number_format($total_amount,2); ?></th>
            </tfoot>
        </table>
		</div>
        </div>
	</fieldset>
	</div>
	<?
	exit();
}

if($action=="pi_rec_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//print_r ($pi_id);

	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	?>
	<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
		document.getElementById('scroll_body').style.overflowY="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
	}

	function window_close()
	{
		parent.emailwindow.hide();
	}

	</script>
	<div style="width:600px" align="center" id="scroll_body" >
	<fieldset style="width:100%; margin-left:10px" >
	<!--<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>&nbsp;&nbsp;&nbsp;&nbsp;
	--><input type="button" value="Close" onClick="window_close()" style="width:100px"  class="formbutton"/>
         <div id="report_container" align="center" style="width:100%" >
             <div style="width:580px">
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                	 <thead bgcolor="#dddddd">
                     	<tr>
                            <th width="30">PO No</th>
                            <th width="120">MRR No</th>
                            <th width="80">Received Date</th>
                             <th width="80">Received Qty</th>
                            <th width="80">Rate</th>
                            <th width="80">Received Value</th>
                        </tr>
                    </thead>
                    <tbody>
                <?
		$i=1;
		//if ($pi_id==0) $piId =""; else $piId =" and a.id in ($pi_id)";
		//if($db_type==0) $grp_con=" group_concat(distinct b.cons_rate) as cons_rate";
	//	else  $grp_con="LISTAGG(po_break_down_id, ',') WITHIN GROUP (ORDER BY po_break_down_id) as po_break_down_id";

		// Data Come after Working in Knit Finish Fabric Receive By Garments query is below
		$sql="Select a.id, a.recv_number, a.receive_date, sum(b.cons_quantity) as cons_quantity, sum(b.cons_amount) as cons_amount from inv_receive_master a,  inv_transaction b where a.id=b.mst_id  and b.pi_wo_batch_no in ($pi_id) and b.transaction_type=1 and b.item_category in($item_category) group by a.recv_number,a.id,a.receive_date order by a.id";
		//echo $sql;
		$result=sql_select($sql);

		$pi_arr=array();
		foreach( $result as $row)
		{
			$total_qnt+=$row[csf("cons_quantity")];
			$total_amount+=$row[csf("cons_amount")];

				if ($i%2==0)
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
			?>
            <tr bgcolor="<? echo $bgcolor ; ?>">
            	<td><? echo $pi_id; ?></td>
                <td><? echo $row[csf("recv_number")]; ?></td>
                <td><? echo change_date_format($row[csf("receive_date")]); ?></td>
                <td align="right"><? echo $row[csf("cons_quantity")];//$receive_qnty; ?></td>
                <td align="right"><? echo number_format($row[csf("cons_amount")]/$row[csf("cons_quantity")],2); ?></td>
                <td align="right"><? echo number_format($row[csf("cons_amount")],2);//number_format($receive_value,2); ?></td>
            </tr>
          </tbody>
		<?
        $i++;
        }
		   ?>
         <tfoot>
            <th colspan="3" align="right">Total : </th>
            <th align="right"><? echo number_format($total_qnt,0); ?></th>
            <th>&nbsp;</th>
            <th align="right"><? echo number_format($total_amount,2); ?></th>
        </tfoot>
    </table>
    </div>
    </div>
	</fieldset>
	</div>
	<?
	exit();
}

?>
