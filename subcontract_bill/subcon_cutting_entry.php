<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create subcon cutting Entry
				
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	26-05-2013
Updated by 		: 	Add 'print' button by Samiur	
Update date		:   05-09-2020
Oracle Convert 	:	Kausar		
Convert date	: 	25-05-2014	   
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
echo load_html_head_contents("Subcon Cutting Entry info","../", 1, 1, '','','');
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function openmypage(page_link,title)
	{
		var company_name=$("#cbo_company_name").val();
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=420px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("hidden_mst_id");//po id
			var theemailItem=this.contentDoc.getElementById("hidden_grmtItem_id");
			//var ret_id=theemail.value.split("_");
			var po_id=theemail.value;
			//alert (theemailItem.value);
			childFormReset();

			var item_id=theemailItem.value;
			//var po_qnty=this.contentDoc.getElementById("hidden_po_qnty").value;
			//var plan_qnty=this.contentDoc.getElementById("hidden_plancut_qnty").value; 
			if (po_id!="")
			{
				freeze_window(5);
				$("#cbo_item_id").val(item_id);
				//$("#txt_order_qty").val(po_qnty);
				//$("#txt_plancut_qty").val(plan_qnty);
				
				get_php_form_data(po_id+'**'+item_id, "populate_data_from_search_popup", "requires/subcon_cutting_entry_controller" );
				var variableSettings=$('#sewing_production_variable').val();
				
				var styleOrOrderWisw=$('#styleOrOrderWisw').val();
				if(variableSettings!=1)
					get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw, "color_and_size_level", "requires/subcon_cutting_entry_controller" ); 
				else
					$("#txt_cutting_qty").removeAttr("readonly");
				show_list_view(po_id+'**'+item_id,'show_dtls_listview','sub_cutting_entry_list_view','requires/subcon_cutting_entry_controller','');			
				set_button_status(0, permission, 'fnc_subcon_cutting_entry',1);
				release_freezing();
			}
		}
	}
    <!--=======================BUTTON FUNCTION==================-->   
	function fnc_subcon_cutting_entry(operation)
	{
		if(operation==5)
		{
			var master_id = $('.Checkbox:checked').map(function() {return this.value;}).get().join(',');	
			// alert(master_id);
			// var master_id=$("#txt_mst_id").val();
			if(master_id=="" || master_id==0)	 
			{
				alert("Select One Item Please");
				return;
			}

			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+master_id+'*'+report_title, "sewing_cutting_entry", "requires/subcon_cutting_entry_controller" );
			return;
		}
		else
		{

		
		if( form_validation('cbo_company_name*txt_order_no*txt_cutting_date*txt_cutting_qty','Company Name*Order Number*Cutting Date*Cutting Quntity')==false )
		{
			return;
		}
		var sewing_production_variable = $("#sewing_production_variable").val();
		var total_row = $("#txt_total_row_count").val();
		var tot_span = $("#txt_span_count").val();
		var tot_row =$("#table_"+j+" tr").length;
		
		if(sewing_production_variable==2)//color level
		{
			var total_row = $("#txt_total_row_count").val();
			var data1="";
			var i=1;
			for(i=1; i<=total_row; i++)
			{
				// data1+=get_submitted_data_string('txt_colo_size_mst_id_'+i+'*colSize_'+i,"../");
				data1+='&txt_colo_size_mst_id_'+i+'='+$('#txt_colo_size_mst_id_'+i).val()+'&colSize_'+i+'='+$('#colSize_'+i).val();
			}
		}
		else if(sewing_production_variable==3)//color and size level
		{
			var tot_span = $("#txt_span_count").val();
			var data2="";
			var k=1;
			var z=1;
			var j=1;
			var tot_row_count="";
			for(k=1; k<=tot_span; k++)
			{
				var tot_row =$("#table_"+j+" tr").length;
				for(i=1; i<=tot_row; i++)
				{
					// data2+=get_submitted_data_string('txt_colo_size_mst_id'+k+'___'+i+'*colSize'+k+'___'+i,"../");
					data2+='&txt_colo_size_mst_id'+k+'___'+i+'='+$('#txt_colo_size_mst_id'+k+'___'+i).val()+'&colSize'+k+'___'+i+'='+$('#colSize'+k+'___'+i).val();
				}
				if(tot_row_count=="")tot_row_count=tot_row; else tot_row_count=tot_row_count+","+tot_row;
				j++;
			}
		}
		freeze_window(operation);	
		var data="action=save_update_delete&operation="+operation+"&tot_row="+tot_row_count+"&tot_span="+tot_span+"&total_row="+total_row+get_submitted_data_string('cbo_company_name*sewing_production_variable*styleOrOrderWisw*txt_order_no*hidden_po_break_down_id*cbo_item_id*process_id*txt_order_qty*cbo_party_name*txt_style_no*txt_plancut_qty*cbo_location*cbo_floor*txt_cutting_date*txt_cutting_qty*hidden_colorSizeID*txt_reject_qty*txt_table_no*txt_remark*txt_cumul_cutting*txt_yet_cut*txt_mst_id',"../");
	 
		var sewing_production_variable = $("#sewing_production_variable").val();
		if(sewing_production_variable==2)//color level
		{
			data=data+data1;
		}
		else if(sewing_production_variable==3)//color and size level 
		{
			data=data+data2;
		}
		http.open("POST","requires/subcon_cutting_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_subcon_cutting_entry_respone;
	}
	}
	
	function fnc_subcon_cutting_entry_respone()
	{
		if(http.readyState == 4)
		{
			//alert (http.responseText); //return;
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]=="167")
			{
				alert("Delete Restricted!! Data Found in Next Process");
				release_freezing();
				return;
			}

			if(reponse[0]=="2")
			{
				window.location.reload();
				release_freezing();
				return;
			}
			show_msg(reponse[0]);
			
			var po_id = $("#hidden_po_break_down_id").val();
			var variableSettings=$('#sewing_production_variable').val();
			var item_id = $("#cbo_item_id").val();
			var styleOrOrderWisw=$('#styleOrOrderWisw').val();
			
			//alert (po_id);
			show_list_view(po_id+'**'+item_id+'**'+variableSettings,'show_dtls_listview','sub_cutting_entry_list_view','requires/subcon_cutting_entry_controller','');
			reset_form('','','txt_cutting_qty*txt_reject_qty*txt_table_no*txt_remark','txt_cutting_date,<? echo date("d-m-Y"); ?>','');
		
			get_php_form_data(po_id+'**'+item_id+'**'+variableSettings, "populate_data_from_search_popup", "requires/subcon_cutting_entry_controller" );
			if(variableSettings!=1) { 
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw, "color_and_size_level", "requires/subcon_cutting_entry_controller" ); 
			}
			else
			{
				$("#txt_cutting_qty").removeAttr("readonly");
			}
			
			set_button_status(0, permission, 'fnc_subcon_cutting_entry',1);
			release_freezing();
		}
	}
	
	function childFormReset()
	{
		reset_form('','sub_cutting_entry_list_view','txt_cutting_qty*txt_reject_qty*txt_table_no*txt_remark','txt_cutting_date,<? echo date("d-m-Y"); ?>','');
		$('#txt_cutting_qty').attr('placeholder','');//placeholder value initilize
		$('#txt_cumul_cutting').attr('placeholder','');//placeholder value initilize
		$('#txt_yet_cut').attr('placeholder','');//placeholder value initilize
		$("#sub_cutting_entry_list_view").html('');
		$("#breakdown_td_id").html('');
	} 
	
	function fn_colorlevel_total(index) //for color level
	{
		var tot_row = $("#txt_total_row_count").val();
		var total_qnty="";
		for(var i=1; i<=tot_row; i++)
		{
			total_qnty=total_qnty*1+$("#colSize_"+i).val()*1;
		}
		document.getElementById('txt_cutting_qty').value=total_qnty;
		document.getElementById('total_color').value=total_qnty;
	}
	
	function sum_qnty(id,vid)
	{
		var filed_value = $("#colSize"+id+"___"+vid).val();
		var placeholder_value = $("#colSize"+id+"___"+vid).attr('placeholder');
		//alert (placeholder_value)
		if(filed_value*1 > placeholder_value*1)
		{
			if( confirm("Qnty Excceded by"+(placeholder_value-filed_value)) )	
				void(0);
			else
			{
				$("#colSize"+id+"___"+vid).val('');
			}
		}
		
		var totalRow = $("#table_"+id+" tr").length;
		//alert (totalRow);
		var total=0;
		for(var i=1; i<=totalRow; i++)
		{
			total=(total*1)+($("#colSize"+id+"___"+i).val()*1);
		}
		$("#total_"+id).html(total);
		var totalVal = 0;
		$("input[name=colSize]").each(function(index, element) {
			totalVal += ( $(this).val() )*1;
		});
		$("#txt_cutting_qty").val(totalVal);
	}

