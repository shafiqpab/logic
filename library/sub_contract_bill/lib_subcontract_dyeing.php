<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Dyeing and Finishing Charge Set up
Functionality	:	
JS Functions	:
Created by		:	Sohel 
Creation date 	: 	04-10-2013
Updated by 		: 	Kausar	
Update date		: 	29-05-13	   
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
echo load_html_head_contents("Dyeing and Finishing Charge Set up", "../../", 1, 1,$unicode,'','');
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
	 
	 var str_const_comp = [<? echo substr(return_library_autocomplete( "select const_comp from lib_subcon_charge group by const_comp", "const_comp" ), 0, -1); ?>];
	 $(document).ready(function(e)
	 {
            $("#text_const_compo").autocomplete({
			 source: str_const_comp
		  });
     });

	function fnc_dyeing_charge( operation )
	{
		if (form_validation('cbo_company_id*text_const_compo*cbo_process_type*cbo_process_id*cbo_dia_width*text_in_house_rate*cbo_uom*cbo_rate_type','Company Name*Const Comp*Process Type*Process Name*Width/Dia type*In House Rate*UOM*Rate Type')==false)
		{
			return;
		}
		else
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_id*text_const_compo*txt_cons_comp_id*cbo_process_type*cbo_process_id*txt_color*cbo_dia_width*text_in_house_rate*cbo_uom*txt_customer_rate*cbo_rate_type*cbo_buyer_id*cbo_status*update_id*cbo_color_range_id*text_dia_range*txt_gsm_range*text_efficiency_range',"../../");
			//alert (data);
			freeze_window(operation);
			http.open("POST","requires/lib_subcontract_dyeing_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_dyeing_charge_reponse;
		}
	}

	function fnc_dyeing_charge_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert (http.responseText);
			var reponse=trim(http.responseText).split('**');
			document.getElementById('update_id').value  = reponse[1];
			show_msg(reponse[0]);
			show_list_view(reponse[2],'list_view_subcon_dying_charge','list_view_subcon_dying_charge','requires/lib_subcontract_dyeing_controller','setFilterGrid("list_view",-1)');
			reset_form('dyeingfinishincharge_1','','','');
			set_button_status(0, permission, 'fnc_dyeing_charge',1);
			release_freezing();
		}
	}
	
	function openmypage_const_comp()
	{
		var data=document.getElementById('cbo_company_id').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/lib_subcontract_dyeing_controller.php?data='+data+'&action=const_comp_popup','Construction & Composition Popup', 'width=880px,height=400px,center=1,resize=1,scrolling=0','../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("hddn_sll_data");//Access form field with id="emailfield"
			if (theemail.value!="")
			{
				var pop_data=trim(theemail.value).split('***');
				$('#txt_cons_comp_id').val(pop_data[0]);
				$('#text_const_compo').val(pop_data[1]);
				//$('#text_gsm').val(pop_data[2]);
			}
		}
	}
	
	function service_work_order()
	{
		var cbo_company_name = $('#cbo_company_id').val();
		var save_string = $('#save_string').val();
		if (form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		if($("#update_id").val()=="")
		{
			alert("At First Save Master Part.");
			return;
		}
		var title="Supplier Work Order Rate Info";
		var page_link = 'requires/lib_subcontract_dyeing_controller.php?cbo_company_name='+cbo_company_name+'&cbo_rate_type='+$("#cbo_rate_type").val()+'&update_id='+$("#update_id").val()+'&action=Supplier_workorder_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var save_string=this.contentDoc.getElementById("hide_save_string").value;	
		
			$('#save_string').val(save_string);
		}
	}

