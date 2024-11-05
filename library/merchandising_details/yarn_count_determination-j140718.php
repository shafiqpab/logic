<?
	session_start();
	if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
	require_once('../../includes/common.php');
	extract($_REQUEST);
	$_SESSION['page_permission']=$permission;
	//----------------------------------------------------------------------------------------------------------------
	echo load_html_head_contents("Yarn Count Determination", "../../", 1, 1,$unicode,'','');
?>
<script type="text/javascript">

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	<?
	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][184] );
	echo "var field_level_data= ". $data_arr . ";\n";
	
	
	?>
	function fnc_yarn_count_determination( operation )
	{
		/*if(operation==1 || operation==2)
		{
			var update_mst_id=$('#update_mst_id').val();
			var status_id=$('#cbo_status').val();
			var response=trim(return_global_ajax_value( update_mst_id, 'check_yarn_count_determination', '', 'requires/yarn_count_determination_controller'));
			var response=response.split("_");
			
			if(status_id!=2)
			{
				if(response[0]==1)
				{
						alert("This Yarn Count Determination is already used another page");
						return;
				}
			}
			
		}*/
		var row_num=$('#tbl_yarn_count tr').length-1;
		var data_all="";
		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('cbo_fabric_nature*txtconstruction*cbocompone_'+i+'*percentone_'+i,'Fab Nature*Constrution*Composition*Percent')==false)
			{
				return;
			}
			data_all=data_all+get_submitted_data_string('cbo_fabric_nature*txtconstruction*txtgsmweight*cbocolortype*stichlength*processloss*cbo_status*txt_sequence*update_mst_id*cbocompone_'+i+'*percentone_'+i+'*cbocountcotton_'+i+'*cbotypecotton_'+i+'*updateid_'+i,"../../");
		}
		//alert(data_all)
		var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+data_all;
		
		freeze_window(operation);
		http.open("POST","requires/yarn_count_determination_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_yarn_count_determination_reponse;
	}
	
	function fnc_yarn_count_determination_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]==15) 
			{ 
				 setTimeout('fnc_yarn_count_determination('+ reponse[1]+')',8000); 
			}
			else
			{
				//alert(reponse[0]);
				show_msg(trim(reponse[0]));
				show_list_view(reponse[1],'search_list_view','yarn_count_container','../merchandising_details/requires/yarn_count_determination_controller','setFilterGrid("list_view",-1)');
				reset_form('yarncountdetermination_1','','');
				set_button_status(0, permission, 'fnc_yarn_count_determination',1);
				release_freezing();
			}
		}
	}
	

	function add_break_down_tr(i) 
	{
		var row_num=$('#tbl_yarn_count tr').length-1;
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
			 $("#tbl_yarn_count tr:last").clone().find("input,select").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name + i },
				  'value': function(_, value) { return value }              
				});  
			  }).end().appendTo("#tbl_yarn_count");
			 $('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
			 $('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+");");
			 
			 $('#txtcompone_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_comp("+i+",1);");
			// $('#cbocompone_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id);");
			 
			 $('#cbocountcotton_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id);");
			 $('#cbotypecotton_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id);");
			 $('#percentone_'+i).removeAttr("onChange").attr("onChange","sum_percent()");
	
			  $('#cbocompone_'+i).val("");
			  $('#txtcompone_'+i).val("");
			  $('#percentone_'+i).val("");
			  $('#cbocountcotton_'+i).val("");
			  $('#cbotypecotton_'+i).val("");
			  $('#updateid_'+i).val("");
		}
	}

	function fn_deletebreak_down_tr(rowNo,table_id) 
	{   
		var numRow = $('table#tbl_yarn_count tbody tr').length; 
		if(numRow==rowNo && rowNo!=1)
		{
			$('#tbl_yarn_count tbody tr:last').remove();
		}
	}

	function show_detail_form(mst_id)
	{
		show_list_view(mst_id,'show_detail_form','form_div','requires/yarn_count_determination_controller','');
	}


	function check_duplicate(id,td)
	{
		//alert(td)
		var cbocompone=document.getElementById('cbocompone_'+id).value;
		var cbocountcotton=document.getElementById('cbocountcotton_'+id).value;
		var cbotypecotton=document.getElementById('cbotypecotton_'+id).value;
		var row_num=$('#tbl_yarn_count tr').length-1;
		for (var k=1;k<=row_num; k++)
		{
			if(k==id)
			{
				continue;
			}
			else
			{
				if(cbocompone==document.getElementById('cbocompone_'+k).value && cbocountcotton==document.getElementById('cbocountcotton_'+k).value && cbotypecotton==document.getElementById('cbotypecotton_'+k).value)
				{
					alert("Same Gmts Composition, Same Count and Same Type Duplication Not Allowed.");
					if(td==1)
					{
						$('#cbocompone_'+id).val('');
						$('#txtcompone_'+id).val('');
						$('#txtcompone_'+id).focus();
					}
					else
					{
						document.getElementById(td).value=0;
						document.getElementById(td).focus();
					}
				}
			}
		}
	}
		
	function sum_percent()
	{
		var i=0;
		 var tot_percent=0;
		 var row_num=$('#tbl_yarn_count tr').length-1;
		 for (var k=1;k<=row_num; k++)
		 {
			 tot_percent+=(document.getElementById('percentone_'+k).value)*1;
			 i++
		 }
		 if(tot_percent>100)
		 {
			 alert("Total Percentage More Than 100 Not Allowed");
			 document.getElementById('percentone_'+i).value=""; 
		 }
	}

	function open_process_loss_pop_up(id)
	{ 
		if(id=="")
		{
			alert("Save Data First");
			return;
		}
		
		var page_link="requires/yarn_count_determination_controller.php?action=open_process_loss_popup_view&mst_id="+trim(id);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Process Loss Pop Up", 'width=480px,height=300px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var tot_process_loss=this.contentDoc.getElementById("tot_process_loss_hidden").value;
			document.getElementById('processloss').value=tot_process_loss;
		}		
	}

	function openpage_mapping_popup(update_id)
	{ 
		if(update_id=="")
		{
			alert("Save Data First");
			return;
		}
		
		var page_link="requires/yarn_count_determination_controller.php?action=openpage_mapping_popup&mst_id="+trim(update_id);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "S. Length/ M. Dia/ F. Dia/ GG Pop Up", 'width=480px,height=200px,center=1,resize=1,scrolling=0','../');
	}
	
	function openmypage_comp(inc)
	{
		var page_link="requires/yarn_count_determination_controller.php?action=composition_popup&inc="+inc;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Composition Popup", 'width=480px,height=350px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			
			var hidcompid=this.contentDoc.getElementById("hidcompid").value;
			var hidcompname=this.contentDoc.getElementById("hidcompname").value;
			$('#cbocompone_'+inc).val(hidcompid);
			$('#txtcompone_'+inc).val(hidcompname);
			check_duplicate(inc,1);
		}
	}
	
