<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Cut Garments Order Entry

Functionality	:
JS Functions	:
Created by		:	Kamrul Hasan
Creation date 	:   17-05-2023
Updated by 		: 	
Update date		: 	
Purpose			: 	
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;


//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];
$location_id = $userCredential[0][csf('location_id')];

$company_credential_cond = "";

if ($company_id >0) {
    $company_credential_cond = "and comp.id in($company_id)";
}

if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)"; 
}

if($item_cate_id !='') {
    $item_cate_credential_cond = $item_cate_id ;  
}
//========== user credential end ==========

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Distribution Receive","../../", 1, 1, $unicode,'','');

?>
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

<?
$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][121] );
echo "var field_level_data= ". $data_arr . ";\n";
?>
function fnc_company_check(val)
{
	if(val==1)
	{
		if($("#cbo_company_name").val()==0)
		{
			alert("Please Select Company.");
			$("#cbo_source").val(0);
		
			return;
		}
		
	}
	else
	{
		get_php_form_data(document.getElementById('cbo_company_name').value,'production_process_control','requires/sewing_input_controller' );
	}
}

function dynamic_must_entry_caption(data)
{
 	if(data==1)
	{
		$('#locations').css('color','blue');
		$('#floors').css('color','blue');

	}
	else
	{
		$('#locations').css('color','black');
		$('#floors').css('color','black');

	}

}




function openmypage(page_link,title)
{
	//var company = $("#cbo_company_name").val();
	/*if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}*/
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var po_id=this.contentDoc.getElementById("hidden_mst_id").value;//po id

		var item_id=this.contentDoc.getElementById("hidden_grmtItem_id").value;
		var po_qnty=this.contentDoc.getElementById("hidden_po_qnty").value;
		var plan_qnty=this.contentDoc.getElementById("hidden_plancut_qnty").value;
		var country_id=this.contentDoc.getElementById("hidden_country_id").value;
		var job_num=this.contentDoc.getElementById("hid_job_num").value;
		var company_id=this.contentDoc.getElementById("hid_company_id").value;
		// alert($item_id);
		// return;

		if (po_id!="")
		{
			freeze_window(5);
			$("#cbo_item_name").val(item_id);
			$("#txt_order_qty").val(po_qnty);
			$("#txt_plancut_qty").val(plan_qnty);
			$("#cbo_country_name").val(country_id);
			$("#hid_job_num").val(job_num);
			$("#cbo_company_name").val(company_id);
			fnc_company_check(3);
	
			get_php_form_data(company_id,'load_variable_settings','requires/distribution_receive_controller');

			childFormReset();//child from reset
			get_php_form_data(po_id+'**'+item_id+'**'+country_id, "populate_data_from_search_popup", "requires/distribution_receive_controller" );
			var variableSettings=$('#sewing_production_variable').val();
			var styleOrOrderWisw=$('#styleOrOrderWisw').val();
		

			if(variableSettings!=1)
			{
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+job_num, 'color_and_size_level',"requires/distribution_receive_controller" );
			}
			else
			{
				$("#txt_cutting_qty").removeAttr("readonly");
			}


			show_list_view(po_id+'**'+item_id+'**'+country_id,'show_dtls_listview','cutting_production_list_view','requires/distribution_receive_controller','setFilterGrid(\'tbl_list_search\',-1)');
			show_list_view(po_id,'show_country_listview','list_view_country','requires/distribution_receive_controller','');
 			set_button_status(0, permission, 'fnc_cutting_update_entry',1);
 			release_freezing();
		}
		$("#cbo_company_name").attr("disabled","disabled");
	}
}



