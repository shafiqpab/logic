<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Production Incentive Payment Report
				
Functionality	:	
JS Functions	:
Created by		:	Ashraful
Creation date 	: 	
Updated by 		:   		
Update date		: 	05-08-2021	   
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
echo load_html_head_contents("Line Wise Productivity Analysis","../../", 1, 1, $unicode,1,1); 
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

	/*    var cbo_get_upto = $("#cbo_get_upto").val();
		var txt_parcentage= $("#txt_parcentage").val();
		if(cbo_get_upto!=0 && txt_parcentage*1<=0)
			{
				alert("Please Insert Percentage.");	
				$("#txt_days").focus();
				return;
			}*/
		var report_title=$( "div.form_caption" ).html();
		
			var data="action=report_generate"+"&type="+type+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_floor_id*cbo_line*hidden_line_id*cbo_buyer_name*txt_date*txt_parcentage*txt_file_no*txt_ref_no*cbo_no_prod_type',"../../")+'&report_title='+report_title;
			//alert(data);
		freeze_window(3);
		http.open("POST","requires/company_wise_hourly_production_monitoring_akh_controller.php",true);
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
	
	 
	 function openmypage(company_id,order_id,subcon_order,floor_id,line_no,action,item_smv,actual_time,line_date,prod_date)
		{
			 popup_width='550px'; 

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/company_wise_hourly_production_monitoring_akh_controller.php?po_id='+order_id+'&company_id='+company_id+'&action='+action+'&floor_id='+floor_id+'&sewing_line='+line_no+'&prod_date='+prod_date+'&item_smv='+item_smv+'&subcon_order='+subcon_order+'&line_date='+line_date+'&actual_time='+actual_time, 'Detail Veiw', 'width='+popup_width+', height=270px,center=1,resize=0,scrolling=0','../');
		}
		
		function openmypage2(company_id,order_id,subcon_order,floor_id,line_no,action,item_smv,actual_time,line_date,prod_date)
		{
			 popup_width='550px'; 

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/company_wise_hourly_production_monitoring_akh_controller.php?po_id='+order_id+'&company_id='+company_id+'&action='+action+'&floor_id='+floor_id+'&sewing_line='+line_no+'&prod_date='+prod_date+'&item_smv='+item_smv+'&subcon_order='+subcon_order+'&line_date='+line_date+'&actual_time='+actual_time, 'Detail Veiw', 'width='+popup_width+', height=270px,center=1,resize=0,scrolling=0','../');
		}
	 
	function generate_style_popup(style,po_id,subcon_order,res_mst_id,floor_id,item_id,prod_reso_allo,prod_date,action,i)
	{
		 popup_width='1120px'; 
		var company_id = $("#cbo_company_id").val();	
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/company_wise_hourly_production_monitoring_akh_controller.php?po_id='+po_id+'&company_id='+company_id+'&action='+action+'&floor_id='+floor_id+'&item_id='+item_id+'&style='+style+'&prod_reso_allo='+prod_reso_allo+'&sewing_line='+res_mst_id+'&prod_date='+prod_date+'&subcon_order='+subcon_order, 'Detail Veiw', 'width='+popup_width+', height=390px,center=1,resize=0,scrolling=0','../');
	}
	 
	function generate_in_out_popup(po_id,action,floor_id,line,i,company_id,date)
	{
		
		 popup_width='1020px'; 
		//var company_id = $("#cbo_company_id").val();	
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/company_wise_hourly_production_monitoring_akh_controller.php?po_id='+po_id+'&company_id='+company_id+'&action='+action+'&type='+i+'&production_date='+date+'&floor='+floor_id+'&sewing_line='+line, 'Detail Veiw', 'width='+popup_width+', height=390px,center=1,resize=0,scrolling=0','../');
		
	}
	
	function openmypage_smv(company_id,order_id,floor_id,sewing_line,first_input_date,action,prod_date)
	{
		var  popup_width='700px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/company_wise_hourly_production_monitoring_akh_controller.php?po_id='+order_id+'&company_id='+company_id+'&action='+action+'&floor_id='+floor_id+'&sewing_line='+sewing_line+'&prod_date='+prod_date+'&first_input_date='+first_input_date, 'Detail Veiw', 'width='+popup_width+', height=370px,center=1,resize=0,scrolling=0','../');
	}
	 
	 
	function openmypage_company()
	{
		
		var company = $("#cbo_company_id").val();
			
		var page_link='requires/company_wise_hourly_production_monitoring_akh_controller.php?action=company_search_popup&company='+company; 
		
		var title="Search Company Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=310px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			
			var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#cbo_company_id").val(prodID);
			$("#cbo_company_name").val(prodDescription);
			
			$("#cbo_location_id").val('');
			$("#cbo_location_name").val('');
			
			$("#hidden_line_id").val('');
			$("#cbo_line ").val('');
			
			$("#cbo_floor_id").val('');
			$("#cbo_floor_name ").val('');
			
			load_drop_down( 'requires/company_wise_hourly_production_monitoring_akh_controller',prodID, 'load_drop_down_buyer', 'buyer_td_id' );
			 
		}
	}
	
	 
	function openmypage_location()
	{
		if( form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		var company = $("#cbo_company_id").val();
		var location = $("#cbo_location_id").val();
			
		var page_link='requires/company_wise_hourly_production_monitoring_akh_controller.php?action=location_search_popup&company='+company+'&location='+location; 
		
		var title="Search Location Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=310px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			
			var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#cbo_location_id").val(prodID);
			$("#cbo_location_name").val(prodDescription);
			
			$("#hidden_line_id").val('');
			$("#cbo_line ").val('');
			
			$("#cbo_floor_id").val('');
			$("#cbo_floor_name ").val('');
		}
	}
	
	function openmypage_floor()
	{
		if( form_validation('cbo_location_name','Location')==false)
		{
			return;
		}
		var location = $("#cbo_location_id").val();
		var floor_id = $("#cbo_floor_id").val();
		
			
		var page_link='requires/company_wise_hourly_production_monitoring_akh_controller.php?action=floor_search_popup&location='+location+'&floor='+floor_id; 
		
		var title="Search Location Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=310px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			
			var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#cbo_floor_id").val(prodID);
			$("#cbo_floor_name").val(prodDescription);
			
			$("#hidden_line_id").val('');
			$("#cbo_line ").val('');
			
		}
	}
	
		
	function openmypage_line()
	{
		if( form_validation('cbo_company_name*txt_date','Company*Production Date')==false)
		{
			return;
		}
		var company = $("#cbo_company_id").val();	
		var location=$("#cbo_location_id").val();
		var floor_id=$("#cbo_floor_id").val();
		var line_id=$("#hidden_line_id").val();
		var txt_date=$("#txt_date").val();
		var page_link='requires/company_wise_hourly_production_monitoring_akh_controller.php?action=line_search_popup&company='+company+'&location='+location+'&floor_id='+floor_id+'&txt_date='+txt_date+'&line_id='+line_id; 
		
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
	
	function show_line_remarks(company_id,order_id,floor_id,line_no,action,prod_date)
	{
		//alert(action)
		popup_width='550px'; 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/company_wise_hourly_production_monitoring_akh_controller.php?po_id='+order_id+'&company_id='+company_id+'&floor_id='+floor_id+'&sewing_line='+line_no+'&prod_date='+prod_date+'&action='+action, 'Detail Veiw','width='+popup_width+', height=270px,center=1,resize=0,scrolling=0','../');
		
	}

	function openmypage_image(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
		}
	}
	
