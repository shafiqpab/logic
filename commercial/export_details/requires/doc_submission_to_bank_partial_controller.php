<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action']; 
 
if($action=="print_button_variable_setting")
{
	//echo "format_id","lib_report_template","template_name ='".$data."' and module_id=5 and report_id=68 and is_deleted=0 and status_active=1"; die;
	
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=5 and report_id=68 and is_deleted=0 and status_active=1");
	//print_r($print_report_format);
   	$printButton=explode(',',$print_report_format);
	foreach($printButton as $id){
		if($id==233)$buttonHtml.='<input id="btn_print_letter" class="formbutton_disabled printReport" type="button" style="width:80px" onclick="print_to_html_report(1)" name="btn_print_letter" value="Print letter">';
		
		if($id==234)$buttonHtml.='<input type="button" style="width:80px;" id="btn_print_letter2"  onClick="print_to_html_report(2)"   class="formbutton_disabled printReport" name="btn_print_letter2" value="Print letter 2" />';
		
		if($id==237)$buttonHtml.='<input type="button" style="width:80px;" id="btn_print_letter3"  onClick="print_to_html_report(3)"   class="formbutton_disabled printReport" name="btn_print_letter3" value="Bill of Exchange" />';	
		if($id==240)$buttonHtml.='<input type="button" style="width:80px;" id="btn_print_letter4"  onClick="print_to_html_report(4)"   class="formbutton_disabled printReport" name="btn_print_letter4" value="Print letter 3" />';	
		if($id==137)$buttonHtml.='<input type="button" style="width:80px;" id="btn_print_letter5"  onClick="print_to_html_report(5)"   class="formbutton_disabled printReport" name="btn_print_letter5" value="Print letter 4" />';	
		if($id==78)$buttonHtml.='<input type="button" style="width:80px;" id="btn_print_letter6"  onClick="print_to_html_report(6)"   class="formbutton_disabled printReport" name="btn_print_letter6" value="Print" />';
		if($id==737)$buttonHtml.='<input type="button" style="width:200px;" id="btn_print_letter7"  onClick="print_to_html_report(7)"   class="formbutton_disabled printReport" name="btn_print_letter7" value="Bank Forwarding and Bill of Exchange" />';
		if($id==129)$buttonHtml.='<input type="button" style="width:80px;" id="btn_print_letter8"  onClick="print_to_html_report(8)"   class="formbutton_disabled printReport" name="btn_print_letter8" value="Print 5" />';
	}
   echo "document.getElementById('button_data_panel').innerHTML = '".$buttonHtml."';\n";
   exit();
} 

if($action=="commercial_head_popup")
{
	echo load_html_head_contents("Doc. Submission to Bank", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(id,fld_val)
		{
			//alert(id+"="+fld_val);
			$('#hdn_head_id').val(id);
			$('#hdn_head_val').val(fld_val);
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:360px;">
        <table cellpadding="0" cellspacing="0" width="360" class="rpt_table">
            <thead>
                <th width="50">SL</th>
                <th>Account Head
                <input type="hidden" id="hdn_head_id" />
                <input type="hidden" id="hdn_head_val" />
                </th>
                
            </thead>
		</table>
        <div style="width:360px; max-height:350px; overflow-y:scroll">
     	<table width="340" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="list_view">
        	<tbody>
            <?
            $i=1;
			foreach($commercial_head as $key=>$val)
			{
				if ($i%2==0)
                    $bgcolor="#FFFFFF";
                else
                    $bgcolor="#E9F3FF";
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value(<? echo $key.",'".$val."'"; ?>);" >
                    <td width="50" align="center"><?= $i++; ?></td>
                    <td><? echo $val; ?></td>
                </tr>
                <?
			}
			?>
            </tbody>
        </table>
        </div>
    </div>
    </body>
    <!--<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>-->
    <script>setFilterGrid('list_view',-1)</script>
    </html>
    <?
	exit();
}

$lcscRes=sql_select("select id, export_lc_no, currency_name, 0 as pay_term from com_export_lc");
foreach($lcscRes as $row)
{
	$lcscRes_arr[$row[csf('id')]]['export_lc_no']=$row[csf('export_lc_no')];
	$lcscRes_arr[$row[csf('id')]]['currency_name']=$row[csf('currency_name')];
	$lcscRes_arr[$row[csf('id')]]['pay_term']=$row[csf('pay_term')];
}

 $ScRes=sql_select("select id, contract_no, currency_name,pay_term from com_sales_contract");
 foreach($ScRes as $row)
 {
	 $ScRes_arr[$row[csf('id')]]['contract_no']=$row[csf('contract_no')];
	 $ScRes_arr[$row[csf('id')]]['currency_name']=$row[csf('currency_name')];
	 $ScRes_arr[$row[csf('id')]]['pay_term']=$row[csf('pay_term')];
 }
 
//--------------------------- Start-------------------------------------//

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond group by buy.id, buy.buyer_name group by buy.id,buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  
	
}

if($action=="populate_acc_loan_no_data")
{
	$data=explode("**",$data);
	$acc_type=$data[0];
	$rowID=$data[1];
	$company_id=$data[2];
	$lein_bank=$data[3];
	$sql_cond="";
	if($company_id>0) $sql_cond=" and company_id=$company_id";
	if($lein_bank>0) $sql_cond.=" and account_id=$lein_bank";
	$sql="select account_no from lib_bank_account where account_type=$acc_type $sql_cond";
	$nameArray=sql_select($sql);
	echo "$('#txt_ac_loan_no_".$rowID."').removeAttr('readonly');\n";
 	echo "$('#txt_ac_loan_no_".$rowID."').val('');\n";
	foreach($nameArray as $row)
	{
		echo "$('#txt_ac_loan_no_".$rowID."').attr('readonly','readonly');\n";
		echo "$('#txt_ac_loan_no_".$rowID."').val('".$row[csf("account_no")]."');\n";
	}
	exit();
}

if($action=="transaction_add_row")
{
	$rowNo = $data+1;
	//create_drop_down( "cbo_account_head_".$rowNo, 200, $commercial_head,"", 1, "-- Select --", $selected, "get_php_form_data(this.value+'**'+".$rowNo."+'**'+document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_lien_bank').value, 'populate_acc_loan_no_data', 'requires/doc_submission_to_bank_partial_controller' );",0,"" )
	echo '<tr id="tr'.$rowNo.'">
			<td>
			<input type="text" name="cbo_account_head[]" id="cbo_account_head_'.$rowNo.'" class="text_boxes" style="width:140px;" onFocus="add_auto_complete( '.$rowNo.' )"  onBlur="fn_value_check('.$rowNo.',this.value,\'cbo_account_head\')" onDblClick="fn_commercial_head_display('.$rowNo.',\'cbo_account_head\')" placeholder="Browse Or Write" />
			</td>							
			<td><input type="text" id="txt_negotiation_dtls_date_'.$rowNo.'" name="txt_negotiation_dtls_date[]" class="datepicker" style="width:110px" /></td>

			<td><input type="text" id="txt_ac_loan_no_'.$rowNo.'" name="txt_ac_loan_no[]" class="text_boxes" style="width:100px" /></td>
			<td><input type="text" id="txt_loan_no_'.$rowNo.'" name="txt_loan_no[]" class="text_boxes" style="width:100px" /></td>
			<td><input type="text" id="txt_domestic_curr_'.$rowNo.'" name="txt_domestic_curr[]" class="text_boxes_numeric" style="width:100px" onkeyup="fn_calculate(this.id,'.$rowNo.')" /></td>
			<td><input type="text" id="txt_conversion_rate_'.$rowNo.'" name="txt_conversion_rate[]" class="text_boxes_numeric" style="width:100px" onkeyup="fn_calculate(this.id,'.$rowNo.')" /></td>
			<td><input type="text" id="txt_lcsc_currency_'.$rowNo.'" name="txt_lcsc_currency[]" class="text_boxes_numeric" style="width:100px" onkeyup="fn_calculate(this.id,'.$rowNo.')" /></td>
			<td>
			<input type="button" id="increaserow_'.$rowNo.'" style="width:25px" class="formbuttonplasminus" value="+" onClick="javascript:fn_inc_decr_row('.$rowNo.',\'increase\');" /><input type="button" id="decreaserow_'.$rowNo.'" style="width:25px" class="formbuttonplasminus" value="-" onClick="javascript:fn_inc_decr_row('.$rowNo.',\'decrease\');" />
			</td> 
		</tr>'; 
	 
	exit();
}

if($action=="lcSc_popup_search")
{
	echo load_html_head_contents("Export Information Entry Form", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$mst_tbl_id=str_replace("'","",$mst_tbl_id);
	if($mst_tbl_id=="") $mst_tbl_id=0;
	//echo $mst_tbl_id.test;die;
	if($mst_tbl_id)
	{
		if($db_type==0)
		{
			$sql_prev_inv=sql_select("select group_concat(invoice_id) as inv_id, max(is_lc) as is_lc from com_export_doc_submission_invo where doc_submission_mst_id=".$mst_tbl_id." and status_active=1 and is_deleted=0");
			$invoice_id_all=$sql_prev_inv[0][csf("inv_id")];
			$prev_is_lc=$sql_prev_inv[0][csf("is_lc")];
		}
		else if($db_type==2)
		{
			$sql_prev_inv=sql_select("select LISTAGG(invoice_id, ',') WITHIN GROUP (ORDER BY invoice_id) as inv_id, max(is_lc) as is_lc from com_export_doc_submission_invo where doc_submission_mst_id=".$mst_tbl_id." and status_active=1 and is_deleted=0");
			//print_r($sql_prev_inv);die;
			foreach($sql_prev_inv as $row)
			{
				$invoice_id_all=$row[csf("inv_id")];
				$prev_is_lc=$row[csf("is_lc")];
			}
		}
	}
	
	
	$invoice_id_all=implode(",",array_unique(explode(",",$invoice_id_all)));
	//echo $invoice_id_all.jahid;die;
	/*echo "select listagg(cast(invoice_id as varchar(4000)), ',') within group(order by b.invoice_id) as inv_id from com_export_doc_submission_invo where doc_submission_mst_id=".$mst_tbl_id;die;
	echo $invoice_id_all.jahid;die;*/
	
	?>
	<script>
	
	
	function set_all()
	{
		var old=document.getElementById('old_data_row_color').value;
		if(old!="")
		{ 
			old=old.split(",");
			for(var i=0; i<old.length; i++)
			{ 
				js_set_value( old[i] ) ;
				//toggle( document.getElementById( 'search' + i ), '#FFFFCC' );
			}
		}
	}
	
	
	var selected_id = new Array;
	var selected_id_dtls = new Array;
	var selected_import_btb = new Array;
	var selected_name = new Array;
	var currencyArr = new Array; //for check currency mix
	var buyerArr = new Array; //for check buyer mix
	var lcScArr = new Array; //for check lc sc mix
	
	
	function check_all_data() 
	{
		//alert('system');
		var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
		tbl_row_count = tbl_row_count - 1;

		for( var i = 1; i <= tbl_row_count; i++ ) 
		{
			var currency = $('#hidden_currency'+i).val();
			var buyer = $('#hidden_buyer'+i).val();
			var lcsc = $('#hidden_lc_sc'+i).val();
			//alert(lcsc);
			if(lcScArr.length==0)
			{
				lcScArr.push( lcsc );
			}
			else if( jQuery.inArray( lcsc, lcScArr )==-1 &&  lcScArr.length>0)
			{
				alert("LC or SC Mixed is Not Allow");
				return;
			}
			js_set_value( i );
		}
	}
	
	function toggle( x, origColor ) {
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}
	
	function js_set_value(str) 
	{ 
		var invoiceID = $('#hidden_invoice_id'+str).val();
		var subdtlsid = $('#hidden_sub_dtls_id'+str).val();
		var currency = $('#hidden_currency'+str).val();
		var buyer = $('#hidden_buyer'+str).val();
		var lcsc = $('#hidden_lc_sc'+str).val();
		var import_btb = $('#hidden_import_btb'+str).val();
		
		//ls sc mix check-------------------------------//
		if(lcScArr.length==0)
		{
			lcScArr.push( lcsc );
		}
		else if( jQuery.inArray( lcsc, lcScArr )==-1 &&  lcScArr.length>0)
		{
			alert("LC or SC Mixed is Not Allow");
			return;
		}
		 
		//currency mix check-------------------------------//
		if(currencyArr.length==0)
		{
			currencyArr.push( currency );
		}
		else if( jQuery.inArray( currency, currencyArr )==-1 &&  currencyArr.length>0)
		{
			alert("Currency Mixed is Not Allow");
			return;
		}
		
		//buyer mix check--------------------------------//
		if(buyerArr.length==0)
		{
			buyerArr.push( buyer );
			//alert(buyer);
		}
		else if( jQuery.inArray( buyer, buyerArr )==-1 &&  buyerArr.length>0)
		{
			alert("Buyer Mixed is Not Allow");
			return;
		}
					
		toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
		
		if( jQuery.inArray( invoiceID, selected_id ) == -1 ) {
			selected_id.push( invoiceID );
			selected_id_dtls.push( subdtlsid );
			selected_import_btb.push( import_btb );
		}
		else 
		{
			for( var i = 0; i < selected_id.length; i++ ) 
			{
				if( selected_id[i] == invoiceID ) break;
			}
			selected_id.splice( i, 1 );
			selected_id_dtls.splice( i, 1 );
			selected_import_btb.splice( i, 1 );
			lcScArr.length=0;
			buyerArr.length=0;
		}
		var id =''; var id_dtls = ''; var import_btb_id = '';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			import_btb_id += selected_import_btb[i] + ',';
			if(selected_id_dtls[i]>0)
			{
				id_dtls += selected_id_dtls[i] + ',';
			}
		}
		id 		= id.substr( 0, id.length - 1 );
		import_btb_id = import_btb_id.substr( 0, import_btb_id.length - 1 );
		if(id_dtls!=""&& id_dtls !=null) id_dtls= id_dtls.substr( 0, id_dtls.length - 1 );
		//alert(import_btb_id);
		import_btb_id = unique__(import_btb_id.split(","));

		$('#all_invoice_id').val( id );	
		//$('#all_sub_dtls_id').val( id_dtls );			
		$('#import_btb_id').val( import_btb_id );	
		$('#hedden_lc_sc').val( lcsc );	 		
 	}
	function unique__(array){
		return array.filter(function(el, index, arr) {
		return index == arr.indexOf(el);
		});
	}
    </script>

	</head>

	<body>
	<div align="center" style="width:940px;">
	<form name="searchexportinformationfrm"  id="searchexportinformationfrm" onKeyPress="return event.keyCode != 13;">
		<fieldset style="width:930px;">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="930" class="rpt_table">
                <thead>
                    <th>Company</th>
                    <th>Buyer</th>
                    <th>LC For</th>
                    <th>Search By</th>
                    <th>Lc Number</th>
                    <th>Invoice No.</th>
                    <th>Inv. Date Range</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:80px" class="formbutton" />
                        <input type="hidden" name="hidden_lcSc_id" id="hidden_lcSc_id" value="" />
                        <input type="hidden" name="is_lcSc" id="is_lcSc" value="" />
                    </th>
                </thead>
                <tr class="general">
                    <td>
                        <?
                            echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--- Select Company ---", $companyID, "",1 );
                        ?>                        
                    </td>
                    <td>
                    	<?
                    		echo create_drop_down( "cbo_buyer_name", 110, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b,lib_buyer_party_type c where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' $buyer_cond  and c.buyer_id=buy.id and c.party_type in (1,3,21,90) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); 
                    		//echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' $buyer_cond group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); // old
						?>
                    </td>
                    <td>
						<? echo create_drop_down( "cbo_lc_for", 80, $lc_for_arr,"", 0, "", 1, "" ); ?>
                    </td>
                    <td> 
                        <?
						//function create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name )
                            $arr=array(1=>'From Invoice',2=>'From Submission To Buyer');
                            //echo create_drop_down( "cbo_buyer_sub", 180, $arr,"", 0, "--- Select ---", 0, "" );
							echo create_drop_down( "cbo_buyer_sub", 120,$arr,"", 0, "", 1, "",0 );
                        ?> 
                    </td>
                    <td>
                        <input type="text" style="width:80px" class="text_boxes"  name="txt_lc_no" id="txt_lc_no" />
                    </td>						
                    <td>
                        <input type="text" style="width:80px" class="text_boxes"  name="txt_invoice_no" id="txt_invoice_no" />
                    </td>      
                    <td>
                        <input type="text" style="width:60px" class="datepicker"  name="txt_date_from" id="txt_date_from" />
                        <input type="text" style="width:60px" class="datepicker"  name="txt_date_to" id="txt_date_to" />
                    </td>

                     <td>
                        <input type="button" name="btn_show"  class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_buyer_sub').value+'**'+document.getElementById('txt_lc_no').value+'**'+document.getElementById('txt_invoice_no').value+'**'+'<? echo $invoice_id_all; ?>'+'**'+'<? echo $mst_tbl_id; ?>'+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_lc_for').value, 'lcSc_search_list_view', 'search_divs', 'doc_submission_to_bank_partial_controller', 'setFilterGrid(\'tbl_list_search\',-1)');set_all();" style="width:80px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="8" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
            <div style="width:100%; margin-top:10px" id="search_divs" align="left"></div>
		</fieldset>
	</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"> </script>
	</html>
	<?
	exit(); 
 
}

