<?
/*-------------------------------------------- Comments
Purpose			: 	This form created Sample Ex-Factory Entry
				
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam Reza
Creation date 	: 	20-04-2015
Purpose			:
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
echo load_html_head_contents("Garments Delivery Info","../", 1, 1, $unicode,'','');
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
//---------------------------------------------------------------------------------------------------------

function ex_factory_sys_popup()
{
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	
	var page_link="requires/sample_ex_factory_controller.php?action=sys_surch_popup&company="+$("#cbo_company_name").val();
	var title="Ex-factory Info";
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,title, 'width=1040px,height=370px,center=1,resize=0,scrolling=0','')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var smp_id=this.contentDoc.getElementById("selected_id").value;
		var smp_id_arr=smp_id.split('*');
		freeze_window(5);
		$("#txt_development_sample_id").val(smp_id_arr[0]);
		show_list_view(smp_id_arr[0],'show_sample_item_listview','list_view_country','requires/sample_ex_factory_controller','');		
		show_list_view(smp_id,'show_dtls_listview','list_view_container','requires/sample_ex_factory_controller','');
		get_php_form_data(smp_id_arr[0], "populate_data_from_search_popup", "requires/sample_ex_factory_controller" );
		
		set_button_status(0, permission, 'fnc_exFactory_entry',1,0);
		release_freezing();
	}
	
}//fnc;



function openmypage(page_link,title)
{
	if( form_validation('cbo_company_name*cbo_order_type','Company Name*Order Type')==false )
	{
		return;
	}
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1040px,height=370px,center=1,resize=0,scrolling=0','')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var smp_id=this.contentDoc.getElementById("selected_id").value;
		freeze_window(5);
		$("#txt_development_sample_id").val(smp_id);
		$("#txt_development_sample_id").attr('placeholder',smp_id);
		$("#dtls_update_id").val('');
		
		if($("#cbo_order_type").val()==3){
			//without order list
			show_list_view(smp_id,'show_sample_item_listview','list_view_country','requires/sample_ex_factory_controller','');
		}
		else
		{
			//with order list
			get_php_form_data(smp_id, "set_po_number", "requires/sample_ex_factory_controller" ); 
		}
		
		set_button_status(0, permission, 'fnc_exFactory_entry',1,0);
		release_freezing();
	}
}//fnc;

function fn_item_color_size(smp_id)
{
	var order_text=$("#txt_development_sample_id").val();
	var smp_dev_id=$("#txt_development_sample_id").attr('placeholder');
	get_php_form_data(order_text+'**'+smp_id+'**'+smp_dev_id, "color_and_size_level_with_order", "requires/sample_ex_factory_controller" ); 
}


function put_sample_item_data(mst_id,smp_id,smp_dev_id)
{
	freeze_window(5);
	get_php_form_data(mst_id+'**'+smp_id+'**'+smp_dev_id, "color_and_size_level", "requires/sample_ex_factory_controller" ); 
	//show_list_view(smp_id,'show_dtls_listview','list_view_container','requires/sample_ex_factory_controller','');
	set_button_status(0, permission, 'fnc_exFactory_entry',1,0);
	release_freezing();
}//fnc;


function openmypage_lcsc()
{
	if( form_validation('txt_development_sample_id','Development Sample ID')==false )
	{
		return;
	}
	var page_link="requires/sample_ex_factory_controller.php?action=lcsc_popup&company="+$("#cbo_company_name").val();
	var title="Order Search";
	//page_link=page_link+''
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=880px,height=380px,center=1,resize=0,scrolling=0','')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var invoice_lcsc_no=( this.contentDoc.getElementById("lc_id_no").value).split(",");
		
		$("#txt_invoice_no").val(invoice_lcsc_no[1]);
		$("#txt_invoice_no").attr('placeholder',invoice_lcsc_no[0]);

		$("#txt_lc_sc_no").val(invoice_lcsc_no[3]);
		$("#txt_lc_sc_no").attr('placeholder',invoice_lcsc_no[2]);
	}
}//fnc;



function fnc_exFactory_entry(operation)
{
	if(operation==4)
	{
		if ( form_validation('txt_challan_no','Challan Number')==false )
		{
			alert("Please save the challan first"); return;
		}		
		else
		{
			 //var report_title=$( "div.form_caption" ).html();
			 //var id_ref=1;
			 print_report( $('#mst_update_id').val()+'*'+$('#cbo_company_name').val(), "ex_factory_print", "requires/sample_ex_factory_controller" ) 
			 return;
		}
	}
	else if(operation==0 || operation==1 || operation==2)
	{ 
		if (form_validation('cbo_company_name*txt_ex_factory_date*cbo_transport_company*txt_development_sample_id*txt_sample_name*txt_ex_factory_qty','Company Name*Ex-factory Date*Transport Company*Development Sample ID*Sample Name*Ex-factory Qty')==false)
		{
			 
			return;
		}		
		else
		{
			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_ex_factory_date').val(), current_date)==false)
			{
				alert("Ex Factory Date Can not Be Greater Than Current Date");
				return;
			}	
			
			if($("#txt_invoice_no").val()!='') 
				var invoice_id = $("#txt_invoice_no").attr('placeholder');
			else 
				var invoice_id = '';
				
			if($("#txt_lc_sc_no").val()!='') 
				var lcsc_id = $("#txt_lc_sc_no").attr('placeholder');
			else 
				var lcsc_id = '';
		 	
			if($("#txt_development_sample_id").val()!='') 
				var txt_development_sample_id = $("#txt_development_sample_id").attr('placeholder');
			else 
				var txt_development_sample_id = '';
		 	
			
			var sample_name =$("#txt_sample_name").val()
		 	
			
			var colorList = ($('#hidden_colorSizeID').val()).split(",");
			var i=0;var colorIDvalue='';
			$("input[name=colSizeQty]").each(function(index, element) {
				if( $(this).val()!='' )
				{
					if(i==0)
					{
						colorIDvalue = colorList[i]+"*"+$(this).val();
					}
					else
					{
						colorIDvalue += "***"+colorList[i]+"*"+$(this).val();
					}
				}
				i++;
			});
			
			var data="action=save_update_delete&operation="+operation+'&invoice_id='+invoice_id+'&lcsc_id='+lcsc_id+'&sample_name='+sample_name+"&colorIDvalue="+colorIDvalue+'&txt_development_sample_id='+txt_development_sample_id+get_submitted_data_string('mst_update_id*dtls_update_id*txt_challan_no*cbo_company_name*cbo_location_name*txt_delivery_to*txt_ex_factory_date*cbo_transport_company*txt_truck_no*txt_lock_no*txt_driver_name*txt_dl_no*txt_mobile_no*txt_do_no*txt_gp_no*txt_final_destination*cbo_forwarder*txt_dipo_name*cbo_order_type*txt_ex_factory_qty*txt_total_carton_qnty*txt_lc_sc_no*txt_carton_per_qnty*txt_remark*cbo_shipping_status',"../");
			
			//alert(data);
 			freeze_window(operation);
 			http.open("POST","requires/sample_ex_factory_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_exFactory_entry_Reply_info;
		}
	}
}//fnc;
 


function fnc_exFactory_entry_Reply_info()
{
 	if(http.readyState == 4) 
	{
		
		var reponse=http.responseText.split('**');		 
		
		if(reponse[0]==0)//insert response;
		{
			show_msg(trim(reponse[0]));
			show_list_view(reponse[2]+'*'+reponse[1],'show_dtls_listview','list_view_container','requires/sample_ex_factory_controller','');
			$('#mst_update_id').val(reponse[1]);
			$('#txt_challan_no').val(reponse[3]);
			childFormReset();
		}
		else if(reponse[0]==1)//update response;
		{
			show_msg(trim(reponse[0]));
			show_list_view(reponse[2]+'*'+reponse[1],'show_dtls_listview','list_view_container','requires/sample_ex_factory_controller','');
			set_button_status(0, permission, 'fnc_exFactory_entry',1,0);
			childFormReset();
		}
		else if(reponse[0]==2)//delete response;
		{
			
			show_msg(trim(reponse[0]));
			
			show_list_view(reponse[2]+'*'+reponse[1],'show_dtls_listview','list_view_container','requires/sample_ex_factory_controller','');
			set_button_status(0, permission, 'fnc_exFactory_entry',1,0);
		}
		
		release_freezing();
 	}
}//fnc; 



 
 

function fn_total(tableName,index) // for color and size level
{
    var filed_value = $("#colSizeQty_"+tableName+index).val();
	var placeholder_value = $("#colSizeQty_"+tableName+index).attr('placeholder');
	
	if(filed_value*1 > placeholder_value*1)
	{
		if( confirm("Qnty Excceded by "+(placeholder_value-filed_value)) )	
			void(0);
		else
		{
			$("#colSizeQty_"+tableName+index).val('');
 		}
	}
	
	var totalRow = $("#table_"+tableName+" tr").length;
	math_operation( "total_"+tableName, "colSizeQty_"+tableName, "+", totalRow);
	if($("#total_"+tableName).val()*1!=0)
	{
		$("#total_"+tableName).html($("#total_"+tableName).val());
	}
	
	var totalVal = 0;
	$("input[name=colSizeQty]").each(function(index, element) {
        totalVal += ( $(this).val() )*1;
    });
	$("#txt_ex_factory_qty").val(totalVal);
	fn_qnty_per_ctn();
	
}//fnc;


function fn_qnty_per_ctn()
{
	var exqty=$("#txt_ex_factory_qty").val()*1;
	var totCtnQty=$("#txt_total_carton_qnty").val()*1;
	if(exqty && totCtnQty)$("#txt_carton_per_qnty").val(exqty/totCtnQty);
}//fnc;


function childFormReset()
{
	reset_form('','','cbo_order_type*txt_development_sample_id*txt_ex_factory_qty*txt_total_carton_qnty*txt_carton_per_qnty*txt_remark*cbo_shipping_status','','');
	$('#txt_sample_name').val(0);//placeholder value initilize
	$('#txt_sewing_qnty').attr('placeholder','');//placeholder value initilize
	$('#txt_invoice_no').attr('placeholder','');//placeholder value initilize
	$('#txt_lc_sc_no').attr('placeholder','');//placeholder value initilize ex_factory_list_view
	$("#breakdown_td_id").html('');
}//fnc;


<!-- ***************************************end********************************-->




 
function delivery_sys_popup()
{
	var page_link='requires/sample_ex_factory_controller.php?action=sys_surch_popup&company='+document.getElementById('cbo_company_name').value;
	var title="Delivery System Popup";
	var company = $("#cbo_company_name").val();
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1040px,height=370px,center=1,resize=0,scrolling=0','')
	emailwindow.onclose=function()
	{
		var delivery_id=this.contentDoc.getElementById("hidden_delivery_id").value;
		//alert(delivery_id);return;
		if(delivery_id !="")
		{
 			//$("#txt_order_qty").val(po_qnty);
			//$("#cbo_item_name").val(item_id);
			//$("#cbo_country_name").val(country_id);
			get_php_form_data(delivery_id, "populate_muster_from_date", "requires/sample_ex_factory_controller" );
			show_list_view(delivery_id,'show_dtls_listview_mst','ex_factory_list_view','requires/sample_ex_factory_controller','');
			setFilterGrid("details_table",-1);
			set_button_status(0, permission, 'fnc_exFactory_entry',1,1);
		}
		
		
	}

}

function fn_show_hide(str)
{
	if(str==3)
	{
		document.getElementById("txt_sample_name").disabled = 1;
		}
	else{
		document.getElementById("txt_sample_name").disabled = 0;
	}	
	document.getElementById("txt_sample_name").value = 0;
	document.getElementById("txt_development_sample_id").value = '';
	$('#txt_development_sample_id').attr('placeholder','Double Click to Search');
}



</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;">
	<?  echo load_freeze_divs ("../",$permission);  ?>
    <div style="width:930px; float:left" align="center">
        <form name="exFactory_1" id="exFactory_1" autocomplete="off" >     
        <fieldset style="width:930px;">
            <legend>Production Module</legend>
                <fieldset>                                       
                <table width="100%" border="0">
                	<tr>
                        <td align="right" colspan="3">Challan No</td>
                        <td colspan="3"> 
                          <input name="txt_challan_no" id="txt_challan_no" class="text_boxes" type="text"  style="width:160px" onDblClick="ex_factory_sys_popup()" placeholder="Browse or Search" />
                          <input type="hidden" id="mst_update_id" readonly />
                        </td>
                    </tr>
                    <tr>
                        <td width="130" align="right" class="must_entry_caption">Company Name </td>
                        <td width="170">
                            <?
                            echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", '', "load_drop_down( 'requires/sample_ex_factory_controller', this.value, 'load_drop_down_location', 'location_td' );",0 ); ?>
                        </td>
                        <td width="130" align="right">Location</td>
                        <td width="170" id="location_td">
                           <? echo create_drop_down( "cbo_location_name", 172, $blank_array,"", 1, "-- Select Location --", $selected, "" );?>
                        </td>   
                        <td align="right">Delivery To</td>
                        <td > 
						<?    
                        echo create_drop_down( "txt_delivery_to", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0 );
                          ?>
                        </td>
                        
                        
                    </tr>
                    <tr>
                        <td width="110" align="right" class="must_entry_caption">Ex- Factory Date </td>
                        <td width="190"> 
                        <input name="txt_ex_factory_date" id="txt_ex_factory_date" class="datepicker"  style="width:160px;" >
                        </td>
                        <td align="right" class="must_entry_caption">Transport. Company </td>
                        <td >
                        <? 
                        echo create_drop_down( "cbo_transport_company", 172, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and a.is_deleted=0 and b.party_type=35   order by a.supplier_name","id,supplier_name", 1, "-- Select Company --", $selected,"","0" );
                        ?>
                        </td>
                        
                        <td  align="right">Truck No</td>
                        <td id="section_td"><input type="text" name="txt_truck_no" id="txt_truck_no" class="text_boxes" style="width:160px;" maxlength="50"></td>
                        
                     </tr>
                     
                    <tr>
                    	<td align="right" >Lock No</td>
                        <td>
                        <input type="text" name="txt_lock_no" id="txt_lock_no" class="text_boxes" style="width:160px;" maxlength="50">
                        </td>
                        <td align="right" >Driver Name</td>
                        <td ><input type="text" name="txt_driver_name" id="txt_driver_name" class="text_boxes" style="width:160px;" maxlength="50"></td>
                        <td align="right">DL/No</td>
                        <td >
                        <input type="text" name="txt_dl_no" id="txt_dl_no" class="text_boxes" style="width:160px;" maxlength="50">
                        </td>
                                            
                    </tr>
                    <tr>
                    	<td align="right" >Mobile Num</td>
                        <td>
                        <input type="text" name="txt_mobile_no" id="txt_mobile_no" class="text_boxes" style="width:160px;" maxlength="50">
                        </td>
                        <td align="right" >DO No</td>
                        <td ><input type="text" name="txt_do_no" id="txt_do_no" class="text_boxes" style="width:160px;" maxlength="50"></td>
                        <td align="right">GP No</td>
                        <td >
                        <input type="text" name="txt_gp_no" id="txt_gp_no" class="text_boxes" style="width:160px;" maxlength="50">
                        </td>
                                            
                    </tr>
                    <tr>
                    	<td align="right" >Final Destination</td>
                        <td>
                        	<input type="text" name="txt_final_destination" id="txt_final_destination" class="text_boxes" style="width:160px;" maxlength="50">
                        </td> 
                        <td align="right" >Forwarder</td>
                        <td>
                        <? 
                        	echo create_drop_down( "cbo_forwarder", 172, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and a.is_deleted=0 and b.party_type in(30,31,32)   order by a.supplier_name","id,supplier_name", 1, "-- Select--", $selected,"","0" );
                        ?>
                        </td>
                        <td align="right" >Dipo Name</td>
                        <td>
                        <input type="text" name="txt_dipo_name" id="txt_dipo_name" class="text_boxes" style="width:160px;" maxlength="50">
                        </td>
                    </tr>
                </table>
                </fieldset>
                <br /> 
                <table cellpadding="0" cellspacing="1" width="100%">
                    <tr>
                        <td width="30%" valign="top">
                          <fieldset>
                          <legend>New Entry</legend>
                            <table  cellpadding="0" cellspacing="2" width="100%">
                                <tr>
                                	<td width="130" align="right" class="must_entry_caption">Order Type</td>
                                    <td>
                                     <?
										echo create_drop_down( "cbo_order_type", 110, $bill_for,"", 1, "-- Select --", "", "fn_show_hide(this.value);","","2,3" );
									 ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right" class="must_entry_caption">Dev. Sample ID</td>
                                    <td><input name="txt_development_sample_id" id="txt_development_sample_id"  placeholder="Double Click to Search" onDblClick="openmypage('requires/sample_ex_factory_controller.php?action=dev_sample_popup&company='+document.getElementById('cbo_company_name').value+'&order_type='+document.getElementById('cbo_order_type').value,'Order Search')" class="text_boxes" style="width:100px " readonly />
                                </tr>
                                 <tr>
                                    <td align="right" class="must_entry_caption">Sample Name</td> 
                                    <td>            
                                      <!--<input name="txt_sample_name" id="txt_sample_name"  class="text_boxes" type="text" style="width:100px" maxlength="50" placeholder="" readonly />-->
                                      <?
                                      echo create_drop_down( "txt_sample_name", 110, "select sample_name,id from lib_sample where is_deleted=0 and status_active=1 order by sample_name","id,sample_name", 1, "-- Select --", $selected, "fn_item_color_size(this.value)" );
									  ?>
                                    </td> 
                               </tr>
                                <tr>
                                    <td align="right" class="must_entry_caption"> Ex- Factory Qnty</td>
                                    <td>
                                        <input name="txt_ex_factory_qty" id="txt_ex_factory_qty" class="text_boxes_numeric" type="text"  style="width:100px;" readonly />
                                       <!-- <input type="hidden" id="hidden_break_down_html"  value="" readonly disabled />-->
                                        <input type="hidden" id="hidden_colorSizeID"  value="" readonly disabled />
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">Total Carton Qnty</td>
                                    <td>
                                       <input name="txt_total_carton_qnty" id="txt_total_carton_qnty" type="text" class="text_boxes_numeric"  style="width:100px" onKeyUp="fn_qnty_per_ctn()" />
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right"> Invoice No</td>
                                    <td> 
                                      <input name="txt_invoice_no" id="txt_invoice_no" type="text" style="width:100px;" onDblClick="openmypage_lcsc()" class="text_boxes" placeholder="Double Click To Search" maxlength="50" readonly  />                  
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right"> LC/SC No</td>
                                    <td>  
                                      <input name="txt_lc_sc_no" id="txt_lc_sc_no"  class="text_boxes" type="text" style="width:100px" maxlength="50" placeholder="" readonly />
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right"> Qnty/Ctn(Pcs/Set)</td>
                                    <td> 
                                         <input name="txt_carton_per_qnty" id="txt_carton_per_qnty" class="text_boxes_numeric"  style="width:100px" readonly />
                                    </td>
                                </tr>
                                <tr>
                                     <td width="102" align="right">Remarks</td> 
                                     <td width="165"> 
                                         <input name="txt_remark" id="txt_remark" type="text"  class="text_boxes" style="width:150px;" maxlength="450"  />
                                     </td>
                                </tr>
                                <tr>
                                      <td width="102" align="right">Shiping Status<span id="completion_perc"></span></td> 
                                      <td width="165">
                                          <?
                                             echo create_drop_down( "cbo_shipping_status", 110, $shipment_status,"", 0, "-- Select --", 2, "",0,'2,3','','','','' );	
                                         ?>                                              
                                      </td>
                                </tr>
                           </table>
                        </fieldset>
                    </td>
                    <td width="1%" valign="top"></td>
                    <td width="28%" valign="top">
                          <fieldset>
                          <legend>Display</legend>
                              <table cellpadding="0" cellspacing="2" width="100%" >
                                 <tr>
                                      <td width="160" align="right">Sewing  Qnty</td>
                                      <td>
                                          <input name="txt_sewing_qnty" id="txt_sewing_qnty"  class="text_boxes_numeric" type="text" style="width:100px" disabled readonly  />
                                      </td>
                                  </tr> 
                                  <tr>
                                      <td align="right">Cuml. Ex-Factory Qnty</td>
                                      <td>
                                          <input type="text" name="txt_cumul_ex_factory_qty" id="txt_cumul_ex_factory_qty" class="text_boxes_numeric"  style="width:100px" disabled readonly  />
                                      </td>
                                  </tr>
                                   <tr>
                                      <td align="right">Yet to Ex-Factory Qnty</td>
                                      <td>
                                          <input type="text" name="txt_yet_quantity" id="txt_yet_quantity" class="text_boxes_numeric"  style="width:100px" disabled readonly />
                                      </td>
                                  </tr>
                               </table>
                          </fieldset>
                      </td>
                    <td width="41%" valign="top" align="center">
                        <div style="max-height:330px; overflow-y:scroll" id="breakdown_td_id"></div>
                    </td>    
                </tr>
                </table>
                <br />
                <table cellpadding="0" cellspacing="1" width="100%">
                    <tr>
                        <td align="center" colspan="6" valign="middle" class="button_container">
                             <? 
                                echo load_submit_buttons( $permission, "fnc_exFactory_entry", 0,1,"reset_form('exFactory_1','list_view_container*list_view_country','','','childFormReset()')",1);
                            ?>
                             <input type="hidden" name="dtls_update_id" id="dtls_update_id" readonly />
                        </td>
                    </tr> 
                </table>
               
           </fieldset>
        </form>
        <div style="float:left;"id="list_view_container"></div>
    </div>
	<div id="list_view_country" style="float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>   
</div>
</body> 
<script src="../includes/functions_bottom.js" type="text/javascript"></script>  
</html>