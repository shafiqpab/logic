<?
/*-------------------------------------------- Comments
Purpose			:	This form will create Sample Requisition for Woven Textile
Functionality	:
JS Functions	:
Created by		:	Md. Helal Uddin
Creation date 	: 	20-12-2022
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
echo load_html_head_contents("Hand loom Requisition", "../../", 1, 1,$unicode,'','');
/*echo '<pre>';
print_r($_SESSION['logic_erp']['mandatory_field'][434]); die;*/
?>

<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';

	var department = [<? echo substr(return_library_autocomplete( "select department_name from wo_quotation_inquery where  status_active=1 and is_deleted=0 group by department_name", "department_name" ), 0, -1); ?>];
	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color where status_active=1 and is_deleted=0 order by color_name ASC", "color_name" ), 0, -1); ?>];

	$(document).ready(function(e)
	 {
			$("#txt_department").autocomplete({
			 source: department
		  });
	 });

	function fnc_quotation_inquery( operation )
	{
		freeze_window(operation);
		if(operation==4)
		{
			if(form_validation('cbo_company_name*txt_system_id','Select Company*System ID')==false)
			{
				release_freezing();
				return;
			}
	
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'**'+$('#txt_system_id').val()+'**'+$('#update_id').val()+'**'+$('#cbo_buyer_name').val()+'**'+$('#txt_style_ref').val()+'**'+report_title, "inquery_entry_print2", "requires/sample_requisition_woven_textile_controller" )
			release_freezing();
			return;
		}
		var cbo_basis = $("#cbo_basis").val() * 1;
		if(cbo_basis == 2)
		{
			var field_id =  'cbo_company_name*cbo_location_name';
			var message_name = 'Company*Location';
		}
		else
		{
			var field_id =  'txt_buyer_inquiry_no*cbo_company_name*cbo_location_name';
			var message_name = 'Buyer Inquiry No*Company*Location';
		}
		if (form_validation(field_id,message_name)==false)
		{
			release_freezing();
			return;
		}
		else // Save Here
		{
			if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][585 ]);?>'){
				if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][585]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][585 ]);?>')==false)
				{
					release_freezing();
					return;
				}
			}
			
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_system_id*update_id*txt_buyer_inquiry_no*txt_buyer_inquiry_id*cbo_company_name*cbo_location_name*txt_style_ref*cbo_within_group*cbo_buyer_name*cbo_brand*cbo_season_name*cbo_season_year*cbo_team_leader*cbo_dealing_merchant*txt_delivery_date*cbo_ready_to_approved*txt_requisition_date*txt_remarks*cbo_basis*cbo_requisition_type',"../../");
			 /*alert(data);
			 release_freezing();
			return;*/	 
			//
			http.open("POST","requires/sample_requisition_woven_textile_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_quotation_inquery_reponse;
		}
	}

	function fnc_quotation_inquery_reponse()
	{
		if(http.readyState == 4)
		{
			//alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			
	
			if(reponse[0]==0 )
			{
			   show_msg(reponse[0]);
			   $("#txt_system_id").val(reponse[1]);
			   $("#update_id").val(reponse[2]);
			   
			   set_button_status(1, permission, 'fnc_quotation_inquery',1,1);
			}
			if(reponse[0]==1 )
			{
				show_msg(reponse[0]);
			}
			if(reponse[0]==10 )
			{
				show_msg(reponse[0]);
			}
			if(reponse[0]==2)
			{
				show_msg(reponse[0]);
				reset_form('quotationinquery_1','','');
			}
			release_freezing();
		}
	}

	function open_mrrpopup()
	{
		//reset_form('','list_container_recipe_items*recipe_items_list_view','','','','');

		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();
		var page_link='requires/sample_requisition_woven_textile_controller.php?action=mrr_popup&company='+company;
		var title="Search  Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1180px,height=400px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];

			var mrrNumber=this.contentDoc.getElementById("hidden_issue_number").value; // mrr number
			mrrNumber = mrrNumber.split("_");
			//var mrrId=this.contentDoc.getElementById("issue_id").value; // mrr number

			$("#txt_system_id").val(mrrNumber[0]);
			$("#update_id").val(mrrNumber[1]);
			
			button_status();
			get_php_form_data(mrrNumber[0], "populate_data_from_data", "requires/sample_requisition_woven_textile_controller");
			fnc_load_tr(document.getElementById("txt_buyer_inquiry_id").value,mrrNumber[1]);

			set_button_status(1, permission, 'fnc_quotation_inquery',1,1);
		}
	}

	function open_inquery_popup()
	{

		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();
		var page_link='requires/sample_requisition_woven_textile_controller.php?action=inquery_popup&company='+company;
		var title="Search  Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1180px,height=400px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];

			var mrrNumber=this.contentDoc.getElementById("hidden_issue_number").value; // mrr number
			mrrNumber = mrrNumber.split("_");
			//var mrrId=this.contentDoc.getElementById("issue_id").value; // mrr number

			$("#txt_buyer_inquiry_no").val(mrrNumber[0]);
			$("#txt_buyer_inquiry_id").val(mrrNumber[1]);

			get_php_form_data(mrrNumber[0], "populate_data_from_inquiry", "requires/sample_requisition_woven_textile_controller");
			fnc_load_tr(mrrNumber[1]);

			set_button_status(0, permission, 'fnc_quotation_inquery',1,1);
		}
	}


	function buyer_season_load()
	{
		var company = $("#cbo_company_name").val();
		var cbo_buyer_name = $("#cbo_buyer_name").val();
		load_drop_down( 'requires/sample_requisition_woven_textile_controller', cbo_buyer_name+"_"+company, 'load_drop_down_season_buyer', 'season_td' );
	}
	function check_quatation()
	{
		var txt_style_ref=$('#txt_style_ref').val();
		var txt_inquery_id=$('#txt_system_id').val();
		var response=return_global_ajax_value( txt_style_ref+"**"+txt_inquery_id, 'check_style_ref', '', 'requires/sample_requisition_woven_textile_controller');
		response=trim(response).split('**');
		if(response[0]==1)
		{
			var r=confirm("Following quotation id found against ' "+ txt_style_ref +" ' style ref.\n"+response[1]+". \n If you want to continue press Ok, otherwise press Cancel");
			if(r==false)
			{
				$('#txt_style_ref').val('')
				return;
			}
			else
			{
				//continue;
			}
		}
	}
	function color_id_reset()
	{
		$('#txt_color_id').val('');
	}
	function fnc_variable_settings_check(company_id)
	{
		$('#txt_color').val('');
		$('#txt_color_id').val('');
		var color_from_lib=return_ajax_request_value(company_id, 'load_variable_settings', 'requires/sample_requisition_woven_textile_controller');
		if(color_from_lib==1)
		{
			$('#txt_color_id').val('');
			$('#txt_color').attr('readonly',true);
			$('#txt_color').attr('placeholder','Browse');
			$('#txt_color').removeAttr("onDblClick").attr("onDblClick","color_select_popup()");
		}
		else
		{
			$('#txt_color_id').val('');
			$('#txt_color').attr('readonly',false);
			$('#txt_color').attr('placeholder','Write');
			$('#txt_color').removeAttr('onDblClick','onDblClick');
		}
	}
	function color_select_popup()
	{
		var buyer_name=$('#cbo_buyer_name').val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sample_requisition_woven_textile_controller.php?action=color_popup&buyer_name='+buyer_name, 'Color Select Pop Up', 'width=250px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var color_name=this.contentDoc.getElementById("color_name");
			var color_id=this.contentDoc.getElementById("color_id");
			if (color_name.value!="")
			{
				$('#txt_color').val(color_name.value);
				$('#txt_color_id').val(color_id.value);

			}
		}
	}

	function reset_fnc()
	{
		reset_form('','','txt_system_id*update_id*txt_buyer_inquiry_no*txt_buyer_inquiry_id*cbo_company_name*cbo_location_name*txt_style_ref*cbo_within_group*cbo_buyer_name*cbo_brand*cbo_season_name*cbo_season_year*cbo_team_leader*cbo_dealing_merchant*txt_delivery_date*cbo_ready_to_approved*txt_requisition_date*txt_remarks','','');
		set_button_status(0, permission, 'fnc_quotation_inquery',1,1);
	}

	function button_status()
	{
		var dtls_row = `<tr id="tr_1" style="height:10px;" class="general">
								<td> 
									<input type="checkbox" id="checkBox_1"  name="checkBox_1"   />
								</td>
								
								<td >
									<input type="text" id="txtconstruction_1"  name="txtconstruction_1" class="text_boxes" style="width:90px" placeholder="Browse" ondblclick="openmypage_fabric_cons(1)" readonly value="" />
                					<input type="hidden" id="fabConstructionId_1"  name="fabConstructionId_1"  value="" />
                					<input type="hidden" id="fabConstruction_1"  name="fabConstruction_1"  value="" />
                					<input type="hidden" id="yarnCountDeterminationId_1"  name="yarnCountDeterminationId_1"  value="" />
								</td>
								<td> 
									<input type="text" id="yarnComposition_1"  name="yarnComposition_1" class="text_boxes" value=""  disabled/>
								</td>
								<td >
									<? 

										echo create_drop_down("cboProductType_1", 100, $color_type, "", 1, "Select", 0, "");
									?>
										
								</td>
								<td >
									<input type="text" id="txtcompone_1"  name="txtcompone_1"  class="text_boxes" style="width:90px" value="" readonly placeholder="Browse" onDblClick="openmypage_comp(1);" />
                            		<input type="hidden" id="cbocompone_1"  name="cbocompone_1" class="text_boxes" style="width:50px" value="" />
								</td>
								<td>
									<input style="width:90px;" type="text" class="text_boxes"  name="txtWeaveDesign_1" id="txtWeaveDesign_1" placeholder="Write" />
								</td>
								<td >
									<? 
										$finish_types = array(1=>"Regular",2=>"Peach",3=>"Brush");
										echo create_drop_down("cboFinishType_1", 70, $finish_types, "", 1, "Select", 0, "");
									?>
										
								</td>
								<td id="color_1">
									<input style="width:70px;" type="text" class="text_boxes"  name="txtColor_1" id="txtColor_1" onkeyup="show_color(1)" placeholder="Write" />
									<input  type="hidden"  name="cboColorId_1" id="cboColorId_1"  />
								</td>
								<td >
									<input style="width:70px;" type="text" class="text_boxes"  name="txtFabricWeight_1" id="txtFabricWeight_1" placeholder="Write" />
								</td>
								<td >
									<? echo create_drop_down( "cboweighttype_1", 80, $fabric_weight_type,"", 1, "-- Select --", '', "",$disabled,"" ); ?>
								</td>
								<td >
									<input style="width:70px;" type="text" class="text_boxes"  name="txtFinishedWidth_1" id="txtFinishedWidth_1" placeholder="Write"  />
								</td>
								<td >
									<input style="width:70px;" type="text" class="text_boxes" placeholder="Write"  name="txtCutableWidth_1" id="txtCutableWidth_1" />
								</td>
								<td >
									<? 
										$wash_types = array(1=>"Wash",2=>"Non-Wash",3=>"Garmnets Wash",4=>"Enzyme Wash");
										echo create_drop_down("cboWashType_1", 70, $wash_types, "", 1, "Select", 0, "");
									?>
								</td>
                                
								<td>
									<input type="text" class="text_boxes_numeric" name="txtOfferQty_1" id="txtOfferQty_1" placeholder="Write"  style="width:70px;" onkeyup="calculate_amount(1)">
								</td>
								<td >
									<?=create_drop_down("cboUom_1", 60, $unit_of_measurement, "", "", "", 2, "", "", "23,27");?>	
								</td>
                                
								
								
								<td>
									<input type="text" class="text_boxes_numeric" name="txtBuyerTgtPrice_1" id="txtBuyerTgtPrice_1" placeholder="Write"  style="width:70px;" onkeyup="calculate_amount(1)">
								</td>
								<td>
									<input type="text" class="text_boxes_numeric" name="txtAmount_1" id="txtAmount_1" placeholder="Write"  style="width:70px;" >
								</td>
								<td>
									<input type="text" class="text_boxes" name="txtHlNo_1" id="txtHlNo_1" readonly  style="width:70px;" >
									<input type="hidden" id="inquiryDtlsId_1"  name="inquiryDtlsId_1"  value="" />
								</td>
								<td>
									<input type="text" class="text_boxes" name="txtRemark_1" id="txtRemark_1" placeholder="Write"  style="width:90px;" >
								</td>
								<td>
									<input type="hidden" id="updateDtlsId_1" name="updateDtlsId_1" class="text_boxes" style="width:20px"/>
									<input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1);" />
									<input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
								</td>
							</tr>`;
		$("#details_container").html(dtls_row);
		set_button_status(0, permission, 'fnc_details_info',2);
	}

	function active_inactive() 
	{
    	var within_group = $('#cbo_within_group').val();
    	var company_id = document.getElementById('cbo_company_name').value;
    	load_drop_down('requires/sample_requisition_woven_textile_controller', within_group + '_' + company_id, 'load_drop_down_buyer', 'buyer_td');

    	if(within_group == 1)
    	{
    		load_drop_down( 'requires/sample_requisition_woven_textile_controller', ''+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_season_buyer', 'season_td');
    		load_drop_down( 'requires/sample_requisition_woven_textile_controller', '', 'load_drop_down_brand', 'brand_td');
    	}
    }


	function openmypage_fabric_cons(inc)
	{
		var fab_construction_id = $('#fabConstructionId_'+inc).val();
		var checkBox = $('#checkBox_'+inc).val();
		if(document.getElementById('checkBox_'+inc).checked)
		{
			var action = "fabric_determination_popup";
			var title = "Material Determination Popup";
			var width = 'width=1080px,height=350px,center=1,resize=1,scrolling=0';
		}
		else
		{
			var action = "fabric_construction_popup";
			var title = "Material Construction Popup";
			var width = 'width=880px,height=350px,center=1,resize=1,scrolling=0';
		}
		var page_link="requires/sample_requisition_woven_textile_controller.php?action="+action+"&fab_construction_id="+fab_construction_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title,width ,'../');
		emailwindow.onclose=function()
		{
			if(document.getElementById('checkBox_'+inc).checked)
			{
				var construction_id 	= this.contentDoc.getElementById("construction_id").value;
				var construction 		= this.contentDoc.getElementById("construction").value;
				var determination_id 	= this.contentDoc.getElementById("determination_id").value;
				$('#fabConstructionId_'+inc).val(construction_id);
				$('#txtconstruction_'+inc).val(construction);
				$('#yarnCountDeterminationId_'+inc).val(determination_id);

				get_php_form_data(determination_id+'_'+inc, "populate_data_from_determination", "requires/sample_requisition_woven_textile_controller");
			}
			else
			{
				var hidfabconspid=this.contentDoc.getElementById("hidfabconspid").value;
				var hidfabconsname=this.contentDoc.getElementById("hidfabconsname").value;
				var fab_construction=this.contentDoc.getElementById("fab_construction").value;
				$('#fabConstructionId_'+inc).val(hidfabconspid);
				$('#txtconstruction_'+inc).val(hidfabconsname);
				$('#fabConstruction_'+inc).val(fab_construction);
			}
			
			
		}
	}

	function show_color(i)
	{
		$( "#txtColor_"+i ).autocomplete({
			 source: function( request, response ) {
				  var matcher =  new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
				  response( $.grep( str_color, function( item ){
					  return matcher.test( item );
				  }) );
			  }
		});
	}

	function add_break_down_tr(i)
	{
		var row_num=$('#tbl_details tbody tr').length;
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
			var k=i-1;
			$("#tbl_details tbody tr:last").clone().find("input,select").each(function(){
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
					'value': function(_, value) { return '' }
				});
			}).end().appendTo("#tbl_details");

			$('#txtconstruction_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_fabric_cons('"+i+"');");
			$('#txtcompone_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_comp('"+i+"');");
			$('#txtColor_'+i).removeAttr("onDblClick").attr("onDblClick","show_color('"+i+"');");
			$('#txtWarpYarnType_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_yarnType(1,"+i+");");
			$('#txtWeftYarnType_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_yarnType(2,"+i+");");
			
			$('#increase_'+i).removeAttr("value").attr("value","+");
			$('#decrease_'+i).removeAttr("value").attr("value","-");
			$('#decrease_'+i).removeAttr("disabled");
			$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
			$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
			set_all_onclick();
		}
	}

	function fn_deleteRow(rowNo)
	{
		var index=rowNo-1;
		$("table#tbl_details tbody tr:eq("+index+")").remove();
		var numRow = $('table#tbl_details tbody tr').length;
		for(i = rowNo;i <= numRow;i++)
		{
			$("#tbl_details tr:eq("+i+")").find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
					'value': function(_, value) { return value }
				});

				$('#txtconstruction_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_fabric_cons('"+i+"');");
				$('#txtcompone_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_comp('"+i+"');");
				$('#txtColor_'+i).removeAttr("onDblClick").attr("onDblClick","show_color('"+i+"');");
				$('#txtWarpYarnType_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_yarnType(1,"+i+");");
				$('#txtWeftYarnType_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_yarnType(2,"+i+");");
				$('#increase_'+i).removeAttr("value").attr("value","+");
				$('#decrease_'+i).removeAttr("value").attr("value","-");
				$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
				$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
				set_all_onclick();

			});
        }
	}

	function openmypage_comp(inc)
	{
		var page_link="requires/sample_requisition_woven_textile_controller.php?action=composition_popup&inc="+inc;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Composition Popup", 'width=480px,height=350px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var hidcompid=this.contentDoc.getElementById("hidcompid").value;
			var hidcompname=this.contentDoc.getElementById("hidcompname").value;
			$('#cbocompone_'+inc).val(hidcompid);
			$('#txtcompone_'+inc).val(hidcompname);
			check_duplicate(inc,1);
		}
	}


	function fnc_details_info( operation )
	{
		var update_id=$('#update_id').val();
		if(update_id=="")
		{
			alert("save master part!!");
			return;
		}
		else
		{
			var row_num=$('#tbl_details tr').length-1;
			var data_all="";
			for (var i=1; i<=row_num; i++)
			{
				if (form_validation('txtconstruction_'+i+'*cboProductType_'+i+'*txtcompone_'+i+'*txtWeave_'+i+'*txtDesign_'+i+'*cboFinishType_'+i+'*txtColor_'+i+'*txtFinishedWidth_'+i+'*txtCutableWidth_'+i+'*cboWashType_'+i+'*cboUom_'+i+'*txtWarpYarnType_'+i+'*txtWeftYarnType_'+i,'Construction*Product Type*Composition*Weave*Design*Finish Type*Color*Finished Width*Cutable Width*Wash Type*Uom*Warp Yarn Type*Weft Yarn Type_')==false)
				{
					return;
				}

				data_all=data_all+get_submitted_data_string('fabConstructionId_'+i+'*txtconstruction_'+i+'*fabConstruction_'+i+'*cboProductType_'+i+'*txtcompone_'+i+'*cbocompone_'+i+'*cboFinishType_'+i+'*txtColor_'+i+'*txtFabricWeight_'+i+'*cboweighttype_'+i+'*txtFinishedWidth_'+i+'*txtCutableWidth_'+i+'*cboWashType_'+i+'*txtOfferQty_'+i+'*cboUom_'+i+'*txtBuyerTgtPrice_'+i+'*txtAmount_'+i+'*updateDtlsId_'+i+'*cboColorId_'+i+'*txtHlNo_'+i+'*inquiryDtlsId_'+i+'*yarnCountDeterminationId_'+i+'*txtRemark_'+i+'*hiddWarpYarnTypeId_'+i+'*hiddWeftYarnTypeId_'+i+'*txtWeave_'+i+'*txtDesign_'+i,"../../");
			}
			 

			var data="action=save_update_delete_dtls&operation="+operation+'&total_row='+row_num+'&update_id='+update_id+data_all;
			   //alert(data); return;
		   freeze_window(operation);
		   http.open("POST","requires/sample_requisition_woven_textile_controller.php", true);
		   http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		   http.send(data);
		   http.onreadystatechange = fnc_details_info_reponse;
		}
	}

	function fnc_details_info_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if(trim(reponse[0])=='appMsg')
			{
				alert(trim(reponse[1]));
				release_freezing();
				return;
			}
			if(reponse[0]==0)
			{
				show_msg(reponse[0]);
				fnc_load_tr(document.getElementById("txt_buyer_inquiry_id").value,reponse[1]);
			}

			if(reponse[0]==1 )
			{
				show_msg(reponse[0]);
				fnc_load_tr(document.getElementById("txt_buyer_inquiry_id").value,reponse[1]);
			}
			if(reponse[0]==2)
			{

				show_msg(reponse[0]);
				button_status();
			}
			else if(reponse[0]==10 )
			{
				show_msg(reponse[0]);
			}
			release_freezing();
		}
	}

	function fnc_load_tr(inquiry_id,up_id='') 
	{
		var data=inquiry_id+'**'+up_id;
		var return_data = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/sample_requisition_woven_textile_controller');
		return_data = return_data.split("**##**");
		var list_view_tr = return_data[0];
		var status = return_data[1];	

		if(list_view_tr=="" || list_view_tr==0)
		{
			set_button_status(0, permission, 'fnc_details_info',2);
			return;
		}
		else(list_view_tr!='')
		{
			$("#details_container tr").remove();
			$("#details_container").append(list_view_tr);
			set_all_onclick();
		 	set_button_status(status, permission, 'fnc_details_info',2);
				return;
		}

		return;
	}
	function inquiry_inactive(basis)
	{
		if(basis == 1)
		{
			$('#txt_buyer_inquiry_no').removeAttr("disabled");
		}
		else
		{
			$('#txt_buyer_inquiry_no').removeAttr("disabled").attr("disabled","disabled");
		}
	}
	function calculate_amount(row)
	{
		var txtBuyerTgtPrice = $(`#txtBuyerTgtPrice_${row}`).val() * 1;
		var txtOfferQty = $(`#txtOfferQty_${row}`).val() * 1;
		var amount = txtBuyerTgtPrice * txtOfferQty;
		$(`#txtAmount_${row}`).val(amount);
	}

	function openmypage_yarnType(type,row)
	{
	 
		if(type==1){
			var hiddYarnTypeId = $('#hiddWarpYarnTypeId_'+row).val();
		}else{
			var hiddYarnTypeId = $('#hiddWeftYarnTypeId_'+row).val();
		}

 
		var title = 'Tag Yarn  Selection Form';
		var page_link='requires/sample_requisition_woven_textile_controller.php?action=yarn_type_popup&hiddYarnTypeId='+hiddYarnTypeId+'&type='+type;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var buyer_id=this.contentDoc.getElementById("hidden_buyer_id").value;	 //Access form field with id="emailfield"
			var buyer_name=this.contentDoc.getElementById("hidden_buyer_name").value;
			if(type==1){
				$('#hiddWarpYarnTypeId_'+row).val(buyer_id);				
				$('#txtWarpYarnType_'+row).val(buyer_name);
			}else{
				$('#hiddWeftYarnTypeId_'+row).val(buyer_id);				
				$('#txtWeftYarnType_'+row).val(buyer_name);
			}

		}
	}
	function link_popup()
	{
		var update_id=$('#update_id').val();
		if(update_id=="")
		{
			alert("save master part!!");
			return;
		}
		else
		{
			var page_link="requires/sample_requisition_woven_textile_controller.php?action=link_popup&update_id="+update_id;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Link Popup", 'width=680px,height=350px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				
			}
		}
	}
