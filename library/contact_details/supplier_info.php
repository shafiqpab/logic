<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//----------------------------------------------------------------------------------------------------------------
//echo load_html_head_contents("Supplier Info","../../",1 ,1 ,'',1 );
echo load_html_head_contents("Supplier Info", "../../", 1, 1, $unicode,1,'');
 
?>

 
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  

var permission='<? echo $permission; ?>';

// Mandatory Field
var field_level_data='';
	<?
	if(isset($_SESSION['logic_erp']['data_arr'][527]))
	{
		$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][527] );
		echo "field_level_data= ". $data_arr . ";\n";
	}
	echo "var mandatory_field = '". implode('*',$_SESSION['logic_erp']['mandatory_field'][527]) . "';\n";
	echo "var mandatory_message = '". implode('*',$_SESSION['logic_erp']['mandatory_message'][527]) . "';\n";
	?>


function fnc_supplier_info( operation )
{

    if(mandatory_field){
			if (form_validation(mandatory_field,mandatory_message)==false)
			{
				release_freezing();
				return;
			}
		}
        
	var cbo_party_type=document.getElementById('cbo_party_type').value.split(',');
	if (form_validation('txt_supplier_name*txt_short_name*cbo_party_type*cbo_tag_company','Supplier Name*Short Name*Party Type*Tag Company')==false)
	{
		return;
	}
	else if($.inArray('90',cbo_party_type)> -1 && form_validation('cbo_buyer','Link to Buyer')==false)
	{
		
		//alert("mmmmm");
		return;
		
		
	}
	else // Save Here
	{
		eval(get_submitted_variables('txt_supplier_name*txt_short_name*txt_contact_person*txt_contact_no*txt_party_type_id*txt_desination*cbo_tag_company*cbo_country*txt_web_site*txt_email*txt_address_1st*txt_address_2nd*txt_address_3rd*txt_address_4th*cbo_buyer*cbo_status*txt_remark*txt_credit_limit_days*txt_credit_limit_amount*cbo_credit_limit_amount_curr*cbo_discount_method*cbo_security_deducted*cbo_vat_to_be_deducted*cbo_ait_to_be_deducted*cbo_individual*cbo_supplier_nature*txt_tag_buyer_id*update_id*txt_supplier_ref*is_posted_accounts'));
		
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_supplier_name*txt_short_name*txt_contact_person*txt_contact_no*txt_party_type_id*txt_desination*cbo_tag_company*cbo_country*txt_web_site*txt_email*txt_address_1st*txt_address_2nd*txt_address_3rd*txt_address_4th*cbo_buyer*cbo_status*txt_remark*txt_credit_limit_days*txt_credit_limit_amount*cbo_credit_limit_amount_curr*cbo_discount_method*cbo_security_deducted*cbo_vat_to_be_deducted*cbo_ait_to_be_deducted*cbo_individual*cbo_supplier_nature*txt_tag_buyer_id*update_id*supplier_hidden_id*txt_supplier_ref*is_posted_accounts*owner_name*owner_nid*owner_contact*owner_email*txt_tin_number*txt_vat_number*cbo_supplier_source','../../');
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/supplier_info_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_supplier_info_reponse;
	}
}

