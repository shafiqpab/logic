<?php
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$selected_name = $_REQUEST['selected_name'];

$buyer_cond="";	$company_cond="";
if ($_SESSION['logic_erp']["data_level_secured"]==1)
{
	if($_SESSION['logic_erp']["buyer_id"]!=0) $buyer_cond=" and buy.id in (".$_SESSION['logic_erp']["buyer_id"].")";
	if($_SESSION['logic_erp']["company_id"]!=0) $company_cond=" and id in (".$_SESSION['logic_erp']["company_id"].")";
}

$fieldArr[2] = array(1=>'Barcode image', 2=>'Qrcode', 3=>"Party name Short", 4=>"Book or po no prefix", 5=>"Mchine dia gause", 6=>"Barcode number", 7=>"Production date", 8=>"Buyer name", 9=>"Po no", 10=>"File no", 11=>"Grouping", 12=>"Comp", 13=>"Dia length tube size", 14=>"Gsm", 15=>"Yarn count", 16=>"Brand", 17=>"Yarn lot", 18=>"Program no", 19=>"Qnty", 20=>"ShiftName",	21=>"Roll no", 22=>"Color",	23=>"ColorRange", 24=>"Style ref", 25=>"Booking no", 26=>"Operator name", 27=>"ProductionId", 28=>"Batch no", 29=>"Tube ref no", 30=>"Machine brand", 31=>"Yarn type", 32=>"Construction", 33=>"Composition", 34=>"Floor name",	35=>"Machine name",	36=>"Dia width gauge", 37=>"Full job no", 38=>"Dia tube", 39=>"Stitch length", 40=>"Reject qty", 41=>"Yarn information string", 42=>"All yarn type", 43=>"Location name", 44=>"Color type name", 45=>"BodyPartId", 46=>"Yarn information string without compo",	47=>"Yarn issue challan no", 48=>"Qc pass qnty pcs", 49=>"Coller cuff size", 50=>"Internal reference no", "51" => "party name full", "52" => "company full name", "53" => "company short name", "54" => "Production time", "55" => "Composition Short", "56"=>"Plan remarks");

$fieldArr[159] = array(1=>'Company Full Name', 2=>"Program no", 3=>"Production Date", 4=>"Production Time",5=>"shiftName", 6=>"Operator Name", 7=>"PO No", 8=>"Buyer Name", 9=>"Style Ref",10=>"Machine Name", 11=>"Machine Dia Gauge", 12=>"Finish Dia", 13=>"Stitch Length", 14=>"Comp", 15=>"Gsm", 16=>"Color", 17=>"Yarn Count", 18=>"Brand", 19=>"Yarn Type", 20=>"Yarn Lot", 21=>"Roll No", 22=>"Qnty", 23=>"Barcode No", 24=>"Barcode image", 25=>"Cust Buyer");

