<?php

header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


//print_r($com_arr);die;
if ($action=="body_part_title_list_view")
{
	$com_arr=return_library_array( "select id, company_name from lib_company", 'id', 'company_name');
	$arr = array(0 => $com_arr);
	echo  create_list_view ( "list_view", "Company Name,Body Part Title", "225,225","500","220", 0, "select company_id,bundle_use_for,id from ppl_bundle_title where status_active=1 and is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "company_id, 0", $arr, "company_id,bundle_use_for", "../merchandising_details/requires/body_part_title_entry_controller", 'setFilterGrid("list_view",-1);' );
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select  id, company_id, bundle_use_for from ppl_bundle_title where status_active=1 and is_deleted=0 and id='$data'" );
	foreach ($nameArray as $inf)
	{		
		echo "document.getElementById('cbo_company_name').value = '".$inf[csf("company_id")]."';\n";
		echo "document.getElementById('txt_body_part_title_name').value  = '".($inf[csf("bundle_use_for")])."';\n";
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_body_part_title',1);\n";
	}
}


if ($action=="company_popup")
{
	echo load_html_head_contents("Company Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $party_type_id;
	?>
	<script>
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});
		var selected_id = new Array(); var selected_name = new Array(); var buyer_id=''; var style_ref_array= new Array();
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
			var old=document.getElementById('txt_party_row_id').value; 
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
			$('#hidden_party_id').val(id);
			$('#hidden_party_name').val(name);
		}
    </script>
	</head>
	<body>
		<div align="center">
			<fieldset style="width:370px;margin-left:10px">
		    	<input type="hidden" name="hidden_party_id" id="hidden_party_id" class="text_boxes" value="">
		        <input type="hidden" name="hidden_party_name" id="hidden_party_name" class="text_boxes" value="">
		        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
		            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
		                <thead>
		                    <th width="50">SL</th>
		                    <th>Company Name</th>
		                </thead>
		            </table>
		            <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
		                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
		                <?
		                    $i=1; $party_row_id=''; 
							$hidden_party_id=explode(",",$txt_copy_to_company_id);
							$company_array=return_library_array( "select id, company_name from lib_company where status_active=1 and id <> $cbo_company_name",'id','company_name');
		                    foreach($company_array as $id=>$name)
		                    {
								
								//if(in_array($id,$not_process_id_print_array))
								//{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									if(in_array($id,$hidden_party_id)) 
									{ 
										if($party_row_id=="") $party_row_id=$i; else $party_row_id.=",".$i;
									}
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
										<td width="50" align="center"><?php echo "$i"; ?>
											<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>	
											<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name; ?>"/>
										</td>	
										<td><p><? echo $name; ?></p></td>
									</tr>
									<?
									$i++;
								//}
		                    }
		                ?>
		                <input type="hidden" name="txt_party_row_id" id="txt_party_row_id" value="<?php echo $party_row_id; ?>"/>
		                </table>
		            </div>
		             <table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
		                <tr>
		                    <td align="center" height="30" valign="bottom">
		                        <div style="width:100%"> 
		                            <div style="width:50%; float:left" align="left">
		                                <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
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
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		set_all();
	</script>
	</html>
	<?
	exit();
}


