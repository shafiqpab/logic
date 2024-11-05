<?
/*-------------------------------------------- Comments
Version (MySql)          :  V2
Version (Oracle)         :  V1
Converted by             :  MONZU
Converted Date           :  21-05-2014
Purpose			         :  This Form Will Create Sample Development Entry.						
Functionality	         :	
JS Functions	         :
Created by		         :	Shajjad 
Creation date 	         : 
Requirment Client        :  Fakir Apperels
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :                
DB Script                : 
Updated by 		         : 	Kaiyum	
Update date		         : 	26-09-2016 
QC Performed BY	         :		
QC Date			         :	
Comments		         : 	[ Kaiyum: update for 'buyer wise season auto select' ]
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$size_arr=return_library_array( "select id, size_name from  lib_size where status_active =1 and is_deleted=0",'id','size_name');
$color_library=return_library_array( "select id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name"  );
$season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name"  );
if ($action=="load_drop_down_season_com")
{ 
	 $season_mandatory_arr=sql_select( "select id, season_mandatory,company_name from variable_order_tracking where company_name='$data' and variable_list=44 order by id" );
	if($season_mandatory_arr[0]['season_mandatory'] == 1)
	{
	echo create_drop_down( "cbo_season_name", 130, "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ","id,season_name", 1, "-- Select Season --", $selected,"" );
	}
	else
	{
		echo create_drop_down( "cbo_season_name", 130, " ","", 1, "-- Select Season --", $selected,"" );
	}

	exit();	 
} 
if($action=="only_for_all_season")
{
	echo create_drop_down( "cbo_season_name", 130, "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ","id,season_name", 1, "-- Select Season --", $selected,"" );
}
if ($action=="load_drop_down_season_buyer")
{
	$datas=explode('_',$data);
	
	//echo create_drop_down( "cbo_season_name", 130, "select a.id,a.season_name from lib_buyer_season a,variable_order_tracking b where a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.buyer_id='$datas[0]' and b.company_name='$datas[1]' and b.season_mandatory=1 and b.variable_list=44","id,season_name", 1, "-- Select Season --", $selected, "" );
	
	
	echo create_drop_down( "cbo_season_name", 130, "select a.id,a.season_name from lib_buyer_season a,lib_buyer_tag_company b where a.buyer_id=b.buyer_id and a.status_active =1 and a.is_deleted=0 and a.buyer_id='$datas[0]' and b.tag_company='$datas[1]'","id,season_name", 1, "-- Select Season --", $selected, "" );
	exit();
}
if ($action=="load_drop_down_buyer")
{ //$selected
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", "", "load_drop_down( 'requires/sample_development_controller', this.value, 'cbo_sample_type', 'sampletd' );load_drop_down( 'requires/sample_development_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_season_buyer', 'season_td');" ); 
}

else if ($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_agent", 130, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21,23))  order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" );  
	exit();	 	 
} 

else if ($action=="cbo_dealing_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 130, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	exit();
}

else if ($action=="cbo_sample_type")
{
	
	//echo "select a.sample_name,a.id from lib_sample a, lib_buyer_tag_sample b where a.id=b.tag_sample and b.buyer_id=$data and b.sequ!=0 and a.is_deleted=0 and a.status_active=1 order by a.sample_name";
	$sample_library=return_library_array( "select a.sample_name,a.id from lib_sample a, lib_buyer_tag_sample b where a.id=b.tag_sample and b.buyer_id=$data and b.sequ!=0 and a.is_deleted=0 and a.status_active=1 order by a.sample_name", "id", "sample_name"  );
	if(count($sample_library)==0){
		$sample_library=return_library_array( "select sample_name,id from lib_sample where is_deleted=0 and status_active=1 order by sample_name", "id", "sample_name"  );

	}
    echo create_drop_down( "cbo_sample_type", 130, $sample_library,"", '1', "--Select--", '', "",'','' );
	exit();
}

else if ($action=="save_update_delete_mst")
{
   $process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id_mst=return_next_id( "id", "sample_development_mst", 1 ) ;
		
		$field_array="id,company_id,buyer_name,quotation_id,style_ref_no,product_dept,product_code,bh_merchant,article_no,item_name,item_category,region,agent_name,team_leader,dealing_marchant,estimated_shipdate,remarks,season_buyer_wise,inserted_by,insert_date,status_active,is_deleted";
		$data_array="(".$id_mst.",".$cbo_company_name.",".$cbo_buyer_name.",".$txt_quotation_id.",".$txt_style_name.",".$cbo_product_department.",".$txt_product_code.",".$txt_bhmerchant.",".$txt_article_no.",".$cbo_item_name.",".$txt_item_catgory.",".$cbo_region.",".$cbo_agent.",".$cbo_team_leader.",".$cbo_dealing_merchant.",".$txt_est_ship_date.",".$txt_remarks.",".$cbo_season_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		$rID=sql_insert("sample_development_mst",$field_array,$data_array,1);
		//echo $rID; die;
		
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "0**".$id_mst;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$id_mst;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con); 
				echo "0**".$id_mst;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$id_mst;
			}
		}
		disconnect($con);
		die;
	}
	
	if ($operation==1)  // Update Here
	{	
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="company_id*buyer_name*quotation_id*style_ref_no*product_dept*product_code*bh_merchant*article_no*item_name*item_category*region*agent_name*team_leader*dealing_marchant*estimated_shipdate*remarks*season_buyer_wise*updated_by*update_date*status_active*is_deleted";
		//txt_bhmerchant*txt_product_code
		$data_array="".$cbo_company_name."*".$cbo_buyer_name."*".$txt_quotation_id."*".$txt_style_name."*".$cbo_product_department."*".$txt_product_code."*".$txt_bhmerchant."*".$txt_article_no."*".$cbo_item_name."*".$txt_item_catgory."*".$cbo_region."*".$cbo_agent."*".$cbo_team_leader."*".$cbo_dealing_merchant."*".$txt_est_ship_date."*".$txt_remarks."*".$cbo_season_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
		
		 $rID=sql_update("sample_development_mst",$field_array,$data_array,"id","".$txt_style_id."",1);
		
		
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_style_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_style_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_style_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_style_id);
			}
		}
		disconnect($con);
		die;
	}
	
	if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		$rID1=sql_delete("sample_development_mst",$field_array,$data_array,"id","".$txt_style_id."",0);
		$rID2=sql_delete("sample_development_dtls",$field_array,$data_array,"sample_mst_id","".$txt_style_id."",0);
		$rID3=sql_delete("sample_development_size",$field_array,$data_array,"mst_id","".$txt_style_id."",1);
		if($db_type==0)
		{
			if($rID1 && $rID2 && $rID3)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_style_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_style_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID1 && $rID2 && $rID3)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_style_id);
			}
			else
			{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$txt_style_id);
			}
		}
		disconnect($con);
	}
}


else if ($action=="save_update_delete_dtl")
{       
   $process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	if ($operation==0)  // Insert Here
	{
		//echo "11**";
		$con = connect();
		/*$new_array_color=array();
		if (!in_array(str_replace("'","",$txt_sample_color),$new_array_color))
		{
		  $txt_sample_color = return_id( str_replace("'","",$txt_sample_color), $color_library, "lib_color", "id,color_name");  
			 // $new_array_color[$color_id]=str_replace("'","",$$txtcolor);
		}*/
		
		if(str_replace("'","",$txt_sample_color)!="")
		{
			if (!in_array(str_replace("'","",$txt_sample_color),$new_array_color))
			{
				$txt_sample_color = return_id( str_replace("'","",$txt_sample_color), $color_library, "lib_color", "id,color_name","116");
				$new_array_color[$txt_sample_color]=str_replace("'","",$txt_sample_color);
			}
			else $txt_sample_color =  array_search(str_replace("'","",$txt_sample_color), $new_array_color);
		}
		else $txt_sample_color=0;
		
		//print_r( $txt_sample_color); die;
		if (is_duplicate_field( "id", "sample_development_dtls", "sample_mst_id=$txt_style_id and sample_name=$cbo_sample_type and sample_color=$txt_sample_color and approval_status in(1,3,5) and is_deleted=0" ) == 1)
		{
			echo "11**0";disconnect($con); die;
		}
		
		else
		{
			
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			
			
			$id_dtls=return_next_id( "id", "sample_development_dtls", 1 ) ;
			
			$field_array="id,sample_mst_id,sample_name,sample_color,working_factory,sent_to_factory_date,factory_dead_line,recieve_date_from_buyer,receive_date_from_factory,fabrication,fabric_sorce,sent_to_buyer_date,key_point,approval_status,department,status_date,tf_receive_date,buyer_meeting_date,sample_charge,sample_curency,buyer_dead_line,buyer_req_no,comments,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id_dtls.",".$txt_style_id.",".$cbo_sample_type.", ".$txt_sample_color.",".$txt_working_factory.",".$txt_sent_to_factory_date.",".$txt_factory_dead_line_date.",".$txt_receive_date_from_buyer.",".$txt_receive_date_from_factory.",".$txt_fabrication.",".$cbo_fabric_sorce.",".$txt_sent_to_buyer_date.",".$txt_key_point.",".$cbo_approval_status.",".$txt_department.",".$txt_status_date.",".$txt_tf_receive_date.",".$txt_buyer_meeting_date.",".$txt_sample_charge.",".$cbo_curency.",".$txt_buyer_dead_line_date.",".$txt_buyer_request_no.",".$txt_comments.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$rID=sql_insert("sample_development_dtls",$field_array,$data_array,1);
			//echo $rID; die;
			
			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");  
					echo "0**".str_replace("'","",$id_dtls);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".str_replace("'","",$id_dtls);
				}
			}
			
			if($db_type==2 || $db_type==1 )
			{
				if($rID )
				{
					oci_commit($con);  
					echo "0**".str_replace("'","",$id_dtls);
				}
				else
				{
					oci_rollback($con); 
					echo "10**".str_replace("'","",$id_dtls);
				}
			}
			disconnect($con);
			die;
		}
	}
	
	if ($operation==1)  //Update Here
	{
		if (is_duplicate_field( "id", "sample_development_dtls", "sample_mst_id=$txt_style_id and sample_name=$cbo_sample_type and sample_color=$txt_sample_color and working_factory=$txt_working_factory and id!=$update_id_dtl  and is_deleted=0" ) == 1)
		{
			echo "11**0"; disconnect($con);die;
		}
		
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			
			/*$new_array_color=array();
			//echo "11**0";
			if (!in_array(str_replace("'","",$txt_sample_color),$new_array_color))
			{
				$txt_sample_color = return_id( str_replace("'","",$txt_sample_color), $color_library, "lib_color", "id,color_name");  
				 // $new_array_color[$color_id]=str_replace("'","",$$txtcolor);
			}*/
			if(str_replace("'","",$txt_sample_color)!="")
			{
				if (!in_array(str_replace("'","",$txt_sample_color),$new_array_color))
				{
					$txt_sample_color = return_id( str_replace("'","",$txt_sample_color), $color_library, "lib_color", "id,color_name","116");
					$new_array_color[$txt_sample_color]=str_replace("'","",$txt_sample_color);
				}
				else $txt_sample_color =  array_search(str_replace("'","",$txt_sample_color), $new_array_color);
			}
			else $txt_sample_color=0;
			//echo $txt_sample_color ; die;
			$field_array="sample_mst_id*sample_name*sample_color*working_factory*sent_to_factory_date*factory_dead_line*recieve_date_from_buyer*receive_date_from_factory*fabrication*fabric_sorce*sent_to_buyer_date*key_point*approval_status*department*status_date*tf_receive_date*buyer_meeting_date*sample_charge*sample_curency*buyer_dead_line*buyer_req_no*comments*updated_by*update_date*status_active*is_deleted";
			
			$data_array="".$txt_style_id."*".$cbo_sample_type."*".$txt_sample_color."*".$txt_working_factory."*".$txt_sent_to_factory_date."*".$txt_factory_dead_line_date."*".$txt_receive_date_from_buyer."*".$txt_receive_date_from_factory."*".$txt_fabrication."*".$cbo_fabric_sorce."*".$txt_sent_to_buyer_date."*".$txt_key_point."*".$cbo_approval_status."*".$txt_department."*".$txt_status_date."*".$txt_tf_receive_date."*".$txt_buyer_meeting_date."*".$txt_sample_charge."*".$cbo_curency."*".$txt_buyer_dead_line_date."*".$txt_buyer_request_no."*".$txt_comments."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";		
			
			$rID=sql_update("sample_development_dtls",$field_array,$data_array,"id","".$update_id_dtl."",1);
			/*$field_array="team_leader_name*team_leader_desig*team_leader_email*updated_by*update_date*status_active*is_deleted";
			$data_array="".$txt_member_name."*".$txt_member_designation."*".$txt_member_email."*".$_SESSION['logic_erp']['user_id']."*'".$date."'*".$cbo_team_member_status."*0";
			$rID=sql_update("lib_marketing_team",$field_array,$data_array,"lib_mkt_team_member_info_id","".$update_id_dtl."",1);*/
			//echo $rID; die;
			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");  
					echo "1**".str_replace("'","",$update_id_dtl);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".str_replace("'","",$update_id_dtl);
				}
			}
			if($db_type==2 || $db_type==1 )
			{
				if($rID )
				{
					oci_commit($con);  
					echo "1**".str_replace("'","",$update_id_dtl);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$update_id_dtl);
				}
			}
			disconnect($con);
		}
		exit();
	}
	
	if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		$rID1=sql_delete(" sample_development_dtls",$field_array,$data_array,"id","".$update_id_dtl."",0);
		$rID2=sql_delete("sample_development_size",$field_array,$data_array,"dtls_id","".$update_id_dtl."",1);
