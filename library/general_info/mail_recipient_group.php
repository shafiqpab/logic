<?
/*-------------------------------------------- Comments

Purpose			: 	This form will be used for mail recipient group

Functionality	:	
					

JS Functions	:

Created by		:	Saidul Reza 
Creation date 	: 	22-12-2013
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
echo load_html_head_contents("Employee Info", "../../", 1, 1,$unicode,'','');


?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission='<? echo $permission; ?>';





function mail_recipient_group(operation)
{
	if( form_validation('company_id*mail_item*selected_id*status','Company Name*Mail Item*You Have Selected*Status')==false )
	{
		return;
	}
	var mail_type=$('#cbo_mail_type').val();

	// if(mail_type==2){
	// 	if( form_validation('cbo_mail_template','Mail Template')==false ){return;}
	// }

	var dataString = "company_id*mail_item*you_have_selected*status*update_id*text_brand_id*txt_buyer_id*cbo_mail_type*cbo_mail_template";
 	var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
   //alert(data);
	//freeze_window(operation);
	http.open("POST","requires/mail_recipient_group_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_emp_info_reponse;
}

function fnc_emp_info_reponse()
{
	if(http.readyState == 4) 
	{
		var response=trim(http.responseText).split('**');
		//alert (http.responseText);
		if(response[0]==0||response[0]==1||response[0]==2)
		{
			show_msg(trim(response[0]));
			
			show_list_view(document.getElementById('company_id').value+'_'+document.getElementById('mail_item').value,'item_list_view','item_list_container','requires/mail_recipient_group_controller','');
			
			show_list_view('','mail_list_view','list_container','requires/mail_recipient_group_controller','');

			
			reset_form('mailrecipientgroup','','','','','');
			set_button_status(0, permission, 'mail_recipient_group',1,1);
			release_freezing();
		}
		if(response[0]==11)
		{
			alert("Mail address should not be duplicate");
			//set_button_status(0, permission, 'fnc_emp_info',1,1);
			release_freezing();
		}
		if(response[0]==15)
		{
			alert("Duplicate data found");
			release_freezing();
		}
	}
}

// flowing script for multy select data------------------------------------------------------------------------------start;

//var selected_id = new Array();
//var selected_email = new Array();
 
function toggle( x, origColor ) 
{
	var newColor = 'yellow';
	if ( x.style ) {
	x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
	}
}


var selected_id = new Array();
var selected_email = new Array();
function js_set_value( str ) 
{
	var str=str*1;
	var email=trim($('#email_id'+str).html());

	toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );

	if( jQuery.inArray( str, selected_id ) == -1 ) 
	{
		selected_id.push( str );
		selected_email.push( email );
	}
	else 
	{
		for( var i = 0; i < selected_id.length; i++ ) 
		{
			if( selected_id[i] == str ) break;
		}
		selected_id.splice( i, 1 );
		selected_email.splice( i, 1 );
	}

	var id =''; var mail ='';
	for( var i = 0; i < selected_id.length; i++ ) 
	{
		id += selected_id[i] + ',';
		mail += selected_email[i] + ',';
	}

	id = id.substr( 0, id.length - 1 );
	mail = mail.substr( 0, mail.length - 1 );
	$('#you_have_selected').val( id );
	$('#selected_id').val( mail );
}
		
		
// avobe script for multy select data------------------------------------------------------------------------------end;
 
					  
function get_update_data ( itm_id,sys_id )
{
	
	document.getElementById('selected_id').value="";
	document.getElementById('you_have_selected').value="";

	$("#mail_setup").find('tbody tr').each(function()
	{
		try 
		{
			var txtOrginal=$(this).attr('id');
			txtOrginal=txtOrginal.split("_");
			var i=trim(txtOrginal[1]);
			var colorr=document.getElementById( 'tr_' +  i ).style.backgroundColor;
			if(colorr=='yellow'){document.getElementById( 'tr_' +  i ).style.backgroundColor='#FFFFCC'; }
		}
		catch(e) 	
		{
			//got error no operation
		}
		
	});
	
	var data=itm_id+"__"+document.getElementById('company_id').value+"__"+document.getElementById('txt_buyer_id').value+"__"+document.getElementById('text_brand_id').value+"__"+sys_id;
	get_php_form_data( data, 'mail_recipient_group_from_data', 'requires/mail_recipient_group_controller' ) ;
	
	var update_v=document.getElementById('update_vlues').value.split(",");	
	 selected_id = new Array();
 	 selected_email = new Array();
 	var tt=document.getElementById('update_vlues').value;
	for(var k=0; k<update_v.length; k++)
	{
		js_set_value( update_v[k] );
	}
}



    function openmypage(page_link,title)
    {
        var company_id= document.getElementById('company_id').value;

	   if (title=="Buyer Selection") {
            var data= document.getElementById('txt_buyer_id').value;
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+"&data="+data+"&company_id="+company_id, title, "width=640px,height=330px,center=1,resize=0,scrolling=0",'../')
        } 
		else if (title=="Brand Selection") {
            var data= document.getElementById('text_brand_id').value;
            var buyer_ids= document.getElementById('txt_buyer_id').value;
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+"&data="+data+"&buyer_id="+buyer_ids, title, "width=640px,height=330px,center=1,resize=0,scrolling=0",'../')
        }

        emailwindow.onclose=function()
        {
            var selected_id=this.contentDoc.getElementById("txt_selected_id");
            var selected_name=this.contentDoc.getElementById("txt_selected");

            if (title=="Buyer Selection")
            {
                document.getElementById('cbo_user_buyer_show').value=selected_name.value;
                document.getElementById('txt_buyer_id').value=selected_id.value;
            } 
			else if (title=="Brand Selection"){
                document.getElementById('text_brand_name_show').value=selected_name.value;
                document.getElementById('text_brand_id').value=selected_id.value;
            }
        }
    }





</script>

</head>

<body onLoad="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission);  ?>
<div style="width:400px; margin:0 auto; float:left">
	
<fieldset style="width:350px;">
	<legend>Mail Recipient Group</legend>
    <form name="mailrecipientgroup" id="mailrecipientgroup" autocomplete = "off">	
        <table cellpadding="0" cellspacing="2" align="center">
            <tr>
                <td align="right" width="100" class="must_entry_caption">Company Name</td>
                <td>
					<?
                    echo create_drop_down( "company_id", 162, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name", 1, "--- Select Company Name ---", 0, "" );
                    ?>
                </td>                
            </tr>
            <tr>
                <td align="right" class="must_entry_caption">Mail Item</td>
                <td>
					<? 
					$temp_form_list_for_mail=array();
					foreach($form_list_for_mail as $key=>$val){
						$temp_form_list_for_mail[$key]=$val." [".$key."]";
					} 
					echo create_drop_down( "mail_item", 162, $temp_form_list_for_mail,"", 1, "--- Select Mail Item ---", 0, "show_list_view(document.getElementById('company_id').value+'_'+this.value,'item_list_view','item_list_container','requires/mail_recipient_group_controller','');" );
                    ?>
                </td>                
            </tr>
            <tr>
                <td align="right">Buyer Name</td>
                <td>
                	<input type="text" name="cbo_user_buyer_show" placeholder="Double Click for Buyer" id="cbo_user_buyer_show" class="text_boxes" style="width:150px" onDblClick="openmypage('requires/mail_recipient_group_controller.php?action=buyer_selection_popup','Buyer Selection')" readonly>
                    <input type="hidden" name="txt_buyer_id" id="txt_buyer_id" class="text_boxes" style="width:220px" readonly >
                </td> 
            </tr>
            <tr>
                <td align="right">Brand</td>
                <td>
                	<input type="text" name="text_brand_name_show" placeholder="Double Click for Buyer" id="text_brand_name_show" class="text_boxes" style="width:150px" onDblClick="openmypage('requires/mail_recipient_group_controller.php?action=brand_selection_popup','Brand Selection')" readonly>
                    <input type="hidden" name="text_brand_id" id="text_brand_id" class="text_boxes" style="width:220px" readonly >
           		</td>
            </tr>
            <tr>
                <td align="right" class="must_entry_caption">You Have Selected</td>
                <td>
                	<input type="text" id="selected_id" name="selected_id" class="text_boxes" style="width:150px;" readonly/>
                    <input type="hidden" id="you_have_selected" name="you_have_selected" />
                    <input type="hidden" id="update_vlues" name="update_vlues" />
                    <input type="hidden" id="update_id" name="update_id" />
                </td> 
            </tr>
			<tr>
                <td align="right" class="must_entry_caption">Mail Type</td>
                <td>
					<?
					$mail_type = array( 1 => "Auto Mail", 5 => "Manual / Additional Mail", 2 => "Ready to Approved", 3 => "Full Approved", 4 => "Partial Approved", 6 => "Deny", 7 => "Unapproved", 8 => "Revised");
                    echo create_drop_down( "cbo_mail_type", 162, $mail_type,"", 1, "---  All Select ---", 1, "load_drop_down( 'requires/mail_recipient_group_controller',$('#mail_item').val()+'_'+this.value, 'load_drop_down_mail_template', 'mail_template' );" );
                    ?>
                </td>                
            </tr>
			<tr>
                <td align="right" >Mail Template</td>
                <td id="mail_template">
					<?
                    echo create_drop_down( "cbo_mail_template", 162, $blank,"", 1, "---  All Select ---", 1, "" );
                    ?>
                </td>                
            </tr>
            <tr>
                <td align="right" class="must_entry_caption">Status</td>
                <td>
					<?
                    echo create_drop_down( "status", 162, $row_status,"", 2, "--- Select Status ---", 0, "" );
                    ?>
                </td>                
            </tr>
            <tr><td colspan="2" align="center"><a target="_blank" href="../../auto_mail/setting/add_auto_mail.php">Set auto mail</a></td></tr>
            <tr>
                <td align="center" colspan="2" class="button_container">
                    <input type="hidden" id="update_id">
                    <? 
                    echo load_submit_buttons( $permission, "mail_recipient_group", 0,0 ,"reset_form('mailrecipientgroup','','');show_list_view('','mail_list_view','list_container','requires/mail_recipient_group_controller','');set_button_status(0, permission, 'mail_recipient_group',1,1);",1);
                    ?>  
                    <input type="hidden"  value="asdasdas"onClick="copyToClipboard('asdasdasdasdasdasd',2)"  >               
                </td>
            </tr>
        </table>
    </form>
</fieldset>
<fieldset style="width:350px;">
    <form>
        <div id="list_container">
            <table class="rpt_table" id="mail_setup" border="1" cellspacing="0" cellpadding="0" rules="all">
                <thead>
                    <th width="50">SL No</th>
                    <th width="300">Email Address</th>
                </thead>
                <tbody>
					<? 
					$result=sql_select("select id,email_address from user_mail_address where status_active=1 and is_deleted=0");
					$sl=1;
					foreach($result as $list_rows){
					$bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";
					?>    
						<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $list_rows[csf('id')];?>" onClick="js_set_value(<? echo $list_rows[csf('id')]; ?>)" style="cursor:pointer;">
							<td><? echo $sl; ?> </td>
							<td id="email_id<? echo $list_rows[csf('id')]; ?>"><? echo $list_rows[csf('email_address')]; ?> </td>
						</tr>
                    <? $sl++; } ?>
                </tbody>  
            </table>    
            <script language="javascript" type="text/javascript">
            setFilterGrid("mail_setup");
            </script>
        </div>
    </form>
</fieldset>
</div>
<div>
<table cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all" >
	<thead>
    	<th width="35">SL</th>
    	<th width="60">Company</th>
        <th width="150">Item Name</th>
        <th width="150">Buyer</th>
        <th width="150">Brand</th>
		<th width="100">Mail Type</th>
    </thead>
    <tbody id="item_list_container">
    	
    </tbody>
</table>

</div>
</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>


</html>
