<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create for Yarn Bag Sticker
					
Functionality	:	
				

JS Functions	:

Created by		:	Ashraful Islam 
Creation date 	: 	04-11-2016
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
echo load_html_head_contents("Pro Forma Invoice", "../../", 1, 1,'','',''); 
?> 	

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  

var permission='<? echo $permission; ?>';
var str_color = [<? echo substr(return_library_autocomplete( "select distinct(color_name) from lib_color", "color_name"  ), 0, -1); ?>];
var str_size = [<? echo substr(return_library_autocomplete( "select distinct(size_name) from lib_size", "size_name"  ), 0, -1); ?>];
var str_composition = [<? echo substr(return_library_autocomplete( "select distinct(fabric_composition) from com_pi_item_details", "fabric_composition"  ), 0, -1); ?>];
var str_construction = [<? echo substr(return_library_autocomplete( "select distinct(fabric_construction) from com_pi_item_details", "fabric_construction"  ), 0, -1); ?>];
var str_dia_width = [<? echo substr(return_library_autocomplete( "select distinct(dia_width) from com_pi_item_details", "dia_width"  ), 0, -1); ?>];





function add_auto_complete(i)
{
	 $("#colorName_"+i).autocomplete({
		 source: str_color
	  });
	  $("#itemColor_"+i).autocomplete({
		 source: str_color
		});
	  $("#sizeName_"+i).autocomplete({
		 source: str_size
	  });
	  $("#composition_"+i).autocomplete({
		 source: str_composition
	  });
	  $("#construction_"+i).autocomplete({
		 source: str_construction
	  });
	  $("#diawidth_"+i).autocomplete({
		 source: str_dia_width
	  });
}
/************************************************************************************************************/
function add_break_down_tr( i )
{ 
	var row_num=$('#tbl_pi_item tbody tr').length;
	row_num++;
	var clone= $("#row_"+i).clone();
	clone.attr({
		id: "row_"+ row_num,
	});
	
	clone.find("input,select").each(function(){
		  
	$(this).attr({ 
	  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
	  'name': function(_, name) { return name },
	  'value': function(_, value) { return value }              
	});
	 
	}).end();
		
	$("#row_"+i).after(clone);
	$('#lotName_'+row_num).val("");
	$('#noOfBag_'+row_num).val("");
	$('#conWgt_'+row_num).val("");
	$('#bagCon_'+row_num).val("");
	$('#bagWgt_'+row_num).val("");
	$('#updateIdDtls_'+row_num).val("");
	
	$('#conWgt_'+row_num).removeAttr("onKeyUp").attr("onKeyUp","calculate_amount("+row_num+")");
	$('#bagCon_'+row_num).removeAttr("onKeyUp").attr("onKeyUp","calculate_amount("+row_num+")");
	
	$('#increase_'+row_num).removeAttr("onclick").attr("onclick","add_break_down_tr("+row_num+");");
	//$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
	set_all_onclick();
	
}	
	

