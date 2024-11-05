<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Garments Ex-Factory Return

Functionality	:
JS Functions	:
Created by		:	Aziz
Creation date 	: 	12-01-2016
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
echo load_html_head_contents("Garments Ex-Factory Return","../", 1, 1, $unicode,'','');

?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function fnc_exFactory_entry(operation)
	{
		if(operation==4)
		{
			 var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_name').val()+'*'+$('#txt_return_id').val()+'*'+$('#txt_ex_factory_date').val()+'*'+report_title, "garments_exfactory_print", "requires/garments_exfactory_return_controller" )

			 return;
		}
		 if(operation==0 || operation==1 || operation==2)
		{
			if(operation==2)
			{
			alert("Not Allow now");return;
			}

			if ( form_validation('cbo_company_name*txt_ex_factory_date*txt_return_date','Company Name*Ex-Factory Date*Return Ex-factory Date')==false )
			{
				return;
			}
			else
			{
				var row_num=$('#tbl_item_details tbody tr').length;
				data2='';
				for(var i=1; i<=row_num; i++)
				{
					//return_prod_qty_row(i);
					/*if (form_validation('txtreturnqty_'+i,'Return Qty')==false)
						{
							return;
						}*/
						var return_qty = $("#txtreturnqty_"+i).val()*1;
						//var delivery_qty = $("#txtexfactoryqty_"+i).val()*1;
						var delivery_qty =$("#txtreturnqty_"+i).attr('placeholder');
						if(return_qty>delivery_qty)
						{
							alert('Return qty. over is not allow than delivery qty.');
							$("#txtreturnqty_"+i).val('');
							$("#txtreturnqty_"+i).focus();
							return;
						}

					data2+=get_submitted_data_string('updatedtlsid_'+i+'*cbobuyer_'+i+'*txtjob_'+i+'*txtorder_'+i+'*cbo_item_name_'+i+'*cbo_country_name_'+i+'*txtcolor_'+i+'*txtsize_'+i+'*txtexfactoryqty_'+i+'*txtreturnqty_'+i+'*colormstid_'+i+'*dtlsmstid_'+i+'*txtdtlsid_'+i,"../",i);
				}

				//alert(data2);
				var data="action=save_update_delete&operation="+operation+"&row_num="+row_num+get_submitted_data_string('cbo_company_name*cbo_location_name*txt_ex_factory_date*txt_return_date*txt_challan_no*txt_return_no*txt_return_id*cbo_transport_company*txt_dl_no*txt_lock_no*txt_truck_no*txt_driver_name*txt_destination*cbo_forwarder*txt_mobile_no*txt_do_no*txt_gp_no*txt_prev_mst_id*sewing_production_variable',"../");
				var data=data+data2;

	 			freeze_window(operation);
	 			http.open("POST","requires/garments_exfactory_return_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_exFactory_entry_Reply_info;
			}
		}
	}

	function fnc_exFactory_entry_Reply_info()
	{
	 	if(http.readyState == 4)
		{
			//alert(http.responseText);return;
			var reponse=http.responseText.split('**');
			var sewing_production_variable=$("#sewing_production_variable").val();
			if(reponse[0]==0)
			{

				show_msg(trim(reponse[0]));
				$("#txt_return_id").val(trim(reponse[1]));
				$("#txt_return_no").val(trim(reponse[2]));
			//	$("#txt_challan_no").val(trim(reponse[4]));
				$("#Print1").removeClass("formbutton_disabled").removeAttr("disabled");
				show_list_view(reponse[1]+'_'+reponse[5]+'_'+sewing_production_variable,'show_dtls_listview_mst2','exfactory_details_container','requires/garments_exfactory_return_controller','');
				var return_id_arr=reponse[4].split(',');
				var k=0;
				for(var j=1;j<=return_id_arr.length;j++)
				{
					$("#updatedtlsid_"+j).val(return_id_arr[k]);
					k++;
				}
				release_freezing();
				set_button_status(1, permission, 'fnc_exFactory_entry',1,1);

			}
			else if(reponse[0]==1)
			{
				//var po_id = reponse[1];
				show_msg(trim(reponse[0]));

				show_list_view(reponse[1]+'_'+reponse[5]+'_'+sewing_production_variable,'show_dtls_listview_mst2','exfactory_details_container','requires/garments_exfactory_return_controller','');
				var return_id_arr=reponse[4].split(',');
				var k=0;
				for(var j=1;j<=return_id_arr.length;j++)
				{
					$("#updatedtlsid_"+j).val(return_id_arr[k]);
					k++;
				}

				release_freezing();
				set_button_status(1, permission, 'fnc_exFactory_entry',1,1);

			}
			else if(reponse[0]==2)
			{
				//var po_id = reponse[1];

				alert('Not Allow, Will Be used in Future');return;
				show_msg(trim(reponse[0]));
				show_list_view(reponse[3]+'_'+sewing_production_variable,'show_dtls_listview_mst2','exfactory_details_container','requires/garments_exfactory_return_controller','');
				//setFilterGrid("details_table",-1);
				release_freezing();
				set_button_status(0, permission, 'fnc_exFactory_entry',1,1);

			}

	 	}
	}

	function fn_qnty_per_ctn()
	{
		 var exQnty = $('#txt_ex_quantity').val();
		 var ctnQnty = $('#txt_total_carton_qnty').val();

		 if(exQnty!="" && ctnQnty!="")
		 {
			 var ctn_per_qnty = parseInt( Number( exQnty/ctnQnty ) );
			 $('#txt_ctn_qnty').val(ctn_per_qnty);
		 }
	}

	function return_system_popup() //Return PopUp
	{
		var sewing_production_variable=$("#sewing_production_variable").val();
		var page_link='requires/garments_exfactory_return_controller.php?action=delivery_system_popup&company='+document.getElementById('cbo_company_name').value;
		var title="System Popup";
		var company = $("#cbo_company_name").val();
		var txt_challan_no=$("#txt_challan_no").val();

		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1040px,height=370px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var return_id=this.contentDoc.getElementById("hidden_return_id").value;
			//alert(delivery_id);return;
			if(return_id !="")
			{
				get_php_form_data(return_id, "populate_master_from_date", "requires/garments_exfactory_return_controller" );
				var txt_challan_no=$("#txt_challan_no").val();
				show_list_view(return_id+'_'+txt_challan_no+'_'+sewing_production_variable,'show_dtls_listview_mst2','exfactory_details_container','requires/garments_exfactory_return_controller','');

				setFilterGrid("details_table",-1);
				set_button_status(1, permission, 'fnc_exFactory_entry',1,1);
			}
		}
	}

	function delivery_challan_popup()
	{
		var page_link='requires/garments_exfactory_return_controller.php?action=delivery_challan_sys_popup&company='+document.getElementById('cbo_company_name').value;
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
				get_php_form_data(delivery_id, "populate_challan_master_from_date", "requires/garments_exfactory_return_controller" );
				var txt_challan_no=$("#txt_challan_no").val();
				var sewing_production_variable=$("#sewing_production_variable").val();
				show_list_view(delivery_id+'_'+txt_challan_no+'_'+sewing_production_variable,'show_dtls_listview_mst','exfactory_details_container','requires/garments_exfactory_return_controller','');
				setFilterGrid("details_table",-1);
				set_button_status(0, permission, 'fnc_exFactory_entry',1,1);
				$("#Print1").addClass("formbutton_disabled").attr("disabled","disabled");
			}
		}

	}
	function return_prod_qty_row(id)
	{
		//alert(id);
		var id=id.split('_');
		var return_qty = $("#txtreturnqty_"+id[1]).val()*1;
		//var delivery_qty = $("#txtexfactoryqty_"+id[1]).val()*1;
		var delivery_qty = $("#txtreturnqty_"+id[1]).attr('placeholder');
		if(return_qty>delivery_qty)
		{
		alert('Return qty. over is not allow than delivery qty.');
		$("#txtreturnqty_"+id[1]).val('');
		$("#txtreturnqty_"+id[1]).focus();
		return;
		}
	}

	function garments_prod_scan(str,type)
	{
		//var basis=$('#cbo_basis').val();
		//var str=$('#txt_chalan_no').val();
			//get_php_form_data(str, "populate_challan_master_from_date", "requires/garments_exfactory_return_controller" );
			//var txt_challan_no=$("#txt_challan_no").val();
			//var delivery_id=$("#txt_prev_mst_id").val();
			//show_list_view(delivery_id+'_'+txt_challan_no,'show_dtls_listview_mst','exfactory_details_container','requires/garments_exfactory_return_controller','');
	}

	$('#txt_chalan_no').live('keydown', function(e) {
		if (e.keyCode === 13) {
		e.preventDefault();
		garments_prod_scan(this.value,1);
		}
	});

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
                        <td align="right" colspan="3">Return No</td>
                        <td colspan="3">
                          <input name="txt_return_no" id="txt_return_no" class="text_boxes" type="text"  style="width:160px" onDblClick="return_system_popup()" placeholder="Browse or Search" />
                          <input name="txt_return_id" id="txt_return_id" class="text_boxes" type="hidden"  style="width:60px"/>
                        </td>
                    </tr>
                    <tr>
                        <td width="130" align="right" class="must_entry_caption">Company Name </td>
                        <td width="170">
                            <?
                            echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", '', "load_drop_down( 'requires/garments_exfactory_return_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data(this.value,'load_variable_settings','requires/garments_exfactory_return_controller');get_php_form_data(this.value,'production_process_control','requires/garments_exfactory_return_controller');",0 ); ?>

                            <input type="hidden" name="sewing_production_variable" id="sewing_production_variable" value="" />
                        	<input type="hidden" name="hidden_variable_cntl" id="hidden_variable_cntl" value="0">
                        	<input type="hidden" name="hidden_preceding_process" id="hidden_preceding_process" value="0">
                        	<input type="hidden" id="styleOrOrderWisw" />
                            <input type="hidden" id="variable_is_controll" />
                        	<input type="hidden" id="txt_qty_source" />

                        </td>
                        <td width="130" align="right">Location</td>
                        <td width="170" id="location_td">
                           <? echo create_drop_down( "cbo_location_name", 172, $blank_array,"", 1, "-- Select Location --", $selected, "" );?>
                        </td>
                        <td align="right"> Delivery Challan No</td>
                        <td >
                          <input name="txt_challan_no" id="txt_challan_no" class="text_boxes" type="text"  style="width:160px"  onDblClick="delivery_challan_popup('requires/garments_exfactory_return_controller.php?action=delivery_challan_sys_popup&company='+document.getElementById('cbo_company_name').value,'System Challan Search')" placeholder="Browse/Scan" readonly />
                        </td>


                    </tr>
                    <tr>
                    	<td align="right" class="must_entry_caption">Transport. Company </td>
                        <td id="transfer_com">
                        <?
                        echo create_drop_down( "cbo_transport_company", 172, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and a.is_deleted=0 and b.party_type=35   order by a.supplier_name","id,supplier_name", 1, "-- Select Company --", $selected,"","0" );

						//echo create_drop_down( "cbo_transport_company", 172, $blank_array,"", 1, "-- Select Transport --", $selected, "",1 );
                        ?>
                        </td>
                        <td width="110" align="right" class="must_entry_caption">Ex- Factory Date </td>
                        <td width="190">
                        <input name="txt_ex_factory_date" id="txt_ex_factory_date" class="datepicker"  style="width:160px;" placeholder="Display" readonly />
                        </td>
                        <td  align="right">Truck No</td>
                        <td id="section_td"><input type="text" name="txt_truck_no" id="txt_truck_no" class="text_boxes" style="width:160px;" maxlength="50" placeholder="Write"  /></td>

                     </tr>

                    <tr>
                    	<td align="right" >Lock No</td>
                        <td>
                        <input type="text" name="txt_lock_no" id="txt_lock_no" class="text_boxes" style="width:160px;" maxlength="50" placeholder="Write"  />
                        </td>
                        <td align="right" >Driver Name</td>
                        <td ><input type="text" name="txt_driver_name" id="txt_driver_name" class="text_boxes" style="width:160px;" maxlength="50" placeholder="Write"  /></td>
                        <td align="right">DL/No</td>
                        <td >
                        <input type="text" name="txt_dl_no" id="txt_dl_no" class="text_boxes" style="width:160px;" maxlength="50" placeholder="Write"  />
                        </td>

                    </tr>
                    <tr>
                    	<td align="right" >Mobile Num</td>
                        <td>
                        <input type="text" name="txt_mobile_no" id="txt_mobile_no" class="text_boxes" style="width:160px;" maxlength="50" placeholder="Write"  />
                        </td>
                        <td align="right" >DO No</td>
                        <td ><input type="text" name="txt_do_no" id="txt_do_no" class="text_boxes" style="width:160px;" maxlength="50" placeholder="Write"  /></td>
                        <td align="right">GP No</td>
                        <td >
                        <input type="text" name="txt_gp_no" id="txt_gp_no" class="text_boxes" style="width:160px;" maxlength="50" placeholder="Write"  />
                        </td>

                    </tr>
                    <tr>
                    	<td align="right">Final Destination</td>
                        <td>
                        	<input type="text" name="txt_destination" id="txt_destination" class="text_boxes" style="width:160px;" maxlength="50" placeholder="Write"  />
                        </td>
                        <td align="right" >Forwarder</td>
                        <td id="forwarder_td">
                        <?
                        	//echo create_drop_down( "cbo_forwarder", 172, $blank_array,"", 1, "-- Select--", $selected,"","1" );
                       echo create_drop_down( "cbo_forwarder", 172, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company b where a.id=b.supplier_id and a.status_active=1 and a.is_deleted=0  and a.id in (select  supplier_id from  lib_supplier_party_type where party_type in(30,31,32)) group by a.id, a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select--", $selected,"","0" );
					    ?>
                        </td>
                        <td width="110" align="right" class="must_entry_caption">Return Date </td>
                        <td width="190">
                        <input name="txt_return_date" id="txt_return_date" class="datepicker"  style="width:160px;" >
                         <input name="txt_prev_mst_id" id="txt_prev_mst_id" class="text_boxes" type="hidden"  style="width:60px"/>
                        </td>
                    </tr>
                </table>
                </fieldset>
                <br />
                <fieldset style="width:930px;">
                 <legend>Disply </legend>
                <table cellpadding="0" cellspacing="1" width="930" class="rpt_table" border="1" rules="all" id="tbl_item_details">
                    <thead>
                    <th width="40">SL</th>
                    <th width="120">Buyer</th>
                    <th width="120">Job No</th>
                    <th width="100">Order No</th>
                    <th width="100">Item </th>
                    <th width="120">Country</th>
                    <th width="90">Color</th>
                    <th width="80">Size </th>
                    <th width="80">Ex-Factory Qty.</th>
                    <th width="80">Return Qty.</th>

                </thead>
                 <tbody id="exfactory_details_container">
                        <tr class="general" id="tr_1">
                        <td id="slTd_1">1</td>
                         <td>

                          <?
                                echo create_drop_down( "cbobuyer_1", 110, "select id,buyer_name from lib_buyer","id,buyer_name", 1, '--- Select ---', 0, "",1,'');
                            ?>
                         </td>
                         <td>
                         <input type="text" name="txtjob[]" id="txtjob_1"  class="text_boxes"  style="width:110px" placeholder="Display" readonly    />

                         </td>
                         <td>
                         <input type="text" name="txtorder[]" id="txtorder_1" class="text_boxes"   style="width:90px" readonly  placeholder="Display"  />
                         </td>
                          <td>

                          <?
                                echo create_drop_down( "cbo_item_name_1", 90, $garments_item,"", 1, "-- Select Item --", $selected, "",1,0 );
                            ?>
                         </td>
                         <td>
							<?
                            echo create_drop_down( "cbo_country_name_1", 110, "select id,country_name from lib_country","id,country_name", 1, "-- Select Country --", $selected, "",1 );
                            ?>
                        </td>
                        <td>
                         <input type="text" name="txtcolor[]" id="txtcolor_1" class="text_boxes"   style="width:85px" readonly placeholder="Display" />
                         </td>
                         <td>
                         <input type="text" name="txtsize[]" id="txtsize_1" class="text_boxes"   style="width:75px" readonly  placeholder="Display" />
                         </td>
                          <td>
                         <input type="text" name="txtexfactoryqty[]" id="txtexfactoryqty_1"   style="width:80px" class="text_boxes_numeric"  placeholder="Display" readonly/>


                         </td>
                          <td width="">
                         <input type="text" name="txtreturnqty[]" id="txtreturnqty_1" class="text_boxes_numeric" onBlur="return_prod_qty_row(this.id);"   style="width:80px"  />
                         <input type="hidden" name="updatedtlsid[]" id="updatedtlsid_1" class="text_boxes" style="width:30px" />
                         <input type="hidden" name="colormstid[]" id="colormstid_1"  value="" class="text_boxes" style="width:80px" readonly />
                         <input type="hidden" name="dtlsmstid[]" id="dtlsmstid_1"  value="" class="text_boxes" style="width:50px" readonly />
                         <input type="hidden" name="txtdtlsid[]" id="txtdtlsid_1" class="text_boxes"  style="width:50px" readonly  >
                         </td>
                        </tr>
                        </tbody>
                </table>
                <br />
                <table cellpadding="0" cellspacing="1" width="930">
                    <tr>
                        <td align="center" colspan="10" valign="middle" class="button_container">
                             <?
                                echo load_submit_buttons( $permission, "fnc_exFactory_entry", 0,1,"reset_form('exFactory_1','','','','')",1);
                            ?>
                             <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly >

                        </td>
                    </tr>
                </table>

           </fieldset>
        </form>
    </div>

</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
