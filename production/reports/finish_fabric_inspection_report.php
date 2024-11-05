<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Fabric Receive Status  Without Order Report.
Functionality	:	
JS Functions	:
Created by		:	Abu Sayed 
Creation date 	: 	26-05-2021
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
echo load_html_head_contents("Fabric Receive Status Without Order Report", "../../", 1, 1,'',1,1);

?>

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";   
	
	var tableFilters = 
	 {
		//col_30: "none",
		
		col_operation: {
		id: ["total_Inspected_Qty","total_length","total_hole_defect","total_dye_defect","total_poly_defect","total_slub_defect_count","total_patta_defect_count","total_cut_defect_count","total_print_mis_defect_count","total_yarn_conta_defect_count","total_neps_defect_count","total_needle_drop_defect_count","total_dead_cotton_defect_count","total_thick_thin_defect_count","total_needle_broken_mark_defect_count","total_side_center_shade_defect_count","total_bowing_defect_count","total_uneven_defect_count","total_dia_mark_defect_count","total_dust_defect_count","total_hairy_defect_count","total_gsm_hole_defect_count","total_running_shade_defect_count","total_crease_mark_defect_count","total_loop_out_defect_count","total_cut_hole_defect_count","total_point"],
	 
	   col: [6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32],
	   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML",]

	
		}
	 } 
	
	 
	function chng_val(vall)
	{
		if(vall=1001)
		{
			if(form_validation('txt_date_to','Date From')==false)
				{
					if(form_validation('txt_date_from','Date From')==false)
					{
						return;
					}
				}
				
		}
		if(vall=1002)
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false)
				{
					return;
				}
		}
	}
	function fn_report_generated(type)
	{
		var company=$('#cbo_company_name').val();
		var txt_batch_no=$('#txt_batch_no').val();
		
        //alert(txt_batch_no);

        if (form_validation('cbo_company_name*txt_batch_no','Comapny Name*txt_batch_no')==false)
        {
            release_freezing();
            return;
        }
	
	
		var data="action=report_generate&&report_format="+type+get_submitted_data_string('cbo_company_name*txt_batch_no',"../../");
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/finish_fabric_inspection_report_controller.php",true);
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
			//alert(reponse[1]);
			$('#report_container2').html(reponse[0]);
			setFilterGrid("table_body",-1,tableFilters);
			// setFilterGrid("table_body_show2",-1,tableFilters3);
			// setFilterGrid("table_body_show4",-1,tableFilters4);
			/*if(reponse[2]==1)
			{
				setFilterGrid("table_body",-1,tableFilters);
				setFilterGrid("table_body2",-1,tableFilters2);
			}*/
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	 		show_msg('3');
		}
	}

	
	function new_window()
	{
		/*document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";*/
		
		$('#table_body tr:first').hide();
		$('#table_body_show4 tr:first').hide();
		//$('#table_body2 tr:first').hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		$('#table_body tr:first').show();
		$('#table_body_show4 tr:first').show();
		//$('#table_body2 tr:first').show();
		
		
		/*document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="300px";*/
	}
	
	
</script>

</head>
 
<body onLoad="set_hotkey();">
 <div style="width:100%;" align="center">
<form id="monthly_ex_factory" name="monthly_ex_factory">
    <div style="width:490px;" align="center">
        <? echo load_freeze_divs ("../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:480px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:480px;">
                <table class="rpt_table" width="450" cellpadding="1" cellspacing="2" align="center">
                	<thead>
                    	<tr>                   
                            <th width="140" class="must_entry_caption">Company Name</th>
                            <th width="140" class="must_entry_caption">Batch No</th>
                            <th width="160" colspan="2">
                            <input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:70px" onClick="reset_form('','report_container*report_container2','','','')" />
                            </th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td align="center"> 
							<?
                            echo create_drop_down("cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "Select Company", $selected, "load_drop_down( 'requires/finish_fabric_inspection_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );");
                            ?>
                         </td>
                         
                        <td>
                                <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:140px" placeholder="Write" o  autocomplete="off">
                        </td>
                    

                      <td>
                        <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1);" />
                       
                      </td>
                    </tr>
                    </tbody>
                </table>
              
            </fieldset>
        </div>
    </div>
     </form>

    <!--<div id="report_container" align="center"></div>-->
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
    <!-- <div style="display:none" id="data_panel"></div>   -->
 </div>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