if($action=="task_name_search")
{ 
    extract($_REQUEST);
	echo load_html_head_contents("Buyer Selection Form","../../../", 1, 1, $unicode,'','');

	$selected_name = $selected_name;
	$select_id = $selected_id;
	$page_id = $page_id;
	//$selectedArr = explode(",", $selected_name);
	?>
	<script>
 
	    var selected_id = new Array();
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_task_list' ).rows.length;
			tbl_row_count = tbl_row_count - 1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val());
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}	
				selected_id.splice( i, 1 );
			}
			var id =''; 
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			$('#pdf_formate_name_id').val( id );
			
		}


		function close_window()
		{
			var nameArr = Array();
			var formate_name_id =$('#pdf_formate_name_id').val();
			var formate_name_id_arr = formate_name_id.split(',');

			$.each(formate_name_id_arr, function( index, value ) {
				nameArr.push($('#txt_task_short_name_'+value).val());
			});
			 
			$('#pdf_formate_name').val( nameArr.join(','));
			parent.emailwindow.hide();
		}
    </script> 
    	<div style="width:auto;" align="center" id="tna_task_list">
		<input type="hidden" name="pdf_formate_name_id" id="pdf_formate_name_id" value="<?= $select_id ?>"/>
		<input type="hidden" name="pdf_formate_name" id="pdf_formate_name" value="<?= $selected_name; ?>"/>
    	<fieldset>
            <div style="width:480px;">
                <table align="center" width="450" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">
                    <thead>
                        <th width="50">SL</th> 
                        <th width="200">Task Name</th>
                        <th width="200">Task Short Name</th>
                    </thead>
                </table> 
            </div>
        
            <div style="overflow-y:scroll; max-height:220px; width:470px;"  align="center">
                <table align="center" id="tbl_task_list" width="450" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">  
                <?
                $i=1;
                foreach($fieldArr[$entry_form] as $key => $data)
                {
					?>
					<tr id="search<? echo $i; ?>">
						<td width="50"><?= $i; ?>
							<input type="hidden" name="txt_individual" id="txt_individual<?= $i ?>" value="<?= $data; ?>"/>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?= $i ?>" value="<?= $key; ?>"/>
						</td>
						<td width="200" onClick="js_set_value(<? echo $i; ?>);"><?= $data; ?></td>
						<td width="200">
							<input type="text" name="txt_task_short_name_<?= $key; ?>" class="text_boxes" style="width:94%" id="txt_task_short_name_<?= $key; ?>" placeholder="Task Short Name"/>
						</td>
					</tr>
					<?
					$i++;
                }
                ?>
                </table>
            </div>
        
            <? if ($is_single!=1) { ?>
			<div style="width:450px; margin-top:10px;">
				<div style="width:50%; float:left" align="left">
					<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
				</div>
				<div style="width:50%; float:left" align="left">
					<input type="button" name="close" onClick="close_window();" class="formbutton" value="Close" style="width:100px" />
				</div>
			</div>
            <? } ?>
    	</fieldset>
    	</div>
       
        <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
     	<script>
 			var tableFilters = 
			{
				//col_2: "none",
				display_all_text: " -- All --",
			}							
			setFilterGrid("tbl_task_list", -1, tableFilters);

			var pdf_formate_name_id=$("#pdf_formate_name_id").val().split(',');
			var pdf_formate_name=$("#pdf_formate_name").val().split(',');
			var pdf_formate_length=pdf_formate_name_id.length;

			for (var i = 0; i < pdf_formate_length; i++) {
				var key = pdf_formate_name_id[i];
				var value = pdf_formate_name[i];
				js_set_value( key )
				$('#txt_task_short_name_'+key).val( value );				
			}			

        </script>
        
	<?
}
if($action == 'save_update_delete')
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
  
	if ($operation==0)  // Insert Here
	{
		$con = connect();

		$mst_id = return_next_id( "id", "pdf_formate_mst", 1);
		$dtls_id = return_next_id( "id", "pdf_formate_details", 1 );

		$field_array = "id, height, width, top_padding, bottom_padding, left_padding, right_padding, line_space, line_break, font_color, code_type, orientation, font, entry_form, inserted_by, insert_date, status_active, is_deleted";
		$field_array_details = "id, mst_id, field_id, font_size, serial_number, font_weight, field_name";

		$data_array = "(".$mst_id.", ".$txt_height.", ".$txt_width.", ".$txt_top_padding.", ".$txt_bottom_padding.", ".$txt_left_padding.", ".$txt_right_padding.", ".$txt_line_space.", ".$txt_line_break.", ".$txt_front_color.", ".$code_type.", ".$cbo_orientation.", ".$cbo_font.", ".$cbo_entry_form.", ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."', 1 , 0)";
		
	
		for ($i=1;$i<=$row_num; $i++)
		{
			$txtFieldName = "txtFieldName_".$i;
			$txtFieldId = "txtFieldId_".$i;
			$txtFontSize = "txtFontSize_".$i;
			$txtFontWeight = "txtFontWeight_".$i;
			$texSerialNumber = "texSerialNumber_".$i;
			if ($data_array_details!='') $data_array_details .=","; 
			$data_array_details .="(".$dtls_id.", ".$mst_id.", ".$$txtFieldId.", ".$$txtFontSize.", ".$$texSerialNumber.",".$$txtFontWeight.",".$$txtFieldName.")";
			$dtls_id++;
		}

		
		//echo "insert into pdf_formate_details ($field_array_details) values $data_array_details";die;
		$rID = sql_insert("pdf_formate_mst", $field_array, $data_array, 1);
		//echo "insert into pdf_formate_mst ($field_array) values $data_array";die;
		$rID2 = sql_insert("pdf_formate_details", $field_array_details, $data_array_details,1);
		//echo "5**".$rID.'**'.$rID2;oci_rollback($con);die;
		  
		if($rID && $rID2)
		{
			oci_commit($con);
			echo "0**".$mst_id;
		}
		else{
			oci_rollback($con);
			echo "5**".$mst_id;
		}
		
		disconnect($con);
		die;
  
	}
	// update here
	else if($operation==1)
	{
		// print_r(123);exit;
		$con = connect();
		$update_id = $txt_system_id;
		$dtls_id = return_next_id( "id", "pdf_formate_details", 1 );
		

		$field_array="height*width*top_padding*bottom_padding*left_padding*right_padding*line_space*line_break*font_color*code_type*orientation*font*entry_form*updated_by*updated_date*status_active*is_deleted";
		$field_array_details = "id, mst_id, field_id, font_size, serial_number, font_weight, field_name";
 
		$cbo_status = 1;
		$data_array="".$txt_height."*".$txt_width."*".$txt_top_padding."*".$txt_bottom_padding."*".$txt_left_padding."*".$txt_right_padding."*".$txt_line_space."*".$txt_line_break."*".$txt_front_color."*".$code_type."*".$cbo_orientation."*".$cbo_font."*".$cbo_entry_form."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*0";

		//	print_r($data_array);exit;
  
		$data_array_details='';
		for ($i=1;$i<=$row_num; $i++)
		{
			$txtFieldName = "txtFieldName_".$i;
			$txtFieldId = "txtFieldId_".$i;
			$txtFontSize = "txtFontSize_".$i;
			$txtFontWeight = "txtFontWeight_".$i;
			$texSerialNumber = "texSerialNumber_".$i;
			if ($data_array_details!='') $data_array_details .=","; 
			$data_array_details .="(".$dtls_id.", ".$update_id.", ".$$txtFieldId.", ".$$txtFontSize.", ".$$texSerialNumber.",".$$txtFontWeight.",".$$txtFieldName.")";
			$dtls_id++;
		}


		$rowID = sql_update("pdf_formate_mst", $field_array, $data_array, "id", $update_id, 1);
		$rID_2 = execute_query("delete from pdf_formate_details where mst_id=$update_id");
		//if ($rID_2) oci_commit($con);		
		$rowID3 = sql_insert("pdf_formate_details", $field_array_details, $data_array_details, 1);
		//echo "10**INSERT INTO pdf_formate_details (".$field_array_details.") VALUES ".$data_array_details;die;
 
		//echo $rID_2."*".$rowID.'*'.$rowID3;oci_rollback($con);die;
		  
		if($rID_2 && $rowID && $rowID3)
		{
			oci_commit($con);
			echo "1**".str_replace("'","",$update_id)."**0";
		}
		else
		{
			oci_rollback($con);
			echo "7**".str_replace("'","",$update_id)."**1";
		}
		
		disconnect($con);
		die;
	}
	else if($operation==2)
	{
		 
		$con = connect();
		$update_id = $txt_system_id;

		$rID_delete = execute_query("delete from pdf_formate_mst where id=$update_id");
		$rID_delete2 = execute_query("delete from pdf_formate_details where mst_id=$update_id");
		  
		if($rID_delete && $rID_delete2)
		{
			oci_commit($con);
			echo "2**".str_replace("'","",$update_id)."**0";
		}
		else
		{
			oci_rollback($con);
			echo "7**".str_replace("'","",$update_id)."**1";
		}
		
		disconnect($con);
		die;
	}
}

