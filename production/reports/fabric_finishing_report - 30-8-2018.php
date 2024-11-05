<?
/*-------------------------------------------- Comments

Purpose			: 	This form will Create Batch Reprot
Functionality	:	
JS Functions	:
Created by		:	Aziz
Creation date 	: 	27-09-2014
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
echo load_html_head_contents("Fabric Finishing Report", "../../", 1, 1,'','','');

?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name", "color_name" ), 0, -1); ?>];

	$(document).ready(function(e)
	 {
            $("#txt_color").autocomplete({
			 source: str_color
		  });
     });

var tableFilters = 
	{
		col_30: "none",
		col_operation: {
		id: ["btg"],
		col: [12],
		operation: ["sum"],
		write_method: ["innerHTML"]
		}
	} 
function fn_dyeing_report_generated(operation)
{
		//alert(operation);
	var b_number=document.getElementById('batch_number').value;
	var batch_no=document.getElementById('batch_number_show').value;
		//alert(batch_number);
	var order_no=document.getElementById('order_no').value;	
	var order_no_hidden=document.getElementById('hidden_order_no').value;	
		
	var j_number=document.getElementById('job_number_show').value;	
	var j_number_hidden=document.getElementById('job_number').value;
	var txt_file_no=document.getElementById('txt_file_no').value;
	var txt_ref_no=document.getElementById('txt_ref_no').value;
	var report_title=$( "div.form_caption" ).html();	
	if(j_number!="" || j_number_hidden!="" || b_number!="" || batch_no!="" || order_no!="" || order_no_hidden!="" || txt_file_no!="" || txt_ref_no!="")
	{
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
	}
	else
	{
		if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From date Fill*To date Fill')==false)
		{
			return;
		}
	}
    freeze_window(5);
    var data="action=fabric_finishing_report&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_buyer_name*job_number_show*job_number*batch_number*batch_number_show*order_no*hidden_order_no*cbo_type*cbo_year*txt_color*txt_date_from*txt_date_to*cbo_shift*txt_file_no*txt_ref_no*cbo_group_by*page_upto*roll_maintained',"../../")+'&report_title='+report_title;
    //alert(data);
    http.open("POST","requires/fabric_finishing_report_controller.php",true);
    http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    http.send(data);
    http.onreadystatechange = fnc_show_batch_report;
	}
	function fnc_show_batch_report()
	{
		if(http.readyState ==4) 
		{
			// alert(http.responseText);
			var reponse=trim(http.responseText).split("****");
			//alert(reponse);
			document.getElementById('report_container2').innerHTML=http.responseText;
			document.getElementById('report_container').innerHTML=report_convert_button('../../');

			//setFilterGrid("table_body",-1,tableFilters);
			release_freezing();
		}
 	}
<!--BatchNumber -->
function batchnumber()
{ 
	if(form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	var company_name=document.getElementById('cbo_company_name').value;
	var batch_number=document.getElementById('batch_number_show').value;
	var page_link="requires/fabric_finishing_report_controller.php?action=batchnumbershow&company_name="+company_name; 
	var title="Batch Number";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=635px,height=400px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("selected_id").value;
		var batch=theemail.split("_");
		document.getElementById('batch_number').value=batch[0];
		document.getElementById('batch_number_show').value=batch[1];
		release_freezing();
	}
}
<!--jobnumber -->
function jobnumber(id)
{ 
	if(form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	var company_name=document.getElementById('cbo_company_name').value;
	var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
	var year=document.getElementById('cbo_year').value;
	var page_link="requires/fabric_finishing_report_controller.php?action=jobnumbershow&company_id="+company_name+"&cbo_buyer_name="+cbo_buyer_name+"&year="+year;
	var title="Job Number";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=615px,height=420px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("selected_id").value;
		//var job=theemail.split("_");
		document.getElementById('job_number').value=theemail;
		document.getElementById('job_number_show').value=theemail;
		release_freezing();
	}
}
function openmypage_order(id)
{ 
	if(form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	var company_name=document.getElementById('cbo_company_name').value;
	var buyer_name=document.getElementById('cbo_buyer_name').value;
	var year=document.getElementById('cbo_year').value;
	var job_number=document.getElementById('job_number_show').value;
	var batch_number=document.getElementById('batch_number_show').value;
	//var ext_number=document.getElementById('txt_ext_no').value;
	var year=document.getElementById('cbo_year').value;
	var page_link="requires/fabric_finishing_report_controller.php?action=order_number_popup&company_name="+company_name+"&buyer_name="+buyer_name+"&year="+year;
	var title="Order Number";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=515px,height=420px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("selected_id").value;
		//var job=theemail.split("_");
		document.getElementById('hidden_order_no').value=theemail;
		document.getElementById('order_no').value=theemail;
		release_freezing();
	}
}
	function toggle( x, origColor ) {
		var newColor = 'green';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}
function js_set_value( str ) {
	toggle( document.getElementById( 'tr_' + str), '#FFF' );
}
function openmypage_color(id)
{ 
	if(form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	var company_name=document.getElementById('cbo_company_name').value;
	var buyer_name=document.getElementById('cbo_buyer_name').value;
	var txtcolor=document.getElementById('txt_color').value;
	var job_number=document.getElementById('job_number_show').value;
	var batch_number=document.getElementById('batch_number_show').value;
	//var ext_number=document.getElementById('txt_ext_no').value;
	var year=document.getElementById('cbo_year').value;
	var page_link="requires/fabric_finishing_report_controller.php?action=check_color_id&txtcolor="+txtcolor;
	var title="Color Name";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=320px,height=350px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{ 
		var theemail=this.contentDoc.getElementById("selected_id").value;
		var split_value=theemail.split('_');
		//alert(theemail);
		document.getElementById('hidden_color_id').value=split_value[0];
		document.getElementById('txt_color').value=split_value[1];
		release_freezing();
	}
}
	function roll_maintain()
	{ 
		var com=$('#cbo_company_name').val();
		//alert(com);
		get_php_form_data($('#cbo_company_name').val(),'roll_maintained_data','requires/fabric_finishing_report_controller' );
		var roll_maintained=$('#roll_maintained').val();
		var page_upto=$('#page_upto').val();
	}
</script>
</head>
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",''); ?>
		 <form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1"> 
         <h3 style="width:1280px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>  <div id="content_search_panel" >      
             <fieldset style="width:1280px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                       		<th class="must_entry_caption">Report Type</th>
                            <th class="must_entry_caption">Company Name</th>
                            <th>Buyer</th>
                            <th>Year</th>
                            <th>Job No</th>
                            <th>Batch No</th>
                            <th>File No</th>
                            <th>Ref. No</th>
                            <th>Order No</th>
                            <th>Color</th>
                            <th>Shift</th>
                            <th>Group by</th>
                            <th class="must_entry_caption">Production Date</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:50px" /></th>
                        </thead>
                        <tbody>
                            <tr>
                            	<td>
                                <? // 9=>"Stentering";
								$search_by_arr=array(0=>"--All--",1=>"Heat Setting",2=>"Slitting/Squeezing",3=>"Drying", 9=>"Stentering",4=>"Compacting",5=>"Special Finish",6=>"Wait For Slitting/Squeezing",10=>"Wait For Stentering",7=>"Wait For Drying",8=>"Wait For Compacting",11=>"Re Stentering",12=>"Re Compacting");
								echo create_drop_down( "cbo_type",120, $search_by_arr,"",0, "", "",'',0 );
								?>
                                </td>
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down('requires/fabric_finishing_report_controller', this.value, 'load_drop_down_buyer', 'cbo_buyer_name_td' );roll_maintain()" );
                                    ?>
                                    <input type="hidden" name="page_upto" id="page_upto" class="text_boxes" style="width:50px;">
                                    <input type="hidden" name="roll_maintained" id="roll_maintained" class="text_boxes" style="width:50px;">
                                </td>
                                <td id="cbo_buyer_name_td">
                                	<?
                                        echo create_drop_down( "cbo_buyer_name", 110, "select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 order by short_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
									?>
                                </td>
                                 <td>
                                	<?
                                       echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
									?>
                                </td>
                                <td>
                                     <input type="text"  name="job_number_show" id="job_number_show" class="text_boxes" style="width:70px;" tabindex="1" 
                                     placeholder="Write/Browse" onDblClick="jobnumber();">
                                     <input type="hidden" name="job_number" id="job_number">
                                </td>
                                <td>
                                     <input type="text"  name="batch_number_show" id="batch_number_show" class="text_boxes" style="width:70px;" tabindex="1" 
                                     placeholder="Write/Browse" onDblClick="batchnumber();">
                                     <input type="hidden" name="batch_number" id="batch_number">
                                </td>
                                 <td>
                                     <input type="text"  name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px;" placeholder="Write">  
                                </td>
                                <td>
                                     <input type="text"  name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:70px;"  placeholder="Write">  
                                </td>
                                
                                 <td>
                                     <input type="text"  name="order_no" id="order_no" class="text_boxes" style="width:70px;" tabindex="1" placeholder="Write/Browse" 
                                     onDblClick="openmypage_order()">
                                     <input type="hidden" name="hidden_order_no" id="hidden_order_no">
                                </td>
                                <td>
                       				 <input type="text"  name="txt_color" id="txt_color" class="text_boxes" style="width:70px;" tabindex="1" placeholder="Write/Browse" 
                       				 onDblClick="openmypage_color()">  <input type="hidden" name="hidden_color_id" id="hidden_color_id">
                                </td>
                                <td>
                                <? 
								 echo create_drop_down( "cbo_shift", 70, $shift_name,"", 1,"-- All --","", "",0,"" );
								
								?>
                                </td>
                                 <td>
								<? 
									$group_type_arr=array(1=>"Floor",2=>"Machine");
									echo create_drop_down("cbo_group_by", 70, $group_type_arr,"", 1, "-Select-", 0, "",0 ,"","","","",""); ?>
                            	</td>
                                <td align="center">
                                     <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:50px" placeholder="From Date"/>
                                     &nbsp;To&nbsp;
                                     <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:50px" placeholder="To Date"/>
                                </td>
                                <td><input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_dyeing_report_generated(0)" /></td>
                            </tr>
                        </tbody>
                    </table>
                    <table>
            	<tr>
                	<td colspan="8">
 						<? echo load_month_buttons(1); ?>
                   	</td>
                </tr>
            </table> 
            <br />
                </fieldset>
         <div id="report_container" align="center"></div>
        <div id="report_container2" align="center"></div>
            </div>
		</form>
     
	</div>
    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>