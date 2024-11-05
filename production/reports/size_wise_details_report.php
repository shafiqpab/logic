<?
/*-------------------------------------------- Comments
Purpose			: 	
				
Functionality	:	
JS Functions	:
Created by		:	Rakib Hasan Mondal
Creation date 	: 	12-12-2023
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
echo load_html_head_contents("Day wise target vs achivement Report","../../", 1, 1, $unicode,1,1); 

$company_library=return_library_array( "SELECT id,company_name from lib_company where status_active =1 and is_deleted=0 order by company_name", "id", "company_name"  ); 
?>	
<script>
	let permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1 ) window.location.href = "../logout.php";

	function generate_report(type)
	{
		let job 	= $('#txt_job_no').val();
		let style 	= $('#txt_style_no').val();
		let order 	= $('#txt_order_no').val();
		field_name = field_msg = '';
		if (!job && !style && !order) 
		{
			field_name = '*txt_job_no';
			field_msg = '*Job Style or PO';
		}
		
		if( form_validation('cbo_company_id*txt_job_year'+field_name,'LC Company*Job Year'+field_msg )==false )
		{
			return;
		}

		let action= "report_generate";
		let data='action='+action+'&type='+type+get_submitted_data_string('cbo_company_id*wo_company_id*txt_job_year*txt_job_no*txt_style_no*txt_order_no*cbo_location_id*cbo_floor_id*hidden_line_id',"../../");
 
		freeze_window(3);
		http.open("POST","requires/size_wise_details_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			//alert (http.responseText);
			let reponse=trim(http.responseText).split("####");
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
		
		let w = window.open("Surprise", "#");
		let d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="auto"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
	}
    function getLocationId() 
	{	 
		let company_id = document.getElementById('wo_company_id').value; 

	    if(company_id !='') 
		{
	      let data="action=load_drop_down_location&choosenCompany="+company_id;
	      http.open("POST","requires/size_wise_details_report_controller.php",true);
	      http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	      http.send(data); 
	      http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              let response = trim(http.responseText);
	              $('#location_td').html(response);
	              set_multiselect('cbo_location_id','0','0','','0');
				  setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_location_id,'0');getFloorId();") ,3000)];
	          }			 
	      };
	    }     
	}
    function browseJobStyle(popupFor)
	{
		if( form_validation('cbo_company_id','LC Company')==false )
		{
			return;
		}

		var companyID = $("#cbo_company_id").val();  
		  
        let title = (popupFor == 1) ? 'Job No Search' : (popupFor == 2) ? 'Style Search': 'Order Search' ; 
		var page_link='requires/size_wise_details_report_controller.php?action=job_style_popup&companyID='+companyID+'&popupFor='+popupFor;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=700px,height=400px,center=1,resize=1,scrolling=0','../../'); 
	}
	function getFloorId() 
	{	 
	    let location_id = document.getElementById('cbo_location_id').value;

	    if(location_id !='') 
		{
	      let data="action=load_drop_down_floor&choosenLocation="+location_id;
	      http.open("POST","requires/size_wise_details_report_controller.php",true);
	      http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	      http.send(data); 
	      http.onreadystatechange = function()
		  {
	          if(http.readyState == 4) 
	          {
	              let response = trim(http.responseText);
	              $('#floor_td').html(response);
	              set_multiselect('cbo_floor_id','0','0','','0');
	          }			 
	      };
	    }         
	}
    function openmypage_line()
	{
		if( form_validation('wo_company_id*cbo_location_id*cbo_floor_id','Working Company*Location*Floor')==false)
		{
			return;
		}
		var company = $("#wo_company_id").val();	
		var location=$("#cbo_location_id").val();
		var floor_id=$("#cbo_floor_id").val();
		var page_link='requires/size_wise_details_report_controller.php?action=line_search_popup&company='+company+'&location='+location+'&floor_id='+floor_id; 
		
		var title="Search line Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=250px,height=300px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			
			var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#cbo_line").val(prodDescription);
			$("#hidden_line_id").val(prodID); 
		}
	}
</script>

</head>
<body onLoad="set_hotkey();">

<form id="sizeWiseDetailsForm">
    <div style="width:100%;" align="center">    
    
        <? 
            $width = 1500;
            echo load_freeze_divs ("../../",'');  
        ?>
         
         <h3 style="width:<?= $width+20 ?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
			<div id="content_search_panel" >      
         <fieldset style="width:<?= $width+20 ?>px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="160" class="must_entry_caption">LC Company</th>
                    <th width="160">Working Company</th>
                    <th width="160">Location</th>
					<th width="80" class="must_entry_caption">Job Year</th>
                    <th width="140" class="must_entry_caption">Job No</th>
                    <th width="140" class="must_entry_caption">Style No</th>
                    <th width="140" class="must_entry_caption">PO No</th>
                    <th width="140">Floor</th>
                    <th width="100">Line</th>
                    <th width="330">
                        <input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('sizeWiseDetailsForm','report_container','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general"> 
                        <td align="center"> 
                            <?
                                echo create_drop_down( "cbo_company_id", 150, $company_library,'', 1, "-- Select Company --", $selected, "" );
                            ?>
                        </td>
                        <td align="center" id="td_company"> 
                            <?
                                echo create_drop_down( "wo_company_id", 150, $company_library,'', 0, "-- Select Company --", $selected, "" );
                            ?>
                        </td>
                        <td align="center" id="location_td"> 
                            <?
                                echo create_drop_down( "cbo_location_id", 150, $blank_array,"","", "-- Select location --", "", 1 ); 
                            ?>
                        </td>
						<td>
							<?
								echo create_drop_down( "txt_job_year", 80, $year,"", 1, "-- Select year --", date('Y'), "","");
							?>
						</td>
                        <td>
                            <input style="width:140px;" type="text"  onDblClick="browseJobStyle(1)" class="text_boxes" autocomplete="off" placeholder="Browse/Write" name="txt_job_no" id="txt_job_no" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).ondblclick()" />
                        </td>                  
                        <td>
                            <input style="width:140px;" type="text"  onDblClick="browseJobStyle(2)" class="text_boxes" autocomplete="off" placeholder="Browse/Write" name="txt_style_no" id="txt_style_no" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).ondblclick()" />
                        </td>                   
                        <td>
                            <input style="width:140px;" type="text"  onDblClick="browseJobStyle(3)" class="text_boxes" autocomplete="off" placeholder="Browse/Write" name="txt_order_no" id="txt_order_no" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).ondblclick()" />
                        </td> 
                        <td align="center" id="floor_td"> 
                            <?
                                echo create_drop_down( "cbo_floor_id", 130, $blank_array,"","", "-- Select floor --", "", 1 ); 
                            ?>
                        </td>             
	                    <td id="line_td">
                               <input type="text" id="cbo_line"  name="cbo_line"  style="width:130px" class="text_boxes" onDblClick="openmypage_line()" placeholder="Browse"  readonly/>
                               <input type="hidden" id="hidden_line_id" name="hidden_line_id" />
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:60px" class="formbutton" />
                            <input type="button" name="search" id="search" value="Color Wise" onClick="generate_report(2)" style="width:80px" class="formbutton" />
                            <input type="button" name="search" id="search" value="Style Wise" onClick="generate_report(3)" style="width:80px" class="formbutton" />
                            <input type="button" name="search" id="search" value="Shipment Summary" onClick="generate_report(4)" style="width:120px" class="formbutton" />
                        </td>
                    </tr>
                </tbody>
            </table> 
        </fieldset>
    	</div>
    </div>
    <div id="report_container" align="center" style="padding:5px 0"></div>
    <div id="report_container2" align="left"></div>
 </form>   
</body>
<script>
    set_multiselect('wo_company_id','0','0','','0'); 
	set_multiselect('cbo_location_id','0','0','','0'); 
	set_multiselect('cbo_floor_id','0','0','','0');  
	setTimeout[($("#td_company a").attr("onclick","disappear_list(wo_company_id,'0');getLocationId();") ,3000)];
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
