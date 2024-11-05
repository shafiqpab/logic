<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Left Over Garments Receive

Functionality	:	This form is finish input entry
JS Functions	:
Created by		:	Shafuqr Rahman, Reaz Uddin
Creation date 	: 	19-04-2018
Updated by 		: 	Shafiq
Update date		: 	21-12-2019
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$u_id=$_SESSION['logic_erp']['user_id'];

$level=return_field_value("user_level","user_passwd","id='$u_id' and valid=1 ","user_level");

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Iron Output Info","../", 1, 1, $unicode,'','');
?>

<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	function fnc_left_over_gmts_input(operation)
	{
		/*if(operation==2) {
			alert("Delete Restricted");
			return;
		}*/
		/*else if(operation==4)
		{
			 var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_name').val()+'*'+$('#txt_mst_id').val()+'*'+report_title+'*'+$('#txt_system_no').val(), "left_over_gmts_receive_print", "requires/left_over_garments_receive_controller" );
			 return;
		}*/
		if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][587]); ?>')
		{
			if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][587]); ?>','<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][587]); ?>')==false) {return;}
		}

		if(operation==0 || operation==1 || operation==2)
		{
	 		if ( form_validation('cbo_company_name*cbo_order_type*cbo_store_name*cbo_goods_type*txt_order_no*txt_total_left_over_receive','Company Name*Order Type*Goods Type*Order No* Receive Qty')==false )
			{
				return;
			}
			else
			{
				freeze_window(operation);
				var sewing_production_variable = $("#sewing_production_variable").val();
				var colorList = ($('#hidden_colorSizeID').val()).split(",");

				var i=0;
				var k=0;
				var colorIDvalue='';

				if(sewing_production_variable==2)//color level
				{
	 				$("input[name=txt_color]").each(function(index, element) {
	 					if( $(this).val()!='' )
						{
							if(i==0)
							{
								colorIDvalue = colorList[i]+"*"+$(this).val();
							}
							else
							{
								colorIDvalue += "**"+colorList[i]+"*"+$(this).val();
							}
						}
						i++;
					});
				}
				else if(sewing_production_variable==3)//color and size level
				{
					$("input[name=colorSize]").each(function(index, element) {
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
				}

				/*if(colorIDvalue=="")
				{

					alert("Please Provide Received Quantity");
					release_freezing();
					return;
				}*/
				//alert(colorIDvalue);

				var txt_po_id = document.getElementById('txt_po_id').value;
				if (txt_po_id == '') {
					alert('Please select from list first');
					release_freezing();
					return;
				}


				var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+get_submitted_data_string('cbo_company_name*cbo_location_name*txt_leftover_date*cbo_order_type*cbo_buyer_name*cbo_store_name*exchange_rate*cbo_working_company_name*cbo_working_location_name*cbo_working_floor_name*txt_remark*cbo_goods_type*txt_order_no*txt_po_id*txt_style_name*cbo_item_name*cbo_country_name*txt_total_left_over_receive*txt_remark2*cbo_currency*txt_fob_rate*txt_leftover_amount*txt_bdt_amount*cbo_room_no*cbo_rack_no*cbo_shelf_no*cbo_bin_no*sewing_production_variable*country_maintain_variable*leftover_source*hidden_po_break_down_id*hidden_colorSizeID*styleOrOrderWisw*iron_production_variable_rej*variable_is_controll*txt_user_lebel*hidden_currency_id*hidden_exchange_rate*hidden_piece_rate*hidden_currency_rate*txt_mst_id*txt_system_no*hidden_dtls_id*hidden_job_no*cbo_category_id',"../");
	 			//alert(data);return;
	 			http.open("POST","requires/left_over_garments_receive_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_left_over_gmts_input_Reply_info;
			}
		}
	}

	function fnc_left_over_gmts_input_Reply_info()
	{
	 	if(http.readyState == 4)
		{

			var company_name 			= $('#cbo_company_name').val();
			var po_id 					= $('#hidden_po_break_down_id').val();
			var location_name 			= $('#cbo_location_name').val();
			var order_type 				= $('#cbo_order_type').val();
			var buyer_name 				= $('#cbo_buyer_name').val();
			var goods_type 				= $('#cbo_goods_type').val();
			var working_company_name 	= $('#cbo_working_company_name').val();
			var working_location_name 	= $('#cbo_working_location_name').val();
			var working_floor_name	    = $('#cbo_working_floor_name').val();
			var country_id 				= $('#cbo_country_name').val();
			var gmts_item 				= $('#cbo_item_name').val();
			var country_maintain		= $('#country_maintain_variable').val();
			var leftover_source		= $('#leftover_source').val();
			var response=http.responseText.split('**');
			if(response[0]==0 || response[0]==1)
			{
				show_msg(trim(response[0]));
				$("#txt_mst_id").val(response[1]);
				$("#txt_system_no").val(response[2]);
				reset_form('','','hidden_dtls_id*txt_order_no*txt_po_id*txt_style_name*cbo_item_name*cbo_country_name*txt_total_left_over_receive*txt_remark2*cbo_currency*txt_fob_rate*txt_leftover_amount*txt_bdt_amount*cbo_room_no*cbo_rack_no*cbo_shelf_no*cbo_bin_no','','');

				$('#breakdown_td_id').html('');

				$("#cbo_company_name").attr("disabled","disabled");
				$("#cbo_location_name").attr("disabled","disabled");
				$("#txt_leftover_date").attr("disabled","disabled");
				$("#cbo_order_type").attr("disabled","disabled");
				$("#cbo_buyer_name").attr("disabled","disabled");
				$("#cbo_store_name").attr("disabled","disabled");
				$("#cbo_goods_type").attr("disabled","disabled");
				$("#cbo_working_location_name").attr("disabled","disabled");
				$("#cbo_working_floor_name").attr("disabled","disabled");
				$("#txt_remark").attr("disabled","disabled");

				show_list_view(response[1]+'**'+country_maintain+'**'+leftover_source,'show_dtls_listview_system','list_view_container','requires/left_over_garments_receive_controller','setFilterGrid(\'tbl_list_search\',-1)');
				show_list_view(po_id+'**'+company_name+'**'+location_name+'**'+order_type+'**'+buyer_name+'**'+goods_type+'**'+working_company_name+'**'+working_location_name+'**'+working_floor_name+'**'+country_maintain+'**'+leftover_source,'show_country_listview','list_view_country','requires/left_over_garments_receive_controller');
				get_left_over_balance(response[4],country_id,gmts_item,company_name,location_name,working_company_name,working_location_name,country_maintain,leftover_source);
				// show_list_view(response[4],'show_dtls_listview','list_view_container','requires/left_over_garments_receive_controller','setFilterGrid(\'tbl_list_search\',-1)');

				set_button_status(0, permission, 'fnc_left_over_gmts_input',1,0);
				release_freezing();
				return;
			}
			else if(response[0]==2)
			{
				// alert(response[1]);
				show_msg(trim(response[0]));

				reset_form('ironoutput_1','list_view_country','','','childFormReset(3)');

				show_list_view(response[1]+'**'+country_maintain+'**'+leftover_source,'show_dtls_listview_system','list_view_container','requires/left_over_garments_receive_controller','setFilterGrid(\'tbl_list_search\',-1)');
				show_list_view(response[4]+'**'+company_name+'**'+location_name+'**'+order_type+'**'+buyer_name+'**'+goods_type+'**'+working_company_name+'**'+working_location_id+'**'+working_floor_id,'show_country_listview','list_view_country','requires/left_over_garments_receive_controller');

				set_button_status(0, permission, 'fnc_left_over_gmts_input',1,0);
				release_freezing();
				return;
			}
			else if(response[0]==3)
			{
				alert('Issue Found. So, you can not delete.');
				release_freezing();
				return;
			}
			release_freezing();
	 	}
	}

	//function openmypage(page_link,title)
	function openmypage( )
	{
		var wo_company_and_location_mandatory = $("#wo_company_and_location_mandatory").val();
		if(wo_company_and_location_mandatory=='1')
		{
			if ( form_validation('cbo_company_name*cbo_location_name*cbo_order_type*cbo_goods_type*txt_leftover_date*cbo_working_company_name*cbo_working_location_name','Company Name*Location Name*Order Type*Goods Type*Receive Date*Working Company*Working Location')==false )
			{
				return;
			}
		}
		else
		{
			if ( form_validation('cbo_company_name*cbo_location_name*cbo_order_type*cbo_goods_type*txt_leftover_date','Company Name*Location Name*Order Type*Goods Type*Receive Date')==false )
			{
				return;
			}
		}

		var page_link = 'requires/left_over_garments_receive_controller.php?action=order_popup&company='+$('#cbo_company_name').val()+'&location_name='+$('#cbo_location_name').val()+'&order_type='+$('#cbo_order_type').val()+'&goods_type='+$('#cbo_goods_type').val()+'&garments_nature='+$('#garments_nature').val()+'&buyer_name='+$('#cbo_buyer_name').val()+'&store_name='+$('#cbo_store_name').val()+'&working_company_name='+$('#cbo_working_company_name').val()+'&working_location_name='+$('#cbo_working_location_name').val()+'&country_maintain_variable='+$('#country_maintain_variable').val()+'&leftover_source='+$('#leftover_source').val()+'&sewing_production_variable='+$('#sewing_production_variable').val()+'&wo_company_and_location_mandatory='+"'"+wo_company_and_location_mandatory+"'";
		var title = 'Order Search';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=940px,height=370px,center=1,resize=0,scrolling=1','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			//alert(theform);

			var country_id=this.contentDoc.getElementById("hidden_country_id").value;
			// var company_id=this.contentDoc.getElementById("hidden_company_id").value;
			var company_id=document.getElementById("cbo_company_name").value;
			var location_name=this.contentDoc.getElementById("hidden_location_name").value;
			var po_id=this.contentDoc.getElementById("hidden_id").value;
			// var po_id=document.getElementById("hidden_po_break_down_id").value;
			var po_number=this.contentDoc.getElementById("hidden_po_number").value;
			var gmts_item=this.contentDoc.getElementById("hidden_gmts_item").value;
			var currency_id=this.contentDoc.getElementById("hidden_currency_id").value;
			//var buyer_id=this.contentDoc.getElementById("hidden_byer_name").value;
			//var style_ref_no=this.contentDoc.getElementById("hidden_style_ref_no").value;
			//var fob_rate=this.contentDoc.getElementById("hidden_order_rate").value;
			get_php_form_data(company_id,'load_variable_settings','requires/left_over_garments_receive_controller');

			if (po_id!="")
			{
				var variableSettings 		= $('#sewing_production_variable').val();
				var company_name 			= $('#cbo_company_name').val();
				var location_name 			= $('#cbo_location_name').val();
				var order_type 				= $('#cbo_order_type').val();
				var buyer_name 				= $('#cbo_buyer_name').val();
				var goods_type 				= $('#cbo_goods_type').val();
				var working_company_name 	= $('#cbo_working_company_name').val();
				var working_location_name 	= $('#cbo_working_location_name').val();
				var country_maintain 		= $('#country_maintain_variable').val();
				var leftover_source			= $('#leftover_source').val();
				var wo_com_loc_mandatory	= $('#wo_company_and_location_mandatory').val();
				//load_drop_down( 'requires/left_over_garments_receive_controller', company_id, 'load_drop_down_store_name', 'store_name_td' );
				//load_drop_down( 'requires/left_over_garments_receive_controller', location_name, 'load_drop_down_floor', 'cbo_floor' );


				check_exchange_rate(currency_id);

				get_php_form_data(po_id+'**'+country_id+'**'+company_id+'**'+gmts_item+'**'+$('#cbo_goods_type').val()+'**'+variableSettings+'**'+$('#cbo_order_type').val()+'**'+location_name+'**'+$('#garments_nature').val()+'**'+country_maintain+'**'+leftover_source+'**'+working_company_name+'**'+working_location_name+'**'+wo_com_loc_mandatory, "color_and_size_level_left_over", "requires/left_over_garments_receive_controller" );

				var goodsType = document.getElementById('cbo_goods_type').value;

				/* if(goodsType != 1) {
					show_list_view(po_id+'**'+gmts_item+'**'+country_id+'**'+company_id+'**'+country_maintain+'**'+leftover_source,'show_dtls_listview','list_view_container','requires/left_over_garments_receive_controller','setFilterGrid(\'tbl_list_search\',-1)');
				} */
				show_list_view(po_id+'**'+company_name+'**'+location_name+'**'+order_type+'**'+buyer_name+'**'+goods_type+'**'+working_company_name+'**'+working_location_name+'**'+country_maintain+'**'+leftover_source,'show_country_listview','list_view_country','requires/left_over_garments_receive_controller');


				$("#cbo_company_name").attr("disabled","disabled");
				$("#cbo_location_name").attr("disabled","disabled");
				//$("#txt_leftover_date").attr("disabled","disabled");
				$("#cbo_order_type").attr("disabled","disabled");
				$("#cbo_buyer_name").attr("disabled","disabled");
				//$("#cbo_store_name").attr("disabled","disabled");
				$("#cbo_goods_type").attr("disabled","disabled");
				$("#cbo_working_company_name").attr("disabled","disabled");
				$("#cbo_working_location_name").attr("disabled","disabled");

				get_left_over_balance(po_id,country_id,gmts_item,company_name,location_name,working_company_name,working_location_name,country_maintain,leftover_source);

				set_button_status(0, permission, 'fnc_left_over_gmts_input',1,0);
				release_freezing();
			}
		}
	}

	function put_country_data(po_id, item_id, country_id, lft_qnty)
	{
		freeze_window(5);
		var countryIdArr = country_id.split('__');
		var countryId = countryIdArr.join(',');

		$("#cbo_item_name").val(item_id);
		$("#txt_left_over_balance").val(lft_qnty);
		$("#cbo_country_name").val(countryIdArr[0]);
		var variableSettingsCountry	= $('#country_maintain_variable').val();
		var leftover_source	= $('#leftover_source').val();

		get_php_form_data(po_id+'**'+item_id+'**'+country_id+'**'+variableSettingsCountry+'**'+leftover_source, "populate_data_from_search_popup", "requires/left_over_garments_receive_controller" );

		var variableSettings=$('#sewing_production_variable').val();
		var styleOrOrderWisw=$('#styleOrOrderWisw').val();
		var prod_reso_allo=$('#prod_reso_allo').val();
		var company_id=$('#cbo_company_name').val();
		var location_name=$('#cbo_location_name').val();
		var working_company_name 	= $('#cbo_working_company_name').val();
		var working_location_name 	= $('#cbo_working_location_name').val();
		var wo_com_loc_mandatory	= $('#wo_company_and_location_mandatory').val();

		if(variableSettings!=1)
		{
			get_php_form_data(po_id+'**'+countryId+'**'+company_id+'**'+item_id+'**'+$('#cbo_goods_type').val()+'**'+variableSettings+'**'+$('#cbo_order_type').val()+'**'+location_name+'**'+$('#garments_nature').val()+'**'+variableSettingsCountry+'**'+leftover_source+'**'+working_company_name+'**'+working_location_name+'**'+wo_com_loc_mandatory, "color_and_size_level_left_over", "requires/left_over_garments_receive_controller" );
		}
		else
		{
			$("#txt_input_qnty").removeAttr("readonly");

			calculateAmounts($("#txt_total_left_over_receive").val());
			checkLeftOverBalance();
		}

		var goodsType = document.getElementById('cbo_goods_type').value;

		if(goodsType != 1) {
			show_list_view(po_id+'**'+item_id+'**'+countryId+'**'+company_id+'**'+leftover_source,'show_dtls_listview','list_view_container','requires/left_over_garments_receive_controller','setFilterGrid(\'tbl_list_search\',-1)');
		}
		// reset_form('','','txt_total_left_over_receive*txt_remark*txt_left_over_balance*txt_fob_rate','','');
		set_button_status(0, permission, 'fnc_left_over_gmts_input',1,0);
		release_freezing();
	}

	function get_left_over_balance(po_id,country_id,gmts_item,company,location,wo_company,wo_location,country_maintain,leftover_source)
	{
		var order_type = $('#cbo_order_type').val();
		var goods_type = $('#cbo_goods_type').val();
		var wo_com_loc_mandatory	= $('#wo_company_and_location_mandatory').val();
		var data = 'po_id='+po_id+'&country_id='+country_id+'&gmts_item='+gmts_item+'&company='+company+'&location='+location+'&wo_company='+wo_company+'&wo_location='+wo_location+'&country_maintain='+country_maintain+'&leftover_source='+leftover_source+'&order_type='+order_type+'&goods_type='+goods_type+'&wo_com_loc_mandatory='+wo_com_loc_mandatory+'&action=get_left_over_balance';
		// var data = {"po_id":po_id,"gmts_item":gmts_item,"country_id":country_id,"action":get_left_over_balance};
		// alert(data);
		$.ajax({
			type:'POST',
			url: 'requires/left_over_garments_receive_controller.php',
			data: data,
			cache: false,
			success: function(response)
			{
				$("#txt_left_over_balance").val(response);
				calculateAmounts($("#txt_total_left_over_receive").val());
			},
			error: function(xhr, status, error) {
	           alert('There are something wrong to get left-over balance.');
	        }
		});
	}

	function system_number_popup()
	{
		/*if ( form_validation('cbo_company_name*cbo_location_name*txt_leftover_date','Company Name*cbo_location_name*Receive Date')==false )
		{
			return;
		}
		else
		{*/

			var page_link = 'requires/left_over_garments_receive_controller.php?action=system_number_popup&company='+$('#cbo_company_name').val()+'&location_name='+$('#cbo_location_name').val()+'&leftover_date='+$('#txt_leftover_date').val()+'&order_type='+$('#cbo_order_type').val()+'&goods_type='+$('#cbo_goods_type').val();

			var title = 'Left Over Garments Search';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1300px,height=370px,center=1,resize=0,scrolling=1','');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];

				var responseDataArr=this.contentDoc.getElementById("hidden_search_data").value.split('_');

				get_php_form_data(responseDataArr[0], "populate_mst_form_data", "requires/left_over_garments_receive_controller" );

				var country_maintain = $("#country_maintain_variable").val();
				var leftover_source = $("#leftover_source").val();

				show_list_view(responseDataArr[0]+'**'+country_maintain+'**'+leftover_source,'show_dtls_listview_system','list_view_container','requires/left_over_garments_receive_controller','setFilterGrid(\'tbl_list_search\',-1)');

				$("#cbo_company_name").attr("disabled","disabled");
				$("#cbo_location_name").attr("disabled","disabled");
				$("#txt_leftover_date").attr("disabled","disabled");
				$("#cbo_order_type").attr("disabled","disabled");
				$("#cbo_store_name").attr("disabled","disabled");
				$("#cbo_goods_type").attr("disabled","disabled");
				//$("#cbo_floor_name").attr("disabled","disabled");
				//$("#txt_remark").attr("disabled","disabled");

				reset_form('','','hidden_dtls_id*txt_order_no*txt_po_id*txt_style_name*cbo_item_name*cbo_country_name*txt_total_left_over_receive*txt_remark2*cbo_currency*txt_fob_rate*txt_leftover_amount*txt_bdt_amount*cbo_room_no*cbo_rack_no*cbo_shelf_no*cbo_bin_no','','');

				release_freezing();

			}

		//}
	}


	function check_exchange_rate(curr_id)
	{
		var cbo_currercy=curr_id;
		var booking_date = $('#txt_leftover_date').val();
		var response=return_global_ajax_value( cbo_currercy+"**"+booking_date, 'check_conversion_rate', '', 'requires/left_over_garments_receive_controller');
		var response=response.split("_");
		$('#exchange_rate').val(response[1]);
	}

	function fn_total_update(tableName,index) // for color and size level
	{
	    var left_over_balance = $("#txt_left_over_balance").val()*1;
	    var left_receive = $("#txt_total_left_over_receive_hidden").val()*1;
	    var current_total = left_over_balance+left_receive;
		var txt_user_lebel=$('#txt_user_lebel').val();
		var variable_is_controll=$('#variable_is_controll').val();

		var totalVal = 0;
		$("input[name=colorSize]").each(function(index, element) {
	        totalVal += ( $(this).val() )*1;
	    });

		if(totalVal*1 > current_total*1)
		{
			if(variable_is_controll==1 && txt_user_lebel!=2)
			{
				alert("Qnty Excceded by "+(current_total));
				$("#colSize_"+tableName+index).val('');
				return;
			}
			else
			{
				if( confirm("Qnty Excceded by "+(current_total)) )
				{
					$("#colSize_"+tableName+index).val('');
					void(0);
				}
				else
				{
					$("#colSize_"+tableName+index).val('');
				}
				return;
			}

		}

		var totalRow = $("#table_"+tableName+" tr").length;
		//alert(tableName);
		if( $("#sewing_production_variable").val()*1 == 3)
		{
			math_operation( "total_"+tableName, "colSize_"+tableName, "+", totalRow);
		}
		if($("#total_"+tableName).val()*1!=0)
		{
			$("#total_"+tableName).html($("#total_"+tableName).val());
		}
		$("#txt_total_left_over_receive").val(totalVal);

		var total_left = $("#txt_total_left_over_receive").val()*1;

		//Laftover Amount = (Total Left Over Receive *  FOB Rate);
		var left_over = ($('#txt_fob_rate').val()*1)*total_left;
		//alert(left_over);
		$('#txt_leftover_amount').val(left_over);

		var bdt_amount = ($('#exchange_rate').val()*1) * $('#txt_leftover_amount').val()*1;
		$('#txt_bdt_amount').val(bdt_amount);
	}


	function fn_total__(tableName,index) // for color and size level
	{
	    var filed_value = $("#colSize_"+tableName+index).val()*1;
		var placeholder_value = $("#colSize_"+tableName+index).attr('placeholder')*1;
		var txt_user_lebel=$('#txt_user_lebel').val()*1;
		var variable_is_controll=$('#hidden_variable_cntl').val()*1;

		if(filed_value > placeholder_value)
		{
			//alert(variable_is_controll+"_"+txt_user_lebel);
			if(txt_user_lebel!=2)
			{
				alert("User Lebel 2");
			}
			else
			{
				// alert("Qnty Excceded by"+(placeholder_value-filed_value));
				// $("#colSize_"+tableName+index).val('');
			}

		}

		var totalRow = $("#table_"+tableName+" tr").length;
		//alert(tableName);
		if( $("#sewing_production_variable").val()*1 == 3)
		{
			math_operation( "total_"+tableName, "colSize_"+tableName, "+", totalRow);
		}
		if($("#total_"+tableName).val()*1!=0)
		{
			$("#total_"+tableName).html($("#total_"+tableName).val());
		}
		var totalVal = 0;
		$("input[name=colorSize]").each(function(index, element)
		{
	        totalVal += ( $(this).val() )*1;
	    });
		//alert(totalVal);
		$("#txt_total_left_over_receive").val(totalVal);

		var total_left = $("#txt_total_left_over_receive").val()*1;

		//Laftover Amount = (Total Left Over Receive *  FOB Rate);
		var left_over = ($('#txt_fob_rate').val()*1)*total_left;
		//alert(left_over);
		$('#txt_leftover_amount').val(left_over);

		var bdt_amount = ($('#exchange_rate').val()*1) * $('#txt_leftover_amount').val()*1;
		$('#txt_bdt_amount').val(bdt_amount);
	}

	function childFormReset(id)
	{
		if(id==1 || id==3){ // Button Resate
			$("#cbo_company_name").removeAttr("disabled");
			$("#cbo_location_name").removeAttr("disabled");
			$("#txt_leftover_date").removeAttr("disabled");
			$("#cbo_order_type").removeAttr("disabled");
			//$("#cbo_buyer_name").removeAttr("disabled");
			$("#cbo_store_name").removeAttr("disabled");
			$("#cbo_goods_type").removeAttr("disabled");
			// $("#cbo_floor_name").removeAttr("disabled");
			$("#txt_remark").removeAttr("disabled");
			$('#breakdown_td_id').html('');
			$('#list_view_container').html('');

		}else if(id==2){ // Button Save / Update

			//$('#list_view_container').html('');

		}else{

			//reset_form('','','txt_remark*txt_remark2*txt_mst_id','','');
			//$('#txt_sewing_quantity').attr('placeholder','');
			//$('#list_view_container').html('');

		}
	}


	function populate_selected_data(dtls_id,mst_id,po_id,country_id,item_id,variableSettings)
	{
		freeze_window(5);

		reset_form('','','hidden_dtls_id*txt_order_no*txt_po_id*txt_style_name*cbo_item_name*cbo_country_name*txt_total_left_over_receive*txt_remark2*cbo_currency*txt_fob_rate*txt_leftover_amount*txt_bdt_amount*cbo_room_no*cbo_rack_no*cbo_shelf_no*cbo_bin_no','','');

		var company_name 			= $('#cbo_company_name').val();
		var location_name 			= $('#cbo_location_name').val();
		var order_type 				= $('#cbo_order_type').val();
		var buyer_name 				= $('#cbo_buyer_name').val();
		var goods_type 				= $('#cbo_goods_type').val();
		var working_company_name 	= $('#cbo_working_company_name').val();
		var working_location_name 	= $('#cbo_working_location_name').val();
		var country_maintain_variable 	= $('#country_maintain_variable').val();
		var leftover_source 		= $('#leftover_source').val();
		var wo_com_loc_mandatory	= $('#wo_company_and_location_mandatory').val();

		//if(variableSettings!=1)
		//{
			get_php_form_data(po_id+'**'+country_id+'**'+item_id+'**'+dtls_id+'**'+mst_id+'**'+variableSettings+'**'+$('#cbo_order_type').val()+'**'+$('#country_maintain_variable').val()+'**'+$('#leftover_source').val()+'**'+$('#cbo_goods_type').val()+'**'+$('#cbo_working_company_name').val()+'**'+$('#cbo_working_location_name').val()+'**'+wo_com_loc_mandatory, "color_and_size_level_left_over_update", "requires/left_over_garments_receive_controller" );
			get_left_over_balance(po_id,country_id,item_id,company_name,location_name,working_company_name,working_location_name,country_maintain_variable,leftover_source);
			show_list_view(po_id+'**'+company_name+'**'+location_name+'**'+order_type+'**'+buyer_name+'**'+goods_type+'**'+working_company_name+'**'+working_location_name+'**'+country_maintain_variable+'**'+leftover_source+'**'+wo_com_loc_mandatory,'show_country_listview','list_view_country','requires/left_over_garments_receive_controller');

		//}
		release_freezing();
	}


	function left_over_gmts_receive_print()
	{
		if ( form_validation('txt_system_no*txt_mst_id','System No*System ID')==false )
		{
			return;
		}
		else
		{
			var rowCount = document.getElementById('tbl_list_search').rows.length;
			var receiveAmounts = '';

			for(var i = 1; i < rowCount; i++) {
				receiveAmounts += '*'+document.getElementById('leftOverReceive_'+i).innerHTML;
			}

			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#txt_mst_id').val()+'*'+report_title+'*'+$('#txt_system_no').val()+'*'+$('#cbo_order_type').val()+'*'+rowCount+receiveAmounts, "left_over_gmts_receive_print", "requires/left_over_garments_receive_controller");
		}

	}



	function fn_total(tableName,index) // for color and size level
	{
	    var left_over_balance = $("#txt_left_over_balance").val()*1;
		var txt_user_lebel=$('#txt_user_lebel').val();
		var variable_is_controll=$('#variable_is_controll').val();

		var totalVal = 0;
		$("input[name=colorSize]").each(function(index, element) {
	        totalVal += ( $(this).val() )*1;
	    });

		if(totalVal*1 > left_over_balance*1)
		{
			if(variable_is_controll==1 && txt_user_lebel!=2)
			{
				alert("Qnty Excceded by "+(left_over_balance));
				$("#colSize_"+tableName+index).val('');
				return;
			}
			else
			{
				if( confirm("Qnty Excceded by "+(left_over_balance)) )
				{
					$("#colSize_"+tableName+index).val('');
					void(0);
				}
				else
				{
					$("#colSize_"+tableName+index).val('');
				}
				return;
			}

		}

		var totalRow = $("#table_"+tableName+" tr").length;
		//alert(tableName);
		if( $("#sewing_production_variable").val()*1 == 3)
		{
			math_operation( "total_"+tableName, "colSize_"+tableName, "+", totalRow);
		}
		if($("#total_"+tableName).val()*1!=0)
		{
			$("#total_"+tableName).html($("#total_"+tableName).val());
		}
		$("#txt_total_left_over_receive").val(totalVal);

		var total_left = $("#txt_total_left_over_receive").val()*1;

		//Laftover Amount = (Total Left Over Receive *  FOB Rate);
		var left_over = ($('#txt_fob_rate').val()*1)*total_left;
		//alert(left_over);
		$('#txt_leftover_amount').val(left_over);

		var bdt_amount = ($('#exchange_rate').val()*1) * $('#txt_leftover_amount').val()*1;
		$('#txt_bdt_amount').val(bdt_amount);
	}

	function fn_colorlevel_total(index) //for color level
	{

		var filed_value = $("#colSize_"+index).val();
		var placeholder_value = $("#colSize_"+index).attr('placeholder');
		var txt_user_lebel=$('#txt_user_lebel').val();
		var variable_is_controll=$('#variable_is_controll').val();

		if(filed_value*1 > placeholder_value*1)
		{
			alert("Qnty Excceded by"+(placeholder_value-filed_value));
			$("#colSize_"+index).val('');
		}

	    var totalRow = $("#table_color tbody tr").length;
		//alert(totalRow);
		math_operation( "total_color", "colSize_", "+", totalRow);
		$("#txt_total_left_over_receive").val( $("#total_color").val() );
	}

	/*function check_left_over_qty(current_qty) //for gross level
	{

		var prev_qty = $("#txt_total_left_over_receive_hidden").val()*1;
		var balance = $("#txt_left_over_balance").val()*1;
		var txt_user_lebel=$('#txt_user_lebel').val();
		var leftoverQty = prev_qty + balance;
		// alert(leftoverQty+'=='+current_qty);
		var variable_is_controll=$('#variable_is_controll').val();

		if(current_qty*1 > leftoverQty*1)
		{
			alert("Qnty Excceded by"+(leftoverQty-current_qty));
			$("#txt_total_left_over_receive").val('');
		}
	}*/

	function calculateAmounts(leftOverReceive) {
		var exchangeRate=$('#exchange_rate').val();
		// var leftOverBalance=$('#txt_left_over_balance').val();
		var fobRate=$('#txt_fob_rate').val();
		var leftOverAmount = leftOverReceive*fobRate;
		var bdtAmount = leftOverReceive*fobRate*exchangeRate;

		$("#txt_leftover_amount").val(leftOverAmount);
		$("#txt_bdt_amount").val(bdtAmount);
	}

	function checkLeftOverBalance() {
		var leftOverRcv = parseFloat($('#txt_total_left_over_receive').val());
		var leftOverBalance = parseFloat($('#txt_left_over_balance').val());

		if (leftOverRcv > leftOverBalance) {
			alert("Qnty Excceded by"+(leftOverBalance-leftOverRcv));
			$("#txt_total_left_over_receive").val('');
		}
	}
