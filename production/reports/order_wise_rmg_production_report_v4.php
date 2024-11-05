<?php
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Report Will Create Order wise Production v4 Report.
Functionality	:	
JS Functions	:
Created by		:	Kamrul Hasan
Creation date 	: 	22-12-2022
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

//--------------------------------------------------------------------------------------------------------------------
// echo load_html_head_contents("Order Wise RMG Production Report V2", "../../", 1, 1,$unicode,'','');
echo load_html_head_contents("Order Wise RMG Production Report V4", "../../", 1, 1,$unicode,1,1);

?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
 	//order wise
	var tableFilters1 = 
	{
		
 	}
	
 	function fnc_load_report_format(data)
 	{
 		var report_ids = return_global_ajax_value( data, 'load_report_format', '', 'requires/order_wise_rmg_production_report_v4_controller');
  		// print_report_button_setting(report_ids);
 	}

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
		var cbo_location=$('#cbo_location').val();
		var txt_style_ref=$('#txt_style_ref').val();
		var txt_order=$('#txt_order').val();
		var cbo_floor=$('#cbo_floor').val();
		var txt_style_ref_number=$('#txt_style_ref_number').val();
		var txt_internal_ref=$('#txt_internal_ref').val();
		
		if(cbo_buyer_name>0 || cbo_location>0 || txt_style_ref!="" || txt_style_ref_number!="" || txt_order!="" || txt_internal_ref!="")
		{
			if (form_validation('cbo_company_name','Comapny Name')==false)
			{
				//alert("Please select Job No Or Order No Or Style Ref. Or Int ref.");
				release_freezing();
				return;
			}
		}
		else 
		{
			if (form_validation('cbo_company_name*cbo_floor*txt_job_no*txt_date_from*txt_date_to','Comapny Name*Floor Name*From Date*To Date')==false)
			{
				release_freezing();
				return;
			}
		}
		
		var data="action=report_generate"+"&type="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_location*cbo_floor*cbo_job_year*txt_style_ref*txt_job_no*txt_job_no_id*txt_order*txt_internal_ref*shipping_status*txt_date_from*txt_date_to',"../../");
		
		http.open("POST","requires/order_wise_rmg_production_report_v4_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("**");
			show_msg('3');
			release_freezing();
			$('#report_container2').html(response[0]);
			// document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>'+'&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
			// append_report_checkbox('table_header_1',1);

			document.getElementById("check_uncheck_tr").style.display="table";
            if($("#check_uncheck").is(":checked")==false){
                $("#check_uncheck").attr("checked","checked");

            }else{
                $("#check_uncheck").rmoveAttr("checked");
            }
						
			setFilterGrid("table_body",-1,tableFilters1);
			
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow='auto';
		document.getElementById('scroll_body').style.maxHeight='none'; 
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>'); 
		d.close();
		document.getElementById('scroll_body').style.overflowY='scroll';
		document.getElementById('scroll_body').style.maxHeight='300px';
	}

	function fn_check_uncheck(){
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

function openmypage_remark(po_break_down_id,item_id,country_id,action)
{
	var garments_nature = $("#cbo_garments_nature").val();
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_rmg_production_report_v4_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&country_id='+country_id+'&action='+action, 'Remarks Veiw', 'width=550px,height=450px,center=1,resize=0,scrolling=0','../');
}

function openmypage_color_countyr_remark(po_break_down_id,item_id,country_id,color_id,action)
{
	var garments_nature = $("#cbo_garments_nature").val();
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_rmg_production_report_v4_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&country_id='+country_id+'&color_id='+color_id+'&action='+action, 'Remarks Veiw', 'width=550px,height=450px,center=1,resize=0,scrolling=0','../');
}

function openmypage_job_color_remark(job_num,job_no,po_no,color_id,action)
{
	var garments_nature = $("#cbo_garments_nature").val();
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_rmg_production_report_v4_controller.php?job_no='+job_no+'&po_no='+po_no+'&color_id='+color_id+'&action='+action, 'Remarks Veiw', 'width=550px,height=450px,center=1,resize=0,scrolling=0','../');
}

function openmypage_order(po_break_down_id,company_name,item_id,country_id,action)
{
	//var garments_nature = $("#cbo_garments_nature").val();
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_rmg_production_report_v4_controller.php?po_break_down_id='+po_break_down_id+'&company_name='+company_name+'&item_id='+item_id+'&country_id='+country_id+'&action='+action, 'Order Quantity', 'width=750px,height=350px,center=1,resize=0,scrolling=0','../');
}

function openmypage_job_color_order(company_name,job_no,po_no,color_id,action)
{
	//var garments_nature = $("#cbo_garments_nature").val();
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_rmg_production_report_v4_controller.php?job_no='+job_no+'&po_no='+po_no+'&company_name='+company_name+'&color_id='+color_id+'&action='+action, 'Order Quantity', 'width=750px,height=350px,center=1,resize=0,scrolling=0','../');
}


function openmypage_order_country(po_break_down_id,company_name,item_id,country_id,color_id,action)
{
	//var garments_nature = $("#cbo_garments_nature").val();
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_rmg_production_report_v4_controller.php?po_break_down_id='+po_break_down_id+'&company_name='+company_name+'&item_id='+item_id+'&country_id='+country_id+'&color_id='+color_id+'&action='+action, 'Order Quantity', 'width=750px,height=350px,center=1,resize=0,scrolling=0','../');
}

function openmypage_rej(po_id,item_id,action,country_id,color_id,reportType,rpt_leb)
{
	//alert(country_id);
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_rmg_production_report_v4_controller.php?po_id='+po_id+'&item_id='+item_id+'&action='+action+'&country_id='+country_id+'&color_id='+color_id+'&reportType='+reportType+'&country_id='+country_id, 'Reject Quantity', 'width=740px,height=350px,center=1,resize=0,scrolling=0','../');
}

function openmypage_sewing_rej(po_id,item_id,action,country_id,color_id,reportType,rpt_leb)
{
	//alert(country_id);
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_rmg_production_report_v4_controller.php?po_id='+po_id+'&item_id='+item_id+'&action='+action+'&country_id='+country_id+'&color_id='+color_id+'&reportType='+reportType+'&country_id='+country_id, 'Sewing Reject Quantity', 'width=350px,height=350px,center=1,resize=0,scrolling=0','../');
}

function openmypage(po_break_down_id,item_id,action,location_id,floor_id,dateOrLocWise,country_id)
{
	if(action==2 || action==3)
		var popupWidth = "width=420px,height=350px,";
	else if (action==10)
		var popupWidth = "width=550px,height=420px,";
	else
		var popupWidth = "width=750px,height=420px,";
	
	if (action==2)
	{
		var popup_caption="Embl. Issue Details";
	}
	else if (action==3)
	{
		var popup_caption="Embl. Rec. Details";
	}
	else
	{
		var popup_caption="Production Quantity";
	}
		
	emailwindow=dhtmlmodal.open('EmailBox','iframe','requires/order_wise_rmg_production_report_v4_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&action='+action+'&location_id='+location_id+'&floor_id='+floor_id+'&dateOrLocWise='+dateOrLocWise+'&country_id='+country_id, popup_caption, popupWidth+'center=1,resize=0,scrolling=0','../');
}

function openmypage_popup(po_break_down_id,item_id,country_id,color_id,prod_popup_type,prod_popup_lelel,action)
{
	if (prod_popup_type==1)
	{
		var popup_caption="Cutting Qnty Details";
	}
	else if (prod_popup_type==2)
	{
		var popup_caption="Embl. Issue. Details";
	}
	else if (prod_popup_type==3)
	{
		var popup_caption="Embl. Rec. Details";
	}
	else if (prod_popup_type==4)
	{
		var popup_caption="Sewing Input Details";
	}
	else if (prod_popup_type==5)
	{
		var popup_caption="Sewing Output Details";
	}
	else if (prod_popup_type==7)
	{
		var popup_caption="Iron Details";
	}
	else if (prod_popup_type==8)
	{
		var popup_caption="Finish Details";
	}
	else if (prod_popup_type==11)
	{
		var popup_caption="Poly Details";
	}
	else
	{
		var popup_caption="Ex-fact Details";
	}
		
	emailwindow=dhtmlmodal.open('EmailBox','iframe','requires/order_wise_rmg_production_report_v4_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&country_id='+country_id+'&color_id='+color_id+'&prod_popup_type='+prod_popup_type+'&prod_popup_lelel='+prod_popup_lelel+'&action='+action, popup_caption, 'width=800px,height=350px,center=1,resize=0,scrolling=0','../');
}
// sent = 1; receive = 2; inhouse=1; subcon = 2
function openmypage_job_color_prod(company_name,job_no,po_no,color_id,prod_popup_type,prod_popup_lelel,action) 
{
	if (prod_popup_type==1)
	{
		var popup_caption="Cutting Qnty Details";
	}
	else if (prod_popup_type==2)
	{
		var popup_caption="Printing Issue Details";
	}
	else if (prod_popup_type==3)
	{
		var popup_caption="Printing Receive Details";
	}
	else if (prod_popup_type==4)
	{
		var popup_caption="Sewing Input Details";
	}
	else if (prod_popup_type==5)
	{
		var popup_caption="Sewing Output Details";
	}
	else if (prod_popup_type==6)
	{
		var popup_caption="Reject Details";
	}
	else if (prod_popup_type==7)
	{
		var popup_caption="Iron Details";
	}
	else if (prod_popup_type==8)
	{
		var popup_caption="Finish Details";
	}
	else if (prod_popup_type==11)
	{
		var popup_caption="Poly Details";
	}
	else
	{
		var popup_caption="Ex-fact Details";
	}
		
	emailwindow=dhtmlmodal.open('EmailBox','iframe','requires/order_wise_rmg_production_report_v4_controller.php?company_name='+company_name+'&job_no='+job_no+'&po_no='+po_no+'&color_id='+color_id+'&prod_popup_type='+prod_popup_type+'&prod_popup_lelel='+prod_popup_lelel+'&action='+action, popup_caption, 'width=800px,height=350px,center=1,resize=0,scrolling=0','../');
}

function openmypage_rej_show(po_id,item_id,action,location_id,floor_id,reportType,country_id)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_rmg_production_report_v4_controller.php?po_id='+po_id+'&item_id='+item_id+'&action='+action+'&location_id='+location_id+'&floor_id='+floor_id+'&reportType='+reportType+'&country_id='+country_id, 'Reject Quantity', 'width=510px,height=350px,center=1,resize=0,scrolling=0','../');
}

	
	
