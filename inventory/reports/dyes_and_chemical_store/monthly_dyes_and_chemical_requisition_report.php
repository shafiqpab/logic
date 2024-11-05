<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Monthly Dyes And Chemical Requisition Report
				
Functionality	:	
JS Functions	:
Created by		:   Nayem
Creation date 	:   07-01-2021
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
echo load_html_head_contents("Monthly Dyes And Chemical Requisition Report","../../../", 1, 1, $unicode,1,1); 


?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function openmypage_item_account()
	{
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_item_category').value+"_"+document.getElementById('txt_item_group_id').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/monthly_dyes_and_chemical_requisition_report_controller.php?action=item_account_popup&data='+data,'Item Account Popup', 'width=800px,height=520px,center=1,resize=0','../../')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("item_account_id");
			var theemailv=this.contentDoc.getElementById("item_account_val");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_item_account_id").value=response[0];
			    document.getElementById("txt_item_acc").value=theemailv.value;
				release_freezing();
			}
		}
	}
	
	function openmypage_item_group()
	{
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_item_category').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/monthly_dyes_and_chemical_requisition_report_controller.php?action=item_group_popup&data='+data,'Item Group Popup', 'width=520px,height=380px,center=1,resize=0,scrolling=0','../../')
		
		emailwindow.onclose=function()
		{
			//var theemail=this.contentDoc.getElementById("item_name_id");
			//var response=theemail.value.split('_');
			var theemail=this.contentDoc.getElementById("item_name_id");
			var theemailv=this.contentDoc.getElementById("item_name_val");
			var response=theemail.value.split('_');
			//alert (response[1]);
			if (theemail.value!="")
			{
				//freeze_window(5);
				document.getElementById("txt_item_group_id").value=response[0];
				document.getElementById("txt_item_group").value=theemailv.value;
				//release_freezing();
			}
		}
	}
	
	function generate_report(report_type)
	{

		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}

		var report_title=$( "div.form_caption" ).html(); 
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_item_category = $("#cbo_item_category").val();
		var item_group_id = $("#txt_item_group_id").val();
		var item_account_id = $("#txt_item_account_id").val();
		var cbo_store_name = $("#cbo_store_name").val();
		var txt_date = $("#txt_date").val();
	
		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_store_name="+cbo_store_name+"&cbo_item_category="+cbo_item_category+"&txt_date="+txt_date+"&item_account_id="+item_account_id+"&item_group_id="+item_group_id+"&report_title="+report_title+"&report_type="+report_type;
		var data="action=generate_report"+dataString;
		// alert (data);
		// freeze_window(3);
		http.open("POST","requires/monthly_dyes_and_chemical_requisition_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+reponse[2]+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			setFilterGrid("table_body_id",-1,'');

			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window(type)
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="250px";
	}
	
	
	function fnc_purchase_requisition_details(dtls_id,action)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/monthly_dyes_and_chemical_requisition_report_controller.php?dtls_id='+dtls_id+'&action='+action,'Purchase Requisition Summery', 'width=900px,height=420px,center=1,resize=0','../../');
		emailwindow.onclose=function()
		{
			
		}
	}
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?><br />    		 
        <form name="monthlyDyesAndChemicalRequisitionReport_1" id="monthlyDyesAndChemicalRequisitionReport_1" autocomplete="off" > 
         <h3 style="width:800px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:800px">      
            <fieldset>  
                <table class="rpt_table" width="800" cellpadding="0" cellspacing="0">
                    <thead>
                        <th width="130" class="must_entry_caption">Company</th>
                        <th width="120">Item Category</th>
                        <th width="100">Item Group</th>
                        <th width="100">Item Account</th>
                        <th width="120">Store</th>
                        <th width="70">Date</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('monthlyDyesAndChemicalRequisitionReport_1','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td>
                                <? 
                                    echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "","" );
                                ?>                            
                            </td>
                           <td>
								<?php 
									echo create_drop_down( "cbo_item_category", 120,$item_category,"", 1, "-- Select Category--", $selected, "","","5,6,7,23","","","");
                                ?> 
                          </td>
                            <td>
                            	<input style="width:90px;" name="txt_item_group" id="txt_item_group" class="text_boxes" onDblClick="openmypage_item_group()" placeholder="Browse" readonly />
                                <input type="hidden" name="txt_item_group_id" id="txt_item_group_id" style="width:90px;"/>  
                            </td>
                            <td>
                            	<input style="width:90px;" name="txt_item_acc" id="txt_item_acc" class="text_boxes" onDblClick="openmypage_item_account()" placeholder="Browse" readonly />
                                <input type="hidden" name="txt_item_account_id" id="txt_item_account_id" style="width:90px;"/>
                            </td>
                           <td width="120" id="store_td">
                                <? 
                                    echo create_drop_down( "cbo_store_name", 120, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and  b.category_type in(5,6,7,23) group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select Store--", "", "" );
                                ?>
                           </td>
                            <td>
                                <input type="text" name="txt_date" id="txt_date" value="<? echo date("d-m-Y", time());?>" class="datepicker" style="width:55px;"/>                    							
                       
                            </td>
                            <td>
                                <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                            </td>
                        </tr>
                    </tbody>
                </table> 
            </fieldset> 
            </div>
                <div id="report_container" align="center"></div>
                <div id="report_container2"></div> 
        </form>    
    </div>
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
<script>set_multiselect('cbo_company_name','0','0','','0');</script>
<script>set_multiselect('cbo_item_category','0','0','','0');</script>
<script>set_multiselect('cbo_store_name','0','0','','0');</script>
</html>
