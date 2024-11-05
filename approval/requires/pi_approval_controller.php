<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_ip=$_SESSION['logic_erp']['user_ip'];

extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');
require_once('../../mailer/class.phpmailer.php');
$from_mail="PLATFORM-ERP@fakir.app";
	
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];
//==========================================================================================
$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
	exit();
}

if($action=="load_drop_down_buyer_new_user")
{
	$data=explode("_",$data);
//	echo "SELECT password,user_level,id,access_ip,buyer_id,unit_id,is_data_level_secured FROM user_passwd WHERE user_name = '$data[1]' AND valid = 1";die;
	$log_sql = sql_select("SELECT user_level,buyer_id,unit_id,is_data_level_secured FROM user_passwd WHERE id = '$data[1]' AND valid = 1"); 
	//print_r($log_sql);die;
	foreach($log_sql as $r_log)
	{
		if($r_log[csf('IS_DATA_LEVEL_SECURED')]==1)
		{
			if($r_log[csf('BUYER_ID')]!="") $buyer_cond=" and buy.id in (".$r_log[csf('BUYER_ID')].")"; else $buyer_cond="";
		}
		else $buyer_cond="";
	}
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
	exit();	
}

if ($action=="load_supplier_dropdown")
{
	$data = explode('_',$data);
	
			echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type =9 and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);

	exit();
	
	/*
	
	if($data[1]==0) 
	{
		echo create_drop_down( "cbo_supplier_id", 151, $blank_array,'', 1, '-- Select Supplier --',0,'',0);
	}
	
	else if($data[1]==1)
	{
		echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type =2 and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);		
	}
	else if($data[1]==2 || $data[1]==3 || $data[1]==13 || $data[1]==14)
	{
		echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type =9 and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
	}
	else if($data[1]==4)
	{
		echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type in(4,5) and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
		
	}
	else if($data[1]==5 || $data[1]==6 || $data[1]==7)
	{
		echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type=3 and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
	}
	else if($data[1]==9 || $data[1]==10)
	{
		echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type = 6 and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
	}
	else if($data[1]==11)
	{
		echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type = 8 and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
	}
	else if($data[1]==12 || $data[1]==24 || $data[1]==25)
	{
		echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type in(20,21,22,23,24,30,31,32,35,36,37,38,39) and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
	}
	else if($data[1]==32)
	{
		echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type in(92) and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
	}
	else
	{
		echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type = 7 and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);

	} 
	
	*/
	
}

/*
if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
	exit();
}
*/

