<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Date Wise Dyes Chamical Receive Issue Report
				
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	10/02/2015
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
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Dyes Chamical Receive Issue","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";

	var tableFilters = 
	 {
		col_30: "none",
		col_operation: {
		id: ["value_total_receive","value_total_issue"],
	   col: [15,16],
	   operation: ["sum","sum"],
	   write_method: ["innerHTML","innerHTML"]
		}
	 }
	  

	function openmypage_style()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();
		var txt_style_ref_no = $("#txt_style_ref_no").val();
		var txt_style_ref_id = $("#txt_style_ref_id").val();
		var txt_style_ref = $("#txt_style_ref").val();
		var cbo_year = $("#cbo_year").val();
		//var txt_style_ref_no = $("#txt_style_ref_no").val();
		var page_link='requires/date_wise_dyes_chami_recv_issue_report_controller.php?action=style_refarence_surch&company='+company+'&buyer='+buyer+'&txt_style_ref_no='+txt_style_ref_no+'&txt_style_ref_id='+txt_style_ref_id+'&txt_style_ref='+txt_style_ref+'&cbo_year='+cbo_year;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			$("#txt_style_ref").val(style_des);
			$("#txt_style_ref_id").val(style_id); 
			$("#txt_style_ref_no").val(style_no); 
		}
	}
	
	
	function openmypage_order()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();	
		var txt_order_id_no = $("#txt_order_id_no").val();
		var txt_order_id = $("#txt_order_id").val();
		var txt_order = $("#txt_order").val();
		var txt_style_ref_no = $("#txt_style_ref").val();
		var txt_style_ref_id = $("#txt_style_ref_id").val();
		var cbo_year = $("#cbo_year").val();
		
		var page_link='requires/date_wise_dyes_chami_recv_issue_report_controller.php?action=order_surch&company='+company+'&buyer='+buyer+'&txt_order_id_no='+txt_order_id_no+'&txt_order_id='+txt_order_id+'&txt_order='+txt_order+'&txt_style_ref_id='+txt_style_ref_id+'&txt_style_ref_no='+txt_style_ref_no+'&cbo_year='+cbo_year; 
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=520px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			$("#txt_order").val(style_des);
			$("#txt_order_id").val(style_id); 
			$("#txt_order_id_no").val(style_des_no);
		}
	}
	

