<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Embroidery Production
Functionality	:	
JS Functions	:
Created by		:	Feroz
Creation date 	: 	23-06-2019
Updated by 		: 		
Update date		: 
Oracle Convert 	:		
Convert date	: 		   
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
echo load_html_head_contents("Embroidery Production Info", "../../", 1, 1,'','','');

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	var str_supervisor = [<? echo substr(return_library_autocomplete( "select distinct(operator_name) as supervisor from subcon_embel_production_dtls", "operator_name"  ), 0, -1); ?>];

	function openmypage_job()
	{
		if ( form_validation('cbo_company_id','Company')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_location').value+"_"+document.getElementById('cbo_buyer_name').value+"_"+document.getElementById('cbo_within_group').value;
		page_link='requires/emb_production_controller.php?action=job_popup&data='+data;
		title='Embroidery Production Info';		
        //alert(data);
		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=990px, height=420px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
		    var issue_job=this.contentDoc.getElementById("selected_job").value;//po id
			if (issue_job!="")
			{
				$("#txt_job_no").val(issue_job);

				freeze_window(5);
				get_php_form_data( issue_job, "load_php_data_to_form", "requires/emb_production_controller" );
				var within_group = $('#cbo_within_group').val(); 
				//show_list_view(theemail.value,'subcontract_dtls_list_view','order_list_view','requires/emb_order_entry_controller','setFilterGrid("list_view",-1)');
				//show_list_view(2+'_'+id+'_'+within_group+'_'+$("#update_id").val(),'order_dtls_list_view','emb_details_container','requires/emb_production_controller','setFilterGrid(\'list_view\',-1)');
				fnc_dtls_data_load(issue_job,0);
				/*var list_view_orders = return_global_ajax_value( 0+'**'+issue_job, 'load_php_dtls_form', '', 'requires/emb_production_controller');
				if(list_view_orders!='')
				{
					$("#embellishment_details_container tr").remove();
					$("#embellishment_details_container").append(list_view_orders);
				}*/

				//set_button_status(1, permission, 'fnc_embel_entry',1);
				release_freezing();
			}
		}
	}
	
	function fnc_embel_entry(operation)
	{

		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "embl_production_entry_print", "requires/emb_production_controller") 
			//return;
			show_msg("3");
		}

		else if(operation==0 || operation==1 || operation==2)
		{

			if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][315]); ?>') 
			{
				if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][315]); ?>','<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][315]); ?>')==false) {return;}
			}

			if (form_validation('txt_prod_date*txt_reporting_hour','Product Date*Reporting Hour')==false)
			{
				return;
			}
			else
			{
				//var row_num=$('#embellishment_details_container tr').length;
				var data_all="";
				var cbo_within_group=$("#cbo_within_group").val();
				
				var j=0; var dataString=''; //var all_barcodes='';
				$("#embellishment_details_container").find('tr').each(function()
				{
					var txtPoId=$(this).find('input[name="txtPoId[]"]').val();
					var txtbuyerPoId=$(this).find('input[name="txtbuyerPoId[]"]').val();
					
					var colorSizeId=$(this).find('input[name="txtColorSizeId[]"]').val();
					var updateIdDtls=$(this).find('input[name="updateIdDtls[]"]').val();
					
					var txtProdQty=$(this).find('input[name="txtProdQty[]"]').val();
					var txtRemarks=$(this).find('input[name="txtRemarks[]"]').val();
					
					if( txtProdQty*1>0)
					{
						j++;
						dataString += '&txtPoId_' + j + '=' + txtPoId +'&txtbuyerPoId_' + j + '=' + txtbuyerPoId +'&colorSizeId_' + j + '=' + colorSizeId + '&updateIdDtls_' + j + '=' + updateIdDtls + '&txtProdQty_' + j + '=' + txtProdQty+ '&txtRemarks_' + j + '=' + txtRemarks;
					}
				});
				if(j<1)
				{
					alert('Please Insert Qty At Least One Row.');
					return;
				}
				/*for (var i=1; i<=row_num; i++)
				{
					//alert(23); 
					if(cbo_within_group==1)
					{
						if (form_validation('txtbuyerPo_'+i+'*txtEmblType_'+i+'*txtProdQty_'+i+'*txtissueqty_'+i+'*cbouom_'+i,'Buyer Po*Emb. Type*Production Qty*Receive Qty*UOM')==false)
						{
							return;
						}
					}
					else
					{
						if (form_validation('txtEmblType_'+i+'*txtProdQty_'+i+'*txtissueqty_'+i+'*cbouom_'+i,'Emb.Type*Production Qty*Receive Qty*UOM')==false)
						{
							return;
						}
					}                

					data_all=data_all+get_submitted_data_string('txtPoId_'+i+'*txtbuyerPoId_'+i+'*txtstyleRef_'+i+'*txtGmtsItem_'+i+'*txtBodyPartId_'+i+'*txtEmblNameId_'+i+'*txtEmblTypeId_'+i+'*txtGmtsItemId_'+i+'*txtColorId_'+i+'*txtColorSizeId_'+i+'*txtSizeId_'+i+'*txtOrderId_'+i+'*txtissueqty_'+i+'*txtProdQty_'+i+'*cbouom_'+i+'*txtRemarks_'+i,"../../");
					//data_all=data_all+get_submitted_data_string('txtbuyerPo_'+i+'*txtstyleRef_'+i+'*txtProdQty_'+i,"../../");
					//alert(25);
					//alert(data_all); return;
				}*/
			}			          
			
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cboShift*txt_super_visor*update_id*txt_production_id*cbo_company_id*cbo_location*cbo_within_group*txt_job_no*txt_order_id*txt_order*txt_order_qty*cbo_buyer_name*txtbuyerPo*txtbuyerPoId*txtstyleRef*cbo_floor_id*cbo_machine_id*txt_prod_date*txt_reporting_hour',"../../")+dataString+'&total_row='+j;//+data_all+'&total_row='+row_num;
			//alert (data); return;
			freeze_window(operation);
			
			http.open("POST","requires/emb_production_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_embel_entry_response;
		}
	}	 
	 
	function fnc_embel_entry_response()
	{
		if(http.readyState == 4) 
		{

			var response=trim(http.responseText).split('**');	

			if(trim(response[0])=='emblQc'){
				alert("QC Found :"+trim(response[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			 }
			show_msg(response[0]);
			
			if( response[0]==0 || response[0]==1 )
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_production_id').value = response[2];
				//var recipe_id = $('#txt_recipe_id').val();
				//fnc_dtls_data_load(recipe_id,response[1]);
				set_button_status(1, permission, 'fnc_embel_entry',1,1);
			}
			if( response[0]==2 )
			{
				location.reload();
			}
			release_freezing();	
		}
	}

	function check_cur_iss_qty_ability(value,i)
	{
		var placeholder_value = $("#txtProdQty_"+i).attr('placeholder')*1;
		// var pre_iss_qty = $("#txtProdQty_"+i).attr('pre_issue_qty')*1;
		var cur_iss_qty = $("#txtProdQty_"+i).val();
		var rec_qty = $("#txtProdQty_"+i).attr('rec_qty')*1;
		// alert(value);
		if((value*1)>placeholder_value)
		{
			//alert("Qnty Excceded");
			var confirm_value=confirm("Issue qty Excceded by Order qty .Press cancel to proceed otherwise press ok. ");
			if(confirm_value!=0)
			{
				$("#txtProdQty_"+i).val('');
			}			
			return;
		}
	}
	function check_iss_qty_ability(value,i)
	{
		//var placeholder_value = $("#txtissueqty_"+i).attr('placeholder')*1;
		var pre_iss_qty = $("#txtissueqty_"+i).val()*1;
		var pro_qty = $("#txtProdQty_"+i).val()*1;
		//alert(placeholder_value);
		if(pro_qty > pre_iss_qty)
		{
			alert("Production qty Excceded than Issue Qty. Press cancel to proceed otherwise press ok.");
			$("#txtProdQty_"+i).val('');
			return;

		}
	}
	 
	// function openmypage_recipe()
	// {
	// 	var cbo_company_id = $('#cbo_company_id').val();
		
	// 	if (form_validation('cbo_company_id','Company')==false)
	// 	{
	// 		return;
	// 	}
	// 	else
	// 	{ 	
	// 		var title = 'Recipe Pop-up';	
	// 		var page_link = 'requires/emb_production_controller.php?cbo_company_id='+cbo_company_id+'&action=recipe_popup';
			  
	// 		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=450px,center=1,resize=1,scrolling=0','../');
			
	// 		emailwindow.onclose=function()
	// 		{
	// 			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
	// 			var str_data=this.contentDoc.getElementById("selected_str_data").value;	 //Access form field with id="emailfield"
				
	// 			if(update_id!="")
	// 			{
	// 				freeze_window(5);
	// 				var estr_data=str_data.split("___");
					
	// 				$('#txt_recipe_id').val(estr_data[0]);
	// 				$('#txt_recipe_no').val( estr_data[1] );
					
	// 				$('#txt_job_no').val(estr_data[2]);
	// 				$('#txt_order_id').val(estr_data[3]);
	// 				$('#txt_order').val(estr_data[4]);
					
	// 				$('#txtbuyerPoId').val(estr_data[8]);
	// 				$('#txtbuyerPo').val(estr_data[9]);
	// 				$('#txtstyleRef').val(estr_data[10]);
					
	// 				load_drop_down( 'requires/emb_production_controller', cbo_company_id+'_'+estr_data[6]+'_'+estr_data[5], 'load_drop_down_buyer', 'party_td');
	// 				$('#txt_order_qty').val(estr_data[7]);
					
	// 				fnc_dtls_data_load(estr_data[0],0);
					
	// 				release_freezing();
	// 			} 
	// 		}
	// 	}
	// }
	
	function fnc_dtls_data_load(recipe_id,uid)
	{
		//alert(recipe_id+'_'+uid); return;
		var cbo_company_id = $('#cbo_company_id').val();
		/*var list_view_orders = return_global_ajax_value( cbo_company_id+'***'+recipe_id+'***'+uid, 'load_php_dtls_form_update', '', 'requires/emb_production_controller');
		if(list_view_orders!='')
		{
			$("#embellishment_details_container").html(list_view_orders);
		}*/
		
		var list_view_orders = return_global_ajax_value( uid+'**'+recipe_id, 'load_php_dtls_form', '', 'requires/emb_production_controller');
		if(list_view_orders!='')
		{
			$("#embellishment_details_container tr").remove();
			$("#embellishment_details_container").append(list_view_orders);
		}
		fnc_total_calculate();
	}
	 
	function fnc_embel_prod_id()
	{
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 
			var company_id = $('#cbo_company_id').val();
			var within_group = $('#cbo_within_group').val();
			var title = 'Production ID Selection Form';	
			//var page_link = 'requires/emb_production_controller.php?cbo_company_id='+company_id+'&action=embel_production_popup';
			var data=company_id+"_"+within_group;
			page_link='requires/emb_production_controller.php?action=embel_production_popup&data='+data
			
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=450px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//Access the form inside the modal window
				var emblishment_data=this.contentDoc.getElementById("hidden_production_data").value;
				//alert(emblishment_id_data);return;
				var emb_data = emblishment_data.split("***");
				if(emb_data[0]!="")
				{
					freeze_window(5);
					
					$('#update_id').val(emb_data[0]);
					$('#txt_production_id').val(emb_data[1]);
					$('#cbo_location').val(emb_data[2]);
					$('#txt_recipe_id').val(emb_data[3]);
					$('#txt_recipe_no').val(emb_data[4]);
					$('#txt_job_no').val(emb_data[5]);
					$('#txt_order_id').val(emb_data[6]);
					$('#txt_order').val(emb_data[7]);
					
					$('#txtbuyerPoId').val(emb_data[14]);
					$('#txtbuyerPo').val(emb_data[15]);
					$('#txtstyleRef').val(emb_data[16]);
					//alert (company_id+'**'+emb_data[8]+'**'+emb_data[9]);
					load_drop_down( 'requires/emb_production_controller', company_id+'_'+emb_data[8]+'_'+emb_data[9], 'load_drop_down_buyer', 'party_td');
					load_drop_down( 'requires/emb_production_controller', company_id+'__'+emb_data[2], 'load_drop_down_floor', 'floor_td');
					load_drop_down( 'requires/emb_production_controller',company_id+'_'+emb_data[18], 'load_drop_down_machine', 'machine_td' );					
					$('#txt_order_qty').val(emb_data[10]);					
					$('#txt_prod_date').val(emb_data[11]);
					//$('#txt_reporting_hour').val(emb_data[12]);
					//alert(emb_data[20]);
					$('#txt_reporting_hour').val(emb_data[20]);
					$('#txt_super_visor').val(emb_data[13]);
					//$('#cboShift').val(emb_data[17]);
					$('#cboShift').val(emb_data[21]);
					 
					$('#cbo_floor_id').val(emb_data[18]);
					$('#cbo_machine_id').val(emb_data[19]);
					$('#cbo_buyer_name').val(emb_data[9]);
					
					fnc_dtls_data_load(emb_data[5],emb_data[0]);
					set_button_status(1, permission, 'fnc_embel_entry',1,1);
					release_freezing();
				}
			}
		}
	}

	function fnc_valid_time(val,field_id)
	{
		var val_length=val.length;
		if(val_length==2)
		{
			document.getElementById(field_id).value=val+":";
		}
	
		var colon_contains=val.includes(":");
		if(colon_contains==false)
		{
			if(val>23)
			{
				document.getElementById(field_id).value='23:';
			}
		}
		else
		{
			var data=val.split(":");
			var minutes=data[1];
			var str_length=minutes.length;
			var hour=data[0]*1;
	
			if(hour>23)
			{
				hour=23;
			}
	
			if(str_length>=2)
			{
				minutes= minutes.substr(0, 2);
				if(minutes*1>59)
				{
					minutes=59;
				}
			}
	
			var valid_time=hour+":"+minutes;
			document.getElementById(field_id).value=valid_time;
		}
	}
	
	function numOnly(myfield, e, field_id)
	{
		var key;
		var keychar;
		if (window.event)
			key = window.event.keyCode;
		else if (e)
			key = e.which;
		else
			return true;
		keychar = String.fromCharCode(key);
	
		// control keys
		if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;
		// numbers
		else if ((("0123456789:").indexOf(keychar) > -1))
		{
			var dotposl=document.getElementById(field_id).value.lastIndexOf(":");
			if(keychar==":" && dotposl!=-1)
			{
				return false;
			}
			return true;
		}
		else
			return false;
	}
	
	function fn_autocomplete()
	{
		 $("#txt_super_visor").autocomplete({
			 source: str_supervisor
		  });
	}
	
	function load_machine()
	{
		//var cbo_company_id = $('#cbo_company_id').val();
		var cbo_source =1; //$('#cbo_knitting_source').val();
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_floor_id = $('#cbo_floor_id').val();
		if(cbo_source==1)
		{
			load_drop_down( 'requires/emb_production_controller',cbo_company_id+'_'+cbo_floor_id, 'load_drop_down_machine', 'machine_td' );
		}
		else
		{
			load_drop_down( 'requires/emb_production_controller',0+'_'+0, 'load_drop_down_machine', 'machine_td' );
		}
	}
	
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
	}
	
	function fnc_total_calculate()
	{
		var rowCount = $('#embellishment_details_container tr').length;
		//alert(rowCount)
		math_operation( "txtTotProdQty", "txtProdQty_", "+", rowCount );
	} 
 </script>
