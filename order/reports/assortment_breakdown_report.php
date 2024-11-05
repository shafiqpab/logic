<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Assortment Breakdown Report [Color and Size]
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	30-10-2016
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
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Assortment Breakdown Report [Color and Size]", "../../", 1, 1,$unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';	

	function openmypage_job()
	{
		if(form_validation('cbo_buyer_id','Buyer Name')==false)
		{
			return;
		}
/*		else 
		{	
*/			var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val()+"_"+$("#cbo_year").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/assortment_breakdown_report_controller.php?data='+data+'&action=job_no_popup', 'Job No Search', 'width=700px,height=420px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("txt_job_id");
				var response=theemailid.value.split('_');
				if ( theemailid.value!="" )
				{
					//alert (response[0]);
					freeze_window(5);
					$("#hidd_job_id").val(response[0]);
					$("#txt_job_no").val(response[1]);
					release_freezing();
				}
			}
		//}
	}
	
	function openmypage_po()
	{
		if(form_validation('cbo_buyer_id','Buyer Name')==false)
		{
			return;
		}
/*		else
		{	
*/			var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val()+"_"+$("#txt_job_no").val()+"_"+$("#cbo_year").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/assortment_breakdown_report_controller.php?data='+data+'&action=po_no_popup', 'PO No Search', 'width=500px,height=420px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("txt_po_id");
				var theemailval=this.contentDoc.getElementById("txt_po_val");
				if (theemailid.value!="" || theemailval.value!="")
				{
					//alert (theemailid.value);
					freeze_window(5);
					$("#hidd_po_id").val(theemailid.value);
					$("#txt_po_no").val(theemailval.value);
					release_freezing();
				}
			}
		//}
	}
	
	function fn_report_generated(operation)
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*txt_job_no*hidd_job_id*hidd_po_id*txt_po_no*txt_date_from*txt_date_to*cbo_year*txt_ref_no*txt_file_no',"../../")+'&report_title='+report_title;
			freeze_window(3);
			http.open("POST","requires/assortment_breakdown_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####");
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			//$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	 		show_msg('3');
			release_freezing();
		}
	}
	
	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}
	
	function openmypage_job_color_size(page_link,title)
	{
		//alert("monzu");
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="330px";
	}
	
	function fnRemoveHidden(str){
		document.getElementById(str).value='';
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>		 
    <form name="colorsizebreak_1" id="colorsizebreak_1" autocomplete="off" > 
        <div style="width:100%;" align="center">
            <fieldset style="width:980px;">
            <legend>Search Panel</legend> 
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" align="center" rules="all">
                <thead>                    
                    <th width="150" class="must_entry_caption">Company Name</th>
                    <th width="130">Buyer</th>
                    <th width="80">Year</th>
                    <th width="100">Job No.</th>
                    <th width="100">PO No.</th>
                    <th width="70">File No.</th>
                    <th width="80">Ref No.</th>
                    <th colspan="2">Date</th>
                    <th width="80"><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('colorsizebreak_1','report_container*report_container2','','','')" /></th>
                </thead>
                 <tbody>
                    <tr class="general">
                        <td> 
							<?
								echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down('requires/assortment_breakdown_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
							<? 
								echo create_drop_down( "cbo_buyer_id", 130, $blank_array,"", 1, "--Select Buyer--", $selected, "",1,"" );
                            ?>
                        </td>
                        <td><? echo create_drop_down( "cbo_year", 70, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
                        <td>
                            <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:90px" placeholder="Write/Browse" onChange="fnRemoveHidden('hidd_job_id')" onDblClick="openmypage_job();"  />
                            <input type="hidden" id="hidd_job_id" name="hidd_job_id" style="width:90px" />
                        </td>
                        <td>
                            <input type="text" name="txt_po_no" id="txt_po_no" class="text_boxes" style="width:90px" placeholder="Write/Browse" onChange="fnRemoveHidden('hidd_po_id')" onDblClick="openmypage_po();"  />
                            <input type="hidden" id="hidd_po_id" name="hidd_po_id" style="width:90px" />
                        </td>
                         <td>
                            <input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px" placeholder="Write"   />
                        </td>
                         <td>
                            <input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:80px" placeholder="Write"   />
                        </td>
                        <td align="center">
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date" >
                        </td>
                        <td align="center">
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date"  >
                        </td>
                        <td align="center">
                            <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated(0)" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="10" align="center">
                            <? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </tbody>
            </table> 
        	</fieldset>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2" align="left"></div>
    </form> 
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
