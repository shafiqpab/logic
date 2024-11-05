<?
/*-------------------------------------------- Comments
Purpose			: 	
				
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	07-08-2019
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
//---------------------------------------------------------------------------------------------
echo load_html_head_contents("Reck Shelf wise Stock Report","../../", 1, 1, $unicode,'',''); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters = 
	 {
		 col_operation: {
			 id: ["value_tot_rcv_amt","value_tot_issue_amt","value_tot_stock_amt"],
			 col: [20,21,22],
			 operation: ["sum","sum","sum"],
			 write_method: ["innerHTML","innerHTML","innerHTML"]
		 }
	 }

	function generate_report(rpt_type)
	{
		var job_no=$('#txt_job_no').val();
		var cbo_value_with = $("#cbo_value_with").val();

		if( form_validation('cbo_company_id*cbo_item_category','Company*Category')==false )
		{
			return;
		}		

		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_item_category*txt_date_from*txt_date_to*txt_job_no*txt_style_ref*cbo_job_year*txt_order_no*cbo_store_id',"../../")+'&report_title='+report_title+'&rpt_type='+rpt_type+"&cbo_value_with="+cbo_value_with;
		//alert (data);
		freeze_window(3);
		http.open("POST","requires/rack_shelf_wise_stock_report_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
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
	    '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$("#table_body tr:first").show();
	}

    function load_store(cat_id){
        var cbo_company_id=$("#cbo_company_id").val();
        load_drop_down( 'requires/rack_shelf_wise_stock_report_controller',cbo_company_id+'_'+cat_id, 'load_drop_down_store', 'store_td' );
    }

</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission); ?>  		 
    <form name="orderwisegreyfabricstock_1" id="orderwisegreyfabricstock_1" autocomplete="off" > 
    <h3 style="width:1140px; margin-top:10px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:1140px;">
                <table class="rpt_table" width="1140px" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="150" class="must_entry_caption">Company</th>                                
                            <th width="150">Location</th>
                            <th width="150" class="must_entry_caption">Item Category</th>
                            <th width="150">Store</th>
                            <th width="90">Value</th>
                            <th width="65">Job Year</th>
                            <th width="100">Job No</th>                            
                            <th width="100">Style Ref.</th>
                            <th width="100">Order No</th>                            
                            <th style="display:none">Date Range</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('orderwisegreyfabricstock_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
                            <? 
                               echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/rack_shelf_wise_stock_report_controller',this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/rack_shelf_wise_stock_report_controller',this.value, 'load_drop_down_store', 'store_td' );" );
                            ?>                            
                        </td>
                        <td id="location_td"> 
                            <?
                                echo create_drop_down( "cbo_location_id", 150, $blank_array,"", 1, "--Select Location--", 0, "",0 );
                            ?>
                        </td>
                        <td> 
                            <?
								 echo create_drop_down( "cbo_item_category", 150, $item_category,"", 1,"-- All --", 0, "load_store(this.value)",0,"2,4,".implode(",",array_keys($general_item_category)) );
                            ?>
                        </td>
                        <td id="store_td">
                            <?
                               echo create_drop_down( "cbo_store_id", 150, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type in(2,4,".implode(",",array_keys($general_item_category)).") and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1,"--Select store--",0,"");
                            ?>                            
                        </td>
                        <td>
                            <?   
                                $valueWithArr=array(0=>'Value With 0',1=>'Value Without 0');
                                echo create_drop_down( "cbo_value_with", 80, $valueWithArr,"","","",1,"","","");
                            ?>
                        </td>
                        <td> 
                            <?
								echo create_drop_down( "cbo_job_year", 60, $year,"", 1,"-- All --", date("Y"), "",0,"");
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:90px;"/>
                        </td>
                        <td>
                            <input type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:90px;"/>
                        </td>
                        <td>
                            <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:90px;"/>
                        </td>
                        <td style="display:none">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px;" readonly/>
                            To			
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px;" readonly/>				
                        </td>
                        
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                        </td>
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
