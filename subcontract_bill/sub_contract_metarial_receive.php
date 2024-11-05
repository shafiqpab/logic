<?
/*--- ----------------------------------------- Comments
Purpose			: 						
Functionality	:	
JS Functions	:
Created by		:	Md. Abdul Hakim 
Creation date 	: 	31-03-2013
Updated by 		: 		
Update date		:
Oracle Convert 	:	Kausar		
Convert date	: 	20-05-2014	   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start(); 
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sub-Contract Material Receive Info", "../", 1,1, $unicode,1,'');

?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';

	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name", "color_name" ), 0, -1); ?>];

	$(document).ready(function(e)
	 {
            $("#txt_color").autocomplete({
			 source: str_color
		  });

            $("#cbo_gmts_material_description").hide();
     });

	var str_material_description = [<? echo substr(return_library_autocomplete( "select material_description from sub_material_dtls group by material_description ", "material_description" ), 0, -1); ?> ];

	function set_auto_complete(type)
	{
		if(type=='subcon_material_receive')
		{
			$("#txt_material_description").autocomplete({
			source: str_material_description
			});
		}
	}


	var str_size = [<? echo substr(return_library_autocomplete( "select size_name from lib_size group by size_name ", "size_name" ), 0, -1); ?> ];
	var str_brand = [<? echo substr(return_library_autocomplete( "select brand_name from lib_brand group by brand_name", "brand_name" ), 0, -1); ?>];

	function set_auto_complete_size(type)
	{
		if(type=='size_return')
		{
			$(".txt_size").autocomplete({
			source: str_size
			});
		}
	}
	function set_auto_complete_brand(type)
	{
		if(type=='brand_return')
		{
			$(".txtbrand").autocomplete({
			source: str_brand
			});
		}
	}


	function openmypage_rec_id(page_link,title)
	{ 
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value;
		page_link='requires/sub_contract_material_receive_controller.php?action=receive_popup&data='+data
		
		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=850px, height=400px, center=1, resize=0, scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_subcontract_frm"); //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("selected_job");
			if (theemail.value!="")
			{
				freeze_window(5);
				reset_form('','','txt_receive_no*cbo_company_name*cbo_location_name*cbo_party_name*txt_receive_challan*txt_receive_date*cbo_item_category*txt_material_description*txt_receive_quantity*txt_order_no*cbo_dia_uom*txt_gsm*txt_color*txt_lot_no*txt_brand','','');
				get_php_form_data( theemail.value, "load_php_data_to_form", "requires/sub_contract_material_receive_controller" );
				show_list_view(theemail.value,'subcontract_receive_dtls_list_view','receive_list_view','requires/sub_contract_material_receive_controller','setFilterGrid("list_view",-1)');
				release_freezing();
			}
		}
	}

	function job_search_popup(page_link,title)
	{
		if ( form_validation('cbo_company_name*cbo_party_name','Company Name*Party Name')==false )
		{
			return;
		}
		else
		{
			var data=document.getElementById('txt_order_no').value+"_"+document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value;
			page_link='requires/sub_contract_material_receive_controller.php?action=job_popup&data='+data
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=400px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				freeze_window(5);
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("selected_order").value;
				var splt_val=theemail.split("_");
				 // alert(theemail);
				get_php_form_data( splt_val[0], "load_php_data_to_form_dtls_order", "requires/sub_contract_material_receive_controller" );
				load_drop_down( 'requires/sub_contract_material_receive_controller',splt_val[0],'load_drop_down_color_for_ord', 'color_td');
				if (splt_val[1]==1)
				{
					show_list_view(splt_val[0],'order_dtls_list_view','order_list_view','requires/sub_contract_material_receive_controller','');
				}
				else
				{
					$('#order_dtls_list_view').html('')
				}
				release_freezing();
			}
		}
	}

	function fnc_material_receive( operation )
	{
		var is_delete=document.getElementById('delete_allowed').value;
		var batch_no=document.getElementById('batch_no').value;
		var cbo_status=document.getElementById('cbo_status').value;
		var zero_val='';

		if(document.getElementById('cbo_item_category').value == 30)
		{
			if(form_validation('cbo_gmts_material_description','Material Description')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('txt_material_description','Material Description')==false)
			{
				return;
			}
		}

		if ( form_validation('cbo_company_name*cbo_party_name*txt_receive_challan*txt_receive_date*txt_order_no*cbo_item_category*txt_receive_quantity*cbo_uom', 'Company Name*Party*Receive Challan*Receive Date*Order No*Item Category*Receive Quantity*UOM')==false )
		{
			return;
		}
		else
		{
			if(cbo_status==1)
			{
				if (is_delete==1)
				{
					alert("Delete Not Allowed. Used in Batch No="+ batch_no);
					return;
				}
			}
			if (operation==2)
			{
				if (is_delete==1)
				{
					alert("Delete Not Allowed. Used in Batch No="+ batch_no);
					return;
				}
				else
				{
					var r=confirm('Press \"OK\" to delete all items of this challan.\nPress \"Cancel\" Do Not Delete.');
					if(r==true) 
					{ 
						zero_val="1";
					}
					else
					{
						zero_val="0";
						return;
					}
				}
			}
			//var rec_date=change_date_format(document.getElementById('txt_receive_date').value);
			var data="action=save_update_delete&operation="+operation+'&zero_val='+zero_val+get_submitted_data_string('txt_receive_no*cbo_company_name*cbo_location_name*cbo_party_name*txt_receive_challan*txt_receive_date*cbo_item_category*txt_material_description*txt_item_id*txt_receive_quantity*txt_rec_rate*cbo_uom*order_no_id*txt_order_no*cbo_status*txt_roll*txt_cone*txt_gsm*txt_color*txt_lot_no*txt_brand*txt_grey_dia*txt_fin_dia*cbo_dia_uom*txt_used_yarn_details*txt_stitch_length*update_id*update_id2*cbo_gmts_material_description*txtsize*txt_mc_dia*txt_mc_gauge*txt_acc_item_colour*txt_acc_item_size',"../");
			/*alert (data); return;*/
			freeze_window(operation);
			http.open("POST","requires/sub_contract_material_receive_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_material_receive_response;
		}
	}
	
	function fnc_material_receive_response()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);//return;
			var response=trim(http.responseText).split('**');
			//if (response[0].length>3) reponse[0]=10;	
			show_msg(response[0]);
			//$('#cbo_uom').val(12);
			if(response[0]==0 || response[0]==1)
			{
				document.getElementById('txt_receive_no').value= response[1];
				document.getElementById('update_id').value = response[2];
				//document.getElementById('update_id2').value = response[3];
				reset_form('','','txt_grey_dia*txt_fin_dia*cbo_dia_uom*txt_receive_quantity*txt_roll*txt_cone','','');
				
				set_button_status(0, permission, 'fnc_material_receive',1);
				show_list_view(response[2],'subcontract_receive_dtls_list_view','receive_list_view','requires/sub_contract_material_receive_controller','setFilterGrid("list_view",-1)');

				//$('#cbo_company_name').attr('disabled','disabled');
				$('#cbo_party_name').attr('disabled','disabled');
			}
			if(response[0]==14)
			{
				//alert("Issue is already done against this challan, Issue No="+response[2]);
				alert("Recv is not allowed Less then Issue Qty="+response[2]);
				release_freezing();
				return;
			}
			if(response[0]==13)
			{
				//alert("Issue is already done against this challan, Issue No="+response[2]);
				alert(response[1]);
				release_freezing();
				return;
			}
			if(response[0]==17) //Recv check
			{
				 
				alert(response[1]);
				release_freezing();
				return;
			}
			if(response[0]==2)
			{
				reset_form('materialreceive_1','receive_list_view','','', '');
			}
			release_freezing();
		}
	}

	function hide_material_description(item_id)
	{
		if(item_id == 30)
		{	
			var order_no_id= $("#order_no_id").val();
			load_drop_down( 'requires/sub_contract_material_receive_controller',order_no_id,'load_td_gmts_material', 'td_gmts_material');
			$("#cbo_gmts_material_description").show();
			$("#txt_material_description").hide();
			$("#txt_material_description").val("");
		}
		else
		{	$("#txt_material_description").show();
			$("#cbo_gmts_material_description").hide();
			$("#cbo_gmts_material_description").val("0");
		}
	}

	function change_uom(item_id)
	{
		/*if(item_id==1 || item_id==2 || item_id==13)
		{
			document.getElementById('cbo_uom').value= 12;
		}
		else if(item_id==3 || item_id==14)
		{
			document.getElementById('cbo_uom').value= 27;
		}
		else if(item_id==0 || item_id==4)
		{
			document.getElementById('cbo_uom').value= 0;
		}
		else
		{
			document.getElementById('cbo_uom').value= 1;
		}*/
		
		if (item_id==1)
		{
			$('#txt_cone').removeAttr('disabled','disabled');	
		}
		else
		{
			$('#txt_cone').val('');
			$('#txt_cone').attr('disabled','disabled');
		}
		
		if(item_id==13)
		{
			$('#txt_material_description').attr('readOnly','readOnly');
		}
		else
		{
			$('#txt_material_description').removeAttr('readOnly','readOnly');
		}
        if(item_id==13 || item_id==14 || item_id==2 || item_id==3)
		{
			$('#tr_used_yarn_details').show();
		}
		else
		{
            $('#tr_used_yarn_details').hide();
        }
	}
	
	function set_form_data(data)
	{
		var data=data.split("**");
		$('#txt_grey_dia').val(data[4]);
        $('#txt_fin_dia').val(data[5]);
                
		if($('#cbo_item_category').val()==13 || $('#cbo_item_category').val()==14 || $('#cbo_item_category').val()==2 || $('#cbo_item_category').val()==3)
		{
			$('#tr_used_yarn_details').show();
		}
		else
		{
            $('#tr_used_yarn_details').hide();
        }
                
		if($('#cbo_item_category').val()==13)
		{
			$('#txt_item_id').val(data[0]);
			$('#txt_material_description').val(data[1]);
			$('#txt_color').val(data[2]);
			$('#txt_material_description').attr('readOnly','readOnly');
            $('#txt_gsm').val(data[3]);
            // $('#tr_used_yarn_details').show();
            $('#txtsize').val(data[6]);
		}
		else
		{
			$('#txt_item_id').val(data[0]);
			$('#txt_material_description').val(data[1]);
			$('#txt_color').val(data[2]);
			$('#txt_material_description').removeAttr('readOnly','readOnly');
            $('#txt_gsm').val(data[3]);
            // $('#tr_used_yarn_details').hide();
            $('#txtsize').val(data[6]);
		}
		
		$("#txt_order_no").attr("main_process_id", data[7] );
		
		if(data[7]==3)
		{
			var prev_rec_qty_bal=data[10]*1;
			var ord_qty=data[9]*1;
			var rec_bal=ord_qty-prev_rec_qty_bal;
			$('#txt_material_description').attr('readOnly','readOnly');
			$('#txt_receive_quantity').val( prev_rec_qty_bal );
			$('#txt_rec_rate').val(data[8]);
			$("#txt_receive_quantity").attr("previous_rec_qty", prev_rec_qty_bal);
			//var previous_rec_qty=$("#txt_receive_quantity").attr("previous_rec_qty", rec_bal);
		}
	}
	
	function fnc_rec_qty_validation(rec_qty)
	{
		var placeholder_value = $("#txt_order_no").attr('main_process_id');
		var previous_rec_qty_bal=$("#txt_receive_quantity").attr("previous_rec_qty")*1;
		if(placeholder_value==3)
		{
			//alert(rec_qty+'='+previous_rec_qty_bal);
			if(rec_qty>previous_rec_qty_bal)
			{
				alert("Recive Qty over from Order Qty.");
				$('#txt_receive_quantity').val( previous_rec_qty_bal );
				return;
			}
		}
	}
	function generate_report(action)
	{
		if (form_validation('txt_receive_no','Booking No')==false)
			{
				return;
			}
			else
			{
				
				
				$report_title=$( "div.form_caption" ).html();
				var data="action="+action+get_submitted_data_string('txt_receive_no*cbo_company_name*cbo_location_name*cbo_party_name*txt_receive_challan*txt_receive_date*update_id',"../")+'&report_title='+$report_title;
				//freeze_window(5);
				http.open("POST","requires/sub_contract_material_receive_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = generate_report_reponse;
			}
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			var file_data=http.responseText.split('****');
			$('#pdf_file_name').html(file_data[1]);
			$('#data_panel').html(file_data[0] );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
		}
	}
