<?
/*-------------------------------------------- Comments -----------------------
Version (MySql)          :  V2
Version (Oracle)         :  V1
Converted by             :  MONZU
Converted Date           :  24-05-2014
Purpose			         : 	This Form Will Create Woven Garments Price Quotation Entry.
Functionality	         :	
JS Functions	         :
Created by		         :	Monzu 
Creation date 	         : 	18-10-2012
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
/*
if(index_page !=1) window.location.href = "../../logout.php"; */
var permission = '<? echo $permission; ?>';
//Master form---------------------------------------------------------------------------
function openmypage(page_link,title)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		
		if(title=="Job/Order Selection Form")
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
			get_php_form_data( theemail.value, action, "requires/sample_stripe_color_measurement_controller" );
			show_list_view(theemail.value,'show_fabric_cost_listview','cost_container','requires/stripe_color_measurement_controller_urmi','');
			show_list_view(theemail.value+'_'+fabric_cost_id+'_'+cbo_color_name,'stripe_color_list_view','stripe_color_list_view_container','requires/sample_stripe_color_measurement_controller','');

			release_freezing();
		}
	}
}

function set_data(fabric_cost_id)
{
	get_php_form_data( fabric_cost_id, 'set_data', "requires/sample_stripe_color_measurement_controller" );
	var txt_job_no=document.getElementById('txt_job_no').value;
	var cbogmtsitem=document.getElementById('cbogmtsitem').value;
    load_drop_down( 'requires/sample_stripe_color_measurement_controller', txt_job_no+'_'+cbogmtsitem+'_'+fabric_cost_id, 'load_drop_down_color', 'color_td' )
}

function open_color_popup()
{
	var txt_job_no=document.getElementById('txt_job_no').value;
	var hidd_job_id=document.getElementById('hidd_job_id').value;
	var cbogmtsitem=document.getElementById('cbogmtsitem').value;
	var fabric_cost_id=document.getElementById('fabricdescription').value;
	var cbo_color_name=document.getElementById('cbo_color_name').value;
	var cbo_company_name=document.getElementById('cbo_company_name').value;
	var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
	if(cbo_color_name==0)
	{
		return;
	}
	var page_link="requires/sample_stripe_color_measurement_controller.php?action=open_color_list_view&txt_job_no="+trim(txt_job_no)+"&hidd_job_id="+hidd_job_id+"&cbogmtsitem="+cbogmtsitem+"&fabric_cost_id="+fabric_cost_id+"&cbo_color_name="+cbo_color_name+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name;
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Stripe Details", 'width=750px,height=400px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		show_list_view(txt_job_no+'_'+fabric_cost_id+'_'+cbo_color_name,'stripe_color_list_view','stripe_color_list_view_container','requires/sample_stripe_color_measurement_controller','');
	}
}

function show_content_data(fabric_cost_id,cbo_color_name)
{
	set_data(fabric_cost_id);
	document.getElementById('fabricdescription').value=fabric_cost_id;
	document.getElementById('cbo_color_name').value=cbo_color_name;
	open_color_popup();
}

</script>
</head>
 
<body onLoad="set_hotkey()" >
    <div style="width:100%;" align="center">
        <div style="display:none"><?=load_freeze_divs ("../../",$permission); ?></div>
         <fieldset style="width:1070px;">
         <legend>Stripe Color Details</legend>
            <form name="precosting_1" id="precosting_1" autocomplete="off"> 
                <div style="width:1050px;">  
                    <table width="100%" cellspacing="2" cellpadding="" border="0">
                        <tr>
                            <td align="right" width="120" class="must_entry_caption">Requisition Id</td>
                            <td width="150">
                                <input  style="width:150px;" type="text" title="Double Click to Search" class="text_boxes" placeholder="New Job No" name="txt_job_no" id="txt_job_no" readonly />  
                                <input type="hidden" name="hidd_job_id" id="hidd_job_id" style="width:30px;" class="text_boxes" />                   
                            </td>
                            <td align="right" width="150">Company</td>
                            <td width="150"><? echo create_drop_down("cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sample_stripe_color_measurement_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );"); ?></td>                                        
                            <td align="right">Buyer</td>
                            <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 160, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" ); ?></td>
                            <td align="right" width="120">Style Ref</td>
                            <td>
                                <input class="text_boxes" type="text" style="width:150px;" name="txt_style_ref" id="txt_style_ref" maxlength="75" title="Maximum 75 Character" readonly/>
                                <input type="hidden" id="update_id" value="" />
                            </td>
                        </tr>
                        <tr>
                        	<td width="100%" align="center" valign="top" id="cost_container" colspan="8"></td>
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
		</fieldset>
    </div>
    <div id="stripe_color_list_view_container"></div>
</body>
<script>
<?
if($txt_job_no !="")
{
	?>
	var fabric_cost_id='';
	var cbo_color_name='';
	var txt_job_no='<? echo $txt_job_no;?>';
	var sample_mst_id='<? echo $hidd_job_id;?>';
	get_php_form_data(txt_job_no, 'populate_data_from_job_table', "requires/sample_stripe_color_measurement_controller" );
	show_list_view(sample_mst_id,'show_fabric_cost_listview','cost_container','requires/sample_stripe_color_measurement_controller','');
	show_list_view(txt_job_no+'_'+sample_mst_id+'_'+cbo_color_name,'stripe_color_list_view','stripe_color_list_view_container','requires/sample_stripe_color_measurement_controller','');
	<?
}
?>
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
