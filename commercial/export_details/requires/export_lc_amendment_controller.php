<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
 
include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

//--------------------------- Start-------------------------------------//
if ($action=="load_drop_down_buyer_search")
{
	echo create_drop_down( "cbo_buyer_name", 165, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  

	exit();
}
//already used 
if($action=='populate_data_from_export_lc')
{
	$data_array=sql_select("select id, export_lc_system_id, export_lc_no, lc_date, beneficiary_name, buyer_name, replacement_lc, lien_bank, lien_date, lc_value, currency_name, last_shipment_date, expiry_date, shipping_mode, tolerance, pay_term, inco_term, inco_term_place, port_of_entry, port_of_loading, port_of_discharge, internal_file_no, remarks, tenor, discount_clauses, claim_adjustment from com_export_lc where id='$data'");
	foreach ($data_array as $row)
	{
		if($db_type==0)
		{
			$attached_po_id=return_field_value("group_concat(wo_po_break_down_id)","com_export_lc_order_info","com_export_lc_id=$data and status_active=1 and is_deleted=0");
		}
		else
		{
			/*$attached_po_id=return_field_value("rtrim(xmlagg(xmlelement(e,wo_po_break_down_id,',').extract('//text()') order by wo_po_break_down_id).GetClobVal(),',') as po_id","com_export_lc_order_info","com_export_lc_id=$data and status_active=1 and is_deleted=0","po_id");	
			$attached_po_id = $attached_po_id->load();*/
			$attached_po_id=return_field_value("listagg(cast(wo_po_break_down_id as varchar(4000)),',') within group (order by wo_po_break_down_id) as po_id","com_export_lc_order_info","com_export_lc_id=$data and status_active=1 and is_deleted=0","po_id");
		}
		
 		echo "document.getElementById('txt_system_id').value 			= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_internal_file_no').value		= '".$row[csf("internal_file_no")]."';\n";
		echo "document.getElementById('txt_export_lc_no').value 		= '".$row[csf("export_lc_no")]."';\n";
		echo "document.getElementById('cbo_beneficiary_name').value 	= '".$row[csf("beneficiary_name")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value			= '".$row[csf("buyer_name")]."';\n";
		echo "document.getElementById('txt_lc_value').value 			= '".$row[csf("lc_value")]."';\n";
		echo "document.getElementById('cbo_currency_name').value 		= '".$row[csf("currency_name")]."';\n";
		echo "document.getElementById('cbo_replacement_lc').value 		= '".$row[csf("replacement_lc")]."';\n";		
		echo "document.getElementById('cbo_lien_bank').value 			= '".$row[csf("lien_bank")]."';\n";
		echo "document.getElementById('txt_lien_date').value 			= '".change_date_format($row[csf("lien_date")])."';\n";
		echo "document.getElementById('txt_last_shipment_date').value 	= '".change_date_format($row[csf("last_shipment_date")])."';\n";
		echo "document.getElementById('txt_expiry_date').value 			= '".change_date_format($row[csf("expiry_date")])."';\n";
		echo "document.getElementById('txt_tolerance').value 			= '".$row[csf("tolerance")]."';\n";
		echo "document.getElementById('cbo_shipping_mode').value 		= '".$row[csf("shipping_mode")]."';\n";
		echo "document.getElementById('cbo_pay_term').value 			= '".$row[csf("pay_term")]."';\n";
		echo "document.getElementById('txt_tenor').value 				= '".$row[csf("tenor")]."';\n";
		echo "document.getElementById('txt_port_of_entry').value 		= '".$row[csf("port_of_entry")]."';\n";
		echo "document.getElementById('txt_port_of_loading').value 		= '".$row[csf("port_of_loading")]."';\n";
		echo "document.getElementById('txt_port_of_discharge').value 	= '".$row[csf("port_of_discharge")]."';\n";
		echo "document.getElementById('txt_discount_clauses').value 	= '".$row[csf("discount_clauses")]."';\n";
		echo "document.getElementById('txt_claim_adjustment').value 	= '".$row[csf("claim_adjustment")]."';\n";
		echo "document.getElementById('txt_remarks').value 				= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('hidden_selectedID').value 		= '".$attached_po_id."';\n";
		
		echo "document.getElementById('txt_amendment_no').value 			= '';\n";
		echo "document.getElementById('update_id').value 					= '';\n";
		echo "document.getElementById('txt_amendment_date').value 			= '';\n";
		echo "document.getElementById('txt_amendment_value').value 			= '';\n";
		echo "document.getElementById('hide_amendment_value').value 		= '';\n";
		echo "document.getElementById('cbo_value_change_by').value 			= '0';\n";
		echo "document.getElementById('hide_value_change_by').value 		= '';\n";
		echo "document.getElementById('txt_last_shipment_date_amnd').value	= '".change_date_format($row[csf("last_shipment_date")])."';\n";
		echo "document.getElementById('txt_expiry_date_amend').value 		= '".change_date_format($row[csf("expiry_date")])."';\n";
		echo "document.getElementById('cbo_shipping_mode_amnd').value 		= '".$row[csf("shipping_mode")]."';\n";
		echo "document.getElementById('cbo_inco_term').value 				= '".$row[csf("inco_term")]."';\n";  
		echo "document.getElementById('txt_inco_term_place').value 			= '".$row[csf("inco_term_place")]."';\n";
		echo "document.getElementById('txt_port_of_entry_amnd').value 		= '".$row[csf("port_of_entry")]."';\n";  
		echo "document.getElementById('txt_port_of_loading_amnd').value 	= '".$row[csf("port_of_loading")]."';\n";
		echo "document.getElementById('txt_port_of_discharge_amnd').value 	= '".$row[csf("port_of_discharge")]."';\n";
		echo "document.getElementById('cbo_pay_term_amnd').value 			= '".$row[csf("pay_term")]."';\n";
		echo "document.getElementById('txt_tenor_amnd').value 				= '".$row[csf("tenor")]."';\n";
		echo "document.getElementById('txt_claim_adjustment_amnd').value 	= '';\n";
		echo "document.getElementById('cbo_claim_adjust_by').value 			= '0';\n";
		echo "document.getElementById('hide_claim_adjust_by').value 		= '';\n";
		echo "document.getElementById('txt_discount_clauses_amnd').value 	= '".$row[csf("discount_clauses")]."';\n";
		echo "document.getElementById('txt_remarks_amnd').value 			= '".$row[csf("remarks")]."';\n";
		
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_amendment_save',1);\n";
 		
		exit();
	}
}

//alredy used
if($action=="export_lc_search")
{
	 echo load_html_head_contents("Export LC Form", "../../../", 1, 1,'','1','');
	?>
	<script>
		function js_set_value(id)
		{
			$('#hidden_export_lc_id').val(id);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:1020px;">
	<form name="searchscfrm"  id="searchscfrm">
		<fieldset style="width:100%; margin-left:5px">
            <legend>Enter search words</legend>           
            	<table cellpadding="0" cellspacing="0" width="80%" class="rpt_table">
                	<thead>
                    	<th>Company</th>
                        <th>Buyer</th>
                        <th>Search By</th>
                        <th>Enter</th>
                        <th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" /><input type="hidden" name="id_field" id="id_field" value="" /></th>
                    </thead>
                    <tr class="general">
                        <td>
                            <?
							   	echo create_drop_down( "cbo_company_name", 165, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--- Select ---", 0, "load_drop_down( 'export_lc_amendment_controller', this.value, 'load_drop_down_buyer_search', 'buyer_td_id' );" );
							?>  
                          </td>
                          <td id="buyer_td_id">
                            <?
							   	echo create_drop_down( "cbo_buyer_name", 165, $blank_array,"", 1, "--- Select ---", $selected, "" );
							?>
                         </td>                  
						<td> 
                        	<?
							   	$arr=array(1=>'LC No',2=>'File No');
								echo create_drop_down( "cbo_search_by", 165, $arr,"", 0, "--- Select ---", 0, "" );
							?>
                        </td>						
						<td id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                            <input type="hidden" id="hidden_export_lc_id" />
            			</td>                       
                         <td>
                 		  	<input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'create_lc_search_list_view', 'search_div', 'export_lc_amendment_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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

if($action=="create_lc_search_list_view")
{
	$data=explode('**',$data); 
	if($data[0]!=0){ $company_id=" and beneficiary_name = $data[0]";}else{ $company_id=""; }
	if($data[1]!=0){ $buyer_id=" and buyer_name = $data[1]";}else{ $buyer_id=""; }
	$search_by=$data[2];
	$search_text=$data[3];
	
	if($search_by==0)
	{
		$search_condition="";
	}
	else if($search_by==1)
	{
		$search_condition="and export_lc_no like '$search_text%'";
	}
	else if($search_by==2)
	{
		$search_condition="and internal_file_no like '$search_text%'";
	}
	
	if($db_type==0) $year_field="YEAR(insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	
	$sql = "select id,export_lc_no,internal_file_no, $year_field export_lc_prefix_number, export_lc_system_id, beneficiary_name, buyer_name, applicant_name, lc_value, lien_bank, pay_term, last_shipment_date, lc_date from com_export_lc where status_active=1 and is_deleted=0 $company_id $buyer_id $search_condition order by id";
	
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	$arr=array (4=>$comp,5=>$buyer_arr,6=>$buyer_arr,8=>$bank_arr,9=>$pay_term);
	echo create_list_view("list_view", "Export LC No,File No,Year,System ID,Company,Buyer Name,Applicant Name,LC Value,Lien Bank,Pay Term,Ship Date,LC Date", "80,80,50,65,70,70,70,100,110,70,80,70","1020","315",0, $sql, "js_set_value", "id", "", 1, "0,0,0,0,beneficiary_name,buyer_name,applicant_name,0,lien_bank,pay_term,0,0", $arr , "export_lc_no,internal_file_no,year,export_lc_prefix_number,beneficiary_name,buyer_name,applicant_name,lc_value,lien_bank,pay_term,last_shipment_date,lc_date", "",'','0,0,0,0,0,0,0,2,0,0,3,3') ;
	exit();
}

//already used
if ($action=="order_popup")
{
	echo load_html_head_contents("Export LC Form", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
	?>
     
	<script>
	 var selected_id = new Array, selected_name = new Array();	
	 function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			
			$('#txt_selected_id').val( id );
		}
	
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
	<form name="searchpofrm"  id="searchpofrm">
		<fieldset style="width:830px">
			<table width="650" cellspacing="0" cellpadding="0" class="rpt_table">
                <thead>
                    <th>Company</th>
                    <th>Search By</th>
                    <th>Search</th>
                    <th>File No</th>
                    <th>SC/LC</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;"></th>                    
                </thead>
                <tr class="general">
                    <td align="center">
                        <? 
							echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select Company --", $company_id,"" );
						?>
                    </td>
                    <td align="center">
                        <?
                        	$arr=array(1=>'PO Number',2=>'Job No',3=>'Style Ref No', 4 => 'Internal Ref.');
							echo create_drop_down( "cbo_search_by", 150, $arr,"",0, "--- Select ---", '',"" );
						?>
                     </td>
                     <td align="center">
                        <input type="text" name="txt_search_text" id="txt_search_text" class="text_boxes" style="width:150px" />
                        <input type="hidden" id="hidden_type" value="<? echo $types; ?>" />	
                        <input type="hidden" id="hidden_buyer_id" value="<? echo $buyer_id; ?>" />	
                        <input type="hidden" id="hidden_po_selectedID" value="<? echo $selectID; ?>" />
                        <input type="hidden" id="export_lcID" value="<? echo $export_lcID; ?>" />
                        <input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />				
                    </td>
                    <td><input type="text" id="txt_file_no" name="txt_file_no" class="text_boxes" value=""></td>
                    <td><input type="text" id="txt_sc_lc" name="txt_sc_lc" class="text_boxes" value=""></td>
                    <td align="center">
                        <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_text').value+'**'+document.getElementById('hidden_type').value+'**'+document.getElementById('hidden_buyer_id').value+'**'+document.getElementById('hidden_po_selectedID').value+'**'+document.getElementById('export_lcID').value+'**'+document.getElementById('txt_file_no').value+'**'+document.getElementById('txt_sc_lc').value, 'create_po_search_list_view', 'search_div', 'export_lc_amendment_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" />
                    </td>
            </tr>
        </table>
        <div style="margin-top:5px" id="search_div" align="left"></div>
	</fieldset>
	</form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?

  exit();
}

if($action=="create_po_search_list_view")
{
	$data=explode('**',$data);

	if($_SESSION['logic_erp']['buyer_id']!=''){$user_buyer=" and wm.buyer_name in (".$_SESSION['logic_erp']['buyer_id'].")";}
	if($_SESSION['logic_erp']['brand_id']!=''){$user_brand=" and wm.brand_id in (".$_SESSION['logic_erp']['brand_id'].")";}


	if ($data[0]!=0) $company=" and wm.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[2]!='') 
	{
		if($data[1]==1)
			$search_text=" and wb.po_number like '".trim($data[2])."%'";
		else if($data[1]==2)	 
			$search_text=" and wm.job_no like '".trim($data[2])."%'"; 
		else if($data[1]==3)
			$search_text=" and wm.style_ref_no like '".trim($data[2])."%'";	
		else if ($data[1] == 4)
            $search_text = " and wb.grouping like '" . trim($data[2]) . "%'";
	}
	$action_types = $data[3];
	$buyer_id = $data[4];	
	if($data[5]=="") $selected_order_id = ""; else $selected_order_id = "and wb.id not in (".$data[5].")";
	$sales_contractID = $data[6];
        
        $txt_file_no = $data[7];
        //$txt_sc_lc = $data[8];
        if ($txt_file_no == "")
        $file_no_cond = "";
        else
        $file_no_cond = "and wb.file_no = '" . $data[7] . "'";
        
        if (trim($data[8])!='') {
             $txt_sc_lc_cond = "and wb.sc_lc like '%" . trim($data[8]) . "%'";
        }else{
            $txt_sc_lc_cond = "";
        }
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand brand",'id','brand_name');
	if($action_types=='attached_po_status')
	{
		$lc_details=return_library_array( "select id, export_lc_no from com_export_lc",'id','export_lc_no');
		$sc_details=return_library_array( "select id, contract_no from com_sales_contract",'id','contract_no');

		$lc_array=array(); $sc_array=array(); $attach_qnty_array=array();
		$sql_lc_sc="select com_export_lc_id as id, wo_po_break_down_id, sum(attached_qnty) as qnty, 1 as type from com_export_lc_order_info where status_active=1 and is_deleted=0 group by wo_po_break_down_id, com_export_lc_id
		union all
		select com_sales_contract_id as id, wo_po_break_down_id, sum(attached_qnty) as qnty, 2 as type from com_sales_contract_order_info where status_active=1 and is_deleted=0 group by wo_po_break_down_id, com_sales_contract_id
		";
		$lc_sc_Array=sql_select($sql_lc_sc);
		foreach($lc_sc_Array as $row_lc_sc)
		{
			if(array_key_exists($row_lc_sc[csf('wo_po_break_down_id')],$attach_qnty_array))
			{
				 $attach_qnty_array[$row_lc_sc[csf('wo_po_break_down_id')]]+=$row_lc_sc[csf('qnty')];
			}
			else
			{
				$attach_qnty_array[$row_lc_sc[csf('wo_po_break_down_id')]]=$row_lc_sc[csf('qnty')];
			}
			
			if($row_lc_sc[csf('type')]==1)
			{
				if($row_lc_sc[csf('qnty')]>0)
				{
					if(array_key_exists($row_lc_sc[csf('wo_po_break_down_id')],$lc_array))
					{
						 $lc_array[$row_lc_sc[csf('wo_po_break_down_id')]].=",".$row_lc_sc[csf('id')];
					}
					else
					{
						$lc_array[$row_lc_sc[csf('wo_po_break_down_id')]]=$row_lc_sc[csf('id')];
					}
				}
			}
			else
			{
				if($row_lc_sc[csf('qnty')]>0)
				{
					if(array_key_exists($row_lc_sc[csf('wo_po_break_down_id')],$sc_array))
					{
						 $sc_array[$row_lc_sc[csf('wo_po_break_down_id')]].=",".$row_lc_sc[csf('id')];
					}
					else
					{
						$sc_array[$row_lc_sc[csf('wo_po_break_down_id')]]=$row_lc_sc[csf('id')];
					}
				}
			}
		}
		
		$sql = "SELECT wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst as job_no, wm.style_ref_no, wm.gmts_item_id, wb.unit_price as rate,wb.file_no,wb.sc_lc, wm.brand_id, wm.job_no_prefix_num as job_no_prefix_num 
		FROM wo_po_break_down wb, wo_po_details_master wm 
		WHERE wb.job_id = wm.id and wm.buyer_name like '$buyer_id' $company $search_text $file_no_cond $txt_sc_lc_cond $user_buyer $user_brand and wb.is_deleted = 0 AND wb.status_active = 1 
		group by wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date, wb.job_no_mst, wm.style_ref_no, wm.gmts_item_id, wb.unit_price,wb.file_no,wb.sc_lc,wm.brand_id,wm.job_no_prefix_num "; 

		?>
        <div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1260" class="rpt_table" >
                <thead>
                    <th width="30">SL</th>
                    <th width="110">PO No</th>
                    <th width="110">Item</th>
					<th width="110">Job No</th>
                    <th width="110">Style No</th>
					<th width="90">Brand</th>
                    <th width="90">PO Quantity</th>
					<th width="90">Rate</th>
                    <th width="100">Price</th>
                    <th width="80">Shipment Date</th>
                    <th width="100">Attached With</th>
                    <th>LC/SC</th>
                    <th width="80">File No</th>
                    <th width="80">SC/LC No</th>
                </thead>
            </table>
            <div style="width:1280px; overflow-y:scroll; max-height:250px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1260" class="rpt_table" id="tbl_list_search" >
                <? 
					$i=1;
                    $nameArray=sql_select( $sql );
                    foreach ($nameArray as $selectResult)
                    {
						if(array_key_exists($selectResult[csf('id')],$attach_qnty_array))
						{
							$order_attached_qnty=$attach_qnty_array[$selectResult[csf('id')]];
							
							if($order_attached_qnty>=$selectResult[csf('po_quantity')])
							{
								$all_lc_id=explode(",",$lc_array[$selectResult[csf('id')]]);
								foreach($all_lc_id as $lc_id)
								{
									if($lc_id!=0)
									{
										if ($i%2==0)  
											$bgcolor="#E9F3FF";
										else
											$bgcolor="#FFFFFF";	
									?>	
										<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="30"><? echo $i; ?></td>
											<td width="110"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
											<td width="110">
												<p>
													<?
														$gmts_item='';
														$gmts_item_id=explode(",",$selectResult[csf('gmts_item_id')]);
														foreach($gmts_item_id as $item_id)
														{
															if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
														}
														echo $gmts_item;
													?>
												</p>
											</td>
											<td width="110"><p><? echo $selectResult[csf('job_no_prefix_num')]; ?></p></td>
											<td width="110"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
											<td width="90"><p><? echo $brand_arr[$selectResult[csf('brand_id')]]; ?></p></td>
											<td width="90" align="right"><? echo $selectResult[csf('po_quantity')]; ?></td>
											<td width="90" align="right"><p><? echo $selectResult[csf('rate')]; ?></p></td>
											<td width="100" align="right"><? echo number_format($selectResult[csf('po_total_price')],2); ?></td>
											<td align="center" width="80"><? echo change_date_format($selectResult[csf('shipment_date')]); ?></td>
											<td width="100"><p><? echo $lc_details[$lc_id]; ?></p></td>
											<td align="center"><? echo 'LC'; ?></td>
                                            <td width="80" align="center"><p><? echo $selectResult[csf('file_no')]; ?></p></td>
                                            <td width="80" align="center"><p><? echo $selectResult[csf('sc_lc')]; ?></p></td>
										</tr>
									<?       	
									$i++;
									}
								}
								
								$all_sc_id=explode(",",$sc_array[$selectResult[csf('id')]]);

								foreach($all_sc_id as $sc_id)
								{
									if($sc_id!=0)
									{	
										if ($i%2==0)  
											$bgcolor="#E9F3FF";
										else
											$bgcolor="#FFFFFF";
									?>	
										<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="30"><? echo $i; ?></td>
											<td width="110"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
											<td width="110">
												<p>
													<?
														$gmts_item='';
														$gmts_item_id=explode(",",$selectResult[csf('gmts_item_id')]);
														foreach($gmts_item_id as $item_id)
														{
															if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
														}
														echo $gmts_item;
													?>
												</p>
											</td>
											<td width="110"><p><? echo $selectResult[csf('job_no_prefix_num')]; ?></p></td>
											<td width="110"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
											<td width="90"><p><? echo $brand_arr[$selectResult[csf('brand_id')]]; ?></p></td>
											<td width="90" align="right"><? echo $selectResult[csf('po_quantity')]; ?></td>
											<td width="90" align="right"><p><? echo $selectResult[csf('rate')]; ?></p></td>
											<td width="100" align="right"><? echo number_format($selectResult[csf('po_total_price')],2); ?></td>
											<td align="center" width="80"><? echo change_date_format($selectResult[csf('shipment_date')]); ?></td>
											<td width="100"><p><? echo $sc_details[$sc_id]; ?></p></td>
											<td align="center"><? echo 'SC'; ?></td>
                                                                                        <td width="80" align="center"><p><? echo $selectResult[csf('file_no')]; ?></p></td>
                                                                                        <td width="80" align="center"><p><? echo $selectResult[csf('sc_lc')]; ?></p></td>
										</tr>
									<?       	
									$i++;
									}
								}
							}
						}
					}
				?>
				</table>
        	</div>
		</div>                   
		<?
		exit();
	}

	if($action_types=='order_select_popup')
	{
		$sql = "SELECT wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst as job_no, wm.style_ref_no, wm.gmts_item_id, wb.unit_price as rate,wb.file_no,wb.sc_lc,wm.brand_id , wm.job_no_prefix_num as job_no_prefix_num
		FROM wo_po_break_down wb, wo_po_details_master wm 
		WHERE wb.job_id = wm.id and wm.buyer_name like '$buyer_id' $selected_order_id $company $search_text $file_no_cond $txt_sc_lc_cond $user_buyer $user_brand and wb.is_deleted = 0 AND wb.status_active = 1 
		group by wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date, wb.job_no_mst, wm.style_ref_no, wm.gmts_item_id, wb.unit_price, wb.file_no,wb.sc_lc,wm.brand_id, wm.job_no_prefix_num "; 

	        ?>
		<div>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1260" class="rpt_table" >
				<thead>
					<th width="40">SL</th>
					<th width="130">PO No</th>
					<th width="130">Item</th>
					<th width="120">Job No</th>
					<th width="120">Style No</th>
					<th width="90">Brand</th>
					<th width="110">PO Quantity</th>
					<th width="80">Rate</th>
					<th width="120">Price</th>
					<th>Shipment Date</th>
	                                <th width="80">File No</th>
	                                <th width="80">SC/LC No</th>
				</thead>
			</table>
			<div style="width:1280px; overflow-y:scroll; max-height:220px;" id="buyer_list_view" align="center">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1260" class="rpt_table" id="tbl_list_search" >
				<?
					$i=1;
					
					$lc_attached_qnty_arr = return_library_array("select wo_po_break_down_id, sum(attached_qnty) as qty from com_export_lc_order_info where status_active = 1 and is_deleted=0 group by wo_po_break_down_id","wo_po_break_down_id","qty");
					$sc_attached_qnty_arr = return_library_array("select wo_po_break_down_id, sum(attached_qnty) as qty from com_sales_contract_order_info where status_active = 1 and is_deleted=0 group by wo_po_break_down_id","wo_po_break_down_id","qty");
						
					$nameArray=sql_select( $sql );
					foreach ($nameArray as $selectResult)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
							
						//$lc_attached_qnty=return_field_value("sum(attached_qnty)","com_export_lc_order_info","wo_po_break_down_id='".$selectResult[csf('id')]."' and status_active = 1 and is_deleted=0");
						//$sc_attached_qnty=return_field_value("sum(attached_qnty)","com_sales_contract_order_info","wo_po_break_down_id='".$selectResult[csf('id')]."' and status_active = 1 and is_deleted=0");
						
						$lc_attached_qnty=$lc_attached_qnty_arr[$selectResult[csf('id')]];
						$sc_attached_qnty=$sc_attached_qnty_arr[$selectResult[csf('id')]];
						$order_attached_qnty=$sc_attached_qnty+$lc_attached_qnty;	
							
						if($order_attached_qnty < $selectResult[csf('po_quantity')] )
						{
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
								<td width="40" align="center"><?php echo "$i"; ?>
								 <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>	
								</td>	
								<td width="130"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
								<td width="130">
									<p>
										<?
											$gmts_item='';
											$gmts_item_id=explode(",",$selectResult[csf('gmts_item_id')]);
											foreach($gmts_item_id as $item_id)
											{
												if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
											}
											echo $gmts_item;
										?>
									</p>
								</td> 
								<td width="120"><p><? echo $selectResult[csf('job_no_prefix_num')]; ?></p></td>
								<td width="120"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
								<td width="90"><p><? echo $brand_arr[$selectResult[csf('brand_id')]]; ?></p></td>
								<td width="110" align="right"><? echo $selectResult[csf('po_quantity')]; ?></td>
								<td width="80" align="right"><p><? echo $selectResult[csf('rate')]; ?></p></td>
								<td width="120" align="right"><? echo number_format($selectResult[csf('po_total_price')],2); ?></td>
								<td align="center"><? echo change_date_format($selectResult[csf('shipment_date')]); ?></td>
	                                                        <td width="80"><p><? echo $selectResult[csf('file_no')]; ?></p></td>
	                                                        <td width="80"><p><? echo $selectResult[csf('sc_lc')]; ?></p></td>
							</tr>
						<?
						$i++;
						}
					}
					?>
				</table>
			</div>
			<table width="790" cellspacing="0" cellpadding="0" style="border:none" align="center">
				<tr>
					<td align="center" height="30" valign="bottom">
						<div style="width:100%"> 
							<div style="width:50%; float:left" align="left">
								<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
							</div>
							<div style="width:50%; float:left" align="left">
								<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
							</div>
						</div>
					</td>
				</tr>
			</table>
		</div>
		<?
	}
	exit();
}  
	
//already user
if($action=="show_po_active_listview")
{
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand brand",'id','brand_name');
	$sql="select wb.id, ci.id as idd, wm.gmts_item_id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wm.order_uom, wm.total_set_qnty as ratio,wm.brand_id, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active from wo_po_break_down wb, wo_po_details_master wm, com_export_lc_order_info ci where wb.job_no_mst = wm.job_no and wb.id=ci.wo_po_break_down_id and ci.com_export_lc_id='$data' and ci.status_active = '1' and ci.is_deleted = '0' order by ci.id";
 	
	/*$arr=array(9=>$attach_detach_array);
	echo create_list_view("list_view", "Order Number,Order Qty,Order Value,Attached Qty,Rate,Attached Value,Style Ref,Item,Job No,Status", "100,100,100,100,60,100,150,150,100,80","1050","200",0, $sql, "get_php_form_data", "idd", "'populate_order_details_form_data'", 0, "0,0,0,0,0,0,0,0,0,status_active", $arr, "po_number,po_quantity,po_total_price,attached_qnty,attached_rate,attached_value,style_ref_no,style_description,job_no_mst,status_active", "requires/export_lc_amendment_controller",'','0,1,1,1,2,2,0,0,0,0','1,po_quantity,po_total_price,attached_qnty,0,attached_value,0,0,0,0');*/
	
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1150" class="rpt_table" >
            <thead>
                <th width="100">Order Number</th>
                <th width="80">Order Qty</th>
                <th width="100">Order Value</th>
                <th width="80">Attached Qty</th>
                <th width="50">UOM</th>
                <th width="60">Rate</th>
                <th width="100">Attached Value</th>
                <th width="100">Attached Qty (Pcs)</th>
                <th width="120">Style Ref</th>
                <th width="130">Gmts. Item</th>
                <th width="80">Job No</th>
                <th width="50">Brand</th>
                <th>Status</th>
            </thead>
        </table>
        <div style="width:1150px; overflow-y:scroll; max-height:200px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1130" class="rpt_table" id="tbl_list_search" >
            <?
                $i=1;
                $nameArray=sql_select( $sql );
                foreach ($nameArray as $selectResult)
                {
                    if($i%2==0) $bgcolor="#E9F3FF";
                    else $bgcolor="#FFFFFF";
					
					$order_qnty_in_pcs=$selectResult[csf('attached_qnty')]*$selectResult[csf('ratio')];
					$total_order_qnty_in_pcs+=$order_qnty_in_pcs;
					$total_attc_value+=$selectResult[csf('attached_value')];
					
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="get_php_form_data('<? echo $selectResult[csf('idd')]; ?>','populate_order_details_form_data','requires/export_lc_amendment_controller')"> 
                        <td width="100"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                        <td width="80" align="right"><? echo $selectResult[csf('po_quantity')]; ?></td>
                        <td width="100" align="right"><? echo number_format($selectResult[csf('po_total_price')],2); ?></td>
                        <td width="80" align="right"><? echo $selectResult[csf('attached_qnty')]; ?></td>
                         <td width="50" align="center"><? echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></td>
                        <td width="60" align="right"><? echo number_format($selectResult[csf('attached_rate')],2); ?></td>
                        <td width="100" align="right"><? echo number_format($selectResult[csf('attached_value')],2); ?></td>
                        <td width="100" align="right"><? echo $order_qnty_in_pcs; ?></td>
                        <td width="120"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
                        <td width="130">
                            <p>
                                <?
                                    $gmts_item='';
                                    $gmts_item_id=explode(",",$selectResult[csf('gmts_item_id')]);
                                    foreach($gmts_item_id as $item_id)
                                    {
                                        if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
                                    }
                                    echo $gmts_item;
                                ?>
                            </p>
                        </td> 
                        <td width="80"><? echo $selectResult[csf('job_no_mst')]; ?></td>
                        <td width="50"><? echo $brand_arr[$selectResult[csf('brand_id')]]; ?></td>
                        <td><? echo $attach_detach_array[$selectResult[csf('status_active')]]; ?></td>		
                    </tr>
                <?
                	$i++;
                }
                ?>
            </table>
        </div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1150" class="rpt_table">
        	<tfoot>
            	<th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th width="60">Total</th>
                <th width="100" align="right"><? echo number_format($total_attc_value,2); ?></th>
                <th width="100" align="right"><? echo number_format($total_order_qnty_in_pcs,0); ?></th>
                <th width="120">&nbsp;</th>
                <th width="130">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th>&nbsp;</th>
            </tfoot>
        </table>
	</div>   
    <?
	exit();
}


if($action=="populate_order_details_form_data")
{
	$data_array=sql_select("select wb.id, ci.id as idd, wm.style_description, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wm.gmts_item_id, wb.unit_price, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active, ci.com_export_lc_id, ci.fabric_description, ci.category_no, ci.hs_code from wo_po_break_down wb, wo_po_details_master wm, com_export_lc_order_info ci where wb.job_no_mst = wm.job_no and wb.id=ci.wo_po_break_down_id and ci.id='$data' and ci.status_active = '1' and ci.is_deleted = '0'");
 
	foreach ($data_array as $row)
	{ 	
		 echo "$('#tbl_order_list tbody tr:not(:first)').remove();\n";
		 
		 $gmts_item='';
		 $gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
		 foreach($gmts_item_id as $item_id)
		 {
			if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
		 }
		
		 echo "document.getElementById('txtordernumber_1').value 			= '".$row[csf("po_number")]."';\n";
		 echo "document.getElementById('txtorderqnty_1').value 				= '".$row[csf("po_quantity")]."';\n";
		 echo "document.getElementById('txtordervalue_1').value 			= '".$row[csf("po_total_price")]."';\n";
		 echo "document.getElementById('txtattachedqnty_1').value 			= '".$row[csf("attached_qnty")]."';\n";
		 echo "document.getElementById('hideattachedqnty_1').value 			= '".$row[csf("attached_qnty")]."';\n";
		 echo "document.getElementById('hiddenunitprice_1').value 			= '".$row[csf("attached_rate")]."';\n";
		 echo "document.getElementById('txtattachedvalue_1').value 			= '".$row[csf("attached_value")]."';\n";
		 echo "document.getElementById('txtstyleref_1').value 				= '".$row[csf("style_ref_no")]."';\n";
		 echo "document.getElementById('txtitemname_1').value 				= '".$gmts_item."';\n";
		 echo "document.getElementById('txtjobno_1').value 					= '".$row[csf("job_no_mst")]."';\n";
		 echo "document.getElementById('cbopostatus_1').value 				= '".$row[csf("status_active")]."';\n";
		echo "document.getElementById('txtfabdescrip_1').value 				= '".$row[csf("fabric_description")]."';\n";
		echo "document.getElementById('txtcategory_1').value 				= '".$row[csf("category_no")]."';\n";
		echo "document.getElementById('txthscode_1').value 				= '".$row[csf("hs_code")]."';\n";
		 
		 echo "document.getElementById('hiddenwopobreakdownid_1').value 	= '".$row[csf("id")]."';\n";
		 echo "document.getElementById('hiddenexportlcorderid').value 		= '".$row[csf("idd")]."';\n";
		 echo "document.getElementById('txt_tot_row').value 				= '1';\n";
		 
		 echo "math_operation( 'totalOrderqnty', 'txtorderqnty_', '+', 1 );\n";
		 echo "math_operation( 'totalOrdervalue', 'txtordervalue_', '+', 1 );\n";
		 echo "math_operation( 'totalAttachedqnty', 'txtattachedqnty_', '+', 1 );\n";
		 echo "math_operation( 'totalAttachedvalue', 'txtattachedvalue_', '+', 1 );\n";
		 
		 $order_attahed_qnty_sc=0; $order_attahed_qnty_lc=0; $order_attahed_val_sc=0; $order_attahed_val_lc=0; $sc_no=''; $lc_no=''; 	
		 $sql_sc ="SELECT a.contract_no, sum(b.attached_qnty) as at_qt,sum(b.attached_value) as at_val FROM com_sales_contract a, com_sales_contract_order_info b WHERE a.id=b.com_sales_contract_id and b.wo_po_break_down_id='".$row[csf("id")]."' and b.status_active = 1 and b.is_deleted=0 group by a.id, a.contract_no";
		 $result_array_sc=sql_select($sql_sc);
		 foreach($result_array_sc as $scArray)
		 {
			if ($sc_no=="") $sc_no = $scArray[csf('contract_no')]; else $sc_no.=",".$scArray[csf('contract_no')];
			$order_attahed_qnty_sc+=$scArray[csf('at_qt')];
			//$order_attahed_val_sc+=$scArray[csf('at_val')];
		 }
		
		 $sql_lc="SELECT a.export_lc_no, sum(b.attached_qnty) as at_qt,sum(b.attached_value) as at_val FROM com_export_lc a, com_export_lc_order_info b WHERE a.id=b.com_export_lc_id and b.wo_po_break_down_id='".$row[csf("id")]."' and b.id!='".$data."' and b.status_active = 1 and b.is_deleted=0 group by a.id, a.export_lc_no";
		 $result_array_sc=sql_select($sql_lc);
		 foreach($result_array_sc as $lcArray)
		 {
			if ($lc_no=="") $lc_no = $lcArray[csf('export_lc_no')]; else $lc_no.=",".$lcArray[csf('export_lc_no')];
			$order_attahed_qnty_lc+=$lcArray[csf('at_qt')];
			//$order_attahed_val_lc+=$lcArray[csf('at_val')];
		 }
		 
		 $order_attached_qnty=$order_attahed_qnty_sc+$order_attahed_qnty_lc;
		 //$order_attached_val=$order_attahed_val_sc+$order_attahed_val_lc;
		 
		 echo "document.getElementById('order_attached_qnty_1').value 		= '".$order_attached_qnty."';\n";
		 echo "document.getElementById('order_attached_lc_no_1').value 		= '".$lc_no."';\n";
		 echo "document.getElementById('order_attached_lc_qty_1').value 	= '".$order_attahed_qnty_lc."';\n";
		 echo "document.getElementById('order_attached_sc_no_1').value 		= '".$sc_no."';\n";
		 echo "document.getElementById('order_attached_sc_qty_1').value 	= '".$order_attahed_qnty_sc."';\n";
		 
		if($db_type==0)
		{
			 $attached_po_id=return_field_value("group_concat(wo_po_break_down_id)","com_export_lc_order_info","com_export_lc_id='".$row[csf("com_export_lc_id")]."' and status_active=1 and is_deleted=0");
		}
		else
		{
			//$attached_po_id=return_field_value("LISTAGG(wo_po_break_down_id, ',') WITHIN GROUP (ORDER BY wo_po_break_down_id) as po_id","com_export_lc_order_info","com_export_lc_id='".$row[csf("com_export_lc_id")]."' and status_active=1 and is_deleted=0","po_id");

			$attached_po_id=return_field_value(" rtrim(xmlagg(xmlelement(e,wo_po_break_down_id,',').extract('//text()') order by wo_po_break_down_id).GetClobVal(),',') as po_id","com_export_lc_order_info","com_export_lc_id='".$row[csf("com_export_lc_id")]."' and status_active=1 and is_deleted=0","po_id");	
			$attached_po_id = $attached_po_id->load();	
		}
		
		 echo "document.getElementById('hidden_selectedID').value 		= '".$attached_po_id."';\n";
		 		 
		 echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_po_selection_save',2);\n";  		 
		 exit();
	}

}

if($action=="populate_attached_po_id")
{
	$data=explode("**",$data);
	$lc_id=$data[0];
	$type=$data[1];
	$amnd_id=$data[2];
	
	if($db_type==0)
	{
		$attached_po_id=return_field_value("group_concat(wo_po_break_down_id)","com_export_lc_order_info","com_export_lc_id='$lc_id' and status_active=1 and is_deleted=0");
	}
	else
	{
		//$attached_po_id=return_field_value("LISTAGG(wo_po_break_down_id, ',') WITHIN GROUP (ORDER BY wo_po_break_down_id) as po_id","com_export_lc_order_info","com_export_lc_id='$lc_id' and status_active=1 and is_deleted=0","po_id");	
		$attached_po_id=return_field_value(" rtrim(xmlagg(xmlelement(e,wo_po_break_down_id,',').extract('//text()') order by wo_po_break_down_id).GetClobVal(),',') as po_id","com_export_lc_order_info","com_export_lc_id='$lc_id' and status_active=1 and is_deleted=0","po_id");	
		$attached_po_id = $attached_po_id->load();	
	}
	
	if($type==2)
	{
		$con = connect();
		execute_query("update com_export_lc_amendment set po_id='$attached_po_id' where id='$amnd_id'");
		disconnect($con);
	}
	
	echo "document.getElementById('hidden_selectedID').value 		= '".$attached_po_id."';\n";
	exit();	
}


if($action=="order_list_for_attach")
{
	$explode_data = explode("**",$data);//0->wo_po_break_down id's, 1->table row
	$data=$explode_data[0];
	$table_row=$explode_data[1];
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand brand",'id','brand_name');
	if($data!="")
	{
		$data_array="SELECT wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.style_ref_no,wm.style_description,wb.unit_price,wm.brand_id FROM wo_po_break_down wb, wo_po_details_master wm WHERE wb.job_no_mst = wm.job_no AND wb.id in ($data) AND wb.is_deleted = 0 AND wb.status_active = 1";
		
		$data_array=sql_select($data_array);
 		foreach ($data_array as $row)
		{
			$table_row++;
			$order_attahed_qnty_sc=0; $order_attahed_qnty_lc=0; $order_attahed_val_sc=0; $order_attahed_val_lc=0; $sc_no=''; $lc_no=''; 	
			$sql_sc ="SELECT a.contract_no, sum(b.attached_qnty) as at_qt,sum(b.attached_value) as at_val FROM com_sales_contract a, com_sales_contract_order_info b WHERE a.id=b.com_sales_contract_id and b.wo_po_break_down_id='".$row[csf("id")]."' and b.status_active = 1 and b.is_deleted=0 group by a.id, a.contract_no";
			$result_array_sc=sql_select($sql_sc);
			foreach($result_array_sc as $scArray)
			{
				if ($sc_no=="") $sc_no = $scArray[csf('export_lc_no')]; else $sc_no.=",".$scArray[csf('contract_no')];
				$order_attahed_qnty_sc+=$scArray[csf('at_qt')];
				$order_attahed_val_sc+=$scArray[csf('at_val')];
			}
			
			$sql_lc="SELECT a.export_lc_no, sum(b.attached_qnty) as at_qt,sum(b.attached_value) as at_val FROM com_export_lc a, com_export_lc_order_info b WHERE a.id=b.com_export_lc_id and b.wo_po_break_down_id='".$row[csf("id")]."' and b.status_active = 1 and b.is_deleted=0 group by a.id, a.export_lc_no";
			$result_array_sc=sql_select($sql_lc);
			foreach($result_array_sc as $lcArray)
			{
				if ($lc_no=="") $lc_no = $lcArray[csf('export_lc_no')]; else $lc_no.=",".$lcArray[csf('export_lc_no')];
				$order_attahed_qnty_lc+=$lcArray[csf('at_qt')];
				$order_attahed_val_lc+=$lcArray[csf('at_val')];
			}
			
			$order_attached_qnty=$order_attahed_qnty_sc+$order_attahed_qnty_lc;
			$order_attached_val=$order_attahed_val_sc+$order_attahed_val_lc;
			
			$remaining_qnty = $row[csf("po_quantity")]-$order_attached_qnty; 
			$remaining_value = $row[csf("po_total_price")]-$order_attached_val;
			
			?>	
			<tr class="general" id="tr_<? echo $table_row; ?>">
				<td>
					<input type="text" name="txtordernumber_<? echo $table_row; ?>" id="txtordernumber_<? echo $table_row; ?>" class="text_boxes" style="width:100px"  value="<? echo $row[csf("po_number")]; ?>" onDblClick= "openmypage('requires/export_lc_amendment_controller.php?action=order_popup&types=order_select_popup&buyer_id='+document.getElementById('cbo_buyer_name').value+'&selectID='+document.getElementById('hidden_selectedID').value+'&export_lcID='+document.getElementById('txt_system_id').value+'&company_id='+document.getElementById('cbo_beneficiary_name').value,'PO Selection Form','<? echo $table_row; ?>')" readonly= "readonly" placeholder="Double Click" />
					<input type="hidden" name="hiddenwopobreakdownid_<? echo $table_row; ?>" id="hiddenwopobreakdownid_<? echo $table_row; ?>" readonly= "readonly" value="<? echo $row[csf("id")]; ?>" />
				</td>
				<td>
					<input type="text" name="txtorderqnty_<? echo $table_row; ?>" id="txtorderqnty_<? echo $table_row; ?>" class="text_boxes" style="width:65px; text-align:right" readonly= "readonly" value="<? echo $row[csf("po_quantity")]; ?>" />
				</td>
				<td>
					<input type="text" name="txtordervalue_<? echo $table_row; ?>" id="txtordervalue_<? echo $table_row; ?>" class="text_boxes" style="width:80px; text-align:right" readonly= "readonly" value="<? echo number_format($row[csf("po_total_price")],2,'.',''); ?>" />
				</td>
				<td>
					<input type="text" name="txtattachedqnty_<? echo $table_row; ?>" id="txtattachedqnty_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:65px" onKeyUp="validate_attach_qnty(<? echo $table_row; ?>)" value="<? echo $remaining_qnty; ?>" />
					<input type="hidden" name="hideattachedqnty_<? echo $table_row; ?>" id="hideattachedqnty_<? echo $table_row;?>" class="text_boxes_numeric" value="<? echo $remaining_qnty; ?>"/>
				</td>
				<td>
                    <input type="text" name="hiddenunitprice_<? echo $table_row; ?>" id="hiddenunitprice_<? echo $table_row; ?>" value="<? echo $row[csf("unit_price")]; ?>" style="width:50px" class="text_boxes_numeric" onKeyUp="calculate_attach_val(<? echo $table_row; ?>)" disabled/>
				</td>
				<td>
					<input type="text" name="txtattachedvalue_<? echo $table_row; ?>" id="txtattachedvalue_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:80px" readonly= "readonly" value="<? echo number_format($remaining_value,2,'.',''); ?>" />
				</td>
				<td>
					<input type="text" name="txtstyleref_<? echo $table_row; ?>" id="txtstyleref_<? echo $table_row; ?>" class="text_boxes" style="width:90px" readonly= "readonly" value="<? echo $row[csf("style_ref_no")]; ?>" />
				</td>
				<td>
					<input type="text" name="txtitemname_<? echo $table_row; ?>" id="txtitemname_<? echo $table_row; ?>" class="text_boxes" style="width:110px" readonly= "readonly" value="<? echo $row[csf("style_description")]; ?>" />
				</td>
				<td>
					<input type="text" name="txtjobno_<? echo $table_row; ?>" id="txtjobno_<? echo $table_row; ?>" class="text_boxes" style="width:80px" readonly= "readonly" value="<? echo $row[csf("job_no_mst")]; ?>"  />
				</td>
                <td><input type="text" name="txtfabdescrip_<? echo $table_row; ?>" id="txtfabdescrip_<? echo $table_row; ?>" class="text_boxes" style="width:90px" /></td>
                <td><input type="text" name="txtcategory_<? echo $table_row; ?>" id="txtcategory_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:50px" /></td>
                <td><input type="text" name="txthscode_<? echo $table_row; ?>" id="txthscode_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:40px"/></td>
                <td><input type="text" name="txtbrand_<? echo $table_row; ?>" id="txtbrand_<? echo $table_row; ?>" class="text_boxes" style="width:40px"  value="<? echo $brand_arr[$row[csf("brand_id")]]; ?>" readonly/></td>
				<td>                             
					<? 
						 echo create_drop_down( "cbopostatus_".$table_row, 60, $attach_detach_array,"", $row[csf("status_active")], "", 1, "" );
					?>
				</td>
               		<input type="hidden" name="order_attached_qnty_<? echo $table_row; ?>" id="order_attached_qnty_<? echo $table_row; ?>" value="<? echo $order_attached_qnty; ?>" readonly= "readonly" />
                    <input type="hidden" name="order_attached_lc_no_<? echo $table_row; ?>" id="order_attached_lc_no_<? echo $table_row; ?>" value="<? echo $lc_no; ?>" readonly= "readonly" />
                    <input type="hidden" name="order_attached_lc_qty_<? echo $table_row; ?>" id="order_attached_lc_qty_<? echo $table_row; ?>" value="<? echo $order_attahed_qnty_lc; ?>" readonly= "readonly" />
                    <input type="hidden" name="order_attached_sc_no_<? echo $table_row; ?>" id="order_attached_sc_no_<? echo $table_row; ?>" value="<? echo $sc_no; ?>" readonly= "readonly" />
                    <input type="hidden" name="order_attached_sc_qty_<? echo $table_row; ?>" id="order_attached_sc_qty_<? echo $table_row; ?>" value="<? echo $order_attahed_qnty_sc; ?>" readonly= "readonly" />
			</tr>
		<?

		}//end foreach
		
	}//end if data condition
	
}

//amendment popup
if($action=="amendment_popup")
{
	echo load_html_head_contents("Export LC Amendment Form", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		function js_set_value(id)
		{
			$('#hidden_amendment_no').val(id);
			parent.emailwindow.hide();
		}	
    </script>
    <div align="center" style="width:100%; margin-top:10px">
    <input type="hidden" id="hidden_amendment_no" value="" />
		<?
            $sql = "SELECT id, amendment_no, amendment_date, export_lc_no, lc_value FROM com_export_lc_amendment WHERE export_lc_id='$export_lc_id' and amendment_no<>0 and status_active=1 and is_deleted=0 and is_original=0 order by amendment_no asc";
                        
            echo  create_list_view("list_view", "Amendment No,Amendment Date,Export LC No,LC Value", "110,100,150,130","600","250",0, $sql , "js_set_value", "id", "", 1, 0, 0, "amendment_no,amendment_date,export_lc_no,lc_value", "",'setFilterGrid(\'list_view\',-1)','0,3,0,2');
        ?>
    </div>
    <?
	exit();
}

if($action=="get_amendment_data")
{
	$data_array = sql_select("SELECT export_lc_id, amendment_no, amendment_date,lien_date, amendment_value, value_change_by, last_shipment_date, expiry_date, shipping_mode, inco_term, inco_term_place, port_of_entry, port_of_loading, port_of_discharge, pay_term, tenor, claim_adjustment, claim_adjust_by, discount_clauses, remarks FROM com_export_lc_amendment WHERE id='$data' and status_active=1 and is_deleted=0");
						
	foreach ($data_array as $row)
	{ 	
 		 echo "document.getElementById('txt_amendment_no').value 			= '".$row[csf("amendment_no")]."';\n";
 		 echo "document.getElementById('txt_amendment_date').value 			= '".change_date_format($row[csf("amendment_date")])."';\n";
 		 echo "document.getElementById('txt_amed_lien_date').value 			= '".change_date_format($row[csf("lien_date")])."';\n";
		 echo "document.getElementById('txt_amendment_value').value 		= '".$row[csf("amendment_value")]."';\n";
		 echo "document.getElementById('hide_amendment_value').value 		= '".$row[csf("amendment_value")]."';\n";
		 echo "document.getElementById('cbo_value_change_by').value 		= '".$row[csf("value_change_by")]."';\n";
		 echo "document.getElementById('hide_value_change_by').value 		= '".$row[csf("value_change_by")]."';\n";
		 echo "document.getElementById('txt_claim_adjustment_amnd').value 	= '".$row[csf("claim_adjustment")]."';\n";
		 echo "document.getElementById('hide_claim_adjustment_amnd').value 	= '".$row[csf("claim_adjustment")]."';\n";
		 echo "document.getElementById('cbo_claim_adjust_by').value 		= '".$row[csf("claim_adjust_by")]."';\n";
		 echo "document.getElementById('hide_claim_adjust_by').value 		= '".$row[csf("claim_adjust_by")]."';\n";
		 
		 $sql=sql_select("SELECT last_shipment_date, expiry_date, shipping_mode, inco_term, inco_term_place, port_of_entry, port_of_loading, port_of_discharge, pay_term, remarks, tenor, discount_clauses FROM com_export_lc WHERE id='".$row[csf('export_lc_id')]."'");
		 
		 echo "document.getElementById('txt_last_shipment_date_amnd').value	= '".change_date_format($sql[0][csf("last_shipment_date")])."';\n";
		 echo "document.getElementById('txt_expiry_date_amend').value 		= '".change_date_format($sql[0][csf("expiry_date")])."';\n";
		 echo "document.getElementById('cbo_shipping_mode_amnd').value 		= '".$sql[0][csf("shipping_mode")]."';\n";
		 echo "document.getElementById('cbo_inco_term').value 				= '".$sql[0][csf("inco_term")]."';\n";  
		 echo "document.getElementById('txt_inco_term_place').value 		= '".$sql[0][csf("inco_term_place")]."';\n";
		 echo "document.getElementById('txt_port_of_entry_amnd').value 		= '".$sql[0][csf("port_of_entry")]."';\n";  
		 echo "document.getElementById('txt_port_of_loading_amnd').value 	= '".$sql[0][csf("port_of_loading")]."';\n";
		 echo "document.getElementById('txt_port_of_discharge_amnd').value 	= '".$sql[0][csf("port_of_discharge")]."';\n";
		 echo "document.getElementById('cbo_pay_term_amnd').value 			= '".$sql[0][csf("pay_term")]."';\n";
		 echo "document.getElementById('txt_tenor_amnd').value 				= '".$sql[0][csf("tenor")]."';\n";
		 echo "document.getElementById('txt_discount_clauses_amnd').value 	= '".$sql[0][csf("discount_clauses")]."';\n";
		 echo "document.getElementById('txt_remarks_amnd').value 			= '".$sql[0][csf("remarks")]."';\n";
		 echo "document.getElementById('update_id').value 					= '".$data."';\n";
 		 echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_amendment_save',1);\n";
 	}
	exit();
}


if ($action=="save_update_delete_amendment")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$prev_value_change_by=return_field_value("value_change_by","com_export_lc_amendment","id=".$update_id);
	
	if(str_replace("'", '', $cbo_value_change_by)==2 )
	{
		$txt_system_id=str_replace("'", "", $txt_system_id);
		$lc_value=return_field_value("lc_value","com_export_lc","id=".$txt_system_id);
		
		if($prev_value_change_by==str_replace("'", '', $cbo_value_change_by))
		{
			$new_lc_value = $lc_value+str_replace("'", '', $hide_amendment_value)-str_replace("'", '', $txt_amendment_value);
		}
		else
		{
			
			$new_lc_value = $lc_value-str_replace("'", '', $hide_amendment_value)-str_replace("'", '', $txt_amendment_value);
		}
		
		
		$pre_tot_attached =  sql_select("select sum(b.attached_value) as attached_value from com_export_lc a, com_export_lc_order_info b where a.id = b.com_export_lc_id and a.id=".$txt_system_id." and b.status_active = 1 and b.is_deleted = 0");

        $new_lc_value = number_format($new_lc_value,2,".","");
        $tot_attached = number_format($pre_tot_attached[0][csf("attached_value")],2,".","");
		if($new_lc_value<$tot_attached)
		{
			echo "11** LC Value Not Allow Less Than Attached Value. ".number_format(($tot_attached),2,'.','')." = ".number_format($new_lc_value,2,'.','');disconnect($con);die;
		}
	}
	
	//txt_system_id cbo_value_change_by
	
 	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if (is_duplicate_field( "amendment_no", "com_export_lc_amendment", "amendment_no=$txt_amendment_no and export_lc_id=$txt_system_id and status_active=1")==1)
		{
			echo "11**0"; disconnect($con);
			die;			
		}
		
		if($db_type==0)
		{
			$attached_po_id=return_field_value("group_concat(wo_po_break_down_id)","com_export_lc_order_info","com_export_lc_id=$txt_system_id and status_active=1 and is_deleted=0");
		}
		else
		{
			//$attached_po_id=return_field_value(" rtrim(xmlagg(xmlelement(e,wo_po_break_down_id,',').extract('//text()') order by wo_po_break_down_id).GetClobVal(),',') as po_id","com_export_lc_order_info","com_export_lc_id=$txt_system_id and status_active=1 and is_deleted=0","po_id");
			//$attached_po_id = $attached_po_id->load();
			
			$attached_po_id=return_field_value("LISTAGG(wo_po_break_down_id, ',') WITHIN GROUP (ORDER BY wo_po_break_down_id) as po_id","com_export_lc_order_info","com_export_lc_id=$txt_system_id and status_active=1 and is_deleted=0","po_id");	
		}
		
		$user_id=''; $entry_date='';
		$data_array=sql_select("select export_lc_no, lc_value, last_shipment_date, expiry_date, shipping_mode, inco_term, inco_term_place, port_of_entry, port_of_loading, port_of_discharge, pay_term, max_btb_limit, foreign_comn, local_comn, tolerance, remarks, tenor, discount_clauses, claim_adjustment,lien_date, updated_by, inserted_by, update_date, insert_date from com_export_lc where id=$txt_system_id");
		
		if($data_array[0][csf('updated_by')]==0)
		{
			$user_id=$data_array[0][csf('inserted_by')];
			$entry_date=$data_array[0][csf('insert_date')];
		}
		else 
		{
			$user_id=$data_array[0][csf('updated_by')];
			$entry_date=$data_array[0][csf('update_date')];
		}
		
		$lc_value = $data_array[0][csf('lc_value')];
		$export_lc_no = $data_array[0][csf('export_lc_no')];
		$claim_adjustment = $data_array[0][csf('claim_adjustment')];
		
		if( str_replace("'", '', $cbo_value_change_by)==1 )
			$new_lc_value = $lc_value+str_replace("'", '', $txt_amendment_value);
		else if(str_replace("'", '', $cbo_value_change_by)==2 )
			$new_lc_value = $lc_value-str_replace("'", '', $txt_amendment_value);
		else
			$new_lc_value = $lc_value;
			
		if( str_replace("'", '', $cbo_claim_adjust_by)==1 )
			$new_claim_adjustment = $claim_adjustment+str_replace("'", '', $txt_claim_adjustment_amnd);
		else if(str_replace("'", '', $cbo_claim_adjust_by)==2 )
			$new_claim_adjustment = $claim_adjustment-str_replace("'", '', $txt_claim_adjustment_amnd);	
		
		$maximum_tolarence = 0; $minimum_tolarence = 0;
 		$maximum_tolarence = $new_lc_value+($new_lc_value*str_replace("'", '', $data_array[0][csf('tolerance')]))/100;
		$minimum_tolarence = $new_lc_value-($new_lc_value*str_replace("'", '', $data_array[0][csf('tolerance')]))/100;
 		
		$foreign_comn_value = 0;$local_comn_value = 0;
		$foreign_comn_value = ($new_lc_value*str_replace("'", '', $data_array[0][csf('foreign_comn')]))/100;
		$local_comn_value = ($new_lc_value*str_replace("'", '', $data_array[0][csf('local_comn')]))/100;
		
		$max_btb_limit_value = ($new_lc_value*str_replace("'", '', $data_array[0][csf('max_btb_limit')]))/100;
		
		//update export lc table
		$lc_sql=sql_select("select lc_value, initial_lc_value from com_export_lc where id=$txt_system_id");
		$ini_lc_value=$lc_sql[0][csf("initial_lc_value")];
		$lc_value=$lc_sql[0][csf("lc_value")];
		if($ini_lc_value>0)
		{
			$field_array_update="lc_value*maximum_tolarence*minimum_tolarence*last_shipment_date*expiry_date*shipping_mode*pay_term*inco_term*inco_term_place*port_of_entry* port_of_loading*port_of_discharge*max_btb_limit_value*foreign_comn_value*local_comn_value*remarks*discount_clauses*tenor*claim_adjustment*updated_by*update_date";
		
			$data_array_update=$new_lc_value."*".$maximum_tolarence."*".$minimum_tolarence."*".$txt_last_shipment_date_amnd."*".$txt_expiry_date_amend."*".$cbo_shipping_mode_amnd."*".$cbo_pay_term_amnd."*".$cbo_inco_term."*".$txt_inco_term_place."*".$txt_port_of_entry_amnd."*".$txt_port_of_loading_amnd."*".$txt_port_of_discharge_amnd."*".$max_btb_limit_value."*".$foreign_comn_value."*".$local_comn_value."*".$txt_remarks_amnd."*".$txt_discount_clauses_amnd."*".$txt_tenor_amnd."*'".$new_claim_adjustment."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		}
		else
		{
			$field_array_update="lc_value*maximum_tolarence*minimum_tolarence*last_shipment_date*expiry_date*shipping_mode*pay_term*inco_term*inco_term_place*port_of_entry* port_of_loading*port_of_discharge*max_btb_limit_value*foreign_comn_value*local_comn_value*remarks*discount_clauses*tenor*claim_adjustment*updated_by*update_date*initial_lc_value";
		
			$data_array_update=$new_lc_value."*".$maximum_tolarence."*".$minimum_tolarence."*".$txt_last_shipment_date_amnd."*".$txt_expiry_date_amend."*".$cbo_shipping_mode_amnd."*".$cbo_pay_term_amnd."*".$cbo_inco_term."*".$txt_inco_term_place."*".$txt_port_of_entry_amnd."*".$txt_port_of_loading_amnd."*".$txt_port_of_discharge_amnd."*".$max_btb_limit_value."*".$foreign_comn_value."*".$local_comn_value."*".$txt_remarks_amnd."*".$txt_discount_clauses_amnd."*".$txt_tenor_amnd."*'".$new_claim_adjustment."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'".$lc_value."'";
		}
		
		
		if(is_duplicate_field("amendment_no", "com_export_lc_amendment", "amendment_no=0 and export_lc_id=$txt_system_id and status_active=1")==0)
		{
			$id=return_next_id( "id", "com_export_lc_amendment", 1 );
			
			$field_array="id, amendment_no, amendment_date, export_lc_id, lien_date, export_lc_no, lc_value, amendment_value, value_change_by, last_shipment_date, expiry_date, shipping_mode, pay_term, inco_term, inco_term_place, port_of_entry, port_of_loading, port_of_discharge, remarks, tenor, discount_clauses, claim_adjustment, claim_adjust_by, po_id, is_original, inserted_by, insert_date";
			
			$amnd_date="";
			$data_array_amnd="(".$id.",0,'".$amnd_date."',".$txt_system_id.",'".$data_array[0][csf('lien_date')]."','".$export_lc_no."',".$lc_value.",0,0,'".$data_array[0][csf('last_shipment_date')]."','".$data_array[0][csf('expiry_date')]."','".$data_array[0][csf('shipping_mode')]."','".$data_array[0][csf('pay_term')]."','".$data_array[0][csf('inco_term')]."','".$data_array[0][csf('inco_term_place')]."','".$data_array[0][csf('port_of_entry')]."','".$data_array[0][csf('port_of_loading')]."','".$data_array[0][csf('port_of_discharge')]."','".$data_array[0][csf('remarks')]."','".$data_array[0][csf('tenor')]."','".$data_array[0][csf('discount_clauses')]."','".$data_array[0][csf('claim_adjustment')]."',0,'".$attached_po_id."',1,".$user_id.",'".$entry_date."')";
			
			//echo "insert into com_export_lc_amendment (".$field_array.") values ".$data_array_amnd;die;
			/*$rID2=sql_insert("com_export_lc_amendment",$field_array,$data_array_amnd,0);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			} */
			
			$id+=1;
		}
		else
		{
			$id=return_next_id( "id", "com_export_lc_amendment", 1 );
		}
		
		$shipment_date=strtotime($data_array[0][csf('last_shipment_date')]);
		$shipment_date_amnd=strtotime(str_replace("'","",$txt_last_shipment_date_amnd));
		$expiry_date=strtotime($data_array[0][csf('expiry_date')]);
		$expiry_date_amnd=strtotime(str_replace("'","",$txt_expiry_date_amend));

		$field_array_amnd="id, amendment_no, amendment_date,lien_date, export_lc_id, export_lc_no, lc_value, amendment_value, value_change_by, claim_adjustment, claim_adjust_by";
		$data_array_amnd2="(".$id.",".$txt_amendment_no.",".$txt_amendment_date.",".$txt_amed_lien_date.",".$txt_system_id.",'".$export_lc_no."','".$new_lc_value."',".$txt_amendment_value.",".$cbo_value_change_by.",".$txt_claim_adjustment_amnd.",".$cbo_claim_adjust_by;
		
		if($shipment_date!=$shipment_date_amnd)
		{
			$field_array_amnd.=",last_shipment_date";
			$data_array_amnd2.=",".$txt_last_shipment_date_amnd;
		}
		
		if($expiry_date!=$expiry_date_amnd)
		{
			$field_array_amnd.=",expiry_date";
			$data_array_amnd2.=",".$txt_expiry_date_amend;
		}
		
		if($data_array[0][csf('shipping_mode')]!=str_replace("'","",$cbo_shipping_mode_amnd))
		{
			$field_array_amnd.=",shipping_mode";
			$data_array_amnd2.=",".$cbo_shipping_mode_amnd;
		}
		
		if($data_array[0][csf('pay_term')]!=str_replace("'","",$cbo_pay_term_amnd))
		{
			$field_array_amnd.=",pay_term";
			$data_array_amnd2.=",".$cbo_pay_term_amnd;
		}
		
		if($data_array[0][csf('inco_term')]!=str_replace("'","",$cbo_inco_term))
		{
			$field_array_amnd.=",inco_term";
			$data_array_amnd2.=",".$cbo_inco_term;
		}
		
		if($data_array[0][csf('inco_term_place')]!=str_replace("'","",$txt_inco_term_place))
		{
			$field_array_amnd.=",inco_term_place";
			$data_array_amnd2.=",".$txt_inco_term_place;
		}
		
		if($data_array[0][csf('port_of_entry')]!=str_replace("'","",$txt_port_of_entry_amnd))
		{
			$field_array_amnd.=",port_of_entry";
			$data_array_amnd2.=",".$txt_port_of_entry_amnd;
		}
		
		if($data_array[0][csf('port_of_loading')]!=str_replace("'","",$txt_port_of_loading_amnd))
		{
			$field_array_amnd.=",port_of_loading";
			$data_array_amnd2.=",".$txt_port_of_loading_amnd;
		}
		
		if($data_array[0][csf('port_of_discharge')]!=str_replace("'","",$txt_port_of_discharge_amnd))
		{
			$field_array_amnd.=",port_of_discharge";
			$data_array_amnd2.=",".$txt_port_of_discharge_amnd;
		}
		
		if($data_array[0][csf('remarks')]!=str_replace("'","",$txt_remarks_amnd))
		{
			$field_array_amnd.=",remarks";
			$data_array_amnd2.=",".$txt_remarks_amnd;
		}
		
		if($data_array[0][csf('tenor')]!=str_replace("'","",$txt_tenor_amnd))
		{
			$field_array_amnd.=",tenor";
			$data_array_amnd2.=",".$txt_tenor_amnd;
		}
		
		if($data_array[0][csf('discount_clauses')]!=str_replace("'","",$txt_discount_clauses_amnd))
		{
			$field_array_amnd.=",discount_clauses";
			$data_array_amnd2.=",".$txt_discount_clauses_amnd;
		}
		
		$field_array_amnd.=",inserted_by, insert_date";
		$data_array_amnd2.=",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		/*$data_array.=",(".$id.",".$txt_amendment_no.",".$txt_amendment_date.",".$txt_system_id.",'".$export_lc_no."',".$new_lc_value.",".$txt_amendment_value.",".$cbo_value_change_by.",".$txt_last_shipment_date_amnd.",".$txt_expiry_date_amend.",".$cbo_shipping_mode_amnd.",".$cbo_pay_term_amnd.",".$cbo_inco_term.",".$txt_inco_term_place.",".$txt_port_of_entry_amnd.",".$txt_port_of_loading_amnd.",".$txt_port_of_discharge_amnd.",".$txt_remarks_amnd.",".$txt_tenor_amnd.",".$txt_discount_clauses_amnd.",".$txt_claim_adjustment_amnd.",".$cbo_claim_adjust_by.",'".$attached_po_id."',0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";   */ 
		//echo "5**0**insert into com_export_lc_amendment (".$field_array_amnd.") values ".$data_array_amnd2;die;
		
		
		$isFirstamnd=is_duplicate_field("amendment_no", "com_export_lc_amendment", "amendment_no=0 and export_lc_id=$txt_system_id and status_active=1");
		
		$rID=sql_update("com_export_lc",$field_array_update,$data_array_update,"id","".$txt_system_id."",0);
		if($rID) $flag=1; else $flag=0;
		
		if($isFirstamnd==0)
		{
			$rID2=sql_insert("com_export_lc_amendment",$field_array,$data_array_amnd,0);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			} 
		}
		
		$rID3=sql_insert("com_export_lc_amendment",$field_array_amnd,$data_array_amnd2,1);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		//echo "10**insert into com_export_lc_amendment (".$field_array.") values ".$data_array_amnd."**".$flag;die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**0**".str_replace("'", '', $txt_system_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**".str_replace("'", '', $txt_system_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);   
				echo "0**0**".str_replace("'", '', $txt_system_id);
			}
			else
			{
				oci_rollback($con);
				echo "5**0**".str_replace("'", '', $txt_system_id);
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
		
		$last_amendment_id=return_field_value("max(id)","com_export_lc_amendment","export_lc_id=$txt_system_id and status_active=1");
		
		if($last_amendment_id!=str_replace("'", '', $update_id))
		{
			echo "14**1"; disconnect($con);
			die;
		}
		
		if (is_duplicate_field( "id", "com_export_lc_amendment", "amendment_no=$txt_amendment_no and export_lc_id=$txt_system_id and status_active=1 and id<>$update_id")==1)
		{
			echo "11**1"; disconnect($con);
			die;			
		}
		
		/*$attached_po_id=return_field_value("group_concat(wo_po_break_down_id)","com_export_lc_order_info","com_export_lc_id=$txt_system_id and status_active=1 and is_deleted=0 group by com_export_lc_id");*/
		
		$data_array=sql_select("select export_lc_no, lc_value, last_shipment_date, expiry_date, shipping_mode, inco_term, inco_term_place, port_of_entry, port_of_loading, port_of_discharge, pay_term, max_btb_limit, foreign_comn, local_comn, tolerance, remarks, tenor, discount_clauses,lien_date, claim_adjustment from com_export_lc where id=$txt_system_id");
		
		$lc_value = $data_array[0][csf('lc_value')];
		$export_lc_no = $data_array[0][csf('export_lc_no')];
		$claim_adjustment = $data_array[0][csf('claim_adjustment')];
		$tlc_value = $data_array[0][csf('lc_value')];
		
		/*if($prev_value_change_by==str_replace("'", '', $cbo_value_change_by))
		{
			if(str_replace("'", '', $cbo_value_change_by)==1) $lc_value=$lc_value-str_replace("'", '', $hide_amendment_value); 
			else if(str_replace("'", '', $cbo_value_change_by)==2) $lc_value=$lc_value+str_replace("'", '', $hide_amendment_value);
		}*/
		
		if (str_replace("'", '', $hide_value_change_by) == 1)
            $lc_value = $lc_value - str_replace("'", '', $hide_amendment_value);
        else if (str_replace("'", '', $hide_value_change_by) == 2)
            $lc_value = $lc_value + str_replace("'", '', $hide_amendment_value);
		
		
		if( str_replace("'", '', $cbo_value_change_by)==1 )
			$new_lc_value = $lc_value+str_replace("'", '', $txt_amendment_value);
		else if(str_replace("'", '', $cbo_value_change_by)==2 )
			$new_lc_value = $lc_value-str_replace("'", '', $txt_amendment_value);
		else
			$new_lc_value = $lc_value;
		
		if(str_replace("'", '', $hide_claim_adjust_by)==1) $claim_adjustment=$claim_adjustment-str_replace("'", '', $hide_claim_adjustment_amnd); 
		else if(str_replace("'", '', $hide_claim_adjust_by)==2) $claim_adjustment=$claim_adjustment+str_replace("'", '', $hide_claim_adjustment_amnd);

		if( str_replace("'", '', $cbo_claim_adjust_by)==1 )
			$new_claim_adjustment = $claim_adjustment+str_replace("'", '', $txt_claim_adjustment_amnd);
		else if(str_replace("'", '', $cbo_claim_adjust_by)==2 )
			$new_claim_adjustment = $claim_adjustment-str_replace("'", '', $txt_claim_adjustment_amnd);	
		
		$maximum_tolarence = 0; $minimum_tolarence = 0;
 		$maximum_tolarence = $new_contract_value+($new_contract_value*str_replace("'", '', $data_array[0][csf('tolerance')]))/100;
		$minimum_tolarence = $new_contract_value-($new_contract_value*str_replace("'", '', $data_array[0][csf('tolerance')]))/100;
 		
		$foreign_comn_value = 0;$local_comn_value = 0;
		$foreign_comn_value = ($new_lc_value*str_replace("'", '', $data_array[0][csf('foreign_comn')]))/100;
		$local_comn_value = ($new_lc_value*str_replace("'", '', $data_array[0][csf('local_comn')]))/100;
		
		$max_btb_limit_value = ($new_lc_value*str_replace("'", '', $data_array[0][csf('max_btb_limit')]))/100;
		
		
		//echo "11**".$new_lc_value."=".$prev_value_change_by."=".str_replace("'", '', $cbo_value_change_by)."=".$tlc_value."=".$lc_value;disconnect($con);die;
		//update export lc table
		$field_array_update="lc_value*maximum_tolarence*minimum_tolarence*last_shipment_date*expiry_date*shipping_mode*pay_term*inco_term*inco_term_place*port_of_entry* port_of_loading*port_of_discharge*max_btb_limit_value*foreign_comn_value*local_comn_value*remarks*discount_clauses*tenor*claim_adjustment*updated_by*update_date";
		
		$data_array_update=$new_lc_value."*".$maximum_tolarence."*".$minimum_tolarence."*".$txt_last_shipment_date_amnd."*".$txt_expiry_date_amend."*".$cbo_shipping_mode_amnd."*".$cbo_pay_term_amnd."*".$cbo_inco_term."*".$txt_inco_term_place."*".$txt_port_of_entry_amnd."*".$txt_port_of_loading_amnd."*".$txt_port_of_discharge_amnd."*".$max_btb_limit_value."*".$foreign_comn_value."*".$local_comn_value."*".$txt_remarks_amnd."*".$txt_discount_clauses_amnd."*".$txt_tenor_amnd."*'".$new_claim_adjustment."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		/*$rID=sql_update("com_export_lc",$field_array_update,$data_array_update,"id","".$txt_system_id."",0);
		if($rID) $flag=1; else $flag=0;*/
		
		$shipment_date=strtotime($data_array[0][csf('last_shipment_date')]);
		$shipment_date_amnd=strtotime(str_replace("'","",$txt_last_shipment_date_amnd));
		$expiry_date=strtotime($data_array[0][csf('expiry_date')]);
		$expiry_date_amnd=strtotime(str_replace("'","",$txt_expiry_date_amend));

		$field_array_amnd="amendment_date*lien_date*export_lc_id*export_lc_no*lc_value*amendment_value*value_change_by*claim_adjustment*claim_adjust_by*updated_by*update_date";
		$data_array_amnd=$txt_amendment_date."*".$txt_amed_lien_date."*".$txt_system_id."*'".$export_lc_no."'*'".$new_lc_value."'*".$txt_amendment_value."*".$cbo_value_change_by."*".$txt_claim_adjustment_amnd."*".$cbo_claim_adjust_by."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		if($shipment_date!=$shipment_date_amnd)
		{
			$field_array_amnd.="*last_shipment_date";
			$data_array_amnd.="*".$txt_last_shipment_date_amnd;
		}
		
		if($expiry_date!=$expiry_date_amnd)
		{
			$field_array_amnd.="*expiry_date";
			$data_array_amnd.="*".$txt_expiry_date_amend;
		}
		
		if($data_array[0][csf('shipping_mode')]!=str_replace("'","",$cbo_shipping_mode_amnd))
		{
			$field_array_amnd.="*shipping_mode";
			$data_array_amnd.="*".$cbo_shipping_mode_amnd;
		}
		
		if($data_array[0][csf('pay_term')]!=str_replace("'","",$cbo_pay_term_amnd))
		{
			$field_array_amnd.="*pay_term";
			$data_array_amnd.="*".$cbo_pay_term_amnd;
		}
		
		if($data_array[0][csf('inco_term')]!=str_replace("'","",$cbo_inco_term))
		{
			$field_array_amnd.="*inco_term";
			$data_array_amnd.="*".$cbo_inco_term;
		}
		
		if($data_array[0][csf('inco_term_place')]!=str_replace("'","",$txt_inco_term_place))
		{
			$field_array_amnd.="*inco_term_place";
			$data_array_amnd.="*".$txt_inco_term_place;
		}
		
		if($data_array[0][csf('port_of_entry')]!=str_replace("'","",$txt_port_of_entry_amnd))
		{
			$field_array_amnd.="*port_of_entry";
			$data_array_amnd.="*".$txt_port_of_entry_amnd;
		}
		
		if($data_array[0][csf('port_of_loading')]!=str_replace("'","",$txt_port_of_loading_amnd))
		{
			$field_array_amnd.="*port_of_loading";
			$data_array_amnd.="*".$txt_port_of_loading_amnd;
		}
		
		if($data_array[0][csf('port_of_discharge')]!=str_replace("'","",$txt_port_of_discharge_amnd))
		{
			$field_array_amnd.="*port_of_discharge";
			$data_array_amnd.="*".$txt_port_of_discharge_amnd;
		}
		
		if($data_array[0][csf('remarks')]!=str_replace("'","",$txt_remarks_amnd))
		{
			$field_array_amnd.="*remarks";
			$data_array_amnd2.="*".$txt_remarks_amnd;
		}
		
		if($data_array[0][csf('tenor')]!=str_replace("'","",$txt_tenor_amnd))
		{
			$field_array_amnd.="*tenor";
			$data_array_amnd.="*".$txt_tenor_amnd;
		}
		
		if($data_array[0][csf('discount_clauses')]!=str_replace("'","",$txt_discount_clauses_amnd))
		{
			$field_array_amnd.="*discount_clauses";
			$data_array_amnd.="*".$txt_discount_clauses_amnd;
		}
		
		/*$field_array="amendment_no*amendment_date*export_lc_id*lc_value*amendment_value*value_change_by*last_shipment_date*expiry_date*shipping_mode*pay_term* inco_term*inco_term_place*port_of_entry*port_of_loading*port_of_discharge*remarks*tenor*discount_clauses*claim_adjustment*claim_adjust_by*po_id*updated_by*update_date";	
		
		$data_array=$txt_amendment_no."*".$txt_amendment_date."*".$txt_system_id."*".$new_lc_value."*".$txt_amendment_value."*".$cbo_value_change_by."*".$txt_last_shipment_date_amnd."*".$txt_expiry_date_amend."*".$cbo_shipping_mode_amnd."*".$cbo_pay_term_amnd."*".$cbo_inco_term."*".$txt_inco_term_place."*".$txt_port_of_entry_amnd."*".$txt_port_of_loading_amnd."*".$txt_port_of_discharge_amnd."*".$txt_remarks_amnd."*".$txt_tenor_amnd."*".$txt_discount_clauses_amnd."*".$txt_claim_adjustment_amnd."*".$cbo_claim_adjust_by."*'".$attached_po_id."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";*/
		
		$rID=sql_update("com_export_lc",$field_array_update,$data_array_update,"id","".$txt_system_id."",0);
		if($rID) $flag=1; else $flag=0;

		$rID2=sql_update("com_export_lc_amendment",$field_array_amnd,$data_array_amnd,"id","".$update_id."",1);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		}

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**0**".str_replace("'", '', $txt_system_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**1**".str_replace("'", '', $txt_system_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "1**0**".str_replace("'", '', $txt_system_id);
			}
			else
			{
				oci_rollback($con);
				echo "6**1**".str_replace("'", '', $txt_system_id);
			}
		}
		disconnect($con);
		die;
	}
	
}

if ($action=="save_update_delete_lc_order_info")
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
		
		/*$last_amendment_id=return_field_value("max(id)","com_export_lc_amendment","export_lc_id=$txt_system_id");
		
		if($last_amendment_id!=str_replace("'", '', $update_id))
		{
			echo "14**1"; 
			die;
		}*/		
		
 		$field_array="id,com_export_lc_id,wo_po_break_down_id,attached_qnty,attached_rate,attached_value,fabric_description,category_no,hs_code,status_active,inserted_by,insert_date,lc_amendment_id";
		$id = return_next_id( "id", "com_export_lc_order_info", 1 );
		$currentattachedvalue = 0;
		for($j=1;$j<=$noRow;$j++)
		{ 	
			$hiddenwopobreakdownid="hiddenwopobreakdownid_".$j;
			$txtattachedqnty="txtattachedqnty_".$j;
			$hiddenunitprice="hiddenunitprice_".$j;
			$txtattachedvalue="txtattachedvalue_".$j;
			$cbopostatus="cbopostatus_".$j;
			$txtfabdescrip="txtfabdescrip_".$j;
			$txtcategory="txtcategory_".$j;
			$txthscode="txthscode_".$j;
			
			if($$hiddenwopobreakdownid!="")
			{
				if($data_array!="") $data_array.=",";
				
				$data_array.="(".$id.",".$txt_system_id.",".$$hiddenwopobreakdownid.",".$$txtattachedqnty.",".$$hiddenunitprice.",".$$txtattachedvalue.",".$$txtfabdescrip.",".$$txtcategory.",".$$txthscode.",".$$cbopostatus.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$update_id.")";
				$id = $id+1;
				$currentattachedval = str_replace("'","",$$txtattachedvalue);
				$currentattachedvalue += number_format($currentattachedval,2,'.','');
			}
		}
		//echo "insert into com_export_lc_order_info ($field_array) values".$data_array ;die;
		//print_r($data_array);die;
		
		$lc_value=return_field_value("lc_value","com_export_lc","id=".$txt_system_id);
        $pre_tot_attached =  sql_select("select sum(b.attached_value) as attached_value from com_export_lc a, com_export_lc_order_info b where a.id = b.com_export_lc_id and a.id=".$txt_system_id." and b.status_active = 1 and b.is_deleted = 0");

        $lc_value = number_format($lc_value,2,".","");
        $tot_attached = number_format($pre_tot_attached[0][csf("attached_value")],2,".","");
		if(number_format(($tot_attached + $currentattachedvalue),2,'.','') > number_format($lc_value,2,'.',''))
        {
            echo "11** Attached Value Exceeds LC Value ".number_format(($tot_attached + $currentattachedvalue),2,'.','')." = ".number_format($lc_value,2,'.','');disconnect($con);die;
        }
		
		$rID=sql_insert("com_export_lc_order_info",$field_array,$data_array,1);
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'", '', $txt_system_id)."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con); 
				echo "0**".str_replace("'", '', $txt_system_id)."**0";
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
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		/*$last_amendment_id=return_field_value("max(id)","com_export_lc_amendment","export_lc_id=$txt_system_id");
		
		if($last_amendment_id!=str_replace("'", '', $update_id))
		{
			echo "14**1"; 
			die;
		}*/	
			 
		 //update code here
		$field_array="id,com_export_lc_id,wo_po_break_down_id,attached_qnty,attached_rate,attached_value,fabric_description,category_no,hs_code,status_active,inserted_by,insert_date";
		$field_array_update="wo_po_break_down_id*attached_qnty*attached_rate*attached_value*fabric_description*category_no*hs_code*status_active*updated_by*update_date";
		
		$hiddenexportlcorderid=str_replace("'", '', $hiddenexportlcorderid);
		$id = return_next_id( "id", "com_export_lc_order_info", 1 );
		$currentattachedvalue = 0;
		for($j=1;$j<=$noRow;$j++)
		{ 	
			$hiddenwopobreakdownid="hiddenwopobreakdownid_".$j;
			$txtattachedqnty="txtattachedqnty_".$j;
			$hiddenunitprice="hiddenunitprice_".$j;
			$txtattachedvalue="txtattachedvalue_".$j;
			$cbopostatus="cbopostatus_".$j;
			$txtfabdescrip="txtfabdescrip_".$j;
			$txtcategory="txtcategory_".$j;
			$txthscode="txthscode_".$j;
			
			if($j==1)
			{
				if($hiddenexportlcorderid!="")
				{
					if(str_replace("'", '', $$cbopostatus)==0)
					{
						$invoice_no="";
						$po_id=$$hiddenwopobreakdownid;
						$sql_invoice="select a.invoice_no from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b where a.id=b.mst_id and a.lc_sc_id=$txt_system_id and a.is_lc=1 and b.po_breakdown_id=$po_id and b.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.invoice_no";
						$data=sql_select($sql_invoice);
						if(count($data)>0)
						{
							foreach($data as $row)
							{
								if($invoice_no=="") $invoice_no=$row[csf('invoice_no')]; else $invoice_no.=",\n".$row[csf('invoice_no')];	
							}
							
							echo "13**".$invoice_no."**1"; disconnect($con);
							die;	
						}
					}
					
					$data_array_update="".$$hiddenwopobreakdownid."*".$$txtattachedqnty."*".$$hiddenunitprice."*".$$txtattachedvalue."*".$$txtfabdescrip."*".$$txtcategory."*".$$txthscode."*".$$cbopostatus."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
					$currentattachedval = str_replace("'","",$$txtattachedvalue);
                    $currentattachedvalue += number_format($currentattachedval,2,'.','');
				}
				else
				{
					if($data_array!="") $data_array.=",";
					
					$data_array.="(".$id.",".$txt_system_id.",".$$hiddenwopobreakdownid.",".$$txtattachedqnty.",".$$hiddenunitprice.",".$$txtattachedvalue.",".$$txtfabdescrip.",".$$txtcategory.",".$$txthscode.",".$$cbopostatus.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$id = $id+1;
					$currentattachedval = str_replace("'","",$$txtattachedvalue);
                    $currentattachedvalue += number_format($currentattachedval,2,'.','');
				}
			}
			else
			{
				if($$hiddenwopobreakdownid!="")
				{
					if($data_array!="") $data_array.=",";
					
					$data_array.="(".$id.",".$txt_system_id.",".$$hiddenwopobreakdownid.",".$$txtattachedqnty.",".$$hiddenunitprice.",".$$txtattachedvalue.",".$$txtfabdescrip.",".$$txtcategory.",".$$txthscode.",".$$cbopostatus.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$id = $id+1;
					$currentattachedval = str_replace("'","",$$txtattachedvalue);
                    $currentattachedvalue += number_format($currentattachedval,2,'.','');
				}
			}
		}
		
		//echo "insert into com_export_lc_order_info (".$field_array.") values".$data_array;die;
		
		$lc_value=return_field_value("lc_value","com_export_lc","id=".$txt_system_id);
        $without_update_dtls_cond="";
        if($hiddenexportlcorderid != ""){
            $without_update_dtls_cond = " and b.id not in ($hiddenexportlcorderid)";
        }
        $pre_tot_attached =  sql_select("select sum(b.attached_value) as attached_value from com_export_lc a, com_export_lc_order_info b where a.id = b.com_export_lc_id and a.id=".$txt_system_id." $without_update_dtls_cond and b.status_active = 1 and b.is_deleted = 0");

        $lc_value = number_format($lc_value,2,".","");
        $tot_attached = number_format($pre_tot_attached[0][csf("attached_value")],2,".","");
		
        if(number_format(($tot_attached + $currentattachedvalue),2,'.','')  > number_format($lc_value,2,'.','') )
        {
            echo "11** Attached Value Exceeds LC Value ".number_format(($tot_attached + $currentattachedvalue),2,'.','')." = ".number_format($lc_value,2,'.','');disconnect($con);die;
        }
		
		
		$flag=1;
		
		if($data_array!="")
		{
			$rID2=sql_insert("com_export_lc_order_info",$field_array,$data_array,0);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			} 
		}
		
		if($data_array_update!="")
		{
			$rID=sql_update("com_export_lc_order_info",$field_array_update,$data_array_update,"id","".$hiddenexportlcorderid."",1);
			if($flag==1) 
			{
				if($rID) $flag=1; else $flag=0;
			} 
		}
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $txt_system_id)."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);   
				echo "1**".str_replace("'", '', $txt_system_id)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "6**0**1";
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="export_lc_amendment_letter")
{
	//echo $data; die;
	$data=explode("**",$data);
	//export lc amendment-------------
	if($data[0]==5)
	{
		//echo $data[0]; die;
		$data_array=sql_select("select id, export_lc_system_id, export_lc_no, lc_date, beneficiary_name, buyer_name, replacement_lc, lien_bank, lien_date, lc_value, currency_name, last_shipment_date, expiry_date, shipping_mode, tolerance, pay_term, inco_term, inco_term_place, port_of_entry, port_of_loading, port_of_discharge, internal_file_no, remarks, tenor, discount_clauses, claim_adjustment from com_export_lc where id='$data'");
		foreach ($data_array as $row)
		{
			$internal_file_no	= $row[csf("internal_file_no")];
			$lien_date			= $row[csf("lien_date")];
			$lien_bank			= $row[csf("lien_bank")];
			$lc_no				= $row[csf("export_lc_no")];
			$lc_date			= change_date_format($row[csf("lc_date")]);
			
			//echo "document.getElementById('txt_system_id').value 			= '".$row[csf("id")]."';\n";
			echo "document.getElementById('txt_internal_file_no').value		= '".$row[csf("internal_file_no")]."';\n";
			//echo "document.getElementById('txt_export_lc_no').value 		= '".$row[csf("export_lc_no")]."';\n";
			//echo "document.getElementById('cbo_beneficiary_name').value 	= '".$row[csf("beneficiary_name")]."';\n";
			//echo "document.getElementById('cbo_buyer_name').value			= '".$row[csf("buyer_name")]."';\n";
			echo "document.getElementById('txt_lc_value').value 			= '".$row[csf("lc_value")]."';\n";
			//echo "document.getElementById('cbo_currency_name').value 		= '".$row[csf("currency_name")]."';\n";
			echo "document.getElementById('cbo_replacement_lc').value 		= '".$row[csf("replacement_lc")]."';\n";		
			echo "document.getElementById('cbo_lien_bank').value 			= '".$row[csf("lien_bank")]."';\n";
			echo "document.getElementById('txt_lien_date').value 			= '".change_date_format($row[csf("lien_date")])."';\n";
			
			echo "document.getElementById('txt_amendment_no').value 			= '';\n";
			echo "document.getElementById('update_id').value 					= '';\n";
			echo "document.getElementById('txt_amendment_date').value 			= '';\n";
			echo "document.getElementById('txt_amendment_value').value 			= '';\n";
			echo "document.getElementById('hide_amendment_value').value 		= '';\n";
			echo "document.getElementById('cbo_value_change_by').value 			= '0';\n";
			echo "document.getElementById('hide_value_change_by').value 		= '';\n";
		}
		
		
		
		
		
		$data_array=sql_select("select id, contract_no, contract_date, lien_bank, lien_date, contract_value, internal_file_no from com_sales_contract where id=".$data[1]."");
		foreach ($data_array as $row)
		{
			$internal_file_no	= $row[csf("internal_file_no")];
			$contract_no		= $row[csf("contract_no")];
			$contract_value		= $row[csf("contract_value")];
			$contract_date		= change_date_format($row[csf("contract_date")]);
			$lien_bank			= $row[csf("lien_bank")];
			$lien_date			= $row[csf("lien_date")];
		}
		
		$data_array1=sql_select("select wm.total_set_qnty as ratio, ci.attached_qnty from wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci where wb.job_no_mst = wm.job_no and wb.id=ci.wo_po_break_down_id and ci.com_sales_contract_id=".$data[1]." and ci.status_active = '1' and ci.is_deleted = '0' order by ci.id");
		foreach($data_array1 as $row1)
		{
			$order_qnty_in_pcs=$row1[csf('attached_qnty')]*$row1[csf('ratio')];
			$total_attach_qty+=$order_qnty_in_pcs;
		}
	}

	//bank information retriving here
	$data_array1=sql_select("select id, bank_name, branch_name, contact_person, address from lib_bank where id=".$lien_bank."");
	foreach ($data_array1 as $row1)
	{ 
		$bank_name		= $row1[csf("bank_name")];
		$branch_name	= $row1[csf("branch_name")];
		$contact_person	= $row1[csf("contact_person")];
		$address		= $row1[csf("address")];
	}
	
	//letter body is retriving here
	$data_array2=sql_select("select letter_body from dynamic_letter where letter_type=".$data[0]."");
	foreach ($data_array2 as $row2)
	{ 
		$letter_body = $row2[csf("letter_body")];
	}
	
	$raw_data=str_replace("INTERNALFILENO",$internal_file_no,$letter_body);
	$raw_data=str_replace("LIENDATE",$lien_date,$raw_data);
	$raw_data=str_replace("CONTACTPERSON",$contact_person,$raw_data);
	$raw_data=str_replace("BANKNAME",$bank_name,$raw_data);
	$raw_data=str_replace("BRANCHNAME",$branch_name,$raw_data);
	$raw_data=str_replace("ADDRESS",$address,$raw_data);
	$raw_data=str_replace("CONTACTNO",$lc_no,$raw_data);
	$raw_data=str_replace("CONTACTDATE",$lc_date,$raw_data);
	$raw_data=str_replace("CONTACTVALUE",$lc_value,$raw_data);
	$raw_data=str_replace("TOTALATTACHQTY",$total_attach_qty,$raw_data);
	
	echo $raw_data;
	exit();
}

