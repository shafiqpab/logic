<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Material Allocation
				
Functionality	:	
JS Functions	:
Created by		:	MONZU
Creation date 	: 	22-06-2013
Updated by 		: 		
Update date		: 	Kaiyum	   
QC Performed BY	:	23-10-2016 [add 'job no' popup]	
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Material Allocation","../../", 1, 1, $unicode);

?>	
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission='<? echo $permission; ?>';

function openmypage_booking(page_link,title)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1080px,height=390px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_booking");
		if (theemail.value!="")
		{
			freeze_window(5);
			reset_form('materialallocation_1','','','cbo_item_category,1*txt_allocation_date,<? echo date("d-m-Y"); ?>','');
			get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/yarn_allocation3rd_controller");
			show_list_view(theemail.value,'fabric_description_list','booking_list','requires/yarn_allocation3rd_controller','');
			show_list_view(document.getElementById('txt_job_no').value,'yarn_description_list','yarn_list','requires/yarn_allocation3rd_controller','');
			show_list_view(document.getElementById('txt_job_no').value+'_'+document.getElementById('cbo_item_category').value+'_'+document.getElementById('txt_booking_no').value,'show_item_active_listview','item_list_view','requires/yarn_allocation3rd_controller','');
			//set_button_status(1, permission, 'fnc_fabric_booking',1);
			//fnc_show_booking(1)
			release_freezing();
		}
	}
}

function openmypage(page_link,title)
{
  	//var garments_nature=document.getElementById('garments_nature').value;
	page_link=page_link;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1206px,height=450px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=(this.contentDoc.getElementById("selected_job").value).split("_");
		var job_no=theemail[0];
		var po_no=theemail[1];
		var po_id=theemail[2];
		//var theemail=this.contentDoc.getElementById("selected_job").value;
/*		var a=document.getElementById('hidden_job_no').value = theemail;
		alert(a);*/
		if (theemail.value!="")
		{
			//freeze_window(5);
		    reset_form('materialallocation_1','','','cbo_item_category,1*txt_allocation_date,<? echo date("d-m-Y"); ?>','');
			$("#txt_order_no").val(po_no);
			$("#txt_order_id").val(po_id);
			
			get_php_form_data(job_no, "populate_data_from_search_popup_job", "requires/yarn_allocation3rd_controller" );
			show_list_view(job_no,'fabric_description_list','booking_list','requires/yarn_allocation3rd_controller','');
			show_list_view(document.getElementById('txt_job_no').value,'yarn_description_list','yarn_list','requires/yarn_allocation3rd_controller','');
			show_list_view(document.getElementById('txt_job_no').value+'_'+document.getElementById('cbo_item_category').value+'_'+document.getElementById('txt_booking_no').value,'show_item_active_listview','item_list_view','requires/yarn_allocation3rd_controller','');
			//set_button_status(1, permission, 'fnc_fabric_booking',1);
			//fnc_show_booking(1)
			//release_freezing();
		}
	}
}

	function open_item_popup(page_link,title)
	{
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var cbo_item_category=document.getElementById('cbo_item_category').value;
		var txt_booking_qnty=document.getElementById('txt_booking_qnty').value
		var txt_item_id=document.getElementById('txt_item_id').value;
		if( cbo_company_name ==0 )
		{
			alert("Select Company Name");
			return;
		}
		
		
		page_link=page_link+'&cbo_company_name='+cbo_company_name+'&cbo_item_category='+cbo_item_category+'&txt_booking_qnty='+txt_booking_qnty+'&txt_item_id='+txt_item_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=450px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("product_id");
			var theemail_number=this.contentDoc.getElementById("product_name");
			var theemail_qnty=this.contentDoc.getElementById("available_qnty");
			var theemail_uom=this.contentDoc.getElementById("unit_of_measurment");
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById('txt_item_id').value=theemail.value;
				document.getElementById('txt_item').value=theemail_number.value;
				document.getElementById('available_qnty').value=theemail_qnty.value;
				document.getElementById('cbo_uom').value=theemail_uom.value;
				release_freezing();
			}
		}
	}
	
	function open_qnty_popup(page_link,title)
	{
		var txt_order_id=document.getElementById('txt_order_id').value;
		var txt_item=document.getElementById('txt_item').value;
		var available_qnty=document.getElementById('available_qnty').value;
		var txt_qnty=document.getElementById('txt_qnty').value;
		var qnty_breck_down=document.getElementById('qnty_breck_down').value;
		var txt_booking_qnty=document.getElementById('txt_booking_qnty').value
		if( txt_order_id ==0 )
		{
			alert("Select Order No");
			return;
		}
		if( txt_item =="" )
		{
			alert("Insert Item");
			return;
		}
		
		page_link=page_link+'&txt_order_id='+txt_order_id+'&available_qnty='+available_qnty+'&txt_qnty='+txt_qnty+'&qnty_breck_down='+qnty_breck_down+'&txt_booking_qnty='+txt_booking_qnty;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=820px,height=450px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("allocated_qnty");
			var theemail_number=this.contentDoc.getElementById("qnty_breck_down");
			if (theemail.value!="")
			{
				freeze_window(5);
			     document.getElementById('txt_qnty').value=theemail.value;
				 document.getElementById('qnty_breck_down').value=theemail_number.value;
				 release_freezing();
			}
		}
	}
	
