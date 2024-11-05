<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Price Quotation V3			
Functionality	:	
JS Functions	:
Created by		:	Md. Reaz Uddin
Creation date 	: 	26-02-2018
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
echo load_html_head_contents("Price Quotation V3", "../../", 1,1, $unicode,1,'');
?>

<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	function fnc_price_quotation_entry( operation )
	{	
		if(operation != 4)
		{
			var is_approved = $("#is_approved").val();
			
			if(is_approved == 1)
			{
				alert('This price quotation is approved. So, you can not any modification!');
				return;
			}
		}	

		if (form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}	
		else
		{
			if(operation==4){
				var updateId = $('#txt_update_id').val();
				//if(updateId=="") {alert("Save First"); return;}
				generate_report_file($('#cbo_company_id').val() + '*' + updateId + '*' + $('#cbo_gmts_type').val(), 'top_botton_report', 'price_quotation_controller_v3');
				return;
				
			}
			
			//Start : Costing Details Total Price Validation
			var cbo_ready_to_approved = $('#cbo_ready_to_approved').val();
		
			if(cbo_ready_to_approved==1)
			{
				if (form_validation('txt_order_conf_price*txt_order_conf_date','Confirm Price*Confirm Date')==false)
				{
					return;
				}	
			}
				
			var totPriceValidation = 1;
			var costingHeadText = "";
			var v=1;
			$("#costing_dtls").find('tr').each(function(){						
				if( v < 24 )
				{
					var idName = ($(this).find("td:eq(2)").find('input').attr('id')).split("__");
					var costingHead = ($(this).find("td:eq(0)").text());
					
					if( v == 7 && idName[0]=="txt_spandex") 
					{
					}
					else
					{
						totPrice  = ($("#"+idName[0]+"__"+1).val()*1) * ($("#"+idName[0]+"__"+2).val()*1);
						 if(totPrice==0){
							totPriceValidation = 0;
							costingHeadText += costingHead+", ";
						}
					}
					
					v++;	
				}
			});
			
			if(totPriceValidation==0){
				if( confirm("Are you sure, to save costing detailed ("+costingHeadText+") total price with zero(0)?") ){
					
				}else{
					return;
				}
			}
			//End : Costing Details Total Price Validation

			var dtlsData="";
			$("#tblFabricConsTop tbody tr").each(function()
			{
				var UpIdDtls 		= $(this).find('input[name="UpdateIdDtls[]"]').val();
				var GmtsType 		= $(this).find('select[name="cboGmtsType[]"]').val();
				var FabricSource 	= $(this).find('select[name="cboFabricSource[]"]').val();
				var FabricNatu 		= $(this).find('select[name="cboFabricNatu[]"]').val();
				var BodyLength 		= $(this).find('input[name="txtBodyLength[]"]').val();
				var SleeveLength 	= $(this).find('input[name="txtSleeveLength[]"]').val();
				var InseamLength 	= $(this).find('input[name="txtInseamLength[]"]').val();
				var FrontBackRise 	= $(this).find('input[name="txtFrontBackRise[]"]').val();
				var SleevAllow 		= $(this).find('input[name="txtSleevAllow[]"]').val();
				var Chest 			= $(this).find('input[name="txtChest[]"]').val();
				var Thigh 			= $(this).find('input[name="txtThigh[]"]').val();
				var ChestAllow 		= $(this).find('input[name="txtChestAllow[]"]').val();
				var Gsm 			= $(this).find('input[name="txtGsm[]"]').val();
				var BodyFabric 		= $(this).find('input[name="txtBodyFabric[]"]').val();
				var Wastage 		= $(this).find('input[name="txtWastage[]"]').val();
				var NetBodyFabric 	= $(this).find('input[name="txtNetBodyFabric[]"]').val();
				var Rib 			= $(this).find('input[name="txtRib[]"]').val();
				var TtlTopCons 		= $(this).find('input[name="txtTtlTopCons[]"]').val();
				
				if(dtlsData != ""){
					dtlsData += "**"+UpIdDtls+"_"+GmtsType+"_"+FabricSource+"_"+FabricNatu+"_"+BodyLength+"_"+SleeveLength+"_"+InseamLength+"_"+FrontBackRise+"_"+SleevAllow+"_"+Chest+"_"+Thigh+"_"+ChestAllow+"_"+Gsm+"_"+BodyFabric+"_"+Wastage+"_"+NetBodyFabric+"_"+Rib+"_"+TtlTopCons;
				}else{
					dtlsData += UpIdDtls+"_"+GmtsType+"_"+FabricSource+"_"+FabricNatu+"_"+BodyLength+"_"+SleeveLength+"_"+InseamLength+"_"+FrontBackRise+"_"+SleevAllow+"_"+Chest+"_"+Thigh+"_"+ChestAllow+"_"+Gsm+"_"+BodyFabric+"_"+Wastage+"_"+NetBodyFabric+"_"+Rib+"_"+TtlTopCons;
				}
				//j++;
				//dataStringTop+="&UpdateIdDtls_" + j + "='" + UpIdDtls + "'&cboGmtsType_" + j + "='" + GmtsType + "'&cboFabricSource_" + j + "='" + FabricSource + "'&cboFabricNatu_" + j + "='" + FabricNatu + "'&txtBodyLength_" + j + "='" + BodyLength + "'&txtSleeveLength_" + j + "='" + SleeveLength + "'&txtSleevAllow_" + j + "='" + SleevAllow+ "'&txtChest_" + j + "='" + Chest + "'&txtChestAllow_" + j + "='" + ChestAllow+ "'&txtGsm_" + j + "='" + Gsm+ "'&txtBodyFabric_" + j + "='" + BodyFabric+ "'&txtWastage_" + j + "='" + Wastage+ "'&txtNetBodyFabric_" + j + "='" + NetBodyFabric+ "'&txtRib_" + j + "='" + Rib + "'&txtTtlTopCons_" + j + "='" + TtlTopCons + "'";
			});
			

			$("#tblFabricConsBottom tbody tr").each(function()
			{
				
				var UpIdDtls 		= $(this).find('input[name="UpdateIdDtls[]"]').val();
				var GmtsType 		= $(this).find('select[name="cboGmtsType[]"]').val();
				var FabricSource 	= $(this).find('select[name="cboFabricSource[]"]').val();
				var FabricNatu 		= $(this).find('select[name="cboFabricNatu[]"]').val();
				var BodyLength 		= $(this).find('input[name="txtBodyLength[]"]').val();
				var SleeveLength 	= $(this).find('input[name="txtSleeveLength[]"]').val();
				var InseamLength 	= $(this).find('input[name="txtInseamLength[]"]').val();
				var FrontBackRise 	= $(this).find('input[name="txtFrontBackRise[]"]').val();
				var SleevAllow 		= $(this).find('input[name="txtSleevAllow[]"]').val();
				var Chest 			= $(this).find('input[name="txtChest[]"]').val();
				var Thigh 			= $(this).find('input[name="txtThigh[]"]').val();
				var ChestAllow 		= $(this).find('input[name="txtChestAllow[]"]').val();
				var Gsm 			= $(this).find('input[name="txtGsm[]"]').val();
				var BodyFabric 		= $(this).find('input[name="txtBodyFabric[]"]').val();
				var Wastage 		= $(this).find('input[name="txtWastage[]"]').val();
				var NetBodyFabric 	= $(this).find('input[name="txtNetBodyFabric[]"]').val();
				var Rib 			= $(this).find('input[name="txtRib[]"]').val();
				var TtlTopCons 		= $(this).find('input[name="txtTtlTopCons[]"]').val();
				
				
				if(dtlsData != ""){
					dtlsData += "**"+UpIdDtls+"_"+GmtsType+"_"+FabricSource+"_"+FabricNatu+"_"+BodyLength+"_"+SleeveLength+"_"+InseamLength+"_"+FrontBackRise+"_"+SleevAllow+"_"+Chest+"_"+Thigh+"_"+ChestAllow+"_"+Gsm+"_"+BodyFabric+"_"+Wastage+"_"+NetBodyFabric+"_"+Rib+"_"+TtlTopCons;
				}else{
					dtlsData += UpIdDtls+"_"+GmtsType+"_"+FabricSource+"_"+FabricNatu+"_"+BodyLength+"_"+SleeveLength+"_"+InseamLength+"_"+FrontBackRise+"_"+SleevAllow+"_"+Chest+"_"+Thigh+"_"+ChestAllow+"_"+Gsm+"_"+BodyFabric+"_"+Wastage+"_"+NetBodyFabric+"_"+Rib+"_"+TtlTopCons;
				}
				
				//k++;
				//dataStringBottom+="&UpdateIdDtls_" + k + "='" + UpIdDtls + "'&cboGmtsType_" + k + "='" + GmtsType + "'&cboFabricSource_" + k + "='" + FabricSource + "'&cboFabricNatu_" + k + "='" + FabricNatu + "'&txtInseamLength_" + k + "='" + InseamLength + "'&txtFrontBackRise_" + k + "='" + FrontBackRise + "'&txtSleevAllow_" + k + "='" + SleevAllow + "'&txtThigh_" + k + "='" + Thigh+ "'&txtChestAllow_" + k + "='" + ChestAllow+ "'&txtGsm_" + k + "='" + Gsm+ "'&txtBodyFabric_" + k + "='" + BodyFabric+ "'&txtWastage_" + k + "='" + Wastage+ "'&txtNetBodyFabric_" + k + "='" + NetBodyFabric+ "'&txtRib_" + k + "='" + Rib + "'&txtTtlBottomCons_" + k + "='" + TtlBottomCons + "'";
			});
			
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string("txt_system_id*txt_update_id*cbo_company_id*txt_team_member*cbo_buyer_name*txt_price_quota_date*cbo_agent*txt_style_ref*txt_gmts_item*txt_fabrication*txt_color*txt_yarn_count*txt_consumption_size*txt_order_qty*cbo_measurement_basis*txt_yarn__1*txt_yarn__2*txt_yarn__3*txt_knit_fab_purc__1*txt_knit_fab_purc__2*txt_knit_fab_purc__3*txt_woven_fab_purc__1*txt_woven_fab_purc__2*txt_woven_fab_purc__3*txt_yarn_dye_crg__1*txt_yarn_dye_crg__2*txt_yarn_dye_crg__3*txt_knit_crg__1*txt_knit_crg__2*txt_knit_crg__3*txt_dye_crg__1*txt_dye_crg__2*txt_dye_crg__3*txt_spandex__1*txt_spandex__2*txt_spandex__3*txt_spandex_amt__1*txt_aop__1*txt_aop__2*txt_aop__3*txt_knit_collar_cuff__1*txt_knit_collar_cuff__2*txt_knit_collar_cuff__3*txt_print__1*txt_print__2*txt_print__3*txt_wash_gmts_dye__1*txt_wash_gmts_dye__2*txt_wash_gmts_dye__3*txt_access_price__1*txt_access_price__2*txt_access_price__3*txt_zipper__1*txt_zipper__2*txt_zipper__3*txt_button__1*txt_button__2*txt_button__3*txt_test__1*txt_test__2*txt_test__3*txt_cm__1*txt_cm__2*txt_cm__3*txt_inspec_cost__1*txt_inspec_cost__2*txt_inspec_cost__3*txt_freight__1*txt_freight__2*txt_freight__3*txt_carrier_cost__1*txt_carrier_cost__2*txt_carrier_cost__3*txt_others__1*txt_others__2*txt_others__3*txt_others_caption*txt_others2__1*txt_others2__2*txt_others2__3*txt_others_caption2*txt_others3__1*txt_others3__2*txt_others3__3*txt_others_caption3*txt_comm_cost__1*txt_comm_cost__2*txt_comm_cost__3*txt_factory_cost*txt_fact_u_price*txt_agnt_comm*txt_local_comm*txt_final_offer_price*txt_order_conf_price*txt_order_conf_date*txt_agnt_comm_tot*txt_local_comm_tot*txt_remarks*txt_embro__1*txt_embro__2*txt_embro__3*cbo_uom_yarn*cbo_uom_knit_fab_purc*cbo_uom_woven_fab_purc*cbo_uom_yarn_dye_crg*cbo_uom_knit_crg*cbo_uom_dye_crg*cbo_uom_spandex*cbo_uom_aop*cbo_uom_collar_cuff*cbo_uom_print*cbo_uom_embro*cbo_uom_wash_gmts_dye*cbo_uom_access_price*cbo_uom_zipper*cbo_uom_button*cbo_uom_test*cbo_uom_cm*cbo_uom_inspec_cost*cbo_uom_freight*cbo_uom_carrier_cost*cbo_uom_others*cbo_uom_others2*cbo_uom_others3*txt_size_range*deleted_id_dtls*cbo_ready_to_approved","../../")+"&dtlsDataString="+ dtlsData;
			
			//alert(data); //return;
			
			freeze_window(operation);
			 
			http.open("POST","requires/price_quotation_controller_v3.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_price_quotation_entry_response;
		 
	 	}			
	}	
	
	 
	function fnc_price_quotation_entry_response()
	{
		
		if(http.readyState == 4) 
		{
			
			//release_freezing();
			var response=http.responseText.split('**');
			 
			show_msg(trim(response[0]));
			
			$("#txt_system_id").val(response[1]);
			$("#txt_update_id").val(response[2]);
			
			if(response[0]==14)	/*Update validation*/
			{
				alert(response[3]); 
				release_freezing();
				return;
			}
			
			if(response[0]==0 || response[0]==1)
			{
				//reset_form('price_quotation_1','','','','','');
				$('#tblFabricConsTop tbody tr').remove();
				$('#tblFabricConsBottom tbody tr').remove();
				var dtlsDatas   = return_global_ajax_value(response[2], "fab_cons_dtls_data", "requires/", "price_quotation_controller_v3");
				var dtlsDataArr =  dtlsDatas.split("__");
	
				$("#tblFabricConsTop tbody").html(dtlsDataArr[0]);
				$("#tblFabricConsBottom tbody").html(dtlsDataArr[1]);
				
				fnc_measurement_bassis(response[3]); //Measurement Basis field condition//
				
				set_button_status(1, permission, 'fnc_price_quotation_entry',1);
			}
			else
			{
				//reset_form('price_quotation_1','','','','','');
				//set_button_status(0, permission, 'fnc_price_quotation_entry',1);
			}
			release_freezing();
		}
		
	}
	
	
	function browse_system_number()
	{
		 emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/price_quotation_controller_v3.php?action=browse_system_number&cbo_company_name=' + $('#cbo_company_id').val(),'Price Quotation V3','width=1030px,height=400px,center=1,resize=0,scrolling=0','../')
        emailwindow.onclose = function ()
		{
			var theform 	= this.contentDoc.forms[0];
			var data 		= this.contentDoc.getElementById("hidden_system_number").value;
			reset_form('price_quotation_1','','','','','');
			get_php_form_data(data, "populate_information_form_data", "requires/price_quotation_controller_v3");
			
			var dtlsDatas   = return_global_ajax_value(data, "fab_cons_dtls_data", "requires/", "price_quotation_controller_v3");
			var dtlsDataArr =  dtlsDatas.split("__");
			
			//$("#tblFabricConsTop tbody").append(dtlsDataArr[0]);
			//$("#tblFabricConsBottom tbody").append(dtlsDataArr[1]);
			$("#tblFabricConsTop tbody").html(dtlsDataArr[0]);
			$("#tblFabricConsBottom tbody").html(dtlsDataArr[1]);
			
			
			$("#tblFabricConsTop tfoot tr #txtSubTtlTopCons").val(dtlsDataArr[2]);
			$("#tblFabricConsBottom tfoot tr #txtSubTtlTopCons").val(dtlsDataArr[3]);
			$("#tblFabricConsBottom tfoot tr #txtGrandTotalCons").val(dtlsDataArr[4]);
			var consId = $("#cbo_measurement_basis").val()*1;
			fnc_measurement_bassis(consId);
			set_all_onclick();
			
			
			var is_approved 	= $("#is_approved").val();
			// alert(is_approved);
			var msg = '';
			if(is_approved != 0)
			{
				msg = (is_approved == 1) ? 'This Quotation is Approved' : 'This Quotation is Partial Approved';
				$("#approved_msg").text(msg);
			}
			//var fNature 	= $("#cbo_fabric_natu").val();
			
			//enableDisableField(fNature); 	// for enable disable field
			//fn_fabric_cons(gmts_id); 		// for Garment Type
        }
	}
	
	function calculate_fabric_cons(parent_table,id) //parent_table,index,thisValue
	{
		
		var mBasis 	= $("#cbo_measurement_basis").val()*1;
		
		if( mBasis == 2 ) 		/*====== Measurement Basis===========*/
		{
			var fabricNatu 		= $("#"+parent_table+" tbody tr #cboFabricNatu_"+id).val()*1;
			var gmts_type 		= $("#"+parent_table+" tbody tr #cboGmtsType_"+id).val()*1;
			var fabric_source 	= $("#"+parent_table+" tbody tr #cboFabricSource_"+id).val()*1;
			var fabric_natu 	= $("#"+parent_table+" tbody tr #cboFabricNatu_"+id).val()*1;
			
			var body_length 	= $("#"+parent_table+" tbody tr #txtBodyLength_"+id).val()*1;
			var sleeve_length 	= $("#"+parent_table+" tbody tr #txtSleeveLength_"+id).val()*1;
			var inseam_length 	= $("#"+parent_table+" tbody tr #txtInseamLength_"+id).val()*1;
			var front_back_rise = $("#"+parent_table+" tbody tr #txtFrontBackRise_"+id).val()*1;
			
			var sleev_allow 	= $("#"+parent_table+" tbody tr #txtSleevAllow_"+id).val()*1;
			
			var chest 			= $("#"+parent_table+" tbody tr #txtChest_"+id).val()*1;
			var thigh	 		= $("#"+parent_table+" tbody tr #txtThigh_"+id).val()*1;
			
			var chest_allow 	= $("#"+parent_table+" tbody tr #txtChestAllow_"+id).val()*1;
			var gsm 			= $("#"+parent_table+" tbody tr #txtGsm_"+id).val()*1;
			var BodyFabricCons 	= $("#"+parent_table+" tbody tr #txtBodyFabric_"+id).val()*1;
			var wastage 		= $("#"+parent_table+" tbody tr #txtWastage_"+id).val()*1;
			var rib 			= $("#"+parent_table+" tbody tr #txtRib_"+id).val()*1;
			
			
			
			if( fabricNatu=="2" ) 		/*====== Knit Fabric===========*/
			{
					enableDisableField(fabricNatu,parent_table,id); 	// for enable disable field
					
					//gmts_type==1 -- 	Top
					//gmts_type==20 --  Bottom
					if(gmts_type==20){
						var result = (((inseam_length+front_back_rise+sleev_allow)*(thigh+chest_allow))*4)*((gsm/10000000)*12); 
						/*For show calculation in Title*/
						var resultString = "((("+inseam_length+"+"+front_back_rise+"+"+sleev_allow+")*("+thigh+"+"+chest_allow+"))*"+4+")*(("+gsm+"/"+10000000+")*"+12+")";
					}else{
						var result = (((body_length+sleeve_length+sleev_allow)*(chest+chest_allow))*2)*((gsm/10000000)*12);
						
						/*For show calculation in Title*/ 
						var resultString = "((("+body_length+"+"+sleeve_length+"+"+sleev_allow+")*("+chest+"+"+chest_allow+"))*"+2+")*(("+gsm+"/"+10000000+")*"+12+")"; 
					}
						
					var result2 = result + (result*wastage)/100;
					var result3 = result2 + (rib*result2)/100;
					
					$("#"+parent_table+" tbody tr #txtBodyFabric_"+id).val(result.toFixed(4));
					$("#"+parent_table+" tbody tr #txtNetBodyFabric_"+id).val(result2.toFixed(4));
					
					$("#"+parent_table+" tbody tr #txtTtlTopCons_"+id).val(result3.toFixed(4));
					
					
					/*Start : For show calculation in Title*/
					var result2String = result+"+("+result+"*"+wastage+"%)";
					var result3String = result2+"+("+rib+"*"+result2+"%)";
					
					$("#"+parent_table+" tbody tr #txtBodyFabric_"+id).attr("title",resultString);
					$("#"+parent_table+" tbody tr #txtNetBodyFabric_"+id).attr("title",result2String);
					
					/*End : For show calculation in Title*/
					
					/*if( fabric_source == 1 && fabric_natu == 2 ) 	// Production and Knit Finish Fabrics
					{
						$("#txt_yarn__1").attr("title",result3String);
						$("#txt_yarn__1").val(result3.toFixed(4));
						$("#txt_yarn__2").removeAttr("disabled");
						
					}
					else if( fabric_source == 2 && fabric_natu == 2 )	// Purchase and Knit Finish Fabrics
					{
						$("#txt_knit_fab_purc__1").attr("title",result3String);
						
						$("#txt_knit_fab_purc__1").val(result3.toFixed(4));
						$("#txt_knit_fab_purc__2").removeAttr("disabled");
					}*/
					
					/*Start: For show calculation in Title
					$("#txt_yarn_dye_crg__1").attr("title",result3String);
					$("#txt_knit_crg__1").attr("title",result3String);
					$("#txt_dye_crg__1").attr("title",result3String);
					End : For show calculation in Title*/
					
					/*$("#txt_yarn_dye_crg__1").val(result3.toFixed(4));
					$("#txt_knit_crg__1").val(result3.toFixed(4));
					$("#txt_dye_crg__1").val(result3.toFixed(4));*/
				
			}
			else if( fabricNatu=="3" ) 	/*====== Woven Fabric =========*/
			{
				
					if(wastage == 0){
						
						var result2 = BodyFabricCons;
					}else{
						var result2 = BodyFabricCons + (BodyFabricCons*wastage)/100;
					}
					
					if(rib==0){
						var result3 = result2;
					}else{
						var result3 = result2 + (rib*result2)/100;
					}

				
					$("#"+parent_table+" tbody tr #txtNetBodyFabric_"+id).val(result2);
					$("#"+parent_table+" tbody tr #txtTtlTopCons_"+id).val(result3);
					
					enableDisableField(fabricNatu,parent_table,id); 	// for enable disable field
			}
			else
			{
				//dddddddddd
			}
		}
		else 		/*====== Cad Bassis ===========*/
		{
			var fabricNatu 		= $("#"+parent_table+" tbody tr #cboFabricNatu_"+id).val()*1;
			var gmts_type 		= $("#"+parent_table+" tbody tr #cboGmtsType_"+id).val()*1;
			var fabric_source 	= $("#"+parent_table+" tbody tr #cboFabricSource_"+id).val()*1;
			var fabric_natu 	= $("#"+parent_table+" tbody tr #cboFabricNatu_"+id).val()*1;
			
			var body_length 	= $("#"+parent_table+" tbody tr #txtBodyLength_"+id).val()*1;
			var sleeve_length 	= $("#"+parent_table+" tbody tr #txtSleeveLength_"+id).val()*1;
			
			var inseam_length 	= $("#"+parent_table+" tbody tr #txtInseamLength_"+id).val()*1;
			var front_back_rise = $("#"+parent_table+" tbody tr #txtFrontBackRise_"+id).val()*1;
			
			var sleev_allow 	= $("#"+parent_table+" tbody tr #txtSleeveLength_"+id).val()*1;
			
			var chest 		= $("#"+parent_table+" tbody tr #txtChest_"+id).val()*1;
			var thigh	 	= $("#"+parent_table+" tbody tr #txtThigh_"+id).val()*1;
			
			var chest_allow = $("#"+parent_table+" tbody tr #txtChestAllow_"+id).val()*1;
			var gsm 		= $("#"+parent_table+" tbody tr #txtGsm_"+id).val()*1;
			
			var BodyFabricCons 	= $("#"+parent_table+" tbody tr #txtBodyFabric_"+id).val()*1;
			
			var wastage 	= $("#"+parent_table+" tbody tr #txtWastage_"+id).val()*1;
			var rib 		= $("#"+parent_table+" tbody tr #txtRib_"+id).val()*1;
			
			if( fabricNatu=="2" ) // Knit Fabric
			{
				
				/*if(gmts_type==20){
					//$("#"+parent_table+" tbody tr #txtBodyFabric_"+id).removeAttr("disabled");
					//$("#"+parent_table+" tbody tr #txtBodyFabric_"+id).attr("disabled","disabled");
				}else{
					//$("#"+parent_table+" tbody tr #txtBodyFabric_"+id).removeAttr("disabled");
					//$("#"+parent_table+" tbody tr #txtBodyFabric_"+id).attr("disabled","disabled");
				}*/
				var result2 = BodyFabricCons +(BodyFabricCons*wastage)/100;
				var result3 = result2 + (rib*result2)/100;
				$("#"+parent_table+" tbody tr #txtNetBodyFabric_"+id).val(result2.toFixed(4));
				$("#"+parent_table+" tbody tr #txtTtlTopCons_"+id).val(result3.toFixed(4));
				
			}
			else if( fabricNatu=="3" ) 	//====== Woven Fabric =========//
			{
				
					if(wastage == 0){
						
						var result2 = BodyFabricCons;
					}else{
						var result2 = BodyFabricCons + (BodyFabricCons*wastage)/100;
					}
					if(rib==0){
						var result3 = result2;
					}else{
						var result3 = result2 + (rib*result2)/100;
					}

				
					$("#"+parent_table+" tbody tr #txtNetBodyFabric_"+id).val(result2.toFixed(4));
					$("#"+parent_table+" tbody tr #txtTtlTopCons_"+id).val(result3.toFixed(4));
					
					enableDisableField(fabricNatu,parent_table,id); 	// for enable disable field
			}
		}
		
		
		
		/*For Sub total and Grand Total*/
		var y=1;
		var z=1;
		var subTotalTopCons = 0;
		var subTotalBottomCons = 0;
		var yarnCons = 0;
		var knitCons = 0;
		var wovenCons=0;
		
		$("#tblFabricConsTop tbody tr").each(function() /*====  Subtotal Top Cons ======*/
		{
			
			subTotalTopCons  = subTotalTopCons + $("#tblFabricConsTop tbody tr #txtTtlTopCons_"+y).val()*1;
			
			var fabricSource 	= $("#tblFabricConsTop tbody tr #cboFabricSource_"+y).val()*1;
			var fabricNatu 	= $("#tblFabricConsTop tbody tr #cboFabricNatu_"+y).val()*1;
			
			if( fabricSource == 1 && fabricNatu == 2 ) 	// Production and Knit Finish Fabrics
			{
				yarnCons = yarnCons + $("#tblFabricConsTop tbody tr #txtTtlTopCons_"+y).val()*1;
			}
			else if( fabricSource == 2 && fabricNatu == 2 )	// Purchase and Knit Finish Fabrics
			{
				knitCons = knitCons + $("#tblFabricConsTop tbody tr #txtTtlTopCons_"+y).val()*1;
			}
			else if( fabricSource == 2 && fabricNatu == 3 )	// Purchase and Woven Fabrics
			{
				wovenCons = wovenCons + $("#tblFabricConsTop tbody tr #txtTtlTopCons_"+y).val()*1;
			}
			
			y++;
		});
		
		$("#tblFabricConsTop tfoot tr #txtSubTtlTopCons").val(subTotalTopCons.toFixed(4));
		
		
		$("#tblFabricConsBottom tbody tr").each(function() /*====  Subtotal Bottom Cons ======*/
		{
			
			subTotalBottomCons  = subTotalBottomCons + $("#tblFabricConsBottom tbody tr #txtTtlTopCons_"+z).val()*1;
			if(subTotalBottomCons>0)
			{
				var fabricSource2 	= $("#tblFabricConsBottom tbody tr #cboFabricSource_"+z).val()*1;
				var fabricNatu2 	= $("#tblFabricConsBottom tbody tr #cboFabricNatu_"+z).val()*1;
				
				if( fabricSource2 == 1 && fabricNatu2 == 2 ) 	// Production and Knit Finish Fabrics
				{
					yarnCons = yarnCons + $("#tblFabricConsBottom tbody tr #txtTtlTopCons_"+z).val()*1;
				}
				else if( fabricSource2 == 2 && fabricNatu2 == 2 )	// Purchase and Knit Finish Fabrics
				{
					knitCons = knitCons + $("#tblFabricConsBottom tbody tr #txtTtlTopCons_"+z).val()*1;
				}
				else if( fabricSource2 == 2 && fabricNatu2 == 3 )	// Purchase and Woven Fabrics
				{
					wovenCons = wovenCons + $("#tblFabricConsBottom tbody tr #txtTtlTopCons_"+z).val()*1;
				}
				
				z++;
			}
		});
		
		
		
		if(subTotalBottomCons>0)
		{
			$("#tblFabricConsBottom tfoot tr #txtSubTtlTopCons").val(subTotalBottomCons.toFixed(4));
		}
		
		var grandTotal = ($("#tblFabricConsTop tfoot tr #txtSubTtlTopCons").val()*1) + ($("#tblFabricConsBottom tfoot tr #txtSubTtlTopCons").val()*1)
		$("#txtGrandTotalCons").val(grandTotal.toFixed(4));
		/*For Sub total and Grand Total*/
		
		if( fabric_source == 1 && fabric_natu == 2 ) 	// Production and Knit Finish Fabrics
		{
			$("#txt_yarn__1").val(yarnCons.toFixed(4));
			$("#txt_yarn_dye_crg__1").val(yarnCons.toFixed(4));
			$("#txt_knit_crg__1").val(yarnCons.toFixed(4));
			$("#txt_dye_crg__1").val(yarnCons.toFixed(4));
			//$("#txt_yarn__2").removeAttr("disabled");
		}
		else if( fabric_source == 2 && fabric_natu == 2 )	// Purchase and Knit Finish Fabrics
		{
			$("#txt_knit_fab_purc__1").val(knitCons.toFixed(4));
			//$("#txt_knit_fab_purc__2").removeAttr("disabled");
		}
		else if( fabric_source == 2 && fabric_natu == 3 )	// Purchase and Woven Fabrics
		{
			$("#txt_woven_fab_purc__1").val(wovenCons.toFixed(4));
			//$("#txt_knit_fab_purc__2").removeAttr("disabled");
		}
		
		
		dtls_data_auto_calculate();
	}
	
	function dtls_data_auto_calculate()
	{
		
		var v=1;
		var spandex_amt_pre = $("#txt_spandex_amt__1").val()*1;
		
		$("#costing_dtls").find('tr').each(function(){						/*Costing Details value re calculate*/
			var totPrice = 0;
			if( v < 24 )
			{
				var idName = ($(this).find("td:eq(2)").find('input').attr('id')).split("__");
				
				//if(idName[0]=="txt_spandex" || idName[0]=="txt_spandex_amt") /*Spandex calculation*/
				if( v == 7 && idName[0]=="txt_spandex") /*Spandex calculation*/
				 {
					 var dye_crg = $("#txt_dye_crg__1").val()*1;
					 if(dye_crg>0)
					 {
						 var spandexamount = dye_crg * $("#txt_spandex__1").val() / 100;
						 $("#txt_spandex_amt__1").val(spandexamount.toFixed(4));
						 totPrice = spandexamount  * ( $("#txt_spandex__2").val()*1 );
						 $("#txt_spandex__3").val(totPrice.toFixed(4));
						 var aop_cons = (dye_crg+spandexamount).toFixed(4);
						 
						 var spandex_amt_current = spandexamount.toFixed(4);
						 
						 if(spandex_amt_pre != spandex_amt_current){ //For stop auto calculate
							$("#txt_aop__1").val(aop_cons);
						 }
						 
					 }
				 }
				 else
				 {
					totPrice  = ($("#"+idName[0]+"__"+1).val()*1) * ($("#"+idName[0]+"__"+2).val()*1);
					$("#"+idName[0]+"__"+3).val(totPrice.toFixed(4)); 
				 }
				 
			}
			v++;
		});
		
		var subTot = 0;
		$(".totalprice").each(function() {
			var tVal = $.trim( $(this).val() );
			
			if ( tVal ) {
				tVal = parseFloat( tVal.replace( /^\$/, "" ) );
			
				subTot += !isNaN( tVal ) ? tVal : 0;
			}
		});
		
		$("#txt_sub_total").val(subTot.toFixed(4));
		
		var commercialCost = 0;
		var commCostPercent = $("#txt_comm_cost__1").val()*1;
		if(commCostPercent>=0)
		{
			commercialCost = (subTot*commCostPercent/100);
			$("#txt_comm_cost__3").val(commercialCost.toFixed(4));
		}
		var factory_cost = (subTot+commercialCost);
		$("#txt_factory_cost").val(factory_cost.toFixed(4));
		
		var fact_u_price = (factory_cost/12);
		$("#txt_fact_u_price").val( fact_u_price.toFixed(4) );
		
		
		var final_offer_price = fact_u_price;
		var agnt_comm = $("#txt_agnt_comm").val()*1;
		var local_comm = $("#txt_local_comm").val()*1;
		if(agnt_comm >= 0)
		{
			//var final_offer_price = ((fact_u_price*agnt_comm/100)+fact_u_price).toFixed(4);
			var agnt_comm_amt = (fact_u_price*agnt_comm/100);
			$("#txt_agnt_comm_tot").val(agnt_comm_amt.toFixed(4)); 
			final_offer_price += agnt_comm_amt;
		}
		if(local_comm >= 0)
		{
			//var final_offer_price = ((fact_u_price*agnt_comm/100)+fact_u_price).toFixed(4);
			var local_comm_amt = (fact_u_price*local_comm/100);
			$("#txt_local_comm_tot").val(local_comm_amt.toFixed(4));
			final_offer_price += local_comm_amt;
		}
		$("#txt_final_offer_price").val((final_offer_price).toFixed(4)); 
	}
	
	
	
	
	function enableDisableField(fNature,parent_table,id)
	{
		if(fNature==2)
		{
			
			$("#"+parent_table+" tbody tr #txtBodyFabric_"+id).attr("disabled","disabled");
			$("#"+parent_table+" tbody tr #txtRib_"+id).removeAttr("disabled");
			
			/*
			$("#txt_woven_fab_purc__1").val('').attr("disabled","disabled");
			$("#txt_woven_fab_purc__2").val('').attr("disabled","disabled");
			$("#txt_woven_fab_purc__3").val('');
			
			$("#txt_yarn__2").removeAttr("disabled");
			
			$("#txt_knit_fab_purc__2").removeAttr("disabled");
			
			$("#txt_yarn_dye_crg__2").removeAttr("disabled");
			
			$("#txt_knit_crg__2").removeAttr("disabled");
			
			$("#txt_dye_crg__2").removeAttr("disabled");
			
			$("#txt_spandex__1").removeAttr("disabled");
			$("#txt_spandex__2").removeAttr("disabled");
			
			$("#txt_aop__2").removeAttr("disabled");
			*/
			
		}
		else if(fNature==3)
		{
			//$("#"+parent_table+" tbody tr #txtNetBodyFabric_"+id).val('');
			//$("#"+parent_table+" tbody tr #txtRib_"+id).attr("disabled","disabled");
			$("#"+parent_table+" tbody tr #txtBodyFabric_"+id).removeAttr("disabled");
			$("#"+parent_table+" tbody tr #txtRib_"+id).removeAttr("disabled");
			
			
			//$("#"+parent_table+" tbody tr #txtBodyFabric_"+id).removeAttr("readonly");
			$("#"+parent_table+" tbody tr #cboFabricSource_"+id).val('2');
			
			/*
			$("#txt_woven_fab_purc__1").removeAttr("disabled");
			$("#txt_woven_fab_purc__2").removeAttr("disabled");
			
			$("#cbo_fabric_source").val('2');
			$("#txt_body_fabric").removeAttr("disabled");
			$("#txt_body_fabric").removeAttr("readonly");
			
			$("#txt_yarn__1").val('');
			$("#txt_yarn__2").val('').attr("disabled","disabled");
			$("#txt_yarn__3").val('');
			
			$("#txt_knit_fab_purc__1").val('');
			$("#txt_knit_fab_purc__2").val('').attr("disabled","disabled");
			$("#txt_knit_fab_purc__3").val('');
			
			$("#txt_yarn_dye_crg__1").val('');
			$("#txt_yarn_dye_crg__2").val('').attr("disabled","disabled");
			$("#txt_yarn_dye_crg__3").val('');
			
			$("#txt_knit_crg__1").val('');
			$("#txt_knit_crg__2").val('').attr("disabled","disabled");
			$("#txt_knit_crg__3").val('');
			
			$("#txt_dye_crg__1").val('');
			$("#txt_dye_crg__2").val('').attr("disabled","disabled");
			$("#txt_dye_crg__3").val('');
			
			$("#txt_spandex__1").val('').attr("disabled","disabled");
			$("#txt_spandex__2").val('').attr("disabled","disabled");
			
			$("#txt_aop__1").val('');
			$("#txt_aop__2").val('').attr("disabled","disabled");
			$("#txt_aop__3").val('');
			*/
			
		}
		else
		{
			
		}
		/*var sum = 0;
		$(".totalprice").each(function() {
			var tVal = $.trim( $(this).val() );
			
			if ( tVal ) {
				tVal = parseFloat( tVal.replace( /^\$/, "" ) );
			
				sum += !isNaN( tVal ) ? tVal : 0;
			}
		});
		$("#txt_sub_total").val(sum.toFixed(4));*/
	}
	
	function fnc_measurement_bassis(mId)
	{
		
		if(mId==1){
			//$(".is_cad_basis").val('')
			$(".is_cad_basis").removeAttr("disabled");
			$(".is_cad_basis_enable").removeAttr("disabled");
			
			$(".is_cad_basis").attr("disabled","disabled");
			
			
		}
		else
		{
			//$(".is_cad_basis").val('')
			$(".is_cad_basis").removeAttr("disabled");
			$(".is_cad_basis_enable").attr("disabled","disabled");
		}
	}
	
	

	function add_break_down_tr(i) 
	{
		//alert( i);
		var row_num = $('#tblFabricConsTop tbody tr').length;
		if (row_num != i) {
			return false;
		} else {
			i++;
			//$('#samplepic_' + i).removeAttr("src,value");
			if (row_num < row_num + 1) {
				$("#tblFabricConsTop tbody tr:last").clone().find("input,select").each(function () {
					$(this).attr({
						'id': function (_, id) {
							var id = id.split("_");
							//alert(id);
							return id[0] + "_" + i;
						},
						/*'name': function (_, name) {
							var name = name.split("_");
							return name[0] + "_" + i;
						},*/
						'value': function (_, value) {
							return value
						},
						'src': function (_, src) {
							return src
						}
					});
				}).end().appendTo("#tblFabricConsTop tbody");
				
				$('#tblFabricConsTop tbody tr #cboGmtsType_' + i).val();
				$('#tblFabricConsTop tbody tr #cboFabricSource_' + i).val();
				$('#tblFabricConsTop tbody tr #cboFabricNatu_' + i).val();
				
				$('#tblFabricConsTop tbody tr #txtBodyLength_' + i).val('');
				$('#tblFabricConsTop tbody tr #txtSleeveLength_' + i).val('');
				$('#tblFabricConsTop tbody tr #txtInseamLength_' + i).val('');
				$('#tblFabricConsTop tbody tr #txtFrontBackRise_' + i).val('');
				$('#tblFabricConsTop tbody tr #txtSleevAllow_' + i).val('');
				$('#tblFabricConsTop tbody tr #txtChest_' + i).val('');
				$('#tblFabricConsTop tbody tr #txtThigh_' + i).val('');
				$('#tblFabricConsTop tbody tr #txtChestAllow_' + i).val('');
				$('#tblFabricConsTop tbody tr #txtGsm_' + i).val('');
				$('#tblFabricConsTop tbody tr #txtBodyFabric_' + i).val('');
				$('#tblFabricConsTop tbody tr #txtWastage_' + i).val('');
				$('#tblFabricConsTop tbody tr #txtNetBodyFabric_' + i).val('');
				$('#tblFabricConsTop tbody tr #txtRib_' + i).val('');
				$('#tblFabricConsTop tbody tr #txtTtlTopCons_' + i).val('');
				$('#tblFabricConsTop tbody tr #UpdateIdDtls_' + i).val('');
				
				
				$('#tblFabricConsTop tbody tr #txtBodyLength_' + i).removeAttr("onBlur").attr("onBlur", "calculate_fabric_cons($(this).closest('table').attr('id'),'"+i+"')");
				$('#tblFabricConsTop tbody tr #txtSleeveLength_' + i).removeAttr("onBlur").attr("onBlur", "calculate_fabric_cons()");
				
				$('#tblFabricConsTop tbody tr #txtInseamLength_' + i).removeAttr("onBlur").attr("onBlur", "calculate_fabric_cons($(this).closest('table').attr('id'),'"+i+"')");
				$('#tblFabricConsTop tbody tr #txtFrontBackRise_' + i).removeAttr("onBlur").attr("onBlur", "calculate_fabric_cons($(this).closest('table').attr('id'),'"+i+"')");
				
				$('#tblFabricConsTop tbody tr #txtSleevAllow_' + i).removeAttr("onBlur").attr("onBlur", "calculate_fabric_cons($(this).closest('table').attr('id'),'"+i+"')");
				
				$('#tblFabricConsTop tbody tr #txtChest_' + i).removeAttr("onBlur").attr("onBlur", "calculate_fabric_cons($(this).closest('table').attr('id'),'"+i+"')");
				
				$('#tblFabricConsTop tbody tr #txtThigh_' + i).removeAttr("onBlur").attr("onBlur", "calculate_fabric_cons($(this).closest('table').attr('id'),'"+i+"')");
				
				$('#tblFabricConsTop tbody tr #txtChestAllow_' + i).removeAttr("onBlur").attr("onBlur", "calculate_fabric_cons($(this).closest('table').attr('id'),'"+i+"')");
				$('#tblFabricConsTop tbody tr #txtGsm_' + i).removeAttr("onBlur").attr("onBlur", "calculate_fabric_cons($(this).closest('table').attr('id'),'"+i+"')");
				$('#tblFabricConsTop tbody tr #txtBodyFabric_' + i).removeAttr("onBlur").attr("onBlur", "calculate_fabric_cons($(this).closest('table').attr('id'),'"+i+"')");
				$('#tblFabricConsTop tbody tr #txtWastage_' + i).removeAttr("onBlur").attr("onBlur", "calculate_fabric_cons($(this).closest('table').attr('id'),'"+i+"')");
				$('#tblFabricConsTop tbody tr #txtNetBodyFabric_' + i).removeAttr("onBlur").attr("onBlur", "calculate_fabric_cons($(this).closest('table').attr('id'),'"+i+"')");
				$('#tblFabricConsTop tbody tr #txtRib_' + i).removeAttr("onBlur").attr("onBlur", "calculate_fabric_cons($(this).closest('table').attr('id'),'"+i+"')");
				
				
				$("#tblFabricConsTop tbody tr:last ").removeAttr('id').attr('id', 'fabTr_' + i);
				$('#tblFabricConsTop tbody tr #btnadd_' + i).removeAttr("onclick").attr("onclick", "add_break_down_tr(" + i + ");");
				$('#tblFabricConsTop tbody tr #decrease_' + i).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + i + ");");

				set_all_onclick();
			}
		}
	}
	

	function fn_deleteRow(rowNo) 
	{
		
		var deleted_row = $("#deleted_id_dtls").val();
		
		if (deleted_row != "") deleted_row = deleted_row + ",";
			
		var numRow = $('#tblFabricConsTop tbody tr').length;
		//alert(numRow+'__'+rowNo);
		if (numRow == rowNo && numRow == 1) {
			return false;
		} else {
			deleted_row = deleted_row + $("#tblFabricConsTop tbody tr #UpdateIdDtls_" + rowNo).val();
			$("#tblFabricConsTop tbody tr:#fabTr_" + rowNo).remove();
		}
		$("#deleted_id_dtls").val(deleted_row);
		//calculate_total_qty();
	}
	
	
	function add_break_down_tr_bottom(j) 
	{
		//alert( j);
		var row_num = $('#tblFabricConsBottom tbody tr').length;
		if (row_num != j) {
			return false;
		} else {
			j++;
			//$('#samplepic_' + j).removeAttr("src,value");
			if (row_num < row_num + 1) {
				$("#tblFabricConsBottom tbody tr:last").clone().find("input,select").each(function () {
					$(this).attr({
						'id': function (_, id) {
							var id = id.split("_");
							//alert(id);
							return id[0] + "_" + j;
						},
						/*'name': function (_, name) {
							var name = name.split("_");
							return name[0] + "_" + j;
						},*/
						'value': function (_, value) {
							return value
						},
						'src': function (_, src) {
							return src
						}
					});
				}).end().appendTo("#tblFabricConsBottom tbody");
				
				$('#tblFabricConsBottom tbody tr #cboGmtsType_' + j).val();
				$('#tblFabricConsBottom tbody tr #cboFabricSource_' + j).val();
				$('#tblFabricConsBottom tbody tr #cboFabricNatu_' + j).val();
				
				$('#tblFabricConsBottom tbody tr #txtBodyLength_' + j).val('');
				$('#tblFabricConsBottom tbody tr #txtSleeveLength_' + j).val('');
				$('#tblFabricConsBottom tbody tr #txtInseamLength_' + j).val('');
				$('#tblFabricConsBottom tbody tr #txtFrontBackRise_' + j).val('');
				$('#tblFabricConsBottom tbody tr #txtSleevAllow_' + j).val('');
				$('#tblFabricConsBottom tbody tr #txtChest_' + j).val('');
				$('#tblFabricConsBottom tbody tr #txtThigh_' + j).val('');
				$('#tblFabricConsBottom tbody tr #txtChestAllow_' + j).val('');
				$('#tblFabricConsBottom tbody tr #txtGsm_' + j).val('');
				$('#tblFabricConsBottom tbody tr #txtBodyFabric_' + j).val('');
				$('#tblFabricConsBottom tbody tr #txtWastage_' + j).val('');
				$('#tblFabricConsBottom tbody tr #txtNetBodyFabric_' + j).val('');
				$('#tblFabricConsBottom tbody tr #txtRib_' + j).val('');
				$('#tblFabricConsBottom tbody tr #txtTtlTopCons_' + j).val('');
				$('#tblFabricConsBottom tbody tr #UpdateIdDtls_' + j).val('');
				
				
				
				$('#tblFabricConsBottom tbody tr #txtBodyLength_' + j).removeAttr("onBlur").attr("onBlur", "calculate_fabric_cons($(this).closest('table').attr('id'),'"+j+"')");
				$('#tblFabricConsBottom tbody tr #txtSleeveLength_' + j).removeAttr("onBlur").attr("onBlur", "calculate_fabric_cons($(this).closest('table').attr('id'),'"+j+"')");
				
				$('#tblFabricConsBottom tbody tr #txtInseamLength_' + j).removeAttr("onBlur").attr("onBlur", "calculate_fabric_cons($(this).closest('table').attr('id'),'"+j+"')");
				$('#tblFabricConsBottom tbody tr #txtFrontBackRise_' + j).removeAttr("onBlur").attr("onBlur", "calculate_fabric_cons($(this).closest('table').attr('id'),'"+j+"')");
				
				$('#tblFabricConsBottom tbody tr #txtSleevAllow_' + j).removeAttr("onBlur").attr("onBlur", "calculate_fabric_cons($(this).closest('table').attr('id'),'"+j+"')");
				
				$('#tblFabricConsBottom tbody tr #txtChest_' + j).removeAttr("onBlur").attr("onBlur", "calculate_fabric_cons($(this).closest('table').attr('id'),'"+j+"')");
				
				$('#tblFabricConsBottom tbody tr #txtThigh_' + j).removeAttr("onBlur").attr("onBlur", "calculate_fabric_cons($(this).closest('table').attr('id'),'"+j+"')");
				
				$('#tblFabricConsBottom tbody tr #txtChestAllow_' + j).removeAttr("onBlur").attr("onBlur", "calculate_fabric_cons($(this).closest('table').attr('id'),'"+j+"')");
				$('#tblFabricConsBottom tbody tr #txtGsm_' + j).removeAttr("onBlur").attr("onBlur", "calculate_fabric_cons($(this).closest('table').attr('id'),'"+j+"')");
				$('#tblFabricConsBottom tbody tr #txtBodyFabric_' + j).removeAttr("onBlur").attr("onBlur", "calculate_fabric_cons($(this).closest('table').attr('id'),'"+ j +"')");
				$('#tblFabricConsBottom tbody tr #txtWastage_' + j).removeAttr("onBlur").attr("onBlur", "calculate_fabric_cons($(this).closest('table').attr('id'),'"+j+"')");
				$('#tblFabricConsBottom tbody tr #txtNetBodyFabric_' + j).removeAttr("onBlur").attr("onBlur", "calculate_fabric_cons($(this).closest('table').attr('id'),'"+j+"')");
				$('#tblFabricConsBottom tbody tr #txtRib_' + j).removeAttr("onBlur").attr("onBlur", "calculate_fabric_cons($(this).closest('table').attr('id'),'"+j+"')");
				
				//$('#txtTtlTopCons_' + j).val('');
				//$('#UpdateIdDtls_' + j).val('');
				$("#tblFabricConsBottom tbody tr:last ").removeAttr('id').attr('id', 'fabBottomTr_' + j);
				//$('#txtMstID_' + j).removeAttr("onBlur").attr("onBlur", "calculate_fabric_cons()");
				//$("#txtqtyset_"+j).removeAttr('class','text_boxes_numeric').attr('class', 'text_boxes_numeric');
				//$('#decrease_'+j).removeAttr("value").attr("value","-");
				$('#tblFabricConsBottom tbody tr #btnaddBottom_' + j).removeAttr("onclick").attr("onclick", "add_break_down_tr_bottom(" + j + ");");
				$('#tblFabricConsBottom tbody tr #decreaseBottom_' + j).removeAttr("onclick").attr("onclick", "fn_deleteRow_bottom(" + j + ");");



				//$('#txtQty_' + j).attr('class', 'text_boxes_numeric');
				//var result = parseInt(num1) + parseInt(num2);
				set_all_onclick();
			}
		}
	}

	function fn_deleteRow_bottom(rowNo) 
	{
		
		var deleted_row = $("#deleted_id_dtls").val();
		
		if (deleted_row != "") deleted_row = deleted_row + ",";
			
		var numRow = $('#tblFabricConsBottom tbody tr').length;
		//alert(numRow+'__'+rowNo);
		if (numRow == rowNo && numRow == 1) {
			return false;
		} else {
			deleted_row = deleted_row + $("#tblFabricConsBottom tbody tr #UpdateIdDtls_" + rowNo).val();
			$("#tblFabricConsBottom tbody tr:#fabBottomTr_" + rowNo).remove();
		}
		$("#deleted_id_dtls").val(deleted_row);
		//calculate_total_qty();
	}
	
	
	function generate_report_file(data, action, page) 
	{
        window.open("requires/price_quotation_controller_v3.php?data=" + data + '&action=' + action, true);
    }
	
	function fnc_hide_show()
	{
		accordion_menu( 'accordion_h2','container2', '');
		accordion_menu( 'accordion_h3','container3', '');
	}
	
	function fnc_pdf_doc_print()
	{
		if (form_validation('txt_system_id','txt_system_id')==false)
		{
			return;
		}
		var data = $("#cbo_company_id").val()+"__"+$("#txt_update_id").val()*1+"__"+$("#txt_system_id").val();
		http.open( 'POST', 'requires/price_quotation_controller_v3.php?action=create_pdf_pages&data='+ data );
		http.onreadystatechange = response_pdf_data;
		http.send(null);
	}
	function response_pdf_data() 
	{
		if(http.readyState == 4) 
		{
			var response = http.responseText.split('###');
			window.open('requires/'+response[1], '', '');
		}
	}
	
    </script>

