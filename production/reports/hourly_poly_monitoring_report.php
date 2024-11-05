<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Hourly Poly Monitoring Report
				
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	05-12-2016
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
echo load_html_head_contents("Hourly Poly Monitoring Report","../../", 1, 1, $unicode,1,1); 
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
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_floor_id*cbo_line*hidden_line_id*cbo_buyer_name*txt_date',"../../")+'&report_title='+report_title+'&type='+type;
		freeze_window(3);
		http.open("POST","requires/hourly_poly_monitoring_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			//alert (http.responseText);
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			//alert(reponse[1]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		document.getElementById('scroll_body_1').style.overflow="auto";
		document.getElementById('scroll_body_2').style.overflow="auto";
		document.getElementById('scroll_body_1').style.maxHeight="none"; 
		document.getElementById('scroll_body_2').style.maxHeight="none"; 
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_print.css" type="text/css"  /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body_1').style.overflowY="scroll"; 
		document.getElementById('scroll_body_2').style.overflowY="scroll"; 
		document.getElementById('scroll_body_1').style.maxHeight="400px";
		document.getElementById('scroll_body_2').style.maxHeight="400px";
	}
		
	function fnc_openmypage_line()
	{
		if( form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company = $("#cbo_company_id").val();	
		var location=$("#cbo_location_id").val();
		var floor_id=$("#cbo_floor_id").val();
		var txt_date=$("#txt_date").val();
		var page_link='requires/hourly_poly_monitoring_report_controller.php?action=line_popup&company='+company+'&location='+location+'&floor_id='+floor_id+'&txt_date='+txt_date; 
		
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
	 
	function openmypage(company_id,order_id,floor_id,line_no,action,item_smv,prod_date,pro_type,item_id)
	{
		 popup_width='650px'; 

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/hourly_poly_monitoring_report_controller.php?po_id='+order_id+'&company_id='+company_id+'&action='+action+'&floor_id='+floor_id+'&sewing_line='+line_no+'&prod_date='+prod_date+'&item_smv='+item_smv+'&pro_type='+pro_type+'&item_id='+item_id, 'Detail Veiw', 'width='+popup_width+', height=270px,center=1,resize=0,scrolling=0','../');
	}

	function openmypage2(company_id,order_id,floor_id,line_no,action,item_smv,color_type_id,prod_date,pro_type,item_id)
	{
		 popup_width='750px'; 

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/hourly_poly_monitoring_report_controller.php?po_id='+order_id+'&company_id='+company_id+'&action='+action+'&floor_id='+floor_id+'&sewing_line='+line_no+'&prod_date='+prod_date+'&item_smv='+item_smv+'&color_type_id='+color_type_id+'&pro_type='+pro_type+'&item_id='+item_id, 'Detail Veiw', 'width='+popup_width+', height=270px,center=1,resize=0,scrolling=0','../');
	}

	function openmypage3(company_id,order_id,floor_id,line_no,action,item_smv,prod_date,pro_type,item_id)
	{
		
		 popup_width='650px'; 

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/hourly_poly_monitoring_report_controller.php?po_id='+order_id+'&company_id='+company_id+'&action='+action+'&floor_id='+floor_id+'&sewing_line='+line_no+'&prod_date='+prod_date+'&item_smv='+item_smv+'&pro_type='+pro_type+'&item_id='+item_id, 'Detail Veiw', 'width='+popup_width+', height=270px,center=1,resize=0,scrolling=0','../');
	}
	 
    
	function refresh_line()
	{
		$("#cbo_line").val('');
		$("#hidden_line_id").val('');
	}
	 
</script>
</head>
<body onLoad="set_hotkey();">
<form id="hourlyPolyMonitoring_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1030px" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1030px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="170" class="must_entry_caption">Company</th>
                    <th class="must_entry_caption">Production Date</th>
                    <th width="140">Location</th>
                    <th width="120">Floor</th>
                    <th width="120">Line No</th>
                    <th width="130">Buyer</th>
                    <th width="200"><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('hourlyPolyMonitoring_1','report_container','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                       <td>
							<? 
                                echo create_drop_down( "cbo_company_id", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/hourly_poly_monitoring_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id' );load_drop_down( 'requires/hourly_poly_monitoring_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                            ?>                            
                        </td>
                         <td>
                            <input type="text" name="txt_date" id="txt_date" class="datepicker" style="width:65px;" readonly value="<? echo date('d-m-Y'); ?>" />
                        </td>
                        <td id="location_td">
                            <? echo create_drop_down( "cbo_location_id", 140, $blank_array,"", 1, "-- Select Location --", "", "" ); ?>                            
                        </td>
                        <td id="floor_td">
                            <?  echo create_drop_down( "cbo_floor_id", 120, $blank_array,"", 1, "-- Select Floor --", "", "" ); ?>                            
                        </td>
                        <td id="line_td"><input type="text" id="cbo_line"  name="cbo_line"  style="width:100px" class="text_boxes" onDblClick="fnc_openmypage_line()" placeholder="Browse"  readonly/><input type="hidden" id="hidden_line_id" name="hidden_line_id" />
                        </td>
                        <td id="buyer_td_id"> 
                            <? echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- Select Buyer --", 0, "",0 ); ?>
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Poly Output" onClick="generate_report(1)" style="width:75px" class="formbutton" />
                            <input type="button" name="search" id="search" value="Sewing Output" onClick="generate_report(2)" style="width:85px" class="formbutton" />
							<input type="button" name="search" id="search" value="Hangtag Output" onClick="generate_report(3)" style="width:90px" class="formbutton" />
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
	set_multiselect('cbo_company_id','0','0','0','0');
  	$("#multiselect_dropdown_table_headercbo_company_id a").click(function(){
		load_buyer_location();
 	});
	function load_buyer_location()
	{
		var company=$("#cbo_company_id").val();
 		load_drop_down( 'requires/hourly_poly_monitoring_report_controller',company, 'load_drop_down_buyer', 'buyer_td_id' );
		load_drop_down( 'requires/hourly_poly_monitoring_report_controller', company, 'load_drop_down_location', 'location_td' )
	}
</script>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
