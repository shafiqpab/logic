<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="load_supplier_dropdown")
{
	//echo $data;
	$data = explode('_',$data);
	if($data[1]==0) 
	{
		echo create_drop_down( "cbo_supplier_id", 140, $blank_array,'', 1, '-- Select Supplier --',0,'',0);
	}
	else if($data[1]==1)
	{
		echo create_drop_down( "cbo_supplier_id", 140,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type =2 and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);		
	}
	else if($data[1]==2 || $data[1]==3 || $data[1]==13 || $data[1]==14)
	{
		echo create_drop_down( "cbo_supplier_id", 140,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type =9 and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
	}
	else if($data[1]==4)
	{
		echo create_drop_down( "cbo_supplier_id", 140,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type in(4,5) and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
		
	}
	else if($data[1]==5 || $data[1]==6 || $data[1]==7)
	{
		echo create_drop_down( "cbo_supplier_id", 140,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type=3 and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
	}
	else if($data[1]==9 || $data[1]==10)
	{
		echo create_drop_down( "cbo_supplier_id", 140,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type = 6 and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
	}
	else if($data[1]==11)
	{
		echo create_drop_down( "cbo_supplier_id", 140,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type = 8 and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
	}
	else if($data[1]==12 || $data[1]==24 || $data[1]==25)
	{
		echo create_drop_down( "cbo_supplier_id", 140,"select DISTINCT(c.supplier_name),c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type in(20,21,22,23,24,30,31,32,35,36,37,38,39) and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
	}
	else
	{
		echo create_drop_down( "cbo_supplier_id", 140,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type = 7 and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);

	} 
	exit(); 
}

if($action=="open_invoice_popup")
{
	echo load_html_head_contents("BTB / Import LC List", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		function js_set_value(id)
		{
			//alert(id)
			var id_array=id.split("_");
			$('#hidden_invoice_id').val(id_array[0]);
			$('#hidden_btb_id').val(id_array[1]);
			
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:930px;">
	<form name="searchscfrm"  id="searchscfrm">
		<fieldset style="width:100%;">
            <legend>Enter search words</legend>           
            	<table cellpadding="0" cellspacing="0" width="930" class="rpt_table">
                	<thead>
                    	<th>Item Category</th>
                        <th>Issue Bank</th>
                    	<th class="must_entry_caption">Company</th>
                        <th>Supplier</th>
                        <th>Bank Ref.</th>
                        <th>Invoice Date</th>
                        <th>
                        	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        	<input type="hidden" name="hidden_invoice_id" id="hidden_invoice_id" value="" />
                            <input type="hidden" id="hidden_btb_id" />
                        </th>
                    </thead>
                    <tr class="general">
                    	
                        <td> 
                             <? echo create_drop_down( "cbo_item_category_id", 130, $item_category,'', 1, '--Select--',0,"load_drop_down( 'import_payment_controller',document.getElementById('txt_company_id').value+'_'+this.value,'load_supplier_dropdown','supplier_td' );",0); ?>  
                        </td>
                        <td> 
                             <? echo create_drop_down( "cbo_issue_bank", 130, "select bank_name,id from lib_bank where is_deleted=0 and status_active=1 and issusing_bank=1 order by bank_name","id,bank_name", 1, '--Select--',0,"",0); ?>  
                        </td>
                        <td>
                           <? 
								echo create_drop_down( "txt_company_id",130,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, 'Select',0,"load_drop_down( 'import_payment_controller',this.value+'_'+document.getElementById('cbo_item_category_id').value,'load_supplier_dropdown','supplier_td' );",0); 
							?>  
                         </td>
                         <td align="center" id="supplier_td">
                          <? echo create_drop_down( "cbo_supplier_id", 140,$blank_array,'', 1, '-- Select Supplier --',0,0,0); ?>       
                         </td> 
                         <td><input type="text" name="txt_bank_ref" id="txt_bank_ref" class="text_boxes" style="width:80px;" /></td>           
						<td> 
                        	 <input type="text" name="btb_start_date" id="btb_start_date" class="datepicker" style="width:65px;" />To
                             <input type="text" name="btb_end_date" id="btb_end_date" class="datepicker" style="width:65px;" />
                        </td>						
						                     
                         <td>
                 		  	<input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_company_id').value+'**'+document.getElementById('cbo_item_category_id').value+'**'+document.getElementById('cbo_supplier_id').value+'**'+document.getElementById('btb_start_date').value+'**'+document.getElementById('btb_end_date').value+'**'+document.getElementById('txt_bank_ref').value+'**'+document.getElementById('cbo_issue_bank').value, 'create_invoice_list_view', 'search_div', 'import_payment_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                         </td>
					</tr>
               </table>
               <table width="100%" style="margin-top:5px" align="center">
					<tr>
                    	<td colspan="5" id="search_div" align="center"></td>
                    </tr>
                </table> 
            </fieldset>
		</form>
	</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit(); 
 
}


if($action=="create_invoice_list_view")
{
	
	$data=explode('**',$data);//0->company,1->Item Category, 2->Supplier,3->Start Date 4->End Date, 5->Search Text 
	$company_id = $data[0];
	$item_category_id = $data[1];
	$supplier_id = $data[2];
	$invoice_start_date = $data[3];
	$invoice_end_date = $data[4];
	$txt_bank_ref =trim($data[5]);
	$cbo_issue_bank =trim(str_replace("'","",$data[6]));
	
	if($company_id==0)
	{
		echo 'Select Importer';die;
	}
	/*if($item_category_id==0)
	{
		echo 'Select Item Category';die;
	}*/
	
	if ($company_id!=0) $company=$company_id;
	if ($item_category_id!=0) $item_cate=$item_category_id; else $item_cate='%%';
	if ($supplier_id!=0) $supplier=$supplier_id; else $supplier='%%';
	if ($txt_bank_ref!='') $bank_ref=$txt_bank_ref; else $bank_ref='%%';
	if($cbo_issue_bank!=0) $issue_bank_cond="and a.issuing_bank_id='$cbo_issue_bank'"; else $issue_bank_cond="";
	//if ($invoice_start_date!='') $start_date=$invoice_start_date; else $start_date='%%';
	//if ($invoice_end_date!='') $end_date=$invoice_end_date; else $end_date='%%';
	
	if($invoice_start_date!='' && $invoice_end_date!='')
	{
		if($db_type==0)
		{
			$date = "and b.invoice_date between '".change_date_format($invoice_start_date,'yyyy-mm-dd','-')."' and '".change_date_format($invoice_end_date,'yyyy-mm-dd','-')."'";
		}
		else
		{
			$date = "and b.invoice_date between '".change_date_format($invoice_start_date,'','',1)."' and '".change_date_format($invoice_end_date,'','',1)."'";
		}
	}
	else
	{
		$date = "";
	}
	 
	$sql = "SELECT a.item_category_id, a.importer_id,a.supplier_id,a.lc_number,a.lc_value,b.invoice_no,b.invoice_date ,b.bank_ref,b.document_value,b.id,a.id as lc_id  FROM com_btb_lc_master_details a, com_import_invoice_mst b WHERE a.id=b.btb_lc_id and  a.importer_id = '".$company."' and a.supplier_id like '".$supplier."' and a.item_category_id like '".$item_cate."' and b.bank_ref like '$bank_ref' and b.is_lc=1 and a.payterm_id !=1 $issue_bank_cond $date and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0";
	//echo $sql;
	$supplier_lib=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name'); 
	//$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	//$arr=array(0=>$company_library,1=>$supplier_lib);
	$arr=array(0=>$item_category,1=>$supplier_lib);
			
	echo  create_list_view("list_view", "Item Category,Supplier,L/C Number,L/C Value,Invoice No,Invoice Date,Bank Ref.,Document Amount", "90,140,100,100,100,80,100,100","900","300",0, $sql , "js_set_value", "id,lc_id", "", 1, "item_category_id,supplier_id,0,0,0,0,0,0", $arr , "item_category_id,supplier_id,lc_number,lc_value,invoice_no,invoice_date,bank_ref,document_value", "",'','0,0,0,2,0,3,0,2') ;
		
	exit();
}

if($action=='populate_data_from_btb_lc')
{
	$data=explode("_",$data);
	
	if($db_type==0)
	{
		$data_array=sql_select("select a.id,a.lc_number,a.issuing_bank_id,a.lc_value,a.currency_id,a.supplier_id,a.importer_id,a.item_category_id,a.maturity_from_id,b.id as invoice_id,b.invoice_no,b.bank_ref,b.bank_acc_date,maturity_date,b.shipment_date,b.bill_date, sum(c.current_acceptance_value) as current_acceptance_value from com_btb_lc_master_details a,com_import_invoice_mst b, com_import_invoice_dtls c where a.id=b.btb_lc_id and a.id=c.btb_lc_id and b.id=c.import_invoice_id and a.id='$data[0]' and b.id='$data[1]'");
	}
	else
	{
		$data_array=sql_select("select a.id,a.lc_number,a.issuing_bank_id,a.lc_value,a.currency_id,a.supplier_id,a.importer_id,a.item_category_id,a.maturity_from_id,b.id as invoice_id,b.invoice_no,b.bank_ref,b.bank_acc_date,maturity_date,b.shipment_date,b.bill_date, sum(c.current_acceptance_value) as current_acceptance_value from com_btb_lc_master_details a,com_import_invoice_mst b, com_import_invoice_dtls c where a.id=b.btb_lc_id and a.id=c.btb_lc_id and b.id=c.import_invoice_id and a.id='$data[0]' and b.id='$data[1]' group by a.id,a.lc_number,a.issuing_bank_id, a.lc_value,a.currency_id,a.supplier_id, a.importer_id,a.item_category_id, a.maturity_from_id,b.id, b.invoice_no, b.bank_ref, b.bank_acc_date,maturity_date,b.shipment_date,b.bill_date");
	}
	foreach ($data_array as $row)
	{ 
		$internal_file_no="";
		$is_lc_sc_sql = sql_select("SELECT lc_sc_id, is_lc_sc FROM com_btb_export_lc_attachment where import_mst_id='".$row[csf("id")]."'");
		list($is_lc_sc_sql_row)=$is_lc_sc_sql;
		if($is_lc_sc_sql_row[csf("is_lc_sc")] == 0)
		{
			$internal_file_sql = sql_select("SELECT internal_file_no FROM com_export_lc where id='".$is_lc_sc_sql_row[csf("lc_sc_id")]."'");
			list($internal_file_sql_row)=$internal_file_sql;
			$internal_file_no=$internal_file_sql_row[csf("internal_file_no")];
		}
		else if($is_lc_sc_sql_row[csf("is_lc_sc")] == 1)
		{
			$internal_file_sql = sql_select("SELECT internal_file_no FROM com_sales_contract where id='".$is_lc_sc_sql_row[csf("lc_sc_id")]."'");
			list($internal_file_sql_row)=$internal_file_sql;
			$internal_file_no=$internal_file_sql_row[csf("internal_file_no")];
		}
		echo "document.getElementById('txt_lc_number').value 				= '".$row[csf("lc_number")]."';\n";
		echo "document.getElementById('btb_lc_id').value 					= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_issuing_bank').value 			= '".$row[csf("issuing_bank_id")]."';\n";
		echo "document.getElementById('cbo_maturit_from_id').value 			= '".$row[csf("maturity_from_id")]."';\n";
		echo "document.getElementById('txt_lc_value').value 				= '".$row[csf("lc_value")]."';\n";
		echo "document.getElementById('cbo_lc_currency_id').value 			= '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('cbo_importer_id').value 				= '".$row[csf("importer_id")]."';\n";
		//echo "load_drop_down( 'requires/import_payment_controller', '".$row[csf('importer_id')]."'+'_'+'".$row[csf('item_category_id')]."', 'load_supplier_dropdown', 'supplier_td');\n";
		echo "document.getElementById('cbo_supplier_id').value 				= '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('cbo_supplier_id').disabled 			= true;\n";
		echo "$('#internal_file_no').val( '$internal_file_no' );\n";
		echo "document.getElementById('txt_bank_ref').value 				= '".$row[csf("bank_ref")]."';\n";
		echo "document.getElementById('txt_invoice_number').value 			= '".$row[csf("invoice_no")]."';\n";
		echo "document.getElementById('invoice_id').value 					= '".$row[csf("invoice_id")]."';\n";
		echo "document.getElementById('txt_shipment_date').value 			= '".change_date_format($row[csf("shipment_date")])."';\n";
		echo "document.getElementById('txt_bank_acceptance_date').value 	= '".change_date_format($row[csf("bank_acc_date")])."';\n";
		echo "document.getElementById('bill_date').value 					= '".change_date_format($row[csf("bill_date")])."';\n";
		echo "document.getElementById('maturity_date').value 				= '".change_date_format($row[csf("maturity_date")])."';\n";
		echo "document.getElementById('txt_invoice_value').value 			= '".$row[csf("current_acceptance_value")]."';\n";
		exit();
	}
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	 
	 
	if ($operation==0)  // Insert Here
	{
		
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id=return_next_id( "id", "com_import_payment", 1 ) ;
		$field_array="id,invoice_id,lc_id,payment_date,payment_head,adj_source,adj_source_ref,conversion_rate,accepted_ammount,domistic_currency,remarks,inserted_by,insert_date";
		$data_array="(".$id.",".$invoice_id.",".$btb_lc_id.",".$import_payment_date.",".$cbo_payment_head_id.",".$cbo_adj_source.",".$adj_source_ref.",".$import_payment_conver_rate.",".$import_payment_accepted_ammount.",".$import_payment_dom_currency.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		$rID=sql_insert("com_import_payment",$field_array,$data_array,1);
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".str_replace("'","",$invoice_id);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$invoice_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);   
				echo "0**".str_replace("'","",$invoice_id);
			}
			else{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$invoice_id);
			}
		}
		disconnect($con);
		die;
	}
	
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="invoice_id*lc_id*payment_date*payment_head*adj_source*adj_source_ref*conversion_rate*accepted_ammount*domistic_currency*remarks*updated_by*update_date";
		$data_array="".$invoice_id."*".$btb_lc_id."*".$import_payment_date."*".$cbo_payment_head_id."*".$cbo_adj_source."*".$adj_source_ref."*".$import_payment_conver_rate."*".$import_payment_accepted_ammount."*".$import_payment_dom_currency."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_update("com_import_payment",$field_array,$data_array,"id","".$update_id."",1);
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$invoice_id);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$invoice_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);  
				echo "1**".str_replace("'","",$invoice_id);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$invoice_id);
			}
		}
		disconnect($con);
		die;
	}
	
	else if ($operation==2)   // Update Here
	{
		/*$unique_check1 = is_duplicate_field( "id", "wo_po_yarn_info_details", "yarn_count_id=$update_id and status_active=1" );
		$unique_check2 = is_duplicate_field( "id", "wo_projected_order_child", "yarn_count_id=$update_id and status_active=1" );
		$unique_check3 = is_duplicate_field( "id", "wo_non_order_info_dtls", "Yarn_count_id 	=$update_id and status_active=1" );
		$unique_check4 = is_duplicate_field( "id", "inv_product_info_details", "yarn_count=$update_id and status_active=1" );*/
		
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$rID=sql_delete("com_import_payment",$field_array,$data_array,"id","".$update_id."",1);
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$invoice_id);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$invoice_id);
			}
		}
		disconnect($con);
		if($db_type==2 || $db_type==1 )
		{
	    	if($rID ){
				oci_commit($con);  
				echo "2**".str_replace("'","",$invoice_id);
			}
			else{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$invoice_id);
			}
		}
		
	}
}


