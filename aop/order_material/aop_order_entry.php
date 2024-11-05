<?
/*--- ----------------------------------------- Comments
Purpose			: 	AOP Order Info					
Functionality	:	
JS Functions	:
Created by		:	K.M Nazim Uddin
Creation date 	: 	02-03-2019
Updated by 		: 		
Update date		:
Oracle Convert 	:		
Convert date	: 	   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start(); 
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("AOP Order Info", "../../", 1,1, $unicode,1,'');
$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');

function arrayExclude($array,Array $excludeKeys){
    foreach($array as $key => $value){
        if(!in_array($key, $excludeKeys)){
            $return[$key] = $value;
        }
    }
    return $return;
}
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	/*var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name ", "color_name" ), 0, -1); ?> ];
	function set_auto_complete(type)
	{
		if(type=='color_return')
		{
			$(".txt_color").autocomplete({
				source: str_color
			});
		}
	}*/

	function openmypage_job()
	{
		if ( form_validation('cbo_company_name','Company')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
		page_link='requires/aop_order_entry_controller.php?action=job_popup&data='+data;
		title='Job No Pop-up Info';
		

		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=1090px, height=420px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemaildata=this.contentDoc.getElementById("selected_job").value;
			var ex_data=theemaildata.split('_');
			if (ex_data[1]!="")
			{//alert(theemail.value);

				freeze_window(5);
				get_php_form_data( ex_data[1], "load_php_data_to_form", "requires/aop_order_entry_controller" );
				var within_group = $('#cbo_within_group').val();
				//show_list_view(theemail.value,'subcontract_dtls_list_view','order_list_view','requires/aop_order_entry_controller','setFilterGrid("list_view",-1)');
				show_list_view(2+'_'+ex_data[1]+'_'+within_group+'_'+$("#update_id").val()+'_'+$("#cbo_company_name").val()+'_'+ex_data[2],'order_dtls_list_view','aop_details_container','requires/aop_order_entry_controller','setFilterGrid(\'list_view\',-1)');
					/*var row_num = $('#tbl_dtls_aop tbody tr').length;
					for(i=1;i<=row_num;i++)
					{
 						load_machine(i);
 					}*/
				
				//$('#txt_aop_ref').attr('disabled',true);				
				set_button_status(1, permission, 'fnc_job_order_entry',1);
				release_freezing();
			}
		}
	}

	function fnc_job_order_entry( operation )
	{
		if(operation==2)
		{
			alert("Delete Restricted.");
			release_freezing();	
			return;
		}
		var delete_master_info=0;
		//var process = $("#cbo_process_name").val();
		var cbo_within_group = $("#cbo_within_group").val();
		if(cbo_within_group==1)
		{	
			if ( form_validation('cbo_company_name*cbo_within_group*cbo_party_name*cbo_currency*txt_order_receive_date*txt_delivery_date*txt_order_no*cbo_party_location*cbo_team_leader*cbo_team_member','Company*Within Group*Party*Currency*Order No*Order Receive Date*Order Delivery Date*Order No.*Party Location*Team Leader*Team Member')==false )
			{
				return;
			}
		}
		else
		{
			if ( form_validation('cbo_company_name*cbo_within_group*cbo_party_name*cbo_currency*txt_order_receive_date*txt_delivery_date*txt_order_no*cbo_team_leader*cbo_team_member','Company*Within Group*Party*Currency*Order Receive Date*Order Delivery Date*Order No.*Team Leader*Team Member')==false )
			{
				return;
			}
		}
		var isFileMandatory = "";
		if(operation==0)
		{
			var r=confirm("Are you sure?");	
			if(r==true)
			{
			}
			else
			{
				release_freezing();	
				return;
			}
			<?php 
				
			if(!empty($_SESSION['logic_erp']['mandatory_field'][278][1]))
			{
				echo " isFileMandatory = ". $_SESSION['logic_erp']['mandatory_field'][278][1] . ";\n";
			} 
			?>
			if($("#multiple_file_field")[0].files.length==0 && isFileMandatory!="" && $('#update_id').val()=='')
			{

				document.getElementById("multiple_file_field").focus();
				var bgcolor='-moz-linear-gradient(bottom, rgb(254,151,174) 0%, rgb(255,255,255) 10%, rgb(254,151,174) 96%)';
				document.getElementById("multiple_file_field").style.backgroundImage=bgcolor;
				alert("Please Add File in Master Part");
				return;	
			}	
		}

		if ('<?php echo implode('*', arrayExclude($_SESSION['logic_erp']['mandatory_field'][278],array(1))); ?>') 
		{
			if (form_validation('<?php echo implode('*',  arrayExclude($_SESSION['logic_erp']['mandatory_field'][278],array(1))); ?>', '<?php echo implode('*',  arrayExclude($_SESSION['logic_erp']['field_message'][278],array(1))); ?>') == false) {

				return;
			}
		}

		
		var row_num=$('#tbl_dtls_aop tbody tr').length;
		var data_all=""; var i=0; var selected_row=0;
		var data_delete="";  var a=0;

		for (var j=1; j<=row_num; j++)
		{
			if(cbo_within_group==1)
			{
				if (form_validation('txtbuyerPo_'+j+'*txtConstruction_'+j+'*txtComposition_'+j+'*txtGsm_'+j+'*txtDia_'+j+'*txtGmtsColor_'+j+'*txtItemColor_'+j+'*txtFinDia_'+j+'*txtAopColor_'+j+'*txtQty_'+j+'*cboUom_'+j+'*txtRateUnit_'+j+'*txtProcessLoss_'+j+'*txtOrderDeliveryDate_'+j,'Buyer PO*Construction*Composition*GSM*Dia*Gmts Color*Item Color*Fin. Dia*AOP Color*Order Qty*UOM*Rate/Unit*P.Loss*Delivery Date')==false)
				{
					return;
				}
			}
			else
			{
				if (form_validation('txtConstruction_'+j+'*txtComposition_'+j+'*txtGsm_'+j+'*txtDia_'+j+'*txtGmtsColor_'+j+'*txtItemColor_'+j+'*txtFinDia_'+j+'*txtAopColor_'+j+'*txtQty_'+j+'*cboUom_'+j+'*txtRateUnit_'+j+'*txtProcessLoss_'+j+'*txtOrderDeliveryDate_'+j,'Construction*Composition*GSM*Dia*Gmts Color*Item Color*Fin. Dia*AOP Color*Order Qty*UOM*Rate/Unit*P.Loss*Delivery Date')==false)
				{
					return;
				}
			}
			i++;
			
			if (($('#txtConstruction_'+j).val().indexOf(',') > -1) || ($('#txtComposition_'+j).val().indexOf(',') > -1))
			{
			  	alert("Please avoid using comma(,) in Construction and Composition field");
			  	return;
			}
			else
			{
				orderDeliveryDate = get_submitted_data_string('txtOrderDeliveryDate_'+j);
				//+ "'&txtbuyerPo_" + j + "='" + txtbuyerPo + "'&txtstyleRef_" + j + "='" + txtstyleRef + "'&txtbuyer_" + j + "='" + txtbuyer 
				data_all+="&txtbuyerPoId_" + i + "='" + $('#txtbuyerPoId_'+j).val()+"'"+"&txtbuyerPo_" + i + "='" + $('#txtbuyerPo_'+j).val()+"'"+"&txtstyleRef_" + i + "='" + $('#txtstyleRef_'+j).val()+"'"+"&txtbuyer_" + i + "='" + $('#txtbuyer_'+j).val()+"'"+"&cboBodyPart_" + i + "='" + $('#cboBodyPart_'+j).val()+"'"+"&txtConstruction_" + i + "='" + $('#txtConstruction_'+j).val()+"'"+"&txtComposition_" + i + "='" + $('#txtComposition_'+j).val()+"'"+"&txtGsm_" + i + "='" + $('#txtGsm_'+j).val()+"'"+"&txtDia_" + i + "='" + $('#txtDia_'+j).val()+"'"+"&txtGmtsColor_" + i + "='" + $('#txtGmtsColor_'+j).val()+"'"+"&txtItemColor_" + i + "='" + $('#txtItemColor_'+j).val()+"'"+"&txtFinDia_" + i + "='" + $('#txtFinDia_'+j).val()+"'"+"&txtAopColor_" + i + "='" + $('#txtAopColor_'+j).val()+"'"+"&txtQty_" + i + "='" + $('#txtQty_'+j).val()+"'"+"&cboUom_" + i + "='" + $('#cboUom_'+j).val()+"'"+"&txtRateUnit_" + i + "='" + $('#txtRateUnit_'+j).val()+"'"+"&txtAmount_" + i + "='" + $('#txtAmount_'+j).val()+"'"+"&txtProcessLoss_" + i + "='" + $('#txtProcessLoss_'+j).val()+"'"+"&hdnDtlsUpdateId_" + i + "='" + $('#hdnDtlsUpdateId_'+j).val()+"'"+"&hdnlibyarndetar_" + i + "='" + $('#hdnlibyarndetar_'+j).val()+"'"+"&hdnbookingDtlsId_" + i + "='" + $('#hdnbookingDtlsId_'+j).val()+"'"+"&txtArtwork_" + i + "='" + $('#txtArtwork_'+j).val()+"'"+"&cboPrintType_" + i + "='" + $('#cboPrintType_'+j).val()+"'"+"&cboMachineName_" + i + "='" + $('#cboMachineName_'+j).val()+"'"+"&cboBillingOn_" + i + "='" + $('#cboBillingOn_'+j).val()+"'"+"&txtDesignNo_" + i + "='" + $('#txtDesignNo_'+j).val()+"'"+"&txtOrderDeliveryDate_" + i + "="+orderDeliveryDate+"";
 			}
		}
		
		if(data_all!='')
		{
			var data="action=save_update_delete&operation="+operation+data_all+'&total_row='+i+get_submitted_data_string('txt_job_no*cbo_company_name*cbo_location_name*cbo_within_group*cbo_party_name*cbo_party_location*cbo_currency*txt_order_receive_date*txt_delivery_date*txt_rec_start_date*txt_rec_end_date*txt_order_no*hid_order_id*update_id*txt_aop_ref*cbo_order_type*cbo_work_order_type*txt_remarks*cbo_team_leader*cbo_team_member*txt_exchange_rate',"../../");
			//alert (data); return;
			freeze_window(operation);
			http.open("POST","requires/aop_order_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_job_order_entry_response;
		}
	}

	function fnc_job_order_entry_response()
	{
		if(http.readyState == 4) 
		{
			release_freezing();
			//alert (http.responseText);return;
			var response=trim(http.responseText).split('**');
			if(trim(response[0])=='aopRec'){
				alert("Receive Found :"+trim(response[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			 }
			 
			 if(trim(response[0])=='aopRecipe'){
				alert("Recipe Found :"+trim(response[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			 }
			if(response[0]==0 || response[0]==1)
			{
				document.getElementById('txt_job_no').value = response[1];
				document.getElementById('update_id').value = response[2];
				if(response[0]==0)
				{
					uploadFile( $("#update_id").val());
				}
				var within_group = $('#cbo_within_group').val();
				$('#txt_order_no').attr('disabled',true);
				$('#cbo_within_group').attr('disabled',true);
				
				var txt_order_no = $('#txt_order_no').val();
				if(txt_order_no=="")
				{
					document.getElementById('txt_order_no').value = response[1];
				}
				
				//$('#txt_aop_ref').attr('disabled',true);
				
				show_list_view(2+'_'+response[1]+'_'+within_group+'_'+response[2]+'_'+$("#cbo_company_name").val(),'order_dtls_list_view','aop_details_container','requires/aop_order_entry_controller','setFilterGrid(\'list_view\',-1)');
				set_button_status(1, permission, 'fnc_job_order_entry',1);

			}
			else if(response[0]==2)
			{
				location.reload();
			}
			show_msg(response[0]);
			
		}
	}

	function fnc_load_party(type,within_group)
	{
		if ( form_validation('cbo_company_name','Company')==false )
		{
			$('#cbo_within_group').val(1);
			return;
		}
		var company = $('#cbo_company_name').val();
		var party_name = $('#cbo_party_name').val();
		var location_name = $('#cbo_location_name').val();
		
		if(within_group==1 && type==1)
		{
			load_drop_down( 'requires/aop_order_entry_controller', company+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
			
			$('#txt_order_no').removeAttr("onDblClick").attr("onDblClick","openmypage_order();");
			$('#txtConstruction_1').removeAttr('onDblClick','onDblClick');
			$('#txtGmtsColor_1').removeAttr("onDblClick","onDblClick')");
			$('#txtItemColor_1').removeAttr("onDblClick","onDblClick')");
			$('#txtAopColor_1').removeAttr("onDblClick","onDblClick'");
			
			$('#txt_order_no').attr('readonly',true);
			$('#txt_order_no').attr('placeholder','Browse');
			$('#txtbuyerPo_1').attr('placeholder','Display');
			$('#txtstyleRef_1').attr('placeholder','Display');
			$('#txtGmtsColor_1').attr('placeholder','Display');
			$('#txtItemColor_1').attr('placeholder','Display');
			$('#txtAopColor_1').attr('placeholder','Display');
			$('#txtbuyer_1').attr('placeholder','Display');
			$('#buyerbuyer_td').html('Cust. Buyer');
			$('#txtConstruction_1').attr('placeholder','');
			$("#cbo_party_location").val(0);
			$('#cbo_party_location').attr('disabled',false);
			$('#cbo_currency').attr('disabled',true);
			
			$('#td_party_location').css('color','blue');
			$('#buyerpo_td').css('color','blue');
			$('#buyerstyle_td').css('color','blue');
		}
		else if(within_group==2 && type==1)
		{
			load_drop_down( 'requires/aop_order_entry_controller', company+'_'+2, 'load_drop_down_buyer', 'buyer_td' );
			$('#txt_order_no').removeAttr('onDblClick','onDblClick');
			
			$('#txtConstruction_1').attr("onDblClick","open_fabric_decription_popup(1);");
			$('#txtGmtsColor_1').attr("onDblClick","color_select_popup('txtGmtsColor_',1);");
			$('#txtItemColor_1').attr("onDblClick","color_select_popup('txtItemColor_',1);");
			$('#txtAopColor_1').attr("onDblClick","color_select_popup('txtAopColor_',1);");
			$('#txtConstruction_1').attr('placeholder','Browse');
			$('#txt_order_no').attr('readonly',false);
			$('#txt_order_no').attr('placeholder','Write');
			$('#txtbuyerPo_1').attr('placeholder','Write');
			$('#txtstyleRef_1').attr('placeholder','Write');
			$('#txtGmtsColor_1').attr('placeholder','Write/Browse');
			$('#txtItemColor_1').attr('placeholder','Write/Browse');
			$('#txtAopColor_1').attr('placeholder','Write/Browse');
			$('#txtbuyer_1').attr('placeholder','Write');
			$('#buyerbuyer_td').html("Buyer's Buyer");
			
			$("#cbo_party_location").val(0); 
			$('#cbo_party_location').attr('disabled',true);
			$('#cbo_currency').attr('disabled',false);
			
			$('#td_party_location').css('color','black');
			$('#buyerpo_td').css('color','black');
			$('#buyerstyle_td').css('color','black');
		}
		else if(within_group==1 && type==2)
		{
			load_drop_down( 'requires/aop_order_entry_controller', party_name+'_'+2, 'load_drop_down_location', 'party_location_td' ); 
			$('#td_party_location').css('color','blue');
			$('#cbo_currency').attr('disabled',true);
		} 
	}

	function openmypage_order()
	{
		var company = $('#cbo_company_name').val();
		var party_name = $('#cbo_party_name').val();
		if ( form_validation('cbo_company_name*cbo_within_group*cbo_party_name','Company*Within Group*Party')==false )
		{
			return;
		}
		else
		{
			var title = 'Work Order No Pop-up Info';
			var page_link = 'requires/aop_order_entry_controller.php?company='+company+'&party_name='+party_name+'&action=order_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=780px,height=420px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var theemaildata=this.contentDoc.getElementById("hidd_booking_data").value;
				
				var ex_data=theemaildata.split('_');
				if (theemaildata!="")
				{
					freeze_window(5);
					$('#txt_order_no').val(ex_data[1]);
					$('#hid_order_id').val(ex_data[0]);
					$('#cbo_currency').val(ex_data[2]);
					$('#cbo_company_name').attr('disabled',true);
					$('#cbo_within_group').attr('disabled',true);
					$('#cbo_party_name').attr('disabled',true);
					$('#cbo_currency').attr('disabled',true);
					exchange_rate(ex_data[2]);
					show_list_view(1+'_'+ex_data[1]+'_'+1,'order_dtls_list_view','aop_details_container','requires/aop_order_entry_controller','setFilterGrid(\'list_view\',-1)');
					//load_drop_down( 'requires/aop_order_entry_controller',company, 'load_drop_machine', 'machine_td' );
					//load_machine(company)
					
					
					var row_num = $('#tbl_dtls_aop tbody tr').length;
					for(i=1;i<=row_num;i++)
					{
 						load_machine(i);
 					}
					load_delivery_date();
					release_freezing();
				}
			}
		}
	}
	
	function fnResetForm() 
	{
        set_button_status(0, permission, 'fnc_job_order_entry', 1);
		reset_form('aoporderentry_1','','','cbo_within_group,1*cbo_currency,1','','');
		$('#tbl_dtls_aop tbody tr:not(:first)').remove();
		$('#cbo_company_name').attr('disabled',false);
		$('#cbo_within_group').attr('disabled',false);
		$('#cbo_party_name').attr('disabled',false);
		$('#txt_order_no').attr('disabled',false);
    }
	
	function location_select()
	{
		if($('#cbo_location_name option').length==2)
		{
			if($('#cbo_location_name option:first').val()==0)
			{
				$('#cbo_location_name').val($('#cbo_location_name option:last').val());
				//eval($('#cbo_location_name').attr('onchange')); 
			}
		}
		else if($('#cbo_location_name option').length==1)
		{
			$('#cbo_location_name').val($('#cbo_location_name option:last').val());
			//eval($('#cbo_location_name').attr('onchange'));
		}	
	}

	function cal_amount(rowNum)
	{
		$("#txtAmount_"+rowNum).val(($("#txtQty_"+rowNum).val()*1)*($("#txtRateUnit_"+rowNum).val()*1));
		//math_operation( "txt_total_order_qnty", "txtorderquantity_", "+", tot_row,ddd );
	}

	function add_dtls_tr(i) 
	{
		var within_group = $('#cbo_within_group').val();
		if(within_group==1)
		{
			alert('This feature is use for Within Group "No" only '); return;
		}
		else
		{
			var row_num=$('#tbl_dtls_aop tbody tr').length;
			if (row_num!=i)
			{
				return false;
			}
			else
			{
				i++;
				$("#tbl_dtls_aop tbody tr:last").clone().find("input,select").each(function() 
				{
					$(this).attr(
					{
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						'name': function(_, name) { return name },
						'value': function(_, value) { return value } 
					}); 
				}).end().appendTo("#tbl_dtls_aop tbody");
				
				$("#tbl_dtls_aop tbody tr:last").removeAttr('id').attr('id','row_'+i);
				$('#increase_'+i).removeAttr("onClick").attr("onClick","add_dtls_tr("+i+");");
				$('#decrease_'+i).removeAttr("onClick").attr("onClick","fnc_delet_dtls_tr("+i+");");
				$('#txtConstruction_'+i).attr("onDblClick","open_fabric_decription_popup("+i+");");
				$('#txtQty_'+i).removeAttr("onKeyUp").attr("onKeyUp","cal_amount("+i+");");
				$('#txtRateUnit_'+i).removeAttr("onKeyUp").attr("onKeyUp","cal_amount("+i+");");
				$('#hdnDtlsUpdateId_' + i).val( '' );
				$('#hdnlibyarndetar_' + i).val( '' );
				$('#hdnbookingDtlsId_' + i).val( '' );
				$('#txtSerial_' + i).val(i); 
				
 				$('#txtConstruction_'+ i).attr('placeholder','Browse');
				$('#txtGmtsColor_'+ i).attr('placeholder','Write/Browse');
				$('#txtAopColor_'+ i).attr('placeholder','Write/Browse');
				$('#txtItemColor_'+ i).attr('placeholder','Write/Browse');
				$('#txtGmtsColor_'+ i).removeAttr("onDblClick").attr("onDblClick","color_select_popup('txtGmtsColor_',"+i+")");
				$('#txtItemColor_'+ i).removeAttr("onDblClick").attr("onDblClick","color_select_popup('txtItemColor_',"+i+")");
				$('#txtAopColor_'+ i).removeAttr("onDblClick").attr("onDblClick","color_select_popup('txtAopColor_',"+i+")");
				$('#txtOrderDeliveryDate_'+i).removeAttr("onChange").attr("onChange","chk_min_del_date("+i+")");
				$('#txtProcessLoss_' + i).removeAttr("onKeyUp").attr("onKeyUp", "copy_process_loss(" + i + ");");
				$('#cboPrintType_' + i).removeAttr("onchange").attr("onchange", "copy_process_loss(" + i + ");");
				$('#cboMachineName_'+i).removeAttr("onChange").attr("onChange","load_machine("+i+")"); 
				$('#cboMachineName_' + i).removeAttr("onchange").attr("onchange", "copy_machine_name(" + i + ");");
				
				$('#txtOrderDeliveryDate_'+i).removeAttr("class").attr("class","datepicker");
				
				
				//$('table #row_'+i+' #machinetd_'+i).removeAttr("id").attr('id','machinetd_'+i);
				$('#row_' + i).find("td:eq(14)").removeAttr('id').attr('id', 'machinetd_' + i);
				set_all_onclick();
				//add_auto_complete(i);
			}
			
		}//cboitemgroup_'+i+'*cbocountry_'+i+'*txtGrade_'+i+'*txtStapleLength_'+i+'*txtMic_'+i+'*txtStrength_'+i+'*txtCropyear_'+i+'*txtColoGrade_'+i+'*txtTrash_'+i+'*txtMoisture_'+i+'*cboCotTypr_'+i+'*cboUom_'+i+'*txtPrm_'+i+'*txtQuantity_'+i+'*txtRate_'+i+'*txtTopup_'+i+'*txtAmount_'+i+'*cboMonth_'+i+'*txtYear_'+i+'*hdnDtlsUpdateId_
	}

	function fnc_delet_dtls_tr(i)
	{ 
		var within_group = $('#cbo_within_group').val();
		if(within_group==1)
		{
			alert('This feature is use for Within Group "No" only '); return;
		}
		else
		{
			var numRow = $('#tbl_dtls_aop tbody tr').length;
			if(numRow==i && i!=1)
			{
				$('#tbl_dtls_aop tbody tr:last').remove();
				//cal_ammount(i-1,1);
			}
		}
	}

	/*function open_fabric_decription_popup(i)
	{
		var cbofabricnature=document.getElementById('cbofabricnature_'+i).value;
		var libyarncountdeterminationid =document.getElementById('libyarncountdeterminationid_'+i).value
		
		var page_link='requires/aop_order_entry_controller.php?action=fabric_description_popup&fabric_nature='+cbofabricnature+'&libyarncountdeterminationid='+libyarncountdeterminationid;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Fabric Description', 'width=960px,height=450px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var fab_des_id=this.contentDoc.getElementById("fab_des_id");
			var fab_nature_id=this.contentDoc.getElementById("fab_nature_id");
			var fab_desctiption=this.contentDoc.getElementById("fab_desctiption");
			var fab_gsm=this.contentDoc.getElementById("fab_gsm");
			var yarn_desctiption=this.contentDoc.getElementById("yarn_desctiption");
			var construction=this.contentDoc.getElementById("construction");
			var composition=this.contentDoc.getElementById("composition");
			document.getElementById('libyarncountdeterminationid_'+i).value=fab_des_id.value;
			document.getElementById('fabricdescription_'+i).value=fab_desctiption.value;
			document.getElementById('fabricdescription_'+i).title=fab_desctiption.value;
			document.getElementById('cbofabricnature_'+i).value=fab_nature_id.value;
			document.getElementById('txtgsmweight_'+i).value=fab_gsm.value;
			document.getElementById('yarnbreackdown_'+i).value=yarn_desctiption.value;
			document.getElementById('construction_'+i).value=construction.value;
			document.getElementById('composition_'+i).value=composition.value;
			//sum_yarn_required()
		}
	}
*/
	function open_fabric_decription_popup(i)
	{
		//alert(i);

		var construction = $('#construction_'+i).val();
		var composition = $('#composition_'+i).val();
		
		var title = 'Fabric Description Pop-up';
		var page_link = 'requires/aop_order_entry_controller.php?construction='+construction+'&composition='+composition+'&action=fabric_description_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=780px,height=420px,center=1,resize=1,scrolling=0','../')

		emailwindow.onclose=function()
		{
			var fab_gsm=this.contentDoc.getElementById("fab_gsm").value;
			var construction=this.contentDoc.getElementById("construction").value;
			var composition=this.contentDoc.getElementById("composition").value;

			$('#txtConstruction_' + i).val( construction );
			$('#txtComposition_' + i).val( composition );
			$('#txtGsm_' + i).val( fab_gsm );

			$('#txtConstruction_' + i).attr('readonly',true);
			$('#txtComposition_' + i).attr('readonly',true);
		}
	}
	//$('#txtFile_'+i).removeAttr("onClick").attr("onClick","file_uploader( '../../',document.getElementById('hdnDtlsUpdateId_"+i+"').value,'', 'aoporderentry_1', 0 ,1);");

	function color_select_popup(id,rowNum)
	{
		var company_name=$('#cbo_company_name').val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/aop_order_entry_controller.php?action=color_popup&company_name='+company_name, 'Color Select Pop Up', 'width=450px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var color_name=this.contentDoc.getElementById("color_name");
			if (color_name.value!="")
			{
				$("#"+id+rowNum).val(color_name.value);
			}
		}
	}

	function load_delivery_date()
	{
		var within_group = $('#cbo_within_group').val();
		if(within_group==2)
		{
			var delivery_date=$('#txt_delivery_date').val();
			var row_num = $('#tbl_dtls_aop tbody tr').length;
			var i=''; 
			for(i=1;i<=row_num;i++)
			{
				$('#txtOrderDeliveryDate_'+i).val(trim(delivery_date));
			} 
		}
		else
		{
			var delivery_date=$('#txt_delivery_date').val();
			var row_num = $('#tbl_dtls_aop tbody tr').length;
			var i=''; 
			for(i=1;i<=row_num;i++)
			{
				$('#txtOrderDeliveryDate_'+i).val(trim(delivery_date));
			} 
		}
	}

	function chk_min_del_date(rowNo)
	{
		var mstDelDate=$('#txt_delivery_date').val();
		var dtlsDelDate=$('#txtOrderDeliveryDate_'+rowNo).val();
		if(mstDelDate=='')
		{
			$('#txt_delivery_date').val(dtlsDelDate);
		}
		else
		{
			var i=''; var otherDtlsDelDate=''; 
			$('#txt_delivery_date').val($('#txtOrderDeliveryDate_1').val());
			for(i=1;i<=$('#tbl_dtls_aop tbody tr').length;i++)
			{
				otherDtlsDelDate=$('#txtOrderDeliveryDate_'+i).val();
				if(otherDtlsDelDate!='')
				{
					if(date_compare( $('#txt_delivery_date').val() , otherDtlsDelDate )==false)
					{
						$('#txt_delivery_date').val(otherDtlsDelDate);
					}
				}
			} 
		}
	}
	
	function exchange_rate(val)
	{
		if(val==0)
		{
			$('#txt_order_receive_date').removeAttr('disabled','disabled');
			$('#cbo_company_name').removeAttr('disabled','disabled');
			$("#txt_exchange_rate").val("");
		}
		else if(val==1)
		{
			$("#txt_exchange_rate").val(1);
			//$('#txt_order_receive_date').attr('disabled','disabled');
			//$('#cbo_company_name').attr('disabled','disabled');
			$('#txt_exchange_rate').attr('disabled','disabled');
		}
		else
		{
			var bill_date = $('#txt_order_receive_date').val();
			var company_name = $('#cbo_company_name').val();
			var response=return_global_ajax_value( val+"**"+bill_date+"**"+company_name, 'check_conversion_rate', '', 'requires/aop_order_entry_controller');
			$('#txt_exchange_rate').val(response);
			$('#txt_order_receive_date').attr('disabled','disabled');
			//$('#cbo_company_name').attr('disabled','disabled');
			$('#txt_exchange_rate').attr('disabled','disabled');
		}
	}
	
function fnc_copyColorRangeProcess(id) 
{
     if(id=="copy_to_all")
    {
        var copy_to_all=$("#copy_to_all").is(":checked");
     }
  }
	function copy_machine_name(i) 
	{
    	var row_num = $('#tbl_dtls_aop tbody tr').length;
		var MachineName = document.getElementById('cboMachineName_' + i).value;
        var copy_to_all=$("#copy_to_all").is(":checked");
		
		
		
		 $("#tbl_dtls_aop").find('tbody tr').each(function () 
		 {
                var x = $(this).find('input[name="txtSerial[]"]').val();
                 if(x >= i)
                {
                    if(copy_to_all)
                    {
						$('#cboMachineName_' + x).val(MachineName);
                    }
                }
            });
        
       /* if(copy_to_all)
        {
			var delivery_date=$('#txt_delivery_date').val();
			var row_num = $('#tbl_dtls_aop tbody tr').length;
 			var x=''; 
			for(x=1;x<=row_num;x++)
			{
				if(copy_to_all)
				{
					$('#cboPrintType_' + x).val(PrintType);
					$('#txtProcessLoss_' + x).val(process_loss);
				 }
			} 
         }*/
        
    }

 	function copy_process_loss(i) 
	{
    	var row_num = $('#tbl_dtls_aop tbody tr').length;
    	var process_loss = document.getElementById('txtProcessLoss_' + i).value;
        var PrintType = document.getElementById('cboPrintType_' + i).value;
		//var MachineName = document.getElementById('cboMachineName_' + i).value;
        var copy_to_all=$("#copy_to_all").is(":checked");
		
		
		
		 $("#tbl_dtls_aop").find('tbody tr').each(function () 
		 {
                var x = $(this).find('input[name="txtSerial[]"]').val();
                 if(x >= i)
                {
                    if(copy_to_all)
                    {
						//alert(MachineName);
                        $('#cboPrintType_' + x).val(PrintType);
						$('#txtProcessLoss_' + x).val(process_loss);
						//$('#cboMachineName_' + x).val(MachineName);
                    }
                }
            });
        
       /* if(copy_to_all)
        {
			var delivery_date=$('#txt_delivery_date').val();
			var row_num = $('#tbl_dtls_aop tbody tr').length;
 			var x=''; 
			for(x=1;x<=row_num;x++)
			{
				if(copy_to_all)
				{
					$('#cboPrintType_' + x).val(PrintType);
					$('#txtProcessLoss_' + x).val(process_loss);
				 }
			} 
         }*/
        
    }
    function uploadFile(mst_id)
	{
		$(document).ready(function() { 
			 
			var suc=0;
			var fail=0;
			for( var i = 0 ; i < $("#multiple_file_field")[0].files.length ; i++)
			{
				var fd = new FormData();
				console.log($("#multiple_file_field")[0].files[i]);
				var files = $("#multiple_file_field")[0].files[i]; 
				fd.append('file', files); 
				$.ajax({ 
					url: 'requires/aop_order_entry_controller.php?action=file_upload&mst_id='+ mst_id, 
					type: 'post', 
					data:fd, 
					contentType: false, 
					processData: false, 
					success: function(response){ 
						var res=response.split('**');
						if(res[0] == 0){ 
							
							suc++;
						}
						else if(fail==0)
						{
							alert('file not uploaded');
							fail++;
						}  
					}, 
				}); 
			}

			if(suc > 0 )
			{
				 document.getElementById('multiple_file_field').value='';
			}
			
			
		}); 
	}
  
  
    function load_machine(rowNo)
	{
		  
		 	var company = $('#cbo_company_name').val();
 			var row_num = $('#tbl_dtls_aop tbody tr').length;
			for(i=rowNo;i<=row_num;i++)
			{
				//$('#cboSection_'+i).val(section);
				load_drop_down( 'requires/aop_order_entry_controller',company+'_'+i , 'load_drop_machine', 'machinetd_'+i );
					//load_drop_down( 'requires/aop_order_entry_controller',company+'_'+rowNo, 'load_drop_machine', 'machine_td' );
			}
		 
	}
</script>
</head>
<body onLoad="set_hotkey();set_auto_complete('color_return');">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission);
		 ?>
        <form name="aoporderentry_1" id="aoporderentry_1" autocomplete="off"> 
			<fieldset style="width:850px;">
			<legend>AOP Order Entry</legend>
                <table width="830" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                    <tr>
                        <td colspan="3" align="right"><strong>Job No</strong></td>
                        <td colspan="3">
                            <input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes_numeric" style="width:90px" readonly />
                            <input class="text_boxes"  type="text" name="txt_job_no" id="txt_job_no" onDblClick="openmypage_job();" placeholder="Double Click" style="width:140px;" readonly /></td>
                    </tr>
                    <tr>
                        <td width="110" class="must_entry_caption">Company Name </td>
                        <td width="160"><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_machine(1);load_drop_down( 'requires/aop_order_entry_controller', this.value+'_'+1, 'load_drop_down_location', 'location_td'); location_select(); fnc_load_party(1,document.getElementById('cbo_within_group').value);exchange_rate(document.getElementById('cbo_currency').value);"); ?>
                        </td>
                        <td width="110">Location Name</td>
                        <td width="160" id="location_td"><? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", $selected, "" ); ?></td>
                        <td width="110" class="must_entry_caption">Within Group</td>
                        <td><?php echo create_drop_down( "cbo_within_group", 150, $yes_no,"", 0, "--  --", 0, "fnc_load_party(1,this.value);" ); ?></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Party</td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_load_party(2,document.getElementById('cbo_within_group').value);"); ?></td>
                        <td id="td_party_location">Party Location</td>
                        <td id="party_location_td"><? echo create_drop_down( "cbo_party_location", 150, $blank_array,"", 1, "-- Select Location --", $selected, "",1 ); ?></td>
                        <td class="must_entry_caption">Ord. Receive Date</td>
                        <td><input type="text" name="txt_order_receive_date"  style="width:140px"  id="txt_order_receive_date" class="datepicker" value="<? echo date("d-m-Y")?>" onChange="exchange_rate(document.getElementById('cbo_currency').value)" readonly /></td>
                    </tr> 
                    <tr>
                    	<td class="must_entry_caption">Delivery Date</td>
                        <td><input type="text" name="txt_delivery_date" id="txt_delivery_date"  style="width:140px"  class="datepicker" value="" onChange="load_delivery_date()" readonly  /></td>
                        <td>Rcv. Start Date</td>
                        <td><input type="text" name="txt_rec_start_date" id="txt_rec_start_date" style="width:140px" class="datepicker" value="" placeholder="Material Rcv. Start Date" readonly /></td>
                    	<td>Rcv. End Date</td>
                        <td><input type="text" name="txt_rec_end_date" id="txt_rec_end_date" style="width:140px" class="datepicker" value="" placeholder="Material Rcv. End Date"  readonly/></td>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">Currency</td>
                        <td><? echo create_drop_down( "cbo_currency", 150, $currency,"", 1, "-- Select Currency --",1,"exchange_rate(this.value)", 1,"" ); 	?></td>
                    	<td class="must_entry_caption"  ><strong>Work Order</strong></td>
                        <td ><input name="txt_order_no" id="txt_order_no" type="text"  class="text_boxes" style="width:140px" placeholder="Browse" onDblClick="openmypage_order();" readonly />
                            <input type="hidden" name="hid_order_id" id="hid_order_id">
                            <input type="hidden" name="update_id" id="update_id">
                        </td>
                        <td>AOP Ref.</td>
                        <td ><input name="txt_aop_ref" id="txt_aop_ref" type="text" class="text_boxes" style="width:140px"/>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">Team Leader</td>
                        <td width="160">
                        	<?php echo create_drop_down( "cbo_team_leader", 150, "select id,team_leader_name from lib_marketing_team where  status_active =1 and is_deleted=0 and project_type=5","id,team_leader_name", 1, "-- Select Leader --", $selected, "load_drop_down( 'requires/aop_order_entry_controller', this.value+'_'+1, 'load_drop_down_member', 'member_td');"); ?>
                        </td>
                        <td class="must_entry_caption">Team Member</td>
                        <td id="member_td">
                        	<?php echo create_drop_down( "cbo_team_member", 150,  $blank_array, "", 1, "-- Select Member --", $selected, "load_drop_down( 'requires/aop_order_entry_controller', this.value+'_'+1, 'load_drop_down_member', 'member_td');"); ?>
                        </td>
                         <td class="must_entry_caption">Exchange Rate</td>
               			 <td><input type="text" name="txt_exchange_rate" id="txt_exchange_rate" style="width:140px" class="text_boxes_numeric"  value=""  readonly/></td>
                    </tr>
                     <tr>
                    	<td>Order Type</td>
                        <td><? echo create_drop_down( "cbo_order_type", 150, $aop_orde_type,"", 1, "--Select--",$selected,"","","" ); ?></td>
                        <td> Wo Type</td>
                        <td><? echo create_drop_down( "cbo_work_order_type", 150, $aop_work_order_type,"", 1, "--Select--",$selected,"","","" ); ?></td>
                    	<td>Remarks</td>
                        <td><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:140px;"  tabindex="15" /></td>
					</tr>
					<tr>
						<td>File / Image</td>
						<td >
							<input type="file" class="image_uploader" id="multiple_file_field" name="multiple_file_field" multiple="multiple" style="width:150px">
						</td>
						<td></td>
						<td>
							<input type="button" class="image_uploader" style="width:150px" maxlength="300" value="ADD FILE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'aop_order_entry', 2 ,1)">
						</td>
						<td></td>
						<td>
							<input type="button" class="image_uploader" style="width:150px" maxlength="300" value="Add Image" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'aop_order_entry', 1 ,1)">
						</td>
					</tr>
                </table>
        </fieldset> 
        <fieldset style="width:1970px;">
           <legend>AOP Order Details Entry
                <span style="margin-left:850px">
                Copy All <input type="checkbox" id="copy_to_all" name="copy_to_all" onClick="fnc_copyColorRangeProcess(this.id)" />
                &nbsp;      
                </span>
           
           </legend>
                <table width="1900px" cellpadding="0" cellspacing="2" border="1" class="rpt_table" rules="all" id="tbl_dtls_aop">
                    <thead class="form_table_header">
                        <th width="100" id="buyerpo_td">Buyer PO</th>
                        <th width="100" id="buyerstyle_td">Buyer Style Ref.</th>
                        <th width="110" id="buyerbuyer_td">Cust. Buyer </th>
                        <th width="80">Body part</th>
                        <th width="90" class="must_entry_caption">Construction</th>
                        <th width="90" class="must_entry_caption">Composition</th>
                        <th width="60" class="must_entry_caption">GSM</th>
                        <th width="60" class="must_entry_caption">Dia</th>
                        <th width="80" class="must_entry_caption">Gmts Color</th>
                        <th width="80" class="must_entry_caption">Item Color</th>
                        <th width="60" class="must_entry_caption">Fin. Dia</th>
                        <th width="80" class="must_entry_caption">AOP Color</th>
                        <th width="80" >Artwork No.</th>
                        <th width="60" >Print Type</th>
                        <th width="100" >Machine No.</th>
                        <th width="60" >Billing On</th>
                        <th width="70" class="must_entry_caption">Order Qty</th>
                        <th width="60" class="must_entry_caption">UOM</th>
                        <th width="60" class="must_entry_caption">Rate/Unit</th>
                        <th width="70">Amount</th>
                        <th width="70" class="must_entry_caption">P.Loss</th>
                        <th width="70" class="must_entry_caption">Delivery Date</th>
                        <th width="70">Design No.</th>
                        <th>Image</th>
                        <th></th>
                    </thead>
                    <tbody id="aop_details_container">
                        <tr id="row_1" >
                            <td><input name="txtbuyerPo_1" id="txtbuyerPo_1" type="text" class="text_boxes" style="width:87px" placeholder="Display"  />
                            	<input name="txtbuyerPoId_1" id="txtbuyerPoId_1" type="hidden" class="text_boxes" style="width:70px" />
                                <input type="hidden" name="txtSerial[]" id="txtSerial_1" class="text_boxes" value="1" readonly/>
                            </td>
                            <td><input name="txtstyleRef_1" id="txtstyleRef_1" type="text" class="text_boxes" style="width:87px" placeholder="Display"  /></td>
                            <td><input id="txtbuyer_1" name="txtbuyer[]" type="text" class="text_boxes" style="width:97px" placeholder="Display" /></td>
                            <td><? echo create_drop_down( "cboBodyPart_1", 80, $body_part,"", 1, "--Select--",0,"", 0 ); ?></td>
                            <td><input type="text" id="txtConstruction_1" name="txtConstruction_1" class="text_boxes"  style="width:77px" readonly /></td>
                            <td><input type="text" id="txtComposition_1" name="txtComposition_1" class="text_boxes" style="width:77px" readonly /></td>
                            <td><input type="text" id="txtGsm_1" name="txtGsm_1" class="text_boxes_numeric" style="width:47px" /></td>
                            <td><input type="text" id="txtDia_1" name="txtDia_1" class="text_boxes" style="width:47px" /></td>
                            <td><input type="text" id="txtGmtsColor_1" name="txtGmtsColor_1" class="text_boxes txt_color" style="width:67px" placeholder="Display" /></td>
                            <td><input type="text" id="txtItemColor_1" name="txtItemColor_1" class="text_boxes txt_color" style="width:67px" placeholder="Display" /></td>
                            <td><input type="text" id="txtFinDia_1" name="txtFinDia_1" class="text_boxes" style="width:47px" /></td>
                            <td><input type="text" id="txtAopColor_1" name="txtAopColor_1" class="text_boxes txt_color" style="width:67px" placeholder="Display" /></td>
                            <td><input type="text" id="txtArtwork_1" name="txtArtwork_1" class="text_boxes" style="width:67px" /></td>
                            <td><? echo create_drop_down( "cboPrintType_1", 60, $print_type,'', 1, '-Select-', $selected, "copy_process_loss(1)","","" ); ?></td>
                             <td id="machinetd_1"><?   
							  echo create_drop_down( "cboMachineName_1",100, $blank_array,'', 1, '-Select-',0,"copy_machine_name(1)","","","","","","","","cboMachineName[]");   
							 ?> 
                              </td>
                             
                            <td><? echo create_drop_down( "cboBillingOn_1", 60, $billing_on_arr,'', 1, '-Select-', $selected, "","","" ); ?></td>
                            <td><input type="text" id="txtQty_1" name="txtQty_1" class="text_boxes_numeric" style="width:57px" onKeyUp="cal_amount(1);" /></td>
                            <td><? echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,'', 1, '-Select-', $selected, "","","1,12,15,23,27" ); ?></td>
							<td><input type="text" id="txtRateUnit_1" name="txtRateUnit_1" class="text_boxes_numeric" style="width:47px" onKeyUp="cal_amount(1);" /></td>
							<td><input type="text" id="txtAmount_1" name="txtAmount_1" class="text_boxes_numeric" style="width:57px" readonly /></td>
							<td><input type="text" id="txtProcessLoss_1" name="txtProcessLoss_1" onKeyUp="copy_process_loss(1)" class="text_boxes_numeric" style="width:55px" />
                                <input type="hidden" name="hdnDtlsUpdateId_1" id="hdnDtlsUpdateId_1">
                                <input type="hidden" name="hdnlibyarndetar_1" id="hdnlibyarndetar_1">
                                <input type="hidden" name="hdnbookingDtlsId_1" id="hdnbookingDtlsId_1">
                            </td>
                            <td>
                            	<input type="text" name="txtOrderDeliveryDate_1" id="txtOrderDeliveryDate_1" class="datepicker" style="width:67px" onChange="chk_min_del_date(1)"  readonly/>
                            </td>
                            <td><input type="text" id="txtDesignNo_1" name="txtDesignNo_1" class="text_boxes" style="width:57px" />
                           	</td>
                           	<td id="image_1"><input type="button" class="image_uploader" name="txtFile_1" id="txtFile_1" onClick="file_uploader ( '../../', document.getElementById('hdnDtlsUpdateId_1').value,'', 'aoporderentry_1', 0 ,1)" style="" value="ADD IMAGE"></td>
                           	<td width="65">
								<input type="button" id="increase_1" name="increase[]" style="width:18px" class="formbuttonplasminus" value="+" onClick="add_dtls_tr(1)" />
								<input type="button" id="decrease_1" name="decrease[]" style="width:18px" class="formbuttonplasminus" value="-" onClick="fnc_delet_dtls_tr(1);" />
							</td>
                        </tr>
                    </tbody>
                </table>
                <table width="1600" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td align="center" valign="middle" class="button_container">
                        	<? echo load_submit_buttons( $permission, "fnc_job_order_entry", 0,0,"fnResetForm();",1); ?>
                        </td>
                    </tr>   
                </table>
            </fieldset> 
        </form>                         
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>