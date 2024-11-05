<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Electronic Approval Setup
				
Functionality	:	
JS Functions	:
Created by		:	REZA 
Creation date 	: 	17-12-2013
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
echo load_html_head_contents("Electronic Approval Setup Info","../", 1, 1, $unicode,1,1); 
?>	

<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
var permission='<? echo $permission; ?>';
 
<?
$designation_a=return_library_array("select id,custom_designation from lib_designation","id","custom_designation");
$designation_ar= json_encode($designation_a);
echo "var designation_arr = ". $designation_ar . ";\n";

$user_id=$_SESSION['logic_erp']['user_id'];
$user_code = $_SESSION['logic_erp']["user_code"];
	?>
	var user_id='<? echo $user_id; ?>';
	var user_code='<? echo $user_code; ?>';
	
	function make_validation()
	{               
		var data=$("#cbo_company_name").val()+"_"+$("#cbo_Report_id").val();
		var is_exists = return_global_ajax_value( data, 'check_data_is_exis', '', 'requires/electronic_approval_setup_controller');
				
		if( is_exists.trim()=='yes')
		{
			alert("Already this Page/Report is exists. You can select from the list");
			$("#cbo_Report_id").val("0");
			return;
		}
	}

	
	function fnc_electronic_approval_setup(operation)
	{
		freeze_window(3);
		if($("#cbo_tag_report").val()==36 || $("#cbo_tag_report").val()==49 || $("#cbo_tag_report").val()==50 || $("#cbo_tag_report").val()==57 || $("#cbo_tag_report").val()==70)
		{
			if( form_validation('cbo_Report_id*cbo_tag_report','Report Id*Tag Report')==false )
			{
				release_freezing();
				return;
			}
			if($("#cbo_company_name").val()>0) {
				alert("Company Wise Entry Not Allowed For This Page.");
				release_freezing();
				return;
			}
		}
		else
		{
			if( form_validation('cbo_company_name*cbo_Report_id*cbo_tag_report','Company Name*Report Id*Tag Report')==false )
			{
				release_freezing();
				return;
			}
		}
		
		if(user_code!='SUPERADMIN')
		{
			if(operation==1 || operation==2)
			{
				alert("Update/Delete Restricted. If need Please Contract With MIS");
				release_freezing();
				return;
			}
		}
	
		var torow=$('#evaluation_tbl tbody tr').length;
		//alert(row_num);return;
		var dataArr=Array();
		var data2="";
		
		for(var i=1; i<=torow; i++)
		{
			if (form_validation('userid_'+i+'*txtcanbypass_'+i+'*txtsequenceno_'+i,'User Id*By Pass*Sequence')==false)
			{
				release_freezing();
				return;
			}
			
			dataArr.push('userid_'+i+'*txtcanbypass_'+i+'*txtsequenceno_'+i+'*txtbuyerid_'+i+'*txtbrandid_'+i+'*updateid_'+i+'*txtdepartmentid_'+i+'*txtgroup_'+i+'*txtlocationid_'+i+'*txtitemcatid_'+i+'*txtfbsourceid_'+i);
			
		}
		
		data2=dataArr.join('*');
		data2=get_submitted_data_string(data2,"../",i);
		 
		var dataString="cbo_company_name*cbo_Report_id*cbo_Report_id*cbo_tag_report";
		var data1="action=save_update_delete&operation="+operation+"&torow="+torow+get_submitted_data_string(dataString,"../");
		var data =data1+data2;
		
		http.open("POST","requires/electronic_approval_setup_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_emp_info_reponse;
	}
 
	function fnc_emp_info_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);
			var response=trim(http.responseText).split('**');
			//alert (response); release_freezing();
			if(response[0]==0||response[0]==1)
			{
				show_msg(trim(response[0]));
				load_grid();
			}
			else if(response[0]==11 || response[0]==21)
			{
				alert(response[1]);
			}
			
			release_freezing();
		}
	}

	function load_grid()
	{
		reset_form('electronicApprovalSetup_1','','','','','cbo_company_name*txtsequenceno_1*txtgroup_1');
		$('#evaluation_tbl tbody tr:not(:first)').remove();
		$('#incrementfactor_1').show();
		$('#decrementfactor_1').show();
		
		var company_id=$("#cbo_company_name").val();
		show_list_view(company_id,'create_emp_list_view','list_container','requires/electronic_approval_setup_controller','setFilterGrid("list_view",-1)');
	}

	// <!--System ID-->
	function open_qepopup(id)
	{ 
		var row_num=$('#evaluation_tbl tbody tr').length;	
		//alert (id);
		
		if( form_validation('cbo_Report_id','Page/Report Name')==false )
		{
			return;
		}
		 
		var uid = $("#allid").val();
		var ud = $("#alldeg").val();
		var tri = $("#alltrid").val();
		//id++;
		var page_link="requires/electronic_approval_setup_controller.php?action=quot_popup"; 
		var title="Electronic Approval Setup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=590px,height=420px,center=1,resize=0,scrolling=0','')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				//freeze_window(5);
				for(var j=1; j<=row_num; j++)
				{
					if(document.getElementById('userid_'+j).value==response[0]){alert("This user is duplicate."); return;}
					
				}
				document.getElementById('userid_'+id).value=response[0];
				document.getElementById('txtsigningauthority_'+id).value=response[1];
				document.getElementById('txtfullname_'+id).value=response[2];
				document.getElementById('txtdesignation_'+id).value=designation_arr[response[3]];
				//continue;
				
				release_freezing();
			}
		}
	}
 

	function add_factor_row( i) 
	{	
		if( form_validation('txtsigningauthority_'+i,'Full Name')==false )
		{
			return;
		}
		var row_num=$('#evaluation_tbl tr').length-1;
		if (row_num!=i)
		{
			return false;
		}
		i++;
	
	 
		$("#evaluation_tbl tr:last").clone().find("input,select").each(function() {
			$(this).attr({
			'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i; },
			'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i; },
			'value': function(_, value) { if(value=='+' || value=="-"){return value}else{return ''} }              
			});
		}).end().appendTo("#evaluation_tbl");
			var k=i-1;
			$('#incrementfactor_'+k).hide();
			$('#decrementfactor_'+k).hide();
			$('#txtsigningauthority_'+i).removeAttr("onDblClick").attr("onDblClick","open_qepopup("+i+");");
			$('#txtdepartment_'+i).removeAttr("onDblClick").attr("onDblClick","openDepartmentPopup("+i+");");
			$('#txtbuyer_'+i).removeAttr("onDblClick").attr("onDblClick","open_buyerpopup("+i+");");
			$('#txtsequenceno_'+i).removeAttr("onKeyUp").attr("onKeyUp","checkSequence("+i+",this.value);");
			$('#txtbrand_'+i).removeAttr("onDblClick").attr("onDblClick","open_brandpopup("+i+");");
			$('#txtlocation_'+i).removeAttr("onDblClick").attr("onDblClick","openLocationPopup("+i+");");
			$('#txtitemcat_'+i).removeAttr("onDblClick").attr("onDblClick","openItemCatPopup("+i+");");
			$('#txtfbsource_'+i).removeAttr("onDblClick").attr("onDblClick","openSourcePopup("+i+");");
		  
			$('#incrementfactor_'+i).removeAttr("onClick").attr("onClick","add_factor_row("+i+");");
			$('#decrementfactor_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+','+'"evaluation_tbl"'+");");
	
		sl=i<10?'0'+i:i;
		$( "#evaluation_tbl tr:last" ).find( "td:first" ).html(sl);
		//$( "#evaluation_tbl tr::input[type='text']" ).find( "input[type=text]:last").value(i);
		document.getElementById('txtsequenceno_'+i).value=i;
		$('#txtgroup_'+i).val(i);
	}

	function approved_data_sync(i)
	{
		var company_name   = $('#cbo_company_name').val();
		var cbo_report_id  = $('#cbo_Report_id').val();
		var cbo_tag_report = $('#cbo_tag_report').val();
		 
		if(form_validation('cbo_tag_report', 'Tag Report')==false)
		{
			return;
		}
		 
		var rowCount  = $('#evaluation_tbl tbody tr').length;
		var title     = 'Approved Data Sync';
		var page_link = 'requires/electronic_approval_setup_controller.php?rowCount='+rowCount+'&company_name='+company_name+'&cbo_report_id='+cbo_report_id+'&cbo_tag_report='+cbo_tag_report+'&action=openpopup_approved_sync';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=700px,height=340px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var form_user_id  = this.contentDoc.getElementById("select_cbo_form_user").value;
			var form_sqp      = this.contentDoc.getElementById("select_cbo_form_sqp").value;
			var department_id = this.contentDoc.getElementById("select_txtdepartmentids").value;
			var buyer_id      = this.contentDoc.getElementById("select_buyer_id").value;
			var brand_id      = this.contentDoc.getElementById("select_brand_id").value;
			var location_id   = this.contentDoc.getElementById("select_location_id").value;
			var store_id      = this.contentDoc.getElementById("select_store_id").value;
			var to_user_id    = this.contentDoc.getElementById("select_cbo_to_user").value;
			var to_user_sqp   = this.contentDoc.getElementById("select_cbo_to_sqp").value;
			
			$('#select_cbo_form_user').val(form_user_id);
			$('#select_cbo_form_sqp').val(form_sqp);
			$('#select_txtdepartmentids').val(department_id);
			$('#select_buyer_id').val(buyer_id);
			$('#select_brand_id').val(brand_id);
			$('#select_location_id').val(location_id);
			$('#select_store_id').val(store_id);
			$('#select_cbo_to_user').val(to_user_id);
			$('#select_cbo_to_sqp').val(to_user_sqp);
		}
	}

	function fn_deletebreak_down_tr(rowNo,table_id ) 
	{
		var numRow = $('#'+table_id+' tbody tr').length;
		if(numRow==rowNo && rowNo!=1)
		{
			var k=rowNo-1;
			$('#incrementfactor_'+k).show();
			$('#decrementfactor_'+k).show();
			
			$('#'+table_id+' tbody tr:last').remove();
		}
		else
			return false;
	}
	function checkSequence(id,v)
	{
		var id1=((id-1)==0)?id:(id-1);
	
		if(($('#evaluation_tbl tbody tr').length) < v)
		{
			alert("Serial brake not allowed");
			document.getElementById('txtsequenceno_'+id).value = id;
		}
		else if(v != id)
		{
			alert('Duplicate sequence number not allowed.');
			document.getElementById('txtsequenceno_'+id).value = id;
		}
	}
	// pop multiple Buyer 
	function open_buyerpopup(i)
	{
		var hidden_buyer_id = $('#txtbuyerid_'+i).val();
		var company_name    = $('#cbo_company_name').val();
	 
		if($("#cbo_tag_report").val() == 36 || $("#cbo_tag_report").val() == 45 || $("#cbo_tag_report").val() == 46 || $("#cbo_tag_report").val() == 70)
		{
		}
		else{
			if( form_validation('cbo_company_name','Company Name') == false )
			{
				return;
			}
		}

		var title = 'Buyer Name Selection Form';	
		var page_link = 'requires/electronic_approval_setup_controller.php?hidden_buyer_id='+hidden_buyer_id+'&company_name='+company_name+'&action=buyer_name_popup';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform      = this.contentDoc.forms[0]
			var process_id   = this.contentDoc.getElementById("hidden_process_id").value;
			var process_name = this.contentDoc.getElementById("hidden_process_name").value;
			$('#txtbuyerid_'+i).val(process_id);
			$('#txtbuyer_'+i).val(process_name);
		}
	}
	
	function open_brandpopup(i)
	{
		var hidden_buyer_id = $('#txtbuyerid_'+i).val();
		var hidden_brand_id = $('#txtbrandid_'+i).val();
		var company_name    = $('#cbo_company_name').val();
		if($("#cbo_tag_report").val()==36 || $("#cbo_tag_report").val()==45 || $("#cbo_tag_report").val()==46)
		{
		}
		else{
			if( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
		}
		var title = 'Brand Name Selection Form';	
		var page_link = 'requires/electronic_approval_setup_controller.php?hidden_brand_id='+hidden_brand_id+'&company_name='+company_name+'&hidden_buyer_id='+hidden_buyer_id+'&action=brand_name_popup';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform      =this.contentDoc.forms[0]
			var process_id   = this.contentDoc.getElementById("hidden_process_id").value;
			var process_name = this.contentDoc.getElementById("hidden_process_name").value;
			$('#txtbrandid_'+i).val(process_id);
			$('#txtbrand_'+i).val(process_name);
		}
	}
	
	function openDepartmentPopup(i){
		var departmentid=$('#txtdepartmentid_'+i).val();
		var company_name   = $('#cbo_company_name').val();
		var cbo_tag_report = $('#cbo_tag_report').val();
		if($("#cbo_tag_report").val()==36  || $("#cbo_tag_report").val()==46)
		{
		}
		else{
			if( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
		}
		var title = 'Department';	
		var page_link = 'requires/electronic_approval_setup_controller.php?departmentid='+departmentid+'&company_name='+company_name+'&cbo_tag_report='+cbo_tag_report+'&departmentid='+departmentid+'&action=department_popup';
		
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=470px,height=350px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform      = this.contentDoc.forms[0];
			var process_id   = this.contentDoc.getElementById("hidden_process_id").value;
			var process_name = this.contentDoc.getElementById("hidden_process_name").value;
			$('#txtdepartmentid_'+i).val(process_id);
			$('#txtdepartment_'+i).val(process_name);
		}
	}

	function openLocationPopup(i)
	{
		var company_name = $('#cbo_company_name').val();
		if( form_validation('cbo_company_name','Company Name') == false )
		{
			return;
		}
	
		var title = 'Location Selection Form';	
		var page_link = 'requires/electronic_approval_setup_controller.php?locationid='+$('#txtlocationid_'+i).val()+'&company_name='+company_name+'&action=location_popup';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose = function()
		{
			var theform = this.contentDoc.forms[0]
			var location_id = this.contentDoc.getElementById("selected_location_id").value;
			var location_name = this.contentDoc.getElementById("selected_location_name").value;
			$('#txtlocationid_'+i).val(location_id);
			$('#txtlocation_'+i).val(location_name);
			
		}
	}

	function openItemCatPopup(i)
	{
		var company_name = $('#cbo_company_name').val();
		if( form_validation('cbo_company_name','Company Name') == false )
		{
			return;
		}
		var title = 'Item Cat. Selection Form';	
		var page_link = 'requires/electronic_approval_setup_controller.php?itemcatid='+$('#txtitemcatid_'+i).val()+'&company_name='+company_name+'&action=item_cat_popup';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=370px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform = this.contentDoc.forms[0]
			var itemcat_id = this.contentDoc.getElementById("selected_itemcat_id").value;
			var itemcat_name = this.contentDoc.getElementById("selected_itemcat_name").value;
			$('#txtitemcatid_'+i).val(itemcat_id);
			$('#txtitemcat_'+i).val(itemcat_name);
		}
	}

	function openSourcePopup(i)
	{
		var company_name = $('#cbo_company_name').val();
		if( form_validation('cbo_company_name','Company Name') == false )
		{
			return;
		}
		var title = 'Item Cat. Selection Form';	
		var page_link = 'requires/electronic_approval_setup_controller.php?sourceid='+$('#txtfbsourceid_'+i).val()+'&company_name='+company_name+'&action=source_popup';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=370px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform = this.contentDoc.forms[0]
			var source_id = this.contentDoc.getElementById("selected_source_id").value;
			var source_name = this.contentDoc.getElementById("selected_source_name").value;
			$('#txtfbsourceid_'+i).val(source_id);
			$('#txtfbsource_'+i).val(source_name);
		}
	}
	
