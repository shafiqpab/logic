<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Room Rack Wise Trims Stock
				
Functionality	:	
JS Functions	:
Created by		:	Rakib
Creation date 	: 	12-06-2022
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
echo load_html_head_contents("Room Rack Wise Trims Stock","../../", 1, 1, $unicode,1,1); 
die;  // This page not used
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	/* var tableFilters = 
	{
		col_40: "none",
		col_operation: {
		id: ["value_total_opening_td","value_total_receive_td","value_total_issue_return_td","value_total_transfer_in","value_total_receive_balance_td","value_total_issue_td","value_total_receive_return_td","value_total_transfer_out","value_total_issue_balance_td","value_total_closing_stock_td","value_total_closing_amnt"],
		//col: [5,6,7,8,9,10,11,12,14],
        col: [6,7,8,9,10,11,12,13,14,15,17],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} */
    

	function fnc_generate_report(operation)
	{
        if( form_validation('cbo_company_name','Company Name')==false )
        {
            return;
        }

		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*txt_style_no*txt_order_no*txt_internal_ref_no*cbo_item_group*cbo_store_name*cbo_floor_name*cbo_room_name*cbo_rack_name*cbo_shelf_name*cbo_binbox_name*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/room_rack_wise_trims_stock_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_report_reponse; 
	}
	
	function fnc_generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			//alert(reponse[0]);

            $("#report_container2").html(reponse[0]);  
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+reponse[2]+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

			setFilterGrid("table_body",-1);
			
 			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window(str)
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="350px";
        $("#table_body tr:first").show();
	}

	function openmypage_item_description()
	{
		if(form_validation('cbo_company_name','Company')==false){ return; }
		
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_item_group').value;
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/room_rack_wise_trims_stock_report_controller.php?action=item_description_popup&data='+data,'Item Description Popup', 'width=620px,height=400px,center=1,resize=0','../../')
		
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

    function getCompanyId() 
	{
	    var company_id = $("#cbo_company_name").val();
	    load_drop_down( 'requires/room_rack_wise_trims_stock_report_controller',company_id, 'load_drop_down_store', 'store_td' );  
	    load_drop_down( 'requires/room_rack_wise_trims_stock_report_controller',company_id, 'load_drop_down_buyer', 'buyer_td' );
	}

    function loadFloor() 
    {
        var company_id = document.getElementById('cbo_company_name').value;
        var store_id = document.getElementById('cbo_store_name').value;
        load_drop_down('requires/room_rack_wise_trims_stock_report_controller', company_id+'_'+store_id, 'load_drop_down_floors', 'floor_td');            
    }

    function loadRoom() 
    {
        var company_id = document.getElementById('cbo_company_name').value;
        var store_id = document.getElementById('cbo_store_name').value;
        var floor_id = document.getElementById('cbo_floor_name').value;
        load_drop_down('requires/room_rack_wise_trims_stock_report_controller', company_id+'_'+store_id+'_'+floor_id, 'load_drop_down_rooms', 'room_td');          
    }

    function loadRack() 
    {
        var company_id = document.getElementById('cbo_company_name').value;
        var store_id = document.getElementById('cbo_store_name').value;
        var floor_id = document.getElementById('cbo_floor_name').value;
        var room_id = document.getElementById('cbo_room_name').value;
        load_drop_down('requires/room_rack_wise_trims_stock_report_controller', company_id+'_'+store_id+'_'+floor_id+'_'+room_id, 'load_drop_down_racks', 'rack_td');
    }

    function loadShelf() 
    {
        var company_id = document.getElementById('cbo_company_name').value;
        var store_id = document.getElementById('cbo_store_name').value;
        var floor_id = document.getElementById('cbo_floor_name').value;
        var room_id = document.getElementById('cbo_room_name').value;
        var rack_id = document.getElementById('cbo_rack_name').value;
        load_drop_down('requires/room_rack_wise_trims_stock_report_controller', company_id+'_'+store_id+'_'+floor_id+'_'+room_id+'_'+rack_id, 'load_drop_down_shelfs', 'shelf_td');        
    }

    function loadBinbox() 
    {
        var company_id = document.getElementById('cbo_company_name').value;
        var store_id = document.getElementById('cbo_store_name').value;
        var floor_id = document.getElementById('cbo_floor_name').value;
        var room_id = document.getElementById('cbo_room_name').value;
        var rack_id = document.getElementById('cbo_rack_name').value;
        var shelf_id = document.getElementById('cbo_shelf_name').value;
        load_drop_down('requires/room_rack_wise_trims_stock_report_controller', company_id+'_'+store_id+'_'+floor_id+'_'+room_id+'_'+rack_id+'_'+shelf_id, 'load_drop_down_binboxs', 'binbox_td');
    }

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../",$permission);  ?>    		 
    <form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" > 
        <div style="width:100%;" align="center">
            <h3 style="width:1700px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
            <div style="width:100%;" id="content_search_panel">
                <fieldset style="width:1700px;">
                    <table class="rpt_table" width="1700" cellpadding="0" cellspacing="0" border="1" rules="all">
                        <thead>
                            <tr> 	 	
                                <th class="must_entry_caption">Company</th> 
                                <th>Buyer</th>                               
                                <th>Job No</th>
                                <th>Style No</th>
                                <th>Order No</th>
                                <th>Internal Ref</th>
                                <th>Item Group</th>
                                <th>Store Name</th>
                                <th>Floor</th>
                                <th>Room</th>
                                <th>Rack</th>
                                <th>Self</th>
                                <th>Bin/Box</th>
                                <th colspan="2">Date Range</th>
                                <th width="100"><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" /></th>
                            </tr>
                        </thead>
                        <tr class="general">
                            <td>
								<? 
                                	echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/room_rack_wise_trims_stock_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );getCompanyId();" );
                                ?>                            
                            </td>
                            <td id="buyer_td"> 
								<?
                                	echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"",1, "--Select Buyer--", 1, "" );
                                ?>
                            </td>
                            <td align="center">
                                <input style="width:80px;" name="txt_job_no" id="txt_job_no" class="text_boxes" placeholder="write" />                                
                            </td>
                            <td align="center">
                                <input style="width:80px;" name="txt_style_no" id="txt_style_no" class="text_boxes" placeholder="write" />                                
                            </td>
                            <td align="center">
                                <input style="width:80px;" name="txt_order_no" id="txt_order_no" class="text_boxes" placeholder="write" />                                
                            </td>
                            <td align="center">
                                <input style="width:80px;" name="txt_internal_ref_no" id="txt_internal_ref_no" class="text_boxes" placeholder="write" />                             
                            </td>
                            <td id="item_group_td">
								<?                                   
                                    echo create_drop_down( "cbo_item_group", 120, "select item_name,id from lib_item_group where item_category=4 and is_deleted=0 and status_active=1 order by item_name","id,item_name", 1,  "--Select Itemgroup--", $selected, "" );
                                ?>
                            </td>
                            <td id="store_td">
								<?
                                    echo create_drop_down( "cbo_store_name", 120, $blank_array,"",1, "--Select Store--", "", "" );
                                ?>
                            </td>
                            <td id="floor_td">
								<?
                                    echo create_drop_down( "cbo_floor_name", 120, $blank_array,"",1, "--Select Floor--", "", "" );
                                ?>
                            </td>
                            <td id="room_td">
								<?
                                    echo create_drop_down( "cbo_room_name", 120, $blank_array,"",1, "--Select Room--", "", "" );
                                ?>
                            </td>
                            <td id="rack_td">
								<?
                                    echo create_drop_down( "cbo_rack_name", 120, $blank_array,"",1, "--Select Rack--", "", "" );
                                ?>
                            </td>
                            <td id="shelf_td">
								<?
                                    echo create_drop_down( "cbo_shelf_name", 120, $blank_array,"",1, "--Select Self--", "", "" );
                                ?>
                            </td>
                            <td id="binbox_td">
								<?
                                    echo create_drop_down( "cbo_binbox_name", 120, $blank_array,"",1, "--Select Binbox--", "", "" );
                                ?>
                            </td>                            
                            <td>
                                <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:65px" placeholder="From Date"/>
                            </td>
                            <td>
                                <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px" placeholder="To Date"/>
                            </td>                          
                            
                            <td>
                            	<input type="button" name="search" id="search" value="Show" onClick="fnc_generate_report(1)" style="width:70px" class="formbutton" />
                            </td>
                         </tr>
                        <tr>
                            <td colspan="16" align="center">
                                <? echo load_month_buttons(1); ?>                                
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
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
