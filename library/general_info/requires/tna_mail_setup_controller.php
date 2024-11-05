<?php
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$mail_type_arr=array(1=>"Starting Reminder",2=>"Completion Reminder",3=>"Yesterday Start Pending",4=>"Yesterday Completion Pending",5=>"Total Start Pending",6=>"Total Completion Pending");

//$lib_designation=return_library_array( "select id,designation from lib_mkt_team_member_info where status_active=1 and is_deleted=0", "id", "designation"  );
//$lib_user=return_library_array( "select id,user_name from user_passwd", "id", "user_name"  );

//system id popup here----------------------//


if ($action=="user_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);  
	?>
<script>


function toggle( x, origColor ) 
{
	var newColor = 'yellow';
	if ( x.style ) {
	x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
	}
}

function mail_validate(email) {
    var atpos = email.indexOf("@");
    var dotpos = email.lastIndexOf(".");
    if (atpos<1 || dotpos<atpos+2 || dotpos+2>=email.length) {
        alert("Invalid Email Address");
        return false;
    }
	else
	{
		return true;
	}

 }

var selected_id = new Array();
var selected_text = new Array();
function js_set_value( str ) 
{ 
	var str=str*1;
	var email=trim($('#user_text'+str).text());

	var user_mail_text=trim($('#user_mail_text'+str).text());
	if(mail_validate(user_mail_text)==false){return;}	


	toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );

	if( jQuery.inArray( str, selected_id ) == -1 ) 
	{
		selected_id.push( str );
		selected_text.push( email );
	}
	else 
	{
		for( var i = 0; i < selected_id.length; i++ ) 
		{
			if( selected_id[i] == str ) break;
		}
		selected_id.splice( i, 1 );
		selected_text.splice( i, 1 );
	}

	var id =''; var mail ='';
	for( var i = 0; i < selected_id.length; i++ ) 
	{
		id += selected_id[i] + ',';
		mail += selected_text[i] + ',';
	}

	id = id.substr( 0, id.length - 1 );
	mail = mail.substr( 0, mail.length - 1 );
	
	$('#selected_id').val( id );
	$('#selected_value').val( mail );

}

function fn_close()
{
	parent.emailwindow.hide();
}



</script>       
       <input type="hidden" id="selected_id" /><input type="hidden" id="selected_value" />
        
        <table class="rpt_table" border="1" cellspacing="0" cellpadding="0" rules="all" width="100%">
            <thead>
                <th width="35">SL No</th>
                <th width="200">User</th>
                <th >Email Address</th>
            </thead>
          </table>
        <div style=" max-height:360px; overflow-y:scroll;">
        <table class="rpt_table" id="mail_setup" border="1" cellspacing="0" cellpadding="0" rules="all" width="578">
            <tbody>
                <? 
                    $result=sql_select("select id,user_name,user_email from user_passwd where valid=1 order by user_name");
                    $sl=1;
                    foreach($result as $rows){
                    $bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";
                    ?>    
                        <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $rows[csf('id')];?>" onClick="js_set_value(<? echo $rows[csf('id')]; ?>)" style="cursor:pointer;">
                            <td align="center" width="35"><? echo $sl; ?> </td>
                            <td width="200" id="user_text<? echo $rows[csf('id')]; ?>"><? echo $rows[csf('user_name')]; ?> </td>
                            <td id="user_mail_text<? echo $rows[csf('id')]; ?>"><? echo $rows[csf('user_email')]; ?> </td>
                        </tr>
                <? $sl++; } ?>
            </tbody>  
        </table>
        </div>
        <table width="100%" cellspacing="0" cellpadding="0" style="border:none" align="center">
                <tr>
                    <td align="center" height="30" valign="bottom">
                        <div style="width:100%"> 
                            
                            <div style="width:100%; float:left" align="center">
                              <input type="button" name="close" onClick="fn_close();" class="formbutton" value="Close" style="width:100px" />
                            </div>
                        </div>
                    </td>
                </tr>
            </table>        
            
        <script language="javascript" type="text/javascript">
        setFilterGrid("mail_setup");
        </script>
     
	 <?
	 foreach(explode(',',$data) as $value){
		if($value){echo '<script language="javascript" type="text/javascript">
        js_set_value( '.$value.' );
        </script>';}
	 }
}


