<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Grey Fabric Issue Status Report
				
Functionality	:	
JS Functions	:
Created by		:	Aziz
Creation date 	: 	26-11-2014
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents(" Style Wise Trims Received Issue and Stock Report","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	var tableFilters = 
	{
		col_30: "none",
		col_operation: {
		id: ["tot_qnty"],
		col: [6],
		operation: ["sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
	
	function openmypage_style()
	{		
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
			var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_trims_receive_issue_stock_urmi_controller.php?data='+data+'&action=style_popup', 'style Search', 'width=480px,height=420px,center=1,resize=0,scrolling=0','../../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("txt_po_id");
				var theemailval=this.contentDoc.getElementById("txt_po_val");
				if (theemailid.value!="" || theemailval.value!="")
				{
					//alert (theemailid.value);
					freeze_window(5);
					$("#txt_style").val(theemailid.value);
					$("#txt_style_id").val(theemailval.value);
					release_freezing();
				}
			}
	}
	
	function openmypage_order()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		
		var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val()+"_"+$("#txt_style").val()+"_"+$("#cbo_year_selection").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_trims_receive_issue_stock_urmi_controller.php?data='+data+'&action=order_no_popup', 'Order No Search', 'width=450px,height=420px,center=1,resize=0,scrolling=0','../../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("txt_po_id");
				var theemailval=this.contentDoc.getElementById("txt_po_val");
				if (theemailid.value!="" || theemailval.value!="")
				{
					//alert (theemailid.value);
					freeze_window(5);
					$("#txt_order_no_id").val(theemailid.value);
					$("#txt_order_no").val(theemailval.value);
					release_freezing();
				}
			}
	}	
	
	function fn_report_generated(operation)
	{
		var style=document.getElementById('txt_style').value;	
		var order_no=document.getElementById('txt_order_no').value;
		var txt_file_no=document.getElementById('txt_file_no').value;
		var txt_ref_no=document.getElementById('txt_ref_no').value;
	
		if(style!="" || order_no!="" || txt_file_no!="" || txt_ref_no!="")
		{
			if(form_validation('cbo_company_id','Company')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_id*txt_date_from*txt_date_to','Company*From date Fill*To date Fill')==false)
			{
				return;
			}
		}
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate_style"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*txt_style*txt_style_id*txt_order_no*txt_order_no_id*txt_date_from*txt_date_to*txt_file_no*txt_ref_no',"../../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/style_wise_trims_receive_issue_stock_urmi_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;  

	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("**");
			//alert (reponse[0]);
			$("#report_container2").html(reponse[0]);
			//$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
			setFilterGrid("tbl_issue_status",-1);
	 		show_msg('3');
			release_freezing();
			
		}
	}
	
	function fn_report_generated_color_size(operation)
	{
		var style=document.getElementById('txt_style').value;	
		var order_no=document.getElementById('txt_order_no').value;
	
		/*if(style!="" || order_no!="")
		{
			if(form_validation('cbo_company_id','Company')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_id*txt_date_from*txt_date_to','Company*From date Fill*To date Fill')==false)
			{
				return;
			}
		}*/
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate_style_color_size"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*txt_style*txt_style_id*txt_order_no*txt_order_no_id*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/style_wise_trims_receive_issue_stock_urmi_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_color_size_reponse;  

	}
	
	function fn_report_generated_color_size_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("**");
			//alert (reponse[0]);
			$("#report_container2").html(reponse[0]);
			//$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
			//setFilterGrid("tbl_issue_status",-1);
	 		show_msg('3');
			release_freezing();
			
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#scroll_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#scroll_body tr:first').show();
		//document.getElementById('scroll_body').style.maxWidth="120px";
	}
	
	
