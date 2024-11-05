<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create TNA Task Template

Functionality	:	
				
JS Functions	:

Created by		:	CTO 
Creation date 	: 	14-11-2012
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
 
 echo load_html_head_contents("TNA Task Template", "../", '', 1, $unicode,'','');

//$task_short_arr = return_library_array("select id,task_short_name from lib_tna_task where status_active=1","id","task_short_name");
//$buyer_name_arr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and FIND_IN_SET(1, 'party_type') and FIND_IN_SET(3, 'party_type') and FIND_IN_SET(21, 'party_type') and FIND_IN_SET(90, 'party_type')","id","buyer_name");

?>	 
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  

var permission='<? echo $permission; ?>';
 	 
function openmypage(page_link,title)
{
	
	var page_link='requires/tna_task_template_controller.php?action=task_template&company_id='+document.getElementById('cbo_company_id').value+'&cbo_task_type='+document.getElementById('cbo_task_type').value+'&cbo_buyer_specific='+document.getElementById('cbo_buyer_specific').value;
	
	//alert(page_link);return;
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=300px,center=1,resize=1,scrolling=0','')
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var sel_id=this.contentDoc.getElementById("txt_selected_id");
		if (sel_id.value!="")
		{
			if(title!="Template Search")
			{/*
				var dd=sel_id.value.split(',');
				document.getElementById('txt_total_task').value=dd.length;
				freeze_window(5);
				//get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/tna_task_template_controller" );
				show_list_view(sel_id.value,'show_list_view_task','po_list_view','requires/tna_task_template_controller','');
				//set_button_status(0, permission, 'fnc_trims_approval',1);
				release_freezing();
				*/ 
			}
			else
			{
				freeze_window(5);
				get_php_form_data( sel_id.value, "populate_data_from_search_popup", "requires/tna_task_template_controller" );
				show_list_view(sel_id.value,'show_list_view_template','po_list_view','requires/tna_task_template_controller','');
				set_button_status(1, permission, 'fnc_tna_task_template',1);
				release_freezing();
			}
		}
	}
}

function openmypage_task( vid, title, is_single )
{  	var selected_task_id_str='';
	var vi=vid.split("_");
	if(!is_single) var is_single=0;
	else
	{
		
		if( $("#hiddentaskid_"+vi[1]).val()=="" || $("#hiddentaskid_"+vi[1]).val()==0 )
		 return;	
	}
		
	if ($("#selected_task_id").val()=="")
	{
		var row_num=$('#tbl_task_template tr').length-1;
		$("#selected_task_id").val('');
		for (var v=1; v<row_num; v++)
		{	
			if ($("#hiddentaskid_"+v).val()!="")
			{ 
				if ($("#selected_task_id").val()=="" ) $("#selected_task_id").val( $("#hiddentaskid_"+v).val( )); else $("#selected_task_id").val( $("#selected_task_id").val()+","+$("#hiddentaskid_"+v).val());
				
			}
		}
	}
	
	
	
	for(var i=1; i<=$('#tbl_task_template tr').length-1; i++)
		{	
			if($("#hiddentaskid_"+i).val()!=''){
				if(selected_task_id_str==''){selected_task_id_str+=$("#hiddentaskid_"+i).val();}
				else{selected_task_id_str+=','+$("#hiddentaskid_"+i).val();}
			}
			
		}
	
	
	
	var page_link='requires/tna_task_template_controller.php?action=task_name_search&selected_ids='+$("#selected_task_id").val()+'&is_single='+is_single+'&task_id='+$("#hiddentaskid_"+vi[1]).val()+'&selected_task_id_str='+selected_task_id_str+'&task_type='+$("#cbo_task_type").val();
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=300px,center=1,resize=1,scrolling=0','')
	emailwindow.onclose=function()
	{
 		var theform=this.contentDoc.forms[0];
		var task_short_name_id=this.contentDoc.getElementById("task_short_name_id").value;
		var task_short_name=this.contentDoc.getElementById("task_short_name").value;
		
		if (task_short_name_id!="")
		{
			task_short_name_id=task_short_name_id.split(",");
			task_short_name=task_short_name.split("*");
			var fld=vid.split("_");
			var row_num=$('#tbl_task_template tr').length-1;
			 
			if( is_single==1 ) 
			{
				$("#hiddendependtaskid_"+vi[1]).val(task_short_name_id);
				$("#"+vi[0]+"_"+vi[1]).val(task_short_name);
				return;
			}
			if (fld[1]==row_num)
			{
				for (var v=0; v<task_short_name_id.length; v++)
				{
					var jj=(row_num*1)+v;
					$("#hiddentaskid_"+jj).val(task_short_name_id[v]);
					$("#"+fld[0]+"_"+jj).val(task_short_name[v]);
					add_new_tr( jj );
				}
			}
			else // single
			{
				for (var v=0; v<task_short_name_id.length; v++)
				{
					if(v==0)
					{
						$("#hiddentaskid_"+fld[1]).val(task_short_name_id[0]);
						$("#"+vid).val(task_short_name[0]);
					}
					else
					{
						var jj=((fld[1]*1)-1)+v;
						$("#hiddentaskid_"+jj).val(task_short_name_id[v]);
						$("#"+fld[0]+"_"+jj).val(task_short_name[v]);
						add_new_tr( jj );
					}
				}
				
			}
			var row_num=$('#tbl_task_template tr').length-1;
			$("#selected_task_id").val('');
			for (var v=1; v<row_num; v++)
			{
				if ($("#hiddentaskid_"+v).val()!="")
				{
					if ($("#selected_task_id").val()=="" ) $("#selected_task_id").val( $("#hiddentaskid_"+v).val( )); else $("#selected_task_id").val( $("#selected_task_id").val()+","+$("#hiddentaskid_"+v).val())
				}
			}
		} 
	}
}

