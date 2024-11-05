<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create purchase requistion
				
Functionality	:	
JS Functions	:
Created by		:	CTO/sohel 
Creation date 	: 	08-04-2013
Updated by 		:	Kausar/Jahid 		
Update date		: 	27-10-2013	   
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
echo load_html_head_contents("Purchase Requistion Info","../", 1, 1, $unicode);
?>	
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission='<? echo $permission; ?>';

	function openmypage_requisition()
	{
		var cbo_company_name = $("#cbo_company_name").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/purchase_requisition_controller.php?cbo_company_name='+cbo_company_name+'&action=purchase_requisition_popup', 'Purchase Requisition Search', 'width=900px,height=400px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_job");
			if (theemail.value!="")
			{
				freeze_window(5);
				get_php_form_data(theemail.value, "load_php_requ_popup_to_form","requires/purchase_requisition_controller" );
				show_list_view(theemail.value,'purchase_requisition_list_view_dtls','purchase_requisition_list_view_dtls','requires/purchase_requisition_controller','setFilterGrid("list_view",-1)');
				disable_enable_fields('cbo_company_name*cbo_item_category_id*cbo_store_name',1);
				set_button_status(1, permission, 'fnc_purchase_requisition',1,0);
				release_freezing();
			}
		}
	}
	
	function openmypage_manual_requisition()
	{
		var cbo_company_name = $("#cbo_company_name").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/purchase_requisition_controller.php?cbo_company_name='+cbo_company_name+'&action=purchase_manual_requisition_popup', 'Purchase Manual Requisition Search', 'width=900px,height=400px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("txt_manual_req");
			if (theemail.value!="")
			{
				//freeze_window(5);
				document.getElementById('txt_manual_req').value = theemail.value;
				//release_freezing();
			}
		}
	}
			
	function fnc_purchase_requisition( operation )
	{
		var is_approved=$('#is_approved').val();
		
		if(is_approved==1)
		{
			alert("Requisition is Approved. So Change Not Allowed");
			return;	
		}
		
		if (form_validation('cbo_company_name*cbo_item_category_id*cbo_store_name*txt_date_from','Company Name*Item Catagory*Store Name*Requisation Date')==false)
		{
			return;
		}
		else
		{
			
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_requisition_no*cbo_company_name*cbo_item_category_id*cbo_location_name*cbo_division_name*cbo_department_name*cbo_section_name*txt_date_from*cbo_store_name*cbo_pay_mode*cbo_source*cbo_currency_name*txt_date_delivery*txt_remarks*txt_manual_req*update_id*txt_req_by',"../");
			/*-------------additional code-----------*/
			/*var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_requisition_no*cbo_company_name*cbo_item_category_id*cbo_location_name*cbo_division_name*cbo_department_name*cbo_section_name*txt_date_from*cbo_store_name*cbo_pay_mode*cbo_source*cbo_currency_name*txt_date_delivery*txt_remarks*txt_manual_req*update_id*txt_brand*txt_model_name*cbo_origin*txt_req_by',"../");*/
			
			freeze_window(operation);
			http.open("POST","requires/purchase_requisition_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_purchase_requisition_reponse;
		}
	}

	function fnc_purchase_requisition_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			// if (reponse[0].length>2) reponse[0]=10;
			
			show_msg(reponse[0]);
			if( reponse[0]==0 ||  reponse[0]==1)
			{
				document.getElementById('txt_requisition_no').value = reponse[1];
				document.getElementById('update_id').value = reponse[2];
				disable_enable_fields('cbo_company_name*cbo_item_category_id*cbo_store_name',1);
				set_button_status(1, permission, 'fnc_purchase_requisition',1,0);
			}
			else if( reponse[0]==2)
			{
				reset_form('purchaserequisition_1*purchaserequisition_2','item_category_div*purchase_requisition_list_view_dtls','','','disable_enable_fields(\'cbo_company_name\');$(\'#tbl_purchase_item tbody tr:not(:first)\').remove();')
				/*------write new code below if necerssary-------*/
			}
			else if(reponse[0]==15) 
			{ 
				 setTimeout('fnc_purchase_requisition( 0 )',8000); 
			}
			release_freezing();
		}
	}


	function openmypage()
	{
		var txt_requisition_no=$('#txt_requisition_no').val();
		var cbo_store_name=$('#cbo_store_name').val();
		if(txt_requisition_no=="")
		{
			alert("Save Data First");return;
		}
		if (form_validation('cbo_company_name*cbo_item_category_id*cbo_store_name','Company Name*Item Catagory*Store Name')==false)
		 {
			 return;
		 }
		 else
		 {
			 var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_item_category_id').value+"_"+document.getElementById('cbo_store_name').value;
			 var page_link='requires/purchase_requisition_controller.php?action=account_order_popup&data='+data
			 var title='Search Item Account';
			 emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1210px,height=400px,center=1,resize=0,scrolling=0','')
				
			 emailwindow.onclose=function()
			 {
			 	var theemail=this.contentDoc.getElementById("item_1").value; 
				var item_category=document.getElementById('cbo_item_category_id').value;
				if(theemail!="")
				{
					var tot_row = $('#tbl_purchase_item tbody tr').length;
					
					
					if($('#itemaccount_1').val()=="") tot_row=0; else tot_row=tot_row;
					var data=theemail+"**"+tot_row+"**"+item_category+"**"+cbo_store_name;	
					var list_view_orders = return_global_ajax_value( data, 'load_php_popup_to_form', '', 'requires/purchase_requisition_controller');
						
					var row_num=1;
					var order_no=$('#itemaccount_'+row_num).val();
					if(order_no=="")
					{
						$("#tbl_purchase_item tr:last").remove();
					}
						$("#tbl_purchase_item tbody:last").append(list_view_orders);
						
					set_all_onclick();
					release_freezing();
					
				}
			}
		}
	}

	function calculate_val()
	{
		var tot_row=$('#tbl_purchase_item'+' tbody tr').length;  
		for(var i=1; i<=tot_row; i++)
		{
			var quantity_val=parseFloat(Number($('#quantity_'+i).val()));
			var rate_val=parseFloat(Number($('#rate_'+i).val()));
			var attached_val=quantity_val*rate_val;
			document.getElementById('amount_'+i).value = number_format (attached_val, 2,'.',"");
		}
	}

	function fnc_purchase_requisition_dtls( operation )
	{
		if (form_validation('update_id*quantity_1','Master Table*Quantity')==false)
		{
		  return;
		}
		else
		{
			var is_approved=$('#is_approved').val();
			
			if(is_approved==1)
			{
				alert("Requisition is Approved. So Change Not Allowed");
				return;	
			}
			
			var tot_row=$('#tbl_purchase_item'+' tbody tr').length;
			var update_id=document.getElementById('update_id').value;
			var data="action=save_update_delete_dtls&operation="+operation +"&tot_row="+tot_row+"&update_id="+update_id;
			//var update_id=document.getElementById('update_id').value;
			var data1='';
			
			for(var i=1; i<=tot_row; i++)
			{
				if(trim($("#quantity_"+i).val())!="")
				{
					/*data1+=get_submitted_data_string('itemaccount_'+i+'*sub_group_'+i+'*itemdescription_'+i+'*itemsize_'+i+'*hiddenitemgroupid_'+i+'*txtreqfor_'+i+'*txtuom_'+i+'*hiddentxtuom_'+i+'*quantity_'+i+'*rate_'+i+'*amount_'+i+'*stock_'+i+'*reorderlable_'+i+'*txt_remarks_'+i+'*cbostatus_'+i+'*item_'+i+'*hiddenid_'+i,"../",i);*/
					/*-------additional code-------------*/
					data1+=get_submitted_data_string('itemaccount_'+i+'*sub_group_'+i+'*itemdescription_'+i+'*itemsize_'+i+'*hiddenitemgroupid_'+i+'*txtreqfor_'+i+'*txtuom_'+i+'*hiddentxtuom_'+i+'*quantity_'+i+'*txtbrand_'+i+'*txtmodelname_'+i+'*cboOrigin_'+i+'*rate_'+i+'*amount_'+i+'*stock_'+i+'*reorderlable_'+i+'*txt_remarks_'+i+'*cbostatus_'+i+'*item_'+i+'*hiddenid_'+i,"../",i);
					//txt_brand*txt_model_name*cbo_origin'

					//data1+=get_submitted_data_string('itemaccount_'+i+'*sub_group_'+i+'*itemdescription_'+i+'*itemsize_'+i+'*hiddenitemgroupid_'+i+'*txtreqfor_'+i+'*txtuom_'+i+'*hiddentxtuom_'+i+'*quantity_'+i+'*rate_'+i+'*amount_'+i+'*stock_'+i+'*reorderlable_'+i+'*cbostatus_'+i+'*item_'+i+'*hiddenid_1',"../",i);
				}
			}
		    data=data+data1;
		   // alert (data1);

		    freeze_window(operation);
		    http.open("POST","requires/purchase_requisition_controller.php",true);
		    http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		    http.send(data);
		    http.onreadystatechange = fnc_purchase_requisition_dtls_reponse;
		}
	}

	function fnc_purchase_requisition_dtls_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			show_msg(reponse[0]);
			
			if( reponse[0]==0 ||  reponse[0]==1 ||  reponse[0]==2)
			{
				show_list_view(reponse[1],'purchase_requisition_list_view_dtls','purchase_requisition_list_view_dtls','requires/purchase_requisition_controller','setFilterGrid("list_view",-1)');
			//	disable_enable_fields('cbo_item_category_id');
				$('#cbo_item_category_id').attr('disabled','disabled');
				reset_form('purchaserequisition_2','','','','$(\'#tbl_purchase_item tbody tr:not(:first)\').remove();',0);
				set_button_status(0, permission, 'fnc_purchase_requisition_dtls',2);
			}
			release_freezing();
		}
	}
	
	function generate_report(type)
	{
		if ( $('#txt_requisition_no').val()=='')
		{
			alert ('Requisition Not Save.');
			return;
		} 
		if(type==3)
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Item Group\nPress  \"OK\"  to Show Item Group");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}
		}

		else if(type==5)
		{
			
			var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remarks').val()+'*'+type+'*'+show_item, "purchase_requisition_print_3", "requires/purchase_requisition_controller" ) ;
			//return;
			show_msg("3");
		}
		
		
		else
		{	
			 var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title, "purchase_requisition_print", "requires/purchase_requisition_controller" ) ;
			//return;
			show_msg("3");
		}
	}

