<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Sewing MIS Report
				
Functionality	:	
JS Functions	:
Created by		:	Ashraful
Creation date 	: 	14-06-2017
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
echo load_html_head_contents("Sewing MIS Report","../../", 1, 1, $unicode,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(type)
	{
		if( form_validation('cbo_company_id*txt_date','Company Name*Production Date')==false )
		{
			return;
		}

		var report_title=$( "div.form_caption" ).html();
		var action="";
		if(type==1){
			action="report_generate_month";
		}
		else if (type==2){
			 action="report_generate_show2";
		}
		else if (type==3){
			 action="report_generate_show3";
		}
		
		var data="action="+action+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_floor_id*cbo_line*hidden_line_id*txt_date*txt_parcentage',"../../")+'&report_title='+report_title;
	
		freeze_window(3);
		http.open("POST","requires/sewing_mis_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			//alert (http.responseText);
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			
			release_freezing();
			//document.getElementById('factory_efficiency').innerHTML=document.getElementById('total_factory_effi').innerHTML;
		//	document.getElementById('factory_parfomance').innerHTML=document.getElementById('total_factory_per').innerHTML;
			//alert(reponse[1]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			show_msg('3');
			release_freezing();
		}
	} 

	function generate_Graph()
	{
		if( form_validation('cbo_company_id*txt_date','Company Name*Production Date')==false )
		{
			return;
		}

		var data='dfdsfds';
		var page_link='requires/sewing_mis_report_controller.php?action=open_selection_popup&data='+data;
		//var page_link='graph_grp.php?action=opendate_popup';
		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, 'Graph Selection', 'width=210px, height=150px, center=1, resize=0, scrolling=0','')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("cbo_copy_type").value;
			
			if(theemail!='')
			{
				var company_id=$("#cbo_company_id").val();
				var cbo_location_id=$("#cbo_location_id").val();
				var floor_id=$("#cbo_floor_id").val();
				var txt_date=$("#txt_date").val();
		
				
				window.open('sewing_mis_report_controller.php?action=generate_report_graph'+'&cbo_company_id='+company_id+'&bundle_copyes='+theemail+'&txt_date='+txt_date+'&cbo_location_id='+cbo_location_id+'&cbo_floor_id='+floor_id, "MY PAGE");
			}
			else
			{
				alert('Please Select Date.');
				return;
			}
			//window.open("requires/knitting_bill_issue_controller.php?data=" + data+'&action='+action, true );
			//alert(theemail);
		} 
	}


	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><link rel="stylesheet" href="../../amchart/plugins/export.css" type="text/css" media="all" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="auto"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
	}
		
	function openmypage_line()
	{
		if( form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company = $("#cbo_company_id").val();	
		var location=$("#cbo_location_id").val();
		var floor_id=$("#cbo_floor_id").val();
		var txt_date=$("#txt_date").val();
		var page_link='requires/sewing_mis_report_controller.php?action=line_search_popup&company='+company+'&location='+location+'&floor_id='+floor_id+'&txt_date='+txt_date; 
		
		var title="Search line Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=310px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			
			var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#cbo_line").val(prodDescription);
			$("#hidden_line_id").val(prodID); 
		}
	}

	function print_report_button_setting(report_ids) 
    {
     
        $('#Show3').hide();
        $('#Show2').hide();
        $('#Show1').hide();
        $('#Graph').hide();

        var report_id=report_ids.split(",");
        report_id.forEach(function(items){
            if(items==242){$('#Show3').show();}
            if(items==259){$('#Show2').show();}
            if(items==147){$('#Show1').show();}
            if(items==763){$('#Graph').show();}
            });
    }
	 
	 function openmypage(company_id,job_data,type,floor_id,line_no,action,item_smv,actual_time,line_date,prod_date)
		{
			 popup_width='550px'; 

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sewing_mis_report_controller.php?job_data='+job_data+'&company_id='+company_id+'&action='+action+'&floor_id='+floor_id+'&sewing_line='+line_no+'&prod_date='+prod_date+'&item_smv='+item_smv+'&type='+type+'&line_date='+line_date+'&actual_time='+actual_time, 'Detail Veiw', 'width='+popup_width+', height=270px,center=1,resize=0,scrolling=0','../');
		}
		
		function openmypage2(company_id,order_id,subcon_order,floor_id,line_no,action,item_smv,actual_time,line_date,prod_date)
		{
			 popup_width='550px'; 

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sewing_mis_report_controller.php?po_id='+order_id+'&company_id='+company_id+'&action='+action+'&floor_id='+floor_id+'&sewing_line='+line_no+'&prod_date='+prod_date+'&item_smv='+item_smv+'&subcon_order='+subcon_order+'&line_date='+line_date+'&actual_time='+actual_time, 'Detail Veiw', 'width='+popup_width+', height=270px,center=1,resize=0,scrolling=0','../');
		}
	 
	  	function generate_style_popup(style,po_id,subcon_order,res_mst_id,floor_id,item_id,prod_reso_allo,prod_date,action,i)
		{
			 popup_width='1120px'; 
			var company_id = $("#cbo_company_id").val();	
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sewing_mis_report_controller.php?po_id='+po_id+'&company_id='+company_id+'&action='+action+'&floor_id='+floor_id+'&item_id='+item_id+'&style='+style+'&prod_reso_allo='+prod_reso_allo+'&sewing_line='+res_mst_id+'&prod_date='+prod_date+'&subcon_order='+subcon_order, 'Detail Veiw', 'width='+popup_width+', height=390px,center=1,resize=0,scrolling=0','../');
		}

		function show_remarks_popup(mst_id,style)
		{
			 popup_width='400px'; 
			var company_id = $("#cbo_company_id").val();	
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sewing_mis_report_controller.php?mst_id='+mst_id+'&style='+style+'&action=show_remarks_popup', 'Remarks Veiw', 'width='+popup_width+', height=390px,center=1,resize=0,scrolling=0','../');
		}