//echo $rID1."==".$rID2; die;
		
		if($db_type==0)
		{
			if($rID1 && $rID2 )
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$update_id_dtl);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$update_id_dtl);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID1 && $rID2 )
			{
				oci_commit($con); 
				echo "2**".str_replace("'","",$update_id_dtl);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id_dtl);
			}
		}
		disconnect($con);
			
	}
}

else if ($action=="sample_development_details_info_list_view")
{
	if ($data!="")
	{
		$sql= "select id,sample_name,sample_color,working_factory,fabrication,receive_date_from_factory,sent_to_factory_date,sent_to_buyer_date,approval_status,status_date,buyer_meeting_date,recieve_date_from_buyer from sample_development_dtls where sample_mst_id ='$data'  and is_deleted=0 order by id";
	}
	else
	{
		$sql= "select id,sample_name,sample_color,working_factory,fabrication,receive_date_from_factory,sent_to_factory_date,sent_to_buyer_date,approval_status,status_date,buyer_meeting_date,recieve_date_from_buyer from sample_development_dtls where is_deleted=0 order by id";
	}
	$sample_name_arr=return_library_array( "select id, sample_name from lib_sample",'id','sample_name');
	$color_name_arr=return_library_array( "select id, color_name from  lib_color where status_active =1 and is_deleted=0",'id','color_name');
	
	$arr=array (0=>$sample_name_arr,1=>$color_name_arr,7=>$approval_status);
	
	echo  create_list_view ( "list_view1", "Sample Name,Sample Color,Working Factory,Fabrication,Buyer Recieve Date,Sent To factory,Submission to Buyer,Approval Status,Status Date,Buyer Meeting", "100,90,100,100,80,80,80,85,80,80","945","120",0, $sql, "get_php_form_data", "id","'load_php_data_to_form_sample_development_details_info'", 1, "sample_name,sample_color,0,0,0,0,0,approval_status,0,0", $arr , "sample_name,sample_color,working_factory,fabrication,recieve_date_from_buyer,sent_to_factory_date,sent_to_buyer_date,approval_status,status_date,buyer_meeting_date", "../woven_order/requires/sample_development_controller", 'setFilterGrid("list_view1",-1);','0,0,0,0,3,3,3,0,3,3' ) ;	
	 exit();
}

