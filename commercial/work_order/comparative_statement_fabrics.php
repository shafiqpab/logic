<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create for Comparative Statement [Fabrics]
Functionality	:
JS Functions	:
Created by		:	Rakib
Creation date 	: 	25-12-2021
Updated by 		: 	
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
echo load_html_head_contents("Comparative Statement Fabrics", "../../",  1, 1, $unicode,1,'');

?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
    var permission='<? echo $permission; ?>';
    function openmypage_supplier()
	{
		var supplier_id = $('#supplier_id').val();
		var title = 'Supplier Name';
        var page_link = 'requires/comparative_statement_fabrics_controller.php?supplier_id='+supplier_id+'&action=supplier_name_popup';
		
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
        var cbo_company_id = $('#cbo_company_id').val();
        var basis_id = $('#cbo_basis_name').val();
        var update_id=$('#update_id').val();
        var txt_requisition_mst=$('#txt_requisition_mst').val();
        var txt_requisition_dtls=$('#prev_req_dtls_id').val();
        $('#txt_style_check').prop('checked', false);

        if (basis_id !=3){
            alert("This Basis is not developed!!");
            return;
        }

        if(basis_id==1)
		{
            var title = 'Demand No';			
            var page_link = 'requires/comparative_statement_fabrics_controller.php?action=demand_popup&txt_requisition_mst='+txt_requisition_mst+'&txt_requisition_dtls='+txt_requisition_dtls+'&update_id='+update_id;
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
                //get_php_form_data(requisition_dtls+'**'+basis_id, "nominated_supplier_name_popup", "requires/comparative_statement_fabrics_controller" );
				
            }
        }
        else if(basis_id==2)
		{
            var title = 'Item No';
            var page_link = 'requires/comparative_statement_fabrics_controller.php?action=item_popup&txt_requisition_mst='+txt_requisition_mst+'&txt_requisition_dtls='+txt_requisition_dtls+'&update_id='+update_id;
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
                //get_php_form_data(item_dtls+'**'+basis_id, "nominated_supplier_name_popup", "requires/comparative_statement_fabrics_controller" );
            }
        }
        else if(basis_id==3)
        {
            var title = 'Style No';            
            var page_link = 'requires/comparative_statement_fabrics_controller.php?action=style_popup&txt_requisition_mst='+txt_requisition_mst+'&txt_requisition_dtls='+txt_requisition_dtls+'&cbo_company_id='+cbo_company_id+'&update_id='+update_id;
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1170px,height=450px,center=1,resize=1,scrolling=0','../');
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
                get_php_form_data(requisition_dtls+'**'+basis_id, "nominated_supplier_name_popup", "requires/comparative_statement_fabrics_controller" );
                
            }
        }
    }

    function fnc_generate_cs() 
    {
        if( form_validation('cbo_company_id*cbo_basis_name*txt_demand*txt_supplier_name','Company*Basis*Demand No/Item*Supplier')==false )
		{
			return;
		}
        var update_id=$("#update_id").val();
		//var job_ids=$("#hidd_job_id").val();
		var txt_requisition_dtls=$("#prev_req_dtls_id").val();
        show_list_view(document.getElementById('cbo_basis_name').value+'**'+document.getElementById('txt_requisition_mst').value+'**'+document.getElementById('txt_requisition_dtls').value+'**'+document.getElementById('supplier_id').value+'**'+update_id+'**'+txt_requisition_dtls+'**'+document.getElementById('cbo_company_name').value, 'load_cs_table', 'cs_tbl', 'requires/comparative_statement_fabrics_controller', 'setFilterGrid(\'cs_tbl\',-1)');

       show_list_view(document.getElementById('cbo_basis_name').value+'**'+document.getElementById('txt_requisition_mst').value+'**'+document.getElementById('txt_requisition_dtls').value+'**'+document.getElementById('supplier_id').value+'**'+update_id, 'load_statment_table', 'statment_tbl', 'requires/comparative_statement_fabrics_controller', 'setFilterGrid(\'statment_tbl\',-1)');
		$('#cs_generate_check').val(1);
    }
	
    function fnc_comparative_statement(operation)
    {
        if(operation==4)
		{
            var form_caption=$( "div.form_caption" ).html();
	 	    print_report( $('#update_id').val()+'*'+form_caption+'*'+$('#cbo_company_id').val()+'*'+$('#cbo_template_id').val(), "comparative_statement_print", "requires/comparative_statement_fabrics_controller" )
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
            data_dtls += '&txtItemCategory_' + i + '=' + $('#txtItemCategory_'+i).attr('title') + '&txtdeterminationid_' + i + '=' + $('#txtdeterminationid_'+i).val() + '&txtfabriccostdtlsid_' + i + '=' + $('#txtfabriccostdtlsid_'+i).val() + '&txtjobid_' + i + '=' + $('#txtjobid_'+i).val() + '&txtItemDescrip_' + i + '=' + $('#txtItemDescrip_'+i).val() + '&txtMillReff_' + i + '=' + $('#txtMillReff_'+i).val() + '&txtRdNo_' + i + '=' + $('#txtRdNo_'+i).val() + '&txtWeight_' + i + '=' + $('#txtWeight_'+i).val() + '&txtCutableWidth_' + i + '=' + $('#txtCutableWidth_'+i).val()+ '&txtUom_' + i + '=' + $('#txtUom_'+i).attr('title')+ '&txtQty_' + i + '=' + $('#txtQty_'+i).attr('title')+ '&txtRate_' + i + '=' + $('#txtRate_'+i).attr('title')+ '&txtAmt_' + i + '=' + $('#txtAmt_'+i).attr('title') + '&txtAllCompanyQuoted_' + i + '=' + $('#txtAllCompanyQuoted_'+i).val()+ '&txtAllCompanyNeg_' + i + '=' + $('#txtAllCompanyNeg_'+i).val()+ '&txtAllCompanyCon_' + i + '=' + $('#txtAllCompanyCon_'+i).val();
            var req_qty=$('#txtQty_'+i).val()*1;
            var quoted_validation=0;
            var neg_validation=0;
           
            for (var m=0; m<col_num; m++)
            {
                var mm=col_num_arr[m];
                var supplier_check = $("#txt_supplier_check_"+mm).is(":checked"); //txtLCType
                if (supplier_check==true) var supplier_check_value=1;
                else var supplier_check_value=0;
                data_supplier += '&txtsuppier_' + i + '_' + mm + '=' + mm + '&txtquoted_' + i + '_' + mm + '=' + $('#txtquoted_'+i+'_'+mm).val() + '&txtneg_' + i + '_' + mm + '=' + $('#txtneg_'+i+'_'+mm).val()+ '&txtcon_' + i + '_' + mm + '=' + $('#txtcon_'+i+'_'+mm).val()+ '&txtSuppCutableWidth_' + i + '_' + mm + '=' + $('#txtSuppCutableWidth_'+i+'_'+mm).val()+ '&txtpayterm_' + i + '_' + mm + '=' + $('#txtpayterm_'+i+'_'+mm).val()+ '&txtLCType_' + i + '_' + mm + '=' + $('#txtLCType_'+i+'_'+mm).val()+ '&txttenor_' + i + '_' + mm + '=' + $('#txttenor_'+i+'_'+mm).val()+ '&cboShipMode_' + i + '_' + mm + '=' + $('#cboShipMode_'+i+'_'+mm).val()+ '&cboSource_' + i + '_' + mm + '=' + $('#cboSource_'+i+'_'+mm).val()+ '&txt_supplier_check_' + mm + '=' + supplier_check_value+ '&origin_' + mm + '=' + $('#origin_'+mm).val();
				
                quoted_validation += +$('#txtquoted_'+i+'_'+mm).val();
                neg_validation += +$('#txtneg_'+i+'_'+mm).val();
            }
            
            for (var m=0; m<company_num; m++)
            {
                var mm=company_num_arr[m];
                var company_check = $("#txt_company_check_"+mm).is(":checked");
                if (company_check==true) var company_check_value=1;
                else var company_check_value=0;
                data_company += '&txtCompany_' + i + '_' + mm + '=' + mm + '&txtCompanyQuoted_' + i + '_' + mm + '=' + $('#txtCompanyQuoted_'+i+'_'+mm).val() + '&txtCompanyNeg_' + i + '_' + mm + '=' + $('#txtCompanyNeg_'+i+'_'+mm).val()+ '&txtCompanyCon_' + i + '_' + mm + '=' + $('#txtCompanyCon_'+i+'_'+mm).val()+ '&txtCompanyCutableWidth_' + i + '_' + mm + '=' + $('#txtCompanyCutableWidth_'+i+'_'+mm).val()+ '&txtCompanyPayTerm_' + i + '_' + mm + '=' + $('#txtCompanyPayTerm_'+i+'_'+mm).val()+ '&txtCompanyTenor_' + i + '_' + mm + '=' + $('#txtCompanyTenor_'+i+'_'+mm).val()+ '&companyShipMode_' + i + '_' + mm + '=' + $('#companyShipMode_'+i+'_'+mm).val()+ '&companySource_' + i + '_' + mm + '=' + $('#companySource_'+i+'_'+mm).val()+ '&txt_company_check_' + mm + '=' + company_check_value+ '&companyOrigin_' + mm + '=' + $('#companyOrigin_'+mm).val();

                quoted_validation += +$('#txtCompanyQuoted_'+i+'_'+mm).val();
                neg_validation += +$('#txtCompanyNeg_'+i+'_'+mm).val();
            }
           

            if(quoted_validation==0 || neg_validation==0)
			{
                alert("Please Fill the Price");
                return;
            }            
        }
        //alert(data_supplier);return;
        //alert(data_company);return;
        // console.log(data_supplier);
		//alert(data_dtls+"="+data_supplier);return;
        //var data_mst=get_submitted_data_string('cbo_basis_name*txt_demand*txt_requisition_mst*txt_requisition_dtls*txt_cs_date*supplier_id*cbo_currency_name*txt_validity_date*cbo_approved*cbo_company_name*txt_comments*update_id*txt_system_id',"../../");
        var data="action=save_update_delete&operation="+operation+"&row_num="+row_num+"&data_dtls="+data_dtls+"&col_num="+col_num+"&data_supplier="+data_supplier+"&company_num="+company_num+"&data_company="+data_company+"&txt_style_check="+txt_style_check+get_submitted_data_string('cbo_company_id*cbo_basis_name*txt_demand*txt_requisition_mst*txt_requisition_dtls*txt_cs_date*supplier_id*cbo_currency_name*txt_validity_date*cbo_ready_to_approved*cbo_company_name*txt_comments*update_id*txt_system_id*hidd_job_id*txt_style_no',"../../");
        //alert(data);return;
        freeze_window(operation);
        http.open("POST","requires/comparative_statement_fabrics_controller.php",true);
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
				$('#cbo_company_id').attr('disabled',true);
                $('#cbo_basis_name').attr('disabled',true);
                //$('#generate_cs').attr('disabled',true);
				set_button_status(1, permission, 'fnc_comparative_statement',1);
				
			}
			if(parseInt(trim(reponse[0]))==2)
			{
				$("#comparativestatement_1").find('input[type="text"],input[type="hidden"]').val('');
				$("#comparativestatement_1").find('select:not([name="cbo_basis_name"])').val(0);
                $('#cbo_company_id').attr('disabled',false);
				$('#cbo_basis_name').attr('disabled',false);
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

    function openmypage_cs_no()
	{
        var cbo_company_id = $('#cbo_company_id').val();
        var page_link='requires/comparative_statement_fabrics_controller.php?action=system_popup&cbo_company_id='+cbo_company_id;
        var title='Search CS PopUp';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=430px,center=1,resize=0,scrolling=0','../')
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var theemail=this.contentDoc.getElementById("selected_id");
            if (theemail.value!="")
            {
                freeze_window(5);
                get_php_form_data(theemail.value, "populate_data_from_search_popup", "requires/comparative_statement_fabrics_controller" );
                //$('#generate_cs').attr('disabled',true);
                $('#cbo_company_id').attr('disabled',true);
				$('#cbo_basis_name').attr('disabled',true);
                var update_id = $('#update_id').val();
				
				show_list_view(document.getElementById('cbo_basis_name').value+'**'+document.getElementById('txt_requisition_mst').value+'**'+document.getElementById('txt_requisition_dtls').value+'**'+document.getElementById('supplier_id').value+'**'+update_id, 'load_statment_table', 'statment_tbl', 'requires/comparative_statement_fabrics_controller', 'setFilterGrid(\'statment_tbl\',-1)');
                set_button_status(1, permission, 'fnc_comparative_statement',1);
                release_freezing();
            }
        }
	}
	
	function form_reset_cs(str)
	{
		var cs_basis=$("#cbo_basis_name").val();
		if(cs_basis==3)
		{
			$("#comparativestatement_1").find('input[type="text"],input[type="hidden"]:not([name="cs_generate_check"])').val('');
			$("#comparativestatement_1").find('select:not([name="cbo_basis_name"])').val(0);
			$("#cs_tbl").html("");
			$("#statment_tbl").html("");
			$('#cbo_basis_name').attr('disabled',false);
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
				$('#txt_demand').attr('disabled',false);
				set_button_status(1, permission, 'fnc_comparative_statement',0);
			}
			else
			{
				$("#comparativestatement_1").find('input[type="text"],input[type="hidden"]:not([name="cs_generate_check"])').val('');
				$("#comparativestatement_1").find('select').val(0);
				$("#cs_tbl").html("");
				$("#statment_tbl").html("");
				$('#cbo_basis_name').attr('disabled',false);
				$('#txt_demand').attr('disabled',false);
				set_button_status(1, permission, 'fnc_comparative_statement',0);
			}
		}		
	}

    function chkNegPrice(val,id,dtlsId)
	{
        var dtlsInfo=dtlsId.split('**');
        var basis=$('#cbo_basis_name').val();
        //var costPrice=$("#txtRate_"+dtlsInfo[0]).val()*1;
        //var reqQuantity=$("#txtQty_"+dtlsInfo[0]).val()*1;

        var rownum = $('#tbl_details tbody tr').length;

        if(id==1) //company
        {
            //if ($('#cbo_basis_name_'+val).val()==)
            //if ($(cbo_basis_name_+val).is(":checked"))
            //var company_check = $("#txt_company_check_"+dtlsInfo[1]).is(":checked");
            for( var i=1; i<=rownum; i++)
            {
                var costPrice=$("#txtRate_"+i).attr('title')*1;
                var reqQuantity=$("#txtQty_"+i).attr('title')*1;
                var txtCompanyNeg=$("#txtCompanyNeg_"+i+"_"+dtlsInfo[1]).val()*1;

                /* if (company_check==true)
                {
                    if(txtCompanyNeg>costPrice)
                    {
                        alert("Costing Price will be equal or less than … not allow higher");
                        $("#txtCompanyNeg_"+dtlsInfo[0]+"_"+dtlsInfo[1]).val('');     
                        $("#txtCompanyTotalValue_"+dtlsInfo[0]+"_"+dtlsInfo[1]).val('');            
                    }
                } */

                var companyTotalValue=reqQuantity*txtCompanyNeg*1;
                $("#txtCompanyTotalValue_"+i+"_"+dtlsInfo[1]).val(companyTotalValue.toFixed(4));
            }           
           
        }
        if(id==2) //supplier
        {
            //var supplier_check = $("#txt_supplier_check_"+dtlsInfo[1]).is(":checked");
            for( var i=1; i<=rownum; i++)
            {
                var costPrice=$("#txtRate_"+i).attr('title')*1;
                var reqQuantity=$("#txtQty_"+i).attr('title')*1;
                var txtneg=$("#txtneg_"+i+"_"+dtlsInfo[1]).val()*1;               

                /* if (supplier_check==true)
                {
                    if(txtneg>costPrice)
                    {
                        alert("Costing Price will be equal or less than … not allow higher");
                        $("#txtneg_"+dtlsInfo[0]+"_"+dtlsInfo[1]).val('');
                        $("#txttotalvalue_"+dtlsInfo[0]+"_"+dtlsInfo[1]).val('');                    
                    }                    
                } */

                var totalvalue=reqQuantity*txtneg*1;
                $("#txttotalvalue_"+i+"_"+dtlsInfo[1]).val(totalvalue.toFixed(4));
            }
            
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
                var costPrice=$("#txtRate_"+i).attr('title')*1;
                var reqQuantity=$("#txtQty_"+i).attr('title')*1;
                var txtCompanyNeg=$("#txtCompanyNeg_"+i+'_'+id).val()*1;

                var companyTotalValue=reqQuantity*txtCompanyNeg*1;
                $("#txtCompanyTotalValue_"+i+'_'+id).val(companyTotalValue.toFixed(4));

                if (company_check==true){
                    if(txtCompanyNeg>costPrice){                        
                        alert("Costing Price will be equal or less than recommend supplier… not allow higher");
                        $("#txt_company_check_"+id).prop("checked", false);
                        //$("#txtCompanyNeg_"+i+'_'+id).val('');
                        //$("#txtCompanyTotalValue_"+i+'_'+id).val('');
                    }                   
                }         
                
            }
        }
        else  //Supplier
        {
            var supplier_check = $("#txt_supplier_check_"+id).is(":checked");
            for( var i=1; i<=rownum; i++)
            {
                var costPrice=$("#txtRate_"+i).attr('title')*1;
                var reqQuantity=$("#txtQty_"+i).attr('title')*1;
                var txtneg=$("#txtneg_"+i+'_'+id).val()*1;

                var totalvalue=reqQuantity*txtneg*1;
                $("#txttotalvalue_"+i+'_'+id).val(totalvalue.toFixed(4));
                
                if (supplier_check==true){
                    if(txtneg>costPrice){
                        alert("Costing Price will be equal or less than recommend supplier… not allow higher");
                        $("#txt_supplier_check_"+id).prop("checked", false);                    
                        //$("#txtneg_"+i+'_'+id).val('');
                        //$("#txttotalvalue_"+i+'_'+id).val('');                        
                    }                   
                }                
            }
        }
    }

    function fn_cutablewidth_check()
	{
        var row_num = $('#tbl_details tbody tr').length;
        var supplier_id = $('#supplier_id').val();
        var col_num_arr = supplier_id.split(',');
        var col_num = col_num_arr.length; 
        var company_name_id = $('#cbo_company_name').val();
        var company_num_arr = company_name_id.split(',');
        if(company_name_id!=''){var company_num = company_num_arr.length}else{var company_num =0;}
        //alert(col_num);alert(company_num);

        var cutablewidth_check = $("#txt_cutablewidth_check").is(":checked");
        if (cutablewidth_check==true) var cutablewidth_check=1;
        else var cutablewidth_check=0;
        if (cutablewidth_check==1)
        {
            if (company_num>0)
            {
                for (var i=1; i<=row_num; i++)
                {
                    // company part
                    for (var m=0; m<company_num; m++)
                    {
                        var mm=company_num_arr[m];
                        if (m==0) var txtCompanyCutableWidth=$("#txtCompanyCutableWidth_"+i+"_"+mm).val()*1;
                        else $("#txtCompanyCutableWidth_"+i+"_"+mm).val(txtCompanyCutableWidth);
                    }

                    // supplier part
                    for (var m=0; m<col_num; m++)
                    {
                        var mm=col_num_arr[m];
                        $("#txtSuppCutableWidth_"+i+"_"+mm).val(txtCompanyCutableWidth);
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
                        if (m==0) var txtSuppCutableWidth=$("#txtSuppCutableWidth_"+i+"_"+mm).val()*1;
                        else $("#txtSuppCutableWidth_"+i+"_"+mm).val(txtSuppCutableWidth);
                    }
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
        var page_link='requires/comparative_statement_fabrics_controller.php?action=style_refno_popup&hidd_job_id='+hidd_job_id+'&demand_mst_id='+demand_mst_id+'&demand_dtls_id='+demand_dtls_id+'&update_id='+update_id;
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
        if (id==1) 
        {
            $("#demand_id_caption").html('Demand No').val('');
            $("#txt_demand").attr("placeholder", "Browse Demand");   
        }
        else if(id==2)
        {
            $("#demand_id_caption").html('Item No').val('');
            $("#txt_demand").attr("placeholder", "Browse Item");
            $('#txt_style_check').prop('checked', false);
            $('#txt_style_no').attr('disabled', true);
        }
        else
        {
            $("#demand_id_caption").html('Style No').val('');
            $("#txt_demand").attr("placeholder", "Browse Style");
            $('#txt_style_check').prop('checked', false);
            $('#txt_style_no').attr('disabled', true);
        } 
    } 

    function change_styleref_cs() 
    {
        if ( $('#txt_style_check').is(':checked')){
            $('#txt_style_no').attr('disabled', false);
        } else {
            $('#txt_style_no').attr('disabled', true);
        }
    }    

</script>
<body onLoad="set_hotkey()">
    <div align="left">
        <? echo load_freeze_divs ("../../",$permission); ?>
        <div >
            <form name="comparativestatement_1" id="comparativestatement_1" autocomplete="off">
                <fieldset style="width:1150px;">
                    <legend>Comparative Statement Fabrics</legend>
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
                            <td width="120" class="must_entry_caption">Basis</td>
                            <td width="150">
                                <? 
                                echo create_drop_down( "cbo_basis_name",151,array(1=>"Demand",3=>"Style"),'',0,'--Select--',3,"form_reset_cs(1);change_caption_name(this.value);",0); //2=>"Item",
                                ?>
                            </td>
                            <td width="120" class="must_entry_caption" id="demand_id_caption">Style No</td>
                            <td width="150">
                                <input style="width:140px;" type="text" title="Double Click to Search" onDblClick="openmypage_demand_no()" class="text_boxes" placeholder="Browse Style" name="txt_demand" id="txt_demand" readonly />
                                <input type="hidden" name="txt_requisition_mst" id="txt_requisition_mst" />
                                <input type="hidden" name="txt_requisition_dtls" id="txt_requisition_dtls" />
                                <input type="hidden" name="cs_generate_check" id="cs_generate_check" value="1" />
                            </td>                            
                            <td class="must_entry_caption" width="120">Supplier</td>
                            <td > 
                                <input type="text" name="txt_supplier_name" id="txt_supplier_name" class="text_boxes" style="width:140px;" placeholder="Double Click To Search" onDblClick="openmypage_supplier()" readonly />
                                <input type="hidden" name="supplier_id" id="supplier_id" />
                            </td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption" >Currency</td>
                            <td>
                                <? echo create_drop_down( "cbo_currency_name", 151, $currency,'',1,'-- Select Currency --',2,""); ?>
                            </td>
                            <td class="must_entry_caption">CS Validity</td>
                            <td>
                                <input style="width:140px " name="txt_validity_date" id="txt_validity_date" class="datepicker" value="<? echo date('d-m-Y', strtotime('+6 month', time())); ?>" readonly />
                            </td>
                            <td width="120" class="must_entry_caption">CS Date</td>
                            <td width="150"><input style="width:140px " name="txt_cs_date" id="txt_cs_date" class="datepicker" readonly value="<? echo date("d-m-Y"); ?>" /></td>                      
                            <!-- <td class="must_entry_caption">Applicable Company</td> -->
                            <td >In-House Sup. Company</td>
                            <td>
                                <?  echo create_drop_down( "cbo_company_name", 151, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name", "id,company_name", 0, "", '', "get_php_form_data( this.value, 'company_wise_report_button_setting','requires/comparative_statement_fabrics_controller');"); ?>
                            </td>                           
                            
                        </tr>
                        <tr>
                            <td>Style Specific CS</td>
                            <td>
                                <input type="checkbox" id="txt_style_check" name="txt_style_check" onclick="change_styleref_cs();"/>&nbsp;<input name="txt_style_no" id="txt_style_no" style="width:120px;" type="text" title="Double Click to Search" onDblClick="openmypage_style_ref_no()" class="text_boxes" placeholder="Browse Style No." disabled />
                    		    <input type="hidden" id="hidd_job_id" name="hidd_job_id" />
                            </td>
                            <td>Comments</td>
                            <td>
                                <input type="text" class="text_boxes" id="txt_comments" style="width:140px" >
                            </td>
                            <td>Ready To Approved</td>
                            <td>
                                <? echo create_drop_down( "cbo_ready_to_approved", 151, $yes_no,'',1,'-- Select --',0,""); ?>
                            </td>
                            <td align="center" colspan="2">
                                <input type="button" class="formbutton" id="generate_cs" value="Generate CS" onClick="fnc_generate_cs()" style="width:80px" >
                            </td>
                        </tr>
                        <tr>
                            <td align="left" height="10" colspan="3">
                            </td>
                            <td align="left" height="10" colspan="5">
                                <?
                                include("../../terms_condition/terms_condition.php");
                                terms_condition(512,'txt_system_id','../../');
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