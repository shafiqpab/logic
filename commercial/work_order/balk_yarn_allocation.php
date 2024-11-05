<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create bulk yarn allocation
				
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	25/10/2014
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
echo load_html_head_contents("Yarn Item allocatiom","../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function openmypage_yarn()
	{
		var page_link='requires/balk_yarn_allocation_controller.php?action=yarn_refarence_surch';
		var title="Search Yarn";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1020px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var prod_ref=this.contentDoc.getElementById("prod_id_des").value.split("_"); // product ID
			$("#txt_yarn_descripton").val(prod_ref[1]);
			$("#hidden_yarn_id").val(prod_ref[0]);
			show_list_view(prod_ref[0],'show_dtls_list_view','list_container','requires/balk_yarn_allocation_controller','');
			//alert(prod_ref[0]);
		}
	}




function add_factor_row( i) 
{	
	var chargefor=0;
	var row_num=$('#tbl_pay_head tbody tr').length;
	//alert(row_num);
	if (row_num!=i)
	{
		return false;
	}
		i++;
	
	//alert(chargefor);
 
 	$("#tbl_pay_head tbody tr:last").clone().find("input,select").each(function() {
		$(this).attr({
		'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i; },
		'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i; },
		'value': function(_, value) { if(value=='+' || value=="-"){return value}else{return ''} }              
		});
		
	}).end().appendTo("#tbl_pay_head");
	
	
		var k=i-1;
		$('#incrementfactor_'+k).hide();
		$('#decrementfactor_'+k).hide();
		$('#cbobuyername_'+i).val('');
	  
	  $('#txtamount_'+i).removeClass().addClass("text_boxes_numeric");
	  $('#txtamount_'+i).removeAttr("onBlur").attr("onBlur","total_val("+i+");");
	  $('#incrementfactor_'+i).removeAttr("onClick").attr("onClick","add_factor_row("+i+");");
	  $('#decrementfactor_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+");");
}


function fn_deletebreak_down_tr(rowNo ) 
{
	
	$('#total_val_foot').html(number_format(($('#total_val_foot').html()*1)-($('#txtamount_'+rowNo).val()*1),2,".",""));
	
	var numRow = $('#tbl_pay_head tbody tr').length;
	if(numRow==rowNo && rowNo!=1)
	{
		var k=rowNo-1;
		$('#incrementfactor_'+k).show();
		$('#decrementfactor_'+k).show();
		$('#tbl_pay_head tbody tr:last').remove();
	}
	else
		return false;
	
}

function total_val(j)
{
	var stock_val=($('#current_stock').html().replace(/\,/g,''))*1;
	var total_val=($('#total_val_foot').html()*1);
	var current_val=($('#txtamount_'+j).val()*1);
	total_val=total_val+current_val;
	if(total_val>stock_val)
	{
		alert("Allocate Quantity Not Over Stock Quantity");
		$('#txtamount_'+j).val(0);
		return;
	}
	
	$('#total_val_foot').html(number_format(total_val,2,".",""));
}




function fnc_charge_payment( operation )
{
	
	var row_num=$('#tbl_pay_head tbody tr').length;
	var hidden_yarn_id=$('#hidden_yarn_id').val();
	var data_all="";
	for (var i=1; i<=row_num; i++)
	{
		data_all=data_all+get_submitted_data_string('cbobuyername_'+i+'*txtamount_'+i+'*txtremarks_'+i,"../../");
		if( form_validation('txtamount_'+i,'allocate quantity')==false )
		{
			return;
		}
		
	}
	//alert(data_all);return;
	var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+data_all+'&hidden_yarn_id='+hidden_yarn_id;//+'&update_id='+update_id
	freeze_window(operation);
	http.open("POST","requires/balk_yarn_allocation_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_charge_payment_response;
}

function fnc_charge_payment_response()
{
	
	if(http.readyState == 4) 
	{
	    var reponse=trim(http.responseText).split('**');
		//alert(http.responseText);
		//release_freezing();
		if(reponse[0]==0 || reponse[0]==1)
		{
			show_msg(trim(reponse[0]));
			set_button_status(1, permission, 'fnc_charge_payment',1,1);
			release_freezing();

		}
		else if(reponse[0]==10 || reponse[0]==15)
		{
			show_msg(trim(reponse[0]));
			release_freezing();
			return;
		}
	}
}

	
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="balk_yarn_1" id="balk_yarn_1" autocomplete="off" >
    <h3 align="left" id="accordion_h1" style="width:350px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div style="width:350px;" align="center" id="content_search_panel">
        <fieldset style="width:350px;">
                <table class="rpt_table" width="350" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th width="300" class="must_entry_caption">Yarn Description</th>                                
                    </tr>
                </thead>
                <tr class="general">
                    <td align="center">
                        <input style="width:300px;"  name="txt_yarn_descripton" id="txt_yarn_descripton"  ondblclick="openmypage_yarn()"  class="text_boxes" placeholder="Double click for the description"   />   
                        <input type="hidden" name="hidden_yarn_id" id="hidden_yarn_id"/>           
                    </td>
                </tr>
                
            </table> 
        </fieldset> 
           
    </div>
    <br /> 
    
        <!-- Result Contain Start-------------------------------------------------------------------->
        <fieldset style="width:1000px;">
        	<div id="list_container" align="center"></div>
        </fieldset>
        <!-- Result Contain END-------------------------------------------------------------------->
    
    
    </form>    
</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
