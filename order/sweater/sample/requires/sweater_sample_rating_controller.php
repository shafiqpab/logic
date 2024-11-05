<?
header('Content-type:text/html; charset=utf-8');
session_start();
if($_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
include('../../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];






if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "load_drop_down( 'requires/sweater_sample_rating_controller', this.value, 'load_drop_down_season_buyer', 'season_td');load_drop_down( 'requires/sweater_sample_rating_controller', this.value, 'load_drop_down_sample_for_buyer', 'sample_td');load_drop_down( 'requires/sweater_sample_rating_controller', this.value+'*'+1, 'load_drop_down_brand', 'brand_td');" );
	exit();
}

if ($action=="load_drop_down_sample_for_buyer")
{
	echo create_drop_down( "cboSampleName_1", 100, "select a.id,a.sample_name,b.sequ from lib_sample a,lib_buyer_tag_sample b where a.id=b.tag_sample and  b.buyer_id=$data and b.sequ  is not null and
 a.status_active=1 and a.is_deleted=0 and a.business_nature=100  group by  a.id,a.sample_name,b.sequ order by b.sequ ","id,sample_name", 1, "-- Select Sample --", $selected, "" );
 exit();
}




if ($action=="load_drop_down_season_buyer")
{

	echo create_drop_down( "cbo_season_name", 150, "select a.id,a.season_name from lib_buyer_season a where a.status_active =1 and a.is_deleted=0 and a.buyer_id='$data'","id,season_name", 1, "-- Select Season --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_agent", 150, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" );
	exit();
}

if ($action=="cbo_dealing_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 172, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	exit();
}

if ($action=="save_update_delete")
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

		$req_id_arr=sql_select("select id from sample_rating_mst where sample_development_mst_id=$txt_requisition_update_id");

		if(count($req_id_arr)>0){
			echo "11**";
			return;
		}

		$mst_id=return_next_id( "id", "sample_rating_mst", 1 ) ;
		

		$new_system_id=explode("*",return_mrr_number( '', '', 'SRP', date("Y",time()), 5, "select rating_number_prefix, rating_number_prefix_num from sample_rating_mst where entry_form=528 and to_char(insert_date,'YYYY')=".date('Y',time())."  order by id desc ", "rating_number_prefix", "rating_number_prefix_num" ));
	

		$field_array="id, rating_number_prefix, rating_number_prefix_num,rating_number, requisition_number,complete_status, complete_date, sample_name, team_name, sample_status, buyer_name, style_ref_no, season, sample_qty, dealing_marchant,item_category, remarks, company_id,entry_form,sample_development_mst_id, inserted_by, insert_date, status_active, is_deleted";
		$data_array="(".$mst_id.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$txt_requisition_id.",".$cbo_complete_status.",".$txt_complete_date.",".$cbo_sample_name.",".$cbo_sample_team.",".$cbo_sample_status.",".$cbo_buyer_name.",".$txt_style_name.",".$cbo_season_name.",".$txt_sample_qty.",".$cbo_dealing_merchant.",".$cbo_gmts_item.",".$txt_remarks.",".$txt_company_id.",528,".$txt_requisition_update_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		//  echo "10**insert into sample_rating_mst (".$field_array.") values ".$data_array; die;


		$field_dtls_array="id,mst_id,sample_particular_id,point_weight,point_scale_id ,point_scale_value,marks,remarks,inserted_by,insert_date,status_active,is_deleted";

		$id_dtls=return_next_id( "id", "sample_rating_dtls", 1 ) ;


			for ($i=1;$i<=$total_row-1;$i++)    {

					$txtMarks="txtMarks_".$i;
					$txtRemarks="txtRemarks_".$i;
					$txtSampleParticularId="txtSampleParticularId_".$i;
					$txtScaleId="txtScaleId_".$i;
					$txtScaleValue="txtScaleValue_".$i;
					

					if ($i!=1) $data_dtls_array .=",";
					$data_dtls_array .="(".$id_dtls.",".$mst_id.",".$$txtSampleParticularId.",5,".$$txtScaleId.",".$$txtScaleValue.",".$$txtMarks.",".$$txtRemarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
					
					

					$id_dtls=$id_dtls+1;


			}
			//  echo "10**insert into sample_rating_dtls (".$field_dtls_array.") values ".$data_dtls_array; die;
			$rID=sql_insert("sample_rating_mst",$field_array,$data_array,1);
			$rID2=sql_insert("sample_rating_dtls",$field_dtls_array,$data_dtls_array,1);
	    



		//echo $rID; die;

		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");
				echo "0**".$new_system_id[0]."**".$mst_id;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$mst_id;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);
				echo "0**".$new_system_id[0]."**".$id_mst;
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
		$field_array="sample_stage_id*requisition_date*style_ref_no*buyer_name*season*product_dept*dealing_marchant*agent_name*buyer_ref*estimated_shipdate*remarks*updated_by*update_date*status_active*is_deleted*req_ready_to_approved*material_delivery_date*team_leader*season_year*brand_id";
		//txt_bhmerchant*txt_product_code
		$data_array="".$cbo_sample_stage."*".$txt_requisition_date."*".$txt_style_name."*".$cbo_buyer_name."*".$cbo_season_name."*".$cbo_product_department."*".$cbo_dealing_merchant."*".$cbo_agent."*".$txt_buyer_ref."*".$txt_est_ship_date."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0*".$cbo_ready_to_approved."*".$txt_material_dlvry_date."*".$cbo_sample_team."*".$cbo_season_year."*".$cbo_brand_id."";

		$field_dtls_array="id*mst_id*sample_particular_id*point_weight*point_scale_id *point_scale_value*marks*remarks*inserted_by*insert_date*status_active*is_deleted";


		$field_array="sample_particular_id*point_weight*point_scale_id*point_scale_value*marks*remarks*updated_by*update_date*status_active*is_deleted";

		$id_dtls=return_next_id( "id", "sample_rating_dtls", 1 ) ;

		for ($i=1;$i<=$total_row-1;$i++)    {

					$txtMarks="txtMarks_".$i;
					$txtRemarks="txtRemarks_".$i;
					$txtSampleParticularId="txtSampleParticularId_".$i;
					$txtScaleId="txtScaleId_".$i;
					$txtScaleValue="txtScaleValue_".$i;
				
					$data_array="".$$txtSampleParticularId."*5*".$$txtScaleId."*".$$txtScaleValue."*".$$txtMarks."*".$$txtRemarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
					
					$rID=sql_update("sample_development_mst",$field_array,$data_array,"id","".$update_id."",1);
			}









		


		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$update_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);
				//echo "1**".str_replace("'","",$update_id);
				echo "1**".str_replace("'","",$txt_requisition_id)."**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
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
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		$rID1=sql_delete("sample_development_mst",$field_array,$data_array,"id","".$update_id."",0);
		$rID2=sql_delete("sample_development_dtls",$field_array,$data_array,"sample_mst_id","".$update_id."",0);
		$rID3=sql_delete("sample_development_size",$field_array,$data_array,"mst_id","".$update_id."",0);
		$rID4=sql_delete("sample_development_fabric_acc",$field_array,$data_array,"sample_mst_id","".$update_id."",0);
		$rID5=sql_delete("sample_development_rf_color",$field_array,$data_array,"mst_id","".$update_id."",0);
		if($db_type==0)
		{
			if($rID1 && $rID2 && $rID3 )
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$update_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID1 && $rID2 && $rID3)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
	}
}