</script>
</head>
<body onLoad="set_hotkey()">
    <div align="center">
		<? echo load_freeze_divs ("../../", $permission); ?>
        <fieldset style="width:800px;">
        	<legend>Dyeing & Finishin Charage</legend>
            <form name="dyeingfinishincharge_1" id="dyeingfinishincharge_1">
                <table cellpadding="0" cellspacing="2" width="790">
                    <tr>
                    	<td colspan="6" align="center"></td>
                    </tr>
                    <tr align="left">
                        <td class="must_entry_caption" width="110">Company Name<input type="hidden" name="update_id" id="update_id" value=""/></td>
                        <td>
							<?
                            	echo create_drop_down( "cbo_company_id", 150, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down('requires/lib_subcontract_dyeing_controller', this.value, 'load_drop_down_buyer_name', 'buyer_td' );");
                            ?>
                        </td>
                        <td class="must_entry_caption" width="110">Const. Compo. </td>
                        <td colspan="3">
                        	<input type="text" name="text_const_compo" id="text_const_compo" class="text_boxes" style="width:405px" value="" readonly placeholder="Browse" onDblClick="openmypage_const_comp();" />
                            <input type="hidden" name="txt_cons_comp_id" id="txt_cons_comp_id" value=""/>
                            <input type="hidden" name="save_string" id="save_string">
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption" width="110">Process Type </td>
                        <td>
							<? 
							    asort($process_type);
                            	echo create_drop_down( "cbo_process_type", 150, $process_type,'', 1, "-- Select Type --", $selected, ""  );
                            ?>
                        </td>
                        <td class="must_entry_caption" width="110">Process Name</td>
                        <td>
							<?
                            	echo create_drop_down( "cbo_process_id", 150, $conversion_cost_head_array,'', 1, "-- Select Name --", $selected, "","","","","","1,2,3,4,101,120,121,122,123,124");
                            ?>
                        </td>
                        <td width="110">Color</td>
                        <td><input type="text"  name="txt_color" id="txt_color" class="text_boxes" style="width:140px;" /></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption" >Width/Dia type </td>
                        <td><? echo create_drop_down( "cbo_dia_width", 150, $fabric_typee,'', 1, "--Select Dia/width--", $selected, "" ); ?></td>
                        <td class="must_entry_caption">In House Rate </td>
                        <td><input type="text" name="text_in_house_rate" id="text_in_house_rate" class="text_boxes_numeric" style="width:140px;" /></td>
                        <td class="must_entry_caption">UOM </td>
                        <td><? echo create_drop_down( "cbo_uom", 150, $unit_of_measurement,'', 1, "--Select UOM--", $selected, "","","1,2,12,27"); ?></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Rate type</td>
                        <td><? echo create_drop_down( "cbo_rate_type", 150, $production_process,'', 1, "-- Select Type --", $selected, "","","3,4,6,7,26,28"); ?></td>
                        <td>Customer Rate</td>
                        <td><input type="text"  name="txt_customer_rate" id="txt_customer_rate" class="text_boxes_numeric" style="width:140px;" /></td>
                        <td>Subcon Buyer </td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_id", 150, $blank_array,'', 1, "-- Select Buyer --", $selected, "","" ,"0" ); ?></td>
                    </tr>
					<tr>
                        <td>Dia Range</td>
						<td><input type="text" name="text_dia_range" id="text_dia_range" class="text_boxes" style="width:140px;" /></td>
                        <td>GSM Range</td>
						<td><input type="text"  name="txt_gsm_range" id="txt_gsm_range" class="text_boxes" style="width:140px;" /></td>
						<td>Color Range </td>
                        <td id=""><? echo create_drop_down( "cbo_color_range_id", 150, $color_range,'', 1, "-- Select --", $selected, "","" ,"" ); ?></td>
                    </tr>
                    <tr>
						<td>Efficiency Range</td>
						<td><input type="text" name="text_efficiency_range" id="text_efficiency_range" class="text_boxes" style="width:140px;" /></td>
                        <td class="must_entry_caption"> Status </td>
                        <td><? echo create_drop_down( "cbo_status", 150, $row_status,'', 2, "", $selected, "","" ,"1,2" ); ?></td>
                        <td height="25" valign="middle" colspan="2" align="right">
                            <input type="button" class="formbuttonplasminus" style="width:170px" value="Supplier Work Order Rate" onClick="service_work_order()">
                        </td>                      
                    </tr>
                    <tr>
                    	<td colspan="6" height="15"></td>
                    </tr>
                    <tr>
                        <td colspan="6" align="center" class="button_container">
							<?
                            	echo load_submit_buttons( $permission, "fnc_dyeing_charge", 0,0,"reset_form('dyeingfinishincharge_1','','','')",1);
                            ?>
                        </td>
                    </tr>
                </table>
            </form>
        </fieldset>
        <br>
        <fieldset style="width:970px;">
            <legend>List View</legend>
            <table cellpadding="0" width="1040" cellspacing="2">
                <tr>
                    <td id="list_view_subcon_dying_charge">
                        <?
                            $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
                            $company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
                            $color_library_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");  
                            $arr=array (0=>$company_arr,2=>$process_type,3=>$conversion_cost_head_array,4=>$color_library_arr, 5=> $color_range, 6=>$fabric_typee,11=>$unit_of_measurement,12=>$production_process,14=>$buyer_arr,15=>$row_status);
                            echo  create_list_view ( "list_view", "Company Name,Const. Compo.,Process Type,Process Name,Color,Color Range,Width/Dia type,Dia Range,GSM Range,Efficiency Range,In House Rate,UOM,Rate type,Cust. Rate,Buyer,Status", "90,130,70,70,70,70,80,80,80,80,60,40,60,70,80,50","1280","250",1, "select id, comapny_id, const_comp, process_type_id, process_id, color_id, color_range_id, width_dia_id,dia_range, gsm_range, efficiency_range, in_house_rate, uom_id, rate_type_id ,customer_rate, buyer_id, status_active from lib_subcon_charge where status_active!=0 and is_deleted=0 and rate_type_id in (3,4,7,6)", "get_php_form_data", "id","'load_php_data_to_form'", 1, "comapny_id,0,process_type_id,process_id,color_id,color_range_id,width_dia_id,0,0,0,0,uom_id,rate_type_id,0,buyer_id,status_active", $arr, "comapny_id,const_comp,process_type_id,process_id,color_id,color_range_id,width_dia_id,dia_range,gsm_range,efficiency_range,in_house_rate,uom_id,rate_type_id,customer_rate,buyer_id,status_active","requires/lib_subcontract_dyeing_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0,0,0,2,0,0,2,0,0');
							exit();
                       ?> 
                    </td>
                </tr>
            </table>
        </fieldset>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>