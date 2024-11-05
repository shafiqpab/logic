<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Shift Wise Productin Report.
Functionality	:	
JS Functions	:
Created by		:	Md. Imrul Kayesh
Creation date 	: 	04-10-2021
Updated by 		:  Kamrul Hasan
Update date		:  11-9-2022 
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
echo load_html_head_contents("Shift Wise Productin Report", "../../", 1, 1,$unicode,'','');
$yr = date("Y");
?>	

<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
    function open_style_ref()
 	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer=$("#cbo_buyer_name").val();
		var job_no=$("#txt_job_no").val();
		var job_id=$("#hidden_job_id").val();
		var cbo_year=$("#cbo_year").val();
		var page_link='requires/shift_wise_productin_report_controller.php?action=style_wise_search&company='+company+'&buyer='+buyer+'&job_no='+job_no+'&job_id='+job_id+'&cbo_year='+cbo_year; 
		var title="Search Style Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var style_data=this.contentDoc.getElementById("selected_id").value;
			var style_data=style_data.split("_");
			var style_hidden_id=style_data[0];
			var style_no=style_data[1];
				
			$("#txt_style_no").val(style_no);
			$("#hidden_style_id").val(style_hidden_id); 
		}
 	}	
 	function open_order_no()
	{
		if( form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var job_no=$("#txt_job_no").val();
		var job_id=$("#hidden_job_id").val();
		var company = $("#cbo_company_name").val();	
		var buyer=$("#cbo_buyer_name").val();
		var style_no=$('#txt_style_no').val();
		var style_id=$('#hidden_style_id').val();
		var cbo_year=$("#cbo_year").val();
	    var page_link='requires/shift_wise_productin_report_controller.php?action=order_wise_search&company='+company+'&buyer='+buyer+'&style_id='+style_id+'&job_id='+job_id+'&job_no='+job_no+'&cbo_year='+cbo_year; 
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=580px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			//alert(prodID); // product ID
			var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#txt_order_no").val(prodDescription);
			$("#hidden_order_id").val(prodID); 
		}
	}
		 
	function open_job_no()
	{
		if( form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer=$("#cbo_buyer_name").val();
		var cbo_year=$("#cbo_year").val();
	    var page_link='requires/shift_wise_productin_report_controller.php?action=job_wise_search&company='+company+'&buyer='+buyer+'&cbo_year='+cbo_year;
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var job_data=this.contentDoc.getElementById("selected_id").value;
			//alert(job_data); // Jov ID
			var job_data=job_data.split("_");
			var job_hidden_id=job_data[0];
			var job_no=job_data[1];
			//var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#txt_job_no").val(job_no);
			$("#hidden_job_id").val(job_hidden_id); 
			//alert($("#hidden_job_id").val())
		}
	}

	function generate_report(type)
	{
	
	    if( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
		if($("#txt_date_from").val()=="" && $("#txt_date_to").val()==""  && $("#txt_order_no").val()=="" )
		{
			if( form_validation('txt_date_from*txt_date_to*txt_order_no','From Date*To Date*Order No')==false )
			{
				return;
			}
			
		}

	
		var report_title=$( "div.form_caption" ).html();
		var data="action=generate_report"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_location_name*cbo_shift_name*txt_order_no*hidden_order_id*hidden_job_id*hidden_style_id*txt_date_from*txt_date_to*cbo_year',"../../")+'&report_title='+report_title+'&type='+type;
		freeze_window(3);
		http.open("POST","requires/shift_wise_productin_report_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

			setFilterGrid('table_body_details',-1);
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="auto"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
	}
	function openmypage_remark(po_break_down_id,action)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/shift_wise_productin_report_controller.php?po_break_down_id='+po_break_down_id+'&action='+action, 'Remarks Veiw', 'width=550px,height=450px,center=1,resize=0,scrolling=0','../');
	}

	function reset_form()
	{
		$("#hidden_style_id").val("");
		$("#hidden_order_id").val("");
		$("#hidden_job_id").val("");
		
	}
	 
	$(function()
	{
		var yr = '<? echo $yr;?>';
		$("#cbo_year").val(yr);
	});	 		 	 
</script>

</head>
 
<body onLoad="set_hotkey();">
  	<div style="width:100%;" align="center"> 
   	<? echo load_freeze_divs ("../../",'');  ?>
    <h3 style="width:920px;  margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
       <div style="width:100%;" align="center" id="content_search_panel">
    <form id="dateWiseProductionReport_1">    
      <fieldset style="width:900px;">
            <table class="rpt_table" width="920" cellpadding="0" cellspacing="0" align="center" border="1" rules="all">
               	<thead>                    
                    <tr>
                        <th class="must_entry_caption" width="130">Company Name</th>
                        <th width="120" >Location</th>
                        <th width="120" >Buyer Name</th>
                        <th width="120" >Shift Name</th>
                        <th width="80">Job Year</th>
						<th width="100" style="display:none;">Job No</th>
                        <th width="100" style="display:none;">Style Reference</th>
                        <th width="100">Order Name </th>
                        <th width="170" class="must_entry_caption"> Production Date </th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:60px" value="Reset" onClick="reset_form()"/></th>
                    </tr>   
              	</thead>
                <tbody>
                <tr class="general">
                    <td> 
                        <?
                            echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/shift_wise_productin_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/shift_wise_productin_report_controller',this.value, 'load_drop_down_location', 'location_td' )" );
                        ?>
                    </td>
                    <td id="location_td">
                        <? 
                        echo create_drop_down( "cbo_location_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td id="buyer_td">
                        <? 
                        echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                        ?>
                    </td>
					<td>
					 <?
						echo create_drop_down( "cbo_shift_name", 120, $shift_name,"", 1, "-Select Type-", 0, "",0 );
						?>
                    </td>
                     <td>
                        <?
						
						echo create_drop_down( "cbo_year", 80, $year,"", 1, "Year--", 0, "",0 );
						?>
                    </td>
					<td style="display:none;">
                       <input type="text" id="txt_job_no"  name="txt_job_no"  style="width:90px" class="text_boxes" onDblClick="open_job_no()" placeholder="Browse/Write" />
                    </td>
                    <td style="display:none;">
                     <input type="text" id="txt_style_no"  name="txt_style_no"  style="width:90px" class="text_boxes" onDblClick="open_style_ref()" placeholder="Browse/Write" />
                       	<input type="hidden" id="hidden_style_id"  name="hidden_style_id" />
                       	<input type="hidden" id="hidden_order_id"  name="hidden_order_id" />
                        <input type="hidden" id="hidden_job_id"  name="hidden_job_id" />
                    </td>
                    <td>
                    	<input type="text" id="txt_order_no"  name="txt_order_no"  style="width:90px" class="text_boxes" onDblClick="open_order_no()" placeholder="Browse/Write" />
                    </td>
                     <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" >&nbsp; To
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date"  ></td>
                    <td>
                        <input type="button" id="show_button_po" class="formbutton" style="width:60px" value="Show" onClick="generate_report(1)" />
                        
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
    
 </form> 
 </div>  
    <div id="report_container" style="padding: 10px;"></div>
    <div id="report_container2"></div>  
 </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