function fnc_tna_task_template( operation )
{
	
	if(operation==2){
		if(confirm("Delete this! If you sure click OK button. Otherwise  click Cancel button.")==false){return;}	
	}
	if (form_validation('txt_lead_time','Lead Time')==false)
	{
		return;
	}	
	else   
	{
		
		var row_num=$('#tbl_task_template tr').length-1;
		var counter = 0;var ListArray=[0];var dataStr=Array();
		
 		dataStr.push('txt_system_id*cbo_company_id*txt_lead_time*cbo_material_source*txt_total_task*cbo_buyer_specific*cbo_task_type');
		
		
		for (var i=1; i<=row_num; i++)
		{
			if (row_num==1)
			{
				if($("#hiddentaskid_"+i).val()!="" && ($("#txtdeadline_"+i).val()=="" || $("#txtdeadline_"+i).val()==0 || $("#txtexecutiondays_"+i).val()=="" || $("#txtexecutiondays_"+i).val()==0 || $("#txtnoticebefore_"+i).val()=="" || $("#txtnoticebefore_"+i).val()==0))
				{
					if (form_validation('hiddentaskid_'+i+'*txtdeadline_'+i+'*txtexecutiondays_'+i+'*txtnoticebefore_'+i+'','Task Name*Deadline*Execution Days*Notice Before')==false && $("#hiddentaskid_"+i).val()!="")
					{
						return;
					}
				}
				else
				{
					//data_all=data_all+get_submitted_data_string('hiddentaskid_'+i+'*txtdeadline_'+i+'*txtexecutiondays_'+i+'*txtnoticebefore_'+i+'*txtsequenceno_'+i+'*cbostatus_'+i+'*updateid_'+i+'*hiddendependtaskid_'+i,"../",i);
					dataStr.push('hiddentaskid_'+i+'*txtdeadline_'+i+'*txtexecutiondays_'+i+'*txtnoticebefore_'+i+'*txtsequenceno_'+i+'*cbostatus_'+i+'*updateid_'+i+'*hiddendependtaskid_'+i);
				}
			}
			else
			{
				if($("#hiddentaskid_"+i).val()!="" && ($("#txtdeadline_"+i).val()=="" || $("#txtdeadline_"+i).val()==0 || $("#txtexecutiondays_"+i).val()=="" || $("#txtexecutiondays_"+i).val()==0 || $("#txtnoticebefore_"+i).val()=="" || $("#txtnoticebefore_"+i).val()==0))
				{
					if (form_validation('hiddentaskid_'+i+'*txtdeadline_'+i+'*txtexecutiondays_'+i+'*txtnoticebefore_'+i+'','Task Name*Deadline*Execution Days*Notice Before')==false && $("#hiddentaskid_"+i).val()!="")
					{
						return;
					}
				}
				else
				{
					//data_all=data_all+get_submitted_data_string('hiddentaskid_'+i+'*txtdeadline_'+i+'*txtexecutiondays_'+i+'*txtnoticebefore_'+i+'*txtsequenceno_'+i+'*cbostatus_'+i+'*updateid_'+i+'*hiddendependtaskid_'+i,"../",i);
					dataStr.push('hiddentaskid_'+i+'*txtdeadline_'+i+'*txtexecutiondays_'+i+'*txtnoticebefore_'+i+'*txtsequenceno_'+i+'*cbostatus_'+i+'*updateid_'+i+'*hiddendependtaskid_'+i);
					counter++;
				}
			} 
		} 
		dataStr=dataStr.join('*');
		data_all=get_submitted_data_string(dataStr,"../");
		
	
		
		var data="action=save_update_delete&operation="+operation + data_all;
		freeze_window(operation);
		http.open("POST","requires/tna_task_template_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_tna_task_template_reponse;
	}
	 
}
	 
function fnc_tna_task_template_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');
		show_msg(trim(reponse[0]));
		$("#txt_system_id").val(trim(reponse[1]));
  		if(reponse[0]==2){reset_form('tnatemplateapproval_1','','');}
		if(trim(reponse[1])){set_button_status(1, permission, 'fnc_tna_task_template',1);}
		release_freezing();
		//get_php_form_data(reponse[1], "populate_data_from_search_popup", "requires/tna_task_template_controller" );
		show_list_view(reponse[1],'show_list_view_template','po_list_view','requires/tna_task_template_controller','');
	}
}



