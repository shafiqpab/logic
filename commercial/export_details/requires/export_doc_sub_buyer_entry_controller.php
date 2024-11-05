<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
 
//--------------------------- Start-------------------------------------//

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  
	exit();	
}


if($action=="populate_acc_loan_no_data")
{
	$data=explode("**",$data);
	$acc_type=$data[0];
	$rowID=$data[1];
	
	$sql="select account_no from lib_bank_account where account_type=$acc_type";
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

/*if($action=="transaction_add_row")
{
	$rowNo = $data+1;
	echo '<tr id="tr'.$rowNo.'">
			<td>'.create_drop_down( "cbo_account_head_".$rowNo, 200, $commercial_head,"", 1, "-- Select --", $selected, "get_php_form_data(this.value+'**'+".$rowNo.", 'populate_acc_loan_no_data', 'requires/export_doc_sub_buyer_entry_controller' );",0,"" ).'</td>							
			<td><input type="text" id="txt_ac_loan_no_'.$rowNo.'" name="txt_ac_loan_no[]" class="text_boxes" style="width:100px" /></td>
			<td><input type="text" id="txt_domestic_curr_'.$rowNo.'" name="txt_domestic_curr[]" class="text_boxes_numeric" style="width:100px" onkeyup="fn_calculate(this.id,'.$rowNo.')" /></td>
			<td><input type="text" id="txt_conversion_rate_'.$rowNo.'" name="txt_conversion_rate[]" class="text_boxes_numeric" style="width:100px" onkeyup="fn_calculate(this.id,'.$rowNo.')" /></td>
			<td><input type="text" id="txt_lcsc_currency_'.$rowNo.'" name="txt_lcsc_currency[]" class="text_boxes_numeric" style="width:100px" onkeyup="fn_calculate(this.id,'.$rowNo.')" /></td>
			<td>
			<input type="button" id="increaserow_'.$rowNo.'" style="width:30px" class="formbutton" value="+" onClick="javascript:fn_inc_decr_row('.$rowNo.',\'increase\');" /><input type="button" id="decreaserow_'.$rowNo.'" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_inc_decr_row('.$rowNo.',\'decrease\');" />
			</td> 
		</tr>'; 
	 
	exit();
}*/

if($action=="lcSc_popup_search")
{
	echo load_html_head_contents("Export Information Entry Form", "../../../", 1, 1,$unicode,'1','');
	extract($_REQUEST);
	$mst_tbl_id=str_replace("'","",$mst_tbl_id);
	if($mst_tbl_id=="") $mst_tbl_id=0;
	if($mst_tbl_id)
	{
		if($db_type==0){
			$invoice_id_all=sql_select("select group_concat(invoice_id) as inv_id from com_export_doc_submission_invo where doc_submission_mst_id=".$mst_tbl_id,"inv_id");
		}
		else if($db_type==2){
			$invoice_id_all=sql_select("select rtrim(xmlagg(xmlelement(e,invoice_id,',').extract('//text()') order by invoice_id).GetClobVal(),',') as inv_id from com_export_doc_submission_invo where doc_submission_mst_id=".$mst_tbl_id,"inv_id");
		}
		if($db_type==2) $invoice_id_all = $invoice_id_all[0][csf("inv_id")]->load(); else  $invoice_id_all = $invoice_id_all[0][csf("inv_id")];
	}
	
	//listagg(cast(invoice_id as varchar(4000)), ',') within group(order by invoice_id) as inv_id
	//echo "select group_concat(invoice_id) as inv_id from com_export_doc_submission_invo  where  doc_submission_mst_id=$mst_tbl_id ";
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
				js_set_value( old[i] ) 
			}
		}
	}
	
	
	var selected_id = new Array;
	var selected_name = new Array;
	var currencyArr = new Array; //for check currency mix
	var buyerArr = new Array; //for check buyer mix
	var lcScArr = new Array; //for check lc sc mix
	var row_id;
	
	function check_all_data() 
	{
		var tbl_row_count = document.getElementById( 'rpt_table_body' ).rows.length;
		tbl_row_count = tbl_row_count - 1;
		//alert(tbl_row_count);
		for( var i = 1; i <= tbl_row_count; i++ ) 
		{
			var currency = $('#hidden_currency'+i).val();
			var buyer = $('#hidden_buyer'+i).val();
			
			if(currencyArr.length==0)
			{
				currencyArr.push(currency);
			}
			else if( jQuery.inArray( currency, currencyArr )==-1 &&  currencyArr.length>0)
			{
				alert("Currency Mixed is Not Allow");return;
			}
			//buyer mix check--------------------------------//
			if(buyerArr.length==0)
			{
				buyerArr.push( buyer );
			}
			else if( jQuery.inArray( buyer, buyerArr )== -1 &&  buyerArr.length>0)
			{
				alert("Buyer Mixed is Not Allow");return;
				
			}
			js_set_value(i);
		}
	}
	
	function toggle( x, origColor ) 
	{
		var newColor = 'yellow';
		if ( x.style ) 
		{
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}
	
	function js_set_value(str, pi_source_btb_lc, bl_no, bl_date) 
	{ 
		var invoiceID = $('#hidden_invoice_id'+str).val();
		var currency = $('#hidden_currency'+str).val();
		var buyer = $('#hidden_buyer'+str).val();
		var lcsc = $('#hidden_lc_sc'+str).val();
		
		//ls sc mix check-------------------------------//
		
		/*if(lcScArr.length==0)
		{
			lcScArr.push( lcsc );
		}
		else if( jQuery.inArray( lcsc, lcScArr )==-1 &&  lcScArr.length>0)
		{
			alert("LC or SC Mixed is Not Allow");
			return;
		}*/
		
		//currency mix check-------------------------------//
		 
		//currency mix check-------------------------------//
		
		if(currencyArr.length==0)
		{
			currencyArr.push(currency);
		}
		else if( jQuery.inArray( currency, currencyArr )==-1 &&  currencyArr.length>0)
		{
			alert("Currency Mixed is Not Allow");return;
		}
		// alert(bl_no+"__"+bl_date)
		if(pi_source_btb_lc==1){
			if(bl_no=="" && bl_date==""){
				 alert("BL Date and BL Number Not Found Against The Invoice");return;
			}
		}
		
		//buyer mix check--------------------------------//
		if(buyerArr.length==0)
		{			
			buyerArr.push( buyer );
			//alert(buyerArr);
		}
		else if( jQuery.inArray( buyer, buyerArr )== -1 &&  buyerArr.length>0)
		{
			alert("Buyer Mixed is Not Allowed");return;
			
		}//else{
			//var index = document.getElementById("search"+str).rowIndex;
			//alert(jQuery.inArray( buyer, buyerArr ));
		//}

		toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
		
		if( jQuery.inArray( invoiceID, selected_id ) == -1 ) {
			selected_id.push( invoiceID );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == invoiceID ) break;
			}			
			selected_id.splice( i, 1 );
			buyerArr.length=0;
		}
		var id =''; var name = '';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
		}
		id 		= id.substr( 0, id.length - 1 );
		$('#all_invoice_id').val( id );			
 	}

    function searchByDependent(val){
        if(val == 3){
            document.getElementById('searchbydependentinvoice').innerHTML  = "Enter BL No";
            document.getElementById('searchbydependentdaterange').innerHTML  = "BL Date Range";
        }else if(val == 2){
            document.getElementById('searchbydependentinvoice').innerHTML  = "Enter Sales Contact No";
            document.getElementById('searchbydependentdaterange').innerHTML  = "Invoice Date Range";
        }else{
            document.getElementById('searchbydependentinvoice').innerHTML  = "Enter Invoice No";
            document.getElementById('searchbydependentdaterange').innerHTML  = "Invoice Date Range";
        }
    }
	
    </script>

</head>

<body>
<div align="center" style="width:100%;">
	<form name="searchexportinformationfrm"  id="searchexportinformationfrm">
		<fieldset style="width:910px;">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="880" class="rpt_table">
            	<thead>
            		<tr>
            			<th>Company</th>
            			<th>Buyer</th>
                        <th>LC For</th>
            			<th>Search By</th>
            			<th id="searchbydependentinvoice">Enter Invoice No</th>
            			<th id="searchbydependentdaterange">Invoice Date Range</th>
            			<th>
            				<input type="reset" name="reset" id="reset" value="Reset" style="width:80px" class="formbutton" />
            				<input type="hidden" name="hidden_lcSc_id" id="hidden_lcSc_id" value="" />
            				<input type="hidden" name="is_lcSc" id="is_lcSc" value="" />
            			</th>
            		</tr>
            	</thead>
            	<tbody>
            		<tr class="general">
            			<td>
            				<?
            				echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--- Select Company ---", $companyID, "",1 );
            				?>                        
            			</td>
            			<td>
            				<?
            				echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b,lib_buyer_party_type c where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' $buyer_cond and c.buyer_id=buy.id and c.party_type=1 group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
                    		//echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' $buyer_cond group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); // old
            				?>
            			</td>
                        <td>
							<? echo create_drop_down( "cbo_lc_for", 100, $lc_for_arr,"", 0, "", 1, "" ); ?>
                        </td>
            			<td> 
            				<?
            				$arr=array(1=>'Invoice No', 2=>'Sales Contract No', 3=>'BL No');
            				echo create_drop_down( "cbo_search_by", 110, $arr,"", 0, "", 0, "searchByDependent(this.value)" );
            				?> 
            			</td>						
            			<td>
            				<input type="text" style="width:90px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
            			</td>
            			<td>
            				<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
            				<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
            			</td>                       
            			<td>
            				<input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $invoice_id_all; ?>'+'**'+'<? echo $mst_tbl_id; ?>'+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_lc_for').value, 'lcSc_search_list_view', 'search_div', 'export_doc_sub_buyer_entry_controller', 'setFilterGrid(\'rpt_table_body\',-1)'); set_all();" style="width:80px;" />
            			</td>
            		</tr>
            		<tr>
            			<td align="center" height="40" valign="middle" colspan="7">
            				<? echo load_month_buttons(1);  ?>
            			</td>
            		</tr>
            	</tbody>
           </table>
            <div style="width:100%; margin-top:10px" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</body>           

</html>
<?
	exit(); 
 
}