function fnc_cutting_update_entry(operation)
{
	/*if(operation==2)
	{
		show_msg('13');
		return;
	}*/
	var source=$("#cbo_source").val();

	if ( form_validation('cbo_company_name*txt_order_no*cbo_cutting_company*txt_cutting_date','Company Name*Order No*Cutting Comapny*Cutting Date')==false )
	{
		return;
	}

	else
		{
			if(source==1)
			{
				if ( form_validation('cbo_location*cbo_floor','Location*Floor')==false )
				{
					return;
				}

			}

			if($('#txt_cutting_qty').val()<1)
			{
				alert("Cutting quantity   should be filled up.");
				return;
				}

			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_cutting_date').val(), current_date)==false)
			{
				alert("Cutting Date Can not Be Greater Than Today");
				return;
			}
			var sewing_production_variable = $("#sewing_production_variable").val();
			
			if(sewing_production_variable=="" || sewing_production_variable==0)
			{
 				sewing_production_variable=3;
 			
			}

			var colorList = ($('#hidden_colorSizeID').val()).split(",");
			var i=0; var k=0; var colorIDvalue=''; var colorIDvalueRej='';
			if(sewing_production_variable==2)//color level
			{
				$("input[name=txt_color]").each(function(index, element) {
 					if( $(this).val()!='' )
					{
						/*if(i==0)
						{
							colorIDvalue = colorList[i]+"*"+$(this).val();
						}
						else
						{
							colorIDvalue += "**"+colorList[i]+"*"+$(this).val();
						}*/

						if(colorIDvalue=="") colorIDvalue = colorList[i]+"*"+$(this).val(); else colorIDvalue += "***"+colorList[i]+"*"+$(this).val();
					}
					i++;
				});
			}
			else if(sewing_production_variable==3)//color and size level
			{
				$("input[name=colorSize]").each(function(index, element) {
					if( $(this).val()!='' )
					{
						/*if(i==0)
						{
							colorIDvalue = colorList[i]+"*"+$(this).val();
						}
						else
						{

							colorIDvalue += "***"+colorList[i]+"*"+$(this).val();
						}*/

						if(colorIDvalue=="") colorIDvalue = colorList[i]+"*"+$(this).val(); else colorIDvalue += "***"+colorList[i]+"*"+$(this).val();
					}
 					i++;
				});
			}


			var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+get_submitted_data_string('garments_nature*cbo_company_name*cbo_country_name*sewing_production_variable*hidden_po_break_down_id*hidden_colorSizeID*cbo_buyer_name*txt_style_no*cbo_item_name*txt_order_qty*txt_plancut_qty*cbo_source*cbo_cutting_company*cbo_location*cbo_floor*txt_cutting_date*txt_reporting_hour*txt_cutting_qty*txt_challan_no*txt_remark*txt_cumul_cutting*txt_yet_cut*hidden_break_down_html*txt_mst_id*hid_job_num*hidden_currency_id*hidden_exchange_rate*hidden_piece_rate',"../../");
			//alert(data); return;
 			freeze_window(operation);
 			http.open("POST","requires/distribution_receive_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = cutting_update_entry_Reply_info;
		}
}

function cutting_update_entry_Reply_info()
{
 	if(http.readyState == 4)
	{
		//alert(http.responseText);
		var variableSettings=$('#sewing_production_variable').val();
		
		var styleOrOrderWisw=$('#styleOrOrderWisw').val();
		var item_id = $("#cbo_item_name").val();
		var country_id = $("#cbo_country_name").val();

		var reponse=http.responseText.split('**');


		if(reponse[0]==15)
		{
			 setTimeout('fnc_cutting_update_entry('+ reponse[1]+')',8000);
		}
		else if(reponse[0]==167)
		{
			alert("Data found in next process for this PO/Item/Country");
		}
		else if(reponse[0]==120)
		{
			alert("Production date cannot be entry back date ");
		}
		else if(reponse[0]==168)
		{
			alert("Cutting quantity is not less than sewing quantity");
		}
		else if(reponse[0]==786)
		{
			alert("Projected PO is not allowed to production. Please check variable settings."); return;
		}
		else if(reponse[0]==0)//insert
		{
			var po_id = reponse[1];

			//alert(reponse[0]+'_'+po_id);

			show_msg(trim(reponse[0]));
			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id,'show_dtls_listview','cutting_production_list_view','requires/distribution_receive_controller','setFilterGrid(\'tbl_list_search\',-1)');
			 	//cbo_produced_by*
			reset_form('','','txt_cutting_qty*hidden_break_down_html*hidden_colorSizeID*txt_remark*txt_mst_id','','','txt_cutting_date*txt_reporting_hour');
 			get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id, "populate_data_from_search_popup", "requires/distribution_receive_controller" );

			if(variableSettings!=1) {
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id, "color_and_size_level", "requires/distribution_receive_controller" );
			}
			else
			{
				$("#txt_cutting_qty").removeAttr("readonly");
			}

			

			$('#txt_cutting_qty').attr('placeholder','');
		}
		else if(reponse[0]==1)//update
		{
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id,'show_dtls_listview','cutting_production_list_view','requires/distribution_receive_controller','setFilterGrid(\'tbl_list_search\',-1)');
			//reset_form( forms, divs, fields, default_val, extra_func, non_refresh_ids )
			//cbo_produced_by*
			reset_form('','','txt_cutting_qty*hidden_break_down_html*hidden_colorSizeID*txt_remark*txt_mst_id','','','txt_cutting_date*txt_reporting_hour');
			get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id, "populate_data_from_search_popup", "requires/distribution_receive_controller" );

			if(variableSettings!=1) {
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id,  "requires/distribution_receive_controller" );
			}
			else
			{
				$("#txt_cutting_qty").removeAttr("readonly");
			}


			$('#txt_cutting_qty').attr('placeholder','');
			set_button_status(0, permission, 'fnc_cutting_update_entry',1);
		}
		else if(reponse[0]==2)//delete
		{
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id,'show_dtls_listview','cutting_production_list_view','requires/distribution_receive_controller','setFilterGrid(\'tbl_list_search\',-1)');
			//cbo_produced_by*
			reset_form('','','txt_reporting_hour*txt_cutting_qty*hidden_break_down_html*hidden_colorSizeID*txt_remark*txt_mst_id','txt_cutting_date,<? echo date("d-m-Y"); ?>','');
			get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id, "populate_data_from_search_popup", "requires/distribution_receive_controller" );

			if(variableSettings!=1) {
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id, "requires/distribution_receive_controller" );
			}
			else
			{
				$("#txt_cutting_qty").removeAttr("readonly");
			}


 			set_button_status(0, permission, 'fnc_cutting_update_entry',1);
 			window.location.reload();
		}
		release_freezing();

 	}
}


