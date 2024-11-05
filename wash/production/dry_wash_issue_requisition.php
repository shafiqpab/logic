<?
/*********************Comments******************
* Purpose			: 	Dry Wash Issue Requisition  
* Functionality	:                                                                           
* JS Functions	:                                                                           
* Created by		:	Md. Mahbubur Rahman                                                               	
* Creation date 	: 	18-04-2022                                                         
* Updated by 		:                                           									
* Update date		:                                                          						   
* QC Performed BY	:                                                                       		
* QC Date			:                                                                       	
* Comments			s:                                                                           
***************/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
echo load_html_head_contents("Dry Wash Issue Requisition ","../../", 1, 1, $unicode,1,1);
$today=date('d-m-Y'); 
//--------------------------------------------------------------------------------------------------------------------
?>	 
<script>
	var permission='<? echo $permission; ?>';
	var today='<? echo $today; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	
	

	function location_select()
	{
		if($('#cbo_location option').length==2)
		{
			if($('#cbo_location option:first').val()==0)
			{
				$('#cbo_location').val($('#cbo_location option:last').val());
				//eval($('#cbo_location').attr('onchange')); 
			}
		}
		else if($('#cbo_location option').length==1)
		{
			$('#cbo_location').val($('#cbo_location option:last').val());
			//eval($('#cbo_location').attr('onchange'));
		}
		load_drop_down('requires/dry_wash_issue_requisition_controller', document.getElementById('cbo_company_id').value+'__'+document.getElementById('cbo_location').value, 'load_drop_down_floor', 'floor_td');	
	}


	function fnc_embel_style_id(row_no)
	{
		var cbo_company_id=$('#cbo_company_id').val();
		
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		
		var title = 'Style Selection Form';	
		var page_link = 'requires/dry_wash_issue_requisition_controller.php?cbo_company_id='+cbo_company_id+'&action=po_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
 			var buyer_style_ref=this.contentDoc.getElementById("buyer_style_ref").value;
			var po_id=this.contentDoc.getElementById("po_id").value;
  			$('#txt_buyer_style').val(buyer_style_ref);
			$('#poId').val(po_id);
		}
	}


	function fnc_item_details(sub_process_id,is_update,store_id)
	{
		
		if(store_id!="") $('#cbo_store_name').val(store_id);
 		if (form_validation('cbo_company_id*cbo_receive_basis*cbo_store_name','Company*Issue Basis*Store Name')==false)
		{
			return;
		}
		$('#accordion_h'+sub_process_id+'span').html("-");

		var cbo_company_id= $('#cbo_company_id').val();
		var cbo_store_name= $('#cbo_store_name').val();
		var cbo_receive_basis= $('#cbo_receive_basis').val();
 		var is_update= $('#update_id').val();
  		var hidden_posted_account=$("#hidden_posted_account").val();
 		show_list_view(cbo_company_id+'**'+sub_process_id+"**"+cbo_receive_basis+"**"+is_update+"**"+cbo_store_name, 'item_details', 'list_container_recipe_items', 'requires/dry_wash_issue_requisition_controller', '');
		$('#list_container_update_items'). remove();
		if(is_update !="")
		{
			set_button_status(1, permission, 'fnc_dry_wash_issue_requisition_entry',1,1);
		}
		else
		{
			set_button_status(0, permission, 'fnc_dry_wash_issue_requisition_entry',1,0);
		}
		
		var tableFilters = 
		 {
			col_0: "none",
			col_7: "none",
			col_8: "none",
			col_9: "none",
			col_10: "none"
		 }
		
	   setFilterGrid("tbl_list_search",-1,tableFilters);
	
	}

	 
	
  	function fnc_embel_requisition_id()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_id").val();	
		var page_link='requires/dry_wash_issue_requisition_controller.php?action=mrr_popup&company='+company; 
		var title="Search Issue Number Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=460px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var mrrNumber=this.contentDoc.getElementById("hidden_issue_number").value; // mrr number
			mrrNumber = mrrNumber.split("_"); 
 			var update_id = mrrNumber[7];
			get_php_form_data(update_id, "populate_data_from_data", "requires/dry_wash_issue_requisition_controller");
 			//document.getElementById("list_container_recipe_items").innerHTML = "";
  			//$('#list_container_recipe_items'). remove();
  			disable_enable_fields( 'cbo_company_id*cbo_receive_basis*cbo_store_name', 1, '', '' );  
			var store_name = document.getElementById('cbo_store_name').value;
			var cbo_receive_basis= $('#cbo_receive_basis').val();
 			show_list_view(company+'**'+company+"**"+cbo_receive_basis+"**"+update_id+"**"+store_name, 'item_details', 'list_container_recipe_items', 'requires/dry_wash_issue_requisition_controller', '');
			
	 
		var tableFilters = 
		 {
			col_0: "none",
			col_7: "none",
			col_8: "none",
			col_9: "none",
			col_10: "none"
		 }
		
	   setFilterGrid("tbl_list_search",-1,tableFilters);
	   
 		}
	}
	
 	function fnc_dry_wash_issue_requisition_entry(operation)
	{
		if(operation==4)
		{
			if($('#update_id').val()<1)
			{
				alert("Save Data First.");return;
			}
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_mrr_no').val()+'*'+$('#cbo_template_id').val(),'chemical_dyes_issue_print','requires/wash_chemical_dyes_issue_controller');
			return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{

			if ($('#update_id').val()!='') {
				operation =1;
			}

			if(operation==2)
			{
				show_msg('13');
				return;
			}
			var row_num=$('#tbl_list_search tbody tr').length;
 			if( form_validation('cbo_company_id*cbo_location*cbo_receive_basis*cbo_method*txt_buyer_style*cbo_store_name','Company Name*Location*Issue Basis*Requisition For*Style*Store Name')==false )
			{
				return;
			}
 			var current_date = '<? echo date("d-m-Y"); ?>';
			if (date_compare($('#txt_requisition_date').val(), current_date) == false) 
			{
				alert("Issue Requisition Date Can not Be Greater Than Current Date");
				return;
			}
  			var data_all="";  var i=0;
 			for (var j=1; j<row_num; j++)
			{
 				var txt_reqn_qnty=$('#txt_reqn_qnty_edit_'+j).val()*1;
				if(txt_reqn_qnty>0) 
				{
					i++;
 					data_all+="&txt_prod_id_" + i + "='" + $('#txt_prod_id_'+j).val() +"'"+"&txt_reqn_qnty_edit_" + i + "='" + $('#txt_reqn_qnty_edit_'+j).val() +"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'";
  				}
			}
 			var data="action=save_update_delete&operation="+operation+'&total_row='+i+data_all+get_submitted_data_string('update_id*cbo_company_id*cbo_location*cbo_floor_id*txt_requisition_date*cbo_receive_basis*cbo_method*txt_buyer_style*cbo_store_name*poId*txt_requisition_id',"../../");
 			 //alert (data); return;
			//freeze_window(operation);
			http.open("POST","requires/dry_wash_issue_requisition_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_dry_wash_issue_requisition_entry_reponse;
		}
	}
	
	
	function fnc_dry_wash_issue_requisition_entry_reponse()
	{	
		if(http.readyState == 4) 
		{   
			var reponse=trim(http.responseText).split('**');	
			show_msg(reponse[0]);
 			if((reponse[0]==0 || reponse[0]==1 ))
			{
				
				//alert(reponse[2]);
				document.getElementById('txt_requisition_id').value = reponse[2];
				document.getElementById('update_id').value = reponse[1];
 				var company = $("#cbo_company_id").val();
				var cbo_receive_basis = $("#cbo_receive_basis").val();
				var store_name = $("#cbo_store_name").val();
				$('#cbo_company_id').attr('disabled','disabled');
				$('#cbo_store_name').attr('disabled','disabled');
					show_list_view(company+'**'+company+"**"+cbo_receive_basis+"**"+reponse[1]+"**"+store_name, 'item_details', 'list_container_recipe_items', 'requires/dry_wash_issue_requisition_controller', '');
				setFilterGrid("tbl_list_search",-1);
				set_button_status(1, permission, 'fnc_dry_wash_issue_requisition_entry',1,1);
			}
 			release_freezing();	
		}
	}

</script> 
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission);  ?>  <br/>
		<form name="drywashissuerequisition_1" id="drywashissuerequisition_1" autocomplete="off" > 
	        <div style="width:1100px;">       
	            <fieldset style="width:1100px;">
	            	<legend>Dry Wash Issue Requisition</legend>
	            	<table width="910" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
		            	<tr>
	                        <td style="text-align: right;" colspan="4" ><b>Requisition Number</b></td>
	                        <td colspan="4">
	                        	<input type="text" name="txt_requisition_id" id="txt_requisition_id" class="text_boxes" style="width:140px; margin: 5px;" placeholder="Browse" onDblClick="fnc_embel_requisition_id(); " readonly />
                        		<input type="hidden" name="update_id" id="update_id"/>
                                <input type="hidden" name="poId" id="poId" class="text_boxes" readonly />
	                        </td>
	                    </tr>
	                    <tr style="margin: 5px;">
	                    	<td class="must_entry_caption" style="text-align: right;"><b>Company Name</b></td>
	                    	<td>
	                    		<? echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Select Company-", 0, "load_drop_down('requires/dry_wash_issue_requisition_controller', this.value, 'load_drop_down_location', 'location_td'); location_select();get_php_form_data( this.value, 'company_wise_report_button_setting','requires/dry_wash_issue_requisition_controller');load_drop_down( 'requires/dry_wash_issue_requisition_controller', $('#cbo_company_id').val()+'_'+$('#cbo_location').val(), 'load_drop_down_store', 'store_td' );");
								
								//load_drop_down('requires/dry_wash_issue_requisition_controller',this.value+'**'+$('#cbo_company_id').val(), 'load_drop_down_store', 'store_td' );
								?>
	                        </td>
	                        <td class="must_entry_caption" style="text-align: right;" ><b>Location</b></td>
	                    	<td id="location_td">
	                    		<? echo create_drop_down("cbo_location", 140, $blank_array,"", 1,"-Select Location-", 1,""); ?>
	                        </td>
	                        <td style="text-align: right;"><b>Floor/Unit</b></td>
	                    	<td id="floor_td">
	                    		<? echo create_drop_down( "cbo_floor_id", 140, $blank_array,"", 1, "--Select Floor/Unit--", $selected, "",1 ); ?>
	                        </td>
	                        <td class="must_entry_caption" style="text-align: right;" ><b>Requisition Date</b></td>
	                    	<td >
	                    		<input type="text" id="txt_requisition_date" name="txt_requisition_date" class="datepicker" placeholder="Requisition Date" style="width: 130px"/>
	                        </td>
	                    </tr>
	                    <tr style="margin: 5px;">
	                    	<td  style="text-align: right;" class="must_entry_caption" ><b>Issue Basis</b></td>
	                    	<td>
                            <? echo create_drop_down("cbo_receive_basis",140,$receive_basis_arr,"",0,"- Select Basis -",4,"","1","4"); ?>
 	                        </td>
	                        <td style="text-align:right;" class="must_entry_caption" ><b>Requisition For</b></td>
	                    	<td >
                                 <? echo create_drop_down( "cbo_method", 140, $dyeing_method,"", 1, "--Select Method--", 180, "",1,"0,180" ); ?>
	                        </td>
	                        <td style="text-align:right;" class="must_entry_caption"><b>Style</b></td>
	                    	<td >
	                    		<input type="text" id="txt_buyer_style" name="txt_buyer_style" class="text_boxes_numeric" style="width: 130px" onDblClick="fnc_embel_style_id(1);" placeholder="Double Click For Style" readonly />
	                        </td>
	                        <td  class="must_entry_caption" style="text-align:right;" ><b>Store Name</b></td>
                             <td id="store_td"><? echo create_drop_down( "cbo_store_name", 140, "$storeName","id,store_name", 1, "-- Select Store --", $storeName, "",0 ); ?></td>
	                    </tr>
	            	</table>
	            	<table cellpadding="0" cellspacing="1" width="100%">
                    <tr> 
                    	<td colspan="6" align="center"></td>				
                    </tr>
                    <tr> 
                    	<td colspan="6" align="center"> <div id="list_container_recipe_items" style="margin-top:10px"></div></td>
                    	<td colspan="6" align="center"> <div id="list_container_update_items" style="margin-top:10px"></div></td>				
                    </tr>

                    <tr>
		                <td align="center" colspan="12" valign="middle" class="button_container">
		                    <? echo load_submit_buttons($permission, "fnc_dry_wash_issue_requisition_entry", 0,0,"reset_form('drywashissuerequisition_1','list_fabric_desc_container*dry_production_list_conainer','','','');",1); ?>
		                    <input type="button" name="print" id="print" value="Print" onClick="fnc_dry_wash_issue_requisition_entry(4)" style="width:100px;display:none;" class="formbuttonplasminus" /> 
		                </td>	  
		            </tr>
                </table>  
            	</fieldset>
            	<div style="width:890px;" id="list_container_yarn"></div> 
	        </div>
	    </form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
	$('#txt_requisition_date').val(today);
</script> 
</html>