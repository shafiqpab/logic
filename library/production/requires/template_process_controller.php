<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="template_process_list_view")
{
	$arr=array (2=>$row_status); 
	echo create_list_view ( "list_view","Template Name,Process Name,Status", "160,200,140","500","220",0, "select template_name, process_name,id,status_active from lib_template_process_mst where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,status_active", $arr , "template_name,process_name,status_active", "requires/template_process_controller", 'setFilterGrid("list_view",-1);' );
	exit();
}

if ($action=="load_php_data_to_form")
{	
	$nameArray=sql_select( "select id, template_name,process_name,process_id,sequence_id,status_active from lib_template_process_mst where id='$data'" );
	foreach ($nameArray as $inf) 
	{
		echo "document.getElementById('txt_template_name').value = '".($inf[csf("template_name")])."';\n";
		echo "document.getElementById('txt_process_name').value = '".($inf[csf("process_name")])."';\n";
		echo "document.getElementById('txt_process_id').value = '".($inf[csf("process_id")])."';\n";
		echo "document.getElementById('txt_process_seq').value = '".($inf[csf("sequence_id")])."';\n";

		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'template_process_info',1);\n";
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		if (is_duplicate_field( "template_name", "lib_template_process_mst", "template_name=$txt_template_name and is_deleted=0" ) == 1)
		{
			echo "11**0"; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id=return_next_id( "id", "lib_template_process_mst", 1 ) ;
			$field_array="id,template_name,process_name,process_id,sequence_id,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id.",".$txt_template_name.",".$txt_process_name.",".$txt_process_id.",".$txt_process_seq.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";
			//Insert Data in lib_template_process_dtls Table----------------------------------------

			$dtls_id=return_next_id( "id", "lib_template_process_dtls", 1 );
			$data_array_process="";
			$process_name=explode(',',str_replace("'","",$txt_process_seq));
			for($i=0; $i<count($process_name); $i++)
			{
				$process_id=explode('_',$process_name[$i]);
				if($i==0) $add_comma=""; else $add_comma=",";
				$data_array_process.="$add_comma(".$dtls_id.",".$id.",".$process_id[0].",".$process_id[1].")";
				$dtls_id++;
			}
			$field_array_process="id,mst_id, process_name_id,sequence";
			$rID=sql_insert("lib_template_process_mst",$field_array,$data_array,0);
			$rID_1=sql_insert("lib_template_process_dtls",$field_array_process,$data_array_process,1);

			//echo "10**insert into lib_template_process_dtls($field_array_buyer)values".$data_array_process;die;
			// echo $rID_1;
			//----------------------------------------------------------------------------------

			if($db_type==0)
			{
				if($rID && $rID_1)
				{
					mysql_query("COMMIT");
					echo "0**".$rID;
				}
				else{
					mysql_query("ROLLBACK");
					echo "10**".$rID;
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID && $rID_1)
				{
					oci_commit($con);
					echo "0**".$rID;
				}
				else{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			disconnect($con);
			die;
		}
	}
	else if ($operation==1)   // Update Here
	{
		if (is_duplicate_field( "template_name", "lib_template_process_mst", "template_name=$txt_template_name and is_deleted=0 and id!=$update_id" ) == 1)
		{
			echo "11**0"; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$field_array="template_name*process_name*process_id*sequence_id*updated_by*update_date*status_active";
			$data_array="".$txt_template_name."*".$txt_process_name."*".$txt_process_id."*".$txt_process_seq."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."";
			//Update Data in lib_template_process_dtls Table----------------------------------------

			$dtls_id=return_next_id( "id", "lib_template_process_dtls", 1 );
			$data_array_process="";
			$process_name=explode(',',str_replace("'","",$txt_process_seq));
			for($i=0; $i<count($process_name); $i++)
			{
				$process_id=explode('_',$process_name[$i]);
				if($i==0) $add_comma=""; else $add_comma=",";
				$data_array_process.="$add_comma(".$dtls_id.",".$id.",".$process_id[0].",".$process_id[1].")";
				$dtls_id++;
			}

			$field_array_process="id,mst_id, process_name_id,sequence";
			$rID=sql_update("lib_template_process_mst",$field_array,$data_array,"id","".$update_id."",0);
			$rID1=execute_query( "delete from lib_template_process_dtls where mst_id = $update_id",0);
			$rID_1=sql_insert("lib_template_process_dtls",$field_array_process,$data_array_process,1);

		   
			if($db_type==0)
			{
				if($rID && $rID1 && $rID_1)
				{
					mysql_query("COMMIT");
					echo "1**".$rID;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$rID;
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID && $rID1 && $rID_1)
				{
					oci_commit($con);
					echo "1**".$rID;
				}
				else{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			disconnect($con);
			die;
		}
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";

		$rID=sql_delete("lib_template_process_mst",$field_array,$data_array,"id","".$update_id."",1);
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");
				echo "2**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);
				echo "2**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
	}
}


if($action=="process_name_popup")
{
  	echo load_html_head_contents("Buyer Name Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
?>
	<script>

		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});

		var selected_id = new Array(); var selected_name = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;

				
			tbl_row_count = tbl_row_count-1;
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

		function set_all() {
			var old_seq = document.getElementById('txt_process_seq').value;
			var old = document.getElementById('txt_process_row_id').value;
			if (old != "") {
				old = old.split(",");
				if(old_seq!=""){
					oldArr = old_seq.split(",");
				}

				for (var k = 0; k < old.length; k++) {
					if(old_seq!=""){
						idSeq = oldArr[k].split("_");
						$('#txt_sequence'+idSeq[0]).val(idSeq[1]);
						//$('#txt_sequence'+old[k]).val(oldArr[k]);
					}

					js_set_value(old[k]);
				}
			}
		}

		function js_set_value(str) {
            /*var currentRowColor=document.getElementById( 'search' + str ).style.backgroundColor;
             if(currentRowColor=='yellow')
             {
             var mandatory=$('#txt_mandatory' + str).val();
             var process_name=$('#txt_individual' + str).val();
             if(mandatory==1)
             {
             alert(process_name+" Subprocess is Mandatory. So You can't De-select");
             return;
             }
         }*/

         toggle(document.getElementById('search' + str), '#FFFFCC');

         if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
         	selected_id.push($('#txt_individual_id' + str).val());
         	selected_name.push($('#txt_individual' + str).val());
         }
         else {
         	for (var i = 0; i < selected_id.length; i++) {
         		if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
         	}
         	selected_id.splice(i, 1);
         	selected_name.splice(i, 1);
         }

         var id = '';
         var name = '';
         for (var i = 0; i < selected_id.length; i++) {
         	id += selected_id[i] + ',';
         	name += selected_name[i] + ',';
         }

         id = id.substr(0, id.length - 1);
         name = name.substr(0, name.length - 1);

         $('#hidden_process_id').val(id);
         $('#hidden_process_name').val(name);
     }

	function window_close(){

		var old = document.getElementById('hidden_process_id').value;
		if (old != "") {
			old = old.split(",");
			var seq='';
			for (var k = 0; k < old.length; k++) {
				if(seq==''){seq=old[k]+'_'+$('#txt_sequence'+old[k]).val();}
				else{seq+=','+old[k]+'_'+$('#txt_sequence'+old[k]).val();}
			}
		}
		$('#hidden_process_seq').val(seq);
		//var oldArr = old_seq.split(",");


		parent.emailwindow.hide();
		}
    </script>

</head>
<body>
	<div align="center">
		<fieldset style="width:370px;margin-left:10px">
			<input type="hidden" name="hidden_process_id" id="hidden_process_id" class="text_boxes" value="">
			<input type="hidden" name="hidden_process_seq" id="hidden_process_seq" class="text_boxes" value="">
			<input type="hidden" name="hidden_process_name" id="hidden_process_name" class="text_boxes"
			value="">
			<form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table">
					<thead>
						<th width="50">SL</th>
						<th>Process Name</th>
						<th width="82">Sequence</th>
					</thead>
				</table>
				<div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view"
				align="center">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table"
				id="tbl_list_search">
				<?
				$i = 1;
				$process_row_id = '';
						$not_process_id_print_array = array(1, 2, 3, 4, 101, 120, 121, 122, 123, 124); //$mandatory_subprocess_array=array(33,63,65,94);
						$hidden_process_id = explode(",", $txt_process_id);
						foreach ($conversion_cost_head_array as $id => $name) {
							if (!in_array($id, $not_process_id_print_array)) {
								if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

								if (in_array($id, $hidden_process_id)) {
									if ($process_row_id == "") $process_row_id = $i; else $process_row_id .= "," . $i;
								}
								/*$mandatory=0;
							if(in_array($id,$mandatory_subprocess_array))
							{
								$mandatory=1;
							}*/
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
								id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
								<td width="50" align="center"><?php echo "$i"; ?>
								<input type="hidden" name="txt_individual_id"
								id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>
								<input type="hidden" name="txt_individual"
								id="txt_individual<?php echo $i ?>"
								value="<? echo $name; ?>"/>
								<input type="hidden" name="txt_mandatory"
								id="txt_mandatory<?php echo $i ?>"
								value="<? echo $mandatory; ?>"/>
							</td>
							<td><p><? echo $name; ?></p></td>
							<td width="65" align="center"><input type="text" id="txt_sequence<? echo $id ?>" name="txt_sequence<? echo $id ?>" value="" class="text_boxes_numeric" style=" width:50px;"></td>
						</tr>
						<?
						$i++;
					}
				}
				?>
				<input type="hidden" name="txt_process_row_id" id="txt_process_row_id" value="<?php echo $process_row_id; ?>"/>

				<input type="hidden" name="txt_process_seq" id="txt_process_seq" value="<?php echo $process_seq; ?>"/>

			</table>
		</div>
		<table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
                <tr>
                    <td align="center" height="30" valign="bottom">
                        <div style="width:100%">
                            <div style="width:50%; float:left" align="left">
                                <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();" /> Check / Uncheck All
                            </div>
							<div style="width:50%; float:left" align="left">
								<input type="button" name="close" onClick="window_close()"
								class="formbutton" value="Close" style="width:100px"/>
							</div>
                        </div>
                    </td>
                </tr>
            </table>
	</form>
</fieldset>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>set_all();</script>
</html>
<?
exit();
}

?>