<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$item_category_without_general=array_diff($item_category,$general_item_category);
$genarel_item_arr=array(4=>"Accessories",8=>"General Item");
$item_category_with_gen=$item_category_without_general+$genarel_item_arr;
ksort($item_category_with_gen);

if ($action=="load_supplier_dropdown")
{
	//echo $data;
	$data = explode('_',$data);
	
	// if ($data[1]==0) echo create_drop_down( "cbo_supplier_id", 150, $blank_array,'', 1, '----Select----',0,0,0);
	if ($data[1]==0){
		echo create_drop_down( "cbo_supplier_id",150,"SELECT c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name ",'id,supplier_name', 1, '----Select----',0,0,0);
	}	
	else if($data[1]==1)
	{
		echo create_drop_down( "cbo_supplier_id", 150,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type =2",'id,supplier_name', 1, '----Select----',0,0,0);		
	}
	else if($data[1]==2 || $data[1]==3 || $data[1]==13 || $data[1]==14)
	{
		echo create_drop_down( "cbo_supplier_id", 150,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type =9",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==4)
	{
		echo create_drop_down( "cbo_supplier_id", 150,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type in(4,5)",'id,supplier_name', 1, '----Select----',0,0,0);
		
	}
	else if($data[1]==5 || $data[1]==6 || $data[1]==7)
	{
		echo create_drop_down( "cbo_supplier_id", 150,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type=3",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==8)
	{
		echo create_drop_down( "cbo_supplier_id", 150,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type = 7",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==9 || $data[1]==10)
	{
		echo create_drop_down( "cbo_supplier_id", 150,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type = 6",'id,supplier_name', 1, '----Select----',0,0,0);
	} 
	else if($data[1]==11)
	{
		echo create_drop_down( "cbo_supplier_id", 150,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type = 8",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==12 || $data[1]==24 || $data[1]==25)
	{
		echo create_drop_down( "cbo_supplier_id", 150,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type in(20,21,22,23,24,30,31,32,35,36,37,38,39)",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else
	{
		echo create_drop_down( "cbo_supplier_id", 150,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type = 7 and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
	} 
	
	exit();  
}



if($action=="set_maturity_date")
{
	$data=explode("_",$data);
	$date=change_date_format($data[0],'yyyy-mm-dd','-');
	echo date('d-m-Y', strtotime($date. ' + '.$data[1].' days'));
}

if($action=="check_duplicate_invoice")
{
	$data=explode("_",$data);
	$invoice_no=return_field_value("a.invoice_no","com_import_invoice_mst a, com_import_invoice_dtls b"," a.id=b.import_invoice_id and a.invoice_no='$data[0]' and  b.btb_lc_id in($data[1]) and a.status_active=1 and a.is_deleted=0 group by a.invoice_no");
	echo trim($invoice_no);
	die;
}

if($action=="check_conversion_rate")
{
	$data=explode("**",$data);
	if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$currency_rate=set_conversion_rate( $data[0], $conversion_date );
	echo "1"."_".$currency_rate;
	exit();
}
 
 
if($action=="open_import_lc_popup")
{
	echo load_html_head_contents("BTB / Import LC List", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
	/*
		var selected_id = new Array;
		//var selected_name = new Array();	
	 function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length-1; 

			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				//js_set_value( i );
				document.getElementById( 'tr_' + i ).click();
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			var str_array=str.split("_");
			toggle( document.getElementById( 'tr_' + str_array[0] ), '#FFFFCC' );
			
			if( jQuery.inArray( str_array[1], selected_id ) == -1 ) {
				selected_id.push( str_array[1] );
				//selected_name.push( str_array[2] );

				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str_array[1] ) break;
				}
				selected_id.splice( i, 1 );
				//selected_name.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				//name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			//name = name.substr( 0, name.length - 1 );

			
			$('#hidden_btb_id').val( id );
			//$('#hidden_btb_number').val( id );
			
		}*/

		function js_set_value(id)
		{
			var data=id.split("_");
			$('#hidden_btb_id').val(data[1]);
			parent.emailwindow.hide();
		}
	
    </script>
</head>

<body>
<div align="center" style="width:1100px;">
	<form name="searchscfrm"  id="searchscfrm">
		<fieldset style="width:100%;">
            <legend>Enter search words</legend>           
            	<table cellpadding="0" cellspacing="0" width="1050" class="rpt_table" rules="all" border="0">
                	<thead>
                    	<th>Company</th>
                    	<th>Item Category</th>
                        <th>Supplier</th>
                        <th>Issuing Bank</th>
                        <th>L/C Type</th>
                        <th>L/C Number</th>
                        <th>Currency</th>
                        <th>
                        	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" id="hidden_btb_id" />
                        	<!--<input type="hidden" name="hidden_btb_number" id="hidden_btb_number" value="" />-->
                        </th>
                    </thead>
                    <tr class="general">
                    	<td>
                           <?
						   		//and comp.core_business not in(3) 
								echo create_drop_down( "txt_company_id",165,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, 'Select',0,"load_drop_down( 'import_document_acceptance_non_lc_controller',this.value,'load_supplier_dropdown','supplier_td' );",0); 
							?>  
                         </td>
                        <td> 
                             <? echo create_drop_down( "cbo_item_category_id", 165, $item_category_with_gen,'', 1, '--Select--',0,"load_drop_down( 'import_document_acceptance_non_lc_controller',document.getElementById('txt_company_id').value+'_'+this.value,'load_supplier_dropdown','supplier_td' );",0); ?>  
                        </td>
                        
                         <td align="center" id="supplier_td">
                          <? echo create_drop_down( "cbo_supplier_id", 150,$blank_array,'', 0, '',0,0,0); ?>       
                          
                         </td>            
						<td> 
                        	 <? echo create_drop_down( "cbo_issuing_bank", 165,"select id,bank_name from lib_bank where is_deleted=0 and status_active=1 and issusing_bank = 1 order by bank_name",'id,bank_name', 1, '-Select-',0,0,""); ?>
                        </td>						
						<td>
							<? echo create_drop_down( "cbo_lc_type_id",165,$lc_type,'',1,'-Select-',"","",""); ?>
                            
            			</td> 
						<td>
							<input type="text" id="txt_lc_number" name="txt_lc_number" class="text_boxes" style="width:100px;" placeholder="LC Number" />
                            
            			</td> 
                        <td>
							<? echo create_drop_down( "cbo_lc_currency_id",70,$currency,'',"",'',2,0,""); ?>
                            
            			</td>                       
                         <td>
                 		  	<input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_company_id').value+'**'+document.getElementById('cbo_item_category_id').value+'**'+document.getElementById('cbo_supplier_id').value+'**'+document.getElementById('cbo_issuing_bank').value+'**'+document.getElementById('cbo_lc_type_id').value+'**'+document.getElementById('txt_lc_number').value+'**'+document.getElementById('cbo_lc_currency_id').value, 'create_btb_search_list_view', 'search_div', 'import_document_acceptance_non_lc_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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

if($action=="create_btb_search_list_view")
{
	$data=explode('**',$data);
	$company_id = $data[0];
	$item_category_id = $data[1];
	$supplier_id = $data[2];
	$issuing_bank_id = $data[3];
	$lc_type_id = $data[4]; 
	$lc_number = $data[5]; 
	$lc_currency_id = $data[6];
	if($company_id==0)
	{
		echo 'Select Importer';die;
	}

	if ($company_id!=0) $company=$company_id; else $company='%%';
	//if ($item_category_id!=0) $item_category_id=$item_category_id; else $item_category_id='%%';
	if ($supplier_id!=0) $supplier=$supplier_id; else $supplier='%%';
	if ($issuing_bank_id!=0) $bank_name=$issuing_bank_id; else $bank_name='%%';
	if ($lc_type_id!=0) $lc_type_id=$lc_type_id; else $lc_type_id='%%';
	if ($lc_number!="") $lc_number_cond=" and lc_number like '%$lc_number'"; else $lc_number_cond='';
	if ($lc_currency_id!=0) $lc_currency_id=$lc_currency_id; else $lc_currency_id='%%';
	
	

	//$item_category_entry_form=0;
	//if($item_category_id>0) $item_category_entry_form=$category_wise_entry_form[$item_category_id];
	//$sql_cond="";
	//if($item_category_entry_form>0) $sql_cond.=" and pi_entry_form =$item_category_entry_form";
	
	if($item_category_id!=0)
	{
		if($item_category_id==8) $cat_id=" and c.item_category_id in(select category_id from lib_item_category_list where category_type=1)";
		else $cat_id=" and c.item_category_id = '$item_category_id'";
	}
	else
	{
		$cat_id="";
	}
	 
	$sql = "SELECT a.id, a.btb_prefix_number, a.btb_system_id, a.lc_number, a.supplier_id, a.application_date, a.last_shipment_date, a.lc_date, a.lc_value, a.supplier_id, a.importer_id, a.payterm_id 
	FROM com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c 
	WHERE a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and a.issuing_bank_id like '".$bank_name."' and a.importer_id like '".$company_id."' and a.supplier_id like '".$supplier."' and a.lc_type_id like '".$lc_type_id."' and a.currency_id like '".$lc_currency_id."' and a.payterm_id=3 and a.status_active = 1 and a.is_deleted = 0 $lc_number_cond  $sql_cond $date $cat_id 
	group by a.id, a.btb_prefix_number, a.btb_system_id, a.lc_number, a.supplier_id, a.application_date, a.last_shipment_date, a.lc_date, a.lc_value, a.supplier_id, a.importer_id, a.payterm_id 
	order by id";
	
	//echo $sql; 
	
	$supplier_lib=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name'); 
	 
	$arr=array(1=>$supplier_lib,7=>$pay_term);
			
	echo  create_list_view("list_view", "System Id,Supplier,L/C Number,L/C Date,L/C Value,Application Date,Last Ship Date,Pay Term", "90,150,150,100,120,100,100,100","1000","240",0, $sql , "js_set_value", "id,lc_number", "", 1, "0,supplier_id,0,0,0,0,0,payterm_id", $arr , "btb_prefix_number,supplier_id,lc_number,lc_date,lc_value,application_date,last_shipment_date,payterm_id", "",'','0,0,0,3,2,3,3,0',"",1) ;
	
	exit();			 
}


if($action=="open_invoice_popup")
{
	echo load_html_head_contents("BTB / Import LC List", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
	var parmission="<? echo $_SESSION['page_permission']; ?>";
		function js_set_value(id)
		{
			//alert(id)
			var id_array=id.split("_");
			$('#hidden_invoice_id').val(id_array[0]);
			$('#hidden_btb_id').val(id_array[1]);
			$('#posted_in_account').val(id_array[2]);
			
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:880px;">
	<form name="searchscfrm"  id="searchscfrm">
		<fieldset style="width:100%;">
            <legend>Enter search words</legend>           
            	<table cellpadding="0" cellspacing="0" width="880" class="rpt_table" rules="all">
                	<thead>
                    	<th>Company</th>
                    	<th>Item Category</th>
                        <th>Supplier</th>
                        <th>BTB No</th>
                        <th>Invoice No</th>
                        <th>Invoice Date</th>
                        <th>
                        	<input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('searchscfrm','search_div','','','','')" />
                        	<input type="hidden" name="hidden_invoice_id" id="hidden_invoice_id" value="" />
                            <input type="hidden" id="hidden_btb_id" />
                            <input type="hidden" id="posted_in_account" />
                        </th>
                    </thead>
                    <tr class="general">
                    	<td>
                           <?
						   		//and comp.core_business not in(3)  
								echo create_drop_down( "txt_company_id",140,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, 'Select',0,"load_drop_down( 'import_document_acceptance_non_lc_controller',this.value+'_'+document.getElementById('cbo_item_category_id').value,'load_supplier_dropdown','supplier_td' );",0); 
							?>  
                        </td>
                        <td> 
                             <? echo create_drop_down( "cbo_item_category_id", 140, $item_category_with_gen,'', 1, '--Select--',0,"load_drop_down( 'import_document_acceptance_non_lc_controller',document.getElementById('txt_company_id').value+'_'+this.value,'load_supplier_dropdown','supplier_td' );",0); ?>  
                        </td>
                        
                        <td align="center" id="supplier_td">
                          <? echo create_drop_down( "cbo_supplier_id", 150,$blank_array,'', 0, '',0,0,0); ?>       
                        </td>            
						<td> 
                        	 <input type="text" name="txt_btb_no" id="txt_btb_no" class="text_boxes" style="width:90px;" />
                        </td>
                        <td> 
                        	 <input type="text" name="txt_invoice_no" id="txt_invoice_no" class="text_boxes" style="width:90px;" />
                        </td>						
						<td> 
                        	 <input type="text" name="btb_start_date" id="btb_start_date" class="datepicker" style="width:60px;" />To
                             <input type="text" name="btb_end_date" id="btb_end_date" class="datepicker" style="width:60px;" />
                        </td>                    
                        <td>
                 		  	<input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_company_id').value+'**'+document.getElementById('cbo_item_category_id').value+'**'+document.getElementById('cbo_supplier_id').value+'**'+document.getElementById('btb_start_date').value+'**'+document.getElementById('btb_end_date').value+'**'+document.getElementById('txt_btb_no').value+'**'+document.getElementById('txt_invoice_no').value, 'create_invoice_list_view', 'search_div', 'import_document_acceptance_non_lc_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                        </td>
					</tr>
               </table>
            </fieldset>
		</form>
	</div> 
    <div id="search_div" style="margin-top:10px;" ></div>
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
	$item_category_id = trim(str_replace("'","",$data[1]));
	$supplier_id = $data[2];
	$invoice_start_date = $data[3];
	$invoice_end_date = $data[4];
	$btb_no = $data[5];
	$invoice_no = $data[6];
	$item_category_entry_form=0;
	//if($item_category_id>0) $item_category_entry_form=$category_wise_entry_form[$item_category_id];
	
	if($company_id==0)
	{
		echo 'Select Importer';die;
	}
	
	/*if($item_category_id==0)
	{
		echo 'Select Item Category';die;
	}*/
	
	
	if ($company_id!=0) $company=$company_id;
	//if ($item_category_id!=0) $item_category=$item_category_id;
	if ($supplier_id!=0) $supplier=$supplier_id; else $supplier='%%';
	if ($invoice_start_date!='') $start_date=$invoice_start_date; else $start_date='%%';
	if ($invoice_end_date!='') $end_date=$invoice_end_date; else $end_date='%%';
	if($lc_start_date!='' && $lc_end_date!='')
	{
		$date = "and b.invoice_date between '".$start_date."' and '".$end_date."'";
	}
	else
	{
		$date = "";
	}
	$btb_no = $data[5];
	$invoice_no = $data[6];
	$sql_cond="";
	if($btb_no!="") $sql_cond.=" and a.lc_number like '%$btb_no%'";
	if($invoice_no!="") $sql_cond.=" and b.invoice_no like '%$invoice_no%'";
	//if($item_category_entry_form>0) $sql_cond.=" and a.pi_entry_form =$item_category_entry_form";
	if($item_category_id!=0)
	{
		if($item_category_id==8) $sql_cond.=" and c.item_category_id in(select category_id from lib_item_category_list where category_type=1)";
		else $sql_cond.=" and c.item_category_id = '$item_category_id'";
	}
	
	$sql = "SELECT  a.importer_id,a.supplier_id,a.lc_number,a.lc_value,b.invoice_no,b.invoice_date ,b.	bank_ref,b.document_value,b.id,b.btb_lc_id as lc_id, b.is_posted_account 
	FROM com_btb_lc_master_details a, com_import_invoice_mst b,  com_import_invoice_dtls c 
	WHERE a.id=b.BTB_LC_ID and c.import_invoice_id=b.id and  a.importer_id = '".$company."' and a.supplier_id like '".$supplier."' and b.is_lc=2 $date and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 $sql_cond";
	
	 //echo $sql;
	
	$supplier_lib=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name'); 
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$arr=array(0=>$company_library,1=>$supplier_lib);
	//function create_list_view( $table_id, $tbl_header_arr, $td_width_arr, $tbl_width, $tbl_height, $tbl_border, $query, $onclick_fnc_name, $onclick_fnc_param_db_arr, $onclick_fnc_param_sttc_arr,  $show_sl, $field_printed_from_array_arr,  $data_array_name_arr, $qry_field_list_array, $controller_file_path, $filter_grid_fnc, $fld_type_arr, $summary_flds, $check_box_all )
		
	echo  create_list_view("list_view", "Company,Supplier,L/C Number,L/C Value,Invoice No,Invoice Date,Bank Ref.,Document Amount", "120,150,110,90,100,70,90","880","340",0, $sql , "js_set_value", "id,lc_id,is_posted_account", "", 1, "importer_id,supplier_id,0,0,0,0,0,0", $arr , "importer_id,supplier_id,lc_number,lc_value,invoice_no,invoice_date,bank_ref,document_value", "",'','0,0,0,2,0,3,0,2') ;
		 
}





if($action=='populate_data_from_btb_lc')
{
	$data=explode("_",$data);
	
	//$data_array=sql_select("select id,btb_system_id,lc_number,issuing_bank_id,lc_value,currency_id,tolerance,tenor,maturity_from_id,supplier_id,importer_id,payterm_id,lc_type_id,item_category_id,port_of_discharge,port_of_loading,inco_term_place,inco_term_id,delivery_mode_id from com_btb_lc_master_details where id in($data[0])"); 
	
	$data_array=sql_select("SELECT a.id,a.btb_system_id,a.lc_number,a.issuing_bank_id,a.lc_value,a.currency_id,a.tolerance,a.tenor,a.maturity_from_id,a.supplier_id,a.importer_id,a.payterm_id,a.lc_type_id,a.item_category_id,a.port_of_discharge,a.port_of_loading,a.inco_term_place,a.inco_term_id,a.delivery_mode_id,a.lc_date,b.source 
	from com_btb_lc_master_details a, COM_BTB_LC_PI c, com_pi_master_details b  
	where a.id in($data[0])  and a.id=c.COM_BTB_LC_MASTER_DETAILS_ID and c.pi_id=b.id and a.is_deleted = 0 and a.status_active = 1");


	$lc_number="";
	$lc_id="";
	$lc_value=0;
	$lc_date="";
	//echo count($data_array);
	//die;
	foreach ($data_array as $row)
	{ 
		if($data[1]==1)
		{
			$internal_file_no="";
			$is_lc_sc_sql = sql_select("SELECT lc_sc_id, is_lc_sc FROM com_btb_export_lc_attachment where import_mst_id='".$row[csf("id")]."'");
			list($is_lc_sc_sql_row)=$is_lc_sc_sql;
			if($is_lc_sc_sql_row[csf("is_lc_sc")] == 0)
			{
				$internal_file_sql = sql_select("SELECT internal_file_no FROM com_export_lc where id='".$is_lc_sc_sql_row[csf("lc_sc_id")]."'");
				list($internal_file_sql_row)=$internal_file_sql;
				$internal_file_no=$internal_file_no.$internal_file_sql_row[csf("internal_file_no")].",";
			}
			else if($is_lc_sc_sql_row[csf("is_lc_sc")] == 1)
			{
				$internal_file_sql = sql_select("SELECT internal_file_no FROM com_sales_contract where id='".$is_lc_sc_sql_row[csf("lc_sc_id")]."'");
				list($internal_file_sql_row)=$internal_file_sql;
				$internal_file_no=$internal_file_no.$internal_file_sql_row[csf("internal_file_no")].",";
			}
			$lc_number=$lc_number.$row[csf("lc_number")].",";
			$lc_date=$lc_date.date('d.m.Y', strtotime($row[csf("lc_date")])).",";
			$lc_id=$lc_id.$row[csf("id")].",";
			$lc_value+=$row[csf("lc_value")];
			echo "document.getElementById('txt_lc_number').value = '".rtrim($lc_number,",")."';\n";
			echo "document.getElementById('btb_lc_id').value = '".rtrim($lc_id,",")."';\n";
			echo "document.getElementById('cbo_issuing_bank').value = '".$row[csf("issuing_bank_id")]."';\n";
			echo "document.getElementById('txt_lc_value').value = '".$lc_value."';\n";
			echo "document.getElementById('cbo_lc_currency_id').value = '".$row[csf("currency_id")]."';\n";
			//echo "document.getElementById('hid_tolarance').value = '".$row[csf("tolerance")]."';\n";
			echo "document.getElementById('cbo_importer_id').value = '".$row[csf("importer_id")]."';\n";
			//echo "document.getElementById('hid_tenor').value = '".$row[csf("tenor")]."';\n";
			//echo "document.getElementById('hid_maturity_from').value = '".$row[csf("maturity_from_id")]."';\n";


			echo "load_drop_down( 'requires/import_document_acceptance_non_lc_controller', '".$row[csf('importer_id')]."'+'_'+'".$row[csf('item_category_id')]."', 'load_supplier_dropdown', 'supplier_td');\n";
			echo "document.getElementById('cbo_supplier_id').value 			= '".$row[csf("supplier_id")]."';\n";
			echo "document.getElementById('cbo_supplier_id').disabled 			= true;\n";
			echo "document.getElementById('cbo_payterm_id').value 		= '".$row[csf("payterm_id")]."';\n";
			echo "document.getElementById('cbo_lc_type_id').value 			= '".$row[csf("lc_type_id")]."';\n";
			echo "document.getElementById('cbo_source_id').value 			= '".$row[csf("source")]."';\n";
			echo "$('#internal_file_no').val( '".rtrim($internal_file_no,",")."' );\n";
			echo "$('#port_of_discharge').val( '$row[port_of_discharge]' );\n";   
			echo "$('#port_of_loading').val( '$row[port_of_loading]' );\n";   
			echo "$('#inco_term_place').val( '$row[inco_term_place]' );\n";   
			echo "$('#cbo_inco_term').val( '$row[inco_term_id]' );\n";   
			echo "$('#cbo_shipment_mode').val( '$row[delivery_mode_id]' );\n";
			
			echo "document.getElementById('hid_lc_date').value = '".rtrim($lc_date,",")."';\n";
			//exit();
		}
		if($data[1]==2)
		{
			$internal_file_no="";
			$is_lc_sc_sql = sql_select("SELECT lc_sc_id, is_lc_sc FROM com_btb_export_lc_attachment where import_mst_id='".$row[csf("id")]."'");
			list($is_lc_sc_sql_row)=$is_lc_sc_sql;
			if($is_lc_sc_sql_row[csf("is_lc_sc")] == 0)
			{
				$internal_file_sql = sql_select("SELECT internal_file_no FROM com_export_lc where id='".$is_lc_sc_sql_row[csf("lc_sc_id")]."'");
				list($internal_file_sql_row)=$internal_file_sql;
				$internal_file_no=$internal_file_no.$internal_file_sql_row[csf("internal_file_no")].",";
			}
			else if($is_lc_sc_sql_row[csf("is_lc_sc")] == 1)
			{
				$internal_file_sql = sql_select("SELECT internal_file_no FROM com_sales_contract where id='".$is_lc_sc_sql_row[csf("lc_sc_id")]."'");
				list($internal_file_sql_row)=$internal_file_sql;
				$internal_file_no=$internal_file_no.$internal_file_sql_row[csf("internal_file_no")].",";
			}
			$lc_number=$lc_number.$row[csf("lc_number")].",";
			$lc_date=$lc_date.date('d.m.Y', strtotime($row[csf("lc_date")])).",";
			$lc_id=$lc_id.$row[csf("id")].",";
			$lc_value+=$row[csf("lc_value")];
			echo "document.getElementById('txt_lc_number').value 			= '".rtrim($lc_number,",")."';\n";
			echo "document.getElementById('btb_lc_id').value 			= '".rtrim($lc_id,",")."';\n";
			echo "document.getElementById('cbo_issuing_bank').value 			= '".$row[csf("issuing_bank_id")]."';\n";
			echo "document.getElementById('txt_lc_value').value 				= '".$lc_value."';\n";
			echo "document.getElementById('cbo_lc_currency_id').value 			= '".$row[csf("currency_id")]."';\n";
			//echo "document.getElementById('hid_tolarance').value 			= '".$row[csf("tolerance")]."';\n";
			//echo "document.getElementById('hid_tenor').value 			= '".$row[csf("tenor")]."';\n";
			//echo "document.getElementById('hid_maturity_from').value 			= '".$row[csf("maturity_from_id")]."';\n";
			echo "document.getElementById('cbo_importer_id').value 				= '".$row[csf("importer_id")]."';\n";
			echo "load_drop_down( 'requires/import_document_acceptance_non_lc_controller', '".$row[csf('importer_id')]."'+'_'+'".$row[csf('item_category_id')]."', 'load_supplier_dropdown', 'supplier_td');\n";
			echo "document.getElementById('cbo_supplier_id').value 			= '".$row[csf("supplier_id")]."';\n";
			echo "document.getElementById('cbo_supplier_id').disabled 			= true;\n";
			echo "document.getElementById('cbo_payterm_id').value 		= '".$row[csf("payterm_id")]."';\n";
			echo "document.getElementById('cbo_lc_type_id').value 			= '".$row[csf("lc_type_id")]."';\n";
			echo "document.getElementById('cbo_source_id').value 			= '".$row[csf("source")]."';\n";
			echo "$('#internal_file_no').val( '".rtrim($internal_file_no,",")."' );\n";
			//echo "$('#port_of_discharge').val( '$row[port_of_discharge]' );\n";   
			//echo "$('#port_of_loading').val( '$row[port_of_loading]' );\n";   
			//echo "$('#inco_term_place').val( '$row[inco_term_place]' );\n";   
			//echo "$('#cbo_inco_term').val( '$row[inco_term_id]' );\n";   
			//echo "$('#cbo_shipment_mode').val( '$row[delivery_mode_id]' );\n";
			echo "document.getElementById('hid_lc_date').value = '".rtrim($lc_date,",")."';\n";
			//exit();
		}
	}
}


if($action=='populate_data_from_invoice')
{
	$data_array=sql_select("select id,btb_lc_id,invoice_no,invoice_date,document_value,shipment_date,company_acc_date,bank_acc_date,bank_ref,acceptance_time,retire_source,remarks,bill_no,bill_date,shipment_mode,document_status,copy_doc_receive_date,original_doc_receive_date,doc_to_cnf,feeder_vessel,mother_vessel,eta_date,ic_receive_date,shipping_bill_no,inco_term,inco_term_place,port_of_loading,port_of_discharge,bill_of_entry_no,psi_reference_no,maturity_date,container_no,pkg_quantity, pkg_quantity_breakdown, pkg_uom,pkg_description,nagotiate_date, edf_tenor, edf_paid_date,bill_of_entry_date,exchange_rate, ready_to_approved, approved, is_posted_account,source from com_import_invoice_mst where id='$data'"); 
	foreach ($data_array as $row)
	{
		if($row[csf("is_posted_account")]==1)
		{
			echo "disable_enable_fields('txt_lc_number*txt_invoice_number*txt_invoice_date*txt_company_acc_date*txt_bank_acceptance_date*txt_document_value*txt_exchange_rate*cbo_retire_source',1,'','');\n";
		}
		else
		{
			echo "disable_enable_fields('txt_lc_number*txt_invoice_number*txt_invoice_date*txt_company_acc_date*txt_bank_acceptance_date*txt_document_value*txt_exchange_rate*cbo_retire_source',0,'','');\n";
		}
		 
		echo "document.getElementById('btb_lc_id').value 			= '".$row[csf("btb_lc_id")]."';\n";
		echo "document.getElementById('invoice_id').value 	= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_invoice_number').value 	= '".$row[csf("invoice_no")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value 	= '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('txt_invoice_date').value 	= '".change_date_format($row[csf("invoice_date")])."';\n";
		echo "document.getElementById('txt_document_value').value 	= '".$row[csf("document_value")]."';\n";
		echo "document.getElementById('txt_shipment_date').value 	= '".change_date_format($row[csf("shipment_date")])."';\n";
		echo "document.getElementById('txt_company_acc_date').value 	= '".change_date_format($row[csf("company_acc_date")])."';\n";
		echo "document.getElementById('txt_bank_acceptance_date').value 	= '".change_date_format($row[csf("bank_acc_date")])."';\n";
		echo "document.getElementById('txt_bank_ref').value 	= '".$row[csf("bank_ref")]."';\n";
		echo "document.getElementById('cbo_acceptance_time').value 	= '".$row[csf("acceptance_time")]."';\n";
		echo "document.getElementById('cbo_retire_source').value 	= '".$row[csf("retire_source")]."';\n";
		echo "document.getElementById('txt_remarks').value 	= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('pkg_description').value 	= '".$row[csf("pkg_description")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value 	= '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('hide_approved_status').value = '".$row[csf("approved")]."';\n";
		echo "document.getElementById('cbo_source_id').value 	= '".$row[csf("source")]."';\n";
		if($row[csf("approved")]==1)
		{
			echo "$('#approved').text('Approved');\n";
		}
		elseif($row[csf("approved")]==3)
		{
			echo "$('#approved').text('Partial Approved');\n";
		}
		else
		{
			echo "$('#approved').text('');\n";
		}
		
		if($row[csf("retire_source")]==30)
		{
			echo "$('#maturity_date').attr('disabled',true);\n";
			echo "$('#txt_edf_tenor').attr('disabled',false);\n";
		}
		else
		{
			echo "$('#maturity_date').attr('disabled',false);\n";
			echo "$('#txt_edf_tenor').attr('disabled',true);\n";
		}
		echo "document.getElementById('txt_edf_tenor').value 	= '".$row[csf("edf_tenor")]."';\n";
		
		echo "document.getElementById('bill_no').value 	= '".$row[csf("bill_no")]."';\n";
		echo "document.getElementById('bill_date').value 	= '".change_date_format($row[csf("bill_date")])."';\n";
		echo "document.getElementById('cbo_shipment_mode').value 	= '".$row[csf("shipment_mode")]."';\n";
		
		echo "document.getElementById('cbo_document_status').value 	= '".$row[csf("document_status")]."';\n";
		echo "document.getElementById('copy_doc_receive_date').value 	= '".change_date_format($row[csf("copy_doc_receive_date")])."';\n";
		echo "document.getElementById('original_doc_receive_date').value 	= '".change_date_format($row[csf("original_doc_receive_date")])."';\n";
		echo "document.getElementById('edf_paid_date').value 	= '".($row[csf('edf_paid_date')] == '0000-00-00' || $row[csf('edf_paid_date')] == '' ? '' : change_date_format($row[csf('edf_paid_date')]))."';\n";
		echo "document.getElementById('bill_of_entry_date').value 	= '".($row[csf('bill_of_entry_date')] == '0000-00-00' || $row[csf('bill_of_entry_date')] == '' ? '' : change_date_format($row[csf('bill_of_entry_date')]))."';\n";
		
		echo "document.getElementById('doc_to_cnf').value 	= '".change_date_format($row[csf("doc_to_cnf")])."';\n";
		echo "document.getElementById('feeder_vessel').value 	= '".$row[csf("feeder_vessel")]."';\n";
		echo "document.getElementById('mother_vessel').value 	= '".$row[csf("mother_vessel")]."';\n";
		
		echo "document.getElementById('eta_date').value 	= '".change_date_format($row[csf("eta_date")])."';\n";
		echo "document.getElementById('ic_receive_date').value 	= '".change_date_format($row[csf("ic_receive_date")])."';\n";
		echo "document.getElementById('shipping_bill_no').value 	= '".$row[csf("shipping_bill_no")]."';\n";
		
		
		echo "document.getElementById('cbo_inco_term').value 	= '".$row[csf("inco_term")]."';\n";
		echo "document.getElementById('inco_term_place').value 	= '".$row[csf("inco_term_place")]."';\n";
		echo "document.getElementById('port_of_loading').value 	= '".$row[csf("port_of_loading")]."';\n";
		
		echo "document.getElementById('port_of_discharge').value 	= '".$row[csf("port_of_discharge")]."';\n";
		echo "document.getElementById('bill_of_entry_no').value 	= '".$row[csf("bill_of_entry_no")]."';\n";
		
		
		echo "document.getElementById('psi_reference_no').value 	= '".$row[csf("psi_reference_no")]."';\n";
		echo "document.getElementById('maturity_date').value 	= '".change_date_format($row[csf("maturity_date")])."';\n";
		
		
		echo "document.getElementById('container_no').value 	= '".$row[csf("container_no")]."';\n";
		echo "document.getElementById('pkg_quantity').value 	= '".$row[csf("pkg_quantity")]."';\n";
		echo "document.getElementById('pkg_quantity_breakdown').value 	= '".$row[csf("pkg_quantity_breakdown")]."';\n";
		echo "document.getElementById('pakg_uom').value 			= '".$row[csf("pkg_uom")]."';\n";
		exit();
	}
}


/*if( $action == "pi_listview" ) 
{
    $data = explode('_',$data);
	$sql="select b.id as pi_id, b.pi_number, b.item_category_id, a.com_btb_lc_master_details_id, c.net_pi_amount AS pi_value, c.work_order_no, c.work_order_id, b.goods_rcv_status, b.ref_closing_status, b.net_total_amount
	from com_btb_lc_pi a, com_pi_master_details b, com_pi_item_details c
	where b.id=a.pi_id and b.id=c.pi_id and a.com_btb_lc_master_details_id in( $data[0]) and a.is_deleted=0 and a.status_active=1 and c.is_deleted=0 and c.status_active=1";
	//echo $sql;
	$data_array=sql_select($sql);
	$pi_list_data=array(); $all_pi_ids=$all_wo_ids="";
	foreach($data_array as $row)
	{
		if($pi_id_check[$row[csf("pi_id")]]=="")
		{
			$pi_id_check[$row[csf("pi_id")]]=$row[csf("pi_id")];
			$all_pi_id.=$row[csf("pi_id")].",";
			if($row[csf("goods_rcv_status")]==2) $all_pi_ids.=$row[csf("pi_id")].",";
		}
		
		if($wo_check[$row[csf("work_order_id")]]=="")
		{
			$wo_check[$row[csf("work_order_id")]]=$row[csf("work_order_id")];
			$pi_list_data[$row[csf("pi_id")]]["work_order_id"].=$row[csf("work_order_id")].",";
			//$all_wo.="'".$row[csf("work_order_no")]."',";
			$all_wo_id.=$row[csf("work_order_id")].",";
			if($row[csf("goods_rcv_status")]==1) $all_wo_ids.=$row[csf("work_order_id")].",";
		}
		
		$pi_list_data[$row[csf("pi_id")]]["com_btb_lc_master_details_id"]=$row[csf("com_btb_lc_master_details_id")];
		$pi_list_data[$row[csf("pi_id")]]["pi_id"]=$row[csf("pi_id")];
		$pi_list_data[$row[csf("pi_id")]]["pi_number"]=$row[csf("pi_number")];
		$pi_list_data[$row[csf("pi_id")]]["item_category_id"]=$row[csf("item_category_id")];
		$pi_list_data[$row[csf("pi_id")]]["ref_closing_status"]=$row[csf("ref_closing_status")];
		$pi_list_data[$row[csf("pi_id")]]["net_pi_value"]=$row[csf("net_total_amount")];
		$pi_list_data[$row[csf("pi_id")]]["pi_value"]+=$row[csf("pi_value")];
		if($row[csf("item_category_id")]==11)
		{
			$all_item_cate_arr[$row[csf("item_category_id")]]=$row[csf("item_category_id")];
			$all_item_cate_arr[4]=4;
		}
		else
		{
			$all_item_cate_arr[$row[csf("item_category_id")]]=$row[csf("item_category_id")];
		}
		
	}
	
	
	$all_wo_id=chop($all_wo_id,",");
	$all_pi_id=chop($all_pi_id,",");
	//echo $data[1];
	if($all_pi_id!="")
	{
		//echo "select pi_id, import_invoice_id, current_acceptance_value, id from com_import_invoice_dtls where status_active=1 and is_deleted=0 and pi_id in($all_pi_id)";die;
		$accep_sql=sql_select("select pi_id, import_invoice_id, current_acceptance_value, id from com_import_invoice_dtls where status_active=1 and is_deleted=0 and pi_id in($all_pi_id)");
		foreach($accep_sql as $row)
		{
			$cumulative_array[$row[csf("pi_id")]]+=$row[csf("current_acceptance_value")];
			if($data[1] == 2)
			{
				$current_acceptance_data[$row[csf("pi_id")]][$row[csf("import_invoice_id")]]["current_acceptance_value"]+=$row[csf("current_acceptance_value")];
				$current_acceptance_data[$row[csf("pi_id")]][$row[csf("import_invoice_id")]]["import_invoice_id"]=$row[csf("id")];
			}
		}
		//echo "<pre>";print($current_acceptance_data);
		$all_pi_ids=chop($all_pi_ids,",");
		$all_wo_ids=chop($all_wo_ids,",");
		
		if($all_pi_ids !="" && $all_wo_ids !="")
		{
			$rcv_pi_sql="select a.id as mst_id, a.booking_id, a.receive_basis, a.exchange_rate, sum(b.order_amount) as order_amount 
			from inv_receive_master a, inv_transaction b 
			where a.id=b.mst_id and a.booking_id in($all_pi_ids) and a.receive_basis=1 and b.receive_basis =1 and b.transaction_type=1 and b.payment_over_recv=0 and b.status_active=1 and b.is_deleted=0 and b.item_category in(".implode(",",$all_item_cate_arr).")
			group by a.id, a.booking_id, a.receive_basis, a.exchange_rate
			union all
			select a.id as mst_id, a.booking_id, a.receive_basis, a.exchange_rate, sum(b.order_amount) as order_amount 
			from inv_receive_master a, inv_transaction b 
			where a.id=b.mst_id and a.booking_id in($all_wo_ids) and a.receive_basis=2 and b.receive_basis =2 and b.transaction_type=1 and b.payment_over_recv=0 and b.status_active=1 and b.is_deleted=0 and b.item_category in(".implode(",",$all_item_cate_arr).")
			group by a.id, a.booking_id, a.receive_basis, a.exchange_rate";
			
			
		}
		else
		{
			if($all_pi_ids !="")
			{
				$rcv_pi_sql="select a.id as mst_id, a.booking_id, a.receive_basis, a.exchange_rate, sum(b.order_amount) as order_amount
				from inv_receive_master a, inv_transaction b 
				where a.id=b.mst_id and a.booking_id in($all_pi_ids) and a.receive_basis=1 and b.receive_basis =1 and b.transaction_type=1 and b.payment_over_recv=0 and b.status_active=1 and b.is_deleted=0 and b.item_category in(".implode(",",$all_item_cate_arr).")
				group by a.id, a.booking_id, a.receive_basis, a.exchange_rate";
			}
			else if($all_wo_ids !="")
			{
				$rcv_pi_sql="select a.id as mst_id, a.booking_id, a.receive_basis, a.exchange_rate, sum(b.order_amount) as order_amount 
				from inv_receive_master a, inv_transaction b 
				where a.id=b.mst_id and a.booking_id in($all_wo_ids) and a.receive_basis=2 and b.receive_basis =2 and b.transaction_type=1 and b.payment_over_recv=0 and b.status_active=1 and b.is_deleted=0 and b.item_category in(".implode(",",$all_item_cate_arr).")
				group by a.id, a.booking_id, a.receive_basis, a.exchange_rate";
			}
		}
		//echo $rcv_pi_sql;
		$rcv_pi_sql_result=sql_select($rcv_pi_sql);$all_receive_id="";
		foreach($rcv_pi_sql_result as $row)
		{
			$all_receive_id.=$row[csf("mst_id")].",";
			$rcv_exchange_rate[$row[csf("mst_id")]]=$row[csf("exchange_rate")];
		}
		$all_receive_id=chop($all_receive_id,",");
		if($all_receive_id!="")
		{
			$return_sql="select a.received_id, b.cons_amount from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.received_id in($all_receive_id) and b.transaction_type=3 and b.status_active=1 and b.is_deleted=0";
			$return_sql_result=sql_select($return_sql);$rcv_wise_rtn_value=array();
			foreach($return_sql_result as $row)
			{
				$all_rtn_value+=$row[csf("cons_amount")]/$rcv_exchange_rate[$row[csf("received_id")]];
				$rcv_wise_rtn_value[$row[csf("received_id")]]+=$row[csf("cons_amount")]/$rcv_exchange_rate[$row[csf("received_id")]];
			}
		}
		$cumulative_receive_data=array();
		foreach($rcv_pi_sql_result as $row)
		{
			$cumulative_receive_data[$row[csf("receive_basis")]][$row[csf("booking_id")]]+=$row[csf("order_amount")]-$rcv_wise_rtn_value[$row[csf("mst_id")]];
			if($mst_id_check[$row[csf("mst_id")]]=="" && $row[csf("mst_id")]>0)
			{
				$mst_id_check[$row[csf("mst_id")]]=$row[csf("mst_id")];
				$cumulative_receive_ids[$row[csf("receive_basis")]][$row[csf("booking_id")]].=$row[csf("mst_id")].",";
			}
		}
		
	}
	
	//echo "333 $all_pi_id 22<pre>";print_r($cumulative_accept_amt_arr);die;
	
	?>
	
	<fieldset style="width:1024px;">
		<legend>BTB / Import LC PI List</legend>
        <div style="width:1000px;" align="left">  
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="rpt_table">
			<thead>
                <th width="140">LC Number</th>
				<th width="140" class="must_entry_caption">PI Number</th>
				<th width="140">Item Category</th>
				<th width="100">PI Value</th>
				<th width="100">Current Acceptance Value  </th>
				<th width="100">MRR Value</th>
				<th width="120">Cumulative Accepted Amount</th>
                <th width="">Balance</th>
			</thead>
		</table>
        </div>
		<div style="width:1000px;">
			<table width="100%" border="0" id="tbl_list_search" cellpadding="0" cellspacing="0" class="rpt_table">
			<?
            $tot_current_acceptance_value = 0;
			$tot_cumu_acceptance_value = 0;
			if($data[3]==1) $is_disable=" disabled"; else $is_disable="";
			
			$i = 0;            
			foreach ($pi_list_data as $row)
	        {
				$i++;
               	$cumulative_accept_amount=0;
				$cumulative_accept_amount=$cumulative_array[$row['pi_id']];
				$cumulative_receive_value="";
				$cumulative_receive_value =$cumulative_receive_data[1][$row['pi_id']];
				$all_receive_ids="";
				$all_receive_ids=chop($cumulative_receive_ids[1][$row['pi_id']],",");
				$receive_basis=1;
				//echo $cumulative_receive_data[1][$row['pi_id']]."==tt".$row['work_order_id'];
				if($cumulative_receive_data[1][$row['pi_id']]=="")
				{
					$wo_id_arr=explode(",",chop($row['work_order_id'],","));
					foreach($wo_id_arr as $wo_id)
					{
						$cumulative_receive_value +=$cumulative_receive_data[2][$wo_id];
						if($cumulative_receive_data[2][$wo_id]) $receive_basis=2;
						$all_receive_ids.=chop($cumulative_receive_ids[2][$wo_id],",").",";
					}
				}
				
				
				if($data[1] == 2)
				{
					$current_acceptance_value=$current_acceptance_data[$row['pi_id']][$data[2]]["current_acceptance_value"];
					$tot_current_acceptance_value += $current_acceptance_value;
					$invoice_dtls_id=$current_acceptance_data[$row['pi_id']][$data[2]]["import_invoice_id"];
				}
				if($row['com_btb_lc_master_details_id']>0)
				{
					$lc_number = return_field_value("lc_number","com_btb_lc_master_details","id='".$row['com_btb_lc_master_details_id']."'");
					$tolerance = return_field_value("tolerance","com_btb_lc_master_details","id='".$row['com_btb_lc_master_details_id']."'");
				}
				$tot_cumu_acceptance_value = $tot_cumu_acceptance_value+$cumulative_accept_amount;
				$balance=$row["net_pi_value"] - $cumulative_accept_amount;
				$tot_cumu_avalance_value+=$balance;
				?>
				<tr>
                    <td width="140">
						<input type="text" name="lc_number[]" id="lc_number_<? echo $i; ?>" class="text_boxes" value="<? echo $lc_number; ?>" style="width:125px;" readonly />
						<input type="hidden" name="lc_id[]" id="lc_id_<? echo $i; ?>" value="<? echo $row['com_btb_lc_master_details_id']; ?>" />
                        <input type="hidden" name="tolerance[]" id="tolerance_<? echo $i; ?>" value="<? echo $tolerance; ?>" />
                        
					</td>
					<td width="140">
						<input type="text" name="pi_number[]" id="pi_number_<? echo $i; ?>" class="text_boxes" value="<? echo $row['pi_number']; ?>" style="width:125px;" readonly />
						<input type="hidden" name="pi_id[]" id="pi_id_<? echo $i; ?>" value="<? echo $row['pi_id']; ?>" />
                        
					</td>
					<td width="140">
						<input type="text" name="item_category[]" id="item_category_<? echo $i; ?>" class="text_boxes" value="<? echo $item_category[$row['item_category_id']]; ?>" style="width:125px;" readonly />
						<input type="hidden" name="item_category_id[]" id="item_category_id_<? echo $i; ?>" value="<? echo $row['item_category_id']; ?>" />
					</td>
					<td width="100">
                    	<input type="text" name="pi_value[]" id="pi_value_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row['net_pi_value']; ?>" style="width:85px;" readonly>
                    </td>
					<td width="100">
                    	<input type="text" name="current_acceptance_value[]" id="current_acceptance_value_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $current_acceptance_value;?>" onKeyUp="calculate(<? echo $i; ?>)"   style="width:85px;" <?= $is_disable; ?>  />
                        <input type="hidden" id="hide_current_acceptance_value_<? echo $i; ?>" value="<? echo $current_acceptance_value;?>" />
                    </td>
					<td width="100">
                    	<input type="text" name="cumulative_receive_value[]" id="cumulative_receive_value_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo  $cumulative_receive_value;?>" onClick="open_mrr_details('<? echo $receive_basis;?>','<? echo chop($all_receive_ids,",");?>','<? echo $row['pi_id'];?>','<? echo $invoice_dtls_id;?>')" style="width:85px;" readonly />
                    </td>
					<td width="120">
                    	<input type="text" name="cumulative_accept_amount[]" id="cumulative_accept_amount_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $cumulative_array[$row["pi_id"]];?>" style="width:105px;" readonly onClick="show_me_cumu_stat(<? echo $row["pi_id"]; ?>)" />
                        <input type="hidden" id="hide_cumulative_accept_amount_<? echo $i; ?>" value="<?  echo $cumulative_array[$row["pi_id"]];?>" />
                    </td>
                    <td width="">
                    	<input type="text" style="width:130px" name="cumulative_accept_balance[]" id="cumulative_accept_balance_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $balance;?>"  readonly />
                       <input type="hidden" id="invoice_dtls_id_<? echo $i; ?>" value="<?  echo $invoice_dtls_id;?>" />

                    </td>
				</tr>
			<? } ?>
                <tfoot>
                    <th colspan='4' style="text-align:right"><b>Total:</b></th>
                    <th style="text-align:right"><input type="text" id="tot_current_acceptance_value" class="text_boxes_numeric" style="width:85px;"  disabled value="<? echo $tot_current_acceptance_value; ?>" /></th>
                    <th ></th>
                    <th ><input type="text" id="tot_cumula_acceptance_value" class="text_boxes_numeric" style="width:105px;"  disabled value="<? echo $tot_cumu_acceptance_value; ?>" /></th>
                    <th ><input type="text" id="tot_cumula_balance_value" style="width:130px" class="text_boxes_numeric"   disabled value="<? echo $tot_cumu_avalance_value; ?>" /></th>
               </tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}*/

if( $action == "pi_listview" ) 
{
    $data = explode('_',$data);
	$cbo_importer_id=$data[4];
	$goods_rcv_variable=return_field_value("export_invoice_qty_source as source","variable_settings_commercial","company_name=$cbo_importer_id and variable_list=23 and status_active=1","source");

	$sql="select b.id as pi_id, b.pi_number, c.item_category_id, c.net_pi_amount AS pi_value, c.work_order_no, c.work_order_id, b.goods_rcv_status, b.ref_closing_status, c.net_pi_amount as net_total_amount, a.com_btb_lc_master_details_id
	from com_btb_lc_pi a, com_pi_master_details b, com_pi_item_details c
	where b.id=a.pi_id and b.id=c.pi_id and a.com_btb_lc_master_details_id in( $data[0]) and a.is_deleted=0 and a.status_active=1 and c.is_deleted=0 and c.status_active=1";
	//echo $sql;
	$data_array=sql_select($sql);
	$pi_list_data=array(); $all_pi_ids=$all_wo_ids=$all_wo_no="";$service_category=array();
	foreach($data_array as $row)
	{
		if($pi_id_check[$row[csf("pi_id")]]=="")
		{
			$pi_id_check[$row[csf("pi_id")]]=$row[csf("pi_id")];
			$all_pi_id.=$row[csf("pi_id")].",";
			if($row[csf("goods_rcv_status")]==2) $all_pi_ids.=$row[csf("pi_id")].",";
		}
		
		if($wo_check[$row[csf("work_order_id")]]=="")
		{
			$wo_check[$row[csf("work_order_id")]]=$row[csf("work_order_id")];
			$all_wo_id.=$row[csf("work_order_id")].",";
			if($row[csf("goods_rcv_status")]==1) 
			{
				$all_wo_ids.=$row[csf("work_order_id")].",";
				$all_wo_no.="'".$row[csf("work_order_no")]."',";
			}
		}
		
		if($wo_check2[$row[csf("pi_id")]][$row[csf("work_order_id")]]=="")
		{
			$wo_check2[$row[csf("pi_id")]][$row[csf("work_order_id")]]=$row[csf("work_order_id")];
			$pi_list_data[$row[csf("pi_id")]][$row[csf("item_category_id")]]["work_order_id"].=$row[csf("work_order_id")].",";
		}

		$pi_list_data[$row[csf("pi_id")]][$row[csf("item_category_id")]]["btb_id"]=$row[csf("com_btb_lc_master_details_id")];
		$pi_list_data[$row[csf("pi_id")]][$row[csf("item_category_id")]]["pi_id"]=$row[csf("pi_id")];
		$pi_list_data[$row[csf("pi_id")]][$row[csf("item_category_id")]]["pi_number"]=$row[csf("pi_number")];
		$pi_list_data[$row[csf("pi_id")]][$row[csf("item_category_id")]]["item_category_id"]=$row[csf("item_category_id")];
		$pi_list_data[$row[csf("pi_id")]][$row[csf("item_category_id")]]["ref_closing_status"]=$row[csf("ref_closing_status")];
		$pi_list_data[$row[csf("pi_id")]][$row[csf("item_category_id")]]["net_total_amount"]+=$row[csf("net_total_amount")];
		$pi_list_data[$row[csf("pi_id")]][$row[csf("item_category_id")]]["pi_value"]+=$row[csf("pi_value")];
		// || $row[csf("item_category_id")]==24 
		if($row[csf("item_category_id")]==12 || $row[csf("item_category_id")]==25 || $row[csf("item_category_id")]==31 || $row[csf("item_category_id")]==74 || $row[csf("item_category_id")]==102 || $row[csf("item_category_id")]==103 || $row[csf("item_category_id")]==104)
		{
			$service_category[$row[csf("item_category_id")]]=$row[csf("item_category_id")];
		}
		elseif($row[csf("item_category_id")]==11)
		{
			$all_item_cate_arr[$row[csf("item_category_id")]]=$row[csf("item_category_id")].",4";
		}
		else
		{
			if($row[csf("item_category_id")]==24) $row[csf("item_category_id")]=1;
			$all_item_cate_arr[$row[csf("item_category_id")]]=$row[csf("item_category_id")];
		}
	}
	
	//echo "<pre>";print_r($pi_list_data);die;

	$all_wo_id=chop($all_wo_id,",");
	$all_pi_id=chop($all_pi_id,",");
	
	//$all_wo_no
	$is_service_category=0;
	if(count($service_category)>0)
	{
		$is_service_category=1;
		$mrr_sql="select a.ID AS MST_ID, a.WO_BOOKING_ID, a.WO_BOOKING_NO, b.ACKN_QTY, b.AMOUNT from WO_SERVICE_ACKNOWLEDGEMENT_MST a, WO_SERVICE_ACKNOWLEDGEMENT_DTLS b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and b.ACKN_QTY>0 and a.WO_BOOKING_ID in($all_wo_id)";
		//echo $mrr_sql;die;
		$mrr_sql_result=sql_select($mrr_sql);
		foreach($mrr_sql_result as $val)
		{
			$cumulative_receive_data[2][$val["WO_BOOKING_ID"]]+=$val["AMOUNT"];
			if($mst_id_check[$val["MST_ID"]]=="")
			{
				$mst_id_check[$val["MST_ID"]]=$val["MST_ID"];
				$cumulative_receive_ids[2][$val["WO_BOOKING_ID"]].=$val["MST_ID"].",";
			}
		}
		if($all_pi_id!="")
		{
			$accep_sql=sql_select("select pi_id, import_invoice_id, current_acceptance_value, id, item_category_id from com_import_invoice_dtls where status_active=1 and is_deleted=0 and pi_id in($all_pi_id)");
			foreach($accep_sql as $row)
			{
				$cumulative_array[$row[csf("pi_id")]][$row[csf("item_category_id")]]+=$row[csf("current_acceptance_value")];
				if($data[1] == 2)
				{
					$current_acceptance_data[$row[csf("pi_id")]][$row[csf("import_invoice_id")]][$row[csf("item_category_id")]]["current_acceptance_value"]+=$row[csf("current_acceptance_value")];
					$current_acceptance_data[$row[csf("pi_id")]][$row[csf("import_invoice_id")]][$row[csf("item_category_id")]]["import_invoice_id"]=$row[csf("id")];
				}
			}
		}
		
	}
	else
	{
		if($all_pi_id!="")
		{
			$accep_sql=sql_select("select pi_id, import_invoice_id, current_acceptance_value, id, item_category_id from com_import_invoice_dtls where status_active=1 and is_deleted=0 and pi_id in($all_pi_id)");
			foreach($accep_sql as $row)
			{
				$cumulative_array[$row[csf("pi_id")]][$row[csf("item_category_id")]]+=$row[csf("current_acceptance_value")];
				if($data[1] == 2)
				{
					$current_acceptance_data[$row[csf("pi_id")]][$row[csf("import_invoice_id")]][$row[csf("item_category_id")]]["current_acceptance_value"]+=$row[csf("current_acceptance_value")];
					$current_acceptance_data[$row[csf("pi_id")]][$row[csf("import_invoice_id")]][$row[csf("item_category_id")]]["import_invoice_id"]=$row[csf("id")];
				}
			}
			
			$all_pi_ids=chop($all_pi_ids,",");
			$all_wo_ids=chop($all_wo_ids,",");
			$all_wo_no=chop($all_wo_no,",");
			
			if($all_pi_ids !="" && $all_wo_ids !="")
			{
				$rcv_pi_sql="select a.id as mst_id, b.pi_wo_batch_no as booking_id, a.receive_basis, a.exchange_rate, b.item_category, sum(b.ORDER_QNTY*b.ORDER_RATE) as order_amount 
				from inv_receive_master a, inv_transaction b 
				where a.id=b.mst_id and b.pi_wo_batch_no in($all_pi_ids) and a.receive_basis=1 and b.receive_basis =1 and b.transaction_type=1 and b.payment_over_recv=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.item_category in(".implode(",",$all_item_cate_arr).")
				group by a.id, b.pi_wo_batch_no, a.receive_basis, a.exchange_rate, b.item_category
				union all
				select a.id as mst_id, b.pi_wo_batch_no as booking_id, a.receive_basis, a.exchange_rate, b.item_category, sum(b.ORDER_QNTY*b.ORDER_RATE) as order_amount 
				from inv_receive_master a, inv_transaction b 
				where a.id=b.mst_id and b.pi_wo_batch_no in($all_wo_ids) and a.BOOKING_NO in($all_wo_no) and a.receive_basis=2 and b.receive_basis =2 and b.transaction_type=1 and b.payment_over_recv=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.item_category in(".implode(",",$all_item_cate_arr).")
				group by a.id, b.pi_wo_batch_no, a.receive_basis, a.exchange_rate, b.item_category";
				
				
			}
			else
			{
				if($all_pi_ids !="")
				{
					if(in_array("2",$all_item_cate_arr))
					{
						$rcv_pi_sql="select a.id as mst_id, a.BOOKING_ID as booking_id, a.receive_basis, a.exchange_rate, b.item_category, sum(b.ORDER_QNTY*b.ORDER_RATE) as order_amount
						from inv_receive_master a, inv_transaction b 
						where a.id=b.mst_id and a.BOOKING_ID in($all_pi_ids) and a.receive_basis=1 and b.receive_basis =1 and b.transaction_type=1 and b.payment_over_recv=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.item_category in(".implode(",",$all_item_cate_arr).")
						group by a.id, a.BOOKING_ID, a.receive_basis, a.exchange_rate, b.item_category";
					}
					else
					{
						$rcv_pi_sql="select a.id as mst_id, b.pi_wo_batch_no as booking_id, a.receive_basis, a.exchange_rate, b.item_category, sum(b.ORDER_QNTY*b.ORDER_RATE) as order_amount
						from inv_receive_master a, inv_transaction b 
						where a.id=b.mst_id and b.pi_wo_batch_no in($all_pi_ids) and a.receive_basis=1 and b.receive_basis =1 and b.transaction_type=1 and b.payment_over_recv=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.item_category in(".implode(",",$all_item_cate_arr).")
						group by a.id, b.pi_wo_batch_no, a.receive_basis, a.exchange_rate, b.item_category";
					}
				}
				else if($all_wo_ids !="")
				{
					$rcv_pi_sql="select a.id as mst_id, b.pi_wo_batch_no as booking_id, a.receive_basis, a.exchange_rate, b.item_category, sum(b.ORDER_QNTY*b.ORDER_RATE) as order_amount 
					from inv_receive_master a, inv_transaction b 
					where a.id=b.mst_id and b.pi_wo_batch_no in($all_wo_ids) and a.BOOKING_NO in($all_wo_no) and a.receive_basis=2 and b.receive_basis =2 and b.transaction_type=1 and b.payment_over_recv=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.item_category in(".implode(",",$all_item_cate_arr).")
					group by a.id, b.pi_wo_batch_no, a.receive_basis, a.exchange_rate, b.item_category";
				}
			}
			//echo $rcv_pi_sql;
			$rcv_pi_sql_result=sql_select($rcv_pi_sql);$all_receive_id="";
			foreach($rcv_pi_sql_result as $row)
			{
				$all_receive_id.=$row[csf("mst_id")].",";
				$rcv_exchange_rate[$row[csf("mst_id")]]=$row[csf("exchange_rate")];
			}
			$all_receive_id=chop($all_receive_id,",");
			if($all_receive_id!="")
			{
				$return_sql="select a.received_id, b.cons_amount, b.item_category from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.received_id in($all_receive_id) and b.transaction_type=3 and b.status_active=1 and b.is_deleted=0";
				$return_sql_result=sql_select($return_sql);$rcv_wise_rtn_value=array();
				foreach($return_sql_result as $row)
				{
					$all_rtn_value+=$row[csf("cons_amount")]/$rcv_exchange_rate[$row[csf("received_id")]];
					$rcv_wise_rtn_value[$row[csf("received_id")]][$row[csf("item_category")]]+=$row[csf("cons_amount")]/$rcv_exchange_rate[$row[csf("received_id")]];
				}
			}
			$cumulative_receive_data=array();
			foreach($rcv_pi_sql_result as $row)
			{
				$cumulative_receive_data[$row[csf("receive_basis")]][$row[csf("booking_id")]][$row[csf("item_category")]]+=$row[csf("order_amount")]-$rcv_wise_rtn_value[$row[csf("mst_id")]][$row[csf("item_category")]];
				if($mst_id_check[$row[csf("mst_id")]][$row[csf("booking_id")]][$row[csf("item_category")]]=="" && $row[csf("mst_id")]>0)
				{
					$mst_id_check[$row[csf("mst_id")]][$row[csf("booking_id")]][$row[csf("item_category")]]=$row[csf("mst_id")];
					$cumulative_receive_ids[$row[csf("receive_basis")]][$row[csf("booking_id")]][$row[csf("item_category")]].=$row[csf("mst_id")].",";
				}
			}
			
		}
	}
	
	//echo $sql;
	?>
	<fieldset style="width:1024px;">
		<legend>BTB / Import LC PI List</legend>
        <div style="width:1000px;" align="left">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="rpt_table">
			<thead>
				<th width="140" class="must_entry_caption">PI Number</th>
				<th width="140">Item Category</th>
				<th width="140">PI Value</th>
				<th width="140">Current Acceptance Value  </th>
				<th width="140">Net MRR Value</th>
				<th width="140">Cumulative Accepted Amount</th>
                <th width="">Balance</th>
			</thead>
		</table>
        </div>
		<div style="width:1000px;">
			<table width="100%" border="0" id="tbl_list_search" cellpadding="0" cellspacing="0" class="rpt_table">
			<?
            $tot_current_acceptance_value = 0;
			$tot_cumu_acceptance_value = 0;
			if($data[3]==1) $readOnly="readonly"; else $readOnly="";
			if($data[6]==1) $is_disable=" disabled"; else $is_disable="";
			$i = 0;
			foreach ($pi_list_data as $pi_id=>$pi_val)
	        {
				foreach ($pi_val as $item_cat_id=>$row)
	        	{
					$i++;
					$cumulative_accept_amount=0;
					$cumulative_accept_amount=$cumulative_array[$row['pi_id']][$item_cat_id];
					$cumulative_receive_value="";
					$cumulative_receive_value =$cumulative_receive_data[1][$row['pi_id']][$item_cat_id];
					$all_receive_ids="";
					$all_receive_ids=chop($cumulative_receive_ids[1][$row['pi_id']][$item_cat_id],",");
					$receive_basis=1;
					if($cumulative_receive_data[1][$row['pi_id']][$item_cat_id]=="")
					{
						$wo_id_arr=explode(",",chop($row['work_order_id'],","));
						foreach($wo_id_arr as $wo_id)
						{
							if(chop($cumulative_receive_ids[2][$wo_id][$item_cat_id],",")!="")
							{
								$cumulative_receive_value +=$cumulative_receive_data[2][$wo_id][$item_cat_id];
								if($cumulative_receive_data[2][$wo_id][$item_cat_id]) $receive_basis=2;
								$all_receive_ids.=chop($cumulative_receive_ids[2][$wo_id][$item_cat_id],",").",";
							}
						}
					}
					
	
					if($data[1] == 2)
					{
						$current_acceptance_value=$current_acceptance_data[$row['pi_id']][$data[2]][$item_cat_id]["current_acceptance_value"];
						$tot_current_acceptance_value += $current_acceptance_value;
						$invoice_dtls_id=$current_acceptance_data[$row['pi_id']][$data[2]][$item_cat_id]["import_invoice_id"];
					}
	
					$tot_cumu_acceptance_value = $tot_cumu_acceptance_value+$cumulative_accept_amount;
					$balance=$row["pi_value"] - $cumulative_accept_amount;
					$tot_cumu_avalance_value+=$balance;
					$ref_closing_status=$row["ref_closing_status"];
					
					?>
					<tr>
						<td width="140">
							<input type="text" name="pi_number[]" id="pi_number_<? echo $i; ?>" class="text_boxes" value="<? echo $row['pi_number']; ?>" style="width:125px;" readonly onClick="fn_pi_print('<? echo $row['pi_id']; ?>','<? echo $row['item_category_id']; ?>')" placeholder="Click for PI print" /> 
							<input type="hidden" name="pi_id[]" id="pi_id_<? echo $i; ?>" value="<? echo $row['pi_id']; ?>" />
                            <input type="hidden" name="lc_id[]" id="lc_id_<? echo $i; ?>" value="<? echo $row['btb']; ?>" />
						</td>
						<td width="140">
							<input type="text" name="item_category[]" id="item_category_<? echo $i; ?>" class="text_boxes" value="<? echo $item_category[$row['item_category_id']]; ?>" style="width:125px;" readonly />
							<input type="hidden" name="item_category_id[]" id="item_category_id_<? echo $i; ?>" value="<? echo $row['item_category_id']; ?>" />
						</td>
						<td width="140" title="<?= $row['pi_value'];?>">
							<input type="text" name="pi_value[]" id="pi_value_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo number_format($row['net_total_amount'],4,".",""); ?>" style="width:125px;" readonly>
						</td>
						<td width="140">
							<input type="text" name="current_acceptance_value[]" id="current_acceptance_value_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo number_format($current_acceptance_value,4,".","");?>" onKeyUp="calculate(<? echo $i; ?>)" style="width:125px;"  <?php echo $readOnly." ".$is_disable; ?>/>
							<input type="hidden" title="<? echo $ref_closing_status;?>" id="hide_current_acceptance_value_<? echo $i; ?>" value="<? echo $current_acceptance_value;?>"  />
						</td>
						<td width="140" title="<? echo "Receive Value-Receive Return Value"; ?>">
							<input type="text" name="cumulative_receive_value[]" id="cumulative_receive_value_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo number_format($cumulative_receive_value,4,".","");?>" onClick="open_mrr_details('<? echo $receive_basis;?>','<? echo chop($all_receive_ids,",");?>','<? echo $row['pi_id'];?>','<? echo $invoice_dtls_id;?>','<? echo $is_service_category;?>','<? echo $row['item_category_id']; ?>')" style="width:125px;" readonly />
						</td>
						<td width="140">
							<input type="text" name="cumulative_accept_amount[]" id="cumulative_accept_amount_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo number_format($cumulative_accept_amount,4,".","");?>" style="width:125px;" readonly onClick="show_me_cumu_stat(<? echo $row['pi_id']; ?>)" />
							<input type="hidden" id="hide_cumulative_accept_amount_<? echo $i; ?>" value="<?  echo $cumulative_accept_amount;?>" />
						</td>
						<td width="">
							<input type="text" name="cumulative_accept_balance[]" id="cumulative_accept_balance_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo number_format($balance,4,".",""); ?>" style="width:128px;" readonly />
						<input type="hidden" id="invoice_dtls_id_<? echo $i; ?>" value="<?  echo $invoice_dtls_id;?>" />
						</td>
					</tr>
					<?
				}
			} 
			?>
			<tfoot>
				<th colspan='3' style="text-align:right"><b>Total:</b></th>
				<th style="text-align:right"><input type="text" id="tot_current_acceptance_value" class="text_boxes_numeric" style="width:125px;"  disabled value="<? echo number_format($tot_current_acceptance_value,2,".",""); ?>" /></th>
				<th ></th>
				<th ><input type="text" id="tot_cumula_acceptance_value" class="text_boxes_numeric" style="width:125px;"  disabled value="<? echo number_format($tot_cumu_acceptance_value,2,".","");  ?>" /></th>
				<th ><input type="text" id="tot_cumula_balance_value" class="text_boxes_numeric" style="width:125px;"  disabled value="<? echo number_format($tot_cumu_avalance_value,2,".","");  ?>" /></th>
		   </tfoot>
		</table>
	</div>
	</fieldset>
	<?
	exit();
}


if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//echo $tot_current_acceptance_value;die;
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con);die;}	
		$id=return_next_id( "id", "com_import_invoice_mst", 1 ) ;
		$field_array="id,btb_lc_id,is_lc,invoice_no,invoice_date,document_value,shipment_date,company_acc_date,bank_acc_date,bank_ref,acceptance_time,retire_source,remarks,edf_tenor,bill_no,bill_date,shipment_mode,document_status,copy_doc_receive_date,original_doc_receive_date,doc_to_cnf,feeder_vessel,mother_vessel,eta_date,ic_receive_date,shipping_bill_no,inco_term,inco_term_place,port_of_loading,port_of_discharge,bill_of_entry_no,psi_reference_no,maturity_date,edf_paid_date,bill_of_entry_date,exchange_rate,container_no,pkg_quantity,pkg_quantity_breakdown,pkg_uom,pkg_description,inserted_by,insert_date,ready_to_approved,source";
		$data_array ="(".$id.",".$btb_lc_id.",2,".$txt_invoice_number.",".$txt_invoice_date.",".$txt_document_value.",".$txt_shipment_date.",".$txt_company_acc_date.",".$txt_bank_acceptance_date.",".$txt_bank_ref.",".$cbo_acceptance_time.",".$cbo_retire_source.",".$txt_remarks.",".$txt_edf_tenor.",".$bill_no.",".$bill_date.",".$cbo_shipment_mode.",".$cbo_document_status.",".$copy_doc_receive_date.",".$original_doc_receive_date.",".$doc_to_cnf.",".$feeder_vessel.",".$mother_vessel.",".$eta_date.",".$ic_receive_date.",".$shipping_bill_no.",".$cbo_inco_term.",".$inco_term_place.",".$port_of_loading.",".$port_of_discharge.",".$bill_of_entry_no.",".$psi_reference_no.",".$maturity_date.",".$edf_paid_date.",".$bill_of_entry_date.",".$txt_exchange_rate.",".$container_no.",".$pkg_quantity.",".$pkg_quantity_breakdown.",".$pakg_uom.",".$pkg_description.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_ready_to_approved.",".$cbo_source_id.")";
		
		
		$pay_id=return_next_id( "id", "com_import_payment", 1 ) ;
		$id_dtls=return_next_id( "id", "com_import_invoice_dtls", 1 ) ;
		$field_array1="id,import_invoice_id,btb_lc_id,is_lc,pi_id,current_acceptance_value,domestic_acceptance_value,item_category_id,inserted_by,insert_date";
		for ($i=1;$i<=$total_row;$i++)
		{
			$lc_id="lc_id_".$i;
			$pi_id="pi_id_".$i;
			$current_acceptance_value="current_acceptance_value_".$i;
			$domestic_acceptance_value=(str_replace("'",'',$$current_acceptance_value)*1)*(str_replace("'",'',$txt_exchange_rate)*1);

			$invoice_dtls_id="invoice_dtls_id_".$i;
			$item_category_id="item_category_id_".$i;
			if ($i!=1) $data_array1 .=",";
			$data_array1 .="(".$id_dtls.",".$id.",".$$lc_id.",2,".$$pi_id.",".$$current_acceptance_value.",".$domestic_acceptance_value.",".$$item_category_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id_dtls=$id_dtls+1;
		}
		check_table_status( $_SESSION['menu_id'],0);
		
		$rID=sql_insert("com_import_invoice_mst",$field_array,$data_array,0);
		$rID2=sql_insert("com_import_invoice_dtls",$field_array1,$data_array1,0);
		//echo "10**ttt**$rID**$rID2";disconnect($con);die;
		
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'","",$btb_lc_id)."**".$id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$btb_lc_id)."**".$id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);
				echo "0**".str_replace("'","",$btb_lc_id)."**".$id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$btb_lc_id)."**".$id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		$hide_approved_status=str_replace("'","",$hide_approved_status);
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1";disconnect($con); die;}	
		$com_pay_id=return_field_value("id","com_import_payment_com","invoice_id=$invoice_id and status_active=1 and is_deleted=0","id");
		$is_posted_account=return_field_value("is_posted_account","com_import_invoice_mst","id=$invoice_id and status_active=1 and is_deleted=0","is_posted_account");
		if($com_pay_id || $hide_approved_status!=0 || $is_posted_account == 1)
		{
			//echo "disable_enable_fields('txt_lc_number*txt_invoice_number*txt_invoice_date*txt_company_acc_date*txt_bank_acceptance_date*txt_document_value*txt_exchange_rate*cbo_retire_source',0,'','');\n";
			//*exchange_rate
			//*".$txt_exchange_rate."
			$field_array_up="bill_no*bill_date*shipment_mode*document_status*copy_doc_receive_date*original_doc_receive_date*doc_to_cnf*feeder_vessel*mother_vessel*eta_date*ic_receive_date*shipping_bill_no*inco_term*inco_term_place*port_of_loading*port_of_discharge*bill_of_entry_no*psi_reference_no*maturity_date*edf_paid_date*bill_of_entry_date*container_no*pkg_quantity*pkg_quantity_breakdown*pkg_uom*pkg_description*updated_by*update_date*ready_to_approved*source";
			$data_array_up ="".$bill_no."*".$bill_date."*".$cbo_shipment_mode."*".$cbo_document_status."*".$copy_doc_receive_date."*".$original_doc_receive_date."*".$doc_to_cnf."*".$feeder_vessel."*".$mother_vessel."*".$eta_date."*".$ic_receive_date."*".$shipping_bill_no."*".$cbo_inco_term."*".$inco_term_place."*".$port_of_loading."*".$port_of_discharge."*".$bill_of_entry_no."*".$psi_reference_no."*".$maturity_date."*".$edf_paid_date."*".$bill_of_entry_date."*".$container_no."*".$pkg_quantity."*".$pkg_quantity_breakdown."*".$pakg_uom."*".$pkg_description."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_ready_to_approved."*".$cbo_source_id."";
		}
		else
		{
			$field_array_up="btb_lc_id*is_lc*invoice_no*invoice_date*document_value*shipment_date*company_acc_date*bank_acc_date*bank_ref*acceptance_time*retire_source*remarks*edf_tenor*bill_no*bill_date*shipment_mode*document_status*copy_doc_receive_date*original_doc_receive_date*doc_to_cnf*feeder_vessel*mother_vessel*eta_date*ic_receive_date*shipping_bill_no*inco_term*inco_term_place*port_of_loading*port_of_discharge*bill_of_entry_no*psi_reference_no*maturity_date*edf_paid_date*bill_of_entry_date*exchange_rate*container_no*pkg_quantity*pkg_quantity_breakdown*pkg_uom*pkg_description*updated_by*update_date*ready_to_approved*source";
			$data_array_up ="".$btb_lc_id."*2*".$txt_invoice_number."*".$txt_invoice_date."*".$txt_document_value."*".$txt_shipment_date."*".$txt_company_acc_date."*".$txt_bank_acceptance_date."*".$txt_bank_ref."*".$cbo_acceptance_time."*".$cbo_retire_source."*".$txt_remarks."*".$txt_edf_tenor."*".$bill_no."*".$bill_date."*".$cbo_shipment_mode."*".$cbo_document_status."*".$copy_doc_receive_date."*".$original_doc_receive_date."*".$doc_to_cnf."*".$feeder_vessel."*".$mother_vessel."*".$eta_date."*".$ic_receive_date."*".$shipping_bill_no."*".$cbo_inco_term."*".$inco_term_place."*".$port_of_loading."*".$port_of_discharge."*".$bill_of_entry_no."*".$psi_reference_no."*".$maturity_date."*".$edf_paid_date."*".$bill_of_entry_date."*".$txt_exchange_rate."*".$container_no."*".$pkg_quantity."*".$pkg_quantity_breakdown."*".$pakg_uom."*".$pkg_description."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_ready_to_approved."*".$cbo_source_id."";
		}
		
		
		$conversation_rate=str_replace("'",'',$txt_exchange_rate)*1;
		if($conversation_rate=="") $conversation_rate=0;
		$pay_id=return_next_id( "id", "com_import_payment", 1 ) ;

		$field_array_up1="import_invoice_id*btb_lc_id*is_lc*pi_id*current_acceptance_value*domestic_acceptance_value*item_category_id*updated_by*update_date";
		$field_array1="id,import_invoice_id,btb_lc_id,is_lc,pi_id,current_acceptance_value,domestic_acceptance_value,item_category_id,inserted_by,insert_date";
		$add_comma=1;
		$id_dtls=return_next_id( "id", "com_import_invoice_dtls", 1 ) ;
		
		for ($i=1;$i<=$total_row;$i++)
		{
			$lc_id="lc_id_".$i;
			$pi_id="pi_id_".$i;
			$current_acceptance_value="current_acceptance_value_".$i;
			$domestic_acceptance_value=(str_replace("'",'',$$current_acceptance_value)*1)*(str_replace("'",'',$txt_exchange_rate)*1);
			$invoice_dtls_id="invoice_dtls_id_".$i;
			$item_category_id="item_category_id_".$i;
			if(str_replace("'",'',$$invoice_dtls_id)!="" && $is_posted_account != 1)
			{
				$id_arr[]=str_replace("'",'',$$invoice_dtls_id);
				$data_array_up1[str_replace("'",'',$$invoice_dtls_id)] =explode("*",("".$invoice_id."*".$$lc_id."*2*".$$pi_id."*".$$current_acceptance_value."*".$domestic_acceptance_value."*".$$item_category_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			
			if(str_replace("'",'',$$invoice_dtls_id)=="")
			{
				if ($add_comma!=1) $data_array1 .=",";
				$data_array1 .="(".$id_dtls.",".$invoice_id.",".$$lc_id.",2,".$$pi_id.",".$$current_acceptance_value.",".$domestic_acceptance_value.",".$$item_category_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id_dtls=$id_dtls+1;
				$add_comma++;
			}
		}
		
		//echo bulk_update_sql_statement( "com_import_invoice_dtls", "id", $field_array_up1, $data_array_up1, $id_arr );
		check_table_status( $_SESSION['menu_id'],0);
		$rID=$rID2=$rID3=true;
		if($com_pay_id  || $hide_approved_status!=0 || $is_posted_account == 1)
		{
			
			$rID=sql_update("com_import_invoice_mst",$field_array_up,$data_array_up,"id","".$invoice_id."",1);
		}
		else
		{
			
			if(count($id_arr)>0)
			{
				$rID2=execute_query(bulk_update_sql_statement( "com_import_invoice_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ));
			}
			
			if($data_array1!="")
			{
				$rID3=sql_insert("com_import_invoice_dtls",$field_array1,$data_array1,0);
			}
			$rID=sql_update("com_import_invoice_mst",$field_array_up,$data_array_up,"id","".$invoice_id."",1);
		}
		
		//echo "10".'*'.$rID.'*'.$rID2.'*'.$rID3;oci_rollback($con);disconnect($con);die;

		if($db_type==0)
		{
			if($rID  && $rID2  && $rID3)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$btb_lc_id)."**".str_replace("'","",$invoice_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$btb_lc_id)."**".str_replace("'","",$invoice_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID  && $rID2  && $rID3)
			{
				oci_commit($con); 
				echo "1**".str_replace("'","",$btb_lc_id)."**".str_replace("'","",$invoice_id);
			}
			else
			{
				oci_rollback($con);  
				echo "10**".str_replace("'","",$btb_lc_id)."**".str_replace("'","",$invoice_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		$hide_approved_status=str_replace("'","",$hide_approved_status);
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**2"; disconnect($con);die;}
		/*$field_array="status_active*is_deleted";
		$data_array="'0'*'1'";*/
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		check_table_status( $_SESSION['menu_id'],0);

		if ($hide_approved_status!=0){
			echo "30**This Invoice is Approved, Delete Not Allow.";disconnect($con);die;
		}
		
		$rID=sql_delete("com_import_invoice_mst",$field_array,$data_array,"id","".$invoice_id."",0);
		$rID2=sql_delete("com_import_invoice_dtls",$field_array,$data_array,"import_invoice_id","".$invoice_id."",1);
		//echo "10**$rID**$rID2";die;
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$btb_lc_id)."**".str_replace("'","",$invoice_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$btb_lc_id)."**".str_replace("'","",$invoice_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);  
				echo "2**".str_replace("'","",$btb_lc_id)."**".str_replace("'","",$invoice_id);
			}
			else
			{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$btb_lc_id)."**".str_replace("'","",$invoice_id);
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="cumulative_details_popup")
{
	echo load_html_head_contents("Cumulative Datails List", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	
</head>

<body>
<div align="center" style="width:500px;">
	
		<fieldset style="width:100%;">
            <legend>Cumulative Details</legend>           
            	<table cellpadding="0" cellspacing="0" width="500" class="rpt_table">
                	<thead>
                        <th>PI Number</th>
                    	<th>Invoice Number</th>
                        <th>Invoice Date</th>
                        <th>Invoice Value</th>
                    </thead>
                    <tbody>
                    <?
					$total=0;
					$sql="select a.invoice_no,a.invoice_date,b.pi_id,b.current_acceptance_value from com_import_invoice_mst a,com_import_invoice_dtls b where a.id=b.import_invoice_id and pi_id=$pi_id and a.status_active=1 and b.status_active=1";
					$data_array=sql_select($sql);
					foreach($data_array as $row)
					{
						$pi_number=return_field_value("pi_number","com_pi_master_details","id=".$row[csf("pi_id")]);
					?>
                    <tr>
                    <td><? echo $pi_number; ?></td>
                    <td><? echo $row[csf("invoice_no")]; ?></td>
                    <td><? echo $row[csf("invoice_date")]; ?></td>
                    <td align="right"><? echo $row[csf("current_acceptance_value")];$total+= $row[csf("current_acceptance_value")]; ?></td>
                    </tr>
                    <?
					}
					?>
                    </tbody>
                    <tfoot>
                    <th></th>
                    <th></th>
                    <th>Total:</th>
                    <th><? echo $total;?></th>
                    </tfoot>
                    </table>
                    
            </fieldset>
		
	</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();	
}


if ($action=="import_document_acceptance_letter")
{
	//echo $data; die;
	$data=explode("**",$data);
	$country_lib=return_library_array( "select id, country_name from lib_country",'id','country_name');	
	
	$data_array=sql_select("select a.btb_lc_id, a.invoice_no, a.invoice_date, a.document_value, a.bank_acc_date, a.bank_ref, a.bill_no, a.bill_date, b.lc_number, b.issuing_bank_id, b.lc_value, b.lc_date, b.supplier_id, b.item_category_id from com_import_invoice_mst a, com_btb_lc_master_details b, com_import_invoice_dtls c where a.id=c.import_invoice_id and a.id='$data[1]' and b.id=c.btb_lc_id group by a.btb_lc_id, a.invoice_no, a.invoice_date, a.document_value, a.bank_acc_date, a.bank_ref, a.bill_no, a.bill_date, b.lc_number, b.issuing_bank_id, b.lc_value, b.lc_date, b.supplier_id, b.item_category_id");
	foreach ($data_array as $row)
	{ 
		$bank_ref			= $row[csf("bank_ref")];
		$document_value		= $row[csf("document_value")];	
		$invoice_no			= $row[csf("invoice_no")];
		$invoice_date		= $row[csf("invoice_date")];
		$lc_id				= $row[csf("btb_lc_id")];
		$bl_cargo_no		= $row[csf("bill_no")];
		$bill_date			= change_date_format($row[csf("bill_date")]);
		$issuing_bank_id	= $row[csf("issuing_bank_id")];
		$supplier_id		= $row[csf("supplier_id")];
		//$lc_number		= $row[csf("lc_number")];
		$lc_number			= $data[2];
		//$lc_date			= $row[csf("lc_date")];
		$lc_date			= $data[4];
		$item_category_id	= $row[csf("item_category_id")];
		($row[csf('bank_acc_date')] == '0000-00-00' || $row[csf('bank_acc_date')] == '') ? $bank_acc_date='' : $bank_acc_date=change_date_format($row[csf('bank_acc_date')]);				
	}
	
	$data_array5=sql_select("select company_name, country_id, city from lib_company b where id='$data[3]'");
	foreach ($data_array5 as $row5)
	{ 
		$company_name	= $row5[csf("company_name")];
		$country_id		= $row5[csf("country_id")];	
		$city			= $row5[csf("city")];
	}
	
	$lca_no=return_field_value("lca_no","com_btb_lc_master_details","lc_number='$data[2]' and importer_id='$data[3]'");
	//$lca_no=return_field_value("lca_no","com_btb_lc_master_details","lc_number='$data[2]' and importer_id='$data[3]'");
	//$lca_no="select lca_no from com_btb_lc_master_details where lc_number='$data[2]' and importer_id='$data[3]'";
	
	//bank information retriving here
	$data_array1=sql_select("select id, bank_name, branch_name, contact_person, address from lib_bank where id='$issuing_bank_id'");
	foreach ($data_array1 as $row1)
	{ 
		$bank_name		= $row1[csf("bank_name")];
		$branch_name	= $row1[csf("branch_name")];
		$contact_person	= $row1[csf("contact_person")];
		$address		= $row1[csf("address")];
	}
	
	//letter body is retriving here
	$data_array2=sql_select("select letter_body from dynamic_letter where letter_type='$data[0]'");
	foreach ($data_array2 as $row2)
	{ 
		$letter_body = $row2[csf("letter_body")];
	}
	
	$raw_data=str_replace("BANKREF",$bank_ref,$letter_body);
	$raw_data=str_replace("BANKACCDATE",$lc_date,$raw_data);
	$raw_data=str_replace("CONTACTPERSON",$contact_person,$raw_data);
	$raw_data=str_replace("BANKNAME",$bank_name,$raw_data);
	$raw_data=str_replace("BRANCHNAME",$branch_name,$raw_data);
	$raw_data=str_replace("ADDRESS",$address,$raw_data);
	$raw_data=str_replace("DOCUMENTVALUE",$document_value,$raw_data);
	$raw_data=str_replace("SUPPLIER",$supplier_lib[$supplier_id],$raw_data);
	$raw_data=str_replace("LCNUMBER",$lc_number,$raw_data);
	//$raw_data=str_replace("LDDATE",date('d.m.Y', strtotime($lc_date)),$raw_data);
	$raw_data=str_replace("LDDATE",$lc_date,$raw_data);
	$raw_data=str_replace("INVOICENUMBER",$invoice_no,$raw_data);
	$raw_data=str_replace("INVOICEDATE",date('d.m.Y', strtotime($invoice_date)),$raw_data);
	$raw_data=str_replace("BLCARGONO",$bl_cargo_no,$raw_data);
	$raw_data=str_replace("ITEMCATEGORY",$item_category[$item_category_id],$raw_data);
	
	$raw_data=str_replace("BLDATE",$bill_date,$raw_data);
	$raw_data=str_replace("LCANO",$lca_no,$raw_data);
	
	$raw_data=str_replace("COMPANYNAME",$company_name,$raw_data);
	$raw_data=str_replace("LOCATIONNAME",$city,$raw_data);
	$raw_data=str_replace("COUNTRYNAME",$country_lib[$country_id],$raw_data);

	echo $raw_data;
	exit();
}


if ($action=="import_document_acceptance_forwarding2")
{
    extract($_REQUEST);
	//echo $data;die;
	
	$pi_sql="select b.pi_id, b.item_category_id, p.company_acc_date
	from com_import_invoice_mst p, com_import_invoice_dtls a, com_pi_item_details b where p.id=a.import_invoice_id and a.pi_id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.import_invoice_id=$data";
	//echo $pi_sql;
	$pi_sql_result=sql_select($pi_sql);
	$all_cat="";
	foreach($pi_sql_result as $row)
	{
		if($cat_check[$row[csf("item_category_id")]]=="")
		{
			$cat_check[$row[csf("item_category_id")]]=$row[csf("item_category_id")];
			$all_cat.=$item_category[$row[csf("item_category_id")]].",";
		}
		$company_acc_date=$row[csf("company_acc_date")];
	}
	$all_cat=chop($all_cat,",");
	
	$sql="SELECT a.id as accep_dtls_id, a.current_acceptance_value, b.id as btb_id, b.lc_number as btb_lc_no, b.importer_id, b.supplier_id, b.payterm_id, b.lc_date as btb_lc_date, b.lc_value as btb_lc_value, d.id as LC_SC_ID, d.export_lc_no as lc_sc_no, d.lc_date as lc_sc_date, d.expiry_date as lc_sc_expire_date, d.last_shipment_date as lc_sc_last_ship_date, d.lc_value as lc_sc_value, 0 as type
	from com_import_invoice_dtls a, com_btb_lc_master_details b 
	left join com_btb_export_lc_attachment c on b.id=c.import_mst_id and c.status_active=1 and c.is_deleted=0 
	left join com_export_lc d on c.lc_sc_id=d.id and c.is_lc_sc=0
	where a.btb_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.import_invoice_id=$data
	union all
	select a.id as accep_dtls_id, a.current_acceptance_value, b.id as btb_id, b.lc_number as btb_lc_no, b.importer_id, b.supplier_id, b.payterm_id, b.lc_date as btb_lc_date, b.lc_value as btb_lc_value, d.id as LC_SC_ID, d.contract_no as lc_sc_no, d.contract_date as lc_sc_date, d.expiry_date as lc_sc_expire_date, d.last_shipment_date as lc_sc_last_ship_date, d.contract_value as lc_sc_value, 1 as type
	from com_import_invoice_dtls a, com_btb_lc_master_details b 
	left join com_btb_export_lc_attachment c on b.id=c.import_mst_id and c.status_active=1 and c.is_deleted=0 
	left join com_sales_contract d on c.lc_sc_id=d.id and c.is_lc_sc=1
	where a.btb_lc_id=b.id and b.id=c.import_mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.import_invoice_id=$data";
	// echo $sql;
	$btb_sql=sql_select( $sql);
	$lc_sc_arr=array();
	foreach($btb_sql as $row)
	{
		if($accep_dtls_check[$row[csf("accep_dtls_id")]]=="")
		{
			$accep_dtls_check[$row[csf("accep_dtls_id")]]=$row[csf("accep_dtls_id")];
			$current_acceptance_value+=$row[csf("current_acceptance_value")];
		}
		$btb_lc_no=$row[csf("btb_lc_no")];
		$importer_id=$row[csf("importer_id")];
		$supplier_id=$row[csf("supplier_id")];
		$payterm_id=$row[csf("payterm_id")];
		$btb_lc_date=$row[csf("btb_lc_date")];
		$btb_lc_value=$row[csf("btb_lc_value")];
		
		if(!in_array($row["LC_SC_ID"],$lc_sc_arr) && $row["LC_SC_ID"]!="")
		{
			$lc_sc_arr[]=$row["LC_SC_ID"];
			$lc_sc_no.=$row[csf("lc_sc_no")].", ";
			$lc_sc_date.=change_date_format($row[csf("lc_sc_date")]).", ";
			$lc_sc_expire_date.=change_date_format($row[csf("lc_sc_expire_date")]).", ";
			$lc_sc_last_ship_date.=change_date_format($row[csf("lc_sc_last_ship_date")]).", ";
			$lc_sc_value+=$row[csf("lc_sc_value")];
			$lc_sc_type=$row[csf("type")];
		}

	}
	$lc_sc_no=rtrim($lc_sc_no,", ");
	$lc_sc_date=rtrim($lc_sc_date,", ");
	$lc_sc_expire_date=rtrim($lc_sc_expire_date,", ");
	$lc_sc_last_ship_date=rtrim($lc_sc_last_ship_date,", ");

	?>
    <style>
	.trborders
	{
		border:1px solid;
		padding-left:2px;
	}
	</style>
    <table id="" cellspacing="0" cellpadding="0" width="750" border="0">
        <tr style="margin-bottom:20px;">
           <!-- <td align="center" style="font-size:18px; font-weight:bold">Company acceptence  date</td>-->
            <td colspan="5" align="center" style="font-size:18px; font-weight:bold">FORWARDING FOR BTB Non L/C ACCEPTANCE</td>
        </tr>
        <!--<tr>
        	<td><?// if($com_acc_date!="" && $com_acc_date!="0000-00-00") echo change_date_format($company_acc_date); ?></td>
            <td colspan="4">&nbsp;</td>
        </tr>-->
        <tr>
        	<td colspan="5">&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td colspan="2" align="center" style="font-size:16px; font-weight:bold">BTB Non L/C INFORMATION</td>
            <td colspan="2" align="center" style="font-size:14px; font-weight:bold">MASTER LC/SC INFORMATION</td>
        </tr>
        <tr>
            <td width="150">&nbsp;</td>
            <td width="150" class="trborders">Company Name</td>
            <td width="150" class="trborders"><? $company_name=return_field_value("company_name","lib_company","id=$importer_id","company_name"); echo $company_name;?></td>
            <td width="150" class="trborders">Master LC/SC No</td>
            <td class="trborders"><? echo $lc_sc_no; ?></td>
        </tr>
        <tr>
            <td></td>
            <td class="trborders">Supplier Name</td>
            <td class="trborders"><? $supplier_name=return_field_value("supplier_name","lib_supplier","id=$supplier_id","supplier_name"); echo $supplier_name;?></td>
            <td class="trborders">LC/SC date</td>
            <td class="trborders"><? echo $lc_sc_date; ?></td>
        </tr>
        <tr>
            <td></td>
            <td class="trborders">BTB Non L/C No</td>
            <td class="trborders"><? echo $btb_lc_no;?></td>
            <td class="trborders">LC/SC expire date</td>
            <td class="trborders"><? echo $lc_sc_expire_date; ?></td>
        </tr>
        <tr>
            <td></td>
            <td class="trborders">Pay Terms</td>
            <td class="trborders"><? echo $pay_term[$payterm_id];?></td>
            <td class="trborders">Last Ship Date</td>
            <td class="trborders"><? echo $lc_sc_last_ship_date; ?></td>
        </tr>
        <tr>
            <td></td>
            <td class="trborders">BTB Non L/C Date</td>
            <td class="trborders"><? if($btb_lc_date!="" && $btb_lc_date!="0000-00-00") echo change_date_format($btb_lc_date); ?></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td class="trborders">LC/SC Value</td>
            <td class="trborders"><? echo number_format($lc_sc_value,2);?></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td class="trborders">BTB Non L/C Value [$]</td>
            <td class="trborders"><? echo number_format($btb_lc_value,2);?></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td class="trborders">BTB Non L/C of Accep. Value [$]</td>
            <td class="trborders"><? echo number_format($current_acceptance_value,2);?></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td class="trborders">Material Category</td>
            <td class="trborders"><? echo $all_cat;?></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="5">&nbsp;</td>
        </tr>
        <tr>
            <td style="padding-left:20px;" valign="top">&#10003;</td>
            <td colspan="4">Docs Need advance acceptance</td>
        </tr>
        <tr>
            <td style="padding-left:20px;" valign="top">&#10003;</td>
            <td colspan="4">We here By confirm that we have received all goods, against the aforesaid documents, which are 'OK' in terms of quality and quantity. We also confirm that the referred goods have been properly reflected in the stock register maintained by store after completion of required formalities and we have no claim against such</td>
        </tr>
    </table>
	<?
	echo signature_table(201, $importer_id, "750px");
	exit();
}





if ($action=="import_document_acceptance_letter")
{
	$data=explode("**",$data);
	$country_lib=return_library_array( "select id, country_name from lib_country",'id','country_name');
	//Sales Contact Lien-------------
	
		
	$data_array=sql_select("select	a.eta_date,a.etd_actual,a.eta_advice,a.eta_actual,a.port_of_discharge,a.shipment_date,a.port_of_loading,a.container_status,a.company_acc_date,a.doc_to_cnf,a.btb_lc_id, a.invoice_no, a.invoice_date, a.document_value, a.bank_acc_date, a.bank_ref, a.bill_no, a.bill_date, b.lc_number, b.issuing_bank_id, b.lc_value, b.lc_date, b.supplier_id, b.item_category_id,b.currency_id,b.last_shipment_date,b.etd_date from com_import_invoice_mst a, com_btb_lc_master_details b where a.id='$data[1]' and b.id=a.btb_lc_id");
		foreach ($data_array as $row)
		{
			$bank_ref			= $row[csf("bank_ref")];
			$document_value		= $row[csf("document_value")];
			$invoice_no			= $row[csf("invoice_no")];
			$invoice_date		= $row[csf("invoice_date")];
			$lc_id				= $row[csf("btb_lc_id")];
			$bl_cargo_no		= $row[csf("bill_no")];
			$bill_date			= change_date_format($row[csf("bill_date")]);
			$issuing_bank_id	= $row[csf("issuing_bank_id")];
			$supplier_id		= $row[csf("supplier_id")];
			$lc_number			= $row[csf("lc_number")];
			$lc_date			= $row[csf("lc_date")];
			$item_category_id	= $row[csf("item_category_id")];
			$doc_to_cnf	= $row[csf("doc_to_cnf")];
			$company_acc_date	= $row[csf("company_acc_date")];
			$company_acc_date	= $row[csf("company_acc_date")];
			$currency_id	= $row[csf("currency_id")];
			$lc_value	= $row[csf("lc_value")];
			$bank_acc_date	= $row[csf("bank_acc_date")];
			
			$bank_acc_date=($row[csf('bank_acc_date')] == '0000-00-00' || $row[csf('bank_acc_date')] == '') ? '' : change_date_format($row[csf('bank_acc_date')]);
			
			
			$eta_date=($row[csf('eta_date')] == '0000-00-00' || $row[csf('eta_date')] == '') ? '' :date('d.m.Y', strtotime($row[csf('eta_date')]));
			$shipment_date=($row[csf('shipment_date')] == '0000-00-00' || $row[csf('shipment_date')] == '') ? '' : $row[csf('shipment_date')];
			$last_shipment_date=($row[csf('last_shipment_date')] == '0000-00-00' || $row[csf('last_shipment_date')] == '') ? '' : date('d.m.Y', strtotime($row[csf('last_shipment_date')]));
			$etd_date=($row[csf('etd_date')] == '0000-00-00' || $row[csf('etd_date')] == '') ? '' : date('d.m.Y', strtotime($row[csf('etd_date')]));
			
			
			
			$etd_actual=($row[csf('etd_actual')] == '0000-00-00' || $row[csf('etd_actual')] == '') ? '' :date('d.m.Y', strtotime($row[csf('etd_actual')]));
			$eta_advice=($row[csf('eta_advice')] == '0000-00-00' || $row[csf('eta_advice')] == '') ? '' :date('d.m.Y', strtotime($row[csf('eta_advice')]));
			$eta_actual=($row[csf('eta_actual')] == '0000-00-00' || $row[csf('eta_actual')] == '') ? '' :date('d.m.Y', strtotime($row[csf('eta_actual')]));
						
			$port_of_discharge=$row[csf('port_of_discharge')];			
			$port_of_loading=$row[csf('port_of_loading')];
			$container_status=$row[csf('container_status')];
			
		}
	

		$data_array5=sql_select("select company_name, country_id, city from lib_company b where id='$data[3]'");
		foreach ($data_array5 as $row5)
		{
			$company_name	= $row5[csf("company_name")];
			$country_id		= $row5[csf("country_id")];
			$city			= $row5[csf("city")];
		}

	$lca_no=return_field_value("lca_no","com_btb_lc_master_details","lc_number='$data[2]' and importer_id='$data[3]'");

	//bank information retriving here
	$data_array1=sql_select("select id, bank_name, branch_name, contact_person, address from lib_bank where id='$issuing_bank_id'");
	foreach ($data_array1 as $row1)
	{
		$bank_name		= $row1[csf("bank_name")];
		$branch_name	= $row1[csf("branch_name")];
		$contact_person	= $row1[csf("contact_person")];
		$address		= $row1[csf("address")];
	}

	$container_status_arr = array(1=>"FCL", 2=>"LCL");
	//letter body is retriving here
	$data_array2=sql_select("select letter_body from dynamic_letter where letter_type='$data[0]'");
	foreach ($data_array2 as $row2)
	{
		$letter_body = $row2[csf("letter_body")];
	}

	$raw_data=str_replace("__BANKREF__",$bank_ref,$letter_body);
	$raw_data=str_replace("__BANKACCDATE__",date('F d, Y', strtotime($bank_acc_date)),$raw_data);
	$raw_data=str_replace("__CONTACTPERSON__",$contact_person,$raw_data);
	$raw_data=str_replace("__BANKNAME__",$bank_name,$raw_data);
	$raw_data=str_replace("__BRANCHNAME__",$branch_name,$raw_data);
	$raw_data=str_replace("__ADDRESS__",$address,$raw_data);
	$raw_data=str_replace("__DOCUMENTVALUE__",number_format($document_value,2),$raw_data);
	$raw_data=str_replace("__SUPPLIER__",$supplier_lib[$supplier_id],$raw_data);
	$raw_data=str_replace("__LCNUMBER__",$lc_number,$raw_data);
	$raw_data=str_replace("__LDDATE__",date('d.m.Y', strtotime($lc_date)),$raw_data);
	$raw_data=str_replace("__INVOICENUMBER__",$invoice_no,$raw_data);
	$raw_data=str_replace("__INVOICEDATE__",date('d.m.Y', strtotime($invoice_date)),$raw_data);
	$raw_data=str_replace("__BLCARGONO__",$bl_cargo_no,$raw_data);
	$raw_data=str_replace("__ITEMCATEGORY__",$item_category[$item_category_id],$raw_data);

	$raw_data=str_replace("__BLDATE__",$bill_date,$raw_data);
	$raw_data=str_replace("__LCANO__",$lca_no,$raw_data);

	$raw_data=str_replace("__COMPANYNAME__",$company_name,$raw_data);
	$raw_data=str_replace("__LOCATIONNAME__",$city,$raw_data);
	$raw_data=str_replace("__COUNTRYNAME__",$country_lib[$country_id],$raw_data);

	$raw_data=str_replace("__DOCTOCNF__",date('d.m.Y', strtotime($doc_to_cnf)),$raw_data);
	$raw_data=str_replace("__COMPANYACCDATE__",date('d.m.Y', strtotime($company_acc_date)),$raw_data);

	$raw_data=str_replace("__CURRENCY__",$currency[$currency_id],$raw_data);
	$raw_data=str_replace("__LCVALUE__",number_format($lc_value,2),$raw_data);



	$raw_data=str_replace("__ETADATE__",$eta_date,$raw_data);
	$raw_data=str_replace("__LASTSHIPMENTDATE__",$last_shipment_date,$raw_data);
	$raw_data=str_replace("__ETDACTUAL__",$etd_actual,$raw_data);
	$raw_data=str_replace("__PORTOFLOADING__",$port_of_loading,$raw_data);
	$raw_data=str_replace("__ETAADVICE__",$eta_advice,$raw_data);
	$raw_data=str_replace("__ETAACTUAL__",$eta_actual,$raw_data);
	$raw_data=str_replace("__PORTOFDISCHARGE__",$port_of_discharge,$raw_data);
	$raw_data=str_replace("__ETDDATE__",$etd_date,$raw_data);
	$raw_data=str_replace("__ETDDATEETDACTUAL__",(datediff('d',$etd_actual,$etd_date))-1,$raw_data);
	$raw_data=str_replace("__CONTAINERSTASUS__",$container_status_arr[str_replace("'","",$container_status)],$raw_data);


	if($container_status==1){$addDay=5;}
	else if($container_status==2){$addDay=7;}
	$clearing_date=add_date($eta_actual ,$addDay);	
	$raw_data=str_replace("__CLEARINGDATE__",date('d.m.Y', strtotime($clearing_date)),$raw_data);


	echo $raw_data;
	exit();
}



if($action=="open_mrr_details")
{
	echo load_html_head_contents("BTB / Import LC List", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$invoice_arr=return_library_array("select id, invoice_no from com_import_invoice_mst where status_active=1","id","invoice_no");
	//echo $receive_basis."=".$mrr_ids."=".$invoice_id."=".$btb_id."=".$pi_id."=".$inv_dtls_id;die;
	if($inv_dtls_id)
	{
		$curr_selected_mrr="select id, import_invoice_id, btb_lc_id, pi_id, invoice_dtls_id, mrr_id from com_import_invoice_dtls_mrr where invoice_dtls_id=$inv_dtls_id and status_active=1";
		//echo $prev_selected_mrr;
		$curr_selected_mrr_result=sql_select($curr_selected_mrr);
		$curr_selected_mrr_arr=array();
		foreach($curr_selected_mrr_result as $row)
		{
			$curr_selected_mrr_arr[$row[csf("mrr_id")]]=$row[csf("mrr_id")];
		}
		
		$prev_selected_mrr="select id, import_invoice_id, btb_lc_id, pi_id, invoice_dtls_id, mrr_id from com_import_invoice_dtls_mrr where invoice_dtls_id<>$inv_dtls_id and pi_id=$pi_id and status_active=1";
		//echo $prev_selected_mrr;
		$prev_selected_mrr_result=sql_select($prev_selected_mrr);
		$prev_selected_mrr_arr=array();
		foreach($prev_selected_mrr_result as $row)
		{
			$prev_selected_mrr_arr[$row[csf("mrr_id")]]=$row[csf("mrr_id")];
			$prev_inv_mrr_arr[$row[csf("mrr_id")]]=$invoice_arr[$row[csf("import_invoice_id")]];
		}
		if(count($prev_selected_mrr_result)>0)
		{
			$js_prev_selected_mrr_arr= json_encode($prev_selected_mrr_arr);
			$js_prev_inv_mrr_arr= json_encode($prev_inv_mrr_arr);
			//echo $js_prev_selected_mrr_arr;
			//echo "var js_prev_selected_mrr_arr = ". $js_prev_selected_mrr_arr . ";\n";	
		}
		if($js_prev_selected_mrr_arr=="") $js_prev_selected_mrr_arr="[]";
		if($js_prev_inv_mrr_arr=="") $js_prev_inv_mrr_arr="[]";
		
	}
	?>
    <script>
	var js_prev_selected_mrr_arr ='<?= $js_prev_selected_mrr_arr;?>';
	var js_prev_inv_mrr_arr ='<?= $js_prev_inv_mrr_arr;?>';
	function fn_mrr_save()
	{
		var inv_id=trim($("#hdn_inv_id").val());
		var btb_id=trim($("#hdn_btb_id").val());
		var pi_id=trim($("#hdn_pi_id").val());
		var inv_dtls_id=trim($("#hdn_inv_dtls_id").val());
		if(inv_id=="")
		{
			alert("Save Document Acceptance First");return;
		}
		var mrr_tbl_lenght=$('#mrr_table tbody tr').length;
		//alert(inv_id+"="+mrr_tbl_lenght);
		var mrr_data="";var j=0;
		for(var i=1; i<=mrr_tbl_lenght; i++)
		{
			if($('#chkMrr_'+i).is(':checked'))
			{
				j++; 
				mrr_data += "&hdnMrrId_"+j+ "='" + $('#hdnMrrId_'+i).val()+"'";
				
			}
		}
		if(mrr_data=="")
		{
			alert("No Data Selected");return;
		}
		var operation=0;
		var data="action=save_update_delete_mrr&operation="+operation+'&total_row='+j+'&inv_id='+inv_id+'&btb_id='+btb_id+'&pi_id='+pi_id+'&inv_dtls_id='+inv_dtls_id+mrr_data;
		//alert(data);return;
		//freeze_window(operation);
		http.open("POST","import_document_acceptance_non_lc_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_mrr_reponse;
	}
	
	function fnc_mrr_reponse()
	{
		if(http.readyState == 4)
		{
			//alert(http.responseText);release_freezing();return;
			var reponse=trim(http.responseText).split('**');
			show_msg(trim(reponse[0]));
			if((reponse[0]==0))
			{
				alert("Data Save Successfully");
				parent.emailwindow.hide();
				return;
			}
			else
			{
				alert("Data Not Save Successfully");return;
			}
		}
	}
	
	function fn_prev_mrr(id_ref)
	{
		var mrr_id=trim($("#hdnMrrId_"+id_ref).val());
		if($("#chkMrr_"+id_ref).is(':checked'))
		{
			if(js_prev_selected_mrr_arr[mrr_id]=="" || js_prev_selected_mrr_arr[mrr_id] == undefined )
			{
				//alert(1);
			}
			else
			{
				$("#chkMrr_"+id_ref).prop('checked', false);
				alert("This MRR Attach In Another Invoice \n Invoice No : "+js_prev_inv_mrr_arr[mrr_id]);
			}
		}
	}
	
	</script>
    <?
	
	$mrr_ids=ltrim(implode(",",array_unique(explode(",",$mrr_ids))), ',');
	$mrr_sql="select a.id, a.recv_number, a.receive_date, a.challan_no, sum(b.order_qnty) as rcv_qnty, sum(b.order_amount) as rcv_amount 
	from inv_receive_master a, inv_transaction b 
	where a.id=b.mst_id and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.payment_over_recv=0 and a.receive_basis=$receive_basis and a.id in($mrr_ids)
	group by a.id, a.recv_number, a.receive_date, a.challan_no 
	order by a.id";
	
	$mrr_return_sql="select a.id, a.issue_number as recv_number, a.issue_date as receive_date, a.challan_no , b.cons_uom as order_uom, sum(b.cons_quantity) as rcv_qnty, sum(b.cons_amount) as rcv_amount from inv_issue_master a, inv_transaction b where a.id=b.mst_id and b.transaction_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.received_id in($mrr_ids)
	group by a.id, a.issue_number, a.issue_date, b.cons_uom, a.challan_no 
	order by a.id";
	//echo $mrr_return_sql;//die;
	$mrr_sql_result=sql_select($mrr_sql);
	$mrr_return_sql_result=sql_select($mrr_return_sql);
	
	if(count($mrr_sql_result)>0)
	{
		?>
        <div align="center" style="width:700px;">
           <table cellpadding="0" cellspacing="0" width="680" class="rpt_table" border="1" rules="all" id="mrr_table">
                <thead>
                    <th width="50">SL</th>
                    <th width="150">MRR Number</th>
                    <th width="80">MRR Date</th>
                    <th width="100">MRR Qty</th>
					<th width="100">Challan No</th>
                    <th width="100">MRR Value</th>
                    <th>
                    <input type="hidden" id="hdn_inv_id" name="hdn_inv_id" value="<?= $invoice_id;?>" />
                    <input type="hidden" id="hdn_btb_id" name="hdn_btb_id" value="<?= $btb_id;?>" />
                    <input type="hidden" id="hdn_pi_id" name="hdn_pi_id" value="<?= $pi_id;?>" />
                    <input type="hidden" id="hdn_inv_dtls_id" name="hdn_inv_dtls_id" value="<?= $inv_dtls_id;?>" />
                    </th>
                </thead>
                <tbody>
                	<?
					$i=1;
					foreach($mrr_sql_result as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF";
						else $bgcolor="#FFFFFF";
						if($curr_selected_mrr_arr[$row[csf("id")]]) $check_mrr="checked"; else $check_mrr="";
						?>
                        <tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor;?>')" id="tr_<?= $i;?>" style="text-decoration:none; cursor:pointer">
                            <td align="center"><?= $i; ?></td>	
                            <td><p><? echo $row[csf("recv_number")]; ?>&nbsp;</p></td>
                            <td align="center"><p><? echo change_date_format($row[csf("receive_date")]); ?>&nbsp;</p></td>
                            <td align="right"><? echo number_format($row[csf("rcv_qnty")],2); ?></td>	
							<td style="padding-left:3px;"><? echo $row[csf("challan_no")]; ?></td>
                            <td align="right"><? echo number_format($row[csf("rcv_amount")],2); ?></td>
                            <td align="center">
                            <input type="checkbox" id="chkMrr_<?= $i; ?>" onClick="fn_prev_mrr(<?= $i; ?>)" name="chkMrr_<?= $i; ?>" <?= $check_mrr; ?> />
                            <input type="hidden" id="hdnMrrId_<?= $i; ?>" name="hdnMrrId_<?= $i; ?>" value="<?= $row[csf("id")];?>" />
                            </td>
                        </tr>
                        <?
						$rcv_total_qnty+=$row[csf("rcv_qnty")];
						$rcv_total_amount+=$row[csf("rcv_amount")];
						$i++;
					}
					?>
                </tbody>
                <tfoot>
                	<th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Total:</th>
                    <th align="right"><? echo number_format($rcv_total_qnty,2); ?></th>
					<th>&nbsp;</th>
                    <th><? echo number_format($rcv_total_amount,2); ?></th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>
        </div>
        <div align="center" style="width:700px;">
        <table cellpadding="0" cellspacing="0" width="680" class="rpt_table" border="1" rules="all">
        	<tr>
            	<td height="50" valign="middle" align="center" class="button_container"><input type="button" id="btb_save" style="width:100px;" class="formbutton" value="Save" onClick="fn_mrr_save();" /></td>
            </tr>
        </table>
        </div>
        <?
	}
	if(count($mrr_return_sql_result)>0)
	{
		?>
        <div align="center" style="width:700px;">
        	
           <table cellpadding="0" cellspacing="0" width="680" class="rpt_table" border="1" rules="all">
                <thead>
                	<tr>
                    	<th colspan="7">Return Data</th>
                    </tr>
                    <tr>
                    	<th width="50">SL</th>
                        <th width="150">MRR Number</th>
                        <th width="80">MRR Date</th>
                        <th width="100">MRR Qty</th>
                        <th width="80">UOM</th>
						<th width="100">Challan No</th>
                        <th>MRR Value</th>
                    </tr>
                    
                </thead>
                <tbody>
                	<?
					$j=1;
					foreach($mrr_return_sql_result as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF";
						else $bgcolor="#FFFFFF";
						?>
                        <tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor;?>')" id="tr_<?= $i;?>" style="text-decoration:none; cursor:pointer">
                            <td align="center"><?= $j; ?></td>	
                            <td><p><? echo $row[csf("recv_number")]; ?>&nbsp;</p></td>
                            <td align="center"><p><? echo change_date_format($row[csf("receive_date")]); ?>&nbsp;</p></td>
                            <td align="right"><? echo number_format($row[csf("rcv_qnty")],2); ?></td>	
                            <td align="center"><? echo $unit_of_measurement[$row[csf("order_uom")]]; ?></td>
							<td style="padding-left:3px;"><? echo $row[csf("challan_no")]; ?></td>
                            <td align="right"><? echo number_format($row[csf("rcv_amount")],2); ?></td>
                        </tr>
                        <?
						$rcv_total_qnty+=$row[csf("rcv_qnty")];
						$rcv_total_amount+=$row[csf("rcv_amount")];
						$i++;$j++;
					}
					?>
                </tbody>
                <tfoot>
                	<th width="40">&nbsp;</th>
                    <th width="120">&nbsp;</th>
                    <th width="70">Total:</th>
                    <th width="80" align="right"><? echo number_format($rcv_total_qnty,2); ?></th>
                    <th width="80">&nbsp;</th>
					<th >&nbsp;</th>
                    <th align="right"><? echo number_format($rcv_total_qnty,2); ?></th>
                </tfoot>
            </table>
        </div>
        <?
	}
	exit();
}

if($action=="save_update_delete_mrr")
{
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//echo "10**".$total_row;die;
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		//echo "10**".$con;die;
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id=return_next_id( "id", "com_import_invoice_dtls_mrr", 1 ) ;
		$field_array="id, import_invoice_id, btb_lc_id, pi_id, invoice_dtls_id, mrr_id, inserted_by, insert_date,  status_active, is_deleted";
		$data_array="";
		for($i=1;$i<=$total_row;$i++)
		{
			$hdnMrrId="hdnMrrId_".$i;
			if($data_array!="") $data_array.=", ";
			$data_array .="(".$id.",".$inv_id.",".$btb_id.",".$pi_id.",".$inv_dtls_id.",".$$hdnMrrId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$id++;
		}
		
		//echo "10**".$data_array;die;
		//echo "10** insert into com_import_invoice_dtls_mrr ($field_array) values $data_array";die;
		$rID2=execute_query("delete from com_import_invoice_dtls_mrr where invoice_dtls_id=$inv_dtls_id");
		$rID=sql_insert("com_import_invoice_dtls_mrr",$field_array,$data_array,0);
		
		//echo "10**".$rID."**".$rID2;die;
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'","",$inv_id)."**".$id;
				
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$inv_id)."**".$id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);
				echo "0**".str_replace("'","",$inv_id)."**".$id;
				
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$inv_id)."**".$id;
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="import_document_acceptance_forwarding3")
{
    extract($_REQUEST);
	//echo $data;die;
	
	$lib_supplier=return_library_array("select id, supplier_name from lib_supplier","id", "supplier_name");
	$lib_buyer=return_library_array("select id, short_name from lib_buyer","id", "short_name");
	$lib_item=return_library_array("select id, item_name from lib_item_group","id", "item_name");
	$color_library = return_library_array('select id,color_name from lib_color where status_active=1 and is_deleted=0','id','color_name');
	$lib_body_part_arr=return_library_array("select id, body_part_full_name from lib_body_part", "id", "body_part_full_name");
	$count_arr=return_library_array("select id, yarn_count from lib_yarn_count WHERE status_active = 1 AND is_deleted = 0 ORDER BY yarn_count",'id','yarn_count');
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
	
	$pi_sql="SELECT b.pi_id, b.item_category_id, b.amount as pi_amount, p.company_acc_date, p.invoice_no, p.bank_ref, p.bank_acc_date, p.exchange_rate
	from com_import_invoice_mst p, com_import_invoice_dtls a, com_pi_item_details b where p.id=a.import_invoice_id and a.pi_id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.import_invoice_id=$data ";
	// echo $pi_sql;
	$pi_sql_result=sql_select($pi_sql);
	$all_cat="";
	foreach($pi_sql_result as $row)
	{
		if($cat_check[$row[csf("item_category_id")]]=="")
		{
			$cat_check[$row[csf("item_category_id")]]=$row[csf("item_category_id")];
			$all_cat.=$item_category[$row[csf("item_category_id")]].",";
		}
		$pi_id.=$row[csf("pi_id")].',';
		$invoice_no=$row[csf("invoice_no")];
		$bank_ref=$row[csf("bank_ref")];
		$bank_acc_date=$row[csf("bank_acc_date")];
		$company_acc_date=$row[csf("company_acc_date")];
		$exchange_rate=$row[csf("exchange_rate")];
		$pi_amount+=$row[csf("pi_amount")];
	}
	$all_cat=chop($all_cat,",");
	$pi_ids=implode(',',array_unique(explode(',',chop($pi_id,','))));
	
	$sql="SELECT a.id as accep_dtls_id, a.pi_id, a.current_acceptance_value, b.id as btb_id, b.lc_number as btb_lc_no, b.importer_id, b.supplier_id, b.payterm_id, b.lc_date as btb_lc_date, b.lc_value as btb_lc_value, b.currency_id, d.id as lc_sc_id, d.buyer_name, d.export_lc_no as lc_sc_no, d.lc_date as lc_sc_date, d.expiry_date as lc_sc_expire_date, d.last_shipment_date as lc_sc_last_ship_date, d.lc_value as lc_sc_value, 0 as type
	from com_import_invoice_dtls a, com_btb_lc_master_details b 
	left join com_btb_export_lc_attachment c on b.id=c.import_mst_id and c.is_lc_sc=0 and c.status_active=1 and c.is_deleted=0 
	left join com_export_lc d on c.lc_sc_id=d.id and c.is_lc_sc=0
	where a.btb_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.import_invoice_id=$data
	union all
	select a.id as accep_dtls_id, a.pi_id, a.current_acceptance_value, b.id as btb_id, b.lc_number as btb_lc_no, b.importer_id, b.supplier_id, b.payterm_id, b.lc_date as btb_lc_date, b.lc_value as btb_lc_value, b.currency_id, d.id as lc_sc_id, d.buyer_name, d.contract_no as lc_sc_no, d.contract_date as lc_sc_date, d.expiry_date as lc_sc_expire_date, d.last_shipment_date as lc_sc_last_ship_date, d.contract_value as lc_sc_value, 1 as type
	from com_import_invoice_dtls a, com_btb_lc_master_details b 
	left join com_btb_export_lc_attachment c on b.id=c.import_mst_id and c.is_lc_sc=1 and c.status_active=1 and c.is_deleted=0 
	left join com_sales_contract d on c.lc_sc_id=d.id and c.is_lc_sc=1
	where a.btb_lc_id=b.id and b.id=c.import_mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.import_invoice_id=$data";
	// echo $sql;die;
	$btb_sql=sql_select( $sql);
	$btb_lc_arr=array();
	foreach($btb_sql as $row)
	{
		if($accep_dtls_check[$row[csf("accep_dtls_id")]]=="")
		{
			$accep_dtls_check[$row[csf("accep_dtls_id")]]=$row[csf("accep_dtls_id")];
			$current_acceptance_value+=$row[csf("current_acceptance_value")];
		}
		$btb_lc_no=$row[csf("btb_lc_no")];
		$importer_id=$row[csf("importer_id")];
		$supplier_id=$row[csf("supplier_id")];
		$payterm_id=$row[csf("payterm_id")];
		$btb_lc_date=$row[csf("btb_lc_date")];
		$btb_lc_value=$row[csf("btb_lc_value")];
		
		$lc_sc_no=$row[csf("lc_sc_no")];
		$lc_sc_date=$row[csf("lc_sc_date")];
		$lc_sc_expire_date=$row[csf("lc_sc_expire_date")];
		$lc_sc_last_ship_date=$row[csf("lc_sc_last_ship_date")];
		$lc_sc_value=$row[csf("lc_sc_value")];
		$lc_sc_type=$row[csf("type")];
		$currency_id=$row[csf("currency_id")];

		$btb_lc_arr[$row[csf("pi_id")]]['btb_lc_no']=$row[csf("btb_lc_no")];
		if ($row[csf("buyer_name")] != ''){
			$btb_lc_arr[$row[csf("pi_id")]]['buyer_name']=$row[csf("buyer_name")];
		}		
		$btb_lc_arr[$row[csf("pi_id")]]['btb_lc_value']=$row[csf("btb_lc_value")];
	}
	//echo '<pre>';print_r($btb_lc_arr);

	if($pi_ids != '')
	{
		$sql_pi = "select a.id as pi_id, a.goods_rcv_status, a.item_category_id, a.supplier_id, a.pi_number, b.work_order_id, b.work_order_no, b.item_group, b.net_pi_amount as pi_value, b.color_id, b.count_name, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_composition_item2, b.yarn_composition_percentage2, b.yarn_type, b.body_part_id, b.fab_type, b.fabric_construction, b.fab_design, b.fabric_composition, b.item_description, b.determination_id, b.item_prod_id, b.rate 
		from com_pi_master_details a, com_pi_item_details b 
		where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in ($pi_ids)";
		// echo $sql_pi;
		$sql_pi_res=sql_select($sql_pi);
		$item_id_arr=array();
		foreach ($sql_pi_res as $row) 
		{
			if($pi_id_check[$row[csf("pi_id")]]=="")
			{
				$pi_id_check[$row[csf("pi_id")]]=$row[csf("pi_id")];
				if ($row[csf("goods_rcv_status")] == 2) $all_pi_ids.=$row[csf("pi_id")].",";
			}

			if($wo_order_id_check[$row[csf("work_order_id")]]=="")
			{
				$wo_order_id_check[$row[csf("work_order_id")]]=$row[csf("work_order_id")];
				if ($row[csf("goods_rcv_status")] == 1) $all_wo_ids.=$row[csf("work_order_id")].",";
			}

			if ($row[csf("item_category_id")] == 1)
			{
				if($row[csf('count_name')]!=0) {$count_name = $count_arr[$row[csf('count_name')]];}
				if($row[csf('yarn_composition_percentage1')]!=0) {$composition_percentage1 = $composition[$row[csf('yarn_composition_item1')]]."%";}
				if($row[csf('yarn_type')]!=0) {$yarnType = $yarn_type[$row[csf('yarn_type')]];}
				if($row[csf('color_id')]!=0) {$color_name = $color_library[$row[csf('color_id')]];}
				$item_desc=	$item_description=$count_name.' '.$composition_percentage1.' '.$yarnType.' '.$color_name;
			}							
			else if ($row[csf("item_category_id")] == 2) $item_desc=$item_description=$row[csf('fabric_construction')]." ".$row[csf('fabric_composition')];
			else if ($row[csf("item_category_id")] == 3) $item_desc=$item_description=$row[csf('color_id')]." ".$row[csf('fabric_construction')]." ".$row[csf('fabric_composition')];
			else if ($row[csf("item_category_id")] == 4) 
			{
				$item_desc=$lib_item[$row[csf("item_group")]];
				$item_description=$row[csf("item_description")];
				$row[csf("rate")]=number_format($row[csf("rate")],6,'.','');
			}
			else 
			{
				$item_desc=$row[csf('item_prod_id')];
				$item_id_arr[$row[csf('item_prod_id')]]=$row[csf('item_prod_id')];
			}
			
			
			$all_pi_data_arr[$row[csf("pi_id")]][$item_desc][$row[csf("rate")]]["pi_number"]=$row[csf("pi_number")];
			$all_pi_data_arr[$row[csf("pi_id")]][$item_desc][$row[csf("rate")]]["supplier_id"]=$row[csf("supplier_id")];
			$all_pi_data_arr[$row[csf("pi_id")]][$item_desc][$row[csf("rate")]]["pi_value"]+=$row[csf("pi_value")];
			$all_pi_data_arr[$row[csf("pi_id")]][$item_desc][$row[csf("rate")]]["item_desc"]=$item_desc;
			$all_pi_data_arr[$row[csf("pi_id")]][$item_desc][$row[csf("rate")]]["item_description"].=$item_description.',';
			$all_pi_data_arr[$row[csf("pi_id")]][$item_desc][$row[csf("rate")]]["pi_id"]=$row[csf("pi_id")];
			$all_pi_data_arr[$row[csf("pi_id")]][$item_desc][$row[csf("rate")]]["color"]=$color_library[$row[csf('color_id')]];
			$all_pi_data_arr[$row[csf("pi_id")]][$item_desc][$row[csf("rate")]]["goods_rcv_status"]=$row[csf("goods_rcv_status")];
			if($pi_wo_check[$row[csf("pi_id")]][$item_desc][$row[csf("rate")]][$row[csf("work_order_id")]]=="")
			{
				$pi_wo_check[$row[csf("pi_id")]][$item_desc][$row[csf("rate")]][$row[csf("work_order_id")]]=$row[csf("work_order_id")];
				$all_pi_data_arr[$row[csf("pi_id")]][$item_desc][$row[csf("rate")]]["work_order_id"].=$row[csf("work_order_id")].",";
				$jjj[$row[csf("pi_id")]][$item_desc][$row[csf("rate")]]["work_order_id"].=$row[csf("work_order_id")].",";
			}
			
		}
		//echo "<pre>";print_r($jjj);die;
		//echo $item_desc;die;

		$all_pi_ids=chop($all_pi_ids,",");
		$all_wo_ids=chop($all_wo_ids,",");

		$accep_sql=sql_select("select pi_id, import_invoice_id, current_acceptance_value, id from com_import_invoice_dtls where status_active=1 and is_deleted=0 and pi_id in($pi_ids)");
		$$cumulative_array=array();
		foreach($accep_sql as $row)
		{
			$cumulative_array[$row[csf("pi_id")]]+=$row[csf("current_acceptance_value")];
		}


		$construction_arr=array(); $composition_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
		$data_array=sql_select($sql_deter);
		if(count($data_array)>0)
		{
			foreach( $data_array as $row )
			{
				$construction_arr[$row[csf('id')]]=$row[csf('construction')];

				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
			}
		}
		//echo '<pre>';print_r($construction_arr);

		$item_des_data=array();
		if(count($item_id_arr)>0)
		{
			$item_des_data=return_library_array("select id, item_description from product_details_master where id in(".implode(",",$item_id_arr).")","id", "item_description");
		}
		
		$rcv_pi_sql="select a.id as mst_id, a.booking_id, a.receive_basis, a.recv_number, a.recv_number_prefix_num, a.receive_date, a.challan_no, b.order_uom, b.order_rate, sum(b.order_qnty) as rcv_qnty, sum(b.order_amount) as rcv_amount, sum(b.cons_amount) as rcv_amount_tk, c.item_category_id, c.item_group_id, c.yarn_count_id, c.yarn_type, c.yarn_comp_type1st, c.color, c.detarmination_id, c.item_description, c.id as item_prod_id, c.entry_form 
		from com_import_invoice_dtls_mrr p, inv_receive_master a, inv_transaction b, product_details_master c 
		where p.mrr_id=a.id and a.id=b.mst_id and b.prod_id=c.id and p.import_invoice_id=$data and a.receive_basis=1 and b.receive_basis =1 and b.transaction_type=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.payment_over_recv=0
		group by a.id, a.booking_id, a.receive_basis, a.recv_number_prefix_num, a.recv_number, a.receive_date, a.challan_no, b.order_uom, b.order_rate, c.item_category_id, c.item_group_id, c.yarn_count_id, c.yarn_type, c.yarn_comp_type1st, c.color, c.detarmination_id, c.item_description, c.id, c.entry_form
		union all
		select a.id as mst_id, a.booking_id, a.receive_basis, a.recv_number, a.recv_number_prefix_num, a.receive_date, a.challan_no, b.order_uom, b.order_rate, sum(b.order_qnty) as rcv_qnty, sum(b.order_amount) as rcv_amount, sum(b.cons_amount) as rcv_amount_tk, c.item_category_id, c.item_group_id, c.yarn_count_id, c.yarn_type, c.yarn_comp_type1st, c.color, c.detarmination_id, c.item_description, c.id as item_prod_id, c.entry_form
		from com_import_invoice_dtls_mrr p, inv_receive_master a, inv_transaction b, product_details_master c 
		where p.mrr_id=a.id and a.id=b.mst_id and b.prod_id=c.id and p.import_invoice_id=$data and a.receive_basis=2 and b.receive_basis =2 and b.transaction_type=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.payment_over_recv=0
		group by a.id, a.booking_id, a.receive_basis, a.recv_number, a.recv_number_prefix_num, a.receive_date, a.challan_no, b.order_uom, b.order_rate, c.item_category_id, c.item_group_id, c.yarn_count_id, c.yarn_type, c.yarn_comp_type1st, c.color, c.detarmination_id, c.item_description, c.id, c.entry_form";
		//echo $rcv_pi_sql;
		
		$rcv_pi_sql_result=sql_select($rcv_pi_sql);
		$receive_data_arr=array();
		foreach ($rcv_pi_sql_result as $row) 
		{
			if ($row[csf("item_category_id")] == 1){
				$item_des=$count_arr[$row[csf('yarn_count_id')]].' '.$composition[$row[csf('yarn_comp_type1st')]].'% '.$yarn_type[$row[csf('yarn_type')]].' '.$color_library[$row[csf('color')]];
			}
			else if ($row[csf("item_category_id")] == 2){
				$item_des=$construction_arr[$row[csf('detarmination_id')]].' '.$composition_arr[$row[csf('detarmination_id')]];
			}
			else if ($row[csf("item_category_id")] == 3){
				$item_des=$row[csf('color')].' '.$construction_arr[$row[csf('detarmination_id')]].' '.$composition_arr[$row[csf('detarmination_id')]];
			}
			else if ($row[csf("item_category_id")] == 4 && $row[csf("entry_form")] == 24){
				$item_des=$lib_item[$row[csf("item_group_id")]];
				$row[csf("order_rate")]=number_format($row[csf("order_rate")],6,'.','');
			}
			else
			{
				$item_des=$row[csf("item_prod_id")];
			}

			$receive_data_arr[$row[csf("booking_id")]][$item_des][$row[csf("order_rate")]][$row[csf("receive_basis")]]['receive_date'].=change_date_format($row[csf("receive_date")]).',';
			$receive_data_arr[$row[csf("booking_id")]][$item_des][$row[csf("order_rate")]][$row[csf("receive_basis")]]['challan_no'].=$row[csf("challan_no")].',';
			$receive_data_arr[$row[csf("booking_id")]][$item_des][$row[csf("order_rate")]][$row[csf("receive_basis")]]['recv_number_prefix_num'].=$row[csf("recv_number_prefix_num")].',';
			$receive_data_arr[$row[csf("booking_id")]][$item_des][$row[csf("order_rate")]][$row[csf("receive_basis")]]['order_uom'].=$unit_of_measurement[$row[csf("order_uom")]].',';
			$receive_data_arr[$row[csf("booking_id")]][$item_des][$row[csf("order_rate")]][$row[csf("receive_basis")]]['order_rate'].=$row[csf("order_rate")].',';
			$receive_data_arr[$row[csf("booking_id")]][$item_des][$row[csf("order_rate")]][$row[csf("receive_basis")]]['rcv_qnty']+=$row[csf("rcv_qnty")];
			$receive_data_arr[$row[csf("booking_id")]][$item_des][$row[csf("order_rate")]][$row[csf("receive_basis")]]['rcv_amount']+=$row[csf("rcv_amount")];
			$receive_data_arr[$row[csf("booking_id")]][$item_des][$row[csf("order_rate")]][$row[csf("receive_basis")]]['rcv_amount_tk']+=$row[csf("rcv_amount_tk")];
			$test_datsss[$row[csf("booking_id")]][$item_des][$row[csf("order_rate")]][$row[csf("receive_basis")]]+=$row[csf("rcv_amount")];
		}
		//echo '<pre>';print_r($receive_data_arr);//die;;
	}


	?>
    <style>
		.trborders
		{
			border:1px solid;
			padding-left:2px;
		}
		.wrd_brk{word-break: break-all;}
		.center{text-align: center;}
		.right{text-align: right;}
	</style>
    <table id="" cellspacing="0" cellpadding="0" width="950" border="0">
        <tr style="margin-bottom:20px;">
            <td colspan="4" align="center" style="font-size:18px; font-weight:bold">FORWARDING FOR BTB Non LC ACCEPTANCE</td>
        </tr>
        <tr>
        	<td colspan="4">&nbsp;</td>
        </tr>
        <!-- <tr>
            <td colspan="2" align="center" style="font-size:16px; font-weight:bold">BTB L/C INFORMATION</td>
            <td colspan="2" align="center" style="font-size:14px; font-weight:bold">MASTER LC/SC INFORMATION</td>
        </tr> -->
		<tr>
			<td colspan="4" height="20"></td>
		</tr>
        <tr>
            <td width="150" >Company Name</td>
            <td width="300" ><? $company_name=return_field_value("company_name","lib_company","id=$importer_id","company_name"); echo $company_name;?></td>
            <td width="150" >Master LC/SC No</td>
            <td ><? echo $lc_sc_no; ?></td>
        </tr>
        <tr>
            <td valign="top">Supplier Name</td>
            <td ><? $supplier_name=return_field_value("supplier_name","lib_supplier","id=$supplier_id","supplier_name"); echo $supplier_name;?></td>
            <td valign="top" >LC/SC date</td>
            <td valign="top" ><? if($lc_sc_date!="" && $lc_sc_date!="0000-00-00") echo change_date_format($lc_sc_date); ?></td>
        </tr>
		<tr>
			<td colspan="4" height="20"></td>
		</tr>
	</table>
	<table cellspacing="0" cellpadding="0" width="950" border="0">
        <tr>
            <td class="trborders" width="180">BTB LC No</td>
            <td class="trborders" width="150"><? echo $btb_lc_no;?></td>
            <td class="trborders" width="150">Invoice No</td>
            <td class="trborders" width="150"><? echo $invoice_no; ?></td>
            <td class="trborders" width="150">Acceptance date</td>
            <td class="trborders"><? echo change_date_format($company_acc_date); ?></td>
        </tr>
        <tr>
            <td class="trborders">Bank Acc. Date</td>
            <td class="trborders"><? echo change_date_format($bank_acc_date); ?></td>
            <td class="trborders">Bank Ref</td>
            <td class="trborders" ><? echo $bank_ref;?></td>
        </tr>
        <tr>
            <td class="trborders">Exchange rate</td>
            <td class="trborders"><? echo $exchange_rate;?></td>
            <td class="trborders">LC/SC expire date</td>
            <td class="trborders"><? if($lc_sc_expire_date!="" && $lc_sc_expire_date!="0000-00-00") echo change_date_format($lc_sc_expire_date); ?></td>
            <td class="trborders">Last Ship Date</td>
            <td class="trborders"><? if($lc_sc_last_ship_date!="" && $lc_sc_last_ship_date!="0000-00-00") echo change_date_format($lc_sc_last_ship_date); ?></td>
        </tr>
		<tr>
			<td colspan="6" height="20"></td>
		</tr>
        <tr>
            <td class="trborders">Pay Terms</td>
            <td class="trborders"><? echo $pay_term[$payterm_id];?></td>
            <td ></td>
            <td ></td>
        </tr>
        <tr>
            <td class="trborders">BTB LC Date</td>
            <td class="trborders"><? if($btb_lc_date!="" && $btb_lc_date!="0000-00-00") echo change_date_format($btb_lc_date); ?></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="trborders">LC/SC Value</td>
            <td class="trborders"><? echo number_format($lc_sc_value,2);?></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="trborders">BTB LC Value <? echo '['.$currency[$currency_id].']';?></td>
            <td class="trborders"><? echo number_format($btb_lc_value,2);?></td>
            <td class="trborders">Pi Value</td>
            <td class="trborders"><? echo number_format($pi_amount,2);?></td>
            <td class="trborders">Balance</td>
            <td class="trborders"><? echo number_format($btb_lc_value-$pi_amount,2);?></td>
        </tr>
        <tr>
            <td class="trborders">BTB of Accep. Value <? echo '['.$currency[$currency_id].']';?></td>
            <td class="trborders"><? echo number_format($current_acceptance_value,2);?></td>
            <td class="trborders">Total Acp Value</td>
            <td class="trborders"><? echo number_format($current_acceptance_value,2);?></td>
            <td class="trborders">Balance</td>
            <td class="trborders"><? echo number_format($pi_amount-$current_acceptance_value,2);?></td>
        </tr>
        <tr>
            <td class="trborders">Material Category</td>
            <td class="trborders"><? echo $all_cat;?></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="4">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="4">&#10003;&nbsp;Docs Need advance acceptance</td>
        </tr>
        <tr>
            <td colspan="4">&#10003;&nbsp;We here By confirm that we have received all goods, against the aforesaid documents, which are 'OK' in terms of quality and quantity. We also confirm that the referred goods have been properly reflected in the stock register maintained by store after completion of required formalities and we have no claim against such</td>
        </tr>
    </table>
	<table id="" cellspacing="0" cellpadding="0" width="1300" border="1">
	<thead>
		<tr bgcolor="#ddd">
			<th width="60">Buyer Name</th>
			<th width="80">Supplier Name</th>
			<th width="80">PI Number</th>
			<th width="80">Item Name</th>
			<th width="80">Item Description</th>
			<th width="80">Color Name</th>
			<th width="60">Challan No</th>
			<th width="80">MRR No</th>
			<th width="85">Receive Date</th>
			<th width="60">UOM</th>
			<th width="80">Receive Qty</th>
			<th width="70">Rate</th>
			<th width="90">Amount <?echo $currency[$currency_id];?></th>
			<th width="90">Amount BDT</th>
			<th width="80">PI Value</th>
			<th width="80">BTB L/C No</th>
			<th width="80">BTB L/C Value</th>
			<th >BTB Acceptance Value</th>
		</tr>
	</thead>
	<?
	
	foreach ($all_pi_data_arr as $pi_id => $pi_data) 
	{
		foreach ($pi_data as $item_desc => $rate_data)  
		{
			foreach ($rate_data as $item_rate => $row) 
			{
				$rcv_qnty=$rcv_amount=0;
				$rcv_amount_tk=0;
				
				$rcv_qnty=$rcv_amount=$rcv_amount_tk=$order_rate=0;
				$challan_no=$order_uom=$recv_number_prefix_num=$receive_date="";
				if ($row['goods_rcv_status'] == 1) 
				{
					$receive_basis = 2;
					$wo_id_arr=explode(",",chop($row['work_order_id'],","));
					foreach($wo_id_arr as $wo_id)
					{
						if($wo_id)
						{
							$challan_no.=rtrim($receive_data_arr[$wo_id][$item_desc][$item_rate][$receive_basis]['challan_no'],',').',';
							$order_uom.=rtrim($receive_data_arr[$wo_id][$item_desc][$item_rate][$receive_basis]['order_uom'],',').',';
							$recv_number_prefix_num.=rtrim($receive_data_arr[$wo_id][$item_desc][$item_rate][$receive_basis]['recv_number_prefix_num'],',').',';
							$receive_date.=rtrim($receive_data_arr[$wo_id][$item_desc][$item_rate][$receive_basis]['receive_date'],',').',';
							$order_rate=implode(',',array_unique(explode(',', rtrim($receive_data_arr[$wo_id][$item_desc][$item_rate][$receive_basis]['order_rate'],','))));
							$rcv_qnty+=$receive_data_arr[$wo_id][$item_desc][$item_rate][$receive_basis]['rcv_qnty'];				
							$rcv_amount+=$receive_data_arr[$wo_id][$item_desc][$item_rate][$receive_basis]['rcv_amount'];				
							$rcv_amount_tk+=$receive_data_arr[$wo_id][$item_desc][$item_rate][$receive_basis]['rcv_amount_tk'];				
						}
					}			
				}	
				else 
				{
					$receive_basis = 1;
					$challan_no.=rtrim($receive_data_arr[$row['pi_id']][$item_desc][$item_rate][$receive_basis]['challan_no'],',').',';
					$order_uom.=rtrim($receive_data_arr[$row['pi_id']][$item_desc][$item_rate][$receive_basis]['order_uom'],',').',';
					$recv_number_prefix_num.=rtrim($receive_data_arr[$row['pi_id']][$item_desc][$item_rate][$receive_basis]['recv_number_prefix_num'],',').',';
					$receive_date.=rtrim($receive_data_arr[$row['pi_id']][$item_desc][$item_rate][$receive_basis]['receive_date'],',').',';
					$order_rate=implode(',',array_unique(explode(',', rtrim($receive_data_arr[$row['pi_id']][$item_desc][$item_rate][$receive_basis]['order_rate'],','))));
					$rcv_qnty=$receive_data_arr[$row['pi_id']][$item_desc][$item_rate][$receive_basis]['rcv_qnty'];
					$rcv_amount=$receive_data_arr[$row['pi_id']][$item_desc][$item_rate][$receive_basis]['rcv_amount'];
					$rcv_amount_tk=$receive_data_arr[$row['pi_id']][$item_desc][$item_rate][$receive_basis]['rcv_amount_tk'];
				}
				$item_description=implode(',',array_unique( explode(',', rtrim($row["item_description"],','))));
				$challan_no_all=implode(',',array_unique( explode(',', rtrim($challan_no,','))));
				$order_uom_all=implode(',',array_unique( explode(',', rtrim($order_uom,','))));
				$recv_number_prefix_num_all=implode(',',array_unique( explode(',', rtrim($recv_number_prefix_num,','))));
				$receive_date_all=implode(',',array_unique( explode(',', rtrim($receive_date,','))));
				if(count($item_des_data)>0) $item_desc=$item_description=$item_des_data[$item_desc];
				
				?>
				<tbody>
					<tr>
						<td width="60" class="wrd_brk"><? echo $lib_buyer[$btb_lc_arr[$pi_id]["buyer_name"]];  ?></td>
						<td width="80" class="wrd_brk"><? echo $lib_supplier[$row["supplier_id"]]; ?></td>
						<td width="80" class="wrd_brk"><? echo $row["pi_number"]; ?></td>
						<td width="80" class="wrd_brk"><? echo $item_desc; ?></td>
						<td width="80" class="wrd_brk"><? echo $item_description; ?></td>
						<td width="80" class="wrd_brk"><? echo $row["color"]; ?></td>
						<td width="60" class="wrd_brk"><? echo $challan_no_all; ?></td>
						<td width="80" class="wrd_brk"><? echo $recv_number_prefix_num_all; ?></td>
						<td width="85" class="wrd_brk"><? echo $receive_date_all; ?></td>
						<td width="60" class="wrd_brk"><? echo $order_uom_all; ?></td>
						<td width="80" class="wrd_brk right"><? echo number_format($rcv_qnty,2); ?></td>
						<td width="70" class="wrd_brk right"><? echo number_format($rcv_amount/$rcv_qnty,4);//number_format($order_rate,4); ?></td>
						<td width="90" class="wrd_brk right"><? echo number_format($rcv_amount,2); $tot_rcv_amount += $rcv_amount; ?></td>
						<td width="90" class="wrd_brk right"><? echo number_format($rcv_amount_tk,2); $tot_rcv_amount_tk += $rcv_amount_tk; ?></td>
						<td width="80" class="wrd_brk right"><? echo number_format($row["pi_value"],2); ?></td>
						<td width="80" class="wrd_brk"><? echo $btb_lc_arr[$pi_id]['btb_lc_no']; ?></td>
						<td width="80" class="wrd_brk right"><? echo $btb_lc_arr[$pi_id]['btb_lc_value']; ?></td>
						<td class="wrd_brk right"><? echo $cumulative_array[$pi_id]; ?></td>
					</tr>
				</tbody>
				<?
			}
		}	
	}	
	?>
	<tfoot>
		<tr bgcolor="#ddd">
			<th width="60"></th>
			<th width="80"></th>
			<th width="80"></th>
			<th width="80"></th>
			<th width="80"></th>
			<th width="80"></th>
			<th width="60"></th>
			<th width="80"></th>
			<th width="85"></th>
			<th width="60"></th>
			<th width="80"></th>
			<th width="60">Total</th>
			<th width="80" class="wrd_brk right" ><? echo number_format($tot_rcv_amount,2); ?></th>
			<th width="80" class="wrd_brk right" ><? echo number_format($tot_rcv_amount_tk,2); ?></th>
			<th width="80"></th>
			<th width="80"></th>
			<th width="80"></th>
			<th ></th>
		</tr>
	</tfoot>
	</table>	
	<br>	
	<?
	
	$rcv_sql="SELECT a.id, b.id as RCV_ID, b.recv_number as RECV_NUMBER, c.prod_id as PROD_ID, c.order_amount as RCV_AMOUNT, c.CONS_AMOUNT as RCV_AMOUNT_BDT 
	from com_import_invoice_dtls_mrr a, inv_receive_master b, inv_transaction c 
	where a.import_invoice_id=$data and a.mrr_id=b.id and b.id=c.mst_id and c.transaction_type=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 ";
	// echo $rcv_sql;
	$rcv_result=sql_select($rcv_sql);
	$rcv_arr=array();
	foreach($rcv_result as $row)
	{
		$rcv_arr[$row['RCV_ID']][$row['PROD_ID']]['rcv_amount']=$row['RCV_AMOUNT'];
		$rcv_arr[$row['RCV_ID']][$row['PROD_ID']]['RCV_AMOUNT_BDT']=$row['RCV_AMOUNT_BDT'];
		$rcv_arr[$row['RCV_ID']][$row['PROD_ID']]['recv_number']=$row['RECV_NUMBER'];
	}
	$rtn_sql="SELECT a.id, a.mrr_id as MRR_ID, b.issue_number as RTN_NUMBER, b.issue_date as RTN_DATE, b.challan_no as CHALLAN_NO, c.prod_id as PROD_ID, c.cons_uom as RTN_UOM, c.cons_quantity as RTN_QNTY, c.cons_rate as RTN_RATE, c.cons_amount as RTN_AMOUNT from com_import_invoice_dtls_mrr a, inv_issue_master b, inv_transaction c where a.import_invoice_id=$data and a.mrr_id=b.received_id and b.id=c.mst_id and c.transaction_type=3 and a.status_active=1 and b.status_active=1 and c.status_active=1";
	// echo $rtn_sql;
	$rtn_result=sql_select($rtn_sql);
	if(count($rtn_result)>0)
	{
		?>					
			<table cellspacing="0" cellpadding="0" width="930" border="1">
				<thead>
					<tr bgcolor="#ddd">
						<th colspan="8">Return Information</th>
						<th rowspan="2">Net Value</th>
					</tr>
					<tr bgcolor="#ddd">
						<th width="140">Ret. MRR Number</th>
						<th width="80">MRR Date</th>
						<th width="80">MRR Qty</th>
						<th width="80">UOM</th>
						<th width="100">Challan No</th>
						<th width="80">Ret. Rate</th>
						<th width="120">Receive MRR No</th>
						<th width="140">Ret. Value (Taka/$)</th>
					</tr>
				</thead>
				<?
				foreach ($rtn_result as $row) 
				{	
					?>
					<tbody>
						<tr>
							<td class="wrd_brk"><? echo $row['RTN_NUMBER'];  ?></td>
							<td class="wrd_brk center"><? echo $row['RTN_DATE']; ?></td>
							<td class="wrd_brk right"><? echo $row['RTN_QNTY'];$total_rtn_qnty+=$row['RTN_QNTY']; ?></td>
							<td class="wrd_brk center"><? echo $unit_of_measurement[$row['RTN_UOM']]; ?></td>
							<td class="wrd_brk"><? echo $row['CHALLAN_NO']; ?></td>
							<td class="wrd_brk right"><? echo $row['RTN_RATE']; ?></td>
							<td class="wrd_brk"><? echo $rcv_arr[$row['MRR_ID']][$row['PROD_ID']]['recv_number']; ?></td>
							<td class="wrd_brk right"><? echo number_format($row['RTN_AMOUNT'],2);$total_rtn_amount+=$row['RTN_AMOUNT']; ?> </td>
							<td class="wrd_brk right">
								<? 
									$rtn_balance_val=$rcv_arr[$row['MRR_ID']][$row['PROD_ID']]['RCV_AMOUNT_BDT']-$row['RTN_AMOUNT'];
									$total_rtn_balance_val+=$rtn_balance_val;
									echo number_format($rtn_balance_val,2); 
								?>
							</td>
						</tr>
					</tbody>
					<?
				}
				?>
				<tfoot>
					<tr bgcolor="#ddd">
						<th ></th>
						<th >Total</th>
						<th class="wrd_brk right"><? echo number_format($total_rtn_qnty,2); ?></th>
						<th ></th>
						<th ></th>
						<th ></th>
						<th ></th>
						<th class="wrd_brk right"><? echo number_format($total_rtn_amount,2); ?></th>
						<th class="wrd_brk right"><? echo number_format($total_rtn_balance_val,2); ?></th>
					</tr>
				</tfoot>
			</table>
		<?
	}

	exit();
}


if($action=="open_pi_item_details")
{
	echo load_html_head_contents("PKG Item Details", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo "test";die;
	?>
    <script>
	
	function fn_calculate_total()
	{
		var ddd={ dec_type:2, comma:0, currency:''}
		var numRow = $('table#pkz_table tbody tr').length;
		math_operation( "total_pkz_qnty", "pkzQnty_", "+", numRow,ddd );
	}
	
	function fn_close()
	{
		var numRow = $('table#pkz_table tbody tr').length;
		var data_string="";
		for(var i=1; i<=numRow; i++)
		{
			if($("#pkzQnty_"+i).val()*1>0)
			{
				if(data_string!="") data_string+="__";
				data_string+=$("#hdnPiId_"+i).val()+"_"+$("#hdnPiDtlsId_"+i).val()+"_"+$("#pkzQnty_"+i).val();
				
			}
		}
		//alert(data_string);
		$("#dtls_data_string").val(data_string);
		parent.emailwindow.hide();
	}
	</script>
    <?
	$pi_wo_data=return_library_array("select b.id, c.wo_number from COM_PI_ITEM_DETAILS b, wo_non_order_info_mst c where b.work_order_id=c.id and b.pi_id in($all_pi_ids) and b.status_active=1 and b.is_deleted=0","id","wo_number");
	$pi_sql="select b.ID, b.PI_ID, a.PI_NUMBER, b.ITEM_DESCRIPTION, b.QUANTITY, b.ITEM_CATEGORY_ID, b.COUNT_NAME, b.YARN_COMPOSITION_ITEM1, b.YARN_TYPE
	from COM_PI_MASTER_DETAILS a, COM_PI_ITEM_DETAILS b  
	where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.PI_ID in($all_pi_ids)
	order by b.PI_ID, b.ID";
	//echo $pi_sql;//die;
	$pi_sql_result=sql_select($pi_sql);
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count WHERE status_active = 1 AND is_deleted = 0 ORDER BY yarn_count",'id','yarn_count');
	
	if(count($pi_sql_result)>0)
	{
		?>
        <div align="center" style="width:700px;">
           <table cellpadding="0" cellspacing="0" width="700" class="rpt_table" border="1" rules="all" id="pkz_table">
                <thead>
                    <th width="50">SL</th>
                    <th width="110">WO Number</th>
                    <th width="110">PI Number</th>
                    <th width="230">Description</th>
                    <th width="100">PI Qty</th>
					<th>Package Qty</th>
                </thead>
                <tbody>
                	<?
					$i=1;
					$pkg_quantity_breakdown_arr=explode("__",$pkg_quantity_breakdown);
					$pkg_data=array();
					foreach($pkg_quantity_breakdown_arr as $pkz_val)
					{
						$pkz_val_arr=explode("_",$pkz_val);
						$pkg_data[$pkz_val_arr[0]][$pkz_val_arr[1]]=$pkz_val_arr[2];
					}
					$tot_pkg_qnty=0;
					
					foreach($pi_sql_result as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF";
						else $bgcolor="#FFFFFF";
						if($row["ITEM_CATEGORY_ID"]==1)
						{
							$description= $count_arr[$row['COUNT_NAME']]." ".$composition[$row['YARN_COMPOSITION_ITEM1']]." ".$yarn_type[$row['YARN_TYPE']];
						}
						else
						{
							$description=$row["ITEM_DESCRIPTION"];
						}
						
						?>
                        <tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor;?>')" id="tr_<?= $i;?>" style="text-decoration:none; cursor:pointer">
                            <td align="center"><?= $i; ?></td>	
                            <td><p><? echo $pi_wo_data[$row["ID"]]; ?>&nbsp;</p></td>
                            <td><p><? echo $row["PI_NUMBER"]; ?>&nbsp;</p></td>
                            <td><p><? echo $description; ?>&nbsp;</p></td>
                            <td align="right"><? echo number_format($row["QUANTITY"],2); ?></td>	
                            <td align="center">
                            <input type="text" id="pkzQnty_<?= $i; ?>" name="pkzQnty[]" value="<?= $pkg_data[$row['PI_ID']][$row['ID']]; ?>" onKeyUp="fn_calculate_total();" class="text_boxes_numeric" style="width:80px" />
                            <input type="hidden" id="hdnPiId_<?= $i; ?>" name="hdnPiId[]" value="<?= $row['PI_ID'];?>" />
                            <input type="hidden" id="hdnPiDtlsId_<?= $i; ?>" name="hdnPiDtlsId[]" value="<?= $row['ID'];?>" />
                            </td>
                        </tr>
                        <?
						$i++;
						$tot_pkg_qnty+=$pkg_data[$row['PI_ID']][$row['ID']];
					}
					?>
                </tbody>
                <tfoot>
                	<th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Total:</th>
                    <th align="right">
                    <input type="text" id="total_pkz_qnty" name="total_pkz_qnty"  class="text_boxes_numeric" style="width:90px" value="<?= $tot_pkg_qnty;?>" readonly />
                    <input type="hidden" id="dtls_data_string" name="dtls_data_string"  />
                    </th>
                </tfoot>
            </table>
        </div>
        <div align="center" style="width:700px;">
        <table cellpadding="0" cellspacing="0" width="680" class="rpt_table" border="1" rules="all">
        	<tr>
            	<td height="50" valign="middle" align="center" class="button_container"><input type="button" id="btb_close" style="width:100px;" class="formbutton" value="Close" onClick="fn_close();" /></td>
            </tr>
        </table>
        </div>
        <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
        <?
	}
	exit();
}

?> 