if($action=="lcSc_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$buyer_id=$data[1];
	$search_by=$data[2];
	$search_string=trim($data[3]);
	$invoiceArr = explode(",",$data[4]);
	$mst_tbl_id = $data[5];
	$txt_date_from = $data[6];
	$txt_date_to = $data[7];
	$cbo_lc_for = $data[8];
	//echo $mst_tbl_id ;die;
	if($mst_tbl_id=='') $mst_tbl_id=0;
	
	if($buyer_id!=0) $buyer_con="and m.buyer_id=$buyer_id"; else $buyer_con;
	if($company_id==0){ echo "Please Select Company first"; die; }


	$sql_cond="";

	if($db_type==0)
		{
			if($txt_date_from!= "" && $txt_date_to!= "")
			{
                if($search_by == 3){
                    $sql_cond .= " and m.bl_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
                }else{
                    $sql_cond .= " and m.invoice_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
                }
			}
		}
		if($db_type==2 || $db_type==1)
		{ 
			if($txt_date_from!= "" && $txt_date_to!= "")
			{
                if($search_by == 3){
                    $sql_cond .= " and m.bl_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
                }else{
                    $sql_cond .= " and m.invoice_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
                }
			}
		}


	$sub_inv_cond="";
	if($mst_tbl_id>0) $sub_inv_cond=" and p.doc_submission_mst_id != '$mst_tbl_id'";
	$sub_invoice_sql="select p.invoice_id from com_export_doc_submission_invo p, com_export_doc_submission_mst q where p.doc_submission_mst_id=q.id and p.status_active=1 and p.is_deleted=0 and q.entry_form=39 and q.company_id = $company_id  $sub_inv_cond";
	//echo $sub_invoice_sql;
	$sub_invoice_result=sql_select($sub_invoice_sql);
	$sub_invoice_data=array();
	foreach($sub_invoice_result as $row)
	{
		$sub_invoice_data[$row[csf("invoice_id")]]=$row[csf("invoice_id")];
	}
	unset($sub_invoice_result);

	$lc_for_cond="";
	if($cbo_lc_for) $lc_for_cond=" and m.lc_for=$cbo_lc_for";
	if($search_by==1)
	{
 		if($search_string != "")
			$sql = "SELECT m.is_lc, m.id, m.invoice_no, m.bl_no, m.bl_date, m.invoice_date, m.lc_sc_id, m.net_invo_value, m.buyer_id, b.short_name, m.lc_for 
			FROM com_export_invoice_ship_mst m, lib_buyer b  
			WHERE m.status_active=1 and m.is_deleted=0 $buyer_con  and benificiary_id=$company_id $sql_cond $lc_for_cond and invoice_no like '%$search_string%' and b.id=m.buyer_id and m.is_lc=2 
			ORDER BY m.invoice_date DESC";
		else 
			$sql = "SELECT m.is_lc, m.id, m.invoice_no, m.bl_no, m.bl_date, m.invoice_date, m.lc_sc_id, m.net_invo_value, m.buyer_id, b.short_name, m.lc_for 
			FROM com_export_invoice_ship_mst m, lib_buyer b  
			WHERE  m.status_active=1 and m.is_deleted=0 $buyer_con  and benificiary_id=$company_id $sql_cond $lc_for_cond and b.id=m.buyer_id and m.is_lc=2 ORDER BY m.invoice_date DESC";
	}
    else if($search_by==3)
    {
        if($search_string != "")
            $sql = "SELECT m.is_lc, m.id, m.invoice_no, m.bl_no, m.bl_date, m.invoice_date, m.lc_sc_id, m.net_invo_value, m.buyer_id, b.short_name, m.lc_for 
			FROM com_export_invoice_ship_mst m, lib_buyer b  
			WHERE m.status_active=1 and m.is_deleted=0 $buyer_con  and benificiary_id=$company_id $sql_cond $lc_for_cond and bl_no like '%$search_string%' and b.id=m.buyer_id and m.is_lc=2 
			ORDER BY m.bl_date DESC";
        else
            $sql = "SELECT m.is_lc, m.id, m.invoice_no, m.bl_no, m.bl_date, m.invoice_date, m.lc_sc_id, m.net_invo_value, m.buyer_id, b.short_name, m.lc_for 
			FROM com_export_invoice_ship_mst m, lib_buyer b  
			WHERE  m.status_active=1 and m.is_deleted=0 $buyer_con  and benificiary_id=$company_id $sql_cond $lc_for_cond and b.id=m.buyer_id and m.is_lc=2 ORDER BY m.bl_date DESC";
    }
	else
	{ 
		if($search_string != "")
			$sql = "SELECT m.is_lc, m.id, m.invoice_no, m.bl_no, m.bl_date, m.invoice_date, m.lc_sc_id, m.net_invo_value, m.buyer_id, b.short_name, m.lc_for 
			FROM com_export_invoice_ship_mst m, lib_buyer b  WHERE m.status_active=1 and m.is_deleted=0 $buyer_con  and benificiary_id=$company_id and m.is_lc=2 and b.id=m.buyer_id and lc_sc_id in (select id from com_sales_contract where contract_no like '%$search_string%' $sql_cond $lc_for_cond and status_active=1 and is_deleted=0) 
			ORDER BY m.invoice_date DESC";
		else
			$sql = "SELECT m.is_lc, m.id, m.invoice_no, m.bl_no, m.bl_date, m.invoice_date, m.lc_sc_id, m.net_invo_value, m.buyer_id, b.short_name, m.lc_for 
			FROM com_export_invoice_ship_mst m, lib_buyer b  
			WHERE   m.status_active=1 and m.is_deleted=0 $buyer_con  and benificiary_id=$company_id $sql_cond $lc_for_cond and b.id=m.buyer_id and m.is_lc=2 ORDER BY m.invoice_date DESC";
	}
	
	//echo $sql;		
		 
	?>
    <div style="width:900px;">
        <table width="880" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">                    
            <thead>
            	<tr>
            		<th width="50">SL</th>
            		<th width="100">Invoice No</th>
            		<th width="70">Invoice Date</th>
            		<th width="150">Sales Contract No</th>
                    <th width="100">BL No</th>
                    <th width="70">BL Date</th>
                    <th width="80">Buyer</th>
                    <th width="80">LC For</th>
            		<th width="70">Currency</th>
            		<th>Net Invoice Value</th>
            	</tr>
            </thead> 
       </table>
    </div>
	<div style="width:900px; overflow-y:scroll; max-height:220px">
        <table width="880" cellpadding="0" cellspacing="0" border="0" class="rpt_table"  rules="all" id="rpt_table_body">
        <tbody>
        <?
            $i=1; $oldDataRow=$pay_term_cond="";
          
            $lcsc_lc_arr=array();
            $lcscRes_data=sql_select("select id, contract_no, currency_name, pay_term, convertible_to_lc from com_sales_contract where status_active=1 and is_deleted=0"); 
            foreach($lcscRes_data as $row_lcsc)
            {
                $lcsc_lc_arr[$row_lcsc[csf('id')]]['contract_no']=$row_lcsc[csf('contract_no')];
                $lcsc_lc_arr[$row_lcsc[csf('id')]]['currency_name']=$row_lcsc[csf('currency_name')]; 
                $lcsc_lc_arr[$row_lcsc[csf('id')]]['pay_term']=$row_lcsc[csf('pay_term')];   
                $lcsc_lc_arr[$row_lcsc[csf('id')]]['convertible_to_lc']=$row_lcsc[csf('convertible_to_lc')];   
            } 
            //var_dump($lcsc_lc_arr[141]['currency_name']);

			$nameArray=sql_select( "select id, pi_source_btb_lc from variable_settings_commercial where company_name='$company_id' and variable_list=38 and STATUS_ACTIVE=1 order by id");

	        $pi_source_btb_lc= $nameArray[0]["PI_SOURCE_BTB_LC"];
            $nameArray=sql_select($sql);
            $oldDataRow="";
            foreach($nameArray as $row)
            {  
                if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";   
                
                $currency_name=$lcsc_lc_arr[$row[csf('lc_sc_id')]]['currency_name'];
                $lc_sc_no=$lcsc_lc_arr[$row[csf('lc_sc_id')]]['contract_no'];
                $pay_term_cond =$lcsc_lc_arr[$row[csf('lc_sc_id')]]['pay_term'] ;
                $convertible_to_lc =$lcsc_lc_arr[$row[csf('lc_sc_id')]]['convertible_to_lc'] ;
                 
                //var_dump($lcscRes);
                if($sub_invoice_data[$row[csf("id")]]=="" && $pay_term_cond!=3 &&  $convertible_to_lc!=1)
                {
                    //old data row arrange here------
                    if( in_array($row[csf("id")], $invoiceArr) )
                    {
        
                        if($oldDataRow=="") $oldDataRow = $i; else $oldDataRow .= ",".$i;
                    }
                    ?>   	
                            <tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $i;?>,<?=$pi_source_btb_lc?>,'<?=$row[csf("bl_no")]?>','<?=change_date_format($row[csf("bl_date")])?>')" > 
                                <td width="50" align="center"><? echo $i;?> 
                                    <input type="hidden" id="hidden_invoice_id<? echo $i; ?>" value="<? echo $row[csf("id")];?>" />
                                    <input type="hidden" id="hidden_invoice_no<? echo $i; ?>" value="<? echo $row[csf("invoice_no")];?>" />                                     
                                </td>		                   
                                <td width="100"><? echo $row[csf("invoice_no")];?></td>

								<td width="70" align="center"><? if($row[csf("invoice_date")]!= "" && $row[csf("invoice_date")]!="0000-00-00") echo change_date_format($row[csf("invoice_date")]);?></td>

                                <td width="150"><p><? echo $lc_sc_no;?></p>
                                <input type="hidden" id="hidden_lc_sc<? echo $i; ?>" value="<? echo $lc_sc_no; ?>" /> 
                                </td>
                                <td width="100"><? echo $row[csf("bl_no")];?></td>

                                <td width="70" align="center"><? if($row[csf("bl_date")]!= "" && $row[csf("bl_date")]!= "0000-00-00") echo change_date_format($row[csf("bl_date")]);?></td>


                                <td width="80"><p><? echo $row[csf("short_name")]; ?></p><input type="hidden" id="hidden_buyer<? echo $i; ?>" value="<? echo $row[csf("buyer_id")]; ?>" />
                                </td>
                                <td width="80"><p><? echo $lc_for_arr[$row[csf("lc_for")]]; ?></p><input type="hidden" id="hidden_lc<? echo $i; ?>" value="<? echo $row[csf("lc_for")]; ?>" />
                                </td>
                                <td width="70">
                                    <p><? echo  $currency[$currency_name]; ?></p>
                                    <input type="hidden" id="hidden_currency<? echo $i; ?>" value="<? echo $currency_name; ?>" />
                                </td>
                                <td align="right"><p><? echo $row[csf("net_invo_value")];?></p></td>
                            </tr>         
                    <?           
                    $i++; 
                }
                
            } //foeach end
         ?>
         </tbody>
        <input type="hidden" name="old_data_row_color" id="old_data_row_color" value="<?php echo $oldDataRow; ?>"/>
        </table>
    </div>	
    <div style="width:50%; float:left" align="left">
        <input type="hidden"  id="all_invoice_id" value="" />
        <input type="hidden"  id="all_invoice_no" value="" /> 
        <input type="checkbox" name="check_all_lc" id="check_all_lc" onClick="check_all_data()" value="0" />&nbsp;&nbsp;Check All
    </div>
    <div style="width:40%; float:left" align="left">
    	<input type="button" class="formbutton" id="close" style="width:80px" onClick="parent.emailwindow.hide();" value="Close" />
    </div> 
                
   <?        
   exit();    
}




