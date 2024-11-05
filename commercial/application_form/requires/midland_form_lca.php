<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create for MIDLAND LCA Form
Functionality	:
JS Functions	:
Created by		:	Safa
Creation date 	: 	12-07-2023
Updated by 		: 	
Update date		: 	
QC Performed BY	:
QC Date			:
Comments		:
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="btb_application_form")
{

	 $sql = "SELECT	IMPORTER_ID,ISSUING_BANK_ID,LC_VALUE,CURRENCY_ID,TENOR,SUPPLIER_ID,ITEM_CATEGORY_ID,PORT_OF_LOADING,PORT_OF_DISCHARGE,DELIVERY_MODE_ID,LAST_SHIPMENT_DATE,LC_EXPIRY_DATE,INCO_TERM_PLACE,DOC_PRESENTATION_DAYS,ORIGIN,INCO_TERM_ID,INSURANCE_COMPANY_NAME,COVER_NOTE_NO,COVER_NOTE_DATE,PAYTERM_ID,TOLERANCE,LCA_NO,LC_NUMBER,LC_DATE,BTB_SYSTEM_ID,PI_ID,PARTIAL_SHIPMENT,LC_TYPE_ID,TRANSHIPMENT FROM COM_BTB_LC_MASTER_DETAILS WHERE ID='$data'";
	 // echo $sql;
	$data_array=sql_select($sql);

    $is_lc_sc_sql=sql_select("SELECT lc_sc_id, is_lc_sc from com_btb_export_lc_attachment where import_mst_id=$data and is_deleted=0 and status_active=1");
	foreach($is_lc_sc_sql as $row)
	{
		if($row[csf('is_lc_sc')]==0){
			$sql_lc=sql_select("SELECT id,export_lc_no,lc_date FROM com_export_lc  where id=".$row[csf('lc_sc_id')]."");
			foreach($sql_lc as $lc_row)
			{
				//$export_lc_sc_no_arr[$lc_row[csf("id")]]=$lc_row[csf("export_lc_no")];
				$lc_sc_no =$lc_row[csf("export_lc_no")];
				$lc_sc_date =$lc_row[csf("lc_date")];
			
			}
		}
		else{
			$sql_sc=sql_select("SELECT id,contract_no,contract_date FROM com_sales_contract where id=".$row[csf('lc_sc_id')]."");
			foreach($sql_sc as $sc_row)
			{
				//$export_lc_sc_no_arr[$sc_row[csf("id")]]=$sc_row[csf("contract_no")];
				$lc_sc_no=$sc_row[csf("contract_no")];
				$lc_sc_date=$sc_row[csf("contract_date")];
			}
		}
		$lc_sc_id_arr[]=$row[csf('lc_sc_id')];
	}


	$order_pcs_qty = return_field_value("sum(b.order_quantity) as order_quantity","com_export_lc_order_info a,wo_po_color_size_breakdown b","b.po_break_down_id=a.wo_po_break_down_id and a.com_export_lc_id in(".implode(',',$lc_sc_id_arr).")","order_quantity");

    //--------------lib
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
	$company_name = return_field_value("company_name","lib_company","id=".$data_array[0]["IMPORTER_ID"],"company_name");
    $irc_no = return_field_value("irc_no","lib_company","id=".$data_array[0]["IMPORTER_ID"],"irc_no");
    $company_irc_expiry_date = return_field_value("irc_expiry_date","lib_company","id=".$data_array[0]["IMPORTER_ID"],"irc_expiry_date");
	if($company_irc_expiry_date!="")
	{
		if(date("m",strtotime($company_irc_expiry_date))>6)
		{
			$irc_year_increment=date("Y",strtotime($company_irc_expiry_date))+1;
			$irc_year=date("Y",strtotime($company_irc_expiry_date))."-".$irc_year_increment;
		}
		else
		{
			$irc_year_decrement=date("Y",strtotime($company_irc_expiry_date))-1;
			$irc_year=$irc_year_decrement."-".date("Y",strtotime($company_irc_expiry_date));
		}
	}

	$country_array = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");
	//echo "select id,plot_no,road_no,block_no,country_id,city,zip_code,irc_no,tin_number,vat_number,bang_bank_reg_no from lib_company where id = ".$data_array[0][csf('importer_id')]."";
	$address = sql_select("SELECT ID,COMPANY_NAME,PLOT_NO,LEVEL_NO,ROAD_NO,BLOCK_NO,COUNTRY_ID,CITY,ZIP_CODE,IRC_NO,TIN_NUMBER,VAT_NUMBER,BANG_BANK_REG_NO,BUSINESS_NATURE,EMAIL,CONTACT_NO FROM LIB_COMPANY WHERE ID = ".$data_array[0]['IMPORTER_ID']."");

	foreach($address as $row){
		$company_add[$row['ID']]['COMPANY_NAME'] = $row['COMPANY_NAME'];
		$company_add[$row['ID']]['EMAIL'] = $row['EMAIL'];
		$company_add[$row['ID']]['CONTACT_NO'] = $row['CONTACT_NO'];
		$company_add[$row['ID']]['PLOT_NO'] = $row['PLOT_NO'];
		$company_add[$row['ID']]['LEVEL_NO'] = $row['LEVEL_NO'];
		$company_add[$row['ID']]['ROAD_NO'] = $row['ROAD_NO'];
		$company_add[$row['ID']]['BLOCK_NO'] = $row['BLOCK_NO'];
		$company_add[$row['ID']]['COUNTRY_ID'] = $row['COUNTRY_ID'];
		$company_add[$row['ID']]['CITY'] = $row['CITY'];
		$company_add[$row['ID']]['ZIP_CODE'] = $row['ZIP_CODE'];
		$company_add[$row['ID']]['IRC_NO'] = $row['IRC_NO'];
		$company_add[$row['ID']]['TIN_NUMBER'] = $row['TIN_NUMBER'];
		$company_add[$row['ID']]['VAT_NUMBER'] = $row['VAT_NUMBER'];
		$company_add[$row['ID']]['BANG_BANK_REG_NO'] = $row['BANG_BANK_REG_NO'];
	}
	//print_r($company_add);
	$city = $company_add[$data_array[0]['IMPORTER_ID']]['CITY'];
	$country_id = $company_add[$data_array[0]['IMPORTER_ID']]['COUNTRY_ID'];
	$contact_no = $company_add[$data_array[0]['IMPORTER_ID']]['CONTACT_NO'];
	$email = $company_add[$data_array[0]['IMPORTER_ID']]['EMAIL'];

	if($city!="")  $comany_details.= "<br>".$city.", ";
	if($country_id!="")  $comany_details.="".$country_array[$country_id].".";
	if($contact_no!="")  $comany_details.="<br>Telephone: ".$contact_no.".";
	if($email!="")  $comany_details.="<br>E-MAIL: ".$email.".";
	
    $business_nature=explode(',',$com_address[0][csf('business_nature')]);
	foreach($business_nature as $row)
	{
		$business_nature_info=$business_nature_arr[$row].',';
	}

	$branch = return_field_value("branch_name","lib_bank","id=".$data_array[0][csf("issuing_bank_id")],"branch_name");

	$bank_address = return_field_value("ADDRESS","lib_bank","id=".$data_array[0][csf("issuing_bank_id")],"ADDRESS");
	$currency_sign = $currency_sign_arr[$data_array[0][csf("currency_id")]];
	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$data_array[0][csf("supplier_id")],"supplier_name");
	$supplier_add = return_field_value("address_1","lib_supplier","id=".$data_array[0][csf("supplier_id")],"address_1");

	$pi_number_arr=return_library_array( "SELECT id, pi_number from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','pi_number');
	
	$hs_code_arr=return_library_array( "SELECT id, HS_CODE from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','HS_CODE');
   
	$pi_date = return_field_value("pi_date","com_pi_master_details"," status_active = 1 AND is_deleted = 0 AND id=".$data_array[0][csf("pi_id")],"pi_date");

    //echo $total_pi=count($pi_number_arr);
    
	$pi_numbers;
	if(count($pi_number_arr)>1){
		foreach($pi_number_arr as $row){
			$pi_numbers .= $row." ,";
		}
		$pi_date='';
	}
	else
	{
		$pi_numbers = $pi_number_arr[$data_array[0][csf("pi_id")]];
		$pi_date=date('d.m.Y',strtotime($pi_date));
	}

	if($data_array[0][csf("last_shipment_date")]!=''){
		$last_shipment_date=date('d.m.Y',strtotime($data_array[0][csf("last_shipment_date")]));
	}
	else
	{
		$last_shipment_date='';
	}


	if($data_array[0][csf("lc_expiry_date")]!=''){
		$lc_expiry_date=date('d.m.Y',strtotime($data_array[0][csf("lc_expiry_date")]));
	}
	else
	{
		$lc_expiry_date='';
	}
	
	if(count($hs_code_arr)>1){
		foreach($hs_code_arr as $row){
			$hs_code .= $row.",";
		}
	}
	else
	{
		$hs_code = $hs_code_arr[$data_array[0][csf("pi_id")]];
	}
	
	$origin = return_field_value("country_name"," lib_country","id=".$data_array[0][csf("origin")],"country_name");

	$inco_term_id=$incoterm[$data_array[0][csf("inco_term_id")]];

	if($data_array[0][csf("cover_note_date")]!=''){
		$cover_note_date=date('d.m.Y',strtotime($data_array[0][csf("cover_note_date")]));
	}
	else
	{
		$cover_note_date='';
	}

	if($data_array[0][csf("payterm_id")]==1){
		$pay_term_cond = $pay_term[$data_array[0][csf("payterm_id")]];
	}else{
		$pay_term_cond = $data_array[0][csf("tenor")]. "Day's";
	}
	if($data_array[0][csf("doc_presentation_days")] == ""){
		$doc_presentation_days = "";
	}else{
		$doc_presentation_days = $data_array[0][csf("doc_presentation_days")]."Days";
    }
	?>

    <style>
		body{
			width:8.5in;
			
			padding:0;
			margin:0;
		}
        .stamp{
            border: 1px solid; 
            height:60px; 
            width:60px; 
            margin:15px; 
            padding:20px; 
            display: flex;
            flex-direction: column; 
            justify-content: center;
        }
    </style>


    <body>
        <div style="margin-left: 35px;">
			MDB (Fx)-
			<table width='800px' cellspacing="0" cellpadding="0" border="1" >
				<tr>
					<td colspan="3" width = "200px" valign="top" style="border-right: none; border-bottom: none; " align="left">
						To <br>
						The Head of Branch <br>
						<b>Midland Bank Limited</b> <br>
						<u><?echo $branch.", ".$bank_address;?></u>
					</td>
					<td colspan="6" width="400px" style="border-right: none; border-bottom: none; border-left: none; "></td>
					<td colspan="3" width = "120px" style="border-right: none; border-bottom: none; border-left: none;" align="right"><p class="stamp">STAMP</p></td>
				</tr>
				<tr align="center">
					<td colspan="12" style=" border-bottom: none; border-right: none;  border-top: none;"><b>APPLICATION AND AGREEMENT FOR IRREVOCABLE DOCUMENTARY CREDIT</b></td>
				</tr>
				<tr>
					<td colspan="12" style="border-bottom: none; border-right: none;  border-top: none;"><br><br> <p>Please open a irrevocable documentary credit through your correspondent confirmed/ Without confirm by <br> <input type="checkbox" disabled> Mail/Airmail <input type="checkbox" disabled> SWIFT <br> <input type="checkbox" disabled> Telex in brief, details by Mail / Airmail as follows :  </p>      
					</td>
				</tr>
				<tr>
					<td colspan="12">Beneficiary's Name and Address: <?echo $supplier_name;?> <br> <?echo $supplier_add;?></td>
				</tr>
				<tr>
					<td colspan="12">Opener's Name and Address: <?echo $company_add[$data_array[0]['IMPORTER_ID']]['COMPANY_NAME'];?>
					<? echo  $comany_details;?>
					</td> 
				</tr>
				<tr>
					<td colspan="2" width = "150px"  valign="top"> Draft Amount: <? echo number_format($data_array[0]["LC_VALUE"],2);?>  </td>
					<td colspan="3" width = "220px"  valign="top">In Words: <? echo number_to_words(def_number_format($data_array[0]["LC_VALUE"],2,""),"USD", "CENTS");?> </td>
					<td colspan="2" width = "150px"  valign="top"> 
					<input type="checkbox" value="<? echo $data_array[0]["PAYTERM_ID"]; ?>" <? if($data_array[0]["PAYTERM_ID"]==1) { ?> checked="checked" <? } ?> /> At Sight <br>

					<input type="checkbox" value="<? echo $data_array[0]["PAYTERM_ID"]; ?>" <? if($data_array[0]["PAYTERM_ID"]==2) { ?> checked="checked" <? } ?> /> days DA/DP
					</td>
					<td colspan="3" width = "200px" valign="top">
					<input type="checkbox" value="<? echo $data_array[0]["PAYTERM_ID"]; ?>" <? if($data_array[0]["INCO_TERM_ID"]==2) { ?> checked="checked" <? } ?> /> CFR &nbsp; &nbsp;
					<input type="checkbox" value="<? echo $data_array[0]["PAYTERM_ID"]; ?>" <? if($data_array[0]["INCO_TERM_ID"]==1) { ?> checked="checked" <? } ?> /> FOB <br>
					<input type="checkbox" value="<? echo $data_array[0]["PAYTERM_ID"]; ?>" <? if($data_array[0]["INCO_TERM_ID"]==3) { ?> checked="checked" <? } ?> /> CIF
					</td>
					<td colspan="2" width = "80px" valign="top"> &nbsp; Drawn On <br>
					<input type="checkbox" disabled> You <br>
					<input type="checkbox" disabled> Us
					</td>
				</tr>
				<tr>
					<td colspan="12"> Description of commodities : <br><br>
					As per Contract/ Proforma Invoice/Indent Number <? echo  $pi_numbers;?> dated of ....................................... 
					of M/S .........................................................................................................................................................
					<br> <br>
					<b>H.S. CODE NO : </b>
					</td> 
				</tr>
				<tr>
					<td colspan="2" width = "200px"  valign="top"> Country of Origin :  </td>
					<td colspan="3" width = "150px"  valign="top">LCAF NO :  </td>
					<td colspan="2" width = "150px"  valign="top">IRC No : </td>
					<td colspan="3" width = "200px" valign="top"> TIN : </td>
					<td colspan="2" width = "100px" valign="top"> VAT : </td>
				</tr>
				<tr>
					<td colspan="2" width = "200px"  valign="top"> Indentor's IRC No :  </td>
					<td colspan="10" width = "600px"  valign="top">Bangladesh Bank Permission No :  </td>
				</tr>
				
				<tr>
					<td colspan="12"> <br> Shipment from .......................................... to ................................................................................. by Sea / Air / Road etc.
					<br>	<br>
					<b>DOCUMENTS REQUIRED : </b> <br>
					1. Commercial Invoice in six copies 2. Packing list in triplicate. 3. Certificate of origin issued by Chamber of Commerce. <br>
					4. Full set of clean shipped on Board Ocean Bills of lading /Air way bill/Truck Receipt/Delivery Challan drawn or endorsed to the order of Midland Bank Limited. 
					</td> 
				</tr>
				<tr>
					<td colspan="6" width = "400px"  valign="top"> Last date od Shipment :  </td>
					<td colspan="6" width = "400px"  valign="top">Last date od Negotiation :  </td>
				</tr>
				<tr>
					<td colspan="6" width = "400px"  valign="top"> Insurance Cover Note/ Policy No : <br> </td>
					<td colspan="6" width = "400px"  valign="top"> Insurance Company Name & Address : <br>........................................................................ </td>
				</tr>
				<tr>
					<td colspan="6" width = "400px"  valign="top">Partshipment &nbsp;&nbsp; <input type="checkbox" disabled> &nbsp;&nbsp; Allowed &nbsp;&nbsp;<input type="checkbox" disabled> &nbsp;&nbsp; Prohibited  </td>
					<td colspan="6" width = "400px"  valign="top">Transhipment &nbsp;&nbsp; <input type="checkbox" disabled> &nbsp;&nbsp; Allowed &nbsp;&nbsp;<input type="checkbox" disabled>&nbsp;&nbsp; Prohibited  </td>
				</tr>
				<tr>
					<td colspan="12"> Other terms and conditions , if any : <br>
					1. Foreign Bank charges are on account of benificiary/applicant
					<br>
					2. <br>
					3. <br>
					4. <br>
					This credit is subject to Uniform Customs & Practice for Documentary Credits (2007 Rev.) ICC Pub -600.
					</td> 
				</tr>
				<tr>
					<td colspan="12">  In consideration of your opening L/C the undersigned unconditionally  agree (s) to the terms and condtions as stated overleaf.
					<br><br>
					<p style="text-align: right; padding-right:5px;"> ..................................... <br>Signature of Applicant <br> Account NO.</p>
					</td> 
				</tr>
			</table>
			<br>
			<br>
			<div align="center">
			<span>FOR BANK'S USE</span>
			</div>
			<br>
			<table width='800px' cellspacing="0" cellpadding="0" border="1" >
				<tr>
					<td colspan="6" width = "400px"  valign="top"> LC No. : <?echo $lc_sc_no;?> </td>
					<td colspan="6" width = "400px"  valign="top">Date of Opening : <? echo change_date_format($lc_sc_date);?> </td>
				</tr>
				<tr>
					<td colspan="6" width = "400px"  valign="top"> Advising Bnak :  </td>
					<td colspan="6" width = "400px"  valign="top">Approving Authority :  </td>
				</tr>
				<tr>
					<td colspan="6" width = "400px"  valign="top"> Reimbursing Bank :  </td>
					<td colspan="6" width = "400px"  valign="top">Approval No & Date :  </td>
				</tr>
			</table>	
			<br>
        </div>
    </body>
    <? 
	exit();
}

?>