<?
	/*--- ----------------------------------------- Comments
	Purpose         :   This form will create Yarn Dyeing Delivery Entry               
	Functionality   :   
	JS Functions    :
	Created by      :   Md. MInul Hasan
	Creation date   :   02-10-2022
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
	echo load_html_head_contents('arn Dyeing Delivery Entry', '../../', 1, 1, $unicode, 1, '');
?>
	<script>
		if( $('#index_page', window.parent.document).val()!=1) window.location.href = '../logout.php';
	    var permission='<?php echo $permission; ?>';

	    function job_search_popup_job()
	    {

	    	if ( form_validation('cbo_company_name*cbo_within_group*cbo_party_name','Company Name*Within Group* Party Name')==false )
			{
				return;
			}

			var cbo_company_name  = $('#cbo_company_name').val();
			var cbo_within_group  = $('#cbo_within_group').val();
			var cbo_party_name    = $('#cbo_party_name').val();

			var data = '&cbo_company_name='+cbo_company_name+'&cbo_within_group='+cbo_within_group+'&cbo_party_name='+cbo_party_name;
	    	
	    	page_link='requires/yd_delivery_entry_v2_controller.php?action=job_search_popup_job'+data;
        	title='Job Search Popup';

        	emailwindow=dhtmlmodal.open('JobBox', 'iframe', page_link, title, 'width=1150px, height=500px, center=1, resize=0, scrolling=0', '../../');
        	emailwindow.onclose=function()
            {
            	var theform=this.contentDoc.forms[0];
                var party_id=this.contentDoc.getElementById("hidden_party_id").value;

                var receive_ids=this.contentDoc.getElementById("txt_selected_id").value;

                var data =party_id+"_"+receive_ids+"_"+cbo_company_name;

                show_list_view(data,'dtls_list_view','list_view','requires/yd_delivery_entry_v2_controller','');

                $('#cbo_company_name').attr('disabled','disabled');
                $('#cbo_within_group').attr('disabled','disabled');
                $('#cbo_party_name').attr('disabled','disabled');
            }
	    }

	    function load_receive_data(job_no,job_id,receive_no,rcv_id,row_id)
	    {
	    	var job_no = job_no;
			var cbo_company_name  = $('#cbo_company_name').val();
	    	var data = job_no+'_'+receive_no+'_'+cbo_company_name;
			
	    	$("#txt_job_no").val( job_no );
	    	$("#txtHdnJobId").val( job_id );
	    	$("#txt_receive_aganist").val( receive_no );
	    	$("#txtHdnRcvId").val( rcv_id );
	    	//$('#txt_job_no').attr('disabled','disabled');

	    	$("#hidden_row_id").val(row_id);

	    	get_php_form_data( data, "load_php_yd_job_data_to_form", "requires/yd_delivery_entry_v2_controller" );

	    	show_list_view(data,'receive_dtls_list_view','delivery_details','requires/yd_delivery_entry_v2_controller','');

	    	set_button_status(0, permission, 'fnc_yd_delivery_entry',1);
	    }

	    function calculate_receive_qty(id,value){

	    	var id = id.split("_");
	    	var value = value*1;

	    	var row_num = id[1];

			var issued_qty = $("#txtReceiveQty_"+row_num).attr("placeholder")*1;
			// var issued_qty=$('#txtTotalqty_'+row_num).val()*1;
			var prev_delv_qty=$('#txtTotalReceiveqty_'+row_num).val()*1;
			var tot_finish_qty=value+prev_delv_qty;
			$('#txtTotalFinqty_'+row_num).val(tot_finish_qty);
			
			var process_loss=$('#txtProcessLoss_'+row_num).val()*1;
			
			var delv_grey_qty=issued_qty+((process_loss/100)*issued_qty);	
	    	// var balance = $('#txtHiddenbalanceqty_'+row_num).val()*1;
	    	// var order_qty = $('#hiddenOrderQty_'+row_num).val()*1;

	    	// var cbo_order_type = $('#cbo_order_type').val()*1;

	    	// if(value>balance || value>order_qty)
	    	if(value>issued_qty)
	    	{
	    		alert("Delivery Quantity Can Not Be Greater Than Store Receive Quantity Or Finish Qty.");
				tot_finish_qty=issued_qty+prev_delv_qty;
	    		$('#txtReceiveQty_'+row_num).val(issued_qty);	
				$('#txtTotalFinqty_'+row_num).val(number_format_common(tot_finish_qty,2));
				$('#txtProLoss_'+row_num).val(0);
				$('#txtGreyReceiveQty_'+row_num).val(delv_grey_qty,2);
				$('#txtBncGreyqty_'+row_num).val(0);
				
				var pro_loss=((delv_grey_qty-issued_qty)/issued_qty)*100;
				
				// pro_loss= number_format_common(pro_loss,2);
				$('#txtProLoss_'+row_num).val(number_format_common(pro_loss,2));
				
	    	}else{
				var pro_loss=$('#txtProLoss_'+row_num).val()*1;
				
				
				$('#txtProLoss_'+row_num).removeAttr('disabled');
				$('#txtGreyReceiveQty_'+row_num).removeAttr('disabled');
			}
			var pro_loss_id='#txtProLoss_'+row_num;
			cal_pro_loss(pro_loss_id,pro_loss);
	    }

		function cal_current_delv_grey_qty(id,value){
			var id = id.split("_");
			var value = value*1;
	    	var row_num = id[1];
			
			var current_delv = $('#txtReceiveQty_'+row_num).val()*1;
			var rcv_qty = $('#txtTotalqty_'+row_num).val()*1;
			var prev_gray_rcv_qty = $('#txtTotalReceiveGreyqty_'+row_num).val()*1;
			var available_grey_qty=rcv_qty-prev_gray_rcv_qty;
			var pro_loss=((value-current_delv)/current_delv)*100;
			
			if(value>0){
				var issued_qty = $("#txtReceiveQty_"+row_num).attr("placeholder")*1;
				var process_loss=$('#txtProcessLoss_'+row_num).val()*1;
				var delv_grey_qty=number_format_common(issued_qty+((process_loss/100)*issued_qty),2);
				if(available_grey_qty < value){
					alert("Current Delivery Agaisnt Grey Qty larger than Available Quantity.");  
					$('#txtGreyReceiveQty_'+row_num).val(number_format_common(available_grey_qty,2));
						pro_loss=((available_grey_qty-current_delv)/current_delv)*100;
						$('#txtBncGreyqty_'+row_num).val(number_format_common(0,2));
					
				}else{
					$('#txtBncGreyqty_'+row_num).val(number_format_common(available_grey_qty-value,2));
				}
				if(pro_loss<0)
				{
					pro_loss=0;
					$('#txtReceiveQty_'+row_num).val(number_format_common(value,2));
				}
				
				pro_loss= number_format_common(pro_loss,2);
				$('#txtProLoss_'+row_num).val(pro_loss);
				
			}
			
		}
		function cal_pro_loss(id,value){
			var id = id.split("_");
			var value = value*1;
	    	var row_num = id[1];

			var rcv_qty = $('#txtTotalqty_'+row_num).val()*1;
			var issued_qty = $("#txtReceiveQty_"+row_num).attr("placeholder")*1;
			var current_delv = $('#txtReceiveQty_'+row_num).val()*1;
			var prev_grey_qty = $('#txtTotalReceiveGreyqty_'+row_num).val()*1;
			var tot_grey_qty = $('#hdnTotGreyQty_'+row_num).val()*1;
			var process_loss=$('#txtProcessLoss_'+row_num).val()*1;
			var delv_grey_qty=number_format_common(issued_qty+((process_loss/100)*issued_qty),2);
			
			var grey_qty= ((current_delv*value)/100)+current_delv;
			// if(grey_qty>rcv_qty){
			// 	alert("Current Delivery Agaisnt Grey Qty must not larger than Receive Quantity.");
			// 	var grey_qnty=$('#txtProLoss_'+row_num).val(0);
			// 	return;
			// } 
			if(delv_grey_qty < grey_qty){
				alert("Current Delivery Agaisnt Grey Qty larger than Received Quantity.");  
				var grey_qnty=$('#txtProLoss_'+row_num).val(0);
				$('#txtGreyReceiveQty_'+row_num).val(number_format_common(current_delv,2));
				return;
			}else{

				var grey_qnty= number_format_common(grey_qty,2, 0, '');
			}
			$('#txtGreyReceiveQty_'+row_num).val(grey_qnty);
			var balance_grey= rcv_qty-(grey_qty+prev_grey_qty);
			
			$('#txtBncGreyqty_'+row_num).val(number_format_common(balance_grey,2,));
			
		}

	    function set_all_delivery(value){

	    	var row_num = document.getElementById("delivery_details").rows.length*1; 

	    	if(row_num>0)
	    	{
	    		if( document.getElementById('txt_check_box_advance').checked==true)
				{
					for (var i =1; i<=row_num; i++) {
	    			
		    			var value= $('#hiddenOrderQty_'+i).val()*1;

		    			var value1= $('#txtbalanceqty_'+i).val()*1;

		    			if(value>value1)
		    			{
		    				$('#txtReceiveQty_'+i).val(value1);

		    				value = value1;
		    			}
		    			else
		    			{
		    				$('#txtReceiveQty_'+i).val(value);
		    			}


		    			var id = '#txtReceiveQty_'+i;

		    			calculate_receive_qty(id,value);
		    		}
				}
				else
				{
					for (var i =1; i<=row_num; i++) {
	    			
		    			var value= $('#hiddenOrderQty_'+i).val()*1;

		    			$('#txtReceiveQty_'+i).val('');
		    		}
				}
	    		
	    	}
	    }

	    function fnc_yd_delivery_entry(operation)
	    {
	    	if(operation==4)
    		{
				var print_copy=confirm("Press  \"Cancel\"  to print Single page\nPress  \"OK\"  to print Triple page");
				if (print_copy==true)
				{
					show_item=3;
				}
				else
				{
					show_item=1;
				}
    			var update_id    				= $('#hdn_update_id').val();
		        var txt_delivery_no    			= $('#txt_delivery_no').val();
		        var cbo_company_name    		= $('#cbo_company_name').val();
		        var cbo_template_id    			= $('#cbo_template_id').val();

		        var report_title=$( "div.form_caption" ).html();
				print_report(cbo_company_name+'*'+update_id+'*'+txt_delivery_no+'*'+report_title+'*'+cbo_template_id+'*'+show_item, "embl_delivery_print", "requires/yd_delivery_entry_v2_controller") 
				show_msg("3");
    		}

	    	if(operation==0 || operation==1 || operation==2)
    		{
    			if ( form_validation('cbo_company_name*cbo_within_group*cbo_party_name*txt_job_no*txt_delivery_date','Company Name*Within Group*Patry Name*Job No*Receive Date')==false )
				{
					return;
				}

		        var txt_job_no   				= $('#txt_job_no').val();
		        var txt_rcv_no   				= $('#txt_receive_aganist').val();
		        var txt_delivery_date 			= $('#txt_delivery_date').val();
		        var cbo_company_name    		= $('#cbo_company_name').val();
		        var cbo_location_name    		= $('#cbo_location_name').val();
		        var cbo_within_group    		= $('#cbo_within_group').val();
		        var cbo_party_name    			= $('#cbo_party_name').val();
		        var cbo_party_location   		= $('#cbo_party_location').val();
		        var txt_wo_no   				= $('#txt_wo_no').val();
		        var cbo_pro_type 				= $('#cbo_pro_type').val();
		        var cbo_order_type    			= $('#cbo_order_type').val();
		        var hdn_update_id    			= $('#hdn_update_id').val();
		        var txt_delivery_no    			= $('#txt_delivery_no').val();
		        var txt_driver_name    			= $('#txt_driver_name').val();
		        var txt_vehicle_no    			= $('#txt_vehicle_no').val();
		        var txt_remarks    				= $('#txt_remarks').val();
		        var hdn_job_id    				= $('#txtHdnJobId').val();
		        var hdn_rcv_id    				= $('#txtHdnRcvId').val();

		        var j=0;
	        	var i=0;
	        	var check_field=0;
	        	var data_all="";
				
	        	$("#delivery_details tr").each(function()
	        	{
	        		var txtstyleRef         = $(this).find('input[name="txtstyleRef[]"]').val();
	        		var txtsaleOrder        = $(this).find('input[name="txtsaleOrder[]"]').val();
	        		var txtsaleOrderID      = $(this).find('input[name="txtsaleOrderID[]"]').val();
	        		var buyerBuyer         	= $(this).find('input[name="buyerBuyer[]"]').val();
	        		var txtlot         		= $(this).find('input[name="txtlot[]"]').val();
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
	        		var txtHiddenOrderqty   = $(this).find('input[name="txtHiddenOrderqty[]"]').val();
	        		var txtProcessLoss      = $(this).find('input[name="txtProcessLoss[]"]').val();
	        		var hiddenProcessLoss	= $(this).find('input[name="txtHiddenProcessLoss[]"]').val();
	        		var txtadjTypeId		= $(this).find('input[name="txtadjTypeId[]"]').val();
	        		var txtadjType			= $(this).find('select[name="txtadjType[]"]').val();
	        		var txtTotalqty			= $(this).find('input[name="txtTotalqty[]"]').val();
	        		var txtHiddenTotalqty	= $(this).find('input[name="txtHiddenTotalqty[]"]').val();
	        		var previousReceiveqty  = $(this).find('input[name="txtPreviousReceiveqty[]"]').val();
	        		var hiddenPreReceiveqty = $(this).find('input[name="txtHiddenPreviousReceiveqty[]"]').val();
	        		var txtHiddenbalanceqty = $(this).find('input[name="txtHiddenbalanceqty[]"]').val();
	        		var txtbalanceqty       = $(this).find('input[name="txtbalanceqty[]"]').val();
	        		var txtReceiveQty       = $(this).find('input[name="txtReceiveQty[]"]').val();
	        		var hiddenOrderQty     	= $(this).find('input[name="hiddenOrderQty[]"]').val();
	        		var txtHiddenDeliveryId = $(this).find('input[name="txtHiddenDeliveryId[]"]').val();;
	        		var txtHiddenDtlsId  	= $(this).find('input[name="txtHiddenDtlsId[]"]').val();
	        		var txtHiddenRcvDtlsId  = $(this).find('input[name="txtHiddenRcvDtlsId[]"]').val();
	        		var txtTotalReceiveqty  = $(this).find('input[name="txtTotalReceiveqty[]"]').val();
	        		var txtGreyReceiveQty	= $(this).find('input[name="txtGreyReceiveQty[]"]').val();
	        		var txtProLoss  		= $(this).find('input[name="txtProLoss[]"]').val();
					var txtTotalqty			=$(this).find('input[name="txtTotalqty[]"]').val();

	            	if(txtReceiveQty*1>0)
	            	{
	            		j++;
	            		i++;

	            		data_all +="&txtstyleRef_"+j+"='"+txtstyleRef+"'&txtsaleOrder_"+j+"='"+txtsaleOrder+"'&txtsaleOrderID_"+j+"='"+txtsaleOrderID+"'&buyerBuyer_"+j+"='"+buyerBuyer+"'&txtlot_"+j+"='"+txtlot+"'&txtGrayLot_"+j+"='"+txtGrayLot+"'&txtcountTypeId_"+j+"='"+txtcountTypeId+"'&txtcountId_"+j+"='"+txtcountId+"'&cboYarnTypeId_"+j+"='"+cboYarnTypeId+"'&txtydCompositionId_"+j+"='"+txtydCompositionId+"'&txtYarnColorId_"+j+"='"+txtYarnColorId+"'&txtnoBag_"+j+"='"+txtnoBag+"'&txtConeBag_"+j+"='"+txtConeBag+"'&cboUomId_"+j+"='"+cboUomId+"'&txtOrderqty_"+j+"='"+txtOrderqty+"'&txtProcessLoss_"+j+"='"+txtProcessLoss+"'&txtadjTypeId_"+j+"='"+txtadjTypeId+"'&txtHiddenTotalqty_"+j+"='"+txtHiddenTotalqty+"'&previousReceiveqty_"+j+"='"+previousReceiveqty+"'&txtbalanceqty_"+j+"='"+txtbalanceqty+"'&txtReceiveQty_"+j+"='"+txtReceiveQty+"'&hiddenOrderQty_"+j+"='"+hiddenOrderQty+"'&txtHiddenDeliveryId_"+j+"='"+txtHiddenDeliveryId+"'&txtHiddenDtlsId_"+j+"='"+txtHiddenDtlsId+"'&txtHiddenRcvDtlsId_"+j+"='"+txtHiddenRcvDtlsId+"'&txtTotalReceiveqty_"+j+"='"+txtTotalReceiveqty+"'&txtGreyReceiveQty_"+j+"='"+txtGreyReceiveQty+"'&txtTotalqty_"+j+"='"+txtTotalqty+"'&txtProLoss_"+j+"='"+txtProLoss+"'";
	            	}	
	        	});

				if(data_all=='')
				{
					alert("Please Insert At Least One Receive Quantity!!!");
					return;
				}

				if(check_field==0)
		        {
		        	var data="action=save_update_delete&operation="+operation+'&total_row='+i+'&txt_job_no='+txt_job_no+'&txt_delivery_date='+txt_delivery_date+'&cbo_company_name='+cbo_company_name+'&cbo_location_name='+cbo_location_name+'&cbo_within_group='+cbo_within_group+'&cbo_party_name='+cbo_party_name+'&cbo_party_location='+cbo_party_location+'&txt_wo_no='+txt_wo_no+'&cbo_pro_type='+cbo_pro_type+'&cbo_order_type='+cbo_order_type+'&hdn_update_id='+hdn_update_id+'&txt_delivery_no='+txt_delivery_no+'&txt_driver_name='+txt_driver_name+'&txt_vehicle_no='+txt_vehicle_no+'&txt_remarks='+txt_remarks+'&hdn_job_id='+hdn_job_id+'&hdn_rcv_id='+hdn_rcv_id+'&txt_rcv_no='+txt_rcv_no+data_all;

		        	//alert(data);
		        	//freeze_window(operation);
		            http.open("POST","requires/yd_delivery_entry_v2_controller.php",true);
		            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		            http.send(data);
		            http.onreadystatechange = fnc_yd_delivery_entry_response;
		        }
		        else
		        {
		        	return;
		        }
    		}
	    }

	    function fnc_yd_delivery_entry_response(){
	    	
	    	if(http.readyState == 4) 
	        {
	            var response=trim(http.responseText).split('**');
				if( response[0]==11)
				{
					alert(response[1]);
				}
	            if(response[0]==0 || response[0]==1){

	                var delivery_no  = response[1];
	                var update_id   = response[2];

	                $("#txt_delivery_no").val( delivery_no );
	                $("#hdn_update_id").val( update_id );

	                var hidden_row_id = $("#hidden_row_id").val()*1;

	                $("#dtls_row_"+hidden_row_id).remove();


			    	$("#txt_job_no").val('');
			    	$("#cbo_pro_type").val('');
			    	$("#cbo_order_type").val('');
			    	$("#txt_wo_no").val('');

	                set_button_status(1, permission, 'fnc_yd_delivery_entry',1);
	                document.getElementById("delivery_details").innerHTML = "";

	                show_delivery_list_view(update_id,delivery_no);
	            }

	            if(response[0]==2)
	            {
	            	var delivery_no  = response[1];
	                var update_id   = response[2];

	            	set_button_status(1, permission, 'fnc_yd_delivery_entry',1);
	                document.getElementById("delivery_details").innerHTML = "";

	                $("#txt_job_no").val('');
			    	$("#cbo_pro_type").val('');
			    	$("#cbo_order_type").val('');
			    	$("#txt_wo_no").val('');

			    	show_delivery_list_view(update_id,delivery_no);

			    	if(response[4]==1)
			    	{
			    		reset_form('dyeingdelivery_1');
			    	}
	            }
	            
	            $("#txt_check_box_advance").prop( "checked", false );

	            show_msg(response[0]);
	            release_freezing();
	        }
	    }

	    function load_delivery_data(job_no,delivery_no,pro_type,order_type,order_no,receive_no,rcv_id,job_id)
	    {
	    	var job_no = job_no;
			var cbo_company_name    		= $('#cbo_company_name').val();
			var hdn_update_id    		= $('#hdn_update_id').val();

	    	var data = job_no+'_'+delivery_no+'_'+receive_no+'_'+cbo_company_name+'_'+hdn_update_id+'_'+rcv_id;

	    	$("#txt_job_no").val( job_no );
	    	$("#cbo_pro_type").val( pro_type );
	    	$("#cbo_order_type").val( order_type );
	    	$("#txt_wo_no").val( order_no );
	    	$("#txt_delivery_no").val( delivery_no );
	    	$("#txt_receive_aganist").val( receive_no );
	    	$("#txtHdnJobId").val( job_id );
	    	$("#txtHdnRcvId").val( rcv_id );
	    	

	    	//get_php_form_data(data, "load_php_yd_delivery_data_to_form", "requires/yd_delivery_entry_v2_controller" );

	    	show_list_view(data,'delivery_update_dtls_list_view','delivery_details','requires/yd_delivery_entry_v2_controller','');

	    	$("#txt_check_box_advance").prop( "checked", false );

	    	set_button_status(1, permission, 'fnc_yd_delivery_entry',1);
	    }

	    function openmypage_system_id()
	    {
			var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
	    	page_link='requires/yd_delivery_entry_v2_controller.php?action=job_search_popup_delivery&data='+data;
        	title='Delivery Search Popup';

        	emailwindow=dhtmlmodal.open('JobBox', 'iframe', page_link, title, 'width=1150px, height=500px, center=1, resize=0, scrolling=0', '../../');
        	emailwindow.onclose=function()
            {
            	var theform=this.contentDoc.forms[0];
                var delivery_id=this.contentDoc.getElementById("hidden_mst_id").value;
                var job_no=this.contentDoc.getElementById("hidden_job_no").value;
                var delivery_no=this.contentDoc.getElementById("hidden_delivery_no").value;

				var cbo_company_name  = $('#cbo_company_name').val();
                var data = job_no+'_'+delivery_no+'_'+cbo_company_name;

                get_php_form_data(data, "load_php_yd_delivery_data_to_form", "requires/yd_delivery_entry_v2_controller" );

                var update_id = $("#hdn_update_id").val();

                $("#txt_delivery_no").val(delivery_no);

                document.getElementById("delivery_details").innerHTML = "";
                set_button_status(1, permission, 'fnc_yd_delivery_entry',1);

	    		show_delivery_list_view(update_id,delivery_no);
            }
	    }
	    function show_delivery_list_view(update_id,delivery_no)
	    {
	    	var update_id = update_id;
	    	var delivery_no = delivery_no;
            var cbo_company_name  = $('#cbo_company_name').val();
	    	var dataString = "&data="+update_id+'_'+delivery_no+'_'+cbo_company_name;
			var data="action=delivery_dtls_list_view"+dataString;

			http.open("POST","requires/yd_delivery_entry_v2_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = delivery_dtls_list_view_reponse;
	    }

	    function delivery_dtls_list_view_reponse()
		{	
			if(http.readyState == 4) 
			{	 
				var reponse=trim(http.responseText).split("******");
				var markup = reponse[0];

				$("#tbl_list_view").html(markup);
			}
		}
	</script>
</head>
	<body onLoad="set_hotkey()">
	  <div style="width:100%;" align="center">
	    <? echo load_freeze_divs ("../../",$permission); ?>
	    	<form name="dyeingdelivery_1" id="dyeingdelivery_1" autocomplete="off">
            	<fieldset style="width:1050px;">
            		<legend>Yarn Dyeing Delivery Entry</legend>
            		<table width="1030" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
            			<tr>
	                        <td colspan="8" align="center"><strong>Delivery ID</strong>
	                            <input class="text_boxes" type="text" name="txt_delivery_no" id="txt_delivery_no" onDblClick="openmypage_system_id();" placeholder="Double Click" readonly style="width: 150px;" />
	                            <input type="hidden" name="hdn_update_id" id="hdn_update_id">
	                            <input type="hidden" name="hidden_row_id" id="hidden_row_id">
	                        </td>
	                    </tr>
						<tr><td>&nbsp;</td></tr>
	                    <tr>
	                    	<td align="right" class="must_entry_caption">Company Name</td>
	                        <td> 
	                            <?php echo create_drop_down('cbo_company_name', 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", 'id,company_name', 1, '-- Select Company --', $selected, "load_drop_down('requires/yd_delivery_entry_v2_controller', this.value+'_'+document.getElementById('cbo_within_group').value,'load_drop_down_party', 'party_td' );load_drop_down('requires/yd_delivery_entry_v2_controller', this.value,'load_drop_down_location', 'location_td' );load_drop_down('requires/yd_delivery_entry_v2_controller',document.getElementById('cbo_party_name').value,'load_drop_down_party_location', 'party_location_td' );",0); ?>
	                        </td>
	                        <td align="right">Location Name</td>
	                        <td id="location_td"> 
	                            <?php echo create_drop_down('cbo_location_name', 150, $blank_array, '', 1, '-- Select Location --', $selected, "",0); ?>
	                        </td>
	                        <td align="right" class="must_entry_caption" >Within Group</td>
	                        <td> 
	                            <?php echo create_drop_down('cbo_within_group', 150, $yes_no, '', 1, '-- Select Within Group --', $selected, "load_drop_down( 'requires/yd_delivery_entry_v2_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_party', 'party_td' );load_drop_down('requires/yd_delivery_entry_v2_controller',document.getElementById('cbo_party_name').value,'load_drop_down_party_location', 'party_location_td' );",0); ?>
	                        </td>
	                        <td align="right" class="must_entry_caption" >Party Name</td>
	                        <td id="party_td"> 
	                            <?php echo create_drop_down('cbo_party_name', 120, $blank_array, '', 1, '-- Select Location --', $selected, "",0); ?>
	                        </td>
	                    </tr>
	                    <tr>
	                    	<td align="right">Party Location</td>
	                        <td id="party_location_td"> 
	                            <?php echo create_drop_down('cbo_party_location', 150, $blank_array, '', 1, '-- Select Location --', $selected, "",0); ?>
	                        </td>
	                    	<td align="right" class="must_entry_caption">Delivery Date</td>
	                        <td> 
	                            <input class="datepicker" type="text" name="txt_delivery_date" id="txt_delivery_date" placeholder="Write Delivery Date" style="width:140px;" value="<? echo date('d-m-Y');?>" />
	                        </td>

							<td align="right">Driver Name</td>
							<td><input type="text" name="txt_driver_name" id="txt_driver_name" class="text_boxes" style="width:140px;" placeholder=""></td>
							<td align="right">Vehicle No</td>
							<td><input type="text" name="txt_vehicle_no" id="txt_vehicle_no" class="text_boxes" style="width:110px;" placeholder=""></td>
	                        
	                    </tr>
						<tr>
							<td align="right">Remarks</td>
							<td colspan="3">
	                        	<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:430px;" placeholder="Remarks Entry">
	                        </td>
							<td colspan="4">&nbsp;</td>
						</tr>
            		</table>
            	</fieldset>
            	<br>
	        	<fieldset style="width: 1300px; margin: 0 auto;">
	        		<legend>Yarn Dyeing Delivery Details</legend>
	        		<table cellpadding="0" cellspacing="0" border="0" width="1000" id="tbl_job_dtls_delivery">
	        			<tr>
	                    	<td align="right" id="job_td" class="must_entry_caption">Job No</td>
	                        <td> 
	                            <input class="text_boxes" type="text" name="txt_job_no" id="txt_job_no" placeholder="Double Click For Job No" onDblClick="job_search_popup_job()" style="width:140px;" readonly />
								<input class="text_boxes_numeric" type="hidden" name="	[]" id="txtHdnJobId" value="">
	                        </td>
	                    	<td align="right">WO No</td>
	                        <td> 
	                            <input class="text_boxes" type="text" name="txt_wo_no" id="txt_wo_no" placeholder="Display" style="width:140px;" readonly />
	                        </td>
	                    	<td align="right">Prod. Type</td>
	                        <td> 
	                            <?php echo create_drop_down('cbo_pro_type', 120, $w_pro_type_arr, '', 1, '-- Select Location --', $selected, "",1); ?>
	                        </td>
	                        <td align="right">Order Type</td>
	                        <td> 
	                            <?php echo create_drop_down('cbo_order_type', 120, $w_order_type_arr, '', 1, '-- Select --', $selected, "",1); ?>
	                        </td>
	                        <td align="right">Receive Aganist</td>
	                        <td> 
	                            <input class="text_boxes" type="text" name="txt_receive_aganist" id="txt_receive_aganist" placeholder="Display" style="width:140px;" readonly />
								<input class="text_boxes_numeric" type="hidden" name="txtHdnRcvId[]" id="txtHdnRcvId" value="">
	                        </td>
	                    </tr>

	        		</table>
	        		<table cellpadding="0" cellspacing="0" border="0" width="1320" id="tbl_dtls_delivery">
	        		 	<thead class="form_table_header">
	        		 		
                            
                            <th width="80" id="grey_lot_no_td" >Raw Yarn Lot</th>
                            
                            <th width="60" id="count_no_td" >Count</th>
                            <th width="80" id="yarn_type_no_td" >Yarn Type</th>
                            <th width="100" id="composition_no_td" >Yarn Composition</th>
                            <th width="120" id="color_no_td">Y/D Color</th>
                            <th width="80" id="yd_batch_lot_no_td" >YD Batch</th>
                            <th width="50" id="order_uom_no_td" >UOM</th>
                            <th width="50" id="order_qty_no_td" >Order Qty</th>
                            <th width="30" id="process_loss_no_td" >Pro Loss %</th>
                            <th width="80" id="adj_type_no_td" >Adj. Type</th>
                            <th width="80" id="qty_no_td" >Insp. Qty</th>
                            <th width="80" id="batch_qty_no_td" >Batch Qty</th>
                            <th width="80">Previous Delivery</th>
                            <th width="80">Previous Delivery Grey Qty</th>
                            <!-- <th width="50" id="grey_qty_no_td" >Grey Qty</th>
                             <th width="50" id="grey_qty_no_td" >Receive Qty</th> -->
                            <!-- <th width="50" id="balance_no_td" >Balance</th> --> 
                            <th width="50" id="yd_eceive_no_td" class="must_entry_caption">
                            	Current Delivery
                            	<!-- <input type="checkbox" name="txt_check_box_advance" id="txt_check_box_advance" onClick="set_all_delivery();"> -->
                            </th>
							<th width="50" class="must_entry_caption">Pro. loss <br>%</th>
							<th width="50" class="must_entry_caption">Current Delivery Agaisnt Grey Qty</th>
							<th width="50">Balance Grey Qty</th>
							<th width="50">Total Fini. Qty</th>
							<th width="60" id="count_type_no_td">Count Type</th>
							<th width="40" id="no_of_bag_no_td">No of Bag</th>
                            <th width="50" id="cone_per_bag_no_td" >Cone Per Bag</th>
							<th width="80" id="style_no_td">Style</th>
                            <th width="60" id="buyer_no_td">Buyer Job</th>
                            <th width="60" id="cus_buyer_no_td" >Cust. Buyer</th>
							
	        		 	</thead>
	        		 	<tbody id="delivery_details"> 	
		        		</tbody>
	        		</table>
	        		<table width="1300" cellspacing="2" cellpadding="0" border="0">
	                    <tr>
	                        <td align="center" valign="middle" class="button_container">
	                        	<? echo create_drop_down( "cbo_template_id", 100, $report_template_list,'', 0, '', 0, "");?>
	                            <?php
									  echo load_submit_buttons( $permission, "fnc_yd_delivery_entry", 0,1,"fnResetForm();",1); 
	                            ?>
	                        </td> 
	                    </tr>
	                </table>
	        	</fieldset>
            </form>
		</div>
		<br>
		<div>
			<fieldset style="width: 800px; margin: 0 auto;">
				<legend>Delivery List View:</legend>
				<div id="delivery_dtls_list_view">
					<table width="900" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
						<thead>
							<th width="35">Sl</th>
							<th width="150">Party Name</th>
							<th width="150">Delivery No</th>
							<th width="150">Job No</th>
							<th width="100">WO No</th>
							<th width="100">Prod. Type</th>
							<th width="100">Order Type</th>
							<th width="150">Receive Aganist</th>
						</thead>
						<tbody id="tbl_list_view">
						</tbody>
					</table>
				</div>
			</fieldset>
		</div>
		<br>
		<div>
			<fieldset style="width: 800px; margin: 0 auto;">
				<legend>Receive List View:</legend>
				<div id="list_view"></div>
			</fieldset>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>