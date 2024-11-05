<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Knit Garments Full Job Copy
Functionality	:
JS Functions	:
Created by		:	Kausar
Creation date 	: 	19-06-2016
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
echo load_html_head_contents("Full Job Copy","../../", 1, 1, $unicode,'','');
?>
<script type="text/javascript">

	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	function openmypage(page_link,title)
	{
		var garments_nature=document.getElementById('garments_nature').value;
		page_link=page_link+'&garments_nature='+garments_nature;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=880px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			if(title=="Job/Order Selection Form")
			{
				var action="populate_data_from_job_table";
			}
			var theform=this.contentDoc.forms[0]
			var theemail=this.contentDoc.getElementById("selected_job")
			if (theemail.value!="")
			{
				$("#txt_po_id").val('');
				$("#txt_po_no").val('');
				freeze_window(5);
				get_php_form_data( theemail.value, action, "requires/job_copy_full_controller" );

				fnc_variable_settings_check( $("#cbo_company_id").val() );
				release_freezing();
			}
		}
	}

	function fnc_copy_job()
	{
		//var txt_job_no=document.getElementById('txt_job_no').value;
		if (form_validation('txt_job_no*cbo_company_id*txt_new_style_ref*tot_smv_qnty','Job No*Company Name*New Style Ref.*SMV')==false)
		{
			return;
		}
		else
		{
			var txt_job_no=document.getElementById('txt_job_no').value;
			var precost_version = return_ajax_request_value(txt_job_no, 'check_precost_version', 'requires/job_copy_full_controller');

			if(precost_version=="111" || precost_version=="0")
			{
				alert("This Job is Budget V1.Sorry, Budget V1 copy Not allowed.");
				return;
			}
			if($('#cbo_new_company_id').val()!=0)
			{
				if($('#cbo_new_buyer_id').val()==0)
				{
					alert('Please Select Buyer Name.');
					return;
				}

				if($('#is_season_must').val()==1)
				{
					if($('#cbo_season_id').val()==0)
					{
						alert('Please Select Season Name.');
						return;
					}
					if($('#cbo_season_id').val()==0)
					{
						alert('Please Select Item.');
						return;
					}
				}

				if($('#tot_smv_qnty').val()==0 || $('#tot_smv_qnty').val()=='')
				{
					alert('Please Write SMV.');
					return;
				}
			}
			var show_copy_int_refyarn_rate='';
			var r=confirm("Press  \"Ok\"  to Copy Without Int. Ref/ Grouping \nPress  \"Cancel\"  Copy With Int. Ref/ Grouping");
			if (r==true) copy_int_ref="1"; else copy_int_ref="0";
			var data="action=save_update_delete_copy_job&copy_int_ref="+copy_int_ref+get_submitted_data_string('txt_job_no*cbo_company_id*cbo_new_company_id*cbo_new_buyer_id*cbo_season_id*txt_new_style_ref*item_id*cbo_gmtsItem_id*tot_smv_qnty*set_breck_down*cbo_order_uom*txt_po_id*txt_costing_date*hiddn_wsdata',"../../");
			freeze_window(0);
			http.open("POST","requires/job_copy_full_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_copy_job_reponse;
		}
	}

	function fnc_copy_job_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if(parseInt(trim(reponse[0]))==10)
			{
				show_msg(trim(reponse[0]));
				release_freezing();
			}
			if (reponse[0].length>2) reponse[0]=10;
			show_msg(trim(reponse[0]));
			//alert(reponse[1])
			document.getElementById('txt_new_job_no').value  = reponse[1];
			$('#tot_smv_qnty').val('');
			document.getElementById('txt_costing_date').disabled=true;
			//set_button_status(1, permission, 'fnc_quotation_entry_dtls',2);
			release_freezing();
		}
	}
	//New Job info part...................................................................

	function open_set_popup(unit_id,texboxid)
	{
		var	pcs_or_set="";
		var txt_job_no=document.getElementById('txt_job_no').value;
		var set_breck_down=document.getElementById('set_breck_down').value;
		var tot_set_qnty=document.getElementById('tot_set_qnty').value;
		var tot_smv_qnty=document.getElementById('tot_smv_qnty').value;

		var precost = return_ajax_request_value(txt_job_no, 'check_precost', 'requires/job_copy_full_controller');
		var data=precost.split("_");
		if(data[0]>0 && texboxid=='cbo_order_uom')
		{
			alert("Pre Cost Found, UOM Change not allowed");
			document.getElementById('cbo_order_uom').value=data[1];
			return;
		}
		else if (data[0]>0 && texboxid=='set_button')
		{
			alert("Pre Cost Found, Any Change will be not allowed");
		}

		if(unit_id==58)
		{
			pcs_or_set="Item Details For Set";
		}
		else if(unit_id==57)
		{
			pcs_or_set="Item Details For Pack";
		}
		else
		{
			pcs_or_set="Item Details For Pcs";
		}

		var page_link="requires/job_copy_full_controller.php?txt_job_no="+trim(txt_job_no)+"&action=open_set_list_view&set_breck_down="+set_breck_down+"&tot_set_qnty="+tot_set_qnty+'&unit_id='+unit_id+'&tot_smv_qnty='+tot_smv_qnty+'&precostfound='+data[0];
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, pcs_or_set, 'width=620px,height=300px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var set_breck_down=this.contentDoc.getElementById("set_breck_down");
			var item_id=this.contentDoc.getElementById("item_id");
			var tot_set_qnty=this.contentDoc.getElementById("tot_set_qnty");
			var tot_smv_qnty=this.contentDoc.getElementById("tot_smv_qnty");

			document.getElementById('set_breck_down').value=set_breck_down.value;
			document.getElementById('item_id').value=item_id.value;
			document.getElementById('tot_set_qnty').value=tot_set_qnty.value;
			document.getElementById('tot_smv_qnty').value=tot_smv_qnty.value;
			load_drop_down( 'requires/job_copy_full_controller', item_id.value, 'load_drop_gmts_item', 'itm_td');
		}
	}

	function openmypage_order()
	{
		if( form_validation('txt_job_no','Job No')==false )
		{
			return;
		}
		var job_no = $("#txt_job_no").val();
		$("#txt_po_id").val('');
		$("#txt_po_no").val('');
		var page_link='requires/job_copy_full_controller.php?action=order_popup&job_no='+job_no;
		var title="Po Search Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var order_no=this.contentDoc.getElementById("txt_selected").value; // product Description
			//alert(style_des_no);
			$("#txt_po_id").val(order_id);
			$("#txt_po_no").val(order_no);
		}
	}

	function fnc_variable_settings_check(company_id)
	{
		var all_variable_settings=return_ajax_request_value(company_id, 'load_variable_settings', 'requires/job_copy_full_controller');
		var ex_variable=all_variable_settings.split("_");
		var season_mandatory=ex_variable[0];
		var set_smv_id=ex_variable[1];
		if(set_smv_id==3 || set_smv_id==4 || set_smv_id==6 || set_smv_id==8 || set_smv_id==9)
		{
			$("#set_button").show();
			//var page_link="requires/job_copy_full_controller.php?action=open_smv_list&txt_style_ref="+txt_style_ref+"&set_smv_id="+set_smv_id+"&item_id="+item_id+"&id="+id+"&cbo_company_name="+cbo_company_name+"&cbo_buyer_name="+cbo_buyer_name;
			$('#set_button').removeAttr("onClick").attr("onClick","check_smv_set_popup();");
			$('#tot_smv_qnty').val('');
			$("#tot_smv_qnty").attr("disabled",true);
			$('#hiddn_wsdata').val('');
		}
		else
		{
			$("#set_button").hide();
			$('#set_button').removeAttr('onClick','onClick');
		}
		$('#is_season_must').val(trim(season_mandatory));
		$('#set_smv_id').val(trim(set_smv_id));
	}

	function check_smv_set_popup()
	{
		if($('#txt_job_no').val()=="")
		{
			alert('Please Select Job.');
			$('#txt_job_no').focus();
			return;
		}
		if (form_validation('txt_new_style_ref','New Style Ref.')==false)
		{
			return;
		}
		var txt_style_ref=''; var cbo_company_name=''; var cbo_buyer_name='';

		if($('#txt_new_style_ref').val()=="") txt_style_ref=$('#txt_style_ref').val(); else txt_style_ref=$('#txt_new_style_ref').val();
		if($('#cbo_new_company_id').val()==0) cbo_company_name=$('#cbo_company_id').val(); else cbo_company_name=$('#cbo_new_company_id').val();
		if($('#cbo_new_buyer_id').val()==0) cbo_buyer_name=$('#cbo_buyer_id').val(); else cbo_buyer_name=$('#cbo_new_buyer_id').val();

		var set_smv_id=$('#set_smv_id').val();
		var item_id=$('#cbo_gmtsItem_id').val();
		var job_no=$('#txt_job_no').val();
			//alert(cbo_company_name);
		if(set_smv_id==3 || set_smv_id==8 || set_smv_id==4 || set_smv_id==6 || set_smv_id==9)
		{
			//$('#tot_smv_qnty').val('');

			$('#hiddn_wsdata').val('');
			var page_link="requires/job_copy_full_controller.php?action=open_smv_list&txt_style_ref="+txt_style_ref+"&set_smv_id="+set_smv_id+"&item_id="+item_id+"&cbo_company_name="+cbo_company_name+"&cbo_buyer_name="+cbo_buyer_name+"&job_no="+job_no;
		}
		else
		{
			return;
		}

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'SMV Pop Up', 'width=750px,height=220px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var selected_smv_data=this.contentDoc.getElementById("selected_smv").value;
			$('#hiddn_wsdata').val(selected_smv_data);
			var smv_data=selected_smv_data.split("*");
			var smv=0;
			for(var j=0; j<smv_data.length; j++)
			{
				var exsmv=smv_data[j].split("_");
				smv=smv+(exsmv[1]*1);
			}
			console.log(smv+'--'+smv_data.length);
			var smv_avg_val = number_format(smv/smv_data.length,2);
			$('#tot_smv_qnty').val(smv_avg_val);
			$("#tot_smv_qnty").attr("disabled",true);
			$("#cbo_gmtsItem_id").attr("disabled",true);
			$("#txt_new_style_ref").attr("disabled",true);
		}
	}