function fnc_material_allocation_entry( operation )
{
	/*if(operation==2)
	{
		show_msg('13');
		return;
	}
	*/
	if (form_validation('txt_job_no*txt_allocation_date*txt_qnty','Job No*Allocation Date*Qnty')==false)
	{
		return;
	}
	else
	{
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_job_no*txt_order_id*txt_item_id*txt_item_id_old*txt_qnty*qnty_breck_down*cbo_item_category*txt_allocation_date*update_id*txt_old_qnty*txt_booking_no',"../../");
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/yarn_allocation3rd_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_material_allocation_entry_reponse;
	}
}

function fnc_material_allocation_entry_reponse()
{
	if(http.readyState == 4) 
	{
		//alert(http.responseText);
		var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(reponse[0]);
		if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		{
			show_list_view(document.getElementById('txt_job_no').value+'_'+document.getElementById('cbo_item_category').value+'_'+document.getElementById('txt_booking_no').value,'show_item_active_listview','item_list_view','requires/yarn_allocation3rd_controller','');
			reset_form('','','txt_item_id_old*txt_item_id*txt_old_qnty*txt_item*txt_qnty*cbo_uom*available_qnty*qnty_breck_down*update_id','cbo_item_category,1*txt_allocation_date,<? echo date("d-m-Y"); ?>','');
	
			set_button_status(0, permission, 'fnc_material_allocation_entry',1);
		}
		release_freezing();
	}
}