if($action=='user_popup'){
echo load_html_head_contents("Popup Info","../../",1, 1,'',1,'');
?>	

<script>

// flowing script for multy select data------------------------------------------------------------------------------start;
  function js_set_value(id)
  { 
 // alert(id)
	document.getElementById('selected_id').value=id;
	  parent.emailwindow.hide();
  }

// avobe script for multy select data------------------------------------------------------------------------------end;

</script>

<form>
        <input type="hidden" id="selected_id" name="selected_id" /> 
       <?php
        $custom_designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');	
		 $Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');	;
		 	//$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
			$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and valid=1 and a.id!=$user_id  and b.is_deleted=0  and b.page_id=$menu_id";
			//echo $sql;
		 $arr=array (2=>$custom_designation,3=>$Department);
		 echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
		?>
        
</form>
<script language="javascript" type="text/javascript">
  setFilterGrid("tbl_style_ref");
</script>


<?
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$sequence_no='';
	$company_name=str_replace("'","",$cbo_company_name);
	$txt_system_id=str_replace("'","",$txt_system_id);
	$txt_pi_no=str_replace("'","",$txt_pi_no);
	
	if($txt_system_id) $system_id_cond=" and a.id=".$txt_system_id.""; else $system_id_cond=""; 
	if($txt_pi_no) $pi_number_cond=" and a.pi_number='".$txt_pi_no."'"; else $pi_number_cond="";

	$date_cond='';
	if(str_replace("'","",$txt_date)!="")
	{
		if(str_replace("'","",$cbo_get_upto)==1) $date_cond=" and a.pi_date>$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==2) $date_cond=" and a.pi_date<=$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==3) $date_cond=" and a.pi_date=$txt_date";
		else $date_cond='';
	}

	$approval_type=str_replace("'","",$cbo_approval_type);
	
	$cbo_supplier_id = str_replace("'","",$cbo_supplier_id);
	if($cbo_supplier_id==0){$cbo_supplier_id="'%%'";}
	
	
	//$user_id=181;
	$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$job_dealing_merchant_array = return_library_array("select job_no, dealing_marchant from wo_po_details_master","job_no","dealing_marchant");

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0","seq");
	
	
	//var_dump($buyer_ids_array);
	
	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Fabric Booking.</font>";
		die;
	}
	
	if($db_type==0) 
	{
		$year_field="YEAR(a.insert_date) as year"; 
		$orderBy_cond="IFNULL";
	}
	else if($db_type==2) 
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
		$orderBy_cond="NVL";
	}
	else 
	{
		$year_field="";//defined Later
		$orderBy_cond="ISNULL";
	}

	if($approval_type==0)
	{
		if($db_type==0)
		{
			$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id='' and is_deleted=0","seq");
		}
		else
		{
			$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
		}
		
		
		if($user_sequence_no==$min_sequence_no)
		{	
			
			$sql="select a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, sum(b.net_pi_amount) as net_pi_amount,a.update_date, a.insert_date,a.is_apply_last_update
			from com_pi_master_details a, com_pi_item_details b 
			where a.id=b.pi_id and a.supplier_id like $cbo_supplier_id and a.importer_id=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved=$approval_type and b.quantity>0 $buyer_id_cond $buyer_id_cond2 $date_cond $system_id_cond $pi_number_cond group by  a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, a.update_date, a.insert_date ,a.is_apply_last_update order by $orderBy_cond(a.update_date, a.insert_date) desc";
		  //echo $sql;	die; 
		}
		else if($sequence_no=="")
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
			
			if($db_type==0)
			{
				//$sequence_no_by=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0");
				$seqSql="select group_concat(sequence_no) as sequence_no_by,
 group_concat(case when bypass=2 and buyer_id<>'' then buyer_id end) as buyer_ids from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
				$seqData=sql_select($seqSql);
				
				$sequence_no_by=$seqData[0][csf('sequence_no_by')];
				
				
				$booking_id=return_field_value("group_concat(distinct(mst_id)) as booking_id","com_pi_master_details a, approval_history b","a.id=b.mst_id and a.importer_id=$company_name and b.sequence_no in ($sequence_no_by) and b.entry_form=21 and b.current_approval_status=1  $date_cond","booking_id");
				
				$booking_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as booking_id","com_pi_master_details a, approval_history b","a.id=b.mst_id and a.importer_id=$company_name and b.sequence_no=$user_sequence_no and b.entry_form=21 and b.current_approval_status=1","booking_id");
			}
			else
			{
				
				$seqSql="select LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no_by, LISTAGG(case when bypass=2 and buyer_id is not null then buyer_id end, ',') WITHIN GROUP (ORDER BY null) as buyer_ids from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
				$seqData=sql_select($seqSql);
				
				$sequence_no_by=$seqData[0][csf('sequence_no_by')];
				
				
				$booking_id=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as booking_id","com_pi_master_details a, approval_history b","a.id=b.mst_id and a.importer_id=$company_name and b.sequence_no in ($sequence_no_by) and b.entry_form=21 and b.current_approval_status=1 $buyer_id_cond $date_cond","booking_id");
				$booking_id=implode(",",array_unique(explode(",",$booking_id)));
				
				$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as booking_id","com_pi_master_details a, approval_history b","a.id=b.mst_id and a.importer_id=$company_name and b.sequence_no=$user_sequence_no and b.entry_form=21 and b.current_approval_status=1","booking_id");
				$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
			}
			 //echo $booking_id;die;
			$result=array_diff(explode(',',$booking_id),explode(',',$booking_id_app_byuser));
			$booking_id=implode(",",$result);
			
			if($db_type==0)
			{
				if($booking_id!="")
				{
					$sql="a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, sum(b.net_pi_amount) as net_pi_amount,a.update_date, a.insert_date ,a.is_apply_last_update
					from com_pi_master_details a, com_pi_item_details b
					where a.id=b.pi_id and a.supplier_id like $cbo_supplier_id and a.importer_id=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved=$approval_type and b.quantity>0
					group by  a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, a.update_date, a.insert_date,a.is_apply_last_update
						union all
						a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, sum(b.net_pi_amount) as net_pi_amount,a.update_date, a.insert_date ,a.is_apply_last_update
						from com_pi_master_details a, com_pi_item_details b
						where a.id=b.pi_id and a.supplier_id like $cbo_supplier_id and a.importer_id=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved=1 and b.quantity>0 and a.id in($booking_id)  $date_cond  $system_id_cond $pi_number_cond
						group by  a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, a.update_date, a.insert_date,a.is_apply_last_update
						order by $orderBy_cond(ob_update, ob_insertdate) desc";
				}
				else
				{
					$sql="select a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, sum(b.net_pi_amount) as net_pi_amount,a.update_date, a.insert_date ,a.is_apply_last_update
					from com_pi_master_details a, com_pi_item_details b
					where a.id=b.pi_id and a.supplier_id like $cbo_supplier_id and a.importer_id=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.quantity>0   $date_cond $system_id_cond $pi_number_cond 
					group by  a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, a.update_date, a.insert_date ,a.is_apply_last_update
					order by $orderBy_cond(a.update_date, a.insert_date) desc";
				}
				//echo $sql;
			}
			else
			{
				if($booking_id!="")
				{
					$sql="select * from(select a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, sum(b.net_pi_amount) as net_pi_amount,a.update_date, a.insert_date ,a.is_apply_last_update
					from com_pi_master_details a, com_pi_item_details b
					where a.id=b.pi_id and a.supplier_id like $cbo_supplier_id and a.importer_id=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved=$approval_type and b.quantity>0  $date_cond   $system_id_cond $pi_number_cond
					group by a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, a.update_date, a.insert_date,a.is_apply_last_update
						union all
						select a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, sum(b.net_pi_amount) as net_pi_amount,a.update_date, a.insert_date ,a.is_apply_last_update
						from com_pi_master_details a, com_pi_item_details b
						where a.id=b.pi_id and a.supplier_id like $cbo_supplier_id and a.importer_id=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved=1 and b.quantity>0 and a.id in($booking_id)   $date_cond $system_id_cond $pi_number_cond
						group by a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, a.update_date, a.insert_date,a.is_apply_last_update) order by $orderBy_cond(update_date, insert_date) desc";
				
				
				}
				else
				{
					$sql="select a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, sum(b.net_pi_amount) as net_pi_amount,a.update_date, a.insert_date ,a.is_apply_last_update
					from com_pi_master_details a, com_pi_item_details b 
					where a.id=b.pi_id and a.supplier_id like $cbo_supplier_id and a.importer_id=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved=$approval_type and b.quantity>0   $date_cond  $system_id_cond $pi_number_cond
					group by a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, a.update_date, a.insert_date,a.is_apply_last_update order by $orderBy_cond(a.update_date, a.insert_date) desc";
				}
				
			}
			
		}
		else
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
			
			$user_sequence_no=$user_sequence_no-1;
			//echo $sequence_no;
			if($sequence_no==$user_sequence_no) 
			{
				$sequence_no_by_pass='';
			}
			else
			{
				if($db_type==0)
				{
					$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");
				}
				else
				{
					$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");	
				}
				
				if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
				else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
				$sql="select a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, sum(b.net_pi_amount) as net_pi_amount,a.update_date, a.insert_date,a.is_apply_last_update from com_pi_master_details a, approval_history b where a.id=b.mst_id and b.entry_form=21 and a.importer_id=$company_name and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.ready_to_approved=1 and a.approved=1   $sequence_no_cond $date_cond $system_id_cond $pi_number_cond order by $orderBy_cond(a.update_date, a.insert_date) desc";
			}
			
		}
		
		
	}
	else
	{
		$buyer_ids=$buyer_ids_array[$user_id]['u'];
		if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
		//$sequence_no_cond=" and b.sequence_no='$user_sequence_no'";
		$sequence_no_cond=" and c.approved_by='$user_id'";
		$sql="select a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, sum(b.net_pi_amount) as net_pi_amount,a.update_date, a.insert_date,a.is_apply_last_update,c.id as approval_id from com_pi_master_details a, com_pi_item_details b,approval_history c where a.id=b.pi_id and b.pi_id=c.mst_id and c.entry_form=21 and a.importer_id=$company_name and a.status_active=1 and a.is_deleted=0 and c.current_approval_status=1 and a.ready_to_approved=1 and a.approved=1  $sequence_no_cond $date_cond $system_id_cond $pi_number_cond group by a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved,a.update_date, a.insert_date,a.is_apply_last_update,c.id
		 order by $orderBy_cond(a.update_date, a.insert_date) desc";
	}
	   // echo $sql;//die;
	?>
    
    <script>
	
		function openmypage_app_cause(wo_id,app_type,i)
		{
			var txt_appv_cause = $("#txt_appv_cause_"+i).val();	
			var approval_id = $("#approval_id_"+i).val();
			
			var data=wo_id+"_"+app_type+"_"+txt_appv_cause+"_"+approval_id;
			
			var title = 'Approval Cause Info';	
			var page_link = 'requires/pi_approval_controller.php?data='+data+'&action=appcause_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','');
			
			emailwindow.onclose=function()
			{
				var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
				$('#txt_appv_cause_'+i).val(appv_cause.value);
			}
		}
		
		function openmypage_app_instrac(wo_id,app_type,i)
		{
			var txt_appv_instra = $("#txt_appv_instra_"+i).val();	
			var approval_id = $("#approval_id_"+i).val();
			
			var data=wo_id+"_"+app_type+"_"+txt_appv_instra+"_"+approval_id;
			
			var title = 'Approval Instruction';	
			var page_link = 'requires/pi_approval_controller.php?data='+data+'&action=appinstra_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','');
			
			emailwindow.onclose=function()
			{
				var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
				
				$('#txt_appv_instra_'+i).val(appv_cause.value);
			}
		}
		
		function openmypage_unapp_request(wo_id,app_type,i)
		{
			var data=wo_id;
			
			var title = 'Un Approval Request';	
			var page_link = 'requires/pi_approval_controller.php?data='+data+'&action=unappcause_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=180px,center=1,resize=1,scrolling=0','');
			
			emailwindow.onclose=function()
			{
				var unappv_request=this.contentDoc.getElementById("hidden_unappv_request");
				
				$('#txt_unappv_req_'+i).val(unappv_request.value);
			}
		}
		
	</script>
    
    <?
		 if($approval_type==0)
		 {
			$fset=1020;
			$table1=1000; 
			$table2=982; 
		 }
		 else if($approval_type==1)
		 {
			$fset=1020;
			$table1=1000; 
			$table2=982; 
		 }
		 
		$print_report_format_ids2=return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name."  and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");
		$format_ids=explode(",",$print_report_format_ids2);
	
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:<? echo $fset; ?>px; margin-top:10px">
        <legend>PI Approval</legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table1; ?>" class="rpt_table" >
                <thead>
                	<th width="50"></th>
                    <th width="40">SL</th>
                    <th width="100">PI No</th>
                    <th width="70">Supplier</th>
                    <th width="100">File no</th>
                    <th width="80">PI Date</th>
                    <th width="100">Last Shipt date</th>
                    <th width="125">Item Category</th>
                    <th width="160">Amount</th>
                    <th width="100">Source</th>
                    <th>Year</th>
                </thead>
            </table>            
            <div style="width:<? echo $table1; ?>px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table2; ?>" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
                            $i=1; $all_approval_id='';
                            $nameArray=sql_select( $sql );
                            foreach ($nameArray as $row)
                            {
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
									
								
								$value=$row[csf('id')];
								
								if($row[csf('booking_type')]==4) 
								{
									$booking_type="Sample";
									$type=3;
								}
								else
								{
									if($row[csf('is_short')]==1) $booking_type="Short"; else $booking_type="Main"; 
									$type=$row[csf('is_short')];
								}
								
								$dealing_merchant=$dealing_merchant_array[$job_dealing_merchant_array[$row[csf('job_no')]]];
								
								if($row[csf('approval_id')]==0)
								{
									$print_cond=1;
								}
								else
								{
									if($duplicate_array[$row[csf('entry_form')]][$row[csf('id')]][$row[csf('sequence_no')]][$row[csf('approved_by')]]=="")
									{
										$duplicate_array[$row[csf('entry_form')]][$row[csf('id')]][$row[csf('sequence_no')]][$row[csf('approved_by')]]=$row[csf('id')];
										$print_cond=1;
									}
									else
									{
										if($all_approval_id=="") $all_approval_id=$row[csf('approval_id')]; else $all_approval_id.=",".$row[csf('approval_id')];
										$print_cond=0;
									}
								}
								if($print_cond==1)
								{	
									
							foreach($format_ids as $row_id)
								{
								
									if($row_id==1)
									{ 
									
									/*echo $print_btn=' <input type="button" value="FB GR" onClick="generate_worder_report("'.$row[csf('booking_no')].'","'.$row[csf('company_id')].'","'.$row[csf('po_break_down_id')].'","'.$row[csf('item_category')].'","'.$row[csf('fabric_source')].'","'.$row[csf('job_no')].'","'.$row[csf('is_approved')].'","'.show_fabric_booking_report_gr.'","'.$i.'")"  style="width:50px;" name="print_gr" id="print_gr"  /> ';*/
									
									 $variable="<input type='button' value='FB GR' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','show_fabric_booking_report_gr','".$i."')\" style='width:50px;' class='formbutton' name='print_gr' id='print_gr' />";
									?>
									
									<?
										
									}
									if($row_id==2)
									{ 
									
									
									 $variable="<input type='button' value='PB F1' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','show_fabric_booking_report','".$i."')\" style='width:50px;' name='print_pbf' id='print_f1' class='formbutton' />";
									?>
									  
							  
									
								   <? }
								   if($row_id==3)
									{ 
									 $variable="<input type='button' value='PB F2' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','show_fabric_booking_report3','".$i."')\" style='width:50px;' name='print_pb' id='print_f2' class='formbutton' />";
									?>
									
									
								   <? }
								   
									if($row_id==4)
									{ 
									 $variable="<input type='button' value='PFC F1' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','show_fabric_booking_report1','".$i."')\" style='width:50px;' name='print_p' id='print_pfc' class='formbutton' />";
									?>
									
									
								   <? }
								   
								   if($row_id==5)
									{ 
									 $variable="<input type='button' value='PFC F2' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','show_fabric_booking_report2','".$i."')\" style='width:50px;' name='print_pfc' id='print_f2' class='formbutton' />";
									?>
									
									
									<?
									}
									if($row_id==6)
									{ 
									 $variable="<input type='button' value='FB F1' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','show_fabric_booking_report4','".$i."')\" style='width:50px;' name='print_fb' id='print_gr' class='formbutton' />";
									?>
									
								   <? }
								   
								   if($row_id==7)
									{ 
									 $variable="<input type='button' value='FB F2' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','show_fabric_booking_report5','".$i."')\" style='width:50px;' name='print_fb' id='print_fbf2' class='formbutton' />";
									?>
									
								   <? }
								   
								}
								?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
										<td width="50" align="center" valign="middle">
											<input type="checkbox" id="tbl_<? echo $i;?>" name="tbl[]" onClick="check_last_update(<? echo $i;?>);" />
											<input id="booking_id_<? echo $i;?>" name="booking_id[]" type="hidden" value="<? echo $value; ?>" />
											<input id="booking_no_<? echo $i;?>" name="booking_no[]" type="hidden" value="<? echo $row[csf('pi_number')]; ?>" />
											<input id="approval_id_<? echo $i;?>" name="approval_id[]" type="hidden" value="<? echo $row[csf('approval_id')]; ?>" />
                                            <input id="last_update_<? echo $i;?>" name="last_update[]" type="hidden" value="<? echo $row[csf('is_apply_last_update')]; ?>" />
                                            <input id="<? echo strtoupper($row[csf('pi_number')]); ?>" name="no_booook[]" type="hidden" value="<? echo $i;?>" />
										</td> 
										<td width="40" align="center"><? echo $i; ?></td>
                                         <td width="100" align="center"><a href='##' style='color:#000' onClick="print_report('<? echo $company_name.'*'.$row[csf('id')].'*'.$row[csf('item_category_id')];?>','print', '../commercial/import_details/requires/pi_print')"><font color="blue"><b><? echo $row[csf('pi_number')]; ?></b></font></a></td>
										<td width="70"><p><? echo $supplier_arr[$row[csf('supplier_id')]];?></p></td>
                                        <td width="100" align="center"><? echo $row[csf('internal_file_no')]; ?></td>
										<td width="80" align="center"><p><? echo change_date_format($row[csf('pi_date')]); ?></p></td>
										<td width="100" align="center"><? echo change_date_format($row[csf('last_shipment_date')]); ?>&nbsp;</td>
										<td width="125"><p><? echo $item_category[$row[csf('item_category_id')]]; ?>&nbsp;</p></td>
										<td width="160" align="right"><p><? echo $row[csf('net_pi_amount')]; ?>&nbsp;</p></td>
										<td width="100" align="center"><p><? echo $source[$row[csf('source')]]; ?>&nbsp;</p></td>
										<td id="dealing_merchant_<? echo $i;?>"><p><? echo $dealing_merchant; ?>&nbsp;</p></td>
										
                                        
									</tr>
                                    <Input name='txt_appv_instra[]' class='text_boxes' placeholder='Please Browse' ID='txt_appv_instra_"<? echo $i;?>"'  type='hidden'>
									<?
									$i++;
								}
								
								if($all_approval_id!="")
								{
									$con = connect();
									$rID=sql_multirow_update("approval_history","current_approval_status",0,"id",$all_approval_id,1);
									disconnect($con);
								}
							}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="<? echo $table1; ?>" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" >
                    	<input type="checkbox" id="all_check" onClick="check_all('all_check')" />
                        <input type="hidden" name="hide_approval_type" id="hide_approval_type" value="<? echo $approval_type; ?>">
                        <font style="display:none"><? echo $all_approval_id; ?></font>
                    </td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/></td>
				</tfoot>
			</table>
        </fieldset>
    </form>         