</script>
</head>
<body onLoad="set_hotkey();set_auto_complete('subcon_material_receive');set_auto_complete_size('size_return');set_auto_complete_brand('brand_return');">
<div style="width:100%;">
	<? echo load_freeze_divs ("../",$permission);  ?>
    <fieldset style="width:1250px;">
    <legend>Sub-Contract Material Receive</legend>
        <form name="materialreceive_1" id="materialreceive_1" autocomplete="off">  
            <table  width="940" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td  width="130" height="" align="right">Receive ID</td>
                    <td  width="170">
                        <input class="text_boxes"  type="text" name="txt_receive_no" id="txt_receive_no" onDblClick="openmypage_rec_id('xx','Subcontract Receive')"  placeholder="Double Click" style="width:160px;" readonly/>
                    </td>
                    <td  width="130" align="right" class="must_entry_caption">Company Name </td>
                    <td width="170"> 
                        <? echo create_drop_down( "cbo_company_name", 172, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sub_contract_material_receive_controller', this.value, 'load_drop_down_location', 'location_td' ); load_drop_down( 'requires/sub_contract_material_receive_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data( this.value, 'load_report_button_setting','requires/sub_contract_material_receive_controller' );"); ?>
                    </td>
                    <td width="130" align="right">Location Name</td>
                    <td id="location_td">
                         <? echo create_drop_down( "cbo_location_name", 172, $blank_array,"", 1, "-- Select Location --", $selected, "" );?>
                    </td>
                </tr>
                <tr>
                    <td align="right" class="must_entry_caption">Party</td>
                    <td id="buyer_td">
                        <? echo create_drop_down( "cbo_party_name", 172, $blank_array,"", 1, "-- Select Party --", $selected, "" );?>
                    </td>
                    <td  width="130" class="must_entry_caption" align="right">Receive Challan</td>
                    <td  width="170">
                        <input class="text_boxes"  type="text" name="txt_receive_challan" id="txt_receive_challan" style="width:160px;" />  
                    </td>
                    <td  width="130" class="must_entry_caption" align="right">Receive Date</td>
                    <td>
                        <input type="text" name="txt_receive_date" id="txt_receive_date"  class="datepicker" style="width:160px" />             
                    </td>
                </tr>
            </table>
            <br/>
            <fieldset style="width:1550px;">
            <legend>Metarial Details Entry</legend>
            <table cellpadding="0" cellspacing="2" border="0" width="1510">
                <thead class="form_table_header">
                    <tr align="center" >
                        <th width="80" class="must_entry_caption">Order No</th>
                        <th width="100" class="must_entry_caption">Item Category</th>
                        <th width="140" class="must_entry_caption">Material Description</th>
                        <th width="60">Lot No.</th>
						<th width="60">Brand</th>
                        <th width="60">Color</th>
                        <th width="60">GMTS Size</th>
						<th width="70">Item Color</th>
                        <th width="70">Item Size</th>
                        <th width="40">GSM</th>                      
                        <th width="70">Stitch Length</th>
                        <th width="40">Grey Dia/ Width</th>
                        <th width="50">M/C Dia</th>
                        <th width="50">M/C Gauge</th>
                        <th width="40">Fin. Dia/ Width</th>
                        <th width="60">Dia UOM</th>
                        <th width="30">Roll /Bag</th>
                        <th width="70" class="must_entry_caption">Receive Qty</th>
                        <th width="60">Rate</th>
                        <th width="40" class="must_entry_caption">UOM</th>
                        <th width="30">Cone</th>
                        <th>Delete</th>
                    </tr>
                </thead> 
                <tr>
                    <td><input type="hidden" name="order_no_id" id="order_no_id">
                        <input class="text_boxes" name="txt_order_no" id="txt_order_no" type="text" style="width:78px" main_process_id="" placeholder="Browse" onDblClick="job_search_popup('requires/sub_contract_material_receive_controller.php?action=job_popup','Order Selection Form')" readonly />
                    </td>
                    <td>
                        <? echo create_drop_down( "cbo_item_category", 100, $item_category,"", 1, "--Select Item--",0,"change_uom(this.value);hide_material_description(this.value);", "","1,2,3,4,13,14,30" );?>
                    </td>
                    <td>
                        <input type="text" id="txt_material_description" name="txt_material_description" class="text_boxes" style="width:140px" title="Maximum 200 Character" >
                        <input type="hidden" name="txt_item_id" id="txt_item_id">
                        <div id="td_gmts_material">
                        <? 
                        echo create_drop_down( "cbo_gmts_material_description", 152, $garments_item,"", 1, "--Select Item--",0,"", "","" );
                        ?>
                        </div>
                    </td>
                    <td>
                    	<input type="text" id="txt_lot_no" name="txt_lot_no" class="text_boxes txt_size" style="width:60px"/>
                    </td>
					<td>
                    	<input type="text" id="txt_brand" name="txt_brand" class="text_boxes txtbrand" style="width:60px"/>
                    </td>
                    <td id="color_td">
                        <!-- <input name="txt_color" id="txt_color" class="text_boxes" type="text"  style="width:60px"  /> -->

	                <?
	                   echo create_drop_down( "txt_color", 60, $blank_array,"", 1, "-Select-", 0,"","","" );
	                ?>
                    </td>
                    <td>
                    	<input type="text" id="txtsize" name="txtsize" class="text_boxes txt_size" style="width:60px"/>
                    </td>
					<td>
                        <input name="txt_acc_item_colour" id="txt_acc_item_colour" class="text_boxes" type="text"  style="width:70px" value=""/>
                    </td>
					<td>
                        <input name="txt_acc_item_size" id="txt_acc_item_size" class="text_boxes" type="text"  style="width:70px" value=""/>
                    </td>
                    <td>
                        <input name="txt_gsm" id="txt_gsm" class="text_boxes_numeric" type="text"  style="width:40px" value=""/>
                    </td>                   
					<td>
                        <input name="txt_stitch_length" id="txt_stitch_length" class="text_boxes" type="text"  style="width:70px" value=""/>
                    </td>
                    <td>
                        <input name="txt_grey_dia" id="txt_grey_dia" class="text_boxes" type="text"  style="width:40px" />
                    </td>
                     <td>
                        <input name="txt_mc_dia" id="txt_mc_dia" class="text_boxes" type="text"  style="width:50px" />
                    </td>
                     <td>
                        <input name="txt_mc_gauge" id="txt_mc_gauge" class="text_boxes" type="text"  style="width:50px" />
                    </td>
                    <td>
                        <input name="txt_fin_dia" id="txt_fin_dia" class="text_boxes" type="text"  style="width:40px" />
                    </td>
                    <td>
                    	<? echo create_drop_down( "cbo_dia_uom",60, $unit_of_measurement,"", 1, "-UOM-",0,"", "","25,29" );?>
                        <!--<input name="txt_dia_uom" id="txt_dia_uom" class="text_boxes" type="text"  style="width:40px" />-->
                    </td>
                    <td>
                        <input name="txt_roll" id="txt_roll" class="text_boxes_numeric" type="text"  style="width:30px" />
                    </td>
                    <td>
                        <input name="txt_receive_quantity" previous_rec_qty="" id="txt_receive_quantity" class="text_boxes_numeric" type="text"  style="width:65px" onBlur="fnc_rec_qty_validation(this.value);" />
                    </td>
                    <td>
                        <input name="txt_rec_rate" id="txt_rec_rate" class="text_boxes_numeric" type="text"  style="width:55px" />
                    </td>
                     <td>
                        <? echo create_drop_down( "cbo_uom",50, $unit_of_measurement,"", 1, "-Select-",12,"", "","" );?>
                    </td>
                    <td>
                        <input name="txt_cone" id="txt_cone" class="text_boxes_numeric" type="text"  style="width:30px" disabled />
                    </td>
                    <td>
                        <? echo create_drop_down( "cbo_status", 60, $yes_no, "", 0, "",2,'','' ); ?>
                    </td>
                </tr>
                <tr id="tr_used_yarn_details" style="display: none;">
                    <th>Used Yarn Details</th>
                    <td colspan="2">
                        <textarea name="txt_used_yarn_details" id="txt_used_yarn_details" class="text_boxes" type="text"  style="width:240px"></textarea>
<!--                        <input name="txt_used_yarn_details" id="txt_used_yarn_details" class="text_boxes" type="text"  style="width:150px"  />-->
                    </td>
                </tr>
             </table>
             </fieldset>  
             <table width="1550" cellspacing="2" cellpadding="0" border="0">
                 <tr>
                      <td><input type="hidden" name="update_id" id="update_id"></td>
                      <td><input type="hidden" name="update_id2" id="update_id2"></td>
                      <td><input type="hidden" name="delete_allowed" id="delete_allowed"></td>
                      <td><input type="hidden" name="batch_no" id="batch_no"></td>
                 </tr>
                 <tr>
                    <td align="center" colspan="23" valign="middle" class="button_container">
                        <? echo load_submit_buttons($permission, "fnc_material_receive", 0,0,"reset_form('materialreceive_1','receive_list_view','','cbo_status,2', 'disable_enable_fields(\'cbo_company_name\',0)')",1); ?>
                    </td>
                 </tr>  
				 <tr>
                        	<td align="center" colspan="6" height="10" id="report_button">
                            <input type="button" value="Print" onClick="generate_report('show_material_receive_report')"  style="width:80px;display:none" name="print_button" id="print_button" class="formbutton"  />
							</td>
                 </tr>  		
                 <br/>
                 <tr align="center">
                    <td colspan="19" id="receive_list_view"> </td>	
                </tr>               
          </table>
        </form>
    </fieldset>
   
    <div id="order_list_view" style="width:350px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
</div>
<div style="display:none" id="data_panel"></div>
</body>
<script language="javascript" type="text/javascript">  
/*	function SetFocus(txt_order_no)  
	{  
	   document.getElementById(txt_order_no).focus();  
	}  
*/</script> 
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>