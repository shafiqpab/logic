<?
/*-------------------------------------------- Comments
Purpose			: 	This form created Inspection and End Sewing Entry
				
Functionality	:	
JS Functions	:
Created by		:	Md Mamun Ahmed Sagor
Creation date 	: 	09/04/2023
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Inspection and End Sewing Entry","../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";


 

function  generate_report(rptType)
{
	var cbo_search_category = $('#cbo_string_search_type').val();
	var cbo_company_id = $("#cbo_company_id").val();
	var txt_booking_no = $("#txt_booking_no").val();
	var cbo_year = $('#cbo_year_selection').val();
	var txt_date_from = $('#txt_date_from').val();
	var txt_date_to = $('#txt_date_to').val();
	var txt_barcode = $("#txt_barcode").val();
	var txt_recieved_number = $("#txt_recieved_id").val();
	
    if( form_validation('cbo_company_id','Company')==false )
    {
        return;
    }

	var dataString = "&cbo_company_id="+cbo_company_id+"&cbo_search_category="+cbo_search_category+"&txt_booking_no="+txt_booking_no+"&cbo_year="+cbo_year+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&txt_barcode="+txt_barcode+"&txt_recieved_number="+txt_recieved_number;


	var data="action=generate_report"+dataString;
	freeze_window(5);
	http.open("POST","requires/inspection_and_end_sewing_entry_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_report_reponse; 
}

function generate_report_reponse()
{	
	if(http.readyState == 4) 
	{
		//alert(http.responseText);	 
		var reponse=trim(http.responseText).split("**");
		//alert(reponse[2]);
		$("#report_container2").html(reponse[0]);
		document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		release_freezing();
		show_msg('3');
	}
} 


function fn_knit_defect(prod_dtls_id,barcode_no)
{
	var roll_maintained=$('#roll_maintained').val();
	//alert(roll_maintained);

	var company_id=$('#cbo_company_id').val();
	if(prod_dtls_id=="")
	{
		alert("Sorry !!.");return;
	}
	else
	{
		var title = 'Knitting Defect Info';	

		var page_link='requires/inspection_and_end_sewing_entry_controller.php?update_dtls_id='+prod_dtls_id+'&roll_maintained='+roll_maintained+'&company_id='+company_id+'&barcode_no='+barcode_no+'&action=knit_defect_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=500px,center=1,resize=1,scrolling=0','');

		emailwindow.onclose=function()
		{
			
		}
	}
}


function new_window()
{
	$('#scroll_body tr:first').hide();
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	
	d.close(); 
	$('#scroll_body tr:first').show();
}


</script>
</head>

 

<?
	 

 
	
	$variable_data = sql_select("select fabric_grade, get_upto_first, get_upvalue_first, get_upto_second,company_name, get_upvalue_second from variable_settings_production where  variable_list=36 order by company_name,get_upvalue_first asc");  
	$exc_perc = array();
	$i = 0;
	$variable_data_count = count($variable_data);
	foreach ($variable_data as $row) 
	{
		if ($exp[$row[csf("company_name")]] == '') $i = 0;
		$exc_perc[$row[csf("company_name")]]['limit'][$i] = $row[csf("get_upvalue_first")] . "__" . $row[csf("get_upvalue_second")];
		$exc_perc[$row[csf("company_name")]]['grade'][$i] = $row[csf("fabric_grade")];
		$i++;
		$exp[$row[csf("company_name")]] = 1;
	}
	//print_r($exc_perc);
	//$js_variable_data_arr=json_encode($exc_perc);
 
	$supplier_arr = return_library_array("select id, short_name from  lib_supplier", "id", "short_name");

 
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) 
	{
		$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
		$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	}

	if ($roll_maintained == 1) 
	{

		/*$data_array=sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.body_part_id, b.febric_description_id, b.machine_no_id, b.gsm, b.width, b.color_id, b.yarn_lot, b.yarn_count, c.barcode_no, c.id as roll_id, c.roll_no, c.qnty
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
		WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2) and c.entry_form in(2) and c.status_active=1 and c.is_deleted=0 and b.id=$update_dtls_id");
		$roll_dtls_data_arr=array();
		foreach($data_array as $row)
		{
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["id"]=$row[csf("id")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["entry_form"]=$row[csf("entry_form")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["company_id"]=$row[csf("company_id")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["receive_basis"]=$row[csf("receive_basis")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["booking_no"]=$row[csf("booking_no")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["booking_id"]=$row[csf("booking_id")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["knitting_source"]=$row[csf("knitting_source")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["knitting_company"]=$row[csf("knitting_company")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["dtls_id"]=$row[csf("dtls_id")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["prod_id"]=$row[csf("prod_id")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["febric_description_id"]=$constructtion_arr[$row[csf("febric_description_id")]]." ".$composition_arr[$row[csf('febric_description_id')]];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["machine_no_id"]=$row[csf("machine_no_id")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["gsm"]=$row[csf("gsm")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["width"]=$row[csf("width")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["color_id"]=$row[csf("color_id")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["yarn_lot"]=$row[csf("yarn_lot")];

			$roll_dtls_data_arr[$row[csf("barcode_no")]]["yarn_count"]=$row[csf("yarn_count")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["barcode_no"]=$row[csf("barcode_no")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["roll_id"]=$row[csf("roll_id")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["roll_no"]=$row[csf("roll_no")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["qnty"]=$row[csf("qnty")];
		}*/
	} 
	else 
	{

		$data_array = sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.body_part_id, b.febric_description_id, b.machine_no_id, b.gsm, b.width, b.color_id, b.yarn_lot, b.yarn_count, b.grey_receive_qnty as qnty
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b 
		WHERE a.id=b.mst_id and a.entry_form in(2) and b.id=$update_dtls_id");

		$roll_dtls_data_arr = array();
		foreach ($data_array as $row) 
		{

			$constraction_comp = $constructtion_arr[$row[csf("febric_description_id")]] . " " . $composition_arr[$row[csf('febric_description_id')]];
			$machine_dia = return_field_value("dia_width", "lib_machine_name", "id=" . $row[csf("machine_no_id")], "dia_width");
			$gsm = $row[csf("gsm")];
			$width = $row[csf("width")];
			$color_id_arr = array_unique(explode(",", $row[csf("color_id")]));
			$all_color = "";
			foreach ($color_id_arr as $color_id) {
				$all_color .= return_field_value("color_name", "lib_color", "id='$color_id'", "color_name") . ",";
			}
			$all_color = chop($all_color, ",");

			$yarn_count_arr = array_unique(explode(",", $row[csf("yarn_count")]));
			$all_yarn_count = "";
			foreach ($yarn_count_arr as $count_id) {
				$all_yarn_count .= return_field_value("yarn_count", "lib_yarn_count", "id='$count_id'", "yarn_count") . ",";
			}
			$all_yarn_count = chop($all_yarn_count, ",");
			$yarn_lot = $row[csf("yarn_lot")];
			$qnty = $row[csf("qnty")];
			$lot_arr = array_unique(explode(",", $row[csf("yarn_lot")]));
			foreach ($lot_arr as $lot) {
				$supplier_id = return_field_value("max(supplier_id) as supplier_id", "product_details_master", "item_category_id=1 and lot='$lot'", "supplier_id");
				$all_supplier .= $supplier_arr[$supplier_id] . ",";
			}
			$all_supplier = chop($all_supplier, ",");
		}
		$disable = "disabled";
	}
	?>
	<script>
		
		var exc_perc =<? echo json_encode($exc_perc); ?>;
		
        function fabric_grading(comp, point) {
            //alert(comp)
            var newp = exc_perc[comp]["limit"];
            newp = JSON.stringify(newp);
            var newstr = newp.split(",");
            for (var m = 0; m < newstr.length; m++) {
            	var limit = exc_perc[comp]["limit"][m].split("__");
            	if ((limit[1] * 1) == 0 && (point * 1) >= (limit[0] * 1)) {
            		return ( exc_perc[comp]["grade"][m]);
            	}
            	if ((point * 1) >= (limit[0] * 1) && (point * 1) <= (limit[1] * 1)) {
            		return exc_perc[comp]["grade"][m];
            	}
                // alert( newstr[m]+"=="+m)
            }
            return '';
        }

        var roll_maintain = $('#hide_roll_maintain').val();
        function fn_barcode() {
        
        	var roll_maintain = $('#hide_roll_maintain').val();
         
        		var title = 'Barcode Or Details Info';
        		var page_link = 'requires/inspection_and_end_sewing_entry_controller.php?roll_maintained=' + roll_maintain + '&action=barcode_defect_popup';
        		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=350px,center=1,resize=1,scrolling=0', '../');
        		emailwindow.onclose = function () {
        			var bar_code_ref = this.contentDoc.getElementById("hide_barcode_id").value.split("**");
        			if (bar_code_ref[1] != "") {
        				get_php_form_data(bar_code_ref[0], 'barcode_roll_find', 'inspection_and_end_sewing_entry_controller');
        			}
        		}
        	
        }


        if (roll_maintain == 1) {
        	$('#txt_barcode').live('keydown', function (e) {
        		if (e.keyCode === 13) {
        			e.preventDefault();
        			var bar_code = $('#txt_barcode').val();
        			get_php_form_data(bar_code, 'barcode_roll_find', 'inspection_and_end_sewing_entry_controller');
        		}
        	});

        	$(document).ready(function (e) {
        		var roll_maintain = $('#hide_roll_maintain').val() * 1;
        		if (roll_maintain > 0) {
        			$('#txt_barcode').focus();
        		}
        		else {
        			$('#txt_qc_name').focus();
        		}

        	});
        }

        function caculate_roll_length() {
        	var roll_weight = $('#txt_roll_weight').val() * 1;
        	var roll_width = $('#txt_roll_width').val() * 1;
        	var gsm = $('#txt_gsm').val() * 1;
        	var roll_length = ((roll_weight * 1000) / (gsm * roll_width * 0.0254) * 1.09361);
        	$('#txt_roll_length').val(number_format(roll_length, 4, '.', ''));
        }

        function fn_panelty_point(i) {

        	var defect_count = $('#defectcount_' + i).val() * 1;
        	var found_inche = $('#foundInche_' + i).val() * 1;
        	var company_id = $('#company_id').val();
        	var found_inche_calc = "";
        	if (found_inche == 1) found_inche_calc = 1;
        	else if (found_inche == 2) found_inche_calc = 2;
        	else if (found_inche == 3) found_inche_calc = 3;
        	else if (found_inche == 4) found_inche_calc = 4;
        	else if (found_inche == 5) found_inche_calc = 2;
        	else if (found_inche == 6) found_inche_calc = 4;
        	var penalty_val = defect_count * found_inche_calc;
        	$('#penaltycount_' + i).val(penalty_val);
        	var ddd = {dec_type: 4, comma: 0, currency: ''}
        	var numRow = $('table#dtls_part tbody tr').length;
        	math_operation("total_penalty_point", "penaltycount_", "+", numRow, ddd);
        	var penalty_ratio = (($('#total_penalty_point').val() * 1) * 36 * 100) / (($('#txt_roll_length').val() * 1) * ($('#txt_roll_width').val() * 1));
        	$('#total_point').val(number_format(penalty_ratio, 4, '.', ''));
            //alert(penalty_ratio);
            /*if(penalty_ratio<21) fab_grade="A";
             else if(penalty_ratio<29 && penalty_ratio>20) fab_grade="B";
             else fab_grade="Reject";*/

             $('#fabric_grade').val(fabric_grading(company_id, penalty_ratio));
         }
         function generate_report_file(data,action,page)
         {
         	window.open("requires/inspection_and_end_sewing_entry_controller.php?data=" + data+'&action='+action, true );
         }

        function fnc_grey_defect_entry(operation) {

         	if (operation == 2) {
         		show_msg('13');
         		return;
         	}

         	if(operation == 4)
         	{	
         		generate_report_file($('#update_id').val() ,
         			'KnittingProductionPrint', 'requires/inspection_and_end_sewing_entry_controller');
         		return;
         	}

         	if (form_validation('txt_roll_length*txt_qc_date*fabric_grade', 'Roll Length*QC Date*Fabric Grade') == false) {
         		return;
         	}
         	var table_length = $('#dtls_part tbody tr').length;
         	var data_string = "";
         	var k = 1;
         	var count_tbl_length = 0;
         	for (var i = 1; i <= table_length; i++) {
         		var defect_name = $('#defectId_' + i).val();
         		var defect_count = $('#defectcount_' + i).val();
         		var found_in_inche = $('#foundInche_' + i).val();
         		var found_inche_val = "";
         		var penalty_point = $('#penaltycount_' + i).val() * 1;

         		if (penalty_point > 0) {
         			if (found_in_inche == 5) found_inche_val = 2;
         			else if (found_in_inche == 6) found_inche_val = 4;
         			else found_inche_val = found_in_inche;
         			data_string += '&defectId_' + k + '=' + defect_name + '&defectcount_' + k + '=' + defect_count + '&foundInche_' + k + '=' + found_in_inche + '&foundIncheVal_' + k + '=' + found_inche_val + '&penaltycount_' + k + '=' + penalty_point;
         			count_tbl_length++;
         			k++;

         		}
         	}

         	data_string = data_string + '&count_tbl_length=' + count_tbl_length;


         	var data = "action=save_update_delete_defect&operation=" + operation + get_submitted_data_string('company_id*hide_roll_maintain*txt_roll_no*roll_id*txt_qc_name*txt_roll_width*txt_roll_weight*txt_roll_length*txt_reject_qnty*hid_reject_qnty*txt_qc_date*total_penalty_point*total_point*fabric_grade*fabric_comments*update_id*hidden_batch_id', "../") + data_string;
            //alert(data);return;
            //alert(data);
            freeze_window(operation);

            http.open("POST", "requires/inspection_and_end_sewing_entry_controller.php", true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_grey_defect_entry_response;
        }

        function fnc_grey_defect_entry_response() {
        	if (http.readyState == 4) {
                //release_freezing();return;
                var reponse = trim(http.responseText).split('**');
                if (reponse[0] == 20) {
                	alert(reponse[1]);
                	release_freezing();
                	return;
                }
                show_msg(reponse[0]);
                if ((reponse[0] == 0 || reponse[0] == 1)) {
                	document.getElementById('update_id').value = reponse[1];
					var hidden_batch_id= $('#hidden_batch_id').val();
                	$('#dtls_list_container').html("");
                	 show_list_view(hidden_batch_id, 'show_qc_listview', 'dtls_list_container', 'requires/inspection_and_end_sewing_entry_controller', '');
                	set_button_status(0, permission, 'fnc_grey_defect_entry', 1);
                	$('#master_part').find('input', 'select').val("");
                	release_freezing();
                }
                else {
                	release_freezing();
                }

            }
        }

        function fn_recet_details() {
        	$('#dtls_part').find('input').val("");
        	$('#dtls_part').find('select').val(0);
        }

        function reject_status(type)
        {
        	//alert(type);
        	if (type==3) // reject
        	{
        		var roll_weight = $('#txt_roll_weight').val();
        		$('#txt_reject_qnty').val(roll_weight);
        	}
        	else // QC Pass, Held Up
        	{
        		//$('#txt_reject_qnty').val('');
        	}
        }

        /*$(function(){  
        	// alert(typeof(roll_weight));      	
        	$('#txt_reject_qnty').keyup(function(e) 
        	{
        		var roll_weight = parseInt($('#txt_roll_weight').val());
        		var Reject_Qty = parseInt($(this).val());
        		//alert(roll_weight+'='+Reject_Qty);   
        		if (roll_weight < Reject_Qty) 
        		{
        			e.preventDefault();
        			alert('Over Quantity Not Allowed');
        			$(this).val(roll_weight);
        			// return;
        		}
        	});
        });*/
        
        $(function() {
        	$('#txt_reject_qnty').keyup(function(e)
        	{
        	    var roll_weight = parseInt($('#txt_roll_weight').val());
        	    var Reject_Qty = parseInt($(this).val());
                if ($(this).val() > roll_weight)
                {
                   e.preventDefault();     
                   $(this).val(roll_weight);
                   alert('Over Qty Not Alowed');
                }
            });
        });

		function openmypage_batchnum()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var roll_maintained=$('#roll_maintained').val();
		
			var page_link='requires/inspection_and_end_sewing_entry_controller.php?cbo_company_id='+cbo_company_id+'&action=batch_number_popup';
			var title='Batch Number Popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=820px,height=420px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var batch_datas=this.contentDoc.getElementById("hidden_batch_id").value.split("_");
				var batch_data=batch_datas[0];
				var is_sales  =batch_datas[1];
				if(batch_data!="")
				{
					freeze_window(5);
					get_php_form_data(batch_data+'_'+is_sales, "populate_data_from_batch", "requires/inspection_and_end_sewing_entry_controller" );
					 
					release_freezing();
				}
			 
			}
		//}
	}
	




    </script>
    <body onLoad="set_hotkey()">
    	<? echo load_freeze_divs("../../", $permission); ?>
    	<form name="defectQcResult_1" id="defectQcResult_1" autocomplete="off">
    		<div style="width:1160px">
    		
    			<input type="hidden" id="hide_roll_maintain" value=""/>
    			<input type="hidden" id="company_id" value=""/>
    			<table width="1100" border="0">
    				<tr>
    					<td width="400" valign="top">
    						<table cellpadding="0" cellspacing="0" border="1" width="400" class="rpt_table" rules="all"
    						id="master_part">

							<tr bgcolor="#E9F3FF"> 
								<td width="200" class="must_entry_caption">Batch No.</td>
								<td align="center">
									<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:150px;" placeholder="Browse" maxlength="20" title="Maximum 20 Character" onDblClick="openmypage_batchnum();" onChange="check_batch();" readonly />
									<input type="hidden" name="hidden_batch_id" id="hidden_batch_id" class="text_boxes" readonly />
									
								</td>
							</tr>
							<tr bgcolor="#FFFFFF">
								<td width="200">Sales Number</td>
								<td align="center">
									<input type="text" name="txt_sales_number" id="txt_sales_number"  class="text_boxes" style="width:150px;"     />
								
								</td>
							</tr>
    						 
    							<tr bgcolor="#FFFFFF">
    								<td>Roll Number</td>
    								<td align="center">
    									<input type="text" id="txt_roll_no" name="txt_roll_no" class="text_boxes"
    									style="width:150px;"  placeholder="Display" >
    									<input type="hidden" id="roll_id" name="roll_id">
    								</td>
    							</tr>
    							<tr bgcolor="#FFFFFF">
    								<td>QC Date</td>
    								<td align="center"><input type="text" id="txt_qc_date" name="txt_qc_date"
    									class="datepicker" style="width:150px;" value="<? echo date("d-m-Y")?>"></td>
    								</tr>
    								<tr bgcolor="#E9F3FF">
    									<td>QC Name</td>
    									<td align="center"><input type="text" id="txt_qc_name" name="txt_qc_name"
    										class="text_boxes" style="width:150px;" placeholder="write">
    									</td>
    								</tr>
    								<tr bgcolor="#FFFFFF">
    									<td>Roll Width (inch)</td>
    									<td align="center"><input type="text" id="txt_roll_width" name="txt_roll_width"
    										class="text_boxes_numeric" style="width:150px;"
    										placeholder="write" onBlur="caculate_roll_length();"></td>
    									</tr>
    									<tr bgcolor="#E9F3FF">
    										<td>Roll Wgt. (Met)</td>
    										<td align="center"><input type="text" id="txt_roll_weight" name="txt_roll_weight"
    											class="text_boxes_numeric" style="width:150px;" readonly
    											placeholder="Display" value="<? echo $qnty; ?>"></td>
    										</tr>
    										<tr bgcolor="#FFFFFF">
    											<td>Roll Length (Yds)</td>
    											<td align="center" title="((roll_weight * 1000) / (gsm * roll_width * 0.0254) * 1.09361)"><input type="text" id="txt_roll_length" name="txt_roll_length"
    												class="text_boxes_numeric" style="width:150px;"
    												placeholder="Display"></td>
    											</tr>
    											<tr bgcolor="#E9F3FF">
    												<td>Reject Qty</td>
    												<td align="center"><input type="text" id="txt_reject_qnty" name="txt_reject_qnty"
    													class="text_boxes_numeric" style="width:150px;"
    													placeholder="write"></td>
    												<input type="hidden" id="hid_reject_qnty" name="hid_reject_qnty">	
    												</tr>
    												<tr bgcolor="#FFFFFF">
    													<td>Construction & Composition</td>
    													<td align="center"><input type="text" id="txt_constract_comp" name="txt_constract_comp"
    														class="text_boxes" style="width:150px;" 
    														placeholder="Display" value="<? echo $constraction_comp; ?>">
    													</td>
    												</tr>
    												 
    												<tr bgcolor="#E9F3FF">
    													<td>GSM</td>
    													<td align="center"><input type="text" id="txt_gsm" name="txt_gsm" class="text_boxes"
    														style="width:150px;"  placeholder="Display"
    														value="<? echo $gsm; ?>"></td>
    												</tr>
													<tr bgcolor="#FFFFFF">
														<td>Dia</td>
														<td align="center"><input type="text" id="txt_dia" name="txt_dia" class="text_boxes"
															style="width:150px;"  placeholder="Display"
															value="<? echo $width; ?>"></td>
													</tr>
													<tr bgcolor="#E9F3FF">
														<td>M/C Dia</td>
														<td align="center"><input type="text" id="txt_mc_dia" name="txt_mc_dia"
														class="text_boxes" style="width:150px;" 
														placeholder="Display" value="<? echo $machine_dia; ?>"></td>
													</tr>
													 
													<tr bgcolor="#FFFFFF">
														<td>Color</td>
														<td align="center"><input type="text" id="txt_color" name="txt_color" class="text_boxes"
															style="width:150px;"  placeholder="Display"
															value="<? echo $all_color; ?>"></td>
													</tr>
													 
													 
    																	 
    																	
    			</table>
    																</td>
    																<td width="50">&nbsp;</td>
    																<td width="600">
    																	<table cellpadding="0" cellspacing="0" border="1" width="600" class="rpt_table" rules="all">
    																		<tr>
    																			<td colspan="5" align="center"><input type="button" id="reset_details"
    																				class="formbuttonplasminus"
    																				value="Reset Defect Counter" style="width:200px;"
    																				onClick="fn_recet_details();"></td>
    																			</tr>
    																		</table>

    																		<table cellpadding="0" cellspacing="0" border="1" width="600" class="rpt_table" rules="all"
    																		id="dtls_part">
    																		<thead>
    																			<tr>
    																				<th width="50">SL</th>
    																				<th width="150">Defect Name</th>
    																				<th width="100">Defect Count</th>
    																				<th width="150">Found in (Inch)</th>
    																				<th>Penalty Point</th>
    																			</tr>
    																		</thead>
    																		<tbody>
    																			<?
																				$defect_array=array(170,171,172,173,174,175);
																				 
    																			$i = 1;
    																			foreach ($knit_defect_array as $defect_id => $val) {

																					if(!in_array($defect_id,$defect_array)){

    																				if ($i % 2 == 0)
    																					$bgcolor = "#E9F3FF";
    																				else
    																					$bgcolor = "#FFFFFF";

    																				?>
    																				<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
    																					<td align="center"><? echo $i; ?></td>
    																					<td title="<?=$defect_id;?>"><p><? echo $val; ?></p>
    																						<input type="hidden" id="defectId_<? echo $i; ?>" name="defectId[]" class="defectId" value="<? echo $defect_id; ?>">
    																						<input type="hidden" class="UpdefectId" id="UpdefectId_<? echo $i; ?>" name="UpdefectId[]"
    																						value="">
    																					</td>
    																					<td><p><input type="text" id="defectcount_<? echo $i; ?>" name="defectcount[]"
    																						class="text_boxes_numeric" style="width:90px"
    																						onBlur="fn_panelty_point(<? echo $i; ?>)"></p></td>
    																						<?
    																						if ($defect_id == 1) $defect_show = '5,6'; else $defect_show = '1,2,3,4';
    																						?>
    																						<td>
    																							<p><? echo create_drop_down("foundInche_" . $i, 152, $knit_defect_inchi_array, "", 1, "-- Select --", 0, "fn_panelty_point(" . $i . ")", '', $defect_show); ?></p>
    																							<input type="hidden" id="foundInchePoint_<? echo $i; ?>"
    																							name="foundInchePoint[]" value="">
    																						</td>
    																						<td><p><input type="text" id="penaltycount_<? echo $i; ?>" name="penaltycount[]"
    																							class="text_boxes_numeric" style="width:130px" readonly></p></td>
    																						</tr>
    																						<?
    																						$i++;
    																					}}
    																					?>
    																				</tbody>
    																				<tfoot>
    																					<tr bgcolor="#CCCCCC">
    																						<td colspan="4" align="right">Total Penalty Point: &nbsp;</td>
    																						<td align="center"><input type="text" class="text_boxes_numeric"
    																							id="total_penalty_point" name="total_penalty_point"
    																							style="width:130px" readonly></td>
    																						</tr>
    																						<tr bgcolor="#CCCCCC">
    																							<td colspan="4" align="right">Total Point: &nbsp;</td>
    																							<td align="center"><input type="text" class="text_boxes_numeric" id="total_point"
    																								name="total_point" style="width:130px" readonly></td>
    																							</tr>
    																							<tr bgcolor="#CCCCCC">
    																								<td colspan="4" align="right">Fabric Grade: &nbsp;</td>
    																								<td align="center"><input type="text" class="text_boxes" id="fabric_grade"
    																									name="fabric_grade" style="width:130px" readonly></td>
    																								</tr>
    																								<tr>
    																									<td>Comments</td>
    																									<td colspan="4"><input type="text" class="text_boxes" id="fabric_comments"
    																										name="fabric_comments" style="width:98%"></td>
    																									</tr>
    																								</tfoot>
    																							</table>
    																						</td>
    																					</tr>
    																					<tr>
    																						<td colspan="3">&nbsp;</td>
    																					</tr>
    																					<tr>
    																						<td colspan="3" align="center" class="button_container">
    																							<?
						echo load_submit_buttons($permission, "fnc_grey_defect_entry", 0, 1, "reset_form('','','','')", 1);//set_auto_complete(1);
						?>
						<input type="hidden" id="update_id" name="update_id"/>
					</td>
				</tr>
			</table>
			<div id="dtls_list_container" style="margin-top:5px;" align="center">
				<?
				$sql_dtls = sql_select("select id, pro_dtls_id, barcode_no, roll_id, roll_no, total_penalty_point, total_point, fabric_grade, comments from pro_qc_result_mst where pro_dtls_id=$update_dtls_id and status_active = 1");
				if (count($sql_dtls) > 0) 
				{
					?>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table">
						<thead>
							<tr>
								<th width="50">SL</th>
								<th width="100">Roll No</th>
								<th width="100">Barcode</th>
								<th width="100">Penalty Point</th>
								<th width="100">Total Point</th>
								<th width="100">Fabric Grade</th>
								<th>Comments</th>
							</tr>
						</thead>
						<tbody>
							<?
							$i = 1;
							foreach ($sql_dtls as $row) {
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>"
									onClick='get_php_form_data(<? echo $row[csf("id")]; ?>, "populate_qc_from_grey_recv", "requires/inspection_and_end_sewing_entry_controller" );'
									style="cursor:pointer;">
									<td align="center"><? echo $i; ?></td>
									<td align="center"><? echo $row[csf("roll_no")]; ?></td>
									<td align="center"><? echo $row[csf("barcode_no")]; ?></td>
									<td align="right"><? echo number_format($row[csf("total_penalty_point")], 2); ?></td>
									<td align="right"><? echo number_format($row[csf("total_point")], 2); ?></td>
									<td><? echo $row[csf("fabric_grade")]; ?></td>
									<td><? echo $row[csf("comments")]; ?></td>
								</tr>
								<?
								$i++;
							}
							?>
						</tbody>
                        <!--<tfoot>
							<tr>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
							</tr>
						</tfoot>-->
					</table>
					<?
				}
				?>
			</div>
			</div>
		</form>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	<script> 
		var barcode_no = '<?php echo trim($barcode_no); ?>';
	    if(barcode_no!="")
		{
			get_php_form_data(barcode_no, 'barcode_roll_find', 'inspection_and_end_sewing_entry_controller');
		}
	</script>
	</html>


<script>

	
 $('#txt_barcode').live('keydown', function(e) {
		if (e.keyCode === 13) 
		{
			e.preventDefault();
			 var txt_barcode= $("#txt_barcode").val();
			 if(txt_barcode){
			generate_report(1);
			 }
		}
	});
	
/*$("#txt_barcode").keyup(function(e){
  if (e.which==13) { // 13 is the code for return
  }
  else {
	 var txt_barcode= $("#txt_barcode").val();
	 if(txt_barcode){
     generate_report(1);
	 }
  }
  //e.preventDefault();
});*/
</script>
<script src="../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