if($action == 'openpopup_pdf_formate')
{
 
	extract($_REQUEST);
	echo load_html_head_contents("Buyer Selection Form","../../../", 1, 1, $unicode,'',''); 	

	$codeTypeArr = ['1'=>'Bar Code','2'=>'QR Code'];
	$sql="select id, height, width, top_padding, bottom_padding, left_padding, right_padding, code_type, line_space, font_color from pdf_formate_mst where status_active=1 and is_deleted=0";
	?>
	<script> 
		function edit_pdf_formate( id ) 
		{			 
			document.getElementById('txt_selected_formate_id').value = id;
		    parent.emailwindow.hide();
		}
	</script> 
    	<div style="width:auto;" align="center" id="tna_task_list">
		<input type="hidden" name="txt_selected_formate_id" id="txt_selected_formate_id" value=""/>
    	<fieldset>
            <div style="width:680px;">
                <table align="center" width="680" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">
                    <thead>
                        <th width="30">SL</th>
                        <th width="80">System ID</th>
                        <th width="80">Height</th>
                        <th width="80">Width</th>
                        <th width="80">Top Padding</th>
                        <th width="80">Bottom Padding</th>
                        <th width="80">Left Padding</th>
                        <th width="80">Right Padding</th>
                        <th>Code Type</th>
                    </thead>
                </table>
            </div>
            <div style="overflow-y:scroll; max-height:200px; width:700px;"  align="center">
                <table align="center" id="tbl_task_list" width="680" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">  
                <?
                $i=1;
                $data_array = sql_select($sql);
                foreach($data_array as $data)
                {					
					?>
					<tr id="search<? echo $i;?>" onClick="edit_pdf_formate(<?= $data[csf('id')];?>);" style="cursor:pointer;">
						<td width="30" align="center"><?= $i; ?></td>
						<td width="80" align="center"><?= $data[csf('id')];?></td>
						<td width="80" align="center"><?= $data[csf('height')];?></td>
						<td width="80" align="center"><?= $data[csf('width')];?></td>
						<td width="80" align="center"><?= $data[csf('top_padding')];?></td>
						<td width="80" align="center"><?= $data[csf('bottom_padding')];?></td>
						<td width="80" align="center"><?= $data[csf('left_padding')];?></td>
						<td width="80" align="center"><?= $data[csf('right_padding')];?></td>
						<td align="center"><?= $codeTypeArr[$data[csf('code_type')]];?></td>
					</tr>
					<?
					$i++;
                }
                ?>
                </table>
            </div>
    	</fieldset>
    	</div>    
        
     	<script>
 			var tableFilters = 
			{
				// col_2: "none",
				display_all_text: " -- All --",
			}							
			setFilterGrid("tbl_task_list", -1, tableFilters);	
        </script>
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<?php
}

