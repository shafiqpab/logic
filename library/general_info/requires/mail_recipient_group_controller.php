<?php
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$lib_designation=return_library_array( "select id,designation from lib_mkt_team_member_info where status_active=1 and is_deleted=0", "id", "designation"  );
$lib_user=return_library_array( "select id,user_name from user_passwd", "id", "user_name"  );

//system id popup here----------------------//


if ($action=="load_drop_down_location_rn")
{
	//$selected=$exdata[0];
  $selected=0;
  if($data=='1'){echo'<input type="text" id="recipient_name" name="recipient_name" class="text_boxes" style="width:212px;"/>';}
  elseif($data=='2')
  {
   echo create_drop_down( "recipient_name", 224, "select id,designation from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id,designation", 1, "--- Select Name ---", 0, "get_php_form_data(this.value,'load_email','requires/email_address_setup_controller');" );
   
   
  }
  else{
    echo create_drop_down( "recipient_name", 224, "select id,user_name from user_passwd where valid=1","id,user_name", 1, "--- Select Name ---", 0, "" );
  }
}

 
if($action=="load_drop_down_mail_template")
{
	list($mail_item,$type,$template_id)=explode('_',$data);

	if($type==5){

		$mail_template_arr[78]=array(1 => "BOM EPM", 2 => "Pre-cost vs Price Quotation", 3 => "Cost Rpt2", 4 => "Cost Woven", 5 => "Bom Woven", 6 => "BOM Dtls", 7 => "Basic Cost", 8 => "MKT Vs Source", 9 => "MO Sheet 2", 10 => "PO Wise", 11 => "BOM PCS", 12 => "MO Sheet PCS", 13 => "Cost Rpt3", 14 => "Slg Cost Rpt", 15 => "BOM pcs woven 4", 16 => "BOM PCS3");


		$mail_template_arr[89]=array(1=>"Cost Rpt",2=>"Cost Rpt2",3=>"BOM Rpt",4=>"BOM Rpt 2",5=>"Acce. Dtls",6=>"Acce. Dtls 2",7=>"Cost Woven",8=>"Bom Woven",9=>"Cost Rpt3",10=>"Cost Rpt4",11=>"Rpt Bpkw",12=>"BOM Dtls",13=>"BOM Rpt 3",14=>"MO Sheet",15=>"Fab. Pre-Cost",16=>"Cost Rpt5",
17=>"Summary",18=>"Budget3 Details",19=>"Cost Rpt6",20=>"Cost sheet",21=>"Budget sheet",22=>"BOM Rpt 4",23=>"MO Sheet 1",
24=>"Budget 4",25=>"MO Sheet 3",26=>"Cost Rpt7");
		$mail_template_arr[90]=array(1=>"Quot. Rpt",2=>"Quot. Rpt2",3=>"Quot. Rpt3",4=>"Quot. Summary",5=>"Cost. Sheet Rpt",6=>"Buyer Sub. Summary",7=>"Summary 2",8=>"LC Cost Dtls",9=>"Quot. Rpt5",10=>"Quot. Rpt6");

		//$mail_template_arr[63]=array(1=>"Mail Additional Info");
	}
	
 	echo create_drop_down( "cbo_mail_template", 162, $mail_template_arr[$mail_item],"", 1, "--Select--", $template_id, "1","","" );
	exit();	
}
 


if($action=="load_email")
{
$sql="select team_member_email from lib_mkt_team_member_info where id=$data";
	$res = sql_select($sql);
	
	foreach($res as $row)
	{	
		echo "$('#email_address').val('".$row[csf("team_member_email")]."');\n";
		}
	exit();	

}



