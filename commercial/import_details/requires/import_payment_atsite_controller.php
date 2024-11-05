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
		echo create_drop_down( "cbo_supplier_id",140,"select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name ",'id,supplier_name', 1, '----Select----',0,0,0);
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
<div align="center" style="width:1030px;">
	<form name="searchscfrm"  id="searchscfrm">
		<fieldset style="width:100%;">
            <legend>Enter search words</legend>           
            	<table cellpadding="0" cellspacing="0" width="1030" class="rpt_table">
                	<thead>
                    	<th>Item Category</th>
                        <th>Issue Bank</th>
                    	<th class="must_entry_caption">Company</th>
                        <th>Supplier</th>
                        <th>Bank Ref.</th>
                        <th>L/C Number</th>
                        <th>Invoice Date</th>
                        <th>
                        	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        	<input type="hidden" name="hidden_invoice_id" id="hidden_invoice_id" value="" />
                            <input type="hidden" id="hidden_btb_id" />
                        </th>
                    </thead>
                    <tr class="general">
                    	
                        <td> 
                             <? echo create_drop_down( "cbo_item_category_id", 130, $item_category,'', 1, '--Select--',0,"load_drop_down( 'import_payment_atsite_controller',document.getElementById('txt_company_id').value+'_'+this.value,'load_supplier_dropdown','supplier_td' );",0); ?>  
                        </td>
                        <td> 
                             <? echo create_drop_down( "cbo_issue_bank", 130, "select bank_name,id from lib_bank where is_deleted=0 and status_active=1 and issusing_bank=1 order by bank_name","id,bank_name", 1, '--Select--',0,"",0); ?>  
                        </td>
                        <td>
                           <? 
								echo create_drop_down( "txt_company_id",130,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, 'Select',0,"load_drop_down( 'import_payment_atsite_controller',this.value+'_'+document.getElementById('cbo_item_category_id').value,'load_supplier_dropdown','supplier_td' );",0); 
							?>  
                         </td>
                         <td align="center" id="supplier_td">
                          <? echo create_drop_down( "cbo_supplier_id", 140,$blank_array,'', 1, '-- Select Supplier --',0,0,0); ?>       
                         </td> 
                         <td><input type="text" name="txt_bank_ref" id="txt_bank_ref" class="text_boxes" style="width:80px;" /></td>           
                         <td><input type="text" name="txt_lc_no" id="txt_lc_no" class="text_boxes" style="width:80px;" /></td>           
						<td> 
                        	 <input type="text" name="btb_start_date" id="btb_start_date" class="datepicker" style="width:65px;" />To
                             <input type="text" name="btb_end_date" id="btb_end_date" class="datepicker" style="width:65px;" />
                        </td>						
						                     
                         <td>
                 		  	<input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_company_id').value+'**'+document.getElementById('cbo_item_category_id').value+'**'+document.getElementById('cbo_supplier_id').value+'**'+document.getElementById('btb_start_date').value+'**'+document.getElementById('btb_end_date').value+'**'+document.getElementById('txt_bank_ref').value+'**'+document.getElementById('cbo_issue_bank').value+'**'+document.getElementById('txt_lc_no').value, 'create_invoice_list_view', 'search_div', 'import_payment_atsite_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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