//invoice list view create
if ($action=="show_invoice_list_view")
{
		//echo $sqlDtls;
		$lcscNoString="";$lcscNoID="";$lienBank="";$currencyID="";$invoice_tr="";$tot_invoice_val=0; 
		$i=1;

		if($db_type==0) $po_breakdown_id_cond=" , group_concat(dtl.po_breakdown_id) as po_breakdown_id";
		else if($db_type==2) $po_breakdown_id_cond=" , rtrim(xmlagg(xmlelement(e,dtl.po_breakdown_id,',').extract('//text()') order by dtl.po_breakdown_id).GetClobVal(),',') as po_breakdown_id";

		if($db_type==0)
		{
			$sqlDtls="SELECT invo.id,invo.invoice_no,invo.buyer_id,invo.invoice_date,invo.lc_sc_id,invo.is_lc,invo.bl_no,invo.bl_date,invo.net_invo_value, invo.forwarder_name, invo.feeder_vessel, sc.lien_bank,sc.currency_name,sc.contract_no as lcsc_no $po_breakdown_id_cond FROM  com_export_invoice_ship_mst invo, com_sales_contract sc, com_export_invoice_ship_dtls dtl  WHERE invo.id in($data) and invo.lc_sc_id = sc.id  and invo.id=dtl.mst_id and invo.is_lc=2 and dtl.current_invoice_qnty>0 group by dtl.mst_id ";
		}
		else if($db_type==2)
		{
			$sqlDtls="SELECT invo.id,invo.invoice_no,invo.buyer_id,invo.invoice_date,invo.lc_sc_id,invo.is_lc,invo.bl_no,invo.bl_date,invo.net_invo_value, invo.forwarder_name, invo.feeder_vessel, sc.lien_bank,sc.currency_name,sc.contract_no as lcsc_no $po_breakdown_id_cond , invo.lc_for
			FROM  com_export_invoice_ship_mst invo, com_sales_contract sc, com_export_invoice_ship_dtls dtl  
			WHERE invo.id in($data) and invo.lc_sc_id = sc.id  and invo.id=dtl.mst_id and invo.is_lc=2 and dtl.current_invoice_qnty>0 
			group by dtl.mst_id,invo.id,invo.invoice_no,invo.buyer_id,invo.invoice_date,invo.lc_sc_id,invo.is_lc,invo.bl_no,invo.bl_date,invo.net_invo_value, invo.forwarder_name, invo.feeder_vessel, sc.lien_bank,sc.currency_name,sc.contract_no, invo.lc_for ";	
		}
		//echo $sqlDtls;//die;
		$resArray=sql_select($sqlDtls);$temp_arr=array();
		foreach($resArray as $res)
		{
			if($res[csf("feeder_vessel")]!='')
			{
				$disble_fv='disabled=\"disabled\"';
			}
			else
			{
				$disble_fv='';
			}
			if($res[csf("forwarder_name")]!=0 && $res[csf("forwarder_name")]!='')
			{
				$disble_fn=1;
			}
			else
			{
				$disble_fn=0;
			}

			if(!in_array($res[csf("lc_sc_id")],$temp_arr))
			{
				$temp_arr[]=$res[csf("lc_sc_id")];
				if($lcscNoString=="") $lcscNoString .= $res[csf("lcsc_no")]; else $lcscNoString .=",".$res[csf("lcsc_no")];
				if($lcscNoID=="") $lcscNoID .= $res[csf("lc_sc_id")]; else $lcscNoID .=",".$res[csf("lc_sc_id")];
			}
			
			
			$lienBank=$res[csf("lien_bank")];
			$currencyID=$res[csf("currency_name")];
			$buyerID=$res[csf("buyer_id")];
			
			//list view data arrange-----------------// <td align=\"center\">".$i."</td>
			if($db_type==2 && $res[csf("po_breakdown_id")]!="") $res[csf("po_breakdown_id")] = $res[csf("po_breakdown_id")]->load();
			if($res[csf("lc_for")]==2)
			{
				$poSQL=sql_select("select requisition_number as po_number from sample_development_mst where id in (".$res[csf("po_breakdown_id")].")");
			}
			else
			{
				$poSQL=sql_select("select po_number from wo_po_break_down where id in (".$res[csf("po_breakdown_id")].")");
			}
			
			$po_numbers="";
			foreach($poSQL as $poR)
			{
				if($po_numbers=="") $po_numbers=$poR[csf("po_number")]; else $po_numbers.=",".$poR[csf("po_number")];
			}
			$invoice_tr .= "<tr>".
								"<td align=\"center\">".$i."</td>".
								"<td><input type=\"text\" id=\"txt_invoice_no$i\" name=\"txt_invoice_no[]\" class=\"text_boxes\" style=\"width:100px\" value=\"".$res[csf("invoice_no")]."\" disabled=\"disabled\" /><input type=\"hidden\" id=\"hidden_invoice_id$i\" value=\"".$res[csf("id")]."\" /></td>".
								"<td><input type=\"text\" id=\"txt_lcsc_no$i\" name=\"txt_lcsc_no[]\" class=\"text_boxes\" style=\"width:100px\" value=\"".$res[csf("lcsc_no")]."\" disabled=\"disabled\" /><input type=\"hidden\" id=\"txt_lcsc_id$i\" value=\"".$res[csf("lc_sc_id")]."\" /><input type=\"hidden\" id=\"hidden_is_lc$i\" value=\"".$res[csf("is_lc")]."\" /></td>".
								"<td><input type=\"text\" id=\"txt_bl_no$i\" name=\"txt_bl_no[]\" class=\"text_boxes\" style=\"width:100px\" value=\"".$res[csf("bl_no")]."\" disabled=\"disabled\" /></td>".
								"<td><input type=\"text\" id=\"txt_invoice_date$i\" name=\"txt_invoice_date[]\" class=\"text_boxes\" style=\"width:100px\" value=\"".$res[csf("invoice_date")]."\" disabled=\"disabled\" /></td>".
								"<td><input type=\"text\" id=\"txt_net_invo_value$i\" name=\"txt_net_invo_value[]\" class=\"text_boxes_numeric\" style=\"width:100px\" value=\"".$res[csf("net_invo_value")]."\" disabled=\"disabled\" /></td>".
								"<td><input type=\"text\" id=\"txt_po_numbers$i\" name=\"txt_po_numbers[]\" class=\"text_boxes\" style=\"width:180px\" value=\"".$po_numbers."\"  disabled=\"disabled\" /><input type=\"hidden\" id=\"hidden_po_numbers_id$i\" value=\"".$res[csf("po_breakdown_id")]."\" disabled=\"disabled\" /></td>".
								"<td><input type=\"text\" id=\"txt_feeder_vessel$i\" name=\"txt_feeder_vessel[]\" class=\"text_boxes\" style=\"width:100px\" value=\"".$res[csf("feeder_vessel")]."\" $disble_fv /></td>".
								"<td>".create_drop_down( "txt_forwarder_name$i", 100, "select s.id, s.supplier_name from lib_supplier s, lib_supplier_tag_company b where s.status_active =1 and s.is_deleted=0 and b.supplier_id=s.id and s.id in (select supplier_id from lib_supplier_party_type where party_type in (30,31,32)) group by s.id, s.supplier_name order by supplier_name",'id,supplier_name', 1, '--Select--',$res[csf('forwarder_name')], '',".$disble_fn.",'1,3' )."</td>".
							"</tr>"; 
			$tot_invoice_val+=$res[csf("net_invo_value")];
			$i++; 
		}
		$invoice_tr .= "<tr class=\"tbl_bottom\">".
							"<td colspan=\"5\" align=\"right\">Total</td>".
							"<td><input type=\"text\" name=\"txt_total\" id=\"txt_total\" class=\"text_boxes_numeric\" style=\"width:100px\" value=\"".$tot_invoice_val."\"  disabled=\"disabled\" /></td>".
							"<td colspan=\"3\">&nbsp;</td>".
						"</tr>";
		echo "$('#lcsc_no').val('".$lcscNoString."');\n";
		echo "$('#lc_sc_id').val('".$lcscNoID."');\n";	
		echo "$('#cbo_buyer_name').val('".$buyerID."');\n";	
		echo "$('#cbo_lien_bank').val('".$lienBank."');\n";
		echo "$('#cbo_currency').val('".$currencyID."');\n";
		
		//list view for invoice area----------------------------------------//
 		echo "$('#invo_table').find('tr:gt(0)').remove()".";\n";         
		echo "$('#invoice_container').html('".$invoice_tr."')".";\n";
		//echo "$('#invoice_container').find('input').attr('Disabled','Disabled');\n";
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	 
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
 	 	//---------------Check Duplicate Bank Ref/ Bill No  ------------------------//
		/*$duplicate = is_duplicate_field("id"," com_export_doc_submission_mst","bank_ref_no=$txt_bank_ref and company_id=$cbo_company_name  and buyer_id=$cbo_buyer_name"); 
		if($duplicate==1) 
		{			 
			echo "20**Duplicate Bank Ref. Number";
			die;
		}*/
		//------------------------------Check Duplicate END---------------------------------------//
		//master table entry here START---------------------------------------//		
		$id=return_next_id("id", "com_export_doc_submission_mst", 1);		
 		$field_array="id,company_id,buyer_id,submit_date,entry_form,submit_to,days_to_realize,possible_reali_date,courier_receipt_no,courier_company,courier_date,lien_bank,lc_currency,submit_type,remarks,inserted_by,insert_date";
		$data_array="(".$id.",".$cbo_company_name.",".$cbo_buyer_name.",".$txt_submit_date.",39,2,".$txt_day_to_realize.",".$txt_possible_reali_date.",".$courier_receipt_no.",".$txt_courier_company.",".$txt_courier_date.",".$cbo_lien_bank.",".$cbo_currency.",1,".$txt_remarks.",'".$user_id."','".$pc_date_time."')";
		//echo "20**".$field_array."<br>".$data_array;die;
		//master table entry here END---------------------------------------// 
 		 
 		//echo "insert into com_export_doc_submission_mst (".$field_array.") values ".$data_array;die;
 		//dtls table entry here START---------------------------------------//		
		$dtlsid=return_next_id("id", "com_export_doc_submission_invo", 1);		
		$field_array_dtls="id,doc_submission_mst_id,invoice_id,is_lc,lc_sc_id,bl_no,invoice_date,net_invo_value,all_order_no,inserted_by,insert_date";
		$field_array_update="feeder_vessel*forwarder_name*updated_by*update_date";
		
		$data_array_dtls="";
		for($i=1;$i<=$invoiceRow;$i++)
		{
			if($i>1) $data_array_dtls .= ",";
			$hidden_invoice_id 		= 'hidden_invoice_id'.$i;
			$txt_lcsc_id 			= 'txt_lcsc_id'.$i;
			$hidden_is_lc 			= 'hidden_is_lc'.$i;
			$txt_lcsc_no 			= 'txt_lcsc_no'.$i;
			$txt_bl_no 				= 'txt_bl_no'.$i;
			$txt_invoice_date 		= 'txt_invoice_date'.$i;
			$txt_net_invo_value 	= 'txt_net_invo_value'.$i;
			$hidden_po_numbers_id 	= 'hidden_po_numbers_id'.$i;
			$txt_feeder_vessel 		= 'txt_feeder_vessel'.$i;
			$txt_forwarder_name 	= 'txt_forwarder_name'.$i;

  			$data_array_dtls .= "(".$dtlsid.",".$id.",'".$$hidden_invoice_id."','".$$hidden_is_lc."','".$$txt_lcsc_id."','".$$txt_bl_no."','".$$txt_invoice_date."','".$$txt_net_invo_value."','".$$hidden_po_numbers_id."','".$user_id."','".$pc_date_time."')";	
			$dtlsid=$dtlsid+1;

			$id_arr[]=$$hidden_invoice_id;
			$data_array_update[str_replace("'","",$$hidden_invoice_id)] = explode("*",("'".$$txt_feeder_vessel."'*".$$txt_forwarder_name."*'".$user_id."'*'".$pc_date_time."'"));
			
		}
		//print_r($data_array_update);
 		//echo "20**".$field_array."<br>".$data_array;die;
		//echo "insert into com_export_doc_submission_mst (".$field_array.") values ".$data_array;die;
		$rID=sql_insert("com_export_doc_submission_mst",$field_array,$data_array,1);
		
		if($data_array_dtls!="")
		{
			
			$dtlsrID=sql_insert("com_export_doc_submission_invo",$field_array_dtls,$data_array_dtls,1);
			//echo $dtlsrID;die;
		}
		if(count($data_array_update)>0)
		{
			//echo "10**".bulk_update_sql_statement( "com_export_invoice_ship_mst", "id", $field_array_update, $data_array_update, $id_arr ); die;
			$rID2=execute_query(bulk_update_sql_statement( "com_export_invoice_ship_mst", "id", $field_array_update, $data_array_update, $id_arr ));			
		}
		//dtls table entry here END---------------------------------------// 
		
		
		//transaction table entry here START---------------------------------------//		
		/*$trid=return_next_id("id", "com_export_doc_submission_trans", 1);		
		$field_array="id,doc_submission_mst_id,acc_head,acc_loan,dom_curr,conver_rate,lc_sc_curr,inserted_by,insert_date";
		$data_array="";$tsrID=true;
		for($i=1;$i<=$transRow;$i++)
		{
			if($i>1) $data_array .= ",";
			$cbo_account_head 		= 'cbo_account_head_'.$i;
			$txt_ac_loan_no 		= 'txt_ac_loan_no_'.$i;
			$txt_domestic_curr 		= 'txt_domestic_curr_'.$i;
			$txt_conversion_rate 	= 'txt_conversion_rate_'.$i;
			$txt_lcsc_currency 		= 'txt_lcsc_currency_'.$i;
			
  			$data_array .= "(".$trid.",".$id.",'".$$cbo_account_head."','".$$txt_ac_loan_no."','".$$txt_domestic_curr."','".$$txt_conversion_rate."','".$$txt_lcsc_currency."','".$user_id."','".$pc_date_time."')";	
			$trid=$trid+1;
		}
 		//echo "20**".$field_array."<br>".$data_array;die;
		if($data_array!="")
		{
			$tsrID=sql_insert("com_export_doc_submission_trans",$field_array,$data_array,1);
		}*/
		//transaction table entry here END---------------------------------------// 
		
		//echo "20**".$rID." && ".$dtlsrID." && ".$tsrID;mysql_query("ROLLBACK");die;
		
		if($db_type==0)
		{
			if($rID && $dtlsrID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "0**".$id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10".$id;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $dtlsrID  && $rID2)
			{
				oci_commit($con);  
				echo "0**".$id;
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
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if(str_replace("'","",$mst_tbl_id)=="" || str_replace("'","",$invoice_tbl_id)=="") { echo "10**";disconnect($con);die; }
		
 		 
		//---------------Check Duplicate Bank Ref/ Bill No  ------------------------//
		/*$duplicate = is_duplicate_field("id"," com_export_doc_submission_mst"," id!=$mst_tbl_id and company_id=$cbo_company_name and buyer_id=$cbo_buyer_name"); 
		if($duplicate==1) 
		{			 
			echo "20**Duplicate Bank Ref. Number";
			die;
		}*/
		//------------------------------Check Duplicate END---------------------------------------//
		
		 
		
		//master table entry here START---------------------------------------//
		$cbo_submit_to=2;
		$entry_form=39;	
		$submission_type=1;	
 		$id=str_replace("'","",$mst_tbl_id);
		$field_array="company_id*buyer_id*submit_date*entry_form*submit_to*days_to_realize*possible_reali_date*courier_receipt_no*courier_company*courier_date*lien_bank*lc_currency*submit_type*remarks*updated_by*update_date";
		$data_array="".$cbo_company_name."*".$cbo_buyer_name."*".$txt_submit_date."*".$entry_form."*".$cbo_submit_to."*".$txt_day_to_realize."*".$txt_possible_reali_date."*".$courier_receipt_no."*".$txt_courier_company."*".$txt_courier_date."*".$cbo_lien_bank."*".$cbo_currency."*".$submission_type."*".$txt_remarks."*'".$user_id."'*'".$pc_date_time."'";
		$field_array_update="feeder_vessel*forwarder_name*updated_by*update_date";
		//echo "20**".$field_array."<br>".$data_array;die;
 		
		//master table entry here END---------------------------------------// 
 		//dtls table entry here START---------------------------------------//	
 		
		$dtlsid=return_next_id("id", "com_export_doc_submission_invo", 1);
		$field_array_dtls="id,doc_submission_mst_id,invoice_id,is_lc,lc_sc_id,bl_no,invoice_date,net_invo_value,all_order_no,inserted_by,insert_date";
		$data_array_dtls="";
		$byyer_sub_arr=array();
		for($i=1;$i<=$invoiceRow;$i++)
		{
			if($i>1) $data_array_dtls .= ",";
			$hidden_invoice_id 		= 'hidden_invoice_id'.$i;
			$txt_lcsc_id 			= 'txt_lcsc_id'.$i;
			$hidden_is_lc 			= 'hidden_is_lc'.$i;
			$txt_lcsc_no 			= 'txt_lcsc_no'.$i;
			$txt_bl_no 				= 'txt_bl_no'.$i;
			$txt_invoice_date 		= 'txt_invoice_date'.$i;
			$txt_net_invo_value 	= 'txt_net_invo_value'.$i;
			$hidden_po_numbers_id 	= 'hidden_po_numbers_id'.$i;
			$byyer_sub_arr[$$hidden_invoice_id]=$$hidden_invoice_id;
			$txt_feeder_vessel 		= 'txt_feeder_vessel'.$i;
			$txt_forwarder_name 	= 'txt_forwarder_name'.$i;
			
  			$data_array_dtls .= "(".$dtlsid.",".$id.",'".$$hidden_invoice_id."','".$$hidden_is_lc."','".$$txt_lcsc_id."','".$$txt_bl_no."','".$$txt_invoice_date."','".$$txt_net_invo_value."','".$$hidden_po_numbers_id."','".$user_id."','".$pc_date_time."')";	
			$dtlsid=$dtlsid+1;

			$id_arr[]=$$hidden_invoice_id;
			$data_array_update[str_replace("'","",$$hidden_invoice_id)] = explode("*",("'".$$txt_feeder_vessel."'*".$$txt_forwarder_name."*'".$user_id."'*'".$pc_date_time."'"));
		}
		
		$bank_submission_arr=return_library_array( "select b.id as id, b.invoice_id from  com_export_doc_submission_mst a, com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and a.entry_form=40 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ", "id", "invoice_id"  );
		
		$prev_buyer_sub_sql="select b.invoice_id from com_export_doc_submission_mst a, com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and a.entry_form=39 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$mst_tbl_id";
		$prev_buyer_sub_result=sql_select($prev_buyer_sub_sql);
		$prev_buy_sub_inv=array();
		foreach($prev_buyer_sub_result as $row)
		{
			$prev_buy_sub_inv[$row[csf("invoice_id")]]=$row[csf("invoice_id")];
		}
		$prev_buyer_sub_array=explode(",",str_replace("'","",$prev_submitted_inv));
		$diff_prev_vs_current=array_diff($prev_buy_sub_inv,$byyer_sub_arr);
		//$diff_prev_vs_current=$diff_prev_vs_current+$prev_buyer_sub_array;
		foreach($diff_prev_vs_current as $inv_id)
		{
			if(in_array($inv_id,$bank_submission_arr))
			{
				echo "50**Bank Submission Found, Update Not Allow.";disconnect($con);die;
			}
		}
 		//echo "20**".$invoiceRow."<br>".$data_array;die;
		$deleteDtls = execute_query("DELETE FROM com_export_doc_submission_invo WHERE doc_submission_mst_id=$mst_tbl_id");
		$dtlsrID=true;
		if($data_array_dtls!="")
		{
			$dtlsrID=sql_insert("com_export_doc_submission_invo",$field_array_dtls,$data_array_dtls,1);
		}
		$rID=sql_update("com_export_doc_submission_mst",$field_array,$data_array,"id",$mst_tbl_id,1);
		if(count($data_array_update)>0)
		{
			//echo "10**".bulk_update_sql_statement( "com_export_invoice_ship_mst", "id", $field_array_update, $data_array_update, $id_arr ); die;
			$rID2=execute_query(bulk_update_sql_statement( "com_export_invoice_ship_mst", "id", $field_array_update, $data_array_update, $id_arr ));			
		}

		//dtls table entry here END---------------------------------------// 
		//transaction table entry here START---------------------------------------//
		/*$deleteDtls = execute_query("DELETE FROM com_export_doc_submission_trans WHERE doc_submission_mst_id=$mst_tbl_id");		
		$trid=return_next_id("id", "com_export_doc_submission_trans", 1);		
		$field_array="id,doc_submission_mst_id,acc_head,acc_loan,dom_curr,conver_rate,lc_sc_curr,inserted_by,insert_date";
		$data_array="";$tsrID=true;
		for($i=1;$i<=$transRow;$i++)
		{
			if($i>1) $data_array .= ",";
			$cbo_account_head 		= 'cbo_account_head_'.$i;
			$txt_ac_loan_no 		= 'txt_ac_loan_no_'.$i;
			$txt_domestic_curr 		= 'txt_domestic_curr_'.$i;
			$txt_conversion_rate 	= 'txt_conversion_rate_'.$i;
			$txt_lcsc_currency 		= 'txt_lcsc_currency_'.$i;
			
  			$data_array .= "(".$trid.",".$id.",'".$$cbo_account_head."','".$$txt_ac_loan_no."','".$$txt_domestic_curr."','".$$txt_conversion_rate."','".$$txt_lcsc_currency."','".$user_id."','".$pc_date_time."')";	
			$trid=$trid+1;
		}
 		//echo "20**".$field_array."<br>".$data_array;die;
		if($data_array!="")
		{
			$tsrID=sql_insert("com_export_doc_submission_trans",$field_array,$data_array,1);
		}*/
		//transaction table entry here END---------------------------------------// 
		
		//echo "20**".$rID." && ".$dtlsrID." && ".$tsrID;mysql_query("ROLLBACK");die;
		
		
		if($db_type==0)
		{
			if($rID && $dtlsrID && $deleteDtls  && $rID2)
			{
				mysql_query("COMMIT");  
				echo "1**".$id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$id;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $dtlsrID && $deleteDtls && $rID2)
			{
				oci_commit($con);    
				echo "1**".$id;
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
	else if ($operation==2)   // Delete Here
	{

		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if(str_replace("'","",$mst_tbl_id)=="" || str_replace("'","",$invoice_tbl_id)=="") { echo "10**";disconnect($con);die; }
 		$id=str_replace("'","",$mst_tbl_id);
		
		$update_field_arr="updated_by*update_date*status_active*is_deleted";
		$update_data_arr="".$user_id."*'".$pc_date_time."'*0*1";
		$upsubDtlsID=$upsubTransid=$chk_sub_bank=true;
		$byyer_sub_arr=array();
		for($i=1;$i<=$invoiceRow;$i++)
		{
			$hidden_invoice_id 		= 'hidden_invoice_id'.$i;
			$byyer_sub_arr[$$hidden_invoice_id]=$$hidden_invoice_id;
		}
		$bank_submission_arr=return_library_array( "select b.id as id, b.invoice_id from  com_export_doc_submission_mst a, com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and a.entry_form=40 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ", "id", "invoice_id"  );
		//echo "10**<pre>"; 
		//print_r($diff_prev_vs_current) die;
		$prev_buyer_sub_array=explode(",",str_replace("'","",$prev_submitted_inv));
		$diff_prev_vs_current=array_diff($prev_buyer_sub_array,$byyer_sub_arr);
		$diff_prev_vs_current=$diff_prev_vs_current+$prev_buyer_sub_array;
		
		foreach($diff_prev_vs_current as $inv_id)
		{
			if(in_array($inv_id,$bank_submission_arr)){
				$chk_sub_bank=0;
				echo "50**Bank Submission Found, Delete Not Allow.";die;
			}
		}
		if($chk_sub_bank){
			$upsubDtlsID=sql_update("com_export_doc_submission_invo",$update_field_arr,$update_data_arr,"doc_submission_mst_id",$id,1);
			$upsubMstID=sql_update("com_export_doc_submission_mst",$update_field_arr,$update_data_arr,"id",$id,1);
		}
		//echo '10**'.$upsubDtlsID.'**'.$upsubMstID.'**'.$chk_sub_bank; die;	
		if($db_type==0)
		{
			if($upsubDtlsID && $upsubMstID && $chk_sub_bank){
				mysql_query("COMMIT");  
				echo "2**".$id;
			}else {
				mysql_query("ROLLBACK"); 
				echo "10**".$id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($upsubDtlsID && $upsubMstID && $chk_sub_bank){
				oci_commit($con);  
				echo "2**".$id;
			}else {
				oci_rollback($con);
				echo "10**".$id;
			}
		}
		disconnect($con);
		die;
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
                    <th colspan="5"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>                	 
                    <th width="140">System Id</th>
                    <th width="180">Invoice No</th>
                    <th width="180" align="center" id="search_by_td_up">Sales Contract No</th>
                    <th width="200">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr>
                	<td align="center"> <input type="text" id="txt_sys_no" name="txt_sys_no" class="text_boxes" style="width:100px;" > </td>                    
                    <td align="center">
                      <input type="text" style="width:150px" class="text_boxes"  name="txt_invoice_num" id="txt_invoice_num" />	
                    </td>
                    <td width="" align="center" id="search_by_td">				
                        <input type="text" style="width:150px" class="text_boxes"  name="txt_sales_con" id="txt_sales_con" />	
                    </td>    
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                    </td> 
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_invoice_num').value+'_'+document.getElementById('txt_sales_con').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company_name; ?>+'_'+<? echo $buyer_name; ?>+'_'+document.getElementById('txt_sys_no').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_system_id_search_list_view', 'search_div', 'export_doc_sub_buyer_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="5">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here-------->
                     <input type="hidden" id="hidden_system_number" value="" />
                    <!-- ---------END------------->
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
	$invoice_num = $ex_data[0];
	$sales_con = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company_name = $ex_data[4];
	$buyer_name = $ex_data[5];
	$sys_id_no = $ex_data[6];
	$search_type = $ex_data[7];

	//echo $invoice_num."##".$search_common."##".$txt_date_from."##".$txt_date_to."##".$company_name."##".$buyer_name."##".$sys_id_no;die;
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

	//echo $invoice_num;
	$sales_con_cond=$select_sc_id=$invoice_cond=$invoice_id="";
	if($sales_con!="")
	{
		$select_sc_id=return_field_value("id as contact_id","com_sales_contract","contract_no='$sales_con'","contact_id");
		if($select_sc_id!="") $sales_con_cond = " and b.lc_sc_id=$select_sc_id";
	}
	if($invoice_num!="")
	{
		//$invoice_id=return_field_value("id as invoice_id","com_export_invoice_ship_mst","invoice_no='$invoice_num'","invoice_id");
		//Content Search facility
		if ($search_type==1) $invoice_no_cond=" and invoice_no='$invoice_num'";
		else if ($search_type==2) $invoice_no_cond=" and invoice_no like '$invoice_num%'";
		else if ($search_type==3) $invoice_no_cond=" and invoice_no like '%$invoice_num'";
		else $invoice_no_cond=" and invoice_no like '%$invoice_num%'";

		$sql_invoice="select id as invoice_id, invoice_no from com_export_invoice_ship_mst where status_active=1 and is_deleted=0 $invoice_no_cond";
		$sql_invoice_res=sql_select($sql_invoice);
		$export_invoice_arr=array();
		foreach ($sql_invoice_res as $val) {
			$invoice_ids.=$val[csf("invoice_id")].',';
		}
		$invoice_ids=rtrim($invoice_ids,',');
		if($invoice_ids!="") $invoice_cond=" and b.invoice_id in($invoice_ids)";
	}
	
	
	$sql_cond=""; 
 	if( $txt_date_from!="" || $txt_date_to!="" )
	{
		if($db_type==0)
		{
			$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
		}
		else if($db_type==2)
		{
			$txt_date_from=change_date_format($txt_date_from,'','',-1);
			$txt_date_to=change_date_format($txt_date_to,'','',-1);
		}
		$sql_cond = " and a.submit_date between '".$txt_date_from."' and '".$txt_date_to."'";
	}
	if(trim($company_name)>0) $sql_cond .= " and a.company_id ='$company_name'";
	if(trim($buyer_name)>0) $sql_cond .= " and a.buyer_id ='$buyer_name'";
	if($sys_id_no!="")  $sys_cond=" and a.id=$sys_id_no";
	
	$bank_arr = return_library_array( "select id, bank_name from lib_bank",'id','bank_name');

	if($db_type==0){
		$is_lc_string_cond=" , GROUP_CONCAT(b.is_lc) as is_lc_string";
		$invoice_id_string_cond=" , GROUP_CONCAT(b.invoice_id) as invoice_id_string";
		$lc_sc_id_string_cond=" , GROUP_CONCAT(b.lc_sc_id) as lc_sc_id_string ";
	} else if($db_type==2){
		$is_lc_string_cond=" , rtrim(xmlagg(xmlelement(e,b.is_lc,',').extract('//text()') order by b.is_lc).GetClobVal(),',') as is_lc_string";
		$invoice_id_string_cond=" , rtrim(xmlagg(xmlelement(e,b.invoice_id,',').extract('//text()') order by b.invoice_id).GetClobVal(),',') as invoice_id_string";
		$lc_sc_id_string_cond=" , rtrim(xmlagg(xmlelement(e,b.lc_sc_id,',').extract('//text()') order by b.lc_sc_id).GetClobVal(),',') as lc_sc_id_string";
	} 

	if($db_type==0)
	{
		$sql = "select a.id,a.bank_ref_no,a.submit_date,a.buyer_id,sum(b.net_invo_value) as net_invo_value,a.submit_to,a.lien_bank,a.submit_type $is_lc_string_cond $invoice_id_string_cond $lc_sc_id_string_cond
			from  com_export_doc_submission_mst a, com_export_doc_submission_invo b
			where a.id=b.doc_submission_mst_id  and a.entry_form=39 and b.is_lc=2 and a.status_active=1 and b.status_active=1  $sql_cond  $sales_con_cond $invoice_cond $sys_cond group by a.id";	
	}
	else if($db_type==2)
	{
		$sql = "select a.id,a.submit_date,a.buyer_id,sum(b.net_invo_value) as net_invo_value,a.submit_to,a.lien_bank,a.submit_type$is_lc_string_cond $invoice_id_string_cond $lc_sc_id_string_cond
			from  com_export_doc_submission_mst a, com_export_doc_submission_invo b
			where a.id=b.doc_submission_mst_id and a.entry_form=39 and b.is_lc=2 and a.status_active=1 and b.status_active=1  $sql_cond  $sales_con_cond $invoice_cond $sys_cond group by a.id,a.submit_date,a.buyer_id,a.submit_to,a.lien_bank,a.submit_type";
	}
	
	//echo $sql;
	$res = sql_select($sql);
    ?>
    <div style="width:918px;">
        <table border="0" width="100%" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <thead>
                <th width="30">SL</th>
                <th width="80">System Id</th>
                <th width="100">Buyer</th>
                <th width="150">Sales Contract No</th>
                <th width="170">Invoice No</th>
                <th width="80">Submit Date</th>               
                <th width="180">Lien Bank</th>
                <th >Invoice Value</th>
            </thead>
        </table>
    </div>            
    <div style="width:918px; overflow-y:scroll; max-height:230px">
        <table border="0" width="900" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="list_view">             
   		<?                     
        	$i=1;
			foreach($res as $row)
			{  
				
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 

				if($db_type==2 && $row[csf("is_lc_string")]!="") $row[csf("is_lc_string")] = $row[csf("is_lc_string")]->load();
				if($db_type==2 && $row[csf("invoice_id_string")]!="") $row[csf("invoice_id_string")] = $row[csf("invoice_id_string")]->load();
				if($db_type==2 && $row[csf("lc_sc_id_string")]!="") $row[csf("lc_sc_id_string")] = $row[csf("lc_sc_id_string")]->load();
				
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
				$scSQL=sql_select("select contract_no from com_sales_contract where id in (".$scID.")");
				foreach($scSQL as $poR)
				{
					if($lc_sc_no=="") $lc_sc_no=$poR[csf("contract_no")]; else $lc_sc_no.=",".$poR[csf("contract_no")];
				} 
				
				//invoice list 
				$invoiceNo="";
				if($row[csf("invoice_id_string")]!="")
				{
					$invoiceSQL=sql_select("select invoice_no from com_export_invoice_ship_mst where  id in (".$row[csf("invoice_id_string")].") ");
					//echo "select invoice_no from com_export_invoice_shipping_mst where  id in (".$row[csf("invoice_id_string")].") and invoice_no='$invoice_no' ";
					foreach($invoiceSQL as $poR)
					{
						if($invoiceNo=="") $invoiceNo=$poR[csf("invoice_no")]; else $invoiceNo.=",".$poR[csf("invoice_no")];
					} 
				}
					//echo array_unique($row[csf("id")]);//$mst_id=array_unique($row[csf("id")]);
          		?>     
			   		<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")];?>)" > 
                        <td width="30" align="center"><? echo $i;?></td>
                        <td width="80" align="center"><? echo $row[csf("id")];?></td>
                        <td width="100"><p><? echo $buyer_library[$row[csf("buyer_id")]]; ?></p></td>                    
                        <td width="150"><p><? echo $lc_sc_no; ?></p></td>
                        <td width="170"><p><? echo $invoiceNo; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf("submit_date")]); ?></td>
                        
                        <td width="180"><p><? echo $bank_arr[$row[csf("lien_bank")]]; ?></p></td>
                        <td align="right"><p><? echo $row[csf("net_invo_value")]; ?></p></td>
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
	if($db_type==0){
		$inv_id_cond=" , ,group_concat(b.id) as inv_id";
		$is_lc_string_cond=" , GROUP_CONCAT(b.is_lc SEPARATOR ',') as is_lc_string";
		$invoice_id_string_cond=" , GROUP_CONCAT(b.invoice_id SEPARATOR ',') as invoice_id_string ";
		$lc_sc_id_string_cond=" , GROUP_CONCAT(b.lc_sc_id SEPARATOR ',') as lc_sc_id_string ";
	} else if($db_type==2){
		$inv_id_cond=" , rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as inv_id";
		$is_lc_string_cond=" , rtrim(xmlagg(xmlelement(e,b.is_lc,',').extract('//text()') order by b.is_lc).GetClobVal(),',') as is_lc_string";
		$invoice_id_string_cond=" , rtrim(xmlagg(xmlelement(e,b.invoice_id,',').extract('//text()') order by b.invoice_id).GetClobVal(),',') as invoice_id_string";
		$lc_sc_id_string_cond=" , rtrim(xmlagg(xmlelement(e,b.lc_sc_id,',').extract('//text()') order by b.lc_sc_id).GetClobVal(),',') as lc_sc_id_string";
	} 

	if($db_type==0)
	{
	$sql = "select a.id,company_id,buyer_id,submit_date,submit_to,bank_ref_no,bank_ref_date,days_to_realize,possible_reali_date,courier_receipt_no,courier_company,courier_date,bnk_to_bnk_cour_no,bnk_to_bnk_cour_dt,lien_bank,lc_currency,submit_type,negotiation_date,remarks $inv_id_cond $is_lc_string_cond $invoice_id_string_cond $lc_sc_id_string_cond
			from  com_export_doc_submission_mst a, com_export_doc_submission_invo b
			where a.id=b.doc_submission_mst_id and a.entry_form=39 and a.id=$data group by a.id";
	}
	else if($db_type==2)
	{
	$sql = "select a.id,a.company_id,a.buyer_id,a.submit_date,a.submit_to,a.days_to_realize,a.possible_reali_date,a.courier_receipt_no,a.courier_company,a.courier_date,a.lien_bank,a.lc_currency,a.submit_type,a.remarks $inv_id_cond $is_lc_string_cond $invoice_id_string_cond $lc_sc_id_string_cond
			from  com_export_doc_submission_mst a, com_export_doc_submission_invo b
			where a.id=b.doc_submission_mst_id and a.entry_form=39 and a.id=$data 
			group by a.id,a.company_id,a.buyer_id,a.submit_date,a.submit_to,a.days_to_realize,a.possible_reali_date,a.courier_receipt_no,a.courier_company,a.courier_date,a.lien_bank,a.lc_currency,a.submit_type,a.remarks ";	
	}
	//echo $sql;
	$res = sql_select($sql);
	$i=1;
	foreach($res as $row)
	{
		if($db_type==2 && $row[csf("is_lc_string")]!="") $row[csf("is_lc_string")] = $row[csf("is_lc_string")]->load();
		if($db_type==2 && $row[csf("invoice_id_string")]!="") $row[csf("invoice_id_string")] = $row[csf("invoice_id_string")]->load();
		if($db_type==2 && $row[csf("lc_sc_id_string")]!="") $row[csf("lc_sc_id_string")] = $row[csf("lc_sc_id_string")]->load();
		if($db_type==2 && $row[csf("inv_id")]!="") $row[csf("inv_id")] = $row[csf("inv_id")]->load();
		
  		echo "$('#cbo_company_name').val('".$row[csf("company_id")]."');\n";
		echo "$('#cbo_buyer_name').val('".$row[csf("buyer_id")]."');\n";
		$invoice_id_string=implode(",",array_unique(explode(",",$row[csf("invoice_id_string")])));
		echo "$('#prev_submitted_inv').val('".$invoice_id_string."');\n";
 		
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
			$poSQL=sql_select("select export_lc_no from com_export_lc where id in (".$lcID.")");
			foreach($poSQL as $poR)
			{
				if($lc_sc_no=="") $lc_sc_no=$poR[csf("export_lc_no")]; else $lc_sc_no.=",".$poR[csf("export_lc_no")];
			} 
		}
		if($scID!="")
		{
			//Sales Contact Number
			$scSQL=sql_select("select contract_no from com_sales_contract where id in (".$scID.")");
			foreach($scSQL as $poR)
			{
				if($lc_sc_no=="") $lc_sc_no=$poR[csf("contract_no")]; else $lc_sc_no.=",".$poR[csf("contract_no")];
			}  		 
		}
		echo "$('#lcsc_no').val('".$lc_sc_no."');\n";  
		$lc_sc_id=implode(",",array_unique(explode(",",$row[csf("lc_sc_id_string")])));
		echo "$('#lc_sc_id').val('".$lc_sc_id."');\n"; 
		echo "$('#invoice_id_string').val('".$row[csf("invoice_id_string")]."');\n"; 
		if($row[csf("submit_date")]!='0000-00-00')
		{		 
 			echo "$('#txt_submit_date').val('".change_date_format($row[csf("submit_date")])."');\n";
		}
		else
		{
			echo "$('#txt_submit_date').val('');\n";
		}
		
		
		/*echo "$('#cbo_submission_type').val('".$row[csf("submit_type")]."');\n";
		if($row[csf("submit_type")]==1)
		{
		 echo "$('#txt_negotiation_date').attr('disabled',true);\n";
		}
		else
		{
			if($row[csf("negotiation_date")]!='0000-00-00')
			{
				echo "$('#txt_negotiation_date').attr('disabled',false);\n";
				echo "$('#txt_negotiation_date').val('".change_date_format($row[csf("negotiation_date")])."');\n";
			}
			else
			{
				echo "$('#txt_negotiation_date').val('');\n";
			}
		}*/
		
		
		echo "$('#txt_day_to_realize').val('".$row[csf("days_to_realize")]."');\n";
		if($row[csf("possible_reali_date")]!='0000-00-00')
		{
			echo "$('#txt_possible_reali_date').val('".change_date_format($row[csf("possible_reali_date")])."');\n";
		}
		else
		{
			echo "$('#txt_possible_reali_date').val('');\n";
		}
		echo "$('#courier_receipt_no').val('".$row[csf("courier_receipt_no")]."');\n";
		echo "$('#txt_courier_company').val('".$row[csf("courier_company")]."');\n";
		if($row[csf("courier_date")]!='0000-00-00')
		{
			echo "$('#txt_courier_date').val('".change_date_format($row[csf("courier_date")])."');\n";
		}
		else
		{
			echo "$('#txt_courier_date').val('');\n";
		}
		/*echo "$('#txt_bnk_to_bnk_cour_no').val('".$row[csf("bnk_to_bnk_cour_no")]."');\n";
		if($row[csf("bnk_to_bnk_cour_dt")]!='0000-00-00')
		{
		echo "$('#txt_bnk_to_bnk_cour_date').val('".change_date_format($row[csf("bnk_to_bnk_cour_dt")])."');\n";
		}
		else
		{
			echo "$('#txt_bnk_to_bnk_cour_date').val('');\n";
		}*/
		echo "$('#cbo_lien_bank').val('".$row[csf("lien_bank")]."');\n";
		echo "$('#cbo_currency').val('".$row[csf("lc_currency")]."');\n";
		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		
		echo "$('#mst_tbl_id').val('".$row[csf("id")]."');\n";
		echo "$('#invoice_tbl_id').val('".$row[csf("inv_id")]."');\n";
		
		//invoice list view -----------------------------------------------------------------------//
		//start -----------------------------------------------------------------------//
		$invSQL="select a.id,a.doc_submission_mst_id,a.invoice_id,a.is_lc,a.lc_sc_id,b.bl_no,a.invoice_date,a.net_invo_value,a.all_order_no, b.invoice_no, b.feeder_vessel, b.forwarder_name, b.lc_for from com_export_doc_submission_invo a, com_export_invoice_ship_mst b where a.invoice_id=b.id and a.doc_submission_mst_id=".$row[csf("id")]." and a.is_lc=2";
 		//echo $invSQL;
		$resArray=sql_select($invSQL);
		$tot_invoice_val=0;
		
 		foreach($resArray as $invRow)
		{ 
 			//ls or sc number
 			if($invRow[csf("feeder_vessel")]!='')
			{
				$disble_fv='disabled=\"disabled\"';
			}
			else
			{
				$disble_fv='';
			}
			if($invRow[csf("forwarder_name")]!=0 && $invRow[csf("forwarder_name")]!='')
			{
				$disble_fn=1;
			}
			else
			{
				$disble_fn=0;
			}
			if($invRow[csf("is_lc")]==1)
				$lc_sc_nos = return_field_value("export_lc_no","com_export_lc","id=".$invRow[csf("lc_sc_id")].""); 
			else
				$lc_sc_nos = return_field_value("contract_no","com_sales_contract","id=".$invRow[csf("lc_sc_id")].""); 
				
			//list view data arrange-----------------// 
			if($invRow[csf("lc_for")]==2)
			{
				$poSQL=sql_select("select requisition_number as po_number from sample_development_mst where id in (".$invRow[csf("all_order_no")].")");
			}
			else
			{
				$poSQL=sql_select("select po_number from wo_po_break_down where id in (".$invRow[csf("all_order_no")].")");
			}
			
			$po_numbers="";
			foreach($poSQL as $poR)
			{
				if($po_numbers=="") $po_numbers=$poR[csf("po_number")]; else $po_numbers.=",".$poR[csf("po_number")];
			}
			$invoice_tr .= "<tr>".
								"<td align=\"center\">".$i."</td>".
								"<td><input type=\"text\" id=\"txt_invoice_no$i\" name=\"txt_invoice_no[]\" class=\"text_boxes\" style=\"width:100px\" value=\"".$invRow[csf("invoice_no")]."\"  disabled=\"disabled\" /><input type=\"hidden\" id=\"hidden_invoice_id$i\" value=\"".$invRow[csf("invoice_id")]."\" /></td>".
								"<td><input type=\"text\" id=\"txt_lcsc_no$i\" name=\"txt_lcsc_no[]\" class=\"text_boxes\" style=\"width:100px\" value=\"".$lc_sc_nos."\"   disabled=\"disabled\" /><input type=\"hidden\" id=\"txt_lcsc_id$i\" value=\"".$invRow[csf("lc_sc_id")]."\" /><input type=\"hidden\" id=\"hidden_is_lc$i\" value=\"".$invRow[csf("is_lc")]."\"   disabled=\"disabled\" /></td>".
								"<td><input type=\"text\" id=\"txt_bl_no$i\" name=\"txt_bl_no[]\" class=\"text_boxes\" style=\"width:100px\" value=\"".$invRow[csf("bl_no")]."\" disabled=\"disabled\" /></td>".
								"<td><input type=\"text\" id=\"txt_invoice_date$i\" name=\"txt_invoice_date[]\" class=\"text_boxes\" style=\"width:100px\" value=\"".$invRow[csf("invoice_date")]."\" disabled=\"disabled\" /></td>".
								"<td><input type=\"text\" id=\"txt_net_invo_value$i\" name=\"txt_net_invo_value[]\" class=\"text_boxes_numeric\" style=\"width:100px\" value=\"".$invRow[csf("net_invo_value")]."\"   disabled=\"disabled\" /></td>".
								"<td><input type=\"text\" id=\"txt_po_numbers$i\" name=\"txt_po_numbers[]\" class=\"text_boxes\" style=\"width:200px\" value=\"".$po_numbers."\"   disabled=\"disabled\" /><input type=\"hidden\" id=\"hidden_po_numbers_id$i\" value=\"".$invRow[csf("all_order_no")]."\"   disabled=\"disabled\" /></td>".
								"<td><input type=\"text\" id=\"txt_feeder_vessel$i\" name=\"txt_feeder_vessel[]\" class=\"text_boxes\" style=\"width:100px\" value=\"".$invRow[csf("feeder_vessel")]."\" $disble_fv /></td>".
								"<td>".create_drop_down( "txt_forwarder_name$i", 100, "select s.id, s.supplier_name from lib_supplier s, lib_supplier_tag_company b where s.status_active =1 and s.is_deleted=0 and b.supplier_id=s.id and s.id in (select supplier_id from lib_supplier_party_type where party_type in (30,31,32)) group by s.id, s.supplier_name order by supplier_name",'id,supplier_name', 1, '--Select--',$invRow[csf('forwarder_name')], '',".$disble_fn.",'1,3' )."</td>".
							"</tr>"; 
			
			$tot_invoice_val+=$invRow[csf("net_invo_value")];
			$i++; 
		}
		
		$invoice_tr.= "<tr class=\"tbl_bottom\">".
							"<td colspan=\"5\" align=\"right\">Total</td>".
							"<td><input type=\"text\" name=\"txt_total\" id=\"txt_total\" class=\"text_boxes_numeric\" style=\"width:100px\" value=\"".$tot_invoice_val."\"   disabled=\"disabled\" /></td>".
							"<td colspan=\"3\">&nbsp;</td>".
						"</tr>";
		//list view for invoice area----------------------------------------//
 		echo "$('#invo_table').find('tr:gt(0)').remove()".";\n";         
		echo "$('#invoice_container').html( '".$invoice_tr."')".";\n";
		//echo "$('#invoice_container').find('input').attr('Disabled','Disabled');\n";
		
 		//invoice list view -----------------------------------------------------------------------//
		//END -----------------------------------------------------------------------//
		
		 
		
		//transaction list generate here Start------------------------------------//
		/*$invSQL =  "select a.id,a.doc_submission_mst_id,a.acc_head,a.acc_loan,a.dom_curr,a.conver_rate,a.lc_sc_curr 
					from com_export_doc_submission_trans a, com_export_invoice_shipping_mst b 
					where a.doc_submission_mst_id=b.id and a.doc_submission_mst_id=".$row[csf("id")]."";
 		//echo $invSQL;
		$resArray=sql_select($invSQL);
		$rowNo=1; $transaction_tr="";
 		foreach($resArray as $invRow)
		{
			$transaction_tr .= "<tr id=\"tr".$rowNo."\">".
					"<td>".create_drop_down( "cbo_account_head_".$rowNo, 200, $commercial_head,"", 1, "-- Select --", $invRow[csf("acc_head")], "get_php_form_data(this.value+\'**\'+".$rowNo.", \'populate_acc_loan_no_data\', \'requires/export_doc_sub_buyer_entry_controller\' )",0, "" )."</td>".
					"<td><input type=\"text\" id=\"txt_ac_loan_no_".$rowNo."\" name=\"txt_ac_loan_no[]\" class=\"text_boxes\" style=\"width:100px\" value=\"".$invRow[csf("acc_loan")]."\" /></td>".
					"<td><input type=\"text\" id=\"txt_domestic_curr_".$rowNo."\" name=\"txt_domestic_curr[]\" class=\"text_boxes_numeric\" style=\"width:100px\" value=\"".$invRow[csf("dom_curr")]."\" onkeyup=\"fn_calculate(this.id,".$rowNo.")\" /></td>".
					"<td><input type=\"text\" id=\"txt_conversion_rate_".$rowNo."\" name=\"txt_conversion_rate[]\" class=\"text_boxes_numeric\" style=\"width:100px\" value=\"".$invRow[csf("conver_rate")]."\" onkeyup=\"fn_calculate(this.id,".$rowNo.")\" /></td>".
					"<td><input type=\"text\" id=\"txt_lcsc_currency_".$rowNo."\" name=\"txt_lcsc_currency[]\" class=\"text_boxes_numeric\" style=\"width:100px\" value=\"".$invRow[csf("lc_sc_curr")]."\" onkeyup=\"fn_calculate(this.id,".$rowNo.")\" /></td>".
					"<td>".
					"<input type=\"button\" id=\"increaserow_".$rowNo."\" style=\"width:30px\" class=\"formbutton\" value=\"+\" onClick=\"javascript:fn_inc_decr_row(".$rowNo.",\'increase\');\" />".
					"<input type=\"button\" id=\"decreaserow_".$rowNo."\" style=\"width:30px\" class=\"formbutton\" value=\"-\" onClick=\"javascript:fn_inc_decr_row(".$rowNo.",\'decrease\');\" />".
					"</td>".
			"</tr>";	
			$rowNo = $rowNo+1;
		}
		
		//list view for transaction area----------------------------------------//
 		echo "$('#transaction_container').find('tr').remove();\n";         
		echo "$('#transaction_container').html( '".$transaction_tr."')".";\n";	*/
		//echo "sum_of_currency();\n";	
		//transaction list generate here End------------------------------------//
		
				 
   	}//main foreach end
		
	exit();	
}

if($action=="buyer_submit_letter")
{
	//echo load_html_head_contents("Buyer Submission Letter","../../", 1, 1, $unicode,'','');
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

	</style>
	<?
	$data=explode('**',$data);
	$order_inv_array=array();
	if($db_type==0)
	{
		$sql_order_inv=sql_select("select b.id as po_id, b.po_number as po_no, c.style_ref_no as style_ref_no, date_format(c.insert_date,'%d-%m-%Y') as style_date from  wo_po_break_down b, wo_po_details_master c where b.job_no_mst=c.job_no");
		
	}
	else
	{
		$sql_order_inv=sql_select("select b.id as po_id, b.po_number as po_no, c.style_ref_no as style_ref_no,to_char(c.insert_date,'DD-MM-YYYY') as style_date 
		from wo_po_break_down b, wo_po_details_master c where b.job_no_mst=c.job_no ");
	}
	foreach($sql_order_inv as $row)
	{
		$order_inv_array[$row[csf("po_id")]]["po_no"]=$row[csf("po_no")];
		$order_inv_array[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
		$order_inv_array[$row[csf("po_id")]]["style_date"]=$row[csf("style_date")];
	}

	
	//print_r($order_inv_array);
	
	/*if($db_type==0)
	{
		$all_inv_id=return_field_value("group_concat(b.invoice_id) inv_id","com_export_doc_submission_mst a, com_export_doc_submission_invo b","a.id=b.doc_submission_mst_id and a.entry_form=39 and a.id=".$data,"inv_id");
		
		$sql_order_inv=sql_select("select a.mst_id, group_concat(b.po_number) as po_no, group_concat(c.style_ref_no) as style_ref_no, group_concat(date_format(c.insert_date,'%d-%m-%Y')) as style_date from com_export_invoice_ship_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.mst_id in($all_inv_id) group by a.mst_id");
		
	}
	else
	{
		$all_inv_id=return_field_value("listagg(cast(b.invoice_id as varchar(4000)), ',') within group(order by b.invoice_id) as inv_id","com_export_doc_submission_mst a, com_export_doc_submission_invo b","a.id=b.doc_submission_mst_id and a.entry_form=39 and a.id=".$data,"inv_id");
		
		
		$sql_order_inv=sql_select("select a.mst_id, listagg(cast(b.po_number as varchar(4000)), ',') within group(order by b.po_number) as po_no, listagg(cast(c.style_ref_no as varchar(4000)), ',') within group(order by c.style_ref_no) as style_ref_no, listagg(cast(to_char(c.insert_date,'DD-MM-YYYY') as varchar(4000)), ',') within group(order by to_char(c.insert_date,'DD-MM-YYYY')) as style_date from com_export_invoice_ship_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.mst_id in($all_inv_id) group by a.mst_id");
	}*/
	//var_dump($order_inv_array);die;
	
	if($data[1]==1)
	{
			
		
		$sql_sub_buyer="select a.id as sub_id, a.company_id, a.buyer_id, a.submit_date, b.is_lc, b.lc_sc_id, b.all_order_no, c.id as inv_id, c.invoice_no, c.invoice_date, c.invoice_quantity, b.net_invo_value as invoice_value, c.bl_no, c.bl_date from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form=39 and a.id=$data[0]";
		//echo $sql_sub_buyer;//die;
		$result=sql_select($sql_sub_buyer);
		
		$sql_buyer_info=sql_select("select id, buyer_name, contact_person, address_1 from lib_buyer where id='".$result[0][csf("buyer_id")]."' ");
		
		$sql_lc=sql_select("SELECT id,export_lc_no,lc_date FROM com_export_lc ");
		foreach($sql_lc as $row)
		{
			$export_lc_no_arr[$row[csf("id")]]["export_lc_no"]=$row[csf("export_lc_no")];
			$export_lc_no_arr[$row[csf("id")]]["lc_date"]=$row[csf("lc_date")];
		}
		$sql_sc=sql_select("SELECT id,contract_no,contract_date FROM com_sales_contract ");
		foreach($sql_sc as $row)
		{
			$export_sc_no_arr[$row[csf("id")]]["contract_no"]=$row[csf("contract_no")];
			$export_sc_no_arr[$row[csf("id")]]["contract_date"]=$row[csf("contract_date")];
		}
		
	
		 
		/*//echo $data."jahid";die;
		if($db_type==2)
		{
			$sql_com="select a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value, a.margin, listagg(cast(b.is_lc_sc || '__' || b.lc_sc_id as varchar(4000)),',') within group (order by b.is_lc_sc,b.lc_sc_id) as lc_sc  from com_btb_lc_master_details a, com_btb_export_lc_attachment b where a.id=b.import_mst_id and  a.id=$data and a.is_deleted = 0 AND a.status_active = 1 group by a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value, a.margin";
		}
		elseif($db_type==0)
		{
			$sql_com="select a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value, a.margin, group_concat(concat(b.is_lc_sc, '__', b.lc_sc_id)) as lc_sc  from com_btb_lc_master_details a, com_btb_export_lc_attachment b where a.id=b.import_mst_id and  a.id=$data and a.is_deleted = 0 AND a.status_active = 1 group by a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.supplier_id, a.origin, a.issuing_bank_id, a.currency_id, a.lc_value, a.margin";
		}
		
		$result=sql_select($sql_com);
		
		$company_name = return_field_value("company_name","lib_company","id=".$result[0][csf("importer_id")],"company_name");
		$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$result[0][csf("supplier_id")],"supplier_name");
		$country_name = return_field_value("country_name"," lib_country","id=".$result[0][csf("origin")],"country_name");*/
		
		//echo $sql_com;
		
		?>
		
		<table width="700" cellpadding="0" align="left" cellspacing="0" border="0">
			<tr>
				<td width="25" ></td>
				<td width="650" align="left">Dated : <? echo change_date_format($result[0][csf("submit_date")]); ?> </td>
				<td width="25" ></td>
			</tr>
			<tr>
				<td colspan="3" height="20"></td>
			</tr>
			<tr>
				<td width="25" ></td>
				<td width="650" align="left">
				<?
					echo "The Manager, <br>";
					echo $sql_buyer_info[0][csf("buyer_name")];
					$adress_all=explode(",",$sql_buyer_info[0][csf("address_1")]);
					$i=0;
					$address_first="";
					foreach($adress_all as $adress)
					{
						if($i%3==0) $address_first.="<br>";
						$address_first.=$adress.", ";
						$i++;
					}
					$address_first=chop($address_first," , ");
					echo $address_first;
					//echo $bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["contact_person"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["bank_name"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["branch_name"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["address"];
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
				Subject: Submission of shipping documents with original BL & CO after payment realization.
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
				<td width="25" ></td>
				<td width="650" align="left">
				We like to inform you that we have submitted the following shipping documents to your office for payment. 
				</td>
				<td width="25" ></td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			<tr>
				<td width="25" ></td>
				<td width="650" align="left">
					<table cellpadding="0" align="left" cellspacing="0" border="1" width="650">
						<thead>
							<tr>
								<th width="80">Lc/Sc. No <br> Date</th>
								<th width="120">Order No</th>
								<th width="100">Invoice No <br> Date</th>
								<th width="120">Style No</th>
								<th width="60">Quantity</th>
								<th width="70">Value</th>
								<th>Bl No <br> Date</th>
							</tr>
						</thead>
						<tbody>
						<?
						$i=1;
						foreach($result as $row)
						{
							$all_order_arr=explode(",",$row[csf("all_order_no")]);
							$all_po=$all_style=$all_style_date="";
							foreach($all_order_arr as $order_id)
							{
								$all_po.=$order_inv_array[$order_id]["po_no"].", ";
								$all_style.=$order_inv_array[$order_id]["style_ref_no"].", ";
								$all_style_date.=$order_inv_array[$order_id]["style_date"].", ";
							}
							$all_po=chop($all_po," , ");
							$all_style=chop($all_style," , ");
							$all_style_date=chop($all_style_date," , ");
							if($row[csf("bl_date")]!="" && $row[csf("bl_date")]!='0000-00-00') $bl_date=change_date_format($row[csf("bl_date")]); else $bl_date="&nbsp;";
							?>
							<tr>
								<td width="80" valign="top"><div style="width:80px;; word-wrap:break-word;">
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
								<td width="120" valign="top"><div style="width:120px;; word-wrap:break-word;"><? echo $all_po; ?></div></td>
								<td width="100" valign="top"><div style="width:100px;; word-wrap:break-word;"><? echo $row[csf("invoice_no")]."<br>".$row[csf("invoice_date")];?> </div></td>
								<td width="120" valign="top"><div style="width:120px;; word-wrap:break-word;"><? echo $all_style."<br>".$all_style_date;?></div></td>
								<td width="60" valign="top" align="right"><div style="width:60px;; word-wrap:break-word;"><? echo number_format($row[csf("invoice_quantity")],2,'.','')?></div></td>
								<td width="70" valign="top" align="right"><div style="width:70px;; word-wrap:break-word;"><? echo number_format($row[csf("invoice_value")],2,'.','')?></div></td>
								<td valign="top"><? echo $row[csf("bl_no")]."<br>".$bl_date; ?></td>
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
				Please acknowledge receipt for above shipping documents with original  BL endorsed by our bank.
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
				Thanking You,
				</td>
				<td width="25" ></td>
			</tr>
			<tr>
				<td colspan="3" height="50"></td>
			</tr>
			<tr>
				<td width="25" ></td>
				<td width="650" align="left">
					<? //echo $company_name;?>
				</td>
				<td width="25" ></td>
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
	else if($data[1]==2)
	{
		$country_library=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );		
		if($db_type==0)
		{
			$sql_sub_info=sql_select("select a.id as sub_id, a.company_id, a.buyer_id, year(a.submit_date) as sub_year, max(c.forwarder_name) as forwarder_name  from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form=39 and a.id=$data[0] group by a.id, a.company_id, a.buyer_id");
		}
		else
		{
			$sql_sub_info=sql_select("select a.id as sub_id, a.company_id, a.buyer_id, to_char(a.submit_date,'yyyy') as sub_year, max(c.forwarder_name) as forwarder_name  from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form=39 and a.id=$data[0] group by a.id, a.company_id, a.buyer_id, to_char(a.submit_date,'yyyy')");
		}
		
		
		if($sql_sub_info[0][csf("forwarder_name")]>0)
		{
			$sql_forwarder_info=sql_select("select id, supplier_name, short_name, address_1 from lib_supplier where id='".$sql_sub_info[0][csf("forwarder_name")]."' ");
		}
		
		//echo $sql_forwarder_info[0][csf("short_name")];die;
		
		//echo "select id, buyer_name, short_name, contact_person, address_1 from lib_buyer where id='".$sql_sub_info[0][csf("buyer_id")]."' ";
		$sql_buyer_info=sql_select("select id, buyer_name, short_name, contact_person, address_1 from lib_buyer where id='".$sql_sub_info[0][csf("buyer_id")]."' ");
		$sql_company_info=sql_select("select id, company_name, company_short_name from  lib_company where id='".$sql_sub_info[0][csf("company_id")]."' ");
		
		//$sql_forwarder_info=sql_select("select id, company_name, company_short_name, contact_person from  com_export_invoice_ship_mst a, lib_buyer b  where id='".$sql_sub_info[0][csf("company_id")]."' ");
		
		$sql_sub_buyer="select a.id as sub_id, a.company_id, a.buyer_id, a.submit_date, b.is_lc, b.lc_sc_id, b.all_order_no, c.id as inv_id, c.invoice_no, c.invoice_date, c.invoice_quantity, b.net_invo_value as invoice_value, c.bl_no, c.bl_date, c.country_id, c.feeder_vessel from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form=39 and a.id=$data[0]";
		//echo $sql_sub_buyer;die;
		$result=sql_select($sql_sub_buyer);		 
		
		?>
		
		<table width="700" cellpadding="0" align="left" cellspacing="0" border="0">
			<tr>
				<td colspan="3" height="110"></td>
			</tr>
			<tr>
				<td width="25" ></td>
				<td width="650" align="left">Ref : <? echo $sql_company_info[0][csf("company_short_name")].'/'.$sql_buyer_info[0][csf("short_name")]; if($sql_forwarder_info[0][csf("short_name")]!="") echo '/'.$sql_forwarder_info[0][csf("short_name")]; echo '/'.$sql_sub_info[0][csf("sub_id")].'/'.$sql_sub_info[0][csf("sub_year")]; ?> </td>
				<td width="25" ></td>
			</tr>
            <tr>
				<td width="25" ></td>
				<td width="650" align="left">Dated : <? echo change_date_format($result[0][csf("submit_date")]); ?> </td>
				<td width="25" ></td>
			</tr>
			<tr>
				<td colspan="3" height="20"></td>
			</tr>
			<tr>
				<td width="25" ></td>
				<td width="650" align="left">
				<?
				
					echo "To, <br>";
					echo $sql_forwarder_info[0][csf("short_name")]."<br>";
					$adress_all=explode(",",$sql_forwarder_info[0][csf("address_1")]);
					$i=0;
					$address_first="";
					foreach($adress_all as $adress)
					{
						if($address_first!="") $address_first.="<br>";
						$address_first.=$adress.", ";
						$i++;
					}
					$address_first=chop($address_first," , ");
					echo $address_first;
					//echo $bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["contact_person"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["bank_name"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["branch_name"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["address"];
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
				Subject: Submission of original shipping documents for <? echo $sql_buyer_info[0][csf("short_name")];?>.
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
				<td width="25" ></td>
				<td width="650" align="left">
				We are submitting following original shipping documents for  <? echo $sql_buyer_info[0][csf("short_name")];?>
				</td>
				<td width="25" ></td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			<tr>
				<td width="25" ></td>
				<td width="650" align="left">
					<table cellpadding="0" align="left" cellspacing="0" border="1" width="650">
						<thead>
							<tr>
								<th >SL</th>
                                <th width="150">Feeder Vasel Name</th>
								<th width="200">Order No</th>
								<th width="120">Country Name</th>
								<th width="150">Invoice No</th>
							</tr>
						</thead>
						<tbody>
						<?
						$i=1;
						foreach($result as $row)
						{
							$all_order_arr=explode(",",$row[csf("all_order_no")]);
							$all_po="";
							foreach($all_order_arr as $order_id)
							{
								$all_po.=$order_inv_array[$order_id]["po_no"].", ";
							}
							$all_po=chop($all_po," , ");
							?>
							<tr>
								<td valign="top" align="center"><? echo $i;?></td>
                                <td width="150" valign="top"><div style="width:150px; word-wrap:break-word;"><? echo $row[csf("feeder_vessel")];?> </div></td>
								<td width="200" valign="top"><div style="width:200px; word-wrap:break-word;"><? echo $all_po; ?></div></td>
								<td width="120" valign="top"><div style="width:120px; word-wrap:break-word;"><? echo $country_library[$row[csf("country_id")]];?></div></td>
								<td valign="top" width="150"><div style="width:150px; word-wrap:break-word;"></div><? echo $row[csf("invoice_no")]; ?></td>
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
				Please acknowledge Upon  receipt.
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
				Thanking You,
				</td>
				<td width="25" ></td>
			</tr>
		</table>
		<?
	}
	else if($data[1]==4)
	{
		$country_library=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );		
		if($db_type==0)
		{
			$sql_sub_info=sql_select("select a.id as sub_id, a.company_id, a.buyer_id, year(a.submit_date) as sub_year, max(c.forwarder_name) as forwarder_name  from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form=39 and a.id=$data[0] group by a.id, a.company_id, a.buyer_id");
		}
		else
		{
			$sql_sub_info=sql_select("select a.id as sub_id, a.company_id, a.buyer_id, to_char(a.submit_date,'yyyy') as sub_year, max(c.forwarder_name) as forwarder_name  from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form=39 and a.id=$data[0] group by a.id, a.company_id, a.buyer_id, to_char(a.submit_date,'yyyy')");
		}
		
		
		if($sql_sub_info[0][csf("forwarder_name")]>0)
		{
			$sql_forwarder_info=sql_select("select id, supplier_name, short_name, address_1 from lib_supplier where id='".$sql_sub_info[0][csf("forwarder_name")]."' ");
		}
		
		//echo $sql_forwarder_info[0][csf("short_name")];die;
		
		//echo "select id, buyer_name, short_name, contact_person, address_1 from lib_buyer where id='".$sql_sub_info[0][csf("buyer_id")]."' ";
		$sql_buyer_info=sql_select("select id, buyer_name, short_name, contact_person, address_1 from lib_buyer where id='".$sql_sub_info[0][csf("buyer_id")]."' ");
		$sql_company_info=sql_select("select id, company_name, company_short_name from  lib_company where id='".$sql_sub_info[0][csf("company_id")]."' ");
		
		//$sql_forwarder_info=sql_select("select id, company_name, company_short_name, contact_person from  com_export_invoice_ship_mst a, lib_buyer b  where id='".$sql_sub_info[0][csf("company_id")]."' ");
		
		$sql_sub_buyer="select a.id as sub_id, a.company_id, a.buyer_id, a.submit_date, b.is_lc, b.lc_sc_id, b.all_order_no, c.id as inv_id, c.invoice_no, c.invoice_date, c.invoice_quantity, b.net_invo_value as invoice_value, c.bl_no, c.bl_date, c.country_id, c.feeder_vessel, c.actual_shipment_date from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form=39 and a.id=$data[0]";
		//echo $sql_sub_buyer;//die;
		$result=sql_select($sql_sub_buyer);		 
		
		?>
		
		<table width="700" cellpadding="0" align="left" cellspacing="0" border="0">
			
			<tr>
				<td width="25" ></td>
				<td width="650" align="left">Ref : <? echo $sql_company_info[0][csf("company_short_name")].'/'.$sql_buyer_info[0][csf("short_name")]; if($sql_forwarder_info[0][csf("short_name")]!="") echo '/'.$sql_forwarder_info[0][csf("short_name")]; echo '/'.$sql_sub_info[0][csf("sub_id")].'/'.$sql_sub_info[0][csf("sub_year")]; ?> </td>
				<td width="25" ></td>
			</tr>
            <tr>
				<td width="25" ></td>
				<td width="650" align="left">Dated : <? echo change_date_format($result[0][csf("submit_date")]); ?> </td>
				<td width="25" ></td>
			</tr>
			<tr>
				<td colspan="3" height="20"></td>
			</tr>
			<tr>
				<td width="25" ></td>
				<td width="650" align="left">
				<?
				
					echo "To, <br>";
					echo $sql_forwarder_info[0][csf("short_name")]."<br>";
					$adress_all=explode(",",$sql_forwarder_info[0][csf("address_1")]);
					$i=0;
					$address_first="";
					foreach($adress_all as $adress)
					{
						if($address_first!="") $address_first.="<br>";
						$address_first.=$adress.", ";
						$i++;
					}
					$address_first=chop($address_first," , ");
					echo $address_first;
					//echo $bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["contact_person"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["bank_name"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["branch_name"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["address"];
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
				Subject: Submission of original shipping documents for <? echo $sql_buyer_info[0][csf("short_name")];?>.
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
				<td width="25" ></td>
				<td width="650" align="left">
				We are submitting following original shipping documents for  <? echo $sql_buyer_info[0][csf("short_name")];?>
				</td>
				<td width="25" ></td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			<tr>
				<td width="25" ></td>
				<td width="650" align="left">
					<table cellpadding="0" align="left" cellspacing="0" border="1" width="650">
						<thead>
							<tr>
								<th >SL</th>
                                <th width="150">Actual Sailing Date</th>
								<th width="200">Order No</th>
								<th width="120">Country Name</th>
								<th width="150">Invoice No</th>
							</tr>
						</thead>
						<tbody>
						<?
						$i=1;
						foreach($result as $row)
						{
							$all_order_arr=explode(",",$row[csf("all_order_no")]);
							$all_po="";
							foreach($all_order_arr as $order_id)
							{
								$all_po.=$order_inv_array[$order_id]["po_no"].", ";
							}
							$all_po=chop($all_po," , ");
							?>
							<tr>
								<td valign="top" align="center"><? echo $i;?></td>
                                <td width="150" valign="top"><div style="width:150px; word-wrap:break-word;"><? echo $row[csf("actual_shipment_date")];?> </div></td>
								<td width="200" valign="top"><div style="width:200px; word-wrap:break-word;"><? echo $all_po; ?></div></td>
								<td width="120" valign="top"><div style="width:120px; word-wrap:break-word;"><? echo $country_library[$row[csf("country_id")]];?></div></td>
								<td valign="top" width="150"><div style="width:150px; word-wrap:break-word;"></div><? echo $row[csf("invoice_no")]; ?></td>
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
				Please acknowledge Upon  receipt.
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
				Thanking You,
				</td>
				<td width="25" ></td>
			</tr>
		</table>
		<?
	}
	else if($data[1]==5)
	{
		$country_library=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );		
		$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );		
		
		$sql_sub_info=sql_select("SELECT a.id as sub_id, a.company_id, a.buyer_id, max(c.forwarder_name) as forwarder_name  
		from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c, com_export_invoice_ship_dtls d 
		where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and c.id=d.mst_id and a.entry_form=39 and a.id=$data[0] 
		group by a.id, a.company_id, a.buyer_id");
		
		if($sql_sub_info[0][csf("forwarder_name")]>0)
		{
			$sql_forwarder_info=sql_select("select id, supplier_name, contact_person, address_1 from lib_supplier where id='".$sql_sub_info[0][csf("forwarder_name")]."' ");
		}

		$sql_lib_location=sql_select("SELECT ID, LOCATION_NAME,CONTACT_NO,ADDRESS,EMAIL,COUNTRY_ID,REMARK from  lib_location where company_id='".$sql_sub_info[0][csf("company_id")]."' and status_active=1 ");
		
		$ac_po_info=sql_select("SELECT d.PO_BREAKDOWN_ID, d.ACTUAL_PO_INFOS 
		from com_export_doc_submission_invo b, com_export_invoice_ship_mst c, com_export_invoice_ship_dtls d 
		where b.invoice_id=c.id and c.id=d.mst_id and b.doc_submission_mst_id=$data[0]");
		$ac_po_data=array();
		foreach($ac_po_info as $row)
		{
			$ac_pos_arr=explode("**",$row["ACTUAL_PO_INFOS"]);
			foreach($ac_pos_arr as $value)
			{
				$val=explode("=",$value);
				if($row["PO_BREAKDOWN_ID"]>0 && $val[2]!="")
				{
					$ac_po_data[$row["PO_BREAKDOWN_ID"]].=$val[2].",";
				}
			}
		}
		//print_r($ac_po_data);
		$sql_sub_buyer="SELECT a.id as sub_id, a.company_id, a.buyer_id, a.submit_date, b.is_lc, b.lc_sc_id, b.all_order_no, c.id as inv_id, c.invoice_no, c.invoice_date, c.invoice_quantity, b.net_invo_value as invoice_value, c.bl_no, c.bl_date, c.country_id, c.feeder_vessel, c.actual_shipment_date,c.total_carton_qnty 
		from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c 
		where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form=39 and a.id=$data[0]";
		//echo $sql_sub_buyer;//die;
		$result=sql_select($sql_sub_buyer);		 
		?>
		<style>
			.wrd_brk{word-break: break-all;}
			.left{text-align: left;}
			.center{text-align: center;}
			.right{text-align: right;}
		</style>

		<table width="800" cellpadding="0" align="left" cellspacing="0" border="0">
			<tr>
				<td width="25" ></td>
				<td width="750" align="center" style="font-size:26px;"><b><? echo $company_library[$data[2]]; ?> </b></td>
				<td width="25" ></td>
			</tr>
            <tr>
				<td width="25" ></td>
				<td width="750" align="center"><? echo $sql_lib_location[0]['LOCATION_NAME']; ?> </td>
				<td width="25" ></td>
			</tr>
            <tr>
				<td width="25" ></td>
				<td width="750" class="center">
					<? echo $country_library[$sql_lib_location[0]['COUNTRY_ID']].', Phone: '.$sql_lib_location[0]['CONTACT_NO'].', Fax# '.$sql_lib_location[0]['REMARK'].', E-mail: '.$sql_lib_location[0]['EMAIL']; ?> 
				</td>
				<td width="25" ></td>
			</tr>
			<tr>
				<td colspan="3" height="50"></td>
			</tr>
			<tr>
				<td width="25" ></td>
				<td width="750" class="left">
					<?
						echo "<b>To: ".$sql_forwarder_info[0][csf("contact_person")]."<br>";
						echo $sql_forwarder_info[0][csf("supplier_name")]."<br>";
						echo 'Address: '.$sql_forwarder_info[0][csf("address_1")]."</b><br>";
					?>
				</td>
				<td width="25" ></td>
			</tr>
			<tr>
				<td width="25" ></td>
				<td width="750" ><b>From: <?echo $sql_lib_location[0][csf('address')];?></b>  </td>
				<td width="25" ></td>
			</tr>
			<tr>
				<td colspan="3" height="30"></td>
			</tr>
			<tr>
				<td width="25" ></td>
				<td width="750" ><b>Particulars as bellow:</b> </td>
				<td width="25" ></td>
			</tr>
			<tr>
				<td colspan="3" height="10"></td>
			</tr>
			<tr>
				<td width="25" ></td>
				<td width="750" align="left">
					<table cellpadding="0" align="left" cellspacing="0" border="1" width="750">
						<thead>
							<tr>
								<th width="30">Sl No.</th>
                                <th width="80">Invoice No</th>
								<th width="80">STYLE</th>
								<th width="80">PO No</th>
								<th width="60">CTN</th>
								<th width="60">PCS</th>
								<th width="80">Value $</th>
								<th width="70">Booking No</th>
								<th width="80">ETD</th>
								<th >Doc's Sub Date</th>
							</tr>
						</thead>
						<tbody>
						<?
						$i=1;
						foreach($result as $row)
						{
							$all_order_arr=explode(",",$row[csf("all_order_no")]);
							$all_po=$all_style=$all_etd_date="";
							foreach($all_order_arr as $order_id)
							{
								if(chop($ac_po_data[$order_id],",")!="")
								{
									$all_po.=chop($ac_po_data[$order_id],",").", ";
								}
								else
								{
									$all_po.=$order_inv_array[$order_id]["po_no"].", ";
								}
								
								$all_style.=$order_inv_array[$order_id]["style_ref_no"].",";
							}
							$all_po=implode(", ",array_unique(explode(",",chop($all_po,", "))));
							$all_style=implode(", ",array_unique(explode(",",chop($all_style,','))));
							?>
							<tr>
								<td class="center"><? echo $i;?></td>
								<td class="wrd_brk"><? echo $row[csf("invoice_no")]; ?></td>
								<td class="wrd_brk"><? echo $all_style; ?></td>
								<td class="wrd_brk"><? echo $all_po; ?></td>
                                <td class="wrd_brk right"><? echo $row[csf("total_carton_qnty")];?></td>
                                <td class="wrd_brk right"><? echo $row[csf("invoice_quantity")];?> </td>
                                <td class="wrd_brk right"><? echo $row[csf("invoice_value")];$total_value+=$row[csf("invoice_value")];?> </td>
                                <td class="wrd_brk"><? echo $row[csf("bl_no")];?> </td>
                                <td class="wrd_brk center"><? echo change_date_format($row[csf("bl_date")]);?> </td>
                                <td class="wrd_brk center"><? echo change_date_format($row[csf("submit_date")]);?> </td>
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
								<th></th>
								<th>Total</th>
								<th></th>
								<th></th>
								<th class="wrd_brk right"><?echo number_format($total_value,2);?></th>
								<th></th>
								<th></th>
								<th></th>
							</tr>
						</tfoot>
					</table>
				</td>
				<td width="25" ></td>
			</tr>
			<tr>
				<td colspan="3" height="100"></td>
			</tr>
			<tr>
				<td width="25" ></td>
				<td width="750" align="left">
					<div style="display: flex;justify-content: space-around;">
						<div style="width: 450px;">Signature</div>
						<div >Company seal</div> 
					</div>
				</td>
				<td width="25" ></td>
			</tr>
		</table>
		<?
	}
	else
	{//echo $data[2]; die;
		
		$country_library=return_library_array( "select id, short_name from lib_country", "id", "short_name"  );
		$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
		$weak_of_year=return_library_array( "select week_date,week from  week_of_year",'week_date','week');
		//print_r($weak_of_year);die;		
		if($db_type==0)
		{
			$sql_sub_info=sql_select("select a.id as sub_id, a.company_id, a.buyer_id, year(a.submit_date) as sub_year, max(c.forwarder_name) as forwarder_name  from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form=39 and a.id=$data[0] group by a.id, a.company_id, a.buyer_id");
		}
		else
		{
			$sql_sub_info=sql_select("select a.id as sub_id, a.company_id, a.buyer_id, to_char(a.submit_date,'yyyy') as sub_year, max(c.forwarder_name) as forwarder_name  from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form=39 and a.id=$data[0] group by a.id, a.company_id, a.buyer_id, to_char(a.submit_date,'yyyy')");
		}
		
		
		if($sql_sub_info[0][csf("forwarder_name")]>0)
		{
			$sql_forwarder_info=sql_select("select id, supplier_name, short_name, address_1 from lib_supplier where id='".$sql_sub_info[0][csf("forwarder_name")]."' ");
		}
		$sql_buyer_info=sql_select("select id, buyer_name, short_name, contact_person, address_1 from lib_buyer where id='".$sql_sub_info[0][csf("buyer_id")]."' ");
		$sql_company_info=sql_select("select id, company_name, company_short_name from  lib_company where id='".$sql_sub_info[0][csf("company_id")]."' ");
		
		$sql_sub_buyer="select a.id as sub_id, a.company_id, a.buyer_id, a.submit_date, b.is_lc, b.lc_sc_id, b.all_order_no, c.id as inv_id, c.invoice_no, c.invoice_date, c.invoice_quantity, b.net_invo_value as invoice_value, c.bl_no, c.bl_date, c.country_id, c.feeder_vessel from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form=39 and a.id=$data[0]";
		//echo $sql_sub_buyer; 
		$result=sql_select($sql_sub_buyer);
		$order_info_array=array(); $po_ids="";
		foreach($result as $row)
		{
			$po_ids.=$row[csf("all_order_no")].",";
		}
		$po_ids = implode(",",array_filter(array_unique(explode(",",$po_ids))));
		//echo "select country_ship_date, cutup, po_break_down_id, country_id from  wo_po_color_size_breakdown where po_break_down_id in ($po_ids) and status_active=1 and is_deleted=0";// die;
		$sql_order_info=sql_select("select country_ship_date, cutup, po_break_down_id, country_id from  wo_po_color_size_breakdown where po_break_down_id in ($po_ids) and status_active=1 and is_deleted=0");
		foreach($sql_order_info as $row)
		{
			$order_info_array[$row[csf("po_break_down_id")]][$row[csf("country_id")]]["country_ship_date"].=$row[csf("country_ship_date")].",";
			$order_info_array[$row[csf("po_break_down_id")]][$row[csf("country_id")]]["cutup"].=$row[csf("cutup")].",";
			$order_info_array[$row[csf("po_break_down_id")]][$row[csf("country_id")]]["country_id"].=$row[csf("country_id")].",";		
		}
		//print_r($order_info_array);
		if($result[0][csf("submit_date")]!="" && $result[0][csf("submit_date")]!='0000-00-00') $submit_date=change_date_format($result[0][csf("submit_date")]); else $submit_date="&nbsp;";
		?>
		<br>
	    <table width="700" cellpadding="0" align="center" cellspacing="0" border="0">
	        <tr class="form_caption">
            	<?
            	
                $data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[2]' and form_name='company_details' and is_deleted=0 and file_type=1");
                ?>
                <td  align="left" width="200">
                <?
                foreach($data_array as $img_row)
                {
					?>
					<img src='../../<? echo $img_row[csf('image_location')]; ?>' height='70' width='200' align="middle" />
					<?
				}
                ?>
                </td>

            	<td  colspan="" align="right" style="font-size:28px; margin-bottom:50px; color:blue;"><strong><? echo $company_library[$data[2]]; ?></strong></td>
            </tr>
	        <tr class="">
	        	<td align="center" style="font-size:14px"> </td>
	        	<td colspan="2" align="center" style="font-size:14px"></td>  
	        </tr>
        </table>
        <br>
		<table width="700" cellpadding="0" align="center" cellspacing="0" border="0">
			<tr>
	            <td width="25" ></td>
	            <td width="650" align="left">Date : <strong><? echo $submit_date; ?></strong></td>
	            <td width="25"></td>
			</tr>
			<tr>
				<td colspan="3" height="20"></td>
			</tr>
			<tr>
	            <td width="25" ></td>
	            <td width="650" align="left"> <? echo "To, <br>";
					echo $sql_forwarder_info[0][csf("supplier_name")]."<br>";
					$adress_all=explode(",",$sql_forwarder_info[0][csf("address_1")]);
					//print_r($adress_all);
					$i=0;
					$address_first="";
					foreach($adress_all as $adress)
					{
						if($address_first!="") $address_first.="<br>";
						$address_first.=$adress.", ";
						$i++;
					}
					$address_first=chop($address_first," , ");
					echo $address_first;?> 
	            <td width="25" ></td>
	        </tr>
			<tr>
				<td colspan="3" height="20"></td>
			</tr>
			<tr>
				<td colspan="3" height="30"></td>
			</tr>
			<tr>
				<td width="25" ></td>
				<td width="650" align="left">
				We are submitting following original shipping documents of <? echo $sql_buyer_info[0][csf("short_name")];?>
				</td>
				<td width="25" ></td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			<tr>
				
				<td colspan="3" align="left">
					<table cellpadding="0" align="left" cellspacing="0" border="1" width="695">
						<thead>
							<tr>
								<!-- PACK	INVOICE NO.	Order No/P.O	VESSEL NAME	COUNTRY	WEEK	CUT OFF -->
                                <th width="60">PACK</th>
								<th width="120">INVOICE NO.</th>
								<th width="120">Order No/P.O</th>
								<th width="120">VESSEL NAME</th>
								<th width="80">COUNTRY</th>
								<th width="70">WEEK</th>
								<th width="60">CUT OFF</th>
							</tr>
						</thead>
						<tbody>
						<?
						$i=1;
						foreach($result as $row)
						{
							$all_order_arr=explode(",",$row[csf("all_order_no")]);
							//$test_data2=""; 
							$all_po=$all_ship_date=$all_cutoff=$all_country="";
							foreach($all_order_arr as $order_id)
							{
								$all_po.=$order_inv_array[$order_id]["po_no"].",";
								$country_ship_date=array_unique(explode(",",chop($order_info_array[$order_id][$row[csf("country_id")]]["country_ship_date"],",")));
								foreach ($country_ship_date as  $value) {
									$all_ship_date.=$weak_of_year[$value].",";
								}
								
								$cutup_date=array_unique(explode(",",chop($order_info_array[$order_id][$row[csf("country_id")]]["cutup"],",")));
								//$test_data.=implode(",",array_unique(explode(",",chop($order_info_array[$order_id]["cutup"],","))))."__";
								foreach ($cutup_date as  $value) {
									$cut_up_data=explode(" ",$cut_up_array[$value]);
									$all_cutoff.=$cut_up_data[0].",";
								}
								//echo $order_info_array[$order_id]["country_id"].jahid;
								$all_country_data=array_unique(explode(",",chop($order_info_array[$order_id][$row[csf("country_id")]]["country_id"],",")));
								//print_r($all_country_data);die;
								foreach ($all_country_data as  $value) {
									$all_country.=$country_library[$value].",";
									//$test_data2.=$value.",";
								}
							}
							
							//$test_data=chop($test_data,"__");
							//$test_data2=chop($test_data2,",");
							//echo $test_data2."<br>";//die;
							$all_po=chop($all_po,",");
							$all_ship_date=chop($all_ship_date,",");
							$all_cutoff=chop($all_cutoff,",");
							$all_country=chop($all_country,",");
							?>
							<tr>
								<td width="60"><div style="width:60px; word-wrap:break-word;"><? echo $i;?> </div></td>
								<td width="120"><div style="width:120px; word-wrap:break-word;"><? echo $row[csf("invoice_no")];?> </div></td>
								<td width="120"><div style="width:120px; word-wrap:break-word;"><? echo $all_po;?> </div></td>
								<td width="120"><div style="width:120px; word-wrap:break-word;"><? echo $row[csf("feeder_vessel")];?> </div></td>
								<td width="80" title="<? echo $row[csf("all_order_no")]; ?>"><div style="width:80px; word-wrap:break-word;"><? echo $all_country; ?> </div></td>
								<td width="70" title="<? echo $order_info_array[$order_id][$row[csf("country_id")]]["country_ship_date"]; ?>"><div style="width:70px; word-wrap:break-word;"><? echo $all_ship_date;?> </div></td>
								<td width="60"><div style="width:60px; word-wrap:break-word;"><? if($all_cutoff!=0 && $all_cutoff!='') echo $all_cutoff;?> </div></td>
							</tr>
							<?
							$i++;
						}
						?>
						</tbody>
					</table>
				</td>
			</tr>			
		</table>
		<table width="695" cellpadding="0" align="center" cellspacing="0" border="0" style="margin-top:15px; alignment-baseline: baseline; " id="foot" >
	    	<tfoot>
	    	<tr>
	    		<td colspan="2"><hr></td>
	    	</tr>
	    	<? $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[2]"); 
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
	
	exit();

}



?>


 