</script>

</head>
<body onLoad="set_hotkey();">

<form id="LineWiseProductivityAnalysis_1">
    <div style="width:1020px" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
        	
        <h3 style="width:1095px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
        <div id="content_search_panel"> 
              
        <fieldset style="width:1000px;">
            <table class="rpt_table" width="1000" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="120" class="must_entry_caption">Company</th>
                    <th width="100" class="must_entry_caption">Production Date</th>
                    <th width="100">Location</th>
                    <th width="70">File No</th>
                    <th width="70">Ref. No</th>
                    <th width="80">Floor</th>
                    <th width="100">Include No Prod.Line</th>
                    <th width="80">Line No</th>
                    <th width="100">Buyer</th>
                    
                    <th width="100">Efficiency %</th>
                    <th width="80"><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('LineWiseProductivityAnalysis_1','report_container','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                       <td>
                       
                       		<input type="text" id="cbo_company_name"  name="cbo_company_name"  style="width:120px" class="text_boxes" onDblClick="openmypage_company()" placeholder="Browse Company"  readonly/>
                            <input type="hidden" id="cbo_company_id" name="cbo_company_id" />
					                           
                        </td>
                         <td>
                            <input type="text" name="txt_date" id="txt_date" class="datepicker" placeholder="Production Date" style="width:100px;" readonly/>
                        </td>
                        <td id="location_td">
                            
                            <input type="text" id="cbo_location_name"  name="cbo_location_name"  style="width:100px" class="text_boxes" onDblClick="openmypage_location()" placeholder="Browse Location"  readonly/>
                            <input type="hidden" id="cbo_location_id" name="cbo_location_id" />
                                                       
                        </td>
                         <td>
                               <input type="text" id="txt_file_no"  name="txt_file_no"  style="width:70px" class="text_boxes"  placeholder="Write"  />
                        </td>
                         <td>
                               <input type="text" id="txt_ref_no"  name="txt_ref_no"  style="width:70px" class="text_boxes"  placeholder="Write"  />
                        </td>
                        <td id="floor_td">
                        
                        	<input type="text" id="cbo_floor_name"  name="cbo_floor_name"  style="width:80px" class="text_boxes" onDblClick="openmypage_floor()" placeholder="Browse Floor"  readonly/>
                               <input type="hidden" id="cbo_floor_id" name="cbo_floor_id" />
                                                   
                        </td>
                         <td>
                            <? 
                                echo create_drop_down( "cbo_no_prod_type", 100, $yes_no,"", 0, "-- Select --", "", "" );
                            ?>                            
                        </td>
                        <td id="line_td">
                               <input type="text" id="cbo_line"  name="cbo_line"  style="width:80px" class="text_boxes" onDblClick="openmypage_line()" placeholder="Browse Line"  readonly/>
                               <input type="hidden" id="hidden_line_id" name="hidden_line_id" />
                        </td>
                        <td id="buyer_td_id"> 
                            <?
                               echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- Select Buyer --", 0, "",0 );
                            ?>
                        </td>
                        
                            <?
								//$get_upto=array(1=>"Greater Than",2=>"Less Than",3=>"Greater/Equal",4=>"Less/Equal");
                               // echo create_drop_down( "cbo_get_upto", 70, $get_upto,"", 1, "- All -", 0, "",0 );
                            ?>
                      
                        <td align="left">
                            <input type="text" id="txt_parcentage" name="txt_parcentage" class="text_boxes_numeric" style="width:80px; text-align:left" value="60" />
                        </td>
                        
                        <td>
       
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(0)" style="width:80px" class="formbutton" />
                            
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
	
</script> 

<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
<script>


</script>
</html>