</script>
</head>
    <body onLoad="set_hotkey()" >
        <div style="width:100%;" align="center">
			<? echo load_freeze_divs ("../../",$permission);  ?>
            <fieldset style="width:950px;">
            <legend>Job Copy</legend>
                <form name="jobcopy_1" id="jobcopy_1" autocomplete="off">
                    <table width="100%" cellspacing="2" cellpadding=""  border="0">
                        <tr>
                            <td width="100" class="must_entry_caption" align="right"><b>Job No</b></td>
                            <td width="130"><input style="width:120px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/job_copy_full_controller.php?action=job_popup','Job/Order Selection Form')" class="text_boxes" placeholder="Browse Job" name="txt_job_no" id="txt_job_no" readonly /></td>
                            <td width="100" align="right"><b>Company</b></td>
                            <td width="130"><? echo create_drop_down("cbo_company_id", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/job_copy_full_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );"); ?></td>
                            <td width="100" align="right"><b>Buyer</b></td>
                            <td id="buyer_td" width="130"><? echo create_drop_down( "cbo_buyer_id", 130, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" ); ?></td>
                            <td align="right"><b>Style Ref.</b></td>
                            <td>
                                <input class="text_boxes" type="text" style="width:120px;" name="txt_style_ref" id="txt_style_ref" maxlength="75" title="Maximum 75 Character" placeholder="Display" readonly/>
                                <input type="hidden" id="update_id" value="" />
                            </td>
                        </tr>
                        <tr>
                         	<td align="right" class="must_entry_caption"><b> Costing Date: </b></td>
                    		<td><input class="datepicker" type="text" style="width:70px;" name="txt_costing_date" id="txt_costing_date" value="<? echo date('d-m-Y'); ?>"/></td>
                            <td align="right" colspan="2"><b>Po Browse</b></td>
                            <td align="left" valign="top" colspan="4"><input class="text_boxes" type="text" style="width:400px;" name="txt_po_no" id="txt_po_no" placeholder="Browse" onDblClick="openmypage_order();" readonly/>
                                <input type="hidden" id="txt_po_id" value="" /></td>
                        </tr>
                        <tr>
                            <td width="100%" align="left" valign="top" colspan="8">&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="100%" align="center" valign="top" colspan="8">
                               <input type="hidden" id="item_id" />
                               <input type="hidden" id="set_breck_down" />
                               <input type="hidden" id="set_smv_id" />
                               <input type="hidden" id="tot_set_qnty" />
                               <input type="hidden" id="hiddn_wsdata" />
                               <fieldset style="width:950px;">
                                    <table cellpadding="5" cellspacing="5">
                                        <tr>
                                            <td colspan="6" align="center">
                                                <b><i>New Job No: </i></b><input  style="width:120px;" type="text"  class="text_boxes"  name="txt_new_job_no" id="txt_new_job_no" placeholder="Display" readonly />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="right"><b>New Company</b></td>
                                            <td><? echo create_drop_down("cbo_new_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/job_copy_full_controller',this.value, 'load_drop_down_buyer_new', 'new_buyer_td_new' ); fnc_variable_settings_check(this.value);"); ?></td>
                                            <td align="right"><b>New Buyer</b></td>
                                            <td id="new_buyer_td_new" width="130"><? echo create_drop_down( "cbo_new_buyer_id", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" ); ?></td>
                                            <td align="right"><b>New Season</b><input type="hidden" name="is_season_must" id="is_season_must" style="width:50px;" class="text_boxes" /></td>
                                            <td id="season_td"><? echo create_drop_down( "cbo_season_id", 150, $blank_array,'', 1, "-- Select Season--",$selected, "" ); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="must_entry_caption" align="right"><b>New Style Ref.</b></td>
                                            <td><input class="text_boxes" type="text" style="width:140px;" name="txt_new_style_ref" id="txt_new_style_ref" maxlength="75" title="Maximum 75 Character" /></td>
                                            <td class="must_entry_caption" align="right">Order Uom</td>
                                            <td><? echo create_drop_down( "cbo_order_uom",50, $unit_of_measurement, "",0, "", 1, "","","1,57,58" );
                                                echo create_drop_down( "cbo_gmtsItem_id", 100, get_garments_item_array(2), 0, 1, "--Select Item--", $data,"",'',$data); ?>
                                            </td>
                                            <td class="must_entry_caption" align="right">SMV</td>
                                            <td>
                                            	<input class="text_boxes_numeric" type="text" style="width:60px;" name="tot_smv_qnty" id="tot_smv_qnty" />
                                            	<input type="button" id="set_button" class="image_uploader" style="width:60px; display:none" value="WS SMV" onClick="open_set_popup(document.getElementById('cbo_order_uom').value,this.id)" />
                                            </td>
                                        </tr>
                                    </table>
                                 </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" valign="middle" class="button_container" colspan="8">
                                <input type="button" id="copyjob" name="copyjob" value="Copy" class="formbutton" style="width:100px" onClick="fnc_copy_job();">
                            </td>
                        </tr>
                    </table>
             </form>
            </fieldset>
        </div>
    </body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
