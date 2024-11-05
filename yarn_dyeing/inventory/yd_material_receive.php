<?php

/*--- ----------------------------------------- Comments 
Purpose         :   This form is for Yarn Dyeing Material Receive                 
Functionality   :   
JS Functions    :
Created by      :   Sapayth Hossain
Creation date   :   05-03-2020
Updated by      :   
Update date     :
Oracle Convert  :       
Convert date    :   
QC Performed BY :       
QC Date         :   
Comments        :
Description     :   This page has 2 parts. The top part is used to browse data using job no textfield. When user double click on job no textfield a popup will open and help a user find his desired YD job.
*/

session_start();
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$user_level=$_SESSION['logic_erp']["user_level"];


//--------------------------------------------------------------------------------------------------------------------
//echo load_html_head_contents('Yarn Dyeing Material Receive', '../../', 1, 1, $unicode, 1, '');
echo load_html_head_contents("Yarn Dyeing Material Receive","../../", 1, 1, $unicode,1,1);
?>
<script type="text/javascript">
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = '../../logout.php';
    var permission='<?php echo $permission; ?>';

    <?
		$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][556] );
		echo "var field_level_data= ". $data_arr . ";\n";
	?>

    function get_company_config(company_id) 
	{
        var withinGroup = document.getElementById('cbo_within_group').value;
 		var work_order_material_auto_receive = $("#work_order_material_auto_receive").val()*1;
 		//alert(work_order_material_auto_receive);
         load_drop_down('requires/yd_material_receive_controller', company_id, 'load_drop_down_location', 'location_td');
        load_drop_down('requires/yd_material_receive_controller', company_id+'_'+withinGroup, 'load_drop_down_buyer', 'buyer_td' );
		
		   if(work_order_material_auto_receive==1)
			{
				$('#txt_job_no').attr('disabled','disabled');
				$('#txt_issue_no').attr('disabled',false); 
				$('#printing_work_oder').css('color','blue');
 			}
			else
			{  
 				$('#txt_issue_no').attr('disabled','true');
				$('#txt_job_no').attr('disabled',false); 
				$('#printing_work_oder').css('color','black');
			}

        // load_drop_down('requires/yd_material_receive_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer', 'buyer_td');
    }


     function get_td_coller(withinGroup) 
	{
 		   if(withinGroup==1)
			{
			 
				//$('#printing_work_oder').css('color','blue');
				$('#job_no_td').css('color','blue');
				
				$('#style_no_td').css('color','blue');
				$('#type_no_td').css('color','blue');
				$('#cone_no_td').css('color','blue');
				$('#bag_no_td').css('color','blue');
				
				 
 			}
			else
			{  
 				//$('#printing_work_oder').css('color','black');
				
				$('#job_no_td').html('Job No/Sales order no'); 
				
				$('#style_no_td').html('Style');
				$('#type_no_td').html('Yarn Type');
				$('#cone_no_td').html('Cone Per Bag');
				$('#bag_no_td').html('No of Cone');
				
				
			}
         // load_drop_down('requires/yd_material_receive_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer', 'buyer_td');
    }
	
	
