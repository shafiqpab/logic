<?
/*-------------------------------------------- Comments
Purpose			: 	Barcode Issue To Finishing		
Functionality	:	
JS Functions	:
Created by		:	Md. Helal Uddin
Creation date 	: 	07-03-2020
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
		var scanned_barcode = new Array();
		<?
		$scanned_barcode_array = array();
	

	$composition_arr = array(); $constructtion_arr = array();
	$sql_deter = "select a.id as ID, a.construction as CONSTRUCTION, b.copmposition_id as COPMPOSITION_ID, b.percent as PERCENT from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		$constructtion_arr[$row['ID']] = $row['CONSTRUCTION'];
		$composition_arr[$row['ID']] .= $composition[$row['COPMPOSITION_ID']] . " " . $row['PERCENT'] . "% ";
	}
	unset($data_array);
	$jsconstructtion_arr = json_encode($constructtion_arr);
	echo "var constructtion_arr = " . $jsconstructtion_arr . ";\n";

	$jscomposition_arr = json_encode($composition_arr);
	echo "var composition_arr = " . $jscomposition_arr . ";\n";

	?>
	function load_scanned_barcode()
	{
		scanned_barcode = new Array();
		var scanned_barcode_nos = trim(return_global_ajax_value('', 'load_scanned_barcode_nos', '', 'requires/grey_feb_delivery_roll_wise_entry_controller'));
		scanned_barcode = eval(scanned_barcode_nos);

		set_button_status(0, permission, 'fnc_grey_delivery_roll_wise', 1);
	}
    function openmypage_mrr()
    {
        var company_id=$('#f_company_id').val();
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/barcode_receive_by_finishing_controller.php?action=mrr_popup&company_id='+company_id, 'MRR Popup', 'width=980px,height=350px,center=1,resize=1,scrolling=0', '')
        emailwindow.onclose = function () {

            var theform = this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
            var hidden_data = this.contentDoc.getElementById("hidden_data").value;   //challan Id and Number
            var barcode_nos = this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode No
            //console.log(hidden_data);
           // console.log(barcode_nos);
           // return;

            if (hidden_data != "") {
                fnc_reset_form();
                var mrr_data = hidden_data.split("**");
                console.log(mrr_data);
                $('#update_id').val(mrr_data[0]);
                $('#txt_mrr_no').val(mrr_data[1]);
                $('#s_company_id').val(mrr_data[2]);
                $('#sc_location_id').val(mrr_data[3]);
                $('#cbo_knitting_source').val(mrr_data[4]);
                $('#f_company_id').val(mrr_data[5]);
                $('#fc_location_id').val(mrr_data[7]);
                $('#fc_floor_id').val(mrr_data[8]);
                //$('#txt_challan_no').attr('disabled', 'disabled');
                $('#s_company_id').attr('disabled', 'disabled');
                $('#sc_location_id').attr('disabled', 'disabled');
                $('#cbo_knitting_source').attr('disabled', 'disabled');
                $('#f_company_id').attr('disabled', 'disabled');
                $('#fc_location_id').attr('disabled', 'disabled');
                $('#fc_floor_id').attr('disabled', 'disabled');
                
                
                
              

               

            var barcode_upd=barcode_nos.split(",");
             for(var k=0; k<barcode_upd.length; k++)
             {
                 create_row(barcode_upd[k],mrr_data[0]);
             }
            set_button_status(1, permission, 'save_update_delete', 1);
         }
     }
    }
	


   

    function save_update_delete(operation)
	{
    	if (operation == 2) {
    		show_msg('13');
    		return;
    	}

    	
    	
    	
    	
       

        var cbo_knitting_source = $('#cbo_knitting_source').val();
        if(cbo_knitting_source == 3){
        	if (form_validation('f_company_id*fc_location_id*cbo_knitting_source*s_company_id', 'Finishing Company*Finishing Location*Source*Skew Company') == false) {
        		return;
        	}
        }
		else{
        	if (form_validation('f_company_id*fc_location_id*cbo_knitting_source*s_company_id*sc_location_id', 'Finishing Company*Finishing Location*Source*Sew Company*Sew Location') == false) {
        		return;
        	}
        }

       
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
            var flooId = $(this).find('input[name="flooId[]"]').val();
            var lineId = $(this).find('input[name="lineId[]"]').val();
            var issueChallanNo = $(this).find('input[name="issueChallanNo[]"]').val();
            


            try {
               
                j++;

                dataString +='&barcodeNo_' + j + '=' + barcodeNo + '&jobNo_' + j + '=' + jobNo + '&year_' + j + '=' + year + '&buyerId_' + j + '=' + buyerId + '&orderId_' + j + '=' + orderId + '&itemId_' + j + '=' + itemId + '&colorId_' + j + '=' + colorId + '&sizeId_' + j + '=' + sizeId + '&countryId_' + j + '=' + countryId + '&lineId_' + j + '=' + lineId ;
            }
            catch (e) {
                
            }
        });

        if (j < 1) {
            alert('No data');
            return;
        }

        
        var data = "action=save_update_delete&operation=" + operation + '&tot_row=' + j + get_submitted_data_string('f_company_id*fc_location_id*fc_floor_id*s_company_id*cbo_knitting_source*sc_location_id*update_id*txt_mrr_no', "../") + dataString;
        freeze_window(operation);

        

        http.open("POST", "requires/barcode_receive_by_finishing_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_save_update_delete_Reply_info;
    }

    function fnc_save_update_delete_Reply_info()
	{
    	if (http.readyState == 4) {
            release_freezing();

            // console.log(http.responseText);
            // return;
            var response = trim(http.responseText).split('**');
            //console.log(response);
            if (response[0] == 11 || response[0]==121) {
            	alert(response[1]);
            	/*var update_id = document.getElementById('update_id').value;
            	var html = trim(return_global_ajax_value(update_id, 'populate_barcode_data_update', '', 'requires/grey_feb_delivery_roll_wise_entry_controller'));
            	if (html != "") {
            		$("#scanning_tbl tbody").html(html);
            		var num_row = $('#scanning_tbl tbody tr').length;
            		$('#txt_tot_row').val(num_row);
            	}*/
            	release_freezing();
            	return;
            }
            show_msg(response[0]);
            if ((response[0] == 0 || response[0] == 1)) {
            	//document.getElementById('update_id').value = response[1];
            	//document.getElementById('txt_mrr_no').value = response[2];

            	$('#txt_mrr_no').val(response[2]);
                $('#update_id').val(response[1]);
                $('#f_company_id').attr('disabled', 'disabled');
                $('#fc_location_id').attr('disabled', 'disabled');
                $('#s_company_id').attr('disabled', 'disabled');
                $('#sc_location_id').attr('disabled', 'disabled');
                $('#fc_floor_id').attr('disabled', 'disabled');
                $('#cbo_knitting_source').attr('disabled', 'disabled');
                
            	set_button_status(1, permission, 'save_update_delete', 1);
            }
            release_freezing();
        }
    }

    function create_row(barcode_no,mst_id=0)
    {
       
        var row_num = $('#txt_tot_row').val();
        var f_company_id = $('#f_company_id').val();
        var fc_location_id = $('#fc_location_id').val();
        var fc_floor_id = $('#fc_floor_id').val();
        //alert(barcode_no);
       // return;
        var barcode_nos = trim(barcode_no);
        //alert(barcode_nos);
        //return;
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
        var barcode_data = trim(return_global_ajax_value(barcode_nos+'**'+f_company_id+"**"+mst_id, 'populate_barcode_data', '', 'requires/barcode_receive_by_finishing_controller'));
        //console.log(barcode_data);

        if (barcode_data == 0)
        {
            alert('Barcode is Not Valid');
            $('#messagebox_main', window.parent.document).fadeTo(100, 1, function () //start fading the messagebox
            {
                $('#messagebox_main', window.parent.document).html('Barcode is Not Valid.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
            });
            $('#txt_bar_code_num').val('');
            return;
        }else if(barcode_data=="Already Received"  && mst_id==0){
            alert(barcode_data);
            return;
        }

       
        var data = barcode_data.split("**");
        //console.log(data);
        //return;
        var qrcode = data[0];
        var challan_no = data[1];
        var company_id = data[2];
        var company_name = data[3];
        var location_id = data[4];
        var location_name = data[5];
        var floor_id = data[6];
        var floor_name = data[7];
        var line_id = data[8];
        var line_name = data[9];
        var year = data[10];
        var job_no_mst = data[11];
        var buyer_id=data[12];
        var buyer_name=data[13];
        var style_ref = data[14];
        var internal_ref = data[15];
        var po_break_down_id = data[16];
        var order_num = data[17];
        var item_id = data[18];
        var item_name=data[19];
        var country_id = data[20];
        var country_name=data[21];
        var color_id = data[22];
        var color_name=data[23];
        var size_id = data[24];
        var size_name=data[25];
        var finishing_company_id=data[26];
        var finishing_location_id=data[27];
        var fining_source=data[28];
        //console.log(fining_source);
        if(fining_source!=1){
            alert('Out Bount Subcontact Source not allowed');
            return;
        }

        var f_company_id=$("#f_company_id").val();
        var fc_location_id=$("#fc_location_id").val();
        var s_company_id=$("#s_company_id").val();
        var sc_location_id=$("#sc_location_id").val();
        var cbo_knitting_source=$("#cbo_knitting_source").val();

        if(f_company_id==0|| s_company_id ==0 || cbo_knitting_source==0 ||sc_location_id==0 || fc_location_id==0){
            $('#f_company_id').val(company_id).attr("disabled",true);
            $('#fc_location_id').val(location_id).attr("disabled",true);
            $('#s_company_id').val(finishing_company_id).attr("disabled",true);
            $('#sc_location_id').val(finishing_location_id).attr("disabled",true);
            $('#cbo_knitting_source').val(1).attr("disabled",true);
            $('#fc_floor_id').val(floor_id).attr("disabled",true);

        }else{
            if(f_company_id!=company_id || fc_location_id!=location_id || s_company_id!= finishing_company_id || finishing_location_id!=sc_location_id){
                alert('Multiple Company and location not allowed');
                return;
            }
        }

            var is_barcode_scanned=return_global_ajax_value(barcode_nos,'check_if_barcode_scanned','','requires/barcode_receive_by_finishing_controller');
            var system_challan =is_barcode_scanned;
            if(system_challan=='') system_challan='Not Found';else system_challan=system_challan;
            if(is_barcode_scanned !=='' && mst_id==0){
               
                alert('Barcode Already Scanned.\nMRR No : ' + system_challan);
                $('#txt_bar_code_num').val('');
                return;
            }



            var bar_code_no = $('#barcodeNo_' + row_num).val();
            if (bar_code_no == "")
            {
               
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
                $("#barcode_"+row_num).text(qrcode);
                $("#issuechallanno_"+row_num).text(challan_no);
                $("#skewcompany_"+row_num).text(company_name);
                $("#skewcomlocation_"+row_num).text(location_name);
                $("#floor_"+row_num).text(floor_name);
                $("#lineno_"+row_num).text(line_name);
                $("#year_"+row_num).text(year);
                $("#jobno_"+row_num).text(job_no_mst);
                $("#buyer_"+row_num).text(buyer_name);
                $("#stylereff_"+row_num).text(style_ref);
                $("#internalreff_"+row_num).text(internal_ref);
                $("#order_"+row_num).text(order_num);
                $("#itme_"+row_num).text(item_name);
                $("#country_"+row_num).text(country_name);
                $("#color_"+row_num).text(color_name);
                $("#size_"+row_num).text(size_name);
                $("#barcodeNo_" + row_num).val(qrcode);
                $('#txt_tot_row').val(row_num);
                 $("#years_" + row_num).val(year);
                 $("#buyerId_" + row_num).val(buyer_id);
                 $("#orderId_" + row_num).val(po_break_down_id);
                 $("#itemId_" + row_num).val(item_id);
                 $("#lineId_" + row_num).val(line_id);
                 $("#colorId_" + row_num).val(color_id);
                 $("#sizeId_" + row_num).val(size_id);
                 $("#countryId_" + row_num).val(country_id);
  
            $('#decrease_' + row_num).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + row_num + ");");
            
        
    }

    $('#txt_bar_code_num').live('keydown', function (e) {
    	if (e.keyCode === 13)
		{
			//for barcode type
			if (form_validation('f_company_id', 'Finishing Company') == false)
			{
				return;
			}
            
			
    		e.preventDefault();
    		var bar_code = $('#txt_bar_code_num').val();
    		create_row(bar_code);
            $('#txt_bar_code_num').val("");
    	}
    });

  
    

   function fn_deleteRow(rid)
   {
           var num_row = $('#scanning_tbl tbody tr').length;
         
            
            if (num_row == 1)
            {
                $('#tr_' + rid + ' td:not(:nth-last-child(2)):not(:last-child)').each(function (index, element) {
                    $(this).html('');
                });

                $('#tr_' + rid).find(":input:not(:button)").val('');
                $('#f_company_id').val(0).removeAttr("disabled");
                $('#fc_location_id').val(0);
                $('#s_company_id').val(0);
                $('#sc_location_id').val(0);
                $('#cbo_knitting_source').val(0);
                
            }
            else
            {
                $("#tr_" + rid).remove();
            }       
   }
    function check_qty(rid)
	{
    	var production_qty = $("#prodQty_" + rid).text() * 1;
    	var roll_delv_qty = $("#currentDelivery_" + rid).val() * 1;
    	if (roll_delv_qty > production_qty) {
    		alert("Delivery Quantity Exceeds Production Quantity.");
    		$("#currentDelivery_" + rid).val(production_qty.toFixed(2));
    		return;
    	}
    }

    function fnc_reset_form()
    {
        $('#scanning_tbl tbody tr').remove();

        var html = '<tr id="tr_1" align="center" valign="middle"><td  id="sl_1"></td><td  id="barcode_1"></td><td id="issuechallanno_1"></td><td  id="skewcompany_1"></td><td id="skewcomlocation_1"></td><td  id="floor_1"></td><td id="lineno_1"></td><td  id="year_1"></td><td  id="jobno_1"></td><td  id="buyer_1"></td><td  id="stylereff_1" align="center"></td><td  id="internalreff_1"></td><td  id="order_1"></td><td  id="itme_1"></td><td  id="country_1"></td><td  id="color_1"></td><td  id="size_1"></td><td  id="remove_1"><input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/><input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/><input type="hidden" name="issueChallanNo[]" id="issueChallanNo_1"/><input type="hidden" name="skewCompany[]" id="skewCompany_1"/><input type="hidden" name="skewCompanyLocation[]" id="skewCompanyLocation_1"/><input type="hidden" name="flooId[]" id="floorId_1"/><input type="hidden" name="lineId[]" id="lineId_1"/><input type="hidden" name="year[]" id="years_1"/><input type="hidden" name="jobNo[]" id="jobNo_1"/><input type="hidden" name="buyerId[]" id="buyerId_1"/><input type="hidden" name="styleRef[]" id="styleRef_1"/><input type="hidden" name="internalRefNo[]" id="internalRefNo"/><input type="hidden" name="orderId[]" id="orderId_1"/><input type="hidden" name="itemId[]" id="itemId_1"/><input type="hidden" name="countryId[]" id="countryId_1"/><input type="hidden" name="colorId[]" id="colorId_1"/><input type="hidden" name="sizeId[]" id="sizeId_1"  /></td></tr>';
        //console.log(html);
        //alert(html);
        $("#scanning_tbl tbody").html(html);
        
        $('#f_company_id').val(0).removeAttr("disabled");
        $('#fc_location_id').val(0).attr('disabled',true);
        $('#fc_floor_id').val(0).attr('disabled',true);
        $('#s_company_id').val(0).attr('disabled',true);
        $('#sc_location_id').val(0).attr('disabled',true);
        $('#cbo_knitting_source').removeAttr("disabled");
        // $('#wc_location_id').val(0).removeAttr("disabled");
        // $('#f_company_id').val(0).removeAttr("disabled");
        // $('#fc_location_id').val(0).removeAttr("disabled");
        // $('#fc_floor_id').val(0).removeAttr("disabled");
        $('#txt_tot_row').val(1);
        $('#update_id').val('');
        $('#txt_mrr_no').val('').removeAttr("disabled");
     

        
        set_button_status(0, permission, 'save_update_delete', 1);
    }
	
    
    function fnc_company_check(val)
    {
        if(val==1)
        {
            if($("#f_company_id").val()==0)
            {
                alert("Please Select Company.");
                $("#cbo_knitting_source").val(0);
                $("#s_company_id").val(0);
                return;
            }
            else
            {
                get_php_form_data(document.getElementById('s_company_id').value,'production_process_control','requires/barcode_receive_by_finishing_controller' );
            }
        }
        else
        {
            get_php_form_data(document.getElementById('f_company_id').value,'production_process_control','requires/barcode_receive_by_finishing_controller' );
        }
    }
</script>
</head>
<body onLoad="set_hotkey();">
	<div align="center" style="width:100%;">
		<? echo load_freeze_divs("../", $permission); ?>
		<form name="rollscanning_1" id="rollscanning_1" autocomplete="off">
			<fieldset style="width:870px;">
				<legend>Roll Scanning test</legend>
				<table cellpadding="0" cellspacing="2" width="850">
					<tr>
						<td colspan="3" align="right" width="100">MRR No</td>
						<td colspan="3">
								<input type="text" name="txt_mrr_no" id="txt_mrr_no" class="text_boxes"
								style="width:120px;" onDblClick="openmypage_mrr()"
								placeholder="Browse MRR NO " readonly/>
								<input type="hidden" name="update_id" id="update_id"/>
						</td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						
							
						<td align="right" width="70">Finishing Company</td>
							<td >
								<?
						echo create_drop_down("f_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name", "id,company_name", 1, "--Display--", 0, "load_drop_down( 'requires/barcode_receive_by_finishing_controller', this.value, 'load_drop_down_finishing_location', 'fc_location_td' );", 0);//$company_cond
						?>
					</td>
					<td  align="right" class="must_entry_caption" width="70">F.C Location</td>
					<td id="fc_location_td">
						<?
						echo create_drop_down("fc_location_id", 130, "select id, location_name from lib_location", "id,location_name", 1, "--Display--", 0, "", 1);
						?>
					</td>
					<td align="right" width="70"> Source</td>
					<td>
						<?
                                echo create_drop_down( "cbo_knitting_source", 130, $knitting_source,"", 1, "-- Select Source --", $selected, "fnc_company_check(this.value);load_drop_down( 'requires/barcode_receive_by_finishing_controller', this.value+'**'+$('#w_company_id').val(), 'load_drop_down_sewing_input', 's_company_td' );", 0, '1,3' );
                                ?> 
					</td>
					<td></td>
					<td></td>


				</tr>
				<tr>
					<td align="right" width="70">Sew. Company</td>
					<td id="s_company_td">
								<?
						echo create_drop_down("s_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name", "id,company_name", 1, "--Display--", 0, "", 1);//$company_cond
						?>
					</td>
					<td align="right" class="must_entry_caption" width="70">Sew. Location</td>
					<td id="sc_location_td">
						<?
						echo create_drop_down("sc_location_id", 130, "select id, location_name from lib_location", "id,location_name", 1, "--Display--", 0, "", 1);
						?>
					</td>
					<td align="right">Fini. Floor</td>
					<td id="fc_floor_td">
						<?
						echo create_drop_down("fc_floor_id", 130, "select id, floor_name from lib_prod_floor", "id,floor_name", 1, "--Display--", 0, "", 1);
						?>
					</td>
                   <td></td>
                   <td></td>
					
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
		<fieldset style="width:1050px;text-align:left">
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
        <table cellpadding="0" width="1030" cellspacing="0" border="1" id="scanning_tbl" class="rpt_table"  rules="all">
            <thead>
                <th width="30">SL</th>
                <th width="50">QR Code</th>
                <th width="60">Issue Ch. No</th>
                <th width="80">Sew. Com</th>
                <th width="80">Sew. Com. Location</th>
                <th width="50">Floor</th>
                <th width="60">Line No.</th>
                <th width="40">Year</th>
                <th width="65">Job No</th>
                <th width="60">Buyer</th>
                <th width="60">Style Reff.</th>
                <th width="65">Internal Reff.</th>
                <th width="50">Order No</th>
                <th width="60">Gmts. Item</th>
                <th width="65">Country</th>
                <th width="50">Color</th>
                <th width="40">GMT Size</th>
                <th >Remove</th>
               
            </thead>
        
       
    		
        		<tbody >
        			<tr id="tr_1" align="center" valign="middle">
        				<td  id="sl_1"></td>
        				<td  id="barcode_1"></td>
        				<td  id="issuechallanno_1"></td>
        				<td  id="skewcompany_1"></td>
        				<td  id="skewcomlocation_1"></td>
        				<td  id="floor_1"></td>
        				<td  id="lineno_1"></td>
        				<td  id="year_1"></td>
        				<td  id="jobno_1"></td>
        				<td  id="buyer_1"></td>
        				<td  id="stylereff_1" align="center"></td>
                        <td  id="internalreff_1"></td>
        				<td  id="order_1"></td>
                        <td  id="itme_1"></td>
                        <td  id="country_1"></td>
                        <td  id="color_1"></td>
                        <td  id="size_1"></td>
                        <td  id="remove_1">
                            <input type="button" id="decrease_1" name="decrease[]" style="width:30px"
                            class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/>
                            <input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/>
                            <input type="hidden" name="issueChallanNo[]" id="issueChallanNo_1"/>
                            <input type="hidden" name="skewCompany[]" id="skewCompany_1"/>
                            <input type="hidden" name="skewCompanyLocation[]" id="skewCompanyLocation_1"/>
                            <input type="hidden" name="flooId[]" id="floorId_1"/>
                            <input type="hidden" name="lineId[]" id="lineId_1"/>
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
    <table width="1130" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
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