<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Order Wise Grey Fabrics Stock Report
				
Functionality	:	
JS Functions	:
Created by		:	
Creation date 	: 	12-07-2018
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
echo load_html_head_contents("Order Wise Grey Fabrics Stock Report","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(rpt_type)
	{
		var booking_no= $("#txt_booking_no").val().trim();
		var txt_date_from= $("#txt_date_from").val().trim();
		var txt_date_to= $("#txt_date_to").val().trim();
		var validation_str = "";var validation_msg = "";
		if(txt_date_from == "" || txt_date_to=="")
		{
			validation_str = "*txt_booking_no";
			validation_msg = "*Date From*Date To";
		}
		else if(booking_no == "")
		{
			validation_str = "*txt_date_from*txt_date_to";
			validation_msg = "*Booking No";
		}
		
		if( form_validation('cbo_company_id'+validation_str,'Company Name'+validation_msg)==false )
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_year*txt_booking_no*cbo_booking_type*cbo_value_with*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;
		//alert (data);
		freeze_window(3);
		http.open("POST","requires/sample_grey_fabrics_stock_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
			//var tableFilters='';
			//if(reponse[2]==3 || reponse[2]==2){setFilterGrid("table_body",-1);}
			
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
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
	}


	function openmypage_booking()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year").val();
		var cbo_booking_type = $("#cbo_booking_type").val();



		var page_link='requires/sample_grey_fabrics_stock_report_controller.php?action=booking_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id+'&booking_type='+cbo_booking_type;
		var title='Booking No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=430px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var booking_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			//alert(booking_no);
			$('#txt_booking_no').val(booking_no);
			//$('#txt_job_id').val(job_id);	 
		}
	}
	
	
	function openpage_fabric_booking(approve_category_book)
	{ 
		var companyID = $("#cbo_company_id").val();
		var type = $("#cbo_booking_type").val();
		var app_cat_book = approve_category_book.split("_");
		var is_approve = app_cat_book[0];
		var item_category_id = app_cat_book[1];
		var booking_no = app_cat_book[2];
		var job_no = app_cat_book[3];
		var po_ids = app_cat_book[4];
		var fabric_source = app_cat_book[5];
		var report_title ="";
		if(type==2){
			var data="action=show_fabric_booking_report6"+'&txt_booking_no='+booking_no+'&cbo_company_name='+companyID+'&id_approved_id='+is_approve+'&cbo_fabric_natu='+item_category_id+'&report_title='+report_title+'&img_source='+'../../../';

			http.open("POST","../../../order/woven_order/requires/sample_booking_non_order_controller.php",true);
		}
		else
		{
			report_title = "Sample Fabric Booking -With order";
			var show_yarn_rate='';
			var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
			if (r==true)
			{
				show_yarn_rate="1";
			}
			else
			{
				show_yarn_rate="0";
			}
			var data="action=show_fabric_booking_report"+'&txt_booking_no='+"'"+booking_no+"'"+'&cbo_company_name='+companyID+'&id_approved_id='+is_approve+'&cbo_fabric_natu='+item_category_id+'&cbo_fabric_source='+fabric_source+'&txt_order_no_id='+po_ids+'&txt_job_no='+"'"+job_no+"'"+'&report_title='+report_title+'&show_yarn_rate='+show_yarn_rate;

			http.open("POST","../../../order/woven_order/requires/sample_booking_controller.php",true);

		}

		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_fabric_report_reponse;

	}
	

	function generate_fabric_report_reponse()
	{
		if(http.readyState == 4) 
		{	
			var file_data=http.responseText.split('****');
			$('#pdf_file_name').html(file_data[1]);
			$('#data_panel').html(file_data[0] );
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
		}
	}


	function openmypage_delivery(orderID,programNo,prodID,from_date,to_date,popup_width,action,type)
	{ //alert(orderID);
		var companyID = $("#cbo_company_id").val();
		var popup_width=popup_width;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sample_grey_fabrics_stock_report_controller.php?companyID='+companyID+'&orderID='+orderID+'&programNo='+programNo+'&prodID='+prodID+'&from_date='+from_date+'&to_date='+to_date+'&action='+action+'&popup_width='+popup_width+'&type='+type, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>  		 
    <form name="orderwisegreyfabricstock_1" id="orderwisegreyfabricstock_1" autocomplete="off" > 
    <h3 style="width:1050px; margin-top:10px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:1050px;">
                <table class="rpt_table" width="1050" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th class="must_entry_caption">Company</th>                                
                            <th>Buyer</th>
                            <th>Booking type</th>
                            <th>Year</th>
                            <th>Booking No</th>
                            <th>Quantity</th>
                            <th class="must_entry_caption">Booking Date Range</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('orderwisegreyfabricstock_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
                            <? 
                               echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sample_grey_fabrics_stock_report_controller',this.value+'_'+1+'_'+4, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>                            
                        </td>
                        <td id="buyer_td"> 
                            <?
                                echo create_drop_down( "cbo_buyer_id", 140, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
                            ?>
                        </td>
                        
                        <td>
                            <?   
                                $valueWithArr=array(1=>'Sample With Order',2=>'Sample Without Order');
                                echo create_drop_down( "cbo_booking_type", 150, $valueWithArr,"",0,"",2,"","1","","","","1");
                                //Sample with order is omitted with consult of mamun, mirza and rasel vai
                            ?>
                        </td>
                        <td> 
                            <?
								echo create_drop_down( "cbo_year", 70, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes" style="width:80px" onDblClick="openmypage_booking();" placeholder="Browse/Write" />
                            <input type="hidden" id="txt_booking_id" name="txt_booking_id"/>
                        </td>

                        <td>
                            <?   
                                $valueWithArr=array(1=>'Qnty With 0',2=>'Qnty Without 0');
                                echo create_drop_down( "cbo_value_with", 102, $valueWithArr,"",0,"",2,"","","");
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("01-m-Y", strtotime('-4 month'));?>" class="datepicker" style="width:70px;" /> To
                            <input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date("d-m-Y");?>" class="datepicker" style="width:70px;"/>				
                        </td>

                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:79px" class="formbutton" />
                        </td>
                    </tr>
                    <tfoot>
							<tr>
								<td colspan="8" align="center">
									<? echo load_month_buttons(1);  ?>
								</td>
							</tr>
						</tfoot>
                </table> 
            </fieldset> 
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2"></div>   
    </form>    
</div>    
<div style="display:none" id="data_panel"></div>
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