function progress_comment_popup(po_id,template_id,tna_process_type)
{
	var data="action=update_tna_progress_comment"+
							'&po_id='+"'"+po_id+"'"+
							'&template_id='+"'"+template_id+"'"+
							'&tna_process_type='+"'"+tna_process_type+"'"+
							'&permission='+"'"+permission+"'";	
							
	http.open("POST","requires/order_wise_rmg_production_report_v4_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_progress_comment_reponse;	
}

function job_progress_comment_popup(company,job_no,po_no,template_id,tna_process_type)
{
	var data="action=update_tna_progress_comment_job"+
							'&job_no='+"'"+job_no+"'"+
							'&po_no='+"'"+po_no+"'"+
							'&template_id='+"'"+template_id+"'"+
							'&tna_process_type='+"'"+tna_process_type+"'"+
							'&permission='+"'"+permission+"'";	
							
	http.open("POST","requires/order_wise_rmg_production_report_v4_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_progress_comment_reponse;	
}

function generate_progress_comment_reponse()
{
	if(http.readyState == 4) 
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
		d.close();
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
		var txt_style_ref = $("#txt_style_ref").val();
		var txt_style_ref_id = $("#txt_style_ref_id").val();
		
		var page_link='requires/order_wise_rmg_production_report_v4_controller.php?action=style_refarence_surch&company='+company+'&buyer='+buyer+'&job_year='+job_year+'&txt_style_ref_id='+txt_style_ref_id+'&txt_style_ref='+txt_style_ref;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			
			//alert(style_no);
			
			$("#txt_style_ref_id").val(style_id); 
			$("#txt_style_ref").val(style_des);
			
		}
	}
	function openmypage_job()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();
		var job_year = $("#cbo_job_year").val();
		var txt_job_no = $("#txt_job_no_id").val();
		var txt_job_no_id = $("#txt_job_no").val();
	
		var page_link='requires/order_wise_rmg_production_report_v4_controller.php?action=job_no_sreach&company='+company+'&buyer='+buyer+'&job_year='+job_year+'&txt_job_no='+txt_job_no+'&txt_job_no_id='+txt_job_no_id;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var job_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			
		
			// alert(style_no);
			$("#txt_job_no").val(job_des);
			$("#txt_job_no_id").val(job_id); 
			
		}
	}
	
	function openall_order()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();	
		var job_year = $("#cbo_job_year").val();
		var txt_style_ref_id = $("#txt_style_ref_id").val();
		var txt_order_id_no = $("#txt_order_id_no").val();
		var txt_order_id = $("#txt_order_id").val();
		var txt_order = $("#txt_order").val();
		var page_link='requires/order_wise_rmg_production_report_v4_controller.php?action=order_surch&company='+company+'&buyer='+buyer+'&job_year='+job_year+'&txt_style_ref_id='+txt_style_ref_id+'&txt_order_id_no='+txt_order_id_no+'&txt_order_id='+txt_order_id+'&txt_order='+txt_order;  
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=520px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			$("#txt_order").val(style_des);
			$("#txt_order_id").val(style_id); 
			$("#txt_order_id_no").val(style_des_no);
		}
	}
	function fnc_chng_orderNo(orderNos)
	{
		$("#txt_order_id").val("");
		$("#txt_order_id_no").val(""); 
	}
	function fnc_chng_job(orderNos)
	{
		$("#txt_job_no_id").val("");
		$("#txt_job_no").val(""); 
	}
	function fnc_chng_styleno(orderNos)
	{
		$("#txt_style_ref_id").val("");
		$("#txt_style_ref").val(""); 
	}