if ($action=="task_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST); 
	?>
<script>

function check_all_data(str) {
	tbl_row_count=str.split(',');
	for( var i = 0; i <= tbl_row_count.length; i++ ) {
		js_set_value( tbl_row_count[i] );
	}
}
function toggle( x, origColor ) 
{
	var newColor = 'yellow';
	if ( x.style ) {
	x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
	}
}


var selected_id = new Array();
var selected_text = new Array();
function js_set_value( str ) 
{
	var str=str*1;
	var email=trim($('#user_text'+str).text());

	toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );

	if( jQuery.inArray( str, selected_id ) == -1 ) 
	{
		selected_id.push( str );
		selected_text.push( email );
	}
	else 
	{
		for( var i = 0; i < selected_id.length; i++ ) 
		{
			if( selected_id[i] == str ) break;
		}
		selected_id.splice( i, 1 );
		selected_text.splice( i, 1 );
	}

	var id =''; var mail ='';
	for( var i = 0; i < selected_id.length; i++ ) 
	{
		id += selected_id[i] + ',';
		mail += selected_text[i] + ',';
	}

	id = id.substr( 0, id.length - 1 );
	mail = mail.substr( 0, mail.length - 1 );
	
	$('#selected_id').val( id );
	$('#selected_value').val( mail );

}

function fn_close()
{
	parent.emailwindow.hide();
}



</script>       
       <input type="hidden" id="selected_id" /><input type="hidden" id="selected_value" />
        
        <table class="rpt_table" border="1" cellspacing="0" cellpadding="0" rules="all" width="100%">
            <thead>
                <th width="35">SL No</th>
                <th width="50">Task ID</th>
                <th width="200">TNA Task Name</th>
                <th>TNA Task Short Name</th>
            </thead>
          </table>
        <div style=" max-height:360px; overflow-y:scroll;">
        <table class="rpt_table" id="mail_setup" border="1" cellspacing="0" cellpadding="0" rules="all" width="578">
            <tbody>
                <? 
                    $tnaArr=$tna_common_task_name_to_process+$tna_task_name;
					
					$result=sql_select("select task_name as id,task_short_name from lib_tna_task where status_active=1 and is_deleted=0 and task_type = $task_type order by task_short_name");
                    $sl=1;
                    $id_arr=array();
					foreach($result as $rows){
                    $id_arr[]=$rows[csf('id')];
					$bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";
                    ?>    
                        <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $rows[csf('id')];?>" onClick="js_set_value(<? echo $rows[csf('id')]; ?>)" style="cursor:pointer;">
                            <td align="center" width="35"><? echo $sl; ?> </td>
                            <td width="50"><? echo $rows[csf('id')]; ?> </td>
                            <td width="200"><? echo $tnaArr[$rows[csf('id')]]; ?> </td>
                            <td id="user_text<? echo $rows[csf('id')]; ?>"><? echo $rows[csf('task_short_name')]; ?> </td>
                        </tr>
                <? $sl++; } ?>
            </tbody>  
        </table>
        </div>
        <table width="100%" cellspacing="0" cellpadding="0" style="border:none" align="center">
                <tr>
                    <td align="center" height="30" valign="bottom">
                           <span style="float:left;"><input type="checkbox" name="check_all" id="check_all" onclick="check_all_data('<? echo implode(',',$id_arr);?>')" /> Check / Uncheck All</span> 
                            <input type="button" name="close" onClick="fn_close();" class="formbutton" value="Close" style="width:100px" />
                    </td>
                </tr>
            </table>        
            
        <script language="javascript" type="text/javascript">
        setFilterGrid("mail_setup");
        </script>
     
	 <?
	 foreach(explode(',',$data) as $value){
		echo '<script language="javascript" type="text/javascript">
        js_set_value( '.$value.' );
        </script>'; 
	 }
}

if ($action=="mail_type_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);  
	?>
<script>
function check_all_data(str) {
	tbl_row_count=str.split(',');
	for( var i = 0; i <= tbl_row_count.length; i++ ) {
		js_set_value( tbl_row_count[i] );
	}
}

function toggle( x, origColor ) 
{
	var newColor = 'yellow';
	if ( x.style ) {
	x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
	}
}


var selected_id = new Array();
var selected_text = new Array();
function js_set_value( str ) 
{
	var str=str*1;
	var email=trim($('#user_text'+str).text());

	toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );

	if( jQuery.inArray( str, selected_id ) == -1 ) 
	{
		selected_id.push( str );
		selected_text.push( email );
	}
	else 
	{
		for( var i = 0; i < selected_id.length; i++ ) 
		{
			if( selected_id[i] == str ) break;
		}
		selected_id.splice( i, 1 );
		selected_text.splice( i, 1 );
	}

	var id =''; var mail ='';
	for( var i = 0; i < selected_id.length; i++ ) 
	{
		id += selected_id[i] + ',';
		mail += selected_text[i] + ',';
	}

	id = id.substr( 0, id.length - 1 );
	mail = mail.substr( 0, mail.length - 1 );
	
	$('#selected_id').val( id );
	$('#selected_value').val( mail );

}

