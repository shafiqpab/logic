<?
/*-------------------------------------------- Comments 
Version          : 
Purpose			 : 
Functionality	 :	
JS Functions	 :
Created by		 : Monir Hossain
Creation date 	 : 13/02/2016
Requirment Client: 
Requirment By    : 
Requirment type  : 
Requirment       : 
Affected page    : 
Affected Code    :              
DB Script        : 
Updated by 		 : 
Update date		 : 
QC Performed BY	 :		
QC Date			 :	
Comments		 : 
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
//function load_html_head_contents($title, $path, $filter, $popup, $unicode, $multi_select, $am_chart)

echo load_html_head_contents("sample receive", "../", 1, 1,$unicode,'','');
?>	
<script>
    if ($('#index_page', window.parent.document).val() != 1)
        window.location.href = "../../logout.php";

    var permission = '<? echo $permission; ?>';

    function fnc_sample_receive(operation)
    {
		if(operation==4){fnc_print_bundle()
		}else{
		
        if (form_validation('receive_date*cbo_fabric_cat*txt_item*txt_style*txt_designer*txt_qty','Date*Category*style*Item*Designer*Quantity') == false)
		
		
        {
            return;
        }
        
        var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('receive_date*cbo_fabric_cat*txt_item*txt_style*cbo_fabric_source*cbo_supplier_source*txt_designer*txt_qty*cbo_fabric_natu*txt_construction*txt_Composition*txt_gsm*cbo_yarn_count*cbo_yarn_type*cbo_status*update_id*color_qty_breakdown', "../");
        //alert(data);
        freeze_window(operation);
        http.open("POST", "requires/sample_inquiry_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_sample_receieve_reponse;
        
    }
	}
    function fnc_sample_receieve_reponse()
    {
        if (http.readyState == 4)
        {
            //alert(http.responseText);
            var reponse = trim(http.responseText).split('**');
			show_msg(reponse[0]);
			if (reponse[0] == 0)
			{
				reset_form('samplereceive_1', '', '');
				show_list_view(reponse[1], 'search_list_view', 'sample_list_view', 'requires/sample_receive_controller', 'setFilterGrid("list_view",-1)');
				// set_button_status(0, permission, 'fnc_sample_receive', 1);
			}
			release_freezing();
        }
    }

   
	
	function fnc_print_bundle()
	{

	   var receive_date = $('#receive_date').val();
	   var update_id = $('#update_id').val();	
			//alert (update_id);
			if(update_id=='')
			{   
			alert('Please, Select data from List view.');
			}
			else
			{
	   print_report(update_id,"sample_receive_print", "requires/sample_receive_controller")
			}
	}
function chanfab(){
	var i= $('#cbo_fabric_source').val();
	if(i==1)$('#cbo_supplier_source').attr('disabled',true);else $('#cbo_supplier_source').attr('disabled',false);
	if(i==2)
	alert('Please, Select Supplier. ');
	$('#cbo_supplier_source').focus()
	}
</script>
</head>
<body>0
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",''); ?>
		 <form name="sample_inquiry_1" id="sample_inquiry_1"> 
        
         <div id="content_search_panel">      
             <fieldset style="width:800px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <tbody>
                        	<tr> 
                            	<th width="52%">Barcode Scan: <input type="text" id="txt_bar_code" name="txt_bar_code" class="text_boxes"  /></th>
                                

                                <td width="48%"><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:100px" onClick="fn_report_generated()"/></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
                 <fieldset style="width:300px;margin-top:10px;">
            <legend></legend>
            <div style="width:810px; margin-top:10px;" id="sample_list_view" align="left">
        
            </div>
        </fieldset>	
            </div>
		</form>
	</div>
    <div id="report_container" align="center"></div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">show_list_view('', 'search_list_view', 'sample_list_view', 'requires/sample_inquiry_controller', 'setFilterGrid("list_view",-1)');	
</script>
</html>