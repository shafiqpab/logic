<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Wash Material Issue					
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	31-03-2019
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
echo load_html_head_contents("Wash Material Issue Info", "../../", 1,1, $unicode,1,'');

?>

	<style>
	#btn_consignment 
	{
	  transition: font-size 3s, color 2s;
	}
	</style>
    
	<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	
	 <?
 if ($_SESSION['logic_erp']['data_arr'][297]){
	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][297] );
    echo "var field_level_data= ". $data_arr . ";\n";
 }

   
 ?>

	function openmypage_issue_id()
	{ 
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
		var page_link='requires/wash_material_issue_controller.php?action=issue_popup&data='+data;
		var title="Issue ID";
		
		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=1000px, height=400px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_subcontract_frm"); //Access the form inside the modal window
			//var theemail=this.contentDoc.getElementById("selected_job");
			var theemail=this.contentDoc.getElementById("selected_job").value;
			//alert (theemail); 
			var splt_val=theemail.split("_");
			if (splt_val[0]!="")
			{
				freeze_window(5);
				reset_form('','','txt_issue_no*cbo_company_name*cbo_location_name*cbo_party_name*txt_issue_challan*txt_issue_date*cbo_within_group*txt_job_no*update_id','','');
				get_php_form_data( splt_val[0], "load_php_data_to_form", "requires/wash_material_issue_controller" );
				let basis=document.getElementById('cbo_basis').value*1;
				//alert(basis);
				if(basis==1)
				{
					
					let requisition_no=document.getElementById('txt_requisition_no').value;
					let requisition_id=document.getElementById('txt_requisition_id').value*1;
					
					var list_view_orders = return_global_ajax_value( splt_val[0]+'**'+splt_val[1]+'**'+1+'**'+document.getElementById('cbo_basis').value+'**'+requisition_no+'**'+requisition_id, 'load_php_dtls_form', '', 'requires/wash_material_issue_controller');
				
				}
				else
				{
					var list_view_orders = return_global_ajax_value( splt_val[0]+'**'+splt_val[1]+'**'+1+'**'+document.getElementById('cbo_basis').value, 'load_php_dtls_form', '', 'requires/wash_material_issue_controller');
				
			   }
				
				
				
				if(list_view_orders!='')
				{
					$("#rec_issue_table tr").remove();
					$("#rec_issue_table").append(list_view_orders);
				}
				
				fnc_total_calculate();
				fnc_total_calculate();
				var within_group=document.getElementById('cbo_within_group').value*1;
				fnc_load_party(within_group);
				set_button_status(1, permission, 'fnc_material_issue',1);
				$('#txt_job_no').attr('disabled','disabled');
				$('#cbo_basis').attr('disabled','disabled');
				enable_disable();
				set_all_onclick();
				release_freezing();
			}
		}
	}

	function job_search_popup()
	{
		
		if ( form_validation('cbo_company_name*cbo_within_group*cbo_party_name','Company Name*within group*Party Name')==false )
		{
			return;
		}
		else
		{
			var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value+"_"+document.getElementById('cbo_basis').value;
			var page_link='requires/wash_material_issue_controller.php?action=job_popup&data='+data;
			var title='Order Popup';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=400px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				freeze_window(5);
				var theform=this.contentDoc.forms[0];
				let basis=document.getElementById('cbo_basis').value*1;
				//alert(basis);
				if(basis==1)
				{
					var theemail=this.contentDoc.getElementById("selected_order").value;
					var splitArr = theemail.split("_");
					$("#txt_job_no").val( splitArr[0] );
					$("#txt_requisition_no").val( splitArr[1] );
					$("#txt_requisition_id").val( splitArr[2] );
					var list_view_orders = return_global_ajax_value( 0+'**'+splitArr[0]+'**'+1+'**'+document.getElementById('cbo_basis').value+'**'+splitArr[1]+'**'+splitArr[2], 'load_php_dtls_form', '', 'requires/wash_material_issue_controller');
				}
				else
				{
					var theemail=this.contentDoc.getElementById("selected_order").value;
					$("#txt_job_no").val( theemail );
					var list_view_orders = return_global_ajax_value( 0+'**'+theemail+'**'+1+'**'+document.getElementById('cbo_basis').value, 'load_php_dtls_form', '', 'requires/wash_material_issue_controller');
			   }
				
				
				if(list_view_orders!='')
				{
					$("#rec_issue_table tr").remove();
					$("#rec_issue_table").append(list_view_orders);
				}
				fnc_total_calculate();
				$('#cbo_company_name').attr('disabled','disabled');
				$('#cbo_party_name').attr('disabled','disabled');
				$('#cbo_basis').attr('disabled','disabled');
				set_all_onclick();
				release_freezing();
			}
		}
	}

	function fnc_material_issue( operation )
	{
		if ( form_validation('cbo_company_name*cbo_location_name*cbo_party_name*txt_issue_date*txt_job_no*cbo_within_group', 'Company Name*Location*Party*Issue Date*Job No*within group')==false )
		{
			return;
		}
		else
		{
			var total_row=$('#rec_issue_table tr').length;

			/*var cbo_within_group = document.getElementById('cbo_within_group').value;
			if (cbo_within_group == 1)
			{
				for (var i=1; i<=total_row; i++)
				{
					if (form_validation('txtbuyerPo_'+i+'*txtstyleRef_'+i,'Buyer PO*Style Ref.')==false)
					{
						return;
					}
				}
			}*/
			
			var data_str="";

			var data_str=get_submitted_data_string('txt_issue_no*cbo_company_name*cbo_location_name*cbo_floor_name*cbo_party_name*txt_issue_challan*txt_issue_date*cbo_within_group*txt_remarks*txt_job_no*update_id*cbo_basis*cbo_issue_purpose*txt_requisition_no*txt_requisition_id',"../../");
			
			var k=0;
			//alert(tot_row);return;
			for (var i=1; i<=total_row; i++)
			{
				var qty=$('#txtissueqty_'+i).val();
				if(qty*1>0)
				{
					k++;
					data_str+="&ordernoid_" + k + "='" + $('#ordernoid_'+i).val()+"'"+"&hidrecsyschallan_" + k + "='" + $('#hidrecsyschallan_'+i).val()+"'"+"&txtbuyerPoId_" + k + "='" + $('#txtbuyerPoId_'+i).val()+"'"+"&cbouom_" + k + "='" + $('#cbouom_'+i).val()+"'"+"&txtissueqty_" + k + "='" + $('#txtissueqty_'+i).val()+"'"+"&updatedtlsid_" + k + "='" + $('#updatedtlsid_'+i).val()+"'"+"&txt_prev_issue_qty_" + k + "='" + $('#txt_prev_issue_qty_'+i).val()+"'"+"&requsitionDtlsId_" + k + "='" + $('#requsitionDtlsId_'+i).val()+"'";
				}
			}

			var data="action=save_update_delete&operation="+operation+'&total_row='+k+data_str;//+'&zero_val='+zero_val
			//alert (data); return;
			freeze_window(operation);
			http.open("POST","requires/wash_material_issue_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_material_issue_response;
		}
	}
	
	function fnc_material_issue_response()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);//return;
			var response=trim(http.responseText).split('**');
			//if (response[0].length>3) reponse[0]=10;	
			/*if(trim(response[0])=='washProduction'){
				alert("Production Found :"+trim(response[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}*/
			/*if(trim(response[0])=='washreturn')
			{
				alert("Wash Issue Return Found :"+trim(response[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}*/
		if(response[0]*1==18*1)
		{
			alert(response[1]);
			release_freezing(); return;
		}
			if(response[0]*1==20*1)
			{
				alert(response[1]);
				release_freezing(); return;
			}
			show_msg(response[0]);
			//$('#cbo_uom').val(12);
			if(response[0]==0 || response[0]==1)
			{
				document.getElementById('txt_issue_no').value= response[1];
				document.getElementById('update_id').value = response[2];
				set_button_status(1, permission, 'fnc_material_issue',1);
				let basis=document.getElementById('cbo_basis').value*1;
				//alert(basis);
				if(basis==1)
				{
						var list_view_orders = return_global_ajax_value( response[2]+'**'+response[3]+'**'+2+'**'+document.getElementById('cbo_basis').value+'**'+document.getElementById('txt_requisition_no').value+'**'+document.getElementById('txt_requisition_id').value, 'load_php_dtls_form', '', 'requires/wash_material_issue_controller');
					
				}
				else
				{
						var list_view_orders = return_global_ajax_value( response[2]+'**'+response[3]+'**'+2+'**'+document.getElementById('cbo_basis').value, 'load_php_dtls_form', '', 'requires/wash_material_issue_controller');
			   }
				if(list_view_orders!='')
				{
					$("#rec_issue_table tr").remove();
					$("#rec_issue_table").append(list_view_orders);
				}
				fnc_total_calculate();
			}
			if(response[0]==2)
			{
				reset_fnc();
			}
			$('#txt_job_no').attr('disabled','disabled');
			$('#cbo_basis').attr('disabled','disabled');
			set_all_onclick();
			release_freezing();
		}
	}
	
	function reset_fnc()
	{
		location.reload(); 
	}
	
	function check_iss_qty_ability(value,i)
	{
		var placeholder_value = $("#txtissueqty_"+i).attr('placeholder')*1;
		var pre_iss_qty = $("#txtissueqty_"+i).attr('pre_issue_qty')*1;
		var rec_qty = $("#txtissueqty_"+i).attr('rec_qty')*1;
		var basis =$("#cbo_basis").val()*1;
		//alert(placeholder_value);
		if(((value*1)+pre_iss_qty)>rec_qty)
		{
			if(basis==1)
			{
				alert("Issue qty Excceded by Requisition Qty.");
			}
			else
			{
				alert("Issue qty Excceded by Receive qty.");
				
			}
			
			
			$("#txtissueqty_"+i).val('');
			/*var confirm_value=confirm("Issue qty Excceded by Receive qty .Press cancel to proceed otherwise press ok. ");
			if(confirm_value!=0)
			{
				$("#txtissueqty_"+i).val('');
			}*/			
			return;
		}
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
		load_drop_down( 'requires/wash_material_issue_controller', document.getElementById('cbo_location_name').value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_floor', 'floor_td');
		
		
		$("#cbo_basis").val(0);
		enable_disable(); 
	}
	
	function fnc_total_calculate()
	{
		var rowCount = $('#rec_issue_table tr').length;
		//alert(rowCount)
		math_operation( "txtTotissueqty", "txtissueqty_", "+", rowCount );
	}
	function fnc_load_party(within_group)
	{
		if(within_group==1)
		{
			
			$('#buyerpo_td').css('color','blue');
			$('#buyerstyle_td').css('color','blue');
			
		}
		else if(within_group==2)
		{
			$('#buyerpo_td').css('color','black');
			$('#buyerstyle_td').css('color','black');
			
			
		}
	}
	
	
	
	
	function fnc_print()
	{
		var issueBasis =$("#cbo_basis").val();  
		if(issueBasis==1)
		{
					document.getElementById('btn_consignment').style.fontSize = '26px';
					document.getElementById('btn_consignment').style.color = 'red';
					document.getElementById('btn_consignment').style.width = '800px';
					document.getElementById('btn_consignment').value = "This Print Button Not Allow Requisition  Basis"; 
		}
		else
		{
		
		
			        document.getElementById('btn_consignment').style.fontSize = '16px';
					document.getElementById('btn_consignment').style.color = 'black';
					document.getElementById('btn_consignment').style.width = '100px';
					document.getElementById('btn_consignment').value = "Print"; 
		
			 var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_name').val()+'*'+$('#txt_issue_no').val()+'*'+report_title+'*'+$('#cbo_within_group').val(), "wash_material_issue_print", "requires/wash_material_issue_controller" )
			 return;
		}
	}


    function enable_disable()
	{
		var issueBasis =$("#cbo_basis").val();  
		if(issueBasis==1)
		{
			let change_caption =document.querySelector("#change_caption").innerHTML = "Requisition  No";
			document.querySelector("#change_caption").style.color = "blue";
			document.querySelector("#change_caption").style.display = "block"; 
			document.querySelector("#txt_requisition_no").style.display = "block"; 
			document.querySelector("#change_req").innerHTML = "Requisition  Qty";
			$("#txt_buyer_order").attr("disabled",true);
		}
		else
		{
			let change_caption =document.querySelector("#change_caption").innerHTML = "Requisition  No";
			document.querySelector("#change_caption").style.display = "none"; 
			document.querySelector("#txt_requisition_no").style.display = "none"; 
			document.querySelector("#change_req").innerHTML = "Order Qty (Pcs)";
			document.querySelector("#change_caption").style.color = "black";
			
			document.getElementById('btn_consignment').style.fontSize = '16px';
			document.getElementById('btn_consignment').style.color = 'black';
			document.getElementById('btn_consignment').style.width = '100px';
			document.getElementById('btn_consignment').value = "Print"; 
		}
		
		
	}
	
	
	 function val_roundup()
	 {
        if($('#round_down').is(':checked')){
            $( "input[name='txtissueqty[]']" ).each(function (index){
                var cur_bal = $(this).val();
                $(this).attr('title', cur_bal);
                var octal = cur_bal.toString().split(".");
                $(this).val(octal[0]);
            });
        }
		else
		{
            $( "input[name='txtissueqty[]']" ).each(function (index){
                var prev_bal = $(this).attr('title');
                if(prev_bal === undefined){
                    prev_bal = 0.0000;
                }
                $(this).attr('title', '');
                $(this).val(prev_bal);
            });
        }
    }


  function remove_del_val()
    {
    	if($('#remove_del_value').is(':checked'))
    	{
            $( "input[name='txtissueqty[]']" ).each(function (index)
            {
                var cur_bal = $(this).val();
                $(this).attr('title', cur_bal);
                var octal = cur_bal.toString().split(".");
                $(this).val('');
            });
        }
        else
        {
            $( "input[name='txtissueqty[]']" ).each(function (index)
            {
                var prev_bal = $(this).attr('title');
                if(prev_bal === undefined){
                    prev_bal = 0.0000;
                }
                $(this).attr('title', '');
                $(this).val(prev_bal);
            });
        }
    }
</script>
</head>
<body onLoad="set_hotkey();location_select()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?>
        <form name="materialissue_1" id="materialissue_1" autocomplete="off">  
            <fieldset style="width:800px;">
    		<legend>Wash Material Issue</legend>
            <table  width="800" cellspacing="2" cellpadding="0" border="0">
            	 <tr>
                    <td colspan="3" align="right"><strong>Issue ID</strong></td>
                    <td colspan="3" align="left">
                    	<input class="text_boxes"  type="text" name="txt_issue_no" id="txt_issue_no" onDblClick="openmypage_issue_id();"  placeholder="Double Click" style="width:140px;" readonly/><input type="hidden" name="update_id" id="update_id">
                    </td>
                </tr>
                <tr>
                    <td width="110" class="must_entry_caption">Company Name </td>
                    <td width="150"> 
                        <? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/wash_material_issue_controller', this.value, 'load_drop_down_location', 'location_td' ); location_select(); load_drop_down( 'requires/wash_material_issue_controller', this.value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_buyer', 'buyer_td' );setFieldLevelAccess(this.value)");//load_drop_down( 'requires/wash_material_issue_controller', this.value, 'load_drop_down_issueto', 'issue_to_td' ); force_pro_source(); ?>
                    </td>
                    <td width="110" class="must_entry_caption">Location Name</td>
                    <td id="location_td">
                         <? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", $selected, "" );?>
                    </td>
                    <td width="110" >Floor/Unit</td>
					<td width="160" id="floor_td"><? echo create_drop_down( "cbo_floor_name", 150, $blank_array,"", 1, "-- Select Floor --", $selected, "" ); ?></td>
                    
        		</tr>
                <tr>
                	<td width="110" class="must_entry_caption">Within Group</td>
					<td>
						<?php echo create_drop_down( "cbo_within_group", 150, $yes_no,"", 1, "-- Select --", 0, "load_drop_down( 'requires/wash_material_issue_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer', 'buyer_td' );fnc_load_party(this.value);" ); ?>
					</td>
                    <td class="must_entry_caption">Party</td>
                    <td id="buyer_td">
                        <? echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "-- Select Party --", $selected, "" );?>
                    </td>
                    <td>Issue Challan</td>
                    <td>
                        <input class="text_boxes" type="text" name="txt_issue_challan" id="txt_issue_challan" style="width:137px;" />  
                    </td>
                    
                </tr>
                <tr style="display:none">

	                <td>Prod Source</td>
	                <td>
	                     <?
	                       echo create_drop_down( "cbo_source", 150, $knitting_source,"", 1, "-- Select Source --", 1, "load_drop_down( 'requires/wash_material_issue_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_company_supplier', 'issue_to_td' );",0,'1,3' );
	                     ?> 
	                </td>
	                <td class="must_entry_caption">Issue To</td>
	                <td id="issue_to_td">
	                     <?
	                        echo create_drop_down( "cbo_company_supplier", 150, $blank_array,"", 1, "-- Select Company --", $selected, "",0 );	
	                     ?> 
	                </td>
                   
	            </tr>
                <tr>
                	<td class="must_entry_caption">Issue Date</td>
                    <td>
                        <input type="text" name="txt_issue_date" id="txt_issue_date" class="datepicker" value="<? echo date("d-m-Y")?>" style="width:137px"  readonly/>             
                    </td>
                 	<td>Issue Basis</td>
                    <td>
                        <? 
                            echo create_drop_down( "cbo_basis", 150, $wo_basis,"",1, "-- Select Basis --",0, "enable_disable();", "", "0,1");
                        ?>
                    </td>
                	<td class="must_entry_caption" ><strong>Job No</strong></td>
                    <td>
                       <input class="text_boxes"  type="text" name="txt_job_no" id="txt_job_no" onDblClick="job_search_popup();" placeholder="Double Click" style="width:137px;" readonly/>             
                    </td>
                    
	            </tr>
                 <tr>

				    <td>Issue Purpose</td>
				    <td>
                        <? 
                            echo create_drop_down( "cbo_issue_purpose", 150, $wash_issue_purpose_arr,"",0,"",1, "");
                        ?>
                    </td>
	                <td>Remarks</td>
	                <td>
	                    <input type="text" name="txt_remarks" id="txt_remarks"  class="text_boxes" style="width:137px;" />
	                </td>
	                <td id="change_caption"><strong>Requisition No</strong></td>
                    <td>
                       <input class="text_boxes"  type="text" name="txt_requisition_no" id="txt_requisition_no"  placeholder="Display" style="width:137px;" readonly/>   
                        <input class="text_boxes"  type="hidden" name="txt_requisition_id" id="txt_requisition_id"  placeholder="Display" style="width:137px;" readonly/>              
                    </td>
	            </tr>
            </table>
         </fieldset>
            <br/>
        <fieldset style="width:800px;">
        <legend>Metarial Details Entry</legend>
            <table cellpadding="0" cellspacing="2" border="1" width="800" id="details_tbl" rules="all">
                <thead class="form_table_header">
                    <tr align="center" >
                        <th width="100" class="must_entry_caption">Order No</th>
                        <th width="100" id="buyerpo_td">Buyer PO</th>
                        <th width="100" id="buyerstyle_td">Style Ref.</th>
                        <th width="90" class="must_entry_caption">Garments Item</th>
                        <th width="90">Color</th>
                        <th width="80">Size</th>
                        <th width="60" id="change_req">Order Qty (Pcs)</th>
                        <th width="60">Prev. Issue. Qty</th>
                        <th width="60" class="must_entry_caption">
                         <input type="checkbox" name="round_down" onClick="val_roundup();" id="round_down" style="font-size: 11px;border-radius: 5px;line-height: 15px; cursor: pointer;"  />
                            <input type="checkbox" name="remove_del_value" onClick="remove_del_val();" id="remove_del_value" style="font-size: 11px;border-radius: 5px;line-height: 15px; cursor: pointer;"  />
                            <hr style="padding: 2px 0px;">
                        
                        Issue Qty</th>
                        <th width="60">Balance Qty</th>
                        <th class="must_entry_caption">UOM</th>
                    </tr>
                </thead>
             	<tbody id="rec_issue_table">
                    <tr>
                        <td><input type="hidden" name="ordernoid_1" id="ordernoid_1">
                        <input type="hidden" name="requsitionDtlsId_1" id="requsitionDtlsId_1">
                            <input type="hidden" name="jobno_1" id="jobno_1">
                            <input type="hidden" name="hidrecsyschallan_1" id="hidrecsyschallan_1">
                            <input type="hidden" name="updatedtlsid_1" id="updatedtlsid_1">
                            <input type="hidden" name="breakdownid_1" id="breakdownid_1">
                            <input class="text_boxes" name="txtorderno_1" id="txtorderno_1" type="text" style="width:90px" readonly />
                        </td>
                        <td><input name="txtbuyerPo_1" id="txtbuyerPo_1" type="text" class="text_boxes" style="width:90px" readonly />
                            <input name="txtbuyerPoId_1" id="txtbuyerPoId_1" type="hidden" class="text_boxes" style="width:70px" />
                        </td>
                        <td><input name="txtstyleRef_1" id="txtstyleRef_1" type="text" class="text_boxes" style="width:90px" readonly /></td>
                        <td><? echo create_drop_down( "cboGmtsItem_1", 90, $garments_item,"", 1, "-- Select --",$break_item[$i], "","","" ); ?></td>
                        <td><input type="text" id="txtcolor_1" name="txtcolor_1" class="text_boxes" style="width:80px" readonly/></td>
                        <td><input type="text" id="txtsize_1" name="txtsize_1" class="text_boxes" style="width:70px" readonly/></td>
                        <td><input type="text" id="txt_order_qty_1" name="txt_order_qty_1" class="text_boxes txt_size" style="width:55px" readonly/></td>
                        <td><input type="text" id="txt_prev_issue_qty_1" name="txt_prev_issue_qty_1" class="text_boxes txt_size" style="width:55px" readonly/></td>
                        <td><input name="txtissueqty[]" id="txtissueqty_1" class="text_boxes_numeric" type="text" onKeyUp="check_receive_qty_ability(this.value,1); fnc_total_calculate();" style="width:60px" /></td>
                        
                        
                        <td><input type="text" id="txt_balance_issue_qty_1" name="txt_balance_issue_qty_1" class="text_boxes txt_size" style="width:55px" readonly/></td>
                        <td><? echo create_drop_down( "cbouom_1",50, $unit_of_measurement,"", 1, "-Select-",1,"", 1,"" );?></td>
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
                        <td>Total:</td>
                        <td><input name="txtTotissueqty" id="txtTotissueqty" class="text_boxes_numeric" type="text" readonly style="width:60px" /></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </tfoot>
             </table>
         
             <table width="800" cellspacing="2" cellpadding="0" border="0">
                 <tr>
                    <td align="center" colspan="10" valign="middle" class="button_container">
                        <? echo load_submit_buttons($permission, "fnc_material_issue", 0,0,"reset_form('materialissue_1','issue_list_view','','cbouom_1,1', '$(\'#details_tbl tbody tr:not(:first)\').remove(); disable_enable_fields(\'cbo_company_name*cbo_within_group*cbo_party_name*cboGmtsItem_1*txtcolor_1\',0)')",1); ?>
                        <input type="button" align="center" value="Print" id="btn_consignment" name="btn_consignment" class="formbutton" style="width:100px;" onClick="fnc_print()" />
                    </td>
                 </tr>  
          	</table>
            </fieldset>
           <div id="issue_list_view"></div>
        </form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript">


</script>
</html>