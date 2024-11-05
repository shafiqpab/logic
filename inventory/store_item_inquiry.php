<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Store Item Inquiry
				
Functionality	:	
JS Functions	:
Created by		:	Fuad
Creation date 	: 	24-08-2015
Updated by 		:	
Update date		: 	 
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;


//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Store Item Inquiry Info","../", 1, 1, $unicode);
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

function generate_report()
{
	if( form_validation('cbo_company_id*cbo_item_category_id','Company Name*Item Category')==false )
	{
		return;
	}
	
	var cbo_company_id = $("#cbo_company_id").val();
	var item_category_id = $("#cbo_item_category_id").val();
	var txt_order_no = $("#txt_order_no").val();
	var txt_booking_no 	= $("#txt_booking_no").val();
	
	if(item_category_id==2 || item_category_id==13 || item_category_id==3 || item_category_id==14 || item_category_id==4)
	{
		if(txt_order_no=="" && txt_booking_no=="")
		{
			alert("Please Insert Order or Booking No.");
			$("#txt_order_no").focus();
			return;
		}
	}
	
	if( form_validation('txt_product_id','Product Id')==false )
	{
		return;
	}
	
	var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_item_category_id*cbo_year*txt_job_no*txt_order_no*txt_progarm_no*txt_booking_no*txt_product_id',"../");
	
	freeze_window(3);
	http.open("POST","requires/store_item_inquiry_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fn_report_generated_reponse;
}

function fn_report_generated_reponse()
{	
	if(http.readyState == 4) 
	{	 
 		var response=trim(http.responseText).split("**");
		$("#report_container2").html(response[0]);  
		show_msg('3');
		release_freezing();
	}
}

function synchronize_stock(prod_id)
{
	var data="action=synchronize_stock&prod_id="+prod_id;
	freeze_window(3);
	http.open("POST","requires/store_item_inquiry_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fn_synchronize_stock_reponse;
}

function fn_synchronize_stock_reponse()
{	
	if(http.readyState == 4) 
	{	 
		var response=trim(http.responseText);
		alert(response);
		release_freezing();
	}
} 
 
function openmypage(type,field_id)
{
	var companyID = $("#cbo_company_id").val();
	var cbo_year = $("#cbo_year").val();
	var txt_job_no = $("#txt_job_no").val();
	var txt_order_no = $("#txt_order_no").val();
	var item_category = $("#cbo_item_category_id").val();
	var txt_booking_no = $("#txt_booking_no").val();
	
	if( form_validation('cbo_company_id','Company Name')==false )
	{
		return;
	}
	
	var popup_width='700px';
	
	if(type==1)
	{
		var page_link='requires/store_item_inquiry_controller.php?action=job_no_popup&companyID='+companyID+'&cbo_year='+cbo_year;
		var title='Job No Search';
	}
	else if(type==2)
	{
		var page_link='requires/store_item_inquiry_controller.php?action=po_no_popup&companyID='+companyID+'&txt_job_no='+txt_job_no;
		var title='PO No Search';
	}
	else if(type==3)
	{
		var page_link='requires/store_item_inquiry_controller.php?action=program_no_popup&companyID='+companyID+'&txt_order_no='+txt_order_no;
		var title='Program No Search';
	}
	else if(type==4)
	{
		if( form_validation('cbo_item_category_id','Item Category')==false )
		{
			return;
		}
		var page_link='requires/store_item_inquiry_controller.php?action=booking_no_popup&companyID='+companyID+'&cbo_year='+cbo_year+'&item_category_id='+item_category;
		var title='Booking No Search';
		popup_width='520px';
	}
	else
	{
		if( form_validation('cbo_item_category_id','Item Category')==false )
		{
			return;
		}
		
		var page_link='requires/store_item_inquiry_controller.php?action=item_desc_popup&companyID='+companyID+'&txt_order_no='+txt_order_no+'&txt_booking_no='+txt_booking_no+'&cbo_year='+cbo_year+'&item_category_id='+item_category;
		var title='Item Search';
		popup_width='520px';
	}
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=370px,center=1,resize=1,scrolling=0','');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var hide_data=this.contentDoc.getElementById("hide_data").value;
		$('#'+field_id).val(hide_data);
	}
}

function search_by(val)
{
	$('#txt_job_no').val('');
	$('#txt_order_no').val('');
	$('#txt_progarm_no').val('');
	$('#txt_booking_no').val('');
	$('#txt_product_id').val('');
	
	if(val==2 || val==3 || val==4 || val==13 || val==14)
	{
		$('#txt_job_no').removeAttr('disabled','disabled');
		$('#txt_order_no').removeAttr('disabled','disabled');
		$('#txt_booking_no').removeAttr('disabled','disabled');
		
		if(val==2 || val==13)
		{
			$('#txt_progarm_no').removeAttr('disabled','disabled');
		}
		else
		{
			$('#txt_progarm_no').attr('disabled','disabled');	
		}
	}
	else
	{
		$('#txt_job_no').attr('disabled','disabled');
		$('#txt_order_no').attr('disabled','disabled');
		$('#txt_booking_no').attr('disabled','disabled');
		$('#txt_progarm_no').attr('disabled','disabled');
	}
}
 
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../",$permission);  ?>    		 
    <form name="storeItemInquiry_1" id="storeItemInquiry_1" autocomplete="off" > 
    <div style="width:100%;" align="center">
        <h3 style="width:930px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
        <div style="width:100%;" id="content_search_panel">
            <fieldset style="width:930px;">
                <table class="rpt_table" width="930" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th class="must_entry_caption">Company</th> 
                            <th class="must_entry_caption">Item Category</th>                               
                            <th>Year</th>
                          	<th>Job</th>
                            <th>Order</th>
                            <th>Program No</th>
                            <th>Booking Without Order</th>
                            <th class="must_entry_caption">Product Id</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
							<? 
                            	echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and core_business not in(3)  $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                            ?>                            
                        </td>
                        <td> 
							<? echo create_drop_down( "cbo_item_category_id", 150, $item_category,"",1, "--- Select Item Category ---", $selected,"search_by(this.value)",0,"","","","12,24,25,28,30"); ?>
                        </td>
                        <td> 
                            <? echo create_drop_down( "cbo_year", 70, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?>
                        </td>
						<td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes_numeric" style="width:80px" onDblClick="openmypage(1,this.id);" placeholder="Browse/Write" />
                        </td>
                        <td>
                            <input type="text" id="txt_order_no" name="txt_order_no" class="text_boxes" style="width:80px" onDblClick="openmypage(2,this.id);" placeholder="Browse/Write" />
                        </td>
                        <td>
                            <input type="text" id="txt_progarm_no" name="txt_progarm_no" class="text_boxes_numeric" style="width:70px" onDblClick="openmypage(3,this.id);" placeholder="Browse/Write" />
                        </td>
                        <td>
                            <input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes_numeric" style="width:100px" onDblClick="openmypage(4,this.id);" placeholder="Browse/Write" />
                        </td>
                        <td>
                            <input type="text" id="txt_product_id" name="txt_product_id" class="text_boxes_numeric" style="width:80px" onDblClick="openmypage(5,this.id);" placeholder="Browse/Write" />
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:80px" class="formbutton" />
                        </td>
                    </tr>
                </table> 
            </fieldset> 
		</div>
    </div>
    <br /> 
    <div id="report_container2" style="margin-left:5px"></div> 
    </form>    
</div>  
</body> 
<script src="../includes/functions_bottom.js" type="text/javascript"></script>  
</html>