function fnc_supplier_info_reponse()
{
	if(http.readyState == 4) 
	{
		//alert(http.responseText)
        console.log(http.responseText);
		var reponse=trim(http.responseText).split('**');
		if(reponse[0]==50)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		}
        if(reponse[0]==15)
        {
            alert("Special Character not allowed!!");
            release_freezing();
            return;
        }

		show_msg(trim(reponse[0]));
		show_list_view(reponse[1],'show_supplier_list_view','list_view_div','../contact_details/requires/supplier_info_controller','setFilterGrid("list_view",-1)');
		reset_form('supplierinfo_1','posted_account_td','');
		set_button_status(0, permission, 'fnc_supplier_info');
        $('#txt_email').val('');
		release_freezing();
	}
} 
 function openmypage_party_type()
	{
		var party_type_id = $('#txt_party_type_id').val();
		var title = 'Party Type Name Selection Form';	
		var page_link = 'requires/supplier_info_controller.php?party_type_id='+party_type_id+'&action=party_name_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var party_id=this.contentDoc.getElementById("hidden_party_id").value;	 //Access form field with id="emailfield"
			var party_name=this.contentDoc.getElementById("hidden_party_name").value;
			$('#txt_party_type_id').val(party_id);
			$('#cbo_party_type').val(party_name);
		}
	}
	
	 function openmypage_tag_buyer()
	{
		var txt_tag_buyer_id = $('#txt_tag_buyer_id').val();
		var title = 'Buyer Name Selection Form';	
		var page_link = 'requires/supplier_info_controller.php?txt_tag_buyer_id='+txt_tag_buyer_id+'&action=buyer_name_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var buyer_id=this.contentDoc.getElementById("hidden_buyer_id").value;	 //Access form field with id="emailfield"
			var buyer_name=this.contentDoc.getElementById("hidden_buyer_name").value;
			$('#txt_tag_buyer_id').val(buyer_id);
			$('#cbo_tag_buyer').val(buyer_name);
		}
	}

    function openmypage_owner_info()
    {
        owner_name*owner_nid*owner_contact*owner_email
        var owner_name = $('#owner_name').val();
        var owner_nid = $('#owner_nid').val();
        var owner_contact = $('#owner_contact').val();
        var owner_email = $('#owner_email').val();
        var title = 'Owner Information Form';    
        var page_link = 'requires/supplier_info_controller.php?owner_name='+owner_name+'&owner_nid='+owner_nid+'&owner_contact='+owner_contact+'&owner_email='+owner_email+'&action=owner_info';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=700px,height=370px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
            var owner_name=this.contentDoc.getElementById("owner_name").value;    //Access form field with id="emailfield"
            var owner_nid=this.contentDoc.getElementById("owner_nid").value;
            var owner_contact=this.contentDoc.getElementById("owner_contact").value;
            var owner_email=this.contentDoc.getElementById("owner_email").value;
            $('#owner_info').val(owner_name);
            $('#owner_name').val(owner_name);
            $('#owner_nid').val(owner_nid);
            $('#owner_contact').val(owner_contact);
            $('#owner_email').val(owner_email);
        }
    }	
</script>
</head>	
<body onLoad="set_hotkey()">

