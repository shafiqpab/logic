<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create for Export PI entry
					
Functionality	:	
				

JS Functions	:
 
Created by		:	Fuad Shahriar 
Creation date 	: 	02-03-2016
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
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Export Pro Forma Invoice", "../../", 1, 1,'','',''); 
?> 	

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  

var permission='<? echo $permission; ?>';
 <?
 if ($_SESSION['logic_erp']['data_arr'][152]){
	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][152] );
    echo "var field_level_data= ". $data_arr . ";\n";
 }

if($_SESSION['logic_erp']['mandatory_field'][152]!=""){

	$mandatory_field_arr= json_encode( $_SESSION['logic_erp']['mandatory_field'][152] );

	echo "var mandatory_field_arr= ". $mandatory_field_arr . ";\n";
}
    
 ?>


function show_print_report()
{
	var cbo_item_category_id=$('#cbo_item_category_id').val();
	if(cbo_item_category_id!=10)
	{
		alert("Print for Knit Fabric");
		return;
	}
	if($('#update_id').val()=="")
	{
		alert("Please Save Data First.");
		return;
	}
	else
	{
		print_report( $('#cbo_exporter_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val(), "print_new", "requires/export_pi_controller" ) 
		return;
	}
}

function show_print_report_3()
{
	var cbo_item_category_id=$('#cbo_item_category_id').val();
	if(cbo_item_category_id!=37)
	{
		alert("Print for Gmts Washing");
		return;
	}
	if($('#update_id').val()=="")
	{
		alert("Please Save Data First.");
		$("#cbo_item_category_id").focus();
		return;
	}
	else
	{
		print_report( $('#cbo_exporter_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val(), "print_new_rpt_3", "requires/export_pi_controller" ) 
		return;
	}
}

	function btn_print_Grmnt_Washing_2()
	{
		var cbo_item_category_id=$('#cbo_item_category_id').val();
		if(cbo_item_category_id!=37)
		{
			alert("Print for Gmts Washing");
			return;
		}
		if($('#update_id').val()=="")
		{
			alert("Please Save Data First.");
			$("#cbo_item_category_id").focus();
			return;
		}
		else  
		{
			print_report( $('#cbo_exporter_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_item_category_id').val()+'*'+$('#cbo_advising_bank').val(), "print_new_rpt_Grmnt_Washing_2", "requires/export_pi_controller" ) 
			return;
		}
	}

function show_print_report_4()
{
	var cbo_item_category_id=$('#cbo_item_category_id').val();
	if(cbo_item_category_id!=45)
	{
		alert("Print for Accessories");
		return;
	}
	if($('#update_id').val()=='')
	{
		alert("Please Save Data First.");
		$("#cbo_item_category_id").focus();
		return;
	}
	else
	{
		print_report( $('#cbo_exporter_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val(), "print_new_rpt_4", "requires/export_pi_controller" ) 
		return;
	}
}

function show_print_report_16()
{
	var cbo_item_category_id=$('#cbo_item_category_id').val();
	if(cbo_item_category_id!=2)
	{
		alert("Print for Woven Garments");
		return;
	}
	if($('#update_id').val()=='')
	{
		alert("Please Save Data First.");
		$("#cbo_item_category_id").focus();
		return;
	}
	else
	{
		print_report( $('#cbo_exporter_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val(), "print_new_rpt_woven", "requires/export_pi_controller" ) 
		return;
	}
}


function show_print_report_5()
{
	var cbo_item_category_id=$('#cbo_item_category_id').val();
	if(cbo_item_category_id!=36)
	{
		alert("Print for Gmts Embroidery");
		return;
	}
	if($('#update_id').val()=="")
	{
		alert("Please Save Data First.");
		$("#cbo_item_category_id").focus();
		return;
	}
	else
	{
		print_report( $('#cbo_exporter_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val(), "print_new_rpt_5", "requires/export_pi_controller" ) 
		return;
	}
}
function show_print_report_6()
{
	var cbo_item_category_id=$('#cbo_item_category_id').val();
	if(cbo_item_category_id!=45)
	{
		alert("Print for Accessories");
		return;
	}
	if($('#update_id').val()=="")
	{
		alert("Please Save Data First.");
		$("#cbo_item_category_id").focus();
		return;
	}
	else
	{
		print_report( $('#cbo_exporter_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val(), "print_new_rpt_6", "requires/export_pi_controller" ) 
		return;
	}
}

function show_print_report_7()
{
	var cbo_item_category_id=$('#cbo_item_category_id').val();
	if(cbo_item_category_id!=37)
	{
		alert("Print for Gmts Washing");
		return;
	}
	if($('#update_id').val()=="")
	{
		alert("Please Save Data First.");
		$("#cbo_item_category_id").focus();
		return;
	}
	else
	{
		// print_report( $('#cbo_exporter_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val(), "print_new_rpt_7", "requires/export_pi_controller" ) 
		$report_title=$( "div.form_caption" ).html();
        freeze_window();
		var data="action=print_new_rpt_7"+'&cbo_exporter_id='+$('#cbo_exporter_id').val()+'&update_id='+$('#update_id').val()+'&txt_system_id='+$('#txt_system_id').val();
		http.open("POST","requires/export_pi_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = show_print_report_7_reponse;

	}
}

function show_print_report_7_reponse(){
	if(http.readyState == 4){
        release_freezing();
		var file_data=http.responseText.split("****");
		$('#data_panel').html(file_data[1]);
		$('#print_report_Excel').removeAttr('href').attr('href','requires/'+trim(file_data[0]));
		document.getElementById('print_report_Excel').click();

		var w = window.open("Surprise", "_blank");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/prt.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
		d.close();
	}
}

function show_print_report_8()
{
	var cbo_item_category_id=$('#cbo_item_category_id').val();
	if(cbo_item_category_id!=10)
	{
		alert("Print for Knit Fabric");
		return;
	}
	if($('#update_id').val()=="")
	{
		alert("Please Save Data First.");
		return;
	}
	else
	{
		print_report( $('#cbo_exporter_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val(), "print_new_rpt_8", "requires/export_pi_controller" ) 
		return;
	}
}

function show_print_report_9()
{
	var cbo_item_category_id=$('#cbo_item_category_id').val();
	if(cbo_item_category_id!=10)
	{
		alert("Print for Knit Fabric");
		return;
	}
	if($('#update_id').val()=="")
	{
		alert("Please Save Data First.");
		return;
	}

	print_report( $('#cbo_exporter_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val(), "print_new_rpt_9", "requires/export_pi_controller" ) 
	return;

}

function show_print_report_10()
{
	var cbo_item_category_id=$('#cbo_item_category_id').val();
	if(cbo_item_category_id!=20 && cbo_item_category_id!=22)
	{
		alert("Print for Kniting, Dyeing and Finishing");
		return;
	}
	if($('#update_id').val()=="")
	{
		alert("Please Save Data First.");
		return;
	}

	print_report( $('#cbo_exporter_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_item_category_id').val(), "print_new_rpt_10", "requires/export_pi_controller" );
	return;

}

function show_print_report_11()
{
	var cbo_item_category_id=$('#cbo_item_category_id').val();
	if(cbo_item_category_id!=45)
	{
		alert("Print for Accessories");
		return;
	}
	if($('#update_id').val()=="")
	{
		alert("Please Save Data First.");
		return;
	}

	print_report( $('#cbo_exporter_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_item_category_id').val(), "print_new_rpt_11", "requires/export_pi_controller" );
	return;

}

function show_print_report_12()
{
    var cbo_item_category_id=$('#cbo_item_category_id').val();
    if(cbo_item_category_id!=23)
    {
        alert("Print for All Over Printing");
        return;
    }
    if($('#update_id').val()=="")
    {
        alert("Please Save Data First.");
        return;
    }

    print_report( $('#cbo_exporter_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_item_category_id').val(), "print_new_rpt_12", "requires/export_pi_controller" );
    return;

}

function show_print_report_13()
{
	var cbo_item_category_id=$('#cbo_item_category_id').val();
	if(cbo_item_category_id!=45)
	{
		alert("Print for Accessories");
		return;
	}
	if($('#update_id').val()=="")
	{
		alert("Please Save Data First.");
		return;
	}

	print_report( $('#cbo_exporter_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_item_category_id').val(), "print_new_rpt_13", "requires/export_pi_controller" );
	return;

}
function show_print_report_14()
{
    var cbo_item_category_id=$('#cbo_item_category_id').val();
    if(cbo_item_category_id!=45)
    {
        alert("Print for Accessories");
        return;
    }
    if($('#update_id').val()=="")
    {
        alert("Please Save Data First.");
        return;
    }

    print_report( $('#cbo_exporter_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_item_category_id').val(), "print_new_rpt_14", "requires/export_pi_controller" );
    return;

}

function show_print_report_15()
{
    var cbo_item_category_id=$('#cbo_item_category_id').val();
    if(cbo_item_category_id!=68 && cbo_item_category_id!=69)
    {
        alert("Print for YD");
        return;
    }
    if($('#update_id').val()=="")
    {
        alert("Please Save Data First.");
        return;
    }

    print_report( $('#cbo_exporter_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_item_category_id').val(), "print_new_rpt_15", "requires/export_pi_controller" );
    return;

}

function fnc_pi_mst( operation )
{
	if(operation==4)
	{ 
		var cbo_item_category_id=$('#cbo_item_category_id').val();
		if(cbo_item_category_id!=10)
		{
			alert("Print for Knit Fabric");
			return;
		}
		 print_report( $('#cbo_exporter_id').val()+'*'+$('#update_id').val(), "print", "requires/export_pi_controller" ) ;
		 return;
	}
	
	if(operation==2)
	{ 
		 //show_msg('13');
		 //return;
	}

	var variable_setting= document.getElementById('hidden_variable_setting').value;

	if (variable_setting == 1)
	{
		if (form_validation('cbo_item_category_id*cbo_exporter_id*cbo_buyer_name*pi_date*cbo_currency_id','Export Item Category*Exporter*Buyer Name*Pi Date*Currency*pi_revised_date')==false)
		{
			return;
		}
	}
	else
	{
		if (form_validation('cbo_item_category_id*cbo_exporter_id*cbo_buyer_name*pi_number*pi_date*cbo_currency_id','Export Item Category*Exporter*Buyer Name*Pi Number*PI Number*Pi Date*Currency*pi_revised_date')==false)
		{
			return;
		}
	}	

	if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][152]); ?>') 
	{
		if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][152]); ?>','<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][152]); ?>')==false) {return;}
	}



	var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_item_category_id*cbo_exporter_id*cbo_within_group*cbo_buyer_name*pi_number*pi_date*last_shipment_date*pi_validity_date*cbo_currency_id*hs_code*txt_swift*txt_internal_file_no*txt_remarks*txt_attention*cbo_advising_bank*update_id*hidden_variable_setting*cbo_approved*pi_revised_date*cbo_pi_revise*cbo_pay_term*txt_tenor*cbo_status*txt_advance_job*txt_yd_job_id*txt_weight_approx*txt_issuing_bank',"../../");
	//alert(data);return;	  
	freeze_window(operation);
	http.open("POST","requires/export_pi_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_pi_mst_reponse;

}

function fnc_pi_mst_reponse()
{
	if(http.readyState == 4) 
	{
		var response=trim(http.responseText).split('**');
		
		show_msg(trim(response[0]));
		
		if((response[0]==0 || response[0]==1))
		{
			document.getElementById('txt_system_id').value = response[1];			
			document.getElementById('update_id').value = response[1];
			document.getElementById('pi_number').value = response[2];
			set_button_status(1, permission, 'fnc_pi_mst',1);

			$('#cbo_item_category_id').attr('disabled','true');
			$('#cbo_exporter_id').attr('disabled','true');
			$('#cbo_within_group').attr('disabled','true');
			$('#cbo_buyer_name').attr('disabled','true');
		}
		else if(response[0]==2){
			show_msg('2');
            reset_form('pimasterform_1','','','cbo_currency_id,2','$(\'#tbl_pi_item tbody tr:not(:first)\').remove();');
            reset_form('pimasterform_2','','','','$(\'#tbl_pi_item tbody tr:not(:first)\').remove();');	
			disable_enable_fields('cbo_item_category_id*cbo_exporter_id*cbo_within_group',0);
		}
		else if(response[0]==7){
			show_msg('13');
		}
		else if(response[0]==8){
			
			alert(response[2]);
		}
		release_freezing();
	}
}

function calculate_amount(i)
{
	var quantity=$('#quantity_'+i).val()*1;
	var hdnQuantity=$('#hdnQuantity_'+i).val()*1;
	if(quantity>hdnQuantity)
	{
		alert ("PI Quantity Can not Exeed Order Quantity");
		$('#quantity_'+i).val('');
		return;
	}
	else
	{
		var ddd={ dec_type:5, comma:0, currency:''}
		math_operation( 'amount_'+i, 'quantity_'+i+'*rate_'+i, '*','',ddd);
		calculate_total_amount(1);
	}
	
}

function calculate_total_amount(type)
{
	if(type==1)
	{
		var ddd={ dec_type:5, comma:0, currency:''}
		var numRow = $('table#tbl_pi_item tbody tr').length; 
		math_operation( "txt_total_amount", "amount_", "+", numRow,ddd );
		math_operation( "txt_total_qnty", "quantity_", "+", numRow,ddd );
	}
	
	var txt_total_amount=$('#txt_total_amount').val();
	var txt_upcharge=$('#txt_upcharge').val();
	var txt_discount=$('#txt_discount').val();
	
	var net_tot_amnt=txt_total_amount*1+txt_upcharge*1-txt_discount*1;
	$('#txt_total_amount_net').val(net_tot_amnt.toFixed(4));
}

function fnCheckUnCheckAll(checkVal)
{
	for (Looper=0; Looper < document.pimasterform_2.length ; Looper++ )
	{
		var strType = document.pimasterform_2.elements[Looper].type;
		if (strType=="checkbox")
		{
			document.pimasterform_2.elements[Looper].checked=checkVal;
		}   
	}
}

function fnc_pi_item_details( operation )
{
	var cbo_item_category = $('#cbo_item_category_id').val();
	var update_id = $('#update_id').val();
	var txt_upcharge = $('#txt_upcharge').val();
	var txt_discount = $('#txt_discount').val();
	var cbo_currency_id = $('#cbo_currency_id').val();

	var pi_number = $('#pi_number').val();
	var txt_advance_job = $('#txt_advance_job').val();
	var txt_yd_job_id = $('#txt_yd_job_id').val();

	var txt_total_amount=0; var txt_total_amount_net=0; var txt_total_qnty=0;
	
	if(update_id=='')
	{
		alert('Please Save PI First');
		return false;
	}
	
	if(operation==2)
	{
		//show_msg('13');
		//return false;
	}
	
	var row_num=$('#tbl_pi_item tbody tr').length;
	var data_all=""; var i=0; var selected_row=0;
	
	for (var j=1; j<=row_num; j++)
	{
		var updateIdDtls=$('#updateIdDtls_'+j).val();
		if($('#workOrderChkbox_'+j).is(':checked') || updateIdDtls!="")
		{
			if (form_validation('workOrderNo_'+j+'*quantity_'+j+'*rate_'+j,'WO*Qunatity*Rate')==false)
			{
				return;
			}
			
			i++;
			
			if (cbo_item_category==1) // Knit Garments
			{
				data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&construction_" + i + "='" + $('#construction_'+j).val()+"'"+"&composition_" + i + "='" + $('#composition_'+j).val()+"'"+"&salesbooking_" + i + "='" + $('#salesbooking_'+j).val()+"'"+"&colorId_" + i + "='" + $('#colorId_'+j).val()+"'"+"&gsm_" + i + "='" + $('#gsm_'+j).val()+"'"+"&diawidth_" + i + "='" + $('#diawidth_'+j).val()+"'"+"&uom_" + i + "='" + $('#uom_'+j).val()+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'"+"&hideDeterminationId_" + i + "='" + $('#hideDeterminationId_'+j).val()+"'";
			}
			else if (cbo_item_category==10) // Knit Fabric
			{
				data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&construction_" + i + "='" + $('#construction_'+j).val()+"'"+"&composition_" + i + "='" + $('#composition_'+j).val()+"'"+"&salesbooking_" + i + "='" + $('#salesbooking_'+j).val()+"'"+"&colorId_" + i + "='" + $('#colorId_'+j).val()+"'"+"&gsm_" + i + "='" + $('#gsm_'+j).val()+"'"+"&diawidth_" + i + "='" + $('#diawidth_'+j).val()+"'"+"&uom_" + i + "='" + $('#uom_'+j).val()+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&txtRemarks_" + i + "='" + $('#txtRemarks_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'"+"&hideDeterminationId_" + i + "='" + $('#hideDeterminationId_'+j).val()+"'";
			}
			else if (cbo_item_category==11) // Woven Fabric
			{
				data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&construction_" + i + "='" + $('#construction_'+j).val()+"'"+"&composition_" + i + "='" + $('#composition_'+j).val()+"'"+"&salesbooking_" + i + "='" + $('#salesbooking_'+j).val()+"'"+"&colorId_" + i + "='" + $('#colorId_'+j).val()+"'"+"&gsm_" + i + "='" + $('#gsm_'+j).val()+"'"+"&diawidth_" + i + "='" + $('#diawidth_'+j).val()+"'"+"&uom_" + i + "='" + $('#uom_'+j).val()+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&txtRemarks_" + i + "='" + $('#txtRemarks_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'"+"&hideDeterminationId_" + i + "='" + $('#hideDeterminationId_'+j).val()+"'";
			}
			else if (cbo_item_category==20 || cbo_item_category==22) //Knitting, Dyeing and Finishing
			{
				data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&construction_" + i + "='" + $('#construction_'+j).val()+"'"+"&composition_" + i + "='" + $('#composition_'+j).val()+"'"+"&salesbooking_" + i + "='" + $('#salesbooking_'+j).val()+"'"+"&colorId_" + i + "='" + $('#colorId_'+j).val()+"'"+"&gsm_" + i + "='" + $('#gsm_'+j).val()+"'"+"&diawidth_" + i + "='" + $('#diawidth_'+j).val()+"'"+"&uom_" + i + "='" + $('#uom_'+j).val()+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&txtRemarks_" + i + "='" + $('#txtRemarks_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'"+"&hideDeterminationId_" + i + "='" + $('#hideDeterminationId_'+j).val()+"'";
			}
			else if (cbo_item_category==35) // Gmts Printing
			{
				data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&salesbooking_" + i + "='" + $('#salesbooking_'+j).val()+"'"+"&hideGsmItem_" + i + "='" + $('#hideGsmItem_'+j).val()+"'"+"&hideProcessEmbl_" + i + "='" + $('#hideProcessEmbl_'+j).val()+"'"+"&hideEmblType_" + i + "='" + $('#hideEmblType_'+j).val()+"'"+"&itemDesc_" + i + "='" + $('#itemDesc_'+j).val()+"'"+"&colorId_" + i + "='" + $('#colorId_'+j).val()+"'"+"&uom_" + i + "='" + $('#uom_'+j).val()+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'";
			}
			else if (cbo_item_category==23) // All Over Printing (AOP)
			{
				data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&salesbooking_" + i + "='" + $('#salesbooking_'+j).val()+"'"+"&colorId_" + i + "='" + $('#colorId_'+j).val()+"'"+"&hideAopColor_" + i + "='" + $('#hideAopColor_'+j).val()+"'"+"&gsm_" + i + "='" + $('#gsm_'+j).val()+"'"+"&hideBodypart_" + i + "='" + $('#hideBodypart_'+j).val()+"'"+"&uom_" + i + "='" + $('#uom_'+j).val()+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&txtRemarks_" + i + "='" + $('#txtRemarks_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'";
			}

			else if (cbo_item_category==37) // Gmts Wash
			{
				data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&jobstyle_" + i + "='" + $('#jobstyle_'+j).val()+"'"+"&salesbooking_" + i + "='" + $('#salesbooking_'+j).val()+"'"+"&hideGsmItem_" + i + "='" + $('#hideGsmItem_'+j).val()+"'"+"&colorId_" + i + "='" + $('#colorId_'+j).val()+"'"+"&itemDesc_" + i + "='" + $('#itemDesc_'+j).val()+"'"+"&hideProcessEmbl_" + i + "='" + $('#hideProcessEmbl_'+j).val()+"'"+"&hideWashType_" + i + "='" + $('#hideWashType_'+j).val()+"'"+"&uom_" + i + "='" + $('#uom_'+j).val()+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'";
			}

			else if (cbo_item_category==36) // Gmts EMB
			{
				data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&salesbooking_" + i + "='" + $('#salesbooking_'+j).val()+"'"+"&hideGsmItem_" + i + "='" + $('#hideGsmItem_'+j).val()+"'"+"&hideBodypart_" + i + "='" + $('#hideBodypart_'+j).val()+"'"+"&hideProcessEmbl_" + i + "='" + $('#hideProcessEmbl_'+j).val()+"'"+"&hideEmblType_" + i + "='" + $('#hideEmblType_'+j).val()+"'"+"&itemDesc_" + i + "='" + $('#itemDesc_'+j).val()+"'"+"&colorId_" + i + "='" + $('#colorId_'+j).val()+"'"+"&hideitemSize_" + i + "='" + $('#hideitemSize_'+j).val()+"'"+"&uom_" + i + "='" + $('#uom_'+j).val()+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'";
			}

			else if (cbo_item_category==45) // Trims
			{
				data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&hscode_" + i + "='" + $('#hscode_'+j).val()+"'"+"&salesbooking_" + i + "='" + $('#salesbooking_'+j).val()+"'"+"&hideGsmItem_" + i + "='" + $('#hideGsmItem_'+j).val()+"'"+"&itemDesc_" + i + "='" + $('#itemDesc_'+j).val()+"'"+"&colorId_" + i + "='" + $('#colorId_'+j).val()+"'"+"&hideitemSize_" + i + "='" + $('#hideitemSize_'+j).val()+"'"+"&uom_" + i + "='" + $('#uom_'+j).val()+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'";
			}
            else if (cbo_item_category==67) // Trims
            {
                data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&itemDesc_" + i + "='" + $('#itemDesc_'+j).val()+"'"+"&colorId_" + i + "='" + $('#colorId_'+j).val()+"'"+"&hideitemSize_" + i + "='" + $('#hideitemSize_'+j).val()+"'"+"&uom_" + i + "='" + $('#uom_'+j).val()+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'";
            }
			else if (cbo_item_category==116) // Service Garments
            {
                data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&hideGsmItem_" + i + "='" + $('#hideGsmItem_'+j).val()+"'"+"&hideProcessEmbl_" + i + "='" + $('#hideProcessEmbl_'+j).val()+"'"+"&colorId_" + i + "='" + $('#colorId_'+j).val()+"'"+"&uom_" + i + "='" + $('#uom_'+j).val()+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'";
            }
			else if (cbo_item_category==68 || cbo_item_category==69) //Yarn Dyeing[Service],Yarn Dyeing[Sales]
			{
				data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&hscode_" + i + "='" + $('#hscode_'+j).val()+"'"+"&buyerJob_" + i + "='" + $('#buyerJob_'+j).val()+"'"+"&custBuyer_" + i + "='" + $('#custBuyer_'+j).val()+"'"+"&countTypeId_" + i + "='" + $('#countTypeId_'+j).val()+"'"+"&hidecountId_" + i + "='" + $('#hidecountId_'+j).val()+"'"+"&yarnTypeId_" + i + "='" + $('#yarnTypeId_'+j).val()+"'"+"&yarnCompositionId_" + i + "='" + $('#yarnCompositionId_'+j).val()+"'"+"&colorRangeId_" + i + "='" + $('#colorRangeId_'+j).val()+"'"+"&colorId_" + i + "='" + $('#colorId_'+j).val()+"'"+"&appRef_" + i + "='" + $('#appRef_'+j).val()+"'"+"&uom_" + i + "='" + $('#uom_'+j).val()+"'"+"&adjTypeId_" + i + "='" + $('#adjTypeId_'+j).val()+"'"+"&totalQty_" + i + "='" + $('#totalQty_'+j).val()+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'";
			}
			else if (cbo_item_category==2) 
			{
				data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&hscode_" + i + "='" + $('#hscode_'+j).val()+"'"+"&Order_no_" + i + "='" + $('#Order_no_'+j).val()+"'"+"&AccPo_NO_" + i + "='" + $('#AccPo_NO_'+j).val()+"'"+"&jobstyle_" + i + "='" + $('#jobstyle_'+j).val()+"'"+"&itemDesc_" + i + "='" + $('#Style_Desc_'+j).val()+"'"+"&hideGsmItem_" + i + "='" + $('#hideGsmItem_'+j).val()+"'"+"&composition_" + i + "='" + $('#composition_'+j).val()+"'"+"&BrandId_" + i + "='" + $('#BrandId_'+j).val()+"'"+"&Status_" + i + "='" + $('#Status_'+j).val()+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'"; 
			}
			
			
			if($('#workOrderChkbox_'+j).is(':checked')){data_all+="&deleteIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'";}		
			
				
			txt_total_amount+=$('#amount_'+j).val()*1;
			txt_total_qnty+=$('#quantity_'+j).val()*1;
			if($('#workOrderChkbox_'+j).is(':checked')) selected_row++;
			
		}
	}

	if(selected_row<1)
	{
		alert("Please Select WO");
		return;
	}
	
	//alert(txt_total_amount);return;
	
	txt_total_amount_net=txt_total_amount*1+txt_upcharge*1-txt_discount*1;

	// alert(data_all+'='+'Details Data Check'); return;

	var data="action=save_update_delete_dtls&operation="+operation+'&cbo_item_category='+cbo_item_category+'&total_row='+i+'&update_id='+update_id+'&txt_total_amount='+txt_total_amount+'&txt_upcharge='+txt_upcharge+'&txt_discount='+txt_discount+'&txt_total_amount_net='+txt_total_amount_net+'&cbo_currency_id='+cbo_currency_id+'&pi_number='+pi_number+'&txt_advance_job='+txt_advance_job+'&txt_yd_job_id='+txt_yd_job_id+'&txt_total_qnty='+txt_total_qnty+data_all;

	//alert(data); return;

	freeze_window(operation);
	
	http.open("POST","requires/export_pi_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_pi_item_details_reponse;
}
		 
function fnc_pi_item_details_reponse()
{
	if(http.readyState == 4) 
	{
		//lert(http.responseText);
		//release_freezing();return;
		var response=http.responseText.split('**');

		show_msg(trim(response[0]));
		
		if(response[0]==0 || response[0]==1)
		{	
			var item_category = document.getElementById('cbo_item_category_id').value;
			var importer_id = document.getElementById('cbo_exporter_id').value;
			show_list_view(response[1]+'_'+item_category+'_'+importer_id, 'pi_details', 'pi_details_container', 'requires/export_pi_controller', '' ) ;
			set_button_status(1, permission, 'fnc_pi_item_details',2);
			$('#check_all').attr('checked',false);
			calculate_total_amount(1);
		}
		else if(response[0]==2)
		{
			show_msg('2');
			var item_category = document.getElementById('cbo_item_category_id').value;
			var importer_id = document.getElementById('cbo_exporter_id').value;
			show_list_view(response[1]+'_'+item_category+'_'+importer_id, 'pi_details', 'pi_details_container', 'requires/export_pi_controller', '' ) ;
			get_php_form_data( response[1], "populate_total_amount_data", "requires/export_pi_controller" );
			calculate_total_amount(1);
		}
		else if(response[0]==7)
		{
			show_msg('13');
		}
		else if(response[0]==8){
			
			alert(response[2]);
		}
		release_freezing();
	}
}

function openmypage()
{
	var exporter_id 	= $('#cbo_exporter_id').val();
	var item_category_id 	= $('#cbo_item_category_id').val();
	var cbo_within_group 	= $('#cbo_within_group').val();
	var cbo_buyer_name 	= $('#cbo_buyer_name').val();
	
	if (form_validation('cbo_exporter_id','Exporter')==false)
	{
		return;
	}
	else
	{ 	
		var title = 'PI Selection Form';	
		var page_link = 'requires/export_pi_controller.php?action=pi_popup&exporter_id='+exporter_id+'&item_category_id='+item_category_id+'&cbo_within_group='+cbo_within_group+'&cbo_buyer_name='+cbo_buyer_name;
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1090px,height=450px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("txt_selected_pi_id") 
			var theemail_itemcat=this.contentDoc.getElementById("item_category_id");
			//alert(theemail.value);
			//alert(theemail_itemcat.value);return;
			if(theemail.value!="")
			{
				freeze_window(5);
				show_list_view(exporter_id+'_'+theemail_itemcat.value+'_'+'1', 'catagory_wise_pi_details', 'export_pi_details_container', 'requires/export_pi_controller', '' ) ;
				get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/export_pi_controller" );
				show_list_view(theemail.value+'_'+theemail_itemcat.value+'_'+exporter_id, 'pi_details', 'pi_details_container', 'requires/export_pi_controller', '' ) ;
				
				var wo_no=$('#workOrderNo_1').val(); 
				if(wo_no=="")
				{
					set_button_status(0, permission, 'fnc_pi_item_details',2);
				}
				else
				{
					set_button_status(1, permission, 'fnc_pi_item_details',2);
				}
				calculate_total_amount(1);
				release_freezing();
			} 
		}
	}
}

function openmypage_wo(row_num)
{
	var exporter_id 		= $('#cbo_exporter_id').val();
	var within_group 		= $('#cbo_within_group').val();
	var buyer_name 			= $('#cbo_buyer_name').val();
	var item_category_id 	= $('#cbo_item_category_id').val();
	if($('#update_id').val()=="")
	{
		alert("Please Save PI First.");
		return;
	}
	if (form_validation('cbo_item_category_id*cbo_exporter_id','Export Item Category*Exporter')==false)
	{
		return;
	}
	else
	{ 
		var prev_wo_ids=''; var prev_wo_feb_datas='';
		var curr_wo_id='';var curr_wo_dtls_id=''; var curr_quantity=''; var curr_amount='';
		var row_num=$('#tbl_pi_item tbody tr').length;
		for (var j=1; j<=row_num; j++)
		{
			var hideWoDtlsId=$('#hideWoDtlsId_'+j).val();
			var hideWoId=$('#hideWoId_'+j).val();
			var hideUpdateIdDtls=$('#updateIdDtls_'+j).val();
			var WoQuantity=$('#quantity_'+j).val();
			var WoAmount=$('#amount_'+j).val();
			if(hideWoDtlsId!="")
			{
				if(prev_wo_ids=="") prev_wo_ids=hideWoDtlsId; else prev_wo_ids+=","+hideWoDtlsId;
			}
			if(hideUpdateIdDtls=="")
			{
				if(curr_wo_id=="") curr_wo_id=hideWoId; else curr_wo_id+=","+hideWoId;
				if(curr_wo_dtls_id=="") curr_wo_dtls_id=hideWoDtlsId; else curr_wo_dtls_id+=","+hideWoDtlsId;
				if(curr_quantity=="") curr_quantity=WoQuantity; else curr_quantity+=","+WoQuantity;
				if(curr_amount=="") curr_amount=WoAmount; else curr_amount+=","+WoAmount;
			}
			if(item_category_id==10)
			{
				
				var hideWoId=$('#hideWoId_'+j).val();
				var workOrderNo=$('#workOrderNo_'+j).val();
			}
		}	

		var title = 'Sales/Booking No. Selection Form';	
		var page_link = 'requires/export_pi_controller.php?exporter_id='+exporter_id+'&within_group='+within_group+'&buyer_name='+buyer_name+'&item_category_id='+item_category_id+'&prev_wo_ids='+prev_wo_ids+'&curr_wo_id='+curr_wo_id+'&curr_quantity='+curr_quantity+'&curr_amount='+curr_amount+'&action=wo_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("txt_selected_wo_id"); //Access form field with id="emailfield"
			var theemail_mst=this.contentDoc.getElementById("txt_selected_wo_mst_id");
			var based_on_data=this.contentDoc.getElementById("cbo_based_on");
			//alert(theform+'='+theemail+'='+theemail_mst+'='+based_on_data);//return;
			if(theemail.value!="")
			{
				//alert(theemail.value);return;
				freeze_window(5);
				var numRow = $('table#tbl_pi_item tbody tr').length; 
				var wo_no=$('#workOrderNo_'+row_num).val(); 
				if(wo_no=="")
				{
					numRow--;
				}

				var job_id=$('#hideWoId_'+row_num).val();
				var item_category=$('#cbo_item_category_id').val();
				/*if(item_category==68 || item_category==69)
				{
					if(job_id!=theemail_mst.value && job_id!='')
					{
						alert("Multiple Job Not Allowed!!!");
						release_freezing();
						return;
					}
				}*/

				var data=theemail.value+"**"+numRow+"**"+theemail_mst.value+"**"+based_on_data.value+"**"+item_category_id+"**"+curr_wo_dtls_id+"**"+curr_quantity;
				//alert(data); // its ok
				var list_view_wo =return_global_ajax_value( data, 'populate_data_wo_form', '', 'requires/export_pi_controller');
				// alert(list_view_wo);return;
				if(wo_no=="")
				{
					$("#row_"+row_num).remove();
				}
				
				$("#tbl_pi_item tbody:last").append(list_view_wo);	
				calculate_total_amount(1);
				release_freezing();
			} 
		}
	}
}
function check_variable_setting(exporter_id)
{
	var response=return_global_ajax_value(exporter_id, 'company_variable_setting_check', '', 'requires/export_pi_controller');	
	if (response == 1){
		$('#hidden_variable_setting').val(response);
		$('#pi_number').val('');
		$('#pi_number').attr('readonly','readonly');
	}
}

function check_swift_code_setting(bank_id)
{
	
	var exporter_id 		= $('#cbo_exporter_id').val();
	var data=bank_id+"**"+exporter_id;
 	var response=return_global_ajax_value(data, 'swift_code_setting', '', 'requires/export_pi_controller');	
 	if(response)
	{
		$('#txt_swift').val(response);
 	}
}

function copy_remarks(i) 
{
	var row_num = $('#pi_details_container tr').length;
	var construction = document.getElementById('construction_' + i).value;
	var composition = document.getElementById('composition_' + i).value;
	var gsm = document.getElementById('gsm_' + i).value;
	var remarks = document.getElementById('txtRemarks_' + i).value;

	var copy_remarks_all=$("#copy_remarks_all").is(":checked");

	if(copy_remarks_all)
	{
		$("#pi_details_container").find('tr').each(function () {
			var x = $(this).find('input[name="txtSerial[]"]').val();
			if(x != i)
			{
				var construction_check = document.getElementById('construction_' + x).value;
				var composition_check = document.getElementById('composition_' + x).value;
				var gsm_check = document.getElementById('gsm_' + x).value;
				if (construction==construction_check && composition==composition_check && gsm==gsm_check) {
					$('#txtRemarks_' + x).val(remarks);
				}
			}

		});
	}
}

function copy_remarks_v2(i) 
{
	var row_num = $('#pi_details_container tr').length;
	var workOrderNo = document.getElementById('workOrderNo_' + i).value;
	var remarks = document.getElementById('txtRemarks_' + i).value;

	var copy_remarks_all=$("#copy_remarks_all").is(":checked");

	if(copy_remarks_all)
	{
		$("#pi_details_container").find('tr').each(function () {
			var x = $(this).find('input[name="txtSerial[]"]').val();
			if(x != i)
			{
				var workOrderNo_check = document.getElementById('workOrderNo_' + x).value;
				if (workOrderNo==workOrderNo_check) {
					$('#txtRemarks_' + x).val(remarks);
				}
			}
		});
	}
}

function openmypage_advance_job()
{
	if( form_validation('cbo_exporter_id','Company Name')==false )
	{
		return;
	}
	var data=document.getElementById('cbo_exporter_id').value+'_'+document.getElementById('cbo_item_category_id').value+'_'+document.getElementById('txt_advance_job').value;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/export_pi_controller.php?action=job_no_popup&data='+data,'YD Job No Popup', 'width=740px,height=380px,center=1,resize=0','../')
	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("hdn_job_info");
		if (theemail.value!="")
		{
			freeze_window(5);
			var response=theemail.value.split('_');
			document.getElementById("txt_yd_job_id").value=response[0];
			document.getElementById("txt_advance_job").value=response[1];
			disable_enable_fields('txt_job_no*txt_style_no',1);
			release_freezing();
		}
	}
}

function disable_enable_advance_job(category_id)
{
	if (category_id==69) $('#txt_advance_job').attr('disabled',false);
	else $('#txt_advance_job').attr('disabled',true);

}

function disable_enable_tenor(pay_term_id)
{
	if (pay_term_id==2) $('#txt_tenor').attr('disabled',false);
	else $('#txt_tenor').attr('disabled',true);

}

function reset_fnc()
{
	//location.reload();
	reset_form('pimasterform_1','','','cbo_currency_id,2','$(\'#tbl_pi_item tbody tr:not(:first)\').remove();');
	disable_enable_fields('cbo_item_category_id*cbo_exporter_id*cbo_within_group',0);
	window.location.reload();
}

</script>

<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission); ?>
        <div>
			<form name="pimasterform_1" id="pimasterform_1" autocomplete="off"> 
                <fieldset style="width:1150px;">
                    <legend>PI Details</legend>
                    <table width="100%" border="0" cellpadding="0" cellspacing="2">
                    	<tr>
                        	<td colspan="8" height="25" valign="middle" align="center" style="border-bottom:0px solid #666"><strong>&nbsp;&nbsp;&nbsp;&nbsp;System ID</strong><input type="text" name="txt_system_id"  style="width:140px"  id="txt_system_id" class="text_boxes" placeholder="Display" readonly value="" /></td>
                        </tr>
                        <tr>
                            <td width="110" align="right" class="must_entry_caption">Export Item Category</td>
                            <td width="150"> 
                                <? 
                                asort($export_item_category);
                                echo create_drop_down( "cbo_item_category_id", 151, $export_item_category,'', 1, ' --Select-- ',0,"show_list_view(document.getElementById('cbo_exporter_id').value+'_'+this.value+'_'+'1'+'_'+this.value, 'catagory_wise_pi_details', 'export_pi_details_container', 'requires/export_pi_controller', '' ) ;disable_enable_advance_job(this.value)",0,'1,2,3,4,5,10,11,20,22,23,24,30,31,35,36,37,45,67,68,69,116');
                                ?>  
                            </td>
                            <td width="110" align="right" class="must_entry_caption">Exporter</td>
                            <td width="150">
                                 <?php echo create_drop_down( "cbo_exporter_id", 151,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name",'id,company_name', 1, 'Select',0,"load_drop_down( 'requires/export_pi_controller',this.value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/export_pi_controller',this.value, 'load_drop_down_avising_bank', 'avising_bank_td' );check_variable_setting(this.value);load_drop_down( 'requires/export_pi_controller',this.value, 'company_wise_report_button_setting', 'buttion_list' );set_field_level_access(this.value);",0); ?>
                                <input type="hidden" name="hidden_variable_setting" id="hidden_variable_setting" value="">       
                            </td>
                            <td width="110" class="must_entry_caption" align="right">Within Group</td>                                              
                            <td>
                                <?php echo create_drop_down( "cbo_within_group", 151, $yes_no,"", 0, "--  --", 0, "load_drop_down( 'requires/export_pi_controller',document.getElementById('cbo_exporter_id').value+'_'+this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?>
                            </td>
							<td align="right" class="must_entry_caption">Customer/Buyer</td>
                            <td id="buyer_td"> 
                                <?php echo create_drop_down( "cbo_buyer_name", 151, $blank_array,"", 1, "-- Select Buyer --", 0, "",1 ); ?>
                            </td>
                        </tr>
                        <tr>                        	
                            <td align="right" class="must_entry_caption">PI No</td>
                            <td><input type="text" name="pi_number" id="pi_number" class="text_boxes" style="width:140px" placeholder="Double click for PI" onDblClick="openmypage()" maxlength="30" /></td>
                            <td align="right" class="must_entry_caption">PI Date</td>
                            <td><input type="text" name="pi_date" id="pi_date" class="datepicker"  style="width:140px" /></td>
							<td align="right">Last Shipment Date</td>
                            <td><input type="text" name="last_shipment_date"  style="width:140px"  id="last_shipment_date" class="datepicker" value="" /></td>
                            <td align="right">PI Validity Date</td>
							<td><input type="text" name="pi_validity_date" id="pi_validity_date"  style="width:140px"  class="datepicker" value="" /></td>
                        </tr>
                        <tr>
                            <td align="right" class="must_entry_caption">Currency</td>
                            <td>
                                <?php echo create_drop_down( "cbo_currency_id", 151,$currency,'',0,'',2,0,0); ?>       
                            </td>
							<td align="right">HS Code</td>
                            <td><input type="text" name="hs_code" id="hs_code" class="text_boxes"  style="width:140px"  value=""  maxlength="30" /></td>
                            <td align="right">Internal File No</td>
                            <td><input type="text" name="txt_internal_file_no" id="txt_internal_file_no"  style="width:140px"  class="text_boxes"  maxlength="100" /></td>
                            <td align="right">Advising Bank</td>
							<td id="avising_bank_td">
								<?php 
								echo create_drop_down( "cbo_advising_bank", 151, $blank_array,"", 1, "-- Select Bank --", 0, "",1 );?> 
							</td>
                        </tr>
                        
                        <tr>
							<td align="right">SWIFT</td>
                            <td>
                             	 <input type="text" name="txt_swift" id="txt_swift"  style="width:140px;" class="text_boxes" />
                            </td>
                         	<td align="right">Attention</td>
                            <td>
                             	 <input type="text" name="txt_attention" id="txt_attention"  style="width:140px;" class="text_boxes" />
                            </td>
                         	<td align="right">Remarks</td>
                            <td>
                             	 <input type="text" name="txt_remarks" id="txt_remarks"  style="width:140px;" class="text_boxes" />
                            </td>
							<td align="right" class="must_entry_caption">PI Revised Date</td>
                            <td><input type="text" name="pi_revised_date" id="pi_revised_date" class="datepicker"  style="width:140px" /></td>
                        </tr>
						<tr>
							<td align="right">PI Revise</td>
							<td>
								<? 
								echo create_drop_down( "cbo_pi_revise", 151, $pi_revise_array,"", 1, "-- Select --", 0, "",0 );
								?> 
							</td>
							<td align="right">Advance Job</td>
							<td>
								<input type="text" name="txt_advance_job" id="txt_advance_job" class="text_boxes" style="width:140px" placeholder="Browse" onDblClick="openmypage_advance_job()" readonly/>
								<input type="hidden" name="txt_yd_job_id" id="txt_yd_job_id">
							</td>              	
                            <td align="right">File</td>
                            <td>
                                 <input type="button" id="image_button" class="image_uploader" style="width:151px" value="CLICK TO ADD FILE" onClick="file_uploader( '../../', document.getElementById('update_id').value,'', 'export_pro_forma_invoice',2,1)" />
                        	</td>
                            <td align="right">Terms and Condition</td>
                            <td>
                            <? 
                            include("../../terms_condition/terms_condition.php");
                            terms_condition(152,'update_id','../../');
                            ?>
                            </td>
                        </tr>
						<tr>
							<td align="right">Pay Term</td>
							<td>
								<? 
								echo create_drop_down( "cbo_pay_term", 151, $pay_term,"", 1, "-- Select --", 0, "disable_enable_tenor(this.value);",0,"1,2");
								?> 
							</td>
							<td align="right">Tenor</td>
							<td>
                             	<input type="text" name="txt_tenor" id="txt_tenor"  style="width:140px;" class="text_boxes_numeric" />
                            </td>
                            <td align="right">Status</td>
							<td><?=create_drop_down("cbo_status", 151, $row_status, 0, "", 1, "",""); ?></td>
							<td align="right">Weight approx</td>
                            <td>
                             	 <input type="text" name="txt_weight_approx" id="txt_weight_approx"  style="width:140px;" class="text_boxes" />
                            </td>
                        </tr>
						<tr>
							<td align="right">Issuing Bank</td>
							<td id="issue_bank_td">
								<? echo create_drop_down( "txt_issuing_bank", 151, $blank_array,"", 1, " Select", 0, "" ); ?>
							</td>
							<td align="right">Ready to Approve </td>
                        <td>
                            <?
                            $ready_to_approve = array(1 => "Yes", 2 => "No");
                            echo create_drop_down("cbo_approved", 151,  $ready_to_approve, "", 1, "-- Select--", 0, "", "", "");
                            ?>
                        </td>
                        </tr>
                        <tr>
                            <td colspan="8" height="50" valign="middle" align="center" class="button_container">
                                <input type="hidden" name="update_id" id="update_id" value="" readonly/>
                                <? 
							   		// echo load_submit_buttons( $permission, "fnc_pi_mst", 0,1 ,"reset_form('pimasterform_1','','','cbo_currency_id,2','$(\'#tbl_pi_item tbody tr:not(:first)\').remove();')",1);
									   echo load_submit_buttons( $permission, "fnc_pi_mst", 0,0 ,"reset_fnc()",1);
							    ?>
                             </td> 
							                        			
                        </tr>      
						<tr>
						<td colspan="6" id="buttion_list"valign="middle" align="center">
							
							 </td> 
						</tr>                  
                    </table>
                </fieldset>
			</form>
			<form name="pimasterform_2" id="pimasterform_2" autocomplete="off">
                <fieldset style="width:1050px; margin-top:10px;">
                    <legend>PI Item Details</legend>
                    <div id="export_pi_details_container"></div>
                </fieldset>
            </form>
        </div>
    </div>
	<div style="display:none" id="data_panel"></div>
	<a id="print_report_Excel" href="" style="text-decoration:none" download hidden>#</a>
</body>
<script type="text/javascript">
	$(document).ready(function() 
		{ 
			for (var property in mandatory_field_arr) {
			  $("#"+mandatory_field_arr[property]).parent().prev('td').css("color", "blue");
			}
		});
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>