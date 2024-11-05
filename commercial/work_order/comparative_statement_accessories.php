<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create for Comparative Statement [Accessories]
Functionality	:
JS Functions	:
Created by		:	Jahid
Creation date 	: 	25-5-2021
Updated by 		: 	Rakib
Update date		: 	
QC Performed BY	:
QC Date			:
Comments		:

*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//-------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Comparative Statement Accessories", "../../",  1, 1, $unicode,1,'');

?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
    var permission='<? echo $permission; ?>';
    function openmypage_supplier()
	{
		var supplier_id = $('#supplier_id').val();
		var title = 'Supplier Name';
        var page_link = 'requires/comparative_statement_accessories_controller.php?supplier_id='+supplier_id+'&action=supplier_name_popup';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var supplier_id=this.contentDoc.getElementById("hidden_supplier_id").value;	
			var supplier_name=this.contentDoc.getElementById("hidden_supplier_name").value;
			$('#supplier_id').val(supplier_id);
			$('#txt_supplier_name').val(supplier_name);
		}
	}

    function openmypage_demand_no() 
    {
        if( form_validation('cbo_company_id*cbo_basis_name','Company*Basis')==false )
		{
			return;
		}
        var basis_id = $('#cbo_basis_name').val();
        var update_id=$('#update_id').val();
        var txt_requisition_mst=$('#txt_requisition_mst').val();
        var txt_requisition_dtls=$('#prev_req_dtls_id').val();
        var cbo_company_id=$('#cbo_company_id').val();
        $('#txt_style_check').prop('checked', false);

        if(basis_id==1)
		{
            var title = 'Demand No';			
            var page_link = 'requires/comparative_statement_accessories_controller.php?action=demand_popup&txt_requisition_mst='+txt_requisition_mst+'&txt_requisition_dtls='+txt_requisition_dtls+'&update_id='+update_id;
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1130px,height=450px,center=1,resize=1,scrolling=0','../');
            emailwindow.onclose=function()
            {
                var theform=this.contentDoc.forms[0];
                var requisition_no=this.contentDoc.getElementById("hidden_req_no").value;                 
                var requisition_id=$.unique(this.contentDoc.getElementById("hidden_req_id").value.split(','));	
                var requisition_dtls=$.unique(this.contentDoc.getElementById("hidden_req_dtls_id").value.split(','));
                var job_id=this.contentDoc.getElementById("hidden_job_id").value;   
                var req_no_arr = requisition_no.split(',');
                var req = $.unique(req_no_arr);                
                $('#txt_demand').val(req);
                $('#txt_requisition_mst').val(requisition_id);
                $('#txt_requisition_dtls').val(requisition_dtls);
				$('#cs_generate_check').val(0);
                $('#hidd_job_id').val(job_id);
                //$('#txt_style_no').val(req);
                get_php_form_data(requisition_dtls+'**'+basis_id, "nominated_supplier_name_popup", "requires/comparative_statement_accessories_controller" );
				
            }
        }
        if(basis_id==2)
		{
            var title = 'Item No';
            var page_link = 'requires/comparative_statement_accessories_controller.php?action=item_popup&txt_requisition_mst='+txt_requisition_mst+'&txt_requisition_dtls='+txt_requisition_dtls+'&update_id='+update_id;
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=450px,center=1,resize=1,scrolling=0','../');
            emailwindow.onclose=function()
            {
                var theform=this.contentDoc.forms[0];
                var item_no=this.contentDoc.getElementById("hidden_item_no").value;	
                var item_id=this.contentDoc.getElementById("hidden_item_id").value;	
                var item_dtls=this.contentDoc.getElementById("hidden_item_dtls_id").value;	

                var item_no_arr = item_no.split(',');
                var item = $.unique(item_no_arr);

                $('#txt_demand').val(item);
                $('#txt_requisition_mst').val(item_id);
                $('#txt_requisition_dtls').val(item_dtls);
                $('#cs_generate_check').val(0);
                get_php_form_data(item_dtls+'**'+basis_id, "nominated_supplier_name_popup", "requires/comparative_statement_accessories_controller" );
            }
        }

        if(basis_id==3)
        {
            var title = 'Style No';            
            var page_link = 'requires/comparative_statement_accessories_controller.php?action=style_popup&txt_requisition_mst='+txt_requisition_mst+'&txt_requisition_dtls='+txt_requisition_dtls+'&update_id='+update_id+'&cbo_company_id='+cbo_company_id;
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1250px,height=450px,center=1,resize=1,scrolling=0','../');
            emailwindow.onclose=function()
            {
                var theform=this.contentDoc.forms[0];
                var requisition_no=this.contentDoc.getElementById("hidden_req_no").value;   
                var requisition_id=$.unique(this.contentDoc.getElementById("hidden_req_id").value.split(','));  
                var requisition_dtls=$.unique(this.contentDoc.getElementById("hidden_req_dtls_id").value.split(','));
                var req_no_arr = requisition_no.split(',');
                var req = $.unique(req_no_arr);                
                $('#txt_demand').val(req);
                $('#txt_requisition_mst').val(requisition_id);
                $('#txt_requisition_dtls').val(requisition_dtls);
                $('#cs_generate_check').val(0);               
                get_php_form_data(requisition_dtls+'**'+basis_id, "nominated_supplier_name_popup", "requires/comparative_statement_accessories_controller" );
                
            }
        }

    }

    function fnc_generate_cs() 
    {
        if( form_validation('cbo_basis_name*txt_demand*txt_supplier_name','Basis*Demand No/Item*Supplier')==false )
		{
			return;
		}
        var update_id=$("#update_id").val();
		//var job_ids=$("#hidd_job_id").val();
		var txt_requisition_dtls=$("#prev_req_dtls_id").val();
        show_list_view(document.getElementById('cbo_basis_name').value+'**'+document.getElementById('txt_requisition_mst').value+'**'+document.getElementById('txt_requisition_dtls').value+'**'+document.getElementById('supplier_id').value+'**'+update_id+'**'+txt_requisition_dtls+'**'+document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_size_lavel').value, 'load_cs_table', 'cs_tbl', 'requires/comparative_statement_accessories_controller', 'setFilterGrid(\'cs_tbl\',-1)');

       show_list_view(document.getElementById('cbo_basis_name').value+'**'+document.getElementById('txt_requisition_mst').value+'**'+document.getElementById('txt_requisition_dtls').value+'**'+document.getElementById('supplier_id').value+'**'+update_id, 'load_statment_table', 'statment_tbl', 'requires/comparative_statement_accessories_controller', 'setFilterGrid(\'statment_tbl\',-1)');
		$('#cs_generate_check').val(1);
        $('#cbo_size_lavel').attr('disabled',true);
    }

    function fnc_print_report(type)
	{
		if ( $('#txt_system_id').val()=='')
		{
			alert ('CS Not Save.');
			return;
		}

        var form_caption=$( "div.form_caption" ).html();
	 	print_report( $('#update_id').val()+'*'+'*'+$('#cbo_template_id').val()+'*'+form_caption, "comparative_statement_print2", "requires/comparative_statement_accessories_controller" );
        return;
    }
	
    function fnc_comparative_statement(operation) 
    {
        if(operation==4)
		{
            var form_caption=$( "div.form_caption" ).html();
	 	    print_report( $('#update_id').val()+'*'+'*'+$('#cbo_template_id').val()+'*'+form_caption, "comparative_statement_print", "requires/comparative_statement_accessories_controller" )
	 	    return;
        }
		
		var cs_generate_check=$('#cs_generate_check').val();       
		if(cs_generate_check==0)
		{
			alert("Please Press Generate CS Button");return;
		}

        if( form_validation('cbo_company_id*cbo_basis_name*txt_demand*txt_cs_date*txt_supplier_name*cbo_currency_name*txt_validity_date','Company*Basis*Demand No/Item*CS Date*Supplier*Currency*CS Validity')==false )
		{
			return;
		}
		
        if(date_compare($('#txt_cs_date').val(), $('#txt_validity_date').val())==false)
		{
			alert("CS Validity Date Can not Be Less Than CS Date");
			return;
		}

        var txt_style_check=0;
        if ($('#txt_style_check').is(':checked')) txt_style_check=1;

        
		/*var cbo_ready_to_approved=$("#cbo_ready_to_approved").val();
		if(operation==2 && cbo_ready_to_approved==1)
		{
			alert("CS Approved, Delete Not Allow");return;
		}*/
		
		/*var prev_req_dtls_id=('#prev_req_dtls_id').val();
		var txt_requisition_dtls=('#txt_requisition_dtls').val();
		var update_id=('#update_id').val();
		if(update_id!="" && prev_req_dtls_id !="" txt_requisition_dtls)*/
        var basis = $('#cbo_basis_name').val();

        var row_num=$('#tbl_details tbody tr').length;
        var data_dtls="";
        var supplier_id = $('#supplier_id').val();
        var col_num_arr = supplier_id.split(',');
        var col_num = col_num_arr.length; 
        var company_name_id = $('#cbo_company_name').val();
        var company_num_arr = company_name_id.split(',');
        if(company_name_id!=''){var company_num = company_num_arr.length}else{var company_num =0;}
        var data_supplier="";
        var data_company="";
        if(row_num==0){
            alert("Please Click Genarate CS");
			return;
        }
		
        for (var i=1; i<=row_num; i++)
		{
            data_dtls += '&txtItemCategory_' + i + '=' + $('#txtItemCategory_'+i).attr('title') + '&txtMainGroup_' + i + '=' + $('#txtMainGroup_'+i).attr('title') + '&txtGroup_' + i + '=' + $('#txtGroup_'+i).attr('title') + '&txtBrandSupplier_' + i + '=' + $('#txtBrandSupplier_'+i).val()+ '&txtItemDescrip_' + i + '=' + $('#txtItemDescrip_'+i).val() + '&txtItemQuality_' + i + '=' + $('#txtItemQuality_'+i).val() + '&txtGmtsColor_' + i + '=' + $('#txtGmtsColor_'+i).attr('title') + '&txtItemSize_' + i + '=' + $('#txtItemSize_'+i).val() + '&txtItemColor_' + i + '=' + $('#txtItemColor_'+i).val() + '&txtSizeLength_' + i + '=' + $('#txtSizeLength_'+i).val() + '&txtUom_' + i + '=' + $('#txtUom_'+i).attr('title')+ '&txtQty_' + i + '=' + $('#txtQty_'+i).val()+ '&txtRate_' + i + '=' + $('#txtRate_'+i).val()+ '&txtAmt_' + i + '=' + $('#txtAmt_'+i).val() + '&txtJobId_' + i + '=' + $('#txtJobId_'+i).val() + '&txtFabricDtlsId_' + i + '=' + $('#txtFabricDtlsId_'+i).val() + '&txtAllCompanyQuoted_' + i + '=' + $('#txtAllCompanyQuoted_'+i).val()+ '&txtAllCompanyNeg_' + i + '=' + $('#txtAllCompanyNeg_'+i).val()+ '&txtAllCompanyCon_' + i + '=' + $('#txtAllCompanyCon_'+i).val();

            var req_qty=$('#txtQty_'+i).val()*1;
            var quoted_validation=0;
            var neg_validation=0;
            var allocated_qty=0;

            for (var m=0; m<col_num; m++)
            {
                var mm=col_num_arr[m];
                var supplier_check = $("#txt_supplier_check_"+mm).is(":checked");
                var supplier_check_value=0;
                if (supplier_check==true) var supplier_check_value=1;
                data_supplier += '&txtsuppier_' + i + '_' + mm + '=' + mm + '&txtquoted_' + i + '_' + mm + '=' + $('#txtquoted_'+i+'_'+mm).val() + '&txtneg_' + i + '_' + mm + '=' + $('#txtneg_'+i+'_'+mm).val()+ '&txtcon_' + i + '_' + mm + '=' + $('#txtcon_'+i+'_'+mm).val()+ '&txtallocatedqty_' + i + '_' + mm + '=' + $('#txtallocatedqty_'+i+'_'+mm).val()+ '&txtpayterm_' + i + '_' + mm + '=' + $('#txtpayterm_'+i+'_'+mm).val()+ '&txtLCType_' + i + '_' + mm + '=' + $('#txtLCType_'+i+'_'+mm).val()+ '&txttenor_' + i + '_' + mm + '=' + $('#txttenor_'+i+'_'+mm).val()+ '&cboIncoTerm_' + i + '_' + mm + '=' + $('#cboIncoTerm_'+i+'_'+mm).val()+ '&txtallocatedqtypercentage_' + i + '_' + mm + '=' + $('#txtallocatedqtypercentage_'+i+'_'+mm).val()+ '&txt_supplier_check_' + mm + '=' + supplier_check_value+ '&origin_' + mm + '=' + $('#origin_'+mm).val()+ '&shipMode_' + mm + '=' + $('#shipMode_'+mm).val();

                allocated_qty += $('#txtallocatedqty_'+i+'_'+mm).val()*1;
				
                quoted_validation += +$('#txtquoted_'+i+'_'+mm).val();
                neg_validation += +$('#txtneg_'+i+'_'+mm).val();
                //allocated_validation += +$('#txtallocatedqty_'+i+'_'+mm).val();
                //allocatedPercentage_validation += +$('#txtallocatedqtypercentage_'+i+'_'+mm).val();
            }
            
            for (var m=0; m<company_num; m++)
            {
                var mm=company_num_arr[m];
                var company_check = $("#txt_company_check_"+mm).is(":checked");
                var company_check_value=0;
                if (company_check==true) var company_check_value=1;
                data_company += '&txtCompany_' + i + '_' + mm + '=' + mm + '&txtCompanyQuoted_' + i + '_' + mm + '=' + $('#txtCompanyQuoted_'+i+'_'+mm).val() + '&txtCompanyNeg_' + i + '_' + mm + '=' + $('#txtCompanyNeg_'+i+'_'+mm).val()+ '&txtCompanyCon_' + i + '_' + mm + '=' + $('#txtCompanyCon_'+i+'_'+mm).val()+ '&txtCompanyAllocatedQty_' + i + '_' + mm + '=' + $('#txtCompanyAllocatedQty_'+i+'_'+mm).val()+ '&txtCompanyPayTerm_' + i + '_' + mm + '=' + $('#txtCompanyPayTerm_'+i+'_'+mm).val()+ '&txtCompanyTenor_' + i + '_' + mm + '=' + $('#txtCompanyTenor_'+i+'_'+mm).val()+ '&companyCboIncoTerm_' + i + '_' + mm + '=' + $('#companyCboIncoTerm_'+i+'_'+mm).val()+ '&txtCompanyAllocatedQtyPercentage_' + i + '_' + mm + '=' + $('#txtCompanyAllocatedQtyPercentage_'+i+'_'+mm).val()+ '&txt_company_check_' + mm + '=' + company_check_value+ '&companyOrigin_' + mm + '=' + $('#companyOrigin_'+mm).val()+ '&companyShipMode_' + mm + '=' + $('#companyShipMode_'+mm).val();

                allocated_qty += $('#txtCompanyAllocatedQty_'+i+'_'+mm).val()*1;

                quoted_validation += +$('#txtCompanyQuoted_'+i+'_'+mm).val();
                neg_validation += +$('#txtCompanyNeg_'+i+'_'+mm).val();
                //allocated_validation += +$('#txtCompanyAllocatedQty_'+i+'_'+mm).val();
                //allocatedPercentage_validation += +$('#txtCompanyAllocatedQtyPercentage_'+i+'_'+mm).val();
            }
           

            if(quoted_validation==0 || neg_validation==0)
			{
                alert("Please Fill the Price");
                return;
            }

            if (basis != 3){
                if (req_qty != allocated_qty){
                    alert("Req. Qty and Allocated Qty must be Equal.");
                    return;
                }
            }
            
        }
        // alert(data_supplier);
        var data="action=save_update_delete&operation="+operation+"&row_num="+row_num+"&data_dtls="+data_dtls+"&col_num="+col_num+"&data_supplier="+data_supplier+"&company_num="+company_num+"&data_company="+data_company+"&txt_style_check="+txt_style_check+get_submitted_data_string('cbo_company_id*cbo_basis_name*txt_demand*txt_requisition_mst*txt_requisition_dtls*txt_cs_date*supplier_id*cbo_currency_name*cbo_size_lavel*txt_validity_date*cbo_ready_to_approved*cbo_company_name*txt_comments*update_id*txt_system_id*hidd_job_id*txt_style_no',"../../");
        //alert(data);return;
        freeze_window(operation);
        http.open("POST","requires/comparative_statement_accessories_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_comparative_statement_reponse;
    }

    function fnc_comparative_statement_reponse()
    {
        if(http.readyState == 4) 
		{
			//alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1)
			{
				document.getElementById('txt_system_id').value=reponse[1];
				document.getElementById('update_id').value=reponse[2];
				document.getElementById('prev_req_dtls_id').value=reponse[3];
				$('#cbo_basis_name').attr('disabled',true);
                $('#cbo_size_lavel').attr('disabled',true);
                //$('#generate_cs').attr('disabled',true);
				set_button_status(1, permission, 'fnc_comparative_statement',1);
				
			}
			if(parseInt(trim(reponse[0]))==2)
			{
				location.reload();
                set_button_status(0, permission, 'fnc_comparative_statement',1);
			}
			if(parseInt(trim(reponse[0]))==11)
			{
				alert(trim(reponse[1]));release_freezing();return;
			}

            if(parseInt(trim(reponse[0]))==20)
            {
                alert(trim(reponse[1]));release_freezing();return;
            }
			show_msg(trim(reponse[0]));
			release_freezing();
		}
    }

    function calculate_allocated_qty(val,id,dtlsId)
    {
        alert(val);
        alert(id);
        alert(dtlsId);
        var dtlsInfo=dtlsId.split('**');
        var row_num=$('#tbl_details tbody tr').length;
        var basis=$('#cbo_basis_name').val();
        var supplier_id = $('#supplier_id').val();
        var col_num_arr = supplier_id.split(',');
        var col_num = col_num_arr.length; 
        var company_name_id = $('#cbo_company_name').val();
        var company_num_arr = company_name_id.split(',');
        if (company_name_id!='') var company_num = company_num_arr.length;        
        else var company_num =0;       

        for (var i=1; i<=row_num; i++)
        {
            var supp_neg_price=com_neg_price="";
            var supp_allocated_qty=com_allocated_qty="";
            var supp_allocated_qty_again=com_allocated_qty_again=0;
            var supp_allocated_qty_percentage=com_allocated_qty_percentage="";
            var total_value="";
            var tot_allocated_qty=0;           

            var req_qty = $('#txtQty_'+i).val()*1;
            for (var m=0; m<col_num; m++)
            {
                var mm=col_num_arr[m];
                supp_allocated_qty_percentage = $('#txtallocatedqtypercentage_'+i+'_'+mm).val()*1;
                supp_allocated_qty = (req_qty*supp_allocated_qty_percentage)/100;
                if (basis==3){
                    if (req_qty < supp_allocated_qty){
                        alert("Allocated Qty Exceeds Req Qty.");
                        $('#txtallocatedqty_'+dtlsInfo[0]+'_'+dtlsInfo[1]).val("");
                        $('#txttotalvalue_'+dtlsInfo[0]+'_'+dtlsInfo[1]).val("");
                        return;
                    }
                }

                if (supp_allocated_qty==0) $('#txtallocatedqty_'+i+'_'+mm).val("");
                else $('#txtallocatedqty_'+i+'_'+mm).val(supp_allocated_qty);

                supp_neg_price = $('#txtneg_'+i+'_'+mm).val()*1;
                supp_allocated_qty_again = $('#txtallocatedqty_'+i+'_'+mm).val()*1;                               
                total_value = supp_neg_price*supp_allocated_qty_again;                
                if (total_value==0) $('#txttotalvalue_'+i+'_'+mm).val("");
                else $('#txttotalvalue_'+i+'_'+mm).val(total_value.toFixed(6));

                if (supp_allocated_qty_again > 0) {
                    tot_allocated_qty += supp_allocated_qty_again;
                }
                //alert(tot_allocated_qty);
                if (basis !=3){                    
                    if (req_qty < tot_allocated_qty){
                        alert("Allocated Qty Exceeds Req Qty.");
                        $('#txtallocatedqtypercentage_'+dtlsInfo[0]+'_'+dtlsInfo[1]).val("");
                        $('#txtallocatedqty_'+dtlsInfo[0]+'_'+dtlsInfo[1]).val("");
                        $('#txttotalvalue_'+dtlsInfo[0]+'_'+dtlsInfo[1]).val("");
                        return;
                    }
                }
            }            


            for (var m=0; m<company_num; m++)
            {
                var mm=company_num_arr[m];
                com_allocated_qty_percentage = $('#txtCompanyAllocatedQtyPercentage_'+i+'_'+mm).val()*1;
                com_allocated_qty = (req_qty*com_allocated_qty_percentage)/100;
                if (basis==3){
                    if (req_qty < com_allocated_qty){
                        alert("Allocated Qty Exceeds Req Qty.");
                        $('#txtCompanyAllocatedQty_'+dtlsInfo[0]+'_'+dtlsInfo[1]).val("");
                        $('#txtCompanyTotalValue_'+dtlsInfo[0]+'_'+dtlsInfo[1]).val("");
                        return;
                    }
                } 
                if (com_allocated_qty==0) $('#txtCompanyAllocatedQty_'+i+'_'+mm).val("");
                else $('#txtCompanyAllocatedQty_'+i+'_'+mm).val(com_allocated_qty);

                com_neg_price = $('#txtCompanyNeg_'+i+'_'+mm).val()*1;
                com_allocated_qty_again = $('#txtCompanyAllocatedQty_'+i+'_'+mm).val()*1;
                total_value = com_neg_price*com_allocated_qty_again;               
                if (total_value==0)  $('#txtCompanyTotalValue_'+i+'_'+mm).val("");
                else $('#txtCompanyTotalValue_'+i+'_'+mm).val(total_value.toFixed(6));

                if (com_allocated_qty_again > 0) {
                    tot_allocated_qty += com_allocated_qty_again;
                }                
                //alert(tot_allocated_qty);
                if (basis !=3){
                    if (req_qty < tot_allocated_qty){
                        alert("Allocated Qty Exceeds Req Qty.");
                        $('#txtCompanyAllocatedQtyPercentage_'+dtlsInfo[0]+'_'+dtlsInfo[1]).val("");
                        $('#txtCompanyAllocatedQty_'+dtlsInfo[0]+'_'+dtlsInfo[1]).val("");
                        $('#txtCompanyTotalValue_'+dtlsInfo[0]+'_'+dtlsInfo[1]).val("");
                        return;
                    }
                } 
            }                      
        }

        let grandTotalValue=0;
        if (id==1) //company
        {
            for (var i=1; i<=row_num; i++)
            {
                var txtCompanyTotalValue=$("#txtCompanyTotalValue_"+i+"_"+dtlsInfo[1]).val();
                grandTotalValue=grandTotalValue+txtCompanyTotalValue*1;
            }
            $('#companyTotalValue'+dtlsInfo[1]).html(grandTotalValue.toFixed(6));
        }

        if (id==2) //supplier 
        {
            for (var i=1; i<=row_num; i++)
            {
                var txttotalvalue=$("#txttotalvalue_"+i+"_"+dtlsInfo[1]).val();
                grandTotalValue=grandTotalValue+txttotalvalue*1;
            }
            $('#suppTotalValue'+dtlsInfo[1]).html(grandTotalValue.toFixed(6));
        }
    }

    /*function calculate_value(basis)
    {
        var row_num=$('#tbl_details tbody tr').length;
        var supplier_id = $('#supplier_id').val();
        var col_num_arr = supplier_id.split(',');
        var col_num = col_num_arr.length; 
        var company_name_id = $('#cbo_company_name').val();
        var company_num_arr = company_name_id.split(',');
        if(company_name_id!='') var company_num = company_num_arr.length;
        else var company_num =0;       

        for (var i=1; i<=row_num; i++)
        {
            var supp_neg_price=com_neg_price="";
            var supp_allocated_qty=com_allocated_qty="";
            var supp_tot_allocated_qty=com_tot_allocated_qty=0;
            var total_value="";
            var allocated_qty="";
            var req_qty = $('#txtQty_'+i).val()*1;
            for (var m=0; m<col_num; m++)
            {
                var mm=col_num_arr[m];
                supp_neg_price = $('#txtneg_'+i+'_'+mm).val()*1;
                supp_allocated_qty = $('#txtallocatedqty_'+i+'_'+mm).val()*1;
                if (basis==3){
                    if (req_qty < supp_allocated_qty){
                        alert("Allocated Qty Exceeds Req Qty.");
                        $('#txttotalvalue_'+i+'_'+mm).val("");
                        return;
                    }
                }             
                total_value = supp_neg_price*supp_allocated_qty;
                if (total_value==0) $('#txttotalvalue_'+i+'_'+mm).val("");
                else $('#txttotalvalue_'+i+'_'+mm).val(total_value.toFixed(2));
                supp_tot_allocated_qty += supp_allocated_qty;
               
            }

            for (var m=0; m<company_num; m++)
            {
                var mm=company_num_arr[m];
                com_neg_price = $('#txtCompanyNeg_'+i+'_'+mm).val()*1;
                com_allocated_qty = $('#txtCompanyAllocatedQty_'+i+'_'+mm).val()*1;
                if (basis==3){
                    if (req_qty < com_allocated_qty){
                        alert("Allocated Qty Exceeds Req Qty.");
                        $('#txtCompanyTotalValue_'+i+'_'+mm).val("");
                        return;
                    }
                }
                total_value = com_neg_price*com_allocated_qty;
                if (total_value==0)  $('#txtCompanyTotalValue_'+i+'_'+mm).val("");
                else $('#txtCompanyTotalValue_'+i+'_'+mm).val(total_value.toFixed(2));
                com_tot_allocated_qty += com_allocated_qty;
            }

            if (basis !=3){
                allocated_qty = supp_tot_allocated_qty*1+com_tot_allocated_qty*1;
                if (req_qty < allocated_qty){
                    alert("Allocated Qty Exceeds Req Qty.");
                    return;
                }
            }    
        }
    }*/
	
    function openmypage_cs_no()
	{
        var cbo_company_id = $('#cbo_company_id').val();
        var page_link='requires/comparative_statement_accessories_controller.php?action=system_popup&cbo_company_id='+cbo_company_id;
        var title='Search CS PopUp';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=430px,center=1,resize=0,scrolling=0','../')
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var theemail=this.contentDoc.getElementById("selected_id");
            if (theemail.value!="")
            {
                freeze_window(5);
                get_php_form_data(theemail.value, "populate_data_from_search_popup", "requires/comparative_statement_accessories_controller" );
				
				
                //$('#generate_cs').attr('disabled',true);
				$('#cbo_basis_name').attr('disabled',true);
                $('#cbo_size_lavel').attr('disabled',true);
                var update_id = $('#update_id').val();
				
				show_list_view(document.getElementById('cbo_basis_name').value+'**'+document.getElementById('txt_requisition_mst').value+'**'+document.getElementById('txt_requisition_dtls').value+'**'+document.getElementById('supplier_id').value+'**'+update_id, 'load_statment_table', 'statment_tbl', 'requires/comparative_statement_accessories_controller', 'setFilterGrid(\'statment_tbl\',-1)');
				
				
				// var row_num=1;
				// $("#tbl_details_test").find('tbody tr').each(function(index, element) 
				// {

				// 	$(this).find('input[name="txtItemQuality[]"]').attr("onChange","copy_value(this.value,'txtItemQuality_',"+row_num+")");
				// 	$(this).find('input[name="txtItemColor[]"]').attr("onChange","copy_value(this.value,'txtItemColor_',"+row_num+")");
				// 	$(this).find('input[name="txtSizeLength[]"]').attr("onChange","copy_value(this.value,'txtSizeLength_',"+row_num+")");
  
                //     row_num++;
				// });
				
                set_button_status(1, permission, 'fnc_comparative_statement',1);
                release_freezing();
            }
        }
	}
	
	function form_reset_cs(str)
	{
		var cs_basis=$("#cbo_basis_name").val();
        var cbo_company_id = $('#cbo_company_id').val();
        if(cbo_company_id<1)
        {
            if(cs_basis==3)
            {
                $("#comparativestatement_1").find('input[type="text"],input[type="hidden"]:not([name="cs_generate_check"])').val('');
                $("#comparativestatement_1").find('select:not([name="cbo_basis_name"])').val(0);
                $("#cs_tbl").html("");
                $("#statment_tbl").html("");
                $('#cbo_basis_name').attr('disabled',false);
                $('#cbo_size_lavel').attr('disabled',false);
                $('#txt_demand').attr('disabled',false);
                set_button_status(1, permission, 'fnc_comparative_statement',0);
            }
            else
            {
                if(str==1)
                {
                    $("#comparativestatement_1").find('input[type="text"],input[type="hidden"]:not([name="cs_generate_check"])').val('');
                    $("#comparativestatement_1").find('select:not([name="cbo_basis_name"])').val(0);
                    $("#cs_tbl").html("");
                    $("#statment_tbl").html("");
                    $('#cbo_basis_name').attr('disabled',false);

                    $('#cbo_size_lavel').attr('disabled',false);
                    $('#txt_demand').attr('disabled',false);
                    set_button_status(1, permission, 'fnc_comparative_statement',0);
                }
                else
                {
                    $("#comparativestatement_1").find('input[type="text"],input[type="hidden"]:not([name="cs_generate_check"])').val('');
                    //$("#comparativestatement_1").find('select').val(0);
                    $("#cs_tbl").html("");
                    $("#statment_tbl").html("");
                    $('#cbo_basis_name').attr('disabled',false);
                    $('#cbo_size_lavel').attr('disabled',false);
                    $('#txt_demand').attr('disabled',false);
                    set_button_status(1, permission, 'fnc_comparative_statement',0);
                }
            }
        }
		
	}

    function chkNegPrice(val,id,dtlsId)
	{
        var dtlsInfo=dtlsId.split('**');
        var basis=$('#cbo_basis_name').val();
        var costPrice=$("#txtRate_"+dtlsInfo[0]).val()*1;
        var reqQuantity=$("#txtQty_"+dtlsInfo[0]).val()*1;
        var rownum = $('#tbl_details tbody tr').length;

        if(id==1)
        {
            /* if(val>costPrice)
            {
                alert("Costing Price will be equal or less than … not allow higher");
                $("#txtCompanyNeg_"+dtlsInfo[0]+"_"+dtlsInfo[1]).val('');               
                $("#txtCompanyAllocatedQtyPercentage_"+dtlsInfo[0]+"_"+dtlsInfo[1]).val('');               
                $("#txtCompanyAllocatedQty_"+dtlsInfo[0]+"_"+dtlsInfo[1]).val('');  
                //alert($("#txtCompanyTotalValue_"+dtlsInfo[0]+"_"+dtlsInfo[1]).val());         
                $("#txtCompanyTotalValue_"+dtlsInfo[0]+"_"+dtlsInfo[1]).val('');            
            } */
            $("#txtCompanyAllocatedQtyPercentage_"+dtlsInfo[0]+"_"+dtlsInfo[1]).val(100);
            $("#txtCompanyAllocatedQty_"+dtlsInfo[0]+"_"+dtlsInfo[1]).val(reqQuantity);
            var companyTotalValue=$("#txtCompanyNeg_"+dtlsInfo[0]+"_"+dtlsInfo[1]).val()*$("#txtCompanyAllocatedQty_"+dtlsInfo[0]+"_"+dtlsInfo[1]).val();
            $("#txtCompanyTotalValue_"+dtlsInfo[0]+"_"+dtlsInfo[1]).val(companyTotalValue.toFixed(6));            

            let grandTotalValue=0;
            for( var i=1; i<=rownum; i++)
            {
                var companyTotalValue=$("#txtCompanyTotalValue_"+i+"_"+dtlsInfo[1]).val();
                grandTotalValue=grandTotalValue+companyTotalValue*1;
            }
            $("#companyTotalValue"+dtlsInfo[1]).html(grandTotalValue.toFixed(6));
            
        }
        if(id==2)
        {
            /* if(val>costPrice)
            {
                alert("Costing Price will be equal or less than … not allow higher");
                $("#txtneg_"+dtlsInfo[0]+"_"+dtlsInfo[1]).val('');             
                $("#txtallocatedqtypercentage_"+dtlsInfo[0]+"_"+dtlsInfo[1]).val('');               
                $("#txtallocatedqty_"+dtlsInfo[0]+"_"+dtlsInfo[1]).val('');               
                $("#txttotalvalue_"+dtlsInfo[0]+"_"+dtlsInfo[1]).val('');
            } */
            $("#txtallocatedqtypercentage_"+dtlsInfo[0]+"_"+dtlsInfo[1]).val(100);
            $("#txtallocatedqty_"+dtlsInfo[0]+"_"+dtlsInfo[1]).val(reqQuantity);
            var totalvalue=$("#txtneg_"+dtlsInfo[0]+"_"+dtlsInfo[1]).val()*$("#txtallocatedqty_"+dtlsInfo[0]+"_"+dtlsInfo[1]).val();
            $("#txttotalvalue_"+dtlsInfo[0]+"_"+dtlsInfo[1]).val(totalvalue.toFixed(6));

            var totalValue=0;
            for( var i=1; i<=rownum; i++)
            {
                totalValue += $("#txttotalvalue_"+i+"_"+dtlsInfo[1]).val()*1;
				//if(totalValue>0) grandTotalValue+=totalvalue*1;
				//alert($("#txttotalvalue_"+i+"_"+dtlsInfo[1]).val()*1+"="+totalValue+"="+i+"="+dtlsInfo[1]);
            }
            $("#suppTotalValue"+dtlsInfo[1]).html(totalValue.toFixed(6));
        }         
	}

    function fn_price_check(type,id)
	{
        var rownum = $('#tbl_details tbody tr').length;
        //alert(rownum);
        if (type==1)  //company
        {
            var company_check = $("#txt_company_check_"+id).is(":checked");
            for( var i=1; i<=rownum; i++)
            {
                var costPrice=$("#txtRate_"+i).val()*1;
                var reqQuantity=$("#txtQty_"+i).val()*1;
                var txtAmt=$("#txtAmt_"+i).val()*1;
                
                var txtCompanyNeg=$("#txtCompanyNeg_"+i+'_'+id).val()*1;

                var companyTotalValue=reqQuantity*txtCompanyNeg;
                $("#txtCompanyTotalValue_"+i+'_'+id).val(companyTotalValue.toFixed(2));                
            }
            if (company_check==true){
                if(txtCompanyNeg>costPrice){                        
                    alert("Costing Price will be equal or less than recommend supplier… not allow higher");
                    $("#txt_company_check_"+id).prop("checked", false);
                    //$("#txtCompanyNeg_"+i+'_'+id).val('');
                    //$("#txtCompanyTotalValue_"+i+'_'+id).val('');
                }                   
            } 
        }
        else  //Supplier
        {
            var supplier_check = $("#txt_supplier_check_"+id).is(":checked");
            let txtTotalAmt=0;
            let txttotalvalue=0;
            for( var i=1; i<=rownum; i++)
            {
                var costPrice=$("#txtRate_"+i).val()*1;
                var reqQuantity=$("#txtQty_"+i).val()*1;
                var txtAmt=$("#txtAmt_"+i).val()*1;
                txtTotalAmt = txtAmt+txtTotalAmt;                
                var txtneg=$("#txtneg_"+i+'_'+id).val()*1;

                var totalvalue=reqQuantity*txtneg;
                $("#txttotalvalue_"+i+'_'+id).val(totalvalue.toFixed(2));
                txttotalvalue = totalvalue*1 + txttotalvalue;
            }

            if (supplier_check==true){                
                if(txttotalvalue>txtTotalAmt){
                    alert("Sum of Total Value will be equal or less than TTL Total Amount");
                    $("#txt_supplier_check_"+id).prop("checked", false);                   
                }                   
            }
        }
    }

    function fn_payterm_check()
	{
        var row_num = $('#tbl_details tbody tr').length;
        var supplier_id = $('#supplier_id').val();
        var col_num_arr = supplier_id.split(',');
        var col_num = col_num_arr.length; 
        var company_name_id = $('#cbo_company_name').val();
        var company_num_arr = company_name_id.split(',');
        if(company_name_id!=''){var company_num = company_num_arr.length}else{var company_num =0;}
        //alert(col_num);alert(company_num);

        var payterm_check = $("#txt_payterm_check").is(":checked");
        if (payterm_check==true) var payterm_check=1;
        else var payterm_check=0;
        if (payterm_check==1)
        {
            if (company_num>0)
            {
                for (var i=1; i<=row_num; i++)
                {
                    // company part
                    for (var m=0; m<company_num; m++)
                    {
                        var mm=company_num_arr[m];
                        if (m==0) var txtCompanyPayTerm=$("#txtCompanyPayTerm_"+i+"_"+mm).val()*1;
                        else $("#txtCompanyPayTerm_"+i+"_"+mm).val(txtCompanyPayTerm);
                    }

                    // supplier part
                    for (var m=0; m<col_num; m++)
                    {
                        var mm=col_num_arr[m];
                        $("#txtpayterm_"+i+"_"+mm).val(txtCompanyPayTerm);
                    }
                }
            }
            else
            {
                for (var i=1; i<=row_num; i++)
                {
                    // supplier part
                    for (var m=0; m<col_num; m++)
                    {
                        var mm=col_num_arr[m];
                        if (m==0) var txtpayterm=$("#txtpayterm_"+i+"_"+mm).val()*1;
                        else $("#txtpayterm_"+i+"_"+mm).val(txtpayterm);
                    }
                }
            }
        }        
    }
   
    function openmypage_style_ref_no(page_link,title)
    {
        var hidd_job_id=$('#hidd_job_id').val();
        var txt_style_no=$('#txt_style_no').val();
        var update_id=$('#update_id').val();
        var demand_mst_id=$('#txt_requisition_mst').val();
        var demand_dtls_id=$('#txt_requisition_dtls').val();
        var page_link='requires/comparative_statement_accessories_controller.php?action=style_refno_popup&hidd_job_id='+hidd_job_id+'&demand_mst_id='+demand_mst_id+'&demand_dtls_id='+demand_dtls_id+'&update_id='+update_id;
        var title='Search Style Ref PopUp';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1250px,height=450px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var theemailmstid=$.unique(this.contentDoc.getElementById("hidden_mst_id").value.split(','));
            var theemaildtlsid=this.contentDoc.getElementById("hidden_dtls_id").value;
            var theemailjobid=$.unique(this.contentDoc.getElementById("hidden_job_id").value.split(','));
            var theemailstyleno=$.unique(this.contentDoc.getElementById("hidden_style_no").value.split(','));

            if ( theemaildtlsid !="" )
            {
                $("#txt_requisition_dtls").val(theemaildtlsid);
                $("#hidd_job_id").val(theemailjobid);
                $("#txt_style_no").val(theemailstyleno);
                
                $("#txt_requisition_mst").val(theemailmstid);
                $("#txt_demand").val(theemailmstid);
                $('#cs_generate_check').val(0);
                //disable_enable_fields('cbo_company_name*cbo_buyer_name*cbo_team_leader*cbo_dealing_merchant',1);
                //$('#cs_generate_check').val(0);

            }
        }
    } 

    function change_caption_name(id)
    {        
        if(id==2)
        {
            $("#demand_id_caption").html('Item No').val('');
            $("#txt_demand").attr("placeholder", "Browse Item");
            $('#txt_style_check').prop('checked', false);
            $('#txt_style_no').attr('disabled', true);
        }
        else if(id==3)
        {
            $("#demand_id_caption").html('Style No').val('');
            $("#txt_demand").attr("placeholder", "Browse Style");
            $('#txt_style_check').prop('checked', false);
            $('#txt_style_no').attr('disabled', true);
        }
        else
        {
            $("#demand_id_caption").html('Demand No').val('');
            $("#txt_demand").attr("placeholder", "Browse Demand");   
        }
    } 

    function change_styleref_cs() 
    {
        var basis=$('#cbo_basis_name').val();
        if ( $('#txt_style_check').is(':checked') && basis==1 ){
            $('#txt_style_no').attr('disabled', false);
        } else {
            $('#txt_style_no').attr('disabled', true);
        }
    }

  
    function copy_value(value,field_id,i,suplier="")
    {

        //  alert(value);
        //  alert(field_id);
        //  alert(i);
        var copy_basis=$('input[name="copy_basis"]:checked').val()
        //alert(copy_basis); return;
        var txtGmtsColor=document.getElementById('txtGmtsColor_'+i).value;
        var txtItemSize=document.getElementById('txtItemSize_'+i).value;
        var rowCount = $('#tbl_details tr').length-3;
        
        if(suplier==''){
            var negQty =0;
        }
        else{
            var negQty =  document.getElementById('txtneg_'+i+'_'+suplier).value;
        }
       
        if(field_id==5){
            field_id = 'txtquoted_';
        }
        if(field_id==6){
            field_id = 'txtneg_';
        }
        if(field_id==7){
            field_id = 'txtItemQuality_';
        }
        if(field_id==8){
            field_id = 'txtItemColor_';
        }
        if(field_id==9){
            field_id = 'txtSizeLength_';
        }

        //alert(field_id);
        //if(copy_basis=="no")  calculate_requirement(i)
        for(var j=i; j<=rowCount; j++)
        {
            
            // var isDisabled = document.getElementById(field_id+j).disabled;
            // if(isDisabled==false)
            // {
                // if(document.getElementById('approved_'+j).value==0){

                  
                    if(field_id=='txtItemQuality_')
                    {
                        if(copy_basis==1)
                        {
                            if( txtGmtsColor==document.getElementById('txtGmtsColor_'+j).value)
                            {
                                document.getElementById(field_id+j).value=value;
                            }
                        }
                        else if(copy_basis==2)
                        {
                            if( txtItemSize==document.getElementById('txtItemSize_'+j).value)
                            {
                                document.getElementById(field_id+j).value=value;
                            }
                        }
                    }
                    if(field_id=='txtItemColor_')
                    {
                        if(copy_basis==1)
                        {
                            if( txtGmtsColor==document.getElementById('txtGmtsColor_'+j).value)
                            {
                                document.getElementById(field_id+j).value=value;
                            }
                        }
                        else if(copy_basis==2)
                        {
                            if( txtItemSize==document.getElementById('txtItemSize_'+j).value)
                            {
                                document.getElementById(field_id+j).value=value;
                            }
                        }
                    }
                    if(field_id=='txtSizeLength_')
                    {
                        if(copy_basis==1)
                        {
                            if( txtGmtsColor==document.getElementById('txtGmtsColor_'+j).value)
                            {
                                document.getElementById(field_id+j).value=value;
                            }
                        }
                        else if(copy_basis==2)
                        {
                            if( txtItemSize==document.getElementById('txtItemSize_'+j).value)
                            {
                                document.getElementById(field_id+j).value=value;
                            }
                        }
                    }

                    if(field_id=='txtquoted_')
                    {
                        if(copy_basis==1)
                        {
                            if( txtGmtsColor==document.getElementById('txtGmtsColor_'+j).value)
                            {
                                //alert(field_id+j+sup);
                                document.getElementById(field_id+j+'_'+suplier).value=value;
                            }
                        }
                        else if(copy_basis==2)
                        {
                            if( txtItemSize==document.getElementById('txtItemSize_'+j).value)
                            {
                                document.getElementById(field_id+j+'_'+suplier).value=value;
                            }
                        }
                    } 
                    
                    if(field_id=='txtneg_')
                    {

                        if(copy_basis==1)
                        {
                            if( txtGmtsColor==document.getElementById('txtGmtsColor_'+j).value)
                            {
                                var reqtqty = Math.round(document.getElementById('txtQty_'+j).value);
                                //alert(negQty);
                                var total_val = Math.round(reqtqty*negQty); 
                                document.getElementById(field_id+j+'_'+suplier).value=value;
                                if(negQty>0){
                                    document.getElementById('txtallocatedqtypercentage_'+j+'_'+suplier).value=100;
                                    document.getElementById('txtallocatedqty_'+j+'_'+suplier).value=reqtqty;
                                }
                                document.getElementById('txttotalvalue_'+j+'_'+suplier).value=total_val;

                                // var dtstr = j+"**"+sup;
                                // calculate_allocated_qty(100,2,dtstr);
                                
                            }
                        }
                        else if(copy_basis==2)
                        {
                            if( txtItemSize==document.getElementById('txtItemSize_'+j).value)
                            {
                                //document.getElementById(field_id+j+'_'+sup).value=value;
                                var reqtqty = Math.round(document.getElementById('txtQty_'+j).value);
                                //alert(negQty);
                                var total_val = Math.round(reqtqty*negQty); 
                                document.getElementById(field_id+j+'_'+suplier).value=value;
                                if(negQty>0){
                                    document.getElementById('txtallocatedqtypercentage_'+j+'_'+suplier).value=100;
                                    document.getElementById('txtallocatedqty_'+j+'_'+suplier).value=reqtqty;
                                }
                                document.getElementById('txttotalvalue_'+j+'_'+suplier).value=total_val;

                            }
                        }
                    }
                        
                    if(field_id=='txtItemQuality_' || field_id=='txtItemColor_' || field_id=='txtSizeLength_')
                    {
                        
                        if(copy_basis==1)
                        {
                            if( txtGmtsColor==document.getElementById('txtGmtsColor_'+j).value) document.getElementById(field_id+j).value=value;
                        }
                        else if(copy_basis==2)
                        {
                            if( txtItemSize==document.getElementById('txtItemSize_'+j).value) document.getElementById(field_id+j).value=value;
                        }
                       
                    }

                    if(field_id=='txtquoted_' || field_id=='txtneg_')
                    {
                        
                        if(copy_basis==1)
                        {
                            if( txtGmtsColor==document.getElementById('txtGmtsColor_'+j).value) document.getElementById(field_id+j+'_'+suplier).value=value;
                            // var dtstr = j+"**"+sup;
                            // calculate_allocated_qty(100,2,dtstr);
                        }
                        else if(copy_basis==2)
                        {
                            if( txtItemSize==document.getElementById('txtItemSize_'+j).value) document.getElementById(field_id+j+'_'+suplier).value=value;
                        }
                       
                    }

                    				
                //}
           // }
        }
       
    }