</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<?  echo load_freeze_divs ("../../",$permission);  ?>
	<div style="float:left; margin-left:5px">
      	<fieldset style="width:950px;height:auto;">
    	<legend>Material Allocation</legend> 
			<form name="materialallocation_1" id="materialallocation_1" autocomplete="off"> 
				<table cellpadding="0" cellspacing="2" width="100%">
                	<tr>
                    	<td width="130"> Booking No </td>            
                        <td width="170" >
                            <input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_booking('requires/yarn_allocation3rd_controller.php?action=fabric_booking_popup','fabric Booking Search')" readonly placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no"/>
                        </td>
                    	<td  width="110" class="must_entry_caption">Job No</td>
                        <td width="190">
                        
                         <input style="width:158px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/yarn_allocation3rd_controller.php?action=order_popup','Job/Order Selection Form')" class="text_boxes" placeholder="Browse Job No" name="txt_job_no" id="txt_job_no" readonly />  
                        <!--
							<input name="txt_job_no" id="txt_job_no" placeholder="Display" readonly style="width:158px" class="text_boxes" />-->
						</td>
                        <td width="110">Company</td>
						<td width="190">
                        <?php 
						 echo create_drop_down( "cbo_company_name", 170,"select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "Display", "", "load_drop_down( 'requires/yarn_allocation3rd_controller', this.value, 'load_drop_down_buyer', 'buyer_td' )",1 );
						 ?>   	
						</td>
                    </tr>
					<tr>
                        <td width="130">Buyer</td>
						<td id="buyer_td" width="170">
							<?php 
                                echo create_drop_down( "cbo_buyer_name", 170,$blank_array,"", 1, "Display", "", "",1,"","","","");
                            ?> 
						</td>
                        <td width="130">Order No</td>
						<td width="170">
                            <input type="text" name="txt_order_no"  id="txt_order_no" style="width:160px " class="text_boxes"  readonly />  <!--placeholder="Click to Search"-->
                            <input type="hidden" name="txt_order_id"  id="txt_order_id" style="width:160px " readonly />
						</td>
                        <td width="100">Item Category</td>
						<td id="" width="200">
                        <?php 
						 echo create_drop_down( "cbo_item_category", 170,$item_category,"", "", "", 1, "",1 );
						 ?> 	
						</td>
					</tr>
					<tr>
                    	<td width="130" class="must_entry_caption">Allocation Date</td>
						<td width="170">
                      		<input type="text" name="txt_allocation_date"  id="txt_allocation_date" style="width:160px" value="<? echo date("d-m-Y")?>" class="datepicker" />
						</td>
						<td width="130">Allocated Yarn</td>
						<td width="170">
                           <input type="text" name="txt_item"  id="txt_item" style="width:160px "  placeholder="Click to Search" class="text_boxes" onClick="open_item_popup( 'requires/yarn_allocation3rd_controller.php?action=open_item_popup','Item List' );"  />
                           <input type="hidden" name="txt_item_id"  id="txt_item_id" style="width:160px "  />
                           <input type="hidden" name="txt_item_id_old"  id="txt_item_id_old" style="width:160px "  />
						</td>
                        <td width="110" class="must_entry_caption">Qnty</td>
						<td id="section_td" width="190">
							<input type="text" name="txt_qnty"  id="txt_qnty" style="width:90px " value="" class="text_boxes_numeric" placeholder="Click" onClick="open_qnty_popup( 'requires/yarn_allocation3rd_controller.php?action=open_qnty_popup','Qnty List' )" />
                        	<?php 
								echo create_drop_down( "cbo_uom", 60,$unit_of_measurement,"", 1, "Display", $selected, "",1,"","","","");
						 	?> 
                      		<input type="hidden" name="txt_old_qnty"  id="txt_old_qnty" style="width:90px " value="" class="text_boxes_numeric" />
                            <input type="hidden" name="available_qnty"  id="available_qnty" style="width:90px " value="" class="text_boxes_numeric" readonly />
                            <input type="hidden" name="qnty_breck_down"  id="qnty_breck_down" style="width:90px "  class="text_boxes"/>	
                            <input type="hidden" name="update_id"  id="update_id" style="width:90px "  class="text_boxes"/>
                            <input type="hidden" name="txt_booking_qnty" id="txt_booking_qnty" value="" />	
                           
						</td>
					</tr>
                  	<tr>
                 		<td></td>
                  	</tr>
				  	<tr>
				  		<td colspan="6" align="center" class="button_container">
                     		<? 
								$date=date('d-m-Y');
                     			echo load_submit_buttons($permission, "fnc_material_allocation_entry", 0,0 ,"reset_form('materialallocation_1','','','cbo_item_category,1*txt_allocation_date,".$date."','')",1);
						 	?>
                        </td>		
					</tr>
				</table>
      		</form>
		</fieldset>
		<fieldset style="width:950px; margin-top:10px; float:left">
        <legend>Allocation List</legend>
       		<div id="item_list_view"> </div>
		</fieldset>
	</div>
	<div style="float:left; margin-left:10px; margin-top:5px">
        <div id="booking_list"></div>
            <br/>
        <div id="yarn_list"></div>
	</div>
</div>
</body> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>  
</html>
