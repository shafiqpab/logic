<?
/*-------------------------------------------- Comments
Purpose			: 	Barcode Issue To Finishing		
Functionality	:	
JS Functions	:
Created by		:	Md. Helal Uddin
Creation date 	: 	05-03-2020
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

	require_once('../includes/common.php');
	extract($_REQUEST);

	$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
	echo load_html_head_contents("Grey Fabric Delivery Roll Wise", "../", 1, 1, $unicode, '', '');
	?>
	<script>

		if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";
		var permission = '<? echo $permission; ?>';
	
  
    $('#txt_bar_code_num').live('keydown', function (e) {
    	if (e.keyCode === 13)
		{
			if (form_validation('w_company_id*wc_location_id', 'Working Company * Working Company Location') == false)
            {
                return;
            }
                    
            var w_company_id = $('#w_company_id').val();
            var wc_location_id = $('#wc_location_id').val();
			
    		e.preventDefault();
    		var bar_code = $('#txt_bar_code_num').val();
    		create_row(bar_code);
    		//load_floor();
            $('#txt_bar_code_num').val("");
    	}
    });
    function fnc_company_check(val)
    {
        if(val==1)
        {
            if($("#w_company_id").val()==0)
            {
                alert("Please Select Company.");
                $("#cbo_knitting_source").val(0);
                $("#f_company_id").val(0);
                return;
            }
            else
            {
                get_php_form_data(document.getElementById('f_company_id').value,'production_process_control','requires/barcode_issue_to_finishing_controller' );
            }
        }
        else
        {
            get_php_form_data(document.getElementById('w_company_id').value,'production_process_control','requires/barcode_issue_to_finishing_controller' );
        }
    }
    function create_row(barcode_no,mst_id=0)
    {
       
        var row_num = $('#txt_tot_row').val();
        var w_company_id = $('#w_company_id').val();
        var wc_location_id = $('#wc_location_id').val();
        var barcode_nos = trim(barcode_no);
        
        scanned_barcode=[];
        var i=1;
        var msg=0;
        //var barcode_da = barcode_nos.split(",");
        $("#scanning_tbl").find('tbody tr').each(function() {
           
            var barcodeNo = $(this).find('input[name="barcodeNo[]"]').val();
            if(trim(barcodeNo) == barcode_nos){
                msg++;
                return;
            }
            
        });
        if(msg>0){
            alert("Barcode already scanned");
            return;
        }
        
        //var barcode_data = trim(return_global_ajax_value(barcode_nos, 'populate_barcode_data', '', 'requires/grey_feb_delivery_roll_wise_entry_controller'));
        var barcode_data = trim(return_global_ajax_value(barcode_nos+'**'+w_company_id+'**'+wc_location_id, 'populate_barcode_data', '', 'requires/barcode_issue_to_finishing_controller'));
        //console.log(barcode_data);
        if (barcode_data == 0)
        {
            alert('Barcode is Not Valid or already received');
            $('#messagebox_main', window.parent.document).fadeTo(100, 1, function () //start fading the messagebox
            {
                $('#messagebox_main', window.parent.document).html('Barcode is Not Valid.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
            });
            $('#txt_bar_code_num').val('');
            return;
        }

       
        var data = barcode_data.split("**");
        //console.log(data);
        var id = data[0];
        var po_break_down_id = data[1];
        var challan_no = data[2];
        var color_id = data[3];
        var color_type_id = data[4];
        var size_id = data[5];
        var country_id = data[6];
        var company_id = data[7];
        var location_id = data[8];
        var floor_id = data[9];
        var item_id = data[10];
        var line_id = data[11];
        var qrcode_year = data[12];
        var qrcode_suffix = data[13];
        var qrcode = data[14];
        //console.loge(qrcode);
        var production_date = data[15];
        var production_hour = data[16];
        var inserted_by = data[17];
        var insert_date = data[18];
        var job_no_mst = data[19];
        var internal_ref = data[20];
        var style_ref = data[21];
        var order_num = data[22];
        var item_name=data[23];
        var country_name=data[24];
        var color_name=data[25];
        var size_name=data[26];
        var buyer_id=data[27];
        var buyer_name=data[28];
        


            var is_barcode_scanned=return_global_ajax_value(barcode_nos,'check_if_barcode_scanned','','requires/barcode_issue_to_finishing_controller');
            var system_challan =is_barcode_scanned;
            //console.log(is_barcode_scanned);
           // return;

            if(system_challan=='') system_challan='Not Found';else system_challan=system_challan;
            if(is_barcode_scanned !=='' && mst_id==0){
                //alert('Sorry! Barcode Already Scanned.');
                alert('Barcode Already Scanned.\nChallan No : ' + system_challan);
                $('#txt_bar_code_num').val('');
                return;
            }



            var bar_code_no = $('#barcodeNo_' + row_num).val();
            if (bar_code_no == "")
            {
                // $('#cbo_company_id').val(company_id);
                // $('#cbo_knitting_source').val(knitting_source_id);
                // $('#txt_knit_company').val(knitting_company);
                // $('#knit_company_id').val(knitting_company_id);
                // $('#cbo_location_id').val(location_id);

                // get_php_form_data( company_id, 'company_wise_report_button_setting','requires/grey_feb_delivery_roll_wise_entry_controller' );
            }
            else
            {
                
                row_num++;
                $("#scanning_tbl tbody tr:first").clone().find("td,input,select").each(function () {
                    $(this).attr({
                        'id': function (_, id) {
                            var id = id.split("_");
                            return id[0] + "_" + row_num
                        },
                        'value': function (_, value) {
                            return value
                        }
                    });
                }).end().prependTo("#scanning_tbl");

                $("#scanning_tbl tbody tr:first").removeAttr('id').attr('id', 'tr_' + row_num);


                
            }

             $("#sl_" + row_num).text(row_num);
                
                $("#barcode_" + row_num).text(qrcode);
                $("#year_" + row_num).text(production_date);
                $("#job_" + row_num).text(job_no_mst);
                $("#buyer_" + row_num).text(buyer_name);
                $("#styleref_" + row_num).text(style_ref);
                $("#internalrefno_" + row_num).text(internal_ref);
                $("#order_" + row_num).text(order_num);
                $("#item_" + row_num).text(item_name);
                $("#country_" + row_num).text(country_name);
                $("#color_" + row_num).text(color_name);
                $("#size_" + row_num).text(size_name);
                $("#barcodeNo_" + row_num).val(qrcode);
                $('#txt_tot_row').val(row_num);
                $("#years_" + row_num).val(production_date);
                $("#buyerId_" + row_num).val(buyer_id);
                $("#orderId_" + row_num).val(po_break_down_id);
                $("#itemId_" + row_num).val(item_id);
                $("#colorId_" + row_num).val(color_id);
                $("#sizeId_" + row_num).val(size_id);
                $("#countryId_" + row_num).val(country_id);
  
            $('#decrease_' + row_num).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + row_num + ");");
        
    }
    function openmypage_challan()
    {
        var company_id=$('#w_company_id').val();
        console.log(company_id);
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/barcode_issue_to_finishing_controller.php?action=challan_popup&company_id='+company_id, 'Challan Popup', 'width=980px,height=350px,center=1,resize=1,scrolling=0', '')
        emailwindow.onclose = function () {

            var theform = this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
            var hidden_data = this.contentDoc.getElementById("hidden_data").value;   //challan Id and Number
            var barcode_nos = this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode No
            //console.log(hidden_data);
            //console.log(barcode_nos);
            //return;

            if (hidden_data != "") {
                fnc_reset_form();
                var challan_data = hidden_data.split("**");
                //console.log(challan_data);
                $('#update_id').val(challan_data[0]);
                $('#txt_challan_no').val(challan_data[1]);
                $('#txt_challan_date').val(challan_data[2]);
                $('#w_company_id').val(challan_data[3]);
                $('#wc_location_id').val(challan_data[4]);
                $('#cbo_knitting_source').val(challan_data[5]);
                $('#f_company_id').val(challan_data[6]);
                $('#fc_location_id').val(challan_data[8]);
                $('#fc_floor_id').val(challan_data[9]);
                //$('#txt_challan_no').attr('disabled', 'disabled');
                $('#w_company_id').attr('disabled', 'disabled');
                $('#wc_location_id').attr('disabled', 'disabled');
                $('#f_company_id').attr('disabled', 'disabled');
                $('#fc_location_id').attr('disabled', 'disabled');
                $('#fc_floor_id').attr('disabled', 'disabled');
                $('#cbo_knitting_source').attr('disabled', 'disabled');
                $('#txt_challan_date').attr('disabled', 'disabled');
                
                
              

               

            var barcode_upd=barcode_nos.split(",");
             for(var k=0; k<barcode_upd.length; k++)
             {
                 create_row(barcode_upd[k],challan_data[0]);
             }
            set_button_status(1, permission, 'save_update_delete', 1);
         }
     }
 }
    
     function save_update_delete(operation)
    {
        
       
        
        if(operation==2){
            alert('Delete Operation is not allowed');
            return ;
        }
        if (form_validation('w_company_id*wc_location_id*f_company_id', 'Working Company*Working Location*Finishing Company') == false)
        {
                return;
        }
       
        var cbo_knitting_source = $('#cbo_knitting_source').val();
        

       
        var j = 0;
        var dataString = '';
        $("#scanning_tbl").find('tbody tr').each(function () {
           
            var barcodeNo = $(this).find('input[name="barcodeNo[]"]').val();
            var jobNo = $(this).find('input[name="jobNo[]"]').val();
            var year = $(this).find('input[name="year[]"]').val();
            var buyerId = $(this).find('input[name="buyerId[]"]').val();
            var orderId = $(this).find('input[name="orderId[]"]').val();
            var itemId = $(this).find('input[name="itemId[]"]').val();
            var colorId = $(this).find('input[name="colorId[]"]').val();
            var sizeId = $(this).find('input[name="sizeId[]"]').val();
            var countryId = $(this).find('input[name="countryId[]"]').val();

            
           
            //alert(prodQty);
            try {
               
                j++;

                dataString +='&barcodeNo_' + j + '=' + barcodeNo + '&jobNo_' + j + '=' + jobNo + '&year_' + j + '=' + year + '&buyerId_' + j + '=' + buyerId + '&orderId_' + j + '=' + orderId + '&itemId_' + j + '=' + itemId + '&colorId_' + j + '=' + colorId + '&sizeId_' + j + '=' + sizeId + '&countryId_' + j + '=' + countryId ;
            }
            catch (e) {
                //got error no operation
            }
        });

        if (j < 1) {
            alert('No data');
            return;
        }

        
        var data = "action=save_update_delete&operation=" + operation + '&tot_row=' + j + get_submitted_data_string('txt_challan_date*txt_challan_no*w_company_id*wc_location_id*cbo_knitting_source*f_company_id*fc_location_id*fc_floor_id*update_id', "../") + dataString;
        freeze_window(operation);

        http.open("POST", "requires/barcode_issue_to_finishing_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = save_update_reply_info;
    }
     function save_update_reply_info()
    {
        if (http.readyState == 4) {
            //release_freezing();return;
            //console.log(http.responseText);
            release_freezing();
            //return;
            var response = trim(http.responseText).split('**');

            show_msg(response[0]);
            if (response[0] == 11) {
                alert(response[1]);
               
                release_freezing();
                return;
            }
            if(response[0]==6){
                alert('There is an error');
               
                release_freezing();
                return;
            }
            if ((response[0] == 0 || response[0] == 1)) {
                document.getElementById('update_id').value = response[1];
                document.getElementById('txt_challan_no').value = response[2];
               // $('#txt_challan_no').attr('disabled', 'disabled');
                $('#w_company_id').attr('disabled', 'disabled');
                $('#wc_location_id').attr('disabled', 'disabled');
                $('#f_company_id').attr('disabled', 'disabled');
                $('#fc_location_id').attr('disabled', 'disabled');
                $('#fc_floor_id').attr('disabled', 'disabled');
                $('#cbo_knitting_source').attr('disabled', 'disabled');
                $('#txt_challan_date').attr('disabled', 'disabled');
             //  document.getElementById('w_company_id').classList.add("disabled");
             //  document.getElementById('txt_challan_date').classList.add("disabled");
                // $('#txt_challan_no').attr('disabled', 'disabled');
                // $('#txt_challan_date').atrr('disabled', 'disabled');
                // $('#w_company_id').attr('disabled', 'disabled');
                // $('#wc_location_id').attr('disabled', 'disabled');
                // $('#f_company_id').attr('disabled', 'disabled');
                // $('#wc_location_id').atrr('disabled', 'disabled');
                // $('#fc_floor_id').atrr('disabled', 'disabled');
                // $('#cbo_knitting_source').atrr('disabled', 'disabled');
                // $('#cbo_knitting_source').attr('readonly', true);
                // document.getElementById("txt_challan_no").readOnly = true; 
                 
                
               
                //add_dtls_data(response[3]);
                set_button_status(1, permission, 'save_update_delete', 1);
            }
            release_freezing();
        }
    }
   
    
   

    function fnc_reset_form()
	{
    	$('#scanning_tbl tbody tr').remove();

    	var html = '<tr id="tr_1" align="center" valign="middle"><td width="30" id="sl_1"></td><td width="80" id="barcode_1"></td><td width="100" id="year_1"></td><td width="85" id="job_1"></td><td width="75" id="buyer_1"></td><td width="75" id="styleref_1"></td><td width="100" id="internalref_no_1"></td><td width="80" id="order_1"></td><td width="120" id="item_1"></td><td width="50" id="country_1"></td><td width="40" id="color_1" ></td><td width="110" id="size_1"></td><td width="100" id="remove_1"><input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/><input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/><input type="hidden" name="year[]" id="years_1"/><input type="hidden" name="jobNo[]" id="jobNo_1"/><input type="hidden" name="buyerId[]" id="buyerId_1"/><input type="hidden" name="styleRef[]" id="styleRef_1"/><input type="hidden" name="internalRefNo[]" id="internalRefNo"/><input type="hidden" name="orderId[]" id="orderId_1"/><input type="hidden" name="itemId[]" id="itemId_1"/><input type="hidden" name="countryId[]" id="countryId_1"/><input type="hidden" name="colorId[]" id="colorId_1"/><input type="hidden" name="sizeId[]" id="sizeId_1"  /></td></tr>';
        //alert(html);
    	$("#scanning_tbl tbody").html(html);
        
    	$('#w_company_id').val(0).removeAttr("disabled");
        $('#cbo_knitting_source').removeAttr("disabled");
    	// $('#wc_location_id').val(0).removeAttr("disabled");
    	// $('#f_company_id').val(0).removeAttr("disabled");
    	// $('#fc_location_id').val(0).removeAttr("disabled");
    	// $('#fc_floor_id').val(0).removeAttr("disabled");
    	$('#txt_tot_row').val(1);
    	$('#update_id').val('');
    	$('#txt_challan_no').val('').removeAttr("disabled");
     

    	
        set_button_status(0, permission, 'save_update_delete', 1);
    }
   function fn_deleteRow(rid)
   {
            var num_row = $('#scanning_tbl tbody tr').length;
            var barcode_no=$('#barcodeNo_'+rid).val();
             var is_barcode_receive=return_global_ajax_value(barcode_no,'check_if_barcode_receive','','requires/barcode_issue_to_finishing_controller');
             console.log(is_barcode_receive);
             if(is_barcode_receive=='')
             {
                if (num_row == 1)
                {
                    $('#tr_' + rid + ' td:not(:nth-last-child(2)):not(:last-child)').each(function (index, element) {
                        $(this).html('');
                    });

                    $('#tr_' + rid).find(":input:not(:button)").val('');
                    
                }
                else
                {
                    $("#tr_" + rid).remove();
                }  
             }
             else
             {
                alert('Remove not allowed. Barcode Already received . Mrr no is '+is_barcode_receive);
                return;
             }
                 
   }
	
  
</script>
</head>
<body onLoad="set_hotkey();">
	<div align="center" style="width:100%;">
		<? echo load_freeze_divs("../", $permission); ?>
		<form name="rollscanning_1" id="rollscanning_1" autocomplete="off">
			<fieldset style="width:870px;">
				<legend>Roll Scanning</legend>
				<table cellpadding="0" cellspacing="2" width="850">
					<tr>
                        <input type="hidden" name="hidden_variable_cntl" id="hidden_variable_cntl">
                            <input type="hidden" name="hidden_preceding_process" id="hidden_preceding_process">
						<td colspan="3" align="right" width="100">Challan No</td>
						<td colspan="3">
								<input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes"
								style="width:120px;" onDblClick="openmypage_challan()"
								placeholder="Browse For Challan No" readonly/>
								<input type="hidden" name="update_id" id="update_id"/>
						</td>
						<td></td>
						<td></td>
					</tr>
					<tr>	
						<td align="right" width="70">Working Company</td>
						<td >     
							<?
    						      echo create_drop_down("w_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name", "id,company_name", 1, "--Display--", 0, "load_drop_down( 'requires/barcode_issue_to_finishing_controller', this.value, 'load_drop_down_working_location', 'wc_location_td' );",0);//
    						?>
					   </td>
					   <td  align="right" class="must_entry_caption" width="70">WC. Location</td>
    					<td id="wc_location_td">
    						<?
    						echo create_drop_down("wc_location_id", 130, "select id, location_name from lib_location", "id,location_name", 1, "--Display--", 0, "", 1);
    						?>
    					</td>
					   <td align="right" width="70"> Source</td>
    					<td>
    						

                             <?
                                echo create_drop_down( "cbo_knitting_source", 130, $knitting_source,"", 1, "-- Select Source --", $selected, "fnc_company_check(this.value);load_drop_down( 'requires/barcode_issue_to_finishing_controller', this.value+'**'+$('#w_company_id').val(), 'load_drop_down_sewing_input', 'f_company_td' );", 0, '1,3' );
                                ?> 
    					</td>
    					<td></td>
    					<td></td>
				    </tr>
    				<tr>
    					<td align="right" width="70">Fini. Company</td>
    					<td id="f_company_td">
    								<?
    						echo create_drop_down("f_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name", "id,company_name", 1, "--Display--", 0, "", 1);//$company_cond
    						?>
    					</td>
    					<td align="right" class="must_entry_caption" width="70">F.C. Location</td>
    					<td id="fc_location_td">
    						<?
    						echo create_drop_down("fc_location_id", 130, "select id, location_name from lib_location", "id,location_name", 1, "--Display--", 0, "", 1);
    						?>
    					</td>
    					<td align="right">Fini. Floor</td>
    					<td id="fc_floor_td">
    						<?
    						echo create_drop_down("fc_floor_id", 130, "select id, floor_name from lib_prod_floor", "id,floor_name", 1, "--Display--", 0, "", 1);
    						?>
    					</td>
    					<td align="right" class="must_entry_caption" width="70">CH. Date</td>
    					<td width="130">
    						<input type="text" name="txt_challan_date" id="txt_challan_date"
    							class="datepicker"  value="<? echo date("d-m-Y"); ?>" readonly="1"/>
    					</td>
    				</tr>
    				
    				<tr>
    					<td ></td>
                        <td colspan="2" align="right"><strong>Barcode Number</strong></td>
                        <td colspan="2"><input type="text" name="txt_bar_code_num" id="txt_bar_code_num" class="text_boxes_numeric" style="width:120px;"  placeholder="Write/Scan"/></td>
                        <td ></td>
                        <td ></td>
                        <td ></td>
    				</tr>
			    </table>
		    </fieldset>
		    <br>
    		<fieldset style="width:1080px;text-align:left">
    			<style>
            			#scanning_tbl tr td {
            				background-color: #FFF;
            				color: #000;
            				border: 1px solid #666666;
            				line-height: 12px;
            				height: 20px;
            				overflow: auto;
            			}
    		    </style>
            
                <div style="width:1070px; max-height:250px; overflow-y:scroll" align="left">
            		<table cellpadding="0" width="1050" cellspacing="0" border="1" id="scanning_tbl" class="rpt_table" rules="all">
                        <thead>
                            <tr>
                                <th width="30">SL</th>
                                <th width="80">QR Code</th>
                                <th width="100">Year</th>
                                <th width="85">Job No</th>
                                <th width="75">Buyer</th>
                                <th width="75">Style Reff.</th>
                                <th width="100">Internal Reff.</th>
                                <th width="80">Order No.</th>
                                <th width="120">Gmts. Item</th>
                                <th width="50">Country</th>
                                <th width="40">Color</th>
                                <th width="110">GMT Size</th>
                                <th width="100">Remove</th>
                            </tr>
                        </thead>
                		<tbody>
                			<tr id="tr_1" align="center" valign="middle">
                				<td width="30" id="sl_1"></td>
                				<td width="80" id="barcode_1"></td>
                				<td width="100" id="year_1"></td>
                				<td width="85" id="job_1"></td>
                				<td width="75" id="buyer_1"></td>
                				<td width="75" id="styleref_1"></td>
                				<td width="100" id="internalrefno_1"></td>
                				<td width="80" id="order_1"></td>
                				<td width="120" id="item_1"></td>
                				<td width="50" id="country_1"></td>
                				<td width="40" id="color_1" ></td>
                                <td width="110" id="size_1"></td>
                				<td width="100" id="remove_1">
                                    <input type="button" id="decrease_1" name="decrease[]" style="width:30px"
                                    class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/>
                                    <input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/>
                                    <input type="hidden" name="year[]" id="years_1"/>
                                    <input type="hidden" name="jobNo[]" id="jobNo_1"/>
                                    <input type="hidden" name="buyerId[]" id="buyerId_1"/>
                                    <input type="hidden" name="styleRef[]" id="styleRef_1"/>
                                    <input type="hidden" name="internalRefNo[]" id="internalRefNo"/>
                                    <input type="hidden" name="orderId[]" id="orderId_1"/>
                                    <input type="hidden" name="itemId[]" id="itemId_1"/>
                                    <input type="hidden" name="countryId[]" id="countryId_1"/>
                                    <input type="hidden" name="colorId[]" id="colorId_1"/>
                                    <input type="hidden" name="sizeId[]" id="sizeId_1"  />            
                                </td>
                				
                			</tr>
                		</tbody>
            	   </table>
                </div>
                <br>
                <table width="1050" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                	<tr>
                		<td align="center" class="button_container">
                			<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
                			<input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes" value="">
                			<input type="hidden" name="txt_deleted_roll_id" id="txt_deleted_roll_id" class="text_boxes"
                			value="">
                            <input type="hidden" name="txt_deleted_barcode" id="txt_deleted_barcode" class="text_boxes" value="">
                			<?
                			echo load_submit_buttons($permission, "save_update_delete", 0, "", "fnc_reset_form()", 1);
                			?>

                		</td>
                	</tr>
                </table>
            </fieldset>
        </form>
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>