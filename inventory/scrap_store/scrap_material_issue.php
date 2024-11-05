<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Scrap Sales Entry 
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	10-05-2015
Updated by 		: 	
Update date		: 	Rakib   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//-----------------------------------------------------------------------------------------
echo load_html_head_contents("Scrap Material Issue", "../../", 1, 1, '', '', '', '');
 
// print_r($_SESSION['logic_erp']['mandatory_message'][700]);die;
		 

//========== user credential  ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("select unit_id as company_id, item_cate_id, company_location_id, store_location_id from user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$category_credential_id = $userCredential[0][csf('item_cate_id')];

if ($company_id !='') {
    $company_credential_cond = "and id in($company_id)";
}

if ($category_credential_id !='') {
    $category_credential_cond = "and CATEGORY_ID in($category_credential_id)";
}

?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	function openmypage_systemId()
	{
		var row_num = $('#tbl_material_details tr').length;
		for(var i=1; i<=row_num; i++)
		{
			$('#dtls_update_id_'+i).val('');
			$('#txt_prodid_'+i).val('');
			$('#txt_itemgroup_'+i).val('');
			$('#txt_itemdes_'+i).val('');
			$('#cbo_rejuomid_'+i).val('');
			$('#txt_stock_'+i).val('');
			$('#txt_salesqty_'+i).val('');
			$('#txt_salesrate_'+i).val('');
			$('#txt_salesamount_'+i).val('');
			$('#txt_bag_'+i).val('');
			$('#txt_remarks_dtls_'+i).val('');
			$('#txt_prodid_'+i).attr('disabled',false);
		}				
		var company_id = $('#cbo_company_id').val();
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 	
			var page_link='requires/scrap_material_issue_controller.php?company_id='+company_id+'&action=system_popup';
			var title='Scram Issue Form';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				freeze_window(5);
				var theform=this.contentDoc.forms[0];
				var sys_id=this.contentDoc.getElementById("hidden_mst_id").value;

				if(sys_id !="")
				{					
					get_php_form_data(sys_id, "populate_data_from_mst", "requires/scrap_material_issue_controller");					
					show_list_view(sys_id,'show_dtls_listview','div_details_list_view','requires/scrap_material_issue_controller','');
					//$("#cbo_company_id").attr('disabled',true);
					set_button_status(0, permission, 'fnc_material_issue',1,1);					
				}
				release_freezing();							 
			}
		}
	}

	function openmypage_ItemDescription()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_category_id = $('#cbo_category_id').val();
		var cbo_store_id = $('#cbo_store_id').val();
		var cbo_purpose = $('#cbo_purpose').val();
		
		if (form_validation('cbo_company_id*cbo_category_id','Company*Item Category')==false)
		{
			return;
		}
	
		var title = 'Item Description Info Pop Up';
		var page_link = 'requires/scrap_material_issue_controller.php?cbo_company_id='+cbo_company_id+'&cbo_category_id='+cbo_category_id+'&cbo_store_id='+cbo_store_id+'&action=itemDescription_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			freeze_window(5);
			var theform=this.contentDoc.forms[0]
			var prod_ids=this.contentDoc.getElementById("hidden_prod_id").value;

			if(prod_ids != '')
			{
				var all_data = cbo_company_id+'**'+cbo_category_id+'**'+prod_ids+'**'+cbo_purpose+'**'+cbo_store_id;				
				var list_view_orders = return_global_ajax_value( all_data, 'show_product_dtls_listview', '', 'requires/scrap_material_issue_controller');
				$('#tbl_material_details').html(list_view_orders);
				set_all_onclick();			
			}
			release_freezing();
		}
	}	
	
	function openpage_customer()
	{
		var cbo_company_id = $('#cbo_company_id').val();

		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
			
		var title = 'All Customer Pop Up';	
		var page_link = 'requires/scrap_material_issue_controller.php?cbo_company_id='+cbo_company_id+'&action=customer_search_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var customer_id=this.contentDoc.getElementById("hidden_customer_id").value; 
			var customer_name=this.contentDoc.getElementById("hidden_customer_no").value; 
			
			$('#txt_customer_id').val(customer_id);
			$('#txt_customer_no').val(customer_name);			
		}
	}	
		
	function fnc_material_issue(operation)
	{

		if(form_validation('cbo_company_id*cbo_location*cbo_category_id*cbo_store_id*txt_selling_date*cbo_purpose','Company*Location*Item Category*Store*Issue Date*Purpose')==false )
		{
			return;
		}
  
		
		var mandatory_fields = '<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][700]);?>';
		var mandatory_messages = '<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][700]);?>';
		if(mandatory_fields)
        {
			var row_num = $('#tbl_material_details tr').length;
			var fieldDataArr = Array();
			var fieldMsgArr = Array();
			for(var i=1; i<=row_num; i++)
		    {
				var mandatory_field_arr = mandatory_fields.split('*');
				var mandatory_message_arr = mandatory_messages.split('*');
				$.each(mandatory_field_arr, function(index, value) {
					fieldDataArr.push(value+'_'+i);
					fieldMsgArr.push( mandatory_message_arr[index]);
				});
		    }

			if (form_validation(fieldDataArr.join('*'), fieldDataArr.join('*'))==false)
			{
				return;
			}

        }
	 
		/*if ($("#txt_prodid").val() == "")
        {
        	alert("Please First Browse The Product ID");
        	$("#txt_prodid").focus();
        	return;
        }*/        
		var purpose = document.getElementById("cbo_purpose").value;
        var row_num = $('#tbl_material_details tr').length;
		var dtls_data="";
		for(var i=1; i<=row_num; i++)
		{				
			if (purpose==1){
				if (form_validation('txt_prodid_'+i+'*txt_salesqty_'+i,'Browse Product Id*Issue Qty')==false)
				{
					return;
				}

			} 
			else {
				if (form_validation('txt_prodid_'+i+'*txt_salesqty_'+i,'Browse Product Id*Issue Qty')==false)
				{
					return;
				}
			}				

			if (operation==0){
				if($("#txt_salesqty_"+i).val()*1>$("#txt_stock_"+i).val()*1)
				{
					alert("Issue Qty Can not be Greater Than Stock Qty.");
					$("#txt_salesqty_"+i).focus();
					return;
				}
			} else if (operation==1){
				if($("#txt_salesqty_"+i).val()*1 > ($("#txt_stock_"+i).val()*1 + $("#hidden_salesqty_"+i).val()*1))
				{
					alert("Issue Qty Can not be Greater Than Stock Qty.");
					$("#txt_salesqty_"+i).focus();
					return;
				}
			}
			
			dtls_data += "*txt_prodid_"+i+"*dtls_update_id_"+i+"*itemgroup_"+i+"*txt_itemdes_"+i+"*cbo_rejuomid_"+i+"*txt_salesqty_"+i+"*txt_salesrate_"+i+"*txt_salesamount_"+i+"*txt_bag_"+i+"*txt_remarks_dtls_"+i;
		}
		var customer_no=document.getElementById("txt_customer_no").value;
		if(customer_no!=""){
			document.getElementById("txt_customer_id").value=customer_no;
		}
		var data="action=save_update_delete&row_num="+row_num+"&operation="+operation+get_submitted_data_string('update_id*txt_system_id*cbo_company_id*cbo_location*cbo_category_id*cbo_store_id*txt_selling_date*cbo_purpose*cbo_pay_term*txt_customer_id*cbo_currency_id*txt_exchange_rate*txt_remarks'+dtls_data,"../../");
		//alert (dtls_data);
		freeze_window(operation);			
		http.open("POST","requires/scrap_material_issue_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_material_issue_response;

	}
	
	function fnc_material_issue_response()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);

			document.getElementById('update_id').value = response[2];
			document.getElementById('txt_system_id').value = response[1];		

			var list_view_dtls = return_global_ajax_value( response[2], 'show_dtls_listview', '', 'requires/scrap_material_issue_controller');
			$('#div_details_list_view').html(list_view_dtls);

			if((response[0]==0 || response[0]==1))
			{				
				var row_num = $('#tbl_material_details tr').length;
				for(var i=1; i<=row_num; i++)
				{
					if (i == 1){
						$('#cbo_company_id').attr('disabled',true);
						$('#cbo_location').attr('disabled',true);
						$('#cbo_category_id').attr('disabled',true);
						$('#cbo_store_id').attr('disabled',true);
						$('#txt_selling_date').attr('disabled',true);
						$('#cbo_purpose').attr('disabled',true);
						$('#txt_customer_no').attr('disabled',true);
						$('#cbo_pay_term').attr('disabled',true);
						$('#cbo_currency_id').attr('disabled',true);
						$('#dtls_update_id_'+i).val('');
						//$('#itemgroup_'+i).val('');
						$('#txt_prodid_'+i).val('');
						$('#txt_itemgroup_'+i).val('');
						$('#txt_itemdes_'+i).val('');
						$('#cbo_rejuomid_'+i).val('');
						$('#txt_stock_'+i).val('');
						$('#txt_salesqty_'+i).val('');
						$('#txt_salesrate_'+i).val('');
						$('#txt_salesamount_'+i).val('');
						$('#txt_bag_'+i).val('');
						$('#txt_remarks_dtls_'+i).val('');
						$('#txt_prodid_'+i).attr('disabled',false);
					} else {
						$('#row_'+i).remove();
					}
				}				
			}

			if (response[0]==2)
			{
				var row_num = $('#tbl_material_details tr').length;
				for(var i=1; i<=row_num; i++)
				{
					if (i == 1){
						$('#cbo_company_id').attr('disabled',true);
						$('#cbo_location').attr('disabled',true);
						$('#cbo_category_id').attr('disabled',true);
						$('#cbo_store_id').attr('disabled',true);
						$('#txt_selling_date').attr('disabled',true);
						$('#cbo_purpose').attr('disabled',true);
						$('#txt_customer_no').attr('disabled',true);
						$('#cbo_pay_term').attr('disabled',true);
						$('#cbo_currency_id').attr('disabled',true);
						
						$('#dtls_update_id_'+i).val('');
						//$('#itemgroup_'+i).val('');
						$('#txt_prodid_'+i).val('');
						$('#txt_itemgroup_'+i).val('');
						$('#txt_itemdes_'+i).val('');
						$('#cbo_rejuomid_'+i).val('');
						$('#txt_stock_'+i).val('');
						$('#txt_salesqty_'+i).val('');
						$('#txt_salesrate_'+i).val('');
						$('#txt_salesamount_'+i).val('');
						$('#txt_bag_'+i).val('');
						$('#txt_remarks_dtls_'+i).val('');
						$('#txt_prodid_'+i).attr('disabled',false);
					} else {
						$('#row_'+i).remove();
					}
				}

				var row_nums = $('#div_details_list_view tr').length;
				if (row_nums == 1){
					$('#cbo_company_id').val('');
					$('#cbo_location').val('');
					$('#cbo_category_id').val('');
					$('#cbo_store_id').val('');
					$('#txt_selling_date').val('');
					$('#cbo_purpose').val('');
					$('#txt_customer_no').val('');
					$('#cbo_pay_term').val('');
					$('#cbo_currency_id').val('');
					$('#txt_exchange_rate').val('');
					$('#txt_remarks').val('');
				}
			}	

			set_button_status(0, permission, 'fnc_material_issue',1,1);	
			release_freezing();	
		}
	}

	function check_enable_disable_field()
	{
		var purpose = document.getElementById("cbo_purpose").value;
		if (purpose==2)
		{
			$('#txt_customer_no').attr('disabled',true);			
			$("#issue_rate_td font").removeAttr('color','blue');
			var row_num = $('#tbl_material_details tr').length;
			for(var i=1; i<=row_num; i++)
			{
				$('#txt_salesrate_'+i).attr('disabled',true);
				$('#txt_salesrate_'+i).val('');
				$('#txt_salesamount_'+i).val('');
			}	
		}	
		else 
		{
			$('#txt_customer_no').attr('disabled',false);
			//$('#txt_salesrate_1').attr('disabled',false);
			$("#issue_rate_td font").attr('color','blue');
			var row_num = $('#tbl_material_details tr').length;
			for(var i=1; i<=row_num; i++)
			{
				$('#txt_salesrate_'+i).attr('disabled',false);
			}
		}	
	}

	function amount_calculation(row_num,purpose)
	{
		//alert(purpose);
		var sales_qty=$('#txt_salesqty_'+row_num).val()*1;
		if (purpose==1) 
		{
			var sales_rate=$('#txt_salesrate_'+row_num).val()*1;
			var tot_amount=sales_qty*sales_rate;
			$('#txt_salesamount_'+row_num).val(tot_amount.toFixed(2));
		}	
		else
		{
			$('#txt_salesrate_'+row_num).val('');
			$('#txt_salesamount_'+row_num).val('');
		}		
	}

	function fnc_load_location(params)
	{
        var item_category_id = document.getElementById('cbo_category_id').value;
        load_drop_down('requires/scrap_material_issue_controller', params+'_'+item_category_id, 'load_drop_down_location', 'location_td');
    }

    function fnc_load_store(params)
    {
        var cbo_company_id = document.getElementById('cbo_company_id').value;
        var cbo_location = document.getElementById('cbo_location').value;
        load_drop_down('requires/scrap_material_issue_controller', cbo_company_id+'_'+cbo_location+'_'+params, 'load_drop_down_store', 'store_td');
    }

    function generate_report(type)
	{
		//alert(type);
		var update_id  = $('#update_id').val();
		if (update_id == '') {
			alert("Please At First Save Data");
			return;
		}
		var company_id = $('#cbo_company_id').val()
		var location_id= $('#cbo_location').val();
		
		if(type==1)
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true)
			{
				show_item=1;
			}
			else
			{
				show_item=0;
			}
			var report_title=$( "div.form_caption" ).html();
			var action='scrap_material_challan_print';
			print_report(company_id+'**'+update_id+'**'+location_id+'**'+show_item+'**'+report_title, action, "requires/scrap_material_issue_controller");

			show_msg("3");
		}
		else
		{
			var action='scrap_material_challan_print2';
			//var report_title='Yarn Sales Challan/Gate Pass';
			var report_title=$( "div.form_caption" ).html();

			print_report(company_id+'**'+update_id+'**'+location_id+'**'+report_title, action, "requires/scrap_material_issue_controller");
			//return;
			show_msg("3");

		}
	}

		
