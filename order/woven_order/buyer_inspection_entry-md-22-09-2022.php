<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Buyer Inspection			
Functionality	:	
JS Functions	:
Created by		:	Md Mamun Ahmed Sagor
Creation date 	: 	22-09-2022
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
echo load_html_head_contents("Service Booking For Dyeing V2", "../../", 1, 1,$unicode,'','');
?>	
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 

var permission='<? echo $permission; ?>';





function setmaster_value(process, sensitivity)
{
	document.getElementById('cbo_process').value=process;
	document.getElementById('cbo_colorsizesensitive').value=sensitivity;
}

function calculate_amount(rowId)
{
	var txt_balqnty=(document.getElementById('txt_balqnty_'+rowId).value)*1;
	var txt_woqnty=(document.getElementById('txt_woqnty_'+rowId).value)*1;
	var txt_rate=(document.getElementById('txt_rate_'+rowId).value)*1;
	var service_rate_from = $('#service_rate_from').val()*1;
	var pre_cost_rate=$('#txt_rate_'+rowId).attr('pre-cost-rate')*1;
	console.log(service_rate_from+'--'+pre_cost_rate+'--'+txt_rate);

	if(txt_woqnty>txt_balqnty)
		{
			alert("Exceed qty not allowed.\n Bal. Qty :");
			$('#txt_woqnty_'+rowId).val('')
			return;
		}	

	if(service_rate_from==2) //No
	{
		if(txt_rate>pre_cost_rate)
		{
			alert("Rate can't greater then budget");
			$('#txt_rate_'+rowId).val('')
			return;
		}	
	}
	var txt_amount=txt_woqnty*txt_rate;
	document.getElementById('txt_amount_'+rowId).value=txt_amount;	

}

function copy_value(type,row_id,color_id)
{
	
	
	 var copy_val=document.getElementById('copy_qnty').checked;
	 var copy_rate=document.getElementById('copy_rate').checked;
	 var rowCount=$('#table_list_view tbody tr').length;
	 if(type=='txt_woqnty'){
		  for(var j=1; j<=rowCount; j++)
		  {
			 
				 
				  document.getElementById('txt_woqnty_'+j).value="";
				  document.getElementById('txt_amount_'+j).value="";	
			 
		  }
	  }

	  if(type=='txt_rate') {

		  for(var j=1; j<=rowCount; j++)
		  {
				  document.getElementById('txt_rate_'+j).value="";
		  }
	  }

	  if(type=='gmts_color') {

			for(var j=1; j<=rowCount; j++){
			var color=document.getElementById('gmts_color_id_'+j).value;
			
				if(color_id==color){
					
					var txt_balqnty=(document.getElementById('txt_balqnty_'+j).value)*1;
					var hidden_rate=(document.getElementById('hidden_rate_'+j).value)*1;
					document.getElementById('txt_woqnty_'+j).value=txt_balqnty;
					document.getElementById('txt_rate_'+j).value=hidden_rate;
					var txt_amount=txt_balqnty*hidden_rate;
					document.getElementById('txt_amount_'+j).value=txt_amount;	
				}

			}
		}


	
		if(type=='sdate') {

				var sdate=document.getElementById('startdate_'+row_id).value;
				for(var j=1; j<=rowCount; j++){
				var color=document.getElementById('gmts_color_id_'+j).value;
				

					if(color_id==color){
						
						document.getElementById('startdate_'+j).value=sdate;
						
					}

				}
		}
		if(type=='edate') {

				var edate=document.getElementById('enddate_'+row_id).value;
				for(var j=1; j<=rowCount; j++){
				var color=document.getElementById('gmts_color_id_'+j).value;

					if(color_id==color){
		
					  document.getElementById('enddate_'+j).value=edate;
		
					}

				}
		}
	  
	
}

function fnc_generate_booking()
{
	
	if (form_validation('txt_order_no_id','Order No*Fabric Nature*Fabric Source')==false)
	{
		return;
	}
	else
	{
		var data="action=generate_fabric_booking"+get_submitted_data_string('txt_order_no_id',"../../");
		http.open("POST","requires/buyer_inspection_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_booking_reponse;
	}
}

function fnc_generate_booking_reponse()
{
	if(http.readyState == 4) 
	{
		document.getElementById('booking_list_view').innerHTML=http.responseText;
	}
}




function open_consumption_popup(page_link,title,po_id,i)
{
	var cbo_company_id=document.getElementById('cbo_company_name').value;
	var po_id =document.getElementById(po_id).value;
	var txtwoq=document.getElementById('txtwoq_'+i).value;
	var cons_breck_downn=document.getElementById('consbreckdown_'+i).value;
	var cbocolorsizesensitive=document.getElementById('cbocolorsizesensitive_'+i).value;
	if(po_id==0 )
	{
		alert("Select Po Id")
	}
	
	else
	{
		var page_link=page_link+'&po_id='+po_id+'&cbo_company_id='+cbo_company_id+'&txtwoq='+txtwoq+'&cons_breck_downn='+cons_breck_downn+'&cbocolorsizesensitive='+cbocolorsizesensitive;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1060px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var cons_breck_down=this.contentDoc.getElementById("cons_breck_down");
			var woq=this.contentDoc.getElementById("cons_sum");
			document.getElementById('consbreckdown_'+i).value=cons_breck_down.value;
			document.getElementById('txtwoq_'+i).value=woq.value;
			document.getElementById('txtamount_'+i).value=(woq.value)*1*(document.getElementById('txtrate_'+i).value);
		}	
	}
}


