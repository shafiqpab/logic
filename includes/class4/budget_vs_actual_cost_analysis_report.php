<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Development Record
Functionality	:	
JS Functions	:
Created by		:	Rakib
Creation date 	: 	18.09.2022
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
if( $_SESSION['logic_crm']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$sql_notes=sql_select("select id, notes from crm_update_notes where status_active=1 and is_deleted=0 order by id asc");
if (count($sql_notes) > 0) {
	$i=1;
	$all_notes='';
	foreach ($sql_notes as $row) {
		$all_notes.= '#'.$i.': '.$row[csf('notes')].',&nbsp';
		$i++;
	}
}
//echo $all_notes;
//----------------------------------------------------------------------------------------------------------------

echo load_html_head_contents("Update Request Report", "../../",1, 1, $unicode,1,'');
?>
<script type="text/javascript" charset="utf-8">
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php"; 
	var permission='<? echo $permission; ?>';

    //save status
	function fnc_status_update( operation, increment_id )
	{
		//var updateid = $("#updateid_"+increment_id).val();
		//var dtlsid = $("#dtlsid_"+increment_id).val();
		var data1="action=save_update_delete&operation="+operation+"&increment_id="+increment_id;
		var data2="";
		data2+=get_submitted_data_string('updateid_'+increment_id+'*dtlsid_'+increment_id+'*scriptdtlsid_'+increment_id+'*cbo_update_status_'+increment_id+'*txtcomments_'+increment_id,'../../');
		var data=data1+data2;
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/update_request_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_status_update_reponse;
	}	
	
	function fnc_status_update_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			show_msg(reponse[0]);
			release_freezing();
		}
	}	

	//Report generate start
	function fn_generate_report(type)
	{	
		var data="action=load_php_dtls_form"+get_submitted_data_string('cbo_customer_name*cbo_project_type*cbo_service_category*cbo_update_status*cbo_update_to*txt_date_from*txt_date_to*cbo_entry_form_report',"../../")+'&report_type='+type;
		//alert(data);  return;
		freeze_window(3);
		http.open("POST","requires/update_request_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_generate_report_reponse;  
	}
	
	function fn_generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			setFilterGrid("tbl_list",-1);
			set_all_onclick();
			show_msg('3');
		    release_freezing();
		}
	}

	function popuppage_dr(i)
	{
		var txtscript = $("#txtscript_"+i).val();
		var txtscript2 = $("#txtscript2_"+i).val();
		//alert(txtscript2);
		var page_link='requires/update_request_report_controller.php?action=dr_popup&txtscript='+txtscript+'&txtscript2='+txtscript2;
		
		var title="Pending Script";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=600px, height=300px, center=1, resize=0, scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("txtscript").value;
			var theemail2=this.contentDoc.getElementById("txtscript2").value;
			$('#txtscript_'+i).val(theemail);
			$('#txtscript2'+i).val(theemail2);
			release_freezing();	 
		}		
	}
	
	function show_inner_filter(e)
	{
		//alert (e.value)
		if (e!=13) {var unicode=e.keyCode? e.keyCode : e.charCode } else {unicode=13;}
		if (unicode==13 )
		{
			generate_report(2);
		}
	}

	function update_status_check()
	{
		var tot_row=document.getElementById('totrow').value;		
		var is_checked=document.getElementById('update_status_check_id').checked;
		//alert(tot_row);
		for (var k=1; k<=tot_row; k++)
		{
			if (is_checked) $('#cbo_update_status_'+k).val(2);
			else $('#cbo_update_status_'+k).val(0);
		}
	}

	function remarks_popup(mst_id,action)
	{ 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/update_request_report_controller.php?mst_id='+mst_id+'&action='+action, 'Remarks Popup', 'width=500px,height=210px,center=1,resize=0,scrolling=0','../');
	}

	function update_tips_popup(issue_id,action)
	{ 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/update_request_report_controller.php?issue_id='+issue_id+'&action='+action, 'Update Tips Popup', 'width=600px,height=260px,center=1,resize=0,scrolling=0','../');
	}

	function update_script_popup(company_id,page_id,action)
	{ 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/update_request_report_controller.php?company_id='+company_id+'&page_id='+page_id+'&action='+action, 'Update Script Popup', 'width=600px,height=260px,center=1,resize=0,scrolling=0','../');
	}

	function search_date_function(id)
	{
		if (id==1) $("#search_by_date_td").html('Update Req Date');
		else $("#search_by_date_td").html('Updated Date');		
	}

