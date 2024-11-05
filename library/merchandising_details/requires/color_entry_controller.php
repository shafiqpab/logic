<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');

if($action == "is_used_color"){
	$po_color=sql_select("select color_number_id from wo_po_color_size_breakdown where color_number_id='$data' and status_active=1 and is_deleted=0");
	$trim_con=sql_select("select color_number_id from wo_pre_cost_trim_co_cons_dtls where color_number_id='$data'");
	$trim_booking=sql_select("select color_number_id,item_color from wo_trim_book_con_dtls where color_number_id='$data' or item_color=$data and is_deleted=0");
	$wo_booking=sql_select("select fabric_color_id,gmts_color_id from wo_booking_dtls where fabric_color_id='$data' or gmts_color_id=$data");
	$com_pi=sql_select("select color_id,item_color from com_pi_item_details where color_id='$data' or item_color=$data and status_active=1 and is_deleted=0");
	$product=sql_select("select color,item_color from product_details_master where color='$data' or item_color=$data and status_active=1 and is_deleted=0");
	$batch=sql_select("select color_id, batch_no from pro_batch_create_mst where color_id='$data' and status_active=1 and is_deleted=0");

	if(count($po_color)>0 || count($trim_con)>0 || count($trim_booking)>0 || count($wo_booking)>0 || count($com_pi)>0 || count($product)>0 || count($batch)>0){
		echo 1;
		exit();
	}
	else{
		echo 0;
		exit();
	}
}

