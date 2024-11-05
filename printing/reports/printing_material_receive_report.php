<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	
Functionality	:	
JS Functions	:
Created by		:	Md Rakib Hasan Mondal
Creation date 	: 	23-September-2023
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
echo load_html_head_contents("Date Wise Production Report", "../../", 1, 1,$unicode,1,1);

?> 
<script>

    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
    var permission = '<? echo $permission; ?>';
    function browseStyle()
    { 
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_buyer_name').value;
		
		
		
		var page_link="requires/printing_material_receive_report_controller.php?action=style_no_popup&data="+data;
		var title="Style Ref.";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=420px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			//var job=theemail.split("_");
			document.getElementById('txt_style_no').value=theemail;
            load_drop_down( 'requires/printing_material_receive_report_controller', theemail, 'load_drop_down_color', 'color_td');
			release_freezing();
		}
	}
    function fn_report_generated(type)
    {
        if ($('#txt_style_no').val() !='' || $('#txt_challan_no').val() !='') {
            if (form_validation('cbo_company_name','Company Name')==false)
            {
                return;
            } 
        }
        else
        {
            if (form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false)
            {
                return;
            } 
        }
                   
        var data="action=report_generate"+'&type='+type+get_submitted_data_string('cbo_company_name*cbo_location*cbo_buyer_name*txt_style_no*cbo_color_id*txt_challan_no*txt_date_from*txt_date_to',"../../");
        freeze_window(3);
        http.open("POST","requires/printing_material_receive_report_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_report_generated_reponse; 
    } 
    function fn_report_generated_reponse()
    {
        if(http.readyState == 4) 
        {
            show_msg('3'); 
            var reponse=trim(http.responseText).split("####"); 
            $('#report_container2').html(reponse[0]);
            let report_type = reponse[2]; 
            // alert(reponse[2]);
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>'+'&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';

            if (report_type == 1) //DELIVERY BUTTON
            { 
                let tableFilters1 =
                { 
                    col_operation: {
                        id: ["total_prod_qty"],
                        col: [15],
                        operation: ["sum"],
                        write_method: ["innerHTML"]
                    }
                } 
                setFilterGrid("table_body",-1,tableFilters1); 
            }
            
            if (report_type == 2) //DELIVERY BUTTON
            {
               let tableFilters2 =
                { 
                    col_operation: {
                        id: ["total_prod_qty","total_reject_qty"],
                        col: [15,16],
                        operation: ["sum","sum"],
                        write_method: ["innerHTML","innerHTML"]
                    }
                }
                setFilterGrid("table_body",-1,tableFilters2); 
            }
            
            
            release_freezing();
            
        }
        
    }

    function new_window() {
        var filter = 0;
		if ($("#table_body tbody tr:first").attr('class')=='fltrow')
		{
			filter = 1;
			$("#table_body tbody tr:first").hide();
		}
        document.getElementById('scroll_body').style.overflow='auto';
        document.getElementById('scroll_body').style.maxHeight='none'; 
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
        '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>'); 
        d.close();
        document.getElementById('scroll_body').style.overflowY='scroll';
        document.getElementById('scroll_body').style.maxHeight='300px';
        if(filter == 1)
		{
			$("#table_body tbody tr:first").show();
		}
    }
    function fnc_issue_print_embroidery(type,txt_system_id,wo_com_id,body_part,funcCallFrom=0) // funcCallFrom  0  from parent 1 for Email 
    { 
        var company_id=$('#cbo_company_name').val();
         
        let delivery_basis = 3;
        let template_id = 1;
 
        if(type==1)
        {
            var report_title=$( "div.form_caption" ).html();
            generate_report_file(company_id+'*'+txt_system_id+'*'+delivery_basis+'*'+report_title+'*'+template_id, 'emblishment_issue_print_2',funcCallFrom);
            return;
        }
        else if(type==2)
        {
            var report_title=$( "div.form_caption" ).html();
            generate_report_file(company_id+'*'+txt_system_id+'*'+delivery_basis+'*'+report_title+'*'+template_id, 'emblishment_issue_print_3',funcCallFrom);
            return;
        }
        else if(type==3)
        {
            var report_title=$( "div.form_caption" ).html();
            generate_report_file(company_id+'*'+txt_system_id+'*'+delivery_basis+'*'+report_title, 'emblishment_issue_print',funcCallFrom);
            return;
        }  
        else if(type==4)
        {
            var report_title=$( "div.form_caption" ).html();
            generate_report_file(company_id+'*'+txt_system_id+'*'+delivery_basis+'*'+report_title+'*'+wo_com_id+'*'+template_id, 'emblishment_issue_print_4',funcCallFrom);
            return;
        }
        else if(type==5)
        {
            var report_title=$( "div.form_caption" ).html();
            generate_report_file(company_id+'*'+txt_system_id+'*'+delivery_basis+'*'+report_title+'*'+wo_com_id+'*'+template_id, 'emblishment_issue_print_5',funcCallFrom);
            return;
        }
        else if(type==6)
        {
            var report_title=$( "div.form_caption" ).html();
            generate_report_file(company_id+'*'+txt_system_id+'*'+delivery_basis+'*'+report_title+'*'+wo_com_id+'*'+template_id, 'emblishment_issue_print_6',funcCallFrom);
            return;
        }
        else if(type==7)
        {
            var report_title=$( "div.form_caption" ).html();
            generate_report_file(company_id+'*'+txt_system_id+'*'+delivery_basis+'*'+report_title+'*'+wo_com_id+'*'+template_id, 'emblishment_issue_print_7',funcCallFrom);
            return;
        }
        else if(type==8)
        {
            var report_title=$( "div.form_caption" ).html();
            generate_report_file(company_id+'*'+txt_system_id+'*'+delivery_basis+'*'+report_title+'*'+wo_com_id+'*'+template_id, 'emblishment_issue_print_8',funcCallFrom);
            return;
        }
        else if(type==9)
        {
            var report_title=$( "div.form_caption" ).html();
            generate_report_file(company_id+'*'+txt_system_id+'*'+delivery_basis+'*'+report_title+'*'+template_id+'*'+body_part, 'emblishment_issue_print_9',funcCallFrom);
            return;
        } 
    }
    function fnc_embl_delivery(type,company,issue_mst_id,location)
	{
		let pageUrl      = "../delivery/requires/embellishment_delivery_bundle_controller";
		let report_title = "Printing Delivery Entry [Bundle]";
		let data         = company+'*'+issue_mst_id+'*'+report_title+'*'+location;

		if(type==1){
			var action = "embl_delivery_bundle_entry_print"; 
		}
		else if(type==2){
			var action = "embl_delivery_bundle_entry_print_2"; 
		}
		else if(type==3){
			var action = "embl_delivery_bundle_entry_print_3"; 
		}

		print_report( data, action, pageUrl);
		show_msg("3");

	}
    function generate_report_file(data,action,funcCallFrom=0)
    {
        let basePath = '../../';
        if (funcCallFrom==1) 
        {
            basePath = '../../../';
        }
        window.open( basePath+'production/requires/print_embro_delivery_entry_controller.php?data=' + data+'&action='+action, true );
    }
    
	function details_data_popup(type,delivery_ids)
	{
		popup_width='1250px'; 
        title = (type==1) ? 'Printing Material Receive Details Popup' : 'Printing Delivery Details Popup' ; 
        company_id  = $('#cbo_company_name').val();
        location_id = $('#cbo_location').val();
        buyer_id    = $('#cbo_buyer_name').val();
        style_no    = $('#txt_style_no').val();
        color_id    = $('#cbo_color_id').val();
        challan_no  = $('#txt_challan_no').val();
        form_date   = $('#txt_date_from').val();
        to_date     = $('#txt_date_to').val();

        data = '&type='+type+'&delivery_ids='+delivery_ids+'&company_id='+company_id+'&buyer_id='+buyer_id+'&location_id='+location_id+'&style_no='+style_no+'&color_id='+color_id+'&challan_no='+challan_no+'&form_date='+form_date+'&to_date='+to_date;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/printing_material_receive_report_controller.php?action=details_popup'+data, title, 'width='+popup_width+', height=290px,center=1,resize=0,scrolling=0','../');
	}
    // Count Visable Row 
    var documentElement = document.documentElement;
    // Add a keypress event listener
    documentElement.addEventListener("keypress", function(event) 
    {
        // Get the event target
        var target = event.target;
        try 
        {
            // Check if the target has the class flt
            if (target.classList.contains("flt"))
            {
                // Check if the key is Enter
                if (event.key === "Enter" )
                {
                    var count = $('#table_body tr:not([style*="display: none"])').length;
                    $('#row_count').text(count - 1);
                }
            }
        } catch (error) {
            console.log(error)
        }
    });