function  generate_report(rptType)
{
	var cbo_item_cat = $("#cbo_item_cat").val();
	var cbo_company_name = $("#cbo_company_name").val();
	var cbo_buyer_name = $("#cbo_buyer_name").val();
	var txt_style_ref = $("#txt_style_ref").val();
	var txt_style_ref_id = $("#txt_style_ref_id").val();	
	var txt_order = $("#txt_order").val();
	var txt_order_id = $("#txt_order_id").val();
	var txt_batch = $("#txt_batch").val();
	var txt_date_from = $("#txt_date_from").val(); 
	var txt_date_to = $("#txt_date_to").val();
	var cbo_year = $("#cbo_year").val();
	var cbo_based_on = $("#cbo_based_on").val();
	
	if(rptType==3)
	{
		if(txt_style_ref!="" || txt_order!="" || txt_batch!="" )
		{
			if( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
		}
		else
		{
			if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Form Date*To Date')==false )
			{
				return;
			}
		}
	}
	else
	{
		if(txt_style_ref!="" || txt_order!="" || txt_batch!="")
		{
			alert("Job Or Order Not Allow");
			$("#txt_style_ref").val("");
			$("#txt_style_ref_id").val("");
			$("#txt_order").val("");
			$("#txt_order_id").val("");
			$("#txt_batch").val("");
			return;
		}
		else
		{
			if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Form Date*To Date')==false )
			{
				return;
			}
		}
	}
	
	
	
	 
	var dataString = "&cbo_item_cat="+cbo_item_cat+"&cbo_company_name="+cbo_company_name+"&cbo_buyer_name="+cbo_buyer_name+"&cbo_year="+cbo_year+"&txt_style_ref="+txt_style_ref+"&txt_style_ref_id="+txt_style_ref_id+"&txt_order="+txt_order+"&txt_order_id="+txt_order_id+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&cbo_based_on="+cbo_based_on+"&txt_batch="+txt_batch+"&rptType="+rptType;
	var data="action=generate_report"+dataString;
	freeze_window(5);
	http.open("POST","requires/date_wise_dyes_chami_recv_issue_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_report_reponse; 
}

function generate_report_reponse()
{	
	if(http.readyState == 4) 
	{
		//alert(http.responseText);	 
		var reponse=trim(http.responseText).split("**");
		//alert(reponse[2]);
		$("#report_container2").html(reponse[0]);
		//document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		document.getElementById('report_container').innerHTML=report_convert_button('../../../');
		append_report_checkbox('table_header_1',1);
		setFilterGrid("table_body",-1,tableFilters);
		release_freezing();
		show_msg('3');
		//document.getElementById('report_container').innerHTML=report_convert_button('../../../');
	}
} 

	/*function new_window()
	{
		 
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tr:first').hide(); 
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="250px";
		$('#table_body tr:first').show();
	}*/


function fn_change_base(str)
{
	if(str==1)
	{
		$("#up_tr_date").html("");
		$("#up_tr_date").html("Transaction Date Range").attr('style','color:blue');
	}
	else
	{
		$("#up_tr_date").html("");
		$("#up_tr_date").html("Insert Date Range").attr('style','color:blue');
	}
}
	
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission);  ?><br />    		 
    <form name="item_receive_issue_1" id="item_receive_issue_1" autocomplete="off" >
    <h3 align="left" id="accordion_h1" style="width:1130px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div style="width:1130px;" align="center" id="content_search_panel">
        <fieldset style="width:1130px;">
                <table class="rpt_table" width="1120" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                    	<th width="100" >Item Category</th>
                        <th width="100" class="must_entry_caption">Company</th>                                
                        <th width="100" >Buyer Name</th>
                        <th width="60">Job Year.</th>
                        <th width="80">Job NO.</th>
                        <th width="80" >Order No.</th>
                        <th width="80" >Batch No</th>
                        <th width="80">Based On</th>
                        <th width="180" id="up_tr_date" class="must_entry_caption">Transaction Date Range</th>
                        <th width="80" style="display:none;">Order Type</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:90px" class="formbutton" onClick="reset_form('item_receive_issue_1','report_container2','','','','');" /></th>
                    </tr>
                </thead>
                <tr class="general">
                	<td>
						<?
                        	echo create_drop_down( "cbo_item_cat", 100, $item_category,"", 1, "-- ALL --", $selected, "",0,"5,6,7" );
                        ?>
                    </td>
                    <td>
                            <?
                        	echo create_drop_down( "cbo_company_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/date_wise_dyes_chami_recv_issue_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                        ?>                          
                    </td>
                    <td id="buyer_td"><? 
                        	echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                        ?></td>
                    
                     <td>
						<?
                            echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
                        ?>
                    </td>
                    <td align="center">
                        <input style="width:80px;"  name="txt_style_ref" id="txt_style_ref"  ondblclick="openmypage_style()"  class="text_boxes" placeholder="Browse or Write"   />   
                        <input type="hidden" name="txt_style_ref_id" id="txt_style_ref_id"/>    <input type="hidden" name="txt_style_ref_no" id="txt_style_ref_no"/>            
                    </td>
                    
                     <td align="center">
                        <input type="text" style="width:80px;"  name="txt_order" id="txt_order"  ondblclick="openmypage_order()"  class="text_boxes" placeholder="Browse or Write"   />   
                        <input type="hidden" name="txt_order_id" id="txt_order_id"/> <input type="hidden" name="txt_order_id_no" id="txt_order_id_no"/>               
                    </td>
                    <td align="center">
                        <input type="text" style="width:80px;"  name="txt_batch" id="txt_batch" class="text_boxes" />   
                        
                    </td>
                    <td>
                    	<?
						$base_on_arr=array(1=>"Transaction Date",2=>"Insert Date");
                        echo create_drop_down( "cbo_based_on", 80, $base_on_arr,"", 0, "", 1, "fn_change_base(this.value);",0 );
                        ?>
                    </td>
                    <td>
                    	<input type="date" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px;" placeholder="From Date" readonly/>&nbsp; TO &nbsp;
                    	<input type="date" name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" style="width:60px;" readonly/>
                    </td>
                    
                    <td style="display:none;">
                    	<?
						$order_type=array(1=>"With Order",2=>"Without Order");
                        echo create_drop_down( "cbo_order_type", 80, $order_type,"", 1, "ALL", 0, "",0 );
                        ?>
                    </td>
                    
                    <td>
                        <input type="button" name="search" id="search" value="All" onClick="generate_report(1)" style="width:60px" class="formbutton" />
                        <input type="button" name="search" id="search" value="Receive" onClick="generate_report(2)" style="width:60px" class="formbutton" />
                        <input type="button" name="search" id="search" value="Issue" onClick="generate_report(3)" style="width:60px" class="formbutton" />
                    </td>
                </tr>
                <tr>
                	<td colspan="12" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                </tr>
                
            </table> 
        </fieldset> 
           
    </div>
        <!-- Result Contain Start-------------------------------------------------------------------->
        	<div id="report_container" align="center"></div>
            <div id="report_container2"></div> 
        <!-- Result Contain END-------------------------------------------------------------------->
    
    
    </form>    
</div>    
</body>
<!--<script>
	set_multiselect('cbo_source','0','0','','0');
</script>  
-->
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
