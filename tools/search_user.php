<?php
include('../includes/common.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Cost Sheets</title>
	
	<script src="../resources/jquery-1.6.2.js" type="text/javascript"></script>
	<link href="../css/style_common.css" rel="stylesheet" type="text/css" />
	<script src="includes/functions.js" type="text/javascript"></script>
	
	<script type="text/javascript">
		var selected_id = new Array; var selected_name = new Array();
		
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
		}
	</script>
</head>

<?php
include('../includes/array_function.php');

?>
<body>
	<div align="center">
		<form name="search_order_frm"  id="search_order_frm">
			<fieldset style="width:350px">
				<table width="350" cellspacing="2" cellpadding="0" border="0">
					<tr>
						<td align="center" >
							You Have Selected:
						</td>
					</tr>
				
					<tr>
						<td align="center">
							<textarea readonly="readonly" style="width:200px" class="text_area" name="txt_selected" id="txt_selected" ></textarea>
							<input type="hidden" readonly="readonly" style="width:200px" class="text_boxes" name="txt_selected_id" id="txt_selected_id" />
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<div style="width:350px;   overflow-y:scroll; min-height:260px; max-height:260px;" id="search_div" align="left">
								<table cellspacing="1" width="100%" id="tbl_list_search">
									<tr>
										<td>SL</td>
										<td>User Name</td>
										<td>User Type</td>
										<td><input type="hidden" name="id_field" id="id_field" /></td>
									</tr>
									<?php
									$i = 1;
									$mod_sql= mysql_db_query($DB, "select * from user_passwd where valid=1 order by id");
									while ($location=mysql_fetch_array($mod_sql))
									{
									
										if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
										else $bgcolor="#FFFFFF";
									?>
									<tr style="text-decoration:none" align="" id="search<?php echo $i; ?>" bgcolor="<?php echo $bgcolor; ?>" onclick="js_set_value(<?php echo $i; ?>)"> 
										<td width="20"><?php echo "$i"; ?></td>
										<td width="155"><?php echo "$location[user_name]"; ?></td>
										<td width="105"><?php echo $user_type[$location[user_level]]; ?>
										<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<?php echo $location['user_name']; ?>" />
											<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<?php echo $location['id']; ?>" />
										</td>
										
										<td></td>	
									</tr>
									<?php
										$i++;
									}
									?>
								</table>
							</div>
						</td>
					</tr>
					<tr>
						<td align="center" height="30" valign="bottom">
						<div style="width:100%"> 
							<div style="width:50%; float:left" align="left">
								<input type="checkbox" name="check_all" id="check_all" onclick="check_all_data()" /> Check / Uncheck All
							</div>
							<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onclick="parent.emailwindow.hide();" class="formbutton" value="Close" />
							</div>
						</div>
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>
</body>