function childFormReset()
{//cbo_produced_by*
	reset_form('','cutting_production_list_view','txt_reporting_hour*txt_challan_no*txt_cutting_qty*hidden_break_down_html*hidden_colorSizeID*txt_remark*txt_cumul_cutting*txt_yet_cut*txt_mst_id','','');
	$('#txt_cutting_qty').attr('placeholder','');//placeholder value initilize
	$('#txt_cumul_cutting').attr('placeholder','');//placeholder value initilize
	$('#txt_yet_cut').attr('placeholder','');//placeholder value initilize
	$("#cutting_production_list_view").html('');
	$("#breakdown_td_id").html('');
}


function fn_hour_check(val)
{

	var hours = $("#txt_reporting_hour").val();
	var hoursArr = hours.split(".");
  	if( hoursArr[1] ) {
 		$("#txt_reporting_hour").val(hoursArr[0]);
		return;
	}

	if(val*1>12)
	{
		alert("You Cross 12!!This is 12 Hours.");
		$("#txt_reporting_hour").val('');
	}

}


function fn_total(tableName,index) // for color and size level
{
    var filed_value = $("#colSize_"+tableName+index).val();
	var placeholder_value = $("#colSize_"+tableName+index).attr('placeholder');
	if(filed_value*1 > placeholder_value*1)
	{
		if( confirm("Qnty Excceded by"+(placeholder_value-filed_value)) )
			void(0);
		else
		{
			$("#colSize_"+tableName+index).val('');
 		}
	}

	var totalRow = $("#table_"+tableName+" tr").length;
	//alert(tableName);
	math_operation( "total_"+tableName, "colSize_"+tableName, "+", totalRow);
	if($("#total_"+tableName).val()*1!=0)
	{
		$("#total_"+tableName).html($("#total_"+tableName).val());
	}
	var totalVal = 0;
	$("input[name=colorSize]").each(function(index, element) {
        totalVal += ( $(this).val() )*1;
    });
	$("#txt_cutting_qty").val(totalVal);
}

