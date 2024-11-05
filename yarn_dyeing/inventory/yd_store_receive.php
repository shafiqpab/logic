<?php

	/*--- ----------------------------------------- Comments 
	Purpose         :   This form is for Yarn Dyeing Store Receive         
	Functionality   :   
	JS Functions    :
	Created by      :   Md. Minul Hasan
	Creation date   :   28-09-2022
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

	echo load_html_head_contents("Yarn Dyeing Store Receive ","../../", 1, 1, $unicode,1,1);
?>
	<script type="text/javascript">
	    if( $('#index_page', window.parent.document).val()!=1) window.location.href = '../../logout.php';
	    var permission='<?php echo $permission; ?>';

	    function fnc_change_challan_permission(val)
	    {

	    	if(val==20)
	    	{
	    		$('#txt_challan_no').removeAttr('readonly');
	    		$('#txt_challan_no').attr('placeholder','Write Challan No');
	    		$('#txt_challan_no').val('');

	    		$('#job_td').html(' Job No');
	    		document.getElementById("job_td").style.color = "blue";
	    	} 
	    	else
	    	{
	    		$('#txt_challan_no').attr('readonly','readonly');
	    		$('#txt_challan_no').attr('placeholder','Display Challan No');
	    		$('#txt_challan_no').val('');

	    		$('#job_td').html('Delivery Challan');
	    		document.getElementById("job_td").style.color = "blue";
	    	}
	    }

	    function job_search_popup_job()
	    {

	    	if ( form_validation('cbo_receive_basis','Receive Basis')==false )
	        {
	            return;
	        }

	        var cbo_receive_basis = $("#cbo_receive_basis").val();

	        if(cbo_receive_basis==21)
	        {
	        	alert("Delivery basis Data Not Avaible. Please Select Job Basis");
	        	return;
	        }
	    	
	    	page_link='requires/yd_store_receive_controller.php?action=job_search_popup_job';
        	title='Job Search Popup';

        	emailwindow=dhtmlmodal.open('JobBox', 'iframe', page_link, title, 'width=1150px, height=500px, center=1, resize=0, scrolling=0', '../../');
        	emailwindow.onclose=function()
            {
            	var theform=this.contentDoc.forms[0];
                var theemail=this.contentDoc.getElementById("hidden_mst_id").value;

                $("#txt_job_no").val( theemail );

                var receiveBasis =  $("#cbo_receive_basis").val();

                var data =theemail+"_"+receiveBasis;

                $('#cbo_receive_basis').attr('disabled','disabled');

                get_php_form_data( theemail, "load_php_yd_job_data_to_form", "requires/yd_store_receive_controller" );

                show_list_view(data,'job_details_list_view','receive_details','requires/yd_store_receive_controller','');
                
            }
	    }

	    function fnc_store_receive_entry(operation)
	    {
	    	if(operation==4)
    		{
    			var update_id    				= $('#hdn_update_id').val();
		        var txt_receive_no    			= $('#txt_receive_no').val();
		        var cbo_company_name    		= $('#cbo_company_name').val();

		        var report_title=$( "div.form_caption" ).html();
				//print_report(cbo_company_name+'*'+update_id+'*'+txt_receive_no+'*'+report_title, "embl_store_receive_print", "requires/yd_store_receive_controller") 
				//show_msg("3");
    		}
	    	if(operation==0 || operation==1 || operation==2)
    		{
    			if ( form_validation('cbo_receive_basis*txt_job_no*txt_receive_date','Receive Basis*Company Name*Receive Date')==false )
				{
					return;
				}

				var cbo_receive_basis    		= $('#cbo_receive_basis').val();
		        var txt_job_no   				= $('#txt_job_no').val();
		        var txt_challan_no   			= $('#txt_challan_no').val();
		        var txt_receive_date 			= $('#txt_receive_date').val();
		        var cbo_company_name    		= $('#cbo_company_name').val();
		        var cbo_location_name    		= $('#cbo_location_name').val();
		        var cbo_within_group    		= $('#cbo_within_group').val();
		        var cbo_party_name    			= $('#cbo_party_name').val();
		        var cbo_party_location   		= $('#cbo_party_location').val();
		        var txt_wo_no   				= $('#txt_wo_no').val();
		        var cbo_pro_type 				= $('#cbo_pro_type').val();
		        var cbo_order_type    			= $('#cbo_order_type').val();
		        var hdn_update_id    			= $('#hdn_update_id').val();
		        var txt_receive_no    			= $('#txt_receive_no').val();

		        var j=0;
	        	var i=0;
	        	var check_field=0;
	        	var data_all="";

	        	$("#receive_details tr").each(function()
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
	        		var txtHiddenReceiveId  = $(this).find('input[name="txtHiddenReceiveId[]"]').val();
	        		var txtHiddendtlsId  	= $(this).find('input[name="txtHiddendtlsId[]"]').val();

	            	if(txtReceiveQty*1>0)
	            	{
	            		j++;
	            		i++;

	            		data_all +="&txtstyleRef_"+j+"='"+txtstyleRef+"'&txtsaleOrder_"+j+"='"+txtsaleOrder+"'&txtsaleOrderID_"+j+"='"+txtsaleOrderID+"'&buyerBuyer_"+j+"='"+buyerBuyer+"'&txtlot_"+j+"='"+txtlot+"'&txtGrayLot_"+j+"='"+txtGrayLot+"'&txtcountTypeId_"+j+"='"+txtcountTypeId+"'&txtcountId_"+j+"='"+txtcountId+"'&cboYarnTypeId_"+j+"='"+cboYarnTypeId+"'&txtydCompositionId_"+j+"='"+txtydCompositionId+"'&txtYarnColorId_"+j+"='"+txtYarnColorId+"'&txtnoBag_"+j+"='"+txtnoBag+"'&txtConeBag_"+j+"='"+txtConeBag+"'&cboUomId_"+j+"='"+cboUomId+"'&txtOrderqty_"+j+"='"+txtOrderqty+"'&txtProcessLoss_"+j+"='"+txtProcessLoss+"'&txtadjTypeId_"+j+"='"+txtadjTypeId+"'&txtHiddenTotalqty_"+j+"='"+txtHiddenTotalqty+"'&previousReceiveqty_"+j+"='"+previousReceiveqty+"'&txtbalanceqty_"+j+"='"+txtbalanceqty+"'&txtReceiveQty_"+j+"='"+txtReceiveQty+"'&txtHiddenReceiveId_"+j+"='"+txtHiddenReceiveId+"'&txtHiddendtlsId_"+j+"='"+txtHiddendtlsId+"'";
	            	}	

	            	
	        	});

				if(data_all=='')
				{
					alert("Please Insert At Least One Receive Quantity!!!");
					return;
				}

				if(check_field==0)
		        {
		        	var data="action=save_update_delete&operation="+operation+'&total_row='+i+'&cbo_receive_basis='+cbo_receive_basis+'&txt_job_no='+txt_job_no+'&txt_challan_no='+txt_challan_no+'&txt_receive_date='+txt_receive_date+'&cbo_company_name='+cbo_company_name+'&cbo_location_name='+cbo_location_name+'&cbo_within_group='+cbo_within_group+'&cbo_party_name='+cbo_party_name+'&cbo_party_location='+cbo_party_location+'&txt_wo_no='+txt_wo_no+'&cbo_pro_type='+cbo_pro_type+'&cbo_order_type='+cbo_order_type+'&hdn_update_id='+hdn_update_id+'&txt_receive_no='+txt_receive_no+data_all;

		        	//alert(data);
		        	freeze_window(operation);
		            http.open("POST","requires/yd_store_receive_controller.php",true);
		            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		            http.send(data);
		            http.onreadystatechange = fnc_store_receive_entry_response;
		        }
		        else
		        {
		        	return;
		        }

    		}
	    }

	    function fnc_store_receive_entry_response(){
	    	
	    	if(http.readyState == 4) 
	        {
	            var response=trim(http.responseText).split('**');

	            if(response[0]==0 || response[0]==1){
	                var receive_no  = response[1];
	                var update_id   = response[2];
	                var job_no   	= response[3];

	                var receiveBasis = $('#cbo_receive_basis').val();

	                document.getElementById('txt_receive_no').value = response[1];
	                document.getElementById('hdn_update_id').value = response[2];
	               	$('#txt_job_no').attr('disabled',true);
	               	$('#cbo_receive_basis').attr('disabled',true);

	                show_list_view(update_id+'_'+receive_no+'_'+receiveBasis+'_'+job_no,'receive_dtls_list_view','receive_details','requires/yd_store_receive_controller','');
	                set_button_status(1, permission, 'fnc_store_receive_entry',1);
	                set_all_onclick();
	            }

	            if(response[0]==2)
	            {
	            	fnResetForm();
	            }

	            if(response[0]==13)
	            {
	            	alert(response[1]);
	            }
	            
	            show_msg(response[0]);
	            release_freezing();
	        }
	    }

	    function calculate_receive_qty(id,value){

	    	var id = id.split("_");
	    	var value = value*1;

	    	var row_num = id[1];

	    	var balance = $('#txtHiddenbalanceqty_'+row_num).val()*1;

	    	if(value>balance)
	    	{
	    		//alert("Receive Quantity Can Not Be Greater Than Balance Quantity");
	    		//$('#txtReceiveQty_'+row_num).val(0);
	    		//return;
	    	}
	    }

	    function job_search_popup_rcv()
	    {

	    	if ( form_validation('cbo_receive_basis','Receive Basis')==false )
	        {
	            return;
	        }

	        var cbo_receive_basis = $("#cbo_receive_basis").val();

	        if(cbo_receive_basis==21)
	        {
	        	alert("Delivery basis Data Not Avaible. Please Select Job Basis");
	        	return;
	        }

	    	page_link='requires/yd_store_receive_controller.php?action=job_search_popup_rcv';
        	title='Receive Search Popup';

        	emailwindow=dhtmlmodal.open('JobBox', 'iframe', page_link, title, 'width=1150px, height=500px, center=1, resize=0, scrolling=0', '../../');
        	emailwindow.onclose=function()
            {
            	var theform=this.contentDoc.forms[0];
                var receive_id=this.contentDoc.getElementById("hidden_mst_id").value;
                var job_no=this.contentDoc.getElementById("hidden_job_no").value;

                var receive_no =  $("#txt_receive_no").val();
                var receive_basis =  $("#cbo_receive_basis").val();

                var data =receive_id+"_"+receive_no+"_"+receive_basis+"_"+job_no;

                $('#cbo_receive_basis').attr('disabled','disabled');

                get_php_form_data( receive_id, "load_php_yd_receive_data_to_form", "requires/yd_store_receive_controller" );

                show_list_view(data,'receive_dtls_list_view','receive_details','requires/yd_store_receive_controller','');

                set_button_status(1, permission, 'fnc_store_receive_entry',1);
            }
	    }

		function fnResetForm()
	 	{
			set_button_status(0, permission, 'fnc_store_receive_entry',1);
			reset_form('yarnstorereceive_1','receive_details','','','','');
	 	}

	 	function set_all_delivery(value){

	    	var row_num = document.getElementById("receive_details").rows.length*1; 

	    	if(row_num>0)
	    	{
	    		if( document.getElementById('txt_check_box_advance').checked==true)
				{
					for (var i =1; i<=row_num; i++) {

						var value = $("#txtReceiveQty_"+i).attr('placeholder')*1;

		    			$('#txtReceiveQty_'+i).val(value);

		    			var id = '#txtReceiveQty_'+i;

		    			calculate_receive_qty(id,value);
		    		}
				}
				else
				{
					for (var i =1; i<=row_num; i++) {
	    			
		    			var value= $('#txtReceiveQty_'+i).val()*1;

		    			$('#txtReceiveQty_'+i).val('');
		    		}
				}
	    		
	    	}
	    }
	</script>
</head>
<body>
	<div style="width:100%;" >
    	<?php echo load_freeze_divs ('../../', $permission); ?>
    	<div>
    		<form name="yarnstorereceive_1" id="yarnstorereceive_1" autocomplete="off">
	        	<fieldset style="width:1000px; margin: 0 auto;">
	        		<legend>Yarn Dyeing Store Receive</legend>
	            	<table width="1000" cellspacing="2" cellpadding="0" border="0">
	                     <tr>
	                        <td colspan="4" align="right">Receive ID</td>
	                        <td colspan="4" align="left">
	                            <input class="text_boxes" type="text" name="txt_receive_no" id="txt_receive_no" placeholder="Double Click" onDblClick="job_search_popup_rcv()" style="width:150px;" readonly />
	                            <input type="hidden" name="hdn_update_id" id="hdn_update_id">                            
	                        </td>
	                    </tr>
	                    <tr>
	                    	<td align="right" class="must_entry_caption">Receive Basis</td>
	                        <td> 
	                            <?php echo create_drop_down('cbo_receive_basis', 163, $receive_basis_arr, '', 1, '-- Select Receive Basis --',20, "fnc_change_challan_permission(this.value);","","20,21"); ?>
	                        </td>
	                        <td align="right" id="job_td" class="must_entry_caption">Job No</td>
	                        <td> 
	                            <input class="text_boxes" type="text" name="txt_job_no" id="txt_job_no" placeholder="Double Click For Job No" onDblClick="job_search_popup_job()" style="width:150px;" readonly />
	                        </td>
	                        <td align="right">Challan ID</td>
	                        <td> 
	                            <input class="text_boxes" type="text" readonly name="txt_challan_no" id="txt_challan_no" placeholder="Display Challan No" style="width:150px;" />
	                        </td>
	                        <td align="right" class="must_entry_caption">Receive Date</td>
	                        <td> 
	                            <input class="datepicker" type="text" name="txt_receive_date" id="txt_receive_date" placeholder="Write Receive Date" style="width:150px;" value="<? echo date('d-m-Y');?>" />
	                        </td>
	                    </tr>
	                    <tr>
	                    	<td align="right">Company Name</td>
	                        <td> 
	                            <?php echo create_drop_down('cbo_company_name', 163, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", 'id,company_name', 1, '-- Select Company --', $selected, "load_drop_down( 'yd_store_receive_controller', this.value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_party', 'party_td' );",1); ?>
	                        </td>
	                        <td align="right">Location Name</td>
	                        <td id="location_td"> 
	                            <?php echo create_drop_down('cbo_location_name', 163, $blank_array, '', 1, '-- Select Location --', $selected, "",1); ?>
	                        </td>
	                        <td align="right">Within Group</td>
	                        <td> 
	                            <?php echo create_drop_down('cbo_within_group', 163, $yes_no, '', 1, '-- Select Within Group --', $selected, "load_drop_down( 'yd_store_receive_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_party', 'party_td' );",1); ?>
	                        </td>
	                        <td align="right">Party Name</td>
	                        <td id="party_td"> 
	                            <?php echo create_drop_down('cbo_party_name', 163, $blank_array, '', 1, '-- Select Location --', $selected, "",1); ?>
	                        </td>
	                    </tr>
	                    <tr>
	                    	<td align="right">Party Location</td>
	                        <td id="party_location_td"> 
	                            <?php echo create_drop_down('cbo_party_location', 163, $blank_array, '', 1, '-- Select Location --', $selected, "",1); ?>
	                        </td>
	                        <td align="right">WO No</td>
	                        <td> 
	                            <input class="text_boxes" type="text" name="txt_wo_no" id="txt_wo_no" placeholder="Display" style="width:150px;" readonly />
	                        </td>
	                        <td align="right">Prod. Type</td>
	                        <td> 
	                            <?php echo create_drop_down('cbo_pro_type', 163, $w_pro_type_arr, '', 1, '-- Select Location --', $selected, "",1); ?>
	                        </td>
	                        <td align="right">Order Type</td>
	                        <td> 
	                            <?php echo create_drop_down('cbo_order_type', 163, $w_order_type_arr, '', 1, '-- Select Location --', $selected, "",1); ?>
	                        </td>
	                    </tr>
	                </table>
	        	</fieldset>
	        	<br>
	        	<fieldset style="width: 1300px; margin: 0 auto;">
	        		<legend>Yarn Dyeing Store Receive Details</legend>
	        		 <table cellpadding="0" cellspacing="0" border="0" width="1300" id="tbl_dtls_receive">
	        		 	<thead class="form_table_header">
	        		 		<th width="80" id="style_no_td">Style</th>
                            <th width="60" id="buyer_no_td">Buyer Job</th>
                            <th width="60" id="cus_buyer_no_td" >Cust. Buyer</th>
                            <th width="80" id="yd_batch_lot_no_td" >YD Batch/Lot</th>
                            <th width="80" id="grey_lot_no_td" >Raw Yarn Lot</th>
                            <th width="60" id="count_type_no_td">Count Type</th>
                            <th width="60" id="count_no_td" >Count</th>
                            <th width="40" id="yarn_type_no_td" >Yarn Type</th>
                            <th width="100" id="composition_no_td" >Yarn Composition</th>
                            <th width="40" id="color_no_td">Y/D Color</th>
                            <th width="40" id="no_of_bag_no_td">No of Bag</th>
                            <th width="50" id="cone_per_bag_no_td" >Cone Per Bag</th>
                            <th width="40" id="order_uom_no_td" >Order UOM</th>
                            <th width="50" id="order_qty_no_td" >Order Qty</th>
                            <th width="50" id="process_loss_no_td" >Process Loss %</th>
                            <th width="50" id="adj_type_no_td" >Adj. Type</th>
                            <th width="50" id="grey_qty_no_td" >Grey Qty</th>
                            <th width="50" id="previous_qty_no_td" >Previous Rcv</th>
                            <th width="50" id="balance_no_td" >Balance</th>
                            <th width="50" id="yd_eceive_no_td" class="must_entry_caption" >
                            	YD Rcv.
                           		<input type="checkbox" name="txt_check_box_advance" id="txt_check_box_advance" onclick="set_all_delivery();">
                        	</th>
	        		 	</thead>
	        		 	<tbody id="receive_details"> 	
		        		</tbody>
	        		 </table>
	        		 <table width="1300" cellspacing="2" cellpadding="0" border="0">
	                    <tr>
	                        <td align="center" valign="middle" class="button_container">
	                            <?php
									  echo load_submit_buttons( $permission, "fnc_store_receive_entry", 0,0,"fnResetForm();",1);

	                            ?>
	                        </td> 
	                    </tr>
	                </table>
	        	</fieldset>
        	</form>
        </div>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>