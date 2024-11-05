<?
/*-------------------------------------------- Comments -----------------------
Version (MySql)          :  V2
Version (Oracle)         :  V1
Converted by             :  Aziz
Converted Date           :  30-08-2018
Purpose			         : 	This Form Will Create Woven Garments  Entry.
Functionality	         :	
JS Functions	         :
Created by		         :	Aziz 
Creation date 	         : 	 30-08-2018
Requirment Client        : 
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :                   
DB Script                : 
Updated by 		         : 		
Update date		         : 		   
QC Performed BY	         :		
QC Date			         :	
Comments		         :
-------------------------------------------------------------------------------*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Stripe Color Info","../../", 1, 1, $unicode,'','');
?>	
<script type="text/javascript">
// common for all----------------------------
/*$(window).bind('beforeunload', function(){
		return '>>>>>Before You Go<<<<<<<< \n Your custom message go here';
	});*/
/*var index_page=$('#index_page', window.parent.document).val();
if(index_page !=1)
{
	index_page=<? //echo $index_page;?>;
}
if(index_page !=1) window.location.href = "../../logout.php"; */
var permission = '<? echo $permission; ?>';
//Master form---------------------------------------------------------------------------
function openmypage(page_link,title)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		
		if(title=="Requisition Selection Form")
		{
			var action="populate_data_from_job_table";	
		}
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var theemail=this.contentDoc.getElementById("selected_job") //Access form field with id="emailfield"
		if (theemail.value!="")
		{
			var fabric_cost_id='';
			var cbo_color_name='';
			freeze_window(5);
			//reset_form('precosting_1','','','','')
			get_php_form_data( theemail.value, action, "requires/stripe_color_measurement_sample_requisition_controller" );
			show_list_view(theemail.value,'show_fabric_cost_listview','cost_container','requires/stripe_color_measurement_sample_requisition_controller','');
			show_list_view(theemail.value+'_'+fabric_cost_id+'_'+cbo_color_name,'stripe_color_list_view','stripe_color_list_view_container','requires/stripe_color_measurement_sample_requisition_controller','');

			release_freezing();
			
		}
		 
	}
}

function set_data(fabric_cost_id)
{
	
	get_php_form_data( fabric_cost_id, 'set_data', "requires/stripe_color_measurement_sample_requisition_controller" );
	var txt_requisition_id=document.getElementById('txt_job_no').value;
//	alert(fabric_cost_id);
	var cbogmtsitem=document.getElementById('cbogmtsitem').value;
	
    load_drop_down( 'requires/stripe_color_measurement_sample_requisition_controller', txt_requisition_id+'_'+cbogmtsitem+'_'+fabric_cost_id, 'load_drop_down_color', 'color_td' )

}

function open_color_popup()
{
		var txt_job_no=document.getElementById('txt_job_no').value;
	    var cbogmtsitem=document.getElementById('cbogmtsitem').value;
		var fabric_cost_id=document.getElementById('fabricdescription').value;
		var cbo_color_name=document.getElementById('cbo_color_name').value;
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		if(cbo_color_name==0)
		{
			return;
		}
		var page_link="requires/stripe_color_measurement_sample_requisition_controller.php?action=open_color_list_view&txt_job_no="+trim(txt_job_no)+"&cbogmtsitem="+cbogmtsitem+"&fabric_cost_id="+fabric_cost_id+"&cbo_color_name="+cbo_color_name+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name;
		
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Set Details", 'width=750px,height=400px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
				
				show_list_view(txt_job_no+'_'+fabric_cost_id+'_'+cbo_color_name,'stripe_color_list_view','stripe_color_list_view_container','requires/stripe_color_measurement_sample_requisition_controller','');

			}
		
}