if($action=="create_payment_list_view")
{
	
	//$data=explode('**',$data);
	 
	 $sql = "SELECT  a.payment_date,a.payment_head ,a.accepted_ammount ,a.adj_source, a.adj_source_ref, a.domistic_currency,b.invoice_no,b.maturity_date,a.id FROM com_import_payment a, com_import_invoice_mst b WHERE a.invoice_id =b.id and  a.invoice_id = $data  and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0";
	
	$supplier_lib=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name'); 
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$arr=array(3=>$commercial_head,5=>$commercial_head);
			
	echo  create_list_view("list_view", "Invoice No,Maturity Date,Payment Date,Payment Head,Accepted Amount,Adjusted Source, Adj. Source Ref.,Domestic Currency", "100,80,80,130,100,130,100","920","240",0, $sql , "get_php_form_data", "id", "'populate_payment_datails_data'", 1, "0,0,0,payment_head,0,adj_source,0,0", $arr , "invoice_no,maturity_date,payment_date,payment_head,accepted_ammount,adj_source,adj_source_ref,domistic_currency", "requires/import_payment_controller",'','0,3,3,0,1,0,0,1') ;
		 
}
if($action=="populate_payment_datails_data")
{
	$data_array=sql_select("select id,invoice_id,lc_id,payment_date,payment_head,adj_source,adj_source_ref,conversion_rate,accepted_ammount,domistic_currency,remarks from com_import_payment  where  id=$data"); 
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('update_id').value 							= '".$row[csf("id")]."';\n";
		echo "document.getElementById('import_payment_date').value 					= '".change_date_format($row[csf("payment_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_payment_head_id').value 					= '".$row[csf("payment_head")]."';\n";
		echo "document.getElementById('cbo_adj_source').value 						= '".$row[csf("adj_source")]."';\n";
		echo "document.getElementById('adj_source_ref').value 						= '".$row[csf("adj_source_ref")]."';\n";
		echo "document.getElementById('import_payment_conver_rate').value 			= '".$row[csf("conversion_rate")]."';\n";
		echo "document.getElementById('import_payment_accepted_ammount').value 		= '".$row[csf("accepted_ammount")]."';\n";
		echo "document.getElementById('import_payment_dom_currency').value 			= '".$row[csf("domistic_currency")]."';\n";
		echo "document.getElementById('txt_remarks').value 							= '".$row[csf("remarks")]."';\n";;
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_import_payment',1);\n";  
		exit();
	}
}
if($action=="set_head_cum_value")
{
	//echo "select sum(accepted_ammount) from com_import_payment  where invoice_id=$data[0] and payment_head =$data[1]";
	$data=explode("_",$data);
	//print_r($data);
	//echo "sum(accepted_ammount) from com_import_payment  where invoice_id=$data[0] and payment_head =$data[1]";
	$accepted_ammount =return_field_value("sum(accepted_ammount) as accepted_ammount","com_import_payment","invoice_id=$data[0] and payment_head =$data[1]");
	echo $accepted_ammount;
	die;
}

?>


 