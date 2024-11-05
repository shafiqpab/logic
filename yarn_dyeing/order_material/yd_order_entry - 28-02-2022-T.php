<?
/*--- ----------------------------------------- Comments
Purpose			: 	Yarn Dyeing Order Entry					
Functionality	:	
JS Functions	:
Created by		:	Shakil Ahmed
Creation date 	: 	08-02-2020
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
echo load_html_head_contents("YD Order Entry Info", "../../", 1,1, $unicode,1,'');

?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

    function fnc_yarn_dyeing( operation )
    {
        var delete_master_info=0; var i=0;
        //var process = $("#cbo_process_name").val();
        var cbo_within_group = $("#cbo_within_group").val();
           
        if ( form_validation('cbo_company_name*cbo_within_group*cbo_party_name*cbo_currency*txt_order_receive_date','Company*Within Group*Party*Currency*Order Receive Date')==false ){
            return;
        }
        
        var txt_job_no          = $('#txt_job_no').val();
        var cbo_company_name    = $('#cbo_company_name').val();
        var cbo_location_name   = $('#cbo_location_name').val();
        var cbo_within_group    = $('#cbo_within_group').val();
        var cbo_party_name      = $('#cbo_party_name').val();
        var cbo_party_location  = $('#cbo_party_location').val();
        var cbo_currency        = $('#cbo_currency').val();
        var txt_order_receive_date = $('#txt_order_receive_date').val();
        var txt_delivery_date   = $('#txt_delivery_date').val();
        var txt_rec_start_date  = $('#txt_rec_start_date').val();
        var txt_rec_end_date    = $('#txt_rec_end_date').val();
        var txt_order_no           = $('#txt_order_no').val();
        var hid_order_id        = $('#hid_order_id').val();
        var txt_remarks         = $('#txt_remarks').val();
        var is_without_order    = $('#hid_is_without_order').val();
        var hid_booking_type    = $('#hid_booking_type').val();
        var update_id           = $('#update_id').val();
		var txt_check_box       = $('#txt_check_box').val();
		
		if ( document.getElementById('txt_check_box').checked==true)
		{
			document.getElementById('txt_check_box').value=1;
			// alert(document.getElementById('txt_check_box').value);
			 //txt_order_no
			
			  $('#cbo_within_group').attr("disabled",true);
			  $('#txt_check_box').attr("disabled",true);
			  
			//alert(chk );
		}
		else if(document.getElementById('txt_check_box').checked==false)
		{
			document.getElementById('txt_check_box').value=0;
			$('#cbo_within_group').attr("disabled",false);
			 $('#txt_check_box').attr("disabled",false);
			
			  
			// alert(document.getElementById('txt_check_box').value);
		}
		var txt_check_box       = $('#txt_check_box').val();
		//alert(3);
		//return;
		
        var j=0; var check_field=0; data_all="";
            
        $("#tbl_dtls_yarn_dyeing tbody tr").each(function()
        {
            var txtstyleRef         = $(this).find('input[name="txtstyleRef[]"]').val();
            var txtsaleOrder        = $(this).find('input[name="txtsaleOrder[]"]').val();
            var txtsaleOrderID      = $(this).find('input[name="txtsaleOrderID[]"]').val();
            var txtProductID        = $(this).find('input[name="txtProductID[]"]').val();
            var txtprocess          = $(this).find('select[name="txtprocess[]"]').val();
            var txtlot              = $(this).find('input[name="txtlot[]"]').val();
           
            var cboCount            = $(this).find('select[name="cboCount[]"]').val();
            var cboYarnType         = $(this).find('select[name="cboYarnType[]"]').val();
            var cboComposition      = $(this).find('select[name="cboComposition[]"]').val();
            var cboUom              = $(this).find('select[name="cboUom[]"]').val();

            var txtItemColor        = $(this).find('input[name="txtItemColor[]"]').val();
            var txtYarnColor        = $(this).find('input[name="txtYarnColor[]"]').val();
            var txtItemColorID      = $(this).find('input[name="txtItemColorID[]"]').val();
            var txtYarnColorID      = $(this).find('input[name="txtYarnColorID[]"]').val();
			//alert(txtItemColor);
			
			

            var txtCSP              = $(this).find('input[name="txtCSP[]"]').val();
            var txtnoBag            = $(this).find('input[name="txtnoBag[]"]').val();
            var txtConeBag          = $(this).find('input[name="txtConeBag[]"]').val();
            var txtNoCone           = $(this).find('input[name="txtNoCone[]"]').val();//txtNoCone_1
            var txtAVG              = $(this).find('input[name="txtAVG[]"]').val();

            var txtOrderqty         = $(this).find('input[name="txtOrderqty[]"]').val();
            var txtRate             = $(this).find('input[name="txtRate[]"]').val();
            var txtAmount           = $(this).find('input[name="txtAmount[]"]').val();
            var txtProcessLoss      = $(this).find('input[name="txtProcessLoss[]"]').val();
            var txtTotalqty         = $(this).find('input[name="txtTotalqty[]"]').val();
            var hdnDtlsUpdateId     = $(this).find('input[name="hdnDtlsUpdateId[]"]').val();
			//alert(txtNoCone+'='+txtAVG);
           
            /*var hdnDtlsdata       = $(this).find('input[name="hdnDtlsdata[]"]').val();
            var hdnDtlsUpdateId     = $(this).find('input[name="hdnDtlsUpdateId[]"]').val();
            var hdnbookingDtlsId    = $(this).find('input[name="hdnbookingDtlsId[]"]').val();
            var txtDeletedId        = $(this).find('input[name="txtDeletedId[]"]').val();
            var txtIsWithOrder      = $(this).find('input[name="txtIsWithOrder[]"]').val();
            var txtIsRevised        = $(this).find('input[name="txtIsRevised[]"]').val();*/
            //txt_total_amount  += $(this).find('input[name="amount[]"]').val()*1;
            //alert(cboSection);
            j++;
			
            
            if( (txtlot=='' || cboCount==0 || cboYarnType==0 || cboComposition==0 || txtItemColor=='' || txtYarnColor==''|| cboUom==0 || txtOrderqty==''))
            {                   
                if(txtlot==''){
                    alert('Please Fill up Lot');
                    check_field=1 ; return;
                }else if(cboCount==0){
                    alert('Please Select Count');
                    check_field=1 ; return;
                }else if(cboYarnType==0){
                    alert('Please Select Yarn Type ');
                    check_field=1 ; return;
                }else if(cboComposition==0){
                    alert('Please Select Yarn Composition');
                    check_field=1 ; return;
                }else if(txtItemColor==''){
                    alert('Please Fill up Item Color ');
                    check_field=1 ; return;
                }else if(txtYarnColor==''){
                    alert('Please Fill up Y/D Color');
                    check_field=1 ; return;
                }else if(cboUom==0){
                    alert('Please Select Uom');
                    check_field=1 ; return;
                }else if(txtOrderqty==''){
                    alert('Please Fill up Order Qty ');
                    check_field=1 ; return;
                }
                return;
            }
			//+"&txtItemColor_" + j + "='" + $('#txtItemColor_'+i).val();
            i++;
			//alert(txt_check_box);
			if(txt_check_box==1)
			{
				//txtItemColor_" + j + "='" + $('#txtItemColor_'+i).val()
				//var txtItemColor=$('#txtItemColor_'+i).val();
				//var txtItemColor=$('#txtItemColor_'+i).val();
			}
			
            data_all += "&txtstyleRef_" + j + "='" + txtstyleRef + "'&txtsaleOrder_" + j + "='" + txtsaleOrder+ "'&txtsaleOrderID_" + j + "='" + txtsaleOrderID+ "'&txtProductID_" + j + "='" + txtProductID + "'&txtprocess_" + j + "='" + txtprocess + "'&txtlot_" + j + "='" + txtlot + "'&cboCount_" + j + "='" + cboCount  + "'&cboYarnType_" + j + "='" + cboYarnType + "'&cboComposition_" + j + "='" + cboComposition + "'&txtItemColor_" + j + "='" + txtItemColor + "'&txtYarnColor_" + j + "='" + txtYarnColor + "'&txtCSP_" + j + "='" + txtCSP + "'&txtnoBag_" + j + "='" + txtnoBag + "'&txtConeBag_" + j + "='" + txtConeBag + "'&txtNoCone_" + j + "='" + txtNoCone + "'&txtAVG_" + j + "='" + txtAVG + "'&cboUom_" + j + "='" + cboUom +"'&txtOrderqty_" + j + "='" + txtOrderqty +"'&txtRate_" + j + "='" + txtRate +"'&txtAmount_" + j + "='" + txtAmount +"'&txtProcessLoss_" + j + "='" + txtProcessLoss+"'&txtTotalqty_" + j + "='" + txtTotalqty+"'&hdnDtlsUpdateId_" + j + "='" + hdnDtlsUpdateId + "'";
        });
        
        if(check_field==0){
            var data="action=save_update_delete&operation="+operation+'&total_row='+i+'&txt_job_no='+txt_job_no+'&cbo_company_name='+cbo_company_name+'&cbo_location_name='+cbo_location_name+'&cbo_within_group='+cbo_within_group+'&cbo_party_name='+cbo_party_name+'&cbo_party_location='+cbo_party_location+'&cbo_currency='+cbo_currency+'&txt_order_receive_date='+txt_order_receive_date+'&txt_delivery_date='+txt_delivery_date+'&txt_rec_start_date='+txt_rec_start_date+'&txt_rec_end_date='+txt_rec_end_date+'&txt_order_no='+txt_order_no+'&hid_order_id='+hid_order_id+'&txt_remarks='+txt_remarks+'&is_without_order='+is_without_order+'&hid_booking_type='+hid_booking_type+'&update_id='+update_id+'&txt_check_box='+txt_check_box+data_all;
			 
            //alert (data); return; 
            freeze_window(operation);
            http.open("POST","requires/yd_order_entry_controller.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_yarn_dyeing_response;
        }else{
            return;
        }
    }

    function fnc_yarn_dyeing_response()
    {
        
        if(http.readyState == 4) 
        {
            //alert (http.responseText);//return;
            var response=trim(http.responseText).split('**');
            
           /* if(trim(response[0])=='20'){
                alert("Job Found ."+"\n So Update/Delete Not Possible"+"\n Job Card No.:"+response[4]);
                release_freezing();return;
            }else if(trim(response[0])=='26'){
                alert ("Delivery Date not allowed less than Order Receive Date");
                release_freezing();return;
            }else if(trim(response[0])=='25'){
                alert ("Receive Date Must be Current Date");
                release_freezing();return;
            }else if(trim(response[0])=='27'){
                alert ("Order Quantity Can't Less Than Delivary Quantity");
                release_freezing();return;
            }*/

            if(response[0]==0 || response[0]==1){
                var job_no      = response[1];
                var update_id   = response[2];
                var order_no    = response[3];
                var within_group = $('#cbo_within_group').val();
                /*if(within_group==2){
                    document.getElementById('txt_order_no').value = response[3];
                }*/
                document.getElementById('txt_job_no').value = response[1];
                document.getElementById('update_id').value = response[2];
                $('#txt_order_no').attr('disabled',true);
                $('#cbo_within_group').attr('disabled',true);
                $('#cbo_company_name').attr('disabled',true);
                $('#txt_delivery_date').attr('disabled',true);
                $('#cbo_party_name').attr('disabled',true);
                $('#cbo_party_location').attr('disabled',true);
                $('#txt_order_receive_date').attr('disabled',true);

                show_list_view(2+'_'+update_id+'_'+job_no+'_'+within_group+'_'+order_no,'order_dtls_list_view','dyeing_details_container','requires/yd_order_entry_controller','setFilterGrid(\'list_view\',-1)');
                set_button_status(1, permission, 'fnc_yarn_dyeing',1);
                //btn_load_change_bookings();
                set_all_onclick();

            }else if(response[0]==2){
                location.reload();
            }
            show_msg(response[0]);
            release_freezing();
        }
    }

    function openmypage_job(){
        if ( form_validation('cbo_company_name','Company')==false ){
            return;
        }
		var txt_check_box=$('#txt_check_box').val();
        var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value+"_"+document.getElementById('txt_check_box').value;

        page_link='requires/yd_order_entry_controller.php?action=job_popup&data='+data;
        title='Job Order Entry';
        
        emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=890px, height=420px, center=1, resize=0, scrolling=0','../')
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var theemaildata=this.contentDoc.getElementById("selected_job").value;
            var ex_data=theemaildata.split('_');
            get_php_form_data(ex_data[0], "populate_job_master_from_data", "requires/yd_order_entry_controller");
            show_list_view(2+'_'+theemaildata,'order_dtls_list_view','dyeing_details_container','requires/yd_order_entry_controller','setFilterGrid(\'list_view\',-1)');
            set_button_status(1, permission, 'fnc_yarn_dyeing',1);
            
        }
    }

    function fnc_load_party(type,within_group)
    {
        if ( form_validation('cbo_company_name','Company')==false )
        {
            $('#cbo_within_group').val(1);
            return;
        }
        //$('#txtOrderDeliveryDate_1').val($('#txt_delivery_date').val());
        var company = $('#cbo_company_name').val();
        var party_name = $('#cbo_party_name').val();
        var location_name = $('#cbo_location_name').val();
        
        if(within_group==1 && type==1)
        {
            load_drop_down( 'requires/yd_order_entry_controller', company+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
            
            $('#txt_order_no').removeAttr("onDblClick").attr("onDblClick","openmypage_order();");
            $('#txt_ex_rate').attr('readonly',true);
            $('#txt_order_no').attr('readonly',true);
            $('#txt_order_no').attr('placeholder','Browse');
            
            
        }
        else if(within_group==2 && type==1)
        {
            load_drop_down( 'requires/yd_order_entry_controller', company+'_'+2, 'load_drop_down_buyer', 'buyer_td' );
            $('#txt_order_no').removeAttr('onDblClick','onDblClick');
            
           
        }
        else if(within_group==1 && type==2)
        {
            load_drop_down( 'requires/yd_order_entry_controller', party_name+'_'+2, 'load_drop_down_location', 'party_location_td' ); 
            
        } 
        
            /*if(within_group==2)
            {
                var uom = $('#cboUom_1').val();
                fnc_load_uom(1,uom);
            }
            else if(within_group==1)
            {
                var uom = $('#cboUom_1').val();
                fnc_load_uom(1,uom);
                
            }*/
    }

    function fnc_load_party(type, within_group) {
        if ( form_validation('cbo_company_name','Company')==false ) {
            $('#cbo_within_group').val(1);
            return;
        }
        var company = $('#cbo_company_name').val();
        var party_name = $('#cbo_party_name').val();
        var location_name = $('#cbo_location_name').val();
        
        if(within_group==1 && type==1) {
            load_drop_down( 'requires/yd_order_entry_controller', company+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
        }
        else if(within_group==2 && type==1) {
            load_drop_down( 'requires/yd_order_entry_controller', company+'_'+2, 'load_drop_down_buyer', 'buyer_td' );
        }
        else if(within_group==1 && type==2) {
            load_drop_down( 'requires/yd_order_entry_controller', party_name+'_'+2, 'load_drop_down_location', 'party_location_td' );
        }
    }

    function workorder_browse() 
    {
        if (form_validation('cbo_company_name', 'Company Name') == false) {
            return;
        }
        var company = $("#cbo_company_name").val();
        page_link = 'requires/yd_order_entry_controller.php?action=order_popup&company=' + company;

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Yarn Dyeing Order Search', 'width=950px, height=450px, center=1, resize=0, scrolling=0', '../');

        emailwindow.onclose = function () 
        {
            var theform = this.contentDoc.forms[0];
            var sys_number = this.contentDoc.getElementById("hidden_sys_number").value.split("_");
            if (sys_number[0] != "") {
                //alert(sys_number);
                var order_id = sys_number[0];
                var within_group = $('#cbo_within_group').val();
                freeze_window(5);
                
                get_php_form_data(order_id, "populate_master_from_data", "requires/yd_order_entry_controller");
                show_list_view(1+'_'+order_id+'_'+0+'_'+within_group+'_'+0,'order_dtls_list_view','dyeing_details_container','requires/yd_order_entry_controller','setFilterGrid(\'list_view\',-1)');

                set_button_status(0, permission, 'fnc_yarn_dyeing', 1, 1);
                release_freezing();
                 /*$("#txt_within_group").val(sys_number[2]);
                get_php_form_data(sys_number[0], "populate_master_from_data", "requires/yarn_dyeing_charge_booking_sales_controller");*/
                //show_list_view(sys_number[0], 'show_dtls_list_view', 'list_container', 'requires/yarn_dyeing_charge_booking_sales_controller', '');
               
                //$('#cbo_company_name').attr('disabled', true);
            }
        }
    }


    function sum_total_qnty(id)
    {

        var orderQty=$("#txtOrderqty_"+id).val()*1;
        var processLoss=$("#txtProcessLoss_"+id).val()*1;
        var totalQty=orderQty/100+processLoss+orderQty;
        $("#txtTotalqty_"+id).val(totalQty);
    }
	function fnc_load_wo(type)
	{
		if(type==1)
		{
			$("#td_check_box").hide();
		}
		else
		{
			 $("#td_check_box").show();
		}
	}
	function fnc_wo_check(type)
	{
		//alert(type);
		if ( document.getElementById('txt_check_box').checked==true)
		{
			document.getElementById('txt_check_box').value=1;
            var within_group = $('#cbo_within_group').val();
			// alert(document.getElementById('txt_check_box').value);
			 //txt_order_no
			 $('#txt_order_no').removeAttr("ondblclick").attr("placeholder","");
			  $('#txt_order_no').removeAttr("readonly");
			  $('#txtItemColor_1').removeAttr("readonly");//txtYarnColor
			  $('#txtnoBag_1').removeAttr("readonly");
			  $('#txtYarnColor_1').removeAttr("readonly");
			  $('#txtOrderqty_1').removeAttr("readonly");
			   $('#txt_delivery_date').removeAttr("disabled");
			  
			  load_drop_down( 'requires/yd_order_entry_controller', within_group+'_'+1, 'load_drop_down_count', 'count_td' );
			  load_drop_down( 'requires/yd_order_entry_controller', 1, 'load_drop_down_yarn_type', 'yarn_type_td' );
			  load_drop_down( 'requires/yd_order_entry_controller', 1, 'load_drop_down_composition', 'composition_td' );
			//alert(chk );
		}
		else if(document.getElementById('txt_check_box').checked==false)
		{
			document.getElementById('txt_check_box').value=0;
			 $('#txt_order_no').removeAttr("placeholder").attr("placeholder","Browse");
			 $('#txt_order_no').attr("onDblClick","workorder_browse();");
			  $('#txt_order_no').attr("readonly","readonly");
			  $('#txtItemColor_1').attr("readonly","readonly");
			  $('#txtnoBag_1').attr("readonly","readonly");
			  $('#txtYarnColor_1').attr("readonly","readonly");
			  $('#txtOrderqty_1').attr("readonly","readonly");
			  $('#txt_delivery_date').attr("disabled","disabled");
			  
			  load_drop_down( 'requires/yd_order_entry_controller', 0, 'load_drop_down_count', 'count_td' );
			  load_drop_down( 'requires/yd_order_entry_controller', 0, 'load_drop_down_yarn_type', 'yarn_type_td' );
			  load_drop_down( 'requires/yd_order_entry_controller', 0, 'load_drop_down_composition', 'composition_td' );
			  
			// alert(document.getElementById('txt_check_box').value);
		}
	}
	function fnc_amount_cal(type)
	{
		if(type==1)
		{
		  var rate=$('#txtRate_1').val();
		  var qty= $('#txtOrderqty_1').val();
		   $('#txtAmount_1').val(qty*rate);
		}
		
	}
    
</script>
</head>

<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission); ?>
        <form name="dyeingorderentry_1" id="dyeingorderentry_1" autocomplete="off"> 
            <fieldset style="width:1150px;">
            <legend>Yarn Dyeing Order Entry</legend>
                <table width="1130" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                    <tr>
                        <td colspan="3" align="right"><strong>Job No</strong></td>
                        <td colspan="2">
                            <input class="text_boxes" type="text" name="txt_job_no" id="txt_job_no" placeholder="Double Click" onDblClick="openmypage_job();" style="width:140px;" readonly />
                        </td>
                    </tr>
                    <tr> </tr>
                    <tr>
                        <td width="110" class="must_entry_caption">Company Name </td>
                        <td width="160"><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/yd_order_entry_controller', this.value+'_'+1, 'load_drop_down_location', 'location_td'); fnc_load_party(1,document.getElementById('cbo_within_group').value);"); ?>
                        </td>
                        <td width="110">Location Name</td>
                        <td width="160" id="location_td"><? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", $selected, "" ); ?></td>
                        <td width="110" class="must_entry_caption">Within Group</td>
                        <td><?php echo create_drop_down( "cbo_within_group", 150, $yes_no,"", 0, "--  --", 0, "fnc_load_party(1,this.value);fnc_load_wo(this.value); " ); ?> &nbsp;
                        <b id="td_check_box" style="display:none">
                        <input class="text_boxes" type="checkbox" name="txt_check_box" id="txt_check_box"  style="width:60px;" value="0" onClick="fnc_wo_check(1);"  /></b></td>
                        
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Party</td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_load_party(2,document.getElementById('cbo_within_group').value);"); ?></td>

                        <td id="td_party_location">Party Location</td>
                        <td id="party_location_td"><? echo create_drop_down( "cbo_party_location", 150, $blank_array,"", 1, "-- Select Location --", $selected, "",1 ); ?></td>
                         <td class="must_entry_caption">Ord. Receive Date</td>
                        <td><input type="text" name="txt_order_receive_date"  style="width:140px"  id="txt_order_receive_date" value="<? echo date("d-m-Y")?>" class="datepicker" /></td>
                    </tr> 
                    <tr>
                        <td>Delivery Date</td>
                        <td> <input type="text" name="txt_delivery_date" id="txt_delivery_date"  style="width:140px"  class="datepicker" value="" /> </td>
                        <td>Rcv. Start Date</td>
                        <td><input type="text" name="txt_rec_start_date" id="txt_rec_start_date" style="width:140px" class="datepicker" value="" placeholder="Material Rcv. Start Date" /></td>

                        <td>Rcv. End Date</td>
                        <td><input type="text" name="txt_rec_end_date" id="txt_rec_end_date" style="width:140px" class="datepicker" value="" placeholder="Material Rcv. End Date" /></td>
                    </tr>
                    <tr>
                        <td  class="must_entry_caption">Currency</td>
                        <td><? echo create_drop_down( "cbo_currency", 150, $currency,"", 1, "-- Select Currency --", $selected,"", 2,"" ); ?></td>
                        <td width="110" ><strong>WO No</strong></td>
                        <td width="160">
                            <input class="text_boxes"  type="text" name="txt_order_no" id="txt_order_no" onDblClick="workorder_browse();" placeholder="Browse" style="width:140px;" readonly />
                            <input class="text_boxes" type="hidden" name="update_id" id="update_id" readonly />
                            <input type="hidden" name="hid_order_id" id="hid_order_id" readonly/>
                            <input type="hidden" name="hid_booking_type" id="hid_booking_type" value="0" readonly/>
                            <input type="hidden" name="hid_is_without_order" id="hid_is_without_order" readonly/>
                        </td>
                        <td >Remarks</td>
                        <td><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:140px"  placeholder="Entry" />
                        </td>
                    </tr> 
                </table>
        </fieldset>
        <fieldset style="width:1550px;">
           <legend>YD Order Entry Details</legend>
                <table width="1550px" cellpadding="0" cellspacing="2" border="0" class="rpt_table" rules="all" id="tbl_dtls_yarn_dyeing">
                    <thead class="form_table_header">
                    	<th width="90" id="styleRef_td">Style</th>
                        <th width="90"id="saleOrder">Sales order no</th>
                        <th width="90" id="process_td">Process</th>
                        <th width="90" class="must_entry_caption">Lot</th>
                        <th width="90"  class="must_entry_caption" >Count</th>
                        <th width="70" id="" class="must_entry_caption">Yarn Type</th>
                        <th width="70" id="" class="must_entry_caption">Yarn Composition</th>
                        <th width="60" class="must_entry_caption" >Item Color</th>
                        <th width="60" class="must_entry_caption" id="">Y/D Color</th>
                        <th width="80" >CSP</th>
                        <th width="80" class="">No of Bag</th>
                        <th width="50">Cone Per Bag</th>
                        <th width="60" >No of Cone</th>
                        <th width="50">AVG. Wt. Per Cone</th>
                        <th width="60" class="must_entry_caption">Uom</th>
                        <th width="60" class="must_entry_caption">Order Qty</th>
                        <th width="60" >Rate</th>
                        <th width="60" class="">Amount </th>
                        <th width="60" >Process Loss %</th>
                        <th width="60">Total Qnty</th>
                        <!-- <th></th> -->
                    </thead>
                    <tbody id="dyeing_details_container">
                        <tr>
                        	<td><input id="txtstyleRef_1" name="txtstyleRef[]" type="text" class="text_boxes" style="width:80px"/></td>
                            <td><input id="txtsaleOrder_1" name="txtsaleOrder[]" type="text" class="text_boxes_numeric" style="width:80px" placeholder=""/>
                            <input id="txtsaleOrderID_1" name="txtsaleOrderID[]" type="hidden" class="text_boxes_numeric" style="width:80px" placeholder=""/>
                        	<input id="txtProductID_1" name="txtProductID[]" type="hidden" class="text_boxes_numeric" style="width:80px" placeholder=""/></td>
                            <td><? echo create_drop_down( "txtprocess_1", 100, $yarn_dyeing_process,"", 1, "-- Select --",0,"",0,'','','','','','',"txtprocess[]"); ?></td> 
                        	<td><input id="txtlot_1" name="txtlot[]" type="text" class="text_boxes" style="width:80px" placeholder=""/></td>
                        	<td id="count_td"><input id="txtcount_1" name="txtcount[]" type="text" class="text_boxes_numeric" style="width:80px" placeholder=""/></td>
                        	<td id="yarn_type_td"><input id="txtydtype_1" name="txtydtype[]" type="text" class="text_boxes" style="width:60px" placeholder=""/></td>
                        	<td id="composition_td"><input id="txtydComposition_1" name="txtydComposition[]" type="text" class="text_boxes" style="width:60px" placeholder=""/></td>
                            <td><input name="txtItemColor[]" id="txtItemColor_1" type="text" class="text_boxes" style="width:50px" placeholder="" readonly />
                            	<input name="txtItemColorID[]" id="txtItemColorID_1" type="hidden" class="text_boxes" style="width:50px" />
                            </td>
                            <td><input name="txtYarnColor[]" id="txtYarnColor_1" type="text" class="text_boxes" style="width:50px" placeholder="" readonly /><input name="txtYarnColorID[]" id="txtYarnColorID_1" type="hidden" class="text_boxes" style="width:50px" /></td>
                            <td><input id="txtCSP_1" name="txtCSP[]" type="text" class="text_boxes_numeric" style="width:70px" placeholder=""/></td>
                            <td><input name="txtnoBag[]" id="txtnoBag_1" type="text" class="text_boxes_numeric" style="width:70px" readonly placeholder="" /></td>
                            <td><input name="txtConeBag[]" id="txtConeBag_1" type="text" class="text_boxes_numeric" style="width:50px" placeholder="Write" /></td>
                            <td><input name="txtNoCone[]" id="txtNoCone_1" class="text_boxes_numeric" type="text"  style="width:50px"  placeholder="Write" /></td>
                            <td><input name="txtAVG[]" id="txtAVG_1" type="text"  class="text_boxes_numeric" style="width:50px" placeholder="Write" /></td>
                            <td><? echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"", 1, "-- Select --",2,"", "","2,1,12,23",'','','','','',"cboUom[]" ); ?></td>
                            <td><input name="txtOrderqty[]" id="txtOrderqty_1" type="text" style="width:50px"  class="text_boxes_numeric" readonly placeholder="" /></td> 
                            <td><input name="txtRate[]" id="txtRate_1" type="text" style="width:50px"  class="text_boxes_numeric" onBlur="fnc_amount_cal(1)"  placeholder="" /></td> 
                            <td><input name="txtAmount[]" id="txtAmount_1" type="text" style="width:50px"  class="text_boxes_numeric"  placeholder="" /></td> 
                            <td><input name="txtProcessLoss[]" id="txtProcessLoss_1" type="text"  class="text_boxes_numeric" style="width:50px" placeholder="" onKeyUp="sum_total_qnty(1);" /></td> 
                            <td><input type="text" name="txtTotalqty[]" id="txttxtTotalqty_1" class="text_boxes_numeric" style="width:50px"  placeholder="" /><input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_1" class="text_boxes_numeric" style="width:50px"  readonly /></td>

                            <!-- onClick="openmypage_avg_wt(1,'0',1)" placeholder="Browse" <td><input type="button" name="btnremarks_1" id="btnremarks_1" class="formbuttonplasminus" value="RMK" onClick="openmypage_remarks(1);" />
                            	<input type="hidden" name="txtremarks_1" id="txtremarks_1" class="text_boxes" />
                            </td> -->
                            <!-- <td width="65">
								<input type="button" id="increase_1" name="increase[]" style="width:18px" class="formbuttonplasminus" value="+" onClick="add_dtls_tr(1)" />
								<input type="button" id="decrease_1" name="decrease[]" style="width:18px" class="formbuttonplasminus" value="-" onClick="fnc_delet_dtls_tr(1);" />
							</td> -->
                        </tr>                     
                    </tbody>
                </table>
                <table width="1550px" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td align="center" colspan="11" class="button_container">
                        	<? echo load_submit_buttons( $permission, "fnc_yarn_dyeing", 0,0,"fnResetForm();",1); ?>
                        </td>
                    </tr>   
                </table>
            </fieldset> 
        </form>                         
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>