if($action=="lcSc_search_list_view")
{
	$data=explode('**',$data); 	
	$company_id=$data[0];
	$buyer_id=$data[1];
	$search_by_buy_sub=$data[2];
	$search_lc_sc=trim($data[3]);
	$search_invoice=trim($data[4]);
	$invoiceAllid =$data[5];
	$invoiceArr = explode(",",$data[5]);
	/*echo $data[5]."<br>";
	print_r($invoiceArr);die;*/
	$mst_tbl_id = $data[6];
	$start_date = $data[7];
	$end_date = $data[8];
	$lc_for = $data[9];

	if(trim($search_invoice) != "")
	{
		$invoice_no_cond = " and m.invoice_no like '%".trim($search_invoice)."%'";
	}

	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond=" and m.invoice_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond=" and m.invoice_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}


	if(empty($invoiceAllid)) $invoiceAllid=0;
	
	if($mst_tbl_id=="") $mst_tbl_id=0;
	
	if($buyer_id!=0) $buyer_con="and m.buyer_id=$buyer_id"; else $buyer_con;
	if($company_id==0){ echo "Please Select Company first"; die; }
	//echo $search_lc_sc.'system';
	
	if($search_by_buy_sub==2)
	{
		/*if($search_invoice != "")
		{
			//$sql = "SELECT m.is_lc,m.id,invoice_no,lc_sc_id,net_invo_value,m.buyer_id,short_name FROM com_export_invoice_ship_mst m, lib_buyer b, com_export_doc_submission_invo c  WHERE m.status_active=1 and m.is_deleted=0 $buyer_con and benificiary_id=$company_id and invoice_no like '%$search_invoice%' and b.id=m.buyer_id  and m.id=c.invoice_id and c.is_converted=0 and m.id NOT IN (select invoice_id from com_export_doc_submission_invo where doc_submission_mst_id != '$mst_tbl_id' and status_active=1 and is_deleted=0)";
			
			$sql = "SELECT m.is_lc,m.id,m.invoice_no,m.invoice_date,m.lc_sc_id,m.net_invo_value,m.buyer_id,b.short_name,c.id as sub_dtls_id,c.doc_submission_mst_id as sub_mst_id, 1 as type FROM com_export_invoice_ship_mst m, lib_buyer b, com_export_doc_submission_invo c, com_export_doc_submission_mst d  WHERE m.status_active=1 and m.is_deleted=0 and b.id=m.buyer_id and m.id=c.invoice_id and d.id=c.doc_submission_mst_id and m.benificiary_id=$company_id and m.invoice_no like '%$search_invoice%' and d.entry_form=39 and c.is_converted=0 $buyer_con $date_cond
			union all
			SELECT m.is_lc,m.id,m.invoice_no,m.invoice_date,m.lc_sc_id,m.net_invo_value,m.buyer_id,b.short_name,c.id as sub_dtls_id,c.doc_submission_mst_id as sub_mst_id, 2 as type FROM com_export_invoice_ship_mst m, lib_buyer b , com_export_doc_submission_invo c , com_export_doc_submission_mst d  WHERE m.status_active=1  and m.is_deleted=0 $buyer_con  and m.benificiary_id=$company_id and b.id=m.buyer_id and m.id=c.invoice_id and d.id=c.doc_submission_mst_id and d.entry_form=39  and c.is_converted=1 $date_cond  and m.id in(select invoice_id from com_export_doc_submission_invo where doc_submission_mst_id ='$mst_tbl_id' and status_active=1 and is_deleted=0)";
			
		}
		else*/ 
		$lc_for_cond="";
		if($lc_for) $lc_for_cond=" and m.lc_for=$lc_for";
		if ($search_lc_sc != "")
		{
			$sql_ext="";
			if(str_replace("'","",$mst_tbl_id)>0) $sql_ext="union all
			SELECT m.is_lc, m.id, m.invoice_no, m.invoice_date, m.lc_sc_id, m.invoice_value, m.net_invo_value, m.buyer_id, b.short_name, c.id as sub_dtls_id, c.doc_submission_mst_id as sub_mst_id, 2 as type 
			FROM com_export_invoice_ship_mst m, lib_buyer b, com_export_doc_submission_invo c, com_export_doc_submission_mst d  
			WHERE m.status_active=1 and m.is_deleted=0 $buyer_con and m.benificiary_id=$company_id and b.id=m.buyer_id $lc_for_cond and m.id=c.invoice_id and d.id=c.doc_submission_mst_id and d.entry_form=39 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.is_converted=1 and m.id in(select invoice_id from com_export_doc_submission_invo where doc_submission_mst_id ='$mst_tbl_id' and status_active=1 and is_deleted=0)"; 
			
			$sql = "SELECT m.is_lc, m.id, m.invoice_no, m.invoice_date, m.lc_sc_id, m.invoice_value, m.net_invo_value, m.buyer_id, b.short_name, c.id as sub_dtls_id, c.doc_submission_mst_id as sub_mst_id, 1 as type 
			FROM com_export_invoice_ship_mst m, lib_buyer b, com_export_doc_submission_invo c, com_export_doc_submission_mst d 
			WHERE m.status_active=1 and m.is_deleted=0 and b.id=m.buyer_id and m.id=c.invoice_id and d.id=c.doc_submission_mst_id and d.entry_form=39 and c.is_converted=0 and m.benificiary_id=$company_id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $date_cond $lc_for_cond $invoice_no_cond and ( m.lc_sc_id in (select id from com_sales_contract where contract_no like '%$search_lc_sc%' and status_active=1 and is_deleted=0) or m.lc_sc_id in(select id from com_export_lc where export_lc_no like '%$search_lc_sc%' and status_active=1 and is_deleted=0) ) $buyer_con
			$sql_ext
			order by invoice_date desc";
			
		}
		else
		{
			$sql_ext="";			
			if(str_replace("'","",$mst_tbl_id)>0) $sql_ext="union all
			SELECT m.is_lc, m.id, m.invoice_no, m.invoice_date, m.lc_sc_id, m.invoice_value, m.net_invo_value, m.buyer_id, b.short_name, c.id as sub_dtls_id, c.doc_submission_mst_id as sub_mst_id, 2 as type 
			FROM com_export_invoice_ship_mst m, lib_buyer b, com_export_doc_submission_invo c, com_export_doc_submission_mst d  
			WHERE m.status_active=1 and m.is_deleted=0 and b.id=m.buyer_id and m.id=c.invoice_id and d.id=c.doc_submission_mst_id and d.entry_form=39 $lc_for_cond and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.is_converted=1 and m.id in(select invoice_id from com_export_doc_submission_invo where doc_submission_mst_id ='$mst_tbl_id' and status_active=1 and is_deleted=0)";
			
			$sql = "SELECT m.is_lc,m.id,m.invoice_no,m.invoice_date,m.lc_sc_id,m.invoice_value,m.net_invo_value,m.buyer_id,b.short_name,c.id as sub_dtls_id,c.doc_submission_mst_id as sub_mst_id, 1 as type FROM com_export_invoice_ship_mst m, lib_buyer b , com_export_doc_submission_invo c, com_export_doc_submission_mst d  
			WHERE m.status_active=1 and m.is_deleted=0 and b.id=m.buyer_id and m.id=c.invoice_id and d.id=c.doc_submission_mst_id and d.entry_form=39 $lc_for_cond and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.is_converted=0 and benificiary_id=$company_id  $buyer_con $date_cond $invoice_no_cond
			$sql_ext
			order by invoice_date desc";
		}
		
		//echo $sql;
	}
	else
	{
		if ($search_lc_sc != "")
		{ 
			$sql_ext="";
			if(str_replace("'","",$mst_tbl_id)>0) $sql_ext="";
			$sql = "SELECT m.is_lc, m.id, m.invoice_no, m.invoice_date, m.lc_sc_id, m.invoice_value, m.net_invo_value, m.buyer_id, b.short_name, m.import_btb
			FROM com_export_invoice_ship_mst m, lib_buyer b 
			WHERE m.status_active=1 and m.is_deleted=0 $buyer_con $date_cond $invoice_no_cond and benificiary_id=$company_id and m.lc_sc_id in(select id from com_export_lc where export_lc_no like '%$search_lc_sc%' and status_active=1 and is_deleted=0) and b.id=m.buyer_id and m.is_lc=1 and m.import_btb <> 1
			UNION ALL 
			SELECT m.is_lc, m.id, m.invoice_no, m.invoice_date, m.lc_sc_id, m.invoice_value, m.net_invo_value, m.buyer_id, b.company_short_name as short_name, m.import_btb 
			FROM com_export_invoice_ship_mst m, lib_company b  
			WHERE m.status_active=1 and m.is_deleted=0 $buyer_con $date_cond $invoice_no_cond and benificiary_id=$company_id and m.lc_sc_id in(select id from com_export_lc where export_lc_no like '%$search_lc_sc%' and status_active=1 and is_deleted=0) and b.id=m.buyer_id and m.is_lc=1 and m.import_btb = 1
			order by invoice_date desc";
		}
		else
		{
			$sql = "SELECT m.is_lc, m.id, m.invoice_no, m.invoice_date, m.lc_sc_id, m.invoice_value, m.net_invo_value, m.buyer_id, b.short_name, m.import_btb 
			FROM com_export_invoice_ship_mst m, lib_buyer b  
			WHERE m.status_active=1 and m.is_deleted=0 $buyer_con $date_cond $invoice_no_cond and benificiary_id=$company_id and b.id=m.buyer_id and m.is_lc=1 and m.import_btb <> 1
			UNION ALL 
			SELECT m.is_lc, m.id, m.invoice_no, m.invoice_date, m.lc_sc_id, m.invoice_value, m.net_invo_value, m.buyer_id, b.company_short_name as short_name, m.import_btb 
			FROM com_export_invoice_ship_mst m, lib_company b  
			WHERE m.status_active=1 and m.is_deleted=0 $buyer_con $date_cond $invoice_no_cond and benificiary_id=$company_id and b.id=m.buyer_id and m.is_lc=1 and m.import_btb = 1
			order by invoice_date desc";
		}
	}
	
	$sub_inv_cond="";
	if($mst_tbl_id>0) $sub_inv_cond=" and p.doc_submission_mst_id !='$mst_tbl_id' ";
	$sub_invoice_sql="select p.invoice_id from com_export_doc_submission_invo p, com_export_doc_submission_mst q where p.doc_submission_mst_id=q.id and p.status_active=1 and p.is_deleted=0 and q.entry_form=40 and q.company_id = $company_id  $sub_inv_cond";
	$sub_invoice_result=sql_select($sub_invoice_sql);
	$sub_invoice_data=array();
	foreach($sub_invoice_result as $row)
	{
		$sub_invoice_data[$row[csf("invoice_id")]]=$row[csf("invoice_id")];
	}
	unset($sub_invoice_result);
	//echo $sql;//die;	
		 
	?>
		<div style=" width:930px;">
            <table  width="910" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">                    
                <thead>
                    <th width="40">SL</th>
                    <th width="180">Invoice No</th>
                    <th width="100">Invoice Date</th>
                    <th width="180">LC/SC No</th>
                    <th width="100">Buyer</th>
                    <th width="100">Currency</th>
                    <th width="100">Gross Invoice Value</th>
                    <th >Net Invoice Value</th>
                </thead> 
           </table>
       </div>             
       <div style="width:930px; overflow-y:scroll; max-height:200px" id="scroll_body">                
       		<table class="rpt_table" width="910" cellpadding="0" cellspacing="0" id="tbl_list_search" border="1" rules="all"  >
			<?
                $i=1; $oldDataRow=$pay_term_cond="";
				//echo $sql;
                $nameArray=sql_select($sql);
                foreach($nameArray as $row)
                {  
                    if ($i%2==0)  $bgcolor="#E9F3FF";
                    else $bgcolor="#FFFFFF";   
                    
                    if($row[csf("is_lc")]==1)
					{
						$lc_sc_no = $lcscRes_arr[$row[csf('lc_sc_id')]]['export_lc_no']; 
                    	$currency_name = $lcscRes_arr[$row[csf('lc_sc_id')]]['currency_name'];
						$pay_term_cond = $lcscRes_arr[$row[csf('lc_sc_id')]]['pay_term'];
					}
					else 
					{
						$lc_sc_no = $ScRes_arr[$row[csf('lc_sc_id')]]['contract_no'];  
                    	$currency_name = $ScRes_arr[$row[csf('lc_sc_id')]]['currency_name'];		
						$pay_term_cond =  $ScRes_arr[$row[csf('lc_sc_id')]]['pay_term'];
 					}
					 
					//var_dump($lcscRes);
					if($pay_term_cond!=3 && $sub_invoice_data[$row[csf("id")]]=="")	// pay term 3 means cash in advanced sales contact not come in document submission form		
					{
						//old data row arrange here------
						if( in_array($row[csf("id")], $invoiceArr) )
						{
							if($oldDataRow=="") $oldDataRow = $i; else $oldDataRow .= ",".$i;
						}
						?>   	
                        <tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $i;?>)" > 
                            <td width="40"><? echo $i;?> 
                                <input type="hidden" id="hidden_invoice_id<? echo $i; ?>" value="<? echo $row[csf("id")];?>" />
                                <input type="hidden" id="hidden_invoice_no<? echo $i; ?>" value="<? echo $row[csf("invoice_no")];?>" />
                                <input type="hidden" id="hidden_sub_dtls_id<? echo $i; ?>" value="<? echo $row[csf("sub_dtls_id")];?>" />
                                <input type="hidden" id="hidden_import_btb<? echo $i; ?>" value="<? echo $row[csf("import_btb")];?>" />                                     
                            </td>		                   
                            <td width="180"><p><? echo $row[csf("invoice_no")];?></p></td>
                            <td width="100"><p><? echo change_date_format($row[csf('invoice_date')]);?></p></td>
                            <td width="180"><p><? echo $lc_sc_no;?>&nbsp;</p><input type="hidden" id="hidden_lc_sc<? echo $i; ?>" value="<? echo $lc_sc_no; ?>" /> </td>
                            
                            <td width="100"><p><? echo $row[csf("short_name")]; ?></p><input type="hidden" id="hidden_buyer<? echo $i; ?>" value="<? echo $row[csf("buyer_id")]; ?>" /></td>
                            <td width="100">
                                <p><? echo $currency[$currency_name]; ?>&nbsp;</p>
                                <input type="hidden" id="hidden_currency<? echo $i; ?>" value="<? echo $currency_name; ?>" />
                            </td>
                            <td width="100" align="right"><p><? echo $row[csf("invoice_value")];?>&nbsp;</p></td>
                            <td align="right"><p><? echo $row[csf("net_invo_value")];?>&nbsp;</p></td>
                        </tr>         
						<?           
						$i++; 
					}//pay term if cond end
					
                } //foeach end
             ?>		
             		
			</table>
            <input type="hidden" name="old_data_row_color" id="old_data_row_color" value="<? echo $oldDataRow; ?>"/>
                </div>
                <div style="width:50%; float:left" align="left">
                            <input type="hidden"  id="all_invoice_id" value="" />
                            <input type="hidden"  id="all_sub_dtls_id" value="" />
                            <input type="hidden"  id="all_invoice_no" value="" /> 
                            <input type="hidden"  id="import_btb_id" value="" />
                            <input type="hidden"  id="hedden_lc_sc" value="" /> 
                            <input type="checkbox" name="check_all_lc" id="check_all_lc" onClick="check_all_data()" value="0" />&nbsp;&nbsp;Check All
                </div>
                <div style="width:40%; float:left" align="left">
                            <input type="submit" class="formbutton" id="close" style="width:80px" onClick="parent.emailwindow.hide();" value="Close" />
                </div>  
                <script src="../../../includes/functions_bottom.js" type="text/javascript"> </script>
                <br>
              
            <?        
	   exit();    
 	
}



