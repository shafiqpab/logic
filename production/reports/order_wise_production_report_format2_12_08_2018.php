<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Report Will Create Order wise Production Report.
Functionality	:	
JS Functions	:
Created by		:	Ashraf/Jahid 
Creation date 	: 	1-04-2013
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
echo load_html_head_contents("Order Wise Production Report", "../../", 1, 1,$unicode,'','');

?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
 	//order wise
	var tableFilters1 = 
	{
		col_51:'none',
		col_operation: { 
			id: ["td_po_qty","td_plan_cutting","td_cut_lay","td_cutting","td_cutting_reject","td_short_access_cut","td_print_issue","td_printIss_subcon","td_print_receive","td_printRec_subcon","total_emb_issue","td_embIss_subcon","total_emb_receive","td_embRec_subcon","total_sp_issue","total_sp_receive","total_sewing_input","total_sewing_out","total_subcon_sewing_input","total_subcon_sewing_out","total_sewing_input_all_qnty","total_sewing_output_all_qnty","td_wash_iss","td_wash_rec","total_iron_qnty","total_re_iron_qnty","total_poly_in_qnty","total_poly_out_qnty","total_poly_qnty","td_finish_in","td_finish_out","td_transfer_in","td_transfer_out","td_finish","td_reject","td_ex_factory","td_short"],
			//col: [10,14,15,16,17,18,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,50,52],
			col: [10,14,15,16,17,18,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,44,45,46,47,48,50,51,52],

			
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
 	}
	//country wise
	var tableFilters2 = 
	{
		col_47:'none',
		col_operation: { 
			id: ["total_order_quantity","plan_cut_qnty","total_cutting","short_excess_cut","total_print_issue_in","total_print_issue_out","total_print_receive_in","total_print_receive_out","total_emb_issue_in","total_emb_issue_out","total_emb_receive_in","total_emb_receive_out","total_sp_issue","total_sp_receive","total_sewing_input","total_sewing_out","total_subcon_sewing_input","total_subcon_sewing_out","total_sewing_input_all_qnty","total_sewing_output_all_qnty","total_wash_issue","total_wash_receive","total_iron_qnty","total_re_iron_qnty","finish_qnty_inhouse","finish_qnty_subcon","total_finish_qnty","total_rej_value_td","total_out","total_shortage"],
			//col: [11,14,15,16,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,42,43,44],
			 col: [12,15,16,17,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,43,44,45],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
 	}
	//color wise
	var tableFilters3 = 
	{
		col_46:'none',
		col_operation: { 
			id: ["total_order_quantity","total_plan_cutting","total_cutting","short_access_cut","total_print_issue_in","total_print_issue_out","total_print_receive_in","total_print_receive_out","total_emb_issue_in","total_emb_issue_out","total_emb_receive_in","total_emb_receive_out","total_sp_issue","total_sp_receive","total_sewing_input","total_sewing_out","total_subcon_sewing_input","total_subcon_sewing_out","total_sewing_input_all_qnty","total_sewing_output_all_qnty","total_wash_issue","total_wash_receive","poly_qnty_inhouse","poly_qnty_subcon","total_poly_qnty","total_iron_qnty","total_re_iron_qnty","finish_qnty_inhouse","finish_qnty_subcon","total_finish_qnty","total_rej_value_td","total_exfact","total_shortage"],
			col: [9,13,14,15,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34, 35,36,37 ,39,40,41,42,43,45,46],
			  //col: [10,14,15,16,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,42,43,44],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
 	}

 	function fnc_load_report_format(data)
 	{
 		var report_ids = return_global_ajax_value( data, 'load_report_format', '', 'requires/order_wise_production_report_format2_controller');
  		print_report_button_setting(report_ids);
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

 	function print_report_button_setting(report_ids)
 	{
 		if(trim(report_ids)=="")
		{
 			$("#show_button").show();
			$("#show_button2").show();
			$("#show_button1").show();
			$("#show_button3").show();
			$("#show_button4").show();
		}
		else
		{
			var report_id=report_ids.split(",");
			$("#show_button").hide();
			$("#show_button2").hide();
			$("#show_button1").hide();
			$("#show_button3").hide();
			$("#show_button4").hide();
			for (var k=0; k<report_id.length; k++)
			{
				if(report_id[k]==124)
				{
					$("#show_button").show();
				}
				else if(report_id[k]==125)
				{
					$("#show_button2").show();
				}
				else if(report_id[k]==126)
				{
					$("#show_button1").show();
				}
				if(report_id[k]==127)
				{
					$("#show_button3").show();
				}

				if(report_id[k]==128)
				{
					$("#show_button4").show();
				}
				
			}
		}
		

	}


			
	function fn_report_generated(type)
	{
		freeze_window(3);
		var cbo_buyer_name=$('#cbo_buyer_name').val();
		var cbo_location=$('#cbo_location').val();
		var txt_style_ref=$('#txt_style_ref').val();
		var txt_order=$('#txt_order').val();
		var txt_style_ref_number=$('#txt_style_ref_number').val();
		var txt_internal_ref=$('#txt_internal_ref').val();
		
		if(cbo_buyer_name>0 || cbo_location>0 || txt_style_ref!="" || txt_style_ref_number!="" || txt_order!="" || txt_internal_ref!="")
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
		
		var data="action=report_generate"+"&type="+type+get_submitted_data_string('cbo_garments_nature*cbo_company_name*cbo_buyer_name*cbo_location*cbo_floor*txt_date_from*txt_date_to*cbo_job_year*txt_style_ref*txt_style_ref_id*txt_order*txt_order_id*shipping_status*txt_style_ref_number*cbo_style_owner_wise_status*txt_internal_ref',"../../");
		
		http.open("POST","requires/order_wise_production_report_format2_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("**");
			show_msg('3');
			release_freezing();
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			append_report_checkbox('table_header_1',1);
			
			if(reponse[1]==5){
				document.getElementById('excel').click();
				$('#report_container2').html('');
				return;
			}
			
			
			
			if(reponse[1]==0)
			{
				setFilterGrid("table_body",-1,tableFilters1);
			}
			else if(reponse[1]==1)
			{
				setFilterGrid("table_body",-1,tableFilters2);
			}
			else
			{
				setFilterGrid("table_body",-1,tableFilters3);
			}
		}
	}

function openmypage_remark(po_break_down_id,item_id,country_id,action)
{
	var garments_nature = $("#cbo_garments_nature").val();
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_production_report_format2_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&country_id='+country_id+'&action='+action, 'Remarks Veiw', 'width=550px,height=450px,center=1,resize=0,scrolling=0','../');
}

function openmypage_color_countyr_remark(po_break_down_id,item_id,country_id,color_id,action)
{
	var garments_nature = $("#cbo_garments_nature").val();
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_production_report_format2_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&country_id='+country_id+'&color_id='+color_id+'&action='+action, 'Remarks Veiw', 'width=550px,height=450px,center=1,resize=0,scrolling=0','../');
}

function openmypage_order(po_break_down_id,company_name,item_id,country_id,action)
{
	//var garments_nature = $("#cbo_garments_nature").val();
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_production_report_format2_controller.php?po_break_down_id='+po_break_down_id+'&company_name='+company_name+'&item_id='+item_id+'&country_id='+country_id+'&action='+action, 'Order Quantity', 'width=750px,height=350px,center=1,resize=0,scrolling=0','../');
}

/*function openmypage_order_colorSize(po_break_down_id,company_name,item_id,country_id,action)
{
	//var garments_nature = $("#cbo_garments_nature").val();
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_production_report_format2_controller.php?po_break_down_id='+po_break_down_id+'&company_name='+company_name+'&item_id='+item_id+'&country_id='+country_id+'&action='+action, 'Order Quantity', 'width=750px,height=350px,center=1,resize=0,scrolling=0','../');
}
*/
function openmypage_order_country(po_break_down_id,company_name,item_id,country_id,color_id,action)
{
	//var garments_nature = $("#cbo_garments_nature").val();
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_production_report_format2_controller.php?po_break_down_id='+po_break_down_id+'&company_name='+company_name+'&item_id='+item_id+'&country_id='+country_id+'&color_id='+color_id+'&action='+action, 'Order Quantity', 'width=750px,height=350px,center=1,resize=0,scrolling=0','../');
}

function openmypage_rej(po_id,item_id,action,country_id,color_id,reportType,rpt_leb)
{
	//alert(country_id);
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_production_report_format2_controller.php?po_id='+po_id+'&item_id='+item_id+'&action='+action+'&country_id='+country_id+'&color_id='+color_id+'&reportType='+reportType+'&country_id='+country_id, 'Reject Quantity', 'width=800px,height=350px,center=1,resize=0,scrolling=0','../');
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
		
	emailwindow=dhtmlmodal.open('EmailBox','iframe','requires/order_wise_production_report_format2_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&action='+action+'&location_id='+location_id+'&floor_id='+floor_id+'&dateOrLocWise='+dateOrLocWise+'&country_id='+country_id, popup_caption, popupWidth+'center=1,resize=0,scrolling=0','../');
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
		
	emailwindow=dhtmlmodal.open('EmailBox','iframe','requires/order_wise_production_report_format2_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&country_id='+country_id+'&color_id='+color_id+'&prod_popup_type='+prod_popup_type+'&prod_popup_lelel='+prod_popup_lelel+'&action='+action, popup_caption, 'width=800px,height=350px,center=1,resize=0,scrolling=0','../');
}

function openmypage_rej_show(po_id,item_id,action,location_id,floor_id,reportType,country_id)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_production_report_format2_controller.php?po_id='+po_id+'&item_id='+item_id+'&action='+action+'&location_id='+location_id+'&floor_id='+floor_id+'&reportType='+reportType+'&country_id='+country_id, 'Reject Quantity', 'width=510px,height=350px,center=1,resize=0,scrolling=0','../');
}
	
/*function disable_order( val )
{
	$('#txt_order_no').val('');
	if(val==5)
	{
		$('#Order_td').html('Style Ref.');
	}
	else
	{
		$('#Order_td').html('Order No');
	}
}*/
	
	
function progress_comment_popup(po_id,template_id,tna_process_type)
{
	var data="action=update_tna_progress_comment"+
							'&po_id='+"'"+po_id+"'"+
							'&template_id='+"'"+template_id+"'"+
							'&tna_process_type='+"'"+tna_process_type+"'"+
							'&permission='+"'"+permission+"'";	
							
	http.open("POST","requires/order_wise_production_report_format2_controller.php",true);
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
		var txt_style_ref_no = $("#txt_style_ref_no").val();
		var txt_style_ref_id = $("#txt_style_ref_id").val();
		var txt_style_ref = $("#txt_style_ref").val();
		var page_link='requires/order_wise_production_report_format2_controller.php?action=style_refarence_surch&company='+company+'&buyer='+buyer+'&job_year='+job_year+'&txt_style_ref_no='+txt_style_ref_no+'&txt_style_ref_id='+txt_style_ref_id+'&txt_style_ref='+txt_style_ref;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			$("#txt_style_ref").val(style_des);
			$("#txt_style_ref_id").val(style_id); 
			$("#txt_style_ref_no").val(style_no); 
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
		var page_link='requires/order_wise_production_report_format2_controller.php?action=order_surch&company='+company+'&buyer='+buyer+'&job_year='+job_year+'&txt_style_ref_id='+txt_style_ref_id+'&txt_order_id_no='+txt_order_id_no+'&txt_order_id='+txt_order_id+'&txt_order='+txt_order;  
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
         
         <h3 style="width:1500px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1500px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
               <thead>                    
                        <th class="must_entry_caption">Company Name</th>
                        <th>Buyer Name</th>
                        <th>Location</th>
                        <th style="display:none;">Floor</th>
                        <th>Year</th>
                        <th>Style</th>
                        <th>Job No</th>
                        <th id="Order_td">Order No</th>
                        <th>Internal Ref. No</th>
                        <th>Ownership</th>
                        <th style="display:none;">Gmts. Nature</th>
                        <th class="must_entry_caption">Shipment Date</th>
                        <th>Shiping Status</th>
                        <th>
                        <input type="reset" id="reset_btn" class="formbutton" style="width:70px; margin-left:-210px;" value="Reset" onClick="reset_form('dateWiseProductionReport_1','report_container*report_container2','','','')" /></th>
                 </thead>
                <tbody>
                <tr class="general">
                    <td> 
                        <?
                            echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "ClearTextBoxValues();load_drop_down( 'requires/order_wise_production_report_format2_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/order_wise_production_report_format2_controller', this.value, 'load_drop_down_location', 'location_td' );fnc_load_report_format(this.value);" );
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
                    <td id="floor_td" style="display:none;">
                    	<? 
                            echo create_drop_down( "cbo_floor", 110, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td align="center">
					<?
                        $year_current=date("Y");
                        echo create_drop_down( "cbo_job_year", 60, $year,"", 1, "All",$year_current,'','');
                    ?>
                    </td>
                    
                    <td align="center">
                            <input name="txt_style_ref_number" id="txt_style_ref_number" class="text_boxes" style="width:70px" placeholder="Write" />
                    </td>
                   
                    <td>
                        <input style="width:100px;"  name="txt_style_ref" id="txt_style_ref"  ondblclick="openmypage_style()" onKeyUp="fnc_chng_jobNo(this.value)" class="text_boxes" placeholder="Browse or Write"   />   
                        <input type="hidden" name="txt_style_ref_id" id="txt_style_ref_id"/>    <input type="hidden" name="txt_style_ref_no" id="txt_style_ref_no"/>
                    </td>
                    <td>
                        <input style="width:100px;"  name="txt_order" id="txt_order"  ondblclick="openall_order()" onKeyUp="fnc_chng_orderNo(this.value)"  class="text_boxes" placeholder="Browse or Write"   />   
                        <input type="hidden" name="txt_order_id" id="txt_order_id"/> <input type="hidden" name="txt_order_id_no" id="txt_order_id_no"/>
                    </td> 
                     <td align="center">
                            <input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px" placeholder="Write" />
                    </td>
                    
                     <td>
					<?
						$style_owner_wise_report=array(1=>"Orginal Owner Ship",2=>"Transfer Owner");
                   		echo create_drop_down( "cbo_style_owner_wise_status", 110, $style_owner_wise_report,"", 1,"--Select--",1,"",0,'','','','','' );	
                    ?>
                    </td>
                    
                    <td style="display:none;">
                    	<? 
                            $arr = array(1=>"ALL",2=>"Woven",3=>"Knit");
							echo create_drop_down( "cbo_garments_nature", 70, $arr,"", 0, "-- Select --", $selected, "",0,"" );
                        ?>
                    </td>   
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date" >&nbsp; To
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date"  ></td>
                    <td>
					<?
						$shipment_status_fashion=array(0=>"ALL",1=>"Full Pending",2=>"Partial Shipment",3=>"Full Shipment",4=>"Full Pending & Partial Shipment");
                   		echo create_drop_down( "shipping_status", 110, $shipment_status_fashion,"", 0, "", 0, "",0,'','','','','' );	
                    ?>
                    </td>
                    <td width="260px;" align="center">
                        <input type="button" id="show_button" class="formbutton" style="width:80px; float:left; " value="Order Wise" onClick="fn_report_generated(0)"/>
                        <input type="button" id="show_button2" class="formbutton" style="width:80px;float:left; display:block" value="Color Wise" onClick="fn_report_generated(2)" />
                        <input type="button" id="show_button1" class="formbutton" style="width:90px; float:left; display:block " value="Country Wise" onClick="fn_report_generated(1)" />
                        
                      
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
                         <input type="button" id="show_button3" class="formbutton" style="width:90px; float:left; display:block " value="Color and Size" onClick="fn_report_generated(3)" />
                        <input type="button" id="show_button4" class="formbutton" style="width:90px; float:left; display:block " value="Order and Size" onClick="fn_report_generated(4)" />
                        <input type="button" id="show_button5" class="formbutton" style="width:90px; float:left; " value="Excel Convert" onClick="fn_report_generated(5)" />
                   	</td>
                </tr>
            </table> 
            <br />
        </fieldset>
    </div>
    </div>
        
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$('#cbo_location').val(0);
</script>
<!--<script class="include" type="text/javascript" src="../../js/chart/logic_chart.js"></script>-->
</html>