function add_new_tr( i )
{
	var row_num=$('#tbl_task_template tr').length-1;
	
	if (row_num!=i)
	{
		return false;
	}
	else
	{
		i++;
		 $("#tbl_task_template tr:last").clone().find("input,select").each(function() {
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return '' }              
			});
		  }).end().appendTo("#tbl_task_template");
		  $("#tbl_task_template tr:last td:eq(4)").attr('id','seq_td_'+i);
		  $("#tbl_task_template tr:last td:eq(4)").html('999');//('id','seq_td_'+i);
		  set_all_onclick();
		  $('#txt_total_task').val(i);
		  $('#cbostatus_'+i).val(1);
		  var k=i-1;
		  $('#increase_'+k).removeAttr("value").attr("value","-");
		  $('#increase_'+i).removeAttr("value").attr("value","+");
		  $('#decrease_'+i).removeAttr("value").attr("value","-");
		  $('#txtsequenceno_'+i).removeAttr("onblur").attr("onblur","set_seq("+i+",this.value);");  
		  $('#increase_'+i).removeAttr("onclick").attr("onclick","javascript:add_new_tr("+i+");");
		  $('#decrease_'+i).removeAttr("onclick").attr("onclick","javascript:fn_deleteRow("+i+");");
	}
}

function fn_deleteRow(rowNo) 
{   
   var numRow = $('table#tbl_task_template tbody tr').length; 
   if(numRow==rowNo && rowNo!=1)
   {
	   $('#tbl_task_template tbody tr:last').remove();
	   $('#txt_total_task').val(numRow-1);
   }
   else
   {
   		reset_form('','','hiddentaskid_'+rowNo+'*txttaskshortname_'+rowNo+'*txtdeadline_'+rowNo+'*txtexecutiondays_'+rowNo+'*txtnoticebefore_'+rowNo+'*txtsequenceno_'+rowNo+'*cbostatus_'+rowNo+'*updateid_'+rowNo);
   } 
}

