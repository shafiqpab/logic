<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//-------------------------------------------------------------------------------------
echo load_html_head_contents("PDF Formate", "../../", 1, 1,$unicode,1,'');
?>

<script>
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>'; 
		
	function addRow( i )
	{
		var row_num = $('#tbl_pdf_formate tr').length-1;
		if (row_num!=i){
			return false;
		}
		else{
			var serial_number = row_num+1;
			i++;
			$("#tbl_pdf_formate tr:last").clone().find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { var nameArr = name.split('_'); return nameArr[0] +"_"+ i},
					'value': function(_, value) { return '' }
				});
			}).end().appendTo("#tbl_pdf_formate");

			$("#tbl_pdf_formate tr:last .serial").html(serial_number);
			var k=i-1;
			$('#increase_'+k).removeAttr("value").attr("value","+");
			$('#increase_'+i).removeAttr("value").attr("value","+");

			$('#decrease_'+i).removeAttr("value").attr("value","-");
			$('#increase_'+i).removeAttr("onclick").attr("onclick","addRow("+i+");");
			$('#decrease_'+i).removeAttr("onclick").attr("onclick","deleteRow("+i+");");
		}
	}

	function deleteRow(rowNo) 
	{
		var numRow = $('table#tbl_pdf_formate tbody tr').length;
		if(numRow==rowNo && rowNo!=1)
		{
			$('#tbl_pdf_formate tbody tr:last').remove();
		}
		else
		{
			//reset_form('','','hiddentaskid_'+rowNo+'*txttaskshortname_'+rowNo+'*txtdeadline_'+rowNo+'*txtexecutiondays_'+rowNo+'*txtnoticebefore_'+rowNo+'*txtsequenceno_'+rowNo+'*cbostatus_'+rowNo+'*updateid_'+rowNo);
		}
	}
 
	function PDFFormate( id, title)
	{
		var selected_task_id_str = '';
		var vi = id.split("_");
		var selected_name = $('#txtFieldName_'+vi[1]).val();
		var selected_id = $('#txtFieldId_'+vi[1]).val();
		var entry_form = $('#cbo_entry_form').val();
		
		var page_link='requires/pdf_formate_controller.php?action=task_name_search&selected_name='+selected_name+'&selected_id='+selected_id+'&entry_form='+entry_form;
		//alert(page_link);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=520px,height=300px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var pdf_formate_name = this.contentDoc.getElementById("pdf_formate_name").value;
			var pdf_formate_name_id = this.contentDoc.getElementById("pdf_formate_name_id").value;
			$('#txtFieldName_'+vi[1]).val(pdf_formate_name);
			$('#txtFieldId_'+vi[1]).val(pdf_formate_name_id);
		}
	}

	function ShowFormate(){
		 
		var page_link='requires/pdf_formate_controller.php?action=openpopup_pdf_formate';
		title = 'Show List';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=300px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var job_id = this.contentDoc.getElementById("txt_selected_formate_id").value;
			freeze_window(5);
			get_php_form_data( job_id, "formate_data_from_search_popup", "requires/pdf_formate_controller" );
			show_list_view(job_id,'field_data_from_search_popup','formate_list_view','requires/pdf_formate_controller','');
			release_freezing();
			 
		}
	}
   
	function pdf_formate_save( operation ) 
	{  
		if(form_validation('txt_height*txt_width*txt_top_padding*txt_bottom_padding*txt_left_padding*txt_right_padding*cbo_entry_form','Height Required*Width Required*Top Margin Required*Bottom Margin Required*Left Margin Required*Right Margin Required*Entry Page')==false)
		{
			return;
		}

		var row_num = $('#tbl_pdf_formate tr').length-1;
		var dtlsFieldArr = Array();
		for(i=1;i<=row_num;i++){
			dtlsFieldArr.push('txtFieldName_'+i+'*txtFieldId_'+i+'*txtFontSize_'+i+'*texSerialNumber_'+i+'*txtFontWeight_'+i);
		}
		  
		var data="action=save_update_delete&operation="+operation+"&row_num="+row_num+get_submitted_data_string('txt_system_id*txt_height*txt_width*txt_top_padding*txt_bottom_padding*txt_left_padding*txt_right_padding*txt_line_space*txt_line_break*txt_front_color*code_type*cbo_orientation*cbo_font*cbo_entry_form*'+dtlsFieldArr.join('*'),"../../");
 
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/pdf_formate_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = () => {

			if(http.readyState == 4) 
			{
				var reponse=trim(http.responseText).split('**');
			    show_msg(reponse[0]);
				if(reponse[0] == 0 || reponse[0] == 1)
				{
					show_msg(trim(reponse[0]));
					$('#txt_system_id').val(reponse[1]);
					set_button_status(1, permission, 'pdf_formate_save',1);
				}
				else if(reponse[0]==2)
				{
					reset_fnc();
					set_button_status(0, permission, 'pdf_formate_save',1);					
				}		
				release_freezing();
			}
		};
	}

	function reset_fnc()
	{
		window.location.reload();
	}
	</script>