</script>
</head>
<body onLoad="set_hotkey();">

<form id="LineWiseProductivityAnalysis_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:800px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:860px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="180" class="must_entry_caption">Company</th>
                    <th class="must_entry_caption">Production Date</th>
                    <th width="150">Location</th>
                    <th width="160">Floor</th>
                    <th width="120" style="display:none">Line No</th>
                    <th width="70">Efficiency %</th>
                    <th width="240" colspan="2"><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('LineWiseProductivityAnalysis_1','report_container','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                       <td><?  echo create_drop_down( "cbo_company_id", 180, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/sewing_mis_report_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data(this.value,'print_button_variable_setting','requires/sewing_mis_report_controller' );" ); ?></td>
                        <td><input type="text" name="txt_date" id="txt_date" class="datepicker" style="width:70px;" readonly/></td>
                        <td id="location_td"><? echo create_drop_down( "cbo_location_id", 140, $blank_array,"", 1, "-- Select Location --", "", "" ); ?></td>
                        <td id="floor_td"><? echo create_drop_down( "cbo_floor_id", 160, $blank_array,"", 1, "-- Select Floor --", "", "" ); ?></td>
                        <td id="line_td" style="display:none">
                               <input type="text" id="cbo_line" name="cbo_line" style="width:120px" class="text_boxes" onDblClick="openmypage_line();" placeholder="Browse"  readonly/>
                               <input type="hidden" id="hidden_line_id" name="hidden_line_id" />
                        </td>
                        <td><input type="text" id="txt_parcentage" name="txt_parcentage" class="text_boxes_numeric" style="width:50px; text-align:left" value="60"/></td>
                        <td>
                            <input type="button" name="Show1" id="Show1" value="Show" onClick="generate_report(1)" style="width:60px;display:none;" class="formbutton" />
							<input type="button" name="Graph" id="Graph" value="Graph" onClick="generate_Graph()" style="width:60px; display:none;" class="formbutton" />
							<input type="button" name="Show3" id="Show3" value="Show3" onClick="generate_report(3)" style="width:60px;display:none;" class="formbutton" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    	</div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left">
    	<div style="float:left; " id="report_container3"></div>
    </div>
 </form>   
</body>
<script>set_multiselect('cbo_floor_id','0','0','','0'); </script> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
<script> $("#cbo_location_id").val(0); </script>
</html>