if ($action=="request_for_insert_amendment")
{
	$data=explode("**",$data);
	//export lc amendment-------------
	if($data[0]==5)
	{
		$data_array=sql_select("select id, export_lc_system_id, export_lc_no, lc_date, beneficiary_name, buyer_name, replacement_lc, lien_bank, lien_date, lc_value, currency_name, last_shipment_date, expiry_date, shipping_mode, tolerance, pay_term, inco_term, inco_term_place, port_of_entry, port_of_loading, port_of_discharge, internal_file_no, remarks, tenor, discount_clauses, claim_adjustment from com_export_lc where id=$data[1]");
		$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
		$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
		foreach ($data_array as $row)
		{
			$ref				= $company_arr[$row[csf("beneficiary_name")]];
			$lien_date			= $row[csf("lien_date")];
			$lien_bank 			= strtoupper($row[csf("lien_bank")]);
			$lc_no				= $row[csf("export_lc_no")];
			$lc_value			= $row[csf("lc_value")];
			$lc_date			= change_date_format($row[csf("lc_date")]);
			$currency_name 		= $currency[$row[csf("currency_name")]];
			$currency_sign 		= $currency_sign_arr[$row[csf("currency_name")]];
		}
		$data_array1 = sql_select("select id, bank_name, branch_name, address from lib_bank where id='$lien_bank'");
			foreach ($data_array1 as $row1) 
			{
				$bank_name = ucwords($row1[csf("bank_name")]);
				$branch_name = ucwords($row1[csf("branch_name")]);
				$address = ucwords($row1[csf("address")]);
			}
			
	}
	?>

	<style type="text/css">
        .a4size {
           width: 21cm;
           height: 26.7cm;
           font-family: Bookman Old Style;
        }
        @media print {
        .a4size{ font-family: Bookman Old Style;font-size: 18px;margin: 80px 100PX 54px 25px;
            }
        size: A4 portrait;
        }
        .parent {
          display: flex;
          flex-direction:row;
          margin-left: 28px;
        }

    </style>
	<div class="a4size">
        <table width="794" cellpadding="0" cellspacing="0" border="0" >
            <div style=" height:100;"></div>
            <div class="parent" >
			Ref No.: <? echo $ref."/COM/".$lc_no?>
			</br>
			Dated:  
                <? //echo date('d M Y',strtotime($lc_date));?>
				<? echo date('d-m-Y');?>
            </div>
            <br>
            <tr>
                <td width="25"></td>
                <td width="650" align="left"></td>
                <td width="25" ></td>
            </tr>
            <tr>
                <td width="25" ></td>
                <td width="650" align="left">
                <?
                    echo "The Sr. Vice President.  <br>";
                    echo $bank_name."<br>";
                    echo $branch_name." Branch.<br>";
                    echo $address;
                ?>.
                </td>
                <td width="25" ></td>
            </tr>
            <tr>
                <td colspan="3" height="100"></td>
            </tr>

            <tr>
                <td width="25"></td>
                <td width="650" align="justify">
                Sub:  Request for Insert Amendment no.1 of Export L/C which No. <? echo $lc_no." dated ".$lc_date." for Amend Value ".$currency_name." ".$currency_sign."".$lc_value." Along with Total Export L/C value USD."; ?> 
               </td>
                <td width="25" ></td>
            </tr>
            <tr>
                <td colspan="3" height="75"></td>
            </tr>
            <tr>
                <td width="25" ></td>
                <td width="650" align="left"> Dear Sir, </td>
                <td width="25" height="50"></td>
            </tr>
            <tr>
                <td width="25" ></td>
                <td width="650" align="justify">
				With reference to the above subject we are requesting you to Insert Amendment no.1 of Export L/C which No.
                <? echo $lc_no." dated:  ".$lc_date." for Amend Value ".$currency_name." ".$currency_sign."".$lc_value." Along with Total Export L/C value USD."; ?> 
                </td>
                <td width="25" ></td>
            </tr>
        </table>
        <table width="794" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td colspan="3" height="150"></td>
            </tr>

            <tr>
                <td colspan="3" height="100"></td>
            </tr>
            <tr>
                <td width="25" ></td>
                <td colspan="2" height="50">
                Thanks & Regards,<br>
                Very truly yours, 
                </td>
            </tr>
            <tr>
                <td width="25"  height="150"></td>
                <td colspan="2">
                AUTHRIZED SIGNATURE
                </td>
            </tr>
        </table>
    </div>
	<?
	exit();
}