function fn_close()
{
	parent.emailwindow.hide();
}



</script>       
       <input type="hidden" id="selected_id" /><input type="hidden" id="selected_value" />
        
        <table class="rpt_table" border="1" cellspacing="0" cellpadding="0" rules="all" width="100%">
            <thead>
                <th width="35">SL No</th>
                <th>Mail Type</th>
            </thead>
          </table>
        <div style=" max-height:360px; overflow-y:scroll;">
        <table class="rpt_table" id="mail_setup" border="1" cellspacing="0" cellpadding="0" rules="all" width="578">
            <tbody>
                <? 
                    $sl=1;
					$id_arr=array();
					foreach($mail_type_arr as $id=>$value){
                    $id_arr[]=$id;
					$bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";
                    ?>    
                        <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $id;?>" onClick="js_set_value(<? echo $id; ?>)" style="cursor:pointer;">
                            <td align="center" width="35"><? echo $sl; ?> </td>
                            <td id="user_text<? echo $id; ?>"><? echo $value; ?> </td>
                        </tr>
                <? $sl++; } ?>
            </tbody>  
        </table>
        </div>
        <table width="100%" cellspacing="0" cellpadding="0" style="border:none" align="center">
                <tr>
                    <td align="center" height="30" valign="bottom">
                          <span style="float:left;"><input type="checkbox" name="check_all" id="check_all" onclick="check_all_data('<? echo implode(',',$id_arr);?>')" /> Check / Uncheck All</span> 
                            
                           <input type="button" name="close" onClick="fn_close();" class="formbutton" value="Close" style="width:100px" />
                    </td>
                </tr>
            </table>        
            
        <script language="javascript" type="text/javascript">
        setFilterGrid("mail_setup");
        </script>
     
	 <?
	 
	 foreach(explode(',',$data) as $value){
		echo '<script language="javascript" type="text/javascript">
        js_set_value( '.$value.' );
        </script>'; 
	 }
	 
}




if($action=="tna_mail_setup_from_data")
{
	$res=sql_select("select ID,USER_ID,TASK_TYPE,TNA_TASK_ID,MAIL_TYPE from tna_mail_setup where id=$data");

	$lib_user=return_library_array( "select id,user_name from user_passwd where id in(".$res[0][csf("user_id")].")", "id", "user_name"  );
	$task_name=return_library_array( "select task_name,task_short_name from lib_tna_task where TASK_TYPE = {$res[0]['TASK_TYPE']} AND task_name in(".$res[0][csf("tna_task_id")].")", "task_name", "task_short_name"  );
	
	foreach(explode(',',$res[0][csf("mail_type")]) as $id){
		$mail_type_text_str[$id]=$mail_type_arr[$id];
	}
	
	echo "$('#txt_user').val('".implode(',',$lib_user)."');\n";
	echo "$('#txt_user_id').val('".$res[0][csf("user_id")]."');\n";	

	echo "$('#cbo_task_type').val('".$res[0][csf("task_type")]."');\n";	

	echo "$('#txt_tna_task').val('".implode(',',$task_name)."');\n";	
	echo "$('#txt_tna_task_id').val('".$res[0][csf("tna_task_id")]."');\n";	

	echo "$('#txt_mail_type').val('".implode(',',$mail_type_text_str)."');\n";	
	echo "$('#txt_mail_type_id').val('".$res[0][csf("mail_type")]."');\n";	
	
	echo "$('#update_id').val('".$res[0][csf("id")]."');\n";	
	echo "set_button_status(1, permission, 'tna_mail_setup',1,1);\n";

	exit();
}//tna_mail_setup_from_data end; 


 
 if($action=="show_listview")
{
	$lib_user=return_library_array( "select id,user_name from user_passwd where valid=1", "id", "user_name"  );
	
	?>
	<div style="width:600px; margin:0 auto;">
	<fieldset style="width:550px;">
    	<form>
            <table class="rpt_table" id="mail_setup" border="1" cellspacing="0" cellpadding="0" rules="all">
                <thead>
                    <th width="30">SL</th>
                    <th width="220">User</th>
                    <th width="100">Task Type</th>
                    <th width="200"> Mail Type</th>
                </thead>
             </table>
            <table class="rpt_table" id="table_body" border="1" cellspacing="0" cellpadding="0" rules="all">
                <tbody>
					<? 
						$result=sql_select("select ID,TASK_TYPE,USER_ID,MAIL_TYPE from tna_mail_setup where status_active=1 and is_deleted=0");
						$sl=1;
						foreach($result as $rows){
							$mail_type_text_str=array();
							foreach(explode(',',$rows[csf("mail_type")]) as $id){
								$mail_type_text_str[$id]=$mail_type_arr[$id];
							}
							$user_name_arr=array();
							foreach(explode(',',$rows[csf("user_id")]) as $id){
								$user_name_arr[$id]=$lib_user[$id];
							}
							
							$bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";
							?>    
                            <tr bgcolor="<?= $bgcolor; ?>" id="tr_<?= $rows[csf('id')];?>" onClick="get_php_form_data(<?= $rows[csf('id')]; ?>, 'tna_mail_setup_from_data', 'requires/tna_mail_setup_controller' );" style="cursor:pointer;">
                                <td width="30" align="center"><?= $sl; ?></td>
                                <td width="220"><p><?= implode(',',$user_name_arr); ?></p></td>
								<td width="100" align="center"><?= $template_type_arr[$rows['TASK_TYPE']]; ?></td>
                                <td width="200" id="email_id<?= $rows[csf('id')]; ?>"><?= implode(',',$mail_type_text_str); ?> </td>
                            </tr>
                    <? $sl++; } ?>
                </tbody>  
            </table>    

    	</form>
	</fieldset>
	</div>

	<? 
	exit();
}
 

