<?
/*--- ----------------------------------------- Comments
Purpose			: 
Functionality	:	
JS Functions	:
Created by		:	K.M Nazim Uddin
Creation date 	: 	23-12-2018
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
echo load_html_head_contents("Trims Job Card Preparation", "../../", 1,1, $unicode,1,'');

?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	function fnc_job_order_entry( operation )
	{
		var delete_master_info=0; var i=0;
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#cbo_within_group').val()+'*'+report_title, "job_card_print", "requires/Job_card_preparation_controller") 
			//return;
			show_msg("3");
		}
		else
		{
			//var process = $("#cbo_process_name").val();
			var cbo_within_group = $("#cbo_within_group").val();
			if ( form_validation('cbo_company_name*cbo_within_group*cbo_party_name*cbo_section*txt_order_no*txt_order_qty','Company*Within Group*Party*Section*Work Order*Order Qty.')==false ) 
				{
					return;
				}
			var txt_job_no 			= $('#txt_job_no').val();
			var cbo_company_name 	= $('#cbo_company_name').val();
			var cbo_location_name 	= $('#cbo_location_name').val();
			var cbo_within_group 	= $('#cbo_within_group').val();
			var cbo_party_name 		= $('#cbo_party_name').val();
			var cbo_party_location 	= $('#cbo_party_location').val();
			var txt_delivery_date 	= $('#txt_delivery_date').val();
			var cbo_section 		= $('#cbo_section').val();
			var txt_order_qty 		= $('#txt_order_qty').val();
			var txt_order_no 		= $('#txt_order_no').val();
			var hid_order_id 		= $('#hid_order_id').val();
			var txt_recv_no 		= $('#txt_recv_no').val();
			var hid_recv_id 		= $('#hid_recv_id').val();
			var update_id 			= $('#update_id').val();
			
			var j=0; var check_field=0; data_all="";
				
			$("#tbl_dtls_emb tbody tr").each(function()
			{
				var txtbuyerPoId 		= $(this).find('input[name="txtbuyerPoId[]"]').val();
				var txtbuyerPo 			= $(this).find('input[name="txtbuyerPo[]"]').val();
				var txtstyleRef 		= $(this).find('input[name="txtstyleRef[]"]').val();
				var txtdescription 		= $(this).find('input[name="txtdescription[]"]').val();
				var txtcolor 			= $(this).find('input[name="txtcolor[]"]').val();
				var txtcolorID 			= $(this).find('input[name="txtcolorID[]"]').val();
				var txtsize 			= $(this).find('input[name="txtsize[]"]').val();
				var txtsizeID 			= $(this).find('input[name="txtsizeID[]"]').val();
				var cboSubSection 		= $(this).find('select[name="cboSubSection[]"]').val();
				var cboUom 				= $(this).find('select[name="cboUom[]"]').val();
				var txtJobQuantity 		= $(this).find('input[name="txtJobQuantity[]"]').val();
				var txtRawMat 			= $(this).find('input[name="txtRawMat[]"]').val();
				var txtImpression 		= $(this).find('input[name="txtImpression[]"]').val();
				var txtRawcolor 		= $(this).find('input[name="txtRawcolor[]"]').val();
				var hdnDtlsdata 		= $(this).find('input[name="hdnDtlsdata[]"]').val();
				var txtCopyChk 			= $(this).find('input[name="txtCopyChk[]"]').val();
				var hdnbookingDtlsId 	= $(this).find('input[name="hdnbookingDtlsId[]"]').val();
				var bookConDtlsId 		= $(this).find('input[name="bookConDtlsId[]"]').val();
				var hdnDtlsUpdateId 	= $(this).find('input[name="hdnDtlsUpdateId[]"]').val();
				var hdnRecDtlsIDs 		= $(this).find('input[name="hdnRecDtlsIDs[]"]').val();
				var hdnBreakIDs 		= $(this).find('input[name="hdnBreakIDs[]"]').val();
				var txtConvFactor 		= $(this).find('input[name="txtConvFactor[]"]').val();

				if(txtdescription=='' || txtcolor=='' || txtsize=='' || txtJobQuantity=='' || txtRawMat=='' || txtImpression=='' || txtImpression==0|| txtRawcolor=='')
				{
					if(txtJobQuantity=='')
					{
						alert('Please Fill up Job Qnty.');
						check_field=1 ; return;
					}
					else if(txtdescription=='')
					{
						alert('Please Fill up Item Description');
						check_field=1 ; return;
					}
					else if(txtRawMat=='')
					{
						alert('Please Fill up Raw Materials');
						check_field=1 ; return;
					}
					//alert (i);
					//return;
				}
				j++; i++;

				data_all += "&txtbuyerPoId_" + j + "='" + txtbuyerPoId + "'&txtbuyerPo_" + j + "='" + txtbuyerPo + "'&txtstyleRef_" + j + "='" + txtstyleRef + "'&txtdescription_" + j + "='" + txtdescription + "'&txtcolor_" + j + "='" + txtcolor + "'&txtcolorID_" + j + "='" + txtcolorID + "'&txtsize_" + j + "='" + txtsizeID + "'&txtsizeID_" + j + "='" + txtsizeID + "'&cboSubSection_" + j + "='" + cboSubSection  + "'&cboUom_" + j + "='" + cboUom + "'&txtJobQuantity_" + j + "='" + txtJobQuantity + "'&txtRawMat_" + j + "='" + txtRawMat + "'&txtImpression_" + j + "='" + txtImpression + "'&txtRawcolor_" + j + "='" + txtRawcolor +"'&hdnDtlsdata_" + j + "='" + hdnDtlsdata +"'&hdnbookingDtlsId_" + j + "='" + hdnbookingDtlsId+"'&hdnDtlsUpdateId_" + j + "='" + hdnDtlsUpdateId+"'&bookConDtlsId_" + j + "='" + bookConDtlsId+"'&hdnRecDtlsIDs_" + j + "='" + hdnRecDtlsIDs+"'&hdnBreakIDs_" + j + "='" + hdnBreakIDs+"'&txtConvFactor_" + j + "='" + txtConvFactor+"'&txtCopyChk_" + j + "='" + txtCopyChk + "'";
				//alert (data_all);
			});

		}
		if(check_field==0)
		{			
			var data="action=save_update_delete&operation="+operation+'&total_row='+i+'&txt_job_no='+txt_job_no+'&cbo_company_name='+cbo_company_name+'&cbo_location_name='+cbo_location_name+'&cbo_within_group='+cbo_within_group+'&cbo_party_name='+cbo_party_name+'&cbo_party_location='+cbo_party_location+'&txt_delivery_date='+txt_delivery_date+'&cbo_section='+cbo_section+'&txt_order_no='+txt_order_no+'&hid_order_id='+hid_order_id+'&txt_order_qty='+txt_order_qty+'&txt_recv_no='+txt_recv_no+'&hid_recv_id='+hid_recv_id+'&update_id='+update_id+data_all;
			//alert (data); //return;
			freeze_window(operation);
			http.open("POST","requires/Job_card_preparation_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_job_entry_response;
		}
	}
	
	function fnc_job_entry_response()
	{
		
		if(http.readyState == 4) 
		{
			//alert (http.responseText);//return;
			var response=trim(http.responseText).split('**');
			/*if(trim(response[0])=='emblRec'){
				alert("Receive Found :"+trim(response[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			 }
			 
			 if(trim(response[0])=='emblRecipe'){
				alert("Recipe Found :"+trim(response[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			 }*/
			if(trim(response[0])=='20')
			{
				alert("Production Found ."+"\n So Update/Delete Not Possible");
				release_freezing();
				return;
			}
			if(response[0]==0 || response[0]==1)
			{
				document.getElementById('txt_job_no').value = response[1];
				document.getElementById('update_id').value = response[2];
				var within_group = $('#cbo_within_group').val();
				
				$('#txt_order_no').attr('disabled',true);
				$('#cbo_within_group').attr('disabled',true);
				
				show_list_view(2+'**'+response[2]+'**'+within_group,'order_dtls_list_view','emb_details_container','requires/Job_card_preparation_controller','setFilterGrid(\'list_view\',-1)');	
				set_button_status(1, permission, 'fnc_job_order_entry',1);

			}
			else if(response[0]==2)
			{
				location.reload();
			}
			show_msg(response[0]);
			release_freezing();
		}
	}

	function openmypage_work_order()
	{
		if ( form_validation('cbo_company_name*cbo_section','Company*Section')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value+"_"+document.getElementById('cbo_section').value;
		page_link='requires/Job_card_preparation_controller.php?action=work_order_popup&data='+data;
		title='Trims Order Receive';
		

		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=990px, height=420px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemailjob=this.contentDoc.getElementById("all_subcon_job").value;
			var theemaildtls=this.contentDoc.getElementById("all_sub_dtls_id").value;
			var theemailbreak=this.contentDoc.getElementById("all_sub_break_id").value;
			var theemailqty=this.contentDoc.getElementById("total_order_qty").value;
			$('#txt_order_qty').val(theemailqty);
			if (theemailbreak!="")
			{
				//alert(theemailbreak);
				freeze_window(5);
				get_php_form_data( theemailjob, "load_php_data_to_form", "requires/Job_card_preparation_controller" );
				var within_group = $('#cbo_within_group').val();
				show_list_view(1+'**'+theemailbreak+'**'+within_group,'order_dtls_list_view','emb_details_container','requires/Job_card_preparation_controller','setFilterGrid(\'list_view\',-1)');				
				set_button_status(0, permission, 'fnc_job_order_entry',1);
				release_freezing();
			}
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
			load_drop_down( 'requires/Job_card_preparation_controller', company+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
			
			//$('#txt_order_no').removeAttr("onDblClick").attr("onDblClick","openmypage_order();");
			
			//$('#txt_order_no').attr('readonly',true);
			//$('#txt_order_no').attr('placeholder','Browse');
			
			$("#cbo_party_location").val(0);
			$('#cbo_party_location').attr('disabled',false);
			$('#cbo_currency').attr('disabled',true);
			
			//$('#td_party_location').css('color','blue');
			//$('#buyerpo_td').css('color','blue');
			//$('#buyerstyle_td').css('color','blue');
		}
		else if(within_group==2 && type==1)
		{
			load_drop_down( 'requires/Job_card_preparation_controller', company+'_'+2, 'load_drop_down_buyer', 'buyer_td' );
			//$('#txt_order_no').removeAttr('onDblClick','onDblClick');
			
			//$('#txt_order_no').attr('readonly',false);
			//$('#txt_order_no').attr('placeholder','Write');
			
			$("#cbo_party_location").val(0); 
			$('#cbo_party_location').attr('disabled',true);
			$('#cbo_currency').attr('disabled',false);
			
			//$('#td_party_location').css('color','black');
			//$('#buyerpo_td').css('color','black');
			//$('#buyerstyle_td').css('color','black');
		}
		else if(within_group==1 && type==2)
		{
			load_drop_down( 'requires/Job_card_preparation_controller', party_name+'_'+2, 'load_drop_down_location', 'party_location_td' ); 
			$('#td_party_location').css('color','blue');
			$('#cbo_currency').attr('disabled',true);
		} 
	}

	function openmypage_row_metarial(type,booking_dtls_id,row)
	{
		var company 	= $('#cbo_company_name').val();
		var within_group = $('#cbo_within_group').val();
		var data_break=$('#hdnDtlsdata_'+row).val();
		var txtJobQuantity=$('#txtJobQuantity_'+row).val();
		var hdnDtlsUpdateId=$('#hdnDtlsUpdateId_'+row).val();
		var order_qty=$('#txt_order_qty').val();
		var desc = $('#txtdescription_'+row).val();
		var txtCopyChk = $('#txtCopyChk_'+row).val();
		var cboUom = $('#cboUom_'+row).val();
		var page_link = 'requires/Job_card_preparation_controller.php?within_group='+within_group+'&data_break='+data_break+'&order_qty='+order_qty+'&company='+company+'&txtJobQuantity='+txtJobQuantity+'&hdnDtlsUpdateId='+hdnDtlsUpdateId+'&txtCopyChk='+txtCopyChk+'&cboUom='+cboUom+'&action=row_metarial_popup';
		
		emailwindow=dhtmlmodal.open('EmailBox','iframe',page_link,'Raw Materials Details Popup', 'width=810px, height=450px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			$('#hdnDtlsdata_'+row).val('');
			var break_data=this.contentDoc.getElementById("hidden_break_tot_row"); 
			var break_delete_id=this.contentDoc.getElementById("txtDeletedId"); 
			var is_copy=this.contentDoc.getElementById("is_copy"); 
			var raw_des=(break_data.value).split('_');
			if(is_copy.value==1)
			{
				var row_num = $('#tbl_dtls_emb tbody tr').length;
				for(i=row;i<=row_num;i++)
				{
					var rowDesc = $('#txtdescription_'+i).val();
					var jobQuantity = $('#txtJobQuantity_'+i).val();
					//alert(jobQuantity+'=='+rowDesc);
					if(desc==rowDesc)
					{
						//alert(rowDesc);
						var OrgJobQuantity = $('#txtJobQuantity_'+row).val();
						var orgBreakDatas=(break_data.value).split('**');
						var data_break_down=orgSingleBreakData=orgSingleQty=ratio=newConsQty=pLoss=newPLossQty=newReqQty=''; 
						for(j=0;j<orgBreakDatas.length;j++)
						{
							orgSingleBreakData=(orgBreakDatas[j]).split('_');
							orgSingleQty=orgSingleBreakData[4];
							ratio=(orgSingleQty)/OrgJobQuantity;
							newConsQty=jobQuantity*ratio;
							pLoss=orgSingleBreakData[5];
							newPLossQty=(newConsQty*pLoss)/100;
							newReqQty=(newConsQty+newPLossQty);

							if(data_break_down=="")
							{
								//data_break_down+=orgSingleBreakData[0]+'_'+orgSingleBreakData[1]+'_'+orgSingleBreakData[2]+'_'+orgSingleBreakData[3]+'_'+newConsQty+'_'+pLoss+'_'+newPLossQty+'_'+newReqQty+'_'+orgSingleBreakData[8]+'_'+orgSingleBreakData[9]+'_'+orgSingleBreakData[10];
								data_break_down+=orgSingleBreakData[0]+'_'+orgSingleBreakData[1]+'_'+orgSingleBreakData[2]+'_'+orgSingleBreakData[3]+'_'+orgSingleBreakData[4]+'_'+orgSingleBreakData[5]+'_'+orgSingleBreakData[6]+'_'+orgSingleBreakData[7]+'_'+orgSingleBreakData[8]+'_'+orgSingleBreakData[9]+'_'+orgSingleBreakData[10];
							}
							else
							{
								data_break_down+="**"+orgSingleBreakData[0]+'_'+orgSingleBreakData[1]+'_'+orgSingleBreakData[2]+'_'+orgSingleBreakData[3]+'_'+orgSingleBreakData[4]+'_'+orgSingleBreakData[5]+'_'+orgSingleBreakData[6]+'_'+orgSingleBreakData[7]+'_'+orgSingleBreakData[8]+'_'+orgSingleBreakData[9]+'_'+orgSingleBreakData[10];
							}
							//alert(data_break_down);
						}
						$('#hdnDtlsdata_'+i).val(data_break_down);
						$('#txtRawMat_'+i).val(raw_des[0]);
						$('#txtCopyChk_'+i).val(1);
					}
					else
					{
						// No action
						//$('#hdnDtlsdata_'+row).val(break_data.value);
					}
				}
			}
			else
			{
				$('#hdnDtlsdata_'+row).val(break_data.value);
				$('#txtCopyChk_'+row).val(2);
				$('#txtRawMat_'+row).val(raw_des[0]);
			}
		}
	}

	function openmypage_job()
	{
		if ( form_validation('cbo_company_name','Company')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
		page_link='requires/Job_card_preparation_controller.php?action=job_popup&data='+data;
		title='Trims Order Receive';
		

		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=990px, height=420px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemaildata=this.contentDoc.getElementById("selected_job").value;
			var ex_data=theemaildata.split('_');
			if (ex_data[0]!="")
			{//alert(theemail.value);

				freeze_window(5);
				get_php_form_data( ex_data[0], "load_mst_php_data_to_form", "requires/Job_card_preparation_controller" );
				var within_group = $('#cbo_within_group').val();
				//show_list_view(theemail.value,'subcontract_dtls_list_view','order_list_view','requires/Job_card_preparation_controller','setFilterGrid("list_view",-1)');
				show_list_view(2+'**'+ex_data[0]+'**'+within_group,'order_dtls_list_view','emb_details_container','requires/Job_card_preparation_controller','setFilterGrid(\'list_view\',-1)');				
				set_button_status(1, permission, 'fnc_job_order_entry',1);
				release_freezing();
			}
		}
	}
	
	function open_color(type,row)
	{
		//alert(type+"=="+row);
		if( form_validation('txtImpression_'+row,'Impression')==false )
		{
			return;
		}
		var company 	= $('#cbo_company_name').val();
		var impression 	= $('#txtImpression_'+row).val();
		var hdnRawcolor = $('#hdnRawcolor_'+row).val();

		var page_link='requires/Job_card_preparation_controller.php?action=color_popup&company='+company+'&type='+type+'&impression='+impression+'&hdnRawcolor='+hdnRawcolor;
		var title="Search Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0','../../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var break_data=this.contentDoc.getElementById("hidden_break_tot_row"); 
			$('#txtRawcolor_'+row).val(break_data.value);
			$('#hdnRawcolor_'+row).val(break_data.value);
		}
	}
	
	function fnResetForm() {
    	$("#tbl_master").find('input').attr("disabled", false);
    	$("#tbl_master").find('input,select').attr("disabled", false);
    	set_button_status(0, permission, 'fnc_job_order_entry', 1);
    	reset_form('emborderentry_1', '', '', '', '', '');
    }