if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$invoice_id_string=str_replace("'","",$invoice_id_string);
	$txt_import_btb=str_replace("'","",$txt_import_btb);
	
	$sql_buyer_submission=sql_select("select b.id as dtls_id, b.invoice_id  from com_export_doc_submission_mst a, com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and a.entry_form=39 and b.is_lc=2 and b.status_active=1 and b.is_deleted=0 and b.invoice_id in($invoice_id_string)");
	$buyer_submission_sub=array();$buyer_submission_inv=array();
	foreach($sql_buyer_submission as $row)
	{
		$buyer_submission_sub[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
		$buyer_submission_inv[$row[csf("invoice_id")]]=$row[csf("dtls_id")];
	}
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		
		//master table entry here START---------------------------------------//		
		$id=return_next_id("id", "com_export_doc_submission_mst_par", 1);		
 		$field_array_mst="id,submission_id,company_id,buyer_id,submit_date,entry_form,submit_to,bank_ref_no,bank_ref_date,days_to_realize,possible_reali_date,courier_receipt_no,courier_company,courier_date,bnk_to_bnk_cour_no,bnk_to_bnk_cour_dt,lien_bank,lc_currency,submit_type,pay_term,negotiation_date,total_negotiated_amount,total_lcsc_currency,remarks,import_btb,inserted_by,insert_date,issue_bank_dtls";
		$data_array_mst="(".$id.",".$txt_ref_id.",".$cbo_company_name.",".$cbo_buyer_name.",".$txt_submit_date.",40,".$cbo_submit_to.",".$txt_bank_ref.",".$txt_bank_ref_date.",".$txt_day_to_realize.",".$txt_possible_reali_date.",".$courier_receipt_no.",".$txt_courier_company.",".$txt_courier_date.",".$txt_bnk_to_bnk_cour_no.",".$txt_bnk_to_bnk_cour_date.",".$cbo_lien_bank.",".$cbo_currency.",".$cbo_submission_type.",".$cbo_pay_term.",".$txt_negotiation_date.",".$total_dom_curr_hid.",".$total_foreign_curr_hid.",".$txt_remarks.",'".$txt_import_btb."','".$user_id."','".$pc_date_time."',".$txt_issue_bank_info_dtls.")";
		
		
		
		//echo "20**".$field_array."<br>".$data_array;die;
		//master table entry here END---------------------------------------// 
	
		//transaction table entry here START---------------------------------------//		
		$trid=return_next_id("id", "com_export_doc_sub_trans", 1);		
		$field_array_trans="id,doc_submission_mst_id, doc_submission_mst_id_par,acc_head,negotiation_date,acc_loan,loan_acc_no,dom_curr,conver_rate,lc_sc_curr,inserted_by,insert_date";
		$data_array_trans=""; $tsrID=true; 
		for($i=1;$i<=$transRow;$i++)
		{ 
			if($i>1) $data_array_trans .= ",";
			$cbo_account_head 		= 'cbo_account_head_'.$i;
			$txt_negotiation_date 	= 'txt_negotiation_dtls_date_'.$i;
			$txt_ac_loan_no 		= 'txt_ac_loan_no_'.$i;
			$txt_loan_no 			= 'txt_loan_no_'.$i;
			$txt_domestic_curr 		= 'txt_domestic_curr_'.$i;
			$txt_conversion_rate 	= 'txt_conversion_rate_'.$i;
			$txt_lcsc_currency 		= 'txt_lcsc_currency_'.$i;

			$orderDeliveryDate=change_date_format(str_replace("'",'',$$txt_negotiation_date), "", "",1);
  			$data_array_trans .= "(".$trid.",".$txt_ref_id.",".$id.",'".$$cbo_account_head."','".$orderDeliveryDate."','".$$txt_ac_loan_no."','".$$txt_loan_no."','".$$txt_domestic_curr."','".$$txt_conversion_rate."','".$$txt_lcsc_currency."','".$user_id."','".$pc_date_time."')";	
			$trid=$trid+1;
		}
		
 		
		$dtlsrID=$tsrID=true;
		$rID=sql_insert("com_export_doc_submission_mst_par",$field_array_mst,$data_array_mst,0);
		// echo "20**insert into com_export_doc_sub_trans (".$field_array_trans.") values ".$data_array_trans;die;disconnect($con);
		if($data_array_trans!="")
		{
			$tsrID=sql_insert("com_export_doc_sub_trans",$field_array_trans,$data_array_trans,0);
		}
		//echo "20**".$rID." && ".$tsrID;oci_rollback($con);disconnect($con);die;
	
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $tsrID)
			{
				oci_commit($con);    
				echo "0**".$id."**".str_replace("'","",$txt_ref_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".$id."**".str_replace("'","",$txt_ref_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		
		
		if(str_replace("'","",$txt_partial_sys_id)=="") { echo "10**";disconnect($con);die; }
		
 		 
		//---------------Check Duplicate Bank Ref/ Bill No  ------------------------//
		if($db_type==0) $null_cond=" and bank_ref_no !='' "; else $null_cond=" and bank_ref_no is not null";
		$duplicate = is_duplicate_field("id"," com_export_doc_submission_mst","bank_ref_no=$txt_bank_ref and id!=$mst_tbl_id and entry_form=40 and company_id=$cbo_company_name and buyer_id=$cbo_buyer_name and status_active=1 and is_deleted=0 $null_cond"); 
		if($duplicate==1 && str_replace("'","",$txt_bank_ref)!="") 
		{			 
			echo "20**Duplicate Bank Ref. Number";disconnect($con);
			die;
		}
		
		$sub_rlz_id = return_field_value("invoice_bill_id","com_export_proceed_realization","invoice_bill_id=".$mst_tbl_id." and is_invoice_bill=1 and status_active=1 and is_deleted=0","invoice_bill_id");
		if($sub_rlz_id>0)
		{
			echo "35**Update Not Allow. This Bill No Found in Export Proceed Realization";disconnect($con); die;
		}
		
		//------------------------------Check Duplicate END---------------------------------------//
		
		
		//master table entry here START---------------------------------------//		
 		$id=str_replace("'","",$txt_partial_sys_id);
		$field_array_mst="company_id*buyer_id*submit_date*submit_to*bank_ref_no*bank_ref_date*days_to_realize*possible_reali_date*courier_receipt_no*courier_company*courier_date*bnk_to_bnk_cour_no*bnk_to_bnk_cour_dt*lien_bank*lc_currency*pay_term*submit_type*negotiation_date*total_negotiated_amount*total_lcsc_currency*remarks*import_btb*updated_by*update_date*issue_bank_dtls";
		$data_array_mst="".$cbo_company_name."*".$cbo_buyer_name."*".$txt_submit_date."*".$cbo_submit_to."*".$txt_bank_ref."*".$txt_bank_ref_date."*".$txt_day_to_realize."*".$txt_possible_reali_date."*".$courier_receipt_no."*".$txt_courier_company."*".$txt_courier_date."*".$txt_bnk_to_bnk_cour_no."*".$txt_bnk_to_bnk_cour_date."*".$cbo_lien_bank."*".$cbo_currency."*".$cbo_pay_term."*".$cbo_submission_type."*".$txt_negotiation_date."*".$total_dom_curr_hid."*".$total_foreign_curr_hid."*".$txt_remarks."*'".$txt_import_btb."'*'".$user_id."'*'".$pc_date_time."'*".$txt_issue_bank_info_dtls."";
		
		
		//echo "20**".$field_array."<br>".$data_array;die;
 		
		
		$trid=return_next_id("id", "com_export_doc_sub_trans", 1);		
		$field_array_trans="id,doc_submission_mst_id, doc_submission_mst_id_par,acc_head,negotiation_date,acc_loan,loan_acc_no,dom_curr,conver_rate,lc_sc_curr,inserted_by,insert_date";
		$data_array_trans=""; $tsrID=true; 
		for($i=1;$i<=$transRow;$i++)
		{ 
			if($i>1) $data_array_trans .= ",";
			$cbo_account_head 		= 'cbo_account_head_'.$i;
			$txt_negotiation_date 	= 'txt_negotiation_dtls_date_'.$i;
			$txt_ac_loan_no 		= 'txt_ac_loan_no_'.$i;
			$txt_loan_no 			= 'txt_loan_no_'.$i;
			$txt_domestic_curr 		= 'txt_domestic_curr_'.$i;
			$txt_conversion_rate 	= 'txt_conversion_rate_'.$i;
			$txt_lcsc_currency 		= 'txt_lcsc_currency_'.$i;

			$orderDeliveryDate=change_date_format(str_replace("'",'',$$txt_negotiation_date), "", "",1);
  			$data_array_trans .= "(".$trid.",".$txt_ref_id.",".$id.",'".$$cbo_account_head."','".$orderDeliveryDate."','".$$txt_ac_loan_no."','".$$txt_loan_no."','".$$txt_domestic_curr."','".$$txt_conversion_rate."','".$$txt_lcsc_currency."','".$user_id."','".$pc_date_time."')";	
			$trid=$trid+1;
		}
		//echo "10** insert into com_export_doc_submission_invo ($field_array_trans) values $data_array_trans";oci_rollback($con);disconnect($con);die;
		//print_r($update_sub_dtlsID_array);
		$all_buyer_sub_dtls_id=str_replace("'","",$all_buyer_sub_dtls_id);
		$rID=$deletetrans=$tsrID=true;
		$rID=sql_update("com_export_doc_submission_mst_par",$field_array_mst,$data_array_mst,"id",$txt_partial_sys_id,1);
		$deletetrans = execute_query("DELETE FROM com_export_doc_sub_trans WHERE doc_submission_mst_id_par=$txt_partial_sys_id");
		if($data_array_trans!="")
		{
			$tsrID=sql_insert("com_export_doc_sub_trans",$field_array_trans,$data_array_trans,0);
		}
		//transaction table entry here END---------------------------------------// 
		$dtlsrID=true;
	

		// $rID=sql_update("com_export_doc_submission_mst",$field_array_mst,$data_array_mst,"id",$mst_tbl_id,1);
	
		if($db_type==0)
		{
			if($rID && $deletetrans && $tsrID)
			{
				mysql_query("COMMIT");  
				echo "1**".$id."**".str_replace("'","",$txt_ref_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$id."**".str_replace("'","",$txt_ref_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $deletetrans && $tsrID)
			{
				oci_commit($con);  
				echo "1**".$id."**".str_replace("'","",$txt_ref_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".$id."**".str_replace("'","",$txt_ref_id);
			}
		}
		disconnect($con);
		die;
	}
	
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if(str_replace("'","",$txt_partial_sys_id)=="") { echo "10**";disconnect($con);die; }
 		$id=str_replace("'","",$txt_partial_sys_id);
		
		$update_field_arr="updated_by*update_date*status_active*is_deleted";
		$update_data_arr="".$user_id."*'".$pc_date_time."'*0*1";
		$upsubDtlsID=$upsubTransid=$update_buyer_convart=$upsubMst=true;
		$sub_rlz_id = return_field_value("invoice_bill_id","com_export_proceed_realization","invoice_bill_id=".$mst_tbl_id." and is_invoice_bill=1 and status_active=1 and is_deleted=0","invoice_bill_id");
		if($sub_rlz_id>0)
		{
			echo "35**Delete Not Allow. This Bill No Found in Export Proceed Realization"; disconnect($con);die;
		}
		else
		{
			if($id>0)
			{
				$upsubMst=sql_update("com_export_doc_submission_mst_par",$update_field_arr,$update_data_arr,"id",$id,1);
				$upsubTransid=sql_update("com_export_doc_sub_trans",$update_field_arr,$update_data_arr,"doc_submission_mst_id_par",$txt_partial_sys_id,1);
			}
			
			if($db_type==0)
			{
				if($upsubMst && $upsubTransid)
				{
					mysql_query("COMMIT");  
					echo "2**".$id;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$id;
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($upsubMst && $upsubTransid)
				{
					oci_commit($con);  
					echo "2**".$id;
				}
				else
				{
					oci_rollback($con);
					echo "10**".$id;
				}
			}
			disconnect($con);
			die;
		}
	}	
}

if($action=="doc_sub_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);  
	?>
	     
	<script>
		function js_set_value(mrr)
		{
	 		$("#hidden_system_number").val(mrr); // mrr number
			parent.emailwindow.hide();
		}
	</script>

	</head>

	<body>
	<div align="center" style="width:100%;" >
	<form name="searchdocfrm_1"  id="searchdocfrm_1" autocomplete="off">
	<table width="880" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>                	 
                    <th width="130">Search By</th>
                    <th width="200" align="center" id="search_by_td_up">Enter Number</th>
                    <th width="200">Buyer</th>
                    <th width="200">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr>                    
                    <td align="center">
                        <?  
                            $search_by = array(1=>'Bank Ref/Bill No',2=>'Submitted To',3=>'Submission Type',4=>'Invoice No');
							$dd="change_search_event(this.value, '0*2*2*0', '0*submited_to*submission_type*0', '../../../')";
							echo create_drop_down( "cbo_search_by", 130, $search_by, "", 0, "--Select--", 0,$dd,0 );
                        ?>
                    </td>
                    <td  align="center" id="search_by_td">				
                        <input type="text" style="width:180px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 
                    <td align="center">
                        <?  
							//echo create_drop_down( "cbo_buyer_name", 180, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Select", $buyer_name, "",0 );
                        echo create_drop_down( "cbo_buyer_name", 180, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Select", $buyer_name, "",0 );
                        ?>
                    </td>   
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                    </td> 
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company_name; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_year_selection').value, 'create_system_id_search_list_view', 'search_div', 'doc_submission_to_bank_partial_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="5">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here-->
                     <input type="hidden" id="hidden_system_number" value="" />
                    <!--end-->
                </td>
            </tr>    
            </tbody>
         </tr>         
        </table>    
        <div align="center" valign="top" id="search_div" style="margin-top:5px"> </div> 
        </form>
   </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}


if($action=="create_system_id_search_list_view")
{
	 
	$ex_data = explode("_",$data);
	$search_by = $ex_data[0];
	$search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company_name = $ex_data[4];
	$buyer_name = $ex_data[5];
	$cbo_year_selection = $ex_data[6];
	$sql_cond="";
	//echo "select id from com_export_invoice_ship_mst where invoice_no='".$search_common."'";
	if($search_by==1 && $search_common!="")
	{
		$sql_cond .= " and a.bank_ref_no like '%$search_common%'";
	}	
	else if($search_by==2 && $search_common!="")
	{
		$sql_cond .= " and a.submit_to='$search_common'";
	}
	else if($search_by==3 && $search_common!="")
	{
		$sql_cond .= " and a.submit_type='$search_common'";
	}
	else if($search_by==4 && $search_common!="")
	{
		$invoice_ids='';
	
		if($db_type==0) $id_cond="group_concat(id) as id";
		else if($db_type==2) $id_cond="LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as id";
		$invoice_cond="invoice_no like '%$search_common%'";
		//echo "select $id_cond from com_export_invoice_ship_mst where $invoice_cond";
		$invoice_ids=return_field_value("$id_cond","com_export_invoice_ship_mst","$invoice_cond","id");
		
		//if($db_type==2 && $invoice_ids!="") $invoice_ids = $invoice_ids->load();
		if ($invoice_ids!="")
		{
			$invoice_ids=explode(",",$invoice_ids);
			$invoice_idsCond=""; 
			//echo count($invoice_ids); die;
			if($db_type==2 && count($invoice_ids)>=999)
			{
				$chunk_arr=array_chunk($invoice_ids,999);
				foreach($chunk_arr as $val)
				{
					$ids=implode(",",$val);
					if($invoice_idsCond=="")
					{
						$invoice_idsCond.=" and ( b.invoice_id in ( $ids) ";
					}
					else
					{
						$invoice_idsCond.=" or  b.invoice_id in ( $ids) ";
					}
				}
				$invoice_idsCond.=")";
			}
			else
			{
				$ids=implode(",",$invoice_ids);
				$invoice_idsCond.=" and b.invoice_id in ($ids) ";
			}
		}
		else if($invoice_ids=="" && (($invoice_cond!="" && $search_by==4)))
		{
			echo "Not Found"; die;
		}
	}
	
	if($db_type==0)
	{
		if( $txt_date_from!="" && $txt_date_to!="" )
		{
			$sql_cond .= " and a.submit_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
	}
	else if($db_type==2)
	{
		if($txt_date_from!="" && $txt_date_to!="") 
		{
			$sql_cond .= " and a.submit_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
		}

		if($cbo_year_selection!="") 
		{
			$sql_cond .= " and EXTRACT(YEAR FROM a.submit_date)='$cbo_year_selection'";
		}
	}
 	
	if(trim($company_name)!="") $sql_cond .= " and company_id ='$company_name'";
	if(trim($buyer_name)!=0) $sql_cond .= " and buyer_id ='$buyer_name'";
	
	$bank_arr = return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	$lc_no_arr = return_library_array( "select id, export_lc_no from com_export_lc",'id','export_lc_no');
	$sc_no_arr = return_library_array( "select id, contract_no from com_sales_contract",'id','contract_no');
	$invoice_no_arr= return_library_array( "select id, invoice_no from com_export_invoice_ship_mst",'id','invoice_no');
	$buyer_arr= return_library_array( "select id, short_name from  lib_buyer",'id','short_name');
	$company_arr= return_library_array( "select id, company_name from  lib_company",'id','company_name');
	
		$sql ="select a.id,a.bank_ref_no,a.submit_date,a.submit_to,a.lien_bank,a.submit_type, a.buyer_id, LISTAGG(b.is_lc, ',') WITHIN GROUP (ORDER BY b.is_lc) as is_lc_string, LISTAGG(b.invoice_id, ',') WITHIN GROUP (ORDER BY b.invoice_id) as invoice_id_string, LISTAGG(b.lc_sc_id, ',') WITHIN GROUP (ORDER BY b.lc_sc_id) as lc_sc_id_string, LISTAGG(b.id, ',') WITHIN GROUP (ORDER BY b.id) as submission_dtls_id, a.is_posted_account, a.import_btb 
			from com_export_doc_submission_mst a, com_export_doc_submission_invo b
			where a.id=b.doc_submission_mst_id and a.submit_type=2 and a.status_active=1 and a.entry_form=40  $sql_cond $invoice_idsCond group by a.id,a.bank_ref_no,a.submit_date, a.submit_to, a.lien_bank, a.submit_type,a.buyer_id, a.is_posted_account, a.import_btb";
	

	$res = sql_select($sql);
    ?>
        <table border="1" width="1030" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <thead>
                <th width="30">SL</th>
                <th width="50">Submission Id</th>
                <th width="100">Bank Ref /Bill No</th>
                <th width="90">Buyer</th>
                <th width="180">LC/SC No</th>
                <th width="200">Invoice List</th>
                <th width="70">Submit Date</th>
                <th width="70">Submitted To</th>
                <th width="130">Lien Bank</th>
                <th >Submit Type</th>
            </thead>
        </table>
    <div style="width:1030px; overflow-y:scroll; max-height:230px">
        <table border="1" width="1012" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="list_view">             
   		<?                     
        	$i=1;
			foreach($res as $row)
			{  
				/*if($db_type==2) $row[csf("is_lc_string")] = $row[csf("is_lc_string")]->load();
				if($db_type==2) $row[csf("invoice_id_string")] = $row[csf("invoice_id_string")]->load();
				if($db_type==2) $row[csf("lc_sc_id_string")] = $row[csf("lc_sc_id_string")]->load();
				if($db_type==2) $row[csf("submission_dtls_id")] = $row[csf("submission_dtls_id")]->load();*/
				
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
				
				$submission_dtls_id = implode(",",array_unique(explode(",",$row[csf("submission_dtls_id")])));
				
				$expisLC = explode(",",$row[csf("is_lc_string")]);
				$expisINV = explode(",",$row[csf("lc_sc_id_string")]);
				$j=0;$lcID="";$scID="";
				foreach($expisLC as $key=>$val)
				{
					if($val==1 && $expisINV[$j]!=0)
					{
 						// export LC id
						if($lcID=="") $lcID .= $expisINV[$j]; else $lcID .=",".$expisINV[$j];
					}
					else if($expisINV[$j]!=0)
					{  
 						//Sales Contact id
						if($scID=="") $scID .= $expisINV[$j]; else $scID .=",".$expisINV[$j];
					}
					$j++;	
 				}
				
				$lc_sc_no="";
				if($lcID!="")
				{
 					// export LC number
					$poSQL=array_unique(explode(",",$lcID));
 					foreach($poSQL as $poR)
					{
						if($lc_sc_no=="") $lc_sc_no=$lc_no_arr[$poR]; else $lc_sc_no.=",".$lc_no_arr[$poR];
					} 
				}
				if($scID!="")
				{
					//Sales Contact Number
					$scSQL=array_unique(explode(",",$scID));
 					foreach($scSQL as $poR)
					{
						if($lc_sc_no=="") $lc_sc_no=$sc_no_arr[$poR]; else $lc_sc_no.=",".$sc_no_arr[$poR];
					}  		 
				}
				
				//invoice list 
				$invoiceNo="";
				if($row[csf("invoice_id_string")]!="")
				{
					$all_invoice_id=array_unique(explode(",",$row[csf("invoice_id_string")]));
					//$invoice_no_arr
					//$invoiceSQL=sql_select("select invoice_no from com_export_invoice_ship_mst where id in (".$row[csf("invoice_id_string")].")");
					foreach($all_invoice_id as $poR)
					{
						if($invoiceNo=="") $invoiceNo=$invoice_no_arr[$poR]; else $invoiceNo.=",".$invoice_no_arr[$poR];
					} 
				}
          		?>     
			   		<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf("id")];?>**<? echo $submission_dtls_id;?>**<? echo $row[csf("is_posted_account")];?>')" > 
                        <td width="30" align="center"><? echo $i;?></td>
                        <td width="60" align="center"><? echo $row[csf("id")];?></td>
                        <td width="100"><p><? echo $row[csf("bank_ref_no")]; ?></p></td>
						<? if($row[csf("import_btb")] == "1"){
							$buyer_company =$company_arr[$row[csf("buyer_id")]];
						}else{
							$buyer_company =$buyer_arr[$row[csf("buyer_id")]];
						}?>
                        <td width="90"><p><? echo $buyer_company;//$buyer_arr[$row[csf("buyer_id")]]; ?></p></td>
                        <td width="180"><p><? echo $lc_sc_no; ?></p></td>
                        <td width="200"><p><? echo $invoiceNo; ?></p></td>
                        <td width="70" align="center"><? echo change_date_format($row[csf("submit_date")]); ?></td>
                        <td width="70"><p><? echo $submited_to[$row[csf("submit_to")]]; ?></p></td>
                        <td width="130"><p><? echo $bank_arr[$row[csf("lien_bank")]]; ?></p></td>
                        <td><p><? echo $submission_type[$row[csf("submit_type")]]; ?></p></td>
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

if($action=="populate_master_from_data")
{  
	$sql = "select a.id,a.company_id, a.buyer_id, a.submit_date, a.submit_to, a.bank_ref_no, a.bank_ref_date, a.days_to_realize,a.possible_reali_date, a.courier_receipt_no, a.courier_company, a.courier_date, a.bnk_to_bnk_cour_no, a.bnk_to_bnk_cour_dt, a.lien_bank, a.lc_currency, a.pay_term,  a.submit_type, a.negotiation_date, a.remarks, listagg(cast(b.is_lc as varchar(4000)), ',') within group(order by b.is_lc) as is_lc_string, listagg(cast(b.invoice_id as varchar(4000)), ',') within group(order by b.invoice_id) as invoice_id_string, listagg(cast(b.lc_sc_id as varchar(4000)), ',') within group(order by b.lc_sc_id) as lc_sc_id_string, listagg(cast(b.id as varchar(4000)), ',') within group(order by b.id) as inv_id, a.import_btb, a.issue_bank_dtls
	from com_export_doc_submission_mst a, com_export_doc_submission_invo b
	where a.id=b.doc_submission_mst_id and a.id=$data 
	group by a.id,a.company_id, a.buyer_id,a.submit_date, a.submit_to,a.bank_ref_no, a.bank_ref_date, a.days_to_realize,a.possible_reali_date, a.courier_receipt_no,a.courier_company,a.courier_date, a.bnk_to_bnk_cour_no, a.bnk_to_bnk_cour_dt,a.lien_bank,a.lc_currency,a.pay_term,a.submit_type,a.negotiation_date,a.remarks,a.import_btb, a.issue_bank_dtls";	

	// echo $sql;
	$res = sql_select($sql);
	$i=1;$all_buyer_sub_dtls_id="";
	foreach($res as $row)
	{
		
		if($row[csf("import_btb")] == 1)
		{
			$company_arr= return_library_array( "select id, company_name from  lib_company",'id','company_name');
			echo '$("#cbo_buyer_name option[value!=\'0\']").remove();';
        	echo '$("#cbo_buyer_name").append("<option selected value=\''.$row[csf("buyer_id")].'\'>'.$company_arr[$row[csf("buyer_id")]].'</option>");'."\n";
		}else{
			echo "$('#cbo_buyer_name').val('".$row[csf("buyer_id")]."');\n";
		} 
		echo "$('#lcsc_no').attr('disabled',true);\n";
  		echo "$('#cbo_company_name').val('".$row[csf("company_id")]."');\n";
  		echo "$('#txt_ref_id').val('".$row[csf("id")]."');\n";
  		echo "$('#txt_import_btb').val('".$row[csf("import_btb")]."');\n";
		echo "$('#txt_issue_bank_info_dtls').val('".$row[csf("issue_bank_dtls")]."');\n";
		$issue_bank_ref=explode("__",$row[csf("issue_bank_dtls")]);
		echo "$('#txt_issue_bank_info').val('".$issue_bank_ref[0]."');\n";
		
		$buyer_sub_dtls=sql_select("select b.id from com_export_doc_submission_mst a, com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and a.entry_form=39 and b.is_converted=1 and  b.invoice_id in (".$row[csf("invoice_id_string")].")");
		foreach($buyer_sub_dtls as $val)
		{
			$all_buyer_sub_dtls_id.=$val[csf("id")].",";
		}
 		
		$expisLC = implode(",",array_unique(explode(",",$row[csf("is_lc_string")])));
		$expisINV = array_unique(explode(",",$row[csf("lc_sc_id_string")]));
		$all_lc_sc_id=implode(",",$expisINV);
		$j=0;$lcID="";$scID="";
		
		$lc_sc_no=""; $lc_import_btb=0;$item_category_id = 0;
		if($expisLC==1)
		{
			$poSQL=sql_select("select export_lc_no, import_btb, export_item_category, pay_term from com_export_lc where id in (".$all_lc_sc_id.")");
			foreach($poSQL as $poR)
			{
				if($lc_sc_no=="") $lc_sc_no=$poR[csf("export_lc_no")]; else $lc_sc_no.=", ".$poR[csf("export_lc_no")];
				if($lc_import_btb==0) $lc_import_btb=$poR[csf("import_btb")]; else $lc_import_btb.=",".$poR[csf("import_btb")];
				if($item_category_id==0) $item_category_id=$poR[csf("export_item_category")]; else $item_category_id.=",".$poR[csf("export_item_category")];
				$pay_term=$poR[csf("pay_term")];
				$lc_for=1;
			} 
		}
		else
		{
			$scSQL=sql_select("select contract_no, pay_term, lc_for  from com_sales_contract where id in (".$all_lc_sc_id.")");
			foreach($scSQL as $poR)
			{
				if($lc_sc_no=="") $lc_sc_no=$poR[csf("contract_no")]; else $lc_sc_no.=",".$poR[csf("contract_no")];
				$pay_term=$poR[csf("pay_term")];
				$lc_for=$poR[csf("lc_for")];
			} 
		}
		$po_numbers_arr=array();
		if($lc_for==2)
		{
			$poSQL=sql_select("select a.requisition_number as PO_NUMBER, c.id as INV_ID 
			from sample_development_mst a, com_export_invoice_ship_dtls b, com_export_invoice_ship_mst c
			 where a.id=b.po_breakdown_id and b.mst_id=c.id and c.lc_sc_id in(".$all_lc_sc_id.") and c.is_lc=$expisLC and c.lc_for=2");
			foreach($poSQL as $value)
			{
				$po_numbers_arr[$value["INV_ID"]].=$value["PO_NUMBER"].",";
			}
		}
		else if($lc_import_btb == 1 && $item_category_id == 2)
		{
			$sales_order_arr =  sql_select("select b.id as INV_ID, b.work_order_no as WORK_ORDER_NO 
			from com_export_lc_order_info a, com_export_invoice_ship_mst b, com_export_invoice_ship_dtls c 
			where a.com_export_lc_id=b.lc_sc_id and b.id=c.mst_id and b.is_lc=$expisLC and a.com_export_lc_id in($all_lc_sc_id)");
			foreach ($sales_order_arr as $value) 
			{
				$po_numbers_arr[$value["INV_ID"]].=$value["WORK_ORDER_NO"].",";
			}
		}
		else if($item_category_id == 10)
		{
			$poSQL=sql_select("select a.job_no_mst as PO_NUMBER, c.id as INV_ID from fabric_sales_order_dtls a, com_export_invoice_ship_dtls b, com_export_invoice_ship_mst c
			where a.id=b.po_breakdown_id and b.mst_id=c.id and c.lc_sc_id in(".$all_lc_sc_id.") and c.is_lc=$expisLC");
			foreach($poSQL as $value)
			{
				$po_numbers_arr[$value["INV_ID"]].=$value["PO_NUMBER"].",";
			}
		}
		else if($item_category_id==23 || $item_category_id==35 || $item_category_id==36 || $item_category_id==37 || $item_category_id==38 || $item_category_id==67)
		{
			$poSQL=sql_select("select a.order_no as PO_NUMBER, c.id as INV_ID from subcon_ord_dtls a, com_export_invoice_ship_dtls b, com_export_invoice_ship_mst c
			where a.id=b.po_breakdown_id and b.mst_id=c.id and c.lc_sc_id in(".$all_lc_sc_id.") and c.is_lc=$expisLC");
			foreach($poSQL as $value)
			{
				$po_numbers_arr[$value["INV_ID"]].=$value["PO_NUMBER"].",";
			}
		}
		else
		{
			//list view data arrange-----------------// 
			$poSQL=sql_select("select a.po_number as PO_NUMBER, c.id as INV_ID from wo_po_break_down a, com_export_invoice_ship_dtls b, com_export_invoice_ship_mst c
			 where a.id=b.po_breakdown_id and b.mst_id=c.id and c.lc_sc_id in(".$all_lc_sc_id.") and c.is_lc=$expisLC");
			foreach($poSQL as $value)
			{
				$po_numbers_arr[$value["INV_ID"]].=$value["PO_NUMBER"].",";
			}
		}
		
		echo "$('#cbo_pay_term').val('".$row[csf("pay_term")]."');\n";
		echo "$('#lcsc_no').val('".$lc_sc_no."');\n";  
		echo "$('#lc_sc_id').val('".$all_lc_sc_id."');\n"; 
		
		if($row[csf("submit_date")]!='0000-00-00' || $row[csf("submit_date")]!='')
		{		 
 			echo "$('#txt_submit_date').val('".change_date_format($row[csf("submit_date")])."');\n";
		}
		else
		{
			echo "$('#txt_submit_date').val('');\n";
		}
		echo "$('#cbo_submit_to').val('".$row[csf("submit_to")]."');\n";
		echo "$('#txt_bank_ref').val('".$row[csf("bank_ref_no")]."');\n";
		if($row[csf("bank_ref_date")]=='0000-00-00' || $row[csf("bank_ref_date")]=='')
		{
			echo "$('#txt_bank_ref_date').val('');\n";
		}
		else
		{
			echo "$('#txt_bank_ref_date').val('".change_date_format($row[csf("bank_ref_date")])."');\n";
		}
		echo "$('#cbo_submission_type').val('".$row[csf("submit_type")]."');\n";

		if($row[csf("submit_type")]==1)
		{
		 echo "$('#txt_negotiation_date').attr('disabled',true);\n";
		 echo "$('#cbo_submission_type').attr('disabled',false);\n";
		}
		else
		{
			echo "$('#cbo_submission_type').attr('disabled',true);\n";
			if($row[csf("negotiation_date")]!='0000-00-00' && $row[csf("negotiation_date")]!='')
			{
				echo "$('#txt_negotiation_date').attr('disabled',false);\n";
				echo "$('#txt_negotiation_date').val('".change_date_format($row[csf("negotiation_date")])."');\n";
			}
			else
			{
				echo "$('#txt_negotiation_date').val('');\n";
			}
		}
		echo "$('#txt_day_to_realize').val('".$row[csf("days_to_realize")]."');\n";
		if($row[csf("possible_reali_date")]=='0000-00-00' || $row[csf("possible_reali_date")]=='')
		{
			echo "$('#txt_possible_reali_date').val('');\n";
		}
		else
		{
			echo "$('#txt_possible_reali_date').val('".change_date_format($row[csf("possible_reali_date")])."');\n";
		}

		echo "$('#courier_receipt_no').val('".$row[csf("courier_receipt_no")]."');\n";
		echo "$('#txt_courier_company').val('".$row[csf("courier_company")]."');\n";
		if($row[csf("courier_date")]=='0000-00-00' || $row[csf("courier_date")]=='')
		{
			echo "$('#txt_courier_date').val('');\n";
		}
		else
		{
			echo "$('#txt_courier_date').val('".change_date_format($row[csf("courier_date")])."');\n";
		}
		echo "$('#txt_bnk_to_bnk_cour_no').val('".$row[csf("bnk_to_bnk_cour_no")]."');\n";
		if($row[csf("bnk_to_bnk_cour_dt")]=='0000-00-00' || $row[csf("bnk_to_bnk_cour_dt")]=='')
		{
			echo "$('#txt_bnk_to_bnk_cour_date').val('');\n";
		}
		else
		{
			echo "$('#txt_bnk_to_bnk_cour_date').val('".change_date_format($row[csf("bnk_to_bnk_cour_dt")])."');\n";
		}
		echo "$('#cbo_lien_bank').val('".$row[csf("lien_bank")]."');\n";
		echo "$('#cbo_currency').val('".$row[csf("lc_currency")]."');\n";
		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		
		echo "$('#mst_tbl_id').val('".$row[csf("id")]."');\n";
		echo "$('#invoice_tbl_id').val('".$row[csf("inv_id")]."');\n";
		
		//invoice list view -----------------------------------------------------------------------//
		//start -----------------------------------------------------------------------//
		$invSQL="select sum(a.net_invo_value) as net_invo_value  from com_export_doc_submission_invo a, com_export_invoice_ship_mst b where a.status_active=1 and a.is_deleted=0 and a.invoice_id=b.id and a.doc_submission_mst_id=".$row[csf("id")]."";
 		// echo $invSQL;die;
		$resArray=sql_select($invSQL);
		$tot_invoice_val=0;$invoice_id="";
 		foreach($resArray as $invRow)
		{ 
			echo "$('#txt_bill_value').val('".$invRow[csf("net_invo_value")]."');\n";
			echo "$('#txt_bill_value').attr('disabled',true);\n";
		}
		 
		
		//transaction list generate here Start------------------------------------//
		$invtrns =  "select sum(a.LC_SC_CURR) as LC_SC_CURR from com_export_doc_sub_trans a, com_export_doc_submission_mst b where a.doc_submission_mst_id=b.id and a.doc_submission_mst_id=".$row[csf("id")]." and a.status_active=1 and a.is_deleted=0";
		
		$trans_arr=sql_select($invtrns);
		foreach($trans_arr as $row){

			echo "$('#txt_Cuml_Neg_Amount').val('".$row[csf("lc_sc_curr")]."');\n";
		    echo "$('#txt_Cuml_Neg_Amount').attr('disabled',true);\n";
		}

		echo "calculateBalance();\n";

		//list view for transaction area----------------------------------------//
 		echo "$('#transaction_container').find('tr').remove();\n";         
		echo "$('#transaction_container').html( '".$transaction_tr."')".";\n";	
		echo "sum_of_currency();\n";	

		//transaction list generate here End------------------------------------//
		echo "fn_negotiation();\n";
		//transaction list generate here End------------------------------------//
   	}//main foreach end $all_buyer_sub_dtls_id$invoice_id

	$all_buyer_sub_dtls_id=chop($all_buyer_sub_dtls_id," , ");
	$invoice_id=chop($invoice_id," , ");
	echo "$('#invoice_id_string').val('".$invoice_id."');\n"; 
	echo "$('#all_buyer_sub_dtls_id').val('".$all_buyer_sub_dtls_id."');\n";	
	exit();	
}

if($action=="doc_sub_popup_par")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);  
	?>
	     
	<script>
		function js_set_value(mrr)
		{
	 		$("#hidden_system_number").val(mrr); // mrr number
			parent.emailwindow.hide();
		}
	</script>

	</head>

	<body>
	<div align="center" style="width:100%;" >
	<form name="searchdocfrm_1"  id="searchdocfrm_1" autocomplete="off">
	<table width="880" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>                	 
                    <th width="130">Search By</th>
                    <th width="120" align="center" id="search_by_td_up">Enter Number</th>
                    <th width="200">Buyer</th>
                    <th width="100">System Num</th>
                    <th width="200">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr class="general">                    
                    <td align="center">
                        <?  
                            $search_by = array(1=>'Bank Ref/Bill No',2=>'Submitted To',3=>'Submission Type',4=>'Invoice No');
							$dd="change_search_event(this.value, '0*2*2*0', '0*submited_to*submission_type*0', '../../../')";
							echo create_drop_down( "cbo_search_by", 130, $search_by, "", 0, "--Select--", 0,$dd,0 );
                        ?>
                    </td>
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:100px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 
                    <td align="center">
                        <?  
                        echo create_drop_down( "cbo_buyer_name", 180, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Select", $buyer_name, "",0 );
                        ?>
                    </td>
                    <td>				
                        <input type="text" style="width:90px" class="text_boxes"  name="txt_sys_no" id="txt_sys_no" />	
                    </td>   
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                    </td> 
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company_name; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_sys_no').value, 'create_system_id_search_list_view_par', 'search_div', 'doc_submission_to_bank_partial_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="6">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here-->
                     <input type="hidden" id="hidden_system_number" value="" />
                    <!--end-->
                </td>
            </tr>    
            </tbody>
         </tr>         
        </table>    
        <div align="center" valign="top" id="search_div" style="margin-top:5px"> </div> 
        </form>
   </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}


if($action=="create_system_id_search_list_view_par")
{
	 
	$ex_data = explode("_",$data);
	$search_by = $ex_data[0];
	$search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company_name = $ex_data[4];
	$buyer_name = $ex_data[5];
	$cbo_year_selection = $ex_data[6];
	$sys_num_par = trim($ex_data[7]);
	$sql_cond="";
	//echo "select id from com_export_invoice_ship_mst where invoice_no='".$search_common."'";
	if($search_by==1 && $search_common!="")
	{
		$sql_cond .= " and a.bank_ref_no like '%$search_common%'";
	}	
	else if($search_by==2 && $search_common!="")
	{
		$sql_cond .= " and a.submit_to='$search_common'";
	}
	else if($search_by==3 && $search_common!="")
	{
		$sql_cond .= " and a.submit_type='$search_common'";
	}
	else if($search_by==4 && $search_common!="")
	{
		$invoice_ids='';
	
		if($db_type==0) $id_cond="group_concat(id) as id";
		else if($db_type==2) $id_cond="LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as id";
		$invoice_cond="invoice_no like '%$search_common%'";
		//echo "select $id_cond from com_export_invoice_ship_mst where $invoice_cond";
		$invoice_ids=return_field_value("$id_cond","com_export_invoice_ship_mst","$invoice_cond","id");
		
		//if($db_type==2 && $invoice_ids!="") $invoice_ids = $invoice_ids->load();
		if ($invoice_ids!="")
		{
			$invoice_ids=explode(",",$invoice_ids);
			$invoice_idsCond=""; 
			//echo count($invoice_ids); die;
			if($db_type==2 && count($invoice_ids)>=999)
			{
				$chunk_arr=array_chunk($invoice_ids,999);
				foreach($chunk_arr as $val)
				{
					$ids=implode(",",$val);
					if($invoice_idsCond=="")
					{
						$invoice_idsCond.=" and ( b.invoice_id in ( $ids) ";
					}
					else
					{
						$invoice_idsCond.=" or  b.invoice_id in ( $ids) ";
					}
				}
				$invoice_idsCond.=")";
			}
			else
			{
				$ids=implode(",",$invoice_ids);
				$invoice_idsCond.=" and b.invoice_id in ($ids) ";
			}
		}
		else if($invoice_ids=="" && (($invoice_cond!="" && $search_by==4)))
		{
			echo "Not Found"; die;
		}
	}
	
	if($txt_date_from!="" && $txt_date_to!="") 
	{
		$sql_cond .= " and a.submit_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
	}

	if($cbo_year_selection!="") 
	{
		$sql_cond .= " and EXTRACT(YEAR FROM a.submit_date)='$cbo_year_selection'";
	}
	
	if($sys_num_par!="") $sql_cond .= " and a.id=$sys_num_par";
 	
	if(trim($company_name)!="") $sql_cond .= " and company_id ='$company_name'";
	if(trim($buyer_name)!=0) $sql_cond .= " and buyer_id ='$buyer_name'";
	
	$bank_arr = return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	$lc_no_arr = return_library_array( "select id, export_lc_no from com_export_lc",'id','export_lc_no');
	$sc_no_arr = return_library_array( "select id, contract_no from com_sales_contract",'id','contract_no');
	$invoice_no_arr= return_library_array( "select id, invoice_no from com_export_invoice_ship_mst",'id','invoice_no');
	$buyer_arr= return_library_array( "select id, short_name from  lib_buyer",'id','short_name');
	$company_arr= return_library_array( "select id, company_name from  lib_company",'id','company_name');
	
	$sql ="select a.id as sys_id_par, a.submission_id as id, a.bank_ref_no, a.submit_date, a.submit_to, a.lien_bank, a.submit_type, a.buyer_id, LISTAGG(b.is_lc, ',') WITHIN GROUP (ORDER BY b.is_lc) as is_lc_string, LISTAGG(b.invoice_id, ',') WITHIN GROUP (ORDER BY b.invoice_id) as invoice_id_string, LISTAGG(b.lc_sc_id, ',') WITHIN GROUP (ORDER BY b.lc_sc_id) as lc_sc_id_string, LISTAGG(b.id, ',') WITHIN GROUP (ORDER BY b.id) as submission_dtls_id, a.is_posted_account, a.import_btb 
	from com_export_doc_submission_mst_par a, com_export_doc_submission_invo b
	where a.submission_id=b.doc_submission_mst_id and a.submit_type=2 and a.status_active=1 and a.entry_form=40  $sql_cond $invoice_idsCond 
	group by a.id, a.submission_id, a.bank_ref_no, a.submit_date, a.submit_to, a.lien_bank, a.submit_type, a.buyer_id, a.is_posted_account, a.import_btb";
	

	$res = sql_select($sql);
    ?>
        <table border="1" width="1030" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <thead>
                <th width="30">SL</th>
                <th width="60">Submit Id</th>
                <th width="100">Bank Ref /Bill No</th>
                <th width="90">Buyer</th>
                <th width="180">LC/SC No</th>
                <th width="200">Invoice List</th>
                <th width="70">Submit Date</th>
                <th width="70">Submitted To</th>
                <th width="130">Lien Bank</th>
                <th >Submit Type</th>
            </thead>
        </table>
    <div style="width:1030px; overflow-y:scroll; max-height:230px;">
        <table border="1" width="1012" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="list_view" align="left">             
   		<?                     
        	$i=1;
			foreach($res as $row)
			{  
				/*if($db_type==2) $row[csf("is_lc_string")] = $row[csf("is_lc_string")]->load();
				if($db_type==2) $row[csf("invoice_id_string")] = $row[csf("invoice_id_string")]->load();
				if($db_type==2) $row[csf("lc_sc_id_string")] = $row[csf("lc_sc_id_string")]->load();
				if($db_type==2) $row[csf("submission_dtls_id")] = $row[csf("submission_dtls_id")]->load();*/
				
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
				
				$submission_dtls_id = implode(",",array_unique(explode(",",$row[csf("submission_dtls_id")])));
				
				$expisLC = explode(",",$row[csf("is_lc_string")]);
				$expisINV = explode(",",$row[csf("lc_sc_id_string")]);
				$j=0;$lcID="";$scID="";
				foreach($expisLC as $key=>$val)
				{
					if($val==1 && $expisINV[$j]!=0)
					{
 						// export LC id
						if($lcID=="") $lcID .= $expisINV[$j]; else $lcID .=",".$expisINV[$j];
					}
					else if($expisINV[$j]!=0)
					{  
 						//Sales Contact id
						if($scID=="") $scID .= $expisINV[$j]; else $scID .=",".$expisINV[$j];
					}
					$j++;	
 				}
				
				$lc_sc_no="";
				if($lcID!="")
				{
 					// export LC number
					$poSQL=array_unique(explode(",",$lcID));
 					foreach($poSQL as $poR)
					{
						if($lc_sc_no=="") $lc_sc_no=$lc_no_arr[$poR]; else $lc_sc_no.=",".$lc_no_arr[$poR];
					} 
				}
				if($scID!="")
				{
					//Sales Contact Number
					$scSQL=array_unique(explode(",",$scID));
 					foreach($scSQL as $poR)
					{
						if($lc_sc_no=="") $lc_sc_no=$sc_no_arr[$poR]; else $lc_sc_no.=",".$sc_no_arr[$poR];
					}  		 
				}
				
				//invoice list 
				$invoiceNo="";
				if($row[csf("invoice_id_string")]!="")
				{
					$all_invoice_id=array_unique(explode(",",$row[csf("invoice_id_string")]));
					//$invoice_no_arr
					//$invoiceSQL=sql_select("select invoice_no from com_export_invoice_ship_mst where id in (".$row[csf("invoice_id_string")].")");
					foreach($all_invoice_id as $poR)
					{
						if($invoiceNo=="") $invoiceNo=$invoice_no_arr[$poR]; else $invoiceNo.=",".$invoice_no_arr[$poR];
					} 
				}
          		?>     
			   		<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf("id")];?>**<? echo $submission_dtls_id;?>**<? echo $row[csf("is_posted_account")];?>**<? echo $row[csf("sys_id_par")];?>')" > 
                        <td width="30" align="center"><? echo $i;?></td>
                        <td width="60" align="center"><? echo $row[csf("sys_id_par")];?></td>
                        <td width="100"><p><? echo $row[csf("bank_ref_no")]; ?></p></td>
						<? if($row[csf("import_btb")] == "1"){
							$buyer_company =$company_arr[$row[csf("buyer_id")]];
						}else{
							$buyer_company =$buyer_arr[$row[csf("buyer_id")]];
						}?>
                        <td width="90"><p><? echo $buyer_company;//$buyer_arr[$row[csf("buyer_id")]]; ?></p></td>
                        <td width="180"><p><? echo $lc_sc_no; ?></p></td>
                        <td width="200"><p><? echo $invoiceNo; ?></p></td>
                        <td width="70" align="center"><? echo change_date_format($row[csf("submit_date")]); ?></td>
                        <td width="70"><p><? echo $submited_to[$row[csf("submit_to")]]; ?></p></td>
                        <td width="130"><p><? echo $bank_arr[$row[csf("lien_bank")]]; ?></p></td>
                        <td><p><? echo $submission_type[$row[csf("submit_type")]]; ?></p></td>
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

if($action=="populate_master_from_data_par")
{
	$data_ref=explode("**",$data);
	$sql = "select a.id,a.company_id, a.buyer_id, a.submit_date, a.submit_to, a.bank_ref_no, a.bank_ref_date, a.days_to_realize,a.possible_reali_date, a.courier_receipt_no, a.courier_company, a.courier_date, a.bnk_to_bnk_cour_no, a.bnk_to_bnk_cour_dt, a.lien_bank, a.lc_currency, a.pay_term,  a.submit_type, a.negotiation_date, a.remarks, listagg(cast(b.is_lc as varchar(4000)), ',') within group(order by b.is_lc) as is_lc_string, listagg(cast(b.invoice_id as varchar(4000)), ',') within group(order by b.invoice_id) as invoice_id_string, listagg(cast(b.lc_sc_id as varchar(4000)), ',') within group(order by b.lc_sc_id) as lc_sc_id_string, listagg(cast(b.id as varchar(4000)), ',') within group(order by b.id) as inv_id, a.import_btb, a.issue_bank_dtls
	from com_export_doc_submission_mst a, com_export_doc_submission_invo b
	where a.id=b.doc_submission_mst_id and a.id=$data_ref[0] 
	group by a.id, a.company_id, a.buyer_id, a.submit_date, a.submit_to, a.bank_ref_no, a.bank_ref_date, a.days_to_realize, a.possible_reali_date,  a.courier_receipt_no, a.courier_company, a.courier_date, a.bnk_to_bnk_cour_no, a.bnk_to_bnk_cour_dt, a.lien_bank, a.lc_currency, a.pay_term, a.submit_type, a.negotiation_date, a.remarks, a.import_btb, a.issue_bank_dtls";	

	// echo $sql;
	$res = sql_select($sql);
	$i=1;$all_buyer_sub_dtls_id="";
	foreach($res as $row)
	{
		
		if($row[csf("import_btb")] == 1)
		{
			$company_arr= return_library_array( "select id, company_name from  lib_company",'id','company_name');
			echo '$("#cbo_buyer_name option[value!=\'0\']").remove();';
        	echo '$("#cbo_buyer_name").append("<option selected value=\''.$row[csf("buyer_id")].'\'>'.$company_arr[$row[csf("buyer_id")]].'</option>");'."\n";
		}else{
			echo "$('#cbo_buyer_name').val('".$row[csf("buyer_id")]."');\n";
		} 
		echo "$('#lcsc_no').attr('disabled',true);\n";
  		echo "$('#cbo_company_name').val('".$row[csf("company_id")]."');\n";
  		echo "$('#txt_ref_id').val('".$row[csf("id")]."');\n";
  		echo "$('#txt_import_btb').val('".$row[csf("import_btb")]."');\n";
		echo "$('#txt_issue_bank_info_dtls').val('".$row[csf("issue_bank_dtls")]."');\n";
		$issue_bank_ref=explode("__",$row[csf("issue_bank_dtls")]);
		echo "$('#txt_issue_bank_info').val('".$issue_bank_ref[0]."');\n";
		
		$buyer_sub_dtls=sql_select("select b.id from com_export_doc_submission_mst a, com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and a.entry_form=39 and b.is_converted=1 and  b.invoice_id in (".$row[csf("invoice_id_string")].")");
		foreach($buyer_sub_dtls as $val)
		{
			$all_buyer_sub_dtls_id.=$val[csf("id")].",";
		}
 		
		$expisLC = implode(",",array_unique(explode(",",$row[csf("is_lc_string")])));
		$expisINV = array_unique(explode(",",$row[csf("lc_sc_id_string")]));
		$all_lc_sc_id=implode(",",$expisINV);
		$j=0;$lcID="";$scID="";
		
		$lc_sc_no=""; $lc_import_btb=0;$item_category_id = 0;
		if($expisLC==1)
		{
			$poSQL=sql_select("select export_lc_no, import_btb, export_item_category, pay_term from com_export_lc where id in (".$all_lc_sc_id.")");
			foreach($poSQL as $poR)
			{
				if($lc_sc_no=="") $lc_sc_no=$poR[csf("export_lc_no")]; else $lc_sc_no.=", ".$poR[csf("export_lc_no")];
				if($lc_import_btb==0) $lc_import_btb=$poR[csf("import_btb")]; else $lc_import_btb.=",".$poR[csf("import_btb")];
				if($item_category_id==0) $item_category_id=$poR[csf("export_item_category")]; else $item_category_id.=",".$poR[csf("export_item_category")];
				$pay_term=$poR[csf("pay_term")];
				$lc_for=1;
			} 
		}
		else
		{
			$scSQL=sql_select("select contract_no, pay_term, lc_for  from com_sales_contract where id in (".$all_lc_sc_id.")");
			foreach($scSQL as $poR)
			{
				if($lc_sc_no=="") $lc_sc_no=$poR[csf("contract_no")]; else $lc_sc_no.=",".$poR[csf("contract_no")];
				$pay_term=$poR[csf("pay_term")];
				$lc_for=$poR[csf("lc_for")];
			} 
		}
		$po_numbers_arr=array();
		if($lc_for==2)
		{
			$poSQL=sql_select("select a.requisition_number as PO_NUMBER, c.id as INV_ID 
			from sample_development_mst a, com_export_invoice_ship_dtls b, com_export_invoice_ship_mst c
			 where a.id=b.po_breakdown_id and b.mst_id=c.id and c.lc_sc_id in(".$all_lc_sc_id.") and c.is_lc=$expisLC and c.lc_for=2");
			foreach($poSQL as $value)
			{
				$po_numbers_arr[$value["INV_ID"]].=$value["PO_NUMBER"].",";
			}
		}
		else if($lc_import_btb == 1 && $item_category_id == 2)
		{
			$sales_order_arr =  sql_select("select b.id as INV_ID, b.work_order_no as WORK_ORDER_NO 
			from com_export_lc_order_info a, com_export_invoice_ship_mst b, com_export_invoice_ship_dtls c 
			where a.com_export_lc_id=b.lc_sc_id and b.id=c.mst_id and b.is_lc=$expisLC and a.com_export_lc_id in($all_lc_sc_id)");
			foreach ($sales_order_arr as $value) 
			{
				$po_numbers_arr[$value["INV_ID"]].=$value["WORK_ORDER_NO"].",";
			}
		}
		else if($item_category_id == 10)
		{
			$poSQL=sql_select("select a.job_no_mst as PO_NUMBER, c.id as INV_ID from fabric_sales_order_dtls a, com_export_invoice_ship_dtls b, com_export_invoice_ship_mst c
			where a.id=b.po_breakdown_id and b.mst_id=c.id and c.lc_sc_id in(".$all_lc_sc_id.") and c.is_lc=$expisLC");
			foreach($poSQL as $value)
			{
				$po_numbers_arr[$value["INV_ID"]].=$value["PO_NUMBER"].",";
			}
		}
		else if($item_category_id==23 || $item_category_id==35 || $item_category_id==36 || $item_category_id==37 || $item_category_id==38 || $item_category_id==67)
		{
			$poSQL=sql_select("select a.order_no as PO_NUMBER, c.id as INV_ID from subcon_ord_dtls a, com_export_invoice_ship_dtls b, com_export_invoice_ship_mst c
			where a.id=b.po_breakdown_id and b.mst_id=c.id and c.lc_sc_id in(".$all_lc_sc_id.") and c.is_lc=$expisLC");
			foreach($poSQL as $value)
			{
				$po_numbers_arr[$value["INV_ID"]].=$value["PO_NUMBER"].",";
			}
		}
		else
		{
			//list view data arrange-----------------// 
			$poSQL=sql_select("select a.po_number as PO_NUMBER, c.id as INV_ID from wo_po_break_down a, com_export_invoice_ship_dtls b, com_export_invoice_ship_mst c
			 where a.id=b.po_breakdown_id and b.mst_id=c.id and c.lc_sc_id in(".$all_lc_sc_id.") and c.is_lc=$expisLC");
			foreach($poSQL as $value)
			{
				$po_numbers_arr[$value["INV_ID"]].=$value["PO_NUMBER"].",";
			}
		}
		
		echo "$('#cbo_pay_term').val('".$row[csf("pay_term")]."');\n";
		echo "$('#lcsc_no').val('".$lc_sc_no."');\n";  
		echo "$('#lc_sc_id').val('".$all_lc_sc_id."');\n"; 
		
		if($row[csf("submit_date")]!='0000-00-00' || $row[csf("submit_date")]!='')
		{		 
 			echo "$('#txt_submit_date').val('".change_date_format($row[csf("submit_date")])."');\n";
		}
		else
		{
			echo "$('#txt_submit_date').val('');\n";
		}
		echo "$('#cbo_submit_to').val('".$row[csf("submit_to")]."');\n";
		echo "$('#txt_bank_ref').val('".$row[csf("bank_ref_no")]."');\n";
		if($row[csf("bank_ref_date")]=='0000-00-00' || $row[csf("bank_ref_date")]=='')
		{
			echo "$('#txt_bank_ref_date').val('');\n";
		}
		else
		{
			echo "$('#txt_bank_ref_date').val('".change_date_format($row[csf("bank_ref_date")])."');\n";
		}
		echo "$('#cbo_submission_type').val('".$row[csf("submit_type")]."');\n";

		if($row[csf("submit_type")]==1)
		{
		 echo "$('#txt_negotiation_date').attr('disabled',true);\n";
		 echo "$('#cbo_submission_type').attr('disabled',false);\n";
		}
		else
		{
			echo "$('#cbo_submission_type').attr('disabled',true);\n";
			if($row[csf("negotiation_date")]!='0000-00-00' && $row[csf("negotiation_date")]!='')
			{
				echo "$('#txt_negotiation_date').attr('disabled',false);\n";
				echo "$('#txt_negotiation_date').val('".change_date_format($row[csf("negotiation_date")])."');\n";
			}
			else
			{
				echo "$('#txt_negotiation_date').val('');\n";
			}
		}
		echo "$('#txt_day_to_realize').val('".$row[csf("days_to_realize")]."');\n";
		if($row[csf("possible_reali_date")]=='0000-00-00' || $row[csf("possible_reali_date")]=='')
		{
			echo "$('#txt_possible_reali_date').val('');\n";
		}
		else
		{
			echo "$('#txt_possible_reali_date').val('".change_date_format($row[csf("possible_reali_date")])."');\n";
		}

		echo "$('#courier_receipt_no').val('".$row[csf("courier_receipt_no")]."');\n";
		echo "$('#txt_courier_company').val('".$row[csf("courier_company")]."');\n";
		if($row[csf("courier_date")]=='0000-00-00' || $row[csf("courier_date")]=='')
		{
			echo "$('#txt_courier_date').val('');\n";
		}
		else
		{
			echo "$('#txt_courier_date').val('".change_date_format($row[csf("courier_date")])."');\n";
		}
		echo "$('#txt_bnk_to_bnk_cour_no').val('".$row[csf("bnk_to_bnk_cour_no")]."');\n";
		if($row[csf("bnk_to_bnk_cour_dt")]=='0000-00-00' || $row[csf("bnk_to_bnk_cour_dt")]=='')
		{
			echo "$('#txt_bnk_to_bnk_cour_date').val('');\n";
		}
		else
		{
			echo "$('#txt_bnk_to_bnk_cour_date').val('".change_date_format($row[csf("bnk_to_bnk_cour_dt")])."');\n";
		}
		echo "$('#cbo_lien_bank').val('".$row[csf("lien_bank")]."');\n";
		echo "$('#cbo_currency').val('".$row[csf("lc_currency")]."');\n";
		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		
		echo "$('#mst_tbl_id').val('".$row[csf("id")]."');\n";
		echo "$('#invoice_tbl_id').val('".$row[csf("inv_id")]."');\n";
		
		//invoice list view -----------------------------------------------------------------------//
		//start -----------------------------------------------------------------------//
		$invSQL="select sum(a.net_invo_value) as net_invo_value  from com_export_doc_submission_invo a, com_export_invoice_ship_mst b where a.status_active=1 and a.is_deleted=0 and a.invoice_id=b.id and a.doc_submission_mst_id=".$row[csf("id")]."";
 		// echo $invSQL;die;
		$resArray=sql_select($invSQL);
		$tot_invoice_val=0;$invoice_id="";
 		foreach($resArray as $invRow)
		{ 
			echo "$('#txt_bill_value').val('".$invRow[csf("net_invo_value")]."');\n";
			echo "$('#txt_bill_value').attr('disabled',true);\n";
		}
		 
		
		//transaction list generate here Start------------------------------------//
		$invtrns =  "select sum(a.LC_SC_CURR) as LC_SC_CURR from com_export_doc_sub_trans a, com_export_doc_submission_mst b where a.doc_submission_mst_id=b.id and a.doc_submission_mst_id=".$row[csf("id")]." and a.DOC_SUBMISSION_MST_ID_PAR<>$data_ref[1] and a.status_active=1 and a.is_deleted=0";
		
		$trans_arr=sql_select($invtrns);
		foreach($trans_arr as $row){

			echo "$('#txt_Cuml_Neg_Amount').val('".$row[csf("lc_sc_curr")]."');\n";
		    echo "$('#txt_Cuml_Neg_Amount').attr('disabled',true);\n";
		}

		echo "calculateBalance();\n";

		//list view for transaction area----------------------------------------//
 		echo "$('#transaction_container').find('tr').remove();\n";         
		echo "$('#transaction_container').html( '".$transaction_tr."')".";\n";	
		echo "sum_of_currency();\n";	

		//transaction list generate here Start------------------------------------//
		$invSQL =  "select a.id, a.doc_submission_mst_id, a.acc_head, a.acc_loan, a.loan_acc_no, a.dom_curr, a.conver_rate, a.lc_sc_curr, a.negotiation_date 
					from com_export_doc_sub_trans a
					where a.DOC_SUBMISSION_MST_ID_PAR=".$data_ref[1]." and a.status_active=1 and a.is_deleted=0";
 		//echo $invSQL;die;
		$resArray=sql_select($invSQL);
		$rowNo=1; $transaction_tr="";
 		foreach($resArray as $invRow)
		{
			$acc_head=$commercial_head[$invRow[csf('acc_head')]];
			$transaction_tr .= "<tr id=\"tr".$rowNo."\">".
					"<td><input type=\"text\" name=\"cbo_account_head[]\" id=\"cbo_account_head_".$rowNo."\" onFocus=\"add_auto_complete( ".$rowNo." )\"  onBlur=\"fn_value_check(".$rowNo.",this.value,\'cbo_account_head\')\" placeholder=\"Browse Or Write\" value=\"".$acc_head."\" title=\"".$invRow[csf('acc_head')]."\" onDblClick=\"fn_commercial_head_display(".$rowNo.",cbo_account_head)\" class=\"text_boxes\" style=\"width:170px;\" /></td>".
					"<td><input type=\"text\" id=\"txt_negotiation_dtls_date_".$rowNo."\" name=\"txt_negotiation_dtls_date[]\" class=\"datepicker\" style=\"width:100px\" value=\"".change_date_format($invRow[csf("negotiation_date")])."\" /></td>".
					"<td><input type=\"text\" id=\"txt_ac_loan_no_".$rowNo."\" name=\"txt_ac_loan_no[]\" class=\"text_boxes\" style=\"width:100px\" value=\"".$invRow[csf("acc_loan")]."\" /></td>".
					"<td><input type=\"text\" id=\"txt_loan_no_".$rowNo."\" name=\"txt_loan_no[]\" class=\"text_boxes\" style=\"width:100px\" value=\"".$invRow[csf("loan_acc_no")]."\" /></td>".
					"<td><input type=\"text\" id=\"txt_domestic_curr_".$rowNo."\" name=\"txt_domestic_curr[]\" class=\"text_boxes_numeric\" style=\"width:100px\" value=\"".($invRow[csf("dom_curr")])."\" onkeyup=\"fn_calculate(this.id,".$rowNo.")\" /></td>".
					"<td><input type=\"text\" id=\"txt_conversion_rate_".$rowNo."\" name=\"txt_conversion_rate[]\" class=\"text_boxes_numeric\" style=\"width:100px\" value=\"".$invRow[csf("conver_rate")]."\" onkeyup=\"fn_calculate(this.id,".$rowNo.")\" /></td>".
					"<td><input type=\"text\" id=\"txt_lcsc_currency_".$rowNo."\" name=\"txt_lcsc_currency[]\" class=\"text_boxes_numeric\" style=\"width:100px\" value=\"".($invRow[csf("lc_sc_curr")])."\" onkeyup=\"fn_calculate(this.id,".$rowNo.")\" /></td>".
					"<td>".
					"<input type=\"button\" id=\"increaserow_".$rowNo."\" style=\"width:30px\" class=\"formbuttonplasminus\" value=\"+\" onClick=\"javascript:fn_inc_decr_row(".$rowNo.",\'increase\');\" />".
					"<input type=\"button\" id=\"decreaserow_".$rowNo."\" style=\"width:30px\" class=\"formbuttonplasminus\" value=\"-\" onClick=\"javascript:fn_inc_decr_row(".$rowNo.",\'decrease\');\" />".
					"</td>".
			"</tr>";	
			$rowNo = $rowNo+1;
		}
		
		//list view for transaction area----------------------------------------//
 		echo "$('#transaction_container').find('tr').remove();\n";         
		echo "$('#transaction_container').html( '".$transaction_tr."')".";\n";	
		echo "sum_of_currency();\n";
		//transaction list generate here End------------------------------------//
   	}//main foreach end $all_buyer_sub_dtls_id$invoice_id

	$all_buyer_sub_dtls_id=chop($all_buyer_sub_dtls_id," , ");
	$invoice_id=chop($invoice_id," , ");
	echo "$('#invoice_id_string').val('".$invoice_id."');\n"; 
	echo "$('#all_buyer_sub_dtls_id').val('".$all_buyer_sub_dtls_id."');\n";	
	exit();	
}




if($action=="bank_submit_letter")
{
	// $ex_data = explode("__",$data);
	// list($mst_id, $company_id, $type, $report_title, $msg) = explode("__", $data);

	extract($_REQUEST);
	// extract(check_magic_quote_gpc($_REQUEST));	
	$mst_id=str_replace("'","",$mst_tbl_id);
	$company_id=str_replace("'","",$cbo_company_name);
	$report_title=str_replace("'","",$report_type);
	$type=str_replace("'","",$type);
	$msg=str_replace("'","",$msg);

	// echo $mst_id.'**'.$company_id.'**'.$type.'**'.$report_title.'**'.$msg; die;
	$currency_sign_array = array(
		1=> "&#2547;", //৳
		2=> "&#36;", //$
		3=> "&#8364;", //€
		4=> "&#8355;", //₣
		5=> "S&#36;", //S$
		6=> "&#163;", //£
		7=> "&#165;"  //¥
	);
	
	ob_start();
	//echo '<pre>'; print_r($ex_data); die;
	//echo load_html_head_contents("Buyer Submission Letter","../../", 1, 1, $unicode,'','');
	//
	?>
	<style type="text/css">
		@media print {
	   #foot{
	       position: fixed;
	    height: 70px;
	    
	    bottom: 0px;
	    left: 0px;
	    right: 0px;
	    margin-bottom: 0px;
	    }
	}
	
	.date_block{
		width: 60%;
		margin: 0;
		padding: 0;
		float: left;
	}
	.buyer_block{
		width: 40%;
		margin: 0;
		padding: 0;
		float: left;
		text-align: right;
	}

	</style>
	<?
	$buyer_name_arr = return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$order_inv_array=array();
	$sql_order_inv=sql_select("select b.id as po_id, b.po_number as po_no,b.job_no_mst,a.order_uom from wo_po_break_down b,wo_po_details_master a where b.job_no_mst=a.job_no and a.status_active=1 and b.status_active=1");
	foreach($sql_order_inv as $row)
	{
		$order_inv_array[$row[csf("po_id")]]["po_no"]=$row[csf("po_no")];
		$order_inv_array[$row[csf("po_id")]]["job_no_mst"]=$row[csf("job_no_mst")];
		$order_inv_array[$row[csf("po_id")]]["order_uom"]=$unit_of_measurement[$row[csf("order_uom")]];
		//$order_uom_arr[$row[csf("job_no_mst")]]=$row[csf("order_uom")];	
	}
	
	
	//echo $order_inv_array[589]["order_uom"] ;die;
	/*$order_inv_array=array();
	$sql_order_inv=sql_select("select job_no,order_uom from wo_po_details_master");
	foreach($sql_order_inv as $row)
	{
		$order_inv_array[$row[csf("job_no")]]["order_uom"]=$row[csf("order_uom")];
		//$order_inv_array[$row[csf("po_id")]]["job_no_mst"]=$row[csf("job_no_mst")];
		
	}*/
	
	
	//$order_uom_arr = return_library_array( "select job_no, order_uom from  wo_po_details_master",'job_no','order_uom');
	
	$sql_sub_buyer="select a.id as sub_id, a.buyer_id, a.company_id, a.submit_date, a.bank_ref_no, a.lc_currency, a.lien_bank, a.remarks, b.is_lc, b.lc_sc_id, b.all_order_no, c.id as inv_id, c.invoice_no, c.invoice_date, c.invoice_quantity, b.net_invo_value as invoice_value, c.bl_no, c.bl_date, c.exp_form_no, c.exp_form_date, c.total_carton_qnty, a.import_btb, a.days_to_realize, c.total_carton_qnty, c.feeder_vessel, a.lc_currency, a.issue_bank_dtls
	from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c 
	where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form=40 and a.id=$mst_id";  
	$sql_sub_buyer;//die;
	$result=sql_select($sql_sub_buyer);
	$feeder_vessel=$total_carton_qnty=$all_bl_no=$all_bl_date='';
	foreach($result as $row)
	{
		$total_inv_value+=$row[csf("invoice_value")];
		$total_invoice_quantity+=$row[csf("invoice_quantity")];
		$lc_sc_ids.=$row[csf("lc_sc_id")].",";
		$all_order_no.=$row[csf("all_order_no")].",";
		$all_invoice_no.=$row[csf("invoice_no")].", ";
		$all_invoice_date.=$row[csf("invoice_date")].",";		
		$days_to_realize=$row[csf("days_to_realize")];
		$lcSc_currency=$row[csf("lc_currency")];
		$bank_ref_no=$row[csf("bank_ref_no")];
		
		if ($row[csf("total_carton_qnty")]!='') 
		{
			$total_carton_qnty.=$row[csf("total_carton_qnty")].",";
		}
		if ($row[csf("bl_no")]!='') 
		{
			$all_bl_no.=$row[csf("bl_no")].",";
		}
		if ($row[csf("bl_date")]!='') 
		{
			$all_bl_date.=$row[csf("bl_date")].",";
		}
		if ($row[csf("feeder_vessel")]!='') 
		{
			$feeder_vessel.=$row[csf("feeder_vessel")].",";
		}
		if ($row[csf("remarks")]!='') 
		{
			$remarks=$row[csf("remarks")].".";
		}
	}
	//echo $feeder_vessel;
	//$allorderno=chop($all_order_no,",");
	//echo $allorderno;die;
	
	$lien_bank=$result[0][csf("lien_bank")];
	//echo $lc_sc_ids; die;
	$lcScIDS=chop($lc_sc_ids,",");
	//echo $lcScIDS;die;
	
	$sql_bank_info=sql_select("SELECT id, contact_person, bank_name, branch_name, address from lib_bank where id=$lien_bank");
	foreach($sql_bank_info as $row)
	{
		$bank_data_arr[$row[csf("id")]]["contact_person"]=$row[csf("contact_person")];
		$bank_data_arr[$row[csf("id")]]["bank_name"]=$row[csf("bank_name")];
		$bank_data_arr[$row[csf("id")]]["branch_name"]=$row[csf("branch_name")];
		$bank_data_arr[$row[csf("id")]]["address"]=$row[csf("address")];
	}

	
	$export_lc_uom_arr=array();
	
	//echo "SELECT com_export_lc_id,uom FROM com_export_lc_order_info where com_export_lc_id in($lcScIDS)"; die;
	$sql_lc_uom=sql_select("SELECT com_export_lc_id,uom FROM com_export_lc_order_info where com_export_lc_id in($lcScIDS)");
	foreach($sql_lc_uom as $row)
	{
		$export_lc_uom_arr[$row[csf("com_export_lc_id")]]["uom"]=$row[csf("uom")];
	}
	
	
	//print_r($export_lc_uom_arr);
	
	if(1==$result[0][csf("is_lc")])
	{
		//echo "SELECT id,export_lc_no,lc_date,lien_bank,consignee,issuing_bank_name,pay_term,tenor FROM com_export_lc where id in($lcScIDS)"; die;
		$sql_lc=sql_select("SELECT id,export_lc_no,lc_date,lien_bank,consignee,issuing_bank_name,pay_term,tenor FROM com_export_lc where id in($lcScIDS)");
		foreach($sql_lc as $row)
		{
			$export_lc_no_arr[$row[csf("id")]]["export_lc_no"]=$row[csf("export_lc_no")];
			$export_lc_no_arr[$row[csf("id")]]["lc_date"]=$row[csf("lc_date")];
			$export_lc_no_arr[$row[csf("id")]]["lien_bank"]=$row[csf("lien_bank")];
			$export_lc_no_arr[$row[csf("id")]]["consignee"]=$row[csf("consignee")];
			$export_lc_no_arr[$row[csf("id")]]["issuing_bank_name"]=$row[csf("issuing_bank_name")];
			$export_lc_no_arr[$row[csf("id")]]["pay_term"]=$row[csf("pay_term")];
			$export_lc_no_arr[$row[csf("id")]]["tenor"]=$row[csf("tenor")];
		}
	}
	else
	{
		/*echo "SELECT id,contract_no,contract_date,lien_bank,consignee,pay_term,tenor FROM com_sales_contract where id in($lcScIDS)";*/
		$sql_sc=sql_select("SELECT id,contract_no,contract_date,lien_bank,consignee,pay_term,tenor FROM com_sales_contract where id in($lcScIDS)");
		foreach($sql_sc as $row)
		{
			$export_sc_no_arr[$row[csf("id")]]["contract_no"]=$row[csf("contract_no")];
			$export_sc_no_arr[$row[csf("id")]]["contract_date"]=$row[csf("contract_date")];
			$export_sc_no_arr[$row[csf("id")]]["lien_bank"]=$row[csf("lien_bank")];
			$export_sc_no_arr[$row[csf("id")]]["consignee"]=$row[csf("consignee")];
			$export_sc_no_arr[$row[csf("id")]]["pay_term"]=$row[csf("pay_term")];
			$export_sc_no_arr[$row[csf("id")]]["tenor"]=$row[csf("tenor")];
		}
	} 

	if($type==1) // Print later 
	{
		?>
	    <br>
	    <table width="700" cellpadding="0" align="left" cellspacing="0" border="0">
	        <tr>
	        	<td colspan="3" height="110"></td>
	        </tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">Dated : <? echo change_date_format($result[0][csf("submit_date")]); ?> </td>
	            <td width="25" ></td>
	        </tr>
	        <tr>
	            <td width="25" colspan="3" >&nbsp;</td>
	        </tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">To</td>
	        </tr>
	        <tr>
	        	<td colspan="3" ></td>
	        </tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">
	            <?
					$leate_header_data="";
					if($result[0][csf("is_lc")]==1)
					{
						$contact_person_arr=explode(",",$bank_data_arr[$export_lc_no_arr[$result[0][csf("lc_sc_id")]]["lien_bank"]]["contact_person"]);
						$contact_person="";
						foreach($contact_person_arr as $val)
						{
							$contact_person.=$val."<br>";
						}
						$leate_header_data=$contact_person.$bank_data_arr[$export_lc_no_arr[$result[0][csf("lc_sc_id")]]["lien_bank"]]["bank_name"]."<br>".$bank_data_arr[$export_lc_no_arr[$result[0][csf("lc_sc_id")]]["lien_bank"]]["branch_name"]."<br>".$bank_data_arr[$export_lc_no_arr[$result[0][csf("lc_sc_id")]]["lien_bank"]]["address"];
						$consinee_all=explode(",",$export_lc_no_arr[$result[0][csf("lc_sc_id")]]["consignee"]);
					}
					else
					{
						$contact_person_arr=explode(",",$bank_data_arr[$export_sc_no_arr[$result[0][csf("lc_sc_id")]]["lien_bank"]]["contact_person"]);
						$contact_person="";
						foreach($contact_person_arr as $val)
						{
							$contact_person.=$val."<br>";
						}
						$leate_header_data=$contact_person.$bank_data_arr[$export_sc_no_arr[$result[0][csf("lc_sc_id")]]["lien_bank"]]["bank_name"]."<br>".$bank_data_arr[$export_sc_no_arr[$result[0][csf("lc_sc_id")]]["lien_bank"]]["branch_name"]."<br>".$bank_data_arr[$export_sc_no_arr[$result[0][csf("lc_sc_id")]]["lien_bank"]]["address"];
						$consinee_all=explode(",",$export_sc_no_arr[$result[0][csf("lc_sc_id")]]["consignee"]);
					}
					
					echo $leate_header_data;
					
					//print_r($consinee_all);
				?>
	           
	            </td>
	            <td width="25" ></td>
	        </tr>
	        <tr>
	        	<td colspan="3" height="30"></td>
	        </tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">
	            Subject: <u>Submission of Export documents for <? echo $currency[$result[0][csf("lc_currency")]]." : ". number_format($total_inv_value,2); ?></u><br>
	            Buyer&nbsp;&nbsp;: 
				<? 
				/*$buyer_name_arr;
				
				$buyer_all="";
				foreach($consinee_all as $buyer_id)
				{
					$buyer_all=$buyer_name_arr[$buyer_id].", ";
				}
				$buyer_all=chop($buyer_all," , ");
				echo $buyer_all;*/
				
				
				echo $buyer_name_arr[$result[0][csf("buyer_id")]];
				?>.
	            </td>
	            <td width="25" ></td>
	        </tr>
	        <tr>
	        	<td colspan="3" height="20"></td>
	        </tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left"> Dear Sir, </td>
	            <td width="25" ></td>
	        </tr>
	        <tr>
	        	<td colspan="3" height="5"></td>
	       </tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">
	            We enclose herewith the following export documents detail of which are given below : 
	            </td>
	            <td width="25" ></td>
	        </tr>
	        <tr>
	        	<td colspan="3" height="15"></td>
	        </tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">
	            	<table cellpadding="0" align="left" cellspacing="0" border="1" width="650" style="font-size:13px;">
	                	<thead>
	                    	<tr>
	                        	<th width="110">Lc/Sc. No <br> Date</th>
	                            <th width="120">Order No</th>
	                            <th width="110">Invoice No <br> Date</th>
	                            <th width="110">Exp No <br> Date</th>
	                            <th width="40">Quantity</th>
                                 <th width="60">UOM</th>
	                            <th width="70">Value <br> USD</th>
	                            <th width="90">Bl No <br> Date</th>
	                        </tr>
	                    </thead>
	                    <tbody>
	                    <?
						$i=1;
						foreach($result as $row)
						{
							if($row[csf("exp_form_date")]!="" && $row[csf("exp_form_date")]!='0000-00-00') $exe_form_date=change_date_format($row[csf("exp_form_date")]); else $exe_form_date="&nbsp;";
							if($row[csf("bl_date")]!="" && $row[csf("bl_date")]!='0000-00-00') $bl_date=change_date_format($row[csf("bl_date")]); else $bl_date="&nbsp;";
							if($row[csf("invoice_date")]!="" && $row[csf("invoice_date")]!='0000-00-00') $inv_date=change_date_format($row[csf("invoice_date")]); else $bl_date="&nbsp;";
							?>
	                    	<tr>
	                        	<td width="110" valign="top"><div style="width:110px;; word-wrap:break-word;">
								<? 
								if($row[csf("is_lc")]==1)
								{
									echo $export_lc_no_arr[$row[csf("lc_sc_id")]]["export_lc_no"]."<br>";
									echo change_date_format($export_lc_no_arr[$row[csf("lc_sc_id")]]["lc_date"]);
								}
								else
								{
									echo $export_sc_no_arr[$row[csf("lc_sc_id")]]["contract_no"]."<br>";
									echo change_date_format($export_sc_no_arr[$row[csf("lc_sc_id")]]["contract_date"]);
								}
								?></div>
	                            </td>
	                            <td width="120" valign="top"><div style="width:120px;; word-wrap:break-word;">
								<? 
								//echo $order_inv_array[$row[csf("inv_id")]]["po_no"];
								if($row[csf("import_btb")] == 1)
								{
									$item_category_id=0;
									if($row[csf("is_lc")] == 1)
									{
										$item_category_id = return_field_value("export_item_category","com_export_lc","id=".$row[csf("lc_sc_id")]."");
									}
									if($item_category_id == 2)
									{
										$sales_order_arr =  sql_select("select work_order_no from com_export_lc_order_info where com_export_lc_id =". $row[csf("lc_sc_id")]);

										foreach ($sales_order_arr as $value) 
										{
											if($po_numbers=="") $po_numbers=$value[csf("work_order_no")]; else $po_numbers.=",".$value[csf("work_order_no")];
										}
										$all_po = implode(",",array_filter(array_unique(explode(",",$po_numbers))));
									}
								}
								else
								{
									$all_order_arr=explode(",",$row[csf("all_order_no")]);
									$all_po="";
									foreach($all_order_arr as $order_id)
									{
										$all_po.=$order_inv_array[$order_id]["po_no"].", ";
									}
									$all_po=chop($all_po," , ");
								}
								echo $all_po;
								?></div></td>
	                            <td width="110" valign="top"><div style="width:110px;; word-wrap:break-word;"><? echo $row[csf("invoice_no")]."<br>".$inv_date;?> </div></td>
	                            <td width="110" valign="top"><div style="width:110px;; word-wrap:break-word;"><? echo $row[csf("exp_form_no")]."<br>".$exe_form_date;?></div></td>
	                            <td width="40" valign="top" align="right"><div style="width:40px;; word-wrap:break-word;"><? echo number_format($row[csf("invoice_quantity")],2); $tot_qty+=$row[csf("invoice_quantity")];?></div></td>
                                
                                <td width="60" valign="top" align="center"><div style="width:60px;; word-wrap:break-word;"><? 
								$order_uom="";
								
								if($row[csf("import_btb")]== 1)
								{
									
									echo $unit_of_measurement[$export_lc_uom_arr[$row[csf("lc_sc_id")]]["uom"]];
								}
								else
								{
									$all_order_arr=explode(",",$row[csf("all_order_no")]);
									$all_po="";
									foreach($all_order_arr as $order_id)
									{
										
										$order_uom.=$order_inv_array[$order_id]["order_uom"].", ";
										
									}
									
									//$all_po = implode(",",array_filter(array_unique(explode(",",$po_numbers))));
									 $orderuom=explode(",",chop($order_uom," , "));
									 $list_explode = array_unique(array_map('trim', $orderuom));
									 $resulter = array_unique($list_explode);
									echo $order_uom = implode(",",array_filter($list_explode));
									
										
								}
								
								?></div></td>
                                
	                            <td width="70" valign="top" align="right">
								<div style="width:70px;; word-wrap:break-word;">
								<? 
								echo $currency_sign_array[$result[0][csf("lc_currency")]].number_format($row[csf("invoice_value")],2,'.',''); $tot_value+=$row[csf("invoice_value")];?></div>
								</td>
	                            <td width="90" valign="top" ><div style="width:90px;; word-wrap:break-word;"><? echo $row[csf("bl_no")]."<br>".$bl_date; ?></div></td>
	                        </tr>
	                        <?
							$i++;
						}
						?>
						<tr> 
							<td align="right" colspan="4" ><strong>Total:</strong></td>
							<td align="right"><? echo $tot_qty; ?>&nbsp;</td>
                            <td align="right">&nbsp;</td>
							<td align="right"><? echo $currency_sign_array[$result[0][csf("lc_currency")]].number_format($tot_value,2,'.',''); ?>&nbsp;</td>
							<td align="right">&nbsp;</td>
						</tr>
	                    </tbody>
	                </table>
	            </td>
	            <td width="25" ></td>
	        </tr>
	        <tr>
	        	<td colspan="3" height="15"></td>
	        </tr>
	    
	       

			<tr>
	            <td width="25" ></td>
	            <td width="400" align="left">
	            	<table cellpadding="0" align="left" cellspacing="0" border="1" width="400" style="font-size:16px;">
	                	<thead>
	                    	<tr>
	                        	<th width="30">Sl</th>
	                            <th width="100">Particulars of Documents</th>
	                            <th width="60">No. Of Copy</th>
	                        </tr>
	                    </thead>
	                    <tbody>
	                    <?
						 $sql = "select mst_id, terms_id, terms_value from doc_submission_terms where terms_value is not null and terms_value not in ('0','0+0') and mst_id=".$mst_id."";       
						$i=1;     
	            		$data_array=sql_select($sql);
						foreach($data_array as $row)
						{
							?>
	                    	<tr>
	                            <td width="30" align="center"><b><? echo $i;?></b></td>
	                            <td width="100" align="left"><b><? echo $document_set[$row[csf("terms_id")]];?></b></td>
	                            <td width="60" align="center"><b><? echo $row[csf("terms_value")]; ?></b></td>
	                        </tr>
	                        <?
							$i++;
						}
						?>
	                    </tbody>
	                </table>
	            </td>
	            <td width="25" ></td>
	        </tr>
	        <tr>
	        	<td colspan="3" height="15"></td>
	        </tr>
	 		<tr>
	            <td width="25" ></td>
	            <td width="650" align="left">
	            We request you to negotiate/purchase these invoices value of the above export documents and credit the proceeds to our account with you.
	            You are also requested to send the above documents to the applicant’s bank by DHL/FEDEX immediately after negotiation/purchase.
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
	            Thanking You
	            </td>
	            <td width="25" ></td>
	        </tr>
	        <tr>
	        	<td colspan="3" height="25"></td>
	       </tr>
	         <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">
	            Yours faithfully
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
				
				echo $company_name;
				 
				?></td>
	            <td width="25" ></td>
	        </tr>
	       <tr>
	        	<td colspan="3" height="50"></td>
	       </tr>
	       <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">
	            Authorized signature
	            </td>
	            <td width="25" ></td>
	        </tr>
	    </table>
	    <?
	}

	if($type==2) // Print later 2
	{
		$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
		$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
		if($result[0][csf("submit_date")]!="" && $result[0][csf("submit_date")]!='0000-00-00') $submit_date=change_date_format($result[0][csf("submit_date")]); else $submit_date="&nbsp;";
		?>
	    <br>
	    <table width="700" cellpadding="0" align="center" cellspacing="0" border="0">
	        <tr class="form_caption">
            	<?
            	
                $data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$company_id' and form_name='company_details' and is_deleted=0 and file_type=1");
                ?>
                <td  align="left" width="200">
                <?
                foreach($data_array as $img_row)
                {
					?>
					<img src='../../../<? echo $img_row[csf('image_location')]; ?>' height='70' width='200' align="middle" />
					<?
				}
                ?>
                </td>

            	<td  colspan="" align="right" style="font-size:28px; margin-bottom:50px; color:blue;"><strong><? echo $company_library[$company_id]; ?></strong></td>
            </tr>
	        <tr class="">
	        	<td align="center" style="font-size:14px"> </td>
	        	<td colspan="2" align="center" style="font-size:14px"></td>  
	        </tr>
        </table>
        <br>
        <table  width="700" cellpadding="0" align="center" cellspacing="0" border="0" style="margin-top:15px;">
        	<tr>
	            <td width="25" ></td>
	            <td width="650" align="right">Date : <strong><? echo $submit_date; ?></strong></td>
	            <td width="25"></td>
	        </tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">To,</td>
	            <td width="25" ></td>
	        </tr>
	        <tr>
	        	<td width="25" ></td>
	        	<td colspan="2" ><strong>The Manager & Head Of Branch</strong></td>
	        </tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">
	            <?
	            echo $bank_data_arr[$result[0][csf("lien_bank")]]["bank_name"]."<br>".$bank_data_arr[$result[0][csf("lien_bank")]]["branch_name"].",<br>".$bank_data_arr[$result[0][csf("lien_bank")]]["address"];
				?>
	            </td>
	            <td width="25" ></td>

	        </tr>
	        <tr>
	        	<td colspan="3" height="30"></td>
	        </tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">
	            Subject: <b>Submission Export Documents</b>
	            </td>
	            <td width="25" ></td>
	        </tr>
	        <tr>
	        	<td colspan="3" height="20"></td>
	        </tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left"> Dear Sir, </td>
	            <td width="25" ></td>
	        </tr>
	        <tr>
	        	<td colspan="3" height="5"></td>
	       	</tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">
	            With due respect we would like to imfrom you that we are sumitting here with the following documents for your kind information and necessary action.
	            </td>
	            <td width="25" ></td>
	        </tr>
	        <tr>
	        	<td colspan="3" height="15"></td>
	        </tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">
	            	<table cellpadding="0" align="center" cellspacing="0" border="1" width="700" style="font-size:16px;">
	                	<thead>
	                    	<tr>
	                    		<th width="30">Sl.</th>
	                        	<th width="110">Invoice No.</th>
	                            <th width="120">Value in USD</th>
	                            <th width="110">Exp. No.</th>
	                            <th width="80">Exp. Date</th>
	                            <th width="200">Remarks</th>
	                        </tr>
	                    </thead>
	                    <tbody>
	                    <?
						$i=1;
						//print_r($result);
						foreach($result as $row)
						{
							if($row[csf("exp_form_date")]!="" && $row[csf("exp_form_date")]!='0000-00-00') $exe_form_date=change_date_format($row[csf("exp_form_date")]); else $exe_form_date="&nbsp;";
							if($row[csf("bl_date")]!="" && $row[csf("bl_date")]!='0000-00-00') $bl_date=change_date_format($row[csf("bl_date")]); else $bl_date="&nbsp;";
							if($row[csf("invoice_date")]!="" && $row[csf("invoice_date")]!='0000-00-00') $inv_date=change_date_format($row[csf("invoice_date")]); else $bl_date="&nbsp;";
							?>
	                    	<tr>
	                            <td width="30" valign="top"><div style="width:30px; word-wrap:break-word;"><? echo $i; ?>
								</div></td>
	                            <td width="110" valign="top"><div style="width:110px;; word-wrap:break-word;"><? echo $row[csf("invoice_no")];?> </div></td>
	                            <td width="120" valign="top" align="right"><div style="width:120px;; word-wrap:break-word;"><? echo number_format($row[csf("invoice_value")],2,'.',''); $tot_value+=$row[csf("invoice_value")];?></div></td>
	                            <td width="110" valign="top"><div style="width:110px;; word-wrap:break-word;"><? echo $row[csf("exp_form_no")];?></div></td>
	                            <td width="80" valign="top" align="center" ><div style="width:80px;; word-wrap:break-word;"><? echo $exe_form_date; ?></div></td>
	                            <td width="200" valign="top" ><div style="width:200px;; word-wrap:break-word;"><? echo $row[csf("remarks")]; ?></div></td>
	                        </tr>
	                        <?
							$i++;
						}
						?>
						<tr> 
							<td align="right" colspan="2" ><strong>Total:</strong></td>
							<td align="right"><? echo number_format($tot_value,2,'.',''); ?>&nbsp;</td>
							<td align="right" colspan="3" >&nbsp;</td>
						</tr>
	                    </tbody>
	                </table>
	            </td>
	            <td width="25" ></td>
	        </tr>
	        <tr style="height:150px;">
	        	<td colspan="3" height="15"></td>
	        </tr>
	 		<tr>
	            <td width="25" ></td>
	            <td width="650" align="left">
	           	We always appriciated for your kind and early co-operation.			
	            </td>
	            <td width="25" ></td>
	        </tr> 
	        <tr>
	        	<td colspan="3" height="15"></td>
	       	</tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">
	            Thanking You,
	            </td>
	            <td width="25" ></td>
	        </tr>
	        <tr>
	        	<td colspan="3" height="15"></td>
	       	</tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">
	            Yours faithfully,
	            </td>
	            <td width="25" ></td>
	        </tr>	       
	    </table>
	    <table width="699" cellpadding="0" align="center" cellspacing="0" border="0" style="margin-top:15px; alignment-baseline: baseline; " id="foot" >
	    	<tfoot>
	    	<tr>
	    		<td colspan="2"><hr></td>
	    	</tr>
	    	<? $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_id"); 
				foreach ($nameArray as $res)
				{ 
				
					$address.= "Plot :".$res[csf('plot_no')]." Road :".$res[csf('road_no')]."<br> Block :".$res[csf('block_no')]." ".$res[csf('city')]." ".$res[csf('zip_code')]; 
					$email.= $res[csf('email')]."<br>";
					$website.= $res[csf('website')];
					
				}?>
	    	<tr>
	    		<td width="450" > <? echo $address; ?></td>
	    		<td width="250" ><a href="https://www.google.com"><? echo "E-mail : ".$email." Website : ".$website; ?></a></td>
	    	</tr>
	    	</tfoot>
	    </table>
	    <?
	}

	if($type==3) // Bill of Exchange
	{
		$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
		$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
		if($result[0][csf("submit_date")]!="" && $result[0][csf("submit_date")]!='0000-00-00') $submit_date=change_date_format($result[0][csf("submit_date")]); else $submit_date="&nbsp;";

	    			
		foreach($result as $row)
		{
			if($row[csf("is_lc")]==1)
			{
				$lc_sc_no = $export_lc_no_arr[$row[csf("lc_sc_id")]]["export_lc_no"]; 
				$lc_sc_date = change_date_format($export_lc_no_arr[$row[csf("lc_sc_id")]]["lc_date"]); 

				$issuing_bank = $export_lc_no_arr[$row[csf("lc_sc_id")]]["issuing_bank_name"];
				$pay_term = $export_lc_no_arr[$row[csf("lc_sc_id")]]["pay_term"];
				
				if ($pay_term==1) 
				{
					$lcSc_pay_term='At Sight';
				}
				if ($pay_term==2)
				{
					$lcSc_tenor = $export_lc_no_arr[$row[csf("lc_sc_id")]]["tenor"].' days after sight/from BL date/Form Ship Date/From Invoice Date ';
				}
			}
			else
			{
				$lc_sc_no = $export_sc_no_arr[$row[csf("lc_sc_id")]]["contract_no"]; 
				$lc_sc_date = change_date_format($export_sc_no_arr[$row[csf("lc_sc_id")]]["contract_date"]);

				$pay_term=$export_sc_no_arr[$row[csf("lc_sc_id")]]["pay_term"];
				
				if ($pay_term==1) 
				{
					$lcSc_pay_term='At Sight';
				}
				if ($pay_term==2)
				{
					$lcSc_tenor = $export_sc_no_arr[$row[csf("lc_sc_id")]]["tenor"].' days after sight/from BL date/Form Ship Date/From Invoice Date ';
				}
			}
		}
		?>
		<style type="text/css">
			.opacity_1
			{
				opacity:0.3; 
				position: absolute; 
				font-size: 400px; 
				left: 40%; 
				top: 20%;
			}	
			.opacity_2
			{
				opacity:0.3; 
				position: absolute; 
				font-size: 400px; 
				left: 40%; 
				top: 230%;
			}
			.opacity_3
			{
				opacity:0.3; 
				position: absolute; 
				font-size: 400px; 
				left: 40%; 
				top: 20%;
			}
			
			
			
			
			@media print {
				.page-break	{ display: block; page-break-after: always;}
			}
			
			#table_1,#table_2,#table_3{  background-position: center;background-repeat: no-repeat; }
			#table_1{background-image:url(../../../img/bg-1.jpg);}
			#table_2{background-image:url(../../../img/bg-2.jpg); }
			#table_3{background-image:url(../../../img/bg-3.jpg);}
			
		</style>
        
        
		<!-- <style>
	        .a4size {
	           width: 700px;
	           height: 950px;
	           line-height: 5px;
	           font-family: Cambria, Georgia, serif;
	        }
	        @media print {
	        .a4size
	        { 
	        	font-family: Cambria;font-size: 18px;
	        	margin: 100px 120PX 54px 36px; 
	        }
	        size: A4 portrait;
	        }
	        .styTd
	        {
	        	font-size:22px; 
	        	font-weight:bold; 
	        	font-family: Edwardian Script ITC, Cambria;
	        } 
	    </style> -->
		<?
	 $copy_no=array(1,2,3); //for Dynamic Copy here 
	 foreach($copy_no as $cid)
	 {
		?>
		
	  	<div class="page-break"> 
	    	<table width="700" cellpadding="0" align="center" cellspacing="0" border="0" id="table_<? echo $cid;?>">
                <tr>
                    <td colspan="5" align="center" style="font-size:30px; font-weight:bold; font-family: Old English Text MT; color: red;"><? echo "Bill of Exchange"; ?></td>
		        </tr>
		        <tr>
		            <td colspan="5" align="center" style="font-size:15px; font-weight:bold"><? echo $company_library[$company_id]; ?></td>
		        </tr>
		        <tr>
		            <td colspan="5" align="center">&nbsp;</td>
		        </tr>

                <tr border="0" style="font-size: 13px;">
		    		<td width="15%" style="height: 40px;" valign="top"><strong>Invoice No:</strong>&nbsp;</td>
		    		<td width="35%" colspan="3" style="border: 1px dotted;height: 40px; word-break: break-all;" valign="top">&nbsp;<? echo chop($all_invoice_no,", "); ?></td> 
		    	</tr>
                
		    	<tr style="font-size: 13px;">
		    		<td><strong>Date:</strong></td> 
		    		<td colspan="3" style="border-left: 1px dotted; border-right: 1px dotted; border-bottom: 1px dotted; line-height: 15px;">&nbsp;<? echo chop($all_invoice_date,","); ?></td>
		    	</tr>
		    	<tr>
		    		<td height="15"colspan="5">&nbsp;</td> 
		    	</tr>
                
                
		    	<tr>
		    		<td style="font-family: Lucida Calligraphy, Cambria;font-size:13px;">For</td> 
		    		<td colspan="1" style="font-size:13px; background:#FFD966; border:1px dotted;"><strong>&nbsp; <? echo number_format($total_inv_value,4).' '.$currency[$lcSc_currency]; ?></strong></td>
                    <td colspan="2"></td>
		    	</tr> 
                
                
		    	<tr border="0" style="font-size:13px;">  
		    		<td colspan="4" style="text-align: justify; height: 30px;">
                    <span style="font-family: Lucida Calligraphy, Cambria;">Tenor of draft</span>&nbsp;&nbsp;&nbsp;<? echo $lcSc_pay_term.$lcSc_tenor; ?> 
                    <span style="float: right;">
                        <span style="font-family: Lucida Calligraphy, Cambria;">Please pay this </span>
                        <span style="color: red; font-family: Lucida Calligraphy, Cambria; font-size:20px;"><? echo $cid;?><?php if($cid==1) { echo 'st';} elseif($cid==2) { echo 'nd';} else{ echo 'rd';} ?>
                        </span>
                    </span>
		    		
                    <div style="height:5px;"></div>
                    <span style="font-family: Lucida Calligraphy, Cambria;">
		    		of Exchange (Second of the same tenure and date unpaid) to the order of </span><span style="font-family: Cambria;font-size:15px;font-weight:none;"> <? echo $bank_data_arr[$result[0][csf("lien_bank")]]["bank_name"].' '.$bank_data_arr[$result[0][csf("lien_bank")]]["address"]; ?></span>
                    
                    <div style="height:40px;"></div>
                    
		    		<span style="font-family: Lucida Calligraphy, Cambria;">The sum of</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <? 
					$total_inv_value=explode('.', $total_inv_value);
					if($lcSc_currency==2){echo number_to_words($total_inv_value[0],"USD", "Cent");}
					else if($lcSc_currency==1){echo number_to_words($total_inv_value[0],"TAKA", "Paisa");}
					else if($lcSc_currency==3){echo number_to_words($total_inv_value[0],"EURO", "Cent");}
					else if($lcSc_currency==4){echo number_to_words($total_inv_value[0],"CHF", "Centimes");}
					else if($lcSc_currency==5){echo number_to_words($total_inv_value[0],"SGD", "Cents ");}
					//echo number_to_words($total_inv_value).' '.$currency[$lcSc_currency]; ?> <span style="float: right;"><span style="font-family: Lucida Calligraphy, Cambria;">only</span></span> 
		    		<br><span style="font-family: Lucida Calligraphy, Cambria;">
		    		Value received and charge the same to the account of</span> <? echo chop($total_carton_qnty,","); ?>
		    		</td> 
		    	</tr> 
		    	<tr border="0" style="font-size: 13px;">
		    		<td><span style="font-family: Lucida Calligraphy, Cambria;">Shipped by</span> &nbsp;</td>
		    		<td colspan="3"><? echo chop($feeder_vessel,","); ?></td> 
		    	</tr>
		    	<tr>
		    		<td height="10" colspan="5"></td> 
		    	</tr>
		    	<tr border="0" style="font-size: 13px;">
		    		<td style="height: 40px;" valign="top"><strong>BL No:</strong>&nbsp;</td>
		    		<td colspan="3"  style="border:1px dotted;height: 40px;"><? echo chop($all_bl_no,","); ?></td> 
		    	</tr>
		    	<tr border="0" style="font-size: 13px;">
		    		<td><strong>Date:</strong></td> 
		    		<td colspan="3" style="border-left: 1px dotted; border-right: 1px dotted; border-bottom: 1px dotted;"><? echo chop($all_bl_date,","); ?></td>
		    	</tr>
		    	<tr>
		    		<td height="10" colspan="5"></td> 
		    	</tr>
		    	<tr border="0" style="font-size: 13px;">
		    		<td colspan="4" style="text-decoration: underline;"><strong><span style="font-family: Lucida Calligraphy, Cambria;">Drawn under</span></strong></td> 
		    	</tr>

		    	<tr>
		    		<td style="font-size: 13px;"><strong>L/C Number:</strong></td> 
						<td width="65%" style="border:1px dotted; font-size: 13px;">&nbsp;<? echo $lc_sc_no; ?></td>&nbsp;&nbsp;&nbsp;
		    		<td style="font-size: 13px;">&nbsp;&nbsp;Date:</td> 
		    		<td style="border: 1px dotted; font-size: 13px;"><? echo $lc_sc_date; ?></td>
		    	</tr> 
		    	<tr>
		    		<td height="20" colspan="5"></td> 
		    	</tr>
		    	<tr style="line-height: 20px; font-size: 13px;">
		    		<td width="6.8%"><span style="font-family: Lucida Calligraphy, Cambria;">To:</span></td> 
		    		<td width="34%" colspan="3"><? echo $issuing_bank; ?></td>
		    	</tr>
                <tfoot>
                <td colspan="6"></td>
                </tfoot>
	    	</table>
            
      	</div>
	    <?
	 }
	
	
	}
	if($type==4) // Print later 3
	{
		?>
	    <br>
	    <table width="700" cellpadding="0" align="left" cellspacing="0" border="0">
	        <tr>
	        	<td colspan="3" height="110"></td>
	        </tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">
					<div class="date_block">
						<b>Dated : <? echo change_date_format($result[0][csf("submit_date")]); ?> </b> 
					</div>
					<div class="buyer_block">
						<!-- <b>Buyer: <? echo $buyer_name_arr[$result[0][csf("buyer_id")]]; ?></b><br/>
						<strong>INV#&nbsp;&nbsp;<? echo chop($all_invoice_no,","); ?></strong> -->
					</div>
					</td>
	            <td width="25"></td>
	        </tr>
	        <tr>
	            <td width="25" colspan="3" >&nbsp;</td>
	        </tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left"></td>
	        </tr>
	        <tr>
	        	<td colspan="3" ></td>
	        </tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">
	            <?
					$leate_header_data="";
					if($result[0][csf("is_lc")]==1)
					{
						$contact_person_arr=explode(",",$bank_data_arr[$export_lc_no_arr[$result[0][csf("lc_sc_id")]]["lien_bank"]]["contact_person"]);
						$contact_person="";
						foreach($contact_person_arr as $val)
						{
							$contact_person.=$val."<br>";
						}
						$leate_header_data=$contact_person.$bank_data_arr[$export_lc_no_arr[$result[0][csf("lc_sc_id")]]["lien_bank"]]["bank_name"]."<br>".$bank_data_arr[$export_lc_no_arr[$result[0][csf("lc_sc_id")]]["lien_bank"]]["branch_name"]."<br>".$bank_data_arr[$export_lc_no_arr[$result[0][csf("lc_sc_id")]]["lien_bank"]]["address"];
						$consinee_all=explode(",",$export_lc_no_arr[$result[0][csf("lc_sc_id")]]["consignee"]);
					}
					else
					{
						$contact_person_arr=explode(",",$bank_data_arr[$export_sc_no_arr[$result[0][csf("lc_sc_id")]]["lien_bank"]]["contact_person"]);
						$contact_person="";
						foreach($contact_person_arr as $val)
						{
							$contact_person.=$val."<br>";
						}
						$leate_header_data=$contact_person.$bank_data_arr[$export_sc_no_arr[$result[0][csf("lc_sc_id")]]["lien_bank"]]["bank_name"]."<br>".$bank_data_arr[$export_sc_no_arr[$result[0][csf("lc_sc_id")]]["lien_bank"]]["branch_name"]."<br>".$bank_data_arr[$export_sc_no_arr[$result[0][csf("lc_sc_id")]]["lien_bank"]]["address"];
						$consinee_all=explode(",",$export_sc_no_arr[$result[0][csf("lc_sc_id")]]["consignee"]);
					}
					
					echo $leate_header_data;
					
					//print_r($consinee_all);
				?>
	           
	            </td>
	            <td width="25" ></td>
	        </tr>
	        <tr>
	        	<td colspan="3" height="30"></td>
	        </tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">
	            <strong>Sub: Submission of Export Documents under the <b>LC/SC NO: <? 
								if($result[0][csf("is_lc")]==1)
								{
									echo $export_lc_no_arr[$result[0][csf("lc_sc_id")]]["export_lc_no"]."<br>";
								}
								else
								{
									echo $export_sc_no_arr[$result[0][csf("lc_sc_id")]]["contract_no"]."<br>";
								}
								?> Date: <? 
								if($result[0][csf("is_lc")]==1)
								{
									
									echo change_date_format($export_lc_no_arr[$result[0][csf("lc_sc_id")]]["lc_date"]);
								}
								else
								{
									
									echo change_date_format($export_sc_no_arr[$result[0][csf("lc_sc_id")]]["contract_date"]);
								}
								//</b> FOR THE AMOUNT OF <b>US$ <? echo number_format($total_inv_value,2,'.',''); </b>
								?>  for negotiation/collection. </strong>
				
	            </td>
	            <td width="25" ></td>
	        </tr>
	        <tr>
	        	<td colspan="3" height="20"></td>
	        </tr>
	        <!-- <tr>
	            <td width="25" ></td>
	            <td width="650" align="left"> Dear Sir, </td>
	            <td width="25" ></td>
	        </tr> -->
	        <tr>
	        	<td colspan="3" height="5"></td>
	       </tr>
           <tr>
	        	<td colspan="3" height="5"></td>
	       </tr>
           <tr>
	        	<td colspan="3" height="15"></td>
	       		 </tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left" style="text-align:justify;">
				Please to inform you that we are submitting export documents against above export Lc/Contract for
	           <?	//<strong><? echo $total_invoice_quantity </strong><b>
			   						
			    				    $all_order_arr=explode(",",$result[0][csf("all_order_no")]);
									$all_po="";
									foreach($all_order_arr as $order_id)
									{
										$order_uom.=$order_inv_array[$order_id]["order_uom"].", ";
									}
									 $orderuom=explode(",",chop($order_uom," , "));
									 $list_explode = array_unique(array_map('trim', $orderuom));
									 $resulter = array_unique($list_explode);
									//echo $order_uom = implode(",",array_filter($list_explode));
									?> <b>US$ <? echo number_format($total_inv_value,2,'.',''); ?></b> to you for getting payment from buyer end. We are submitting require export documents as per terms of Lc/Contract. After receiving the payment from buyer end you may disburse the amount to our various accounts for adjustment of BTB Lc liability & meet-up overhead expenses of the company.
 
	            </td>
	            <td width="25" ></td>
	        </tr>
            
			<tr>
	            <td width="25" ></td>
	            <td width="400" align="left">
	            	<table cellpadding="0" align="left" cellspacing="0"  width="400" style="font-size:16px; margin-top: 15px;">
	                    <tbody>
	                    <?
						 $sql = "select mst_id, terms_id, terms_value from doc_submission_terms where terms_value is not null and terms_value not in ('0','0+0') and mst_id=".$mst_id."";      
						$i=1;     
	            		$data_array=sql_select($sql);
						foreach($data_array as $row)
						{
							?>
	                    	<tr>
	                            <td width="30" align="center"><? echo $i;?>.</td>
	                            <td width="100" align="left"><? echo $document_set[$row[csf("terms_id")]];?>&nbsp<? echo $row[csf("terms_value")]; ?></td>
	                        </tr>
	                        <?
							$i++;
						}
						?>
	                    </tbody>
	                </table>
	            </td>
	            <td width="25" ></td>
	        </tr>
	                
            <tr>
           		 <td width="25" ></td>
	            <td width="650" align="left">
	            
	            </td>
	            <td width="25" ></td>
            </tr>
            <tr><td colspan="3" height="15"></td></tr>
            <tr>
				<td width="25" ></td>
				<td width="650" align="left">
					<b>Buyer: <? echo $buyer_name_arr[$result[0][csf("buyer_id")]]; ?></b><br/>
					<strong>INV#&nbsp;&nbsp;<? echo chop($all_invoice_no,", "); ?></strong>
				</td>
				<td width="25" ></td>
			</tr>
            <tr><td colspan="3" height="20"></td></tr>
            <tr>
				<td width="25" ></td>
				<td width="650" align="left">
				Your kind co-operation will be highly appreciated in this regards.
				</td>
				<td width="25" ></td>
			</tr>
	        <tr>
	        	<td colspan="3" height="15"></td>
	        </tr>
            <tr>
	        	<td colspan="3" height="15"></td>
	        </tr>
				<? 
				if(1==$msg) 
				{?>
            	<tr>
           		<td width="25" ></td>
	            <td width="650" align="left">
					<strong><? echo "Note: Please send the documents to the buyer bank under discounting as per Lc clause.";?> </strong>
	            </td>
	            <td width="25" ></td>
                </tr>
				<? 
				}
	           ?>
            <tr>
            	<td colspan="3" height="15"></td>
            </tr>
            <tr>
           	 <td colspan="3" height="15"></td>
            </tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">
	            	Thanking yours,
	            </td>
	            <td width="25" ></td>
	        </tr>
			<tr>
           	 <td colspan="3" height="10"></td>
            </tr>
	         <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">
	            	<span style="margin-top: 15px;">Sincerely yours</span>
	            </td>
	            <td width="25" ></td>
	        </tr>
			<tr>
           	 <td colspan="3" height="25"></td>
            </tr>
	        <tr>
				<td width="25" ></td>
	        	<td width="650" height="80">
				<strong style="text-decoration: overline;" >Authorized Signature</strong>
				</td>
				<td width="25" ></td>
	        </tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left"></td>
	            <td width="25" ></td>
	        </tr>
	       <tr>
	        	<td colspan="3" height="50"></td>
	       </tr>
	    </table>
	    <?
	}
	if($type==5) // Print later 4
	{
		?>
	    <br>
	    <table width="700" cellpadding="0" align="left" cellspacing="0" border="0">
	        <tr>
	        	<td colspan="3" height="110"></td>
	        </tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">Dated : <? echo change_date_format($result[0][csf("submit_date")]); ?> </td>
	            <td width="25" ></td>
	        </tr>
	        <tr>
	            <td width="25" colspan="3" >&nbsp;</td>
	        </tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">To</td>
	        </tr>
	        <tr>
	        	<td colspan="3" ></td>
	        </tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">
	            <?
					$leate_header_data="";
					if($result[0][csf("is_lc")]==1)
					{
						$contact_person_arr=explode(",",$bank_data_arr[$export_lc_no_arr[$result[0][csf("lc_sc_id")]]["lien_bank"]]["contact_person"]);
						$contact_person="";
						foreach($contact_person_arr as $val)
						{
							$contact_person.=$val."<br>";
						}
						$leate_header_data=$contact_person.$bank_data_arr[$export_lc_no_arr[$result[0][csf("lc_sc_id")]]["lien_bank"]]["bank_name"]."<br>".$bank_data_arr[$export_lc_no_arr[$result[0][csf("lc_sc_id")]]["lien_bank"]]["branch_name"]."<br>".$bank_data_arr[$export_lc_no_arr[$result[0][csf("lc_sc_id")]]["lien_bank"]]["address"];
						$consinee_all=explode(",",$export_lc_no_arr[$result[0][csf("lc_sc_id")]]["consignee"]);
					}
					else
					{
						$contact_person_arr=explode(",",$bank_data_arr[$export_sc_no_arr[$result[0][csf("lc_sc_id")]]["lien_bank"]]["contact_person"]);
						$contact_person="";
						foreach($contact_person_arr as $val)
						{
							$contact_person.=$val."<br>";
						}
						$leate_header_data=$contact_person.$bank_data_arr[$export_sc_no_arr[$result[0][csf("lc_sc_id")]]["lien_bank"]]["bank_name"]."<br>".$bank_data_arr[$export_sc_no_arr[$result[0][csf("lc_sc_id")]]["lien_bank"]]["branch_name"]."<br>".$bank_data_arr[$export_sc_no_arr[$result[0][csf("lc_sc_id")]]["lien_bank"]]["address"];
						$consinee_all=explode(",",$export_sc_no_arr[$result[0][csf("lc_sc_id")]]["consignee"]);
					}
					
					echo $leate_header_data;
					
					//print_r($consinee_all);
				?>
	           
	            </td>
	            <td width="25" ></td>
	        </tr>
	        <tr>
	        	<td colspan="3" height="30"></td>
	        </tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">
	            Subject: <u>Submission of Export documents for <? echo $currency[$result[0][csf("lc_currency")]]." : ". number_format($total_inv_value,2); ?></u><br>
	            Buyer&nbsp;&nbsp;: 
				<? 
				/*$buyer_name_arr;
				
				$buyer_all="";
				foreach($consinee_all as $buyer_id)
				{
					$buyer_all=$buyer_name_arr[$buyer_id].", ";
				}
				$buyer_all=chop($buyer_all," , ");
				echo $buyer_all;*/
				
				
				echo $buyer_name_arr[$result[0][csf("buyer_id")]];
				?>.
	            </td>
	            <td width="25" ></td>
	        </tr>
	        <tr>
	        	<td colspan="3" height="20"></td>
	        </tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left"> Dear Sir, </td>
	            <td width="25" ></td>
	        </tr>
	        <tr>
	        	<td colspan="3" height="5"></td>
	       </tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">
	            We enclose herewith the following export documents detail of which are given below : 
	            </td>
	            <td width="25" ></td>
	        </tr>
	        <tr>
	        	<td colspan="3" height="15"></td>
	        </tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">
	            	<table cellpadding="0" align="left" cellspacing="0" border="1" width="650" style="font-size:13px;">
	                	<thead>
	                    	<tr>
	                        	<th width="110">Lc/Sc. No <br> Date</th>
	                            <th width="120">Order No</th>
	                            <th width="110">Invoice No <br> Date</th>
	                            <th width="110">Exp No <br> Date</th>
	                            <th width="40">Quantity</th>
                                 <th width="60">UOM</th>
	                            <th width="70">Value <br> USD</th>
	                        </tr>
	                    </thead>
	                    <tbody>
	                    <?
						$i=1;
						foreach($result as $row)
						{
							if($row[csf("exp_form_date")]!="" && $row[csf("exp_form_date")]!='0000-00-00') $exe_form_date=change_date_format($row[csf("exp_form_date")]); else $exe_form_date="&nbsp;";
							if($row[csf("bl_date")]!="" && $row[csf("bl_date")]!='0000-00-00') $bl_date=change_date_format($row[csf("bl_date")]); else $bl_date="&nbsp;";
							if($row[csf("invoice_date")]!="" && $row[csf("invoice_date")]!='0000-00-00') $inv_date=change_date_format($row[csf("invoice_date")]); else $bl_date="&nbsp;";
							?>
	                    	<tr>
	                        	<td width="110" valign="top"><div style="width:110px; word-wrap:break-word;">
								<? 
								if($row[csf("is_lc")]==1)
								{
									echo $export_lc_no_arr[$row[csf("lc_sc_id")]]["export_lc_no"]."<br>";
									echo change_date_format($export_lc_no_arr[$row[csf("lc_sc_id")]]["lc_date"]);
								}
								else
								{
									echo $export_sc_no_arr[$row[csf("lc_sc_id")]]["contract_no"]."<br>";
									echo change_date_format($export_sc_no_arr[$row[csf("lc_sc_id")]]["contract_date"]);
								}
								?></div>
	                            </td>
	                            <td width="120" valign="top"><div style="width:120px;; word-wrap:break-word;">
								<? 
								//echo $order_inv_array[$row[csf("inv_id")]]["po_no"];
								if($row[csf("import_btb")] == 1)
								{
									$item_category_id=0;
									if($row[csf("is_lc")] == 1)
									{
										$item_category_id = return_field_value("export_item_category","com_export_lc","id=".$row[csf("lc_sc_id")]."");
									}
									if($item_category_id == 2)
									{
										$sales_order_arr =  sql_select("select work_order_no from com_export_lc_order_info where com_export_lc_id =". $row[csf("lc_sc_id")]);

										foreach ($sales_order_arr as $value) 
										{
											if($po_numbers=="") $po_numbers=$value[csf("work_order_no")]; else $po_numbers.=",".$value[csf("work_order_no")];
										}
										$all_po = implode(",",array_filter(array_unique(explode(",",$po_numbers))));
									}
								}
								else
								{
									$all_order_arr=explode(",",$row[csf("all_order_no")]);
									$all_po="";
									foreach($all_order_arr as $order_id)
									{
										$all_po.=$order_inv_array[$order_id]["po_no"].", ";
									}
									$all_po=chop($all_po," , ");
								}
								echo $all_po;
								?></div></td>
	                            <td width="110" valign="top"><div style="width:110px;; word-wrap:break-word;"><? echo $row[csf("invoice_no")]."<br>".$inv_date;?> </div></td>
	                            <td width="110" valign="top"><div style="width:110px;; word-wrap:break-word;"><? echo $row[csf("exp_form_no")]."<br>".$exe_form_date;?></div></td>
	                            <td width="40" valign="top" align="right"><div style="width:40px;; word-wrap:break-word;"><? echo number_format($row[csf("invoice_quantity")],2); $tot_qty+=$row[csf("invoice_quantity")];?></div></td>
                                
                                <td width="60" valign="top" align="center"><div style="width:60px;; word-wrap:break-word;"><? 
								$order_uom="";
								
								if($row[csf("import_btb")]== 1)
								{
									
									echo $unit_of_measurement[$export_lc_uom_arr[$row[csf("lc_sc_id")]]["uom"]];
								}
								else
								{
									$all_order_arr=explode(",",$row[csf("all_order_no")]);
									$all_po="";
									foreach($all_order_arr as $order_id)
									{
										
										$order_uom.=$order_inv_array[$order_id]["order_uom"].", ";
										
									}
									
									//$all_po = implode(",",array_filter(array_unique(explode(",",$po_numbers))));
									 $orderuom=explode(",",chop($order_uom," , "));
									 $list_explode = array_unique(array_map('trim', $orderuom));
									 $resulter = array_unique($list_explode);
									echo $order_uom = implode(",",array_filter($list_explode));
									
										
								}
								
								?></div></td>
                                
	                            <td width="70" valign="top" align="right">
								<div style="width:70px;; word-wrap:break-word;">
								<? 
								echo $currency_sign_array[$result[0][csf("lc_currency")]].number_format($row[csf("invoice_value")],2,'.',''); $tot_value+=$row[csf("invoice_value")];?></div>
								</td>
	                            
	                        </tr>
	                        <?
							$i++;
						}
						?>
						<tr> 
							<td align="right" colspan="4" ><strong>Total:</strong></td>
							<td align="right"><? echo $tot_qty; ?>&nbsp;</td>
                            <td align="right">&nbsp;</td>
							<td align="right"><? echo $currency_sign_array[$result[0][csf("lc_currency")]].number_format($tot_value,2,'.',''); ?>&nbsp;</td>
						</tr>
	                    </tbody>
	                </table>
	            </td>
	            <td width="25" ></td>
	        </tr>
	        <tr>
	        	<td colspan="3" height="15"></td>
	        </tr>
	    
		   <? 
		   	$sql = "select mst_id, terms_id, terms_value from doc_submission_terms where terms_value is not null and terms_value not in ('0','0+0') and mst_id=".$mst_id."";       
			   $data_array=sql_select($sql);

			   //print_r($data_array[0][csf("terms_id")]);
		   if ($data_array[0][csf("terms_id")] != "") {
			?>
			<tr>
	            <td width="25" ></td>
	            <td width="400" align="left">
	            	<table cellpadding="0" align="left" cellspacing="0" border="1" width="400" style="font-size:16px;">
	                	<thead>
	                    	<tr>
	                        	<th width="30">Sl</th>
	                            <th width="100">Particulars of Documents</th>
	                            <th width="60">No. Of Copy</th>
	                        </tr>
	                    </thead>
	                    <tbody>
	                    <?
						 
						 $i=1;     
						foreach($data_array as $row)
						{
							?>
	                    	<tr>
	                            <td width="30" align="center"><b><? echo $i;?></b></td>
	                            <td width="100" align="left"><b><? echo $document_set[$row[csf("terms_id")]];?></b></td>
	                            <td width="60" align="center"><b><? echo $row[csf("terms_value")]; ?></b></td>
	                        </tr>
	                        <?
							$i++;
						}
						?>
	                    </tbody>
	                </table>
	            </td>
	            <td width="25" ></td>
	        </tr>
			<?
		   } 
		   ?>
	        <tr>
	        	<td colspan="3" height="15"></td>
	        </tr>
	 		<tr>
	            <td width="25" ></td>
	            <td width="650" align="left" style="text-align:justify;">
	            Please negotiate/purchase the above export documents and proceed amount to credit our account, which is maintain with you.
	            </td>
	            <td width="25" ></td>
	        </tr> 
	        <tr>
	        	<td colspan="3" height="10"></td>
	        </tr>
			<?
			if ($remarks !="") {
			?>
			<tr>
	        	<td colspan="3" height="5"></td>
	        </tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">
	            <? echo "<strong>Note: </strong> ".$remarks;?>
	            </td>
	            <td width="25" ></td>
	        </tr>
	        <tr>
	        	<td colspan="3" height="5"></td>
	       	</tr>
			<?
			} 	
			?>
	        
	        <tr>
	        	<td colspan="3" height="15"></td>
	        </tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">
	            Thanking You
	            </td>
	            <td width="25" ></td>
	        </tr>
	        <tr>
	        	<td colspan="3" height="25"></td>
	       </tr>
	         <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">
	            Yours faithfully
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
				
				echo $company_name;
				 
				?></td>
	            <td width="25" ></td>
	        </tr>
	       <tr>
	        	<td colspan="3" height="50"></td>
	       </tr>
	       <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">
	            Authorized signature
	            </td>
	            <td width="25" ></td>
	        </tr>
	    </table>
	    <?
	}

	if ($type==6)
	{
		if ($db_type==2)
		{
			$sql = "SELECT a.bank_ref_no, a.submit_date, a.lc_currency, b.id, b.doc_submission_mst_id, b.invoice_id, b.submission_dtls_id, b.is_lc, b.lc_sc_id, b.bl_no, b.bl_date, b.invoice_date, b.net_invo_value, b.all_order_no, c.invoice_no, c.id as dtls_id, c.import_btb, c.exp_form_no 
			from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c 
			where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.id=$mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
			order by to_number(regexp_substr(c.invoice_no, '\d+'))";
		}
		else
		{
			$sql = "SELECT a.bank_ref_no, a.submit_date, a.lc_currency, b.id, b.doc_submission_mst_id, b.invoice_id, b.submission_dtls_id, b.is_lc, b.lc_sc_id, b.bl_no, b.bl_date, b.invoice_date, b.net_invo_value, b.all_order_no, c.invoice_no, c.id as dtls_id, c.import_btb, c.exp_form_no 
			from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c 
			where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.id=$mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
			order by c.invoice_no";
		}	
		//echo $sql;
		$sql_res = sql_select($sql);
		foreach ($sql_res as $val)
		{
			if ($val[csf("is_lc")]==1)
			{
				$export_lc_arr[$val[csf("lc_sc_id")]] = $val[csf("lc_sc_id")];
			}
			else
			{
				$sales_contract_arr[$val[csf("lc_sc_id")]] = $val[csf("lc_sc_id")];
			}
			$total_value += $val[csf("net_invo_value")];
		}

		$export_lc_ids = implode(",",array_keys($export_lc_arr));
		$sales_contract_ids = implode(",",array_keys($sales_contract_arr));

		if ($export_lc_ids != "")
		{
			$export_sql= "select id, export_lc_no, lc_date from com_export_lc where id in($export_lc_ids)";
			$export_sql_res=sql_select($export_sql);
			foreach ($export_sql_res as $val) {
				$export_lc_no_arr[$val[csf('id')]] = $val[csf('export_lc_no')];
				$export_lc_date_arr[$val[csf('id')]] = $val[csf('lc_date')];
			}
			//$export_lc_no_arr = return_library_array("select id, export_lc_no from com_export_lc where id in($export_lc_ids)","id","export_lc_no");
		}

		if ($sales_contract_ids != "")
		{
			$sales_contract_sql= "select id, contract_no, contract_date from com_sales_contract where id in($sales_contract_ids)";
			$sales_contract_sql_res=sql_select($sales_contract_sql);
			foreach ($sales_contract_sql_res as $val) {
				$sales_contract_no_arr[$val[csf('id')]] = $val[csf('contract_no')];
				$sales_contract_date_arr[$val[csf('id')]] = $val[csf('contract_date')];
			}
			//$sales_contract_no_arr = return_library_array("select id, contract_no from com_sales_contract where id in($sales_contract_ids)","id","contract_no");
		}
		$lc_sc_currency = $currency_sign_array[$sql_res[0][csf('lc_currency')]];		
		?>
		<div style="width: 950px">
			<p><h1 align="center">FDBP SUMMERY</h1></p>
			<table class="rpt_table" border="1" rules="all" width="950" cellpadding="0" cellspacing="0">
				<tr style="background-color: #BBBBBB;">
					<th width="200">Bank Ref/ Bill No :</th>
					<th width="200"><? echo $sql_res[0][csf('bank_ref_no')]; ?></th>
					<th width="100">Total Value:</th>
					<th width="200"><? echo $lc_sc_currency.number_format($total_value,2); ?></th>
					<th width="150">Sub. Date :</th>
					<th width="100"><? echo change_date_format($sql_res[0][csf('submit_date')]); ?></th>
				</tr>
			</table>
			<br/>	
			<table class="rpt_table" border="1" rules="all" width="950" cellpadding="0" cellspacing="0">
				<thead>
					<tr style="background-color: #BBBBBB;">
						<th width="50">SL No</th>
						<th width="150">Invoice No</th>
						<th width="150">Exp No</th>
						<th width="200">Value</th>
						<th width="200">LC/Contract No</th>
						<th width="200">LC/Contract Date</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i=1;$total_value=0;
					foreach ($sql_res as $row) 
					{
						if($i%2==0) $bgcolor="#DFDFDF"; else $bgcolor="#FFFFFF";
						if ($row[csf('is_lc')] == 1)
						{
							$lc_sc_no = $export_lc_no_arr[$row[csf('lc_sc_id')]];
							$ls_sc_date = $export_lc_date_arr[$row[csf('lc_sc_id')]];
						}
						else
						{
							$lc_sc_no = $sales_contract_no_arr[$row[csf('lc_sc_id')]];
							$ls_sc_date = $sales_contract_date_arr[$row[csf('lc_sc_id')]];
						}						
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>">
							<td width="50" align="center"><? echo $i; ?></td>
							<td width="150" align="center"><p><? echo $row[csf('invoice_no')]; ?></p></td>
							<td width="150" align="center"><p><? echo $row[csf('exp_form_no')]; ?></p></td>
							<td width="200" align="right"><p><? echo $lc_sc_currency.number_format($row[csf('net_invo_value')],2); ?></p></td>
							<td width="200" align="center"><p><? echo $lc_sc_no; ?></p></td>
							<td width="200" align="center"><p><? echo change_date_format($ls_sc_date); ?></p></td>
						</tr>
						<?
						$i++;
						$total_value += $row[csf('net_invo_value')];
					}
					?>					
				</tbody>
				<tfoot>
					<tr style="background-color: #BBBBBB">
						<th width="50"></th>
						<th width="150"></th>
						<th width="150" align="right">Total :</th>
						<th width="200" align="right"><p><? echo $lc_sc_currency.number_format($total_value,2); ?></p></th>
						<th width="200"></th>
						<th width="200"></th>
					</tr>
				</tfoot>
			</table>
			<table>
				<tr>
	        	<td colspan="3" height="10"></td>
	        </tr>
			<?
			if ($remarks !="") {
			?>
			<tr>
	        	<td colspan="3" height="5"></td>
	        </tr>
	        <tr>
	            <td width="25" ></td>
	            <td width="650" align="left">
	            <? echo "<strong>Remarks: </strong> ".$remarks;?>
	            </td>
	            <td width="25" ></td>
	        </tr>
	        <tr>
	        	<td colspan="3" height="5"></td>
	       	</tr>
			<?
			} 	
			?>
	        
	        <tr>
	        	<td colspan="3" height="15"></td>
	        </tr>
			</table>
		</div>
		
		<?
	}

	if($type==7) // Bank Forwarding and Bill of Exchange
	{
		$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
		$buyer_address = return_library_array( "select id, address_1 from  lib_buyer",'id','address_1');
		$buyer_library = return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
		$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
		if($result[0][csf("submit_date")]!="" && $result[0][csf("submit_date")]!='0000-00-00') $submit_date=change_date_format($result[0][csf("submit_date")]); else $submit_date="&nbsp;";

	    			
		foreach($result as $row)
		{
			$issue_bank_dtls=$row[csf("issue_bank_dtls")];
			$expl_issue_bank_dtls=explode('__', $issue_bank_dtls);
			$name_issue_bank=$expl_issue_bank_dtls[0];
			$to_issue_bank=$expl_issue_bank_dtls[1];
			$attention_issue_bank=$expl_issue_bank_dtls[2];
			$address_issue_bank=$expl_issue_bank_dtls[3];

			if($row[csf("is_lc")]==1)
			{
				$lc_sc_no = $export_lc_no_arr[$row[csf("lc_sc_id")]]["export_lc_no"]; 				
				$lc_sc_date = change_date_format($export_lc_no_arr[$row[csf("lc_sc_id")]]["lc_date"]); 
				$consignee = explode(',', $export_lc_no_arr[$row[csf("lc_sc_id")]]["consignee"]);


				$issuing_bank = $export_lc_no_arr[$row[csf("lc_sc_id")]]["issuing_bank_name"];
				$pay_terms = $export_lc_no_arr[$row[csf("lc_sc_id")]]["pay_term"];
				
				if ($pay_terms==1) 
				{
					$lcSc_pay_term='At Sight';
				}
				if ($pay_terms==2)
				{
					$lcSc_tenor = $export_lc_no_arr[$row[csf("lc_sc_id")]]["tenor"].' days after sight/from BL date/Form Ship Date/From Invoice Date ';
				}
			}
			else
			{
				$lc_sc_no = $export_sc_no_arr[$row[csf("lc_sc_id")]]["contract_no"]; 
				$lc_sc_date = change_date_format($export_sc_no_arr[$row[csf("lc_sc_id")]]["contract_date"]);

				$pay_terms=$export_sc_no_arr[$row[csf("lc_sc_id")]]["pay_term"];
				
				if ($pay_terms==1) 
				{
					$lcSc_pay_term='At Sight';
				}
				if ($pay_terms==2)
				{
					$lcSc_tenor = $export_sc_no_arr[$row[csf("lc_sc_id")]]["tenor"].' days after sight/from BL date/Form Ship Date/From Invoice Date ';
				}
			}
		}
		
		?>
		<style type="text/css" media="all" >

			.watermark1{
				font-size: 200px;
				position: absolute;
				top: 110%;
				left: 45%;
				opacity: .3;
				z-index: 9999;
				visibility: visible;
				overflow: hidden;

			}
			.watermark2{
				font-size: 200px;
				position: absolute;
				top: 200%;
				left: 45%;
				opacity: .3;
				z-index: 9999;
				content: "2";
			}
			.opacity_1
			{
				opacity:0.3; 
				position: absolute; 
				font-size: 400px; 
				left: 40%; 
				top: 20%;
			}	
			.opacity_2
			{
				opacity:0.3; 
				position: absolute; 
				font-size: 400px; 
				left: 40%; 
				top: 230%;
			}
			.opacity_3
			{
				opacity:0.3; 
				position: absolute; 
				font-size: 400px; 
				left: 40%; 
				top: 20%;
			}
			
			
			
			
			@media print {
				.page-break	{ display: block; page-break-after: always;}
				.watermark1{
					font-size: 200px;
					top: 118%;
					left: 42%;
					opacity: .3;
					z-index: 9999;
					visibility: visible;
					overflow: hidden;
					display:block;

				}
				.watermark2{
					display:block;
					position: absolute;
					font-size: 200px;
					top: 168%;
					left: 42%;
					opacity: .3;
					z-index: 1;
				}
			}
			
			#table_1,#table_2,#table_3{  background-position: center;background-repeat: no-repeat; }
			#table_1{background-image:url(../../../img/bg-1.jpg);}
			#table_2{background-image:url(../../../img/bg-2.jpg); }
			#table_3{background-image:url(../../../img/bg-3.jpg);}
			.fontsize{font-size: 5px !important;}
			
		</style>
		<?
		$sql_company = sql_select("SELECT * FROM lib_company WHERE id=$company_id and is_deleted=0 and status_active=1");
	  	foreach($sql_company as $company_data) 
	  	{
			if($company_data[csf('plot_no')]!='') $plot_no = 'Plot No.#'.$company_data[csf('plot_no')].','.' ';else $plot_no='';
			if($company_data[csf('level_no')]!='') $level_no = 'Level No.#'.$company_data[csf('level_no')].','.' ';else $level_no='';
			if($company_data[csf('road_no')]!='') $road_no = 'Road No.#'.$company_data[csf('road_no')].','.' ';else $road_no='';
			if($company_data[csf('block_no')]!='') $block_no = 'Block No.#'.$company_data[csf('block_no')].','.' ';else $block_no='';
			if($company_data[csf('city')]!='') $city = $company_data[csf('city')].','.' ';else $city='';
			if($company_data[csf('zip_code')]!='') $zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
			if($company_data[csf('country_id')]!=0) $country = $country_arr[$company_data[csf('country_id')]].'.';else $country='';
			
			$company_address = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
		}

		$sql_sub_bank="select a.id as sub_id, a.buyer_id, a.company_id, a.submit_date, a.lc_currency, a.lien_bank, a.remarks, a.lc_currency, a.pay_term
		from com_export_doc_submission_mst a where a.entry_form=40 and a.id=$mst_id and a.status_active=1 and a.is_deleted=0";  
		$sql_sub_bank_res=sql_select($sql_sub_bank);
		$remarks='';
		if ($sql_sub_bank_res[0][csf("remarks")] != ''){
			$remarks = $sql_sub_bank_res[0][csf("remarks")];
		}

		$back_sql=sql_select("select a.id, a.swift_code, b.account_type, b.account_no from lib_bank a, lib_bank_account b where a.id=b.account_id");

		$bank_data=array();
		foreach($back_sql as $row)
		{		
			if($row[csf("account_type")]==10) 
			$bank_data[$row[csf("id")]]["account_no"]=$row[csf("account_no")];		
		}

		?>		
	  	<div class="page-break"> 
	  		<table width="700" cellpadding="0" align="center" cellspacing="0" border="0">
                <tr>
                    <td colspan="5" align="center" style="font-size:30px; font-weight:bold;"><? echo $company_library[$company_id]; ?></td>
		        </tr>
		        <tr>
		            <td colspan="5" align="center" style="font-size:15px; font-weight:bold"><? echo $company_address; ?></td>
		        </tr>
		        <tr><td colspan="5" align="center">&nbsp;</td></tr>
		        <tr>
		            <td colspan="5" align="center"><hr style="width:100%;font-weight: bold;"></td>
		        </tr>
		        <tr>
		            <td colspan="5"><strong>Date: <? echo change_date_format($sql_sub_bank_res[0][csf("submit_date")]); ?></strong></td>
		        </tr>
		        <tr><td colspan="5">&nbsp;</td></tr>
		        <tr><td colspan="5">The Chief Manager</td></tr>
		        <tr><td colspan="5"><? echo $bank_data_arr[$sql_sub_bank_res[0][csf("lien_bank")]]["bank_name"]; ?></td></tr>
		        <tr><td colspan="5"><? echo $bank_data_arr[$sql_sub_bank_res[0][csf("lien_bank")]]["address"]; ?></td></tr>
		        <tr><td colspan="5">&nbsp;</td></tr>
		        <tr><td colspan="5" style="border: 2px solid black;">Sub: Request For Negotiation Of Bill Against Shipping Documents For USD <strong><? echo number_format($total_inv_value,2); ?></strong> Our <strong>Invoice No.: <? echo rtrim($all_invoice_no,', '); ?></strong> Under <strong>L/C No. <? echo $lc_sc_no; ?>  Dt. <? echo change_date_format($lc_sc_date); ?>, A/C: <? echo $name_issue_bank; ?>, <? echo $address_issue_bank; ?><strong></td></tr>
				<tr><td colspan="5">&nbsp;</td></tr>
				<tr><td colspan="5">DEAR SIR,</td></tr>
				<tr><td colspan="5">With Reference To The Subject Stated Above, Enclosed Please Find Herewith The Following Documents For Your Kind Perusal For Negotiation And Onward Submission To The Buyer’s Bank For Necessary Action.</td></tr>
				<tr><td colspan="5">&nbsp;</td></tr>
				<tr><td colspan="5" align="left">
				<? 
			   	$sql = "select mst_id, terms_id, terms_value, terms_bank from doc_submission_terms where terms_value is not null and terms_value not in ('0','0+0') and mst_id=".$mst_id."";       
				$data_array=sql_select($sql);

				   //print_r($data_array[0][csf("terms_id")]);
			   if ($data_array[0][csf("terms_id")] != "") {
				?>

		            	<table cellpadding="0" align="left" cellspacing="0" border="1" width="400" style="font-size:16px;">
		                	<thead>
		                    	<tr>
		                        	<th width="30">Sl No.</th>
		                            <th width="100">Types of Documents</th>
		                            <th width="60">Original</th>
		                            <th width="60">Bank Copy</th>
		                        </tr>
		                    </thead>
		                    <tbody>
		                    <?
							 
							 $i=1;     
							foreach ($data_array as $row)
							{
								?>
		                    	<tr>
		                            <td width="30" align="center"><? echo $i; ?></td>
		                            <td width="100" align="left"><? echo $document_set[$row[csf("terms_id")]];?></td>
		                            <td width="60" align="center"><? echo $row[csf("terms_value")]; ?></td>
		                            <td width="60" align="center"><? echo $row[csf("terms_bank")]; ?></td>
		                        </tr>
		                        <?
								$i++;
							}
							?>
		                    </tbody>
		                </table>

				<?
			   } 
			   ?>
				</td></tr>
				<tr><td colspan="5">&nbsp;</td></tr>
				<tr><td colspan="5">In View Of The Above, We Sincerely Hope That You Would Kindly Take All Necessary Steps To Negotiate The Above Documents And Credit The Proceeds To Our <strong>CD A/C # <? echo $bank_data[$lien_bank]["account_no"]; ?></strong> Being Maintained With You.</td></tr>
				<tr><td colspan="5">&nbsp;</td></tr>
				<tr><td colspan="5"><strong>Note: <? echo $remarks; ?></strong></td></tr>
				<tr><td colspan="5">&nbsp;</td></tr>
				<tr><td colspan="5">Your Best Co-Operation And Expeditious Action In The Matter Would Be Highly Appreciated.</td></tr>
				<tr><td colspan="5">&nbsp;</td></tr>
				<tr><td colspan="5">&nbsp;</td></tr>
				<tr><td colspan="5">Thanking You,</td></tr>
		    </table>
		</div>

		<div>      
	    	<table width="700" cellpadding="0" align="center" cellspacing="0" border="0" style="border: 2px solid black; height: 450px;">
	    		
                <tr>
                    <td colspan="5" align="center" style="font-size:35px; font-weight:bold;"><? echo "Bill of Exchange"; ?></td>
		        </tr>
					<tr>
						<td colspan="3" align="left" style="font-size:12px; font-weight:bold">No. <? echo rtrim($all_invoice_no,', '); ?></td>
						<td colspan="2" align="right" style="font-size:12px; font-weight:bold">Date: <? echo change_date_format($sql_sub_bank_res[0][csf("submit_date")]); ?></td>
					</tr>
					<tr><td colspan="5" align="center">&nbsp;</td></tr>
					<tr><td colspan="5" align="left"><p style=" font-size:12px; text-decoration: underline; font-weight: bold;">FOR USD <? echo number_format($total_inv_value,2); ?></p></td></tr>
					<tr><td colspan="5" align="center">&nbsp;</td></tr>
					<tr>
						<td colspan="5" align="left" style=" font-size:12px;"><? echo $days_to_realize; ?> Days <? if ($sql_sub_bank_res[0][csf("pay_term")] == 2) echo 'Deffered'; else if ($sql_sub_bank_res[0][csf("pay_term")] == 1) echo 'At Sight'; ?> of this FIRST Bill of Exchange ( SECOND of the same tenor and date being unpaid) pay to the order of <? echo $bank_data_arr[$lien_bank]["bank_name"].','.$bank_data_arr[$lien_bank]["address"].'.'; ?><i>The Sum Of Us Dollar <? echo number_to_words($total_inv_value, "USD", "CENTS"); ?></i> Value received & charged the same to the account of <? echo $buyer_library[$consignee[0]].', '.$buyer_address[$consignee[0]].'. '; ?>Drawn Under Documentary Credit No. <? echo $lc_sc_no; ?>&nbsp;Dt.<? echo change_date_format($lc_sc_date); ?> of Bank of <? echo $name_issue_bank.', '.$address_issue_bank.', '; ?>against our commercial invoice no: <? echo rtrim($all_invoice_no,', '); ?></td>
					</tr>
					<tr><td colspan="5" align="left" style=" font-size:12px;"><? echo $remarks; ?></td></tr>
					<tr><td colspan="5" align="right" style="font-size:12px;font-weight:bold;">FOR <? echo $company_library[$company_id]; ?></td></tr>
					<tr><td colspan="5" align="center">&nbsp;</td></tr>
					<tr><td colspan="5" align="left" style=" font-size:12px;"><strong>To: 
						<? 
						$ex_to_issue=explode(",",$to_issue_bank);
						foreach($ex_to_issue as $key=>$value){
							echo $value."</br>";
						}
						?></strong></td></tr>
					<tr><td colspan="5" align="left" style=" font-size:12px;"><strong>ATTN: 
						<? 
							$ex_attention_issue=explode(",",$attention_issue_bank);
							foreach($ex_attention_issue as $key=>$value){
								echo $value."</br>";
							}
						?></strong></td></tr>		    
	    	</table>
	    	<div class="watermark1">1</div>
      	</div>

        </br>
      	<div>      
	    	<table width="700" cellpadding="0" align="center" cellspacing="0" border="0" style="border: 2px solid black; height: 450px;">

                <tr>
                    <td colspan="5" align="center" style="font-size:35px; font-weight:bold;"><? echo "Bill of Exchange"; ?></td>
		        </tr>
					<tr>
						<td colspan="3" align="left" style="font-size:12px; font-weight:bold">No. <? echo rtrim($all_invoice_no,', '); ?></td>
						<td colspan="2" align="right" style="font-size:12px; font-weight:bold">Date: <? echo change_date_format($sql_sub_bank_res[0][csf("submit_date")]); ?></td>
					</tr>
					<tr><td colspan="5" align="center">&nbsp;</td></tr>
					<tr><td colspan="5" align="left" style=" font-size:12px;"><p style="text-decoration: underline; font-weight: bold;">FOR USD <? echo number_format($total_inv_value,2); ?></p></td></tr>
					<tr><td colspan="5" align="center">&nbsp;</td></tr>
					<tr>
						<td colspan="5" align="left" style=" font-size:12px;"><? echo $days_to_realize; ?> Days <? if ($sql_sub_bank_res[0][csf("pay_term")] == 2) echo 'Deffered'; else if ($sql_sub_bank_res[0][csf("pay_term")] == 1) echo 'At Sight'; ?> of this SECOND Bill of Exchange ( FIRST of the same tenor and date being unpaid) pay to the order of <? echo $bank_data_arr[$lien_bank]["bank_name"].','.$bank_data_arr[$lien_bank]["address"].'.'; ?><i>The Sum Of Us Dollar <? echo number_to_words($total_inv_value, "USD", "CENTS"); ?></i> Value received & charged the same to the account of <? echo $buyer_library[$consignee[0]].', '.$buyer_address[$consignee[0]].'. '; ?>Drawn Under Documentary Credit No. <? echo $lc_sc_no; ?>&nbsp;Dt.<? echo change_date_format($lc_sc_date); ?> of Bank of <? echo $name_issue_bank.', '.$address_issue_bank.', '; ?>against our commercial invoice no: <? echo rtrim($all_invoice_no,', '); ?></td>
					</tr>
					<tr><td colspan="5" align="left"><? echo $remarks; ?></td></tr>
					<tr><td colspan="5" align="right" style="font-size:12px; font-weight:bold;">FOR <? echo $company_library[$company_id]; ?></td></tr>
					<tr><td colspan="5" align="center">&nbsp;</td></tr>
					<tr><td colspan="5" align="left" style=" font-size:12px;"><strong>To: <? 
						$ex_to_issue=explode(",",$to_issue_bank);
						foreach($ex_to_issue as $key=>$value){
							echo $value."</br>";
						}
						?></strong></td></tr>
					<tr><td colspan="5" align="left" style=" font-size:12px;"><strong>ATTN: <? 
							$ex_attention_issue=explode(",",$attention_issue_bank);
							foreach($ex_attention_issue as $key=>$value){
								echo $value."</br>";
							}
						?></strong></td></tr>	    
	    	</table>
	    	<div class="watermark2">2</div>
      	</div>
	    <?	
	}

	if($type==8) // Print 5
	{
		//print_r($data);
		$sql = "SELECT a.ID as doc_id,d.CURRENT_INVOICE_RATE as CURRENT_INVOICE_RATE,  d.po_breakdown_id as PO_BREAKDOWN_ID, sum(d.current_invoice_qnty) as CURRENT_INVOICE_QNTY, c.invoice_no,e.po_number,
		a.bank_ref_no ,a.SUBMIT_DATE,a.BNK_TO_BNK_COUR_NO ,e.job_id as job_id,b.lc_sc_id,b.is_lc
		from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c , com_export_invoice_ship_dtls d, wo_po_break_down e
		where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.id=$mst_id and c.id =d.mst_id and d.po_breakdown_id=e.id and a.status_active=1 and a.is_deleted=0 
		and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.ID,
		d.CURRENT_INVOICE_RATE,d.po_breakdown_id,a.bank_ref_no,a.SUBMIT_DATE,a.BNK_TO_BNK_COUR_NO ,c.invoice_no,c.id,e.po_number,e.job_id,b.lc_sc_id,b.is_lc order by c.id asc
		";
		//echo $sql;
		$sql_res = sql_select($sql);
		$row_count = array();
		$span = 0;
		foreach($sql_res as $row_inv)
		{
			$row_count[$row_inv['doc_id']]++;
			//$all_ord .=  $row_inv[csf('po_breakdown_id')].',';
			$expisLC = implode(",",array_unique(explode(",",$row_inv[csf("is_lc")])));
			$expisINV = array_unique(explode(",",$row_inv[csf("lc_sc_id")]));
			$all_lc_sc_id=implode(",",$expisINV);

		}

		// echo "select export_lc_no, import_btb, export_item_category, pay_term from com_export_lc where id in (".$all_lc_sc_id.")";
		// echo "select contract_no, pay_term, lc_for  from com_sales_contract where id in (".$all_lc_sc_id.")";
	

		if($expisLC==1)
		{
			$poSQL=sql_select("select export_lc_no, import_btb, export_item_category, pay_term from com_export_lc where id in (".$all_lc_sc_id.")");
			foreach($poSQL as $poR)
			{
				if($lc_sc_no=="") $lc_sc_no=$poR[csf("export_lc_no")]; else $lc_sc_no.=",".$poR[csf("export_lc_no")];
				
			} 
		}
		else
		{
			$scSQL=sql_select("select contract_no, pay_term, lc_for  from com_sales_contract where id in (".$all_lc_sc_id.")");
			foreach($scSQL as $poR)
			{
				if($lc_sc_no=="") $lc_sc_no=$poR[csf("contract_no")]; else $lc_sc_no.=",".$poR[csf("contract_no")];
			} 
		}
	   
		// $all_po=chop($all_ord," , ");
		// $order_inv_array=array();
		// $sql_order_inv=sql_select("SELECT b.id as po_id, b.po_number as po_no,b.job_no_mst,a.order_uom from wo_po_break_down b,wo_po_details_master a where b.id in ($all_po) and b.job_no_mst=a.job_no and a.status_active=1 and b.status_active=1");
		// foreach($sql_order_inv as $row)
		// {
		// 	$order_inv_array[$row[csf("po_id")]]["po_no"] .= $row[csf("po_no")];
		// }


		$inv_sql = "SELECT sum(b.NET_INVO_VALUE) as inv_val  from com_export_doc_submission_mst a, com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and a.id=$mst_id and a.status_active=1 and a.is_deleted=0 
		and b.status_active=1 and b.is_deleted=0";
		$sql_tot_inv = sql_select($inv_sql);
		foreach($sql_tot_inv as $inv_val)
		{
			$inv_value_total += $inv_val[csf('inv_val')];
		}
		$style_ref_arr=return_library_array("select id, style_ref_no from wo_po_details_master", "id", "style_ref_no");
		ob_start();
		?>
		<div style="width: 1300px">
			<p>
			<h1 align="center"></h1>
			</p>
			
			<br />
			<table class="rpt_table" border="1" rules="all" width="1300" cellpadding="0" cellspacing="0">
				<thead>
					<tr style="background-color: #BBBBBB;">
						<th width="150">Invoice No.</th>
						<th width="150">Style No.</th>
						<th width="100">Order No.</th>
						<th width="100">Rate</th>
						<th width="100">Ship Qty</th>
						<th width="150">Total value of Shipment</th>
						<th width="100">Bank Ref No. </th>
						<th width="100">LC No</th>
						<th width="100">Bill of Enchange value</th>
						<th width="150">Bank to bank tracking No</th>
						<th width="100">B2B docs sending DATE</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i = 1;
					$total_value = 0;
					$span = 0;
					foreach ($sql_res as $row) 
					{
						
						$rowspan = $row_count[$row[csf('id')]];
						if ($i % 2 == 0) $bgcolor = "#DFDFDF";
						else $bgcolor = "#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>">
							<td width="150" align="center"> <p><? echo  $row[csf('invoice_no')]?></p></td> 
							<td width="150" align="right"><p><?
							echo $style_ref_arr[$row[csf('job_id')]];?></p></td>
							<td width="100" align="right"><p> <? echo $row[csf('po_number')]?></p></td> 
							<td width="100" align="right">
								<p><? echo number_format($row[csf('current_invoice_rate')],2); ?></p>
							</td>
							<td width="100" align="right">
								<p><? echo $row[csf('current_invoice_qnty')]; ?></p>
							</td>
							<td width="150" align="right">
								<p><?  $total_val = $row[csf('current_invoice_rate')]*$row[csf('current_invoice_qnty')];
								echo "$"." ". number_format($total_val,2);
								?></p>
							</td> 
							<?
							if($span == 0)
							{?>
							<td rowspan="<?= $rowspan; ?>"  valign="middle" align="center" width="100" >
								<p><? echo $row[csf('bank_ref_no')]; ?></p>
							</td>
							<td rowspan="<?= $rowspan; ?>"  valign="middle" align="center" width="100" >
								<p><? echo $lc_sc_no; ?></p>
							</td>
							<td rowspan="<?= $rowspan; ?>"  valign="middle" align="center" width="100">
								<p><? echo "$"." ". number_format($inv_value_total,2); ?></p>
							</td>
							<td rowspan="<?= $rowspan; ?>"  valign="middle" align="center" width="150">
								
								<p><? echo $row[csf('BNK_TO_BNK_COUR_NO')]; ?></p>
							</td>
							<td rowspan="<?= $rowspan; ?>"  valign="middle" align="center" width="100">
								<p><? echo change_date_format($row[csf('SUBMIT_DATE')]); ?></p>
							</td>
							<?
							}?>
						</tr>
						<?
						$span++;
						$total_value += $total_val;
					}
					?>
				</tbody>
				<tfoot>
					<tr style="background-color: #BBBBBB">
						<th width="150"></th>
						<th width="150"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100" align="right">Total :</th>
						<th width="150" align="right"> <p><? echo "$" ." ". number_format($total_value, 2); ?></p></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="150"></th>
						<th width="100"></th>
					</tr>
				</tfoot>
			</table>
		</div>

		<?
	}

	$user_id=$_SESSION['logic_erp']['user_id'];
		
		$html = ob_get_contents();
		ob_clean();
		//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
		foreach (glob("tb*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename="tb".$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, $html);

		echo "$html####$filename####$type";
	exit();
}

if($action=="save_update_delete_terms_acc")
{
    $process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//echo $operation; die;
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		 $id=return_next_id( "id", "doc_submission_terms", 1 ) ;
		 $field_array="id,mst_id,terms_id,terms_value,terms_bank";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $termsId="termsId_".$i;
			 $value="value_".$i;
			 $bank="bank_".$i;
			 
			if ($i!=1) $data_array .=",";
			$data_array.="(".$id.",".$mst_tbl_id.",".$$termsId.",".$$value.",".$$bank.")";
			$id=$id+1;
		 }
		 //echo  $data_array;
		$rID_de=execute_query( "delete from doc_submission_terms where  mst_id =".$mst_tbl_id."",0);

		 $rID=sql_insert("doc_submission_terms",$field_array,$data_array,1);
		 //check_table_status( $_SESSION['menu_id'],0);
		
		//echo "10**".$data_array;die;
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_booking_no[0];
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$new_booking_no[0];
			}
			else{
				oci_rollback($con);
				echo "10**".$new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	}
	if ($operation==1)  // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		 $id=return_next_id( "id", "doc_submission_terms", 1 ) ;
		 $field_array="id,mst_id,terms_id,terms_value,terms_bank";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $termsId="termsId_".$i;
			 $value="value_".$i;
			 $bank="bank_".$i;
			 
			if ($i!=1) $data_array .=",";
			$data_array.="(".$id.",".$mst_tbl_id.",".$$termsId.",".$$value.",".$$bank.")";
			$id=$id+1;
		 }
		 //echo  $data_array;
		 //echo "delete from doc_submission_terms where  mst_id =".$mst_tbl_id.""; die;
		$rID_de=execute_query("delete from doc_submission_terms where  mst_id =$mst_tbl_id",0);
		//echo $rID_de."joy"; die;
		 $rID=sql_insert("doc_submission_terms",$field_array,$data_array,1);
		 //check_table_status( $_SESSION['menu_id'],0);
		
		//echo "10**".$data_array;die;
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
	}	
}

if($action=="acc_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);	
	
	?>
	<script>
	
	function load_trims_uom(item_group,str)
	{
		var uom=trim_uom_arr[item_group].split('*');
		var html="<option value='"+uom[0]+"'>"+uom[1]+"</option>";
		document.getElementById('uom_'+str).innerHTML=html;
	}
			
	function add_break_down_tr(i) 
	{
		var row_num=$('#tbl_terms_details tr').length-1;
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
		 
			 $("#tbl_terms_details tr:last").clone().find("input,select").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name + i },
				  'value': function(_, value) { return '' }              
				});  
			  }).end().appendTo("#tbl_terms_details");
			
			 $('#itemgroup_'+i).removeAttr("onChange").attr("onChange","load_trims_uom(this.value,"+i+");");
			
			 $('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
			 $('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
			 
			 $('#increase_'+i).removeAttr("value").attr("value","+");
			 $('#decrease_'+i).removeAttr("value").attr("value","-");
			 
			 
			 $('#termscondition_'+i).val("");
			 $('#tbl_terms_details tbody tr:last td:first-child').text(i);
		}	  
	}

	/*	function fn_deletebreak_down_tr(rowNo) 
	{   
		
			var numRow = $('table#tbl_terms_details tbody tr').length; 
			if(numRow==rowNo && rowNo!=1)
			{
				$('#tbl_terms_details tbody tr:last').remove();
			}
	}*/

	function fn_deletebreak_down_tr(rowNo) 
	{  
		if(rowNo!=1)
		{
			var index=rowNo-1
			$("#tbl_terms_details tbody tr:eq("+index+")").remove();
			var numRow=$('#tbl_terms_details tbody tr').length;
			for(i = rowNo;i <= numRow;i++){
				$("#tbl_terms_details tr:eq("+i+")").find("input,select").each(function() {
					$(this).attr({
					  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					  //'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
					  'value': function(_, value) { return value }              
					}); 
					
				$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
				$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
				$("#tbl_terms_details tr:eq("+i+") td:eq(0)").text(i);
				})

			}
		}
		
	}

	function fnc_trims_acc( operation )
	{
		//alert(operation);
	    var row_num=$('#tbl_terms_details tr').length-1;
	    var mst_tbl_id = $('#mst_tbl_id').val();
		var data_all="";
		for (var i=1; i<=row_num; i++)
		{			
			data_all=data_all+get_submitted_data_string('termsId_'+i+'*value_'+i+'*bank_'+i,"../../../");
		}
		var data="action=save_update_delete_terms_acc&operation="+operation+'&total_row='+row_num+data_all+'&mst_tbl_id='+mst_tbl_id;
		//freeze_window(operation);
		http.open("POST","doc_submission_to_bank_partial_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_trims_acc_reponse;
	}

	function fnc_trims_acc_reponse()
	{
		
		if(http.readyState == 4) 
		{
		    var reponse=trim(http.responseText).split('**');
				if (reponse[0].length>2) reponse[0]=10;
				if(reponse[0]==0 || reponse[0]==1)
				{
					parent.emailwindow.hide();
				}
		}
	}

	</script>

	</head>

	<body>
		<div align="center" style="width:100%;" >
			<? echo load_freeze_divs ("../../../",$permission);  ?>
			<fieldset>
				<form id="termscondi_1" autocomplete="off">
				<input type="hidden" id="mst_tbl_id"   name="mst_tbl_id"  value="<? echo str_replace("'","",$mst_tbl_id);?>"  /> 
				<table width="520" cellspacing="0" class="rpt_table" border="0" id="tbl_terms_details" rules="all">
						<thead>
							<tr>
								<th width="30">Sl</th>
								<th width="200">Terms</th>
								<th>Value</th>
                                <th width="130">Bank</th>
								<th width="70"></th>
							</tr>
						</thead>
						<tbody>
						<?
						$sql = "select mst_id, terms_id, terms_value, terms_bank from doc_submission_terms where mst_id=".$mst_tbl_id."";            
						$data_array=sql_select($sql);
						$save_update=1;
						if ( count($data_array)>0) // Data update
						{
							$i=0;
							foreach( $data_array as $row )
							{
								$i++;
								?>
									<tr id="settr_<? echo $i;?>" align="center">
										
										<td><? echo $i;?></td> 
										<td>  
										<?  
											echo create_drop_down( "termsId_".$i, 150, $document_set,"", 1, "-- Select --", $row[csf('terms_id')], "",0 );
										?>
										</td>                           
										<!-- <td>
										<input type="text" id="terms_<? echo $i;?>"   name="terms_<? echo $i;?>" style="width:80%"  class="text_boxes"  value="<? echo $document_set[$row[csf('terms_id')]]; ?>"  /> 
										<input type="hidden" id="terms_id_<? echo $i;?>" name="terms_id_<? echo $i;?>" style="width:80%"  class="text_boxes"  value="<? echo $key ?>"  />
										</td> -->
									
										<td>
										<input type="text" id="value_<? echo $i;?>" name="value_<? echo $i;?>" style="width:90%"  class="text_boxes"  value="<? echo $row[csf('terms_value')]; ?>"  /> 
										</td>
                                        <td>
										<input type="text" id="bank_<? echo $i;?>" name="bank_<? echo $i;?>" style="width:100px"  class="text_boxes"  value="<? echo $row[csf('terms_bank')]; ?>"  /> 
										</td>
										<td> 
										<input type="button" id="increase_<? echo $i; ?>" style="width:20px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
										<input type="button" id="decrease_<? echo $i; ?>" style="width:20px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />

										</td>
									</tr>
								<?
							}
						}
						else
						{
							$save_update=0; // Data save
							$i=1;
								?>
									<? foreach ($document_set as $key=> $data) { ?>
									<tr id="settr_<? echo $i;?>" align="center">
										
										<td><? echo $i;?></td>   
										<td>  
										<?  
											echo create_drop_down( "termsId_".$i, 150, $document_set,"", 1, "-- Select --", $i, "",0 );
										?>
										</td>                        
										<!-- <td>
										<input type="text" id="terms_<? echo $i;?>"   name="terms_<? echo $i;?>" style="width:80%"  class="text_boxes"  value="<? echo $data ?>"  /> 
										<input type="hidden" id="terms_id_<? echo $i;?>"   name="terms_id_<? echo $i;?>" style="width:80%"  class="text_boxes"  value="<? echo $key ?>"  />
										</td> -->
									
										<td>
										<input type="text" id="value_<? echo $i;?>"   name="value_<? echo $i;?>" style="width:90%"  class="text_boxes"  value=""  /> 
										</td>
                                        <td>
										<input type="text" id="bank_<? echo $i;?>" name="bank_<? echo $i;?>" style="width:100px"  class="text_boxes"  value=""  /> 
										</td>
										<td> 
										<input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
										<input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />

										</td>
									</tr>

									<? $i++;
										}
						}
							
							?>
					</tbody>
					</table>
					
					<table width="650" cellspacing="0" class="" border="0">
						<tr>
							<td align="center" height="15" width="100%"> </td>
						</tr>
						<tr>
							<td align="center" width="100%" class="button_container">
								<?
									echo load_submit_buttons( $permission, "fnc_trims_acc", $save_update,0 ,"reset_form('termscondi_1','','','','')",1) ; 
									?>
							</td> 
						</tr>
					</table>
				</form>
			</fieldset>
		</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}


if($action=="bank_info_popup")
{
  	echo load_html_head_contents("Issue Bank Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$txt_issue_bank_info_dtls_ref=explode("__",str_replace("'","",$txt_issue_bank_info_dtls));  
	?>
	     
	<script>
		function js_set_value()
		{
	 		var bank_name=$('#txt_bank_name').val();
			var txt_to=$('#txt_to').val();
			var txt_attention=$('#txt_attention').val();
			var txt_address=$('#txt_address').val();
			$('#hdn_issue_bank_info_dtls').val(bank_name+"__"+txt_to+"__"+txt_attention+"__"+txt_address);
			parent.emailwindow.hide();
		}
		var str_bank_name = [ <? echo substr(return_library_autocomplete("SELECT REGEXP_SUBSTR(ISSUE_BANK_DTLS, '[^__]+',1,1) dtls1 FROM COM_EXPORT_DOC_SUBMISSION_MST where ISSUE_BANK_DTLS IS NOT NULL", "dtls1" ), 0, -1); ?> ];
		var str_bank_to = [ <? echo substr(return_library_autocomplete("SELECT REGEXP_SUBSTR(ISSUE_BANK_DTLS, '[^__]+',1,2) dtls2 FROM COM_EXPORT_DOC_SUBMISSION_MST where ISSUE_BANK_DTLS IS NOT NULL", "dtls2" ), 0, -1); ?> ];
		var str_bank_att = [ <? echo substr(return_library_autocomplete("SELECT REGEXP_SUBSTR(ISSUE_BANK_DTLS, '[^__]+',1,3) dtls3 FROM COM_EXPORT_DOC_SUBMISSION_MST where ISSUE_BANK_DTLS IS NOT NULL", "dtls3" ), 0, -1); ?> ];
		var str_bank_add = [ <? echo substr(return_library_autocomplete("SELECT REGEXP_SUBSTR(ISSUE_BANK_DTLS, '[^__]+',1,4) dtls4 FROM COM_EXPORT_DOC_SUBMISSION_MST where ISSUE_BANK_DTLS IS NOT NULL", "dtls4" ), 0, -1); ?> ];

		$( document ).ready(function() {
			$("#txt_bank_name").autocomplete({
			source: str_bank_name
			});
			$("#txt_to").autocomplete({
					source: str_bank_to
			});
			$("#txt_attention").autocomplete({
					source: str_bank_att
			});
			$("#txt_address").autocomplete({
					source: str_bank_add
			});
		});
	</script>

	</head>

	<body>
	<div align="center" style="width:700px;">
	<form name="searchdocfrm_1"  id="searchdocfrm_1" autocomplete="off" >
    <legend>Issuing Bank Info</legend>
	<table width="680" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
            <tbody>
                <tr>
                	<td width="130" align="right">Name</td>
                    <td width="170">&nbsp;<input type="text" name="txt_bank_name" id="txt_bank_name" style="width:150px" class="text_boxes" value="<?= $txt_issue_bank_info_dtls_ref[0];?>" /></td> 
                    <td width="130" align="right">To</td>
                    <td>&nbsp;<input type="text" name="txt_to" id="txt_to" style="width:150px" class="text_boxes"  value="<?= $txt_issue_bank_info_dtls_ref[1]; ?>" /></td> 
            	</tr>
                <tr>
                	<td align="right">Attention</td>
                    <td>&nbsp;<input type="text" name="txt_attention" id="txt_attention" style="width:150px" class="text_boxes"  value="<?= $txt_issue_bank_info_dtls_ref[2];?>" /></td> 
                    <td align="right">Address</td>
                    <td>&nbsp;<input type="text" name="txt_address" id="txt_address" style="width:150px" class="text_boxes"  value="<?= $txt_issue_bank_info_dtls_ref[3];?>" /></td> 
            	</tr>
                <tr><td>&nbsp;</td></tr>
                <tr>
                	<td colspan="4" align="center">
                    <input type="button" id="btn_close" style="width:100px" class="formbutton" value="Close" onClick="js_set_value();" />
                    <input type="hidden" id="hdn_issue_bank_info_dtls" name="hdn_issue_bank_info_dtls" />
                    </td>
                </tr>
            </tbody>         
    </table>    
    </form>
    </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

?>


 