</head>

<body onLoad="set_hotkey();">
	<div align="center" style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission); ?>
    <form name="embellishmentEntry_1" id="embellishmentEntry_1">
        <fieldset style="width:800px; margin-bottom:10px;"">
        <legend>Embroidery Production</legend> 
            <table width="100%" cellpadding="1" cellspacing="1" border="0" > 
                <tr>
                    <td colspan="3" align="right"><strong>Production ID</strong></td>
                    <td colspan="3">
                        <input type="text" name="txt_production_id" id="txt_production_id" class="text_boxes" style="width:140px;" placeholder="Browse" onDblClick="fnc_embel_prod_id();" />
                        <input type="hidden" name="update_id" id="update_id"/>
                    </td>
                </tr>
                <tr>
                    <td width="100" class="must_entry_caption">Company Name</td>
                    <td width="160"><? echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Select Company-", $selected, "load_drop_down('requires/emb_production_controller', this.value, 'load_drop_down_location', 'location_td'); location_select(); load_drop_down( 'requires/emb_production_controller', this.value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_buyer', 'party_td' );"); ?></td>                 
                    <td width="100">Location</td>
                    <td width="160" id="location_td"><? echo create_drop_down("cbo_location", 150, $blank_array,"", 1,"-Select Location-", 0,""); ?></td>
                    <td width="110" >Within Group</td>
					<td>
						<?php echo create_drop_down( "cbo_within_group", 150, $yes_no,"", 0, "--  --", 0, "load_drop_down( 'requires/emb_production_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_buyer', 'party_td' );" ); ?>
					</td>
                </tr>
                <tr>
                	<td class="must_entry_caption">Party Name</td>
                    <td id="party_td"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "--Select Party--", $selected, ""); ?></td>
                    <td class="must_entry_caption">Job No.</td>
                    <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:140px;" placeholder="Browse" onDblClick="openmypage_job();" readonly/>
                    	<input type="hidden" name="txt_job_id" id="txt_job_id" class="text_boxes" value="0" style="width:40px;" />
                    </td>
                    <td>Floor</td>
                    <td id="floor_td"><? echo create_drop_down( "cbo_floor_id", 150, $blank_array,"", 1, "--Select Floor--", $selected, "",1 ); ?></td>
                    
                    
                </tr>
                 <tr>
                	 <td style="display:none">Order No.</td>
                    <td style="display:none">
                        <input type="text" name="txt_order" id="txt_order" class="text_boxes" value="" style="width:140px;" disabled placeholder="Display" />
                        <input type="hidden" name="txt_order_id" id="txt_order_id" class="text_boxes" value="" style="width:60px;" />
                    </td>
                    <td>Machine</td>
                    <td id="machine_td"><? echo create_drop_down( "cbo_machine_id", 150, $blank_array,"", 1, "--Select Machine--", $selected, "",1 ); ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr style="display:none">
                    <td>Order Qty</td>
                    <td><input type="text" name="txt_order_qty" id="txt_order_qty" class="text_boxes_numeric" style="width:140px;" disabled placeholder="Display"/></td>
                    <td>Buyer Po</td>
                    <td><input name="txtbuyerPo" id="txtbuyerPo" type="text" class="text_boxes" style="width:140px" readonly />
                        <input name="txtbuyerPoId" id="txtbuyerPoId" type="hidden" class="text_boxes" style="width:70px" />
                    </td>
                    <td>Buyer Style</td>
                    <td><input name="txtstyleRef" id="txtstyleRef" type="text" class="text_boxes" style="width:140px" readonly /></td>
                </tr>
                
               
            </table>
        </fieldset>                 
        <fieldset style="width:1180px;" >
        <legend>Emb Production Details Info</legend>
            <table cellpadding="0" cellspacing="0" width="1180" class="rpt_table" border="1" rules="all" id="tbl_item_details">
                <thead>
                	<tr>
                        <th>&nbsp;</th>
                        <th class="must_entry_caption">Production Date</th>
                        <th><input type="text" name="txt_prod_date" id="txt_prod_date" class="datepicker" style="width:80px;" placeholder="Write" value="<? echo date('d-m-Y'); ?>"  readonly/></th>
                        <th class="must_entry_caption">Reporting Hour</th>
                        <th><input name="txt_reporting_hour" id="txt_reporting_hour" class="text_boxes" style="width:100px" placeholder="24 Hour Format" onBlur="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyUp="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyPress="return numOnly(this,event,this.id);" /></th>
                        <th>Operator/Superviser</th>
                        <th><input type="text" name="txt_super_visor" id="txt_super_visor" class="text_boxes" onKeyUp="fn_autocomplete();" style="width:80px"></th>
                        <th>Shift</th>
                        <th><? echo create_drop_down( "cboShift", 80, $shift_name,"", 1, '- Select -', 0,"",'','','','','','','',''); ?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                	<tr>
                        <th width="40">SL</th>
                        <th width="150">Order No.</th>
                        <th width="100" class="must_entry_caption">Buyer Po</th>
                        <th width="100">Style Ref.</th>
                        <th width="90">Gmts Item</th>
                        <th width="110">Body Part</th>
                        <th width="110">Emb. Name</th>
                        <th width="80" class="must_entry_caption">Emb. Type</th>
                        <!-- <th width="110">Process/ Type</th> -->
                        <th width="80">Color</th>
                        <th width="70">Size</th>
                        <th width="60">Order Qty (Pcs)</th>
                        <th width="60" class="must_entry_caption">Issue Qty</th>
                        <th width="60" class="must_entry_caption">Production Qty (Pcs)</th>
                        <th width="50" class="must_entry_caption">UOM</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                
                <tbody id="embellishment_details_container">
                    <tr class="general" name="tr[]" id="tr_1">
                        <td><input type="text" name="txtSl[]" id="txtSl_1" class="text_boxes_numeric" style="width:30px" value="1" disabled /></td>
                        <td><input name="txtorderNo_1" id="txtorderNo_1" type="text" class="text_boxes" style="width:100px" readonly />
                            <input name="txtPoId_" id="txtPoId_" type="hidden" class="text_boxes" style="width:100px" />
                        </td>
                        <td><input name="txtbuyerPo_1" id="txtbuyerPo_1" type="text" class="text_boxes" style="width:90px" readonly />
                            <input name="txtbuyerPoId_1" id="txtbuyerPoId_1" type="hidden" class="text_boxes" style="width:70px" />
                        </td>
                        <td><input name="txtstyleRef_1" id="txtstyleRef_1" type="text" class="text_boxes" style="width:90px" readonly /></td>
                        <td>
                        	<input type="text" name="txtGmtsItem[]" id="txtGmtsItem_1" class="text_boxes" style="width:80px" placeholder="Display"disabled />
                        	<input type="hidden" name="txtGmtsItemId[]" id="txtGmtsItemId_1" value="" style="width:50px" />
                        </td>
                        <td>	
                            <input type="text" name="txtBodyPart[]" id="txtBodyPart_1" class="text_boxes" style="width:100px" placeholder="Display" disabled  />
                            <input type="hidden" name="txtBodyPartId[]" id="txtBodyPartId_1" style="width:50px" class="text_boxes" />
                        </td>
                        <td>
                            <input type="text" name="txtEmblName[]" id="txtEmblName_1" value="" class="text_boxes" style="width:100px" placeholder="Display" disabled />
                            <input type="hidden" name="txtEmblNameId[]" id="txtEmblNameId_1" value="" />
                        </td>
                        <td>
							<input type="text" name="txtEmblType[]" id="txtEmblType_1" value="" class="text_boxes" style="width:100px" placeholder="Display" disabled />
                            <input type="hidden" name="txtEmblTypeId[]" id="txtEmblTypeId_1" value="" />                        
                        </td>
                        
                        <!-- <td><? //echo create_drop_down( "cboGmtsItem_1", 90, $garments_item,"", 1, "-- Select --",$break_item[$i], "","","" ); ?></td> -->
                        <td>
                            <input type="text" name="txtColor[]" id="txtColor_1" value="" class="text_boxes"  style="width:70px" placeholder="Display" disabled/>
                            <input type="hidden" name="txtColorId[]" id="txtColorId_1" value="" />
                            <input type="hidden" name="textColorSizeId[]" id="txtColorSizeId_1" value="" />
                        </td>
                        <td>
                        	<input type="text" name="txtSize[]" id="txtSize_1" value="" class="text_boxes"  style="width:60px" placeholder="Display" disabled/>
                            <input type="hidden" name="txtSizeId[]" id="txtSizeId_1" value="" />
                        </td>
                        <td><input name="txtOrderId_1" id="txtOrderId_1" class="text_boxes_numeric" type="text" style="width:50px" disabled/></td>
                        <td>
                        	<input name="txtissueqty_1" id="txtissueqty_1" class="text_boxes_numeric" type="text" onKeyUp="check_iss_qty_ability(this.value,1); fnc_total_calculate();" style="width:50px" readonly />
                        </td>
                        <td>
                        	<input type="text" name="txtProdQty[]" id="txtProdQty_1" class="text_boxes_numeric" style="width:50px" placeholder="Write" onBlur="fnc_total_calculate();" />
                        </td>
                        <td><? echo create_drop_down( "cbouom_1",50, $unit_of_measurement,"", 1, "-Select-",1,"", 1,"" );?></td>
                        <td><input type="text" name="txtRemarks[]" id="txtRemarks_1" class="text_boxes" style="width:60px" placeholder="Write" /></td>
                    </tr>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom" name="tr_btm" id="tr_btm">
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <!-- <td>&nbsp;</td> -->
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>Total:</td>
                        <td><input type="text" name="txtTotProdQty" id="txtTotProdQty" class="text_boxes_numeric" style="width:50px" placeholder="Display" readonly /></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
            </fieldset>
            <table cellpadding="0" cellspacing="1" width="800">
                <tr>
                     <td align="center" colspan="6" valign="middle" class="button_container">
                        <? echo load_submit_buttons($permission, "fnc_embel_entry", 0,1,"refresh_data();",1); ?> 
                    </td>	  
                </tr>
            </table>
    	</form>
    </div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>