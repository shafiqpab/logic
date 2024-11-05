<?
/*-------------------------------------------- Comments

Purpose         :   This form will Create Garments Exfectory Return Report
Functionality   :  
JS Functions    :
Created by      :   Md. Saidul Islam Reza
Creation date   :   14-03-2021
Updated by      :       
Update date     :
QC Performed BY :  
QC Date         : 
Comments        :

*/

session_start(); 
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Garments Exfectory Return Report", "../../", 1, 1,$unicode,1,1);

?>
<script src="../../Chart.js-master/Chart.js"></script>
<script>
	
	function open_search_by(str)
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var cbo_company_id=$("#cbo_company_id").val();
		var cbo_buyer_id=$("#cbo_buyer_id").val();
	
		var page_link='requires/garments_ex_feactory_return_report_controller.php?action=search_by_popup&cbo_company_id='+cbo_company_id+'&cbo_buyer_id='+cbo_buyer_id;
		var title="Search by job";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=780px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var job_no=this.contentDoc.getElementById("selected_job_no").value;
			var job_id=this.contentDoc.getElementById("selected_job_id").value;

			if(job_data !== ""){
				$("#txt_job_no").val(job_no);
				$("#hidden_job_id").val(job_id); 
			}
		}
	 }
	
	
	
	
	
	
	var tableFilters = 
		{
/*			col_30: "none",
			col_operation: {
			id: ["tot_qnty"],
			col: [8],
			operation: ["sum"],
			write_method: ["innerHTML"]
			}
*/		} 

	function fn_report_generated()
	{   
		
		
		
		
	/*	if( form_validation('cbo_company_id*txt_job_no','Company Name*Job')==false  &&   form_validation('cbo_company_id*txt_po_no','Company Name*PO')==false &&  form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*Start Date*End Date')==false)
		{
			return;
		}*/
		
		
		if(form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*Start Date*End Date')==false)
		{
			return;
		}
		
		
		
		
	
		var report_title=$( "div.form_caption" ).html(); 
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*txt_job_no*hidden_job_id*txt_po_no*hidden_po_id*cbo_date_type*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/garments_ex_feactory_return_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####"); 
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body",-1,tableFilters);
			release_freezing();
		}
	}

	 
	 
    function new_window()
    {
        document.getElementById('scroll_body').style.overflow="auto";
        document.getElementById('scroll_body').style.maxHeight="none"; 
        $(".flt").css('display','none');
           
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
    '<html><head><link rel="stylesheet" href="../../css/style_print.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
        d.close(); 
        
        document.getElementById('scroll_body').style.overflowY="auto"; 
        document.getElementById('scroll_body').style.maxHeight="400px";
        $(".flt").css('display','block');
      }
	  

</script>
</head>

<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",''); ?>
         <form name="knitDyeingLoadReport_1" id="knitDyeingLoadReport_1"> 
         <h3 style="width:1000px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
             <fieldset style="width:1000px;">
                 <table class="rpt_table" width="1000" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th class="must_entry_caption">Compuny</th>
                            <th>Buyer</th>
                            <th>Job No</th>
                            <th>Order No</th>
                            <th>Date Type</th>
                            <th colspan="2" id="date_type">Return Date</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('knitDyeingLoadReport_1','report_container*report_container2','','','')" class="formbutton" style="width:80px" /></th>
                        </thead>
                        <tbody>
                            <tr class="general" align="center">
                               <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_id", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down('requires/garments_ex_feactory_return_report_controller',this.value, 'load_drop_down_buyer','cbo_buyer_name_td' );" );
                                    ?>
                                </td>
                               <td id="cbo_buyer_name_td"> 
                                    <?
                                        echo create_drop_down( "cbo_buyer_id", 170, "","", 1, "-- Select Buyer --", $selected, "" );
                                    ?>
                                </td>
                                <td>
                                    <input style="width:120px;"  name="txt_job_no" id="txt_job_no"  onDblClick="__open_search_by(1)"  class="text_boxes"  />   
                                    <input type="hidden" name="hidden_job_id" id="hidden_job_id"/>
                                </td>
                                <td>
                                    <input style="width:120px;"  name="txt_po_no" id="txt_po_no"  onDblClick="__open_search_by(2)"  class="text_boxes"   />   
                                    <input type="hidden" name="hidden_po_id" id="hidden_po_id"/>
                                </td>
                                
                               <td> 
                                    <?
                                        $typeArr=array(2=>'Return Date',1=>'Ex-Factory Date');
										echo create_drop_down( "cbo_date_type", 120, $typeArr,"", 0, "-- Select --", $selected, "$('#date_type').text($('#cbo_date_type :selected').text());" );
                                    ?>
                                </td>
                                
                                <td>
                                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" >
                                </td>  
                                <td>
                                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"  placeholder="To Date"  >
                                </td>
                                <td>
                                    <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated()" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="7" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
        </form>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="center"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>