<?
	exit();	
}





if ($action=="approve")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//$user_id=181;
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	$msg=''; $flag=''; $response='';
	// $txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if($_REQUEST['txt_alter_user_id']!="") 	$user_id_approval=$_REQUEST['txt_alter_user_id'];
	else						$user_id_approval=$user_id;

	// echo $user_id_approval;die();
	
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");
	
	if($approval_type==0)
	{
		$response=$booking_ids;

		$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");
		
		if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;
		
		$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, user_ip,comments,inserted_by,insert_date"; 
		$id=return_next_id( "id","approval_history", 1 ) ;
		
		$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=21 group by mst_id","mst_id","approved_no");
		
		$approved_status_arr = return_library_array("select id, approved from com_pi_master_details where id in($booking_ids)","id","approved");


		$approved_no_array=array();
		$booking_ids_all=explode(",",$booking_ids);
		$booking_nos_all=explode(",",$booking_nos);
		$app_instru_all=explode(",",$appv_instras);
		$book_nos='';

		for($i=0;$i<count($booking_ids_all);$i++)
		{
			//$val=$booking_nos_all[$i];
			$val=$booking_ids_all[$i];
			$booking_id=$booking_ids_all[$i];
			$app_instru=$app_instru_all[$i];

			$approved_no=$max_approved_no_arr[$booking_id];
			$approved_status=$approved_status_arr[$booking_id];
			
			if($approved_status==0)
			{
				$approved_no=$approved_no+1;
				$approved_no_array[$val]=$approved_no;
				if($book_nos=="") $book_nos=$val; else $book_nos.=",".$val;
			}
			if(($user_sequence_no*1)==0) { echo "seq**".$user_sequence_no; disconnect($con);die; }
			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",21,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."','".$user_ip."',".$app_instru.",".$user_id.",'".$pc_date_time."')"; 
			$id=$id+1;
			
			/*if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",21,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id.",'".$pc_date_time."','".$user_ip."')"; 
			$id=$id+1;*/
			
		}
		
		 //print_r($data_array);die;
		
		
		
		if(count($approved_no_array)>0)
		{
			$approved_string="";
			
			foreach($approved_no_array as $key=>$value)
			{
				$approved_string.=" WHEN $key THEN $value";
			}
			
			$approved_string_mst="CASE id ".str_replace("'",'',$approved_string)." END";
			$approved_string_dtls="CASE id ".str_replace("'",'',$approved_string)." END";
			
			$sql_insert="insert into com_pi_master_details_history(id,approved_no, mst_id, item_category_id, importer_id, supplier_id, pi_number, pi_date, last_shipment_date, pi_validity_date, currency_id, source, hs_code, internal_file_no, intendor_name, pi_basis_id, remarks, total_amount, upcharge, discount, net_total_amount, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted) 
				select	
				'', $approved_string_mst, id, item_category_id, importer_id, supplier_id, pi_number, pi_date, last_shipment_date, pi_validity_date, currency_id, source, hs_code, internal_file_no, intendor_name, pi_basis_id, remarks, total_amount, upcharge, discount, net_total_amount, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from com_pi_master_details where id in ($book_nos)";
					
				  //echo '10**';
				  //echo $sql_insert ;die;	
			
			/*$rID3=execute_query($sql_insert,0);
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			} */
			
			$sql_insert_dtls="insert into com_pi_item_details_history(id, approved_no, dtls_id,pi_id, work_order_no, work_order_id, work_order_dtls_id, determination_id, item_prod_id, item_group, item_description, color_id, item_color, size_id, item_size, count_name, yarn_composition_item1, yarn_composition_percentage1, yarn_composition_item2, yarn_composition_percentage2, fabric_composition, fabric_construction, yarn_type, gsm, dia_width, weight, uom, quantity, rate, amount, net_pi_rate, net_pi_amount, service_type, brand_supplier, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted) 
				select	
				'', $approved_string_dtls, id, pi_id, work_order_no, work_order_id,work_order_dtls_id, determination_id,  item_prod_id,item_group, item_description,  color_id,item_color, size_id, item_size, count_name, yarn_composition_item1, yarn_composition_percentage1,yarn_composition_item2,  yarn_composition_percentage2,  fabric_composition,fabric_construction, yarn_type,gsm,dia_width,weight,uom, quantity, rate, amount, net_pi_rate, net_pi_amount, service_type, brand_supplier, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from com_pi_item_details where pi_id in ($book_nos)";
					
				 //echo '10**';
				// echo $sql_insert_dtls ;die;	
			
			
			/*$rID4=execute_query($sql_insert_dtls,1);
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			} */
		}
		
		
		
		$rID=sql_multirow_update("com_pi_master_details","approved",1,"id",$booking_ids,0);
		
		if($rID) $flag=1; else $flag=0;
		
		if($approval_ids!="")
		{
			$rIDapp=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,0);
			if($flag==1) 
			{
				if($rIDapp) $flag=1; else $flag=0; 
			} 
		}
		
		//echo "18**".$sql_insert_dtls;die;
		//echo "18**insert into approval_history (".$field_array.") Values ".$data_array;die;
		$rID2=sql_insert("approval_history",$field_array,$data_array,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		
		if(count($approved_no_array)>0)
		{
			$rID3=execute_query($sql_insert,0);
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			} 

			$rID4=execute_query($sql_insert_dtls,1);
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			} 
		}
		
		//echo '10**'.$rID2.','.$rID3.','.$rID4;die;
		 //echo '10**'.$rID2.','.$rID3.','.$rID4;die;
		
		/*oci_rollback($con); 
			echo "21**".$sql_insert;die;*/
		if($flag==1) $msg='19'; else $msg='21';
	}
	else
	{
		$rID=sql_multirow_update("com_pi_master_details","approved*ready_to_approved","0*0","id",$booking_ids,0);
		if($rID) $flag=1; else $flag=0;

		//$rID2=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,0);
		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=21 and mst_id in ($booking_ids)";
		$rID2=execute_query($query,1);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
			
		// $data=$user_id."*'".$pc_date_time."'";
		// $rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date",$data,"id",$approval_ids,1);
		$data=$user_id_approval."*'".$pc_date_time."'*".$user_id."*'".$pc_date_time."'";
		$rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date*updated_by*update_date",$data,"id",$approval_ids,1);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		
		$response=$booking_ids;
		
		if($flag==1) $msg='20'; else $msg='22';
	}
	
	//echo '10**'.$rID.','.$rID2.','.$rID3;die;
	//echo '10**'.$data;die;
	
	if($db_type==0)
	{ 
		if($flag==1)
		{
			mysql_query("COMMIT");  
			echo $msg."**".$response;
		}
		else
		{
			mysql_query("ROLLBACK"); 
			echo $msg."**".$response;
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($flag==1)
		{
			oci_commit($con); 
			echo $msg."**".$response;
		}
		else
		{
			oci_rollback($con); 
			echo $msg."**".$response;
		}
	}
	disconnect($con);
	die;
	
}