function serialize_table_row()
{
 	var $table = $('#tbl_task_template');
    var $rows = $('tbody > tr',$table);
    $rows.sort(function(a, b){
        var keyA = ($('td:eq(4)',a).text());
        var keyB = $('td:eq(4)',b).text();
        	return (keyA*1 > keyB*1) ? 1 : 0;
    });
    $.each($rows, function(index, row){
      $table.append(row);
    });
}

function set_seq( id, val )
{
	$("#seq_td_"+id).html(val);// ('cindex',val);
}
</script>
 
</head>
 
<body onLoad="set_hotkey()">

<div style="width:100%;" align="center">
												 
     <? echo load_freeze_divs ("../",$permission);  ?>
   
     <table width="90%" cellpadding="0" cellspacing="2" align="center">
     	<tr>
        	<td width="70%" align="center" valign="top">  <!--   Form Left Container -->
            	<form name="tnatemplateapproval_1" id="tnatemplateapproval_1" autocomplete="off">
            	<fieldset style="width:950px;">
                <legend>TNA Task Template</legend>
                	<table  cellspacing="2" cellpadding="0" border="0">
                       <tr>
                            <td align="center" colspan="12">  <b> System ID : </b>
                                <input style="width:120px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/tna_task_template_controller.php?action=task_template','Template Search')" class="text_boxes" autocomplete="off" placeholder="Search TNA" name="txt_system_id" id="txt_system_id" readonly />
                                 
                             </td>
                       </tr>
                       <tr>
                            <td align="right"> Company </td>              
                            <td  width="100" ><? echo create_drop_down( "cbo_company_id", 150, "select id, company_name from lib_company where status_active =1 and is_deleted=0 order by company_name","id,company_name", 1, "-- All Company --", $selected, "load_drop_down( 'requires/tna_task_template_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );?></td>
                             <td align="right" class="must_entry_caption">Buyer</td>
                             <td id="buyer_td">
									<?
										if($db_type==0)
										{
                                        echo create_drop_down( "cbo_buyer_specific", 150, "select buy.id, buy.buyer_name from lib_buyer buy  where status_active =1 and is_deleted=0 $buyer_cond  and (FIND_IN_SET(1, party_type) or FIND_IN_SET(3, party_type) or FIND_IN_SET(21, party_type) or FIND_IN_SET(90, party_type)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
										}
										else
										{
											echo create_drop_down( "cbo_buyer_specific", 150, "select distinct(buy.id), buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );
										}
                                    ?>
                            </td>
                            <td align="right" class="must_entry_caption">Lead Time </td>
                            <td width="45">
                                	<input type="text" name="txt_lead_time" id="txt_lead_time" style="width:100px" class="text_boxes_numeric">
                               
                            </td>
                            <!--<td align="right" class="must_entry_caption">Material Source</td>
                            <td width="90">
                              <? //echo create_drop_down( "cbo_material_source", 100, $material_source,"", 1, "-- Select --", $selected, "",'' );	?>	
                              
                            </td>-->
                            <input type="hidden" id="cbo_material_source" value="0">
                            <td align="right" class="must_entry_caption">Type</td>
                            <td width="90">
                              <? 
							  	echo create_drop_down( "cbo_task_type", 90, $template_type_arr,"", 1, "-- Select --",1, "",'' );		
								?>	
                            </td>
                            <td align="right">Total Task</td>
                            <td>
                                  <input style="width:40px;" disabled readonly type="text"  name="txt_total_task" id="txt_total_task" value="1" class="text_boxes_numeric"  />
                                                            
                            </td>
                        </tr>
                        <tr>
                        	<td colspan="12" height="15"></td>
                        </tr>
                        <tr>
                        	<td align="center" colspan="12" valign="top" id="po_list_view">
                            	
                             	<table id="tbl_task_template" class="rpt_table" rules="all" border="1">
                                	<thead>
                                    <tr>
                                        <th width="200" class="must_entry_caption">Task Short Name </th>
                                        <th width="100" class="must_entry_caption">Deadline</th>
                                        <th width="100" class="must_entry_caption">Execution Days</th>
                                        <th width="100" class="must_entry_caption">Notice Before </th>	
                                        <th width="10" style="display:none"> </th>					     
                                        <th width="100">Sequence No</th>
                                        <th width="100">Dependant Task</th>
                                        <th>Status
                                        	<input type="hidden" name="selected_task_id" id="selected_task_id">
                                        </th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    	<tr>
                                            <td>
                                                <input type="text" name="txttaskshortname_1" id="txttaskshortname_1" style="width:190px" class="text_boxes" placeholder="Double Click To Search" onDblClick="openmypage_task($(this).attr('id'),'Task Name Search')" readonly />
                                                <input type="hidden" id="hiddentaskid_1" name="hiddentaskid_1" value="" />
                                            </td>
                                            <td>
                                                <input name="txtdeadline_1" type="number" id="txtdeadline_1" style="width:90px" class="text_boxes_numeric"/>
                                            </td>
                                            <td>
                                                <input name="txtexecutiondays_1"  type="text" id="txtexecutiondays_1" style="width:90px" class="text_boxes_numeric"/>
                                            </td>
                                            <td>
                                                <input name="txtnoticebefore_1" type="text" id="txtnoticebefore_1" style="width:90px" class="text_boxes_numeric"/>
                                                    
                                            </td>
                                             <td width="10" id="seq_td_1" style="display:none"></td>	
                                            <td cindex="">
                                            	 <input name="txtsequenceno_1"  type="text" id="txtsequenceno_1" onBlur="set_seq( 1, this.value )" style="width:90px" class="text_boxes_numeric"/>
                                            </td>
                                            <td cindex="">
                                            	 <input name="txtdependanttask_1"  type="text" id="txtdependanttask_1" onDblClick="openmypage_task($(this).attr('id'),'Task Name Search',1)" style="width:90px" placeholder="Double Click"  class="text_boxes"/>
                                                  <input type="hidden" id="hiddendependtaskid_1" name="hiddendependtaskid_1" value="" />
                                            </td>
                                            <td>
                                            	<?
                                            		echo create_drop_down( "cbostatus_1", 80, $row_status,"", '', "", $selected, "" );
												?>
                                            	<input type="hidden" id="updateid_1">
                                            </td>
                                            <td width="65">
                            					<input type="button" id="increase_1" style="width:30px" class="formbutton" value="+" onClick="add_new_tr(1)" />&nbsp;<input type="button" id="decrease_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deleteRow(1);" />
                                            </td>
                                        </tr> 
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" colspan="12" height="10">
                                
                            </td>
                        </tr>
                        <tr>
                            <td align="center" colspan="12" valign="middle" class="button_container">
                            	
                              <? echo load_submit_buttons( $permission, "fnc_tna_task_template", 0,0 ,"",1) ; //reset_form('tnatemplateapproval_1','','')?>
                              &nbsp;&nbsp;
							  <input type="button" class="formbutton" style="width:100px" value="Serialize" onClick="serialize_table_row()">&nbsp;&nbsp;
							  <input type="button" class="formbutton" style="width:100px" value="Copy" onClick="fnc_tna_task_template(5)">
                            </td>
                       </tr>
                    </table>
                 
              </fieldset>
              </form>
           </td>
         </tr>
         
	</table>
	</div>
</body>
           
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>





<!--<td align="right" width="100">Task Library</td>
<td id="buyer_td">
	<input style="width:90px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/tna_task_template_controller.php?action=task_library','TNA TASK Selection Form')" class="text_boxes" autocomplete="off" placeholder="Search Task" name="txt_total_task" id="txt_total_task" value="1" readonly />                              
</td>-->