</script>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;">
	<? echo load_freeze_divs ("../",$permission);  ?>
	<div style="width:930px; float:left" align="center">
        <fieldset style="width:930px;">
        <legend>Left Over Garments Receive</legend>
            <form name="ironoutput_1" id="ironoutput_1" autocomplete="off" >
  				<fieldset>
                    <table width="100%" border="0">
                        <tr>
                            <td colspan="3" align="right">System ID</td>
                            <td colspan="3">
                            <input name="txt_system_no" id="txt_system_no" onDblClick="system_number_popup()" placeholder="Browse"  class="text_boxes"  style="width:158px;"  >
                            <input type="hidden" name="txt_mst_id" id="txt_mst_id" disabled />
                            </td>
                        </tr>
                        <tr>
                            <td width="110" class="must_entry_caption">Company</td>
                            <td width="180">
                            <?
                            echo create_drop_down( "cbo_company_name", 170, "select company_name,id from lib_company comp where is_deleted=0  and status_active=1 $company_cond  order by company_name",'id,company_name', 1, '--- Select Company ---', 0, "load_drop_down( 'requires/left_over_garments_receive_controller', this.value, 'load_drop_down_location', 'location' );load_drop_down( 'requires/left_over_garments_receive_controller',this.value, 'load_drop_down_store_name', 'store_name_td');get_php_form_data(this.value,'load_variable_settings','requires/left_over_garments_receive_controller');" );
                            ?>
                            <input type="hidden" id="sewing_production_variable" />
                            <input type="hidden" id="country_maintain_variable" />
                            <input type="hidden" id="leftover_source" />
                            <input type="hidden" id="hidden_colorSizeID"  value=""  />
                            <input type="hidden" id="hidden_po_break_down_id" value="" />
                            <input type="hidden" id="styleOrOrderWisw"  value="" />
                            <input type="hidden" id="txt_user_lebel" value="<? echo $level; ?>" />
                            <input type="hidden" id="variable_is_controll"  value="" />
                            <input type="hidden" id="iron_production_variable_rej"  value="" />
                            <input type="hidden" id="hidden_currency_id"  value="" />
                            <input type="hidden" id="hidden_exchange_rate"  value="" />
                            <input type="hidden" id="hidden_piece_rate"  value="" />
                            <input type="hidden" name="hidden_variable_cntl" id="hidden_variable_cntl" value="0">
            				<input type="hidden" name="hidden_preceding_process" id="hidden_preceding_process">
            				<input type="hidden" name="wo_company_and_location_mandatory" id="wo_company_and_location_mandatory">


                            </td>
                            <td width="110" id="locations"  class="must_entry_caption">Location</td>
                            <td width="180" id="location">
                            <?
                            echo create_drop_down( "cbo_location_name", 170, $blank_array,'', 1, '--- Select Location ---', $selected, "",0,0 );
                            ?>
                            </td>
                            <td width="110" class="must_entry_caption">Receive Date</td>
                            <td width="180">
                            <input name="txt_leftover_date" id="txt_leftover_date" class="datepicker" type="text" value="<? echo date("d-m-Y")?>" style="width:158px;"   />
                            </td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Order Type</td>
                            <td>
                            <?

                            echo create_drop_down( "cbo_order_type", 170, $order_source, "", 0, "-- Select --", $selected, "", "", "1,2", "", "");
                            ?>
                            </td>
                            <td>Buyer</td>
                            <td>
                            <?
                            echo create_drop_down( "cbo_buyer_name", 170, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,0 );
                            ?>
                            </td>
                            <td class="must_entry_caption">Store Name</td>
                            <td id="store_name_td" >

                            <?
                            echo create_drop_down( "cbo_store_name", 170, "select id,store_name from lib_store_location  where id='$data'", "id,store_name", 1, "-- Select Store --", $selected,"",0,0);
                            ?>
                            </td>
                        </tr>
                        <tr>
                        	<td width="120" class="must_entry_caption">Goods Type</td>
	                        <td>
	                        <?
	                        $goods_type_arr = array(1=>"Good GMT In Hand",2=>"Damage GMT",3=>"Leftover Sample");
	                        echo create_drop_down( "cbo_goods_type", 170, $goods_type_arr, "", 1, "-- Select Goods Type --", $selected, "", "", "", '', '');
	                        ?>
	                        </td>
	                        <!-- <td>Floor</td>
	                        <td id="cbo_floor">
	                        <?
	                        //echo create_drop_down( "cbo_floor_name", 170, $blank_array, "",1, "-- Select Floor --", $selected, "" );
	                        ?>
	                        </td> -->
	                        <td width="100">Exchange Rate</td>
	                        <td width="170">
	                        <input name="exchange_rate" id="exchange_rate" class="text_boxes" style="width:158px " disabled>
	                        </td>
	                        <td width="110" class="must_entry_caption">Working Company</td>
                            <td width="180">
                            <?
                            echo create_drop_down( "cbo_working_company_name", 170, "select company_name,id from lib_company comp where is_deleted=0  and status_active=1 $company_cond  order by company_name",'id,company_name', 1, '--- Select working Company ---', 0, "load_drop_down( 'requires/left_over_garments_receive_controller', this.value, 'load_drop_down_working_location', 'working_location' );" );
                            ?>
                            </td>
                        </tr>
                        <tr>

                            <td width="110" id="locations"  class="must_entry_caption">Working Location</td>
                            <td width="180" id="working_location">
                            <?
                            echo create_drop_down( "cbo_working_location_name", 170, $blank_array,'', 1, '--- Select Location ---', $selected, "",0,0 );
                            ?>
                            </td>
							<!-- class="must_entry_caption" -->
                             <td width="110">Working Floor</td>
                            <td width="180" id="working_floor">
                            	<?
	                        	echo create_drop_down( "cbo_working_floor_name", 170, $blank_array, "",1, "-- Select Floor --", $selected, "" );
	                        	?>
                            </td>
                            <td>Remarks</td>
                            <td colspan="3">
                            <input name="txt_remark" id="txt_remark" class="text_boxes"  style="width:96.5% " >
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <br />
                <div id="dtls_data" align="center">
                 	<table cellpadding="0" cellspacing="1" width="100%">
                        <tr>
                            <td width="30%" valign="top">
                            <fieldset>
                            <legend>New Entry</legend>
                                <table  cellpadding="0" cellspacing="2" width="100%">
                                    <tr>
                                        <td class="must_entry_caption">PO No</td>
                                        <td>
                                        <input name="txt_order_no" placeholder="Double Click to Search" id="txt_order_no" onDblClick="openmypage()"  class="text_boxes" style="width:120px" readonly />
                                        <input type="hidden" id="txt_po_id"  name="txt_po_id"/>
                                        <input type="hidden" id="hidden_dtls_id"  name="hidden_dtls_id"/>
                                        <input type="hidden" id="hidden_job_no"  name="hidden_job_no"/>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Style Name</td>
                                        <td>
                                        <input name="txt_style_name" id="txt_style_name" class="text_boxes"  style="width:120px" disabled  />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Item Name</td>
                                        <td>
                                        <?
                                        echo create_drop_down( "cbo_item_name", 132, $garments_item,"", 1, "-- Select Item --", $selected, "",1,0 );
                                        ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Country</td>
                                        <td>
                                        <?
                                        echo create_drop_down( "cbo_country_name", 132, "select id,country_name from lib_country","id,country_name", 1, "-- Select Country --", $selected, "",1 );
                                        ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Total Left Over Receive</td>
                                        <td>
                                        <input type="text" name="txt_total_left_over_receive" id="txt_total_left_over_receive" class="text_boxes_numeric" style="width:120px" onkeyup="calculateAmounts(this.value);checkLeftOverBalance();" disabled/>
                                        <input type="hidden" name="txt_total_left_over_receive_hidden" id="txt_total_left_over_receive_hidden" class="text_boxes_numeric"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Left Over Balance</td>
                                        <td>
                                        <input type="text" name="txt_left_over_balance" id="txt_left_over_balance" class="text_boxes_numeric" style="width:120px" disabled/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Category</td>
                                        <td>
	                                        <?php
	                                        	$categories = array(1 => 'A', 2 => 'B',3 => 'C',4=>'D',5=>'Sample');
	                                        	echo create_drop_down( 'cbo_category_id', 132, $categories, '', 1, '-- Select Category --', $selected, '', 0);
	                                        ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Remarks</td>
                                        <td>
                                        <input type="text" name="txt_remark2" id="txt_remark2" class="text_boxes" style="width:120px" />
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                            </td>
                            <td width="1%" valign="top"></td>
                            <td width="25%" valign="top">
                            <fieldset>
                            <legend>Display</legend>
                                <table  cellpadding="0" cellspacing="2" width="100%" >
                                    <tr>
                                        <td width="140">Currency</td>
                                        <td>
                                        <?
                                        echo create_drop_down( "cbo_currency", 92, $currency, "", 1, "-- Select --", $selected, "",1, "","","");
                                        ?>
                                        <input type="hidden" name="hidden_currency_rate" id="hidden_currency_rate" value="" class="text_boxes" style="width:80px"  />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>FOB rate</td>
                                        <td>
                                        <input type="text" name="txt_fob_rate" id="txt_fob_rate" class="text_boxes_numeric" style="width:80px" disabled />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td> Leftover Amount</td>
                                        <td>
                                        <input type="text" name="txt_leftover_amount" id="txt_leftover_amount" class="text_boxes_numeric" style="width:80px"  disabled  />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>BDT Amount</td>
                                        <td>
                                        <input type="text" name="txt_bdt_amount" id="txt_bdt_amount" class="text_boxes_numeric" style="width:80px" disabled />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Room</td>
                                        <td>
                                        <input type="text" name="cbo_room_no" id="cbo_room_no" class="text_boxes_numeric" style="width:80px" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Rack</td>
                                        <td>
                                        <input type="text" name="cbo_rack_no" id="cbo_rack_no" class="text_boxes_numeric" style="width:80px" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Shelf</td>
                                        <td>
                                        <input type="text" name="cbo_shelf_no" id="cbo_shelf_no" class="text_boxes_numeric" style="width:80px" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="">Bin</td>
                                        <td>
                                        <input type="text" name="cbo_bin_no" id="cbo_bin_no" class="text_boxes_numeric" style="width:80px" />
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                            </td>
                            <td width="40%" valign="top" >
                            	<div style="max-height:300px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" colspan="9" valign="middle" class="button_container">
                            <?
                            $date=date('d-m-Y');
                            echo load_submit_buttons( $permission, "fnc_left_over_gmts_input", 0,0,"reset_form('ironoutput_1','list_view_country','','txt_leftover_date,".$date."','childFormReset(1)')",1);
                            ?>
                            <input value="Print" name="receive_print" onClick="left_over_gmts_receive_print()" style="width:80px" id="receive_print" class="formbutton" type="button">
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                	</table>
                </div>
         		<div style="width:930px; margin-top:5px;"  id="list_view_container" align="center"></div>
            </form>
        </fieldset>
    </div>
	<div id="list_view_country" style="width:385px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>