if($action=="img")
{
	echo load_html_head_contents("Image View", "../../", 1, 1,'','','');
	extract($_REQUEST);
	
?>
	<fieldset style="width:600px; margin-left:5px">
		<div style="width:100%; word-wrap:break-word" id="scroll_body">
             <table border="0" rules="all" width="100%" cellpadding="2" cellspacing="2">
             	<tr>
					<?
					$i=0;
                    $sql="select image_location from common_photo_library where master_tble_id='$job_no' and form_name='knit_order_entry' and file_type=1";
                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
						$i++;
                    ?>
                    	<td align="center"><img width="300px" height="180px" src="../../<? echo $row[csf('image_location')];?>" /></td>
                    <?
						if($i%2==0) echo "</tr><tr>";
                    }
                    ?>
                </tr>
            </table>
        </div>	
	</fieldset>     
<?
exit();
}

if($action=="file")
{
	echo load_html_head_contents("File View", "../../", 1, 1,'','','');
	extract($_REQUEST);
	
?>
	<fieldset style="width:600px; margin-left:5px">
		<div style="width:100%; word-wrap:break-word" id="scroll_body">
             <table border="0" rules="all" width="100%" cellpadding="2" cellspacing="2">
             	<tr>
					<?
					$i=0;
                    $sql="select image_location from common_photo_library where master_tble_id='$job_no' and form_name='knit_order_entry' and file_type=2";
                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
						$i++;
                    ?>
                    	<td width="100" align="center"><a href="../../<? echo $row[csf('image_location')]; ?>"><img width="89" height="97" src="../../file_upload/blank_file.png"><br>File-<? echo $i; ?></a></td>
                    <?
						if($i%6==0) echo "</tr><tr>";
                    }
                    ?>
                </tr>
            </table>
        </div>	
	</fieldset>     
