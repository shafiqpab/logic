<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Dayes and Chemical At a Glance
				
Functionality	:	
JS Functions	:
Created by		:	K.M Nazim Uddin
Creation date 	: 	28-01-2022
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
echo load_html_head_contents("Dayes and Chemical At a Glance","../../../", 1, 1, $unicode,1,1); 


?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters = 
	{
		col_40: "none",
		col_operation: {
		id: ["value_tot_qty","value_tot_a","value_tot_b","value_tot_b"],
		col: [6,7,8,9],
		operation: ["sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	
	function generate_report()
	{
		if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Date*TO Date')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html(); 
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_item_category_id = $("#cbo_item_category_id").val();
		var txt_item_acc = $("#txt_item_acc").val();
		var txt_item_account_id = $("#txt_item_account_id").val();
		var cbo_item_group = $("#cbo_item_group").val();
		var txt_date_from = $("#txt_date_from").val();
		var txt_date_to = $("#txt_date_to").val();
		var txt_prod_id = $("#txt_prod_id").val();
		var location_id = $("#location_id").val();
		//var txt_date = $("#txt_date").val();

		//txt_date_from*txt_date_to*txt_prod_id*location_id		
		
		
		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_item_group="+cbo_item_group+"&cbo_item_category_id="+cbo_item_category_id+"&location_id="+location_id+"&txt_item_account_id="+txt_item_account_id+"&txt_item_acc="+txt_item_acc+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&txt_prod_id="+txt_prod_id+"&report_title="+report_title;
		var data="action=generate_report"+dataString;
		//alert (data);
		freeze_window(3);
		http.open("POST","requires/dyes_and_chemical_at_a_glance_report_controller.php",true);
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
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
				setFilterGrid("table_body_id",-1,tableFilters);
			
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window(type)
	{
		if(type == 1)
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$('#table_body_id tr:first').hide();
			//$('#rpt_table_header tr th:last').attr('width', 120);
			//$('#table_body_id tr td:last').attr('width', 100);
			//$('#table_body_footer tr th:last').attr('width', 120);
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close(); 
			$('#table_body_id tr:first').show();
			$('#rpt_table_header tr th:last').attr('width', '');
			$('#table_body_id tr td:last').attr('width', '');
			$('#table_body_footer tr th:last').attr('width', '');
			document.getElementById('scroll_body').style.overflowY="scroll"; 
			document.getElementById('scroll_body').style.maxHeight="250px";
		}
		else
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
	}
	
	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}
	
	function getItemId()
	{
		var cbo_company_name=$('#cbo_company_name').val();
		var cbo_item_category_id=$('#cbo_item_category_id').val();
		load_drop_down( 'requires/dyes_and_chemical_at_a_glance_report_controller', cbo_company_name+'_'+cbo_item_category_id, 'load_multiselect_item_group', 'item_td' );
		set_multiselect('cbo_item_group','0','0','','0','');
	}

    function locationLoad()
    {
        var cbo_company_name=$('#cbo_company_name').val();
        load_drop_down( 'requires/dyes_and_chemical_at_a_glance_report_controller', cbo_company_name, 'load_location', 'location-td' );
        set_multiselect('location_id','0','0','','0','');
    }

	function openmypage_item_account()
	{
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_item_category_id').value+"_"+document.getElementById('cbo_item_group').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/dyes_and_chemical_at_a_glance_report_controller.php?action=item_account_popup&data='+data,'Item Account Popup', 'width=800px,height=520px,center=1,resize=0','../../')		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("item_account_id");
			var theemailv=this.contentDoc.getElementById("item_account_val");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{

				//freeze_window(5);
				document.getElementById("txt_item_account_id").value=response[0];
			    document.getElementById("txt_item_acc").value=theemailv.value;
				//reset_form();
				//get_php_form_data( response[0], "item_account_dtls_popup", "requires/dyes_and_chemical_at_a_glance_report_controller" );
				release_freezing();
			}
		}
	}
	
	function pipeline_dtls(pi_data,prod_id,porduct_data_all,action)
	{
		var width=750;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/dyes_and_chemical_at_a_glance_report_controller.php?action='+action+'&pi_data='+pi_data+'&prod_id='+prod_id+'&porduct_data_all='+porduct_data_all, 'PI Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}

</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?><br />    		 
    	<form name="dyes_cmcl_smry_rpt" id="dyes_cmcl_smry_rpt" autocomplete="off" > 
    		<h3 style="width:1060px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
     		<div id="content_search_panel" style="width:1060px">
	        	<fieldset>  
	            	<table class="rpt_table" width="1060" cellpadding="0" cellspacing="0" border="1" rull="all">
	                	<thead>
	                    	<th width="150" class="must_entry_caption">Company</th>
	                    	<th width="150">Location</th>
	                    	<th width="150">Item Category</th>
	                    	<th width="120">Item Group</th>
	                    	<th width="110">Item Description</th>
	                    	<th width="80">Product ID</th>
	                    	<th width="160" class="must_entry_caption">Date</th>
	                    	<th>
	                    		<input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('dyes_cmcl_smry_rpt','report_container*report_container2','','','')" /></th>
	                	</thead>
	                	<tbody>
	                    	<tr class="general">
	                        	<td>
	                            <? 
	                                echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 0, "", $selected, "" );
	                            ?>                            
	                        	</td>
	                        	<td id="location-td">
	                            <?
	                                echo create_drop_down( "location_id", 150, $blank_array,"", 1, "-- Select Location --", "", "" );
	                            ?>                            
	                        	</td>
	                       		<td id="cat_td">
								<?php 
									echo create_drop_down( "cbo_item_category_id", 150,$item_category,"", 0, "", $selected, "","","5,6,7,19,20,22,23,39","","","");
	                            ?> 
	                      		</td>
								<td id="item_td">
	                            <? 
	                                echo create_drop_down( "cbo_item_group", 120, $blank_array,"", 1, "--Select Store--", "", "" );
	                            ?>
	                       		</td>
	                    		<td>
	                    			<input style="width:110px;" name="txt_item_acc" id="txt_item_acc" class="text_boxes" onDblClick="openmypage_item_account()" placeholder="Browse" readonly />
	                        		<input type="hidden" name="txt_item_account_id" id="txt_item_account_id" style="width:90px;"/>  
	                    		</td>
	                    		<td>
	                    			<input style="width:60px;" name="txt_prod_id" id="txt_prod_id" class="text_boxes_numeric" />
	                    		</td>
	                       		<td>
		                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" >&nbsp; To
		                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date"  >
		                        </td>
	                    		<td>
	                            	<input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:70px" class="formbutton" />
	                        	</td>
	                    	</tr>
	                	</tbody>
	                	<tfoot>
		                    <tr>
		                        <td colspan="8" align="left">
		                        	<? echo load_month_buttons(1);  ?>&nbsp;&nbsp;	                        
		                        </td>
		                    </tr>
		                </tfoot>
		            </table> 
		        </fieldset> 
    		</div>
            <div id="report_container" align="center" style="width:1150px;"></div>
            <div id="report_container2"></div> 
    	</form>    
    </div>
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
<script>
	set_multiselect('cbo_company_name','0','0','','0', 'locationLoad()');
	set_multiselect('location_id','0','0','','0', '');
	set_multiselect('cbo_item_category_id','0','0','','0', 'getItemId()');
	set_multiselect('cbo_item_group','0','0','','0', '');
</script> 
</html>