</script>

</head>
 
<body onLoad="set_hotkey();">

<form id="dateWiseProductionReport_1">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
         
         <h3 style="width:1450px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1450px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
               <thead>                    
                        <th class="must_entry_caption"> Working Company</th>
                        <th>Buyer Name</th>
                        <th>Location</th>
                        <th>Floor</th>
                        <th>Year</th>
                        <th>Style</th>
                        <th>Job No</th>
                        <th id="Order_td">Order No</th>
                        <th>Internal Ref. No</th>
						<th>Shiping Status</th>
                        <th class="must_entry_caption">Production Date</th>                   
                        <th align="center">
                        <input type="reset" id="reset_btn" class="formbutton" style="width:70px; " value="Reset" onClick="reset_form('dateWiseProductionReport_1','report_container*report_container2','','','')" /></th>
              </thead>
                <tbody>
                <tr class="general">
                    <td> 
                        <?
                            echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "ClearTextBoxValues();load_drop_down( 'requires/order_wise_rmg_production_report_v4_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/order_wise_rmg_production_report_v4_controller', this.value, 'load_drop_down_location', 'location_td' );fnc_load_report_format(this.value);" );
                        ?>
                    </td>
                    <input type="hidden" name="report_ids" id="report_ids" />
                    <td id="buyer_td">
                        <? 
                            echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td id="location_td">
                    	<? 
                            echo create_drop_down( "cbo_location", 120, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td id="floor_td">
                    	<? 
                            echo create_drop_down( "cbo_floor", 110, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td align="center">
					<?
                        $year_current=date("Y");
                        echo create_drop_down( "cbo_job_year", 50, $year,"", 1, "All",$year_current,'','');
                    ?>
                    </td>
					<td>
                        <input style="width:80px;"  name="txt_style_ref" id="txt_style_ref"  ondblclick="openmypage_style()" onKeyUp="fnc_chng_styleno(this.value)" class="text_boxes" placeholder="Browse "   />   
                        <input type="hidden" name="txt_style_ref_id" id="txt_style_ref_id"/>  
                    </td>
                    
                    <td align="center">
					<input style="width:80px;"  name="txt_job_no" id="txt_job_no"  ondblclick="openmypage_job()" onKeyUp="fnc_chng_job(this.value)" class="text_boxes" placeholder="Browse "   />   
                        <input type="hidden" name="txt_job_no_id" id="txt_job_no_id"/>
                    </td>
                   
                   
                    <td>
                        <input style="width:100px;"  name="txt_order" id="txt_order"  ondblclick="openall_order()" onKeyUp="fnc_chng_orderNo(this.value)"  class="text_boxes" placeholder="Browse "   />   
                        <input type="hidden" name="txt_order_id" id="txt_order_id"/> <input type="hidden" name="txt_order_id_no" id="txt_order_id_no"/>
                    </td> 
                     <td align="center">
                            <input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px" placeholder="Write" />
                    </td>
					
                    <td>
					<?
						$shipment_status_fashion=array(0=>"ALL",1=>"Full Pending",2=>"Partial Shipment",3=>"Full Shipment",4=>"Full Pending & Partial Shipment");
                   		echo create_drop_down( "shipping_status", 90, $shipment_status_fashion,"", 0, "", 0, "",0,'','','','','' );	
                    ?>
					</td>
					
                    
                   
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date" >&nbsp; To
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date"  ></td>
                    <td>
                        <input type="button" id="show_button" class="formbutton" style="width:70px; " value="Show" onClick="fn_report_generated(0)"/>    
					</td>                    
					
                </tr>
                </tbody>
            </table>
            <table>
            	<tr>
                	<td>
 						<? echo load_month_buttons(1); ?>
                     </td>
                     <td>
                     	
                     </td>
                     <td>                        
                       
                   	</td>
                </tr>
            </table>
          
        </fieldset>
    </div>
    </div>
        
    <div id="report_container" align="center" style="padding: 10px 0;"></div>
    <div id="report_container2" align="left"></div>
 </form>    
</body>
<script>
$('#cbo_location').val(0); 
$('#active_status').val(0);

set_multiselect('cbo_floor','0','0','','0'); 
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<!--<script class="include" type="text/javascript" src="../../js/chart/logic_chart.js"></script>-->
</html>