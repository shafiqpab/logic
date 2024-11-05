<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Inhouse Print/Embroidary Production Report .
Functionality	:	
JS Functions	:
Created by		:	Md Mamun Ahmed Sagor
Creation date 	: 	26-04-2021
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
echo load_html_head_contents("Inhouse Print/Embroidary Production Report", "../../", 1, 1,$unicode,'','');

?>	

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';
			

        var tableFilters = 
				 {
					  
					col_operation: {
					   id: ["s_print_qty","s_print_val","s_embl_qty","s_embl_val","s_print_embl_qty","s_print_embl_val","s_issue_print_val","s_issue_embl_val","s_issue_print_embl_val","s_inc_print_val","s_inc_embl_val","s_inc_print_embl_val"],
					   col: [2,3,4,5,6,7,8,9,10,11,12,13],
					   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					},
						
				 }

                 var tableFilters1 = 
				 {
					  
					col_operation: {
					   id: ["print_val","embl_val"],
					   col: [10,13],
					   operation: ["sum","sum"],
					   write_method: ["innerHTML","innerHTML"]
					},
						
				 }
	function fn_report_generated(rptType)
	{
        var buyer = $('#cbo_buyer_name').val();
    
        var job = $('#txt_job_no').val();
        var order = $('#txt_order_no').val();
        var flag = 0;
        if(buyer !=0  || job !="" || order !="")
        {
            flag = 1;
        }
        else
        {            
            if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Comapny Name*From Date*To Date')==false)
            {
                flag = 0;
                return;
            }
        }	
			
		var data="action=report_generate&rptType="+rptType+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*txt_order_no*txt_date_from*txt_date_to',"../../");
		freeze_window(3);
		http.open("POST","requires/inhouse_print_embroidary_production_income_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;	
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
            var response=trim(http.responseText).split("####");
            $("#report_container").html(response[0]);  
            document.getElementById('report_button_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body",-1,tableFilters);		
            setFilterGrid("table_body1",-1,tableFilters1);		
			show_msg('3');
			release_freezing();
		}
	}

    function new_window()
    {
        document.getElementById('scroll_body').style.overflow="auto";
        document.getElementById('scroll_body').style.maxHeight="none";
        $('#table_body tr:first').hide();
        $('#table_body1 tr:first').hide();
        
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
        '<html><head><title></title><link rel="stylesheet" href="../../css/style_print.css" type="text/css" /></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
        d.close(); 
        $('#table_body tr:first').show();
        $('#table_body1 tr:first').show();
        document.getElementById('scroll_body').style.overflowY="scroll";
        document.getElementById('scroll_body').style.maxHeight="330px";
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
	    var page_link='requires/inhouse_print_embroidary_production_income_report_controller.php?action=order_wise_search&company='+company+'&buyer='+buyer+'&style_id='+style_id+'&job_id='+job_id+'&job_no='+job_no+'&cbo_year='+cbo_year; 
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
		var buyer=$("#cbo_buyer_name").val();
		var cbo_year=$("#cbo_year").val();
		var page_link='requires/inhouse_print_embroidary_production_income_report_controller.php?action=job_popup&buyer='+buyer+'&cbo_year='+cbo_year;
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var job_data=this.contentDoc.getElementById("selected_id").value;
			var job_data=job_data.split("_");
			var job_hidden_id=job_data[0];
			var job_no=job_data[1];
			$("#txt_job_no").val(job_no);
			$("#hidden_job_id").val(job_hidden_id); 
		}
	}

    function openmypage_order(issue_num,order_id,job_id,date_from,date_to,action)
	{
		//var garments_nature = $("#cbo_garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/inhouse_print_embroidary_production_income_report_controller.php?order_id='+order_id+'&job_id='+job_id+'&issue_num='+issue_num+'&date_from='+date_from+'&date_to='+date_to+'&action='+action, 'Order Quantity', 'width=750px,height=350px,center=1,resize=0,scrolling=0','../');
	}
	

</script>
</head>
 
<body onLoad="set_hotkey();">
<form id="">
    <div style="width:100%;" align="center"> 

        <? echo load_freeze_divs ("../../",'');  ?>  

         <fieldset style="width:750px;">
        	<legend>Search Panel</legend>
            <table class="rpt_table" width="750px" cellpadding="0" cellspacing="0" align="center">
               <thead>                    
                    <tr>
                        <th width="100"  class="must_entry_caption">Company Name</th>                       
                        <th width="100">Buyer Name</th>                       
                        <th width="80">Job No</th>
                        <th width="80">Order No</th>              
                        <th width="150" id="search_text_td" class="must_entry_caption">Embellishment Rcv. Date</th>
                        <th width="150"><input type="reset" id="reset_btn" class="formbutton" style="width:60px" value="Reset" /></th>
                    </tr>    
                 </thead>
                <tbody>
                <tr class="general">
                    <td> 
                        <?
                            echo create_drop_down( "cbo_company_name", 100, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/inhouse_print_embroidary_production_income_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                        ?>
                    </td>
                   
                    <td id="buyer_td">
                        <? 
                            echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                        ?>
                    </td>
                    
                    <td>
                    	<input type="text"  name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:80px;" onDblClick="open_job_no()" placeholder="Browse/Write">
                    </td>
                    <td>
                    	<input type="text"  name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:80px;" onDblClick="open_order_no()"  placeholder="Browse/Write"  >
                    </td>
                    
                  

                   
                    <td width="">
                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date">&nbsp; To
                    	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date">
                    </td>
                    <td>
                        <input type="button" id="show_button" class="formbutton" style="width:60px" value="Show" onClick="fn_report_generated(1)" />
                       
                    </td>
                </tr>
                </tbody>
            </table>
            <table>
            	<tr>
                	<td colspan="12">
 						<? echo load_month_buttons(1); ?> 
                   	</td>
                </tr>
            </table> 
            <br />
        </fieldset>
    </div>
    <br>
    <div id="report_button_container" align="center" style="margin: 0 auto;padding-bottom:10px;"></div>
    <div id="report_container" align="center" style="margin: 0 auto;"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