</head>

<body onLoad="set_hotkey()">
<? echo load_freeze_divs ("../../", $permission);  ?>
<div align="center" style="width:100%;">
        <fieldset style="width:700px;">
            <legend>PDF Formate</legend>
            <form name="pdf_formate_1" id="pdf_formate_1" autocomplete="off"> 
                <table cellpadding="2" cellspacing="2" width="100%">
					<tr> 
						<td colspan="6" align="center"><strong>System ID</strong>
							<input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:145px"  placeholder="Show Double Click" onDblClick="ShowFormate();"/>
						</td>
                    </tr>
					<tr>
						<td width="110" align="right" class="must_entry_caption">Height</td>
						<td width="150">
							<input type="text" name="txt_height" id="txt_height" class="text_boxes" style="width:140px" maxlength="50" placeholder="Height in mm"/>
						</td>
						<td width="110" align="right" class="must_entry_caption">Width</td>
						<td width="150">
							<input type="text" name="txt_width" id="txt_width" class="text_boxes" style="width:140px" maxlength="50" placeholder="Width in mm"/>
						</td>
						<td width="110" align="right" class="must_entry_caption">Top Margin</td>
						<td width="150">
							<input type="text" name="txt_top_padding" id="txt_top_padding" class="text_boxes" style="width:140px"  maxlength="50" placeholder="Top Margin in mm"/>
						</td>            
					</tr>
					<tr>
						<td width="110" align="right"  class="must_entry_caption">Bottom Margin</td>
						<td width="150">
							<input type="text" name="txt_bottom_padding" id="txt_bottom_padding" class="text_boxes" style="width:140px"  maxlength="50" placeholder="Bottom Margin in mm"/>
						</td>
						<td width="110" align="right"  class="must_entry_caption">Left Margin</td>
						<td width="150">
							<input type="text" name="txt_left_padding" id="txt_left_padding" class="text_boxes" style="width:140px"  maxlength="50" placeholder="Left Margin in mm"/>
						</td>
						<td width="110" align="right"  class="must_entry_caption">Right Margin</td>
						<td width="150">
							<input type="text" name="txt_right_padding" id="txt_right_padding" class="text_boxes" style="width:140px"  maxlength="50" placeholder="Right Margin in mm"/>
						</td>         
					</tr>
					<tr>
						<td width="110" align="right" class="must_entry_caption">Line Space</td>
						<td width="150">
							<input type="text" name="txt_line_space" id="txt_line_space" class="text_boxes" style="width:140px" maxlength="50" placeholder="Line Space"/>
						</td>

						<td width="110" align="right" class="must_entry_caption">Line Break</td>
						<td width="150">
							<input type="text" name="txt_line_break" id="txt_line_break" class="text_boxes" style="width:140px" maxlength="50" placeholder="Line Break"/>
						</td>

						<td width="110" align="right" class="must_entry_caption">Font Color</td>
						<td width="150">
							<input type="color" name="txt_front_color" id="txt_front_color" value="#000000"  style="width:150px;height:20px;"/>
						</td>
					</tr>
					<tr>
					    <td width="110" align="right" class="must_entry_caption">Code Type</td>
						<td width="150">
						<? 
						$codeTypeArr = ['1'=>'Code 39', '2'=>'Code 128', '3'=>'QR Code', '4'=>'Codabar'];
						echo create_drop_down("code_type", 150, $codeTypeArr, "", "", "", 1, "" );
						?>
						</td>
						<td width="110" align="right" class="must_entry_caption">Orientation</td>
						<td width="150">
						<?
						$OrientationArr = ['L'=>'Landscape', 'P'=>'Portrait'];
						echo create_drop_down("cbo_orientation", 150, $OrientationArr, "", "", "", 1, "" );
						?>
						</td>
						<td width="110" align="right" class="must_entry_caption">Font</td>
						<td width="150">
						<?
						$FontArr = ['Arial'=>'Arial', 'Courier'=>'Courier', 'Helvetica'=>'Helvetica', 'Times'=>'Times', 'Symbol'=>'Symbol', 'ZapfDingbats'=>'ZapfDingbats'];
						echo create_drop_down("cbo_font", 150, $FontArr, "", "", "", 1, "" );
						?>
						</td>
					</tr>
					<tr>
						<td width="110" align="right" class="must_entry_caption">Entry Page</td>
						<td width="150"><? echo create_drop_down( "cbo_entry_form", 150, $entry_form,"",1,"--Select Page--", 0,"", 0,"2,159","" ); ?>
                        </td>
					</tr>
					<tr>
                        <td align="left" colspan="6" valign="top" id="formate_list_view" >
							<table id="tbl_pdf_formate" class="rpt_table" rules="all" border="1"  align="left" width="100%">
								<thead>
									<tr>
										<th width="20" class="must_entry_caption">Sl</th>
										<th class="must_entry_caption">Field Name</th>
										<th width="80">Font Size</th>
										<th width="80">Font Weight</th>
										<th width="100" class="must_entry_caption">Line Sequance</th>
										<th width="68"></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="serial" align="center">1</td>
										<td align="center">
											<input type="text" name="txtFieldName_1" id="txtFieldName_1" style="width:95%" class="text_boxes" placeholder="Double Click To Search" onDblClick="PDFFormate($(this).attr('id'),'Task Name Search')" readonly/>
											<input type="hidden" id="txtFieldId_1" name="txtFieldId_1"/>
										</td>
										<td align="center">
											<input type="text" name="txtFontSize_1" class="text_boxes" style="width:50px" id="txtFontSize_1" value="12"/>
										</td>
			
										<td align="center">
											<? 
												$fontWeightArr = ['normal'=>'normal','bold'=>'bold','bolder'=>'bolder','lighter'=>'lighter'];
												echo create_drop_down("txtFontWeight_1", 150, $fontWeightArr, "", "", "", 1, "" );
											?>
										</td>

										
										<td align="center">
											<input type="text" name="texSerialNumber_1" id="texSerialNumber_1" style="width:80px" class="text_boxes" placeholder="Serial Number"/>
										</td>
										
										<td>
											<input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbutton" value="+" onClick="addRow(1);" />&nbsp;
											<input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbutton" value="-" onClick="deleteRow(1);" />
										</td>
									</tr>
								</tbody>
							</table>
                        </td>
					</td>
					<br/>
					<tr>
                        <td colspan="6" height="50" valign="middle" align="center" class="button_container">
                            <?= load_submit_buttons( $permission, "pdf_formate_save", 0,0 ,"reset_form('pdf_formate_1', '', '')",1);?>
                         </td>
                    </tr>
				</table>
            </form> 
        </fieldset>
        </div>
  </body>
    
<script>//set_multiselect('cbo_catagory_item','0','0','','');</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