if($action=="mail_recipient_group_from_data")
{
	list($mail_item,$company_id,$buyer_id,$brand_id,$sys_id )=explode('__',$data);

	if($sys_id){$whereCon.=" and id=$sys_id";}
	if($mail_item){$whereCon.=" and mail_item=$mail_item";}
	if($company_id){$whereCon.=" and company_id=$company_id";}
	
	
	$res=sql_select("select id,COMPANY_ID,MAIL_ITEM,mail_id,brand_ids,buyer_ids ,status_active,mail_type,mail_template from mail_group_mst where is_deleted=0 $whereCon");
	

	$brand_arr=return_library_array("select b.id, b.brand_name from  lib_buyer_brand b where b.is_deleted=0 and b.status_active=1 and b.id in(".$res[0][csf("brand_ids")].") group by b.id, b.brand_name order by b.brand_name", "id", "brand_name"  );

	$buyer_arr=return_library_array("select a.id, a.buyer_name from  lib_buyer a where a.id in(".$res[0][csf("buyer_ids")].") order by a.buyer_name", "id", "buyer_name"  );

	echo "load_drop_down( 'requires/mail_recipient_group_controller','".$res[0][csf("MAIL_ITEM")]."'+'_'+'".$res[0][csf("mail_type")]."'+'_'+'".$res[0][csf("mail_template")]."', 'load_drop_down_mail_template', 'mail_template' );";
	
	echo "$('#company_id').val('".$res[0][csf("COMPANY_ID")]."');\n";	
	echo "$('#mail_item').val('".$res[0][csf("MAIL_ITEM")]."');\n";	
	
	// echo "$('#cbo_mail_template').val('".$res[0][csf("mail_template")]."');\n";	
	
	echo "$('#text_brand_name_show').val('".implode('*',$brand_arr)."');\n";	
	echo "$('#text_brand_id').val('".$res[0][csf("brand_ids")]."');\n";	
	
	echo "$('#cbo_user_buyer_show').val('".implode('*',$buyer_arr)."');\n";	
	echo "$('#txt_buyer_id').val('".$res[0][csf("buyer_ids")]."');\n";	
	echo "$('#update_vlues').val('".$res[0][csf("mail_id")]."');\n";	
	echo "$('#update_id').val('".$res[0][csf("id")]."');\n";	
	echo "$('#status').val('".$res[0][csf("status_active")]."');\n";
	echo "$('#cbo_mail_type').val('".$res[0][csf("mail_type")]."');\n";
			
	//echo "show_list_view('$job_no'+'**'+'$booking_no','show_fabric_desc_listview','list_fabric_desc_container','requires/finish_fabric_receive_controller','');\n";
			
				
	if($res[0][csf("id")])
	{
		echo "set_button_status(1, permission, 'mail_recipient_group',1,1);";
	}
	else
	{
		echo "set_button_status(0, permission, 'mail_recipient_group',1,1);";
	}exit();
}//mail_recipient_group_from_data end; 


 
 
 
 
