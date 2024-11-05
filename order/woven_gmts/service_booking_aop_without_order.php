<?
/*-------------------------------------------- Comments 
Version          : V1
Purpose			 : This form will create Service Booking aop without order
Functionality	 :	
JS Functions	 :
Created by		 : Ashraful 
Creation date 	 : 17-08-2015
Requirment Client: 
Requirment By    : 
Requirment type  : 
Requirment       : 
Affected page    : 
Affected Code    :              
DB Script        : 
Updated by 		 : 
Update date		 : 
QC Performed BY	 :		
QC Date			 :	
Comments		 : From this version oracle conversion is start
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Woven Service Booking", "../../", 1, 1,$unicode,'','');
?>	
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 

var permission='<? echo $permission; ?>';
function openmypage_order(page_link,title)
{
	if (form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}	
	else
	{
		page_link=page_link+get_submitted_data_string('cbo_company_name*cbo_buyer_name','../../');
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=470px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];;
			var id=this.contentDoc.getElementById("booking_id").value;
			
			if (id!="")
			{
				booking_number=id.split('_');
				//reset_form('','booking_list_view','txt_fabric_booking*txt_fabric_booking_id*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_booking_date*cbo_supplier_name*txt_attention*txt_delivery_date*cbo_source*txt_booking_no');
				freeze_window(5);
				document.getElementById('txt_fabric_booking_id').value=booking_number[0];
				document.getElementById('txt_fabric_booking').value=booking_number[1];
				get_php_form_data(booking_number[0], "populate_order_data_from_search_popup", "requires/service_booking_aop_without_order_controller" );
				release_freezing();
			}
		}
	}
}

function load_fabric_dropdown(fabric_source)
	{
		load_drop_down( 'requires/service_booking_aop_without_order_controller', document.getElementById('txt_fabric_booking_id').value+"_"+fabric_source, 'load_drop_down_fabric_description', 'fabric_description_td' )
	}
	
function check_exchange_rate()
{
	var cbo_currercy=$('#cbo_currency').val();
	var booking_date = $('#txt_booking_date').val();
	var response=return_global_ajax_value( cbo_currercy+"**"+booking_date, 'check_conversion_rate', '', 'requires/service_booking_aop_without_order_controller');
	var response=response.split("_");
	$('#txt_exchange_rate').val(response[1]);
	
}

function get_related_data(fabric_id)
{
	var cbo_fabric_source=document.getElementById('cbo_fabric_source').value;	
	get_php_form_data(fabric_id+"_"+cbo_fabric_source, "get_related_data", "requires/service_booking_aop_without_order_controller" );

}
	



function calculate_amount()
{
	var txt_woqnty=(document.getElementById('txt_wo_qty').value)*1;
	var txt_rate=(document.getElementById('txt_rate').value)*1;
	var txt_amount=txt_woqnty*txt_rate;
	document.getElementById('txt_amount').value=txt_amount;	

}









function openmypage_booking(page_link,title)
{
	page_link=page_link+get_submitted_data_string('cbo_company_name*cbo_buyer_name','../../');
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_booking");
		
		if (theemail.value!="")
		{
			reset_form('servicebooking_1','aop_list_view_panel','','txt_booking_date,<? echo date("d-m-Y"); ?>','','cbo_currency');
		 	get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/service_booking_aop_without_order_controller" );
			get_php_form_data(document.getElementById('txt_fabric_booking_id').value, "populate_order_data_from_search_popup", "requires/service_booking_aop_without_order_controller" );
		    show_list_view(theemail.value+"_"+document.getElementById('txt_fabric_booking_id').value+"_"+document.getElementById('cbo_fabric_source').value, 'aop_detls_list_view','aop_list_view_panel','requires/service_booking_aop_without_order_controller','');
	   		set_button_status(0, permission, 'fnc_aop_non_ord_booking',1);
		}
	}
}





function open_terms_condition_popup(page_link,title)
{
	var txt_booking_no=document.getElementById('txt_booking_no').value;
	if (txt_booking_no=="")
	{
		alert("Save The Booking First")
		return;
	}	
	else
	{
	    page_link=page_link+get_submitted_data_string('txt_booking_no','../../');
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=470px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
		}
	}
}


function fnc_aop_non_ord_booking( operation )
{
	var data_all="";
	if (form_validation('cbo_company_name*txt_fabric_booking*txt_booking_date*txt_dev_start_date*txt_dev_end_date*cbo_printing_color*cbo_pay_mode','Company Name*Fab.Booking No*Booking Date*Delivery Start Date*Delivery End Date*Print Color*Pay Mode')==false)
	{
		return;
	}
	else
	{
	data_all=data_all+get_submitted_data_string('txt_booking_no*mst_id*cbo_company_name*txt_fabric_booking*txt_fabric_booking_id*cbo_buyer_name*txt_booking_date*cbo_currency*txt_exchange_rate*cbo_pay_mode*cbo_source*cbo_aop_source*cbo_supplier_name*txt_attention*cbo_fabric_source*cbo_fabric_description*txt_gsm*txt_fin_dia*cbo_uom*txt_art_work*txt_wo_qty*txt_rate*txt_amount*txt_dev_start_date*txt_dev_end_date*txt_remarks*cbo_printing_color*dtls_id',"../../");
	}
	
	var data="action=save_update_delete&operation="+operation+data_all;
	freeze_window(operation);
	http.open("POST","requires/service_booking_aop_without_order_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_aop_non_ord_booking_reponse;
}
	 
function fnc_aop_non_ord_booking_reponse()
{
	if(http.readyState == 4) 
	{
		 var reponse=trim(http.responseText).split('**');
		 show_msg(trim(reponse[0]));
		 if(reponse[0]==0 || reponse[0]==1)
		 {
			document.getElementById('txt_booking_no').value=reponse[1];
			document.getElementById('mst_id').value=reponse[2];
		 	set_button_status(0, permission, 'fnc_aop_non_ord_booking',1);
			show_list_view(reponse[2]+"_"+document.getElementById('txt_fabric_booking_id').value+"_"+document.getElementById('cbo_fabric_source').value, 'aop_detls_list_view','aop_list_view_panel','requires/service_booking_aop_without_order_controller','');
			reset_form('','','cbo_fabric_source*cbo_fabric_description*txt_gsm*txt_fin_dia*cbo_uom*txt_art_work*txt_wo_qty*txt_rate*txt_amount*txt_dev_start_date*txt_dev_end_date*txt_remarks*dtls_id','txt_booking_date,<? echo date("d-m-Y"); ?>');
		 }
		 if(reponse[0]==2)
		 {
			set_button_status(0, permission, 'fnc_aop_non_ord_booking',1);
		 }
		 release_freezing();
	}
}
 

function get_dtls_data(id)
{
	get_php_form_data( id, "populate_data_dtls_from_search_popup", "requires/service_booking_aop_without_order_controller" );
	set_button_status(1, permission, 'fnc_aop_non_ord_booking',1);
}


 








	
function generate_trim_report(action)
{
if (form_validation('txt_booking_no','Booking No')==false)
	{
		return;
	}
	else
	{
		$report_title=$( "div.form_caption" ).html();
		var data="action="+action+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_fabric_booking_id*mst_id',"../../")+'&report_title='+$report_title;
		http.open("POST","requires/service_booking_aop_without_order_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_trim_report_reponse;
	}	
}

function generate_trim_report_reponse()
{
	if(http.readyState == 4) 
	{
		var file_data=http.responseText.split('****');
		$('#pdf_file_name').html(file_data[1]);
		$('#data_panel2').html(file_data[0] );
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel2').innerHTML+'</body</html>');
		d.close();
	}
}	

</script>
 
</head>
 
<body onLoad="set_hotkey();check_exchange_rate();">
<div style="width:100%;" align="center">
     <? echo load_freeze_divs ("../../",$permission);  ?>
            	<form name="servicebooking_1"  autocomplete="off" id="servicebooking_1">
            	<fieldset style="width:900px;">
                <legend>Service Booking</legend>
            		<table  width="900" cellspacing="2" cellpadding="0" border="1">
                        <tr>
                        <td align="130"></td>
                        <td align="170"></td>
                        <td  width="130" height="" align="right" class="must_entry_caption"> WO No </td>
                        <td  width="170" >
                            <input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_booking('requires/service_booking_aop_without_order_controller.php?action=aop_without_order_booking_search','AOP Without Order Booking Search')" readonly placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no"/>
                            <input class="text_boxes" type="hidden" style="width:160px"  readonly  name="mst_id" id="mst_id" />
                        </td>
                        <td align="130"></td>
                        <td align="170"></td>
                        </tr>
                   		<tr>
                           <td  align="right" class="must_entry_caption">Company Name</td>
                           <td>
                              <?  $date=date('d-m-Y');
							  	echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected, "reset_form('servicebooking_1','aop_list_view_panel','','txt_booking_date,".$date."','','cbo_company_name*cbo_currency');load_drop_down( 'requires/service_booking_aop_without_order_controller', this.value, 'load_drop_down_buyer', 'buyer_td' )","","" );
								?>	  
                           </td>
                           <td height="" align="right" class="must_entry_caption">Fab.Booking No</td>   
                            <td>
                                 <input class="text_boxes" type="text" style="width:160px;" placeholder="Double click for Booking"  onDblClick="openmypage_order('requires/service_booking_aop_without_order_controller.php?action=order_search_popup','Order Search')"   name="txt_fabric_booking" id="txt_fabric_booking"/>
                                 <input class="text_boxes" type="hidden" style="width:772px;"  name="txt_fabric_booking_id" id="txt_fabric_booking_id"/>
                            </td>   
							  <td align="right" >Buyer Name</td>   
   							 <td id="buyer_td"> 
                             <?  
							  	echo create_drop_down( "cbo_buyer_name", 172, "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,"" );
								?>
                         	</td>
                        </tr>
                        
                        <tr>
                            
                             
                            <td  width="130" align="right" class="must_entry_caption">Booking Date</td>
                            <td width="170">
                                    <input class="datepicker" type="text" style="width:160px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<? echo date("d-m-Y")?>" disabled />	
                            </td>   
                           
							<td align="right">Currency</td>
                              <td>
                              <? 
							  	echo create_drop_down( "cbo_currency", 172, $currency,"", 1, "-- Select --", 2, "check_exchange_rate()",0 );		
							  ?>	
                               
                              </td>	
                              <td align="right">Exchange Rate</td>
                            <td>
                             <input style="width:160px;" type="text" class="text_boxes"  name="txt_exchange_rate" id="txt_exchange_rate" readonly  />  
                            </td>
                        </tr>
                        <tr>
                        	
                       
                        	
                            <td  align="right" class="must_entry_caption">Pay Mode</td>
                            <td>
                               <?
							   		echo create_drop_down( "cbo_pay_mode", 172, $pay_mode,"", 1, "-- Select Pay Mode --", "", "","" );
							   ?> 
                            </td>
                            <td  width="130" height="" align="right"> Source </td>              <!-- 11-00030  -->
                             <td  width="170" >
                                	<?
							   		echo create_drop_down( "cbo_source", 172, $source,"", 1, "-- Select Source --", "", "","" );
							   ?>
                             </td>
                             <td  width="130" height="" align="right"> Aop Source </td>              <!-- 11-00030  -->
                             <td  width="170" >
                                	<?
							   		echo create_drop_down( "cbo_aop_source", 172, $knitting_source,"", 1, "-- Select Source --", "", "load_drop_down( 'requires/service_booking_aop_without_order_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_supplier', 'suplier_td' )","","1,3" );
							   ?>
                             </td>
                        </tr>
                        <tr>
                              <td  align="right">Supplier Name</td>
                            <td id="suplier_td">
                               <?
							   		echo create_drop_down( "cbo_supplier_name", 172, $blank_array,"", 1, "-- Select Supplier --", $selected, "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/service_booking_aop_without_order_controller');",0 );
							   ?> 
                            </td> 
                            <td align="right">Attention</td>   
                        	<td align="left" height="10" colspan="3">
                            	<input class="text_boxes" type="text" style="width:97%;"  name="txt_attention" id="txt_attention"/>
                            	<input type="hidden" class="image_uploader" style="width:162px" value="Lab DIP No" onClick="openmypage('requires/service_booking_aop_without_order_controller.php?action=lapdip_no_popup','Lapdip No','lapdip')">
                            </td>   
                                                           
                        </tr>
                         
                        <tr>
                        	<td>&nbsp;</td>
                        	<td>
                        		<?
									include("../../terms_condition/terms_condition.php");
									terms_condition(177,'txt_booking_no','../../');
								?>
                        	</td>
                        </tr>
                        
                        <tr>
                            <td align="right"></td> 
                             
                        </tr>
                        <tr>
                        	<td align="center" colspan="6" valign="top" id="booking_list_view1">
                            	
                            </td>
                        </tr>
                        
                        
                    </table>
                 
              </fieldset>
           
              <br/>
          
              <fieldset style="width:900px;">
                <legend>Service Booking</legend>
            		<table   border="0" width="900" cellpadding="0" cellspacing="2">
                       <!-- <thead>
						<th>Fabric Description</th>
                        <th>Artwork No</th>
						<th>Gmts. Color</th>
						<th>Item Color</th>
                        <th>UOM</th>
                        <th>Fin Dia</th>
                        <th>Delivery Start Date</th>
                        <th>Delivery End Date</th>
                        <th>WO. Qnty</th>
                        <th>Rate</th>
                        <th>Amount</th>
                        <th>Remarks</th>
                        <th></th>
					</thead>-->
                        <tr>
                            <td align="right" class="must_entry_caption">Fabric Source</td>
                            <td>
                            <? 
                            echo create_drop_down( "cbo_fabric_source", 172, $aop_nonor_fabric_source, "",0, "", 1, "load_fabric_dropdown(this.value)","","" );
                            ?>	  
                            </td>
                            <td  align="right" class="must_entry_caption">Fabric Description</td>
                            <td id="fabric_description_td">
                            <? 
                            echo create_drop_down( "cbo_fabric_description", 172, $blank_array, "",1, "-- Select Fabric --", $selected, "","","" );
                            ?>	  
                            </td>
                            <td height="" align="right">AOP.GSM</td>   
                            <td>
                            <input class="text_boxes" type="text" style="width:160px;"   name="txt_gsm" id="txt_gsm"/>
                            </td>
                        </tr>
                        
                         <tr>
                            <td height="" align="right">AOP.Dia</td>   
                            <td>
                             <input class="text_boxes" type="text" style="width:160px;"   name="txt_fin_dia" id="txt_fin_dia"/>
                            </td>
                             <td height="" align="right">UOM</td>   
                            <td>
                          <? echo create_drop_down( "cbo_uom", 172, $unit_of_measurement,"", 1, "-- Select --", $selected, "","","" );?>
                            </td>
                            <td height="" align="right">Artwork No</td>   
                            <td>
                            <input class="text_boxes" type="text" style="width:160px;"   name="txt_art_work" id="txt_art_work"/>
                            </td>   
                            
                        </tr>
                        
                         <tr>
                         <td align="right" >Gmts. Color</td>   
                            <td id="gmtcolor_td"> 
                            <?  
                            echo create_drop_down( "cbo_gmtcolor", 172, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
                            ?>
                            </td>
                             <td align="right" >WO. Qnty</td>   
                            <td> 
                             <input class="text_boxes_numeric" type="text" style="width:160px;" onChange="calculate_amount()"   name="txt_wo_qty" id="txt_wo_qty"/>
                            </td>
                             
                            <td  align="right" class="must_entry_caption">Aop Rate</td>
                            <td>
                             <input class="text_boxes_numeric" type="text" style="width:160px;"  onChange="calculate_amount()"  name="txt_rate" id="txt_rate"/>
                            </td>
                        </tr>
                        
                       
                        
                         <tr>
                         <td  align="right" class="must_entry_caption">Amount</td>
                            <td>
                             <input class="text_boxes_numeric" type="text" style="width:160px;"   name="txt_amount" id="txt_amount" readonly/>
                            </td>
                            <td  align="right" class="must_entry_caption">Delivery Start Date</td>
                            <td>
                            <input class="datepicker" type="text" style="width:160px;"   name="txt_dev_start_date" id="txt_dev_start_date"/>  
                            </td>
                            <td  align="right" class="must_entry_caption">Delivery End Date</td>
                            <td>
                            <input class="datepicker" type="text" style="width:160px;"   name="txt_dev_end_date" id="txt_dev_end_date"/>  
                            </td>   
                            
                        </tr>
                          <tr>
                            <td align="right">Remarks</td>   
                            <td> 
                             <input spellcheck="true"  class="text_boxes" type="text" style="width:160px;"   name="txt_remarks" id="txt_remarks"/>
                            </td>
                            <td align="right"></td> 
                            <td height="25" valign="middle">
                            <input type="button" class="image_uploader" style="width:192px" value="CLICK TO ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('mst_id').value,document.getElementById('dtls_id').value, 'aop_non_order_booking', 0 ,1)">
                           </td>
                            <td align="right" class="must_entry_caption">Printing Color</td>   
                            <td id="printcolor_td"> 
                            <?  
                           	 echo create_drop_down( "cbo_printing_color", 172, $blank_array,"", 1, "-- Select --", $selected, "","","" );
                            ?>
                            </td>
                        </tr>
                       <tr>
                        	<td align="center" colspan="6" valign="middle" class="button_container">
                              <? echo load_submit_buttons( $permission, "fnc_aop_non_ord_booking", 0,0 ,"",1) ; ?>
                               <input   class="text_boxes" type="hidden" style="width:160px;"   name="dtls_id" id="dtls_id"/>
                            </td>
                        </tr>
                        <tr>
                        	<td align="center" colspan="6" height="10">
                            <div id="pdf_file_name"></div>
                        <input type="button" value="Print Booking" onClick="generate_trim_report('show_trim_booking_report')"  style="width:100px" name="print_booking" id="print_booking" class="formbutton" />
                         
                        </td>
                        </tr>
                    </table>
                 
              <div id="aop_list_view_panel">
              </div>
           	  <br/>  <br/> 
              <div style="" id="data_panel">
              </div>
              <br/>  <br/>
               <div style="display:none" id="data_panel2">
              </div> 
              </fieldset>
           </form>
              

</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>