function job_search_popupsds()
{
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	
	 
	var company = $("#cbo_company_name").val();
	var page_link='requires/yd_material_receive_controller.php?action=job_search_popup&company='+company;
	var title="Search MRR Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=400px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var mrr_id=this.contentDoc.getElementById("hidden_recv_number").value; // mrr number
 		 
		/*$('#tbl_child').find('input,select').val("");
		get_php_form_data(mrr_id, "populate_data_from_data", "requires/raw_material_item_receive_controller");
		$("#tbl_master").find('input,select').attr("disabled", true);
		disable_enable_fields( 'txt_mrr_no*txt_remarks*txt_sup_ref', 0, "", "" );
		var posted_in_account=$("#hidden_posted_in_account").val()*1;

		if(posted_in_account==1) 	$("#accounting_posting_td").text("Already Posted In Accounts.");
		else 						$("#accounting_posting_td").text("");

		var txt_wo_pi_req_id = $("#txt_wo_pi_req_id").val();
		var txt_wo_pi_req = $("#txt_wo_pi_req").val();
		//fn_onCheckBasis($("#cbo_receive_basis").val());

		$("#txt_wo_pi_req_id").val(txt_wo_pi_req_id);
		$("#txt_wo_pi_req").val(txt_wo_pi_req);

		$("#cbo_supplier").attr("disabled",true);
		$("#txt_wo_pi_req").attr("disabled",true);
		var basis=$("#cbo_receive_basis").val();
		if(basis==1 || basis==2 || basis==7)
		{
			show_list_view($("#cbo_receive_basis").val()+"**"+$("#txt_wo_pi_req_id").val(),'show_product_listview','list_product_container','requires/raw_material_item_receive_controller','setFilterGrid(\'list_view\',-1)');
		}
		set_button_status(0, permission, 'fnc_general_item_receive_entry',1,1);*/
 	}
}
    function job_search_popup() 
	{
       if( form_validation('cbo_company_name*cbo_party_name','Company Name*Party Name')==false )
		{
			return;
		}
        var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_party_name').value;
        page_link='requires/yd_material_receive_controller.php?action=job_search_popup&data='+data;
        title='Search Yarn Dyeing Job';
        jobPopup=dhtmlmodal.open('JobBox', 'iframe', page_link, title, 'width=1360px, height=400px, center=1, resize=0, scrolling=0', '../');
        jobPopup.onclose=function() 
		{
            var theform=this.contentDoc.forms[0];
            var job_id_mst=this.contentDoc.getElementById('selected_order_id').value;

            get_php_form_data('1_'+job_id_mst, 'populate_mst_data_from_search_popup', 'requires/yd_material_receive_controller');
            show_list_view('1_'+job_id_mst, 'populate_dtls_data_from_search_popup', 'material-details','requires/yd_material_receive_controller', '');

            document.getElementById('cbo_company_name').setAttribute('disabled', 'disabled');
            document.getElementById('cbo_location_name').setAttribute('disabled', 'disabled');
            document.getElementById('cbo_within_group').setAttribute('disabled', 'disabled');
            document.getElementById('cbo_party_name').setAttribute('disabled', 'disabled');

            set_button_status(0, permission, 'fnc_material_receive', 1);
        }
    } 
	  
	
	function issue_search_popup() 
	{
        if( !form_validation('cbo_company_name', 'Company Name') ) 
		{
            return; // returning to the form if required fields is not done
        }
        var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_party_name').value;
        page_link='requires/yd_material_receive_controller.php?action=issue_search_popup&data='+data;
        title='Search Yarn Dyeing Job';
        jobPopup=dhtmlmodal.open('JobBox', 'iframe', page_link, title, 'width=560px, height=400px, center=1, resize=0, scrolling=0', '../');
        jobPopup.onclose=function() 
		{
            var theform=this.contentDoc.forms[0];
            var theemaildata=this.contentDoc.getElementById('selected_order_id').value;
			var ex_data=theemaildata.split('_');
			var job_id_mst=ex_data[0];
			var issue_id=ex_data[1];
			var issue_number=ex_data[2];
			//alert(issue_number);
			
			$('#txt_issue_no').val(issue_number);
			$('#hid_challan_id').val(issue_id);
			var cbo_company_name=document.getElementById('cbo_company_name').value;
			
 			get_php_form_data('1_'+job_id_mst, 'populate_mst_data_from_search_popup', 'requires/yd_material_receive_controller');
           // get_php_form_data('1_'+job_id_mst, 'populate_issue_data_from_search_popup', 'requires/yd_material_receive_controller');
            show_list_view('1_'+job_id_mst+'_'+cbo_company_name+'_'+issue_id, 'populate_dtls_data_from_search_popup', 'material-details','requires/yd_material_receive_controller', '');

            document.getElementById('cbo_company_name').setAttribute('disabled', 'disabled');
            document.getElementById('cbo_location_name').setAttribute('disabled', 'disabled');
            document.getElementById('cbo_within_group').setAttribute('disabled', 'disabled');
            document.getElementById('cbo_party_name').setAttribute('disabled', 'disabled');

            set_button_status(0, permission, 'fnc_material_receive', 1);
        }
    }

    function job_search_popup_rcv() 
	{
        if( !form_validation('cbo_company_name', 'Company Name') ) {
            return; // returning to the form if required fields is not done
        }
        var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('cbo_within_group').value;

        page_link='requires/yd_material_receive_controller.php?action=job_search_popup_rcv&data='+data;
        title='Search Yarn Dyeing Job';
        jobPopup=dhtmlmodal.open('JobBox', 'iframe', page_link, title, 'width=1130px, height=400px, center=1, resize=0, scrolling=0', '../../');
        jobPopup.onclose=function() 
		{
            var theform=this.contentDoc.forms[0];
            var mat_mst_id=this.contentDoc.getElementById('hdn_mat_mst_id').value;
            var comp=this.contentDoc.getElementById('hdn_company').value;
            var location=this.contentDoc.getElementById('hdn_location').value;
            var party=this.contentDoc.getElementById('hdn_party').value;
            var cbo_company_name=document.getElementById('cbo_company_name').value;
            reset_form('', '', 'txt_receive_no*cbo_within_group*txt_receive_challan*txt_receive_date*txt_issue_no*txt_job_no', '', '');
            // reset_form( forms, divs, fields, default_val, extra_func, non_refresh_ids )
            // document.getElementById('cbo_location_name');

            get_php_form_data('3_'+mat_mst_id, 'populate_mst_data_from_search_popup', 'requires/yd_material_receive_controller');
            show_list_view('3_'+mat_mst_id+'_'+cbo_company_name, 'populate_dtls_data_from_search_popup', 'material-details', 'requires/yd_material_receive_controller', '');
			
			$('#txt_issue_no').attr('disabled','true');
			$('#txt_job_no').attr('disabled','true');
            // show_list_view(batch_against+'**'+batch_for+'**'+reponse[1]+'**'+roll_maintained+'**'+batch_maintained,'batch_details','batch_details_container','requires/batch_creation_controller','');

            set_button_status(1, permission, 'fnc_material_receive', 1);
        }
    }

    function fnc_total_calculate() 
	{
        var rowCount = document.getElementById('material-details').children.length - 1;
		var ddd={ dec_type:1, comma:0, currency:0}
        math_operation('txtTotRcvQty', 'txtRcvQty_', '+', rowCount,ddd);
		
		var revQtytotal=$("#txtTotRcvQty").val()*1;
	$("#txtTotRcvQty").val( number_format (revQtytotal, 2,'.' , ""));
    }

    function fnc_material_receive(operation) 
	{
		
		 var within_group=document.getElementById('cbo_within_group').value*1;
		 
		 if(within_group==1)
		 {
			if ( form_validation('cbo_company_name*cbo_within_group*cbo_party_name*txt_receive_date*txt_job_no', 'Company Name*Within Group*Party*Receive Date*Job No')==false ) {
			return;
			}
		}
		else
		{
			
			if ( form_validation('cbo_company_name*cbo_within_group*cbo_party_name*txt_receive_challan*txt_receive_date*txt_job_no', 'Company Name*Within Group*Party*Challan*Receive Date*Job No')==false ) {
			return;
			}
		}
		
       
		
		
        
        var data_str=get_submitted_data_string
		('cbo_company_name*cbo_location_name*cbo_within_group*cbo_party_name*txt_receive_challan*txt_receive_date*txt_issue_no*txt_job_no*txtTotRcvQty*hdn_job_no_id*hdn_update_id*txt_receive_no*hdn_booking_type_id*hdn_booking_without_order*txt_issue_no*hid_challan_id*proportionately_qnty', '../../../');

        var total_row = document.getElementById('material-details').children.length - 1;
        var proportionately_qnty = (document.getElementById('proportionately_qnty').value)*1;
        var total_qty = (document.getElementById('txtTotRcvQty').value)*1;
		if(proportionately_qnty!=0 && proportionately_qnty<total_qty)
		{
			alert("Total Qty must not larger than Proportionately Qty");return;
		}
		
		var sl=0;
        for (var i=1; i<=total_row; i++) 
		{
            var qty=$('#txtRcvQty_'+i).val();
           /* if(!qty) 
			{
                alert('Please fill Receive Quantity');
                return;
            }*/
			
			if(qty>0)
			{
				sl++;
			}


			//if(qty>0) 
			//{
                data_str+=get_submitted_data_string('hdnItemColor_'+i+'*cboUom_'+i+'*txtRcvQty_'+i+'*hdnJobDtlsId_'+i+'*txtNoOfBag_'+i+'*txtConePerBag_'+i+'*txtNoOfCone_'+i+'*hdnYarnType_'+i+'*hdnDtlsId_'+i+'*hdnDtlsId_'+i+'*hdnSalesOrdId_'+i+'*hdnSalesOrdNo_'+i+'*hdnProductId_'+i+'*txtLot_'+i, '../../../');
				
            //}
           
        }
		// alert(data_str);
		if(sl==0)
		{
			alert('Please fill Receive Quantity');
			return;
		}
		

        var data='action=save_update_delete&operation='+operation+'&total_row='+total_row+data_str;
        // console.log(data); return;
        freeze_window(operation);
        http.open('POST', 'requires/yd_material_receive_controller.php', true);
        http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        http.send(data);
        http.onreadystatechange = fnc_material_receive_response;
    }
	
	
	

    function fnc_material_receive_response() 
	{
        if(http.readyState == 4) {
            //alert(http.responseText);//return;
            var response=trim(http.responseText).split('**');
            show_msg(response[0]);
            //$('#cbo_uom').val(12);
            if(response[0]==0 || response[0]==1)
			 {
                // console.log(response[0]);
                document.getElementById('txt_receive_no').value= response[1];
                document.getElementById('hdn_update_id').value = response[2];

                show_list_view('2_'+response[2], 'populate_dtls_data_from_search_popup', 'material-details', 'requires/yd_material_receive_controller', '');

                set_button_status(1, permission, 'fnc_material_receive', 1);
            }
            if(response[0]==2)
            {
              
			 // alert('xcxc');
			  
			    // location.reload(); 
				 fnResetForm();
				 
            }
			$('#txt_issue_no').attr('disabled','true');
			$('#txt_job_no').attr('disabled','true');
        }
        release_freezing();
    }
	
	   function calculate_poportion2222(value) 
		{
			 
			//var total_row = document.getElementById('material-details').children.length - 1;
			var rowCount = $('#material-details tr').length - 1;
			
			//alert(rowCount); return;
			var len  = totalProp = 0;
	
			for (var i = 1; i <= rowCount; i++) 
			{
				len = len + 1;
 				var txt_order_qnty = ($('#txtorderQty_' + i).val()) * 1;
				//totalProp += (txt_order_qnty * 1);
				//alert(txt_order_qnty); alert(value);
				
 				var proportionate_qnty = number_format_common(((txt_order_qnty/value)), 2, 0, 1);
				//totalProp += (proportionate_qnty * 1);
				$('#txtRcvQty_' + i).val(number_format_common(proportionate_qnty, 2, 0, 1));
				//alert(totalProp);

			} 
		}
		
		function calculate_poportion(value) 
		{
			var balanceQty=(document.getElementById('txtTotalBalanceQuantity').value) * 1;
			var proportionQty=(document.getElementById('proportionately_qnty').value) * 1;
			if(balanceQty!=0 && balanceQty<proportionQty)
			{
				alert('Proportionately must not larger than Balance Qty');
				document.getElementById('proportionately_qnty').value=balanceQty;	
				// return;
			}
			/*var pre_qnty_breck_down='<? //echo $pre_qnty_breck_down;?>';
			var pre_qnty_breck_down_arr=pre_qnty_breck_down.split(',');
			var po_data = [];
			for (var k = 0; k < pre_qnty_breck_down_arr.length; k++) 
			{
				var po_data_arr=pre_qnty_breck_down_arr[k].split('_');
				po_data[po_data_arr[1]] = po_data_arr[0];
			}

			if(is_fabric_level == 1)
			{
				var tot_po_qnty = (document.getElementById('tot_fab_booking_qnty').value) * 1;
			}
			else
			{
				var tot_po_qnty = (document.getElementById('tot_po_qnty').value) * 1;
			}*/
			var tot_po_qnty = (document.getElementById('txttotalorderquantity').value) * 1;
			
			//alert(tot_po_qnty);
			var rowCount = $('#material-details tr').length - 1;
			var len  = totalProp = 0;
	
			for (var i = 1; i <= rowCount; i++) 
			{
				len = len + 1;
				var txt_order_qnty = ($('#txtorderQty_' + i).val()) * 1;
				//alert(value);
				//alert(txt_order_qnty);
				if(balanceQty!=0 && balanceQty<proportionQty)
				{
					var proportionate_qnty = number_format_common((((balanceQty / tot_po_qnty) * txt_order_qnty)), 2, 0, 1);
				}else
				{

					var proportionate_qnty = number_format_common((((value / tot_po_qnty) * txt_order_qnty)), 2, 0, 1);
				}
				
				$('#txtRcvQty_' + i).val(number_format_common(proportionate_qnty, 2, 0, 1));
				
				fnc_total_calculate();
				
				/*
				totalProp += (proportionate_qnty * 1);

				if (rowCount == len) 
				{
					var balance = value - totalProp;
					proportionate_qnty = (proportionate_qnty*1) + (balance*1);
				}

				var order_id  = $('#txt_order_id_' + i).val();
				var order_status_id =  $('#txt_order_status_id_' + i).val()*1;
				var qnty = po_data[order_id];
				var update_id = '<? //echo $update_id;?>';
								
				if(update_id!="" && order_status_id==3 && qnty<proportionate_qnty) // at update even do not allow cancel po allocated qnty
				{
					$('#txt_qnty_' + i).val(number_format_common(qnty, 2, 0, 1));
				}
				else
				{
					$('#txtRcvQty_' + i).val(number_format_common(proportionate_qnty, 2, 0, 1));
				}*/

			}
		}
		
    function fnResetForm() 
	{
       
	   
	   
	   //  echo load_submit_buttons($permission, 'fnc_material_receive', 0, 0,"reset_form('matarial_dtls_entry_1','material-details','cbo_company_name*cbo_location_name*cbo_within_group*cbo_party_name*txt_receive_challan*txt_issue_no*txt_job_no*txtTotRcvQty*hdn_job_no_id*hdn_update_id*txt_receive_no*txt_issue_no','txt_receive_date','')", 1);
								
	    set_button_status(0, permission, 'fnc_material_receive', 1);
		reset_form('matarial_dtls_entry_1','','cbo_company_name*cbo_location_name*cbo_within_group*cbo_party_name*txt_receive_challan*txt_issue_no*txt_job_no*txtTotRcvQty*hdn_job_no_id*hdn_update_id*txt_receive_no*txt_issue_no*txt_receive_date','','')
		
		
		
		//reset_form('washorderentry_1','','','cbo_within_group,1*cbo_currency,1*cboUom_1,2','','');
		$('#tbl_dtls_emb tbody tr:not(:first)').remove();
		$('#cbo_company_name').attr('disabled',false);
		$('#cbo_within_group').attr('disabled',false);
		$('#cbo_party_name').attr('disabled',false);
		$('#cbo_location_name').attr('disabled',false);
		$('#txt_issue_no').attr('disabled',false);
		$('#txt_job_no').attr('disabled',false);
		//$('#cboGmtsItem_1').attr('disabled',false);
		//$('#cboProcessName_1').attr('disabled',false);
		//$('#cbotype_1').attr('disabled',false);
		//$('#cboBodyPart_1').attr('disabled',false);
    }

    function check_balance_qnty(id)
    {
    	var balance = $("#"+id).attr('placeholder')*1;
    	var qty = $("#"+id).val()*1;
    	
    	if(qty>balance)
    	{
    		alert("Receive quantity Should Not Be Greater Than Balance Quantity. Balance Quantity is:"+balance);
    		$("#"+id).val('');
    		fnc_total_calculate();
    		return;
    	}
    }
	function fnc_service_yarn_daying_print(operation){

		if ( form_validation('cbo_company_name*hdn_update_id','Company Name*Update Id')==false )
		{
			return;
		}
		if(operation==1){
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#hdn_update_id').val()+'*'+report_title+'*'+$('#cbo_within_group').val(), "service_yd_material_receive_print", "requires/yd_material_receive_controller") 
			//return;
			show_msg("3"); 
		}
	}
