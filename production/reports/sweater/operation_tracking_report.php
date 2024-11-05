<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Operation Tracking report.
Functionality	:	
JS Functions	:
Created by		:	Shafiq
Creation date 	: 	21-07-2022
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//=================
echo load_html_head_contents("Operation Tracking report", "../../../", 1,1,$unicode,1,1);
?>	
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
    var permission = '<? echo $permission; ?>';
    function open_job_no_popup()
    {
		 if( form_validation('cbo_company_name','Company Name')==false )
		 {
			return;
		 }
		var company = $("#cbo_company_name").val();	
		
		var page_link='requires/operation_tracking_report_controller.php?action=job_no_search_popup&company='+company+'&style=1'; 
		var title="Search Style Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=820px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]; 
				var job_id=this.contentDoc.getElementById("hide_job_id").value;
				var job_no=this.contentDoc.getElementById("hide_job_no").value; // product Description
				console.log(job_id);
				console.log(job_no);
				$("#txt_job_no").val(job_no);
				$("#hidden_job_id").val(job_id); 
			}
	}		

    function generate_report(type)
    {
        //cbo_company_name*cbo_working_company*cbo_buyer_name*cbo_year_selection*txt_style_no*hidden_job_id*txt_cutting_no*txt_date_from*cbo_status*txt_date_to
        if( form_validation('cbo_company_name','Company Name')==false )
        {
            return;
        }
        var job_no = $("#txt_job_no").val();
        var cutting_no = $("#txt_cutting_no").val();
        var qr_code = $("#txt_qr_code").val();
        var op_code = $("#txt_op_code").val();
        if( job_no=="" && cutting_no=="" && qr_code=="" && op_code=="")
        {
           alert("Please enter anyone of search field value.");
           return false;
        }
        var report_title=$( "div.form_caption" ).html();
        var data="action=generate_report"+get_submitted_data_string('cbo_company_name*txt_job_no*hidden_job_id*txt_cutting_no*hidden_ratio_id*txt_qr_code*txt_op_code',"../../../")+'&report_title='+report_title+'&type='+type;
        //alert(data);
        freeze_window(3);
        http.open("POST","requires/operation_tracking_report_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = generate_report_reponse;  
    }
    
    function generate_report_reponse()
    {	
        if(http.readyState == 4) 
        {
            var reponse=trim(http.responseText).split("####");
            show_msg('3');
            release_freezing();
            $("#report_container2").html(reponse[0]);  
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

            var tableFilters = {
                col_operation: {
                id: ["tot_qty","tot_prod","tot_balance"],
                col:  [13,15,16],
                operation: ["sum","sum","sum"],
                write_method: ["innerHTML","innerHTML","innerHTML"]
                }
            }
            setFilterGrid("scroll_body",-1,tableFilters);
            
        } 

    } 
    

    function new_window()
    {
        const el = document.querySelector('#scroll_body');
        if (el) {
            document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="none"; 
            $("#scroll_body tr:first").hide();

        }
        
        //$(".flt").hide();
        
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
        '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="all" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
        d.close(); 
        if (el) {
            document.getElementById('scroll_body').style.overflowY="auto"; 
            document.getElementById('scroll_body').style.maxHeight="400px";
            $("#scroll_body tr:first").show();

        }
        
        //$(".flt").show();
    }

    function reset_form()
    {
        $("#hidden_job_id").val("");
        
    }
    function openmypage_embl(company_id,order_id,order_number,insert_date,type,action,width,height,embl_type,color_id)
	{
        var popup_width=width;
        var popup_height=height;
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/operation_tracking_report_controller.php?company_id='+company_id+'&action='+action+'&insert_date='+insert_date+'&order_id='+order_id+'&order_number='+order_number+'&type='+type+'&embl_type='+embl_type+'&color_id='+color_id, 'Detail Veiw', 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../../../');
		
	} 	


	function openmypage_buyer_ins(style,po_id,action)
	{
		var data=style+'_'+po_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/operation_tracking_report_controller.php?data='+data+'&action='+action, 'Inspection View', 'width=550px,height=300px,center=1,resize=0,scrolling=0','../../../');
	}
	function print_report_button_setting(company_id)
	{

		var report_ids=return_global_ajax_value(company_id, 'print_report_button_setting', '', 'requires/operation_tracking_report_controller');

		$("#show_button").hide();	 
		$("#show_button2").hide();	 

		var report_id=report_ids.split(",");
		if(trim(report_ids))
		{


			for (var k=0; k<report_id.length; k++)
			{
				if(report_id[k]==108)
				{
					$("#show_button").show();	 
				}
				if(report_id[k]==195)
				{
					$("#show_button2").show();	 
				}
			}
		}
		else
		{
			$("#show_button").show();	 
			$("#show_button2").show();	
		}
	}
    function open_cutting_popup()
	{ 
		if( form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		} 
		var company_id=$("#cbo_company_name").val();
		var page_link='requires/operation_tracking_report_controller.php?action=cutting_number_popup&company_id='+company_id; 
		var title="Search Yarn Lot Ratio No Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=420px,center=1,resize=0,scrolling=0',' ../');
		emailwindow.onclose=function()
		{
            var theform=this.contentDoc.forms[0]; 
			var cut_id=this.contentDoc.getElementById("update_mst_id").value.split('_');

			$("#txt_cutting_no").val(cut_id[1]);
			$("#hidden_ratio_id").val(cut_id[0]);
				
		}
	}
    function open_qr_code_popup()
	{ 
		if( form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		} 
		var company_id=$("#cbo_company_name").val();
		var page_link='requires/operation_tracking_report_controller.php?action=bundle_qr_code_popup&company_id='+company_id; 
		var title="Operation QR Code Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=420px,center=1,resize=0,scrolling=0',' ../');
		emailwindow.onclose=function()
		{
            var theform=this.contentDoc.forms[0]; 
			var qr_code=this.contentDoc.getElementById("hidden_bundle_nos").value;

			$("#txt_qr_code").val(qr_code);
				
		}
	}
    function open_op_code_popup()
	{ 
		if( form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		} 
		var company_id=$("#cbo_company_name").val();
		var page_link='requires/operation_tracking_report_controller.php?action=operation_qr_code_popup&company_id='+company_id; 
		var title="Operation QR Code Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1090px,height=420px,center=1,resize=0,scrolling=0',' ../');
		emailwindow.onclose=function()
		{
            var theform=this.contentDoc.forms[0]; 
			var qr_code=this.contentDoc.getElementById("hidden_bundle_nos").value;

			$("#txt_op_code").val(qr_code);
				
		}
	}
    function openmypage_job(job_key,sys_key,color_key,size_key)
	{
		var page_link='requires/operation_tracking_report_controller.php?action=job_popup&job_key='+job_key+'&sys_key='+sys_key+'&color_key='+color_key+'&size_key='+size_key;
		var title="Job POPUP";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1060px,height=370px,center=1,resize=0,scrolling=0','../../../')
	}
    function openmypage_issue(search_string)
	{
		var page_link='requires/operation_tracking_report_controller.php?action=issue_popup&search_string='+search_string;
		var title="Knitting Issue POPUP";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=370px,height=370px,center=1,resize=0,scrolling=0','../../../')
	}
    function openmypage_rcv(search_string)
	{
		var page_link='requires/operation_tracking_report_controller.php?action=receive_popup&search_string='+search_string;
		var title="Knitting Receive  POPUP";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=370px,height=370px,center=1,resize=0,scrolling=0','../../../')
	}

	$(function(){
		$("#cbo_status").val(1);
	}) ;	 	 