</script>
<script type="text/javascript">
	function toggle(source) {
	  checkboxes = document.getElementsByName('master_id');
	  for(var i=0, n=checkboxes.length;i<n;i++) {
	    checkboxes[i].checked = source.checked;
	  }
	}
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
    <div style="width:850px;" align="center"><? echo load_freeze_divs ("../",$permission); ?></div>
<!--=======================CUTTING ENTRY==================-->
    <fieldset style="width:800px"> 
        <legend>Cutting Entry</legend>
        <form name="cuttingentry_1" id="cuttingentry_1" action=""  autocomplete="off">
            <fieldset>
            <table width="100%">
                <tr>
                    <td width="110" class="must_entry_caption">Company </td>
                    <td width="140">
                        <?
                            echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 and core_business not in(3)  $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/subcon_cutting_entry_controller', this.value, 'load_drop_down_location', 'location_td');load_drop_down( 'requires/subcon_cutting_entry_controller', this.value+'_'+document.getElementById('cbo_location').value, 'load_drop_down_floor', 'floor_td' );load_drop_down( 'requires/subcon_cutting_entry_controller', this.value, 'load_drop_down_party', 'party_td');get_php_form_data(this.value,'load_variable_settings','requires/subcon_cutting_entry_controller');",0 );	
                        ?>
                        <input type="hidden" id="sewing_production_variable" />
                        <input type="hidden" id="styleOrOrderWisw" />  
                    </td>
                    <td width="110" class="must_entry_caption">Order No</td>
                    <td width="140">
                        <input name="txt_order_no" id="txt_order_no" placeholder="Double Click to Search" onDblClick="openmypage('requires/subcon_cutting_entry_controller.php?action=order_popup&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value,'Order Search')" class="text_boxes" style="width:130px" readonly />
                        <input type="hidden" id="hidden_po_break_down_id" value="" />	
                        <input type="hidden" id="process_id" value="" />
                    </td>
                    <td width="110">Order Qty</td>
                    <td width="140">
                        <input name="txt_order_qty" id="txt_order_qty" class="text_boxes_numeric"  style="width:130px" disabled />
                    </td>
                </tr>
                <tr>
                    <td>Party Name</td>
                    <td id="party_td">
                        <?
                            echo create_drop_down( "cbo_party_name", 140, $blank_array,"", 1, "-- Select Party --", $selected, "",1,"","","","",4);
                        ?> 
                    </td>
                    <td>Style</td>
                    <td>
                        <input type="text" name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:130px" disabled />
                    </td>
                    <td>Plan Cut Qty</td>
                    <td>
                        <input name="txt_plancut_qty" id="txt_plancut_qty" class="text_boxes_numeric"  style="width:130px" disabled />
                    </td>
                </tr>
                <tr>
                    <td>Item Name</td>
                    <td>
                        <?
                            echo create_drop_down( "cbo_item_id", 140, $garments_item,"", 1, "-- Select Item --", $selected, "",1 );	
                        ?> 
                    </td>
                    <td>Location</td>
                    <td id="location_td">
                        <?
                            echo create_drop_down( "cbo_location", 140, $blank_array,"", 1, "-- Select Location --", $selected, "",0 );	
                        ?> 
                    </td>
                    <td>Floor</td>
                    <td id="floor_td">
                        <?
                            echo create_drop_down( "cbo_floor", 140, $blank_array,"", 1, "-- Select Floor --", $selected, "",0 );	
                        ?> 
                    </td>
                </tr>
            </table>
            </fieldset>
            <table cellpadding="0" cellspacing="1" width="100%">
            	<tr><td colspan="5">&nbsp;</td></tr>
                <tr>
                    <td width="30%" valign="top">
                     <fieldset>
                     <legend>New Entry</legend>
                        <table  cellpadding="0" cellspacing="2" width="100%">
                            <tr>
                                <td width="120" class="must_entry_caption">Cutting Date</td>
                                <td width="110">
                                    <input class="datepicker" type="text" style="width:100px;" name="txt_cutting_date" id="txt_cutting_date"  value="<? echo date("d-m-Y")?>" />
                                </td>
                            </tr>
                            <tr>
                                <td width="" class="must_entry_caption">Cutting Qty</td> 
                                <td width=""> 
                                    <input type="text" name="txt_cutting_qty" id="txt_cutting_qty" class="text_boxes_numeric" style="width:100px" readonly />
                                    <input type="hidden" id="hidden_break_down_html"  value="" />
                                    <input type="hidden" id="hidden_colorSizeID"  value="" />
                                </td>
                            </tr>
                            <tr>
                                <td>Reject Qty</td>
                                <td>
                                    <input name="txt_reject_qty" id="txt_reject_qty" class="text_boxes_numeric" type="text" style="width:100px;"  />
                                </td>
                            </tr>
                            <tr>
                                <td>Table No.</td> 
                                <td>
                                    <input name="txt_table_no" id="txt_table_no" class="text_boxes_numeric"  style="width:100px" />
                                </td>
                            </tr>
                            <tr>
                                <td>Remarks</td> 
                                <td> 
                                    <input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:100px"  />
                                </td>
                            </tr>
                        </table>
                     </fieldset>
                    </td>
                    <td width="1%" valign="top"></td>
                    <td width="23%" valign="top">
                    <fieldset>
                    <legend>Display</legend>
                        <table  cellpadding="0" cellspacing="2" width="100%" >
                            <tr>
                                <td width="120">Cuml. Cutt.</td>
                                <td>
                                    <input type="text" name="txt_cumul_cutting" id="txt_cumul_cutting" class="text_boxes_numeric" style="width:80px" disabled />
                                </td>
                            </tr>
                            <tr>
                                <td width="120">Yet to Cut</td>
                                <td>
                                    <input type="text" name="txt_yet_cut" id="txt_yet_cut" class="text_boxes_numeric" style="width:80px" disabled  />
                                </td>
                            </tr>
                        </table>
                    </fieldset>	
                    </td>
                    <td width="1%" valign="top"></td>                            
                    <td width="45%" valign="top">
                        <div style="max-height:250px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
                    </td>                          
                </tr>
                <tr>
                    <td align="center" colspan="5" valign="middle" class="button_container">
                        <?
							$date=date('d-m-Y');
                            echo load_submit_buttons( $permission, "fnc_subcon_cutting_entry", 0,0 ,"reset_form('cuttingentry_1','','','txt_cutting_date,".$date."','childFormReset()');$('#txt_cutting_qty').attr('placeholder','');",1);
                        ?>
                        <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly >
                        <input value="Print" name="print2" onclick="fnc_subcon_cutting_entry(5)" style="width:80px" id="Print2" class="formbutton" type="button">	
                    <td>&nbsp;</td>					
                </tr>
            </table>
        </form>
    </fieldset>
    <div style="width:800px; margin-top:5px;" id="sub_cutting_entry_list_view" align="center"></div>
</div>
</body> 
<script src="../includes/functions_bottom.js" type="text/javascript"></script>  
</html>