if ($action=="load_drop_down_brand")
{
	$data_arr = explode("*", $data);
	if($data_arr[1] == 1) $width=90; else $width=150;
	echo create_drop_down( "cbo_brand_id", $width, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data_arr[0]' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}

if($action=="create_requisition_id_search_list_view")
{
	$data=explode('_',$data);
	
	
	if ($data[1]!=0) $company=" and a.company_id='$data[1]'"; else 
	{ echo "<b style='color:crimson;'> Please Select Company First.</b>"; die; }
	
	

	if ($data[2]!=0) $buyer=" and a.buyer_name='$data[2]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	

	if($data[0]==4 || $data[0]==0)
		{
		
		  if ($data[5]!="") $style_cond=" and a.style_ref_no like '%$data[5]%' "; else $style_cond="";
		}

	if($data[0]==2)
		{
		 
		  if ($data[5]!="") $style_cond=" and a.style_ref_no like '$data[5]%' "; else $style_cond="";
		}

	if($data[0]==3)
		{
		  
		  if ($data[5]!="") $style_cond=" and a.style_ref_no like '%$data[5]' "; else $style_cond="";
		}


	if($db_type==0)
	{
	if ($data[3]!="" &&  $data[4]!="") $estimated_shipdate  = "and a.REQUISITION_DATE  between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $estimated_shipdate ="";
	}
	if($db_type==2)
	{
	if ($data[3]!="" &&  $data[4]!="") $estimated_shipdate  = "and a.REQUISITION_DATE  between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $estimated_shipdate ="";
	}
	if ($data[6]!="") $requisition_num=" and a.requisition_number_prefix_num like '%$data[6]' "; else $requisition_num="";

	//if (!$data[8] && trim($data[7])=="") {echo "<b style='color:crimson;'> Please Select Sample Stage</b>";die;}


	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$team_leader=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
	$season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name"  );

	$arr=array (2=>$buyer_arr,3=>$brand_arr,5=>$season_arr,7=>$product_dept,4=>$dealing_marchant,9=>$sample_stage);
	$sql="";
	if($db_type==0)
	{
		

		$sql="select a.id,a.requisition_number_prefix_num,SUBSTRING_INDEX(a.insert_date, '-', 1) as year,a.company_id,a.buyer_name,a.style_ref_no,a.product_dept,a.dealing_marchant,a.sample_stage_id, a.season, a.season_year, a.brand_id from sample_development_mst a,tna_process_mst b
		where a.entry_form_id=341 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		and b.po_number_id=a.id and b.task_type=5 and b.task_number=8  $company $buyer $style_id_cond $style_cond $estimated_shipdate $requisition_num  $stage_id $brand_id $season_year $season order by a.id DESC";
	

	}
	else if($db_type==2)
	{
	$sql="select a.id,a.requisition_number_prefix_num,to_char(a.insert_date,'YYYY') as year,a.company_id,a.buyer_name,a.style_ref_no,a.product_dept,a.dealing_marchant,a.sample_stage_id, a.season, a.season_year, a.brand_id from sample_development_mst a,tna_process_mst b
	where a.entry_form_id=341 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	and b.po_number_id=a.id and b.task_type=5 and b.task_number=8  $company $buyer $style_id_cond $style_cond $estimated_shipdate $requisition_num  $stage_id $brand_id $season_year $season order by a.id DESC";

	}
	

	
	echo  create_list_view("list_view", "Year,Requisition No,Buyer Name,Style Name,Dealing Merchant", "60,100,120,100,90","550","240",0, $sql , "js_set_value", "id", "", 1, "0,0,buyer_name,0,dealing_marchant", $arr , "year,requisition_number_prefix_num,buyer_name,style_ref_no,dealing_marchant", "",'','0,0,0,0,0') ;

	exit();
}

if($action=="create_rating_search_list_view")
{
	$data=explode('_',$data);
	
	
	if ($data[1]!=0) $company=" and company_id='$data[1]'"; else 
	{ echo "<b style='color:crimson;'> Please Select Company First.</b>"; die; }
	
	

	if ($data[2]!=0) $buyer=" and buyer_name='$data[2]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	

	if($data[0]==4 || $data[0]==0)
		{
		
		  if ($data[5]!="") $style_cond=" and style_ref_no like '%$data[5]%' "; else $style_cond="";
		}

	if($data[0]==2)
		{
		 
		  if ($data[5]!="") $style_cond=" and style_ref_no like '$data[5]%' "; else $style_cond="";
		}

	if($data[0]==3)
		{
		  
		  if ($data[5]!="") $style_cond=" and style_ref_no like '%$data[5]' "; else $style_cond="";
		}


	if($db_type==0)
	{
	if ($data[3]!="" &&  $data[4]!="") $rating_date  = "and insert_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $rating_date ="";
	}
	if($db_type==2)
	{
	if ($data[3]!="" &&  $data[4]!="") $rating_date  = "and insert_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $rating_date ="";
	}
	if ($data[6]!="") $requisition_num=" and requisition_number like '%$data[6]' "; else $requisition_num="";
	if ($data[7]!="") $rating_num=" and rating_number_prefix_num like '%$data[7]' "; else $rating_num="";

	//if (!$data[8] && trim($data[7])=="") {echo "<b style='color:crimson;'> Please Select Sample Stage</b>";die;}


	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$team_leader=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
	$season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name"  );

	$arr=array (3=>$buyer_arr,5=>$dealing_marchant);
	$sql="";
	if($db_type==0)
	{
		
	
		$sql= "select id,rating_number_prefix_num,SUBSTRING_INDEX(insert_date, '-', 1) as year,  requisition_number, buyer_name, style_ref_no, dealing_marchant,item_category,sample_development_mst_id, status_active, is_deleted from sample_rating_mst where  entry_form=528  and  status_active=1 and is_deleted=0 $company $buyer $rating_num $style_cond $rating_date";
			

	}
	else if($db_type==2)
	{
	$sql= "select id,rating_number_prefix_num, to_char(insert_date,'YYYY') as year,  requisition_number, buyer_name, style_ref_no, dealing_marchant,item_category,sample_development_mst_id, status_active, is_deleted from sample_rating_mst where  entry_form=528  and  status_active=1 and is_deleted=0 $company $buyer $rating_num $style_cond $rating_date";
	}

	
	echo  create_list_view("list_view", "Year,Rating No,Req. No,Buyer Name,Style Name,Dealing Merchant", "60,100,100,120,100,90","650","240",0, $sql , "js_set_value", "id", "", 1, "0,0,0,buyer_name,0,dealing_marchant", $arr , "year,rating_number_prefix_num,requisition_number,buyer_name,style_ref_no,dealing_marchant", "",'','0,0,0,0,0,0') ;

	exit();
}

if($action=="populate_data_from_requisition")
{
	
	$res = sql_select("SELECT id, company_id, team_leader, location_id, buyer_name, style_ref_no, product_dept, agent_name, dealing_marchant, season, buyer_ref, estimated_shipdate, remarks, requisition_number, sample_stage_id, requisition_date, material_delivery_date, quotation_id, is_approved, is_acknowledge, req_ready_to_approved, season_year, brand_id from sample_development_mst where id=$data and entry_form_id=341 and is_deleted=0 and status_active=1");
	$sample_st=$res[0][csf("sample_stage_id")];
	$quotation_info=$res[0][csf("quotation_id")];
	
	if($sample_st==1)
	{
		$job_arr=array();
		$job_sql="select id,company_name, buyer_name, style_ref_no, product_dept, location_name, agent_name, dealing_marchant, season_matrix, season_buyer_wise,gmts_item_id,garments_nature from wo_po_details_master where is_deleted=0 and status_active=1";
		$job_sql_res=sql_select($job_sql);
		foreach($job_sql_res as $jrow)
		{
			$season_id=0;
			if($jrow[csf("season_matrix")]!=0) $season_id=$jrow[csf("season_matrix")];
			else $season_id=$jrow[csf("season_buyer_wise")];

			$job_arr[$jrow[csf("id")]]['company']=$jrow[csf("company_name")];

			$job_arr[$jrow[csf("id")]]['buyer']=$jrow[csf("buyer_name")];
			$job_arr[$jrow[csf("id")]]['style']=$jrow[csf("style_ref_no")];
			$job_arr[$jrow[csf("id")]]['dept']=$jrow[csf("product_dept")];
			$job_arr[$jrow[csf("id")]]['loaction']=$jrow[csf("location_name")];
			$job_arr[$jrow[csf("id")]]['agent']=$jrow[csf("agent_name")];
			$job_arr[$jrow[csf("id")]]['dmarchant']=$jrow[csf("dealing_marchant")];
			//$job_arr[$jrow[csf("id")]]['bh']=$jrow[csf("bh_merchant")];
			$job_arr[$jrow[csf("id")]]['gmts']=$jrow[csf("gmts_item_id")];
			$job_arr[$jrow[csf("id")]]['gmtsnature']=$jrow[csf("garments_nature")];
			$job_arr[$jrow[csf("id")]]['season']=$season_id;
		}
	 	unset($job_sql_res);

	}

	


	  $is_booking = sql_select("SELECT style_id from wo_non_ord_samp_booking_dtls where style_id=$data and status_active=1 and is_deleted=0 and entry_form_id=140 group by style_id union SELECT style_id from wo_booking_dtls where style_id=$data and status_active=1 and is_deleted=0 and entry_form_id=139 group by style_id");
	 //clearstatcache();
 	foreach($res as $result)
	{
		$req_id=$result[csf('id')];
		echo "load_drop_down( 'requires/sweater_sample_rating_controller', '".$result[csf("company_id")]."', 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/sweater_sample_rating_controller','".$result[csf("buyer_name")]."', 'load_drop_down_season_buyer', 'season_td');load_drop_down( 'requires/sweater_sample_rating_controller','".$result[csf("buyer_name")]."', 'load_drop_down_sample_for_buyer', 'sample_td');\n";

 		echo "$('#txt_requisition_id').val('".$result[csf('requisition_number')]."');\n";
		 echo "$('#txt_requisition_update_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_sample_status').val('".$result[csf('sample_stage_id')]."');\n";
		echo "$('#cbo_sample_team').val('".$result[csf('team_leader')]."');\n";
		echo "$('#txt_style_name').val('".$result[csf('style_ref_no')]."');\n";
		echo "$('#txt_company_id').val('".$result[csf('company_id')]."');\n";
		
		$tna_data=sql_select("select ID,PO_NUMBER_ID,TASK_NUMBER,ACTUAL_START_DATE from tna_process_mst where task_type=5 and is_deleted=0 and PO_NUMBER_ID=$data and TASK_NUMBER=8 and status_active=1");

		echo "$('#txt_complete_date').val('".change_date_format($tna_data[0][csf('ACTUAL_START_DATE')],'dd-mm-yyyy','-')."');\n";
		if($tna_data[0][csf('ACTUAL_START_DATE')]){
			echo "$('#cbo_complete_status').val('2');\n";	
		}else{
			echo "$('#cbo_complete_status').val('1');\n";
		}	
		if($result[csf('sample_stage_id')]==1)
		{
			
			echo "$('#cbo_buyer_name').val('".$job_arr[$result[csf("quotation_id")]]['buyer']."');\n";		
			echo "$('#txt_style_name').val('".$job_arr[$result[csf("quotation_id")]]['style']."');\n";		
			echo "$('#cbo_dealing_merchant').val('".$job_arr[$result[csf("quotation_id")]]['dmarchant']."');\n";
			echo "$('#cbo_season_name').val('".$job_arr[$result[csf("quotation_id")]]['season']."');\n";
		}
		else if($result[csf('sample_stage_id')]==2 && ($result[csf('quotation_id')]))
		{
			echo "$('#cbo_buyer_name').val('".$inq_arr[$result[csf("quotation_id")]]['buyer']."');\n";
			echo "$('#cbo_dealing_merchant').val('".$inq_arr[$result[csf("quotation_id")]]['dmarchant']."');\n";
			echo "$('#cbo_season_name').val('".$inq_arr[$result[csf("quotation_id")]]['season']."');\n";		
			echo "$('#cbo_season_name').val('".$result[csf('season')]."');\n";
			echo "$('#txt_remarks').val('".$inq_arr[$result[csf("quotation_id")]]['remarks']."');\n";
		}
 		else
		{
		
			echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";		
			echo "$('#cbo_dealing_merchant').val('".$result[csf('dealing_marchant')]."');\n";
		}
	
		echo "$('#txt_remarks').val('".$result[csf('remarks')]."');\n";
	
		echo "$('#cbo_season_name').val('".$result[csf('season')]."');\n";
	

		echo "$('#txt_requisition_id').attr('disabled','true')".";\n";
		echo "$('#cbo_sample_status').attr('disabled','true')".";\n";
		echo "$('#cbo_sample_team').attr('disabled','true')".";\n";
		echo "$('#txt_style_name').attr('disabled','true')".";\n";
		echo "$('#cbo_complete_status').attr('disabled','true')".";\n";
		echo "$('#cbo_dealing_merchant').attr('disabled','true')".";\n";
		echo "$('#cbo_buyer_name').attr('disabled','true')".";\n";
		echo "$('#cbo_season_name').attr('disabled','true')".";\n";
		echo "$('#txt_remarks').attr('disabled','true')".";\n";
		echo "$('#txt_sample_qty').attr('disabled','true')".";\n";
		echo "$('#cbo_gmts_item').attr('disabled','true')".";\n";
		echo "$('#cbo_sample_name').attr('disabled','true')".";\n";
		
  	}
	  $sql_sam="SELECT  sample_name, gmts_item_id, sum(sample_prod_qty) as qnty
	  from sample_development_dtls 
	  where entry_form_id=341 and sample_mst_id=$data and  is_deleted=0  and status_active=1 group by  sample_name, gmts_item_id ";
	  $sql_result =sql_select($sql_sam);

		echo "$('#cbo_sample_name').val('".$sql_result[0][csf('sample_name')]."');\n";		
		echo "$('#cbo_gmts_item').val('".$sql_result[0][csf('gmts_item_id')]."');\n";
		echo "$('#txt_sample_qty').val('".$sql_result[0][csf('qnty')]."');\n";



	  
   unset($res);
 	exit();
}

if($action=="populate_data_from_rating")
{
	
	$res = sql_select("select id, rating_number_prefix, rating_number_prefix_num,rating_number, requisition_number,complete_status, complete_date, sample_name, team_name, sample_status, buyer_name, style_ref_no, season, sample_qty, dealing_marchant,item_category, remarks, company_id,entry_form,sample_development_mst_id from sample_rating_mst where id=$data and entry_form=528 and is_deleted=0 and status_active=1");

	


	


	 
	 //clearstatcache();
 	foreach($res as $result)
	{
		$req_id=$result[csf('id')];
		echo "load_drop_down( 'requires/sweater_sample_rating_controller', '".$result[csf("company_id")]."', 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/sweater_sample_rating_controller','".$result[csf("buyer_name")]."', 'load_drop_down_season_buyer', 'season_td');load_drop_down( 'requires/sweater_sample_rating_controller','".$result[csf("buyer_name")]."', 'load_drop_down_sample_for_buyer', 'sample_td');\n";

		echo "$('#txt_rating_id').val('".$result[csf('rating_number')]."');\n";

 		echo "$('#txt_requisition_id').val('".$result[csf('requisition_number')]."');\n";
		echo "$('#txt_requisition_update_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_sample_status').val('".$result[csf('sample_status')]."');\n";
		echo "$('#cbo_sample_team').val('".$result[csf('team_name')]."');\n";
		echo "$('#txt_style_name').val('".$result[csf('style_ref_no')]."');\n";
		echo "$('#txt_company_id').val('".$result[csf('company_id')]."');\n";		
		echo "$('#cbo_complete_status').val('".$result[csf('complete_status')]."');\n";	
		echo "$('#cbo_dealing_merchant').val('".$result[csf('dealing_marchant')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf("buyer_name")]."');\n";
		echo "$('#cbo_season_name').val('".$result[csf('season')]."');\n";
		echo "$('#txt_remarks').val('".$result[csf('remarks')]."');\n";
		echo "$('#txt_sample_qty').val('".$result[csf('sample_qty')]."');\n";
		echo "$('#cbo_gmts_item').val('".$result[csf('item_category')]."');\n";
		echo "$('#cbo_sample_name').val('".$result[csf('sample_name')]."');\n";	

		
		
		

		echo "$('#update_id').val('".$result[csf('id')]."');\n";
		if($result[csf('id')][csf('complete_date')]!==""){
			echo "$('#txt_complete_date').val('".change_date_format($result[csf('id')][csf('complete_date')],'dd-mm-yyyy','-')."');\n";		
		}
		

		echo "$('#txt_requisition_id').attr('disabled','true')".";\n";
		echo "$('#cbo_sample_status').attr('disabled','true')".";\n";
		echo "$('#cbo_sample_team').attr('disabled','true')".";\n";
		echo "$('#txt_style_name').attr('disabled','true')".";\n";
		echo "$('#cbo_complete_status').attr('disabled','true')".";\n";
		echo "$('#cbo_dealing_merchant').attr('disabled','true')".";\n";
		echo "$('#cbo_buyer_name').attr('disabled','true')".";\n";
		echo "$('#cbo_season_name').attr('disabled','true')".";\n";
		echo "$('#txt_remarks').attr('disabled','true')".";\n";
		echo "$('#txt_sample_qty').attr('disabled','true')".";\n";
		echo "$('#cbo_gmts_item').attr('disabled','true')".";\n";
		echo "$('#cbo_sample_name').attr('disabled','true')".";\n";

		
  	}

			$result_dtls=sql_select("select id,mst_id,sample_particular_id,point_weight,point_scale_id ,point_scale_value,marks,remarks from sample_rating_dtls where mst_id=$data");

			foreach($result_dtls as $val){
				$pid=$val[csf('sample_particular_id')];
				$sid=$val[csf('sample_particular_id')];
				echo "$('#txtMarks_".$pid."').val('".$val[csf('marks')]."');\n";	
				echo "$('#txtRemarks_".$pid."').val('".$val[csf('remarks')]."');\n";	
				echo "$('#txtSampleParticularId_".$pid."').val('".$pid."');\n";	
				echo "$('#txtScaleId_".$pid."').val('".$val[csf('point_scale_id')]."');\n";	
				echo "$('#txtScaleValue_".$pid."').val('".$val[csf('point_scale_value')]."');\n";	
			}
			unset($result_dtls);
			
		
		


	  
   unset($res);
 	exit();
}

if($action=="load_php_dtls_form")
{
	$ex_data = explode("**",$data);
	$up_id=$ex_data[0];
	$type=$ex_data[1];
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$buyer_aganist_req=return_library_array( "select id,buyer_name from sample_development_mst where is_deleted=0 and status_active=1 order by buyer_name", "id", "buyer_name"  );
	
	$is_copy=return_field_value("IS_COPY","sample_development_mst","entry_form_id=341 and id='$up_id' and status_active=1 and is_deleted=0");
	$is_disable=($is_copy)?0:1;
	if($type==1)
	{
		$sql_sam="SELECT id, sample_name, gmts_item_id, smv,article_no, sample_color, sample_prod_qty, submission_qty, delv_start_date, delv_end_date, sample_charge, sample_curency, size_data,fabric_status,acc_status,embellishment_status from sample_development_dtls where entry_form_id=341 and sample_mst_id='$up_id' and  is_deleted=0  and status_active=1 order by id ASC"; //echo $sql_sam;
		$value=return_field_value("quotation_id","sample_development_mst","entry_form_id=341 and id='$up_id' and status_active=1 and is_deleted=0");
		
		
		
		$sql_result =sql_select($sql_sam);  $i=1;
		if(count($sql_result)>0)
		{
			foreach($sql_result as $row)
			{
				?>
				<tr id="tr_<? echo $i; ?>" style="height:10px;" class="general">
					<td>
						<?
						if($row[csf("fabric_status")]==1 || $row[csf("acc_status")]==1 || $row[csf("embellishment_status")]==1)
						{

							$sql="select a.id,a.sample_name,b.sequ from lib_sample a,lib_buyer_tag_sample b where a.id=b.tag_sample and b.buyer_id=$buyer_aganist_req[$up_id] and b.sequ is not null and a.status_active=1 and a.is_deleted=0  group by  a.id,a.sample_name,b.sequ order by b.sequ";
							echo create_drop_down( "cboSampleName_$i", 100, $sql,"id,sample_name", 1, "select Sample", $row[csf("sample_name")], "",1);
						}
						else
						{
							$sql="select a.id,a.sample_name,b.sequ from lib_sample a,lib_buyer_tag_sample b where a.id=b.tag_sample and b.buyer_id=$buyer_aganist_req[$up_id] and b.sequ is not null and a.status_active=1 and a.is_deleted=0  group by  a.id,a.sample_name,b.sequ order by b.sequ";
							echo create_drop_down( "cboSampleName_$i", 100, $sql,"id,sample_name", 1, "select Sample", $row[csf("sample_name")], "",$is_disable);
						}
						?>
					</td>
					<td>

						<?
						if($row[csf("fabric_status")]==1 || $row[csf("acc_status")]==1 || $row[csf("embellishment_status")]==1)
						{
							if($value=="" || $value==0)
							{
								echo create_drop_down( "cboGarmentItem_$i", 100, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id")], "",1,"");
							}
							else
							{
								echo create_drop_down( "cboGarmentItem_$i", 100, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id")], "",1,$row[csf("gmts_item_id")]);
							}
						}
						else
						{
							if($value=="" || $value==0)
							{
								echo create_drop_down( "cboGarmentItem_$i", 100, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id")], "",$is_disable,"");
							}
							else
							{
								echo create_drop_down( "cboGarmentItem_$i", 100, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id")], "",1,$row[csf("gmts_item_id")]);
							}
						}
						?>

					</td>
					<td>
						<input style="width:40px;" type="text" class="text_boxes_numeric" name="txtSmv_<? echo $i; ?>" id="txtSmv_<? echo $i; ?>" value="<? echo $row[csf("smv")]; ?>"/>
						<input type="hidden" id="updateidsampledtl_<? echo $i; ?>" name="updateidsampledtl_<? echo $i; ?>" style="width:20px" value="<? echo $row[csf("id")]; ?>" />
					</td>
					<input type="hidden" id="txtDeltedIdSd" name="txtDeltedIdSd"  class="text_boxes" style="width:20px" value="" />
					<td><input style="width:60px;" type="text" class="text_boxes"  name="txtArticle_<? echo $i; ?>" id="txtArticle_<? echo $i; ?>" placeholder="write" value="<? echo $row[csf("article_no")]; ?>" /></td>
					<td><input style="width:80px;" type="text" class="text_boxes"  name="txtColor_<? echo $i; ?>" id="txtColor_<? echo $i; ?>" placeholder="write/browse" onDblClick="openmypage_color_size('requires/sweater_sample_rating_controller.php?action=color_popup','Color Search','1','<? echo $i; ?>');" value="<? echo $color_arr[$row[csf("sample_color")]]; ?>"/></td>

					<td>
						<?
						if($row[csf("fabric_status")]==1 || $row[csf("acc_status")]==1 || $row[csf("embellishment_status")]==1)
						{
							?>
							<input style="width:100px;" type="text" class="text_boxes_numeric"  name="txtSampleProdQty_<? echo $i; ?>" readonly id="txtSampleProdQty_<? echo $i; ?>" placeholder="browse"  ondblclick="openmypage_sizeinfo('requires/sweater_sample_rating_controller.php?action=sizeinfo_popup','Size Search','<? echo $i;?>')" value="<? echo $row[csf("sample_prod_qty")]; ?>"   />

							 <!--onFocus="openmypage_sizeinfo('requires/sweater_sample_rating_controller.php?action=sizeinfo_popup_mouseover','Size Search','< ? echo $i;?>')"-->
							
							<?
						}
						else {
							?>
							<input style="width:100px;" type="text" class="text_boxes_numeric"  name="txtSampleProdQty_<? echo $i; ?>" readonly id="txtSampleProdQty_<? echo $i; ?>" placeholder="browse"   ondblclick="openmypage_sizeinfo('requires/sweater_sample_rating_controller.php?action=sizeinfo_popup','Size Search','<? echo $i;?>')"  value="<? echo $row[csf("sample_prod_qty")]; ?>"/>
							<?
						}
						?>

					</td>

					<input type="hidden" class="text_boxes"  name="txtAllData_<? echo $i;?>" id="txtAllData_<? echo $i;?>" value="<? echo $row[csf("size_data")]; ?>"/>

					<td><input style="width:100px;" type="text" class="text_boxes_numeric"  name="txtSubmissionQty_<? echo $i; ?>" readonly id="txtSubmissionQty_<? echo $i; ?>" placeholder=""  value="<? echo $row[csf("submission_qty")]; ?>" /></td>
					<td><input style="width:85px;" class="datepicker" name="txtDelvStartDate_<? echo $i; ?>" id="txtDelvStartDate_<? echo $i; ?>" value="<? echo change_date_format($row[csf("delv_start_date")]); ?>" onChange="fn_calculate_delivery_date(<? echo $i; ?>)"  <? echo $disabled; $disabled='disabled';?> /></td>
					<td><input style="width:85px;" class="datepicker" name="txtDelvEndDate_<? echo $i; ?>" id="txtDelvEndDate_<? echo $i; ?>" value="<? echo change_date_format($row[csf("delv_end_date")]); ?>" readonly disabled /></td>
					<td><input style="width:70px;" type="text" class="text_boxes_numeric"  name="txtChargeUnit_<? echo $i; ?>" id="txtChargeUnit_<? echo $i; ?>" placeholder="write" value="<? echo $row[csf("sample_charge")]; ?>"/></td>
					<td><? echo create_drop_down( "cboCurrency_$i", 70, $currency, "","","",$row[csf("sample_curency")], "", "", "" ); ?></td>
					<td><input type="button" class="image_uploader" name="txtFile_<? echo $i; ?>" id="txtFile_<? echo $i; ?>" size="10" value="ADD IMAGE" onClick="file_uploader ( '../../../', document.getElementById('updateidsampledtl_<? echo $i;?>').value,'', 'sample_details_1', 0 ,1)"></td>
					<td>
						<?
						if($row[csf("fabric_status")] ==1 || $row[csf("acc_status")]==1 || $row[csf("embellishment_status")]==1)
						{
							?>
							<input type="button" id="increase_<? echo $i; ?>" name="increase_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+"  onClick="add_break_down_tr(<? echo $i; ?>)" />
							<input type="button" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" disabled onClick="" />
							<?
						}
						else
						{
							?>
							<input type="button" id="increase_<? echo $i; ?>" name="increase_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?>)" />
							<input type="button" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />



							<?
						}
						?>
					</td>
				</tr>
				<?
				$i++;
			}
		}

	}

	else if($type==2)
	{
		
		$sql_fabric="SELECT id,sample_mst_id,sample_name,gmts_item_id,body_part_id,fabric_nature_id,fabric_description,gsm,dia,sample_color,color_type_id,width_dia_id,uom_id,remarks_ra,required_dzn,required_qty,color_data, determination_id,process_loss_percent,grey_fab_qnty,gauge,development_no,buyer_prov,no_of_ends from sample_development_fabric_acc where sample_mst_id='$up_id' and form_type=1 and  is_deleted=0  and status_active=1 order by id ASC";
		 //echo $sql_fabric;die;
		$sql_resultf =sql_select($sql_fabric);
		$i=1;
		if(count($sql_resultf)>0)
		{
			
			$sql="SELECT a.id,a.sample_name,b.id as dtls_id from  lib_sample a ,sample_development_dtls b where  a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id=b.sample_name and b.entry_form_id=341 and b.sample_mst_id='$up_id' group by a.id,a.sample_name,b.id order by b.id";
			$samp_array=array();
			$samp_result=sql_select($sql);
			if(count($samp_result)>0)
			{
				foreach($samp_result as $keys=>$vals)
				{
					$samp_array[$vals[csf("id")]]=$vals[csf("sample_name")];
				}

			}
			
			
			foreach($sql_resultf as $row)
			{
				?>
				<tr id="tr_<? echo $i; ?>" style="height:10px;" class="general">
					<td align="center" id="rfSampleId_1">
						<?
						echo create_drop_down( "cboRfSampleName_$i", 95, $samp_array,"", '', "", $row[csf("sample_name")],"");
						?>

					</td>
                    <td align="center" id="rfDevelopmentNo_1">
						<input style="width:70px;" type="text" placeholder="Write" class="text_boxes"  name="txtRfDevelopmentNo_<? echo $i; ?>" id="txtRfDevelopmentNo_<? echo $i; ?>" value="<? echo $row[csf("development_no")];?>"/>
					</td>
					<td align="center" id="rfItemId_1">
						<?
						$sql_f=sql_select("select id,gmts_item_id from sample_development_dtls where is_deleted=0  and status_active=1 and entry_form_id=341 and sample_mst_id='$up_id'");
						$gmtsf="";
						foreach ($sql_f as $rowf)
						{
							$gmtsf.=$rowf[csf("gmts_item_id")].",";
						}
						echo create_drop_down( "cboRfGarmentItem_$i", 95, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id")],"","",$gmtsf);

						?>

					</td>
                    
                    <td align="center">
						<input style="width:70px;" type="text" placeholder="Write" class="text_boxes"  name="txtBuyerProv_<?= $i;?>" id="txtBuyerProv_<?= $i;?>" value="<? echo $row[csf("buyer_prov")]; ?>"/>
					</td>
                    
                    
                    <td align="center" id="rfGmtsColorId_1">
                        <?
                        echo create_drop_down( "cboRfGmtsColorId_$i", 70, $color_arr,"", 0, "Select Color", $row[csf("sample_color")], "","",$row[csf("sample_color")]);
                        ?>
                    </td>
					<td align="center" id="rf_body_part_1">
						<input type="hidden" id="cboRfBodyPart_<? echo $i; ?>" name="cboRfBodyPart_<? echo $i; ?>" class="text_boxes" style="width:70px"  value="<? echo $row[csf("body_part_id")];?>"  readonly/>
						<input type="text" id="cboRfBodyPartname_<? echo $i; ?>" name="cboRfBodyPartname_<? echo $i; ?>" class="text_boxes" style="width:70px" onDblClick="open_body_part_popup(<? echo $i; ?>)" value="<? echo $body_part[$row[csf("body_part_id")]];?>" onBlur="load_data_to_rfcolor(<? echo $i; ?>)"   placeholder="DblClick" readonly/>
					</td>
					<td align="center" id="rf_fabric_nature_1">
						<?
						echo create_drop_down( "cboRfFabricNature_$i", 95, $item_category,"", 0, "Select Fabric Nature",$row[csf("fabric_nature_id")] , "","","100");

						?>

					</td>
					<td align="center" id="rf_fabric_description_1">
						<input style="width:60px;" type="text" class="text_boxes"  name="txtRfFabricDescription_<? echo $i; ?>" id="txtRfFabricDescription_<? echo $i; ?>" placeholder="write/browse" onDblClick="open_fabric_description_popup(<? echo $i; ?>)" value="<? echo $row[csf("fabric_description")]; ?>"/>
						<input type="hidden" name="libyarncountdeterminationid_<? echo $i; ?>" id="libyarncountdeterminationid_<? echo $i; ?>" class="text_boxes" style="width:10px" value="<? echo $row[csf("determination_id")]; ?>">
					</td>

                    <td align="center" id="rf_gauge_1">
                        <?php echo create_drop_down( "txtRfGauge_$i", 50, $gauge_arr,"", '', "", $row[csf("gauge")],""); ?>
                    </td>
                    <td align="center">
                        <input style="width:70px;" type="text" placeholder="Write" class="text_boxes_numeric"  name="txtNoOfEnds_<?= $i; ?>" id="txtNoOfEnds_<?= $i; ?>" value="<? echo $row[csf("no_of_ends")]; ?>"/>
                    </td>

					<td align="center" id="rf_color_1">
                        <input style="width:60px;" type="text" class="text_boxes"  name="txtRfColor_<? echo $i; ?>" id="txtRfColor_<? echo $i; ?>" placeholder="browse" onDblClick="openmypage_rf_color('requires/sweater_sample_rating_controller.php?action=color_popup_rf','Color Search','<? echo $i;?>');"

						readonly=""  value="<?
						$a=$row[csf("color_data")];
						$colors="";
						$c=explode("-----",$a);
						foreach($c as $v)
						{
							$cc=explode("__",$v);
							if($colors=="")
							{
								$colors.=$cc[1];
							}
							else
							{
								$colors.='***'.$cc[1];
							}
						}
						echo $colors;

						?>"/>
					</td>
                    

					<td align="center" id="rf_color_type_1">
						<?
						echo create_drop_down( "cboRfColorType_$i", 95, $color_type,"", 1, "Select Color Type", $row[csf("color_type_id")], "");
						?>
                        
                        
                        <input type="hidden" id="updateidRequiredDtl_<? echo $i; ?>" name="updateidRequiredDtl_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo $row[csf("id")]; ?>"  />
                        <input type="hidden" id="txtDeltedIdRf" name="txtDeltedIdRf"  class="text_boxes" style="width:20px" value="" />
                        <input type="hidden" name="txtRfColorAllData_<? echo $i; ?>" id="txtRfColorAllData_<? echo $i; ?>" value="<? echo $row[csf("color_data")]; ?>"  class="text_boxes">
                                            
					</td>
					<td align="center" id="rf_uom_1">
						<?
						echo create_drop_down( "cboRfUom_$i", 56, $unit_of_measurement,'', '',"",$row[csf("uom_id")],"",1,"12,15,27,1,23" );
						?>
					</td>

					<td align="center" id="rf_req_qty_1">
						<input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtRfReqQty_<? echo $i; ?>" id="txtRfReqQty_<? echo $i; ?>" placeholder="" value="<? echo $row[csf("required_qty")]; ?>" readonly/>
					</td>

					 <td align="center" id="rf_req_dzn_1" style="display:none;">
                         <input style="width:50px;" type="text" class="text_boxes" value="<? echo $row[csf("remarks_ra")]; ?>"  name="txtRfRemarks_<? echo $i;?>" id="txtRfRemarks_<? echo $i;?>" onClick="required_fab_remarks(<? echo $i; ?>);"  />
                     </td>



					<td id="rf_image_1"><input type="button" class="image_uploader" name="txtRfFile_<? echo $i; ?>" id="txtRfFile_<? echo $i; ?>" onClick="file_uploader ( '../../../', document.getElementById('updateidRequiredDtl_<? echo $i;?>').value,'', 'required_fabric_1', 0 ,1)" value="ADD IMAGE"></td>
					<td width="70">
						<input type="button" id="increaserf_<? echo $i; ?>" name="increaserf_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_rf_tr(<? echo $i; ?>)" />
						<input type="button" id="decreaserf_<? echo $i; ?>" name="decreaserf_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_rf_deleteRow(<? echo $i; ?>);" />
					</td>
				</tr>

				<?
				$i++;
			}
		}
		else
		{ 
			$sql_sam="SELECT a.id,a.sample_name,b.id as dtls_id from  lib_sample a ,sample_development_dtls b where  a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id=b.sample_name and b.entry_form_id=341 and b.sample_mst_id='$up_id' group by a.id,a.sample_name,b.id order by b.id";
			$samp_result =sql_select($sql_sam);
			
			$samp_array=array();
			if(count($samp_result)>0)
			{
				foreach($samp_result as $vals)
				{
					$samp_array[$vals[csf("id")]]=$vals[csf("sample_name")];
				}

			}
			
			
			$samp_sql="SELECT id, sample_name, gmts_item_id, smv,article_no, sample_color, sample_prod_qty, submission_qty, delv_start_date, delv_end_date, sample_charge, sample_curency, size_data,fabric_status,acc_status,embellishment_status from sample_development_dtls where entry_form_id=341 and sample_mst_id='$up_id' and  is_deleted=0  and status_active=1 order by id ASC";
			$samp_sql_result =sql_select($samp_sql);
			$i=1;
			foreach($samp_sql_result as $row)
			{
				?>

				<tr id="tr_<? echo $i; ?>" style="height:10px;" class="general">
					<td align="center" id="rfSampleId_1">
						
						<?
						/*$sql="SELECT a.id,a.sample_name,b.id as dtls_id from  lib_sample a ,sample_development_dtls b where  a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id=b.sample_name and b.entry_form_id=341 and b.sample_mst_id='$up_id' group by a.id,a.sample_name,b.id order by b.id";
						$samp_array=array();
						$samp_result=sql_select($sql);
						if(count($samp_result)>0)
						{
							foreach($samp_result as $keys=>$vals)
							{
								$samp_array[$vals[csf("id")]]=$vals[csf("sample_name")];
							}

						}*/

						echo create_drop_down( "cboRfSampleName_$i", 95, $samp_array,"", '', "", $row[csf("sample_name")],"");
						?>

					</td>
                    <td align="center" id="rfDevelopmentNo_1">
						<input style="width:70px;" type="text" placeholder="Write" class="text_boxes"  name="txtRfDevelopmentNo_<? echo $i; ?>" id="txtRfDevelopmentNo_<? echo $i; ?>" value="<? echo $row[csf("development_no")];?>"/>
					</td>
					<td align="center" id="rfItemId_1">
						<?
						$sql_f=sql_select("select id,gmts_item_id from sample_development_dtls where is_deleted=0  and status_active=1 and entry_form_id=341 and sample_mst_id='$up_id'");
						$gmtsf="";
						foreach ($sql_f as $rowf)
						{
							$gmtsf.=$rowf[csf("gmts_item_id")].",";
						}
						echo create_drop_down( "cboRfGarmentItem_$i", 95, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id")],"","",$gmtsf);

						?>

					</td>
                    <td align="center">
						<input style="width:70px;" type="text" placeholder="Write" class="text_boxes"  name="txtBuyerProv_<?= $i;?>" id="txtBuyerProv_<?= $i;?>" value="<? echo $row[csf("buyer_prov")]; ?>"/>
					</td>
                    
                    
                    <td align="center" id="rfGmtsColorId_1">
                        <?
                        echo create_drop_down( "cboRfGmtsColorId_$i", 70, $color_arr,"", 0, "Select Color", $row[csf("sample_color")], "","",$row[csf("sample_color")]);
                        ?>
                    </td>
					<td align="center" id="rf_body_part_1">
						<input type="hidden" id="cboRfBodyPart_<? echo $i; ?>" name="cboRfBodyPart_<? echo $i; ?>" class="text_boxes" style="width:70px"  value="<? echo $row[csf("body_part_id")];?>"  readonly/>
						<input type="text" id="cboRfBodyPartname_<? echo $i; ?>" name="cboRfBodyPartname_<? echo $i; ?>" class="text_boxes" style="width:70px" onDblClick="open_body_part_popup(<? echo $i; ?>)" value="<? echo $body_part[$row[csf("body_part_id")]];?>" onBlur="load_data_to_rfcolor(<? echo $i; ?>)"   placeholder="DblClick" readonly/>
					</td>
					<td align="center" id="rf_fabric_nature_1">
						<?
						echo create_drop_down( "cboRfFabricNature_$i", 95, $item_category,"", 0, "Select Fabric Nature",$row[csf("fabric_nature_id")] , "","","100");

						?>

					</td>
					<td align="center" id="rf_fabric_description_1">
						<input style="width:60px;" type="text" class="text_boxes"  name="txtRfFabricDescription_<? echo $i; ?>" id="txtRfFabricDescription_<? echo $i; ?>" placeholder="write/browse" onDblClick="open_fabric_description_popup(<? echo $i; ?>)" value="<? echo $row[csf("fabric_description")]; ?>"/>
						<input type="hidden" name="libyarncountdeterminationid_<? echo $i; ?>" id="libyarncountdeterminationid_<? echo $i; ?>" class="text_boxes" style="width:10px" value="<? echo $row[csf("determination_id")]; ?>">
					</td>

                    <td align="center" id="rf_gauge_1">
                        <?php echo create_drop_down( "txtRfGauge_$i", 50, $gauge_arr,"", '', "", "",""); ?>
                    </td> 
                    
                    <td align="center">
                        <input style="width:70px;" type="text" placeholder="Write" class="text_boxes_numeric"  name="txtNoOfEnds_<?= $i; ?>" id="txtNoOfEnds_<?= $i; ?>" value="<? echo $row[csf("no_of_ends")]; ?>"/>
                    </td>
                                       

					<td align="center" id="rf_color_1">
                        <input style="width:60px;" type="text" class="text_boxes"  name="txtRfColor_<? echo $i; ?>" id="txtRfColor_<? echo $i; ?>" placeholder="browse" onDblClick="openmypage_rf_color('requires/sweater_sample_rating_controller.php?action=color_popup_rf','Color Search','<? echo $i;?>');"

						readonly=""  value="<?
						$a=$row[csf("color_data")];
						$colors="";
						$c=explode("-----",$a);
						foreach($c as $v)
						{
							$cc=explode("__",$v);
							if($colors=="")
							{
								$colors.=$cc[1];
							}
							else
							{
								$colors.='***'.$cc[1];
							}
						}
						echo $colors;

						?>"/>
					</td>
                    

					<td align="center" id="rf_color_type_1">
						<?
						echo create_drop_down( "cboRfColorType_$i", 95, $color_type,"", 1, "Select Color Type", 2, "");
						?>
                        
                        
                        <input type="hidden" id="updateidRequiredDtl_<? echo $i; ?>" name="updateidRequiredDtl_<? echo $i; ?>"  class="text_boxes" style="width:20px" value="<? echo $row[csf("id")]; ?>"  />
                        <input type="hidden" id="txtDeltedIdRf" name="txtDeltedIdRf"  class="text_boxes" style="width:20px" value="" />
                        <input type="hidden" name="txtRfColorAllData_<? echo $i; ?>" id="txtRfColorAllData_<? echo $i; ?>" value="<? echo $row[csf("color_data")]; ?>"  class="text_boxes">
                                            
					</td>
					<td align="center" id="rf_uom_1">
						<?
						echo create_drop_down( "cboRfUom_$i", 56, $unit_of_measurement,'', '',"",$row[csf("uom_id")],"",1,"12,15,27,1,23" );
						?>
					</td>

					<td align="center" id="rf_req_qty_1">
						<input style="width:50px;" type="text" class="text_boxes_numeric"  name="txtRfReqQty_<? echo $i; ?>" id="txtRfReqQty_<? echo $i; ?>" placeholder="" value="<? echo $row[csf("required_qty")]; ?>" readonly/>
					</td>

					 <td align="center" id="rf_req_dzn_1" style="display:none;">
                             	<input style="width:50px;" type="text" class="text_boxes" value="<? echo $row[csf("remarks_ra")]; ?>"  name="txtRfRemarks_<? echo $i;?>" id="txtRfRemarks_<? echo $i;?>" onClick="required_fab_remarks(<? echo $i; ?>);"  />
                     </td>



					<td id="rf_image_1"><input type="button" class="image_uploader" name="txtRfFile_<? echo $i; ?>" id="txtRfFile_<? echo $i; ?>" onClick="file_uploader ( '../../../', document.getElementById('updateidRequiredDtl_<? echo $i;?>').value,'', 'required_fabric_1', 0 ,1)" value="ADD IMAGE"></td>
					<td width="70">
						<input type="button" id="increaserf_<? echo $i; ?>" name="increaserf_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_rf_tr(<? echo $i; ?>)" />
						<input type="button" id="decreaserf_<? echo $i; ?>" name="decreaserf_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_rf_deleteRow(<? echo $i; ?>);" />
					</td>
				</tr>

				<?
				$i++;
			}
		}
		

	}

	else if($type==3)
	{
		$sql_sam="SELECT id,sample_mst_id,sample_name_ra,gmts_item_id_ra,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,remarks_ra from sample_development_fabric_acc where sample_mst_id='$up_id' and form_type=2 and  is_deleted=0  and status_active=1 order by id ASC";
		$sql_result =sql_select($sql_sam);  $i=1;
		if(count($sql_result)>0)
		{
			foreach($sql_result as $row)
			{
				?>
				<tr  id="tr_<? echo $i;?>"  class="general">
					<td align="center" id="raSampleId_1" width="100">
						<?
						$sql="select a.id,a.sample_name,b.id as dtls_id from  lib_sample a ,sample_development_dtls b where  a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id=b.sample_name and b.entry_form_id=341 and b.sample_mst_id='$up_id' group by a.id,a.sample_name,b.id order by b.id";
						$samp_array=array();
						$samp_result=sql_select($sql);
						if(count($samp_result)>0)
						{
							foreach($samp_result as $keys=>$vals)
							{
								$samp_array[$vals[csf("id")]]=$vals[csf("sample_name")];
							}

						}
						echo create_drop_down( "cboRaSampleName_$i", 100, $samp_array,"", '', "",$row[csf("sample_name_ra")], "","");

						?>

					</td>

					<td align="center" id="raItemId_1" width="100">
						<?
						$sql_gmts=sql_select("select id,gmts_item_id from sample_development_dtls where is_deleted=0  and status_active=1 and entry_form_id=341 and sample_mst_id='$up_id'");
						$gmts="";
						foreach ($sql_gmts as $rows)
						{
							$gmts.=$rows[csf("gmts_item_id")].",";
						}
						echo create_drop_down( "cboRaGarmentItem_$i", 100, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id_ra")] , "",0,$gmts);

						?>


					</td>
					<td align="center" id="ra_trims_group_1" width="100">
						<?
						$sql="select item_name,id from lib_item_group where  is_deleted=0  and
						status_active=1 order by item_name";
						echo create_drop_down( "cboRaTrimsGroup_$i", 100, $sql,"id,item_name", 1, "Select Item", $row[csf("trims_group_ra")] , "load_uom_for_trims('$i',this.value);");

						?>
					</td>
					<td align="center" id="ra_description_1" width="130">
						<input style="width:130px;" type="text" class="text_boxes"  name="txtRaDescription_<? echo $i;?>" id="txtRaDescription_<? echo $i;?>" placeholder="write" value="<? echo $row[csf("description_ra")]; ?>"/>

						<input type="hidden" id="updateidAccessoriesDtl_<? echo $i;?>" name="updateidAccessoriesDtl_<? echo $i;?>"  class="text_boxes" style="width:20px" value="<? echo $row[csf("id")]; ?>" />
					</td>
					<input type="hidden" id="txtDeltedIdRa" name="txtDeltedIdRa"  class="text_boxes" style="width:20px" value="" />
					<td align="center" id="ra_brand_supp_1" width="130">
						<input style="width:130px;" type="text" class="text_boxes"  name="txtRaBrandSupp_<? echo $i;?>" id="txtRaBrandSupp_<? echo $i;?>" placeholder="write" value="<? echo $row[csf("brand_ref_ra")]; ?>"/>
					</td>

					<td align="center" id="ra_uom_1" width="100">
						<?
						echo create_drop_down( "cboRaUom_$i", 100, $unit_of_measurement,'', '', "",$row[csf("uom_id_ra")],"","","" );
						?>
					</td>

					<td align="center" id="ra_req_dzn_1" width="100">
						<input style="width:100px;" type="text" class="text_boxes_numeric"  name="txtRaReqDzn_<? echo $i;?>" id="txtRaReqDzn_<? echo $i;?>" placeholder="write" value="<? echo $row[csf("req_dzn_ra")]; ?>" onBlur="calculate_required_qty('2','<? echo $i ;?>');" />
					</td>

					<td align="center" id="ra_req_qty_1" width="100">
						<input style="width:100px;" type="text" class="text_boxes_numeric"  name="txtRaReqQty_<? echo $i;?>" id="txtRaReqQty_<? echo $i;?>" placeholder="write" value="<? echo $row[csf("req_qty_ra")]; ?>" readonly/>
					</td>
					<input type="hidden" class="text_boxes"  name="txtMemoryDataRa_<? echo $i;?>" id="txtMemoryDataRa_<? echo $i;?>" />

					<td align="center" id="ra_remarks_1" width="70">
						<input style="width:70px;" type="text" class="text_boxes"  name="txtRaRemarks_<? echo $i;?>" id="txtRaRemarks_<? echo $i;?>" placeholder="write" value="<? echo $row[csf("remarks_ra")]; ?>" />
					</td>
					<td id="ra_image_1"><input type="button" class="image_uploader" name="txtRaFile_<? echo $i;?>" id="txtRaFile_<? echo $i;?>" onClick="file_uploader ( '../../../', document.getElementById('updateidAccessoriesDtl_<? echo $i;?>').value,'', 'required_accessories_1', 0 ,1)"style="width:80px;" value="ADD IMAGE"></td>
					<td width="70">
						<input type="button" id="increasera_<? echo $i;?>" name="increasera_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_ra_tr(<? echo $i;?>)" />
						<input type="button" id="decreasera_<? echo $i;?>" name="decreasera_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_ra_deleteRow(<? echo $i;?>);" />
					</td>
				</tr>
				<?
				$i++;
			}
		}


	}


	else if($type==4)
	{
		$sql_sam="SELECT id,sample_mst_id,sample_name_re,gmts_item_id_re,name_re,type_re,remarks_re from sample_development_fabric_acc where sample_mst_id='$up_id' and form_type=3 and  is_deleted=0  and status_active=1  order by id ASC";
		$sql_result =sql_select($sql_sam);  $i=1;
		if(count($sql_result)>0)
		{

			foreach($sql_result as $row)
			{
				?>
				<tr id="tr_<? echo $i;?>" style="height:10px;" class="general">
					<td align="center" id="reSampleId_1">
						<?
						$sql="select a.id,a.sample_name,b.id as dtls_id from  lib_sample a ,sample_development_dtls b where  a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id=b.sample_name and b.entry_form_id=341 and b.sample_mst_id='$up_id' group by a.id,a.sample_name,b.id order by b.id";
						$samp_array=array();
						$samp_result=sql_select($sql);
						if(count($samp_result)>0)
						{
							foreach($samp_result as $keys=>$vals)
							{
								$samp_array[$vals[csf("id")]]=$vals[csf("sample_name")];
							}

						}

						echo create_drop_down( "cboReSampleName_$i", 140, $samp_array,"", '', "",$row[csf("sample_name_re")],"","");
						?>

					</td>

					<td align="center" id="reItemIid_1">
						<?
						$sql_gmts_re=sql_select("select id,gmts_item_id from sample_development_dtls where is_deleted=0  and status_active=1 and entry_form_id=341 and sample_mst_id='$up_id'");
						$gmts="";
						foreach ($sql_gmts_re as $rowss)
						{
							$gmts.=$rowss[csf("gmts_item_id")].",";
						}
						echo create_drop_down( "cboReGarmentItem_$i", 140, $garments_item,"", 1, "Select Item",$row[csf("gmts_item_id_re")], "","",$gmts);
						?>

						<input type="hidden" id="updateidRequiredEmbellishdtl_<? echo $i;?>" name="updateidRequiredEmbellishdtl_<? echo $i;?>"   style="width:20px;" value="<? echo $row[csf("id")]; ?>" class="text_boxes"/>
						<input type="hidden" id="txtDeltedIdRe" name="txtDeltedIdRe"   style="width:20px;" value="" class="text_boxes"/>
					</td>
					<td align="center" id="re_name_1">
						<?
						       // $sql="select id,item_name from  lib_garment_item where status_active=1 and is_deleted=0";
						echo create_drop_down( "cboReName_$i", 140, $emblishment_name_array,"", 1, "Select Name", $row[csf("name_re")], "cbotype_loder($i);");

						?>
					</td>
					<td align="center" id="reType_<? echo $i ?>">
						<?
						$type_array=array(1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type);
						echo create_drop_down( "cboReType_$i", 140, $type_array[$row[csf("name_re")]],"", 1, "Select Type",$row[csf("type_re")] , "");

						?>
					</td>
					<td align="center" id="re_remarks_1">
						<input style="width:90px;" type="text" class="text_boxes"  name="txtReRemarks_<? echo $i;?>" id="txtReRemarks_<? echo $i;?>" placeholder="write" value="<? echo $row[csf("remarks_re")]; ?>"/>
					</td>



					<td id="re_image_1"><input type="button" class="image_uploader" name="reTxtFile_<? echo $i;?>" id="reTxtFile_<? echo $i;?>" size="20" style="width:170px;" value="CLICK TO ADD/VIEW IMAGE" onClick="file_uploader ( '../../../', document.getElementById('updateidRequiredEmbellishdtl_<? echo $i;?>').value,'', 'required_embellishment_1', 0 ,1);"></td>
					<td width="70">
						<input type="button" id="increasere_<? echo $i; ?>" name="increasere_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_re_tr(<? echo $i; ?>)" />
						<input type="button" id="decreasere_<? echo $i; ?>" name="decreasere_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_re_deleteRow(<? echo $i; ?>);" />
					</td>
				</tr>
				<?
				$i++;
			}
		}

	}
	exit();
}

if ($action=="load_drop_down_buyer_req")
{
	echo create_drop_down( "cbo_buyer_name", 157, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'sweater_sample_requisition_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'sweater_sample_requisition_controller', this.value+'*'+1, 'load_drop_down_brand', 'brand_td');" );
	exit();
}


if($action=="requisition_id_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sweater Sample Requisition Info","../../../../", 1, 1, $unicode);
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
		
	function show_system_id(){
		
		if(document.getElementById('txt_requisition_num').value==''   &&  document.getElementById('txt_style_name1').value==''  && ( document.getElementById('txt_date_from').value=='' || document.getElementById('txt_date_to').value=='')){
			var fillData="cbo_company_mst*txt_date_from*txt_date_to";
			var fillMessage=" Company Name*Est. Ship From Date*Est. Ship To Date";
		}
		else
		{
			var fillData="cbo_company_mst";
			var fillMessage="Company Name Stage";
		}
		
		if (form_validation(fillData,fillMessage)==false)
		{
			return;
		}
		else{
			show_list_view ( document.getElementById('cbo_search_category').value+'_'+document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_style_name1').value+'_'+document.getElementById('txt_requisition_num').value, 'create_requisition_id_search_list_view', 'search_div', 'sweater_sample_rating_controller', 'setFilterGrid(\'list_view\',-1)')
		}
	}
		
		
    </script> 
 </head>
 <body>
	<div align="center" style="width:800px;" >
	<form name="searchsampledevelopmentfrm_1"  id="searchsampledevelopmentfrm_1" autocomplete="off">
		<table width="800" cellspacing="0" cellpadding="0" align="center">
    		<tr>
        		<td align="center" width="100%">
            		<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                        <thead>
                        	<th  colspan="7">
                              <? echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" ); ?>
                            </th>
                        </thead>
                        <thead>
                        	<th class="must_entry_caption" width="140">Company Name</th>
                            <th width="157">Buyer Name</th>                            
                            <th width="70">Requisition No</th>                          
                            <th  width="120" >Style Name</th>
                            <th width="160">Requisition Date</th>
                            <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                        </thead>
        				<tr>
                        	<td width="140">
                            	<input type="hidden" id="selected_job">
								<?
                                    echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company_id,"load_drop_down( 'sweater_sample_requisition_controller', this.value, 'load_drop_down_buyer_req', 'buyer_td_req' );" );
                                ?>
                    		</td>
                   			<td id="buyer_td_req" width="157">
								 <?
                                    echo create_drop_down( "cbo_buyer_name", 157, $blank_array,'', 1, "-- Select Buyer --" );
                                ?>
                            </td>
                          
                            <td width="70">
								<input type="text" style="width:70px" class="text_boxes" name="txt_requisition_num" id="txt_requisition_num"  />
                            </td>

                           
        				
                            <td width="90" align="center">
                                <input type="text" style="width:90px" class="text_boxes"  name="txt_style_name1" id="txt_style_name1"  />
                            </td>


                            <td  width="160">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px"> To
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px">
                            </td>
                            <td align="center" width="80">
                                <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_system_id()" style="width:80px;" />
                            </td>
        				</tr>
                        <tr>
                            <td colspan="11" align="center"><?=load_month_buttons(1); ?></td>
                        </tr>
             		</table>
          		</td>
        	</tr>
        	<tr>
            	<td align="center" valign="top" id="search_div"></td>
        	</tr>
    	</table>
    </form>
	</div>
 </body>
 <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
 </html>
 <?
 exit();
}


if($action=="sample_rating_id_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sweater Sample Requisition Info","../../../../", 1, 1, $unicode);
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
		
	function show_system_id(){
		
		if(document.getElementById('txt_requisition_num').value=='' &&  document.getElementById('txt_rating_num').value==''  &&  document.getElementById('txt_style_name').value==''  && ( document.getElementById('txt_date_from').value=='' || document.getElementById('txt_date_to').value=='')){
			var fillData="cbo_company_mst*txt_date_from*txt_date_to";
			var fillMessage=" Company Name*Est. Ship From Date*Est. Ship To Date";
		}
		else
		{
			var fillData="cbo_company_mst";
			var fillMessage="Company Name Stage";
		}
		
		if (form_validation(fillData,fillMessage)==false)
		{
			return;
		}
		else{
			show_list_view ( document.getElementById('cbo_search_category').value+'_'+document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_style_name').value+'_'+document.getElementById('txt_requisition_num').value+'_'+document.getElementById('txt_rating_num').value, 'create_rating_search_list_view', 'search_div', 'sweater_sample_rating_controller', 'setFilterGrid(\'list_view\',-1)')
		}
	}
		
		
    </script> 
 </head>
 <body>
	<div align="center" style="width:900px;" >
	<form name="searchsampledevelopmentfrm_1"  id="searchsampledevelopmentfrm_1" autocomplete="off">
		<table width="900" cellspacing="0" cellpadding="0" align="center">
    		<tr>
        		<td align="center" width="100%">
            		<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                        <thead>
                        	<th  colspan="7">
                              <? echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" ); ?>
                            </th>
                        </thead>
                        <thead>
                        	<th class="must_entry_caption" width="140">Company Name</th>
                            <th width="157">Buyer Name</th>                            
                            <th width="70">Req. No</th>   
							<th width="70">Rating Id</th>                           
                            <th  width="120" >Style Name</th>
                            <th width="160">Rating Date</th>
                            <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                        </thead>
        				<tr>
                        	<td width="140">
                            	<input type="hidden" id="selected_job">
								<?
                                    echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company_id,"load_drop_down( 'sweater_sample_requisition_controller', this.value, 'load_drop_down_buyer_req', 'buyer_td_req' );" );
                                ?>
                    		</td>
                   			<td id="buyer_td_req" width="157">
								 <?
                                    echo create_drop_down( "cbo_buyer_name", 157, $blank_array,'', 1, "-- Select Buyer --" );
                                ?>
                            </td>
                          
                            <td width="70">
								<input type="text" style="width:70px" class="text_boxes" name="txt_requisition_num" id="txt_requisition_num"  />
                            </td>
							<td width="70">
								<input type="text" style="width:70px" class="text_boxes" name="txt_rating_num" id="txt_rating_num"  />
                            </td>

                           
        				
                            <td width="90" align="center">
                                <input type="text" style="width:90px" class="text_boxes"  name="txt_style_name" id="txt_style_name"  />
                            </td>


                            <td  width="160">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px"> To
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px">
                            </td>
                            <td align="center" width="80">
                                <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_system_id()" style="width:80px;" />
                            </td>
        				</tr>
                        <tr>
                            <td colspan="11" align="center"><?=load_month_buttons(1); ?></td>
                        </tr>
             		</table>
          		</td>
        	</tr>
        	<tr>
            	<td align="center" valign="top" id="search_div"></td>
        	</tr>
    	</table>
    </form>
	</div>
 </body>
 <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
 </html>
 <?
 exit();
}
?>
