<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Item List Report
				
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	17/12/2013
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
echo load_html_head_contents("Item List","../../", 1, 1, $unicode,1);
?>	
<script>
var permission='<? echo $permission; ?>';

	function openmypage_item_group()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		else
		{	
			var data = $("#cbo_company_id").val()+"_"+$("#cbo_item_category").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/item_list_controller.php?data='+data+'&action=item_group_popup', 'Item Group Search', 'width=800px,height=420px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("item_group_id");
				var theemailval=this.contentDoc.getElementById("item_group_val");
				if (theemailid.value!="" || theemailval.value!="")
				{
					//alert (theemailid.value);
					freeze_window(5);
					$("#txt_group_id").val(theemailid.value);
					$("#txt_item_group").val(theemailval.value);
					release_freezing();
				}
			}
		}
	}
	
	function openmypage_subgroup()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		else
		{	
			var data = $("#cbo_company_id").val()+"_"+$("#cbo_item_category").val()+"_"+$("#txt_group_id").val()
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/item_list_controller.php?data='+data+'&action=item_subgroup_popup', 'Item Sub Group Search', 'width=800px,height=420px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("item_subgroup_id");
				var theemailval=this.contentDoc.getElementById("item_subgroup_val");
				if (theemailid.value!="" || theemailval.value!="")
				{
					//alert (theemailid.value);
					freeze_window(5);
					$("#txt_item_subgroup_id").val(theemailid.value);
					$("#txt_item_subgroup").val(theemailval.value);
					release_freezing();
				}
			}
		}
	}
	
	function fn_report_generated()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_item_category*txt_group_id*txt_item_subgroup_id',"../../")+'&report_title='+report_title;
			freeze_window(3);
			http.open("POST","requires/item_list_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
	 		show_msg('3');
			release_freezing();
		}
	}
	
</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../");  ?>
        <form id="itemList_1">
         <h3 style="width:1050px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:1050px" >      
            <fieldset>  
                <table cellpadding="0" cellspacing="2" width="1020px" class="tbl_capacity_allocation">
                    <tr>
                        <td width="80" class="must_entry_caption"><strong>Company </strong></td>
                        <td width="150">
							<? 
								echo create_drop_down( "cbo_company_id", 145, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", $selected,"" );
                            ?>
                        </td>
                        <td width="70"><strong>Category </strong></td>
                        <td width="145">
							<?
								echo create_drop_down( "cbo_item_category", 140, $item_category,"", 0, "", 0, "","","","","","1,2,3,4,12,13,14" );
                            ?>
                        </td>
                        <td width="100"><strong>Item Group</strong></td>
                        <td width="140">
                        	<input type="text" name="txt_item_group" id="txt_item_group" class="text_boxes" style="width:140px" placeholder="Double Click" onDblClick="openmypage_item_group();" /><input type="hidden" name="txt_group_id" id="txt_group_id" class="text_boxes" style="width:140px" />
                        </td>
                        <td width="130"><strong>Item Sub Group</strong></td>
                        <td width="140">
                        	<input type="text" name="txt_item_subgroup" id="txt_item_subgroup" class="text_boxes" style="width:140px" placeholder="Double Click" onDblClick="openmypage_subgroup();" /><input type="hidden" name="txt_item_subgroup_id" id="txt_item_subgroup_id" class="text_boxes" style="width:140px" />
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="fn_report_generated()" style="width:80px" class="formbutton" />
                        </td>
                    </tr>
                </table>
            </fieldset>
            </div>
        </form>
        <div id="report_container" align="center"></div>
        <div id="report_container2"> 
    </div>
    </div>
</body>
<script>
	set_multiselect('cbo_item_category','0*0','0','','0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>