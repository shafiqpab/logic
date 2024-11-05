<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create for Cash Incentive Submission V2 Entry
Functionality	:
JS Functions	:
Created by		:	Jahid
Creation date 	: 	21-06-2022
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
echo load_html_head_contents("Cash Incentive Submission V2 Entry", "../../",1,1, $unicode,'','');

?>
<script>
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
    var permission='<? echo $permission; ?>';
    function openmypage_sys_no()
    {
        if( form_validation('cbo_company_name','Company Name')==false )
        {
            return;
        }
        var cbo_company_name = $("#cbo_company_name").val();
        var page_link = 'requires/cash_incentive_submission_v2_controller.php?action=system_popup&cbo_company_name='+cbo_company_name;
        var title = 'Search Cash Incentive Submission Entry';

        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1120px,height=400px,center=1,resize=0,scrolling=0','../');
        emailwindow.onclose=function()
        {
			var theemail=this.contentDoc.getElementById("selected_id").value.split("_");
			var mst_id=theemail[0];
			var invoice_ids=theemail[1];
			
            if(mst_id!="")
            {
                freeze_window();
                get_php_form_data( mst_id+"**"+is_sc_lc, 'populate_data_from_search_popup','requires/cash_incentive_submission_v2_controller');
				var is_sc_lc=$("#is_lc_sc").val();
				//var invoice_id_arr=$("#submission_invoice_id").val();
				show_list_view(invoice_ids+"_"+is_sc_lc+"_"+mst_id, 'show_invoice_dtls_listview_update', 'dtls_list_view', 'requires/cash_incentive_submission_v2_controller', 'setFilterGrid("list_fabric_desc_container",-1)' ) ;
                $('#cbo_company_name').attr('disabled',true);
                //$('#txt_lc_sc_no').attr('disabled',true);
				//alert($("#special_submitted_chk").val());
				fn_total_amt(1,0);
				/*var special_submitted_chk=$("#special_submitted_chk").val();
				if(special_submitted_chk==1)
				{
					calculate_amount(1);
				}
				var euro_incentive_percent=$("#euro_incentive_percent").val()*1;
				//alert(euro_incentive_percent);
				if(euro_incentive_percent>0)
				{
					calculate_amount(2);
				}
				
				var general_incentive_percent=$("#general_incentive_percent").val()*1;
				if(general_incentive_percent>0)
				{
					calculate_amount(3);
				}
				
				var market_submitted_chk=$("#market_submitted_chk").val();
				if(market_submitted_chk==1)
				{
					calculate_amount(4);
				}*/
				
                set_button_status(1, permission, 'fnc_cash_incentive_submission',1);
            }
            release_freezing();
        }
    }
	
    function openmypage_realizationInfo()
    {
        if (form_validation('cbo_company_name','Company')==false )
        {
            return;
        }
        var beneficiary_name = $("#cbo_company_name").val();
		var update_id = $("#update_id").val();
		var submission_invoice_id = $("#submission_invoice_id").val();
        var page_link='requires/cash_incentive_submission_v2_controller.php?action=proceed_realization_popup_search&beneficiary_name='+beneficiary_name+'&update_id='+update_id+'&submission_invoice_id='+submission_invoice_id;
        var title='Export Proceeds Realization Entry Form';
        
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1150px,height=420px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var realization_id=this.contentDoc.getElementById("hidden_realization_id").value;
            var is_lc_sc=this.contentDoc.getElementById("hidden_is_lc").value;
            var invoice_id_all=this.contentDoc.getElementById("hidden_invoice_id").value;
            var lc_sc_id=this.contentDoc.getElementById("hidden_lc_sc_id").value;
            var internal_file_no=this.contentDoc.getElementById("hidden_inter_file_no").value;
			var file_no_string=this.contentDoc.getElementById("file_no_string").value;
			
			//alert(file_no_string);
			
            var realization_id_arr = $.unique(realization_id.split(','));
			var invoice_id_arr = $.unique(invoice_id_all.split(','));
            var internal_file_no_arr = $.unique(internal_file_no.split(','));
            var sc_lc_id = $.unique(lc_sc_id.split(','));
            var is_lc_sc_arr = $.unique(is_lc_sc.split(','));
			//alert(invoice_id_arr);return;
            //if(is_lc_sc_arr.length>1){
                //alert('LS/SC Can Not Mixed');
                //return;
            //}

            if(trim(realization_id)!="")
            {
                freeze_window(5);
                get_php_form_data(realization_id_arr, "populate_data_from_invoice_bill", "requires/cash_incentive_submission_v2_controller" );
				var update_id=$("#update_id").val();	
				show_list_view(invoice_id_arr+"_"+is_lc_sc_arr+"_"+update_id, 'show_invoice_dtls_listview', 'dtls_list_view', 'requires/cash_incentive_submission_v2_controller', 'setFilterGrid("list_fabric_desc_container",-1)' ) ;
                $("#realization_id").val(realization_id_arr);			
                $("#submission_invoice_id").val(invoice_id_arr);			
                //$("#is_lc_sc").val(is_lc_sc_arr);			
                $("#lc_sc_id").val(sc_lc_id);			
                $("#txt_file_no").val(internal_file_no_arr);	
				//$("#txt_file_no_string").val(file_no_string);
                
				fn_total_amt(1,0);
				var update_id = $("#update_id").val();
				if(update_id!="")
				{
					$("#special_submitted_chk").val("0");
					$("#special_submitted_chk").prop('checked', false); 
					$("#market_submitted_chk").val("0");
					$("#market_submitted_chk").prop('checked', false);
					$("#euro_incentive_percent").val("");
					$("#general_incentive_percent").val("");
					
					$("#txt_total_special_incentive").val("");
					$("#txt_total_euro_incentive").val("");
					$("#txt_total_general_incentive").val("");
					$("#txt_total_market_incentive").val("");
				}
				
				release_freezing();
            }
                        
        }
    }
    function numberPopup(id)
    {
        if( form_validation('cbo_company_name','Company Name')==false )
        {
            return;
        }
        var cbo_company_name = $("#cbo_company_name").val();
        var page_link = 'requires/cash_incentive_submission_v2_controller.php?action=btb_lc_popup&cbo_company_name='+cbo_company_name;
        var title = 'Search LC/SC Details';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=400px,center=1,resize=0,scrolling=0','../')
        emailwindow.onclose=function()
        {
			var theemail=this.contentDoc.getElementById("selected_id").value;
            if(theemail!="")
            {
                var data=theemail;
                get_php_form_data( data, 'load_btb_lc_info','requires/cash_incentive_submission_v2_controller');
                // set_all_onclick();
            }
            release_freezing();
        }
    }

    

    function fnc_cash_incentive_submission(operation) 
    {
        if (form_validation('cbo_company_name*cbo_bank_name*txt_submission_date*txt_lc_sc_no','Company Name*Bank Name*Submission Date*LS/SC')==false)
        {
            return;
        }
		
		var j=0; var i=1; var dataString='';
		//.not(':first')
		$("#tbl_lc_details").find('tbody tr').each(function()
		{
			var buyer=$('#buyer_'+i).attr('title');
			var lcSc=$('#lcSc_'+i).attr('title');
			var billNo_=$('#billNo_'+i).attr('title');
			var expNo=$('#expNo_'+i).attr('title');
			var invoiceNo=$('#invoiceNo_'+i).attr('title');
			var invoiceQnty=$('#invoiceQnty_'+i).attr('title');
			var invoiceValue=$('#invoiceValue_'+i).attr('title');
			var rlzDate=$('#rlzDate_'+i).attr('title');
			
			
			var txtRlzValue=$('#txtRlzValue_'+i).val();
			var txtspecialIncentive=$('#txtspecialIncentive_'+i).val();
			var txteuroIncentive=$('#txteuroIncentive_'+i).val();
			var txtgeneralIncentive=$('#txtgeneralIncentive_'+i).val();
			var txtmarketIncentive=$('#txtmarketIncentive_'+i).val();
			
			
			var txtRlzId=$('#txtRlzId_'+i).val();
			var txtInoiceId=$('#txtInoiceId_'+i).val();
			var txtLcScId=$('#txtLcScId_'+i).val();
			var txtIsLcSc=$('#txtIsLcSc_'+i).val();
			var updateDtlsId=$('#updateDtlsId_'+i).val();
			
			//alert(productCode);
			
			if(txtRlzValue>0 && (txtspecialIncentive>0 || txteuroIncentive >0 || txtgeneralIncentive  >0 || txtmarketIncentive  >0))	
			{
				j++;
				dataString+='&buyer' + j + '=' + buyer + '&lcSc' + j + '=' + lcSc + '&billNo_' + j + '=' + billNo_ + '&expNo' + j + '=' + expNo + '&invoiceNo' + j + '=' + invoiceNo+ '&invoiceQnty' + j + '=' + invoiceQnty + '&invoiceValue' + j + '=' + invoiceValue + '&rlzDate' + j + '=' + rlzDate + '&txtRlzValue' + j + '=' + txtRlzValue + '&txtspecialIncentive' + j + '=' + txtspecialIncentive + '&txteuroIncentive' + j + '=' + txteuroIncentive + '&txtgeneralIncentive' + j + '=' + txtgeneralIncentive  + '&txtmarketIncentive' + j + '=' + txtmarketIncentive+ '&txtRlzId' + j + '=' + txtRlzId + '&txtInoiceId' + j + '=' + txtInoiceId+ '&txtLcScId' + j + '=' + txtLcScId + '&txtIsLcSc' + j + '=' + txtIsLcSc + '&updateDtlsId' + j + '=' + updateDtlsId;
			
			}
			
			i++;
		});
		
		if(j<1)
		{
			alert('No data');return;
		}
		
        // *cbo_buyer_name *is_lc_sc
        var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_system_id*update_id*cbo_company_name*cbo_bank_name*txt_submission_date*txt_lc_sc_no*realization_id*submission_invoice_id*lc_sc_id*txt_file_no_string*txt_lc_value*txt_day_to_realize*txt_possible_reali_date*txt_file_no*txt_file_year*txt_bank_file_no*txt_invoice_value*txt_incective_bank_file*txt_net_realize_value*txt_net_weight*txt_yarn_qnty*txt_yarn_value*txt_yarn_rate*txt_over_head_charge*txt_total_value*txt_remarks*txt_certificate_amount*txt_sub_exchange_rate*txt_audit_exchange_rate*txt_loan*cbo_loan_value*txt_loan_given*special_submitted_chk*euro_incentive_percent*general_incentive_percent*market_submitted_chk*txt_total_special_incentive*txt_total_euro_incentive*txt_total_general_incentive*txt_total_market_incentive*txt_loan_no*txt_loan_date',"../../")+dataString;
        //alert(data);return;
        freeze_window(operation);
        http.open("POST","requires/cash_incentive_submission_v2_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_cash_incentive_submission_reponse;

    }

    function fnc_cash_incentive_submission_reponse()
    {
        if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
            if(reponse[0]==404)
			{
                alert(reponse[1]);
			}
 
			if(reponse[0]==0 || reponse[0]==1)
			{
				document.getElementById('txt_system_id').value=reponse[1];
				document.getElementById('update_id').value=reponse[2];
                $('#cbo_company_name').attr('disabled',true);
                //$('#txt_lc_sc_no').attr('disabled',true);
				var is_lc_sc=$("#is_lc_sc").val();
				var invoice_id_arr=$("#submission_invoice_id").val();
				show_list_view(invoice_id_arr+"_"+is_lc_sc+"_"+reponse[2], 'show_invoice_dtls_listview_update', 'dtls_list_view', 'requires/cash_incentive_submission_v2_controller', 'setFilterGrid("list_fabric_desc_container",-1)' ) ;

				set_button_status(1, permission, 'fnc_cash_incentive_submission',1);
			}
			if(reponse[0]==2)
			{
                form_reset_cise();
				reset_form('cashincentivesubmission_1','','','','','');
                set_button_status(0, permission, 'fnc_cash_incentive_submission',1);
			}
			show_msg(reponse[0]);
			release_freezing();
		}
    }

    function form_reset_cise() {
        $('#cbo_company_name').removeAttr('disabled');
        $('#txt_lc_sc_no').removeAttr('disabled');
		var list_view_wo =trim(return_global_ajax_value( 0, 'submission_details', '', 'requires/cash_incentive_submission_v2_controller'));
		$('#dtls_list_view').html('');
		$('#dtls_list_view').html(list_view_wo);
		$('txt_total_rlz_value').val('');
		$('txt_total_special_incentive').val('');
		$('txt_total_euro_incentive').val('');
		$('txt_total_general_incentive').val('');
		$('txt_total_market_incentive').val('');
		$('total_invoice_qnty').text("");
		$('total_invoice_value').text("");
    }
	
	function set_ini_dtls()
	{
		var cbo_company_id = $('#cbo_company_name').val();
		
		$('#realization_id').val('');
		$('#submission_invoice_id').val('');	
		$('#is_lc_sc').val('');
		$('#lc_sc_id').val('');	
		$('#txt_file_no_string').val('');
		
		var list_view_wo =trim(return_global_ajax_value( cbo_company_id, 'submission_details', '', 'requires/cash_incentive_submission_v2_controller'));
		$('#dtls_list_view').html('');
		$('#dtls_list_view').html(list_view_wo);
	}
	
	function fn_total_amt(str,row_id)
	{
		var numRow = $('table#tbl_lc_details tbody tr').length;
		if(str==1)
		{
			
			var invoice_qnty=0; var invoice_value=0; var realized_value=0;
			for(var i=1; i<=numRow; i++)
			{
				 invoice_qnty+=$("#invoiceQnty_"+i).attr("title")*1;
				 invoice_value+=$("#invoiceValue_"+i).attr("title")*1;
				 realized_value+=$("#txtRlzValue_"+i).val()*1;
			}
			//alert(invoice_qnty+"="+invoice_value);
			$("#total_invoice_qnty").html("<b>"+invoice_qnty.toFixed(2)+"</b>");
			$("#total_invoice_qnty").attr("title",invoice_qnty);
			$("#total_invoice_value").html("<b>"+invoice_value.toFixed(4)+"</b>");
			$("#total_invoice_value").attr("title",invoice_value);
			$("#txt_total_rlz_value").val(realized_value.toFixed(4));
		}
		else
		{
			var net_inv_value=$("#invoiceValue_"+row_id).attr("title")*1;
			var rlz_value=$("#txtRlzValue_"+row_id).val()*1;
			//alert(net_inv_value+"="+rlz_value);
			if(rlz_value>net_inv_value)
			{
				alert("Realize value can't exceed to invoice value");
				$("#txtRlzValue_"+row_id).val("");
				var ddd={ dec_type:2, comma:0, currency:''}
				math_operation( "txt_total_rlz_value", "txtRlzValue_", "+", numRow,ddd );
				return;
			}
			else
			{
				var ddd={ dec_type:2, comma:0, currency:''}
				math_operation( "txt_total_rlz_value", "txtRlzValue_", "+", numRow,ddd );
			}
		}
		
	}
	
	function calculate_amount(id){
        
        if(id==1)
		{
            var special_submitted_chk = $("#special_submitted_chk").val();
			var numRow = $('table#tbl_lc_details tbody tr').length;
            if($("#special_submitted_chk").is(':checked'))
			{
                $("#special_submitted_chk").val(1);
				var net_realize_value = ""; var total_sp_incentive=0;
				for(var i=1; i<=numRow; i++)
				{
					if($("#txtspecialIncentive_"+i).attr("title")*1<=0)
					{
						net_realize_value = $("#txtRlzValue_"+i).val()*1;
						if(net_realize_value>0)
						{
							 amount= net_realize_value*1*.01;
							 $("#txtspecialIncentive_"+i).val(amount);
							 total_sp_incentive+=amount;
						}
					}
				}
				$("#txt_total_special_incentive").val(total_sp_incentive);
            }
			else
			{
                $("#special_submitted_chk").val(0);
                for(var i=1; i<=numRow; i++)
				{
					$("#txtspecialIncentive_"+i).val("");
				}
				$("#txt_total_special_incentive").val("");
            }
        }
        if(id==2)
		{
            var euro_incentive_percent = (($("#euro_incentive_percent").val()*1)/100);
			var txt_total_value = $("#txt_total_value").val()*1;
			var numRow = $('table#tbl_lc_details tbody tr').length;
            if(euro_incentive_percent>0 && txt_total_value>0)
			{
				var total_invoice_value = $("#total_invoice_value").attr("title")*1;
				var invoice_val = ""; var invoice_percent=0; var total_euro_incentive=0;
				for(var i=1; i<=numRow; i++)
				{
					if($("#txteuroIncentive_"+i).attr("title")*1<=0)
					{
						invoice_val = $("#invoiceValue_"+i).attr("title")*1;
						//alert(invoice_val+"="+total_invoice_value+"="+txt_total_value);//return;
						invoice_percent=((invoice_val/total_invoice_value)*txt_total_value);
						if(invoice_val>0)
						{
							//alert(invoice_percent+"="+euro_incentive_percent)
							amount= invoice_percent*euro_incentive_percent;
							$("#txteuroIncentive_"+i).val(amount.toFixed(4));
							total_euro_incentive+=amount;
						}
					}
				}
				$("#txt_total_euro_incentive").val(total_euro_incentive.toFixed(2));
            }
			else
			{
                $("#euro_incentive_percent").val("");
                for(var i=1; i<=numRow; i++)
				{
					$("#txteuroIncentive_"+i).val("");
				}
				$("#txt_total_euro_incentive").val("");
            }
         }
		if(id==3)
		{
			var euro_incentive_percent = (($("#general_incentive_percent").val()*1)/100);
			var txt_total_value = $("#txt_total_value").val()*1;
			var numRow = $('table#tbl_lc_details tbody tr').length;
            if(euro_incentive_percent>0 && txt_total_value>0)
			{
				var total_invoice_value = $("#total_invoice_value").attr("title")*1;
				var invoice_val = ""; var invoice_percent=0; var total_euro_incentive=0;
				for(var i=1; i<=numRow; i++)
				{
					if($("#txtgeneralIncentive_"+i).attr("title")*1<=0)
					{
						invoice_val = $("#invoiceValue_"+i).attr("title")*1;
						//alert(invoice_val+"="+total_invoice_value+"="+txt_total_value);return;
						invoice_percent=((invoice_val/total_invoice_value)*txt_total_value);
						if(invoice_val>0)
						{
							//alert(invoice_percent+"="+euro_incentive_percent)
							amount= invoice_percent*euro_incentive_percent;
							$("#txtgeneralIncentive_"+i).val(amount.toFixed(4));
							total_euro_incentive+=amount;
						}
					}
				}
				$("#txt_total_general_incentive").val(total_euro_incentive.toFixed(2));
            }
			else
			{
                $("#general_incentive_percent").val("");
                for(var i=1; i<=numRow; i++)
				{
					$("#txtgeneralIncentive_"+i).val("");
				}
				$("#txt_total_general_incentive").val("");
            }
        }
        if(id==4)
		{
			var market_submitted_chk = $("#market_submitted_chk").val();
			var numRow = $('table#tbl_lc_details tbody tr').length;
            if($("#market_submitted_chk").is(':checked'))
			{
				
                $("#market_submitted_chk").val(1);
				var net_realize_value = "";var total_market_incentive=0;
				for(var i=1; i<=numRow; i++)
				{
					if($("#txtmarketIncentive_"+i).attr("title")*1<=0)
					{
						net_realize_value = $("#txtRlzValue_"+i).val()*1;
						if(net_realize_value>0)
						{
							 amount= net_realize_value*1*.04;
							 $("#txtmarketIncentive_"+i).val(amount);
							 total_market_incentive+=amount;
						}
					}
				}
				$("#txt_total_market_incentive").val(total_market_incentive);
            }
			else
			{
                $("#market_submitted_chk").val(0);
                for(var i=1; i<=numRow; i++)
				{
					$("#txtmarketIncentive_"+i).val("");
				}
				$("#txt_total_market_incentive").val("");
            }
        }
    }
		
	function tot_yarn_val()
	{
		var txt_yarn_qnty=$("#txt_yarn_qnty").val()*1;
		var txt_yarn_value=$("#txt_yarn_value").val()*1;
		var txt_over_head_charge=$("#txt_over_head_charge").val()*1;
		var tot_yarn_val=txt_yarn_value+txt_over_head_charge;
		var yarn_rate=txt_yarn_value/txt_yarn_qnty;
		$("#txt_yarn_rate").val(yarn_rate);
		$("#txt_total_value").val(tot_yarn_val);
	}
	
	function calculation(id)
	{
		var txt_sub_exchange_rate=document.getElementById('txt_sub_exchange_rate').value;
		var txt_loan=document.getElementById('txt_loan').value;
		//var cbo_loan_value=document.getElementById('cbo_loan_value');
		var cbo_loan_value=$("#cbo_loan_value").val();
		var txt_total_market_incentive=document.getElementById('txt_total_market_incentive').value;
		var txt_certificate_amount=document.getElementById('txt_certificate_amount').value;
		var txt_total_special_incentive=document.getElementById('txt_total_special_incentive').value;
		var txt_total_general_incentive=document.getElementById('txt_total_general_incentive').value;
		var txt_total_euro_incentive=document.getElementById('txt_total_euro_incentive').value;
		if(cbo_loan_value==1)
		{

		if(isNaN(txt_total_market_incentive) || txt_total_market_incentive === "" || txt_total_market_incentive === null) {
			txt_total_market_incentive = 0;
		}

		if(isNaN(txt_total_special_incentive) || txt_total_special_incentive === "" || txt_total_special_incentive === null) {
			txt_total_special_incentive = 0;
		}

		if(isNaN(txt_total_general_incentive) || txt_total_general_incentive === "" || txt_total_general_incentive === null) {
			txt_total_general_incentive = 0;
		}

		if(isNaN(txt_total_euro_incentive) || txt_total_euro_incentive === "" || txt_total_euro_incentive === null) {
			txt_total_euro_incentive = 0;
		}
		var total_val = parseFloat(txt_total_market_incentive) + parseFloat(txt_total_special_incentive) + parseFloat(txt_total_general_incentive) + parseFloat(txt_total_euro_incentive);

        //alert(total_val);
		  var total=(parseFloat(total_val)*parseFloat(txt_sub_exchange_rate)*parseFloat(txt_loan))/100;
				if(!isNaN(total))
				{
					document.getElementById('txt_loan_given').value=total;
				}
			
	    }
		else if(cbo_loan_value==2)
		{
			var total=(parseFloat(txt_certificate_amount)*parseFloat(txt_loan))/100;
			if(!isNaN(total))
			{
				document.getElementById('txt_loan_given').value=total;
			}
	    }
		
	}

        
		
	

    function fnc_refreshment(){
		$("#txt_net_weight").val("");
		$("#special_submitted_chk").val(0);
		$("#special_submitted_chk").attr('checked',false);
		$("#euro_incentive_percent").val("");
		$("#general_incentive_percent").val("");
        $("#market_submitted_chk").val(0);
		$("#market_submitted_chk").attr('checked',false);
		$("#txt_total_special_incentive").val("");
		$("#txt_total_euro_incentive").val("");
		$("#txt_total_general_incentive").val("");
		$("#txt_total_market_incentive").val("");
		$("#txt_yarn_qnty").val("");
		$("#txt_yarn_value").val("");
		$("#txt_yarn_rate").val("");
		$("#txt_over_head_charge").val("");
		$("#txt_total_value").val("");
		$("#txt_remarks").val("");
		$("#txt_system_id").val("");
		$("#update_id").val("");
		$("#txt_certificate_amount").val("");
		$("#txt_sub_exchange_rate").val("");
		$("#txt_audit_exchange_rate").val("");
		$("#txt_loan_given").val("");
		$("#cbo_loan_value").val("");
		$("#txt_loan").val("");
		$("#txt_loan_no").val("");
		$("#txt_loan_date").val("");
		

        var i=1;
        $("#tbl_lc_details").find('tbody tr').each(function() 
		{
			
			$('#txtspecialIncentive_'+i).val("");
			$('#txteuroIncentive_'+i).val("");
			$('#txtgeneralIncentive_'+i).val("");
			$('#txtmarketIncentive_'+i).val("");
		
            i++	;				
		});
        set_button_status(0, permission, 'fnc_cash_incentive_submission',1);
    }

    function fnc_print(type)
	{

		if($('#txt_system_id').val()=="")
		{
			alert("Please Save First.");
			return;
		}
		else
		{
			if(type==1)
			{
				print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val(), "print_generate", "requires/cash_incentive_submission_v2_controller");
			}
			if(type==2)
			{
				print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val(), "print_generate2", "requires/cash_incentive_submission_v2_controller");
			}
		}
	}
	
	function realy_days(field_id)
	{   
	   var from_date = $('#txt_submission_date').val();
	   var days_to_realize = $('#txt_day_to_realize').val();
	   var to_date = $('#txt_possible_reali_date').val();	  
	   days_to_realize = days_to_realize*1-1;
	   
	   if(days_to_realize=="" || days_to_realize*1==-1) return;
	   if(from_date !="" )
	   {		
			if( (field_id == 'txt_day_to_realize' || field_id == 'txt_bank_ref_date') && days_to_realize!="")
			{ 
				var res_date = add_days( from_date, days_to_realize );
				$('#txt_possible_reali_date').val(res_date);
			}
			else if(field_id == 'txt_possible_reali_date' &&  to_date!="")
			{ 				 
				var datediff = date_diff( 'd', from_date, to_date )+1;					 
				$('#txt_day_to_realize').val(datediff);
			}
			
	   }               
	}

