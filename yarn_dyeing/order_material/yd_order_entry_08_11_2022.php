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

    <?
    if($_SESSION['logic_erp']['mandatory_field'][374]!="")
	{
		$mandatory_field_arr= json_encode( $_SESSION['logic_erp']['mandatory_field'][374] );
		echo "var mandatory_field_arr= ". $mandatory_field_arr . ";\n";
	}
	?>

    var use_for_value = [<? echo substr(return_library_autocomplete( "select use_for from yd_ord_dtls group by use_for ", "use_for" ), 0, -1); ?> ];

    function fnc_yarn_dyeing( operation )
    {
        var delete_master_info=0; var i=0;
        var data_all="";
        //var process = $("#cbo_process_name").val();
        var cbo_within_group = $("#cbo_within_group").val(); 
         
		 
		 if(cbo_within_group==1)
		 {  
			if ( form_validation('cbo_company_name*cbo_within_group*cbo_party_name*cbo_currency*txt_order_receive_date','Company*Within Group*Party*Currency*Order Receive Date')==false ){
				return;
			}
		 }
		 else
		 {
			if ( form_validation('cbo_company_name*cbo_within_group*cbo_party_name*cbo_currency*txt_order_receive_date*cbo_order_type','Company*Within Group*Party*Currency*Order Receive Date*Order Type')==false )
			{
				return;
			} 
		 }

         if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][374]);?>'){
			if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][374]);?>','<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][374]);?>')==false)
			{
				return;
			}
		}
        
        var txt_job_no          = $('#txt_job_no').val();
        var cbo_company_name    = $('#cbo_company_name').val();
        var cbo_location_name   = $('#cbo_location_name').val();
        var txt_order_receive_date = $('#txt_order_receive_date').val();
        var cbo_within_group    = $('#cbo_within_group').val();
        var cbo_party_name      = $('#cbo_party_name').val();
        var cbo_party_location  = $('#cbo_party_location').val();
        var cbo_currency        = $('#cbo_currency').val();
        var exchange_rate        = $('#txt_exchange_rate').val();
        var txt_delivery_date   = $('#txt_delivery_date').val();
        var txt_rec_start_date  = $('#txt_rec_start_date').val();
        var txt_rec_end_date    = $('#txt_rec_end_date').val();
        var tag_pi_no    = $('#txt_tag_pi_no').val();
        var txt_order_no           = $('#txt_order_no').val();
        var cbo_order_type           = $('#cbo_order_type').val();
        var cbo_yd_type           = $('#cbo_yd_type').val();
        var cbo_yd_process           = $('#cbo_yd_process').val();
        var attention           = $('#attention').val();
        var cbo_team_leader           = $('#cbo_team_leader').val();
        var cbo_team_member           = $('#cbo_team_member').val();
        var party_ref           = $('#party_ref').val();
        var hid_order_id        = $('#hid_order_id').val();
        var txt_remarks         = $('#txt_remarks').val();

        var is_without_order    = $('#hid_is_without_order').val();
        var hid_booking_type    = $('#hid_booking_type').val();
        var update_id           = $('#update_id').val();
        var txt_deleted_id      = $('#txt_deleted_id').val();
		var txt_check_box       = $('#txt_check_box').val();
        var cbo_pro_type       = $('#cbo_pro_type').val();
        var txt_advance_job       = $('#txt_advance_job').val();
        var txt_yd_job_id       = $('#txt_yd_job_id').val();


        var txt_check_box_confirm = $('#txt_check_box_confirm').val();
        var txt_advance_job       = $('#txt_advance_job').val();
        var txt_tag_pi_no         = $('#txt_tag_pi_no').val();
        var balance_qty           = $('#txt_tag_pi_balance_qty').val()*1;

        if(txt_check_box_confirm!='' && txt_advance_job!='' && txt_tag_pi_no!='')
        {

            var row_num = $('#tbl_dtls_yarn_dyeing tbody tr').length;

            var tot_amount = 0;
            for(var j=1; j<=row_num; j++)
            {
                tot_amount += $('#txtOrderqty_'+j).val()*1;
            }

            if(tot_amount>balance_qty)
            {
                alert("Total Order Quantity Can Not Be Greater Than Balance Quantity!!! Balance Quantity ="+balance_qty);
                return;
            }
        }
		
		if( document.getElementById('txt_check_box').checked==true)
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
		
		
		
	function fnc_wo_check_confirm(type)
	{
		//alert(document.getElementById('txt_check_box_confirm').checked==true);
		if(document.getElementById('txt_check_box_confirm').checked==true)
		{
			document.getElementById('txt_check_box_confirm').value=1;
			document.getElementById('txt_check_box_advance').value=0;
			$('#txt_check_box_confirm').attr("checked",true); 
			$('#txt_check_box_advance').attr("checked",false); 
		}
		else if(document.getElementById('txt_check_box_confirm').checked==false)
		{
			document.getElementById('txt_check_box_confirm').value=1;
			$('#txt_check_box_confirm').attr("checked",true); 
			
			//document.getElementById('txt_check_box_confirm').value=0;
			//document.getElementById('txt_check_box_advance').value=1;
			//$('#txt_check_box_advance').attr("checked",true);
		}
		var cbo_order_type = $('#cbo_order_type').val();
		fnc_load_order_type(cbo_order_type);
		 
	}
	function fnc_wo_check_advance(type)
	{
		if( document.getElementById('txt_check_box_advance').checked==true)
		{
			 document.getElementById('txt_check_box_advance').value=1;
			 $('#txt_check_box_advance').attr("checked",true);
			 document.getElementById('txt_check_box_confirm').value=0;
  			 $('#txt_check_box_confirm').attr("checked",false); 
		}
		else if(document.getElementById('txt_check_box_advance').checked==false)
		{
			
			 document.getElementById('txt_check_box_advance').value=1;
			 $('#txt_check_box_advance').attr("checked",true);
			//document.getElementById('txt_check_box_advance').value=0;
			//document.getElementById('txt_check_box_confirm').value=1;
			//$('#txt_check_box_confirm').attr("checked",true); 
		}
		 var cbo_order_type = $('#cbo_order_type').val();
		fnc_load_order_type(cbo_order_type);
		 
	}
		
		var txt_check_box       		= $('#txt_check_box').val();
		var txt_check_box_confirm       = $('#txt_check_box_confirm').val();
		var txt_check_box_advance       = $('#txt_check_box_advance').val();
		//alert(3); 
		//return;
		
        var j=0; var check_field=0;
            
        $("#tbl_dtls_yarn_dyeing tbody tr").each(function()
        {
            var txtstyleRef         = $(this).find('input[name="txtstyleRef[]"]').val();
            var txtsaleOrder        = $(this).find('input[name="txtsaleOrder[]"]').val();
            var txtsaleOrderID      = $(this).find('input[name="txtsaleOrderID[]"]').val();
            var txtProductID        = $(this).find('input[name="txtProductID[]"]').val();
            var txtbuyerbuyer          = $(this).find('input[name="txtbuyerBuyer[]"]').val();
            var txtprocess          = $(this).find('input[name="txtprocess[]"]').val();
            var txtlot              = $(this).find('input[name="txtlot[]"]').val();
            var txtcounttype              = $(this).find('select[name="txtcountType[]"]').val();
           
            var cboCount            = $(this).find('select[name="cboCount[]"]').val();
            var cboYarnType         = $(this).find('select[name="cboYarnType[]"]').val();
            var cboComposition      = $(this).find('select[name="cboComposition[]"]').val();
            var cboUom              = $(this).find('select[name="cboUom[]"]').val();
  			var txtItemColor          = $(this).find('select[name="txtItemColor[]"]').val();
            var txtYarnColor        = $(this).find('input[name="txtYarnColor[]"]').val();
            var txtItemColorID      = $(this).find('input[name="txtItemColorID[]"]').val();
            var txtYarnColorID      = $(this).find('input[name="txtYarnColorID[]"]').val();

            var txtCSP              = $(this).find('input[name="txtCSP[]"]').val();
            var txtnoBag            = $(this).find('input[name="txtnoBag[]"]').val();
            var txtConeBag          = $(this).find('input[name="txtConeBag[]"]').val();
            var txtNoCone           = $(this).find('input[name="txtNoCone[]"]').val();//txtNoCone_1
            var txtAVG              = $(this).find('input[name="txtAVG[]"]').val();

            var txtOrderqty         = $(this).find('input[name="txtOrderqty[]"]').val();
            var txtRate             = $(this).find('input[name="txtRate[]"]').val();
            var txtAmount           = $(this).find('input[name="txtAmount[]"]').val();
            var txtProcessLoss      = $(this).find('input[name="txtProcessLoss[]"]').val();
            var txtadjtype          = $(this).find('select[name="txtadjType[]"]').val()*1;
            var txtTotalqty         = $(this).find('input[name="txtTotalqty[]"]').val();
            var hdnDtlsUpdateId     = $(this).find('input[name="hdnDtlsUpdateId[]"]').val();
            var txtprocessname      = $(this).find('input[name="txtprocessname[]"]').val();
            var useFor              = $(this).find('input[name="txtUseFor[]"]').val();
            var appRef              = $(this).find('input[name="txtAppRef[]"]').val();
            var txtShade            = $(this).find('input[name="txtShade[]"]').val();
            var txtShadeId          = $(this).find('input[name="txtShadeId[]"]').val();
            var txtShadeMstId       = $(this).find('input[name="txtShadeMstId[]"]').val();

			//alert(txtNoCone+'='+txtAVG);
           
            /*var hdnDtlsdata       = $(this).find('input[name="hdnDtlsdata[]"]').val();
            var hdnDload_drop_down_counttlsUpdateId     = $(this).find('input[name="hdnDtlsUpdateId[]"]').val();
            var hdnbookingDtlsId    = $(this).find('input[name="hdnbookingDtlsId[]"]').val();
            var txtDeletedId        = $(this).find('input[name="txtDeletedId[]"]').val();
            var txtIsWithOrder      = $(this).find('input[name="txtIsWithOrder[]"]').val();
            var txtIsRevised        = $(this).find('input[name="txtIsRevised[]"]').val();*/
            //txt_total_amount  += $(this).find('input[name="amount[]"]').val()*1;
            //alert(cboSection);
            j++;
			
            
            if( (txtlot=='' || cboCount==0 || cboYarnType==0 || txtcounttype==0 || cboComposition==0 || txtItemColor==0 || txtYarnColor==''|| cboUom==0 || txtOrderqty=='' || txtShade==''))
            {   
                
                if(txt_check_box==0 || cbo_within_group==1)
				{
                    if(txtlot=='')
					{
                        alert('Please Fill up Lot');
                        check_field=1 ; return;
                    }
					else if(txtcounttype==0)
					{
						alert('Please Select Count Type ');
						check_field=1 ; return;
					}
					else if(txtItemColor==0)
					{
						alert('Please Fill up Item Color ');
						check_field=1 ; return;
					}
                }

                if(cboCount==0)
				{
                    alert('Please Select Count');
                    check_field=1 ; return;
                }
				else if(cboYarnType==0)
				{
                    alert('Please Select Yarn Type ');
                    check_field=1 ; return;
                }
				else if(cboComposition==0)
				{
                    alert('Please Select Yarn Composition');
                    check_field=1 ; return;
                }
				else if(txtYarnColor==''){
                    alert('Please Fill up Y/D Color');
                    check_field=1 ; return;
                }
				else if(cboUom==0){
                    alert('Please Select Uom');
                    check_field=1 ; return;
                }else if(txtOrderqty==''){
                    alert('Please Fill up Order Qty ');
                    check_field=1 ; return;
                }
                
            }
			//+"&txtItemColor_" + j + "='" + $('#txtItemColor_'+i).val();
            i++;
			//alert(txt_check_box);
            data_all += "&txtstyleRef_" + j + "='" + txtstyleRef + "'&txtsaleOrder_" + j + "='" + txtsaleOrder+ "'&txtsaleOrderID_" + j + "='" + txtsaleOrderID+ "'&txtProductID_" + j + "='" + txtProductID + "'&txtprocess_" + j + "='" + txtprocess + "'&txtlot_" + j + "='" + txtlot + "'&cboCount_" + j + "='" + cboCount  + "'&cboYarnType_" + j + "='" + cboYarnType + "'&cboComposition_" + j + "='" + cboComposition + "'&txtItemColor_" + j + "='" + txtItemColor + "'&txtYarnColor_" + j + "='" + txtYarnColor + "'&txtCSP_" + j + "='" + txtCSP + "'&txtnoBag_" + j + "='" + txtnoBag + "'&txtConeBag_" + j + "='" + txtConeBag + "'&txtNoCone_" + j + "='" + txtNoCone + "'&txtAVG_" + j + "='" + txtAVG + "'&cboUom_" + j + "='" + cboUom +"'&txtOrderqty_" + j + "='" + txtOrderqty +"'&txtRate_" + j + "='" + txtRate +"'&txtAmount_" + j + "='" + txtAmount +"'&txtProcessLoss_" + j + "='" + txtProcessLoss+"'&txtTotalqty_" + j + "='" + txtTotalqty+"'&txtbuyerbuyer_" + j + "='" + txtbuyerbuyer+"'&txtcounttype_" + j + "='" + txtcounttype+"'&txtprocessname_" + j + "='" + txtprocessname+"'&txtadjtype_" + j + "='" + txtadjtype+"'&hdnDtlsUpdateId_" + j + "='" + hdnDtlsUpdateId +"'&useFor_" + j + "='" + useFor+"'&appRef_" + j + "='" + appRef +"'&txtShade_" + j + "='" + txtShade +"'&txtShadeId_" + j + "='" + txtShadeId +"'&txtShadeMstId_" + j + "='" + txtShadeMstId + "'";
        });

        if(check_field==0){
            var data="action=save_update_delete&operation="+operation+'&total_row='+i+'&txt_job_no='+txt_job_no+'&cbo_company_name='+cbo_company_name+'&cbo_location_name='+cbo_location_name+'&cbo_within_group='+cbo_within_group+'&cbo_party_name='+cbo_party_name+'&cbo_party_location='+cbo_party_location+'&cbo_currency='+cbo_currency+'&txt_order_receive_date='+txt_order_receive_date+'&txt_delivery_date='+txt_delivery_date+'&txt_rec_start_date='+txt_rec_start_date+'&txt_rec_end_date='+txt_rec_end_date+'&txt_order_no='+txt_order_no+'&hid_order_id='+hid_order_id+'&txt_remarks='+txt_remarks+'&is_without_order='+is_without_order+'&hid_booking_type='+hid_booking_type+'&update_id='+update_id+'&txt_check_box='+txt_check_box+'&txt_exchange_rate='+exchange_rate+'&txt_tag_pi_no='+tag_pi_no+'&cbo_order_type='+cbo_order_type+'&cbo_yd_type='+cbo_yd_type+'&cbo_yd_process='+cbo_yd_process+'&attention='+attention+'&cbo_team_leader='+cbo_team_leader+'&cbo_team_member='+cbo_team_member+'&party_ref='+party_ref+data_all+'&txt_deleted_id='+txt_deleted_id+'&cbo_pro_type='+cbo_pro_type+'&txt_check_box_confirm='+txt_check_box_confirm+'&txt_check_box_advance='+txt_check_box_advance+'&txt_advance_job='+txt_advance_job+'&txt_yd_job_id='+txt_yd_job_id;
			 
           // alert (data); return; 
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
            
            if(trim(response[0])=='20'){
                alert(response[1]);
                release_freezing();return;
            }
			
			/* if(trim(response[0])=='18'){
                alert("Receive Found ."+"\n So Update/Delete Not Possible"+"\n Job No.:"+response[1]);
                release_freezing();return;
            }*/
			
			if(trim(response[0])=='40'){
                alert("Order Qty cannot be less than Receive Qty"+"\n Order Qty :"+response[1]);
 				$('#txtOrderqty_'+response[2]).focus();
                release_freezing();return;
            }
			/*else if(trim(response[0])=='26'){
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
               // $('#txt_order_no').attr('disabled',true);
                $('#cbo_within_group').attr('disabled',true);
                $('#cbo_company_name').attr('disabled',true);
               // $('#txt_delivery_date').attr('disabled',true);
                $('#cbo_party_name').attr('disabled',true);
                $('#cbo_party_location').attr('disabled',true);
                $('#txt_order_receive_date').attr('disabled',true);

                disable_enable_fields('txt_yd_job_id*txt_advance_job*txt_tag_pi_no',1);

                show_list_view(2+'_'+update_id+'_'+job_no+'_'+within_group+'_'+order_no,'order_dtls_list_view','dyeing_details_container','requires/yd_order_entry_controller','setFilterGrid(\'list_view\',-1)');
                set_button_status(1, permission, 'fnc_yarn_dyeing',1);
                //btn_load_change_bookings();

                var row_num = $('#tbl_dtls_yarn_dyeing tbody tr').length;

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
        
        emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=1390px, height=420px, center=1, resize=0, scrolling=0','../')
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var theemaildata=this.contentDoc.getElementById("selected_job").value;
            var ex_data=theemaildata.split('_');
            get_php_form_data(ex_data[0], "populate_job_master_from_data", "requires/yd_order_entry_controller");
            show_list_view(2+'_'+theemaildata,'order_dtls_list_view','dyeing_details_container','requires/yd_order_entry_controller','setFilterGrid(\'list_view\',-1)');
            set_button_status(1, permission, 'fnc_yarn_dyeing',1);

            var row_num = $('#tbl_dtls_yarn_dyeing tbody tr').length;

            for(var i =1; i<=row_num;i++)
            {
                //set_multiselect('txtprocess_'+i,'0','0','','0');
            }
            
        }
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

            load_drop_down( 'requires/yd_order_entry_controller', 0, 'load_drop_down_location', 'party_location_td' );
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
        var cbo_within_group = $("#cbo_within_group").val();
        page_link = 'requires/yd_order_entry_controller.php?action=order_popup&company=' + company+'&cbo_within_group=' + cbo_within_group;

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Yarn Dyeing Order Search', 'width=950px, height=450px, center=1, resize=0, scrolling=0', '../');

        emailwindow.onclose = function () 
        {
            var theform = this.contentDoc.forms[0];
            var sys_number = this.contentDoc.getElementById("hidden_sys_number").value.split("_");
            if (sys_number[0] != "") {
                //alert(sys_number);
                var order_id = sys_number[0];
                var within_group = $('#cbo_within_group').val();
				$('#hid_is_sales_check').val(sys_number[3]);
                freeze_window(5);
                
                get_php_form_data(order_id, "populate_master_from_data", "requires/yd_order_entry_controller");
                show_list_view(1+'_'+order_id+'_'+0+'_'+within_group+'_'+0,'order_dtls_list_view','dyeing_details_container','requires/yd_order_entry_controller','setFilterGrid(\'list_view\',-1)');
                set_button_status(0, permission, 'fnc_yarn_dyeing', 1, 1);

                var row_num = $('#tbl_dtls_yarn_dyeing tbody tr').length;

                for(var i =1; i<=row_num;i++)
                {
                   // set_multiselect('txtprocess_'+i,'0','0','','0');
                }
                release_freezing();
                 /*$("#txt_within_group").val(sys_number[2]);
                get_php_form_data(sys_number[0], "populate_master_from_data", "requires/yarn_dyeing_charge_booking_sales_controller");*/
                //show_list_view(sys_number[0], 'show_dtls_list_view', 'list_container', 'requires/yarn_dyeing_charge_booking_sales_controller', '');
               
                $('#cbo_company_name').attr('disabled', true);
                $('#cbo_within_group').attr('disabled', true);
                $('#cbo_party_name').attr('disabled', true);
            }
        }
    }


    function sum_total_qnty(id)
    {
        //var orderQty=$("#txtOrderqty_"+id).val()*1;
        //var processLoss=$("#txtProcessLoss_"+id).val()*1;
       // var totalQty=orderQty/100+processLoss+orderQty;
        var rate=$('#txtRate_'+id).val()*1;
        var qty=$('#txtOrderqty_'+id).val()*1;
        var adj_type = $('#txtadjType_'+id).val()*1;
        var txtProcessLoss = $('#txtProcessLoss_'+id).val()*1;
        $('#txtAmount_'+id).val(qty*rate);

        var total_qty = 0;
        
        if(adj_type==1){
            var adj_qty = (qty*txtProcessLoss)/100;
            var total_qty = qty+adj_qty;
        }
        else if(adj_type==2){
            var adj_qty = (qty*txtProcessLoss)/100;
            var total_qty = qty-adj_qty;
        }
        //$('#txtTotalqty_'+id).val(total_qty);
    }
	function fnc_load_wo(type)
	{
		if(type==1)
		{
			$("#td_check_box").hide();
			$("#td_check_box_advance").hide();
			$("#td_check_box_confirm").hide();
			 
		}
		else
		{
			$("#td_check_box").show();
			$("#td_check_box_advance").show();
			$("#td_check_box_confirm").show();
            $('#cbo_within_group').attr("disabled","disabled");
			fnc_wo_check(type);

            //load_drop_down( 'requires/yd_order_entry_controller', 0, 'load_drop_down_count', 'count_td' );
            //load_drop_down( 'requires/yd_order_entry_controller', 0, 'load_drop_down_yarn_type', 'yarn_type_td' );
            //load_drop_down( 'requires/yd_order_entry_controller', 0, 'load_drop_down_composition', 'composition_td' );
		}
	}

	function fnc_wo_check(type)
	{
		
		if ( document.getElementById('txt_check_box').checked==true)
		{
			document.getElementById('txt_check_box').value=1;
            var within_group = $('#cbo_within_group').val(); 
			var hid_is_sales_check = $('#hid_is_sales_check').val();
			// alert(document.getElementById('txt_check_box').value);
			 //txt_order_no
			 $('#txt_order_no').removeAttr("ondblclick").attr("placeholder","Write");
			 // $('#txt_order_no').removeAttr("readonly");
			  $('#txtItemColor_1').removeAttr("readonly");//txtYarnColor
			  $('#txtnoBag_1').removeAttr("readonly");
			  $('#txtYarnColor_1').removeAttr("readonly");
			  //$('#txtOrderqty_1').removeAttr("readonly");
			  // $('#txt_delivery_date').removeAttr("disabled");
			  
			  load_drop_down( 'requires/yd_order_entry_controller', within_group+'_'+1+'_'+hid_is_sales_check, 'load_drop_down_count', 'count_td' );
			  load_drop_down( 'requires/yd_order_entry_controller', 1, 'load_drop_down_yarn_type', 'yarn_type_td' );
			  load_drop_down( 'requires/yd_order_entry_controller', 1, 'load_drop_down_composition', 'composition_td' );
			  
			  //$('#count_type').removeClass("must_entry_caption");
			  $('#count_type').html('Count Type'); 
			  $('#color_range').html('Color Range'); 
			 
			//alert(chk );
		}
		else if(document.getElementById('txt_check_box').checked==false)
		{
//alert(type);
			document.getElementById('txt_check_box').value=0;
			 $('#txt_order_no').removeAttr("placeholder").attr("placeholder","Browse");
			 $('#txt_order_no').attr("onDblClick","workorder_browse();");
			 var within_group = $('#cbo_within_group').val(); 
			  var hid_is_sales_check = $('#hid_is_sales_check').val();
			  //$('#txt_order_no').attr("readonly","readonly");
			  $('#txtItemColor_1').attr("readonly","readonly");
			  $('#txtnoBag_1').attr("readonly","readonly");
			  $('#txtYarnColor_1').attr("readonly","readonly");
			  //$('#txtOrderqty_1').attr("readonly","readonly");
			  //$('#txt_delivery_date').attr("disabled","disabled");
 			 // load_drop_down( 'requires/yd_order_entry_controller', 0, 'load_drop_down_count', 'count_td' );
 			  load_drop_down( 'requires/yd_order_entry_controller', within_group+'_'+1+'_'+hid_is_sales_check, 'load_drop_down_count', 'count_td' );
			  load_drop_down( 'requires/yd_order_entry_controller', 0, 'load_drop_down_yarn_type', 'yarn_type_td' );
			  load_drop_down( 'requires/yd_order_entry_controller', 0, 'load_drop_down_composition', 'composition_td' );
			  $('#count_type').css('color','blue');
			  $('#color_range').css('color','blue');
			// alert(document.getElementById('txt_check_box').value);
		}
	}
	
	
	function fnc_wo_check_confirm(type)
	{
		//alert(document.getElementById('txt_check_box_confirm').checked==true);
		if(document.getElementById('txt_check_box_confirm').checked==true)
		{
            $('#txt_advance_job').attr("disabled",false);
			document.getElementById('txt_check_box_confirm').value=1;
			document.getElementById('txt_check_box_advance').value=0;
			$('#txt_check_box_confirm').attr("checked",true); 
			$('#txt_check_box_advance').attr("checked",false); 
		}
		else if(document.getElementById('txt_check_box_confirm').checked==false)
		{
			document.getElementById('txt_check_box_confirm').value=1;
			$('#txt_check_box_confirm').attr("checked",true); 
			
			//document.getElementById('txt_check_box_confirm').value=0;
			//document.getElementById('txt_check_box_advance').value=1;
			//$('#txt_check_box_advance').attr("checked",true);
		}
		var cbo_order_type = $('#cbo_order_type').val();
		fnc_load_order_type(cbo_order_type);
		 
	}
	function fnc_wo_check_advance(type)
	{
		if( document.getElementById('txt_check_box_advance').checked==true)
		{
            var txt_advance_job = $('#txt_advance_job').val();

            if(txt_advance_job!=''){

                alert("You can Not Set Advance Job!!!");
                $('#txt_check_box_advance').attr("checked",false);
                return true;
            }
            else
            {
                disable_enable_fields('txt_advance_job',1);
                document.getElementById('txt_check_box_advance').value=1;
                $('#txt_check_box_advance').attr("checked",true);
                document.getElementById('txt_check_box_confirm').value=0;
                $('#txt_check_box_confirm').attr("checked",false);
            }
             
		}
		else if(document.getElementById('txt_check_box_advance').checked==false)
		{
			document.getElementById('txt_check_box_advance').value=1;
			$('#txt_check_box_advance').attr("checked",true);
			//document.getElementById('txt_check_box_advance').value=0;
			//document.getElementById('txt_check_box_confirm').value=1;
			//$('#txt_check_box_confirm').attr("checked",true); 
		}
		 var cbo_order_type = $('#cbo_order_type').val();
		fnc_load_order_type(cbo_order_type);
		 
	}
	
	function fnc_load_order_type(value)
	{
		
		
		
		 var cbo_within_group = $("#cbo_within_group").val(); 
 		 if(cbo_within_group==1)
		 {  
			 document.getElementById("OrderQty").style.color = "blue";
			 document.getElementById("OrderQty").innerHTML = "Order Qty"; 
			 document.getElementById("TotalQnty").innerHTML = "Total Qnty";	
		 }
		 else
		 {
			 if(value==2) 
			 {
				 document.getElementById("OrderQty").style.color = "blue";
				 document.getElementById("OrderQty").innerHTML = "Finish Qty"; 
				 document.getElementById("TotalQnty").innerHTML = "Grey Qty";	 
			 }
			 else  if(value==1)
			 
			 {
				  document.getElementById("OrderQty").style.color = "blue";
				  document.getElementById("OrderQty").innerHTML = "Grey Qty";
				  document.getElementById("TotalQnty").innerHTML = "Finish Qty";
			 }
			 else
			 {
				 
				document.getElementById("OrderQty").style.color = "blue";
			    document.getElementById("OrderQty").innerHTML = "Order Qty"; 
			    document.getElementById("TotalQnty").innerHTML = "Total Qnty"; 
				 
			 }
	 
		 }
		
		
		 
		 
		 
		  
		  
		
		if( document.getElementById('txt_check_box_advance').checked==true)
		{
			 document.getElementById('txt_check_box_advance').value=1;
  			 if(value==1) 
			 {
 				alert("Service Not Allow"); 
				document.getElementById('cbo_order_type').value=2;
				return;	 
			 }
		}
		
		//fnc_wo_check_confirm(2);
		//fnc_wo_check_advance(3);
		
		
	}
		
	
	function fnc_amount_cal(type)
	{
        var rate=$('#txtRate_'+type).val()*1;
        var qty=$('#txtOrderqty_'+type).val()*1;
        var adj_type = $('#txtadjType_'+type).val()*1;
        var txtProcessLoss = $('#txtProcessLoss_'+type).val()*1;
        $('#txtAmount_'+type).val(qty*rate);

        var total_qty = 0;

        
        if(adj_type==1){
            var adj_qty = (qty*txtProcessLoss)/100;
            var total_qty = qty+adj_qty;
        }
        else if(adj_type==2){
            var adj_qty = (qty*txtProcessLoss)/100;
            var total_qty = qty-adj_qty;
        }
        $('#txtTotalqty_'+type).val(total_qty);

        check_total_amount();
	}

    function check_total_amount()
    {

        var txt_check_box_confirm = $('#txt_check_box_confirm').val();
        var txt_advance_job       = $('#txt_advance_job').val();
        var txt_tag_pi_no         = $('#txt_tag_pi_no').val();
        var balance_qty           = $('#txt_tag_pi_balance_qty').val()*1;

        if(txt_check_box_confirm!='' && txt_advance_job!='' && txt_tag_pi_no!='')
        {

            var row_num = $('#tbl_dtls_yarn_dyeing tbody tr').length;

            var tot_amount = 0;
            for(var j=1; j<=row_num; j++)
            {
                tot_amount += $('#txtOrderqty_'+j).val()*1;
            }

            if(tot_amount>balance_qty)
            {
                alert("Total Order Quantity Can Not Be Greater Than Balance Quantity!!! Balance Quantity ="+balance_qty);
                return;
            }
        }

    }

    function fnc_addRow( i, table_id, tr_id )
    {
        var txt_check_box = $('#txt_check_box').val();
        var within_group = $('#cbo_within_group').val();
        if(within_group==1 || txt_check_box==0)
        {
            return;
        }
        else
        {
            var prefix=tr_id.substr(0, tr_id.length-1);
            var row_num = $('#tbl_dtls_yarn_dyeing tbody tr').length; 
            //alert(i+"**"+table_id+"**"+tr_id+"**"+row_num);
            row_num++;
            var txtprocess= $("#"+'txtprocess_'+i).val();
            var cboCount= $("#"+'cboCount_'+i).val();
            var cboComposition= $("#"+'cboComposition_'+i).val();
            var cboUom= $("#"+'cboUom_'+i).val();
            var clone= $("#"+tr_id+i).clone();
            clone.attr({
                id: tr_id + row_num,
            });
            
            clone.find("input,select").each(function(){

                $(this).attr({ 
                    'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
                    //'name': function(_, name) { var name=name.split("_"); return name[0] },
                    'name': function(_, name) { return name },
                    'value': function(_, value) { return value }
                });
            }).end();
            $("#"+tr_id+i).after(clone);
            //$('#rate_'+row_num).removeAttr("onKeyUp").attr("onKeyUp","calculate_amount("+row_num+")");
            
            $('#txtprocess_'+row_num).removeAttr("value").attr("value",txtprocess);
            $('#cboCount_'+row_num).removeAttr("value").attr("value",cboCount);
            $('#cboComposition_'+row_num).removeAttr("value").attr("value",cboComposition);
            $('#cboUom_'+row_num).removeAttr("value").attr("value",cboUom);
            
            $('#hdnDtlsdata_'+row_num).removeAttr("value").attr("value","");
            $('#txtShade_'+row_num).removeAttr("value").attr("value","");
            
            
            $('#txtnoBag_'+row_num).removeAttr("readonly");
            //$('#txtOrderqty_'+row_num).removeAttr("readonly");
            $('#txtOrderqty_'+row_num).removeAttr("value").attr("value","");            
            $('#txtAmount_'+row_num).removeAttr("value").attr("value","");
            $('#txtRate_'+row_num).removeAttr("value").attr("value","");
            $('#txtProcessLoss_'+row_num).removeAttr("value").attr("value","");
            $('#txtTotalqty_'+row_num).removeAttr("value").attr("value","");
            $('#txtRate_'+row_num).removeAttr("onkeyup").attr("onkeyup","fnc_amount_cal("+row_num+")");

            $('#hdnDtlsUpdateId_'+row_num).removeAttr("value").attr("value","");

            $('#txtOrderqty_'+row_num).removeAttr("onKeyUp").attr("onkeyup","fnc_amount_cal("+row_num+");");
            $('#txtRate_'+row_num).removeAttr("onKeyUp").attr("onkeyup","fnc_amount_cal("+row_num+");");
            $('#txtAmount_'+row_num).removeAttr("onKeyUp").attr("onkeyup","fnc_amount_cal("+row_num+");");
            $('#txtProcessLoss_'+row_num).removeAttr("onKeyUp").attr("onkeyup","fnc_amount_cal("+row_num+");");
            $('#txtadjType_'+row_num).removeAttr("onchange").attr("onchange","fnc_amount_cal("+row_num+");");

            $('#increase_'+row_num).removeAttr("value").attr("value","+");
            $('#decrease_'+row_num).removeAttr("value").attr("value","-");
            $('#increase_'+row_num).removeAttr("onclick").attr("onclick","fnc_addRow("+row_num+",'"+table_id+"','"+tr_id+"');");
            $('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fnc_deleteRow("+row_num+",'"+table_id+"','"+tr_id+"');");
            
            //add_auto_complete(row_num);
            //fnc_comm_basis();
            set_all_onclick();
        }
    }

    function fnc_deleteRow(rowNo,table_id,tr_id) 
    { 
        var txt_check_box = $('#txt_check_box').val();
        var within_group = $('#cbo_within_group').val();
        if(within_group==1 || txt_check_box==0)
        {
            return;
        }
        else
        {
            var numRow = $('#'+table_id+' tbody tr').length; 
            var prefix=tr_id.substr(0, tr_id.length-1);
            var total_row=$('#'+prefix+'_tot_row').val();
            
            var numRow = $('table#tbl_dtls_yarn_dyeing tbody tr').length; 
            if(numRow!=1)
            {
                var updateIdDtls=$('#hdnDtlsUpdateId_'+rowNo).val();
                var txt_deleted_id=$('#txt_deleted_id').val();
                var selected_id='';
                if(updateIdDtls!='')
                {
                    if(txt_deleted_id=='') selected_id=updateIdDtls; else selected_id=txt_deleted_id+','+updateIdDtls;
                    $('#txt_deleted_id').val( selected_id );
                }
                
                $("#"+tr_id+rowNo).remove();
                $('#'+prefix+'_tot_row').val(total_row-1);
                //calculate_total_amount(1);
            }
            else
            {
                return false;
            }
        }
    }

    function exchange_rate(val) 
    {
        if(form_validation('cbo_company_name*txt_order_receive_date', 'Company Name*Receive Date')==false )
        {
            $("#cbo_currency_id").val(0);
            return;
        }
        
        if(val==0)
        {
            $('#txt_order_receive_date').removeAttr('disabled','disabled');
            $('#cbo_company_name').removeAttr('disabled','disabled');
            $("#txt_exchange_rate").val("");
            $('#txt_exchange_rate').attr('disabled','disabled');
        }
        else if(val==1)
        {
            $("#txt_exchange_rate").val(1);
            $('#txt_order_receive_date').attr('disabled','disabled');
            $('#cbo_company_name').attr('disabled','disabled');
            $('#txt_exchange_rate').attr('disabled','disabled');
        }
        else
        {
            var receive_date = $('#txt_order_receive_date').val();
            var company_name = $('#cbo_company_name').val();
            var response=return_global_ajax_value( val+"**"+receive_date+"**"+company_name, 'check_conversion_rate', '', 'requires/yd_order_entry_controller');
            $('#txt_exchange_rate').val(response);
            $('#txt_order_receive_date').attr('disabled','disabled');
            $('#cbo_company_name').attr('disabled','disabled');
            $('#txt_exchange_rate').attr('disabled','disabled');
        }
    }


    function openmypage_process(row_no)
    {
        var rows=row_no.split("_"); 
        var row=rows[1];
        var txtprocess=$('#txtprocess_'+row).val();
        var txtprocessname=$('#txtprocessname_'+row).val();
        var cbo_yd_process=$('#cbo_yd_process').val();
        var cbo_yd_type=$('#cbo_yd_type').val();

        var page_link = 'requires/yd_order_entry_controller.php?action=process_popup&txtprocess='+txtprocess+'&txtprocessname='+txtprocessname+'&cbo_yd_process='+cbo_yd_process+'&cbo_yd_type='+cbo_yd_type;
        var title = "Process Search";
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=490px,height=230px,center=1,resize=1,scrolling=0','../')
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            //var theemail=this.contentDoc.getElementById("txt_selected_id");
            //var theemailname=this.contentDoc.getElementById("txt_selected_name");
            var theemail=this.contentDoc.getElementById("txt_selected_id").value; //dtls id here
            var theemailname=this.contentDoc.getElementById("txt_selected_name").value; // req mst id
            //alert(theemail);
            var theemailArr = theemail.split(",");  
            var uniqueSet = new Set(theemailArr);
            var uniqueArr = Array.from(uniqueSet);
            var process_id_unique = uniqueArr.join(",");
            var theemailnameArr = theemailname.split(",");
            var uniqueSetname = new Set(theemailnameArr);
            var uniquenameArr = Array.from(uniqueSetname);
            var process_name_unique = uniquenameArr.join(",");
            if (theemail.value!="")
            {
                freeze_window(5);
                $('#txtprocess_'+row).val(process_id_unique);
                $('#txtprocessname_'+row).val(process_name_unique);
                release_freezing();
                set_all_onclick();
            }
        }
    }

    function yd_print_report()
    {
        if($('#update_id').val()=="")
        {
            alert("Please Save Data First.");
            return;
        }
        else
        {

            var update_id          = $('#update_id').val();
            var cbo_company_name    = $('#cbo_company_name').val();
            var cbo_within_group    = $('#cbo_within_group').val();

            var action = 'yarn_dyeing_order_entry_print';
            var data  = cbo_company_name+'*'+update_id+'*'+cbo_within_group;
            window.open("requires/yd_order_entry_controller.php?data=" + data+'&action='+action, true );
        }
    }

    function add_auto_complete(btn_id)
    {
        
        var i=btn_id.split("_")[1]
         $("#txtUseFor_"+i).autocomplete({
             source: use_for_value
          });
    }
    function use_for_copy_value(value,field_id,btn_id)
    {
        var i=btn_id.split("_")[1];
        //alert(i);
        var txtcolorId=document.getElementById('txtcolor_'+i).value;
        var rowCount = $('#tbl_dtls_yarn_dyeing tbody tr').length;
            for(var j=i; j<=rowCount; j++)
            {
                if(field_id=='txtUseFor_')
                {
                    
                    document.getElementById(field_id+j).value=value;
                            
                }
            }
    }

    function change_sub_process(value)
    {
        var rowCount = $('#tbl_dtls_yarn_dyeing tbody tr').length;
        for(var j=1; j<=rowCount; j++)
        {
            $('#txtprocess_'+j).val('');
            $('#txtprocessname_'+j).val('');
        }
    }

    function fnc_shade_popup(id)
    {

        if(form_validation('cbo_company_name*cbo_within_group*cbo_party_name*txt_order_receive_date', 'Company Name*Within Group*Party Name*Order Receive Date')==false )
        {
            $("#"+id).val('');
            return;
        }

        var row_id = id.split("_");
        var row_num = row_id[1];

        var color_range_id   =document.getElementById('txtItemColor_'+row_num).value;

        if(color_range_id==0){
            alert("Select Color Range Please!!!");
            $("#"+id).val('');
            return;
        }

        var cbo_company_name    =document.getElementById('cbo_company_name').value;
        var cbo_within_group    =document.getElementById('cbo_within_group').value;
        var cbo_party_name      =document.getElementById('cbo_party_name').value;
        var txt_order_receive_date   =document.getElementById('txt_order_receive_date').value;

        var page_link = 'requires/yd_order_entry_controller.php?action=shade_popup&cbo_company_name='+cbo_company_name+'&cbo_within_group='+cbo_within_group+'&cbo_party_name='+cbo_party_name+'&txt_order_receive_date='+txt_order_receive_date+'&color_range_id='+color_range_id;
        var title = "Shade % Search";
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=600px,height=430px,center=1,resize=1,scrolling=0','../');

        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var hidden_uper_limit=this.contentDoc.getElementById("hidden_uper_limit").value;
            var hidden_rate=this.contentDoc.getElementById("hidden_rate").value;
            var hidden_id=this.contentDoc.getElementById("hidden_id").value;
            var hidden_shade_mst_id=this.contentDoc.getElementById("hidden_shade_mst_id").value;

            //$("#"+id).val(hidden_uper_limit);
            //$("#txtRate_"+row_num).val(hidden_rate);
            //$("#txtShadeId_"+row_num).val(hidden_id);
            //$("#txtShadeMstId_"+row_num).val(hidden_shade_mst_id);

            //$('#txtRate_'+row_num).attr("disabled",true);

            //fnc_amount_cal(row_num);
        }

    }

    function set_shade_rate(id)
    {
        if(form_validation('cbo_company_name*cbo_within_group*cbo_party_name*txt_order_receive_date', 'Company Name*Within Group*Party Name*Order Receive Date')==false )
        {
            $("#"+id).val('');
            return;
        }

        var row_id = id.split("_");
        var row_num = row_id[1];

        var color_range_id   =document.getElementById('txtItemColor_'+row_num).value;
        var shade_limit   =document.getElementById(id).value;

        if(color_range_id==0){
            alert("Select Color Range Please!!!");
            $("#"+id).val('');
            return;
        }

        var cbo_company_name    =document.getElementById('cbo_company_name').value;
        var cbo_within_group    =document.getElementById('cbo_within_group').value;
        var cbo_party_name      =document.getElementById('cbo_party_name').value;
        var txt_order_receive_date   =document.getElementById('txt_order_receive_date').value;

        get_php_form_data(cbo_company_name+"_"+cbo_within_group+"_"+cbo_party_name+"_"+txt_order_receive_date+"_"+color_range_id+"_"+shade_limit+"_"+row_num, "populate_shade_rate_from_data", "requires/yd_order_entry_controller");

        fnc_amount_cal(row_num);
    }

    function check_shade_rate(id)
    {
        var row_id = id.split("_");
        var row_num = row_id[1];

        $("#txtShade_"+row_num).val('');
        $("#txtRate_"+row_num).val('');

        fnc_amount_cal(row_num);
    }

    function re_set_shade_rate()
    {
        var rowCount = $('#tbl_dtls_yarn_dyeing tbody tr').length;

        for(var j=1; j<=rowCount; j++)
        {
            $("#txtShade_"+j).val('');
            $("#txtRate_"+j).val('');

            fnc_amount_cal(j);
        }
    } 

    function openmypage_advance_job()
    {
        if( form_validation('cbo_company_name*cbo_within_group','Company Name*Within Group')==false )
        {
            return;
        }
        var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_advance_job').value;
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/yd_order_entry_controller.php?action=job_no_popup&data='+data,'YD Job No Popup', 'width=740px,height=380px,center=1,resize=0','../')
        emailwindow.onclose=function()
        {
            var theemail=this.contentDoc.getElementById("hdn_job_info");
            if (theemail.value!="")
            {
                freeze_window(5);
                var response=theemail.value.split('_');
                document.getElementById("txt_yd_job_id").value=response[0];
                document.getElementById("txt_advance_job").value=response[1];
                document.getElementById("txt_tag_pi_no").value=response[2];
                document.getElementById("txt_tag_pi_balance_qty").value=response[3];
                disable_enable_fields('txt_tag_pi_no',1);
                release_freezing();
            }
        }
    }