//--------------------------------------------------------------------------------------------
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN");}

		 
		if(str_replace("'","",$update_id)=="")
		{
			$id= return_next_id("id","tna_mail_setup",1);
			$field_array_mst="id,user_id,task_type,tna_task_id,mail_type,inserted_by,insert_date,status_active,is_deleted";
			$data_array_mst="(".$id.",".$txt_user_id.",".$cbo_task_type.",".$txt_tna_task_id.",".$txt_mail_type_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$rID=sql_insert("tna_mail_setup",$field_array_mst,$data_array_mst,1);
			
		}
		
		//echo "0**".$data_array_mst;die;
		
		if($db_type==0)
		{
		 if( $rID)
			{
			   mysql_query("COMMIT");  
			   echo "0**".str_replace("'",'',$id);
			}
			else
			{
			    mysql_query("ROLLBACK"); 
		    	echo "10**".str_replace("'",'',$id);
			}
		}
		if($db_type==2 || $db_type==1 )
		    
			{
			if( $rID)
				{
				  oci_commit($con);  
				   echo "0**".str_replace("'",'',$id);
				}
				else
				{
					oci_rollback($con); 
					echo "10**".str_replace("'",'',$id);
				}
			}
				disconnect($con);
				die;
	}
	else if ($operation==1)   // Update Here=============================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$update_id=str_replace("'",'',$update_id);
		if($update_id!="")
		{
			$field_array_up="user_id*task_type*tna_task_id*mail_type*updated_by*update_date";
			$data_array_up="".$txt_user_id."*".$cbo_task_type."*".$txt_tna_task_id."*".$txt_mail_type_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=sql_update("tna_mail_setup",$field_array_up,$data_array_up,"id",$update_id,1);
		}
	 //echo "10**".$data_array_up;die;

		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$rID);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$rID);
			}
		}
		if($db_type==2 || $db_type==1 )
			{
				if($rID)
				  {
					oci_commit($con); 
					echo "1**".str_replace("'",'',$rID);
					  
				  }
				  else
				  {
					 oci_rollback($con);
					 echo "10**".str_replace("'",'',$rID);  
				  }
			}
			disconnect($con);
			die;
	}
	else if ($operation==2)  // Delete Here
	{
			
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$update_id=str_replace("'",'',$update_id);
		if($update_id!="")
		{
			$field_array_up="updated_by*update_date*status_active*is_deleted";
			$data_array_up="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			$rID=sql_update("tna_mail_setup",$field_array_up,$data_array_up,"id",$update_id,1);
		}
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$rID);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$rID);
			}
		}
		if($db_type==2 || $db_type==1 )
			{
				if($rID)
				  {
					oci_commit($con); 
					echo "1**".str_replace("'",'',$rID);
					  
				  }
				  else
				  {
					 oci_rollback($con);
					 echo "10**".str_replace("'",'',$rID);  
				  }
			}
			disconnect($con);
			die;
		
		
	}





}

?>