</script>
</head>
<body onLoad="set_hotkey(); buyer_season_load();">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",$permission);  ?>
        <fieldset style="width:1800px;">
            <legend>Hand loom Requisition</legend>
            <form name="quotationinquery_1" id="quotationinquery_1" autocomplete="off">
                <table  width="1020" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td colspan="8" style="justify-content:center;text-align: center;" align="center">
                            System ID<input style="width:140px;" type="text" title="Double Click to Search" onDblClick="open_mrrpopup();" class="text_boxes" placeholder="Browse" name="txt_system_id" id="txt_system_id" readonly />
                            <input type="hidden" name="update_id" id="update_id" />
                        </td>
                    </tr>
                    <tr>
                    	<td width="100" class="must_entry_caption" align="right">Basis</td>
                    	<td width="150">
                        	<?
                        		$handloom_basis = array(1=>"Inquiry",2=>"RND");
								echo create_drop_down("cbo_basis", 140, $handloom_basis, "", 0, "--  --", 1, "inquiry_inactive(this.value);");
							?>
                        </td><td width="100" class="must_entry_caption" align="right">Requisition Type</td>
                    	<td width="150">
                        	<?
                        		$requisition_type = array(1=>"SPO",2=>"SRO");
								echo create_drop_down("cbo_requisition_type", 140, $requisition_type, "", 0, "--  --", 1, "inquiry_inactive(this.value);");
							?>
                        </td>
						<td width="100" class="must_entry_caption" align="right">Company</td>
                        <td width="150">
                        	<? 
                        	$com_sql = "select comp.id,comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and core_business not in(3) $company_cond order by company_name";
                        	
                        	echo create_drop_down( "cbo_company_name", 140,$com_sql,"id,company_name", 1, "-- Select Company --", $selected,"load_drop_down( 'requires/sample_requisition_woven_textile_controller', $('#cbo_within_group').val()+'_'+this.value, 'load_drop_down_buyer', 'buyer_td');load_drop_down( 'requires/sample_requisition_woven_textile_controller', this.value, 'load_drop_down_season_com', 'season_td'); fnc_variable_settings_check(this.value);load_drop_down( 'requires/sample_requisition_woven_textile_controller', this.value, 'load_drop_down_location', 'location_td' );" ,0); ?>
                        		
                        </td>
                    	<td width="100" class="must_entry_caption" align="right">Inquiry ID/ AR No</td>
                    	<td width="150">
                    		<input style="width:130px;" type="text" title="Double Click to Search" onDblClick="open_inquery_popup();" class="text_boxes" placeholder="Browse" name="txt_buyer_inquiry_no" id="txt_buyer_inquiry_no" readonly />
                    		<input type="hidden" name="txt_buyer_inquiry_id" id="txt_buyer_inquiry_id" />
                    	</td>
                        
                        
                        
                        
                        
                    </tr>
                    <tr>
                    	<td width="100" class="must_entry_caption" align="right">Style Ref. </td>
                        <td width="150" >
                        	<input class="text_boxes" type="text" style="width:130px" placeholder="Write"  name="txt_style_ref" id="txt_style_ref" onBlur="check_quatation();"/>
                        </td>
                    	<td width="100"  align="right">Within Group</td>
                        <td width="150">
                        	<?
								echo create_drop_down("cbo_within_group", 140, $yes_no, "", 0, "--  --", 2, "active_inactive();");
							?>
                        </td>
                    	<td width="100" class="must_entry_caption" align="right">Buyer </td>
                        <td id="buyer_td">
                        	<? echo create_drop_down( "cbo_buyer_name", 140, "select id,buyer_name from lib_buyer  where status_active =1 and is_deleted=0  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/sample_requisition_woven_textile_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_season_buyer', 'season_td');load_drop_down( 'requires/sample_requisition_woven_textile_controller', this.value, 'load_drop_down_brand', 'brand_td');" ,0); ?>
                        		
                        </td>
                        <td align="right">Brand</td>
                        <td id="brand_td">
                        	<? echo create_drop_down( "cbo_brand", 140, $blank_array,"",1, "-Brand-", $selected,""); ?>
                        	
                        </td>
                       
						
                        
                    </tr>

                    <tr>
						<td width="100" class="must_entry_caption" align="right">Location</td>
						<td id="location_td">
							<?
							echo create_drop_down( "cbo_location_name", 140, "select id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name",'id,location_name', 1, '--- Select Location ---', 0, ""  );
							?>
						</td>
                    	 <td align="right">Season</td>
                        <td  id="season_td">
                        	<? echo create_drop_down( "cbo_season_name", 140, $blank_array,"", 1, "-Season-", $selected, "" ); ?>
                        		
                        </td>
                    	<td align="right">Season Year:</td>
						<td>
							<? echo create_drop_down( "cbo_season_year", 140, create_year_array(),"", 1,"-All-", 1, "",0,"" ); ?>
						</td>
                        
						<td align="right" class="must_entry_caption">Team leader</td>
                       	<td>
                       		<? 
							   if($_SESSION['logic_erp']['user_level']!=2){$whereCon=" and USER_TAG_ID={$_SESSION['logic_erp']['user_id']}";}
							   $sql_team = "select id, team_leader_name from lib_marketing_team where project_type=2 and status_active =1 and is_deleted=0  and id in(select TEAM_ID from LIB_MKT_TEAM_MEMBER_INFO where 1=1 $whereCon )  order by team_leader_name";
							   $team_onchange_str = "load_drop_down( 'requires/sample_requisition_woven_textile_controller', this.value, 'load_drop_down_dealing_merchant', 'div_marchant'); load_drop_down( 'requires/sample_requisition_woven_textile_controller', this.value, 'load_drop_down_sample_marchant', 'div_sample_marchant'); ";
							   echo create_drop_down( "cbo_team_leader", 140, $sql_team,"id,team_leader_name", 1, "-Select Team-", $selected, $team_onchange_str ); 
						   ?>
                        </td>
                        
                       	
                    </tr>
                    <tr>
					    <td align="right" class="must_entry_caption">Dealing Merchant</td>
                       	<td id="div_marchant" >
                       		<? echo create_drop_down( "cbo_dealing_merchant",140, "select b.id,b.team_member_name from lib_marketing_team a,lib_mkt_team_member_info b where a.id=b.team_id and  a.PROJECT_TYPE=2 and b.status_active =1 and a.is_deleted=0 and a.status_active =1 and b.is_deleted=0 order by b.team_member_name","id,team_member_name", 1, "-- Select Merchant --", $selected, "" ); //a.lib_mkt_team_member_info_id=b.id and ?>
                       			
                       	</td>
                    	<td align="right">Delivery Date</td>
                        <td>
                        	<input type="text" style="width:130px" class="datepicker" placeholder="Select Date"  name="txt_delivery_date" id="txt_delivery_date"/>
                        </td>
                    	<td align="right">Ready To Approved</td>
						<td>
							<?
							echo create_drop_down( "cbo_ready_to_approved", 140, $yes_no,"", 1, "-- Select--", 2, "","","");
							?>
						</td>
						
                        <td align="right">Image</td>
                        <td >
                            <input type="button" class="image_uploader" style="width:140px" value="CLICK TO ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'hand_loom_requisition', 0 ,1)">
                        </td>
                        
                    </tr>
                    
                    <tr>
                    	<td align="right">Requisition Date</td>
                        <td>
                        	<input  type="text" style="width:130px" class="datepicker" placeholder="Select Date"  name="txt_requisition_date" id="txt_requisition_date"/>
                        </td>
                    	<td align="right">Remarks </td>
                        <td colspan="3"><input class="text_boxes" type="text" style="width:98%" placeholder="Write"  name="txt_remarks" id="txt_remarks"/></td>
						<td align="right"> ADD File</td>
                        <td >
                          <input type="button" class="image_uploader" style="width:140px" value=" ADD File" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'hand_loom_requisition', 2 ,1)">
                        </td>
                    </tr>
                    <tr>
                    	<td align="right">
							
						</td>
						<td colspan="2"><input type="button" class="image_uploader" style="width:140px" onclick="link_popup()" value="Link Details"></td>
                    	<td colspan="2">
	                    	<?
	                    		include("../../terms_condition/terms_condition.php");
	                    		terms_condition(632,'txt_system_id','../../','');
	                    	?>
                    	</td>
                    </tr>
                    
                    <tr>
                        <td align="center" colspan="8" valign="middle" style="max-height:380px; min-height:15px;" id="size_color_breakdown11">
                        <?=load_submit_buttons( $permission, "fnc_quotation_inquery", 0,1 ,"reset_fnc();",1); ?>                
                        </td>
                   </tr>
                </table>

				
					<legend>Details Info</legend>
					<table cellpadding="0" cellspacing="0" width="2020" class="rpt_table" border="1" rules="all" id="tbl_details">
						<thead>
							<th width="35">Check</th>
							
							<th width="100" class="must_entry_caption">Fabric Construction </th>
							<th width="100" class="must_entry_caption">Yarn Composition </th>
							<th width="100" class="must_entry_caption">Prodcut Type</th>
							<th width="100" class="must_entry_caption">Fab. Composition</th>
							<th width="100" class="must_entry_caption">Warp Yarn Type</th>
							<th width="100" class="must_entry_caption">Weft Yarn Type</th>
							<th width="100" class="must_entry_caption">Weave</th>
							<th width="100" class="must_entry_caption">Design</th>
							<th width="80" class="must_entry_caption">Finish Type</th>
							<th width="100" class="must_entry_caption">Fab. Color</th>
							<th width="80">Fabric Weight</th>
							<th width="90" >F.Weight Type</th>
							<th width="70" class="must_entry_caption">Finished Width</th>

							<th width="60" class="must_entry_caption">Cutable Width</th>
							<th width="60" class="must_entry_caption">Wash Type</th>
                            <th width="80">Requsition Qty</th>
                            <th width="80" class="must_entry_caption">UOM</th>
                            <th width="80">Buyer Tgt. Price</th>
							<th width="80">Amount</th>
							<th width="100" class="must_entry_caption">HL No/Labdip/Strike off </th>
							<th width="100">Remarks</th>
							<th width="100">&nbsp;</th>
						</thead>
						<tbody id="details_container">
							<tr id="tr_1" style="height:10px;" class="general">
								<td> 
									<input type="checkbox" id="checkBox_1"  name="checkBox_1"   />
								</td>
								
								<td >
									<input type="text" id="txtconstruction_1"  name="txtconstruction_1" class="text_boxes" style="width:90px" placeholder="Browse" ondblclick="openmypage_fabric_cons(1)" readonly value="" />
                					<input type="hidden" id="fabConstructionId_1"  name="fabConstructionId_1"  value="" />
                					<input type="hidden" id="fabConstruction_1"  name="fabConstruction_1"  value="" />
                					<input type="hidden" id="yarnCountDeterminationId_1"  name="yarnCountDeterminationId_1"  value="" />
								</td>
								<td> 
									<input type="text" id="yarnComposition_1"  name="yarnComposition_1" class="text_boxes" value=""  disabled/>
								</td>
								<td >
									<? 

										echo create_drop_down("cboProductType_1", 100, $color_type, "", 1, "Select", 0, "");
									?>
										
								</td>
								<td >
									<input type="text" id="txtcompone_1"  name="txtcompone_1"  class="text_boxes" style="width:90px" value="" readonly placeholder="Browse" onDblClick="openmypage_comp(1);" />
                            		<input type="hidden" id="cbocompone_1"  name="cbocompone_1" class="text_boxes" style="width:50px" value="" />
									<input type="hidden" id="cboyarntype_1"  name="cboyarntype_1" class="text_boxes" style="width:50px" value="" />
								</td>
								<td>
									<input type="text" id="txtWarpYarnType_1"  name="txtWarpYarnType_1"  class="text_boxes" style="width:90px" value="" readonly placeholder="Browse" onDblClick="openmypage_yarnType(1,1);" />
									<input type="hidden" id="hiddWarpYarnTypeId_1"  name="hiddWarpYarnTypeId_1" class="text_boxes" style="width:50px" value="" />
								 
								</td>
								<td>
									<input type="text" id="txtWeftYarnType_1"  name="txtWeftYarnType_1"  class="text_boxes" style="width:90px" value="" readonly placeholder="Browse" onDblClick="openmypage_yarnType(2,1);" />
									<input type="hidden" id="hiddWeftYarnTypeId_1"  name="hiddWeftYarnTypeId_1" class="text_boxes" style="width:50px" value="" />
								 
								</td>

								<td>
									<input style="width:90px;" type="text" class="text_boxes"  name="txtWeave_1" id="txtWeave_1" placeholder="Write" />
								</td>
								<td>
									<input style="width:90px;" type="text" class="text_boxes"  name="txtDesign_1" id="txtDesign_1" placeholder="Write" />
								</td>
								<td >
									<? 
										$finish_types = array(1=>"Regular",2=>"Peach",3=>"Brush");
										echo create_drop_down("cboFinishType_1", 70, $finish_types, "", 1, "Select", 0, "");
									?>
										
								</td>
								<td id="color_1">
									<input style="width:70px;" type="text" class="text_boxes"  name="txtColor_1" id="txtColor_1" onkeyup="show_color(1)" placeholder="Write" />
									<input  type="hidden"  name="cboColorId_1" id="cboColorId_1"  />
								</td>
								<td >
									<input style="width:70px;" type="text" class="text_boxes"  name="txtFabricWeight_1" id="txtFabricWeight_1" placeholder="Write" />
								</td>
								<td >
									<? echo create_drop_down( "cboweighttype_1", 80, $fabric_weight_type,"", 1, "-- Select --", '', "",$disabled,"" ); ?>
								</td>
								<td >
									<input style="width:70px;" type="text" class="text_boxes"  name="txtFinishedWidth_1" id="txtFinishedWidth_1" placeholder="Write"  />
								</td>
								<td >
									<input style="width:70px;" type="text" class="text_boxes" placeholder="Write"  name="txtCutableWidth_1" id="txtCutableWidth_1" />
								</td>
								<td >
									<? 
										$wash_types = array(1=>"Wash",2=>"Non-Wash",3=>"Garmnets Wash",4=>"Enzyme Wash");
										echo create_drop_down("cboWashType_1", 70, $wash_types, "", 1, "Select", 0, "");
									?>
								</td>
                                
								<td>
									<input type="text" class="text_boxes_numeric" name="txtOfferQty_1" id="txtOfferQty_1" placeholder="Write"  style="width:70px;" onkeyup="calculate_amount(1)">
								</td>
								<td >
									<?=create_drop_down("cboUom_1", 60, $unit_of_measurement, "", "", "", 2, "", "", "23,27");?>	
								</td>
                                
								
								
								<td>
									<input type="text" class="text_boxes_numeric" name="txtBuyerTgtPrice_1" id="txtBuyerTgtPrice_1" placeholder="Write"  style="width:70px;" onkeyup="calculate_amount(1)">
								</td>
								<td>
									<input type="text" class="text_boxes_numeric" name="txtAmount_1" id="txtAmount_1" placeholder="Write"  style="width:70px;" >
								</td>
								<td>
									<input type="text" class="text_boxes" name="txtHlNo_1" id="txtHlNo_1" readonly  style="width:70px;" >
									<input type="hidden" id="inquiryDtlsId_1"  name="inquiryDtlsId_1"  value="" />
								</td>
								<td>
									<input type="text" class="text_boxes" name="txtRemark_1" id="txtRemark_1" placeholder="Write"  style="width:90px;" >
								</td>
								<td>
									<input type="hidden" id="updateDtlsId_1" name="updateDtlsId_1" class="text_boxes" style="width:20px"/>
									<input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1);" />
									<input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
								</td>
							</tr>
						</tbody>

					</table>
					<table style="margin-top: 5px;">
						<tr>
							<td colspan="17"  valign="bottom" align="center" class="">
								<?=load_submit_buttons($permission, "fnc_details_info", 0, 0, "button_status()", 2);?>
								<input type="hidden" name="hidden_size_id" id="hidden_size_id" value="">
								<input type="hidden" name="hidden_bhqty" id="hidden_bhqty" value="">
								<input type="hidden" name="hidden_plnqnty" id="hidden_plnqnty" value="">
								<input type="hidden" name="hidden_dyqnty" id="hidden_dyqnty" value="">
								<input type="hidden" name="hidden_testqnty" id="hidden_testqnty" value="">
								<input type="hidden" name="hidden_selfqnty" id="hidden_selfqnty" value="">
								<input type="hidden" name="hidden_totalqnty" id="hidden_totalqnty" value="">
								<input type="hidden" name="hidden_tbl_size_id" id="hidden_tbl_size_id" value="">
							</td>
						</tr>
					</table>
            </form>
        </fieldset>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	var company_id=document.getElementById('cbo_company_name').value;
	if(company_id!=0){
		setTimeout(function(){
			load_drop_down( 'requires/sample_requisition_woven_textile_controller', company_id, 'load_drop_down_buyer', 'buyer_td');
			load_drop_down( 'requires/sample_requisition_woven_textile_controller', company_id, 'load_drop_down_season_com', 'season_td');
			fnc_variable_settings_check(company_id);
			
			
			var buyer_id=document.getElementById('cbo_buyer_name').value; 
			if(buyer_id!=0){
				setTimeout(function(){
					load_drop_down( 'requires/sample_requisition_woven_textile_controller',document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_season_buyer', 'season_td'); 
					load_drop_down( 'requires/sample_requisition_woven_textile_controller', document.getElementById('cbo_buyer_name').value, 'load_drop_down_brand', 'brand_td');
					
				}, 500);
			}
			
			
		}, 1000);
	}
		
</script>



</html>