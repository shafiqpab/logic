<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Machine List Library
Functionality	:	 
JS Functions	:
Created by		:	CTO 
Creation date 	: 	08-10-2012
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
echo load_html_head_contents("Sewing Operation Entry", "../../", 1, 1,$unicode,'','');
 ?>	
 
<script language="javascript">
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission='<? echo $permission; ?>';


function fnc_machine_name_entry( operation )
{
  //txt_group
  var catagory_id=$("#cbo_catagory").val();
  var cbo_isSubcon=$("#cbo_isSubcon").val();
   if(catagory_id==25)
   {
	   if (form_validation('txt_group','Group')==false)
		{
			return;   
		}
   }
  var statusValidation=1;
  if(cbo_isSubcon==1)
	{
		 if (form_validation('cbo_company_name*cbo_location_name*txt_machine_no*cbo_catagory*cbo_supplier','Company Name*Location Name*Machine No*Machine Category*S.con Part')==false)
		 {
		 	statusValidation=0;
		 	return;
		 }
	}
	else
	{
		  if (form_validation('cbo_company_name*cbo_location_name*cbo_floor_name*txt_machine_no*cbo_catagory','Company Name*Location Name*Floor Name*Machine No*Machine Category')==false)
		  {
		  	statusValidation=0;
		  	return;
		  }
	}

  
  if (statusValidation==0)
	{
		return;  //'txt_operation*txt_rate*cbo_uom*cbo_resource*txt_operator_smv*txt_helper_smv*txt_total_smv
	}
	else
	{
		eval(get_submitted_variables('cbo_company_name*cbo_location_name*cbo_floor_name*txt_machine_no*cbo_catagory*txt_group*txt_fab_group*fab_group_hid_id*txt_dia_width*txt_gauge*txt_extra_cylinder*txt_no_of_feeder*txt_attachment*txt_prod_capacity*cbo_capacity_uom*txt_brand*txt_origin*txt_purchase_date*txt_purchase_cost*txt_accumulated_dep*txt_depreciation_rate*cbo_depreciation_method*cbo_status*txt_remarks*cbo_mc_type*update_id*cbo_isSubcon*txt_efficiency*txt_pipe_weight*cbo_supplier'));
		
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_floor_name*txt_machine_no*cbo_catagory*txt_group*txt_fab_group*fab_group_hid_id*txt_dia_width*txt_gauge*txt_extra_cylinder*txt_no_of_feeder*txt_attachment*txt_prod_capacity*cbo_capacity_uom*txt_brand*txt_origin*txt_purchase_date*txt_purchase_cost*txt_accumulated_dep*txt_depreciation_rate*cbo_depreciation_method*cbo_status*txt_remarks*cbo_mc_type*txt_seq_no*update_id*cbo_isSubcon*cbo_machinetype*txt_efficiency*txt_norsel_weight_api*txt_norsel_printer_api*txt_norsel_printer*txt_pipe_weight*txt_cycle_time*txt_machine_capacity*cbo_supplier',"../../");
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/machine_name_entry_controller.php", true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_machine_name_entry_reponse;
	}
}

function fnc_machine_name_entry_reponse()
{
	if(http.readyState == 4) 
	{
		//alert(http.responseText);
		var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(reponse[0]);
		//document.getElementById('update_id').value  = reponse[2];
		show_list_view('','machine_entry_list_view','machine_entry_list','requires/machine_name_entry_controller','setFilterGrid("list_view",-1)');
		set_button_status(0, permission, 'fnc_machine_name_entry',1);
		reset_form('machinename_1','','');
		release_freezing();
	}
}

function fnc_mc_cpm()
{
	var update_id = $('#update_id').val();
	if(update_id=="")
	{
		alert("Save The Machine First.");
		return;
	}
	var page_link='requires/machine_name_entry_controller.php?action=mccpm_popup&update_id='+update_id;
	var title='M/C CPM Entry Info';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=330px,height=300px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
	}
}	
function openmypage_fabric_group()
	{
		var page_link="requires/machine_name_entry_controller.php?action=fabric_group_popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Fabric group Popup", 'width=480px,height=350px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var hidfabgroupid=this.contentDoc.getElementById("hidfabgroupid").value;
			var hidfabgroupname=this.contentDoc.getElementById("hidfabgroupname").value;
			$('#fab_group_hid_id').val(hidfabgroupid);
			$('#txt_fab_group').val(hidfabgroupname);
		}
	}

	function fnc_sConPartActive(id)
	{
		if(id==1)
		{
			$('#cbo_supplier').attr('disabled',false);
		}
		else
		{
			$('#cbo_supplier').attr('disabled',true);
			$('#cbo_supplier').val(0);
		}
	}