if ($action=="save_update_delete")
{

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$com_copy_id=str_replace("'","",$txt_copy_to_company_id);
	$txt_copy_to_company_id=str_replace("'","",$txt_copy_to_company_id);

	if($txt_copy_to_company_id!="") $txt_copy_to_company_id=$txt_copy_to_company_id.",".str_replace("'","",$cbo_company_name); else $txt_copy_to_company_id=str_replace("'","",$cbo_company_name);

	if ($operation==0)  // Insert Here
	{	
		//$duplicate = is_duplicate_field("id","ppl_bundle_title","company_id=$cbo_company_name and bundle_use_for=$txt_body_part_title_name and is_deleted=0 ");

		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if($txt_copy_to_company_id != "")
		{
			$company_id_array = return_library_array("select id,id as cmid from lib_company where is_deleted=0 and status_active=1 and id in($txt_copy_to_company_id)",'id','cmid');
			$company_ids = implode(",",$company_id_array);

			if(is_duplicate_field("id","ppl_bundle_title","company_id in($company_ids) and bundle_use_for=$txt_body_part_title_name and is_deleted=0 ") == 1)
			{
				echo "11**0";die;
			}


			
			//$id=return_next_id( "id", "ppl_bundle_title", 1 ) ;
			$field_array="id,company_id,bundle_use_for,inserted_by,insert_date,status_active,is_deleted";


			$data_array="";$j=0;
			$id=return_next_id( "id", "ppl_bundle_title", 1 ) ;
			foreach($company_id_array as $com_id=>$val_com_id)
			{ 
				//$id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				//$id=return_next_id( "id", "ppl_bundle_title", 1 ) ;
				if($j==0)
				{
					$data_array="(".$id.",".$com_id.",".$txt_body_part_title_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."', 1, 0)";
				}
				else 
				{
					$data_array .="(".$id.",".$com_id.",".$txt_body_part_title_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."', 1, 0)";
				}
 				$j++;
 				$id++;
			}

			//$data_array="(".$id.",".$cbo_company_name.",".$txt_body_part_title_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."', 1, 0)";

			//Insert Data in ppl_bundle_title Table----------------------------------------
			//echo "10**INSERT INTO ppl_bundle_title (".$field_array.") VALUES ".$data_array."";die;
			
			$rID=sql_insert("ppl_bundle_title",$field_array,$data_array,1);

			//----------------------------------------------------------------------------------

			if($db_type==0)
			{
				if($rID)
				{
					mysql_query("COMMIT");
					echo "0**".$rID;
				}
				else{
					mysql_query("ROLLBACK");
					echo "10**".$rID;
				}
			}

			if($db_type==2 || $db_type==1 )
			{
				if($rID)
				{
					oci_commit($con);
					echo "0**".$rID;
				}
				else{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
		}
		else
		{
			if(is_duplicate_field("id","ppl_bundle_title","company_id=$cbo_company_name and bundle_use_for=$txt_body_part_title_name and is_deleted=0 ") == 1)
			{
				echo "11**0";die;
			}


			/*$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}*/
			$id=return_next_id( "id", "ppl_bundle_title", 1 ) ;
			$field_array="id,company_id,bundle_use_for,inserted_by,insert_date,status_active,is_deleted";

			$data_array="(".$id.",".$cbo_company_name.",".$txt_body_part_title_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."', 1, 0)";

			//Insert Data in ppl_bundle_title Table----------------------------------------
			//echo "10**INSERT INTO ppl_bundle_title (".$field_array.") VALUES ".$data_array."";die;
			
			$rID=sql_insert("ppl_bundle_title",$field_array,$data_array,1);

			//----------------------------------------------------------------------------------

			if($db_type==0)
			{
				if($rID)
				{
					mysql_query("COMMIT");
					echo "0**".$rID;
				}
				else{
					mysql_query("ROLLBACK");
					echo "10**".$rID;
				}
			}

			if($db_type==2 || $db_type==1 )
			{
				if($rID)
				{
					oci_commit($con);
					echo "0**".$rID;
				}
				else{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}

		}


		disconnect($con);
		die;		
	}
	else if ($operation==1)   // Update Here
	{


		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if(is_duplicate_field("id","ppl_bundle_title","company_id=$cbo_company_name and bundle_use_for=$txt_body_part_title_name and is_deleted=0 and id !=$update_id") == 1)
		{
			echo "11**0";die;
		}

		
		/*if($com_copy_id !="")
		{
			$prev_item_sql="select company_id, bundle_use_for from ppl_bundle_title where id=$update_id";
			$prev_item_sql_result=sql_select($prev_item_sql);
			$com_company_id=$prev_item_sql_result[0][csf("company_id")];
			$com_bundle_use_for=$prev_item_sql_result[0][csf("bundle_use_for")];
			
			
			if(trim(str_replace("'","",$cbo_company_name)) != trim($com_company_id) || trim(str_replace("'","",$txt_body_part_title_name)) != trim($com_bundle_use_for))
			{
				echo "11**Previous Item And Current Item Not Match.";
				disconnect($con);die;
			}
		}*/


		if($txt_copy_to_company_id !="")
		{
			$insert_com_array[str_replace("'","",$cbo_company_name)]=str_replace("'","",$cbo_company_name);
			if(str_replace("'","",$txt_body_part_title_name)=='') $duplicate_cond=" and bundle_use_for is null"; else $duplicate_cond=" and bundle_use_for=$txt_body_part_title_name";
			
			$another_com_item_sql="select company_id from ppl_bundle_title where company_id=$cbo_company_name and bundle_use_for=$txt_body_part_title_name and is_deleted=0 and id<>$update_id $duplicate_cond ";
			$another_com_item_sql_result=sql_select($another_com_item_sql);
			foreach($another_com_item_sql_result as $row)
			{
				$insert_com_array[$row[csf("company_id")]]=$row[csf("company_id")];
			}
			$com_cond="";
			if(count($insert_com_array)>0) $com_cond=" and id not in(".implode(",",$insert_com_array).") ";
			$company_id_array = return_library_array("select id, id as cmid from lib_company where is_deleted=0 and status_active=1 and id in($txt_copy_to_company_id) $com_cond",'id','cmid');



			$field_array_insert="id,company_id,bundle_use_for,inserted_by,insert_date,status_active,is_deleted";
			$j=0;
			$id=return_next_id( "id", "ppl_bundle_title", 1 );
			foreach($company_id_array as $com_id=>$val_com_id)
			{
				//$id=return_next_id( "id", "ppl_bundle_title", 1 );
				if($j==0)
				{
					$data_array_insert="(".$id.",".$com_id.",".$txt_body_part_title_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."', 1, 0)";
				}
				else 
				{
					$data_array_insert .="(".$id.",".$com_id.",".$txt_body_part_title_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."', 1, 0)";
					
				}
 				$j++;
 				$id++;

			}



		}



		//echo "10** insert into ppl_bundle_title ($field_array_insert) values $data_array_insert";oci_rollback($con);disconnect($con);die;


		$field_array="company_id*bundle_use_for*updated_by*update_date";

		$data_array="".$cbo_company_name."*".$txt_body_part_title_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//echo $data_array;die;
		//echo "10** insert into ppl_bundle_title ($field_array) values $data_array";oci_rollback($con);disconnect($con);die;
		$rID=sql_update("ppl_bundle_title", $field_array, $data_array, "id", $update_id, 1);
		//Update Data in ppl_bundle_title Table----------------------------------------
		$rID_insert=true;
		if($data_array_insert!="")
		{
			$rID_insert=sql_insert("ppl_bundle_title",$field_array_insert,$data_array_insert,0);
		}

		//echo "10** insert into ppl_bundle_title ($field_array_insert) values $data_array_insert";oci_rollback($con);disconnect($con);die;
		//echo "10**$rID**$rID_insert";die;
		
		if($db_type==0)
		{
			if($rID && $rID_insert)
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
		if($db_type==2 || $db_type==1 )
		   {
	        if($rID && $rID_insert)
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
 //    else if ($operation==2)   // Delete Here
	// {

	// 	$con = connect();
	// 	if($db_type==0)
	// 	{
	// 		mysql_query("BEGIN");
	// 	}

	// 	$field_array="updated_by*update_date*status_active*is_deleted";
	//     $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";

	// 	$rID=sql_delete("ppl_bundle_title",$field_array,$data_array,"id","".$update_id."",1);

	// 	if($db_type==0)
	// 	{
	// 		if($rID )
	// 		  {
	// 			mysql_query("COMMIT");
	// 			echo "2**".$rID;
	// 		   }
	// 		else
	// 		  {
	// 			mysql_query("ROLLBACK");
	// 			echo "10**".$rID;
	// 		  }
	// 	}
	// 	if($db_type==2 || $db_type==1 )
	// 	   {
	//           if($rID )
	// 		    {
	// 				oci_commit($con);
	// 				echo "2**".$rID;
	// 			}
	// 			else{
	// 				oci_rollback($con);
	// 				echo "10**".$rID;
	// 			}
	// 	   }
	// 	disconnect($con);
	// 	die;
	// }

}
?>