if ($action=="formate_data_from_search_popup")
{ 
	$data_array = sql_select("select id, height, width, top_padding, bottom_padding, left_padding, right_padding, code_type, line_space, line_break, font_color, orientation, font, entry_form from  pdf_formate_mst where id='$data'");
 
	foreach ($data_array as $row)
	{
		echo "document.getElementById('txt_system_id').value = '".$row[csf("id")]."';\n";  
		echo "document.getElementById('txt_height').value = '".$row[csf("height")]."';\n"; 
		echo "document.getElementById('txt_width').value = '".$row[csf("width")]."';\n"; 
		echo "document.getElementById('txt_top_padding').value = '".$row[csf("top_padding")]."';\n";  
		echo "document.getElementById('txt_bottom_padding').value = '".$row[csf("bottom_padding")]."';\n";  
		echo "document.getElementById('txt_left_padding').value = '".$row[csf("left_padding")]."';\n"; 
		echo "document.getElementById('txt_right_padding').value = '".$row[csf("right_padding")]."';\n";
		echo "document.getElementById('code_type').value = '".$row[csf("code_type")]."';\n";
		echo "document.getElementById('txt_line_space').value = '".$row[csf("line_space")]."';\n";
		echo "document.getElementById('txt_line_break').value = '".$row[csf("line_break")]."';\n";
		echo "document.getElementById('txt_front_color').value = '".$row[csf("font_color")]."';\n";
		echo "document.getElementById('cbo_orientation').value = '".$row[csf("orientation")]."';\n";
		echo "document.getElementById('cbo_font').value = '".$row[csf("font")]."';\n";
		echo "document.getElementById('cbo_entry_form').value = '".$row[csf("entry_form")]."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'pdf_formate_save', 1);\n";
		die; 
	}
}


