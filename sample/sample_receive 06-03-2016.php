<?
/*-------------------------------------------- Comments 
Version          : 
Purpose			 : 
Functionality	 :	
JS Functions	 :
Created by		 : Monir Hossain
Creation date 	 : 17/01/2016
Requirment Client: 
Requirment By    : 
Requirment type  : 
Requirment       : 
Affected page    : 
Affected Code    :              
DB Script        : 
Updated by 		 : 
Update date		 : 
QC Performed BY	 :		
QC Date			 :	
Comments		 : 
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("sample receive", "../", 1, 1,$unicode,'','');
?>	
<script>
    if ($('#index_page', window.parent.document).val() != 1)
        window.location.href = "../../logout.php";

    var permission = '<? echo $permission; ?>';

    function fnc_sample_receive(operation)
    {
		if(operation==4)
		{
			fnc_print_bundle()
		}
		else
		{
			if (form_validation('receive_date*cbo_fabric_cat*txt_item*txt_style*txt_designer*txt_qty','Date*Category*style*Item*Designer*Quantity') == false)
			{
				return;
			}
			
			var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('receive_date*cbo_fabric_cat*txt_item*txt_style*cbo_fabric_source*cbo_supplier_source*txt_designer*txt_qty*cbo_fabric_natu*txt_construction*txt_Composition*txt_gsm*cbo_yarn_count*cbo_yarn_type*cbo_status*update_id*color_qty_breakdown', "../");
			//alert(data);
			freeze_window(operation);
			http.open("POST", "requires/sample_receive_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_sample_receieve_response;
		}
	}
	
    function fnc_sample_receieve_response()
    {
        if (http.readyState == 4)
        {
          //alert(http.responseText);
            var response = trim(http.responseText).split('**');
			//alert (reponse);
			show_msg(response[0]);
			release_freezing();
			
			if (response[0] == 0 || response[0] == 1)
			{
				//alert(reponse[0]);
				$('#txt_sys_id').val(response[1]);
				$('#update_id').val(response[1]);
				get_php_form_data(response[1], 'load_php_data_to_form', 'requires/sample_receive_controller');
				set_button_status(1, permission, 'fnc_sample_receive',1);
			}
			
			if(response[0] == 1)
			{
				reset_form('samplereceive_1', '', '');
			}
        }
    }

    function sample_receive_pop()
    {
		var page_link = 'requires/sample_receive_controller.php?action=sample_re_popup&color_qty_breakdown='+$("#color_qty_breakdown").val()+'&update_id='+$("#update_id").val();
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Item......   Category.....', 'width=1050px, height=280px, center=1, resize=0, scrolling=0', '');
        emailwindow.onclose = function()
        {
            var total_qty = this.contentDoc.getElementById("hidden_total_qty").value;
			var save_string = this.contentDoc.getElementById("hidden_process_string").value;
            $("#txt_qty").val(total_qty);
			$("#color_qty_breakdown").val(save_string);
        }
    }
	
	
	function sample_list_pop()
    {
		var page_link = 'requires/sample_receive_controller.php?action=search_list_view';
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, 'sample List View', 'width=900px, height=280px, center=1, resize=0, scrolling=0', '');
        emailwindow.onclose = function()
        {
			var update_id = this.contentDoc.getElementById("update_id");
			// alert(update_id.value);
			document.getElementById('txt_sys_id').value = update_id.value;
			get_php_form_data(update_id.value, 'load_php_data_to_form', 'requires/sample_receive_controller');
			
        }
    }

	function fnc_print_bundle()
	{
	   var receive_date = $('#receive_date').val();
	   var update_id = $('#update_id').val();	
	   print_report(update_id,"sample_receive_print", "requires/sample_receive_controller")
			
	}
	
	function chanfab()
	{
		var i= $('#cbo_fabric_source').val();
		if(i==1)
		{
			$('#cbo_supplier_source').attr('disabled',true);
		}
		else
		{
			 $('#cbo_supplier_source').attr('disabled',false);
			 if (form_validation('cbo_supplier_source','Supplier') == false)
				{
					return;
				}
		}
	

	}
		
	var str_construction = [<? echo substr(return_library_autocomplete( "select distinct(construction) from sample_receive_mst","construction" ), 0, -1); ?>];
	var str_composition = [<? echo substr(return_library_autocomplete( "select distinct(composition) from sample_receive_mst","composition" ), 0, -1); ?>];
	var str_style_ref = [<? echo substr(return_library_autocomplete( "select distinct(style_ref) from sample_receive_mst","style_ref" ), 0, -1); ?>];
	var str_designer = [<? echo substr(return_library_autocomplete( "select distinct(designer) from sample_receive_mst","designer" ), 0, -1); ?>];
	//alert(str_construction);
	function add_auto_complete(j)
	{
		 $("#txt_construction").autocomplete({
			 source: str_construction
		  });
		  
		  $("#txt_Composition").autocomplete({
			 source: str_composition
		  });
		  
		   $("#txt_style").autocomplete({
			 source: str_style_ref
		  });
		  
		  $("#txt_designer").autocomplete({
			 source: str_designer
		  });
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
<div align="center">
<? echo load_freeze_divs ("../",$permission); ?>
<fieldset style="width:850px;">
    <form name="samplereceive_1" id="samplereceive_1" enctype="multipart/form-data">	
        <table cellpadding="0" cellspacing="1" width="650" align="center" >
            <tr>
                <td>
                     <fieldset style="width:350px; float:left;">
                        <legend>Item info</legend>
                        <table cellpadding="0" cellspacing="1" width="300" align="center" id="sample_re">
                            <tr>
                                <td align="right" width="100" class="must_entry_caption">Receive Date</td>
                                <td width='100'>
                                    <input type="text" name="receive_date" id="receive_date" class="datepicker" style="width:200px" placeholder="Date Picker"/>	
                                    <input type="hidden" name="update_id" id="update_id"  />					
                                </td>
                            </tr>	
                            <tr>
                                <td  align="right" class="must_entry_caption">Category</td>
                                <td valign="top">
                                    <? 
                                    $sample_category=array(1=>"Basic",2=>"Casual Wear",3=>"Dress Up",4=>"Holiday",5=>"Occasion Wear",6=>
                                    "Sport Wear",7=>"Work Wear");
                                    echo create_drop_down( "cbo_fabric_cat", 212, $sample_category,"", 1, "-- Select --", 0,$onchange_func,
                                    $is_disabled, ""); 		
                                    ?>	
                                </td>
                            </tr>
                            <tr>
                                <td  align="right" class="must_entry_caption">Style Ref.</td>
                                <td valign="top">
                                    <input type="text"  name="txt_style" id="txt_style" class="text_boxes" style="width:200px" 
                                    placeholder="Write">
                                </td>			
                            </tr>
                            <tr>
                                <td  align="right" class="must_entry_caption">Item</td>
                                <td valign="top">
                                    <input type="text"  name="txt_item" id="txt_item" class="text_boxes" style="width:200px" placeholder="Write">
                                </td>			
                            </tr>
                            <tr>
                                <td  align="right" class="must_entry_caption">Source</td>
                                <td valign="top">
                                    <? 
                                    $sample_source=array(1=>"Product development",2=>"Factory",);
                                    echo create_drop_down( "cbo_fabric_source", 212, $sample_source,"", 1, "-- Select --", "","chanfab();", "", "");		
                                    ?>
                                </td>			
                            </tr>
                            <tr>
                                <td  align="right" class="must_entry_caption">Supplier</td>
                                <td valign="top">
                                    <? 
                                    $supplier="select id,company_name from lib_company";
                                    echo create_drop_down( "cbo_supplier_source", 212,$supplier,"id,company_name", 1, "-- Select --", "","", "", "");		
                                    ?>
                                </td>			
                            </tr>
                            <tr>
                                <td  align="right" class="must_entry_caption">Designer</td>
                                <td valign="top">
                                    <input type="text" name="txt_designer" id="txt_designer" class="text_boxes" style="width:200px" placeholder="Write">
                                </td>			
                            </tr>
                            <tr>
                                <td  align="right" class="must_entry_caption">Qty</td>
                                <td valign="top">
                                    <input type="text" name="txt_qty" id="txt_qty" onDblClick="sample_receive_pop()" placeholder="Double Click" class="text_boxes_numeric" style="width: 200px" readonly >
                                     <input type="text" name="color_qty_breakdown" id="color_qty_breakdown"  />
                                </td>			
                            </tr>
                            <tr>
                            <td></td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
                <td>
                    <fieldset style="height:200px; float:left;" >
                        <legend>Material and Value Addition</legend>
                        <table cellpadding="0" cellspacing="1" width="300"  align="center">
                            <tr>
                                <td align="right" >Fabric Nature</td>
                                <td colspan="3">
                                    <? 
                                    echo create_drop_down( "cbo_fabric_natu", 212, $item_category,"", 1, "-- Select --", 1,$onchange_func,$is_disabled, '2,3');		
                                    ?>						
                                </td>
                            </tr>	
                            <tr>
                                <td  align="right">Construction</td>
                                <td>
                                    <input type="text" id="txt_construction" name="txt_construction" class="text_boxes" style="width:200px" placeholder="Write" onFocus="add_auto_complete(1)" />
                                </td>
                            </tr>
                            <tr>
                                <td  align="right">Composition</td>
                                <td >
                                    <input type="text" id="txt_Composition" name="txt_Composition" class="text_boxes" style="width:200px" placeholder="Write" onFocus="add_auto_complete(1)" />
                                </td>			
                            </tr>
                            <tr>
                                <td  align="right">GSM</td>
                                <td valign="top">
                                    <input type="text" id="txt_gsm" name="txt_gsm" class="text_boxes_numeric" style="width:200px"  placeholder="Write">
                                </td>			
                            </tr>
                            <tr>
                                <td  align="right">Yarn Count</td>
                                <td valign="top">
                                   <? 
                                   $yarn_count="select id,yarn_count from lib_yarn_count";
                                    echo create_drop_down( "cbo_yarn_count", 212, $yarn_count,"id,yarn_count", 1, "-- Select --","","", "","");		
                                    ?>
                                </td>	
                            </tr>
                            <tr>
                                <td  align="right">Yarn Type</td>
                                <td valign="top">
                                    <? 
                                    echo create_drop_down( "cbo_yarn_type", 212, $yarn_type,"", 1, "-- Select --","","", "", "");		
                                    ?>
                                </td>	
                            </tr>
                            <tr>
                                <td  align="right">Status</td>
                                <td valign="top">
                                    <? 
                                    echo create_drop_down( "cbo_status", 212, $row_status,"", 1, "-- Select --",'',$onchange_func,$is_disabled, "");		
                                    ?>
                                </td>	
                            </tr>
                            
                            <tr>
                                <td  align="right"> <strong>System Id:</strong></td>
                                <td valign="top">
                                  
                                    <input type="text" id="txt_sys_id" name="txt_sys_id" class="text_boxes" style="width:200px" placeholder="Browse" value="" onClick="sample_list_pop();" readonly >
                                </td>	
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="button_container" align="center">
                    <? 
                    echo load_submit_buttons( $permission, "fnc_sample_receive", 0,1 ,"reset_form('samplereceive_1','','',0)");
                    ?>
                </td>
            </tr>
        </table>
	</form>	
</fieldset>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>