</script>
</head>
<body onLoad="set_hotkey();">
<div align="center" style="width:100%">
         <? echo load_freeze_divs ("../../",$permission);  ?>
	<fieldset style="width:1000px;">
		<legend>Machine Name Info</legend>
		<form name="machinename_1" id="machinename_1" autocomplete="off">
			<table align="center" width="1000">
				<tr>
          <td>
                    	<tr>
                    		<td width="80" class="must_entry_caption">Company</td>
							<td width="100"><? echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name",'id,company_name', 1,"--- Select Company ---",'' ,"load_drop_down( 'requires/machine_name_entry_controller', this.value, 'load_drop_down_location', 'location' ); show_list_view(this.value,'machine_entry_list_view','machine_entry_list','../production/requires/machine_name_entry_controller','setFilterGrid(\'list_view\',-1)')" ); ?>
							</td>

							<td class="must_entry_caption">Location</td>
							<td  id="location"><? echo create_drop_down( "cbo_location_name", 140, $blank_array,'', 1, '--- Select Location ---', 0, "load_drop_down( 'requires/machine_name_entry_controller', this.value, 'load_drop_down_floor', 'floor' )"  ); ?></td>

							<td class="must_entry_caption">Floor No</td>
							<td  id="floor"><? echo create_drop_down( "cbo_floor_name", 140, "$blank_array",'', 1, '--- Select Floor ---' ); ?></td>

							<td class="must_entry_caption">Machine No</td>
							<td><input type="text" name="txt_machine_no" id="txt_machine_no" class="text_boxes" style="width:127px"></td>
                    	</tr>

                    	<tr>
                    		<td class="must_entry_caption">Category</td>
							<td><? echo create_drop_down( "cbo_catagory", 140, $machine_category,'', '', "" ); ?></td>

							<td>Prod. Capacity</td>
							<td><input type="text" name="txt_prod_capacity" id="txt_prod_capacity" class="text_boxes_numeric" style="width:40px"/>&nbsp;Effi%<input type="text" name="txt_efficiency" id="txt_efficiency" class="text_boxes_numeric" style="width:48px"/></td>

							<td>Capacity UOM</td>
							<td><? echo create_drop_down( "cbo_capacity_uom", 140, $unit_of_measurement,'', '', "" ); ?></td>
							
							<td>Brand</td>
							<td><input type="text" name="txt_brand" id="txt_brand" class="text_boxes" style="width:127px"></td>
                    	</tr>

                    	<tr>
                    		<td>Origin</td>
							<td><input type="text" name="txt_origin" id="txt_origin" class="text_boxes" style="width:127px"></td>
							<td>Group</td>
							<td><input type="text" name="txt_group" id="txt_group" class="text_boxes" style="width:127px"></td>
							<td>Dia/Width</td>
							<td><input type="text" name="txt_dia_width" id="txt_dia_width" class="text_boxes_numeric" style="width:127px"></td>
							<td>Gauge</td>
							<td><input type="text" name="txt_gauge" id="txt_gauge" class="text_boxes" style="width:127px"></td>
                    	</tr>

                    	<tr>
                    		<td>Extra Cylinder</td>
							<td><input type="text" name="txt_extra_cylinder" id="txt_extra_cylinder" class="text_boxes" style="width:127px"></td>

							<td>No of feeder/Tube</td>
							<td><input type="text" name="txt_no_of_feeder" id="txt_no_of_feeder" class="text_boxes" style="width:127px"></td>

							<td>Cycle Time [Min]</td>
							<td><input type="text" name="txt_cycle_time" id="txt_cycle_time" class="text_boxes_numeric" style="width:127px"></td>
							
							<td>Machine Type</td>
							<td><? echo create_drop_down( "cbo_machinetype", 140, $machine_type,'', '', "" ); ?></td>
                    	</tr>

                    	<tr>
                    		<td>Attachment</td>
							<td><input type="text" name="txt_attachment" id="txt_attachment" class="text_boxes" style="width:127px"></td>

							<td>Norsel weight scale API</td>
							<td><input type="text" name="txt_norsel_weight_api" id="txt_norsel_weight_api" class="text_boxes" style="width:127px"></td>

							<td>Norsel printer API</td>
							<td><input type="text" name="txt_norsel_printer_api" id="txt_norsel_printer_api" class="text_boxes" style="width:127px"></td>
							
							<td>Norsel printer</td>
							<td><input type="text" name="txt_norsel_printer" id="txt_norsel_printer" class="text_boxes" style="width:127px"></td>
                    	</tr>

                    	<tr>
                    		<td>Accumulated Dep.</td>
							<td><input type="text" name="txt_accumulated_dep" id="txt_accumulated_dep" class="text_boxes_numeric" style="width:127px"></td>

							<td>Depreciation Rate</td>
							<td><input type="text" name="txt_depreciation_rate" id="txt_depreciation_rate" class="text_boxes_numeric" style="width:127px"></td>

							<td>Depreciation Method</td>
							<td><? echo create_drop_down( "cbo_depreciation_method", 140, $depreciation_method,'', '', "" ); ?></td>
							
							<td>Sub-Con Out-Bound</td>
							<td><? echo create_drop_down( "cbo_isSubcon", 140, $yes_no,'', '', '',2,"fnc_sConPartActive(this.value);" ); ?></td>
                    	</tr>

                    	<tr>
                    		<td>Purchase Date</td>
							<td><input type="text" name="txt_purchase_date" id="txt_purchase_date" class="datepicker" style="width:127px;"></td>

							<td>Purchase Cost</td>
							<td><input type="text" name="txt_purchase_cost" id="txt_purchase_cost" class="text_boxes_numeric"  style="width:50px">&nbsp;&nbsp; <input type="button" class="image_uploader" style="width:68px" value="M/C CPM" onClick="fnc_mc_cpm();"><input type="hidden" name="txt_mccpm" id="txt_mccpm" class="text_boxes" style="width:42px"></td>

							<td>Pipe weight</td>
							<td><input type="text" name="txt_pipe_weight" id="txt_pipe_weight" class="text_boxes_numeric" style="width:127px"></td>
							
							<td>Sequence No</td>
							<td><input type="text" name="txt_seq_no" id="txt_seq_no" class="text_boxes_numeric" style="width:127px"></td>
                    	</tr>

                    	<tr>
							<td>Machine Capacity</td>
							<td><input type="text" name="txt_machine_capacity" id="txt_machine_capacity" class="text_boxes_numeric" style="width:127px"></td>                    		
							<td>Fabric Group</td>
							<td><input type="text" name="txt_fab_group" id="txt_fab_group" class="text_boxes" style="width:127px"  onDblClick="openmypage_fabric_group()" placeholder="Browse"><input type="hidden" name="fab_group_hid_id" id="fab_group_hid_id" class="text_boxes_numeric"></td>
							<td>Dyeing M/C Type</td>
							<td ><? echo create_drop_down( "cbo_mc_type", 140, $dyeing_mcTypeArr,'', '', '' ); ?></td>
							<td>Status</td>
							<td><? echo create_drop_down( "cbo_status", 140, $row_status,'', '', '' ); ?><input type="hidden" name="update_id" id="update_id" class="text_boxes" style="width:127px"></td>
                    	</tr>
             <tr>
            		<td>Remarks</td>
								<td><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:127px"></td>

								<td  class="must_entry_caption">S.Con.Party</td>
                <td>
                  <? echo create_drop_down( "cbo_supplier", 140, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 and a.is_deleted=0 group by a.id, a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",1 );?>
                </td>
						</tr>
            <tr>
							<td colspan="8" align="center">&nbsp;</td>
						</tr>
						<tr>
						  <td colspan="8" align="center" class="button_container"><? echo load_submit_buttons( $permission, "fnc_machine_name_entry", 0,0 ,"reset_form('machinename_1','','',1)"); ?></td>				
						</tr>
        </td>
      </tr>
			</table>
			<table>
				<tr>
					<td align="center" id="machine_entry_list">&nbsp;</td>
				</tr>
			</table>
		</form>
    </fieldset>	
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript">//set_bangla();</script>
<script>
$("#cbo_location_name").val(0);

</script>
</html>