if ($action=="field_data_from_search_popup")
{
	$data_array= sql_select("select id, mst_id, field_id, field_name, serial_number, font_size, font_weight from pdf_formate_details where mst_id ='$data' order by serial_number asc");
	//print_r($data_array);exit;
	?> 
    <table id="tbl_pdf_formate" class="rpt_table" rules="all" border="1" width="100%">
		<thead>
		    <tr>
				<th width="20" class="must_entry_caption">Sl.</th>
				<th class="must_entry_caption">Field Name</th>
				<th width="50">Front Size</th>
				<th width="80">Front Weight</th>
				<th width="100" class="must_entry_caption">Serial Number </th>
				<th width="68"></th>
			</tr>
		</thead>

		<tbody>
		    <?
			$fontWeightArr = array(''=>'normal','B'=>'bold');
			$i = '';
			foreach($data_array as $row ){
			   	$i++;
		    ?>
			<tr>
				<td class="serial" align="center"><?= $i; ?></td>
				<td align="center">
					<input type="text" name="txtFieldName_<?= $i; ?>" id="txtFieldName_<?= $i; ?>" style="width:95%" class="text_boxes" placeholder="Double Click To Search" onDblClick="PDFFormate($(this).attr('id'),'Task Name Search')" readonly value="<?= $row[csf('field_name')]; ?>"/>
					<input type="hidden" id="txtFieldId_<?= $i; ?>" name="txtFieldId_<?= $i; ?>" value="<?= $row[csf('FIELD_ID')]; ?>"/>
				</td>
				<td align="center">
					<input type="text" name="txtFontSize_<?= $i; ?>" class="text_boxes" style="width:50px" id="txtFontSize_<?= $i; ?>" value="<?= $row[csf('FONT_SIZE')]; ?>"/>
				</td>

				<td align="center">
					<? 
						echo create_drop_down("txtFontWeight_$i", 150, $fontWeightArr, "", "", "", 1, $row[csf('FONT_WEIGHT')] );
					?>
				</td>


				<td align="center">
					<input type="text" name="texSerialNumber_<?= $i; ?>" id="texSerialNumber_<?= $i; ?>" style="width:80px" class="text_boxes" placeholder="Serial Number" value="<?= $row[csf('SERIAL_NUMBER')] ?>"/>
				</td>
				
				<td>
				    <input type="button" id="increase_<?= $i; ?>" name="increase_<?= $i; ?>" style="width:30px" class="formbutton" value="+" onClick="addRow(<? echo $i; ?>);" />&nbsp;
					<input type="button" id="decrease_<?= $i; ?>" name="decrease_<?= $i; ?>" style="width:30px" class="formbutton" value="-" onClick="deleteRow(<? echo $i; ?>);" />
				</td>
			</tr>
			<?
			}
			?>
		</tbody> 
	</table>

	<?
}
?>