</script>         
                          
</head>
 
<body onLoad="set_hotkey();">

<form id="lineWiseProductionReport_1">
    <div style="width:100%;" align="center">   
        <? echo load_freeze_divs ("../../",'');  ?>     
        <fieldset style="width:1360px;">
            <legend>Search Panel</legend> 
            <table class="rpt_table" width="1360px" cellpadding="0" cellspacing="0" border="1" align="center">
                <thead>                    
                    <tr>
                        <th width="150" class="must_entry_caption">Emb Company</th>
                        <th width="150">Emb Location</th>
                        <th width="150">Buyer</th> 
                        <th width="150">Buyer Style</th> 
                        <th width="150">Color</th> 
                        <th width="150">Challan No</th> 
                        <th width="140" class="must_entry_caption"> Date Range</th>
                        <th width="180"><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                    </tr>    
                </thead>
                <tbody>
                    <tr >
                        <td width="150"> 
                            <?
                                echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/printing_material_receive_report_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/printing_material_receive_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>                   
                        <td id="location_td">
                            <? 
                                echo create_drop_down( "cbo_location", 150, $blank_array,"", 1, "-- Select Location --", $selected, " load_drop_down( 'requires/printing_material_receive_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );", 1, "" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "", 1, "" );
                            ?>
                        </td> 
                        <td  align="center">				
                            <input type="text" style="width:150px" class="text_boxes" name="txt_style_no" id="txt_style_no" placeholder="Browse/Write" onDblClick="browseStyle()" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).ondblclick()" />	
                        </td>
                        <td id="color_td">
                            <? 
                                echo create_drop_down( "cbo_color_id", 150, $blank_array,"", 1, "-- Select Color --", $selected, "", 1);
                            ?>
                        </td> 
                        <td  align="center">				
                            <input type="text" style="width:150px" class="text_boxes" name="txt_challan_no" id="txt_challan_no" />	
                        </td>
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:55px"/>                                             
                            To
                            <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:55px"/>
                        </td>                
                        <td >
                            <input type="button" id="show_button" class="formbutton" style="width:90px" value="Show" onClick="fn_report_generated(1)" />
                            <input type="button" id="show_button" class="formbutton" style="width:90px" value="Delivery" onClick="fn_report_generated(2)" />
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="7"><? echo load_month_buttons(1);  ?></td>
                        <td >
                            <input type="button" id="show_button" class="formbutton" style="width:90px" value="Summary" onClick="fn_report_generated(3)" /> 
                        </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    </div>
    <div id="report_container" align="center" style="padding: 10px;"></div>
    <div id="report_container2"></div>
 </form>    
</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>