function openmypage_booking(page_link,title)
{
	var cbo_company_name=document.getElementById('cbo_company_name').value;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&cbo_company_name='+cbo_company_name, title, 'width=1000px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_booking");
		
		if (theemail.value!="")
		{
			
		
		
		
		
		 	get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/buyer_inspection_entry_controller" );
	   		set_button_status(1, permission, 'fnc_trims_booking',1);
			 
			 
		}
	}
}





	function fnc_trims_booking( operation )
	{
		

		
		var data_all="";
		if (form_validation('cbo_company_name*txt_workorder_no','Company Name*WO Booking')==false)
		{
			return;
		}
		else
		{
			
			var row_num=$('#table_list_view tbody tr').length;



		for (var i=1; i<=row_num; i++)
		{
			var ackn_qnty=$('#txt_ackn_qnty_'+i).val();
			if(ackn_qnty==""){
				alert("fill up Ackn Qnty.");
				return;
			};
		
		

			data_all+=get_submitted_data_string('txt_job_no_'+i+'*order_id_'+i+'*gmts_item_id_'+i+'*emb_name_id_'+i+'*emb_type_id_'+i+'*body_part_id_'+i+'*uom_id_'+i+'*txt_wo_qnty_'+i+'*txt_ackn_qnty_'+i+'*txt_rate_'+i+'*txt_amount_'+i+'*txt_job_no_'+i+'*txt_test_item_'+i+'*test_item_id_'+i+'*test_for_id_'+i+'*test_category_id_'+i+'*color_id_'+i+'*txt_vat_amount_'+i+'*txt_tot_amount_'+i+'*txt_fab_description_'+i+'*txt_gsm_'+i+'*txt_dia_'+i+'*service_for_id_'+i+'*txt_service_details_'+i+'*txt_item_description_'+i+'*item_category_id_'+i+'*item_group_id_'+i+'*txt_service_number_'+i+'*fab_color_id_'+i+'*update_dtls_id_'+i,"../../",i);	
			
		}
		

						
		data_all=data_all+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_workorder_no*txt_workorder_no_id*cbo_source*txt_style_ref*txt_inspection_date*txt_style_desc*cbo_buyer_name*txt_job_no*booking_mst_id',"../../");
		
	
		}
		


		
		var data="action=save_update_delete&operation="+operation+data_all+'&row_num='+row_num;
		
		freeze_window(operation);
		http.open("POST","requires/buyer_inspection_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_trims_booking_reponse;
	}
	 
	function fnc_trims_booking_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			show_msg(trim(reponse[0]));
			if(reponse[0]==0 || reponse[0]==1)
			{
				document.getElementById('txt_booking_no').value=reponse[1];
				document.getElementById('update_id').value=reponse[2];
				document.getElementById('booking_mst_id').value=reponse[2];
			
				set_button_status(1, permission, 'fnc_trims_booking',1);
				
			}
			if(reponse[0]==2)
			{
				set_button_status(0, permission, 'fnc_trims_booking',1);
			reset_form('','','txt_booking_no*cbo_company_name*txt_style_ref*txt_inspection_date*cbo_supplier_name*txt_style_desc','txt_inspection_date,<? echo date("d-m-Y"); ?>'); 
			}
			var wo_type=$('#cbo_source').val();
				
  			 show_list_view(reponse[2]+'_'+wo_type,'service_ackn_booking_list_view','booking_list_view2','requires/buyer_inspection_entry_controller','setFilterGrid(\'list_view\',-1)');
			release_freezing();
		}
	}
 



 

	function fnc_show_booking()
	{
		if (form_validation('txt_booking_no','Booking No')==false)
		{
			return;
		}
		else
		{
			var data="action=show_trim_booking"+get_submitted_data_string('txt_booking_no',"../../");
			http.open("POST","requires/buyer_inspection_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_show_booking_reponse;
		}
	}

	function fnc_show_booking_reponse()
	{
		if(http.readyState == 4) 
		{
			document.getElementById('booking_list_view').innerHTML=http.responseText;
			set_button_status(1, permission, 'fnc_trims_booking',2);
			set_all_onclick();
		}
	}


	function fnc_fab_booking(page_link,title)
	{
		if (form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var company=$("#cbo_company_name").val()*1;
	
		var order_no_id=$("#txt_order_no_id").val()*1;
		
		//alert(company);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&company='+company+'&order_no_id='+order_no_id, title, 'width=1190px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_booking");
			if (theemail.value!="")
			{
				//reset_form('fabricbooking_1','booking_list_view','','txt_inspection_date,<? //echo date("d-m-Y"); ?>');
			//	get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/service_booking_aop_urmi_controller" );
				//check_month_setting();
				//var is_approved_id=$('#id_approved_id').val();
				//alert(is_approved_id);
			
				//$('#cbo_company_name').attr('disabled','true');
				//set_button_status(1, permission, 'fnc_fabric_booking',1);
				$("#txt_fab_booking").val(theemail.value);
			
				
			}
		}
	}

	function fnc_amount(rowid){
	
		var rate=$("#txt_rate_"+rowid).val()*1;
		var qnty=$("#txt_ackn_qnty_"+rowid).val()*1;
		var wo_qnty=$("#txt_wo_qnty_"+rowid).val()*1;
		if(qnty>wo_qnty){
			alert("Not allow Ackn Qnty more than WO Qnty.");
			$("#txt_ackn_qnty_"+rowid).val("");
			return;
		}
		var amount=rate*qnty;
		$("#txt_amount_"+rowid).val(amount);
	}

	function openmypage_po()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
     /*		else
		{	
     */			var data = $("#cbo_company_name").val()+"_"+$("#cbo_buyer_name").val()+"_"+$("#txt_job_no").val();
	 
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/buyer_inspection_entry_controller.php?data='+data+'&action=po_no_popup', 'PO No Search', 'width=500px,height=420px,center=1,resize=0,scrolling=0','../')
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
	function fnRemoveHidden(str){
		document.getElementById(str).value='';
	}
	
</script>
 
</head>
 
<body onLoad="set_hotkey();check_exchange_rate();">
<div style="width:100%;" align="center">
     <? echo load_freeze_divs ("../../",$permission);  ?>
            	<form name="servicebooking_1"  autocomplete="off" id="servicebooking_1">
            	<fieldset style="width:700px;">
                <legend>Inspection Entry</legend>
            		<table  width="700" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                    
						  <td  width="120" height="" align="right" class="must_entry_caption" colspan="3"> Inspection No: </td>              <!-- 11-00030  -->
						  <td  width="120" colspan="3">
							<input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_booking('requires/buyer_inspection_entry_controller.php?action=service_booking_popup','Service Booking Search')" readonly placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no"/>
							<input type="hidden" id="booking_mst_id" value="">
						  </td>
                  
                    </tr>
					<tr>
						  <td  width="120" align="right" class="must_entry_caption">Job No</td>
                          <td><input class="text_boxes" type="text" style="width: 110px;"  name="txt_job_no" id="txt_job_no"/></td> 
						  <td  width="120" class="must_entry_caption" align="right">Company Name</td>
						  <td>  <? 
							  echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected, "check_exchange_rate();print_button_setting(this.value);","","" );
							?>	  
						 </td>
					     <td class="must_entry_caption" width="120" align="right">Source</td>   
                         <td>
								<? 	$source=array(1=>"In-house",2=>"Out-bound Subcontract");
                     			echo create_drop_down( "cbo_source", 120, $basis_on,"", 1, "-Select-", 1, "fnc_basis_value(this.value)",0 );?>
                        </td>
					</tr>
                  
					<tr>
						   <td  width="120" align="right">Buyer Name:</td>
                           <td id="buyer_td">
                               <?
							   echo create_drop_down( "cbo_buyer_name", 120, "select id,supplier_name from lib_supplier where status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );
							   ?> 
                           </td> 
                     
                          <td align="right" width="120">Style Ref.</td>
                          <td><input style="width:110px;" type="text" class="text_boxes_numeric"  name="txt_style_ref" id="txt_style_ref"/></td>							
						  <td align="right" width="120">Style Description:</td>  
                          <td height="10"><input class="text_boxes" type="text" style="width: 110px;"  name="txt_style_desc" id="txt_style_desc"/></td> 
                     </tr>
                       
                     <tr>
						   <td  width="120" align="right">Working Company:</td>
                           <td>
							    <?
							   		echo create_drop_down( "cbo_working_company", 120, "select id,supplier_name from lib_supplier where status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );
							    ?> 
                            </td> 
                        	<td align="right" width="120">Location</td>
                            <td><?
							   		echo create_drop_down( "cbo_location", 120, "select id,supplier_name from lib_supplier where status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select --", $selected, "",0 );
							   ?>  
                            </td>
							<td align="right" width="120">Floor/Unit</td>
                            <td> <?
							   		echo create_drop_down( "cbo_floor_unit", 120, "select id,supplier_name from lib_supplier where status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select --", $selected, "",0 );
							   ?>  
                            </td>
                     </tr>
					 <tr>
							<td align="right" width="120">Order Qty:</td>  
                        	<td height="10" ><input class="text_boxes" type="text" style="width: 110px;"  name="txt_order_qnty" id="txt_order_qnty"/></td> 
							<td  width="120" align="right">UOM:</td>
                            <td>
                               <?
							   		echo create_drop_down( "cbo_uom", 120, "select id,supplier_name from lib_supplier where status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );
							   ?> 
                            </td> 
							<td  width="120" align="right" class="must_entry_caption">Inspected By:</td>
                            <td>
                               <?
							   		echo create_drop_down( "cbo_inspected_by", 120, "select id,supplier_name from lib_supplier where status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );
							   ?> 
                            </td> 
                     </tr>
					 <tr>
						   <td  width="120" align="right" class="must_entry_caption">Ins. Company:</td>
                           <td>
                               <?
							   		echo create_drop_down( "cbo_buyer_name", 120, "select id,supplier_name from lib_supplier where status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );
							   ?> 
                            </td> 
                     
						  <td  width="120" align="right" class="must_entry_caption">Ins. Date</td>
                         <td>
                           <input class="datepicker" type="text" style="width:110px" name="txt_inspection_date" id="txt_inspection_date" onChange="check_exchange_rate();" value="<? echo date("d-m-Y")?>"/>	
						 <td  width="120" align="right" class="must_entry_caption">Inspected Lebel:</td>
                         <td>
                               <?
							   		echo create_drop_down( "cbo_inspected_lebel", 120, "select id,supplier_name from lib_supplier where status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );
							   ?> 
                            </td> 
                     </tr>
					<tr>
						<td valign="middle" align="center" colspan="6">
							<input type="button" class="image_uploader" style="width:120px" value="ADD FILE" onClick="file_uploader ( '../../', document.getElementById('txt_booking_no').value,'', 'service_acknowledgement', 2 ,1)">

						</td>
						
					</tr>
					<tr>

		
						<td valign="middle" align="center" colspan="6" class="must_entry_caption"><b>Actual PO No</b> </br>
							<input type="text" name="txt_po_no" id="txt_po_no" class="text_boxes" style="width:100px" placeholder="Write/Browse" onChange="fnRemoveHidden('hidd_po_id');" onDblClick="openmypage_po();"  />
                            <input type="hidden" id="hidd_po_id" name="hidd_po_id" style="width:50px" />
							

								<input class="text_boxes" type="hidden"   name="service_rate_from" id="service_rate_from" value=""/>
								<input class="text_boxes" type="hidden"   name="update_id" id="update_id" value=""/>
				
						</td>   
						
						
					</tr>
					<tr>
						<input type="hidden" name="update_id" id="update_id" value=""><input type="hidden" id="report_ids" >
						<td id="button_data_panel" align="center" colspan="10" height="10"></td>
					</tr>
                    </table>
                 
              </fieldset>
           </form>
              <br/>
           <form name="servicebooking_2"  autocomplete="off" id="servicebooking_2">   
              <fieldset style="width:1200px;">
                <legend>Service Booking</legend>
            		
				 
              <div id="booking_list_view">
			 			 
              </div>
			  <table  width="900" cellspacing="2" cellpadding="0" border="0">

                      

					
                    <tr align="center">
                        <td colspan="6" id="booking_list_view2"></td>	
                    </tr>
						<tr>
                        	<td align="center" colspan="10" valign="middle" class="button_container">
                              <? echo load_submit_buttons( $permission, "fnc_trims_booking", 0,0 ,"reset_form('servicebooking_1','','','','','');",1) ; ?>
                            </td>
                        </tr>
						<tr>                     
                       		  <td  width="130" height="" align="right" colspan="6"> 
								 <input type="hidden" name="cbo_process"   id="cbo_process" value="">
                        		 <input type="hidden" name="cbo_colorsizesensitive"   id="cbo_colorsizesensitive" value="">
								 <input type="hidden" name="hide_fabric_description"   id="hide_fabric_description" value="">
                         		 <input type="hidden" name="txt_all_update_id"   id="txt_all_update_id" value="">
                         	 </td>              <!-- 11-00030  -->
                        	<td  width="170" > </td>
                        </tr>
                    </table>
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