</script>

</head>
 
<body onLoad="set_hotkey();">
  <div style="width:100%;" align="center"> 
   <? echo load_freeze_divs ("../../../",'');  ?>
    <h3 style="width:700px;  margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
       <div style="width:100%;" align="center" id="content_search_panel">
    <form id="dateWiseProductionReport_1">    
      <fieldset style="width:700px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" align="center">
               <thead>                    
                       <tr>
                        <th class="must_entry_caption" width="150"><strong >Company Name</strong></th>
                        <th width="110">Job No</th>
                        <th width="120">Lot Ratio No</th>
                        <th width="120">Bundle QR Code</th>
                        <th width="120">Operation QR Code</th>                      
                        <th width="70">
                            <input type="reset" id="reset_btn" class="formbutton" style="width:60px" value="Reset" onClick="reset_form()"/>
                        </th>
                    </tr>   
              </thead>
                <tbody>
                <tr class="general">
                    <td> 
                        <?
                            echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                        ?>
                    </td>
                    
                    <td>
                        <input type="text" id="txt_job_no"  name="txt_job_no"  style="width:85%" class="text_boxes" onDblClick="open_job_no_popup()" placeholder="Browse/Write" />
                       <input type="hidden" id="hidden_job_id"  name="hidden_job_id" />                        
                    </td>
                    <td>
                        <input type="text" name="txt_cutting_no" id="txt_cutting_no" class="text_boxes" style="width:110px" placeholder="Browse/Write" onDblClick="open_cutting_popup()" />
                       <input type="hidden" id="hidden_ratio_id"  name="hidden_ratio_id" /> 
                    </td> 
                    <td>
                        <input type="text" name="txt_qr_code" id="txt_qr_code" class="text_boxes" style="width:110px" placeholder="Browse/Write" onDblClick="open_qr_code_popup()"/>
                    </td> 
                    <td>
                        <input type="text" name="txt_op_code" id="txt_op_code" class="text_boxes" style="width:110px" placeholder="Browse/Write" onDblClick="open_op_code_popup()" />
                    </td>                   
                  
                    <td width="70">
                        <input type="button" id="show_button" class="formbutton" style="width:60px;" value="Show" onClick="generate_report(1)" />
                        
                    </td>
                </tr>
                </tbody>
            </table>
      </fieldset>
      <div   id="report_container" align="center" style="margin: 5px 0;"></div>
      <div id="report_container2" ></div>  
 </form> 
 </div>   
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
