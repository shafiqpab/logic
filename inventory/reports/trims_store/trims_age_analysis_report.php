<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Trims Age Analysis Report
				
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam Reza 
Creation date 	: 	14-08-2016
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
echo load_html_head_contents("Trims Age Analysis Report","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function fnc_generate_report(operation)
	{
		if( form_validation('cbo_company_name*txt_date_from*txt_no_col*txt_range','Company Name*Date*No of Col*Range')==false )
		{
			return;
		}
	
		var report_title=$( "div.form_caption" ).html();
		var data="action=generate_report"+get_submitted_data_string('cbo_company_name*cbo_item_group*txt_item_description_id*txt_date_from*txt_no_col*txt_range',"../../../")+'&report_title='+report_title;
		
		freeze_window(3);
		http.open("POST","requires/trims_age_analysis_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_report_reponse; 
	}
	
	function fnc_generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			var ids="['total_stock_td'";var cols="[4";var operations="['sum'";var write_methods="['innerHTML'";var i=1;
			for(i;i<reponse[2]*1;i++){
				ids+=",'age_"+i+"'";
				cols+=","+(4+i);
				operations+=",'sum'";
				write_methods+=",'innerHTML'";
			}
			ids+=",'total_amount_id']";cols+=","+(i+5)+"]";operations+=",'sum']";write_methods+=",'innerHTML']";
				var tableFilters = 
				{
					col_operation: {
					id: eval(ids),
					col: eval(cols),
					operation: eval(operations),
					write_method: eval(write_methods)
					}
				} 
			
			setFilterGrid("table_body",-1,tableFilters);
	
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
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflow="scroll"; 
		document.getElementById('scroll_body').style.maxHeight="350px";
		$("#table_body tr:first").show();
	}

	function openmypage_item_description()
	{
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_item_group').value;
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/trims_age_analysis_report_controller.php?action=item_description_popup&data='+data,'Item Description Popup', 'width=470px,height=400px,center=1,resize=0','../../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("item_desc_id");
			var theemailv=this.contentDoc.getElementById("item_desc_val");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_item_description_id").value=response[0];
				document.getElementById("txt_item_description").value=theemailv.value;
				
				release_freezing();
			}
		}
	}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../../",$permission);  ?>    		 
    <form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" > 
        <div style="width:800px; margin:0 auto;">
            <h3 align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
            <div id="content_search_panel">
                <fieldset>
                    <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                        <thead>
                            <tr> 	 	
                                <th class="must_entry_caption">Company</th> 
                                <th>Item Group</th>                               
                                <th>Item Description</th>
                                <th class="must_entry_caption">Date</th>
                                <th>No. of Col.</th>
                                <th>Range</th>
                                <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" /></th>
                            </tr>
                        </thead>
                        <tr class="general">
                            <td>
								<? 
                                	echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                                ?>                            
                            </td>
                            <td> 
								<?
                                	echo create_drop_down( "cbo_item_group", 140, "select item_name,id from lib_item_group where item_category=4 and is_deleted=0 and status_active=1 order by item_name","id,item_name", 0, "", $selected, "" );
                                ?>
                            </td>
                            <td align="center">
                                <input style="width:110px;" name="txt_item_description" id="txt_item_description" class="text_boxes" onDblClick="openmypage_item_description()"  placeholder="Browse Description"  />
                                <input type="hidden" name="txt_item_description_id" id="txt_item_description_id" style="width:90px;"/>             
                            </td>
                            <td>
                                <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y", time()-86400);?>" class="datepicker" style="width:65px" readonly/>
                            </td>
                        <td align="center">
                            <input type="text" id="txt_no_col" name="txt_no_col" class="text_boxes_numeric" style="width:30px" value="" />
                        </td>
                        <td>
                            <input type="text" id="txt_range" name="txt_range" class="text_boxes_numeric" style="width:30px" value="" />
                        </td>
                         <td>   	
                                <input type="button" name="search" id="search" value="Show" onClick="fnc_generate_report()" style="width:70px" class="formbutton" />
                            </td>
                        </tr>
                    </table> 
                </fieldset> 
            </div>
        </div>
        <br /> 
        <div id="report_container" align="center"></div>
        <div id="report_container2"></div> 
    </form>    
    </div>    
</body> 
	<script> set_multiselect('cbo_item_group','0','0','','0');</script> 
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script> $("#cbo_value_with").val(0);</script> 
</html>