</script>
</head>

<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission); ?>
        <form name="dyeingorderentry_1" id="dyeingorderentry_1" autocomplete="off"> 
            <fieldset style="width:1350px;">
            <legend>Yarn Dyeing Order Entry</legend>
                <table width="1330" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                    <tr>
                        <td colspan="4" align="right"><strong>Job No</strong></td>
                        <td colspan="2">
                            <input class="text_boxes" type="text" name="txt_job_no" id="txt_job_no" placeholder="Double Click" onDblClick="openmypage_job();" style="width:140px;" readonly />
                        </td>
                    </tr>
                    <tr> </tr>
                    <tr>
                        <td width="110" class="must_entry_caption">Company Name </td>
                        <td width="160"><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/yd_order_entry_controller', this.value+'_'+1, 'load_drop_down_location', 'location_td'); fnc_load_party(1,document.getElementById('cbo_within_group').value);exchange_rate(document.getElementById('cbo_currency').value);re_set_shade_rate();"); ?>
                        </td>
                        </td>
                        <td width="110">Location Name</td>
                        <td width="160" id="location_td"><? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", $selected, "" ); ?></td>
                        <td class="must_entry_caption">Ord. Receive Date</td>
                        <td><input onChange="re_set_shade_rate();" type="text" name="txt_order_receive_date"  style="width:140px"  id="txt_order_receive_date" value="<? echo date("d-m-Y")?>" class="datepicker" /></td>
                        <td width="110" class="must_entry_caption">Within Group</td>
                        <td width="350"><?php echo create_drop_down( "cbo_within_group", 150, $yes_no,"", 0, "--  --", 0, "fnc_load_party(1,this.value);fnc_load_wo(this.value);" ); ?> &nbsp;

                        <b id="td_check_box" style="display:none">
                        <input class="text_boxes" type="checkbox" name="txt_check_box" id="txt_check_box"  style="width:60px;" value="1" onClick="fnc_wo_check(1);" checked="checked"  />Without Sales</b></td>
                    </tr>
                    <tr>
                         <td width="110" class="must_entry_caption">Party</td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_load_party(2,document.getElementById('cbo_within_group').value);re_set_shade_rate();"); ?></td>
                        <td id="td_party_location">Party Location</td>
                        <td id="party_location_td"><? echo create_drop_down( "cbo_party_location", 150, $blank_array,"", 1, "-- Select Location --", $selected, "",1 ); ?></td>
                         
                        <td class="must_entry_caption">Currency</td>                                              
                        <td id="currency_td">
                            <?
                            echo create_drop_down("cbo_currency", 150, $currency,"", 1, "-- Select Currency --",$selected,"exchange_rate(this.value)", "","","","","",7 ); 
                            ?>
                        </td>
                        <td>Exchange Rate</td>
                         <td><input type="text" name="txt_exchange_rate" id="txt_exchange_rate" style="width:140px" class="text_boxes_numeric" readonly  value=""  />&nbsp;
						 <b id="td_check_box_confirm" style="display:none">
                        <input class="text_boxes" type="checkbox" name="txt_check_box_confirm" id="txt_check_box_confirm"  style="width:60px;" value="1" onClick="fnc_wo_check_confirm(2);"  checked="checked" />Confirm</b>
						 </td>
                    </tr> 
                    <tr>
                        <td>Delivery Date</td>
                        <td> <input type="text" name="txt_delivery_date" id="txt_delivery_date"  style="width:140px"  class="datepicker" value="" /> 
						 </td>
                        <td>Rcv. Start Date</td>
                        <td><input type="text" name="txt_rec_start_date" id="txt_rec_start_date" style="width:140px" class="datepicker" value="" placeholder="Material Rcv. Start Date" /></td>

                        <td>Rcv. End Date</td>
                        <td><input type="text" name="txt_rec_end_date" id="txt_rec_end_date" style="width:140px" class="datepicker" value="" placeholder="Material Rcv. End Date" /></td>
                        <td>Advance Job No</td>
                        <td>
                            <input type="text" name="txt_advance_job" id="txt_advance_job" class="text_boxes" style="width:140px" placeholder="Browse" onDblClick="openmypage_advance_job()" readonly/>
                            <input type="hidden" name="txt_yd_job_id" id="txt_yd_job_id">

                            <b id="td_check_box_advance" style="display:none"><input class="text_boxes" type="checkbox" name="txt_check_box_advance" id="txt_check_box_advance"  style="width:60px;" value="0" onClick="fnc_wo_check_advance(3);"  />Advance/Block</b>
                        </td>
                    </tr>
                    <tr>
                        <td>Tag PI No.</td>
                        <td><input type="text" name="txt_tag_pi_no" id="txt_tag_pi_no" style="width:140px" class="text_boxes" value="" placeholder="Write" />&nbsp;
                            <input type="hidden" name="txt_tag_pi_balance_qty" id="txt_tag_pi_balance_qty" style="width:140px" class="text_boxes" value="" placeholder="Write" />&nbsp;
                        </td>
                        <td width="110" ><strong>WO No</strong></td>
                        <td width="160">
                            <input class="text_boxes"  type="text" name="txt_order_no" id="txt_order_no" onDblClick="workorder_browse();" placeholder="Browse" style="width:140px;"  />
                            <input class="text_boxes" type="hidden" name="update_id" id="update_id" readonly />
                            <input type="hidden" name="txt_deleted_id[]" id="txt_deleted_id" readonly />
                            <input type="hidden" name="hid_order_id" id="hid_order_id" readonly/>
                            <input type="hidden" name="hid_booking_type" id="hid_booking_type" value="0" readonly/>
                            <input type="hidden" name="hid_is_without_order" id="hid_is_without_order" readonly/>
                            <input type="hidden" name="hid_is_sales_check" id="hid_is_sales_check" readonly/>
                        </td>
                        <td >Prod. Type</td>                                              
                        <td id="order_type_td">
                            <?
                           
                            echo create_drop_down("cbo_pro_type", 150, $w_pro_type_arr,"", 1, "-- Select Type --",$selected,"", "","","","","",7 ); 
                            ?>
                        </td>
                        <td >Order Type</td>                                              
                        <td id="order_type_td">
                            <?
                            
                            echo create_drop_down("cbo_order_type", 150, $w_order_type_arr,"", 1, "-- Select Type --",$selected,"fnc_load_order_type(this.value);", "","","","","",7 ); 
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td >Y/D Type</td>                                              
                        <td id="yd_type_td">
                            <?
                            
                            echo create_drop_down("cbo_yd_type", 150, $yd_type_arr,"", 1, "-- Select Y/D Type --",$selected,"", "","","","","",7 ); 
                            ?>
                        </td>
                        <td>Y/D Process</td>                                              
                        <td id="yd_process_td">
                            <?
                           
                            echo create_drop_down("cbo_yd_process", 150, $yd_process_arr,"", 1, "-- Select Y/D Process --",$selected,"change_sub_process(this.value);", "","","","","",7 ); 
                            ?>
                        </td>
                        <td>Attention By</td> 
                         <td><input type="text" name="attention" id="attention" style="width:140px" class="text_boxes" value="" placeholder="Write" /></td>
                        <td >Team Leader</td>                                              
                        <td id="team_leader_td">
                            <?php echo create_drop_down( "cbo_team_leader", 150, "select id,team_leader_name from lib_marketing_team where  status_active =1 and is_deleted=0 and project_type=10","id,team_leader_name", 1, "-- Select Leader --", $selected, "load_drop_down( 'requires/yd_order_entry_controller', this.value+'_'+1, 'load_drop_down_member', 'team_member_td');");?>
                        </td>
                    </tr>
                    <tr>
                        <td>Team Member</td>                                              
                        <td id="team_member_td">
                            <?php echo create_drop_down( "cbo_team_member", 150,  $blank_array, "", 1, "-- Select Member --", $selected, "load_drop_down( 'requires/yd_order_entry_controller', this.value+'_'+1, 'load_drop_down_member', 'team_member_td');"); ?>
                        </td>
                        <td>Party Ref.</td> 
                         <td><input type="text" name="party_ref" id="party_ref" style="width:140px" class="text_boxes" value="" placeholder="Write" /></td>
                         <td >Remarks</td>
                        <td ><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:150px"  placeholder="Write" />
                        </td>
                        <td colspan="1" align="left">
                          <input type="button" class="image_uploader" style="width:112px" value=" ADD File" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'yarn_dyeing', 2 ,1)">
                        </td>
                     
                       <td colspan="1" align="left">
                                <input type="button" class="image_uploader" style="width:150px" value="CLICK TO ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'yarn_dyeing', 0 ,1)">

                        </td>
                    </tr>
                </table>
        </fieldset>
        <fieldset style="width:2200px;">
           <legend>YD Order Entry Details</legend>
                <table width="2180px" cellpadding="0" cellspacing="2" border="0" class="rpt_table" rules="all" id="tbl_dtls_yarn_dyeing">
                    <thead class="form_table_header">
                    	<th width="90" id="styleRef_td">Style</th>
                        <th width="90"id="saleOrder">Job No/Sales order no</th>
                        <th width="90"id="buyerBuyer">Cust. Buyer</th>
                        <th width="90" id="process_td">Sub-Process</th>
                        <th width="90" class="must_entry_caption">Lot</th>
                        <th width="90"  class="must_entry_caption" id="count_type">Count Type</th>
                        <th width="90"  class="must_entry_caption" >Count</th>
                        <th width="70"  class="must_entry_caption">Yarn Type</th>
                        <th width="70"  class="must_entry_caption">Yarn Composition</th>
                        <th width="90" class="must_entry_caption" id="color_range">Color Range</th>
                        <th width="60" class="must_entry_caption">Y/D Color</th>
                        <th width="60">Shade %</th>
                        <th width="60" title="Approval Ref./Lab Dip">App. Ref.</th>
                        <th width="80">Use For</th> 
                        <th width="80" title="Count Strength Product" >CSP</th>
                        <th width="80" class="">No of Bag</th>
                        <th width="50">Cone Per Bag</th>
                        <th width="60" >No of Cone</th>
                        <th width="50">AVG. Wt. Per Cone</th>
                        <th width="60" class="must_entry_caption">Uom</th>
                        <th width="60" class="must_entry_caption" id="OrderQty">Order Qty</th>
                        <th width="60" >Rate</th>
                        <th width="60" class="">Amount </th>
                        <th width="60" >Process Loss %</th>
                        <th  width="60" >Adj. Type</th>
                        <th width="60" id="TotalQnty">Total Qnty</th>
                        <th>RMK</th>
                    </thead>
                    <tbody id="dyeing_details_container">
                        <tr id="row_1">
                        	<td><input id="txtstyleRef_1" name="txtstyleRef[]" type="text" class="text_boxes" style="width:80px"/></td>
                            <td><input id="txtsaleOrder_1" name="txtsaleOrder[]" type="text" class="text_boxes" style="width:80px" placeholder=""/>
                            <input id="txtsaleOrderID_1" name="txtsaleOrderID[]" type="hidden" class="text_boxes_numeric" style="width:80px" placeholder=""/>
                        	<input id="txtProductID_1" name="txtProductID[]" type="hidden" class="text_boxes_numeric" style="width:80px" placeholder=""/></td>
                            <td><input id="buyerBuyer_1" name="txtbuyerBuyer[]" type="text" class="text_boxes" style="width:80px" placeholder=""/>
                           </td>
                            <td>

                                <input type="text" name="txtprocessname[]" id="txtprocessname_1" class="text_boxes" value="" style="width:80px;" onDblClick="openmypage_process(this.id)" placeholder="Doble Click For Process"  readonly />  
                           <input type="hidden" name="txtprocess[]" id="txtprocess_1" class="text_boxes" value="" style="width:80px;"  readonly />
                            </td> 
                        	<td><input id="txtlot_1" name="txtlot[]" type="text" class="text_boxes" style="width:80px" placeholder=""/></td>
                            <td>
                                <?                                
                                echo create_drop_down( "txtcountType_1", 100, $count_type_arr,'', 1, '--- Select---', 0, "",0,'','','','','','',"txtcountType[]");
                                ?>
                            </td>
                        	<td id="count_td"><input id="txtcount_1" name="txtcount[]" type="text" class="text_boxes_numeric" style="width:80px" placeholder=""/></td>
                        	<td id="yarn_type_td"><input id="txtydtype_1" name="txtydtype[]" type="text" class="text_boxes" style="width:60px" placeholder=""/></td>
                        	<td id="composition_td"><input id="txtydComposition_1" name="txtydComposition[]" type="text" class="text_boxes" style="width:60px" placeholder=""/></td>
                            <td>
                            <? echo   create_drop_down( "txtItemColor_1", 90, $color_range,"", 1, "-- Select --",0,"check_shade_rate(this.id);",0,'','','','','','',"txtItemColor[]")   ?>
                            <input name="txtItemColorID[]" id="txtItemColorID_1" type="hidden" class="text_boxes" style="width:50px" />
                            </td>
                            <td><input name="txtYarnColor[]" id="txtYarnColor_1" type="text" class="text_boxes" style="width:50px" placeholder="" readonly /><input name="txtYarnColorID[]" id="txtYarnColorID_1" type="hidden" class="text_boxes" style="width:50px" /></td>
                            <td><input name="txtShade[]" id="txtShade_1" type="text" class="text_boxes_numeric" style="width:50px" placeholder="write Or Browse" onChange="set_shade_rate(this.id);" onDblClick="fnc_shade_popup(this.id);" />
                                <input name="txtShadeId[]" id="txtShadeId_1" type="hidden" class="text_boxes_numeric" style="width:50px" placeholder="write Or Browse"/>
                                <input name="txtShadeMstId[]" id="txtShadeMstId_1" type="hidden" class="text_boxes_numeric" style="width:50px" placeholder="write Or Browse"/>
                            </td>
                            <td><input id="txtAppRef_1" name="txtAppRef[]" type="text" class="text_boxes" style="width:70px" placeholder=""/></td>
                            <td><input id="txtUseFor_1"  onfocus="add_auto_complete(this.id)" onBlur="fn_filed_check(this.id);use_for_copy_value(this.value,'txtUseFor_',this.id)" name="txtUseFor[]" type="text" class="text_boxes" style="width:70px" placeholder=""/></td>
                            <td><input id="txtCSP_1" name="txtCSP[]" type="text" class="text_boxes_numeric" style="width:70px" placeholder=""/></td>
                            <td><input name="txtnoBag[]" id="txtnoBag_1" type="text" class="text_boxes_numeric" style="width:70px" readonly placeholder="" /></td>
                            <td><input name="txtConeBag[]" id="txtConeBag_1" type="text" class="text_boxes_numeric" style="width:50px" placeholder="Write" /></td>
                            <td><input name="txtNoCone[]" id="txtNoCone_1" class="text_boxes_numeric" type="text"  style="width:50px"  placeholder="Write" /></td>
                            <td><input name="txtAVG[]" id="txtAVG_1" type="text"  class="text_boxes_numeric" style="width:50px" placeholder="Write" /></td>
                            <td><? echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"", 1, "-- Select --",12,"", "","2,1,12,23,15",'','','','','',"cboUom[]" ); ?></td>
                            <td><input name="txtOrderqty[]" onKeyUp="fnc_amount_cal(1)" id="txtOrderqty_1" type="text" style="width:50px"  class="text_boxes_numeric" placeholder="" /></td> 
                            <td><input name="txtRate[]" id="txtRate_1" type="text" style="width:50px"  class="text_boxes_numeric" onKeyUp="fnc_amount_cal(1)"  placeholder="" /></td> 
                            <td><input name="txtAmount[]" id="txtAmount_1" type="text" style="width:50px"  class="text_boxes_numeric"  placeholder="" /></td> 
                            <td><input name="txtProcessLoss[]"   id="txtProcessLoss_1" type="text"  class="text_boxes_numeric" style="width:50px" placeholder="" onKeyUp="fnc_amount_cal(1);sum_total_qnty(1);" /></td>
                            <td>
                                <?                                
                                echo create_drop_down( "txtadjType_1", 80, $adj_type_arr,'', 1, '--- Select---', 2, "fnc_amount_cal(1);",0,'','','','','','',"txtadjType[]");
                                ?>
                            <td><input readonly type="text" name="txtTotalqty[]" id="txtTotalqty_1" class="text_boxes_numeric" style="width:50px"  placeholder="" /><input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_1" class="text_boxes_numeric" style="width:50px"  readonly /></td>

                            <!-- onClick="openmypage_avg_wt(1,'0',1)" placeholder="Browse" <td><input type="button" name="btnremarks_1" id="btnremarks_1" class="formbuttonplasminus" value="RMK" onClick="openmypage_remarks(1);" />
                            	<input type="hidden" name="txtremarks_1" id="txtremarks_1" class="text_boxes" />
                            </td> -->
                            <td width="65">
                            <input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(1,'tbl_dtls_yarn_dyeing','row_')" />
                            <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(1,'tbl_dtls_yarn_dyeing','row_');" />
                            </td>
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
                <table width="1550" cellspacing="5" cellpadding="0" border="0">
                    <tr>
                        <td align="center" colspan="11" valign="middle" class="button_container">
                            <input type="button" id="Print" value="Print" class="formbutton" style="width:100px;" onClick="yd_print_report();" >
                        </td>
                        </td>
                    </tr>   
                </table>
            </fieldset> 
        </form>                         
    </div>
</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
   // set_multiselect('txtprocess_1','0','0','','0');
   $(function(){
		
		for (var property in mandatory_field_arr) {
			$("#"+mandatory_field_arr[property]).parent().prev('td').css("color", "blue");
		}
	});
</script>
</html>