<?
exit();
}

if ($action=="appcause_popup")
{
	echo load_html_head_contents("Approval Cause Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$data_all=explode('_',$data);
	$wo_id=$data_all[0];
	$app_type=$data_all[1];
	$app_cause=$data_all[2];
	$approval_id=$data_all[3];
	
	if($app_cause=="")
	{	
		$sql_cause="select MAX(id) as id from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=7 and user_id='$user_id' and booking_id='$wo_id' and approval_type='$app_type' and status_active=1 and is_deleted=0";				
		$nameArray_cause=sql_select($sql_cause);
		foreach($nameArray_cause as $row)
		{
			$app_cause=return_field_value("approval_cause", "fabric_booking_approval_cause", "id='".$row[csf("id")]."' and status_active=1 and is_deleted=0");
		}
	}
	
	?>
    <script>
	
		$( document ).ready(function() {
			document.getElementById("appv_cause").value='<? echo $app_cause; ?>';
		});
		
		var permission='<? echo $permission; ?>';
		
		function fnc_appv_entry(operation)
		{
			var appv_cause = $('#appv_cause').val();
			
			if (form_validation('appv_cause','Approval Cause')==false)
			{
				if (appv_cause=='')
				{
					alert("Please write cause.");
				}
				return;
			}
			else
			{
				
				var data="action=save_update_delete_appv_cause&operation="+operation+get_submitted_data_string('appv_cause*wo_id*appv_type*page_id*user_id*approval_id',"../../");
				//alert (data);return;
				freeze_window(operation);
				http.open("POST","pi_approval_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange=fnc_appv_entry_Reply_info;
			}
		}
		
		function fnc_appv_entry_Reply_info()
		{
			if(http.readyState == 4) 
			{
				//release_freezing();	
				//alert(http.responseText);return;
			
				var reponse=trim(http.responseText).split('**');	
				show_msg(reponse[0]);
				
				set_button_status(1, permission, 'fnc_appv_entry',1);
				release_freezing();
				
				generate_worder_mail(reponse[2],reponse[3],reponse[4],reponse[5]);
			}
		}
		
		function fnc_close()
		{	
			appv_cause= $("#appv_cause").val();
			
			document.getElementById('hidden_appv_cause').value=appv_cause;
			
			parent.emailwindow.hide();
		}
		
		function generate_worder_mail(woid,mail,appvtype,user)
		{
			var data="action=app_cause_mail&woid="+woid+'&mail='+mail+'&appvtype='+appvtype+'&user='+user;
			//alert (data);return;
			freeze_window(6);
			http.open("POST","pi_approval_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=fnc_appv_mail_Reply_info;
			
		}
		
		function fnc_appv_mail_Reply_info()
		{
			if(http.readyState == 4) 
			{
				var response=trim(http.responseText).split('**');
				/*if(response[0]==222)
				{
					show_msg(reponse[0]);
				}*/
				release_freezing();
			}
		}
			
    </script>
    <body>
		<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
                    	<textarea name="appv_cause" id="appv_cause" class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character"></textarea>
                        <Input type="hidden" name="wo_id" class="text_boxes" ID="wo_id" value="<? echo $wo_id; ?>" style="width:30px" />
                        <Input type="hidden" name="appv_type" class="text_boxes" ID="appv_type" value="<? echo $app_type; ?>" style="width:30px" />
                        <Input type="hidden" name="page_id" class="text_boxes" ID="page_id" value="<? echo $menu_id; ?>" style="width:30px" />
                        <Input type="hidden" name="user_id" class="text_boxes" ID="user_id" value="<? echo $user_id; ?>" style="width:30px" />
                        <Input type="hidden" name="approval_id" class="text_boxes" ID="approval_id" value="<? echo $approval_id; ?>" style="width:30px" />
                    </td>
                </tr>
            </table> 
              
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >
                
                <tr>
                    <td align="center" class="button_container">
                        <? 
						//print_r ($id_up_all);
                            if($id_up!='')
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 1,0,"reset_form('size_1','','','','','');",1);
                            }
                            else
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 0,0,"reset_form('size_1','','','','','');",1);
                            }
                        ?>
                        <input type="hidden" name="hidden_appv_cause" id="hidden_appv_cause" class="text_boxes /">
                        
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                    </td>	  
                </tr>
            </table>
            </fieldset>
            </form>
        </div> 
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="appinstra_popup")
{
	echo load_html_head_contents("Approval Cause Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$data_all=explode('_',$data);
	$wo_id=$data_all[0];
	$app_type=$data_all[1];
	$app_cause=$data_all[2];
	$approval_id=$data_all[3];
	?>
    <script>
	
		$( document ).ready(function() {
			document.getElementById("appv_cause").value='<? echo $app_cause; ?>';
		});
		
		var permission='<? echo $permission; ?>';
		
		function fnc_close()
		{	
			appv_cause= $("#appv_cause").val();
			
			document.getElementById('hidden_appv_cause').value=appv_cause;
			
			parent.emailwindow.hide();
		}
		
		
			
    </script>
    <body>
		<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
                    	<textarea name="appv_cause" id="appv_cause" class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character"></textarea>
                        <Input type="hidden" name="wo_id" class="text_boxes" ID="wo_id" value="<? echo $wo_id; ?>" style="width:30px" />
                        <Input type="hidden" name="appv_type" class="text_boxes" ID="appv_type" value="<? echo $app_type; ?>" style="width:30px" />
                        <Input type="hidden" name="page_id" class="text_boxes" ID="page_id" value="<? echo $menu_id; ?>" style="width:30px" />
                        <Input type="hidden" name="user_id" class="text_boxes" ID="user_id" value="<? echo $user_id; ?>" style="width:30px" />
                        <Input type="hidden" name="approval_id" class="text_boxes" ID="approval_id" value="<? echo $approval_id; ?>" style="width:30px" />
                    </td>
                </tr>
            </table> 
              
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >
                
                <tr>
                    <td align="center" class="button_container">
                        <? 
						//print_r ($id_up_all);
                            /*if($id_up!='')
                            {
                                echo load_submit_buttons($permission, "fnc_appv_instru_entry", 1,0,"reset_form('size_1','','','','','');",1);
                            }
                            else
                            {
                                echo load_submit_buttons($permission, "fnc_appv_instru_entry", 0,0,"reset_form('size_1','','','','','');",1);
                            }*/
                        ?>
                        <input type="hidden" name="hidden_appv_cause" id="hidden_appv_cause" class="text_boxes"/>
                        
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                    </td>	  
                </tr>
            </table>
            </fieldset>
            </form>
        </div> 
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="unappcause_popup")
{
	echo load_html_head_contents("Un Approval Request", "../../", 1, 1,'','','');
	extract($_REQUEST);
	$wo_id=$data;
	$sql_req="select approval_cause from fabric_booking_approval_cause where entry_form=7 and booking_id='$wo_id' and approval_type=2 and status_active=1 and is_deleted=0";				
	$nameArray_req=sql_select($sql_req);
	foreach($nameArray_req as $row)
	{
		$unappv_req=$row[csf('approval_cause')];
	}
	?>
    <script>
	
		//var permission='<?// echo $permission; ?>';
		
		$( document ).ready(function() {
			document.getElementById("unappv_req").value='<? echo $unappv_req; ?>';
		});
		
		
		function fnc_close()
		{	
			unappv_request= $("#unappv_req").val();
			document.getElementById('hidden_unappv_request').value=unappv_request;
			parent.emailwindow.hide();
		}
			
    </script>
    <body>
		<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
                    	<textarea name="unappv_req" id="unappv_req" readonly class="text_area" style="width:430px; height:100px;"></textarea>
                    </td>
                </tr>
            </table> 
              
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >
                <tr>
                    <td align="center">
                    	<input type="hidden" name="hidden_unappv_request" id="hidden_unappv_request" class="text_boxes /">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                    </td>	  
                </tr>
            </table>
            </fieldset>
            </form>
        </div> 
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
<?
	exit();
}


if ($action=="save_update_delete_appv_cause")
{
	//$approval_id
	$approval_type=str_replace("'","",$appv_type);
	
	
	if($approval_type==0)
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
			
			$approved_no_history=return_field_value("approved_no","approval_history","entry_form=7 and mst_id=$wo_id and approved_by=$user_id");
			$approved_no_cause=return_field_value("approval_no","fabric_booking_approval_cause","entry_form=7 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");
			
			//echo "shajjad_".$approved_no_history.'_'.$approved_no_cause; die;
			
			if($approved_no_history=="" && $approved_no_cause=="")
			{
				//echo "insert"; die;
				
				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;
			
				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id_mst.",".$page_id.",7,".$user_id.",".$wo_id." ,".$appv_type.",0,".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
				//echo $rID; die;
				
				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT");
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
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
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						oci_rollback($con); 
						echo "10**".$rID;
					}
				}
				disconnect($con);
				die;	
			}
			else if($approved_no_history=="" && $approved_no_cause!="")
			{
				$con = connect();
				if($db_type==0)
				{
					mysql_query("BEGIN");
				}
				
				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=7 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");
				
				$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
				$data_array="".$page_id."*7*".$user_id."*".$wo_id."*".$appv_type."*0*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
				
				 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);
				
				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT"); 
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id); 
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
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id); 
					}
					else
					{
						oci_rollback($con); 
						echo "10**".$rID;
					}
				}
				disconnect($con);
				die;
			}
			else if($approved_no_history!="" && $approved_no_cause!="")
			{
				$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=7 and mst_id=$wo_id and approved_by=$user_id");
				$max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=7 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");
				
				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;
			
					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id_mst.",".$page_id.",7,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
					//echo $rID; die;
					
					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT");
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
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
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							oci_rollback($con); 
							echo "10**".$rID;
						}
					}
					disconnect($con);
					die;
				}
				else if($max_appv_no_his==$max_appv_no_cause)
				{	
					$con = connect();
					if($db_type==0)
					{
						mysql_query("BEGIN");
					}
					
					$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=7 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");
					
					$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
					$data_array="".$page_id."*7*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
					
					 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);
					
					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT"); 
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id); 
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
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id); 
						}
						else
						{
							oci_rollback($con); 
							echo "10**".$rID;
						}
					}
					disconnect($con);
					die;
				}
			}
			
		}
	
		if ($operation==1)  // Update Here
		{	
			
		}
	
	}//type=0
	if($approval_type==1)
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
		
			$unapproved_cause_id=return_field_value("id","fabric_booking_approval_cause","entry_form=7 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");
			
			$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=7 and mst_id=$wo_id and approved_by=$user_id");
			
			if($unapproved_cause_id=="")
			{
			
				//echo "shajjad_".$unapproved_cause_id; die;
		
				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;
			
				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_history_id,approval_cause,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id_mst.",".$page_id.",7,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$approval_id.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				
				$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
				//echo $rID; die;
		
				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT");
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
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
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						oci_rollback($con); 
						echo "10**".$rID;
					}
				}
			
				disconnect($con);
				die;
			}
			else
			{
				
				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=7 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");
				
				$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_history_id*approval_cause*updated_by*update_date*status_active*is_deleted";
				$data_array="".$page_id."*7*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$approval_id."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
				
				 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);
				
				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT"); 
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id); 
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
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id); 
					}
					else
					{
						oci_rollback($con); 
						echo "10**".$rID;
					}
				}
				disconnect($con);
				die;
			}
		}
		
	}//type=1
}



