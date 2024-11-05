<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Process Wise Hourly Production Monitoring Report
				
Functionality	:	
JS Functions	:
Created by		:	Md. Helal Uddin
Creation date 	: 	23-10-2022
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
echo load_html_head_contents("Process Wise Hourly Production Monitoring Report","../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(type)
	{
		var txt_style_ref = document.getElementById('txt_style_ref').value ;
		var txt_job_no = document.getElementById('txt_job_no').value ;
		var hidd_job_id = document.getElementById('hidd_job_id').value ;
		var txt_wo_no = document.getElementById('txt_wo_no').value ;
		var hidd_wo_id = document.getElementById('hidd_wo_id').value ;
		var txt_fso_no = document.getElementById('txt_fso_no').value ;
		var hide_fso_id = document.getElementById('hide_fso_id').value ;
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		else if( txt_style_ref.length < 1 &&  txt_job_no.length < 1 &&  hidd_job_id.length < 1 &&  txt_wo_no.length < 1 &&  hidd_wo_id.length < 1 &&  txt_fso_no.length < 1 &&  hide_fso_id.length < 1 )
		{
			if( form_validation('txt_date_from*txt_date_to','Date*Date')==false )
			{
				return;
			}
		}

		var report_title=$( "div.form_caption" ).html();
		var action = "report_generate";
		var data="action="+action+get_submitted_data_string('cbo_company_id*txt_job_no*hidd_job_id*txt_style_ref*txt_wo_no*hidd_wo_id*txt_fso_no*hide_fso_id*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/stripe_measurement_report_controller.php",true);
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
			//alert(reponse[1]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		var show_hide = [];
		var proc = [1,2];
		for( let pr in proc)
		{
			const el = document.querySelector("#table_body"+proc[pr]);
			if (el)
			{
				if ($("#table_body"+proc[pr]+" tbody tr:first").attr('class')=='fltrow')
				{
					show_hide.push(proc[pr]);
					console.log(proc[pr]+'=>yes');
					$("#table_body"+proc[pr]+" tbody tr:first").hide();
					if(proc[pr] == 1) document.getElementById("scroll_body"+proc[pr]).style.marginLeft="15px";
					document.getElementById("scroll_body"+proc[pr]).style.overflow="auto";
					document.getElementById("scroll_body"+proc[pr]).style.maxHeight="none";
				}
				else{
					console.log(proc[pr]+'=>no');
				}
			}
		}
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		for( let pr in proc)
		{
			const el = document.querySelector("#table_body"+proc[pr]);
			if (el)
			{
				if(show_hide.includes(proc[pr]) )
				{	
					console.log(proc[pr]+'=>yes');
					$("#table_body"+proc[pr]+" tbody tr:first").show();
					if(proc[pr] == 1) document.getElementById("scroll_body"+proc[pr]).style.marginLeft="0px";
					document.getElementById('scroll_body'+proc[pr]).style.overflowY="auto"; 
					document.getElementById('scroll_body'+proc[pr]).style.maxHeight="380px";
				}
				else
				{
					console.log(proc[pr]+'=>no');
				}
			}
		}
	}
	 
	function openmypage_job()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		var data = $("#cbo_company_id").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/stripe_measurement_report_controller.php?data='+data+'&action=job_no_popup', 'Job No Search', 'width=700px,height=420px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theemailid=this.contentDoc.getElementById("txt_job_id");
			var response=theemailid.value.split('_');
			if ( theemailid.value!="" )
			{
				$("#hidd_job_id").val(response[0]);
				$("#txt_job_no").val(response[1]);
			}
		}
	}
	function fnRemoveHidden(str){
		document.getElementById(str).value='';
	}

	function openmypage_wo()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		else
		{	
			var data = $("#cbo_company_id").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/stripe_measurement_report_controller.php?data='+data+'&action=wo_no_popup', 'Wo No Search', 'width=650px,height=420px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("txt_wo_id");
				var theemailval=this.contentDoc.getElementById("txt_wo_no");
				//var response=theemailid.value.split('_');
				if ( theemailval.value!="" )
				{
					//alert (response[0]);
					freeze_window(5);
					$("#hidd_wo_id").val(theemailid.value);
					$("#txt_wo_no").val(theemailval.value);
					release_freezing();
				}
			}
		}
	}

	function openmypage_fso()
	{
        var companyID = $("#cbo_company_id").val();
        
		
        if (form_validation('cbo_company_id', 'Company Name') == false)
		{
            return;
        }

        var page_link = 'requires/stripe_measurement_report_controller.php?action=actn_job_popup&companyID=' + companyID ;
        ;
        var title = 'Sales Order Search';
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=730px,height=400px,center=1,resize=1,scrolling=0', '../');
        emailwindow.onclose = function ()
		{
            var theform = this.contentDoc.forms[0];
            var fso_no = this.contentDoc.getElementById("hide_fso_no").value;
            var fso_id = this.contentDoc.getElementById("hide_fso_id").value;

            $('#txt_fso_no').val(fso_no);
            $('#hide_fso_id').val(fso_id);
        }
    }
	 
</script>

</head>
<body onLoad="set_hotkey();">

<form id="LineWiseProductivityAnalysis_1">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
         
         <h3 style="width:1030px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1030px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="160" class="must_entry_caption">Company</th>
                    <th width="150">JOB NO.</th>
                    <th width="150">Style</th>
                    <th width="160">Fabric Booking NO.</th>
                    <th width="140">FSO NO.</th>
                    <th width="140" colspan="2">Date Range</th>
                    <th width="150"><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('LineWiseProductivityAnalysis_1','report_container','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                       <td>
							<? 
                                echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "" );
                            ?>                            
                        </td>
                         <td>
                            <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px" placeholder="Write/Browse" onChange="fnRemoveHidden('hidd_job_id');" onDblClick="openmypage_job();"  />
                            <input type="hidden" id="hidd_job_id" name="hidd_job_id" style="width:40px" />
                        </td>
                        <td > 
                            <input type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:110px;" placeholder="Write" /> 
                        </td>
                        <td >
                            <input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:100px" placeholder="Write/Browse" onDblClick="openmypage_wo();" onChange="fnRemoveHidden('hidd_wo_id');" />
                            <input type="hidden" id="hidd_wo_id" name="hidd_wo_id" style="width:50px" />                  
                        </td>
                        <td >
                            <input type="text" name="txt_fso_no" id="txt_fso_no" class="text_boxes" style="width:110px" placeholder="Write/Browse" onDblClick="openmypage_fso();" onChange="fnRemoveHidden('hide_fso_id');" autocomplete="off">
                        	<input type="hidden" name="hide_fso_id" id="hide_fso_id" readonly>                             
                        </td>
                       <td>
                       		<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" >
                       </td>
                        <td>
                        	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To Date" >
                        </td>
                        
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:50px" class="formbutton" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    	</div>
    </div>
    <div id="report_container" align="center" style="padding:5px 0"></div>
    <div id="report_container2" align="left" style="margin-left:80px"></div>
 </form>  
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
