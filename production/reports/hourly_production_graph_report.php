<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Hourly Production Graph Report
				
Functionality	:	
JS Functions	:
Created by		:	Reza
Creation date 	: 	30-10-2017
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
echo load_html_head_contents("Hourly Production Graph","../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(type)
	{
		if( form_validation('cbo_company_id*txt_date','Company Name*Production Date')==false )
		{
			return;
		}

		var report_title=$( "div.form_caption" ).html();
		if(type==1)
		{
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*txt_date',"../../")+'&report_title='+report_title;//*cbo_location_id*cbo_floor_id*cbo_line*hidden_line_id*cbo_buyer_name
		}
		else
		{
			var data="action=report_generate2"+get_submitted_data_string('cbo_company_id*txt_date',"../../")+'&report_title='+report_title;//cbo_location_id*cbo_floor_id*cbo_line*hidden_line_id*cbo_buyer_name*
		}
		freeze_window(3);
		http.open("POST","requires/hourly_production_graph_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			//document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			accordion_menu('accordion_h1','content_search_panel','');
			show_msg('3');
			release_freezing();
		}
	} 


	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="auto"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
	}
		
	function openmypage_line()
	{
		if( form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company = $("#cbo_company_id").val();	
		var location=$("#cbo_location_id").val();
		var floor_id=$("#cbo_floor_id").val();
		var txt_date=$("#txt_date").val();
		var page_link='requires/hourly_production_graph_report_controller.php?action=line_search_popup&company='+company+'&location='+location+'&floor_id='+floor_id+'&txt_date='+txt_date; 
		
		var title="Search line Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=310px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			
			var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#cbo_line").val(prodDescription);
			$("#hidden_line_id").val(prodID); 
		}
	}
	 
	 function openmypage(company_id,order_id,subcon_order,floor_id,line_no,action,item_smv,actual_time,line_date,prod_date)
		{
			 popup_width='550px'; 

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/hourly_production_graph_report_controller.php?po_id='+order_id+'&company_id='+company_id+'&action='+action+'&floor_id='+floor_id+'&sewing_line='+line_no+'&prod_date='+prod_date+'&item_smv='+item_smv+'&subcon_order='+subcon_order+'&line_date='+line_date+'&actual_time='+actual_time, 'Detail Veiw', 'width='+popup_width+', height=270px,center=1,resize=0,scrolling=0','../');
		}
		
		function openmypage2(company_id,order_id,subcon_order,floor_id,line_no,action,item_smv,actual_time,line_date,prod_date)
		{
			 popup_width='550px'; 

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/hourly_production_graph_report_controller.php?po_id='+order_id+'&company_id='+company_id+'&action='+action+'&floor_id='+floor_id+'&sewing_line='+line_no+'&prod_date='+prod_date+'&item_smv='+item_smv+'&subcon_order='+subcon_order+'&line_date='+line_date+'&actual_time='+actual_time, 'Detail Veiw', 'width='+popup_width+', height=270px,center=1,resize=0,scrolling=0','../');
		}
	 
	  function generate_style_popup(style,po_id,subcon_order,res_mst_id,floor_id,item_id,prod_reso_allo,prod_date,action,i)
		{
			 popup_width='1120px'; 
			var company_id = $("#cbo_company_id").val();	
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/hourly_production_graph_report_controller.php?po_id='+po_id+'&company_id='+company_id+'&action='+action+'&floor_id='+floor_id+'&item_id='+item_id+'&style='+style+'&prod_reso_allo='+prod_reso_allo+'&sewing_line='+res_mst_id+'&prod_date='+prod_date+'&subcon_order='+subcon_order, 'Detail Veiw', 'width='+popup_width+', height=390px,center=1,resize=0,scrolling=0','../');
		}
	 
	 
</script>

</head>
<body onLoad="set_hotkey();">

<form id="LineWiseProductivityAnalysis_1">
    <div style="width:100%;" align="center">    
		<? echo load_freeze_divs ("../../",'');  ?>
        <h3 style="width:370px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
        <div id="content_search_panel" >      
         <fieldset style="width:370px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="160" class="must_entry_caption">Company</th>
<!--                    <th width="150">Buyer</th>
                    <th width="160">Location</th>
                    <th width="140">Floor</th>
                    <th width="140">Line No</th>
-->                    <th class="must_entry_caption">Production Date</th>
                    <th width="110"><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('LineWiseProductivityAnalysis_1','report_container','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                       <td>
							<? 
                                echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/hourly_production_graph_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id' );load_drop_down( 'requires/hourly_production_graph_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                            ?>                            
                        </td>
<!--                        <td id="buyer_td_id"> 
                            <?
                               //echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- Select Buyer --", 0, "",0 );
                            ?>
                        </td>
                        <td id="location_td">
                            <? 
                                //echo create_drop_down( "cbo_location_id", 150, $blank_array,"", 1, "-- Select Location --", "", "" );
                            ?>                            
                        </td>
                        <td id="floor_td">
                            <? 
                                //echo create_drop_down( "cbo_floor_id", 130, $blank_array,"", 1, "-- Select Floor --", "", "" );
                            ?>                            
                        </td>
                        <td id="line_td">
                               <input type="text" id="cbo_line"  name="cbo_line"  style="width:140px" class="text_boxes" onDblClick="openmypage_line()" placeholder="Browse"  readonly/>
                               <input type="hidden" id="hidden_line_id" name="hidden_line_id" />
                        </td>
-->                         <td>
                            <input type="text" name="txt_date" id="txt_date" class="datepicker" style="width:90px;" readonly value="<? echo date('d-m-Y',time());?>"/>
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:100px" class="formbutton" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>   
</body>
<script>
	set_multiselect('cbo_floor_id','0','0','','0');
</script> 

<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
<script>
$("#cbo_location_id").val(0);

</script>
</html>