if ($action=="request_for_insert_amendment2")
{
	$data=explode("**",$data);
	//export lc amendment-------------
	if($data[0]==5)
	{
		// $data_array=sql_select("select id, export_lc_system_id, export_lc_no, lc_date, beneficiary_name, buyer_name, replacement_lc, lien_bank, lien_date, lc_value, currency_name, last_shipment_date, expiry_date, shipping_mode, tolerance, pay_term, inco_term, inco_term_place, port_of_entry, port_of_loading, port_of_discharge, internal_file_no, remarks, tenor, discount_clauses, claim_adjustment from com_export_lc where id=$data[1]");

		$data_array=sql_select("SELECT a.id,a.beneficiary_name, a.export_lc_no, a.lc_date, a.lien_bank, a.lien_date, a.lc_value, a.export_lc_system_id, a.currency_name,a.buyer_name , a.last_shipment_date, a.expiry_date, b.amendment_no, b.amendment_value, b.amendment_date, b.value_change_by
        from com_export_lc a, com_export_lc_amendment b
        where a.id='$data[1]' and a.id=b.export_lc_id and b.id='$data[2]' and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0");
		$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
		$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
		foreach ($data_array as $row)
		{
			$ref				= $company_arr[$row[csf("beneficiary_name")]];
			$lien_date			= $row[csf("lien_date")];
			$lien_bank 			= strtoupper($row[csf("lien_bank")]);
			$lc_no				= $row[csf("export_lc_no")];
			$lc_value			= number_format($row[csf("lc_value")],2);
			$lc_date			= change_date_format($row[csf("lc_date")]);
			$currency_name 		= $currency[$row[csf("currency_name")]];
			$currency_sign 		= $currency_sign_arr[$row[csf("currency_name")]];

			$amnd_no			= $row[csf("amendment_no")];
			$amnd_value			= number_format($row[csf("amendment_value")]);
			if($row[csf("value_change_by")]!=0 || $row[csf("value_change_by")]!=''){$value_change_by = $increase_decrease[$row[csf("value_change_by")]];}
			

		}
		$data_array1 = sql_select("select id, bank_name, branch_name, address from lib_bank where id='$lien_bank'");
			foreach ($data_array1 as $row1) 
			{
				$bank_name = ucwords($row1[csf("bank_name")]);
				$branch_name = ucwords($row1[csf("branch_name")]);
				$address = ucwords($row1[csf("address")]);
			}
			
	}
	?>

	<style type="text/css">
        .a4size {
           width: 21cm;
           height: 26.7cm;
           font-family: Bookman Old Style;
		   /* text-transform: uppercase; */
        }
        @media print {
        .a4size{ font-family: Bookman Old Style;font-size: 18px;margin: 80px 100PX 54px 25px;
            }
        size: A4 portrait;
        }
        .parent {
          display: flex;
          flex-direction:row;
          margin-left: 28px;
        }

    </style>
	<div class="a4size">
        <table width="794" cellpadding="0" cellspacing="0" border="0" >
            <div style=" height:100;"></div>
            <div class="parent" >
			<strong>Date: <? echo date('M d, Y');?> 
			</br>
			Ref No.: <? //echo $ref."/COM/".$lc_no?></strong>
            </div>
            <br>
            <tr>
                <td width="25"></td>
                <td width="650" align="left"></td>
                <td width="25" ></td>
            </tr>
            <tr>
                <td width="25" ></td>
                <td width="650" align="left"><strong>
                <?
                    echo "To <br>";
                    echo "The Manager   <br>";
                    echo $bank_name."<br>";
                    echo $branch_name." Branch.<br>";
                    echo $address;
                ?>.
				</strong>
                </td>
                <td width="25" ></td>
            </tr>
            <tr>
                <td colspan="3" height="50"></td>
            </tr>

            <tr>
                <td width="25"></td>
                <td width="650" align="justify">
                <strong>Sub: Request for Lien  of Export L/C No. <? echo $lc_no;?> ( <?echo $amnd_no;?> ) DT.<? echo $lc_date." ".$value_change_by;?> VALUE BY <? echo $currency_name."".$amnd_value;?> to make total value <? echo $currency_name." ".$lc_value;?> for opening back to back L/C.</strong>
               </td>
                <td width="25" ></td>
            </tr>
            <tr>
                <td colspan="3" height="30"></td>
            </tr>
            <tr>
                <td width="25" ></td>
                <td width="650" align="left"> Dear Sir, </td>
                <td width="25" height="50"></td>
            </tr>
			<tr>
                <td colspan="3" height="20"></td>
            </tr>
            <tr>
                <td width="25" ></td>
                <td width="650" align="justify">
				With reference to the above, enclosed please find herewith original <strong>Export L/C
				<? echo $lc_no;?> ( <?echo $amnd_no;?> ) DT.<? echo $lc_date;?> increase  value by <? echo $currency_name." ".$amnd_value;?> total value <? echo $currency_name." ".$lc_value;?> for opening back to back L/CS.</strong>

                </td>
                <td width="25" ></td>
			</tr>
			<tr>
                <td colspan="3" height="50"></td>
            </tr>
            <tr>
                <td width="25" ></td>
                <td width="650" align="justify">
				We would very much appreciate for lien same with your counter and allow us to open B/B L/CS and document negotiation. 
                </td>
                <td width="25" ></td>
            </tr>
			<tr>
                <td colspan="3" height="50"></td>
            </tr>
            <tr>
                <td width="25" ></td>
                <td width="650" align="justify">
				Your nice co-operation on this regard is solicited. 
                </td>
                <td width="25" ></td>
            </tr>
			<tr>
                <td colspan="3" height="70"></td>
            </tr>
			<tr>
                <td width="25" ></td>
                <td width="650" align="justify">
				Thanking you
                </td>
                <td width="25" ></td>
            </tr>
			<tr>
                <td colspan="3" height="20"></td>
            </tr>
			<tr>
                <td width="25" ></td>
                <td width="650" align="justify">
				SINCERELY YOURS
                </td>
                <td width="25" ></td>
            </tr>
        </table>
    </div>
	<?
	exit();
}

?>


 