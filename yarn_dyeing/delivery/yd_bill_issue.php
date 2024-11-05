<?
	/*--- ----------------------------------------- Comments
	Purpose         :   This form will create Yarn Dyeing Bill Entry               
	Functionality   :   
	JS Functions    :
	Created by      :   Sakib Ahamed 
	Creation date   :   04-11-2023
	Updated by      :       
	Update date     :
	Oracle Convert  :       
	Convert date    :      
	QC Performed BY :       
	QC Date         :   
	Comments        :
	*/
	session_start();
	require_once('../../includes/common.php');
	extract($_REQUEST);
	$_SESSION['page_permission']=$permission;
	//--------------------------------------------------------------------------------------------------------------------
	echo load_html_head_contents('Yarn Dyeing Bill Entry', '../../', 1, 1, $unicode, 1, '');
?>

</head>

<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = '../logout.php';
	var permission='<?php echo $permission; ?>';
	function exchange_rate(val)
	{
		if(form_validation('cbo_company_name*txt_bill_date', 'Company Name*Bill Date')==false )
		{
			$("#cbo_currency_id").val(0);
			return;
		}
		
		if(val==0)
		{
			$('#txt_bill_date').removeAttr('disabled','disabled');
			$('#cbo_company_name').removeAttr('disabled','disabled');
			$("#txt_exchange_rate").val("");
		}
		else if(val==1)
		{
			$("#txt_exchange_rate").val(1);
			$('#txt_bill_date').attr('disabled','disabled');
			$('#cbo_company_name').attr('disabled','disabled');
			$('#txt_exchange_rate').attr('disabled','disabled');
		}
		else
		{
			var bill_date = $('#txt_bill_date').val();
			var company_name = $('#cbo_company_name').val();
			var response=return_global_ajax_value( val+"**"+bill_date+"**"+company_name, 'check_conversion_rate', '', 'requires/yd_bill_issue_controller');
			$('#txt_exchange_rate').val(response);
			$('#txt_bill_date').attr('disabled','disabled');
			$('#cbo_company_name').attr('disabled','disabled');
			$('#txt_exchange_rate').attr('disabled','disabled');
		}
	}

	function job_search_popup_job()
    {

    	if ( form_validation('cbo_company_name*cbo_within_group*cbo_party_name*cbo_currency_id','Company Name*Within Group* Party Name*Currency')==false )
		{
			return;
		}
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var cbo_within_group=document.getElementById('cbo_within_group').value;
		var cbo_party_name=document.getElementById('cbo_party_name').value;
		var cbo_pro_type=document.getElementById('cbo_pro_type').value;
		var cbo_order_type=document.getElementById('cbo_order_type').value;
		var data = '&cbo_company_name='+cbo_company_name+'&cbo_within_group='+cbo_within_group+'&cbo_party_name='+cbo_party_name+'&cbo_pro_type='+cbo_pro_type+'&cbo_order_type='+cbo_order_type;
    	
    	page_link='requires/yd_bill_issue_controller.php?action=job_search_popup_job'+data;
    	title='Job Search Popup';

    	emailwindow=dhtmlmodal.open('JobBox', 'iframe', page_link, title, 'width=1150px, height=500px, center=1, resize=0, scrolling=0', '../../');
    	emailwindow.onclose=function()
        {
        	var theform=this.contentDoc.forms[0];
            var party_id=this.contentDoc.getElementById("hidden_party_id").value;

            var receive_ids=this.contentDoc.getElementById("txt_selected_id").value;

            var data =party_id+"_"+receive_ids+"_"+cbo_company_name;

            show_list_view(data,'dtls_list_view','save_list','requires/yd_bill_issue_controller','');

            $('#cbo_company_name').attr('disabled','disabled');
            $('#cbo_within_group').attr('disabled','disabled');
            $('#cbo_party_name').attr('disabled','disabled');
        }
    }

	function load_receive_data(job_no,receive_no,row_id)
	{
		var job_no = job_no;
		var cbo_company_name  = $('#cbo_company_name').val();
		var data = job_no+'_'+receive_no+'_'+cbo_company_name;

		$("#txt_job_no").val( job_no );
		// $("#txt_receive_aganist").val( receive_no );
		$('#txt_job_no').attr('disabled','disabled');

		$("#hidden_row_id").val(row_id);

		// get_php_form_data( job_no, "load_php_yd_job_data_to_form", "requires/yd_bill_issue_controller" );

		show_list_view(data,'receive_dtls_list_view','bill_details','requires/yd_bill_issue_controller','');

		set_button_status(0, permission, 'fnc_yd_bill_entry',1);
	}

	function openmypage_bill_no()
	{ 
		if(form_validation('cbo_company_name', 'Company Name')==false )
		{
			return;
		}
		else
		{
			var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
			//alert(data);
			var page_link='requires/yd_bill_issue_controller.php?action=bill_popup&data='+data;
			var title="Bill Popup";	
			
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=1000px, height=400px, center=1, resize=0, scrolling=0','../')
			emailwindow.onclose=function()
            {
				var theform=this.contentDoc.forms[0];
				
				var update_id=this.contentDoc.getElementById("update_id").value;
                var bill_no=this.contentDoc.getElementById("txt_bill_no").value;
                var order_no=this.contentDoc.getElementById("hidden_order_no").value;
				

                var data = update_id+'_'+order_no;

                get_php_form_data(data, "load_php_yd_bill_data_to_form", "requires/yd_bill_issue_controller" );

                var update_id = $("#update_id").val();

                $("#hidden_order_no").val(order_no);

                document.getElementById("bill_details").innerHTML = "";
                set_button_status(1, permission, 'fnc_yd_delivery_entry',1);

	    		show_save_list_view(update_id,order_no);
            }
		}
	}
	function show_save_list_view(update_id,order_no)
	{
		var update_id = update_id;
		var order_no = order_no;
		var cbo_company_name  = $('#cbo_company_name').val();
		var dataString = "&data="+update_id+'_'+order_no+'_'+cbo_company_name;
		var data="action=bill_dtls_list_view"+dataString;

		http.open("POST","requires/yd_bill_issue_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = bill_dtls_list_view_reponse;
	}

	function bill_dtls_list_view_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("******");
			var markup = reponse[0];

			$("#tbl_list_view").html(markup);
		}
	}
	function load_bill_data(yd_job_no,bill_no)
	    {
	    	var yd_job_no = yd_job_no;
			var cbo_company_name = $('#cbo_company_name').val();
			var hdn_update_id = $('#update_id').val();

	    	var data = yd_job_no+'_'+bill_no+'_'+cbo_company_name+'_'+hdn_update_id;

	    	$("#txt_job_no").val( yd_job_no );
	    	$("#txt_delivery_no").val( bill_no );


	    	show_list_view(data,'bill_update_dtls_list_view','bill_details','requires/yd_bill_issue_controller','');

	    	// $("#txt_check_box_advance").prop( "checked", false );

	    	set_button_status(1, permission, 'fnc_yd_bill_entry',1);
	    }

	function validateBillQty(row_id)
	{
		var order_qty=(document.getElementById("txtOrderqty_"+row_id).value)*1;
		var bill_qty=(document.getElementById("txtbillqty_"+row_id).value)*1;
		if(order_qty < bill_qty){
			alert("Bill Quantity Can Not Larger Than Order Quantity");
			document.getElementById("txtbillqty_"+row_id).value=order_qty;
		}

	}

	function calculateAmount(row_id)
	{
		var rate=(document.getElementById("txtrate_"+row_id).value)*1;
		var bill_qty=(document.getElementById("txtbillqty_"+row_id).value)*1;
		var exchange_rate=(document.getElementById("txt_exchange_rate").value)*1;
		var amount= rate*bill_qty;
		var domestic_amount= amount*exchange_rate;
		
		document.getElementById("txtamount_"+row_id).value=number_format_common(amount,2);
		document.getElementById("txtdomesticamount_"+row_id).value=number_format_common(domestic_amount,2);
	}
	function fnc_yd_bill_entry( operation )
    {
		if(operation==4)
		{
			var yd_job_no    		= $('#txt_job_no').val();
			var bill_no    			= $('#txt_bill_no').val();
			var update_id    		= $('#update_id').val();
			var cbo_company_name	= $('#cbo_company_name').val();
			
			var report_title=$( "div.form_caption" ).html();
			print_report(yd_job_no+'*'+bill_no+'*'+cbo_company_name+'*'+update_id+'*'+report_title, "yd_bill_print", "requires/yd_bill_issue_controller") 
			show_msg("3");
		}
        
        var cbo_within_group = $("#cbo_within_group").val(); 
         
		 
		 if(cbo_within_group==1)
		 {  
			if ( form_validation('cbo_company_name*cbo_within_group*cbo_party_name*cbo_currency_id*txt_bill_date','Company*Within Group*Party*Currency*Bill Date')==false ){
				return;
			}
		 }
		 else
		 {
			if ( form_validation('cbo_company_name*cbo_within_group*cbo_party_name*cbo_currency_id*txt_bill_date','Company*Within Group*Party*Currency*Bill Date')==false )
			{
				return;
			} 
		 }

        //  if('<? //echo implode('*',$_SESSION['logic_erp']['mandatory_field'][698]);?>'){
		// 	if (form_validation('<? //echo implode('*',$_SESSION['logic_erp']['mandatory_field'][698]);?>','<? //echo implode('*',$_SESSION['logic_erp']['mandatory_message'][698]);?>')==false)
		// 	{
		// 		return;
		// 	}
		// }
		if(operation==0 || operation==1 || operation==2)
    	{
        
			var txt_bill_no         = $('#txt_bill_no').val();
			var cbo_company_name    = $('#cbo_company_name').val();
			var cbo_location_name   = $('#cbo_location_name').val();
			var cbo_within_group    = $('#cbo_within_group').val();
			var cbo_party_name      = $('#cbo_party_name').val();
			var cbo_party_location  = $('#cbo_party_location').val();
			var txt_bill_date 		= $('#txt_bill_date').val();
			var cbo_currency        = $('#cbo_currency_id').val();
			var exchange_rate       = $('#txt_exchange_rate').val();
			var cbo_pro_type       	= $('#cbo_pro_type').val();
			var cbo_order_type      = $('#cbo_order_type').val();
			var txt_remarks         = $('#txt_remarks').val();
			var update_id           = $('#update_id').val();
			var txt_yd_job_no      	= $('#txt_job_no').val();
			


			
				var j=0;
	        	var i=0;
	        	var data_all="";

	        	$("#bill_details tr").each(function()
	        	{
	        		var ydJobId       		= $(this).find('input[name="ydJobId[]"]').val();
	        		var ydJobDtlsId       	= $(this).find('input[name="ydJobDtlsId[]"]').val();
	        		var deliveryId       	= $(this).find('input[name="deliveryId[]"]').val();
	        		var deliveryDtlsId      = $(this).find('input[name="deliveryDtlsId[]"]').val();
	        		var txtdeliveryNo       = $(this).find('input[name="txtdeliveryNo[]"]').val();
	        		var txtdeliveryDate     = $(this).find('input[name="txtdeliveryDate[]"]').val();
	        		var txtstyleRef         = $(this).find('input[name="txtstyleRef[]"]').val();
	        		var txtsaleOrder        = $(this).find('input[name="txtsaleOrder[]"]').val();
	        		var txtsaleOrderID      = $(this).find('input[name="txtsaleOrderID[]"]').val();
	        		var buyerBuyer         	= $(this).find('input[name="buyerBuyer[]"]').val();
	        		var txtlot         		= $(this).find('input[name="txtYdlot[]"]').val();
	        		var txtGrayLot         	= $(this).find('input[name="txtGrayLot[]"]').val();
	        		var txtHiddenGrayLot    = $(this).find('input[name="txtHiddenGrayLot[]"]').val();
	        		var txtcountTypeId      = $(this).find('input[name="txtcountTypeId[]"]').val();
	        		var txtcountType        = $(this).find('select[name="txtcountType[]"]').val();
	        		var txtcountId        	= $(this).find('input[name="txtcountId[]"]').val();
	        		var cboCount         	= $(this).find('select[name="cboCount[]"]').val();
	        		var cboYarnTypeId       = $(this).find('input[name="cboYarnTypeId[]"]').val();
	        		var cboYarnType         = $(this).find('select[name="cboYarnType[]"]').val();
	        		var txtydCompositionId  = $(this).find('input[name="txtydCompositionId[]"]').val();
	        		var cboComposition      = $(this).find('select[name="cboComposition[]"]').val();
	        		var txtYarnColorId      = $(this).find('input[name="txtYarnColorId[]"]').val();
	        		var txtYarnColor        = $(this).find('select[name="txtYarnColor[]"]').val();
	        		var txtnoBag         	= $(this).find('input[name="txtnoBag[]"]').val();
	        		var txtConeBag         	= $(this).find('input[name="txtConeBag[]"]').val();
	        		var cboUomId         	= $(this).find('input[name="cboUomId[]"]').val();
	        		var cboUom         		= $(this).find('select[name="cboUom[]"]').val();
	        		var txtOrderqty         = $(this).find('input[name="txtOrderqty[]"]').val();
	        		var txtbillqty   		= $(this).find('input[name="txtbillqty[]"]').val();
	        		var txtrate      		= $(this).find('input[name="txtrate[]"]').val();
	        		var txtamount			= $(this).find('input[name="txtamount[]"]').val();
	        		var txtdomesticamount	= $(this).find('input[name="txtdomesticamount[]"]').val();
	        		var txtremarks  		= $(this).find('input[name="txtremarks[]"]').val();
	        		var txtHiddenDtlsId  	= $(this).find('input[name="txtHiddenDtlsId[]"]').val();
	        		var workOrderNo  		= $(this).find('input[name="workOrderNo[]"]').val();
	        		var ydOrderId  			= $(this).find('input[name="ydOrderId[]"]').val();

	            	
					j++;i++;

					data_all +="&ydJobId_"+j+"='"+ydJobId+"'&ydJobDtlsId_"+j+"='"+ydJobDtlsId+"'&deliveryId_"+j+"='"+deliveryId+"'&deliveryDtlsId_"+j+"='"+deliveryDtlsId+"'&txtdeliveryNo_"+j+"='"+txtdeliveryNo+"'&txtdeliveryno_"+j+"='"+txtdeliveryNo+"'&txtdeliveryDate_"+j+"='"+txtdeliveryDate+"'&txtstyleRef_"+j+"='"+txtstyleRef+"'&txtsaleOrder_"+j+"='"+txtsaleOrder+"'&txtsaleOrderID_"+j+"='"+txtsaleOrderID+"'&buyerBuyer_"+j+"='"+buyerBuyer+"'&txtlot_"+j+"='"+txtlot+"'&txtGrayLot_"+j+"='"+txtGrayLot+"'&txtcountTypeId_"+j+"='"+txtcountTypeId+"'&txtcountId_"+j+"='"+txtcountId+"'&cboYarnTypeId_"+j+"='"+cboYarnTypeId+"'&txtydCompositionId_"+j+"='"+txtydCompositionId+"'&txtYarnColorId_"+j+"='"+txtYarnColorId+"'&txtnoBag_"+j+"='"+txtnoBag+"'&txtConeBag_"+j+"='"+txtConeBag+"'&cboUomId_"+j+"='"+cboUomId+"'&txtOrderqty_"+j+"='"+txtOrderqty+"'&txtbillqty_"+j+"='"+txtbillqty+"'&txtrate_"+j+"='"+txtrate+"'&txtamount_"+j+"='"+txtamount+"'&txtdomesticamount_"+j+"='"+txtdomesticamount+"'&txtremarks_"+j+"='"+txtremarks+"'&txtHiddenDtlsId_"+j+"='"+txtHiddenDtlsId+"'&workOrderNo_"+j+"='"+workOrderNo+"'&ydOrderId_"+j+"='"+ydOrderId+"'";
	            	
	        	});
				if(data_all=='')
				{
					alert("Please Insert At Least One Receive Quantity!!!");
					return;
				}

			

				var data="action=save_update_delete&operation="+operation+'&total_row='+i+'&txt_bill_no='+txt_bill_no+'&cbo_company_name='+cbo_company_name+'&cbo_location_name='+cbo_location_name+'&cbo_within_group='+cbo_within_group+'&cbo_party_name='+cbo_party_name+'&cbo_party_location='+cbo_party_location+'&txt_bill_date='+txt_bill_date+'&cbo_currency='+cbo_currency+'&txt_exchange_rate='+exchange_rate+'&cbo_pro_type='+cbo_pro_type+'&cbo_order_type='+cbo_order_type+'&txt_remarks='+txt_remarks+'&update_id='+update_id+'&txt_yd_job_no='+txt_yd_job_no+data_all;
				
				//alert (data); return; 
				freeze_window(operation);
				http.open("POST","requires/yd_bill_issue_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_yd_bill_entry_response;
			
		}
    }

    function fnc_yd_bill_entry_response()
    {
        
        if(http.readyState == 4) 
        {
            //alert (http.responseText);//return;
            var response=trim(http.responseText).split('**');
            
            if(trim(response[0])=='20'){
                alert(response[1]);
                release_freezing();return;
            }
			
			
			if(trim(response[0])=='40'){
                alert("Order Qty cannot be less than Receive Qty"+"\n Order Qty :"+response[1]);
 				$('#txtOrderqty_'+response[2]).focus();
                release_freezing();return;
            }

            if(response[0]==0 || response[0]==1){
                var bill_no      = response[1];
                var update_id   = response[2];
                var order_no    = response[3];
                var within_group = $('#cbo_within_group').val();
                /*if(within_group==2){
                    document.getElementById('txt_order_no').value = response[3];
                }*/
                document.getElementById('txt_bill_no').value = response[1];
                document.getElementById('update_id').value = response[2];
                $('#cbo_within_group').attr('disabled',true);
                $('#cbo_company_name').attr('disabled',true);
                $('#cbo_party_name').attr('disabled',true);
                $('#cbo_party_location').attr('disabled',true);
                $('#cbo_currency_id').attr('disabled',true);

				var hidden_row_id = $("#hidden_row_id").val()*1;
	            $("#dtls_row_"+hidden_row_id).remove();
				
				show_save_list_view(update_id,order_no);
                set_button_status(1, permission, 'fnc_yd_bill_entry',1);
                //btn_load_change_bookings();

                var row_num = $('#tbl_dtls_bill tbody tr').length;

                for(var i =1; i<=row_num;i++)
                {
                    //set_multiselect('txtprocess_'+i,'0','0','','0');
                }
                set_all_onclick();

            }else if(response[0]==2){
                location.reload();
            }
            show_msg(response[0]);
            release_freezing();
			// alert(update_id,order_no);
        }
    }
	function fnc_yd_delivery_print(delivery_id)
	{
		var cbo_company_name = $('#cbo_company_name').val();
		var report_title="Delivery ";
		var cbo_template_id="1";
		delivery_no="";
		print_report(cbo_company_name+'*'+delivery_id+'*'+delivery_no+'*'+report_title+'*'+cbo_template_id, "embl_delivery_print", "requires/yd_delivery_entry_v2_controller") 
		show_msg("3");
	}



</script>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission); ?>
		<form name="dyeingbill_1" id="dyeingbill_1" autocomplete="off">
			<fieldset style="width:1050px;">
				<legend>Yarn Dyeing Bill Entry</legend>
				<table width="1030" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
					<tr>
						<td colspan="4" align="right"><strong>Bill ID</strong></td>
						<td colspan="4">
							<input class="text_boxes" type="text" name="txt_bill_no" id="txt_bill_no" onDblClick="openmypage_bill_no();" placeholder="Double Click" readonly style="width: 150px;" value="" />
							<input type="hidden" name="update_id" id="update_id" value="">
							<input type="hidden" name="hidden_order_no" id="hidden_order_no" value="">
							<input type="hidden" name="hidden_row_id" id="hidden_row_id">
						</td>
					</tr>
					<tr>
						<td align="right" class="must_entry_caption">YD Company</td>
						<td> 
							<?php echo create_drop_down('cbo_company_name', 150, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 1, '-- Select Company --', $selected, "load_drop_down('requires/yd_bill_issue_controller', this.value,'load_drop_down_location', 'location_td' );load_drop_down( 'requires/yd_bill_issue_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_party', 'party_td' );",0); ?>
						</td>
						<td align="right" class="must_entry_caption">Location Name</td>
						<td id="location_td"> 
							<?php echo create_drop_down('cbo_location_name', 150, $blank_array, '', 1, '-- Select Location --', $selected, "",0); ?>
						</td>
						<td align="right" class="must_entry_caption" >Within Group</td>
						<td> 
							<?php echo create_drop_down('cbo_within_group', 150, $yes_no, '', 0, '-- Select Within Group --', $selected, "load_drop_down( 'requires/yd_bill_issue_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_party', 'party_td' );load_drop_down( 'requires/yd_bill_issue_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_party_location', 'party_location_td' );",0); ?>
						</td>
						<td align="right" class="must_entry_caption" >Party</td>
						<td id="party_td"> 
							<?php echo create_drop_down('cbo_party_name', 120, $blank_array, '', 1, '-- Select Location --', $selected, "",0); ?>
						</td>
					</tr>
					<tr>
						<td align="right">Party Location</td>
						<td id="party_location_td"> 
							<?php echo create_drop_down('cbo_party_location', 150, $blank_array, '', 1, '-- Select Location --', $selected, "",0); ?>
						</td>
						<td align="right" class="must_entry_caption">Bill Date</td>
						<td> 
							<input class="datepicker" type="text" name="txt_bill_date" id="txt_bill_date" placeholder="Write Bill Date" style="width:140px;" value="<? echo date('d-m-Y');?>" />
						</td>
						<td align="right" class="must_entry_caption">Currency</td>
						<td id="currency_td">
							<? echo create_drop_down("cbo_currency_id", 150, $currency,"", 1, "-- Select Currency --",$selected,"exchange_rate(this.value)", "","","","","",7 ); ?>
						</td> 
						<td class="must_entry_caption" align="right">Exch. Rate</td>
						<td>
							<input type="text" name="txt_exchange_rate" id="txt_exchange_rate" style="width:110px" class="text_boxes_numeric"  value=""  readonly/>
						</td>
					<tr>
						<td align="right">Prod. Type</td>
						<td> 
							<?php echo create_drop_down('cbo_pro_type', 150, $w_pro_type_arr, '', 1, '-- Select --', $selected, ""); ?>
	                    </td> 
						<td align="right">Order Type</td>
						<td>
						<?php echo create_drop_down('cbo_order_type', 150, $w_order_type_arr, '', 1, '-- Select --', $selected, ""); ?>
						</td>
						<td align="right">Remarks</td>
						<td colspan="3">
							<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:395px;" placeholder="Remarks Entry">
						</td>
					</tr>
				</table>
			</fieldset>
			<fieldset style="width: 1250px; margin: 0 auto;">
	        		<legend>Yarn Dyeing Bill Details</legend>
					<table>
						<tr>
						<td align="right" class="must_entry_caption">Job No.</td>
						<td> 
							<input class="text_boxes" type="text" style="width:140px" name="txt_job_no" id="txt_job_no" placeholder="Double Click For Job No" onDblClick="job_search_popup_job()" style="width:140px;" readonly />
						</td>
						</tr>
					</table>
	        		<table cellpadding="0" cellspacing="0" border="0" width="1240" id="tbl_dtls_bill">
	        		 	<thead class="form_table_header">
	        		 		<th width="80" id="delivery_id_no_td">Delivery ID</th>
	        		 		<th width="80" id="delivery_date_no_td">Delivery Date</th>
	        		 		<th width="80" id="style_no_td">Style</th>
                            <th width="60" id="buyer_job_no_td">Buyer Job</th>
                            <th width="60" id="cus_buyer_no_td" >Cust. Buyer</th>
                            <th width="80" id="yd_batch_lot_no_td" >YD Batch/Lot</th>
                            <th width="80" id="grey_lot_no_td" >Grey Lot</th>
                            <th width="60" id="count_type_no_td">Count Type</th>
                            <th width="60" id="count_no_td" >Count</th>
                            <th width="80" id="yarn_type_no_td" >Yarn Type</th>
                            <th width="100" id="composition_no_td" >Yarn Composition</th>
                            <th width="80" id="color_no_td">Y/D Color</th>
                            <th width="40" id="no_of_bag_no_td">No of Bag</th>
                            <th width="50" id="cone_per_bag_no_td" >Cone Per Bag</th>
                            <th width="50" id="uom_no_td" >UOM</th>
                            <th width="50" id="order_qty_no_td" >Order Qty</th>
                            <th width="50" id="Bill_qty_no_td" >Bill Qty</th>
                            <th width="50" id="rate_no_td" >Rate</th>
                            <th width="50" id="amount" > Amount</th>
                            <th width="50" id="Domestic_amount" >Domestic Amount</th>
                            <th width="50" id="Remarks" >Remarks</th>
	        		 	</thead>
	        		 	<tbody id="bill_details"> 	
		        		</tbody>
	        		</table>
	        		<table width="1240" cellspacing="2" cellpadding="0" border="0">
	                    <tr>
	                        <td align="center" valign="middle" class="button_container">
	                        	<? //echo create_drop_down( "cbo_template_id", 100, $report_template_list,'', 0, '', 0, "");?>
	                            <?php
									  echo load_submit_buttons( $permission, "fnc_yd_bill_entry", 0,1,"fnResetForm();",1); 
	                            ?>
	                        </td> 
	                    </tr>
	                </table>
	        	</fieldset>
		</form>
	</div>
	<br>
		<div>
			<fieldset style="width: 640px; margin: 0 auto;">
				<legend>Save List View:</legend>
				<div id="save_list_view">
					<table width="635" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
						<thead>
							<th width="35">Sl</th>
							<th width="150">Delivery No</th>
							<th width="150">Job No</th>
							<th width="100">WO No</th>
							<th width="100">Prod. Type</th>
							<th width="100">Order Type</th>
						</thead>
						<tbody id="tbl_list_view">
						</tbody>
					</table>
				</div>
			</fieldset>
		</div>
	<br>
		<div>
			<fieldset style="width: 640px; margin: 0 auto;">
				<legend> Save List:</legend>
				<div id="save_list"></div>
			</fieldset>
		</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