<div align="center" style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="excelImport_1" id="excelImport_1" action="supplier_info_import_excel.php" enctype="multipart/form-data" method="post">
    	<table cellpadding="0" cellspacing="2" width="1000" style="padding-left: 5px; padding-right: 5px;">
    		<tr>
    			<td width="200" align="left"><input type="file" id="uploadfile" name="uploadfile" class="image_uploader" required style="width:200px" /></td>
    			<td width="200" align="left"><input type="submit" name="submit" value="Excel File Upload" class="formbutton" style="width:110px" /></td>                
             	<td width="540" align="right"><a href="../../excel_format/supplier_up_requirement.xls"><input type="button" value="Excel Format Download" name="excel" id="excel" class="formbutton" style="width:150px"/></a></td>
            </tr>
    	</table>
    </form>
	<fieldset style="width:1010px;">
    <legend>Supplier Info</legend>
		
		 <form name="supplierinfo_1" id="supplierinfo_1" autocomplete="off">	
			<table cellpadding="0" cellspacing="2" border="0" width="100%">
			  <tr>
                    <td width="130" class="must_entry_caption">Supplier Name  </td>
                    <td width="180">
                   		<input type="text" name="txt_supplier_name" id="txt_supplier_name" class="text_boxes" style="width:180px" maxlength="100" title="Maximum 100 Character" />						<input type="hidden" name="supplier_hidden_id" id="supplier_hidden_id" class="text_boxes" /> 
                    </td>
                    <td width="130" class="must_entry_caption">
                        Short Name 
                    </td>
                    <td width="180">
                        <input type="text" name="txt_short_name" id="txt_short_name" class="text_boxes" style="width:180px" maxlength="35" title="Maximum 35 Character"/>						
                    </td>
                    <td>
                        Contact Person
                    </td>
                    <td>
                        <input type="text" name="txt_contact_person" id="txt_contact_person" class="text_boxes" style="width:180px"  maxlength="100" title="Maximum 100 Character"/>						
                    </td>
                </tr>			
				<tr>
                    <td width="130">
                        Designation
                    </td>
                    <td width="180">
                        <input type="text" name="txt_desination" id="txt_desination" class="text_boxes" style="width:180px" maxlength="50" title="Maximum 50 Character"/>						
                    </td>
                	
                    <td>
                        Contact No
                    </td>
                    <td>
                        <input type="text" name="txt_contact_no" id="txt_contact_no" class="text_boxes" style="width:180px" maxlength="28" title="Maximum 28 Character"/>						
                    </td>
                    <td>
                        Email
                    </td>
                    <td>
                        <input type="email" name="txt_email" id="txt_email" class="text_boxes" style="width:180px;" maxlength="100" title="Maximum 100 Character"/>						
                    </td>

                </tr>
                <tr>
                    <td>
                        http://www.
                    </td>
                    <td>
                        <input type="text" name="txt_web_site" id="txt_web_site" class="text_boxes" style="width:180px" maxlength="30" title="Maximum 30 Character"/>						
                    </td>
                    <td>
                        Address1
                    </td>
                    <td>
                        <textarea name="txt_address_1st" id="txt_address_1st" class="text_area" style="width:180px;" maxlength="500" title="Maximum 500 Character"></textarea>
                                        
                    </td>
                    <td>
                        Address2
                    </td>
                    <td>
                        <textarea name="txt_address_2nd" id="txt_address_2nd" class="text_area" style="width:180px;" maxlength="500" title="Maximum 500 Character"></textarea>
                                        
                    </td>
                </tr>
                <tr>
                    <td>
                        Address3
                    </td>
                    <td>
                        <textarea name="txt_address_3rd" id="txt_address_3rd" class="text_area" style="width:180px;" maxlength="500" title="Maximum 500 Character"></textarea>
                                        
                    </td>
                    <td>
                        Address4
                    </td>
                    <td>
                        <textarea name="txt_address_4th" id="txt_address_4th" class="text_area" style="width:180px;" maxlength="500" title="Maximum 500 Character"></textarea>
                                        
                    </td>
                    <td>
                        Country
                    </td>
                    <td>
                    <? echo create_drop_down( "cbo_country", 190, "select id,country_name from   lib_country where is_deleted=0 and status_active=1 order by country_name", "id,country_name", 1, "-- Select Country --", $selected_index, $onchange_func, $onchange_func_param_db,$onchange_func_param_sttc  ); ?>
                                    
                    </td>
                </tr> 
                <tr>
                	<td class="must_entry_caption">
                        Supplier Type
                    </td>
                    <td>
						<? 
                       // echo create_drop_down( "cbo_party_type", 190, $party_type_supplier, "", 0, "", '', 'set_value_supplier_nature(this.value)', $onchange_func_param_db,$onchange_func_param_sttc  ); 
					   
                        ?>
                          <input type="text" name="cbo_party_type" id="cbo_party_type" class="text_boxes" style="width:180px;" placeholder="Double Click To Search" onDblClick="openmypage_party_type();" readonly />
                            <input type="hidden" name="txt_party_type_id" id="txt_party_type_id" />				
                    </td>
                    <td class="must_entry_caption">
                        Tag Company
                    </td>
                    <td>
						<? 
                        echo create_drop_down( "cbo_tag_company", 190, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name", "id,company_name", 0, "", '', ''  ); 
                        ?>				
                    </td>
                    <td>
                       Link to Buyer
                    </td>
                    <td >
						<?php
						 echo create_drop_down( "cbo_buyer", 190, "select id,buyer_name from  lib_buyer where is_deleted=0 and status_active=1 order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected_index, $onchange_func, $onchange_func_param_db,$onchange_func_param_sttc  ); 
                        ?>
                	</td>
                </tr>
                <tr>
                	<td>
                        Credit Limit (Days)
                    </td>
                    <td>
					    <input type="text" name="txt_credit_limit_days" id="txt_credit_limit_days" class="text_boxes_numeric" style="width:180px" />						
                    </td>
                    <td>
                        Credit Limit (Amount)
                    </td>
                    <td>
                    <input type="text" name="txt_credit_limit_amount" id="txt_credit_limit_amount" class="text_boxes_numeric" style="width:100px" />						

						<? 
                        echo create_drop_down( "cbo_credit_limit_amount_curr",75, $currency, "", 0, "", '', ''  ); 
                        ?>				
                    </td>
                    <td>
                        Discount Method
                    </td>
                    <td >
						<?php
						 echo create_drop_down( "cbo_discount_method", 190, $currency, "", 1, "-- Select Method --", $selected_index, $onchange_func, $onchange_func_param_db,$onchange_func_param_sttc  ); 
                        ?>
                	</td>
                </tr>
                <tr>
                <tr>
                	<td>
                        Security deducted
                    </td>
                    <td>
					    <?php
						 echo create_drop_down( "cbo_security_deducted", 190, $yes_no, "", 0, "", "", "", "",""); 
                        ?>					
                    </td>
                    <td>
                        VAT to be deducted
                    </td>
                    <td>
						<? 
                        echo create_drop_down( "cbo_vat_to_be_deducted", 190,  $yes_no, "", 0, "", "", "", "",""); 
                        ?>				
                    </td>
                    <td>
                        AIT to be deducted
                    </td>
                    <td >
						<?php
						 echo create_drop_down( "cbo_ait_to_be_deducted",190, $yes_no, "", 0, "", "", "", "",""); 
                        ?>
                	</td>
                </tr>
                
                <tr>
                    
                    <td>
                        Remark
                    </td>
                    <td colspan="3">
                        <input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:503px" maxlength="500" title="Maximum 500 Character"999/>
                    </td>
                     <td>
                        Individual
                    </td>
                    <td>
						
                         <?
						 echo create_drop_down( "cbo_individual",190, $yes_no, "", 0, "", "", "", "","" );
						 ?>

                	</td>
                </tr>	
				<tr>
                     <td>
                        Supplier Nature
                    </td>
                    <td >
						
                         <?
						 echo create_drop_down( "cbo_supplier_nature", 190, $supplier_nature,'', $is_select, $select_text, 1, $onchange_func, "",'','' );
						 ?>

                	</td>
                    <td>
                        Status
                    </td>
                    <td >
						
                         <?
						 echo create_drop_down( "cbo_status", 190, $row_status,'', $is_select, $select_text, 1, $onchange_func, "",'','' );
						 ?>

                	</td>
                    <td>
                        Image
                    </td>
                    <td height="25" valign="middle">
                    	<input type="button" class="image_uploader" style="width:192px" value="CLICK TO ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'supplier_info', 0 ,1)">
                    </td>
                </tr>
                <tr>
                	<td>
                        Tag  Buyer
                    </td>
                    <td>
						
                          <input type="text" name="cbo_tag_buyer" id="cbo_tag_buyer" class="text_boxes" style="width:180px;" placeholder="Double Click To Search" onDblClick="openmypage_tag_buyer();" readonly />
                            <input type="hidden" name="txt_tag_buyer_id" id="txt_tag_buyer_id" />				
                    </td>
                    <td>
                        Supplier Ref.
                    </td>
                    <td>
						<input type="text" name="txt_supplier_ref" id="txt_supplier_ref" class="text_boxes"style="width:180px;" />				
                    </td>
                    <td>Owner Info</td>
                    <td>
                        <input type="text" name="owner_info" id="owner_info" class="text_boxes" style="width:180px;" placeholder="Double Click To Browse" onDblClick="openmypage_owner_info();" readonly >  

                        <input type="hidden" name="owner_name" id="owner_name">          
                        <input type="hidden" name="owner_nid" id="owner_nid">          
                        <input type="hidden" name="owner_contact" id="owner_contact">          
                        <input type="hidden" name="owner_email" id="owner_email">     


                    </td>
                                        
                </tr>		  
				 	 
                <tr>
                    <td>TIN Number</td>
				 	<td   style="max-width:100px; color:red; font-size:14px;">
                     <input type="text" name="txt_tin_number" id="txt_tin_number" class="text_boxes" style="width:180px;">
                     </td>
                     <td>VAT Number</td>
                     <td   style="max-width:100px; color:red; font-size:14px;">
                     <input type="text" name="txt_vat_number" id="txt_vat_number" class="text_boxes" style="width:180px;">
                     </td>
                     <td>Supplier ID</td>
                     <td style="max-width:100px; color:red; font-size:14px;">
                     <input type="text" name="update_id" id="update_id" class="text_boxes_numeric" style="width:180px;" readonly disabled>
                     </td>
                </tr>
                <tr>
                    <td>Supplier Source</td>
                    
                     <td>
                        <?
                         echo create_drop_down( "cbo_supplier_source", 190,  $commission_particulars, "", 1, "----Select-------", "", "", "",""); 
					  ?>
                     </td>

                     
                </tr>

                <tr>
				 	<td colspan="6" id="posted_account_td" style="max-width:100px; color:red; font-size:14px;">
                    </td>
             					
                </tr>
                <tr>
                    <td colspan="6" align="center" height="40" valign="middle" class="button_container"> 
                    <? 
                        echo load_submit_buttons( $permission, "fnc_supplier_info", 0,0 ,"reset_form('supplierinfo_1','','')",1);
                    ?> 

						<input type="hidden" name="is_posted_accounts" id="is_posted_accounts" />  
                    </td>					
                </tr>	
               

			</table>
		  </form>
	</fieldset>	
	
	<div style="width:100%; float:left; margin:auto" align="center" id="search_container">
		<fieldset style="width:600px; margin-top:10px">
            <div style="text-align:center;"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --","","","","2,3,4" ); ?></div>
			<table width="1010" cellspacing="2" cellpadding="0" border="0">
                    
					<tr>
						<td colspan="3">
                        <div id="list_view_div">
                        	<?
							$arr=array (8=>$currency,9=>$row_status,11=>$commission_particulars);
							echo  create_list_view ( "list_view", "ID,Supplier Name,Short Name,Supplier Type,Contact Person,Designation,Credit Limit(Days),Credit Limit (Amount),Currency, Status,Owner,Supplier Source", "50,150,100,150,100,120,80,80,70,70,70","1180","220",0, "select supplier_name,short_name,party_type,contact_person,designation,credit_limit_days,credit_limit_amount,credit_limit_amount_currency,status_active,id,owner_name,source_id,owner_nid,owner_contact,owner_email from lib_supplier where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,0,0,0,0,0,0,credit_limit_amount_currency,status_active,0,source_id", $arr , "id,supplier_name,short_name,party_type,contact_person,designation,credit_limit_days,credit_limit_amount,credit_limit_amount_currency,status_active,owner_name,source_id", "../contact_details/requires/supplier_info_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,1,1,0,0') ;
							
							 ?>
                        </div>
						</td>
					</tr>
				</table>
		</fieldset>	
	</div>
</div>
</body>

<script>
	set_multiselect('cbo_tag_company','0','0','','__set_buyer_status__../contact_details/requires/supplier_info_controller');
</script>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>