function show_content_data(fabric_cost_id,cbo_color_name)
{
	set_data(fabric_cost_id);
	document.getElementById('fabricdescription').value=fabric_cost_id;
	document.getElementById('cbo_color_name').value=cbo_color_name;
	open_color_popup()
}
function fn_deletebreak_down_tr(fabric_cost_id,cbo_color_name){
	var permission_array=permission.split("_");
	if(permission_array[2]!=1){
		alert("You have no delete permission");
		return;
	}
	var txt_job_no=document.getElementById('txt_job_no').value;
	if(fabric_cost_id !="" &&  permission_array[2]==1)
	{
		var response=return_global_ajax_value(fabric_cost_id+"_"+cbo_color_name+"_"+txt_job_no, 'delete_row', '', 'requires/stripe_color_measurement_sample_requisition_controller');
		if(response*1==11){
			alert("Yarn Booking Found for this Job, Delete not Possible");
			return;
		}
		if(response*1==1){
			alert("Data has been Deleted");
			var fabric_cost_id='';
	        var cbo_color_name='';
			show_list_view(txt_job_no+'_'+fabric_cost_id+'_'+cbo_color_name,'stripe_color_list_view','stripe_color_list_view_container','requires/stripe_color_measurement_sample_requisition_controller','');
		}
		else{
			alert("Problem Found, Delete not Successfull");
		}
	}
}

</script>
</head>
 
<body onLoad="set_hotkey()" >
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",$permission);  ?>
         <fieldset style="width:1070px;">
         <legend>Sample-Requisition</legend>
        <table width="90%" cellpadding="0" cellspacing="2" align="center">
            <tr>
                <td width="100%" align="left" valign="top">
                   
                        <form name="precosting_1" id="precosting_1" autocomplete="off"> 
                            <div style="width:1070px;">  
                                <table  width="100%" cellspacing="2" cellpadding=""  border="0">
                                    <tr>
                                        <td align="right" width="120" class="must_entry_caption">Requisition No</td>
                                        <td  width="150">
                                        <input  style="width:150px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/stripe_color_measurement_sample_requisition_controller.php?action=order_popup','Requisition Selection Form')" class="text_boxes" placeholder="New Requisition No" name="txt_job_no" id="txt_job_no" readonly />                    
                                        </td>
                                        <td  align="right"  width="150">Company</td>
                                        <td  width="150">
                                        <?
                                        echo create_drop_down("cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/stripe_color_measurement_sample_requisition_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );");
                                        ?>
                                        </td>                                        
                                        <td align="right">Buyer</td>
                                        <td id="buyer_td" >
                                        <? 
                                        echo create_drop_down( "cbo_buyer_name", 160, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                                        ?>
                                        </td>
                                       
                                        <td align="right"  width="120">Style Ref</td>
                                        <td>
                                        <input class="text_boxes" type="text" style="width:150px;" name="txt_style_ref" id="txt_style_ref" maxlength="75" title="Maximum 75 Character" readonly/>
                                        <input type="hidden" id="update_id" value="" />
                                        </td>
                                    </tr>
                                     
                                    
                                    <tr>
                                        <td width="100%" align="center" valign="top" id="cost_container" colspan="8" >
                                        </td>
                                        </tr>
                                    <tr> 
                                    
                                        <td align="center" valign="middle" class="button_container" colspan="8"> 
										<?
										//echo load_submit_buttons( $permission, "fnc_precosting_entry", 0,0 ,"reset_form('precosting_1','','','')",1,1) ;
										?>  
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            </form>
                </td>
            </tr>
            
        </table> 
          </fieldset>
    </div>
    <div id="stripe_color_list_view_container">
    </div>
</body>
<script>
<?
if($txt_requisition_id !="")
{
	?>
	var fabric_cost_id='';
	var cbo_color_name='';
	var txt_requisition_id='<? echo $txt_requisition_id;?>';
	//alert(txt_requisition_id);
	get_php_form_data(txt_requisition_id, 'populate_data_from_job_table', "requires/stripe_color_measurement_sample_requisition_controller" );
	show_list_view(txt_requisition_id,'show_fabric_cost_listview','cost_container','requires/stripe_color_measurement_sample_requisition_controller','');
	show_list_view(txt_requisition_id+'_'+fabric_cost_id+'_'+cbo_color_name,'stripe_color_list_view','stripe_color_list_view_container','requires/stripe_color_measurement_sample_requisition_controller','');
	<?
}
?>
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