</script>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>		 
     <form name="developmentrecord_1" id="developmentrecord_1" autocomplete="off" > 
	 <h3 style="width:1100px; " align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Update Request</h3>
     <div id="content_search_panel" style="width:1100px" align="center" >
        <fieldset>
        <table width="1100" cellpadding="0" cellspacing="2" align="center" class="rpt_table">
			<thead>
				<tr>
					<th width="100">Project Type</th>
					<th width="100">Service category</th>
					<th width="160">Company</th>
                    <th  width="100" >Entry Form/Report</th>
					<th width="100">Update Status</th>
					<th width="100">Update To</th>
					<th width="100" id="search_by_date_td"><? echo "Update Req Date"; ?></th>
					<th width="200" colspan="2"><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" /></th>
				</tr>
			</thead>
         	<tbody>
         		<tr>
                	<td width="100"> 
			   			<? echo create_drop_down( "cbo_project_type", 100,$module_type,"", 1, "-- Select --", 0, "",0,"" ); ?>
					</td>
					
					<td id="category_td">
						<?
						echo create_drop_down( "cbo_service_category",160, $service_category,'', 1, "-- Select --", "", "", "",'','' );
						?>	
					</td>
               	 
                    <td width="160">
					    <? 
					    echo create_drop_down( "cbo_customer_name", 160, "select a.id, a.buyer_name from lib_buyer a where a.is_mkt=0 and a.status_active=1 and a.is_deleted=0 order by a.buyer_name","id,buyer_name",1, "--Select--", 0,"",0);
                        ?> 
                    </td>
                    <td  width="100">            
                    <? 
                    echo create_drop_down( "cbo_entry_form_report", 100, $entry_form_reports_arr,"", 1, "-- Select --", "", "","","" );
                    ?>
                </td>					
                	<td width="100">
						<?
						$update_status_arr=array(1=>'Pending',2=>'Done',3=>'Hold');
						echo create_drop_down("cbo_update_status",100, $update_status_arr,"","","-- Select --",1,"search_date_function(this.value);","","" );
						?>
                	</td>
					<td width="100">
						<?
						echo create_drop_down("cbo_update_to",100, $update_on_arr,"",1,"-- Select --","","","","" ); 
						?>
                	</td>
					<td width="160" align="center">
                   		<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date" >&nbsp;To&nbsp;
                   		<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date" >
                 	</td>
              
					<td width="100" align="center">
						<input type="button" name="search" id="search" value="Show" onClick="fn_generate_report(1)" style="width:80px" class="formbutton" />
						<input type="button" name="search1" id="search1" value="Summery" onClick="fn_generate_report(2)" style="width:80px" class="formbutton" />
					</td>
            	</tr>
         	</tbody>
        </table> 
        </fieldset>
    </div>
    </form>
		<div><marquee style="height:20px; font-size:18px;" onMouseOver="this.setAttribute('scrollamount', 0, 0);" OnMouseOut="this.setAttribute('scrollamount', 4, 0);"><ul><li style="color: red;  list-style-type: none;"><strong>
		<? echo 'Update Restricted:-&nbsp;'.rtrim($all_notes,',&nbsp'); ?>
		</strong></li></ul></marquee></div>
        <div id="report_container" align="center"></div>
  		<div id="report_container2"></div> 
     </form>
    </div>
</body>
<script>set_multiselect('cbo_customer_name','0','0','','0');</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
