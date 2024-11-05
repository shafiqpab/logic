<?
/*-------------------------------------------- Comments

Purpose			: 	This form will be used for mail recipient setup.

Functionality	:	First create Store Location and save.
					select a team from list view for update.

JS Functions	:

Created by		:	Saidul Reza 
Creation date 	: 	04-12-2013
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
echo load_html_head_contents("Mail Recipent", "../../", 1, 1,$unicode,'','');


$lib_designation=return_library_array( "select id,designation from lib_mkt_team_member_info where status_active=1 and is_deleted=0", "id", "designation"  );
$lib_user=return_library_array( "select id,user_name from user_passwd", "id", "user_passwd"  );


?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission='<? echo $permission; ?>';


function validateForm(fn,dn)
{
var val=document.forms[fn][dn].value;
var a=val.indexOf("@");
var d=val.lastIndexOf(".");
if (a<1 || d<a+2 || d+2>=val.length)
  {
  bgcolor='-moz-linear-gradient(bottom, rgb(254,151,174) 0%, rgb(255,255,255) 10%, rgb(254,151,174) 96%)';
  document.forms[fn][dn].style.borderColor="#f00";
  document.forms[fn][dn].style.backgroundImage=bgcolor;
  document.forms[fn][dn].focus();
  return 0;
  }
}


function mail_recipient(operation)
{
	

	if( form_validation('user_type*recipient_name*email_address','User Type *Recipient Name*Email Address')==false )
	{
		return;
	}
	else if(validateForm('mailrecipientsetup_1','email_address')==0){return;}
	
	var dataString = "user_type*recipient_name*email_address*update_id";
 	var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
	freeze_window(operation);
	http.open("POST","requires/mail_recipient_setup_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_emp_info_reponse;
}

function fnc_emp_info_reponse()
{
	if(http.readyState == 4) 
	{
		var response=trim(http.responseText).split('**');
		//alert (response);return;
		
			show_msg(trim(response[0]));
			//var ms_id=document.getElementById("update_id").value=response[1];
			//document.getElementById("txt_emp_code").value=response[1];
			show_list_view('','create_user_emsil_list_view','list_container','requires/mail_recipient_setup_controller','setFilterGrid("list_view",-1)');
			
			reset_form('mailrecipientsetup_1','','','','','');
			
			set_button_status(0, permission, 'mail_recipient',1,1);
			release_freezing();
		
		if(response[0]==11)
		{
			alert("Id Card Number Should not be Duplicate");
			//set_button_status(0, permission, 'fnc_emp_info',1,1);
			release_freezing();
		}
	
	
	
	}
	
	
}


</script>

</head>

<body onLoad="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission);  ?>
<div align="center" style="width:100%;">
	
        <fieldset style="width:600px;">
       	 <legend>Mail Recipient Setup</legend>
            <form name="mailrecipientsetup_1" id="mailrecipientsetup_1" autocomplete = "off">	
              <table cellpadding="0" cellspacing="2" width="70%" align="center">
                <tr>
                  	<td width="121" class="must_entry_caption">User Type</td>
                  	<td>
                      <?
					  $user_data=array('1'=>'Management','2'=>'Marketing','3'=>'General');
                          echo create_drop_down( "user_type", 224, $user_data,"", 1, "--- Select Designation ---", 0, "load_drop_down( 'requires/mail_recipient_setup_controller', this.value, 'load_drop_down_location_rn', 'location_td_rn');" );
                      ?>
                  	</td>                
                </tr>
                <tr>
                  	<td width="121" class="must_entry_caption">Recipient Name</td>
                  	<td id="location_td_rn"><input type="text" id="recipient_name" name="recipient_name" class="text_boxes" style="width:212px;"/></td>                
                </tr>
                <tr>
                  	<td width="121" class="must_entry_caption">Email Address </td>
                  	<td><input type="text" id="email_address" name="email_address" class="text_boxes" style="width:212px;"/></td> 
                </tr>
                <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                    <tr>
                          <td align="center" colspan="2" class="button_container">
                          <input type="hidden" id="update_id">
                           <? 
                           echo load_submit_buttons( $permission, "mail_recipient", 0,0 ,"reset_form('mailrecipientsetup_1','','')",1);
                          ?>                   
                           </td>
                    </tr>
              </table>
            </form>
        </fieldset>
        
        
        
  <fieldset style="width:600px;">
  <form>
    <div style="width:600px;" id="list_container">
  
<table class="rpt_table" border="1" cellspacing="0" cellpadding="0" rules="all">
    <thead>
        <th width="50">SL No</th>
        <th width="100">User Type</th>
        <th width="150">Recipient Name</th>
        <th width="290">Email Address</th>
    </thead>
    <tbody>
<? 
$lib_user=return_library_array( "select id,user_name from user_passwd", "id", "user_name"  );
$result=sql_select("select id,user_type,user_id,email_address from user_mail_address where status_active=1 and is_deleted=0");
$sl=1;
foreach($result as $list_rows){
 ?>    
    <tr onClick="get_php_form_data(<? echo $list_rows[csf('id')]; ?>,'mail_recipient_from_data','requires/mail_recipient_setup_controller')" style="cursor:pointer;">
        <td><? echo $sl; ?> </td>
        <td><? echo $mail_user_type[$list_rows[csf('user_type')]]; ?> </td>
        <td><? 
		
		if($list_rows[csf('user_type')]==1) echo $list_rows[csf('user_id')];
		else if($list_rows[csf('user_type')]==2)
			echo $lib_designation[$list_rows[csf('user_id')]];
		else if($list_rows[csf('user_type')]==3)
			echo $lib_user[$list_rows[csf('user_id')]];
		 ?></td>
        <td><? echo $list_rows[csf('email_address')]; ?> </td>
    </tr>
<? $sl++; } ?>  

</tbody>  
</table>    
    
    
    </div>
  </form>
  </fieldset>
        
        
        
        
        
        
        
        </div>
	</body>
    
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
