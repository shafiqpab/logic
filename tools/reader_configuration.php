<?php
session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_GET);
$permission=explode('_',$permission);
 
if ($permission[0]==1 ) $insert="New Entry permission. "; else $insert="";
if ($permission[1]==1 ) $update="Edit permission. "; else $update="";
if ($permission[2]==1 ) $delete="Delete permission. "; else $delete="";
if ($permission[3]==1 ) $approve="Approval permission. "; else $approve="";

//--------------------------------------------------------------------------------------------------------------------
include('../includes/common.php');
include('../includes/array_function.php');
include('../includes/common_functions.php');
?>

<head>
	<link href="../css/style_common.css" rel="stylesheet" type="text/css" media="screen" />
	<link href="../css/popup_window.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="../js/popup_window.js"></script>
	<script type="text/javascript" src="../js/modal.js"></script>
	
	<script src="../includes/ajax_submit.js" type="text/javascript"></script>
	<script src="../includes/functions.js" type="text/javascript"></script>
	
	<script type="text/javascript" src="../resources/jquery_ui/jquery-1.4.4.min.js"></script>
	<link href="../resources/jquery_ui/jquery-ui-1.8.10.custom.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="../resources/jquery_ui/jquery-ui-1.8.10.custom.min.js"></script>
	
<script>
	
	function openmypage(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link, title, 'width=400px,height=420px,center=1,resize=0,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("txt_selected") //Access form field with id="emailfield"
			var theemailid=this.contentDoc.getElementById("txt_selected_id")
			
			document.getElementById('txt_user_name').value=theemail.value;
			document.getElementById('txt_user_id').value=theemailid.value;
		}
	}
	
</script>
    <script>
		var save_perm = <? echo $permission[0]; ?>;
		var edit_perm = <? echo $permission[1]; ?>;
		var delete_perm = <? echo $permission[2]; ?>;
		var approve_perm = <? echo $permission[3]; ?>;
	</script>
</head>
<body onLoad="document.onkeydown = checkKeycode;">
	
<div align="center" style="width:900px">
<p id="permission_caption">
						<? 
                            echo "Your Permissions--> \n".$insert.$update;
                        ?>
         </p>
