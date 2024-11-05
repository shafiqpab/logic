<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Sewing Operation Entry
Functionality	:	 
JS Functions	:
Created by		:	Ashraful 
Creation date 	: 	18-9-2019
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
<script>
	//if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	function fnc_sewing_operation_entry( operation )
	{
		
		if(operation==2){if(confirm('Delete This Operation ?')==0){return;}}
		
		if (form_validation('cbo_garment_item*cbo_bodypart*txt_operation*cbo_smv_basis*cbo_resource','Garments Item* Body Part*Operation Name*SMV Basis*Resource')==false)
		{
			return; 
		}
		
		var cbo_resource=$('#cbo_resource').val();
		/*if(cbo_resource==40 || cbo_resource==41 || cbo_resource==43 || cbo_resource==44 || cbo_resource==48 || cbo_resource==53 || cbo_resource==54 || cbo_resource==55 || cbo_resource==56 || cbo_resource==68 || cbo_resource==69 || cbo_resource==70 || cbo_resource==90)
		{
			if (form_validation('txt_helper_smv','Manual SMV')==false)
			{
				return; 
			}
		}
		else
		{
			if (form_validation('txt_operator_smv','Machine SMV')==false)
			{
				return; 
			}
		}*/

		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_id*cbo_product_dept*cbo_garment_item*cbo_bodypart*txt_code*txt_operation*txt_rate*fabric_desc_id*cbo_smv_basis*txt_seam_length*cbo_resource*txt_operator_smv*smv_data_operator*txt_helper_smv*smv_data_helper*cbo_department_code*cbo_uom*cbo_status*update_id*smv_data*txt_product_code*txt_gmts_code*txt_body_part_code*cbo_prod_type',"../../");
		freeze_window(operation);
		http.open("POST","requires/sewing_operation_controller.php", true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_sewing_operation_entry_reponse;
		
	}

	function fnc_sewing_operation_entry_reponse()
	{
		if(http.readyState == 4) 
		{
			
			//release_freezing();return;
			var response=trim(http.responseText).split('**');
			if(response[0].length>2) response[0]=10;
			show_msg(response[0]);
			if(response[0]==0 || response[0]==1)
			{
				set_button_status(0, permission, 'fnc_sewing_operation_entry',1);
				reset_form('','','txt_operator_smv*txt_helper_smv','cbo_department_code,8','','');
				$("#cbo_department_code").val(8);
				$("#update_id").val('');
				fnc_smv_active();
			}
			else if(response[0]==2){
				set_button_status(0, permission, 'fnc_sewing_operation_entry',1);
				reset_form('','','txt_operator_smv*txt_helper_smv','cbo_department_code,8','','');
				$("#cbo_department_code").val(8);
				$("#update_id").val('');
				fnc_smv_active();
				show_operation(2);
			}
			release_freezing();
		}
	}	
	
	function show_operation(type)
	{
		if(type==2)
		{
			var data=$('#departmenCode').val()+"__"+$('#garmentItem').val()+"__"+$('#bodypart').val()+"__"+$('#resource').val()+"__"+$('#cbo_company_id').val();
			show_list_view(data,'sewing_operation_list_view','sewing_operation_list','requires/sewing_operation_controller','setFilterGrid("list_view",-1)');
		}
		else
		{
			show_list_view('','sewing_operation_list_view','sewing_operation_list','requires/sewing_operation_controller','setFilterGrid("list_view",-1)');
		}
	}

	function fnc_smv_active()
	{
		var resource=$("#cbo_resource").val();
		var smv_basis=$("#cbo_smv_basis").val();
		$("#smv_data_operator").val('');
		$("#smv_data_helper").val('');
		/*$("#txt_operator_smv").val('');
		$("#txt_helper_smv").val('');*/
		
		if(resource==129 || resource==40 || resource==41 || resource==43 || resource==44 || resource==48 || resource==53 || resource==54 || resource==55 || resource==56 || resource==68 || resource==69 || resource==70 || resource==90)
		{
			$("#txt_operator_smv").attr("disabled",true);
			$("#txt_helper_smv").attr("disabled",false);
			if(smv_basis==2)
			{
				$('#txt_helper_smv').attr('readOnly','readOnly');
				$('#txt_helper_smv').attr('placeholder','Double Click');
				$('#txt_helper_smv').attr('onDblClick','openmypage_smv_calculation()');
			}
			else
			{
				$('#txt_helper_smv').removeAttr('readOnly','readOnly');
				$('#txt_helper_smv').attr('placeholder','Write');
				$('#txt_helper_smv').removeAttr('onDblClick','openmypage_smv_calculation()');
			}
			
			$('#txt_operator_smv').attr('readOnly','readOnly');
			$('#txt_operator_smv').attr('placeholder','');
			$('#txt_operator_smv').removeAttr('onDblClick','openmypage_smv_calculation()');
		}
		else
		{
			$("#txt_operator_smv").attr("disabled",false);
			$("#txt_helper_smv").attr("disabled",true);
			if(smv_basis==2)
			{
				$('#txt_operator_smv').attr('readOnly','readOnly');
				$('#txt_operator_smv').attr('placeholder','Double Click');
				$('#txt_operator_smv').attr('onDblClick','openmypage_smv_calculation()');
			}
			else
			{
				$('#txt_operator_smv').removeAttr('readOnly','readOnly');
				$('#txt_operator_smv').attr('placeholder','Write');
				$('#txt_operator_smv').removeAttr('onDblClick','openmypage_smv_calculation()');
			}
			
			$('#txt_helper_smv').attr('readOnly','readOnly');
			$('#txt_helper_smv').attr('placeholder','');
			$('#txt_helper_smv').removeAttr('onDblClick','openmypage_smv_calculation()');
		}
	}
	
	function openmypage_fabricDescription()
	{
		var garments_nature = $('#garments_nature').val();
		var title = 'Fabric Description Info';	
		var page_link = 'requires/sewing_operation_controller.php?action=fabricDescription_popup&garments_nature='+garments_nature;
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("hidden_desc_id").value;	 //Access form field with id="emailfield"
			var theename=this.contentDoc.getElementById("hidden_desc_no").value; //Access form field with id="emailfield"
			var theegsm=this.contentDoc.getElementById("hidden_gsm").value; //Access form field with id="emailfield"
			
			$('#txt_fabric_description').val(theename);
			$('#fabric_desc_id').val(theemail);
		}
	}
	
	function openmypage_smv_calculation()
	{
		var txt_fabric_description=$('#txt_fabric_description').val();
		var txt_seam_length=$('#txt_seam_length').val();
		var cbo_resource=$('#cbo_resource').val();
		var cbo_garment_item=$('#cbo_garment_item').val();
		var cbo_bodypart=$('#cbo_bodypart').val();
		var txt_operation=$('#txt_operation').val();
		var smv_data=$('#smv_data').val();
		
		if(cbo_resource==129 || cbo_resource==40 || cbo_resource==41 || cbo_resource==43 || cbo_resource==44 || cbo_resource==48 || cbo_resource==53 || cbo_resource==54 || cbo_resource==55 || cbo_resource==56 || cbo_resource==68 || cbo_resource==69 || cbo_resource==70 || cbo_resource==90)
		{
			var prev_data=$('#smv_data_helper').val();
		}
		else
		{
			var prev_data=$('#smv_data_operator').val();
		}
		
		var title = 'SMV Calculation';	
		var page_link = 'requires/sewing_operation_controller.php?action=smvCalculation_popup&txt_fabric_description='+txt_fabric_description+'&txt_seam_length='+txt_seam_length+'&cbo_resource='+cbo_resource+'&cbo_garment_item='+cbo_garment_item+'&cbo_bodypart='+cbo_bodypart+'&txt_operation='+txt_operation+'&prev_data='+prev_data+'&smv_data='+smv_data;
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=990px,height=470px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var hidden_data=this.contentDoc.getElementById("hidden_data").value;	 //Access form field with id="emailfield"
			var hidden_dtls_data=this.contentDoc.getElementById("hidden_dtls_data").value; //Access form field with id="emailfield"
			var hidden_smv=this.contentDoc.getElementById("hidden_smv").value; //Access form field with id="emailfield"
			
			//if(cbo_resource==40)
			if(cbo_resource==129 || cbo_resource==40 || cbo_resource==41 || cbo_resource==43 || cbo_resource==44 || cbo_resource==48 || cbo_resource==53 || cbo_resource==54 || cbo_resource==55 || cbo_resource==56)
			{
				$('#smv_data_helper').val(hidden_dtls_data);
				$('#smv_data_operator').val('');
				$('#txt_helper_smv').val(hidden_smv);
				$('#txt_operator_smv').val('');
			}
			else
			{
				$('#smv_data_operator').val(hidden_dtls_data);
				$('#smv_data_helper').val('');
				$('#txt_operator_smv').val(hidden_smv);
				$('#txt_helper_smv').val('');
			}
			$('#smv_data').val(hidden_data);
		}
	}
	
	function print_sheet()
	{
		if($('#update_id').val()=="")
		{
			alert("Save First");	
			return;
		}
		print_report($('#update_id').val(), "time_study_sheet_print", "requires/sewing_operation_controller");
	}
	
	
	function openmypage(id,action,title,width)
	{
		var selected_data = $('#'+id).val();
		var page_link = 'requires/sewing_operation_controller.php?action='+action+'&selected_data='+selected_data;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+width+',height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var data_id=this.contentDoc.getElementById("hidden_selected_data_id").value;
			var data_id_txt=this.contentDoc.getElementById("hidden_selected_data_text").value;
			$('#'+id).val(data_id);
			$('#'+id+'_view').val(data_id_txt);
			fnc_smv_active();
		}
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
<div align="center" style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission); ?>
    <fieldset style="width:990px;">
    <legend>Sewing Operation Entry</legend>
    	<form name="sewingoperationentry_1" id="sewingoperationentry_1" autocomplete="off">	
            <table width="95%">
                <tr>
					<td width="120">Company Name</td> 
                    <td>
						<?
							echo create_drop_down( "cbo_company_id", 140,"select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name", 1, "-- All company --", 0, "" );
						?>
					</td>
                	<td width="120">Product Dept.</td> 
                    <td><? echo create_drop_down( "cbo_product_dept", 172, $product_dept,'', 1,"-- Select --" ); ?></td>
                    <td width="120" class="must_entry_caption">Garments Item</td> 
                    <td>
					<input type="text" name="cbo_garment_item_view" id="cbo_garment_item_view" class="text_boxes" style="width:140px" onDblClick="openmypage('cbo_garment_item','garment_item_list_view','Garments Item','750px')" placeholder="Double Click" readonly/>
                    <input type="hidden" name="cbo_garment_item" id="cbo_garment_item" />
					<? //echo create_drop_down( "cbo_garment_item", 152, $garments_item,'', 1,"--Select Gmts. Item--" ); ?>
                    </td>
                </tr>
                <tr>
					<td class="must_entry_caption">Body Part</td>
                    <td>
						<input type="text" name="cbo_bodypart_view" id="cbo_bodypart_view" class="text_boxes" style="width:130px" onDblClick="openmypage('cbo_bodypart','bodypart_list_view','Body Part','750px')" placeholder="Double Click" readonly/>
                    	<input type="hidden" name="cbo_bodypart" id="cbo_bodypart" />
					
						<? //asort($body_part); echo create_drop_down( "cbo_bodypart", 142, $body_part,'', 1, "-- Select Body Part--",'','','','2,3,6,7,9,10,11,26,28,40,53,59,60,63,79,92,106,196,197,314' ); ?>
                    </td>
                	<td>Code</td>
                    <td>
                    	<input type="text" name="txt_product_code" id="txt_product_code" class="text_boxes" maxlength="2" style="width:20px" placeholder="P"/>
                        <input type="text" name="txt_gmts_code" id="txt_gmts_code" class="text_boxes" maxlength="4"  style="width:30px" placeholder="G"/>
                        <input type="text" name="txt_body_part_code" id="txt_body_part_code" class="text_boxes" maxlength="4" style="width:30px" placeholder="B"/>
                    	<input type="text" name="txt_code" id="txt_code" class="text_boxes" style="width:34px;" readonly disabled />
                    </td>
                    <td class="must_entry_caption">Operation Name</td>
                    <td><input type="text" name="txt_operation" id="txt_operation" class="text_boxes" style="width:140px" placeholder="Write"/></td>
                </tr>
                <tr>
					<td>Rate</td>
                    <td><input type="text" name="txt_rate" id="txt_rate" class="text_boxes_numeric" style="width:130px;" /></td>
                    <td>Fabric Type </td>
                    <td>
                        <input type="text" name="txt_fabric_description" id="txt_fabric_description" class="text_boxes" style="width:160px" onDblClick="openmypage_fabricDescription()" placeholder="Double Click To Search" readonly/>
                        <input type="hidden" name="fabric_desc_id" id="fabric_desc_id" class="text_boxes">
                    </td>
                    <td class="must_entry_caption">SMV Basis</td>
                    <td><? echo create_drop_down( "cbo_smv_basis", 152, $smv_basis,'', '1', '-- Select --','','fnc_smv_active();','','' ); ?></td>
                    
                </tr>
                <tr>
					<td>Seam Length</td>
                    <td><input type="text" name="txt_seam_length" id="txt_seam_length" class="text_boxes_numeric" style="width:130px; " /></td>
                    <td class="must_entry_caption">Resource</td>
                    <td>
					<input type="text" name="cbo_resource_view" id="cbo_resource_view" class="text_boxes" style="width:160px" onDblClick="openmypage('cbo_resource','resource_list_view','Resource','750px')" placeholder="Double Click" readonly/>
                    <input type="hidden" name="cbo_resource" id="cbo_resource" />
					
					
					<? //asort($production_resource); echo create_drop_down( "cbo_resource", 172, $production_resource,'', '1', '-- Select --','1','fnc_smv_active();' ); ?>
                    
                    </td>
                    <td>Machine SMV</td>
                    <td>
                    	<input type="text" name="txt_operator_smv" id="txt_operator_smv" class="text_boxes_numeric" style="width:140px;" readonly/>
                    	<input type="hidden" name="smv_data_operator" id="smv_data_operator" class="text_boxes_numeric" style="width:130px;" readonly/>
                    </td>
                    
                </tr>
                <tr>
					<td>Manual SMV</td>
                    <td>
                    	<input type="text" name="txt_helper_smv" id="txt_helper_smv" class="text_boxes_numeric" style="width:130px;" disabled/>
                        <input type="hidden" name="smv_data_helper" id="smv_data_helper" class="text_boxes_numeric" style="width:130px;" readonly/>
                    </td>
                    <td>Department Code</td>
                    <td><? echo create_drop_down( "cbo_department_code", 172, $machine_category,'', '', '','8','fnc_smv_active();','','1,6,8,25,26,27,28,29,30,31,32' ); ?></td>
                    <td>UOM</td>
                    <td><? echo create_drop_down( "cbo_uom", 152, $unit_of_measurement,'', '', '' ); ?></td>   
                </tr>
                <tr>
					<td>Action</td>
                    <td><? echo create_drop_down( "cbo_status", 142, $row_status,'', '', '' ); ?></td> 
                    <td>Prod. Type</td>
                    <td>
						<?
                    		$production_rate_type=array(1=>"Fixed Rate",2=>"Piece Rate",3=>"Outsourcing");
                     echo create_drop_down( "cbo_prod_type", 172, $production_rate_type,'', 1,"-- Select --",'','','','' ); 
					 	?>
					 </td>
                </tr>
                <tr>
                    <td colspan="4" class="button_container" valign="top" align="right">
						<? echo load_submit_buttons($permission, "fnc_sewing_operation_entry", 0,0,"reset_form('sewingoperationentry_1','','','cbo_department_code,8','')",1); ?>		
                        <input type="hidden" name="update_id" id="update_id" >
                        <input type="hidden" name="smv_data" id="smv_data" >
                    </td>
                    <td colspan="2" class="button_container" valign="top">
                    	<input type="button" class="formbuttonplasminus" value="Time Study Sheet" id="report" onClick="print_sheet();" style="width:110px;"/>
                    </td>					
                </tr>
            </table>
            <div id="sewing_operation_list">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="990" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="110">Prod. Type</th>
			<th width="110">Body Part<br>
			<?
				$sql_bpart="select a.body_part_full_name,b.mst_id,b.entry_page_id from lib_body_part_tag_entry_page b, lib_body_part a where a.id=b.mst_id and b.status_active=1 and a.status=1  and b.is_deleted=0  and a.is_deleted=0";
						$sql_result=sql_select($sql_bpart);
						foreach ($sql_result as $value) 
						{
							if($value[csf("entry_page_id")]==148)
							{
								$tag_body_part_arr[$value[csf("mst_id")]]=$value[csf("body_part_full_name")];
							}
								$all_body_part_arr[$value[csf("mst_id")]]=$value[csf("body_part_full_name")];
						}
					   $body_partArr=array();
					   if(count($tag_body_part_arr)>0)
					   {
						$body_partArr=$tag_body_part_arr;   
					   }
					   else
					   {
						 $body_partArr=$all_body_part_arr;     
					   }
					    asort($body_partArr);
			// echo create_drop_down( "bodypart", 80, $body_partArr,'', 1, "-- Select --",$data[2],'show_operation(2);','','2,3,6,7,9,10,11,26,28,40,53,59,60,63,79,92,106,196,197,314' ); 
			 echo create_drop_down( "bodypart", 100, $body_partArr,'', 1, "-- Select --",$data[2],'show_operation(2);','','' ); 
			 ?>
             </th> 
			<th width="110">Department Code<br><? echo create_drop_down( "departmenCode", 100, $machine_category,'', 1,"-- Select --",$data[0],'show_operation(2);','','1,6,8,25,26,27,28,29,30,31,32' ); ?></th>
			<th width="110">Garments Item<br><? echo create_drop_down( "garmentItem", 105, get_garments_item_array(100),'', 1,"-- Select --",$data[1],'show_operation(2);' ); ?></th>
			              
			<th width="80">Operation </th>
			<th width="150">Operation Desc.</th>

            <th width="85">Resources<br><? asort($production_resource); echo create_drop_down( "resource", 75, $production_resource,'', '1', '-- Select --',$data[3],'show_operation(2);',"","129,130"); ?></th>
            <th width="75">Machine SMV</th>
            <th width="">Manual SMV</th>
	
		</thead>
	</table>
            </div>
           <!-- <table width="100%">
                 <tr>
                    <td colspan="6" align="center">						
                    <?/*$arr=array(0=>$product_dept,1=>$garments_item,2=>$body_part,5=>$smv_basis,7=>$production_resource,10=>$machine_category);
						echo create_list_view ( "list_view", "Product Dept., Garments Item, Body Part,Code,Operation, SMV Basis, Seam Length,Resources,Machine SMV,Manual SMV, Department Code", "80,115,80,90,90,60,75,70,75,70","950","220",1, "select product_dept,gmt_item_id, bodypart_id,code,operation_name,smv_basis,seam_length, resource_sewing, operator_smv, helper_smv, department_code, id from lib_sewing_operation_entry where is_deleted=0 order by id Desc", "get_php_form_data", "id","'load_php_data_to_form'", 1, "product_dept,gmt_item_id,bodypart_id,0,0,smv_basis,0,resource_sewing,0,0,department_code", $arr , "product_dept,gmt_item_id,bodypart_id,code,operation_name,smv_basis,seam_length,resource_sewing,operator_smv,helper_smv,department_code","requires/sewing_operation_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0,2,2,0');*/
                    ?>
                    </td>					
                </tr>		
            </table>-->
        </form>	
    </fieldset>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$('#cbo_smv_basis').val(1);
	fnc_smv_active();
</script>
</html>
