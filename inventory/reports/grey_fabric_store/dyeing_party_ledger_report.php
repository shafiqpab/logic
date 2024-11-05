<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Dyeing Party Wise Report
				
Functionality	:	
JS Functions	:
Created by		:	Aziz
Creation date 	: 	12-12-2017
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
echo load_html_head_contents("Dyeing Party Wise Report","../../../", 1, 1, $unicode,0,0); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(type)
	{
		
			if( form_validation('cbo_company_name','Company Name')==false)
			{
				return;
			}	
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_dyeing_source*txt_dyeing_com_id*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title+'&type='+type;
			//alert(data);
		freeze_window(3);
		http.open("POST","requires/dyeing_party_ledger_report_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body",-1);
			
			show_msg('3');
			release_freezing();
		}
	} 



	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflow="scroll"; 
		document.getElementById('scroll_body').style.maxHeight="350px";
		$("#table_body tr:first").show();
	
	}

	
	function openmypage_party()
	{
		if( form_validation('cbo_company_name*cbo_dyeing_source','Company Name*Source')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var cbo_dyeing_source = $("#cbo_dyeing_source").val();
		var txt_knit_comp_id = $("#txt_knit_comp_id").val();
		var page_link='requires/dyeing_party_ledger_report_controller.php?action=party_popup&companyID='+companyID+'&cbo_dyeing_source='+cbo_dyeing_source+'&txt_knit_comp_id='+txt_knit_comp_id;
		var title='Party Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=430px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var party_name=this.contentDoc.getElementById("hide_party_name").value;
			var party_id=this.contentDoc.getElementById("hide_party_id").value;
			
			$('#txt_dyeing_company').val(party_name);
			$('#txt_dyeing_com_id').val(party_id);	 
		}
	}
	
	
	
	function dyeing_company_val()
	{
		$('#txt_dyeing_company').val('');
		$('#txt_dyeing_com_id').val('');	 
	}
	
	
	function openmypage_delivery(party_id,knit_source,from_date,to_date,popup_width,action,type)
	{ //alert(prod_id)
		var companyID = $("#cbo_company_name").val();
		var popup_width=popup_width;
		//alert(popup_width);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/dyeing_party_ledger_report_controller.php?companyID='+companyID+'&party_id='+party_id+'&knit_source='+knit_source+'&from_date='+from_date+'&to_date='+to_date+'&action='+action+'&popup_width='+popup_width, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	function openmypage_grey_fab_recv(po_id,knit_source,style,colors,prod_id,deter_id,knitting_company,action)
	{ //alert(prod_id)
		var companyID = $("#cbo_company_name").val();
		var popup_width='700px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/dyeing_party_ledger_report_controller.php?companyID='+companyID+'&po_id='+po_id+'&knit_source='+knit_source+'&style='+style+'&colors='+colors+'&prod_id='+prod_id+'&action='+action+'&deter_id='+deter_id+'&knitting_company='+knitting_company, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
</script>
<style>
 /* The Modal (background) */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

 /* Modal Header */
.modal-header {
    padding: 2px 16px;
    background-color: #999;
    color: white;
}

/* Modal Body */
.modal-body {padding: 2px 16px;}

/* Modal Footer */
.modal-footer {
    padding: 2px 16px;
    background-color: #999;
    color: white;
}

/* Modal Content */
.modal-content {
    position: relative;
    background-color: #fefefe;
    margin: auto;
    padding: 0;
    border: 1px solid #888;
    width: 80%;
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
    -webkit-animation-name: animatetop;
    -webkit-animation-duration: 0.4s;
    animation-name: animatetop;
    animation-duration: 0.4s
}

/* Add Animation */
@-webkit-keyframes animatetop {
    from {top: 300px; opacity: 0}
    to {top: 0; opacity: 1}
}

@keyframes animatetop {
    from {top: 300px; opacity: 0}
    to {top: 0; opacity: 1}
}

/* The Close Button */
.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
</style> 
</head>

<body onLoad="set_hotkey();">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../../",$permission); ?><br />    		 
    <form name="PartyWiseGreyReconciliation_1" id="PartyWiseGreyReconciliation_1" autocomplete="off" > 
        <div style="width:100%;" align="center">
            <fieldset style="width:750px;">
            <legend>Search Panel</legend> 
                <table class="rpt_table" width="750" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="130" class="must_entry_caption">Company</th>                                
                            <th width="120">Source</th>
                            <th width="130">Dyeing Company</th>
                            <th  width="180" id="search_by_th_up2">Date</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('PartyWiseGreyReconciliation_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr align="center">
                        <td>
                            <? 
                               echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                            ?>                            
                        </td>
                        <td>
							<?
                                echo create_drop_down("cbo_dyeing_source",120,$knitting_source,"", 0, "-- Select Source --", 3,"dyeing_company_val();",0,'1,3');
                            ?>
                        </td>
                        <td id="dyeing_com">
                            <input type="text" id="txt_dyeing_company" name="txt_dyeing_company" class="text_boxes" style="width:130px" onDblClick="openmypage_party();" placeholder="Browse Party" />           <input type="hidden" id="txt_dyeing_com_id" name="txt_dyeing_com_id" class="text_boxes" style="width:60px" />
                        </td>
                        
                        <td align="center">
                        		<? //echo date("01-m-Y"); ?>
                             <input type="text" name="txt_date_from" id="txt_date_from"   class="datepicker" style="width:60px" placeholder="From Date"/>
                             To
                             <input type="text" name="txt_date_to" id="txt_date_to"   class="datepicker" style="width:60px" placeholder="To Date"/>
                        </td>
                        <td>
                       	 <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:100px" class="formbutton" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="10" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table>
              
            </fieldset>  
            <br>
            <div id="report_container" align="center"></div>
        	<div id="report_container2"></div>
        </div>
    </form>
    <br>
  <input type="button" id="myBtn" value="OPen" style="display:none"/>
    <div id="myModal" class="modal">
  <div class="modal-content">
  <div class="modal-header">
    <span class="close">×</span>
    <h2 id="td_title"></h2>
  </div>
  <div class="modal-body">
    <p id="ccc">Some text in the Modal Body</p>
   
  </div>
  <div class="modal-footer">
    <h3></h3>
  </div>
</div>

</div>
<script>
//============modal=========
var modal = document.getElementById('myModal');

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on the button, open the modal
btn.onclick = function() {
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

function setdata_po(data,title_name){
	
	document.getElementById('ccc').innerHTML=data;
	document.getElementById('td_title').innerHTML=title_name;
	document.getElementById('myBtn').click();
}
</script>     
</div>    
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>

<?php
/* 
|	QC Comments :

|	Form Page:
|	Multiselect is not use in this file but unnecessarily loaded multiselect sources   --- QC
|	Need to indent code using hard tabs  --- Programmar
|	Need to customize design of Dyeing Company popup --- QC
|	Need to customize design of F.Booking popup  --- --- QC
|	Need to same design of all popup   --- QC
|	Need to edit form caption F.Booking  ----- 
|	Need to edit form caption Trans. Date to Transaction Data since space is available
|	Need to edit report_type[2] values

|	Controller Page:
|	Need to indent code using hard tabs
|	Need to full form of the report title
|	Need to all report data v-align middle
|	Need to customize report when I click on the show button without input transaction date in report header show From To 
|	Need to show appropriate message when no data found in any search criteria
|	Modal window is not use in Dyeing Company popup but unnecessarily loaded all modal sources
|	Modal window is not use in Job No popup but unnecessarily loaded all modal sources 
|	Modal window is not use in F.Booking popup but unnecessarily loaded all modal sources

*/
?>