//--------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------
if ($action=="save_update_delete")
{
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN");}

		
		$sqlResult=sql_select("select ID,COMPANY_ID,MAIL_ITEM,MAIL_ID,BRAND_IDS,BUYER_IDS ,STATUS_ACTIVE from mail_group_mst where mail_item=$mail_item and company_id=$company_id and BUYER_IDS=$txt_buyer_id and BRAND_IDS=$text_brand_id and is_deleted=0");
		if(count($sqlResult)>0){echo "15**".str_replace("'",'',$id);exit();}
	
		
		
		if(str_replace("'","",$update_id)=="")
		{
			
			$id= return_next_id("id","mail_group_mst",1);
			$field_array_mst="id, company_id, mail_item, mail_id,buyer_ids,brand_ids,mail_type,mail_template, inserted_by, insert_date, status_active, is_deleted, is_locked";
			$data_array_mst="(".$id.",".$company_id.",".$mail_item.",".$you_have_selected.",".$txt_buyer_id.",".$text_brand_id.",".$cbo_mail_type.",".$cbo_mail_template.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$status.",0,0)";
			$rID=sql_insert("mail_group_mst",$field_array_mst,$data_array_mst,1);
			$data_array="";
			$selected_user_id=explode(',',str_replace("'","",$you_have_selected));
			$id_child= return_next_id("id","mail_group_child",1);
			for($i=0; $i<count($selected_user_id); $i++)
			{
				//if($id_lib_buyer_party_type=="") $id_lib_buyer_party_type=return_next_id( "id", "lib_buyer_party_type", 1 ); else $id_lib_buyer_party_type=$id_lib_buyer_party_type+1;/
				
				if($i==0) $add_comma=""; else $add_comma=",";
				$data_array.="$add_comma(".$id_child.",".$id.",".$mail_item.",".$selected_user_id[$i].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$status.",0,0)";
				$id_child++;
			}
			$field_array="id, mail_group_mst_id, mail_item_mst, mail_user_setup_id, inserted_by, insert_date, status_active, is_deleted, is_locked";
			$rID1=sql_insert("mail_group_child",$field_array,$data_array,1);
			
		}
		
		
		
		if($db_type==0)
		{
		 if( $rID && $rID1)
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
			if( $rID && $rID1)
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

		if(str_replace("'",'',$update_id)!="")
		{
			
			$update_id=str_replace("'",'',$update_id);
			$field_array_up="company_id*mail_item*mail_id*buyer_ids*brand_ids*mail_type*mail_template*updated_by*update_date*status_active";
			$data_array_up="".$company_id."*".$mail_item."*".$you_have_selected."*".$txt_buyer_id."*".$text_brand_id."*".$cbo_mail_type."*".$cbo_mail_template."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$status."";
			
			$rID=sql_update("mail_group_mst",$field_array_up,$data_array_up,"id",$update_id,1);
			$delete=execute_query("delete from mail_group_child where mail_group_mst_id=$update_id",1);
			$data_array="";
			$selected_user_id=explode(',',str_replace("'","",$you_have_selected));
			
			for($i=0; $i<count($selected_user_id); $i++)
			{
				if($id_child=="") $id_child=return_next_id( "id", "mail_group_child", 1 ); else $id_child=$id_child+1;
				
				if($i==0) $add_comma=""; else $add_comma=",";
				$data_array.="$add_comma(".$id_child.",".$update_id.",".$mail_item.",".$selected_user_id[$i].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$status.",0,0)";
				//$id_child++;
			}
			$field_array="id, mail_group_mst_id, mail_item_mst, mail_user_setup_id, inserted_by, insert_date, status_active, is_deleted, is_locked";
			$rID1=sql_insert("mail_group_child",$field_array,$data_array,1);
		}
			

		if($db_type==0)
		{
			if($rID1)
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
				if($rID1)
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
			$field_array="updated_by*update_date*status_active*is_deleted";
			$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			$rID=sql_update("mail_group_mst",$field_array,$data_array,"id","".$update_id."",1);

			$rID2=sql_update("mail_group_child",$field_array,$data_array,"mail_group_mst_id","".$update_id."",1);
 			
			if($db_type==0)
			{
				if($rID && $rID2)
				{
					mysql_query("COMMIT");  
					echo "2**".$update_id;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$update_id;
				}
			}
		 
			if($db_type==2 || $db_type==1 )
			{ 
				if($rID==1 && $rID2==1){
					oci_commit($con); 
					echo "2**".$update_id;
				}
				else{
					oci_rollback($con);
					echo "10**".str_replace("'",'',$update_id);  	
				}
			}

			disconnect($con);
			die;
		}	





}







if ($action=="buyer_selection_popup")//brand_selection_popup
{
	
	extract($_REQUEST);
	//echo load_html_head_contents("Buyer Selection Form","../../", $filter, '', $unicode);
	echo load_html_head_contents("Buyer Selection Form","../../../", 1, 1, $unicode,'','');
 	ob_start();
	?>
	<script type="text/javascript">
	var selected_id = new Array();
	var selected_name = new Array();
		
		function check_all_data(str) {
			//var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			
			//tbl_row_count = tbl_row_count - 1;
			tbl_row_count=str.split(',');
			for( var i = 0; i <= tbl_row_count.length; i++ ) {
				js_set_value( tbl_row_count[i] );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			
			var buyer_row_id= document.getElementById( 'search' + str );
			if (buyer_row_id !=null) 
			{
				toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
				
				
				
				if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) 
				{
					selected_id.push( $('#txt_individual_id' + str).val() );
					selected_name.push( $('#txt_individual' + str).val() );
					//selected_job.push( $('#txt_individual_job' + str).val() );
					
				}
				else 
				{
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
					//selected_job.splice( i,1);
				}
				var id ='';
				var name = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + '*';
					//job += selected_job[i] + '*';
				}
				id = id.substr( 0, id.length - 1 );
				name = name.substr( 0, name.length - 1 );
				//job = job.substr( 0, job.length - 1 );
				
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name );
				//$('#txt_selected_job').val( job );
			}
		}
     </script>
     <input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
     <input type="hidden" name="txt_selected"  id="txt_selected" width="650px" value="" />
	<div style="width:625px; overflow-y:scroll; min-height:50px; max-height:250px;" id="buyer_list_view" align="left">
		<table  cellspacing="0" cellpadding="0" width="100%" class="rpt_table" id="tbl_list_search" border="1" rules="all" >
			<thead>
				<tr>
				<th width="50" align="left">SL No</th>
				<th width="130" align="left">Buyer Name</th>
                <th width="120" align="left">Sub Con. Party</th>
				<th width="100" align="left">Contact Person</th>
				
				<th align="center">Email</th>
				</tr>
			</thead>
		
		<tbody id="search_tble">		
		<?php

			if($unit_name != ""){ $buyers_cond="and b.tag_company in($unit_name)"; }
			else{ $buyers_cond=""; }
		 
			$i=1;

			$nameArray=sql_select( "select a.id, a.buyer_name, a.contact_person, a.buyer_email from lib_buyer a, lib_buyer_tag_company b where a.is_deleted=0 and a.status_active=1 and a.id=b.buyer_id and b.TAG_COMPANY=$company_id $buyers_cond group by a.id, a.buyer_name, a.contact_person, a.buyer_email  order by a.buyer_name" );

			 foreach ($nameArray as $selectResult)
			 {
			 $id_arr[]=$selectResult[csf('id')];
				if ($i%2==0)  
                	$bgcolor="#E9F3FF";
                else
                	$bgcolor="#FFFFFF";	
	
		?>		
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $selectResult[csf('id')];?>" onclick="js_set_value(<? echo $selectResult[csf('id')];?>)"> 
				<td width="50" align="center"><?php echo "$i"; ?>
                 <input type="hidden" name="txt_individual" id="txt_individual<? echo $selectResult[csf('id')];?>" value="<?php echo $selectResult[csf('buyer_name')]; ?>"/>
                 <input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $selectResult[csf('id')];?>" value="<?php echo $selectResult[csf('id')]; ?>"/>	
					                         
                </td>	
				<td width="130"><p><?php echo $selectResult[csf('buyer_name')]; ?></p></td>
                <td width="120">
                    <p> 
						<?php
							$frm=$replacement_lc[$selectResult[csf('subcontract_party')]];  
							echo $frm;
                        ?>
                    </p>
                </td>
				<td width="100"> <p><?php echo $selectResult[csf('contact_person')];  ?></p></td>
				<td><p><?php echo  $selectResult[csf('buyer_email')];   ?></p></td>				
			</tr>
			<?php
			$i++;
			}
			?>
			</tbody>
		</table>
	</div>
    <div style="width:600px;" align="left">
		<table width="100%" rules="all" border="1">
    		<tr>
				<td align="center" colspan="6" height="30" valign="bottom">
					<div style="width:100%"> 
							<div style="width:50%; float:left" align="left">
								<input type="checkbox" name="check_all" id="check_all" onclick="check_all_data('<? echo implode(',',$id_arr);?>')" /> Check / Uncheck All
							</div>
							<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onclick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
							</div>
					</div>
				</td>
			</tr>
        </table>
	</div>