</script>
</head>
<body>
<div style="width:100%;" >
    <?php echo load_freeze_divs ('../../', $permission); ?>
    <div>
        <fieldset style="width:500px; margin: 0 auto;">
            <legend>Yarn Dyeing Material Receive</legend>
            <form name="yarnreceive_1" id="yarnreceive_1" autocomplete="off">  
                <table width="800" cellspacing="2" cellpadding="0" border="0">
                     <tr>
                        <td colspan="3" align="right">Receive ID</td>
                        <td colspan="3" align="left">
                            <input class="text_boxes" type="text" name="txt_receive_no" id="txt_receive_no" placeholder="Double Click" onDblClick="job_search_popup_rcv()" style="width:150px;" readonly />
                            <input type="hidden" name="hdn_update_id" id="hdn_update_id">                            
                            <input type="hidden" name="hdn_job_no_id" id="hdn_job_no_id">
                            <input type="hidden" name="hdn_booking_type_id" id="hdn_booking_type_id">
                            <input type="hidden" name="hdn_booking_without_order" id="hdn_booking_without_order">
                             <input type="hidden" id="work_order_material_auto_receive" />
                        </td>
                    </tr>
                    <tr>
                        <td align="right" class="must_entry_caption">Company Name</td>
                        <td> 
                            <?php echo create_drop_down('cbo_company_name', 163, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", 'id,company_name', 1, '-- Select Company --', $selected, "get_php_form_data(this.value,'load_variable_settings','requires/yd_material_receive_controller');get_company_config(this.value);"); ?>
                        </td>
                        <td align="right">Location Name</td>
                        <td id="location_td">
                            <?php echo create_drop_down('cbo_location_name', 163, $blank_array, '', 1, '-- Select Location --', $selected, ''); ?>
                        </td>
                        <td align="right" class="must_entry_caption">Within Group</td>
                        <td>
                            <?php echo create_drop_down('cbo_within_group', 163, $yes_no, '', 0, '', 1, "load_drop_down('requires/yd_material_receive_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer', 'buyer_td');get_td_coller(this.value);"); ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" class="must_entry_caption">Party</td>
                        <td id="buyer_td">
                            <?php echo create_drop_down('cbo_party_name', 163, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", 'id,company_name', 1, '-- Select Party --', $selected, ''); ?>
                        </td>
                        <td class="must_entry_caption" align="right">Receive Challan</td>
                        <td>
                            <input class="text_boxes" type="text" name="txt_receive_challan" id="txt_receive_challan" style="width:150px;" />
                        </td>
                        <td class="must_entry_caption" align="right">Receive Date</td>
                        <td>
                            <input type="text" name="txt_receive_date" id="txt_receive_date"  class="datepicker" style="width:150px" />
                        </td>
                    </tr>
                    <tr>
                        <td align="right" id="printing_work_oder">Issue Id</td>
                        <td align="left">
                            <input class="text_boxes" type="text" name="txt_issue_no" id="txt_issue_no" placeholder="Double Click" onDblClick="issue_search_popup();"  style="width:150px;" readonly />
                            <input type="hidden" name="hid_challan_id" id="hid_challan_id">  
                        </td>
                        <td align="right" class="must_entry_caption">Job No</td>
                        <td align="left">
                            <input class="text_boxes" type="text" name="txt_job_no" id="txt_job_no" onDblClick="job_search_popup();" placeholder="Double Click" style="width:150px;" readonly />
                        </td>
                    </tr>
                </table>
            </form>
        </fieldset>
    </div>
    <div id="mat_dtls_area" style="margin-top: 15px;">
        <fieldset style="width: 1200px; margin: 0 auto;">
            <legend>Material Details Entry</legend>
            <form id="matarial_dtls_entry_1" name="matarial_dtls_entry_1">
                <table cellpadding="0" cellspacing="2" border="0" width="1200" id="tbl_dtls_emb">
                    <thead class="form_table_header">
                     <tr align="center">
                     <td  colspan="14">
                            <label for="distribution_type_0">Proportionately</label> 
                            <input type="text" name="proportionately_qnty" id="proportionately_qnty" style="width:60px;" class="text_boxes_numeric" value="<? echo $txt_qnty; ?>" onChange="calculate_poportion(this.value)"/> 
                      </td>
                        </tr>
                        <tr align="center" >
                            <th width="80" class="must_entry_caption" id="style_no_td">Style</th>
                            <th width="60" class="must_entry_caption" id="job_no_td">Job No/Sales order no</th>
                            <th width="60">Cust. Buyer</th>
                            <th width="100">Raw Yarn Lot</th>
                            <th width="40">Count</th>
                            <th width="60" class="must_entry_caption" id="type_no_td">Yarn Type</th>
                            <th width="180">Yarn Composition</th>
                            <th width="40">Item Color</th>
                            <th width="40">No of Bag</th>
                            <th width="40" class="must_entry_caption" id="bag_no_td">Cone Per Bag</th>
                            <th width="40" class="must_entry_caption" id="cone_no_td">No of Cone</th>
                            <th width="70">AVG. Wt. Per Cone</th>
                            <th width="40">UOM</th>
                            <th width="50" class="must_entry_caption">Rcv Qty.</th>
                        </tr>
                    </thead> 
                    <tbody id="material-details"></tbody>
                </table>
                <table width="1200" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td align="center" colspan="13" valign="middle" class="button_container">
                            <?php
							
							//function reset_form( forms, divs, fields, default_val, extra_func, non_refresh_ids )
                              
								
								  echo load_submit_buttons( $permission, "fnc_material_receive", 0,0,"fnResetForm();",1); 
								
                                // load_submit_buttons($permission, $sub_func, $is_update, $is_show_print, $refresh_function, $btn_id, $is_show_approve)
                            ?>
                        </td>
                    </tr>
					<tr>
						<td  align="center" colspan="10">
					    	<input id="Print" class="formbutton" type="button" style="width:80px" onClick="fnc_service_yarn_daying_print(1)" name="print" value="Print">
						</td>
                    </tr> 
                </table>
            </form>
        </fieldset>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>