else if ($action=="load_php_data_to_form_sample_development_details_info")
{
	$nameArray=sql_select( "select id,sample_name,sample_color,working_factory,sent_to_factory_date,factory_dead_line,recieve_date_from_buyer,receive_date_from_factory,fabrication,fabric_sorce,sent_to_buyer_date,key_point,approval_status,department,status_date,tf_receive_date,buyer_meeting_date,sample_charge,sample_curency,buyer_req_no,comments,buyer_dead_line from sample_development_dtls where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('cbo_sample_type').value = '".$inf[csf("sample_name")]."';\n"; 
		
		//$color_name=return_field_value('color_name','lib_color','id="'.$inf[csf("sample_color")].'"');
		$color_name=$color_library [$inf[csf("sample_color")]];  
		echo "document.getElementById('txt_sample_color').value  = '".$color_name."';\n"; 
		echo "document.getElementById('txt_working_factory').value  = '".$inf[csf("working_factory")]."';\n"; 
		echo "document.getElementById('txt_sent_to_factory_date').value  = '".change_date_format($inf[csf("sent_to_factory_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('txt_factory_dead_line_date').value  = '".change_date_format($inf[csf("factory_dead_line")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('txt_receive_date_from_buyer').value  = '".change_date_format($inf[csf("recieve_date_from_buyer")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('txt_receive_date_from_factory').value  = '".change_date_format($inf[csf("receive_date_from_factory")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('txt_fabrication').value  = '".$inf[csf("fabrication")]."';\n";
		
		echo "document.getElementById('cbo_fabric_sorce').value  = '".$inf[csf("fabric_sorce")]."';\n";
		
		echo "document.getElementById('txt_sent_to_buyer_date').value  = '".change_date_format($inf[csf("sent_to_buyer_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('txt_key_point').value  = '".$inf[csf("key_point")]."';\n";
		echo "document.getElementById('cbo_approval_status').value  = '".$inf[csf("approval_status")]."';\n";
		echo "document.getElementById('txt_department').value  = '".$inf[csf("department")]."';\n";
		echo "document.getElementById('txt_status_date').value  = '".change_date_format($inf[csf("status_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('txt_tf_receive_date').value  = '".change_date_format($inf[csf("tf_receive_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('txt_buyer_meeting_date').value  = '".change_date_format($inf[csf("buyer_meeting_date")],'dd-mm-yyyy','-')."';\n";
		
		echo "document.getElementById('txt_buyer_request_no').value  = '".$inf[csf("buyer_req_no")]."';\n";

		echo "document.getElementById('txt_comments').value  = '".$inf[csf("comments")]."';\n";
		echo "document.getElementById('txt_buyer_dead_line_date').value  = '".change_date_format($inf[csf("buyer_dead_line")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('txt_sample_charge').value  = '".$inf[csf("sample_charge")]."';\n";
		echo "document.getElementById('cbo_curency').value  = '".$inf[csf("sample_curency")]."';\n";
		echo "document.getElementById('update_id_dtl').value  = '".$inf[csf("id")]."';\n"; 
		
		//echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_sample_development_details_info',2,1);\n"; 
		echo "set_button_status(1, permission, 'fnc_sample_development_details_info',2,1);\n";
		echo "option_disabled('update_mode');\n";
		
	}
	$qry_size="select id, mst_id, dtls_id, size_id, size_qty,bh_qty,remarks from sample_development_size where dtls_id='$data'";
	
	$qry_result=sql_select($qry_size);
	foreach ($qry_result as $row)
	{
		if($id=="") $id=$row[csf("id")]; else $id.="*".$row[csf("id")];
		//if($mst_id=="") $mst_id=$row[csf("mst_id")]; else $mst_id.="*".$row[csf("mst_id")];
		//if($dtls_id=="") $dtls_id=$row[csf("dtls_id")]; else $dtls_id.="*".$row[csf("dtls_id")];
		if($size_id=="") $size_id=$size_arr[$row[csf("size_id")]]; else $size_id.="*".$size_arr[$row[csf("size_id")]];
		if($size_qty=="") $size_qty=$row[csf("size_qty")]; else $size_qty.="*".$row[csf("size_qty")];
		if($bh_qty=="") $bh_qty=$row[csf("bh_qty")]; else $bh_qty.="*".$row[csf("bh_qty")];
		if($remarks=="") $remarks=$row[csf("remarks")]; else $remarks.="*".$row[csf("remarks")];
	}
	//echo "document.getElementById('hidden_mst_id').value 	 				= '".$mst_id."';\n";
	//echo "document.getElementById('hidden_dtls_id').value 	 				= '".$dtls_id."';\n";
	echo "document.getElementById('hidden_size_id').value 	 				= '".$size_id."';\n";
	echo "document.getElementById('hidden_qnty').value 	 					= '".$size_qty."';\n";
	echo "document.getElementById('hidden_bhqnty').value 	 					= '".$bh_qty."';\n";
	echo "document.getElementById('hidden_remarks').value 	 					= '".$remarks."';\n";
	echo "document.getElementById('hidden_tbl_size_id').value 	 			= '".$id."';\n";
	exit();
}

else if($action=="style_id_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample Development Info","../../../", 1, 1, $unicode);
?>
	<script>
		$(document).ready(function(e) {
            $("#txt_search_common").focus();
        });
		function search_populate(str)
		{
			//alert(str); 
			if(str==0) 
			{		
				document.getElementById('search_by_th_up').innerHTML="Enter Style ID";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';		 
			}
			else if(str==1) 
			{
				document.getElementById('search_by_th_up').innerHTML="Enter Style Name";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}																																								
		}
		
		function js_set_value( mst_id )
		{
			document.getElementById('selected_job').value=mst_id;
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
	<div align="center" style="width:100%;" >
	<form name="searchsampledevelopmentfrm_1"  id="searchsampledevelopmentfrm_1" autocomplete="off">
            		<table width="900" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                        <thead>
                        	<th colspan="6">
                              <?=create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" ); ?>
                            </th>
                        </thead>
                        <thead>
                        	<th width="140">Company Name</th>
                            <th width="160">Buyer Name</th>                	 
                            <th width="130">Style ID</th>
                            <th  width="130" >Style Name</th>
                            <th width="200">Est. Ship Date Range</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                        </thead>
        				<tr class="general">
                        	<td width="140"> 
                            	<input type="hidden" id="selected_job">
								<? 
                                    echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'sample_development_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                                ?>
                    		</td>
                   			<td id="buyer_td" width="160">
								 <? 
                                    echo create_drop_down( "cbo_buyer_name", 157, $blank_array,'', 1, "-- Select Buyer --" );
                                ?>	
                            </td>
                            <td width="130">  
								<input type="text" style="width:130px" class="text_boxes"  name="txt_style_id" id="txt_style_id"  />	
                            </td>
                            <td width="130" align="center">				
                                <input type="text" style="width:130px" class="text_boxes"  name="txt_style_name1" id="txt_style_name1"  />			
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                            </td> 
                            <td align="center">
                                <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_id').value+'_'+document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_style_name1').value, 'create_style_id_search_list_view', 'search_div', 'sample_development_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                            </td>
        				</tr>
                        <tr>
                            <td align="center" valign="middle" colspan="6">
                                <?=load_month_buttons(1); ?>
                            </td>
                        </tr>
             		</table>
        <div id="search_div"></div>
    </form>
	</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

else if($action=="create_style_id_search_list_view")
{
	$data=explode('_',$data);
	if ($data[2]!=0) $company=" and company_id='$data[2]'"; else { echo "Please Select Company First."; die; }
	if ($data[3]!=0) $buyer=" and buyer_name='$data[3]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($data[0]==1)
		{
		   if (trim($data[1])!="") $style_id_cond=" and id='$data[1]'"; else $style_id_cond="";
		   if ($data[6]!="") $style_cond=" and style_ref_no='$data[6]'"; else $style_cond="";
		}
	
	if($data[0]==4 || $data[0]==0)
		{
		  if (trim($data[1])!="") $style_id_cond=" and id like '%$data[1]%' "; else $style_id_cond="";
		  if ($data[6]!="") $style_cond=" and style_ref_no like '%$data[6]%' "; else $style_cond="";
		}
	
	if($data[0]==2)
		{
		  if (trim($data[1])!="") $style_id_cond=" and id like '$data[1]%' "; else $style_id_cond="";
		  if ($data[6]!="") $style_cond=" and style_ref_no like '$data[6]%' "; else $style_cond="";
		}
	
	if($data[0]==3)
		{
		  if (trim($data[1])!="") $style_id_cond=" and id like '%$data[1]' "; else $style_id_cond="";
		  if ($data[6]!="") $style_cond=" and style_ref_no like '%$data[6]' "; else $style_cond="";
		}
	
	
	if($db_type==0)
	{
	if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and estimated_shipdate  between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'"; else $estimated_shipdate ="";
	}
	if($db_type==2)
	{
	if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and estimated_shipdate  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $estimated_shipdate ="";
	}
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	$dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$team_leader=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');
	$season_name_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
	
	$arr=array (1=>$comp,2=>$buyer_arr,4=>$product_dept,5=>$team_leader,6=>$dealing_marchant,8=>$season_name_arr);
	$sql="";
	/*if (trim($data[1])!="")//Search By
	{
		if(trim($data[0])==0)//Style ID
		{
	 	 	$sql= "select id,company_id,buyer_name,style_ref_no,product_dept,team_leader,dealing_marchant,article_no from sample_development_mst where id='$data[1]' and status_active=1 and is_deleted=0 $company $buyer $estimated_shipdate order by id"; 
			
		 	echo  create_list_view("list_view", "Style Id,Company,Buyer Name,Style Name,Product Department,Team Leader,Dealing Merchant,Article Number", "60,140,140,100,90,90,90,80","900","240",0, $sql , "js_set_value", "id", "", 1, "0,company_id,buyer_name,0,product_dept,team_leader,dealing_marchant,0", $arr , "id,company_id,buyer_name,style_ref_no,product_dept,team_leader,dealing_marchant,article_no", "",'','0,0,0,0,0,0,0,0') ;
		}
		else if(trim($data[0])==1)//Style Name
		{
	 */	 	$sql= "select id,company_id,buyer_name,style_ref_no,product_dept,team_leader,dealing_marchant,article_no,season_buyer_wise from sample_development_mst where status_active=1 and is_deleted=0 $company $buyer $style_id_cond $style_cond $estimated_shipdate order by id";
			//echo $sql;
		 	echo  create_list_view("list_view", "Style Id,Company,Buyer Name,Style Name,Product Department,Team Leader,Dealing Merchant,Article Number,Season", "60,140,140,100,90,90,90,70,100","900","240",0, $sql , "js_set_value", "id", "", 1, "0,company_id,buyer_name,0,product_dept,team_leader,dealing_marchant,0,season_buyer_wise", $arr , "id,company_id,buyer_name,style_ref_no,product_dept,team_leader,dealing_marchant,article_no,season_buyer_wise", "",'','0,0,0,0,0,0,0,0,0') ;
/*		}
	}
	else
	{
		$sql= "select id,company_id,buyer_name,style_ref_no,product_dept,team_leader,dealing_marchant,article_no from sample_development_mst where status_active=1 and is_deleted=0 $company $buyer $estimated_shipdate order by id";
			
		 	echo  create_list_view("list_view", "Style Id,Company,Buyer Name,Style Name,Product Department,Team Leader,Dealing Merchant,Article Number", "60,140,140,100,90,90,90,80","900","240",0, $sql , "js_set_value", "id", "", 1, "0,company_id,buyer_name,0,product_dept,team_leader,dealing_marchant,0", $arr , "id,company_id,buyer_name,style_ref_no,product_dept,team_leader,dealing_marchant,article_no", "",'','0,0,0,0,0,0,0,0') ;
	}*/
	exit();
}

else if($action=="populate_data_from_search_popup")
{
	//$dataArr = explode("**",$data);
	//$po_id = $dataArr[0];
	//$item_id = $dataArr[1];
	$res = sql_select("select *
			from sample_development_mst
			where id=$data"); 
	
 	foreach($res as $result)
	{
		echo "load_drop_down( 'requires/sample_development_controller', '".$result[csf("company_id")]."', 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/sample_development_controller', '".$result[csf("company_id")]."', 'load_drop_down_agent', 'agent_td' );load_drop_down( 'requires/sample_development_controller', '".$result[csf("season_buyer_wise")]."', 'load_drop_down_season_com', 'season_td');\n";
		
		echo "load_drop_down( 'requires/sample_development_controller','".$result[csf("season_buyer_wise")]."', 'only_for_all_season', 'season_td');\n";
		
		echo "load_drop_down( 'requires/sample_development_controller', '".$result[csf("team_leader")]."', 'cbo_dealing_merchant', 'div_marchant' ) ;\n";
		echo "color_from_library('".$result[csf("company_id")]."');\n";
		echo "load_drop_down( 'requires/sample_development_controller', '".$result[csf("buyer_name")]."', 'cbo_sample_type', 'sampletd' ) ;\n";
		//load_drop_down( 'requires/sample_development_controller', this.value, 'cbo_sample_type', 'sampletd' )
		
		echo "$('#txt_style_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_company_name').val('".$result[csf('company_id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "document.getElementById('txt_quotation_id').value = '".$result[csf("quotation_id")]."';\n";
		echo "$('#txt_style_name').val('".$result[csf('style_ref_no')]."');\n";
		echo "$('#cbo_product_department').val('".$result[csf('product_dept')]."');\n";
		echo "$('#txt_article_no').val('".$result[csf('article_no')]."');\n";
		echo "$('#cbo_item_name').val('".$result[csf('item_name')]."');\n";
		echo "$('#txt_item_catgory').val('".$result[csf('item_category')]."');\n";
		echo "$('#cbo_region').val('".$result[csf('region')]."');\n";
		echo "$('#cbo_agent').val('".$result[csf('agent_name')]."');\n";
		echo "$('#cbo_team_leader').val('".$result[csf('team_leader')]."');\n";
		echo "$('#cbo_dealing_merchant').val('".$result[csf('dealing_marchant')]."');\n";
		echo "$('#txt_product_code').val('".$result[csf('product_code')]."');\n";
		echo "$('#txt_bhmerchant').val('".$result[csf('bh_merchant')]."');\n";
		
		echo "$('#txt_est_ship_date').val('".change_date_format($result[csf('estimated_shipdate')],'dd-mm-yyyy','-')."');\n";
		echo "$('#txt_remarks').val('".$result[csf('remarks')]."');\n";
		echo "$('#cbo_season_name').val('".$result[csf('season_buyer_wise')]."');\n";
		//echo "$('#update_id').val('".$result[csf('id')]."');\n";
		
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_sample_development_mst_info',1);\n"; 
		//$set_qty = return_field_value("set_item_ratio","wo_po_details_mas_set_details","job_no='".$result[csf('job_no')]."' and gmts_item_id='$item_id'");
		//$total_produced = return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and production_type=1 and is_deleted=0");
		//$yet_to_produced = $result[csf('plan_cut')]*$set_qty - $total_produced;
		//echo "$('#txt_yet_cut').val('".$yet_to_produced."');\n";
  	}
 	exit();	
}	

else if ($action=="marchant_team_info_list_view")
{
		 
		 	 $arr=array (5=>$row_status);
			//$sql= "select a.id,a.team_name,a.team_leader_name,a.status_active,count(b.team_id) as team from lib_marketing_team a, lib_mkt_team_member_info b  where a.id=b.team_id and a.is_deleted=0 and b.is_deleted=0 group by a.id, a.team_name, a.team_leader_name order by a.id";
			 $sql= "select team_name,team_leader_name,team_leader_desig,team_leader_email,total_member,status_active,id from lib_marketing_team where is_deleted=0 order by team_name";
			 echo  create_list_view ( "list_view", "Team Name,Team Leader Name,Designation,Email,Total Member,Status", "150,200,100,150,55","800","220",0, $sql, "get_php_form_data", "id","'load_php_data_to_form'", 1, "0,0,0,0,0,status_active", $arr , "team_name,team_leader_name,team_leader_desig,team_leader_email,total_member,status_active", "../merchandising_details/requires/marchant_team_info_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,1,0' ) ;
exit();
}
//second list view

else if ($action=="load_php_data_to_form")
{
$nameArray=sql_select( "select id,team_name,team_leader_name,team_leader_desig,team_leader_email,status_active,lib_mkt_team_member_info_id from lib_marketing_team  where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('txt_team_name').value  = '".$inf[csf("team_name")]."';\n";    
		echo "document.getElementById('txt_team_leader_name').value  = '".$inf[csf("team_leader_name")]."';\n"; 
		echo "document.getElementById('txt_team_leader_desig').value  = '".$inf[csf("team_leader_desig")]."';\n";
		echo "document.getElementById('txt_team_leader_email').value  = '".$inf[csf("team_leader_email")]."';\n"; 
		echo "document.getElementById('cbo_team_status').value  = '".$inf[csf("status_active")]."';\n";  
		echo "document.getElementById('update_id').value  = '".$inf[csf("id")]."';\n"; 
		echo "document.getElementById('id_lib_mkt_team_member_info').value  = '".$inf[csf("lib_mkt_team_member_info_id")]."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_marchant_team_info',1);\n"; 
		echo "show_list_view('".$inf[csf("id")]."', 'marchant_team_info_det_list_view', 'member_list_view', '../merchandising_details/requires/marchant_team_info_controller', 'setFilterGrid(\'list_view1\',-1)');\n";  
	}
	exit();
}

else if ($action=="sample_development_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
?>
	<div style="width:850px;">
    <table width="820" cellspacing="0" align="right">
        <tr>
            <td colspan="2" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
            <td colspan="2" align="center">
				<?
				//echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
					
						 echo $result[csf('plot_no')].', '.$result[csf('level_no')].', '.$result[csf('road_no')].', '.$result[csf('block_no')].', '.$result[csf('city')].', '.$result[csf('zip_code')].', '.$result[csf('province')].', '.$country_arr[$result[csf('country_id')]]; ?><br> 
						Email Address: <? echo $result[csf('email')];?> 
						Website No: <? echo $result[csf('website')];
					}
                ?> 
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center" style="font-size:medium"><strong><u>SAMPLE REQUEST FORM</u></strong></td>
        </tr>
        <tr> <td colspan="2">&nbsp;</td></tr>
        <? 
	 $sql="select a.id,a.buyer_name,a.style_ref_no,a.season_buyer_wise,a.item_name,b.sample_name,b.sent_to_factory_date,b.factory_dead_line,b.sent_to_buyer_date,b.fabrication from sample_development_mst a,sample_development_dtls b where  b.id='$data[1]' and  a.id=b.sample_mst_id";
	//echo $sql;
	$dataArray=sql_select($sql);
	foreach ($dataArray as $row)
	{ 
	?>
        <tr>
        	<td width="400" align="left" valign="top"> 
            	<table align="left" cellspacing="0" width="380"  border="1" rules="all" class="rpt_table" >
                	<tr>
                    	<td width="180">Sample Request Date</td>
                        <td width="120"><? echo change_date_format($row[csf('sent_to_factory_date')],"dd-mm-yyyy","-"); ?></td>
                    </tr>
                	<tr>
                    	<td width="180">Sample Expected Date</td>
                        <td width="120"><? echo change_date_format($row[csf('factory_dead_line')],"dd-mm-yyyy","-"); ?></td>
                    </tr>
                	<tr>
                    	<td width="180">Submission Date</td>
                        <td width="120"><? echo change_date_format($row[csf('sent_to_buyer_date')],"dd-mm-yyyy","-"); ?></td>
                    </tr>
                	<tr>
                    	<td width="180">&nbsp;</td>
                        <td width="120">&nbsp;</td>
                    </tr>
                	<tr>
                    	<td width="180">&nbsp;</td>
                        <td width="120">&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td align="right" width="380" rowspan="3" valign="top">
            	<table align="right" cellspacing="0" width="380"  border="1" rules="all" class="rpt_table" >
                	<tr>
                    	<td width="180">Quotation</td>
                        <td width="120">&nbsp;</td>
                    </tr>
                	<tr>
                    	<td width="180">Development</td>
                        <td width="120" align="center"><? if ($row[csf('sample_name')]==6) echo "√"; ?></td>
                    </tr>
                	<tr>
                    	<td width="180">Tailoring Sample</td>
                        <td width="110" align="center">&nbsp;</td>
                    </tr>
                	<tr>
                    	<td width="180">Photo</td>
                        <td width="110" align="center">&nbsp;</td>
                    </tr>
                	<tr>
                    	<td width="180">Proto</td>
                        <td width="110" align="center">&nbsp;</td>
                    </tr>
                	<tr>
                    	<td width="180">Approvel</td>
                        <td width="110" align="center">&nbsp;</td>
                    </tr>
                	<tr>
                    	<td width="180">Seal</td>
                        <td width="110" align="center">&nbsp;</td>
                    </tr>
                	<tr>
                    	<td width="180">Fit</td>
                        <td width="110" align="center"><? if ($row[csf('sample_name')]==7) echo "√"; ?></td>
                    </tr>
                	<tr>
                    	<td width="180">SMS</td>
                        <td width="110" align="center">&nbsp;</td>
                    </tr>
                	<tr>
                    	<td width="180">Test</td>
                        <td width="110" align="center">&nbsp;</td>
                    </tr>
                	<tr>
                    	<td width="180">Siz Set</td>
                        <td width="110" align="center"><? if ($row[csf('sample_name')]==8) echo "√"; ?></td>
                    </tr>
                	<tr>
                    	<td width="180">Production-FOP</td>
                        <td width="110" align="center">&nbsp;</td>
                    </tr>
                	<tr>
                    	<td width="180">Color</td>
                        <td width="110" align="center">&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr> <td colspan="2">&nbsp;</td></tr>
        <tr>
        	<td width="400" align="left" valign="top">
            	<table align="left" cellspacing="0" width="380"  border="1" rules="all" class="rpt_table" >
                	<tr>
                    	<td width="180">Buyer Name</td>
                        <td width="120"><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
                    </tr>
                	<tr>
                    	<td width="180">Style Number</td>
                        <td width="120"><? echo $row[csf('style_ref_no')]; ?></td>
                    </tr>
                	<tr>
                    	<td width="180">Season</td>
                        <td width="120"><? echo $row[csf('season_buyer_wise')]; ?></td>
                    </tr>
                	<tr>
                    	<td width="180">Description</td>
                        <td width="120"><? echo $garments_item[$row[csf('item_name')]]; ?></td>
                    </tr>
                	<tr>
                    	<td width="180">Fabric Type</td>
                        <td width="120"><? echo $row[csf('fabrication')]; ?></td>
                    </tr>
                	<tr>
                    	<td width="180">Previous VER</td>
                        <td width="120">&nbsp;</td>
                    </tr>
                	<tr>
                    	<td width="180">Present VER</td>
                        <td width="120">&nbsp;</td>
                    </tr>
                	<tr>
                    	<td width="180">Teck Pack Date</td>
                        <td width="120">&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr> <td colspan="2">&nbsp;</td></tr>
        <tr>
        	<td width="400" align="left" valign="top" colspan="2">
            	<table align="left" cellspacing="0" width="380" class="rpt_table" >
                	<tr>
                    	<td width="180">Buyer Pattern</td>
                        <td width="30">YES</td>
                        <td width="30" style="border:solid 1px">&nbsp;</td>
                        <td width="30">NO</td>
                        <td width="30" style="border:solid 1px">&nbsp;</td>
                    </tr>
                	<tr>
                    	<td width="180">Reference Sample</td>
                        <td width="30">YES</td>
                        <td width="30" style="border:solid 1px">&nbsp;</td>
                        <td width="30">NO</td>
                        <td width="30" style="border:solid 1px">&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr> <td colspan="2">&nbsp;</td></tr>
        <tr>
        	<td width="550" align="left" valign="top" colspan="2">
            	<table align="left" cellspacing="0" border="1" width="380" class="rpt_table" >
                    <tbody>
                        <tr>
                            <td width="150" align="center">Size Required</td>
                    <?
						$sql_qry="Select id, dtls_id, size_id, size_qty,bh_qty from sample_development_size where dtls_id=$data[1] ";
						//echo $sql_qry;
						$result=sql_select($sql_qry);
						$i=1; $total_gmts_pcs=0;$total_bhqty=0;
						foreach($result as $row)
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							?>
                                <td align="right" width="60"><? echo $size_arr[$row[csf('size_id')]]; ?></td>
						<?	
						$i++;	
						}
						?>
                        	<td width="60" align="center">Total</td>
                        </tr>
                        <tr>
                            <td width="100" align="center">No Of PCS</td>
                        <?
						foreach($result as $row)
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							?>
                                <td align="right"><? echo $row[csf('size_qty')]; $total_gmts_pcs+=$row[csf('size_qty')]; ?>&nbsp;</td>
						<?	
						$i++;	
						}
                    ?>
                        	<td align="right"><? echo $total_gmts_pcs; ?>&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="100" align="center">BH Qty</td>
                        <?
						foreach($result as $row)
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							?>
                                <td align="right"><? echo $row[csf('bh_qty')]; $total_bh_qty+=$row[csf('bh_qty')]; ?>&nbsp;</td>
						<?	
						$i++;	
						}
                    ?>
                        	<td align="right"><? echo $total_bh_qty; ?>&nbsp;</td>
                        </tr>
                    </tbody>
                
                
<!--                	<tr>
                    	<td width="180">Size Required</td>
                        <td width="30" style="border:solid 1px" align="center">S</td>
                        <td width="30" style="border:solid 1px" align="center">M</td>
                        <td width="30" style="border:solid 1px" align="center">L</td>
                        <td width="30" style="border:solid 1px" align="center">XL</td>
                        <td width="30" style="border:solid 1px" align="center">XXL</td>
                        <td width="30" style="border:solid 1px" align="center">XXXL</td>
                    </tr>
                	<tr>
                    	<td width="180">No Of PCS</td>
                        <td width="30" style="border:solid 1px">&nbsp;</td>
                        <td width="30" style="border:solid 1px">&nbsp;</td>
                        <td width="30" style="border:solid 1px">&nbsp;</td>
                        <td width="30" style="border:solid 1px">&nbsp;</td>
                        <td width="30" style="border:solid 1px">&nbsp;</td>
                        <td width="30" style="border:solid 1px">&nbsp;</td>
                    </tr>
                	<tr>
                    	<td width="180">&nbsp;</td>
                        <td width="30" style="border:solid 1px">&nbsp;</td>
                        <td width="30" style="border:solid 1px">&nbsp;</td>
                        <td width="30" style="border:solid 1px">&nbsp;</td>
                        <td width="30" style="border:solid 1px">&nbsp;</td>
                        <td width="30" style="border:solid 1px">&nbsp;</td>
                        <td width="30" style="border:solid 1px">&nbsp;</td>
                    </tr>
-->                </table>
            </td>
        </tr>
        <tr> <td colspan="2">&nbsp;</td></tr>
        <tr>
        	<td width="400" align="left" valign="top" >
            	<table align="left" cellspacing="0" width="380" class="rpt_table" >
                	<tr>
                    	<td width="180">Fabric In House</td>
                        <td width="30">YES</td>
                        <td width="30" style="border:solid 1px">&nbsp;</td>
                        <td width="30">NO</td>
                        <td width="30" style="border:solid 1px">&nbsp;</td>
                    </tr>
                	<tr>
                    	<td width="180">Thread In House</td>
                        <td width="30">YES</td>
                        <td width="30" style="border:solid 1px">&nbsp;</td>
                        <td width="30">NO</td>
                        <td width="30" style="border:solid 1px">&nbsp;</td>
                    </tr>
                	<tr>
                    	<td width="180">Trims In House</td>
                        <td width="30">YES</td>
                        <td width="30" style="border:solid 1px">&nbsp;</td>
                        <td width="30">NO</td>
                        <td width="30" style="border:solid 1px">&nbsp;</td>
                    </tr>
                	<tr>
                    	<td width="180">Fabrics Available</td>
                        <td width="30">YES</td>
                        <td width="30" style="border:solid 1px">&nbsp;</td>
                        <td width="30">NO</td>
                        <td width="30" style="border:solid 1px">&nbsp;</td>
                    </tr>
                </table>
            </td>
        	<td width="400" align="left" valign="top" >
            	<table align="left" cellspacing="0" width="380" class="rpt_table" border="1" rules="all">
                	<tr>
                    	<td width="180" >GSM</td>
                        <td width="120" >&nbsp;</td>
                    </tr>
                	<tr>
                    	<td width="180" >S/Thread Count</td>
                        <td width="120" >&nbsp;</td>
                    </tr>
                	<tr>
                    	<td width="180" >Fab Yarn Count</td>
                        <td width="120" >&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr> <td colspan="2">&nbsp;</td></tr>
        <tr>
        	<td width="810" align="left" valign="top" colspan="2" >
            	<table align="left" cellspacing="0" width="800" class="rpt_table" >
                	<tr>
                    	<td width="200" >Fabric Swatch</td>
                        <td width="200" >Thread Ref</td>
                    	<td width="200" >Lables</td>
                        <td width="200" >Button/Rivet</td>
                    </tr>
                    <tr>
                    	<td width="200" style="border:solid 1px" height="150" >&nbsp;</td>
                    	<td width="200" style="border:solid 1px" height="150" >&nbsp;</td>
                    	<td width="200" style="border:solid 1px" height="150" >&nbsp;</td>
                    	<td width="200" style="border:solid 1px" height="150" >&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr> <td colspan="2">&nbsp;</td></tr>
        <tr>
        	<td width="810" align="left" valign="top" colspan="2" >
            	<table align="left" cellspacing="0" width="800" class="rpt_table" border="1" rules="all" >
                	<tr>
                    	<td width="150" >Pattern Maker </td>
                        <td width="250" >&nbsp;</td>
                    	<td width="200" >Date :</td>
                        <td width="200" >&nbsp;</td>
                    </tr>
                	<tr>
                    	<td width="150" >Cutter </td>
                        <td width="250" >&nbsp;</td>
                    	<td width="200" >Date :</td>
                        <td width="200" >&nbsp;</td>
                    </tr>
                	<tr>
                    	<td width="150" >Sample Group </td>
                        <td width="250" >&nbsp;</td>
                    	<td width="200" >Date :</td>
                        <td width="200" >&nbsp;</td>
                    </tr>
                	<tr>
                    	<td width="150" >Quality Assurence </td>
                        <td width="250" >&nbsp;</td>
                    	<td width="200" >Date :</td>
                        <td width="200" >&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr> <td colspan="2">&nbsp;</td></tr>
        <? } ?>
        <tr>
        	<td width="810" align="left" valign="top" colspan="2" >
            	<table align="left" cellspacing="0" width="800" class="rpt_table" >
                	<tr>
                    	<td width="260" >Merchant Sign</td>
                        <td width="6">&nbsp;</td>
                        <td width="260" >Tech Manager Sign</td>
                        <td width="6">&nbsp;</td>
                    	<td width="260" >Pattern Storage Area</td>
                    </tr>
                    <tr>
                    	<td width="260" style="border:solid 1px" height="50" >&nbsp;</td>
                        <td width="6">&nbsp;</td>
                    	<td width="260" style="border:solid 1px" height="50" >&nbsp;</td>
                        <td width="6">&nbsp;</td>
                    	<td width="260" style="border:solid 1px" height="50" >&nbsp;</td>
                    </tr>
                </table>
            </td>
        <tr>
        	<td width="810" align="left" valign="top" colspan="2" >
            	<table align="left" cellspacing="0" width="810" class="rpt_table" >
                	<tr>
                    	<td colspan="5">
							<?
                             echo signature_table(86, $data[0], "810px");
                            ?>
                        </td>
                        
                    </tr>
                    
                </table>
            </td>
        </tr>
	</table>
   
    </div>
<?
exit();
}

if($action=="sample_development_request_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$dealing_merchant_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
	$sample_library=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name");
	$size_library=return_library_array( "select id, size_name from lib_size where status_active =1 and is_deleted=0", "id", "size_name"  );
	$color_library=return_library_array( "select id, color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name"  );



	//select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name
?>
	<div style="width:1000px;">
    
     <table width="1000" cellspacing="0" border="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
            <td colspan="6" align="center">
				<?
				//echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
											 
						 echo $result[csf('plot_no')].', '.$result[csf('level_no')].', '.$result[csf('road_no')].', '.$result[csf('block_no')].', '.$result[csf('city')].', '.$result[csf('zip_code')].', '.$result[csf('province')].', '.$country_arr[$result[csf('country_id')]]; ?><br> 
						 <? echo $result[csf('email')];?> 
						 <? echo $result[csf('website')];
					}
					
					  $sql="select a.id,a.buyer_name,a.style_ref_no,a.season_buyer_wise,a.item_name,a.article_no,a.estimated_shipdate,a.dealing_marchant,a.season_buyer_wise,a.remarks from sample_development_mst a where  a.id='$data[2]' ";
	//echo $sql;
	$dataArray=sql_select($sql);
	
	
	$sql_qry_date_arr="Select min(sent_to_factory_date) as request_date,min(factory_dead_line) as factory_dead_line from sample_development_dtls where sample_mst_id=$data[2] ";
						//echo $sql_qry;
						$result_data=sql_select($sql_qry_date_arr);
						$request_date=$result_data[0][csf('request_date')];
						$expected_date=$result_data[0][csf('factory_dead_line')];
                ?> 
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:medium"><strong><u>SAMPLE REQUEST FORM</u></strong></td>
        </tr>
        <tr> <td colspan="6">&nbsp;</td></tr>
         <tr>
        	<td width="250" align="left" valign="top" colspan="2">
            	<table align="left" cellspacing="0" border="0" width="90%" >
                <tr>
        	<td width="100"><strong>Buyer Name </strong></td> <td width="100" align="left">:&nbsp<? echo $buyer_library[$dataArray[0][csf('buyer_name')]];?></td> <td width="100" align="left"><strong>Style Id</strong></td> <td width="100">:&nbsp<? echo $dataArray[0][csf('id')];?></td><td width="100" align="left"><strong>Request Date</strong></td> <td width="100">:&nbsp<? echo change_date_format($request_date);?></td>
        </tr>
        <tr>
           <td width="100"><strong>Style Name</strong></td> <td width="100">:&nbsp<? echo $dataArray[0][csf('style_ref_no')];?></td> <td width="100" align="left"><strong>Season</strong></td> <td width="100">:&nbsp<? echo $season_arr[$dataArray[0][csf('season_buyer_wise')]];?></td>
           <td width="120" align="left"><strong>Expected Date</strong></td> <td width="100" align="left">:&nbsp<? echo change_date_format($expected_date);?></td>
        </tr>
        <tr>
        	 <td width="100"><strong>Gmts. Item</strong></td> <td width="100">:&nbsp<? echo $garments_item[$dataArray[0][csf('item_name')]];?></td> 
             <td width="100" align="left"><strong>D.Merchant</strong></td> <td width="100">:&nbsp<? echo $dealing_merchant_library[$dataArray[0][csf('dealing_marchant')]];?></td>
             <td width="100" align="left"><strong>Est.Ship Date</strong></td> <td width="100">:&nbsp<? echo change_date_format($dataArray[0][csf('estimated_shipdate')]);?></td>
        </tr>
        <tr>
       		 <td width="100"><strong>Remarks/Desc.</strong></td> <td colspan="5">:<? echo $dataArray[0][csf('remarks')];?></td>
        </tr>
        </table>
        </td>
        </tr>
        
         <tr> <td colspan="6">&nbsp;</td></tr>
        <tr>
        	<td width="350" align="left" valign="top" colspan="2">
            	<table align="left" cellspacing="0" border="1" width="100%" class="rpt_table" rules="all">
                    <tbody>
                        <tr>
                            <td width="150" colspan="9" align="center"><strong>Sample Information</td>
                        </tr>
                        <tr>
                           <th width="30" align="center">SL</th>
                           <th width="100" align="left">Sample Name</th> 
                           <th width="120" align="left">Fabric Details</th>
                           <th width="70" align="left">Fabric Source</th>
                           <th width="80" align="left">Color</th>
                           <th width="80" align="center">Size</th>
                           <th width="40" align="center">Sample Qty</th>
                            <th width="40" align="center">BH Qty</th>
						  <th width="100" align="center">Remarks</th>
                           <th width="70" align="center">Delivery Date</th>
						   <th width="70" align="center">Sent To Smpl Dept.</th>
                           <th width="120" align="center">Buyer Request No</th>
                           <th width="80" align="center">Comments</th>
                        </tr>
                        
                        <?
							$sample_color_arr=return_library_array( "select id, sample_color from sample_development_dtls", "id", "sample_color"  );
//LEFT JOIN pro_ex_factory_mst d on  c.po_break_down_id=d.po_break_down_id //sample_development_size
                      $sql_qry="Select a.id as dtls_id,a.sample_name,a.sample_color,b.size_id, sum(b.size_qty) as size_qty,sum(b.bh_qty) as bh_qty,a.fabrication,a.fabric_sorce,a.comments,a.comments,a.factory_dead_line,a.sent_to_factory_date,a.buyer_req_no,b.remarks from sample_development_dtls a LEFT JOIN sample_development_size b on  a.sample_mst_id=mst_id and a.id=b.dtls_id and b.is_deleted=0  where a.is_deleted=0 and a.sample_mst_id=$data[2] group by a.id,a.sample_name,a.fabrication,a.fabric_sorce,a.factory_dead_line,a.comments,a.buyer_req_no,a.sample_color,b.size_id,a.sent_to_factory_date,b.remarks order by a.id";
					  //a.sample_color,b.size_id
                      
					//echo $sql_qry;  
					  
					  
					  
					  
					  
						$result=sql_select($sql_qry);
 						$sample_data_size_arr=array();
						foreach($result as $row)
						{
							$sample_data_size_arr[$row[csf('sample_name')]][$row[csf('sample_color')]][$row[csf('size_id')]]['qty']=$row[csf('size_qty')];
						}
						$sample_id_array=array();
						$i=1; $total_gmts_pcs=0;$total_bhqty=0;$k=0;
						foreach($result as $row)
						{
							$size_qty=$row[csf('size_qty')];
							$bh_qty=$row[csf('bh_qty')];
						?>
                        <tr>
                            <?
                            //if (!in_array($row[csf('sample_color')].$row[csf('sample_name')],$sample_id_array) )
							//{ 
							$k++;
							?>
                            <td  align="center"><? echo $k;?></td>
                            <td  align="left"><? echo $sample_library[$row[csf('sample_name')]];?></td>
                            <td  align="left"><? echo $row[csf('fabrication')];?></td>
                            <td  align="left"><? echo $fabric_source[$row[csf('fabric_sorce')]];?></td>
                            <td  align="left"><? echo $color_library[$row[csf('sample_color')]];?></td>
                            <?
							//$sample_id_array[]=$row[csf('sample_color')].$row[csf('sample_name')];
							//}
							//else
							//{ ?>
                            
                           <!-- <td  align="center"><? //echo $i;?></td>
                            <td><? //echo $sample_library[$row[csf('sample_name')]];?></td>
                            <td  align="center"><? //echo $sample_library[$row[csf('sample_name')]];?></td>
                            <td  align="center"><? //echo $sample_library[$row[csf('sample_name')]];?></td>
                            <td  align="center"><? //echo $color_library[$row[csf('comments')]];?></td>-->
                            <?
							//}
						?>
                        
							
                            <td  align="center"><? echo $size_library[$row[csf('size_id')]];?></td>
                            <td  align="center"><? echo number_format($size_qty);?></td>
                             <td  align="center"><? echo number_format($bh_qty);?></td>
							 <td  align="center"><? echo $row[csf('remarks')];?></td>
                            <td  align="center"><? echo change_date_format($row[csf('factory_dead_line')]);?></td>
							<td  align="center"><? echo change_date_format($row[csf('sent_to_factory_date')]);?></td>
                            <td  align="center"><? echo $row[csf('buyer_req_no')];?></td>
                            <td  align="center"><? echo $row[csf('comments')];?></td>
                            
                        </tr>
                        <?
							
						$i++;
						$total_gmts_pcs+=$size_qty;
						$total_bhqty+=$bh_qty;
						}
						?>
                    </tbody>
                    <tfoot>
                    <tr>
                    <td align="right" colspan="6"><strong>Total </strong></td> <td align="center"><? echo $total_gmts_pcs;?> </td>
                    <td align="center"><? echo $total_bhqty; ?> </td>
                    <td>&nbsp;</td><td>&nbsp;</td>
                    </tr>
                    </tfoot>
               </table>
             </td>
        </tr>
         <tr> <td colspan="6">&nbsp;</td></tr>
        	  
             <tr>
        	<td width="250" align="left" valign="top" colspan="6">
             <table align="left" cellspacing="0" border="0" width="100%" >
                <tr>
                <td>
                	 <table align="left" cellspacing="0" border="0" width="100%" >
                     	<tr>
                            <td width="100">Buyer Pattern</td> <td width="5">:</td>
                            <td width="10">YES</td>
                            <td width="10"  style="border:solid 1px;"> </td>
                            <td width="10">&nbsp;&nbsp;NO</td>
                            <td width="20"  style="border:solid 1px;"> </td>
                            
                            <td width="50">&nbsp;&nbsp;Print</td> <td width="5">:</td>
                            <td width="10">YES</td>
                            <td width="10"  style="border:solid 1px"> </td>
                            <td width="10">&nbsp;&nbsp;NO</td>
                            <td style="border:solid 1px"> </td>
                            
                            <td rowspan="6"  style="border:solid 0px; margin-left:5px;" width="40%" align="center">
							 <?
                            $data_array_img=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[2]' and form_name='sample_development' and is_deleted=0 and file_type=1");
                            foreach($data_array_img as $img_row)
                            {
                                ?>
                                <img src='../../<? echo $img_row[csf('image_location')]; ?>' height='130' width='180' style="border:solid;"  align="middle" />	
                                <? 
                            }
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="100">Ref. Sample</td> <td width="5">:</td>
                            <td width="10">YES</td>
                            <td width="10"  style="border:solid 1px"> </td>
                            <td width="10">&nbsp;&nbsp;NO</td>
                            <td width="20"  style="border:solid 1px"> </td>
                            
                            <td width="50">&nbsp;&nbsp;Embroidery</td> <td width="5">:</td>
                            <td width="10">YES</td>
                            <td width="10"  style="border:solid 1px"> </td>
                            <td width="10">&nbsp;&nbsp;NO</td>
                            <td width="20"  style="border:solid 1px"> </td>
                        </tr>
                        <tr>
                            <td  width="100">Fabric In House</td> <td width="5">:</td>
                            <td width="10">YES</td>
                			<td width="10"  style="border:solid 1px"> </td>
                            <td width="20">&nbsp;&nbsp;NO</td>
                            <td width="10"  style="border:solid 1px"> </td>
                            <td width="50">&nbsp;&nbsp;Gmts Wash</td> <td width="5">:</td>
                            <td width="10">YES</td>
                            <td width="10"  style="border:solid 1px"> </td>
                            <td width="10">&nbsp;&nbsp;NO</td>
                            <td width="20"  style="border:solid 1px"> </td>
                        </tr>
                         <tr>
                        <tr>
                            <td  width="100">Thread In House</td> <td width="5">:</td>
                            <td width="6">YES</td>
                            <td width="10"  style="border:solid 1px"> </td>
                            <td width="10">&nbsp;&nbsp;NO</td>
                            <td width="10"  style="border:solid 1px"> </td>
                            <td  width="50"></td> <td width="5"></td>
                            <td width="10"></td><td width="10"> </td>
                            <td width="10"></td>
                		</tr>
                         <tr>
                            <td  width="100">Trims In House</td> <td width="5">:</td>
                            <td width="10">YES</td>
                			<td width="10"  style="border:solid 1px"> </td>
                            <td width="10">&nbsp;&nbsp;NO</td>
                            <td width="20"  style="border:solid 1px"> </td>
                            <td width="50"></td> <td width="5"></td>
                            <td width="10"></td><td width="10"> </td>
                            <td width="10"></td>
                		</tr>
                       
                     </table>
                </td>
                
                </tr>
                
                </table>
                </td>
                </tr>
                
                 <tr> <td colspan="6">&nbsp;</td></tr>
        	  <tr>
        	<td width="250" align="left" valign="top" colspan="6">
            	<table align="left" cellspacing="0" border="2" width="100%" class="rpt_table" rules="all">
                <tr>
                <th>Fabric Swatch</th> <th>Thread Ref.</th> <th>Label </th> <th>Button/Rivet</th>
                </tr>
                <tr height="150">
                <td width="200">&nbsp; </td>  <td width="200">&nbsp; </td>  <td width="200">&nbsp; </td>  <td width="200">&nbsp; </td>
                </tr>
                
                </table>
                </td>
                </tr>
                <tr>
        	<td width="700" align="left" valign="top" colspan="6" >
            	<table align="left" cellspacing="0" width="900" class="rpt_table" >
                	<tr>
                    	<td colspan="5">
							<?
                             echo signature_table(86, $data[0], "900px");
                            ?>
                        </td>
                        
                    </tr>
                    
                </table>
            </td>
        </tr>
        
        </table>
    
    </div>
    
 <?
}
    

else if ($action=="sizeinfo_popup")
{
	echo load_html_head_contents("Sample Development Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data_all=explode('_',$data);
	//var_dump($data_all);
	$size_id=$data_all[0];
	$size_qty=$data_all[1];
	$id_up=$data_all[2];
	$bh_qty=$data_all[3];
	$remarks=$data_all[4];
	
	$size_id_all=explode('*',$size_id);
	$size_qty_all=explode('*',$size_qty);
	$bh_qty_all=explode('*',$bh_qty);
	$remark_all=explode('*',$remarks);
	$id_up_all=explode('*',$id_up);
	$total_data=count($id_up_all);
	$total_pcs=0;
	$total_bhqty=0;
	//print_r ($id_up_all);
	?>
    <script>
		var permission='<? echo $permission; ?>';
		var str_size = [<? echo substr(return_library_autocomplete( "select size_name from lib_size where status_active =1 and is_deleted=0 group by size_name ", "size_name" ), 0, -1); ?> ];
 
 		function add_break_down_tr( i )
		{ 
			var row_num=$('#size_tbl tbody tr').length;
			
			if (row_num!=i)
			{
				return false;
			}
			else
			{ 
				i++;
				$("#size_tbl tbody tr:last").clone().find("input,select").each(function(){
					  
				$(this).attr({ 
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name },
				  'value': function(_, value) { return '' }              
				});
				 
				}).end().appendTo("#size_tbl");
					
				$("#size_tbl tbody tr:last").removeAttr('id').attr('id','row_'+i);
				
				$('#increase_'+i).removeAttr("value").attr("value","+");
				//$('#txtgmtpcs_'+i).removeAttr("onKeyUp").attr("onKeyUp","calculate_total_qnty("+i+");");
				$('#decrease_'+i).removeAttr("value").attr("value","-");
				$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
				$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
				   
				add_auto_complete(i);
			}
		}
		
		function fn_deleteRow(rowNo) 
		{ 
			var numRow=$('#size_tbl tbody tr').length;
			if(numRow==rowNo && rowNo!=1)
			{
				$('#size_tbl tbody tr:last').remove();
			}
			else
			{
				$("#txtsizename_"+rowNo).val('');
				$("#txtgmtpcs_"+rowNo).val('');
				$("#txtgmtbhqty_"+rowNo).val('');
				$("#txtremarks_"+rowNo).val('');
				$("#sizeupid_"+rowNo).val('');
			}
		}
		
		function add_auto_complete(i)
		{
			$(document).ready(function(e)
			 {
					$("#txtsizename_"+i).autocomplete({
					 source: str_size
				  });
			 });
		}
		
		function fnc_size_entry(operation)
		{
			var tot_row=$('#size_tbl tbody tr').length;
			//alert (tot_row);
			var data_all=''; var j=0;
			for(i=1; i<=tot_row; i++)
			{
				if (form_validation('txtsizename_'+i,'Size Name')==false )
				{
					return;
				}
				
				var siz_val=$("#txtsizename_"+i).val();
				
				if (siz_val!="")
				{
					j++;
					data_all+=get_submitted_data_string('txtsizename_'+i+'*txtgmtpcs_'+i+'*txtgmtbhqty_'+i+'*txtremarks_'+i+'*sizeupid_'+i,"../../../",i);
				}
			}
			if(data_all=='')
			{
				alert("No Data Select");	
				return;
			}
			//alert(data_string);return;
			var data="action=save_update_delete_size&operation="+operation+get_submitted_data_string('mainupid*dtlsupid',"../../../")+data_all+'&tot_row='+j;
			//alert (data);
			freeze_window(operation);
			http.open("POST","sample_development_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=fnc_size_entry_Reply_info;
		}
		
		function fnc_size_entry_Reply_info()
		{
			if(http.readyState == 4) 
			{
				// alert(http.responseText);//return;
				var reponse=trim(http.responseText).split('**');	
				show_msg(reponse[0]);
				var save_id_return= return_global_ajax_value( reponse[2], 'load_php_dtls_form_return_id_date', '', 'sample_development_controller');
				var reponse_return_id=save_id_return.split('*');
				
				var tot_row=$('#size_tbl tbody tr').length;
				var k=1;
				for(i=0; i<=reponse_return_id.length; i++)
				{
					$('#sizeupid_'+k).val(reponse_return_id[i]);
					var id=$('#sizeupid_'+k).val();
					k++;
				}
				//if(id!='')
				//{
					set_button_status(1, permission, 'fnc_size_entry',1);
				//}
				release_freezing();	
			}
		}
		
		function fnc_close(mainval,upval)
		{
			//alert (upval);
			var tot_row=$('#size_tbl tbody tr').length;
			var txt_size="";var txt_qnty=""; var txt_hidd_id=""; var txt_bhqnty="";
			var total_qnty="";var total_rate="";var total_amount="";var main_id="";var dtls_id="";
			main_id=mainval;
			dtls_id=upval;
			
			for(var i=1; i<=tot_row; i++)
			{
				if(i>1)
				{
					txt_size +="*";
					txt_qnty +="*";
					txt_hidd_id +="*";
				}
				txt_size += $("#txtsizename_"+i).val();
				txt_qnty += $("#txtgmtpcs_"+i).val();
				txt_bhqnty += $("#txtgmtbhqty_"+i).val();
				// txt_remarks += $("#txtremarks_"+i).val();
				txt_hidd_id += $("#sizeupid_"+i).val();
			}
			document.getElementById('hidden_size').value=txt_size;
			document.getElementById('hidden_qty').value=txt_qnty;
			document.getElementById('hidden_bhqty').value=txt_bhqnty;
			// document.getElementById('hidden_remarks').value=txt_remarks;
			document.getElementById('hidden_id').value=txt_hidd_id;
			parent.emailwindow.hide();
		}
		
		function calculate_total_qnty(index) //for color level
		{
			var tot_row=$('#size_tbl tbody tr').length;
			var total_qnty="";
			for(var i=1; i<=tot_row; i++)
			{
				//alert($("#txtsizename_"+i).val());
				total_qnty=total_qnty*1+$("#txtgmtpcs_"+i).val()*1;
				
			}
			document.getElementById('txt_total_pcs').value=total_qnty;
			//document.getElementById('total_color').value=total_qnty;
		}
		
		function calculate_total_bhqnty(index) //for color level
		{
			
			
			var tot_row=$('#size_tbl tbody tr').length;
			var total_bhqnty="";
			for(var i=1; i<=tot_row; i++)
			{
				
	  			var bh_qty=$("#txtgmtbhqty_"+i).val()*1;
	  			var pcs_qty=$("#txtgmtpcs_"+i).val()*1;
	  			
	  			if(bh_qty>pcs_qty)
	  			{
	  				alert(' BH Qty is  Greater Than Pcs qty.');
					$("#txtgmtbhqty_"+i).val('');
					document.getElementById('txt_total_bh_qty').value-bh_qty;
					
	  				return;
	  			}
				
				total_bhqnty=total_bhqnty*1+$("#txtgmtbhqty_"+i).val()*1;
			}
			document.getElementById('txt_total_bh_qty').value=total_bhqnty;
			//document.getElementById('total_color').value=total_qnty;
		}
			
    </script>
    <body onLoad="add_auto_complete(1);">
		<div align="center" style="width:100%;" >
        <? echo load_freeze_divs ("../../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:525px;">
            <table align="center" cellspacing="0" width="525" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <thead>
                    <th width="150" >Size</th>
                    <th width="75" >Gmts Pcs</th>
                  	 <th width="75" >BH Qty</th>
				  	 <th width="75" >Remarks</th>
                    <th width="70" ><Input type="hidden" name="mainupid" class="text_boxes" ID="mainupid" value="<? echo $txt_style_id; ?>" style="width:30px" /><Input type="hidden" name="dtlsupid" class="text_boxes" ID="dtlsupid" value="<? echo $update_id_dtl; ?>" style="width:30px" />
                    <!--<Input type="hidden" name="samp_color_id" class="text_boxes" ID="samp_color_id" value="<? //echo $txt_sample_color; ?>" style="width:30px" />-->
                    </th>
                </thead>
                <tbody>
                <?
					if ($id_up_all=="" || $id_up_all==0)
					{
					?>
						<tr id="row_1">
							<td width="150" align="center" ><Input name="txtsizename[]" class="text_boxes" ID="txtsizename_1" value="" style="width:150px" /><Input type="hidden" name="sizeupid[]" class="text_boxes" ID="sizeupid_1" value="" style="width:30px" onKeyUp="calculate_total_qnty(1);" ></td>
							<td width="75" align="center" ><Input name="txtgmtpcs[]" class="text_boxes_numeric" ID="txtgmtpcs_1" style="width:75px"  /></td>
                            <td width="75" align="center" ><Input name="txtgmtbhqty[]" class="text_boxes_numeric" ID="txtgmtbhqty_1" style="width:75px"  /></td>
							<td width="75" align="center" ><Input name="txtremarks[]" class="text_boxes_numeric" ID="txtremarks_1" style="width:75px"  /></td>
							<td align="center">
								<input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( 1 )" />
								<input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(1);" />
							</td> 
						</tr>
					<?
					}
					elseif ($id_up_all!="" || $id_up_all!=0)
					{
						$k=0;
						for($i=0; $i<$total_data; $i++)
						{
							$k++;
					?>
						<tr id="row_1">
							<td width="150" align="center" ><Input name="txtsizename[]" class="text_boxes" ID="txtsizename_<? echo $k; ?>" value="<? echo $size_id_all[$i]; ?>" style="width:150px" /><Input type="hidden" name="sizeupid[]" class="text_boxes" ID="sizeupid_<? echo $k; ?>" value="<? echo $id_up_all[$i]; ?>" style="width:30px"  ></td>
							<td width="75" align="center" ><Input name="txtgmtpcs[]" class="text_boxes_numeric" ID="txtgmtpcs_<? echo $k; ?>" value="<? echo $size_qty_all[$i]; $total_pcs+=$size_qty_all[$i]; ?>" style="width:75px" onKeyUp="calculate_total_qnty(<? echo $k; ?>);" /></td>
                            <td width="75" align="center" ><Input name="txtgmtbhqty[]" class="text_boxes_numeric" ID="txtgmtbhqty_<? echo $k; ?>" value="<? echo $bh_qty_all[$i]; $total_bhqty+=$bh_qty_all[$i]; ?>" onKeyUp="calculate_total_bhqnty(<? echo $k; ?>);" style="width:75px"  /></td>
							<td width="75" align="center" ><Input name="txtremarks[]" class="text_boxes" ID="txtremarks_<? echo $k; ?>" value="<? echo $remark_all[$i];?>" style="width:75px"  /></td>
							<td align="center">
								<input type="button" id="increase_<? echo $k; ?>" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $k; ?> )" />
								<input type="button" id="decrease_<? echo $k; ?>" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<? echo $k; ?>);" />
							</td> 
						</tr>
					<?
						}
					}
                ?>
                </tbody>
            </table>
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >
                <tr>
                	<td width="178">&nbsp;</td>
                	<td width="80" align="center"><Input name="txt_total_pcs" class="text_boxes_numeric" ID="txt_total_pcs" style="width:82px" value="<? echo $total_pcs; ?>" readonly /></td>
                    <td width="75" align="center"><Input name="txt_total_bh_qty" class="text_boxes_numeric" ID="txt_total_bh_qty" style="width:82px" value="<? echo $total_bhqty; ?>" readonly /></td>
                    <td >&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="3" align="center" class="button_container">
                        <? 
						//print_r ($id_up_all);
                            if($id_up!='')
                            {
                                echo load_submit_buttons($permission, "fnc_size_entry", 1,0,"reset_form('size_1','','','','','');",1);
                            }
                            else
                            {
                                echo load_submit_buttons($permission, "fnc_size_entry", 0,0,"reset_form('size_1','','','','','');",1);
                            }
                        ?>
                        <input type="hidden" name="hidden_size" id="hidden_size" class="text_boxes /">
                        <input type="hidden" name="hidden_qty" id="hidden_qty" class="text_boxes" />
                        <input type="hidden" name="hidden_bhqty" id="hidden_bhqty" class="text_boxes" />
                        <input type="hidden" name="hidden_id" id="hidden_id" class="text_boxes" />
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="3">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close( document.getElementById('mainupid').value,document.getElementById('dtlsupid').value);" style="width:100px" />
                    </td>	  
                </tr>
            </table>
            </fieldset>
            </form>
        </div> 
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="save_update_delete_size")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		} 
		/*$new_array_color=array();
		if (!in_array(str_replace("'","",$samp_color_id),$new_array_color))
		{
		  $txt_sample_color = return_id( str_replace("'","",$samp_color_id), $color_library, "lib_color", "id,color_name");  
			 // $new_array_color[$color_id]=str_replace("'","",$$txtcolor);
		}*/
		if(str_replace("'","",$samp_color_id)!="")
		{
			if (!in_array(str_replace("'","",$samp_color_id),$new_array_color))
			{
				$txt_sample_color = return_id( str_replace("'","",$samp_color_id), $color_library, "lib_color", "id,color_name","116");
				$new_array_color[$txt_sample_color]=str_replace("'","",$samp_color_id);
			}
			else $txt_sample_color =  array_search(str_replace("'","",$samp_color_id), $new_array_color);
		}
		else $txt_sample_color=0;
		
		$sizeid=return_next_id( "id","sample_development_size", 1 ) ;
		$field_array_size="id, mst_id, dtls_id,size_id, size_qty,bh_qty,remarks, inserted_by, insert_date, status_active, is_deleted";
		$data_array_size=''; 
		for($i=1;$i<=$tot_row; $i++)
		{
			$txtsizename='txtsizename_'.$i;
			$txtgmtpcs='txtgmtpcs_'.$i;
			$txtgmtbhqty='txtgmtbhqty_'.$i;
			$txtremarks_='txtremarks_'.$i;
			$sizeupid='sizeupid_'.$i;
			//if($id=="") $sizeid=return_next_id( "id", "sample_development_size", 1 ); //else $sizeid=$sizeid+1;
			//$size_id=return_id( $$txtsizename, $size_arr, "lib_size", "id,size_name");
			
			if(str_replace("'","",$$txtsizename)!="")
			{
				if (!in_array(str_replace("'","",$$txtsizename),$new_array_size))
				{
					$size_id = return_id( str_replace("'","",$$txtsizename), $size_arr, "lib_size", "id,size_name","116");
					$new_array_size[$size_id]=str_replace("'","",$$txtsizename);
				}
				else $size_id =  array_search(str_replace("'","",$$txtsizename), $new_array_size);
			}
			else $size_id=0;

			if($i==1) $add_comma=""; else $add_comma=",";

			$data_array_size.="$add_comma(".$sizeid.",".$mainupid.",".$dtlsupid.",".$size_id.",".$$txtgmtpcs.",".$$txtgmtbhqty.",".$$txtremarks_.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$sizeid=$sizeid+1;
		}
		//echo "insert into sample_development_size (".$field_array_size.") Values ".$data_array_size."";die;
		$rIDs=sql_insert("sample_development_size",$field_array_size,$data_array_size,1);
		
		if($db_type==0)
		{
			if($rIDs)
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'","",$$sizeupid)."**".str_replace("'","",$dtlsupid)."**1";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**".str_replace("'","",$$sizeupid)."**".str_replace("'","",$dtlsupid)."**0";
			}
		}
		
		else if($db_type==2 || $db_type==1 )
		{
			if($rIDs)
			{
				oci_commit($con); 
				echo "0**".str_replace("'","",$$sizeupid)."**".str_replace("'","",$dtlsupid)."**1";
			}
			else
			{
				oci_rollback($con); 
				echo "5**".str_replace("'","",$$sizeupid)."**".str_replace("'","",$dtlsupid)."**0";
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
		/*$new_array_color=array();
		if (!in_array(str_replace("'","",$samp_color_id),$new_array_color))
		{
		  $txt_sample_color = return_id( str_replace("'","",$samp_color_id), $color_library, "lib_color", "id,color_name");  
			 // $new_array_color[$color_id]=str_replace("'","",$$txtcolor);
		}*/
		
		$sizeid=return_next_id( "id","sample_development_size", 1 ) ;
		$field_array_size="id, mst_id, dtls_id,size_id, size_qty,bh_qty,remarks, inserted_by, insert_date, status_active, is_deleted";
		$data_array_size=''; 
		for($i=1;$i<=$tot_row; $i++)
		{
			$txtsizename='txtsizename_'.$i;
			$txtgmtpcs='txtgmtpcs_'.$i;
			$txtgmtbhqty='txtgmtbhqty_'.$i; 
			$txtremarks='txtremarks_'.$i;

			$sizeupid='sizeupid_'.$i;
			//$size_id=return_id( $$txtsizename, $size_arr, "lib_size", "id,size_name");
			if(str_replace("'","",$$txtsizename)!="")
			{
				if (!in_array(str_replace("'","",$$txtsizename),$new_array_size))
				{
					$size_id = return_id( str_replace("'","",$$txtsizename), $size_arr, "lib_size", "id,size_name","116");
					$new_array_size[$size_id]=str_replace("'","",$$txtsizename);
				}
				else $size_id =  array_search(str_replace("'","",$$txtsizename), $new_array_size);
			}
			else $size_id=0;
			if($i==1) $add_comma=""; else $add_comma=",";

			$data_array_size.="$add_comma(".$sizeid.",".$mainupid.",".$dtlsupid.",".$size_id.",".$$txtgmtpcs.",".$$txtgmtbhqty.",".$$txtremarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$sizeid=$sizeid+1;
		}
		$rID=execute_query("delete from sample_development_size where dtls_id=$dtlsupid ");
		$rIDs=sql_insert("sample_development_size",$field_array_size,$data_array_size,1);
		if($db_type==0)
		{
			if( $rID && $rIDs )
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$$sizeupid)."**".str_replace("'","",$dtlsupid)."**1";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".str_replace("'","",$$sizeupid)."**".str_replace("'","",$dtlsupid)."**0";
			}
		}
		else if($db_type==2 || $db_type==1)
		{
			if( $rID && $rIDs )
			{
				oci_commit($con);  
				echo "1**".str_replace("'","",$$sizeupid)."**".str_replace("'","",$dtlsupid)."**1";
			}
			else
			{
				oci_rollback($con); 
				echo "6**".str_replace("'","",$$sizeupid)."**".str_replace("'","",$dtlsupid)."**0";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="load_php_dtls_form_return_id_date")
{
	$qry_result=sql_select( "select id,dtls_id from  sample_development_size where dtls_id='$data'");
	foreach ($qry_result as $row)
	{
		if($id=="") $id=$row[csf("id")]; else $id.="*".$row[csf("id")];
	}
	echo $id;
}

if($action=="color_from_library")
{
  $color_from_library=return_field_value("color_from_library", "variable_order_tracking", "company_name=$data  and variable_list=23  and status_active=1 and is_deleted=0");
  echo trim($color_from_library);
  disconnect($con);die;
}

if($action=="color_popup")
{
echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
extract($_REQUEST);
?>
<script> 
function js_set_value(data)
{
	document.getElementById('color_name').value=data;
    parent.emailwindow.hide();
}
</script> 
</head>
<body>
<body>
<div align="center">
<form>
<input type="hidden" id="color_name" name="color_name" />
<?
	if($buyer_name=="" || $buyer_name==0)
	{
		$sql="select color_name,id FROM lib_color  WHERE status_active=1 and is_deleted=0";
	}
	else
	{
		$sql="select a.color_name,a.id FROM lib_color a, lib_color_tag_buyer b  WHERE a.id=b.color_id and b.buyer_id=$buyer_name and  status_active=1 and is_deleted=0"; 
	}
	echo  create_list_view("list_view", "Color Name", "160","210","420",0, $sql , "js_set_value", "color_name", "", 1, "0", $arr , "color_name", "requires/sample_booking_non_order_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0,0,0,0,0,0,2,2,2,2,2') ;
	?>
    </form>
    </div>
    </body>
    </html>
    <?
	exit();
}

if($action=="quotation_inquery_id")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST); 
	//echo $cbo_buyer_name; die;
?>
     
<script>
	function js_set_value(mrr)
	{
 		$("#hidden_issue_number").val(mrr); // mrr number
		parent.emailwindow.hide();
	}
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="500" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
            <thead>
                    <th colspan="4"> </th>
                    <th  >
                      <?
                       echo create_drop_down( "cbo_string_search_type", 140, $string_search_type,'', 1, "--Searching Type--" );
                      ?>
                    </th>
                   <th colspan="3" > </th>     
           </thead>
            <thead>
                <tr> 
                   <th width="150">Company Name</th>               	 
                    <th width="150">Buyer Name</th>
                    <th width="100">Inquery ID</th>
                    <th width="80">Year</th>
                    <th width="150" >Style Reff.</th>
                    <th width="100" >Request No</th>
                    <th width="100">Inquery Date </th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:80px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr>
                <td>
                    <?
                        echo create_drop_down( "cbo_company_name", 150, "select comp.id,comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "",1);
                     ?> 
                
                
                </td>
                    <td>
                        <?  
							echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $cbo_buyer_name, "" );
                        ?>
                    </td>
                    
                 
                    <td width="" align="center" >				
                        <input type="text" style="width:80px" class="text_boxes"  name="txt_inquery_id" id="txt_inquery_id" />	
                    </td>
                       <td>
                         <? 
                            echo create_drop_down( "cbo_year", 70, $year,"", 1, "- Select- ", date('Y'), "" );
                         ?>	
                      
                    </td>
                    <td width="" align="center" >				
                        <input type="text" style="width:120px" class="text_boxes"  name="txt_style" id="txt_style" />	
                    </td>
                    <td width="" align="center" >				
                        <input type="text" style="width:80px" class="text_boxes"  name="txt_requst_no" id="txt_requst_no" />	
                    </td>    
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="  Date" />
                        
                    </td> 
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_date_from').value+'_'+<? echo $company; ?>+'_'+document.getElementById('txt_inquery_id').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('txt_requst_no').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_mrr_search_list_view', 'search_div', 'sample_development_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="7">
				
                    <!-- Hidden field here-------->
                     <input type="hidden" id="hidden_issue_number" value="" />
                    <!-- ---------END------------->
                </td>
            </tr>    
            </tbody>
         </tr>         
        </table>    
        <div align="center" valign="top" id="search_div"> </div> 
        </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}



if($action=="create_mrr_search_list_view")
{
	
	$ex_data = explode("_",$data);
	$txt_buyer = $ex_data[0];
	$txt_style = $ex_data[1];
	$inq_date = $ex_data[2];
	$company = $ex_data[3];
    if($company==0) $company_name=""; else $company_name=" and company_id=$company";
	if($txt_buyer==0) $buyer_name=""; else $buyer_name="and buyer_id=$txt_buyer";
	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(`insert_date`, '-', 1)=$ex_data[5]";
	if($db_type==2) $year_cond=" and to_char(insert_date,'YYYY')=$ex_data[5]";
	if( $inq_date!="" )  $inquery_date.= " and inquery_date='".change_date_format($inq_date,'yyyy-mm-dd',"-",1)."'";
	
	$sql_cond='';
	$inquery_id_cond='';
	$request_no='';
	if($ex_data[7]==1)
		{
			
		   if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce='".str_replace("'","",$txt_style)."'";
		   if (trim($ex_data[4])!="")  $inquery_id_cond=" and system_number_prefix_num='$ex_data[4]'  $year_cond"; 
		   if (trim($ex_data[6])!="") $request_no=" and buyer_request='$ex_data[6]'"; 
		}
	
	if($ex_data[7]==4 || $ex_data[7]==0)
		{
		  if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce like '%".str_replace("'","",$txt_style)."%' ";
		  if (trim($ex_data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '%$ex_data[4]%' $year_cond";
		  if (trim($ex_data[6])!="") $request_no=" and buyer_request like '%$ex_data[6]%' ";
		}
	
	if($ex_data[7]==2)
		{
		  if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce like '".str_replace("'","",$txt_style)."%' ";
		  if (trim($ex_data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '$ex_data[4]%' $year_cond";
		  if (trim($ex_data[6])!="") $request_no=" and buyer_request like '$ex_data[6]%' "; 
		}
	
	if($ex_data[7]==3)
		{
		  if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce like '%".str_replace("'","",$txt_style)."' ";
		  if (trim($ex_data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '%$ex_data[4]' $year_cond"; 
		  if (trim($ex_data[6])!="") $request_no=" and buyer_request like '%$ex_data[6]' "; 
		}
	$buyer_arr = return_library_array("select id,buyer_name from  lib_buyer ","id","buyer_name");
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$arr=array(0=>$company_arr,1=>$buyer_arr,8=>$row_status);
	 $sql = "select system_number_prefix_num,system_number,buyer_request, company_id,buyer_id,season_buyer_wise,inquery_date,style_refernce,status_active,extract(year from insert_date) as year ,id from wo_quotation_inquery where is_deleted=0 $company_name $buyer_name $sql_cond $inquery_id_cond $request_no $inquery_date ";
	//echo $sql;
	echo create_list_view("list_view", "Company Name,Buyer Name,Inquery ID,Year,Request No,Style Reff., Inquery Date,Season,Status","120,120,70,50,70,120,90,120,100","920","260",0, $sql , "js_set_value", "system_number,id", "", 1, "company_id,buyer_id,0,0,0,0,0,0,status_active", $arr, "company_id,buyer_id,system_number_prefix_num,year,buyer_request,style_refernce,inquery_date,season_buyer_wise,status_active", "",'','0') ;
	?>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="populate_data_from_data")
{
	$data=explode("**",$data);
	
	$sql = sql_select("select  id,buyer_id,company_id,season_buyer_wise,dealing_marchant, style_refernce,est_ship_date from wo_quotation_inquery where system_number='$data[0]'");
	
	foreach($sql as $row)
	{
		
		echo "load_drop_down( 'requires/sample_development_controller', '".$row[csf("buyer_id")]."', 'cbo_sample_type', 'sampletd' ) ;\n";
		echo "document.getElementById('txt_style_name').value = '".$row[csf("style_refernce")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('cbo_dealing_merchant').value = '".$row[csf("dealing_marchant")]."';\n";
		echo "document.getElementById('cbo_season_name').value = '".$row[csf("season_buyer_wise")]."';\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_quotation_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_est_ship_date').value = '".change_date_format($row[csf("est_ship_date")],'dd-mm-yyyy','-')."';\n";
		//echo "load_drop_down( 'requires/sample_development_controller', '".$row[csf("team_leader")]."', 'cbo_dealing_merchant', 'div_marchant' ) ;\n";
		//load_drop_down( 'requires/sample_development_controller', this.value, 'cbo_dealing_merchant', 'div_marchant' )
		
	}
	exit();
}

?>