<script>
		var buyer_data='<? echo $data;?>';
		buyer_arr=buyer_data.split(',');
		for(var i=0;i<buyer_arr.length;i++)
		{
			js_set_value( buyer_arr[i] );	
		}
		setFilterGrid("search_tble",-1);
</script>
<?	 
}

if ($action=="brand_selection_popup")//brand_selection_popup
{
	extract($_REQUEST);
	//echo load_html_head_contents("Buyer Selection Form","../../", $filter, '', $unicode);
	echo load_html_head_contents("Brand Selection Form","../../../", 1, 1, $unicode,'','');
 	ob_start();
	?>
	<script type="text/javascript">
	var selected_id = new Array();
	var selected_name = new Array();
		
		function check_all_data(str) {
			//var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			
			//tbl_row_count = tbl_row_count - 1;
			tbl_row_count=str.split(',');
			for( var i = 0; i <= tbl_row_count.length; i++ ) {
				js_set_value( tbl_row_count[i] );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			
			var buyer_row_id= document.getElementById( 'search' + str );
			if (buyer_row_id !=null) 
			{
				toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
				
				
				
				if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) 
				{
					selected_id.push( $('#txt_individual_id' + str).val() );
					selected_name.push( $('#txt_individual' + str).val() );
					//selected_job.push( $('#txt_individual_job' + str).val() );
					
				}
				else 
				{
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
					//selected_job.splice( i,1);
				}
				var id ='';
				var name = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + '*';
					//job += selected_job[i] + '*';
				}
				id = id.substr( 0, id.length - 1 );
				name = name.substr( 0, name.length - 1 );
				//job = job.substr( 0, job.length - 1 );
				
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name );
				//$('#txt_selected_job').val( job );
			}
		}
     </script>
     <input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
     <input type="hidden" name="txt_selected"  id="txt_selected" width="650px" value="" />
	<div style="width:625px; overflow-y:scroll; min-height:50px; max-height:250px;" id="buyer_list_view" align="left">
		<table  cellspacing="0" cellpadding="0" width="100%" class="rpt_table" id="tbl_list_search" border="1" rules="all" >
			<thead>
				<tr>
				<th width="50" align="left">SL No</th>
				<th width="130" align="left">Brand Name</th>
                <th width="" align="left">Buyer Name</th>
				
				</tr>
			</thead>
		
		<tbody id="search_tble">		
		<?php

		$buyer_array=return_library_array( "Select id,buyer_name from lib_buyer", "id", "buyer_name"  );
		//$brand_array=return_library_array( "Select id,brand_name from lib_buyer_brand", "id", "brand_name"  );
			
			
			if($buyer_id!=''){ $buyers_cond="and b.buyer_id in($buyer_id)"; }
			else{ $buyers_cond=""; }
			$i=1;

			$nameArray=sql_select( "select b.id, b.brand_name, b.buyer_id from  lib_buyer_brand b where b.is_deleted=0 and b.status_active=1 $buyers_cond group by b.id, b.brand_name, b.buyer_id  order by b.brand_name" );


			 foreach ($nameArray as $selectResult)
			 {
			 $id_arr[]=$selectResult[csf('id')];
				if ($i%2==0)  
                	$bgcolor="#E9F3FF";
                else
                	$bgcolor="#FFFFFF";	
	
		?>		
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $selectResult[csf('id')];?>" onclick="js_set_value(<? echo $selectResult[csf('id')];?>)"> 
				<td width="50" align="center"><?php echo "$i"; ?>
                 <input type="hidden" name="txt_individual" id="txt_individual<? echo $selectResult[csf('id')];?>" value="<?php echo $selectResult[csf('brand_name')]; ?>"/>
                 <input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $selectResult[csf('id')];?>" value="<?php echo $selectResult[csf('id')]; ?>"/>	
					                         
                </td>	
				<td width="130"><p>
					<?php 	
						echo $selectResult[csf('brand_name')];							
					?></p>
				</td>
                <td width="130"><p>
					<?php 	
						echo $buyer_array[$selectResult[csf('buyer_id')]];							
					?></p>
				</td>
              			
			</tr>
			<?php
			$i++;
			}
			?>
			</tbody>
		</table>
	</div>
    <div style="width:600px;" align="left">
		<table width="100%" border="1" rules="all">
    		<tr>
				<td align="center" colspan="6" height="30" valign="bottom">
					<div style="width:100%"> 
							<div style="width:50%; float:left" align="left">
								<input type="checkbox" name="check_all" id="check_all" onclick="check_all_data('<? echo implode(',',$id_arr);?>')" /> Check / Uncheck All
							</div>
							<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onclick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
							</div>
					</div>
				</td>
			</tr>
        </table>
	</div>
