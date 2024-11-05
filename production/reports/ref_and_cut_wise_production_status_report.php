<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	Ref and Cut Wise Production Status Report
Functionality	:	
JS Functions	:
Created by		:	Md. Kamrul Hasan
Creation date 	: 	10-September-2023
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
echo load_html_head_contents("Ref and Cut Wise Production Status Report","../../", 1, 1, $unicode,0,0);
?>	

<script>
    function reprot_gen(type)
	{
		var cbo_company_name=$('#cbo_company_name').val();
		var cbo_po_company=$('#cbo_po_company').val();
        var cbo_location=$('#cbo_location').val();
		var cbo_buyer_name=$('#cbo_buyer_name').val();
		var cbo_floor_name=$('#cbo_floor_name').val();
		var txt_cuttiong_no=$('#txt_cuttiong_no').val();
		var internal_ref=$('#internal_ref').val();
		
		var txt_date_from=$('#txt_date_from').val();
		var txt_date_to=$('#txt_date_to').val();

        if(form_validation('cbo_company_name*cbo_po_company*cbo_location','Comapny Name*Working Factory*Location')==false)
        {
            release_freezing();
            return;
        }
		if (cbo_buyer_name==0 && cbo_floor_name==0 && txt_cuttiong_no=='' && internal_ref=='')
		{
			if(form_validation('txt_date_from*txt_date_to','From date*To date')==false)
            {
                release_freezing();
                return;
            }
		}
		
        if(type == 1){
            var action = "report_gen_show_button";
        }else if(type == 2){
            var action = "report_gen_show_button2";
        }else{
            alert("Something Went Wrong!");
            return; 
        }

        
		var data="action="+action+get_submitted_data_string('cbo_company_name*cbo_po_company*cbo_location*cbo_buyer_name*cbo_floor_name*txt_cuttiong_no*internal_ref*txt_date_from*txt_date_to',"../../");
		freeze_window(3);
		http.open("POST","requires/ref_and_cut_wise_production_status_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
			
	}
	

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			release_freezing();
			var reponse=trim(http.responseText).split("####");

			$('#report_container2').html(reponse[0]);

			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	 		show_msg('3');
		}
	}

    function new_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	}
</script>


</head>
 
<body onLoad="set_hotkey();">
<form id="ref_cut_wise_production_status_report">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../"); ?>
		<h3 style="width:1200px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
		<div id="content_search_panel">
		<fieldset style="width:960px;">
            <table class="rpt_table" width="1200" cellpadding="1" cellspacing="2" align="center">
            	<thead>
                	<tr> 
                        <th width="120" class="must_entry_caption">Company Name</th>
                        <th width="120" class="must_entry_caption">Working Factory</th>
                        <th width="120" class="must_entry_caption">Location</th>
                        <th width="120">Buyer Name</th>
                        <th width="80">Floor</th>
                        <th width="100">Cutting No.</th>
                        <th width="100">Internal Ref.</th>
                        <th width="180">Date Range</th>
                        <th width="200">
                            <input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:70px" onClick="reset_form('','report_container*report_container2','','','')" />
                        </th>
                    </tr>
                 </thead>
                <tbody>
                <tr class="general">
                    <td align="center"> 
						<?
                        echo create_drop_down("cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "Select Company", $selected, "load_drop_down( 'requires/ref_and_cut_wise_production_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );");
                        ?>
                    </td>
                    <td>
                        <?
                        echo create_drop_down("cbo_po_company", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "Select Company", $selected, "load_drop_down( 'requires/ref_and_cut_wise_production_status_report_controller',this.value, 'load_drop_down_location', 'location_td' );");
                        ?>
                    </td>
                    <td id="location_td">
                        <? echo create_drop_down( "cbo_location", 120, "","", 1, "-- Select Location --", '', "","0" ); ?>
                    </td>
                    <td id="buyer_td">
                        <? echo create_drop_down( "cbo_buyer_name", 120, "","", 1, "-- Select Buyer --", '', "","0" ); ?>
                    </td>
                    <td id="floor_td">
                        <? echo create_drop_down( "cbo_floor_name", 120, "","", 1, "-- Select Floor --", '', "","0" ); ?>
                    </td>
					<td>
                       <input type="text" id="txt_cuttiong_no"  name="txt_cuttiong_no"  style="width:80px" class="text_boxes" placeholder="Write"  />
                    </td>
                    <td>
                    	<input type="text" name="internal_ref" id="internal_ref" class="text_boxes"  placeholder="Write" style="width:87px"  />
                    </td>
                    <td width="180">
                  		<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" >&nbsp; To
                  		<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" >
                    </td>
                   	<td>
                        <input type="button" id="show1" class="formbutton" style="width:70px" value="Show" onClick="reprot_gen(1);" />
                        <input type="button" id="show2" class="formbutton" style="width:70px" value="Show2" onClick="reprot_gen(2);" />
                    </td>
                </tr>
                </tbody>
            </table>
            <table>
                <tr>
                    <td>
                        <? echo load_month_buttons(1); ?>
                    </td>
                </tr>
            </table> 
    	</fieldset>
    	</div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
</form>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