/*	function openmypage(po_id,item_group,action)
	{ //alert(prod_id)
		var companyID = $("#cbo_company_id").val();
		var popup_width='500px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_trims_receive_issue_stock_urmi_controller.php?companyID='+companyID+'&po_id='+po_id+'&item_group='+item_group+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=250px,center=1,resize=0,scrolling=0','../../');
	}*/
	function openmypage_des(po_id,item_group,des_prod,action)
	{ //alert(des_prod)
		var companyID = $("#cbo_company_id").val();
		var popup_width='500px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_trims_receive_issue_stock_urmi_controller.php?companyID='+companyID+'&po_id='+po_id+'&item_group='+item_group+'&action='+action+'&des_prod='+des_prod, 'Details Veiw', 'width='+popup_width+', height=250px,center=1,resize=0,scrolling=0','../../');
	}
	function search_populate(str)
		{
			
			if(str==1)
			{
				document.getElementById('search_by_th_up').innerHTML="Shipment Date";
			
			}
			else if(str==2)
			{
				document.getElementById('search_by_th_up').innerHTML="Transaction Date";
			
			}
			
		}
		
	function openmypage_color_size(po_id,item_group,itemcolor,item_size,action)
	{ //alert(prod_id)
		var companyID = $("#cbo_company_id").val();
		var popup_width='500px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_trims_receive_issue_stock_urmi_controller.php?companyID='+companyID+'&po_id='+po_id+'&item_group='+item_group+'&itemcolor='+itemcolor+'&item_size='+item_size+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=250px,center=1,resize=0,scrolling=0','../../');
	}
	function openmypage_color_size_issue(po_id,item_group,itemcolor,item_size,action)
	{ //alert(prod_id)
		var companyID = $("#cbo_company_id").val();
		var popup_width='500px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_trims_receive_issue_stock_urmi_controller.php?companyID='+companyID+'&po_id='+po_id+'&item_group='+item_group+'&itemcolor='+itemcolor+'&item_size='+item_size+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=250px,center=1,resize=0,scrolling=0','../../');
	}
function openmypage(po_id,item_group,item_color,gmts_color,item_size,recv_basis,without_order,type,action)
	{  //alert(type)
		var companyID = $("#cbo_company_id").val();
		if(type==2) var popup_width='750px';else var popup_width='700px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_trims_receive_issue_stock_urmi_controller.php?companyID='+companyID+'&po_id='+po_id+'&item_group='+item_group+'&item_color='+item_color+'&gmts_color='+gmts_color+'&item_size='+item_size+'&recv_basis='+recv_basis+'&without_order='+without_order+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
	}
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?>    		 
        <form name="greyissuestatus_1" id="greyissuestatus_1" autocomplete="off" > 
         <h3 style="width:1000px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:1000px" >      
            <fieldset>  
                <table class="rpt_table" width="1000" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <th class="must_entry_caption">Company</th>
                        <th>Buyer</th>
                        <th>Style</th>
                        <th>File No</th>
                        <th>Order No.</th>
                        <th>Ref No</th>
                        <th align="center" class="must_entry_caption">Shipment Date</th>
                       <th><input type="reset" name="res" id="res" value="Reset" style="width:60px" onClick="$('#txt_style').val('');$('#txt_order_no_id').val('');" class="formbutton" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td>
                                <? 
                                    echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/style_wise_trims_receive_issue_stock_urmi_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                                ?>                            
                            </td>
                            <td id="buyer_td">
								<?
									echo create_drop_down( "cbo_buyer_id", 150,$blank_array,"", 1, "-- Select Buyer--", $selected, "","","","","","");
                                ?> 
                          	</td>
                            <td>
                            	<input style="width:120px;" name="txt_style_id" id="txt_style_id" class="text_boxes" onDblClick="openmypage_style()" placeholder="Browse Style" readonly />
                                <input type="hidden" name="txt_style" id="txt_style" style="width:90px;"/>
                            </td>
                            <td >
                            	<input style="width:60px;" name="txt_file_no" id="txt_file_no" class="text_boxes" placeholder="Write"  />
                             </td>
                            <td >
                            	<input style="width:120px;" name="txt_order_no" id="txt_order_no" class="text_boxes" onDblClick="openmypage_order()" onChange="$('#txt_order_no_id').val('');" placeholder="Browse/Write Order"  />
                                <input type="hidden" name="txt_order_no_id" id="txt_order_no_id" style="width:90px;"/>
                            </td>
                            <td >
                            	<input style="width:60px;" name="txt_ref_no" id="txt_ref_no" class="text_boxes" placeholder="Write"  />
                             </td>
                            
                            <td>
                                <input type="text" name="txt_date_from" id="txt_date_from" value="<? //echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:50px;" />
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" value="<? //echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:50px;"/>
                            </td>
                            <td>
                                <input type="button" name="search" id="search" value="Show" onClick="fn_report_generated(3)" style="width:60px" class="formbutton" />
                            </td>
                             
                           
                        </tr>
                       
                    </tbody>
                    <tfoot>
                    
                        <tr>
                            <td colspan="8" align="center"><? echo load_month_buttons(1);  ?>
                            <input type="button" name="search" id="search" value="Item Color Size" onClick="fn_report_generated_color_size(4)" style="width:100px" class="formbutton" />
                            </td>
                            
                        </tr>
                    </tfoot>
                </table> 
            </fieldset> 
            </div>
          		
                <div id="report_container" align="center"></div>
                <div id="report_container2"></div> 
              
        </form>    
    </div>
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>