<? 
/*------------------------------------------------------------ Comments
Purpose			: 	This form will create for Yarn Bag Sticker
Functionality	:	
JS Functions	:
Created by		:	MD MAHBUBUR RAHMAN
Creation date 	: 	1-10-2016
Updated by 		: 	
Update date		: 
QC Performed BY	:		
QC Date			:	
Comments		:
*/
//----------------------------------------------------------------------------------

session_start();
if($_SESSION['logic_erp']['user_id']=="") header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
echo load_html_head_contents("Yarn Bag Receive", "../../", 1, 1,'','',''); 
?>

<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission='<? echo $permission; ?>';
	var scanned_barcode=new Array();
	
	function openmypage_barcode()
	{ 
		if(form_validation('cbo_receive_basis','Receive Basis')==false)
		{
			return; 
		}
	
		var cbo_receive_basis = $('#cbo_receive_basis').val();
		var booking_type = $('#booking_type').val();	
		var company_id=$('#cbo_company_name').val();
		var pi_id=$('#pi_id').val();
	    var prevBarCodeNos='';
		
		$("#tbl_yarn_bag_recieve_item").find('tbody tr').each(function()
		{
			var barcode_no=$(this).find('input[name="barCodeNo[]"]').val();
			
			if(barcode_no!="")
			{
				if(prevBarCodeNos=="") prevBarCodeNos=barcode_no; else prevBarCodeNos+=","+barcode_no;
			}
		});
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/yarn_bag_receive_controller.php?company_id='+company_id+'&cbo_receive_basis='+cbo_receive_basis+'&pi_id='+pi_id+'&prevBarCodeNos='+prevBarCodeNos+'&booking_type='+booking_type+'&action=barcode_popup','Barcode Popup','width=800px,height=400px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function() 
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var barcode_nos=this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos
			var selected_pi_id=this.contentDoc.getElementById("selected_pi_id").value; //Barcode Nos
			var booking_basis=this.contentDoc.getElementById("cbo_booking_basis").value; //Barcode Nos
			//freeze_window(5);
			//alert(booking_basis);
			
			if(pi_id=="")
			{
				var receive_date=$('#txt_receive_date').val();
				get_php_form_data(selected_pi_id+"**"+receive_date+"**"+$('#cbo_receive_basis').val()+"**"+booking_basis,'get_pi_data', 'requires/yarn_bag_receive_controller');
				$('#cbo_receive_basis').attr('disabled','true');
				$('#booking_type').val(booking_basis);
			}
			
			var barCodeNo=$('#barCodeNo_1').val();
			if(barCodeNo=="")
			{
				var tot_row=0;
			}
			else
			{
				var tot_row=$('#txt_tot_row').val();
			}
			
			var exchange_rate=$('#txt_exchange_rate').val();
			var ile_perc=$('#ile_perc').val();
			var data=barcode_nos+"**"+exchange_rate+"**"+tot_row+"**"+ile_perc;
			var list_view_barcode =return_global_ajax_value( data, 'populate_barcode_data', '', 'requires/yarn_bag_receive_controller');
			if(barCodeNo=="")
			{
				$('#tbl_yarn_bag_recieve_item tbody tr:last').remove();
			}
			
			$("#yarn_bag_recieve_container").append(list_view_barcode);	
			load_scanned_barcode();
			
			var lastTrId = $('#tbl_yarn_bag_recieve_item tbody tr:last').attr('id').split('_');
			var numRow=lastTrId[1];
			$('#txt_tot_row').val(numRow);
		}
	}
	
	function load_scanned_barcode()
	{
		scanned_barcode=new Array();
		$("#tbl_yarn_bag_recieve_item").find('tbody tr').each(function()
		{
			var barcode_no=$(this).find('input[name="barCodeNo[]"]').val();
			if(barcode_no!="")
			{
				scanned_barcode.push(barcode_no);
			}
		});
		
	}
	
	$('#txt_yarn_barcode').live('keydown', function(e) {
		if (e.keyCode === 13) 
		{
			e.preventDefault();
			
			var bar_code=trim($('#txt_yarn_barcode').val());
			//alert(scanned_barcode+"**"+bar_code);
			if( jQuery.inArray( bar_code, scanned_barcode )>-1) 
			{ 
				alert('Sorry! Barcode Already Scanned.'); 
				$('#txt_yarn_barcode').val('');
				return; 
			}
			
			var pi_id_prev=$('#pi_id').val();	
			var pi_id_curr_info=trim(return_global_ajax_value( bar_code, 'populate_pi_id', '', 'requires/yarn_bag_receive_controller'));

			if(pi_id_curr_info=="")
			{
				alert('Barcode is Not Valid');
				$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
				{
					$('#messagebox_main', window.parent.document).html('Barcode is Not Valid.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
				});
				$('#txt_yarn_barcode').val('');
				return; 
			}
			
			
			pi_id_curr_info=pi_id_curr_info.split("_");
			var pi_id_curr=pi_id_curr_info[1];
			var sticker_receive_basis=pi_id_curr_info[0];
			
			
			if(pi_id_curr==0)
			{
				alert('Sorry! Barcode Already Scanned.'); 
				$('#txt_yarn_barcode').val('');
				return; 
			}
			
			if(!(pi_id_prev=="" || pi_id_prev==pi_id_curr))
			{
				alert("PI Mix Not Allowed");
				$('#txt_yarn_barcode').val('');
				return;	
			}
			
			if(sticker_receive_basis==1)
			{
				var receive_basis=1;
				var booking_type=0;
				$('#cbo_receive_basis').val(receive_basis);
				$('#booking_type').val(0);
			}
			else if(sticker_receive_basis==2)
			{
				var receive_basis=2;
				var booking_type=1;
				$('#cbo_receive_basis').val(2);
				$('#booking_type').val(1);
			}
			else if(sticker_receive_basis==3)
			{
				var receive_basis=2;
				var booking_type=2;
				$('#cbo_receive_basis').val(2);
				$('#booking_type').val(2);
			}
			else if(sticker_receive_basis==4)
			{
				var receive_basis=2;
				var booking_type=3;
				$('#cbo_receive_basis').val(2);
				$('#booking_type').val(3);
			}
			
				
			if(pi_id_prev=="")
			{
				var receive_date=$('#txt_receive_date').val();
				get_php_form_data(pi_id_curr+"**"+receive_date+"**"+receive_basis+"**"+booking_type,'get_pi_data', 'requires/yarn_bag_receive_controller');
				$('#cbo_receive_basis').attr('disabled','true');
			}
			
			var barCodeNo=$('#barCodeNo_1').val();
			if(barCodeNo=="")
			{
				var tot_row=0;
			}
			else
			{
				var tot_row=$('#txt_tot_row').val();
			}
			
			var exchange_rate=$('#txt_exchange_rate').val();
			var ile_perc=$('#ile_perc').val();
			var data=bar_code+"**"+exchange_rate+"**"+tot_row+"**"+ile_perc;
			
			var list_view_barcode=trim(return_global_ajax_value( data, 'populate_barcode_data', '', 'requires/yarn_bag_receive_controller'));
			if(list_view_barcode=="")
			{
				alert('Sorry! Barcode Already Scanned.'); 
				$('#txt_yarn_barcode').val('');
				return; 
			}
			
			if(barCodeNo=="")
			{
				$('#tbl_yarn_bag_recieve_item tbody tr:last').remove();
			}
			
			$("#yarn_bag_recieve_container").append(list_view_barcode);	
				
			var lastTrId = $('#tbl_yarn_bag_recieve_item tbody tr:last').attr('id').split('_');
			var numRow=lastTrId[1];
			$('#txt_tot_row').val(numRow);
			$('#txt_yarn_barcode').val('');
			scanned_barcode.push(bar_code);
		}
	});
	
	function fn_deleteRow( mid )
	{
		var num_row =$('#tbl_yarn_bag_recieve_item tbody tr').length;
		//alert(mid);return;
		var bar_code =$("#barCodeNo_"+mid).val();
		if(num_row ==1)
		{
			$('#row_'+mid).find(":input:not(:button)").val('');
		}
		else
		{
			$("#row_"+mid).remove();
		}
		
		var index = scanned_barcode.indexOf(bar_code);
		scanned_barcode.splice(index,1);
	}
	
	function fnc_yarn_bag_receive( operation ) 	
	{	
		if(operation==4)
		{
			print_report( $('#cbo_company_name').val()+'*'+$('#txt_mrr_no').val()+'*'+$('#update_id').val(),"yarn_bag_receive_print", "requires/yarn_bag_receive_controller" ) 
			 return;
		}
		else if(operation==2)
		{
			show_msg('13');
			return;
		}
	
		if(form_validation('txt_receive_date*cbo_store_name*cbo_receive_purpose*pi_number','Receive Date*Store Name*Purpose*PI Number')==false)
		{
			return; 
		}
                
		var current_date = '<? echo date("d-m-Y"); ?>';
		if (date_compare($('#txt_receive_date').val(), current_date) == false) 
		{
			alert("Receive Date Can not Be Greater Than Current Date");
			return;
		}

		var j=0; var dataString='';
		$("#tbl_yarn_bag_recieve_item").find('tbody tr').each(function()
		{
			var lotName=$(this).find('input[name="lotName[]"]').val();
			var countName=$(this).find('select[name="countName[]"]').val();
			var yarnCompositionItem=$(this).find('select[name="yarnCompositionItem[]"]').val();
            var yarnCompositionPercentage=$(this).find('input[name="yarnCompositionPercentage[]"]').val();
			var yarnType=$(this).find('select[name="yarnType[]"]').val();
			var colorId=$(this).find('input[name="colorId[]"]').val();
			var colorName=$(this).find('input[name="colorName[]"]').val();
            var brand=$(this).find('input[name="brandId[]"]').val();
			var barCodeNo=$(this).find('input[name="barCodeNo[]"]').val();
			var conWgt=$(this).find('input[name="conWgt[]"]').val();
			var bagCon=$(this).find('input[name="bagCon[]"]').val();
			var bagWgt=$(this).find('input[name="bagWgt[]"]').val();
			var rate=$(this).find('input[name="rate[]"]').val();
			var ilecost=$(this).find('input[name="ilecost[]"]').val();
			var amount=$(this).find('input[name="amount[]"]').val();
			var bookcurrency=$(this).find('input[name="bookcurrency[]"]').val();
			
			if(lotName!="")	
			{
				j++;
					
				dataString+='&lotName' + j + '=' + lotName + '&countName' + j + '=' + countName + '&yarnCompositionItem' + j + '=' + yarnCompositionItem + '&yarnCompositionPercentage'+ j + '=' + yarnCompositionPercentage +'&yarnType' + j + '=' + yarnType + '&colorId' + j + '=' + colorId + '&colorName' + j + '=' + colorName + '&brandId' + j + '=' + brand +'&barCodeNo' + j + '=' + barCodeNo + '&conWgt' + j + '=' + conWgt + '&bagCon' + j + '=' + bagCon + '&bagWgt' + j + '=' + bagWgt + '&rate' + j + '=' + rate + '&ilecost' + j + '=' + ilecost + '&amount' + j + '=' + amount + '&bookcurrency' + j + '=' + bookcurrency;
			}
			
		});
		
		if(j<1)
		{
			alert('No data');
			return;
		}
		
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_mrr_no*txt_challan_no*txt_receive_date*cbo_store_name*cbo_receive_purpose*cbo_receive_basis*pi_id*cbo_company_name*cbo_source*cbo_currency*txt_exchange_rate*cbo_supplier*ile_perc*update_id*booking_type',"../../")+dataString;
		
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/yarn_bag_receive_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_yarn_bag_receive_reponse;
	
	}
	
	function fnc_yarn_bag_receive_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
                        if (response[0] * 1 == 20 * 1) 
                        {
                            alert(response[1]);
                            release_freezing();
                            return;
                        }
                        
			show_msg(trim(response[0])); 
			
			if((response[0]==0 || response[0]==1))
			{
				$("#txt_mrr_no").val(response[1]);
				$("#update_id").val(response[2]);
				set_button_status(1, permission, 'fnc_yarn_bag_receive',1);
			}
			release_freezing();
		}
	}
	
	function open_mrrpopup()
	{
		var page_link='requires/yarn_bag_receive_controller.php?action=mrr_popup_info'; 
		var title="Search MRR Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=400px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var recv_id=this.contentDoc.getElementById("hidden_recv_id").value;
			var mrrNumber=this.contentDoc.getElementById("hidden_recv_number").value; // mrr number
			
			$("#update_id").val(recv_id);
			$("#txt_mrr_no").val(mrrNumber);

			get_php_form_data(mrrNumber+"_"+recv_id, "populate_data_from_data", "requires/yarn_bag_receive_controller");
			show_list_view(recv_id,'recive_details','yarn_bag_recieve_container','requires/yarn_bag_receive_controller','');
			
			load_scanned_barcode();
			
			var lastTrId = $('#tbl_yarn_bag_recieve_item tbody tr:last').attr('id').split('_');
			var numRow=lastTrId[1];
			$('#txt_tot_row').val(numRow);
			$('#txt_yarn_barcode').val('');
			set_button_status(1, permission, 'fnc_yarn_bag_receive',1,1);	
		}
	}

