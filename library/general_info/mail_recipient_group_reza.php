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
	
	var dataString = "company_id*mail_item*you_have_selected*status*update_id";
 	var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../");
// alert(data);	
	freeze_window(operation);
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
		//alert (http.responseText);return;
		if(response[0]==0||response[0]==1||response[0]==2)
		{
			//show_msg(trim(response[0]));
			//var ms_id=document.getElementById("update_id").value=response[1];
			//document.getElementById("txt_emp_code").value=response[1];
			//show_list_view(response[1],'create_user_emsil_list_view','list_container','requires/mail_recipient_setup_controller','setFilterGrid("mail_setup",-1)');
			
			reset_form('mailrecipientgroup','','','','','');
			
			set_button_status(0, permission, 'mail_recipient_group',1,1);
			release_freezing();
		}
		if(response[0]==11)
		{
			alert("Id Card Number Should not be Duplicate");
			//set_button_status(0, permission, 'fnc_emp_info',1,1);
			release_freezing();
		}
	
	
	
	}
	
	
}

// flowing script for multy select data------------------------------------------------------------------------------start;

 var selected_id = new Array();
 var selected_email = new Array();
 
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		
		
		function js_set_value( str ) {
			 
			var email=trim($('#email_id'+str).html());
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
			if( jQuery.inArray( str, selected_id ) == -1 ) {
				selected_id.push( str );
				selected_email.push( email );
				//selected_job.push( $('#txt_individual_job' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str ) break;
				}
				selected_id.splice( i, 1 );
				selected_email.splice( i, 1 );
				//selected_job.splice( i,1);
			}
			var id =''; mail ='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				mail += selected_email[i] + ',';
				//job += selected_job[i] + '*';
			}
			id = id.substr( 0, id.length - 1 );
			mail = mail.substr( 0, mail.length - 1 );
			//job = job.substr( 0, job.length - 1 );
			
			$('#you_have_selected').val( id );
			$('#selected_id').val( mail );
		
		}
		
// avobe script for multy select data------------------------------------------------------------------------------end;
 
					  
function get_update_data ( itm_id )
{ 
	var data=itm_id+"__"+document.getElementById('company_id').value;
	get_php_form_data( data, 'mail_recipient_group_from_data', 'requires/mail_recipient_group_controller' ) ;
	var update_v=document.getElementById('update_vlues').value.split(",");
	for(var k=0; k<update_v.length; k++)
	{
		js_set_value( parseInt(update_v[k]) )
	}
}
</script>

</head>

<body onLoad="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission);  ?>
<div style="width:500px; margin:0 auto;">
	
        <fieldset style="width:450px;">
       	 <legend>Mail Recipient Group</legend>
            <form name="mailrecipientgroup" id="mailrecipientgroup" autocomplete = "off">	
              <table cellpadding="0" cellspacing="2" align="center">
                <tr>
                  	<td width="121" class="must_entry_caption">Company Name</td>
                  	<td>
                      <?
                          echo create_drop_down( "company_id", 300, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name", 1, "--- Select Company Name ---", 0, "" );
                      ?>
                  	</td>                
                </tr>
                <tr>
                  	<td width="121" class="must_entry_caption">Mail Item</td>
                  	<td>
                    <?
                          echo create_drop_down( "mail_item", 300, $form_list_for_mail,"", 1, "--- Select Mail Item ---", 0, "get_update_data( this.value)" );
                      ?>
                  	</td>                
                </tr>
                <tr>
                  	<td width="121" class="must_entry_caption">You Have Selected</td>
                  	<td><input type="text" id="selected_id" name="selected_id" class="text_boxes" style="width:288px;" readonly/>
                    <input type="hidden" id="you_have_selected" name="you_have_selected" />
                    <input type="hidden" id="update_vlues" name="update_vlues" />
                    <input type="hidden" id="update_id" name="update_id" />
                    </td> 
                </tr>
                
                <tr>
                  	<td width="121" class="must_entry_caption">Status</td>
                  	<td>
                      <?
                          echo create_drop_down( "status", 150, $row_status,"", 2, "--- Select Status ---", 0, "" );
                      ?>
                  	</td>                
                </tr>

                <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                    <tr>
                          <td align="center" colspan="2" class="button_container">
                          <input type="hidden" id="update_id">
                           <? 
                           echo load_submit_buttons( $permission, "mail_recipient_group", 0,0 ,"reset_form('mailrecipientgroup','','')",1);
                          ?>                   
                           </td>
                    </tr>
              </table>
            </form>
        </fieldset>
        
        
        
  <fieldset style="width:450px;">
  <form>
<div id="list_container">



 
<table class="rpt_table" id="mail_setup" border="1" cellspacing="0" cellpadding="0" rules="all">
    <thead>
        <th width="50">SL No</th>
        <th width="400">Email Address</th>
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
</body>
    
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>


</html>