</script>
<body onLoad="set_hotkey()">
    <div align="left">
        <? echo load_freeze_divs ("../../",$permission); ?>
        <div >
            <form name="comparativestatement_1" id="comparativestatement_1" autocomplete="off">
                <fieldset >
                    <legend>Comparative Statement</legend>
                    <table width="1125" border="0" cellpadding="0" cellspacing="2" id="tbl_btb">
                        <tr>
                            <td colspan="4" align="right">CS Number</td>
                            <td colspan="4" >
                                <input style="width:140px;" type="text" title="Double Click to Search" onDblClick="openmypage_cs_no()" class="text_boxes" placeholder="Browse CS Number" name="txt_system_id" id="txt_system_id" readonly />
                                <input type="hidden" name="update_id" id="update_id" />
                            </td>
                        </tr>
                        <tr>
                            <td width="120" class="must_entry_caption">Company Name</td>
                            <td width="150">
                                <? 
                                echo create_drop_down( "cbo_company_id",151,"select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name",1, "-- Select --", $selected,""); 
                                ?>
                            </td>

                            <td width="125" class="must_entry_caption">Basis</td>
                            <td width="150">
                                <? 
                                echo create_drop_down( "cbo_basis_name",150,array(1=>"Demand",2=>"Item",3=>"Style"),'',0,'--Select--',3,"form_reset_cs(1);change_caption_name(this.value);",0);  
                                ?>
                            </td>
                            <td width="125" class="must_entry_caption" id="demand_id_caption">Style No</td>
                            <td width="150">
                                <input style="width:140px;" type="text" title="Double Click to Search" onDblClick="openmypage_demand_no()" class="text_boxes" placeholder="Browse Style" name="txt_demand" id="txt_demand" readonly />
                                <input type="hidden" name="txt_requisition_mst" id="txt_requisition_mst" />
                                <input type="hidden" name="txt_requisition_dtls" id="txt_requisition_dtls" />
                                <input type="hidden" name="cs_generate_check" id="cs_generate_check" value="1" />
                            </td>
                            <td width="125" class="must_entry_caption">CS Date</td>
                            <td width="150"><input style="width:140px " name="txt_cs_date" id="txt_cs_date" class="datepicker" readonly value="<? echo date("d-m-Y"); ?>" /></td>
                            
                        </tr>
                        <tr>
                            <td class="must_entry_caption" width="150">Supplier</td>
                            <td > 
                                <input type="text" name="txt_supplier_name" id="txt_supplier_name" class="text_boxes" style="width:140px;" placeholder="Double Click To Search" onDblClick="openmypage_supplier()" readonly />
                                <input type="hidden" name="supplier_id" id="supplier_id" />
                            </td>
                            <td class="must_entry_caption" >Currency</td>
                            <td>
                                <? echo create_drop_down( "cbo_currency_name", 150, $currency,'',1,'-- Select Currency --',2,""); ?>
                            </td>
                            <td class="must_entry_caption">CS Validity</td>
                            <td>
                                <input style="width:140px " name="txt_validity_date" id="txt_validity_date" class="datepicker" value="<? echo date('d-m-Y', strtotime('+6 month', time())); ?>" readonly />
                            </td>                            
                            <!-- <td class="must_entry_caption">Applicable Company</td> -->
                            <td >In-House Sup. Company</td>
                            <td>
                                <?  echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name", "id,company_name", 0, "", '', "get_php_form_data( this.value, 'company_wise_report_button_setting','requires/comparative_statement_accessories_controller');"); ?>
                            </td>
                            
                        </tr>
                        <tr>
                            <td>Size Lavel</td>
                            <td>
                                <? echo create_drop_down( "cbo_size_lavel", 150, $yes_no,'',1,'-- Select --',2,""); ?>
                            </td>
                            <td>Ready To Approved</td>
                            <td>
                                <? echo create_drop_down( "cbo_ready_to_approved", 150, $yes_no,'',1,'-- Select --',0,""); ?>
                            </td>
                            <td>Style Specific CS</td>
                            <td><input type="checkbox" id="txt_style_check" name="txt_style_check" onClick="change_styleref_cs();"/></td>
                            <td>Style Ref</td>
                            <td>
                            <input name="txt_style_no" id="txt_style_no" style="width:140px;" type="text" title="Double Click to Search" onDblClick="openmypage_style_ref_no()" class="text_boxes" placeholder="Browse Style No." disabled />
                    		<input type="hidden" id="hidd_job_id" name="hidd_job_id" />
                            </td>
                        </tr>
                        <tr>
                            <td >Comments</td>
                            <td colspan="5" >
                                <input type="text" class="text_boxes" id="txt_comments" style="width:700px" >
                            </td>
                            <td colspan="2" align="center">
                                <input type="button" class="formbutton" id="generate_cs" value="Generate CS" onClick="fnc_generate_cs()" style="width:80px" >
                            </td>
                        </tr>
                        <tr>
                            <td align="left" height="10" colspan="3">
                            </td>
                            <td align="left" height="10" colspan="5">
                                <?
                                include("../../terms_condition/terms_condition.php");
                                terms_condition(482,'txt_system_id','../../');
                                ?>
                            </td>
                        </tr>
                    </table>
                    <div id="cs_tbl"></div>
                    <table width="1000" border="0" cellpadding="0" cellspacing="2" id="tbl_btb">
                        <tr>
                            <td colspan="8" height="20" width="100%" align="center">
                                <div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div>
                                <? echo create_drop_down( "cbo_template_id", 100, $report_template_list,'', 0, '', 0, "");?>&nbsp;
                                <?  echo load_submit_buttons( $permission, "fnc_comparative_statement", 0,1,"form_reset_cs(2)",1) ;?>
                                <input type="hidden" id="prev_req_dtls_id" name="prev_req_dtls_id" />
                                <input type="button" name="search" id="search" value="Print 2" onClick="fnc_print_report(2)" style="width:80px;" class="formbuttonplasminus" />
                            </td>  
                        </tr>
                    </table>
                    
                </fieldset>
            </form>
            <div id="statment_tbl"></div>
        </div>
    </div>
</body>
<script>set_multiselect('cbo_company_name','0','0','','0');</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>