function fn_total_rej(tableName,index) // for color and size level
{
    var filed_value = $("#colSizeRej_"+tableName+index).val();
    var colsizes= $("#colSize_"+tableName+index).val();
    if(colsizes=="" && filed_value !="")
    {
    	$("#colSize_"+tableName+index).val(0);
    }
	var totalRow = $("#table_"+tableName+" tr").length;
	//alert(tableName);
	math_operation( "total_"+tableName, "colSizeRej_"+tableName, "+", totalRow);

	var totalValRej = 0;
	$("input[name=colorSizeRej]").each(function(index, element) {
        totalValRej += ( $(this).val() )*1;
    });
	$("#txt_reject_qty").val(totalValRej);
}


function fn_colorlevel_total(index) //for color level
{

	var filed_value = $("#colSize_"+index).val();
	var placeholder_value = $("#colSize_"+index).attr('placeholder');
	if(filed_value*1 > placeholder_value*1)
	{
		if( confirm("Qnty Excceded by"+(placeholder_value-filed_value)) )
			void(0);
		else
		{
			$("#colSize_"+index).val('');
 		}
	}

    var totalRow = $("#table_color tbody tr").length;
	//alert(totalRow);
	math_operation( "total_color", "colSize_", "+", totalRow);
	$("#txt_cutting_qty").val( $("#total_color").val() );
}





function put_country_data(po_id, item_id, country_id, po_qnty, plan_qnty)
{
	freeze_window(5);

	childFormReset();//child from reset
	$("#cbo_item_name").val(item_id);
	$("#txt_order_qty").val(po_qnty);
	$("#txt_plancut_qty").val(plan_qnty);
	$("#cbo_country_name").val(country_id);

	get_php_form_data(po_id+'**'+item_id+'**'+country_id, "populate_data_from_search_popup", "requires/distribution_receive_controller" );
	var variableSettings=$('#sewing_production_variable').val();
	var variableSettingsReject=$('#cutting_production_variable_reject').val();
	var styleOrOrderWisw=$('#styleOrOrderWisw').val();
	var job_num=$("#hid_job_num").val();

	if(variableSettings!=1)
	{
		get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+job_num, "color_and_size_level","requires/distribution_receive_controller");
	}
	else
	{
		$("#txt_cutting_qty").removeAttr("readonly");
	}

	if(variableSettingsReject!=1)
	{
		$("#txt_reject_qty").attr("readonly");
	}
	else
	{
		$("#txt_reject_qty").removeAttr("readonly");
	}

	show_list_view(po_id+'**'+item_id+'**'+country_id,'show_dtls_listview','cutting_production_list_view','requires/distribution_receive_controller','');
	set_button_status(0, permission, 'fnc_cutting_update_entry',1);
	release_freezing();
}



function fnc_valid_time(val,field_id)
{
	var val_length=val.length;
	if(val_length==2)
	{
		document.getElementById(field_id).value=val+":";
	}

	var colon_contains=val.contains(":");
	if(colon_contains==false)
	{
		if(val>23)
		{
			document.getElementById(field_id).value='23:';
		}
	}
	else
	{
		var data=val.split(":");
		var minutes=data[1];
		var str_length=minutes.length;
		var hour=data[0]*1;

		if(hour>23)
		{
			hour=23;
		}

		if(str_length>=2)
		{
			minutes= minutes.substr(0, 2);
			if(minutes*1>59)
			{
				minutes=59;
			}
		}

		var valid_time=hour+":"+minutes;
		document.getElementById(field_id).value=valid_time;
	}
}

function numOnly(myfield, e, field_id)
{
	var key;
	var keychar;
	if (window.event)
		key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;
	keychar = String.fromCharCode(key);

	// control keys
	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
	return true;
	// numbers
	else if ((("0123456789:").indexOf(keychar) > -1))
	{
		var dotposl=document.getElementById(field_id).value.lastIndexOf(":");
		if(keychar==":" && dotposl!=-1)
		{
			return false;
		}
		return true;
	}
	else
		return false;
}

function fn_chk_next_process_qty(tableName,index,sizeId) // for color and size level
{
	// alert('ok');return;
	var data="action=chk_next_process_qty&colorId="+tableName+"&sizeId="+sizeId+get_submitted_data_string('cbo_country_name*sewing_production_variable*hidden_po_break_down_id*hidden_colorSizeID*cbo_item_name',"../../");
	//alert(data); return;
	var filed_value = $("#colSize_"+tableName+index).val()*1;
	var prev_value = $("#colSizeUpQty_"+tableName+index).val()*1;
	$.ajax({
		url: 'requires/distribution_receive_controller.php',
		type: 'POST',
		data: data,
		success: function(response)
		{
			var resData = trim(response).split("****");
			var cutQty = resData[0];
			var inputQty = resData[1];
			if((filed_value+(cutQty-prev_value))*1 < inputQty*1)
			{	
				alert('Sorry! Cutting qnty will not less than input qnty');			
				$("#colSize_"+tableName+index).val(prev_value);		 		
			}
		}
	});
}


