<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Marketing Position With Value Ref. no Wise Report.
Functionality	:	
JS Functions	:
Created by		:	Aziz
Creation date 	: 	09-03-2020
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
echo load_html_head_contents("Marketing Position With Value Ref. no Report","../../../", 1, 1, $unicode,1,1);
?>	
<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	function fn_report_generated(type)
	{
		//alert(type);return;
		var txt_style_ref = $("#txt_style_ref").val();
		var txt_order = $("#txt_order").val();
		
		var txt_ref_no = $("#txt_ref_no").val();
		var txt_file_no = $("#txt_file_no").val();
		
		if(txt_style_ref!="" || txt_order!="" || txt_ref_no!="" || txt_file_no!="")
		{
			if(form_validation('cbo_company_name','Company Name')==false)
			{
				return;
			}
		}
		else
		{
			
				if(txt_style_ref!="" || txt_order!="" || txt_ref_no!="" || txt_file_no!="")
				{
					if(form_validation('cbo_company_name','Company Name')==false)
					{
						return;
					}
				}
				else
				{
					if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false)
					{
						return;
					}	
				}
			
		}
		var report_title=$( "div.form_caption" ).html();	
		if(type==1)
		{
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year*txt_style_ref*txt_style_ref_id*txt_file_no*txt_ref_no*txt_order*txt_order_id*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title+'&type='+type;
		}
		
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/marketing_position_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			release_freezing();
			var reponse=trim(http.responseText).split("**");
			//alert(reponse[3]);
			//console.log(reponse);
			$('#report_container2').html(reponse[0]);
			//var tot_rows=$('#table_body tr').length;
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../');
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+reponse[3]+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
			/*document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';*/
			/*if(tot_rows>1)
			{*/
			//alert(reponse[2]+'='+reponse[3]);
			
			
			
				//setFilterGrid("table_body4",-1,tableFilters4);	
				
			 
	 		show_msg('3');
		}
	}
	
	function new_window(type)
	{
		
		//alert(type);
		
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		if(type!=3)
		{
			$('#table_body tr:first').hide();
			$('#table_body2 tr:first').hide();
		}
		else
		{
			$('#table_body3 tr:first').hide();
		}
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		if(type!=3)
		{
			$('#table_body tr:first').show();
			$('#table_body2 tr:first').show();
		}
		else
		{
			$('#table_body3 tr:first').hide();
		}
		
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
	}
	function new_window2()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		$('#table_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		$('#table_body tr:first').show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
	}
	
	function openmypage_style()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();
		var cbo_year = $("#cbo_year").val();
		var txt_style_ref_no = $("#txt_style_ref_no").val();
		var txt_style_ref_id = $("#txt_style_ref_id").val();
		var txt_style_ref = $("#txt_style_ref").val();
		var page_link='requires/marketing_position_report_controller.php?action=style_refarence_surch&companyID='+company+'&buyer_name='+buyer+'&txt_style_ref_no='+txt_style_ref_no+'&txt_style_ref_id='+txt_style_ref_id+'&txt_style_ref='+txt_style_ref+'&cbo_year_id='+cbo_year;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=370px,center=1,resize=0,scrolling=0','../../')
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
	
	function openmypage_order(type_id)
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();	
		var txt_style_ref = $("#txt_style_ref").val();
		var cbo_year = $("#cbo_year").val();
		var txt_order_id_no = $("#txt_order_id_no").val();
		var txt_order_id = $("#txt_order_id").val();
		var txt_order = $("#txt_order").val();
		var page_link='requires/marketing_position_report_controller.php?action=order_surch&company='+company+'&buyer_name='+buyer+'&txt_order_id_no='+txt_order_id_no+'&txt_order_id='+txt_order_id+'&txt_order='+txt_order+'&txt_style_ref='+txt_style_ref+'&cbo_year='+cbo_year+'&type_id='+type_id; 
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=700px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			//alert(type_id);
			if(type_id==1)
			{
			$("#txt_order").val(style_des);
			$("#txt_order_id").val(style_id); 
			$("#txt_order_id_no").val(style_des_no);
			}
			else
			{
				$("#txt_ref_no").val(style_des); 
			}
		}
	}

	function fn_order_disable(type_id)
	{
		if(type_id==2)
		{
			$('#txt_order').attr("disabled",true);
		}
		else
		{
			$('#txt_order').attr("disabled",false);
		}
	}
	function fn_date_chack(str)
	{
		if(str==1)
		{
			var ship_date=$('#txt_date_from').val();
			if(ship_date!="")
			{
				$('#txt_ex_date_form').val("");
				$('#txt_ex_date_to').val("");
			}
		}
		else
		{
			var ex_fact_date=$('#txt_ex_date_form').val();
			if(ex_fact_date!="")
			{
				$('#txt_date_from').val("");
				$('#txt_date_to').val("");
			}
		}
	}
	
	function open_trims_dtls(po_break_down_id,tot_po_qnty,ratio,page_title,action)
	{
		//alert(po_break_down_id);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/marketing_position_report_controller.php?po_break_down_id='+po_break_down_id+'&tot_po_qnty='+tot_po_qnty+'&ratio='+ratio+'&action='+action, page_title, 'width=670px,height=400px,center=1,resize=0,scrolling=0','../../');
	}
	function generate_ex_factory_popup(action,job,id,width)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/marketing_position_report_controller.php?action='+action+'&job='+job+'&id='+id, 'Ex-Factory Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}
	
	function openmypage_rej(po_id,company,action,reportType)
	{
		//alert(country_id);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/marketing_position_report_controller.php?po_id='+po_id+'&company='+company+'&action='+action+'&reportType='+reportType, 'Reject Quantity', 'width=600px,height=350px,center=1,resize=0,scrolling=0','../../');
	}	
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
        <form id="orderStatusReport" name="orderStatusReport">
			<? echo load_freeze_divs ("../../../"); ?>
            <h3 align="left" id="accordion_h1" style="width:1130px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:1170px;">
                <table class="rpt_table" width="1170" cellpadding="0" cellspacing="0" align="center" rules="all">
                    <thead>
                        <th class="must_entry_caption" width="130">Company Name</th>
                        <th width="130">Buyer Name</th>
                       
                        <th width="50">Job Year</th>
                        <th  width="100">Job No</th>
                        <th  width="120">Order No</th>
                        <th  width="80">File No</th>
                         <th  width="80">Ref. No.</th>

                        <th width="160" class="must_entry_caption">Shipment Date</th>
                       
                        <th><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:50px" onClick="reset_form('orderStatusReport','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tr class="general">                   
                        <td> 
							<?
                            echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/marketing_position_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?></td>
                       
                        <td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --",0 , "",0,"" );//date("Y",time()) ?>	</td>
                        <td align="center">
                            <input style="width:100px;" name="txt_style_ref" id="txt_style_ref" onDblClick="openmypage_style()" class="text_boxes" placeholder="Wr/Br"/>
                            <input type="hidden" name="txt_style_ref_id" id="txt_style_ref_id"/>    <input type="hidden" name="txt_style_ref_no" id="txt_style_ref_no"/>            
                        </td>
                        <td align="center">
                            <input style="width:120px;" name="txt_order" id="txt_order" onDblClick="openmypage_order(1)" class="text_boxes" placeholder="Wr/Br" />   
                            <input type="hidden" name="txt_order_id" id="txt_order_id"/> <input type="hidden" name="txt_order_id_no" id="txt_order_id_no"/>               
                        </td>
                       
                        <td align="center">
                            <input style="width:80px;" name="txt_file_no" id="txt_file_no"  class="text_boxes" placeholder="Write" />   
                                        
                        </td>
                         <td align="center">
                            <input style="width:80px;" name="txt_ref_no" id="txt_ref_no"  class="text_boxes" placeholder="Write" />   
                                        
                        </td>
                        <td>
                            <input type="text" id="txt_date_from" name="txt_date_from" class="datepicker" style="width:60px" onChange="fn_date_chack(1)" readonly>To
                            <input type="text" id="txt_date_to" name="txt_date_to" class="datepicker" style="width:60px" readonly>
                        </td>
                        
                        <td>
                        	<input type="button" id="show_button" class="formbutton" style="width:43px" value="Show" onClick="fn_report_generated(1)" />
                            
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="9" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                    </tr>
                </table>
            </fieldset>
            </div>
        </form>
    </div> 
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>  
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