</script>
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission); ?>
        <form name="cashincentivesubmission_1" id="cashincentivesubmission_1" autocomplete="off">
            <fieldset style="width:1350px;">
                <legend>Cash Incentive Submission Entry</legend>
                <table width="1350" border="0" cellpadding="0" cellspacing="2" id="tbl_btb">
                    <tr>
                        <td colspan="5" align="right"><strong>Submission ID</strong></td> 
                        <td colspan="5">
                            <input style="width:140px;" type="text" title="Double Click to Search" onDblClick="openmypage_sys_no()" class="text_boxes" placeholder="Browse" name="txt_system_id" id="txt_system_id" readonly />
                            <input type="hidden" id="update_id">
                        </td>
                    <tr>
                        <td width="100" class="must_entry_caption">Company Name </td>
                        <td width="150">
                             <? echo create_drop_down( "cbo_company_name", 145, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $cbo_country_id, "set_ini_dtls()");?>
                        </td>
                        <td width="100" class="must_entry_caption">Bank Name</td>
                        <td width="150" >
                            <?
							echo create_drop_down("cbo_bank_name", 145, "select bank_name,id from lib_bank where is_deleted=0 and status_active=1 and (LIEN_BANK=1 or ISSUSING_BANK=1 or ADVISING_BANK=1) order by bank_name", "id,bank_name", 1, "-- Select Bank --", 0, "");
							?>
                        </td>
                        <td width="100" class="must_entry_caption">Submission Date</td>
                        <td width="150">
                            <input style="width:130px " name="txt_submission_date" id="txt_submission_date" class="datepicker" readonly/>
                        </td>
                        <td width="100" class="must_entry_caption">LC/SC No</td>
                        <td width="150">
                            <input style="width:130px;" type="text" title="Double Click to Search" onDblClick="openmypage_realizationInfo()" class="text_boxes" placeholder="Browse" name="txt_lc_sc_no" id="txt_lc_sc_no" readonly />
                            <input type="hidden" id="realization_id">
                            <input type="hidden" id="submission_invoice_id">
                            <input type="hidden" id="is_lc_sc">
                            <input type="hidden" id="lc_sc_id">
                            <input type="hidden" id="txt_file_no_string">
                            
                        </td>
                        <td width="100">Export L/c Value</td>
                        <td><input type="text" name="txt_lc_value" id="txt_lc_value" style="width:130px" class="text_boxes" placeholder="Display" readonly></td>
                    </tr>
                    <tr>
                    	<td>Days to Realize </td>
                        <td>
                            <input type="text" name="txt_day_to_realize" id="txt_day_to_realize" class="text_boxes_numeric" style="width:130px" onChange="realy_days(this.id)" />	
                        </td>
                        <td>Possible Reali Date</td>
                        <td>
                        	<input type="text" name="txt_possible_reali_date" id= "txt_possible_reali_date" class="datepicker" onChange="realy_days(this.id)" style="width:130px" />
                        </td>
                        <td>Internal File No</td>
                        <td>
                            <input style="width:130px;" type="text" class="text_boxes" name="txt_file_no" id="txt_file_no" placeholder="Display" readonly />
                        </td>
                        <td >File Year</td>
                        <td>
                            <input type="text" name="txt_file_year" id="txt_file_year" style="width:130px" class="text_boxes" placeholder="Display" readonly>
                        </td>
                        <td>Bank File No</td>
                        <td>
                            <input type="text" name="txt_bank_file_no" id="txt_bank_file_no" style="width:130px" class="text_boxes" placeholder="Display" readonly>
                        </td>
                    </tr>
                    <tr>
                        <td >Invoice Value</td>
                        <td>
                            <input type="text" name="txt_invoice_value" id="txt_invoice_value" style="width:130px" class="text_boxes_numeric" placeholder="Display" readonly>
                        </td>
                        <td>Incentive Bank File</td>
                        <td>
                            <input type="text" name="txt_incective_bank_file" id="txt_incective_bank_file" style="width:130px" class="text_boxes">
                        </td>
                        <td>Net Realize Value</td>
                        <td>
                            <input type="text" name="txt_net_realize_value" id="txt_net_realize_value" style="width:130px" class="text_boxes_numeric" placeholder="Display" readonly >
                        </td>
                        <td>Total Net Weight</td>
                        <td>
                            <input type="text" name="txt_net_weight" id="txt_net_weight" style="width:130px" class="text_boxes_numeric"  >
                        </td>
                        <td>Total Yarn/Kg</td>
                        <td>
                            <input style="width:130px;" type="text" class="text_boxes_numeric" name="txt_yarn_qnty" id="txt_yarn_qnty" onKeyUp="tot_yarn_val()"/>
                        </td>
                    </tr> 
                    <tr>
                    	<td >Yarn Value</td>
                        <td>
                            <input type="text" name="txt_yarn_value" id="txt_yarn_value" style="width:130px" class="text_boxes_numeric" onKeyUp="tot_yarn_val()" >
                        </td>
                        <td>Yarn Rate/Kg</td>
                        <td>
                            <input type="text" name="txt_yarn_rate" id="txt_yarn_rate" style="width:130px" class="text_boxes_numeric" placeholder="Display" readonly>
                        </td>
                        <td>Overhead Charges</td>
                        <td>
                            <input type="text" name="txt_over_head_charge" id="txt_over_head_charge" style="width:130px" class="text_boxes_numeric" onKeyUp="tot_yarn_val()" >
                        </td>
                        <td >Total Value</td>
                        <td>
                            <input type="text" name="txt_total_value" id="txt_total_value" style="width:130px" class="text_boxes_numeric" readonly>
                        </td>
                        <td>Audit/Certificate Amount 	BDT</td>
						<td>
						<input type="text" name="txt_certificate_amount" id="txt_certificate_amount" style="width:130px" class="text_boxes_numeric" onKeyUp="calculation()"/>
						</td>
                    </tr> 
					<tr>
					    <td>Sub. Exch. Rate</td>
						<td>
						<input type="text" name="txt_sub_exchange_rate" id="txt_sub_exchange_rate" style="width:130px" class="text_boxes_numeric"  onKeyUp="calculation()" />
						</td>
						<td>Audit Exch. Rate</td>
						<td>
						<input type="text" name="txt_audit_exchange_rate" id="txt_audit_exchange_rate" style="width:130px" class="text_boxes_numeric" />
						</td>
						<td>Loan based on</td>
						<td>
						<?
						$loan_based = array(1 => "Submission Value", 2 => "Certificate Value");
						echo create_drop_down("cbo_loan_value", 130, $loan_based, "", 1, "-- Select--", 0, "calculation()", "", "");
						?>
					   </td>
						<td>Loan %</td>
						<td>
						<input type="text" name="txt_loan" id="txt_loan" style="width:130px" class="text_boxes_numeric" onKeyUp="calculation()" />
						</td>
						<td>Loan Given Value BDT</td>
						<td>
						<input type="text" name="txt_loan_given" id="txt_loan_given" style="width:130px" class="text_boxes_numeric" readonly/>
						</td>
						
						
					</tr>
					<tr>
					<td>Loan No</td>
					<td>
					<input type="text" name="txt_loan_no" id="txt_loan_no" style="width:130px" class="text_boxes" />
					</td>
					<td >Loan Date</td>
					<td><input type="text" name="txt_loan_date" id="txt_loan_date" class="datepicker" style="width:150px" readonly ></td>
					<td>Remarks</td>
						<td>
						<input type="text" name="txt_remarks" id="txt_remarks" style="width:130px" class="text_boxes" />
						</td>
					</tr>
                </table>
                <div style="width:1250px;" align="left">
                    <table cellspacing="0" border="1" width="1250" class="rpt_table" id="tbl_lc_details" align="left" rules="all">
                        <thead>
                            <tr>
                                <th width="40">SL</th>
                                <th width="60">Buyer</th>
                                <th width="120">LC/SC No</th>
                                <th width="90">Bill No</th>
                                <th width="90">EXP No</th>
                                <th width="100">Invoice No</th>
                                <th width="80">Invoice Qty (Pcs)</th>
                                <th width="90">Net Invoice Value</th>
                                <th width="100">Realized Value</th>
                                <th width="70">Realized Date</th>
                                <th width="100">Submitted to Bank(Special Incentive 1%)<br><input type="checkbox" id="special_submitted_chk" name="special_submitted_chk" value="0" onClick="calculate_amount(1)"></th>
                                <th width="100">Euro Zone Incentive(Yarn)<br><input type="text" name="euro_incentive_percent" id="euro_incentive_percent" class="text_boxes_numeric" style="width:25px" onKeyUp="calculate_amount(2)" /></th>
                                <th width="100">General Incentive(Yarn)<br><input type="text" name="general_incentive_percent" id="general_incentive_percent" class="text_boxes_numeric" style="width:25px" onKeyUp="calculate_amount(3)" /></th>
                                <th>Submitted to Bank(New Market 4%)<br><input type="checkbox" id="market_submitted_chk" name="market_submitted_chk" value="0" onClick="calculate_amount(4)"></th>
                                
                            </tr>
                        </thead>
                        <tbody id="dtls_list_view">
                            <tr>
                                <td align="center"></td>
                                <td id="buyer_1"></td>
                                <td id="lcSc_1"></td>
                                <td id="billNo_1"></td>
                                <td id="expNo_1"></td>
                                <td id="invoiceNo_1"></td>
                                <td id="invoiceQnty_1" align="right"></td>
                                <td id="invoiceValue_1" align="right"></td>
                                <td id="rlzValue_1" align="center"><input type="text" name="txtRlzValue[]" id="txtRlzValue_1" class="text_boxes_numeric" style="width:80px" onKeyUp="fn_total_amt(0,1)" /></td>
                                <td id="rlzDate_1" align="center"></td>
                                <td id="specialIncentive_1" align="center"><input type="text" name="txtspecialIncentive[]" id="txtspecialIncentive_1" class="text_boxes_numeric" style="width:80px" readonly /></td>
                                <td id="eurolIncentive_1" align="center"><input type="text" name="txteuroIncentive[]" id="txteuroIncentive_1" class="text_boxes_numeric" style="width:80px" readonly /></td>
                                <td id="generalIncentive_1" align="center"><input type="text" name="txtgeneralIncentive[]" id="txtgeneralIncentive_1" class="text_boxes_numeric" style="width:80px" readonly /></td>
                                <td id="marketIncentive_1" align="center">
                                <input type="text" name="txtmarketIncentive[]" id="txtmarketIncentive_1" class="text_boxes_numeric" style="width:80px" readonly />
                                <input type="hidden" name="txtRlzId[]" id="txtRlzId_1" class="text_boxes" />
                                <input type="hidden" name="txtInoiceId[]" id="txtInoiceId_1" class="text_boxes" />
                                <input type="hidden" name="txtLcScId[]" id="txtLcScId_1" class="text_boxes" />
                                <input type="hidden" name="txtIsLcSc[]" id="txtIsLcSc_1" class="text_boxes" />
                                <input type="hidden" name="updateDtlsId[]" id="updateDtlsId_1" class="text_boxes" />
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                        	<tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td align="right">Total:</td>
                                <td id="total_invoice_qnty" align="right"></td>
                                <td id="total_invoice_value" align="right"></td>
                                <td align="center"><input type="text" name="txt_total_rlz_value" id="txt_total_rlz_value" class="text_boxes_numeric" style="width:80px" /></td>
                                <td></td>
                                <td align="center"><input type="text" name="txt_total_special_incentive" id="txt_total_special_incentive" class="text_boxes_numeric" style="width:80px"  onKeyUp="calculation()" /></td>
                                <td align="center"><input type="text" name="txt_total_euro_incentive" id="txt_total_euro_incentive" class="text_boxes_numeric" style="width:80px"  onKeyUp="calculation()" /></td>
                                <td align="center"><input type="text" name="txt_total_general_incentive" id="txt_total_general_incentive" class="text_boxes_numeric" style="width:80px"  onKeyUp="calculation()" /></td>
                                <td align="center">
                                <input type="text" name="txt_total_market_incentive" id="txt_total_market_incentive" class="text_boxes_numeric" style="width:80px"  onKeyUp="calculation()" />
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </fieldset>
            <table cellspacing="0" border="1" width="1250" class="rpt_table" align="left" rules="all">
                <tr>
                    <td colspan="6" height="50" valign="middle" align="center" class="button_container">
                     <? echo load_submit_buttons( $permission, "fnc_cash_incentive_submission", 0,0,"form_reset_cise();reset_form('cashincentivesubmission_1','dtls_list_view','','','','');",1); ?>
                     <input type="button"  value="Refresh 2" class="formbutton" style="width:100px;" onClick="fnc_refreshment();" >
                     <input type="button" name="Print" id="Print" value="Print" onClick="fnc_print(1)" style="width:100px" class="formbutton" />
					 <input type="button" name="Print2" id="Print2" value="Print2" onClick="fnc_print(2)" style="width:100px" class="formbutton" />
                    </td>
                    
                </tr>
            </table>
        </form>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>