</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
 	<?  echo load_freeze_divs ("../",$permission);  ?>
    <fieldset style="width:900px;height:auto;">
    <legend>Purchase Requisition</legend> 
    <form name="purchaserequisition_1" id="purchaserequisition_1" autocomplete="off"> 
        <table cellpadding="0" cellspacing="2" width="100%">
            <tr>
                <td colspan="6" align="center" ><b>Requisition No</b>
                <input name="txt_requisition_no"  id="txt_requisition_no" placeholder="Double Click to Search" onDblClick="openmypage_requisition();" readonly  style="width:158px "  class="text_boxes"/>
                </td>
            </tr>
        <tr><td height="15"></td></tr>
        <tr>
            <td width="110" class="must_entry_caption">Company</td>
            <td width="160">
                <input type="hidden" name="update_id" id="update_id" value="">
                <input type="hidden" name="is_approved" id="is_approved" value="">
				<?php 
					 echo create_drop_down( "cbo_company_name", 160,"select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/purchase_requisition_controller', this.value, 'load_drop_down_location','location_td');load_drop_down( 'requires/purchase_requisition_controller', this.value, 'load_drop_down_division','division_td');load_drop_down( 'requires/purchase_requisition_controller', document.getElementById('cbo_item_category_id').value+'_'+this.value, 'load_drop_down_stor','stor_td');" );
                 ?>   	
            </td>
            <td width="110" class="must_entry_caption">Item Category</td>
            <td width="160">
				<?php 
					echo create_drop_down( "cbo_item_category_id", 160,$item_category,"", 1, "-- Select --", $selected, "load_drop_down( 'requires/purchase_requisition_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_stor','stor_td');show_list_view(this.value,'item_category_details', 'item_category_div', 'requires/purchase_requisition_controller', '' );","","","","","1,2,3,12,13,14,24,25");
                 ?> 
            </td>
            <td width="110">Location</td>
            <td id="location_td" width="160">
				<?php 
					 echo create_drop_down( "cbo_location_name", 160,$blank_array,"", 1, "-- Select --", $selected, "" );
                 ?> 	
            </td>
        </tr>
        <tr>
            <td width="110">For Division</td>
            <td id="division_td" width="160">
			   <?php 
					echo create_drop_down( "cbo_division_name", 160,$blank_array,"", 1, "-- Select --", $selected, "" );
               ?> 	
            </td>
            <td width="110">For Department</td>
            <td id="department_td" width="160">
			   <?php 
					echo create_drop_down( "cbo_department_name", 160,$blank_array,"", 1, "-- Select --", $selected, "" );
               ?> 	
            </td>
            <td width="110">For Section</td>
            <td id="section_td" width="160">
				<?php 
					echo create_drop_down( "cbo_section_name", 160,$blank_array,"", 1, "-- Select --", $selected, "" );
                ?> 	
            </td>
        </tr>
        <tr>
            <td width="110" class="must_entry_caption">Req. Date</td>
            <td width="150">
                <input type="text" name="txt_date_from"  style="width:150px"  id="txt_date_from" class="datepicker" value="" />
            </td>
            <td width="110" class="must_entry_caption">Store Name</td>
            <td id="stor_td" width="160">
				<?php 
					 echo create_drop_down( "cbo_store_name", 160,$blank_array,"", 1, "-- Select --", $selected, "" );
               	?> 
            </td>
            <td width="110">Pay Mode</td>
            <td width="160">
				<?php 
					echo create_drop_down( "cbo_pay_mode", 160,$pay_mode,"", 0, "", $selected, "","" );
                ?> 
            </td>
        </tr>
        <tr>
            <td width="110">Source</td>
            <td id="mode_td">
				 <?php 
					 echo create_drop_down( "cbo_source", 160, $source,"", 0, "", 3, "","" );
                 ?> 
            </td>
            <td>Currency</td>
            <td> 
				<?
					echo create_drop_down( "cbo_currency_name", 160,$currency,"", 1, "-- Select --", $selected, "" );
                ?> 
            </td>
            <td width="110">Delivery Date</td>
            <td width="150">
                <input type="text" name="txt_date_delivery"  style="width:150px"  id="txt_date_delivery" class="datepicker" value="" />
            </td>
        </tr>
        <tr>
            <td width="110">Remarks</td>
            <td >
                <input type="text" name="txt_remarks"  id="txt_remarks" style="width:300px " value="" class="text_boxes" maxlength="50" title="Maximum 50 Character"/>
            </td>
             <td width="20" align="">Req.By</td>
            <td>
                <input type="text" name="txt_req_by"  id="txt_req_by" style="width:150px " value="" class="text_boxes" maxlength="50" title="Maximum 50 Character"/>
            </td>
            <td width="50">Manual Req. No.</td>
            <td width="100">
                <input type="text" name="txt_manual_req"  id="txt_manual_req" style="width:150px " class="text_boxes" onDblClick="openmypage_manual_requisition();" placeholder="Browse Or Write" />
            </td>	
        </tr>

         <!--...................... additional code....................... -->

         <!-- <tr>
                <td>Brand</td>
                <td><Input name="txt_brand" ID="txt_brand"  style="width:145px" class="text_boxes" autocomplete="off" /></td>
                <td>Origin</td>
                <td><?
                //echo create_drop_down( "cbo_origin", 155, "select country_name,id from lib_country comp where is_deleted=0  and status_active=1 order by country_name",'id,country_name', 1, '--- Select Country ---', 0 );            
                ?></td>
         
                <td>Model</td>
                <td><Input name="txt_model_name" ID="txt_model_name"  style="width:145px" class="text_boxes" autocomplete="off"  maxlength="50" title="Maximum 50 Character"></td>
                
            </tr> -->

        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td colspan="6" align="center" class="button_container"><div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div>
				<? 
					echo load_submit_buttons($permission, "fnc_purchase_requisition", 0,0,"reset_form('purchaserequisition_2*purchaserequisition_1','','','','disable_enable_fields(\'cbo_company_name\');$(\'#tbl_purchase_item tbody tr:not(:first)\').remove();')",1);//item_category_div*purchase_requisition_list_view_dtls*approved
                 ?>
                 <input type="button" name="search" id="search" value="With Group" onClick="generate_report(1)" style="width:100px" class="formbuttonplasminus" />
                 <input type="button" name="search" id="search" value="WithOut group" onClick="generate_report(2)" style="width:100px" class="formbuttonplasminus" />
                 <input type="button" name="search" id="search" value="Print Report" onClick="generate_report(3)" style="width:100px" class="formbuttonplasminus" />
                 <input type="button" name="search" id="search" value="Print Report 2" onClick="generate_report(4)" style="width:100px" class="formbuttonplasminus" />
                 <input type="button" name="search" id="search5" value="Print Report 3" onClick="generate_report(5)" style="width:100px" class="formbuttonplasminus" />
            </td>		
        </tr>
     </table>
  </form>
  </fieldset>
    <fieldset style="width:1030px; margin-top:10px;">
        <legend>Purchase Requisition Details</legend>
        <form name="purchaserequisition_2" id="purchaserequisition_2" autocomplete="off">
       		<div id="item_category_div" style="max-height:200px; overflow:auto;"></div>
        </form>
    </fieldset>
    
    <fieldset style="width:1060px; margin-top:10px;">
            <legend>Purchase Requisition List</legend>
            <div id="purchase_requisition_list_view_dtls" overflow:auto;> </div>
   </fieldset>
</div>
</body> 
<script src="../includes/functions_bottom.js" type="text/javascript"></script>  
</html>