</script>
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
    <? echo load_freeze_divs ("../../",$permission); ?>
        <form name="yarnbagform_1" id="yarnbagform_1" autocomplete="off">
            <fieldset style="width:1040px; margin-top:10px;">
            <legend>Yarn Bag Receive</legend>
            <table width="900" border="0" cellpadding="0" cellspacing="3" align="center">
            	<tr>
                    <td colspan="6" align="center"><b>MRR Number</b>
                        <input type="text" name="txt_mrr_no" id="txt_mrr_no" class="text_boxes" style="width:155px" placeholder="Double Click To Search" onDblClick="open_mrrpopup()" readonly />
                        <input type="hidden" name="update_id" id="update_id" readonly/> 
                    </td>
                </tr>
                 <tr><td><br/></td></tr>
            	<tr>
                	<td class="must_entry_caption" width="100"> Receive Date</td>
                	<td width="100">
                      <input type="text" name="txt_receive_date" id="txt_receive_date"  class="datepicker" style="width:160px" />
                    </td> 
                    <td width="100" align="left" class="must_entry_caption">Store Name</td>
                    <td id="store_td">
						<? 
                        	echo create_drop_down( "cbo_store_name", 172, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=1 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select --",0, "",0); 
                        ?>
                    </td>
                    <td width="100">Challan No</td>
                    <td>
                    	<input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:160px"/>
                    </td>
                </tr>
                <tr>
                	<td id="receive_basis_caption" class="must_entry_caption" width="100">Purpose</td>
                    <td width="170">
						<? 
                       		echo create_drop_down( "cbo_receive_purpose", 170, $yarn_issue_purpose,"", 1, "-- Select Purpose --", 16, "", "","2,5,6,15,16");
                        ?>
                    </td>
                    <td align="left" width="100">Receive Basis</td>
                    <td>
						<?php
						//print_r($receive_basis_arr);
							echo create_drop_down( "cbo_receive_basis",172,$receive_basis_arr,'', 1,"-- Select Purpose --","","",1,'1,2');
                        ?> 
                    </td>
                    <td id="receive_basis_caption" width="100">PI/WO No</td>
                    <td>
                    	<input type="text" name="pi_number" id="pi_number" class="text_boxes" style="width:160px" disabled/>
                        <input type="hidden" name="pi_id" id="pi_id"/>
                        <input type="hidden" name="booking_type" id="booking_type"/>
                    </td>
                </tr>
                <tr>
                    <td width="100">Company Name </td>
                    <td>
						<? 
                        	echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select Company --",$selected,"",1 );
                        ?>
                    </td>
                    <td align="left" id="receive_basis_caption" width="100">Source</td>
                    <td id="sources">
						<?
                            echo create_drop_down( "cbo_source", 172,$source,"",'1', "-- Select --",$selected,"",1 );
                        ?>
                        <input type="hidden" name="ile_perc" id="ile_perc"/>
                    </td>
                    <td width="100">Currency</td>
                    <td>
						<?
                        	echo create_drop_down("cbo_currency",170,$currency,"",1,"-- Select Currency --",0,"",1);
                        ?>
                    </td> 
                </tr>
                <tr>
                    <td width="100">Exchange Rate</td>
                    <td>
                    	<input type="text" name="txt_exchange_rate" id="txt_exchange_rate" class="text_boxes_numeric" style="width:160px" disabled/>
                    </td>
                    <td width="100" align="left"> Supplier </td>
                    <td id="supplier" width="160"> 
						<?
                       		echo create_drop_down( "cbo_supplier", 170,"select id, supplier_name from lib_supplier","id,supplier_name", 1, " Display ", "", "",1);
                        ?>
                    </td>
                    <td width="100">Bag Barcode</td>
                    <td>
                    	<input type="text" name="txt_yarn_barcode" id="txt_yarn_barcode" class="text_boxes_numeric" style="width:160px" onDblClick="openmypage_barcode()" placeholder="Browse/Write/Scan" />
                    </td> 
                </tr>  
            </table>
            <fieldset style="width:1030px; margin-top:10px;">
            <legend>Yarn Bag Receive Details</legend>
            <div id="yarn_bag_receive_details_container">
                <table class="rpt_table" width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" id="tbl_yarn_bag_recieve_item">
                    <thead>
                        <th>Lot</th>
                        <th>Count</th>
                        <th>Composition</th>
                        <th>%</th>
                        <th>Yarn Type</th>
                        <th>Color</th>
                        <th>Brand</th>
                        <th>Bag Barcode No</th>
                        <th>Wgt/Cone</th>
                        <th>Cone/Bag</th>
                        <th>BagWgt</th>			
                        <th>Rate/kg</th>
                        <th>ILE Cost</th>
                        <th>Amount</th>
                        <th>Book Currency</th>
                        <th></th>
                    </thead>
                    <tbody id="yarn_bag_recieve_container">
                    	<? $i=1; ?>
                        <tr class="general" id="row_<?php echo $i; ?>">
                            <td>
                                <input type="text" name="lotName[]" id="lotName_<?php echo $i; ?>" class="text_boxes" style="width:50px" disabled />
                            </td>
                            <td>
                                <?
                                	echo create_drop_down("countName_".$i, 85, "SELECT id,yarn_count FROM lib_yarn_count",'id,yarn_count', 1, '-Select-','',"",1,"","","","","","","countName[]"); 
                                ?>                         
                            </td>
                            <td>
                                <?
                                    echo create_drop_down( "yarnCompositionItem_".$i,160, $composition,'', 1, '-Select-','',"",1,"","","","","","","yarnCompositionItem[]"); 
                                ?>    
                            </td>
                            <td>
                                <input type="text" name="yarnCompositionPercentage[]" id="yarnCompositionPercentage_<?php echo $i; ?>" value="" style="width:40px"  class="text_boxes" readonly disabled/>
                            </td>
                            <td> 
                                <?
                                    echo create_drop_down( "yarnType_".$i,80,$yarn_type,'', 1,'-Select-','',"",1,"","","","","","","yarnType[]"); 
                                ?>    
                            </td>
                            <td>
                                <input type="text" name="colorName[]" id="colorName_<?php echo $i; ?>" class="text_boxes" value="" style="width:50px;" disabled/>
                                <input type="hidden" name="colorId[]" id="colorId_<?php echo $i; ?>"/>
                            </td>
                            <td>
                                <input type="text" name="brand[]" id="brand_<?php echo $i; ?>"  style="width:50px"  class="text_boxes" disabled/>
                                <input type="hidden" name="brandId[]" id="brandId_<?php echo $i; ?>" />
                            </td>
                            <td>
                                <input type="text" name="barCodeNo[]" id="barCodeNo_<?php echo $i; ?>"  style="width:80px"  class="text_boxes_numeric" disabled/>
                            </td>
                            <td>
                                <input type="text" name="conWgt[]" id="conWgt_<?php echo $i; ?>" class="text_boxes_numeric" value="" style="width:50px;" disabled />
                            </td>
                            <td>
                                <input type="text" name="bagCon[]" id="bagCon_<?php echo $i; ?>" class="text_boxes_numeric" value="" style="width:45px;" disabled />
                            </td>
                            <td>
                                <input type="text" name="bagWgt[]" id="bagWgt_<?php echo $i; ?>" class="text_boxes_numeric" value="" style="width:50px;" disabled/>
                            </td>
                            <td> 
                                <input type="text" name="rate[]" id="rate_<?php echo $i; ?>" class="text_boxes_numeric" value="" style="width:50px;" disabled/></td>
                            <td> 
                                <input type="text" name="ilecost[]" id="ilecost_<?php echo $i; ?>" class="text_boxes_numeric" value="" style="width:40px;"  disabled/></td>
                            <td> 
                                <input type="text" name="amount[]" id="amount_<?php echo $i; ?>" class="text_boxes_numeric" value="" style="width:50px;" disabled/>	</td>
                            <td> 
                                <input type="text" name="bookcurrency[]" id="bookcurrency_<?php echo $i; ?>" class="text_boxes_numeric" value="" style="width:60px;"  disabled/>	
                            </td>  
                            <td width="65">
                                <input type="button" id="decrease_<?php echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<?php echo $i; ?>);" />
                                
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
                        <? 
							 echo load_submit_buttons( $_SESSION['page_permission'], "fnc_yarn_bag_receive", 0,1 ,"reset_form('yarnbagform_1','','','txt_tot_row,1','$(\'#tbl_yarn_bag_recieve_item tbody tr:not(:first)\').remove();','cbo_receive_purpose*cbo_receive_basis')",1) ; 
							
						 ?>
                    </tr>
                    <tr>
                        <input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
                    </tr>				
                </table> 
            </fieldset>
        </form>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>