</script>

</head>	
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%; position:relative; margin-bottom:5px; margin-top:5px">
		<? echo load_freeze_divs ("../../",$permission);  ?>
        <form name="yarncountdetermination_1" id="yarncountdetermination_1" autocomplete="off">
            <fieldset style="width:680px;">
                <legend>Yarn Count Determination </legend>
                <table width="100%" border="0" cellpadding="0" cellspacing="2">
                    <tr>
                        <td width="100" class="must_entry_caption">Fabric Nature</td>
                        <td width="210"><?  echo create_drop_down( "cbo_fabric_nature",200, $item_category,"", 0, "", '', "",$disabled,"2,3,100" ); ?></td>
                        <td width="100" class="must_entry_caption">Construction</td>
                        <td width="200"><input type="text" id="txtconstruction"  name="txtconstruction" class="text_boxes" style="width:200px" value="" /></td>
                    </tr>
                    <tr>
                        <td>Color Range</td>
                        <td><? echo create_drop_down( "cbocolortype", 200, $color_range,"", 1, "-- Select --", '', "",$disabled,"" ); ?></td>
                        <td>GSM</td>
                        <td><input type="text" id="txtgsmweight" name="txtgsmweight" class="text_boxes_numeric" style="width:200px" value="" /></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td><input id="mapping_popup" class="image_uploader" type="button" onClick="openpage_mapping_popup(document.getElementById('update_mst_id').value)" value="S. Length/ M. Dia/ F. Dia/ GG Pop Up" style="width:200px;">
                        	<input type="hidden" id="update_mst_id" value=""/> 
                        </td>
                        <td>Status</td>	
                        <td><? echo create_drop_down("cbo_status", 200, $row_status, "", "", "", 0, "","","1,2"); ?></td>
                    </tr>
                </table>
            </fieldset>
            <script>
				$('#cbofabricnature').val( $('#garments_nature').val() );
			</script>
            <fieldset style="width:680px;">
                <legend>Yarn Count Determination </legend>
                <div id="form_div">
                    <table width="100%" border="0" id="tbl_yarn_count" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" >
                        <thead>
                            <tr>
                            	<th width="150" class="must_entry_caption">Composition</th><th width="50" class="must_entry_caption">%</th><th width="150">Count</th><th width="150">Type</th><th>&nbsp;</th> 
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="yarncost_1" align="center">
                                <td width="150"><? //echo create_drop_down( "cbocompone_1", 150, $composition,"", 1, "-- Select --", '', "check_duplicate(1,this.id)",'','' ); ?>
                                	<input type="text" id="txtcompone_1"  name="txtcompone_1"  class="text_boxes" style="width:140px" value="" readonly placeholder="Browse" onDblClick="openmypage_comp(1);" />
                                    <input type="hidden" id="cbocompone_1"  name="cbocompone_1" class="text_boxes" style="width:50px" value="" />
                                </td>
                                <td width="50"><input type="text" id="percentone_1"  name="percentone_1" onChange="sum_percent()" class="text_boxes" style="width:50px" value="" /></td>
                                <td width="70"><? echo create_drop_down( "cbocountcotton_1", 150, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1," -- Select Count --", '', 'check_duplicate(1,this.id)','','' ); ?>
                                </td>
                                <td width="100"><? echo create_drop_down( "cbotypecotton_1", 150, $yarn_type,"", 1, "-- Select --", '', 'check_duplicate(1,this.id)','','' ); ?></td>
                                <td> 
                                    <input type="button" id="increase_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(1)" />
                                    <input type="button" id="decrease_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1);" />
                                    <input type="hidden" id="updateid_1" name="updateid_1"  class="text_boxes" style="width:20px" value=""  />  
                                </td>  
                            </tr>
                        </tbody>
                    </table>
                </div>
                <br/>
                <table width="100%" border="" cellpadding="0" cellspacing="0"  rules="all">
                    <tr>
                        <td align="center" width="150">Stitch Length</td>
                        <td align="center" width="50"><input type="text" id="stichlength" name="stichlength" class="text_boxes_numeric" style="width:50px" value=""></td>
                        <td width="120" align="center">Process Loss</td>
                        <td width="120"><input type="text" id="processloss" name="processloss" class="text_boxes_numeric" style="width:50px" value="" readonly></td>
                        <td width="120" align="center">Sequence No</td>
                        <td width="120"><input type="text" id="txt_sequence" name="txt_sequence" class="text_boxes_numeric" style="width:50px" value="" ></td>
                        <td><input id="process_loss_pop_up" class="image_uploader" type="button" onClick="open_process_loss_pop_up(document.getElementById('update_mst_id').value)" value="Process Loss% Break-down" style="width:150px;">
                        </td>				
                    </tr>	
                    <tr>
                        <td colspan="5" align="center" class="button_container"><? echo load_submit_buttons( $permission, "fnc_yarn_count_determination", 0,0 ,"reset_form('yarncountdetermination_1','','')",1); ?> 
                        </td>				
                    </tr>	
                </table>
            </fieldset>
        </form>	
		<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
		<script> set_field_level_access(<? echo end(array_keys($_SESSION['logic_erp']['data_arr'][184]));?>); </script>
        <div id="yarn_count_container">
			<?
				$composition_arr=array();
				$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
				//$sql_q="select a.fab_nature_id, a.construction, a.gsm_weight, a.color_range_id,a .stich_length, a.process_loss, a.sequence_no, b.copmposition_id, b.percent, b.count_id, b.type_id, a.id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.is_deleted=0 order by b.id";
				$sql_q="select mst_id, copmposition_id, percent, count_id, type_id from lib_yarn_count_determina_dtls where is_deleted=0 order by id";
				
				$data_array=sql_select($sql_q);
				if (count($data_array)>0)
				{
					foreach( $data_array as $row )
					{
						if(array_key_exists($row[csf('mst_id')],$composition_arr))
						{
							$composition_arr[$row[csf('mst_id')]]=$composition_arr[$row[csf('mst_id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
						}
						else
						{
							$composition_arr[$row[csf('mst_id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
						}
					}
				}
				unset($data_array);
				//print_r($composition_arr);
				
				$sql="select id, fab_nature_id, construction, gsm_weight, status_active, color_range_id, stich_length, process_loss, sequence_no from  lib_yarn_count_determina_mst where is_deleted=0 order by id";
				
				$arr=array (0=>$item_category, 3=>$color_range,6=>$composition_arr,8=>$row_status);
				
				echo  create_list_view ( "list_view", "Fab Nature,Construction,GSM/Weight,Color Range,Stitch Length,Process Loss,Composition,Sequence No,Status", "100,100,100,100,90,50,300,50,50","1000","350",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form'",1, "fab_nature_id,0,0,color_range_id,0,0,id,0,status_active", $arr , "fab_nature_id,construction,gsm_weight,color_range_id,stich_length,process_loss,id,sequence_no,status_active", "requires/yarn_count_determination_controller",'setFilterGrid("list_view",-1);','0,0,1,0,1,1,0,0,0') ;
				exit();
            ?>
        </div>
    </div>
</body>

</html>
