<?
/*-------------------------------------------- Comments

Purpose         :   This form will Create Roll Position Tracking Report
Functionality   :  
JS Functions    :
Created by      :   Tfzzl
Creation date   :   12-05-2017
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
echo load_html_head_contents("Roll Position Tracking Report", "../../", 1, 1,$unicode,1,1);
//echo load_html_head_contents("Supplier Info", "../../", 1, 1, $unicode,1,'');

?>
<script src="../../Chart.js-master/Chart.js"></script>
<script>
	var tableFilters = 
		{
			col_30: "none",
			col_operation: {
			id: ["tot_qnty"],
			col: [8],
			operation: ["sum"],
			write_method: ["innerHTML"]
			}
		} 

	function fn_report_generated()
	{   
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		if( $("#txt_job_no").val() == '' ){
	
			if( form_validation('txt_date_from*txt_date_to','txt_date_from*txt_date_to')==false )//&& $("#txt_cutting_no").val() == ''
			{
				return;
			}
	
		}
	
		var report_title=$( "div.form_caption" ).html(); 
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*hidden_floor_id*txt_date_to*txt_date_from*txt_job_no*hidden_job_id*hidden_style_no*hidden_cut_no*hidden_po_no',"../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/bundle_under_sewing_wip_report_controller.php",true);
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
			setFilterGrid("tbl_list_search",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}

	

	function generate_report(company,challan_no,type)
	{	
		var delivery_basis =3;
		var report_title="Bundle Wise Sewing Input";//report_title
		// generate_report_file(company + '*' + challan_no + '*' + delivery_basis + '*' + report_title,type, '../requires/bundle_wise_sewing_input_controller');
        var param = company + '*' + challan_no + '*' + delivery_basis + '*' + report_title;
			// alert(type);

			var zero_val=1;	
			var path='../../';	
		var data="action="+type+"&data="+param+"'&img_path="+path;
		// alert(data);
		
		
		
		http.open("POST","../requires/bundle_wise_sewing_input_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_report_reponse;
	}
	
	function fnc_generate_report_reponse()
	{
		if(http.readyState == 4) 
		{
			/*var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><body>'+http.responseText+'</body</html>');
			d.close();*/
			var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+http.responseText+'</body</html>');
				d.close();
		}
	}

	function open_search_by()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
	
		var cbo_company=$("#cbo_company_name").val();
		var txt_cutting=$("#txt_cutting_no").val();
		var txt_floor=$("#hidden_floor_id").val();
	
		var page_link='requires/bundle_under_sewing_wip_report_controller.php?action=search_by_popup&cbo_company='+cbo_company+'&txt_floor='+txt_floor;
		var title="Search By Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=780px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var job_data=this.contentDoc.getElementById("selected_job").value;
			var job_style=this.contentDoc.getElementById("selected_style").value;
			var job_po=this.contentDoc.getElementById("selected_po").value;
			var job_cut=this.contentDoc.getElementById("selected_cut").value;
			// alert(job_cut); // Jov ID
			// var job_data=job_data.split("_");
			// var job_hidden_id=job_data[0];
			// var job_no=job_data[1];
			// var hidden_style_no=job_data[2];
			// var hidden_cut_no=job_data[3];
			// var hidden_po_no=job_data[4];
	
			//var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			if(job_data !== ""){
				$("#txt_job_no").val(job_data);
				$("#hidden_job_id").val(job_data); 
				$("#hidden_style_no").val(job_style); 
				$("#hidden_cut_no").val(job_cut); 
				$("#hidden_po_no").val(job_po); 
			}
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
    '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
        d.close(); 
        
        document.getElementById('scroll_body').style.overflowY="auto"; 
        document.getElementById('scroll_body').style.maxHeight="400px";
        $(".flt").css('display','block');
      }
	  
	function open_floor_name()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
	
		var cbo_company=$("#cbo_company_name").val();
	
		var page_link='requires/bundle_under_sewing_wip_report_controller.php?action=floor_popup&cbo_company='+cbo_company;
		var title="Search By Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=330px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var floor_id=this.contentDoc.getElementById("hid_floor_id").value;
			var floor_name=this.contentDoc.getElementById("hid_floor_name").value;
			
			if(floor_name != ""){
				freeze_window(5);
				$("#hidden_floor_id").val(floor_id);
				$("#txt_floor_name").val(floor_name); 
				release_freezing();
			}
		}
	 }

</script>
</head>

<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",''); ?>
         <form name="knitDyeingLoadReport_1" id="knitDyeingLoadReport_1"> 
         <h3 style="width:780px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
             <fieldset style="width:780px;">
                 <table class="rpt_table" width="780" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th class="must_entry_caption" width="170">Working Compuny</th>
                            <th  width="120">Search By</th>
                            <!-- <th  width="120">Cutting No</th> -->
                            <th  width="120">Floor Name</th>
                            <th  id="search_text_td" class="must_entry_caption" colspan="2">Input Date Range</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('knitDyeingLoadReport_1','report_container*report_container2','','','')" class="formbutton" style="width:80px" /></th>
                        </thead>
                        <tbody>
                            <tr class="general" align="center">
                               <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                                    ?>
                                </td>
                                <td>
                                    <input style="width:120px;"  name="txt_job_no" id="txt_job_no"  onDblClick="open_search_by()"  class="text_boxes" placeholder="Browse"  />   
                                    <input type="hidden" name="hidden_job_id" id="hidden_job_id"/>
                                    <input type="hidden" name="hidden_style_no" id="hidden_style_no"/>
                                    <input type="hidden" name="hidden_cut_no" id="hidden_cut_no"/>
                                    <input type="hidden" name="hidden_po_no" id="hidden_po_no"/>
                                </td>
                                <td>
                                	<input type="hidden" name="hidden_floor_id" id="hidden_floor_id"/>
                                    <input name="txt_floor_name" id="txt_floor_name" class="text_boxes" style="width:140px" placeholder="Browse" onDblClick="open_floor_name();" readonly >
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
<script>
	set_multiselect('cbo_company_name','0','0','0','0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>