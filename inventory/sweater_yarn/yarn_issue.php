<?

/*-------------------------------------------- Comments
Purpose         :   This form will create Yarn Issue Entry
                
Functionality   :   
JS Functions    :
Created by      :   Jahid
Creation date   :   07-05-2018
Updated by      :   
Update date     :   
QC Performed BY :       
QC Date         :   
Comments        :
*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Yarn Issue Info", "../../", 1, 1, $unicode, 1, 1);
?>
<script>
    <?
    $data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][3]) ;
    echo "var field_level_data= ". $data_arr . ";\n";
    ?>

    var permission = '<? echo $permission; ?>';
    if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../../logout.php";
    //set_field_level_access(3);
	
    function fn_room_rack_self_box() {
        if ($("#cbo_room").val() != 0)
            disable_enable_fields('txt_rack', 0, '', '');
        else {
            reset_form('', '', 'txt_rack*txt_shelf', '', '', '');
            disable_enable_fields('txt_rack*txt_shelf', 1, '', '');
        }
        if ($("#txt_rack").val() != 0)
            disable_enable_fields('txt_shelf', 0, '', '');
        else {
            reset_form('', '', 'txt_shelf', '', '', '');
            disable_enable_fields('txt_shelf', 1, '', '');
        }
    }

    function disable_fields(){
        $('#cbo_floor').attr('disabled', 'disabled');
        $('#cbo_room').attr('disabled', 'disabled');
        $('#txt_rack').attr('disabled', 'disabled');
        $('#txt_shelf').attr('disabled', 'disabled');
    }

    function generate_report_file(data, action, page) {
        window.open("requires/yarn_issue_controller.php?data=" + data + '&action=' + action, true);
    }

	function active_inactive(val) 
	{
		$('#tbl_child').find('input,select').val("");
		if(val==5)
		{
			$("#txt_buyer_job_no").val('').attr('disabled',false);
			$("#txt_lot_ratio").val('').attr('disabled',true);
			$("#txt_booking_no").val('').attr('disabled',true);
		}
		else if(val==6)
		{
			$("#txt_buyer_job_no").val('').attr('disabled',true);
			$("#txt_lot_ratio").val('').attr('disabled',false);
			$("#txt_booking_no").val('').attr('disabled',true);
		}
		else if(val==9)
		{
			$("#txt_buyer_job_no").val('').attr('disabled',true);
			$("#txt_lot_ratio").val('').attr('disabled',true);
			$("#txt_booking_no").val('').attr('disabled',false);
		}
		else
		{
			$("#txt_buyer_job_no").val('').attr('disabled',true);
			$("#txt_lot_ratio").val('').attr('disabled',true);
			$("#txt_booking_no").val('').attr('disabled',false);
		}
		
		if(val==5)
		{
			$("#cbo_issue_purpose").attr('disabled',false);
			load_drop_down( 'requires/yarn_issue_controller', val, 'load_drop_down_purpose', 'iss_purpose_td' );
		}
		else if(val==6)
		{
			load_drop_down( 'requires/yarn_issue_controller', val, 'load_drop_down_purpose', 'iss_purpose_td' );
			$("#cbo_issue_purpose").attr('disabled',true);
		}
		else if(val==9)
		{
			$("#cbo_issue_purpose").attr('disabled',false);
			load_drop_down( 'requires/yarn_issue_controller', val, 'load_drop_down_purpose', 'iss_purpose_td' );
		}
		else
		{
			// load_drop_down( 'requires/yarn_issue_controller', val, 'load_drop_down_purpose', 'iss_purpose_td' );
			// $("#cbo_issue_purpose").attr('disabled',true);
            $("#cbo_issue_purpose").attr('disabled',false);
			load_drop_down( 'requires/yarn_issue_controller', val, 'load_drop_down_purpose', 'iss_purpose_td' );
		}
		$("#txt_lot_no").val('');
		$("#requisition_item").html('');
    }

    function open_mrrpopup() {
        if (form_validation('cbo_company_id', 'Company Name') == false) {
            return;
        }
        var company = $("#cbo_company_id").val();
        var page_link = 'requires/yarn_issue_controller.php?action=mrr_popup&company=' + company;
        var title = "Search Issue Popup";
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=370px,center=1,resize=0,scrolling=0', '')
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0];
            var sysNumber = this.contentDoc.getElementById("hidden_sys_number").value.split(","); // system number
            $("#txt_system_no").val(sysNumber[0]);
			$("#update_id_mst").val(sysNumber[2]);
            $("#is_approved").val(sysNumber[1]);
            $("#is_posted_account").val(sysNumber[6]);
            if (sysNumber[6] == 1) document.getElementById("accounting_posted_status").innerHTML = "Already Posted In Accounting.";
            else document.getElementById("accounting_posted_status").innerHTML = "";
            // master part call here
            get_php_form_data(sysNumber[2], "populate_data_from_data", "requires/yarn_issue_controller");

            if (sysNumber[3] == 1) {
                $("#cbo_buyer_name option[value!='0']").remove();
                $("#cbo_buyer_name").append("<option selected value='" + sysNumber[4] + "'>" + sysNumber[5] + "</option>");
            }

            //list view call here
			disable_enable_fields('cbo_company_id*cbo_basis*cbo_issue_purpose*cbo_supplier*cbo_knitting_source*cbo_knitting_company*cbo_location_id', 1, "", "");
			show_list_view(sysNumber[2], 'show_dtls_list_view', 'list_container_yarn', 'requires/yarn_issue_controller', '');
			set_button_status(0, permission, 'fnc_yarn_issue_entry', 1, 1);
        }
    }

    //form reset/refresh function here
    function fnResetForm() {
        $("#tbl_master").find('input').attr("disabled", false);
        $("#dyeingColor_td").html('<? echo create_drop_down("cbo_dyeing_color", 142, $blank_array, "", 1, "-- Select --", 0, "", 0); ?>');
        $("#tbl_master").find('input,select').attr("disabled", false);
        set_button_status(0, permission, 'fnc_yarn_issue_entry', 1);
        reset_form('yarn_issue_1', 'list_container_yarn*requisition_item', '', '', '', 'cbo_uom');
        document.getElementById("accounting_posted_status").innerHTML = "";
    }

	function openmypage_job()
	{
		if(form_validation('cbo_company_id*cbo_basis*cbo_issue_purpose*cbo_store_name','Company Name*Basis*Issue Purpose*Store Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_store_name = $("#cbo_store_name").val();
		var cbo_basis = $("#cbo_basis").val();
		//var cbo_year = $("#cbo_year").val();
		var page_link='requires/yarn_issue_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name;
		var title='Job No Search';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			var buyer_id=this.contentDoc.getElementById("hide_buyer_id").value;
			var sty_ref=this.contentDoc.getElementById("hide_sty_ref").value;
			$("#tbl_child").find('select:not([id="cbo_store_name"]):not([name="cbo_buyer_name"]),input:not([name="txt_lot_ratio"]):not([name="txt_lot_ratio_id"]):not([name="txt_buyer_job_no"]):not([name="hide_job_id"]):not([name="txt_style_no"]):not([name="txt_booking_no"]):not([name="hide_booking_id"])').val('');
			show_list_view(job_no+'__'+cbo_store_name+'__'+companyID+'__'+cbo_basis, 'show_job_item_list_view', 'requisition_item', 'requires/yarn_issue_controller', '');
			//alert (job_no);
			$('#txt_buyer_job_no').val(job_no);
			$('#hide_job_id').val(job_id);
			$('#cbo_buyer_name').val(buyer_id).attr('disabled',true);
			$('#txt_style_no').val(sty_ref);
			$("#cbo_store_name").attr('disabled',true);	 
		}
	}
	
	function openmypage_booking()
	{
		if(form_validation('cbo_company_id*cbo_basis*cbo_issue_purpose*cbo_store_name','Company Name*Basis*Issue Purpose*Store Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_store_name = $("#cbo_store_name").val();
		var cbo_basis = $("#cbo_basis").val();
		var issue_purpose = $("#cbo_issue_purpose").val();
		if(issue_purpose==7 || issue_purpose==8 || issue_purpose==12 || issue_purpose==15 || issue_purpose==38 || issue_purpose==46 || issue_purpose==50 || issue_purpose==51)
		{
			var page_link='requires/yarn_issue_controller.php?action=booking_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&issue_purpose='+issue_purpose;
			var title='Booking No Search';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=780px,height=420px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var booking_no=this.contentDoc.getElementById("hide_booking_no").value;
				var booking_id=this.contentDoc.getElementById("hide_booking_id").value;
				//alert(booking_no);
				$("#tbl_child").find('select:not([id="cbo_store_name"]):not([name="cbo_buyer_name"]),input:not([name="txt_lot_ratio"]):not([name="txt_lot_ratio_id"]):not([name="txt_buyer_job_no"]):not([name="hide_job_id"]):not([name="txt_style_no"]):not([name="txt_booking_no"]):not([name="hide_booking_id"])').val('');
				//:not([name="txt_booking_no"]):not([name="hide_booking_id"])
				show_list_view(booking_id+'__'+booking_no+'__'+companyID+'__'+cbo_basis+'__'+cbo_store_name+'__'+issue_purpose, 'show_booking_item_list_view', 'requisition_item', 'requires/yarn_issue_controller', '');
				$("#cbo_store_name").attr('disabled',true);
				$("#txt_booking_no").val(booking_no);
				$("#hide_booking_id").val(booking_id);
			}
		}
		else
		{
			alert("Plese Select Service Wise Issue Purpose");
			$('#cbo_issue_purpose').focus();
			return;
		}
		
	}
	
	function set_form_data(prod_data)
	{
		var prod_ref=prod_data.split("__");
		if(prod_ref[18]==2)
		{
			load_drop_down( 'requires/yarn_issue_controller', prod_ref[18], 'load_drop_down_supplier_com', 'supplier' );
		}
		else
		{
			load_drop_down( 'requires/yarn_issue_controller', prod_ref[19], 'load_drop_down_supplier', 'supplier' );
		}
		
		var pord_id=prod_ref[0];
		var product_name_details=prod_ref[1];
		var unit_of_measure=prod_ref[2];
		var lot=prod_ref[3];
		var color_id=prod_ref[4];
		var yarn_count_id=prod_ref[5];
		var yarn_type=prod_ref[6];
		var brand=prod_ref[7];
		var brand_supplier=prod_ref[8];
		//alert(prod_ref[9]);
		var yarn_qnty=prod_ref[9];
		var composition=prod_ref[10];
		var composition_id=prod_ref[11];
		var uom_id=prod_ref[12];
		var brand_id=prod_ref[13];
		//alert(prod_ref[14]);
		var supplier_id=prod_ref[14];
		var job_no=prod_ref[15];
		var job_id=prod_ref[16];
		var wo_qnty=prod_ref[17];
        var floor = prod_ref[20];
        var room = prod_ref[21];
        var rack = prod_ref[22];
        var shelf = prod_ref[23];
        var bin = prod_ref[24];
		// alert(prod_ref[19])
		$("#txt_prod_id").val(pord_id);
		$("#txt_lot_no").val(lot);
		$("#txt_composition").val(composition);
		$("#txt_composition_id").val(composition_id);
		$("#cbo_uom").val(unit_of_measure);
		$("#cbo_uom_id").val(uom_id);
		$("#cbo_yarn_type").val(yarn_type);
		$("#cbo_color").val(color_id);
		$("#cbo_brand").val(brand);
		$("#cbo_brand_id").val(brand_id);
		$("#cbo_yarn_count").val(yarn_count_id);
		$("#txt_current_stock").val(yarn_qnty);
		$("#cbo_supplier").val(supplier_id);
		$("#txt_wo_qnty").val(wo_qnty);


        load_drop_down( 'requires/yarn_issue_controller', prod_ref[19], 'load_drop_down_floor', 'floor_td' );
        load_drop_down( 'requires/yarn_issue_controller', prod_ref[19], 'load_drop_down_room', 'room_td' );
        load_drop_down( 'requires/yarn_issue_controller', prod_ref[19], 'load_drop_down_rack', 'rack_td' );
        load_drop_down( 'requires/yarn_issue_controller', prod_ref[19], 'load_drop_down_shelf', 'shelf_td' );


		$("#cbo_floor").val(floor);
		$("#cbo_room").val(room);
		$("#txt_rack").val(rack);         
		$("#txt_shelf").val(shelf);         
		if(job_no!="" && job_no!=undefined)
		{
			//alert(job_no);
			$("#txt_buyer_job_no").val(job_no);
			$("#hide_job_id").val(job_id);
		}
	}
	
	function openmypage_lot_ratio()
	{
		if (form_validation('cbo_company_id*cbo_basis*cbo_store_name', 'Company Name*Basis*Store') == false) {
            return;
        }

        var company = $("#cbo_company_id").val();
        var cbo_store_name = $("#cbo_store_name").val();
		var cbo_basis = $("#cbo_basis").val();
		var page_link='requires/yarn_issue_controller.php?action=lot_ration_popup&company_id='+company+"&cbo_store_name="+cbo_store_name; 
		var title="Lot Ration Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=420px,center=1,resize=0,scrolling=0','');
		emailwindow.onclose=function()
		{
			var sysNumber = this.contentDoc.getElementById("update_mst_id"); 
			var sysNumber=sysNumber.value.split('_');
			var lot_ration_id=sysNumber[0];
			var job_no=sysNumber[1];
			var lot_ration_no=sysNumber[2];
			$("#txt_lot_ratio_id").val(lot_ration_id);
			$("#txt_lot_ratio").val(lot_ration_no);
			//alert(job_no);return;
			get_php_form_data( job_no, "load_php_mst_job_data", "requires/yarn_issue_controller" );
			show_list_view(job_no+'__'+cbo_store_name+'__'+company+'__'+cbo_basis+'__'+lot_ration_id, 'show_lot_item_list_view', 'requisition_item', 'requires/yarn_issue_controller', '');
			$("#cbo_store_name").attr('disabled',true);
		}
	}
	
	/*function load_list_view(str) {
        if (str == "") {
            $('#requisition_item').html('');
            return;
        }
        show_list_view(str + ',' + $("#cbo_company_id").val() + ',' + $("#cbo_buyer_name").val(), 'show_req_list_view', 'requisition_item', 'requires/yarn_issue_controller', '');
    }*/
	
	function fn_stock_check()
	{
		var issue_qnty=$("#txt_issue_qnty").val()*1;
		var stock_qnty=$("#txt_current_stock").val()*1;
		var cbo_basis=$("#cbo_basis").val()*1;
		var wo_qnty=$("#txt_wo_qnty").val()*1;
		if(issue_qnty>stock_qnty)
		{
			alert("Issue Quantity Exceeded Current Stock");
			$("#txt_issue_qnty").val("").focus();
		}
		if(issue_qnty>wo_qnty && cbo_basis==9)
		{
			alert("Issue Quantity Exceeded WO Quantity");
			$("#txt_issue_qnty").val("").focus();
		}
	}
	
	function fnc_yarn_issue_entry(operation) {

        if (operation == 4) {

            if ($("#update_id_mst").val() == "") {
                alert("Please Save First.");
                return;
            }
            var report_title = $("div.form_caption").html();
			//alert(report_title);return;
            generate_report_file($('#cbo_company_id').val() + '*' + $('#update_id_mst').val() + '*' + report_title, 'yarn_issue_print', 'requires/yarn_issue_controller');
            return;
        }
        else 
		{
			//if(operation == 2)
//			{
//				show_msg(13);alert("Define Later");return;
//			}
            if ($("#is_posted_account").val() == 1) {
                alert("Already Posted In Accounting. Save Update Delete Restricted.");
                return;
            }
            var is_approved = $('#is_approved').val();

            if (is_approved == 1) {
                alert("Yarn issue is Approved. So Change Not Allowed");
                return;
            }
			
			var current_date = '<? echo date("d-m-Y"); ?>';
            if (date_compare($('#txt_issue_date').val(), current_date) == false) {
                alert("Issue Date Can not Be Greater Than Current Date");
                return;
            }
			
            if (form_validation('cbo_company_id*cbo_basis*cbo_issue_purpose*txt_issue_date*cbo_supplier*cbo_store_name*txt_buyer_job_no*txt_lot_no*txt_issue_qnty', 'Company*Basis*Issue Purpose*Issue Date*Supplier*Store*Job*Lot*Issue Quantity') == false) {
                return;
            }

			if($('#txt_current_stock').val()<=0)
			{
				alert("Current Stock Quantity can not less than Zero");
				return;
			}
			
            // Store upto validation start
            var store_update_upto=$('#store_update_upto').val()*1;
            var cbo_floor=$('#cbo_floor').val()*1;
            var cbo_room=$('#cbo_room').val()*1;
            var txt_rack=$('#txt_rack').val()*1;
            var txt_shelf=$('#txt_shelf').val()*1;
            var cbo_bin = 0 //no bin field on sweater yarn issue 
            // var cbo_bin=$('#cbo_bin').val()*1;

            if(store_update_upto > 1)
            {
                if(store_update_upto==6 && (cbo_floor==0 || cbo_room==0 || txt_rack==0 || txt_shelf==0 || cbo_bin==0))
                {
                    alert("Up To Bin Value Full Fill Required For Inventory");return;
                }
                else if(store_update_upto==5 && (cbo_floor==0 || cbo_room==0 || txt_rack==0 || txt_shelf==0))
                {
                    alert("Up To Shelf Value Full Fill Required For Inventory");return;
                }
                else if(store_update_upto==4 && (cbo_floor==0 || cbo_room==0 || txt_rack==0))
                {
                    alert("Up To Rack Value Full Fill Required For Inventory");return;
                }
                else if(store_update_upto==3 && (cbo_floor==0 || cbo_room==0))
                {
                    alert("Up To Room Value Full Fill Required For Inventory");return;
                }
                else if(store_update_upto==2 && cbo_floor==0)
                {
                    alert("Up To Floor Value Full Fill Required For Inventory");return;
                }
            }
            // Store upto validation End

			var dataString = 'txt_system_no*update_id_mst*cbo_company_id*cbo_basis*cbo_issue_purpose*txt_issue_date*cbo_knitting_source*cbo_knitting_company*cbo_location_id*cbo_supplier*txt_challan_no*cbo_loan_party*cbo_sample_type*cbo_ready_to_approved*txt_remarks*cbo_store_name*txt_buyer_job_no*hide_job_id*cbo_buyer_name*txt_style_no*txt_lot_ratio*txt_lot_ratio_id*txt_lot_no*txt_prod_id*txt_weight_per_bag*txt_issue_qnty*txt_weight_per_cone*txt_current_stock*txt_no_cone*txt_no_bag*cbo_dyeing_color*txt_returnable_qty*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_yarn_count*cbo_color*txt_composition*cbo_brand*cbo_yarn_type*cbo_uom*update_id*cbo_item*cbo_uom_id*cbo_brand_id*txt_booking_no*hide_booking_id';
		
			var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string(dataString, "../../");
			//alert(data);return;
			freeze_window(operation);
			http.open("POST", "requires/yarn_issue_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_yarn_issue_entry_reponse;
		}
	}

	function fnc_yarn_issue_entry_reponse() {
		if (http.readyState == 4) {
			var reponse = trim(http.responseText).split('**');
			
			if (reponse[0] * 1 == 20 || reponse[0] * 1 == 31) {
				alert(reponse[1]);
				show_msg(reponse[0]);
				release_freezing();
				return;
			}
			else if (reponse[0] == 0 || reponse[0] == 1 || reponse[0] == 2) {
				show_msg(reponse[0]);
				//alert(reponse[0]);
				$("#txt_system_no").val(reponse[1]);
				$("#update_id_mst").val(reponse[2]);
				disable_enable_fields('cbo_company_id*cbo_basis*cbo_issue_purpose*cbo_supplier*cbo_knitting_source*cbo_knitting_company*cbo_location_id', 1, "", "");
				$("#tbl_child").find('select:not([id="cbo_store_name"]):not([name="cbo_buyer_name"]),input:not([name="txt_lot_ratio"]):not([name="txt_lot_ratio_id"]):not([name="txt_buyer_job_no"]):not([name="hide_job_id"]):not([name="txt_style_no"]):not([name="txt_booking_no"]):not([name="hide_booking_id"])').val('');
				show_list_view(reponse[2], 'show_dtls_list_view', 'list_container_yarn', 'requires/yarn_issue_controller', '');
				set_button_status(0, permission, 'fnc_yarn_issue_entry', 1, 1);
				release_freezing();
			}
			else
			{
				show_msg(reponse[0]);
				release_freezing();
				return;
			}
		}
	}

    function independence_basis_controll_function(data)
    {
    	var independent_control_arr = JSON.parse('<? echo json_encode($independent_control_arr); ?>');
    	$("#cbo_basis option[value='6']").show();
    	$("#cbo_basis").val(0);
    	if(independent_control_arr && independent_control_arr[data]==1)
    	{
    		$("#cbo_basis option[value='6']").hide();
    	}

        var status = return_global_ajax_value(data, 'upto_variable_settings', '', 'requires/yarn_issue_controller').trim();
        $('#store_update_upto').val(status);
    }

</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="left">
        <? echo load_freeze_divs("../../", $permission); ?><br/>
        <form name="yarn_issue_1" id="yarn_issue_1" autocomplete="off">
            <div style="width:980px; float:left; position:relative" align="center">
                <table width="80%" cellpadding="0" cellspacing="2">
                    <tr>
                        <td width="100%" align="center" valign="top">
                            <fieldset style="width:980px;">
                                <legend>Yarn Issue</legend>
                                <br/>
                                <fieldset style="width:950px;">
                                    <table width="950" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                                        <tr>
                                            <td colspan="6" align="center"><b>System ID</b>
                                                <input type="text" name="txt_system_no" id="txt_system_no" class="text_boxes" style="width:160px" placeholder="Double Click To Search" onDblClick="open_mrrpopup()" readonly/><input type="hidden" id="update_id_mst" name="update_id_mst" readonly/>&nbsp;&nbsp;
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="120" align="right" class="must_entry_caption">Company Name</td>
                                            <td width="170">
                                                <?
												//get_php_form_data( this.value, 'company_wise_report_button_setting','requires/yarn_issue_controller' );
                                                echo create_drop_down("cbo_company_id", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/yarn_issue_controller', this.value, 'load_drop_down_supplier', 'supplier' );independence_basis_controll_function(this.value);load_drop_down( 'requires/yarn_issue_controller',this.value+'_'+1, 'load_drop_down_buyer', 'buyer_td_id' );load_room_rack_self_bin('requires/yarn_issue_controller*1', 'store','store_td', this.value,'','','','','','','','');");
                                                ?>
                                            </td>
                                            <td width="120" align="right" class="must_entry_caption">Basis</td>
                                            <td width="160" id="receive_baisis_td">
                                                <?
                                                echo create_drop_down("cbo_basis", 170, $issue_basis, "", 1, "-- Select Basis --", $selected, "active_inactive(this.value);", "", "5,6,9,10");
                                                ?>
                                            </td>
                                            <td width="120" align="right" class="must_entry_caption">Issue Purpose</td>
                                            <td id="iss_purpose_td">
                                                <?
                                                echo create_drop_down("cbo_issue_purpose", 170, $yarn_issue_purpose, "", 1, "-- Select Purpose --", $selected, "", "", "1,3,4,5,7,8,12,15,26,29,30,38,46,47,50,51","","","");//9,10,11,13,14,16,27,28,32,33
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="right" class="must_entry_caption">Issue Date</td>
                                            <td>
                                                <input type="text" name="txt_issue_date" id="txt_issue_date" class="datepicker" style="width:160px;" placeholder="Select Date" value="<? echo date('d-m-Y');?>" readonly/>
                                            </td>
                                            <td align="right" id="knit_source">Knitting Source</td>
                                            <td width="170">
                                                <?
                                                echo create_drop_down("cbo_knitting_source", 170, $knitting_source, "", 1, "-- Select --", $selected, "load_drop_down( 'requires/yarn_issue_controller', this.value+'**'+$('#cbo_company_id').val()+'**'+$('#cbo_issue_purpose').val(), 'load_drop_down_knit_com', 'knitting_company_td' );", "", "1,3");
                                                ?>
                                            </td>
                                            <td align="right" id="knit_com"> Issue To</td>
                                            <td id="knitting_company_td">
                                                <?
                                                echo create_drop_down("cbo_knitting_company", 170, $blank_array, "", 1, "-- Select --", $selected, "", "", "");
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            
                                            <td align="right">Location</td>
                                            <td id="location_td">
                                                <?
                                                echo create_drop_down("cbo_location_id", 170, $blank_array, "", 1, "-- Select Location --", "", "");
                                                ?>
                                            </td>
                                            <td id="loanParty_td" align="right">Loan Party</td>
                                            <td id="loanParty">
                                                <?
                                                echo create_drop_down("cbo_loan_party", 170, $blank_array, "", 1, "--- Select Party ---", $selected, "", 1);
                                                ?>
                                            </td>
                                            <td  align="right">Challan/Program No</td>
                                            <td >
                                                <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:160px" placeholder="Entry">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td  align="right">Sample Type</td>
                                            <td ><?
                                            echo create_drop_down("cbo_sample_type", 170, "select id,sample_name from lib_sample where status_active=1 and is_deleted=0 order by sample_name", "id,sample_name", 1, "-- Select --", $selected, "", "", "");
                                            ?>
                                            </td>
                                            <td align="right">Ready to Approve</td>
                                            <td>
                                                <?
                                                echo create_drop_down("cbo_ready_to_approved", 172, $yes_no, "", 1, "-- Select--", 2, "", "", "");
                                                ?>
                                            </td>
                                            <td  align="right">Remarks</td>
                                            <td >
                                                <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:160px" placeholder="Entry">
                                            </td>
                                        
                                    </tr>
                                    <tr>
                                        <td align="right">&nbsp;</td>
                                        <td colspan="3">&nbsp;</td>
                                        <td align="right">&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                </table>
                            </fieldset>
                            <br/>
                            <table cellpadding="0" cellspacing="1" width="96%" id="tbl_child">
                                <tr>
                                    <td width="49%" valign="top">
                                        <fieldset style="width:950px;">
                                            <legend>New Issue Item</legend>
                                            <table width="100%" cellspacing="2" cellpadding="0" border="0">
                                                <tr>
                                                    <td align="right" class="must_entry_caption">Store Name</td>
                                                    <td id="store_td">
                                                        <?
                                                        echo create_drop_down("cbo_store_name", 162, $blank_array, "", 1, "-- Select Store --", 0, "", 0,'','','','','','',"cbo_store_name");
                                                        ?>
                                                    </td>
                                                    <td align="right">Buyer Job No</td>
                                                    <td>
                                                        <input type="text" name="txt_buyer_job_no" id="txt_buyer_job_no" class="text_boxes" style="width:130px" onDblClick="openmypage_job()" readonly placeholder="Browse"/>
                                                        <input type="hidden" name="hide_job_id" id="hide_job_id">
                                                    </td>
                                                    <td align="right">Booking No</td>
                                                    <td>
                                                        <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:130px" onDblClick="openmypage_booking()" readonly placeholder="Browse"/>
                                                        <input type="hidden" name="hide_booking_id" id="hide_booking_id">
                                                    </td>
                                                    <td align="right">Style</td>
                                                    <td>
                                                        <input type="text" name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:140px" readonly placeholder="Display"/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                	<td align="right" class="must_entry_caption" id="supplier_td">Supplier</td>
                                                    <td id="supplier">
                                                        <?
                                                        echo create_drop_down("cbo_supplier", 162, $blank_array, "", 1, "-- Select --", 0, "", 1);
                                                        ?>
                                                    </td>
                                                    <td width="110" align="right">Lot Ratio. No</td>
                                                    <td width="150">
                                                        <input type="text" name="txt_lot_ratio" id="txt_lot_ratio" class="text_boxes" onDblClick="openmypage_lot_ratio()"  placeholder="Browse or Write" style="width:130px;"/>
                                                        <input type="hidden" name="txt_lot_ratio_id" id="txt_lot_ratio_id">
                                                    </td>
                                                    <td align="right">Buyer Name</td>
                                                    <td id="buyer_td_id">
                                                        <?
                                                        echo create_drop_down("cbo_buyer_name", 140, $blank_array, "", 1, "-- Select Buyer --", 0, "", 1);
                                                        ?>
                                                    </td>
                                                    <td align="right">Floor</td>
                                                    <td id="floor_td">
                                                        <? echo create_drop_down( "cbo_floor", 152,$blank_array,"", 1, "--Select--", 0, "",1 ); ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="110" align="right" class="must_entry_caption">Lot No</td>
                                                    <td>
                                                        <input type="text" name="txt_lot_no" id="txt_lot_no" class="text_boxes" placeholder="Display" style="width:150px;" readonly/>
                                                        <input type="hidden" name="txt_prod_id" id="txt_prod_id" readonly/>
                                                    </td>
                                                    <td align="right">Wght  per Bag</td>
                                                    <td>
                                                        <input name="txt_weight_per_bag" id="txt_weight_per_bag" class="text_boxes_numeric" type="text" style="width:130px;" placeholder="Entry"/>
                                                    </td>
                                                    <td align="right">Composition</td>
                                                    <td width="130">
                                                        <input type="text" name="txt_composition" id="txt_composition" class="text_boxes" style="width:130px;" placeholder="Display" readonly>
                                                        <input type="hidden" name="txt_composition_id" id="txt_composition_id">
                                                    </td>
                                                    <td align="right">Room</td>
                                                    <td id="room_td"><? echo create_drop_down( "cbo_room", 152,$blank_array,"", 1, "--Select--", 0, "",1 );?> </td>
												</tr>
                                                <tr>
                                                    <td align="right" class="must_entry_caption">Issue Qty.</td>
                                                    <td>
                                                        <input type="text" name="txt_issue_qnty" id="txt_issue_qnty" class="text_boxes_numeric" style="width:150px;" onKeyUp="fn_stock_check()" />
                                                    </td>
                                                    <td align="right">Wght @ Cone</td>
                                                    <td><input class="text_boxes_numeric" name="txt_weight_per_cone" id="txt_weight_per_cone" type="text" style="width:130px;" placeholder="Entry"/></td>
                                                    <td align="right">Yarn Type</td>
                                                    <td><input type="text" name="cbo_yarn_type" id="cbo_yarn_type" class="text_boxes" style="width:130px;" placeholder="Display" disabled readonly></td>
                                                    <td align="right" >Rack</td>
                                                    <td id="rack_td"><? echo create_drop_down( "txt_rack", 152,$blank_array,"", 1, "--Select--", 0, "",1 ); ?></td>
                                                </tr>
                                                <tr>
                                                    <td align="right">Current Stock:</td>
                                                    <td><input type="text" name="txt_current_stock" id="txt_current_stock" class="text_boxes_numeric" style="width:150px;" placeholder="Display" readonly/></td>
                                                    <td align="right">No. Of Cone</td>
                                                    <td>
                                                        <input type="text" name="txt_no_cone" id="txt_no_cone" class="text_boxes_numeric" style="width:130px;" placeholder="Entry"/>
                                                    </td>
                                                    <td align="right">Color</td>
                                                     <td><input type="text" name="cbo_color" id="cbo_color" class="text_boxes" style="width:130px;" placeholder="Display" disabled readonly></td>
                                                    <td align="right" >Shelf</td>
                                                    <td id="shelf_td"><?  echo create_drop_down( "txt_shelf", 152,$blank_array,"", 1, "--Select--", 0, "",1 ); ?></td>
                                                </tr>
                                                <tr>
                                                    <td align="right">WO/Lot Qnty : </td>
                                                    <td><input type="text" name="txt_wo_qnty" id="txt_wo_qnty" class="text_boxes_numeric" style="width:150px;" placeholder="Display" readonly/></td>
                                                     <td align="right">Dyeing Color</td>
                                                     <td id="dyeingColor_td"><? echo create_drop_down("cbo_dyeing_color",142, $blank_array, "", 1, "-- Select --", 0, "", 0); ?></td>
                                                     <td align="right">Using Item</td>
                                                     <td><? echo create_drop_down("cbo_item", 142, $using_item_arr, "", 1, "--Select--", "", "", 0); ?></td>
                                                     <td align="right">Brand</td>
                                                     <td>
                                                     <input type="text" name="cbo_brand" id="cbo_brand" class="text_boxes" style="width:140px;" placeholder="Display" disabled readonly>
                                                     <input type="hidden" name="cbo_brand_id" id="cbo_brand_id" readonly/>
                                                     </td>
                                                </tr>
                                                <tr>
                                                    <td align="right">Returnable Qty.</td>
                                                    <td><input type="text" name="txt_returnable_qty" id="txt_returnable_qty" class="text_boxes_numeric" style="width:150px;" /></td>
                                                    <td align="right">No. Of Bag</td>
                                                    <td><input type="text" name="txt_no_bag" id="txt_no_bag" class="text_boxes_numeric" style="width:130px;" placeholder="Entry"/></td>
                                                    
                                                     <td align="right">Yarn Count</td>
                                                     <td><input type="text" name="cbo_yarn_count" id="cbo_yarn_count" class="text_boxes" style="width:130px;" placeholder="Display" disabled readonly></td>
                                                     <td align="right">UOM</td>
                                                    <td><input type="text" name="cbo_uom" id="cbo_uom" class="text_boxes" style="width:140px;" placeholder="Display" disabled readonly><input type="hidden" name="cbo_uom_id" id="cbo_uom_id"></td>
                                                     <!--<td align="right">Supplier</td>
                                                     <td>
                                                        <?echo create_drop_down("cbo_supplier_lot", 142, "select id,supplier_name from lib_supplier where status_active =1 and is_deleted=0", "id,supplier_name", 1, "-- Display --", 0, "", 1);
                                                        ?>
                                                    </td>
                                                    <td align="right">BTB Selection</td>
                                                    <td>
                                                        <input type="text" class="text_boxes" id="txt_btb_selection"
                                                        name="txt_btb_selection" value=""
                                                        onDblClick="openmypage_btb_selection()"
                                                        placeholder="Double Click" style="width:150px;" readonly>
                                                        <input type="hidden" class="text_boxes" id="txt_btb_lc_id"
                                                        name="txt_btb_lc_id" value="">
                                                    </td>-->
                                                </tr>
                                        </table>
                                    </fieldset>
                                </td>
                            </tr>
                        </table>
                        <table cellpadding="0" cellspacing="1" width="100%">
                            <tr>
                                <td colspan="6" align="center"></td>
                            </tr>
                            <tr>
                                <td align="center" colspan="6" valign="middle" class="button_container">
                                    <div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div>
                                    <!-- details table id for update -->
                                    <input type="hidden" id="is_approved" name="is_approved" value="" readonly/>
                                    <input type="hidden" id="update_id" name="update_id" readonly/>
                                    <input type="hidden" id="is_posted_account" name="is_posted_account" value=""/>
                                    <input type="hidden" name="store_update_upto" id="store_update_upto">
                                    <? echo load_submit_buttons($permission, "fnc_yarn_issue_entry", 0, 1, "fnResetForm()", 1); ?>
                                    <!--<input type="button" name="print"id="Printt1"value="Print"onClick="fnc_yarn_issue_entry(4)"style="width:80px;" class="formbutton">-->             
                                    <div id="accounting_posted_status" style=" color:red; font-size:24px;" align="left"></div>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                    <fieldset>
                        <div style="width:970px;" id="list_container_yarn"></div>
                    </fieldset>
                </td>
            </tr>
        </table>
    </div>
    <div style="float:left; position:relative; margin-left:15px" align="left" id="requisition_item"></div>
</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