</script>
</head>
 
<body onLoad="set_hotkey()">
    <div style="width:1350px; margin:0 auto;"><?=load_freeze_divs("../",$permission); ?></div>
        <fieldset style="width:1055px; margin:0 auto;">
        <legend>Electronic Approval Setup <b style="color: black;" id="note_view"></b> </legend> 
        <form name="electronicApprovalSetup_1" id="electronicApprovalSetup_1" autocomplete="off">
        <table width="1350" cellspacing="2" cellpadding="0" border="1"  id="tbl_quotation_evalu" rules="all">
            <tr>
                <td align="center">
                    <fieldset style="width:850px;" id="quotationevaluation_3">
                        <table width="840" cellspacing="2" cellpadding="0" border="1" rules="all">
                            <tr>
                            	<td class="must_entry_caption">Company</td>
                                <td><?=create_drop_down( "cbo_company_name", 180, "select comp.id,comp.company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_grid();" ); ?></td>
                                <td class="must_entry_caption">Page/Report Name </td>
                                <td><?=create_drop_down( "cbo_Report_id", 230, "select m_menu_id,menu_name from main_menu where report_menu=1 and status=1 order by menu_name","m_menu_id,menu_name", 1, "-- Select Page/Report Name --", $selected, "make_validation();",0,"","","","",10); ?></td>
                                <td class="must_entry_caption">Tag Report</td>
                                <td><?=create_drop_down( "cbo_tag_report", 181, $entry_form_for_approval,"", 1,"-- Select Page/Report Name --",$selected, "$('#department_td').text((this.value==11)?'Component':'Department');",0,"","","","",10); ?></td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
            <tr height="5"></tr>
            <tr>
                <td align="center">
                    <fieldset style="width:1350px;">
                        <table align="left" width="100%" cellspacing="0" cellpadding="0" id="evaluation_tbl"  border="1" class="rpt_table" rules="all">
                            <thead>
                                <th>SL 
									<input type="hidden" id="allid">
									<input type="hidden" id="alldeg">
									<input type="hidden" id="alltrid">
                                </th>
                                <th>Signing Authority</th>
                                <th>Full Name</th>
                                <th>Designation</th>
                                <th id="department_td">Department</th>
                                <th>Buyer</th>
                                <th>Brand</th>
                                <th>Location</th>
                                <th>Item Cat.</th>
                                <th>FB Source</th>
                                <th>Bypass</th>
                                <th>Seq No</th>
                                <th>Group</th>
                                <th width="70">&nbsp;</th>
                            </thead>
                            <tbody>
                                <tr>
                                	<td align="center" width="35">01</td>
                                    <td>
                                    	<input type="hidden" name="updateid_1" id="updateid_1" />
                                        <input type="hidden" name="userid_1" id="userid_1" />
                                        <input style="width:120px;" type="text" name="txtsigningauthority_1" readonly placeholder="Double Click" id="txtsigningauthority_1" onDblClick="open_qepopup(1);"  class="text_boxes" />
                                    </td>
                                    <td><input style="width:120px;" type="text" readonly name="txtfullname_1" id="txtfullname_1"  class="text_boxes" /></td>
                                    <td><input style="width:120px;" type="text" readonly name="txtdesignation_1" id="txtdesignation_1"  class="text_boxes" /></td>
                                    <td>
                                    	<input style="width:120px;" type="text" readonly name="txtdepartment_1" id="txtdepartment_1"  class="text_boxes" onDblClick="openDepartmentPopup(1);" placeholder="Browse" />
                                    	<input type="hidden" readonly name="txtdepartmentid_1" id="txtdepartmentid_1" />
                                    </td>
                                    <td>
                                        <input style="width:100px;" type="text" readonly name="txtbuyer_1" id="txtbuyer_1"  class="text_boxes" onDblClick="open_buyerpopup(1);" placeholder="Browse" />
                                         <input type="hidden" name="txtbuyerid_1" id="txtbuyerid_1"/>
                                    </td>
                                    <td>
                                        <input style="width:80px;" type="text" readonly name="txtbrand_1" id="txtbrand_1"  class="text_boxes" onDblClick="open_brandpopup(1)" placeholder="Browse" />
                                        <input type="hidden" name="txtbrandid_1" id="txtbrandid_1" />
                                    </td>
									<td>
                                        <input style="width:80px;" type="text" readonly name="txtlocation_1" id="txtlocation_1"  class="text_boxes" onDblClick="openLocationPopup(1)" placeholder="Browse" />
                                        <input type="hidden" name="txtlocationid_1" id="txtlocationid_1"/>
                                    </td>
									<td>
                                        <input style="width:80px;" type="text" readonly name="txtitemcat_1" id="txtitemcat_1"  class="text_boxes" onDblClick="openItemCatPopup(1)" placeholder="Browse" />
                                        <input type="hidden" name="txtitemcatid_1" id="txtitemcatid_1"/>
                                    </td>
			
									<td>
                                        <input style="width:80px;" type="text" readonly name="txtfbsource_1" id="txtfbsource_1"  class="text_boxes" onDblClick="openSourcePopup(1)" placeholder="Browse" />
                                        <input type="hidden" name="txtfbsourceid_1" id="txtfbsourceid_1"/>
                                    </td>


                                    <td><?=create_drop_down( "txtcanbypass_1", 50, $yes_no,"", 1, "-- Select --", 2, "",0,"","","","",10); ?></td>
                                    <td><input style="width:40px;" type="text"  name="txtsequenceno_1" id="txtsequenceno_1"  value="1" onKeyUp="checkSequence(1,this.value);" class="text_boxes" /></td>
									<td><input style="width:40px;" type="text"  name="txtgroup_1" id="txtgroup_1"  value="1" class="text_boxes" /></td>
                                    <td width="70" align="center">
                                        &nbsp;
                                        <input style="width:30px;" type="button" id="incrementfactor_1" name="incrementfactor_1"  class="formbuttonplasminus" value="+" onClick="add_factor_row(1);"/>
                                        <input style="width:30px;" type="button" id="decrementfactor_1" name="decrementfactor_1"  class="formbuttonplasminus" value="-" onClick="javascript:fn_deletebreak_down_tr(1,'evaluation_tbl');"/>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td align="center" class="button_container">
                    <? echo load_submit_buttons($permission,"fnc_electronic_approval_setup",0,0,"reset_form('electronicApprovalSetup_1','list_container','','','$(\'#evaluation_tbl tbody tr:not(:first)\').remove();');",1); ?>
					<input type="button" class="formbutton" value="Approved Data Sync" onClick="approved_data_sync(1);" style="width:120px;"/>
                </td>
            </tr>
        </table>
    </form> 
    <div id="list_container" style="margin:0 auto; width:40%;">
    <?
	$sql="select a.company_id, a.page_id,a.entry_form,b.menu_name from electronic_approval_setup a, main_menu b where b.m_menu_id=a.page_id and a.company_id=0 and a.is_deleted=0 GROUP BY a.company_id, b.menu_name,a.page_id,a.entry_form";	
	//echo $sql;
	$arr=array(1=>$entry_form_for_approval);
	echo create_list_view("list_view", "Page/Report Name,Entry Form", "300,200","550","260",0, $sql, "get_php_form_data", "company_id,page_id", "'electronic_approval_setup_from_data','requires/electronic_approval_setup_controller'", 1, "0,entry_form", $arr , "menu_name,entry_form", "employee_info_controller",'','0,0') ;
    ?>
</div> 
<div>
	
</div>
</fieldset>
<script src="../includes/functions_bottom.js" type="text/javascript"></script> 
</body>
</html> 