</head>
<body onLoad="set_hotkey();">
	<div style="width:100%;">
		<? echo load_freeze_divs ("../../",$permission); ?>
        
        <fieldset>
            <div style="width:1100px; float:left;" align="left">
                 <form name="price_quotation_1" id="price_quotation_1" autocomplete="off"> 
                    <table cellpadding="0" cellspacing="2" width="1080" style="margin-top: 10px;">
                    	<!--<tr><td colspan="12"><h1 style="color:#F30;font-size:30px;">Under QC</h1></td></tr>-->
                        <tr>
                            <td align="right" colspan="6" width="490">System ID </td>
                            <td align="left" colspan="6" width="490">
                                <input type="text" id="txt_system_id" name="txt_system_id" onDblClick="browse_system_number()" class="text_boxes" value="" style="width:150px;" placeholder="Browse"/> 
                                <input type="hidden" id="txt_update_id" name="txt_update_id" class="text_boxes"  value="" width="150"/> 
                                <input type="hidden" name="deleted_id_dtls" id="deleted_id_dtls" /> 
                             </td>
                             <div id="approved_msg" style="font-size: 16px;color:red;position: absolute;right: 27%"></div>
                        </tr>
                    </table>
                    <div>
                        &nbsp;
                        <h3 style="width:1080px;" align="left" id="accordion_h2" class="accordion_h" onClick="accordion_menu(this.id,'container2','')"> -Basic Info</h3> 
                        <div id="container2">
                            <table cellspacing="0" width="1000" class="rpt_table" id="tbl_pi_item" rules="all" align="center"  style="margin-bottom: 10px;">
                               <thead>                            
                                    <th width="">Company</th>
                                    <th width="">Team Member</th>
                                    <th width="">Buyer</th>
                                    <th width="">Entry Date</th>
                                    <th width="">Agent</th>
                                    <th width="">Style Ref</th>
                                    <th width="">Garment Item</th>
                                    <th width="">Fabrication</th>
                                    <th width="">Color</th>
                                    <th width="">Yarn Count</th>
                                    <th width="">Consumption Size</th>
                                    <th width="">Size<br>Range</th>
                                    <th width="">Order Qty</th>
                                    <th width="">Consumption<br>Basis</th>
                                </thead>
                                <tbody class="general">
                                    <tr>
                                        <td>
                                        <? 
                                        	echo create_drop_down( "cbo_company_id", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and core_business not in(3) order by comp.company_name","id,company_name", 1, "-- Select --",$selected,"load_drop_down( 'requires/price_quotation_controller_v3', this.value,'load_drop_down_buyer','buyer_td' );load_drop_down( 'requires/price_quotation_controller_v3', this.value,'load_drop_down_agent','agent_td' );", 0 );
                                        ?>
                                        </td>
                                        <td>
                                        	<input type="text" name="txt_team_member" id="txt_team_member" class="text_boxes" value="" style="width: 100px;" placeholder="Write" />
                                        </td>
                                        <td id="buyer_td">
										<? 
                                        	echo create_drop_down( "cbo_buyer_name", 100,$blank_array,"", 1, "-- Select --",$selected,"", 0 );
                                        ?>
                                        </td>
                                        <td><input type="text" name="txt_price_quota_date" id="txt_price_quota_date" class="datepicker" value="" style="width: 55px;" placeholder="Date" /></td>
                                        <td id="agent_td">
                                        <?	
											echo create_drop_down( "cbo_agent", 100,$blank_array,"", 1, "-- Select --",$selected,"", 0 );
										?>
                                        </td>
                                        <td><input type="text" name="txt_style_ref" id="txt_style_ref"  value="" class="text_boxes"  style="width:80px" placeholder="Write" /></td>
                                        <td><input type="text" name="txt_gmts_item" id="txt_gmts_item"  value="" class="text_boxes"  style="width:80px" placeholder="Write" /></td>
                                        <td><input type="text" name="txt_fabrication" id="txt_fabrication"  value="" class="text_boxes"  style="width:80px" placeholder="Write"  /></td>
                                        <td><input type="text" name="txt_color" id="txt_color"  value="" class="text_boxes"  style="width:80px" placeholder="Write" /></td>
                                        <td><input type="text" name="txt_yarn_count" id="txt_yarn_count"  value="" class="text_boxes"  style="width:50px" placeholder="Write"  /></td>
                                        <td><input type="text" name="txt_consumption_size" id="txt_consumption_size" value="" class="text_boxes"  style="width:60px" placeholder="Write" /></td>
                                        <td><input type="text" name="txt_size_range" id="txt_size_range" value="" class="text_boxes"  style="width:60px" placeholder="Write" /></td>
                                        <td><input type="text" name="txt_order_qty" id="txt_order_qty" value="" class="text_boxes_numeric"  style="width:60px" placeholder="Write" /></td>
                                        <td>
                                        <? 
											$measurement_basis_arr = array(1=>"Cad Bassis", 2=>"Measurement Basis");
                                        	echo create_drop_down( "cbo_measurement_basis",100, $measurement_basis_arr,"",0, "-- Select --","2","fnc_measurement_bassis(this.value)","","" );
                                        ?>
                                        </td>
                                    </tr> 
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div>
                        &nbsp;
                        <h3 style="width:1080px;" align="left" id="accordion_h3" class="accordion_h" onClick="accordion_menu(this.id,'container3','')"> -Fabric Consumption/Dz</h3> 
                        <div id="container3">
                            <table id="tblFabricConsTop" cellspacing="0" width="1080" class="rpt_table" rules="all" align="center" style="margin-bottom: 10px;">
                                <thead> 
                                    <th>Garment<br>Type</th>
                                    <th>Source </th>
                                    <th>Fabric Nature</th>
                                    <th>Body<br>Length</th>
                                    <th>Sleeve<br>Length</th>
                                    <th>Allow</th>
                                    <th>1/2 Chest</th>
                                    <th>Allow</th>
                                    <th>GSM</th>
                                    <th>Body Fabric<br>Cons</th>
                                    <th>Wastage<br>%</th>
                                    <th>Net Body<br>Fabric</th>
                                    <th>Rib<br>%</th>
                                    <th>TTL<br>Top Cons</th>
                                    <th>Action</th>
                                </thead>
                                <tbody  class="general">
                                    <tr id="fabTr_1">
                                        <td><? echo create_drop_down( "cboGmtsType_1",70, $body_part_type,"",0, "-- Select --",$selected,"","","1","","","","","","cboGmtsType[]" ); ?></td>
                                        <td><? echo create_drop_down( "cboFabricSource_1",90,$fabric_source,"",2,"-- Select --", "1","", "", "1,2","","","","","","cboFabricSource[]");	?></td>
                                        <td><? echo create_drop_down( "cboFabricNatu_1",130,$item_category,"",2,"-- Select --","2","","", "2,3","","","","","","cboFabricNatu[]"); ?></td>
                                        
                                        
                                        
                                        
                                        <td>
                                            <input type="text" name="txtBodyLength[]" id="txtBodyLength_1" onBlur="calculate_fabric_cons( $(this).closest('table').attr('id'),1 )" class="text_boxes_numeric is_cad_basis" value="" style="width:45px"  placeholder="Write" />
                                        </td>
                                        <td>
                                         <input type="text" name="txtSleeveLength[]" id="txtSleeveLength_1" onBlur="calculate_fabric_cons( $(this).closest('table').attr('id'),1 )" class="text_boxes_numeric is_cad_basis"  value="" style="width:45px" placeholder="Write" />
                                        </td>
                                        
                                        
                                        
                                        
                                        <td style="display:none"><input type="text" name="txtInseamLength[]" id="txtInseamLength_1" onBlur="calculate_fabric_cons( $(this).closest('table').attr('id'),1 )" class="text_boxes_numeric is_cad_basis" value="" style="width:45px" placeholder="Write" /></td>
                                        <td style="display:none"><input type="text" name="txtFrontBackRise[]" id="txtFrontBackRise_1" onBlur="calculate_fabric_cons( $(this).closest('table').attr('id'),1 )" class="text_boxes_numeric is_cad_basis"  value="" style="width:45px" placeholder="Write" /></td>
                                        
                                        
                                        
                                        
                                        
                                        <td>
                                            <input type="text" name="txtSleevAllow[]" id="txtSleevAllow_1" onBlur="calculate_fabric_cons( $(this).closest('table').attr('id'),1 )" class="text_boxes_numeric is_cad_basis"  value="" style="width:45px" placeholder="Write" />
                                        </td>
                                        <td id="show_hide_11" style="">
                                            <input type="text" name="txtChest[]" id="txtChest_1"  onBlur="calculate_fabric_cons( $(this).closest('table').attr('id'),1 )"class="text_boxes_numeric is_cad_basis" value="" style="width:45px;" placeholder="Write" />
                                        </td>
                                        
                                        
                                        <td style="display:none">
                                       	 	<input type="text" name="txtThigh[]" id="txtThigh_1" onBlur="calculate_fabric_cons( $(this).closest('table').attr('id'),1 )" class="text_boxes_numeric is_cad_basis"  value="" style="width:45px" placeholder="Write" />
                                        </td>
                                        
                                        
                                        
                                        
                                        <td>
                                            <input type="text" name="txtChestAllow[]" id="txtChestAllow_1" onBlur="calculate_fabric_cons( $(this).closest('table').attr('id'),1 )" class="text_boxes_numeric is_cad_basis"  value="" style="width:45px" placeholder="Write" />
                                        </td>
                                        
                                        <td>
                                            <input type="text" name="txtGsm[]" id="txtGsm_1" class="text_boxes_numeric" onBlur="calculate_fabric_cons( $(this).closest('table').attr('id'),1 )" value="" style="width:45px" placeholder="Write" />
                                        </td>
                                        <td>
                                            <input type="text" name="txtBodyFabric[]" id="txtBodyFabric_1" class="text_boxes_numeric is_cad_basis_enable"  onBlur="calculate_fabric_cons( $(this).closest('table').attr('id'),1 )"  disabled  value="" style="width:65px" />
                                        </td>
                                        <td>
                                            <input type="text" name="txtWastage[]" id="txtWastage_1" class="text_boxes_numeric" onBlur="calculate_fabric_cons( $(this).closest('table').attr('id'),1 )" value="" style="width:45px"  placeholder="Write" />
                                        </td>
                                        <td>
                                            <input type="text" name="txtNetBodyFabric[]" id="txtNetBodyFabric_1" class="text_boxes_numeric" readonly disabled value="" style="width:65px"/>
                                        </td>
                                        <td>
                                            <input type="text" name="txtRib[]" id="txtRib_1" class="text_boxes_numeric" onBlur="calculate_fabric_cons( $(this).closest('table').attr('id'),1 )" value="" style="width:45px" placeholder="Write" />
                                        </td>
                                        <td>
                                            <input type="text" name="txtTtlTopCons[]" id="txtTtlTopCons_1" class="text_boxes_numeric" value="" disabled style="width:65px"  />
                                        </td>
                                        <td>
                                            <input type="button" name="btnadd[]" id="btnadd_1" value="+" class="formbutton" onClick="add_break_down_tr(1)" style="width:35px"/>
                                            <input type="button" name="decrease[]" id="decrease_1" value="-" class="formbutton" onClick="fn_deleteRow(1)" style="width:35px"/>       
                                            
                                            <input type="hidden" name="UpdateIdDtls[]" id="UpdateIdDtls_1" value=""   class="text_boxes" style="width:200px" />
                                        </td>
                                    </tr> 
                                    
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="13" style="text-align:right">Subtotal Top Cons </td>
                                        <td><input type="text" name="txtSubTtlTopCons" id="txtSubTtlTopCons" class="text_boxes_numeric" disabled value="" style="width:65px"  /></td>
                                    </tr>
                                </tfoot>
                            </table>
                            
                            <table id="tblFabricConsBottom" cellspacing="0" width="1080" class="rpt_table" rules="all" align="center" style="margin-bottom: 10px;">
                               <thead> 
                               		<th>Garment<br>Type</th>
                               		<th>Source</th>
                                    <th>Fabric Nature</th>
                                                          
                                    <th>TTL Side/<br>Inseam<br>Length</th>
                                    <th>Front/<br>Back Rise</th>
                                    
                                    <th>Allow</th>
                                    <th>1/2 Thigh</th>
                                    <th>Allow</th>
                                    <th>GSM</th>
                                    <th>Body Fabric<br>Cons</th>
                                    <th>Wastage<br>%</th>
                                    <th>Net Body<br>Fabric</th>
                                    <th>Rib<br>%</th>
                                    <th>TTL<br>Bottom Cons</th>
                                    <th>Action</th>
                                </thead>
                               <tbody class="general">
                                    <tr id="fabBottomTr_1">
                                        <td><?  echo create_drop_down( "cboGmtsType_1", 70, $body_part_type,"",0, "-- Select --",$selected,"","","20","","","","","","cboGmtsType[]" );  ?></td>
                                        <td><? echo create_drop_down( "cboFabricSource_1",90,$fabric_source,"",2,"-- Select --", "1","", "", "1,2","","","","","","cboFabricSource[]");	?></td>
                                        <td><? echo create_drop_down( "cboFabricNatu_1",130,$item_category,"",2,"-- Select --","2","","", "2,3","","","","","","cboFabricNatu[]"); ?></td>
                                        
                                        
                                        
                                        
                                        <td style="display:none">
                                            <input type="text" name="txtBodyLength[]" id="txtBodyLength_1" onBlur="calculate_fabric_cons( $(this).closest('table').attr('id'),1 )" class="text_boxes_numeric is_cad_basis" value="" style="width:45px"  placeholder="Write" />
                                        </td>
                                        <td style="display:none">
                                         <input type="text" name="txtSleeveLength[]" id="txtSleeveLength_1" onBlur="calculate_fabric_cons( $(this).closest('table').attr('id'),1 )" class="text_boxes_numeric is_cad_basis"  value="" style="width:45px" placeholder="Write" />
                                        </td>
                                        
                                        
                                        
                                        
                                        <td><input type="text" name="txtInseamLength[]" id="txtInseamLength_1" onBlur="calculate_fabric_cons( $(this).closest('table').attr('id'),1 )" class="text_boxes_numeric is_cad_basis" value="" style="width:45px" placeholder="Write" /></td>
                                        <td><input type="text" name="txtFrontBackRise[]" id="txtFrontBackRise_1" onBlur="calculate_fabric_cons( $(this).closest('table').attr('id'),1 )" class="text_boxes_numeric is_cad_basis"  value="" style="width:45px" placeholder="Write" /></td>
                                        
                                        
                                        
                                        
                                        
                                        <td>
                                            <input type="text" name="txtSleevAllow[]" id="txtSleevAllow_1" onBlur="calculate_fabric_cons( $(this).closest('table').attr('id'),1 )" class="text_boxes_numeric is_cad_basis"  value="" style="width:45px" placeholder="Write" />
                                        </td>
                                        
                                        <td style="display:none">
                                            <input type="text" name="txtChest[]" id="txtChest_1"  onBlur="calculate_fabric_cons( $(this).closest('table').attr('id'),1 )"class="text_boxes_numeric is_cad_basis" value="" style="width:45px;" placeholder="Write" />
                                        </td>
                                        
                                        
                                        <td>
                                       	 	<input type="text" name="txtThigh[]" id="txtThigh_1" onBlur="calculate_fabric_cons( $(this).closest('table').attr('id'),1 )" class="text_boxes_numeric is_cad_basis"  value="" style="width:45px" placeholder="Write" />
                                        </td>
                                        
                                        
                                        
                                        
                                        <td>
                                            <input type="text" name="txtChestAllow[]" id="txtChestAllow_1" onBlur="calculate_fabric_cons( $(this).closest('table').attr('id'),1 )" class="text_boxes_numeric is_cad_basis"  value="" style="width:45px" placeholder="Write" />
                                        </td>
                                        
                                        <td>
                                            <input type="text" name="txtGsm[]" id="txtGsm_1" class="text_boxes_numeric" onBlur="calculate_fabric_cons( $(this).closest('table').attr('id'),1 )" value="" style="width:45px" placeholder="Write" />
                                        </td>
                                        <td>
                                            <input type="text" name="txtBodyFabric[]" id="txtBodyFabric_1" class="text_boxes_numeric is_cad_basis_enable"  onBlur="calculate_fabric_cons( $(this).closest('table').attr('id'),1 )"  disabled  value="" style="width:65px" />
                                        </td>
                                        <td>
                                            <input type="text" name="txtWastage[]" id="txtWastage_1" class="text_boxes_numeric" onBlur="calculate_fabric_cons( $(this).closest('table').attr('id'),1 )" value="" style="width:45px"  placeholder="Write" />
                                        </td>
                                        <td>
                                            <input type="text" name="txtNetBodyFabric[]" id="txtNetBodyFabric_1" class="text_boxes_numeric" readonly disabled value="" style="width:65px"/>
                                        </td>
                                        <td>
                                            <input type="text" name="txtRib[]" id="txtRib_1" class="text_boxes_numeric" onBlur="calculate_fabric_cons( $(this).closest('table').attr('id'),1 )" value="" style="width:45px" placeholder="Write" />
                                        </td>
                                        <td>
                                            <input type="text" name="txtTtlTopCons[]" id="txtTtlTopCons_1" class="text_boxes_numeric" disabled value="" style="width:65px"  />
                                        </td>
                                        <td>
                                            <input type="button" name="btnaddBottom[]" id="btnaddBottom_1" value="+" class="formbutton" onClick="add_break_down_tr_bottom(1)" style="width:35px"/>
                                            <input type="button" name="decreaseBottom[]" id="decreaseBottom_1" value="-" class="formbutton" onClick="fn_deleteRow_bottom(1)" style="width:35px"/>       
                                            
                                            <input type="hidden" name="UpdateIdDtls[]" id="UpdateIdDtls_1" value=""   class="text_boxes" style="width:200px" />
                                        </td>
                                    </tr> 
                                    
                                </tbody>
                               <tfoot>
                                    <tr>
                                        <td colspan="13" style="text-align:right">Subtotal Bottom Cons </td>
                                        <td align="center"><input type="text" name="txtSubTtlTopCons" id="txtSubTtlTopCons" class="text_boxes_numeric" disabled value="" style="width:65px"  /></td>
                                    </tr>
                                    <tr>
                                        <td colspan="13" style="text-align:right">Grand Total Cons </td>
                                        <td align="center"><input type="text" name="txtGrandTotalCons" id="txtGrandTotalCons" class="text_boxes_numeric" disabled value="" style="width:65px"  /></td>
                                    </tr>
                            	</tfoot>
                            </table> 
                        </div>
                    </div>
                    <br>
                    <div style="float:left;width:550px;">
                        <fieldset>
                            <legend>Costing Details</legend>
                            <table cellspacing="0" width="550" class="rpt_table" id="" rules="all" align="left" style="margin-bottom: 10px;" >
                                <thead>
                                    <th width="">Costing Head</th>
                                    <th width="">UOM</th>
                                    <th width="100">Consumption</th>
                                    <th width="100">Unit Price</th>
                                    <th width="100">Total Price</th>
                                </thead>
                                <tbody class="" id="costing_dtls">
                                    <tr>
                                        <td>Yarn</td>
                                        <td>
                                        <? echo create_drop_down( "cbo_uom_yarn", 55,$unit_of_measurement,"", 0, "-- Select --",12,"", 0,"2,12,27" ); ?>
                                        </td>
                                        <td><input type="text" name="txt_yarn__1" id="txt_yarn__1" value=""  onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric"   style="width:100px"  /></td>
                                        <td><input type="text" name="txt_yarn__2" id="txt_yarn__2" class="text_boxes_numeric" onBlur="dtls_data_auto_calculate()" value="" style="width:100px"  /></td>
                                        <td><input type="text" name="txt_yarn__3" id="txt_yarn__3" class="text_boxes_numeric totalprice" disabled  value="" style="width:100px"  /></td>
                                    </tr> 
                                     <tr>
                                        <td>Knit Fabric Purchase</td>
                                        <td>
                                        <? echo create_drop_down( "cbo_uom_knit_fab_purc", 55,$unit_of_measurement,"",0, "-- Select --",12,"", 0,"2,12,27" ); ?>
                                        </td>
                                        <td><input type="text" name="txt_knit_fab_purc__1" id="txt_knit_fab_purc__1" onBlur="dtls_data_auto_calculate()" value=""  class="text_boxes_numeric"   style="width:100px"  /></td>
                                        <td><input type="text" name="txt_knit_fab_purc__2" id="txt_knit_fab_purc__2" class="text_boxes_numeric" onBlur="dtls_data_auto_calculate()" value="" style="width:100px"  /></td>
                                        <td><input type="text" name="txt_knit_fab_purc__3" id="txt_knit_fab_purc__3" class="text_boxes_numeric totalprice" disabled  value="" style="width:100px"  /></td>
                                    </tr>
                                    <tr>
                                        <td>Woven Fabric Purchase</td>
                                        <td>
                                        <? echo create_drop_down( "cbo_uom_woven_fab_purc", 55,$unit_of_measurement,"",0, "-- Select --",12,"", 0,"2,12,27" ); ?>
                                        </td>
                                        <td><input type="text" name="txt_woven_fab_purc__1" id="txt_woven_fab_purc__1" onBlur="dtls_data_auto_calculate()" value=""  class="text_boxes_numeric"   style="width:100px"  /></td>
                                        <td><input type="text" name="txt_woven_fab_purc__2" id="txt_woven_fab_purc__2" class="text_boxes_numeric" onBlur="dtls_data_auto_calculate()" value="" style="width:100px"  /></td>
                                        <td><input type="text" name="txt_woven_fab_purc__3" id="txt_woven_fab_purc__3" class="text_boxes_numeric totalprice" disabled  value="" style="width:100px"  /></td>
                                    </tr>
                                    <tr>
                                        <td>Yarn Dyeing Charge</td>
                                        <td>
                                        <? echo create_drop_down( "cbo_uom_yarn_dye_crg", 55,$unit_of_measurement,"", 0, "-- Select --",12,"", 0,"2,12,27" ); ?>
                                        </td>
                                        <td><input type="text" name="txt_yarn_dye_crg__1" id="txt_yarn_dye_crg__1" onBlur="dtls_data_auto_calculate()" value="" class="text_boxes_numeric"   style="width:100px"  /></td>
                                        <td><input type="text" name="txt_yarn_dye_crg__2" id="txt_yarn_dye_crg__2" class="text_boxes_numeric" onBlur="dtls_data_auto_calculate()" value="" style="width:100px"  /></td>
                                        <td><input type="text" name="txt_yarn_dye_crg__3" id="txt_yarn_dye_crg__3" class="text_boxes_numeric totalprice" disabled  value="" style="width:100px"  /></td>
                                    </tr>


                                    
                                    <tr>
                                        <td>Knitting Charge</td>
                                        <td>
                                        <? echo create_drop_down( "cbo_uom_knit_crg", 55,$unit_of_measurement,"", 0, "-- Select --",12,"", 0,"2,12,27" ); ?>
                                        </td>
                                        <td><input type="text" name="txt_knit_crg__1" id="txt_knit_crg__1" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value=""  style="width:100px"  /></td>
                                        <td><input type="text" name="txt_knit_crg__2" id="txt_knit_crg__2"  onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value="" style="width:100px"  /></td>
                                        <td><input type="text" name="txt_knit_crg__3" id="txt_knit_crg__3" class="text_boxes_numeric totalprice" disabled  value="" style="width:100px"  /></td>
                                    </tr> 
                                    <tr>
                                        <td>Dyeing Charge</td>
                                        <td>
                                        <? echo create_drop_down( "cbo_uom_dye_crg", 55,$unit_of_measurement,"",0, "-- Select --",12,"", 0,"2,12,27" ); ?>
                                        </td>
                                        <td><input type="text" name="txt_dye_crg__1" id="txt_dye_crg__1" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value=""  style="width:100px"  /></td>
                                        <td><input type="text" name="txt_dye_crg__2" id="txt_dye_crg__2" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value="" style="width:100px"  /></td>
                                        <td><input type="text" name="txt_dye_crg__3" id="txt_dye_crg__3" class="text_boxes_numeric totalprice" disabled  value="" style="width:100px"  /></td>
                                    </tr>
                                     
                                    <tr>
                                        <td>Spandex &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;%</td>
                                        <td>
                                        <? echo create_drop_down( "cbo_uom_spandex", 55,$unit_of_measurement,"", 0, "-- Select --",12,"", 0,"2,12,27" ); ?>
                                        </td>
                                        <td>
                                        <input type="text" name="txt_spandex__1" id="txt_spandex__1" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value=""  style="width:30px" placeholder="%" /> 
                                        <input type="text" name="txt_spandex_amt__1" id="txt_spandex_amt__1" class="text_boxes_numeric" value=""  style="width:55px"  disabled />
                                        </td>
                                        <td><input type="text" name="txt_spandex__2" id="txt_spandex__2" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value="" style="width:100px"  /></td>
                                        <td><input type="text" name="txt_spandex__3" id="txt_spandex__3" class="text_boxes_numeric totalprice" disabled  value="" style="width:100px"  /></td>
                                    </tr>
                                    
                                    <tr>
                                        <td>AOP</td>
                                        <td>
                                        <? echo create_drop_down( "cbo_uom_aop", 55,$unit_of_measurement,"", 0, "-- Select --",12,"", 0,"2,12,27" ); ?>
                                        </td>
                                        <td><input type="text" name="txt_aop__1" id="txt_aop__1" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value=""  style="width:100px"  /></td>
                                        <td><input type="text" name="txt_aop__2" id="txt_aop__2" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value="" style="width:100px"  /></td>
                                        <td><input type="text" name="txt_aop__3" id="txt_aop__3" class="text_boxes_numeric totalprice" disabled  value="" style="width:100px"  /></td>
                                    </tr> 
                                    
                                    
                                    <tr>
                                        <td>Flat Knit Collar & Cuff</td>
                                        <td>
                                        <? echo create_drop_down( "cbo_uom_collar_cuff", 55,$unit_of_measurement,"", 0, "-- Select --",2,"", 0,"2,12,27" ); ?>
                                        </td>
                                        <td><input type="text" name="txt_knit_collar_cuff__1" id="txt_knit_collar_cuff__1" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value=""  style="width:100px"  /></td>
                                        <td><input type="text" name="txt_knit_collar_cuff__2" id="txt_knit_collar_cuff__2" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value="" style="width:100px"  /></td>
                                        <td><input type="text" name="txt_knit_collar_cuff__3" id="txt_knit_collar_cuff__3"  class="text_boxes_numeric totalprice" disabled value="" style="width:100px"  /></td>
                                    </tr>
                                     
                                    <tr>
                                        <td>Print</td>
                                        <td>
                                        <? echo create_drop_down( "cbo_uom_print", 55,$unit_of_measurement,"", 0, "-- Select --",2,"", 0,"2,12,27" ); ?>
                                        </td>
                                        <td><input type="text" name="txt_print__1" id="txt_print__1" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value=""  style="width:100px"  /></td>
                                        <td><input type="text" name="txt_print__2" id="txt_print__2" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value="" style="width:100px"  /></td>
                                        <td><input type="text" name="txt_print__3" id="txt_print__3" class="text_boxes_numeric totalprice" disabled  value="" style="width:100px"  /></td>
                                    </tr>
                                     <tr>
                                        <td>Embroidery</td>
                                        <td>
                                        <? echo create_drop_down( "cbo_uom_embro", 55,$unit_of_measurement,"", 0, "-- Select --",2,"", 0,"2,12,27" ); ?>
                                        </td>
                                        <td><input type="text" name="txt_embro__1" id="txt_embro__1" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value=""  style="width:100px"  /></td>
                                        <td><input type="text" name="txt_embro__2" id="txt_embro__2" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value="" style="width:100px"  /></td>
                                        <td><input type="text" name="txt_embro__3" id="txt_embro__3" class="text_boxes_numeric totalprice" disabled  value="" style="width:100px"  /></td>
                                    </tr>
                                    <tr>
                                        <td>Wash/Gmts Dyeing</td>
                                        <td>
                                        <? echo create_drop_down( "cbo_uom_wash_gmts_dye",55,$unit_of_measurement,"", 0, "-- Select --",2,"", 0, "2,12,27" ); ?>
                                        </td>
                                        <td><input type="text" name="txt_wash_gmts_dye__1" id="txt_wash_gmts_dye__1" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value=""  style="width:100px"  /></td>
                                        <td><input type="text" name="txt_wash_gmts_dye__2" id="txt_wash_gmts_dye__2" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value="" style="width:100px"  /></td>
                                        <td><input type="text" name="txt_wash_gmts_dye__3" id="txt_wash_gmts_dye__3" class="text_boxes_numeric totalprice" disabled  value="" style="width:100px"  /></td>
                                    </tr> 
                                    <tr>
                                        <td>Accessories Price</td>
                                        <td>
                                        <? echo create_drop_down( "cbo_uom_access_price",55,$unit_of_measurement,"", 0, "-- Select --",2,"", 0, "2,12,27" ); ?>
                                        </td>
                                        <td><input type="text" name="txt_access_price__1" id="txt_access_price__1" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value=""  style="width:100px"  /></td>
                                        <td><input type="text" name="txt_access_price__2" id="txt_access_price__2" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value="" style="width:100px"  /></td>
                                        <td><input type="text" name="txt_access_price__3" id="txt_access_price__3" class="text_boxes_numeric totalprice" disabled value="" style="width:100px"  /></td>
                                    </tr> 
                                    <tr>
                                        <td>Zipper</td>
                                        <td>
                                        <? echo create_drop_down( "cbo_uom_zipper",55,$unit_of_measurement,"", 0, "-- Select --",2,"", 0, "2,12,27" ); ?>
                                        </td>
                                        <td><input type="text" name="txt_zipper__1" id="txt_zipper__1" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value=""  style="width:100px"  /></td>
                                        <td><input type="text" name="txt_zipper__2" id="txt_zipper__2" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value="" style="width:100px"  /></td>
                                        <td><input type="text" name="txt_zipper__3" id="txt_zipper__3" class="text_boxes_numeric totalprice" disabled value=""  style="width:100px"  /></td>
                                    </tr> 
                                    <tr>
                                        <td>Button</td>
                                        <td>
                                        <? echo create_drop_down( "cbo_uom_button",55,$unit_of_measurement,"", 0, "-- Select --",2,"", 0, "2,12,27" ); ?>
                                        </td>
                                        <td><input type="text" name="txt_button__1" id="txt_button__1" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value=""  style="width:100px"  /></td>
                                        <td><input type="text" name="txt_button__2" id="txt_button__2" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value="" style="width:100px"  /></td>
                                        <td><input type="text" name="txt_button__3" id="txt_button__3" class="text_boxes_numeric totalprice" disabled value=""  style="width:100px"  /></td>
                                    </tr> 
                                    <tr>
                                        <td>Test</td>
                                        <td>
                                        <? echo create_drop_down( "cbo_uom_test",55,$unit_of_measurement,"", 0, "-- Select --",2,"", 0, "2,12,27" ); ?>
                                        </td>
                                        <td><input type="text" name="txt_test__1" id="txt_test__1" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value=""  style="width:100px"  /></td>
                                        <td><input type="text" name="txt_test__2" id="txt_test__2" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value="" style="width:100px"  /></td>
                                        <td><input type="text" name="txt_test__3" id="txt_test__3" class="text_boxes_numeric totalprice"  disabled value="" style="width:100px"  /></td>
                                    </tr> 
                                    <tr>
                                        <td>CM</td>
                                        <td>
                                        <? echo create_drop_down( "cbo_uom_cm",55,$unit_of_measurement,"", 0, "-- Select --",2,"", 0, "2,12,27" ); ?>
                                        </td>
                                        <td><input type="text" name="txt_cm__1" id="txt_cm__1" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value=""  style="width:100px"  /></td>
                                        <td><input type="text" name="txt_cm__2" id="txt_cm__2" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value="" style="width:100px"  /></td>
                                        <td><input type="text" name="txt_cm__3" id="txt_cm__3" class="text_boxes_numeric totalprice" disabled value="" style="width:100px"  /></td>
                                    </tr> 
                                    <tr>
                                        <td>Inspection Cost</td>
                                        <td>
                                        <? echo create_drop_down( "cbo_uom_inspec_cost",55,$unit_of_measurement,"", 0, "-- Select --",2,"", 0, "2,12,27" ); ?>
                                        </td>
                                        <td><input type="text" name="txt_inspec_cost__1" id="txt_inspec_cost__1" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value=""  style="width:100px"  /></td>
                                        <td><input type="text" name="txt_inspec_cost__2" id="txt_inspec_cost__2" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value="" style="width:100px"  /></td>
                                        <td><input type="text" name="txt_inspec_cost__3" id="txt_inspec_cost__3" class="text_boxes_numeric totalprice" disabled value="" style="width:100px"  /></td>
                                    </tr> 
                                    <tr>
                                        <td>Freight</td>
                                        <td>
                                        <? echo create_drop_down( "cbo_uom_freight",55,$unit_of_measurement,"", 0, "-- Select --",2,"", 0, "2,12,27" ); ?>
                                        </td>
                                        <td><input type="text" name="txt_freight__1" id="txt_freight__1" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value=""  style="width:100px"  /></td>
                                        <td><input type="text" name="txt_freight__2" id="txt_freight__2" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value="" style="width:100px"  /></td>
                                        <td><input type="text" name="txt_freight__3" id="txt_freight__3" class="text_boxes_numeric totalprice" disabled value="" style="width:100px"  /></td>
                                    </tr> 
                                    <tr>
                                        <td>Currier Cost</td>
                                        <td>
                                        <? echo create_drop_down( "cbo_uom_carrier_cost",55,$unit_of_measurement,"", 0, "-- Select --",2,"", 0, "2,12,27" ); ?>
                                        </td>
                                        <td><input type="text" name="txt_carrier_cost__1" id="txt_carrier_cost__1" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value=""  style="width:100px"  /></td>
                                        <td><input type="text" name="txt_carrier_cost__2" id="txt_carrier_cost__2" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value="" style="width:100px"  /></td>
                                        <td><input type="text" name="txt_carrier_cost__3" id="txt_carrier_cost__3" class="text_boxes_numeric totalprice" disabled value="" style="width:100px"  /></td>
                                    </tr> 
                                    <tr>
                                        <td><input type="text" name="txt_others_caption" id="txt_others_caption" onBlur="" class="text_boxes" value=""  style="width:100px"  /></td>
                                        <td>
                                        <? echo create_drop_down( "cbo_uom_others",55,$unit_of_measurement,"",0, "-- Select --",2,"", 0, "2,12,27" ); ?>
                                        </td>
                                        <td><input type="text" name="txt_others__1" id="txt_others__1" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value=""  style="width:100px"  /></td>
                                        <td><input type="text" name="txt_others__2" id="txt_others__2" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value="" style="width:100px"  /></td>
                                        <td><input type="text" name="txt_others__3" id="txt_others__3" class="text_boxes_numeric totalprice" disabled value="" style="width:100px"  /></td>
                                    </tr> 
                                    <tr>
                                        <td><input type="text" name="txt_others_caption2" id="txt_others_caption2" onBlur="" class="text_boxes" value=""  style="width:100px"  /></td>
                                        <td>
                                        <? echo create_drop_down( "cbo_uom_others2",55,$unit_of_measurement,"",0, "-- Select --",2,"", 0, "2,12,27" ); ?>
                                        </td>
                                        <td><input type="text" name="txt_others2__1" id="txt_others2__1" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value=""  style="width:100px"  /></td>
                                        <td><input type="text" name="txt_others2__2" id="txt_others2__2" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value="" style="width:100px"  /></td>
                                        <td><input type="text" name="txt_others2__3" id="txt_others2__3" class="text_boxes_numeric totalprice" disabled value="" style="width:100px"  /></td>
                                    </tr>
                                    <tr>
                                        <td><input type="text" name="txt_others_caption3" id="txt_others_caption3" onBlur="" class="text_boxes" value=""  style="width:100px"  /></td>
                                        <td>
                                        <? echo create_drop_down( "cbo_uom_others3",55,$unit_of_measurement,"",0, "-- Select --",2,"", 0, "2,12,27" ); ?>
                                        </td>
                                        <td><input type="text" name="txt_others3__1" id="txt_others3__1" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value=""  style="width:100px"  /></td>
                                        <td><input type="text" name="txt_others3__2" id="txt_others3__2" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value="" style="width:100px"  /></td>
                                        <td><input type="text" name="txt_others3__3" id="txt_others3__3" class="text_boxes_numeric totalprice" disabled value="" style="width:100px"  /></td>
                                    </tr>
                                    <tr style="background-color:#ACC9EF;font-weight:bold">
                                        <td colspan="4"> Sub Total </td>
                                        <td><input type="text" name="txt_sub_total" id="txt_sub_total" class="text_boxes_numeric" readonly disabled value="" style="width:100px"  /></td>
                                    </tr> 
                                    <tr>
                                        <td>Commercial Cost&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;%</td>
                                        <td colspan="3"><input type="text" name="txt_comm_cost__1" id="txt_comm_cost__1" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value=""  style="width:100px"  /></td>
                                        <td style="display:none;"> &nbsp; <input type="text" name="txt_comm_cost__2" id="txt_comm_cost__2" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value="" style="width:100px"  /> </td>
                                        <td><input type="text" name="txt_comm_cost__3" id="txt_comm_cost__3" onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" disabled  value="" style="width:100px"  /></td>
                                    </tr> 
                                    <tr style="background-color:#ACC9EF;font-weight:bold">
                                        <td colspan="4"> Total Factory Cost/ Dz </td>
                                        <td><input type="text" name="txt_factory_cost" id="txt_factory_cost" class="text_boxes_numeric" readonly disabled value="" style="width:100px"  /></td>
                                    </tr> 
									<tr style="background-color:#ACC9EF;font-weight:bold">
                                         <td>Ready To Approved</td>
                    					<td colspan="2"><? echo create_drop_down( "cbo_ready_to_approved", 100, $yes_no,"", 1, "-- Select--", 2, "","","" ); ?></td>
										 <td  colspan="2"> &nbsp;</td>

                                    </tr>
                                </tbody>
                            </table>
                        </fieldset>
                    </div>
                    <div style="float:left;width:40px;"> &nbsp; </div>
                    <div style="float:left;width:300px;">
                        <fieldset>
                            <legend>Offer Price/Unit</legend>
                            <table cellspacing="0" width="300" class="rpt_table" id="" rules="all" align="left" style="margin-bottom: 10px;" >

                                <tbody>
                                    <tr>
                                        <td>Factory Unit Price</td>
                                        <td width="100"><input type="text" name="txt_fact_u_price" id="txt_fact_u_price" class="text_boxes_numeric" value=""  style="width:100px" readonly disabled /></td>
                                    </tr> 
                                    <tr>
                                        <td>Agent Commission &nbsp; &nbsp;&nbsp; %</td>
                                        <td><input type="text" name="txt_agnt_comm" id="txt_agnt_comm"  onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value=""  style="width:30px"  /> <input type="text" name="txt_agnt_comm_tot" id="txt_agnt_comm_tot"   class="text_boxes_numeric" value=""  style="width:55px" disabled  /></td>
                                    </tr> 
                                     <tr>
                                        <td>Local Commission &nbsp; &nbsp;&nbsp; %</td>
                                        <td><input type="text" name="txt_local_comm" id="txt_local_comm"  onBlur="dtls_data_auto_calculate()" class="text_boxes_numeric" value=""  style="width:30px"  />  <input type="text" name="txt_local_comm_tot" id="txt_local_comm_tot"   class="text_boxes_numeric" value=""  style="width:55px" disabled  /></td>
                                    </tr> 
                                    
                                    <tr>
                                        <td>Final Offer Price</td>
                                        <td><input type="text" name="txt_final_offer_price" id="txt_final_offer_price" class="text_boxes_numeric" value="" style="width:100px" readonly disabled /></td>
                                    </tr> 
                                    <tr>
                                        <td>Order Confirmed Price</td>
                                        <td><input type="text" name="txt_order_conf_price" id="txt_order_conf_price" class="text_boxes_numeric" value=""  style="width:100px" /></td>
                                    </tr> 
                                    <tr>
                                        <td>Order Confirmed Date</td>
                                        <td><input type="text" name="txt_order_conf_date" id="txt_order_conf_date" class="datepicker" value=""  style="width:100px"  /></td>
                                        <input type="hidden" name="is_approved" id="is_approved" value="">
                                    </tr> 
                                </tbody>
                            </table>
                        </fieldset>
                         <fieldset>
                            <legend>Remarks :</legend>
                            <table cellspacing="0" width="300" class="rpt_table" id="" rules="all" align="left" style="margin-bottom: 10px;" >

                                <tbody>
                                    <tr>
                                        <td colspan="11">
                                        <!--<input type="text" name="txt_remarks" id="txt_remarks" class="text_area" style="width:97%;" value=""/>-->
                                        <textarea id="txt_remarks" name="txt_remarks" class="text_area" maxlength="4000" style="width:97%; border-style:solid; border-width:1px; border-color:#6699FF; border-radius:5px; resize:none;"></textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </fieldset>
                    </div>

                    
                    <div style="width:900px;">
                    <table cellpadding="0" cellspacing="2" width="900">
                        <tr>
                            <td align="center" colspan="6" valign="middle" class="button_container"><div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div>
                            <?
                                echo load_submit_buttons( $permission, "fnc_price_quotation_entry",0,1 ,"fnc_reset_form()",1);
                            ?>
                            <input type="button" name="pdf_doc_print" id="pdf_doc_print" value="PDF Print" class="formbutton" onClick="fnc_pdf_doc_print()" style="width:80px;" />
                            </td>
                        </tr>
                    </table>
                    
                    </div>
                </form>                        
            </div>
        </fieldset>
    </div>
    <div id="data_panel2" style="margin-top:10px; display:none"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	fnc_hide_show();
	function fnc_reset_form()
	{
		reset_form('price_quotation_1','','','','','');
		set_button_status(0, permission, 'fnc_price_quotation_entry',1);
		
		$('#tblFabricConsTop tbody tr').remove();
		$('#tblFabricConsBottom tbody tr').remove();
		
		
		var htmlTop ='<tr id="fabTr_1"><td><? echo create_drop_down( "cboGmtsType_1", 70, $body_part_type,"",0, "-- Select --",$selected,"","","1","","","","","","cboGmtsType[]");?></td><td><? echo create_drop_down( "cboFabricSource_1",90,$fabric_source,"",2,"-- Select --", "1","", "", "1,2","","","","","","cboFabricSource[]");?></td><td><? echo create_drop_down( "cboFabricNatu_1",130,$item_category,"",2,"-- Select --","2","","", "2,3","","","","","","cboFabricNatu[]");?></td><td><input type="text" name="txtBodyLength[]" id="txtBodyLength_1" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),1 )" class="text_boxes_numeric is_cad_basis" value="" style="width:45px" placeholder="Write" /></td><td><input type="text" name="txtSleeveLength[]" id="txtSleeveLength_1" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),1 )" class="text_boxes_numeric is_cad_basis" value="" style="width:45px" placeholder="Write" /></td><td style="display:none"><input type="text" name="txtInseamLength[]" id="txtInseamLength_1" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),1 )" class="text_boxes_numeric is_cad_basis" value="" style="width:45px" placeholder="Write" /></td><td style="display:none"><input type="text" name="txtFrontBackRise[]" id="txtFrontBackRise_1" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),1 )" class="text_boxes_numeric is_cad_basis" value="" style="width:45px" placeholder="Write" /></td><td><input type="text" name="txtSleevAllow[]" id="txtSleevAllow_1" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),1 )" class="text_boxes_numeric is_cad_basis" value="" style="width:45px" placeholder="Write" /></td><td><input type="text" name="txtChest[]" id="txtChest_1" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),1 )"class="text_boxes_numeric is_cad_basis" value="" style="width: 45px;" placeholder="Write" /></td><td style="display:none"><input type="text" name="txtThigh[]" id="txtThigh_1" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),1 )" class="text_boxes_numeric is_cad_basis" value="" style="width:45px" placeholder="Write" /></td><td><input type="text" name="txtChestAllow[]" id="txtChestAllow_1" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),1 )" class="text_boxes_numeric is_cad_basis" value="" style="width:45px" placeholder="Write" /></td><td><input type="text" name="txtGsm[]" id="txtGsm_1" class="text_boxes_numeric" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),1 )" value="" style="width:45px" placeholder="Write" /> </td><td><input type="text" name="txtBodyFabric[]" id="txtBodyFabric_1" class="text_boxes_numeric is_cad_basis_enable" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),1 )" disabled value="" style="width:65px" /></td><td><input type="text" name="txtWastage[]" id="txtWastage_1" class="text_boxes_numeric" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),1 )" value="" style="width:45px" placeholder="Write" /></td><td><input type="text" name="txtNetBodyFabric[]" id="txtNetBodyFabric_1" class="text_boxes_numeric" readonly disabled value="" style="width:65px"/></td><td><input type="text" name="txtRib[]" id="txtRib_1" class="text_boxes_numeric" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),1 )" value="" style="width:45px" placeholder="Write" /></td><td><input type="text" name="txtTtlTopCons[]" id="txtTtlTopCons_1" class="text_boxes_numeric" value="" disabled style="width:65px" /></td><td><input type="button" name="btnadd[]" id="btnadd_1" value="+" class="formbutton" onClick="add_break_down_tr(1)" style="width:35px"/><input type="button" name="decrease[]" id="decrease_1" value="-" class="formbutton" onClick="fn_deleteRow(1)" style="width:35px"/><input type="hidden" name="UpdateIdDtls[]" id="UpdateIdDtls_1" value="" class="text_boxes" style="width:200px" /></td></tr> ';
		
		var htmlBottom = '<tr id="fabBottomTr_1"><td><? echo create_drop_down( "cboGmtsType_1", 70, $body_part_type,"",0, "-- Select --",$selected,"","","20","","","","","","cboGmtsType[]" );?></td><td><? echo create_drop_down( "cboFabricSource_1",90,$fabric_source,"",2,"-- Select --", "1","", "", "1,2","","","","","","cboFabricSource[]");?></td><td><? echo create_drop_down( "cboFabricNatu_1",130,$item_category,"",2,"-- Select --","2","","", "2,3","","","","","","cboFabricNatu[]");?></td><td style="display:none"><input type="text" name="txtBodyLength[]" id="txtBodyLength_1" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),1 )" class="text_boxes_numeric is_cad_basis" value="" style="width:45px" placeholder="Write" /></td><td style="display:none"><input type="text" name="txtSleeveLength[]" id="txtSleeveLength_1" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),1 )" class="text_boxes_numeric is_cad_basis" value="" style="width:45px" placeholder="Write" /></td><td><input type="text" name="txtInseamLength[]" id="txtInseamLength_1" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),1 )" class="text_boxes_numeric is_cad_basis" value="" style="width:45px" placeholder="Write" /></td><td><input type="text" name="txtFrontBackRise[]" id="txtFrontBackRise_1" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),1 )" class="text_boxes_numeric is_cad_basis" value="" style="width:45px" placeholder="Write" /></td><td><input type="text" name="txtSleevAllow[]" id="txtSleevAllow_1" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),1 )" class="text_boxes_numeric is_cad_basis" value="" style="width:45px" placeholder="Write" /></td><td style="display:none"> <input type="text" name="txtChest[]" id="txtChest_1" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),1 )"class="text_boxes_numeric is_cad_basis" value="" style="width: 45px;" placeholder="Write" /></td><td><input type="text" name="txtThigh[]" id="txtThigh_1" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),1 )" class="text_boxes_numeric is_cad_basis" value="" style="width:45px" placeholder="Write" /></td><td> <input type="text" name="txtChestAllow[]" id="txtChestAllow_1" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),1 )" class="text_boxes_numeric is_cad_basis" value="" style="width:45px" placeholder="Write" /></td><td><input type="text" name="txtGsm[]" id="txtGsm_1" class="text_boxes_numeric" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),1 )" value="" style="width:45px" placeholder="Write" /></td><td> <input type="text" name="txtBodyFabric[]" id="txtBodyFabric_1" class="text_boxes_numeric is_cad_basis_enable" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),1 )" disabled value="" style="width:65px" /></td><td> <input type="text" name="txtWastage[]" id="txtWastage_1" class="text_boxes_numeric" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),1 )" value="" style="width:45px" placeholder="Write" /></td><td> <input type="text" name="txtNetBodyFabric[]" id="txtNetBodyFabric_1" class="text_boxes_numeric" readonly disabled value="" style="width:65px"/></td><td> <input type="text" name="txtRib[]" id="txtRib_1" class="text_boxes_numeric" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),1 )" value="" style="width:45px" placeholder="Write" /></td><td> <input type="text" name="txtTtlTopCons[]" id="txtTtlTopCons_1" class="text_boxes_numeric" disabled value="" style="width:65px" /></td><td><input type="button" name="btnaddBottom[]" id="btnaddBottom_1" value="+" class="formbutton" onClick="add_break_down_tr_bottom(1)" style="width:35px"/> <input type="button" name="decreaseBottom[]" id="decreaseBottom_1" value="-" class="formbutton" onClick="fn_deleteRow_bottom(1)" style="width:35px"/> <input type="hidden" name="UpdateIdDtls[]" id="UpdateIdDtls_1" value="" class="text_boxes" style="width:200px" /></td></tr>';
		
		$("#tblFabricConsTop tbody").html(htmlTop);
		$("#tblFabricConsBottom tbody").html(htmlBottom);
	}
</script>
<script>
	var style_ref = [<? echo substr(return_library_autocomplete( "select style_ref  from wo_price_quotation_v3_mst  where status_active=1 and is_deleted=0  group by style_ref order by style_ref", "style_ref"  ), 0, -1); ?>];
	
	var gmts_item = [<? echo substr(return_library_autocomplete( "select gmts_item  from wo_price_quotation_v3_mst  where status_active=1 and is_deleted=0  group by gmts_item order by gmts_item", "gmts_item"  ), 0, -1); ?>];
	
	var fabrication = [<? echo substr(return_library_autocomplete( "select fabrication  from wo_price_quotation_v3_mst  where status_active=1 and is_deleted=0  group by fabrication order by fabrication", "fabrication"  ), 0, -1); ?>];
	
	var color = [<? echo substr(return_library_autocomplete( "select color from wo_price_quotation_v3_mst  where status_active=1 and is_deleted=0  group by color order by color", "color"  ), 0, -1); ?>];
	
	var yarn_count = [<? echo substr(return_library_autocomplete( "select yarn_count from wo_price_quotation_v3_mst  where status_active=1 and is_deleted=0  group by yarn_count order by yarn_count", "yarn_count"  ), 0, -1); ?>];
	
	var cons_size = [<? echo substr(return_library_autocomplete( "select cons_size from wo_price_quotation_v3_mst  where status_active=1 and is_deleted=0  group by cons_size order by cons_size", "cons_size"  ), 0, -1); ?>];
	
	
	$("#txt_style_ref").autocomplete({source:style_ref });
	$("#txt_gmts_item").autocomplete({source:gmts_item });
	$("#txt_fabrication").autocomplete({source:fabrication });
	$("#txt_color").autocomplete({source:color });
	$("#txt_yarn_count").autocomplete({source:yarn_count });
	$("#txt_consumption_size").autocomplete({source:cons_size });
</script>
</html>			