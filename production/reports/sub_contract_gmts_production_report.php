<?php
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Report Will Create Sub Contract GMTS Production Report.
Functionality	:	
JS Functions	:
Created by		:	MD MAMUN AHMED SAGOR 
Creation date 	: 	07-04-2021
Updated by 		: 		
Update date		: 		   
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sub Contract GMTS Production Report", "../../", 1, 1,$unicode,'','');

?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
 	//order wise
	
	
 
 	
 	function ClearTextBoxValues()
 	{
 		$("#cbo_buyer_name").val('');
	   // $("#cbo_job_year").val('');
		$("#txt_style_ref_no").val('');
		$("#txt_style_ref_id").val('');
		$("#txt_style_ref").val('');
		$("#txt_order_id_no").val('');
		$("#txt_order_id").val('');
		$("#txt_order").val('');
		$("#txt_internal_ref").val('');
		$("#txt_style_ref_number").val('');
		
 	}

 	
			
	function fn_report_generated(type)
	{
		freeze_window(3);
		var cbo_buyer_name=$('#cbo_buyer_name').val();	
		var txt_style_ref=$('#txt_style_ref').val();	
		var txt_style_ref_number=$('#txt_style_ref_number').val();	
		var txt_date_from=$('#txt_date_from').val();
		var txt_date_to=$('#txt_date_to').val();
	
		if(txt_style_ref!=""  || (txt_date_from!="" && txt_date_to!=""))
		{
			
			if (form_validation('cbo_company_name','Comapny Name')==false)
			{
				release_freezing();
				return;
			}
		}
		else 
		{
			if (form_validation('cbo_company_name*txt_date_from*txt_date_to','Comapny Name*From Date*To Date')==false)
			{
				release_freezing();
				return;
			}
		}
		var po_status;
		if(type==3)
		{
			var r = confirm("Press ok for all PO and press cancel for confirm PO");
			if (r == true) {
			  po_status = "all";
			} else {
			  po_status = "confirm";
			}
		}
		
		var data="action=report_generate"+"&type="+type+"&po_status="+po_status+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to*cbo_job_year*txt_style_ref*txt_style_ref_id',"../../");
		
		http.open("POST","requires/sub_contract_gmts_production_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{			
			var reponse=trim(http.responseText).split("####");
			// alert(reponse[2]);
			show_msg('3');
			release_freezing();
			$('#report_container2').html(reponse[0]);
			// document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			append_report_checkbox('table_header_1',1);

			/*document.getElementById("check_uncheck_tr").style.display="table";
            if($("#check_uncheck").is(":checked")==false){
                $("#check_uncheck").attr("checked","checked");

            }else{
                $("#check_uncheck").rmoveAttr("checked");
            }*/
			
			if(reponse[2]==5){
				document.getElementById('excel').click();
				$('#report_container2').html('');
				return;
			}
			
			
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_print.css" type="text/css"  /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="auto"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
	}	

	function fn_check_uncheck()
	{
	    var lengths = $("[type=checkbox]").length;
	    if($("#check_uncheck").is(":checked") != true){     
	        for(var i=0; i<=lengths; i++){
	            
	            $("[type=checkbox]").prop('checked', false);
	            $("[type=checkbox]").removeClass('rpt_check');
	            $("[type=checkbox]").removeAttr('checked');
	        }
	    }else{
	        $("[type=checkbox]").prop('checked', true);
	        for(var i=0; i<=lengths; i++){
	            
	            $("[type=checkbox]").not("#check_uncheck").addClass('rpt_check');
	            $("[type=checkbox]").attr('checked',"checked");
	        }
	    }    
	}

	
	
	function openmypage_style()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();
		var job_year = $("#cbo_job_year").val();
		var txt_style_ref_no = $("#txt_style_ref_no").val();
		var txt_style_ref_id = $("#txt_style_ref_id").val();
		var txt_style_ref = $("#txt_style_ref").val();
		var page_link='requires/sub_contract_gmts_production_report_controller.php?action=style_refarence_surch&company='+company+'&buyer='+buyer+'&job_year='+job_year+'&txt_style_ref_no='+txt_style_ref_no+'&txt_style_ref_id='+txt_style_ref_id+'&txt_style_ref='+txt_style_ref;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			var year=this.contentDoc.getElementById("txt_year").value; // product Description
		//	alert(year);
			$("#txt_style_ref").val(style_des);
			$("#txt_style_ref_id").val(style_id); 
			$("#txt_style_ref_no").val(style_no); 
			$("#txt_year").val(year); 
		}
	}
	

	function fnc_chng_orderNo(orderNos)
	{
		$("#txt_order_id").val("");
		$("#txt_order_id_no").val(""); 
	}
	function fnc_chng_jobNo(orderNos)
	{
		$("#txt_style_ref_id").val("");
		$("#txt_style_ref_no").val(""); 
	}
