<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create for Commercial Office Note entry
					
Functionality	:	
				

JS Functions	:

Created by		:	Tipu 
Creation date 	: 	17-10-2019 
Updated by 		: 	
Update date		: 	  	   

QC Performed BY	:		

QC Date			:	

Comments		: 

*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Pre Export Finance Form", "../../", 1, 1,'','1','');
?>	
 
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission='<? echo $permission; ?>';


	function openmypage_pi()
	{
		var item_category = $('#txt_hidden_item_category').val(); 
		var cbo_importer_id = $("#cbo_importer_id").val();
	 	var txt_hidden_pi_id = $("#txt_hidden_pi_id").val();
		var update_id = $("#update_id").val();	  
		if (form_validation('cbo_importer_id','Importer Name')==false)
		{
			return;
		}
		//item_category_id='+item_category
		//var page_link='requires/commercial_office_note_controller.php?action=pi_popup&cbo_importer_id='+cbo_importer_id+'&txt_hidden_pi_id='+txt_hidden_pi_id+'&update_id='+update_id;

		var page_link = 'requires/commercial_office_note_controller.php?item_category_id='+item_category+'&txt_hidden_pi_id='+txt_hidden_pi_id+'&update_id='+update_id+'&cbo_importer_id='+cbo_importer_id+'&action=pi_popup';

		var title='PI Selection Form';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1015px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{ 
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window 
			var pi_id=this.contentDoc.getElementById("txt_selected_id").value; 
			var pi_no=this.contentDoc.getElementById("txt_selected").value;
			var txt_item_category=this.contentDoc.getElementById("txt_item_category").value;
			var txt_pi_entry_form=this.contentDoc.getElementById("txt_pi_entry_form").value;
			
			//alert(pi_id+'='+pi_no+'='+txt_item_category+'='+txt_pi_entry_form);return;

			$('#cbo_importer_id').attr("disabled",true); 
            $('#cbo_item_category_id').attr("disabled",true);
			if (pi_id!="")
			{ 
				$('#txt_hidden_pi_id').val(pi_id);
				$('#txt_pi').val(pi_no);
				$('#txt_hidden_pi_item').val(txt_pi_entry_form);

				get_php_form_data(pi_id, "set_value_pi_select", "requires/commercial_office_note_controller" );

				show_list_view(pi_id+'_'+txt_pi_entry_form+'_'+txt_item_category,'show_pi_details_list','pi_details_list','requires/commercial_office_note_controller','setFilterGrid(\'pi_details_list\',-1)'); 
				//get_php_form_data(pi_id+'_'+txt_pi_entry_form+'_'+txt_item_category, "show_pi_details_list", "requires/commercial_office_note_controller" );
				release_freezing();
			} 
			else
			{
				$('#txt_pi').val('');
				$('#txt_hidden_pi_id').val('');
				$('#txt_hidden_pi_item').val('');
				reset_form('','pi_details_list');
			}			 
		}
	}

	function fnc_com_office_note_entry(operation)
	{
		if (form_validation('cbo_importer_id*cbo_lc_type_id*txt_pi*txt_office_note_date','Importer Name*Buyer Name*LC/SC No*Submission Date')==false )
		{
			return;
		}

		if(operation==3 || operation==4 || operation==5)
		{
			if (form_validation('update_id','Save Data First')==false)
			{
				alert("Save Data First");
				return;
			}

			var item_category=$('#txt_hidden_item_category').val();//return;
			if (operation==4) // Print
			{				
				if (item_category==1 || item_category==2 || item_category==3 || item_category==4)
				{
					print_report( $('#cbo_importer_id').val()+"**"+$('#update_id').val()+"**"+$('#is_approved').val()+"**"+$('#cbo_template_id').val(), "print", "requires/commercial_office_note_controller" ) ;
			 		return;
				}
				else
				{
					alert('Please Select another Print Button!!');
					return;
				}	
				
			}
			else if(operation==3) //print 3
			{
				{				
				if (item_category==1 || item_category==2 || item_category==3 || item_category==4)
				{
					print_report( $('#cbo_importer_id').val()+"**"+$('#update_id').val()+"**"+$('#is_approved').val()+"**"+$('#cbo_template_id').val(), "print3", "requires/commercial_office_note_controller" ) ;
			 		return;
				}
				else
				{
					alert('Please Select another Print Button!!');
					return;
				}	
				
			}
			}
			else // Print 2
			{
				//alert($('#update_id').val());//return;
				if (item_category==1 || item_category==2 || item_category==3 || item_category==4)
				{
					alert('Please Select another Print Button!!');
					return;
				}
				else
				{
					print_report( $('#cbo_importer_id').val()+"**"+$('#update_id').val()+"**"+$('#is_approved').val()+"**"+$('#cbo_template_id').val(), "print2", "requires/commercial_office_note_controller" );
				 	return;
				} 	
			}
		}

		var is_approved=$('#is_approved').val();		
		if(is_approved==1 || is_approved==3)
		{
			alert("Office Note is Approved. So Change Not Allowed");
			return;	
		}

		var j=0; var dataString=''; 
		$("#pi_details_list").find('tbody tr').each(function()
		{
			var piId=$(this).find('input[name="piId[]"]').val();
			var pidtlsId=$(this).find('input[name="pidtlsId[]"]').val();
			var piNumber=$(this).find('input[name="piNumber[]"]').val();
			var compositionItem1=$(this).find('input[name="compositionItem1[]"]').val();
			var compositionPercentage=$(this).find('input[name="compositionPercentage[]"]').val();
			var compositionItem2=$(this).find('input[name="compositionItem2[]"]').val();

			var bodyPartId=$(this).find('input[name="bodyPartId[]"]').val();
			var fabType=$(this).find('input[name="fabType[]"]').val();
			var fabric_construction=$(this).find('input[name="fabric_construction[]"]').val();
			var fab_design=$(this).find('input[name="fab_design[]"]').val();
			var fabric_composition=$(this).find('input[name="fabric_composition[]"]').val();

			var itemDescription=$(this).find('input[name="itemDescription[]"]').val();
			var itemCategoryId=$(this).find('input[name="itemCategoryId[]"]').val();
			var colorId=$(this).find('input[name="colorId[]"]').val();
			var countName=$(this).find('input[name="countName[]"]').val();
			var yarnType=$(this).find('input[name="yarnType[]"]').val();
			var hsCode=$(this).find('input[name="hsCode[]"]').val();
			var uom=$(this).find('input[name="uom[]"]').val();
			var quantity=$(this).find('input[name="quantity[]"]').val();
			var rate=$(this).find('input[name="rate[]"]').val();
			var amount=$(this).find('input[name="amount[]"]').val();

			j++;
			dataString+='&pidtlsId_'+j+'='+pidtlsId+'&piId_'+j+'='+piId+'&piNumber_'+j+'='+piNumber+'&compositionItem1_'+j+'='+compositionItem1+'&compositionPercentage_'+j+'='+compositionPercentage+'&bodyPartId_'+j+'='+bodyPartId+'&fabType_'+j+'='+fabType+'&fabric_construction_'+j+'='+fabric_construction+'&fab_design_'+j+'='+fab_design+'&fabric_composition_'+j+'='+fabric_composition+'&compositionItem2_'+j+'='+compositionItem2+'&itemDescription_'+j+'='+itemDescription+'&itemCategoryId_'+j+'='+itemCategoryId+'&colorId_'+j+'='+colorId+'&countName_'+j+'='+countName+'&yarnType_'+j+'='+yarnType+'&hsCode_'+j+'='+hsCode+'&uom_'+j+'='+uom+'&quantity_'+j+'='+quantity+'&rate_'+j+'='+rate+'&amount_'+j+'='+amount;

		});

		// alert(dataString);return;	

 		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+dataString+get_submitted_data_string('cbo_importer_id*cbo_lc_type_id*txt_pi*txt_office_note_date*txt_hidden_pi_id*txt_hidden_item_category*txt_pi_value*txt_lc_value*hidden_pi_currency_id*txt_last_shipment_date*cbo_supplier_id*cbo_ready_to_approved*txt_hidden_pi_item*txt_tenor*txt_remarks*update_id*txt_system_id*cbo_proposed_bank*txt_exchange_rate*cbo_section',"../../");

		//alert(data);return;

		freeze_window(operation);		
		http.open("POST","requires/commercial_office_note_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_com_office_note_entry_reply_info;
	}


	function fnc_com_office_note_entry_reply_info()
	{
		
		if(http.readyState == 4) 
		{
			// alert(http.responseText);
			var reponse=http.responseText.split('**');	
			if(trim(reponse[0])==20)
			{
				alert(reponse[1]);
				show_msg('13');
				release_freezing();
				return;
			}
			show_msg(trim(reponse[0])); 
			if( reponse[0]==0 || reponse[0]==1)
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_system_id').value = reponse[2];
				//get_php_form_data(trim(reponse[1]), "populate_master_from_data", "requires/commercial_office_note_controller");
				set_button_status(1, permission, 'fnc_com_office_note_entry',1); 
				$('#cbo_importer_id').attr('disabled','true');			 
			}
			//fnResetForm();
			release_freezing();
		}
	}

	function openmypagesystem()
	{
		if (form_validation('cbo_importer_id','Importer Name')==false )
		{
			return;
		}
		var importer_name = $("#cbo_importer_id").val();
		var page_link='requires/commercial_office_note_controller.php?action=system_id_pop&importer_name='+importer_name; 
		var title="Search System Number Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var mstID=this.contentDoc.getElementById("hidden_system_number").value; // master table id
			//alert(mstID);
	  		// master part call here		
			get_php_form_data(mstID, "populate_master_from_data", "requires/commercial_office_note_controller");

			var pi_mst_id = $('#txt_hidden_pi_id').val(); 
			
			get_php_form_data(pi_mst_id, "set_value_pi_select", "requires/commercial_office_note_controller" );

			show_list_view(pi_mst_id,'show_pi_details_list','pi_details_list','requires/commercial_office_note_controller','setFilterGrid(\'pi_details_list\',-1)'); 

			set_button_status(1, permission, 'fnc_com_office_note_entry',1);		
	  	}
	}

	function fnResetForm()
	{
		reset_form('docsubmFrm_1','','','','','');
		$('#cbo_importer_id').attr('disabled',false);
		$('#pi_details_list').find('tr:gt(0)').remove();
		// $('#pi_details_list').append('<tr id="tr0"><td colspan="6"><b><center>Please Select Pro Forma Invoice List</center></b></td></tr>');
 		set_button_status(0, permission, 'fnc_com_office_note_entry',1);	
	}

	function fn_deleteRow(rowNo)
	{
		var index=rowNo-1
		$("table#pi_details_list tbody tr:eq("+index+")").remove()
		var numRow = $('#pi_details_list tbody tr').length;
		for(i = rowNo;i <= numRow;i++){
			$("#pi_details_list tr:eq("+i+")").find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'value': function(_, value) { return value }
				});
				$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deleteRow("+i+",'pi_details_list');");
			});
		}
		calculate_total();
	}

	function calculate_total()
	{
		var total_amont=0;
		var total_roll_weight='';
		$("table#pi_details_list").find('tbody tr').each(function()
		{
			var Amt=$(this).find('input[name="amount[]"]').val();
			//alert(Amt);
			total_amont=total_amont*1+Amt*1;
		});	
		$("#total_amount").text(total_amont.toFixed(2));
	}