if ($action=="color_list_view")
{
	$arr=array (1=>$row_status);
	echo  create_list_view ( "list_view", "Color Name,Status", "250,200","500","220",0, "select  color_name,status_active,id from  lib_color where is_deleted=0 order by color_name",   "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,status_active", $arr , "color_name,status_active", "requires/color_entry_controller", 'setFilterGrid("list_view",-1);' ) ;
	exit();
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select  color_name,tag_buyer,status_active,id from  lib_color where is_deleted=0 and id='$data'" );
	foreach ($nameArray as $inf)
	{
		$po_color=sql_select("select color_number_id from wo_po_color_size_breakdown where color_number_id='$data' and status_active=1 and is_deleted=0");
		$trim_con=sql_select("select color_number_id from wo_pre_cost_trim_co_cons_dtls where color_number_id='$data'");
		$trim_booking=sql_select("select color_number_id,item_color from wo_trim_book_con_dtls where color_number_id='$data' or item_color=$data and is_deleted=0");
		$wo_booking=sql_select("select fabric_color_id,gmts_color_id from wo_booking_dtls where fabric_color_id='$data' or gmts_color_id=$data");
		$com_pi=sql_select("select color_id,item_color from com_pi_item_details where color_id='$data' or item_color=$data and status_active=1 and is_deleted=0");
		$product=sql_select("select color,item_color from product_details_master where color='$data' or item_color=$data and status_active=1 and is_deleted=0");
		$batch=sql_select("select color_id, batch_no from pro_batch_create_mst where color_id='$data' and status_active=1 and is_deleted=0");
	
		if(count($po_color)>0 || count($trim_con)>0 || count($trim_booking)>0 || count($wo_booking)>0 || count($com_pi)>0 || count($product)>0 || count($batch)>0){
			echo "$('#txt_color_name').attr('disabled','disabled');\n";
			echo "$('#cbo_status').attr('disabled','disabled');\n";
			echo "$('#txt_color_name').attr('isused','1');\n";
			$usedmsg="This Color Used in Another Table. Only Buyer Add Allow.";
		}
		else
		{
			$usedmsg="";
			echo "$('#txt_color_name').removeAttr('disabled','disabled');\n";
			echo "$('#cbo_status').removeAttr('disabled','disabled');\n";
			echo "$('#txt_color_name').attr('isused','0');\n";
		}
		echo "document.getElementById('usedmsg').innerHTML = '".$usedmsg."';\n";
	
		echo "document.getElementById('txt_color_name').value = '".($inf[csf("color_name")])."';\n";
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n";
		$buyer_name='';
		$tag_buyer_arr=explode(",",$inf[csf("tag_buyer")]);
		foreach($tag_buyer_arr as $val)
		{
			if($buyer_name=="") $buyer_name=$buyer_arr[$val]; else $buyer_name.=",".$buyer_arr[$val];
		}

		echo "document.getElementById('txt_tag_buyer').value  = '".$buyer_name."';\n";
		echo "document.getElementById('txt_tag_buyer_id').value  = '".$inf[csf("tag_buyer")]."';\n";
		//echo "set_multiselect('cbo_tag_buyer','0','1','".($inf[csf("tag_buyer")])."','0');\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_color_info',1);\n";
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$txt_color_name=str_replace('"','',trim($txt_color_name));

	if ($operation==0)  // Insert Here
	{
		if(is_duplicate_field( "color_name", " lib_color", "LOWER(color_name)=LOWER($txt_color_name) and is_deleted=0" ) == 1)
		{
			echo "11**0"; disconnect($con); die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id=return_next_id( "id", "lib_color", 1 ) ;
			$field_array="id,color_name,tag_buyer,inserted_by,insert_date,status_active,is_deleted";
			$str_rep=array("_","/", "&", "*", "(", ")", "=","'",",","\r", "\n",'"','#');
			
			$txt_color_name=str_replace($str_rep,' ',$txt_color_name);
			
			$data_array="(".$id.",'".trim(strtoupper($txt_color_name))."',".$txt_tag_buyer_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";
			//Insert Data in lib_color_tag_buyer Table----------------------------------------

			$id_lib_color_tag_buyer=return_next_id( "id", "lib_color_tag_buyer", 1 );
			$data_array_buyer="";
			$tag_buyer=explode(',',str_replace("'","",$txt_tag_buyer_id));
			for($i=0; $i<count($tag_buyer); $i++)
			{
				if($i==0) $add_comma=""; else $add_comma=",";
				$data_array_buyer.="$add_comma(".$id_lib_color_tag_buyer.",".$id.",".$tag_buyer[$i].")";
				$id_lib_color_tag_buyer++;
			}
			$field_array_buyer="id, color_id, buyer_id";
			$rID=sql_insert("lib_color",$field_array,$data_array,0);
			$rID_1=sql_insert("lib_color_tag_buyer",$field_array_buyer,$data_array_buyer,1);
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
		if(is_duplicate_field( "color_name", " lib_color", "id!=$update_id and LOWER(color_name)=LOWER(".strtoupper($txt_color_name).") and is_deleted=0" ) == 1)
		{
			echo "11**0"; disconnect($con); die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			if($upneed==0)
			{
				$field_array="color_name*tag_buyer*updated_by*update_date*status_active";
				$str_rep=array("_","/", "&", "*", "(", ")", "=","'",",","\r", "\n",'"','#');
				$txt_color_name=str_replace($str_rep,' ',$txt_color_name);
				$data_array="'".trim(strtoupper($txt_color_name))."'*".$txt_tag_buyer_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."";
				//echo "10**".$data_array; die;
				$id_lib_color_tag_buyer=return_next_id( "id", "lib_color_tag_buyer", 1 );
				$data_array_buyer="";
				$tag_buyer=explode(',',str_replace("'","",$txt_tag_buyer_id));
				for($i=0; $i<count($tag_buyer); $i++)
				{
					if($i==0) $add_comma=""; else $add_comma=",";
					$data_array_buyer.="$add_comma(".$id_lib_color_tag_buyer.",".$update_id.",".$tag_buyer[$i].")";
					$id_lib_color_tag_buyer++;
				}
			}
			else if($upneed==1)
			{
				$field_array="tag_buyer";
				$data_array="".$txt_tag_buyer_id."";
				//echo "10**".$data_array; die;
				$id_lib_color_tag_buyer=return_next_id( "id", "lib_color_tag_buyer", 1 );
				$data_array_buyer="";
				$tag_buyer=explode(',',str_replace("'","",$txt_tag_buyer_id));
				for($i=0; $i<count($tag_buyer); $i++)
				{
					if($i==0) $add_comma=""; else $add_comma=",";
					$data_array_buyer.="$add_comma(".$id_lib_color_tag_buyer.",".$update_id.",".$tag_buyer[$i].")";
					$id_lib_color_tag_buyer++;
				}
			}
			$field_array_buyer="id,color_id,buyer_id";
			$rID=sql_update("lib_color",$field_array,$data_array,"id","".$update_id."",0);
			$rID1=execute_query( "delete from lib_color_tag_buyer where color_id = $update_id",0);
			$rID_1=sql_insert("lib_color_tag_buyer",$field_array_buyer,$data_array_buyer,1);
			//echo "10**".$rID1.'='.$rID_1."delete from lib_color_tag_buyer where color_id = $update_id"; die;
	
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

		$rID=sql_delete("lib_color",$field_array,$data_array,"id","".$update_id."",1);
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

if($action=="buyer_name_popup")
{
  	echo load_html_head_contents("Buyer Name Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
?>
	<script>
		var is_disable='<?=$is_disable; ?>';

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

		function set_all()
		{
			var old=document.getElementById('txt_buyer_row_id').value;
			if(old!="")
			{
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{
					js_set_value( old[k] )
				}
			}
		}

		function js_set_value( str )
		{
			// var exstr=strData.split('__')
			// var str=exstr[0];
			// if(exstr[1]==1 && is_disable==1)
			// {
			// 	alert("Buyer Remove Restricted. Only Add Allow.");
			// 	return;
			// }
			
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

			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hidden_buyer_id').val(id);
			$('#hidden_buyer_name').val(name);
		}
    </script>

</head>
<body>
<div align="center">
	<fieldset style="width:370px;margin-left:10px">
    	<input type="hidden" name="hidden_buyer_id" id="hidden_buyer_id" class="text_boxes" value="">
        <input type="hidden" name="hidden_buyer_name" id="hidden_buyer_name" class="text_boxes" value="">
        <form name="searchbuyerfrm_1"  id="searchbuyerfrm_1" autocomplete="off">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
                <thead>
                    <th width="50">SL</th>
                    <th>Buyer Name</th>
                </thead>
            </table>
            <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
					<?
                    $sql_buyer=sql_select("select id, buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name");
                    foreach($sql_buyer as $row)
                    {
                        $buyer_arr[$row[csf('id')]]=$row[csf('buyer_name')];
                    }
                    $i=1; $buyer_row_id="";
                    $hidden_buyer_id=explode(",",$txt_tag_buyer_id);
                    asort($buyer_arr);
                    foreach($buyer_arr as $id=>$name)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$is_remove=0;
                        if(in_array($id,$hidden_buyer_id))
                        {
                            if($buyer_row_id=="") $buyer_row_id=$i; else $buyer_row_id.=",".$i;
							$is_remove=1;
                        }
                        ?>
                        <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>" onClick="js_set_value('<?=$i; ?>');">
                            <td width="50" align="center"><?=$i; ?>
                                <input type="hidden" name="txt_individual_id" id="txt_individual_id<?=$i; ?>" value="<?=$id; ?>"/>
                                <input type="hidden" name="txt_individual" id="txt_individual<?=$i; ?>" value="<?=$name; ?>"/>
                                <input type="hidden" name="txt_mandatory" id="txt_mandatory<?=$i; ?>" value="<?=$mandatory; ?>"/>
                            </td>
                            <td style="word-break:break-all"><?=$name; ?></td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                    <input type="hidden" name="txt_buyer_row_id" id="txt_buyer_row_id" value="<?=$buyer_row_id; ?>"/>
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
                                <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
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