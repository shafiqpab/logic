<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Yarn Count Determination[Sweater]
Functionality	:
JS Functions	:
Created by		:	zakaria joy
Creation date 	: 	27-07-2020
Updated by 		:	
Update date		:	
QC Performed BY	:
QC Date			:
Comments		: Entry Form 426
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
echo load_html_head_contents("Yarn Count Determination[Sweater]", "../../", 1, 1,$unicode,'','');
?>
<script type="text/javascript">

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	<?
	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][184] );
	echo "var field_level_data= ". $data_arr . ";\n";
	?>
	function fnc_fabric_count_determination( operation )
	{
		freeze_window(operation);
		/*if('<?php// echo implode('*',$_SESSION['logic_erp']['mandatory_field'][426]);?>'){
			if (form_validation('<?php// echo implode('*',$_SESSION['logic_erp']['mandatory_field'][426]);?>','<?php// echo implode('*',$_SESSION['logic_erp']['field_message'][426]);?>')==false)
			{
				release_freezing();
				return;
			}
		}*/
		
		if(form_validation('cbo_fabric_nature*txtcompone','Fabric Nature*Composition')==false)//*txt_date_from*txt_date_to*From Date*To Date
		{
			release_freezing();
			return;
		}
			

		if(operation==1 || operation==2)
		{
			var update_mst_id=$('#update_mst_id').val();
			var status_id=$('#cbo_status').val();
			var response=trim(return_global_ajax_value( update_mst_id, 'check_yarn_count_determination', '', 'requires/yarn_count_determination_sweater_controller'));
			var response=response.split("_");
			
			if(status_id!=2)
			{
				if(response[0]==1)
				{
					alert("This Yarn Count Determination is already used another page");
					release_freezing();
					return;
				}
			}
		}
		//sum_percent();
		/*var row_num=$('#tbl_yarn_count tr').length-1;
		var data_all="";
		var total = document.getElementById('total_percent').value;
		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('cbo_fabric_nature*txttype*txtconstruction*txtdesign*cbocompone_'+i+'*percentone_'+i,'Fab Nature*Type*Constrution*Design*Composition*Percent')==false)
			{
				release_freezing();
				return;
			}
			else if(total < 100)
			{
				alert("Total Percentage Less Than 100 Not Allowed");
				release_freezing();
				return;
			}
			else
			{
				data_all=data_all+get_submitted_data_string('cbo_fabric_nature*txttype*txtconstruction*txtweight*cboweighttype*txtdesign*txtfabricref*txtrdno*cbocolortype*cbo_status*update_mst_id*txt_full_width*txt_cutable_width*cbocompone_'+i+'*percentone_'+i+'*cbocountcotton_'+i+'*cbotypecotton_'+i+'*updateid_'+i,"../../");
			}
		}*/
		//alert(data_all)
		//var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+data_all;
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_fabric_nature*txtrdno*txtmillRef*txtconstruction*txtcompone*cbocomponeid*cbocountcotton*cbotypecotton*cbocolorrange*cbo_gauge*txt_process_loss*txt_seq_no*cbo_status*update_mst_id*txt_sys_code',"../../");
		
		http.open("POST","requires/yarn_count_determination_sweater_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_count_determination_reponse;
	}
	
	function fnc_fabric_count_determination_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			console.log(http.responseText);
			if(reponse[0]==50)
			{
				alert(reponse[1]);
				release_freezing();
				return;	
			}
			if(reponse[0]==15) 
			{ 
				 setTimeout('fnc_fabric_count_determination('+ reponse[1]+')',8000); 
			}
			else if(reponse[0]==10 || reponse[0]==11)
			{
				show_msg(trim(reponse[0]));
				release_freezing();
			}
			else
			{
				//alert(reponse[0]);
				show_msg(trim(reponse[0]));
				show_list_view(reponse[1],'search_list_view','yarn_count_container','requires/yarn_count_determination_sweater_controller','setFilterGrid("list_view",-1)');
				reset_form('yarncountdetermination_1','','');
				set_button_status(0, permission, 'fnc_fabric_count_determination',1);
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
		//show_list_view(mst_id,'show_detail_form','form_div','requires/yarn_count_determination_sweater_controller','');
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
		 tot_percent=number_format(tot_percent,4,".","")
		 if(tot_percent>100)
		 {
			 alert("Total Percentage More Than 100 Not Allowed");
			 document.getElementById('percentone_'+i).value=""; 
		 }
		 //var rowCount = $('#tbl_yarn_count  tr').length-1;
		 var ddd={ dec_type:1, comma:0, currency:0}
		 math_operation( "total_percent", "percentone_", '+', row_num,ddd);
	}

	function open_process_loss_pop_up(id)
	{ 
		if(id=="")
		{
			alert("Save Data First");
			return;
		}
		
		var page_link="requires/yarn_count_determination_sweater_controller.php?action=open_process_loss_popup_view&mst_id="+trim(id);
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
		
		var page_link="requires/yarn_count_determination_sweater_controller.php?action=openpage_mapping_popup&mst_id="+trim(update_id);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "S. Length/ M. Dia/ F. Dia/ GG Pop Up", 'width=480px,height=200px,center=1,resize=1,scrolling=0','../');
	}
	
	function openmypage_comp(inc)
	{
		var page_link="requires/yarn_count_determination_sweater_controller.php?action=composition_popup&inc="+inc;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Composition Popup", 'width=480px,height=350px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var hidcompid=this.contentDoc.getElementById("hidcompid").value;
			var hidcompname=this.contentDoc.getElementById("hidcompname").value;

			$('#txtcompone').val(hidcompname);
			$('#cbocomponeid').val(hidcompid);
			//check_duplicate(inc,1);
		}
	}