/*if($action=="create_invoice_list_view")
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
	 
	$sql = "SELECT a.item_category_id, a.importer_id,a.supplier_id,a.lc_number,a.lc_value,b.invoice_no,b.invoice_date ,b.bank_ref,b.document_value,b.id,a.id as lc_id  
	FROM com_btb_lc_master_details a, com_import_invoice_mst b 
	WHERE a.id=b.btb_lc_id and  a.importer_id = '".$company."' and a.supplier_id like '".$supplier."' and a.item_category_id like '".$item_cate."' and b.bank_ref like '$bank_ref' and b.is_lc=1 and a.payterm_id =1 $issue_bank_cond $date and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
	union all 
	SELECT a.item_category_id, a.importer_id,a.supplier_id,a.lc_number,a.lc_value,b.invoice_no,b.invoice_date ,b.bank_ref,b.document_value,b.id,a.id as lc_id  
	FROM com_btb_lc_master_details a, com_import_invoice_mst b 
	WHERE a.id=b.btb_lc_id and  a.importer_id = '".$company."' and a.supplier_id like '".$supplier."' and a.item_category_id like '".$item_cate."' and b.bank_ref like '$bank_ref' and b.is_lc=2 $issue_bank_cond $date and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0";
	//echo $sql;
	
	
	
	$supplier_lib=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name'); 
	//$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	//$arr=array(0=>$company_library,1=>$supplier_lib);
	$arr=array(0=>$item_category,1=>$supplier_lib);
			
	echo  create_list_view("list_view", "Item Category,Supplier,L/C Number,L/C Value,Invoice No,Invoice Date,Bank Ref.,Document Amount", "90,140,100,100,100,80,100,100","900","300",0, $sql , "js_set_value", "id,lc_id", "", 1, "item_category_id,supplier_id,0,0,0,0,0,0", $arr , "item_category_id,supplier_id,lc_number,lc_value,invoice_no,invoice_date,bank_ref,document_value", "",'','0,0,0,2,0,3,0,2') ;
		
	exit();
}
*/

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
	$txt_lc_no =trim($data[7]);
	
	if($company_id==0)
	{
		echo 'Select Importer';die;
	}
	
	if ($company_id!=0) $company=$company_id;
	if ($item_category_id!=0) $item_cate=$item_category_id; else $item_cate='%%';
	if ($supplier_id!=0) $supplier=$supplier_id; else $supplier='%%';
	if ($txt_bank_ref!='') $bank_ref=$txt_bank_ref; else $bank_ref='%%';
	if ($txt_lc_no!='') $lc_no=$txt_lc_no; else $lc_no='%%';
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

	if ($db_type==2) $app_nes_setup_date=change_date_format(date('d-m-Y'), "", "",1);
    else if ($db_type==0) $app_nes_setup_date=change_date_format(date('d-m-Y'),'yyyy-mm-dd');
    $approval_status="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '$app_nes_setup_date' and company_id='$company')) and page_id=39 and status_active=1 and is_deleted=0";
    $app_need_setup=sql_select($approval_status);
    $approval_need=$app_need_setup[0][csf("approval_need")];

    if ($approval_need ==1) // If Approval Necessity Setup Yes then only approved invoice show only
    {
    	$sql = "SELECT a.item_category_id, a.importer_id,a.supplier_id,a.lc_number,a.lc_value,b.invoice_no,b.invoice_date ,b.bank_ref,b.document_value,b.id,a.id as lc_id  
		FROM com_btb_lc_master_details a, com_import_invoice_mst b 
		WHERE a.id=b.btb_lc_id and  a.importer_id = '".$company."' and a.supplier_id like '".$supplier."' and a.item_category_id like '".$item_cate."' and b.bank_ref like '$bank_ref' and a.lc_number like '$lc_no' and b.is_lc=1 and a.payterm_id =1 $issue_bank_cond $date and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.approved=1 and a.ref_closing_status=0
		union all 
		SELECT a.item_category_id, a.importer_id,a.supplier_id,a.lc_number,a.lc_value,b.invoice_no,b.invoice_date ,b.bank_ref,b.document_value,b.id,a.id as lc_id  
		FROM com_btb_lc_master_details a, com_import_invoice_mst b 
		WHERE a.id=b.btb_lc_id and  a.importer_id = '".$company."' and a.supplier_id like '".$supplier."' and a.item_category_id like '".$item_cate."' and b.bank_ref like '$bank_ref' and a.lc_number like '$lc_no' and b.is_lc=2 and a.lc_type_id not in(4,5,6) $issue_bank_cond $date and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.approved=1 and a.ref_closing_status=0";
    }
    else
    {
    	$sql = "SELECT a.item_category_id, a.importer_id,a.supplier_id,a.lc_number,a.lc_value,b.invoice_no,b.invoice_date ,b.bank_ref,b.document_value,b.id,a.id as lc_id  
		FROM com_btb_lc_master_details a, com_import_invoice_mst b 
		WHERE a.id=b.btb_lc_id and  a.importer_id = '".$company."' and a.supplier_id like '".$supplier."' and a.item_category_id like '".$item_cate."' and b.bank_ref like '$bank_ref' and a.lc_number like '$lc_no' and b.is_lc=1 and a.payterm_id =1 $issue_bank_cond $date and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and a.ref_closing_status=0
		union all 
		SELECT a.item_category_id, a.importer_id,a.supplier_id,a.lc_number,a.lc_value,b.invoice_no,b.invoice_date ,b.bank_ref,b.document_value,b.id,a.id as lc_id  
		FROM com_btb_lc_master_details a, com_import_invoice_mst b 
		WHERE a.id=b.btb_lc_id and  a.importer_id = '".$company."' and a.supplier_id like '".$supplier."' and a.item_category_id like '".$item_cate."' and b.bank_ref like '$bank_ref' and a.lc_number like '$lc_no' and b.is_lc=2 and a.lc_type_id not in(4,5,6) $issue_bank_cond $date and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and a.ref_closing_status=0";
    }	
	//echo $sql;
	
	$accepted_ammount_arr =return_library_array("select invoice_id, sum(accepted_ammount) as accepted_ammount from com_import_payment_com where status_active = 1 and is_deleted = 0 and payment_head=40 group by invoice_id","invoice_id","accepted_ammount");
	
	$supplier_lib=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name'); 
	//$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	//$arr=array(0=>$company_library,1=>$supplier_lib);
	//$arr=array(0=>$item_category,1=>$supplier_lib);
	//echo  create_list_view("list_view", "Item Category,Supplier,L/C Number,L/C Value,Invoice No,Invoice Date,Bank Ref.,Document Amount", "90,140,100,100,100,80,100,100","900","300",0, $sql , "js_set_value", "id,lc_id", "", 1, "item_category_id,supplier_id,0,0,0,0,0,0", $arr , "item_category_id,supplier_id,lc_number,lc_value,invoice_no,invoice_date,bank_ref,document_value", "",'','0,0,0,2,0,3,0,2') ;
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="930" class="rpt_table" >
        <thead>
            <th width="40">SL</th>
            <th width="100">Item Category</th>
            <th width="140">Supplier</th>
            <th width="100">L/C Number</th>
            <th width="100">L/C Value</th>
            <th width="100">Invoice No</th>
            <th width="80">Invoice Date</th>
            <th width="100">Bank Ref.</th>
            <th>Document Amount</th>
        </thead>
    </table>
    <div style="width:900px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="930" class="rpt_table" id="list_view" >
        <?
            $sql_result=sql_select($sql);
			$i=1; 
            foreach($sql_result as $row)
            {
                
				$balance_value=$row[csf('document_value')]-$accepted_ammount_arr[$row[csf('id')]];
				if($balance_value>0)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('lc_id')];?>')">
                        <td width="40" align="center"><? echo $i; ?></td>	
                        <td width="100"><p><? echo $item_category[$row[csf('item_category_id')]]; ?></p></td>
                        <td width="140"><p><? echo $supplier_lib[$row[csf('supplier_id')]]; ?></p></td>
                        <td width="100"><p><? echo $row[csf('lc_number')]; ?></p></td>
                        <td width="100" align="right"><p><? echo number_format($row[csf('lc_value')],2); ?></p></td>
                        <td width="100"><p><? echo $row[csf('invoice_no')]; ?></p></td>
                        <td width="80" align="center"><p><? echo change_date_format($row[csf('invoice_date')]); ?></p></td>
                        <td width="100"><p><? echo $row[csf('bank_ref')]; ?></p></td>
                        <td align="right" title="<? echo $balance_value; ?>"><p><? echo number_format($row[csf('document_value')],2); ?></p></td>
                    </tr>
                    <?
                    $i++;
				}
				
            }
        ?>
        </table>
    </div>
	<?
	exit();
}