function fn_deleteRow(rowNo) 
{ 
	var numRow = $('#tbl_pi_item tbody tr').length; 
	alert(numRow);
	if(numRow!=1)
	{
		var updateIdDtls=$('#updateIdDtls_'+rowNo).val();
		var txt_deleted_id=$('#txt_deleted_id').val();
		var selected_id='';
	
		if(updateIdDtls!='')
		{
			if(txt_deleted_id=='') selected_id=updateIdDtls; else selected_id=txt_deleted_id+','+updateIdDtls;
			$('#txt_deleted_id').val( selected_id );
		}
		$("#row_"+rowNo).remove();
		//calculate_total_amount(1);
	}
	else
	{
		return false;
	}
}
	
	 
function openmypage()
{
	var title = 'PI Selection Form';
	var cbo_receive_basis = $('#cbo_receive_basis').val();	
	var page_link = 'requires/yarn_bag_sticker_controller.php?action=pi_popup&cbo_receive_basis='+cbo_receive_basis;
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=450px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var theemail=this.contentDoc.getElementById("txt_selected_pi_id") //Access form field with id="emailfield"
		
		if(theemail.value!="")
		{
			freeze_window(5);
			var pi_data=(theemail.value).split("_");
			$('#pi_number').val(pi_data[1]);
			$('#hidden_pi_id').val(pi_data[0]);
			$('#hidden_company_id').val(pi_data[2]);
			
			show_list_view(pi_data[0]+"_"+cbo_receive_basis, 'pi_details', 'pi_details_container', 'requires/yarn_bag_sticker_controller', '' ) ;
			set_button_status(0, permission, 'fnc_pi_item_details',1);
			var row_num=$('#tbl_pi_item tbody tr').length;
			$("#txt_tot_row").val(row_num);
			show_list_view(pi_data[0]+"_"+cbo_receive_basis, 'pi_details_listview', 'pi_listview_container', 'requires/yarn_bag_sticker_controller', '' ) ;
		
			set_all_onclick();
			release_freezing();
		} 
	}
}

function calculate_amount(i)
{
	//var ddd={ dec_type:5, comma:0, currency:''}
	//math_operation( 'bagWgt_'+i, 'conWgt_'+i+'*bagCon_'+i, '*','',ddd);
	//calculate_total_amount(1);
	var con_weight=$("#conWgt_"+i).val()*1;
	var bag_con=$("#bagCon_"+i).val()*1;
	$("#bagWgt_"+i).val( number_format (con_weight*bag_con, 2,'.' , ""));
}

function change_receive_vasis(value)
{
	var caption_value='';
	if(value==1)
	{
		caption_value="PI";
	}
	else if(value==2)
	{
		caption_value="Yarn Parchach order";
	}
	else if(value==3)
	{
		caption_value="Yarn Dyeing Work Order";
	}
	else if(value==4)
	{
		caption_value="Yarn Dyeing Work Order Without Order";
	}
	$("#receive_basis_caption").text(caption_value);
	$("#receive_basis_caption").css("color","blue");
	$('#hidden_pi_id').val('');
	$('#pi_number').val('');
	//alert(value);
}