</script>
 
 
</head> 
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission); ?><br/>
		<fieldset style="width:930px; margin-bottom:10px;">
			<form name="docsubmFrm_1" id="docsubmFrm_1" autocomplete="off" method="POST"  >
				<!-- 1st form Start here -->
				<fieldset style="width:950px;">
					<legend>Commercial Module</legend>
					<table width="950" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
						<tr>
							<td colspan="6" align="center">System ID &nbsp;<input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" placeholder="Double Click to Update" onDblClick="openmypagesystem();" readonly /></td>
							<input type="hidden" name="update_id" id="update_id" value=""/> 
						</tr>
						<tr>
							<td colspan="6" align="center">&nbsp;</td>
						</tr>
						<tr>
							<td  width="130" align="right" class="must_entry_caption">Importer</td>
							<td width="170">
								<?
								echo create_drop_down( "cbo_importer_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "" );//load_drop_down( 'requires/commercial_office_note_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );
								?>
							</td>
							<td  width="130" align="right" class="must_entry_caption">Office Note Date</td>
							<td width="170">
								<input style="width:140px " name="txt_office_note_date" id="txt_office_note_date" class="datepicker"  placeholder="Select Date" />
							</td>
							<td width="130" align="right">Proposed Bank</td>
							<td width="160">
								<?php
									if ($db_type==0)
									{
										echo create_drop_down("cbo_proposed_bank", 150,"select id,concat(a.bank_name,' (', a.branch_name,')') as bank_name from lib_bank where is_deleted=0 and status_active=1 and issusing_bank = 1 order by bank_name",'id,bank_name', 1, '----Select----',0,0,0); 
									}
									else
									{ 
										echo create_drop_down("cbo_proposed_bank", 150,"select id,(bank_name || ' (' || branch_name || ')' ) as bank_name from lib_bank where is_deleted=0 and status_active=1 and issusing_bank = 1 order by bank_name",'id,bank_name', 1, '----Select----',0,0,0); 
									}
								?>
							</td>
						</tr>
						<tr>
							<td width="130" align="right" class="must_entry_caption">LC Type</td>
							<td width="170" id="buyer_td">
								<?
								echo create_drop_down( "cbo_lc_type_id",150,$lc_type,'',1,'-Select',1,"",0);
								?>
							</td>
							<td width="130" align="right">Exchange Rate</td>
							<td width="170">
								<input type="text" name="txt_exchange_rate" id="txt_exchange_rate" class="text_boxes" style="width:140px" />
							</td>
							<td  width="130" align="right">Section</td>
							<td width="170">
								<?php echo create_drop_down("cbo_section", 151,"select id, section_name from lib_section where status_active=1 and is_deleted=0",'id,section_name', 1, '----Select----',0,0,0); 
								?>
							</td>
						</tr>
						<tr>
							<td width="130" align="right" class="must_entry_caption">Pro Forma Invoice</td>
							<td width="170">
								<input type="text" name="txt_pi" id="txt_pi" class="text_boxes" style="width:140px" placeholder="Double Click to Search"  readonly onDblClick="openmypage_pi();" />
								<input type="hidden" name="txt_hidden_pi_id" id="txt_hidden_pi_id" readonly />
								<input type="hidden" name="txt_hidden_item_category" id="txt_hidden_item_category" readonly />
								<input type="hidden" name="txt_pi_value" id="txt_pi_value" readonly />
								<input type="hidden" name="txt_lc_value" id="txt_lc_value" readonly />
								<input type="hidden" name="hidden_pi_currency_id" id="hidden_pi_currency_id" readonly />
								<input type="hidden" name="txt_last_shipment_date" id="txt_last_shipment_date" readonly />
								<input type="hidden" name="cbo_supplier_id" id="cbo_supplier_id" readonly />
								<input type="hidden" name="txt_hidden_pi_item" id="txt_hidden_pi_item" readonly />
							</td>
							<td  width="130" align="right">Tenor</td>
							<td width="170">
								<input type="text" name="txt_tenor" id="txt_tenor" class="text_boxes_numeric" style="width:140px" />
							</td>					
							<td  width="130" align="right">Remarks</td>
							<td> <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:140px" /></td>
						</tr>
						<tr>
							<td  width="130" align="right">Ready To Approve</td>
							<td width="170">
								<input type="hidden" name="is_approved" id="is_approved" value="">
								<? echo create_drop_down( "cbo_ready_to_approved", 151, $yes_no,"", 1, "-- Select--", 2, "","","" ); ?>
							</td>
							<td width="130"></td>
							<td width="170"></td>
							<td width="130"></td>
                        	<td width="170">
                                <input type="button" id="image_button" class="image_uploader" style="width:151px;" value="CLICK TO ADD FILE" onClick="file_uploader('../../', document.getElementById('txt_system_id').value, '', 'Commercial Office Note', 2, 1)" />
                        	</td>
                        </tr>
					</table>
				</fieldset>
				<br/>
					<div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div>
				<br/>
				<fieldset style="width:1050px; margin-top:10px;">
                    <legend>PI Item List</legend>
                    <div id="pi_details_list" style="max-height:200px; overflow:auto;"></div>
                </fieldset>
				
				<table cellpadding="0" cellspacing="1" width="100%">
					<tr>
						<td colspan="8" align="center"></td>
					</tr>
					<tr>
						<td align="center" colspan="8" valign="middle" class="button_container">
							<? echo create_drop_down( "cbo_template_id", 100, $report_template_list,'', 0, '', 0, "");?>&nbsp;
							<? echo load_submit_buttons( $permission, "fnc_com_office_note_entry", 0,1 ,"fnResetForm();",1); ?>
							<? //echo load_submit_buttons( $permission, "fnc_com_office_note_entry", 0,0,"fnResetForm()",1);?>
							<input type="button" class="formbutton" id="btn_print_letter" value="Print 2" style="width:100px;" onClick="fnc_com_office_note_entry(5)" >
							<input type="button" class="formbutton" id="btn_print_letter" value="Print 3" style="width:100px;" onClick="fnc_com_office_note_entry(3)" >
						</td>
					</tr>
				</table>
				
			</form>
		</fieldset>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>