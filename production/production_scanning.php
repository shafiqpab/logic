<?
/*--- ----------------------------------------- Comments
Purpose			: 					
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	22-09-2013
Updated by 		: 		
Update date		: 		   
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
echo load_html_head_contents("Production Scanning", "../", 1,1, $unicode,1,'');
?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
var permission='<? echo $permission; ?>';

<?php
	$color_array=return_library_array( "select id,color_name from  lib_color", "id", "color_name"  );
	$jscolor_array= json_encode($color_array); 
	echo "var color_array = ". $jscolor_array . ";\n"; 
	
	$size_array=return_library_array( "select id,size_name from  lib_size", "id", "size_name"  );
	$jssize_array= json_encode($size_array); 
	echo "var size_array = ". $jssize_array . ";\n"; 
	
	$sew_oper_array=return_library_array( "select id,operation_name from  lib_sewing_operation_entry", "id", "operation_name"  );
	$jssew_oper_array= json_encode($sew_oper_array); 
	echo "var sew_oper_array = ". $jssew_oper_array . ";\n";
	
	$jsbody_part_array= json_encode($body_part); 
	echo "var body_part_array = ". $jsbody_part_array . ";\n";
	
	$jsgarments_item_array= json_encode($garments_item); 
	echo "var garments_item_array = ". $jsgarments_item_array . ";\n";
	
	
	$company_name_array=return_library_array( "select id,company_name from  lib_company", "id", "company_name"  );
	$jscompany_name_array= json_encode($company_name_array); 
	echo "var company_name_array = ". $jscompany_name_array . ";\n";
	
	$buyer_name_array=return_library_array( "select id,buyer_name from  lib_buyer", "id", "buyer_name"  );
	$jsbuyer_name_array= json_encode($buyer_name_array); 
	echo "var buyer_name_array = ". $jsbuyer_name_array . ";\n";
	
	$data_array=sql_select("SELECT a.job_no,a.buyer_name,b.po_number,a.style_ref_no,a.company_name,b.id as bid,c.id as cid,c.size_number_id,c.color_number_id,c.item_number_id FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.shiping_status<>3");
	$po_details_array=array();
	$po_id=array();
	foreach($data_array as $row)
	{
		$po_details_array[$row[csf("cid")]]['job_no']=$row[csf("job_no")];
		$po_details_array[$row[csf("cid")]]['buyer_name']=$row[csf("buyer_name")];
		$po_details_array[$row[csf("cid")]]['company_name']=$row[csf("company_name")];
		$po_details_array[$row[csf("cid")]]['style_ref_no']=$row[csf("style_ref_no")];
		$po_details_array[$row[csf("cid")]]['poid']=$row[csf("bid")];
		$po_details_array[$row[csf("cid")]]['size_number_id']=$row[csf("size_number_id")];
		$po_details_array[$row[csf("cid")]]['color_number_id']=$row[csf("color_number_id")];
		$po_details_array[$row[csf("cid")]]['item_number_id']=$row[csf("item_number_id")];
		$po_details_array[$row[csf("cid")]]['po_number']=$row[csf("po_number")];
		
		$po_id[]=$row[csf("cid")];
		$job_no[]=$row[csf("job_no")];
	}
	$jspo_details_array= json_encode($po_details_array);
	echo "var po_details_array = ". $jspo_details_array . ";\n";
	$po_ids=implode(",",$po_id);
	$job_nos="'".implode("','",$job_no)."'";
	
	$data_array=sql_select("select mst_id,production_type,color_size_break_down_id,production_qnty from  pro_garments_production_dtls where color_size_break_down_id in ($po_ids) ");
	$production_arr=array();
	$prod_arr=array();
	foreach($data_array as $row)
	{
		$production_arr[$row[csf("mst_id")]][$row[csf("color_size_break_down_id")]]=$row[csf("production_qnty")];
		$prod_arr[]=$row[csf("mst_id")];
	}
	$jsproduction_arr= json_encode($production_arr); 
	echo "var production_arr = ". $jsproduction_arr . ";\n"; 
	$prod_arrs=implode(",",$prod_arr);
	
	$data_array=sql_select("select a.id,body_part_id,operation_id,oparetion_type_id,a.total_smv from ppl_gsd_entry_dtls a,ppl_gsd_entry_mst b where b.id=a.mst_id and po_job_no in ( $job_nos )");
	$gsd_arr=array();
	foreach($data_array as $row)
	{
		$gsd_arr[$row[csf("id")]]['body_part_id']=$row[csf("body_part_id")];
		$gsd_arr[$row[csf("id")]]['operation_id']=$row[csf("operation_id")];
		$gsd_arr[$row[csf("id")]]['total_smv']=$row[csf("total_smv")];
	}
	$jsgsd_arr= json_encode($gsd_arr); 
	echo "var gsd_arr = ". $jsgsd_arr . ";\n"; 
	
	$bundle_array=return_library_array( "select id, pcs_per_bundle from pro_bundle_dtls", "id", "pcs_per_bundle"  );
	
	$already_barcode_scaning=return_library_array( "select operation_barcode,operation_barcode from pro_scanning_operation", "operation_barcode", "operation_barcode"  );
	//echo "select a.op_code,a.bundle_dtls,a.bundle_mst,a.prod_mst,a.style,a.gsd_mst,a.gsd_dtls,b.pro_gmts_pro_id from  pro_operation_bar_code a, pro_bundle_mst b where a.id=b.bundle_mst and  a.prod_mst in ($po_ids) ";
	$data_array=sql_select("select a.op_code,a.bundle_dtls,a.bundle_mst,a.prod_mst,a.style,a.gsd_mst,a.gsd_dtls,b.pro_gmts_pro_id,b.pcs_per_bundle from pro_operation_bar_code a, pro_bundle_mst b where b.id=a.bundle_mst and a.prod_mst in ($po_ids)");
	$operation_barcode=array(); 
	foreach($data_array as $row)
	{
		//if(!in_array($row[csf("op_code")],$already_barcode_scaning))
		//{
			$operation_barcode[$row[csf("op_code")]]['bundle_dtls']=$row[csf("bundle_dtls")];
			$operation_barcode[$row[csf("op_code")]]['bundle_mst']=$row[csf("bundle_mst")];
			$operation_barcode[$row[csf("op_code")]]['prod_mst']=$row[csf("prod_mst")];
			$operation_barcode[$row[csf("op_code")]]['style']=$row[csf("style")];
			$operation_barcode[$row[csf("op_code")]]['gsd_mst']=$row[csf("gsd_mst")];
			$operation_barcode[$row[csf("op_code")]]['gsd_dtls']=$row[csf("gsd_dtls")];
			$operation_barcode[$row[csf("op_code")]]['pro_gmts_pro_id']=$row[csf("pro_gmts_pro_id")];
			//$operation_barcode[$row[csf("op_code")]]['pcs_per_bundle']=$row[csf("pcs_per_bundle")];
			$operation_barcode[$row[csf("op_code")]]['pcs_per_bundle']=$bundle_array[$row[csf("bundle_dtls")]];
			//$bnd_mst_arr[]=$row[csf("bundle_mst")];
		//}
	}
	$operation_barcode= json_encode($operation_barcode);
	echo "var operation_barcode = ". $operation_barcode . ";\n";
	//$bnd_mst=implode(",",$bnd_mst_arr);
	 
?>

	function openmypage_workercode()
	{ 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/production_scanning_controller.php?action=worker_code_popup','Worker Code Popup', 'width=850px,height=350px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("worker_id");
			var response=theemail.value.split('__');
			
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_update_id").value='';
				document.getElementById("txt_worker_code").value=response[1];
				document.getElementById("txt_worker_name").value=response[2];
				
				document.getElementById("cbo_designation").value=response[3];
				document.getElementById("cbo_location_id").value=response[4];
				document.getElementById("cbo_line_num").value=response[5];
				//document.getElementById("cbo_floor_id").value=response[6]; 
				document.getElementById("txt_id_card_no").value=response[6];
				
				$('#txt_prod_date').focus();
				create_row(1);
				set_button_status(0, permission, 'fnc_production_scanning',1);
				release_freezing();
			}
		}
	}

	function update_info_popup()
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/production_scanning_controller.php?action=upodate_info_popup','Search System Code', 'width=850px,height=350px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("worker_id");
			var response=theemail.value.split('__');
			
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_update_id").value=response[0];
				document.getElementById("txt_worker_code").value=response[1];
				document.getElementById("txt_worker_name").value=response[2];
				var barcode_upd=response[3];
				document.getElementById("cbo_designation").value=response[4];
				document.getElementById("cbo_location_id").value=response[5];
				document.getElementById("cbo_line_num").value=response[6];
				document.getElementById("cbo_floor_id").value=response[7];
				document.getElementById("txt_prod_date").value=response[8]; 
				document.getElementById("txt_id_card_no").value=response[9];
				
				barcode_upd=barcode_upd.split(",");
				//create_row(1);
				 $('#scanning_tbl tbody tr:not(:last)').remove();
				//$('#scanning_tbl tbody tr:first ').remove();
				//var old=$('#scanning_tbl tbody').html();
				//$('#scanning_tbl tbody').html('');
				//$('#scanning_tbl tbody').html(new_row+old);
				//$('#txt_bar_code_num').focus();
				
				for(var k=0; k<barcode_upd.length; k++)
				{
					create_row( barcode_upd[k] );
					
				}
				set_button_status(1, permission, 'fnc_production_scanning',1);
				release_freezing();
			}
		}
	}
	
	function fnc_production_scanning( operation )
	{
	 	if( form_validation('txt_worker_code*txt_prod_date','Worker Code*Production Date')==false)
		{
			return; 
		}
		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_prod_date').val(), current_date)==false)
		{
			alert("Production Date Can not Be Greater Than Current Date");
			return;
		}	
		
		var num_row =$('#scanning_tbl tbody tr').length; 
		if(num_row<2) 
		{
			alert('No data');
			return;
		}
		var data1="action=save_update_delete&operation="+operation+"&num_row="+num_row+get_submitted_data_string('txt_worker_code*txt_prod_date*txt_update_id',"../");
		var data2='';
		var i=1;
		for(var i=1; i<=num_row; i++)
		{
			if($('#txtbarcode_'+i).html()!="")
			{
				if(data2=="") data2=$('#txtbarcode_'+i).html();
				else data2=data2+"__"+$('#txtbarcode_'+i).html();
			}
		}
		var data=data1+'&bundle_operarion_num='+data2;
		freeze_window(operation);
		http.open("POST","requires/production_scanning_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_production_scanning_response;
	}

	function fnc_production_scanning_response()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);
			
			if(response[0]==15) 
			{ 
				 setTimeout('fnc_production_scanning('+ reponse[1]+')',8000); 
			}	
			else if((response[0]==0 || response[0]==1))
			{
				document.getElementById('txt_update_id').value = response[1];
				set_button_status(1, permission, 'fnc_production_scanning',1);
			}
			release_freezing();
		}
	}
	
	var scanned_barcode=new Array();
	function create_row( update_values )
	{
		var num_row =$('#scanning_tbl tbody tr').length; 
		
		if( update_values==0 )
			var bar_code = document.getElementById('txt_bar_code_num').value;
		else if( update_values==1 )
		{
			$('#scanning_tbl tbody').html('');
			var num_rowl =1; //$('#scanning_tbl tbody tr').length;
			var new_row ='<tr id="tbl_row_id'+num_rowl+'"><td width="50" id="txtslnum_'+num_rowl+'">'+num_rowl+'</td><td width="100" id="txtbarcode_'+num_rowl+'"></td><td width="150" id="txtopernamebody_'+num_rowl+'" ></td><td width="100" id="txtbundlenum_'+num_rowl+'" ></td><td width="80" id="txtprodqnty_'+num_rowl+'" ></td><td width="100" id="txtcolorsize_'+num_rowl+'" ></td><td width="70" id="txtsamperoperation_'+num_rowl+'" ></td><td width="100" id="txtordnum_'+num_rowl+'" ></td><td width="100" id="txtstyleref_'+num_rowl+'" ></td><td width="100" id="txtgmtitem_'+num_rowl+'" ></td><td width="100" id="txtbuyer_'+num_rowl+'" ></td><td id="txtcompany_'+num_rowl+'" width="100" ></td><td><input type="button" value=" - " class="formbutton" style="width:35px"  id="btn_'+num_rowl+'"  /></td></tr>';
			$('#scanning_tbl tbody').html(new_row);
			return;
		}
		else
			var bar_code = update_values; //document.getElementById('txt_bar_code_num').value;
		
		document.getElementById('txt_bar_code_num').value='';
		//alert(operation_barcode[bar_code]);
		if(!operation_barcode[bar_code])
		{ 	
			$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
			 {
				$('#messagebox_main', window.parent.document).html('Code is Not Valid.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
			 });
			return; 
		}
		
		if( jQuery.inArray( bar_code, scanned_barcode )>-1) {   return; alert('Sorry! Duplicate Entry.'); }
		scanned_barcode.push(bar_code);
 		
		document.getElementById('txt_bar_code_num').value='';
		var gsd=sew_oper_array[gsd_arr[operation_barcode[bar_code]['gsd_dtls']]['operation_id']]+";"+body_part_array[gsd_arr[operation_barcode[bar_code]['gsd_dtls']]['body_part_id']];
		
		var bundle_num=bar_code.substr(0,11);
		var prod_qnty=operation_barcode[bar_code]['pcs_per_bundle'];
		//production_arr[operation_barcode[bar_code]['pro_gmts_pro_id']][operation_barcode[bar_code]['prod_mst']]
		var color=color_array[po_details_array[operation_barcode[bar_code]['prod_mst']]['color_number_id']];
		var size=size_array[po_details_array[operation_barcode[bar_code]['prod_mst']]['size_number_id']];
		var order=po_details_array[operation_barcode[bar_code]['prod_mst']]['po_number'];
		var buyer_name=buyer_name_array[po_details_array[operation_barcode[bar_code]['prod_mst']]['buyer_name']];
		var company_name=company_name_array[po_details_array[operation_barcode[bar_code]['prod_mst']]['company_name']];
		var style_ref_no=po_details_array[operation_barcode[bar_code]['prod_mst']]['style_ref_no'];
		var gmt_item=garments_item_array[po_details_array[operation_barcode[bar_code]['prod_mst']]['item_number_id']]; 
		var sam=gsd_arr[operation_barcode[bar_code]['gsd_dtls']]['total_smv'];
		var num_rowl=(num_row*1)+1;
		
		var new_row ='<tr id="tbl_row_id'+num_rowl+'"><td width="50" id="txtslnum_'+num_rowl+'">'+num_rowl+'</td><td width="100" id="txtbarcode_'+num_rowl+'"></td><td width="150" id="txtopernamebody_'+num_rowl+'" ></td><td width="100" id="txtbundlenum_'+num_rowl+'" ></td><td width="80" id="txtprodqnty_'+num_rowl+'" ></td><td width="100" id="txtcolorsize_'+num_rowl+'" ></td><td width="70" id="txtsamperoperation_'+num_rowl+'" ></td><td width="100" id="txtordnum_'+num_rowl+'" ></td><td width="100" id="txtstyleref_'+num_rowl+'" ></td><td width="100" id="txtgmtitem_'+num_rowl+'" ></td><td width="100" id="txtbuyer_'+num_rowl+'" ></td><td id="txtcompany_'+num_rowl+'" width="100" ></td><td><input type="button" value=" - " class="formbutton" style="width:35px"  id="btn_'+num_rowl+'"  /></td></tr>';
		
		var new_row =new_row+'<tr id="tbl_row_id'+num_row+'"><td width="50" id="txtslnum_'+num_row+'">'+num_row+'</td><td width="100" id="txtbarcode_'+num_row+'">'+bar_code+'</td><td width="150" id="txtopernamebody_'+num_row+'" >'+gsd+'</td><td width="100" id="txtbundlenum_'+num_row+'" >'+bundle_num+'</td><td width="80" id="txtprodqnty_'+num_row+'" >'+prod_qnty+'</td><td width="100" id="txtcolorsize_'+num_row+'" >'+color+';'+size+'</td><td width="70" id="txtsamperoperation_'+num_row+'" >'+sam+'</td><td width="100" id="txtordnum_'+num_row+'" >'+order+'</td><td width="100" id="txtstyleref_'+num_row+'" >'+style_ref_no+'</td><td width="100" id="txtgmtitem_'+num_row+'" >'+gmt_item+'</td><td width="100" id="txtbuyer_'+num_row+'" >'+buyer_name+'</td><td id="txtcompany_'+num_row+'" width="100" >'+company_name+'</td><td><input type="button" value=" - " onclick="clear_row('+num_row+')" class="formbutton" style="width:35px"  id="btn_'+num_row+'"  /></td></tr>';  
		  
		$('#scanning_tbl tbody tr:first').remove();
		var old=$('#scanning_tbl tbody').html();
	 	$('#scanning_tbl tbody').html('');
		$('#scanning_tbl tbody').html(new_row+old);
		$('#txt_bar_code_num').focus();
	}
	
$('#txt_bar_code_num').live('keydown', function(e) {
    if (e.keyCode === 13) {
        e.preventDefault();
        create_row( 0 );
    }
});

$('#txt_worker_code').live('keydown', function(e) {
    if (e.keyCode === 13) {
        e.preventDefault();
        get_php_form_data($('#txt_worker_code').val()+"**1", "populate_employee_info_data", "requires/production_scanning_controller" );
    }
});

$('#txt_id_card_no').live('keydown', function(e) {
    if (e.keyCode === 13) {
        e.preventDefault();
        get_php_form_data($('#txt_id_card_no').val()+"**2", "populate_employee_info_data", "requires/production_scanning_controller" );
    }
});


function clear_row( rid )
{
	//scanned_barcode.pop($('#txtbarcode_'+rid).html());
	$('#tbl_row_id'+rid +' td').each(function(index, element) {
		$(this).html('');
    });
	
	$('#tbl_row_id'+rid +' td').hide();
}
</script>
</head>

<body onLoad="set_hotkey()">
 <div align="center" style="width:100%;">
   <? echo load_freeze_divs ("../",$permission); ?>
    <form name="prodscanning_1" id="prodscanning_1"  autocomplete="off"  >
	<fieldset style="width:880px;">
    <legend>Production Scanning</legend>
        <table cellpadding="0" cellspacing="2" width="100%">
        	<tr>
            	<td width="110px" class="must_entry_caption">Worker Code</td>
            	<td>
                	<input type="text" name="txt_worker_code" id="txt_worker_code" class="text_boxes" placeholder="Browse" style="width:140px;" onDblClick="openmypage_workercode();"  />
				</td>
            	<td width="110px">Worker Name</td>
            	<td>
                	<input type="text" name="txt_worker_name" id="txt_worker_name" class="text_boxes" style="width:140px;" readonly />
				</td>
            	<td width="110px">Designation</td>
            	<td>
                	<?
						echo create_drop_down( "cbo_designation",150,"select designation_id,designation_name from lib_employee where status_active =1 and is_deleted=0 order by designation_name","designation_id,designation_name", 1, "-- Select Designation --", $selected,"",1);
                    ?>
				</td>
			</tr>
        	<tr>
            	<td width="110px">Line Number</td>
            	<td>
                	<?
						echo create_drop_down( "cbo_line_num",150,"select line_no,line_name from lib_employee where status_active =1 and is_deleted=0 order by line_name","line_no,line_name", 1, "-- Select Line --", $selected,"",1);
                    ?>
				</td>
            	<td width="110px">Location</td>
            	<td>
					<?
						echo create_drop_down( "cbo_location_id",150,"select id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected,"",1);
                    ?>
				</td>
            	<td width="110px">Floor Name</td>
            	<td id="floor_td">
					<?
						echo create_drop_down( "cbo_floor_id",150,"select id,floor_name from  lib_prod_floor where status_active =1 and is_deleted=0 order by floor_name","id,floor_name", 1, "-- Select Floor --", "", "",1);	
                    ?>
				</td>
			</tr>
            
            <tr>
            	<td class="must_entry_caption">Production Date</td>
            	<td>
                	<input type="text" name="txt_prod_date" id="txt_prod_date" class="datepicker" style="width:140px;" onChange="$('#txt_bar_code_num').focus();"  readonly />
				</td>
                <td>System ID</td>
            	<td>
                	<input type="text" name="txt_update_id" id="txt_update_id" class="text_boxes" style="width:140px;" placeholder="Double Click" onDblClick="update_info_popup()"  readonly />
				</td>
            	<td>ID Card No</td>
            	<td>
                	<input type="text" name="txt_id_card_no" id="txt_id_card_no" class="text_boxes" style="width:140px;" placeholder="Browse" onDblClick="openmypage_workercode();"/>
				</td> 
			</tr>
            
            <tr>
            	<td colspan="6" height="12"></td>
            </tr>
            <tr>
                <td width="140" align="center" colspan="6"><strong>Bar Code Number</strong>&nbsp;&nbsp;
                    <input type="text" name="txt_bar_code_num" id="txt_bar_code_num" class="text_boxes_numeric" style="width:170px;"/>
                </td>
            </tr>
            <tr>
            	<td colspan="6">&nbsp;</td>
            </tr>
        </table>
        </fieldset>
		<br>
        <fieldset style="width:1240px;text-align:left">
         <style>
		 	 #scanning_tbl_top thead th
			 {
				 background-image: -moz-linear-gradient( rgb(194,220,255) 10%, rgb(136,170,214) 96%);
				border: 1px solid #8DAFDA;
				color: #444;
				font-size: 12px;
				font-weight: bold;
				text-align:center;
				line-height:12px;
				height:25px;
			 }
			 
		 	#scanning_tbl tbody tr td
			{
				background-color:#FFF;
				color:#000;
				
				border: 1px solid #666666;
				text-align:center;
				line-height:12px;
				height:21px;
				overflow:auto;
			}
		 </style>
        <table cellpadding="0" width="1200" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
        	<thead>
            	<th width="50" align="center">SL</th>
                <th width="100" align="center">Bar Code</th>
                <th width="150" align="center">Operation Name & Body Part</th>
                <th width="100" align="center">Bundle Num</th>
                <th width="80" align="center">Prod. Qnty</th>
                <th width="100" align="center">Color & Size</th>
                <th width="70" align="center">SAM Per Operation</th>
                <th width="100" align="center">Ord. Num</th>
                <th width="100" align="center">Style Ref.</th>
                <th width="100" align="center">Gmt. Item</th>
                <th width="100" align="center">Buyer</th>
                <th align="center" width="100" >Company</th>
                <th align="center" ></th>
            </thead>
         </table>
         
         <div style="width:1230px; max-height:250px; min-height:150px; overflow:auto" align="left">
         <table cellpadding="0" cellspacing="0" width="1200" border="1" id="scanning_tbl" rules="all" class="rpt_table">
            <tbody>
            	<tr id="tbl_row_id1" align="center">
                	<td width="50" id="txtslnum_1"></td>
                	<td width="100" id="txtbarcode_1"></td>
                	<td width="150" id="txtopernamebody_1"></td>
                	<td width="100" align="center" id="txtbundlenum_1"></td>
                	<td width="80" align="center" id="txtprodqnty_1"></td>
                	<td width="100" align="center" id="txtcolorsize_1"></td>
                	<td width="70" align="center" id="txtsamperoperation_1"></td>
                	<td width="100" align="center" id="txtordnum_1"></td>
                	<td width="100" align="center" id="txtstyleref_1"></td>
                	<td width="100" align="center" id="txtgmtitem_1"></td>
                	<td width="100" align="center" id="txtbuyer_1"></td>
                	<td align="center"  width="100"  id="txtcompany_1"></td>
                    <td align="right" >
                    	<input type="button" value=" - " class="formbutton" onBlur="clear_row( 1 )" style="width:35px" />
                    </td>
                </tr>
            </tbody>
        </table>
        </div>
 	    <br>
        <table style="width:1200px;" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
            <tr>
                <td align="center" class="button_container">
                    <? 
                       echo load_submit_buttons($permission,"fnc_production_scanning",0,0,"reset_form('prodscanning_1','','','','$(\'#scanning_tbl tr:not(:first)\').remove();')",1);
                    ?>
                </td>
            </tr>  
        </table>
    </fieldset>
    </form>
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