</script>

</head>
 
<body onLoad="set_hotkey();">

<form id="dateWiseProductionReport_1">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
         
         <h3 style="width:780px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:780px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
               <thead>                    
                        <th class="must_entry_caption">Company Name</th>
                        <th>Buyer Name</th>
                        <th>Year</th>
                        <th>Job No</th>
                        <th class="must_entry_caption"> Date</th>
                        <th align="center">
                        <input type="reset" id="reset_btn" class="formbutton" style="width:70px;" value="Reset" onClick="reset_form('dateWiseProductionReport_1','report_container*report_container2','','','')" /></th>
                 </thead>
                <tbody>
                <tr class="general">
                    <td> 
                        <?
                            echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "ClearTextBoxValues();load_drop_down( 'requires/sub_contract_gmts_production_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/sub_contract_gmts_production_report_controller', this.value, 'load_drop_down_location', 'location_td' );fnc_load_report_format(this.value);" );
                        ?>
                    </td>
                    <input type="hidden" name="report_ids" id="report_ids" />
                    <td id="buyer_td">
                        <? 
                            echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                        ?>
                    </td>
                 
                    <td align="center">
					<?
						
						$selected_year=date("Y");                               
                        echo create_drop_down( "cbo_job_year", 60, $year,"", 1, "--Select Year--",$selected_year,'',0);
                    ?>
                    </td>
                    <td>
                        <input style="width:80px;"  name="txt_style_ref" id="txt_style_ref"  ondblclick="openmypage_style()" onKeyUp="fnc_chng_jobNo(this.value)" class="text_boxes" placeholder="Browse or Write"   />   
                        <input type="hidden" name="txt_style_ref_id" id="txt_style_ref_id"/>    <input type="hidden" name="txt_style_ref_no" id="txt_style_ref_no"/>
						<input type="hidden" name="txt_year" id="txt_year"/>
                    </td>
                                     
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date" >&nbsp; To
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date"  ></td>
                    <td width="100px;" align="center">

					<input type="button" id="show_button4" class="formbutton" style="width:90px; float:left; display:block " value="Show" onClick="fn_report_generated(4)" />
                      
                    </td>
                </tr>
                </tbody>
            </table>
            <table>
            	<tr>
                	<td>
 						<? echo load_month_buttons(1); ?>
                     </td>
                    
                    
                </tr>
            </table>
            <table align="left">
                <tr id="check_uncheck_tr" style="display:none;">
                    <td><input type="checkbox" id="check_uncheck" name="check_uncheck" onClick="fn_check_uncheck()"/> <strong style="color:#176aaa; font-size:14px; font-weight:bold;">Check/Uncheck All</strong>
                    </td>
                </tr>
            </table>
            <br />
        </fieldset>
    </div>
    </div>
        
    <div id="report_container" align="center" style="padding: 10px;"></div>
    <div id="report_container2" align="left"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$('#cbo_location').val(0); 
$('#active_status').val(0);
</script>
<!--<script class="include" type="text/javascript" src="../../js/chart/logic_chart.js"></script>-->
</html>