<script>
		var buyer_data='<? echo $data;?>';
		buyer_arr=buyer_data.split(',');
		for(var i=0;i<buyer_arr.length;i++)
		{
			js_set_value( buyer_arr[i] );	
		}
		setFilterGrid("search_tble",-1);
</script>
<?	 
}


if($action=="item_list_view")
{ 
	    list($company,$mail_item)=explode('_',$data);
 
		$com_arr=return_library_array( "select id,COMPANY_SHORT_NAME from lib_company where status_active=1 and is_deleted=0 order by COMPANY_SHORT_NAME", "id", "COMPANY_SHORT_NAME"  );

		$brand_arr=return_library_array("select b.id, b.brand_name from  lib_buyer_brand b where b.is_deleted=0 and b.status_active=1  group by b.id, b.brand_name order by b.brand_name", "id", "brand_name"  );
	
		$buyer_arr=return_library_array("select a.id, a.buyer_name from  lib_buyer a where  1=1 order by a.buyer_name", "id", "buyer_name"  );

		
		$sqlResult=sql_select("select ID,COMPANY_ID,MAIL_ITEM,MAIL_ID,BRAND_IDS,BUYER_IDS ,STATUS_ACTIVE,MAIL_TYPE from mail_group_mst where mail_item=$mail_item and company_id=$company and is_deleted=0");
		
		$i=1;
		$mail_type = array( 1 => "Auto Mail", 5 => "Manual / Additional Mail", 2 => "Ready to Approved", 3 => "Full Approved", 4 => "Partial Approved", 6 => "Deny", 7 => "Unapproved", 8 => "Revised");
		foreach($sqlResult as $rows){
		    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
		
			$buyerNameArr=array();
			$brandNameArr=array();
			foreach(explode(',',$rows['BUYER_IDS']) as $bid){
				$buyerNameArr[$bid]= $buyer_arr[$bid];
			}
			foreach(explode(',',$rows['BRAND_IDS']) as $brid){
				$brandNameArr[$brid]= $brand_arr[$brid];
			}
		
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer" onclick="get_update_data(0,<?=$rows['ID'];?>);"> 
				<td width="35"><?= $i;?></td>
				<td><?= $com_arr[$rows['COMPANY_ID']];?></td>
				<td><?= $form_list_for_mail[$rows['MAIL_ITEM']]." [".$rows['MAIL_ITEM']."]";?></td>
				<td><p><?= implode(', ',$buyerNameArr);?></p></td>
				<td><p><?= implode(', ',$brandNameArr);?></p></td>
				<td><p><?= $mail_type[$rows['MAIL_TYPE']];;?></p></td>
			</tr>
        <?
		$i++;
		}
        
}



if($action=="mail_list_view")
{ 
?>	
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
	
<?	
}

?>