</script>
</head>	
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%; position:relative; margin-bottom:5px; margin-top:5px">
		<? echo load_freeze_divs ("../../",$permission);  ?>
        <form name="yarncountdetermination_1" id="yarncountdetermination_1" autocomplete="off">
            <fieldset style="width:1000px;">
                <legend>Yarn Count Determination[Sweater]</legend>
                <table width="100%" border="0" cellpadding="0" cellspacing="2">
                    <tr>
                        <td width="110" class="must_entry_caption">Fabric Nature</td>
                        <td width="120"><?=create_drop_down( "cbo_fabric_nature",110, $item_category,"", 0, "", '', "",$disabled,"100" ); ?></td>
                        <td width="110">RD No.</td>
                        <td width="120"><input type="text" id="txtrdno" name="txtrdno" class="text_boxes" style="width:100px" value="" /></td>
                        <td width="110">Mill Ref</td>
                        <td width="120"><input type="text" id="txtmillRef" name="txtmillRef" class="text_boxes" style="width:100px" value="" /></td>
                        <td width="110">Construction</td>
                        <td><input type="text" id="txtconstruction" name="txtconstruction" class="text_boxes" style="width:100px" value="" /></td>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">Fab. Composition</td>
                        <td>
                        	<input type="text" id="txtcompone" name="txtcompone" class="text_boxes" style="width:100px" value="" readonly placeholder="Browse" onDblClick="openmypage_comp(1);" />
                        	<input type="hidden" id="cbocomponeid" name="cbocomponeid" class="text_boxes" style="width:50px" value="" />
                        </td>
                        <td class="must_entry_caption">Count</td>
                        <td><?=create_drop_down( "cbocountcotton", 110, "select id, yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1," -Select Count-", '', '','',''); ?></td>
                    	<td>Yarn Type</td>
                        <td><?=create_drop_down("cbotypecotton", 110, $yarn_type,"", 1,"-Select-",'','check_duplicate(1,this.id)','','','','',$ommitYarnType); ?></td>
                        <td>Color Range</td>
                        <td><? echo create_drop_down( "cbocolorrange", 110, $color_range,"", 1, "-Select-", '', "",$disabled,"" ); ?></td>
                    </tr>
                    <tr> 
                        <td>Gauge</td>
                        <td><?=create_drop_down( "cbo_gauge", 110, $gauge_arr,"", 1, "--Gauge--", $selected, "" ); ?></td>
                        <td>Process Loss</td>
                        <td><input type="text" id="txt_process_loss" name="txt_process_loss" class="text_boxes_numeric" style="width:100px" value="" /></td>
                        <td>Sequence No</td>
                        <td><input type="text" id="txt_seq_no" name="txt_seq_no" class="text_boxes_numeric" style="width:100px" value="" /></td>
                        <td>Status</td>	
                        <td>
                        <? echo create_drop_down("cbo_status", 110, $row_status, "", "", "", 0, "","","1,2"); ?>
                        <input type="hidden" id="update_mst_id" value=""/>                        	
                        </td>
                    </tr>
                    <tr>
                    	<td>Sys. Code</td>
                        <td><input type="text" id="txt_sys_code" name="txt_sys_code" class="text_boxes" style="width:100px" value="" readonly /></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" class="button_container"><?=load_submit_buttons( $permission, "fnc_fabric_count_determination", 0,0 ,"reset_form('yarncountdetermination_1','','')",1); ?> 
                        </td>				
                    </tr>
                </table>
            </fieldset>
            <fieldset style="width:680px; display:none">
                <legend>Composition Dtls</legend>
                <div id="form_div">
                    <table width="100%" border="0" id="tbl_yarn_count" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" >
                        <thead>
                            <tr>
                            	<th width="150" class="must_entry_caption">Composition</th><th width="50" class="must_entry_caption">%</th><th width="150">Count</th><th width="150">Type</th><th>&nbsp;</th> 
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="yarncost_1" align="center">
                                <td width="150">
                                	<input type="text" id="txtcompone_1"  name="txtcompone_1"  class="text_boxes" style="width:140px" value="" readonly placeholder="Browse" onDblClick="openmypage_comp(1);" />
                                    <input type="hidden" id="cbocompone_1"  name="cbocompone_1" class="text_boxes" style="width:50px" value="" />
                                </td>
                                <td width="50"><input type="text" id="percentone_1"  name="percentone_1" onChange="sum_percent()" class="text_boxes_numeric" style="width:50px" value="" /></td>
                                <td width="70"><? echo create_drop_down( "cbocountcotton_1", 150, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1," -- Select Count --", '', 'check_duplicate(1,this.id)','','' ); ?>
                                </td>
                                <td width="100">
                                	<? 
                                	echo create_drop_down( "cbotypecotton_1", 150, $yarn_type,"", 1, "-- Select --", '', 'check_duplicate(1,this.id)','','','','',$ommitYarnType ); ?> 
                                </td>
                                <td> 
                                    <input type="button" id="increase_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(1)" />
                                    <input type="button" id="decrease_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1);" />
                                    <input type="hidden" id="updateid_1" name="updateid_1"  class="text_boxes" style="width:20px" value=""  />  
                                    
                                </td>  
                            </tr>
                        </tbody>
                    </table>

                            <tr><td> <input type="hidden"  class="text_boxes" style="width:30px" name="total_percent" id="total_percent" value=""></td></tr>
                </div>
                <br/>
                <table width="100%" border="" cellpadding="0" cellspacing="0"  rules="all">
                    <tr>
                        <td colspan="5" align="center" class="button_container"><? // echo load_submit_buttons( $permission, "fnc_fabric_count_determination", 0,0 ,"reset_form('yarncountdetermination_1','','')",1); ?> 
                        </td>				
                    </tr>	
                </table>
            </fieldset>
        </form>	
		<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
		<script> //set_field_level_access(<? echo end(array_keys($_SESSION['logic_erp']['data_arr'][184]));?>); </script>
        
        <div id="yarn_count_container">
			<?
			//$composition_arr=array();
			$lib_yarn_count=return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
			$user_arr=return_library_array( "select user_full_name,id from user_passwd", "id", "user_full_name");
			$lib_group_short=return_library_array( "select id,group_short_name from lib_group where id=1 and status_active=1", "id", "group_short_name");
			$group_short_name=$lib_group_short[1];
			//$sql_q="select mst_id, copmposition_id, percent, count_id, type_id from lib_yarn_count_determina_dtls where is_deleted=0  order by id";
			
			/*$data_array=sql_select($sql_q);
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
						$composition_arr[$row[csf('mst_id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]];
					}
					$sys_code=$group_short_name.'-'.$row[csf('mst_id')];
					$sysCodeArr[$row[csf('mst_id')]]=$sys_code;
				}
			}
			unset($data_array);*/
			//print_r($sysCodeArr);				
			$sql="select id, fab_nature_id, rd_no, mill_ref, construction, color_range_id, gauge, process_loss, sequence_no, fab_composition, fabric_composition_id, count, yarn_type, inserted_by, status_active from lib_yarn_count_determina_mst where is_deleted=0 and entry_form=461 order by id DESC";				
			$arr=array (3=>$lib_yarn_count, 4=>$yarn_type, 8=>$user_arr, 9=>$row_status);
			echo  create_list_view ( "list_view", "Sys Code,Construction,Fab. Composition,Count,Yarn Type,RD No,Mill Ref.,Seq.,Insert By,Status", "70,120,150,70,70,80,80,50,100","900","350",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form'",1, "0,0,0,count,yarn_type,0,0,0,inserted_by,status_active", $arr , "id,construction,fab_composition,count,yarn_type,rd_no,mill_ref,sequence_no,inserted_by,status_active", "requires/yarn_count_determination_sweater_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0,0,0') ;
			exit();
            ?>
        </div>
    </div>
</body>

</html>