</script>
<body onLoad="set_hotkey()">
<div  align="center" style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="materialIssue_1" id="materialIssue_1" autocomplete="off">
    <div style="width:950px;">   
        <fieldset style="width:950px;">
        <legend>Scrap Material Issue</legend>
        <br>
        	<fieldset style="width:850px;">
                <table width="850" cellspacing="2" cellpadding="0" border="0" id="tbl_master" align="center">
                    <tr>
                        <td colspan="3" align="right"><strong>System ID</strong></td>
                        <td colspan="3" align="left">
                        	<input type="hidden" name="update_id" id="update_id" />
                            <input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_systemId();" readonly />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Company Name</td>
                        <td>
                            <? 
                                echo create_drop_down( "cbo_company_id", 160, "select id,company_name from lib_company  where status_active=1 and is_deleted=0 $company_credential_cond order by company_name","id,company_name", 1, "-- Select Company --", 0, "fnc_load_location(this.value);get_php_form_data( this.value, 'company_wise_report_button_setting','requires/scrap_material_issue_controller' );" );
                            ?>
                        </td>

                        <td class="must_entry_caption">Location</td>
                        <td id="location_td">
                            <? 
                                echo create_drop_down("cbo_location", 160, $blank_array, "", 1, "-- Select --", 0, "", 0, "");
                            ?>
                        </td>

                        <td class="must_entry_caption">Item Category</td>
                        <td>
                            <? 
                            	$item_cate_array = return_library_array("select category_id, short_name from  lib_item_category_list where status_active=1 and is_deleted=0 $category_credential_cond order by short_name", "category_id", "short_name"); //$category_credential_cond
                                echo create_drop_down("cbo_category_id", 160, $item_cate_array, "", 1, "-- Select Item --", $selected, "fnc_load_store(this.value);", "", "");
                            ?>
                        </td>                        
                    </tr> 
                    <tr>
                    	<td class="must_entry_caption">Store Name</td>
                        <td id="store_td">
                            <? 
                                echo create_drop_down("cbo_store_id", 160, $blank_array, "", 1, "-- Select --", 0, "", 0, "");
                            ?>
                        </td>

                        <td class="must_entry_caption">Issue Date</td>
                        <td>
                            <input class="datepicker" type="text" style="width:150px" name="txt_selling_date" id="txt_selling_date" placeholder="Date" value="<? echo date("d-m-Y"); ?>"/>
                        </td>

                        <td class="must_entry_caption">Issue Purpose</td>
                        <td>
							<?
								$purpose_array=array(1=>"Sales", 2=>"Disposal");
                                echo create_drop_down("cbo_purpose", 160, $purpose_array, "", 1, "--Select Purpose--", 0, "check_enable_disable_field()");
                            ?>
                        </td>                    	
                    </tr>
                    <tr>
                    	<td>Customer</td>
                        <td>
                            <input name="txt_customer_no" id="txt_customer_no" class="text_boxes" style="width:150px"  placeholder="Browse Customer" onDblClick="openpage_customer();" readonly/>
                            <input type="hidden" name="txt_customer_id" id="txt_customer_id"/>
                        </td>

                        <td>Pay Term</td>
                        <td>
                            <? 
                                echo create_drop_down("cbo_pay_term", 160, $pay_mode, "", 1, "--Select Pay Term--", 4, "", "", "1,4");
                            ?>
                        </td>

                        <td>Currency</td>
                        <td>
                            <?
                                echo create_drop_down("cbo_currency_id", 160, $currency, "", 1, "--Select Currency--", 1, "", 1);
                            ?>
                        </td>
                    </tr>
                    <tr>
                    	<td>Exchange Rate</td>
                        <td>
							<input type="text" name="txt_exchange_rate" id="txt_exchange_rate" class="text_boxes_numeric" style="width:150px"  value="1" disabled="disabled"/>
                        </td>

                    	<td>Remarks</td>
                        <td colspan="3">
                            <input name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:455px"/>
                        </td> 
                    	 
                    </tr>
                </table>
            </fieldset>
            <br>
            <fieldset style="width:870px">
            <legend>Material Entry</legend>
            <table width="870" cellspacing="1" cellpadding="1" border="1" rules="all" class="rpt_table">
                <thead>
                    <th width="80" class="must_entry_caption">Product ID</th>
                    <th width="100">Item Group</th>
                    <th width="150">Item Description</th>
                    <th width="80">UOM</th>
                    <th width="80">Stock Qty</th>
                    <th width="80" class="must_entry_caption">Issue Qty</th>
                    <th width="60" class="must_entry_caption" id="issue_rate_td">Issue Rate</th>
                    <th width="80">Amount</th>
                    <th width="60">No. Of Bag</th>
                    <th width="100">Remarks</th>
				</thead>
                <tbody id="tbl_material_details">
                	<tr id="row_1">
                		<input type="hidden" name="dtls_update_id_1" id="dtls_update_id_1"/>
                		<input type="hidden" name="trans_id_1" id="trans_id_1"/>
                		<input type="hidden" name="itemgroup_1" id="itemgroup_1"/>
                    	<td>                            
                            <input type="text" name="txt_prodid_1" id="txt_prodid_1" class="text_boxes" style="width:80px" onDblClick="openmypage_ItemDescription()" placeholder="Browse" readonly />                      
                        </td>

                        <td><input type="text" name="txt_itemgroup_1" id="txt_itemgroup_1" class="text_boxes" style="width:100px" disabled="disabled" /></td>

                        <td><input type="text" name="txt_itemdes_1" id="txt_itemdes_1" class="text_boxes" style="width:150px" disabled="disabled" /></td>

                        <!-- <td><? //echo create_drop_down( "cbo_rejuomid_1", 80, $unit_of_measurement, "", 1, "-Select-", 0, "", 1 ); ?></td> -->
                         <td><? echo create_drop_down( "cbo_rejuomid_1", 80, $unit_of_measurement, "", 1, "-Select-", 0, "", 1, '','','','','','', "cbo_rejuomid[]"); ?></td>

                        <td>
                        	<input type="text" name="txt_stock_1" id="txt_stock_1" class="text_boxes_numeric" style="width:80px" disabled="disabled" />                        	
                        </td>

                        <td>
                        	<input type="text" name="txt_salesqty_1" id="txt_salesqty_1" class="text_boxes_numeric" style="width:80px" onkeyup='amount_calculation(1,document.getElementById("cbo_purpose").value);'/>
                        	<input type="hidden" name="hidden_salesqty_1" id="hidden_salesqty_1"/>
                        </td>

                        <td><input type="text" name="txt_salesrate_1" id="txt_salesrate_1" class="text_boxes_numeric" style="width:60px" onkeyup='amount_calculation(1,document.getElementById("cbo_purpose").value);'/></td>

                        <td><input type="text" name="txt_salesamount_1" id="txt_salesamount_1" class="text_boxes_numeric" style="width:80px" disabled="disabled" /></td>

                        <td><input type="text" name="txt_bag_1" id="txt_bag_1" class="text_boxes_numeric" style="width:60px;"/></td>

                        <td><input type="text" name="txt_remarks_dtls_1" id="txt_remarks_dtls_1" class="text_boxes" style="width:100px;"/></td>
                    </tr>
                </tbody>
                <tr>
                    <td align="center" colspan="10" class="button_container" width="100%">
                        <? //Report Setting > inventory\purchase_requisition.php
                            echo load_submit_buttons($permission, "fnc_material_issue", 0,0,"reset_form('materialIssue_1','div_details_list_view','','','')",1);
                        ?>
                        
                        <input type="button" name="search" id="search1" value="Print" onClick="generate_report(1)" style="width:100px;display:none;" class="formbuttonplasminus" />
                 		<input type="button" name="search" id="search2" value="Print 2" onClick="generate_report(2)" style="width:100px;display:none;" class="formbuttonplasminus" />
                    </td>
                </tr>
			</table>
            </fieldset>
		</fieldset>
        <div style="width:1000px;" id="div_details_list_view"></div>

	</div>
	</form>
</div>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>