</script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission); ?>
        <form name="emborderentry_1" id="emborderentry_1" autocomplete="off"> 
			<fieldset style="width:950px;">
			<legend>Trims Order Receive</legend>
                <table width="900" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                    <tr>
                        <td colspan="3" align="right"><strong>Job ID</strong></td>
                        <td colspan="3">
                            <input class="text_boxes"  type="text" name="txt_job_no" id="txt_job_no" onDblClick="openmypage_job();" placeholder="Double Click" style="width:140px;" readonly /></td>
                    </tr>
                    <tr>
                        <td width="110" class="must_entry_caption">Company Name </td>
                        <td width="160"><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/Job_card_preparation_controller', this.value+'_'+1, 'load_drop_down_location', 'location_td'); fnc_load_party(1,document.getElementById('cbo_within_group').value);"); ?>
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
                       <td class="must_entry_caption">Delivery Date</td>
                       <td><input type="text" name="txt_delivery_date" id="txt_delivery_date"  style="width:140px"  class="datepicker" value="" disabled="disabled" /></td>
                    </tr> 
                    <tr>
                    	<td class="must_entry_caption">Section</td>
                        <td><? echo create_drop_down( "cbo_section", 150, $trims_section,"", 1, "-- Select Section --","",'',0,'','','','','','',"cboSection[]"); ?></td>
                    	<td class="must_entry_caption" ><strong>Work Order</strong></td>
                        <td ><input name="txt_order_no" id="txt_order_no" type="text"  class="text_boxes" style="width:140px" placeholder="Browse" onDblClick="openmypage_work_order();" readonly />
                            <input type="hidden" name="hid_order_id" id="hid_order_id">
                            <input type="hidden" name="update_id" id="update_id">
                        </td>
                    	<td class="must_entry_caption" >Receive  NO.</td>
                        <td ><input name="txt_recv_no" id="txt_recv_no" type="text"  class="text_boxes" style="width:140px"  readonly />
                        	 <input type="hidden" name="hid_recv_id" id="hid_recv_id">
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Order Qty.</td>
                        <td><input name="txt_order_qty" id="txt_order_qty" type="text"  class="text_boxes" style="width:140px" readonly /></td>
                        <td >Terms and Condition</td>
                        <td>
	                        <? 
	                        include("../../terms_condition/terms_condition.php");
	                        terms_condition(257,'update_id','../../');
	                        ?>
                        </td>
                    </tr>
                </table>
        	</fieldset> 
        	<fieldset style="width:1200px;">
           	<legend>Trims Order Receive Details Entry</legend>
                <table cellpadding="0" cellspacing="2" border="0" class="rpt_table" rules="all" id="tbl_dtls_emb">
                    <thead class="form_table_header">
                        <th width="110" id="buyerpo_td">Buyer's PO</th>
                        <th width="110" id="buyerstyle_td">Buyer's Style Ref.</th>
                        <th width="90" class="must_entry_caption">Item Description</th>
                        <th width="60">Color</th>
                        <th width="70">Size</th>
                        <th width="70">Sub Section</th>
                        <th width="70" class="must_entry_caption">UOM</th>
                        <th width="70">Conv. Factor</th>
                        <th  style="display: none;" width="80" class="must_entry_caption">Job Qnty.</th>
                        <th width="70" class="must_entry_caption">Raw Materials</th>
                        <th width="80">Impression</th>
                        <th width="80">Color</th>
                        <th></th>
                    </thead>
                    <tbody id="emb_details_container">
                        <tr id="row_1">
                            <td><input id="txtbuyerPo_1" name="txtbuyerPo[]" type="text" class="text_boxes" style="width:100px;" placeholder="Display" />
                            	<input id="txtbuyerPoId_1" name="txtbuyerPoId[]" type="hidden" class="text_boxes" style="width:70px" readonly />
                            </td>
                            <td><input id="txtstyleRef_1" name="txtstyleRef[]" type="text" class="text_boxes" style="width:100px" placeholder="Display"/></td>
                            <td><input id="txtdescription_1" name="txtdescription[]" type="text" class="text_boxes" style="width:100px" placeholder="Display"/></td>
                            <td><input id="txtcolor_1" name="txtcolor[]" type="text" class="text_boxes" style="width:100px" placeholder="Display"/>
                            	<input id="txtcolorID_1" name="txtcolorID[]" type="hidden" class="text_boxes" style="width:100px" placeholder="Display"/></td>
                            <td><input id="txtsize_1" name="txtsize[]" type="text" class="text_boxes" style="width:100px" placeholder="Display"/>
							<input id="txtsizeID_1" name="txtsizeID[]" type="hidden" class="text_boxes" style="width:100px" placeholder="Display"/>
                            </td>
                            <td id="subSectionTd_1"><? echo create_drop_down( "cboSubSection_1", 70, $trims_sub_section,"", 1, "-- Select Sub-Section --","",'',0,'','','','','','',"cboSubSection[]"); ?></td>
                            <td><? echo create_drop_down( "cboUom_1", 70, $unit_of_measurement,"", 1, "-- Select --",2,1, 1,'','','','','','',"cboUom[]"); ?>	</td>
                            <td ><input id="txtConvFactor_1" name="txtConvFactor[]" type="text" class="text_boxes_numeric" style="width:57px" placeholder="Display"/></td>
                            <td style="display: none;"><input id="txtJobQuantity_1" name="txtJobQuantity[]" class="text_boxes_numeric" type="text"  style="width:67px" placeholder="Display" /></td>
							<td><input id="txtRawMat_1" name="txtRawMat[]" type="text" class="text_boxes" onClick="openmypage_row_metarial(1,'0',1)" style="width:100px" placeholder="Click"/></td>
							<td><input id="txtImpression_1" name="txtImpression[]" type="text" class="text_boxes" style="width:100px" placeholder="Display"/></td>
							<td><input id="txtRawcolor_1" name="txtRawcolor[]" type="text" class="text_boxes" onClick="open_color(1,1)" style="width:100px" placeholder="Click"/>
								<input type="hidden" id="hdnRawcolor_1" name="hdnRawcolor[]" class="text_boxes" style="width:100px" />
                            	<input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_1">
                                <input type="hidden" name="hdnDtlsdata[]" id="hdnDtlsdata_1">
                                <input type="hidden" name="hdnbookingDtlsId[]" id="hdnbookingDtlsId_1">
                                <input type="hidden" name="txtCopyChk[]" id="txtCopyChk_1" value="0">
                            </td>
                        </tr>                     
                    </tbody>
                </table>
                <table width="1130" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td align="center" colspan="14" valign="middle" class="button_container">
                        	<? echo load_submit_buttons( $permission, "fnc_job_order_entry", 0,1,"fnResetForm();",1); ?>
                        </td>
                    </tr>   
                </table>
            </fieldset> 
        </form>                         
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>