if ( $action=="app_cause_mail" )
{
	//echo $woid.'_'.$mail.'_'.$appvtype; die;
	ob_start();
	?>
    
        <table width="800" cellpadding="0" cellspacing="0" border="1">
            <tr>
                <td valign="top" align="center"><strong><font size="+2">Subject : Fabric Booking &nbsp;<?  if($appvtype==0) echo "Approval Request"; else echo "Un-Approval Request"; ?>&nbsp;Refused</font></strong></td>
            </tr>
            <tr>
                <td valign="top">
                    Dear Mr. <?   
								$to="";
								
								$sql ="SELECT c.team_member_name FROM wo_booking_mst a,wo_po_details_master b,lib_mkt_team_member_info c where b.job_no=a.job_no and b.dealing_marchant=c.id and a.id=$woid";
								$result=sql_select($sql);
								foreach($result as $row)
								{
									if ($to=="")  $to=$row[csf('team_member_name')]; else $to=$to.", ".$row[csf('team_member_name')]; 
								}
								echo $to;  
							?>
                            <br> Your Fabric Booking No. &nbsp;
							<?
								$sql1 ="SELECT booking_no,buyer_id FROM wo_booking_mst where id=$woid";
								$result1=sql_select($sql1);
								foreach($result1 as $row1)
								{
									$wo_no=$row1[csf('booking_no')]; 
									$buyer=$row1[csf('buyer_id')]; 
								}
								
								
							?>&nbsp;<?  echo $wo_no;  ?>,&nbsp; <? echo $buyer_arr[$buyer]; ?>&nbsp;of buyer has been refused due to following reason. 
                </td>
            </tr>
            <tr>
                <td valign="top">
                    <?  echo $mail; ?>
                </td>
            </tr>
            <tr>
                <td valign="top">
                    Thanks,<br>
					<?
						$user_name=return_field_value("user_name","user_passwd","id=$user"); 
						echo $user_name;  
					?>
                </td>
            </tr>
        </table>
    <?
	
	$to="";
	
	$sql2 ="SELECT c.team_member_email FROM wo_booking_mst a,wo_po_details_master b,lib_mkt_team_member_info c where b.job_no=a.job_no and b.dealing_marchant=c.id and a.id=$woid";
		
		$result2=sql_select($sql2);
		foreach($result2 as $row2)
		{
			if ($to=="")  $to=$row2[csf('team_member_email')]; else $to=$to.", ".$row2[csf('team_member_email')]; 
		}
		
 		$subject="Approval Status";
    	$message="";
    	$message=ob_get_contents();
    	ob_clean();
		
		//echo $message;
		 //$to='akter.babu@gmail.com,saeed@fakirapparels.com,akter.hossain@fakirapparels.com,bdsaeedkhan@gmail.com,shajjadhossain81@gmail.com';
		//$to='shajjad@logicsoftbd.com';
		//$to='shajjadhossain81@gmail.com';
		$header=mail_header();
		
		echo send_mail_mailer( $to, $subject, $message, $header );
		
		/*if (mail($to,$subject,$message,$header))
			echo "****Mail Sent.---".date("Y-m-d");
		else
			echo "****Mail Not Sent.---".date("Y-m-d");*/
		
		//echo "222**".$woid;
		exit();
		
}