<form name="reader_conf" id="reader_conf" autocomplete="off" action="javascript:fnc_reader_conf(save_perm,edit_perm,delete_perm,approve_perm)" >
	
	<fieldset style="width:700px">
		<legend>Reader Configurations </legend>
		<table cellpadding="0" cellspacing="2" width="100%">
			
			<tr>
				<td align="center" colspan="4" height="30" valign="top">
					<div id="messagebox" align="center"></div>				
					
					</td>
			</tr>
			<tr>
				<td colspan="4" align="center">
					<fieldset style="width:90%">
					<legend>General Info</legend>
					<table>
						<tr>
							<td>
								Company</td>
							<td>
								<select name="cbo_company_mst" id="cbo_company_mst" style="width:145px" class="combo_boxes">
									<option value="0">--- Select ---</option>
									<?
									$mod_sql= mysql_db_query($DB, "select * from lib_company where is_deleted=0 and status_active=1 order by id"); //where is_deleted=0 and status=0
									while ($r_mod=mysql_fetch_array($mod_sql))
									{
									?>
									<option value=<? echo $r_mod["id"];
									if ($company_combo==$r_mod["id"]){?> selected <?php }?>><? echo "$r_mod[company_name]" ?> </option>
									<?
									}
									?>
								</select>	<input type="hidden" name="save_up" id="save_up">						</td>
							<td width="130" valign="top">User Name</td>
							<td width="180" valign="top">
								<input type="text" placeholder="Double Click Here for User" name="txt_user_name" id="txt_user_name" readonly="true" onDblClick="openmypage('search_user.php','User Search'); return false"  class="text_boxes" style="width:175px">							
								<input type="hidden" name="txt_user_id" id="txt_user_id" readonly="true" class="text_boxes" style="width:175px">	
								</td>
						</tr>
						<tr>
							
							<td width="130" v150align="top">
								Total Reader							</td>
						  <td width="220" valign="top">
								<input name="txt_total_reader" id="txt_total_reader" class="text_boxes" style="width:145px;">							</td>
							<td width="" valign="top">
								Reader Model/Group							</td>
							<td width="" valign="top">
								<input name="txt_reader_model" id="txt_reader_model" class="text_boxes" style="width:175px">							</td>
						</tr>
					</table>
				</fieldset>				</td>
			</tr>
			<tr>
				<td colspan="4" align="center">
					<fieldset style="width:90%">
					<legend>Database Info</legend>
						<table>
							<tr>
								<td>
									Database Type </td>
								<td>
									<select name="cbo_db_type" id="cbo_db_type" style="width:145px" class="combo_boxes">
										<option value="0">--- Select ---</option>
										<option value="1">ODBC Driver</option>
										<option value="2">OLEDB Driver</option>
                                        <option value="3">MYSQL DB</option>
                                        <option value="4">Text File Based</option>
									</select>								</td>
								<td width="130" valign="top">Server Name</td>
								<td width="180" valign="top">
									<input name="txt_server_name" id="txt_server_name"  class="text_boxes" style="width:175px">								</td>
							</tr>
							<tr>
								<td width="130" valign="top">Database Name</td>
								<td width="180" valign="top">
									<input name="txt_database_name" id="txt_database_name"   class="text_boxes" style="width:145px">								</td>
								<td width="130" valign="top">Table Name</td>
								<td width="180" valign="top">
									<input name="txt_table_name" id="txt_table_name"   class="text_boxes" style="width:175px">								</td>
							</tr>
							<tr>
								<td width="130" v150align="top">
									User ID								</td>
							  <td width="220" valign="top">
									<input name="txt_db_user" id="txt_db_user" class="text_boxes"   style="width:145px;">								</td>
								<td width="" valign="top">
									Password								</td>
								<td width="" valign="top">
									<input name="txt_db_password" id="txt_db_password" class="text_boxes" style="width:175px">								</td>
							</tr>
						</table>
				</fieldset>				</td>
			</tr>
			<tr>
				<td colspan="4" align="center">
					<fieldset style="width:90%">
					<legend>Table Info</legend>
						<table>
							<tr>
								<td>
									RF Code Field</td>
								<td>
									<input name="txt_rf_code_fld" id="txt_rf_code_fld"  class="text_boxes" style="width:145px">								</td>
								<td width="130" valign="top">Date Field</td>
								<td width="180" valign="top">
									<input name="txt_date_fld" id="txt_date_fld"  class="text_boxes" style="width:175px">								</td>
							</tr>
							<tr>
								
								<td width="130" v150align="top">
									Time Field								</td>
							  <td width="220" valign="top">
									<input name="txt_time_fld" id="txt_time_fld" class="text_boxes" style="width:145px;">								</td>
								<td width="" valign="top">
									Reader No Field								</td>
								<td width="" valign="top">
									<input name="txt_reader_no_fld" id="txt_reader_no_fld" class="text_boxes" style="width:175px">								</td>
							</tr>
							<tr>
								
								<td width="130" v150align="top">
									Network No Field								</td>
							  <td width="220" valign="top">
									<input name="txt_net_no_fld" id="txt_net_no_fld" class="text_boxes" style="width:145px;">								</td>
								<td width="" valign="top">
									Status Field								</td>
								<td width="" valign="top">
									<input name="txt_sts_fld" id="txt_sts_fld" class="text_boxes" style="width:175px">								</td>
							</tr>
                            <tr>
								<td width="" valign="top">ID Field Name
									 					</td>
								<td width="" valign="top"><input name="txt_id_fld" id="txt_id_fld" class="text_boxes" style="width:145px">
									 							</td>
								<td width="130" v150align="top">
									Date Format								</td>
							  <td width="220" valign="top">
									<select name="cbo_date_format" id="cbo_date_format" class="combo_boxes" style="width:175px">
                                    	<option value="1">yyyymmdd</option>
                                        <option value="2">dd/mm/yyyy</option>
                                        <option value="3">yyyy/mm/dd</option>
                                        <option value="4">dd-mm-yyyy</option>
                                        <option value="5">yyyy-mm-dd</option>
                                    </select>								
                                 </td>
								
							</tr>
						</table>
				</fieldset>				</td>
			</tr>
			<tr>
				<td colspan="4" height="40" valign="middle" align="center"><input type="submit" name="Save" class="formbutton" id="Save" value="Save" style="width:100px">				  &nbsp;&nbsp;
				  <input type="reset" name="e" id="e" value="Reset" class="formbutton" style="width:100px">			  </td>
			</tr>	
		</table>
		
	</fieldset>
</form>
</div>
</body>

	