if($action=="open_system_popup")
{
	echo load_html_head_contents("BTB / Import LC List", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		function js_set_value(id)
		{
			
			var id_array=id.split("_");
			$('#hidden_invoice_id').val(id_array[0]);
			$('#hidden_btb_id').val(id_array[1]);
			$('#hidden_system_id').val(id_array[2]);
			$('#hidden_system_no').val(id_array[4]);
			$('#hidden_payment_date').val(id_array[3]);
			$('#hidden_posted_account').val(id_array[5]);
			//alert($('#hidden_posted_account').val())
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:1000px;">
	<form name="searchscfrm"  id="searchscfrm">
		<fieldset style="width:100%;">
            <legend>Enter search words</legend>           
            	<table cellpadding="0" cellspacing="0" width="1000" class="rpt_table">
                	<thead>
                    	<th>Item Category</th>
                        <th>Issue Bank</th>
                    	<th class="must_entry_caption">Company</th>
                        <th>Supplier</th>
                        <th>System No</th>
                        <th>Bank Ref.</th>
                        <th>Payment Date</th>
                        <th>
                        	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        	<input type="hidden" name="hidden_invoice_id" id="hidden_invoice_id" value="" />
                            <input type="hidden" id="hidden_btb_id" />
                            <input type="hidden" name="hidden_payment_date" id="hidden_payment_date" value="" />
                            <input type="hidden" id="hidden_system_no" />
                            <input type="hidden" id="hidden_system_id" />
                            <input type="hidden" id="hidden_posted_account" />
                        </th>
                    </thead>
                    <tr class="general">
                    	
                        <td> 
                             <? echo create_drop_down( "cbo_item_category_id", 130, $item_category,'', 1, '--Select--',0,"load_drop_down( 'import_payment_atsite_controller',document.getElementById('txt_company_id').value+'_'+this.value,'load_supplier_dropdown','supplier_td' );",0); ?>  
                        </td>
                        <td> 
                             <? echo create_drop_down( "cbo_issue_bank", 130, "select bank_name,id from lib_bank where is_deleted=0 and status_active=1 and issusing_bank=1 order by bank_name","id,bank_name", 1, '--Select--',0,"",0); ?>  
                        </td>
                        <td>
                           <? 
								echo create_drop_down( "txt_company_id",130,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, 'Select',0,"load_drop_down( 'import_payment_atsite_controller',this.value+'_'+document.getElementById('cbo_item_category_id').value,'load_supplier_dropdown','supplier_td' );",0); 
							?>  
                         </td>
                         <td align="center" id="supplier_td">
                          <? echo create_drop_down( "cbo_supplier_id", 140,$blank_array,'', 1, '-- Select Supplier --',0,0,0); ?>       
                         </td> 
                         <td><input type="text" name="txt_systen_no" id="txt_systen_no" class="text_boxes" style="width:60px;" /></td> 
                         <td><input type="text" name="txt_bank_ref" id="txt_bank_ref" class="text_boxes" style="width:80px;" /></td>           
						<td> 
                        	 <input type="text" name="btb_start_date" id="btb_start_date" class="datepicker" style="width:65px;" />To
                             <input type="text" name="btb_end_date" id="btb_end_date" class="datepicker" style="width:65px;" />
                        </td>						
						                     
                         <td>
                 		  	<input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_company_id').value+'**'+document.getElementById('cbo_item_category_id').value+'**'+document.getElementById('cbo_supplier_id').value+'**'+document.getElementById('btb_start_date').value+'**'+document.getElementById('btb_end_date').value+'**'+document.getElementById('txt_bank_ref').value+'**'+document.getElementById('cbo_issue_bank').value+'**'+document.getElementById('txt_systen_no').value, 'create_system_list_view', 'search_div', 'import_payment_atsite_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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

if($action=="create_system_list_view")
{
	
	$data=explode('**',$data);//0->company,1->Item Category, 2->Supplier,3->Start Date 4->End Date, 5->Search Text 
	$company_id = $data[0];
	$item_category_id = $data[1];
	$supplier_id = $data[2];
	$invoice_start_date = $data[3];
	$invoice_end_date = $data[4];
	$txt_bank_ref =trim($data[5]);
	$cbo_issue_bank =trim(str_replace("'","",$data[6]));
	$txt_system_no =trim($data[7]);
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
	if($txt_system_no!="") $system_no_cond="and d.system_number_prefix_num='$txt_system_no'"; else $system_no_cond="";
	//if ($invoice_start_date!='') $start_date=$invoice_start_date; else $start_date='%%';
	//if ($invoice_end_date!='') $end_date=$invoice_end_date; else $end_date='%%';
	
	if($invoice_start_date!='' && $invoice_end_date!='')
	{
		if($db_type==0)
		{
			$date = "and d.payment_date between '".change_date_format($invoice_start_date,'yyyy-mm-dd','-')."' and '".change_date_format($invoice_end_date,'yyyy-mm-dd','-')."'";
		}
		else
		{
			$date = "and d.payment_date between '".change_date_format($invoice_start_date,'','',1)."' and '".change_date_format($invoice_end_date,'','',1)."'";
		}
	}
	else
	{
		$date = "";
	}
	
	
	//$sql="select sum(b.domistic_currency) as domistic_currency, "; 
	$sql = "SELECT d.is_posted_account,d.id as system_id,d.system_number,a.item_category_id, a.importer_id,a.supplier_id,a.lc_number,a.lc_value,b.invoice_no,d.payment_date ,b.bank_ref,b.document_value,b.id,a.id as lc_id  
	FROM com_btb_lc_master_details a, com_import_invoice_mst b,com_import_payment_com c,com_import_payment_com_mst d 
	WHERE d.id=c.mst_id and c.invoice_id=b.id and b.btb_lc_id=a.id and   a.importer_id = '".$company."' and a.supplier_id like '".$supplier."' and a.item_category_id like '".$item_cate."' and b.bank_ref like '$bank_ref' and b.is_lc=1 and a.payterm_id =1 $issue_bank_cond $date  $system_no_cond  and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 
	group by d.is_posted_account,d.id,d.system_number,a.item_category_id, a.importer_id,a.supplier_id,a.lc_number,a.lc_value,b.invoice_no,d.payment_date ,b.bank_ref,b.document_value,b.id,a.id
	union all
	SELECT d.is_posted_account,d.id as system_id,d.system_number,a.item_category_id, a.importer_id,a.supplier_id,a.lc_number,a.lc_value,b.invoice_no,d.payment_date ,b.bank_ref,b.document_value,b.id,a.id as lc_id  
	FROM com_btb_lc_master_details a, com_import_invoice_mst b,com_import_payment_com c,com_import_payment_com_mst d 
	WHERE d.id=c.mst_id and c.invoice_id=b.id and b.btb_lc_id=a.id and   a.importer_id = '".$company."' and a.supplier_id like '".$supplier."' and a.item_category_id like '".$item_cate."' and b.bank_ref like '$bank_ref' and b.is_lc=2 $issue_bank_cond $date  $system_no_cond  and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 
	group by d.is_posted_account,d.id,d.system_number,a.item_category_id, a.importer_id,a.supplier_id,a.lc_number,a.lc_value,b.invoice_no,d.payment_date ,b.bank_ref,b.document_value,b.id,a.id";
	//echo $sql;
	$supplier_lib=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name'); 
	//$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	//$arr=array(0=>$company_library,1=>$supplier_lib);
	$arr=array(1=>$item_category,2=>$supplier_lib);
			
	echo  create_list_view("list_view", "System No,Item Category,Supplier,L/C Number,L/C Value,Invoice No,Invoice Date,Bank Ref.,Document Amount", "110,90,140,100,100,100,80,100,100","1000","300",0, $sql , "js_set_value", "id,lc_id,system_id,payment_date,system_number,is_posted_account,", "", 1, "0,item_category_id,supplier_id,0,0,0,0,0,0", $arr , "system_number,item_category_id,supplier_id,lc_number,lc_value,invoice_no,payment_date,bank_ref,document_value", "",'','0,0,0,0,2,0,3,0,2') ;
		
	exit();
}


if($action=='populate_data_from_btb_lc')
{
	$data=explode("_",$data);
	
	$comp = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');
	$exportPiSupp = sql_select("select c.import_pi, a.id from com_btb_lc_master_details a , com_btb_lc_pi b , com_pi_master_details c where a.id = b.com_btb_lc_master_details_id and b.pi_id = c.id");
	foreach ($exportPiSupp as $value)
	{
		$exportPiSuppArr[$value[csf("id")]] = $value[csf("import_pi")];
	}
	
	if($db_type==0)
	{
		$data_array=sql_select("select a.id,a.lc_number,a.issuing_bank_id,a.lc_value,a.currency_id,a.supplier_id,a.importer_id,a.item_category_id,a.maturity_from_id,b.id as invoice_id,b.invoice_no,b.bank_ref,b.bank_acc_date,maturity_date,b.shipment_date,b.bill_date, sum(c.current_acceptance_value) as current_acceptance_value from com_btb_lc_master_details a,com_import_invoice_mst b, com_import_invoice_dtls c where a.id=b.btb_lc_id and a.id=c.btb_lc_id and b.id=c.import_invoice_id and a.id='$data[0]' and b.id='$data[1]'");
	}
	else
	{
		$data_array=sql_select("SELECT a.id,a.lc_number,a.issuing_bank_id,a.lc_value,a.currency_id,a.supplier_id,a.importer_id,a.item_category_id,a.maturity_from_id,b.id as invoice_id,b.invoice_no,b.bank_ref,b.bank_acc_date,maturity_date,b.shipment_date,b.bill_date, sum(c.current_acceptance_value) as current_acceptance_value , b.exchange_rate
		from com_btb_lc_master_details a,com_import_invoice_mst b, com_import_invoice_dtls c 
		where a.id=b.btb_lc_id and a.id=c.btb_lc_id and b.id=c.import_invoice_id and a.id='$data[0]' and b.id='$data[1]' 
		group by a.id,a.lc_number,a.issuing_bank_id, a.lc_value,a.currency_id,a.supplier_id, a.importer_id,a.item_category_id, a.maturity_from_id,b.id, b.invoice_no, b.bank_ref, b.bank_acc_date,maturity_date,b.shipment_date,b.bill_date, b.exchange_rate");
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
		//echo "load_drop_down( 'requires/import_payment_atsite_controller', '".$row[csf('importer_id')]."'+'_'+'".$row[csf('item_category_id')]."', 'load_supplier_dropdown', 'supplier_td');\n";
		if($exportPiSuppArr[$row[csf("id")]] == 1)
		{
			echo '$("#cbo_supplier_id option[value!=\'0\']").remove();'."\n";
			echo '$("#cbo_supplier_id").append("<option selected value=\''.$row[csf("supplier_id")].'\'>'.$comp[$row[csf("supplier_id")]].'</option>");'."\n";
		}
		else
		{
			echo "document.getElementById('cbo_supplier_id').value 				= '".$row[csf("supplier_id")]."';\n";
		}
		
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
		echo "document.getElementById('import_payment_conver_rate').value 			= '".$row[csf("exchange_rate")]."';\n";

		$accepted_ammount =return_field_value("sum(accepted_ammount) as accepted_ammount","com_import_payment_com"," status_active = 1 and is_deleted = 0 and payment_head=40 and invoice_id=$data[1] ","accepted_ammount");
		$inv_balance_val=$row[csf("current_acceptance_value")]-$accepted_ammount;
		echo "document.getElementById('inv_bal_value').value 				= '".$inv_balance_val."';\n";
		exit();
	}
}


if($action=='populate_data_from_mst')
{
	

	$data_array=sql_select("select payment_date from com_import_payment_com_mst where id=$data");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('import_payment_date').value= '".change_date_format($row[csf("payment_date")])."';\n";
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
		$id=return_next_id( "id", "com_import_payment_com_mst", 1 ) ;
		$dtls_id=return_next_id( "id", "com_import_payment_com", 1 ) ;
		
		if($db_type==0) $year_cond="YEAR(insert_date)"; 
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		
		if(str_replace("'","",$txt_system_no)=="") //insert
		{
			$new_return_number=explode("*",return_mrr_number( str_replace("'","",$cbo_importer_id), '', 'IMPC', date("Y",time()), 5, "select system_number_prefix,system_number_prefix_num from com_import_payment_com_mst where company_id=$cbo_importer_id and $year_cond=".date('Y',time())." order by id DESC", "system_number_prefix", "system_number_prefix_num" ));
		
			$field_array="id,system_number_prefix,system_number_prefix_num,system_number,company_id,invoice_id,lc_id,payment_date,inserted_by,insert_date";
			$data_array="(".$id.",'".$new_return_number[1]."','".$new_return_number[2]."','".$new_return_number[0]."',".$cbo_importer_id.",".$invoice_id.",".$btb_lc_id.",".$import_payment_date.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
		}
		else  	//update
		{	 
			$new_return_number[0]=str_replace("'","",$txt_system_no); 
			$id=str_replace("'","",$txt_system_id); 
		
 			$field_array="payment_date*updated_by*update_date";
			$data_array="".$import_payment_date."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";
		
		}
		
		$field_array1="id,mst_id,invoice_id,lc_id,payment_date,payment_head,adj_source,adj_source_ref,conversion_rate,accepted_ammount, domistic_currency, remarks,inserted_by,insert_date";
		$data_array1="(".$dtls_id.",".$id.",".$invoice_id.",".$btb_lc_id.",".$import_payment_date.",".$cbo_payment_head_id.",".$cbo_adj_source.",".$adj_source_ref.",".$import_payment_conver_rate.",".$import_payment_accepted_ammount.",".$import_payment_dom_currency.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		if(str_replace("'","",$txt_system_no)=="")
		{
			
			$rID=sql_insert("com_import_payment_com_mst",$field_array,$data_array,1);
			//echo "10**$rID**insert into com_import_payment_com_mst($field_array)values".$data_array;die;
		}
		else
		{
			$rID=sql_update("com_import_payment_com_mst",$field_array,$data_array,"id",$id,0);	
		}
		
		//	echo "<br/>10**insert into com_import_payment_com($field_array1)values".$data_array1;die;
		$rID1=sql_insert("com_import_payment_com",$field_array1,$data_array1,1);
		//echo "10**".$rID."**".$rID1;die;
		if($db_type==0)
		{
			if($rID && $rID1){
				mysql_query("COMMIT");  
				echo "0**".str_replace("'","",$id)."**".str_replace("'","",$new_return_number[0]);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$id)."**".str_replace("'","",$new_return_number[0]);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1){
				oci_commit($con);   
				echo "0**".str_replace("'","",$id)."**".str_replace("'","",$new_return_number[0]);
			}
			else{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$id)."**".str_replace("'","",$new_return_number[0]);
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
		
		$field_array_mst="payment_date";
		$data_array_mst="".$import_payment_date."";
		
		$field_array="invoice_id*lc_id*payment_date*payment_head*adj_source*adj_source_ref*conversion_rate*accepted_ammount*domistic_currency*remarks*updated_by*update_date";
		$data_array="".$invoice_id."*".$btb_lc_id."*".$import_payment_date."*".$cbo_payment_head_id."*".$cbo_adj_source."*".$adj_source_ref."*".$import_payment_conver_rate."*".$import_payment_accepted_ammount."*".$import_payment_dom_currency."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_update("com_import_payment_com",$field_array,$data_array,"id","".$update_id."",1);
		$rID1=sql_update("com_import_payment_com_mst",$field_array_mst,$data_array_mst,"id","".$txt_system_id."",1);
		
		$rID2=execute_query("update com_import_payment_com set payment_date=".$import_payment_date." where mst_id=".$txt_system_id." ",0);
		//echo $rID."**".$rID1."**".$rID2;die;
		if($db_type==0)
		{
			if($rID && $rID1 && $rID2){
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_system_id)."**".str_replace("'","",$txt_system_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_system_id)."**".str_replace("'","",$txt_system_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1 && $rID2){
				oci_commit($con);  
				echo "1**".str_replace("'","",$txt_system_id)."**".str_replace("'","",$txt_system_no);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_system_id)."**".str_replace("'","",$txt_system_no);
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
		
		$posted_account =return_field_value("is_posted_account","com_import_payment_com_mst"," status_active = 1 and is_deleted = 0 and invoice_id=$invoice_id","is_posted_account");
		
		if($posted_account)
		{
			echo "30**Delete Not Allow, Already Posted In Accounts";disconnect($con);die;
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		//$rID=sql_delete("com_import_payment_com",$field_array,$data_array,"id","".$update_id."",1);
		$rID=sql_delete("com_import_payment_com",$field_array,$data_array,"mst_id","".$txt_system_id."",1);
		$rID2=sql_delete("com_import_payment_com_mst",$field_array,$data_array,"id","".$txt_system_id."",1);
		//$rID=sql_delete("com_import_payment_com",$field_array,$data_array,"id","".$update_id."",1);
		
		if($db_type==0)
		{
			if($rID && $rID2){
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_system_id)."**".str_replace("'","",$txt_system_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_system_id)."**".str_replace("'","",$txt_system_no);
			}
		}		
		if($db_type==2 || $db_type==1 )
		{
	    	if($rID && $rID2){
				oci_commit($con);  
				echo "2**".str_replace("'","",$txt_system_id)."**".str_replace("'","",$txt_system_no);
			}
			else{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$txt_system_id)."**".str_replace("'","",$txt_system_no);
			}
		}
		disconnect($con);die;
	}
}


if($action=="create_payment_list_view")
{
	
	//$data=explode('_',$data);
	 
	  $sql = "SELECT  a.payment_date,a.payment_head ,a.accepted_ammount ,a.adj_source, a.adj_source_ref, a.domistic_currency,b.invoice_no,b.maturity_date,a.id FROM com_import_payment_com_mst i,com_import_payment_com a, com_import_invoice_mst b WHERE i.id=a.mst_id and a.invoice_id =b.id and  i.id = $data and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and i.status_active = 1 and i.is_deleted = 0";
	 
	// $sql = "SELECT  a.payment_date,a.payment_head ,a.accepted_ammount ,a.adj_source, a.adj_source_ref, a.domistic_currency,b.invoice_no,b.maturity_date,a.id FROM com_import_payment_com a, com_import_payment_com_mst b WHERE a.mst_id =b.id and  b.id = $data[1] and a.invoice_id=$data[0]  and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0";
	//echo $sql;
	$supplier_lib=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name'); 
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$arr=array(3=>$commercial_head,5=>$commercial_head);
			
	echo  create_list_view("list_view", "Invoice No,Maturity Date,Payment Date,Payment Head,Accepted Amount,Adjusted Source, Adj. Source Ref.,Domestic Currency", "100,80,80,130,100,130,100","920","240",0, $sql , "get_php_form_data", "id", "'populate_payment_datails_data'", 1, "0,0,0,payment_head,0,adj_source,0,0", $arr , "invoice_no,maturity_date,payment_date,payment_head,accepted_ammount,adj_source,adj_source_ref,domistic_currency", "requires/import_payment_atsite_controller",'','0,3,3,0,2,0,0,1') ;
		 
}
if($action=="populate_payment_datails_data")
{
	$data_array=sql_select("select id,invoice_id,lc_id,payment_date,payment_head,adj_source,adj_source_ref,conversion_rate,accepted_ammount,domistic_currency,remarks from com_import_payment_com  where  status_active = 1 and is_deleted = 0 and  id=$data"); 
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('update_id').value 							= '".$row[csf("id")]."';\n";
		echo "document.getElementById('import_payment_date').value 					= '".change_date_format($row[csf("payment_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_payment_head_id').value 					= '".$row[csf("payment_head")]."';\n";
		echo "document.getElementById('cbo_adj_source').value 						= '".$row[csf("adj_source")]."';\n";
		echo "document.getElementById('adj_source_ref').value 						= '".$row[csf("adj_source_ref")]."';\n";
		echo "document.getElementById('import_payment_conver_rate').value 			= '".$row[csf("conversion_rate")]."';\n";
		echo "document.getElementById('import_payment_accepted_ammount').value 		= '".$row[csf("accepted_ammount")]."';\n";
		echo "document.getElementById('previous_accepted_ammount').value 		= '".$row[csf("accepted_ammount")]."';\n";
		echo "document.getElementById('import_payment_dom_currency').value 			= '".$row[csf("domistic_currency")]."';\n";
		echo "document.getElementById('txt_remarks').value 							= '".$row[csf("remarks")]."';\n";;
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_import_payment',1);\n";  
		exit();
	}
}
if($action=="set_head_cum_value")
{
	
	$data=explode("_",$data);
	//echo "select sum(accepted_ammount) from com_import_payment_com  where invoice_id=$data[0] and payment_head =$data[1]";
	//print_r($data);
	//echo "sum(accepted_ammount) from com_import_payment_com  where invoice_id=$data[0] and payment_head =$data[1]";
	$accepted_ammount =return_field_value("sum(accepted_ammount) as accepted_ammount","com_import_payment_com"," status_active = 1 and is_deleted = 0 and invoice_id=$data[0] and payment_head =$data[1]","accepted_ammount");
	echo $accepted_ammount;
	die;
}

/**
 * Check conversion rate action will return company wise last conversion rate before given date
 * Added by Shafiq-sumon
 */
if($action=="check_conversion_rate")
{
	$data=explode("**",$data);
	//var_dump($data);die;
	if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$currency_rate=set_conversion_rate_company_wise( $data[0], $conversion_date, $data[2] );
	echo "1"."_".$currency_rate;
	exit();
}
/**
 * set_conversion_rate_company_wise function will return company wise last conversion rate before given date
 * Added by Shafiq-sumon
 */
function set_conversion_rate_company_wise($cid, $cdate, $company_id ) {
	global $db_type;

	if ($cdate == '') {
		if ($db_type == 0) {
			$cdate = date("Y-m-d", time());
		} else {
			$cdate = date("d-M-y", time());
		}

	}
	if ($cid == 1) {
		return "1";
	} else {
		if ($db_type == 0) {
			$cdate = change_date_format($cdate, "yyyy-mm-dd", "-", 1);
		} else {
			$cdate = change_date_format($cdate, "d-M-y", "-", 1);
		}
		//echo $cdate;die;
		$queryText = "select conversion_rate from currency_conversion_rate where con_date<='" . $cdate . "' and currency=$cid and company_id = $company_id and status_active=1 and is_deleted=0 order by con_date desc";
		//echo $queryText; die;
		$nameArray = sql_select($queryText, '', $new_conn);
		if (count($nameArray) > 0) {
			foreach ($nameArray as $result) {
				if ($result[csf('conversion_rate')] != "") {
					return $result[csf("conversion_rate")];
				} else {
					return "0";
				}
			}

		} else {
			return "0";
		}

	}
}
?>


 