function fnc_pi_item_details( operation )
{
	
	var cbo_receive_basis = $('#cbo_receive_basis').val();
	var hidden_pi_id = $('#hidden_pi_id').val();
	var pi_number = $('#pi_number').val();
	var update_id = $('#update_id').val();
	var txt_date=$('#txt_date').val();
	var hidden_company_id=$('#hidden_company_id').val();
	
	if(operation==2)
	{
		show_msg('13');
		return false;
	}
	
	if(form_validation('txt_date','Date')==false)
   	{
		return;
   	}
	
	
	var row_num=$('#tbl_pi_item tbody tr').length;
	var data_all=""; var i=0; var selected_row=0; var error=0;
	
	$("#tbl_pi_item").find('tbody tr').each(function()
	{
		var id=$(this).attr("id");
		id=id.split("_");
		tr_id=id[1];
		var updateIdDtls=$('#updateIdDtls_'+tr_id).val();
		var is_checked=0;
		if($('#check_'+tr_id).is(':checked') || updateIdDtls!="")
		{
			if (form_validation('lotName_'+tr_id+'*countName_'+tr_id+'*yarnCompositionItem_'+tr_id+'*yarnCompositionPercentage_'+tr_id+'*yarnType_'+tr_id+'*colorName_'+tr_id+'*brand_'+tr_id+'*noOfBag_'+tr_id+'*conWgt_'+tr_id+'*bagCon_'+tr_id+'*bagWgt_'+tr_id+'*rateUnit_'+tr_id,'Lot*Count*Composition*Yarn Type*Color*Brand*No of Bag*Wgt/Con*Con/Bag*Bag/Wgt*Rate/PerUnit')==false)
			{
				error=1;
				return;
			}
			
			i++;
			if($('#check_'+tr_id).is(':checked'))  is_checked=1;
			data_all+="&lotName_" + i + "='" + $('#lotName_'+tr_id).val()+"'"+"&countName_" + i + "='" + $('#countName_'+tr_id).val()+"'"+"&yarnCompositionItem_" + i + "='" + $('#yarnCompositionItem_'+tr_id).val()+"'"+"&yarnCompositionPercentage_"+ i + "='" + $('#yarnCompositionPercentage_'+tr_id).val() + "'" + "&yarnType_" + i + "='" + $('#yarnType_'+tr_id).val()+"'"+"&colorId_" + i + "='" + $('#colorId_'+tr_id).val()+"'"+"&brand_" + i + "='" + $('#brand_'+tr_id).val() + "'" + "&noOfBag_" + i + "='" + $('#noOfBag_'+tr_id).val()+"'"+"&conWgt_" + i + "='" + $('#conWgt_'+tr_id).val()+"'"+"&bagCon_" + i + "='" + $('#bagCon_'+tr_id).val()+"'"+"&bagWgt_" + i + "='" + $('#bagWgt_'+tr_id).val()+"&piDtlsId_" + i + "=" + $('#piDtlsId_'+tr_id).val()+"&is_checked_" + i + "=" + is_checked+"&updateIdDtls_" + i + "=" +updateIdDtls+"&rateUnit_" + i + "=" + $('#rateUnit_'+tr_id).val();
			

			if($('#check_'+tr_id).is(':checked')) selected_row++;
		}
	});
		
	if(error==1) { return;}
	if(selected_row<1)
	{
		alert("Please Select WO");
		return;
	}
	//alert(data_all);return;
	
	
	var data="action=save_update_delete_dtls&operation="+operation+'&pi_number='+pi_number+'&cbo_receive_basis='+cbo_receive_basis+'&hidden_pi_id='+hidden_pi_id+'&total_row='+i+'&update_id='+update_id+'&txt_date='+txt_date+'&hidden_company_id='+hidden_company_id+data_all;
	// alert(data);return;
	freeze_window(operation);
	
	http.open("POST","requires/yarn_bag_sticker_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_pi_item_details_reponse;
}
		 
function fnc_pi_item_details_reponse()
{
	if(http.readyState == 4) 
	{
		//alert(http.responseText);release_freezing();return;
		var reponse=http.responseText.split('**'); 
		show_msg(trim(reponse[0]));
		
		if((reponse[0]==0 || reponse[0]==1))
		{	
			var cbo_receive_basis = $('#cbo_receive_basis').val();
			var hidden_pi_id = $('#hidden_pi_id').val();
			$('#update_id').val(reponse[1]);
			show_list_view(reponse[1], 'pi_update_details', 'pi_details_container', 'requires/yarn_bag_sticker_controller', '' ) ;
			show_list_view(hidden_pi_id+"_"+cbo_receive_basis, 'pi_details_listview', 'pi_listview_container', 'requires/yarn_bag_sticker_controller', '' ) ;
			set_button_status(1, permission, 'fnc_pi_item_details',1);
			set_all_onclick();
		}
		else if(reponse[0]==11)
		{
			alert(reponse[1]);
		}
		
		release_freezing();
	}
}




	function fnc_bundle_report_one()
	{
		var update_id = $('#update_id').val();
		var cbo_receive_basis = $('#cbo_receive_basis').val();
		if(trim(update_id)=="") {return;}
		//alert(update_id);
		window.open("requires/yarn_bag_sticker_controller.php?update_id=" + update_id+"&cbo_receive_basis=" + cbo_receive_basis+'&action=print_barcode_one', true );
		//var url=return_ajax_request_value(data, "print_report_bundle_barcode", "cut_and_lay_roll_wise_entry_controller");
		//window.open(url,"##");
	}


function put_data_dtls_part(data)
{
	get_php_form_data( data, "populate_data_from_master", "requires/yarn_bag_sticker_controller" );
	show_list_view(data, 'pi_update_details', 'pi_details_container', 'requires/yarn_bag_sticker_controller', '' ) ;
	set_button_status(1, permission, 'fnc_pi_item_details',1);
	set_all_onclick();
}

</script>

<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission); ?>
			
            <form name="pimasterform_1" id="pimasterform_1" autocomplete="off">
                <fieldset style="width:1150px; margin-top:10px;">
                	 <fieldset style="width:950px; margin-top:10px;">
                         <table width="900" border="0" cellpadding="0" cellspacing="0" align="center">
                             <tr  style="height:10px">
                            	 
                            </tr> 
                            <tr>
                                <td align="left" class="must_entry_caption" width="100">Receive Basis</td>
                                <td>
                                <?php
                                    $case_basis_arr=array(1=>"PI",2=>"Yarn Purchase order",3=>"Yarn Dyeing Work Order",4=>"Yarn Dyeing Work Order Without Order");
                                    echo create_drop_down( "cbo_receive_basis",152,$case_basis_arr,'', 0,'',"","change_receive_vasis(this.value)");
                                ?> 
                                </td>
                                <td align="center" class="must_entry_caption" id="receive_basis_caption" width="100">PI No</td>
                                <td>
                                    <input type="text" name="pi_number" id="pi_number" class="text_boxes" style="width:140px" placeholder="Double click for PI" onDblClick="openmypage()"  maxlength="50" />
                                    <input type="hidden" name="hidden_pi_id" id="hidden_pi_id" readonly/>
                                    <input type="hidden" name="hidden_company_id" id="hidden_company_id" readonly/>
                                </td>
                                <td  class="must_entry_caption" id="" width="100">Date</td>
                                <td>
                                    <input type="text" name="txt_date" id="txt_date" value="<? echo date("d-m-Y");?>" class="datepicker" style="width:140px" />
                                </td> 
                            </tr>
                            <tr  style="height:10px">
                            	 
                            </tr>                 
                        </table>
                  </fieldset>  
			<fieldset style="width:1120px; margin-top:10px;">
            	<legend>PI Item Details</legend>
                   <div id="pi_details_container">
                        <table class="rpt_table" width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" id="tbl_pi_item">
                            <thead>
                                <th class="must_entry_caption">Lot</th>
                                <th class="must_entry_caption">Count</th>
                                <th class="must_entry_caption">Composition</th>
                                <th class="must_entry_caption">%</th>
                                <th class="must_entry_caption">Yarn Type</th>
                                <th class="must_entry_caption">Color</th>
                                <th class="must_entry_caption">Brand</th>
                                <th class="must_entry_caption">No of Bag</th>
                                <th class="must_entry_caption">Wgt/Cone</th>
                                <th class="must_entry_caption">Cone/Bag</th>
                                <th class="must_entry_caption">BagWgt</th>
                                <th class="must_entry_caption">Rate/PerUnit</th>
                                <th width="80"></th>
                            </thead>
                            <tbody>
                            <? $i=1; ?>
                            	 <tr class="general" id="row_<?php echo $i; ?>">
                                    <td>
                                        <input type="checkbox" id="check_<?php echo $i; ?>" name="check[]"  />
                                        <input type="text" name="lotName[]" id="lotName_<?php echo $i; ?>" class="text_boxes"   maxlength="50" style="width:80px" />
                                    </td>
                                    
                                    <td>
                                    <?
                                        echo create_drop_down( "countName_".$i, 85, "SELECT id,yarn_count FROM lib_yarn_count WHERE status_active = 1 AND is_deleted = 0 ORDER BY yarn_count ASC",'id,yarn_count', 1, '-Select-',$row[csf('count_name')],"",1,"","","","","","","countName[]"); 
                                    ?>                         
                                    </td>
                                    <td>
                                        <?
                                           
                                            echo create_drop_down( "yarnCompositionItem_".$i,120, $composition,'', 1, '-Select-',$row[csf('yarn_composition_item')],"control_composition(1,'comp_one')",1); 
                                        ?>    
                                    </td>
                                    <td>
                                        <input type="text" name="yarnCompositionPercentage[]" id="yarnCompositionPercentage_<?php echo $i; ?>" value="" style="width:40px"  class="text_boxes" readonly disabled/>
                                    </td>
                                    <td>
                                        <?
                                            echo create_drop_down( "yarnType_".$i,80,$yarn_type,'', 1,'-Select-',$row[csf('yarn_type')],"",1); 
                                        ?>    
                                    </td>
                                    <td>
                                        <input type="text" name="colorName_<?php echo $i; ?>" id="colorName_<?php echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( 1 )"  maxlength="50" value="<? echo $color_array[$row[csf('color_id')]];?>"  style="width:60px;" readonly/>
                                    </td>
                                    <td>
                                        <input type="text" name="brand_<?php echo $i; ?>" id="brand_<?php echo $i; ?>" class="text_boxes"  maxlength="50" value=""  style="width:60px;" />
                                    </td>
                                    <td>
                                         <input type="text" name="noOfBag_<?php echo $i; ?>" id="noOfBag_<?php echo $i; ?>"    maxlength="50" style="width:80px"  class="text_boxes_numeric"/>
                                    </td>
                                    <td>
                                        <input type="text" name="conWgt_<?php echo $i; ?>" id="conWgt_<?php echo $i; ?>" class="text_boxes_numeric" value="" style="width:61px;" onKeyUp="calculate_amount(1)" />
                                    </td>
                                    <td>
                                        <input type="text" name="bagCon_<?php echo $i; ?>" id="bagCon_<?php echo $i; ?>" class="text_boxes_numeric" value="" style="width:45px;" onKeyUp="calculate_amount(1)" />
                                    </td>
                                    <td>
                                        <input type="text" name="bagWgt_<?php echo $i; ?>" id="bagWgt_<?php echo $i; ?>" class="text_boxes_numeric" value="" style="width:60px;" readonly/>
                                        <input type="hidden" name="updateIdDtls_<?php echo $i; ?>" id="updateIdDtls_<?php echo $i; ?>" readonly/>
                                  
                                        <input type="hidden" name="piDtlsId_<?php echo $i; ?>" id="piDtlsId_<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>" readonly/>
                                    </td>
                                    <td>
                                        <input type="text" name="rateUnit_<?php echo $i; ?>" id="rateUnit_<?php echo $i; ?>" class="text_boxes_numeric" value="" style="width:60px;" />
                                       
                                    </td>
                                    <td>
                                    <input type="button" id="add_tr_<?php echo $i; ?>" name="add_tr[]" class="formbutton" value="+"  style=" width:30px"/>

                                    </td>
                                </tr>
                            </tbody>
                   		</table>
                     </div>     
               </fieldset>
               <table width="100%">
                    <tr>
                        <td class="button_container" colspan="2"></td>
                    </tr>
                    <tr>
                     
                        <td width="80%" align="center"> 
                            <input type="hidden" name="txt_deleted_id" id="txt_deleted_id" readonly/> 
                            <input type="hidden" name="txt_tot_row" id="txt_tot_row" value="<? echo $txt_tot_row; ?>" readonly/> 
                             <input type="hidden" name="update_id" id="update_id" readonly/>                     
                           <? echo load_submit_buttons( $_SESSION['page_permission'], "fnc_pi_item_details", 0,0 ,"reset_form('pimasterform_1','','','txt_tot_row,0','$(\'#tbl_pi_item tbody tr:not(:first)\').remove();')",1) ; ?>
                           <input type="button" id="btn_bundle_stiker" name="btn_bundle_stiker" value="Bag Sticker" class="formbutton" onClick="fnc_bundle_report_one()"/>
                        </td>    
                    </tr>				
                </table>
                
                
                   <div id="pi_listview_container">
                      
                   </div>     
                
          </fieldset>   
              
                   
                
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>