</script>

</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;">
        <div style="width:850px;" align="center">
             <? echo load_freeze_divs ("../../",$permission); ?>
        </div>
        <div style="width:930px; float:left" align="center">
            <fieldset style="width:930px">
            <legend>Production Module</legend>
            	<form name="cuttingupdate_1" id="cuttingupdate_1" action=""  autocomplete="off">
                    <fieldset>
                        <table width="100%">

                            <tr>
                             	<input type="hidden" name="hidden_variable_cntl" id="hidden_variable_cntl">
            					<input type="hidden" name="hidden_preceding_process" id="hidden_preceding_process">
								 <td width="130" onClick="show_report(2)" class="must_entry_caption">Order No</td>
								 <td width="170">
									 <input name="txt_order_no" placeholder="Double Click to Search" onDblClick="openmypage('requires/distribution_receive_controller.php?action=order_popup&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value,'Order Search')" id="txt_order_no" class="text_boxes" style="width:200px " readonly />
									 <input type="hidden" id="hidden_po_break_down_id" value="" />
									 <input type="hidden" id="hid_job_num" value="" />
								</td>
								<!-- <td width="130" onClick="show_report(1)" class="must_entry_caption">Company </td>-->

                                <td width="130"   class="must_entry_caption">Company </td>

                                    <td width="170">
                                        <?
                                            echo create_drop_down( "cbo_company_name", 210, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, " get_php_form_data(this.value,'load_variable_settings','requires/distribution_receive_controller');",1 );
                                        ?>
                                        <input type="hidden" id="sewing_production_variable" />
                                        <input type="hidden" id="styleOrOrderWisw" />
                                        <input type="hidden" id="cutting_production_variable_reject" />
                                        <input type="hidden" id="sewing_production_variable" />
                                        <input type="hidden" id="styleOrOrderWisw" />
                                        <input type="hidden" id="cutting_production_variable_reject" />

                                        <input type="hidden" id="hidden_currency_id" />
                                        <input type="hidden" id="hidden_exchange_rate" />
                                        <input type="hidden" id="hidden_piece_rate" />
                                    </td>

                                    <td width="130" >Country</td>
                                    <td width="170">
                                        <?
                                            echo create_drop_down( "cbo_country_name", 210, "select id,country_name from lib_country","id,country_name", 1, "-- Select Country --", $selected, "",1 );
                                        ?>
                                    </td>
                              </tr>
                              <tr>
                                    <td width="130">Buyer Name</td>
                                    <td width="170" id="buyer_td">
                                        <?
                                            echo create_drop_down( "cbo_buyer_name", 210, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,0 );
                                        ?>
                                     </td>
                                     <td width="130">Style</td>
                                     <td width="170">
                                        <input type="text" name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:200px" disabled readonly />
                                     </td>
                                     <td width="130"> Item </td>
                                     <td width="170">
                                          <?
                                            echo create_drop_down( "cbo_item_name", 210, $garments_item,"", 1, "-- Select Item --", $selected, "",1,0 );
                                          ?>
                                     </td>
                              </tr>
                              <tr>
                                     <td width="130">Order Qnty</td>
                                     <td width="170">
                                        <input name="txt_order_qty" id="txt_order_qty" class="text_boxes_numeric"  style="width:197px" disabled readonly  />
                                     </td>
                                     <td width="130">Plan Knit Qnty</td>
                                     <td width="170">
                                        <input name="txt_plancut_qty" id="txt_plancut_qty" class="text_boxes_numeric"  style="width:200px" disabled readonly />
                                     </td>
                                     <td width="130" class="must_entry_caption">Source</td>
                                     <td width="170">
                                         <?
                                            echo create_drop_down( "cbo_source", 210, $knitting_source,"", 1, "-- Select Source --", $selected, "fnc_company_check(this.value);load_drop_down( 'requires/distribution_receive_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_cutt_company', 'cutt_company_td' );dynamic_must_entry_caption(this.value);",0,'1,3' );
                                         ?>
                                     </td>
                              </tr>
                              <tr>
                                     <td width="130" class="must_entry_caption">Knitting Company</td>
                                     <td width="170" id="cutt_company_td">
                                         <?
                                            echo create_drop_down( "cbo_cutting_company", 210, $blank_array,"", 1, "--- Select Knitting Company ---", $selected, "",0 );
                                         ?>
                                     </td>
                                     <td width="130" id="locations">Location</td>
                                     <td width="170" id="location_td">
                                         <?
                                         echo create_drop_down( "cbo_location", 210, $blank_array,"", 1, "-- Select Location --", $selected, "",0 );
                                         ?>
                                     </td>
                                     <td width="130" id="floors">Floor</td>
                                     <td width="170" id="floor_td">
                                         <?
                                         echo create_drop_down( "cbo_floor", 210, $blank_array,"", 1, "-- Select Floor--", $selected, "",0 );
                                         ?>
                                     </td>
                              
                        </table>
                        </fieldset>
                        <table><tr><td colspan="6" height="5"></td></tr></table>

                        <table cellpadding="0" cellspacing="1" width="100%">
                        	<tr>
                           		<td width="30%" valign="top">
                                    <fieldset>
                                    <legend>New Entry</legend>
                                        <table  cellpadding="0" cellspacing="2" width="100%">
                                        
                                            <tr>
                                                <td width="120" class="must_entry_caption">Distribution Entry Date</td>
                                                <td>
                                                <input class="datepicker" type="text" style="width:110px;" value="<? echo date("d-m-Y")?>" name="txt_cutting_date" id="txt_cutting_date" />
                                                </td>
                                            </tr>
                                           
                                            

                                            <tr>
                                                <td width="" class="must_entry_caption">Knitting Quantity</td>
                                                <td width="">
                                                <input type="text" name="txt_cutting_qty" id="txt_cutting_qty" class="text_boxes_numeric" style="width:110px" readonly />
                                                <input type="hidden" id="hidden_break_down_html"  value="" disabled />
                                                <input type="hidden" id="hidden_colorSizeID"  value="" disabled />
                                                 </td>
                                             </tr>
                                            
                                                <td width="">Reporting Hour</td>
                                                <td width="">
                                               <input name="txt_reporting_hour" id="txt_reporting_hour" class="text_boxes" style="width:110px" placeholder="24 Hour Format" onBlur="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyUp="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyPress="return numOnly(this,event,this.id);" maxlength="8" />
                                                 </td>
                                            </tr>
                                            <tr>
                                                 <td width="">Challan No</td>
                                                 <td width=""><input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:110px" /></td>
                                            </tr>
                                            <tr>
                                                 <td width="">Remarks</td>
                                                 <td colspan="4">
                                                 <input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:110px"  />
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
                                                    <td width="120">Cuml. Knit.</td>
                                                    <td>
                                                    <input type="text" name="txt_cumul_cutting" id="txt_cumul_cutting" class="text_boxes_numeric" style="width:80px" disabled readonly />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="120">Yet to Knit.</td>
                                                    <td>
                                                    <input type="text" name="txt_yet_cut" id="txt_yet_cut" class="text_boxes_numeric" style="width:80px" disabled  readonly />
                                                    </td>
                                                </tr>
                                            </table>
                                        </fieldset>
                                </td>
                                <td width="1%" valign="top"></td>
                                <td width="40%" valign="top">
                                    <div style="max-height:300px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
                                </td>
                         </tr>
                         <tr>
                            <td align="center" colspan="9" valign="middle" class="button_container">
                                <?
								$date=date('d-m-Y');
                                echo load_submit_buttons( $permission, "fnc_cutting_update_entry", 0,0,"reset_form('cuttingupdate_1','list_view_country','','txt_cutting_date,".$date."','childFormReset()');",1);
                                ?>
                                <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly >
	                            <input type="hidden" name="save_data" id="save_data" readonly />
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                    <div style="width:910px; margin-top:5px;" id="cutting_production_list_view" align="center"></div>
              	</form>
            </fieldset>
        </div>
        <div id="list_view_country" style="width:390px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
