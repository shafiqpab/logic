<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Subcontract Dyeing And Finishing Delivery and Bill Report
				
Functionality	:	
JS Functions	:
Created by		:	Helal Uddin
Creation date 	: 	27-05-2021
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
echo load_html_head_contents("Knitting Bill Report","../../", 1, 1, $unicode,0,0); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	
	var tableFilters = 
		{
		
		
			col_operation: {
			id:["total_bill","total_amount"],
			col: [11,13],
			operation: ["sum","sum"],
			write_method: ["innerHTML","innerHTML"]
			}
		
		}
	
	
	function generate_wo_order_report(company_id,knitting_wo_id)
	{
		
		
		print_report( company_id+'**'+knitting_wo_id,"work_order_print", "requires/subcontract_dyeing_and_finishing_delivery_and_bill_controller");
	}
	
	

	function generate_report()
	{
		
		
		if($("#txt_order_id").val()=="")
		{
			if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*Date Form*Date To')==false )
			{
				return;
			}
		}
		else{
			if( form_validation('cbo_company_id','Company Name')==false )
			{
				return;
			}
		}
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*txt_order_id*cbo_party_name*cbo_date_type*cbo_year*txt_date_from*txt_date_to*search_type',"../../")+'&report_title='+report_title;
		
		freeze_window(3);
		http.open("POST","requires/subcontract_dyeing_and_finishing_delivery_and_bill_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			// alert(http.responseText);
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window();" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
			//var batch_type = document.getElementById('cbo_batch_type').value;
			
			setFilterGrid("table_body",-1,tableFilters);
			
			show_msg('3');
			release_freezing();
		}
	} 
	
	function new_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";
		
		//$("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="400px";
		
		//$("#table_body tr:first").show();
	}

	function openmypage_order() 
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_id").val();	
		
		var cbo_year = $("#cbo_year").val();
		var txt_order_id = $("#txt_order_id").val();
		var page_link='requires/subcontract_dyeing_and_finishing_delivery_and_bill_controller.php?action=order_search&company='+company+'&txt_order_id='+txt_order_id+'&cbo_year='+cbo_year; 
		var title="Order No Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var txt_id_string=this.contentDoc.getElementById("txt_id_string").value; // product ID
			var txt_name_string=this.contentDoc.getElementById("txt_name_string").value; // product Description
			var txt_type=this.contentDoc.getElementById("txt_type").value; // product Description
			//alert(style_des_no);
			$("#search_type").val(txt_type);
			$("#txt_order_id").val(txt_id_string); 
			$("#txt_order").val(txt_name_string);

		}
	}
	
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission); ?>   		 
    <form name="knitting_bill_report_1" id="knitting_bill_report_1" autocomplete="off" > 
    <h3 style="width:1070px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:1070px;">
                <table class="rpt_table" width="1050" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 
                        	 	
                            <th width="140" class="must_entry_caption">Company</th>    
                            <th width="140">Party Name</th>
                            <th width="70">Year</th>                           
                            <th width="100">Search By</th>                           
                            <th  width="170">Based on</th>
                            <th id="search_by_td"  width="160" class="must_entry_caption">Bill Date Range</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('knitting_bill_report_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                    	
                        <td>
                            <? 
                               echo create_drop_down( "cbo_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/subcontract_dyeing_and_finishing_delivery_and_bill_controller',this.value+'_'+'1', 'load_drop_down_party_name', 'party_td' );" );
                            ?>                            
                        </td>
                        <td width="140" id="party_td">
                        	<? echo create_drop_down( "cbo_party_name", 130, $blank_array,"", 1, "--Select Party--", $selected, "",0,"","","","",6); ?>
                   		</td>
                        <td><? echo create_drop_down( "cbo_year", 50, create_year_array(),"", 1,"-All-",0 , "",0,"" ); ?></td>
                        
                        
                       
                       
                        
                        <td align="center" >
							<input style="width:100px;" name="txt_order" id="txt_order" onDblClick="openmypage_order()" class="text_boxes" placeholder="Browse" readonly="1" />   
                            <input type="hidden" name="txt_order_id" id="txt_order_id"/>
                            <input type="hidden" name="txt_order_id_no" id="txt_order_id_no"/>
                            <input type="hidden" name="search_type" id="search_type"/> 
						</td>
                         <td> 
                            <?
                            	// $dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
								$search_by=array(1=>'Delivery Date',2=>'Bill Date');
                                echo create_drop_down( "cbo_date_type", 100, $search_by,"", "", "", "2", "",0 );
                            ?>
                        </td>
                        <td >
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:60px" placeholder="From Date"/>
                             To
                             <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:60px" placeholder="To Date"/>
                        </td>
                       
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report();" style="width:70px" class="formbutton" />
                        </td>
                    </tr>
                    <tr>
						<td colspan="9" align="center" width="100%"><? echo load_month_buttons(1); ?></td>                    
					</tr>
                </table> 
            </fieldset> 
        </div>
            <div id="report_container" align="center"></div>
            <div id="report_container2"></div>   
    </form>    
</div>    
</body>  

<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