if($action=="check_booking_last_update")
{
	$last_update=return_field_value("is_apply_last_update","com_pi_master_details","id='".trim($data)."'");
	echo $last_update;
	exit();	
}


// For Comments
if($action=="show_fabric_comment_report")
{
	echo load_html_head_contents("Approval Cause Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	//$last_update=return_field_value("is_apply_last_update","wo_booking_mst","booking_no='".trim($data)."'");
	//echo $last_update;
?>
<body>
<div>
<table width="870"   cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
 <thead>
   <tr align="center">
    <th colspan="12"><b>Comments</b></th>
    </tr>
    
    <tr>
    <th width="30" rowspan="2">Sl</th>
    <th width="120" rowspan="2">Po NO</th>
    <th width="70" rowspan="2">Ship Date</th>  
    <th width="80" rowspan="2">As Merketing</th>
    <th width="70" rowspan="2">As Budget</th>
    <th width="70" rowspan="2">Mn.Book Qty</th>
    <th width="70" rowspan="2">Sht.Book Qty</th>
    <th width="70" rowspan="2">Smp.Book Qty</th>
    <th  width="70" rowspan="2">Tot.Book Qty</th>
    <th colspan="2">Balance</th>
    <th width="" rowspan="2">Comments ON Budget</th>
    </tr>
    <tr>
    <th width="70">As Mkt.</th>
    <th width="70">As Budget</th>
    </tr>
     </thead>
</table>
<?

	$cbo_fabric_natu=str_replace("'","",$fab_nature);
	$cbo_fabric_source=str_replace("'","",$fab_source);
	if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and a.fab_nature_id='$cbo_fabric_natu'"; 
	if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and a.fabric_source='$cbo_fabric_source'"; 
	 $paln_cut_qnty_array=return_library_array( "select min(id) as id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown  where po_break_down_id in(".str_replace("'","",$order_id).") and is_deleted=0 and status_active=1 group by color_number_id,size_number_id,item_number_id,po_break_down_id", "id", "plan_cut_qnty");
	//print_r( $paln_cut_qnty_array);
	//echo $job_no;
	$item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no ='$job_no'", "gmts_item_id", "set_item_ratio");
	//$item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no ='$job_no'", "gmts_item_id", "set_item_ratio");
//	echo "select quotation_id from wo_po_details_master where job_no='".$job_no."' ";
	$quotation_id = return_field_value(" quotation_id as quotation_id"," wo_po_details_master ","job_no='".$job_no."'","quotation_id");  
	$tot_mkt_cost  = return_field_value(" sum(b.fab_knit_req_kg) as mkt_cost","wo_price_quotation a,wo_pri_quo_sum_dtls b"," a.id=b.quotation_id and a.id='".$quotation_id."'","mkt_cost");
//	print_r( $item_ratio_array);
	$nameArray=sql_select("
	select
	a.id,
	a.item_number_id,
	a.costing_per,
	a.job_no,
	b.po_break_down_id,
	b.color_size_table_id,
	b.requirment,
	c.po_number
FROM
	wo_pre_cost_fabric_cost_dtls a,
	wo_pre_cos_fab_co_avg_con_dtls b,
	wo_po_break_down c
WHERE
	a.job_no=b.job_no and
	a.job_no=c.job_no_mst and
    a.id=b.pre_cost_fabric_cost_dtls_id and
	b.po_break_down_id=c.id and
	b.po_break_down_id in (".str_replace("'","",$order_id).")   and a.status_active=1 and a.is_deleted=0
	order by id");
	$count=0;
	//$cbo_fabric_natu $cbo_fabric_source_cond
	$tot_grey_req_as_pre_cost_arr=array();$tot_grey_req_as_price_cost_arr=array();$tot_grey_req_as_price_cost=0;
	foreach ($nameArray as $result)
	{
		//echo "select quotation_id as quotation_id from wo_po_details_master where job_no='".$result[csf('job_no')]."'";
		// $quotation_id = return_field_value(" quotation_id as quotation_id"," wo_po_details_master ","job_no='".$result[csf('job_no')]."'","quotation_id");  
		if (count($nameArray)>0 )
		{
            if($result[csf("costing_per")]==1)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(12*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
				$dzn_qnty_p=12;
				//$tot_mkt_price=$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(12*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==2)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(1*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
				$dzn_qnty_p=1;
			}
			if($result[csf("costing_per")]==3)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(24*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
				$dzn_qnty_p=12*2;
			}
			if($result[csf("costing_per")]==4)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(36*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
				$dzn_qnty_p=12*3;
			}
			if($result[csf("costing_per")]==5)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(48*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
				$dzn_qnty_p=12*4;
			}
			$dzn_qnty_p=$dzn_qnty_p*$item_ratio_array[$result[csf("item_number_id")]];
			
			  $tot_grey_req_as_price_cost+=($tot_mkt_cost/$dzn_qnty_p)*$paln_cut_qnty_array[$result[csf("color_size_table_id")]];
			//echo $paln_cut_qnty_array[$result[csf("color_size_table_id")]].'='.$tot_mkt_cost.'/'.$dzn_qnty_p.'<br>';
			//$tot_grey_req_as_price_cost_arr[$quotation_id]+=$tot_grey_req_as_price_cost;
			
			$tot_grey_req_as_pre_cost_arr[$result[csf("po_number")]]+=$tot_grey_req_as_pre_cost;
        }
    }
	       // $tot_grey_req_as_pre_cost_arr[$result[csf("po_number")]];
		  // echo $tot_grey_req_as_pre_cost;die;
		   //Price Quotation
		 

		
		//$price_costing_per_id = return_field_value(" costing_per as costing_per"," wo_price_quotation ","id='".$quotation_id."'","costing_per"); 
		//$item_ratio_array=return_library_array( "select total_set_qnty from wo_price_quotation  where id ='$quotation_id'", "gmts_item_id", "set_item_ratio");
		//$item_ratio_array_price = return_field_value(" total_set_qnty as total_set_qnty"," wo_price_quotation ","id='".$quotation_id."'","total_set_qnty");
		   
				/*$dzn_qnty_p=0;
				//$price_costing_per_id=$price_costing_perArray[$row[csf('quotation_id')]]['costing_per'];
				if($price_costing_per_id==1) $dzn_qnty_p=12;
				else if($price_costing_per_id==3) $dzn_qnty_p=12*2;
				else if($price_costing_per_id==4) $dzn_qnty_p=12*3;
				else if($price_costing_per_id==5) $dzn_qnty_p=12*4;
				else $dzn_qnty_p=1;
				$dzn_qnty_p=$dzn_qnty_p*$item_ratio_array_price;*/
				//echo $dzn_qnty_p;
		   
		            $total_pre_cost=0;
					$total_booking_qnty_main=0;
					$total_booking_qnty_short=0;
					$total_booking_qnty_sample=0;
					$total_tot_bok_qty=0;
					$tot_balance=0;
					
					$booking_qnty_main=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b, wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no  and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$order_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and a.is_short=2 and c.item_category=2 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
					
					$booking_qnty_short=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$order_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and c.item_category=2 and a.is_short=1 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
					$booking_qnty_sample=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c  where a.job_no =b.job_no_mst and  a.job_no=c.job_no and  a.booking_no=c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$order_id).") and a.booking_type =4 and c.fabric_source=$cbo_fabric_source and c.item_category=2  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
					$sql_data=sql_select( "select max(a.id) as id,  a.po_number,max(a.pub_shipment_date) as pub_shipment_date,sum(a.plan_cut) as plan_cut  from wo_po_break_down a,wo_pre_cost_sum_dtls b,wo_pre_cost_mst c where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no and a.id in(".str_replace("'","",$order_id).") group by a.po_number order by id");





 
//costing_per
//$sql= "select a.id,a.company_id, a.buyer_id,b.system_number_prefix_num,b.buyer_request, a.style_ref,a.style_desc,a.pord_dept,a.offer_qnty,a.est_ship_date from  wo_price_quotation a left join wo_quotation_inquery b on a.inquery_id=b.id and b.status_active=1  and b.is_deleted=0 where a.status_active=1  and a.is_deleted=0  $company $buyer $est_ship_date $quotation_id_cond $style_cond $inquery_cond $buyer_request_cond order by id";

?>
<div style="width:890px; max-height:400px; overflow-y:scroll" id="scroll_body">
<table width="870"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
<?
$k=0;$total_price_mkt_cost=0;
foreach($sql_data  as $row)
	{
		 if ($i%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
	$k++;
	//tot_grey_req_as_price_cost_arr
	$quotation_id = return_field_value(" a.quotation_id as quotation_id"," wo_po_details_master a,wo_po_break_down b ","a.job_no=b.job_no_mst and b.po_number='".$row[csf('po_number')]."'","quotation_id");  
	?>
<tr bgcolor="<? echo $bgcolor; ?>">
    <td width="30"> <? echo $k; ?> </td>
    <td width="120"><p><? echo $row[csf("po_number")]; ?></p> </td>
    <td width="70" align="right"><? echo change_date_format($row[csf("pub_shipment_date")],"dd-mm-yyyy",'-'); ?> </td>
    <td width="80" align="right"><? $total_price_mkt_cost+=$tot_grey_req_as_price_cost;echo number_format($tot_grey_req_as_price_cost,2);?> </td>
    <td width="70" align="right"><?  echo number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]],2); $total_pre_cost+=$tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]];?> </td>
    <td width="70" align="right"><? echo number_format($booking_qnty_main[$row[csf("id")]],2); $total_booking_qnty_main+=$booking_qnty_main[$row[csf("id")]];?> </td>
    <td width="70" align="right"> <? echo number_format($booking_qnty_short[$row[csf("id")]],2); $total_booking_qnty_short+=$booking_qnty_short[$row[csf("id")]];?></td>
    <td width="70" align="right"><? echo number_format($booking_qnty_sample[$row[csf("id")]],2); $total_booking_qnty_sample+=$booking_qnty_sample[$row[csf("id")]];?></td>
    <td width="70" align="right">	<? $tot_bok_qty=$booking_qnty_main[$row[csf("id")]]+$booking_qnty_short[$row[csf("id")]]+$booking_qnty_sample[$row[csf("id")]]; echo number_format($tot_bok_qty,2); $total_tot_bok_qty+=$tot_bok_qty;?> </td>
    <td width="70" align="right"> <? $balance= def_number_format($total_price_mkt_cost-$tot_bok_qty,2,""); echo number_format($balance,2); $tot_balance+= $balance?></td>
    <td width="70" align="right"> <?  $total_pre_cost_bal=$tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]-$tot_bok_qty;$tot_pre_cost_bal+=$total_pre_cost_bal;echo number_format($total_pre_cost_bal,2); ?></td>
    <td width="">
    <p>
     <? 
	$pre_cost= $tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]];
	
	if( $total_pre_cost_bal>0)
	{
		echo "Less Booking";
	}
	else if ($total_pre_cost_bal<0) 
	{
		echo "Over Booking";
	} 
	else if ($pre_cost==$tot_bok_qty) 
	{
		echo "As Per";
	} 
	else
	{
		echo "";
	}
	?>
    </p>
	</td>
</tr>
<?
	}
?>
<tfoot>
    <tr>
    <td colspan="3">Total:</td>
    <td align="right"><? echo number_format($total_price_mkt_cost,2); ?></td>
     <td align="right"><? echo number_format($total_pre_cost,2); ?></td>
    <td align="right"><? echo number_format($total_booking_qnty_main,2); ?></td>
    <td align="right"><? echo number_format($total_booking_qnty_short,2); ?></td>
    <td align="right"><? echo number_format($total_booking_qnty_sample,2); ?></td>
     <td align="right"><? echo number_format($total_tot_bok_qty,2); ?></td>
    <td align="right"><? echo number_format($tot_balance,2); ?></td>
    <td align="right"><? echo number_format($tot_pre_cost_bal,2); ?></td>
    </tr>
    </tfoot>
</table>
</div>
</div>
 <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</body>
<?	
	
	exit();	
}


?>